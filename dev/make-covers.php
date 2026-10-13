<?php
/**
 * Build cover images (1200×630) into falcon-pro/assets/img/covers/:
 *   <slug>.webp        article cover (featured image of the post)
 *   page-<slug>.webp   page cover (FALCON Setup button 4 sets it as the page's featured image = its own og:image)
 *
 * Needs the preview server running (php -S localhost:8765 dev/preview/router.php),
 * Google Chrome, and the GD extension:
 *   php -d extension=mbstring -d extension=gd dev/make-covers.php [slug ...]        article covers
 *   php -d extension=mbstring -d extension=gd dev/make-covers.php pages             every page cover
 *   php -d extension=mbstring -d extension=gd dev/make-covers.php page-pricing ...  single page covers
 * Without arguments every article in fenix_seed_articles() is rendered (page covers only when asked for).
 * The card template is dev/preview/cover.php (no numbers in either kind); the page titles live in its $page_covers map.
 *   article cover = small category line + title + icon · page cover = title + sub line + icon, nothing above the title
 *   (owner, 12 Oct 2026: a heading stands alone; do not add a label line back to the page covers).
 * A line marked "!" means the title is still too long at the smallest size: shorten it and render again.
 * A page that answers with a PHP error instead of the card is never saved as an image.
 */

$root   = dirname( __DIR__ );
$outdir = $root . '/falcon-pro/assets/img/covers';
$base   = getenv( 'FALCON_PREVIEW' ) ? getenv( 'FALCON_PREVIEW' ) : 'http://localhost:8765';

$chrome = getenv( 'CHROME_BIN' );
if ( ! $chrome ) {
	foreach ( array( 'C:/Program Files/Google/Chrome/Application/chrome.exe', 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe', '/usr/bin/google-chrome', '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome' ) as $c ) {
		if ( file_exists( $c ) ) {
			$chrome = $c;
			break;
		}
	}
}
if ( ! $chrome ) {
	fwrite( STDERR, "Chrome not found. Set CHROME_BIN.\n" );
	exit( 1 );
}
if ( ! function_exists( 'imagewebp' ) ) {
	fwrite( STDERR, "GD with WebP support is required (-d extension=gd).\n" );
	exit( 1 );
}

// Article slugs = content files, unless given on the command line. "pages" = every page cover of dev/preview/cover.php.
$slugs = array();
foreach ( array_slice( $argv, 1 ) as $arg ) {
	if ( 'pages' !== $arg ) {
		$slugs[] = $arg;
		continue;
	}
	$list = @file_get_contents( $base . '/__cover/pages/' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $list || ! preg_match_all( '/^page-[a-z0-9-]+$/m', $list, $m ) ) {
		fwrite( STDERR, "Could not read the page-cover list from $base/__cover/pages/ (is the preview server running?).\n" );
		exit( 1 );
	}
	$slugs = array_merge( $slugs, $m[0] );
}
if ( count( $argv ) < 2 ) {
	foreach ( glob( $root . '/falcon-pro/inc/content/articles/*.html' ) as $f ) {
		$slugs[] = basename( $f, '.html' );
	}
}
if ( ! is_dir( $outdir ) ) {
	mkdir( $outdir, 0755, true );
}
$tmp  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'falcon-covers';
$prof = $tmp . DIRECTORY_SEPARATOR . 'profile';
if ( ! is_dir( $prof ) ) {
	mkdir( $prof, 0755, true );
}

$fail = 0;
foreach ( array_unique( $slugs ) as $slug ) {
	if ( ! preg_match( '/^[a-z0-9-]+$/', $slug ) ) {
		continue;
	}
	$url = $base . '/__cover/' . $slug . '/';
	$hdr = @get_headers( $url ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( ! $hdr || false === strpos( $hdr[0], '200' ) ) {
		echo "✗ $slug · no such article in fenix_seed_articles() or page in \$page_covers (dev/preview/cover.php), or the preview server is down\n";
		$fail++;
		continue;
	}
	$flags = '"' . $chrome . '" --headless=new --disable-gpu --hide-scrollbars --force-device-scale-factor=1'
		. ' --window-size=1200,630 --virtual-time-budget=4000 --user-data-dir="' . $prof . '"';

	// 1) ผลการจัดหัวเรื่องที่สคริปต์ของแม่แบบเขียนไว้ใน <html data-fit data-size>
	$dom = array();
	exec( $flags . ' --dump-dom "' . $url . '" 2>&1', $dom, $rc );
	$dom = implode( "\n", $dom );
	$fit = preg_match( '/<html[^>]*\sdata-fit="([^"]*)"/', $dom, $m ) ? $m[1] : 'unknown';
	$px  = preg_match( '/<html[^>]*\sdata-size="([^"]*)"/', $dom, $m ) ? $m[1] : '?';

	// 2) ภาพ
	$png = $tmp . DIRECTORY_SEPARATOR . $slug . '.png';
	@unlink( $png ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	$o = array();
	exec( $flags . ' --screenshot="' . $png . '" "' . $url . '" 2>&1', $o, $rc );
	if ( ! file_exists( $png ) ) {
		echo "✗ $slug · screenshot failed\n";
		$fail++;
		continue;
	}
	$im = imagecreatefrompng( $png );
	// มุมซ้ายบนของการ์ดเป็นพื้น #172125 เสมอ · สีอื่น = หน้าไม่ได้เรนเดอร์เป็นการ์ด (เช่น ข้อความ error ของ PHP บนพื้นขาว) ไม่บันทึก
	$rgb = imagecolorat( $im, 3, 3 );
	if ( 'unknown' === $fit || abs( ( ( $rgb >> 16 ) & 0xFF ) - 0x17 ) > 6 || abs( ( ( $rgb >> 8 ) & 0xFF ) - 0x21 ) > 6 || abs( ( $rgb & 0xFF ) - 0x25 ) > 6 ) {
		echo "✗ $slug · the page did not render as a cover card (PHP error on the preview server?) · open $url\n";
		$fail++;
		continue;
	}
	if ( 1200 !== imagesx( $im ) || 630 !== imagesy( $im ) ) {
		$box = imagecreatetruecolor( 1200, 630 );
		imagecopyresampled( $box, $im, 0, 0, 0, 0, 1200, 630, min( 1200, imagesx( $im ) ), min( 630, imagesy( $im ) ) );
		$im = $box;
	}
	imagepalettetotruecolor( $im );
	imagewebp( $im, $outdir . '/' . $slug . '.webp', 86 );
	$ok = 'ok' === $fit;
	if ( ! $ok ) {
		$fail++;
	}
	printf( "%s %s (%d KB) · title %s at %spx\n", $ok ? '✓' : '!', $slug, (int) round( filesize( $outdir . '/' . $slug . '.webp' ) / 1024 ), $fit, $px );
}
exit( $fail ? 1 : 0 );

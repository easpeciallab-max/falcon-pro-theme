<?php
/**
 * Build in-content diagrams: falcon-pro/assets/img/illus/falcon-pro-ea-<name>-v<N>.webp (wide 1600×760)
 * and falcon-pro-ea-<name>-mobile-v<N>.webp (tall 800×1140, served to narrow screens by [falcon_figure]).
 *
 * Needs the preview server running (php -S localhost:8765 dev/preview/router.php),
 * Google Chrome, and the GD extension:
 *   php -d extension=mbstring -d extension=gd dev/make-art.php [name ...]
 * Without arguments every diagram listed by /__art/ is rendered.
 * Template and texts: dev/preview/art.php (route /__art/<name>/?layout=wide|tall). The version number N lives there
 * ('v' of each diagram): raise it whenever a picture changes (images are cached by URL), render again, then update
 * the file name in fenix_figure_registry() (falcon-pro/inc/shortcodes.php), in fenix_home_defaults()
 * (falcon-pro/inc/modules/home.php, the how-it-works file only) and anywhere else the file is named, and delete the
 * files of the older version. The script lists older versions that are still on disk ("~" lines) but never deletes them.
 * A picture starts with its title: there is no label line above it (owner, 12 Oct 2026).
 * A line marked "!" means the text did not fit even at the smallest size: shorten it in art.php and render again.
 */

$root   = dirname( __DIR__ );
$outdir = $root . '/falcon-pro/assets/img/illus';
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

// name => version, from the template itself
$list = @file_get_contents( $base . '/__art/' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
if ( false === $list ) {
	fwrite( STDERR, "Preview server is not answering at $base (php -S localhost:8765 dev/preview/router.php).\n" );
	exit( 1 );
}
$known = array();
foreach ( preg_split( '/\R/', trim( $list ) ) as $line ) {
	if ( preg_match( '/^([a-z0-9-]+)\t(\d+)$/', $line, $m ) ) {
		$known[ $m[1] ] = (int) $m[2];
	}
}
$names = array_slice( $argv, 1 );
if ( ! $names ) {
	$names = array_keys( $known );
}

if ( ! is_dir( $outdir ) ) {
	mkdir( $outdir, 0755, true );
}
$tmp  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'falcon-art';
$prof = $tmp . DIRECTORY_SEPARATOR . 'profile';
if ( ! is_dir( $prof ) ) {
	mkdir( $prof, 0755, true );
}

$layouts = array(
	'wide' => array( 1600, 760, '' ),
	'tall' => array( 800, 1140, '-mobile' ),
);
$fail = 0;
foreach ( $names as $name ) {
	if ( ! isset( $known[ $name ] ) ) {
		echo "✗ $name · not in dev/preview/art.php\n";
		$fail++;
		continue;
	}
	foreach ( $layouts as $layout => $size ) {
		$url   = $base . '/__art/' . $name . '/?layout=' . $layout;
		$flags = '"' . $chrome . '" --headless=new --disable-gpu --hide-scrollbars --force-device-scale-factor=1'
			. ' --window-size=' . $size[0] . ',' . $size[1] . ' --virtual-time-budget=4000 --user-data-dir="' . $prof . '"';

		// 1) ผลการจัดข้อความที่สคริปต์ของแม่แบบเขียนไว้ใน <html data-fit data-k>
		$dom = array();
		exec( $flags . ' --dump-dom "' . $url . '" 2>&1', $dom, $rc );
		$dom  = implode( "\n", $dom );
		$fit  = preg_match( '/<html[^>]*\sdata-fit="([^"]*)"/', $dom, $m ) ? $m[1] : 'unknown';
		$zoom = preg_match( '/<html[^>]*\sdata-k="([^"]*)"/', $dom, $m ) ? $m[1] : '?';

		// 2) ภาพ
		$png = $tmp . DIRECTORY_SEPARATOR . $name . '-' . $layout . '.png';
		@unlink( $png ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$o = array();
		exec( $flags . ' --screenshot="' . $png . '" "' . $url . '" 2>&1', $o, $rc );
		if ( ! file_exists( $png ) ) {
			echo "✗ $name ($layout) · screenshot failed\n";
			$fail++;
			continue;
		}
		$im = imagecreatefrompng( $png );
		// มุมซ้ายบนของแผนภาพเป็นพื้น #172125 เสมอ · สีอื่น หรือไม่มี data-fit = หน้าไม่ได้เรนเดอร์เป็นแผนภาพ (เช่น ข้อความ error ของ PHP) ไม่บันทึก
		$rgb = imagecolorat( $im, 3, 3 );
		if ( 'unknown' === $fit || abs( ( ( $rgb >> 16 ) & 0xFF ) - 0x17 ) > 6 || abs( ( ( $rgb >> 8 ) & 0xFF ) - 0x21 ) > 6 || abs( ( $rgb & 0xFF ) - 0x25 ) > 6 ) {
			echo "✗ $name ($layout) · the page did not render as a diagram (PHP error on the preview server?) · open $url\n";
			$fail++;
			continue;
		}
		if ( $size[0] !== imagesx( $im ) || $size[1] !== imagesy( $im ) ) {
			$box = imagecreatetruecolor( $size[0], $size[1] );
			imagecopyresampled( $box, $im, 0, 0, 0, 0, $size[0], $size[1], min( $size[0], imagesx( $im ) ), min( $size[1], imagesy( $im ) ) );
			$im = $box;
		}
		imagepalettetotruecolor( $im );
		$out = $outdir . '/falcon-pro-ea-' . $name . $size[2] . '-v' . $known[ $name ] . '.webp';
		imagewebp( $im, $out, 88 );
		$ok = 'ok' === $fit;
		if ( ! $ok ) {
			$fail++;
		}
		printf( "%s %s (%dx%d, %d KB) · text %s, size %s\n", $ok ? '✓' : '!', basename( $out ), imagesx( $im ), imagesy( $im ), (int) round( filesize( $out ) / 1024 ), $fit, $zoom );
	}

	// รุ่นเก่าของแผนภาพเดียวกันที่ยังค้างอยู่ในโฟลเดอร์: แจ้งให้ลบเอง (ไม่ลบให้ เพราะอาจยังมีจุดที่อ้างชื่อไฟล์รุ่นเก่าอยู่)
	foreach ( (array) glob( $outdir . '/falcon-pro-ea-' . $name . '*-v*.webp' ) as $old ) {
		if ( preg_match( '/^falcon-pro-ea-' . preg_quote( $name, '/' ) . '(?:-mobile)?-v(\d+)\.webp$/', basename( $old ), $m ) && (int) $m[1] !== $known[ $name ] ) {
			echo '~ ' . basename( $old ) . ' · older version still on disk: delete it once nothing names it (grep the repo for the file name)' . "\n";
		}
	}
}
exit( $fail ? 1 : 0 );

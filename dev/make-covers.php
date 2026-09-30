<?php
/**
 * Build article cover images: falcon-pro/assets/img/covers/<slug>.webp (1200×630).
 *
 * Needs the preview server running (php -S localhost:8765 dev/preview/router.php),
 * Google Chrome, and the GD extension:
 *   php -d extension=mbstring -d extension=gd dev/make-covers.php [slug ...]
 * Without arguments every article in fenix_seed_articles() is rendered.
 * The card template is dev/preview/cover.php (title + category + icon · no numbers).
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

// Article slugs = content files, unless given on the command line.
$slugs = array_slice( $argv, 1 );
if ( ! $slugs ) {
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
foreach ( $slugs as $slug ) {
	if ( ! preg_match( '/^[a-z0-9-]+$/', $slug ) ) {
		continue;
	}
	$url = $base . '/__cover/' . $slug . '/';
	$hdr = @get_headers( $url ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( ! $hdr || false === strpos( $hdr[0], '200' ) ) {
		echo "✗ $slug · not in fenix_seed_articles() or preview server is down\n";
		$fail++;
		continue;
	}
	$png = $tmp . DIRECTORY_SEPARATOR . $slug . '.png';
	@unlink( $png ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	$cmd = '"' . $chrome . '" --headless=new --disable-gpu --hide-scrollbars --force-device-scale-factor=1'
		. ' --window-size=1200,630 --virtual-time-budget=4000'
		. ' --user-data-dir="' . $prof . '" --screenshot="' . $png . '" "' . $url . '"';
	exec( $cmd . ' 2>&1', $o, $rc );
	if ( ! file_exists( $png ) ) {
		echo "✗ $slug · screenshot failed\n";
		$fail++;
		continue;
	}
	$im = imagecreatefrompng( $png );
	if ( 1200 !== imagesx( $im ) || 630 !== imagesy( $im ) ) {
		$fit = imagecreatetruecolor( 1200, 630 );
		imagecopyresampled( $fit, $im, 0, 0, 0, 0, 1200, 630, min( 1200, imagesx( $im ) ), min( 630, imagesy( $im ) ) );
		$im = $fit;
	}
	imagepalettetotruecolor( $im );
	imagewebp( $im, $outdir . '/' . $slug . '.webp', 86 );
	printf( "✓ %s (%d KB)\n", $slug, (int) round( filesize( $outdir . '/' . $slug . '.webp' ) / 1024 ) );
}
exit( $fail ? 1 : 0 );

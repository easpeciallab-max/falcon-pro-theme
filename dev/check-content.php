<?php
/**
 * Dev check for seed content in falcon-pro/inc/content/ (see dev/content-spec.md)
 * Run: php -d extension=mbstring dev/check-content.php
 */
require __DIR__ . '/preview/wp-stubs.php';
require __DIR__ . '/../falcon-pro/functions.php';

$root     = __DIR__ . '/../falcon-pro/inc/content/';
$pages    = array_keys( fenix_site_pages() );
$articles = array_map(
	function ( $f ) {
		return basename( $f, '.html' );
	},
	glob( $root . 'articles/*.html' )
);
$valid    = array_merge( $pages, $articles );
$problems = 0;

foreach ( array_merge( glob( $root . 'pages/*.html' ), glob( $root . 'articles/*.html' ) ) as $file ) {
	$rel   = basename( dirname( $file ) ) . '/' . basename( $file, '.html' );
	$raw   = file_get_contents( $file );
	$issue = array();

	if ( ! preg_match( '/^\s*<!--meta\s+(\{.*?\})\s*-->/s', $raw, $m ) || ! is_array( json_decode( $m[1], true ) ) ) {
		$issue[] = 'meta JSON missing/invalid';
		$meta    = array();
	} else {
		$meta = json_decode( $m[1], true );
		foreach ( array( 'title', 'seo_title', 'description', 'keyword' ) as $k ) {
			if ( empty( $meta[ $k ] ) ) {
				$issue[] = "meta.$k empty";
			}
		}
		if ( 0 === strpos( $rel, 'articles/' ) ) {
			foreach ( array( 'excerpt', 'category' ) as $k ) {
				if ( empty( $meta[ $k ] ) ) {
					$issue[] = "meta.$k empty";
				}
			}
		}
		if ( ! empty( $meta['seo_title'] ) && mb_strlen( $meta['seo_title'] ) > 65 ) {
			$issue[] = 'seo_title > 65 chars';
		}
	}
	if ( preg_match( '/<h1[\s>]/i', $raw ) ) {
		$issue[] = 'contains <h1>';
	}
	if ( preg_match( '/<img[\s>]/i', $raw ) ) {
		$issue[] = 'contains <img>';
	}
	if ( false === strpos( $raw, 'callout--warn' ) ) {
		$issue[] = 'no callout--warn (risk context)';
	}
	if ( substr_count( $raw, '[falcon_line' ) !== substr_count( $raw, '[/falcon_line]' ) ) {
		$issue[] = 'unbalanced [falcon_line]';
	}
	if ( preg_match_all( '#\{\{home\}\}/([a-z0-9\-]+)/#', $raw, $links ) ) {
		foreach ( array_unique( $links[1] ) as $slug ) {
			if ( ! in_array( $slug, $valid, true ) ) {
				$issue[] = "link to unknown slug /$slug/";
			}
		}
	}
	// balanced common block tags
	foreach ( array( 'p', 'div', 'ul', 'ol', 'li', 'table', 'details', 'h2', 'h3' ) as $tag ) {
		$open  = preg_match_all( "#<$tag(\s[^>]*)?>#i", $raw );
		$close = preg_match_all( "#</$tag>#i", $raw );
		if ( $open !== $close ) {
			$issue[] = "<$tag> open $open / close $close";
		}
	}
	// no invented performance claims (heuristic)
	if ( preg_match( '/(win\s*rate|อัตราชนะ)[^<]{0,20}\d{2}(\.\d+)?\s*%/iu', $raw ) ) {
		$issue[] = 'possible win-rate number — check it is hypothetical';
	}

	$words = mb_strlen( trim( preg_replace( '/\s+/u', ' ', strip_tags( preg_replace( '/^\s*<!--meta.*?-->/s', '', $raw ) ) ) ) );
	printf( "%-34s %6d chars  %s\n", $rel, $words, $issue ? '✗ ' . implode( '; ', $issue ) : '✓' );
	$problems += count( $issue );
}
echo $problems ? "\n$problems issue(s)\n" : "\nall good\n";

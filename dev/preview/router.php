<?php
/**
 * Local preview router: php -S localhost:8765 dev/preview/router.php (from repo root)
 * Renders theme templates with stub WP functions so layout/CSS can be checked in a browser.
 */

$uri  = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$uri  = rawurldecode( $uri );
$root = realpath( __DIR__ . '/../../falcon-pro' );

// Static theme files: /theme/...
if ( 0 === strpos( $uri, '/theme/' ) ) {
	$file = realpath( $root . substr( $uri, 6 ) );
	if ( $file && 0 === strpos( $file, $root ) && is_file( $file ) ) {
		$types = array( 'css' => 'text/css', 'js' => 'application/javascript', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'webp' => 'image/webp', 'svg' => 'image/svg+xml' );
		$ext   = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
		header( 'Content-Type: ' . ( isset( $types[ $ext ] ) ? $types[ $ext ] : 'application/octet-stream' ) );
		readfile( $file );
		return true;
	}
	http_response_code( 404 );
	return true;
}

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

require __DIR__ . '/wp-stubs.php';
require $root . '/functions.php';
do_action( 'after_setup_theme' );
do_action( 'init' );

$slug = trim( $uri, '/' );

// Keep-case wrapping (inc/modules/infra.php): on the live site the buffer starts at template_redirect, a hook the
// preview never fires, so start the same buffer here. Not for the cover cards and diagrams (they are not theme pages on the live site).
// Add ?nocaps=0 to see a page without the wrapping.
if ( function_exists( 'fenix_nocaps_buffer' ) && 0 !== strpos( $slug, '__cover/' ) && 0 !== strpos( $slug . '/', '__art/' ) && ! ( isset( $_GET['nocaps'] ) && '0' === $_GET['nocaps'] ) ) {
	ob_start( 'fenix_nocaps_buffer' );
}

// Search results: /?s=term or /search/ renders search.php (the WP_Query stub has no posts, so this is the "no results" state)
if ( isset( $_GET['s'] ) || 'search' === $slug ) {
	$GLOBALS['fx_post'] = array( 'title' => 'ค้นหา', 'content' => '', 'slug' => 'search', 'type' => 'search' );
	$GLOBALS['fx_loop'] = 1; // have_posts() = false
	require $root . '/search.php';
	return true;
}

if ( '' === $slug ) {
	$GLOBALS['fx_route']['front'] = true;
	$GLOBALS['fx_post']           = array( 'title' => 'หน้าแรก', 'content' => '', 'slug' => '', 'type' => 'page' );
	require $root . '/front-page.php';
	return true;
}

// Retired pages (the 'retired' rows of the manifest in inc/setup.php): the old URL answers with a 301 to the page that replaces it,
// like fenix_retired_redirect() does on the live site (template_redirect, which the preview never fires). The tracking
// parameters the live redirect keeps (utm_*, click ids) are kept here too · add ?noredirect=1 to see the Location as text instead.
if ( function_exists( 'fenix_retired_url' ) && '' !== fenix_retired_url( $slug ) ) {
	$fx_to   = fenix_retired_url( $slug );
	$fx_keep = array();
	foreach ( $_GET as $fx_key => $fx_value ) {
		if ( is_string( $fx_key ) && is_scalar( $fx_value ) && preg_match( '/^(?:utm_[a-z0-9_]{1,40}|gclid|gbraid|wbraid|fbclid|ttclid|msclkid)$/', $fx_key ) ) {
			$fx_keep[ $fx_key ] = (string) $fx_value;
		}
	}
	if ( $fx_keep ) {
		$fx_to .= '?' . http_build_query( $fx_keep, '', '&', PHP_QUERY_RFC3986 );
	}
	while ( ob_get_level() ) {
		ob_end_clean();
	}
	if ( isset( $_GET['noredirect'] ) ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo '301 ' . $fx_to;
		return true;
	}
	header( 'Location: ' . $fx_to, true, 301 );
	header( 'X-Redirect-By: FALCON PRO EA (preview)' );
	return true;
}

// Pages from the theme's own page manifest (inc/setup.php) when available.
$pages = function_exists( 'fenix_site_pages' ) ? fenix_site_pages() : array();
if ( isset( $pages[ $slug ] ) ) {
	$p       = $pages[ $slug ];
	$content = '';
	if ( ! empty( $p['content'] ) && function_exists( 'fenix_seed_content' ) ) {
		// same text FALCON Setup writes into the page (seed comments stripped) when that helper exists
		$content = function_exists( 'fenix_setup_page_content' ) ? fenix_setup_page_content( $p['content'] ) : fenix_seed_content( $p['content'] );
	}
	$GLOBALS['fx_post'] = array( 'title' => $p['title'], 'content' => $content, 'slug' => $slug, 'type' => 'page' );
	if ( 'articles' === $slug ) {
		$GLOBALS['fx_route']['home'] = true;
		require $root . '/index.php';
		return true;
	}
	$tpl = ! empty( $p['template'] ) ? $p['template'] : 'page.php';
	require $root . '/' . $tpl;
	return true;
}

// In-content diagram: /__art/<name>/?layout=wide|tall · /__art/ lists the names (source for dev/make-art.php)
if ( 0 === strpos( $slug . '/', '__art/' ) ) {
	$fx_art_name = trim( (string) substr( $slug, 6 ), '/' );
	require __DIR__ . '/art.php';
	return true;
}

// Cover card: /__cover/<article-slug>/ for an article, /__cover/page-<page-slug>/ for a page (source for dev/make-covers.php)
if ( 0 === strpos( $slug, '__cover/' ) && function_exists( 'fenix_seed_articles' ) ) {
	$fx_cover_slug = substr( $slug, 8 );
	require __DIR__ . '/cover.php';
	return true;
}

// Article preview: /article/<file> renders inc/content/articles/<file>.html with single.php
if ( 0 === strpos( $slug, 'article/' ) && function_exists( 'fenix_seed_articles' ) ) {
	$key  = substr( $slug, 8 );
	$arts = fenix_seed_articles();
	if ( isset( $arts[ $key ] ) ) {
		$GLOBALS['fx_post'] = array( 'title' => $arts[ $key ]['title'], 'content' => fenix_seed_content( $arts[ $key ]['content'] ), 'slug' => $key, 'type' => 'post' );
		require $root . '/single.php';
		return true;
	}
}

// Fallback for a theme copy without the page manifest. Retired pages (forward-test, how-to-install) are not listed: they redirect above.
$legacy = array(
	'backtest'        => array( 'template-backtest.php', 'Backtest' ),
	'pricing'         => array( 'template-pricing.php', 'แพ็กเกจและราคา' ),
	'risk-disclosure' => array( 'template-risk.php', 'ประกาศความเสี่ยง' ),
);
if ( isset( $legacy[ $slug ] ) && file_exists( $root . '/' . $legacy[ $slug ][0] ) ) {
	$GLOBALS['fx_post'] = array( 'title' => $legacy[ $slug ][1], 'content' => '', 'slug' => $slug, 'type' => 'page' );
	require $root . '/' . $legacy[ $slug ][0];
	return true;
}

// Anything else (and /404/): the theme's 404 template with a 404 status
http_response_code( 404 );
$GLOBALS['fx_post'] = array( 'title' => 'ไม่พบหน้า', 'content' => '', 'slug' => '404', 'type' => '404' );
require $root . '/404.php';
return true;

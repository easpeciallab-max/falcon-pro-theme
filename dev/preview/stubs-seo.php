<?php
/**
 * Preview stubs for the SEO + INFRA module (inc/seo.php, inc/modules/infra.php).
 * Loaded by dev/preview/wp-stubs.php · every stub is wrapped in function_exists() so other modules' stubs win if loaded first.
 */

if ( ! function_exists( 'is_page_template' ) ) {
	function is_page_template( $template = '' ) {
		if ( empty( $GLOBALS['fx_post']['slug'] ) || ! function_exists( 'fenix_site_pages' ) ) {
			return false;
		}
		$pages = fenix_site_pages();
		$slug  = $GLOBALS['fx_post']['slug'];
		$tpl   = isset( $pages[ $slug ]['template'] ) ? $pages[ $slug ]['template'] : '';
		return '' === $template ? '' !== $tpl : in_array( $tpl, (array) $template, true );
	}
}
if ( ! function_exists( 'is_tag' ) ) {
	function is_tag() { return false; }
}
if ( ! function_exists( 'is_tax' ) ) {
	function is_tax() { return false; }
}
if ( ! function_exists( 'is_author' ) ) {
	function is_author() { return false; }
}
if ( ! function_exists( 'is_date' ) ) {
	function is_date() { return false; }
}
if ( ! function_exists( 'is_post_type_archive' ) ) {
	function is_post_type_archive() { return false; }
}
if ( ! function_exists( 'is_preview' ) ) {
	function is_preview() { return false; }
}
if ( ! function_exists( 'is_feed' ) ) {
	function is_feed() { return false; }
}
if ( ! function_exists( 'is_robots' ) ) {
	function is_robots() { return false; }
}
if ( ! function_exists( 'wp_doing_ajax' ) ) {
	function wp_doing_ajax() { return false; }
}
if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) { return is_object( $thing ) && 'WP_Error' === get_class( $thing ); }
}
if ( ! function_exists( 'get_term_link' ) ) {
	function get_term_link() { return home_url( '/category/' ); }
}
if ( ! function_exists( 'wp_count_posts' ) ) {
	/* seed articles are imported as drafts → 0 published posts, like a fresh FALCON install */
	function wp_count_posts() { return (object) array( 'publish' => 0, 'draft' => 10 ); }
}
if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) { return parse_url( (string) $url, $component ); }
}
if ( ! function_exists( 'wp_parse_str' ) ) {
	function wp_parse_str( $string, &$array ) { parse_str( (string) $string, $array ); }
}
if ( ! function_exists( 'build_query' ) ) {
	function build_query( $data ) { return http_build_query( $data ); }
}
if ( ! function_exists( 'status_header' ) ) {
	function status_header( $code ) { http_response_code( (int) $code ); }
}
if ( ! function_exists( 'nocache_headers' ) ) {
	function nocache_headers() {}
}
if ( ! function_exists( 'has_blocks' ) ) {
	function has_blocks() { return false; }
}
if ( ! function_exists( 'get_post_thumbnail_id' ) ) {
	function get_post_thumbnail_id() { return 0; }
}
if ( ! function_exists( 'remove_filter' ) ) {
	function remove_filter() { return false; }
}
if ( ! function_exists( 'remove_post_type_support' ) ) {
	function remove_post_type_support() {}
}
if ( ! function_exists( 'get_current_user_id' ) ) {
	function get_current_user_id() { return 0; }
}
if ( ! function_exists( 'wp_dequeue_style' ) ) {
	function wp_dequeue_style( $handle ) { unset( $GLOBALS['fx_styles'][ $handle ] ); }
}
if ( ! function_exists( 'wp_deregister_style' ) ) {
	function wp_deregister_style( $handle ) { unset( $GLOBALS['fx_styles'][ $handle ] ); }
}

/*
 * Core prints <meta name='robots'> from the wp_robots filter at wp_head priority 1.
 * Emulate it so the noindex rules of inc/seo.php are visible in the preview.
 */
if ( ! function_exists( 'wp_robots' ) ) {
	function wp_robots() {
		$robots = apply_filters( 'wp_robots', array() );
		$parts  = array();
		foreach ( (array) $robots as $directive => $value ) {
			if ( is_string( $value ) ) {
				$parts[] = "$directive:$value";
			} elseif ( $value ) {
				$parts[] = $directive;
			}
		}
		if ( $parts ) {
			echo "<meta name='robots' content='" . esc_attr( implode( ', ', $parts ) ) . "' />\n";
		}
	}
	add_action( 'wp_head', 'wp_robots', 1 );
}

/*
 * wp-stubs.php has a no-op remove_action(), so the callbacks that inc/seo.php replaces with remove_action()
 * would print twice in the preview. Drop the originals once the theme has loaded (router fires 'init').
 */
function fx_seo_stub_apply_replacements() {
	$pairs = array(
		'fenix_open_graph'    => 'fenix_seo_open_graph',
		'fenix_schema_jsonld' => 'fenix_seo_site_schema',
	);
	if ( empty( $GLOBALS['fx_hooks']['wp_head'] ) ) {
		return;
	}
	foreach ( $GLOBALS['fx_hooks']['wp_head'] as $priority => $callbacks ) {
		foreach ( $callbacks as $i => $cb ) {
			if ( is_string( $cb[0] ) && isset( $pairs[ $cb[0] ] ) && function_exists( $pairs[ $cb[0] ] ) ) {
				unset( $GLOBALS['fx_hooks']['wp_head'][ $priority ][ $i ] );
			}
		}
	}
}
add_action( 'init', 'fx_seo_stub_apply_replacements', 999 );

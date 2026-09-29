<?php
/**
 * Minimal WordPress stubs for previewing the theme locally without WordPress.
 * Not part of the theme — WP Pusher only deploys falcon-pro/.
 *
 * Usage: php -S localhost:8765 dev/preview/router.php  (run from repo root)
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'FALCON_PREVIEW', true );

$GLOBALS['fx_hooks']   = array();
$GLOBALS['fx_styles']  = array();
$GLOBALS['fx_scripts'] = array();
$GLOBALS['fx_post']    = array( 'title' => '', 'content' => '', 'slug' => '', 'type' => 'page', 'date' => '2026-09-29' );
$GLOBALS['fx_loop']    = 0;
$GLOBALS['fx_route']   = array( 'front' => false, 'home' => false );

function fx_theme_dir() { return realpath( __DIR__ . '/../../falcon-pro' ); }

function add_action( $h, $cb, $p = 10, $a = 1 ) { $GLOBALS['fx_hooks'][ $h ][ $p ][] = array( $cb, $a ); }
function add_filter( $h, $cb, $p = 10, $a = 1 ) { add_action( $h, $cb, $p, $a ); }
function remove_action() {}
function do_action( $h, ...$args ) {
	if ( empty( $GLOBALS['fx_hooks'][ $h ] ) ) { return; }
	ksort( $GLOBALS['fx_hooks'][ $h ] );
	foreach ( $GLOBALS['fx_hooks'][ $h ] as $cbs ) { foreach ( $cbs as $cb ) { call_user_func_array( $cb[0], array_slice( $args, 0, $cb[1] ) ); } }
}
function apply_filters( $h, $v, ...$args ) {
	if ( empty( $GLOBALS['fx_hooks'][ $h ] ) ) { return $v; }
	ksort( $GLOBALS['fx_hooks'][ $h ] );
	foreach ( $GLOBALS['fx_hooks'][ $h ] as $cbs ) { foreach ( $cbs as $cb ) { $v = call_user_func_array( $cb[0], array_slice( array_merge( array( $v ), $args ), 0, max( 1, $cb[1] ) ) ); } }
	return $v;
}
function has_filter( $h = "" ) { return ! empty( $GLOBALS["fx_hooks"][ $h ] ); }
function has_action( $h = "" ) { return ! empty( $GLOBALS["fx_hooks"][ $h ] ); }
function add_theme_support() {}
function load_theme_textdomain() {}
function register_nav_menus() {}
$GLOBALS['fx_sc'] = array();
function add_shortcode( $tag, $cb ) { $GLOBALS['fx_sc'][ $tag ] = $cb; }
function shortcode_exists( $tag ) { return in_array( $tag, array( 'falcon_line', 'falcon_brand', 'falcon_broker', 'falcon_calc' ), true ) && isset( $GLOBALS['fx_sc'][ $tag ] ); }
function shortcode_atts( $pairs, $atts ) { $atts = (array) $atts; $out = array(); foreach ( $pairs as $k => $v ) { $out[ $k ] = isset( $atts[ $k ] ) ? $atts[ $k ] : $v; } return $out; }
function shortcode_parse_atts( $text ) { $a = array(); if ( preg_match_all( '/(\w+)\s*=\s*"([^"]*)"/', (string) $text, $m, PREG_SET_ORDER ) ) { foreach ( $m as $x ) { $a[ strtolower( $x[1] ) ] = $x[2]; } } return $a; }
function do_shortcode( $s ) {
	return preg_replace_callback(
		'/\[(\w+)([^\]]*)\](?:(.*?)\[\/\1\])?/s',
		function ( $m ) {
			if ( ! isset( $GLOBALS['fx_sc'][ $m[1] ] ) ) { return $m[0]; }
			return call_user_func( $GLOBALS['fx_sc'][ $m[1] ], shortcode_parse_atts( $m[2] ), isset( $m[3] ) ? $m[3] : '' );
		},
		$s
	);
}
add_filter( 'the_content', 'do_shortcode', 11 );
function untrailingslashit( $s ) { return rtrim( (string) $s, '/\\' ); }
function trailingslashit( $s ) { return untrailingslashit( $s ) . '/'; }
function get_post_field( $f, $id = 0 ) { return 'post_name' === $f ? $GLOBALS['fx_post']['slug'] : ''; }
function get_post_status() { return 'publish'; }
function get_post( $id = 0 ) { return (object) array( 'ID' => 1, 'post_content' => $GLOBALS['fx_post']['content'], 'post_excerpt' => '', 'post_title' => $GLOBALS['fx_post']['title'], 'post_parent' => 0, 'post_name' => $GLOBALS['fx_post']['slug'], 'post_status' => 'publish', 'post_type' => $GLOBALS['fx_post']['type'], 'post_date' => '2026-09-29 09:00:00', 'post_modified' => '2026-09-29 09:00:00' ); }
function has_site_icon() { return false; }
function wp_get_nav_menu_object() { return false; }
function register_post_meta() {}
function add_image_size() {}

function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return esc_html( $s ); }
function esc_url( $s ) { return esc_html( $s ); }
function esc_url_raw( $s ) { return (string) $s; }
function esc_textarea( $s ) { return esc_html( $s ); }
function esc_js( $s ) { return addslashes( (string) $s ); }
function esc_html__( $s ) { return esc_html( $s ); }
function esc_attr__( $s ) { return esc_attr( $s ); }
function __( $s ) { return $s; }
function _e( $s ) { echo $s; }
function _x( $s ) { return $s; }
function _n( $a, $b, $n ) { return 1 === $n ? $a : $b; }
function wp_kses_post( $s ) { return $s; }
function wp_kses( $s ) { return $s; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return (string) $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function sanitize_title( $s ) { return sanitize_key( str_replace( ' ', '-', (string) $s ) ); }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }
function wp_unslash( $s ) { return $s; }
function absint( $n ) { return abs( (int) $n ); }
function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d ); }
function wp_trim_words( $t, $n = 55, $more = '…' ) { $t = wp_strip_all_tags( $t ); return mb_strlen( $t ) > $n * 6 ? mb_substr( $t, 0, $n * 6 ) . $more : $t; }
function strip_shortcodes( $s ) { return $s; }
function wpautop( $s ) { return $s; }
function wp_create_nonce() { return 'nonce'; }
function check_ajax_referer() { return true; }
function wp_die() { exit; }
function current_user_can() { return false; }
function is_user_logged_in() { return false; }
function is_admin() { return false; }
function is_customize_preview() { return false; }
function wp_is_mobile() { return false; }
function get_locale() { return 'th'; }
function language_attributes() { echo 'lang="th"'; }
function bloginfo( $k ) { echo esc_html( get_bloginfo( $k ) ); }
function get_bloginfo( $k = 'name' ) {
	$m = array( 'name' => 'FALCON PRO EA', 'description' => 'ผู้ช่วยเทรดอัตโนมัติสำหรับ MT5', 'charset' => 'UTF-8' );
	return isset( $m[ $k ] ) ? $m[ $k ] : '';
}
function home_url( $p = '' ) { return 'http://localhost:8765' . ( $p ? '/' . ltrim( $p, '/' ) : '/' ); }
function site_url( $p = '' ) { return home_url( $p ); }
function admin_url( $p = '' ) { return home_url( 'wp-admin/' . $p ); }
function get_template_directory() { return fx_theme_dir(); }
function get_stylesheet_directory() { return fx_theme_dir(); }
function get_template_directory_uri() { return 'http://localhost:8765/theme'; }
function get_stylesheet_directory_uri() { return get_template_directory_uri(); }
function get_stylesheet_uri() { return get_template_directory_uri() . '/style.css'; }
function get_theme_mod( $k, $d = false ) { return $d; }
function get_option( $k, $d = false ) {
	$m = array( 'posts_per_page' => 9, 'blog_public' => 1, 'date_format' => 'j M Y', 'show_on_front' => 'page', 'page_for_posts' => 0 );
	return isset( $m[ $k ] ) ? $m[ $k ] : $d;
}
function update_option() { return true; }
function get_post_meta() { return ''; }
function update_post_meta() { return true; }
function wp_get_attachment_image_url() { return ''; }
function wp_get_attachment_image() { return ''; }
function get_page_by_path( $slug ) { return (object) array( 'ID' => crc32( $slug ), 'post_title' => $slug ); }
function get_page_template_slug() { return ''; }
function get_queried_object_id() { return 1; }
function get_queried_object() { return get_post(); }
function get_the_ID() { return 1; }
function get_query_var( $k, $d = '' ) { return $d; }
function get_search_query() { return ''; }
function add_query_arg() { return '#'; }
function paginate_links() { return ''; }
function the_posts_pagination() {}
function wp_link_pages() {}
function edit_post_link() {}
function comments_open() { return false; }
function get_comments_number() { return 0; }

function is_front_page() { return $GLOBALS['fx_route']['front']; }
function is_home() { return $GLOBALS['fx_route']['home']; }
function is_page( $s = null ) { $ok = 'page' === $GLOBALS['fx_post']['type'] && ! $GLOBALS['fx_route']['home']; return null === $s ? $ok : ( $ok && in_array( $GLOBALS['fx_post']['slug'], (array) $s, true ) ); }
function is_single() { return 'post' === $GLOBALS['fx_post']['type']; }
function is_singular( $t = null ) { return ! $GLOBALS['fx_route']['home'] && ( null === $t || $t === $GLOBALS['fx_post']['type'] || ( is_array( $t ) && in_array( $GLOBALS['fx_post']['type'], $t, true ) ) ); }
function is_archive() { return false; }
function is_search() { return false; }
function is_404() { return false; }
function is_category() { return false; }
function is_main_query() { return true; }
function in_the_loop() { return true; }

function have_posts() { return $GLOBALS['fx_loop'] < 1 && ! $GLOBALS['fx_route']['home']; }
function the_post() { $GLOBALS['fx_loop']++; $GLOBALS['post'] = get_post(); }
function rewind_posts() { $GLOBALS['fx_loop'] = 0; }
function wp_reset_postdata() {}
function get_the_title() { return $GLOBALS['fx_post']['title']; }
function the_title() { echo esc_html( get_the_title() ); }
function get_the_content() { return $GLOBALS['fx_post']['content']; }
function the_content() { echo apply_filters( 'the_content', get_the_content() ); }
function get_the_excerpt() { return wp_trim_words( get_the_content(), 30 ); }
function the_excerpt() { echo esc_html( get_the_excerpt() ); }
function has_excerpt() { return false; }
function get_permalink( $p = 0 ) { if ( is_object( $p ) && isset( $p->post_title ) && ! isset( $p->post_content ) ) { return home_url( $p->post_title . '/' ); } return home_url( $GLOBALS['fx_post']['slug'] . '/' ); }
function the_permalink() { echo esc_url( get_permalink() ); }
function get_the_date( $f = '' ) { return 'c' === $f ? '2026-09-29T09:00:00+07:00' : '29 ก.ย. 2026'; }
function get_the_modified_date( $f = '' ) { return get_the_date( $f ); }
function get_the_time( $f = '' ) { return get_the_date( $f ); }
function get_the_author() { return 'FALCON PRO Team'; }
function the_author() { echo 'FALCON PRO Team'; }
function get_the_category() { return array( (object) array( 'name' => 'คู่มือ EA', 'term_id' => 1, 'slug' => 'guide' ) ); }
function get_category_link() { return '#'; }
function get_the_tag_list() { return ''; }
function has_post_thumbnail() { return false; }
function the_post_thumbnail() {}
function get_the_post_thumbnail_url() { return ''; }
function post_class( $c = '' ) { echo 'class="' . esc_attr( is_array( $c ) ? implode( ' ', $c ) : $c ) . '"'; }
function body_class( $c = '' ) { $cl = apply_filters( 'body_class', array( is_front_page() ? 'home' : 'page' ) ); echo 'class="' . esc_attr( implode( ' ', $cl ) ) . '"'; }
function the_archive_title() { echo 'บทความ'; }
function get_the_archive_description() { return ''; }
function get_the_archive_title() { return 'บทความ'; }
function wp_get_document_title() { return get_the_title() . ' · FALCON PRO EA'; }
function get_post_type() { return $GLOBALS['fx_post']['type']; }
function get_posts() { return array(); }
function get_pages() { return array(); }
function get_children() { return array(); }
function wp_get_recent_posts() { return array(); }

class WP_Query {
	public $posts = array();
	public $max_num_pages = 0;
	public $found_posts = 0;
	public function __construct( $a = array() ) {}
	public function have_posts() { return false; }
	public function the_post() {}
}

function wp_enqueue_style( $h, $src = '' ) { if ( $src ) { $GLOBALS['fx_styles'][ $h ] = $src; } }
function wp_enqueue_script( $h, $src = '' ) { if ( $src ) { $GLOBALS['fx_scripts'][ $h ] = $src; } }
function wp_register_style() {}
function wp_register_script() {}
function wp_localize_script( $h, $name, $data ) { $GLOBALS['fx_inline'][] = 'var ' . $name . ' = ' . json_encode( $data ) . ';'; }
function wp_add_inline_script( $h, $js ) { $GLOBALS['fx_inline'][] = $js; }
function wp_add_inline_style() {}
function wp_head() {
	do_action( 'wp_enqueue_scripts' );
	echo '<title>' . esc_html( wp_get_document_title() ) . "</title>\n";
	foreach ( $GLOBALS['fx_styles'] as $h => $src ) { echo '<link rel="stylesheet" id="' . esc_attr( $h ) . '" href="' . esc_url( $src ) . "\">\n"; }
	do_action( 'wp_head' );
}
function wp_footer() {
	if ( ! empty( $GLOBALS['fx_inline'] ) ) { echo '<script>' . implode( "\n", $GLOBALS['fx_inline'] ) . "</script>\n"; }
	foreach ( $GLOBALS['fx_scripts'] as $h => $src ) { echo '<script src="' . esc_url( $src ) . "\"></script>\n"; }
	do_action( 'wp_footer' );
}
function wp_body_open() { do_action( 'wp_body_open' ); }
function get_header() { require fx_theme_dir() . '/header.php'; }
function get_footer() { require fx_theme_dir() . '/footer.php'; }
function get_template_part( $slug, $name = null, $args = array() ) {
	$f = fx_theme_dir() . '/' . $slug . ( $name ? '-' . $name : '' ) . '.php';
	if ( file_exists( $f ) ) { require $f; }
}
function locate_template( $t ) { $f = fx_theme_dir() . '/' . ( is_array( $t ) ? $t[0] : $t ); return file_exists( $f ) ? $f : ''; }
function wp_nav_menu( $args ) {
	if ( ! empty( $args['fallback_cb'] ) && function_exists( $args['fallback_cb'] ) ) { call_user_func( $args['fallback_cb'], $args ); }
}
function has_nav_menu() { return false; }
function wp_get_nav_menu_items() { return array(); }

function get_categories() { return array(); }
function is_paged() { return false; }
function get_edit_post_link() { return '#'; }
$GLOBALS['wp_query'] = new WP_Query();
function post_password_required() { return false; }
function wp_parse_args( $args, $defaults = array() ) { return array_merge( (array) $defaults, is_array( $args ) ? $args : array() ); }
function attachment_url_to_postid() { return 0; }
function wp_get_attachment_metadata() { return false; }
function wp_get_attachment_image_srcset() { return false; }
function wp_get_nav_menu_name() { return ''; }
function get_nav_menu_locations() { return array(); }
function wp_script_add_data() {}
function wp_style_add_data() {}
function wp_add_inline_script_safe() {}

/* stub เพิ่มเติมของแต่ละโมดูล: dev/preview/stubs-<module>.php (ห่อด้วย function_exists) */
foreach ( (array) glob( __DIR__ . '/stubs-*.php' ) as $fx_stub_file ) {
	require $fx_stub_file;
}

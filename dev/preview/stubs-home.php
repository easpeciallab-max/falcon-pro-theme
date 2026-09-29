<?php
/**
 * Preview helpers for the HOME module (front-page.php) · not part of the theme (WP Pusher deploys falcon-pro/ only).
 * Loaded by dev/preview/wp-stubs.php before functions.php.
 *
 * Try other Customizer values without WordPress by adding hm_mod[...] to the preview URL, e.g.
 *   /?hm_mod[color_mode]=balanced
 *   /?hm_mod[pricing_mode]=price&hm_mod[pkg2_price]=4,900
 *   /?hm_mod[line_url]=https://lin.ee/example&hm_mod[show_pain]=0&hm_mod[home_what_img]=
 * Only active when hm_mod is present in the query string.
 */

if ( ! empty( $_GET['hm_mod'] ) && is_array( $_GET['hm_mod'] ) && function_exists( 'add_filter' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			foreach ( (array) $_GET['hm_mod'] as $fx_key => $fx_value ) { // phpcs:ignore WordPress.Security.NonceVerification
				$fx_key = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $fx_key ) );
				if ( '' !== $fx_key && is_string( $fx_value ) ) {
					$d[ $fx_key ] = $fx_value;
				}
			}
			return $d;
		},
		99
	);
}

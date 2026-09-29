<?php
/**
 * Preview stubs for the consent module (inc/modules/consent.php) · dev only, not deployed.
 *
 * Simulate settings in the preview with a query string:
 *   ?fx_consent=ga        → valid GA4 ID (analytics category)
 *   ?fx_consent=pixel     → valid Meta Pixel ID (marketing category)
 *   ?fx_consent=ga,pixel  → both
 *   ?fx_consent=force     → show_cookie_consent on, no tracking IDs ("necessary only" mode)
 *   ?fx_consent=bad       → malformed IDs (must be ignored: no card, no tags)
 */

if ( ! function_exists( 'wp_script_is' ) ) {
	function wp_script_is( $handle, $list = 'enqueued' ) {
		return isset( $GLOBALS['fx_scripts'][ $handle ] );
	}
}

if ( ! function_exists( 'wp_print_inline_script_tag' ) ) {
	function wp_print_inline_script_tag( $js, $attributes = array() ) {
		echo '<script>' . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

if ( isset( $_GET['fx_consent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$GLOBALS['fx_consent_sim'] = preg_match_all( '/ga|pixel|force|bad/', (string) $_GET['fx_consent'], $fx_m ) ? $fx_m[0] : array(); // phpcs:ignore
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			$sim = $GLOBALS['fx_consent_sim'];
			if ( in_array( 'ga', $sim, true ) ) {
				$d['ga_measurement_id'] = 'G-FALCON0TEST';
			}
			if ( in_array( 'pixel', $sim, true ) ) {
				$d['fb_pixel_id'] = '123456789012345';
			}
			if ( in_array( 'force', $sim, true ) ) {
				$d['show_cookie_consent'] = true;
			}
			if ( in_array( 'bad', $sim, true ) ) {
				$d['ga_measurement_id'] = 'UA-12345-1';
				$d['fb_pixel_id']       = 'abc123';
			}
			return $d;
		},
		99
	);
}

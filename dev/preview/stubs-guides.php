<?php
/**
 * Preview stubs for the guides module (falcon-pro/inc/modules/guides.php).
 * Loaded by dev/preview/wp-stubs.php. Not part of the theme.
 *
 * Step images: a step whose data names a file in falcon-pro/assets/img/guides/ shows that file by default
 * (5th element of the step array). Steps without a bundled file stay text-only until the owner uploads one.
 *
 * Preview-only switches (query string, e.g. /vps-windows/?gdimg=1):
 *   gdimg=0      empty every step image slot (text-only layout, as if no screenshot existed)
 *   gdimg=1      fill every step image slot with a theme banner so the text | image layout can be checked
 *   gdimg=odd    fill only odd steps (mixed layout: image steps + full-width text steps)
 *   gdimg=shots  fill every slot from falcon-pro/assets/img/guides/ (real screenshot sizes: small dialogs,
 *                wide crops, tall phone captures), to check that small files are not upscaled
 *   gdlight=1    render with color_mode 'balanced' (white / light-grey bands instead of dark bands)
 *   gdline=1     set a LINE URL so contact buttons point to LINE instead of /go/
 *   gdbroker=<name>[&gdserver=<name>]
 *                preview the {broker} / {server} tokens with a name taken from the URL and a sample signup link
 *                (opens the disclosure gate). Nothing broker-specific is stored in a file.
 *                Same effect as ?broker=<name>&server=<name> in wp-stubs.php, which works on every page.
 *   gdeager=1    print every image with loading="eager" decoding="sync" (screenshots only). Chrome headless with
 *                --virtual-time-budget takes its screenshot while lazy / async-decoded images are still pending,
 *                so step pictures come out as empty frames at random; with this switch all of them are in the
 *                capture. The theme output itself is not changed.
 *
 * Bundled step images today (file names in fenix_guide_data(), 5th element of a step): open-mt5-account steps 2-6,
 * deposit steps 1-5, mt5-login steps 1-6. Check each guide in three states before a release:
 *   /deposit/                                   generic defaults (no broker name, no partner link)
 *   /deposit/?broker=<name>&server=<name>       real names + disclosure
 *   /deposit/?gdimg=0                           text-only steps (what a visitor sees if the owner removes the pictures)
 *
 * A step that names a file missing from assets/img/guides/ prints a PHP warning in the preview
 * (the theme itself just leaves that slot empty).
 */

/* gdeager=1: outermost output buffer of the request (this file loads before the router starts the keep-case buffer) */
if ( isset( $_GET['gdeager'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- local preview only
	ob_start(
		function ( $html ) {
			return str_replace( array( ' loading="lazy"', ' decoding="async"' ), array( ' loading="eager"', ' decoding="sync"' ), (string) $html );
		}
	);
}

if ( isset( $_GET['gdimg'] ) || isset( $_GET['gdlight'] ) || isset( $_GET['gdline'] ) || isset( $_GET['gdbroker'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- local preview only
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			if ( isset( $_GET['gdlight'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$d['color_mode'] = 'balanced';
			}
			if ( isset( $_GET['gdline'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$d['line_url'] = 'https://lin.ee/preview-only';
			}
			if ( isset( $_GET['gdbroker'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$name = trim( strip_tags( (string) $_GET['gdbroker'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
				if ( '' !== $name ) {
					$d['broker_name']       = mb_substr( $name, 0, 60 );
					$d['broker_signup_url'] = 'https://example.com/signup?ref=preview-only';
				}
				$server = isset( $_GET['gdserver'] ) ? trim( strip_tags( (string) $_GET['gdserver'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				if ( '' !== $server ) {
					$d['broker_server'] = mb_substr( $server, 0, 60 );
				}
			}
			if ( isset( $_GET['gdimg'] ) && function_exists( 'fenix_guide_map' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$mode     = (string) $_GET['gdimg']; // phpcs:ignore WordPress.Security.NonceVerification
				$prefixes = array_merge( array_values( fenix_guide_map() ), array( 'inst' ) );

				/* gdimg=0: no step image at all, including the ones bundled with the theme */
				if ( '0' === $mode ) {
					foreach ( $prefixes as $p ) {
						for ( $i = 1; $i <= fenix_guide_step_count(); $i++ ) {
							$d[ $p . '_step' . $i . '_img' ]        = '';
							$d[ $p . '_step' . $i . '_img_mobile' ] = '';
						}
					}
					return $d;
				}

				$odd  = 'odd' === $mode;
				$base = get_template_directory_uri() . '/assets/img/banners/';
				$pool = array(
					'falcon-pro-ea-mt5-navigator.webp',
					'falcon-pro-ea-mt5-settings-panel.webp',
					'falcon-pro-ea-mt5-mobile-dark.webp',
					'falcon-pro-ea-mt5-laptop-running.webp',
					'falcon-pro-ea-mt5-ea-status-panel.webp',
					'falcon-pro-ea-mt5-tablet-metatrader.webp',
				);
				$roll = false;
				if ( 'shots' === $mode ) {
					$shots = array_map( 'basename', (array) glob( get_template_directory() . '/assets/img/guides/*.webp' ) );
					if ( $shots ) {
						sort( $shots );
						$base = get_template_directory_uri() . '/assets/img/guides/';
						$pool = $shots;
						$roll = true; // keep counting across guides so every file shows up somewhere
					}
				}
				$n = 0;
				foreach ( $prefixes as $p ) {
					for ( $i = 1; $i <= fenix_guide_step_count(); $i++ ) {
						if ( $odd && 0 === $i % 2 ) {
							continue;
						}
						$pick = $roll ? $n++ : $i - 1;
						$d[ $p . '_step' . $i . '_img' ]         = $base . $pool[ $pick % count( $pool ) ];
						$d[ $p . '_step' . $i . '_img_caption' ] = 'ภาพตัวอย่างสำหรับพรีวิวเท่านั้น';
					}
				}
			}
			return $d;
		},
		99
	);
}

/* Step data that names a bundled image which is not on disk: say so in the preview (the theme leaves the slot empty). */
add_action(
	'init',
	function () {
		if ( ! function_exists( 'fenix_guide_missing_images' ) ) {
			return;
		}
		foreach ( fenix_guide_missing_images() as $row ) {
			trigger_error( 'guide step image not found in falcon-pro/assets/img/guides/: ' . $row, E_USER_WARNING ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions, WordPress.Security.EscapeOutput
		}
	}
);

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
 *
 * Useful states of the home chapters (the chapter list and its numbering follow these switches):
 *   /?hm_mod[show_home_compare]=0&hm_mod[show_home_ready]=0   the two add-on chapters off (inc/modules/homeplus.php) → 10 chapters
 *                                                             (12 with everything on · the dashboard chapter is one of them)
 *   /?hm_mod[home_compare_rows]=x                             fewer than header + 1 row → the compare chapter is not counted or printed
 *   /?hm_mod[hero_btn2_url]=%23faq&hm_mod[show_faq]=0         hero text link hidden because its target chapter is off
 *   /?hm_mod[ih_step5_img]=http://localhost:8765/theme/assets/img/guides/<file>.webp   one step picture → the film strip appears
 *   /?hm_mod[home_what_img]=http://localhost:8765/theme/assets/img/banners/<file>.webp square picture → chapter 01 side column
 *   /?hm_mod[home_show_devices]=0                             no device tiles
 * The readiness checklist needs JavaScript for the tick boxes; to see the plain list, remove the class hm-js from <main> in DevTools.
 * Every page counts as published in the preview, so "link hidden while its page is unpublished" cannot be seen here
 * (fenix_home_link() → fenix_published_page_url()); it is covered by reading the code and by a harness run outside the repo.
 *
 * Dashboard on the home page (chapter #dashboard after the start path, quiet visit link in the hero, the "two ways" line of the
 * start path, the visit link under the tests chapter): all of it follows the dashboard module, so use its switches from
 * dev/preview/stubs-dashboard.php, e.g. /?dash=0 (feature off: the chapter is not counted, 11 chapters, no dashboard link anywhere),
 * /?dashimg=0 (block without the screenshot), /?dashhide=visit, plus
 *   /?hm_mod[show_home_dashboard]=0     chapter off: the visit links in the hero and under the tests chapter go with it (the
 *                                       sample-data note lives in the chapter), the "two ways" line of the start path stays
 *   /?hm_mod[home_hero_show_dash]=0     no visit link in the hero
 *
 * Headings stand alone by default (owner decision, 12 Oct 2026). Short switches that turn the removed labels back on, to check
 * that the markup still works when the owner ticks the boxes in the Customizer (they work on every page, not only the home page):
 *   ?kickers=1   show_kickers on (section 1): the small words above headings everywhere: page hero / guide hero kickers,
 *                section-head kickers, the home chapter line "01 / 12 · label", the "ข้อ 01" tag on the home feature cards,
 *                the label of the contact block and of the dashboard block, the label of the Backtest / Forward status card
 *   ?rail=1      home_show_rail on: the vertical chapter rail + the progress bar under the header (home page only),
 *                together with the data-chapter attributes home.js reads
 *   ?brandline=1 home_hero_show_brand on: the letter-spaced brand line above the home headline (off = same text, .sr-only in the H1)
 *   ?wm=1        show_footer_watermark on: the large outlined brand text above the footer status bar
 * Other values of the same kind have no short switch; use hm_mod, e.g. /?hm_mod[hero_badge]=EA%20MT5 (badge above the home H1)
 * or /go/?hm_mod[go_badges]=XAUUSD (chips under the /go title; one chip per line in the Customizer).
 *
 * Tests chapter note: on WordPress the home page reads the published /backtest/ page and looks for the block with
 * id="backtest-results" (fenix_home_backtest_live). The preview has no stored pages, so it reads the seed file instead:
 *   /            seed has the results heading → note that points to the Backtest page and the dashboard
 *   /?hm_bt=0    page still on old content (no results heading) → the "no published results yet" note
 */

if ( function_exists( 'add_filter' ) ) {
	add_filter(
		'fenix_home_backtest_live',
		function ( $live ) {
			if ( isset( $_GET['hm_bt'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				return '0' !== (string) $_GET['hm_bt']; // phpcs:ignore WordPress.Security.NonceVerification
			}
			$fx_seed = dirname( __DIR__, 2 ) . '/falcon-pro/inc/content/pages/backtest.html';
			$fx_html = is_file( $fx_seed ) ? (string) file_get_contents( $fx_seed ) : '';
			return (bool) preg_match( '/\sid\s*=\s*(["\'])backtest-results\1/i', $fx_html );
		}
	);
}

/* ?kickers=1 · ?rail=1 · ?brandline=1 · ?wm=1 : tick the checkboxes that are off by default (see the header of this file) */
if ( function_exists( 'add_filter' ) && ( isset( $_GET['kickers'] ) || isset( $_GET['rail'] ) || isset( $_GET['brandline'] ) || isset( $_GET['wm'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			$fx_switches = array(
				'kickers'   => 'show_kickers',
				'rail'      => 'home_show_rail',
				'brandline' => 'home_hero_show_brand',
				'wm'        => 'show_footer_watermark',
			);
			foreach ( $fx_switches as $fx_param => $fx_key ) {
				if ( isset( $_GET[ $fx_param ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$d[ $fx_key ] = '0' !== (string) $_GET[ $fx_param ]; // phpcs:ignore WordPress.Security.NonceVerification
				}
			}
			return $d;
		},
		98
	);
}

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

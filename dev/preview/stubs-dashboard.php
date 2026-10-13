<?php
/**
 * Preview stubs for the dashboard module (falcon-pro/inc/modules/dashboard.php).
 * Loaded by dev/preview/wp-stubs.php. Not part of the theme.
 *
 * The page itself is /dashboard/ (template-dashboard.php + inc/content/pages/dashboard.html).
 *
 * Preview-only switches (query string, e.g. /dashboard/?dash=0):
 *   dash=0        feature off (show_dashboard unticked): every dashboard button, note and promo block disappears,
 *                 the page keeps its text · [falcon_dashboard] buttons in the long text print nothing
 *   dashurl=0     feature on but the address field empty (same result as dash=0)
 *   dashimg=0     empty both screenshot slots of /dashboard/ (the Preview section and the picture of the home block drop out)
 *   dashlight=1   render with color_mode 'balanced' (white / light-grey bands instead of dark bands)
 *   dashline=1    set a LINE URL so contact buttons point to LINE instead of /go/
 *   dashhide=visit|signup|signin   blank that button label (the button and its note are hidden everywhere)
 *   dashblock=home|go|pricing|install|page
 *                 print fenix_dashboard_block( <context> ) on the page you are looking at, so the reusable promo block can be
 *                 checked before another template places it: before </main> on normal pages, above the risk note on /go/.
 *                 Add &dashwrap=0 for the card without its own <section> (as placed inside an existing section).
 *                 Examples: /?dashblock=home · /pricing/?dashblock=pricing · /how-to-install/?dashblock=install · /go/?dashblock=go
 *
 * Check before a release: /dashboard/ · /dashboard/?dash=0 · /dashboard/?dashimg=0 · /dashboard/?dashlight=1
 * · /dashboard/?broker=<name>&server=<name> (the long text names the broker and prints the partner disclosure).
 *
 * The two screenshots in falcon-pro/assets/img/dashboard/ (defaults of the image slots dash_shot1 / dash_shot2) were taken
 * from the dashboard's visit mode (address of the Customizer setting dashboard_url + "#visit", which opens the "choose EA"
 * page) with headless Chrome, 12 Oct 2026:
 *   falcon-pro-dashboard-choose-ea-v1.webp        window 1280 × 900 at 2x, cut to the main column (the side menu names the
 *                                                 broker, so it is left out) from the top down to the lower edge of the EA
 *                                                 card pictures, saved 1600 px wide
 *   falcon-pro-dashboard-choose-ea-phone-v1.webp  phone window 390 × 844 at 2x, first screen, saved 780 px wide
 * Only the page's own buttons were pressed before the capture (close on the "install the app" hint, "later" on the passkey
 * hint). Nothing was drawn or edited. Neither picture shows a balance, a profit and loss figure or a backtest figure.
 * A new capture gets a new version number in its file name (browsers and the host cache by URL), must not show the broker's
 * name, and needs the sample-data caption when balances or profit and loss are in view.
 */

// phpcs:disable WordPress.Security.NonceVerification -- local preview only

if ( isset( $_GET['dash'] ) || isset( $_GET['dashurl'] ) || isset( $_GET['dashimg'] ) || isset( $_GET['dashlight'] ) || isset( $_GET['dashline'] ) || isset( $_GET['dashhide'] ) ) {
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			if ( isset( $_GET['dash'] ) && '0' === (string) $_GET['dash'] ) {
				$d['show_dashboard'] = false;
			}
			if ( isset( $_GET['dashurl'] ) && '0' === (string) $_GET['dashurl'] ) {
				$d['dashboard_url'] = '';
			}
			if ( isset( $_GET['dashimg'] ) && '0' === (string) $_GET['dashimg'] ) {
				foreach ( array( 'dash_shot1', 'dash_shot2' ) as $key ) {
					$d[ $key . '_img' ]        = '';
					$d[ $key . '_img_mobile' ] = '';
				}
			}
			if ( isset( $_GET['dashlight'] ) ) {
				$d['color_mode'] = 'balanced';
			}
			if ( isset( $_GET['dashline'] ) ) {
				$d['line_url'] = 'https://lin.ee/preview-only';
			}
			if ( isset( $_GET['dashhide'] ) && in_array( (string) $_GET['dashhide'], array( 'visit', 'signup', 'signin' ), true ) ) {
				$d[ 'dashboard_' . $_GET['dashhide'] . '_label' ] = '';
			}
			return $d;
		},
		99
	);
}

/*
 * dashblock=<context>: the block is rendered once the page head is done (wp_body_open, so every setting and conditional
 * tag is ready), kept in a global, and put into the finished page by the output buffer started here.
 */
if ( isset( $_GET['dashblock'] ) ) {
	$GLOBALS['fx_dash_block'] = '';
	add_action(
		'wp_body_open',
		function () {
			if ( ! function_exists( 'fenix_dashboard_block' ) ) {
				return;
			}
			$context = preg_replace( '/[^a-z]/', '', strtolower( (string) $_GET['dashblock'] ) );
			$args    = array();
			if ( isset( $_GET['dashwrap'] ) && '0' === (string) $_GET['dashwrap'] ) {
				$args['wrap'] = false;
			}
			ob_start();
			fenix_dashboard_block( $context, $args );
			$html = (string) ob_get_clean();
			if ( isset( $args['wrap'] ) && 'go' !== $context ) {
				/* a card without its own section still needs a section + container around it to be judged */
				$html = '<section class="section' . fenix_dashboard_tone() . '"><div class="container">' . $html . '</div></section>';
			}
			$GLOBALS['fx_dash_block'] = $html;
		}
	);
	ob_start(
		function ( $page ) {
			$block = isset( $GLOBALS['fx_dash_block'] ) ? (string) $GLOBALS['fx_dash_block'] : '';
			if ( '' === $block ) {
				return $page;
			}
			foreach ( array( '<p class="lh-note">', '<p class="lh-legal">', '</main>' ) as $mark ) {
				$at = strrpos( (string) $page, $mark );
				if ( false !== $at ) {
					return substr( $page, 0, $at ) . $block . substr( $page, $at );
				}
			}
			return $page . $block;
		}
	);
}
// phpcs:enable

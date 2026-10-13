<?php
/**
 * Preview stubs for the /go link hub module (falcon-pro/inc/modules/go.php).
 * Loaded by dev/preview/wp-stubs.php. Not part of the theme.
 *
 * Preview-only switches (query string, e.g. /go/?godemo=1):
 *   godemo=1     fill LINE / OpenChat / socials / broker portal link so every block of /go renders
 *                (add &broker=NAME&server=NAME from wp-stubs.php to see the partner signup button on step 1)
 *   gonote=1     put a sample text in the note of step 1 (the default note says the dashboard takes real accounts only)
 *   godeposit=0  blank the deposit guide link: without godemo the whole deposit step drops out,
 *                with godemo the guide button shows in its dashed "coming soon" state next to the portal button
 *   golight=1    render with color_mode 'balanced' (white card on the dark page)
 *   goconsent=1  force the cookie consent card open (show_cookie_consent) to check it on the standalone page
 *   gohide=2,4   blank the titles of those steps (go_stepN_title) to check that hidden steps drop out and the rest renumber
 *   gopending=1  clear every link of the info group and the VPS grid to check the dashed "coming soon" state
 *   gofast=1     set a sample EA download link + card image (the EA file step shows the image card instead of the LINE button;
 *                the step itself needs goold=1, it is off by default)
 *   goold=1      tick the three steps of the retired self-install route (go_show_download, go_show_install, go_show_vps):
 *                their buttons to retired pages (/how-to-install/, /vps-*) must not print · goold=download,vps ticks only those
 *   godash=1     tick "show the dashboard card" (go_show_dashboard, off by default): the compact dashboard card between the
 *                contact buttons and the steps · godash=0 forces it off
 * The default route is 4 steps: account → MT5 app → deposit → start on the dashboard (visit + signup buttons, key go_dash_*).
 * That last step and the optional card come from the dashboard module, so its switches from dev/preview/stubs-dashboard.php
 * apply here too: /go/?dash=0 (feature off: no dashboard step, 3 steps), /go/?dashhide=signup (one button), /go/?golight=1.
 */

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( isset( $_GET['godemo'] ) || isset( $_GET['gonote'] ) || isset( $_GET['godeposit'] ) || isset( $_GET['golight'] ) || isset( $_GET['goconsent'] ) || isset( $_GET['gohide'] ) || isset( $_GET['gopending'] ) || isset( $_GET['gofast'] ) || isset( $_GET['godash'] ) || isset( $_GET['goold'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- local preview only
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			// phpcs:disable WordPress.Security.NonceVerification -- local preview only
			if ( isset( $_GET['golight'] ) ) {
				$d['color_mode'] = 'balanced';
			}
			if ( isset( $_GET['godash'] ) ) {
				$d['go_show_dashboard'] = '0' !== (string) $_GET['godash'];
			}
			if ( isset( $_GET['goold'] ) ) {
				$fx_old = '1' === (string) $_GET['goold'] ? array( 'download', 'install', 'vps' ) : array_map( 'trim', explode( ',', (string) $_GET['goold'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				foreach ( array( 'download', 'install', 'vps' ) as $fx_step ) {
					$d[ 'go_show_' . $fx_step ] = in_array( $fx_step, $fx_old, true );
				}
			}
			if ( isset( $_GET['godemo'] ) ) {
				$d['line_url']               = 'https://lin.ee/preview-only';
				$d['line_openchat_url']      = 'https://line.me/ti/g2/preview-only';
				$d['facebook_url']           = 'https://www.facebook.com/preview-only';
				$d['instagram_url']          = 'https://www.instagram.com/preview-only';
				$d['tiktok_url']             = 'https://www.tiktok.com/@preview-only';
				$d['youtube_url']            = 'https://www.youtube.com/@preview-only';
				$d['go_deposit_url']         = 'https://example.com/portal';
			}
			if ( isset( $_GET['gonote'] ) ) {
				$d['go_step1_note'] = 'ตัวอย่างพรีวิว: หมายเหตุของขั้นเปิดบัญชีจะแสดงตรงนี้เมื่อเจ้าของกรอก';
			}
			if ( isset( $_GET['godeposit'] ) && '0' === (string) $_GET['godeposit'] ) {
				$d['go_deposit_guide_url'] = '';
			}
			if ( isset( $_GET['goconsent'] ) ) {
				$d['show_cookie_consent'] = true;
			}
			if ( isset( $_GET['gohide'] ) ) {
				foreach ( explode( ',', (string) $_GET['gohide'] ) as $n ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					$n = (int) $n;
					if ( $n >= 1 && $n <= 6 ) {
						$d[ 'go_step' . $n . '_title' ] = '';
					}
				}
			}
			if ( isset( $_GET['gopending'] ) ) {
				for ( $i = 1; $i <= 6; $i++ ) {
					$d[ 'go_btn' . $i . '_url' ] = '';
				}
				foreach ( array( 'windows', 'android', 'ios' ) as $os ) {
					$d[ 'go_vps_' . $os . '_url' ] = '';
				}
			}
			if ( isset( $_GET['gofast'] ) ) {
				$d['go_fast_url'] = 'https://example.com/files/falcon-pro-ea-preview.zip';
				$d['go_fast_img'] = get_template_directory_uri() . '/assets/img/brand/falcon-pro-share-1200x630.jpg';
			}
			// phpcs:enable
			return $d;
		},
		99
	);
}

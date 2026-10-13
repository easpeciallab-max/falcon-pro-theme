<?php
/**
 * Preview stubs for the /go link hub module (falcon-pro/inc/modules/go.php).
 * Loaded by dev/preview/wp-stubs.php. Not part of the theme.
 *
 * Preview-only switches (query string, e.g. /go/?godemo=1):
 *   godemo=1     fill LINE / OpenChat / socials / broker portal link so every block of /go renders
 *                (add &broker=NAME&server=NAME from wp-stubs.php to see the partner signup button on step 1)
 *   gonote=1     put a sample text in the optional note of step 1 (empty by default: /go carries no partner note)
 *   godeposit=0  blank the deposit guide link: without godemo the whole deposit step drops out (5 steps),
 *                with godemo the guide button shows in its dashed "coming soon" state next to the portal button
 *   golight=1    render with color_mode 'balanced' (white card on the dark page)
 *   goconsent=1  force the cookie consent card open (show_cookie_consent) to check it on the standalone page
 *   gohide=2,4   blank the titles of those steps to check that hidden steps drop out and the rest renumber
 *   gopending=1  clear every link of the info group and the VPS grid to check the dashed "coming soon" state
 *   gofast=1     set a sample EA download link + card image (step 4 shows the image card instead of the LINE button)
 *   godash=0     untick "show the dashboard card" (go_show_dashboard): the compact dashboard card between the contact buttons
 *                and the self-install steps disappears, everything else stays
 * The dashboard card itself comes from the dashboard module (fenix_dashboard_block( 'go' )), so its own switches from
 * dev/preview/stubs-dashboard.php apply here too: /go/?dash=0 (feature off: no card), /go/?dashhide=signup (one button),
 * /go/?golight=1 (card on the white /go card).
 */

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( isset( $_GET['godemo'] ) || isset( $_GET['gonote'] ) || isset( $_GET['godeposit'] ) || isset( $_GET['golight'] ) || isset( $_GET['goconsent'] ) || isset( $_GET['gohide'] ) || isset( $_GET['gopending'] ) || isset( $_GET['gofast'] ) || isset( $_GET['godash'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- local preview only
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			// phpcs:disable WordPress.Security.NonceVerification -- local preview only
			if ( isset( $_GET['golight'] ) ) {
				$d['color_mode'] = 'balanced';
			}
			if ( isset( $_GET['godash'] ) && '0' === (string) $_GET['godash'] ) {
				$d['go_show_dashboard'] = false;
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

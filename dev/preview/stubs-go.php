<?php
/**
 * Preview stubs for the /go link hub module (falcon-pro/inc/modules/go.php).
 * Loaded by dev/preview/wp-stubs.php. Not part of the theme.
 *
 * Preview-only switches (query string, e.g. /go/?godemo=1):
 *   godemo=1     fill LINE / OpenChat / socials / partner note / deposit so every block of /go renders
 *   golight=1    render with color_mode 'balanced' (white card on the dark page)
 *   goconsent=1  force the cookie consent card open (show_cookie_consent) to check it on the standalone page
 *   gohide=2,4   blank the titles of those steps to check that hidden steps drop out and the rest renumber
 *   gopending=1  clear every link of the info group and the VPS grid to check the dashed "coming soon" state
 *   gofast=1     set a sample EA download link + card image (step 4 shows the image card instead of the LINE button)
 */

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( isset( $_GET['godemo'] ) || isset( $_GET['golight'] ) || isset( $_GET['goconsent'] ) || isset( $_GET['gohide'] ) || isset( $_GET['gopending'] ) || isset( $_GET['gofast'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- local preview only
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			// phpcs:disable WordPress.Security.NonceVerification -- local preview only
			if ( isset( $_GET['golight'] ) ) {
				$d['color_mode'] = 'balanced';
			}
			if ( isset( $_GET['godemo'] ) ) {
				$d['line_url']               = 'https://lin.ee/preview-only';
				$d['line_openchat_url']      = 'https://line.me/ti/g2/preview-only';
				$d['facebook_url']           = 'https://www.facebook.com/preview-only';
				$d['instagram_url']          = 'https://www.instagram.com/preview-only';
				$d['tiktok_url']             = 'https://www.tiktok.com/@preview-only';
				$d['youtube_url']            = 'https://www.youtube.com/@preview-only';
				$d['go_step1_note']          = 'ตัวอย่างพรีวิว: ข้อความเปิดเผยความเป็นพันธมิตรจะแสดงตรงนี้เมื่อเจ้าของกรอก';
				$d['go_deposit_label']       = 'ตัวอย่างพรีวิว: หน้าสมาชิกโบรกเกอร์';
				$d['go_deposit_url']         = 'https://example.com/portal';
				$d['go_deposit_guide_label'] = 'ตัวอย่างพรีวิว: คู่มือฝากเงิน';
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

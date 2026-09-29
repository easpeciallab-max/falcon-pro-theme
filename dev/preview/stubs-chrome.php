<?php
/**
 * Preview stubs for the SITE CHROME module (header/footer/dock) · not part of the theme.
 * Loaded by dev/preview/wp-stubs.php · every function is wrapped so other modules' stubs can coexist.
 */

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( ! function_exists( 'wp_date' ) ) {
	function wp_date( $format, $timestamp = null, $timezone = null ) {
		$date = new DateTime( '@' . ( null === $timestamp ? time() : (int) $timestamp ) );
		$date->setTimezone( $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone( 'Asia/Bangkok' ) );
		return $date->format( $format );
	}
}

if ( ! function_exists( 'antispambot' ) ) {
	function antispambot( $email ) {
		return (string) $email;
	}
}

if ( ! function_exists( 'is_email' ) ) {
	function is_email( $email ) {
		return false !== filter_var( (string) $email, FILTER_VALIDATE_EMAIL ) ? $email : false;
	}
}

/*
 * โหมดตัวอย่าง (เฉพาะ preview): ?fx_chrome_demo=1 → เติมช่องทางติดต่อตัวอย่าง (ลิงก์ example ไม่ใช่ของจริง)
 * ใช้ดูสถานะ "มี LINE แล้ว": QR, OpenChat, คอลัมน์ช่องทาง, ปุ่ม LINE กลางบาร์ล่าง, ปุ่ม LINE ลอย
 */
if ( isset( $_GET['fx_chrome_demo'] ) ) {
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			return array_merge(
				$d,
				array(
					'line_url'          => 'https://lin.ee/example',
					'line_openchat_url' => 'https://line.me/ti/g2/example',
					'line_qr_image'     => 'http://localhost:8765/theme/assets/img/brand/falcon-pro-icon-512.png',
					'facebook_url'      => 'https://www.facebook.com/example',
					'youtube_url'       => 'https://www.youtube.com/@example',
					'contact_email'     => 'team@example.com',
					'footer_hours_text' => "จันทร์–ศุกร์ 10:00–19:00 น. (ตัวอย่างใน preview)",
				)
			);
		},
		99
	);
}

/* $wp->request: เส้นทางของหน้าปัจจุบัน (บาร์ล่างมือถือใช้คิดช่องที่ active) */
if ( ! isset( $GLOBALS['wp'] ) ) {
	$fx_chrome_path = parse_url( isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH );
	$GLOBALS['wp']  = (object) array( 'request' => trim( is_string( $fx_chrome_path ) ? $fx_chrome_path : '', '/' ) );
}

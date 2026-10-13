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

/* fenix_chrome_dock() caches its result once WordPress has parsed the request (did_action( 'wp' )) · the preview is always past that point */
if ( ! function_exists( 'did_action' ) ) {
	function did_action( $hook = '' ) {
		return 1;
	}
}

/*
 * โหมดตัวอย่าง (เฉพาะ preview)
 *   ?fx_chrome_demo=1     เติมช่องทางติดต่อตัวอย่างครบทุกช่อง (ลิงก์ example ไม่ใช่ของจริง · รูป QR เป็นรูปแทน)
 *                         ใช้ดูสถานะ "มีครบ": ปุ่มติดต่อบนแถบหัว, QR, OpenChat, อีเมล, เวลาตอบแชท, คอลัมน์ช่องทาง, ปุ่ม LINE กลางบาร์ล่าง
 *   ?fx_chrome_demo=live  ใส่เฉพาะลิงก์ LINE OA ของ FALCON (เท่ากับค่าบนเว็บจริงตอนนี้) → เห็นรูป QR ที่มากับธีม
 *                         (รูปนั้นแสดงเฉพาะเมื่อลิงก์ LINE ตรงกับที่ QR เข้ารหัสไว้ · ดู fenix_chrome_qr_url)
 *   &fx_chrome_nodock=1   ปิดบาร์ล่างมือถือ → ปุ่มติดต่อบนแถบหัวยังอยู่ในจอแคบ
 *   ?fx_chrome_nodash=1   ลบชื่อเมนู "แดชบอร์ด" (nav_dashboard_label ว่าง) → หน้า /dashboard/ กลับไปอยู่ใต้ "คู่มือการใช้งาน"
 *                         ทั้งในเมนูสำรองและในคอลัมน์คู่มือของ footer (ไม่มีรายการระดับบน)
 * แถวสมาชิกใน footer (ลิงก์เข้าสู่ระบบแดชบอร์ด) ตามโมดูล dashboard: ?dash=0 หรือ ?dashhide=signin = แถวนี้หายไป
 * (สวิตช์อยู่ใน dev/preview/stubs-dashboard.php) · พรีวิวไม่มีเมนูที่ตั้งเอง จึงเห็นเฉพาะเมนูสำรอง
 */
if ( isset( $_GET['fx_chrome_demo'] ) || isset( $_GET['fx_chrome_nodock'] ) || isset( $_GET['fx_chrome_nodash'] ) ) {
	add_filter(
		'fenix_defaults',
		function ( $d ) {
			// phpcs:disable WordPress.Security.NonceVerification -- local preview only
			if ( isset( $_GET['fx_chrome_nodock'] ) ) {
				$d['show_mobile_nav'] = false;
			}
			if ( isset( $_GET['fx_chrome_nodash'] ) ) {
				$d['nav_dashboard_label'] = '';
			}
			if ( ! isset( $_GET['fx_chrome_demo'] ) ) {
				return $d;
			}
			if ( 'live' === (string) $_GET['fx_chrome_demo'] ) {
				$d['line_url'] = 'https://lin.ee/9Ct7DTS';
				return $d;
			}
			// phpcs:enable
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

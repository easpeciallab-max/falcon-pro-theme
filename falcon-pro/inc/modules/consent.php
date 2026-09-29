<?php
/**
 * FALCON PRO EA · PDPA consent card + GA4 / Meta Pixel IDs
 *
 * วิธีทำงาน
 * - fenix_consent_card() ต่อท้ายทุกหน้าทาง wp_footer (priority 5) หน้า /go/ แบบไม่มีกรอบธีมก็ได้การ์ดด้วย
 * - ฝั่ง PHP ไม่แตะคุกกี้ของผู้เข้าชม เพื่อให้ปลั๊กอินแคชเก็บหน้าเดียวใช้ได้กับทุกคน · การแสดง/ซ่อนการ์ดเป็นงานของ assets/js/consent.js
 * - HTML ไม่มีสคริปต์ GA4 หรือ Pixel ติดมา · consent.js ค่อยใส่สคริปต์ของหมวดที่ผู้เข้าชมกดอนุญาต
 * - Consent Mode v2: <head> ประกาศทุกสัญญาณเป็น denied ไว้ก่อน แล้ว consent.js ส่ง update เมื่อได้รับอนุญาต
 * - เงื่อนไขเปิดการ์ดอัตโนมัติ: มีรหัส GA4/Pixel ถูกรูปแบบ หรือเจ้าของเว็บติ๊ก show_cookie_consent
 * - รูปแบบคุกกี้ fenix_consent: v<รุ่น>.a<0|1|->.m<0|1|->.t<unix> ("-" คือหมวดที่ตอนตอบยังไม่มีบริการ) · 365 วัน · SameSite=Lax
 * - คำบนการ์ดแก้ได้ทั้งหมดที่ ปรับแต่ง → หมวด 18 (fenix_cookie)
 * - เปิดการ์ดซ้ำจากที่ไหนก็ได้ด้วย <a href="#cookie-settings"> หรือคลาส .cookie-reopen (ตัวช่วย: fenix_consent_link())
 * - API สำหรับสคริปต์อื่น: window.fenixConsent.has('analytics'|'marketing') และเหตุการณ์ 'fenix:consent' บน document
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น (ข้อความของ FALCON เอง)
 * ============================================================== */

function fenix_consent_defaults( $d ) {
	return array_merge(
		$d,
		array(
			/* คีย์เดิมที่ใช้ต่อ · show_cookie_consent = แสดงการ์ดแม้ยังไม่มีรหัสติดตาม · cookie_consent_text = ข้อความชั้นแรก */
			'show_cookie_consent'      => false,
			'cookie_consent_text'      => 'ค่าเริ่มต้นของเว็บไซต์นี้คือเปิดแค่คุกกี้ที่ระบบต้องใช้ ส่วนคุกกี้วัดสถิติและคุกกี้โฆษณาจะเริ่มทำงานก็ต่อเมื่อคุณอนุญาต และถอนการอนุญาตได้ทุกเมื่อ',
			'ga_measurement_id'        => '',
			'fb_pixel_id'              => '',

			'consent_version'          => '1',
			'consent_kicker'           => 'ความเป็นส่วนตัวของคุณ',
			'consent_title'            => 'คุณเลือกเองได้ว่าจะเปิดคุกกี้หมวดไหน',
			'consent_policy_label'     => 'ดูนโยบายคุกกี้',
			'consent_accept_label'     => 'ยอมรับทั้งหมด',
			'consent_reject_label'     => 'ปฏิเสธที่ไม่จำเป็น',
			'consent_prefs_label'      => 'เลือกทีละหมวด',
			'consent_save_label'       => 'บันทึกตัวเลือก',
			'consent_ok_label'         => 'รับทราบ',
			'consent_close_label'      => 'ปิดการ์ดคุกกี้',
			'consent_always_label'     => 'ทำงานตลอด',
			'consent_detail_label'     => 'ดูรายละเอียดคุกกี้',
			'consent_necessary_name'   => 'คุกกี้จำเป็นต่อระบบ',
			'consent_necessary_desc'   => 'ใช้จำคำตอบเรื่องคุกกี้ของคุณ เว็บจะได้ไม่ถามซ้ำทุกหน้า หมวดนี้ปิดไม่ได้ และไม่ได้ใช้ติดตามว่าคุณเป็นใคร',
			'consent_necessary_detail' => "fenix_consent · จำคำตอบแยกตามหมวด พร้อมเลขรุ่นความยินยอมและเวลาที่กดบันทึก · เก็บ 365 วัน\nfenixConsentDismissed · จดไว้ในเบราว์เซอร์ว่าคุณกดปิดการ์ดในรอบนี้ · ลบเองเมื่อปิดแท็บ",
			'consent_analytics_name'   => 'วัดสถิติการเข้าชม',
			'consent_analytics_desc'   => 'ใช้ Google Analytics 4 นับจำนวนผู้เข้าชม ดูว่าหน้าไหนถูกเปิดอ่านและปุ่มไหนถูกกด เพื่อให้เรารู้ว่าควรปรับเนื้อหาส่วนใด',
			'consent_analytics_detail' => "ผู้ให้บริการ · Google\n_ga · สร้างรหัสสุ่มประจำเบราว์เซอร์เพื่อนับผู้เข้าชมโดยไม่รู้ว่าเป็นใคร · สูงสุดราว 2 ปี\n_ga_* · จำรอบการเข้าชมปัจจุบันของคุณ · สูงสุดราว 2 ปี",
			'consent_marketing_name'   => 'วัดผลโฆษณา',
			'consent_marketing_desc'   => 'ใช้ Meta Pixel ตรวจว่าแคมเปญที่เราลงไว้ใน Facebook หรือ Instagram พาผู้สนใจมาถึงเว็บไซต์ได้จริงไหม และใช้เลือกกลุ่มผู้ชมของโฆษณาครั้งต่อไป',
			'consent_marketing_detail' => "ผู้ให้บริการ · Meta Platforms\n_fbp · จับคู่เบราว์เซอร์นี้กับการเข้าชมจากโฆษณา · ราว 90 วัน\n_fbc · เก็บรหัสการคลิกเมื่อคุณมาจากโฆษณา · ราว 90 วัน\nสัญญาณโฆษณาของ Google (เมื่อเว็บใช้ GA4) · อนุญาตให้ Google นำข้อมูลการเข้าชมไปใช้กับงานโฆษณา · มีผลเฉพาะเมื่อเปิดหมวดนี้",
			'consent_none_text'        => 'ขณะนี้เว็บไซต์ยังไม่ได้ติดตั้งเครื่องมือวัดสถิติหรือเครื่องมือโฆษณาใด ๆ จึงมีเพียงคุกกี้หมวดจำเป็นที่ทำงานอยู่',
			'consent_prefs_note'       => 'จะกลับมาเปลี่ยนตัวเลือกเมื่อไรก็ได้ เพียงกด "ตั้งค่าคุกกี้" ที่ส่วนล่างของหน้า ไม่ว่าจะเลือกแบบไหน คุณยังอ่านได้ทุกหน้าและทักหาเราทาง LINE ได้ตามปกติ',
			'consent_link_label'       => 'ตั้งค่าคุกกี้',
		)
	);
}
add_filter( 'fenix_defaults', 'fenix_consent_defaults' );

/* ==============================================================
 * Customizer · แทนหมวดคุกกี้เดิมทั้งหมวด (คงตำแหน่งเดิมในรายการ)
 * ============================================================== */

function fenix_consent_sections( $sections, $d ) {
	$own = array( 'show_cookie_consent', 'cookie_consent_text', 'ga_measurement_id', 'fb_pixel_id' );

	/* ย้ายฟิลด์ติดตาม/คุกกี้ออกจากหมวดอื่น (เช่น fenix_seo) ให้อยู่ที่นี่ที่เดียว */
	foreach ( $sections as $section_id => $section ) {
		if ( 'fenix_cookie' === $section_id || empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
			continue;
		}
		foreach ( $own as $key ) {
			unset( $sections[ $section_id ]['fields'][ $key ] );
		}
	}

	$detail_hint = 'บรรทัดละ 1 รายการ · รูปแบบ "ชื่อคุกกี้ · ใช้ทำอะไร · อายุ" · ชื่อคุกกี้แสดงตามตัวพิมพ์จริง';

	$sections['fenix_cookie'] = array(
		'title'       => '18) คุกกี้ & ความยินยอม · PDPA',
		'description' => 'การ์ดนี้แสดงเองทันทีที่มี GA4 Measurement ID หรือ Meta Pixel ID ถูกรูปแบบอย่างน้อยหนึ่งตัว (ติ๊กช่องด้านล่างถ้าอยากให้แสดงตั้งแต่ยังไม่มีรหัส) · สคริปต์ของแต่ละบริการเริ่มทำงานหลังผู้เข้าชมกดอนุญาตหมวดของบริการนั้นแล้วเท่านั้น · ห้ามให้ปลั๊กอินอื่นฝังแท็กซ้ำ (เช่น Site Kit หรือปลั๊กอิน Pixel) เพราะจะทำงานก่อนได้รับอนุญาต · เมื่อเพิ่มบริการหรือเปลี่ยนวัตถุประสงค์ของคุกกี้ ให้บวกเลขรุ่นขึ้น 1 ระบบจะขอความยินยอมจากทุกคนอีกรอบ · ลิงก์นโยบายโผล่เมื่อหน้า privacy-policy เผยแพร่แล้ว (ชี้ไปหัวข้อ #cookies) · ลิงก์ใด ๆ ที่ชี้ไป #cookie-settings จะเปิดการ์ดนี้',
		'fields'      => array(
			'ga_measurement_id'        => array( 'GA4 Measurement ID (รูปแบบ G-XXXXXXXXXX)', 'text', 'หาได้ใน GA4 → Admin → Data streams · ถ้ารูปแบบผิด ระบบจะไม่ยอมบันทึก' ),
			'fb_pixel_id'              => array( 'Meta Pixel ID (ตัวเลข 10–20 หลัก)', 'text', 'หาได้ใน Meta Events Manager → Data sources · ถ้ารูปแบบผิด ระบบจะไม่ยอมบันทึก' ),
			'show_cookie_consent'      => array( 'แสดงการ์ดคุกกี้แม้ยังไม่ได้ใส่รหัสติดตาม', 'checkbox' ),
			'consent_version'          => array( 'เลขรุ่นของความยินยอม', 'text', 'บวกเลขขึ้นเมื่อเปลี่ยนวิธีใช้คุกกี้ ระบบจะขอความยินยอมใหม่จากทุกคน' ),
			'consent_kicker'           => array( 'การ์ด · ข้อความเล็กเหนือหัวข้อ', 'text' ),
			'consent_title'            => array( 'การ์ด · หัวข้อ', 'text' ),
			'cookie_consent_text'      => array( 'การ์ด · ข้อความหลัก (ก่อนกดเลือกทีละหมวด)', 'textarea' ),
			'consent_policy_label'     => array( 'การ์ด · คำบนลิงก์ไปนโยบายคุกกี้', 'text' ),
			'consent_accept_label'     => array( 'ข้อความปุ่ม · ยอมรับทุกหมวด', 'text' ),
			'consent_reject_label'     => array( 'ข้อความปุ่ม · ปฏิเสธทุกหมวดที่ไม่จำเป็น', 'text' ),
			'consent_prefs_label'      => array( 'ข้อความปุ่ม · ไปหน้าเลือกทีละหมวด', 'text' ),
			'consent_save_label'       => array( 'ข้อความปุ่ม · บันทึกตัวเลือกที่ติ๊กไว้', 'text' ),
			'consent_ok_label'         => array( 'ข้อความปุ่ม · รับทราบ (ใช้เมื่อยังไม่มีรหัสติดตาม)', 'text' ),
			'consent_close_label'      => array( 'ปุ่ม X · คำอ่านสำหรับผู้ใช้โปรแกรมอ่านจอ', 'text' ),
			'consent_always_label'     => array( 'ป้ายบนหมวดจำเป็น (ปิดไม่ได้)', 'text' ),
			'consent_detail_label'     => array( 'คำบนปุ่มเปิดรายละเอียดคุกกี้ของแต่ละหมวด', 'text' ),
			'consent_necessary_name'   => array( 'หมวดจำเป็น · ชื่อหมวด', 'text' ),
			'consent_necessary_desc'   => array( 'หมวดจำเป็น · คำอธิบายสั้น', 'textarea' ),
			'consent_necessary_detail' => array( 'หมวดจำเป็น · รายละเอียดคุกกี้', 'textarea', $detail_hint ),
			'consent_analytics_name'   => array( 'หมวดวัดสถิติ (GA4) · ชื่อหมวด', 'text' ),
			'consent_analytics_desc'   => array( 'หมวดวัดสถิติ (GA4) · คำอธิบายสั้น', 'textarea' ),
			'consent_analytics_detail' => array( 'หมวดวัดสถิติ (GA4) · รายละเอียดคุกกี้', 'textarea', $detail_hint ),
			'consent_marketing_name'   => array( 'หมวดโฆษณา (Meta Pixel) · ชื่อหมวด', 'text' ),
			'consent_marketing_desc'   => array( 'หมวดโฆษณา (Meta Pixel) · คำอธิบายสั้น', 'textarea' ),
			'consent_marketing_detail' => array( 'หมวดโฆษณา (Meta Pixel) · รายละเอียดคุกกี้', 'textarea', $detail_hint ),
			'consent_none_text'        => array( 'ข้อความเมื่อยังไม่ได้ใส่รหัสติดตามใด ๆ', 'textarea' ),
			'consent_prefs_note'       => array( 'ข้อความใต้รายการหมวด (หน้าเลือกทีละหมวด)', 'textarea' ),
			'consent_link_label'       => array( 'ข้อความลิงก์ "ตั้งค่าคุกกี้" (หน้า /go และจุดที่ใช้ fenix_consent_link)', 'text' ),
		),
	);

	return $sections;
}
add_filter( 'fenix_customizer_sections', 'fenix_consent_sections', 30, 2 );

/* ==============================================================
 * ตรวจรูปแบบรหัสติดตาม
 * ============================================================== */

/**
 * GA4 Measurement ID ที่ใช้ได้ (G-XXXX) หรือ '' ถ้ารูปแบบไม่ตรง
 */
function fenix_consent_sanitize_ga_id( $value ) {
	$value = strtoupper( preg_replace( '/\s+/', '', (string) $value ) );
	return preg_match( '/^G-[A-Z0-9]{4,20}$/', $value ) ? $value : '';
}

/**
 * Meta Pixel ID ที่ใช้ได้ (ตัวเลข 10–20 หลัก) หรือ '' ถ้ารูปแบบไม่ตรง
 */
function fenix_consent_sanitize_pixel_id( $value ) {
	$value = preg_replace( '/\s+/', '', (string) $value );
	return preg_match( '/^\d{10,20}$/', $value ) ? $value : '';
}

/**
 * รุ่นความยินยอม · จำนวนเต็มตั้งแต่ 1 ขึ้นไป
 */
function fenix_consent_sanitize_version( $value ) {
	return (string) max( 1, absint( $value ) );
}

/**
 * รหัสผิดรูปแบบ: แสดงข้อผิดพลาดตรงช่องกรอกและไม่ยอมบันทึก เจ้าของเว็บจะได้รู้ตัวทันที
 */
function fenix_consent_validate_id( $validity, $value, $setting = null ) {
	$value = trim( (string) $value );
	if ( '' === $value || ! is_object( $setting ) || ! isset( $setting->id ) || ! is_object( $validity ) ) {
		return $validity;
	}
	if ( 'ga_measurement_id' === $setting->id && '' === fenix_consent_sanitize_ga_id( $value ) ) {
		$validity->add( 'fenix_bad_ga_id', 'รูปแบบรหัส GA4 ไม่ถูกต้อง · รหัสที่ใช้ได้หน้าตาแบบ G-AB12CD34EF (เปิด GA4 → Admin → Data streams แล้วคัดลอก Measurement ID)' );
	}
	if ( 'fb_pixel_id' === $setting->id && '' === fenix_consent_sanitize_pixel_id( $value ) ) {
		$validity->add( 'fenix_bad_pixel_id', 'รูปแบบรหัส Meta Pixel ไม่ถูกต้อง · ใส่เฉพาะตัวเลข 10–20 หลัก ห้ามมีตัวอักษรหรือช่องว่าง (คัดลอกจาก Meta Events Manager → Data sources)' );
	}
	return $validity;
}

/* sanitize ต่อท้ายตัวกลางของ customizer.php (sanitize_text_field ที่ priority 10) · validate แยกต่อ setting */
add_filter( 'customize_sanitize_ga_measurement_id', 'fenix_consent_sanitize_ga_id', 20 );
add_filter( 'customize_sanitize_fb_pixel_id', 'fenix_consent_sanitize_pixel_id', 20 );
add_filter( 'customize_sanitize_consent_version', 'fenix_consent_sanitize_version', 20 );
add_filter( 'customize_validate_ga_measurement_id', 'fenix_consent_validate_id', 10, 3 );
add_filter( 'customize_validate_fb_pixel_id', 'fenix_consent_validate_id', 10, 3 );

/* ==============================================================
 * ค่าที่ใช้งานจริง
 * ============================================================== */

/**
 * รหัสติดตามที่ผ่านการตรวจแล้ว (ค่าเก่าที่เคยบันทึกแบบไม่ตรวจก็ถูกกรองที่นี่)
 *
 * @return array { ga: string, pixel: string }
 */
function fenix_consent_ids() {
	return array(
		'ga'    => fenix_consent_sanitize_ga_id( fenix_mod( 'ga_measurement_id' ) ),
		'pixel' => fenix_consent_sanitize_pixel_id( fenix_mod( 'fb_pixel_id' ) ),
	);
}

function fenix_consent_version() {
	return max( 1, absint( fenix_mod( 'consent_version' ) ) );
}

/**
 * ลิงก์นโยบาย (หัวข้อคุกกี้) · เฉพาะเมื่อหน้า privacy-policy เผยแพร่แล้ว
 */
function fenix_consent_policy_url() {
	$url = function_exists( 'fenix_published_page_url' ) ? fenix_published_page_url( 'privacy-policy' ) : '';
	return $url ? $url . '#cookies' : '';
}

/**
 * ลิงก์ "ตั้งค่าคุกกี้" สำหรับท้ายเว็บ / หน้า /go/ / ที่อื่น ๆ · consent.js ดักคลิกแล้วเปิดการ์ดหน้าเลือกทีละหมวด
 *
 * @param array $args { class, label, echo }
 * @return string
 */
function fenix_consent_link( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'class' => '',
			'label' => '',
			'echo'  => true,
		)
	);
	$label = '' !== trim( (string) $args['label'] ) ? $args['label'] : fenix_mod( 'consent_link_label' );
	$html  = sprintf(
		'<a class="%1$s" href="#cookie-settings">%2$s</a>',
		esc_attr( trim( 'cookie-reopen ' . $args['class'] ) ),
		esc_html( $label )
	);
	if ( $args['echo'] ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
	}
	return $html;
}

/**
 * รายละเอียดคุกกี้ของหมวด: บรรทัดละ 1 รายการ · ชื่อคุกกี้ (มี _ หรือเป็น camelCase) ห่อด้วย <code class="keep-case">
 */
function fenix_consent_detail_html( $text ) {
	$items = fenix_lines( $text );
	if ( ! $items ) {
		return '';
	}
	$html = '';
	foreach ( $items as $item ) {
		$safe  = esc_html( $item );
		$coded = preg_replace(
			'/(?<![A-Za-z0-9_*])(_[A-Za-z0-9]+(?:_[A-Za-z0-9]+)*(?:_\*)?|[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)+(?:_\*)?|[a-z]+(?:[A-Z][a-z0-9]+){2,})(?![A-Za-z0-9_])/',
			'<code class="consent-code keep-case">$1</code>',
			$safe
		);
		$html .= '<li>' . ( null === $coded ? $safe : $coded ) . '</li>';
	}
	return '<ul class="consent-detail-list">' . $html . '</ul>';
}

/* ==============================================================
 * ส่งค่าให้ consent.js และตั้ง Consent Mode เริ่มต้น
 * ============================================================== */

/**
 * ส่งการตั้งค่าให้ consent.js ผ่าน window.fenixConsentConfig · ค่าไม่ขึ้นกับผู้เข้าชม หน้าเว็บจึงยังแคชได้
 */
function fenix_consent_script_data() {
	if ( function_exists( 'wp_script_is' ) && ! wp_script_is( 'fenix-consent-js', 'enqueued' ) ) {
		return;
	}
	$ids = fenix_consent_ids();
	wp_localize_script(
		'fenix-consent-js',
		'fenixConsentConfig',
		array(
			'version' => (string) fenix_consent_version(),
			'ga'      => $ids['ga'],
			'pixel'   => $ids['pixel'],
			'force'   => fenix_mod( 'show_cookie_consent' ) ? '1' : '',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fenix_consent_script_data', 20 );

/**
 * Consent Mode v2 · ค่าเริ่มต้น denied ทุกสัญญาณ พิมพ์ต้น <head> ก่อนแท็ก Google ใด ๆ
 * ใช้ฟังก์ชันภายใน (ไม่สร้าง window.gtag) สคริปต์อื่นจึงไม่ส่ง event ก่อนผู้ใช้อนุญาต
 */
function fenix_consent_mode_default() {
	if ( is_admin() ) {
		return;
	}
	$ids = fenix_consent_ids();
	if ( '' === $ids['ga'] ) {
		return;
	}
	$js = "window.dataLayer=window.dataLayer||[];(function(){function g(){window.dataLayer.push(arguments);}g('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied'});g('set','ads_data_redaction',true);})();window.fenixConsentDefault=true;";
	if ( function_exists( 'wp_print_inline_script_tag' ) ) {
		wp_print_inline_script_tag( $js );
		return;
	}
	echo '<script>' . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- static string
}
add_action( 'wp_head', 'fenix_consent_mode_default', 1 );

/* ==============================================================
 * การ์ดความยินยอม
 * ============================================================== */

/**
 * การ์ดออกมาพร้อม hidden ทุกครั้ง · เบราว์เซอร์ที่ปิด JS จะไม่เห็นการ์ด และไม่มีสคริปต์ติดตามตัวไหนทำงาน
 */
function fenix_consent_card() {
	if ( is_admin() ) {
		return;
	}

	$policy_url   = fenix_consent_policy_url();
	$policy_label = trim( (string) fenix_mod( 'consent_policy_label' ) );
	$policy_link  = ( $policy_url && '' !== $policy_label )
		? ' <a class="consent-policy" href="' . esc_url( $policy_url ) . '">' . esc_html( $policy_label ) . '</a>'
		: '';
	$detail_label = fenix_mod( 'consent_detail_label' );

	$cats = array(
		'necessary' => array(
			'name'   => fenix_mod( 'consent_necessary_name' ),
			'desc'   => fenix_mod( 'consent_necessary_desc' ),
			'detail' => fenix_mod( 'consent_necessary_detail' ),
			'icon'   => 'lock',
		),
		'analytics' => array(
			'name'   => fenix_mod( 'consent_analytics_name' ),
			'desc'   => fenix_mod( 'consent_analytics_desc' ),
			'detail' => fenix_mod( 'consent_analytics_detail' ),
			'icon'   => 'chart',
		),
		'marketing' => array(
			'name'   => fenix_mod( 'consent_marketing_name' ),
			'desc'   => fenix_mod( 'consent_marketing_desc' ),
			'detail' => fenix_mod( 'consent_marketing_detail' ),
			'icon'   => 'target',
		),
	);
	?>
<section class="consent" id="cookie-settings" aria-labelledby="consent-title" data-view="intro" hidden>
	<div class="consent-head">
		<span class="consent-badge" aria-hidden="true"><?php echo fenix_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<div class="consent-head-text">
			<?php if ( '' !== trim( (string) fenix_mod( 'consent_kicker' ) ) ) : ?>
				<p class="consent-kicker"><?php echo esc_html( fenix_mod( 'consent_kicker' ) ); ?></p>
			<?php endif; ?>
			<h2 class="consent-title" id="consent-title" tabindex="-1"><?php echo fenix_text( fenix_mod( 'consent_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></h2>
		</div>
	</div>

	<div class="consent-body">
		<p class="consent-text" data-show="intro"><?php echo esc_html( fenix_mod( 'cookie_consent_text' ) ); ?><?php echo $policy_link; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?></p>
		<p class="consent-note consent-note--none" data-when="none" hidden><?php echo esc_html( fenix_mod( 'consent_none_text' ) ); ?></p>

		<div class="consent-prefs" data-show="prefs" hidden>
			<ul class="consent-cats">
				<?php foreach ( $cats as $fenix_cat_key => $fenix_cat ) : ?>
					<?php
					$fenix_cat_id  = 'consent-' . $fenix_cat_key;
					$fenix_locked  = 'necessary' === $fenix_cat_key;
					$fenix_details = fenix_consent_detail_html( $fenix_cat['detail'] );
					?>
					<li class="consent-cat<?php echo $fenix_locked ? ' is-locked' : ''; ?>"<?php echo $fenix_locked ? '' : ' data-cat="' . esc_attr( $fenix_cat_key ) . '" hidden'; ?>>
						<div class="consent-cat-row">
							<span class="consent-cat-icon" aria-hidden="true"><?php echo fenix_icon( $fenix_cat['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<div class="consent-cat-main">
								<p class="consent-cat-name" id="<?php echo esc_attr( $fenix_cat_id ); ?>-name"><?php echo esc_html( $fenix_cat['name'] ); ?></p>
								<p class="consent-cat-desc" id="<?php echo esc_attr( $fenix_cat_id ); ?>-desc"><?php echo esc_html( $fenix_cat['desc'] ); ?></p>
							</div>
							<?php if ( $fenix_locked ) : ?>
								<span class="consent-always"><?php echo esc_html( fenix_mod( 'consent_always_label' ) ); ?></span>
							<?php else : ?>
								<label class="consent-switch">
									<input type="checkbox" role="switch" name="<?php echo esc_attr( $fenix_cat_key ); ?>" aria-labelledby="<?php echo esc_attr( $fenix_cat_id ); ?>-name" aria-describedby="<?php echo esc_attr( $fenix_cat_id ); ?>-desc">
									<span class="consent-track" aria-hidden="true"></span>
								</label>
							<?php endif; ?>
						</div>
						<?php if ( $fenix_details && '' !== trim( (string) $detail_label ) ) : ?>
							<details class="consent-more">
								<summary><span><?php echo esc_html( $detail_label ); ?></span><?php echo fenix_icon( 'arrow', 'icon consent-more-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
								<?php echo $fenix_details; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_consent_detail_html ?>
							</details>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="consent-note"><?php echo esc_html( fenix_mod( 'consent_prefs_note' ) ); ?><?php echo $policy_link; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?></p>
		</div>
	</div>

	<div class="consent-actions">
		<button type="button" class="consent-btn consent-btn--save" data-consent="save" data-show="prefs" data-when="optional" hidden><?php echo esc_html( fenix_mod( 'consent_save_label' ) ); ?></button>
		<button type="button" class="consent-btn consent-btn--choice" data-consent="reject" data-when="optional" hidden><?php echo esc_html( fenix_mod( 'consent_reject_label' ) ); ?></button>
		<button type="button" class="consent-btn consent-btn--choice" data-consent="accept" data-when="optional" hidden><?php echo esc_html( fenix_mod( 'consent_accept_label' ) ); ?></button>
		<button type="button" class="consent-btn consent-btn--ok" data-consent="ok" data-when="none" hidden><?php echo esc_html( fenix_mod( 'consent_ok_label' ) ); ?></button>
		<button type="button" class="consent-btn consent-btn--link" data-consent="prefs" data-show="intro" data-when="optional" hidden><?php echo esc_html( fenix_mod( 'consent_prefs_label' ) ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</div>

	<button type="button" class="consent-close" data-consent="close" aria-label="<?php echo esc_attr( fenix_mod( 'consent_close_label' ) ); ?>"><?php echo fenix_icon( 'x', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</section>
	<?php
}
add_action( 'wp_footer', 'fenix_consent_card', 5 );

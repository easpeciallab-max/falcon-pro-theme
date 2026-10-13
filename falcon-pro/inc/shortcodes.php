<?php
/**
 * FALCON PRO EA · Shortcodes สำหรับใช้ในเนื้อหาเพจ/บทความ
 *
 * [falcon_line pos="xxx"]ข้อความปุ่ม[/falcon_line]  ปุ่ม LINE (ลิงก์จาก Customizer)
 * [falcon_brand]                                   ชื่อแบรนด์
 * [falcon_broker] / [falcon_broker field="server"] ชื่อโบรกเกอร์ / ชื่อเซิร์ฟเวอร์ MT5 (ตั้งค่าใน Customizer)
 * [falcon_broker field="url"]                      ปุ่มสมัครบัญชี (rel="sponsored") · พาร์ตเนอร์ยังตั้งไม่ครบ (fenix_partner_ready) = ไม่แสดง
 * [falcon_broker field="disclosure" box="1"]       ข้อความเปิดเผยความเป็นพาร์ตเนอร์ทั้งบล็อก · พาร์ตเนอร์ยังตั้งไม่ครบ = ไม่แสดง
 * [falcon_email]                                   อีเมลติดต่อ (ลิงก์ mailto เข้ารหัสกันบอต) · ไม่ได้ตั้ง = ไม่แสดง
 * [falcon_if has="email|partner|openchat|line"]…[/falcon_if]  แสดงเนื้อหาข้างในเมื่อมีค่านั้นใน Customizer (not="…" = กลับด้าน)
 * [falcon_figure name="xxx" alt="" caption=""]     แผนภาพที่มากับธีม (assets/img/illus · รายชื่อใน fenix_figure_registry())
 * [falcon_calc type="lot|drawdown"]                เครื่องคำนวณ (JS ใน main.js)
 * [falcon_dashboard mode="visit|signup|signin" pos="xxx"]ข้อความปุ่ม[/falcon_dashboard]  ปุ่มไปแดชบอร์ด (ที่อยู่จาก Customizer หมวด 1.1) · ปิดฟีเจอร์ = ไม่แสดง
 *
 * กติกาเขียนเนื้อหาอยู่ที่ dev/content-spec.md · dev/check-content.php ตรวจชื่อ shortcode ค่า field/has และคู่เปิดปิดให้
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * รายชื่อ shortcode ของธีม (ใช้ร่วมกับ inc/seo.php และ dev/check-content.php)
 */
function fenix_shortcode_tags() {
	return array( 'falcon_line', 'falcon_brand', 'falcon_broker', 'falcon_email', 'falcon_if', 'falcon_figure', 'falcon_calc', 'falcon_dashboard' );
}

/**
 * shortcode ที่คืน "ทั้งบล็อก" (แผนภาพ เครื่องคิด ปุ่ม ข้อความเปิดเผย และแท็กเปิด/ปิดของ [falcon_if] แบบครอบบล็อก)
 * เมื่ออยู่บรรทัดของตัวเอง ให้คั่นด้วยบรรทัดว่างหน้าและหลังก่อนถึง wpautop (the_content 10)
 * ไม่ทำ = สองบรรทัดที่ติดกันถูกรวมเป็น <p>…<br>…</p> เดียว บล็อกของเราจึงไปซ้อนอยู่ใน <p> (HTML ผิด เกิดย่อหน้าว่าง)
 * แตะเฉพาะบรรทัดที่มี shortcode ของธีมตัวเดียวล้วน ๆ · บรรทัดอื่นและ shortcode ที่อยู่ในประโยคไม่ถูกแตะ
 */
function fenix_sc_block_lines( $content ) {
	$content = (string) $content;
	if ( false === strpos( $content, '[falcon_' ) ) {
		return $content;
	}
	$out = preg_replace(
		'/^[ \t]*('
			. '\[falcon_(?:figure|calc)\b[^\]\r\n]*\]'
			. '|\[falcon_broker\b[^\]\r\n]*\bfield="(?:url|disclosure)"[^\]\r\n]*\]'
			. '|\[falcon_line\b[^\]\r\n]*\][^\[\r\n]*\[\/falcon_line\]'
			. '|\[falcon_dashboard\b[^\]\r\n]*\][^\[\r\n]*\[\/falcon_dashboard\]'
			. '|\[falcon_if\b[^\]\r\n]*\]'
			. '|\[\/falcon_if\]'
		. ')[ \t]*\r?$/m',
		"\n\n$1\n\n",
		$content
	);
	return null === $out ? $content : $out;
}
add_filter( 'the_content', 'fenix_sc_block_lines', 8 );

function fenix_sc_line( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'pos' => 'content' ), $atts, 'falcon_line' );
	$text = trim( wp_strip_all_tags( (string) $content ) );
	if ( '' === $text ) {
		$text = 'สอบถามทีมงานทาง LINE';
	}
	// ไม่มี LINE → ใช้หน้า /go/ แทน · ไม่มีทั้งคู่ = ไม่แสดงปุ่ม (ไม่ชี้ไปที่ '#')
	$target = fenix_contact_target();
	if ( '' === $target['url'] ) {
		return '';
	}
	if ( ! $target['is_line'] ) {
		$text = fenix_mod( 'contact_fallback_text' );
	}
	return '<span class="sc-line"><a class="btn btn-fire" href="' . esc_url( $target['url'] ) . '"' . ( $target['is_line'] ? ' target="_blank" rel="noopener"' : '' ) . ' data-line-pos="' . esc_attr( sanitize_key( $atts['pos'] ) ) . '">'
		. fenix_icon( $target['is_line'] ? 'line' : 'chat' ) . '<span>' . esc_html( $text ) . '</span>' . fenix_icon( 'arrow' ) . '</a></span>';
}
add_shortcode( 'falcon_line', 'fenix_sc_line' );

/**
 * โหมดที่ [falcon_dashboard] รับ (ไม่ระบุ = visit) · visit = โหมดเยี่ยมชม · signup = สมัครสมาชิก · signin = เข้าสู่ระบบ
 */
function fenix_sc_dashboard_modes() {
	return function_exists( 'fenix_dashboard_modes' ) ? fenix_dashboard_modes() : array( 'visit', 'signup', 'signin' );
}

/**
 * [falcon_dashboard mode="visit" pos="xxx"]ข้อความปุ่ม[/falcon_dashboard] · ปุ่มไปแดชบอร์ด (เปิดแท็บใหม่) ทั้งบล็อกแบบเดียวกับ [falcon_line]
 * ที่อยู่ของแดชบอร์ดมาจาก Customizer หมวด 1.1 เท่านั้น (inc/modules/dashboard.php) เนื้อหาเพจจึงไม่ต้องพิมพ์ที่อยู่เอง
 * ไม่ใส่ข้อความปุ่ม = ใช้ข้อความของโหมดนั้นจาก Customizer · ปิดฟีเจอร์ ไม่มีที่อยู่ สะกดโหมดผิด หรือข้อความปุ่มของโหมดนั้นใน Customizer ถูกลบจนว่าง = ไม่แสดงอะไร
 * visit = ปุ่มเขียว · signup / signin = ปุ่มเข้ม · pos ใช้นับคลิก (event dashboard_click ใน assets/js/main.js)
 */
function fenix_sc_dashboard( $atts, $content = '' ) {
	if ( ! function_exists( 'fenix_dashboard_button' ) ) {
		return '';
	}
	$atts = shortcode_atts(
		array(
			'mode' => 'visit',
			'pos'  => 'content',
		),
		$atts,
		'falcon_dashboard'
	);
	$mode = strtolower( trim( (string) $atts['mode'] ) );
	if ( ! in_array( $mode, fenix_sc_dashboard_modes(), true ) ) {
		return '';
	}
	$button = fenix_dashboard_button(
		$mode,
		array(
			'text'  => trim( wp_strip_all_tags( (string) $content ) ),
			'class' => 'visit' === $mode ? 'btn btn-fire' : 'btn btn-dark',
			'pos'   => $atts['pos'],
		)
	);
	return '' === $button ? '' : '<span class="sc-line sc-dash">' . $button . '</span>';
}
add_shortcode( 'falcon_dashboard', 'fenix_sc_dashboard' );

function fenix_sc_brand() {
	return esc_html( fenix_mod( 'hero_title' ) ? fenix_mod( 'hero_title' ) : 'FALCON PRO EA' );
}
add_shortcode( 'falcon_brand', 'fenix_sc_brand' );

/**
 * ค่า field ที่ [falcon_broker] รับ (ไม่ระบุ = name)
 */
function fenix_sc_broker_fields() {
	return array( 'name', 'server', 'url', 'disclosure' );
}

/**
 * ข้อความเปิดเผยความเป็นพาร์ตเนอร์เป็น HTML ทั้งบล็อก (escape แล้ว) · '' เมื่อพาร์ตเนอร์ยังตั้งไม่ครบ (fenix_partner_ready: ลิงก์สมัครบัญชี + ชื่อโบรกเกอร์จริง)
 * ย่อหน้าเดียว = <p class="broker-disclosure"> · หลายบล็อก = <div class="broker-disclosure"> · $box = กรอบ callout--info
 * คืนทั้งบล็อกเสมอ จึงต้องวาง shortcode ไว้บรรทัดของตัวเอง (ไม่อยู่ใน <p> ร่วมกับข้อความอื่น)
 */
function fenix_broker_disclosure_html( $box = false ) {
	$text = fenix_broker_disclosure();
	$html = '' !== $text ? fenix_rich_text( $text ) : '';
	if ( '' === $html ) {
		return '';
	}
	if ( $box ) {
		return '<div class="callout callout--info broker-disclosure" role="note">' . $html . '</div>';
	}
	if ( 0 === strpos( $html, '<p>' ) && '</p>' === substr( $html, -4 ) && 1 === substr_count( $html, '</p>' ) ) {
		return '<p class="broker-disclosure" role="note">' . substr( $html, 3 );
	}
	return '<div class="broker-disclosure" role="note">' . $html . '</div>';
}

function fenix_sc_broker( $atts ) {
	$atts  = shortcode_atts(
		array(
			'field' => 'name',
			'box'   => '0',
		),
		$atts,
		'falcon_broker'
	);
	$field = strtolower( trim( (string) $atts['field'] ) );

	if ( 'url' === $field ) {
		/* ปุ่มสมัครบัญชี: ลิงก์พาร์ตเนอร์ติด rel="sponsored" เสมอ · พาร์ตเนอร์ยังตั้งไม่ครบ (ไม่มีลิงก์ หรือยังไม่มีชื่อโบรกเกอร์จริง)
		 * หรือไม่มีข้อความปุ่ม = ไม่แสดง · เงื่อนไขเดียวกับข้อความเปิดเผย ปุ่มจึงไม่ขึ้นโดยไม่มีข้อความเปิดเผยคู่กัน */
		if ( ! function_exists( 'fenix_partner_ready' ) || ! fenix_partner_ready() ) {
			return '';
		}
		$url   = trim( (string) fenix_mod( 'broker_signup_url' ) );
		$label = trim( fenix_tokens( (string) fenix_mod( 'broker_signup_text' ) ) );
		if ( '' === $url || '' === $label ) {
			return '';
		}
		/* ห่อด้วย .sc-line เหมือนปุ่ม LINE: เว้นระยะบนล่างเท่ากัน และข้อความปุ่มยาวตัดบรรทัดได้บนจอแคบ */
		return '<span class="sc-line"><a class="btn btn-dark" href="' . esc_url( $url ) . '" target="_blank" rel="noopener sponsored"><span>' . esc_html( $label ) . '</span>' . fenix_icon( 'external', 'icon icon-sm' ) . '</a></span>';
	}

	if ( 'disclosure' === $field ) {
		return fenix_broker_disclosure_html( in_array( strtolower( trim( (string) $atts['box'] ) ), array( '1', 'true', 'yes' ), true ) );
	}

	/* ชื่อ / เซิร์ฟเวอร์: แหล่งเดียวกับตัวแทน {broker} {server} (ช่องที่ว่างใช้คำกลางของธีม) */
	return esc_html( fenix_tokens( 'server' === $field ? '{server}' : '{broker}' ) );
}
add_shortcode( 'falcon_broker', 'fenix_sc_broker' );

/**
 * [falcon_email] · อีเมลติดต่อจาก Customizer (contact_email) เป็นลิงก์ mailto
 * เข้ารหัสด้วย antispambot และติดคลาส keep-case (ไม่ถูกแปลงเป็นตัวพิมพ์ใหญ่) · ไม่ได้ตั้งหรือรูปแบบผิด = ''
 */
function fenix_sc_email() {
	$email = trim( (string) fenix_mod( 'contact_email' ) );
	if ( '' === $email || ! is_email( $email ) ) {
		return '';
	}
	$safe = antispambot( $email );
	return '<a class="keep-case" href="mailto:' . esc_attr( $safe ) . '">' . esc_html( $safe ) . '</a>';
}
add_shortcode( 'falcon_email', 'fenix_sc_email' );

/**
 * เงื่อนไขที่ [falcon_if] รู้จัก
 */
function fenix_sc_if_keys() {
	return array( 'email', 'partner', 'openchat', 'line' );
}

/**
 * มีค่านั้นใน Customizer หรือไม่ · คืน null เมื่อไม่รู้จักชื่อเงื่อนไข
 *   email    = อีเมลติดต่อถูกรูปแบบ (contact_email)
 *   partner  = พาร์ตเนอร์ตั้งครบ: มีลิงก์สมัครบัญชี (broker_signup_url) และมีชื่อโบรกเกอร์จริง (broker_name)
 *              ตัวตรวจเดียวกับข้อความเปิดเผยพาร์ตเนอร์และปุ่มสมัคร (fenix_partner_ready ใน functions.php)
 *   openchat = มีลิงก์ LINE OpenChat (line_openchat_url)
 *   line     = มีลิงก์ LINE OA (line_url)
 *
 * @return bool|null
 */
function fenix_sc_has( $what ) {
	switch ( strtolower( trim( (string) $what ) ) ) {
		case 'email':
			$email = trim( (string) fenix_mod( 'contact_email' ) );
			return '' !== $email && (bool) is_email( $email );
		case 'partner':
			return function_exists( 'fenix_partner_ready' ) && fenix_partner_ready();
		case 'openchat':
			return '' !== trim( (string) fenix_mod( 'line_openchat_url' ) );
		case 'line':
			return function_exists( 'fenix_has_line_url' ) && fenix_has_line_url();
	}
	return null;
}

/**
 * ตรวจ attribute ของ [falcon_if]: has = ต้องมีทุกค่า · not = ต้องไม่มีสักค่า (คั่นหลายค่าด้วยคอมมา)
 * ไม่ระบุเงื่อนไข หรือสะกดชื่อเงื่อนไขผิด = ไม่ผ่าน (ซ่อนเนื้อหาไว้ก่อน ดีกว่าแสดงผิดที่)
 */
function fenix_sc_if_test( $atts ) {
	$atts = is_array( $atts ) ? $atts : array();
	$list = array();
	foreach ( array( 'has', 'not' ) as $side ) {
		$list[ $side ] = isset( $atts[ $side ] ) ? preg_split( '/[\s,]+/', (string) $atts[ $side ], -1, PREG_SPLIT_NO_EMPTY ) : array();
		$list[ $side ] = is_array( $list[ $side ] ) ? $list[ $side ] : array();
	}
	if ( ! $list['has'] && ! $list['not'] ) {
		return false;
	}
	foreach ( $list['has'] as $key ) {
		if ( true !== fenix_sc_has( $key ) ) {
			return false;
		}
	}
	foreach ( $list['not'] as $key ) {
		if ( false !== fenix_sc_has( $key ) ) {
			return false;
		}
	}
	return true;
}

/**
 * [falcon_if has="partner"]…[/falcon_if] · แสดงเนื้อหาข้างในเฉพาะเมื่อเงื่อนไขผ่าน (ไม่ผ่าน = '')
 * ใช้ได้ 2 แบบ: ในประโยค (มีข้อความอื่นอยู่ในย่อหน้าเดียวกัน) หรือครอบทั้งบล็อกโดยวางแท็กเปิดและปิดไว้นอก <p>/<li>/<h2>
 * ห้ามซ้อน [falcon_if] ใน [falcon_if] (WordPress ไม่รองรับ shortcode ชื่อเดียวกันซ้อนกัน)
 */
function fenix_sc_if( $atts, $content = '' ) {
	if ( ! fenix_sc_if_test( $atts ) ) {
		return '';
	}
	$content = (string) $content;
	/* แบบครอบบล็อก: wpautop ห่อแท็กเปิดและปิดเป็นย่อหน้าของตัวเอง แล้ว shortcode_unautop ถอด <p> นอกสุดออก
	 * จึงเหลือ </p> ค้างหน้าเนื้อหาและ <p> ค้างท้าย · shortcode ที่อยู่เดี่ยว ๆ ข้างในยังถูกห่อ <p> อยู่ */
	$content = (string) preg_replace( '#^\s*</p>#', '', $content );
	$content = (string) preg_replace( '#<p>\s*$#', '', $content );
	if ( function_exists( 'shortcode_unautop' ) ) {
		$content = shortcode_unautop( $content );
	}
	return do_shortcode( $content );
}
add_shortcode( 'falcon_if', 'fenix_sc_if' );

/**
 * แผนภาพและภาพประกอบในเนื้อหาที่มากับธีม (ไฟล์อยู่ใน assets/img/illus/)
 * เพิ่มรายการด้วยฟิลเตอร์ fenix_figure_registry · ชื่อ (a-z 0-9 -) => array(
 *   'file'    => 'falcon-pro-xxx-v1.webp',  ชื่อไฟล์ใน assets/img/illus/ (ชื่อกลาง ไม่มีชื่อแบรนด์อื่นหรือชื่อโบรกเกอร์)
 *   'alt'     => '…',                        ข้อความแทนภาพ (จำเป็น · เขียนใหม่ของ FALCON · ใช้ {brand} {broker} ได้)
 *   'caption' => '…',                        คำบรรยายใต้ภาพ ('' = ไม่มี)
 *   'mobile'  => '…',                        ไม่บังคับ: ไฟล์สำหรับจอแคบ · ไม่ระบุ = ใช้ไฟล์ชื่อเดียวกันที่เติม -mobile ถ้ามี
 * )
 * รายการที่ไม่มีไฟล์จริงจะไม่แสดงอะไร · เปลี่ยนภาพเมื่อไหร่ให้เปลี่ยนเลข v ในชื่อไฟล์ (แคชเก็บตาม URL)
 * ห้ามลงทะเบียนภาพที่มีตัวเลขผลเทรด กำไร หรือ Win rate
 */
function fenix_figure_registry() {
	return (array) apply_filters(
		'fenix_figure_registry',
		array(
			/* แผนภาพที่เรนเดอร์ด้วย dev/make-art.php (ข้อความในภาพอยู่ที่ dev/preview/art.php) · ไฟล์จอแคบชื่อ …-mobile-v2.webp ถูกหยิบใช้เอง
			 * แก้ภาพเมื่อไหร่ให้เพิ่มเลข v ทั้งใน art.php และชื่อไฟล์ตรงนี้ */
			'account-flow' => array(
				'file'    => 'falcon-pro-ea-account-flow-v2.webp',
				'alt'     => 'ลำดับสี่ขั้นจากสมัครสมาชิกกับโบรกเกอร์ ยืนยันตัวตน (KYC) เปิดบัญชีเทรด MT5 จนฝากเงินเข้าบัญชีเทรด',
				'caption' => 'ภาพประกอบลำดับงานฝั่งโบรกเกอร์ ชื่อเมนูและหน้าจอจริงอาจต่างกันไปตามโบรกเกอร์',
			),
			'vps-flow'     => array(
				'file'    => 'falcon-pro-ea-vps-flow-v2.webp',
				'alt'     => 'อุปกรณ์ของคุณเชื่อมต่อผ่าน Remote Desktop ไปยัง Windows VPS ซึ่งเปิด MT5 และ EA ค้างไว้ให้ทำงานต่อเนื่อง',
				'caption' => 'ภาพประกอบการทำงานของ VPS: MT5 และ EA ทำงานบน VPS ส่วนอุปกรณ์ของคุณใช้เปิดเข้าไปดูและสั่งงาน',
			),
			'how-it-works' => array(
				'file'    => 'falcon-pro-ea-how-it-works-v2.webp',
				'alt'     => 'วงจรการทำงานของ EA สี่ขั้น: คุณเลือกชุด EA และตั้งขีดจำกัด EA เฝ้าตลาดทองคำ ส่งคำสั่งในบัญชีของคุณเอง แล้วคุณตรวจผลและตัดสินใจว่าจะรันต่อหรือสั่งหยุด',
				'caption' => 'ภาพประกอบขั้นตอนการทำงานของ EA ไม่ใช่ผลการเทรดจริง',
			),
		)
	);
}

/**
 * ข้อมูลพร้อมใช้ของแผนภาพ 1 รายการ: URL + ขนาดจริงของไฟล์ (และไฟล์จอแคบถ้ามี) · null = ไม่มีรายการหรือไม่มีไฟล์
 *
 * @return array|null { src, width, height, alt, caption, mobile: array|null { src, width, height } }
 */
function fenix_figure( $name ) {
	static $cache = array();
	$name = sanitize_key( (string) $name );
	if ( '' === $name ) {
		return null;
	}
	if ( array_key_exists( $name, $cache ) ) {
		return $cache[ $name ];
	}
	$cache[ $name ] = null;

	$all = fenix_figure_registry();
	if ( empty( $all[ $name ] ) || ! is_array( $all[ $name ] ) || empty( $all[ $name ]['file'] ) ) {
		return null;
	}
	$entry = $all[ $name ];
	$dir   = get_template_directory() . '/assets/img/illus/';
	$base  = get_template_directory_uri() . '/assets/img/illus/';
	$probe = function ( $file ) use ( $dir, $base ) {
		$file = basename( (string) $file ); // ชื่อไฟล์เท่านั้น ไม่รับ path
		if ( '' === $file || ! is_file( $dir . $file ) ) {
			return null;
		}
		$size = @getimagesize( $dir . $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( ! is_array( $size ) || empty( $size[0] ) || empty( $size[1] ) ) {
			return null;
		}
		return array(
			'src'    => $base . $file,
			'width'  => (int) $size[0],
			'height' => (int) $size[1],
		);
	};

	$main = $probe( $entry['file'] );
	if ( null === $main ) {
		return null;
	}

	$candidates = array();
	if ( ! empty( $entry['mobile'] ) ) {
		$candidates[] = (string) $entry['mobile'];
	} else {
		$file = basename( (string) $entry['file'] );
		$dot  = strrpos( $file, '.' );
		$stem = false === $dot ? $file : substr( $file, 0, $dot );
		$ext  = false === $dot ? '' : substr( $file, $dot );
		$candidates[] = $stem . '-mobile' . $ext; // xxx-v1-mobile.webp
		if ( preg_match( '/^(.+)(-v\d+)$/', $stem, $m ) ) {
			$candidates[] = $m[1] . '-mobile' . $m[2] . $ext; // xxx-mobile-v1.webp
		}
	}
	$mobile = null;
	foreach ( $candidates as $candidate ) {
		$mobile = $probe( $candidate );
		if ( null !== $mobile ) {
			break;
		}
	}

	$cache[ $name ] = $main + array(
		'alt'     => isset( $entry['alt'] ) ? (string) $entry['alt'] : '',
		'caption' => isset( $entry['caption'] ) ? (string) $entry['caption'] : '',
		'mobile'  => $mobile,
	);
	return $cache[ $name ];
}

/**
 * [falcon_figure name="xxx" alt="" caption=""] · alt / caption ใน shortcode ทับค่าจากรายการ
 * พิมพ์ width/height จริงของไฟล์ (จองพื้นที่ก่อนโหลด) + --media-w ให้ CSS ใช้เป็น max-width เหมือนช่องรูปของคู่มือ
 */
function fenix_sc_figure( $atts ) {
	$atts = shortcode_atts(
		array(
			'name'    => '',
			'alt'     => '',
			'caption' => '',
		),
		$atts,
		'falcon_figure'
	);
	$fig  = fenix_figure( $atts['name'] );
	if ( null === $fig ) {
		return '';
	}
	$alt     = trim( fenix_tokens( '' !== trim( (string) $atts['alt'] ) ? (string) $atts['alt'] : $fig['alt'] ) );
	$caption = trim( fenix_tokens( '' !== trim( (string) $atts['caption'] ) ? (string) $atts['caption'] : $fig['caption'] ) );

	$img = sprintf(
		'<img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" loading="lazy" decoding="async" style="--media-w:%3$dpx">',
		esc_url( $fig['src'] ),
		esc_attr( $alt ),
		$fig['width'],
		$fig['height']
	);
	if ( is_array( $fig['mobile'] ) ) {
		$img = sprintf(
			'<picture><source media="(max-width: 640px)" srcset="%1$s" width="%2$d" height="%3$d">%4$s</picture>',
			esc_url( $fig['mobile']['src'] ),
			$fig['mobile']['width'],
			$fig['mobile']['height'],
			$img
		);
	}
	return '<figure class="fenix-figure">' . $img . ( '' !== $caption ? '<figcaption>' . esc_html( $caption ) . '</figcaption>' : '' ) . '</figure>';
}
add_shortcode( 'falcon_figure', 'fenix_sc_figure' );

function fenix_sc_calc( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'lot' ), $atts, 'falcon_calc' );
	static $n = 0;
	$n++;
	$id = 'calc-' . $n;

	ob_start();
	if ( 'drawdown' === $atts['type'] ) :
		?>
		<div class="calc" data-calc="drawdown" id="<?php echo esc_attr( $id ); ?>">
			<div class="calc-head">
				<?php echo fenix_icon_badge( 'chart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<strong>คำนวณกำไรที่ต้องทำคืนหลัง Drawdown</strong>
					<small>ขาดทุนไปกี่ % ต้องกำไรกลับกี่ % จึงจะคืนทุน</small>
				</div>
			</div>
			<div class="calc-grid">
				<label class="calc-field">
					<span>Drawdown (%)</span>
					<input type="number" inputmode="decimal" min="0" max="99" step="0.1" value="20" data-in="dd">
				</label>
				<label class="calc-field">
					<span>ทุนก่อนติดลบ (USD) · ไม่บังคับ</span>
					<input type="number" inputmode="decimal" min="0" step="1" value="1000" data-in="balance">
				</label>
			</div>
			<div class="calc-out" aria-live="polite">
				<div><span>ต้องทำกำไรคืน</span><strong data-out="gain">–</strong></div>
				<div><span>ทุนที่เหลือ</span><strong data-out="left">–</strong></div>
			</div>
			<div class="calc-bar" aria-hidden="true"><i data-out="bar"></i></div>
			<p class="calc-note">สูตร: กำไรที่ต้องทำคืน = DD ÷ (1 − DD) · เป็นการคำนวณทางคณิตศาสตร์ ไม่ใช่ผลการเทรด</p>
		</div>
		<?php
	else :
		?>
		<div class="calc" data-calc="lot" id="<?php echo esc_attr( $id ); ?>">
			<div class="calc-head">
				<?php echo fenix_icon_badge( 'calc' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<strong>เครื่องคิด Lot Size ตามความเสี่ยง</strong>
					<small>คำนวณขนาดออเดอร์จากทุน ความเสี่ยงต่อไม้ และระยะ Stop Loss</small>
				</div>
			</div>
			<div class="calc-grid">
				<label class="calc-field">
					<span>ทุนในบัญชี (USD)</span>
					<input type="number" inputmode="decimal" min="0" step="1" value="1000" data-in="balance">
				</label>
				<label class="calc-field">
					<span>ความเสี่ยงต่อไม้ (%)</span>
					<input type="number" inputmode="decimal" min="0" max="100" step="0.1" value="1" data-in="risk">
				</label>
				<label class="calc-field">
					<span>ระยะ Stop Loss (pips)</span>
					<input type="number" inputmode="decimal" min="0" step="0.1" value="50" data-in="sl">
				</label>
				<label class="calc-field">
					<span>สินทรัพย์ (ค่าอ้างอิง)</span>
					<select data-in="preset">
						<option value="10">EURUSD / GBPUSD / AUDUSD · บัญชี USD</option>
						<option value="10">XAUUSD · สัญญา 100 oz (1 pip = 0.10)</option>
						<option value="custom">กำหนดมูลค่า pip เอง</option>
					</select>
				</label>
				<label class="calc-field">
					<span>มูลค่า 1 pip ต่อ 1 lot (USD)</span>
					<input type="number" inputmode="decimal" min="0" step="0.01" value="10" data-in="pipval">
				</label>
				<label class="calc-field">
					<span>Lot ขั้นต่ำ / ขั้นการปรับ</span>
					<input type="number" inputmode="decimal" min="0.001" step="0.01" value="0.01" data-in="step">
				</label>
			</div>
			<div class="calc-out" aria-live="polite">
				<div><span>ความเสี่ยงเป็นเงิน</span><strong data-out="money">–</strong></div>
				<div><span>Lot ที่เหมาะสม</span><strong data-out="lot">–</strong></div>
			</div>
			<p class="calc-note">ผลเป็นการคำนวณเบื้องต้น มูลค่า pip ต่างกันตามสินทรัพย์ โบรกเกอร์ และสกุลเงินบัญชี · ตรวจใน MT5: คลิกขวาที่สินทรัพย์ → Specification</p>
		</div>
		<?php
	endif;
	return ob_get_clean();
}
add_shortcode( 'falcon_calc', 'fenix_sc_calc' );

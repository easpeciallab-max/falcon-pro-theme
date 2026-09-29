<?php
/**
 * FALCON PRO EA · โมดูล Site Chrome (header · footer · แถบติดต่อท้ายหน้าย่อย · บาร์ล่างมือถือ)
 *
 * หน้าตาแบบ FALCON (มุมโค้ง ปุ่ม pill เส้นบาง เขียว FALCON บนพื้นเข้ม #172125)
 * - ค่าเริ่มต้น + ส่วน Customizer ของ header/footer/dock อยู่ในไฟล์นี้ (ผ่านฟิลเตอร์ fenix_defaults / fenix_customizer_sections)
 * - fenix_line_cta() ทุกหน้าย่อยถูกแทนด้วยแถบเข้มเต็มความกว้าง .cta-console (add_action 'fenix_line_cta')
 * - ปุ่มติดต่อทุกปุ่มใช้ fenix_contact_target() / fenix_contact_button() · ไม่มีปุ่มใดชี้ไปที่ '#'
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น (ข้อความทั้งหมดเขียนใหม่ในน้ำเสียงของ FALCON)
 * ============================================================== */
add_filter(
	'fenix_defaults',
	function ( $d ) {
		/* เลิกใช้ในโครง footer ใหม่ (footer-top เดิม + กล่อง CTA ซ้ำ) */
		foreach ( array( 'footer_kicker', 'footer_cta_title', 'footer_cta_text', 'footer_tagline', 'footer_trust_items' ) as $fenix_retired ) {
			unset( $d[ $fenix_retired ] );
		}

		return array_merge(
			$d,
			array(
				/* ช่องทาง (ต่อจาก facebook_url ใน "1) ช่องทางติดต่อ") */
				'instagram_url'            => '',
				'tiktok_url'               => '',
				'youtube_url'              => '',
				'float_line_text'          => 'แชทกับทีม FALCON ทาง LINE',

				/* แถบติดต่อท้ายหน้าย่อย (.cta-console) */
				'chrome_cta_label'         => 'ยังไม่แน่ใจ ถามก่อนได้',
				'contact_title'            => 'อยากรู้ก่อนว่า FALCON PRO EA ไปกันได้กับทุนและบัญชีที่มีอยู่ไหม?',
				'contact_text'             => 'ส่งรายละเอียดทุน โบรกเกอร์ และวิธีเทรดที่ใช้อยู่มาให้ทีมงาน เราจะช่วยประเมินความพร้อมของบัญชี และอธิบายลำดับการติดตั้งให้เข้าใจก่อน โดยไม่เร่งให้ตัดสินใจซื้อ',

				/* Footer · แถวแรก (launch console) */
				'footer_console_label'     => 'ติดต่อทีม FALCON PRO',
				'footer_headline'          => 'เริ่มจากบทสนทนาสั้น ๆ กับทีม FALCON',
				'footer_sub'               => 'เลือกแพ็กเกจ ลง EA บน MT5 หรือปรับค่าความเสี่ยงให้พอดีกับเงินทุน ถามทีมงานได้ทุกเรื่องก่อนลงมือจริง',
				'footer_line_text'         => 'เปิดแชท LINE กับทีมงาน',
				'footer_qr_toggle_text'    => 'เปิด QR Code สำหรับสแกน',
				'footer_line_qr_alt'       => 'QR Code บัญชี LINE ทางการของ FALCON PRO EA',
				'footer_prep_title'        => 'ข้อความแรกที่ช่วยให้ทีมตอบได้ตรง',
				'footer_prep_text'         => 'พิมพ์สั้น ๆ ตามหัวข้อด้านล่าง ทีมงานจะได้ไม่ต้องถามย้อนหลายรอบ',
				'footer_prep_items'        => "งบที่ตั้งใจใช้กับ EA (ประมาณคร่าว ๆ ก็ได้)\nใช้โบรกเกอร์ไหน และเปิดบัญชี MT5 แบบใดไว้\nเรื่องที่อยากให้ช่วย: ติดตั้ง ดูแพ็กเกจ หรือเช็กความพร้อม\nอยากให้ทีมทักกลับช่วงไหนของวัน",
				'footer_hours_title'       => 'ช่วงเวลาที่ทีมตอบแชท',
				'footer_hours_text'        => '',

				/* Footer · ดัชนี */
				'footer_index_title'       => 'หน้าในเว็บไซต์',
				'footer_channels_title'    => 'ติดตามและติดต่อ',
				'footer_docs_title'        => 'เอกสารและนโยบาย',
				'footer_docs_items'        => "about|เกี่ยวกับเรา\nprivacy-policy|นโยบายความเป็นส่วนตัว\nterms-of-use|เงื่อนไขการใช้บริการ\ndata-deletion|คำขอลบข้อมูลส่วนบุคคล",
				'footer_risk_link'         => 'ประกาศความเสี่ยง',
				'footer_spec_title'        => 'ข้อมูลระบบโดยย่อ',
				'footer_spec_items'        => '',
				'footer_facebook_text'     => 'เพจ Facebook',
				'footer_instagram_text'    => 'Instagram',
				'footer_tiktok_text'       => 'TikTok',
				'footer_youtube_text'      => 'YouTube',
				'footer_email_text'        => 'อีเมลถึงทีมงาน',

				/* Footer · ลายน้ำ + แถบสถานะ */
				'show_footer_watermark'    => true,
				'footer_watermark_text'    => 'FALCON PRO',
				'footer_copyright_text'    => 'สงวนลิขสิทธิ์',
				'footer_status_text'       => 'ทำงานบน MetaTrader 5 · ตั้งความเสี่ยงให้พอดีกับทุนทุกครั้ง',
				'show_footer_clock'        => true,
				'footer_clock_label'       => 'เวลาประเทศไทย',
				'footer_backtop_text'      => 'ขึ้นด้านบน',

				/* บาร์ล่างมือถือ (ช่องกลาง = ปุ่มติดต่อ) */
				'mobile_nav_test_label'    => 'การทดสอบ',
				'mobile_nav_test_url'      => '/backtest/',
				'mobile_nav_contact_label' => 'ติดต่อ',

				/* เมนูหลัก (ใช้เมื่อยังไม่ได้สร้างเมนูใน WordPress) + ข้อความระบบ */
				'nav_home_label'           => 'หน้าแรก',
				'nav_test_label'           => 'การทดสอบ',
				'nav_guide_label'          => 'คู่มือการใช้งาน',
				'nav_pricing_label'        => 'แพ็กเกจ',
				'nav_articles_label'       => 'บทความ',
				'nav_contact_label'        => 'ติดต่อ',
				'chrome_skip_text'         => 'ข้ามไปที่เนื้อหาหลัก',
				'chrome_loading_text'      => 'กำลังโหลดบทความ…',
			)
		);
	}
);

/* ==============================================================
 * Customizer
 * ============================================================== */

/**
 * แทรกรายการต่อจาก key ที่กำหนด (ไม่มี key นั้น = ต่อท้าย)
 */
function fenix_chrome_insert_after( $array, $after, $insert ) {
	if ( ! is_array( $array ) ) {
		$array = array();
	}
	if ( ! isset( $array[ $after ] ) ) {
		return array_merge( $array, $insert );
	}
	$out = array();
	foreach ( $array as $key => $value ) {
		$out[ $key ] = $value;
		if ( $key === $after ) {
			foreach ( $insert as $new_key => $new_value ) {
				$out[ $new_key ] = $new_value;
			}
		}
	}
	return $out;
}

add_filter(
	'fenix_customizer_sections',
	function ( $sections, $d ) {
		/* 1) ช่องทางติดต่อ · โซเชียลเพิ่ม + ป้ายแถบติดต่อท้ายหน้าย่อย */
		if ( isset( $sections['fenix_general']['fields'] ) ) {
			$sections['fenix_general']['fields'] = fenix_chrome_insert_after(
				$sections['fenix_general']['fields'],
				'line_url',
				array(
					'contact_fallback_text' => array( 'ข้อความปุ่มติดต่อเมื่อยังไม่ใส่ลิงก์ LINE', 'text', 'ปุ่มจะพาไปหน้า /go/ (เมื่อเผยแพร่แล้ว) · ไม่มีทั้ง LINE และหน้า /go/ = ซ่อนปุ่มติดต่อทั้งเว็บ' ),
				)
			);
			$sections['fenix_general']['fields'] = fenix_chrome_insert_after(
				$sections['fenix_general']['fields'],
				'facebook_url',
				array(
					'instagram_url' => array( 'ลิงก์ Instagram (ถ้ามี)', 'url' ),
					'tiktok_url'    => array( 'ลิงก์ TikTok (ถ้ามี)', 'url' ),
					'youtube_url'   => array( 'ลิงก์ YouTube (ถ้ามี)', 'url' ),
				)
			);
			$sections['fenix_general']['fields'] = fenix_chrome_insert_after(
				$sections['fenix_general']['fields'],
				'contact_title',
				array(
					'chrome_cta_label' => array( 'แถบติดต่อท้ายหน้าย่อย · ป้ายเล็กเหนือหัวข้อ', 'text' ),
				)
			);
		}

		$sections['fenix_footer'] = array(
			'title'       => '15) Footer ท้ายเว็บ',
			'description' => 'Footer พื้นเข้ม 5 แถว: ช่องทางติดต่อ → ดัชนีลิงก์ → คำเตือนความเสี่ยงฉบับเต็ม → ลายน้ำแบรนด์ → แถบสถานะ · ช่องที่เว้นว่างจะถูกซ่อน · ปุ่มติดต่อใช้ลิงก์ LINE จาก "1) ช่องทางติดต่อ" (ไม่มี LINE = ไปหน้า /go/ ถ้าเผยแพร่แล้ว ไม่มีทั้งคู่ = ซ่อนปุ่ม) · รูป QR ใช้ "รูป QR Code LINE OA" ในหมวด 1',
			'fields'      => array(
				'footer_console_label'  => array( 'แถวติดต่อ · ป้ายเล็กด้านบน', 'text' ),
				'footer_headline'       => array( 'แถวติดต่อ · หัวข้อ (ว่าง = ใช้หัวข้อแถบติดต่อท้ายหน้าย่อย)', 'text' ),
				'footer_sub'            => array( 'แถวติดต่อ · คำอธิบาย (ว่าง = ใช้คำอธิบายแถบติดต่อท้ายหน้าย่อย)', 'textarea' ),
				'footer_line_text'      => array( 'ข้อความปุ่ม LINE (ใช้กับปุ่มติดต่อทั่วทั้งเว็บ)', 'text' ),
				'footer_qr_toggle_text' => array( 'ข้อความปุ่มเปิด QR บนมือถือ', 'text' ),
				'footer_line_qr_alt'    => array( 'คำอธิบายรูป QR (alt)', 'text' ),
				'footer_prep_title'     => array( 'กล่องเตรียมข้อความแรก · หัวข้อ', 'text' ),
				'footer_prep_text'      => array( 'กล่องเตรียมข้อความแรก · คำอธิบาย', 'textarea' ),
				'footer_prep_items'     => array( 'กล่องเตรียมข้อความแรก · รายการ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'footer_hours_title'    => array( 'เวลาตอบแชท · หัวข้อ', 'text' ),
				'footer_hours_text'     => array( 'เวลาตอบแชท · รายละเอียด (ว่าง = ไม่แสดง)', 'textarea', 'กรอกตามเวลาที่ทีมตอบได้จริงเท่านั้น บรรทัดละ 1 ช่วง' ),
				'footer_index_title'    => array( 'หัวคอลัมน์ · หน้าในเว็บไซต์', 'text', 'รายการหน้ามาจากเมนูหลัก (เฉพาะระดับบน) · ยังไม่มีเมนู = ใช้หน้ามาตรฐานที่เผยแพร่แล้ว' ),
				'footer_channels_title' => array( 'หัวคอลัมน์ · ช่องทาง', 'text' ),
				'footer_facebook_text'  => array( 'ชื่อลิงก์ Facebook', 'text' ),
				'footer_instagram_text' => array( 'ชื่อลิงก์ Instagram', 'text' ),
				'footer_tiktok_text'    => array( 'ชื่อลิงก์ TikTok', 'text' ),
				'footer_youtube_text'   => array( 'ชื่อลิงก์ YouTube', 'text' ),
				'footer_email_text'     => array( 'ชื่อลิงก์อีเมล', 'text' ),
				'footer_docs_title'     => array( 'หัวคอลัมน์ · เอกสาร', 'text' ),
				'footer_docs_items'     => array( 'รายการเอกสาร (slug|ชื่อลิงก์ บรรทัดละ 1 หน้า)', 'textarea', 'แสดงเฉพาะหน้าที่เผยแพร่แล้ว · ลิงก์ประกาศความเสี่ยงแสดงเมื่อหน้า risk-disclosure เผยแพร่แล้ว (ข้อความเตือนฉบับเต็มแสดงใต้ดัชนีเสมอ) · ชื่อลิงก์ "ตั้งค่าคุกกี้" ท้ายคอลัมน์แก้ได้ที่หมวดคุกกี้' ),
				'footer_risk_link'      => array( 'ชื่อลิงก์ประกาศความเสี่ยง', 'text' ),
				'footer_spec_title'     => array( 'หัวคอลัมน์ · ข้อมูลระบบ', 'text' ),
				'footer_spec_items'     => array( 'ข้อมูลระบบ (ป้าย|ค่า บรรทัดละ 1 แถว)', 'textarea', 'ว่าง = ใช้ไฮไลต์ 1–4 ของหน้าแรก · บรรทัดที่เป็นตัวเลขตามด้วย % หรือมีคำว่า "กำไร" จะไม่แสดง (ห้ามใส่ผลเทรด)' ),
				'show_footer_watermark' => array( 'แสดงชื่อแบรนด์ตัวใหญ่แบบเส้นขอบ', 'checkbox' ),
				'footer_watermark_text' => array( 'ชื่อแบรนด์ตัวใหญ่', 'text', 'คำสุดท้ายจะเป็นสีเขียว' ),
				'footer_copyright_text' => array( 'แถบล่าง · ข้อความลิขสิทธิ์', 'text' ),
				'footer_status_text'    => array( 'แถบล่าง · ข้อความสถานะ (ว่าง = ไม่แสดง)', 'text' ),
				'show_footer_clock'     => array( 'แถบล่าง · แสดงนาฬิกาเวลาไทย', 'checkbox' ),
				'footer_clock_label'    => array( 'แถบล่าง · ป้ายนาฬิกา', 'text' ),
				'footer_backtop_text'   => array( 'แถบล่าง · ข้อความลิงก์กลับขึ้นด้านบน', 'text' ),
			),
		);

		$sections['fenix_mobile_nav'] = array(
			'title'       => '16) เมนูลัดมือถือด้านล่าง',
			'description' => 'บาร์ 5 ช่องบนจอมือถือ · ช่องกลางคือปุ่มติดต่อ: มีลิงก์ LINE = ทัก LINE · ไม่มี LINE = ไปหน้า /go/ (เมื่อเผยแพร่แล้ว) · ไม่มีทั้งคู่ = เหลือ 4 ช่อง · ช่องของหน้าปัจจุบันติดไฟให้อัตโนมัติ',
			'fields'      => array(
				'show_mobile_nav'          => array( 'แสดงเมนูลัดด้านล่างบนมือถือ', 'checkbox' ),
				'mobile_nav_home_label'    => array( 'ช่อง 1 · ชื่อ', 'text' ),
				'mobile_nav_home_url'      => array( 'ช่อง 1 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_test_label'    => array( 'ช่อง 2 · ชื่อ', 'text' ),
				'mobile_nav_test_url'      => array( 'ช่อง 2 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_line_label'    => array( 'ช่อง 3 (กลาง) · ชื่อปุ่มเมื่อมีลิงก์ LINE', 'text' ),
				'mobile_nav_contact_label' => array( 'ช่อง 3 (กลาง) · ชื่อปุ่มเมื่อยังไม่มี LINE (ไปหน้า /go/)', 'text' ),
				'mobile_nav_price_label'   => array( 'ช่อง 4 · ชื่อ', 'text' ),
				'mobile_nav_price_url'     => array( 'ช่อง 4 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_install_label' => array( 'ช่อง 5 · ชื่อ', 'text' ),
				'mobile_nav_install_url'   => array( 'ช่อง 5 · ลิงก์หรือ slug', 'text' ),
			),
		);

		$sections = fenix_chrome_insert_after(
			$sections,
			'fenix_mobile_nav',
			array(
				'fenix_chrome_nav' => array(
					'title'       => '16.1) เมนูหลัก (Header) · ข้อความระบบ',
					'description' => 'ชื่อเมนูที่ใช้เมื่อยังไม่ได้ตั้งเมนูใน รูปแบบ → เมนู (แสดงเฉพาะหน้าที่เผยแพร่แล้ว) · "ติดต่อ" จะต่อท้ายเมนูหลักให้อัตโนมัติเมื่อหน้า /go/ เผยแพร่แล้วและเมนูยังไม่มีลิงก์ติดต่อ',
					'fields'      => array(
						'nav_home_label'      => array( 'เมนู · หน้าแรก', 'text' ),
						'nav_test_label'      => array( 'เมนู · กลุ่มการทดสอบ', 'text' ),
						'nav_guide_label'     => array( 'เมนู · กลุ่มคู่มือ', 'text' ),
						'nav_pricing_label'   => array( 'เมนู · แพ็กเกจ', 'text' ),
						'nav_articles_label'  => array( 'เมนู · บทความ', 'text' ),
						'nav_contact_label'   => array( 'เมนู · ติดต่อ (ว่าง = ไม่ต่อท้ายอัตโนมัติ)', 'text' ),
						'chrome_skip_text'    => array( 'ลิงก์ข้ามไปเนื้อหา (เห็นเมื่อกด Tab)', 'text' ),
						'chrome_loading_text' => array( 'ข้อความระหว่างโหลดบทความเพิ่ม', 'text' ),
					),
				),
			)
		);

		return $sections;
	},
	10,
	2
);

/* ==============================================================
 * ตัวช่วย
 * ============================================================== */

/**
 * ลิงก์ใช้งานได้จริง (ไม่ว่าง ไม่ใช่ '#')
 */
function fenix_chrome_url_ok( $url ) {
	$url = trim( (string) $url );
	return '' !== $url && '#' !== $url;
}

/**
 * คำที่ห้ามตัดกลางบรรทัดในหัวข้อ (fenix_text) · คำทับศัพท์ที่ตัวตัดคำของเบราว์เซอร์มักแยกผิด
 */
add_filter(
	'fenix_keep_words',
	function ( $words ) {
		return array_merge( (array) $words, array( 'เด|โม' ) );
	}
);

/**
 * body class: มีบาร์ล่างมือถือ (ให้การ์ดคุกกี้/ปุ่มลอยยกตัวพ้นบาร์ได้ด้วย var(--dock-h))
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( fenix_mod( 'show_mobile_nav' ) ) {
			$classes[] = 'has-dock';
		}
		return $classes;
	}
);

/**
 * ข้อความของ JS (main.js) · ต่อจาก fenix_assets() ที่ลงทะเบียน fenix-main แล้ว
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_localize_script(
			'fenix-main',
			'fenixChrome',
			array(
				'loadingText' => (string) fenix_mod( 'chrome_loading_text' ),
			)
		);
	},
	20
);

/**
 * ตัวสลับภาษา · ไม่ใช้ GTranslate (แปลฝั่งเบราว์เซอร์อย่างเดียว ไม่มี URL ให้ Google เก็บ และ widget ใส่ inline style ทับธีม)
 * ใช้ fenix_language_switcher() เดิม โดยซ่อน shortcode [gtranslate] ระหว่างเรียก → ได้ TranslatePress / Polylang / WPML หรือรายการสำรอง
 */
function fenix_chrome_language_switcher() {
	if ( ! fenix_mod( 'show_language_switcher' ) || ! function_exists( 'fenix_language_switcher' ) ) {
		return;
	}
	global $shortcode_tags;
	$gtranslate = null;
	if ( is_array( $shortcode_tags ) && isset( $shortcode_tags['gtranslate'] ) ) {
		$gtranslate = $shortcode_tags['gtranslate'];
		unset( $shortcode_tags['gtranslate'] );
	}
	fenix_language_switcher();
	if ( null !== $gtranslate ) {
		$shortcode_tags['gtranslate'] = $gtranslate;
	}
}

/**
 * URL ของหน้าตาม slug เมื่อเผยแพร่แล้ว ('' = ยังไม่เผยแพร่/ไม่มี)
 */
function fenix_chrome_page_url( $slug ) {
	return function_exists( 'fenix_published_page_url' ) ? (string) fenix_published_page_url( $slug ) : '';
}

/**
 * URL หน้ารวมบทความ (หน้า Posts ของ WordPress หรือเพจ slug articles) · '' = ยังไม่เผยแพร่
 */
function fenix_chrome_articles_url() {
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page > 0 && 'publish' === get_post_status( $posts_page ) ) {
		return (string) get_permalink( $posts_page );
	}
	return fenix_chrome_page_url( 'articles' );
}

/**
 * โครงเมนูมาตรฐานของ FALCON (ใช้เมื่อยังไม่มีเมนูใน WP) · เฉพาะหน้าที่เผยแพร่แล้ว
 *
 * @return array[] { label, url, slug, current, children[] }
 */
function fenix_chrome_nav_tree() {
	static $tree = null;
	if ( null !== $tree ) {
		return $tree;
	}
	$tree = array(
		array(
			'label'    => (string) fenix_mod( 'nav_home_label' ),
			'url'      => home_url( '/' ),
			'current'  => is_front_page(),
			'children' => array(),
		),
	);

	$groups = array(
		'test'  => array( 'nav_test_label', 'backtest' ),
		'guide' => array( 'nav_guide_label', 'how-to-install' ),
	);
	foreach ( $groups as $group => $conf ) {
		$children = array();
		$active   = false;
		if ( function_exists( 'fenix_guide_links' ) ) {
			foreach ( fenix_guide_links( array( $group ) ) as $slug => $link ) {
				$url = fenix_chrome_page_url( $slug );
				if ( '' === $url ) {
					continue;
				}
				$here       = is_page( $slug );
				$active     = $active || $here;
				$children[] = array(
					'label'   => $link['label'],
					'url'     => $url,
					'current' => $here,
				);
			}
		}
		if ( empty( $children ) ) {
			continue;
		}
		$landing = fenix_chrome_page_url( $conf[1] );
		$tree[]  = array(
			'label'    => (string) fenix_mod( $conf[0] ),
			'url'      => '' !== $landing ? $landing : $children[0]['url'],
			'current'  => false,
			'ancestor' => $active,
			'children' => $children,
		);
	}

	$singles = array(
		array( 'nav_pricing_label', fenix_chrome_page_url( 'pricing' ), is_page( 'pricing' ) ),
		array( 'nav_articles_label', fenix_chrome_articles_url(), is_home() ),
		array( 'nav_contact_label', fenix_chrome_page_url( 'go' ), is_page( 'go' ) ),
	);
	foreach ( $singles as $single ) {
		$label = trim( (string) fenix_mod( $single[0] ) );
		if ( '' === $single[1] || '' === $label ) {
			continue;
		}
		$tree[] = array(
			'label'    => $label,
			'url'      => $single[1],
			'current'  => (bool) $single[2],
			'children' => array(),
		);
	}
	return $tree;
}

/**
 * เมนูสำรอง (ยังไม่ได้ตั้งเมนูหลักใน WP) · แทน fenix_fallback_menu() ที่ลิงก์ /go/ และ /articles/ แม้ยังไม่เผยแพร่
 */
function fenix_chrome_fallback_menu() {
	echo '<ul class="nav-list">';
	foreach ( fenix_chrome_nav_tree() as $item ) {
		$classes = array( 'menu-item' );
		if ( ! empty( $item['children'] ) ) {
			$classes[] = 'menu-item-has-children';
		}
		if ( ! empty( $item['current'] ) ) {
			$classes[] = 'current-menu-item';
		}
		if ( ! empty( $item['ancestor'] ) ) {
			$classes[] = 'current-menu-ancestor';
		}
		echo '<li class="' . esc_attr( implode( ' ', $classes ) ) . '"><a href="' . esc_url( $item['url'] ) . '"' . ( ! empty( $item['current'] ) ? ' aria-current="page"' : '' ) . '>' . esc_html( $item['label'] ) . '</a>';
		if ( ! empty( $item['children'] ) ) {
			echo '<ul class="sub-menu">';
			foreach ( $item['children'] as $child ) {
				echo '<li class="menu-item' . ( $child['current'] ? ' current-menu-item' : '' ) . '"><a href="' . esc_url( $child['url'] ) . '"' . ( $child['current'] ? ' aria-current="page"' : '' ) . '>' . esc_html( $child['label'] ) . '</a></li>';
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * ต่อท้าย "ติดต่อ" (/go/) ในเมนูหลักที่สร้างเองใน WP
 * เฉพาะเมื่อหน้า go เผยแพร่แล้ว และเมนูยังไม่มีลิงก์ /go/ รายการชื่อเดียวกัน หรือลิงก์ LINE
 */
function fenix_chrome_menu_contact( $items, $args ) {
	if ( ! is_object( $args ) || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}
	$label = trim( (string) fenix_mod( 'nav_contact_label' ) );
	$go    = fenix_chrome_page_url( 'go' );
	if ( '' === $label || '' === $go ) {
		return $items;
	}
	/* มีลิงก์ /go/ อยู่แล้ว (รวมแบบไม่มี / ท้าย หรือมี ?query / #anchor) หรือมีรายการชื่อเดียวกัน */
	if ( false !== strpos( $items, esc_url( $go ) ) || preg_match( '#href="[^"]*/go/?(?:[?\#][^"]*)?"#i', $items ) || false !== strpos( $items, '>' . esc_html( $label ) . '<' ) ) {
		return $items;
	}
	/* มีลิงก์ LINE (OA / OpenChat / LIFF / line://) อยู่แล้ว */
	if ( preg_match( '#href="(?:(?:https?:)?//(?:www\.)?(?:lin\.ee|line\.me|liff\.line\.me|page\.line\.me)/|line:)#i', $items ) ) {
		return $items;
	}
	if ( fenix_has_line_url() ) {
		$line = trim( (string) fenix_mod( 'line_url' ) );
		if ( false !== strpos( $items, esc_url( $line ) ) ) {
			return $items;
		}
	}
	$here = is_page( 'go' );
	return $items . '<li class="menu-item menu-item-fenix-contact' . ( $here ? ' current-menu-item' : '' ) . '"><a href="' . esc_url( $go ) . '"' . ( $here ? ' aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a></li>';
}
add_filter( 'wp_nav_menu_items', 'fenix_chrome_menu_contact', 10, 2 );

/**
 * โซเชียล + อีเมล ที่กรอกแล้ว
 *
 * @return array[] { icon, url, label, mailto }
 */
function fenix_chrome_socials() {
	$out = array();
	foreach ( array(
		'facebook'  => array( 'facebook_url', 'footer_facebook_text' ),
		'instagram' => array( 'instagram_url', 'footer_instagram_text' ),
		'tiktok'    => array( 'tiktok_url', 'footer_tiktok_text' ),
		'youtube'   => array( 'youtube_url', 'footer_youtube_text' ),
	) as $icon => $keys ) {
		$url   = trim( (string) fenix_mod( $keys[0] ) );
		$label = trim( (string) fenix_mod( $keys[1] ) );
		if ( ! fenix_chrome_url_ok( $url ) || '' === $label ) {
			continue;
		}
		$out[] = array(
			'icon'  => $icon,
			'url'   => $url,
			'label' => $label,
		);
	}
	$email = trim( (string) fenix_mod( 'contact_email' ) );
	$label = trim( (string) fenix_mod( 'footer_email_text' ) );
	if ( '' !== $email && '' !== $label && is_email( $email ) ) {
		$out[] = array(
			'icon'   => 'mail',
			'mailto' => $email,
			'label'  => $label,
		);
	}
	return $out;
}

/**
 * href ของรายการโซเชียล (อีเมลเข้ารหัสด้วย antispambot)
 */
function fenix_chrome_social_href( $item ) {
	if ( isset( $item['mailto'] ) ) {
		return 'mailto:' . esc_attr( antispambot( $item['mailto'] ) );
	}
	return esc_url( $item['url'] );
}

/**
 * แถวไอคอนโซเชียล (ในลิ้นชักเมนูมือถือ) · ไม่มีช่องทางที่กรอก = ไม่พิมพ์อะไร
 */
function fenix_chrome_social_row( $class = 'nav-social' ) {
	$items = fenix_chrome_socials();
	if ( empty( $items ) ) {
		return;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		printf(
			'<a class="social-link" href="%1$s"%2$s aria-label="%3$s">%4$s</a>',
			fenix_chrome_social_href( $item ), // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_chrome_social_href
			isset( $item['mailto'] ) ? '' : ' target="_blank" rel="noopener"',
			esc_attr( $item['label'] ),
			fenix_icon( $item['icon'], 'icon' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
	echo '</div>';
}

/**
 * Footer · รายการหน้า (เมนูหลักระดับบน → โครงเมนูมาตรฐาน)
 *
 * @return array[] { 0 => label, 1 => url }
 */
function fenix_chrome_index_items() {
	$items     = array();
	$locations = function_exists( 'get_nav_menu_locations' ) ? get_nav_menu_locations() : array();
	if ( ! empty( $locations['primary'] ) ) {
		$menu_items = wp_get_nav_menu_items( (int) $locations['primary'] );
		if ( is_array( $menu_items ) ) {
			foreach ( $menu_items as $menu_item ) {
				if ( (int) $menu_item->menu_item_parent > 0 || ! fenix_chrome_url_ok( $menu_item->url ) ) {
					continue;
				}
				$items[] = array( wp_strip_all_tags( (string) $menu_item->title ), (string) $menu_item->url );
			}
		}
	}
	if ( empty( $items ) ) {
		foreach ( fenix_chrome_nav_tree() as $node ) {
			$items[] = array( $node['label'], $node['url'] );
		}
	}
	return $items;
}

/**
 * Footer · ช่องทาง (LINE OA, OpenChat, โซเชียล, อีเมล) เฉพาะที่กรอกแล้ว
 */
function fenix_chrome_channels() {
	$out = array();
	if ( fenix_has_line_url() ) {
		$out[] = array(
			'icon'  => 'line',
			'url'   => trim( (string) fenix_mod( 'line_url' ) ),
			'label' => (string) fenix_mod( 'footer_line_text' ),
			'pos'   => 'footer-channels',
			'line'  => true,
		);
	}
	$openchat = trim( (string) fenix_mod( 'line_openchat_url' ) );
	if ( fenix_chrome_url_ok( $openchat ) ) {
		$out[] = array(
			'icon'  => 'chat',
			'url'   => $openchat,
			'label' => (string) fenix_mod( 'line_openchat_text' ),
			'pos'   => 'footer-channels-openchat',
		);
	}
	return array_merge( $out, fenix_chrome_socials() );
}

/**
 * Footer · เอกสาร (เฉพาะหน้าที่เผยแพร่แล้ว) + ประกาศความเสี่ยง (แสดงเสมอ)
 */
function fenix_chrome_doc_items() {
	$out = array();
	foreach ( fenix_lines( fenix_mod( 'footer_docs_items' ) ) as $line ) {
		if ( false === strpos( $line, '|' ) ) {
			continue;
		}
		list( $slug, $label ) = array_map( 'trim', explode( '|', $line, 2 ) );
		$slug = sanitize_title( $slug );
		if ( '' === $slug || '' === $label || 'risk-disclosure' === $slug ) {
			continue;
		}
		$url = fenix_chrome_page_url( $slug );
		if ( '' !== $url ) {
			$out[] = array( $label, $url );
		}
	}
	/* ประกาศความเสี่ยง: แสดงทุกครั้งที่หน้าเผยแพร่แล้ว (ข้อความเตือนฉบับเต็มอยู่ใต้ดัชนีเสมอ จึงไม่ลิงก์ไปหน้าที่ยังไม่มี) */
	$risk_label = trim( (string) fenix_mod( 'footer_risk_link' ) );
	$risk_url   = fenix_chrome_page_url( 'risk-disclosure' );
	if ( '' !== $risk_label && '' !== $risk_url ) {
		$out[] = array( $risk_label, $risk_url );
	}
	return $out;
}

/**
 * Footer · ข้อมูลระบบ (ป้าย|ค่า) · ไม่รับค่าที่อ่านเป็นผลเทรด (ตัวเลขตามด้วย % หรือมีคำว่า กำไร) และค่าที่ยังเป็น placeholder
 */
function fenix_chrome_spec_rows() {
	$lines = fenix_lines( fenix_mod( 'footer_spec_items' ) );
	if ( empty( $lines ) ) {
		for ( $i = 1; $i <= 4; $i++ ) {
			$lines[] = trim( (string) fenix_mod( 'highlight' . $i ) );
		}
	}
	$rows = array();
	foreach ( $lines as $line ) {
		if ( false === strpos( (string) $line, '|' ) ) {
			continue;
		}
		list( $label, $value ) = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' === $label || '' === $value || fenix_is_placeholder( $value ) ) {
			continue;
		}
		if ( preg_match( '/^[+-]?\d[\d.,]*\s*%/u', $value ) || false !== strpos( $line, 'กำไร' ) ) {
			continue;
		}
		$rows[] = array( $label, $value );
	}
	return $rows;
}

/**
 * บาร์ล่างมือถือ · 5 ช่อง (ช่องกลาง = ติดต่อ) + ช่องที่ active คิดฝั่ง PHP (ปิด JS ก็ถูก)
 *
 * @return array { items[], active (int, -1 = ไม่มี), x (float %) }
 */
function fenix_chrome_dock() {
	$slots = array(
		array(
			'label' => fenix_mod( 'mobile_nav_home_label' ),
			'url'   => fenix_link_url( fenix_mod( 'mobile_nav_home_url' ) ),
			'icon'  => 'home',
		),
		array(
			'label' => fenix_mod( 'mobile_nav_test_label' ),
			'url'   => fenix_link_url( fenix_mod( 'mobile_nav_test_url' ) ),
			'icon'  => 'flask',
			'group' => 'test',
		),
	);
	$target = fenix_contact_target();
	if ( '' !== $target['url'] ) {
		$slots[] = array(
			'label'  => fenix_mod( $target['is_line'] ? 'mobile_nav_line_label' : 'mobile_nav_contact_label' ),
			'url'    => $target['url'],
			'icon'   => $target['is_line'] ? 'line' : 'chat',
			'action' => true,
			'line'   => $target['is_line'],
		);
	}
	$slots[] = array(
		'label' => fenix_mod( 'mobile_nav_price_label' ),
		'url'   => fenix_link_url( fenix_mod( 'mobile_nav_price_url' ) ),
		'icon'  => 'tag',
	);
	$slots[] = array(
		'label' => fenix_mod( 'mobile_nav_install_label' ),
		'url'   => fenix_link_url( fenix_mod( 'mobile_nav_install_url' ) ),
		'icon'  => 'book',
	);

	$items = array();
	foreach ( $slots as $slot ) {
		if ( '' === trim( (string) $slot['label'] ) || ! fenix_chrome_url_ok( $slot['url'] ) ) {
			continue;
		}
		$items[] = $slot;
	}

	/* หน้าปัจจุบัน: ใช้ $wp->request (ผ่าน routing ของ WP แล้ว ไม่มี query string) */
	$request = ( isset( $GLOBALS['wp'] ) && is_object( $GLOBALS['wp'] ) && isset( $GLOBALS['wp']->request ) ) ? (string) $GLOBALS['wp']->request : '';
	$home    = '/' . trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	$here    = '/' . trim( (string) wp_parse_url( home_url( '/' . ltrim( $request, '/' ) ), PHP_URL_PATH ), '/' );
	$host    = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$active  = -1;
	$score   = -1;

	foreach ( $items as $idx => $item ) {
		if ( ! empty( $item['action'] ) || 0 === strpos( $item['url'], '#' ) ) {
			continue;
		}
		$link_host = wp_parse_url( $item['url'], PHP_URL_HOST );
		if ( $link_host && $link_host !== $host ) {
			continue;
		}
		$path = '/' . trim( (string) wp_parse_url( $item['url'], PHP_URL_PATH ), '/' );
		$hit  = $path === $home ? $here === $home : ( $here === $path || 0 === strpos( $here, $path . '/' ) );
		if ( $hit && strlen( $path ) > $score ) {
			$active = (int) $idx;
			$score  = strlen( $path );
		}
	}

	/* หน้า Forward Test อยู่กลุ่มเดียวกับ Backtest → ช่อง "การทดสอบ" ติดไฟ */
	if ( -1 === $active && is_page() && function_exists( 'fenix_site_pages' ) ) {
		$slug  = (string) get_post_field( 'post_name', get_queried_object_id() );
		$pages = fenix_site_pages();
		$group = isset( $pages[ $slug ]['group'] ) ? $pages[ $slug ]['group'] : '';
		foreach ( $items as $idx => $item ) {
			if ( '' !== $group && isset( $item['group'] ) && $item['group'] === $group ) {
				$active = (int) $idx;
				break;
			}
		}
	}

	$count = max( 1, count( $items ) );
	return array(
		'items'  => $items,
		'active' => $active,
		'x'      => $active >= 0 ? ( $active + 0.5 ) * ( 100 / $count ) : 50,
	);
}

/* ==============================================================
 * แถบติดต่อท้ายหน้าย่อย (.cta-console) · แทน fenix_line_cta() เดิม
 * QR / OpenChat / รายการเตรียมข้อความ ย้ายไปอยู่ในแถวแรกของ footer (ไม่ซ้ำสองที่)
 * ============================================================== */
function fenix_chrome_line_cta( $title = '', $sub = '' ) {
	if ( '' === fenix_contact_href() && ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$title = '' !== trim( (string) $title ) ? $title : fenix_mod( 'contact_title' );
	$sub   = '' !== trim( (string) $sub ) ? $sub : fenix_mod( 'contact_text' );
	$label = trim( (string) fenix_mod( 'chrome_cta_label' ) );
	?>
	<section class="cta-console" id="cta" aria-labelledby="cta-console-title">
		<div class="container cta-console-inner">
			<div class="cta-console-copy reveal">
				<?php if ( '' !== $label ) : ?>
					<p class="chrome-label"><span class="chrome-dot" aria-hidden="true"></span><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
				<h2 class="cta-console-title" id="cta-console-title"><?php echo fenix_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></h2>
				<?php if ( '' !== trim( (string) $sub ) ) : ?>
					<p class="cta-console-sub"><?php echo fenix_text( $sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></p>
				<?php endif; ?>
			</div>
			<div class="cta-console-action">
				<?php
				fenix_contact_button(
					array(
						'class' => 'btn btn-fire btn-lg',
						'pos'   => 'page-cta',
					)
				);
				?>
			</div>
		</div>
	</section>
	<?php
}
add_action( 'fenix_line_cta', 'fenix_chrome_line_cta', 10, 2 );

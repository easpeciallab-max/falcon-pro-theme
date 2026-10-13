<?php
/**
 * FALCON PRO EA · โมดูล Site Chrome (header · footer · แถบติดต่อท้ายหน้าย่อย · บาร์ล่างมือถือ)
 *
 * หน้าตาแบบ FALCON (มุมโค้ง ปุ่ม pill เส้นบาง เขียว FALCON บนพื้นเข้ม #172125)
 * - ค่าเริ่มต้น + ส่วน Customizer ของ header/footer/dock อยู่ในไฟล์นี้ (ผ่านฟิลเตอร์ fenix_defaults / fenix_customizer_sections)
 * - บล็อกติดต่อ (ปุ่ม LINE · OpenChat · อีเมล · QR · รายการเตรียมข้อความ · เวลาตอบแชท) วาดโดย fenix_chrome_launch() ที่เดียว
 *   และพิมพ์ได้หน้าละ 1 ครั้ง: หน้าที่เรียก fenix_line_cta() ได้บล็อกท้ายเนื้อหา (.cta-console) พร้อมหัวข้อของหน้านั้น
 *   หน้าอื่น (หน้าแรก บทความรวม 404 ค้นหา เพจเอกสาร) ได้บล็อกเดียวกันเป็นแถวแรกของ footer (fenix_chrome_footer_contact)
 * - ปุ่มติดต่อบนแถบหัว: fenix_chrome_header_cta() · คอลัมน์คู่มือใน footer: fenix_chrome_guide_items()
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

				/* รูป QR ของ LINE OA ที่มากับธีม (ใช้เมื่อเจ้าของยังไม่อัปโหลดรูปเอง · ไม่มีไฟล์ = ว่าง) */
				'line_qr_image'            => fenix_chrome_qr_bundled_url(),

				/* บล็อกติดต่อท้ายหน้าย่อย (.cta-console) */
				'chrome_cta_label'         => 'ยังไม่แน่ใจ ถามก่อนได้',
				'contact_title'            => 'อยากรู้ก่อนว่า FALCON PRO EA ไปกันได้กับทุนและบัญชีที่มีอยู่ไหม?',
				/* FALCON ใช้ผ่านแดชบอร์ดอย่างเดียว (เจ้าของตัดสิน 13 ต.ค. 2026): ไม่มีไฟล์ EA ให้ติดตั้งเอง ข้อความชวนคุยจึงพูดถึงการเริ่มใช้บนแดชบอร์ด */
				'contact_text'             => 'ส่งรายละเอียดทุน โบรกเกอร์ และวิธีเทรดที่ใช้อยู่มาให้ทีมงาน เราจะช่วยประเมินความพร้อมของบัญชี และอธิบายลำดับการเริ่มใช้บนแดชบอร์ดให้เข้าใจก่อน โดยไม่เร่งให้ตัดสินใจซื้อ',

				/* Footer · แถวแรก (launch console) */
				'footer_console_label'     => 'ติดต่อทีม FALCON PRO',
				'footer_headline'          => 'เริ่มจากบทสนทนาสั้น ๆ กับทีม FALCON',
				'footer_sub'               => 'เลือกแพ็กเกจ ขอรหัสเชิญเข้าแดชบอร์ด หรือเลือกชุด EA ให้พอดีกับเงินทุน ถามทีมงานได้ทุกเรื่องก่อนลงมือจริง',
				'footer_line_text'         => 'เปิดแชท LINE กับทีมงาน',
				'footer_qr_toggle_text'    => 'เปิด QR Code สำหรับสแกน',
				'footer_qr_text'           => 'ดูเว็บนี้บนคอมพิวเตอร์อยู่? สแกน QR ด้วยกล้องมือถือ แล้วคุยกับทีมงานต่อใน LINE',
				'footer_line_qr_alt'       => 'QR Code บัญชี LINE ทางการของ FALCON PRO EA',
				'footer_prep_title'        => 'ข้อความแรกที่ช่วยให้ทีมตอบได้ตรง',
				'footer_prep_text'         => 'พิมพ์สั้น ๆ ตามหัวข้อด้านล่าง ทีมงานจะได้ไม่ต้องถามย้อนหลายรอบ',
				'footer_prep_items'        => "งบที่ตั้งใจใช้กับ EA (ประมาณคร่าว ๆ ก็ได้)\nใช้โบรกเกอร์ไหน และเปิดบัญชี MT5 แบบใดไว้\nเรื่องที่อยากให้ช่วย: ขอรหัสเชิญ ดูแพ็กเกจ หรือเช็กความพร้อม\nอยากให้ทีมทักกลับช่วงไหนของวัน",
				'footer_hours_title'       => 'ช่วงเวลาที่ทีมตอบแชท',
				'footer_hours_text'        => '',

				/* Footer · ดัชนี */
				'footer_index_title'       => 'หน้าในเว็บไซต์',
				'footer_channels_title'    => 'ติดตามและติดต่อ',
				'footer_docs_title'        => 'เอกสารและนโยบาย',
				'footer_docs_items'        => "about|เกี่ยวกับเรา\nprivacy-policy|นโยบายความเป็นส่วนตัว\nterms-of-use|เงื่อนไขการใช้บริการ\ndata-deletion|คำขอลบข้อมูลส่วนบุคคล",
				'footer_risk_link'         => 'ประกาศความเสี่ยง',
				'footer_risk_more'         => 'อ่านประกาศความเสี่ยงแบบแยกหัวข้อ',
				'footer_risk_short'        => 'การเทรดทองคำและ CFD ด้วย Leverage มีความเสี่ยงสูง คุณอาจสูญเสียเงินทุนทั้งหมด FALCON PRO EA เปิดออเดอร์แก้เพิ่มด้วย Lot ที่ใหญ่ขึ้นและไม่ตัดขาดทุนเอง ผลในอดีตไม่รับประกันผลในอนาคต ซอฟต์แวร์นี้ไม่ถือเป็นคำแนะนำด้านการลงทุน และไม่มีการรับประกันกำไร',
				'footer_spec_title'        => 'ข้อมูลระบบโดยย่อ',
				'footer_spec_items'        => '',
				'footer_facebook_text'     => 'เพจ Facebook',
				'footer_instagram_text'    => 'Instagram',
				'footer_tiktok_text'       => 'TikTok',
				'footer_youtube_text'      => 'YouTube',
				'footer_email_text'        => 'อีเมลถึงทีมงาน',
				/* ลิงก์เข้าสู่ระบบแดชบอร์ดสำหรับสมาชิกเดิม (แถวเล็กใต้ดัชนี) · ข้อความลิงก์และที่อยู่ใช้ค่าจากหมวด 1.1) แดชบอร์ด */
				'footer_member_label'      => 'สำหรับสมาชิก',

				/* Footer · ลายน้ำ + แถบสถานะ · ชื่อแบรนด์ตัวใหญ่แบบเส้นขอบปิดเป็นค่าเริ่มต้น (เจ้าของสั่งถอด 12 ต.ค. 2026) · ปิด = footer.php ไม่พิมพ์ p.ft-wm */
				'show_footer_watermark'    => false,
				'footer_watermark_text'    => 'FALCON PRO',
				'footer_copyright_text'    => 'สงวนลิขสิทธิ์',
				'footer_status_text'       => 'ทำงานบน MetaTrader 5 · ตั้งความเสี่ยงให้พอดีกับทุนทุกครั้ง',
				'show_footer_clock'        => true,
				'footer_clock_label'       => 'เวลาประเทศไทย',
				'footer_backtop_text'      => 'ขึ้นด้านบน',

				/* บาร์ล่างมือถือ (ช่องกลาง = ปุ่มติดต่อ) · ช่อง 5 เดิมคือ "วิธีติดตั้ง" (เพจนั้นถอดออกแล้ว) ตอนนี้ไปหน้าแนะนำแดชบอร์ดของเว็บนี้
				 * ช่องที่ชี้ไปเพจที่ถอดแล้วหรือยังไม่เผยแพร่ไม่ถูกพิมพ์ (fenix_chrome_slot_url) บาร์จึงไม่มีปุ่มที่พาไปหน้า 404 */
				'mobile_nav_test_label'    => 'การทดสอบ',
				'mobile_nav_test_url'      => '/backtest/',
				'mobile_nav_contact_label' => 'ติดต่อ',
				'mobile_nav_install_label' => 'แดชบอร์ด',
				'mobile_nav_install_url'   => '/dashboard/',

				/* เมนูหลัก (ใช้เมื่อยังไม่ได้สร้างเมนูใน WordPress) + ข้อความระบบ */
				'nav_home_label'           => 'หน้าแรก',
				'nav_test_label'           => 'การทดสอบ',
				'nav_guide_label'          => 'คู่มือการใช้งาน',
				/* รายการระดับบนไปหน้า /dashboard/ (แสดงเมื่อหน้านั้นเผยแพร่แล้ว) · ว่าง = หน้านั้นอยู่ในกลุ่มคู่มือตามเดิม */
				'nav_dashboard_label'      => 'แดชบอร์ด',
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
					'chrome_cta_label' => array( 'บล็อกติดต่อท้ายหน้าย่อย · ป้ายเล็กเหนือหัวข้อ', 'text', 'แสดงเมื่อเปิด "แสดงคำเล็กเหนือหัวข้อ (ทั้งเว็บ)" ด้านบนเท่านั้น' ),
				)
			);
			/* รูป QR: บอกเจ้าของว่าธีมมีรูปตั้งต้นให้ และแสดงเมื่อไร */
			if ( isset( $sections['fenix_general']['fields']['line_qr_image'] ) && ! isset( $sections['fenix_general']['fields']['line_qr_image'][2] ) ) {
				$sections['fenix_general']['fields']['line_qr_image'][2] = 'ไม่อัปโหลด = ใช้รูป QR ที่มากับธีม ซึ่งแสดงเฉพาะเมื่อ "ลิงก์ LINE OA" ด้านบนเป็นลิงก์เดียวกับที่ QR นั้นพาไป · เปลี่ยนบัญชี LINE เมื่อไร ให้อัปโหลด QR ของบัญชีใหม่ที่ช่องนี้ แล้วลองสแกนด้วยมือถือทุกครั้ง · กดลบรูป = ไม่แสดง QR';
			}
		}

		$sections['fenix_footer'] = array(
			'title'       => '15) บล็อกติดต่อ + Footer ท้ายเว็บ',
			'description' => 'บล็อกติดต่อ (ปุ่ม LINE · OpenChat · อีเมล · QR · รายการเตรียมข้อความ · เวลาตอบแชท) แสดงหน้าละ 1 ครั้ง: หน้าย่อยที่มีบล็อกติดต่อของตัวเอง (คู่มือ การทดสอบ แพ็กเกจ ความเสี่ยง เกี่ยวกับเรา บทความ) แสดงท้ายเนื้อหาด้วยหัวข้อของหน้านั้น ส่วนหน้าอื่น (หน้าแรก หน้ารวมบทความ เพจเอกสาร) แสดงเป็นแถวแรกของ footer ด้วยหัวข้อ "แถวติดต่อ" ด้านล่าง · ถัดลงไปคือ ดัชนีลิงก์ → คำเตือนความเสี่ยงฉบับเต็ม → ลายน้ำแบรนด์ → แถบสถานะ · ช่องที่เว้นว่างจะถูกซ่อน · ปุ่มติดต่อใช้ลิงก์ LINE จาก "1) ช่องทางติดต่อ" (ไม่มี LINE = ไปหน้า /go/ ถ้าเผยแพร่แล้ว ไม่มีทั้งคู่ = ซ่อนปุ่ม) · รูป QR ใช้ "รูป QR Code LINE OA" ในหมวด 1',
			'fields'      => array(
				'footer_console_label'  => array( 'แถวติดต่อ · ป้ายเล็กด้านบน', 'text', 'แสดงเมื่อเปิด "แสดงคำเล็กเหนือหัวข้อ (ทั้งเว็บ)" ที่หมวด 1 เท่านั้น' ),
				'footer_headline'       => array( 'แถวติดต่อ · หัวข้อ (ว่าง = ใช้หัวข้อบล็อกติดต่อท้ายหน้าย่อย)', 'text' ),
				'footer_sub'            => array( 'แถวติดต่อ · คำอธิบาย (ว่าง = ใช้คำอธิบายบล็อกติดต่อท้ายหน้าย่อย)', 'textarea' ),
				'footer_line_text'      => array( 'ข้อความปุ่ม LINE (ใช้กับปุ่มติดต่อทั่วทั้งเว็บ)', 'text' ),
				'footer_qr_text'        => array( 'QR · ข้อความข้างรูปบนจอกว้าง (ว่าง = แสดงเฉพาะรูป)', 'text', 'บนคอมพิวเตอร์และแท็บเล็ต รูป QR แสดงเปิดไว้ในบล็อกติดต่อพร้อมข้อความนี้ · บนมือถือ QR พับอยู่ในปุ่มด้านล่าง เพราะกดปุ่ม LINE ได้โดยตรง' ),
				'footer_qr_toggle_text' => array( 'QR · ข้อความปุ่มเปิด QR บนมือถือ', 'text' ),
				'footer_line_qr_alt'    => array( 'คำอธิบายรูป QR (alt)', 'text' ),
				'footer_prep_title'     => array( 'กล่องเตรียมข้อความแรก · หัวข้อ', 'text' ),
				'footer_prep_text'      => array( 'กล่องเตรียมข้อความแรก · คำอธิบาย', 'textarea' ),
				'footer_prep_items'     => array( 'กล่องเตรียมข้อความแรก · รายการ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'footer_hours_title'    => array( 'เวลาตอบแชท · หัวข้อ', 'text' ),
				'footer_hours_text'     => array( 'เวลาตอบแชท · รายละเอียด (ว่าง = ไม่แสดง)', 'textarea', 'กรอกตามเวลาที่ทีมตอบได้จริงเท่านั้น บรรทัดละ 1 ช่วง' ),
				'footer_index_title'    => array( 'หัวคอลัมน์ · หน้าในเว็บไซต์', 'text', 'รายการหน้ามาจากเมนูหลัก (เฉพาะระดับบน) · ยังไม่มีเมนู = ใช้หน้ามาตรฐานที่เผยแพร่แล้ว · คอลัมน์คู่มือที่อยู่ถัดไปดึงหน้าคู่มือที่เผยแพร่แล้วมาเองทุกหน้า หัวคอลัมน์ใช้ชื่อ "เมนู · กลุ่มคู่มือ" ในหมวด 16.1' ),
				'footer_channels_title' => array( 'หัวคอลัมน์ · ช่องทาง', 'text' ),
				'footer_facebook_text'  => array( 'ชื่อลิงก์ Facebook', 'text' ),
				'footer_instagram_text' => array( 'ชื่อลิงก์ Instagram', 'text' ),
				'footer_tiktok_text'    => array( 'ชื่อลิงก์ TikTok', 'text' ),
				'footer_youtube_text'   => array( 'ชื่อลิงก์ YouTube', 'text' ),
				'footer_email_text'     => array( 'ชื่อลิงก์อีเมล', 'text', 'ใช้ทั้งในคอลัมน์ช่องทางและในบล็อกติดต่อ · แสดงเมื่อกรอก "อีเมลติดต่อ" ในหมวด 1 แล้วเท่านั้น' ),
				'footer_member_label'   => array( 'แถวสมาชิกแดชบอร์ด · ข้อความนำหน้าลิงก์เข้าสู่ระบบ (ว่าง = แสดงเฉพาะลิงก์)', 'text', 'แถวเล็กใต้ดัชนีลิงก์ สำหรับสมาชิกเดิมของแดชบอร์ด · ข้อความลิงก์ใช้ "ข้อความปุ่ม · เข้าสู่ระบบ" และที่อยู่จากหมวด 1.1) แดชบอร์ด (ลบข้อความปุ่มนั้นหรือปิดแดชบอร์ด = แถวนี้หายไป) · ลิงก์เปิดแท็บใหม่' ),
				'footer_docs_title'     => array( 'หัวคอลัมน์ · เอกสาร', 'text' ),
				'footer_docs_items'     => array( 'รายการเอกสาร (slug|ชื่อลิงก์ บรรทัดละ 1 หน้า)', 'textarea', 'แสดงเฉพาะหน้าที่เผยแพร่แล้ว · ลิงก์ประกาศความเสี่ยงแสดงเมื่อหน้า risk-disclosure เผยแพร่แล้ว (กล่องคำเตือนแสดงใต้ดัชนีเสมอ) · ชื่อลิงก์ "ตั้งค่าคุกกี้" ท้ายคอลัมน์แก้ได้ที่หมวดคุกกี้' ),
				'footer_risk_link'      => array( 'ชื่อลิงก์ประกาศความเสี่ยง', 'text' ),
				'footer_risk_short'     => array( 'คำเตือนความเสี่ยงท้ายเว็บ · ฉบับสั้น (ว่าง = แสดงฉบับเต็ม)', 'textarea', 'ข้อความในกล่องคำเตือนท้ายทุกหน้า ควรยาวไม่เกิน 2 บรรทัด และต้องบอกว่าเสียเงินทุนได้ทั้งหมด ผลในอดีตไม่รับประกันอนาคต และไม่ใช่คำแนะนำการลงทุน · ใช้ฉบับสั้นเฉพาะเมื่อมีลิงก์ไปหน้าประกาศความเสี่ยง (หน้านั้นเผยแพร่แล้วและช่องลิงก์ด้านล่างไม่ว่าง) ไม่เช่นนั้นแสดงคำเตือนฉบับเต็มจากหมวดคำเตือนของหน้าแรก · คำเตือนฉบับเต็มยังอยู่ที่หน้าแรก หน้า /go และหน้าประกาศความเสี่ยงเสมอ' ),
				'footer_risk_more'      => array( 'ลิงก์ใต้คำเตือนความเสี่ยง · ข้อความ (ว่าง = ไม่แสดงลิงก์)', 'text', 'ต่อท้ายคำเตือนความเสี่ยงใน footer พาไปหน้าประกาศความเสี่ยง (แสดงเมื่อหน้านั้นเผยแพร่แล้ว) · ถ้าเว้นว่าง กล่องคำเตือนจะกลับไปแสดงฉบับเต็ม' ),
				'footer_spec_title'     => array( 'หัวคอลัมน์ · ข้อมูลระบบ', 'text' ),
				'footer_spec_items'     => array( 'ข้อมูลระบบ (ป้าย|ค่า บรรทัดละ 1 แถว)', 'textarea', 'ว่าง = ใช้ไฮไลต์ 1–4 ของหน้าแรก · บรรทัดที่เป็นตัวเลขตามด้วย % หรือมีคำว่า "กำไร" จะไม่แสดง (ห้ามใส่ผลเทรด)' ),
				'show_footer_watermark' => array( 'แสดงชื่อแบรนด์ตัวใหญ่แบบเส้นขอบ', 'checkbox', 'ปิดไว้เป็นค่าเริ่มต้น · เป็นตัวหนังสือตกแต่งขนาดใหญ่เหนือแถบล่างสุดของเว็บ' ),
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
			'description' => 'บาร์ 5 ช่องบนจอมือถือ · ช่องกลางคือปุ่มติดต่อ: มีลิงก์ LINE = ทัก LINE · ไม่มี LINE = ไปหน้า /go/ (เมื่อเผยแพร่แล้ว) · ไม่มีทั้งคู่ = เหลือ 4 ช่อง · ช่องของหน้าปัจจุบันติดไฟให้อัตโนมัติ · ช่องที่ใส่ slug ของเพจที่ยังไม่เผยแพร่ หรือเพจที่ถอดออกจากเว็บแล้ว (คู่มือติดตั้ง คู่มือ VPS หน้า Forward Test) จะไม่แสดง',
			'fields'      => array(
				'show_mobile_nav'          => array( 'แสดงเมนูลัดด้านล่างบนมือถือ', 'checkbox' ),
				'mobile_nav_home_label'    => array( 'ช่อง 1 · ชื่อ', 'text' ),
				'mobile_nav_home_url'      => array( 'ช่อง 1 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_test_label'    => array( 'ช่อง 2 · ชื่อ', 'text' ),
				'mobile_nav_test_url'      => array( 'ช่อง 2 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_line_label'    => array( 'ช่อง 3 (กลาง) · ชื่อปุ่มเมื่อมีลิงก์ LINE', 'text', 'ใช้เป็นข้อความปุ่มติดต่อบนแถบหัวเว็บด้วย (จอกว้าง และจอมือถือเมื่อปิดเมนูลัดนี้) · เว้นว่าง = ไม่มีปุ่มทั้งสองที่' ),
				'mobile_nav_contact_label' => array( 'ช่อง 3 (กลาง) · ชื่อปุ่มเมื่อยังไม่มี LINE (ไปหน้า /go/)', 'text', 'ใช้เป็นข้อความปุ่มติดต่อบนแถบหัวเว็บด้วยเช่นกัน' ),
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
						'nav_guide_label'     => array( 'เมนู · กลุ่มคู่มือ', 'text', 'ใช้เป็นหัวคอลัมน์คู่มือใน footer ด้วย' ),
						'nav_dashboard_label' => array( 'เมนู · แดชบอร์ด (ไปหน้า /dashboard/ ของเว็บนี้)', 'text', 'แสดงเป็นรายการระดับบนเมื่อหน้า /dashboard/ เผยแพร่แล้ว · เว้นว่าง = หน้านั้นกลับไปอยู่ใต้กลุ่มคู่มือ · มีผลกับเมนูสำรองเท่านั้น ถ้าตั้งเมนูเองที่ รูปแบบ → เมนู ให้เพิ่มหน้า "แดชบอร์ด FALCON PRO" ที่นั่น · footer แสดงลิงก์หน้านี้เสมอเมื่อเผยแพร่แล้ว (คอลัมน์หน้าในเว็บไซต์ หรือคอลัมน์คู่มือ)' ),
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
 * body class: มีบาร์ล่างมือถือจริง (เปิดใช้ และมีอย่างน้อย 1 ช่อง)
 * ให้การ์ดคุกกี้/ปุ่มลอยยกตัวพ้นบาร์ได้ด้วย var(--dock-h) และซ่อนปุ่มติดต่อบนแถบหัวในจอมือถือ (บาร์ล่างมีปุ่มติดต่ออยู่แล้ว)
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( fenix_mod( 'show_mobile_nav' ) ) {
			$dock = fenix_chrome_dock();
			if ( ! empty( $dock['items'] ) ) {
				$classes[] = 'has-dock';
			}
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
 * ปุ่มติดต่อบนแถบหัว · LINE → หน้า /go/ (เมื่อเผยแพร่แล้ว) → ไม่พิมพ์ (ไม่มีปุ่มที่ชี้ '#')
 * ข้อความใช้ชุดเดียวกับช่องกลางของบาร์ล่างมือถือ (mobile_nav_line_label / mobile_nav_contact_label) · ข้อความว่าง = ไม่พิมพ์
 * จอ ≤760px ที่มีบาร์ล่าง (body.has-dock) ซ่อนด้วย CSS เพราะบาร์ล่างมีปุ่มติดต่ออยู่แล้ว
 */
function fenix_chrome_header_cta() {
	$target = fenix_contact_target();
	if ( '' === $target['url'] ) {
		return;
	}
	$label = trim( (string) fenix_mod( $target['is_line'] ? 'mobile_nav_line_label' : 'mobile_nav_contact_label' ) );
	if ( '' === $label ) {
		return;
	}
	printf(
		'<a class="btn btn-fire btn-sm header-cta" href="%1$s"%2$s data-line-pos="header">%3$s<span>%4$s</span></a>',
		esc_url( $target['url'] ),
		$target['is_line'] ? ' target="_blank" rel="noopener"' : '',
		fenix_icon( $target['is_line'] ? 'line' : 'chat' ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( $label )
	);
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
 * หน้า /dashboard/ ของเว็บนี้เป็นรายการระดับบนของเมนูมาตรฐานหรือไม่: หน้าเผยแพร่แล้ว และมีชื่อเมนู (nav_dashboard_label)
 * ชื่อเมนูว่าง = หน้านั้นอยู่ในกลุ่มคู่มือตาม manifest (fenix_site_pages) เหมือนหน้าคู่มืออื่น
 * หน้านี้เป็นหน้าอธิบายบนเว็บเรา (ไม่ใช่ตัวแดชบอร์ด) จึงแสดงได้แม้ปิดปุ่มไปแดชบอร์ดที่หมวด 1.1
 */
function fenix_chrome_dashboard_top() {
	return '' !== trim( (string) fenix_mod( 'nav_dashboard_label' ) ) && '' !== fenix_chrome_page_url( 'dashboard' );
}

/**
 * โครงเมนูมาตรฐานของ FALCON (ใช้เมื่อยังไม่มีเมนูใน WP) · เฉพาะหน้าที่เผยแพร่แล้ว
 * ลำดับ: หน้าแรก → การทดสอบ → คู่มือการใช้งาน → แดชบอร์ด → แพ็กเกจ → บทความ → ติดต่อ
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

	/* หน้า /dashboard/ เป็นรายการระดับบนของเมนู (เมื่อเผยแพร่แล้วและมีชื่อเมนู) · กรณีนั้นไม่ซ้ำในเมนูย่อยของกลุ่มคู่มือ */
	$dash_top = fenix_chrome_dashboard_top();

	/* กลุ่มของเมนู = กลุ่มเพจใน manifest (เพจที่ถอดออกจากเว็บแล้วไม่อยู่ใน fenix_guide_links จึงไม่ขึ้นเมนู)
	 * หัวกลุ่มชี้ไปเพจแรกของกลุ่มที่เผยแพร่แล้ว · กลุ่มที่เหลือเพจเดียว (การทดสอบ มี Backtest หน้าเดียว) เป็นรายการระดับบนรายการเดียว
	 * ไม่มีเมนูย่อยที่ซ้ำกับหัวกลุ่ม */
	$groups = array(
		'test'  => 'nav_test_label',
		'guide' => 'nav_guide_label',
	);
	foreach ( $groups as $group => $label_key ) {
		$children = array();
		$active   = false;
		if ( function_exists( 'fenix_guide_links' ) ) {
			foreach ( fenix_guide_links( array( $group ) ) as $slug => $link ) {
				if ( 'dashboard' === $slug && $dash_top ) {
					continue;
				}
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
		$label = trim( (string) fenix_mod( $label_key ) );
		if ( 1 === count( $children ) ) {
			$tree[] = array(
				'label'    => '' !== $label ? $label : $children[0]['label'],
				'url'      => $children[0]['url'],
				'current'  => $active,
				'children' => array(),
			);
			continue;
		}
		$tree[] = array(
			'label'    => $label,
			'url'      => $children[0]['url'],
			'current'  => false,
			'ancestor' => $active,
			'children' => $children,
		);
	}

	$singles = array(
		array( 'nav_dashboard_label', $dash_top ? fenix_chrome_page_url( 'dashboard' ) : '', is_page( 'dashboard' ) ),
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
 * slug ของเพจในเว็บนี้ที่รายการเมนูชี้ไป · '' = รายการนี้ไม่ได้ชี้ไปเพจระดับบนของเว็บ (ลิงก์นอกเว็บ หมวด บทความ เพจลูก #anchor)
 * - รายการแบบเลือกเพจ (post_type page): slug ของเพจนั้น
 * - รายการแบบลิงก์กำหนดเอง: ที่อยู่ในเว็บนี้รูปแบบ /slug/ (มี ?query หรือ #anchor ต่อท้ายได้)
 */
function fenix_chrome_menu_item_slug( $item ) {
	if ( ! is_object( $item ) ) {
		return '';
	}
	$type = isset( $item->type ) ? (string) $item->type : '';
	if ( 'post_type' === $type ) {
		if ( ! isset( $item->object, $item->object_id ) || 'page' !== $item->object || (int) get_post_field( 'post_parent', (int) $item->object_id ) > 0 ) {
			return '';
		}
		return (string) get_post_field( 'post_name', (int) $item->object_id );
	}
	if ( 'custom' !== $type || empty( $item->url ) ) {
		return '';
	}
	$parts = wp_parse_url( (string) $item->url );
	$home  = wp_parse_url( home_url( '/' ) );
	if ( ! is_array( $parts ) || empty( $parts['path'] ) ) {
		return '';
	}
	if ( ! empty( $parts['host'] ) && ( empty( $home['host'] ) || strtolower( $parts['host'] ) !== strtolower( $home['host'] ) ) ) {
		return '';
	}
	$base = isset( $home['path'] ) ? untrailingslashit( (string) $home['path'] ) : '';
	$path = (string) $parts['path'];
	if ( '' !== $base ) {
		if ( 0 !== strpos( $path, $base . '/' ) ) {
			return '';
		}
		$path = substr( $path, strlen( $base ) );
	}
	return preg_match( '#^/([a-z0-9][a-z0-9\-]*)/?$#i', $path, $m ) ? strtolower( $m[1] ) : '';
}

/**
 * รายการเมนูนี้พาผู้เข้าชมไปหน้าที่เปิดไม่ได้แล้วหรือไม่:
 * - เพจที่ถอดออกจากเว็บแล้ว (fenix_retired_pages · เช่นคู่มือติดตั้ง คู่มือ VPS และ Forward Test) แม้เพจใน WordPress ยังเผยแพร่อยู่
 * - รายการแบบเลือกเพจที่เพจนั้นไม่ได้เผยแพร่ (ฉบับร่าง รอตรวจ ส่วนตัว): WordPress พิมพ์รายการแบบนี้ตามปกติ ผู้เข้าชมกดแล้วเจอหน้า 404
 * - ลิงก์กำหนดเองไปเพจของธีม (fenix_site_pages) ที่ยังไม่เผยแพร่
 */
function fenix_chrome_menu_item_dead( $item ) {
	if ( ! is_object( $item ) ) {
		return false;
	}
	$is_page = isset( $item->type, $item->object, $item->object_id ) && 'post_type' === $item->type && 'page' === $item->object;
	if ( $is_page && 'publish' !== get_post_status( (int) $item->object_id ) ) {
		return true;
	}
	$slug = fenix_chrome_menu_item_slug( $item );
	if ( '' === $slug ) {
		return false;
	}
	if ( function_exists( 'fenix_is_retired_page' ) && fenix_is_retired_page( $slug ) ) {
		return true;
	}
	if ( $is_page || ! function_exists( 'fenix_site_pages' ) ) {
		return false;
	}
	$pages = fenix_site_pages();
	return isset( $pages[ $slug ] ) && empty( $pages[ $slug ]['front'] ) && '' === fenix_chrome_page_url( $slug );
}

/**
 * ตัดรายการที่ปลายทางเปิดไม่ได้ออกจากรายการเมนู (เมนูบนเว็บจริงสร้างด้วยมือ และยังมีรายการของเพจที่ถอดออกแล้ว)
 * - หัวกลุ่มที่ปลายทางเปิดไม่ได้แต่ยังมีรายการย่อยเหลือ: คงหัวกลุ่มไว้ แล้วชี้ไปรายการย่อยตัวแรกที่เหลือ (ไม่งั้นทั้งกลุ่มจะหายไปด้วย)
 * - หัวกลุ่มที่เสียรายการย่อยไปจนเหลือรายการเดียวซึ่งชี้ที่เดียวกับหัวกลุ่ม: ตัดรายการย่อยนั้น (ไม่เหลือเมนูย่อยที่มีรายการเดียวซ้ำกับหัวกลุ่ม)
 * ไม่มีรายการที่ต้องตัด = คืนรายการเดิมโดยไม่แตะ · ไม่แก้เมนูในฐานข้อมูล เจ้าของยังเห็นและลบรายการเองได้ที่ รูปแบบ → เมนู
 */
function fenix_chrome_menu_prune( $items ) {
	if ( ! is_array( $items ) || ! $items ) {
		return $items;
	}
	$dead = array();
	foreach ( $items as $item ) {
		if ( isset( $item->ID ) && fenix_chrome_menu_item_dead( $item ) ) {
			$dead[ (int) $item->ID ] = true;
		}
	}
	if ( ! $dead ) {
		return $items;
	}
	/* รายการย่อยที่เหลือของแต่ละหัวกลุ่ม และหัวกลุ่มที่เสียรายการย่อยไปอย่างน้อยหนึ่งรายการ */
	$kids = array();
	$lost = array();
	foreach ( $items as $item ) {
		$parent = isset( $item->menu_item_parent ) ? (int) $item->menu_item_parent : 0;
		if ( ! $parent || ! isset( $item->ID ) ) {
			continue;
		}
		if ( isset( $dead[ (int) $item->ID ] ) ) {
			$lost[ $parent ] = true;
		} else {
			$kids[ $parent ][] = $item;
		}
	}
	$drop = array();
	$out  = array();
	foreach ( $items as $item ) {
		$id = isset( $item->ID ) ? (int) $item->ID : 0;
		if ( ! $id || ! isset( $dead[ $id ] ) ) {
			if ( $id && isset( $lost[ $id ], $kids[ $id ] ) && 1 === count( $kids[ $id ] ) && isset( $item->url, $kids[ $id ][0]->url ) && untrailingslashit( (string) $item->url ) === untrailingslashit( (string) $kids[ $id ][0]->url ) ) {
				$drop[ (int) $kids[ $id ][0]->ID ] = true;
			}
			$out[] = $item;
			continue;
		}
		if ( empty( $kids[ $id ] ) ) {
			continue;
		}
		$head            = clone $item;
		$head->url       = (string) $kids[ $id ][0]->url;
		$head->type      = 'custom';
		$head->object    = 'custom';
		$head->object_id = $id;
		$out[]           = $head;
		if ( 1 === count( $kids[ $id ] ) ) {
			$drop[ (int) $kids[ $id ][0]->ID ] = true;
		}
	}
	if ( $drop ) {
		$out = array_filter(
			$out,
			function ( $item ) use ( $drop ) {
				return ! isset( $item->ID ) || ! isset( $drop[ (int) $item->ID ] );
			}
		);
	}
	return array_values( $out );
}

/**
 * ใช้ fenix_chrome_menu_prune() กับทุกเมนูที่พิมพ์บนหน้าเว็บ (เมนูหลัก ลิ้นชักมือถือ และคอลัมน์ "หน้าในเว็บไซต์" ของ footer ที่อ่านเมนูเดียวกัน)
 * ไม่ทำในหลังบ้าน (หน้า รูปแบบ → เมนู ต้องเห็นทุกรายการ) REST cron และหน้าต่างพรีวิวของ Customizer (รายการที่ผู้ดูแลกำลังเพิ่มต้องเห็นได้)
 */
function fenix_chrome_menu_items( $items ) {
	if ( is_admin() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_customize_preview() ) {
		return $items;
	}
	return fenix_chrome_menu_prune( $items );
}
add_filter( 'wp_get_nav_menu_items', 'fenix_chrome_menu_items', 20 );

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
	$mail = fenix_chrome_mail();
	if ( $mail ) {
		$out[] = $mail;
	}
	return $out;
}

/**
 * ช่องทางอีเมล (contact_email ในหมวด 1 + ชื่อลิงก์ footer_email_text) · ยังไม่กรอก/รูปแบบผิด/ไม่มีชื่อลิงก์ = array ว่าง
 * ใช้ทั้งคอลัมน์ช่องทาง แถวไอคอนในลิ้นชัก และบล็อกติดต่อ · ที่อยู่อีเมลอยู่ใน href เท่านั้น (ไม่พิมพ์เป็นข้อความ จึงไม่โดนกฎตัวพิมพ์ใหญ่)
 *
 * @return array { icon, mailto, label } หรือ array()
 */
function fenix_chrome_mail() {
	$email = trim( (string) fenix_mod( 'contact_email' ) );
	$label = trim( (string) fenix_mod( 'footer_email_text' ) );
	if ( '' === $email || '' === $label || ! is_email( $email ) ) {
		return array();
	}
	return array(
		'icon'   => 'mail',
		'mailto' => $email,
		'label'  => $label,
	);
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
 * Footer · คอลัมน์คู่มือ: ทุกหน้าในกลุ่ม guide ของ fenix_site_pages() ที่เผยแพร่แล้ว (ลำดับตาม manifest)
 * เพิ่มหน้าคู่มือใหม่ใน manifest แล้วเผยแพร่ = ขึ้นคอลัมน์นี้เอง ไม่ต้องแก้เมนู
 *
 * หน้า /dashboard/ (กลุ่ม guide ใน manifest) ขึ้นคอลัมน์นี้เสมอเมื่อเผยแพร่แล้ว เว้นแต่คอลัมน์ "หน้าในเว็บไซต์" มีลิงก์หน้านั้นอยู่แล้ว
 * (เมนูสำรองที่มีรายการ "แดชบอร์ด" ระดับบน หรือเมนูที่เจ้าของเว็บตั้งเอง) · footer จึงมีลิงก์หน้านี้หนึ่งครั้งพอดี
 *
 * @param array[] $index รายการของคอลัมน์ "หน้าในเว็บไซต์" (fenix_chrome_index_items) ใช้กันลิงก์ซ้ำ
 * @return array[] { 0 => label, 1 => url, 2 => bool หน้าปัจจุบัน }
 */
function fenix_chrome_guide_items( $index = array() ) {
	$out = array();
	if ( ! function_exists( 'fenix_guide_links' ) ) {
		return $out;
	}
	$listed = array();
	foreach ( (array) $index as $entry ) {
		if ( isset( $entry[1] ) ) {
			$listed[ untrailingslashit( (string) $entry[1] ) ] = true;
		}
	}
	foreach ( fenix_guide_links( array( 'guide' ) ) as $slug => $link ) {
		$url = fenix_chrome_page_url( $slug );
		if ( '' === $url ) {
			continue;
		}
		if ( 'dashboard' === $slug && isset( $listed[ untrailingslashit( $url ) ] ) ) {
			continue;
		}
		$out[] = array( $link['label'], $url, is_page( $slug ) );
	}
	return $out;
}

/**
 * Footer · ลิงก์เข้าสู่ระบบแดชบอร์ดสำหรับสมาชิกเดิม (แถวเล็กใต้ดัชนี) · คืน HTML ที่ escape แล้ว หรือ '' เมื่อไม่มีปลายทาง
 * ปลายทางและข้อความลิงก์มาจากโมดูล dashboard (หมวด 1.1: ที่อยู่ + ข้อความปุ่มเข้าสู่ระบบ) · ปิดแดชบอร์ด / ลบข้อความปุ่ม = ''
 * ลิงก์เปิดแท็บใหม่ และนับคลิกเป็น dashboard_click (dash_mode = signin · dash_pos = footer)
 */
function fenix_chrome_member_link() {
	if ( ! function_exists( 'fenix_dashboard_button' ) ) {
		return '';
	}
	$link = (string) fenix_dashboard_button(
		'signin',
		array(
			'class' => 'ft-member-link',
			'pos'   => 'footer',
			'icon'  => false,
		)
	);
	if ( '' === $link ) {
		return '';
	}
	$label = trim( (string) fenix_mod( 'footer_member_label' ) );
	return '<p class="ft-member">' . fenix_icon( 'lock', 'icon icon-sm' ) . ( '' !== $label ? '<span class="ft-member-label">' . esc_html( $label ) . '</span>' : '' ) . $link . '</p>';
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
 * ปลายทางของช่องในบาร์ล่างมือถือ (ค่าจากช่อง "ลิงก์หรือ slug") · '' = ไม่พิมพ์ช่องนี้
 * slug ของเพจของธีมที่ถอดออกจากเว็บแล้ว หรือยังไม่เผยแพร่ = '' (ค่าที่เจ้าของเคยบันทึกไว้อาจยังชี้ไปเพจเดิม เช่น /how-to-install/)
 * ค่าอื่น (URL เต็ม #anchor เพจที่เจ้าของสร้างเอง) ผ่าน fenix_link_url() ตามเดิม
 */
function fenix_chrome_slot_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( preg_match( '#^/?([a-z0-9][a-z0-9\-]*)/?$#i', $value, $m ) && function_exists( 'fenix_site_pages' ) ) {
		$slug  = strtolower( $m[1] );
		$pages = fenix_site_pages( true );
		if ( isset( $pages[ $slug ] ) && empty( $pages[ $slug ]['front'] ) && '' === fenix_chrome_page_url( $slug ) ) {
			return '';
		}
	}
	return fenix_link_url( $value );
}

/**
 * บาร์ล่างมือถือ · 5 ช่อง (ช่องกลาง = ติดต่อ) + ช่องที่ active คิดฝั่ง PHP (ปิด JS ก็ถูก)
 *
 * @return array { items[], active (int, -1 = ไม่มี), x (float %) }
 */
function fenix_chrome_dock() {
	/* เรียก 2 ครั้งต่อหน้า (body_class + footer.php) · คิดครั้งเดียว */
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$slots = array(
		array(
			'label' => fenix_mod( 'mobile_nav_home_label' ),
			'url'   => fenix_chrome_slot_url( fenix_mod( 'mobile_nav_home_url' ) ),
			'icon'  => 'home',
		),
		array(
			'label' => fenix_mod( 'mobile_nav_test_label' ),
			'url'   => fenix_chrome_slot_url( fenix_mod( 'mobile_nav_test_url' ) ),
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
		'url'   => fenix_chrome_slot_url( fenix_mod( 'mobile_nav_price_url' ) ),
		'icon'  => 'tag',
	);
	/* ช่อง 5: ค่าเริ่มต้นไปหน้า /dashboard/ ของเว็บนี้ (ไอคอนจอ) · เจ้าของชี้ไปหน้าอื่น เช่นคู่มือ = ไอคอนหนังสือตามเดิม */
	$last_url = fenix_chrome_slot_url( fenix_mod( 'mobile_nav_install_url' ) );
	$slots[]  = array(
		'label' => fenix_mod( 'mobile_nav_install_label' ),
		'url'   => $last_url,
		'icon'  => preg_match( '#/dashboard/?(?:[?\#].*)?$#i', $last_url ) ? 'monitor' : 'book',
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

	/* หน้าอื่นของกลุ่มเดียวกันใน manifest (กลุ่ม test) → ช่อง "การทดสอบ" ติดไฟ */
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
	$dock  = array(
		'items'  => $items,
		'active' => $active,
		'x'      => $active >= 0 ? ( $active + 0.5 ) * ( 100 / $count ) : 50,
	);
	/* จำผลไว้เฉพาะเมื่อ WordPress รู้แล้วว่ากำลังแสดงหน้าไหน (ช่อง active ขึ้นกับหน้าปัจจุบัน) */
	if ( did_action( 'wp' ) ) {
		$cache = $dock;
	}
	return $dock;
}

/* ==============================================================
 * บล็อกติดต่อ (.ft-launch) · ตัววาดเดียวของทั้งเว็บ พิมพ์ได้หน้าละ 1 ครั้ง
 * ซ้าย: ป้ายเล็ก · หัวข้อ · คำอธิบาย · ปุ่มติดต่อ · OpenChat · อีเมล · QR
 * ขวา: รายการเตรียมข้อความแรก + เวลาตอบแชท
 * หน้าย่อยเรียกผ่าน fenix_line_cta() (ห่อด้วย .cta-console ท้ายเนื้อหา) · หน้าที่เหลือได้จาก footer.php
 * ============================================================== */

/**
 * รูป QR ของ LINE OA ที่มากับธีม และลิงก์ที่ QR นั้นพาไป (อ่านกลับจากไฟล์ด้วยตัวถอดรหัสแล้ว)
 * เปลี่ยนบัญชี LINE: วางไฟล์ใหม่ใน assets/img/brand/ (เลขรุ่นถัดไป) แล้วแก้ทั้ง 2 ค่านี้พร้อมกัน
 *
 * @return array { file, link (โฮสต์ + path ไม่มี https:// และ / ท้าย) }
 */
function fenix_chrome_qr_bundled() {
	return array(
		'file' => 'falcon-pro-line-qr-v1.png',
		'link' => 'lin.ee/9Ct7DTS',
	);
}

/**
 * URL ของรูป QR ที่มากับธีม · ไม่มีไฟล์ = '' (ค่าเริ่มต้นของ line_qr_image)
 */
function fenix_chrome_qr_bundled_url() {
	$qr = fenix_chrome_qr_bundled();
	if ( ! file_exists( get_template_directory() . '/assets/img/brand/' . $qr['file'] ) ) {
		return '';
	}
	return get_template_directory_uri() . '/assets/img/brand/' . $qr['file'];
}

/**
 * รูป QR ที่จะแสดงในบล็อกติดต่อ · '' = ไม่แสดง
 * - แสดงเฉพาะเมื่อปุ่มติดต่อเป็น LINE (ไม่มีลิงก์ LINE = ไม่มี QR)
 * - รูปที่มากับธีมพาไปลิงก์เดียวเท่านั้น จึงแสดงเมื่อ "ลิงก์ LINE OA" ในหมวด 1 เป็นลิงก์เดียวกัน
 *   (กัน QR พาลูกค้าไปคนละบัญชีกับปุ่ม เมื่อเจ้าของเปลี่ยนลิงก์ LINE แต่ยังไม่ได้อัปโหลด QR ใหม่)
 * - รูปที่เจ้าของอัปโหลดเองแสดงเสมอ
 */
function fenix_chrome_qr_url() {
	$target = fenix_contact_target();
	if ( ! $target['is_line'] ) {
		return '';
	}
	$qr = trim( (string) fenix_mod( 'line_qr_image' ) );
	if ( ! fenix_chrome_url_ok( $qr ) ) {
		return '';
	}
	if ( $qr === fenix_chrome_qr_bundled_url() ) {
		$own   = fenix_chrome_qr_bundled();
		$parts = wp_parse_url( $target['url'] );
		$host  = isset( $parts['host'] ) ? strtolower( (string) preg_replace( '/^www\./i', '', (string) $parts['host'] ) ) : '';
		$path  = isset( $parts['path'] ) ? rtrim( (string) $parts['path'], '/' ) : '';
		if ( $host . $path !== $own['link'] ) {
			return '';
		}
	}
	return $qr;
}

/**
 * บล็อกติดต่อ · คืน true เมื่อพิมพ์ · false เมื่อหน้านี้พิมพ์ไปแล้ว หรือไม่มีช่องทางให้ติดต่อเลย (แอดมินยังเห็นบล็อกพร้อมคำแนะนำ)
 *
 * @param array $args {
 *     @type string $label ป้ายเล็กเหนือหัวข้อ
 *     @type string $title หัวข้อ (H2)
 *     @type string $sub   คำอธิบาย
 *     @type string $pos   ตำแหน่งสำหรับนับคลิก (data-line-pos ของปุ่มติดต่อ · ปุ่ม OpenChat = {pos}-openchat)
 *     @type bool   $wrap  true = ห่อด้วย .cta-console#cta + .container (ใช้ท้ายเนื้อหา) · false = การ์ดอย่างเดียว (ใน footer)
 * }
 */
function fenix_chrome_launch( $args = array() ) {
	if ( ! empty( $GLOBALS['fenix_chrome_cta_done'] ) ) {
		return false;
	}
	$args = wp_parse_args(
		$args,
		array(
			'label' => '',
			'title' => '',
			'sub'   => '',
			'pos'   => 'footer',
			'wrap'  => false,
		)
	);

	$target   = fenix_contact_target();
	$is_admin = current_user_can( 'edit_theme_options' );
	$oc_url   = trim( (string) fenix_mod( 'line_openchat_url' ) );
	$oc_text  = trim( (string) fenix_mod( 'line_openchat_text' ) );
	$openchat = ( fenix_chrome_url_ok( $oc_url ) && '' !== $oc_text ) ? $oc_url : '';
	$mail     = fenix_chrome_mail();
	if ( '' === $target['url'] && '' === $openchat && ! $mail && ! $is_admin ) {
		return false;
	}
	$GLOBALS['fenix_chrome_cta_done'] = true;

	$pos     = sanitize_key( (string) $args['pos'] );
	$pos     = '' !== $pos ? $pos : 'footer';
	$wrap    = ! empty( $args['wrap'] );
	$id      = $wrap ? 'cta-console-title' : 'ft-launch-title';
	/* ป้ายเล็กเหนือหัวข้อของบล็อกติดต่อ: พิมพ์เฉพาะเมื่อเปิด "คำเล็กเหนือหัวข้อ" (show_kickers · หมวด 1) · ค่าเริ่มต้นปิด = H2 เป็นบรรทัดแรกของบล็อก */
	$label   = function_exists( 'fenix_kicker' ) ? fenix_kicker( $args['label'] ) : '';
	$title   = trim( (string) $args['title'] );
	$sub     = trim( (string) $args['sub'] );
	$qr      = fenix_chrome_qr_url();
	$qr_alt  = (string) fenix_mod( 'footer_line_qr_alt' );
	$qr_text = trim( (string) fenix_mod( 'footer_qr_text' ) );
	$qr_open = trim( (string) fenix_mod( 'footer_qr_toggle_text' ) );
	$prep    = fenix_lines( fenix_mod( 'footer_prep_items' ) );
	$hours   = fenix_lines( fenix_mod( 'footer_hours_text' ) );
	$side    = $prep || $hours;
	?>
	<?php if ( $wrap ) : ?>
	<div class="cta-console" id="cta">
		<div class="container">
	<?php endif; ?>
	<section class="ft-launch<?php echo $side ? '' : ' ft-launch--solo'; ?>"<?php echo '' !== $title ? ' aria-labelledby="' . esc_attr( $id ) . '"' : ''; ?>>
		<div class="ft-launch-main">
			<?php if ( '' !== $label ) : ?>
				<p class="chrome-label"><span class="chrome-dot" aria-hidden="true"></span><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h2 class="ft-launch-title" id="<?php echo esc_attr( $id ); ?>"><?php echo fenix_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $sub ) : ?>
				<p class="ft-launch-sub"><?php echo fenix_text( $sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></p>
			<?php endif; ?>

			<?php /* แถวปุ่ม: ปุ่มติดต่อ (ไม่มีปลายทาง = ไม่พิมพ์ · แอดมินเห็นคำแนะนำ) + OpenChat · ไม่มีทั้งคู่และไม่ใช่แอดมิน = ไม่มีแถวนี้ (เหลือลิงก์อีเมล) */ ?>
			<?php if ( '' !== $target['url'] || '' !== $openchat || $is_admin ) : ?>
			<div class="ft-actions">
				<?php
				fenix_contact_button(
					array(
						'class' => 'btn btn-fire btn-lg',
						'pos'   => $pos,
					)
				);
				?>
				<?php if ( '' !== $openchat ) : ?>
					<a class="btn btn-ghost btn-lg ft-openchat" href="<?php echo esc_url( $openchat ); ?>" target="_blank" rel="noopener" data-line-pos="<?php echo esc_attr( $pos . '-openchat' ); ?>">
						<?php echo fenix_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $oc_text ); ?></span>
					</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ( $mail ) : ?>
				<p class="ft-mail">
					<a href="<?php echo fenix_chrome_social_href( $mail ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?>">
						<?php echo fenix_icon( 'mail', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $mail['label'] ); ?></span>
					</a>
				</p>
			<?php endif; ?>

			<?php if ( '' !== $qr ) : ?>
				<?php /* จอกว้าง: QR เปิดไว้ให้ยกมือถือสแกนจากจอ · จอแคบ: พับใน <details> (กดปุ่ม LINE ได้ตรง ๆ) · CSS แสดงทีละแบบ */ ?>
				<div class="ft-qr">
					<img src="<?php echo esc_url( $qr ); ?>" alt="<?php echo esc_attr( $qr_alt ); ?>" width="132" height="132" loading="lazy" decoding="async">
					<?php if ( '' !== $qr_text ) : ?>
						<p class="ft-qr-text"><?php echo fenix_icon( 'qr', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_text( $qr_text ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></span></p>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $qr_open ) : ?>
					<details class="ft-qr-mobile">
						<summary><?php echo fenix_icon( 'qr', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $qr_open ); ?></span></summary>
						<img src="<?php echo esc_url( $qr ); ?>" alt="<?php echo esc_attr( $qr_alt ); ?>" width="200" height="200" loading="lazy" decoding="async">
					</details>
				<?php endif; ?>
			<?php elseif ( $target['is_line'] && $is_admin ) : ?>
				<p class="admin-hint">แอดมินเท่านั้นที่เห็นบรรทัดนี้: บล็อกติดต่อยังไม่มีรูป QR ที่ตรงกับลิงก์ LINE ปัจจุบัน ให้เปิด ปรับแต่ง หมวด 1 แล้วอัปโหลดรูปที่ช่อง "รูป QR Code LINE OA"</p>
			<?php endif; ?>
		</div>

		<?php if ( $side ) : ?>
		<div class="ft-launch-side">
			<?php if ( $prep ) : ?>
				<div class="ft-prep">
					<?php if ( '' !== trim( (string) fenix_mod( 'footer_prep_title' ) ) ) : ?>
						<h3 class="ft-side-title"><?php echo esc_html( fenix_mod( 'footer_prep_title' ) ); ?></h3>
					<?php endif; ?>
					<?php if ( '' !== trim( (string) fenix_mod( 'footer_prep_text' ) ) ) : ?>
						<p class="ft-side-text"><?php echo esc_html( fenix_mod( 'footer_prep_text' ) ); ?></p>
					<?php endif; ?>
					<ol class="ft-prep-list">
						<?php foreach ( $prep as $fenix_i => $fenix_item ) : ?>
							<li><span class="ft-prep-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_i + 1 ) ); ?></span><span><?php echo esc_html( $fenix_item ); ?></span></li>
						<?php endforeach; ?>
					</ol>
				</div>
			<?php endif; ?>
			<?php if ( $hours ) : ?>
				<div class="ft-hours">
					<h3 class="ft-side-title"><?php echo fenix_icon( 'clock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'footer_hours_title' ) ); ?></h3>
					<p class="ft-side-text"><?php echo implode( '<br>', array_map( 'esc_html', $hours ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- each line escaped ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</section>
	<?php if ( $wrap ) : ?>
		</div>
	</div>
	<?php endif; ?>
	<?php
	return true;
}

/**
 * บล็อกติดต่อท้ายหน้าย่อย · แทน fenix_line_cta() เดิม (add_action 'fenix_line_cta')
 * หัวข้อ/คำอธิบายมาจากหน้านั้น (ว่าง = contact_title / contact_text ในหมวด 1)
 */
function fenix_chrome_line_cta( $title = '', $sub = '' ) {
	fenix_chrome_launch(
		array(
			'label' => fenix_mod( 'chrome_cta_label' ),
			'title' => '' !== trim( (string) $title ) ? $title : fenix_mod( 'contact_title' ),
			'sub'   => '' !== trim( (string) $sub ) ? $sub : fenix_mod( 'contact_text' ),
			'pos'   => 'page-cta',
			'wrap'  => true,
		)
	);
}
add_action( 'fenix_line_cta', 'fenix_chrome_line_cta', 10, 2 );

/**
 * หน้านี้พิมพ์บล็อกติดต่อไปแล้วหรือยัง (footer.php ใช้ตัดสินว่าจะต่อดัชนีลิงก์ชิดกับบล็อกท้ายเนื้อหาหรือไม่)
 */
function fenix_chrome_launch_done() {
	return ! empty( $GLOBALS['fenix_chrome_cta_done'] );
}

/**
 * บล็อกติดต่อในแถวแรกของ footer · เฉพาะหน้าที่ยังไม่มีบล็อกติดต่อ (หน้าแรก หน้ารวมบทความ เพจเอกสาร 404 ค้นหา)
 * ปิดได้ด้วยฟิลเตอร์ fenix_chrome_footer_contact
 */
function fenix_chrome_footer_contact() {
	if ( fenix_chrome_launch_done() || ! apply_filters( 'fenix_chrome_footer_contact', true ) ) {
		return false;
	}
	$title = trim( (string) fenix_mod( 'footer_headline' ) );
	$sub   = trim( (string) fenix_mod( 'footer_sub' ) );
	return fenix_chrome_launch(
		array(
			'label' => fenix_mod( 'footer_console_label' ),
			'title' => '' !== $title ? $title : fenix_mod( 'contact_title' ),
			'sub'   => '' !== $sub ? $sub : fenix_mod( 'contact_text' ),
			'pos'   => 'footer',
			'wrap'  => false,
		)
	);
}

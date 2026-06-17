<?php
/**
 * FALCON PRO EA · Customizer (หน้า "ปรับแต่ง")
 * ทุกข้อความ รูปภาพ ลิงก์ และการเปิดและปิด section แก้ได้จากที่นี่
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------- Sanitize helpers ---------------- */

function fenix_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

function fenix_sanitize_pricing_mode( $value ) {
	return in_array( $value, array( 'price', 'contact' ), true ) ? $value : 'contact';
}

/* ---------------- Register ---------------- */

function fenix_customize_register( $wp_customize ) {

	$d = fenix_defaults();

	$wp_customize->add_panel(
		'fenix_panel',
		array(
			'title'       => 'FALCON PRO · ตั้งค่าหน้าเว็บ',
			'description' => 'แก้ไขข้อความ รูปภาพ ลิงก์ และเปิดและปิดแต่ละส่วนของหน้าแรกได้ทั้งหมดจากเมนูนี้ กด "เผยแพร่" เพื่อบันทึก',
			'priority'    => 10,
		)
	);

	/*
	 * โครงสร้าง: section_id => [ title, description, fields ]
	 * field: id => [ label, type, (choices/description) ]
	 */
	$sections = array(

		'fenix_general' => array(
			'title'       => '1) ช่องทางติดต่อ (สำคัญ ตั้งค่าก่อน)',
			'description' => 'ลิงก์ LINE จะถูกใช้กับปุ่มทุกปุ่มบนเว็บโดยอัตโนมัติ',
			'fields'      => array(
				'line_url'        => array( 'ลิงก์ LINE OA', 'url', 'เช่น https://lin.ee/xxxxx หรือ https://line.me/R/ti/p/@xxxxx' ),
				'facebook_url'    => array( 'ลิงก์ Facebook Page (ถ้ามี)', 'url' ),
				'contact_email'   => array( 'อีเมลติดต่อ (ถ้ามี)', 'text' ),
				'show_float_line' => array( 'แสดงปุ่ม LINE ลอยมุมขวาล่าง', 'checkbox' ),
				'float_line_text' => array( 'ข้อความบนปุ่ม LINE ลอย', 'text' ),
				'show_language_switcher' => array( 'แสดงตัวสลับภาษาในเมนูบน', 'checkbox' ),
				'language_fallback_items' => array( 'รายการภาษาสำรอง (code|ธง|ป้ายสั้น|ชื่อภาษา)', 'textarea' ),
			),
		),

		'fenix_hero' => array(
			'title'  => '2) Hero · ส่วนแรกของหน้า',
			'fields' => array(
				'show_hero'      => array( 'แสดงส่วนนี้', 'checkbox' ),
				'hero_badge'     => array( 'ป้ายเล็กเหนือชื่อ', 'text' ),
				'hero_title'     => array( 'ชื่อหลัก (Headline)', 'text' ),
				'hero_subtitle'  => array( 'ประโยคหลัก (Sub headline)', 'text' ),
				'hero_desc'      => array( 'คำอธิบายสั้น', 'textarea' ),
				'hero_btn1_text' => array( 'ข้อความปุ่มหลัก (ลิงก์ไป LINE)', 'text' ),
				'hero_btn2_text' => array( 'ข้อความปุ่มรอง (เลื่อนไปส่วนรู้จักระบบ)', 'text' ),
				'hero_note'      => array( 'ข้อความเตือนความเสี่ยงใต้ปุ่ม', 'text' ),
				'hero_image'     => array( 'ภาพประกอบ Hero (ไม่ใส่ = ใช้โลโก้)', 'image', 'แนะนำภาพ Dashboard หรือหน้าจอ MT5 ขนาดกว้างอย่างน้อย 900px' ),
			),
		),

		'fenix_pain' => array(
			'title'  => '3) ปัญหาของนักเทรด',
			'fields' => array(
				'show_pain'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'pain_title'    => array( 'หัวข้อ', 'text' ),
				'pain_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pain1_title'   => array( 'การ์ด 1 · หัวข้อ', 'text' ),
				'pain1_desc'    => array( 'การ์ด 1 · รายละเอียด', 'textarea' ),
				'pain2_title'   => array( 'การ์ด 2 · หัวข้อ', 'text' ),
				'pain2_desc'    => array( 'การ์ด 2 · รายละเอียด', 'textarea' ),
				'pain3_title'   => array( 'การ์ด 3 · หัวข้อ', 'text' ),
				'pain3_desc'    => array( 'การ์ด 3 · รายละเอียด', 'textarea' ),
				'pain4_title'   => array( 'การ์ด 4 · หัวข้อ', 'text' ),
				'pain4_desc'    => array( 'การ์ด 4 · รายละเอียด', 'textarea' ),
			),
		),

		'fenix_about' => array(
			'title'  => '4) FALCON PRO EA คืออะไร',
			'fields' => array(
				'show_about'  => array( 'แสดงส่วนนี้', 'checkbox' ),
				'about_title' => array( 'หัวข้อ', 'text' ),
				'about_text'  => array( 'เนื้อหา (เว้นบรรทัด = ขึ้นย่อหน้าใหม่)', 'textarea' ),
			),
		),

		'fenix_features' => array(
			'title'  => '5) จุดเด่นของระบบ (6 ข้อ)',
			'fields' => array(
				'show_features'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'features_title'    => array( 'หัวข้อ', 'text' ),
				'features_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'feat1_title'       => array( 'จุดเด่น 1 · หัวข้อ', 'text' ),
				'feat1_desc'        => array( 'จุดเด่น 1 · รายละเอียด', 'textarea' ),
				'feat2_title'       => array( 'จุดเด่น 2 · หัวข้อ', 'text' ),
				'feat2_desc'        => array( 'จุดเด่น 2 · รายละเอียด', 'textarea' ),
				'feat3_title'       => array( 'จุดเด่น 3 · หัวข้อ', 'text' ),
				'feat3_desc'        => array( 'จุดเด่น 3 · รายละเอียด', 'textarea' ),
				'feat4_title'       => array( 'จุดเด่น 4 · หัวข้อ', 'text' ),
				'feat4_desc'        => array( 'จุดเด่น 4 · รายละเอียด', 'textarea' ),
				'feat5_title'       => array( 'จุดเด่น 5 · หัวข้อ', 'text' ),
				'feat5_desc'        => array( 'จุดเด่น 5 · รายละเอียด', 'textarea' ),
				'feat6_title'       => array( 'จุดเด่น 6 · หัวข้อ', 'text' ),
				'feat6_desc'        => array( 'จุดเด่น 6 · รายละเอียด', 'textarea' ),
			),
		),

		'fenix_gallery' => array(
			'title'       => '6) ภาพ Dashboard / ระบบจริง',
			'description' => 'ถ้ายังไม่อัปโหลดภาพ ธีมจะแสดงกรอบตัวอย่าง (ระบุว่าเป็นภาพประกอบ) ให้อัตโนมัติ',
			'fields'      => array(
				'show_gallery'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'gallery_title'    => array( 'หัวข้อ', 'text' ),
				'gallery_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'gallery_img1'     => array( 'ภาพที่ 1', 'image' ),
				'gallery_cap1'     => array( 'คำบรรยายภาพที่ 1', 'text' ),
				'gallery_img2'     => array( 'ภาพที่ 2', 'image' ),
				'gallery_cap2'     => array( 'คำบรรยายภาพที่ 2', 'text' ),
				'gallery_img3'     => array( 'ภาพที่ 3', 'image' ),
				'gallery_cap3'     => array( 'คำบรรยายภาพที่ 3', 'text' ),
				'gallery_img4'     => array( 'ภาพที่ 4', 'image' ),
				'gallery_cap4'     => array( 'คำบรรยายภาพที่ 4', 'text' ),
				'gallery_note'     => array( 'หมายเหตุท้ายส่วน', 'text' ),
			),
		),

		'fenix_steps' => array(
			'title'  => '7) วิธีเริ่มใช้งาน (4 ขั้นตอน)',
			'fields' => array(
				'show_steps'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'steps_kicker'   => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
				'steps_title'    => array( 'หัวข้อ', 'text' ),
				'steps_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'step1_title'    => array( 'ขั้นตอน 1 · หัวข้อ', 'text' ),
				'step1_desc'     => array( 'ขั้นตอน 1 · รายละเอียด', 'textarea' ),
				'step2_title'    => array( 'ขั้นตอน 2 · หัวข้อ', 'text' ),
				'step2_desc'     => array( 'ขั้นตอน 2 · รายละเอียด', 'textarea' ),
				'step3_title'    => array( 'ขั้นตอน 3 · หัวข้อ', 'text' ),
				'step3_desc'     => array( 'ขั้นตอน 3 · รายละเอียด', 'textarea' ),
				'step4_title'    => array( 'ขั้นตอน 4 · หัวข้อ', 'text' ),
				'step4_desc'     => array( 'ขั้นตอน 4 · รายละเอียด', 'textarea' ),
			),
		),

		'fenix_perf' => array(
			'title'       => '8) ผลการทดสอบ / Performance',
			'description' => 'กรอกข้อมูลจริงจากการทดสอบเท่านั้น และอย่าลบข้อความ Disclaimer',
			'fields'      => array(
				'show_perf'          => array( 'แสดงส่วนนี้', 'checkbox' ),
				'perf_kicker'        => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
				'perf_title'         => array( 'หัวข้อ', 'text' ),
				'perf_subtitle'      => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'stat1_label'        => array( 'ข้อมูล 1 · หัวข้อ', 'text' ),
				'stat1_value'        => array( 'ข้อมูล 1 · ค่า', 'text' ),
				'stat2_label'        => array( 'ข้อมูล 2 · หัวข้อ', 'text' ),
				'stat2_value'        => array( 'ข้อมูล 2 · ค่า', 'text' ),
				'stat3_label'        => array( 'ข้อมูล 3 · หัวข้อ', 'text' ),
				'stat3_value'        => array( 'ข้อมูล 3 · ค่า', 'text' ),
				'stat4_label'        => array( 'ข้อมูล 4 · หัวข้อ', 'text' ),
				'stat4_value'        => array( 'ข้อมูล 4 · ค่า', 'text' ),
				'stat5_label'        => array( 'ข้อมูล 5 · หัวข้อ', 'text' ),
				'stat5_value'        => array( 'ข้อมูล 5 · ค่า', 'text' ),
				'stat6_label'        => array( 'ข้อมูล 6 · หัวข้อ', 'text' ),
				'stat6_value'        => array( 'ข้อมูล 6 · ค่า', 'text' ),
				'perf_image'         => array( 'ภาพกราฟผลทดสอบ (Backtest/Forward)', 'image' ),
				'perf_image_caption' => array( 'คำบรรยายภาพ', 'text' ),
				'verified_link_label' => array( 'ข้อความปุ่มผลเรียลไทม์ (ถ้ามี)', 'text' ),
				'verified_link_url'   => array( 'ลิงก์ผลที่ตรวจสอบได้ เช่น Myfxbook / FXBlue (แสดงปุ่มเมื่อกรอกเท่านั้น)', 'url' ),
				'perf_note'          => array( 'หมายเหตุเงื่อนไขการทดสอบ', 'textarea' ),
				'perf_disclaimer'    => array( 'ข้อความ Disclaimer (จำเป็นต้องมี)', 'textarea' ),
			),
		),

		'fenix_fit' => array(
			'title'  => '9) เหมาะกับใคร / ไม่เหมาะกับใคร',
			'fields' => array(
				'show_fit'       => array( 'แสดงส่วนนี้', 'checkbox' ),
				'fit_title'      => array( 'หัวข้อ', 'text' ),
				'fit_subtitle'   => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'fit_good_title' => array( 'หัวข้อฝั่ง "เหมาะกับ"', 'text' ),
				'fit_good_items' => array( 'รายการฝั่ง "เหมาะกับ" (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'fit_bad_title'  => array( 'หัวข้อฝั่ง "ไม่เหมาะกับ"', 'text' ),
				'fit_bad_items'  => array( 'รายการฝั่ง "ไม่เหมาะกับ" (บรรทัดละ 1 ข้อ)', 'textarea' ),
			),
		),

		'fenix_pricing' => array(
			'title'       => '10) แพ็กเกจราคา',
			'description' => 'เลือกได้ว่าจะโชว์ราคา หรือให้สอบถามราคาทาง LINE',
			'fields'      => array(
				'show_pricing'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'pricing_title'    => array( 'หัวข้อ', 'text' ),
				'pricing_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pricing_mode'     => array(
					'รูปแบบการแสดงราคา',
					'radio',
					array(
						'price'   => 'แสดงตัวเลขราคา',
						'contact' => 'ไม่แสดงราคา · ให้สอบถามทาง LINE',
					),
				),
				'pkg1_name'        => array( 'แพ็กเกจ 1 · ชื่อ', 'text' ),
				'pkg1_tag'         => array( 'แพ็กเกจ 1 · คำอธิบายสั้น', 'text' ),
				'pkg1_price'       => array( 'แพ็กเกจ 1 · ราคา', 'text' ),
				'pkg1_period'      => array( 'แพ็กเกจ 1 · หน่วย/ระยะเวลา', 'text' ),
				'pkg1_features'    => array( 'แพ็กเกจ 1 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg1_featured'    => array( 'แพ็กเกจ 1 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pkg2_name'        => array( 'แพ็กเกจ 2 · ชื่อ', 'text' ),
				'pkg2_tag'         => array( 'แพ็กเกจ 2 · คำอธิบายสั้น', 'text' ),
				'pkg2_price'       => array( 'แพ็กเกจ 2 · ราคา', 'text' ),
				'pkg2_period'      => array( 'แพ็กเกจ 2 · หน่วย/ระยะเวลา', 'text' ),
				'pkg2_features'    => array( 'แพ็กเกจ 2 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg2_featured'    => array( 'แพ็กเกจ 2 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pkg3_name'        => array( 'แพ็กเกจ 3 · ชื่อ', 'text' ),
				'pkg3_tag'         => array( 'แพ็กเกจ 3 · คำอธิบายสั้น', 'text' ),
				'pkg3_price'       => array( 'แพ็กเกจ 3 · ราคา', 'text' ),
				'pkg3_period'      => array( 'แพ็กเกจ 3 · หน่วย/ระยะเวลา', 'text' ),
				'pkg3_features'    => array( 'แพ็กเกจ 3 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg3_featured'    => array( 'แพ็กเกจ 3 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pricing_btn_text' => array( 'ข้อความปุ่มบนการ์ดราคา', 'text' ),
				'pricing_note'     => array( 'หมายเหตุท้ายส่วนราคา', 'text' ),
				'show_pricing_home'  => array( 'แสดงตัวอย่างแพ็กเกจบนหน้าแรก', 'checkbox' ),
				'pricing_home_title' => array( 'หน้าแรก · หัวข้อแพ็กเกจ', 'text' ),
				'pricing_home_sub'   => array( 'หน้าแรก · คำอธิบายแพ็กเกจ', 'textarea' ),
			),
		),

		'fenix_reviews' => array(
			'title'       => '11) รีวิวลูกค้า',
			'description' => 'สำคัญ: ใช้รีวิวจริงจากลูกค้าเท่านั้น ห้ามแต่งรีวิว และหลีกเลี่ยงรีวิวแนวการันตีกำไร เมื่อมีรีวิวจริงแล้วค่อยติ๊ก "แสดงส่วนนี้"',
			'fields'      => array(
				'show_reviews'     => array( 'แสดงส่วนนี้ (เปิดเมื่อมีรีวิวจริง)', 'checkbox' ),
				'reviews_title'    => array( 'หัวข้อ', 'text' ),
				'reviews_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'rev1_text'        => array( 'รีวิว 1 · ข้อความ', 'textarea' ),
				'rev1_name'        => array( 'รีวิว 1 · ชื่อผู้รีวิว', 'text' ),
				'rev2_text'        => array( 'รีวิว 2 · ข้อความ', 'textarea' ),
				'rev2_name'        => array( 'รีวิว 2 · ชื่อผู้รีวิว', 'text' ),
				'rev3_text'        => array( 'รีวิว 3 · ข้อความ', 'textarea' ),
				'rev3_name'        => array( 'รีวิว 3 · ชื่อผู้รีวิว', 'text' ),
			),
		),

		'fenix_faq' => array(
			'title'       => '12) FAQ คำถามที่พบบ่อย',
			'description' => 'มีช่องให้ 10 ข้อ ข้อไหนเว้นว่างไว้จะไม่แสดงผล',
			'fields'      => array(
				'show_faq'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'faq_title'    => array( 'หัวข้อ', 'text' ),
				'faq_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			),
		),

		'fenix_risk' => array(
			'title'       => '13) คำเตือนความเสี่ยง',
			'description' => 'ส่วนนี้สำคัญต่อความน่าเชื่อถือของแบรนด์ ไม่แนะนำให้ปิด',
			'fields'      => array(
				'show_risk'  => array( 'แสดงส่วนนี้', 'checkbox' ),
				'risk_title' => array( 'หัวข้อ', 'text' ),
				'risk_text'  => array( 'ข้อความคำเตือน', 'textarea' ),
			),
		),

		'fenix_cta' => array(
			'title'  => '14) CTA ปิดท้าย',
			'fields' => array(
				'show_cta'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'show_mid_cta'  => array( 'แสดงแถบทัก LINE คั่นกลางหน้าแรก', 'checkbox' ),
				'mid_cta_title' => array( 'แถบกลางหน้า · หัวข้อ', 'text' ),
				'mid_cta_text'  => array( 'แถบกลางหน้า · คำอธิบาย', 'textarea' ),
				'cta_title'    => array( 'หัวข้อ', 'text' ),
				'cta_subtitle' => array( 'คำอธิบาย', 'textarea' ),
				'cta_btn_text' => array( 'ข้อความปุ่ม', 'text' ),
			),
		),

		'fenix_footer' => array(
			'title'  => '15) Footer ท้ายเว็บ',
			'fields' => array(
				'footer_kicker'       => array( 'ข้อความเล็กเหนือ CTA footer', 'text' ),
				'footer_cta_title'    => array( 'หัวข้อ CTA ใน footer', 'text' ),
				'footer_cta_text'     => array( 'คำอธิบาย CTA ใน footer', 'textarea' ),
				'footer_line_text'    => array( 'ข้อความปุ่ม LINE ใน footer', 'text' ),
				'footer_facebook_text' => array( 'ข้อความลิงก์ Facebook ใน footer', 'text' ),
				'footer_email_text'    => array( 'ข้อความลิงก์ Email ใน footer', 'text' ),
				'footer_prep_title'    => array( 'หัวข้อการ์ดเตรียมข้อมูลก่อนทัก LINE', 'text' ),
				'footer_prep_text'     => array( 'คำอธิบายการ์ดเตรียมข้อมูลก่อนทัก LINE', 'textarea' ),
				'footer_prep_items'    => array( 'รายการที่ควรเตรียม (บรรทัดละ 1 รายการ)', 'textarea' ),
				'footer_tagline'      => array( 'คำโปรยใต้โลโก้', 'textarea' ),
				'footer_trust_items'  => array( 'ป้ายความน่าเชื่อถือ (บรรทัดละ 1 รายการ)', 'textarea' ),
				'footer_risk_link'    => array( 'ข้อความลิงก์คำเตือนความเสี่ยง', 'text' ),
			),
		),

		'fenix_mobile_nav' => array(
			'title'       => '16) เมนูลัดมือถือด้านล่าง',
			'description' => 'เมนูลัดด้านล่างบนมือถือ ใช้สำหรับพาผู้เข้าชมไปยังหน้าหลักที่สำคัญและปุ่มทัก LINE',
			'fields'      => array(
				'show_mobile_nav'        => array( 'แสดงเมนูลัดด้านล่างบนมือถือ', 'checkbox' ),
				'mobile_nav_home_label'  => array( 'เมนู 1 · ชื่อ', 'text' ),
				'mobile_nav_home_url'    => array( 'เมนู 1 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_test_label'  => array( 'เมนู 2 · ชื่อ', 'text' ),
				'mobile_nav_test_url'    => array( 'เมนู 2 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_price_label'   => array( 'เมนู 3 · ชื่อ', 'text' ),
				'mobile_nav_price_url'     => array( 'เมนู 3 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_install_label' => array( 'เมนู 4 · ชื่อ', 'text' ),
				'mobile_nav_install_url'   => array( 'เมนู 4 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_line_label'    => array( 'เมนู 5 · ชื่อปุ่ม LINE', 'text' ),
			),
		),
	);

	$sections['fenix_seo'] = array(
		'title'       => '17) SEO / แชร์ลิงก์',
		'description' => 'รูปและคำโปรยเวลาแชร์ลิงก์ใน LINE/Facebook (Open Graph) บทความจะใช้ภาพหน้าปก (Featured image) อัตโนมัติ',
		'fields'      => array(
			'og_default_image'       => array( 'รูปสำหรับแชร์ (แนะนำ 1200×630px)', 'image' ),
			'og_default_description' => array( 'คำโปรยเวลาแชร์หน้าหลัก/หน้าที่ไม่มีภาพ', 'textarea' ),
		),
	);

	$sections['fenix_cookie'] = array(
		'title'       => '18) คุกกี้ / Tracking',
		'description' => 'แถบขอความยินยอมคุกกี้ และโค้ดติดตาม (Google Analytics / Facebook Pixel จะโหลดเฉพาะหลังผู้ใช้กด "ยอมรับ" เท่านั้น)',
		'fields'      => array(
			'show_cookie_consent' => array( 'แสดงแถบขอความยินยอมคุกกี้', 'checkbox' ),
			'cookie_consent_text' => array( 'ข้อความบนแถบคุกกี้', 'textarea' ),
			'ga_measurement_id'   => array( 'Google Analytics 4 ID (เช่น G-XXXXXXXXXX)', 'text' ),
			'fb_pixel_id'         => array( 'Facebook Pixel ID', 'text' ),
		),
	);

	/* เพิ่ม FAQ 10 ข้อเข้า section FAQ */
	for ( $i = 1; $i <= 10; $i++ ) {
		$sections['fenix_faq']['fields'][ 'faq' . $i . '_q' ] = array( 'คำถามข้อ ' . $i, 'text' );
		$sections['fenix_faq']['fields'][ 'faq' . $i . '_a' ] = array( 'คำตอบข้อ ' . $i, 'textarea' );
	}

	/* ===================================================
	 * หมวดเพิ่มเติมสำหรับหน้าย่อย (multipage)
	 * =================================================== */

	$sections['fenix_home'] = array(
		'title'       => '19) หน้าแรก · ไฮไลต์ & การ์ดนำทาง',
		'description' => 'แถบไฮไลต์ใต้ Hero และการ์ด 5 ใบที่ลิงก์ไปยังหน้าย่อย ลิงก์ตั้งค่าตาม slug มาตรฐานแล้ว แก้ได้หากใช้ slug อื่น',
		'fields'      => array(
			'show_highlight'   => array( 'แสดงแถบไฮไลต์ใต้ Hero', 'checkbox' ),
			'highlight1'       => array( 'ไฮไลต์ 1', 'text' ),
			'highlight2'       => array( 'ไฮไลต์ 2', 'text' ),
			'highlight3'       => array( 'ไฮไลต์ 3', 'text' ),
			'highlight4'       => array( 'ไฮไลต์ 4', 'text' ),
			'show_live_status'     => array( 'แสดงแถบสถานะเคลื่อนไหวใต้ Hero', 'checkbox' ),
			'live_status_kicker'   => array( 'ป้ายแถบสถานะ', 'text' ),
			'live_status_items'    => array( 'ข้อความในแถบสถานะ (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'show_control_center'  => array( 'แสดงส่วน Control Center หน้าแรก', 'checkbox' ),
			'control_kicker'       => array( 'Control Center · ป้ายเล็ก', 'text' ),
			'control_title'        => array( 'Control Center · หัวข้อ', 'text' ),
			'control_subtitle'     => array( 'Control Center · คำอธิบาย', 'textarea' ),
			'control_panel_title'  => array( 'แผงสถานะ · หัวข้อ', 'text' ),
			'control_panel_status' => array( 'แผงสถานะ · สถานะหลัก', 'text' ),
			'control_badge'        => array( 'แผงสถานะ · ป้ายด้านขวา', 'text' ),
			'control_panel_text'   => array( 'แผงสถานะ · รายละเอียด', 'textarea' ),
			'control_metric1_label' => array( 'ข้อมูลย่อย 1 · หัวข้อ', 'text' ),
			'control_metric1_value' => array( 'ข้อมูลย่อย 1 · ค่า', 'text' ),
			'control_metric2_label' => array( 'ข้อมูลย่อย 2 · หัวข้อ', 'text' ),
			'control_metric2_value' => array( 'ข้อมูลย่อย 2 · ค่า', 'text' ),
			'control_metric3_label' => array( 'ข้อมูลย่อย 3 · หัวข้อ', 'text' ),
			'control_metric3_value' => array( 'ข้อมูลย่อย 3 · ค่า', 'text' ),
			'control_list_title'   => array( 'รายการเตรียมตัว · หัวข้อ', 'text' ),
			'control_list_items'   => array( 'รายการเตรียมตัว (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'home_cards_title' => array( 'หัวข้อกลุ่มการ์ด', 'text' ),
			'home_cards_sub'   => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			'card1_title'      => array( 'การ์ด 1 · หัวข้อ', 'text' ),
			'card1_desc'       => array( 'การ์ด 1 · รายละเอียด', 'textarea' ),
			'card1_url'        => array( 'การ์ด 1 · ลิงก์', 'url' ),
			'card2_title'      => array( 'การ์ด 2 · หัวข้อ', 'text' ),
			'card2_desc'       => array( 'การ์ด 2 · รายละเอียด', 'textarea' ),
			'card2_url'        => array( 'การ์ด 2 · ลิงก์', 'url' ),
			'card3_title'      => array( 'การ์ด 3 · หัวข้อ', 'text' ),
			'card3_desc'       => array( 'การ์ด 3 · รายละเอียด', 'textarea' ),
			'card3_url'        => array( 'การ์ด 3 · ลิงก์', 'url' ),
			'card4_title'      => array( 'การ์ด 4 · หัวข้อ', 'text' ),
			'card4_desc'       => array( 'การ์ด 4 · รายละเอียด', 'textarea' ),
			'card4_url'        => array( 'การ์ด 4 · ลิงก์', 'url' ),
			'card5_title'      => array( 'การ์ด 5 · หัวข้อ', 'text' ),
			'card5_desc'       => array( 'การ์ด 5 · รายละเอียด', 'textarea' ),
			'card5_url'        => array( 'การ์ด 5 · ลิงก์', 'url' ),
		),
	);

	$sections['fenix_backtest'] = array(
		'title'       => '20) หน้า Backtest',
		'description' => 'กรอกผลการทดสอบย้อนหลังจริงเท่านั้น และอย่าลบ Disclaimer',
		'fields'      => array(
			'backtest_sub'         => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'backtest_intro'       => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'bt_stat1_label'       => array( 'สถิติ 1 · หัวข้อ', 'text' ),
			'bt_stat1_value'       => array( 'สถิติ 1 · ค่า', 'text' ),
			'bt_stat2_label'       => array( 'สถิติ 2 · หัวข้อ', 'text' ),
			'bt_stat2_value'       => array( 'สถิติ 2 · ค่า', 'text' ),
			'bt_stat3_label'       => array( 'สถิติ 3 · หัวข้อ', 'text' ),
			'bt_stat3_value'       => array( 'สถิติ 3 · ค่า', 'text' ),
			'bt_stat4_label'       => array( 'สถิติ 4 · หัวข้อ', 'text' ),
			'bt_stat4_value'       => array( 'สถิติ 4 · ค่า', 'text' ),
			'bt_stat5_label'       => array( 'สถิติ 5 · หัวข้อ', 'text' ),
			'bt_stat5_value'       => array( 'สถิติ 5 · ค่า', 'text' ),
			'bt_stat6_label'       => array( 'สถิติ 6 · หัวข้อ', 'text' ),
			'bt_stat6_value'       => array( 'สถิติ 6 · ค่า', 'text' ),
			'bt_stat7_label'       => array( 'สถิติ 7 · หัวข้อ', 'text' ),
			'bt_stat7_value'       => array( 'สถิติ 7 · ค่า', 'text' ),
			'bt_stat8_label'       => array( 'สถิติ 8 · หัวข้อ', 'text' ),
			'bt_stat8_value'       => array( 'สถิติ 8 · ค่า', 'text' ),
			'backtest_img'         => array( 'ภาพกราฟ/รายงานผล Backtest', 'image' ),
			'backtest_img_caption' => array( 'คำบรรยายภาพ', 'text' ),
			'backtest_note'        => array( 'หมายเหตุเงื่อนไขการทดสอบ', 'textarea' ),
			'backtest_disclaimer'  => array( 'Disclaimer (จำเป็น)', 'textarea' ),
		),
	);

	$sections['fenix_forward'] = array(
		'title'       => '21) หน้า Forward Test',
		'description' => 'กรอกผลการทดสอบบนบัญชีจริง/เดโม่จริงเท่านั้น และอย่าลบ Disclaimer',
		'fields'      => array(
			'forward_sub'         => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'forward_intro'       => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'fw_stat1_label'      => array( 'สถิติ 1 · หัวข้อ', 'text' ),
			'fw_stat1_value'      => array( 'สถิติ 1 · ค่า', 'text' ),
			'fw_stat2_label'      => array( 'สถิติ 2 · หัวข้อ', 'text' ),
			'fw_stat2_value'      => array( 'สถิติ 2 · ค่า', 'text' ),
			'fw_stat3_label'      => array( 'สถิติ 3 · หัวข้อ', 'text' ),
			'fw_stat3_value'      => array( 'สถิติ 3 · ค่า', 'text' ),
			'fw_stat4_label'      => array( 'สถิติ 4 · หัวข้อ', 'text' ),
			'fw_stat4_value'      => array( 'สถิติ 4 · ค่า', 'text' ),
			'fw_stat5_label'      => array( 'สถิติ 5 · หัวข้อ', 'text' ),
			'fw_stat5_value'      => array( 'สถิติ 5 · ค่า', 'text' ),
			'fw_stat6_label'      => array( 'สถิติ 6 · หัวข้อ', 'text' ),
			'fw_stat6_value'      => array( 'สถิติ 6 · ค่า', 'text' ),
			'forward_img'         => array( 'ภาพผลการทดสอบ', 'image' ),
			'forward_img_caption' => array( 'คำบรรยายภาพ', 'text' ),
			'forward_link_label'  => array( 'ข้อความปุ่มลิงก์ผลเรียลไทม์ (ถ้ามี)', 'text' ),
			'forward_link_url'    => array( 'ลิงก์ผลเรียลไทม์ (เช่น Myfxbook)', 'url' ),
			'forward_note'        => array( 'หมายเหตุ', 'textarea' ),
			'forward_disclaimer'  => array( 'Disclaimer (จำเป็น)', 'textarea' ),
		),
	);

	$sections['fenix_install'] = array(
		'title'       => '22) หน้า How to Install',
		'description' => 'คู่มือติดตั้งทีละขั้นตอน อัปโหลดภาพประกอบแต่ละขั้นได้ (เว้นว่างได้)',
		'fields'      => array(
			'install_sub'      => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'install_intro'    => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'install_req'      => array( 'สิ่งที่ต้องเตรียม (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'inst_step1_title' => array( 'ขั้นตอน 1 · หัวข้อ', 'text' ),
			'inst_step1_desc'  => array( 'ขั้นตอน 1 · รายละเอียด', 'textarea' ),
			'inst_step1_img'   => array( 'ขั้นตอน 1 · ภาพ', 'image' ),
			'inst_step2_title' => array( 'ขั้นตอน 2 · หัวข้อ', 'text' ),
			'inst_step2_desc'  => array( 'ขั้นตอน 2 · รายละเอียด', 'textarea' ),
			'inst_step2_img'   => array( 'ขั้นตอน 2 · ภาพ', 'image' ),
			'inst_step3_title' => array( 'ขั้นตอน 3 · หัวข้อ', 'text' ),
			'inst_step3_desc'  => array( 'ขั้นตอน 3 · รายละเอียด', 'textarea' ),
			'inst_step3_img'   => array( 'ขั้นตอน 3 · ภาพ', 'image' ),
			'inst_step4_title' => array( 'ขั้นตอน 4 · หัวข้อ', 'text' ),
			'inst_step4_desc'  => array( 'ขั้นตอน 4 · รายละเอียด', 'textarea' ),
			'inst_step4_img'   => array( 'ขั้นตอน 4 · ภาพ', 'image' ),
			'inst_step5_title' => array( 'ขั้นตอน 5 · หัวข้อ', 'text' ),
			'inst_step5_desc'  => array( 'ขั้นตอน 5 · รายละเอียด', 'textarea' ),
			'inst_step5_img'   => array( 'ขั้นตอน 5 · ภาพ', 'image' ),
			'inst_step6_title' => array( 'ขั้นตอน 6 · หัวข้อ', 'text' ),
			'inst_step6_desc'  => array( 'ขั้นตอน 6 · รายละเอียด', 'textarea' ),
			'inst_step6_img'   => array( 'ขั้นตอน 6 · ภาพ', 'image' ),
			'install_note'     => array( 'ข้อความปิดท้าย', 'text' ),
		),
	);

	$sections['fenix_pricing_extra'] = array(
		'title'       => '23) หน้า Pricing (เพิ่มเติม)',
		'description' => 'หน้านี้ใช้แพ็กเกจจากหมวด "แพ็กเกจราคา" ร่วมกัน และเพิ่มตารางเปรียบเทียบได้ที่นี่',
		'fields'      => array(
			'pricing_sub'   => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'compare_title' => array( 'หัวข้อตารางเปรียบเทียบ', 'text' ),
			'compare_rows'  => array( 'ตารางเปรียบเทียบ (บรรทัดละ 1 แถว คั่นช่องด้วย | บรรทัดแรกคือหัวตาราง ใช้ ✓ และ ✗ ได้)', 'textarea' ),
		),
	);

	$sections['fenix_riskpage'] = array(
		'title'       => '24) หน้า Risk Disclosure',
		'description' => 'หน้าประกาศความเสี่ยงฉบับเต็ม มี 6 หัวข้อ เว้นว่างหัวข้อที่ไม่ใช้ได้',
		'fields'      => array(
			'riskpage_sub'     => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'riskpage_intro'   => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'riskpage_image'   => array( 'ภาพประกอบความเสี่ยง', 'image' ),
			'riskpage_image_caption' => array( 'คำบรรยายภาพประกอบความเสี่ยง', 'text' ),
			'rp_block1_title'  => array( 'หัวข้อ 1', 'text' ),
			'rp_block1_text'   => array( 'เนื้อหา 1', 'textarea' ),
			'rp_block2_title'  => array( 'หัวข้อ 2', 'text' ),
			'rp_block2_text'   => array( 'เนื้อหา 2', 'textarea' ),
			'rp_block3_title'  => array( 'หัวข้อ 3', 'text' ),
			'rp_block3_text'   => array( 'เนื้อหา 3', 'textarea' ),
			'rp_block4_title'  => array( 'หัวข้อ 4', 'text' ),
			'rp_block4_text'   => array( 'เนื้อหา 4', 'textarea' ),
			'rp_block5_title'  => array( 'หัวข้อ 5', 'text' ),
			'rp_block5_text'   => array( 'เนื้อหา 5', 'textarea' ),
			'rp_block6_title'  => array( 'หัวข้อ 6', 'text' ),
			'rp_block6_text'   => array( 'เนื้อหา 6', 'textarea' ),
			'riskpage_updated' => array( 'วันที่ปรับปรุงล่าสุด', 'text' ),
		),
	);

	$sections['fenix_team'] = array(
		'title'       => '25) ทีมงาน / ใครอยู่เบื้องหลัง',
		'description' => 'แสดงตัวตนของทีม/ผู้พัฒนาเพื่อสร้างความน่าเชื่อถือ แนะนำประสบการณ์และเหตุผลที่สร้างระบบ เปิด "แสดงส่วนนี้" เมื่อกรอกข้อมูลจริงแล้ว ห้ามใส่ข้อมูลเท็จ',
		'fields'      => array(
			'show_team'   => array( 'แสดงส่วนนี้ (เปิดเมื่อกรอกข้อมูลจริง)', 'checkbox' ),
			'team_kicker' => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'team_title'  => array( 'หัวข้อ', 'text' ),
			'team_text'   => array( 'เนื้อหา (เว้นบรรทัด = ย่อหน้าใหม่)', 'textarea' ),
			'team_points' => array( 'จุดเด่นของทีม (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'team_img'    => array( 'รูปทีม / ผู้พัฒนา (ถ้ามี)', 'image' ),
		),
	);

	$sections['fenix_assurance'] = array(
		'title'       => '26) ความมั่นใจก่อนเริ่ม',
		'description' => 'จุดสร้างความสบายใจก่อนตัดสินใจ ใช้ข้อความที่เป็นจริง ไม่การันตีกำไร',
		'fields'      => array(
			'show_assurance'     => array( 'แสดงส่วนนี้', 'checkbox' ),
			'assurance_kicker'   => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'assurance_title'    => array( 'หัวข้อ', 'text' ),
			'assurance_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			'assurance_items'    => array( 'รายการความมั่นใจ (บรรทัดละ 1 ข้อ)', 'textarea' ),
		),
	);

	$sections['fenix_blog'] = array(
		'title'       => '27) หน้าแรก · บทความล่าสุด',
		'description' => 'แสดงบทความล่าสุด 3 รายการบนหน้าแรก ส่วนนี้จะซ่อนอัตโนมัติเมื่อยังไม่มีบทความที่เผยแพร่',
		'fields'      => array(
			'show_blog'      => array( 'แสดงส่วนนี้', 'checkbox' ),
			'blog_kicker'    => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'blog_title'     => array( 'หัวข้อ', 'text' ),
			'blog_subtitle'  => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			'blog_all_label' => array( 'ข้อความลิงก์ดูบทความทั้งหมด', 'text' ),
		),
	);

	$priority = 10;

	foreach ( $sections as $section_id => $section ) {

		$wp_customize->add_section(
			$section_id,
			array(
				'panel'       => 'fenix_panel',
				'title'       => $section['title'],
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'priority'    => $priority,
			)
		);
		$priority += 10;

		foreach ( $section['fields'] as $field_id => $field ) {

			$label = $field[0];
			$type  = $field[1];
			$extra = isset( $field[2] ) ? $field[2] : '';

			switch ( $type ) {
				case 'checkbox':
					$sanitize = 'fenix_sanitize_checkbox';
					break;
				case 'url':
				case 'image':
					$sanitize = 'esc_url_raw';
					break;
				case 'textarea':
					$sanitize = 'sanitize_textarea_field';
					break;
				case 'radio':
					$sanitize = 'fenix_sanitize_pricing_mode';
					break;
				default:
					$sanitize = 'sanitize_text_field';
			}

			$wp_customize->add_setting(
				$field_id,
				array(
					'default'           => isset( $d[ $field_id ] ) ? $d[ $field_id ] : '',
					'type'              => 'theme_mod',
					'sanitize_callback' => $sanitize,
				)
			);

			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						$field_id,
						array(
							'label'       => $label,
							'section'     => $section_id,
							'description' => is_string( $extra ) ? $extra : '',
						)
					)
				);
			} elseif ( 'radio' === $type ) {
				$wp_customize->add_control(
					$field_id,
					array(
						'label'   => $label,
						'section' => $section_id,
						'type'    => 'radio',
						'choices' => is_array( $extra ) ? $extra : array(),
					)
				);
			} else {
				$wp_customize->add_control(
					$field_id,
					array(
						'label'       => $label,
						'section'     => $section_id,
						'type'        => $type,
						'description' => is_string( $extra ) ? $extra : '',
					)
				);
			}
		}
	}
}
add_action( 'customize_register', 'fenix_customize_register' );

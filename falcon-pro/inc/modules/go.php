<?php
/**
 * FALCON PRO EA · โมดูลหน้า /go (ลิงก์รวม) · template-go.php
 *
 * FALCON ใช้ผ่านแดชบอร์ดสมาชิกอย่างเดียว (เจ้าของตัดสิน 13 ต.ค. 2026): EA ทำงานบนเซิร์ฟเวอร์ของเรา ไม่มีไฟล์ EA ให้ลูกค้า
 * ไม่มีการติดตั้งบนคอมหรือ VPS ของลูกค้า · แอป MT5 มีไว้ดูบัญชี · หน้านี้จึงเป็นเส้นทางเดียว: เปิดบัญชี → แอป MT5 → ฝากเงิน → เริ่มใช้บนแดชบอร์ด
 *
 * โครงหน้า (ลำดับตามหน้าลิงก์รวมของเว็บในเครือ · หน้าตาแบบ FALCON):
 *   โลโก้กลม + ชื่อ + คำโปรย + ป้าย → กลุ่มติดต่อทีม (LINE, OpenChat) → [การ์ดแดชบอร์ดแบบย่อ fenix_dashboard_block( 'go' ) · ปิดเป็นค่าเริ่มต้น]
 *   → ขั้นตอนมีเลข (ขั้นสุดท้าย = ปุ่มเข้าชมและปุ่มสมัครแดชบอร์ด) → ข้อมูลก่อนเริ่ม (ปุ่ม 1 ถึง 6) → โซเชียล → เนื้อหาเพจ (ปิดไว้)
 *   → คำเตือนความเสี่ยงฉบับเต็ม → ลิงก์เอกสาร + ตั้งค่าคุกกี้
 *
 * กติกา
 * - ขั้นตอนที่ไม่มีปุ่มจะถูกซ่อน และเลขขั้นเรียงใหม่ตามที่เหลือจริง ({n} ในหัวข้อ = จำนวนขั้นที่แสดง)
 * - ขั้นรับไฟล์ EA · ติดตั้ง EA · VPS ปิดเป็นค่าเริ่มต้น (go_show_download · go_show_install · go_show_vps) ข้อความเดิมเก็บไว้ครบ
 * - ปุ่มที่มีข้อความแต่ไม่มีลิงก์ หรือชี้ไปเพจของเว็บที่ยังไม่เผยแพร่ = ปุ่มเส้นประ กดไม่ได้ พร้อมป้าย "เร็ว ๆ นี้"
 * - ปุ่มที่ชี้ไปเพจที่ถอดออกจากเว็บแล้ว (fenix_retired_pages ใน inc/setup.php) ไม่ถูกพิมพ์เลย
 * - ขั้นเปิดบัญชี: มี "ลิงก์สมัครบัญชี" และชื่อโบรกเกอร์จริง (หมวดโบรกเกอร์ · fenix_partner_ready) = ปุ่มสมัคร rel="sponsored noopener" เปิดแท็บใหม่ · ไม่มี = ปุ่ม LINE ขอลิงก์
 *   หน้านี้ไม่มีหมายเหตุใต้ปุ่มสมัคร (ข้อความเปิดเผยความเป็นพาร์ตเนอร์อยู่ที่หน้าคู่มือเปิดบัญชีและข้อกำหนดการใช้บริการ)
 * - ขั้นฝากเงิน: ปุ่มคู่มือ (/deposit/) แสดงเมื่อหน้านั้นเผยแพร่แล้ว · ปุ่มหน้าสมาชิกของโบรกเกอร์แสดงเมื่อเจ้าของกรอกลิงก์ · ไม่มีทั้งคู่ = ซ่อนขั้น
 * - ปุ่ม LINE ทุกปุ่มใช้ fenix_contact_target() / fenix_contact_button() · ไม่มีลิงก์ LINE = ไม่แสดงปุ่ม
 * - ข้อความทุกคำเป็น setting ในหมวด "30) หน้า /go" และ "31) หน้า /go · ขั้นตอนเริ่มใช้งาน"
 * - คีย์เดิม go_step1..6 ย้ายไปคีย์ใหม่ผ่านค่าเริ่มต้น (ค่าที่เจ้าของเคยบันทึกไว้ยังถูกใช้ต่อ)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น (ข้อความของ FALCON เอง)
 * ============================================================== */

/**
 * ค่าที่เจ้าของเคยบันทึกไว้ในคีย์เดิม (go_step1..6) · ไม่มี = ใช้ค่าใหม่
 */
function fenix_go_legacy( $old_key, $fallback ) {
	$value = get_theme_mod( $old_key, '' );
	return ( is_string( $value ) && '' !== trim( $value ) ) ? $value : $fallback;
}

function fenix_go_defaults( $d ) {
	/* คีย์เดิมของหน้า /go (ชื่อขั้นอย่างเดียว) · ย้ายไปคีย์ใหม่ด้านล่าง */
	foreach ( array( 'go_step1', 'go_step2', 'go_step3', 'go_step4', 'go_step5', 'go_step6' ) as $old ) {
		unset( $d[ $old ] );
	}

	return array_merge(
		$d,
		array(
			/* ส่วนหัวการ์ด */
			'go_logo'                => '',
			'go_title'               => 'FALCON PRO EA',
			/* คำโปรยต้องตรงกับตัวระบบ: เทรดทองคำ XAUUSD อย่างเดียว ตามกลยุทธ์ของชุด EA ที่เลือก (ผู้ใช้ไม่ได้ตั้งเงื่อนไขเข้าออกเอง) */
			'go_sub'                 => "EA เทรดทองคำ XAUUSD บน MetaTrader 5\nมีคำถามคุยกับทีมงานได้ หรือเริ่มทีละขั้นตามลำดับด้านล่าง",
			/* ป้ายเล็กใต้คำโปรย: ว่างเป็นค่าเริ่มต้น (เจ้าของสั่งถอดป้ายตกแต่งรอบชื่อ 12 ต.ค. 2026) · ว่าง = template-go.php ไม่พิมพ์ ul.lh-badges · ช่องใน Customizer ยังอยู่ */
			'go_badges'              => '',

			/* กลุ่มติดต่อทีม */
			'go_help_title'          => 'สงสัยตรงไหน คุยกับทีมงานก่อนเริ่มได้',
			'go_line_label'          => 'คุยกับทีมงาน FALCON ผ่าน LINE',
			'go_openchat_label'      => 'เข้ากลุ่ม OpenChat ผู้ใช้ FALCON',

			/* การ์ดแดชบอร์ดแบบย่อ (ต่อจากกลุ่มติดต่อทีม ก่อนขั้นตอน) ปิดเป็นค่าเริ่มต้น: ปุ่มเข้าชมและปุ่มสมัครย้ายไปอยู่ในขั้นสุดท้ายของเส้นทาง
			 * (ขั้น "เริ่มใช้บนแดชบอร์ด" ด้านล่าง) หน้านี้จึงมีเส้นทางเดียวจากบนลงล่าง ไม่มีปุ่มชุดเดียวกันสองที่ · ติ๊กเปิดได้ถ้าอยากให้มีการ์ดด้านบนด้วย
			 * ข้อความ ปุ่ม และหมายเหตุของการ์ดอยู่ที่หมวด 1.1) แดชบอร์ด */
			'go_show_dashboard'      => false,

			/* ขั้นตอน · ส่วนรวม · FALCON ใช้ผ่านแดชบอร์ดอย่างเดียว (เจ้าของตัดสิน 13 ต.ค. 2026): เปิดบัญชี → แอป MT5 → ฝากเงิน → เริ่มบนแดชบอร์ด
			 * EA ทำงานบนเซิร์ฟเวอร์ของเรา ไม่มีไฟล์ EA ให้ลูกค้า ไม่มีการติดตั้งบนคอมหรือ VPS ของลูกค้า · แอป MT5 มีไว้ดูบัญชีเท่านั้น */
			'go_steps_title'         => 'เริ่มใช้ผ่านแดชบอร์ด · ทำตาม {n} ขั้นนี้',
			'go_step_word'           => 'ขั้นที่',
			'go_pending_label'       => 'เร็ว ๆ นี้',

			/* ขั้น 1 · เปิดบัญชี (แดชบอร์ดรับเฉพาะบัญชีเงินจริงของโบรกเกอร์ที่รองรับ ไม่รับบัญชีเดโม · บัญชี Cent ใช้ได้) */
			'go_step1_title'         => fenix_go_legacy( 'go_step1', 'เปิดบัญชีเทรด MT5' ),
			'go_step1_desc'          => 'เปิดบัญชี MT5 แบบเงินจริงกับ {broker} ตั้งแต่กรอกใบสมัคร ยืนยันอีเมล จนส่งเอกสารยืนยันตัวตนผ่าน แล้วเก็บเลขบัญชี รหัสผ่าน และชื่อเซิร์ฟเวอร์ไว้ใช้ในขั้นต่อไป',
			'go_step1_note'          => 'แดชบอร์ดรับเฉพาะบัญชีเงินจริง ไม่รับบัญชีเดโม ส่วนบัญชี Cent ใช้ได้',
			'go_step1_badge'         => '',
			'go_signup_line_label'   => 'ขอลิงก์เปิดบัญชีผ่าน LINE',
			'go_account_guide_label' => fenix_go_legacy( 'go_step2', 'คู่มือเปิดบัญชีและยืนยันตัวตน' ),
			'go_account_guide_url'   => '/open-mt5-account/',

			/* ขั้น 2 · แอป MT5 (ลิงก์ร้านแอปใช้คีย์เดิม mt5_dl_*) · แอปมีไว้ดูบัญชี: ยอดเงิน ออเดอร์ที่เปิดอยู่ และประวัติ · EA ไม่ได้ทำงานในแอปหรือบนเครื่องของลูกค้า */
			'go_step2_title'         => fenix_go_legacy( 'go_step3', 'ดาวน์โหลดและล็อกอิน MT5' ),
			'go_step2_desc'          => 'เลือกแอปตามเครื่องที่ใช้ แล้วเข้าสู่ระบบด้วยเลขบัญชีและรหัสผ่านจากขั้นแรก',
			'go_step2_note'          => 'แอป MT5 บนมือถือหรือคอมพิวเตอร์มีไว้ดูยอดเงิน ออเดอร์ที่เปิดอยู่ และประวัติของบัญชี ส่วน EA ทำงานบนเซิร์ฟเวอร์ของเรา คุณจึงไม่ต้องเปิดเครื่องค้างไว้และไม่ต้องเช่า VPS',
			'go_step2_badge'         => '',
			'go_mt5_ios_label'       => 'iPhone',
			'go_mt5_android_label'   => 'Android',
			'go_mt5_windows_label'   => 'Windows',
			'go_mt5_macos_label'     => '',
			'go_mt5_macos_url'       => '',
			'go_mt5_login_label'     => 'วิธีล็อกอิน MT5 ครั้งแรก',
			'go_mt5_login_url'       => '/mt5-login/',

			/* ขั้น 3 · ฝากเงิน
			 * ปุ่มคู่มือชี้หน้า /deposit/ และแสดงเมื่อหน้านั้นเผยแพร่แล้ว · ปุ่มหลัก (หน้าสมาชิกของโบรกเกอร์) แสดงเมื่อเจ้าของกรอกลิงก์
			 * ลิงก์หน้าสมาชิกเป็นของโบรกเกอร์รายใดรายหนึ่ง จึงไม่มีค่าเริ่มต้นในโค้ด · ยังไม่มีทั้งสองปุ่ม = ซ่อนทั้งขั้น */
			'go_step3_title'         => 'เติมเงินทุนเข้าบัญชี MT5',
			'go_step3_desc'          => 'ฝากผ่านหน้าสมาชิกของโบรกเกอร์ในชื่อคุณเอง และเริ่มด้วยเงินจำนวนน้อยที่ยอมเสียได้ทั้งหมด เช่น เริ่มกับบัญชี Cent',
			'go_step3_note'          => '',
			'go_step3_badge'         => '',
			'go_deposit_label'       => 'ฝากเงินผ่านหน้าสมาชิกของ {broker}',
			'go_deposit_url'         => '',
			'go_deposit_guide_label' => 'คู่มือฝากเงินเข้าบัญชี MT5',
			'go_deposit_guide_url'   => '/deposit/',

			/* ขั้นสุดท้าย · เริ่มใช้บนแดชบอร์ด (คีย์ go_dash_*) · ปุ่มเข้าชมและปุ่มสมัครใช้ที่อยู่และข้อความปุ่มจากหมวด 1.1) แดชบอร์ด
			 * (fenix_dashboard_url / fenix_dashboard_label) และนับคลิกเป็น dashboard_click ตำแหน่ง go-steps · หมายเหตุใต้ปุ่ม (ข้อมูลตัวอย่างของโหมดเยี่ยมชม
			 * รหัสเชิญ สถานะ) มาจากหมวดเดียวกัน · ลำดับในคำอธิบายตรงกับที่แดชบอร์ดให้ทำ: สมัครด้วยรหัสเชิญ → เพิ่มบัญชี MT5 → เลือกชุด EA → กดเริ่ม
			 * ห้ามเขียนจำนวนบัญชี เวลารอ หรือราคาในขั้นนี้ และห้ามเขียนว่าทีมงานขอรหัสผ่าน · ปิดแดชบอร์ดที่หมวด 1.1 = ขั้นนี้หายไปเอง */
			'go_dash_title'          => 'เริ่มใช้บนแดชบอร์ด FALCON PRO',
			'go_dash_desc'           => 'คุยกับทีมงานเพื่อเลือกแพ็กเกจและรับรหัสเชิญ สมัครสมาชิกแดชบอร์ด เพิ่มบัญชี MT5 เลือกชุด EA แล้วกดเริ่ม จากนั้นสั่งหยุดได้ทุกเมื่อ จะเข้าชมแดชบอร์ดก่อนสมัครก็ได้',
			'go_dash_note'           => 'รหัสผ่านเทรดของ MT5 คุณพิมพ์เองในแดชบอร์ดเท่านั้น ทีมงานไม่ขอรหัสผ่านหรือรหัสยืนยันทางแชท',
			'go_dash_badge'          => '',
			'go_dash_guide_label'    => 'ดูขั้นตอนบนแดชบอร์ดทีละขั้น',
			'go_dash_guide_url'      => '/dashboard/',

			/* ขั้นรับไฟล์ EA (คีย์ go_step4_*) ปิดเป็นค่าเริ่มต้น: FALCON ไม่ส่งไฟล์ EA ให้ลูกค้าแล้ว (ใช้ผ่านแดชบอร์ดอย่างเดียว)
			 * ข้อความ ปุ่ม และการ์ดดาวน์โหลดของขั้นนี้ยังเก็บไว้ครบ เปิดกลับได้ด้วยช่องติ๊ก go_show_download */
			'go_show_download'       => false,
			'go_step4_title'         => 'รับไฟล์ FALCON PRO EA',
			'go_step4_desc'          => 'รับไฟล์ .ex5 ของ FALCON PRO EA แล้วสอบถามทีมงานเรื่องค่าเริ่มต้นที่สอดคล้องกับขนาดพอร์ตก่อนติดตั้ง',
			'go_step4_note'          => '',
			'go_step4_badge'         => '',
			'go_download_line_label' => fenix_go_legacy( 'go_step4', 'ทัก LINE เพื่อขอรับไฟล์ EA' ),
			'go_fast_img'            => '',
			'go_fast_url'            => '',
			'go_fast_alt'            => 'การ์ดดาวน์โหลดไฟล์ FALCON PRO EA',
			'go_fast_label'          => 'ดาวน์โหลดไฟล์ EA (.ex5)',

			/* ขั้น 5 · ติดตั้ง EA และขั้น 6 · VPS ปิดเป็นค่าเริ่มต้น (เจ้าของสั่ง 13 ต.ค. 2026: หน้านี้ไม่ต้องมีสองขั้นนี้ เพราะ EA รันบนแดชบอร์ด)
			 * ข้อความและปุ่มของสองขั้นยังเก็บไว้ครบ เปิดกลับได้ด้วยช่องติ๊ก · ปุ่มที่ชี้ไปเพจที่ถอดออกจากเว็บแล้ว (คู่มือติดตั้ง คู่มือ VPS) ไม่ถูกพิมพ์ */
			'go_show_install'        => false,
			'go_show_vps'            => false,

			/* ขั้น 5 · ติดตั้ง EA */
			'go_step5_title'         => fenix_go_legacy( 'go_step5', 'ติดตั้งและตั้งค่า EA ในโปรแกรม MT5' ),
			'go_step5_desc'          => 'ย้ายไฟล์ .ex5 ไปที่ MQL5 → Experts ลาก EA ลงกราฟทองคำ XAUUSD แล้วตรวจว่าปุ่ม Algo Trading ทำงานอยู่',
			'go_step5_note'          => '',
			'go_step5_badge'         => '',
			/* ไม่สัญญาว่ามีภาพทุกขั้น: คู่มือติดตั้งมีภาพมากับธีมเพียงบางขั้น ที่เหลือรอภาพจากเจ้าของเว็บ */
			'go_install1_label'      => 'คู่มือติดตั้ง EA ทีละขั้น',
			'go_install1_url'        => '/how-to-install/',
			'go_install2_label'      => 'คำนวณ Lot ก่อนเปิดใช้งานจริง',
			'go_install2_url'        => '/tools/',

			/* ขั้น 6 · VPS */
			'go_step6_title'         => fenix_go_legacy( 'go_step6', 'ตั้ง VPS ให้ EA ทำงานต่อเนื่อง' ),
			'go_step6_desc'          => 'ถ้าปิดคอมพิวเตอร์หรืออินเทอร์เน็ตหลุด MT5 จะหยุดและ EA ก็หยุดตาม การเช่า VPS ที่ออนไลน์ตลอดเวลาช่วยให้ MT5 เปิดค้างไว้แทนเครื่องของคุณ เลือกคู่มือตามเครื่องที่คุณใช้รีโมตเข้า VPS',
			'go_step6_note'          => '',
			'go_step6_badge'         => 'แนะนำ',
			'go_vps_windows_label'   => 'Windows',
			'go_vps_windows_url'     => '/vps-windows/',
			'go_vps_android_label'   => 'Android',
			'go_vps_android_url'     => '/vps-android/',
			'go_vps_ios_label'       => 'iPhone / iPad',
			'go_vps_ios_url'         => '/vps-ios/',
			/* ยังไม่มีคู่มือ VPS สำหรับ macOS → ไม่แสดงปุ่ม (เดิมเป็นปุ่มเส้นประ "เร็ว ๆ นี้" ค้างถาวร) · มีคู่มือเมื่อไรกรอกชื่อปุ่ม + ลิงก์ได้ที่ Customizer */
			'go_vps_macos_label'     => '',
			'go_vps_macos_url'       => '',

			/* กลุ่มข้อมูลก่อนเริ่ม */
			/* ปุ่ม 1 เดิมไปหน้า Forward Test (ถอดออกจากเว็บแล้ว) · ตอนนี้ไปเครื่องคำนวณ ซึ่งเดิมเข้าได้จากขั้นติดตั้ง EA ที่ปิดอยู่ */
			'go_info_title'          => 'ข้อมูลที่ควรรู้ก่อนเริ่ม',
			'go_btn1_label'          => 'คำนวณ Lot และ Drawdown ก่อนเริ่ม',
			'go_btn1_url'            => '/tools/',
			'go_btn2_label'          => 'Backtest และเงื่อนไขการทดสอบย้อนหลัง',
			'go_btn2_url'            => '/backtest/',
			'go_btn3_label'          => 'แพ็กเกจและราคา',
			'go_btn3_url'            => '/pricing/',
			'go_btn4_label'          => 'เกี่ยวกับ FALCON PRO EA',
			'go_btn4_url'            => '/about/',
			'go_btn5_label'          => 'บทความ EA และ MT5',
			'go_btn5_url'            => '/articles/',
			'go_btn6_label'          => '',
			'go_btn6_url'            => '',

			/* ท้ายการ์ด */
			'go_show_doc'            => false,
			'go_home_label'          => 'กลับหน้าแรกของเว็บไซต์',
		)
	);
}
add_filter( 'fenix_defaults', 'fenix_go_defaults' );

/* ==============================================================
 * Customizer · แทนหมวด fenix_go เดิม (คงตำแหน่งเดิม) + หมวดขั้นตอนต่อท้าย
 * ============================================================== */

function fenix_go_sections( $sections, $d ) {
	$link_rule = 'ใส่ slug เช่น /pricing/ หรือ URL เต็ม · เว้นว่างแต่มีข้อความ = ปุ่มเส้นประ "เร็ว ๆ นี้" · slug ของเพจที่ยังไม่เผยแพร่ก็แสดงเป็นปุ่มเส้นประจนกว่าจะเผยแพร่';

	$head = array(
		'title'       => '30) หน้า /go (ลิงก์รวม) · ส่วนหัวและลิงก์',
		'description' => 'หน้าลิงก์รวมสำหรับใส่ในโปรไฟล์ LINE OA / Facebook / TikTok และใช้เป็นปลายทางโฆษณา · เป็นหน้าเดี่ยว ไม่มีเมนูและท้ายเว็บ · เพจต้องใช้เทมเพลต "FALCON · หน้ารวมลิงก์ (/go)" · ปุ่ม LINE ใช้ลิงก์จากหมวด 1 · ท้ายการ์ดแสดงคำเตือนความเสี่ยงฉบับเต็มจากหมวดคำเตือนเสมอ · ช่องที่เว้นว่างจะถูกซ่อน',
		'fields'      => array(
			'go_logo'           => array( 'โลโก้วงกลม (ไม่ใส่ = ใช้ตราเหยี่ยวของธีม)', 'image', 'แนะนำภาพจัตุรัสอย่างน้อย 168×168px เพราะแสดงในกรอบวงกลม' ),
			'go_title'          => array( 'ชื่อบนการ์ด (H1 · เว้นว่าง = ใช้ชื่อเพจ)', 'text' ),
			'go_sub'            => array( 'คำโปรยใต้ชื่อ (ขึ้นบรรทัดใหม่ได้)', 'textarea' ),
			'go_badges'         => array( 'ป้ายเล็กใต้คำโปรย (บรรทัดละ 1 ป้าย · เว้นว่าง = ซ่อน)', 'textarea', 'ค่าเริ่มต้นเว้นว่าง ชื่อกับคำโปรยจึงขึ้นเดี่ยว ๆ · ห้ามใส่ตัวเลขผลเทรดหรือคำรับประกันกำไร' ),
			'go_help_title'     => array( 'กลุ่มติดต่อทีม · หัวข้อ', 'text' ),
			'go_line_label'     => array( 'กลุ่มติดต่อทีม · ข้อความปุ่ม LINE', 'text', 'ลิงก์ใช้ค่า LINE จากหมวด 1 · ไม่มีลิงก์ LINE = ไม่แสดงปุ่ม' ),
			'go_openchat_label' => array( 'กลุ่มติดต่อทีม · ข้อความปุ่ม OpenChat', 'text', 'ลิงก์ใช้ค่า "ลิงก์ LINE OpenChat" จากหมวด 1 · ไม่มีลิงก์ = ไม่แสดงปุ่ม' ),
			'go_show_dashboard' => array( 'แสดงการ์ดแดชบอร์ด (ปุ่มเข้าชมและปุ่มสมัคร) ต่อจากกลุ่มติดต่อทีม', 'checkbox', 'ปิดเป็นค่าเริ่มต้น เพราะปุ่มเข้าชมและปุ่มสมัครอยู่ในขั้นสุดท้ายของขั้นตอนแล้ว (หมวด 31) · ติ๊กเมื่ออยากให้มีการ์ดด้านบนด้วย หน้านี้จะมีปุ่มชุดเดียวกันสองที่ · หัวข้อ คำอธิบาย ข้อความปุ่ม หมายเหตุ และข้อควรรู้เรื่องความเสี่ยงของการ์ดใช้ค่าจากหมวด "1.1) แดชบอร์ด" (หัวข้อและคำอธิบายของหน้า /go อยู่ท้ายหมวดนั้น) · ปิดแดชบอร์ดที่หมวด 1.1 = การ์ดนี้หายเอง' ),
			'go_pending_label'  => array( 'ป้ายของปุ่มที่ยังไม่มีลิงก์', 'text', 'ปุ่มที่มีข้อความแต่ไม่มีลิงก์จะเป็นเส้นประ กดไม่ได้ พร้อมป้ายนี้' ),
			'go_info_title'     => array( 'กลุ่มข้อมูลก่อนเริ่ม · หัวข้อ', 'text' ),
			'go_btn1_label'     => array( 'ปุ่ม 1 · ข้อความ', 'text' ),
			'go_btn1_url'       => array( 'ปุ่ม 1 · ลิงก์', 'path', $link_rule . ' · ปุ่มที่ชี้ไปเพจที่ถอดออกจากเว็บแล้ว (คู่มือติดตั้ง คู่มือ VPS หน้า Forward Test) จะไม่แสดง' ),
			'go_btn2_label'     => array( 'ปุ่ม 2 · ข้อความ', 'text' ),
			'go_btn2_url'       => array( 'ปุ่ม 2 · ลิงก์', 'path' ),
			'go_btn3_label'     => array( 'ปุ่ม 3 · ข้อความ', 'text' ),
			'go_btn3_url'       => array( 'ปุ่ม 3 · ลิงก์', 'path' ),
			'go_btn4_label'     => array( 'ปุ่ม 4 · ข้อความ', 'text' ),
			'go_btn4_url'       => array( 'ปุ่ม 4 · ลิงก์', 'path' ),
			'go_btn5_label'     => array( 'ปุ่ม 5 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
			'go_btn5_url'       => array( 'ปุ่ม 5 · ลิงก์', 'path' ),
			'go_btn6_label'     => array( 'ปุ่ม 6 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
			'go_btn6_url'       => array( 'ปุ่ม 6 · ลิงก์', 'path' ),
			'go_show_doc'       => array( 'แสดงเนื้อหาของเพจ /go (จากตัวแก้ไข WordPress) ใต้ปุ่มโซเชียล', 'checkbox' ),
			'go_home_label'     => array( 'ท้ายการ์ด · ข้อความลิงก์กลับหน้าแรก (เว้นว่าง = ซ่อน)', 'text', 'ลิงก์เอกสาร (นโยบายความเป็นส่วนตัว · เงื่อนไข · ประกาศความเสี่ยง) แสดงเองเมื่อเพจเผยแพร่แล้ว · ไอคอนโซเชียลใช้ลิงก์จากหมวดช่องทางติดต่อ' ),
		),
	);

	$steps = array(
		'title'       => '31) หน้า /go · ขั้นตอนเริ่มใช้งาน',
		'description' => 'เส้นทางของหน้านี้คือการใช้ FALCON ผ่านแดชบอร์ด: เปิดบัญชี → แอป MT5 → ฝากเงิน → เริ่มใช้บนแดชบอร์ด (4 ขั้น) · EA ทำงานบนเซิร์ฟเวอร์ของเรา จึงไม่มีขั้นรับไฟล์ ติดตั้ง หรือ VPS เป็นค่าเริ่มต้น (สามขั้นนั้นยังมีช่องติ๊กเปิดได้ ข้อความเดิมเก็บไว้ครบ) · ขั้นตอนที่ไม่มีปุ่มจะถูกซ่อน และเลขขั้นเรียงใหม่อัตโนมัติ · ทุกขั้นมี หัวข้อ / คำอธิบาย / หมายเหตุ / ป้ายเล็ก (เว้นว่าง = ซ่อน) · หัวข้อ คำอธิบาย หมายเหตุของทุกขั้น และข้อความปุ่มฝากเงิน ใช้ {broker} {brand} แทนชื่อจากหมวดโบรกเกอร์และชื่อแบรนด์ได้ · ห้ามใส่ตัวเลขผลเทรดสมมติหรือคำรับประกันกำไร',
		'fields'      => array(
			'go_steps_title'         => array( 'หัวข้อรวมของขั้นตอน', 'text', 'ใส่ {n} เพื่อแสดงจำนวนขั้นที่แสดงจริงอัตโนมัติ' ),
			'go_step_word'           => array( 'คำนำหน้าเลขขั้นสำหรับโปรแกรมอ่านหน้าจอ', 'text' ),

			'go_step1_title'         => array( 'ขั้นเปิดบัญชี · หัวข้อ', 'text' ),
			'go_step1_desc'          => array( 'ขั้นเปิดบัญชี · คำอธิบาย', 'textarea' ),
			'go_step1_note'          => array( 'ขั้นเปิดบัญชี · หมายเหตุ (เว้นว่าง = ไม่แสดง)', 'textarea', 'ค่าเริ่มต้นบอกว่าแดชบอร์ดรับเฉพาะบัญชีเงินจริง ไม่รับบัญชีเดโม และบัญชี Cent ใช้ได้ · ข้อความเปิดเผยความเป็นพาร์ตเนอร์กับโบรกเกอร์แสดงที่หน้าคู่มือเปิดบัญชีและหน้าข้อกำหนดการใช้บริการอยู่แล้ว หน้านี้จึงไม่มีข้อความนั้น · ถ้าต้องการให้แสดงที่หน้านี้ด้วย พิมพ์ {disclosure} ต่อท้ายเพื่อใช้ข้อความชุดเดียวกับหมวด 29) โบรกเกอร์ (ยังไม่มีลิงก์สมัคร = ไม่แสดงอะไร) · ข้อความแสดงเหนือปุ่มของขั้นนี้' ),
			'go_step1_badge'         => array( 'ขั้นเปิดบัญชี · ป้ายเล็ก', 'text' ),
			'go_signup_line_label'   => array( 'ขั้นเปิดบัญชี · ข้อความปุ่ม LINE เมื่อยังไม่มีลิงก์สมัคร', 'text', 'ปุ่มสมัครใช้ "ลิงก์สมัครบัญชี" และ "ข้อความปุ่มสมัครบัญชี" จากหมวดโบรกเกอร์ ({broker} ในข้อความปุ่มถูกแทนด้วยชื่อโบรกเกอร์) · กรอกลิงก์นั้นแล้ว ปุ่ม LINE นี้จะถูกแทนด้วยปุ่มสมัครที่เปิดแท็บใหม่' ),
			'go_account_guide_label' => array( 'ขั้นเปิดบัญชี · ข้อความปุ่มคู่มือ', 'text' ),
			'go_account_guide_url'   => array( 'ขั้นเปิดบัญชี · ลิงก์ปุ่มคู่มือ', 'path', $link_rule ),

			'go_step2_title'         => array( 'ขั้นแอป MT5 · หัวข้อ', 'text' ),
			'go_step2_desc'          => array( 'ขั้นแอป MT5 · คำอธิบาย', 'textarea' ),
			'go_step2_note'          => array( 'ขั้นแอป MT5 · หมายเหตุ', 'textarea' ),
			'go_step2_badge'         => array( 'ขั้นแอป MT5 · ป้ายเล็ก', 'text' ),
			'go_mt5_ios_label'       => array( 'ขั้นแอป MT5 · ปุ่ม iPhone', 'text' ),
			'mt5_dl_ios'             => array( 'ขั้นแอป MT5 · ลิงก์ App Store', 'url' ),
			'go_mt5_android_label'   => array( 'ขั้นแอป MT5 · ปุ่ม Android', 'text' ),
			'mt5_dl_android'         => array( 'ขั้นแอป MT5 · ลิงก์ Google Play', 'url' ),
			'go_mt5_windows_label'   => array( 'ขั้นแอป MT5 · ปุ่ม Windows', 'text' ),
			'mt5_dl_windows'         => array( 'ขั้นแอป MT5 · ลิงก์ดาวน์โหลด Windows', 'url' ),
			'go_mt5_macos_label'     => array( 'ขั้นแอป MT5 · ปุ่ม macOS (เว้นว่าง = ซ่อน)', 'text' ),
			'go_mt5_macos_url'       => array( 'ขั้นแอป MT5 · ลิงก์ดาวน์โหลด macOS', 'url' ),
			'go_mt5_login_label'     => array( 'ขั้นแอป MT5 · ข้อความปุ่มคู่มือล็อกอิน', 'text' ),
			'go_mt5_login_url'       => array( 'ขั้นแอป MT5 · ลิงก์ปุ่มคู่มือล็อกอิน', 'path', $link_rule ),

			'go_step3_title'         => array( 'ขั้นฝากเงิน · หัวข้อ', 'text' ),
			'go_step3_desc'          => array( 'ขั้นฝากเงิน · คำอธิบาย', 'textarea' ),
			'go_step3_note'          => array( 'ขั้นฝากเงิน · หมายเหตุ', 'textarea' ),
			'go_step3_badge'         => array( 'ขั้นฝากเงิน · ป้ายเล็ก', 'text' ),
			'go_deposit_label'       => array( 'ขั้นฝากเงิน · ข้อความปุ่มหลัก', 'text', '{broker} ถูกแทนด้วยชื่อโบรกเกอร์จากหมวด 29 · ปุ่มนี้แสดงเมื่อกรอกลิงก์ปุ่มหลักด้านล่างแล้วเท่านั้น' ),
			'go_deposit_url'         => array( 'ขั้นฝากเงิน · ลิงก์ปุ่มหลัก (เช่น หน้าสมาชิกของโบรกเกอร์ · ว่าง = ซ่อนปุ่ม)', 'path', 'ใส่ URL หน้าสมาชิก (Client Portal) ของโบรกเกอร์ที่ใช้ฝากเงิน · เปิดแท็บใหม่ เป็นลิงก์ธรรมดา ไม่ใช่ลิงก์สมัคร' ),
			'go_deposit_guide_label' => array( 'ขั้นฝากเงิน · ข้อความปุ่มคู่มือ', 'text' ),
			'go_deposit_guide_url'   => array( 'ขั้นฝากเงิน · ลิงก์ปุ่มคู่มือ', 'path', 'ค่าเริ่มต้น /deposit/ (คู่มือฝากเงิน) · ปุ่มคู่มือแสดงเมื่อหน้านั้นเผยแพร่แล้ว หรือเมื่อมีปุ่มหลักอยู่แล้ว (กรณีหลังถ้าหน้ายังไม่เผยแพร่จะเป็นปุ่มเส้นประ "เร็ว ๆ นี้") · ยังไม่มีทั้งปุ่มหลักและหน้าคู่มือ = ซ่อนทั้งขั้น และเลขขั้นที่เหลือเรียงใหม่' ),

			'go_dash_title'          => array( 'ขั้นแดชบอร์ด · หัวข้อ', 'text', 'ขั้นสุดท้ายของเส้นทาง · ปุ่มเข้าชมและปุ่มสมัครใช้ที่อยู่และข้อความปุ่มจากหมวด "1.1) แดชบอร์ด" (ลบข้อความปุ่มใดในหมวดนั้น = ปุ่มนั้นหายจากขั้นนี้) · หมายเหตุใต้ปุ่มเรื่องข้อมูลตัวอย่าง รหัสเชิญ และสถานะ ใช้ค่าจากหมวดเดียวกัน · ปิดแดชบอร์ดที่หมวด 1.1 = ขั้นนี้หายเอง' ),
			'go_dash_desc'           => array( 'ขั้นแดชบอร์ด · คำอธิบาย', 'textarea', 'ห้ามใส่จำนวนบัญชี เวลารอ ราคา หรือคำว่าทีมงานจะขอรหัสผ่าน' ),
			'go_dash_note'           => array( 'ขั้นแดชบอร์ด · หมายเหตุ (เว้นว่าง = ไม่แสดง)', 'textarea' ),
			'go_dash_badge'          => array( 'ขั้นแดชบอร์ด · ป้ายเล็ก', 'text' ),
			'go_dash_guide_label'    => array( 'ขั้นแดชบอร์ด · ข้อความปุ่มคู่มือ (เว้นว่าง = ซ่อน)', 'text' ),
			'go_dash_guide_url'      => array( 'ขั้นแดชบอร์ด · ลิงก์ปุ่มคู่มือ', 'path', 'ค่าเริ่มต้น /dashboard/ (หน้าแนะนำแดชบอร์ดของเว็บนี้) · ปุ่มนี้แสดงเมื่อหน้านั้นเผยแพร่แล้ว' ),

			'go_show_download'       => array( 'แสดงขั้นรับไฟล์ EA', 'checkbox', 'ปิดเป็นค่าเริ่มต้น: FALCON ใช้ผ่านแดชบอร์ดอย่างเดียว ไม่มีไฟล์ EA ส่งให้ลูกค้า · ติ๊กเฉพาะเมื่อกลับมาแจกไฟล์จริง ข้อความและการ์ดดาวน์โหลดด้านล่างยังเก็บไว้ครบ' ),
			'go_step4_title'         => array( 'ขั้นรับไฟล์ EA · หัวข้อ', 'text' ),
			'go_step4_desc'          => array( 'ขั้นรับไฟล์ EA · คำอธิบาย', 'textarea' ),
			'go_step4_note'          => array( 'ขั้นรับไฟล์ EA · หมายเหตุ', 'textarea' ),
			'go_step4_badge'         => array( 'ขั้นรับไฟล์ EA · ป้ายเล็ก', 'text' ),
			'go_download_line_label' => array( 'ขั้นรับไฟล์ EA · ข้อความปุ่ม LINE เมื่อยังไม่มีลิงก์ไฟล์', 'text' ),
			'go_fast_url'            => array( 'ขั้นรับไฟล์ EA · ลิงก์ดาวน์โหลดไฟล์ (ว่าง = ใช้ปุ่ม LINE ด้านบน)', 'path', 'อัปโหลดไฟล์ที่ สื่อ → เพิ่มใหม่ แล้ววาง URL ที่นี่ · อย่าใส่ไฟล์ .ex5/.zip ไว้ในโฟลเดอร์ธีม' ),
			'go_fast_label'          => array( 'ขั้นรับไฟล์ EA · ข้อความปุ่มดาวน์โหลด (ใช้เมื่อไม่มีรูปการ์ด)', 'text' ),
			'go_fast_img'            => array( 'ขั้นรับไฟล์ EA · รูปการ์ดดาวน์โหลด (ไม่บังคับ)', 'image', 'แนะนำภาพแนวนอน 1200×630px · มีรูป + ลิงก์ = แสดงเป็นการ์ดรูปแทนปุ่ม' ),
			'go_fast_alt'            => array( 'ขั้นรับไฟล์ EA · คำอธิบายรูปการ์ด (alt)', 'text' ),

			'go_show_install'        => array( 'แสดงขั้นติดตั้ง EA', 'checkbox', 'ปิดเป็นค่าเริ่มต้น (EA ทำงานบนเซิร์ฟเวอร์ของเรา ลูกค้าไม่ต้องติดตั้ง) · เลขขั้นและจำนวนขั้นในหัวข้อปรับเอง · ปุ่มคู่มือที่ชี้ไปหน้า /how-to-install/ ไม่แสดงแล้ว เพราะหน้านั้นถอดออกจากเว็บ ถ้าเปิดขั้นนี้ต้องใส่ลิงก์ปลายทางใหม่เอง' ),
			'go_step5_title'         => array( 'ขั้นติดตั้ง EA · หัวข้อ', 'text' ),
			'go_step5_desc'          => array( 'ขั้นติดตั้ง EA · คำอธิบาย', 'textarea' ),
			'go_step5_note'          => array( 'ขั้นติดตั้ง EA · หมายเหตุ', 'textarea' ),
			'go_step5_badge'         => array( 'ขั้นติดตั้ง EA · ป้ายเล็ก', 'text' ),
			'go_install1_label'      => array( 'ขั้นติดตั้ง EA · ปุ่มคู่มือ 1 · ข้อความ', 'text' ),
			'go_install1_url'        => array( 'ขั้นติดตั้ง EA · ปุ่มคู่มือ 1 · ลิงก์', 'path', $link_rule ),
			'go_install2_label'      => array( 'ขั้นติดตั้ง EA · ปุ่มคู่มือ 2 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
			'go_install2_url'        => array( 'ขั้นติดตั้ง EA · ปุ่มคู่มือ 2 · ลิงก์', 'path' ),

			'go_show_vps'            => array( 'แสดงขั้น VPS', 'checkbox', 'ปิดเป็นค่าเริ่มต้น (ลูกค้าไม่ต้องเช่า VPS) · ปุ่มที่ชี้ไปหน้าคู่มือ VPS ไม่แสดงแล้ว เพราะหน้าเหล่านั้นถอดออกจากเว็บ ถ้าเปิดขั้นนี้ต้องใส่ลิงก์ปลายทางใหม่เอง' ),
			'go_step6_title'         => array( 'ขั้น VPS · หัวข้อ', 'text' ),
			'go_step6_desc'          => array( 'ขั้น VPS · คำอธิบาย', 'textarea' ),
			'go_step6_note'          => array( 'ขั้น VPS · หมายเหตุ', 'textarea' ),
			'go_step6_badge'         => array( 'ขั้น VPS · ป้ายเล็ก', 'text' ),
			'go_vps_windows_label'   => array( 'ขั้น VPS · ปุ่ม Windows · ข้อความ', 'text' ),
			'go_vps_windows_url'     => array( 'ขั้น VPS · ปุ่ม Windows · ลิงก์', 'path', $link_rule ),
			'go_vps_android_label'   => array( 'ขั้น VPS · ปุ่ม Android · ข้อความ', 'text' ),
			'go_vps_android_url'     => array( 'ขั้น VPS · ปุ่ม Android · ลิงก์', 'path' ),
			'go_vps_ios_label'       => array( 'ขั้น VPS · ปุ่ม iPhone · ข้อความ', 'text' ),
			'go_vps_ios_url'         => array( 'ขั้น VPS · ปุ่ม iPhone · ลิงก์', 'path' ),
			'go_vps_macos_label'     => array( 'ขั้น VPS · ปุ่ม macOS · ข้อความ (เว้นว่าง = ซ่อน)', 'text', 'ค่าเริ่มต้นเว้นว่าง เพราะยังไม่มีคู่มือ VPS สำหรับ macOS · กรอกเมื่อมีหน้าคู่มือแล้วเท่านั้น' ),
			'go_vps_macos_url'       => array( 'ขั้น VPS · ปุ่ม macOS · ลิงก์ (ว่าง = ปุ่มเส้นประ "เร็ว ๆ นี้")', 'path' ),
		),
	);

	/* แทนหมวดเดิมในตำแหน่งเดิม แล้วต่อหมวดขั้นตอนไว้ถัดไป */
	$out   = array();
	$added = false;
	foreach ( $sections as $section_id => $section ) {
		if ( 'fenix_go' === $section_id ) {
			$out['fenix_go']       = $head;
			$out['fenix_go_steps'] = $steps;
			$added                 = true;
			continue;
		}
		if ( 'fenix_go_steps' === $section_id ) {
			continue;
		}
		$out[ $section_id ] = $section;
	}
	if ( ! $added ) {
		$out['fenix_go']       = $head;
		$out['fenix_go_steps'] = $steps;
	}

	/* คีย์ของหน้านี้อยู่ที่หมวด /go ที่เดียว (กันซ้ำจากหมวดอื่น) */
	$own = array_merge( array_keys( $head['fields'] ), array_keys( $steps['fields'] ) );
	foreach ( $out as $section_id => $section ) {
		if ( 'fenix_go' === $section_id || 'fenix_go_steps' === $section_id || empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
			continue;
		}
		foreach ( $own as $key ) {
			unset( $out[ $section_id ]['fields'][ $key ] );
		}
	}

	return $out;
}
add_filter( 'fenix_customizer_sections', 'fenix_go_sections', 20, 2 );

/* ==============================================================
 * ตัวช่วยของหน้า /go
 * ============================================================== */

/**
 * ค่า setting ที่ตัดช่องว่างแล้ว
 */
function fenix_go_mod( $key ) {
	return trim( (string) fenix_mod( $key ) );
}

/**
 * ข้อความของหน้า /go ที่ใช้ตัวแทน {brand} {broker} {server} {disclosure} ได้ (fenix_tokens ใน functions.php)
 * แทนค่าก่อน escape เสมอ · {disclosure} ว่างเมื่อยังไม่มีลิงก์สมัครบัญชี ช่องที่มีแต่ตัวแทนนี้จึงไม่แสดงอะไร
 * ใช้กับ: หัวข้อ / คำอธิบาย / หมายเหตุของแต่ละขั้น ข้อความปุ่มสมัครบัญชี และข้อความปุ่มฝากเงิน
 */
function fenix_go_text( $key ) {
	$text = fenix_go_mod( $key );
	return function_exists( 'fenix_tokens' ) ? trim( (string) fenix_tokens( $text ) ) : $text;
}

/**
 * แปลงลิงก์ที่ตั้งค่าไว้เป็น URL ที่กดได้จริง · '' = ยังไม่มีปลายทาง (แสดงเป็นปุ่ม "เร็ว ๆ นี้")
 * slug ของเพจในเว็บ (fenix_site_pages) ที่ยังไม่เผยแพร่ถือว่ายังไม่มีปลายทาง กันลิงก์ 404
 * นับรวมแถวใน manifest ที่ยังรอไฟล์เนื้อหา (fenix_site_pages( true )) · เพจแบบนั้นยังไม่ถูกสร้าง จึงต้องไม่กลายเป็นลิงก์
 */
function fenix_go_href( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url || '#' === $url ) {
		return '';
	}
	if ( preg_match( '#^/?([a-z0-9][a-z0-9\-]*)/?$#i', $url, $m ) && function_exists( 'fenix_site_pages' ) ) {
		$pages = fenix_site_pages( true );
		$slug  = strtolower( $m[1] );
		if ( isset( $pages[ $slug ] ) && empty( $pages[ $slug ]['front'] ) && function_exists( 'fenix_published_page_url' ) && '' === fenix_published_page_url( $slug ) ) {
			return '';
		}
	}
	return fenix_link_url( $url );
}

/**
 * ลิงก์ที่ตั้งค่าไว้ชี้ไปเพจที่ถอดออกจากเว็บแล้วหรือไม่ (ช่อง retired ของ manifest · เช่น /how-to-install/ /vps-windows/ /forward-test/)
 * ปุ่มแบบนี้ไม่ถูกพิมพ์เลย ต่างจากเพจที่ยังไม่เผยแพร่ซึ่งเป็นปุ่มเส้นประ "เร็ว ๆ นี้": เพจที่ถอดแล้วจะไม่กลับมา จึงไม่ควรบอกว่ากำลังจะมี
 * (ค่าที่เจ้าของเคยบันทึกไว้ใน Customizer อาจยังชี้ไปเพจเหล่านี้)
 */
function fenix_go_is_retired( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url || ! function_exists( 'fenix_is_retired_page' ) ) {
		return false;
	}
	/* URL เต็มของเว็บนี้ (https://เว็บ/how-to-install/) นับเหมือน /how-to-install/ */
	$home = untrailingslashit( home_url( '/' ) );
	if ( 0 === stripos( $url, $home . '/' ) ) {
		$url = substr( $url, strlen( $home ) );
	}
	return (bool) preg_match( '#^/?([a-z0-9][a-z0-9\-]*)/?$#i', $url, $m ) && fenix_is_retired_page( $m[1] );
}

/**
 * ลิงก์ออกนอกเว็บ (เปิดแท็บใหม่)
 */
function fenix_go_is_external( $href ) {
	if ( ! preg_match( '#^(https?:)?//#i', (string) $href ) ) {
		return false;
	}
	$host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$to   = wp_parse_url( ( 0 === strpos( $href, '//' ) ? 'https:' : '' ) . $href, PHP_URL_HOST );
	return $host && $to ? strtolower( $host ) !== strtolower( $to ) : true;
}

/**
 * attribute เพิ่ม (escape ในนี้)
 */
function fenix_go_attrs( $attrs ) {
	$out = '';
	foreach ( (array) $attrs as $name => $value ) {
		if ( true === $value ) {
			$out .= ' ' . esc_attr( $name );
		} elseif ( null !== $value && false !== $value ) {
			$out .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}
	}
	return $out;
}

/**
 * ป้าย "เร็ว ๆ นี้"
 */
function fenix_go_pending_tag() {
	$label = fenix_go_mod( 'go_pending_label' );
	return '' !== $label ? '<span class="lh-pending-tag">' . esc_html( $label ) . '</span>' : '';
}

/**
 * ปุ่มบนการ์ด · มีลิงก์ = <a> · มีข้อความแต่ไม่มีลิงก์ = ปุ่มเส้นประ กดไม่ได้ + ป้าย "เร็ว ๆ นี้" · ไม่มีข้อความ = ไม่แสดง
 *
 * @param string $class  lh-btn (เต็มความกว้าง) หรือ lh-mini-btn (ปุ่มรอง)
 * @param string $label  ข้อความ
 * @param string $url    ลิงก์ที่ตั้งค่าไว้ (slug หรือ URL เต็ม)
 * @param string $icon   ชื่อไอคอนของ fenix_icon()
 * @param array  $attrs  attribute เพิ่ม
 * @return string HTML ที่ escape แล้ว
 */
function fenix_go_button( $class, $label, $url, $icon, $attrs = array() ) {
	$label = trim( (string) $label );
	if ( '' === $label || fenix_go_is_retired( $url ) ) {
		return '';
	}
	$href   = fenix_go_href( $url );
	$is_btn = false !== strpos( ' ' . $class . ' ', ' lh-btn ' );

	if ( '' !== $href ) {
		$external = fenix_go_is_external( $href );
		if ( $external && ! isset( $attrs['target'] ) ) {
			$attrs['target'] = '_blank';
			$attrs['rel']    = isset( $attrs['rel'] ) ? $attrs['rel'] : 'noopener';
		}
		return sprintf(
			'<a class="%1$s" href="%2$s"%3$s><span class="lh-ic">%4$s</span><span class="lh-lbl">%5$s</span>%6$s</a>',
			esc_attr( $class ),
			esc_url( $href ),
			fenix_go_attrs( $attrs ),
			fenix_icon( $icon ),
			fenix_text( $label ),
			$is_btn ? '<span class="lh-ar" aria-hidden="true">' . fenix_icon( $external ? 'external' : 'arrow' ) . '</span>' : ''
		);
	}

	/* ลิงก์ที่ยังกดไม่ได้: บอกโปรแกรมอ่านหน้าจอว่าเป็นลิงก์ที่ปิดอยู่ (ไม่อยู่ในลำดับ Tab) */
	return sprintf(
		'<span class="%1$s is-pending" role="link" aria-disabled="true"><span class="lh-ic">%2$s</span><span class="lh-lbl">%3$s</span>%4$s</span>',
		esc_attr( $class ),
		fenix_icon( $icon ),
		fenix_text( $label ),
		fenix_go_pending_tag()
	);
}

/**
 * ปุ่ม LINE ภายในขั้นตอน (ขอลิงก์เปิดบัญชี / ขอไฟล์ EA) · แสดงเฉพาะเมื่อมีลิงก์ LINE จริง
 */
function fenix_go_line_button( $label, $pos ) {
	$label  = trim( (string) $label );
	$target = fenix_contact_target();
	if ( '' === $label || '' === $target['url'] || ! $target['is_line'] ) {
		return '';
	}
	return sprintf(
		'<a class="lh-btn lh-btn-line lh-btn-line-step" href="%1$s" target="_blank" rel="noopener" data-line-pos="%2$s">%3$s<span class="lh-lbl">%4$s</span></a>',
		esc_url( $target['url'] ),
		esc_attr( $pos ),
		fenix_icon( 'line' ),
		fenix_text( $label )
	);
}

/**
 * ตารางปุ่มตามอุปกรณ์ (แอป MT5 / คู่มือ VPS) · รายการ: array( label, url, icon )
 */
function fenix_go_tiles( $items, $class = '' ) {
	$html  = '';
	$count = 0;
	foreach ( $items as $item ) {
		$label = trim( (string) $item[0] );
		if ( '' === $label || fenix_go_is_retired( $item[1] ) ) {
			continue;
		}
		$href = fenix_go_href( $item[1] );
		/* ป้ายที่มี " / " (เช่น iPhone / iPad): ช่องว่างหน้าเครื่องหมายเป็นแบบไม่ตัดบรรทัด ป้ายในกระเบื้องแคบจึงตัดได้เฉพาะหลัง "/"
		   ไม่มีบรรทัดที่ขึ้นต้นด้วย "/" (เดิมตัดเป็น "iPhone" กับ "/ iPad") · ตัวหนังสือที่เห็นและที่โปรแกรมอ่านหน้าจออ่านยังเป็นคำเดิม */
		$label_html = str_replace( ' / ', "\u{00A0}/ ", esc_html( $label ) );
		++$count;
		if ( '' !== $href ) {
			$html .= sprintf(
				'<a class="lh-tile" href="%1$s"%2$s><span class="lh-tile-ic">%3$s</span><span class="lh-tile-lbl">%4$s</span></a>',
				esc_url( $href ),
				fenix_go_is_external( $href ) ? ' target="_blank" rel="noopener"' : '',
				fenix_icon( $item[2] ),
				$label_html
			);
			continue;
		}
		$html .= sprintf(
			'<span class="lh-tile is-pending" role="link" aria-disabled="true"><span class="lh-tile-ic">%1$s</span><span class="lh-tile-lbl">%2$s</span>%3$s</span>',
			fenix_icon( $item[2] ),
			$label_html,
			fenix_go_pending_tag()
		);
	}
	if ( ! $count ) {
		return '';
	}
	return '<div class="' . esc_attr( trim( 'lh-tiles lh-tiles--' . min( $count, 4 ) . ' ' . $class ) ) . '">' . $html . '</div>';
}

/**
 * การ์ด/ปุ่มดาวน์โหลดไฟล์ EA · ไม่มีลิงก์ = ''
 */
function fenix_go_download() {
	$href = fenix_go_href( fenix_go_mod( 'go_fast_url' ) );
	if ( '' === $href ) {
		return '';
	}
	$attrs = array();
	if ( preg_match( '/\.(zip|ex5|set|pdf)([?#]|$)/i', $href ) ) {
		$attrs['download'] = true;
	}
	$img = fenix_go_mod( 'go_fast_img' );
	if ( '' !== $img ) {
		return sprintf(
			'<a class="lh-feature" href="%1$s"%2$s%3$s><img src="%4$s" alt="%5$s" width="1200" height="630" loading="lazy" decoding="async"></a>',
			esc_url( $href ),
			fenix_go_attrs( $attrs ),
			fenix_go_is_external( $href ) ? ' target="_blank" rel="noopener"' : '',
			esc_url( $img ),
			esc_attr( fenix_go_mod( 'go_fast_alt' ) )
		);
	}
	return fenix_go_button( 'lh-btn lh-btn-accent', fenix_go_mod( 'go_fast_label' ), fenix_go_mod( 'go_fast_url' ), 'download', $attrs );
}

/**
 * แสดงการ์ดแดชบอร์ดบนหน้านี้ไหม: เปิดช่อง go_show_dashboard + มีโมดูล dashboard + แดชบอร์ดเปิดใช้อยู่ (หมวด 1.1)
 */
function fenix_go_dashboard_on() {
	return (bool) fenix_mod( 'go_show_dashboard' ) && function_exists( 'fenix_dashboard_block' ) && function_exists( 'fenix_dashboard_enabled' ) && fenix_dashboard_enabled();
}

/**
 * ปุ่มไปแดชบอร์ดในขั้นสุดท้ายของเส้นทาง (หน้าตาแบบปุ่มของหน้านี้: วงไอคอน + ข้อความ + ลูกศรออกนอกเว็บ)
 * ที่อยู่และข้อความปุ่มมาจากโมดูล dashboard (หมวด 1.1): fenix_dashboard_url( $mode ) + fenix_dashboard_label( $mode )
 * ข้อความปุ่มของโหมดนั้นว่าง หรือปิดแดชบอร์ด = '' (กติกาเดียวกับ fenix_dashboard_button)
 * แอตทริบิวต์ data-dash / data-dash-pos แบบเดียวกับ fenix_dashboard_button: main.js นับคลิกเป็น dashboard_click (dash_mode · dash_pos)
 *
 * @param string $mode  visit | signup
 * @param string $class คลาสของปุ่ม (lh-btn …)
 * @param string $icon  ชื่อไอคอนของ fenix_icon()
 * @return string HTML ที่ escape แล้ว
 */
function fenix_go_dash_button( $mode, $class, $icon ) {
	if ( ! function_exists( 'fenix_dashboard_url' ) || ! function_exists( 'fenix_dashboard_label' ) ) {
		return '';
	}
	$url   = (string) fenix_dashboard_url( $mode );
	$label = (string) fenix_dashboard_label( $mode );
	if ( '' === $url || '' === $label ) {
		return '';
	}
	$hint = function_exists( 'fenix_dashboard_text' ) ? (string) fenix_dashboard_text( 'dashboard_newtab_hint' ) : '';
	return sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="noopener" data-dash="%3$s" data-dash-pos="go-steps"><span class="lh-ic">%4$s</span><span class="lh-lbl">%5$s%6$s</span><span class="lh-ar" aria-hidden="true">%7$s</span></a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $mode ),
		fenix_icon( $icon ),
		fenix_text( $label ),
		'' !== $hint ? '<span class="screen-reader-text"> (' . esc_html( $hint ) . ')</span>' : '',
		fenix_icon( 'external' )
	);
}

/**
 * เนื้อหาของขั้น "เริ่มใช้บนแดชบอร์ด": ปุ่มเข้าชม (ปุ่มเด่น) + ปุ่มสมัคร + หมายเหตุของโมดูล dashboard + ปุ่มไปหน้า /dashboard/ ของเว็บนี้
 * ไม่มีปุ่มเข้าชมและปุ่มสมัครทั้งคู่ (ปิดแดชบอร์ด หรือเจ้าของลบข้อความปุ่มทั้งสอง) = '' ขั้นนี้จึงไม่ถูกพิมพ์
 * หมายเหตุ: ข้อมูลในโหมดเยี่ยมชมเป็นตัวอย่าง (แสดงคู่กับปุ่มเข้าชมเสมอ) · การสมัครต้องใช้รหัสเชิญ · สถานะการเปิดให้บริการ (เมื่อเจ้าของกรอกไว้)
 */
function fenix_go_dash_body() {
	if ( ! function_exists( 'fenix_dashboard_enabled' ) || ! fenix_dashboard_enabled() ) {
		return '';
	}
	$body = fenix_go_dash_button( 'visit', 'lh-btn lh-btn-accent lh-btn-dash', 'monitor' ) . fenix_go_dash_button( 'signup', 'lh-btn lh-btn-dash', 'user' );
	if ( '' === $body ) {
		return '';
	}
	if ( function_exists( 'fenix_dashboard_notes' ) ) {
		$body .= fenix_dashboard_notes( array( 'sample', 'invite', 'status' ), 'lh-dash-notes' );
	}
	/* ปุ่มคู่มือของขั้นนี้พิมพ์เมื่อปลายทางเปิดได้จริงเท่านั้น (หน้า /dashboard/ ยังไม่เผยแพร่ = ไม่มีปุ่ม ไม่ใช่ปุ่มเส้นประ) */
	if ( '' !== fenix_go_href( fenix_go_mod( 'go_dash_guide_url' ) ) ) {
		$body .= fenix_go_button( 'lh-mini-btn', fenix_go_mod( 'go_dash_guide_label' ), fenix_go_mod( 'go_dash_guide_url' ), 'book' );
	}
	return $body;
}

/**
 * ขั้นตอนเริ่มใช้งาน · ขั้นที่ไม่มีปุ่มถูกตัดออก ลำดับใน array = เลขที่แสดง
 * ค่าเริ่มต้น 4 ขั้น: เปิดบัญชี → แอป MT5 → ฝากเงิน → เริ่มใช้บนแดชบอร์ด
 * ขั้นรับไฟล์ EA · ติดตั้ง EA · VPS (ของทางติดตั้งเองเดิม) พิมพ์เฉพาะเมื่อเจ้าของติ๊กเปิด และอยู่ก่อนขั้นแดชบอร์ดเสมอ
 *
 * @return array[] { key, prefix (ต้นชื่อ setting: {prefix}_title / _desc / _note / _badge), n (เลขของคีย์ go_stepN_* · 0 = ขั้นแดชบอร์ด), body (HTML ที่ escape แล้ว) }
 */
function fenix_go_steps() {
	$steps = array();

	// 1 · เปิดบัญชี: ปุ่มสมัคร (หมวดโบรกเกอร์) หรือปุ่ม LINE ขอลิงก์ + ปุ่มคู่มือ.
	$signup_url   = fenix_go_mod( 'broker_signup_url' );
	$signup_label = fenix_go_text( 'broker_signup_text' ); // {broker} ในข้อความปุ่ม → ชื่อจริง (ตรงกับปุ่มของ [falcon_broker field="url"])
	$body         = '';
	if ( function_exists( 'fenix_partner_ready' ) && fenix_partner_ready() && '' !== fenix_go_href( $signup_url ) && '' !== $signup_label ) {
		$href  = fenix_go_href( $signup_url );
		$body .= sprintf(
			'<a class="lh-btn lh-btn-signup" href="%1$s" target="_blank" rel="sponsored noopener" data-go-pos="go-signup"><span class="lh-ic">%2$s</span><span class="lh-lbl">%3$s</span><span class="lh-ar" aria-hidden="true">%4$s</span></a>',
			esc_url( $href ),
			fenix_icon( 'user' ),
			fenix_text( $signup_label ),
			fenix_icon( 'external' )
		);
	} else {
		$body .= fenix_go_line_button( fenix_go_mod( 'go_signup_line_label' ), 'go-signup' );
	}
	$body   .= fenix_go_button( 'lh-mini-btn', fenix_go_mod( 'go_account_guide_label' ), fenix_go_mod( 'go_account_guide_url' ), 'book' );
	$steps[] = array( 'key' => 'account', 'prefix' => 'go_step1', 'n' => 1, 'body' => $body );

	// 2 · แอป MT5 ตามอุปกรณ์ + คู่มือล็อกอิน.
	$body  = fenix_go_tiles(
		array(
			array( fenix_go_mod( 'go_mt5_ios_label' ), fenix_go_mod( 'mt5_dl_ios' ), 'apple' ),
			array( fenix_go_mod( 'go_mt5_android_label' ), fenix_go_mod( 'mt5_dl_android' ), 'android' ),
			array( fenix_go_mod( 'go_mt5_windows_label' ), fenix_go_mod( 'mt5_dl_windows' ), 'windows' ),
			array( fenix_go_mod( 'go_mt5_macos_label' ), fenix_go_mod( 'go_mt5_macos_url' ), 'macos' ),
		),
		'lh-tiles--apps'
	);
	$body   .= fenix_go_button( 'lh-mini-btn', fenix_go_mod( 'go_mt5_login_label' ), fenix_go_mod( 'go_mt5_login_url' ), 'book' );
	$steps[] = array( 'key' => 'mt5', 'prefix' => 'go_step2', 'n' => 2, 'body' => $body );

	// 3 · ฝากเงิน: ปุ่มหลัก (หน้าสมาชิกของโบรกเกอร์) แสดงเมื่อมีลิงก์เท่านั้น · ลิงก์ธรรมดา ไม่ใช่ลิงก์สมัคร (rel noopener อย่างเดียว).
	// ปุ่มคู่มือแสดงเมื่อหน้าคู่มือเปิดได้จริง หรือมีปุ่มหลักอยู่แล้ว · ไม่ให้ขั้นนี้โผล่มาพร้อมปุ่มเส้นประ "เร็ว ๆ นี้" ปุ่มเดียว
	// (เช่น ธีมอัปเดตแล้วแต่ยังไม่ได้กด FALCON Setup สร้างหน้า /deposit/) → กรณีนั้นซ่อนทั้งขั้น.
	$body        = '';
	$portal_text = fenix_go_text( 'go_deposit_label' ); // {broker} ในข้อความปุ่ม → ชื่อจริง
	$has_portal  = '' !== $portal_text && '' !== fenix_go_href( fenix_go_mod( 'go_deposit_url' ) );
	$guide_ready = '' !== fenix_go_href( fenix_go_mod( 'go_deposit_guide_url' ) );
	if ( $has_portal ) {
		$body .= fenix_go_button( 'lh-btn lh-btn-accent', $portal_text, fenix_go_mod( 'go_deposit_url' ), 'dollar' );
	}
	if ( $has_portal || $guide_ready ) {
		$body .= fenix_go_button( 'lh-mini-btn', fenix_go_mod( 'go_deposit_guide_label' ), fenix_go_mod( 'go_deposit_guide_url' ), 'book' );
	}
	$steps[] = array( 'key' => 'deposit', 'prefix' => 'go_step3', 'n' => 3, 'body' => $body );

	// ไฟล์ EA (เฉพาะเมื่อเปิดช่อง go_show_download · ค่าเริ่มต้นปิด): การ์ด/ปุ่มดาวน์โหลด หรือปุ่ม LINE ขอไฟล์.
	if ( fenix_mod( 'go_show_download' ) ) {
		$body = fenix_go_download();
		if ( '' === $body ) {
			$body = fenix_go_line_button( fenix_go_mod( 'go_download_line_label' ), 'go-download' );
		}
		$steps[] = array( 'key' => 'download', 'prefix' => 'go_step4', 'n' => 4, 'body' => $body );
	}

	// ติดตั้ง EA (เฉพาะเมื่อเปิดช่อง go_show_install · ค่าเริ่มต้นปิด).
	if ( fenix_mod( 'go_show_install' ) ) {
		$body    = fenix_go_button( 'lh-btn', fenix_go_mod( 'go_install1_label' ), fenix_go_mod( 'go_install1_url' ), 'gear' );
		$body   .= fenix_go_button( 'lh-btn', fenix_go_mod( 'go_install2_label' ), fenix_go_mod( 'go_install2_url' ), 'calc' );
		$steps[] = array( 'key' => 'install', 'prefix' => 'go_step5', 'n' => 5, 'body' => $body );
	}

	// 6 · VPS ตามอุปกรณ์ที่ใช้เชื่อมต่อ (เฉพาะเมื่อเปิดช่อง go_show_vps).
	$body = ! fenix_mod( 'go_show_vps' ) ? '' : fenix_go_tiles(
		array(
			array( fenix_go_mod( 'go_vps_windows_label' ), fenix_go_mod( 'go_vps_windows_url' ), 'windows' ),
			array( fenix_go_mod( 'go_vps_android_label' ), fenix_go_mod( 'go_vps_android_url' ), 'android' ),
			array( fenix_go_mod( 'go_vps_ios_label' ), fenix_go_mod( 'go_vps_ios_url' ), 'apple' ),
			array( fenix_go_mod( 'go_vps_macos_label' ), fenix_go_mod( 'go_vps_macos_url' ), 'macos' ),
		),
		'lh-tiles--vps'
	);
	if ( '' !== $body ) {
		$steps[] = array( 'key' => 'vps', 'prefix' => 'go_step6', 'n' => 6, 'body' => $body );
	}

	// ขั้นสุดท้าย · เริ่มใช้บนแดชบอร์ด: ปุ่มเข้าชมก่อนสมัคร + ปุ่มสมัคร (คีย์ go_dash_* · ไม่มีปุ่ม = ขั้นนี้ถูกตัดออกด้านล่าง).
	$steps[] = array( 'key' => 'dashboard', 'prefix' => 'go_dash', 'n' => 0, 'body' => fenix_go_dash_body() );

	return array_values(
		array_filter(
			$steps,
			static function ( $step ) {
				return '' !== trim( $step['body'] ) && '' !== fenix_go_text( $step['prefix'] . '_title' );
			}
		)
	);
}

/**
 * ปุ่มกลุ่มข้อมูลก่อนเริ่ม (1–6)
 */
function fenix_go_info_buttons() {
	$icons = array( 1 => 'calc', 2 => 'flask', 3 => 'tag', 4 => 'users', 5 => 'book', 6 => 'arrow' );
	$html  = '';
	for ( $i = 1; $i <= 6; $i++ ) {
		$html .= fenix_go_button( 'lh-btn', fenix_go_mod( 'go_btn' . $i . '_label' ), fenix_go_mod( 'go_btn' . $i . '_url' ), $icons[ $i ] );
	}
	return $html;
}

/**
 * โซเชียลที่กรอกลิงก์ไว้ (facebook_url จากหมวด 1 · instagram/tiktok/youtube จากโมดูล chrome)
 *
 * @return array[] { name, url, label }
 */
function fenix_go_socials() {
	$out = array();
	foreach ( array(
		'facebook'  => array( 'facebook_url', 'footer_facebook_text', 'Facebook' ),
		'instagram' => array( 'instagram_url', 'footer_instagram_text', 'Instagram' ),
		'tiktok'    => array( 'tiktok_url', 'footer_tiktok_text', 'TikTok' ),
		'youtube'   => array( 'youtube_url', 'footer_youtube_text', 'YouTube' ),
	) as $name => $social ) {
		$url = fenix_go_mod( $social[0] );
		if ( '' === $url || '#' === $url ) {
			continue;
		}
		/* ชื่อช่องทาง (aria-label) ใช้ข้อความเดียวกับท้ายเว็บ · ไม่มี = ชื่อแพลตฟอร์ม */
		$label = fenix_go_mod( $social[1] );
		$out[] = array(
			'name'  => $name,
			'url'   => $url,
			'label' => '' !== $label ? $label : $social[2],
		);
	}
	return $out;
}

/**
 * ลิงก์เอกสารท้ายการ์ด · เฉพาะเพจที่เผยแพร่แล้ว ใช้ชื่อเพจจริง
 *
 * @return array[] { url, label }
 */
function fenix_go_legal_links() {
	$pages = function_exists( 'fenix_site_pages' ) ? fenix_site_pages() : array();
	$out   = array();
	foreach ( array( 'privacy-policy', 'terms-of-use', 'risk-disclosure' ) as $slug ) {
		$url = function_exists( 'fenix_published_page_url' ) ? fenix_published_page_url( $slug ) : '';
		if ( '' === $url ) {
			continue;
		}
		$page  = get_page_by_path( $slug );
		$label = $page ? trim( (string) get_post_field( 'post_title', $page ) ) : '';
		if ( '' === $label && isset( $pages[ $slug ]['title'] ) ) {
			$label = $pages[ $slug ]['title'];
		}
		if ( '' !== $label ) {
			$out[] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}
	return $out;
}

/**
 * โลโก้วงกลม · ไม่ตั้งค่า = ตราเหยี่ยวของธีม (180px สำหรับกรอบ 84px)
 */
function fenix_go_logo_url() {
	$logo = fenix_go_mod( 'go_logo' );
	return '' !== $logo ? $logo : get_template_directory_uri() . '/assets/img/brand/falcon-pro-icon-180.png';
}

/**
 * คำที่ห้ามตัดบรรทัดกลางคำบนหน้า /go (การ์ดกว้างไม่เกิน 460px ตัดบรรทัดบ่อย)
 * template-go.php เพิ่มฟิลเตอร์ fenix_keep_words เฉพาะตอนแสดงหน้านี้ · "|" = รอยต่อที่เบราว์เซอร์มักตัดผิด
 */
function fenix_go_keep_words( $words ) {
	return array_merge(
		(array) $words,
		array( 'ยืนยัน|ตัว|ตน', 'ต่อ|ไป', 'ค่า|เริ่ม|ต้น', 'เริ่ม|ต้น', 'ติด|ตั้ง', 'เซิร์ฟ|เวอร์', 'คอม|พิว|เตอร์', 'ด้าน|ล่าง', 'รหัส|ผ่าน', 'ของ|เรา', 'เงิน|จริง' )
	);
}

/**
 * คลาสของ <body> บนหน้านี้ (template-go.php เพิ่มฟิลเตอร์ก่อนเรียก body_class)
 */
function fenix_go_body_class( $classes ) {
	$classes[] = 'link-hub-page';
	$classes[] = function_exists( 'fenix_is_dark_mode' ) && fenix_is_dark_mode() ? 'link-hub--dark' : 'link-hub--light';
	/* หน้านี้ไม่มีบาร์ล่างมือถือ · เอาคลาสที่ใช้เว้นที่ให้บาร์ (โมดูล chrome) ออก */
	return array_values( array_unique( array_diff( $classes, array( 'has-dock', 'has-mobile-app-nav' ) ) ) );
}

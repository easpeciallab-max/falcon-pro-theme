<?php
/**
 * FALCON PRO EA · โมดูลหน้าแรก (front-page.php)
 *
 * โครงหน้าแรกแบบ hero + 9 บทมีเลข หน้าตาแบบ FALCON:
 * hero → 01 ระบบคืออะไร → 02 จุดที่แผนมักหลุด → 03 วงจรการทำงาน → 04 โมดูล → 05 การทดสอบ
 * → 06 ติดตั้ง → 07 แพ็กเกจ → 08 ถาม-ตอบ → 09 ความเสี่ยง (แถบเตือนเต็มความกว้าง นับเป็นบทที่ 09)
 *
 * - ค่าเริ่มต้นของหน้าแรก: fenix_home_defaults() (ฟิลเตอร์ fenix_defaults)
 * - หมวดใน Customizer เรียงตามลำดับบท: fenix_home_customizer_sections() (ฟิลเตอร์ fenix_customizer_sections)
 * - ตัวช่วยที่ front-page.php ใช้: fenix_home_chapters(), fenix_home_tone(), fenix_home_ch_head(),
 *   fenix_home_spec_items(), fenix_home_log_lines(), fenix_home_contact_link() ฯลฯ
 * - บล็อกเดิมที่ถอดออกจากหน้าแรก (แถบไฮไลต์, แกลเลอรี, ผลทดสอบ, เหมาะกับใคร, รีวิว, ทีมงาน, ความมั่นใจ,
 *   การ์ดนำทาง, บทความล่าสุด, แถบกลางหน้า, CTA ท้ายหน้า) ยังเก็บค่าไว้ในหมวด "ค่าเดิมที่ไม่ได้แสดงแล้ว"
 *   (CTA ท้ายหน้าย้ายไปเป็นแถวติดต่อใน footer ของโมดูล chrome)
 *
 * กฎ: ไม่มีตัวเลขผลเทรด ไม่มีคำรับประกันกำไร ไม่ลดทอนคำเตือนความเสี่ยง · ข้อความทุกคำมาจาก setting
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น (ข้อความไทยของ FALCON เอง · ไม่คัดลอกจากแบรนด์อื่น)
 * ============================================================== */

add_filter( 'fenix_defaults', 'fenix_home_defaults' );

function fenix_home_defaults( $d ) {
	$banners = get_template_directory_uri() . '/assets/img/banners/';
	$note    = 'ภาพประกอบ ตัวเลขที่เห็นบนจอใช้สาธิตหน้าตาโปรแกรมเท่านั้น ไม่ใช่ผลการเทรดจริง';
	$specs   = array(
		'แพลตฟอร์ม|MetaTrader 5 บนเครื่องของคุณหรือ VPS',
		'สินทรัพย์|คู่เงิน ทองคำ และสัญลักษณ์ในบัญชี MT5',
		'หลักการ|ออกคำสั่งเมื่อกติกาครบทุกข้อ',
		'ชุดที่ได้รับ|ไฟล์ EA · Preset · คู่มือไทย',
	);
	$spec_note = 'แถบนี้สรุปคุณสมบัติของโปรแกรม ไม่มีตัวเลขผลการเทรด';

	return array_merge(
		$d,
		array(

			/* ---------- ทั่วทั้งหน้าแรก ---------- */
			'home_show_rail'  => true,
			'home_rail_label' => 'สารบัญบทในหน้าแรก',
			'home_fig_label'  => 'ภาพ',

			/* ---------- Hero ---------- */
			'hero_badge'           => 'EA บน MetaTrader 5 · ทีมงานไทยดูแลการติดตั้ง',
			'hero_desc'            => 'ตั้งกติกาการเข้าและออกออเดอร์ไว้ครั้งเดียว แล้วให้ FALCON PRO EA เฝ้ากราฟ MT5 และลงมือตามกติกานั้นทุกรอบ คุณเลือกสินทรัพย์ กำหนดทุน และขีดเส้นความเสี่ยงเอง ระบบไม่คาดเดาตลาดแทนคุณ',
			'hero_btn1_text'       => 'ปรึกษาทีม FALCON ทาง LINE',
			'hero_btn2_text'       => 'ดูว่าระบบทำงานอย่างไร',
			'hero_note'            => 'การเทรดมีความเสี่ยง เงินทุนลดลงได้บางส่วนหรือทั้งหมด ตัวเลขในอดีตไม่ใช่คำสัญญาของผลครั้งต่อไป ก่อนเริ่มใช้งานควรอ่านประกาศความเสี่ยงให้ครบ',
			'home_hero_img_alt'    => 'ภาพประกอบ FALCON PRO EA ที่ใช้งานร่วมกับ MetaTrader 5',
			'home_hero_spec_items' => implode( "\n", $specs ),
			'home_hero_spec_note'  => $spec_note,

			/* Hero · แผงควบคุมจำลอง (แสดงเมื่อไม่ได้ใส่ภาพ Hero) · ไม่มีสถานะที่อ่านแล้วเหมือนกำลังเทรดจริง */
			'hero_panel_status'   => 'ภาพจำลองหน้าจอ',
			'hero_panel_fields'   => "สินทรัพย์|เลือกเองได้\nขนาด Lot|คำนวณจากทุน\nความเสี่ยง|คุณเป็นคนตั้ง\nโหมด|ทำตามกติกา",
			'hero_panel_button'   => 'ทำงานตามกติกาที่คุณตั้ง',
			'hero_panel_tags'     => 'มีวินัย · เป็นระบบ · สม่ำเสมอ',
			'home_hero_panel_sub' => 'EA อัตโนมัติ',

			/* แถบไฮไลต์เดิม (หน้าแรกไม่แสดงแล้ว · footer ใช้เป็นค่าสำรองของ "ข้อมูลระบบ") · ให้ตรงกับแถบข้อมูลใน Hero */
			'highlight1'     => $specs[0],
			'highlight2'     => $specs[1],
			'highlight3'     => $specs[2],
			'highlight4'     => $specs[3],
			'highlight_note' => $spec_note,

			/* ---------- 01 ระบบคืออะไร ---------- */
			'home_what_kicker'          => 'รู้จักระบบ',
			'about_title'               => 'FALCON PRO EA คืออะไร · EA ที่ทำงานตามกติกาบน MetaTrader 5',
			'about_text'                => "FALCON PRO EA เป็นโปรแกรมประเภท Expert Advisor (EA) หรือที่นักเทรดไทยเรียกกันว่าบอทเทรด ติดตั้งอยู่บนกราฟของ MetaTrader 5 คอยอ่านราคาทุกครั้งที่ราคาขยับ และจะส่งคำสั่งซื้อขายก็ต่อเมื่อเงื่อนไขที่กำหนดไว้ครบทุกข้อ ขนาดออเดอร์และจุดปิดคำนวณจากค่าที่คุณตั้ง ไม่ได้ขึ้นกับอารมณ์ของวันนั้น\n\nประโยชน์หลักคือแผนเทรดของคุณถูกใช้ซ้ำแบบเดียวกันทุกวัน รวมถึงช่วงที่คุณไม่ได้เปิดหน้าจอ อย่างไรก็ตาม EA ไม่ได้ลบความเสี่ยงออกจากการเทรด ผลลัพธ์ยังขึ้นกับจังหวะตลาด ต้นทุนการเทรดของแต่ละโบรกเกอร์ และค่าตั้งที่คุณเลือก",
			'about_points'              => "ที่ทำงาน|MT5 บนเครื่องที่เปิดค้างไว้ หรือเซิร์ฟเวอร์ VPS\nหลักคิด|ยึดเงื่อนไขที่เขียนไว้ ไม่ไล่ราคา ไม่เพิ่ม Lot เพื่อเอาคืน\nผู้ควบคุม|คุณกำหนดทุน ขนาด Lot และเพดานขาดทุน กดหยุดได้ทุกเมื่อ\nการดูแล|คู่มือติดตั้งฉบับภาษาไทย และทีมตอบแชทผ่าน LINE",
			'home_what_principle_label' => 'สิ่งที่เรายึดถือ',
			'home_what_principle'       => 'พูดถึงข้อจำกัดของระบบก่อนข้อดี และจะไม่เผยแพร่ตัวเลขใดที่ตรวจสอบย้อนกลับไม่ได้',
			'home_what_img'             => $banners . 'falcon-pro-ea-mt5-laptop-overview.webp',
			'home_what_img_mobile'      => '',
			'home_what_img_alt'         => 'แล็ปท็อปที่เปิด MetaTrader 5 คู่กับหน้าจอ FALCON PRO EA (ภาพประกอบ)',
			'home_what_img_caption'     => $note,
			'home_what_img_note'        => 'รูปที่แนะนำ: จอ MT5 ขณะ FALCON PRO EA ทำงาน (ปิดเลขบัญชีได้) ขนาดราว 1280x800 px',

			/* ---------- 02 จุดที่แผนมักหลุด ---------- */
			'home_pain_kicker'         => 'จุดที่แผนมักหลุด',
			'pain_title'               => 'EA เทรด Forex ช่วยอะไรได้บ้าง · สี่จุดที่แผนเทรดมือมักหลุด',
			'pain_subtitle'            => 'ถ้าเคยเจอข้อใดข้อหนึ่งด้านล่าง นั่นคือช่องว่างที่ระบบทำตามกติกาออกแบบมาปิด ส่วนการตัดสินใจเรื่องเงินทุนยังอยู่ที่คุณ',
			'pain1_title'              => 'ตัดสินใจตามความรู้สึก',
			'pain1_desc'               => 'เห็นกราฟพุ่งแล้วรีบตาม เห็นติดลบนิดเดียวก็รีบปิด จังหวะเข้าออกจริงจึงคลาดจากแผนที่เขียนไว้',
			'pain2_title'              => 'ไม่มีเวลานั่งดูกราฟ',
			'pain2_desc'               => 'ราคาเคลื่อนไหวตอนคุณทำงานหรือพักผ่อน สัญญาณที่รอมาทั้งสัปดาห์อาจเกิดขึ้นตอนที่คุณไม่ได้เปิดจอ',
			'pain3_title'              => 'ขนาดออเดอร์ไม่คงที่',
			'pain3_desc'               => 'บางวันใช้ Lot เล็ก บางวันขยายขึ้นเพื่อเอาคืน พอร์ตจึงแกว่งกว้างกว่าระดับที่ตั้งใจรับไว้ตั้งแต่ต้น',
			'pain4_title'              => 'แผนดีแต่ทำได้ไม่ต่อเนื่อง',
			'pain4_desc'               => 'กฎที่ตั้งไว้ใช้ได้สองสามวันแล้วเริ่มมีข้อยกเว้น จนสุดท้ายแยกไม่ออกว่าผลที่ได้มาจากแผนหรือมาจากโชค',
			'home_pain_resolved_label' => 'ส่วนที่ FALCON PRO EA รับไปทำ',
			'home_pain_resolved_text'  => 'ตรวจเงื่อนไขชุดเดิมทุกครั้งที่ตลาดเปิดโดยไม่มีข้อยกเว้น ส่วนเงินทุนที่ใช้ เพดานความเสี่ยง และการสั่งเริ่มหรือหยุดระบบยังอยู่ในมือคุณ',

			/* ---------- 03 วงจรการทำงาน ---------- */
			'steps_kicker'          => 'วงจรการทำงาน',
			'steps_title'           => 'FALCON PRO EA ทำงานบน MT5 อย่างไร · 4 ขั้นที่วนซ้ำทุกวัน',
			'steps_subtitle'        => 'ตั้งค่ารอบแรกให้เข้ากับบัญชี จากนั้นระบบจะวนตรวจเงื่อนไข ส่งคำสั่ง และแสดงสถานะตามลำดับเดิม คุณเข้ามาดูและปรับค่าได้ตลอด',
			'step1_title'           => 'กำหนดทุนและเพดานความเสี่ยง',
			'step1_desc'            => 'ใส่ทุนที่จะให้ระบบใช้ เลือกขนาด Lot และระดับการย่อตัวของพอร์ตที่รับได้ ถ้ายังไม่แน่ใจ ทีมงานมีไฟล์ Preset ตั้งต้นให้เลือกตามขนาดบัญชี',
			'step2_title'           => 'อ่านราคาและเทียบกับเงื่อนไข',
			'step2_desc'            => 'ระหว่างที่ตลาดเปิด EA ตรวจราคาบนกราฟแล้วเทียบกับกติกาที่ตั้งไว้ ถ้าเงื่อนไขยังไม่ครบ ระบบจะรอต่อโดยไม่ส่งคำสั่งใด ๆ',
			'step3_title'           => 'ส่งคำสั่งและดูแลออเดอร์',
			'step3_desc'            => 'เมื่อเงื่อนไขครบ ระบบส่งคำสั่งไปยังโบรกเกอร์ด้วยขนาดที่คำนวณจากค่าของคุณ และปิดออเดอร์ตามกติกาที่วางไว้ตั้งแต่แรก',
			'step4_title'           => 'ตรวจสถานะและทบทวนค่า',
			'step4_desc'            => 'ดูสถานะระบบจากแผงบนกราฟ เช็กยอดบัญชีจากแอป MT5 ในโทรศัพท์ และทักทีมงานทาง LINE เมื่ออยากทบทวนค่าที่ใช้อยู่',
			'home_show_how_log'     => true,
			'home_how_log_title'    => 'บันทึกการทำงาน · ภาพจำลอง',
			'home_how_log_prompt'   => 'falcon@mt5:~$',
			'home_how_log_start'    => 'เริ่มรอบการทำงาน',
			'home_how_log_lines'    => '',
			'home_how_log_ready'    => 'เฝ้ากราฟต่อ · ยังไม่ส่งคำสั่งจนกว่ากติกาจะครบ',
			'steps_checklist_title' => 'เตรียมสี่อย่างนี้ก่อนเริ่ม',
			'steps_checklist'       => "บัญชี MT5 ที่โบรกเกอร์อนุญาตให้รัน EA\nเงินทุนที่ขาดทุนได้โดยไม่กระทบค่าใช้จ่ายประจำ\nเครื่องที่เปิด MT5 ค้างไว้ได้ หรือ VPS\nเวลาอ่านคู่มือ และลองกับบัญชีเดโมก่อน",
			'home_how_img'          => $banners . 'falcon-pro-ea-mt5-settings-panel.webp',
			'home_how_img_mobile'   => '',
			'home_how_img_alt'      => 'แผงตั้งค่า FALCON PRO EA ช่องสินทรัพย์ ขนาด Lot ความเสี่ยง และโหมด (ภาพประกอบ)',
			'home_how_img_caption'  => 'ภาพประกอบแผงตั้งค่า ไม่ใช่ผลการเทรดจริง',
			'home_how_img_note'     => 'รูปที่แนะนำ: กราฟ MT5 ที่ติด FALCON PRO EA แล้ว เห็นแผงสถานะชัด ๆ (เบลอเลขบัญชีได้) ขนาดราว 1280x800 px',

			/* ---------- 04 โมดูล ---------- */
			'home_features_kicker'   => 'ส่วนประกอบหลัก',
			'home_feat_module_label' => 'โมดูล',
			'features_title'         => 'หกโมดูลหลักที่ FALCON PRO EA ใช้ทำงานทุกวัน',
			'features_subtitle'      => 'แต่ละส่วนออกแบบให้คุณตรวจได้ว่าระบบกำลังทำอะไร และให้คุณเป็นคนกำหนดขอบเขตของเงินทุนเสมอ',
			'feat1_title'            => 'ส่งคำสั่งตามเงื่อนไขล้วน',
			'feat1_desc'             => 'เปิดหรือปิดออเดอร์ก็ต่อเมื่อกติกาครบ ไม่มีการเข้าเพิ่มเพราะราคาวิ่งแรงหรือเพราะอยากเอาคืน',
			'feat2_title'            => 'พัฒนาเพื่อ MetaTrader 5',
			'feat2_desc'             => 'เขียนด้วยภาษา MQL5 ติดตั้งเป็นไฟล์ .ex5 ใช้ได้ทั้งบัญชีเดโมและบัญชีจริงของโบรกเกอร์ที่เปิดให้ใช้ EA',
			'feat3_title'            => 'หน้าจอสรุปสถานะในตัว',
			'feat3_desc'             => 'รู้ได้ทันทีว่าระบบเปิดทำงานอยู่ไหม มีออเดอร์ค้างอยู่เท่าไร และกำลังใช้ค่าตั้งชุดใด โดยไม่ต้องสลับหน้าต่าง',
			'feat4_title'            => 'เพดานความเสี่ยงที่คุณตั้งเอง',
			'feat4_desc'             => 'กำหนดขนาด Lot ต่อออเดอร์และระดับการย่อตัวของพอร์ตที่ยอมรับได้ ให้สัมพันธ์กับทุนที่ใช้จริง',
			'feat5_title'            => 'เดินตามแผนแม้คุณไม่อยู่หน้าจอ',
			'feat5_desc'             => 'ตราบใดที่ MT5 บนเครื่องของคุณหรือบน VPS ยังเปิดอยู่ ระบบเดินต่อได้ระหว่างที่คุณเดินทาง ทำงาน หรือพักผ่อน',
			'feat6_title'            => 'ทีมไทยช่วยดูการตั้งค่า',
			'feat6_desc'             => 'ติดตั้งไม่ผ่านหรือไม่แน่ใจค่าไหน ส่งภาพหน้าจอให้ทีมงานทาง LINE ช่วยไล่ดูทีละขั้นได้',

			/* ---------- 05 การทดสอบ ---------- */
			'tests_kicker'            => 'ทดสอบก่อนใช้จริง',
			'tests_title'             => 'Backtest กับ Forward Test ต่างกันอย่างไร · วิธีทดสอบ EA ก่อนใช้เงินจริง',
			'tests_subtitle'          => 'สองวิธีตอบคำถามคนละข้อ ควรทำทั้งคู่และจดเงื่อนไขทุกครั้ง ภาพในบทนี้ใช้อธิบายขั้นตอน ไม่ใช่ผลทดสอบของ FALCON PRO EA',
			'home_tests_tab_label'    => 'เลือกวิธีทดสอบ',
			'home_tests_tab_bt_label' => 'Backtest · ย้อนหลัง',
			'home_tests_tab_fw_label' => 'Forward · ตลาดจริง',
			'tests_bt_title'          => 'Backtest · ทดสอบกับข้อมูลในอดีต',
			'tests_bt_text'           => 'เปิด Strategy Tester ใน MT5 (กด Ctrl+R) แล้วรันระบบกับราคาย้อนหลัง เพื่อดูว่ากติกาทำงานอย่างไรในสภาพตลาดหลายแบบ ค่าที่ควรจดคือ Max Drawdown, Profit Factor และจำนวนออเดอร์ แต่ข้อมูลย้อนหลังไม่มี Slippage แบบตลาดจริง ตัวเลขจึงมักดูดีกว่าตอนใช้งาน',
			'home_tests_bt_btn'       => 'อ่านวิธีทำ Backtest',
			'tests_bt_img_mobile'     => '',
			'tests_bt_img_alt'        => 'ภาพประกอบหน้าจอ MT5 บนแล็ปท็อป ใช้อธิบายการทดสอบย้อนหลัง',
			'tests_bt_img_caption'    => 'ภาพประกอบวิธีทดสอบย้อนหลัง ไม่ใช่ผลทดสอบ และไม่ใช่ผลการเทรดจริงของ FALCON PRO EA',
			'tests_bt_img_note'       => 'รูปที่แนะนำ: หน้า Strategy Tester ที่ตั้งค่าไว้ หรือรายงาน Backtest จริงเมื่อพร้อมเผยแพร่ · ราว 1280x720 px',
			'home_tests_fw_btn'       => 'อ่านวิธีทำ Forward Test',
			'tests_fw_title'          => 'Forward Test · ทดสอบกับตลาดปัจจุบัน',
			'tests_fw_text'           => 'ปล่อยระบบให้ทำงานบนบัญชีเดโมหรือบัญชีเซ็นต์ไปพร้อมกับตลาดจริง จะเห็นผลของ Spread ค่าคอมมิชชัน และความเร็วในการส่งคำสั่งของโบรกเกอร์ที่ใช้จริง ต้องใช้เวลาหลายสัปดาห์ แต่สะท้อนการใช้งานได้ใกล้กว่า',
			'tests_fw_img_mobile'     => '',
			'tests_fw_img_alt'        => 'ภาพประกอบโต๊ะทำงานที่เปิด MT5 ใช้อธิบายการทดสอบกับตลาดจริง',
			'tests_fw_img_caption'    => 'ภาพประกอบการทดสอบกับตลาดปัจจุบัน ไม่ใช่ผลการเทรดจริงของ FALCON PRO EA',
			'tests_fw_img_note'       => 'รูปที่แนะนำ: รายงานบัญชีทดสอบที่มีลิงก์ให้คนอื่นตรวจได้ (เมื่อมีข้อมูลจริง) · ราว 1280x720 px',
			'tests_note'              => 'ผลทดสอบทุกชุดที่เผยแพร่จะระบุเงื่อนไขครบทุกค่า · ผลที่เคยเกิดขึ้นแล้วไม่ใช่หลักประกันของผลในอนาคต',
			'tests_pending_note'      => 'ตอนนี้ FALCON PRO EA ยังไม่มีผลทดสอบที่เผยแพร่ เมื่อมีข้อมูลที่ตรวจสอบได้ เราจะลงไว้ที่หน้าทดสอบย้อนหลังและหน้าทดสอบกับตลาดจริง',

			/* ---------- 06 ติดตั้ง ---------- */
			'home_install_kicker'     => 'เริ่มติดตั้ง',
			'install_home_title'      => 'สามขั้นจากไฟล์ EA สู่กราฟ MT5 ที่พร้อมทำงาน',
			'install_home_sub'        => 'ขั้นตอนเต็มพร้อมภาพอยู่ในคู่มือติดตั้ง ถ้าติดจุดไหน ส่งภาพหน้าจอให้ทีมงานช่วยดูทาง LINE ได้',
			'home_install_step_label' => 'ขั้น',
			'home_install_film_label' => 'ภาพประกอบขั้นตอนติดตั้ง เลื่อนซ้ายขวาเพื่อดู',
			'ih_step1_title'          => 'เตรียมบัญชีและโปรแกรม',
			'ih_step1_desc'           => 'สมัครบัญชีกับโบรกเกอร์ที่เปิดให้ใช้ EA บน MetaTrader 5 ติดตั้ง MT5 บนเครื่องที่จะเปิดทิ้งไว้หรือบน VPS และเก็บไฟล์ FALCON PRO EA ที่ได้รับไว้ให้พร้อม',
			'ih_step1_img'            => $banners . 'falcon-pro-ea-mt5-tablet-metatrader.webp',
			'ih_step1_img_mobile'     => '',
			'ih_step1_img_alt'        => 'MetaTrader 5 บนแท็บเล็ต (ภาพประกอบ)',
			'ih_step1_img_caption'    => $note,
			'ih_step1_img_note'       => 'รูปที่แนะนำ: หน้าล็อกอินบัญชีใน MT5 หรือหน้าติดตั้งโปรแกรม · ราว 1280x720 px',
			'ih_step2_title'          => 'ย้ายไฟล์เข้า MT5',
			'ih_step2_desc'           => 'ใน MT5 เปิดเมนู File → Open Data Folder → MQL5 → Experts แล้ววางไฟล์ .ex5 ปิดและเปิด MT5 ใหม่ จากนั้นกดปุ่ม Algo Trading บนแถบเครื่องมือจนขึ้นสีเขียว',
			'ih_step2_img'            => $banners . 'falcon-pro-ea-mt5-navigator.webp',
			'ih_step2_img_mobile'     => '',
			'ih_step2_img_alt'        => 'หน้าต่าง Navigator ของ MT5 ที่มี FALCON PRO EA (ภาพประกอบ)',
			'ih_step2_img_caption'    => $note,
			'ih_step2_img_note'       => 'รูปที่แนะนำ: โฟลเดอร์ MQL5 → Experts ที่มีไฟล์ EA กับปุ่ม Algo Trading ที่เปิดอยู่ · ราว 1280x720 px',
			'ih_step3_title'          => 'แนบ EA แล้วเช็กการทำงาน',
			'ih_step3_desc'           => 'ลาก FALCON PRO EA จาก Navigator ขึ้นกราฟ โหลด Preset ที่เลือกไว้ จากนั้นเปิดแท็บ Experts ดูว่าไม่มีข้อความแจ้งข้อผิดพลาด',
			'ih_step3_img'            => $banners . 'falcon-pro-ea-mt5-laptop-running.webp',
			'ih_step3_img_mobile'     => '',
			'ih_step3_img_alt'        => 'FALCON PRO EA ทำงานบนกราฟ MT5 ในแล็ปท็อป (ภาพประกอบ)',
			'ih_step3_img_caption'    => $note,
			'ih_step3_img_note'       => 'รูปที่แนะนำ: EA บนกราฟพร้อมแท็บ Experts ที่ไม่มีข้อผิดพลาด · ราว 1280x720 px',
			'install_home_note'       => 'แอป MT5 บนมือถือใช้ดูยอดเงินและออเดอร์ได้ แต่รัน EA ไม่ได้ ตัวระบบต้องทำงานบนคอมพิวเตอร์ที่เปิดค้างไว้ หรือบน VPS',
			'home_install_btn'        => 'เปิดคู่มือติดตั้งแบบละเอียด',
			'home_install_btn_url'    => '/how-to-install/',

			/* ---------- 07 แพ็กเกจ ---------- */
			'home_pricing_kicker'       => 'ระดับการดูแล',
			'pricing_home_title'        => 'สามแพ็กเกจ ระบบตัวเดียวกัน ต่างกันที่การดูแล',
			'pricing_home_sub'          => 'ทุกแพ็กเกจได้ FALCON PRO EA ตัวเดียวกัน สิ่งที่ต่างคือจำนวนบัญชีและความใกล้ชิดของทีมงาน ดูรายละเอียดเต็มได้ที่หน้าแพ็กเกจ',
			'home_pricing_tabs_label'   => 'เลือกดูแพ็กเกจ',
			'home_pricing_rec_label'    => 'แนะนำ',
			'home_pricing_contact_text' => 'สอบถามราคาล่าสุด',
			'home_pricing_margin_note'  => 'การซื้อแพ็กเกจไม่ได้ทำให้การเทรดปลอดภัยขึ้น ตลาดยังผันผวนและบัญชียังขาดทุนได้เสมอ ศึกษาประกาศความเสี่ยงของเราก่อนชำระเงิน',
			'home_pricing_more_text'    => 'ดูตารางเทียบแพ็กเกจแบบเต็ม',

			/* ---------- 08 ถาม-ตอบ ---------- */
			'home_faq_kicker' => 'ถาม-ตอบ',
			'faq_title'       => 'คำถามเรื่อง EA MT5 ที่ทีม FALCON PRO ได้รับบ่อย',
			'faq_subtitle'    => 'คำตอบสั้น ๆ สำหรับช่วงก่อนตัดสินใจ ถ้าข้อไหนยังไม่ชัด ทักทีมงานได้โดยไม่มีข้อผูกมัด',
			'faq1_q'          => 'EA บอทเทรด และโรบอทเทรด คือสิ่งเดียวกันไหม?',
			'faq1_a'          => 'ใช่ ทั้งสามคำพูดถึงโปรแกรมแบบเดียวกัน คือโปรแกรมที่ติดไว้บนกราฟของ MetaTrader และสั่งซื้อหรือขายเองตามกติกาที่เขียนไว้ EA ย่อมาจาก Expert Advisor ซึ่งเป็นชื่อที่ MT5 ใช้ ส่วนบอทเทรดกับโรบอทเทรดเป็นคำที่คนไทยเรียกกันทั่วไป FALCON PRO EA คือโปรแกรมประเภทนี้ที่สร้างมาสำหรับ MetaTrader 5',
			'faq2_q'          => 'EA เหมาะกับคนแบบไหน และไม่เหมาะกับใคร?',
			'faq2_a'          => 'เหมาะกับคนที่มีเวลาดูกราฟน้อย อยากให้แผนเทรดถูกใช้อย่างสม่ำเสมอ และรับได้ว่าการเทรดมีช่วงขาดทุน ไม่เหมาะกับคนที่หวังผลตอบแทนเร็ว ใช้เงินที่เสียไม่ได้ หรือไม่อยากใช้เวลาทำความเข้าใจค่าตั้งเลย',
			'faq3_q'          => 'FALCON PRO EA รับประกันกำไรหรือไม่?',
			'faq3_a'          => 'ไม่รับประกัน และไม่มีระบบเทรดใดรับประกันได้อย่างซื่อตรง ผลลัพธ์ขึ้นกับความผันผวนของตลาด ค่าที่ตั้ง ขนาดเงินทุน รวมถึงต้นทุนและกติกาของโบรกเกอร์ เงินทุนของคุณจึงลดลงได้ทั้งบางส่วนและทั้งหมด ถ้ามีใครยืนยันว่า EA ได้กำไรแน่นอน ให้ระวังเป็นพิเศษ',
			'faq4_q'          => 'ต้องใช้กับโบรกเกอร์เจ้าไหน?',
			'faq4_a'          => 'โบรกเกอร์ใดก็ได้ที่มี MetaTrader 5 และเปิดให้รัน EA ก่อนเริ่มควรดูประเภทบัญชี Spread ค่าคอมมิชชัน และ Leverage แล้วส่งข้อมูลชุดนี้ให้ทีมงาน เพื่อเลือกค่าตั้งต้นที่ตรงกับบัญชีของคุณ',
			'faq5_q'          => 'ใช้กับทองคำ XAUUSD หรือคู่เงินไหนได้บ้าง?',
			'faq5_a'          => 'ใช้กับสินทรัพย์ที่มีอยู่ในบัญชี MT5 ของคุณได้ ทั้งคู่เงินหลัก ทองคำ และสินทรัพย์อื่น แต่ละตัวผันผวนไม่เท่ากันจึงต้องใช้ค่าตั้งต่างกัน ทีมงานจะแนะนำ Preset ตามสินทรัพย์และขนาดทุนที่คุณแจ้ง',
			'faq6_q'          => 'ต้องเปิดคอมพิวเตอร์ไว้ตลอดไหม ใช้บนมือถือได้หรือเปล่า?',
			'faq6_a'          => 'EA ทำงานได้เฉพาะใน MT5 ที่รันอยู่บนคอมพิวเตอร์หรือเซิร์ฟเวอร์ VPS ปิดเครื่องเมื่อไรระบบก็หยุดตามไปด้วย ส่วนแอป MT5 บนโทรศัพท์ใช้รัน EA ไม่ได้ แต่ล็อกอินเข้าบัญชีเดียวกันเพื่อดูยอดเงินและออเดอร์ได้',
			'faq7_q'          => 'ควรเริ่มด้วยทุนเท่าไร?',
			'faq7_a'          => 'ไม่มีตัวเลขเดียวที่เหมาะกับทุกคน ขึ้นกับสินทรัพย์ที่เลือก เพดานความเสี่ยงที่รับได้ และกติกาบัญชีของโบรกเกอร์ หลักที่ควรยึดคือเริ่มจากเงินก้อนที่ถ้าหายไปแล้วชีวิตประจำวันยังเดินต่อได้ และคุยกับทีมงานเพื่อประเมินก่อนเริ่ม',
			'faq8_q'          => 'ลองกับบัญชีเดโมก่อนได้ไหม?',
			'faq8_a'          => 'ได้ และควรทำ ลองรันบนบัญชีเดโมหรือบัญชีเซ็นต์ของโบรกเกอร์ที่จะใช้จริงสักระยะ แล้วสังเกตว่าระบบทำงานอย่างไรเมื่อเจอ Spread และเงื่อนไขจริง ก่อนตัดสินใจย้ายไปบัญชีจริง',
			'faq9_q'          => 'นอกจากไฟล์ EA แล้วได้อะไรอีกบ้าง?',
			'faq9_a'          => 'นอกจากไฟล์ EA คุณจะได้คู่มือการติดตั้งฉบับภาษาไทย ไฟล์ Preset สำหรับความเสี่ยงหลายระดับ เวอร์ชันอัปเดต และช่องทาง LINE สำหรับถามเรื่องการติดตั้งหรือการตั้งค่า ขอบเขตการดูแลของแต่ละแพ็กเกจดูได้ที่หน้าแพ็กเกจ',
			'faq10_q'         => 'ใช้ EA แล้วยังต้องระวังความเสี่ยงเรื่องไหน?',
			'faq10_a'         => 'มีทั้งความเสี่ยงจากตลาด เช่น ราคากระชากช่วงข่าว Spread ถ่างออก และ Slippage กับความเสี่ยงจากการใช้งาน เช่น ตั้งขนาดออเดอร์ใหญ่เกินทุน อินเทอร์เน็ตหรือ VPS ขัดข้อง ก่อนใช้เงินจริงควรอ่านประกาศความเสี่ยงของเราให้ครบทุกข้อ',

			/* ---------- 09 ความเสี่ยง ---------- */
			'home_risk_kicker'    => 'ความเสี่ยง',
			'home_risk_label'     => 'คำเตือน',
			'risk_title'          => 'อ่านข้อนี้ก่อนเริ่มใช้เงินจริง',
			'risk_text'           => 'การเทรด Forex ทองคำ CFD และสินทรัพย์ที่ใช้ Leverage มีความเสี่ยงสูง ราคาอาจเคลื่อนไหวรุนแรงจนคุณสูญเสียเงินทุนไปบางส่วนหรือทั้งหมด ผลการทดสอบและผลที่เคยเกิดขึ้นในอดีตไม่ได้รับประกันผลในอนาคต FALCON PRO EA เป็นซอฟต์แวร์ที่ส่งคำสั่งตามเงื่อนไขที่ผู้ใช้ตั้งไว้ ไม่ถือเป็นคำแนะนำด้านการลงทุน และไม่มีการรับประกันกำไรในทุกกรณี ผู้ใช้ควรศึกษาข้อมูล ทดลองบนบัญชีเดโม และกำหนดความเสี่ยงให้เหมาะกับฐานะของตนเองก่อนใช้งานจริง',
			'home_risk_more_text' => 'เปิดอ่านประกาศความเสี่ยงทุกข้อ',

			/* ---------- ค่าเดิมที่หน้าแรกไม่แสดงแล้ว · เขียนใหม่ไม่ให้ซ้ำแบรนด์อื่น (เก็บไว้เผื่ออ้างอิง) ---------- */
			'fit_title'          => 'FALCON PRO EA เข้ากับคุณหรือเปล่า?',
			'fit_subtitle'       => 'ลองเทียบกับรายการด้านล่างก่อน เพื่อให้ตัดสินใจจากข้อเท็จจริงมากกว่าความคาดหวัง',
			'fit_good_items'     => "คนที่อยากให้แผนเทรดมีขั้นตอนชัดเจน\nคนที่อยากลดการกดตามอารมณ์\nคนที่ยอมรับได้ว่าการเทรดมีช่วงขาดทุน\nคนที่พร้อมใช้เวลาทำความเข้าใจค่าตั้ง\nคนที่มอง EA เป็นตัวช่วยทำตามแผน ไม่ใช่ทางลัด",
			'fit_bad_items'      => "คนที่ต้องการผลตอบแทนเร็ว\nคนที่รับการขาดทุนไม่ได้เลย\nคนที่ไม่อยากศึกษาการใช้งาน\nคนที่คาดว่า EA จะได้กำไรทุกวัน\nคนที่ใช้เงินจำเป็นมาเทรด",
			'perf_subtitle'      => 'ทุกชุดข้อมูลที่เผยแพร่จะระบุเงื่อนไขการทดสอบไว้ครบ',
			'perf_note'          => 'หมายเหตุ: Spread ค่าคอมมิชชัน และ Slippage ที่ต่างกันในแต่ละบัญชีทำให้ตัวเลขออกมาไม่เท่ากันได้',
			'perf_disclaimer'    => 'ข้อมูลการทดสอบมีไว้เพื่อการศึกษา ผลในอดีตไม่ได้รับประกันผลในอนาคต ผู้ใช้ต้องทำความเข้าใจความเสี่ยงให้ครบก่อนตัดสินใจใช้งาน',
			'home_cards_sub'     => 'ข้อมูลแยกเป็นหมวด อ่านทีละเรื่องได้ตามจังหวะของคุณ',
			'card3_desc'         => 'เทียบสิทธิ์และการดูแลของแต่ละแพ็กเกจ',
			'card5_desc'         => 'สิ่งที่ต้องรู้เรื่องความเสี่ยงก่อนใช้เงินจริง',
		)
	);
}

/* ==============================================================
 * Customizer · หมวดของหน้าแรกเรียงตามลำดับบนหน้า
 * ============================================================== */

add_filter( 'fenix_customizer_sections', 'fenix_home_customizer_sections', 10, 2 );

function fenix_home_customizer_sections( $sections, $d ) {
	$rule  = 'ห้ามใส่ตัวเลขผลเทรด เปอร์เซ็นต์กำไร หรือคำสัญญาว่าได้กำไร';
	$lines = 'บรรทัดละ 1 รายการ';

	$home = array(
		'fenix_home_boot'     => array(
			'title'       => '2) หน้าแรก · Hero และรางเลขบท',
			'description' => 'ส่วนบนสุดของหน้าแรก มี H1 ได้หนึ่งอัน ปุ่มติดต่อไป LINE (ยังไม่ใส่ LINE = ไปหน้า /go/ ถ้าเผยแพร่แล้ว ไม่มีทั้งคู่ = ซ่อนปุ่ม) และแถบข้อมูลระบบที่ไม่ใช่ผลการเทรด · เลขบทนับเฉพาะบทที่เปิดอยู่',
			'fields'      => array(
				'show_hero'            => array( 'แสดงส่วน Hero', 'checkbox' ),
				'home_show_rail'       => array( 'แสดงรางเลขบท (จอกว้าง) และแถบความคืบหน้าใต้เมนู (จอเล็ก)', 'checkbox' ),
				'home_rail_label'      => array( 'ชื่อรางเลขบท (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
				'home_fig_label'       => array( 'คำนำหน้าเลขภาพ (เช่น "ภาพ" จะแสดงเป็น ภาพ 01)', 'text' ),
				'hero_badge'           => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
				'hero_title'           => array( 'ชื่อแบรนด์ (อยู่ใน H1)', 'text' ),
				'hero_subtitle'        => array( 'หัวข้อหลัก (H1)', 'text' ),
				'hero_subtitle_em'     => array( 'คำเน้นสีเขียวท้ายหัวข้อ (เช่น MT5)', 'text' ),
				'hero_desc'            => array( 'คำอธิบายสั้น', 'textarea', $rule ),
				'hero_btn1_text'       => array( 'ข้อความปุ่มติดต่อ (เมื่อมีลิงก์ LINE)', 'text', 'ถ้ายังไม่ใส่ LINE ปุ่มจะชี้ไปหน้า /go/ และใช้ข้อความปุ่มสำรองด้านล่าง' ),
				'hero_btn2_text'       => array( 'ข้อความลิงก์ไปบท "วงจรการทำงาน" (เว้นว่าง = ซ่อน)', 'text' ),
				'hero_note'            => array( 'ข้อความเตือนความเสี่ยงใต้ปุ่ม (ห้ามลบ)', 'textarea' ),
				'home_hero_spec_items' => array( 'แถบข้อมูลระบบ (' . $lines . ': หัวข้อ|ค่า)', 'textarea', 'บรรทัดที่มีตัวเลขตามด้วย % หรือคำว่า "กำไร" จะไม่แสดง' ),
				'home_hero_spec_note'  => array( 'คำอธิบายแถบข้อมูล (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
				'hero_image'           => array( 'ภาพ Hero (ไม่ใส่ = แสดงแผงควบคุม EA จำลอง)', 'image', 'แนะนำภาพสี่เหลี่ยมจัตุรัส 1000px ขึ้นไป · ถ้าภาพมีตัวเลข ต้องไม่อ่านแล้วเหมือนผลการเทรด' ),
				'home_hero_img_alt'    => array( 'คำอธิบายภาพ Hero (alt)', 'text' ),
				'hero_panel_title'     => array( 'แผงจำลอง · ชื่อ', 'text' ),
				'hero_panel_status'    => array( 'แผงจำลอง · สถานะ', 'text', 'หลีกเลี่ยงคำที่ทำให้เข้าใจว่ากำลังเทรดจริง' ),
				'hero_panel_badge'     => array( 'แผงจำลอง · ป้ายขวาบน', 'text' ),
				'home_hero_panel_sub'  => array( 'แผงจำลอง · ข้อความเล็กใต้ป้ายขวาบน', 'text' ),
				'hero_panel_fields'    => array( 'แผงจำลอง · ช่องข้อมูล (' . $lines . ': ชื่อ|ค่า)', 'textarea', $rule ),
				'hero_panel_button'    => array( 'แผงจำลอง · ข้อความแถบเขียว', 'text' ),
				'hero_panel_tags'      => array( 'แผงจำลอง · ข้อความเล็กข้างแถบเขียว', 'text' ),
				'hero_panel_caption'   => array( 'แผงจำลอง · คำบรรยายใต้แผง (จำเป็น)', 'text' ),
			),
		),

		'fenix_home_what'     => array(
			'title'  => '2.1) หน้าแรก · บท 01 ระบบคืออะไร',
			'fields' => array_merge(
				array(
					'show_about'                => array( 'แสดงบทนี้', 'checkbox' ),
					'home_what_kicker'          => array( 'ป้ายบท (แสดงหลังเลขบท และบนรางเลขบท)', 'text' ),
					'about_title'               => array( 'หัวข้อ (H2)', 'text' ),
					'about_text'                => array( 'เนื้อหา (เว้นบรรทัดว่าง = ย่อหน้าใหม่)', 'textarea', $rule ),
					'about_points'              => array( 'ตารางข้อมูลระบบ (' . $lines . ': หัวข้อ|รายละเอียด)', 'textarea', 'บรรทัดที่ไม่มีหัวข้อจะได้เลข 01, 02 … แทน' ),
					'home_what_principle_label' => array( 'กล่องหลักการ · หัวข้อเล็ก', 'text' ),
					'home_what_principle'       => array( 'กล่องหลักการ · ข้อความ (เว้นว่าง = ซ่อน)', 'textarea' ),
				),
				fenix_media_fields( 'home_what', 'ภาพ 01' )
			),
		),

		'fenix_home_pain'     => array(
			'title'  => '2.2) หน้าแรก · บท 02 จุดที่แผนมักหลุด',
			'fields' => array(
				'show_pain'                => array( 'แสดงบทนี้', 'checkbox' ),
				'home_pain_kicker'         => array( 'ป้ายบท', 'text' ),
				'pain_title'               => array( 'หัวข้อ (H2)', 'text' ),
				'pain_subtitle'            => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pain1_title'              => array( 'ข้อ 1 · หัวข้อ (ถูกขีดฆ่าเมื่อเลื่อนมาถึง)', 'text' ),
				'pain1_desc'               => array( 'ข้อ 1 · รายละเอียด', 'textarea' ),
				'pain2_title'              => array( 'ข้อ 2 · หัวข้อ', 'text' ),
				'pain2_desc'               => array( 'ข้อ 2 · รายละเอียด', 'textarea' ),
				'pain3_title'              => array( 'ข้อ 3 · หัวข้อ', 'text' ),
				'pain3_desc'               => array( 'ข้อ 3 · รายละเอียด', 'textarea' ),
				'pain4_title'              => array( 'ข้อ 4 · หัวข้อ', 'text' ),
				'pain4_desc'               => array( 'ข้อ 4 · รายละเอียด', 'textarea' ),
				'home_pain_resolved_label' => array( 'แถวสรุปท้ายรายการ · หัวข้อเล็ก', 'text' ),
				'home_pain_resolved_text'  => array( 'แถวสรุปท้ายรายการ · ข้อความ (เว้นว่าง = ซ่อน)', 'textarea', $rule ),
			),
		),

		'fenix_home_how'      => array(
			'title'  => '2.3) หน้าแรก · บท 03 วงจรการทำงาน',
			'fields' => array_merge(
				array(
					'show_steps'            => array( 'แสดงบทนี้', 'checkbox' ),
					'steps_kicker'          => array( 'ป้ายบท', 'text' ),
					'steps_title'           => array( 'หัวข้อ (H2)', 'text' ),
					'steps_subtitle'        => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
					'step1_title'           => array( 'ขั้น 1 · หัวข้อ', 'text' ),
					'step1_desc'            => array( 'ขั้น 1 · รายละเอียด', 'textarea' ),
					'step2_title'           => array( 'ขั้น 2 · หัวข้อ', 'text' ),
					'step2_desc'            => array( 'ขั้น 2 · รายละเอียด', 'textarea' ),
					'step3_title'           => array( 'ขั้น 3 · หัวข้อ', 'text' ),
					'step3_desc'            => array( 'ขั้น 3 · รายละเอียด', 'textarea' ),
					'step4_title'           => array( 'ขั้น 4 · หัวข้อ', 'text' ),
					'step4_desc'            => array( 'ขั้น 4 · รายละเอียด', 'textarea' ),
					'home_show_how_log'     => array( 'แสดงหน้าจอบันทึกการทำงาน (พิมพ์ทีละบรรทัด)', 'checkbox' ),
					'home_how_log_title'    => array( 'หน้าจอบันทึก · ชื่อบนแถบด้านบน', 'text' ),
					'home_how_log_prompt'   => array( 'หน้าจอบันทึก · ตัวนำหน้าคำสั่ง', 'text' ),
					'home_how_log_start'    => array( 'หน้าจอบันทึก · บรรทัดคำสั่งแรก', 'text' ),
					'home_how_log_lines'    => array( 'หน้าจอบันทึก · บรรทัดกลาง (' . $lines . ')', 'textarea', 'เว้นว่าง = ใช้ชื่อ 4 ขั้นด้านบน · บรรทัดที่มีเวลา (เช่น 10:30) ราคา ทศนิยม % หรือคำว่า "กำไร" จะถูกข้าม' ),
					'home_how_log_ready'    => array( 'หน้าจอบันทึก · บรรทัดสุดท้าย', 'text' ),
					'steps_checklist_title' => array( 'รายการเตรียมตัว · หัวข้อ', 'text' ),
					'steps_checklist'       => array( 'รายการเตรียมตัว (' . $lines . ')', 'textarea' ),
				),
				fenix_media_fields( 'home_how', 'ภาพ 02' )
			),
		),

		'fenix_home_features' => array(
			'title'  => '2.4) หน้าแรก · บท 04 ส่วนประกอบหลัก (6 โมดูล)',
			'fields' => array(
				'show_features'          => array( 'แสดงบทนี้', 'checkbox' ),
				'home_features_kicker'   => array( 'ป้ายบท', 'text' ),
				'home_feat_module_label' => array( 'คำนำหน้าเลขโมดูล (เช่น "โมดูล" จะแสดงเป็น โมดูล 01)', 'text' ),
				'features_title'         => array( 'หัวข้อ (H2)', 'text' ),
				'features_subtitle'      => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'feat1_title'            => array( 'โมดูล 1 · หัวข้อ', 'text' ),
				'feat1_desc'             => array( 'โมดูล 1 · รายละเอียด', 'textarea', $rule ),
				'feat2_title'            => array( 'โมดูล 2 · หัวข้อ', 'text' ),
				'feat2_desc'             => array( 'โมดูล 2 · รายละเอียด', 'textarea' ),
				'feat3_title'            => array( 'โมดูล 3 · หัวข้อ', 'text' ),
				'feat3_desc'             => array( 'โมดูล 3 · รายละเอียด', 'textarea' ),
				'feat4_title'            => array( 'โมดูล 4 · หัวข้อ', 'text' ),
				'feat4_desc'             => array( 'โมดูล 4 · รายละเอียด', 'textarea' ),
				'feat5_title'            => array( 'โมดูล 5 · หัวข้อ', 'text' ),
				'feat5_desc'             => array( 'โมดูล 5 · รายละเอียด', 'textarea' ),
				'feat6_title'            => array( 'โมดูล 6 · หัวข้อ', 'text' ),
				'feat6_desc'             => array( 'โมดูล 6 · รายละเอียด', 'textarea' ),
			),
		),

		'fenix_home_tests'    => array(
			'title'       => '2.5) หน้าแรก · บท 05 การทดสอบ',
			'description' => 'บทนี้อธิบายวิธีทดสอบเท่านั้น ไม่แสดงตัวเลขผลทดสอบ · ภาพที่มีตัวเลขต้องมีคำบรรยายว่าเป็นภาพประกอบ ไม่ใช่ผลการเทรดจริง · ปุ่มไปหน้า Backtest / Forward Test แสดงเมื่อหน้านั้นเผยแพร่แล้ว',
			'fields'      => array_merge(
				array(
					'show_tests'              => array( 'แสดงบทนี้', 'checkbox' ),
					'tests_kicker'            => array( 'ป้ายบท', 'text' ),
					'tests_title'             => array( 'หัวข้อ (H2)', 'text' ),
					'tests_subtitle'          => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
					'home_tests_tab_label'    => array( 'ชื่อกลุ่มแท็บ (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
					'home_tests_tab_bt_label' => array( 'แท็บ 1 · ชื่อแท็บ', 'text', 'จอแคบจะตัดบรรทัดได้เฉพาะหลังเครื่องหมาย " · " (เช่น Backtest · ย้อนหลัง)' ),
					'tests_bt_title'          => array( 'แท็บ 1 · หัวข้อ', 'text' ),
					'tests_bt_text'           => array( 'แท็บ 1 · เนื้อหา', 'textarea', $rule ),
					'home_tests_bt_btn'       => array( 'แท็บ 1 · ข้อความปุ่มไปหน้า Backtest (เว้นว่าง = ซ่อน)', 'text' ),
				),
				fenix_media_fields( 'tests_bt', 'แท็บ 1 · ภาพ 03' ),
				array(
					'home_tests_tab_fw_label' => array( 'แท็บ 2 · ชื่อแท็บ', 'text', 'จอแคบจะตัดบรรทัดได้เฉพาะหลังเครื่องหมาย " · "' ),
					'tests_fw_title'          => array( 'แท็บ 2 · หัวข้อ', 'text' ),
					'tests_fw_text'           => array( 'แท็บ 2 · เนื้อหา', 'textarea', $rule ),
					'home_tests_fw_btn'       => array( 'แท็บ 2 · ข้อความปุ่มไปหน้า Forward Test (เว้นว่าง = ซ่อน)', 'text' ),
				),
				fenix_media_fields( 'tests_fw', 'แท็บ 2 · ภาพ 04' ),
				array(
					'tests_note' => array( 'หมายเหตุท้ายบท (แสดงเสมอ · ห้ามลบคำเตือน)', 'textarea' ),
					'tests_pending_note' => array( 'ข้อความ "ยังไม่มีผลทดสอบ" (แสดงเฉพาะตอนที่หน้า Backtest/Forward ยังไม่มีตัวเลขจริง)', 'textarea' ),
				)
			),
		),

		'fenix_home_install'  => array(
			'title'  => '2.6) หน้าแรก · บท 06 ติดตั้ง 3 ขั้น',
			'fields' => array_merge(
				array(
					'show_install_home'       => array( 'แสดงบทนี้', 'checkbox' ),
					'home_install_kicker'     => array( 'ป้ายบท', 'text' ),
					'install_home_title'      => array( 'หัวข้อ (H2)', 'text' ),
					'install_home_sub'        => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
					'home_install_step_label' => array( 'คำนำหน้าเลขขั้น (เช่น "ขั้น" จะแสดงเป็น ขั้น 01)', 'text' ),
					'home_install_film_label' => array( 'ชื่อแถบภาพเลื่อน (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
					'ih_step1_title'          => array( 'ขั้น 1 · หัวข้อ', 'text' ),
					'ih_step1_desc'           => array( 'ขั้น 1 · รายละเอียด', 'textarea' ),
				),
				fenix_media_fields( 'ih_step1', 'ขั้น 1' ),
				array(
					'ih_step2_title' => array( 'ขั้น 2 · หัวข้อ', 'text' ),
					'ih_step2_desc'  => array( 'ขั้น 2 · รายละเอียด', 'textarea' ),
				),
				fenix_media_fields( 'ih_step2', 'ขั้น 2' ),
				array(
					'ih_step3_title' => array( 'ขั้น 3 · หัวข้อ', 'text' ),
					'ih_step3_desc'  => array( 'ขั้น 3 · รายละเอียด', 'textarea' ),
				),
				fenix_media_fields( 'ih_step3', 'ขั้น 3' ),
				array(
					'install_home_note'    => array( 'หมายเหตุเรื่องมือถือ (เว้นว่าง = ซ่อน)', 'textarea' ),
					'home_install_btn'     => array( 'ข้อความปุ่มไปคู่มือติดตั้ง (เว้นว่าง = ซ่อน)', 'text' ),
					'home_install_btn_url' => array( 'ลิงก์ปุ่มคู่มือติดตั้ง (slug หรือ URL)', 'path', 'ลิงก์ในเว็บจะแสดงเมื่อหน้านั้นเผยแพร่แล้วเท่านั้น' ),
				)
			),
		),

		'fenix_home_pricing'  => array(
			'title'       => '2.7) หน้าแรก · บท 07 แพ็กเกจ',
			'description' => 'ชื่อ ราคา สิ่งที่ได้ ป้ายแนะนำ ข้อความปุ่ม และรูปแบบการแสดงราคา ตั้งที่หมวด "10) แพ็กเกจราคา" · ถ้าราคายังไม่มีตัวเลขจริง (เช่น X,XXX) ระบบจะแสดงข้อความสอบถามราคาแทน · จอแคบเป็นแท็บเลือกแพ็กเกจ จอกว้าง (1100px ขึ้นไป) เป็นตารางเทียบ',
			'fields'      => array(
				'show_pricing_home'         => array( 'แสดงบทนี้', 'checkbox' ),
				'home_pricing_kicker'       => array( 'ป้ายบท', 'text' ),
				'pricing_home_title'        => array( 'หัวข้อ (H2)', 'text' ),
				'pricing_home_sub'          => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'home_pricing_tabs_label'   => array( 'ชื่อกลุ่มแท็บแพ็กเกจ (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
				'home_pricing_rec_label'    => array( 'ป้ายแพ็กเกจแนะนำ', 'text' ),
				'home_pricing_contact_text' => array( 'ข้อความแทนราคา (เมื่อไม่แสดงราคา)', 'text' ),
				'home_pricing_margin_note'  => array( 'คำเตือนใต้ตารางแพ็กเกจ (ห้ามลบ)', 'textarea' ),
				'home_pricing_more_text'    => array( 'ข้อความลิงก์ไปหน้าแพ็กเกจ (เว้นว่าง = ซ่อน)', 'text' ),
			),
		),

		'fenix_home_faq'      => array(
			'title'       => '2.8) หน้าแรก · บท 08 ถาม-ตอบ',
			'description' => 'มีช่อง 10 ข้อ ข้อที่คำถามหรือคำตอบว่างจะไม่แสดง · เปิดอ่านได้ทีละข้อ · คำถามชุดนี้ใช้ทำ FAQ schema ของหน้าแรกด้วย',
			'fields'      => array(
				'show_faq'        => array( 'แสดงบทนี้', 'checkbox' ),
				'home_faq_kicker' => array( 'ป้ายบท', 'text' ),
				'faq_title'       => array( 'หัวข้อ (H2)', 'text' ),
				'faq_subtitle'    => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			),
		),

		'fenix_home_risk'     => array(
			'title'       => '2.9) หน้าแรก · บท 09 คำเตือนความเสี่ยง',
			'description' => 'สำคัญต่อความน่าเชื่อถือ ไม่แนะนำให้ปิด · ตัวข้อความคำเตือนฉบับเต็มแก้ที่หมวด "13) คำเตือนความเสี่ยง" (ใช้ร่วมกันทั้งเว็บ)',
			'fields'      => array(
				'show_risk'           => array( 'แสดงบทนี้', 'checkbox' ),
				'home_risk_kicker'    => array( 'ป้ายบท', 'text' ),
				'home_risk_label'     => array( 'ป้ายตราเตือน', 'text' ),
				'risk_title'          => array( 'หัวข้อ (H2)', 'text' ),
				'home_risk_more_text' => array( 'ข้อความปุ่มไปหน้าประกาศความเสี่ยง (เว้นว่าง = ซ่อน · แสดงเมื่อหน้านั้นเผยแพร่แล้ว)', 'text' ),
			),
		),
	);

	/* ข้อความคำเตือนหลักใช้ทั้งเว็บ (หน้าแรก บท 09 · ท้ายเว็บทุกหน้า · บทความ · หน้า /go/) จึงแยกเป็นหมวดของตัวเอง ต่อจากหมวดแพ็กเกจ */
	$sitewide = array(
		'fenix_risk' => array(
			'title'       => '13) คำเตือนความเสี่ยง',
			'description' => 'ข้อความเดียวนี้แสดงที่หน้าแรก (บท 09) ท้ายเว็บทุกหน้า ท้ายบทความ และหน้า /go/ · สำคัญต่อความถูกต้องและความน่าเชื่อถือ ห้ามลบ ตัด หรือลดทอน',
			'fields'      => array(
				'risk_text' => array( 'ข้อความคำเตือนฉบับเต็ม', 'textarea', 'ต้องบอกว่าขาดทุนได้ ผลในอดีตไม่รับประกันอนาคต และไม่ใช่คำแนะนำการลงทุน' ),
			),
		),
	);

	for ( $i = 1; $i <= 10; $i++ ) {
		$home['fenix_home_faq']['fields'][ 'faq' . $i . '_q' ] = array( 'คำถามข้อ ' . $i, 'text' );
		$home['fenix_home_faq']['fields'][ 'faq' . $i . '_a' ] = array( 'คำตอบข้อ ' . $i, 'textarea', $rule );
	}

	/* หมวดเดิมของหน้าแรก · ฟิลด์ที่หมวดใหม่ไม่ได้ใช้ย้ายไปหมวด "ค่าเดิม" ท้ายสุด */
	$origin_full = array( 'fenix_hero', 'fenix_pain', 'fenix_about', 'fenix_features', 'fenix_gallery', 'fenix_steps', 'fenix_perf', 'fenix_fit', 'fenix_reviews', 'fenix_faq', 'fenix_risk', 'fenix_home', 'fenix_team', 'fenix_assurance', 'fenix_home_extra', 'fenix_cta', 'fenix_blog' );
	$origin_part = array( 'fenix_pricing' );

	/* คีย์ที่โมดูลอื่นย้ายไปหมวดของตัวเองแล้ว ไม่ลงทะเบียนซ้ำ */
	$claimed = array();
	foreach ( $sections as $sid => $section ) {
		if ( in_array( $sid, $origin_full, true ) || in_array( $sid, $origin_part, true ) || empty( $section['fields'] ) ) {
			continue;
		}
		foreach ( array_keys( $section['fields'] ) as $key ) {
			$claimed[ $key ] = true;
		}
	}

	/* ข้อความปุ่มติดต่อสำรอง (ใช้ทั้งเว็บ) ยังไม่มีช่องใน Customizer = เพิ่มไว้ที่หมวด Hero */
	if ( ! isset( $claimed['contact_fallback_text'] ) && array_key_exists( 'contact_fallback_text', $d ) ) {
		$home['fenix_home_boot']['fields'] = fenix_home_insert_after(
			$home['fenix_home_boot']['fields'],
			'hero_btn1_text',
			array(
				'contact_fallback_text' => array( 'ข้อความปุ่มติดต่อสำรอง (เมื่อยังไม่ใส่ LINE · ปุ่มชี้ไปหน้า /go/)', 'text', 'ใช้กับปุ่มติดต่อทุกปุ่มบนเว็บ' ),
			)
		);
	}

	/* ถอดคีย์ที่โมดูลอื่นถือไว้ออกจากหมวดของเรา แล้วจดคีย์ที่เราถือ */
	$owned = array();
	$strip = function ( $group ) use ( $claimed, &$owned ) {
		foreach ( $group as $sid => $section ) {
			foreach ( array_keys( $section['fields'] ) as $key ) {
				if ( isset( $claimed[ $key ] ) ) {
					unset( $group[ $sid ]['fields'][ $key ] );
					continue;
				}
				$owned[ $key ] = true;
			}
		}
		return $group;
	};
	$home     = $strip( $home );
	$sitewide = array_filter(
		$strip( $sitewide ),
		function ( $section ) {
			return ! empty( $section['fields'] );
		}
	);

	$legacy = array();
	foreach ( $origin_full as $sid ) {
		if ( empty( $sections[ $sid ]['fields'] ) ) {
			unset( $sections[ $sid ] );
			continue;
		}
		foreach ( $sections[ $sid ]['fields'] as $key => $field ) {
			if ( ! isset( $owned[ $key ] ) && ! isset( $claimed[ $key ] ) ) {
				$legacy[ $key ] = $field;
			}
		}
		unset( $sections[ $sid ] );
	}
	foreach ( $origin_part as $sid ) {
		if ( empty( $sections[ $sid ]['fields'] ) ) {
			continue;
		}
		foreach ( array_keys( $sections[ $sid ]['fields'] ) as $key ) {
			if ( isset( $owned[ $key ] ) ) {
				unset( $sections[ $sid ]['fields'][ $key ] );
			}
		}
		if ( empty( $sections[ $sid ]['fields'] ) ) {
			unset( $sections[ $sid ] );
		}
	}

	/* แทรกหมวดหน้าแรกต่อจากหมวด 1 (ช่องทางติดต่อ) · หมวดคำเตือนหลัก (13) ต่อจากหมวด 10) แพ็กเกจราคา */
	$out      = array();
	$inserted = false;
	$risk_in  = false;
	foreach ( $sections as $sid => $section ) {
		$out[ $sid ] = $section;
		if ( 'fenix_general' === $sid ) {
			$out      = array_merge( $out, $home );
			$inserted = true;
		}
		if ( 'fenix_pricing' === $sid && $inserted ) {
			$out     = array_merge( $out, $sitewide );
			$risk_in = true;
		}
	}
	if ( ! $inserted ) {
		$out = array_merge( $home, $out );
	}
	if ( ! $risk_in && $sitewide ) {
		/* ไม่มีหมวดแพ็กเกจ = วางต่อจากบท 09 ของหน้าแรก (ไม่พบ = ต่อท้าย) */
		$out = fenix_home_insert_after( $out, 'fenix_home_risk', $sitewide );
	}

	if ( $legacy ) {
		$out['fenix_home_legacy'] = array(
			'title'       => '99) หน้าแรก · ค่าเดิมที่ไม่ได้แสดงแล้ว',
			'description' => 'ส่วนเหล่านี้ถูกถอดออกจากหน้าแรกเมื่อจัดโครงใหม่เป็น 9 บท (แกลเลอรี ผลทดสอบ รีวิว ทีมงาน ความมั่นใจ การ์ดนำทาง บทความล่าสุด แถบกลางหน้า และ CTA ท้ายหน้าซึ่งย้ายไปเป็นแถวติดต่อใน Footer) ค่ายังเก็บไว้เผื่ออ้างอิง การแก้ค่าที่นี่ไม่เปลี่ยนหน้าแรก · ยกเว้นไฮไลต์ 1–4 ซึ่ง Footer ยังใช้เป็นค่าสำรองของ "ข้อมูลระบบ" เมื่อหมวด 15) ยังไม่กรอก',
			'fields'      => $legacy,
		);
	}

	return $out;
}

/**
 * แทรกรายการต่อจาก key ที่กำหนด (ไม่มี key นั้น = ต่อท้าย)
 */
function fenix_home_insert_after( $array, $after, $insert ) {
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

/* ==============================================================
 * ตัวช่วยของ front-page.php
 * ============================================================== */

/**
 * บทที่เปิดอยู่ตามลำดับบนหน้า · id => { n: '01', label: ป้ายบท }
 * เลขบทนับเฉพาะบทที่เปิด (show_*) ตามลำดับจริง
 */
function fenix_home_chapters() {
	static $chapters = null;
	if ( null !== $chapters ) {
		return $chapters;
	}
	$defs     = array(
		'what'     => array( 'show_about', 'home_what_kicker' ),
		'pain'     => array( 'show_pain', 'home_pain_kicker' ),
		'how'      => array( 'show_steps', 'steps_kicker' ),
		'features' => array( 'show_features', 'home_features_kicker' ),
		'tests'    => array( 'show_tests', 'tests_kicker' ),
		'install'  => array( 'show_install_home', 'home_install_kicker' ),
		'pricing'  => array( 'show_pricing_home', 'home_pricing_kicker' ),
		'faq'      => array( 'show_faq', 'home_faq_kicker' ),
		'risk'     => array( 'show_risk', 'home_risk_kicker' ),
	);
	$chapters = array();
	foreach ( $defs as $id => $def ) {
		if ( ! fenix_mod( $def[0] ) ) {
			continue;
		}
		$chapters[ $id ] = array(
			'n'     => sprintf( '%02d', count( $chapters ) + 1 ),
			'label' => trim( (string) fenix_mod( $def[1] ) ),
		);
	}
	return $chapters;
}

/**
 * โทนพื้นของแต่ละส่วน (เรียกตามลำดับบนหน้า)
 * - โหมดเข้ม: ใช้ fenix_section_tone() · ส่วนเข้มที่อยู่ติดกันสลับเฉด (#172125 ↔ #10181B) อัตโนมัติ
 *   ส่วนที่ fenix_section_tone() ให้เป็นพื้นสว่าง (install / faq / risk) คงสว่าง
 * - โหมดขาว/ดำสลับ: บท pain / tests / faq เป็นพื้นเทาอ่อน (.hm-surface) สลับกับพื้นขาว
 */
function fenix_home_tone( $key ) {
	static $dark_count = 0;
	if ( ! fenix_is_dark_mode() ) {
		return in_array( $key, array( 'pain', 'tests', 'faq' ), true ) ? ' hm-surface' : '';
	}
	$tone = fenix_section_tone( $key, 1 === $dark_count % 2 );
	if ( '' !== $tone ) {
		$dark_count++;
	}
	return $tone;
}

/**
 * แอตทริบิวต์บนแท็ก section ของบท (data-chapter / data-chapter-label) · home.js ใช้ขับรางเลขบท
 */
function fenix_home_ch_attrs( $id ) {
	$chapters = fenix_home_chapters();
	if ( ! isset( $chapters[ $id ] ) ) {
		return;
	}
	echo ' data-chapter="' . esc_attr( $chapters[ $id ]['n'] ) . '" data-chapter-label="' . esc_attr( $chapters[ $id ]['label'] ) . '"';
}

/**
 * หัวบทมาตรฐาน · "01 / 09 · ป้ายบท" + H2 + คำอธิบาย
 */
function fenix_home_ch_head( $id, $title, $sub = '', $align = 'left' ) {
	$chapters = fenix_home_chapters();
	if ( ! isset( $chapters[ $id ] ) ) {
		return;
	}
	fenix_chapter_head( (int) $chapters[ $id ]['n'], count( $chapters ), $chapters[ $id ]['label'], $title, trim( (string) $sub ), $align );
}

/**
 * ข้อความที่อ่านแล้วเหมือนผลเทรด: ตัวเลขตามด้วย % หรือมีคำว่า กำไร
 */
function fenix_home_is_result_text( $text ) {
	$text = (string) $text;
	return (bool) preg_match( '/[+-]?\d[\d.,]*\s*%/u', $text ) || false !== mb_strpos( $text, 'กำไร' );
}

/**
 * แถบข้อมูลระบบใน Hero (หัวข้อ|ค่า) · ข้ามบรรทัดที่อ่านแล้วเหมือนผลเทรด
 */
function fenix_home_spec_items() {
	$items = array();
	foreach ( fenix_lines( fenix_mod( 'home_hero_spec_items' ) ) as $line ) {
		if ( fenix_home_is_result_text( $line ) ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( ! isset( $parts[1] ) || '' === $parts[1] ) {
			continue;
		}
		$items[] = array(
			'label' => $parts[0],
			'value' => $parts[1],
		);
	}
	return $items;
}

/**
 * บรรทัดของหน้าจอบันทึก (คำสั่ง → "01 · ชื่อขั้น" หรือบรรทัดที่กรอกเอง → บรรทัดพร้อม)
 * ห้ามมีเวลา ราคา หรือผลเทรด: บรรทัดที่มี hh:mm, %, ทศนิยม, สกุลเงิน หรือคำว่า กำไร ถูกข้าม
 *
 * @param array $steps [ [ n, title, desc ], ... ]
 * @return array [ { t: ข้อความ, c: cmd|idx|log|ready }, ... ]
 */
function fenix_home_log_lines( $steps ) {
	$skip  = function ( $text ) {
		return fenix_home_is_result_text( $text )
			|| preg_match( '/\d{1,2}:\d{2}/', $text )
			|| false !== strpos( $text, '%' )
			|| preg_match( '/\d+\.\d+/', $text )
			|| preg_match( '/[$฿€£]\s*\d|\d[\d,]*\s*(?:[$฿€£]|บาท|USD|USC|THB)/iu', $text );
	};
	$lines = array();
	$cmd   = trim( trim( (string) fenix_mod( 'home_how_log_prompt' ) ) . ' ' . trim( (string) fenix_mod( 'home_how_log_start' ) ) );
	if ( '' !== $cmd && ! $skip( $cmd ) ) {
		$lines[] = array(
			't' => $cmd,
			'c' => 'cmd',
		);
	}
	$custom = fenix_lines( fenix_mod( 'home_how_log_lines' ) );
	if ( empty( $custom ) ) {
		foreach ( $steps as $step ) {
			if ( '' === $step['title'] || $skip( $step['title'] ) ) {
				continue;
			}
			$lines[] = array(
				't' => $step['n'] . ' · ' . $step['title'],
				'c' => 'idx',
			);
		}
	} else {
		foreach ( $custom as $line ) {
			if ( $skip( $line ) ) {
				continue;
			}
			$lines[] = array(
				't' => $line,
				'c' => 'log',
			);
		}
	}
	$ready = trim( (string) fenix_mod( 'home_how_log_ready' ) );
	if ( '' !== $ready && ! $skip( $ready ) ) {
		$lines[] = array(
			't' => '› ' . $ready,
			'c' => 'ready',
		);
	}
	return $lines;
}

/**
 * ลิงก์ภายในที่แสดงได้ · slug ภายในเว็บจะคืนค่าเมื่อหน้านั้นเผยแพร่แล้ว (กันลิงก์ไปหน้า 404 ก่อนรัน FALCON Setup)
 * URL ภายนอก / anchor คืนค่าตามเดิม · ว่าง หรือ '#' = ''
 */
function fenix_home_link( $path ) {
	$path = trim( (string) $path );
	if ( '' === $path || '#' === $path ) {
		return '';
	}
	if ( preg_match( '#^/?([a-z0-9-]+)/?$#', $path, $m ) && function_exists( 'fenix_published_page_url' ) ) {
		return '' !== fenix_published_page_url( $m[1] ) ? home_url( '/' . $m[1] . '/' ) : '';
	}
	return fenix_link_url( $path );
}

/**
 * พิมพ์ย่อหน้าจาก textarea (เว้นบรรทัดว่าง = ย่อหน้าใหม่)
 */
function fenix_home_paragraphs( $text, $class = '' ) {
	foreach ( preg_split( '/\n\s*\n/', (string) $text ) as $para ) {
		$para = trim( $para );
		if ( '' === $para ) {
			continue;
		}
		echo '<p' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . nl2br( fenix_text( $para ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text
	}
}

/**
 * ป้ายแท็บ (บท 05 / 07) · ตัดบรรทัดได้เฉพาะหลัง " · " แต่ละช่วงไม่ถูกตัดกลางคำ (เช่น "ย้อน|หลัง" บนจอแคบ)
 * ไม่มี " · " = ข้อความเดิมผ่าน fenix_text()
 */
function fenix_home_tab_label( $text ) {
	$parts = array_values( array_filter( array_map( 'trim', explode( '·', (string) $text ) ), 'strlen' ) );
	if ( count( $parts ) < 2 ) {
		return fenix_text( $text );
	}
	$last = count( $parts ) - 1;
	$out  = array();
	foreach ( $parts as $i => $part ) {
		$out[] = '<span class="hm-nw">' . fenix_text( $part ) . ( $i < $last ? "\u{00A0}·" : '' ) . '</span>';
	}
	return implode( ' ', $out );
}

/**
 * มีอะไรให้แสดงในช่องรูปไหม (มีรูป หรือแอดมินที่จะเห็นกรอบรอใส่รูป) · ใช้ตัดสินเลย์เอาต์ 1 หรือ 2 คอลัมน์
 */
function fenix_home_has_media( $key ) {
	if ( '' !== trim( (string) fenix_mod( $key . '_img' ) ) ) {
		return true;
	}
	return current_user_can( 'edit_theme_options' ) && ( '' !== trim( (string) fenix_mod( $key . '_img_note' ) ) || '' !== trim( (string) fenix_mod( $key . '_img_alt' ) ) );
}

/**
 * อาร์กิวเมนต์ของ fenix_media_slot() บนหน้าแรก · รูปที่มากับธีม (เช่น แบนเนอร์ 1254x1254) ใช้ขนาดจริงของไฟล์
 * กรอบจึงจองพื้นที่ตรงกับรูปตั้งแต่แรก (ไม่มี layout shift) · รูปในคลังสื่อใช้ขนาดจาก metadata ใน fenix_media_info() อยู่แล้ว
 */
function fenix_home_media_args( $key, $width, $height, $args = array() ) {
	static $sizes = array();
	$src  = trim( (string) fenix_mod( $key . '_img' ) );
	$base = trailingslashit( get_template_directory_uri() );
	if ( '' !== $src && 0 === strpos( $src, $base ) ) {
		if ( ! isset( $sizes[ $src ] ) ) {
			$file           = get_template_directory() . '/' . ltrim( substr( $src, strlen( $base ) ), '/' );
			$info           = ( false === strpos( $file, '..' ) && is_file( $file ) ) ? @getimagesize( $file ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors
			$sizes[ $src ] = ( is_array( $info ) && ! empty( $info[0] ) && ! empty( $info[1] ) ) ? array( (int) $info[0], (int) $info[1] ) : array( (int) $width, (int) $height );
		}
		list( $width, $height ) = $sizes[ $src ];
	}
	return array_merge(
		array(
			'width'  => (int) $width,
			'height' => (int) $height,
		),
		$args
	);
}

/**
 * ราคาที่แสดงได้จริง (มีตัวเลข และไม่ใช่ placeholder อย่าง "X,XXX" / "ระบุ...")
 */
function fenix_home_price_ready( $price ) {
	$price = trim( (string) $price );
	return '' !== $price && (bool) preg_match( '/\d/', $price ) && ! fenix_is_placeholder( $price );
}

/**
 * ปุ่มติดต่อของแพ็กเกจ · ปลายทางเดียวกับ fenix_contact_button() (LINE → /go/ → ไม่แสดง) ไม่มีปุ่มใดชี้ '#'
 * เพิ่ม data-line-pkg ให้ main.js ส่งชื่อแพ็กเกจไปกับ event line_click
 */
function fenix_home_contact_link( $text, $class, $pos, $pkg = '' ) {
	static $hinted = false;
	$target = fenix_contact_target();
	if ( '' === $target['url'] ) {
		if ( ! $hinted && current_user_can( 'edit_theme_options' ) ) {
			$hinted = true;
			echo '<span class="admin-hint">ปุ่มติดต่อถูกซ่อน: ใส่ลิงก์ LINE ที่ ปรับแต่ง → 1) ช่องทางติดต่อ</span>';
		}
		return;
	}
	$fallback = trim( (string) fenix_mod( 'contact_fallback_text' ) );
	$label    = $target['is_line'] ? trim( (string) $text ) : $fallback;
	if ( '' === $label ) {
		$label = '' !== $fallback ? $fallback : trim( (string) fenix_mod( 'footer_line_text' ) );
	}
	printf(
		'<a class="%1$s" href="%2$s"%3$s data-line-pos="%4$s"%5$s>%6$s<span>%7$s</span>%8$s</a>',
		esc_attr( $class ),
		esc_url( $target['url'] ),
		$target['is_line'] ? ' target="_blank" rel="noopener"' : '',
		esc_attr( $pos ),
		'' !== $pkg ? ' data-line-pkg="' . esc_attr( $pkg ) . '"' : '',
		fenix_icon( $target['is_line'] ? 'line' : 'chat' ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( $label ),
		fenix_icon( 'arrow' ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/**
 * คำประสมที่เบราว์เซอร์มักตัดกลางคำในหัวข้อบทของหน้าแรก (เช่น "ต่าง|กันอย่างไร", "ระบบตัว|เดียวกัน")
 * ใช้เฉพาะหน้าแรก · "|" = รอยต่อที่ห้ามตัดบรรทัด (fenix_keep_words ใส่ word joiner ให้)
 */
function fenix_home_keep_words( $words ) {
	if ( ! is_front_page() ) {
		return $words;
	}
	return array_merge( (array) $words, array( 'ต่าง|กัน', 'ตัว|เดียว' ) );
}
add_filter( 'fenix_keep_words', 'fenix_home_keep_words' );

/**
 * โหลดภาพ Hero ล่วงหน้าบนจอกว้าง (เฉพาะหน้าแรกที่ใส่ภาพ Hero เอง)
 */
function fenix_home_preload_hero() {
	if ( ! is_front_page() || ! fenix_mod( 'show_hero' ) ) {
		return;
	}
	if ( function_exists( 'fenix_has_elementor_content' ) && fenix_has_elementor_content() && fenix_uses_elementor_page_template() ) {
		return;
	}
	$src = trim( (string) fenix_mod( 'hero_image' ) );
	if ( '' === $src ) {
		return;
	}
	printf( '<link rel="preload" as="image" href="%s" media="(min-width: 961px)" fetchpriority="high">' . "\n", esc_url( $src ) );
}
add_action( 'wp_head', 'fenix_home_preload_hero', 1 );

<?php
/**
 * FALCON PRO EA · โมดูลหน้าเนื้อหา
 * (Backtest / Forward Test / Pricing / Risk Disclosure / เพจเอกสาร / หน้ารวมบทความ / บทความ / ค้นหา / 404)
 *
 * - ค่าเริ่มต้นใหม่ + เขียนข้อความเดิมใหม่ให้เป็นของ FALCON เอง ผ่านฟิลเตอร์ 'fenix_defaults'
 * - ฟิลด์ Customizer ของหน้ากลุ่มนี้ ผ่านฟิลเตอร์ 'fenix_customizer_sections'
 * - ตัวช่วยที่เทมเพลตของโมดูลนี้ใช้ (prefix fenix_pages_)
 *
 * กฎเนื้อหา: ห้ามใส่ตัวเลขผลการเทรดสมมติ ห้ามรับประกันกำไร ห้ามลดทอนคำเตือนความเสี่ยง
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น
 * ============================================================== */

add_filter( 'fenix_defaults', 'fenix_pages_defaults' );

function fenix_pages_defaults( $d ) {
	return array_merge(
		$d,
		array(
			/* ---------- หน้า Backtest ---------- */
			'backtest_kicker'      => 'Backtest',
			'backtest_sub'         => 'ตั้งค่าการจำลองย้อนหลังบน MT5 ให้ใกล้บัญชีจริง แล้วแยกให้ออกว่าตัวเลขในรายงานบอกอะไร และบอกอะไรไม่ได้',
			'backtest_intro'       => 'Backtest คือการจำลองให้ EA เทรดกับราคาในอดีตด้วยกฎชุดเดียวกับที่จะใช้จริง เพื่อดูว่าระบบรับมือกับจังหวะตลาดแต่ละแบบอย่างไร ผลที่ได้เป็นข้อมูลประกอบการตัดสินใจ ไม่ใช่คำยืนยันว่าตลาดข้างหน้าจะให้ผลแบบเดียวกัน',
			'backtest_img_mobile'  => '',
			'backtest_img_alt'     => '',
			'backtest_img_caption' => 'ภาพประกอบวิธีทดสอบ ไม่ใช่ผลการทดสอบ',
			'backtest_img_note'    => 'ใส่ภาพหน้าต่างตั้งค่าการทดสอบของ MT5 (ชื่อ EA, สัญลักษณ์, Timeframe, ช่วงวันที่, Modelling) ห้ามมีตัวเลขผลการทดสอบ และห้ามทำให้ดูเหมือนหน้ารายงานของเว็บยืนยันผลภายนอก',
			'backtest_note'        => 'ตัวเลขจาก Backtest ขยับได้มากตามคุณภาพข้อมูลราคา โหมด Modelling ค่า Spread ค่าคอมมิชชัน และ Slippage ที่ตั้งไว้ตอนทดสอบ ผลชุดใดที่ไม่บอกค่าเหล่านี้ ควรอ่านอย่างระมัดระวัง',
			'backtest_disclaimer'  => 'ตัวเลขจากการจำลองย้อนหลังเป็นข้อมูลเพื่อศึกษาการทำงานของระบบ ไม่ใช่หลักประกันว่าผลข้างหน้าจะออกมาเหมือนเดิม และไม่นับเป็นคำแนะนำการลงทุน การเทรดมีความเสี่ยงสูง เงินทุนอาจลดลงบางส่วนหรือหมดทั้งบัญชี',
			'backtest_cta_title'   => 'ตั้งค่าการทดสอบแล้วยังไม่แน่ใจ?',
			'backtest_cta_text'    => 'ถามทีมงานได้ว่าควรตั้ง Modelling, Spread และช่วงวันที่อย่างไร ให้การทดสอบใกล้กับเงื่อนไขของบัญชีที่คุณจะใช้จริง',

			/* การ์ดสถานะเมื่อยังไม่มีตัวเลขจริง (ใช้ทั้งหน้า Backtest และ Forward) */
			'results_pending_label' => 'สถานะผลทดสอบ',
			'results_pending_title' => 'ผลทดสอบชุดจริงยังไม่พร้อมเผยแพร่',
			'results_pending_text'  => 'หน้านี้จะแสดงตัวเลขก็ต่อเมื่อมีผลที่ตรวจย้อนได้ พร้อมเงื่อนไขการทดสอบครบทุกค่า ตอนนี้จึงยังไม่มีตัวเลขใด ๆ ตรงนี้ ระหว่างรอ อ่านวิธีทดสอบและวิธีอ่านรายงานด้านล่าง แล้วลองทำเองบนบัญชีทดลองได้',

			/* ---------- หน้า Forward Test ---------- */
			'forward_kicker'       => 'Forward Test',
			'forward_sub'          => 'ให้ EA ทำงานกับราคาที่เกิดขึ้นใหม่ทุกวัน เพื่อดูว่าผลบนตลาดจริงห่างจากตัวเลขย้อนหลังแค่ไหน',
			'forward_intro'        => 'Forward Test คือการเปิดให้ EA ทำงานกับราคาที่เพิ่งเกิดขึ้นจริงทีละวัน จะใช้บัญชีทดลอง บัญชีเซ็นต์ หรือบัญชีเงินจริงก็ได้ ระบบต้องรับมือกับ Spread, Slippage และความเร็วในการส่งคำสั่งตามสภาพจริง ภาพที่ได้จึงใกล้การใช้งานจริงกว่าการจำลองย้อนหลัง',
			'forward_img_mobile'   => '',
			'forward_img_alt'      => '',
			'forward_img_caption'  => 'ภาพประกอบวิธีอ่านผลการทดสอบ ไม่ใช่ผลการทดสอบ',
			'forward_img_note'     => 'ใส่ภาพประกอบวิธีอ่านผลบนตลาดจริง เช่น ตำแหน่งเส้น Balance กับ Equity หรือแท็บ History ของ MT5 ปิดหรือเบลอตัวเลขทั้งหมด และห้ามเลียนแบบหน้าเว็บยืนยันผลภายนอก',
			'forward_note'         => 'ผลของช่วงเวลาหนึ่งใช้แทนช่วงเวลาอื่นไม่ได้ ตลาดที่เปลี่ยนจังหวะอาจทำให้ตัวเลขชุดต่อไปต่างออกไปมาก ควรดูระยะเวลาที่รันและประเภทบัญชีประกอบทุกครั้ง',
			'forward_disclaimer'   => 'ผลที่ได้จากการรันกับตลาดจริง ไม่ว่าบนบัญชีทดลองหรือบัญชีเงินจริง บอกได้เฉพาะช่วงเวลาที่รันอยู่ ตัวเลขที่ผ่านมาไม่ใช่หลักประกันผลข้างหน้า และไม่นับเป็นคำแนะนำการลงทุน การเทรดมีความเสี่ยงสูง เงินทุนอาจลดลงบางส่วนหรือหมดทั้งบัญชี',
			'forward_cta_title'    => 'อยากเริ่มจากบัญชีทดลองหรือบัญชีเซ็นต์ก่อน?',
			'forward_cta_text'     => 'เล่าทุนและโบรกเกอร์ที่ใช้ให้ทีมงานฟัง แล้วคุยกันว่าควรรันทดสอบนานแค่ไหน และควรจับตาค่าอะไรบ้างก่อนตัดสินใจ',

			/* ---------- หน้า Pricing ---------- */
			'pricing_page_kicker'     => 'Pricing',
			'pricing_sub'             => 'แต่ละแพ็กเกจต่างกันที่จำนวนบัญชีและระดับการดูแล ไม่ได้ต่างกันที่ผลการเทรด',
			'pricing_kicker'          => 'Packages',
			'pricing_title'           => 'แพ็กเกจที่เปิดให้ใช้งาน',
			'pricing_subtitle'        => 'ราคาบนการ์ดคือค่าไฟล์ สิทธิ์ใช้งาน และการดูแลจากทีมงาน เทียบรายละเอียดทีละหัวข้อด้านล่าง แล้วถามทีมงานให้ชัดก่อนชำระเงิน',
			'pricing_note'            => 'ราคาในการ์ดเป็นค่าเครื่องมือและบริการ ไม่ได้บอกผลกำไรที่จะได้ ราคาและเงื่อนไขอาจปรับเปลี่ยน ให้ยึดตามที่ทีมงานยืนยันกับคุณก่อนชำระเงิน',
			'pricing_flag_label'      => 'แนะนำ',
			'pricing_contact_label'   => 'สอบถามราคา',
			'pricing_contact_via'     => 'ทาง LINE',

			/* แพ็กเกจ (หมวด 10 ใช้ร่วมกับหน้าแรก) · เขียนใหม่ทั้งชุด ให้ตรงกับตาราง License และตารางเปรียบเทียบ */
			'pkg1_tag'                => 'ติดตั้งและดูแลเองได้ ใช้กับบัญชีเดียว',
			'pkg1_features'           => "สิทธิ์ใช้งานกับบัญชีเทรด 1 บัญชี\nคู่มือติดตั้งภาษาไทยทีละขั้น\nรับไฟล์รุ่นใหม่ตามรอบอัปเดต\nถามปัญหาการใช้งานทาง LINE",
			'pkg2_tag'                => 'รันต่อเนื่อง และอยากได้ค่าตั้งต้นพร้อมใช้',
			'pkg2_features'           => "สิทธิ์ใช้งานกับบัญชีเทรด 1 ถึง 2 บัญชี\nไฟล์ Preset สำหรับเริ่มต้นตามระดับความเสี่ยง\nรับไฟล์รุ่นใหม่ตามรอบอัปเดต\nทีมงานตอบคำถามแบบรายบุคคลทาง LINE",
			'pkg3_tag'                => 'อยากให้ทีมงานช่วยตั้งแต่ติดตั้งจนระบบรันได้',
			'pkg3_features'           => "ทีมงานช่วยติดตั้งบนคอมพิวเตอร์หรือ VPS ของคุณ\nไล่ตั้งค่าความเสี่ยงให้เข้ากับทุนไปด้วยกัน\nช่วยเช็กสถานะระบบในช่วงแรกของการใช้งาน\nติดต่อทีมงานได้ใกล้ชิดกว่าแพ็กเกจอื่น",

			'compare_kicker'          => 'Compare',
			'compare_title'           => 'เทียบแพ็กเกจทีละหัวข้อ',
			'compare_yes_label'       => 'มี',
			'compare_no_label'        => 'ไม่มี',
			'compare_rows'            => "หัวข้อ | Starter | Pro | VIP\nบัญชีเทรดที่ใช้สิทธิ์ได้ | 1 | 1–2 | ตามที่ตกลง\nPreset สำหรับเริ่มต้น | ✗ | ✓ | ✓\nทีมงานติดตั้งให้บนเครื่องหรือ VPS | ✗ | ✗ | ✓\nรับไฟล์รุ่นใหม่ตามรอบอัปเดต | ✓ | ✓ | ✓\nการตอบคำถามทาง LINE | ทั่วไป | รายบุคคล | ใกล้ชิด",
			'pricing_license_kicker'  => 'License',
			'pricing_license_title'   => 'ใช้ได้กี่บัญชี และติดตั้งบน VPS อย่างไร',
			'pricing_license_text'    => 'สรุปเงื่อนไขการใช้งานของแต่ละแพ็กเกจ ก่อนชำระเงินให้ทีมงานยืนยันอีกครั้งว่าตรงกับบัญชีที่คุณจะใช้',
			'pricing_license_rows'    => "แพ็กเกจ | บัญชีเทรดที่ผูกสิทธิ์ได้ | ติดตั้งบน VPS | ย้ายไปบัญชีใหม่\nStarter | 1 บัญชี | ทำเองตามคู่มือ | บอกทีมงานให้เปลี่ยนบัญชีที่ผูกไว้\nPro | 1 ถึง 2 บัญชี | ทำเองตามคู่มือ | บอกทีมงานให้เปลี่ยนบัญชีที่ผูกไว้\nVIP | ตามที่ตกลงกับทีมงาน | ทีมงานติดตั้งให้ | บอกทีมงานให้เปลี่ยนบัญชีที่ผูกไว้",
			'pricing_license_points'  => "เงินทุนเทรดอยู่ในบัญชีของคุณกับโบรกเกอร์ตลอดเวลา ไม่ต้องโอนมาที่ทีมงาน\nทีมงานไม่ขอรหัสผ่านหลัก (Master password) ของบัญชีเทรด ขั้นตอนที่ต้องใช้รหัสผ่าน คุณเป็นคนกรอกเอง\nค่าเช่า VPS ไม่ได้อยู่ในตารางนี้ ถามทีมงานได้ว่าแพ็กเกจที่สนใจรวมอะไรบ้าง\nไฟล์และสิทธิ์ใช้งานมีไว้สำหรับผู้ซื้อ รายละเอียดเป็นไปตาม[เงื่อนไขการใช้บริการ](/terms-of-use/)",
			'pricing_order_kicker'    => 'How to Order',
			'pricing_order_title'     => 'ตั้งแต่ทักทีมงานจนระบบพร้อมทำงาน',
			'pricing_order_sub'       => 'คุยและชำระเงินผ่านช่องทางทางการของ FALCON PRO EA เท่านั้น ถ้ามีบัญชีอื่นทักมาขอรับเงินแทน ให้ตรวจสอบกับทีมงานก่อนเสมอ',
			'pricing_order_steps'     => "เล่าเป้าหมายให้ทีมงานฟัง | บอกทุนโดยประมาณ โบรกเกอร์ที่ใช้ และประสบการณ์กับ MT5 ยิ่งข้อมูลครบ ทีมงานยิ่งแนะนำได้ตรง\nคุยเรื่องความเสี่ยงก่อนเลือกแพ็กเกจ | ทีมงานช่วยดูว่าการตั้งค่าแบบไหนเข้ากับทุน และจะบอกตรง ๆ ถ้าตอนนี้ยังไม่ใช่เวลาที่เหมาะจะเริ่ม\nชำระเงินผ่านช่องทางที่ยืนยันแล้ว | โอนเฉพาะบัญชีที่ทีมงานแจ้งในช่องทางทางการ และตรวจชื่อผู้รับให้ตรงก่อนโอนทุกครั้ง\nรับไฟล์ คู่มือ และ Preset | ได้ไฟล์ .ex5 คู่มือภาษาไทย และไฟล์ตั้งค่าตามแพ็กเกจ พร้อมคำอธิบายว่าไฟล์ไหนใช้ในกรณีใด\nติดตั้งแล้วให้ทีมงานช่วยตรวจ | ติดตั้งตาม[คู่มือวิธีติดตั้ง](/how-to-install/) แล้วส่งภาพหน้าจอกราฟกับแท็บ Experts ให้ทีมงานดูความพร้อม",
			'pricing_order_btn_text'  => 'เริ่มคุยกับทีมงาน',
			'pricing_cta_title'       => 'เทียบแล้วยังเลือกไม่ลง?',
			'pricing_cta_text'        => 'บอกทีมงานว่าคุณดูแล MT5 เองได้แค่ไหนและมีกี่บัญชี แล้วค่อยตัดสินใจจากข้อมูลที่ครบก่อนชำระเงิน',

			/* ---------- หน้า Risk Disclosure ---------- */
			'riskpage_kicker'        => 'Risk Disclosure',
			'riskpage_sub'           => 'สรุปความเสี่ยงจากตลาด ข้อจำกัดของระบบ และหน้าที่ของผู้ใช้ ที่ควรเข้าใจก่อนให้ EA ทำงานกับเงินจริง',
			'riskpage_intro'         => 'FALCON PRO EA ส่งคำสั่งเทรดตามกฎที่ตั้งไว้ในบัญชีของคุณเอง ทุกออเดอร์จึงใช้เงินทุนของคุณจริง ก่อนเริ่มใช้งาน โปรดอ่านทุกหัวข้อในหน้านี้ และถ้าข้อใดยังไม่ชัด ให้ถามทีมงานจนเข้าใจก่อนตัดสินใจ',
			'riskpage_image_caption' => 'ภาพประกอบแนวคิดการคุมขนาด Lot ให้สัมพันธ์กับทุน (ตัวอย่างเพื่อการศึกษา ไม่ใช่ผลการเทรด)',
			'rp_block1_title'        => 'เงินทุนที่ใช้เทรดอาจหายไปบางส่วนหรือทั้งหมด',
			'rp_block1_text'         => 'การเทรด Forex ทองคำ CFD และสินทรัพย์ทางการเงินอื่นมีความเสี่ยงสูงกว่าการออมหรือการลงทุนทั่วไปมาก ราคาอาจกระโดดแรงในเวลาสั้น ๆ จนขาดทุนเกินกว่าที่คาดไว้ ความเสียหายเกิดได้ตั้งแต่บางส่วนไปจนถึงเงินทั้งหมดในบัญชี จึงควรใช้เฉพาะเงินที่ถ้าเสียไปแล้วไม่กระทบชีวิตประจำวัน ไม่ใช้เงินกู้หรือเงินสำรองฉุกเฉิน',
			'rp_block2_title'        => 'ไม่มีคำรับประกันผลกำไร',
			'rp_block2_text'         => 'FALCON PRO EA ส่งคำสั่งเมื่อเงื่อนไขที่ตั้งไว้ครบ ความมีวินัยของระบบช่วยตัดการตัดสินใจตามอารมณ์ได้ แต่เปลี่ยนความไม่แน่นอนของตลาดให้เป็นกำไรที่แน่นอนไม่ได้ ผลที่ออกมาแปรตามจังหวะของตลาด การตั้งค่าที่คุณเลือก เงินทุนที่ใช้ และกติกาการเทรดของโบรกเกอร์ ถ้ามีใครรับปากว่ากำไรแน่นอน คนนั้นไม่ได้พูดในนามของเรา',
			'rp_block3_title'        => 'ตัวเลขจากการทดสอบและผลที่ผ่านมาไม่ได้บอกอนาคต',
			'rp_block3_text'         => 'ตัวเลขจากการทดสอบย้อนหลังบอกได้แค่ว่าระบบน่าจะทำอะไรในตลาดที่ผ่านไปแล้ว และตัวเลขจากการรันกับตลาดจริงก็เล่าได้เฉพาะช่วงเวลาที่รันอยู่ ตลาดข้างหน้าอาจมีความผันผวน สภาพคล่อง และต้นทุนต่างออกไป ผลที่ผ่านมาจึงรับประกันผลลัพธ์ในอนาคตไม่ได้',
			'rp_block4_title'        => 'การตัดสินใจและการดูแลบัญชีเป็นของคุณ',
			'rp_block4_text'         => 'ผู้ใช้งานเป็นคนกำหนดทุกอย่างที่มีผลต่อบัญชี ตั้งแต่โบรกเกอร์ที่เปิดบัญชี ขนาด Lot ระดับความเสี่ยงที่ยอมรับ ไปจนถึงจังหวะที่เปิดหรือหยุด EA ความรับผิดชอบต่อผลการเทรด การบริหารความเสี่ยง และการรักษารหัสผ่านให้ปลอดภัยจึงเป็นของผู้ใช้งานเอง',
			'rp_block5_title'        => 'เนื้อหาบนเว็บนี้อธิบายเครื่องมือ ไม่ใช่คำแนะนำการลงทุน',
			'rp_block5_text'         => 'บทความ คู่มือ และคำตอบจากทีมงานมีไว้อธิบายการใช้เครื่องมือและความรู้ทั่วไป ไม่ได้ประเมินฐานะการเงิน เป้าหมาย หรือภาระของผู้อ่านแต่ละคน จึงนับเป็นคำแนะนำด้านการเงิน การลงทุน หรือภาษีไม่ได้ ถ้าต้องการคำแนะนำที่เหมาะกับตัวคุณ ควรปรึกษาผู้ให้คำปรึกษาที่ได้รับใบอนุญาต',
			'rp_block6_title'        => 'ระบบ การเชื่อมต่อ และโบรกเกอร์อาจทำให้คำสั่งคลาดเคลื่อน',
			'rp_block6_text'         => 'ทุกคำสั่งของ EA ต้องผ่านโปรแกรม MT5 ที่เปิดค้างไว้ และต้องส่งถึงเซิร์ฟเวอร์ของโบรกเกอร์ได้ทันเวลา ถ้า VPS รีสตาร์ต อินเทอร์เน็ตหลุด ไฟดับ หรือ MT5 อัปเดตแล้วไม่เปิดกลับมา ระบบจะหยุดโดยที่คุณอาจไม่รู้ตัว ฝั่งโบรกเกอร์เองก็อาจปฏิเสธคำสั่ง ส่ง Requote หรือจับคู่ราคาคลาดจากที่ตั้งไว้ในช่วงตลาดผันผวน จึงควรตรวจสถานะระบบและบัญชีเป็นประจำ',
			'riskpage_cta_title'     => 'ยังมีเรื่องความเสี่ยงที่อยากถามให้ชัด?',
			'riskpage_cta_text'      => 'ถามทีมงานได้ทุกเรื่องก่อนเริ่ม ตั้งแต่กรณีเลวร้ายของการตั้งค่าที่คุณเลือก ไปจนถึงวิธีเช็กว่าระบบยังทำงานปกติ',

			/* ---------- เพจเอกสาร (page.php: เกี่ยวกับเรา / นโยบาย / เงื่อนไข / ลบข้อมูล) ---------- */
			'doc_updated_label'   => 'ปรับปรุงล่าสุด',
			'doc_toc_label'       => 'สารบัญ',
			'aboutpage_cta_title' => 'อยากคุยกับทีมที่ดูแล FALCON PRO EA โดยตรง?',
			'aboutpage_cta_text'  => 'ถามเรื่องการติดตั้ง ความเสี่ยง หรือแพ็กเกจได้ก่อนตัดสินใจ ทีมงานตอบเป็นภาษาไทย',

			/* ---------- หน้ารวมบทความ (index.php) ---------- */
			'articles_kicker'           => 'Articles',
			'articles_title'            => 'คลังความรู้ EA บน MT5',
			/* blog_subtitle: คำโปรยใต้หัวข้อหน้า /articles/ (คีย์เดิมของธีม ย้ายมาอยู่หมวดหน้าบทความ) */
			'blog_subtitle'             => 'เรื่องที่ควรรู้ก่อนและระหว่างใช้ EA ตั้งแต่พื้นฐาน MT5 การคุมขนาด Lot จนถึงต้นทุนแฝงอย่าง Spread และ Slippage เขียนภาษาไทยให้อ่านจบแล้วนำไปทำต่อได้',
			'articles_count_text'       => '{n} บทความ',
			'articles_all_label'        => 'ทั้งหมด',
			'articles_load_more'        => 'โหลดเพิ่ม',
			'articles_prev_label'       => 'ก่อนหน้า',
			'articles_next_label'       => 'ถัดไป',
			'articles_empty_text'       => 'กำลังเตรียมบทความ ระหว่างรอ ลองเริ่มจากคู่มือทีละขั้นด้านล่าง',
			'articles_guides_kicker'    => 'Guides',
			'articles_guides_title'     => 'คู่มือที่เปิดอ่านได้แล้ว',
			'articles_guides_sub'       => 'เริ่มจากหัวข้อที่ตรงกับขั้นที่คุณอยู่ ตั้งแต่เปิดบัญชี ติดตั้ง ทดสอบ ไปจนถึงรันต่อเนื่องบน VPS',
			'articles_guides_items'     => "how-to-install | วางไฟล์ .ex5 เปิด Algo Trading แล้วเช็กว่า EA เริ่มทำงาน\nopen-mt5-account | เลือกชนิดบัญชี ยืนยันตัวตน และเตรียมบัญชีให้พร้อมรัน EA\nmt5-login | ลงโปรแกรม MetaTrader 5 แล้วเข้าบัญชีด้วยเลขบัญชีและชื่อเซิร์ฟเวอร์\nbacktest | จำลองการเทรดใน Strategy Tester แล้วตรวจรายงานทีละค่า\nforward-test | รันกับตลาดจริงบนบัญชีเดโมหรือ Cent ก่อนใช้เงินจริง\nvps-windows | ต่อ Windows VPS ด้วย Remote Desktop ให้ MT5 ทำงานทั้งวัน\nvps-android | ใช้ Windows App บนมือถือ Android เช็กสถานะ EA บน VPS\nvps-ios | ใช้ Windows App บน iOS และ iPadOS เช็กหน้าจอ MT5 บน VPS\ntools | คำนวณ Lot ตามความเสี่ยง และกำไรที่ต้องทำคืนหลัง Drawdown\nrisk-disclosure | อ่านก่อนใช้เงินจริง: ตลาด ระบบ และเรื่องที่ผู้ใช้ต้องดูแลเอง",

			/* ---------- บทความเดี่ยว (single.php) ---------- */
			'article_published_label' => 'เผยแพร่',
			'article_updated_label'   => 'อัปเดตล่าสุด',
			'article_reading_text'    => 'อ่านประมาณ {n} นาที',
			'article_toc_label'       => 'หัวข้อในบทความนี้',
			'article_share_label'     => 'แชร์บทความนี้',
			'article_author_kicker'   => 'ผู้เขียน',
			'article_author_name'     => 'ทีมงาน FALCON PRO EA',
			'article_author_bio'      => 'เรียบเรียงโดยทีมงานที่ดูแล FALCON PRO EA เพื่อใช้ศึกษาเรื่อง EA และ MetaTrader 5 เนื้อหาไม่ใช่คำแนะนำการลงทุน ถ้าพบจุดที่คลาดเคลื่อน แจ้งทีมงานได้ทางช่องทางติดต่อ',
			'article_author_link'     => 'รู้จักทีมงานและหลักการทำงาน',
			'article_disclaimer_text' => 'ตัวเลขในบทความเป็นตัวอย่างสมมติเพื่ออธิบายหลักการเท่านั้น ไม่ใช่ผลการเทรดของ FALCON PRO EA',
			'article_disclaimer_link' => 'เปิดอ่านคำเตือนความเสี่ยงทุกข้อ',
			'article_related_title'   => 'อ่านต่อในหมวดเดียวกัน',

			/* ---------- ค้นหา + 404 ---------- */
			'search_title'              => 'ผลการค้นหา',
			'search_count_text'         => 'พบ {n} รายการ',
			'search_placeholder'        => 'พิมพ์คำที่ต้องการค้นหา…',
			'search_empty_text'         => 'ยังไม่มีบทความที่ตรงกับคำนี้ ลองค้นด้วยคำหลักคำเดียว เช่น VPS หรือ Lot หรือเปิดคู่มือด้านล่าง',
			'err404_title'              => 'หน้านี้ไม่มีอยู่ หรือถูกย้ายไปแล้ว',
			'err404_text'               => 'ลิงก์อาจพิมพ์ผิด หรือหน้านี้เปลี่ยนที่อยู่ ลองค้นด้วยคำสั้น ๆ หรือเลือกไปหน้าที่คนเปิดบ่อยด้านล่าง',
			'err404_search_placeholder' => 'ค้นหาคู่มือหรือบทความ…',
			'err404_home_label'         => 'กลับหน้าแรก',
			'err404_contact_text'       => 'คุยกับทีมงานทาง LINE',
			'err404_links'              => "การทดสอบ | /backtest/\nแพ็กเกจ | /pricing/\nวิธีติดตั้ง | /how-to-install/\nประกาศความเสี่ยง | /risk-disclosure/",
		)
	);
}

/* ==============================================================
 * Customizer
 * ============================================================== */

add_filter( 'fenix_customizer_sections', 'fenix_pages_customizer_sections', 10, 2 );

function fenix_pages_customizer_sections( $sections, $d ) {
	$stats_bt = array();
	for ( $i = 1; $i <= 8; $i++ ) {
		$stats_bt[ 'bt_stat' . $i . '_label' ] = array( 'สถิติ ' . $i . ' · หัวข้อ', 'text' );
		$stats_bt[ 'bt_stat' . $i . '_value' ] = array( 'สถิติ ' . $i . ' · ค่า (ขึ้นต้นด้วย "ระบุ" = ยังไม่แสดง)', 'text' );
	}
	$stats_fw = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$stats_fw[ 'fw_stat' . $i . '_label' ] = array( 'สถิติ ' . $i . ' · หัวข้อ', 'text' );
		$stats_fw[ 'fw_stat' . $i . '_value' ] = array( 'สถิติ ' . $i . ' · ค่า (ขึ้นต้นด้วย "ระบุ" = ยังไม่แสดง)', 'text' );
	}

	$sections['fenix_backtest'] = array(
		'title'       => '20) หน้า Backtest',
		'description' => 'กรอกผลการทดสอบย้อนหลังจริงเท่านั้น ห้ามใส่ตัวเลขสมมติ และห้ามลบ Disclaimer · ข้อความที่ขึ้นต้นด้วย "ระบุ" หรือ "เช่น" จะถูกซ่อนจนกว่าจะกรอกจริง',
		'fields'      => array_merge(
			array(
				'backtest_kicker' => array( 'ป้ายเล็กเหนือชื่อหน้า (อังกฤษสั้น ๆ)', 'text' ),
				'backtest_sub'    => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'backtest_intro'  => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			),
			$stats_bt,
			array(
				'results_pending_label' => array( 'การ์ดยังไม่มีผลทดสอบ · ป้ายเล็ก (ใช้ทั้งหน้า Backtest และ Forward)', 'text' ),
				'results_pending_title' => array( 'การ์ดยังไม่มีผลทดสอบ · หัวข้อ (แสดงเมื่อยังไม่กรอกตัวเลขจริง)', 'text' ),
				'results_pending_text'  => array( 'การ์ดยังไม่มีผลทดสอบ · รายละเอียด', 'textarea' ),
			),
			fenix_media_fields( 'backtest', 'ภาพประกอบวิธีทดสอบ (ไม่ใช่ภาพผล)' ),
			array(
				'backtest_note'       => array( 'หมายเหตุเงื่อนไขการทดสอบ', 'textarea' ),
				'backtest_disclaimer' => array( 'Disclaimer (จำเป็น ห้ามลบ)', 'textarea' ),
				'backtest_cta_title'  => array( 'บล็อกติดต่อท้ายหน้า · หัวข้อ', 'text' ),
				'backtest_cta_text'   => array( 'บล็อกติดต่อท้ายหน้า · คำอธิบาย', 'textarea' ),
			)
		),
	);

	$sections['fenix_forward'] = array(
		'title'       => '21) หน้า Forward Test',
		'description' => 'กรอกผลการทดสอบบนบัญชีจริง/เดโมที่ตรวจสอบได้เท่านั้น ห้ามใส่ตัวเลขสมมติ และห้ามลบ Disclaimer',
		'fields'      => array_merge(
			array(
				'forward_kicker' => array( 'ป้ายเล็กเหนือชื่อหน้า (อังกฤษสั้น ๆ)', 'text' ),
				'forward_sub'    => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'forward_intro'  => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			),
			$stats_fw,
			fenix_media_fields( 'forward', 'ภาพประกอบวิธีอ่านผล (ไม่ใช่ภาพผล)' ),
			array(
				'forward_link_label' => array( 'ข้อความปุ่มลิงก์ผลที่ตรวจสอบได้ (ถ้ามี)', 'text' ),
				'forward_link_url'   => array( 'ลิงก์ผลที่ตรวจสอบได้ (เช่น หน้าติดตามผลของบุคคลที่สาม)', 'url' ),
				'forward_note'       => array( 'หมายเหตุ', 'textarea' ),
				'forward_disclaimer' => array( 'Disclaimer (จำเป็น ห้ามลบ)', 'textarea' ),
				'forward_cta_title'  => array( 'บล็อกติดต่อท้ายหน้า · หัวข้อ', 'text' ),
				'forward_cta_text'   => array( 'บล็อกติดต่อท้ายหน้า · คำอธิบาย', 'textarea' ),
			)
		),
	);

	$sections['fenix_pricing_extra'] = array(
		'title'       => '23) หน้า Pricing (เพิ่มเติม)',
		'description' => 'หน้านี้ใช้แพ็กเกจจากหมวด "10) แพ็กเกจราคา" ร่วมกัน · ถ้าราคายังไม่มีตัวเลข (เช่น X,XXX) การ์ดจะแสดงข้อความสอบถามราคาแทน · ตรวจตาราง License ให้ตรงกับเงื่อนไขจริงก่อนเผยแพร่',
		'fields'      => array(
			'pricing_page_kicker'    => array( 'ป้ายเล็กเหนือชื่อหน้า (อังกฤษสั้น ๆ)', 'text' ),
			'pricing_sub'            => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'pricing_kicker'         => array( 'ส่วนแพ็กเกจ · ป้ายเล็ก', 'text' ),
			'pricing_flag_label'     => array( 'ป้ายบนการ์ดที่ติ๊ก "แนะนำ"', 'text' ),
			'pricing_contact_label'  => array( 'ข้อความแทนราคา (โหมดสอบถามราคา)', 'text' ),
			'pricing_contact_via'    => array( 'บรรทัดเล็กใต้ข้อความแทนราคา (แสดงเฉพาะเมื่อมีลิงก์ LINE)', 'text' ),
			'compare_kicker'         => array( 'ตารางเปรียบเทียบ · ป้ายเล็ก', 'text' ),
			'compare_title'          => array( 'ตารางเปรียบเทียบ · หัวข้อ', 'text' ),
			'compare_rows'           => array( 'ตารางเปรียบเทียบ (บรรทัดละ 1 แถว คั่นช่องด้วย | บรรทัดแรกคือหัวตาราง ใช้ ✓ และ ✗ ได้)', 'textarea' ),
			'compare_yes_label'      => array( 'ตารางเปรียบเทียบ · คำอ่านของ ✓ (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
			'compare_no_label'       => array( 'ตารางเปรียบเทียบ · คำอ่านของ ✗ (สำหรับโปรแกรมอ่านหน้าจอ)', 'text' ),
			'pricing_license_kicker' => array( 'สิทธิ์ใช้งาน · ป้ายเล็ก', 'text' ),
			'pricing_license_title'  => array( 'สิทธิ์ใช้งาน · หัวข้อ', 'text' ),
			'pricing_license_text'   => array( 'สิทธิ์ใช้งาน · คำอธิบาย', 'textarea' ),
			'pricing_license_rows'   => array( 'สิทธิ์ใช้งาน · ตาราง (บรรทัดละ 1 แถว คั่นช่องด้วย " | " บรรทัดแรกคือหัวตาราง)', 'textarea', 'ต้องตรงกับการ์ดแพ็กเกจและตารางเปรียบเทียบ' ),
			'pricing_license_points' => array( 'สิทธิ์ใช้งาน · ข้อควรรู้ (บรรทัดละ 1 ข้อ · [ข้อความ](/slug/) = ลิงก์ภายใน)', 'textarea' ),
			'pricing_order_kicker'   => array( 'ขั้นตอนสั่งซื้อ · ป้ายเล็ก', 'text' ),
			'pricing_order_title'    => array( 'ขั้นตอนสั่งซื้อ · หัวข้อ', 'text' ),
			'pricing_order_sub'      => array( 'ขั้นตอนสั่งซื้อ · คำอธิบาย', 'textarea' ),
			'pricing_order_steps'    => array( 'ขั้นตอนสั่งซื้อ (บรรทัดละ 1 ขั้น รูปแบบ: หัวข้อ | รายละเอียด)', 'textarea' ),
			'pricing_order_btn_text' => array( 'ขั้นตอนสั่งซื้อ · ข้อความปุ่มติดต่อ', 'text' ),
			'pricing_cta_title'      => array( 'บล็อกติดต่อท้ายหน้า · หัวข้อ', 'text' ),
			'pricing_cta_text'       => array( 'บล็อกติดต่อท้ายหน้า · คำอธิบาย', 'textarea' ),
		),
	);

	$risk_blocks = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$risk_blocks[ 'rp_block' . $i . '_title' ] = array( 'หัวข้อ ' . $i, 'text' );
		$risk_blocks[ 'rp_block' . $i . '_text' ]  = array( 'เนื้อหา ' . $i, 'textarea' );
	}
	$sections['fenix_riskpage'] = array(
		'title'       => '24) หน้า Risk Disclosure',
		'description' => 'หน้าประกาศความเสี่ยงฉบับเต็ม มี 6 หัวข้อ ห้ามลบหรือลดทอนคำเตือน · วันที่ปรับปรุงที่ยังเป็น "ระบุ..." จะไม่แสดง',
		'fields'      => array_merge(
			array(
				'riskpage_kicker'        => array( 'ป้ายเล็กเหนือชื่อหน้า (อังกฤษสั้น ๆ)', 'text' ),
				'riskpage_sub'           => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'riskpage_intro'         => array( 'กล่องคำเตือนเกริ่นนำ', 'textarea' ),
				'riskpage_image'         => array( 'ภาพประกอบความเสี่ยง (ไม่บังคับ · ห้ามมีตัวเลขผลการเทรด)', 'image' ),
				'riskpage_image_caption' => array( 'คำบรรยายภาพประกอบความเสี่ยง', 'text' ),
			),
			$risk_blocks,
			array(
				'riskpage_updated'   => array( 'วันที่ปรับปรุงล่าสุด (เช่น ปรับปรุงล่าสุด: 1 ตุลาคม 2569)', 'text' ),
				'riskpage_cta_title' => array( 'บล็อกติดต่อท้ายหน้า · หัวข้อ', 'text' ),
				'riskpage_cta_text'  => array( 'บล็อกติดต่อท้ายหน้า · คำอธิบาย', 'textarea' ),
			)
		),
	);

	$sections['fenix_pages_docs'] = array(
		'title'       => '27) เพจเอกสาร · เกี่ยวกับเรา / นโยบาย / เงื่อนไข',
		'description' => 'เพจทั่วไปที่ไม่ได้เลือกเทมเพลต (เช่น about, privacy-policy, terms-of-use, data-deletion) · วันที่ปรับปรุงดึงจากวันที่แก้ไขเพจล่าสุดอัตโนมัติ · บล็อกติดต่อแสดงเฉพาะเพจ about',
		'fields'      => array(
			'doc_updated_label'   => array( 'ข้อความหน้าวันที่ปรับปรุง', 'text' ),
			'doc_toc_label'       => array( 'หัวกล่องสารบัญ', 'text' ),
			'aboutpage_cta_title' => array( 'เพจเกี่ยวกับเรา · หัวข้อบล็อกติดต่อ', 'text' ),
			'aboutpage_cta_text'  => array( 'เพจเกี่ยวกับเรา · คำอธิบายบล็อกติดต่อ', 'textarea' ),
		),
	);

	$sections['fenix_pages_articles'] = array(
		'title'       => '28) หน้ารวมบทความ & บทความ',
		'description' => 'ข้อความบนหน้า /articles/ หน้าหมวดหมู่ และหน้าบทความเดี่ยว · กล่องผู้เขียนใช้ข้อเท็จจริงเท่านั้น ห้ามอ้างประสบการณ์หรือคุณวุฒิที่ไม่มีจริง',
		'fields'      => array(
			'articles_kicker'         => array( 'หน้ารวม · ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'articles_title'          => array( 'หน้ารวม · หัวข้อ (ใช้เมื่อยังไม่ได้ตั้งเพจบทความ)', 'text' ),
			'blog_subtitle'           => array( 'หน้ารวม · คำโปรยใต้หัวข้อ', 'textarea' ),
			'articles_count_text'     => array( 'หน้ารวม · บรรทัดจำนวนบทความ ({n} = จำนวน)', 'text' ),
			'articles_all_label'      => array( 'หน้ารวม · ป้ายหมวด "ทั้งหมด"', 'text' ),
			'articles_load_more'      => array( 'หน้ารวม · ปุ่มโหลดเพิ่ม', 'text' ),
			'articles_prev_label'     => array( 'หน้ารวม · ลิงก์หน้าก่อนหน้า (เมื่อปิด JavaScript)', 'text' ),
			'articles_next_label'     => array( 'หน้ารวม · ลิงก์หน้าถัดไป (เมื่อปิด JavaScript)', 'text' ),
			'articles_empty_text'     => array( 'หน้ารวม · ข้อความเมื่อยังไม่มีบทความ', 'textarea' ),
			'articles_guides_kicker'  => array( 'รายการคู่มือ · ป้ายเล็ก', 'text' ),
			'articles_guides_title'   => array( 'รายการคู่มือ · หัวข้อ', 'text' ),
			'articles_guides_sub'     => array( 'รายการคู่มือ · คำอธิบาย', 'textarea' ),
			'articles_guides_items'   => array( 'รายการคู่มือ (บรรทัดละ 1 เพจ รูปแบบ: slug | คำอธิบายหนึ่งบรรทัด · แสดงเฉพาะเพจที่เผยแพร่แล้ว)', 'textarea' ),
			'article_published_label' => array( 'บทความ · ข้อความหน้าวันที่เผยแพร่', 'text' ),
			'article_updated_label'   => array( 'บทความ · ข้อความหน้าวันที่อัปเดต', 'text' ),
			'article_reading_text'    => array( 'บทความ · เวลาอ่านโดยประมาณ ({n} = นาที)', 'text' ),
			'article_toc_label'       => array( 'บทความ · หัวกล่องสารบัญ', 'text' ),
			'article_share_label'     => array( 'บทความ · ข้อความหน้าปุ่มแชร์', 'text' ),
			'article_author_kicker'   => array( 'กล่องผู้เขียน · ป้ายเล็ก', 'text' ),
			'article_author_name'     => array( 'กล่องผู้เขียน · ชื่อ', 'text' ),
			'article_author_bio'      => array( 'กล่องผู้เขียน · คำอธิบาย (ข้อเท็จจริงเท่านั้น)', 'textarea' ),
			'article_author_link'     => array( 'กล่องผู้เขียน · ข้อความลิงก์ไปเพจเกี่ยวกับเรา', 'text' ),
			'article_disclaimer_text' => array( 'บทความ · คำเตือนเพิ่มเติมต่อท้ายคำเตือนความเสี่ยงหลัก', 'textarea', 'แสดงต่อจากข้อความในหมวด "13) คำเตือนความเสี่ยง" เสมอ (ข้อความหลักไม่ถูกตัด)' ),
			'article_disclaimer_link' => array( 'บทความ · ข้อความลิงก์ไปหน้าประกาศความเสี่ยง', 'text' ),
			'article_related_title'   => array( 'บทความ · หัวข้อบทความที่เกี่ยวข้อง', 'text' ),
		),
	);

	$sections['fenix_pages_misc'] = array(
		'title'       => '32) หน้าค้นหา & หน้า 404',
		'description' => 'ข้อความบนหน้าผลการค้นหาและหน้าไม่พบหน้า (404) · ลิงก์ด่วน 404 ใช้รูปแบบ: ชื่อ | /slug/',
		'fields'      => array(
			'search_title'              => array( 'ค้นหา · หัวข้อ (ต่อท้ายด้วยคำค้น)', 'text' ),
			'search_count_text'         => array( 'ค้นหา · บรรทัดจำนวนผล ({n} = จำนวน)', 'text' ),
			'search_placeholder'        => array( 'ค้นหา · ข้อความในช่องค้นหา', 'text' ),
			'search_empty_text'         => array( 'ค้นหา · ข้อความเมื่อไม่พบผล', 'textarea' ),
			'err404_title'              => array( '404 · หัวข้อ', 'text' ),
			'err404_text'               => array( '404 · คำอธิบาย', 'textarea' ),
			'err404_search_placeholder' => array( '404 · ข้อความในช่องค้นหา', 'text' ),
			'err404_home_label'         => array( '404 · ปุ่มกลับหน้าแรก', 'text' ),
			'err404_contact_text'       => array( '404 · ปุ่มติดต่อ (แสดงเมื่อมีลิงก์ LINE หรือหน้า /go/)', 'text' ),
			'err404_links'              => array( '404 · ลิงก์ด่วน (บรรทัดละ 1 ลิงก์ รูปแบบ: ชื่อ | /slug/)', 'textarea' ),
		),
	);

	/* คีย์ที่ย้ายมาอยู่หมวดของโมดูลนี้: ถอดออกจากหมวดอื่นให้เหลือช่องเดียว (หมวดที่ว่างแล้วถูกลบทิ้ง) */
	$own = array(
		'blog_subtitle'         => 'fenix_pages_articles',
		'results_pending_title' => 'fenix_backtest',
		'results_pending_text'  => 'fenix_backtest',
	);
	foreach ( $sections as $sid => $section ) {
		if ( empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
			continue;
		}
		foreach ( $own as $key => $home_sid ) {
			if ( $sid !== $home_sid && isset( $section['fields'][ $key ] ) ) {
				unset( $sections[ $sid ]['fields'][ $key ] );
			}
		}
		if ( empty( $sections[ $sid ]['fields'] ) && ! in_array( $sid, $own, true ) ) {
			unset( $sections[ $sid ] );
		}
	}

	return $sections;
}

/* ==============================================================
 * ตัวช่วยทั่วไป
 * ============================================================== */

/**
 * คลาสโทนพื้นของ section ในหน้ากลุ่มนี้
 * โหมดเข้ม: ตาม fenix_section_tone() · โหมดขาว/ดำสลับ (balanced): ใช้ .section-alt เพื่อสลับจังหวะ section ที่ติดกัน
 */
function fenix_pages_tone( $key, $alt = false, $light_alt = false ) {
	$tone = fenix_section_tone( $key, $alt );
	if ( '' === $tone && $light_alt ) {
		return ' section-alt';
	}
	return $tone;
}

/**
 * ค่ามีเนื้อหาจริง (ไม่ว่าง ไม่ใช่ "ระบุ..." / "เช่น...")
 */
function fenix_pages_has( $value ) {
	return ! fenix_is_placeholder( $value );
}

/**
 * ข้อความวันที่ที่ยังเป็น placeholder (เช่น "ปรับปรุงล่าสุด: ระบุวันที่" หรือมี <!-- -->)
 */
function fenix_pages_date_pending( $value ) {
	$value = trim( (string) $value );
	return fenix_is_placeholder( $value ) || false !== mb_strpos( $value, 'ระบุ' ) || false !== strpos( $value, '<!--' ) || ! preg_match( '/[0-9\x{0E50}-\x{0E59}]/u', $value );
}

/**
 * แทน {n} ในข้อความด้วยตัวเลข
 */
function fenix_pages_count_text( $key, $n ) {
	return str_replace( '{n}', number_format_i18n( (int) $n ), (string) fenix_mod( $key ) );
}

/**
 * วันที่แบบไทย (พ.ศ.) จากสตริง ISO 8601 ของ WordPress (ใช้วันที่ตามเขตเวลาของเว็บ)
 */
function fenix_pages_thai_date( $iso ) {
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})/', (string) $iso, $m ) ) {
		return '';
	}
	$months = array( 1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม' );
	$month  = (int) $m[2];
	if ( ! isset( $months[ $month ] ) ) {
		return '';
	}
	return (int) $m[3] . ' ' . $months[ $month ] . ' ' . ( (int) $m[1] + 543 );
}

/**
 * เวลาอ่านโดยประมาณ (นาที) · ภาษาไทยไม่มีการเว้นวรรคระหว่างคำ จึงนับจากจำนวนตัวอักษร
 */
function fenix_pages_reading_minutes( $html ) {
	$text  = preg_replace( '/\s+/u', '', wp_strip_all_tags( (string) $html ) );
	$chars = function_exists( 'mb_strlen' ) ? mb_strlen( (string) $text, 'UTF-8' ) : strlen( (string) $text ) / 3;
	return max( 1, (int) ceil( $chars / 850 ) );
}

/**
 * alt ของรูปหน้าปก: ใช้ข้อความ alt จากคลังสื่อ · ถ้าว่าง หรือเป็นข้อความกลางที่ Setup ใส่ให้ทุกรูป ใช้ชื่อบทความแทน
 */
function fenix_pages_featured_alt( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$title   = wp_strip_all_tags( get_the_title( $post_id ) );
	$thumb   = function_exists( 'get_post_thumbnail_id' ) ? (int) get_post_thumbnail_id( $post_id ) : 0;
	$alt     = $thumb ? trim( (string) get_post_meta( $thumb, '_wp_attachment_image_alt', true ) ) : '';
	$generic = (array) apply_filters( 'fenix_pages_generic_alts', array( 'FALCON PRO EA ผู้ช่วยเทรดอัตโนมัติสำหรับ MT5 (ภาพประกอบ)' ) );
	if ( '' === $alt || in_array( $alt, $generic, true ) ) {
		return $title;
	}
	return $alt;
}

/**
 * แตกบรรทัด "ซ้าย | ขวา" เป็น array( ซ้าย, ขวา )
 */
function fenix_pages_pairs( $text ) {
	$out = array();
	foreach ( fenix_lines( $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' === $parts[0] ) {
			continue;
		}
		$out[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
	}
	return $out;
}

/* ==============================================================
 * ตาราง: จอแคบ (≤ 640px) เรียงแต่ละแถวเป็นการ์ดพร้อมป้ายหัวคอลัมน์
 * ============================================================== */

/**
 * ใส่ data-label ให้ทุก <td> ของ table.data-table จากหัวตาราง และติดคลาส table-wrap--stack table-wrap--cards
 * เทมเพลตของโมดูลนี้เปิดใช้ด้วย fenix_pages_enable_table_cards() ก่อนพิมพ์เนื้อหา
 */
function fenix_pages_stack_tables( $html ) {
	if ( false === stripos( (string) $html, '<table' ) ) {
		return $html;
	}
	$html = preg_replace_callback(
		'#<table\b[^>]*\bdata-table\b[^>]*>.*?</table>#is',
		'fenix_pages_stack_table_cb',
		(string) $html
	);
	return preg_replace( '#<div class="table-wrap">#', '<div class="table-wrap table-wrap--stack table-wrap--cards">', $html );
}

function fenix_pages_stack_table_cb( $match ) {
	$table = $match[0];
	if ( ! preg_match( '#<thead\b[^>]*>(.*?)</thead>#is', $table, $head ) ) {
		return $table;
	}
	preg_match_all( '#<th\b[^>]*>(.*?)</th>#is', $head[1], $cells );
	$labels = array();
	foreach ( $cells[1] as $cell ) {
		$labels[] = trim( wp_strip_all_tags( $cell ) );
	}
	if ( ! $labels ) {
		return $table;
	}
	return preg_replace_callback(
		'#<tr\b([^>]*)>(.*?)</tr>#is',
		function ( $row ) use ( $labels ) {
			$col   = 0;
			$inner = preg_replace_callback(
				'#<td\b([^>]*)>#i',
				function ( $td ) use ( &$col, $labels ) {
					$label = isset( $labels[ $col ] ) ? $labels[ $col ] : '';
					$col++;
					if ( '' === $label || false !== stripos( $td[1], 'data-label' ) ) {
						return $td[0];
					}
					return '<td' . $td[1] . ' data-label="' . esc_attr( $label ) . '">';
				},
				$row[2]
			);
			return '<tr' . $row[1] . '>' . $inner . '</tr>';
		},
		$table
	);
}

function fenix_pages_enable_table_cards() {
	static $done = false;
	if ( ! $done ) {
		add_filter( 'the_content', 'fenix_pages_stack_tables', 30 );
		$done = true;
	}
}

/* ==============================================================
 * ส่วนประกอบของหน้าทดสอบ (Backtest / Forward)
 * ============================================================== */

/**
 * แถวสถิติแบบเส้นบาง (แสดงเฉพาะค่าที่กรอกจริง) หรือการ์ด "ยังไม่มีผลที่เผยแพร่"
 *
 * @param string $prefix 'bt' | 'fw'
 * @param int    $count  จำนวนช่อง
 * @param string $type   'backtest' | 'forward'
 */
function fenix_pages_stats( $prefix, $count, $type ) {
	$rows = array();
	for ( $i = 1; $i <= $count; $i++ ) {
		$label = trim( (string) fenix_mod( $prefix . '_stat' . $i . '_label' ) );
		$value = trim( (string) fenix_mod( $prefix . '_stat' . $i . '_value' ) );
		if ( '' === $label || fenix_is_placeholder( $value ) ) {
			continue;
		}
		$rows[] = array( $label, $value );
	}
	if ( ! $rows ) {
		fenix_pages_pending( $type );
		return;
	}
	$cols = count( $rows ) >= 4 && 0 === count( $rows ) % 4 ? 4 : 3;
	echo '<dl class="stats-line stats-line--' . esc_attr( (string) $cols ) . ' reveal">';
	foreach ( $rows as $row ) {
		echo '<div class="stats-line-item"><dt>' . esc_html( $row[0] ) . '</dt><dd>' . esc_html( $row[1] ) . '</dd></div>';
	}
	echo '</dl>';
}

/**
 * การ์ดสถานะ "ยังไม่มีผลที่เผยแพร่" · ไม่มีตัวเลขสมมติ · ข้อความทั้งหมดมาจาก Customizer
 *
 * @param string $type 'backtest' | 'forward'
 */
function fenix_pages_pending( $type ) {
	$kicker = fenix_mod( 'backtest' === $type ? 'backtest_kicker' : 'forward_kicker' );
	$label  = trim( (string) fenix_mod( 'results_pending_label' ) );
	?>
	<div class="results-pending tests-pending reveal">
		<?php echo fenix_icon_badge( 'backtest' === $type ? 'candles' : 'pulse', 'green' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div>
			<?php if ( '' !== $label || '' !== trim( (string) $kicker ) ) : ?>
				<span class="card-label"><?php echo esc_html( trim( $kicker . ( '' !== $label && '' !== trim( (string) $kicker ) ? ' · ' : '' ) . $label ) ); ?></span>
			<?php endif; ?>
			<h2><?php echo fenix_text( fenix_mod( 'results_pending_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
			<p><?php echo esc_html( fenix_mod( 'results_pending_text' ) ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * ส่วนบนของหน้าทดสอบ: เกริ่นนำ → สถิติ/สถานะ → ภาพประกอบ → หมายเหตุ → คำเตือน
 */
function fenix_pages_tests_top( $type ) {
	$is_bt  = 'backtest' === $type;
	$key    = $is_bt ? 'backtest' : 'forward';
	$intro  = fenix_mod( $key . '_intro' );
	$note   = fenix_mod( $key . '_note' );
	$link   = trim( (string) fenix_mod( 'forward_link_url' ) );
	$label  = trim( (string) fenix_mod( 'forward_link_label' ) );
	?>
	<section class="section tests-top<?php echo esc_attr( fenix_pages_tone( 'tests', true, true ) ); ?>">
		<div class="container">
			<?php if ( fenix_pages_has( $intro ) ) : ?>
				<p class="tests-lead reveal"><?php echo fenix_text( $intro ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
			<?php endif; ?>

			<?php fenix_pages_stats( $is_bt ? 'bt' : 'fw', $is_bt ? 8 : 6, $type ); ?>

			<?php if ( ! $is_bt && '' !== $link && '' !== $label ) : ?>
				<p class="tests-link reveal">
					<a class="btn btn-ghost" href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener nofollow">
						<span><?php echo esc_html( $label ); ?></span>
						<?php echo fenix_icon( 'external', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</p>
			<?php endif; ?>

			<?php
			fenix_media_slot(
				$key,
				array(
					'width'  => 1280,
					'height' => 720,
					'class'  => 'tests-figure',
				)
			);
			?>

			<?php if ( fenix_pages_has( $note ) ) : ?>
				<p class="tests-note reveal"><?php echo fenix_icon( 'flask', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $note ); ?></span></p>
			<?php endif; ?>

			<div class="disclaimer tests-disclaimer">
				<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p><?php echo esc_html( fenix_mod( $key . '_disclaimer' ) ); ?></p>
			</div>
		</div>
	</section>
	<?php
}

/* ==============================================================
 * ส่วนประกอบของหน้า Pricing
 * ============================================================== */

/**
 * ราคาเป็นตัวเลขจริงหรือยัง (X,XXX / ระบุราคา = ยังไม่ใช่)
 */
function fenix_pages_price_ready( $price ) {
	return fenix_pages_has( $price ) && (bool) preg_match( '/\d/', (string) $price );
}

/**
 * ปุ่มบนการ์ดแพ็กเกจ: LINE → หน้า /go/ (ถ้าเผยแพร่แล้ว) → ไม่แสดงปุ่ม
 */
function fenix_pages_package_button( $name, $featured ) {
	$target = fenix_contact_target();
	if ( '' === $target['url'] ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<span class="admin-hint">ปุ่มถูกซ่อน: ใส่ลิงก์ LINE ที่ ปรับแต่ง → 1) ช่องทางติดต่อ หรือเผยแพร่หน้า /go/</span>';
		}
		return;
	}
	printf(
		'<a class="btn %1$s btn-block price-btn" href="%2$s"%3$s data-line-pos="pricing" data-line-pkg="%4$s">%5$s<span>%6$s</span></a>',
		$featured ? 'btn-fire' : 'btn-ghost',
		esc_url( $target['url'] ),
		$target['is_line'] ? ' target="_blank" rel="noopener"' : '',
		esc_attr( $name ),
		fenix_icon( $target['is_line'] ? 'line' : 'chat' ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( fenix_mod( 'pricing_btn_text' ) )
	);
}

/**
 * หัว section แบบชิดซ้าย (kicker + H2 + คำอธิบาย)
 */
function fenix_pages_sec_head( $kicker, $title, $sub = '', $class = '' ) {
	if ( '' === trim( (string) $title ) ) {
		return;
	}
	?>
	<div class="pg-head <?php echo esc_attr( $class ); ?> reveal">
		<?php if ( $kicker ) : ?>
			<span class="kicker"><?php echo esc_html( $kicker ); ?></span>
		<?php endif; ?>
		<h2><?php echo fenix_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
		<?php if ( fenix_pages_has( $sub ) ) : ?>
			<p><?php echo fenix_text( $sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/* ==============================================================
 * รายการคู่มือพร้อมคำอธิบายหนึ่งบรรทัด (เฉพาะเพจที่เผยแพร่แล้ว)
 * ============================================================== */

function fenix_pages_guide_items() {
	$pages = function_exists( 'fenix_site_pages' ) ? fenix_site_pages() : array();
	$items = array();
	foreach ( fenix_pages_pairs( fenix_mod( 'articles_guides_items' ) ) as $pair ) {
		$slug = sanitize_title( $pair[0] );
		$url  = function_exists( 'fenix_published_page_url' ) ? fenix_published_page_url( $slug ) : '';
		if ( '' === $url ) {
			continue;
		}
		$label = $slug;
		if ( isset( $pages[ $slug ] ) ) {
			$label = ! empty( $pages[ $slug ]['menu'] ) ? $pages[ $slug ]['menu'] : $pages[ $slug ]['title'];
		}
		$items[] = array(
			'label' => $label,
			'url'   => $url,
			'desc'  => $pair[1],
		);
	}
	return $items;
}

function fenix_pages_guide_grid( $items ) {
	if ( ! $items ) {
		return;
	}
	?>
	<ol class="guide-more reveal">
		<?php foreach ( $items as $fenix_i => $fenix_item ) : ?>
			<li>
				<a href="<?php echo esc_url( $fenix_item['url'] ); ?>">
					<span class="guide-more-idx"><?php echo esc_html( sprintf( '%02d', $fenix_i + 1 ) ); ?></span>
					<span class="guide-more-body">
						<span class="guide-more-name"><?php echo fenix_text( $fenix_item['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span>
						<?php if ( '' !== $fenix_item['desc'] ) : ?>
							<span class="guide-more-sub"><?php echo fenix_text( $fenix_item['desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span>
						<?php endif; ?>
					</span>
					<?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php
}

/**
 * ปุ่มโหลดเพิ่ม + ลิงก์แบ่งหน้าเมื่อปิด JavaScript (ใช้ใน index.php / search.php)
 */
function fenix_pages_load_more() {
	$query = $GLOBALS['wp_query'];
	if ( (int) $query->max_num_pages <= 1 ) {
		return;
	}
	?>
	<div class="load-more">
		<button type="button" class="btn btn-ghost load-more-btn"
			data-page="<?php echo esc_attr( (string) max( 1, (int) get_query_var( 'paged' ) ) ); ?>"
			data-max="<?php echo esc_attr( (string) (int) $query->max_num_pages ); ?>"
			data-query="<?php echo esc_attr( wp_json_encode( $query->query ) ); ?>">
			<?php echo esc_html( fenix_mod( 'articles_load_more' ) ); ?>
		</button>
	</div>
	<noscript>
		<div class="pagination">
			<?php
			echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
				array(
					'prev_text' => '&larr; ' . esc_html( fenix_mod( 'articles_prev_label' ) ),
					'next_text' => esc_html( fenix_mod( 'articles_next_label' ) ) . ' &rarr;',
				)
			);
			?>
		</div>
	</noscript>
	<?php
}

/**
 * ฟอร์มค้นหา (404 / หน้าผลการค้นหา)
 */
function fenix_pages_search_form( $placeholder, $class = 'error-search' ) {
	?>
	<form class="<?php echo esc_attr( $class ); ?>" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="search" name="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" aria-label="<?php echo esc_attr( $placeholder ); ?>" spellcheck="false" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
		<button type="submit" aria-label="<?php echo esc_attr( $placeholder ); ?>"><?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</form>
	<?php
}

/* ==============================================================
 * บทความเดี่ยว (single.php)
 * ============================================================== */

/**
 * breadcrumb แบบเดียวกับหัวเพจ · ใช้ fenix_breadcrumbs() ให้ตรงกับ BreadcrumbList schema
 */
function fenix_pages_crumbs( $title = '' ) {
	$crumbs = fenix_breadcrumbs( $title );
	if ( count( $crumbs ) < 2 ) {
		return;
	}
	$last = count( $crumbs ) - 1;
	?>
	<nav class="crumbs" aria-label="เส้นทางนำทาง">
		<ol>
			<?php foreach ( $crumbs as $fenix_i => $fenix_crumb ) : ?>
				<li>
					<?php if ( $fenix_crumb['url'] && $fenix_i < $last ) : ?>
						<a href="<?php echo esc_url( $fenix_crumb['url'] ); ?>"><?php echo esc_html( $fenix_crumb['name'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $fenix_crumb['name'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * ลิงก์เพจเกี่ยวกับเรา (เฉพาะเมื่อเผยแพร่แล้ว)
 */
function fenix_pages_about_url() {
	return function_exists( 'fenix_published_page_url' ) ? (string) fenix_published_page_url( 'about' ) : '';
}

/**
 * ข้อมูลวันที่ + เวลาอ่านของบทความ
 *
 * @return array { published, published_iso, modified, modified_iso, minutes }
 */
function fenix_pages_article_meta( $post_id = 0 ) {
	$published_iso = (string) get_the_date( 'c', $post_id ? $post_id : null );
	$modified_iso  = (string) get_the_modified_date( 'c', $post_id ? $post_id : null );
	$published     = fenix_pages_thai_date( $published_iso );
	$modified      = fenix_pages_thai_date( $modified_iso );
	return array(
		'published'     => $published,
		'published_iso' => $published_iso,
		'modified'      => $modified !== $published ? $modified : '',
		'modified_iso'  => $modified_iso,
		'minutes'       => fenix_pages_reading_minutes( get_the_content() ),
	);
}

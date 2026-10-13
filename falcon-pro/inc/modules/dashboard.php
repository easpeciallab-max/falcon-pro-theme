<?php
/**
 * FALCON PRO EA · โมดูลแดชบอร์ด (DASHBOARD)
 *
 * FALCON ใช้งานผ่านแดชบอร์ดสมาชิกทางเดียว (เจ้าของยืนยัน 13 ต.ค. 2026): สมัครสมาชิกด้วยรหัสเชิญ เพิ่มบัญชี MT5 เลือกชุด EA แล้วกดเริ่มรัน
 * MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ที่ทีมงานดูแล · ไม่มีไฟล์ EA หรือไฟล์ Preset ให้ลูกค้า ไม่มีการติดตั้งบนคอมหรือ VPS ของลูกค้า
 * ลูกค้าไม่ต้องเปิดคอมค้างและไม่ต้องเช่า VPS · แอป MT5 บนมือถือหรือคอมมีไว้ดูบัญชีเท่านั้น · การรันบนแดชบอร์ดรวมอยู่ในค่าแพ็กเกจ
 * ไม่มีค่ารายเดือนหรือรายปี · รับเฉพาะบัญชีเงินจริงของโบรกเกอร์พาร์ตเนอร์ (บัญชี Cent ใช้ได้ ไม่รับบัญชีเดโม)
 * โมดูลนี้ดูแลเรื่องแดชบอร์ดบนเว็บ: ลิงก์ ปุ่ม บล็อกแนะนำ และหน้า /dashboard/ ซึ่งเป็นหน้า "วิธีใช้ FALCON" ของเว็บ
 * ห้ามเขียนข้อความที่เทียบแดชบอร์ดกับการติดตั้งเอง หรือเรียกแดชบอร์ดว่า "อีกทางหนึ่ง" / "ทางเลือก"
 *
 * ลิงก์เข้าแดชบอร์ด (ที่อยู่หลักตั้งที่ Customizer ช่อง dashboard_url เท่านั้น · ห้ามพิมพ์ที่อยู่นั้นลงไฟล์อื่นของธีม):
 *   {ที่อยู่หลัก}#visit   โหมดเยี่ยมชม ดูได้โดยไม่ต้องมีบัญชี (บัญชี ออเดอร์ กำไรขาดทุน และการแจ้งเตือนเป็นข้อมูลตัวอย่าง)
 *   {ที่อยู่หลัก}#signup  ฟอร์มสมัครสมาชิก (ต้องมีรหัสเชิญจากทีมงาน)
 *   {ที่อยู่หลัก}#signin  ฟอร์มเข้าสู่ระบบ
 *
 * ส่วนที่โมดูลอื่นเรียกใช้ (เรียกหลัง function_exists เสมอ):
 *   fenix_dashboard_url( $mode )            ที่อยู่ตามโหมด '' | visit | signup | signin · '' เมื่อปิดฟีเจอร์หรือไม่มีที่อยู่
 *   fenix_dashboard_button( $mode, $args )  คืน <a> (target _blank · rel noopener · data-dash · data-dash-pos) หรือ '' เมื่อไม่มีปลายทาง
 *   fenix_dashboard_block( $context, $args ) พิมพ์บล็อกแนะนำ · context = home | go | pricing | page
 *   fenix_dashboard_notes( $which )         คืนหมายเหตุ (รหัสเชิญ / ข้อมูลตัวอย่าง / สถานะ) เป็น <ul>
 *   [falcon_dashboard mode="visit|signup|signin" pos="…"]ข้อความปุ่ม[/falcon_dashboard] (inc/shortcodes.php) สำหรับเนื้อหาเพจ
 *
 * หน้า /dashboard/ (template-dashboard.php): หัวเพจ + ปุ่ม → ใครทำอะไร (คุณ / เซิร์ฟเวอร์ของ FALCON) → ภาพหน้าจอ → สิ่งที่ทำได้ → 5 ขั้นเริ่มใช้ + ปุ่ม
 *   → เนื้อหายาวจากหน้าแก้ไขเพจ (inc/content/pages/dashboard.html) → บล็อกติดต่อ
 *
 * - ข้อความทุกคำเป็น setting: ค่าเริ่มต้น fenix_dashboard_defaults() · ช่องใน Customizer หมวด 1.1 และ 24.1
 * - CSS: assets/css/dashboard.css (ปุ่ม + บล็อกแนะนำ · โหลดทุกหน้า) และ assets/css/dashboard-page.css (เฉพาะหน้า /dashboard/)
 * - นับคลิก: event dashboard_click (dash_mode, dash_pos, link_url, page_path) ใน assets/js/main.js ส่งหลังผู้ใช้ยินยอมหมวดวิเคราะห์เท่านั้น
 *
 * กฎเนื้อหา: ไม่มีตัวเลขผล Backtest หรือตัวเลขตัวอย่างของโหมดเยี่ยมชม (ตัวเลข Backtest อยู่ที่หน้า /backtest/ เท่านั้น)
 * ไม่รับประกันกำไร ไม่บอกว่าบริการเปิดเต็มรูปแบบแล้ว ไม่ระบุที่ตั้งเซิร์ฟเวอร์ ตัวเลข uptime หรือจำนวนลูกค้า
 * ชื่อโบรกเกอร์มาจาก Customizer เท่านั้น ({broker} / [falcon_broker]) · ภาพหน้าจอที่เห็นยอดเงินหรือกำไรขาดทุนตัวอย่างต้องมีคำบรรยายว่าเป็นข้อมูลตัวอย่าง
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น (ข้อความไทยของ FALCON เอง)
 * ============================================================== */

add_filter( 'fenix_defaults', 'fenix_dashboard_defaults' );

/**
 * URL เต็มของภาพหน้าจอแดชบอร์ดที่มากับธีม (assets/img/dashboard/{file}) · คืน '' เมื่อไม่มีไฟล์นั้นจริง
 */
function fenix_dashboard_image_url( $file ) {
	$file = ltrim( str_replace( '\\', '/', trim( (string) $file ) ), '/' );
	if ( '' === $file || false !== strpos( $file, '..' ) ) {
		return '';
	}
	$rel = '/assets/img/dashboard/' . $file;
	return is_file( get_template_directory() . $rel ) ? get_template_directory_uri() . $rel : '';
}

function fenix_dashboard_defaults( $d ) {
	return array_merge(
		$d,
		array(
			/* ---------- ลิงก์ ปุ่ม และหมายเหตุ (ใช้ทั้งเว็บ) ---------- */
			'show_dashboard'         => true,
			'dashboard_url'          => 'https://dashboard.falconpro-th.com/',
			'dashboard_visit_label'  => 'เข้าชมแดชบอร์ดก่อนสมัคร',
			'dashboard_signup_label' => 'สมัครสมาชิกแดชบอร์ด',
			'dashboard_signin_label' => 'เข้าสู่ระบบแดชบอร์ด',
			'dashboard_newtab_hint'  => 'เปิดในแท็บใหม่',
			'dashboard_invite_note'  => 'การสมัครสมาชิกต้องใช้รหัสเชิญ ขอรหัสได้จากทีมงานทาง LINE',
			'dashboard_sample_note'  => 'ในโหมดเยี่ยมชม บัญชี ออเดอร์ ตัวเลขกำไรขาดทุน และการแจ้งเตือนเป็นข้อมูลตัวอย่าง ไม่ใช่ผลการเทรด',
			'dashboard_status_note'  => 'สถานะตอนนี้: แดชบอร์ดอยู่ระหว่างเตรียมเปิดให้บริการ เข้าชมได้แล้ว ส่วนการเชื่อมบัญชี MT5 และการกดเริ่มรันจะใช้ได้เมื่อระบบพร้อม สอบถามกำหนดการได้จากทีมงานทาง LINE',

			/* ---------- บล็อกแนะนำแดชบอร์ด (หน้าแรก / หน้า go / หน้าแพ็กเกจ / หน้าอื่น) ----------
			 * แดชบอร์ดคือทางเดียวที่ใช้ FALCON: ข้อความชุดนี้ไม่เทียบกับการติดตั้งเอง และไม่เรียกแดชบอร์ดว่า "อีกทาง" หรือ "ทางเลือก" */
			'dashboard_block_kicker'  => 'Dashboard',
			'dashboard_block_title'   => 'แดชบอร์ด FALCON PRO: EA ทำงานบนเซิร์ฟเวอร์ คุณสั่งงานและติดตามบัญชีจากเว็บ',
			'dashboard_block_text'    => 'FALCON PRO EA ใช้งานผ่านแดชบอร์ดสมาชิกทางเดียว: สมัครสมาชิกด้วยรหัสเชิญจากทีมงาน เพิ่มบัญชี MT5 เลือกชุด EA แล้วกดเริ่มรัน จากนั้น MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ที่ทีมงานดูแล ไม่มีไฟล์ให้ติดตั้ง และคุณไม่ต้องเปิดคอมค้างหรือเช่า VPS',
			'dashboard_block_points'  => "MT5 กับ EA ทำงานต่อบนเซิร์ฟเวอร์ แม้คุณปิดเบราว์เซอร์หรือปิดเครื่อง\n"
				. "เลือกชุด EA ตามระดับความเสี่ยงที่รับได้ สั่งเริ่มและหยุดเองจากหน้าเว็บ\n"
				. "ดูยอดเงิน ออเดอร์ และประวัติการเทรดได้ทั้งบนคอมและมือถือ\n"
				. 'เปิดเพดานขาดทุน Guardian เองได้ และรับการแจ้งเตือนในเว็บหรือทาง LINE',
			'dashboard_block_caution' => 'การรันบนเซิร์ฟเวอร์ไม่ได้ทำให้ความเสี่ยงของการเทรดลดลง EA ทุกชุดถือออเดอร์ที่ติดลบไว้และไม่ตัดขาดทุนเอง เงินในบัญชีลดลงได้จนหมด',
			'dashboard_block_more'    => 'ดูว่าแดชบอร์ดทำงานอย่างไร',
			'dashboard_home_title'    => 'ใช้ FALCON ผ่านแดชบอร์ดสมาชิก: EA ทำงานบนเซิร์ฟเวอร์ ไม่ต้องติดตั้งโปรแกรมเอง',
			'dashboard_home_text'     => '',
			'dashboard_go_title'      => 'แดชบอร์ด FALCON PRO',
			'dashboard_go_text'       => 'ระบบสมาชิกที่ให้ MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ เข้าชมหน้าเลือก EA และหน้าติดตามบัญชีได้ก่อนสมัคร',
			/* หน้าแพ็กเกจ: การรันบนแดชบอร์ดรวมอยู่ในค่าแพ็กเกจแล้ว ไม่มีค่ารายเดือนหรือรายปี (เจ้าของยืนยัน 13 ต.ค. 2026) · จำนวนบัญชีทีมงานยืนยันในแชท ห้ามพิมพ์ตัวเลข */
			'dashboard_pricing_title' => 'Pro และ VIP ใช้งานผ่านแดชบอร์ด เข้าชมได้ก่อนตัดสินใจ',
			'dashboard_pricing_text'  => 'การรัน EA ผ่านแดชบอร์ดรวมอยู่ในค่าแพ็กเกจที่ชำระครั้งเดียวแล้ว ไม่มีค่ารายเดือนหรือรายปี สิทธิ์ใช้งานผูกกับเลขบัญชี MT5 ที่คุณแจ้งไว้ จำนวนบัญชีที่รันได้ขึ้นกับแพ็กเกจและที่ว่างบนเซิร์ฟเวอร์ เมื่อที่เต็มจะมีคิว ถามทีมงานก่อนชำระว่าแพ็กเกจที่คุณเลือกรันได้กี่บัญชี',

			/* ---------- หน้า /dashboard/ · หัวเพจ ---------- */
			'dash_kicker' => 'Dashboard',
			'dash_sub'    => 'FALCON PRO EA ใช้งานผ่านแดชบอร์ดนี้ทางเดียว MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ที่ทีมงานดูแล คุณเลือกชุด EA สั่งเริ่มหรือหยุด และติดตามบัญชีได้จากคอมหรือมือถือ เข้าชมก่อนได้โดยไม่ต้องสมัคร',

			/* ---------- หน้า /dashboard/ · ใครทำอะไร (คุณ / เซิร์ฟเวอร์ของ FALCON) ----------
			 * แทนส่วน "ใช้ FALCON ได้สองทาง" เดิม (คีย์ dash_ways_* และ dash_way1_* ถูกถอดออกพร้อมข้อความเทียบกับการติดตั้งเอง 13 ต.ค. 2026)
			 * ตาราง "เรื่อง | ส่วนของคุณ | ส่วนของเซิร์ฟเวอร์" แสดงเป็นการ์ดสองใบ · ทุกข้อมาจากข้อกำหนดการใช้บริการและหน้าช่วยเหลือของแดชบอร์ด
			 * แถวสุดท้ายต้องบอกว่าขาดทุนได้และเซิร์ฟเวอร์ไม่ลดความเสี่ยง (ห้ามลบ) · ห้ามใส่จำนวนบัญชี เวลารอ ที่ตั้งเซิร์ฟเวอร์ หรือตัวเลข uptime */
			'dash_roles_kicker'   => 'Who does what',
			'dash_roles_title'    => 'ใช้ FALCON ผ่านแดชบอร์ด: ใครทำอะไรบ้าง',
			'dash_roles_sub'      => 'ไม่มีไฟล์ EA ให้ดาวน์โหลด และไม่มีอะไรต้องติดตั้งบนคอมของคุณ EA ทำงานบนเซิร์ฟเวอร์ของ FALCON ตลอดช่วงที่คุณสั่งให้รัน ส่วนเงินทุน การตัดสินใจ และความเสี่ยงของการเทรดยังเป็นของคุณทั้งหมด',
			'dash_roles_rows'     => "เรื่อง | ส่วนที่คุณทำเอง | ส่วนที่เซิร์ฟเวอร์ของ FALCON ทำ\n"
				. "MT5 กับ EA | ไม่ต้องติดตั้งโปรแกรม ไม่ต้องมีไฟล์ EA ไม่ต้องเปิดคอมค้างหรือเช่า VPS | MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ที่ทีมงานดูแล และทำงานต่อแม้คุณปิดเบราว์เซอร์หรือปิดเครื่อง\n"
				. "บัญชีเทรดและเงินทุน | เปิดบัญชี MT5 เงินจริงในชื่อของคุณกับโบรกเกอร์ที่แดชบอร์ดรองรับ แล้วฝากเงินกับโบรกเกอร์โดยตรง บัญชี Cent ใช้ได้ บัญชีเดโมใช้ไม่ได้ | เข้าบัญชีนั้นเพื่อให้ EA ส่งคำสั่งซื้อขาย ระบบถอนหรือโอนเงินออกจากบัญชีไม่ได้\n"
				. "รหัสผ่านเทรดของ MT5 | พิมพ์เองในฟอร์มเพิ่มบัญชีของแดชบอร์ดเท่านั้น ไม่ส่งให้ใครทางแชท | รับรหัสที่ถูกเข้ารหัสในเบราว์เซอร์ของคุณก่อนส่ง และถอดได้เฉพาะในเครื่องที่รัน MT5\n"
				. "การสั่งงาน | เลือกชุด EA กดเริ่มรัน และกดหยุดได้ทุกเมื่อจากหน้าเว็บ | ทำตามคำสั่งนั้น โดยการหยุด EA ไม่ได้ปิดออเดอร์ที่ยังเปิดอยู่\n"
				. "การดูบัญชี | ดูจากหน้าเว็บแดชบอร์ด คู่กับแอป MT5 บนมือถือหรือคอม ซึ่งมีไว้ดูยอดเงิน ออเดอร์ และประวัติเท่านั้น | ส่งยอดเงิน ออเดอร์ และการแจ้งเตือนขึ้นหน้าเว็บเป็นรอบ\n"
				. 'ผลการเทรด | กำไรและขาดทุนเกิดในบัญชีของคุณ และขาดทุนได้จนหมดบัญชี | ไม่ทำให้ความเสี่ยงลดลง และไม่ตัดขาดทุนให้เอง',
			'dash_roles_link'     => 'เริ่มที่คู่มือเปิดบัญชี MT5',
			'dash_roles_link_url' => '/open-mt5-account/',
			'dash_roles_note'     => 'การรันบนแดชบอร์ดรวมอยู่ในค่าแพ็กเกจที่ชำระครั้งเดียวแล้ว ไม่มีค่ารายเดือนหรือรายปี สิทธิ์ใช้งานและสิ่งที่ได้ในแต่ละระดับดูที่หน้า [แพ็กเกจและราคา](/pricing/)',

			/* ---------- หน้า /dashboard/ · ภาพหน้าจอ ---------- */
			'dash_shots_kicker'      => 'Preview',
			'dash_shots_title'       => 'หน้าตาของแดชบอร์ดบนคอมและมือถือ',
			'dash_shots_sub'         => 'ภาพหน้าจอจากโหมดเยี่ยมชมของแดชบอร์ด ซึ่งเปิดดูได้เองโดยไม่ต้องมีบัญชี',
			'dash_shot1_img'         => fenix_dashboard_image_url( 'falcon-pro-dashboard-choose-ea-v1.webp' ),
			'dash_shot1_img_mobile'  => '',
			'dash_shot1_img_alt'     => 'หน้าเลือก EA ของแดชบอร์ด FALCON PRO บนจอคอม มีแถบแจ้งว่าเป็นโหมดเยี่ยมชม คำอธิบายระบบ EA FALCON และการ์ดชุด FALCON PRO, FALCON PLUS กับ FALCON ULTRA',
			'dash_shot1_img_caption' => 'หน้าเลือก EA บนจอคอม ถ่ายจากโหมดเยี่ยมชม · บัญชีที่เห็นในภาพเป็นบัญชีตัวอย่าง ไม่ใช่ผลการเทรด',
			'dash_shot1_img_note'    => 'ภาพที่ต้องใส่: หน้าเลือก EA ของแดชบอร์ดในโหมดเยี่ยมชม ที่ความกว้างจอคอม ครอปเฉพาะคอลัมน์เนื้อหา ไม่ให้เห็นชื่อโบรกเกอร์ในเมนูด้านข้าง · ภาพที่เห็นยอดเงินหรือกำไรขาดทุนต้องมีคำบรรยายว่าเป็นข้อมูลตัวอย่าง',
			'dash_shot2_img'         => fenix_dashboard_image_url( 'falcon-pro-dashboard-choose-ea-phone-v1.webp' ),
			'dash_shot2_img_mobile'  => '',
			'dash_shot2_img_alt'     => 'หน้าเลือก EA ของแดชบอร์ด FALCON PRO บนมือถือ มีแถบโหมดเยี่ยมชม ขั้นตอนเลือกชุด EA เลือกบัญชี MT5 และเริ่มรัน กับเมนูด้านล่างของแอป',
			'dash_shot2_img_caption' => 'หน้าเดียวกันบนมือถือ ถ่ายจากโหมดเยี่ยมชม · ข้อมูลบัญชีเป็นตัวอย่าง',
			'dash_shot2_img_note'    => 'ภาพที่ต้องใส่: หน้าเดียวกันบนมือถือ (แนวตั้ง) ในโหมดเยี่ยมชม · ภาพที่เห็นยอดเงินหรือกำไรขาดทุนต้องมีคำบรรยายว่าเป็นข้อมูลตัวอย่าง',

			/* ---------- หน้า /dashboard/ · สิ่งที่ทำได้ ---------- */
			'dash_feats_kicker' => 'Features',
			'dash_feats_title'  => 'สิ่งที่ทำได้ในแดชบอร์ด',
			'dash_feats_sub'    => 'ทุกข้อเป็นเครื่องมือสำหรับสั่งงานและติดตามบัญชี ไม่มีข้อใดทำให้ความเสี่ยงของการเทรดหายไป',
			'dash_feats'        => "server | EA ทำงานบนเซิร์ฟเวอร์ | MT5 กับ EA อยู่บนเซิร์ฟเวอร์ที่ทีมงานดูแล ทำงานต่อได้แม้คุณปิดเบราว์เซอร์หรือปิดเครื่อง และหยุดเมื่อคุณกดหยุด EA\n"
				. "gauge | เลือกและเปลี่ยนชุด EA | เลือกชุดตามระดับความเสี่ยงที่รับได้ ชุดที่เปิดให้ตั้ง Lot เลือกได้ว่าจะให้ระบบคิด Lot ตามทุนหรือกำหนดเอง ส่วน FALCON 5.0 ระบบคิด Lot ให้เอง แล้วเปลี่ยนชุดภายหลังได้จากการ์ดบัญชี\n"
				. "chart | ดูยอดเงิน ออเดอร์ และประวัติ | เห็น Balance กับ Equity ของแต่ละบัญชี รายการออเดอร์ที่ยังเปิด และประวัติการเทรดย้อนหลัง จากหน้าเว็บเดียว\n"
				. "shield | เพดานขาดทุน Guardian ที่คุณเปิดเอง | ค่าเริ่มต้นคือปิด เมื่อคุณเปิดไว้และขาดทุนถึงเพดานที่ตั้ง ระบบจะหยุด EA พยายามปิดออเดอร์ทั้งหมด แล้วล็อกบัญชีจนกว่าคุณจะปลดล็อก\n"
				. "chat | แจ้งเตือนในเว็บและทาง LINE | เหตุการณ์ของบัญชีขึ้นในหน้าการแจ้งเตือน และส่งเข้า LINE ได้เมื่อคุณเชื่อม LINE กับบัญชีสมาชิก\n"
				. "phone | ติดตั้งเป็นแอปบนมือถือ | เพิ่มแดชบอร์ดไว้บนหน้าจอโฮมของ Android หรือ iPhone แล้วเปิดใช้ได้เหมือนแอป\n"
				. 'clock | ปฏิทินข่าวเศรษฐกิจ | ดูข่าวที่มีผลต่อราคาทองคำ และบัญชีจริงมีโหมดหลบข่าวของ EA ที่เปิดหรือปิดได้',
			'dash_feats_note'   => 'ตัวเลขยอดเงินกับออเดอร์บนหน้าเว็บถูกดึงมาเป็นรอบ ไม่ได้ตามราคาทุกขณะ ให้ยึดข้อมูลในแอป MT5 และรายงานของโบรกเกอร์เป็นหลัก จึงควรติดตั้งแอป MT5 บนมือถือไว้ตรวจบัญชีด้วยตัวเองเสมอ',

			/* ---------- หน้า /dashboard/ · 5 ขั้นเริ่มใช้ ---------- */
			'dash_steps_kicker' => 'Get started',
			'dash_steps_title'  => 'เริ่มใช้แดชบอร์ดใน 5 ขั้น',
			'dash_steps_sub'    => 'ก่อนถึงขั้นแรก คุณต้องมีบัญชี MT5 เงินจริงที่ฝากเงินแล้ว และได้รหัสเชิญจากทีมงานหลังเลือกแพ็กเกจ ลำดับด้านล่างตรงกับหน้าช่วยเหลือในแดชบอร์ด อ่านให้ครบทุกขั้นก่อนกดเริ่มรัน และเริ่มด้วยเงินจำนวนน้อยที่ยอมเสียได้ทั้งหมด เช่น บัญชี Cent',
			'dash_steps'        => "สร้างบัญชีสมาชิก | สมัครด้วยรหัสเชิญจากทีมงาน กดยืนยันอีเมล แล้วเปิดการยืนยันตัวตนสองขั้น (2FA) เพื่อป้องกันบัญชี\n"
				. "เพิ่มบัญชี MT5 | ใส่เลขบัญชี รหัสผ่านเทรดของ MT5 (Master Password) และเลือกโบรกเกอร์ที่แดชบอร์ดรองรับ แดชบอร์ดรับเฉพาะบัญชีเงินจริง ยังไม่มีบัญชีให้เริ่มที่ [คู่มือเปิดบัญชี MT5](/open-mt5-account/)\n"
				. "เลือกชุด EA และ Lot | เลือกชุดตามความเสี่ยงที่รับได้ ถ้าชุดนั้นเปิดให้ตั้ง Lot ให้เลือกว่าจะใช้ Lot อัตโนมัติตามทุนหรือกำหนดเอง ส่วน FALCON 5.0 ระบบคิด Lot ให้เอง\n"
				. "กดเริ่มรัน | ยอมรับเอกสาร และอนุญาตให้เทรดด้วยเงินจริง (ถามครั้งเดียวต่อบัญชี) ระบบตรวจสิทธิ์ใช้งาน 1 ถึง 3 นาที เมื่อสถานะขึ้นว่ากำลังรัน EA จึงเริ่มทำงาน\n"
				. 'ดูแลความเสี่ยง | ตัดสินใจว่าจะเปิดเพดานขาดทุน Guardian หรือไม่ และตั้งเพดานไว้ที่เท่าไร ค่าเริ่มต้นคือปิด และการกดหยุด EA ไม่ได้ปิดออเดอร์ที่เปิดอยู่',
			'dash_steps_note'   => 'ผู้ใช้ต้องมีอายุ 20 ปีบริบูรณ์ขึ้นไป และเป็นเจ้าของบัญชี MT5 ที่นำมาเชื่อม ทีมงานไม่ขอรหัสผ่านสมาชิก รหัส 2FA หรือรหัสยืนยันของคุณไม่ว่าช่องทางใด',

			/* ---------- หน้า /dashboard/ · บล็อกติดต่อท้ายหน้า ---------- */
			'dash_cta_title' => 'อยากได้รหัสเชิญ หรือยังมีคำถามเรื่องแดชบอร์ด?',
			'dash_cta_text'  => 'ทักทีมงานทาง LINE เพื่อขอรหัสเชิญ ถามเรื่องสิทธิ์ใช้งานของบัญชี MT5 หรือให้ช่วยดูว่าแพ็กเกจไหนเข้ากับจำนวนบัญชีของคุณ ทีมงานไม่ขอรหัสผ่านของคุณในแชท',
		)
	);
}

/* ==============================================================
 * Customizer · หมวด 1.1 (ลิงก์ ปุ่ม บล็อกแนะนำ) ต่อจากช่องทางติดต่อ · หมวด 24.1 (หน้า /dashboard/) ต่อจากหน้า Risk Disclosure
 * ============================================================== */

add_filter( 'fenix_customizer_sections', 'fenix_dashboard_customizer_sections', 30, 2 );

/**
 * แทรก section ใหม่ต่อจาก section ที่ระบุ (ไม่พบ = ต่อท้าย)
 */
function fenix_dashboard_insert_after( $array, $after, $insert ) {
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

function fenix_dashboard_customizer_sections( $sections, $d ) {
	$rule  = 'ห้ามใส่ตัวเลขผล Backtest ตัวเลขตัวอย่างจากโหมดเยี่ยมชม เปอร์เซ็นต์กำไร หรือคำสัญญาว่าได้กำไร · ห้ามบอกว่าบริการเปิดเต็มรูปแบบถ้ายังไม่เปิด · {brand} {broker} ใช้ได้';
	$block = 'เว้นว่าง = ใช้หัวข้อหรือคำอธิบายกลางของบล็อก (ช่องด้านบน)';
	$kick  = 'แสดงเมื่อเปิด "แสดงคำเล็กเหนือหัวข้อ (ทั้งเว็บ)" ที่หมวด 1 เท่านั้น';

	$global = array(
		'fenix_dashboard' => array(
			'title'       => '1.1) แดชบอร์ด · ลิงก์ ปุ่ม และบล็อกแนะนำ',
			'description' => 'แดชบอร์ดคือระบบสมาชิกที่ให้ MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ และเป็นทางเดียวที่ลูกค้าใช้ FALCON (ไม่มีไฟล์ EA ให้ติดตั้งเอง) · อย่าเขียนเทียบกับการติดตั้งเอง และอย่าเรียกแดชบอร์ดว่าอีกทางหนึ่งหรือทางเลือก · หมวดนี้ตั้งที่อยู่ของแดชบอร์ด ข้อความปุ่ม หมายเหตุ และบล็อกแนะนำที่แสดงในหน้าต่าง ๆ · ธีมต่อ #visit (โหมดเยี่ยมชม) #signup (สมัครสมาชิก) #signin (เข้าสู่ระบบ) ท้ายที่อยู่ให้เอง จึงใส่เฉพาะที่อยู่หลัก · ปิด "แสดงแดชบอร์ดบนเว็บ" หรือเว้นที่อยู่ให้ว่าง = ปุ่มและบล็อกแนะนำทุกจุดหายไป (หน้า /dashboard/ ยังอ่านได้ แต่ไม่มีปุ่ม) · ข้อความหน้า /dashboard/ แก้ที่หมวด 24.1',
			'fields'      => array(
				'show_dashboard'          => array( 'แสดงแดชบอร์ดบนเว็บ (ปุ่มและบล็อกแนะนำทุกจุด)', 'checkbox' ),
				'dashboard_url'           => array( 'ที่อยู่ของแดชบอร์ด (ที่อยู่หลัก ไม่ต้องใส่ #visit)', 'url', 'ต้องขึ้นต้นด้วย https:// · ธีมต่อ #visit #signup #signin ให้ตามปุ่ม' ),
				'dashboard_visit_label'   => array( 'ข้อความปุ่ม · เข้าชมโดยไม่ต้องสมัคร (#visit)', 'text', 'เว้นว่าง = ซ่อนปุ่มนี้ทุกจุด' ),
				'dashboard_signup_label'  => array( 'ข้อความปุ่ม · สมัครสมาชิก (#signup)', 'text', 'เว้นว่าง = ซ่อนปุ่มนี้ทุกจุด' ),
				'dashboard_signin_label'  => array( 'ข้อความปุ่ม · เข้าสู่ระบบ (#signin)', 'text', 'เว้นว่าง = ซ่อนปุ่มนี้ทุกจุด' ),
				'dashboard_newtab_hint'   => array( 'ข้อความสำหรับโปรแกรมอ่านหน้าจอ: ปุ่มเปิดแท็บใหม่', 'text' ),
				'dashboard_invite_note'   => array( 'หมายเหตุ · การสมัครต้องใช้รหัสเชิญ (แสดงคู่กับปุ่มสมัคร)', 'text', 'เว้นว่าง = ไม่แสดง · ถ้าเลิกใช้รหัสเชิญแล้วค่อยลบ' ),
				'dashboard_sample_note'   => array( 'หมายเหตุ · ข้อมูลในโหมดเยี่ยมชมเป็นตัวอย่าง (ห้ามลบขณะยังมีปุ่มเข้าชม)', 'text', 'ต้องบอกชัดว่าตัวเลขในโหมดเยี่ยมชมไม่ใช่ผลการเทรด' ),
				'dashboard_status_note'   => array( 'หมายเหตุ · สถานะการเปิดให้บริการ', 'textarea', 'แสดงในบล็อกแนะนำและหน้า /dashboard/ · เมื่อแดชบอร์ดเปิดให้เชื่อมบัญชีและเริ่มรันได้จริงแล้ว ให้แก้ข้อความหรือเว้นว่างเพื่อซ่อน' ),
				'dashboard_block_kicker'  => array( 'บล็อกแนะนำ · ป้ายเล็กเหนือหัวข้อ (ภาษาอังกฤษ)', 'text', $kick ),
				'dashboard_block_title'   => array( 'บล็อกแนะนำ · หัวข้อกลาง', 'text', $rule ),
				'dashboard_block_text'    => array( 'บล็อกแนะนำ · คำอธิบายกลาง', 'textarea', $rule ),
				'dashboard_block_points'  => array( 'บล็อกแนะนำ · จุดเด่น (บรรทัดละ 1 ข้อ · แนะนำ 3 ถึง 4 ข้อ)', 'textarea', $rule ),
				'dashboard_block_caution' => array( 'บล็อกแนะนำ · ข้อควรรู้เรื่องความเสี่ยง (ห้ามลบ)', 'textarea', 'ต้องบอกว่าการรันบนเซิร์ฟเวอร์ไม่ลดความเสี่ยง และ EA ไม่ตัดขาดทุนเอง' ),
				'dashboard_block_more'    => array( 'บล็อกแนะนำ · ข้อความลิงก์ไปหน้า /dashboard/', 'text', 'แสดงเมื่อหน้า /dashboard/ เผยแพร่แล้ว · เว้นว่าง = ไม่แสดงลิงก์' ),
				'dashboard_home_title'    => array( 'หน้าแรก · หัวข้อของบล็อก', 'text', $block ),
				'dashboard_home_text'     => array( 'หน้าแรก · คำอธิบายของบล็อก', 'textarea', $block ),
				'dashboard_go_title'      => array( 'หน้า /go · หัวข้อของบล็อก', 'text', $block ),
				'dashboard_go_text'       => array( 'หน้า /go · คำอธิบายของบล็อก', 'textarea', $block ),
				'dashboard_pricing_title' => array( 'หน้าแพ็กเกจ · หัวข้อของบล็อก', 'text', $block ),
				'dashboard_pricing_text'  => array( 'หน้าแพ็กเกจ · คำอธิบายของบล็อก', 'textarea', $block . ' · การรันผ่านแดชบอร์ดรวมอยู่ในค่าแพ็กเกจ Pro และ VIP แล้ว ไม่มีค่ารายเดือนหรือรายปี · จำนวนบัญชีที่รันได้ให้ทีมงานยืนยันในแชท อย่าพิมพ์ตัวเลข' ),
			),
		),
	);

	$page_fields = array(
		'dash_kicker'        => array( 'ป้ายเหนือชื่อหน้า', 'text', $kick . ' · ใช้เป็นชื่อหน้าสำรองเมื่อเพจไม่มีชื่อ' ),
		'dash_sub'           => array( 'คำโปรยใต้ชื่อหน้า', 'textarea', $rule ),
		'dash_roles_kicker'   => array( 'ใครทำอะไร · ป้ายเล็ก', 'text', $kick ),
		'dash_roles_title'    => array( 'ใครทำอะไร · หัวข้อ (H2)', 'text' ),
		'dash_roles_sub'      => array( 'ใครทำอะไร · คำอธิบาย', 'textarea' ),
		'dash_roles_rows'     => array( 'ใครทำอะไร · ตาราง (บรรทัดแรก = ชื่อของการ์ดสองใบ · คั่นช่องด้วย | )', 'textarea', 'รูปแบบ: เรื่อง | ส่วนที่คุณทำเอง | ส่วนที่เซิร์ฟเวอร์ทำ · แสดงเป็นการ์ดสองใบ ใบขวาคือเซิร์ฟเวอร์ของ FALCON · เว้นว่างทั้งช่อง = ซ่อนส่วนนี้ · ต้องมีแถวที่บอกว่าขาดทุนได้และเซิร์ฟเวอร์ไม่ลดความเสี่ยง · อย่าเขียนเทียบกับการติดตั้งเอง · ' . $rule ),
		'dash_roles_link'     => array( 'ใครทำอะไร · ข้อความลิงก์ท้ายการ์ดใบซ้าย (เว้นว่าง = ไม่แสดง)', 'text' ),
		'dash_roles_link_url' => array( 'ใครทำอะไร · ปลายทางของลิงก์นั้น (เช่น /open-mt5-account/)', 'path', 'ลิงก์ไปเพจในเว็บจะแสดงเมื่อเพจนั้นเผยแพร่แล้วเท่านั้น · เพจที่ถอดออกจากเว็บแล้วไม่ถูกลิงก์' ),
		'dash_roles_note'     => array( 'ใครทำอะไร · หมายเหตุใต้การ์ด', 'textarea', '[ข้อความ](/slug/) = ลิงก์ภายในเว็บ' ),
		'dash_shots_kicker'  => array( 'ภาพหน้าจอ · ป้ายเล็ก', 'text', $kick ),
		'dash_shots_title'   => array( 'ภาพหน้าจอ · หัวข้อ (H2)', 'text' ),
		'dash_shots_sub'     => array( 'ภาพหน้าจอ · คำอธิบาย', 'textarea' ),
	);
	$page_fields += fenix_media_fields( 'dash_shot1', 'ภาพหน้าจอ 1 (จอคอม)' );
	$page_fields += fenix_media_fields( 'dash_shot2', 'ภาพหน้าจอ 2 (มือถือ แนวตั้ง)' );
	$page_fields += array(
		'dash_feats_kicker' => array( 'สิ่งที่ทำได้ · ป้ายเล็ก', 'text', $kick ),
		'dash_feats_title'  => array( 'สิ่งที่ทำได้ · หัวข้อ (H2)', 'text' ),
		'dash_feats_sub'    => array( 'สิ่งที่ทำได้ · คำอธิบาย', 'textarea' ),
		'dash_feats'        => array( 'สิ่งที่ทำได้ · รายการ (บรรทัดละ 1 ข้อ: ไอคอน | หัวข้อ | รายละเอียด)', 'textarea', 'ไอคอนที่ใช้ได้ เช่น server gauge chart shield chat phone clock bolt lock user monitor · ข้อแรกแสดงเป็นการ์ดใหญ่ · ' . $rule ),
		'dash_feats_note'   => array( 'สิ่งที่ทำได้ · หมายเหตุใต้รายการ', 'textarea' ),
		'dash_steps_kicker' => array( 'ขั้นตอนเริ่มใช้ · ป้ายเล็ก', 'text', $kick ),
		'dash_steps_title'  => array( 'ขั้นตอนเริ่มใช้ · หัวข้อ (H2)', 'text' ),
		'dash_steps_sub'    => array( 'ขั้นตอนเริ่มใช้ · คำอธิบาย', 'textarea' ),
		'dash_steps'        => array( 'ขั้นตอนเริ่มใช้ (บรรทัดละ 1 ขั้น: หัวข้อ | รายละเอียด)', 'textarea', '[ข้อความ](/slug/) = ลิงก์ภายในเว็บ · ระยะเวลาให้ใส่เฉพาะที่แดชบอร์ดระบุเอง · ' . $rule ),
		'dash_steps_note'   => array( 'ขั้นตอนเริ่มใช้ · หมายเหตุใต้ปุ่ม', 'textarea' ),
		'dash_cta_title'    => array( 'บล็อกติดต่อท้ายหน้า · หัวข้อ', 'text' ),
		'dash_cta_text'     => array( 'บล็อกติดต่อท้ายหน้า · คำอธิบาย', 'textarea' ),
	);

	$page = array(
		'fenix_dashboard_page' => array(
			'title'       => '24.1) หน้าแดชบอร์ด (/dashboard/)',
			'description' => 'ส่วนออกแบบด้านบนของเพจ /dashboard/ (เทมเพลต "FALCON · หน้าแดชบอร์ด") · คำอธิบายยาว ตารางชุด EA และถาม-ตอบ แก้ในหน้าแก้ไขเพจ · ปุ่มและหมายเหตุใช้ค่าจากหมวด 1.1 · ช่องรูป: ไม่มีรูป = คนทั่วไปไม่เห็นส่วนนั้น (แอดมินเห็นกรอบบอกว่าต้องใส่ภาพอะไร) · ภาพหน้าจอต้องไม่เห็นชื่อโบรกเกอร์ และภาพที่เห็นยอดเงินหรือกำไรขาดทุนตัวอย่างต้องมีคำบรรยายว่าเป็นข้อมูลตัวอย่าง ไม่ใช่ผลการเทรด',
			'fields'      => $page_fields,
		),
	);

	$sections = fenix_dashboard_insert_after( $sections, 'fenix_general', $global );
	return fenix_dashboard_insert_after( $sections, 'fenix_riskpage', $page );
}

/* ==============================================================
 * CSS เฉพาะหน้า /dashboard/ (assets/css/dashboard-page.css) · โหลดเฉพาะเพจที่ใช้เทมเพลตนี้
 * ส่วนปุ่มและบล็อกแนะนำ (assets/css/dashboard.css) อยู่ในรายการ fenix_asset_modules() ของ functions.php และโหลดทุกหน้า
 * ============================================================== */

add_filter( 'fenix_asset_modules', 'fenix_dashboard_asset_modules' );

function fenix_dashboard_asset_modules( $modules ) {
	$modules = (array) $modules;
	if ( in_array( 'dashboard-page', $modules, true ) ) {
		return $modules;
	}
	$at = array_search( 'dashboard', $modules, true );
	if ( false === $at ) {
		$modules[] = 'dashboard-page';
		return $modules;
	}
	array_splice( $modules, (int) $at + 1, 0, array( 'dashboard-page' ) );
	return $modules;
}

add_filter( 'fenix_asset_module_needed', 'fenix_dashboard_asset_needed', 10, 2 );

function fenix_dashboard_asset_needed( $needed, $module ) {
	if ( 'dashboard-page' === $module ) {
		return is_page_template( 'template-dashboard.php' );
	}
	return $needed;
}

/* ==============================================================
 * ลิงก์และปุ่ม
 * ============================================================== */

/**
 * โหมดของลิงก์เข้าแดชบอร์ด (ต่อท้ายที่อยู่หลักเป็น #โหมด)
 */
function fenix_dashboard_modes() {
	return array( 'visit', 'signup', 'signin' );
}

/**
 * ข้อความจาก setting พร้อมแทนตัวแทน {brand} {broker} (ข้อความล้วน ยังไม่ escape)
 */
function fenix_dashboard_text( $key ) {
	$text = trim( (string) fenix_mod( $key ) );
	return function_exists( 'fenix_tokens' ) ? trim( (string) fenix_tokens( $text ) ) : $text;
}

/**
 * ที่อยู่ของแดชบอร์ดตามโหมด
 * - '' = ที่อยู่หลัก · visit | signup | signin = ที่อยู่หลัก + '#โหมด' · โหมดที่ไม่รู้จัก = ที่อยู่หลัก
 * - คืน '' เมื่อปิดฟีเจอร์ (show_dashboard) หรือช่องที่อยู่ว่าง / ไม่ใช่ http(s)
 * - # ที่ติดมากับช่องที่อยู่ (เช่นวางลิงก์ที่ลงท้าย #visit) ถูกตัดออกก่อน · ที่อยู่ที่ไม่มี path ได้ / ต่อท้าย
 */
function fenix_dashboard_url( $mode = '' ) {
	if ( ! fenix_mod( 'show_dashboard' ) ) {
		return '';
	}
	$base = trim( (string) fenix_mod( 'dashboard_url' ) );
	$base = (string) preg_replace( '/#.*$/s', '', $base );
	if ( ! preg_match( '#^https?://[^/\s?]+#i', $base, $m ) ) {
		return '';
	}
	if ( strlen( $base ) === strlen( $m[0] ) ) {
		$base .= '/';
	}
	$mode = strtolower( trim( (string) $mode ) );
	return in_array( $mode, fenix_dashboard_modes(), true ) ? $base . '#' . $mode : $base;
}

/**
 * เปิดใช้แดชบอร์ดบนเว็บอยู่หรือไม่ (เปิดฟีเจอร์ + มีที่อยู่ที่ใช้ได้)
 */
function fenix_dashboard_enabled() {
	return '' !== fenix_dashboard_url();
}

/**
 * ข้อความปุ่มของแต่ละโหมด (ข้อความล้วน) · '' = เจ้าของเว็บซ่อนปุ่มนั้น
 */
function fenix_dashboard_label( $mode ) {
	$mode = in_array( $mode, fenix_dashboard_modes(), true ) ? $mode : 'signin';
	return fenix_dashboard_text( 'dashboard_' . $mode . '_label' );
}

/**
 * ปุ่มไปแดชบอร์ด · คืน HTML ของ <a> หรือ '' เมื่อไม่มีปลายทาง / ไม่มีข้อความปุ่ม
 * ข้อความปุ่มของโหมดใน Customizer (หมวด 1.1) ว่าง = ปุ่มโหมดนั้นถูกปิดทุกจุด ไม่ว่าผู้เรียกจะส่ง $text มาหรือไม่
 *
 * @param string $mode 'visit' | 'signup' | 'signin' | '' (ที่อยู่หลัก)
 * @param array  $args {
 *     @type string      $text   ข้อความปุ่ม (ว่าง = ข้อความของโหมดนั้นจาก Customizer)
 *     @type string      $class  คลาสของ <a> (ค่าเริ่มต้น: visit = 'btn btn-fire' · โหมดอื่น = 'btn btn-ghost')
 *     @type string      $pos    ตำแหน่งสำหรับนับคลิก (data-dash-pos)
 *     @type bool|string $icon   true = ไอคอนของโหมด · ชื่อไอคอนจาก fenix_icon() · false = ไม่มี
 *     @type bool        $arrow  แสดงไอคอนลิงก์ออกนอกเว็บท้ายปุ่ม
 * }
 */
function fenix_dashboard_button( $mode = 'visit', $args = array() ) {
	$mode = strtolower( trim( (string) $mode ) );
	$mode = in_array( $mode, fenix_dashboard_modes(), true ) ? $mode : '';
	$url  = fenix_dashboard_url( $mode );
	if ( '' === $url ) {
		return '';
	}
	$args = wp_parse_args(
		$args,
		array(
			'text'  => '',
			'class' => 'visit' === $mode ? 'btn btn-fire' : 'btn btn-ghost',
			'pos'   => 'content',
			'icon'  => true,
			'arrow' => true,
		)
	);
	/* ข้อความปุ่มของโหมดใน Customizer ถูกลบจนว่าง = เจ้าของเว็บปิดปุ่มโหมดนั้นทุกจุด แม้ผู้เรียกหรือ shortcode จะส่งข้อความของตัวเองมา */
	$label = fenix_dashboard_label( $mode );
	if ( '' !== $mode && '' === $label ) {
		return '';
	}
	$text = trim( (string) $args['text'] );
	if ( '' === $text ) {
		$text = $label;
	}
	if ( '' === $text ) {
		return '';
	}
	$icons = array(
		'visit'  => 'monitor',
		'signup' => 'user',
		'signin' => 'lock',
	);
	$icon  = '';
	if ( true === $args['icon'] ) {
		$icon = fenix_icon( isset( $icons[ $mode ] ) ? $icons[ $mode ] : 'monitor' );
	} elseif ( is_string( $args['icon'] ) && '' !== $args['icon'] ) {
		$icon = fenix_icon( $args['icon'] );
	}
	$hint = fenix_dashboard_text( 'dashboard_newtab_hint' );
	return sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="noopener" data-dash="%3$s" data-dash-pos="%4$s">%5$s<span>%6$s</span>%7$s%8$s</a>',
		esc_attr( trim( (string) $args['class'] ) ),
		esc_url( $url ),
		esc_attr( '' !== $mode ? $mode : 'open' ),
		esc_attr( sanitize_key( (string) $args['pos'] ) ),
		$icon, // inline SVG ของธีม
		fenix_dashboard_esc( $text ),
		$args['arrow'] ? fenix_icon( 'external', 'icon icon-sm' ) : '',
		'' !== $hint ? '<span class="sr-only"> (' . esc_html( $hint ) . ')</span>' : ''
	);
}

/**
 * หมายเหตุประกอบปุ่ม เป็น <ul class="dash-notes"> (escape แล้ว) · '' เมื่อไม่มีข้อใดให้แสดง
 * - invite = การสมัครต้องใช้รหัสเชิญ (แสดงเมื่อมีปุ่มสมัคร) · sample = ข้อมูลโหมดเยี่ยมชมเป็นตัวอย่าง (แสดงเมื่อมีปุ่มเข้าชม)
 * - status = สถานะการเปิดให้บริการ
 *
 * @param string[] $which ลำดับหมายเหตุที่ต้องการ
 * @param string   $class คลาสเพิ่มของ <ul>
 */
function fenix_dashboard_notes( $which = array( 'sample', 'invite' ), $class = '' ) {
	if ( ! fenix_dashboard_enabled() ) {
		return '';
	}
	$map  = array(
		'sample' => array( 'dashboard_sample_note', 'flask', 'visit' ),
		'invite' => array( 'dashboard_invite_note', 'lock', 'signup' ),
		'status' => array( 'dashboard_status_note', 'clock', '' ),
	);
	$html = '';
	foreach ( (array) $which as $name ) {
		if ( ! isset( $map[ $name ] ) ) {
			continue;
		}
		/* หมายเหตุของปุ่มที่ถูกซ่อน (ข้อความปุ่มว่าง) ไม่ต้องแสดง */
		if ( '' !== $map[ $name ][2] && '' === fenix_dashboard_label( $map[ $name ][2] ) ) {
			continue;
		}
		$text = fenix_dashboard_text( $map[ $name ][0] );
		if ( '' === $text ) {
			continue;
		}
		$html .= '<li class="dash-note dash-note--' . esc_attr( $name ) . '">' . fenix_icon( $map[ $name ][1], 'icon icon-sm' ) . '<span>' . fenix_dashboard_esc( $text ) . '</span></li>';
	}
	if ( '' === $html ) {
		return '';
	}
	return '<ul class="' . esc_attr( trim( 'dash-notes ' . $class ) ) . '">' . $html . '</ul>';
}

/**
 * URL ของหน้า /dashboard/ บนเว็บนี้ เมื่อเผยแพร่แล้วและไม่ใช่หน้าที่กำลังแสดง ('' = ไม่ต้องลิงก์)
 */
function fenix_dashboard_page_url() {
	if ( is_page( 'dashboard' ) || ! function_exists( 'fenix_published_page_url' ) ) {
		return '';
	}
	return '' !== (string) fenix_published_page_url( 'dashboard' ) ? home_url( '/dashboard/' ) : '';
}

/**
 * คลาสโทนพื้นของ section ในโมดูลนี้ · โหมดเข้ม: .is-dark (+ .is-dark-2 เมื่อ $alt) · โหมดขาว/ดำสลับ: .section-alt เมื่อ $alt
 */
function fenix_dashboard_tone( $alt = false ) {
	$tone = fenix_section_tone( 'dashboard', $alt );
	if ( '' === $tone && $alt ) {
		return ' section-alt';
	}
	return $tone;
}

/**
 * บรรทัด "ช่อง | ช่อง | ช่อง" → array ของแถว (ข้ามบรรทัดที่มีช่องมีเนื้อหาน้อยกว่า $min)
 */
function fenix_dashboard_rows( $text, $min = 1 ) {
	$rows = array();
	foreach ( fenix_lines( $text ) as $line ) {
		$cells = array_map( 'trim', explode( '|', $line ) );
		if ( count( array_filter( $cells, 'strlen' ) ) >= $min ) {
			$rows[] = $cells;
		}
	}
	return $rows;
}

/**
 * คำทับศัพท์ที่เบราว์เซอร์ตัดบรรทัดกลางคำในการ์ดและปุ่มแคบ (เช่น "แด" ขึ้นบรรทัดใหม่เป็น "ชบอร์ด")
 * แก้รายการได้ด้วยฟิลเตอร์ fenix_dashboard_nobr_words
 */
function fenix_dashboard_nobr_words() {
	return (array) apply_filters( 'fenix_dashboard_nobr_words', array( 'ออเดอร์', 'แดชบอร์ด', 'โบรกเกอร์', 'เซิร์ฟเวอร์', 'เบราว์เซอร์', 'ติดตั้ง', 'ทางเลือก' ) );
}

/**
 * esc_html + กันตัดบรรทัดกลางคำ (fenix_text) แล้วห่อคำในรายการข้างบนด้วย span.nobr · ใช้กับข้อความสั้นทุกจุดของโมดูลนี้
 * ใช้ span.nobr (white-space: nowrap ใน assets/css/components.css) ไม่ใช้อักขระมองไม่เห็นคั่นกลางคำ:
 * ตัวหนังสือในหน้ายังเป็นคำเดิมครบทุกตัว การค้นหาในหน้า โปรแกรมอ่านหน้าจอ และ search engine จึงอ่านคำว่า "แดชบอร์ด" ได้ตามปกติ
 * แตะเฉพาะช่วงข้อความ ไม่แตะแท็ก (ผลของ fenix_text มีแท็กได้เฉพาะ span.nobr ของคำละตินที่มียัติภังค์)
 */
function fenix_dashboard_esc( $text ) {
	$html  = fenix_text( $text );
	$words = array_values( array_filter( array_map( 'strval', fenix_dashboard_nobr_words() ), 'strlen' ) );
	if ( '' === $html || empty( $words ) ) {
		return $html;
	}
	$wrap = array();
	foreach ( $words as $word ) {
		$wrap[ esc_html( $word ) ] = '<span class="nobr">' . esc_html( $word ) . '</span>';
	}
	$parts = preg_split( '/(<[^>]*>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( false === $parts ) {
		return $html;
	}
	$inside = 0; // อยู่ใน span.nobr ที่ fenix_text ใส่ไว้แล้ว: ไม่ห่อซ้อน
	foreach ( $parts as $idx => $part ) {
		if ( '' === $part ) {
			continue;
		}
		if ( '<' === $part[0] ) {
			$inside += 0 === strpos( $part, '</' ) ? -1 : 1;
			continue;
		}
		if ( $inside <= 0 ) {
			$parts[ $idx ] = strtr( $part, $wrap ); // แทนพร้อมกันทุกคำในรอบเดียว (ไม่แทนซ้ำในผลที่เพิ่งแทน)
		}
	}
	return implode( '', $parts );
}

/**
 * ข้อความที่อ่านแล้วเหมือนผลเทรด (ตัวเลขตามด้วย %) · ใช้กรองจุดเด่นและรายการที่เจ้าของพิมพ์เอง
 */
function fenix_dashboard_is_result_text( $text ) {
	return (bool) preg_match( '/[+-]?\d[\d.,]*\s*%/u', (string) $text );
}

/* ==============================================================
 * บล็อกแนะนำแดชบอร์ด (ใช้ซ้ำได้ทุกหน้า)
 * ============================================================== */

/**
 * หัวข้อ / คำอธิบายของบล็อกตามหน้า · ช่องของหน้านั้นว่าง = ใช้ข้อความกลาง
 *
 * @param string $context home | go | pricing | page
 * @param string $part    title | text
 */
function fenix_dashboard_block_text( $context, $part ) {
	$own = '';
	if ( in_array( $context, array( 'home', 'go', 'pricing' ), true ) ) {
		$own = fenix_dashboard_text( 'dashboard_' . $context . '_' . $part );
	}
	return '' !== $own ? $own : fenix_dashboard_text( 'dashboard_block_' . $part );
}

/**
 * พิมพ์บล็อกแนะนำแดชบอร์ด: ป้าย + หัวข้อ + คำอธิบาย + จุดเด่น + ข้อควรรู้เรื่องความเสี่ยง | ปุ่มเข้าชม + ปุ่มสมัคร + หมายเหตุ + ลิงก์หน้า /dashboard/
 * ปิดฟีเจอร์หรือไม่มีที่อยู่ = ไม่พิมพ์อะไร (แอดมินเห็นคำแนะนำ) · คืน true เมื่อพิมพ์บล็อก
 *
 * @param string $context home | go | pricing | page (หน้าอื่น ๆ · ค่าที่ไม่รู้จัก เช่น 'install' ของหน้าวิธีติดตั้งที่ถอดออกแล้ว = page)
 * @param array  $args {
 *     @type bool   $wrap    true = พิมพ์ <section class="section"> + .container ครอบ (ค่าเริ่มต้น · หน้า go = false เป็นการ์ดย่อ)
 *     @type bool   $alt     โทนพื้นสลับ (เข้มกว่า / เทาอ่อน) เมื่อ $wrap
 *     @type string $heading แท็กของหัวข้อ h2 | h3
 *     @type bool   $points  แสดงรายการจุดเด่น (ค่าเริ่มต้น: ทุกหน้า ยกเว้น go)
 *     @type bool   $media   แสดงภาพหน้าจอแดชบอร์ด (ช่องรูป dash_shot1) ข้างข้อความ (ค่าเริ่มต้น: เฉพาะ home)
 *     @type string $pos     ตำแหน่งสำหรับนับคลิก (ค่าเริ่มต้น "{context}-dashboard")
 *     @type string $id      id ของบล็อก
 * }
 */
function fenix_dashboard_block( $context = 'page', $args = array() ) {
	$context = in_array( $context, array( 'home', 'go', 'pricing', 'page' ), true ) ? $context : 'page';
	if ( ! fenix_dashboard_enabled() ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<p class="admin-hint dash-admin-hint">บล็อกแดชบอร์ดถูกซ่อน: เปิดและใส่ที่อยู่ที่ ปรับแต่ง → 1.1) แดชบอร์ด</p>';
		}
		return false;
	}
	$args = wp_parse_args(
		$args,
		array(
			'wrap'    => 'go' !== $context,
			'alt'     => false,
			'heading' => 'h2',
			'points'  => 'go' !== $context,
			'media'   => 'home' === $context,
			'pos'     => $context . '-dashboard',
			'id'      => 'dashboard-promo',
		)
	);
	$visit  = fenix_dashboard_button(
		'visit',
		array(
			'class' => 'btn btn-fire dash-btn',
			'pos'   => $args['pos'],
		)
	);
	$signup = fenix_dashboard_button(
		'signup',
		array(
			'class' => 'btn btn-ghost dash-btn',
			'pos'   => $args['pos'],
		)
	);
	$more_url   = fenix_dashboard_page_url();
	$more_label = fenix_dashboard_text( 'dashboard_block_more' );
	if ( '' === $visit && '' === $signup && ( '' === $more_url || '' === $more_label ) ) {
		return false;
	}

	$title   = fenix_dashboard_block_text( $context, 'title' );
	$text    = fenix_dashboard_block_text( $context, 'text' );
	/* ป้ายเล็กเหนือหัวข้อของการ์ด: พิมพ์เฉพาะเมื่อเปิด "คำเล็กเหนือหัวข้อ" (show_kickers · หมวด 1 · fenix_kicker ใน inc/components.php) · ค่าเริ่มต้นปิด */
	$kicker  = function_exists( 'fenix_kicker' ) ? fenix_kicker( fenix_dashboard_text( 'dashboard_block_kicker' ) ) : '';
	$caution = fenix_dashboard_text( 'dashboard_block_caution' );
	$points  = array();
	if ( $args['points'] ) {
		foreach ( fenix_lines( fenix_dashboard_text( 'dashboard_block_points' ) ) as $line ) {
			if ( ! fenix_dashboard_is_result_text( $line ) ) {
				$points[] = $line;
			}
		}
	}
	/* id ไม่ซ้ำแม้หน้าเดียวกันพิมพ์บล็อกมากกว่าหนึ่งครั้ง (ครั้งถัดไปได้ -2, -3 …) */
	static $used = array();
	$id          = sanitize_key( (string) $args['id'] );
	$id          = '' !== $id ? $id : 'dashboard-promo';
	$used[ $id ] = isset( $used[ $id ] ) ? $used[ $id ] + 1 : 1;
	if ( $used[ $id ] > 1 ) {
		$id .= '-' . $used[ $id ];
	}
	$args['id'] = $id;

	$tag   = 'h3' === $args['heading'] ? 'h3' : 'h2';
	$notes = fenix_dashboard_notes( array( 'sample', 'invite', 'status' ) );
	$media = $args['media'] && '' !== trim( (string) fenix_mod( 'dash_shot1_img' ) );
	$dark  = fenix_is_dark_mode();
	$class = 'dash-promo dash-promo--' . $context . ( $media ? ' dash-promo--media' : '' );

	if ( $args['wrap'] ) {
		echo '<section class="section ' . esc_attr( $class . fenix_dashboard_tone( (bool) $args['alt'] ) ) . '" id="' . esc_attr( $args['id'] ) . '"><div class="container">';
	} else {
		echo '<div class="' . esc_attr( $class . ( $dark ? ' is-dark' : '' ) ) . '" id="' . esc_attr( $args['id'] ) . '">';
	}
	?>
	<div class="dash-card<?php echo $args['wrap'] ? ' reveal' : ''; ?>">
		<div class="dash-card-main">
			<?php if ( '' !== $kicker ) : ?>
				<span class="kicker"><?php echo esc_html( $kicker ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h2 | h3 ?> class="dash-card-title"><?php echo fenix_dashboard_esc( $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h2 | h3 ?>>
			<?php endif; ?>
			<?php if ( '' !== $text ) : ?>
				<p class="dash-card-text"><?php echo fenix_dashboard_esc( $text ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
			<?php endif; ?>
			<?php if ( $points ) : ?>
				<ul class="dash-points">
					<?php foreach ( $points as $fenix_point ) : ?>
						<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_dashboard_esc( $fenix_point ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( '' !== $caution ) : ?>
				<p class="dash-caution"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_dashboard_esc( $caution ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
			<?php endif; ?>
		</div>
		<div class="dash-card-side">
			<?php
			if ( $media ) {
				fenix_media_slot(
					'dash_shot1',
					array(
						'width'  => 1600,
						'height' => 1037,
						'class'  => 'dash-card-shot',
						'group'  => 'dash-promo',
					)
				);
			}
			?>
			<?php if ( '' !== $visit || '' !== $signup ) : ?>
				<div class="dash-actions">
					<?php echo $visit . $signup; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_button ?>
				</div>
			<?php endif; ?>
			<?php echo $notes; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_notes ?>
			<?php if ( '' !== $more_url && '' !== $more_label ) : ?>
				<a class="dash-more" href="<?php echo esc_url( $more_url ); ?>"><span><?php echo esc_html( $more_label ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
	echo $args['wrap'] ? '</div></section>' : '</div>';
	return true;
}

/* ==============================================================
 * หน้า /dashboard/ (template-dashboard.php)
 * ============================================================== */

/**
 * หัว section ชิดซ้าย (H2 + คำอธิบาย) · ใช้คลาส .pg-head ของ assets/css/pages.css
 * ป้ายเล็กเหนือ H2 ($kicker) พิมพ์เฉพาะเมื่อเปิด "คำเล็กเหนือหัวข้อ" (show_kickers · หมวด 1) · ค่าเริ่มต้นปิด = H2 เป็นลูกตัวแรกของ .pg-head
 */
function fenix_dashboard_head( $kicker, $title, $sub = '' ) {
	$title = trim( (string) $title );
	if ( '' === $title ) {
		return;
	}
	$kicker = function_exists( 'fenix_kicker' ) ? fenix_kicker( $kicker ) : '';
	?>
	<div class="pg-head dash-head reveal">
		<?php if ( '' !== $kicker ) : ?>
			<span class="kicker"><?php echo esc_html( $kicker ); ?></span>
		<?php endif; ?>
		<h2><?php echo fenix_dashboard_esc( $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
		<?php if ( '' !== trim( (string) $sub ) ) : ?>
			<p><?php echo fenix_dashboard_esc( $sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * แถวปุ่มของหน้า (เข้าชม + สมัคร [+ เข้าสู่ระบบ]) · คืน HTML ที่ escape แล้ว หรือ '' เมื่อไม่มีปุ่ม
 * ปิดฟีเจอร์ = '' (แอดมินได้คำแนะนำ) · $ghost = คลาสของปุ่มรอง (หัวเพจพื้นเข้มใช้ btn-ghost-dark)
 */
function fenix_dashboard_page_actions( $pos, $signin = false, $ghost = 'btn btn-ghost' ) {
	if ( ! fenix_dashboard_enabled() ) {
		return current_user_can( 'edit_theme_options' ) ? '<span class="admin-hint">ปุ่มแดชบอร์ดถูกซ่อน: เปิดและใส่ที่อยู่ที่ ปรับแต่ง → 1.1) แดชบอร์ด</span>' : '';
	}
	$html  = fenix_dashboard_button(
		'visit',
		array(
			'class' => 'btn btn-fire dash-btn',
			'pos'   => $pos,
		)
	);
	$html .= fenix_dashboard_button(
		'signup',
		array(
			'class' => $ghost . ' dash-btn',
			'pos'   => $pos,
		)
	);
	if ( $signin ) {
		$html .= fenix_dashboard_button(
			'signin',
			array(
				'class' => 'dash-textlink',
				'pos'   => $pos,
				'icon'  => false,
			)
		);
	}
	return $html;
}

/**
 * หัวเพจ: breadcrumb + ป้าย + H1 + คำโปรย + ปุ่มเข้าชม / สมัคร + หมายเหตุ (ข้อมูลตัวอย่าง · รหัสเชิญ)
 */
function fenix_dashboard_page_hero( $title ) {
	$kicker  = fenix_dashboard_text( 'dash_kicker' );
	$sub     = fenix_dashboard_text( 'dash_sub' );
	$actions = fenix_dashboard_page_actions( 'dashboard-hero', false, 'btn btn-ghost-dark' );
	if ( '' !== $actions ) {
		$actions .= fenix_dashboard_notes( array( 'sample', 'invite' ), 'dash-notes--hero' );
	}
	$title = '' !== trim( (string) $title ) ? $title : $kicker;
	if ( function_exists( 'fenix_pages_hero' ) ) {
		fenix_pages_hero( $kicker, $title, fenix_is_placeholder( $sub ) ? '' : $sub, $actions );
		return;
	}
	fenix_page_hero( $kicker, $title, fenix_is_placeholder( $sub ) ? '' : $sub );
}

/**
 * [1] ใครทำอะไร · ตาราง "เรื่อง | ส่วนที่คุณทำเอง | ส่วนที่เซิร์ฟเวอร์ของ FALCON ทำ" แสดงเป็นการ์ดสองใบ (ใบขวา = เซิร์ฟเวอร์)
 * แต่ละใบเป็นรายการ dt/dd ลำดับเดียวกัน อ่านคู่กันได้ทีละเรื่อง และบนจอแคบการ์ดแต่ละใบยังอ่านครบในตัวเอง
 * แทนส่วน "สองทางในการใช้ FALCON" เดิม: FALCON ใช้งานผ่านแดชบอร์ดทางเดียว จึงไม่มีการเทียบกับการติดตั้งเองอีก
 * (คลาส CSS ยังชื่อ .dash-ways / .dash-way ตามเดิม · assets/css/dashboard-page.css)
 */
function fenix_dashboard_page_roles() {
	$rows = fenix_dashboard_rows( fenix_dashboard_text( 'dash_roles_rows' ), 2 );
	if ( count( $rows ) < 2 ) {
		return;
	}
	$head = array_shift( $rows );
	$ways = array(
		1 => array(
			'name'  => isset( $head[1] ) ? $head[1] : '',
			'items' => array(),
		),
		2 => array(
			'name'  => isset( $head[2] ) ? $head[2] : '',
			'items' => array(),
		),
	);
	/* แถวที่ใช้ได้ต้องมีครบทั้งหัวแถวและค่าของสองฝั่ง การ์ดสองใบจึงมีจำนวนแถวเท่ากันและอ่านคู่กันได้ทีละแถว */
	foreach ( $rows as $row ) {
		$label = isset( $row[0] ) ? $row[0] : '';
		$one   = isset( $row[1] ) ? $row[1] : '';
		$two   = isset( $row[2] ) ? $row[2] : '';
		if ( '' === $label || '' === $one || '' === $two || fenix_dashboard_is_result_text( $one . ' ' . $two ) ) {
			continue;
		}
		$ways[1]['items'][] = array( $label, $one );
		$ways[2]['items'][] = array( $label, $two );
	}
	if ( empty( $ways[1]['items'] ) ) {
		return;
	}
	$link_label = fenix_dashboard_text( 'dash_roles_link' );
	$link_path  = trim( (string) fenix_mod( 'dash_roles_link_url' ) );
	$link_url   = '';
	/* เพจที่ถอดออกจากเว็บแล้ว (วิธีติดตั้ง, VPS, Forward Test) ไม่ถูกลิงก์ แม้ค่าที่บันทึกไว้ยังชี้ไป */
	$link_gone  = function_exists( 'fenix_slug_retired' ) && ! preg_match( '#^(https?:)?//#i', $link_path ) && fenix_slug_retired( $link_path );
	if ( '' !== $link_label && '' !== $link_path && '#' !== $link_path && ! $link_gone ) {
		/* slug ในเว็บ: ลิงก์เฉพาะเมื่อหน้านั้นเผยแพร่แล้ว */
		if ( preg_match( '#^/?([a-z0-9-]+)/?$#', $link_path, $m ) && function_exists( 'fenix_published_page_url' ) ) {
			$link_url = '' !== (string) fenix_published_page_url( $m[1] ) ? home_url( '/' . $m[1] . '/' ) : '';
		} else {
			$link_url = fenix_link_url( $link_path );
		}
	}
	$note  = fenix_dashboard_text( 'dash_roles_note' );
	$visit = fenix_dashboard_button(
		'visit',
		array(
			'class' => 'btn btn-fire dash-btn',
			'pos'   => 'dashboard-roles',
		)
	);
	?>
<section class="section dash-ways<?php echo esc_attr( fenix_dashboard_tone( true ) ); ?>" id="roles">
	<div class="container">
		<?php fenix_dashboard_head( fenix_dashboard_text( 'dash_roles_kicker' ), fenix_dashboard_text( 'dash_roles_title' ), fenix_dashboard_text( 'dash_roles_sub' ) ); ?>
		<?php /* --dash-rows = หัวการ์ด + จำนวนแถว + ท้ายการ์ด (แถวของสองใบอยู่บนเส้นเดียวกันด้วย subgrid) */ ?>
		<div class="dash-ways-grid" style="<?php echo esc_attr( '--dash-rows:' . ( count( $ways[1]['items'] ) + 2 ) ); ?>">
			<?php foreach ( $ways as $fenix_col => $fenix_way ) : ?>
				<?php $fenix_is_dash = 2 === $fenix_col; ?>
				<article class="dash-way reveal<?php echo $fenix_is_dash ? ' dash-way--dash' : ''; ?>">
					<header class="dash-way-head">
						<?php echo fenix_icon_badge( $fenix_is_dash ? 'server' : 'user', $fenix_is_dash ? 'green' : 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div>
							<?php if ( '' !== $fenix_way['name'] ) : ?>
								<h3 class="dash-way-name"><?php echo fenix_dashboard_esc( $fenix_way['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
							<?php endif; ?>
						</div>
					</header>
					<dl class="dash-way-list">
						<?php foreach ( $fenix_way['items'] as $fenix_item ) : ?>
							<div class="dash-way-row">
								<dt><?php echo fenix_dashboard_esc( $fenix_item[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></dt>
								<dd><?php echo fenix_dashboard_esc( $fenix_item[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
					<?php /* ท้ายการ์ดพิมพ์เสมอ (ว่างได้) เพื่อให้สองใบมีจำนวนแถวเท่ากัน */ ?>
					<?php if ( $fenix_is_dash && '' !== $visit ) : ?>
						<div class="dash-way-foot"><?php echo $visit; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_button ?></div>
					<?php elseif ( ! $fenix_is_dash && '' !== $link_url ) : ?>
						<div class="dash-way-foot"><a class="btn btn-ghost dash-btn" href="<?php echo esc_url( $link_url ); ?>"><?php echo fenix_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $link_label ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></div>
					<?php else : ?>
						<div class="dash-way-foot"></div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php if ( '' !== $note ) : ?>
			<p class="dash-sec-note reveal"><?php echo fenix_icon( 'chat', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_rich_inline( $note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
		<?php endif; ?>
	</div>
</section>
	<?php
}

/**
 * [2] ภาพหน้าจอ: จอคอม (กว้าง) + มือถือ (แนวตั้ง) · ไม่มีรูปทั้งสองช่อง = คนทั่วไปไม่เห็น section นี้
 */
function fenix_dashboard_page_shots() {
	$admin = current_user_can( 'edit_theme_options' );
	$slots = array();
	foreach ( array( 'dash_shot1', 'dash_shot2' ) as $key ) {
		$has_img  = '' !== trim( (string) fenix_mod( $key . '_img' ) );
		$has_note = '' !== trim( (string) fenix_mod( $key . '_img_note' ) ) || '' !== trim( (string) fenix_mod( $key . '_img_alt' ) );
		/* เงื่อนไขเดียวกับ fenix_media_slot(): มีรูป หรือแอดมินที่มีโน้ตรอใส่รูป */
		if ( $has_img || ( $admin && $has_note ) ) {
			$slots[] = $key;
		}
	}
	if ( empty( $slots ) ) {
		return;
	}
	$label = trim( (string) fenix_mod( 'guides_img_label' ) );
	?>
<section class="section dash-shots<?php echo esc_attr( fenix_dashboard_tone() ); ?>" id="preview">
	<div class="container">
		<?php fenix_dashboard_head( fenix_dashboard_text( 'dash_shots_kicker' ), fenix_dashboard_text( 'dash_shots_title' ), fenix_dashboard_text( 'dash_shots_sub' ) ); ?>
		<div class="dash-shots-grid<?php echo 1 === count( $slots ) ? ' dash-shots-grid--one' : ''; ?>">
			<?php foreach ( $slots as $fenix_n => $fenix_key ) : ?>
				<?php
				$fenix_phone = 'dash_shot2' === $fenix_key;
				fenix_media_slot(
					$fenix_key,
					array(
						'width'  => $fenix_phone ? 780 : 1600,
						'height' => $fenix_phone ? 1688 : 1037,
						'label'  => '' !== $label ? trim( $label . ' ' . sprintf( '%02d', $fenix_n + 1 ) ) : '',
						'group'  => 'dash-shots',
						'class'  => $fenix_phone ? 'dash-shot dash-shot--phone' : 'dash-shot dash-shot--desk',
					)
				);
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
	<?php
}

/**
 * [3] สิ่งที่ทำได้: การ์ด ไอคอน + หัวข้อ + รายละเอียด (ข้อแรกเป็นการ์ดใหญ่)
 */
function fenix_dashboard_page_feats() {
	$items = array();
	foreach ( fenix_dashboard_rows( fenix_dashboard_text( 'dash_feats' ), 2 ) as $row ) {
		/* สองช่อง = หัวข้อ | รายละเอียด (ไม่ระบุไอคอน) */
		if ( 2 === count( $row ) ) {
			array_unshift( $row, '' );
		}
		$title = isset( $row[1] ) ? $row[1] : '';
		$desc  = isset( $row[2] ) ? $row[2] : '';
		if ( '' === $title || fenix_dashboard_is_result_text( $title . ' ' . $desc ) ) {
			continue;
		}
		$icon    = sanitize_key( $row[0] );
		$items[] = array(
			'icon'  => '' !== $icon && '' !== fenix_icon( $icon ) ? $icon : 'check',
			'title' => $title,
			'desc'  => $desc,
		);
	}
	if ( empty( $items ) ) {
		return;
	}
	$note = fenix_dashboard_text( 'dash_feats_note' );
	?>
<section class="section dash-feats-section<?php echo esc_attr( fenix_dashboard_tone( true ) ); ?>" id="features">
	<div class="container">
		<?php fenix_dashboard_head( fenix_dashboard_text( 'dash_feats_kicker' ), fenix_dashboard_text( 'dash_feats_title' ), fenix_dashboard_text( 'dash_feats_sub' ) ); ?>
		<ul class="dash-feats" data-count="<?php echo esc_attr( (string) count( $items ) ); ?>">
			<?php foreach ( $items as $fenix_i => $fenix_item ) : ?>
				<li class="dash-feat reveal<?php echo 0 === $fenix_i ? ' dash-feat--lead' : ''; ?>">
					<?php echo fenix_icon_badge( $fenix_item['icon'], 0 === $fenix_i ? 'green' : 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="dash-feat-body">
						<h3><?php echo fenix_dashboard_esc( $fenix_item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
						<?php if ( '' !== $fenix_item['desc'] ) : ?>
							<p><?php echo fenix_dashboard_esc( $fenix_item['desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( '' !== $note ) : ?>
			<p class="dash-sec-note reveal"><?php echo fenix_icon( 'phone', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_rich_inline( $note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
		<?php endif; ?>
	</div>
</section>
	<?php
}

/**
 * [4] ขั้นตอนเริ่มใช้ + ปุ่มเข้าชม / สมัคร / เข้าสู่ระบบ + หมายเหตุ (สถานะ · ข้อมูลตัวอย่าง · รหัสเชิญ)
 * การ์ดขั้นตอนใช้คลาส .order-flow ของ assets/css/pages.css (ชุดเดียวกับขั้นตอนสั่งซื้อในหน้าแพ็กเกจ)
 */
function fenix_dashboard_page_steps() {
	$steps = array();
	foreach ( fenix_dashboard_rows( fenix_dashboard_text( 'dash_steps' ) ) as $row ) {
		if ( '' === $row[0] ) {
			continue;
		}
		$steps[] = array( $row[0], isset( $row[1] ) ? trim( implode( ' | ', array_slice( $row, 1 ) ) ) : '' );
	}
	$actions = fenix_dashboard_page_actions( 'dashboard-steps', true );
	if ( empty( $steps ) && '' === $actions ) {
		return;
	}
	$notes = fenix_dashboard_notes( array( 'status', 'sample', 'invite' ), 'dash-notes--row' );
	$note  = fenix_dashboard_text( 'dash_steps_note' );
	?>
<section class="section dash-steps-section<?php echo esc_attr( fenix_dashboard_tone() ); ?>" id="start">
	<div class="container">
		<?php fenix_dashboard_head( fenix_dashboard_text( 'dash_steps_kicker' ), fenix_dashboard_text( 'dash_steps_title' ), fenix_dashboard_text( 'dash_steps_sub' ) ); ?>
		<?php if ( $steps ) : ?>
			<ol class="order-flow order-flow--<?php echo esc_attr( (string) min( 5, count( $steps ) ) ); ?> dash-steps">
				<?php foreach ( $steps as $fenix_n => $fenix_step ) : ?>
					<li class="reveal">
						<span class="order-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_n + 1 ) ); ?></span>
						<h3><?php echo fenix_dashboard_esc( $fenix_step[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
						<?php if ( '' !== $fenix_step[1] ) : ?>
							<p><?php echo fenix_rich_inline( $fenix_step[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<?php if ( '' !== $actions || '' !== $notes || '' !== $note ) : ?>
			<div class="dash-start reveal">
				<?php if ( '' !== $actions ) : ?>
					<div class="dash-actions dash-actions--row"><?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_page_actions ?></div>
				<?php endif; ?>
				<?php echo $notes; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_notes ?>
				<?php if ( '' !== $note ) : ?>
					<p class="dash-start-note"><?php echo fenix_icon( 'shield', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_dashboard_esc( $note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
	<?php
}

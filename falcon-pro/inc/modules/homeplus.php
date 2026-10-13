<?php
/**
 * FALCON PRO EA · บทเสริมของหน้าแรก (ต่อเข้ากับโครงบทมีเลขของ inc/modules/home.php)
 *
 *   [compare] เทียบวิธีทำงาน: เทรดด้วยมือ กับให้ EA ทำตามแผน · อยู่ต่อจากบทการทดสอบ
 *   [ready]   เช็กความพร้อมก่อนเริ่ม: รายการติ๊กได้ มีวงความคืบหน้า และปุ่มคุยกับทีมงานเมื่อครบ · อยู่ต่อจากบทแดชบอร์ด ก่อนบทแพ็กเกจ
 *
 * - นับเป็นบทที่ fenix_home_chapter_defs() (home.php) เลขบทและรางเลขบทจึงเรียงต่อกันเอง
 *   บทที่ปิดอยู่หรือไม่มีเนื้อหาจะไม่ถูกนับและไม่ถูกพิมพ์
 * - front-page.php เรียก fenix_homeplus_section( 'compare' | 'ready' ) ตามตำแหน่งบนหน้า
 * - ข้อความทุกคำเป็น setting: ค่าเริ่มต้น fenix_homeplus_defaults() · ช่องใน Customizer หมวด 2.6 และ 2.9
 * - ไม่มี JS ก็อ่านได้ครบ: ตารางเป็นตารางธรรมดา · เช็กลิสต์เป็นรายการพร้อมลิงก์คู่มือ (ช่องติ๊กและวงความคืบหน้าแสดงเมื่อ home.js ทำงาน)
 * - เช็กลิสต์ไม่เก็บและไม่ส่งข้อมูลใด: สถานะอยู่ในหน้าเท่านั้น (ไม่มี cookie, localStorage หรือคำขอไปเซิร์ฟเวอร์)
 * - CSS อยู่ท้าย assets/css/home.css (บล็อก H15) · JS อยู่ใน assets/js/home.js
 *
 * กฎ: ไม่มีตัวเลขผลเทรด ไม่มีคำรับประกันกำไร · ตารางเทียบต้องบอกว่าทั้งสองแบบขาดทุนได้
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * ค่าเริ่มต้น (ข้อความไทยของ FALCON เอง)
 * ============================================================== */

add_filter( 'fenix_defaults', 'fenix_homeplus_defaults' );

function fenix_homeplus_defaults( $d ) {
	return array_merge(
		$d,
		array(
			/* ---------- [compare] เทียบวิธีทำงาน ---------- */
			'show_home_compare'   => true,
			'home_compare_kicker' => 'เทียบวิธีทำงาน',
			/* ตารางต้องตรงกับตัวระบบ: เทรดทองคำ XAUUSD อย่างเดียว · ออเดอร์แก้ใช้ Lot ที่ใหญ่ขึ้นทีละขั้น · ไม่ตัดขาดทุนเอง
			 * ห้ามมีช่องที่บอกว่า EA ไม่ขยาย Lot หรือ EA ตัดขาดทุนให้ */
			'home_compare_title'  => 'เทรดด้วยมือ กับให้ EA เทรดตามกลยุทธ์ ต่างกันตรงไหน',
			'home_compare_sub'    => 'เทียบกันเฉพาะวิธีทำงานในแต่ละวัน ไม่มีช่องไหนบอกว่าแบบใดทำเงินได้มากกว่า เพราะผลลัพธ์ของทั้งสองแบบขึ้นกับตลาดและการตัดสินใจของคุณ',
			'home_compare_rows'   => "เทียบเรื่อง | เทรดด้วยมือ | ให้ FALCON PRO EA เทรดตามกลยุทธ์\n"
				. "สินทรัพย์ที่เทรด | เลือกเองได้ทุกครั้ง | ทองคำ XAUUSD อย่างเดียว\n"
				. "ใครเป็นคนเฝ้ากราฟ | ตัวคุณเอง ทุกครั้งที่ตลาดเปิดและอยากหาจังหวะ | ตัวโปรแกรม ส่วนคุณเปิดแอป MT5 หรือแดชบอร์ดดูยอดเมื่อสะดวก\n"
				. "กติกาถูกใช้สม่ำเสมอแค่ไหน | ขึ้นกับสภาพจิตใจและผลของออเดอร์ล่าสุด | เงื่อนไขของชุด EA เดียวกันถูกตรวจซ้ำทุกรอบ\n"
				. "ตอนที่คุณทำงานหรือหลับ | พักตามคุณ สัญญาณที่เกิดช่วงนั้นไม่ถูกเทรด | ทำงานต่อได้ ถ้า MT5 บนคอมหรือ VPS ของคุณยังเปิดอยู่ หรือเมื่อรันบนเซิร์ฟเวอร์ผ่านแดชบอร์ด\n"
				. "ขนาด Lot ของแต่ละออเดอร์ | คิดใหม่ก่อนกดทุกครั้ง จึงเปลี่ยนตามอารมณ์ได้ | ออเดอร์แรกคิดจากทุนหรือค่าที่ตั้งไว้ ออเดอร์แก้ใหญ่ขึ้นทีละขั้นตามที่โปรแกรมกำหนด\n"
				. "เมื่อออเดอร์ติดลบ | คุณตัดสินใจเองว่าจะตัดขาดทุนหรือถือต่อ | ไม่ตัดขาดทุนเอง ถือไว้แล้วเปิดออเดอร์แก้เพิ่มด้วย Lot ที่ใหญ่ขึ้น\n"
				. 'ขาดทุนได้ไหม | ได้ | ได้เหมือนกัน และเสียได้ถึงหมดบัญชี',
			'home_compare_note'   => 'ไม่ว่าจะกดเองหรือให้ EA กดแทน เงินในบัญชีก็ลดลงได้ทั้งคู่ สิ่งที่ EA ช่วยคือความสม่ำเสมอในการทำตามกลยุทธ์ ไม่ใช่การลดความเสี่ยงของตลาด และเพราะ EA ไม่ตัดขาดทุนเอง ช่วงที่ราคาทองคำไปทางเดียวนาน บัญชีอาจเสียหายหนักกว่าการเทรดมือที่ตัดขาดทุนเร็ว',

			/* ---------- [ready] เช็กความพร้อม ---------- */
			'show_home_ready'      => true,
			'home_ready_kicker'    => 'เช็กความพร้อม',
			'home_ready_title'     => 'พร้อมให้ EA ทำงานกับเงินจริงหรือยัง · เช็กลิสต์ก่อนเริ่ม',
			'home_ready_sub'       => 'ไล่ดูทีละข้อว่าทำเสร็จแล้วหรือยัง ข้อที่ยังค้างมีลิงก์ไปคู่มือของเรื่องนั้น',
			'home_ready_items'     => "เปิดบัญชีเทรด MetaTrader 5 และยืนยันตัวตนกับโบรกเกอร์เสร็จแล้ว | /open-mt5-account/\n"
				. "ล็อกอิน MT5 ได้ และเห็นราคาทองคำ XAUUSD ในโปรแกรมขยับ | /mt5-login/\n"
				. "เลือกแล้วว่าจะรัน EA ทางไหน ระหว่างติดตั้งเองบนคอมหรือ VPS กับรันผ่านแดชบอร์ด | /dashboard/\n"
				. "ดูผล Backtest ของชุด EA ที่สนใจ พร้อมเงื่อนไขและข้อจำกัดครบแล้ว | /backtest/\n"
				. "อ่านประกาศความเสี่ยงจบทุกข้อ และเข้าใจว่า EA ไม่ตัดขาดทุนเอง เงินในบัญชีอาจลดลงจนหมด | /risk-disclosure/\n"
				. "เงินก้อนนี้ถ้าเสียไปทั้งหมด ค่าใช้จ่ายประจำเดือนยังไม่สะดุด |\n"
				. 'วางแผนเริ่มด้วยเงินจำนวนน้อยหรือบัญชี Cent ก่อนเพิ่มทุน | /forward-test/',
			'home_ready_link_text' => 'เปิดอ่าน',
			'home_ready_progress'  => 'ติ๊กแล้ว {n} จาก {total} ข้อ',
			'home_ready_done'      => 'ครบทุกข้อแล้ว ขั้นต่อไปคือคุยกับทีมงานเรื่องทุนและการตั้งค่าก่อนเริ่มจริง',
			'home_ready_btn'       => 'คุยกับทีมงานทาง LINE',
			'home_ready_note'      => 'เช็กลิสต์นี้ทำงานในเบราว์เซอร์ของคุณเท่านั้น เราไม่บันทึกและไม่ส่งคำตอบไปที่ใด',
		)
	);
}

/* ==============================================================
 * Customizer · หมวดของสองบท แทรกตามลำดับบนหน้า (ต่อจากหมวดของบทก่อนหน้า)
 * ============================================================== */

add_filter( 'fenix_customizer_sections', 'fenix_homeplus_customizer_sections', 11, 2 );

function fenix_homeplus_customizer_sections( $sections, $d ) {
	$rule    = 'ห้ามใส่ตัวเลขผลเทรด เปอร์เซ็นต์กำไร หรือคำสัญญาว่าได้กำไร';
	$kick    = 'แสดงต่อจากเลขบทเหนือหัวข้อ เมื่อเปิด "แสดงคำเล็กเหนือหัวข้อ (ทั้งเว็บ)" ที่หมวด 1 และใช้เป็นชื่อบทบนรางเลขบทเมื่อเปิดราง · ค่าเริ่มต้นปิดทั้งสองอย่าง หัวข้อจึงขึ้นเดี่ยว ๆ';
	$compare = array(
		'fenix_home_compare' => array(
			'title'       => '2.6) หน้าแรก · บท 06 เทียบวิธีทำงาน (ตาราง)',
			'description' => 'ตารางเทียบการเทรดด้วยมือกับการให้ EA ทำตามแผน · ต้องมีแถวที่บอกว่าทั้งสองแบบขาดทุนได้ และห้ามมีช่องที่บอกว่าแบบใดได้ผลดีกว่า · จอแคบแต่ละแถวจะเรียงเป็นการ์ด · ตารางที่เหลือไม่ถึง 2 บรรทัด (หัวตาราง + 1 แถว) จะซ่อนทั้งบท',
			'fields'      => array(
				'show_home_compare'   => array( 'แสดงบทนี้', 'checkbox' ),
				'home_compare_kicker' => array( 'ป้ายบท (คำเล็กเหนือหัวข้อ)', 'text', $kick ),
				'home_compare_title'  => array( 'หัวข้อ (H2)', 'text' ),
				'home_compare_sub'    => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'home_compare_rows'   => array( 'ตาราง (บรรทัดแรก = หัวตาราง · คั่นช่องด้วย | · ช่องแรกของแต่ละบรรทัด = หัวแถว)', 'textarea', 'คอลัมน์สุดท้ายคือคอลัมน์ของ EA (หัวคอลัมน์สีเขียว) · บรรทัดที่มีตัวเลขตามด้วย % จะไม่แสดง · ' . $rule ),
				'home_compare_note'   => array( 'หมายเหตุใต้ตาราง (ห้ามลบเรื่องขาดทุน)', 'textarea' ),
			),
		),
	);
	$ready   = array(
		'fenix_home_ready' => array(
			'title'       => '2.9) หน้าแรก · บท 09 เช็กความพร้อม (รายการติ๊กได้)',
			'description' => 'ผู้เข้าชมติ๊กข้อที่ทำเสร็จแล้ว วงความคืบหน้าจะขยับ และเมื่อครบทุกข้อจะมีปุ่มคุยกับทีมงาน · ไม่มีการบันทึกหรือส่งคำตอบ สถานะอยู่ในหน้านั้นเท่านั้น · เบราว์เซอร์ที่ปิด JavaScript จะเห็นเป็นรายการธรรมดาพร้อมลิงก์คู่มือ · ไม่มีรายการ = ซ่อนทั้งบท',
			'fields'      => array(
				'show_home_ready'      => array( 'แสดงบทนี้', 'checkbox' ),
				'home_ready_kicker'    => array( 'ป้ายบท (คำเล็กเหนือหัวข้อ)', 'text', $kick ),
				'home_ready_title'     => array( 'หัวข้อ (H2)', 'text' ),
				'home_ready_sub'       => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'home_ready_items'     => array( 'รายการ (บรรทัดละ 1 ข้อ: ข้อความ | ลิงก์คู่มือ หรือเว้นว่าง)', 'textarea', 'ลิงก์ในเว็บจะแสดงเมื่อหน้านั้นเผยแพร่แล้วเท่านั้น · ' . $rule ),
				'home_ready_link_text' => array( 'ข้อความลิงก์คู่มือท้ายแต่ละข้อ (เว้นว่าง = ไม่แสดงลิงก์)', 'text' ),
				'home_ready_progress'  => array( 'ข้อความความคืบหน้า ({n} = จำนวนที่ติ๊ก · {total} = จำนวนทั้งหมด)', 'text' ),
				'home_ready_done'      => array( 'ข้อความเมื่อติ๊กครบทุกข้อ', 'textarea', 'ให้ชวนคุยกับทีมงานก่อนเริ่ม ไม่ใช่บอกว่าพร้อมทำกำไร' ),
				'home_ready_btn'       => array( 'ข้อความปุ่มติดต่อเมื่อติ๊กครบ (เมื่อมีลิงก์ LINE)', 'text' ),
				'home_ready_note'      => array( 'หมายเหตุเรื่องความเป็นส่วนตัวใต้วงความคืบหน้า (เว้นว่าง = ซ่อน)', 'text' ),
			),
		),
	);

	$sections = fenix_home_insert_after( $sections, 'fenix_home_tests', $compare );
	/* เช็กความพร้อมอยู่ต่อจากบทแดชบอร์ด (ลำดับเดียวกับบนหน้า) · ไม่มีหมวดนั้น = ต่อจากเส้นทางเริ่มต้นตามเดิม */
	return fenix_home_insert_after( $sections, isset( $sections['fenix_home_dashboard'] ) ? 'fenix_home_dashboard' : 'fenix_home_install', $ready );
}

/* ==============================================================
 * ตัวช่วย
 * ============================================================== */

/**
 * พิมพ์บทเสริมตามชื่อ · พิมพ์เฉพาะบทที่ fenix_home_chapters() นับไว้ (เปิดอยู่และมีเนื้อหา)
 */
function fenix_homeplus_section( $name ) {
	$chapters = fenix_home_chapters();
	$fn       = 'fenix_homeplus_' . $name;
	if ( isset( $chapters[ $name ] ) && function_exists( $fn ) ) {
		call_user_func( $fn );
	}
}

/**
 * แถวของตารางเทียบ · คืน array( 'head' => [...], 'rows' => [ [...], ... ] ) หรือ array() เมื่อเหลือไม่ถึงหัวตาราง + 1 แถว
 * - จำนวนคอลัมน์ยึดตามหัวตาราง (อย่างน้อย 2) แถวที่ช่องขาดถูกเติมช่องว่าง ช่องเกินถูกตัด
 * - แถวที่มีตัวเลขตามด้วย % ถูกข้าม (อ่านแล้วเหมือนผลเทรด) · ไม่กรองคำว่า "กำไร" เพราะแถว "ไม่รับประกันกำไร" ต้องแสดงได้
 */
function fenix_homeplus_compare_rows() {
	static $out = null;
	if ( null !== $out ) {
		return $out;
	}
	$out  = array();
	$rows = fenix_home_rows( fenix_mod( 'home_compare_rows' ), 2 );
	if ( count( $rows ) < 2 ) {
		return $out;
	}
	$head = array_shift( $rows );
	$cols = count( $head );
	$body = array();
	foreach ( $rows as $row ) {
		if ( preg_match( '/[+-]?\d[\d.,]*\s*%/u', implode( ' ', $row ) ) ) {
			continue;
		}
		$body[] = array_slice( array_pad( $row, $cols, '' ), 0, $cols );
	}
	if ( $cols >= 2 && $body ) {
		$out = array(
			'head' => $head,
			'rows' => $body,
		);
	}
	return $out;
}

/**
 * รายการของเช็กลิสต์ · [ { text, url } ] · url = '' เมื่อไม่ได้ใส่ลิงก์หรือหน้าปลายทางยังไม่เผยแพร่
 */
function fenix_homeplus_ready_items() {
	static $items = null;
	if ( null !== $items ) {
		return $items;
	}
	$items = array();
	foreach ( fenix_home_rows( fenix_mod( 'home_ready_items' ) ) as $row ) {
		if ( '' === $row[0] ) {
			continue;
		}
		$items[] = array(
			'text' => $row[0],
			'url'  => isset( $row[1] ) ? fenix_home_link( $row[1] ) : '',
		);
	}
	return $items;
}

/* ==============================================================
 * [compare] ตารางเทียบวิธีทำงาน
 * ใช้ตารางของธีม (.table-wrap.table-wrap--stack > .data-table) + หัวแถวแบบ th scope="row" และคอลัมน์ของ EA (.is-brand)
 * ============================================================== */
function fenix_homeplus_compare() {
	$table = fenix_homeplus_compare_rows();
	if ( empty( $table ) ) {
		return;
	}
	$head = $table['head'];
	$last = count( $head ) - 1;
	$note = trim( (string) fenix_mod( 'home_compare_note' ) );
	?>
<section class="hm-ch hp-compare<?php echo esc_attr( fenix_home_tone( 'compare' ) ); ?>" id="compare"<?php fenix_home_ch_attrs( 'compare' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'compare', fenix_mod( 'home_compare_title' ), fenix_mod( 'home_compare_sub' ) ); ?>
		<div class="table-wrap table-wrap--stack hp-compare-wrap">
			<table class="data-table hp-compare-table">
				<thead>
					<tr>
						<?php foreach ( $head as $fenix_c => $fenix_cell ) : ?>
							<th scope="col"<?php echo $fenix_c === $last ? ' class="is-brand"' : ''; ?>><?php echo fenix_home_text( $fenix_cell ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $table['rows'] as $fenix_row ) : ?>
						<tr>
							<?php foreach ( $fenix_row as $fenix_c => $fenix_cell ) : ?>
								<?php if ( 0 === $fenix_c ) : ?>
									<th scope="row"><?php echo fenix_home_text( $fenix_cell ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></th>
								<?php else : ?>
									<?php /* ข้อความของช่องอยู่ใน <span> เดียว: จอแคบ td เป็น grid สองช่อง (ป้าย ::before | ค่า) ถ้าปล่อยข้อความกับ span.nobr เป็นลูกตรงของ td แต่ละท่อนจะกลายเป็นช่อง grid แยกกัน */ ?>
									<td<?php echo $fenix_c === $last ? ' class="is-brand"' : ''; ?> data-label="<?php echo esc_attr( $head[ $fenix_c ] ); ?>"><span class="hp-compare-val"><?php echo fenix_home_text( $fenix_cell ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></td>
								<?php endif; ?>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php if ( '' !== $note ) : ?>
			<p class="hm-notice hm-notice--warn"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_home_text( $note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
		<?php endif; ?>
	</div>
</section>
	<?php
}

/* ==============================================================
 * [ready] เช็กความพร้อม
 * ไม่มี JS: รายการธรรมดา + ลิงก์คู่มือ (ช่องติ๊ก วงความคืบหน้า และกล่อง "ครบแล้ว" ซ่อนอยู่จนกว่า home.js จะใส่ .hm-js)
 * มี JS: ติ๊กแล้ววง --p ขยับ ข้อความสถานะ (aria-live) เปลี่ยน และเมื่อครบทุกข้อจึงแสดงปุ่มติดต่อ
 * ============================================================== */
function fenix_homeplus_ready() {
	$items = fenix_homeplus_ready_items();
	if ( empty( $items ) ) {
		return;
	}
	$total    = count( $items );
	$link     = trim( (string) fenix_mod( 'home_ready_link_text' ) );
	$progress = trim( (string) fenix_mod( 'home_ready_progress' ) );
	$done     = trim( (string) fenix_mod( 'home_ready_done' ) );
	$privacy  = trim( (string) fenix_mod( 'home_ready_note' ) );
	/* ข้อความสถานะของทุกจำนวนที่ติ๊กได้ (0 … ทั้งหมด) แทนค่า {n} {total} ไว้ล่วงหน้า · home.js แค่เลือกบรรทัดตามจำนวนที่ติ๊ก */
	$states = array();
	for ( $n = 0; $n <= $total; $n++ ) {
		$states[] = str_replace( array( '{n}', '{total}' ), array( (string) $n, (string) $total ), $progress );
	}
	?>
<section class="hm-ch hp-ready<?php echo esc_attr( fenix_home_tone( 'ready' ) ); ?>" id="ready"<?php fenix_home_ch_attrs( 'ready' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'ready', fenix_mod( 'home_ready_title' ), fenix_mod( 'home_ready_sub' ) ); ?>
		<div class="hp-ready-grid" data-hm-ready data-states="<?php echo esc_attr( wp_json_encode( $states, JSON_UNESCAPED_UNICODE ) ); ?>">
			<ul class="hp-ready-list">
				<?php foreach ( $items as $fenix_item ) : ?>
					<li class="hp-ready-item">
						<label class="hp-ready-label">
							<input type="checkbox" class="hp-ready-box" autocomplete="off">
							<span class="hp-ready-tick" aria-hidden="true"><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="hp-ready-text"><?php echo fenix_home_text( $fenix_item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span>
						</label>
						<?php if ( '' !== $fenix_item['url'] && '' !== $link ) : ?>
							<a class="hp-ready-link" href="<?php echo esc_url( $fenix_item['url'] ); ?>"><span><?php echo esc_html( $link ); ?></span><span class="sr-only">: <?php echo esc_html( $fenix_item['text'] ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="hp-ready-meter">
				<div class="hp-ring" style="--p:0" aria-hidden="true">
					<svg viewBox="0 0 120 120" focusable="false"><circle class="hp-ring-bg" cx="60" cy="60" r="50"/><circle class="hp-ring-fg" cx="60" cy="60" r="50" pathLength="100"/></svg>
					<span class="hp-ring-num"><b data-hm-ready-n>0</b><i>/ <?php echo (int) $total; ?></i></span>
				</div>
				<div class="hp-ready-live" aria-live="polite">
					<?php if ( '' !== $progress ) : ?>
						<p class="hp-ready-status" data-hm-ready-status><?php echo esc_html( $states[0] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $done ) : ?>
						<p class="hp-ready-done" data-hm-ready-done hidden><?php echo fenix_home_text( $done ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
					<?php endif; ?>
				</div>
				<div class="hp-ready-cta" data-hm-ready-done hidden>
					<?php fenix_home_contact_link( fenix_mod( 'home_ready_btn' ), 'btn btn-fire hp-ready-btn', 'home-ready' ); ?>
				</div>
				<?php if ( '' !== $privacy ) : ?>
					<p class="hp-ready-note"><?php echo fenix_icon( 'lock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_home_text( $privacy ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
	<?php
}

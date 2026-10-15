<?php
/**
 * In-content diagram (not a cover) · preview route /__art/<name>/?layout=wide|tall
 * /__art/ alone prints the list for dev/make-art.php (one line per diagram: name<TAB>version).
 * Rendered by headless Chrome in dev/make-art.php and saved to falcon-pro/assets/img/illus/:
 *   falcon-pro-ea-<name>-v<N>.webp         wide 1600×760 (desktop and tablet)
 *   falcon-pro-ea-<name>-mobile-v<N>.webp  tall 800×1140 (narrow screens · picked up by [falcon_figure] on its own)
 * Look: FALCON Banner theme v3 · flat #172125 ground, #1D2A2F / #25343A panels, solid #48F790 blocks (the logo green) with a diagonal cut,
 * #48F790 on dark only, Noto Sans Thai from the theme, one soft neutral shadow, no gradients, no glow. Not part of the theme.
 *
 * กติกาของภาพ:
 * - ไม่มีตัวเลขผลเทรด ราคา หรือชื่อโบรกเกอร์ในภาพ (เลขที่มีได้คือเลขลำดับขั้นเท่านั้น) และไม่สัญญาผลกำไร
 * - ไม่มีป้ายหรือบรรทัดเล็กเหนือหัวเรื่อง (เจ้าของสั่ง 12 ต.ค. 2026: หัวเรื่องยืนเดี่ยว) · ภาพเริ่มที่หัวเรื่อง ตามด้วยบรรทัดรอง · อย่าเพิ่มกลับ
 * - ห้ามใส่ letter-spacing กับข้อความไทย
 * - ข้อความในภาพต้องตรงกับวิธีทำงานจริงของ FALCON (เอกสารของแดชบอร์ด): ผู้ใช้เลือกชุด EA และวิธีคิด Lot ไม่ได้เขียนกติกาเทรดเอง
 *   · EA ไม่ตัดขาดทุนเอง · การสั่งหยุดไม่ได้ปิดออเดอร์ที่ค้าง · Lot เปลี่ยนได้เมื่อไม่มีออเดอร์ค้าง · ทีมงานไม่ขอรหัสผ่านหรือรหัสยืนยันใด ๆ
 * - ข้อความตัดบรรทัดได้เฉพาะที่ช่องว่าง หรือที่เครื่องหมาย | ซึ่งใส่ไว้ระหว่างคำไทยที่เขียนติดกันเพื่อบอกจุดตัดที่ยอมให้
 *   (ไม่ถูกพิมพ์ออกมา) จึงไม่มีคำไทยถูกตัดกลางคำ · เครื่องหมาย ~ คือช่องว่างที่ห้ามตัดบรรทัด ใช้ยึดคำที่ต้องอยู่ด้วยกัน
 *   เช่น Sign~out หรือ บน~VPS · ทั้งสองเครื่องหมายใช้ได้ในหัวเรื่อง บรรทัดรอง หัวข้อ คำอธิบาย และแถบด้านล่าง (ไม่ใช้ในป้ายสั้น)
 * - ถ้าข้อความยังล้น สคริปต์ท้ายไฟล์จะย่อตัวอักษรลงทีละ 2% (ต่ำสุด 80%) แล้วเขียนผลไว้ที่ <html data-fit data-k>
 *   dev/make-art.php อ่านค่านี้ พิมพ์ขนาดที่ใช้จริง และแจ้งเตือนเมื่อผลไม่ใช่ "ok" · ทั้งสามภาพตอนนี้ลงตัวที่ขนาดเต็ม (1)
 * - ข้อความถูกอบลงในภาพ: แก้คำเมื่อไหร่ให้เพิ่มเลข 'v' ของแผนภาพนั้น เรนเดอร์ใหม่ แล้วแก้ชื่อไฟล์ใน fenix_figure_registry()
 *   (inc/shortcodes.php) ใน fenix_home_defaults() (inc/modules/home.php · เฉพาะ how-it-works) และทุกจุดที่อ้างชื่อไฟล์ให้ตรงกัน
 *   เพราะแคชเก็บรูปตาม URL · ลบไฟล์รุ่นเก่าออกจาก assets/img/illus/ ด้วย (dev/make-art.php เตือนเมื่อยังเหลืออยู่)
 *
 * โครงข้อมูลของแผนภาพ 1 ภาพ:
 *   'v'      เลขรุ่นของไฟล์ · 'title' หัวเรื่อง · 'sub' บรรทัดรอง
 *   'mode'   'steps' = ขั้นตอนมีเลขลำดับ · 'chain' = ส่วนประกอบที่ต่อถึงกัน ไม่มีเลข
 *   'steps'  array( ไอคอนจาก fenix_icon(), หัวข้อ, คำอธิบาย, [ป้ายสั้น ๆ ใต้คำอธิบาย], [true = การ์ดเด่น] )
 *   'note'   แถบข้อความสำคัญด้านล่าง · 'icon' ไอคอนของแถบนั้น
 *
 * @var string $fx_art_name set by router.php
 */

$fx_arts = array(
	/* หน้าแรก: EA ทำงานอย่างไร (วงจร 4 ขั้นในบัญชีของผู้ใช้เอง)
	 * v2 (13 ต.ค. 2026): ไม่มีป้ายเหนือหัวเรื่อง และแก้คำให้ตรงกับตัวโปรแกรม · ผู้ใช้เลือกชุด EA ทุน และวิธีคิด Lot (ไม่ได้เขียนกติกาเอง
	 * และไม่มี "ระดับขาดทุนที่ระบบตัดให้") · ขั้นที่ 4 คือดูแล้วตัดสินใจรันต่อหรือหยุด (ไม่ใช่ "ปรับค่าได้ทุกเมื่อ": Lot เปลี่ยนได้เมื่อไม่มีออเดอร์ค้าง)
	 * · แถบล่างบอกตรง ๆ ว่า EA ไม่ตัดขาดทุนเอง · ถ้าแก้หัวข้อของขั้น ให้แก้ alt ของ how-it-works ใน fenix_figure_registry() ให้ตรงกันด้วย */
	'how-it-works' => array(
		'v'      => 2,
		'title'  => 'EA ทำงานอย่างไร|ในบัญชีของคุณ',
		'sub'    => 'สี่ขั้นที่วนซ้ำตลอดการใช้งาน โดยคุณเป็นผู้กำหนดกรอบเอง',
		'mode'   => 'steps',
		'steps'  => array(
			array( 'gauge', 'คุณเลือกชุด~EA และตั้งขีดจำกัด', 'เลือกชุดตามความเสี่ยง|ที่รับได้ กำหนดทุน|และวิธีคิด~Lot' ),
			array( 'candles', 'EA เฝ้าตลาด|ทองคำ|แทนคุณ', 'อ่านราคา XAUUSD บน~MT5 ตามเงื่อนไข|ของชุดที่เลือก' ),
			array( 'bolt', 'ส่งคำสั่งในบัญชี|ของคุณเอง', 'ออเดอร์เปิดและปิด|ในบัญชีโบรกเกอร์|ที่เป็นชื่อคุณ' ),
			array( 'gear', 'คุณตรวจผล|และตัดสินใจ', 'ดูออเดอร์และยอดเงิน แล้วเลือกเอง|ว่าจะรันต่อ|หรือสั่งหยุด' ),
		),
		'note'   => 'EA ไม่ตัดขาดทุนเอง และไม่รับประกันกำไร การเทรดมีความเสี่ยง|และเสียเงินได้ทั้งบัญชี',
		'icon'   => 'warn',
	),

	/* คู่มือเปิดบัญชี: จากสมัครสมาชิกจนมีเงินในบัญชีเทรด (ลำดับเดียวกับคู่มือเปิดบัญชีและคู่มือฝากเงิน)
	 * v2: ไม่มีป้ายเหนือหัวเรื่อง · บรรทัดรองพูดว่า "ก่อนเริ่มใช้ EA" (ใช้ได้ทั้งทางติดตั้งเองและทางรันผ่านแดชบอร์ด)
	 * · แถบล่าง: ทีมงานไม่ขอรหัสผ่านหรือรหัสยืนยันใด ๆ (เดิมพูดถึงรหัสผ่านหลักอย่างเดียว แคบกว่าที่เว็บและแดชบอร์ดประกาศ) */
	'account-flow' => array(
		'v'      => 2,
		'title'  => 'จากสมัครสมาชิก จนเงินเข้าบัญชีเทรด~MT5',
		'sub'    => 'งานฝั่งโบรกเกอร์สี่ขั้น ทำให้ครบก่อนเริ่มใช้~EA',
		'mode'   => 'steps',
		'steps'  => array(
			array( 'user', 'สมัครสมาชิก|กับโบรกเกอร์', 'กรอกฟอร์มสมัคร แล้วกดยืนยัน|ในอีเมลที่ได้รับ' ),
			array( 'shield', 'ยืนยันตัวตน (KYC)', 'อัปโหลดเอกสาร|ในหน้าสมาชิก แล้วรอผลตรวจ' ),
			array( 'monitor', 'เปิดบัญชีเทรด MT5', 'จดเลขบัญชี รหัสผ่าน และชื่อเซิร์ฟเวอร์|เก็บไว้' ),
			array( 'dollar', 'ฝากเงิน|เข้าบัญชีเทรด', 'ทำรายการ|ในหน้าสมาชิก|ของโบรกเกอร์ แล้วตรวจยอด|ใน MT5' ),
		),
		'note'   => 'เงินอยู่ในบัญชีโบรกเกอร์|ในชื่อของคุณเอง ทีมงานไม่รับฝากเงิน และไม่ขอรหัสผ่าน|หรือรหัสยืนยันใด~ๆ',
		'icon'   => 'lock',
	),

	/* คู่มือ VPS สามหน้า: เครื่องของคุณ → Remote Desktop → Windows VPS ที่เปิด MT5 กับ EA ค้างไว้ (ทางติดตั้งเอง)
	 * v2: ไม่มีป้ายเหนือหัวเรื่อง ข้อความอื่นเหมือนรุ่นแรก
	 * v3 (13 ต.ค. 2026): ป้ายสั้นในการ์ด (Windows / Android / iPhone ฯลฯ) เป็นสี่เหลี่ยมมุมมนเล็กน้อย ไม่ใช่ทรงแคปซูล ข้อความเหมือน v2 ทุกคำ */
	'vps-flow'     => array(
		'v'      => 3,
		'title'  => 'EA ทำงานบน VPS ส่วนคุณแค่เปิดเข้าไปดู',
		'sub'    => 'เครื่องของคุณเป็นเพียงหน้าจอควบคุม ตัวที่เทรดจริงคือ~MT5~บน~VPS',
		'mode'   => 'chain',
		'steps'  => array(
			array( 'phone', 'อุปกรณ์ของคุณ', 'ใช้เปิดดูและสั่งงานเท่านั้น ปิดเครื่องได้ตามปกติ', array( 'Windows', 'Android', 'iPhone' ) ),
			array( 'link', 'Remote Desktop', 'ช่องทางเข้าหน้าจอ|ของ VPS ผ่านอินเทอร์เน็ต ด้วย~IP ชื่อผู้ใช้ และรหัสผ่าน', array( 'Remote Desktop Connection', 'Windows App' ) ),
			array( 'server', 'Windows VPS', 'MT5 และ EA เปิดค้างไว้|บนเครื่องนี้ จึงทำงานต่อเนื่อง|ตลอดเวลาที่ตลาดเปิด', array( 'MT5', 'EA', 'เปิดตลอด' ), true ),
		),
		'note'   => 'เลิกดูให้ปิดหน้าต่าง Remote~Desktop (Disconnect) อย่า~Sign~out อย่า~Shut~down และอย่าปิด~MT5 บน~VPS',
		'icon'   => 'warn',
	),
);

/* รายชื่อสำหรับ dev/make-art.php */
if ( '' === $fx_art_name ) {
	header( 'Content-Type: text/plain; charset=utf-8' );
	foreach ( $fx_arts as $name => $art ) {
		echo $name . "\t" . (int) $art['v'] . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed keys of the array above
	}
	return;
}
if ( ! isset( $fx_arts[ $fx_art_name ] ) ) {
	http_response_code( 404 );
	echo 'No art: ' . htmlspecialchars( $fx_art_name );
	return;
}

$a     = $fx_arts[ $fx_art_name ];
$tall  = isset( $_GET['layout'] ) && 'tall' === $_GET['layout']; // phpcs:ignore WordPress.Security.NonceVerification
$chain = isset( $a['mode'] ) && 'chain' === $a['mode'];
$theme = get_template_directory_uri();

// ช่องว่างและ | = จุดที่ยอมให้ขึ้นบรรทัดใหม่ · ~ = ช่องว่างที่ห้ามตัดบรรทัด · แต่ละท่อนเป็น inline-block จึงไม่ถูกตัดกลางคำ
$fx_chunks = function ( $text ) {
	$out = array();
	foreach ( preg_split( '/ +/u', trim( (string) $text ), -1, PREG_SPLIT_NO_EMPTY ) as $word ) {
		$html = '';
		foreach ( explode( '|', $word ) as $part ) {
			if ( '' !== $part ) {
				$html .= '<span>' . esc_html( str_replace( '~', "\u{00A0}", $part ) ) . '</span>';
			}
		}
		$out[] = $html;
	}
	return implode( ' ', $out );
};
$fx_plain = function ( $text ) {
	return str_replace( array( '|', '~' ), array( '', ' ' ), (string) $text );
};
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title><?php echo esc_html( $fx_plain( $a['title'] ) ); ?></title>
<link rel="stylesheet" href="<?php echo esc_url( $theme . '/assets/css/fonts.css' ); ?>">
<style>
:root{--bg:#172125;--deep:#10181B;--p1:#1D2A2F;--p2:#25343A;--line:rgba(255,255,255,.10);--green:#48F790;--mint:#48F790;--ink:#EEF3F4;--ink2:#A3B3B8;--on-green:#06140B;--k:1}
*{box-sizing:border-box;margin:0;padding:0}
html,body{width:<?php echo $tall ? 800 : 1600; ?>px;height:<?php echo $tall ? 1140 : 760; ?>px;overflow:hidden}
body{position:relative;background:var(--bg);color:var(--ink);font-family:'Noto Sans Thai',sans-serif;-webkit-font-smoothing:antialiased}
.fit span{display:inline-block}
.wrap{position:absolute;inset:<?php echo $tall ? '38px 40px 36px' : '42px 52px 44px'; ?>;display:flex;flex-direction:column}

/* หัวภาพ = หัวเรื่อง + บรรทัดรอง ไม่มีบรรทัดใดอยู่เหนือหัวเรื่อง · พื้นที่ของป้ายเดิมถูกใช้กับหัวเรื่องและโลโก้ที่ใหญ่ขึ้น
   ภาพแนวนอน: โลโก้อยู่ขวา กึ่งกลางแนวตั้งกับหัวเรื่องและบรรทัดรอง
   ภาพแนวตั้ง: โลโก้ลอยอยู่มุมขวาบนข้างหัวเรื่อง หัวเรื่องจึงเว้นที่ด้านขวาไว้ (max-width ไม่ใช่ padding เพื่อให้สคริปต์วัดความกว้างที่ใช้ได้จริง)
   แล้วตัดเป็นสองบรรทัด ส่วนบรรทัดรองอยู่ใต้โลโก้และใช้ความกว้างได้เต็ม */
.head{position:relative;display:flex;align-items:center;justify-content:space-between;gap:32px}
.head-txt{min-width:0;flex:1 1 auto}
h1{font-size:calc(<?php echo $tall ? 50 : 56; ?>px*var(--k));line-height:1.3;font-weight:700<?php echo $tall ? ';max-width:calc(100% - 150px)' : ''; ?>}
.sub{margin-top:<?php echo $tall ? 8 : 6; ?>px;font-size:calc(<?php echo $tall ? 26 : 29; ?>px*var(--k));line-height:1.48;font-weight:500;color:var(--ink2)}
.mark{flex:0 0 auto;display:block;width:auto;<?php echo $tall ? 'position:absolute;top:8px;right:0;height:60px' : 'height:80px'; ?>}

/* การ์ดต้องไม่ใส่ overflow:hidden (ค่านั้นทำให้การ์ดหดเล็กกว่าข้อความได้ แล้วตัวอักษรจะถูกตัดโดยที่สคริปต์ตรวจไม่เจอ)
   มุมโค้งของแถบเลขในภาพแนวตั้งใช้ overflow:clip แทน ซึ่งไม่ทำให้การ์ดหดต่ำกว่าเนื้อหา */
.steps{flex:1 1 auto;display:flex;align-items:stretch;<?php echo $tall ? 'flex-direction:column;margin-top:24px' : 'margin-top:34px'; ?>}
.step{position:relative;min-width:0;display:flex;background:var(--p1);border:2px solid var(--line);border-radius:18px;box-shadow:0 2px 4px rgba(8,12,14,.16),0 14px 30px rgba(8,12,14,.20);
	<?php echo $tall ? 'flex:1 1 auto;flex-direction:row;align-items:center;gap:22px;padding:16px 24px 16px 22px' : 'flex:1 1 0;flex-direction:column;padding:26px 24px 22px'; ?>}
.step.hl{background:var(--p2);border-color:var(--green)}
<?php if ( $tall ) : ?>
.step{overflow:clip}
.num{align-self:stretch;flex:0 0 66px;margin:-16px 0 -16px -22px;padding-right:16px;display:flex;align-items:center;justify-content:center;background:var(--green);color:var(--on-green);font-size:34px;font-weight:700;line-height:1;clip-path:polygon(0 0,100% 0,calc(100% - 20px) 100%,0 100%)}
.num + .badge{margin-left:-8px}
<?php else : ?>
.num{position:absolute;top:-2px;right:24px;min-width:70px;height:44px;padding:0 16px 0 28px;display:flex;align-items:center;justify-content:center;background:var(--green);color:var(--on-green);font-size:27px;font-weight:700;line-height:1;clip-path:polygon(16px 0,100% 0,100% 100%,0 100%)}
<?php endif; ?>
.badge{flex:0 0 auto;width:<?php echo $tall ? 84 : 92; ?>px;height:<?php echo $tall ? 84 : 92; ?>px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--deep)}
.badge svg{width:<?php echo $tall ? 42 : 46; ?>px;height:<?php echo $tall ? 42 : 46; ?>px;color:var(--mint);stroke-width:1.7}
.step.hl .badge{background:var(--green)}
.step.hl .badge svg{color:var(--on-green)}
.copy{min-width:0;<?php echo $tall ? 'flex:1 1 auto' : 'margin-top:18px;display:flex;flex-direction:column;flex:1 1 auto'; ?>}
.step h2{font-size:calc(<?php echo $tall ? 34 : ( $chain ? 35 : 37 ); ?>px*var(--k));line-height:1.36;font-weight:700;color:var(--ink)}
.step p{margin-top:<?php echo $tall ? 2 : 8; ?>px;font-size:calc(<?php echo $tall ? 26 : 28; ?>px*var(--k));line-height:1.5;color:var(--ink2)}
.chips{display:flex;flex-wrap:wrap;gap:8px;<?php echo $tall ? 'margin-top:8px' : 'margin-top:auto;padding-top:14px'; ?>}
/* ป้ายสั้น = สี่เหลี่ยมมุมมนเล็กน้อย (10px บนผืน 1600px ย่อแล้วเท่ากับราว 6px ของป้ายสถานะบนเว็บ) · ห้ามใช้ทรงแคปซูล 999px */
.chip{padding:4px 14px;border-radius:10px;background:var(--deep);border:2px solid var(--line);font-size:calc(21px*var(--k));line-height:1.5;font-weight:600;color:var(--ink);white-space:nowrap}
.step.hl .chip{background:var(--green);border-color:var(--green);color:var(--on-green)}
.arrow{flex:0 0 auto;display:flex;align-items:center;justify-content:center;color:var(--green);<?php echo $tall ? 'height:' . ( $chain ? 28 : 24 ) . 'px' : 'width:' . ( $chain ? 60 : 44 ) . 'px'; ?>}
.arrow svg{width:<?php echo $tall ? 28 : 34; ?>px;height:<?php echo $tall ? 28 : 34; ?>px;stroke-width:2.8<?php echo $tall ? ';transform:rotate(90deg)' : ''; ?>}
<?php if ( $chain && ! $tall ) : ?>
/* โหมด chain แนวนอน: การ์ดกว้างกว่า ไอคอนกับหัวข้ออยู่แถวเดียวกัน คำอธิบายและป้ายกินเต็มความกว้างการ์ด */
.step{display:grid;grid-template-columns:92px minmax(0,1fr);grid-template-rows:auto auto 1fr;column-gap:18px;align-content:start}
.copy{display:contents}
.step h2{align-self:center}
.step p{grid-column:1/-1;margin-top:16px}
.chips{grid-column:1/-1;align-self:end;margin-top:0}
<?php endif; ?>

.note{flex:0 0 auto;margin-top:<?php echo $tall ? 22 : 26; ?>px;display:flex;align-items:stretch;background:var(--green);color:var(--on-green);border-radius:14px;overflow:hidden}
.note-ic{flex:0 0 <?php echo $tall ? 92 : 104; ?>px;display:flex;align-items:center;justify-content:center;padding-right:18px;background:var(--deep);color:var(--mint);clip-path:polygon(0 0,100% 0,calc(100% - 24px) 100%,0 100%)}
.note-ic svg{width:<?php echo $tall ? 36 : 38; ?>px;height:<?php echo $tall ? 36 : 38; ?>px;stroke-width:1.9}
.note p{flex:1 1 auto;min-width:0;padding:<?php echo $tall ? '16px 22px 16px 6px' : '17px 28px 17px 8px'; ?>;font-size:calc(<?php echo $tall ? 25 : 26; ?>px*var(--k));line-height:1.5;font-weight:600}
</style>
</head>
<body class="<?php echo $tall ? 'is-tall' : 'is-wide'; ?>">
<div class="wrap" id="w">
	<div class="head">
		<div class="head-txt">
			<h1 class="fit"><?php echo $fx_chunks( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></h1>
			<p class="sub fit"><?php echo $fx_chunks( $a['sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></p>
		</div>
		<img class="mark" src="<?php echo esc_url( fenix_wordmark_url( 'light' ) ); ?>" alt="">
	</div>
	<div class="steps">
		<?php foreach ( $a['steps'] as $i => $s ) : ?>
			<?php if ( $i ) : ?>
				<div class="arrow"><?php echo fenix_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG ?></div>
			<?php endif; ?>
			<div class="step<?php echo ! empty( $s[4] ) ? ' hl' : ''; ?>">
				<?php if ( ! $chain ) : ?>
					<span class="num"><?php echo (int) ( $i + 1 ); ?></span>
				<?php endif; ?>
				<span class="badge"><?php echo fenix_icon( $s[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG ?></span>
				<div class="copy">
					<h2 class="fit"><?php echo $fx_chunks( $s[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></h2>
					<p class="fit"><?php echo $fx_chunks( $s[2] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></p>
					<?php if ( ! empty( $s[3] ) ) : ?>
						<div class="chips">
							<?php foreach ( (array) $s[3] as $chip ) : ?>
								<span class="chip"><?php echo esc_html( $chip ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( ! empty( $a['note'] ) ) : ?>
		<div class="note">
			<span class="note-ic"><?php echo fenix_icon( isset( $a['icon'] ) ? $a['icon'] : 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG ?></span>
			<p class="fit"><?php echo $fx_chunks( $a['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></p>
		</div>
	<?php endif; ?>
</div>
<script>
// ย่อตัวอักษรทีละ 2% (ต่ำสุด 80%) จนเนื้อหาไม่ล้นกรอบและไม่มีท่อนข้อความใดกว้างกว่ากล่องของมัน แล้วรายงานผลที่ <html data-fit>
(function(){var r=document.documentElement,w=document.getElementById('w'),k=1;
function over(){if(w.scrollHeight>w.clientHeight+1){return'height';}var s=document.querySelectorAll('.fit span'),c=document.querySelectorAll('.chips'),i;
for(i=0;i<s.length;i++){if(s[i].offsetWidth>s[i].parentNode.clientWidth+1){return'width';}}
for(i=0;i<c.length;i++){if(c[i].scrollWidth>c[i].clientWidth+1){return'chips';}}return'';}
function fit(){var o=over(),why=o;while(o&&k>.8){k=Math.round((k-.02)*100)/100;r.style.setProperty('--k',k);o=over();}r.setAttribute('data-fit',o?'overflow-'+o:'ok');r.setAttribute('data-k',k);if(why){r.setAttribute('data-shrunk',why);}}
if(document.fonts&&document.fonts.ready){document.fonts.ready.then(fit);}else{fit();}})();
</script>
</body>
</html>

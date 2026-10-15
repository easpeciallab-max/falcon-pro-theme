<?php
/**
 * Build the DRAWN step pictures of the guide pages into falcon-pro/assets/img/guides/
 * (file name: falcon-pro-ea-<name>-v<N>.webp, 1320×1080):
 *   vps-ios-credentials   /vps-ios/ step 5 · the Add PC form with the Credentials row marked
 *   vps-ios-save          /vps-ios/ step 6 · the same form filled in, with the Save button marked
 *
 * Needs Google Chrome and the GD extension. No preview server: the page is built here and opened from a temp file.
 *   php -d extension=mbstring -d extension=gd dev/make-guide-mocks.php [name ...]
 * Without arguments every picture in $mocks is rendered.
 *
 * Why these two are drawn: no real capture of these screens exists yet. They are "ภาพจำลอง" (illustrations), and the
 * picture itself, its alt text (step data in falcon-pro/inc/modules/guides.php) and dev/image-shot-list.md all say so.
 * The owner replaces them with a real capture by uploading one in the Customizer.
 *
 * กติกาของภาพจำลอง (ต้องผ่านทุกข้อ):
 * - ป้ายและชื่อช่องทุกคำต้องมาจากภาพถ่ายจริงของหน้าเดียวกันเท่านั้น (falcon-pro-ea-vps-ios-add-pc-v1.webp):
 *   Add PC, Save, PC Name, Credentials, Ask When Required, General, Friendly Name, Admin Mode, Swap Mouse Buttons,
 *   Gateway, No Gateway Configured · ห้ามเดาหน้าจอที่ไม่มีภาพจริงยืนยัน (จึงไม่มีภาพจำลองของแอปบน Android)
 * - ค่าตัวอย่าง: IP ใช้ช่วงสำหรับเอกสาร 203.0.113.10 เท่านั้น · Friendly Name เป็นชื่อตัวอย่างของ FALCON · ไม่มีรหัสผ่าน ไม่มีชื่อผู้ใช้จริง
 * - ลุค FALCON: พื้น #172125 แผง #1D2A2F แถว #25343A เส้น #34474E ตัวอักษร #EEF3F4 / #A3B3B8
 *   กรอบเน้นเป็นเส้นทึบ #48F790 · ไม่มี gradient ไม่มีเงาเรืองแสง · ฟอนต์ Noto Sans Thai จาก falcon-pro/assets/fonts
 * - ทุกภาพมีแถบ "ภาพจำลอง" ด้านล่าง · ไม่มีชื่อผู้ให้บริการ VPS ชื่อโบรกเกอร์ หรือแบรนด์อื่นในภาพ
 * - ข้อความถูกอบลงในภาพ: แก้เมื่อไหร่ให้เพิ่มเลข 'v' ของภาพนั้น เรนเดอร์ใหม่ แล้วแก้ชื่อไฟล์ในข้อมูลขั้นตอน
 *   (องค์ประกอบที่ 5 ของขั้นนั้นใน fenix_guide_data()) และใน dev/image-shot-list.md ให้ตรงกัน เพราะแคชเก็บรูปตาม URL
 * - ห้ามใส่ letter-spacing กับข้อความไทย
 *
 * A line marked "!" means a label did not fit its row or the font did not load: fix the text and render again.
 * A page that did not render as the dark card (wrong corner colour) is never saved.
 */

$root   = dirname( __DIR__ );
$outdir = $root . '/falcon-pro/assets/img/guides';
$fonts  = $root . '/falcon-pro/assets/fonts';
$width  = 1320;
$height = 1080;

/*
 * โครงข้อมูลของภาพ 1 ภาพ:
 *   'v'     เลขรุ่นของไฟล์
 *   'mark'  จุดที่ตีกรอบสีเขียว: 'credentials' (แถว Credentials) หรือ 'save' (ปุ่ม Save)
 *   'save'  true = ปุ่ม Save สว่าง (กรอกครบแล้ว) · false = จาง (เหมือนภาพจริงตอนยังไม่กรอก PC Name)
 *   'note'  ข้อความในแถบ "ภาพจำลอง" ด้านล่าง (ไทย เขียนใหม่ของ FALCON)
 * ค่าในแถว (IP ตัวอย่าง ชื่อตัวอย่าง) ใช้ร่วมกันทั้งสองภาพ จึงอยู่ใน $form ด้านล่าง
 */
$mocks = array(
	'vps-ios-credentials' => array(
		'v'    => 1,
		'mark' => 'credentials',
		'save' => false,
		'note' => 'วาดตามหน้า Add PC ของ Windows App บน iPhone หน้าจอจริงอาจต่างออกไป',
	),
	'vps-ios-save'        => array(
		'v'    => 1,
		'mark' => 'save',
		'save' => true,
		'note' => 'วาดตามหน้า Add PC ของ Windows App บน iPhone หน้าจอจริงอาจต่างออกไป',
	),
);

/* แถวของฟอร์ม เรียงตามภาพถ่ายจริง · array( กลุ่ม, ป้าย, ค่า หรือ 'toggle', คีย์สำหรับตีกรอบ ) */
$form = array(
	array( '', 'PC Name', '203.0.113.10', 'pcname' ),
	array( '', 'Credentials', 'Ask When Required', 'credentials' ),
	array( 'General', 'Friendly Name', 'FALCON VPS', 'friendly' ),
	array( 'General', 'Admin Mode', 'toggle', '' ),
	array( 'General', 'Swap Mouse Buttons', 'toggle', '' ),
	array( 'Gateway', 'No Gateway Configured', '', '' ),
);

$chrome = getenv( 'CHROME_BIN' );
if ( ! $chrome ) {
	foreach ( array( 'C:/Program Files/Google/Chrome/Application/chrome.exe', 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe', '/usr/bin/google-chrome', '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome' ) as $c ) {
		if ( file_exists( $c ) ) {
			$chrome = $c;
			break;
		}
	}
}
if ( ! $chrome ) {
	fwrite( STDERR, "Chrome not found. Set CHROME_BIN.\n" );
	exit( 1 );
}
if ( ! function_exists( 'imagewebp' ) ) {
	fwrite( STDERR, "GD with WebP support is required (-d extension=gd).\n" );
	exit( 1 );
}

/* ฟอนต์ของธีมฝังเป็น data URI: หน้า file:// โหลดฟอนต์ได้แน่นอน ไม่ขึ้นกับนโยบายไฟล์ของเบราว์เซอร์ */
$faces = '';
foreach (
	array(
		'noto-sans-thai-thai.woff2'  => 'U+02D7, U+0303, U+0331, U+0E01-0E5B, U+200C-200D, U+25CC',
		'noto-sans-thai-latin.woff2' => 'U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD',
	) as $file => $range
) {
	$bin = @file_get_contents( $fonts . '/' . $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $bin ) {
		fwrite( STDERR, "Font file missing: falcon-pro/assets/fonts/$file\n" );
		exit( 1 );
	}
	$faces .= '@font-face{font-family:"Noto Sans Thai";font-style:normal;font-weight:300 800;src:url(data:font/woff2;base64,' . base64_encode( $bin ) . ') format("woff2");unicode-range:' . $range . ';}';
}

/**
 * HTML ของภาพ 1 ภาพ (หน้าเดี่ยว ไม่มีไฟล์ภายนอก)
 */
function fx_mock_html( array $mock, array $form, $faces, $width, $height ) {
	$e    = function ( $s ) {
		return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' );
	};
	$rows = '';
	$open = null;
	foreach ( $form as $row ) {
		list( $group, $label, $value, $key ) = $row;
		if ( $group !== $open ) {
			if ( null !== $open ) {
				$rows .= '</div>';
			}
			if ( '' !== $group ) {
				$rows .= '<p class="grp">' . $e( $group ) . '</p>';
			}
			$rows .= '<div class="set">';
			$open  = $group;
		}
		$marked = '' !== $key && $key === $mock['mark'];
		$rows  .= '<div class="row' . ( $marked ? ' is-marked' : '' ) . '"><span class="lab">' . $e( $label ) . '</span>';
		if ( 'toggle' === $value ) {
			$rows .= '<span class="tog"><i></i></span>';
		} else {
			$rows .= '<span class="val">' . $e( $value ) . '</span><span class="chev"></span>';
		}
		$rows .= '</div>';
	}
	$rows .= '</div>';

	$save = 'btn-save' . ( $mock['save'] ? ' is-on' : '' ) . ( 'save' === $mock['mark'] ? ' is-marked' : '' );

	return '<!doctype html><html lang="th"><head><meta charset="utf-8"><style>' . $faces . '
*{box-sizing:border-box;margin:0;padding:0}
html,body{width:' . (int) $width . 'px;height:' . (int) $height . 'px;overflow:hidden;background:#172125}
body{font-family:"Noto Sans Thai",sans-serif;color:#EEF3F4;-webkit-font-smoothing:antialiased}
.panel{position:absolute;left:48px;top:44px;width:1224px;height:900px;background:#1D2A2F;border:2px solid #34474E;border-radius:30px;padding:36px 40px}
.head{position:relative;height:84px;display:flex;align-items:center;justify-content:space-between}
.x{width:76px;height:76px;border-radius:50%;background:#25343A;border:2px solid #34474E;position:relative}
.x:before,.x:after{content:"";position:absolute;left:21px;top:35px;width:30px;height:5px;border-radius:3px;background:#EEF3F4}
.x:before{transform:rotate(45deg)}.x:after{transform:rotate(-45deg)}
.ttl{position:absolute;left:0;right:0;text-align:center;font-size:42px;font-weight:700;line-height:84px;pointer-events:none}
.btn-save{min-width:176px;height:76px;padding:0 34px;border-radius:38px;background:#25343A;border:2px solid #34474E;font-size:36px;font-weight:600;line-height:72px;text-align:center;color:#6F8188}
.btn-save.is-on{color:#EEF3F4}
.btn-save.is-marked{border:5px solid #48F790;line-height:66px}
.grp{margin:26px 0 12px 8px;font-size:28px;font-weight:700;line-height:38px;letter-spacing:.04em;text-transform:uppercase;color:#A3B3B8}
.set{background:#25343A;border-radius:26px;margin-top:24px}
.grp + .set{margin-top:0}
.row{position:relative;height:88px;display:flex;align-items:center;padding:0 34px;white-space:nowrap}
.row + .row:before{content:"";position:absolute;left:34px;right:34px;top:0;height:2px;background:#34474E}
.lab{font-size:36px;font-weight:600;flex:0 0 auto}
.val{margin-left:auto;font-size:36px;font-weight:400;color:#A3B3B8;padding-left:24px}
.chev{flex:0 0 auto;width:18px;height:18px;margin-left:22px;border-top:5px solid #A3B3B8;border-right:5px solid #A3B3B8;transform:rotate(45deg)}
.tog{margin-left:auto;width:96px;height:54px;border-radius:27px;background:#34474E;position:relative}
.tog i{position:absolute;left:5px;top:5px;width:44px;height:44px;border-radius:50%;background:#EEF3F4}
.row.is-marked{border:5px solid #48F790;border-radius:22px;padding:0 29px;background:#1D2A2F}
.row.is-marked:before,.row.is-marked + .row:before{display:none}
.strip{position:absolute;left:48px;right:48px;top:968px;height:68px;display:flex;align-items:center;white-space:nowrap}
.tag{flex:0 0 auto;height:58px;padding:0 26px;border-radius:29px;background:#48F790;color:#06140B;font-size:34px;font-weight:700;line-height:58px}
.note{margin-left:22px;font-size:30px;font-weight:400;line-height:68px;color:#A3B3B8}
</style></head><body>
<div class="panel">
<div class="head"><span class="x"></span><span class="ttl">Add PC</span><span class="' . $save . '">Save</span></div>
' . $rows . '
</div>
<div class="strip"><span class="tag">ภาพจำลอง</span><span class="note">' . $e( $mock['note'] ) . '</span></div>
<script>
(function(){
	function done(){
		var bad = false, i, els = document.querySelectorAll(".row,.strip,.head");
		for ( i = 0; i < els.length; i++ ) { if ( els[i].scrollWidth > els[i].clientWidth + 1 ) { bad = true; } }
		var p = document.querySelector(".panel");
		if ( p.scrollHeight > p.clientHeight + 1 ) { bad = true; }
		var font = document.fonts && document.fonts.check("600 36px \"Noto Sans Thai\"", "Add PC ภาพจำลอง");
		document.documentElement.setAttribute("data-fit", bad ? "overflow" : "ok");
		document.documentElement.setAttribute("data-font", font ? "ok" : "missing");
	}
	if ( document.fonts && document.fonts.ready ) { document.fonts.ready.then(done); } else { done(); }
})();
</script>
</body></html>';
}

$names = array_slice( $argv, 1 );
if ( ! $names ) {
	$names = array_keys( $mocks );
}
if ( ! is_dir( $outdir ) ) {
	mkdir( $outdir, 0755, true );
}
$tmp  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'falcon-guide-mocks';
$prof = $tmp . DIRECTORY_SEPARATOR . 'profile';
if ( ! is_dir( $prof ) ) {
	mkdir( $prof, 0755, true );
}

$fail = 0;
foreach ( $names as $name ) {
	if ( ! isset( $mocks[ $name ] ) ) {
		echo "✗ $name · not in \$mocks (dev/make-guide-mocks.php)\n";
		$fail++;
		continue;
	}
	$page = $tmp . DIRECTORY_SEPARATOR . $name . '.html';
	file_put_contents( $page, fx_mock_html( $mocks[ $name ], $form, $faces, $width, $height ) );
	$url   = 'file:///' . str_replace( ' ', '%20', ltrim( str_replace( '\\', '/', $page ), '/' ) );
	$flags = '"' . $chrome . '" --headless=new --disable-gpu --hide-scrollbars --force-device-scale-factor=1'
		. ' --window-size=' . $width . ',' . $height . ' --virtual-time-budget=4000 --user-data-dir="' . $prof . '"';

	// 1) ผลตรวจข้อความและฟอนต์ที่สคริปต์ของหน้าเขียนไว้ใน <html data-fit data-font>
	$dom = array();
	exec( $flags . ' --dump-dom "' . $url . '" 2>&1', $dom, $rc );
	$dom  = implode( "\n", $dom );
	$fit  = preg_match( '/<html[^>]*\sdata-fit="([^"]*)"/', $dom, $m ) ? $m[1] : 'unknown';
	$font = preg_match( '/<html[^>]*\sdata-font="([^"]*)"/', $dom, $m ) ? $m[1] : 'unknown';

	// 2) ภาพ
	$png = $tmp . DIRECTORY_SEPARATOR . $name . '.png';
	@unlink( $png ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	$o = array();
	exec( $flags . ' --screenshot="' . $png . '" "' . $url . '" 2>&1', $o, $rc );
	if ( ! file_exists( $png ) ) {
		echo "✗ $name · screenshot failed\n";
		$fail++;
		continue;
	}
	$im = imagecreatefrompng( $png );
	// มุมซ้ายบนของภาพเป็นพื้น #172125 เสมอ · สีอื่น หรือไม่มี data-fit = หน้าไม่ได้เรนเดอร์เป็นภาพจำลอง ไม่บันทึก
	$rgb = imagecolorat( $im, 3, 3 );
	if ( 'unknown' === $fit || abs( ( ( $rgb >> 16 ) & 0xFF ) - 0x17 ) > 6 || abs( ( ( $rgb >> 8 ) & 0xFF ) - 0x21 ) > 6 || abs( ( $rgb & 0xFF ) - 0x25 ) > 6 ) {
		echo "✗ $name · the page did not render as a mock-up card · open $page in a browser\n";
		$fail++;
		continue;
	}
	if ( $width !== imagesx( $im ) || $height !== imagesy( $im ) ) {
		$box = imagecreatetruecolor( $width, $height );
		imagefill( $box, 0, 0, imagecolorallocate( $box, 0x17, 0x21, 0x25 ) );
		imagecopy( $box, $im, 0, 0, 0, 0, min( $width, imagesx( $im ) ), min( $height, imagesy( $im ) ) );
		$im = $box;
	}
	imagepalettetotruecolor( $im );
	$out = $outdir . '/falcon-pro-ea-' . $name . '-v' . (int) $mocks[ $name ]['v'] . '.webp';
	imagewebp( $im, $out, 90 );
	$ok = 'ok' === $fit && 'ok' === $font;
	if ( ! $ok ) {
		$fail++;
	}
	printf( "%s %s (%dx%d, %d KB) · text %s, font %s\n", $ok ? '✓' : '!', basename( $out ), imagesx( $im ), imagesy( $im ), (int) round( filesize( $out ) / 1024 ), $fit, $font );
}
exit( $fail ? 1 : 0 );

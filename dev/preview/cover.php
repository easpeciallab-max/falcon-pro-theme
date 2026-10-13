<?php
/**
 * Cover card (1200×630), one template for two kinds of cover:
 * - article · preview route /__cover/<article-slug>/     → falcon-pro/assets/img/covers/<slug>.webp
 * - page    · preview route /__cover/page-<page-slug>/   → falcon-pro/assets/img/covers/page-<slug>.webp
 *   (FALCON Setup ปุ่ม "4) SEO และระบบ" ตั้งไฟล์ page-<slug>.webp เป็นภาพเด่นของเพจ = og:image ของหน้านั้น · fenix_setup_page_covers())
 * /__cover/pages/ prints the page-cover names, one per line (used by "dev/make-covers.php pages").
 * Rendered by headless Chrome in dev/make-covers.php.
 * Text only: no numbers, no results. Not part of the theme.
 * The two kinds differ in one thing: an article cover keeps the small category line above its title (unchanged),
 * a page cover has no line above its title (owner, 12 Oct 2026: a heading stands alone). On a page cover the freed row goes
 * to larger type: the title starts at 64px instead of 60px and its block may be 420px high instead of 390px. The block keeps
 * its place in the upper part of the space above the wordmark, so no empty row is left where the line was.
 *
 * @var string $fx_cover_slug set by router.php
 */

/*
 * ปกของเพจ: slug => array( 'หัวเรื่อง: คำขยาย', ไอคอนจาก fenix_icon(), สีแผง green | white | dark )
 * - หัวเรื่องบอกว่าหน้านี้ใช้ทำอะไร เขียนเป็นของ FALCON เอง · ห้ามมีตัวเลขผลเทรด ราคา ชื่อแพ็กเกจ หรือชื่อโบรกเกอร์
 * - ปกของเพจไม่มีป้ายหรือบรรทัดเล็กเหนือหัวเรื่อง (เจ้าของสั่ง 12 ต.ค. 2026) มีแค่หัวเรื่อง บรรทัดรอง โลโก้ และไอคอน · อย่าเพิ่มกลับ
 * - ไม่ทำปกให้ home, go, articles และเพจกฎหมาย (หน้าเหล่านั้นใช้การ์ดแชร์กลางของเว็บ)
 * - แก้ข้อความแล้วต้องเรนเดอร์ใหม่ (dev/make-covers.php page-<slug>) · ถ้าปกเดิมถูกนำเข้าคลังสื่อของเว็บจริงไปแล้ว ปุ่ม Setup จะไม่นำเข้าซ้ำ
 *   และไม่ทับภาพเด่นที่มีอยู่: ให้ลบไฟล์ปกเดิมของเพจนั้นออกจากคลังสื่อ (สื่อ → ลบถาวร) แล้วกดปุ่ม "4) SEO และระบบ" อีกครั้ง
 */
$page_covers = array(
	'backtest'         => array( 'Backtest EA บน MT5: ตั้ง Strategy Tester และตรวจรายงานทีละค่า', 'candles', 'white' ),
	'forward-test'     => array( 'Forward Test EA บนตลาดจริง: เลือกบัญชี ระยะเวลา และวิธีอ่านผล', 'pulse', 'white' ),
	'how-to-install'   => array( 'วิธีติดตั้ง EA ใน MT5: วางไฟล์ เปิด Algo Trading และตรวจการทำงาน', 'download', 'green' ),
	'open-mt5-account' => array( 'เปิดบัญชีเทรด MT5: สมัคร ยืนยันตัวตน จนได้เลขบัญชี', 'user', 'green' ),
	'deposit'          => array( 'ฝากเงินเข้าบัญชีเทรด MT5: ทำรายการผ่านหน้าสมาชิกของโบรกเกอร์', 'dollar', 'green' ),
	'mt5-login'        => array( 'ติดตั้งและล็อกอิน MT5: บน Windows, Android และ iPhone', 'monitor', 'green' ),
	'vps-windows'      => array( 'Windows VPS สำหรับรัน EA: เชื่อมต่อด้วย Remote Desktop ทีละขั้น', 'windows', 'dark' ),
	'vps-android'      => array( 'VPS บนมือถือ Android: เข้า Windows VPS ด้วย Windows App', 'android', 'dark' ),
	'vps-ios'          => array( 'VPS บน iPhone: เชื่อมต่อ Windows VPS จาก iPhone หรือ iPad ด้วย Windows App', 'apple', 'dark' ),
	'tools'            => array( 'เครื่องมือคำนวณ Lot และ Drawdown: วางแผนความเสี่ยงก่อนตั้งค่า EA', 'calc', 'white' ),
	'dashboard'        => array( 'แดชบอร์ด FALCON PRO: ให้ EA ทำงานบนเซิร์ฟเวอร์ ติดตามบัญชี MT5 จากเว็บ', 'layout', 'green' ),
	'pricing'          => array( 'แพ็กเกจและราคา: สิ่งที่ได้รับ และขั้นตอนเริ่มใช้งาน', 'tag', 'green' ),
	'risk-disclosure'  => array( 'ประกาศความเสี่ยง: เรื่องที่ต้องรู้ก่อนเทรดด้วยเงินจริง', 'shield', 'white' ),
	'about'            => array( 'เกี่ยวกับเรา: หลักที่เรายึด และเส้นที่เราไม่ข้าม', 'users', 'dark' ),
);

/* รายชื่อปกของเพจ สำหรับ dev/make-covers.php pages */
if ( 'pages' === $fx_cover_slug ) {
	header( 'Content-Type: text/plain; charset=utf-8' );
	foreach ( array_keys( $page_covers ) as $pslug ) {
		echo 'page-' . $pslug . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed keys of the array above
	}
	return;
}

$is_page = 0 === strpos( $fx_cover_slug, 'page-' );
if ( $is_page ) {
	$pslug = substr( $fx_cover_slug, 5 );
	if ( ! isset( $page_covers[ $pslug ] ) ) {
		http_response_code( 404 );
		echo 'No page cover: ' . htmlspecialchars( $pslug );
		return;
	}
	/* กันพิมพ์ slug ผิด: ต้องเป็นแถวใน manifest ของเว็บ (รวมแถวที่ยังรอไฟล์เนื้อหา) ไม่งั้น Setup จะหาเพจไม่เจอ */
	if ( function_exists( 'fenix_site_pages' ) && ! array_key_exists( $pslug, fenix_site_pages( true ) ) ) {
		http_response_code( 404 );
		echo 'Page cover for a slug that is not in fenix_site_pages(): ' . htmlspecialchars( $pslug );
		return;
	}
	$art = array(
		'title' => $page_covers[ $pslug ][0],
		'meta'  => array(),
	);
} else {
	$arts = fenix_seed_articles();
	if ( ! isset( $arts[ $fx_cover_slug ] ) ) {
		http_response_code( 404 );
		echo 'No article: ' . htmlspecialchars( $fx_cover_slug );
		return;
	}
	$art = $arts[ $fx_cover_slug ];
}

// slug => icon name from fenix_icon() · default 'book'
$icons = array(
	'what-is-ea-mt5'              => 'robot',
	'ea-gold-xauusd'              => 'candles',
	'vps-for-ea'                  => 'server',
	'drawdown'                    => 'chart',
	'lot-size-calculation'        => 'calc',
	'free-ea-vs-paid'             => 'tag',
	'spread-slippage'             => 'pulse',
	'margin-call-stop-out'        => 'warn',
	'choose-broker-for-ea'        => 'shield',
	'ea-scam-warning'             => 'lock',
	'mt4-vs-mt5'                  => 'monitor',
	'demo-account-ea'             => 'flask',
	'cent-account'                => 'dollar',
	'grid-martingale-ea'          => 'layout',
	'profit-factor'               => 'bars',
	'ea-not-trading'              => 'terminal',
	'leverage'                    => 'gauge',
	'swap-commission'             => 'moon',
	'ea-news-trading'             => 'bolt',
	'vps-mt5-stable'              => 'windows',
	'ea-optimization-overfitting' => 'target',
	'ea-monitoring-routine'       => 'clock',
);
// category => English kicker + panel variant
$cats = array(
	'พื้นฐาน EA'      => array( 'EA Basics', 'green' ),
	'บริหารความเสี่ยง' => array( 'Risk Management', 'white' ),
	'MT5 & VPS'       => array( 'MT5 & VPS', 'dark' ),
);
$cat     = isset( $art['meta']['category'] ) ? $art['meta']['category'] : '';
$kicker  = isset( $cats[ $cat ] ) ? $cats[ $cat ][0] : 'Article';
$variant = isset( $cats[ $cat ] ) ? $cats[ $cat ][1] : 'green';
$icon    = isset( $icons[ $fx_cover_slug ] ) ? $icons[ $fx_cover_slug ] : 'book';
if ( $is_page ) {
	$kicker = ''; // ปกของเพจไม่มีบรรทัดเหนือหัวเรื่อง
	list( , $icon, $variant ) = $page_covers[ $pslug ];
}
$theme   = get_template_directory_uri();

// "หัวเรื่อง: คำขยาย" → บรรทัดใหญ่ + บรรทัดรอง · แต่ละวลี (คั่นด้วยช่องว่าง) ไม่ถูกตัดกลางคำ
$parts = array_map( 'trim', explode( ':', $art['title'], 2 ) );
$chunk = function ( $text ) {
	$out   = array();
	$words = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
	$n     = count( $words );
	// คำสั้นท้ายบรรทัด (เช่น "EA") เกาะกับวลีก่อนหน้า ไม่ตกไปอยู่บรรทัดเดียว
	if ( $n > 1 && mb_strlen( $words[ $n - 1 ] ) <= 3 ) {
		$words[ $n - 2 ] .= "\u{00A0}" . array_pop( $words );
	}
	foreach ( $words as $w ) {
		$out[] = '<span>' . esc_html( $w ) . '</span>';
	}
	return implode( ' ', $out );
};
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title><?php echo esc_html( $art['title'] ); ?></title>
<link rel="stylesheet" href="<?php echo esc_url( $theme . '/assets/css/fonts.css' ); ?>">
<style>
*{box-sizing:border-box;margin:0;padding:0}
html,body{width:1200px;height:630px;overflow:hidden}
body{background:#172125;color:#fff;font-family:'Noto Sans Thai',sans-serif;position:relative}
.panel{position:absolute;top:0;right:0;width:470px;height:630px;clip-path:polygon(150px 0,100% 0,100% 100%,0 100%)}
.stripe{position:absolute;top:0;right:430px;width:190px;height:630px;background:#1D2A2F;clip-path:polygon(150px 0,190px 0,40px 100%,0 100%)}
.v-green .panel{background:#22C55E}
.v-white .panel{background:#FFFFFF}
.v-dark .panel{background:#25343A}
.badge{position:absolute;top:50%;right:95px;width:230px;height:230px;margin-top:-115px;border-radius:50%;background:#10181B;display:flex;align-items:center;justify-content:center}
.v-dark .badge{background:#22C55E}
.badge svg{width:112px;height:112px;color:#4AF28E;stroke-width:1.6}
.v-dark .badge svg{color:#06140B}
.txt{position:absolute;left:72px;top:64px;width:640px;height:502px;display:flex;flex-direction:column}
.kicker{font-size:21px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#4AF28E;display:flex;align-items:center;gap:14px}
.kicker:before{content:"";width:44px;height:4px;background:#22C55E;border-radius:2px}
.ttl{margin:auto 0;padding:18px 0 26px}
h1{font-size:60px;line-height:1.3;font-weight:700}
.sub{margin-top:.35em;font-size:36px;line-height:1.4;font-weight:500;color:#A3B3B8}
.ttl span{display:inline-block}
.foot{margin-top:auto;display:flex;align-items:center;gap:18px}
.foot img{height:62px;width:auto;display:block}
</style>
</head>
<?php
/* ปกของเพจไม่มีบรรทัดเหนือหัวเรื่อง: แถวที่ว่างลงถูกใช้ไปกับตัวอักษรที่ใหญ่ขึ้น (เริ่ม 64px แทน 60px บรรทัดรอง 38px) และบล็อกหัวเรื่องที่สูงได้ถึง 420px
 * บล็อกหัวเรื่องยังวางในช่วงบนของพื้นที่เหนือโลโก้เหมือนเดิม (ช่องว่างแบ่งสามส่วน: บนหัวเรื่อง 1 ใต้หัวเรื่อง 2) จึงไม่เหลือแถบว่างตรงที่เคยมีป้าย
 * 64px เป็นขนาดใหญ่สุดที่หัวเรื่องและบรรทัดรองของปกทั้ง 14 ใบยังตัดบรรทัดสวย (66px ทำให้บรรทัดรองของบางใบเหลือคำเดียวตกบรรทัด)
 * ปกบทความใช้ค่าเดิม (60px, 390px) */
$fit_size = $is_page ? 64 : 60;
$fit_max  = $is_page ? 420 : 390;
?>
<body class="v-<?php echo esc_attr( $variant ); ?><?php echo $is_page ? ' is-page' : ''; ?>" data-start="<?php echo (int) $fit_size; ?>" data-max="<?php echo (int) $fit_max; ?>">
	<div class="stripe"></div>
	<div class="panel"></div>
	<div class="badge"><?php echo fenix_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG ?></div>
	<div class="txt">
		<?php if ( '' !== $kicker ) : ?>
			<p class="kicker"><?php echo esc_html( $kicker ); ?></p>
		<?php endif; ?>
		<div class="ttl" id="t">
			<h1><?php echo $chunk( $parts[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></h1>
			<?php if ( ! empty( $parts[1] ) ) : ?>
				<p class="sub"><?php echo $chunk( $parts[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped per chunk ?></p>
			<?php endif; ?>
		</div>
		<div class="foot"><img src="<?php echo esc_url( fenix_wordmark_url( 'light' ) ); ?>" alt=""></div>
	</div>
<script>
// Shrink the title until no phrase wraps mid-word (down to 46px) and the block fits above the wordmark.
// Start size and height limit come from <body data-start data-max>: 60px / 390px on an article cover, 64px / 420px on a page cover (no line above the title).
// The result is written to <html data-fit="ok|overflow" data-size="…"> so dev/make-covers.php can warn about a title that is still too long.
(function(){var t=document.getElementById('t'),h=t.querySelector('h1'),p=t.querySelector('.sub'),b=document.body,s=parseInt(b.getAttribute('data-start'),10)||60,max=parseInt(b.getAttribute('data-max'),10)||390;function set(){h.style.fontSize=s+'px';if(p){p.style.fontSize=Math.round(s*.6)+'px';}}
function split(){var a=h.querySelectorAll('span');for(var i=0;i<a.length;i++){if(a[i].offsetHeight>s*1.9){return true;}}return false;}
function wide(){var a=t.querySelectorAll('span');for(var i=0;i<a.length;i++){if(a[i].offsetWidth>t.clientWidth+1){return true;}}return false;}
function fit(){set();while(split()&&s>46){s-=2;set();}while(t.offsetHeight>max&&s>36){s-=2;set();}
var r=document.documentElement;r.setAttribute('data-fit',(split()||wide()||t.offsetHeight>max)?'overflow':'ok');r.setAttribute('data-size',s);}
if(document.fonts&&document.fonts.ready){document.fonts.ready.then(fit);}fit();})();
</script>
</body>
</html>

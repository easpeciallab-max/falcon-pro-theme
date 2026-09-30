<?php
/**
 * Article cover card (1200×630) · preview route /__cover/<article-slug>/
 * Rendered by headless Chrome in dev/make-covers.php and saved to falcon-pro/assets/img/covers/<slug>.webp
 * Text only: no numbers, no results. Not part of the theme.
 *
 * @var string $fx_cover_slug set by router.php
 */

$arts = fenix_seed_articles();
if ( ! isset( $arts[ $fx_cover_slug ] ) ) {
	http_response_code( 404 );
	echo 'No article: ' . htmlspecialchars( $fx_cover_slug );
	return;
}
$art = $arts[ $fx_cover_slug ];

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
<body class="v-<?php echo esc_attr( $variant ); ?>">
	<div class="stripe"></div>
	<div class="panel"></div>
	<div class="badge"><?php echo fenix_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG ?></div>
	<div class="txt">
		<p class="kicker"><?php echo esc_html( $kicker ); ?></p>
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
(function(){var t=document.getElementById('t'),h=t.querySelector('h1'),p=t.querySelector('.sub'),s=60;function set(){h.style.fontSize=s+'px';if(p){p.style.fontSize=Math.round(s*.6)+'px';}}
function split(){var a=h.querySelectorAll('span');for(var i=0;i<a.length;i++){if(a[i].offsetHeight>s*1.9){return true;}}return false;}
function fit(){while(split()&&s>46){s-=2;set();}while(t.offsetHeight>390&&s>36){s-=2;set();}}
if(document.fonts&&document.fonts.ready){document.fonts.ready.then(fit);}fit();})();
</script>
</body>
</html>

<?php
/**
 * FALCON PRO EA · โครงสร้างเพจ + ตัวช่วยตั้งค่าเว็บ (รูปแบบ → FALCON Setup)
 *
 * - fenix_site_pages()     : รายการเพจทั้งหมดของเว็บ (slug → template, เนื้อหาเริ่มต้น, กลุ่มเมนู)
 * - fenix_seed_articles()  : บทความ SEO เริ่มต้น (นำเข้าเป็นฉบับร่าง)
 * - หน้า admin             : สร้างเพจที่ยังไม่มี, ตั้งหน้าแรก/หน้าบทความ, สร้างเมนู, นำเข้าบทความ, ปล่อยบทความ,
 *                            ตั้งค่า SEO และระบบ (fenix_setup_seo: Yoast + ค่าเริ่มต้นของ WordPress + ภาพปกของเพจเป็นภาพเด่น)
 *
 * เนื้อหาเริ่มต้นอยู่ใน inc/content/pages/*.html และ inc/content/articles/*.html
 * บรรทัดแรกของไฟล์เป็น <!--meta {...json...} --> (title, seo_title, description, keyword, excerpt, category, kicker)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * รายการเพจของเว็บ (ลำดับ = ลำดับที่แสดงในหน้า Setup)
 * group: test | guide | pricing | doc | hub
 * status: publish | draft (เพจกฎหมายสร้างเป็นร่างให้เจ้าของตรวจก่อนเผยแพร่)
 * order: ลำดับเพจในหลังบ้าน (menu_order) = ตำแหน่งในรายการนี้ × 10 · เติมให้อัตโนมัติ ไม่ต้องพิมพ์เอง
 *
 * แถวที่ระบุไฟล์เนื้อหา (content) แต่ธีมยังไม่มีไฟล์นั้น = เพจที่เตรียมไว้ล่วงหน้า ยังไม่นับเป็นเพจของเว็บ:
 * ไม่ถูกสร้าง ไม่ขึ้นเมนู/footer/ดัชนีคู่มือ และไม่มีเส้นทางในพรีวิว จนกว่าไฟล์จะมา (กันเพจว่างและลิงก์ 404)
 * ส่ง $include_pending = true เพื่อดูทุกแถว (หน้า Setup ใช้แจ้งว่ามีแถวไหนยังรอไฟล์)
 */
function fenix_site_pages( $include_pending = false ) {
	static $all   = null;
	static $ready = null;
	if ( null === $all ) {
		$all   = fenix_site_pages_manifest();
		$ready = array();
		$order = 0;
		foreach ( $all as $slug => $page ) {
			$order                += 10;
			$all[ $slug ]['order'] = $order;
			if ( ! empty( $page['content'] ) && ! file_exists( get_template_directory() . '/inc/content/' . $page['content'] . '.html' ) ) {
				continue;
			}
			$ready[ $slug ] = $all[ $slug ];
		}
	}
	return $include_pending ? $all : $ready;
}

/**
 * ตัว manifest ดิบ (แก้รายการเพจที่นี่) · โค้ดอื่นเรียกผ่าน fenix_site_pages() เสมอ
 */
function fenix_site_pages_manifest() {
	return array(
		'home'             => array( 'title' => 'หน้าแรก', 'template' => '', 'content' => '', 'group' => 'hub', 'front' => true, 'seo' => array( 'seo_title' => 'FALCON PRO EA · EA MT5 ผู้ช่วยเทรดอัตโนมัติบน MetaTrader 5', 'description' => 'FALCON PRO EA คือ Expert Advisor สำหรับ MetaTrader 5 ที่เทรดทองคำ XAUUSD ตามชุด EA ที่คุณเลือก ติดตั้งเองหรือรันผ่านแดชบอร์ดได้ พร้อมคู่มือภาษาไทยและทีมงานทาง LINE', 'keyword' => 'EA MT5' ) ),
		'backtest'         => array( 'title' => 'Backtest', 'template' => 'template-backtest.php', 'content' => 'pages/backtest', 'group' => 'test', 'menu' => 'Backtest' ),
		'forward-test'     => array( 'title' => 'Forward Test', 'template' => 'template-forward.php', 'content' => 'pages/forward-test', 'group' => 'test', 'menu' => 'Forward Test' ),
		'how-to-install'   => array( 'title' => 'วิธีติดตั้ง EA ใน MT5', 'template' => 'template-install.php', 'content' => 'pages/how-to-install', 'group' => 'guide', 'menu' => 'วิธีติดตั้ง EA บน MT5' ),
		'open-mt5-account' => array( 'title' => 'เปิดบัญชี MT5', 'template' => 'template-guide.php', 'content' => 'pages/open-mt5-account', 'group' => 'guide', 'menu' => 'เปิดบัญชีเทรด MT5' ),
		'deposit'          => array( 'title' => 'ฝากเงินเข้าบัญชีเทรด MT5', 'template' => 'template-guide.php', 'content' => 'pages/deposit', 'group' => 'guide', 'menu' => 'ฝากเงินเข้าบัญชีเทรด' ),
		'mt5-login'        => array( 'title' => 'ติดตั้งและล็อกอิน MT5', 'template' => 'template-guide.php', 'content' => 'pages/mt5-login', 'group' => 'guide', 'menu' => 'ติดตั้งและล็อกอิน MT5' ),
		'vps-windows'      => array( 'title' => 'คู่มือ VPS บน Windows', 'template' => 'template-guide.php', 'content' => 'pages/vps-windows', 'group' => 'guide', 'menu' => 'VPS บน Windows' ),
		'vps-android'      => array( 'title' => 'คู่มือ VPS บน Android', 'template' => 'template-guide.php', 'content' => 'pages/vps-android', 'group' => 'guide', 'menu' => 'VPS บน Android' ),
		'vps-ios'          => array( 'title' => 'คู่มือ VPS บน iPhone', 'template' => 'template-guide.php', 'content' => 'pages/vps-ios', 'group' => 'guide', 'menu' => 'VPS บน iPhone / iPad' ),
		'tools'            => array( 'title' => 'เครื่องมือคำนวณ', 'template' => 'template-guide.php', 'content' => 'pages/tools', 'group' => 'guide', 'menu' => 'เครื่องคำนวณ Lot / Drawdown' ),
		'dashboard'        => array( 'title' => 'แดชบอร์ด FALCON PRO', 'template' => 'template-dashboard.php', 'content' => 'pages/dashboard', 'group' => 'guide', 'menu' => 'แดชบอร์ด FALCON PRO' ),
		'pricing'          => array( 'title' => 'แพ็กเกจและราคา', 'template' => 'template-pricing.php', 'content' => 'pages/pricing', 'group' => 'pricing' ),
		'risk-disclosure'  => array( 'title' => 'ประกาศความเสี่ยง', 'template' => 'template-risk.php', 'content' => 'pages/risk-disclosure', 'group' => 'doc' ),
		'about'            => array( 'title' => 'เกี่ยวกับเรา', 'template' => '', 'content' => 'pages/about', 'group' => 'doc' ),
		'privacy-policy'   => array( 'title' => 'นโยบายความเป็นส่วนตัว', 'template' => '', 'content' => 'pages/privacy-policy', 'group' => 'doc', 'status' => 'draft' ),
		'terms-of-use'     => array( 'title' => 'ข้อกำหนดและเงื่อนไขการใช้บริการ', 'template' => '', 'content' => 'pages/terms-of-use', 'group' => 'doc', 'status' => 'draft' ),
		'data-deletion'    => array( 'title' => 'คำขอลบข้อมูลส่วนบุคคล', 'template' => '', 'content' => 'pages/data-deletion', 'group' => 'doc', 'status' => 'draft' ),
		'go'               => array( 'title' => 'ติดต่อ FALCON PRO EA', 'template' => 'template-go.php', 'content' => '', 'group' => 'hub', 'seo' => array( 'seo_title' => 'ติดต่อ FALCON PRO EA · LINE และลิงก์เริ่มต้นใช้งาน EA MT5', 'description' => 'ช่องทางติดต่อ FALCON PRO EA ทาง LINE พร้อมลิงก์เริ่มต้นใช้งาน 6 ขั้น ตั้งแต่เปิดบัญชี ดาวน์โหลด MT5 ติดตั้ง EA จนถึงตั้งค่า VPS ให้ระบบทำงานต่อเนื่อง', 'keyword' => 'FALCON PRO EA' ) ),
		'articles'         => array( 'title' => 'บทความ EA และ MT5', 'template' => '', 'content' => '', 'group' => 'hub', 'posts' => true, 'seo' => array( 'seo_title' => 'คลังความรู้ EA บน MT5 ภาษาไทย · FALCON PRO EA', 'description' => 'รวมบทความและคู่มือ EA MT5 ภาษาไทย ตั้งแต่พื้นฐาน EA คืออะไร การคำนวณ Lot, Drawdown, Spread, Margin Call ไปจนถึงการเลือกโบรกเกอร์และ VPS สำหรับบอทเทรด', 'keyword' => 'บทความ EA' ) ),
	);
}

/**
 * รายการบทความเริ่มต้น: slug => แบนเนอร์สำรอง
 * (ใช้เมื่อยังไม่มีรูปปกเฉพาะบทความที่ assets/img/covers/<slug>.webp · สร้างด้วย dev/make-covers.php)
 * ฟังก์ชันนี้ไม่อ่านไฟล์ จึงเรียกจากหน้าเว็บได้ทุกคำขอ
 */
function fenix_seed_article_covers() {
	return array(
		'what-is-ea-mt5'              => 'falcon-pro-ea-mt5-laptop-overview.webp',
		'ea-gold-xauusd'              => 'falcon-pro-ea-mt5-mobile-xauusd.webp',
		'vps-for-ea'                  => 'falcon-pro-ea-mt5-desk-setup.webp',
		'drawdown'                    => 'falcon-pro-ea-mt5-settings-panel.webp',
		'lot-size-calculation'        => 'falcon-pro-ea-mt5-mobile-settings.webp',
		'free-ea-vs-paid'             => 'falcon-pro-ea-mt5-feature-toggles.webp',
		'spread-slippage'             => 'falcon-pro-ea-mt5-laptop-falcon.webp',
		'margin-call-stop-out'        => 'falcon-pro-ea-mt5-tablet-metatrader.webp',
		'choose-broker-for-ea'        => 'falcon-pro-ea-mt5-navigator.webp',
		'ea-scam-warning'             => 'falcon-pro-ea-mt5-ea-status-panel.webp',
		'mt4-vs-mt5'                  => 'falcon-pro-ea-mt5-tablet-metatrader.webp',
		'demo-account-ea'             => 'falcon-pro-ea-mt5-laptop-overview.webp',
		'cent-account'                => 'falcon-pro-ea-mt5-mobile-settings.webp',
		'grid-martingale-ea'          => 'falcon-pro-ea-mt5-settings-panel.webp',
		'profit-factor'               => 'falcon-pro-ea-mt5-laptop-falcon.webp',
		'ea-not-trading'              => 'falcon-pro-ea-mt5-ea-status-panel.webp',
		'leverage'                    => 'falcon-pro-ea-mt5-feature-toggles.webp',
		'swap-commission'             => 'falcon-pro-ea-mt5-navigator.webp',
		'ea-news-trading'             => 'falcon-pro-ea-mt5-mobile-xauusd.webp',
		'vps-mt5-stable'              => 'falcon-pro-ea-mt5-desk-setup.webp',
		'ea-optimization-overfitting' => 'falcon-pro-ea-mt5-laptop-running.webp',
		'ea-monitoring-routine'       => 'falcon-pro-ea-mt5-phone-watch.webp',
	);
}

/**
 * บทความเริ่มต้น (slug => ไฟล์ + รูปหน้าปก) · อ่านไฟล์เนื้อหา ใช้ในหน้า Setup เท่านั้น
 */
function fenix_seed_articles() {
	static $list = null;
	if ( null !== $list ) {
		return $list;
	}
	$img  = get_template_directory() . '/assets/img/';
	$list = array();
	foreach ( fenix_seed_article_covers() as $slug => $banner ) {
		$meta = fenix_seed_meta( 'articles/' . $slug );
		if ( null === $meta ) {
			continue; // ยังไม่มีไฟล์เนื้อหา
		}
		$own           = 'covers/' . $slug . '.webp';
		$list[ $slug ] = array(
			'title'   => ! empty( $meta['title'] ) ? $meta['title'] : $slug,
			'content' => 'articles/' . $slug,
			'cover'   => file_exists( $img . $own ) ? $own : 'banners/' . $banner,
			'meta'    => $meta,
		);
	}
	return $list;
}

/**
 * อ่านไฟล์เนื้อหาเริ่มต้น → array( 'meta' => array, 'body' => string ) หรือ null ถ้าไม่มีไฟล์
 */
function fenix_seed_file( $rel ) {
	static $cache = array();
	if ( isset( $cache[ $rel ] ) ) {
		return $cache[ $rel ];
	}
	$rel  = preg_replace( '#[^a-z0-9/_\-]#', '', strtolower( (string) $rel ) );
	$path = get_template_directory() . '/inc/content/' . $rel . '.html';
	if ( '' === $rel || ! file_exists( $path ) ) {
		$cache[ $rel ] = null;
		return null;
	}
	$raw  = (string) file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$meta = array();
	if ( preg_match( '/^\s*<!--meta\s+(\{.*?\})\s*-->\s*/s', $raw, $m ) ) {
		$decoded = json_decode( $m[1], true );
		if ( is_array( $decoded ) ) {
			$meta = $decoded;
		}
		$raw = substr( $raw, strlen( $m[0] ) );
	}
	$cache[ $rel ] = array(
		'meta' => $meta,
		'body' => trim( $raw ),
	);
	return $cache[ $rel ];
}

function fenix_seed_meta( $rel ) {
	$file = fenix_seed_file( $rel );
	return $file ? $file['meta'] : null;
}

/**
 * เนื้อหาพร้อมใช้ ({{home}} → URL เว็บ)
 */
function fenix_seed_content( $rel ) {
	$file = fenix_seed_file( $rel );
	if ( ! $file ) {
		return '';
	}
	return str_replace( '{{home}}', untrailingslashit( home_url() ), $file['body'] );
}

/**
 * ป้ายเล็กเหนือหัวเพจ (kicker) จากไฟล์เนื้อหา หรือกลุ่มเพจ
 */
function fenix_page_kicker( $slug ) {
	$pages = fenix_site_pages();
	if ( isset( $pages[ $slug ] ) && ! empty( $pages[ $slug ]['content'] ) ) {
		$meta = fenix_seed_meta( $pages[ $slug ]['content'] );
		if ( ! empty( $meta['kicker'] ) ) {
			return $meta['kicker'];
		}
	}
	$groups = array(
		'test'    => 'Testing',
		'guide'   => 'Guide',
		'pricing' => 'Pricing',
		'doc'     => 'Document',
	);
	$group = isset( $pages[ $slug ]['group'] ) ? $pages[ $slug ]['group'] : '';
	return isset( $groups[ $group ] ) ? $groups[ $group ] : 'FALCON PRO EA';
}

/**
 * รายการคู่มือ (ใช้ในเมนู, footer, หน้า /articles/, กล่องคู่มือที่เกี่ยวข้อง)
 */
function fenix_guide_links( $groups = array( 'guide', 'test' ) ) {
	$out = array();
	foreach ( fenix_site_pages() as $slug => $page ) {
		if ( in_array( $page['group'], (array) $groups, true ) ) {
			$out[ $slug ] = array(
				'label' => ! empty( $page['menu'] ) ? $page['menu'] : $page['title'],
				'url'   => home_url( '/' . $slug . '/' ),
			);
		}
	}
	return $out;
}

/**
 * URL ของเพจตาม slug เฉพาะเมื่อเผยแพร่แล้ว (ฉบับร่าง/ไม่มี = '') · กันลิงก์ 404 ไปยังเพจกฎหมายที่ยังเป็นร่าง
 */
function fenix_published_page_url( $slug ) {
	static $cache = array();
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}
	$page            = get_page_by_path( $slug );
	$cache[ $slug ] = ( $page && 'publish' === get_post_status( $page ) ) ? get_permalink( $page ) : '';
	return $cache[ $slug ];
}

/**
 * slug ของบทความเริ่มต้นที่ผู้เข้าชมยังเปิดไม่ได้ (ยังไม่ได้นำเข้า หรือยังเป็นฉบับร่าง)
 * · 1 query (อ่านเฉพาะคอลัมน์ post_name) ต่อคำขอ
 */
function fenix_unpublished_seed_articles() {
	if ( isset( $GLOBALS['fenix_unpublished_seed'] ) ) {
		return $GLOBALS['fenix_unpublished_seed'];
	}
	global $wpdb;
	$slugs = array_keys( fenix_seed_article_covers() );
	$in    = implode( ',', array_fill( 0, count( $slugs ), '%s' ) );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $in มีแต่ %s
	$have = (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_name FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_name IN ($in)", $slugs ) );

	$GLOBALS['fenix_unpublished_seed'] = array_values( array_diff( $slugs, $have ) );
	return $GLOBALS['fenix_unpublished_seed'];
}

/**
 * เผยแพร่/ถอนบทความระหว่างคำขอ (เช่นนำเข้าแบบเผยแพร่ทันที) → คำนวณรายการใหม่
 */
function fenix_unpublished_seed_reset() {
	unset( $GLOBALS['fenix_unpublished_seed'] );
}
add_action( 'transition_post_status', 'fenix_unpublished_seed_reset' );

/**
 * ตัดลิงก์ที่ชี้ไปยัง slug ในรายการ $slugs (ปลายทางที่ผู้เข้าชมยังเปิดไม่ได้) ออกจาก HTML
 * 1) ประโยคอ้างอิงที่ห่อด้วย <span class="xref"> หรือ <p class="xref"> → ลบทั้งประโยค
 * 2) รายการ <li> ที่มีแต่ลิงก์นั้น (เช่นในกล่อง "ที่เกี่ยวข้อง") → ลบทั้งรายการ · กล่องที่ว่างลงถูกลบด้วย
 * 3) ลิงก์ที่เหลือในเนื้อความ → ข้อความธรรมดา
 * กติกาความปลอดภัย: ทุกขั้นหยุดที่ขอบบล็อก (li, p, ul, div, h2 ...) และไม่ข้ามแท็กซ้อนชนิดเดียวกัน
 * ถ้า HTML ผิดรูปจน match ไม่ได้ ขั้นนั้นไม่ทำอะไร (ลิงก์อาจยังเหลือ แต่เนื้อหาไม่มีวันหาย)
 * · ขั้นใดล้มเหลว (null) จะข้ามขั้นนั้น · ใช้ quantifier แบบ possessive จึงไม่ติด backtrack/JIT limit
 */
function fenix_unlink_slugs( $content, $slugs ) {
	if ( ! $slugs || false === stripos( (string) $content, '<a' ) ) {
		return $content;
	}
	// ที่อยู่ปลายทาง: แบบเต็ม (http/https หรือ //), แบบ /slug/ ภายในเว็บ · ท้ายมี ?query หรือ #anchor ได้
	$parts = wp_parse_url( home_url() );
	$host  = isset( $parts['host'] ) ? preg_quote( $parts['host'], '#' ) : '';
	$path  = isset( $parts['path'] ) ? preg_quote( untrailingslashit( $parts['path'] ), '#' ) : '';
	$alt   = implode( '|', array_map( function ( $s ) { return preg_quote( $s, '#' ); }, $slugs ) );
	$url   = '(?:(?:https?:)?//' . $host . '(?::\d+)?)?' . $path . '/(?:' . $alt . ')/?(?:[?\#][^"\'\s>]*)?';
	$href  = '<a\s[^>]*?href\s*=\s*(?:"' . $url . '"|\'' . $url . '\')[^>]*>';
	$block = '(?:li|p|ul|ol|div|h[1-6]|table|thead|tbody|tr|td|th|details|summary|blockquote|section|figure)\b';
	// ข้อความในลิงก์: ห้ามข้าม </a> และห้ามข้ามขอบบล็อก
	$text  = '((?:[^<]++|<(?!/a\s*>|/?' . $block . '))*+)';
	$link  = $href . $text . '</a\s*>';
	// ข้อความในประโยค xref: ห้ามมีแท็กชนิดเดียวกันซ้อน และห้ามข้ามขอบบล็อก
	$xtext = '(?:(?!</?(?:\1\b|' . $block . ')).)*';
	$steps = array(
		array( '#\s*<(p|span) class="xref">' . $xtext . '?' . $href . $xtext . '</\1>#is', '' ),
		array( '#<li>\s*' . $link . '\s*</li>\s*#is', '' ),
		array( '#<div class="related-links">\s*<h2[^>]*>(?:(?!</?h2\b).)*</h2>\s*<ul[^>]*>\s*</ul>\s*</div>#is', '' ),
		array( '#' . $link . '#is', '$1' ),
	);
	foreach ( $steps as $step ) {
		$next = preg_replace( $step[0], $step[1], $content );
		if ( null !== $next ) {
			$content = $next;
		}
	}
	return $content;
}

/**
 * ในเนื้อหา: ลิงก์ไปเพจใน manifest ที่ยังไม่เผยแพร่ (ฉบับร่างของเพจกฎหมาย หรือเพจที่ยังไม่ได้สร้าง) หรือบทความเริ่มต้นที่ยังไม่เผยแพร่
 * จะไม่เป็นลิงก์จนกว่าปลายทางจะเผยแพร่ (กันลิงก์ 404 ระหว่างทยอยเผยแพร่บทความ) · กติกาอยู่ที่ fenix_unlink_slugs()
 * · priority 15: หลัง wpautop (10) และ shortcode (11) ก่อนสร้างสารบัญ (20) สารบัญจึงไม่มีหัวข้อที่ถูกลบ
 * · ทำเฉพาะตอนแสดงผลให้ผู้เข้าชม ไม่ทำในหลังบ้าน/REST/cron (ปลั๊กอิน SEO ที่นับลิงก์ภายในจะเห็นลิงก์ครบ)
 */
function fenix_unlink_draft_pages( $content ) {
	if ( false === stripos( (string) $content, '<a' ) ) {
		return $content;
	}
	if ( ( is_admin() && ! wp_doing_ajax() ) || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $content;
	}
	return fenix_unlink_slugs( $content, fenix_unreachable_slugs() );
}

/**
 * slug ที่ผู้เข้าชมยังเปิดไม่ได้: เพจใน manifest ที่ยังไม่เผยแพร่ + บทความเริ่มต้นที่ยังไม่เผยแพร่
 * ใช้ทั้งกับเนื้อหาเพจ (fenix_unlink_draft_pages) และลิงก์ [ข้อความ](/slug/) ในข้อความ Customizer (fenix_rich_inline)
 *
 * เพจใน manifest ที่ยังไม่เผยแพร่มีสามแบบ: เพจที่ Setup สร้างเป็นฉบับร่าง (เพจกฎหมาย) · เพจที่ธีมรุ่นใหม่เพิ่มเข้ามา
 * แต่เจ้าของยังไม่ได้กด FALCON Setup ให้สร้าง (เช่น /deposit/ ซึ่งข้อความเริ่มต้นของคู่มือเปิดบัญชีลิงก์ไปหาทันทีที่อัปเดตธีม)
 * · และเพจที่เจ้าของถอดออกเอง · ทั้งสามแบบต้องไม่เป็นลิงก์ 404 เหมือนที่เมนู footer หน้าแรก และหน้า /go ตรวจอยู่แล้ว
 * fenix_published_page_url() จำผลต่อ slug ไว้ตลอดคำขอ และส่วนใหญ่ถูกถามโดย footer อยู่แล้ว จึงเพิ่ม query ไม่กี่ครั้งต่อหน้า
 */
function fenix_unreachable_slugs() {
	$drafts = array();
	foreach ( fenix_site_pages() as $slug => $page ) {
		if ( empty( $page['front'] ) && ! fenix_published_page_url( $slug ) ) {
			$drafts[] = $slug;
		}
	}
	/* แถวใน manifest ที่ยังรอไฟล์เนื้อหา: Setup ยังสร้างเพจนี้ไม่ได้ ลิงก์ไปหาจึงยังไม่มีปลายทาง (ปกติรายการนี้ว่าง ไม่มี query เพิ่ม)
	 * เว้นแต่เจ้าของสร้างเพจ slug นั้นเองและเผยแพร่แล้ว */
	foreach ( array_diff( array_keys( fenix_site_pages( true ) ), array_keys( fenix_site_pages() ) ) as $slug ) {
		if ( ! fenix_published_page_url( $slug ) ) {
			$drafts[] = $slug;
		}
	}
	return array_merge( $drafts, fenix_unpublished_seed_articles() );
}
add_filter( 'the_content', 'fenix_unlink_draft_pages', 15 );

/* ==============================================================
 * Admin · รูปแบบ → FALCON Setup
 * ============================================================== */

function fenix_setup_menu() {
	add_theme_page( 'FALCON Setup', 'FALCON Setup', 'edit_theme_options', 'falcon-setup', 'fenix_setup_screen' );
}
add_action( 'admin_menu', 'fenix_setup_menu' );

/**
 * หา page จาก slug (รวมฉบับร่าง)
 */
function fenix_find_page( $slug ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page ) {
		return $page;
	}
	$q = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 1,
		)
	);
	return $q ? $q[0] : null;
}

function fenix_find_post( $slug ) {
	$q = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'post',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 1,
		)
	);
	return $q ? $q[0] : null;
}

/**
 * ใส่ SEO meta ให้ Yoast / Rank Math (ถ้ามีค่าในไฟล์)
 */
function fenix_apply_seo_meta( $post_id, $meta ) {
	$meta = wp_slash( $meta ); // update_post_meta() คาดหวังข้อมูลแบบ slashed
	if ( ! empty( $meta['seo_title'] ) ) {
		update_post_meta( $post_id, '_yoast_wpseo_title', $meta['seo_title'] );
		update_post_meta( $post_id, 'rank_math_title', $meta['seo_title'] );
	}
	if ( ! empty( $meta['description'] ) ) {
		update_post_meta( $post_id, '_yoast_wpseo_metadesc', $meta['description'] );
		update_post_meta( $post_id, 'rank_math_description', $meta['description'] );
		update_post_meta( $post_id, 'fenix_meta_description', $meta['description'] );
	}
	if ( ! empty( $meta['keyword'] ) ) {
		update_post_meta( $post_id, '_yoast_wpseo_focuskw', $meta['keyword'] );
		update_post_meta( $post_id, 'rank_math_focus_keyword', $meta['keyword'] );
	}
}

/**
 * นำรูปแบนเนอร์ในธีมเข้า Media Library (ครั้งเดียว) เพื่อใช้เป็นรูปหน้าปกบทความ
 */
function fenix_banner_attachment( $file, $alt = '' ) {
	// $file = พาธใต้ assets/img/ เฉพาะโฟลเดอร์ banners/ หรือ covers/ (ชื่อไฟล์ล้วน = banners/)
	$file = ltrim( str_replace( '\\', '/', (string) $file ), '/' );
	if ( false === strpos( $file, '/' ) ) {
		$file = 'banners/' . $file;
	}
	if ( ! preg_match( '#^(banners|covers)/[a-z0-9._-]+$#i', $file ) ) {
		return 0;
	}
	$map = get_option( 'fenix_banner_attachments', array() );
	if ( ! empty( $map[ $file ] ) && get_post( $map[ $file ] ) ) {
		return (int) $map[ $file ];
	}
	$src = get_template_directory() . '/assets/img/' . $file;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( basename( $file ) );
	if ( ! $tmp || ! copy( $src, $tmp ) ) {
		return 0;
	}
	$id = media_handle_sideload(
		array(
			'name'     => basename( $file ),
			'tmp_name' => $tmp,
		),
		0,
		'' !== $alt ? $alt : 'FALCON PRO EA · ภาพประกอบ'
	);
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', '' !== $alt ? $alt : 'FALCON PRO EA ผู้ช่วยเทรดอัตโนมัติสำหรับ MT5 (ภาพประกอบ)' );
	$map[ $file ] = (int) $id;
	update_option( 'fenix_banner_attachments', $map, false );
	return (int) $id;
}

/**
 * ภาพเด่นของเพจนี้คือภาพปกที่ FALCON Setup นำเข้าจากธีมเอง (assets/img/covers/page-*.webp) หรือไม่
 * ภาพปกแบบนี้เป็นการ์ดชื่อเพจสำหรับรูปแชร์/ schema เท่านั้น page.php จึงไม่พิมพ์ซ้ำในเนื้อหา · ภาพเด่นที่เจ้าของเลือกเอง = false
 */
function fenix_is_theme_page_cover( $post_id ) {
	$thumb = (int) get_post_thumbnail_id( $post_id );
	if ( ! $thumb ) {
		return false;
	}
	foreach ( (array) get_option( 'fenix_banner_attachments', array() ) as $file => $id ) {
		if ( (int) $id === $thumb && 0 === strpos( (string) $file, 'covers/page-' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * เนื้อหาตั้งต้นของ "เพจ" ที่พร้อมบันทึกลงฐานข้อมูล: fenix_seed_content() แล้วตัดคอมเมนต์ HTML ที่เหลือในไฟล์ออก
 * (โน้ตถึงผู้ดูแลในไฟล์ต้องไม่ไปโผล่ในซอร์สของหน้าเว็บ และ wpautop จะห่อคอมเมนต์เดี่ยวเป็น <p> ว่าง)
 * คงคอมเมนต์ที่ WordPress ใช้งานจริงไว้: more / nextpage / noteaser และบล็อก wp:
 * ใช้เฉพาะเส้นทางสร้าง/แทนเนื้อหาเพจ · บทความยังอ่านผ่าน fenix_seed_content() ตามเดิม (ลายนิ้วมือฉบับร่างจึงไม่เปลี่ยน)
 */
function fenix_setup_page_content( $rel ) {
	$content = fenix_seed_content( $rel );
	if ( false === strpos( $content, '<!--' ) ) {
		return $content;
	}
	$note = '<!--(?!\s*(?:more|nextpage|noteaser|\/?wp:)).*?-->';
	/* คอมเมนต์ที่ขึ้นต้นบรรทัด: ตัดพร้อมช่องว่างและการขึ้นบรรทัดของมัน · คอมเมนต์กลางประโยค: ตัดเฉพาะตัวคอมเมนต์ */
	$clean = preg_replace( array( '/^[ \t]*' . $note . '[ \t]*\r?\n?/ms', '/' . $note . '/s' ), '', $content );
	return is_string( $clean ) ? trim( $clean ) : $content;
}

/**
 * สร้างเพจที่ยังไม่มี · ไม่แตะเนื้อหาเพจที่มีอยู่แล้ว (เว้นแต่เลือก overwrite)
 * overwrite แทนเนื้อหา ชื่อเพจ (เมื่อไฟล์ระบุ title) เทมเพลต และค่า SEO (ชื่อ คำอธิบาย คีย์เวิร์ดหลัก) ของทุกเพจ
 * เพจที่ไม่มีไฟล์เนื้อหา (home, go, articles) ได้เฉพาะค่า SEO จากแถว seo ของ manifest: ไม่มีทางอื่นที่ค่านั้นถูกเขียนซ้ำหลังสร้างเพจ
 * ลำดับเพจ (menu_order) มาจากลำดับใน fenix_site_pages(): เพจใหม่ได้ลำดับตั้งแต่สร้าง · เพจเดิมที่ลำดับยังเป็น 0 ได้ลำดับนี้
 * (เขียนคอลัมน์ตรง ๆ ไม่ผ่าน wp_update_post วันที่ "ปรับปรุงล่าสุด" ของเพจจึงไม่ขยับ) · ลำดับที่เจ้าของจัดเองไม่ถูกแตะ
 */
function fenix_setup_create_pages( $overwrite = false ) {
	global $wpdb;
	$log     = array();
	$ordered = 0;
	foreach ( fenix_site_pages() as $slug => $page ) {
		$meta    = ! empty( $page['content'] ) ? fenix_seed_meta( $page['content'] ) : ( isset( $page['seo'] ) ? $page['seo'] : array() );
		$title   = ! empty( $meta['title'] ) ? $meta['title'] : $page['title'];
		$content = ! empty( $page['content'] ) ? fenix_setup_page_content( $page['content'] ) : '';
		$status  = isset( $page['status'] ) ? $page['status'] : 'publish';
		$order   = isset( $page['order'] ) ? (int) $page['order'] : 0;
		$found   = fenix_find_page( $slug );

		$seed_rev = ! empty( $meta['rev'] ) ? (int) $meta['rev'] : 1;
		if ( ! $found ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => $status,
					'post_title'   => wp_slash( $title ),
					'post_name'    => $slug,
					'post_content' => wp_slash( $content ),
					'menu_order'   => $order,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				$log[] = '✗ ' . $slug . ' · ' . $id->get_error_message();
				continue;
			}
			update_post_meta( $id, 'fenix_seed_rev', $seed_rev );
			$log[] = '✓ สร้าง /' . $slug . '/' . ( 'draft' === $status ? ' (ฉบับร่าง รอตรวจ)' : '' );
			if ( 'privacy-policy' === $slug ) {
				update_option( 'wp_page_for_privacy_policy', (int) $id ); // ตั้งเป็นเพจนโยบายความเป็นส่วนตัวของเว็บ
			}
		} else {
			$id = $found->ID;
			if ( $order && 0 === (int) $found->menu_order ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- ตั้งใจไม่ผ่าน wp_update_post (จะขยับ post_modified ของทุกเพจ)
				if ( false !== $wpdb->update( $wpdb->posts, array( 'menu_order' => $order ), array( 'ID' => $id ), array( '%d' ), array( '%d' ) ) ) {
					clean_post_cache( $id );
					$ordered++;
				}
			}
			// เพจ Privacy Policy ฉบับร่างที่ WordPress สร้างให้ตอนติดตั้ง (ยังไม่เคยแก้) → แทนด้วยเนื้อหาของธีม หนึ่งครั้ง
			// ต้องเป็นเพจที่ธีมยังไม่เคยวางเนื้อหา (ไม่มี meta fenix_seed_rev: ธีมเขียน meta นี้ทั้งตอนสร้างเพจและตอนแทนเพจ)
			// และมีวันที่ GMT จริง: เพจของ WordPress ตอนติดตั้งมี ส่วนฉบับร่างที่สร้างผ่าน wp_insert_post มีค่าเป็นศูนย์
			// แล้ว post_modified จะเท่ากับ post_date ทุกครั้งที่บันทึก ใช้ตัดสินว่า "ยังไม่แก้" ไม่ได้
			// ฉบับร่างที่ธีมสร้างเองหรือเคยแทนไปแล้ว และฉบับร่างที่เจ้าของเขียนเอง จึงถูกแทนเฉพาะเมื่อติ๊กเขียนทับ
			// หรือกด "แทนด้วยรุ่นล่าสุด" ในแถวของเพจ
			$core_privacy = 'privacy-policy' === $slug
				&& (int) get_option( 'wp_page_for_privacy_policy' ) === (int) $id
				&& 'draft' === $found->post_status
				&& '' === (string) get_post_meta( $id, 'fenix_seed_rev', true )
				&& ! empty( $found->post_date_gmt ) && '0000-00-00 00:00:00' !== $found->post_date_gmt
				&& $found->post_modified === $found->post_date;
			if ( $core_privacy && $content && ! $overwrite ) {
				wp_update_post(
					array(
						'ID'           => $id,
						'post_title'   => wp_slash( $title ),
						'post_content' => wp_slash( $content ),
					)
				);
				update_post_meta( $id, '_wp_page_template', $page['template'] );
				update_post_meta( $id, 'fenix_seed_rev', ! empty( $meta['rev'] ) ? (int) $meta['rev'] : 1 );
				fenix_apply_seo_meta( $id, $meta );
				$log[] = '↻ /privacy-policy/ · เพจ Privacy Policy ที่ WordPress สร้างไว้ตอนติดตั้ง (ยังไม่เคยถูกแก้) ถูกแทนด้วยฉบับของธีมทั้งชื่อเพจ เนื้อหา และค่า SEO · ทำครั้งเดียว เพจยังเป็นฉบับร่าง';
				continue;
			}
			if ( $overwrite && $content ) {
				// ชื่อเพจคือ H1 ของหน้าคู่มือและเพจเอกสาร: เขียนทับเนื้อหาแล้วต้องได้ชื่อของรุ่นเดียวกันด้วย (เฉพาะเมื่อไฟล์ระบุ title)
				$update = array(
					'ID'           => $id,
					'post_content' => wp_slash( $content ),
				);
				if ( ! empty( $meta['title'] ) ) {
					$update['post_title'] = wp_slash( $meta['title'] );
				}
				wp_update_post( $update );
				update_post_meta( $id, 'fenix_seed_rev', $seed_rev );
			}
			if ( $overwrite ) {
				// บอกให้ครบว่าเพจนี้ถูกเขียนทับส่วนไหน: ค่า SEO (ชื่อ คำอธิบาย คีย์เวิร์ดหลัก) ถูกแทนด้านล่างด้วย และเป็นส่วนเดียว
				// ที่เพจไม่มีไฟล์เนื้อหา (home, go, articles) ได้รับ
				$parts = array();
				if ( $content ) {
					$parts[] = 'เนื้อหา';
					if ( ! empty( $meta['title'] ) ) {
						$parts[] = 'ชื่อเพจ';
					}
				}
				if ( ! empty( $meta['seo_title'] ) || ! empty( $meta['description'] ) || ! empty( $meta['keyword'] ) ) {
					$parts[] = 'ค่า SEO';
				}
				if ( $parts ) {
					$log[] = '↻ เขียนทับ /' . $slug . '/ · ' . implode( ' · ', $parts );
				}
			}
		}

		if ( ! empty( $page['template'] ) ) {
			$current = get_post_meta( $id, '_wp_page_template', true );
			if ( ! $current || 'default' === $current || $overwrite ) {
				update_post_meta( $id, '_wp_page_template', $page['template'] );
			}
		}
		if ( $meta && ( ! $found || $overwrite ) ) {
			fenix_apply_seo_meta( $id, $meta );
		}
	}
	if ( $ordered ) {
		$log[] = '✓ จัดลำดับเพจในหลังบ้านตามลำดับของธีม ' . $ordered . ' เพจ (เฉพาะเพจที่ยังไม่เคยจัดลำดับ)';
	}
	$pending = array_diff( array_keys( fenix_site_pages( true ) ), array_keys( fenix_site_pages() ) );
	if ( $pending ) {
		$log[] = '• ยังไม่สร้าง /' . implode( '/ /', $pending ) . '/ · ธีมรุ่นนี้ยังไม่มีไฟล์เนื้อหาของเพจนี้';
	}
	return $log;
}

/**
 * ตั้งหน้าแรก (home) + หน้าบทความ (articles)
 */
function fenix_setup_reading() {
	$home     = fenix_find_page( 'home' );
	$articles = fenix_find_page( 'articles' );
	if ( ! $home || ! $articles ) {
		return array( '✗ ต้องสร้างเพจ home และ articles ก่อน' );
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home->ID );
	update_option( 'page_for_posts', $articles->ID );
	return array( '✓ ตั้ง "หน้าแรก" เป็นหน้าเว็บหลัก และ "บทความ" เป็นหน้ารวมบทความ' );
}

/**
 * เพจคู่มือ/ทดสอบใน manifest ที่เผยแพร่แล้วแต่ยังไม่อยู่ในเมนู $menu_id (เช่นเพจที่ธีมเพิ่มหลังสร้างเมนูไปแล้ว)
 * อ่านอย่างเดียว: เมนูที่มีอยู่เป็นของเจ้าของเว็บ ธีมไม่เพิ่มหรือลบรายการเอง · คืน array( slug => ชื่อในเมนู )
 */
function fenix_setup_menu_missing( $menu_id ) {
	$ids  = array();
	$urls = array();
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
		if ( isset( $item->object, $item->object_id ) && 'page' === $item->object ) {
			$ids[] = (int) $item->object_id;
		}
		if ( ! empty( $item->url ) ) {
			$urls[] = untrailingslashit( (string) $item->url );
		}
	}
	$missing = array();
	foreach ( fenix_guide_links( array( 'test', 'guide' ) ) as $slug => $link ) {
		$page = fenix_find_page( $slug );
		if ( ! $page || 'publish' !== $page->post_status ) {
			continue;
		}
		if ( in_array( (int) $page->ID, $ids, true ) || in_array( untrailingslashit( $link['url'] ), $urls, true ) ) {
			continue;
		}
		$missing[ $slug ] = $link['label'];
	}
	return $missing;
}

/**
 * สร้างเมนูหลัก (ถ้ายังไม่มีเมนูที่ตำแหน่ง primary)
 * มีเมนูอยู่แล้ว = ไม่แตะ แต่แจ้งเพจคู่มือที่ยังไม่อยู่ในเมนู ให้เจ้าของเพิ่มเองที่ รูปแบบ → เมนู
 */
function fenix_setup_menu_build() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! empty( $locations['primary'] ) && wp_get_nav_menu_object( $locations['primary'] ) ) {
		$log     = array( '• มีเมนูหลักอยู่แล้ว (ไม่สร้างซ้ำ) · แก้ได้ที่ รูปแบบ → เมนู' );
		$missing = fenix_setup_menu_missing( $locations['primary'] );
		if ( $missing ) {
			$names = array();
			foreach ( $missing as $slug => $label ) {
				$names[] = $label . ' (/' . $slug . '/)';
			}
			$log[] = '• เพจที่ยังไม่อยู่ในเมนูหลัก: ' . implode( ' · ', $names ) . ' · เพิ่มเองที่ รูปแบบ → เมนู (วางใต้หัวข้อคู่มือ ตามลำดับในตารางสถานะเพจด้านล่าง)';
		}
		return $log;
	}
	// มีเมนู FALCON อยู่แล้ว (เช่นกดซ้ำ หรือเคยถอดออกจากตำแหน่ง) → แค่ตั้งตำแหน่งให้ ไม่เพิ่มรายการซ้ำ
	$existing = wp_get_nav_menu_object( 'FALCON · เมนูหลัก' );
	if ( $existing ) {
		$locations['primary'] = $existing->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		return array( '• ใช้เมนู "FALCON · เมนูหลัก" ที่มีอยู่แล้ว และตั้งเป็นเมนู Header' );
	}
	$menu_id = wp_create_nav_menu( 'FALCON · เมนูหลัก' );
	if ( is_wp_error( $menu_id ) ) {
		return array( '✗ สร้างเมนูไม่สำเร็จ: ' . $menu_id->get_error_message() );
	}
	// ตั้งตำแหน่งก่อนเพิ่มรายการ เพื่อให้การกดซ้ำระหว่างทำงานเจอเมนูนี้และไม่สร้างซ้ำ
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	// $target = slug ของเพจ (ลิงก์แบบ page object ให้ไฮไลต์เมนูปัจจุบันได้) หรือ URL เต็ม
	$add = function ( $title, $target, $parent = 0 ) use ( $menu_id ) {
		$args = array(
			'menu-item-title'     => $title,
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		);
		$page = 0 === strpos( $target, 'http' ) ? null : fenix_find_page( $target );
		if ( $page ) {
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $page->ID;
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = 0 === strpos( $target, 'http' ) ? $target : home_url( '/' . $target . '/' );
		}
		return wp_update_nav_menu_item( $menu_id, 0, $args );
	};

	$add( 'หน้าแรก', home_url( '/' ) );
	$test = $add( 'การทดสอบ', 'backtest' );
	foreach ( fenix_guide_links( array( 'test' ) ) as $slug => $link ) {
		$add( $link['label'], $slug, $test );
	}
	$guide = $add( 'คู่มือการใช้งาน', 'how-to-install' );
	foreach ( fenix_guide_links( array( 'guide' ) ) as $slug => $link ) {
		$add( $link['label'], $slug, $guide );
	}
	$add( 'แพ็กเกจ', 'pricing' );
	$add( 'บทความ', 'articles' );
	$add( 'ติดต่อ', 'go' );

	return array( '✓ สร้างเมนูหลักและตั้งที่ตำแหน่ง Header แล้ว' );
}

/**
 * นำเข้าบทความ · บทความที่มี slug อยู่แล้วไม่ถูกสร้างซ้ำ
 * (ฉบับร่างที่ยังไม่เคยแก้จะอัปเดตตามไฟล์รุ่นล่าสุด ดู fenix_setup_refresh_draft)
 */
function fenix_setup_import_articles( $publish = false ) {
	$log = array();
	foreach ( fenix_seed_articles() as $slug => $art ) {
		$found = fenix_find_post( $slug );
		if ( $found ) {
			$log[] = fenix_setup_refresh_draft( $found, $art )
				? '↻ อัปเดตฉบับร่างตามไฟล์รุ่นล่าสุด: ' . $art['title']
				: '• มีอยู่แล้ว: ' . $slug;
			continue;
		}
		$meta = $art['meta'];
		$cat  = 0;
		if ( ! empty( $meta['category'] ) ) {
			$term = term_exists( $meta['category'], 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $meta['category'], 'category' );
			}
			if ( ! is_wp_error( $term ) ) {
				$cat = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			}
		}
		$id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => $publish ? 'publish' : 'draft',
				'post_title'    => wp_slash( $art['title'] ),
				'post_name'     => $slug,
				'post_content'  => wp_slash( fenix_seed_content( $art['content'] ) ),
				'post_excerpt'  => isset( $meta['excerpt'] ) ? wp_slash( $meta['excerpt'] ) : '',
				'post_category' => $cat ? array( $cat ) : array(),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			$log[] = '✗ ' . $slug . ' · ' . $id->get_error_message();
			continue;
		}
		fenix_apply_seo_meta( $id, $meta );
		update_post_meta( $id, 'fenix_seed_hash', fenix_seed_fingerprint( fenix_find_post( $slug ) ) );
		// รูปปกเฉพาะบทความ (covers/) ใช้ชื่อบทความเป็น alt · แบนเนอร์สำรองใช้ alt กลาง
		$thumb = fenix_banner_attachment( $art['cover'], 0 === strpos( $art['cover'], 'covers/' ) ? $art['title'] : '' );
		if ( $thumb ) {
			set_post_thumbnail( $id, $thumb );
		}
		$log[] = '✓ ' . ( $publish ? 'เผยแพร่' : 'ฉบับร่าง' ) . ': ' . $art['title'];
	}
	return $log ? $log : array( '• ไม่มีไฟล์บทความให้นำเข้า' );
}

/**
 * ลายนิ้วมือของสิ่งที่ธีมเขียนลงบทความตอนนำเข้า: หัวเรื่อง คำโปรย เนื้อหา และช่อง SEO ของ Yoast
 * ใช้ตัดสินว่าเจ้าของเว็บแก้ฉบับร่างด้วยมือแล้วหรือยัง · $post ต้องเป็นค่าที่อ่านสดจากฐานข้อมูล (fenix_find_post)
 */
function fenix_seed_fingerprint( $post ) {
	if ( ! $post ) {
		return '';
	}
	$parts = array( (string) $post->post_title, (string) $post->post_excerpt, (string) $post->post_content );
	foreach ( array( '_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_focuskw' ) as $key ) {
		$parts[] = (string) get_post_meta( $post->ID, $key, true );
	}
	return md5( implode( "\n\x1e", $parts ) );
}

/**
 * ฉบับร่างของบทความเริ่มต้นที่เจ้าของเว็บ "ยังไม่เคยแก้" → อัปเดตหัวเรื่อง/เนื้อหา/คำโปรย/SEO ให้ตรงไฟล์รุ่นล่าสุดของธีม
 * ยังไม่เคยแก้ = ลายนิ้วมือปัจจุบันตรงกับที่ธีมประทับไว้ตอนนำเข้า (meta fenix_seed_hash)
 * · ฉบับร่างที่นำเข้าก่อนมี meta นี้: ถือว่ายังไม่แก้เมื่อ post_modified_gmt ยังเป็นศูนย์
 *   (มีแต่ wp_insert_post ครั้งแรกที่ปล่อยค่านี้เป็นศูนย์ · บันทึกจากหน้าแก้ไข, autosave, Quick Edit ใส่เวลาจริงทุกครั้ง
 *   ห้ามใช้ post_modified === post_date เพราะ WordPress ตั้งสองค่านี้เท่ากันทุกครั้งที่บันทึกฉบับร่าง)
 * ไม่แตะบทความที่เผยแพร่/ตั้งเวลาแล้ว และไม่แตะฉบับร่างที่แก้ด้วยมือ · คืน true เมื่อมีการอัปเดต
 */
function fenix_setup_refresh_draft( $post, $art ) {
	if ( ! $post || 'draft' !== $post->post_status ) {
		return false;
	}
	$hash      = (string) get_post_meta( $post->ID, 'fenix_seed_hash', true );
	$untouched = '' !== $hash ? fenix_seed_fingerprint( $post ) === $hash : '0000-00-00 00:00:00' === $post->post_modified_gmt;
	if ( ! $untouched ) {
		return false;
	}
	$content = fenix_seed_content( $art['content'] );
	if ( '' === $content ) {
		return false;
	}
	$meta      = $art['meta'];
	$excerpt   = isset( $meta['excerpt'] ) ? (string) $meta['excerpt'] : '';
	$same_post = $content === $post->post_content && $art['title'] === $post->post_title && $excerpt === $post->post_excerpt;
	$same_seo  = true;
	foreach ( array( 'seo_title' => '_yoast_wpseo_title', 'description' => '_yoast_wpseo_metadesc', 'keyword' => '_yoast_wpseo_focuskw' ) as $field => $key ) {
		if ( ! empty( $meta[ $field ] ) && (string) $meta[ $field ] !== (string) get_post_meta( $post->ID, $key, true ) ) {
			$same_seo = false;
		}
	}
	if ( $same_post && $same_seo ) {
		if ( '' === $hash ) {
			update_post_meta( $post->ID, 'fenix_seed_hash', fenix_seed_fingerprint( $post ) );
		}
		return false;
	}
	if ( ! $same_post ) {
		$id = wp_update_post(
			array(
				'ID'           => $post->ID,
				'post_title'   => wp_slash( $art['title'] ),
				'post_content' => wp_slash( $content ),
				'post_excerpt' => wp_slash( $excerpt ),
			),
			true
		);
		if ( is_wp_error( $id ) || ! $id ) {
			return false;
		}
	}
	fenix_apply_seo_meta( $post->ID, $meta );
	update_post_meta( $post->ID, 'fenix_seed_hash', fenix_seed_fingerprint( fenix_find_post( $post->post_name ) ) );
	return true;
}

/**
 * ลำดับการปล่อยบทความ: เรื่องพื้นฐานและเรื่องที่หน้าคู่มืออ้างถึงออกก่อน แล้วสลับหมวดกันไป
 * slug ที่ไม่อยู่ในรายการ (บทความที่เพิ่มทีหลัง) ต่อท้ายตามลำดับใน fenix_seed_article_covers()
 */
function fenix_seed_publish_order() {
	$order = array(
		'what-is-ea-mt5',
		'ea-not-trading',
		'drawdown',
		'lot-size-calculation',
		'vps-for-ea',
		'ea-scam-warning',
		'cent-account',
		'demo-account-ea',
		'vps-mt5-stable',
		'profit-factor',
		'margin-call-stop-out',
		'choose-broker-for-ea',
		'ea-gold-xauusd',
		'ea-optimization-overfitting',
		'leverage',
		'mt4-vs-mt5',
		'spread-slippage',
		'ea-monitoring-routine',
		'grid-martingale-ea',
		'free-ea-vs-paid',
		'swap-commission',
		'ea-news-trading',
	);
	$all   = array_keys( fenix_seed_article_covers() );
	return array_values( array_unique( array_merge( array_intersect( $order, $all ), $all ) ) );
}

/**
 * วันในสัปดาห์ที่ปล่อยบทความตามจำนวนเรื่องต่อสัปดาห์ (เลขวันแบบ ISO: 1 = จันทร์ … 7 = อาทิตย์)
 */
function fenix_release_weekdays( $per_week ) {
	$map = array(
		1 => array( 2 ),
		2 => array( 2, 5 ),
		3 => array( 1, 3, 5 ),
	);
	return isset( $map[ (int) $per_week ] ) ? $map[ (int) $per_week ] : $map[2];
}

/**
 * ช่องเวลาปล่อยบทความถัดไป: วันถัดจาก $after ที่ตรงกับ $days เวลา 10:00 น. เวลาไทย (อย่างเร็วคือวันรุ่งขึ้น)
 */
function fenix_release_next_slot( DateTimeImmutable $after, $days ) {
	$slot = $after->setTimezone( new DateTimeZone( 'Asia/Bangkok' ) );
	for ( $i = 0; $i < 14; $i++ ) {
		$slot = $slot->modify( '+1 day' )->setTime( 10, 0, 0 );
		if ( in_array( (int) $slot->format( 'N' ), $days, true ) ) {
			break;
		}
	}
	return $slot;
}

/**
 * ปล่อยบทความเริ่มต้นที่ยังเป็นฉบับร่าง: $now_count เรื่องแรกเผยแพร่ทันที ที่เหลือตั้งเวลา 10:00 น. เวลาไทย
 * ตามวันของ fenix_release_weekdays() ต่อคิวหลังบทความของธีมที่ตั้งเวลาไว้แล้ว · กดซ้ำได้ (ทำเฉพาะฉบับร่าง)
 * ฉบับร่างที่ยังไม่เคยแก้ถูกอัปเดตตามไฟล์รุ่นล่าสุดก่อนปล่อย (ข้อความที่เผยแพร่จึงตรงกับไฟล์ของธีมเสมอ)
 * ลิงก์ไปบทความที่ยังไม่ถึงคิวถูกซ่อนด้วย fenix_unlink_slugs() จนกว่าจะเผยแพร่
 */
function fenix_setup_release_articles( $now_count = 6, $per_week = 2 ) {
	$now_count = max( 0, min( 100, (int) $now_count ) );
	$days      = fenix_release_weekdays( $per_week );
	$utc       = new DateTimeZone( 'UTC' );
	$slot      = new DateTimeImmutable( 'now', $utc );
	$arts      = fenix_seed_articles();
	$log       = array();
	$queue     = array();

	foreach ( fenix_seed_publish_order() as $slug ) {
		$post = fenix_find_post( $slug );
		if ( ! $post ) {
			continue;
		}
		if ( 'future' === $post->post_status && '0000-00-00 00:00:00' !== $post->post_date_gmt ) {
			// จุดเริ่มนับคิว = บทความของธีมที่ตั้งเวลาไว้ล่าสุด (ไม่นับโพสต์อื่นที่เจ้าของเว็บตั้งเวลาเอง)
			$at = date_create_immutable( $post->post_date_gmt, $utc );
			if ( $at && $at > $slot ) {
				$slot = $at;
			}
			continue;
		}
		if ( 'draft' !== $post->post_status ) {
			continue;
		}
		if ( isset( $arts[ $slug ] ) && fenix_setup_refresh_draft( $post, $arts[ $slug ] ) ) {
			$log[] = '↻ อัปเดตฉบับร่างตามไฟล์รุ่นล่าสุด: ' . $arts[ $slug ]['title'];
			$fresh = fenix_find_post( $slug );
			$post  = $fresh ? $fresh : $post;
		}
		$queue[] = $post;
	}
	if ( ! $queue ) {
		return array( '• ไม่มีบทความฉบับร่างให้ปล่อย (ยังไม่ได้นำเข้า หรือปล่อยครบแล้ว)' );
	}

	foreach ( $queue as $i => $post ) {
		if ( $i < $now_count ) {
			// ใส่เวลาปัจจุบันเองเสมอ (แบบเดียวกับ bulk edit ของ WordPress) · ฉบับร่างที่เคยตั้งเวลาแล้วดึงกลับจะไม่ถูกตั้งเวลากลับไปวันเดิม
			$done = wp_update_post(
				array(
					'ID'            => $post->ID,
					'post_status'   => 'publish',
					'post_date'     => current_time( 'mysql' ),
					'post_date_gmt' => '',
				),
				true
			);
			$want = 'publish';
			$ok   = '✓ เผยแพร่แล้ว: ' . $post->post_title;
		} else {
			$slot = fenix_release_next_slot( $slot, $days );
			$gmt  = $slot->setTimezone( $utc )->format( 'Y-m-d H:i:s' );
			// edit_date จำเป็น: ถ้าไม่ส่ง WordPress จะล้างวันที่ของฉบับร่างเป็น "ตอนนี้" แล้วเผยแพร่ทันทีแทนการตั้งเวลา
			$done = wp_update_post(
				array(
					'ID'            => $post->ID,
					'post_status'   => 'future',
					'post_date'     => get_date_from_gmt( $gmt ),
					'post_date_gmt' => $gmt,
					'edit_date'     => true,
				),
				true
			);
			$want = 'future';
			$ok   = '→ ตั้งเวลา ' . $slot->format( 'd/m/Y H:i' ) . ' น. (เวลาไทย): ' . $post->post_title;
		}
		if ( is_wp_error( $done ) ) {
			$log[] = '✗ ' . $post->post_name . ' · ' . $done->get_error_message();
			continue;
		}
		$after = fenix_find_post( $post->post_name );
		$got   = $after ? $after->post_status : 'ไม่พบ';
		$log[] = $want === $got ? $ok : '✗ ' . $post->post_name . ' · สถานะที่ได้คือ ' . $got . ' (ต้องการ ' . $want . ')';
	}
	return $log;
}

/**
 * ปล่อยบทความโดยระบุจำนวนที่ต้องการให้เผยแพร่อยู่ "รวม" (ไม่ใช่ "เพิ่มอีก N")
 * กดซ้ำหรือส่งฟอร์มซ้ำหลังสะดุดกลางทางจึงเติมให้ครบตามเป้าเท่านั้น ไม่เผยแพร่เกิน
 */
function fenix_setup_release_to_total( $want_live, $per_week = 2 ) {
	$have = fenix_setup_article_counts();
	return fenix_setup_release_articles( max( 0, (int) $want_live - (int) $have['publish'] ), $per_week );
}

/**
 * นับสถานะบทความเริ่มต้น:
 * array( 'publish' => n, 'future' => n, 'draft' => n, 'none' => n, 'next' => 'd/m/Y H:i' | '', 'last' => 'd/m/Y' | '' ) เวลาไทย
 */
function fenix_setup_article_counts() {
	$out  = array(
		'publish' => 0,
		'future'  => 0,
		'draft'   => 0,
		'none'    => 0,
		'next'    => '',
		'last'    => '',
	);
	$next = null;
	$last = null;
	$utc  = new DateTimeZone( 'UTC' );
	foreach ( array_keys( fenix_seed_article_covers() ) as $slug ) {
		$post = fenix_find_post( $slug );
		if ( ! $post ) {
			$out['none']++;
			continue;
		}
		$status = in_array( $post->post_status, array( 'publish', 'future' ), true ) ? $post->post_status : 'draft';
		$out[ $status ]++;
		if ( 'future' === $status ) {
			$at = date_create_immutable( $post->post_date_gmt, $utc );
			if ( $at && ( null === $next || $at < $next ) ) {
				$next = $at;
			}
			if ( $at && ( null === $last || $at > $last ) ) {
				$last = $at;
			}
		}
	}
	$bkk = new DateTimeZone( 'Asia/Bangkok' );
	if ( $next ) {
		$out['next'] = $next->setTimezone( $bkk )->format( 'd/m/Y H:i' );
	}
	if ( $last ) {
		$out['last'] = $last->setTimezone( $bkk )->format( 'd/m/Y' );
	}
	return $out;
}

/**
 * กันบทความตั้งเวลาค้าง ("Missed schedule") บนโฮสต์ที่ WP-Cron ไม่ทำงาน:
 * ทุก 15 นาที (เมื่อมีคนเปิดเว็บ) เผยแพร่บทความที่เลยเวลาตั้งไว้แล้วเกิน 5 นาที ด้วยฟังก์ชันเดียวกับที่ WP-Cron ใช้
 */
function fenix_publish_missed_schedule() {
	if ( wp_doing_cron() || get_transient( 'fenix_missed_schedule_check' ) ) {
		return;
	}
	set_transient( 'fenix_missed_schedule_check', 1, 15 * MINUTE_IN_SECONDS );
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$ids = (array) $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'future' AND post_date_gmt > '0000-00-00 00:00:00' AND post_date_gmt <= %s ORDER BY post_date_gmt ASC LIMIT 5", gmdate( 'Y-m-d H:i:s', time() - 5 * MINUTE_IN_SECONDS ) ) );
	foreach ( $ids as $id ) {
		check_and_publish_future_post( (int) $id );
	}
}
add_action( 'wp_loaded', 'fenix_publish_missed_schedule', 99 );

/**
 * ภาพปกของเพจ (assets/img/covers/page-<slug>.webp · เรนเดอร์ด้วย dev/make-covers.php) → ภาพเด่นของเพจ
 * ปลั๊กอิน SEO ใช้ภาพเด่นเป็นรูปตอนแชร์ลิงก์ (og:image) และรูปหลักของหน้าใน schema · page.php ไม่พิมพ์ภาพนี้ซ้ำในเนื้อหา
 * - ทำเฉพาะเพจที่ธีมมีไฟล์ภาพปก และเพจนั้นยังไม่มีภาพเด่น (ภาพที่เจ้าของเลือกเองไม่ถูกแทน)
 * - ไม่ทำกับ home / go (ใช้รูปแชร์กลางของเว็บ) · articles (หน้ารวมบทความ เป็นงานของส่วนบทความ) · เพจกฎหมาย 3 หน้า
 * - เพจที่เพิ่งได้ภาพเด่นถูกบันทึกซ้ำหนึ่งครั้งให้ Yoast อ่านรูปใหม่ วันที่ "ปรับปรุงล่าสุด" ของเพจนั้นจึงขยับเป็นวันที่กด
 *   (เพจอื่นไม่ถูกบันทึกซ้ำ กดกี่ครั้งวันที่ก็ไม่ขยับ)
 *
 * @return string[] log
 */
function fenix_setup_page_covers() {
	$skip  = array( 'home', 'go', 'articles', 'privacy-policy', 'terms-of-use', 'data-deletion' );
	$files = 0;
	$done  = array();
	$fail  = array();
	foreach ( fenix_site_pages() as $slug => $page ) {
		$file = 'covers/page-' . $slug . '.webp';
		if ( in_array( $slug, $skip, true ) || ! file_exists( get_template_directory() . '/assets/img/' . $file ) ) {
			continue;
		}
		$files++;
		$found = fenix_find_page( $slug );
		if ( ! $found || has_post_thumbnail( $found->ID ) ) {
			continue;
		}
		$thumb = fenix_banner_attachment( $file, wp_strip_all_tags( $found->post_title ) . ' · FALCON PRO EA' );
		if ( ! $thumb || ! set_post_thumbnail( $found->ID, $thumb ) ) {
			$fail[] = '/' . $slug . '/';
			continue;
		}
		wp_update_post( array( 'ID' => $found->ID ) );
		$done[] = '/' . $slug . '/';
	}
	if ( ! $files ) {
		return array( '• ภาพปกของเพจ: ธีมรุ่นนี้ยังไม่มีไฟล์ภาพปก (assets/img/covers/page-<slug>.webp) · ข้ามขั้นนี้' );
	}
	$log = array( $done ? '✓ ภาพปกของเพจ: ตั้งเป็นภาพเด่นให้ ' . implode( ' ', $done ) : '• ภาพปกของเพจ: ไม่มีเพจที่ต้องใส่เพิ่ม (มีภาพเด่นอยู่แล้ว หรือยังไม่ได้สร้างเพจ)' );
	if ( $fail ) {
		$log[] = '✗ ภาพปกของเพจ: นำเข้าคลังสื่อไม่สำเร็จ ' . implode( ' ', $fail ) . ' · ตรวจสิทธิ์เขียนโฟลเดอร์ uploads แล้วกดอีกครั้ง';
	}
	return $log;
}

/**
 * ค่าที่นับว่าเป็น "ค่าเริ่มต้นของ Yoast" ของช่องในกลุ่ม wpseo_titles (ช่องที่ยังไม่มีใครตั้งเอง)
 * ชื่อหน้าค้นหาและหน้า 404 เป็นข้อความที่ Yoast แปลตามภาษาเว็บ จึงถาม Yoast เองก่อน (WPSEO_Options::get_default)
 * แล้วเติมค่าภาษาอังกฤษที่รู้จักไว้เป็นทางสำรอง (เว็บที่บันทึกค่าไว้ตอนภาษายังเป็นอังกฤษ หรือ Yoast รุ่นที่ไม่มีเมธอดนี้)
 *
 * @return string[]
 */
function fenix_setup_yoast_defaults( $key ) {
	$known = array(
		'separator'          => array( 'sc-dash' ),
		'title-search-wpseo' => array( 'You searched for %%searchphrase%% %%page%% %%sep%% %%sitename%%' ),
		'title-404-wpseo'    => array( 'Page not found %%sep%% %%sitename%%' ),
	);
	$list  = isset( $known[ $key ] ) ? $known[ $key ] : array();
	if ( class_exists( 'WPSEO_Options' ) && is_callable( array( 'WPSEO_Options', 'get_default' ) ) ) {
		$default = WPSEO_Options::get_default( 'wpseo_titles', $key );
		if ( is_string( $default ) && '' !== trim( $default ) ) {
			$list[] = $default;
		}
	}
	return $list;
}

/**
 * ตั้งค่า SEO และระบบ (ปุ่มที่ 4 ของหน้า Setup) · กดซ้ำได้ ไม่แตะเนื้อหาเพจหรือบทความ · ต้องมีสิทธิ์ manage_options (ตรวจที่ fenix_setup_handle)
 * 1) WordPress: โพสต์/เพจที่สร้างใหม่ปิดคอมเมนต์และ pingback (ธีมปิดในโค้ดอยู่แล้ว ตั้งซ้ำไว้เผื่อวันที่เปลี่ยนธีม)
 * 2) คลังสื่อ: เติม alt ให้ไฟล์โลโก้ที่ alt ยังว่าง (ไอคอนเว็บ โลโก้ของเว็บ และไฟล์ที่ชื่อบอกว่าเป็นโลโก้ FALCON PRO)
 * 3) ภาพปกของเพจ → ภาพเด่น (fenix_setup_page_covers)
 * 4) Yoast SEO (เมื่อเปิดใช้): ชื่อเว็บ/ชื่อองค์กร ตัวคั่น ชื่อหน้าค้นหาและ 404 ปิด archive ที่เว็บไม่ใช้ ตัดฟีดคอมเมนต์และ shortlink
 *    ทุกค่าถูกอ่านกลับมาตรวจ (ชื่อช่องของ Yoast เปลี่ยนตามรุ่นได้) ค่าที่ไม่ติดถูกแจ้งใน log
 * สิ่งที่ตั้งใจไม่แตะ:
 * - ประเภทผู้เผยแพร่ (องค์กร/บุคคล) ชื่อเว็บ ชื่อองค์กร ตัวคั่นชื่อหน้า ชื่อหน้าค้นหา และชื่อหน้า 404 ของ Yoast ที่เจ้าของตั้งเองแล้ว:
 *   เขียนเฉพาะตอนที่ช่องยังว่าง ยังเป็นค่าเริ่มต้นของ Yoast (fenix_setup_yoast_defaults) หรือเป็นค่าของธีมอยู่แล้ว · ช่องที่ข้ามถูกแจ้งใน log
 * - รูปแชร์เริ่มต้นของ Yoast (og_default_image): ถ้ามีค่า fenix_seo_yoast_share_fallback() จะถอยให้ทันที ช่อง "รูปแชร์เริ่มต้น" ใน Customizer จะหมดผลเงียบ ๆ
 * - โลโก้องค์กรของ Yoast ที่มีค่าอยู่แล้ว (เติมเฉพาะตอนว่าง จากโลโก้ของเว็บก่อน แล้วจึงไอคอนเว็บ)
 * - คำอธิบายหมวดบทความ และข้อความท้ายฟีดบทความ (งานของส่วนบทความ)
 * - ชื่อหน้าหมวดบทความ: ตั้งเฉพาะเมื่อ $category_titles = true (ช่องติ๊กในฟอร์ม ค่าเริ่มต้นไม่ติ๊ก)
 *
 * @param bool $category_titles ตั้งรูปแบบชื่อและคำอธิบายของหน้าหมวดบทความใน Yoast ด้วย
 * @return string[] log
 */
function fenix_setup_seo( $category_titles = false ) {
	$log  = array();
	$name = trim( wp_specialchars_decode( (string) get_option( 'blogname' ), ENT_QUOTES ) );
	if ( '' === $name ) {
		$name = 'FALCON PRO EA';
	}

	/* 1) ค่าเริ่มต้นของ WordPress */
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'default_pingback_flag', 0 );
	$log[] = '✓ WordPress: โพสต์และเพจที่สร้างใหม่จะปิดคอมเมนต์และ pingback ตั้งแต่ต้น';

	/* 2) alt ของไฟล์โลโก้ */
	$logo_ids = array( (int) get_option( 'site_icon' ), (int) get_theme_mod( 'custom_logo' ) );
	$by_name  = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image',
			'posts_per_page' => 30,
			's'              => 'logo',
			'fields'         => 'ids',
		)
	);
	foreach ( (array) $by_name as $att ) {
		/* เฉพาะไฟล์ที่ชื่อมีทั้ง "falcon pro" และ "logo" · ภาพหน้าจอหรือแบนเนอร์ที่ชื่อขึ้นต้นเหมือนกันต้องไม่ได้ alt ของโลโก้ */
		if ( preg_match( '/falcon[-_ ]?pro.*logo|logo.*falcon[-_ ]?pro/i', (string) get_post_meta( $att, '_wp_attached_file', true ) . ' ' . get_the_title( $att ) ) ) {
			$logo_ids[] = (int) $att;
		}
	}
	$alt_done = 0;
	foreach ( array_filter( array_unique( $logo_ids ) ) as $att ) {
		if ( 'attachment' === get_post_type( $att ) && '' === trim( (string) get_post_meta( $att, '_wp_attachment_image_alt', true ) ) ) {
			update_post_meta( $att, '_wp_attachment_image_alt', wp_slash( 'โลโก้ ' . $name ) );
			$alt_done++;
		}
	}
	$log[] = $alt_done ? '✓ คลังสื่อ: เติมข้อความ alt ให้ไฟล์โลโก้ ' . $alt_done . ' ไฟล์' : '• คลังสื่อ: ไฟล์โลโก้มีข้อความ alt ครบแล้ว';

	/* 3) ภาพปกของเพจ */
	$log = array_merge( $log, fenix_setup_page_covers() );

	/* 4) Yoast SEO */
	if ( ! class_exists( 'WPSEO_Options' ) ) {
		$log[] = '• Yoast SEO: ไม่ได้เปิดใช้ปลั๊กอิน · ข้ามการตั้งค่าส่วนนี้ (ธีมพิมพ์ meta และ schema พื้นฐานเองอยู่แล้ว)';
		return $log;
	}
	$set = array(
		'disable-author'              => true,
		'disable-date'                => true,
		'disable-post_format'         => true,
		'remove_feed_global_comments' => true,
		'remove_feed_post_comments'   => true,
		'remove_shortlinks'           => true,
	);
	$notes = array();

	/* ช่องที่เจ้าของอาจตั้งเองใน Yoast: เขียนเฉพาะตอนที่ช่องยังว่าง ยังเป็นค่าเริ่มต้นของ Yoast หรือเป็นค่าของธีมอยู่แล้ว
	 * ช่องที่มีค่าอื่น = เจ้าของตั้งเอง ไม่แตะ และแจ้งชื่อช่องกับค่าปัจจุบันใน log */
	$guarded = array(
		'company_or_person'  => array( 'ผู้เผยแพร่แบบองค์กร', 'company' ), // ค่าเริ่มต้นของ Yoast คือ company อยู่แล้ว · เจ้าของที่เลือก person ไว้จะไม่ถูกสลับกลับ
		'website_name'       => array( 'ชื่อเว็บ', $name ),
		'company_name'       => array( 'ชื่อองค์กร', $name ),
		'separator'          => array( 'ตัวคั่นชื่อหน้า', 'sc-middot' ),
		'title-search-wpseo' => array( 'ชื่อหน้าค้นหา', 'ค้นหาคำว่า %%searchphrase%% %%page%% %%sep%% %%sitename%%' ),
		'title-404-wpseo'    => array( 'ชื่อหน้า 404', 'ไม่มีหน้านี้ในเว็บ %%sep%% %%sitename%%' ),
	);
	$squash  = static function ( $value ) {
		return trim( (string) preg_replace( '/\s+/u', ' ', (string) $value ) );
	};
	$applied = array();
	$kept    = array();
	foreach ( $guarded as $key => $row ) {
		$current = $squash( WPSEO_Options::get( $key ) );
		if ( '' === $current || $current === $squash( $row[1] ) || in_array( $current, array_map( $squash, fenix_setup_yoast_defaults( $key ) ), true ) ) {
			$set[ $key ] = $row[1];
			$applied[]   = $row[0];
		} else {
			$kept[] = $row[0] . ' (ค่าตอนนี้ "' . $current . '")';
		}
	}

	/* โลโก้องค์กร: เติมเฉพาะตอน Yoast ยังว่าง · โลโก้ของเว็บ (custom_logo) มาก่อน เพราะเป็นรูปที่ Yoast พิมพ์ใน schema อยู่แล้ว */
	if ( (int) WPSEO_Options::get( 'company_logo_id' ) || '' !== trim( (string) WPSEO_Options::get( 'company_logo' ) ) ) {
		$notes[] = 'โลโก้องค์กรมีค่าอยู่แล้ว ไม่เปลี่ยน';
	} else {
		$logo = (int) get_theme_mod( 'custom_logo' );
		if ( ! $logo ) {
			$logo = (int) get_option( 'site_icon' );
		}
		$logo_url = $logo ? (string) wp_get_attachment_url( $logo ) : '';
		if ( '' !== $logo_url ) {
			$set['company_logo']    = $logo_url;
			$set['company_logo_id'] = $logo;
			$notes[]                = 'โลโก้องค์กร = โลโก้ของเว็บ';
		} else {
			$notes[] = 'ยังไม่มีโลโก้ของเว็บหรือไอคอนเว็บให้ใช้เป็นโลโก้องค์กร (ตั้งที่ ปรับแต่ง → Site Identity แล้วกดปุ่มนี้อีกครั้ง)';
		}
	}

	/* Facebook: เฉพาะเมื่อกรอกลิงก์เพจของ FALCON ไว้ใน Customizer */
	$facebook = trim( (string) fenix_mod( 'facebook_url' ) );
	if ( '' !== $facebook && '#' !== $facebook ) {
		$set['facebook_site'] = esc_url_raw( $facebook );
	} else {
		$notes[] = 'ยังไม่ได้กรอกลิงก์ Facebook ใน Customizer จึงไม่ตั้งช่อง Facebook';
	}

	/* หน้าหมวดบทความ (ตัวเลือก): แทนชื่อแบบ "… Archives" ด้วยชื่อหมวดภาษาไทย · ไม่แก้ชื่อหรือคำอธิบายของหมวดใด */
	if ( $category_titles ) {
		$set['title-tax-category']    = 'หมวดหมู่ %%term_title%% %%page%% %%sep%% %%sitename%%';
		$set['metadesc-tax-category'] = '%%term_description%%';
	} else {
		$notes[] = 'ไม่ได้ติ๊กช่องหน้าหมวดบทความ จึงไม่แตะชื่อหน้าหมวด';
	}

	$failed = array();
	foreach ( $set as $key => $value ) {
		WPSEO_Options::set( $key, $value );
		/* ตรวจด้วยการอ่านกลับ: ผลของ set() ใช้ไม่ได้ (เป็น false เมื่อค่าเดิมเท่ากัน) และ Yoast อาจปรับค่าหรือไม่รู้จักชื่อช่อง */
		if ( (string) WPSEO_Options::get( $key ) !== (string) $value ) {
			$failed[] = $key;
		}
	}
	/* สรุปเฉพาะสิ่งที่เขียนจริงในรอบนี้ · ชื่อที่ใช้ถูกบอกเมื่อช่องชื่อเว็บหรือชื่อองค์กรถูกเขียนเท่านั้น */
	$summary = $applied ? 'ค่าของธีมในช่อง ' . implode( ', ', $applied ) . ( array_intersect( array( 'ชื่อเว็บ', 'ชื่อองค์กร' ), $applied ) ? ' (ชื่อที่ใช้คือ "' . $name . '")' : '' ) . ' · ' : '';
	$log[]   = ( $failed ? '• ' : '✓ ' ) . 'Yoast SEO: บันทึกและอ่านกลับตรงกัน ' . ( count( $set ) - count( $failed ) ) . ' จาก ' . count( $set ) . ' ช่อง (' . $summary . 'ปิดหน้ารวมตามผู้เขียน วันที่ และรูปแบบโพสต์ · ตัดฟีดคอมเมนต์และ shortlink)';
	if ( $kept ) {
		$log[] = '• Yoast SEO: ไม่เปลี่ยนช่องที่มีค่าที่ตั้งเองอยู่แล้ว ' . count( $kept ) . ' ช่อง: ' . implode( ' · ', $kept ) . ' · ถ้าต้องการค่าของธีมในช่องใด ให้ตั้งช่องนั้นเองที่หน้า Yoast SEO → Settings';
	}
	if ( $notes ) {
		$log[] = '• Yoast SEO: ' . implode( ' · ', $notes );
	}
	if ( $failed ) {
		$log[] = '✗ Yoast SEO ไม่รับค่าของช่อง: ' . implode( ', ', $failed ) . ' · รุ่นของปลั๊กอินอาจใช้ชื่อช่องต่างออกไป ให้ตั้งช่องนี้เองในหน้า Yoast SEO → Settings';
	}
	return $log;
}

/**
 * แทนเนื้อหาเพจเดียวด้วยเนื้อหาตั้งต้นรุ่นล่าสุด (เก็บรุ่นเก่าเป็น revision ของ WP)
 * ชื่อเพจถูกแทนด้วยเมื่อไฟล์ระบุ title: ชื่อเพจคือ H1 ของหน้าคู่มือและเพจเอกสาร เนื้อหารุ่นใหม่จึงต้องมากับหัวเรื่องของรุ่นเดียวกัน
 */
function fenix_setup_replace_page( $slug ) {
	$pages = fenix_site_pages();
	if ( ! isset( $pages[ $slug ] ) || empty( $pages[ $slug ]['content'] ) ) {
		return array( '✗ ไม่พบเนื้อหาตั้งต้นของ /' . $slug . '/' );
	}
	$found = fenix_find_page( $slug );
	if ( ! $found ) {
		return array( '✗ ยังไม่มีเพจ /' . $slug . '/ · กด "สร้างเพจ" ก่อน' );
	}
	$meta   = fenix_seed_meta( $pages[ $slug ]['content'] );
	$update = array(
		'ID'           => $found->ID,
		'post_content' => wp_slash( fenix_setup_page_content( $pages[ $slug ]['content'] ) ),
	);
	$retitled = ! empty( $meta['title'] ) && (string) $meta['title'] !== (string) $found->post_title;
	if ( ! empty( $meta['title'] ) ) {
		$update['post_title'] = wp_slash( $meta['title'] );
	}
	wp_update_post( $update );
	update_post_meta( $found->ID, 'fenix_seed_rev', ! empty( $meta['rev'] ) ? (int) $meta['rev'] : 1 );
	if ( $meta ) {
		fenix_apply_seo_meta( $found->ID, $meta );
	}
	return array( '↻ แทนเนื้อหาและค่า SEO ของ /' . $slug . '/ ด้วยรุ่นล่าสุดแล้ว' . ( $retitled ? ' และเปลี่ยนชื่อเพจเป็น "' . $meta['title'] . '"' : '' ) . ' (เนื้อหารุ่นเดิมอยู่ใน Revisions ของเพจ)' );
}

/**
 * บรรทัดปฏิเสธของปุ่มที่ต้องมีสิทธิ์ผู้ดูแลระบบ (manage_options) · $do = ค่า fenix_do ของปุ่ม
 * ทุกปุ่มของหน้า Setup ยกเว้น "สร้างเมนูอย่างเดียว" อยู่ในกลุ่มนี้: สร้าง เผยแพร่ หรือเขียนทับเพจและบทความ
 * และเปลี่ยนค่าหน้าแรก / หน้าบทความ ซึ่ง WordPress สงวนให้ผู้ดูแลระบบ (เมนูเป็นงานของสิทธิ์ edit_theme_options อยู่แล้ว)
 */
function fenix_setup_refusal( $do ) {
	$names = array(
		'all'      => 'ตั้งค่าเว็บทั้งหมด',
		'pages'    => 'สร้างเพจอย่างเดียว',
		'reading'  => 'ตั้งหน้าแรกและหน้าบทความ',
		'replace'  => 'แทนด้วยรุ่นล่าสุด',
		'articles' => 'นำเข้าบทความ',
		'release'  => 'เผยแพร่และตั้งเวลา',
		'seo'      => 'ตั้งค่า SEO และระบบ',
	);
	return '✗ ' . ( isset( $names[ $do ] ) ? '"' . $names[ $do ] . '" ' : 'คำสั่งนี้' ) . 'ทำได้เฉพาะผู้ดูแลระบบ (สิทธิ์ manage_options) · ไม่ได้เปลี่ยนค่าใด';
}

function fenix_setup_handle() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( 'ไม่มีสิทธิ์' );
	}
	check_admin_referer( 'fenix_setup' );

	$do = isset( $_POST['fenix_do'] ) ? sanitize_key( wp_unslash( $_POST['fenix_do'] ) ) : '';
	// ผู้ที่มีแค่ edit_theme_options เปิดหน้านี้ดูตารางสถานะและสร้างเมนูได้ ปุ่มอื่นทุกปุ่มต้องมีสิทธิ์ manage_options
	// ตรวจก่อนตั้งตัวล็อก: คำขอที่ถูกปฏิเสธต้องไม่ทิ้งล็อกค้างและไม่แตะค่าใด
	if ( 'menu' !== $do && ! current_user_can( 'manage_options' ) ) {
		set_transient( 'fenix_setup_log', array( fenix_setup_refusal( $do ) ), 120 );
		wp_safe_redirect( admin_url( 'themes.php?page=falcon-setup' ) );
		exit;
	}

	// กันกดซ้ำขณะกำลังทำงาน (ดับเบิลคลิก / สองแท็บ)
	if ( get_transient( 'fenix_setup_lock' ) ) {
		set_transient( 'fenix_setup_log', array( '• กำลังทำงานอยู่ · รอสักครู่แล้วรีเฟรชหน้านี้' ), 120 );
		wp_safe_redirect( admin_url( 'themes.php?page=falcon-setup' ) );
		exit;
	}
	set_transient( 'fenix_setup_lock', 1, 300 );
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // นำเข้า/ปล่อยบทความ 20+ เรื่องพร้อมรูปปกอาจใช้เวลา
	}
	ignore_user_abort( true );

	$log = array();

	if ( 'pages' === $do || 'all' === $do ) {
		$log = array_merge( $log, fenix_setup_create_pages( ! empty( $_POST['fenix_overwrite'] ) ) );
	}
	if ( 'reading' === $do || 'all' === $do ) {
		$log = array_merge( $log, fenix_setup_reading() );
	}
	if ( 'menu' === $do || 'all' === $do ) {
		$log = array_merge( $log, fenix_setup_menu_build() );
	}
	if ( 'replace' === $do ) {
		$slug = isset( $_POST['fenix_slug'] ) ? sanitize_key( wp_unslash( $_POST['fenix_slug'] ) ) : '';
		// ต้องติ๊ก "ยืนยัน" ข้างปุ่มก่อน (แทนกล่อง confirm ของเบราว์เซอร์ ใช้ได้แม้ปิด JavaScript)
		$log  = array_merge( $log, empty( $_POST['fenix_confirm'] ) ? array( '• ยังไม่ได้ติ๊ก "ยืนยัน" · ไม่ได้แทนเนื้อหา /' . $slug . '/' ) : fenix_setup_replace_page( $slug ) );
	}
	if ( 'articles' === $do ) {
		$log = array_merge( $log, fenix_setup_import_articles( ! empty( $_POST['fenix_publish'] ) ) );
	}
	if ( 'release' === $do ) {
		// ตัวเลขในฟอร์ม = จำนวนบทความที่ต้องการให้เผยแพร่อยู่ "รวม" · ส่งฟอร์มซ้ำหรือกดซ้ำหลังสะดุดจึงไม่เผยแพร่เกินที่ตั้งใจ
		$want = isset( $_POST['fenix_now'] ) ? absint( wp_unslash( $_POST['fenix_now'] ) ) : 6;
		$week = isset( $_POST['fenix_per_week'] ) ? absint( wp_unslash( $_POST['fenix_per_week'] ) ) : 2;
		$log  = array_merge( $log, fenix_setup_release_to_total( $want, $week ) );
	}
	if ( 'seo' === $do ) {
		// ปุ่มนี้เขียนค่าระบบของ WordPress และค่าของ Yoast SEO ซึ่ง WordPress สงวนให้ผู้ดูแลระบบ: ต้องมีสิทธิ์ manage_options
		// (ด่านบนสุดของฟังก์ชันปฏิเสธไปแล้ว ตรวจซ้ำตรงนี้เผื่อด่านนั้นถูกแก้ในวันหลัง)
		if ( current_user_can( 'manage_options' ) ) {
			// ช่องติ๊ก "หน้าหมวดบทความ" ไม่ติ๊กเป็นค่าเริ่มต้น: ไม่ติ๊ก = ไม่แตะรูปแบบชื่อหน้าหมวดใน Yoast
			$log = array_merge( $log, fenix_setup_seo( ! empty( $_POST['fenix_seo_category'] ) ) );
		} else {
			$log[] = fenix_setup_refusal( 'seo' );
		}
	}
	if ( 'all' === $do ) {
		flush_rewrite_rules( false );
	}

	delete_transient( 'fenix_setup_lock' );
	set_transient( 'fenix_setup_log', $log, 120 );
	wp_safe_redirect( admin_url( 'themes.php?page=falcon-setup&done=1' ) );
	exit;
}
add_action( 'admin_post_fenix_setup', 'fenix_setup_handle' );

/**
 * บรรทัดวันที่มีผลและเลขรุ่นของเอกสาร (<p class="doc-meta"> บรรทัดบนสุดของเพจนโยบายและข้อกำหนด) เป็นข้อความล้วน
 * ใช้ในตารางสถานะของหน้า Setup ให้เจ้าของเห็นว่าเพจที่กำลังจะเผยแพร่ระบุวันที่และรุ่นอะไร · ไม่มีบรรทัดนี้ = ''
 */
function fenix_setup_doc_meta_line( $content ) {
	if ( ! preg_match( '#<p\b[^>]*\bclass="[^"]*\bdoc-meta\b[^"]*"[^>]*>(.*?)</p>#is', (string) $content, $m ) ) {
		return '';
	}
	$text = (string) preg_replace( '#<br\s*/?>#i', ' · ', $m[1] );
	return trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $text ) ) );
}

function fenix_setup_screen() {
	$log   = get_transient( 'fenix_setup_log' );
	$pages = fenix_site_pages();
	delete_transient( 'fenix_setup_log' );
	/* ลิงก์สมัครบัญชีถูกบันทึกแล้ว แต่พาร์ตเนอร์ยังตั้งไม่ครบ (ยังไม่มีชื่อโบรกเกอร์จริง): ปุ่มสมัครและข้อความเปิดเผยถูกซ่อนอยู่ */
	$fenix_partner_pending = function_exists( 'fenix_partner_pending' ) && fenix_partner_pending();
	/* ทุกปุ่มยกเว้น "สร้างเมนูอย่างเดียว" ต้องมีสิทธิ์ manage_options (ตรวจจริงที่ fenix_setup_handle) · หน้านี้ยังเปิดให้ผู้มีสิทธิ์แก้ธีมดูตารางสถานะได้ */
	$fenix_can_manage = current_user_can( 'manage_options' );
	?>
	<div class="wrap">
		<h1>FALCON PRO EA · ตั้งค่าเว็บ</h1>
		<p>สร้างเพจทั้งหมดของเว็บพร้อมเทมเพลตและเนื้อหาเริ่มต้นในคลิกเดียว เพจที่มีอยู่แล้วจะ<strong>ไม่ถูกแก้ไข</strong> (เว้นแต่ติ๊กเขียนทับ) · ข้อยกเว้นเดียว: เพจ Privacy Policy ฉบับร่างที่ WordPress สร้างไว้ตอนติดตั้งและยังไม่เคยถูกแก้ จะถูกแทนด้วยฉบับของธีมหนึ่งครั้ง</p>

		<?php if ( ! $fenix_can_manage ) : ?>
			<div class="notice notice-info"><p><strong>บัญชีนี้ไม่มีสิทธิ์ผู้ดูแลระบบ (manage_options)</strong> · ดูตารางสถานะเพจและกด "สร้างเมนูอย่างเดียว" ได้ ส่วนปุ่มอื่นในหน้านี้สร้าง เผยแพร่ หรือเขียนทับเพจและบทความ และเปลี่ยนค่าหน้าแรกของ WordPress จึงทำได้เฉพาะผู้ดูแลระบบ คำขอจากบัญชีนี้จะถูกปฏิเสธโดยไม่เปลี่ยนค่าใด</p></div>
		<?php endif; ?>

		<?php if ( ! get_option( 'blog_public' ) ) : ?>
			<div class="notice notice-warning"><p><strong>เว็บยังปิดไม่ให้ Google ทำดัชนี</strong> · ไปที่ <a href="<?php echo esc_url( admin_url( 'options-reading.php' ) ); ?>">ตั้งค่า → การอ่าน</a> แล้วเอาเครื่องหมายออกจาก "ขอให้ search engines ไม่ทำดัชนีเว็บไซต์นี้" เมื่อพร้อมเปิดตัว</p></div>
		<?php endif; ?>
		<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
			<div class="notice notice-warning"><p><strong>ลิงก์ถาวรยังเป็นแบบ ?p=</strong> · ไปที่ <a href="<?php echo esc_url( admin_url( 'options-permalink.php' ) ); ?>">ตั้งค่า → ลิงก์ถาวร</a> แล้วเลือก "ชื่อเรื่อง (Post name)"</p></div>
		<?php endif; ?>
		<?php if ( '#' === fenix_mod( 'line_url' ) || ! fenix_mod( 'line_url' ) ) : ?>
			<div class="notice notice-warning"><p><strong>ยังไม่ได้ใส่ลิงก์ LINE OA</strong> · ปุ่มติดต่อทุกปุ่มจะพาไปหน้า /go/ แทน (ถ้าหน้า /go/ ยังไม่เผยแพร่ ปุ่มจะถูกซ่อน) · ตั้งค่าที่ <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=fenix_general' ) ); ?>">ปรับแต่ง → ช่องทางติดต่อ</a></p></div>
		<?php endif; ?>
		<?php if ( $fenix_partner_pending ) : ?>
			<div class="notice notice-warning"><p><strong>ใส่ลิงก์สมัครบัญชีของโบรกเกอร์แล้ว แต่ยังไม่ได้ตั้งชื่อโบรกเกอร์</strong> · ตอนนี้ปุ่มสมัคร ข้อความเปิดเผยความเป็นพาร์ตเนอร์ และย่อหน้าเรื่องพาร์ตเนอร์ในเพจเอกสารยังไม่แสดงบนเว็บ (ธีมไม่แสดงปุ่มลิงก์พาร์ตเนอร์โดยไม่มีชื่อโบรกเกอร์และข้อความเปิดเผย) · กรอกชื่อโบรกเกอร์จริงและชื่อเซิร์ฟเวอร์ที่ <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=fenix_broker' ) ); ?>">ปรับแต่ง → 29) โบรกเกอร์</a> แล้วเปิดหน้าคู่มือเปิดบัญชีอ่านข้อความเปิดเผยอีกรอบ</p></div>
		<?php endif; ?>

		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><?php echo implode( '<br>', array_map( 'esc_html', (array) $log ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:18px 0 26px;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;max-width:760px">
			<?php wp_nonce_field( 'fenix_setup' ); ?>
			<input type="hidden" name="action" value="fenix_setup">
			<h2 style="margin-top:0">1) ตั้งค่าเว็บทั้งหมด</h2>
			<p>สร้างเพจที่ยังไม่มี → ตั้งหน้าแรก/หน้าบทความ → สร้างเมนูหลัก</p>
			<p><label><input type="checkbox" name="fenix_overwrite" value="1"<?php disabled( $fenix_can_manage, false ); ?>> เขียนทับเพจที่มีอยู่ด้วยค่าเริ่มต้นของธีม: <strong>ชื่อเพจ เนื้อหา และค่า SEO</strong> (ชื่อ SEO คำอธิบาย และคีย์เวิร์ดหลักในช่องของ Yoast) ของทุกเพจ · หน้าแรก /go/ และ /articles/ ไม่มีไฟล์เนื้อหา จึงถูกแทนเฉพาะค่า SEO · ระวัง: ข้อความ ชื่อเพจ และค่า SEO ที่แก้ไว้เองจะหายทุกเพจ และค่า SEO เดิมไม่ถูกเก็บใน Revisions ของเพจ</label></p>
			<p><button class="button button-primary" name="fenix_do" value="all"<?php disabled( $fenix_can_manage, false ); ?>>ตั้งค่าเว็บทั้งหมด</button>
				<button class="button" name="fenix_do" value="pages"<?php disabled( $fenix_can_manage, false ); ?>>สร้างเพจอย่างเดียว</button>
				<button class="button" name="fenix_do" value="menu">สร้างเมนูอย่างเดียว</button></p>
			<p style="margin:16px 0 4px"><strong>ลำดับงานหลังอัปเดตธีมเป็นรุ่นใหม่</strong></p>
			<ol style="margin:0 0 10px 20px">
				<li>อัปเดตธีมบนเว็บให้เป็นรุ่นล่าสุดก่อน แล้วเปิดหน้านี้ใหม่</li>
				<li>กรอกค่าใน <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">ปรับแต่ง</a> ให้ครบ: ช่องทางติดต่อ โบรกเกอร์ และหมวดที่ธีมรุ่นใหม่เพิ่มเข้ามา · ค่าที่เคยบันทึกไว้ไม่เปลี่ยนตามค่าเริ่มต้นใหม่ของธีม ต้องแก้เอง</li>
				<li>ติ๊กช่องเขียนทับด้านบน แล้วกด "ตั้งค่าเว็บทั้งหมด": เพจใหม่ถูกสร้าง ส่วนเพจเดิมได้ชื่อเพจ เนื้อหา และค่า SEO ของรุ่นล่าสุด</li>
				<li>กด "ตั้งค่า SEO และระบบ" ในข้อ 4</li>
				<li>เพิ่มเพจที่เพิ่งสร้างเข้าเมนูเองที่ <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">รูปแบบ → เมนู</a> ("สร้างเมนูอย่างเดียว" บอกว่าเพจไหนยังไม่อยู่ในเมนู)</li>
				<li>อ่านตรวจเพจกฎหมายฉบับร่าง แก้วันที่มีผลกับเลขรุ่นให้ตรงวันเผยแพร่จริง แล้วกดเผยแพร่</li>
			</ol>
			<p style="color:#50575e">มีเพจที่แก้ข้อความเองและต้องการเก็บไว้ (รวมถึงเพจกฎหมายที่แก้วันที่มีผลหรือเลขรุ่นไว้ตามข้อ 6): อย่าติ๊กเขียนทับ กดปุ่มโดยไม่ติ๊ก (สร้างเฉพาะเพจที่ยังไม่มี) แล้วใช้ "แทนด้วยรุ่นล่าสุด" ในตารางสถานะเพจทีละเพจ · หน้าแรก /go/ และ /articles/ ไม่มีปุ่มนั้น ค่า SEO ของสามเพจนี้ถูกเขียนเฉพาะตอนสร้างเพจและตอนติ๊กเขียนทับ ถ้าไม่ติ๊กให้แก้ในช่อง Yoast ของเพจเอง</p>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0 0 26px;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;max-width:760px">
			<?php wp_nonce_field( 'fenix_setup' ); ?>
			<input type="hidden" name="action" value="fenix_setup">
			<h2 style="margin-top:0">2) นำเข้าบทความ SEO (<?php echo (int) count( fenix_seed_articles() ); ?> บทความ)</h2>
			<p>นำเข้าเป็น <strong>ฉบับร่าง</strong> พร้อมหมวดหมู่ คำอธิบาย SEO และรูปหน้าปก · กดซ้ำได้ บทความที่มีอยู่แล้วจะไม่ถูกสร้างซ้ำ และฉบับร่างที่ยังไม่เคยแก้จะอัปเดตตามไฟล์รุ่นล่าสุดของธีม</p>
			<p><label><input type="checkbox" name="fenix_publish" value="1"> เผยแพร่ทันที (ไม่แนะนำ)</label></p>
			<p><button class="button button-primary" name="fenix_do" value="articles"<?php disabled( $fenix_can_manage, false ); ?>>นำเข้าบทความ</button></p>
		</form>

		<?php $fenix_counts = fenix_setup_article_counts(); ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0 0 26px;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;max-width:760px">
			<?php wp_nonce_field( 'fenix_setup' ); ?>
			<input type="hidden" name="action" value="fenix_setup">
			<h2 style="margin-top:0">3) ปล่อยบทความฉบับร่าง</h2>
			<p>เผยแพร่ชุดแรกทันที แล้วตั้งเวลาให้บทความที่เหลือออกเองทุกสัปดาห์ เวลา 10:00 น. (เวลาไทย) · ทำกับบทความของธีมที่ยังเป็น<strong>ฉบับร่าง</strong>เท่านั้น · ฉบับร่างที่ยังไม่เคยแก้จะอัปเดตตามไฟล์รุ่นล่าสุดของธีมก่อนปล่อย ส่วนฉบับร่างที่คุณแก้เองจะปล่อยตามที่แก้ไว้ · ลิงก์ไปบทความที่ยังไม่ถึงคิวจะถูกซ่อนไว้จนกว่าบทความนั้นจะออก</p>
			<p>
				<strong>สถานะตอนนี้:</strong>
				เผยแพร่แล้ว <?php echo (int) $fenix_counts['publish']; ?> ·
				ตั้งเวลาไว้ <?php echo (int) $fenix_counts['future']; ?><?php echo $fenix_counts['next'] ? ' (เรื่องถัดไป ' . esc_html( $fenix_counts['next'] ) . ' น. · เรื่องสุดท้าย ' . esc_html( $fenix_counts['last'] ) . ')' : ''; ?> ·
				ฉบับร่าง <?php echo (int) $fenix_counts['draft']; ?>
				<?php if ( $fenix_counts['none'] ) : ?>
					· ยังไม่ได้นำเข้า <?php echo (int) $fenix_counts['none']; ?>
				<?php endif; ?>
			</p>
			<p>
				<label>ให้มีบทความเผยแพร่แล้วรวม <input type="number" name="fenix_now" value="<?php echo (int) max( 6, $fenix_counts['publish'] ); ?>" min="0" max="100" style="width:70px"> เรื่อง</label>
				&nbsp;·&nbsp;
				<label>ที่เหลือสัปดาห์ละ
					<select name="fenix_per_week">
						<option value="1">1 เรื่อง (วันอังคาร)</option>
						<option value="2" selected>2 เรื่อง (อังคาร และ ศุกร์)</option>
						<option value="3">3 เรื่อง (จันทร์ พุธ ศุกร์)</option>
					</select>
				</label>
			</p>
			<p><button class="button button-primary" name="fenix_do" value="release"<?php disabled( ! $fenix_can_manage || 0 === (int) $fenix_counts['draft'] ); ?>>เผยแพร่และตั้งเวลา</button></p>
		</form>

		<?php if ( ! current_user_can( 'manage_options' ) ) : ?>
			<div style="margin:0 0 26px;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;max-width:760px">
				<h2 style="margin-top:0">4) SEO และระบบ</h2>
				<p>ขั้นนี้เปลี่ยนค่าระบบของ WordPress และค่าของ Yoast SEO จึงทำได้เฉพาะบัญชีผู้ดูแลระบบ (สิทธิ์ manage_options) · บัญชีที่ใช้อยู่ตอนนี้ไม่มีสิทธิ์นั้น</p>
			</div>
		<?php else : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0 0 26px;padding:18px 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;max-width:760px">
			<?php wp_nonce_field( 'fenix_setup' ); ?>
			<input type="hidden" name="action" value="fenix_setup">
			<h2 style="margin-top:0">4) SEO และระบบ</h2>
			<p>งานตั้งค่าที่ทำครั้งเดียวหลังติดตั้งธีม · กดซ้ำได้ ไม่ทับค่าที่คุณตั้งเองใน Yoast และไม่แก้ข้อความในเพจหรือบทความ</p>
			<ul style="list-style:disc;margin:0 0 12px 20px">
				<li><strong>Yoast SEO</strong> (ถ้าเปิดใช้): ผู้เผยแพร่แบบองค์กร พร้อมชื่อเว็บและชื่อองค์กร · ตัวคั่นชื่อหน้าเป็นจุดกลาง · ชื่อหน้าค้นหาและหน้า 404 เป็นภาษาไทย (ช่องกลุ่มนี้ตั้งให้เฉพาะช่องที่ยังว่างหรือยังเป็นค่าเริ่มต้นของ Yoast ช่องที่คุณตั้งเองแล้วไม่ถูกแตะ และผลลัพธ์จะบอกว่าข้ามช่องไหน) · ปิดหน้ารวมตามผู้เขียน ตามวันที่ และตามรูปแบบโพสต์ · ตัดฟีดคอมเมนต์และ shortlink · ลิงก์ Facebook เมื่อกรอกไว้ใน Customizer · โลโก้องค์กรจะเติมให้เฉพาะตอนที่ Yoast ยังว่าง</li>
				<li><strong>WordPress</strong>: โพสต์และเพจที่สร้างใหม่ปิดคอมเมนต์และ pingback ตั้งแต่ต้น</li>
				<li><strong>คลังสื่อ</strong>: เติมข้อความ alt ให้ไฟล์โลโก้ที่ยังไม่มี</li>
				<li><strong>ภาพปกของเพจ</strong>: ตั้งเป็นภาพเด่นให้เพจที่ยังไม่มีภาพเด่น เพื่อใช้เป็นรูปตอนแชร์ลิงก์ (เว้นหน้าแรก หน้า /go/ หน้ารวมบทความ และเพจกฎหมาย) · เพจที่เพิ่งได้ภาพจะมีวันที่ปรับปรุงล่าสุดเป็นวันนี้</li>
			</ul>
			<p style="color:#50575e">ปุ่มนี้ไม่ตั้ง "รูปแชร์เริ่มต้น" ใน Yoast · รูปแชร์กลางของเว็บยังมาจากช่อง "รูปสำหรับแชร์" ในหมวด SEO ของ Customizer ตามเดิม</p>
			<p><label><input type="checkbox" name="fenix_seo_category" value="1"> ตั้งชื่อหน้าหมวดบทความใน Yoast เป็นรูปแบบภาษาไทยด้วย (แทนคำว่า Archives ต่อท้ายชื่อหมวด) · ไม่ติ๊ก = ไม่แตะหน้าหมวดบทความ</label></p>
			<p><button class="button button-primary" name="fenix_do" value="seo">ตั้งค่า SEO และระบบ</button></p>
		</form>
		<?php endif; ?>

		<h2>สถานะเพจ</h2>
		<table class="widefat striped" style="max-width:980px">
			<thead><tr><th>URL</th><th>ชื่อเพจ</th><th>สถานะ</th><th>เทมเพลต</th><th>เนื้อหาตั้งต้น</th><th></th></tr></thead>
			<tbody>
			<?php
			$fenix_stale_drafts = array();
			foreach ( $pages as $slug => $page ) :
				$found = fenix_find_page( $slug );
				$tpl   = $found ? get_post_meta( $found->ID, '_wp_page_template', true ) : '';
				$ok    = ! $page['template'] || $tpl === $page['template'];
				$smeta = ! empty( $page['content'] ) ? fenix_seed_meta( $page['content'] ) : null;
				$srev  = $smeta ? ( ! empty( $smeta['rev'] ) ? (int) $smeta['rev'] : 1 ) : 0;
				$prev  = $found ? (int) get_post_meta( $found->ID, 'fenix_seed_rev', true ) : 0;
				$stale = $found && $srev && $prev < $srev;
				/* ฉบับร่างที่เนื้อหายังเป็นรุ่นเก่ากว่าไฟล์ของธีม: ถ้ากดเผยแพร่ทั้งอย่างนี้ ข้อความ วันที่ และโน้ตถึงผู้ดูแลของรุ่นก่อนจะขึ้นเว็บ */
				$draft_stale = $stale && 'publish' !== $found->post_status;
				if ( $draft_stale ) {
					$fenix_stale_drafts[] = $slug;
				}
				/* เพจเอกสาร: บรรทัดวันที่มีผลและเลขรุ่นที่อยู่ในเพจตอนนี้ (สิ่งที่จะขึ้นเว็บเมื่อกดเผยแพร่) และของไฟล์รุ่นล่าสุดเมื่อเพจยังเป็นรุ่นเก่า */
				$doc_now  = '';
				$doc_seed = '';
				if ( $found && $smeta && 'doc' === $page['group'] ) {
					$doc_now  = fenix_setup_doc_meta_line( $found->post_content );
					$doc_seed = fenix_setup_doc_meta_line( fenix_seed_content( $page['content'] ) );
				}
				?>
				<tr>
					<td><code>/<?php echo esc_html( $slug ); ?>/</code></td>
					<td>
						<?php echo esc_html( $found ? $found->post_title : $page['title'] ); ?>
						<?php if ( '' !== $doc_now ) : ?>
							<br><span style="color:#50575e">ในเพจตอนนี้ · <?php echo esc_html( $doc_now ); ?></span>
						<?php elseif ( '' !== $doc_seed ) : ?>
							<br><span style="color:#b32d2e">ในเพจตอนนี้ไม่มีบรรทัดวันที่มีผลและเลขรุ่นของเอกสาร</span>
						<?php endif; ?>
						<?php if ( $stale && '' !== $doc_seed && $doc_seed !== $doc_now ) : ?>
							<br><span style="color:#50575e">ในรุ่นล่าสุดของธีม · <?php echo esc_html( $doc_seed ); ?></span>
						<?php endif; ?>
					</td>
					<td><?php echo $found ? esc_html( 'publish' === $found->post_status ? 'เผยแพร่' : 'ฉบับร่าง' ) : '<span style="color:#b32d2e">ยังไม่มี</span>'; ?></td>
					<td><?php echo $page['template'] ? ( $ok ? '✓ ' : '<span style="color:#b32d2e">✗ </span>' ) . esc_html( $page['template'] ) : '—'; ?></td>
					<td>
						<?php if ( ! $srev ) : ?>—<?php elseif ( ! $found ) : ?>รุ่น <?php echo (int) $srev; ?><?php elseif ( $stale ) : ?>
							<?php if ( $draft_stale ) : ?>
								<strong style="display:block;margin-bottom:4px;color:#b32d2e">ฉบับร่างนี้เป็นรุ่นเก่า · แทนด้วยรุ่นล่าสุดก่อนเผยแพร่</strong>
							<?php endif; ?>
							<span style="color:#b26c09">มีรุ่นใหม่ (<?php echo (int) $prev; ?> → <?php echo (int) $srev; ?>)</span>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;margin-left:6px">
								<?php wp_nonce_field( 'fenix_setup' ); ?>
								<input type="hidden" name="action" value="fenix_setup">
								<input type="hidden" name="fenix_slug" value="<?php echo esc_attr( $slug ); ?>">
								<label style="white-space:nowrap" title="ข้อความ ชื่อเพจ และค่า SEO (ชื่อ SEO คำอธิบาย คีย์เวิร์ดหลัก) ที่แก้เองในเพจนี้จะถูกแทน · ข้อความและชื่อเพจรุ่นเดิมยังอยู่ใน Revisions ของเพจ ส่วนค่า SEO เดิมไม่ถูกเก็บ"><input type="checkbox" name="fenix_confirm" value="1" required<?php disabled( $fenix_can_manage, false ); ?>> ยืนยัน</label>
								<button class="button button-small" name="fenix_do" value="replace"<?php disabled( $fenix_can_manage, false ); ?>>แทนด้วยรุ่นล่าสุด</button>
							</form>
						<?php else : ?>✓ รุ่น <?php echo (int) $srev; ?><?php endif; ?>
					</td>
					<td><?php if ( $found ) : ?><a href="<?php echo esc_url( get_edit_post_link( $found->ID ) ); ?>">แก้ไข</a> · <a href="<?php echo esc_url( get_permalink( $found ) ); ?>" target="_blank" rel="noopener">ดู</a><?php endif; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		$fenix_pending = array_diff( array_keys( fenix_site_pages( true ) ), array_keys( $pages ) );
		if ( $fenix_pending ) :
			?>
			<p style="max-width:980px;color:#b26c09">เพจที่ธีมเตรียมไว้แต่ยังไม่มีไฟล์เนื้อหาในรุ่นนี้ (ยังไม่ถูกสร้างและยังไม่ขึ้นเมนู): <code>/<?php echo esc_html( implode( '/ /', $fenix_pending ) ); ?>/</code></p>
		<?php endif; ?>
		<?php if ( $fenix_stale_drafts ) : ?>
			<div class="notice notice-error inline" style="max-width:980px;margin:12px 0"><p><strong>ฉบับร่างที่ยังเป็นรุ่นเก่า: <code>/<?php echo esc_html( implode( '/ /', $fenix_stale_drafts ) ); ?>/</code></strong> · อย่าเพิ่งกดเผยแพร่ ติ๊ก "ยืนยัน" แล้วกด "แทนด้วยรุ่นล่าสุด" ในแถวของเพจนั้นก่อน ฉบับร่างรุ่นเก่ายังมีข้อความ วันที่มีผล และโน้ตถึงผู้ดูแลของรุ่นก่อนอยู่ในเนื้อหา</p></div>
		<?php endif; ?>
		<p style="max-width:980px;color:#50575e">เพจ <strong>นโยบายความเป็นส่วนตัว / เงื่อนไขการใช้บริการ / คำขอลบข้อมูล</strong> ถูกสร้างเป็นฉบับร่าง เพื่อให้เจ้าของเว็บอ่านทวนก่อนเผยแพร่ ทำตามลำดับนี้: 1) กรอกหมวดช่องทางติดต่อและหมวดโบรกเกอร์ใน Customizer ให้ครบก่อน เพราะอีเมลติดต่อและข้อมูลพาร์ตเนอร์ในเอกสารดึงจากสองหมวดนั้น 2) ถ้าแถวของเพจในตารางขึ้นว่า "มีรุ่นใหม่" ให้ติ๊ก "ยืนยัน" แล้วกด "แทนด้วยรุ่นล่าสุด" ก่อนทุกครั้ง 3) เปิดเพจอ่านตรวจข้อความทั้งหน้า พร้อมวันที่มีผลและเลขรุ่นของเอกสาร (บรรทัดที่อยู่ในเพจตอนนี้แสดงใต้ชื่อเพจในตาราง) ถ้าจะเผยแพร่วันอื่น ให้แก้วันที่และส่วนวันที่ท้ายเลขรุ่นให้ตรงวันเผยแพร่จริง 4) กดเผยแพร่</p>
		<p style="max-width:980px;color:#50575e">เพจคู่มือที่ธีมเพิ่มเข้ามาภายหลังจะไม่ถูกใส่ในเมนูหลักที่มีอยู่แล้ว · หลังสร้างเพจ ให้เพิ่มเองที่ <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">รูปแบบ → เมนู</a> (ปุ่ม "สร้างเมนูอย่างเดียว" จะบอกว่าเพจไหนยังไม่อยู่ในเมนู)</p>
		<p style="max-width:980px;color:#50575e">อย่าเปิดเพจเหล่านี้ด้วย Elementor · จะทับการแสดงผลของเทมเพลต</p>
	</div>
	<?php
}

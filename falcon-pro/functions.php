<?php
/**
 * FALCON PRO EA · Theme functions
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FALCON_VERSION', '3.0.0' );

/* --------------------------------------------------------------
 * Theme setup
 * -------------------------------------------------------------- */
function fenix_setup() {
	load_theme_textdomain( 'falcon-pro', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'elementor' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	register_nav_menus(
		array(
			'primary' => 'เมนูหลัก (Header)',
			'footer'  => 'เมนูท้ายเว็บ (Footer)',
		)
	);
}
add_action( 'after_setup_theme', 'fenix_setup' );

/* --------------------------------------------------------------
 * Styles & scripts
 * -------------------------------------------------------------- */
function fenix_assets() {
	$style_path    = get_stylesheet_directory() . '/style.css';
	$script_path   = get_template_directory() . '/assets/js/main.js';
	$style_version = file_exists( $style_path ) ? filemtime( $style_path ) : FALCON_VERSION;
	$script_version = file_exists( $script_path ) ? filemtime( $script_path ) : FALCON_VERSION;

	wp_enqueue_style(
		'fenix-fonts',
		get_template_directory_uri() . '/assets/css/fonts.css', // ฟอนต์ Noto Sans Thai แบบ self-host (ไม่โหลดจาก Google ก่อนยินยอมคุกกี้)
		array(),
		file_exists( get_template_directory() . '/assets/css/fonts.css' ) ? filemtime( get_template_directory() . '/assets/css/fonts.css' ) : FALCON_VERSION
	);
	wp_enqueue_style( 'fenix-style', get_stylesheet_uri(), array( 'fenix-fonts' ), $style_version );
	wp_enqueue_script( 'fenix-main', get_template_directory_uri() . '/assets/js/main.js', array(), $script_version, true );

	/* CSS/JS ของแต่ละโมดูล (assets/css/*.css, assets/js/*.js ยกเว้น main.js) · ไฟล์ที่ไม่มีจะถูกข้าม */
	foreach ( fenix_asset_modules() as $fenix_module ) {
		if ( ! fenix_asset_module_needed( $fenix_module ) ) {
			continue;
		}
		$css = '/assets/css/' . $fenix_module . '.css';
		if ( file_exists( get_template_directory() . $css ) ) {
			wp_enqueue_style( 'fenix-' . $fenix_module, get_template_directory_uri() . $css, array( 'fenix-style' ), filemtime( get_template_directory() . $css ) );
		}
		$js = '/assets/js/' . $fenix_module . '.js';
		if ( file_exists( get_template_directory() . $js ) ) {
			wp_enqueue_script( 'fenix-' . $fenix_module . '-js', get_template_directory_uri() . $js, array( 'fenix-main' ), filemtime( get_template_directory() . $js ), true );
		}
	}
	wp_localize_script(
		'fenix-main',
		'fenixLoadMore',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'fenix_load_more' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fenix_assets' );

/**
 * ลำดับโมดูล CSS/JS (โหลดหลัง style.css ตามลำดับนี้)
 */
function fenix_asset_modules() {
	return apply_filters( 'fenix_asset_modules', array( 'components', 'chrome', 'home', 'guides', 'pages', 'go', 'consent' ) );
}

/**
 * โหลด CSS/JS ของโมดูลเฉพาะหน้าที่ใช้ (ลด CSS ที่ไม่ได้ใช้ต่อหน้า) · แก้ได้ด้วยฟิลเตอร์ fenix_asset_module_needed
 */
function fenix_asset_module_needed( $module ) {
	$is_go = is_page_template( 'template-go.php' );
	switch ( $module ) {
		case 'home':
			$needed = is_front_page();
			break;
		case 'go':
			$needed = $is_go;
			break;
		case 'guides':
			$needed = is_page_template( 'template-guide.php' ) || is_page_template( 'template-install.php' );
			break;
		case 'chrome':
			$needed = ! $is_go;
			break;
		default:
			$needed = true;
	}
	return (bool) apply_filters( 'fenix_asset_module_needed', $needed, $module );
}

/* --------------------------------------------------------------
 * Builder compatibility
 * -------------------------------------------------------------- */
function fenix_is_elementor_page( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

function fenix_elementor_data_has_widgets( $elements ) {
	if ( ! is_array( $elements ) ) {
		return false;
	}

	foreach ( $elements as $element ) {
		if ( ! empty( $element['widgetType'] ) ) {
			return true;
		}

		if ( ! empty( $element['elements'] ) && fenix_elementor_data_has_widgets( $element['elements'] ) ) {
			return true;
		}
	}

	return false;
}

function fenix_has_elementor_content( $post_id = null ) {
	if ( ! fenix_is_elementor_page( $post_id ) ) {
		return false;
	}

	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
	if ( empty( $elementor_data ) ) {
		return false;
	}

	$elements = json_decode( $elementor_data, true );

	return fenix_elementor_data_has_widgets( $elements );
}

function fenix_uses_elementor_page_template( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$template = $post_id ? get_page_template_slug( $post_id ) : '';

	return in_array(
		$template,
		array(
			'elementor_canvas',
			'elementor_header_footer',
			'template-elementor-canvas.php',
			'template-elementor-full-width.php',
		),
		true
	);
}

function fenix_body_classes( $classes ) {
	if ( is_page() && fenix_is_elementor_page( get_queried_object_id() ) ) {
		$classes[] = 'fenix-has-elementor';
	}
	if ( fenix_is_dark_mode() ) {
		$classes[] = 'theme-dark';
	}

	return $classes;
}
add_filter( 'body_class', 'fenix_body_classes' );

/**
 * โหมดสีของเว็บ: 'dark' = โทนเข้มเป็นหลัก (#172125) · 'balanced' = ขาว/ดำสลับ
 */
function fenix_is_dark_mode() {
	return 'balanced' !== fenix_mod( 'color_mode' );
}

/**
 * คลาสโทนพื้นของ section · ในโหมดเข้ม section ตามรายการด้านล่างเป็นพื้นเข้ม (.is-dark)
 * $alt = true → ใช้เฉดเข้มกว่า (สลับจังหวะ section ที่อยู่ติดกัน)
 */
function fenix_section_tone( $key, $alt = false ) {
	if ( ! fenix_is_dark_mode() ) {
		return '';
	}
	$light = array( 'gallery', 'install', 'faq', 'risk', 'assurance', 'longform', 'posts' );
	if ( in_array( $key, $light, true ) ) {
		return '';
	}
	return $alt ? ' is-dark is-dark-2' : ' is-dark';
}

/* --------------------------------------------------------------
 * Default content (ทุกค่าแก้ได้ในหน้า "ปรับแต่ง / Customize")
 * -------------------------------------------------------------- */
function fenix_defaults() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}

	$banner_assets  = get_template_directory_uri() . '/assets/img/banners/';

	$d = array(
		/* ทั่วไป */
		'line_url'        => '',
		'contact_fallback_text' => 'ดูช่องทางติดต่อทั้งหมด',
		'color_mode'      => 'dark',
		'wordmark_dark'   => '',
		'wordmark_light'  => '',
		'line_openchat_url'  => '',
		'line_openchat_text' => 'เข้ากลุ่ม LINE OpenChat',
		'line_qr_image'      => '',
		'contact_title'      => 'พร้อมคุยเรื่อง FALCON PRO EA กับทีมงานแล้วหรือยัง?',
		'contact_text'       => 'ทักมาทาง LINE ทีมงานช่วยประเมินทุน ความเสี่ยง และแนะนำการติดตั้งให้เหมาะกับบัญชีของคุณ ไม่มีข้อผูกมัด',

		/* สถานะผลทดสอบ (แสดงเมื่อยังไม่ได้กรอกตัวเลขจริง) */
		'results_pending_title' => 'ยังไม่มีผลทดสอบที่เผยแพร่',
		'results_pending_text'  => 'เราไม่แสดงตัวเลขที่ยังตรวจสอบไม่ได้ เมื่อมีผลที่พร้อมเผยแพร่ จะแสดงที่หน้านี้พร้อมเงื่อนไขการทดสอบครบทุกค่า ระหว่างนี้อ่านวิธีทดสอบและวิธีอ่านผลด้วยตัวเองได้ด้านล่าง',

		/* หน้า /go (ลิงก์รวม) */
		'go_sub'         => 'ผู้ช่วยเทรดอัตโนมัติสำหรับ MT5 · ทักทีมงาน หรือเริ่มตามขั้นตอนด้านล่าง',
		'go_steps_title' => 'เริ่มต้นใน 6 ขั้น',
		'go_step1'       => 'เปิดบัญชีเทรด MT5',
		'go_step2'       => 'อ่านคู่มือเปิดบัญชีและยืนยันตัวตน',
		'go_step3'       => 'ดาวน์โหลดและล็อกอิน MT5',
		'go_step4'       => 'ขอรับไฟล์ FALCON PRO EA ทาง LINE',
		'go_step5'       => 'ติดตั้ง EA บน MT5',
		'go_step6'       => 'ตั้ง VPS ให้ EA ทำงานต่อเนื่อง',
		'mt5_dl_windows' => 'https://www.metatrader5.com/en/download',
		'mt5_dl_android' => 'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5',
		'mt5_dl_ios'     => 'https://apps.apple.com/app/metatrader-5/id413251709',

		/* โบรกเกอร์ (ใช้ในคู่มือเปิดบัญชี / ล็อกอิน ผ่าน shortcode [falcon_broker]) */
		'broker_name'        => 'โบรกเกอร์ที่คุณเลือก',
		'broker_server'      => 'ชื่อเซิร์ฟเวอร์ที่ได้รับทางอีเมลหลังเปิดบัญชี',
		'broker_signup_url'  => '',
		'broker_signup_text' => 'เปิดบัญชีกับโบรกเกอร์',
		'facebook_url'    => '',
		'contact_email'   => '',
		'show_float_line' => false,
		'float_line_text' => 'สอบถามทาง LINE',
		'show_language_switcher' => false,
		'language_fallback_items' => "th|🇹🇭|TH|ไทย\nen|🇬🇧|EN|English\nzh|🇨🇳|ZH|中文\nfr|🇫🇷|FR|Français\nde|🇩🇪|DE|Deutsch\nru|🇷🇺|RU|Русский\nja|🇯🇵|JA|日本語\nko|🇰🇷|KO|한국어",

		/* Mobile bottom bar */
		'show_mobile_nav'         => true,
		'mobile_nav_home_label'   => 'หน้าแรก',
		'mobile_nav_home_url'     => '/',
		'mobile_nav_test_label'   => 'ผลทดสอบ',
		'mobile_nav_test_url'     => '/forward-test/',
		'mobile_nav_price_label'   => 'แพ็กเกจ',
		'mobile_nav_price_url'     => '/pricing/',
		'mobile_nav_install_label' => 'วิธีติดตั้ง',
		'mobile_nav_install_url'   => '/how-to-install/',
		'mobile_nav_line_label'    => 'ทัก LINE',

		/* SEO / แชร์ลิงก์ (Open Graph) */
		'og_default_image'       => '',
		'og_default_description' => 'FALCON PRO EA · ระบบช่วยเทรดอัตโนมัติบน MetaTrader 5 เน้นวินัยและการบริหารความเสี่ยง',

		/* คุกกี้ / Consent + Tracking (โหลด tracking เฉพาะหลังกดยอมรับ) */
		'show_cookie_consent' => false,
		'cookie_consent_text' => 'เว็บไซต์นี้ใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานและวิเคราะห์การเข้าชม คุณเลือกยอมรับหรือปฏิเสธคุกกี้ที่ไม่จำเป็นได้',
		'ga_measurement_id'   => '',
		'fb_pixel_id'         => '',

		/* Hero */
		'show_hero'      => true,
		'hero_badge'     => 'Expert Advisor for MetaTrader 5',
		'hero_title'     => 'FALCON PRO EA',
		'hero_subtitle'  => 'ผู้ช่วยเทรดอัตโนมัติสำหรับ',
		'hero_subtitle_em' => 'MT5',
		'hero_desc'      => 'EA MT5 ที่ทำงานตามกฎที่ตั้งไว้ล่วงหน้า ใช้ได้กับ Forex ทองคำ และสินทรัพย์อื่นบน MetaTrader 5 ไม่ตัดสินใจตามอารมณ์ ทุนและระดับความเสี่ยงคุณเป็นคนกำหนดเอง',
		'hero_point1_title' => 'เทรดอัตโนมัติตามกฎ',
		'hero_point1_desc'  => 'เปิดและปิดออเดอร์เมื่อเงื่อนไขครบ ไม่เดา ไม่ไล่ราคา',
		'hero_point2_title' => 'ติดตั้งง่าย ตั้งค่าไม่ซับซ้อน',
		'hero_point2_desc'  => 'มีคู่มือภาษาไทยและทีมช่วยตั้งค่าผ่าน LINE',
		'hero_point3_title' => 'บริหารความเสี่ยงอย่างเป็นระบบ',
		'hero_point3_desc'  => 'กำหนดขนาดออเดอร์และระดับ Drawdown ที่รับได้เอง',
		'hero_btn1_text' => 'สอบถามทาง LINE',
		'hero_btn2_text' => 'ระบบทำงานอย่างไร',
		'hero_btn2_url'  => '#how-it-works',
		'hero_note'      => 'การลงทุนมีความเสี่ยง ผลลัพธ์ขึ้นอยู่กับการตั้งค่าและการบริหารความเสี่ยงของผู้ใช้',
		'hero_tagline'   => 'Automate · Analyze · Trade Better',
		'hero_image'     => '',
		/* Hero · แผงควบคุมจำลอง (แสดงเมื่อไม่ได้ใส่รูป Hero) */
		'hero_panel_title'   => 'FALCON PRO EA',
		'hero_panel_status'  => 'Running',
		'hero_panel_badge'   => 'MT5',
		'hero_panel_fields'  => "Symbol|XAUUSD\nLot Size|ตามทุน\nRisk|คุณกำหนด\nMode|Auto",
		'hero_panel_button'  => 'Trading Active',
		'hero_panel_tags'    => 'Disciplined · Systematic · Consistent',
		'hero_panel_caption' => 'ภาพจำลองแผงควบคุม ไม่ใช่ผลการเทรดจริง',

		/* ปัญหานักเทรด */
		'show_pain'     => true,
		'pain_title'    => 'ใช้ EA เทรด Forex ดีไหม? ปัญหาที่คนเทรดมือเจอบ่อย',
		'pain_answer'   => 'สิ่งที่ระบบอัตโนมัติเข้ามาแทน คือการทำตามกฎเดิมทุกครั้ง ส่วนทุน ความเสี่ยง และการตัดสินใจเริ่มหรือหยุดระบบยังเป็นของคุณเสมอ',
		'pain_subtitle' => 'ปัญหาคลาสสิกที่นักเทรดส่วนใหญ่ต้องเจอ และเป็นเหตุผลที่ FALCON PRO EA ถูกสร้างขึ้นมา',
		'pain1_title'   => 'ใช้อารมณ์ในการเข้าออเดอร์',
		'pain1_desc'    => 'กลัวตกรถ รีบเข้า รีบออก สุดท้ายไม่ทำตามแผนที่วางไว้',
		'pain2_title'   => 'ไม่มีเวลานั่งเฝ้าจอทั้งวัน',
		'pain2_desc'    => 'พลาดจังหวะสำคัญ เพราะต้องทำงานประจำหรือติดธุระ',
		'pain3_title'   => 'คุม Lot และความเสี่ยงไม่เป็นระบบ',
		'pain3_desc'    => 'บางครั้งเทรดใหญ่เกินไป จนพอร์ตแกว่งแรงเกินกว่าจะรับไหว',
		'pain4_title'   => 'ขาดวินัยในการทำตามแผน',
		'pain4_desc'    => 'วางแผนไว้ดี แต่พอกราฟวิ่งจริงกลับเปลี่ยนใจกลางทาง',

		/* FALCON PRO EA คืออะไร */
		'show_about'  => true,
		'about_title' => 'FALCON PRO EA คืออะไร · EA เทรดอัตโนมัติบน MT5',
		'about_text'  => "FALCON PRO EA คือ Expert Advisor หรือที่หลายคนเรียกว่าบอทเทรด โรบอทเทรด สำหรับแพลตฟอร์ม MetaTrader 5 ติดตั้งบนกราฟ MT5 แล้วเฝ้าดูราคาแทนคุณตลอดเวลาที่ตลาดเปิด เมื่อราคาเข้าเงื่อนไขที่ตั้งไว้ ระบบจะเปิดออเดอร์ คุมขนาดออเดอร์และเงื่อนไขปิดตามค่าที่กำหนด แล้วปิดเมื่อครบเงื่อนไข โดยไม่ต้องนั่งเฝ้าจอ\n\nสิ่งที่ระบบให้คือความสม่ำเสมอ ทำตามกฎเดิมทุกครั้ง ไม่รีบเข้า ไม่ลังเลตอนควรออก แต่ EA ไม่ใช่เครื่องมือการันตีกำไร ผลลัพธ์ขึ้นกับสภาวะตลาด โบรกเกอร์ และการตั้งค่าของคุณ",
		'about_points' => "แพลตฟอร์ม|ทำงานบน MetaTrader 5 โดยตรง ติดตั้งครั้งเดียวแล้วรันต่อเนื่องบนคอมพิวเตอร์หรือ VPS\nวินัย|ทำตามกฎเดิมทุกครั้ง ไม่ให้ความกลัวหรือความโลภมาแทรกการตัดสินใจ\nการควบคุม|คุณกำหนดทุน ขนาดออเดอร์ และระดับความเสี่ยงเอง หยุดระบบได้ทุกเมื่อ",
		'about_image'         => $banner_assets . 'falcon-pro-ea-mt5-laptop-overview.webp',
		'about_image_caption' => 'ภาพประกอบ ตัวเลขในภาพเป็นตัวอย่างหน้าตาโปรแกรม ไม่ใช่ผลการเทรดจริง',

		/* จุดเด่น */
		'show_features'     => true,
		'features_title'    => 'จุดเด่นของ FALCON PRO EA',
		'features_subtitle' => 'ออกแบบมาเพื่อให้การเทรดของคุณเป็นระบบ ตรวจสอบได้ และอยู่ในกรอบความเสี่ยงที่วางไว้',
		'feat1_title'       => 'ระบบช่วยเทรดอัตโนมัติ',
		'feat1_desc'        => 'เข้าและออกออเดอร์ตามเงื่อนไขที่กำหนดไว้ล่วงหน้า ไม่ใช่ตามอารมณ์',
		'feat2_title'       => 'รองรับ MetaTrader 5',
		'feat2_desc'        => 'พัฒนาด้วย MQL5 สำหรับผู้ใช้งานแพลตฟอร์ม MT5 โดยเฉพาะ',
		'feat3_title'       => 'Dashboard ดูง่าย',
		'feat3_desc'        => 'ติดตามสถานะระบบ กำไร/ขาดทุน และเงื่อนไขการทำงานได้ชัดเจนในหน้าจอเดียว',
		'feat4_title'       => 'แนวคิดบริหารความเสี่ยง',
		'feat4_desc'        => 'วางแผนเรื่อง Lot, Stop Loss และ Drawdown ได้ตามระดับความเสี่ยงที่เหมาะกับทุนของคุณ',
		'feat5_title'       => 'เหมาะกับคนไม่มีเวลาเฝ้าจอ',
		'feat5_desc'        => 'ให้ระบบช่วยทำงานตามแผนที่วางไว้ แม้ในเวลาที่คุณไม่อยู่หน้าจอ',
		'feat6_title'       => 'มีทีมช่วยแนะนำการติดตั้ง',
		'feat6_desc'        => 'ดูแลตั้งแต่ติดตั้งจนตั้งค่าเสร็จ มือใหม่ก็เริ่มต้นได้อย่างมั่นใจ',

		/* ภาพระบบ */
		'show_gallery'     => true,
		'gallery_title'    => 'FALCON PRO EA บนทุกหน้าจอ',
		'gallery_subtitle' => 'EA รันบน MetaTrader 5 ในคอมพิวเตอร์หรือ VPS ส่วนมือถือและแท็บเล็ตใช้ติดตามสถานะบัญชีผ่านแอป MT5',
		'gallery_note'     => 'ภาพประกอบการนำเสนอ ตัวเลขในภาพเป็นตัวอย่างเพื่อให้เห็นหน้าตาการใช้งาน ไม่ใช่ผลการเทรดจริงและไม่ใช่การรับประกันผลลัพธ์',
		'gallery_img1'     => $banner_assets . 'falcon-pro-ea-mt5-feature-toggles.webp',
		'gallery_cap1'     => 'โมดูลหลัก: Auto Trading, Risk Management, Multi-Symbol, Smart Filter',
		'gallery_img2'     => $banner_assets . 'falcon-pro-ea-mt5-settings-panel.webp',
		'gallery_cap2'     => 'แผงตั้งค่า Symbol, Lot Size, Risk และ Mode',
		'gallery_img3'     => $banner_assets . 'falcon-pro-ea-mt5-navigator.webp',
		'gallery_cap3'     => 'FALCON PRO ในหน้าต่าง Navigator ของ MetaTrader 5',
		'gallery_img4'     => $banner_assets . 'falcon-pro-ea-mt5-ea-status-panel.webp',
		'gallery_cap4'     => 'แผงสถานะ EA บนกราฟ MT5',
		'gallery_img5'     => $banner_assets . 'falcon-pro-ea-mt5-mobile-settings.webp',
		'gallery_cap5'     => 'ติดตามสถานะบัญชีผ่านแอป MT5 บนมือถือ',
		'gallery_img6'     => $banner_assets . 'falcon-pro-ea-mt5-tablet-box.webp',
		'gallery_cap6'     => 'ดูภาพรวมบนแท็บเล็ต',

		/* ขั้นตอนใช้งาน */
		'show_steps'     => true,
		'steps_kicker'   => 'How it works',
		'steps_title'    => 'FALCON PRO EA ทำงานอย่างไรบน MetaTrader 5',
		'steps_subtitle' => 'บอทเทรด MT5 ทำงานเป็นวงจร 4 ขั้น ซ้ำแบบเดิมทุกวัน ตั้งแต่ตั้งค่าครั้งแรกจนถึงการติดตามผล',
		'step1_title'    => 'ตั้งทุนและระดับความเสี่ยง',
		'step1_desc'     => 'กำหนดทุนที่ใช้กับระบบ เลือกระดับความเสี่ยงและขนาด Lot ให้สอดคล้องกับบัญชี MT5 ทีมงานมีไฟล์ Preset ให้เริ่มจากค่าที่เหมาะกับทุน',
		'step2_title'    => 'ระบบตรวจเงื่อนไขตลาดตามกฎ',
		'step2_desc'     => 'EA อ่านราคาจากกราฟตลอดเวลาที่ตลาดเปิด เทียบกับเงื่อนไขที่ตั้งไว้ ถ้ายังไม่เข้าเงื่อนไขก็รอ ไม่เดาและไม่ไล่ราคา',
		'step3_title'    => 'เปิดและปิดออเดอร์อัตโนมัติ',
		'step3_desc'     => 'เมื่อเงื่อนไขครบ ระบบส่งคำสั่งไปยังโบรกเกอร์ จัดการขนาดออเดอร์และเงื่อนไขปิดตามค่าที่คุณตั้ง แล้วปิดเมื่อครบกฎ',
		'step4_title'    => 'ติดตามผลผ่าน Dashboard และ LINE',
		'step4_desc'     => 'แผงสถานะบนกราฟแสดงทุน กำไรขาดทุน Drawdown และออเดอร์แบบเรียลไทม์ ดูยอดบัญชีในแอป MT5 บนมือถือได้ และถามทีมงานทาง LINE ได้เสมอ',

		/* ผลการทดสอบ */
		'show_perf'         => true,
		'perf_kicker'       => 'Evidence',
		'perf_title'        => 'ผลการทดสอบระบบ',
		'perf_subtitle'     => 'ความโปร่งใสคือสิ่งที่เราให้ความสำคัญ ข้อมูลการทดสอบทุกชุดระบุเงื่อนไขไว้ชัดเจน',
		'stat1_label'       => 'ช่วงเวลาทดสอบ',
		'stat1_value'       => 'ระบุช่วงเวลา',
		'stat2_label'       => 'คู่เงินที่ทดสอบ',
		'stat2_value'       => 'เช่น XAUUSD',
		'stat3_label'       => 'Timeframe',
		'stat3_value'       => 'เช่น M15',
		'stat4_label'       => 'ทุนเริ่มต้น',
		'stat4_value'       => 'ระบุทุนทดสอบ',
		'stat5_label'       => 'Max Drawdown',
		'stat5_value'       => 'ระบุ %',
		'stat6_label'       => 'จำนวนออเดอร์',
		'stat6_value'       => 'ระบุจำนวน',
		'perf_image'        => '',
		'perf_image_caption'=> 'กราฟผลการทดสอบระบบ (Backtest / Forward Test)',
		'perf_note'         => 'หมายเหตุ: ผลการทดสอบขึ้นอยู่กับ Spread, Commission และ Slippage ของแต่ละโบรกเกอร์',
		'perf_disclaimer'   => 'ผลการทดสอบใช้เพื่อประกอบการศึกษาเท่านั้น ผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต ผู้ใช้งานควรเข้าใจความเสี่ยงก่อนตัดสินใจใช้งานระบบ',

		/* เหมาะกับใคร */
		'show_fit'       => true,
		'fit_title'      => 'FALCON PRO EA เหมาะกับใคร?',
		'fit_subtitle'   => 'เราอยากให้คุณตัดสินใจจากข้อมูลจริง ไม่ใช่ความคาดหวังเกินจริง',
		'fit_good_title' => 'เหมาะกับ',
		'fit_good_items' => "คนที่อยากเทรดอย่างเป็นระบบ มีแบบแผนชัดเจน\nคนที่ต้องการลดการใช้อารมณ์ในการเทรด\nคนที่เข้าใจว่าการเทรดมีความเสี่ยง\nคนที่มีเวลาเรียนรู้การตั้งค่าระบบ\nคนที่มองว่า EA คือเครื่องมือช่วย ไม่ใช่เครื่องการันตีกำไร",
		'fit_bad_title'  => 'ไม่เหมาะกับ',
		'fit_bad_items'  => "คนที่หวังกำไรเร็วหรือรวยทางลัด\nคนที่รับความเสี่ยงและการขาดทุนไม่ได้\nคนที่ไม่ต้องการศึกษาการใช้งานเลย\nคนที่คิดว่า EA จะทำเงินให้ได้ตลอดเวลา\nคนที่ไม่ได้ใช้เงินเย็นในการเทรด",

		/* แพ็กเกจ */
		'show_pricing'     => true,
		'pricing_title'    => 'แพ็กเกจการใช้งาน',
		'pricing_subtitle' => 'เลือกแพ็กเกจที่เหมาะกับการใช้งานของคุณ หรือทักมาปรึกษาก่อนตัดสินใจได้',
		'pricing_mode'     => 'contact',
		'pricing_btn_text' => 'สอบถามแพ็กเกจนี้',
		'pricing_note'     => 'ราคาและเงื่อนไขอาจมีการเปลี่ยนแปลง สอบถามรายละเอียดล่าสุดได้ทาง LINE',
		'pkg1_name'        => 'Starter',
		'pkg1_tag'         => 'สำหรับเริ่มต้นทดลองใช้งาน',
		'pkg1_price'       => 'X,XXX',
		'pkg1_period'      => 'บาท / ปี',
		'pkg1_features'    => "ใช้งานได้ 1 บัญชีเทรด\nคู่มือการติดตั้งและตั้งค่า\nอัปเดตระบบตามรอบเวอร์ชัน\nSupport เบื้องต้นผ่าน LINE",
		'pkg1_featured'    => false,
		'pkg2_name'        => 'Pro',
		'pkg2_tag'         => 'สำหรับใช้งานจริงต่อเนื่อง',
		'pkg2_price'       => 'X,XXX',
		'pkg2_period'      => 'บาท / ปี',
		'pkg2_features'    => "ใช้งานได้ 1 ถึง 2 บัญชีเทรด\nไฟล์ Preset ตั้งค่าพร้อมใช้\nอัปเดตระบบต่อเนื่อง\nSupport ส่วนตัวผ่าน LINE",
		'pkg2_featured'    => true,
		'pkg3_name'        => 'VIP',
		'pkg3_tag'         => 'สำหรับคนที่อยากให้ทีมดูแลใกล้ชิด',
		'pkg3_price'       => 'X,XXX',
		'pkg3_period'      => 'บาท / ปี',
		'pkg3_features'    => "ทีมงานช่วยติดตั้งบนเครื่อง / VPS\nสอนตั้งค่าและแนวคิดบริหารความเสี่ยง\nติดตามผลในช่วงเริ่มต้นใช้งาน\nSupport ส่วนตัวแบบใกล้ชิด",
		'pkg3_featured'    => false,

		/* รีวิว (ปิดไว้ก่อน จนกว่าจะมีรีวิวจริง) */
		'show_reviews'     => false,
		'reviews_title'    => 'เสียงจากผู้ใช้งานจริง',
		'reviews_subtitle' => 'รีวิวจากลูกค้าที่ใช้งาน FALCON PRO EA',
		'rev1_text'        => 'ทีมงานช่วยติดตั้งดีมาก อธิบายเข้าใจง่าย มือใหม่ก็เริ่มต้นได้',
		'rev1_name'        => 'ตัวอย่างรีวิว · แทนที่ด้วยรีวิวจริง',
		'rev2_text'        => 'Dashboard ดูง่าย เหมาะกับคนที่อยากเทรดอย่างเป็นระบบ',
		'rev2_name'        => 'ตัวอย่างรีวิว · แทนที่ด้วยรีวิวจริง',
		'rev3_text'        => 'ชอบที่ทีมงานอธิบายเรื่องความเสี่ยงก่อนเริ่มใช้งานจริง',
		'rev3_name'        => 'ตัวอย่างรีวิว · แทนที่ด้วยรีวิวจริง',

		/* FAQ */
		'show_faq'     => true,
		'faq_title'    => 'คำถามที่พบบ่อยเกี่ยวกับ FALCON PRO EA และ EA MT5',
		'faq_subtitle' => 'รวมคำตอบสำหรับคำถามที่ลูกค้าสอบถามเข้ามามากที่สุด',
		'faq1_q'       => 'EA เทรด คืออะไร ต่างจากบอทเทรดหรือโรบอทเทรดไหม?',
		'faq1_a'       => 'ไม่ต่างกัน EA ย่อมาจาก Expert Advisor คือโปรแกรมที่ติดตั้งบน MetaTrader แล้วส่งคำสั่งซื้อขายตามกฎที่เขียนไว้ คนไทยเรียกทั้งบอทเทรด โรบอทเทรด หรือ EA เทรด แต่หมายถึงสิ่งเดียวกัน FALCON PRO EA คือ EA สำหรับ MetaTrader 5',
		'faq2_q'       => 'ใช้ EA เทรด Forex ดีไหม เหมาะกับใคร?',
		'faq2_a'       => 'เหมาะกับคนที่มีแผนเทรดแต่ทำตามได้ไม่สม่ำเสมอ ไม่มีเวลาเฝ้าจอ หรืออยากลดการตัดสินใจตามอารมณ์ ไม่เหมาะกับคนที่หวังกำไรเร็วหรือรับการขาดทุนไม่ได้ เพราะ EA ขาดทุนได้เหมือนการเทรดทุกแบบ',
		'faq3_q'       => 'FALCON PRO EA การันตีกำไรไหม?',
		'faq3_a'       => 'ไม่การันตี ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง และเงื่อนไขของโบรกเกอร์ ผลในอดีตไม่รับประกันผลในอนาคต ใครที่การันตีกำไรจาก EA ควรระวังเป็นพิเศษ',
		'faq4_q'       => 'ใช้กับโบรกเกอร์ไหนได้บ้าง?',
		'faq4_a'       => 'ใช้ได้กับโบรกเกอร์ที่ให้บริการ MetaTrader 5 และอนุญาตให้ใช้ EA ก่อนเริ่มใช้จริงควรสอบถามทีมงานเรื่องประเภทบัญชี Spread และเงื่อนไขของโบรกเกอร์ที่คุณใช้',
		'faq5_q'       => 'เทรดทอง XAUUSD ได้ไหม ใช้กับคู่เงินอะไรบ้าง?',
		'faq5_a'       => 'ใช้ได้กับคู่เงิน ทองคำ และสินทรัพย์อื่นที่มีบนบัญชี MT5 ของโบรกเกอร์ที่คุณเลือก ค่าที่เหมาะสมของแต่ละสินทรัพย์ต่างกัน ทีมงานจะแนะนำ Preset ให้ตรงกับสินทรัพย์และทุนของคุณ',
		'faq6_q'       => 'ต้องเปิดคอมตลอดไหม ใช้ EA บนมือถือได้ไหม?',
		'faq6_a'       => 'EA ต้องรันบน MT5 เวอร์ชันคอมพิวเตอร์ที่เปิดอยู่ตลอด หรือบน VPS ที่ทำงาน 24 ชั่วโมง แอป MT5 บนมือถือใช้ดูยอดเงินและออเดอร์ได้ แต่รัน EA ไม่ได้',
		'faq7_q'       => 'ใช้ทุนเริ่มต้นเท่าไร?',
		'faq7_a'       => 'ขึ้นอยู่กับสินทรัพย์ ระดับความเสี่ยง และเงื่อนไขของโบรกเกอร์ ควรใช้เงินที่เสียได้โดยไม่กระทบชีวิตประจำวัน และทักทีมงานเพื่อประเมินทุนที่เหมาะสมก่อนเริ่ม',
		'faq8_q'       => 'ทดลองบนบัญชีเดโมก่อนได้ไหม?',
		'faq8_a'       => 'ได้และแนะนำให้ทำ เริ่มจากบัญชีเดโมหรือบัญชีเซ็นต์เพื่อดูพฤติกรรมของระบบกับตลาดจริง ก่อนเพิ่มทุนบนบัญชีจริง',
		'faq9_q'       => 'ต่างจาก EA แจกฟรีทั่วไปอย่างไร?',
		'faq9_a'       => 'นอกจากตัวไฟล์ EA คุณจะได้คู่มือภาษาไทย ไฟล์ Preset ตามระดับความเสี่ยง การอัปเดตเวอร์ชัน และทีมงานที่ช่วยติดตั้งและตอบคำถามผ่าน LINE',
		'faq10_q'      => 'การใช้ EA มีความเสี่ยงอะไรบ้าง?',
		'faq10_a'      => 'ความผันผวนของตลาด ข่าวแรง Slippage Spread ที่กว้างขึ้น การตั้งค่าที่ไม่เหมาะกับทุน ปัญหาอินเทอร์เน็ตหรือ VPS และการใช้ทุนเกินระดับที่รับได้ ควรอ่านประกาศความเสี่ยงฉบับเต็มก่อนเริ่มใช้งาน',

		/* คำเตือนความเสี่ยง */
		'show_risk'  => true,
		'risk_title' => 'คำเตือนความเสี่ยง',
		'risk_text'  => 'การเทรด Forex, Gold, CFD หรือสินทรัพย์ทางการเงินมีความเสี่ยงสูง ผู้ใช้งานอาจขาดทุนได้ ผลการทดสอบหรือผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต FALCON PRO EA เป็นเครื่องมือช่วยเทรดตามเงื่อนไขที่กำหนด ไม่ใช่การรับประกันผลกำไร ผู้ใช้งานควรศึกษาข้อมูลและบริหารความเสี่ยงให้เหมาะสมกับตนเองก่อนใช้งานจริง',

		/* CTA ปิดท้าย */
		'show_cta'     => true,
		'cta_title'    => 'พร้อมเริ่มเทรดอย่างเป็นระบบกับ FALCON PRO EA แล้วหรือยัง?',
		'cta_subtitle' => 'สอบถามรายละเอียด การติดตั้ง แพ็กเกจ และความเหมาะสมกับทุนของคุณได้ทาง LINE',
		'cta_btn_text' => 'ทัก LINE เพื่อขอรายละเอียด',

		/* Footer */
		'footer_kicker'       => 'FALCON PRO EA',
		'footer_cta_title'    => 'พร้อมเริ่มต้นใช้งาน FALCON PRO?',
		'footer_cta_text'     => 'สอบถามการติดตั้ง เงื่อนไขการใช้งาน และความเหมาะสมกับทุนของคุณได้ทาง LINE',
		'footer_line_text'    => 'ทัก LINE Official Account',
		'footer_facebook_text' => 'Facebook Page',
		'footer_email_text'    => 'Email Support',
		'footer_prep_title'    => 'ข้อมูลที่ทีมจะถามก่อนแนะนำ',
		'footer_prep_text'     => 'เตรียมข้อมูลสั้น ๆ เพื่อให้ทีมช่วยแนะนำได้ตรงขึ้น',
		'footer_prep_items'    => "ทุนที่ต้องการใช้กับระบบ\nโบรกเกอร์และประเภทบัญชี MT5\nเป้าหมาย: ติดตั้ง / สอบถามราคา / ตรวจความเหมาะสม\nช่วงเวลาที่สะดวกให้ทีมติดต่อกลับ",
		'footer_tagline'      => "FALCON PRO EA ถูกออกแบบให้เป็นผู้ช่วยจัดระบบการเทรดบน MetaTrader 5 สำหรับผู้ที่ต้องการลดการตัดสินใจตามอารมณ์ และให้การทำงานเป็นไปตามแผนที่กำหนดไว้อย่างมีวินัย\n\nแนวทางของระบบให้ความสำคัญกับการใช้งานภายใต้กรอบความเสี่ยงที่ชัดเจน ช่วยให้ผู้ใช้พิจารณาความเหมาะสมของทุน การตั้งค่า และเงื่อนไขการใช้งานก่อนเริ่มต้นจริง",
		'footer_trust_items'  => "MT5 Expert Advisor\nSupport ภาษาไทย\nRisk-first setup",
		'footer_risk_link'    => 'คำเตือนความเสี่ยง',
	);

	/* ===== เนื้อหาหน้าย่อย (multipage) ===== */
	$d = array_merge(
		$d,
		array(

			/* หน้าแรก · แถบไฮไลต์ */
			'show_highlight' => true,
			'highlight1'     => 'แพลตฟอร์ม|MetaTrader 5',
			'highlight2'     => 'สินทรัพย์|Forex ทองคำ และสินทรัพย์บน MT5',
			'highlight3'     => 'รูปแบบ|เทรดอัตโนมัติตามกฎที่ตั้งไว้',
			'highlight4'     => 'การส่งมอบ|ไฟล์ EA + คู่มือภาษาไทย',
			'highlight_note' => 'ป้ายข้อมูลระบบ ไม่ใช่ผลการเทรด',

			/* หน้าแรก · ขั้นตอน · รายการเตรียมตัว */
			'steps_checklist_title' => 'ต้องมีอะไรบ้างก่อนเริ่ม',
			'steps_checklist'       => "บัญชี MT5 กับโบรกเกอร์ที่รองรับ\nทุนที่พร้อมรับความเสี่ยง\nคอมพิวเตอร์ที่เปิดตลอด หรือ VPS\nเวลาอ่านคู่มือประมาณ 30 นาที",

			/* หน้าแรก · วิธีทดสอบ (Backtest / Forward) */
			'show_tests'      => true,
			'tests_kicker'    => 'Testing',
			'tests_title'     => 'วิธีทดสอบ FALCON PRO EA ด้วย Backtest และ Forward Test',
			'tests_subtitle'  => 'การทดสอบ EA MT5 มี 2 แบบ ควรดูทั้งคู่ก่อนใช้เงินจริง และอ่านเงื่อนไขการทดสอบทุกครั้ง',
			'tests_bt_title'  => 'Backtest · ทดสอบย้อนหลัง',
			'tests_bt_text'   => 'รัน EA ใน Strategy Tester ของ MT5 กับข้อมูลราคาในอดีต เพื่อดูพฤติกรรมของระบบภายใต้เงื่อนไขที่กำหนด ค่าที่ควรอ่านคือ Profit Factor, Max Drawdown และจำนวนเทรด ข้อจำกัดคือผลขึ้นกับคุณภาพข้อมูล Spread และ Slippage จึงมักดูดีกว่าของจริง',
			'tests_bt_img'    => $banner_assets . 'falcon-pro-ea-mt5-laptop-falcon.webp',
			'tests_fw_title'  => 'Forward Test · ทดสอบเดินหน้า',
			'tests_fw_text'   => 'รัน EA กับตลาดจริงแบบเรียลไทม์บนบัญชีเดโม บัญชีเซ็นต์ หรือบัญชีจริง เห็นผลของ Spread, Slippage และความเร็วส่งคำสั่งจริง ใช้เวลานานกว่า แต่สะท้อนการใช้งานจริงได้ใกล้กว่า Backtest',
			'tests_fw_img'    => $banner_assets . 'falcon-pro-ea-mt5-desk-setup.webp',
			'tests_note'      => 'ผลทดสอบที่เผยแพร่จะแสดงพร้อมเงื่อนไขครบถ้วนที่หน้า Backtest และ Forward Test ผลในอดีตไม่รับประกันผลในอนาคต',

			/* หน้าแรก · ติดตั้งย่อ 3 ขั้น */
			'show_install_home'  => true,
			'install_home_title' => 'ติดตั้ง FALCON PRO EA บน MT5 ใน 3 ขั้น',
			'install_home_sub'   => 'ติดตั้งเองได้ตามขั้นตอนด้านล่าง ติดตรงไหนทักทีมงานทาง LINE ได้ทันที',
			'ih_step1_title'     => 'เตรียมบัญชี MT5 และไฟล์ EA',
			'ih_step1_desc'      => 'เปิดบัญชีกับโบรกเกอร์ที่รองรับ MetaTrader 5 ติดตั้ง MT5 บนคอมพิวเตอร์หรือ VPS แล้วดาวน์โหลดไฟล์ FALCON PRO EA ที่ได้รับ',
			'ih_step2_title'     => 'วางไฟล์ในโฟลเดอร์ Experts',
			'ih_step2_desc'      => 'ใน MT5 ไปที่ File → Open Data Folder → MQL5 → Experts วางไฟล์ลงไป รีสตาร์ต MT5 แล้วเปิดปุ่ม Algo Trading ให้เป็นสีเขียว',
			'ih_step3_title'     => 'ตั้งค่าตาม Preset และตรวจสถานะ',
			'ih_step3_desc'      => 'ลาก EA ขึ้นกราฟ โหลด Preset ตามระดับความเสี่ยง แล้วตรวจแผงสถานะและแท็บ Experts ว่าระบบทำงานปกติ',
			'install_home_note'  => 'ดูผลผ่านแอป MT5 บนมือถือได้ แต่ตัว EA ต้องรันบนคอมพิวเตอร์หรือ VPS ที่เปิดตลอด',

			/* หน้าแรก · แถบสถานะ / Control center */
			'show_live_status'     => false,
			'live_status_kicker'   => 'Live System Flow',
			'live_status_items'    => "FALCON PRO EA บน MetaTrader 5\nRisk-first setup\nBacktest และ Forward Test\nตั้งค่าตามทุนและความเสี่ยง\nLINE Support ภาษาไทย",
			'show_control_center'  => false,
			'control_kicker'       => 'FALCON PRO EA Control Center',
			'control_title'        => 'ภาพรวมก่อนเริ่มใช้งาน FALCON PRO EA',
			'control_subtitle'     => 'ดูขั้นตอนสำคัญของระบบ ตั้งแต่ความพร้อมของ MetaTrader 5 การตั้งค่าความเสี่ยง ไปจนถึงการติดตามผลผ่าน Dashboard โดยไม่ต้องเดาเอง',
			'control_panel_title'  => 'System Readiness',
			'control_panel_status' => 'พร้อมประเมินความเหมาะสม',
			'control_badge'        => 'MT5',
			'control_panel_text'   => 'ก่อนเริ่มใช้งาน ทีมจะช่วยเช็กข้อมูลหลักที่ส่งผลต่อการตั้งค่า เช่น ประเภทบัญชี โบรกเกอร์ ทุนที่ใช้ และระดับความเสี่ยงที่รับได้',
			'control_metric1_label' => 'Platform',
			'control_metric1_value' => 'MetaTrader 5',
			'control_metric2_label' => 'Mode',
			'control_metric2_value' => 'EA Setup',
			'control_metric3_label' => 'Focus',
			'control_metric3_value' => 'Risk-first',
			'control_list_title'   => 'สิ่งที่ควรเตรียมก่อนเริ่ม',
			'control_list_items'   => "บัญชี MetaTrader 5 และโบรกเกอร์ที่ใช้งาน\nทุนที่ต้องการนำมาใช้กับระบบ\nระดับความเสี่ยงที่รับได้\nเป้าหมายการใช้งาน: ติดตั้ง / ทดลอง / ปรับพอร์ต",

			/* หน้าแรก · การ์ดนำทาง */
			'home_cards_title' => 'คู่มือและข้อมูลเชิงลึก',
			'home_cards_sub'   => 'เราแยกข้อมูลเป็นหมวด เพื่อให้คุณศึกษาได้ละเอียดก่อนตัดสินใจ',
			'card1_title'      => 'Backtest EA บน MT5',
			'card1_desc'       => 'วิธีทดสอบย้อนหลังใน Strategy Tester และวิธีอ่านรายงานผลให้เป็น',
			'card1_url'        => '/backtest/',
			'card2_title'      => 'Forward Test',
			'card2_desc'       => 'ทดสอบกับตลาดจริงบนบัญชีเดโม เซ็นต์ หรือบัญชีจริง ต่างจาก Backtest อย่างไร',
			'card2_url'        => '/forward-test/',
			'card3_title'      => 'แพ็กเกจ & ราคา',
			'card3_desc'       => 'เปรียบเทียบแพ็กเกจและสิ่งที่ได้รับในแต่ละระดับ',
			'card3_url'        => '/pricing/',
			'card4_title'      => 'วิธีติดตั้ง',
			'card4_desc'       => 'คู่มือติดตั้ง EA บน MT5 ทีละขั้น พร้อมวิธีแก้ปัญหาที่เจอบ่อย',
			'card4_url'        => '/how-to-install/',
			'card5_title'      => 'คำเตือนความเสี่ยง',
			'card5_desc'       => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งานจริง',
			'card5_url'        => '/risk-disclosure/',
			'card6_title'      => 'คู่มือ VPS',
			'card6_desc'       => 'รัน EA ต่อเนื่องบน VPS และเชื่อมต่อจาก Windows, Android หรือ iPhone',
			'card6_url'        => '/vps-windows/',

			/* หน้า Backtest */
			'backtest_sub'        => 'วิธีทดสอบย้อนหลังใน Strategy Tester ของ MT5 และวิธีอ่านผลให้เป็น',
			'backtest_intro'      => 'Backtest คือการนำกลยุทธ์ของ EA มาทดสอบกับข้อมูลราคาในอดีต เพื่อดูพฤติกรรมของระบบภายใต้เงื่อนไขที่กำหนด เมื่อมีผลที่เผยแพร่ เราจะระบุพารามิเตอร์การทดสอบไว้ครบทุกค่าเพื่อความโปร่งใส',
			'bt_stat1_label'      => 'ช่วงเวลาทดสอบ',
			'bt_stat1_value'      => 'ระบุช่วงเวลา',
			'bt_stat2_label'      => 'คู่เงิน / สินทรัพย์',
			'bt_stat2_value'      => 'เช่น XAUUSD',
			'bt_stat3_label'      => 'Timeframe',
			'bt_stat3_value'      => 'เช่น M15',
			'bt_stat4_label'      => 'ทุนเริ่มต้น',
			'bt_stat4_value'      => 'ระบุทุน',
			'bt_stat5_label'      => 'กำไรสุทธิ (Net Profit)',
			'bt_stat5_value'      => 'ระบุผล',
			'bt_stat6_label'      => 'Profit Factor',
			'bt_stat6_value'      => 'ระบุค่า',
			'bt_stat7_label'      => 'Max Drawdown',
			'bt_stat7_value'      => 'ระบุ %',
			'bt_stat8_label'      => 'จำนวนเทรดทั้งหมด',
			'bt_stat8_value'      => 'ระบุจำนวน',
			'backtest_img'        => '',
			'backtest_img_caption'=> 'กราฟ Equity / รายงานผล Backtest จาก MT5',
			'backtest_note'       => 'หมายเหตุ: ผลขึ้นอยู่กับคุณภาพข้อมูลราคา Spread, Commission และ Slippage ที่ใช้ในการทดสอบ',
			'backtest_disclaimer' => 'ผลการทดสอบย้อนหลังใช้เพื่อการศึกษาเท่านั้น ไม่ได้รับประกันผลลัพธ์ในอนาคต และไม่ใช่คำแนะนำในการลงทุน',

			/* หน้า Forward Test */
			'forward_sub'        => 'ทดสอบกับตลาดจริงบนบัญชีเดโม เซ็นต์ หรือบัญชีจริง และต่างจาก Backtest อย่างไร',
			'forward_intro'      => 'Forward Test คือการรันระบบกับสภาวะตลาดจริงแบบเรียลไทม์ สะท้อนสภาพการเทรดจริงได้ดีกว่าการทดสอบย้อนหลัง เมื่อมีผลที่ตรวจสอบได้ ข้อมูลจะแสดงและอัปเดตที่หน้านี้',
			'fw_stat1_label'     => 'ช่วงเวลาทดสอบ',
			'fw_stat1_value'     => 'ระบุช่วงเวลา',
			'fw_stat2_label'     => 'ประเภทบัญชี',
			'fw_stat2_value'     => 'ระบุ Real / Demo',
			'fw_stat3_label'     => 'คู่เงิน / สินทรัพย์',
			'fw_stat3_value'     => 'เช่น XAUUSD',
			'fw_stat4_label'     => 'ทุนเริ่มต้น',
			'fw_stat4_value'     => 'ระบุทุน',
			'fw_stat5_label'     => 'ผลตอบแทนสะสม',
			'fw_stat5_value'     => 'ระบุ %',
			'fw_stat6_label'     => 'Max Drawdown',
			'fw_stat6_value'     => 'ระบุ %',
			'forward_img'        => '',
			'forward_img_caption'=> 'ภาพผลการทดสอบจาก Myfxbook / FX Blue / MT5',
			'forward_link_label' => '',
			'forward_link_url'   => '',
			'forward_note'       => 'หมายเหตุ: ผลในช่วงเวลาหนึ่งไม่ได้บ่งบอกถึงผลในอีกช่วงเวลาหนึ่ง',
			'forward_disclaimer' => 'ผลการทดสอบบนบัญชีจริง/เดโม่สะท้อนช่วงเวลาที่ทดสอบเท่านั้น ผลในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต และไม่ใช่คำแนะนำในการลงทุน',

			/* หน้า How to Install */
			'install_sub'       => 'คู่มือติดตั้งและเริ่มใช้งานทีละขั้นตอน',
			'install_intro'     => 'ทำตามขั้นตอนด้านล่างเพื่อเริ่มใช้งาน FALCON PRO EA หากติดขั้นตอนไหน ทีมงานพร้อมช่วยเหลือผ่าน LINE',
			'install_req'       => "บัญชีเทรดของโบรกเกอร์ที่รองรับ MetaTrader 5\nโปรแกรม MetaTrader 5 (PC หรือ VPS)\nไฟล์ FALCON PRO EA ที่ได้รับหลังสั่งซื้อ\nแนะนำใช้ VPS เพื่อให้ระบบทำงานต่อเนื่อง 24 ชม.",
			'inst_step1_title'  => 'ติดตั้ง MetaTrader 5 / เตรียม VPS',
			'inst_step1_desc'   => 'ดาวน์โหลดและติดตั้ง MT5 จากโบรกเกอร์ของคุณ หากต้องการให้ระบบรันตลอด 24 ชม. แนะนำให้เช่า VPS แล้วติดตั้ง MT5 บน VPS แทนเครื่องส่วนตัว',
			'inst_step1_img'    => '',
			'inst_step2_title'  => 'เปิดโฟลเดอร์ Experts แล้วนำไฟล์ EA เข้า',
			'inst_step2_desc'   => 'ใน MT5 ไปที่เมนู File → Open Data Folder → MQL5 → Experts จากนั้นวางไฟล์ FALCON PRO EA ลงในโฟลเดอร์นี้ แล้วปิด-เปิด MT5 หรือกด Refresh',
			'inst_step2_img'    => '',
			'inst_step3_title'  => 'ลาก EA ขึ้นกราฟและตั้งค่า',
			'inst_step3_desc'   => 'เปิดกราฟคู่เงินที่ต้องการ แล้วลาก FALCON PRO EA จากหน้าต่าง Navigator ขึ้นกราฟ ตั้งค่าพารามิเตอร์ตามคำแนะนำ เช่น Lot และระดับความเสี่ยงให้เหมาะกับทุน',
			'inst_step3_img'    => '',
			'inst_step4_title'  => 'เปิด AutoTrading',
			'inst_step4_desc'   => 'กดปุ่ม AutoTrading (Algo Trading) ด้านบนให้เป็นสีเขียว และตรวจสอบว่ามีไอคอนหน้ายิ้มมุมขวาบนของกราฟ แสดงว่า EA พร้อมทำงาน',
			'inst_step4_img'    => '',
			'inst_step5_title'  => 'ตรวจสอบการทำงานผ่าน Dashboard',
			'inst_step5_desc'   => 'สังเกตสถานะระบบบนกราฟและแท็บ Experts/Journal ว่าทำงานปกติ ติดตามผลและเงื่อนไขการเทรดได้จาก Dashboard ของระบบ',
			'inst_step5_img'    => '',
			'inst_step6_title'  => 'ปรับความเสี่ยงให้เหมาะกับตัวเอง',
			'inst_step6_desc'   => 'ทบทวนการตั้งค่าความเสี่ยงเป็นระยะ ใช้เงินเย็น และปรับ Lot ให้สอดคล้องกับทุน เพื่อให้ Drawdown อยู่ในระดับที่รับได้',
			'inst_step6_img'    => '',
			'install_note'      => 'ต้องการให้ทีมงานช่วยติดตั้งให้? ทักมาทาง LINE ได้เลย',

			/* หน้า Pricing (เพิ่มเติม) */
			'pricing_sub'   => 'เลือกแพ็กเกจที่เหมาะกับการใช้งานของคุณ',
			'compare_title' => 'ตารางเปรียบเทียบแพ็กเกจ',
			'compare_rows'  => "รายการ | Starter | Pro | VIP\nจำนวนบัญชีที่ใช้ได้ | 1 | 1-2 | ตามตกลง\nไฟล์ Preset ตั้งค่าพร้อมใช้ | ✗ | ✓ | ✓\nทีมช่วยติดตั้ง / VPS | ✗ | ✗ | ✓\nอัปเดตระบบ | ✓ | ✓ | ✓\nระดับการ Support | พื้นฐาน | ส่วนตัว | ใกล้ชิด",

			/* หน้า Risk Disclosure */
			'riskpage_sub'     => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งาน',
			'riskpage_intro'   => 'โปรดอ่านและทำความเข้าใจข้อมูลความเสี่ยงต่อไปนี้อย่างละเอียดก่อนตัดสินใจใช้งาน FALCON PRO EA หรือทำการเทรดใด ๆ',
			'riskpage_image'   => '',
			'riskpage_image_caption' => 'ตัวอย่างแนวทางตั้งค่าความเสี่ยงและ Lot Size ให้เหมาะสมกับทุน',
			'rp_block1_title'  => 'ความเสี่ยงของการเทรด',
			'rp_block1_text'   => 'การเทรด Forex, ทองคำ, CFD และสินทรัพย์ทางการเงินอื่น ๆ มีความเสี่ยงสูงต่อเงินทุนของคุณ ราคาอาจเคลื่อนไหวผันผวนรุนแรง คุณอาจสูญเสียเงินลงทุนบางส่วนหรือทั้งหมด จึงควรใช้เฉพาะเงินเย็นที่พร้อมรับความเสี่ยงได้',
			'rp_block2_title'  => 'ไม่มีการรับประกันผลกำไร',
			'rp_block2_text'   => 'FALCON PRO EA เป็นเครื่องมือช่วยเทรดที่ทำงานตามเงื่อนไขที่กำหนดไว้ ไม่ใช่ระบบที่รับประกันผลกำไร ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง และเงื่อนไขของโบรกเกอร์',
			'rp_block3_title'  => 'ผลในอดีตไม่ได้บ่งบอกอนาคต',
			'rp_block3_text'   => 'ผลการทดสอบย้อนหลัง (Backtest) และผลการทดสอบบนบัญชีจริง/เดโม่ (Forward Test) สะท้อนเฉพาะช่วงเวลาที่ทดสอบเท่านั้น ไม่ได้รับประกันว่าผลในอนาคตจะเป็นไปในทิศทางเดียวกัน',
			'rp_block4_title'  => 'ความรับผิดชอบของผู้ใช้งาน',
			'rp_block4_text'   => 'ผู้ใช้งานเป็นผู้รับผิดชอบการตัดสินใจเทรดและการตั้งค่าระบบด้วยตนเอง รวมถึงการบริหารความเสี่ยง การเลือกโบรกเกอร์ และการดูแลบัญชีของตนเอง',
			'rp_block5_title'  => 'ไม่ใช่คำแนะนำการลงทุน',
			'rp_block5_text'   => 'ข้อมูลทั้งหมดบนเว็บไซต์นี้จัดทำขึ้นเพื่อให้ข้อมูลเกี่ยวกับเครื่องมือเท่านั้น ไม่ถือเป็นคำแนะนำทางการเงินหรือการลงทุน หากต้องการคำแนะนำเฉพาะบุคคล ควรปรึกษาผู้เชี่ยวชาญที่ได้รับอนุญาต',
			'rp_block6_title'  => '',
			'rp_block6_text'   => '',
			'riskpage_updated' => 'ปรับปรุงล่าสุด: ระบุวันที่',
		)
	);

	/* ===== ส่วนเสริมความเชื่อใจหน้าแรก (ทีมงาน / ความมั่นใจ / Mid CTA / Pricing teaser / ลิงก์ผล verified) ===== */
	$d = array_merge(
		$d,
		array(

			/* ทีมงาน / ใครอยู่เบื้องหลัง (ปิดไว้ก่อน จนกว่าเจ้าของจะกรอกข้อมูลจริง) */
			'show_team'   => false,
			'team_kicker' => 'Who We Are',
			'team_title'  => 'ทีมที่อยู่เบื้องหลัง FALCON PRO EA',
			'team_text'   => "FALCON PRO EA พัฒนาและดูแลโดยทีมงานที่ติดตามตลาดและการเทรดบน MetaTrader 5 เราตั้งใจสร้างเครื่องมือที่ช่วยให้การเทรดเป็นระบบและตรวจสอบได้ พร้อมดูแลผู้ใช้งานผ่าน LINE หลังเริ่มใช้งานจริง",
			'team_points' => "ดูแลผู้ใช้งานผ่าน LINE ภาษาไทย\nให้ความสำคัญกับการบริหารความเสี่ยง\nอัปเดตระบบตามรอบเวอร์ชัน",
			'team_img'    => '',

			/* ความมั่นใจก่อนเริ่มใช้งาน */
			'show_assurance'     => true,
			'assurance_kicker'   => 'Before You Start',
			'assurance_title'    => 'สบายใจก่อนเริ่มใช้งาน',
			'assurance_subtitle' => 'เราอยากให้คุณเข้าใจระบบและมั่นใจก่อนตัดสินใจ ไม่ใช่เร่งให้รีบซื้อ',
			'assurance_items'    => "ทดลองแนวคิดระบบบนบัญชี Demo ก่อนได้\nมีทีมไทยช่วยแนะนำการติดตั้งจนระบบทำงานได้จริง\nสอบถามและปรึกษาทีมก่อนตัดสินใจได้\nอัปเดตระบบตามรอบเวอร์ชันอย่างต่อเนื่อง",

			/* Mid CTA (แถบทัก LINE คั่นกลางหน้า) */
			'show_mid_cta'  => true,
			'mid_cta_title' => 'ยังไม่แน่ใจว่าเหมาะกับทุนของคุณไหม?',
			'mid_cta_text'  => 'ทักมาให้ทีมช่วยประเมินความเหมาะสมก่อนตัดสินใจได้ ไม่มีข้อผูกมัด',

			/* Pricing teaser หน้าแรก (ใช้แพ็กเกจร่วมกับหมวด 10) */
			'show_pricing_home'  => true,
			'pricing_home_title' => 'แพ็กเกจการใช้งาน',
			'pricing_home_sub'   => 'เลือกระดับการดูแลที่เหมาะกับคุณ ดูรายละเอียดทั้งหมดได้ที่หน้าแพ็กเกจ',

			/* ปุ่มลิงก์ผลที่ตรวจสอบได้ (เช่น Myfxbook) บนหน้าแรก */
			'verified_link_label' => 'ดูผลแบบเรียลไทม์',
			'verified_link_url'   => '',

			/* บทความล่าสุดบนหน้าแรก (ซ่อนอัตโนมัติเมื่อยังไม่มีบทความที่เผยแพร่) */
			'show_blog'      => true,
			'blog_kicker'    => 'Articles',
			'blog_title'     => 'บทความและความรู้',
			'blog_subtitle'  => 'รวมบทความเกี่ยวกับ EA การเทรดอัตโนมัติ และการบริหารความเสี่ยงบน MetaTrader 5',
			'blog_all_label' => 'ดูบทความทั้งหมด',
		)
	);

	/* โมดูล (inc/modules/*.php) เพิ่มค่าเริ่มต้นของตัวเองผ่านฟิลเตอร์นี้ */
	$d = apply_filters( 'fenix_defaults', $d );

	return $d;
}

/**
 * อ่านค่า theme mod พร้อม fallback เป็นค่าเริ่มต้น
 */
function fenix_mod( $key ) {
	$defaults = fenix_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( $key, $default );
}

/**
 * แปลง textarea เป็น array รายการ (บรรทัดละ 1 รายการ)
 */
function fenix_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * ตรวจว่าค่ายังเป็น placeholder (ยังไม่กรอกจริง) หรือไม่
 * ใช้ซ่อนสถิติที่ยังขึ้นต้นด้วย "ระบุ" / "เช่น" ไม่ให้หน้าแรกดูเหมือนยังทำไม่เสร็จ
 */
function fenix_is_placeholder( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return true;
	}
	foreach ( array( 'ระบุ', 'เช่น' ) as $needle ) {
		if ( 0 === mb_strpos( $value, $needle ) ) {
			return true;
		}
	}
	return false;
}

/**
 * แปลงลิงก์ที่ตั้งค่าได้ ให้รองรับทั้ง URL เต็ม, anchor และ slug ภายในเว็บ
 */
function fenix_link_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( '#' === $url || 0 === strpos( $url, '#' ) || preg_match( '#^(https?:)?//#i', $url ) || preg_match( '#^(mailto|tel):#i', $url ) ) {
		return $url;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * URL โลโก้ (ใช้โลโก้ที่อัปโหลดเอง ถ้าไม่มีใช้โลโก้ที่ฝังมากับธีม)
 */
function fenix_logo_url() {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return get_template_directory_uri() . '/assets/img/logo.png';
}

/**
 * URL โลโก้ตัวอักษร (wordmark) · 'dark' = ตัวอักษรเข้มสำหรับพื้นขาว, 'light' = ตัวอักษรขาวสำหรับพื้นดำ
 * ตั้งรูปเองได้ที่ ปรับแต่ง → ช่องทางติดต่อ (wordmark_dark / wordmark_light)
 */
function fenix_wordmark_url( $variant = 'dark' ) {
	$variant = 'light' === $variant ? 'light' : 'dark';
	$custom  = fenix_mod( 'wordmark_' . $variant );
	if ( $custom ) {
		return $custom;
	}
	return get_template_directory_uri() . '/assets/img/brand/falcon-pro-wordmark-' . $variant . '.webp';
}

/**
 * ไอคอนในวงกลม (ดำ/เขียว) ตามสไตล์แบนเนอร์ FALCON
 */
function fenix_icon_badge( $name, $variant = 'dark' ) {
	return '<span class="ic-badge ic-badge--' . esc_attr( $variant ) . '">' . fenix_icon( $name ) . '</span>';
}

/**
 * เมนูสำรอง กรณียังไม่ได้สร้างเมนูใน WordPress
 * ชี้ไปยังหน้าย่อยตาม slug ที่แนะนำ (ปรับเมนูจริงได้ที่ รูปแบบ → เมนู)
 */
function fenix_fallback_menu() {
	if ( function_exists( 'fenix_chrome_fallback_menu' ) ) {
		fenix_chrome_fallback_menu();
	}
}

/**
 * Language switcher slot.
 *
 * This prefers multilingual plugins for real translated URLs, hreflang, SEO,
 * and Elementor compatibility. The manual fallback is only a visible starter
 * until a plugin such as TranslatePress, Polylang, or WPML owns translations.
 */
function fenix_language_switcher() {
	if ( ! fenix_mod( 'show_language_switcher' ) ) {
		return;
	}

	$plugin_markup = '';
	$plugin_class  = '';

	if ( shortcode_exists( 'language-switcher' ) ) {
		$plugin_markup = do_shortcode( '[language-switcher]' );
	} elseif ( function_exists( 'pll_the_languages' ) ) {
		$plugin_markup = pll_the_languages(
			array(
				'echo'          => 0,
				'show_flags'    => 0,
				'show_names'    => 1,
				'hide_if_empty' => 0,
			)
		);
	} elseif ( function_exists( 'icl_get_languages' ) ) {
		$wpml_languages = icl_get_languages( 'skip_missing=0&orderby=code' );

		if ( is_array( $wpml_languages ) && $wpml_languages ) {
			$plugin_markup = '<ul class="language-switcher-list">';
			foreach ( $wpml_languages as $language ) {
				if ( empty( $language['url'] ) || empty( $language['native_name'] ) ) {
					continue;
				}

				$plugin_markup .= sprintf(
					'<li><a class="%1$s" href="%2$s">%3$s</a></li>',
					! empty( $language['active'] ) ? 'is-active' : '',
					esc_url( $language['url'] ),
					esc_html( $language['native_name'] )
				);
			}
			$plugin_markup .= '</ul>';
		}
	}

	if ( $plugin_markup ) {
		echo '<div class="language-switcher language-switcher--plugin' . esc_attr( $plugin_class ) . '" aria-label="' . esc_attr__( 'Language switcher', 'falcon-pro' ) . '">';
		echo wp_kses_post( $plugin_markup );
		echo '</div>';
		return;
	}

	$languages = array();
	foreach ( fenix_lines( fenix_mod( 'language_fallback_items' ) ) as $language_line ) {
		$parts = array_map( 'trim', explode( '|', $language_line ) );
		if ( count( $parts ) < 3 ) {
			continue;
		}

		$code = sanitize_key( $parts[0] );
		if ( ! $code ) {
			continue;
		}

		if ( count( $parts ) >= 4 ) {
			$flag  = $parts[1];
			$short = $parts[2];
			$label = $parts[3];
		} else {
			$flag  = '';
			$short = $parts[1];
			$label = $parts[2];
		}

		$languages[ $code ] = array(
			'flag'  => $flag,
			'short' => $short,
			'label' => $label,
		);
	}

	if ( ! $languages ) {
		$languages = array(
			'th' => array(
				'flag'  => '🇹🇭',
				'short' => 'TH',
				'label' => 'ไทย',
			),
			'en' => array(
				'flag'  => '🇬🇧',
				'short' => 'EN',
				'label' => 'English',
			),
		);
	}

	$current = substr( get_locale(), 0, 2 );
	if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = sanitize_key( wp_unslash( $_GET['lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( ! isset( $languages[ $current ] ) ) {
		$current = 'th';
	}
	?>
	<details class="language-switcher language-switcher--fallback">
		<summary aria-label="<?php echo esc_attr__( 'Choose language', 'falcon-pro' ); ?>">
			<span class="language-switcher-current">
				<?php if ( ! empty( $languages[ $current ]['flag'] ) ) : ?>
					<span class="language-switcher-flag" aria-hidden="true"><?php echo esc_html( $languages[ $current ]['flag'] ); ?></span>
				<?php endif; ?>
				<span class="language-switcher-code"><?php echo esc_html( $languages[ $current ]['short'] ); ?></span>
			</span>
		</summary>
		<div class="language-switcher-menu">
			<?php foreach ( $languages as $code => $language ) : ?>
				<a class="<?php echo esc_attr( $code === $current ? 'is-active' : '' ); ?>" href="<?php echo esc_url( add_query_arg( 'lang', $code ) ); ?>">
					<?php if ( ! empty( $language['flag'] ) ) : ?>
						<span class="language-switcher-flag" aria-hidden="true"><?php echo esc_html( $language['flag'] ); ?></span>
					<?php endif; ?>
					<span class="language-switcher-code"><?php echo esc_html( $language['short'] ); ?></span>
					<small><?php echo esc_html( $language['label'] ); ?></small>
				</a>
			<?php endforeach; ?>
		</div>
	</details>
	<?php
}

/**
 * หัวหน้าเพจ (page hero) ใช้ร่วมกันทุกหน้าย่อย · มี breadcrumb (+ BreadcrumbList schema ใน inc/seo.php)
 */
function fenix_page_hero( $kicker, $title, $subtitle = '' ) {
	$crumbs = fenix_breadcrumbs( $title );
	?>
	<section class="phero">
		<div class="container phero-inner reveal">
			<?php if ( count( $crumbs ) > 1 ) : ?>
				<nav class="crumbs" aria-label="เส้นทางนำทาง">
					<ol>
						<?php foreach ( $crumbs as $fenix_i => $fenix_crumb ) : ?>
							<li>
								<?php if ( $fenix_crumb['url'] && $fenix_i < count( $crumbs ) - 1 ) : ?>
									<a href="<?php echo esc_url( $fenix_crumb['url'] ); ?>"><?php echo esc_html( $fenix_crumb['name'] ); ?></a>
								<?php else : ?>
									<span aria-current="page"><?php echo esc_html( $fenix_crumb['name'] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				</nav>
			<?php endif; ?>
			<?php if ( $kicker ) : ?>
				<span class="kicker"><?php echo esc_html( $kicker ); ?></span>
			<?php endif; ?>
			<h1 class="phero-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="phero-sub"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * การ์ดสถานะ "ยังไม่มีผลทดสอบที่เผยแพร่" (หน้า Backtest / Forward) · ไม่แสดงตัวเลขสมมติ
 */
function fenix_results_pending( $type = 'backtest' ) {
	?>
	<div class="results-pending reveal">
		<?php echo fenix_icon_badge( 'backtest' === $type ? 'candles' : 'pulse' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div>
			<span class="card-label"><?php echo esc_html( fenix_mod( 'results_pending_label' ) ? fenix_mod( 'results_pending_label' ) : ( 'backtest' === $type ? 'Backtest' : 'Forward Test' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'results_pending_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'results_pending_text' ) ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * เส้นทาง breadcrumb ของหน้าปัจจุบัน: หน้าแรก › (กลุ่ม) › หน้านี้
 */
function fenix_breadcrumbs( $title = '' ) {
	$crumbs = array(
		array(
			'name' => fenix_mod( 'nav_home_label' ) ? fenix_mod( 'nav_home_label' ) : 'หน้าแรก',
			'url'  => home_url( '/' ),
		),
	);
	if ( is_front_page() ) {
		return $crumbs;
	}
	if ( is_singular( 'post' ) || is_home() || is_archive() || is_search() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		$crumbs[]   = array(
			'name' => 'บทความ',
			'url'  => $posts_page ? get_permalink( $posts_page ) : home_url( '/articles/' ),
		);
		if ( is_home() ) {
			array_pop( $crumbs );
			$crumbs[] = array(
				'name' => 'บทความ',
				'url'  => '',
			);
			return $crumbs;
		}
	} elseif ( is_page() && function_exists( 'fenix_site_pages' ) ) {
		$slug   = (string) get_post_field( 'post_name', get_queried_object_id() );
		$pages  = fenix_site_pages();
		$group  = isset( $pages[ $slug ]['group'] ) ? $pages[ $slug ]['group'] : '';
		$parent = array(
			'test'  => array( fenix_mod( 'nav_test_label' ) ? fenix_mod( 'nav_test_label' ) : 'การทดสอบ', '/backtest/' ),
			'guide' => array( fenix_mod( 'nav_guide_label' ) ? fenix_mod( 'nav_guide_label' ) : 'คู่มือการใช้งาน', '/how-to-install/' ),
		);
		// เมื่อมีปลั๊กอิน SEO: ให้ breadcrumb ที่มองเห็นตรงกับ BreadcrumbList ของปลั๊กอิน (ไม่ใส่ระดับกลุ่มที่ปลั๊กอินไม่รู้จัก)
		if ( isset( $parent[ $group ] ) && ! in_array( $slug, array( 'backtest', 'how-to-install' ), true ) && ! ( function_exists( 'fenix_has_seo_plugin' ) && fenix_has_seo_plugin() ) ) {
			$crumbs[] = array(
				'name' => $parent[ $group ][0],
				'url'  => home_url( $parent[ $group ][1] ),
			);
		}
	}
	$crumbs[] = array(
		'name' => $title ? $title : wp_strip_all_tags( get_the_title() ),
		'url'  => is_singular() ? get_permalink() : '',
	);
	return $crumbs;
}

/**
 * เนื้อหาแบบยาวของเพจ (จาก editor) + สารบัญอัตโนมัติ · ใช้ต่อท้ายเทมเพลตเพจที่มีส่วนออกแบบไว้ด้านบน
 */
function fenix_page_longform( $section_class = 'section' ) {
	$content = apply_filters( 'the_content', get_the_content() );
	if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
		return;
	}
	$toc = isset( $GLOBALS['fenix_toc'] ) ? $GLOBALS['fenix_toc'] : array();
	$toc = array_values(
		array_filter(
			$toc,
			function ( $item ) {
				return 2 === $item['level'];
			}
		)
	);
	?>
	<section class="<?php echo esc_attr( $section_class ); ?> longform">
		<div class="container">
			<div class="longform-layout<?php echo count( $toc ) > 2 ? '' : ' longform-layout--solo'; ?>">
				<?php if ( count( $toc ) > 2 ) : ?>
					<aside class="longform-toc" aria-label="<?php echo esc_attr( fenix_mod( 'doc_toc_label' ) ? fenix_mod( 'doc_toc_label' ) : 'สารบัญ' ); ?>">
						<details open>
							<summary><?php echo esc_html( fenix_mod( 'doc_toc_label' ) ? fenix_mod( 'doc_toc_label' ) : 'สารบัญ' ); ?></summary>
							<ol>
								<?php foreach ( $toc as $fenix_item ) : ?>
									<li><a href="#<?php echo esc_attr( $fenix_item['id'] ); ?>"><?php echo esc_html( $fenix_item['text'] ); ?></a></li>
								<?php endforeach; ?>
							</ol>
						</details>
					</aside>
				<?php endif; ?>
				<div class="entry-content guide-content">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * บล็อกติดต่อทีมงาน (LINE OA / OpenChat / QR + สิ่งที่ทีมจะถาม) · ใช้ปิดท้ายทุกหน้าย่อย
 */
function fenix_line_cta( $title = '', $sub = '' ) {
	/* โมดูล chrome (inc/modules/chrome.php) เป็นผู้วาดแถบติดต่อท้ายหน้า · ที่นี่แค่ส่งต่อ */
	if ( has_action( 'fenix_line_cta' ) ) {
		do_action( 'fenix_line_cta', $title, $sub );
		return;
	}
	$title = $title ? $title : fenix_mod( 'contact_title' );
	$sub   = $sub ? $sub : fenix_mod( 'contact_text' );
	?>
	<section class="section contact-block<?php echo esc_attr( fenix_section_tone( 'contact', true ) ); ?>" id="cta">
		<div class="container container-narrow">
			<div class="contact-console reveal">
				<div class="contact-main">
					<h2><?php echo esc_html( $title ); ?></h2>
					<p><?php echo esc_html( $sub ); ?></p>
					<div class="contact-actions"><?php fenix_contact_button( array( 'class' => 'btn btn-fire btn-lg', 'pos' => 'page-cta' ) ); ?></div>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/* --------------------------------------------------------------
 * Inline SVG icons
 * -------------------------------------------------------------- */
function fenix_icon( $name, $class = 'icon' ) {
	$svg = array(
		'flame'    => '<path d="M12 2c1 4-3 5.5-3 9a3 3 0 0 0 6 0c0-1.2-.6-2.2-1.2-3.1C16.5 9.4 19 11.6 19 15a7 7 0 0 1-14 0c0-5 5-7.5 7-13z"/>',
		'pulse'    => '<path d="M3 12h4l2.5-6 4 12L16 12h5"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
		'gauge'    => '<path d="M4.5 19a9 9 0 1 1 15 0"/><path d="M12 13l4-4"/><circle cx="12" cy="14" r="1.6"/>',
		'flag'     => '<path d="M5 21V4"/><path d="M5 5h12l-2.5 3.5L17 12H5"/>',
		'home'     => '<path d="M4 11.5 12 5l8 6.5"/><path d="M6.5 10.5V20h11v-9.5"/><path d="M10 20v-5h4v5"/>',
		'chart'    => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M7 15l3-3 2.4 2.4L17.5 9"/><path d="M15 9h2.5v2.5"/>',
		'tag'      => '<path d="M20 12.5 12.5 20 4 11.5V4h7.5L20 12.5z"/><circle cx="8.2" cy="8.2" r="0.8"/>',
		'cpu'      => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="10" y="10" width="4" height="4"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
		'candles'  => '<path d="M7 6v3M7 15v3M7 9h0a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 7 15h0a1.5 1.5 0 0 1-1.5-1.5v-3A1.5 1.5 0 0 1 7 9zM17 3v3M17 13v4M17 6h0a1.5 1.5 0 0 1 1.5 1.5v4A1.5 1.5 0 0 1 17 13h0a1.5 1.5 0 0 1-1.5-1.5v-4A1.5 1.5 0 0 1 17 6z"/><path d="M3 21h18"/>',
		'layout'   => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11"/>',
		'shield'   => '<path d="M12 3l7 3v5c0 4.6-3 8.4-7 10-4-1.6-7-5.4-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
		'moon'     => '<path d="M20 14.5A8 8 0 1 1 9.5 4 6.5 6.5 0 0 0 20 14.5z"/>',
		'headset'  => '<path d="M4 13a8 8 0 0 1 16 0"/><rect x="3" y="13" width="4" height="6" rx="1.6"/><rect x="17" y="13" width="4" height="6" rx="1.6"/><path d="M19 19a3 3 0 0 1-3 3h-3"/>',
		'check'    => '<path d="M4 12.5l5 5L20 6.5"/>',
		'x'        => '<path d="M6 6l12 12M18 6L6 18"/>',
		'warn'     => '<path d="M12 3.5l9.5 16.5h-19L12 3.5z"/><path d="M12 10v4.2"/><circle cx="12" cy="17" r="0.4"/>',
		'chat'     => '<path d="M21 12a8 8 0 0 1-8 8c-1.2 0-2.4-.25-3.4-.7L4 21l1.4-4.2A8 8 0 1 1 21 12z"/><path d="M8.5 11h.01M12 11h.01M15.5 11h.01"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'quote'    => '<path d="M7.5 11c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V9.5C10.5 7 9 5.5 7 5M17.5 11c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V9.5C20.5 7 19 5.5 17 5"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'facebook' => '<path d="M14 8h2.5V4.5H14c-2.2 0-4 1.8-4 4V11H7.5v3.5H10v6h3.5v-6h2.6l.4-3.5h-3V8.7c0-.4.3-.7.5-.7z"/>',
		'download' => '<path d="M12 4v10M7.5 10.5L12 15l4.5-4.5"/><path d="M5 19h14"/>',
		'link'     => '<path d="M9.5 14.5l5-5"/><path d="M11.5 6.5l1-1a4 4 0 0 1 5.7 5.7l-2 2"/><path d="M12.5 17.5l-1 1a4 4 0 0 1-5.7-5.7l2-2"/>',
		'gear'     => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
		'bars'     => '<path d="M6 20v-6M12 20V10M18 20V4"/>',
		'robot'    => '<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 8V5"/><circle cx="12" cy="4" r="1"/><circle cx="9" cy="13.5" r="1"/><circle cx="15" cy="13.5" r="1"/><path d="M9.5 17h5"/>',
		'play'     => '<path d="M8 5.5v13l10.5-6.5L8 5.5z"/>',
		'bolt'     => '<path d="M13 2 4.5 13.5H11L10 22l8.5-11.5H12L13 2z"/>',
		'target'   => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="0.8"/>',
		'server'   => '<rect x="4" y="4" width="16" height="7" rx="1.6"/><rect x="4" y="13" width="16" height="7" rx="1.6"/><path d="M8 7.5h.01M8 16.5h.01"/>',
		'phone'    => '<rect x="7" y="2.5" width="10" height="19" rx="2.2"/><path d="M11 18.5h2"/>',
		'monitor'  => '<rect x="3" y="4" width="18" height="12" rx="1.8"/><path d="M8 20h8M12 16v4"/>',
		'calc'     => '<rect x="5" y="2.5" width="14" height="19" rx="2"/><path d="M8 6.5h8"/><path d="M8.5 11h.01M12 11h.01M15.5 11h.01M8.5 14.5h.01M12 14.5h.01M15.5 14.5h.01M8.5 18h.01M12 18h.01M15.5 18h.01"/>',
		'book'     => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15z"/><path d="M4 20.5A2.5 2.5 0 0 1 6.5 18H20v3H6.5"/>',
		'lock'     => '<rect x="5" y="10.5" width="14" height="10" rx="2"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/>',
		'trash'    => '<path d="M4 7h16M9.5 7V4.5h5V7M6.5 7l1 13h9l1-13"/>',
		'windows'  => '<path d="M3.5 5.5 10.5 4.5v7h-7zM12 4.3l8.5-1.3v8.5H12zM3.5 12.5h7v7l-7-1zM12 12.5h8.5V21L12 19.7z"/>',
		'apple'    => '<path d="M16.4 12.6c0-2.3 1.9-3.4 2-3.5-1.1-1.6-2.8-1.8-3.4-1.8-1.4-.1-2.8.9-3.5.9-.7 0-1.9-.9-3.1-.8-1.6 0-3 .9-3.8 2.3-1.6 2.8-.4 7 1.2 9.3.8 1.1 1.7 2.4 2.9 2.3 1.2 0 1.6-.7 3-.7s1.8.7 3.1.7c1.3 0 2.1-1.1 2.8-2.3.9-1.3 1.3-2.6 1.3-2.6s-2.5-1-2.5-3.8zM14.1 5.8c.6-.8 1.1-1.9 1-3-1 0-2.1.7-2.8 1.5-.6.7-1.1 1.8-1 2.9 1.1.1 2.1-.6 2.8-1.4z"/>',
		'android'  => '<path d="M6 10v6.5a1 1 0 0 0 1 1h1v3h2v-3h4v3h2v-3h1a1 1 0 0 0 1-1V10H6z"/><path d="M6.5 9a5.5 5.5 0 0 1 11 0z"/><path d="M8 4l1.5 2M16 4l-1.5 2"/>',
		'external' => '<path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
		'image'    => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.6"/><path d="M21 16l-5.5-5.5L6 20"/>',
		'flask'    => '<path d="M9 3h6"/><path d="M10 3v6L4.8 18.2A1.8 1.8 0 0 0 6.4 21h11.2a1.8 1.8 0 0 0 1.6-2.8L14 9V3"/><path d="M7.5 15h9"/>',
		'macos'    => '<rect x="3" y="4" width="18" height="12" rx="1.8"/><path d="M2 20h20"/>',
		'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18.5 20a6.5 6.5 0 0 0-3-5.5"/>',
		'terminal' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9l3 3-3 3M12.5 15H17"/>',
		'qr'       => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM20 14v.01M14 20h.01M17 20h4v-3"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r="0.6"/>',
		'tiktok'   => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.5 2.6 2.3 4.3 5 4.5"/>',
		'youtube'  => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.5v5l4.5-2.5z"/>',
		'dollar'   => '<path d="M12 2v20"/><path d="M17 6.5c-1-1.3-2.7-2-5-2-2.8 0-4.5 1.4-4.5 3.4 0 4.6 10 2.4 10 7.2 0 2-1.9 3.4-5 3.4-2.4 0-4.3-.8-5.3-2.3"/>',
	);

	// แบรนด์ไอคอน LINE (โลโก้จริง) · เป็น path แบบ fill ไม่ใช่ stroke จึง render แยก.
	if ( 'line' === $name ) {
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true" focusable="false"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.348 0 .63.283.63.63 0 .344-.282.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>';
	}

	if ( ! isset( $svg[ $name ] ) ) {
		return '';
	}

	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $svg[ $name ] . '</svg>';
}

/* --------------------------------------------------------------
 * Post views (ตัวนับยอดเข้าชมบทความ)
 * -------------------------------------------------------------- */
function fenix_get_post_views( $post_id ) {
	return (int) get_post_meta( $post_id, 'fenix_views', true );
}

function fenix_increment_post_views( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	update_post_meta( $post_id, 'fenix_views', fenix_get_post_views( $post_id ) + 1 );
}

/* นับเฉพาะผู้เข้าชมหน้าบทความเดี่ยว (ข้ามแอดมิน เพื่อไม่ให้ตัวเลขเพี้ยน)
   หมายเหตุ: ถ้าใช้ปลั๊กแคชหน้า ตัวเลขอาจนับไม่ครบทุกครั้ง */
add_action(
	'wp_head',
	function () {
		if ( is_singular( 'post' ) && ! current_user_can( 'edit_posts' ) ) {
			fenix_increment_post_views( get_queried_object_id() );
		}
	}
);



/* --------------------------------------------------------------
 * Table of Contents · เก็บหัวข้อ H2/H3 จากเนื้อหาบทความ + ใส่ id ให้ลิงก์
 * ($GLOBALS['fenix_toc'] ถูกเติมตอน the_content ถูกประมวลผล)
 * -------------------------------------------------------------- */
function fenix_collect_toc( $content ) {
	if ( ! ( is_singular( array( 'post', 'page' ) ) && is_main_query() && in_the_loop() ) ) {
		return $content;
	}

	$GLOBALS['fenix_toc'] = array();
	$index                = 0;

	return preg_replace_callback(
		'/<(h[23])([^>]*)>(.*?)<\/\1>/is',
		function ( $matches ) use ( &$index ) {
			$index++;
			$tag   = strtolower( $matches[1] );
			$attrs = $matches[2];
			$inner = $matches[3];

			if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$id     = 'toc-' . $index;
				$attrs .= ' id="' . $id . '"';
			}

			$GLOBALS['fenix_toc'][] = array(
				'level' => (int) substr( $tag, 1 ),
				'text'  => trim( wp_strip_all_tags( $inner ) ),
				'id'    => $id,
			);

			return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'fenix_collect_toc', 20 );

/* ตัดคำนำหน้า "หมวดหมู่:" / "ป้ายกำกับ:" ออกจากหัวข้อหน้า archive */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/* --------------------------------------------------------------
 * การ์ดบทความ (ใช้ร่วมกันที่ index และ AJAX โหลดเพิ่ม)
 * -------------------------------------------------------------- */
function fenix_post_card() {
	$cats = get_the_category();
	$cat  = ! empty( $cats ) ? $cats[0] : null;
	?>
	<article <?php post_class( 'post-card' ); ?>>
		<?php if ( has_post_thumbnail() ) : ?>
			<a class="post-card-thumb" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium_large', array( 'alt' => function_exists( 'fenix_pages_featured_alt' ) ? fenix_pages_featured_alt( get_the_ID() ) : get_the_title() ) ); ?>
				<?php if ( $cat ) : ?>
					<span class="post-card-cat"><?php echo esc_html( $cat->name ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="post-card-body">
			<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
			<span class="post-card-more">อ่านต่อ <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</article>
	<?php
}

/* --------------------------------------------------------------
 * AJAX โหลดบทความเพิ่ม (ปุ่ม "โหลดเพิ่ม")
 * -------------------------------------------------------------- */
function fenix_load_more() {
	check_ajax_referer( 'fenix_load_more', 'nonce' );

	$page = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;

	$incoming = array();
	if ( isset( $_POST['query'] ) ) {
		$decoded = json_decode( wp_unslash( $_POST['query'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $decoded ) ) {
			$incoming = $decoded;
		}
	}

	// รับเฉพาะ query var ที่หน้าเว็บส่งมาได้จริง + sanitize ทีละค่า (กัน inject meta_query/tax_query หนัก ๆ)
	$query = array();
	if ( isset( $incoming['category_name'] ) ) {
		$query['category_name'] = sanitize_text_field( $incoming['category_name'] );
	}
	if ( isset( $incoming['cat'] ) ) {
		$query['cat'] = (int) $incoming['cat'];
	}
	if ( isset( $incoming['tag'] ) ) {
		$query['tag'] = sanitize_text_field( $incoming['tag'] );
	}
	if ( isset( $incoming['author'] ) ) {
		$query['author'] = (int) $incoming['author'];
	}
	if ( isset( $incoming['author_name'] ) ) {
		$query['author_name'] = sanitize_text_field( $incoming['author_name'] );
	}
	if ( isset( $incoming['s'] ) ) {
		$query['s'] = sanitize_text_field( $incoming['s'] );
	}

	// บังคับค่าที่ปลอดภัย ไม่ให้ฝั่ง client กำหนดเอง
	$query['paged']               = $page;
	$query['post_type']           = 'post';
	$query['post_status']         = 'publish';
	$query['posts_per_page']      = (int) get_option( 'posts_per_page' );
	$query['ignore_sticky_posts'] = true;

	$loop = new WP_Query( $query );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			fenix_post_card();
		}
	}
	wp_reset_postdata();
	wp_die();
}
add_action( 'wp_ajax_fenix_load_more', 'fenix_load_more' );
add_action( 'wp_ajax_nopriv_fenix_load_more', 'fenix_load_more' );

/* --------------------------------------------------------------
 * Customizer
 * -------------------------------------------------------------- */
require get_template_directory() . '/inc/components.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/shortcodes.php';
require get_template_directory() . '/inc/seo.php';

/* โมดูลเพิ่มเติม (หน้าแรก, header/footer, คู่มือ, หน้าย่อย, /go, คุกกี้ ฯลฯ) · โหลดตามลำดับชื่อไฟล์ */
foreach ( (array) glob( get_template_directory() . '/inc/modules/*.php' ) as $fenix_module_file ) {
	require $fenix_module_file;
}

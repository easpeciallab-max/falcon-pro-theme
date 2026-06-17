<?php
/**
 * FALCON PRO EA · Theme functions
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FALCON_VERSION', '1.0.0' );

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
		'https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'fenix-style', get_stylesheet_uri(), array( 'fenix-fonts' ), $style_version );
	wp_enqueue_script( 'fenix-main', get_template_directory_uri() . '/assets/js/main.js', array(), $script_version, true );
	wp_localize_script(
		'fenix-main',
		'fenixLoadMore',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'fenix_load_more' ),
		)
	);
	if ( fenix_mod( 'ga_measurement_id' ) || fenix_mod( 'fb_pixel_id' ) ) {
		wp_localize_script(
			'fenix-main',
			'fenixTracking',
			array(
				'ga'    => fenix_mod( 'ga_measurement_id' ),
				'pixel' => fenix_mod( 'fb_pixel_id' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fenix_assets' );

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

	return $classes;
}
add_filter( 'body_class', 'fenix_body_classes' );

/* --------------------------------------------------------------
 * Default content (ทุกค่าแก้ได้ในหน้า "ปรับแต่ง / Customize")
 * -------------------------------------------------------------- */
function fenix_defaults() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}

	$install_assets = get_template_directory_uri() . '/assets/img/install/';

	$d = array(
		/* ทั่วไป */
		'line_url'        => '#',
		'facebook_url'    => '',
		'contact_email'   => '',
		'show_float_line' => false,
		'float_line_text' => 'สอบถามทาง LINE',
		'show_language_switcher' => true,
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
		'hero_badge'     => 'Automated Trading System • MT5',
		'hero_title'     => 'FALCON PRO EA',
		'hero_subtitle'  => 'ระบบช่วยเทรดอัตโนมัติ เพื่อการเทรดที่มีวินัยมากขึ้น',
		'hero_desc'      => 'ออกแบบมาเพื่อช่วยให้การเทรดเป็นระบบ ลดการตัดสินใจตามอารมณ์ พร้อมแนวคิดบริหารความเสี่ยงสำหรับผู้ใช้งาน MetaTrader 5',
		'hero_btn1_text' => 'สอบถามรายละเอียด',
		'hero_btn2_text' => 'รู้จักระบบ',
		'hero_note'      => 'การเทรดมีความเสี่ยง โปรดศึกษาข้อมูลก่อนตัดสินใจใช้งาน',
		'hero_image'     => '',

		/* ปัญหานักเทรด */
		'show_pain'     => true,
		'pain_title'    => 'เทรดเองแล้วเจอปัญหาแบบนี้ไหม?',
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
		'about_title' => 'FALCON PRO EA คืออะไร?',
		'about_text'  => "FALCON PRO EA คือระบบช่วยเทรดอัตโนมัติ (Expert Advisor) บนแพลตฟอร์ม MetaTrader 5 ที่ทำงานตามเงื่อนไขที่กำหนดไว้ล่วงหน้า ช่วยให้การเข้าและออกออเดอร์เป็นระบบมากขึ้น ลดการตัดสินใจจากอารมณ์ และติดตามการทำงานของระบบได้ง่ายผ่าน Dashboard\n\nEA ไม่ใช่เครื่องมือการันตีกำไร แต่เป็นผู้ช่วยให้คุณเทรดตามแผนได้สม่ำเสมอขึ้น ภายใต้การบริหารความเสี่ยงที่คุณเป็นผู้กำหนดเอง",

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
		'gallery_title'    => 'หน้าตาระบบจริง',
		'gallery_subtitle' => 'Dashboard ออกแบบให้ดูง่าย เห็นสถานะระบบ เงื่อนไขหลัก และผลการทำงานในบัญชีได้ชัดเจน',
		'gallery_note'     => '* ภาพใช้เพื่อประกอบการนำเสนอ ไม่ใช่การรับประกันผลลัพธ์',
		'gallery_img1'     => $install_assets . 'step-05.jpg',
		'gallery_cap1'     => 'Dashboard แสดงสถานะการทำงานของ FALCON PRO EA',
		'gallery_img2'     => $install_assets . 'step-06.jpg',
		'gallery_cap2'     => 'ตัวอย่างการตั้งค่าความเสี่ยงและ Lot Size',
		'gallery_img3'     => '',
		'gallery_cap3'     => 'หน้าต่างตั้งค่าหลักของระบบ',
		'gallery_img4'     => '',
		'gallery_cap4'     => 'ตัวอย่างสถานะการทำงาน Buy / Sell',

		/* ขั้นตอนใช้งาน */
		'show_steps'     => true,
		'steps_kicker'   => 'Workflow',
		'steps_title'    => 'เริ่มใช้งานใน 4 ขั้นตอน',
		'steps_subtitle' => 'จากศูนย์จนระบบเริ่มทำงาน โดยมีทีมงานช่วยดูแลตลอดทาง',
		'step1_title'    => 'ติดตั้ง MT5',
		'step1_desc'     => 'เตรียมบัญชีเทรดและติดตั้งแพลตฟอร์ม MetaTrader 5 บนเครื่องหรือ VPS',
		'step2_title'    => 'ติดตั้ง FALCON PRO EA',
		'step2_desc'     => 'ทีมงานช่วยแนะนำการติดตั้งและตั้งค่าเบื้องต้นจนระบบพร้อมทำงาน',
		'step3_title'    => 'ตั้งค่าความเสี่ยง',
		'step3_desc'     => 'เลือก Lot ทุนเริ่มต้น และระดับความเสี่ยงให้เหมาะสมกับพอร์ตของคุณ',
		'step4_title'    => 'ติดตามผลผ่าน Dashboard',
		'step4_desc'     => 'ดูสถานะระบบและผลการทำงานได้อย่างเป็นระบบ โปร่งใส ตรวจสอบได้',

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
		'faq_title'    => 'คำถามที่พบบ่อย',
		'faq_subtitle' => 'รวมคำตอบสำหรับคำถามที่ลูกค้าสอบถามเข้ามามากที่สุด',
		'faq1_q'       => 'FALCON PRO EA คืออะไร?',
		'faq1_a'       => 'ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) ที่ทำงานตามเงื่อนไขที่กำหนดไว้ล่วงหน้า บนแพลตฟอร์ม MetaTrader 5',
		'faq2_q'       => 'EA การันตีกำไรไหม?',
		'faq2_a'       => 'ไม่การันตี ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง และเงื่อนไขของโบรกเกอร์ที่ใช้งาน',
		'faq3_q'       => 'ต้องมีความรู้เทรดมาก่อนไหม?',
		'faq3_a'       => 'ควรมีพื้นฐานเรื่อง Lot, Stop Loss, Drawdown และความเสี่ยง เพื่อให้ตั้งค่าระบบได้เหมาะสมกับทุนของตัวเอง',
		'faq4_q'       => 'ใช้ทุนเริ่มต้นเท่าไร?',
		'faq4_a'       => 'ขึ้นอยู่กับระดับความเสี่ยงที่เลือกและเงื่อนไขของโบรกเกอร์ แนะนำให้ทักมาปรึกษาทีมงานเพื่อประเมินทุนที่เหมาะสมก่อนเริ่มใช้งาน',
		'faq5_q'       => 'ต้องเปิดคอมตลอดเวลาไหม?',
		'faq5_a'       => 'EA ทำงานบน MT5 จึงต้องเปิดเครื่องให้ระบบทำงานต่อเนื่อง หรือใช้บริการ VPS เพื่อให้ระบบทำงานได้ตลอด 24 ชั่วโมง',
		'faq6_q'       => 'รองรับบัญชีแบบไหนบ้าง?',
		'faq6_a'       => 'โดยทั่วไปรองรับบัญชีของโบรกเกอร์ที่ใช้ MetaTrader 5 สอบถามความเข้ากันได้กับโบรกเกอร์ของคุณได้ทาง LINE',
		'faq7_q'       => 'ติดตั้งเองได้ไหม?',
		'faq7_a'       => 'ได้ มีคู่มือประกอบการติดตั้ง และหากไม่สะดวก ทีมงานมีบริการช่วยติดตั้งและตั้งค่าให้จนระบบพร้อมใช้งาน',
		'faq8_q'       => 'การใช้ EA มีความเสี่ยงอะไรบ้าง?',
		'faq8_a'       => 'ความผันผวนของตลาด ข่าวแรง Slippage Spread ที่กว้างขึ้น การตั้งค่าที่ไม่เหมาะสม และการใช้ทุนเกินระดับความเสี่ยงที่รับได้ ทั้งหมดนี้คือสิ่งที่ควรเข้าใจก่อนเริ่มใช้ระบบ',
		'faq9_q'       => '',
		'faq9_a'       => '',
		'faq10_q'      => '',
		'faq10_a'      => '',

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
		'footer_prep_title'    => 'ก่อนทัก LINE',
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
			'show_highlight' => false,
			'highlight1'     => 'ทำงานบน MetaTrader 5',
			'highlight2'     => 'ลดการเทรดด้วยอารมณ์',
			'highlight3'     => 'บริหารความเสี่ยงได้',
			'highlight4'     => 'มีทีมช่วยติดตั้ง',

			/* หน้าแรก · แถบสถานะ / Control center */
			'show_live_status'     => true,
			'live_status_kicker'   => 'Live System Flow',
			'live_status_items'    => "FALCON PRO EA บน MetaTrader 5\nRisk-first setup\nBacktest และ Forward Test\nตั้งค่าตามทุนและความเสี่ยง\nLINE Support ภาษาไทย",
			'show_control_center'  => true,
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
			'home_cards_title' => 'ดูข้อมูลเชิงลึกต่อ',
			'home_cards_sub'   => 'เราแยกข้อมูลเป็นหมวด เพื่อให้คุณศึกษาได้ละเอียดก่อนตัดสินใจ',
			'card1_title'      => 'ผล Backtest',
			'card1_desc'       => 'ผลการทดสอบย้อนหลังพร้อมเงื่อนไขการทดสอบที่ชัดเจน',
			'card1_url'        => '/backtest/',
			'card2_title'      => 'ผล Forward Test',
			'card2_desc'       => 'ผลการทดสอบบนบัญชีจริง/เดโม่แบบเรียลไทม์',
			'card2_url'        => '/forward-test/',
			'card3_title'      => 'แพ็กเกจ & ราคา',
			'card3_desc'       => 'เปรียบเทียบแพ็กเกจและสิ่งที่ได้รับในแต่ละระดับ',
			'card3_url'        => '/pricing/',
			'card4_title'      => 'วิธีติดตั้ง',
			'card4_desc'       => 'คู่มือติดตั้ง EA บน MT5 ทีละขั้นตอน',
			'card4_url'        => '/how-to-install/',
			'card5_title'      => 'คำเตือนความเสี่ยง',
			'card5_desc'       => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งานจริง',
			'card5_url'        => '/risk-disclosure/',

			/* หน้า Backtest */
			'backtest_sub'        => 'ผลการทดสอบย้อนหลัง (Historical Backtest)',
			'backtest_intro'      => 'Backtest คือการนำกลยุทธ์ของ EA มาทดสอบกับข้อมูลราคาในอดีต เพื่อดูพฤติกรรมของระบบภายใต้เงื่อนไขที่กำหนด ผลด้านล่างระบุพารามิเตอร์การทดสอบไว้อย่างชัดเจนเพื่อความโปร่งใส',
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
			'forward_sub'        => 'ผลการทดสอบบนบัญชีจริง / เดโม่ (Forward Test)',
			'forward_intro'      => 'Forward Test คือการรันระบบกับสภาวะตลาดจริงแบบเรียลไทม์ สะท้อนสภาพการเทรดจริงได้ดีกว่าการทดสอบย้อนหลัง ข้อมูลด้านล่างจะอัปเดตตามรอบการทดสอบ',
			'fw_stat1_label'     => 'ช่วงเวลาทดสอบ',
			'fw_stat1_value'     => 'ระบุช่วงเวลา',
			'fw_stat2_label'     => 'ประเภทบัญชี',
			'fw_stat2_value'     => 'Real / Demo',
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
			'inst_step1_img'    => $install_assets . 'step-01.jpg',
			'inst_step2_title'  => 'เปิดโฟลเดอร์ Experts แล้วนำไฟล์ EA เข้า',
			'inst_step2_desc'   => 'ใน MT5 ไปที่เมนู File → Open Data Folder → MQL5 → Experts จากนั้นวางไฟล์ FALCON PRO EA ลงในโฟลเดอร์นี้ แล้วปิด-เปิด MT5 หรือกด Refresh',
			'inst_step2_img'    => $install_assets . 'step-02.jpg',
			'inst_step3_title'  => 'ลาก EA ขึ้นกราฟและตั้งค่า',
			'inst_step3_desc'   => 'เปิดกราฟคู่เงินที่ต้องการ แล้วลาก FALCON PRO EA จากหน้าต่าง Navigator ขึ้นกราฟ ตั้งค่าพารามิเตอร์ตามคำแนะนำ เช่น Lot และระดับความเสี่ยงให้เหมาะกับทุน',
			'inst_step3_img'    => $install_assets . 'step-03.jpg',
			'inst_step4_title'  => 'เปิด AutoTrading',
			'inst_step4_desc'   => 'กดปุ่ม AutoTrading (Algo Trading) ด้านบนให้เป็นสีเขียว และตรวจสอบว่ามีไอคอนหน้ายิ้มมุมขวาบนของกราฟ แสดงว่า EA พร้อมทำงาน',
			'inst_step4_img'    => $install_assets . 'step-04.jpg',
			'inst_step5_title'  => 'ตรวจสอบการทำงานผ่าน Dashboard',
			'inst_step5_desc'   => 'สังเกตสถานะระบบบนกราฟและแท็บ Experts/Journal ว่าทำงานปกติ ติดตามผลและเงื่อนไขการเทรดได้จาก Dashboard ของระบบ',
			'inst_step5_img'    => $install_assets . 'step-05.jpg',
			'inst_step6_title'  => 'ปรับความเสี่ยงให้เหมาะกับตัวเอง',
			'inst_step6_desc'   => 'ทบทวนการตั้งค่าความเสี่ยงเป็นระยะ ใช้เงินเย็น และปรับ Lot ให้สอดคล้องกับทุน เพื่อให้ Drawdown อยู่ในระดับที่รับได้',
			'inst_step6_img'    => $install_assets . 'step-06.jpg',
			'install_note'      => 'ต้องการให้ทีมงานช่วยติดตั้งให้? ทักมาทาง LINE ได้เลย',

			/* หน้า Pricing (เพิ่มเติม) */
			'pricing_sub'   => 'เลือกแพ็กเกจที่เหมาะกับการใช้งานของคุณ',
			'compare_title' => 'ตารางเปรียบเทียบแพ็กเกจ',
			'compare_rows'  => "รายการ | Starter | Pro | VIP\nจำนวนบัญชีที่ใช้ได้ | 1 | 1-2 | ตามตกลง\nไฟล์ Preset ตั้งค่าพร้อมใช้ | ✗ | ✓ | ✓\nทีมช่วยติดตั้ง / VPS | ✗ | ✗ | ✓\nอัปเดตระบบ | ✓ | ✓ | ✓\nระดับการ Support | พื้นฐาน | ส่วนตัว | ใกล้ชิด",

			/* หน้า Risk Disclosure */
			'riskpage_sub'     => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งาน',
			'riskpage_intro'   => 'โปรดอ่านและทำความเข้าใจข้อมูลความเสี่ยงต่อไปนี้อย่างละเอียดก่อนตัดสินใจใช้งาน FALCON PRO EA หรือทำการเทรดใด ๆ',
			'riskpage_image'   => $install_assets . 'step-06.jpg',
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
 * เมนูสำรอง กรณียังไม่ได้สร้างเมนูใน WordPress
 * ชี้ไปยังหน้าย่อยตาม slug ที่แนะนำ (ปรับเมนูจริงได้ที่ รูปแบบ → เมนู)
 */
function fenix_fallback_menu() {
	$items = array(
		'/'                => 'หน้าแรก',
		'/pricing/'        => 'แพ็กเกจ',
		'/how-to-install/' => 'วิธีติดตั้ง',
	);
	echo '<ul class="nav-list">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">หน้าแรก</a></li>';
	echo '<li class="menu-item-has-children"><a href="' . esc_url( home_url( '/forward-test/' ) ) . '">ผลทดสอบ</a>';
	echo '<ul class="sub-menu">';
	echo '<li><a href="' . esc_url( home_url( '/backtest/' ) ) . '">Backtest</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/forward-test/' ) ) . '">Forward Test</a></li>';
	echo '</ul></li>';
	foreach ( $items as $path => $label ) {
		if ( '/' === $path ) {
			continue;
		}
		echo '<li><a href="' . esc_url( home_url( $path ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
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

	if ( shortcode_exists( 'gtranslate' ) ) {
		$plugin_markup = do_shortcode( '[gtranslate]' );
		$plugin_class  = ' language-switcher--gtranslate';
	} elseif ( shortcode_exists( 'language-switcher' ) ) {
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
 * หัวหน้าเพจ (page hero) ใช้ร่วมกันทุกหน้าย่อย
 */
function fenix_page_hero( $kicker, $title, $subtitle = '' ) {
	?>
	<section class="phero">
		<div class="phero-bg" aria-hidden="true">
			<span class="ember ember-a"></span>
			<span class="ember ember-b"></span>
		</div>
		<div class="container phero-inner reveal">
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
 * แถบ CTA ทักไลน์ ใช้ปิดท้ายหน้าย่อย
 */
function fenix_line_cta( $title = '', $sub = '' ) {
	$title = $title ? $title : 'มีคำถาม? ทักมาคุยกับเราได้เลย';
	$sub   = $sub ? $sub : 'สอบถามรายละเอียด การติดตั้ง และความเหมาะสมกับทุนของคุณได้ทาง LINE';
	?>
	<section class="section cta cta--slim" id="cta">
		<div class="cta-bg" aria-hidden="true"><span class="ember ember-c"></span></div>
		<div class="container container-narrow">
			<div class="cta-inner reveal">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo esc_html( $sub ); ?></p>
				<a class="btn btn-line btn-lg" href="<?php echo esc_url( fenix_mod( 'line_url' ) ); ?>" target="_blank" rel="noopener">
					<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					ทัก LINE เพื่อสอบถาม
				</a>
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
 * Open Graph / Twitter meta (รูปแชร์ LINE/Facebook)
 * ปิดอัตโนมัติถ้ามีปลั๊ก SEO (Yoast/Rank Math) เพื่อไม่ให้แท็กซ้ำ
 * -------------------------------------------------------------- */
function fenix_open_graph() {
	$site        = get_bloginfo( 'name' );
	$default_img = fenix_mod( 'og_default_image' );
	if ( ! $default_img ) {
		$default_img = fenix_logo_url();
	}

	if ( is_singular() ) {
		$title = get_the_title();
		$desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 40, '…' );
		$url   = get_permalink();
		$img   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		if ( ! $img ) {
			$img = $default_img;
		}
		$type = is_singular( 'post' ) ? 'article' : 'website';
	} else {
		$title = is_front_page() ? $site : wp_strip_all_tags( wp_get_document_title() );
		$desc  = fenix_mod( 'og_default_description' );
		$url   = home_url( '/' );
		$img   = $default_img;
		$type  = 'website';
	}

	$desc = trim( (string) $desc );

	if ( '' !== $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	$tags = array(
		'og:site_name'   => $site,
		'og:locale'      => get_locale(),
		'og:type'        => $type,
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $url,
		'og:image'       => $img,
	);

	foreach ( $tags as $property => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}
		printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $value ) );
	}

	if ( '' !== (string) $img ) {
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $title ) );
	}

	if ( 'article' === $type ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}

	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( '' !== $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( '' !== (string) $img ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_attr( $img ) );
		printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $title ) );
	}
}
if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
	add_action( 'wp_head', 'fenix_open_graph', 5 );
}

/* --------------------------------------------------------------
 * Structured data: Organization + WebSite (ทั้งเว็บ) + FAQPage (หน้าแรก)
 * ปิดอัตโนมัติถ้ามีปลั๊ก SEO
 * -------------------------------------------------------------- */
function fenix_schema_jsonld() {
	$blocks = array();

	$org = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
		'logo'     => fenix_logo_url(),
	);
	if ( fenix_mod( 'facebook_url' ) ) {
		$org['sameAs'] = array( fenix_mod( 'facebook_url' ) );
	}
	$blocks[] = $org;

	$blocks[] = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	if ( is_front_page() ) {
		$faqs = array();
		for ( $i = 1; $i <= 10; $i++ ) {
			$q = fenix_mod( 'faq' . $i . '_q' );
			$a = fenix_mod( 'faq' . $i . '_a' );
			if ( $q && $a ) {
				$faqs[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $q ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $a ),
					),
				);
			}
		}
		if ( $faqs ) {
			$blocks[] = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $faqs,
			);
		}
	}

	foreach ( $blocks as $fenix_block ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $fenix_block, JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
	add_action( 'wp_head', 'fenix_schema_jsonld', 6 );
}

/* --------------------------------------------------------------
 * Table of Contents · เก็บหัวข้อ H2/H3 จากเนื้อหาบทความ + ใส่ id ให้ลิงก์
 * ($GLOBALS['fenix_toc'] ถูกเติมตอน the_content ถูกประมวลผล)
 * -------------------------------------------------------------- */
function fenix_collect_toc( $content ) {
	if ( ! ( is_singular( 'post' ) && is_main_query() && in_the_loop() ) ) {
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
				<?php the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
				<?php if ( $cat ) : ?>
					<span class="post-card-cat"><?php echo esc_html( $cat->name ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="post-card-body">
			<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
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
require get_template_directory() . '/inc/customizer.php';

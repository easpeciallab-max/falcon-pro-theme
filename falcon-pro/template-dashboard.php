<?php
/**
 * Template Name: FALCON · หน้าแดชบอร์ด
 *
 * ใช้กับเพจ /dashboard/ · อธิบายทางที่สองของการใช้ FALCON: ให้ MT5 กับ EA ทำงานบนเซิร์ฟเวอร์ผ่านแดชบอร์ด
 * โครง: หัวเพจ (ปุ่มเข้าชม + ปุ่มสมัคร + หมายเหตุ) → สองทางในการใช้ FALCON (การ์ดเทียบสองใบ) → ภาพหน้าจอ (จอคอม + มือถือ)
 *       → สิ่งที่ทำได้ → 5 ขั้นเริ่มใช้ + ปุ่ม + หมายเหตุ → เนื้อหายาวจากหน้าแก้ไขเพจ (พื้นขาว · สารบัญ · ตารางชุด EA · ถาม-ตอบ) → บล็อกติดต่อ
 * ข้อความส่วนออกแบบแก้ที่ ปรับแต่ง → 24.1) หน้าแดชบอร์ด · ที่อยู่ของแดชบอร์ด ข้อความปุ่ม และหมายเหตุแก้ที่ 1.1) แดชบอร์ด
 * ไม่มีตัวเลขผล Backtest ในหน้านี้ (อยู่ที่หน้า /backtest/ เท่านั้น)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	the_post();
}

$fenix_title = get_the_title();
$fenix_ready = function_exists( 'fenix_dashboard_page_hero' );

if ( $fenix_ready ) {
	fenix_dashboard_page_hero( $fenix_title );
} else {
	fenix_page_hero( '', $fenix_title );
}
?>

<main id="main" class="dash-page">

<?php
if ( $fenix_ready ) {
	fenix_dashboard_page_ways();
	fenix_dashboard_page_shots();
	fenix_dashboard_page_feats();
	fenix_dashboard_page_steps();
}

if ( function_exists( 'fenix_pages_enable_table_cards' ) ) {
	fenix_pages_enable_table_cards();
}
fenix_page_longform( 'section' );

if ( $fenix_ready ) {
	fenix_line_cta( fenix_dashboard_text( 'dash_cta_title' ), fenix_dashboard_text( 'dash_cta_text' ) );
} else {
	fenix_line_cta();
}
?>

</main>

<?php
get_footer();

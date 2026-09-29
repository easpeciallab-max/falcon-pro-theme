<?php
/**
 * Template Name: FALCON · หน้า How to Install
 *
 * hero → เกริ่นนำ + ภาพรวมขั้นตอน + สิ่งที่ต้องเตรียม → 6 ขั้นตอนติดตั้ง (ข้อความ | ภาพ) → เช็กว่า EA พร้อมทำงาน
 * → เนื้อหายาวจากหน้าแก้ไขเพจ (สารบัญ + ตารางแก้ปัญหา + FAQ) → คู่มือที่ควรอ่านต่อ → ติดต่อทีมงาน
 * ข้อความและช่องรูปของแต่ละขั้นแก้ที่ ปรับแต่ง → 22) หน้า How to Install
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
$fenix_title  = get_the_title();
$fenix_kicker = fenix_mod( 'inst_kicker' );
$fenix_sub    = fenix_mod( 'install_sub' );
fenix_page_hero( $fenix_kicker, $fenix_title ? $fenix_title : $fenix_kicker, fenix_is_placeholder( $fenix_sub ) ? '' : $fenix_sub );
?>

<main id="main" class="guide-page gd-page gd-page--install">

<?php
fenix_guide_top(
	array(
		'intro'       => fenix_mod( 'install_intro' ),
		'quick_title' => fenix_mod( 'inst_quick_title' ),
		'quick'       => fenix_mod( 'inst_quick' ),
		'prep_title'  => fenix_mod( 'inst_req_title' ),
		'prep'        => fenix_mod( 'install_req' ),
		'prep_note'   => fenix_mod( 'inst_req_note' ),
	)
);

fenix_guide_steps( 'inst_step', '', fenix_mod( 'install_note' ) );

fenix_guide_check( fenix_mod( 'inst_check_title' ), fenix_mod( 'inst_check' ), fenix_mod( 'inst_check_note' ) );

fenix_guide_longform();

fenix_guide_related( 'how-to-install' );

fenix_line_cta( fenix_mod( 'inst_cta_title' ), fenix_mod( 'inst_cta_text' ) );
?>

</main>

<?php
get_footer();

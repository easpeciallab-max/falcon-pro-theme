<?php
/**
 * Template Name: FALCON · หน้า Forward Test
 *
 * โครง: หัวเพจ → สรุปผล/สถานะ + ลิงก์ผลที่ตรวจสอบได้ + ภาพประกอบวิธีอ่านผล + หมายเหตุ + คำเตือน (พื้นเข้ม)
 *       → เนื้อหายาวจาก editor (พื้นขาว อ่านง่าย) → บล็อกติดต่อ
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
$fenix_sub   = fenix_mod( 'forward_sub' );
fenix_page_hero( fenix_mod( 'forward_kicker' ), $fenix_title ? $fenix_title : fenix_mod( 'forward_kicker' ), fenix_pages_has( $fenix_sub ) ? $fenix_sub : '' );
?>

<main id="main" class="tests-page tests-page--forward">

	<?php fenix_pages_tests_top( 'forward' ); ?>

	<?php
	fenix_pages_enable_table_cards();
	fenix_page_longform( 'section' );
	?>

	<?php fenix_line_cta( fenix_mod( 'forward_cta_title' ), fenix_mod( 'forward_cta_text' ) ); ?>

</main>

<?php
get_footer();

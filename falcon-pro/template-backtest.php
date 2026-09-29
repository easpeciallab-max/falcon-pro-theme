<?php
/**
 * Template Name: FALCON · หน้า Backtest
 *
 * โครง: หัวเพจ → สรุปผล/สถานะ + ภาพประกอบวิธีทดสอบ + หมายเหตุ + คำเตือน (พื้นเข้ม)
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
$fenix_sub   = fenix_mod( 'backtest_sub' );
fenix_page_hero( fenix_mod( 'backtest_kicker' ), $fenix_title ? $fenix_title : fenix_mod( 'backtest_kicker' ), fenix_pages_has( $fenix_sub ) ? $fenix_sub : '' );
?>

<main id="main" class="tests-page tests-page--backtest">

	<?php fenix_pages_tests_top( 'backtest' ); ?>

	<?php
	fenix_pages_enable_table_cards();
	fenix_page_longform( 'section' );
	?>

	<?php fenix_line_cta( fenix_mod( 'backtest_cta_title' ), fenix_mod( 'backtest_cta_text' ) ); ?>

</main>

<?php
get_footer();

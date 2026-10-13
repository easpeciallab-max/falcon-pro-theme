<?php
/**
 * Template Name: FALCON · หน้า Backtest
 *
 * โครง: หัวเพจ → สรุปผล/สถานะ + ภาพประกอบวิธีทดสอบ + หมายเหตุ + คำเตือน (พื้นเข้ม)
 *       → เนื้อหายาวจาก editor (พื้นขาว อ่านง่าย) → บล็อกติดต่อ
 * หน้านี้เป็นหน้าเดียวของกลุ่มการทดสอบ: ไม่มีลิงก์ แท็บ หรือการ์ดไปหน้า Forward Test (ถอดออกจากเว็บ 13 ต.ค. 2026 · URL เดิมส่งต่อมาที่หน้านี้)
 * และไม่สัญญาว่าจะมีผลจากตลาดจริงมาแสดง · ลูกค้าไม่ได้รับไฟล์ EA จึงไม่ชวนให้รันการทดสอบเอง
 * ตัวเลขผล Backtest ของแต่ละชุด EA อยู่ในตารางของเนื้อหาเพจเท่านั้น (หัวข้อ id="backtest-results" ใน inc/content/pages/backtest.html)
 * คู่กับเงื่อนไขการทดสอบและข้อจำกัดทุกข้อ · ส่วนบนของหน้าตามเนื้อหาเพจ (fenix_pages_stats ใน inc/modules/pages.php):
 *   - เนื้อหามีหัวข้อผล = การ์ด "ผลอยู่ในตารางด้านล่าง" + ปุ่มเลื่อนไปที่ตาราง (fenix_pages_backtest_live_card · ไม่มีตัวเลข ไม่มีช่องสถิติ)
 *   - เนื้อหายังไม่มีหัวข้อผล = การ์ดสถานะ + รายการหัวข้อสถิติที่จะรายงาน (มีแต่ชื่อหัวข้อ ไม่มีค่า · fenix_pages_pending)
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

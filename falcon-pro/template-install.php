<?php
/**
 * Template Name: FALCON · หน้า How to Install
 *
 * เทมเพลตนี้ไม่มีเพจใดของเว็บใช้แล้ว: หน้า /how-to-install/ ถูกถอดออกเมื่อ 13 ต.ค. 2026 (FALCON ใช้งานผ่านแดชบอร์ดทางเดียว
 * ไม่มีไฟล์ EA ให้ลูกค้าติดตั้งเอง) และ URL เดิมถูกส่งต่อไป /dashboard/ (fenix_retired_redirect ใน inc/setup.php)
 * โค้ดคงไว้ให้เพจเก่าที่ยังค้างใน WordPress ไม่พัง · ห้ามนำข้อความชุด install_* / inst_* ไปพิมพ์ในหน้าที่ยังอยู่
 *
 * hero (ชื่อหน้า + ภาพรวมขั้นตอนแบบย่อ) → เกริ่นนำ + สิ่งที่ต้องเตรียม
 * → 6 ขั้นตอนติดตั้ง (ข้อความ | ภาพ) → เช็กว่า EA พร้อมทำงาน → บล็อกแดชบอร์ด (fenix_dashboard_block ถ้าเปิดใช้)
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
/* ตัวแทน {broker} {brand} ในคำโปรย → ค่าจริง (แทนค่าก่อน แล้ว hero เป็นผู้ escape) · ภาพรวมขั้นตอนแบบย่ออยู่ใน hero */
fenix_guide_hero(
	$fenix_kicker,
	$fenix_title ? $fenix_title : $fenix_kicker,
	fenix_is_placeholder( $fenix_sub ) ? '' : fenix_guide_tokens( $fenix_sub ),
	fenix_mod( 'inst_quick_title' ),
	fenix_mod( 'inst_quick' )
);
?>

<main id="main" class="guide-page gd-page gd-page--install">

<?php
/* ภาพรวมขั้นตอน (quick) อยู่ใน hero แล้ว จึงไม่ส่งให้ส่วนบนพิมพ์ซ้ำ */
fenix_guide_top(
	array(
		'intro'      => fenix_mod( 'install_intro' ),
		'prep_title' => fenix_mod( 'inst_req_title' ),
		'prep'       => fenix_mod( 'install_req' ),
		'prep_note'  => fenix_mod( 'inst_req_note' ),
	)
);

fenix_guide_steps( 'inst_step', '', fenix_mod( 'install_note' ) );

fenix_guide_check( fenix_mod( 'inst_check_title' ), fenix_mod( 'inst_check' ), fenix_mod( 'inst_check_note' ) );

/* บล็อกแดชบอร์ด (ข้อความกลางของบล็อก: แดชบอร์ดคือทางเดียวที่ใช้ FALCON) · ข้อความและการเปิดปิดอยู่ที่ ปรับแต่ง → 1.1) แดชบอร์ด
 * ปิดฟีเจอร์หรือไม่มีโมดูล = ไม่พิมพ์อะไร */
if ( function_exists( 'fenix_dashboard_block' ) ) {
	fenix_dashboard_block( 'page', array( 'alt' => true ) );
}

fenix_guide_longform();

fenix_guide_related( 'how-to-install' );

fenix_line_cta( fenix_guide_tokens( fenix_mod( 'inst_cta_title' ) ), fenix_guide_tokens( fenix_mod( 'inst_cta_text' ) ) );
?>

</main>

<?php
get_footer();

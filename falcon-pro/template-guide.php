<?php
/**
 * Template Name: FALCON · หน้าคู่มือ / เนื้อหายาว
 *
 * ใช้กับ: เปิดบัญชี MT5, ติดตั้งและล็อกอิน MT5, VPS (Windows / Android / iOS) และหน้าเครื่องมือคำนวณ
 * - หน้าคู่มือ (slug อยู่ใน fenix_guide_map() ของ inc/modules/guides.php):
 *   hero → เกริ่นนำ + ภาพรวมขั้นตอน + สิ่งที่ต้องเตรียม → ขั้นตอน (ข้อความ | ภาพ) + การ์ดดาวน์โหลดแอป
 *   → เช็กลิสต์ → เนื้อหายาวจากหน้าแก้ไขเพจ (สารบัญ + ตาราง + FAQ) → คู่มือที่ควรอ่านต่อ → ติดต่อทีมงาน
 *   ขั้นตอน / เช็กลิสต์ / การ์ดดาวน์โหลด / ข้อความติดต่อ แก้ที่ ปรับแต่ง → "คู่มือ · …"
 *   คำอธิบายยาว ตารางแก้ปัญหา และ FAQ แก้ในหน้าแก้ไขเพจ
 * - เพจอื่นที่เลือกเทมเพลตนี้ (เช่น /tools/): เนื้อหาเพจ + สารบัญ → คู่มือที่ควรอ่านต่อ → ติดต่อทีมงาน (ไม่มีหน้าว่าง)
 * - หน้าเอกสาร (about / privacy / terms / data-deletion) ใช้ page.php
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

$fenix_slug  = (string) get_post_field( 'post_name', get_the_ID() );
$fenix_pages = fenix_site_pages();
$fenix_group = isset( $fenix_pages[ $fenix_slug ]['group'] ) ? $fenix_pages[ $fenix_slug ]['group'] : '';
$fenix_p     = function_exists( 'fenix_guide_prefix' ) ? fenix_guide_prefix() : '';

if ( $fenix_p ) {
	$fenix_kicker = fenix_mod( $fenix_p . '_kicker' );
	$fenix_sub    = fenix_mod( $fenix_p . '_sub' );
} else {
	$fenix_kicker = fenix_page_kicker( $fenix_slug );
	$fenix_sub    = has_excerpt() ? get_the_excerpt() : (string) get_post_meta( get_the_ID(), 'fenix_meta_description', true );
}

fenix_page_hero( $fenix_kicker, get_the_title(), fenix_is_placeholder( $fenix_sub ) ? '' : $fenix_sub );
?>

<main id="main" class="guide-page gd-page<?php echo esc_attr( $fenix_p ? ' gd-page--' . $fenix_p : ' gd-page--' . sanitize_key( $fenix_slug ) ); ?>">

<?php if ( $fenix_p ) : ?>

	<?php
	fenix_guide_top(
		array(
			'intro'       => fenix_mod( $fenix_p . '_intro' ),
			'quick_title' => fenix_mod( $fenix_p . '_quick_title' ),
			'quick'       => fenix_mod( $fenix_p . '_quick' ),
			'prep_title'  => fenix_mod( $fenix_p . '_prep_title' ),
			'prep'        => fenix_mod( $fenix_p . '_prep' ),
			'prep_note'   => fenix_mod( $fenix_p . '_prep_note' ),
		)
	);

	fenix_guide_steps( $fenix_p . '_step', in_array( $fenix_p, fenix_guide_dl_prefixes(), true ) ? $fenix_p : '' );

	fenix_guide_check( fenix_mod( $fenix_p . '_check_title' ), fenix_mod( $fenix_p . '_check' ), fenix_mod( $fenix_p . '_check_note' ) );

	fenix_guide_longform();

	fenix_guide_related( $fenix_slug );

	fenix_line_cta( fenix_mod( $fenix_p . '_cta_title' ), fenix_mod( $fenix_p . '_cta_text' ) );
	?>

<?php else : ?>

	<?php
	if ( function_exists( 'fenix_guide_longform' ) ) {
		fenix_guide_longform();
	} else {
		fenix_page_longform( 'section section--flush-top' );
	}

	if ( 'guide' === $fenix_group && function_exists( 'fenix_guide_related' ) ) {
		fenix_guide_related( $fenix_slug );
	}

	if ( 'tools' === $fenix_slug ) {
		fenix_line_cta( fenix_mod( 'gtools_cta_title' ), fenix_mod( 'gtools_cta_text' ) );
	} else {
		fenix_line_cta();
	}
	?>

<?php endif; ?>

</main>

<?php
get_footer();

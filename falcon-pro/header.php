<?php
/**
 * Header · แถบหัวเว็บโทนเข้ม
 * - html.no-js → .js ด้วยสคริปต์บรรทัดเดียว (ใช้กับ .reveal / .watch / นาฬิกาใน footer)
 * - viewport-fit=cover ให้ env(safe-area-inset-bottom) ของบาร์ล่างมือถือทำงานบน iPhone
 * - เมนูสำรอง/ต่อท้าย "ติดต่อ"/แถวโซเชียลในลิ้นชัก/ปุ่มติดต่อบนแถบหัว อยู่ใน inc/modules/chrome.php
 * - แถบหัว: ปุ่มสมาชิกแดชบอร์ด (fenix_chrome_header_member: เข้าสู่ระบบทุกจอ · สมัครสมาชิกเฉพาะจอคอม) แล้วตามด้วยปุ่มติดต่อ (LINE)
 *   เจ้าของสั่งเพิ่มปุ่มเข้าสู่ระบบ 15 ต.ค. 2026 หลังสมาชิกหาไม่เจอ · หน้าอธิบาย /dashboard/ อยู่ในเมนู และลิงก์เข้าสู่ระบบยังอยู่ท้ายเว็บด้วย
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<script>document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, 'js' );</script>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $fenix_skip = trim( (string) fenix_mod( 'chrome_skip_text' ) ); ?>
<?php if ( '' !== $fenix_skip ) : ?>
<a class="skip-link" href="#main"><?php echo esc_html( $fenix_skip ); ?></a>
<?php endif; ?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
<header class="site-header" id="top">
	<div class="container header-inner">

		<a class="brand brand--wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' · ' . fenix_mod( 'nav_home_label' ) ); ?>">
			<img class="brand-wordmark" src="<?php echo esc_url( fenix_wordmark_url( fenix_is_dark_mode() ? 'light' : 'dark' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="132" height="64">
		</a>

		<nav class="site-nav" id="site-nav" aria-label="เมนูหลัก">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'fallback_cb'    => function_exists( 'fenix_chrome_fallback_menu' ) ? 'fenix_chrome_fallback_menu' : 'fenix_fallback_menu',
					'depth'          => 2,
				)
			);
			if ( function_exists( 'fenix_chrome_social_row' ) ) {
				fenix_chrome_social_row( 'nav-social' );
			}
			?>
		</nav>

		<?php
		/* ปุ่มสมาชิกแดชบอร์ด (เข้าสู่ระบบ · สมัครสมาชิกเฉพาะจอคอม) แล้วปุ่มติดต่อ (LINE → หน้า /go/ → ไม่แสดง)
		 * จอมือถือที่มีบาร์ล่างซ่อนปุ่มติดต่อด้วย CSS เพราะบาร์ล่างมีปุ่มติดต่ออยู่แล้ว */
		if ( function_exists( 'fenix_chrome_header_member' ) ) {
			fenix_chrome_header_member();
		}
		if ( function_exists( 'fenix_chrome_header_cta' ) ) {
			fenix_chrome_header_cta();
		}
		if ( function_exists( 'fenix_chrome_language_switcher' ) ) {
			fenix_chrome_language_switcher();
		}
		?>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="เปิด/ปิดเมนู">
			<span></span><span></span><span></span>
		</button>

	</div>
</header>
<?php endif; ?>

<?php
/**
 * 404 · ไม่พบหน้า
 *
 * ปุ่มติดต่อใช้ fenix_contact_target(): LINE → หน้า /go/ → ไม่แสดง (ไม่มีลิงก์ '#')
 * ลิงก์ลัดท้ายการ์ดเป็นรายการ (nav > ul.error-links > li) · ล้างสไตล์รายการที่ assets/css/chrome.css หัวข้อ 8
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$fenix_target = fenix_contact_target();
?>

<main id="main">
	<?php
	/* พื้นขาวเสมอ ตามกฎท้าย style.css (body.theme-dark .error-404 = พื้นขาว) · ไม่ใส่คลาส .is-dark:
	   คลาสนั้นทำให้หัวข้อ ลิงก์ลัด และช่องค้นหาเป็นสีสำหรับพื้นเข้ม ซึ่งมองไม่เห็นบนพื้นขาว (หัวข้อ H1 เคยเป็นขาวบนขาว) */
	?>
	<section class="error-404">
		<div class="container container-narrow">
			<div class="error-404-inner">

				<p class="error-code" aria-hidden="true">404</p>
				<h1 class="error-title"><?php echo fenix_text( fenix_mod( 'err404_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h1>
				<p class="error-text"><?php echo esc_html( fenix_mod( 'err404_text' ) ); ?></p>

				<?php fenix_pages_search_form( fenix_mod( 'err404_search_placeholder' ) ); ?>

				<div class="error-actions">
					<a class="btn btn-fire" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo fenix_icon( 'home', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( fenix_mod( 'err404_home_label' ) ); ?></span>
					</a>
					<?php
					fenix_contact_button(
						array(
							'text'  => fenix_mod( 'err404_contact_text' ),
							'class' => $fenix_target['is_line'] ? 'btn btn-line' : 'btn btn-ghost',
							'pos'   => '404',
							'arrow' => false,
						)
					);
					?>
				</div>

				<?php
				/* ลิงก์ลัด: เฉพาะคู่ที่มีทั้งชื่อและปลายทาง · พิมพ์เป็นรายการ (ul > li) ให้โปรแกรมอ่านหน้าจอบอกจำนวนลิงก์ได้ */
				$fenix_links = array();
				foreach ( fenix_pages_pairs( fenix_mod( 'err404_links' ) ) as $fenix_link ) {
					if ( '' !== trim( (string) $fenix_link[0] ) && '' !== trim( (string) $fenix_link[1] ) ) {
						$fenix_links[] = $fenix_link;
					}
				}
				?>
				<?php if ( $fenix_links ) : ?>
					<nav class="error-links-nav" aria-label="<?php echo esc_attr( fenix_mod( 'err404_title' ) ); ?>">
						<ul class="error-links">
							<?php foreach ( $fenix_links as $fenix_link ) : ?>
								<li><a href="<?php echo esc_url( fenix_link_url( $fenix_link[1] ) ); ?>"><?php echo esc_html( $fenix_link[0] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

			</div>
		</div>
	</section>
</main>

<?php
get_footer();

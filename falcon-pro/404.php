<?php
/**
 * 404 · ไม่พบหน้า
 *
 * @package falcon-pro
 */

get_header();

$fenix_line = fenix_mod( 'line_url' );
?>

<main id="main">
	<section class="error-404">
		<div class="container container-narrow">
			<div class="error-404-inner">

				<p class="error-code">404</p>
				<h1 class="error-title">ไม่พบหน้าที่คุณค้นหา</h1>
				<p class="error-text">หน้านี้อาจถูกย้าย ลบ หรือพิมพ์ลิงก์ผิด ลองค้นหา หรือกลับไปหน้าหลักได้เลย</p>

				<form class="error-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input type="search" name="s" placeholder="ค้นหาบทความ…" aria-label="ค้นหา" spellcheck="false" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
					<button type="submit" aria-label="ค้นหา"><?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</form>

				<div class="error-actions">
					<a class="btn btn-fire" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo fenix_icon( 'home', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						กลับหน้าแรก
					</a>
					<a class="btn btn-line" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
						<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						ทัก LINE
					</a>
				</div>

				<nav class="error-links" aria-label="ลิงก์ด่วน">
					<a href="<?php echo esc_url( home_url( '/forward-test/' ) ); ?>">ผลทดสอบ</a>
					<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">แพ็กเกจ</a>
					<a href="<?php echo esc_url( home_url( '/how-to-install/' ) ); ?>">วิธีติดตั้ง</a>
					<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">คำเตือนความเสี่ยง</a>
				</nav>

			</div>
		</div>
	</section>
</main>

<?php
get_footer();

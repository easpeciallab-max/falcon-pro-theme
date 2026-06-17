<?php
/**
 * Template Name: FALCON · หน้า Forward Test
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$fenix_title = get_the_title();
fenix_page_hero( 'Forward Test', $fenix_title ? $fenix_title : 'ผล Forward Test', fenix_mod( 'forward_sub' ) );
?>

<main id="main">

<section class="section">
	<div class="container">

		<?php if ( fenix_mod( 'forward_intro' ) ) : ?>
			<p class="lead reveal"><?php echo esc_html( fenix_mod( 'forward_intro' ) ); ?></p>
		<?php endif; ?>

		<div class="stats-grid reveal">
			<?php
			for ( $i = 1; $i <= 6; $i++ ) :
				$s_label = fenix_mod( 'fw_stat' . $i . '_label' );
				$s_value = fenix_mod( 'fw_stat' . $i . '_value' );
				if ( ! $s_label && ! $s_value ) {
					continue;
				}
				?>
				<div class="stat">
					<span class="stat-label"><?php echo esc_html( $s_label ); ?></span>
					<span class="stat-value"><?php echo esc_html( $s_value ); ?></span>
				</div>
			<?php endfor; ?>
		</div>

		<?php if ( fenix_mod( 'forward_img' ) ) : ?>
			<figure class="perf-figure reveal">
				<img src="<?php echo esc_url( fenix_mod( 'forward_img' ) ); ?>" alt="<?php echo esc_attr( fenix_mod( 'forward_img_caption' ) ); ?>" loading="lazy">
				<?php if ( fenix_mod( 'forward_img_caption' ) ) : ?>
					<figcaption><?php echo esc_html( fenix_mod( 'forward_img_caption' ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php else : ?>
			<figure class="perf-figure shot-placeholder reveal">
				<div class="shot-empty">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.png' ); ?>" alt="" loading="lazy" width="110" height="110">
					<span class="chip">ภาพประกอบ</span>
					<p>อัปโหลดภาพผล Forward Test ได้ที่ ปรับแต่ง → หน้า Forward Test</p>
				</div>
			</figure>
		<?php endif; ?>

		<?php if ( fenix_mod( 'forward_link_url' ) && fenix_mod( 'forward_link_label' ) ) : ?>
			<p class="reveal" style="text-align:center;">
				<a class="btn btn-ghost" href="<?php echo esc_url( fenix_mod( 'forward_link_url' ) ); ?>" target="_blank" rel="noopener">
					<?php echo esc_html( fenix_mod( 'forward_link_label' ) ); ?>
					<?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</p>
		<?php endif; ?>

		<?php if ( fenix_mod( 'forward_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'forward_note' ) ); ?></p>
		<?php endif; ?>

		<div class="disclaimer reveal">
			<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo esc_html( fenix_mod( 'forward_disclaimer' ) ); ?></p>
		</div>

	</div>
</section>

<?php fenix_line_cta( 'ติดตามผลแบบเรียลไทม์?', 'สอบถามผลการทดสอบล่าสุดและช่องทางติดตามได้ทาง LINE' ); ?>

</main>

<?php
get_footer();

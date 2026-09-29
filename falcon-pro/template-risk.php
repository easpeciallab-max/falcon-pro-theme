<?php
/**
 * Template Name: FALCON · หน้า Risk Disclosure
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$fenix_title = get_the_title();
fenix_page_hero( 'Risk Disclosure', $fenix_title ? $fenix_title : 'คำเตือนความเสี่ยง', fenix_mod( 'riskpage_sub' ) );
?>

<main id="main">

<section class="section">
	<div class="container container-narrow">

		<div class="riskdoc-intro reveal">
			<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo esc_html( fenix_mod( 'riskpage_intro' ) ); ?></p>
		</div>

		<?php if ( fenix_mod( 'riskpage_image' ) ) : ?>
			<figure class="riskdoc-figure reveal">
				<img src="<?php echo esc_url( fenix_mod( 'riskpage_image' ) ); ?>" alt="<?php echo esc_attr( fenix_mod( 'riskpage_image_caption' ) ); ?>" loading="lazy">
				<?php if ( fenix_mod( 'riskpage_image_caption' ) ) : ?>
					<figcaption><?php echo esc_html( fenix_mod( 'riskpage_image_caption' ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="riskdoc">
			<?php
			$fenix_n = 0;
			for ( $i = 1; $i <= 6; $i++ ) :
				$b_title = fenix_mod( 'rp_block' . $i . '_title' );
				$b_text  = fenix_mod( 'rp_block' . $i . '_text' );
				if ( ! $b_title && ! $b_text ) {
					continue;
				}
				$fenix_n++;
				?>
				<article class="riskdoc-block reveal">
					<h2><span class="riskdoc-num"><?php echo esc_html( str_pad( (string) $fenix_n, 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $b_title ); ?></h2>
					<p><?php echo nl2br( esc_html( $b_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				</article>
			<?php endfor; ?>
		</div>

		<?php if ( fenix_mod( 'riskpage_updated' ) ) : ?>
			<p class="riskdoc-updated reveal"><?php echo esc_html( fenix_mod( 'riskpage_updated' ) ); ?></p>
		<?php endif; ?>

	</div>
</section>

<?php fenix_page_longform( 'section section-alt' ); ?>

<?php fenix_line_cta( 'มีคำถามเรื่องความเสี่ยง?', 'ทักมาสอบถามทีมงานก่อนตัดสินใจใช้งานได้ทาง LINE' ); ?>

</main>

<?php
get_footer();

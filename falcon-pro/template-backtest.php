<?php
/**
 * Template Name: FALCON · หน้า Backtest
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$fenix_title = get_the_title();
fenix_page_hero( 'Backtest', $fenix_title ? $fenix_title : 'ผล Backtest', fenix_mod( 'backtest_sub' ) );
?>

<main id="main">

<section class="section">
	<div class="container">

		<?php if ( fenix_mod( 'backtest_intro' ) ) : ?>
			<p class="lead reveal"><?php echo esc_html( fenix_mod( 'backtest_intro' ) ); ?></p>
		<?php endif; ?>

		<?php
		$fenix_real_stats = 0;
		for ( $i = 1; $i <= 8; $i++ ) {
			if ( ! fenix_is_placeholder( fenix_mod( 'bt_stat' . $i . '_value' ) ) ) {
				$fenix_real_stats++;
			}
		}
		?>
		<?php if ( ! $fenix_real_stats ) : ?>
			<?php fenix_results_pending( 'backtest' ); ?>
		<?php else : ?>
		<div class="stats-grid stats-grid--4 reveal">
			<?php
			for ( $i = 1; $i <= 8; $i++ ) :
				$s_label = fenix_mod( 'bt_stat' . $i . '_label' );
				$s_value = fenix_mod( 'bt_stat' . $i . '_value' );
				if ( ! $s_label || fenix_is_placeholder( $s_value ) ) {
					continue;
				}
				?>
				<div class="stat">
					<span class="stat-label"><?php echo esc_html( $s_label ); ?></span>
					<span class="stat-value"><?php echo esc_html( $s_value ); ?></span>
				</div>
			<?php endfor; ?>
		</div>
		<?php endif; ?>

		<?php if ( fenix_mod( 'backtest_img' ) ) : ?>
			<figure class="perf-figure reveal">
				<img src="<?php echo esc_url( fenix_mod( 'backtest_img' ) ); ?>" alt="<?php echo esc_attr( fenix_mod( 'backtest_img_caption' ) ); ?>" loading="lazy">
				<?php if ( fenix_mod( 'backtest_img_caption' ) ) : ?>
					<figcaption><?php echo esc_html( fenix_mod( 'backtest_img_caption' ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<?php if ( fenix_mod( 'backtest_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'backtest_note' ) ); ?></p>
		<?php endif; ?>

		<div class="disclaimer reveal">
			<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo esc_html( fenix_mod( 'backtest_disclaimer' ) ); ?></p>
		</div>

	</div>
</section>

<?php fenix_page_longform( 'section section-alt' ); ?>

<?php fenix_line_cta( 'อยากรู้วิธีทดสอบให้ตรงกับบัญชีของคุณ?', 'สอบถามวิธีตั้งค่า Strategy Tester และเงื่อนไขการทดสอบเพิ่มเติมได้ทาง LINE' ); ?>

</main>

<?php
get_footer();

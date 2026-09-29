<?php
/**
 * Template Name: FALCON · หน้า How to Install
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$fenix_title = get_the_title();
fenix_page_hero( 'How to Install', $fenix_title ? $fenix_title : 'วิธีติดตั้ง', fenix_mod( 'install_sub' ) );
?>

<main id="main">

<section class="section">
	<div class="container container-narrow">

		<?php if ( fenix_mod( 'install_intro' ) ) : ?>
			<p class="lead reveal"><?php echo esc_html( fenix_mod( 'install_intro' ) ); ?></p>
		<?php endif; ?>

		<?php
		$fenix_reqs = fenix_lines( fenix_mod( 'install_req' ) );
		if ( ! empty( $fenix_reqs ) ) :
			?>
			<div class="req-box reveal">
				<h3><?php echo fenix_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> สิ่งที่ต้องเตรียม</h3>
				<ul>
					<?php foreach ( $fenix_reqs as $fenix_req ) : ?>
						<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_req ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</div>

	<div class="container">
		<ol class="guide">
			<?php
			for ( $i = 1; $i <= 6; $i++ ) :
				$g_title = fenix_mod( 'inst_step' . $i . '_title' );
				$g_desc  = fenix_mod( 'inst_step' . $i . '_desc' );
				$g_img   = fenix_mod( 'inst_step' . $i . '_img' );
				if ( ! $g_title && ! $g_desc ) {
					continue;
				}
				?>
				<li class="guide-step reveal">
					<div class="guide-body">
						<span class="guide-num"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="guide-text">
							<h3><?php echo esc_html( $g_title ); ?></h3>
							<p><?php echo esc_html( $g_desc ); ?></p>
						</div>
					</div>
					<?php if ( $g_img ) : ?>
						<figure class="guide-img">
							<img src="<?php echo esc_url( $g_img ); ?>" alt="<?php echo esc_attr( $g_title ); ?>" loading="lazy">
						</figure>
					<?php endif; ?>
				</li>
			<?php endfor; ?>
		</ol>
	</div>

	<?php if ( fenix_mod( 'install_note' ) ) : ?>
		<div class="container container-narrow">
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'install_note' ) ); ?></p>
		</div>
	<?php endif; ?>
</section>

<?php fenix_page_longform( 'section section-alt' ); ?>

<?php fenix_line_cta( 'อยากให้ทีมงานช่วยติดตั้ง?', 'ทักมาทาง LINE ทีมงานช่วยติดตั้งและตั้งค่าให้จนระบบพร้อมใช้งาน' ); ?>

</main>

<?php
get_footer();

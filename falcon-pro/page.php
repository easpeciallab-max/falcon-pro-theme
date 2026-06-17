<?php
/**
 * Static page (หน้าทั่วไป เช่น นโยบายความเป็นส่วนตัว, เงื่อนไข, เกี่ยวกับเรา)
 *
 * @package falcon-pro
 */

get_header();

$fenix_is_elementor = fenix_is_elementor_page();
?>

<main id="main"<?php echo $fenix_is_elementor ? ' class="elementor-page-shell elementor-page-shell--auto"' : ''; ?>>
	<?php if ( $fenix_is_elementor ) : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'elementor-entry' ); ?>>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>

	<?php else : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<div class="page-hero">
				<div class="container">
					<nav class="breadcrumb breadcrumb-center" aria-label="เส้นทางนำทาง">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
						<?php if ( $post->post_parent ) : ?>
							<span class="breadcrumb-sep" aria-hidden="true">›</span>
							<a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>"><?php echo esc_html( get_the_title( $post->post_parent ) ); ?></a>
						<?php endif; ?>
						<span class="breadcrumb-sep" aria-hidden="true">›</span>
						<span class="breadcrumb-current"><?php the_title(); ?></span>
					</nav>
					<h1><?php the_title(); ?></h1>
				</div>
			</div>

			<div class="entry">
				<div class="container-narrow">
					<article <?php post_class(); ?>>

						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="article-thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
						<?php endif; ?>

						<div class="entry-content">
							<?php the_content(); ?>
						</div>

						<?php
						wp_link_pages(
							array(
								'before' => '<div class="page-links">หน้า: ',
								'after'  => '</div>',
							)
						);
						?>

					</article>
				</div>
			</div>

		<?php endwhile; ?>

	<?php endif; ?>
</main>

<?php
get_footer();

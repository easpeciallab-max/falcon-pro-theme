<?php
/**
 * Template Name: FALCON - Elementor Full Width
 * Template Post Type: page
 *
 * @package falcon-pro
 */

get_header();
?>

<main id="main" class="elementor-page-shell elementor-page-shell--full">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'elementor-entry' ); ?>>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();

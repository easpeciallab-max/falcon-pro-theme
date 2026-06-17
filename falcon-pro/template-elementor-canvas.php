<?php
/**
 * Template Name: FALCON - Elementor Canvas
 * Template Post Type: page
 *
 * @package falcon-pro
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'fenix-elementor-canvas' ); ?>>
<?php wp_body_open(); ?>

<main id="main" class="elementor-page-shell elementor-page-shell--canvas">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'elementor-entry' ); ?>>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
</main>

<?php wp_footer(); ?>
</body>
</html>

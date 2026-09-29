<?php
/**
 * 404 · ไม่พบหน้า
 *
 * ปุ่มติดต่อใช้ fenix_contact_target(): LINE → หน้า /go/ → ไม่แสดง (ไม่มีลิงก์ '#')
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
	<section class="error-404<?php echo esc_attr( fenix_section_tone( 'error', true ) ); ?>">
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

				<?php $fenix_links = fenix_pages_pairs( fenix_mod( 'err404_links' ) ); ?>
				<?php if ( $fenix_links ) : ?>
					<nav class="error-links" aria-label="<?php echo esc_attr( fenix_mod( 'err404_title' ) ); ?>">
						<?php foreach ( $fenix_links as $fenix_link ) : ?>
							<?php if ( '' !== $fenix_link[1] ) : ?>
								<a href="<?php echo esc_url( fenix_link_url( $fenix_link[1] ) ); ?>"><?php echo esc_html( $fenix_link[0] ); ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

			</div>
		</div>
	</section>
</main>

<?php
get_footer();

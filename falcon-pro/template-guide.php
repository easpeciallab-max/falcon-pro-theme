<?php
/**
 * Template Name: FALCON · หน้าคู่มือ / เนื้อหายาว
 *
 * ใช้กับ: เปิดบัญชี MT5, ล็อกอิน MT5, VPS (Windows/Android/iOS), เครื่องมือคำนวณ, เกี่ยวกับเรา, นโยบาย/เงื่อนไข
 * เนื้อหาแก้ได้ในหน้าแก้ไขเพจตามปกติ · ธีมสร้างสารบัญ + FAQ schema จากหัวข้อ/คำถามในเนื้อหาให้อัตโนมัติ
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$fenix_slug  = (string) get_post_field( 'post_name', get_the_ID() );
$fenix_pages = fenix_site_pages();
$fenix_group = isset( $fenix_pages[ $fenix_slug ]['group'] ) ? $fenix_pages[ $fenix_slug ]['group'] : 'guide';

fenix_page_hero(
	fenix_page_kicker( $fenix_slug ),
	get_the_title(),
	'doc' === $fenix_group ? '' : ( has_excerpt() ? get_the_excerpt() : (string) get_post_meta( get_the_ID(), 'fenix_meta_description', true ) )
);
?>

<main id="main" class="guide-page guide-page--<?php echo esc_attr( $fenix_group ); ?>">

	<?php fenix_page_longform( 'section section--flush-top' ); ?>

	<?php if ( 'guide' === $fenix_group ) : ?>
		<?php
		$fenix_related = fenix_guide_links( array( 'guide', 'test' ) );
		unset( $fenix_related[ $fenix_slug ] );
		?>
		<section class="section section-alt related-guides">
			<div class="container">
				<div class="sec-head reveal">
					<span class="kicker">Guides</span>
					<h2>คู่มืออื่นที่เกี่ยวข้อง</h2>
				</div>
				<ol class="guide-index reveal">
					<?php $fenix_n = 0; ?>
					<?php foreach ( $fenix_related as $fenix_link ) : ?>
						<?php $fenix_n++; ?>
						<li>
							<a href="<?php echo esc_url( $fenix_link['url'] ); ?>">
								<span class="mono"><?php echo esc_html( sprintf( '%02d', $fenix_n ) ); ?></span>
								<span><?php echo esc_html( $fenix_link['label'] ); ?></span>
								<?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( 'doc' !== $fenix_group || 'about' === $fenix_slug ) : ?>
		<?php fenix_line_cta(); ?>
	<?php endif; ?>

</main>

<?php
get_footer();

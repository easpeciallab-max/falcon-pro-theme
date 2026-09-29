<?php
/**
 * หน้าผลการค้นหา
 *
 * โครง: หัวเพจ (คำค้น) → แถบจำนวนผล + ค้นหาอีกครั้ง → การ์ดบทความ + โหลดเพิ่ม
 *       ไม่พบผล: ข้อความแนะนำ + รายการคู่มือพร้อมคำอธิบาย (เฉพาะเพจที่เผยแพร่แล้ว)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$fenix_found = (int) $GLOBALS['wp_query']->found_posts;
$fenix_has   = have_posts();
$fenix_query = get_search_query();

fenix_page_hero( '', fenix_mod( 'search_title' ) . ( '' !== $fenix_query ? ': “' . $fenix_query . '”' : '' ) );
?>

<main id="main" class="search-page">

	<div class="search-bar">
		<div class="container">
			<?php if ( $fenix_found ) : ?>
				<p class="archive-count"><?php echo esc_html( fenix_pages_count_text( 'search_count_text', $fenix_found ) ); ?></p>
			<?php endif; ?>
			<?php fenix_pages_search_form( fenix_mod( 'search_placeholder' ), 'error-search search-bar-form' ); ?>
		</div>
	</div>

	<div class="posts-wrap">
		<div class="container">

			<?php if ( $fenix_has ) : ?>

				<div class="posts-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						fenix_post_card();
					endwhile;
					?>
				</div>

				<?php fenix_pages_load_more(); ?>

			<?php else : ?>

				<?php $fenix_guides = fenix_pages_guide_items(); ?>
				<div class="articles-empty">
					<p class="no-posts"><?php echo fenix_icon( 'book', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( fenix_mod( 'search_empty_text' ) ); ?></span></p>
					<?php if ( $fenix_guides ) : ?>
						<h2 class="guide-more-title"><?php echo fenix_text( fenix_mod( 'articles_guides_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
						<?php fenix_pages_guide_grid( $fenix_guides ); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();

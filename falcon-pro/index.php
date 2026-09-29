<?php
/**
 * หน้ารวมบทความ (/articles/) + หน้าหมวดหมู่/ป้ายกำกับ
 *
 * โครง: หัวเพจ → บรรทัดจำนวนบทความ + หมวด → การ์ดบทความ + โหลดเพิ่ม
 *       ยังไม่มีบทความ: "กำลังเตรียมบทความ" + รายการคู่มือพร้อมคำอธิบาย (เฉพาะเพจที่เผยแพร่แล้ว)
 *       มีบทความ (หน้าแรกของรายการ): รายการคู่มือเป็น section พื้นเข้มท้ายหน้า
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$fenix_desc   = get_the_archive_description();
$fenix_found  = (int) $GLOBALS['wp_query']->found_posts;
$fenix_guides = fenix_pages_guide_items();
$fenix_has    = have_posts();
?>

<main id="main" class="articles-page">

	<?php
	if ( is_home() && ! is_front_page() ) {
		$fenix_posts_page = (int) get_option( 'page_for_posts' );
		$fenix_h1         = $fenix_posts_page ? get_the_title( $fenix_posts_page ) : fenix_mod( 'articles_title' );
	} elseif ( is_search() ) {
		$fenix_h1 = fenix_mod( 'search_title' ) . ': ' . get_search_query();
	} elseif ( is_archive() ) {
		$fenix_h1 = wp_strip_all_tags( get_the_archive_title() );
	} else {
		$fenix_h1 = fenix_mod( 'articles_title' );
	}
	$fenix_sub = $fenix_desc ? wp_strip_all_tags( $fenix_desc ) : ( is_home() ? fenix_mod( 'blog_subtitle' ) : '' );
	fenix_page_hero( fenix_mod( 'articles_kicker' ), $fenix_h1, fenix_pages_has( $fenix_sub ) ? $fenix_sub : '' );
	?>

	<?php
	$fenix_cats      = is_search() ? array() : get_categories( array( 'hide_empty' => true ) );
	$fenix_posts_url = (int) get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/articles/' );
	if ( $fenix_found || count( $fenix_cats ) > 1 ) :
		?>
		<div class="archive-bar">
			<div class="container">
				<?php if ( $fenix_found ) : ?>
					<p class="archive-count"><?php echo esc_html( fenix_pages_count_text( 'articles_count_text', $fenix_found ) ); ?></p>
				<?php endif; ?>
				<?php if ( count( $fenix_cats ) > 1 ) : ?>
					<nav class="cat-chips" aria-label="<?php echo esc_attr( fenix_mod( 'articles_kicker' ) ); ?>">
						<a class="chip-link<?php echo is_home() ? ' is-active' : ''; ?>" href="<?php echo esc_url( $fenix_posts_url ); ?>"><?php echo esc_html( fenix_mod( 'articles_all_label' ) ); ?></a>
						<?php foreach ( $fenix_cats as $fenix_cat ) : ?>
							<a class="chip-link<?php echo is_category( $fenix_cat->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $fenix_cat ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

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

				<div class="articles-empty">
					<p class="no-posts"><?php echo fenix_icon( 'book', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( fenix_mod( is_search() ? 'search_empty_text' : 'articles_empty_text' ) ); ?></span></p>
					<?php if ( $fenix_guides ) : ?>
						<h2 class="guide-more-title"><?php echo fenix_text( fenix_mod( 'articles_guides_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
						<?php fenix_pages_guide_grid( $fenix_guides ); ?>
					<?php endif; ?>
				</div>

			<?php endif; ?>

		</div>
	</div>

	<?php if ( is_home() && ! is_paged() && $fenix_has && $fenix_guides ) : ?>
		<section class="section guide-hub<?php echo esc_attr( fenix_pages_tone( 'guides', true, true ) ); ?>">
			<div class="container">
				<?php fenix_pages_sec_head( fenix_mod( 'articles_guides_kicker' ), fenix_mod( 'articles_guides_title' ), fenix_mod( 'articles_guides_sub' ) ); ?>
				<?php fenix_pages_guide_grid( $fenix_guides ); ?>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php
get_footer();

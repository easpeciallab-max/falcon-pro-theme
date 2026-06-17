<?php
/**
 * Search results (หน้าผลการค้นหา)
 *
 * @package falcon-pro
 */

get_header();

$fenix_found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main id="main">

	<div class="page-hero archive-hero">
		<div class="container">

			<nav class="breadcrumb breadcrumb-center" aria-label="เส้นทางนำทาง">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
				<span class="breadcrumb-sep" aria-hidden="true">›</span>
				<span class="breadcrumb-current">ค้นหา</span>
			</nav>

			<h1>ผลการค้นหา: “<?php echo esc_html( get_search_query() ); ?>”</h1>

			<?php if ( $fenix_found ) : ?>
				<p class="archive-count"><?php echo esc_html( 'พบ ' . number_format_i18n( $fenix_found ) . ' ผลลัพธ์' ); ?></p>
			<?php endif; ?>

			<form class="error-search search-hero-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" placeholder="ค้นหาอีกครั้ง…" aria-label="ค้นหา" spellcheck="false" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit" aria-label="ค้นหา"><?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</form>

		</div>
	</div>

	<div class="posts-wrap">
		<div class="container">

			<?php if ( have_posts() ) : ?>

				<div class="posts-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						fenix_post_card();
					endwhile;
					?>
				</div>

				<?php if ( $GLOBALS['wp_query']->max_num_pages > 1 ) : ?>
					<div class="load-more">
						<button type="button" class="btn btn-ghost load-more-btn"
							data-page="<?php echo esc_attr( (string) max( 1, (int) get_query_var( 'paged' ) ) ); ?>"
							data-max="<?php echo esc_attr( (string) (int) $GLOBALS['wp_query']->max_num_pages ); ?>"
							data-query="<?php echo esc_attr( wp_json_encode( $GLOBALS['wp_query']->query ) ); ?>">
							โหลดเพิ่ม
						</button>
					</div>
					<noscript>
						<div class="pagination">
							<?php
							echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
								array(
									'prev_text' => '&larr; ก่อนหน้า',
									'next_text' => 'ถัดไป &rarr;',
								)
							);
							?>
						</div>
					</noscript>
				<?php endif; ?>

			<?php else : ?>

				<p class="no-posts">ไม่พบบทความที่ตรงกับ “<?php echo esc_html( get_search_query() ); ?>” ลองค้นด้วยคำอื่นดูครับ</p>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();

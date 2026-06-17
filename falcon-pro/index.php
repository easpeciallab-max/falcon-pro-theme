<?php
/**
 * Blog index / archive (รายการบทความ + หน้าหมวดหมู่/ป้ายกำกับ/ค้นหา)
 *
 * @package falcon-pro
 */

get_header();

$fenix_desc  = get_the_archive_description();
$fenix_found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main id="main">

	<div class="page-hero archive-hero">
		<div class="container">

			<nav class="breadcrumb breadcrumb-center" aria-label="เส้นทางนำทาง">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
				<span class="breadcrumb-sep" aria-hidden="true">›</span>
				<span class="breadcrumb-current">
					<?php
					if ( is_search() ) {
						echo 'ค้นหา';
					} elseif ( is_archive() ) {
						the_archive_title();
					} else {
						echo 'บทความ';
					}
					?>
				</span>
			</nav>

			<h1>
				<?php
				if ( is_home() && ! is_front_page() ) {
					$fenix_posts_page = (int) get_option( 'page_for_posts' );
					echo $fenix_posts_page ? esc_html( get_the_title( $fenix_posts_page ) ) : 'บทความ';
				} elseif ( is_search() ) {
					echo 'ผลการค้นหา: ' . esc_html( get_search_query() );
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					echo 'บทความ';
				}
				?>
			</h1>

			<?php if ( $fenix_desc ) : ?>
				<div class="archive-desc"><?php echo wp_kses_post( $fenix_desc ); ?></div>
			<?php elseif ( is_home() ) : ?>
				<p class="muted">ความรู้เรื่อง EA, MT5 และการบริหารความเสี่ยง</p>
			<?php endif; ?>

			<?php if ( $fenix_found ) : ?>
				<p class="archive-count"><?php echo esc_html( number_format_i18n( $fenix_found ) . ' บทความ' ); ?></p>
			<?php endif; ?>

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

				<p class="no-posts">
					<?php echo is_search() ? 'ไม่พบบทความที่ตรงกับคำค้นหา' : 'ยังไม่มีบทความในขณะนี้'; ?>
				</p>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();

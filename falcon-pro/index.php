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

	<?php
	if ( is_home() && ! is_front_page() ) {
		$fenix_posts_page = (int) get_option( 'page_for_posts' );
		$fenix_h1         = $fenix_posts_page ? get_the_title( $fenix_posts_page ) : 'บทความ';
	} elseif ( is_search() ) {
		$fenix_h1 = 'ผลการค้นหา: ' . get_search_query();
	} elseif ( is_archive() ) {
		$fenix_h1 = wp_strip_all_tags( get_the_archive_title() );
	} else {
		$fenix_h1 = 'บทความ';
	}
	$fenix_sub = $fenix_desc ? wp_strip_all_tags( $fenix_desc ) : ( is_home() ? fenix_mod( 'blog_subtitle' ) : '' );
	fenix_page_hero( 'Articles', $fenix_h1, $fenix_sub );
	?>

	<?php
	$fenix_cats = get_categories( array( 'hide_empty' => true ) );
	if ( ! is_search() && count( $fenix_cats ) > 1 ) :
		$fenix_posts_url = (int) get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
		?>
		<nav class="cat-chips" aria-label="หมวดหมู่บทความ">
			<div class="container">
				<a class="chip-link<?php echo is_home() ? ' is-active' : ''; ?>" href="<?php echo esc_url( $fenix_posts_url ); ?>">ทั้งหมด<?php echo $fenix_found && is_home() ? ' · ' . esc_html( number_format_i18n( $fenix_found ) ) : ''; ?></a>
				<?php foreach ( $fenix_cats as $fenix_cat ) : ?>
					<a class="chip-link<?php echo is_category( $fenix_cat->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $fenix_cat ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>
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


	<?php if ( is_home() && ! is_paged() ) : ?>
		<section class="section section-alt guide-hub">
			<div class="container">
				<div class="sec-head reveal">
					<span class="kicker">Guides</span>
					<h2>คู่มือ EA และ MetaTrader 5 ภาษาไทย</h2>
					<p>คู่มือแบบทีละขั้นตั้งแต่เปิดบัญชี ติดตั้ง ทดสอบ จนถึงรันบน VPS</p>
				</div>
				<ol class="guide-index reveal">
					<?php $fenix_n = 0; ?>
					<?php foreach ( array_merge( fenix_guide_links( array( 'guide', 'test' ) ), array( 'risk-disclosure' => array( 'label' => 'ประกาศความเสี่ยงของการใช้ EA', 'url' => home_url( '/risk-disclosure/' ) ) ) ) as $fenix_link ) : ?>
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
</main>

<?php
get_footer();

<?php
/**
 * เพจทั่วไป / เพจเอกสาร (เกี่ยวกับเรา, นโยบายความเป็นส่วนตัว, เงื่อนไขการใช้บริการ, คำขอลบข้อมูล)
 *
 * breadcrumb + H1 (ไม่มีป้ายเล็ก) → คอลัมน์อ่านแคบคอลัมน์เดียว → บรรทัดวันที่ปรับปรุงล่าสุด
 * ไม่มีบล็อกติดต่อในหน้า ยกเว้นเพจ about · เพจที่สร้างด้วย Elementor แสดงผลของ Elementor ตามเดิม
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$fenix_is_elementor = fenix_is_elementor_page();
?>

<main id="main"<?php echo $fenix_is_elementor ? ' class="elementor-page-shell elementor-page-shell--auto"' : ' class="doc-page"'; ?>>
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

			$fenix_slug = (string) get_post_field( 'post_name', get_the_ID() );

			fenix_page_hero( '', wp_strip_all_tags( get_the_title() ) );

			fenix_pages_enable_table_cards();
			$GLOBALS['fenix_toc'] = array();
			$fenix_content        = apply_filters( 'the_content', get_the_content() );
			$fenix_content        = str_replace( ']]>', ']]&gt;', $fenix_content );
			$fenix_doc_toc = array();
			foreach ( (array) $GLOBALS['fenix_toc'] as $fenix_h ) {
				if ( 2 === (int) $fenix_h['level'] && '' !== $fenix_h['text'] ) {
					$fenix_doc_toc[] = $fenix_h;
				}
			}
			$fenix_updated = fenix_pages_thai_date( get_the_modified_date( 'c' ) );
			?>

			<section class="section doc-section-wrap">
				<div class="container container-narrow doc-col">
					<article <?php post_class( 'doc-article' ); ?>>

						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="article-thumb"><?php the_post_thumbnail( 'large', array( 'alt' => fenix_pages_featured_alt( get_the_ID() ) ) ); ?></figure>
						<?php endif; ?>

						<?php if ( count( $fenix_doc_toc ) >= 4 ) : ?>
							<details class="doc-toc">
								<summary><?php echo fenix_icon( 'layout', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( fenix_mod( 'doc_toc_label' ) ); ?></span><span class="doc-toc-count"><?php echo esc_html( number_format_i18n( count( $fenix_doc_toc ) ) ); ?></span></summary>
								<ol>
									<?php foreach ( $fenix_doc_toc as $fenix_h ) : ?>
										<li><a href="#<?php echo esc_attr( $fenix_h['id'] ); ?>"><?php echo esc_html( $fenix_h['text'] ); ?></a></li>
									<?php endforeach; ?>
								</ol>
							</details>
						<?php endif; ?>

						<div class="entry-content guide-content doc-body">
							<?php echo $fenix_content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>

						<?php
						wp_link_pages(
							array(
								'before' => '<div class="page-links">',
								'after'  => '</div>',
							)
						);
						?>

						<?php if ( '' !== $fenix_updated ) : ?>
							<p class="doc-updated">
								<?php echo fenix_icon( 'clock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span><?php echo esc_html( fenix_mod( 'doc_updated_label' ) ); ?>:</span>
								<time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( $fenix_updated ); ?></time>
							</p>
						<?php endif; ?>

					</article>
				</div>
			</section>

			<?php
			if ( 'about' === $fenix_slug ) {
				fenix_line_cta( fenix_mod( 'aboutpage_cta_title' ), fenix_mod( 'aboutpage_cta_text' ) );
			}
			?>

		<?php endwhile; ?>

	<?php endif; ?>
</main>

<?php
get_footer();

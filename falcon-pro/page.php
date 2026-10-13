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

			/* สารบัญ: เฉพาะ H2 · ไม่นับหัวของกล่อง .related-links ท้ายหน้า (ไม่ใช่ข้อของเอกสาร)
			 * id ของหัวกล่องนั้นถูกเติมโดย fenix_collect_toc() จึงต้องหาจากเนื้อหาที่ผ่านฟิลเตอร์แล้ว */
			$fenix_toc_skip = array();
			if ( preg_match_all( '#<div\b[^>]*\bclass="[^"]*\brelated-links\b[^"]*"[^>]*>\s*<h2\b[^>]*\bid="([^"]+)"#i', $fenix_content, $fenix_m ) ) {
				$fenix_toc_skip = $fenix_m[1];
			}
			$fenix_doc_toc  = array();
			$fenix_numbered = 0;
			foreach ( (array) $GLOBALS['fenix_toc'] as $fenix_h ) {
				if ( 2 !== (int) $fenix_h['level'] || '' === $fenix_h['text'] || in_array( $fenix_h['id'], $fenix_toc_skip, true ) ) {
					continue;
				}
				$fenix_doc_toc[] = $fenix_h;
				/* หัวข้อพิมพ์เลขข้อไว้เอง เช่น "1. ข้อมูลที่เราเก็บ" หรือ "๒) ..." */
				if ( preg_match( '/^\s*[0-9\x{0E50}-\x{0E59}]+(?:\.[0-9]+)*[.)]?\s/u', $fenix_h['text'] ) ) {
					$fenix_numbered++;
				}
			}
			/* ครึ่งหนึ่งขึ้นไปมีเลขอยู่แล้ว (เพจกฎหมาย) → สารบัญไม่ใส่เลขลำดับซ้ำ (.doc-toc--plain ท้าย style.css) */
			$fenix_toc_plain = $fenix_doc_toc && $fenix_numbered * 2 >= count( $fenix_doc_toc );

			/* ภาพปกที่ FALCON Setup ใส่เป็นภาพเด่นของเพจ (covers/page-<slug>.webp) ใช้เป็นรูปแชร์และรูปหลักใน schema เท่านั้น
			 * ไม่พิมพ์ในเนื้อหา (เป็นการ์ดชื่อเพจ ซ้ำกับ H1) · ภาพเด่นที่เจ้าของเลือกเองยังแสดงตามเดิม */
			$fenix_show_thumb = has_post_thumbnail() && ! ( function_exists( 'fenix_is_theme_page_cover' ) && fenix_is_theme_page_cover( get_the_ID() ) );

			$fenix_updated = fenix_pages_thai_date( get_the_modified_date( 'c' ) );
			?>

			<section class="section doc-section-wrap">
				<div class="container container-narrow doc-col">
					<article <?php post_class( 'doc-article' ); ?>>

						<?php if ( $fenix_show_thumb ) : ?>
							<figure class="article-thumb"><?php the_post_thumbnail( 'large', array( 'alt' => fenix_pages_featured_alt( get_the_ID() ) ) ); ?></figure>
						<?php endif; ?>

						<?php if ( count( $fenix_doc_toc ) >= 4 ) : ?>
							<details class="doc-toc<?php echo $fenix_toc_plain ? ' doc-toc--plain' : ''; ?>">
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

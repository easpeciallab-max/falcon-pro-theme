<?php
/**
 * บทความเดี่ยว (คอนเทนต์ SEO)
 *
 * โครง:
 * แถบอ่าน → หัวบทความพื้นเข้ม (breadcrumb · หมวด · H1 · วันที่เผยแพร่/อัปเดต · เวลาอ่าน)
 * → รูปหน้าปก (alt จากคลังสื่อ) → สารบัญ → เนื้อหา (พื้นขาว อ่านง่าย) → แชร์ → ป้ายกำกับ
 * → กล่องผู้เขียน (ลิงก์ไปเพจเกี่ยวกับเรา) → คำเตือนความเสี่ยง → บทความในหมวดเดียวกัน (พื้นเข้ม) → บล็อกติดต่อ (ติดส่วนท้ายเว็บ)
 *
 * ข้อความที่ผู้เข้าชมเห็นมาจาก Customizer → "หน้ารวมบทความ & บทความ"
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$fenix_cats   = get_the_category();
	$fenix_cat    = ! empty( $fenix_cats ) ? $fenix_cats[0] : null;
	$fenix_thumb  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	$fenix_meta   = fenix_pages_article_meta();
	$fenix_about  = fenix_pages_about_url();
	$fenix_link   = get_permalink();
	$fenix_author = trim( (string) fenix_mod( 'article_author_name' ) );
	?>

	<div class="reading-progress" aria-hidden="true"><span></span></div>

	<main id="main" class="article-page">
		<article <?php post_class( 'single-article' ); ?>>

			<header class="article-hero<?php echo esc_attr( fenix_section_tone( 'article-hero', true ) ); ?>">
				<div class="container container-narrow">
					<?php fenix_pages_crumbs( wp_strip_all_tags( get_the_title() ) ); ?>

					<?php if ( $fenix_cat ) : ?>
						<a class="article-cat" href="<?php echo esc_url( get_category_link( $fenix_cat->term_id ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
					<?php endif; ?>

					<h1 class="article-title"><?php echo fenix_text( wp_strip_all_tags( get_the_title() ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></h1>

					<div class="article-meta">
						<?php if ( '' !== $fenix_meta['published'] ) : ?>
							<span class="article-meta-item">
								<?php echo fenix_icon( 'clock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span><?php echo esc_html( fenix_mod( 'article_published_label' ) ); ?></span>
								<time datetime="<?php echo esc_attr( $fenix_meta['published_iso'] ); ?>"><?php echo esc_html( $fenix_meta['published'] ); ?></time>
							</span>
						<?php endif; ?>
						<?php if ( '' !== $fenix_meta['modified'] ) : ?>
							<span class="article-meta-item">
								<span><?php echo esc_html( fenix_mod( 'article_updated_label' ) ); ?></span>
								<time datetime="<?php echo esc_attr( $fenix_meta['modified_iso'] ); ?>"><?php echo esc_html( $fenix_meta['modified'] ); ?></time>
							</span>
						<?php endif; ?>
						<span class="article-meta-item">
							<?php echo fenix_icon( 'book', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( fenix_pages_count_text( 'article_reading_text', $fenix_meta['minutes'] ) ); ?></span>
						</span>
					</div>
				</div>
			</header>

			<div class="article-body">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="container container-narrow">
						<figure class="article-thumb"><?php the_post_thumbnail( 'large', array( 'alt' => fenix_pages_featured_alt( get_the_ID() ) ) ); ?></figure>
					</div>
				<?php endif; ?>

				<div class="container container-narrow">
					<?php
					fenix_pages_enable_table_cards();
					$GLOBALS['fenix_toc'] = array();
					$fenix_content        = apply_filters( 'the_content', get_the_content() );
					$fenix_content        = str_replace( ']]>', ']]&gt;', $fenix_content );
					$fenix_art_toc        = isset( $GLOBALS['fenix_toc'] ) ? $GLOBALS['fenix_toc'] : array();
					?>

					<?php if ( count( $fenix_art_toc ) >= 2 ) : ?>
						<nav class="toc" aria-label="<?php echo esc_attr( fenix_mod( 'article_toc_label' ) ); ?>">
							<p class="toc-title"><?php echo fenix_icon( 'layout', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( fenix_mod( 'article_toc_label' ) ); ?></p>
							<ul>
								<?php foreach ( $fenix_art_toc as $fenix_h ) : ?>
									<li class="toc-l<?php echo esc_attr( (string) $fenix_h['level'] ); ?>"><a href="#<?php echo esc_attr( $fenix_h['id'] ); ?>"><?php echo esc_html( $fenix_h['text'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

					<div class="entry-content">
						<?php echo $fenix_content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>

					<div class="article-share">
						<span class="article-share-label"><?php echo esc_html( fenix_mod( 'article_share_label' ) ); ?></span>
						<a class="share-btn share-line" href="<?php echo esc_url( 'https://social-plugins.line.me/lineit/share?url=' . rawurlencode( $fenix_link ) ); ?>" target="_blank" rel="noopener" aria-label="LINE"><?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<a class="share-btn share-fb" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $fenix_link ) ); ?>" target="_blank" rel="noopener" aria-label="Facebook"><?php echo fenix_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<button class="share-btn share-copy" type="button" data-url="<?php echo esc_url( $fenix_link ); ?>" aria-label="<?php echo esc_attr( fenix_mod( 'article_share_label' ) ); ?>"><?php echo fenix_icon( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>

					<?php
					wp_link_pages(
						array(
							'before' => '<div class="page-links">',
							'after'  => '</div>',
						)
					);

					$fenix_tags = get_the_tag_list( '<ul class="article-tags"><li>', '</li><li>', '</li></ul>' );
					if ( $fenix_tags ) {
						echo wp_kses_post( $fenix_tags );
					}
					?>

					<?php if ( '' !== $fenix_author ) : ?>
						<aside class="author-box">
							<?php echo fenix_icon_badge( 'users', 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<div class="author-box-body">
								<span class="card-label"><?php echo esc_html( fenix_mod( 'article_author_kicker' ) ); ?></span>
								<p class="author-box-name"><?php echo esc_html( $fenix_author ); ?></p>
								<?php if ( fenix_pages_has( fenix_mod( 'article_author_bio' ) ) ) : ?>
									<p class="author-box-bio"><?php echo esc_html( fenix_mod( 'article_author_bio' ) ); ?></p>
								<?php endif; ?>
								<?php if ( '' !== $fenix_about && '' !== trim( (string) fenix_mod( 'article_author_link' ) ) ) : ?>
									<a class="author-box-link" href="<?php echo esc_url( $fenix_about ); ?>"><?php echo esc_html( fenix_mod( 'article_author_link' ) ); ?> <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<?php endif; ?>
							</div>
						</aside>
					<?php endif; ?>

					<?php /* คำเตือนความเสี่ยง · ข้อความหลักเต็มเสมอ + คำเตือนเพิ่มของบทความ · ไม่ใช้ .reveal ให้เห็นทันทีแม้ JS ไม่ทำงาน */ ?>
					<div class="article-disclaimer">
						<?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div>
							<p><?php echo esc_html( fenix_mod( 'risk_text' ) ); ?></p>
							<?php if ( fenix_pages_has( fenix_mod( 'article_disclaimer_text' ) ) ) : ?>
								<p><?php echo esc_html( fenix_mod( 'article_disclaimer_text' ) ); ?></p>
							<?php endif; ?>
							<?php
							$fenix_risk_url = function_exists( 'fenix_published_page_url' ) ? fenix_published_page_url( 'risk-disclosure' ) : '';
							if ( '' !== $fenix_risk_url && '' !== trim( (string) fenix_mod( 'article_disclaimer_link' ) ) ) :
								?>
								<p><a href="<?php echo esc_url( $fenix_risk_url ); ?>"><?php echo esc_html( fenix_mod( 'article_disclaimer_link' ) ); ?></a></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

		</article>

		<?php
		if ( $fenix_cat ) :
			$fenix_related = new WP_Query(
				array(
					'category__in'        => array( $fenix_cat->term_id ),
					'post__not_in'        => array( get_the_ID() ),
					'posts_per_page'      => 9,
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $fenix_related->have_posts() ) :
				?>
				<section class="section related-section<?php echo esc_attr( fenix_pages_tone( 'related', true, true ) ); ?>" aria-labelledby="related-title">
					<div class="container">
						<div class="related-head">
							<h2 id="related-title"><?php echo fenix_text( fenix_mod( 'article_related_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
							<div class="rail-nav">
								<button type="button" class="rail-btn rail-prev" data-dir="-1" aria-label="<?php echo esc_attr( fenix_mod( 'articles_prev_label' ) ); ?>"><?php echo fenix_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
								<button type="button" class="rail-btn rail-next" data-dir="1" aria-label="<?php echo esc_attr( fenix_mod( 'articles_next_label' ) ); ?>"><?php echo fenix_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
							</div>
						</div>
						<div class="related-rail">
							<?php
							while ( $fenix_related->have_posts() ) :
								$fenix_related->the_post();
								?>
								<article <?php post_class( 'post-card' ); ?>>
									<?php if ( has_post_thumbnail() ) : ?>
										<a class="post-card-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
											<?php the_post_thumbnail( 'medium_large', array( 'alt' => fenix_pages_featured_alt( get_the_ID() ) ) ); ?>
										</a>
									<?php endif; ?>
									<div class="post-card-body">
										<span class="post-meta"><?php echo esc_html( fenix_pages_thai_date( get_the_date( 'c' ) ) ); ?></span>
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									</div>
								</article>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				</section>
				<?php
			endif;
		endif;
		?>

		<?php /* บล็อกติดต่อปิดท้าย · อยู่ติดส่วนท้ายเว็บ (หลังบทความในหมวดเดียวกัน) */ ?>
		<?php fenix_line_cta(); ?>

	</main>

	<?php
	/* BlogPosting · ผู้เขียนเป็นทีมงาน (Organization) ตามกล่องผู้เขียน ไม่เปิดเผยชื่อผู้ใช้ WordPress */
	$fenix_author_node = array(
		'@type' => 'Organization',
		'name'  => '' !== $fenix_author ? $fenix_author : get_bloginfo( 'name' ),
	);
	if ( '' !== $fenix_about ) {
		$fenix_author_node['url'] = $fenix_about;
	}
	$fenix_schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => wp_strip_all_tags( get_the_title() ),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => $fenix_author_node,
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => fenix_logo_url(),
			),
		),
		'mainEntityOfPage' => $fenix_link,
	);
	if ( $fenix_thumb ) {
		$fenix_schema['image'] = $fenix_thumb;
	}
	if ( ! fenix_has_seo_plugin() ) {
		$fenix_schema['description'] = fenix_meta_description();
		echo '<script type="application/ld+json">' . wp_json_encode( $fenix_schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	?>

	<?php
endwhile;

get_footer();

<?php
/**
 * Template Name: FALCON · หน้า Risk Disclosure
 *
 * โครง: หัวเพจ → กล่องคำเตือน + ภาพประกอบ (ถ้ามี) + หัวข้อความเสี่ยง 01 ถึง 07 (พื้นเข้ม · เลขลำดับอยู่นอก <h2> · จำนวนหัวข้อ = fenix_pages_risk_block_count)
 *       → เนื้อหายาวจาก editor (พื้นขาว) → วันที่ปรับปรุงล่าสุด (ท้ายเนื้อหาทั้งหมด · ไม่ได้กรอก = วันที่แก้ไขเพจ) → บล็อกติดต่อ
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	the_post();
}
$fenix_title = get_the_title();
$fenix_sub   = fenix_mod( 'riskpage_sub' );
fenix_page_hero( fenix_mod( 'riskpage_kicker' ), $fenix_title ? $fenix_title : fenix_mod( 'riskpage_kicker' ), fenix_pages_has( $fenix_sub ) ? $fenix_sub : '' );

$fenix_image   = trim( (string) fenix_mod( 'riskpage_image' ) );
$fenix_caption = trim( (string) fenix_mod( 'riskpage_image_caption' ) );
/* วันที่ปรับปรุง: ค่าที่เจ้าของกรอก · ยังไม่กรอก = วันที่แก้ไขเพจล่าสุด (fenix_pages_risk_updated) */
$fenix_updated = fenix_pages_risk_updated();
?>

<main id="main" class="risk-page">

<section class="section riskdoc-section<?php echo esc_attr( fenix_pages_tone( 'riskdoc', true, true ) ); ?>">
	<div class="container container-narrow">

		<?php /* คำเตือนเกริ่นนำ · ไม่ใช้ .reveal เพื่อให้เห็นทันทีแม้ JS ไม่ทำงาน */ ?>
		<div class="riskdoc-intro">
			<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo fenix_text( fenix_mod( 'riskpage_intro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
		</div>

		<?php if ( '' !== $fenix_image ) : ?>
			<figure class="riskdoc-figure media-frame">
				<?php
				echo fenix_media_open( // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside
					$fenix_image,
					fenix_media_picture( '', $fenix_image, $fenix_caption, 1280, 720 ),
					$fenix_caption
				);
				?>
				<?php if ( '' !== $fenix_caption ) : ?>
					<figcaption class="media-cap"><?php echo esc_html( $fenix_caption ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="riskdoc">
			<?php
			$fenix_n     = 0;
			$fenix_count = function_exists( 'fenix_pages_risk_block_count' ) ? fenix_pages_risk_block_count() : 6;
			for ( $i = 1; $i <= $fenix_count; $i++ ) :
				$b_title = trim( (string) fenix_mod( 'rp_block' . $i . '_title' ) );
				$b_text  = trim( (string) fenix_mod( 'rp_block' . $i . '_text' ) );
				if ( '' === $b_title && '' === $b_text ) {
					continue;
				}
				$fenix_n++;
				?>
				<?php /* เลขลำดับอยู่นอก <h2> และซ่อนจากโปรแกรมอ่านหน้าจอ: หัวข้อในโครงเอกสารจึงเป็นข้อความล้วน ไม่มี "01" นำหน้า */ ?>
				<article class="riskdoc-block" id="<?php echo esc_attr( 'risk-' . $fenix_n ); ?>">
					<span class="riskdoc-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_n ) ); ?></span>
					<?php if ( '' !== $b_title ) : ?>
						<h2><?php echo fenix_text( $b_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $b_text ) : ?>
						<p><?php echo nl2br( esc_html( $b_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php endif; ?>
				</article>
			<?php endfor; ?>
		</div>

	</div>
</section>

<?php
fenix_pages_enable_table_cards();
fenix_page_longform( 'section' );
?>

<?php if ( '' !== $fenix_updated ) : ?>
	<div class="riskdoc-updated-wrap">
		<div class="container container-narrow">
			<p class="riskdoc-updated"><?php echo fenix_icon( 'clock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_updated ); ?></span></p>
		</div>
	</div>
<?php endif; ?>

<?php fenix_line_cta( fenix_mod( 'riskpage_cta_title' ), fenix_mod( 'riskpage_cta_text' ) ); ?>

</main>

<?php
get_footer();

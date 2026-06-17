<?php
/**
 * Template Name: FALCON · หน้า Pricing
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$fenix_title = get_the_title();
fenix_page_hero( 'Pricing', $fenix_title ? $fenix_title : 'แพ็กเกจ & ราคา', fenix_mod( 'pricing_sub' ) );

$fenix_line = fenix_mod( 'line_url' );
$fenix_mode = fenix_mod( 'pricing_mode' );
?>

<main id="main">

<?php /* การ์ดแพ็กเกจ */ ?>
<section class="section">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Packages</span>
			<h2><?php echo esc_html( fenix_mod( 'pricing_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'pricing_subtitle' ) ); ?></p>
		</div>

		<div class="pricing-grid">
			<?php
			for ( $i = 1; $i <= 3; $i++ ) :
				$k_name = fenix_mod( 'pkg' . $i . '_name' );
				if ( ! $k_name ) {
					continue;
				}
				$k_featured = (bool) fenix_mod( 'pkg' . $i . '_featured' );
				?>
				<article class="price-card reveal <?php echo $k_featured ? 'is-featured' : ''; ?>">
					<?php if ( $k_featured ) : ?>
						<span class="price-flag">แนะนำ</span>
					<?php endif; ?>
					<h3 class="price-name"><?php echo esc_html( $k_name ); ?></h3>
					<p class="price-tag"><?php echo esc_html( fenix_mod( 'pkg' . $i . '_tag' ) ); ?></p>
					<?php if ( 'price' === $fenix_mode ) : ?>
						<div class="price-amount">
							<strong><?php echo esc_html( fenix_mod( 'pkg' . $i . '_price' ) ); ?></strong>
							<span><?php echo esc_html( fenix_mod( 'pkg' . $i . '_period' ) ); ?></span>
						</div>
					<?php else : ?>
						<div class="price-amount price-contact">
							<strong>สอบถามราคา</strong>
							<span>ทาง LINE</span>
						</div>
					<?php endif; ?>
					<ul class="price-feats">
						<?php foreach ( fenix_lines( fenix_mod( 'pkg' . $i . '_features' ) ) as $fenix_item ) : ?>
							<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<a class="btn <?php echo $k_featured ? 'btn-fire' : 'btn-ghost'; ?> btn-block" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
						<?php echo esc_html( fenix_mod( 'pricing_btn_text' ) ); ?>
					</a>
				</article>
			<?php endfor; ?>
		</div>

		<?php if ( fenix_mod( 'pricing_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'pricing_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php /* ตารางเปรียบเทียบ */ ?>
<?php
$fenix_rows = fenix_lines( fenix_mod( 'compare_rows' ) );
if ( count( $fenix_rows ) >= 2 ) :
	$fenix_table = array();
	foreach ( $fenix_rows as $fenix_row ) {
		$fenix_cells = array_map( 'trim', explode( '|', $fenix_row ) );
		$fenix_table[] = $fenix_cells;
	}
	$fenix_head = array_shift( $fenix_table );
	?>
	<section class="section section-alt" id="compare">
		<div class="container container-narrow">
			<div class="sec-head reveal">
				<span class="kicker">Compare</span>
				<h2><?php echo esc_html( fenix_mod( 'compare_title' ) ); ?></h2>
			</div>
			<div class="compare-wrap reveal">
				<table class="compare-table">
					<thead>
						<tr>
							<?php foreach ( $fenix_head as $idx => $fenix_cell ) : ?>
								<th class="<?php echo 0 === $idx ? 'compare-rowhead' : ''; ?>"><?php echo esc_html( $fenix_cell ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $fenix_table as $fenix_trow ) : ?>
							<tr>
								<?php
								foreach ( $fenix_trow as $idx => $fenix_cell ) :
									$fenix_cls = 0 === $idx ? 'compare-rowhead' : '';
									if ( '✓' === $fenix_cell ) {
										$fenix_cls .= ' cell-yes';
									} elseif ( '✗' === $fenix_cell || 'x' === strtolower( $fenix_cell ) ) {
										$fenix_cls .= ' cell-no';
									}
									?>
									<td class="<?php echo esc_attr( trim( $fenix_cls ) ); ?>">
										<?php
										if ( '✓' === $fenix_cell ) {
											echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput
											echo '<span class="sr-only">มี</span>';
										} elseif ( '✗' === $fenix_cell || 'x' === strtolower( $fenix_cell ) ) {
											echo fenix_icon( 'x', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput
											echo '<span class="sr-only">ไม่มี</span>';
										} else {
											echo esc_html( $fenix_cell );
										}
										?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php fenix_line_cta( 'ยังไม่แน่ใจว่าแพ็กเกจไหนเหมาะ?', 'ทักมาปรึกษาทีมงานเพื่อเลือกแพ็กเกจที่เหมาะกับทุนและการใช้งานของคุณ' ); ?>

</main>

<?php
get_footer();

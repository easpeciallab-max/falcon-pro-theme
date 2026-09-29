<?php
/**
 * Template Name: FALCON · หน้า Pricing
 *
 * โครง:
 * หัวเพจ → การ์ดแพ็กเกจ + หมายเหตุ → ตารางเปรียบเทียบ → สิทธิ์ใช้งาน/VPS + ขั้นตอนสั่งซื้อ
 * → เนื้อหายาวจาก editor → บล็อกติดต่อ
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
$fenix_sub   = fenix_mod( 'pricing_sub' );
fenix_page_hero( fenix_mod( 'pricing_page_kicker' ), $fenix_title ? $fenix_title : fenix_mod( 'pricing_title' ), fenix_pages_has( $fenix_sub ) ? $fenix_sub : '' );

$fenix_mode     = fenix_mod( 'pricing_mode' );
$fenix_has_line = fenix_has_line_url();
?>

<main id="main" class="pricing-page">

<?php /* การ์ดแพ็กเกจ (ปิดได้ที่ ปรับแต่ง → 10) แพ็กเกจราคา → แสดงส่วนนี้) */ ?>
<?php if ( fenix_mod( 'show_pricing' ) ) : ?>
<section class="section pricing-packages<?php echo esc_attr( fenix_pages_tone( 'pricing', true, true ) ); ?>" id="packages">
	<div class="container">
		<?php fenix_pages_sec_head( fenix_mod( 'pricing_kicker' ), fenix_mod( 'pricing_title' ), fenix_mod( 'pricing_subtitle' ) ); ?>

		<div class="pricing-grid">
			<?php
			for ( $i = 1; $i <= 3; $i++ ) :
				$k_name = trim( (string) fenix_mod( 'pkg' . $i . '_name' ) );
				if ( '' === $k_name ) {
					continue;
				}
				$k_featured = (bool) fenix_mod( 'pkg' . $i . '_featured' );
				$k_price    = fenix_mod( 'pkg' . $i . '_price' );
				$k_tag      = fenix_mod( 'pkg' . $i . '_tag' );
				?>
				<article class="price-card reveal<?php echo $k_featured ? ' is-featured' : ''; ?>">
					<div class="price-card-head">
						<h3 class="price-name"><?php echo esc_html( $k_name ); ?></h3>
						<?php if ( $k_featured && fenix_mod( 'pricing_flag_label' ) ) : ?>
							<span class="price-flag"><?php echo esc_html( fenix_mod( 'pricing_flag_label' ) ); ?></span>
						<?php endif; ?>
					</div>
					<?php if ( fenix_pages_has( $k_tag ) ) : ?>
						<p class="price-tag"><?php echo fenix_text( $k_tag ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
					<?php endif; ?>
					<?php if ( 'price' === $fenix_mode && fenix_pages_price_ready( $k_price ) ) : ?>
						<div class="price-amount">
							<strong><?php echo esc_html( $k_price ); ?></strong>
							<span><?php echo esc_html( fenix_mod( 'pkg' . $i . '_period' ) ); ?></span>
						</div>
					<?php else : ?>
						<div class="price-amount price-contact">
							<strong><?php echo esc_html( fenix_mod( 'pricing_contact_label' ) ); ?></strong>
							<?php if ( $fenix_has_line && fenix_mod( 'pricing_contact_via' ) ) : ?>
								<span><?php echo esc_html( fenix_mod( 'pricing_contact_via' ) ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<ul class="price-feats">
						<?php foreach ( fenix_lines( fenix_mod( 'pkg' . $i . '_features' ) ) as $fenix_item ) : ?>
							<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php fenix_pages_package_button( $k_name, $k_featured ); ?>
				</article>
			<?php endfor; ?>
		</div>

		<?php if ( fenix_pages_has( fenix_mod( 'pricing_note' ) ) ) : ?>
			<p class="pricing-note reveal"><?php echo fenix_icon( 'tag', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( fenix_mod( 'pricing_note' ) ); ?></span></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ตารางเปรียบเทียบ · จอแคบเรียงเป็นการ์ดทีละหัวข้อ */ ?>
<?php
$fenix_rows = fenix_lines( fenix_mod( 'compare_rows' ) );
if ( count( $fenix_rows ) >= 2 ) :
	$fenix_table = array();
	foreach ( $fenix_rows as $fenix_row ) {
		$fenix_table[] = array_map( 'trim', explode( '|', $fenix_row ) );
	}
	$fenix_head = array_shift( $fenix_table );
	?>
	<section class="section compare-section<?php echo esc_attr( fenix_pages_tone( 'compare' ) ); ?>" id="compare">
		<div class="container container-narrow">
			<?php fenix_pages_sec_head( fenix_mod( 'compare_kicker' ), fenix_mod( 'compare_title' ) ); ?>
			<div class="compare-wrap compare-wrap--cards reveal">
				<table class="compare-table">
					<thead>
						<tr>
							<?php foreach ( $fenix_head as $idx => $fenix_cell ) : ?>
								<th scope="col"<?php echo 0 === $idx ? ' class="compare-rowhead"' : ''; ?>><?php echo esc_html( $fenix_cell ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $fenix_table as $fenix_trow ) : ?>
							<tr>
								<?php
								foreach ( $fenix_trow as $idx => $fenix_cell ) :
									$fenix_yes = '✓' === $fenix_cell;
									$fenix_no  = '✗' === $fenix_cell || 'x' === strtolower( $fenix_cell );
									$fenix_lbl = isset( $fenix_head[ $idx ] ) ? $fenix_head[ $idx ] : '';
									if ( 0 === $idx ) :
										?>
										<th scope="row" class="compare-rowhead"><?php echo esc_html( $fenix_cell ); ?></th>
										<?php
										continue;
									endif;
									?>
									<td class="<?php echo $fenix_yes ? 'cell-yes' : ( $fenix_no ? 'cell-no' : 'cell-text' ); ?>" data-label="<?php echo esc_attr( $fenix_lbl ); ?>">
										<?php
										if ( $fenix_yes ) {
											echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput
											echo '<span class="sr-only">' . esc_html( fenix_mod( 'compare_yes_label' ) ) . '</span>';
										} elseif ( $fenix_no ) {
											echo fenix_icon( 'x', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput
											echo '<span class="sr-only">' . esc_html( fenix_mod( 'compare_no_label' ) ) . '</span>';
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

<?php /* สิทธิ์ใช้งาน / VPS + ขั้นตอนสั่งซื้อ */ ?>
<?php
$fenix_license = trim( (string) fenix_mod( 'pricing_license_rows' ) );
$fenix_points  = fenix_lines( fenix_mod( 'pricing_license_points' ) );
$fenix_steps   = fenix_pages_pairs( fenix_mod( 'pricing_order_steps' ) );
if ( '' !== $fenix_license || $fenix_points || $fenix_steps ) :
	?>
	<section class="section pricing-terms<?php echo esc_attr( fenix_pages_tone( 'pricing-terms', true, true ) ); ?>" id="license">
		<div class="container">
			<?php if ( '' !== $fenix_license || $fenix_points ) : ?>
				<div class="pricing-license">
					<?php fenix_pages_sec_head( fenix_mod( 'pricing_license_kicker' ), fenix_mod( 'pricing_license_title' ), fenix_mod( 'pricing_license_text' ) ); ?>
					<?php if ( '' !== $fenix_license ) : ?>
						<div class="pricing-license-table reveal">
							<?php echo str_replace( 'table-wrap table-wrap--stack', 'table-wrap table-wrap--stack table-wrap--cards', fenix_rich_text( $fenix_license ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_rich_text ?>
						</div>
					<?php endif; ?>
					<?php if ( $fenix_points ) : ?>
						<ul class="pricing-points reveal">
							<?php foreach ( $fenix_points as $fenix_point ) : ?>
								<li><?php echo fenix_icon( 'shield', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_rich_inline( $fenix_point ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $fenix_steps ) : ?>
				<div class="pricing-order" id="order">
					<?php fenix_pages_sec_head( fenix_mod( 'pricing_order_kicker' ), fenix_mod( 'pricing_order_title' ), fenix_mod( 'pricing_order_sub' ) ); ?>
					<ol class="order-flow order-flow--<?php echo esc_attr( (string) min( 5, count( $fenix_steps ) ) ); ?>">
						<?php foreach ( $fenix_steps as $fenix_n => $fenix_step ) : ?>
							<li class="reveal">
								<span class="order-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_n + 1 ) ); ?></span>
								<h3><?php echo fenix_text( $fenix_step[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
								<?php if ( '' !== $fenix_step[1] ) : ?>
									<p><?php echo fenix_rich_inline( $fenix_step[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
					<div class="order-action reveal">
						<?php
						fenix_contact_button(
							array(
								'text'  => fenix_mod( 'pricing_order_btn_text' ),
								'class' => 'btn btn-fire',
								'pos'   => 'pricing-order',
							)
						);
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php
fenix_pages_enable_table_cards();
fenix_page_longform( 'section' );
?>

<?php fenix_line_cta( fenix_mod( 'pricing_cta_title' ), fenix_mod( 'pricing_cta_text' ) ); ?>

</main>

<?php
get_footer();

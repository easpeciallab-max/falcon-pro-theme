<?php
/**
 * แผงควบคุม EA จำลอง (HTML ล้วน · ไม่มีตัวเลขผลการเทรด)
 * ข้อความทั้งหมดแก้ได้ที่ ปรับแต่ง → Hero
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fenix_panel_fields = array();
foreach ( fenix_lines( fenix_mod( 'hero_panel_fields' ) ) as $fenix_row ) {
	$fenix_parts = array_map( 'trim', explode( '|', $fenix_row, 2 ) );
	if ( isset( $fenix_parts[1] ) ) {
		$fenix_panel_fields[] = $fenix_parts;
	}
}
?>
<div class="ea-panel" role="img" aria-label="<?php echo esc_attr( fenix_mod( 'hero_panel_title' ) . ' · ' . fenix_mod( 'hero_panel_caption' ) ); ?>">
	<div class="ea-panel-chart" aria-hidden="true">
		<svg viewBox="0 0 320 110" preserveAspectRatio="none" focusable="false">
			<g class="ea-grid"><path d="M0 22H320M0 55H320M0 88H320"/></g>
			<g class="ea-candles">
				<?php
				// แท่งเทียนตกแต่ง (ไม่ใช่ข้อมูลราคาจริง)
				$fenix_candles = array( array( 70, 88, 80, 94, 0 ), array( 62, 84, 68, 90, 1 ), array( 58, 76, 60, 80, 1 ), array( 60, 74, 66, 78, 0 ), array( 50, 70, 54, 72, 1 ), array( 44, 62, 48, 66, 1 ), array( 46, 60, 52, 64, 0 ), array( 36, 56, 40, 58, 1 ), array( 30, 48, 34, 52, 1 ), array( 32, 46, 38, 50, 0 ), array( 22, 40, 26, 44, 1 ), array( 14, 34, 18, 36, 1 ) );
				foreach ( $fenix_candles as $fenix_ci => $fenix_c ) :
					$fenix_x = 14 + $fenix_ci * 26;
					?>
					<g class="<?php echo $fenix_c[4] ? 'up' : 'down'; ?>">
						<line x1="<?php echo (int) $fenix_x; ?>" y1="<?php echo (int) $fenix_c[0]; ?>" x2="<?php echo (int) $fenix_x; ?>" y2="<?php echo (int) $fenix_c[3]; ?>"/>
						<rect x="<?php echo (int) $fenix_x - 5; ?>" y="<?php echo (int) $fenix_c[2]; ?>" width="10" height="<?php echo (int) max( 6, $fenix_c[1] - $fenix_c[2] ); ?>" rx="1.5"/>
					</g>
				<?php endforeach; ?>
			</g>
		</svg>
	</div>

	<div class="ea-panel-head">
		<span class="ea-panel-ic"><?php echo fenix_icon( 'robot' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<div class="ea-panel-name">
			<strong><?php echo esc_html( fenix_mod( 'hero_panel_title' ) ); ?></strong>
			<span class="ea-status"><i aria-hidden="true"></i><?php echo esc_html( fenix_mod( 'hero_panel_status' ) ); ?></span>
		</div>
		<span class="ea-panel-badge">
			<strong><?php echo esc_html( fenix_mod( 'hero_panel_badge' ) ); ?></strong>
			<small>Automated Trading</small>
		</span>
	</div>

	<?php if ( ! empty( $fenix_panel_fields ) ) : ?>
		<dl class="ea-fields">
			<?php foreach ( $fenix_panel_fields as $fenix_field ) : ?>
				<div class="ea-field">
					<dt><?php echo esc_html( $fenix_field[0] ); ?></dt>
					<dd><?php echo esc_html( $fenix_field[1] ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	<?php endif; ?>

	<div class="ea-panel-foot">
		<span class="ea-active"><?php echo fenix_icon( 'play', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'hero_panel_button' ) ); ?></span>
		<?php if ( fenix_mod( 'hero_panel_tags' ) ) : ?>
			<span class="ea-tags"><?php echo esc_html( fenix_mod( 'hero_panel_tags' ) ); ?></span>
		<?php endif; ?>
	</div>
</div>
<?php if ( fenix_mod( 'hero_panel_caption' ) ) : ?>
	<p class="ea-panel-caption"><?php echo esc_html( fenix_mod( 'hero_panel_caption' ) ); ?></p>
<?php endif; ?>

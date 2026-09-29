<?php
/**
 * Footer · "Console" ของ FALCON (พื้น --black-deep)
 * 1) ช่องทางติดต่อ (ปุ่มติดต่อ + QR + OpenChat + กล่องเตรียมข้อความแรก + เวลาตอบแชท)
 * 2) ดัชนี: หน้าในเว็บ · ช่องทาง · เอกสาร · ข้อมูลระบบ
 * 3) คำเตือนความเสี่ยงฉบับเต็ม (ห้ามลบ/ย่อ)
 * 4) ชื่อแบรนด์ตัวใหญ่แบบเส้นขอบ (ไม่บังคับ)
 * 5) แถบสถานะ: ลิขสิทธิ์ · ข้อความสถานะ · นาฬิกาเวลาไทย (ต้องมี JS) · กลับขึ้นด้านบน
 * + บาร์ล่างมือถือ (ช่อง active คิดฝั่ง PHP) และปุ่ม LINE ลอย (เฉพาะเมื่อมีลิงก์ LINE)
 * การ์ดความยินยอมคุกกี้พิมพ์ผ่าน wp_footer (inc/modules/consent.php) ไม่ใช่ไฟล์นี้
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fenix_chrome_dock' ) ) {
	wp_footer();
	echo "</body>\n</html>\n";
	return;
}

$fenix_target   = fenix_contact_target();
$fenix_is_admin = current_user_can( 'edit_theme_options' );
$fenix_openchat = trim( (string) fenix_mod( 'line_openchat_url' ) );
$fenix_openchat = fenix_chrome_url_ok( $fenix_openchat ) ? $fenix_openchat : '';
$fenix_qr       = $fenix_target['is_line'] ? trim( (string) fenix_mod( 'line_qr_image' ) ) : '';
$fenix_headline = trim( (string) fenix_mod( 'footer_headline' ) );
$fenix_headline = '' !== $fenix_headline ? $fenix_headline : (string) fenix_mod( 'contact_title' );
$fenix_sub      = trim( (string) fenix_mod( 'footer_sub' ) );
$fenix_sub      = '' !== $fenix_sub ? $fenix_sub : (string) fenix_mod( 'contact_text' );
$fenix_label    = trim( (string) fenix_mod( 'footer_console_label' ) );
$fenix_prep     = fenix_lines( fenix_mod( 'footer_prep_items' ) );
$fenix_hours    = fenix_lines( fenix_mod( 'footer_hours_text' ) );
$fenix_launch   = '' !== $fenix_target['url'] || '' !== $fenix_openchat || $fenix_is_admin;

$fenix_index    = fenix_chrome_index_items();
$fenix_channels = fenix_chrome_channels();
$fenix_docs     = fenix_chrome_doc_items();
/* ลิงก์เปิดการ์ดคุกกี้ · ชื่อลิงก์ตั้งที่หมวดคุกกี้ (inc/modules/consent.php) */
$fenix_cookie   = ( function_exists( 'fenix_consent_link' ) && '' !== trim( (string) fenix_mod( 'consent_link_label' ) ) ) ? fenix_consent_link( array( 'echo' => false ) ) : '';
$fenix_specs    = fenix_chrome_spec_rows();

/* ชื่อแบรนด์ตัวใหญ่ · คำสุดท้ายเป็นสีเขียว (FALCON / PRO) */
$fenix_wm   = trim( (string) fenix_mod( 'footer_watermark_text' ) );
$fenix_wm_a = $fenix_wm;
$fenix_wm_b = '';
$fenix_wm_s = strrpos( $fenix_wm, ' ' );
if ( false !== $fenix_wm_s ) {
	$fenix_wm_a = substr( $fenix_wm, 0, $fenix_wm_s );
	$fenix_wm_b = substr( $fenix_wm, $fenix_wm_s + 1 );
}

$fenix_bkk  = new DateTimeZone( 'Asia/Bangkok' );
$fenix_top  = trim( (string) fenix_mod( 'footer_backtop_text' ) );
$fenix_stat = trim( (string) fenix_mod( 'footer_status_text' ) );
?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
<footer class="site-footer site-footer--dark site-footer--console" id="contact">
	<div class="container ft-inner">

		<?php if ( $fenix_launch ) : ?>
		<section class="ft-launch<?php echo ( $fenix_prep || $fenix_hours ) ? '' : ' ft-launch--solo'; ?>" aria-labelledby="ft-launch-title">
			<div class="ft-launch-main">
				<?php if ( '' !== $fenix_label ) : ?>
					<p class="chrome-label"><span class="chrome-dot" aria-hidden="true"></span><?php echo esc_html( $fenix_label ); ?></p>
				<?php endif; ?>
				<h2 class="ft-launch-title" id="ft-launch-title"><?php echo fenix_text( $fenix_headline ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></h2>
				<?php if ( '' !== trim( $fenix_sub ) ) : ?>
					<p class="ft-launch-sub"><?php echo fenix_text( $fenix_sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_text ?></p>
				<?php endif; ?>

				<div class="ft-actions">
					<div class="ft-contact<?php echo '' !== $fenix_qr ? ' has-qr' : ''; ?>">
						<?php
						fenix_contact_button(
							array(
								'class' => 'btn btn-fire btn-lg',
								'pos'   => 'footer',
							)
						);
						?>
						<?php if ( '' !== $fenix_qr ) : ?>
							<div class="ft-qr-flyout" aria-hidden="true">
								<img src="<?php echo esc_url( $fenix_qr ); ?>" alt="" width="160" height="160" loading="lazy" decoding="async">
							</div>
						<?php endif; ?>
					</div>
					<?php if ( '' !== $fenix_openchat ) : ?>
						<a class="btn btn-ghost btn-lg ft-openchat" href="<?php echo esc_url( $fenix_openchat ); ?>" target="_blank" rel="noopener" data-line-pos="footer-openchat">
							<?php echo fenix_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( fenix_mod( 'line_openchat_text' ) ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $fenix_qr ) : ?>
					<details class="ft-qr-mobile">
						<summary><?php echo fenix_icon( 'qr', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( fenix_mod( 'footer_qr_toggle_text' ) ); ?></span></summary>
						<img src="<?php echo esc_url( $fenix_qr ); ?>" alt="<?php echo esc_attr( fenix_mod( 'footer_line_qr_alt' ) ); ?>" width="200" height="200" loading="lazy" decoding="async">
					</details>
				<?php elseif ( $fenix_target['is_line'] && $fenix_is_admin ) : ?>
					<p class="admin-hint">ยังไม่มีรูป QR · อัปโหลดที่ ปรับแต่ง → 1) ช่องทางติดต่อ → รูป QR Code LINE OA (ข้อความนี้เห็นเฉพาะแอดมิน)</p>
				<?php endif; ?>
			</div>

			<?php if ( $fenix_prep || $fenix_hours ) : ?>
			<div class="ft-launch-side">
				<?php if ( $fenix_prep ) : ?>
					<div class="ft-prep">
						<h3 class="ft-side-title"><?php echo esc_html( fenix_mod( 'footer_prep_title' ) ); ?></h3>
						<?php if ( '' !== trim( (string) fenix_mod( 'footer_prep_text' ) ) ) : ?>
							<p class="ft-side-text"><?php echo esc_html( fenix_mod( 'footer_prep_text' ) ); ?></p>
						<?php endif; ?>
						<ol class="ft-prep-list">
							<?php foreach ( $fenix_prep as $fenix_i => $fenix_item ) : ?>
								<li><span class="ft-prep-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_i + 1 ) ); ?></span><span><?php echo esc_html( $fenix_item ); ?></span></li>
							<?php endforeach; ?>
						</ol>
					</div>
				<?php endif; ?>
				<?php if ( $fenix_hours ) : ?>
					<div class="ft-hours">
						<h3 class="ft-side-title"><?php echo fenix_icon( 'clock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'footer_hours_title' ) ); ?></h3>
						<p class="ft-side-text"><?php echo implode( '<br>', array_map( 'esc_html', $fenix_hours ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- each line escaped ?></p>
					</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</section>
		<?php endif; ?>

		<nav class="ft-index" aria-label="ลิงก์ท้ายเว็บ">
			<?php if ( $fenix_index ) : ?>
			<div class="ft-col">
				<h3 class="ft-col-title"><?php echo esc_html( fenix_mod( 'footer_index_title' ) ); ?></h3>
				<ol class="ft-list ft-list--num">
					<?php foreach ( $fenix_index as $fenix_i => $fenix_entry ) : ?>
						<li><a href="<?php echo esc_url( $fenix_entry[1] ); ?>"><span class="ft-idx" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_i + 1 ) ); ?></span><span><?php echo esc_html( $fenix_entry[0] ); ?></span></a></li>
					<?php endforeach; ?>
				</ol>
			</div>
			<?php endif; ?>

			<?php if ( $fenix_channels ) : ?>
			<div class="ft-col">
				<h3 class="ft-col-title"><?php echo esc_html( fenix_mod( 'footer_channels_title' ) ); ?></h3>
				<ul class="ft-list ft-list--chan">
					<?php foreach ( $fenix_channels as $fenix_channel ) : ?>
						<li>
							<a class="ft-chan<?php echo ! empty( $fenix_channel['line'] ) ? ' is-line' : ''; ?>" href="<?php echo fenix_chrome_social_href( $fenix_channel ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?>"<?php echo isset( $fenix_channel['mailto'] ) ? '' : ' target="_blank" rel="noopener"'; ?><?php echo ! empty( $fenix_channel['pos'] ) ? ' data-line-pos="' . esc_attr( $fenix_channel['pos'] ) . '"' : ''; ?>>
								<span class="ft-chan-ic"><?php echo fenix_icon( $fenix_channel['icon'], 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span><?php echo esc_html( $fenix_channel['label'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ( $fenix_docs || '' !== $fenix_cookie ) : ?>
			<div class="ft-col">
				<h3 class="ft-col-title"><?php echo esc_html( fenix_mod( 'footer_docs_title' ) ); ?></h3>
				<ul class="ft-list">
					<?php foreach ( $fenix_docs as $fenix_doc ) : ?>
						<li><a href="<?php echo esc_url( $fenix_doc[1] ); ?>"><?php echo esc_html( $fenix_doc[0] ); ?></a></li>
					<?php endforeach; ?>
					<?php if ( '' !== $fenix_cookie ) : ?>
						<li><?php echo $fenix_cookie; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_consent_link ?></li>
					<?php endif; ?>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ( $fenix_specs ) : ?>
			<div class="ft-col ft-col--spec">
				<h3 class="ft-col-title"><?php echo esc_html( fenix_mod( 'footer_spec_title' ) ); ?></h3>
				<dl class="ft-spec">
					<?php foreach ( $fenix_specs as $fenix_spec ) : ?>
						<div class="ft-spec-row"><dt><?php echo esc_html( $fenix_spec[0] ); ?></dt><dd><?php echo esc_html( $fenix_spec[1] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			</div>
			<?php endif; ?>
		</nav>

		<?php if ( '' !== trim( (string) fenix_mod( 'risk_text' ) ) ) : ?>
		<div class="ft-risk" role="note">
			<span class="ft-risk-ic"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<p><?php echo esc_html( fenix_mod( 'risk_text' ) ); ?></p>
		</div>
		<?php endif; ?>

	</div>

	<?php if ( fenix_mod( 'show_footer_watermark' ) && '' !== $fenix_wm ) : ?>
	<p class="ft-wm watch keep-case" aria-hidden="true"><span class="ft-wm-a"><?php echo esc_html( $fenix_wm_a ); ?></span><?php if ( '' !== $fenix_wm_b ) : ?> <span class="ft-wm-b"><?php echo esc_html( $fenix_wm_b ); ?></span><?php endif; ?></p>
	<?php endif; ?>

	<div class="ft-status">
		<div class="container ft-status-inner">
			<p class="ft-copy">&copy; <?php echo esc_html( wp_date( 'Y', null, $fenix_bkk ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?><?php echo '' !== trim( (string) fenix_mod( 'footer_copyright_text' ) ) ? ' · ' . esc_html( fenix_mod( 'footer_copyright_text' ) ) : ''; ?></p>
			<?php if ( '' !== $fenix_stat ) : ?>
				<p class="ft-status-text"><?php echo esc_html( $fenix_stat ); ?></p>
			<?php endif; ?>
			<?php if ( fenix_mod( 'show_footer_clock' ) ) : ?>
				<p class="ft-clock"><span class="chrome-dot" aria-hidden="true"></span><span><?php echo esc_html( fenix_mod( 'footer_clock_label' ) ); ?></span> <time data-clock-out data-tz="Asia/Bangkok" datetime="<?php echo esc_attr( wp_date( 'c', null, $fenix_bkk ) ); ?>"><?php echo esc_html( wp_date( 'H:i', null, $fenix_bkk ) ); ?></time></p>
			<?php endif; ?>
			<?php if ( '' !== $fenix_top ) : ?>
				<a class="ft-top" href="#top"><?php echo esc_html( $fenix_top ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php endif; ?>
		</div>
	</div>
</footer>
<?php endif; ?>

<?php if ( fenix_mod( 'show_mobile_nav' ) ) : ?>
	<?php
	$fenix_dock  = fenix_chrome_dock();
	$fenix_style = sprintf( '--dock-n:%d;--dock-x:%s%%', count( $fenix_dock['items'] ), number_format( $fenix_dock['x'], 3, '.', '' ) );
	?>
	<?php if ( $fenix_dock['items'] ) : ?>
<nav class="mobile-app-nav dock<?php echo $fenix_dock['active'] >= 0 ? ' is-lit' : ''; ?>" aria-label="เมนูลัดมือถือ" style="<?php echo esc_attr( $fenix_style ); ?>">
	<span class="dock-lamp" aria-hidden="true"></span>
	<div class="dock-keys">
		<?php
		foreach ( $fenix_dock['items'] as $fenix_slot => $fenix_item ) :
			$fenix_is_action = ! empty( $fenix_item['action'] );
			$fenix_is_here   = ! $fenix_is_action && (int) $fenix_slot === $fenix_dock['active'];
			$fenix_cls       = 'dock-key';
			if ( $fenix_is_action ) {
				$fenix_cls .= ' is-action' . ( ! empty( $fenix_item['line'] ) ? ' is-line' : '' );
			} elseif ( $fenix_is_here ) {
				$fenix_cls .= ' is-active';
			}
			?>
		<a class="<?php echo esc_attr( $fenix_cls ); ?>" href="<?php echo esc_url( $fenix_item['url'] ); ?>" data-slot="<?php echo esc_attr( (string) $fenix_slot ); ?>"<?php echo $fenix_is_here ? ' aria-current="page"' : ''; ?><?php echo ! empty( $fenix_item['line'] ) ? ' target="_blank" rel="noopener"' : ''; ?><?php echo $fenix_is_action ? ' data-line-pos="dock"' : ''; ?>>
			<span class="dock-cap"><?php echo fenix_icon( $fenix_item['icon'], 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="dock-label"><?php echo esc_html( $fenix_item['label'] ); ?></span>
		</a>
		<?php endforeach; ?>
	</div>
</nav>
<div class="dock-spacer" aria-hidden="true"></div>
	<?php endif; ?>
<?php endif; ?>

<?php if ( fenix_has_line_url() && fenix_mod( 'show_float_line' ) ) : ?>
<a class="float-line<?php echo fenix_mod( 'show_mobile_nav' ) ? ' float-line--dock' : ''; ?>" href="<?php echo esc_url( trim( (string) fenix_mod( 'line_url' ) ) ); ?>" target="_blank" rel="noopener" data-line-pos="float">
	<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span><?php echo esc_html( fenix_mod( 'float_line_text' ) ); ?></span>
</a>
<?php endif; ?>

<?php if ( fenix_has_line_url() && ! fenix_mod( 'show_float_line' ) ) : ?>
<a class="line-fab" href="<?php echo esc_url( trim( (string) fenix_mod( 'line_url' ) ) ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( fenix_mod( 'float_line_text' ) ); ?>" data-line-pos="fab">
	<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>

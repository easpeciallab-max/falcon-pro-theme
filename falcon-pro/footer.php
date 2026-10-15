<?php
/**
 * Footer · "Console" ของ FALCON (พื้น --black-deep)
 * 1) บล็อกติดต่อ (ปุ่มติดต่อ + OpenChat + อีเมล + QR + กล่องเตรียมข้อความแรก + เวลาตอบแชท) · fenix_chrome_launch() ใน inc/modules/chrome.php
 *    พิมพ์ที่นี่เฉพาะหน้าที่ยังไม่มีบล็อกติดต่อท้ายเนื้อหา (หน้าที่เรียก fenix_line_cta() พิมพ์ไปแล้ว → footer เริ่มที่ดัชนี ไม่ซ้ำ)
 * 2) ดัชนี: หน้าในเว็บ · คู่มือ (จาก manifest · รวมหน้า /dashboard/) · ช่องทาง · เอกสาร · ข้อมูลระบบ
 *    + แถวเล็กสำหรับสมาชิกแดชบอร์ด: ลิงก์เข้าสู่ระบบ (fenix_chrome_member_link · เปิดแท็บใหม่)
 * 3) คำเตือนความเสี่ยงฉบับเต็ม (ห้ามลบ/ย่อ) + ลิงก์ไปหน้าประกาศความเสี่ยง
 * 4) ชื่อแบรนด์ตัวใหญ่แบบเส้นขอบ (show_footer_watermark · ค่าเริ่มต้นปิด = ไม่พิมพ์ p.ft-wm)
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

/* หน้านี้มีบล็อกติดต่อท้ายเนื้อหาแล้ว → footer ต่อชิดกับบล็อกนั้น (ไม่มีเส้นคั่นและไม่เว้นช่องด้านบน) */
$fenix_joined = fenix_chrome_launch_done();

$fenix_index    = fenix_chrome_index_items();
/* หน้า /dashboard/ อยู่ในคอลัมน์คู่มือ เว้นแต่คอลัมน์หน้าในเว็บไซต์มีลิงก์นั้นแล้ว (ไม่ซ้ำสองคอลัมน์) */
$fenix_guides   = fenix_chrome_guide_items( $fenix_index );
/* ลิงก์เข้าสู่ระบบแดชบอร์ดสำหรับสมาชิกเดิม (แถวเล็กใต้ดัชนี · '' = ปิดแดชบอร์ดหรือไม่มีที่อยู่) */
$fenix_member   = function_exists( 'fenix_chrome_member_link' ) ? fenix_chrome_member_link() : '';
$fenix_g_title  = trim( (string) fenix_mod( 'nav_guide_label' ) );
$fenix_channels = fenix_chrome_channels();
$fenix_docs     = fenix_chrome_doc_items();
/* ลิงก์เปิดการ์ดคุกกี้ · ชื่อลิงก์ตั้งที่หมวดคุกกี้ (inc/modules/consent.php) */
$fenix_cookie   = ( function_exists( 'fenix_consent_link' ) && '' !== trim( (string) fenix_mod( 'consent_link_label' ) ) ) ? fenix_consent_link( array( 'echo' => false ) ) : '';
$fenix_specs    = fenix_chrome_spec_rows();

/* จำนวนคอลัมน์ของดัชนี · จำนวนคี่ = คอลัมน์ข้อมูลระบบกินเต็มแถวในโหมด 2 คอลัมน์ (ไม่เหลือคอลัมน์เดี่ยวค้างแถว) */
$fenix_cols = ( $fenix_index ? 1 : 0 ) + ( $fenix_guides ? 1 : 0 ) + ( $fenix_channels ? 1 : 0 ) + ( ( $fenix_docs || '' !== $fenix_cookie ) ? 1 : 0 ) + ( $fenix_specs ? 1 : 0 );

/* ลิงก์ต่อท้ายคำเตือนความเสี่ยง → หน้าประกาศความเสี่ยง (เมื่อเผยแพร่แล้ว และไม่ใช่หน้านั้นเอง) */
$fenix_risk_more = trim( (string) fenix_mod( 'footer_risk_more' ) );
$fenix_risk_url  = ( '' !== $fenix_risk_more && ! is_page( 'risk-disclosure' ) ) ? fenix_chrome_page_url( 'risk-disclosure' ) : '';

/* ข้อความในกล่องคำเตือน · ฉบับสั้น (footer_risk_short) ใช้ได้เฉพาะเมื่อผู้อ่านไปถึงฉบับเต็มได้ในคลิกเดียว:
   มีลิงก์ไปหน้าประกาศความเสี่ยง หรือกำลังอยู่ที่หน้านั้น · ไม่เข้าเงื่อนไข หรือเว้นช่องว่าง = คำเตือนฉบับเต็มทุกคำ (เจ้าของตัดสิน 13 ต.ค. 2026) */
$fenix_risk_short = trim( (string) fenix_mod( 'footer_risk_short' ) );
$fenix_risk_brief = '' !== $fenix_risk_short && ( '' !== $fenix_risk_url || is_page( 'risk-disclosure' ) );
$fenix_risk_text  = $fenix_risk_brief ? $fenix_risk_short : (string) fenix_mod( 'risk_text' );

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
<footer class="site-footer site-footer--dark site-footer--console<?php echo $fenix_joined ? ' site-footer--joined' : ''; ?>" id="contact">
	<div class="container ft-inner">

		<?php
		/* บล็อกติดต่อ · พิมพ์เฉพาะเมื่อหน้านี้ยังไม่มี (ตัววาดและตัวกันซ้ำอยู่ใน inc/modules/chrome.php) */
		fenix_chrome_footer_contact();
		?>

		<?php /* ft-index--spec = มีคอลัมน์ข้อมูลระบบ (รายการมีเส้นปิดท้ายของตัวเอง): chrome.css ใช้ตัดเส้นบนของแถวสมาชิกเมื่อคอลัมน์นี้กินเต็มแถวสุดท้าย ไม่ให้มีเส้นซ้อนสองเส้น */ ?>
		<nav class="ft-index<?php echo ( 1 === $fenix_cols % 2 ) ? ' ft-index--odd' : ''; ?><?php echo $fenix_specs ? ' ft-index--spec' : ''; ?>" aria-label="ลิงก์ท้ายเว็บ">
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

			<?php if ( $fenix_guides ) : ?>
			<div class="ft-col ft-col--guides">
				<?php if ( '' !== $fenix_g_title ) : ?>
					<h3 class="ft-col-title"><?php echo esc_html( $fenix_g_title ); ?></h3>
				<?php endif; ?>
				<ul class="ft-list">
					<?php foreach ( $fenix_guides as $fenix_guide ) : ?>
						<li><a href="<?php echo esc_url( $fenix_guide[1] ); ?>"<?php echo $fenix_guide[2] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $fenix_guide[0] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
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

		<?php echo $fenix_member; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_chrome_member_link ?>

		<?php if ( '' !== trim( $fenix_risk_text ) ) : ?>
		<div class="ft-risk<?php echo $fenix_risk_brief ? ' ft-risk--brief' : ''; ?>" role="note">
			<span class="ft-risk-ic"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div class="ft-risk-body">
				<?php /* ฉบับสั้นต้องมาคู่กับลิงก์ไปฉบับเต็มเสมอ (ดูเงื่อนไขด้านบน) · ห้ามตัดกล่องนี้ออก */ ?>
				<p><?php echo function_exists( 'fenix_dashboard_esc' ) ? fenix_dashboard_esc( $fenix_risk_text ) : esc_html( $fenix_risk_text ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside · span.nobr กันคำว่า ออเดอร์ ถูกตัดกลางคำ ?></p>
				<?php if ( '' !== $fenix_risk_url ) : ?>
					<a class="ft-risk-more" href="<?php echo esc_url( $fenix_risk_url ); ?>"><span><?php echo esc_html( $fenix_risk_more ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
			</div>
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
				<?php /* #top ที่ไม่มี element ชื่อนี้ = เบราว์เซอร์เลื่อนขึ้นบนสุดของหน้า (มาตรฐาน HTML) · ห้ามใส่ id="top" ให้แถบหัว: แถบหัวติดขอบบนตลอด ลิงก์จึงไม่เลื่อน */ ?>
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
			if ( ! empty( $fenix_item['feature'] ) ) {
				$fenix_cls .= ' is-feature';
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

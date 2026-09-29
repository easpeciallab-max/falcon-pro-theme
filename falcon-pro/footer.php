<?php
/**
 * Footer
 *
 * @package falcon-pro
 */

$fenix_line  = fenix_mod( 'line_url' );
$fenix_fb    = fenix_mod( 'facebook_url' );
$fenix_email = fenix_mod( 'contact_email' );
$fenix_intro = fenix_lines( fenix_mod( 'footer_tagline' ) );
$fenix_trust = fenix_lines( fenix_mod( 'footer_trust_items' ) );
$fenix_prep  = fenix_lines( fenix_mod( 'footer_prep_items' ) );
$fenix_mobile_nav = array(
	array(
		'label' => fenix_mod( 'mobile_nav_home_label' ),
		'url'   => fenix_link_url( fenix_mod( 'mobile_nav_home_url' ) ),
		'icon'  => 'home',
	),
	array(
		'label' => fenix_mod( 'mobile_nav_test_label' ),
		'url'   => fenix_link_url( fenix_mod( 'mobile_nav_test_url' ) ),
		'icon'  => 'chart',
	),
	array(
		'label' => fenix_mod( 'mobile_nav_price_label' ),
		'url'   => fenix_link_url( fenix_mod( 'mobile_nav_price_url' ) ),
		'icon'  => 'tag',
	),
	array(
		'label' => fenix_mod( 'mobile_nav_install_label' ),
		'url'   => fenix_link_url( fenix_mod( 'mobile_nav_install_url' ) ),
		'icon'  => 'download',
	),
	array(
		'label'        => fenix_mod( 'mobile_nav_line_label' ),
		'url'          => $fenix_line,
		'icon'         => 'line',
		'is_action'    => true,
		'target_blank' => true,
	),
);
?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
<?php
$fenix_openchat = fenix_mod( 'line_openchat_url' );
$fenix_specs    = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$fenix_hl = fenix_mod( 'highlight' . $i );
	if ( $fenix_hl && false !== strpos( $fenix_hl, '|' ) ) {
		$fenix_specs[] = array_map( 'trim', explode( '|', $fenix_hl, 2 ) );
	}
}
$fenix_docs = array(
	'about'           => 'เกี่ยวกับเรา',
	'privacy-policy'  => 'นโยบายความเป็นส่วนตัว',
	'terms-of-use'    => 'เงื่อนไขการใช้บริการ',
	'data-deletion'   => 'คำขอลบข้อมูล',
	'risk-disclosure' => fenix_mod( 'footer_risk_link' ),
);
?>
<footer class="site-footer site-footer--dark" id="contact">
	<div class="container">

		<div class="footer-top">
			<div class="footer-brand">
				<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> · หน้าแรก">
					<img src="<?php echo esc_url( fenix_wordmark_url( 'light' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="200" height="97" loading="lazy">
				</a>
				<?php if ( $fenix_intro ) : ?>
					<p class="footer-tagline"><?php echo esc_html( $fenix_intro[0] ); ?></p>
				<?php endif; ?>
				<?php if ( $fenix_trust ) : ?>
					<ul class="footer-trust">
						<?php foreach ( $fenix_trust as $fenix_item ) : ?>
							<li><?php echo esc_html( $fenix_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="footer-cta-box">
				<p class="footer-kicker"><?php echo esc_html( fenix_mod( 'footer_kicker' ) ); ?></p>
				<h2><?php echo esc_html( fenix_mod( 'footer_cta_title' ) ); ?></h2>
				<p><?php echo esc_html( fenix_mod( 'footer_cta_text' ) ); ?></p>
				<a class="btn btn-fire" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="footer">
					<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( fenix_mod( 'footer_line_text' ) ); ?>
				</a>
			</div>
		</div>

		<div class="footer-cols">
			<nav class="footer-col" aria-label="ดัชนีหน้า">
				<h3 class="footer-head">ดัชนีหน้า</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a></li>
					<li><a href="<?php echo esc_url( home_url( '/#how-it-works' ) ); ?>">ระบบทำงานอย่างไร</a></li>
					<li><a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">แพ็กเกจและราคา</a></li>
					<li><a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">บทความ</a></li>
					<li><a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>">คำถามที่พบบ่อย</a></li>
				</ul>
			</nav>
			<nav class="footer-col" aria-label="คู่มือ">
				<h3 class="footer-head">คู่มือ</h3>
				<ul>
					<?php foreach ( fenix_guide_links( array( 'guide', 'test' ) ) as $fenix_link ) : ?>
						<li><a href="<?php echo esc_url( $fenix_link['url'] ); ?>"><?php echo esc_html( $fenix_link['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
			<div class="footer-col">
				<h3 class="footer-head">ช่องทาง</h3>
				<ul>
					<li><a href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="footer-col">LINE Official Account</a></li>
					<?php if ( $fenix_openchat ) : ?>
						<li><a href="<?php echo esc_url( $fenix_openchat ); ?>" target="_blank" rel="noopener">LINE OpenChat</a></li>
					<?php endif; ?>
					<?php if ( $fenix_fb ) : ?>
						<li><a href="<?php echo esc_url( $fenix_fb ); ?>" target="_blank" rel="noopener"><?php echo esc_html( fenix_mod( 'footer_facebook_text' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $fenix_email ) : ?>
						<li><a class="keep-case" href="<?php echo esc_url( 'mailto:' . $fenix_email ); ?>"><?php echo esc_html( $fenix_email ); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( home_url( '/go/' ) ); ?>">หน้าลิงก์รวม</a></li>
				</ul>
			</div>
			<nav class="footer-col" aria-label="เอกสาร">
				<h3 class="footer-head">เอกสาร</h3>
				<ul>
					<?php foreach ( $fenix_docs as $fenix_slug => $fenix_label ) : ?>
						<?php
						$fenix_doc = get_page_by_path( $fenix_slug );
						if ( 'risk-disclosure' !== $fenix_slug && ( ! $fenix_doc || 'publish' !== get_post_status( $fenix_doc ) ) ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( $fenix_doc ? get_permalink( $fenix_doc ) : home_url( '/' . $fenix_slug . '/' ) ); ?>"><?php echo esc_html( $fenix_label ); ?></a></li>
					<?php endforeach; ?>
					<?php if ( fenix_mod( 'show_cookie_consent' ) ) : ?>
						<li><button type="button" class="footer-linkbtn cookie-reopen">ตั้งค่าคุกกี้</button></li>
					<?php endif; ?>
				</ul>
			</nav>
			<?php if ( $fenix_specs ) : ?>
				<div class="footer-col">
					<h3 class="footer-head">ข้อมูลระบบ</h3>
					<dl class="footer-specs">
						<?php foreach ( $fenix_specs as $fenix_spec ) : ?>
							<div><dt><?php echo esc_html( $fenix_spec[0] ); ?></dt><dd><?php echo esc_html( $fenix_spec[1] ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
				</div>
			<?php endif; ?>
		</div>

		<p class="footer-risk"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( fenix_mod( 'risk_text' ) ); ?></p>

		<div class="footer-bottom">
			<p class="footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · สงวนลิขสิทธิ์</p>
			<p class="footer-copy spaced">Trade Smarter · Live Better</p>
		</div>

	</div>
</footer>
<?php endif; ?>

<?php if ( fenix_mod( 'show_mobile_nav' ) ) : ?>
<nav class="mobile-app-nav" aria-label="เมนูลัดมือถือ">
	<?php foreach ( $fenix_mobile_nav as $fenix_item ) : ?>
	<a class="mobile-app-nav-item<?php echo ! empty( $fenix_item['is_action'] ) ? ' is-action' : ''; ?>" href="<?php echo esc_url( $fenix_item['url'] ); ?>"<?php echo ! empty( $fenix_item['target_blank'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
		<span class="mobile-app-nav-icon"><?php echo fenix_icon( $fenix_item['icon'], 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span><?php echo esc_html( $fenix_item['label'] ); ?></span>
	</a>
	<?php endforeach; ?>
</nav>
<?php endif; ?>

<?php if ( fenix_mod( 'show_float_line' ) ) : ?>
<a class="float-line" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( fenix_mod( 'float_line_text' ) ); ?>">
	<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span><?php echo esc_html( fenix_mod( 'float_line_text' ) ); ?></span>
</a>
<?php endif; ?>

<?php if ( $fenix_line && ! fenix_mod( 'show_float_line' ) ) : ?>
<a class="line-fab" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" aria-label="สอบถามทาง LINE">
	<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</a>
<?php endif; ?>

<?php if ( fenix_mod( 'show_cookie_consent' ) ) : ?>
<div class="cookie-consent" role="region" aria-label="ความยินยอมการใช้คุกกี้">
	<button type="button" class="cookie-consent-close" aria-label="ปิด"><?php echo fenix_icon( 'x', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<p class="cookie-consent-text">
		<?php echo esc_html( fenix_mod( 'cookie_consent_text' ) ); ?>
		<?php
		$fenix_privacy = fenix_published_page_url( 'privacy-policy' );
		if ( $fenix_privacy ) :
			?>
			<a class="cookie-consent-link" href="<?php echo esc_url( $fenix_privacy ); ?>">อ่านนโยบาย</a>
		<?php endif; ?>
	</p>
	<div class="cookie-consent-actions">
		<button type="button" class="btn btn-ghost btn-sm cookie-decline">ปฏิเสธ</button>
		<button type="button" class="btn btn-fire btn-sm cookie-accept">ยอมรับ</button>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>

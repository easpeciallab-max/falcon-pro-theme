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
<footer class="site-footer" id="contact">
	<div class="container">

		<div class="footer-cta">
			<div>
				<p class="footer-kicker"><?php echo esc_html( fenix_mod( 'footer_kicker' ) ); ?></p>
				<h2><?php echo esc_html( fenix_mod( 'footer_cta_title' ) ); ?></h2>
				<p><?php echo esc_html( fenix_mod( 'footer_cta_text' ) ); ?></p>
			</div>
			<a class="footer-primary" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
				<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( fenix_mod( 'footer_line_text' ) ); ?></span>
			</a>
		</div>

		<div class="footer-main">

			<div class="footer-brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img class="brand-logo" src="<?php echo esc_url( fenix_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="64" height="64">
					<span class="brand-name">FALCON <em>PRO</em><small>EA&nbsp;for&nbsp;MT5</small></span>
				</a>
				<?php if ( $fenix_intro ) : ?>
				<div class="footer-tagline">
					<?php foreach ( $fenix_intro as $fenix_paragraph ) : ?>
					<p><?php echo esc_html( $fenix_paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ( $fenix_trust ) : ?>
				<ul class="footer-trust">
					<?php foreach ( $fenix_trust as $fenix_item ) : ?>
					<li><?php echo esc_html( $fenix_item ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

			<div class="footer-prep">
				<h3 class="footer-head"><?php echo esc_html( fenix_mod( 'footer_prep_title' ) ); ?></h3>
				<p><?php echo esc_html( fenix_mod( 'footer_prep_text' ) ); ?></p>

				<?php if ( $fenix_prep ) : ?>
				<ul class="footer-prep-list">
					<?php foreach ( $fenix_prep as $fenix_item ) : ?>
					<li>
						<?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $fenix_item ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

		</div>

		<div class="footer-bottom">
			<p class="footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> สงวนลิขสิทธิ์</p>
			<?php if ( $fenix_fb || $fenix_email ) : ?>
			<div class="footer-links-mini">
				<?php if ( $fenix_fb ) : ?>
				<a href="<?php echo esc_url( $fenix_fb ); ?>" target="_blank" rel="noopener"><?php echo esc_html( fenix_mod( 'footer_facebook_text' ) ); ?></a>
				<?php endif; ?>
				<?php if ( $fenix_email ) : ?>
				<a class="keep-case" href="<?php echo esc_url( 'mailto:' . $fenix_email ); ?>"><?php echo esc_html( $fenix_email ); ?></a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<nav class="footer-legal" aria-label="ลิงก์ทางกฎหมาย">
				<?php
				foreach ( array(
					'about'          => 'เกี่ยวกับเรา',
					'privacy-policy' => 'นโยบายความเป็นส่วนตัว',
					'terms'          => 'เงื่อนไขการใช้บริการ',
				) as $fenix_slug => $fenix_label ) :
					$fenix_legal_page = get_page_by_path( $fenix_slug );
					if ( $fenix_legal_page ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $fenix_legal_page ) ); ?>"><?php echo esc_html( $fenix_label ); ?></a>
						<?php
					endif;
				endforeach;
				?>
				<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>"><?php echo esc_html( fenix_mod( 'footer_risk_link' ) ); ?></a>
			</nav>
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
		$fenix_privacy = get_page_by_path( 'privacy-policy' );
		if ( $fenix_privacy ) :
			?>
			<a class="cookie-consent-link" href="<?php echo esc_url( get_permalink( $fenix_privacy ) ); ?>">อ่านนโยบาย</a>
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

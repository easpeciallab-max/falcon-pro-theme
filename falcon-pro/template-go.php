<?php
/**
 * Template Name: FALCON · หน้ารวมลิงก์ (/go)
 *
 * หน้าลิงก์รวม (link-in-bio / ปลายทางโฆษณา) แบบหน้าเดี่ยว: ไม่มีเมนู ท้ายเว็บ แถบล่างมือถือ หรือปุ่มลอย
 * การ์ดคุกกี้ยังแสดงตามปกติเพราะพิมพ์ผ่าน wp_footer()
 * ลำดับ: ส่วนหัว → ติดต่อทีม → ขั้นตอนเริ่มใช้งาน (เลขเรียงใหม่เมื่อซ่อนขั้น) → ข้อมูลก่อนเริ่ม → โซเชียล → คำเตือนความเสี่ยง → เอกสาร
 * ข้อความ/ปุ่ม/ลิงก์ทั้งหมดแก้ได้ที่ ปรับแต่ง → หมวด "30) หน้า /go" และ "31) หน้า /go · ขั้นตอนเริ่มใช้งาน" (ตัวช่วยอยู่ใน inc/modules/go.php)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_posts() ) {
	the_post();
}

/* คำไทยที่ไม่ควรถูกตัดกลางคำในการ์ดแคบ (มีผลเฉพาะหน้านี้) */
add_filter( 'fenix_keep_words', 'fenix_go_keep_words' );

$fenix_go_title = fenix_go_mod( 'go_title' );
if ( '' === $fenix_go_title ) {
	$fenix_go_title = get_the_title();
}
$fenix_go_tagline = fenix_go_mod( 'go_sub' );
$fenix_go_badges  = fenix_lines( fenix_mod( 'go_badges' ) );
$fenix_go_tone    = function_exists( 'fenix_is_dark_mode' ) && fenix_is_dark_mode() ? 'dark' : 'light';

/* กลุ่มติดต่อทีม · LINE ผ่าน fenix_contact_button() (ไม่มีลิงก์ = ไม่มีปุ่ม, แอดมินเห็นคำแนะนำ) */
$fenix_go_line_label = fenix_go_mod( 'go_line_label' );
if ( '' === $fenix_go_line_label ) {
	$fenix_go_line_label = fenix_go_mod( 'footer_line_text' );
}
$fenix_go_line_html = '';
if ( '' !== $fenix_go_line_label ) {
	ob_start();
	fenix_contact_button(
		array(
			'text'  => $fenix_go_line_label,
			'class' => 'lh-btn lh-btn-line',
			'pos'   => 'go-top',
			'arrow' => false,
			'icon'  => true,
		)
	);
	$fenix_go_line_html = trim( (string) ob_get_clean() );
}

$fenix_go_openchat_url   = fenix_go_mod( 'line_openchat_url' );
$fenix_go_openchat_label = fenix_go_mod( 'go_openchat_label' );
if ( '' === $fenix_go_openchat_label ) {
	$fenix_go_openchat_label = fenix_go_mod( 'line_openchat_text' );
}
$fenix_go_show_openchat = '' !== $fenix_go_openchat_url && '#' !== $fenix_go_openchat_url && '' !== $fenix_go_openchat_label;
$fenix_go_help_title    = fenix_go_mod( 'go_help_title' );

/* ขั้นตอน · ขั้นที่ว่างถูกตัดออกแล้ว เลขที่แสดง = ลำดับที่เหลือ */
$fenix_go_steps       = fenix_go_steps();
$fenix_go_steps_title = str_replace( '{n}', (string) count( $fenix_go_steps ), fenix_go_mod( 'go_steps_title' ) );
$fenix_go_step_word   = fenix_go_mod( 'go_step_word' );

$fenix_go_info_title = fenix_go_mod( 'go_info_title' );
$fenix_go_info_html  = fenix_go_info_buttons();
$fenix_go_socials    = fenix_go_socials();

/* เนื้อหาเพจจากตัวแก้ไข (ปิดไว้เป็นค่าเริ่มต้น) */
$fenix_go_doc = '';
if ( fenix_mod( 'go_show_doc' ) ) {
	$fenix_go_doc = trim( (string) apply_filters( 'the_content', get_the_content() ) );
}

/* ท้ายการ์ด: กลับหน้าแรก · เอกสารที่เผยแพร่แล้ว · ตั้งค่าคุกกี้ */
$fenix_go_home_label = fenix_go_mod( 'go_home_label' );
$fenix_go_legal      = fenix_go_legal_links();
$fenix_go_cookie     = '';
$fenix_go_cookie_lbl = fenix_go_mod( 'consent_link_label' );
if ( '' !== $fenix_go_cookie_lbl ) {
	if ( function_exists( 'fenix_consent_link' ) ) {
		$fenix_go_cookie = fenix_consent_link(
			array(
				'class' => 'lh-legal-cookie',
				'echo'  => false,
			)
		);
	} else {
		/* สำรองเมื่อไม่มีโมดูลคุกกี้: ลิงก์เดิมที่ consent.js ดักคลิกเพื่อเปิดการ์ด */
		$fenix_go_cookie = '<a class="cookie-reopen lh-legal-cookie" href="#cookie-settings">' . esc_html( $fenix_go_cookie_lbl ) . '</a>';
	}
}

add_filter( 'body_class', 'fenix_go_body_class', 99 );
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<script>document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, 'js' );</script>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'link-hub-page' ); ?>>
<?php wp_body_open(); ?>

<main class="link-hub" id="main">
	<div class="lh-card lh-card--<?php echo esc_attr( $fenix_go_tone ); ?>">

		<header class="lh-brand">
			<div class="lh-logo">
				<img src="<?php echo esc_url( fenix_go_logo_url() ); ?>" alt="<?php echo esc_attr( $fenix_go_title ); ?>" width="84" height="84" decoding="async" fetchpriority="high">
			</div>

			<h1 class="lh-title"><?php echo fenix_text( $fenix_go_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></h1>

			<?php if ( '' !== $fenix_go_tagline ) : ?>
				<p class="lh-tagline"><?php echo nl2br( fenix_text( $fenix_go_tagline ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></p>
			<?php endif; ?>

			<?php if ( $fenix_go_badges ) : ?>
				<ul class="lh-badges">
					<?php foreach ( $fenix_go_badges as $fenix_go_badge ) : ?>
						<li><?php echo esc_html( $fenix_go_badge ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</header>

		<?php if ( '' !== $fenix_go_line_html || $fenix_go_show_openchat ) : ?>
			<section class="lh-group lh-group--help"<?php echo '' !== $fenix_go_help_title ? ' aria-labelledby="lh-help-title"' : ''; ?>>
				<?php if ( '' !== $fenix_go_help_title ) : ?>
					<h2 class="lh-section-title" id="lh-help-title"><?php echo fenix_text( $fenix_go_help_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></h2>
				<?php endif; ?>

				<?php echo $fenix_go_line_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_contact_button ?>

				<?php if ( $fenix_go_show_openchat ) : ?>
					<a class="lh-btn lh-btn-openchat" href="<?php echo esc_url( $fenix_go_openchat_url ); ?>" target="_blank" rel="noopener" data-line-pos="go-openchat" data-contact="openchat">
						<span class="lh-ic"><?php echo fenix_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="lh-lbl"><?php echo fenix_text( $fenix_go_openchat_label ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></span>
					</a>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php if ( $fenix_go_steps ) : ?>
			<section class="lh-group lh-journey"<?php echo '' !== $fenix_go_steps_title ? ' aria-labelledby="lh-steps-title"' : ''; ?>>
				<?php if ( '' !== $fenix_go_steps_title ) : ?>
					<h2 class="lh-steps-title" id="lh-steps-title"><?php echo fenix_text( $fenix_go_steps_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></h2>
				<?php endif; ?>
				<ol class="lh-steps">
					<?php foreach ( $fenix_go_steps as $fenix_go_idx => $fenix_go_step ) : ?>
						<?php
						$fenix_go_n     = (int) $fenix_go_step['n'];
						$fenix_go_num   = $fenix_go_idx + 1;
						$fenix_go_badge = fenix_go_mod( 'go_step' . $fenix_go_n . '_badge' );
						$fenix_go_desc  = fenix_go_mod( 'go_step' . $fenix_go_n . '_desc' );
						$fenix_go_note  = fenix_go_mod( 'go_step' . $fenix_go_n . '_note' );
						?>
						<li class="lh-step lh-step--<?php echo esc_attr( $fenix_go_step['key'] ); ?>">
							<span class="lh-step-num" aria-hidden="true"><?php echo esc_html( (string) $fenix_go_num ); ?></span>
							<div class="lh-step-head">
								<h3 class="lh-step-title">
									<span class="lh-step-name"><?php if ( '' !== $fenix_go_step_word ) : ?><span class="screen-reader-text"><?php echo esc_html( $fenix_go_step_word . ' ' . $fenix_go_num . ' ' ); ?></span><?php endif; ?><?php echo fenix_text( fenix_go_mod( 'go_step' . $fenix_go_n . '_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></span>
									<?php if ( '' !== $fenix_go_badge ) : ?>
										<span class="lh-step-badge"><?php echo esc_html( $fenix_go_badge ); ?></span>
									<?php endif; ?>
								</h3>
								<?php if ( '' !== $fenix_go_desc ) : ?>
									<p class="lh-step-desc"><?php echo fenix_text( $fenix_go_desc ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></p>
								<?php endif; ?>
								<?php if ( '' !== $fenix_go_note ) : ?>
									<p class="lh-step-note"><?php echo fenix_text( $fenix_go_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></p>
								<?php endif; ?>
							</div>
							<div class="lh-step-body">
								<?php echo $fenix_go_step['body']; // phpcs:ignore WordPress.Security.EscapeOutput -- built with escaping in fenix_go_steps() ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( '' !== trim( $fenix_go_info_html ) ) : ?>
			<section class="lh-group lh-group--info"<?php echo '' !== $fenix_go_info_title ? ' aria-labelledby="lh-info-title"' : ''; ?>>
				<?php if ( '' !== $fenix_go_info_title ) : ?>
					<h2 class="lh-section-title" id="lh-info-title"><?php echo fenix_text( $fenix_go_info_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></h2>
				<?php endif; ?>
				<?php echo $fenix_go_info_html; // phpcs:ignore WordPress.Security.EscapeOutput -- built with escaping in fenix_go_info_buttons() ?>
			</section>
		<?php endif; ?>

		<?php if ( $fenix_go_socials ) : ?>
			<div class="lh-socials">
				<?php foreach ( $fenix_go_socials as $fenix_go_social ) : ?>
					<a class="lh-soc lh-soc--<?php echo esc_attr( $fenix_go_social['name'] ); ?>" href="<?php echo esc_url( $fenix_go_social['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $fenix_go_social['label'] ); ?>">
						<?php echo fenix_icon( $fenix_go_social['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $fenix_go_doc ) : ?>
			<section class="lh-doc entry-content">
				<?php echo $fenix_go_doc; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content filters ?>
			</section>
		<?php endif; ?>

		<?php if ( '' !== trim( (string) fenix_mod( 'risk_text' ) ) ) : ?>
			<p class="lh-note"><?php echo fenix_icon( 'warn', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_text( fenix_mod( 'risk_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_text ?></span></p>
		<?php endif; ?>

		<?php if ( '' !== $fenix_go_home_label || $fenix_go_legal || '' !== $fenix_go_cookie ) : ?>
			<p class="lh-legal">
				<?php if ( '' !== $fenix_go_home_label ) : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $fenix_go_home_label ); ?></a>
				<?php endif; ?>
				<?php foreach ( $fenix_go_legal as $fenix_go_doc_link ) : ?>
					<a href="<?php echo esc_url( $fenix_go_doc_link['url'] ); ?>"><?php echo esc_html( $fenix_go_doc_link['label'] ); ?></a>
				<?php endforeach; ?>
				<?php echo $fenix_go_cookie; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in fenix_consent_link ?>
			</p>
		<?php endif; ?>

	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>

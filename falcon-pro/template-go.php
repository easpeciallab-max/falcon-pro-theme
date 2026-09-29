<?php
/**
 * Template Name: FALCON · หน้ารวมลิงก์ (/go)
 *
 * หน้าลิงก์รวมแบบ link-in-bio สำหรับใส่ใน LINE OA / Facebook / TikTok · ข้อความแก้ได้ที่ ปรับแต่ง → หน้า /go
 *
 * @package falcon-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$fenix_line     = fenix_mod( 'line_url' );
$fenix_openchat = fenix_mod( 'line_openchat_url' );
$fenix_signup   = fenix_mod( 'broker_signup_url' );

$fenix_steps = array(
	array(
		'title' => fenix_mod( 'go_step1' ),
		'url'   => $fenix_signup ? $fenix_signup : home_url( '/open-mt5-account/' ),
		'icon'  => 'user',
		'ext'   => (bool) $fenix_signup,
	),
	array(
		'title' => fenix_mod( 'go_step2' ),
		'url'   => home_url( '/open-mt5-account/' ),
		'icon'  => 'book',
	),
	array(
		'title' => fenix_mod( 'go_step3' ),
		'url'   => home_url( '/mt5-login/' ),
		'icon'  => 'download',
		'dl'    => true,
	),
	array(
		'title' => fenix_mod( 'go_step4' ),
		'url'   => $fenix_line,
		'icon'  => 'line',
		'ext'   => true,
		'pos'   => 'go-request-ea',
	),
	array(
		'title' => fenix_mod( 'go_step5' ),
		'url'   => home_url( '/how-to-install/' ),
		'icon'  => 'gear',
	),
	array(
		'title' => fenix_mod( 'go_step6' ),
		'url'   => home_url( '/vps-windows/' ),
		'icon'  => 'server',
	),
);
?>

<main id="main" class="go-page">
	<section class="go-hero">
		<div class="container container-narrow">
			<div class="go-card reveal">
				<img class="go-wordmark" src="<?php echo esc_url( fenix_wordmark_url( 'light' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="260" height="126">
				<h1 class="go-title"><?php the_title(); ?></h1>
				<p class="go-sub"><?php echo esc_html( fenix_mod( 'go_sub' ) ); ?></p>

				<div class="go-primary">
					<a class="btn btn-fire btn-lg btn-block" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="go-main">
						<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'footer_line_text' ) ); ?>
					</a>
					<?php if ( $fenix_openchat ) : ?>
						<a class="btn btn-ghost-dark btn-lg btn-block" href="<?php echo esc_url( $fenix_openchat ); ?>" target="_blank" rel="noopener" data-line-pos="go-openchat">
							<?php echo fenix_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( fenix_mod( 'line_openchat_text' ) ); ?>
						</a>
					<?php endif; ?>
				</div>

				<h2 class="go-head"><?php echo esc_html( fenix_mod( 'go_steps_title' ) ); ?></h2>
				<ol class="go-steps">
					<?php foreach ( $fenix_steps as $fenix_i => $fenix_step ) : ?>
						<?php
						if ( ! $fenix_step['title'] ) {
							continue;
						}
						?>
						<li>
							<a class="go-step" href="<?php echo esc_url( $fenix_step['url'] ); ?>"<?php echo ! empty( $fenix_step['ext'] ) ? ' target="_blank" rel="noopener"' : ''; ?><?php echo ! empty( $fenix_step['pos'] ) ? ' data-line-pos="' . esc_attr( $fenix_step['pos'] ) . '"' : ''; ?>>
								<span class="mono"><?php echo esc_html( sprintf( '%02d', $fenix_i + 1 ) ); ?></span>
								<?php echo fenix_icon( $fenix_step['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span class="go-step-text"><?php echo esc_html( $fenix_step['title'] ); ?></span>
								<?php echo fenix_icon( ! empty( $fenix_step['ext'] ) ? 'external' : 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
							<?php if ( ! empty( $fenix_step['dl'] ) ) : ?>
								<div class="go-dl">
									<?php
									foreach ( array(
										'windows' => array( 'Windows', fenix_mod( 'mt5_dl_windows' ) ),
										'android' => array( 'Android', fenix_mod( 'mt5_dl_android' ) ),
										'apple'   => array( 'iPhone', fenix_mod( 'mt5_dl_ios' ) ),
									) as $fenix_os => $fenix_dl ) :
										if ( ! $fenix_dl[1] ) {
											continue;
										}
										?>
										<a href="<?php echo esc_url( $fenix_dl[1] ); ?>" target="_blank" rel="noopener"><?php echo fenix_icon( $fenix_os, 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $fenix_dl[0] ); ?></a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>

				<nav class="go-links" aria-label="ข้อมูลเพิ่มเติม">
					<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">แพ็กเกจและราคา</a>
					<a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">บทความ</a>
					<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">ประกาศความเสี่ยง</a>
					<?php if ( fenix_published_page_url( 'privacy-policy' ) ) : ?>
						<a href="<?php echo esc_url( fenix_published_page_url( 'privacy-policy' ) ); ?>">นโยบายความเป็นส่วนตัว</a>
					<?php endif; ?>
				</nav>

				<p class="go-risk"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( fenix_mod( 'hero_note' ) ); ?></p>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();

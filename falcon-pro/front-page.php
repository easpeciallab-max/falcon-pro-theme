<?php
/**
 * Front page · FALCON PRO EA (รวมเนื้อหา "รู้จัก FALCON PRO" + hub นำทาง)
 * ลำดับ section: hero → specs → about → pain → how → features → gallery → tests → install → pricing → hub → faq → blog → risk → cta
 *
 * @package falcon-pro
 */

get_header();

if ( fenix_has_elementor_content() && fenix_uses_elementor_page_template() ) :
	?>
	<main id="main" class="elementor-page-shell elementor-page-shell--front">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'elementor-entry' ); ?>>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</main>
	<?php
	get_footer();
	return;
endif;

$fenix_line = fenix_mod( 'line_url' );
?>

<main id="main">

<?php /* ============ HERO (ขาว·ดำตัดทแยง ตามแบนเนอร์) ============ */ ?>
<?php if ( fenix_mod( 'show_hero' ) ) : ?>
<section class="hero hero--split" id="hero">
	<div class="hero-split-bg" aria-hidden="true"></div>

	<div class="container hero-inner">
		<div class="hero-copy reveal">
			<span class="badge"><?php echo esc_html( fenix_mod( 'hero_badge' ) ); ?></span>
			<h1 class="hero-title">
				<span class="hero-brand"><?php echo esc_html( fenix_mod( 'hero_title' ) ); ?></span>
				<span class="hero-headline">
					<?php echo esc_html( fenix_mod( 'hero_subtitle' ) ); ?>
					<?php if ( fenix_mod( 'hero_subtitle_em' ) ) : ?>
						<em><?php echo esc_html( fenix_mod( 'hero_subtitle_em' ) ); ?></em>
					<?php endif; ?>
				</span>
			</h1>
			<p class="hero-desc"><?php echo esc_html( fenix_mod( 'hero_desc' ) ); ?></p>

			<ul class="hero-points">
				<?php
				$fenix_hp_icons = array( 'gear', 'bars', 'shield' );
				for ( $i = 1; $i <= 3; $i++ ) :
					$fenix_hp_title = fenix_mod( 'hero_point' . $i . '_title' );
					if ( ! $fenix_hp_title ) {
						continue;
					}
					?>
					<li>
						<?php echo fenix_icon_badge( $fenix_hp_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span>
							<strong><?php echo esc_html( $fenix_hp_title ); ?></strong>
							<?php echo esc_html( fenix_mod( 'hero_point' . $i . '_desc' ) ); ?>
						</span>
					</li>
				<?php endfor; ?>
			</ul>

			<div class="hero-actions">
				<a class="btn btn-fire btn-lg" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="hero">
					<?php echo fenix_icon( 'line', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( fenix_mod( 'hero_btn1_text' ) ); ?>
					<?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
				<a class="btn btn-ghost btn-lg" href="<?php echo esc_url( fenix_link_url( fenix_mod( 'hero_btn2_url' ) ) ); ?>">
					<?php echo esc_html( fenix_mod( 'hero_btn2_text' ) ); ?>
				</a>
			</div>

			<p class="hero-note">
				<?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'hero_note' ) ); ?>
			</p>
		</div>

		<div class="hero-visual reveal">
			<?php $fenix_hero_img = fenix_mod( 'hero_image' ); ?>
			<?php if ( $fenix_hero_img ) : ?>
				<figure class="hero-frame">
					<img src="<?php echo esc_url( $fenix_hero_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'hero_title' ) . ' ' . fenix_mod( 'hero_subtitle' ) . ' ' . fenix_mod( 'hero_subtitle_em' ) ); ?>" loading="eager" width="1254" height="1254">
				</figure>
			<?php else : ?>
				<?php get_template_part( 'inc/parts/ea-panel' ); ?>
			<?php endif; ?>
			<?php if ( fenix_mod( 'hero_tagline' ) ) : ?>
				<p class="hero-tagline spaced"><?php echo esc_html( fenix_mod( 'hero_tagline' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* รับประกันว่ามี h1 เสมอ แม้ปิด hero (สำคัญต่อ SEO/screen reader) */ ?>
<?php if ( ! fenix_mod( 'show_hero' ) ) : ?>
<h1 class="sr-only"><?php echo esc_html( fenix_mod( 'hero_title' ) ? fenix_mod( 'hero_title' ) : get_bloginfo( 'name' ) ); ?></h1>
<?php endif; ?>

<?php /* ============ แถบข้อมูลระบบ (label|value) ============ */ ?>
<?php if ( fenix_mod( 'show_highlight' ) ) : ?>
<section class="highlight-bar spec-bar" aria-label="ข้อมูลระบบโดยสรุป">
	<div class="container">
		<dl class="spec-list reveal">
			<?php
			for ( $i = 1; $i <= 4; $i++ ) :
				$fenix_hl = fenix_mod( 'highlight' . $i );
				if ( ! $fenix_hl ) {
					continue;
				}
				$fenix_hl_parts = array_map( 'trim', explode( '|', $fenix_hl, 2 ) );
				?>
				<div class="spec-item">
					<?php if ( isset( $fenix_hl_parts[1] ) ) : ?>
						<dt><?php echo esc_html( $fenix_hl_parts[0] ); ?></dt>
						<dd><?php echo esc_html( $fenix_hl_parts[1] ); ?></dd>
					<?php else : ?>
						<dd><?php echo esc_html( $fenix_hl_parts[0] ); ?></dd>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</dl>
		<?php if ( fenix_mod( 'highlight_note' ) ) : ?>
			<p class="spec-note"><?php echo esc_html( fenix_mod( 'highlight_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ แถบสถานะเคลื่อนไหว ============ */ ?>
<?php
$fenix_live_items = fenix_lines( fenix_mod( 'live_status_items' ) );
?>
<?php if ( fenix_mod( 'show_live_status' ) && fenix_mod( 'live_status_kicker' ) ) : ?>
<section class="live-strip" aria-label="<?php echo esc_attr( fenix_mod( 'live_status_kicker' ) ); ?>">
	<div class="container">
		<div class="live-strip-inner reveal">
			<span class="live-strip-label">
				<i aria-hidden="true"></i>
				<?php echo esc_html( fenix_mod( 'live_status_kicker' ) ); ?>
			</span>
			<?php if ( ! empty( $fenix_live_items ) ) : ?>
				<div class="live-track-wrap">
					<div class="live-track">
						<?php for ( $round = 0; $round < 2; $round++ ) : ?>
							<?php foreach ( $fenix_live_items as $fenix_live_item ) : ?>
								<span class="live-item">
									<i aria-hidden="true"></i>
									<?php echo esc_html( $fenix_live_item ); ?>
								</span>
							<?php endforeach; ?>
						<?php endfor; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ FALCON PRO EA คืออะไร ============ */ ?>
<?php if ( fenix_mod( 'show_about' ) ) : ?>
<section class="section" id="about">
	<div class="container">
		<div class="about-layout">
			<div class="about-copy reveal">
				<span class="kicker">About</span>
				<h2><?php echo esc_html( fenix_mod( 'about_title' ) ); ?></h2>
				<div class="about-panel">
					<?php
					foreach ( preg_split( '/\n\s*\n/', (string) fenix_mod( 'about_text' ) ) as $fenix_para ) {
						$fenix_para = trim( $fenix_para );
						if ( '' === $fenix_para ) {
							continue;
						}
						echo '<p>' . nl2br( esc_html( $fenix_para ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</div>
				<?php $fenix_about_points = fenix_lines( fenix_mod( 'about_points' ) ); ?>
				<?php if ( ! empty( $fenix_about_points ) ) : ?>
					<ul class="about-points">
						<?php foreach ( $fenix_about_points as $fenix_point ) : ?>
							<?php $fenix_ap = array_map( 'trim', explode( '|', $fenix_point, 2 ) ); ?>
							<li>
								<strong><?php echo esc_html( $fenix_ap[0] ); ?></strong>
								<?php if ( isset( $fenix_ap[1] ) ) : ?>
									<span><?php echo esc_html( $fenix_ap[1] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php $fenix_about_img = fenix_mod( 'about_image' ); ?>
			<?php if ( $fenix_about_img ) : ?>
				<figure class="about-figure reveal">
					<a href="<?php echo esc_url( $fenix_about_img ); ?>" class="lightbox" data-caption="<?php echo esc_attr( fenix_mod( 'about_image_caption' ) ); ?>">
						<img src="<?php echo esc_url( $fenix_about_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'about_title' ) ); ?>" loading="lazy" width="1254" height="1254">
					</a>
					<?php if ( fenix_mod( 'about_image_caption' ) ) : ?>
						<figcaption><?php echo esc_html( fenix_mod( 'about_image_caption' ) ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ปัญหา ============ */ ?>
<?php if ( fenix_mod( 'show_pain' ) ) : ?>
<section class="section section-alt" id="pain">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">The Problem</span>
			<h2><?php echo esc_html( fenix_mod( 'pain_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'pain_subtitle' ) ); ?></p>
		</div>
		<div class="grid grid-4">
			<?php
			for ( $i = 1; $i <= 4; $i++ ) :
				$p_title = fenix_mod( 'pain' . $i . '_title' );
				$p_desc  = fenix_mod( 'pain' . $i . '_desc' );
				if ( ! $p_title && ! $p_desc ) {
					continue;
				}
				?>
				<article class="card pain-card pain-card-<?php echo esc_attr( $i ); ?> reveal">
					<span class="card-num">0<?php echo esc_html( (string) $i ); ?></span>
					<h3><?php echo esc_html( $p_title ); ?></h3>
					<p><?php echo esc_html( $p_desc ); ?></p>
				</article>
			<?php endfor; ?>
		</div>
		<?php if ( fenix_mod( 'pain_answer' ) ) : ?>
			<div class="pain-answer reveal">
				<?php echo fenix_icon_badge( 'robot', 'green' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p><?php echo esc_html( fenix_mod( 'pain_answer' ) ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ทีมงาน / ใครอยู่เบื้องหลัง ============ */ ?>
<?php if ( fenix_mod( 'show_team' ) ) : ?>
<section class="section team-section" id="team">
	<div class="container container-narrow">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'team_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'team_title' ) ); ?></h2>
		</div>
		<?php $fenix_team_img = fenix_mod( 'team_img' ); ?>
		<div class="team-layout<?php echo $fenix_team_img ? '' : ' team-layout--solo'; ?> reveal">
			<?php if ( $fenix_team_img ) : ?>
				<figure class="team-photo">
					<img src="<?php echo esc_url( $fenix_team_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'team_title' ) ); ?>" loading="lazy">
				</figure>
			<?php endif; ?>
			<div class="team-body">
				<?php
				foreach ( preg_split( '/\n\s*\n/', (string) fenix_mod( 'team_text' ) ) as $fenix_para ) {
					$fenix_para = trim( $fenix_para );
					if ( '' === $fenix_para ) {
						continue;
					}
					echo '<p>' . nl2br( esc_html( $fenix_para ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				$fenix_team_points = fenix_lines( fenix_mod( 'team_points' ) );
				?>
				<?php if ( ! empty( $fenix_team_points ) ) : ?>
					<ul class="team-points">
						<?php foreach ( $fenix_team_points as $fenix_point ) : ?>
							<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_point ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ Control Center ============ */ ?>
<?php if ( fenix_mod( 'show_control_center' ) ) : ?>
<section class="section control-section" id="system">
	<div class="container">
		<div class="control-layout">
			<div class="control-copy reveal">
				<span class="kicker"><?php echo esc_html( fenix_mod( 'control_kicker' ) ); ?></span>
				<h2><?php echo esc_html( fenix_mod( 'control_title' ) ); ?></h2>
				<p><?php echo esc_html( fenix_mod( 'control_subtitle' ) ); ?></p>
			</div>
			<div class="control-panel reveal" aria-label="<?php echo esc_attr( fenix_mod( 'control_panel_title' ) ); ?>">
				<div class="control-panel-top">
					<div>
						<span class="control-eyebrow">
							<i aria-hidden="true"></i>
							<?php echo esc_html( fenix_mod( 'control_panel_title' ) ); ?>
						</span>
						<strong><?php echo esc_html( fenix_mod( 'control_panel_status' ) ); ?></strong>
					</div>
					<span class="control-badge"><?php echo esc_html( fenix_mod( 'control_badge' ) ); ?></span>
				</div>
				<p class="control-panel-text"><?php echo esc_html( fenix_mod( 'control_panel_text' ) ); ?></p>

				<div class="control-metrics">
					<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
						<div class="control-metric">
							<span><?php echo esc_html( fenix_mod( 'control_metric' . $i . '_label' ) ); ?></span>
							<strong><?php echo esc_html( fenix_mod( 'control_metric' . $i . '_value' ) ); ?></strong>
						</div>
					<?php endfor; ?>
				</div>

				<div class="control-checklist">
					<h3><?php echo esc_html( fenix_mod( 'control_list_title' ) ); ?></h3>
					<ul>
						<?php foreach ( fenix_lines( fenix_mod( 'control_list_items' ) ) as $fenix_item ) : ?>
							<li>
								<?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span><?php echo esc_html( $fenix_item ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ระบบทำงานอย่างไร (4 ขั้น + รายการเตรียมตัว) ============ */ ?>
<?php if ( fenix_mod( 'show_steps' ) ) : ?>
<section class="section section-dark steps-section" id="how-it-works">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'steps_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'steps_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'steps_subtitle' ) ); ?></p>
		</div>
		<div class="how-layout">
			<ol class="steps steps-timeline">
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
					<?php
					$step_title = fenix_mod( 'step' . $i . '_title' );
					$step_desc  = fenix_mod( 'step' . $i . '_desc' );
					if ( ! $step_title && ! $step_desc ) {
						continue;
					}
					?>
					<li class="step reveal">
						<span class="step-num">0<?php echo esc_html( (string) $i ); ?></span>
						<h3><?php echo esc_html( $step_title ); ?></h3>
						<p><?php echo esc_html( $step_desc ); ?></p>
					</li>
				<?php endfor; ?>
			</ol>
			<?php $fenix_checklist = fenix_lines( fenix_mod( 'steps_checklist' ) ); ?>
			<?php if ( ! empty( $fenix_checklist ) ) : ?>
				<aside class="how-checklist reveal">
					<h3><?php echo esc_html( fenix_mod( 'steps_checklist_title' ) ); ?></h3>
					<ul class="check-list">
						<?php foreach ( $fenix_checklist as $fenix_item ) : ?>
							<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ จุดเด่น ============ */ ?>
<?php if ( fenix_mod( 'show_features' ) ) : ?>
<section class="section" id="features">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Key Features</span>
			<h2><?php echo esc_html( fenix_mod( 'features_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'features_subtitle' ) ); ?></p>
		</div>
		<div class="grid grid-3 features-grid">
			<?php
			$fenix_feat_icons = array( 'robot', 'candles', 'layout', 'shield', 'moon', 'headset' );
			for ( $i = 1; $i <= 6; $i++ ) :
				$f_title = fenix_mod( 'feat' . $i . '_title' );
				$f_desc  = fenix_mod( 'feat' . $i . '_desc' );
				if ( ! $f_title && ! $f_desc ) {
					continue;
				}
				?>
				<article class="card feat-card feat-card-<?php echo esc_attr( $i ); ?> reveal">
					<?php echo fenix_icon_badge( $fenix_feat_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="card-label">Module 0<?php echo esc_html( (string) $i ); ?></span>
					<h3><?php echo esc_html( $f_title ); ?></h3>
					<p><?php echo esc_html( $f_desc ); ?></p>
				</article>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ แกลเลอรีภาพ (คลิกดูภาพใหญ่) ============ */ ?>
<?php if ( fenix_mod( 'show_gallery' ) ) : ?>
<?php
$fenix_shots = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$g_img = fenix_mod( 'gallery_img' . $i );
	if ( $g_img ) {
		$fenix_shots[] = array(
			'src' => $g_img,
			'cap' => fenix_mod( 'gallery_cap' . $i ),
		);
	}
}
?>
<?php if ( ! empty( $fenix_shots ) ) : ?>
<section class="section section-alt" id="screenshots">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Screens</span>
			<h2><?php echo esc_html( fenix_mod( 'gallery_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'gallery_subtitle' ) ); ?></p>
		</div>
		<div class="gallery-grid">
			<?php foreach ( $fenix_shots as $fenix_n => $fenix_shot ) : ?>
				<figure class="gallery-item reveal">
					<a class="lightbox" href="<?php echo esc_url( $fenix_shot['src'] ); ?>" data-caption="<?php echo esc_attr( $fenix_shot['cap'] ); ?>" data-group="home-gallery">
						<img src="<?php echo esc_url( $fenix_shot['src'] ); ?>" alt="<?php echo esc_attr( $fenix_shot['cap'] ? $fenix_shot['cap'] : fenix_mod( 'gallery_title' ) ); ?>" loading="lazy" width="1254" height="1254">
					</a>
					<?php if ( $fenix_shot['cap'] ) : ?>
						<figcaption><span class="mono"><?php echo esc_html( sprintf( '%02d', $fenix_n + 1 ) ); ?></span> <?php echo esc_html( $fenix_shot['cap'] ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
		<?php if ( fenix_mod( 'gallery_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'gallery_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>
<?php endif; ?>

<?php /* ============ Mid CTA (ทัก LINE คั่นกลางหน้า) ============ */ ?>
<?php if ( fenix_mod( 'show_mid_cta' ) ) : ?>
<section class="mid-cta" aria-label="ทัก LINE ปรึกษา">
	<div class="container">
		<div class="mid-cta-inner reveal">
			<div class="mid-cta-copy">
				<h2><?php echo esc_html( fenix_mod( 'mid_cta_title' ) ); ?></h2>
				<p><?php echo esc_html( fenix_mod( 'mid_cta_text' ) ); ?></p>
			</div>
			<a class="btn btn-fire" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="mid-cta">
				<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				ทัก LINE ปรึกษาก่อนตัดสินใจ
			</a>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ วิธีทดสอบ Backtest / Forward (แท็บ) ============ */ ?>
<?php if ( fenix_mod( 'show_tests' ) ) : ?>
<section class="section" id="tests">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'tests_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'tests_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'tests_subtitle' ) ); ?></p>
		</div>
		<div class="tabs reveal" data-tabs>
			<div class="tab-list" role="tablist" aria-label="<?php echo esc_attr( fenix_mod( 'tests_title' ) ); ?>">
				<button class="tab" role="tab" id="tab-bt" aria-controls="panel-bt" aria-selected="true" type="button"><span class="mono">01</span> Backtest</button>
				<button class="tab" role="tab" id="tab-fw" aria-controls="panel-fw" aria-selected="false" tabindex="-1" type="button"><span class="mono">02</span> Forward Test</button>
			</div>
			<?php
			$fenix_tabs = array(
				'bt' => array( 'tests_bt', '/backtest/', 'ดูรายละเอียด Backtest' ),
				'fw' => array( 'tests_fw', '/forward-test/', 'ดูรายละเอียด Forward Test' ),
			);
			foreach ( $fenix_tabs as $fenix_tab_id => $fenix_tab ) :
				$fenix_tab_img = fenix_mod( $fenix_tab[0] . '_img' );
				?>
				<div class="tab-panel" role="tabpanel" id="panel-<?php echo esc_attr( $fenix_tab_id ); ?>" aria-labelledby="tab-<?php echo esc_attr( $fenix_tab_id ); ?>"<?php echo 'bt' === $fenix_tab_id ? '' : ' hidden'; ?>>
					<div class="tab-panel-grid<?php echo $fenix_tab_img ? '' : ' tab-panel-grid--solo'; ?>">
						<?php if ( $fenix_tab_img ) : ?>
							<figure class="tab-figure">
								<a class="lightbox" href="<?php echo esc_url( $fenix_tab_img ); ?>" data-caption="ภาพประกอบ ไม่ใช่ผลการทดสอบของ FALCON PRO EA">
									<img src="<?php echo esc_url( $fenix_tab_img ); ?>" alt="<?php echo esc_attr( fenix_mod( $fenix_tab[0] . '_title' ) ); ?>" loading="lazy" width="1254" height="1254">
								</a>
								<figcaption>ภาพประกอบ ไม่ใช่ผลการทดสอบของ FALCON PRO EA</figcaption>
							</figure>
						<?php endif; ?>
						<div class="tab-copy">
							<h3><?php echo esc_html( fenix_mod( $fenix_tab[0] . '_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( $fenix_tab[0] . '_text' ) ); ?></p>
							<a class="btn btn-ghost" href="<?php echo esc_url( home_url( $fenix_tab[1] ) ); ?>"><?php echo esc_html( $fenix_tab[2] ); ?> <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( fenix_mod( 'tests_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'tests_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ผลการทดสอบ (แสดงเมื่อกรอกตัวเลขจริงแล้วเท่านั้น) ============ */ ?>
<?php
$fenix_perf_stats = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$fenix_sv = fenix_mod( 'stat' . $i . '_value' );
	if ( ! fenix_is_placeholder( $fenix_sv ) ) {
		$fenix_perf_stats[] = array(
			'label' => fenix_mod( 'stat' . $i . '_label' ),
			'value' => $fenix_sv,
		);
	}
}
$fenix_perf_img     = fenix_mod( 'perf_image' );
$fenix_verified_url = fenix_mod( 'verified_link_url' );
$fenix_has_perf     = ! empty( $fenix_perf_stats ) || $fenix_perf_img || $fenix_verified_url;
?>
<?php if ( fenix_mod( 'show_perf' ) && $fenix_has_perf ) : ?>
<section class="section section-alt performance-section" id="performance">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'perf_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'perf_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'perf_subtitle' ) ); ?></p>
		</div>

		<div class="performance-layout<?php echo $fenix_perf_img ? '' : ' performance-layout--solo'; ?>">
			<?php if ( ! empty( $fenix_perf_stats ) ) : ?>
				<div class="stats-grid stats-grid--home reveal">
					<?php foreach ( $fenix_perf_stats as $fenix_stat ) : ?>
						<div class="stat">
							<span class="stat-label"><?php echo esc_html( $fenix_stat['label'] ); ?></span>
							<span class="stat-value"><?php echo esc_html( $fenix_stat['value'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $fenix_perf_img ) : ?>
				<figure class="perf-figure perf-figure--home reveal">
					<img src="<?php echo esc_url( $fenix_perf_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'perf_image_caption' ) ); ?>" loading="lazy">
					<?php if ( fenix_mod( 'perf_image_caption' ) ) : ?>
						<figcaption><?php echo esc_html( fenix_mod( 'perf_image_caption' ) ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>

		<?php if ( $fenix_verified_url ) : ?>
			<p class="perf-verified reveal">
				<a class="btn btn-ghost" href="<?php echo esc_url( $fenix_verified_url ); ?>" target="_blank" rel="noopener">
					<?php echo fenix_icon( 'pulse', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( fenix_mod( 'verified_link_label' ) ); ?>
				</a>
			</p>
		<?php endif; ?>

		<?php if ( fenix_mod( 'perf_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'perf_note' ) ); ?></p>
		<?php endif; ?>

		<?php if ( fenix_mod( 'perf_disclaimer' ) ) : ?>
			<div class="disclaimer reveal">
				<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p><?php echo esc_html( fenix_mod( 'perf_disclaimer' ) ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ติดตั้งย่อ 3 ขั้น ============ */ ?>
<?php if ( fenix_mod( 'show_install_home' ) ) : ?>
<section class="section section-alt" id="install">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Install</span>
			<h2><?php echo esc_html( fenix_mod( 'install_home_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'install_home_sub' ) ); ?></p>
		</div>
		<ol class="install-mini">
			<?php
			$fenix_ih_icons = array( 'download', 'server', 'gear' );
			for ( $i = 1; $i <= 3; $i++ ) :
				$fenix_ih_title = fenix_mod( 'ih_step' . $i . '_title' );
				if ( ! $fenix_ih_title ) {
					continue;
				}
				?>
				<li class="card install-mini-card reveal">
					<div class="install-mini-top">
						<?php echo fenix_icon_badge( $fenix_ih_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="card-label">ขั้น 0<?php echo esc_html( (string) $i ); ?></span>
					</div>
					<h3><?php echo esc_html( $fenix_ih_title ); ?></h3>
					<p><?php echo esc_html( fenix_mod( 'ih_step' . $i . '_desc' ) ); ?></p>
				</li>
			<?php endfor; ?>
		</ol>
		<div class="install-mini-foot reveal">
			<?php if ( fenix_mod( 'install_home_note' ) ) : ?>
				<p><?php echo fenix_icon( 'phone', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( fenix_mod( 'install_home_note' ) ); ?></p>
			<?php endif; ?>
			<a class="btn btn-dark" href="<?php echo esc_url( home_url( '/how-to-install/' ) ); ?>">อ่านคู่มือติดตั้งฉบับเต็ม <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ เหมาะกับใคร ============ */ ?>
<?php if ( fenix_mod( 'show_fit' ) ) : ?>
<section class="section" id="fit">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Honest Check</span>
			<h2><?php echo esc_html( fenix_mod( 'fit_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'fit_subtitle' ) ); ?></p>
		</div>
		<div class="fit-grid">
			<div class="fit-card fit-good reveal">
				<h3><?php echo fenix_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'fit_good_title' ) ); ?></h3>
				<ul>
					<?php foreach ( fenix_lines( fenix_mod( 'fit_good_items' ) ) as $fenix_item ) : ?>
						<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="fit-card fit-bad reveal">
				<h3><?php echo fenix_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'fit_bad_title' ) ); ?></h3>
				<ul>
					<?php foreach ( fenix_lines( fenix_mod( 'fit_bad_items' ) ) as $fenix_item ) : ?>
						<li><?php echo fenix_icon( 'x', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ รีวิวลูกค้า (เปิดเมื่อมีรีวิวจริง) ============ */ ?>
<?php if ( fenix_mod( 'show_reviews' ) ) : ?>
<section class="section reviews-section" id="reviews">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Reviews</span>
			<h2><?php echo esc_html( fenix_mod( 'reviews_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'reviews_subtitle' ) ); ?></p>
		</div>
		<div class="grid grid-3 reviews-grid">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php
				$fenix_rev_text = fenix_mod( 'rev' . $i . '_text' );
				$fenix_rev_name = fenix_mod( 'rev' . $i . '_name' );
				if ( ! $fenix_rev_text ) {
					continue;
				}
				?>
				<figure class="card review-card reveal">
					<span class="card-icon"><?php echo fenix_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<blockquote><?php echo esc_html( $fenix_rev_text ); ?></blockquote>
					<?php if ( $fenix_rev_name ) : ?>
						<figcaption><?php echo esc_html( $fenix_rev_name ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ความมั่นใจก่อนเริ่ม ============ */ ?>
<?php $fenix_assurance_items = fenix_lines( fenix_mod( 'assurance_items' ) ); ?>
<?php if ( fenix_mod( 'show_assurance' ) && ! empty( $fenix_assurance_items ) ) : ?>
<section class="section section-alt assurance-section" id="assurance">
	<div class="container container-narrow">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'assurance_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'assurance_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'assurance_subtitle' ) ); ?></p>
		</div>
		<ul class="assurance-list reveal">
			<?php foreach ( $fenix_assurance_items as $fenix_item ) : ?>
				<li>
					<span class="assurance-ic"><?php echo fenix_icon( 'shield', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><?php echo esc_html( $fenix_item ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>

<?php /* ============ Pricing teaser หน้าแรก ============ */ ?>
<?php if ( fenix_mod( 'show_pricing_home' ) ) : ?>
<section class="section section-alt pricing-teaser" id="pricing">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Pricing</span>
			<h2><?php echo esc_html( fenix_mod( 'pricing_home_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'pricing_home_sub' ) ); ?></p>
		</div>
		<?php $fenix_pmode = fenix_mod( 'pricing_mode' ); ?>
		<div class="grid grid-3 price-teaser-grid">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php
				$fenix_pk_name = fenix_mod( 'pkg' . $i . '_name' );
				if ( ! $fenix_pk_name ) {
					continue;
				}
				$fenix_pk_tag      = fenix_mod( 'pkg' . $i . '_tag' );
				$fenix_pk_price    = fenix_mod( 'pkg' . $i . '_price' );
				$fenix_pk_period   = fenix_mod( 'pkg' . $i . '_period' );
				$fenix_pk_featured = fenix_mod( 'pkg' . $i . '_featured' );
				?>
				<article class="card price-teaser-card<?php echo $fenix_pk_featured ? ' is-featured' : ''; ?> reveal">
					<?php if ( $fenix_pk_featured ) : ?><span class="price-flag">แนะนำ</span><?php endif; ?>
					<span class="card-label">0<?php echo esc_html( (string) $i ); ?></span>
					<h3><?php echo esc_html( $fenix_pk_name ); ?></h3>
					<?php if ( $fenix_pk_tag ) : ?><p class="price-tag"><?php echo esc_html( $fenix_pk_tag ); ?></p><?php endif; ?>
					<?php if ( 'price' === $fenix_pmode && $fenix_pk_price ) : ?>
						<p class="price-amt"><span><?php echo esc_html( $fenix_pk_price ); ?></span> <?php echo esc_html( $fenix_pk_period ); ?></p>
					<?php else : ?>
						<p class="price-amt price-amt--contact">สอบถามราคาทาง LINE</p>
					<?php endif; ?>
					<?php $fenix_pk_feats = array_slice( fenix_lines( fenix_mod( 'pkg' . $i . '_features' ) ), 0, 4 ); ?>
					<?php if ( ! empty( $fenix_pk_feats ) ) : ?>
						<ul class="price-feats price-feats--mini">
							<?php foreach ( $fenix_pk_feats as $fenix_item ) : ?>
								<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<a class="btn <?php echo $fenix_pk_featured ? 'btn-fire' : 'btn-ghost'; ?> btn-block" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="pricing-home-<?php echo esc_attr( (string) $i ); ?>">
						<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'pricing_btn_text' ) ); ?>
					</a>
				</article>
			<?php endfor; ?>
		</div>
		<p class="sec-note reveal"><a class="price-all-link" href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">ดูตารางเปรียบเทียบแพ็กเกจทั้งหมด <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
	</div>
</section>
<?php endif; ?>

<?php /* ============ การ์ดนำทาง (HUB) ============ */ ?>
<section class="section" id="explore">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Explore</span>
			<h2><?php echo esc_html( fenix_mod( 'home_cards_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'home_cards_sub' ) ); ?></p>
		</div>
		<div class="grid grid-3 hub-grid">
			<?php
			$fenix_card_icons = array( 'candles', 'pulse', 'tag', 'download', 'shield', 'server' );
			for ( $i = 1; $i <= 6; $i++ ) :
				$c_title = fenix_mod( 'card' . $i . '_title' );
				$c_desc  = fenix_mod( 'card' . $i . '_desc' );
				$c_url   = fenix_mod( 'card' . $i . '_url' );
				if ( ! $c_title ) {
					continue;
				}
				?>
				<a class="card hub-card hub-card-<?php echo esc_attr( $i ); ?> reveal" href="<?php echo esc_url( fenix_link_url( $c_url ) ); ?>">
					<?php echo fenix_icon_badge( $fenix_card_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h3><?php echo esc_html( $c_title ); ?></h3>
					<p><?php echo esc_html( $c_desc ); ?></p>
					<span class="hub-go">ดูรายละเอียด <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</a>
			<?php endfor; ?>
		</div>
	</div>
</section>

<?php /* ============ FAQ (ย่อ) ============ */ ?>
<?php if ( fenix_mod( 'show_faq' ) ) : ?>
<section class="section section-alt" id="faq">
	<div class="container container-narrow">
		<div class="sec-head reveal">
			<span class="kicker">FAQ</span>
			<h2><?php echo esc_html( fenix_mod( 'faq_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'faq_subtitle' ) ); ?></p>
		</div>
		<div class="faq-list reveal">
			<?php
			$fenix_first = true;
			for ( $i = 1; $i <= 10; $i++ ) :
				$q = fenix_mod( 'faq' . $i . '_q' );
				$a = fenix_mod( 'faq' . $i . '_a' );
				if ( ! $q || ! $a ) {
					continue;
				}
				?>
				<details class="faq-item" <?php echo $fenix_first ? 'open' : ''; ?>>
					<summary>
						<span><?php echo esc_html( $q ); ?></span>
						<i class="faq-plus" aria-hidden="true"></i>
					</summary>
					<div class="faq-answer"><p><?php echo nl2br( esc_html( $a ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p></div>
				</details>
				<?php
				$fenix_first = false;
			endfor;
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ บทความล่าสุด (ซ่อนอัตโนมัติเมื่อไม่มีบทความ) ============ */ ?>
<?php
$fenix_blog_q = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<?php if ( fenix_mod( 'show_blog' ) && $fenix_blog_q->have_posts() ) : ?>
<section class="section blog-section" id="articles">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'blog_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'blog_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'blog_subtitle' ) ); ?></p>
		</div>
		<div class="posts-grid reveal">
			<?php
			while ( $fenix_blog_q->have_posts() ) :
				$fenix_blog_q->the_post();
				fenix_post_card();
			endwhile;
			?>
		</div>
		<?php
		$fenix_posts_page = (int) get_option( 'page_for_posts' );
		if ( $fenix_posts_page ) :
			?>
			<p class="sec-note reveal"><a class="price-all-link" href="<?php echo esc_url( get_permalink( $fenix_posts_page ) ); ?>"><?php echo esc_html( fenix_mod( 'blog_all_label' ) ); ?> <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>

<?php /* ============ คำเตือนความเสี่ยง (ย่อ) ============ */ ?>
<?php if ( fenix_mod( 'show_risk' ) ) : ?>
<section class="section section-risk" id="risk">
	<div class="container container-narrow">
		<div class="risk-box reveal">
			<span class="risk-kicker">Notice · ประกาศความเสี่ยง</span>
			<h2>
				<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'risk_title' ) ); ?>
			</h2>
			<p><?php echo nl2br( esc_html( fenix_mod( 'risk_text' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<a class="risk-more" href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">อ่านประกาศความเสี่ยงฉบับเต็ม <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ CTA ============ */ ?>
<?php if ( fenix_mod( 'show_cta' ) ) : ?>
<section class="section cta cta--dark" id="cta">
	<div class="container container-narrow">
		<div class="cta-inner reveal">
			<img class="cta-wordmark" src="<?php echo esc_url( fenix_wordmark_url( 'light' ) ); ?>" alt="" width="240" height="116" loading="lazy">
			<h2><?php echo esc_html( fenix_mod( 'cta_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'cta_subtitle' ) ); ?></p>
			<a class="btn btn-fire btn-lg" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" data-line-pos="home-cta">
				<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'cta_btn_text' ) ); ?>
				<?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<p class="spaced cta-tagline">Trade Smarter · Live Better</p>
		</div>
	</div>
</section>
<?php endif; ?>

</main>

<?php
get_footer();

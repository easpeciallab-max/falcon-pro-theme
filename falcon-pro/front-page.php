<?php
/**
 * Front page · FALCON PRO EA (รวมเนื้อหา "รู้จัก FALCON PRO" + hub นำทาง)
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

<?php /* ============ HERO ============ */ ?>
<?php if ( fenix_mod( 'show_hero' ) ) : ?>
<section class="hero" id="hero">
	<div class="hero-bg" aria-hidden="true">
		<span class="ember ember-a"></span>
		<span class="ember ember-b"></span>
		<svg class="hero-candles" viewBox="0 0 560 300" fill="none" preserveAspectRatio="xMidYMax meet">
			<g stroke="currentColor" stroke-width="2">
				<line x1="40"  y1="150" x2="40"  y2="280"/><rect x="28"  y="180" width="24" height="70"  rx="3"/>
				<line x1="110" y1="120" x2="110" y2="262"/><rect x="98"  y="150" width="24" height="80"  rx="3"/>
				<line x1="180" y1="140" x2="180" y2="250"/><rect x="168" y="168" width="24" height="56"  rx="3"/>
				<line x1="250" y1="80"  x2="250" y2="225"/><rect x="238" y="108" width="24" height="86"  rx="3"/>
				<line x1="320" y1="60"  x2="320" y2="190"/><rect x="308" y="86"  width="24" height="76"  rx="3"/>
				<line x1="390" y1="78"  x2="390" y2="170"/><rect x="378" y="100" width="24" height="48"  rx="3"/>
				<line x1="460" y1="20"  x2="460" y2="150"/><rect x="448" y="44"  width="24" height="80"  rx="3"/>
				<line x1="530" y1="0"   x2="530" y2="110"/><rect x="518" y="20"  width="24" height="64"  rx="3"/>
			</g>
		</svg>
	</div>

	<div class="container hero-inner">
		<div class="hero-copy reveal">
			<span class="badge">
				<?php echo fenix_icon( 'flame', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'hero_badge' ) ); ?>
			</span>
			<h1 class="hero-title"><?php echo esc_html( fenix_mod( 'hero_title' ) ); ?></h1>
			<p class="hero-sub"><?php echo esc_html( fenix_mod( 'hero_subtitle' ) ); ?></p>
			<p class="hero-desc"><?php echo esc_html( fenix_mod( 'hero_desc' ) ); ?></p>

			<div class="hero-actions">
				<a class="btn btn-fire" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
					<?php echo fenix_icon( 'line', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( fenix_mod( 'hero_btn1_text' ) ); ?>
				</a>
				<a class="btn btn-ghost" href="#about">
					<?php echo esc_html( fenix_mod( 'hero_btn2_text' ) ); ?>
					<?php echo fenix_icon( 'arrow', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>

			<p class="hero-note">
				<?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'hero_note' ) ); ?>
			</p>
		</div>

		<?php
		$fenix_hero_img = fenix_mod( 'hero_image' );
		$fenix_is_photo = ! empty( $fenix_hero_img );
		$fenix_hero_src = $fenix_is_photo ? $fenix_hero_img : get_template_directory_uri() . '/assets/img/logo.png';
		?>
		<div class="hero-visual <?php echo $fenix_is_photo ? 'hero-visual--photo' : 'hero-visual--orb'; ?> reveal">
			<?php if ( $fenix_is_photo ) : ?>
				<figure class="hero-frame">
					<img src="<?php echo esc_url( $fenix_hero_src ); ?>" alt="<?php echo esc_attr( fenix_mod( 'hero_title' ) ); ?>" loading="eager">
				</figure>
			<?php else : ?>
				<div class="hero-orb">
					<span class="orb-ring orb-ring-a"></span>
					<span class="orb-ring orb-ring-b"></span>
					<img src="<?php echo esc_url( $fenix_hero_src ); ?>" alt="<?php echo esc_attr( fenix_mod( 'hero_title' ) ); ?>" loading="eager" width="420" height="420">
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* รับประกันว่ามี h1 เสมอ แม้ปิด hero (สำคัญต่อ SEO/screen reader) */ ?>
<?php if ( ! fenix_mod( 'show_hero' ) ) : ?>
<h1 class="sr-only"><?php echo esc_html( fenix_mod( 'hero_title' ) ? fenix_mod( 'hero_title' ) : get_bloginfo( 'name' ) ); ?></h1>
<?php endif; ?>

<?php /* ============ แถบไฮไลต์ ============ */ ?>
<?php if ( fenix_mod( 'show_highlight' ) ) : ?>
<section class="highlight-bar" aria-label="จุดเด่นโดยสรุป">
	<div class="container">
		<ul class="highlight-list reveal">
			<?php
			$fenix_hl_icons = array( 'cpu', 'pulse', 'gauge', 'headset' );
			for ( $i = 1; $i <= 4; $i++ ) :
				$hl = fenix_mod( 'highlight' . $i );
				if ( ! $hl ) {
					continue;
				}
				?>
				<li>
					<span class="hl-icon"><?php echo fenix_icon( $fenix_hl_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><?php echo esc_html( $hl ); ?></span>
				</li>
			<?php endfor; ?>
		</ul>
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

<?php /* ============ ปัญหา ============ */ ?>
<?php if ( fenix_mod( 'show_pain' ) ) : ?>
<section class="section" id="pain">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">The Problem</span>
			<h2><?php echo esc_html( fenix_mod( 'pain_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'pain_subtitle' ) ); ?></p>
		</div>
		<div class="grid grid-4">
			<?php
			$fenix_pain_icons = array( 'pulse', 'clock', 'gauge', 'flag' );
			for ( $i = 1; $i <= 4; $i++ ) :
				$p_title = fenix_mod( 'pain' . $i . '_title' );
				$p_desc  = fenix_mod( 'pain' . $i . '_desc' );
				if ( ! $p_title && ! $p_desc ) {
					continue;
				}
				?>
				<article class="card pain-card pain-card-<?php echo esc_attr( $i ); ?> reveal">
					<span class="card-icon"><?php echo fenix_icon( $fenix_pain_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h3><?php echo esc_html( $p_title ); ?></h3>
					<p><?php echo esc_html( $p_desc ); ?></p>
				</article>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ FALCON PRO EA คืออะไร ============ */ ?>
<?php if ( fenix_mod( 'show_about' ) ) : ?>
<section class="section section-alt" id="about">
	<div class="container container-narrow">
		<div class="sec-head reveal">
			<span class="kicker">About</span>
			<h2><?php echo esc_html( fenix_mod( 'about_title' ) ); ?></h2>
		</div>
		<div class="about-panel reveal">
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
			$fenix_feat_icons = array( 'cpu', 'candles', 'layout', 'shield', 'moon', 'headset' );
			for ( $i = 1; $i <= 6; $i++ ) :
				$f_title = fenix_mod( 'feat' . $i . '_title' );
				$f_desc  = fenix_mod( 'feat' . $i . '_desc' );
				if ( ! $f_title && ! $f_desc ) {
					continue;
				}
				?>
				<article class="card feat-card feat-card-<?php echo esc_attr( $i ); ?> reveal">
					<span class="card-icon"><?php echo fenix_icon( $fenix_feat_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h3><?php echo esc_html( $f_title ); ?></h3>
					<p><?php echo esc_html( $f_desc ); ?></p>
				</article>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ภาพ Dashboard ============ */ ?>
<?php if ( fenix_mod( 'show_gallery' ) ) : ?>
<section class="section section-alt" id="screenshots">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Screenshots</span>
			<h2><?php echo esc_html( fenix_mod( 'gallery_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'gallery_subtitle' ) ); ?></p>
		</div>
		<?php
		$fenix_shots = array();
		for ( $i = 1; $i <= 4; $i++ ) {
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
			<div class="shots-grid">
				<?php foreach ( $fenix_shots as $fenix_shot ) : ?>
					<figure class="shot reveal">
						<img src="<?php echo esc_url( $fenix_shot['src'] ); ?>" alt="<?php echo esc_attr( $fenix_shot['cap'] ); ?>" loading="lazy">
						<?php if ( $fenix_shot['cap'] ) : ?>
							<figcaption><?php echo esc_html( $fenix_shot['cap'] ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="shots-grid">
				<?php for ( $i = 1; $i <= 2; $i++ ) : ?>
					<figure class="shot shot-placeholder reveal">
						<div class="shot-empty">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.png' ); ?>" alt="" loading="lazy" width="120" height="120">
							<span class="chip">ภาพประกอบ</span>
							<p>อัปโหลดภาพ Dashboard ได้ที่หน้า ปรับแต่ง → ภาพ Dashboard / ระบบจริง</p>
						</div>
						<figcaption><?php echo esc_html( fenix_mod( 'gallery_cap' . $i ) ); ?></figcaption>
					</figure>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
		<?php if ( fenix_mod( 'gallery_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( fenix_mod( 'gallery_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ขั้นตอนใช้งาน ============ */ ?>
<?php if ( fenix_mod( 'show_steps' ) ) : ?>
<section class="section steps-section" id="how-it-works">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker"><?php echo esc_html( fenix_mod( 'steps_kicker' ) ); ?></span>
			<h2><?php echo esc_html( fenix_mod( 'steps_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'steps_subtitle' ) ); ?></p>
		</div>
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
	</div>
</section>
<?php endif; ?>

<?php /* ============ Mid CTA (ทัก LINE คั่นกลางหน้า) ============ */ ?>
<?php if ( fenix_mod( 'show_mid_cta' ) ) : ?>
<section class="mid-cta" aria-label="ทัก LINE ปรึกษา">
	<div class="container container-narrow">
		<div class="mid-cta-inner reveal">
			<div class="mid-cta-copy">
				<h2><?php echo esc_html( fenix_mod( 'mid_cta_title' ) ); ?></h2>
				<p><?php echo esc_html( fenix_mod( 'mid_cta_text' ) ); ?></p>
			</div>
			<a class="btn btn-line" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
				<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				ทัก LINE ปรึกษาก่อนตัดสินใจ
			</a>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ผลการทดสอบ ============ */ ?>
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
<section class="section pricing-teaser" id="pricing">
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
					<h3><?php echo esc_html( $fenix_pk_name ); ?></h3>
					<?php if ( $fenix_pk_tag ) : ?><p class="price-tag"><?php echo esc_html( $fenix_pk_tag ); ?></p><?php endif; ?>
					<?php if ( 'price' === $fenix_pmode && $fenix_pk_price ) : ?>
						<p class="price-amt"><span><?php echo esc_html( $fenix_pk_price ); ?></span> <?php echo esc_html( $fenix_pk_period ); ?></p>
					<?php else : ?>
						<p class="price-amt price-amt--contact">สอบถามราคาทาง LINE</p>
					<?php endif; ?>
					<a class="btn btn-line btn-block" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
						<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'pricing_btn_text' ) ); ?>
					</a>
				</article>
			<?php endfor; ?>
		</div>
		<p class="sec-note reveal"><a class="price-all-link" href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">ดูรายละเอียดแพ็กเกจทั้งหมด <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
	</div>
</section>
<?php endif; ?>

<?php /* ============ การ์ดนำทาง (HUB) ============ */ ?>
<section class="section section-alt" id="explore">
	<div class="container">
		<div class="sec-head reveal">
			<span class="kicker">Explore</span>
			<h2><?php echo esc_html( fenix_mod( 'home_cards_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'home_cards_sub' ) ); ?></p>
		</div>
		<div class="grid grid-3 hub-grid">
			<?php
			$fenix_card_icons = array( 'candles', 'pulse', 'layout', 'download', 'shield' );
			for ( $i = 1; $i <= 5; $i++ ) :
				$c_title = fenix_mod( 'card' . $i . '_title' );
				$c_desc  = fenix_mod( 'card' . $i . '_desc' );
				$c_url   = fenix_mod( 'card' . $i . '_url' );
				if ( ! $c_title ) {
					continue;
				}
				?>
				<a class="card hub-card hub-card-<?php echo esc_attr( $i ); ?> reveal" href="<?php echo esc_url( fenix_link_url( $c_url ) ); ?>">
					<span class="card-icon"><?php echo fenix_icon( $fenix_card_icons[ $i - 1 ] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
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
<section class="section" id="faq">
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
<section class="section section-alt blog-section" id="articles">
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
			<h2>
				<?php echo fenix_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'risk_title' ) ); ?>
			</h2>
			<p><?php echo nl2br( esc_html( fenix_mod( 'risk_text' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<a class="risk-more" href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">อ่านคำเตือนความเสี่ยงฉบับเต็ม <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ CTA ============ */ ?>
<?php if ( fenix_mod( 'show_cta' ) ) : ?>
<section class="section cta" id="cta">
	<div class="cta-bg" aria-hidden="true"><span class="ember ember-c"></span></div>
	<div class="container container-narrow">
		<div class="cta-inner reveal">
			<img class="cta-logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.png' ); ?>" alt="" width="96" height="96" loading="lazy">
			<h2><?php echo esc_html( fenix_mod( 'cta_title' ) ); ?></h2>
			<p><?php echo esc_html( fenix_mod( 'cta_subtitle' ) ); ?></p>
			<a class="btn btn-line btn-lg" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
				<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo esc_html( fenix_mod( 'cta_btn_text' ) ); ?>
			</a>
		</div>
	</div>
</section>
<?php endif; ?>

</main>

<?php
get_footer();

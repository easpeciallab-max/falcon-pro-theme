<?php
/**
 * Front page · FALCON PRO EA
 *
 * โครงหน้าแบบ hero + บทมีเลข · หน้าตาแบบ FALCON (Noto Sans Thai, มุมโค้ง, ปุ่ม pill, ic-badge, เขียว FALCON บนพื้นเข้ม #172125)
 * hero → what → pain → how → features → tests → [compare] → install (เริ่มต้น 6 ขั้น + อุปกรณ์) → dashboard → [ready] → pricing → faq → risk
 * บทในวงเล็บเหลี่ยมพิมพ์โดยโมดูลเสริม inc/modules/homeplus.php ผ่าน fenix_homeplus_section()
 * บท dashboard = หัวบทของหน้าแรก + บล็อกแนะนำของโมดูล dashboard (fenix_dashboard_block( 'home' ) · ปิดแดชบอร์ดที่หมวด 1.1 = ไม่มีบทนี้)
 * ลิงก์และปุ่มไปแดชบอร์ดทุกจุดมาจาก fenix_dashboard_button() (เปิดแท็บใหม่ · นับคลิก dashboard_click) · ปุ่มหลักของ Hero ยังเป็นปุ่มติดต่อ LINE
 * - หัวข้อยืนเดี่ยวเป็นค่าเริ่มต้น: ไม่มีรางเลขบท ไม่มีป้ายเล็กหรือบรรทัดชื่อแบรนด์เหนือ H1 ไม่มี "01 / 12 · ป้ายบท" เหนือ H2 และไม่มีป้ายเลขบนการ์ดจุดเด่น
 *   เปิดกลับได้ที่ตัวปรับแต่ง: home_show_rail · hero_badge · home_hero_show_brand (หมวด 2) และ show_kickers (หมวด 1 · ใช้ทั้งเว็บ)
 * - เลขบทนับเฉพาะบทที่เปิดอยู่ (show_*) และมีเนื้อหา · รางเลขบท (≥1240px) / แถบความคืบหน้า (<1240px) อ่านจาก data-chapter (พิมพ์เฉพาะเมื่อเปิดราง)
 * - ข้อความทุกคำมาจาก setting (ค่าเริ่มต้นใน inc/modules/home.php และ homeplus.php) · escape ทุก output
 * - ลิงก์ไปหน้าในเว็บผ่าน fenix_home_link(): หน้าที่ยังไม่เผยแพร่ = ไม่พิมพ์ลิงก์ (ไม่มีลิงก์ 404)
 * - ไม่มี JS ก็อ่านได้ครบ: หน้าจอบันทึกพิมพ์ข้อความเต็มไว้แล้ว + มีรายการขั้นตอนคู่แฝด · แท็บใช้ radio · FAQ ใช้ details · เช็กลิสต์เป็นรายการธรรมดา
 * - ไม่มีตัวเลขผลเทรด ไม่มีเวลา/ราคาในหน้าจอบันทึก · คำเตือนความเสี่ยงแสดงเต็ม ไม่ตัด
 * - ตัวเลขผล Backtest อยู่ที่หน้า /backtest/ เท่านั้น: บทการทดสอบชี้ไปหน้านั้นและแดชบอร์ดเมื่อเนื้อหาของหน้านั้นมีตารางผลแล้ว (fenix_home_backtest_live)
 * - CTA ท้ายหน้าเดิม (#cta) ย้ายไปเป็นแถวติดต่อใน footer (โมดูล chrome)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

$fenix_chapters = fenix_home_chapters();
$fenix_ch_count = count( $fenix_chapters );
$fenix_ch_total = sprintf( '%02d', $fenix_ch_count );
$fenix_fig      = trim( (string) fenix_mod( 'home_fig_label' ) );
/* หัวข้อยืนเดี่ยวเป็นค่าเริ่มต้น: รางเลขบท (home_show_rail) และคำเล็กเหนือหัวข้อ (show_kickers · หมวด 1) ปิดอยู่
 * ปิดราง = ไม่มี nav.hm-rail / .hm-progress และไม่มี data-chapter(s) ในหน้า · ปิดคำเล็ก = ไม่มีเลขบท ป้ายบท และป้ายเลขบนการ์ดจุดเด่น */
$fenix_rail     = fenix_home_rail_on();
$fenix_labels   = fenix_show_kickers();
?>

<main id="main" class="home-v3"<?php echo $fenix_rail ? ' data-chapters="' . esc_attr( (string) $fenix_ch_count ) . '"' : ''; ?>>

<?php /* ============ รางเลขบท (จอกว้าง) + แถบความคืบหน้า (จอเล็ก) · ค่าเริ่มต้นปิด (ไม่พิมพ์อะไร) · เปิดแล้วไม่มี JS แสดง 00 นิ่ง ลิงก์ยังใช้ได้ ============ */ ?>
<?php if ( $fenix_rail ) : ?>
<nav class="hm-rail" aria-label="<?php echo esc_attr( fenix_mod( 'home_rail_label' ) ); ?>">
	<div class="hm-rail-inner">
		<p class="hm-rail-counter" aria-hidden="true"><span class="hm-rail-n" data-rail-n>00</span><span class="hm-rail-total">/ <?php echo esc_html( $fenix_ch_total ); ?></span></p>
		<span class="hm-rail-track" aria-hidden="true"><i class="hm-rail-fill"></i></span>
		<ol class="hm-rail-list">
			<?php foreach ( $fenix_chapters as $fenix_ch_id => $fenix_ch ) : ?>
				<li><a href="#<?php echo esc_attr( $fenix_ch_id ); ?>" data-rail-link="<?php echo esc_attr( $fenix_ch['n'] ); ?>"><span class="hm-rail-num" aria-hidden="true"><?php echo esc_html( $fenix_ch['n'] ); ?></span><span class="hm-rail-tip"><?php echo esc_html( $fenix_ch['label'] ); ?></span></a></li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
<div class="hm-progress" aria-hidden="true"><i class="hm-progress-fill"></i></div>
<?php endif; ?>

<?php /* ============ HERO · H1 เดียว + ปุ่มติดต่อเดียว + ลิงก์ไปบท how + คำเตือน + แถบข้อมูลระบบ ============ */ ?>
<?php if ( fenix_mod( 'show_hero' ) ) : ?>
	<?php
	$fenix_hero_badge = trim( (string) fenix_mod( 'hero_badge' ) ); /* ว่างเป็นค่าเริ่มต้น = ไม่มีป้ายเหนือ H1 */
	$fenix_hero_brand = trim( (string) fenix_mod( 'hero_title' ) );
	/* บรรทัดชื่อแบรนด์เหนือหัวข้อหลัก: ค่าเริ่มต้นปิด · ปิด = ชื่อแบรนด์ยังอยู่ใน H1 แบบ .sr-only (ข้อความของ H1 เท่าเดิม แต่ไม่แสดงบนจอ) */
	$fenix_hero_line  = (bool) fenix_mod( 'home_hero_show_brand' );
	$fenix_hero_sub   = trim( (string) fenix_mod( 'hero_subtitle' ) );
	$fenix_hero_em    = trim( (string) fenix_mod( 'hero_subtitle_em' ) );
	$fenix_hero_desc  = trim( (string) fenix_mod( 'hero_desc' ) );
	$fenix_hero_link  = trim( (string) fenix_mod( 'hero_btn2_text' ) );
	$fenix_hero_url   = fenix_home_hero_link( fenix_mod( 'hero_btn2_url' ) ); /* '' = บทปลายทางปิดอยู่ หรือหน้าปลายทางยังไม่เผยแพร่ */
	$fenix_hero_note  = trim( (string) fenix_mod( 'hero_note' ) );
	$fenix_hero_img   = trim( (string) fenix_mod( 'hero_image' ) );
	$fenix_specs      = fenix_home_spec_items();
	$fenix_spec_note  = trim( (string) fenix_mod( 'home_hero_spec_note' ) );
	/* ลิงก์เข้าชมแดชบอร์ดใน Hero แสดงเมื่อบทแดชบอร์ดอยู่บนหน้านี้ด้วย (บทนั้นมีหมายเหตุว่าข้อมูลในโหมดเยี่ยมชมเป็นตัวอย่าง ไม่ใช่ผลการเทรด) */
	$fenix_hero_dash  = fenix_mod( 'home_hero_show_dash' ) && isset( $fenix_chapters['dashboard'] ) && function_exists( 'fenix_dashboard_button' );
	?>
<section class="hm-hero<?php echo esc_attr( fenix_home_tone( 'hero' ) ); ?>" id="hero"<?php echo $fenix_rail ? ' data-chapter="00"' : ''; ?> aria-labelledby="hm-hero-title">
	<div class="hm-hero-main">
		<div class="hm-hero-bg" aria-hidden="true"></div>
		<div class="container hm-hero-grid">
			<div class="hm-hero-copy">
				<?php if ( '' !== $fenix_hero_badge ) : ?>
					<p class="hm-hero-badge"><i aria-hidden="true"></i><?php echo esc_html( $fenix_hero_badge ); ?></p>
				<?php endif; ?>
				<h1 class="hm-hero-title" id="hm-hero-title">
					<?php if ( '' !== $fenix_hero_brand ) : ?>
						<span class="<?php echo $fenix_hero_line ? 'hm-hero-brand' : 'sr-only'; ?>"><?php echo esc_html( $fenix_hero_brand ); ?></span>
					<?php endif; ?>
					<span class="hm-hero-headline"><?php echo fenix_home_text( $fenix_hero_sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?><?php if ( '' !== $fenix_hero_em ) : ?> <em><?php echo esc_html( $fenix_hero_em ); ?></em><?php endif; ?></span>
				</h1>
				<?php if ( '' !== $fenix_hero_desc ) : ?>
					<p class="hm-hero-desc"><?php echo fenix_home_text( $fenix_hero_desc ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
				<?php endif; ?>
				<div class="hm-hero-actions">
					<?php
					fenix_contact_button(
						array(
							'text'  => fenix_mod( 'hero_btn1_text' ),
							'class' => 'btn btn-fire btn-lg',
							'pos'   => 'home-hero',
						)
					);
					?>
					<?php if ( '' !== $fenix_hero_link && '' !== $fenix_hero_url ) : ?>
						<a class="hm-textlink" href="<?php echo esc_url( $fenix_hero_url ); ?>"><?php echo esc_html( $fenix_hero_link ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<?php endif; ?>
					<?php
					/* ลิงก์ข้อความเล็กไปโหมดเยี่ยมชมของแดชบอร์ด · อยู่หลังปุ่มติดต่อเสมอ (ไม่ดันปุ่ม LINE ลงบนจอมือถือ) · ปิดแดชบอร์ด = ไม่พิมพ์อะไร */
					if ( $fenix_hero_dash ) {
						echo fenix_dashboard_button( // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_button
							'visit',
							array(
								'class' => 'hm-textlink hm-hero-dash',
								'pos'   => 'home-hero',
								'icon'  => false,
							)
						);
					}
					?>
				</div>
				<?php if ( '' !== $fenix_hero_note ) : ?>
					<p class="hm-hero-note"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_home_text( $fenix_hero_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
				<?php endif; ?>
			</div>

			<div class="hm-hero-visual">
				<?php if ( '' !== $fenix_hero_img ) : ?>
					<figure class="hm-hero-frame">
						<img src="<?php echo esc_url( $fenix_hero_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'home_hero_img_alt' ) ); ?>" width="1000" height="1000" loading="eager" decoding="async">
					</figure>
				<?php else : ?>
					<?php get_template_part( 'inc/parts/ea-panel' ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $fenix_specs ) ) : ?>
		<div class="hm-hero-spec">
			<div class="container">
				<dl class="hm-spec" style="--spec-n:<?php echo (int) min( 4, count( $fenix_specs ) ); ?>">
					<?php foreach ( $fenix_specs as $fenix_spec ) : ?>
						<div class="hm-spec-item">
							<dt><?php echo esc_html( $fenix_spec['label'] ); ?></dt>
							<dd><?php echo fenix_home_text( $fenix_spec['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
				<?php if ( '' !== $fenix_spec_note ) : ?>
					<p class="sr-only"><?php echo esc_html( $fenix_spec_note ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
<?php else : /* ให้มี h1 เสมอ แม้ปิด hero (SEO / โปรแกรมอ่านหน้าจอ) */ ?>
	<?php $fenix_h1 = trim( fenix_mod( 'hero_title' ) . ' ' . fenix_mod( 'hero_subtitle' ) . ' ' . fenix_mod( 'hero_subtitle_em' ) ); ?>
<h1 class="sr-only"><?php echo esc_html( '' !== $fenix_h1 ? $fenix_h1 : get_bloginfo( 'name' ) ); ?></h1>
<?php endif; ?>

<?php /* ============ 01 · ระบบคืออะไร (#what + #about) · ย่อหน้า + ตารางข้อมูลระบบ + กล่องหลักการ + ภาพ 01 ============ */ ?>
<?php if ( isset( $fenix_chapters['what'] ) ) : ?>
	<?php
	$fenix_sheet = array();
	foreach ( fenix_lines( fenix_mod( 'about_points' ) ) as $fenix_point ) {
		$fenix_pt = array_map( 'trim', explode( '|', $fenix_point, 2 ) );
		if ( ! isset( $fenix_pt[1] ) ) {
			$fenix_pt = array( '', $fenix_pt[0] );
		}
		if ( '' === $fenix_pt[1] ) {
			continue;
		}
		if ( '' === $fenix_pt[0] ) {
			$fenix_pt[0] = sprintf( '%02d', count( $fenix_sheet ) + 1 );
		}
		$fenix_sheet[] = $fenix_pt;
	}
	$fenix_principle       = trim( (string) fenix_mod( 'home_what_principle' ) );
	$fenix_principle_label = trim( (string) fenix_mod( 'home_what_principle_label' ) );
	$fenix_about_text      = trim( (string) fenix_mod( 'home_about_btn_text' ) );
	$fenix_about_url       = fenix_home_link( fenix_mod( 'home_about_btn_url' ) );
	$fenix_what_media      = fenix_home_has_media( 'home_what' );
	$fenix_what_args       = fenix_home_media_args(
		'home_what',
		1280,
		800,
		array(
			'label' => trim( $fenix_fig . ' 01' ),
			'group' => 'home',
		)
	);
	/* ภาพแถบแนวนอน (กว้างเกือบ 2 เท่าของสูงขึ้นไป เช่น แผนภาพ 4 ขั้น 1600x760) อ่านไม่ออกในคอลัมน์ข้างเนื้อหา → วางเต็มความกว้างใต้ย่อหน้าเกริ่น
	 * ภาพหน้าจอ 16:10 / 16:9 ภาพจัตุรัส และภาพแนวตั้ง อยู่คอลัมน์ขวาตามเดิม */
	$fenix_what_wide       = '' !== trim( (string) fenix_mod( 'home_what_img' ) ) && $fenix_what_args['width'] >= 1.9 * $fenix_what_args['height'];
	?>
<section class="hm-ch hm-what<?php echo esc_attr( fenix_home_tone( 'what' ) ); ?>" id="what"<?php fenix_home_ch_attrs( 'what' ); ?>>
	<span id="about" class="hm-anchor" aria-hidden="true"></span>
	<div class="container">
		<?php fenix_home_ch_head( 'what', fenix_mod( 'about_title' ) ); ?>
		<div class="hm-what-grid<?php echo ( $fenix_what_media && ! $fenix_what_wide ) ? '' : ' is-solo'; ?><?php echo $fenix_what_wide ? ' is-wide' : ''; ?>">
			<div class="hm-what-copy">
				<?php fenix_home_paragraphs( fenix_mod( 'about_text' ), 'hm-lead' ); ?>
				<?php if ( $fenix_what_wide ) : ?>
					<div class="hm-what-media hm-what-media--wide">
						<?php fenix_media_slot( 'home_what', $fenix_what_args ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $fenix_sheet ) ) : ?>
					<dl class="hm-sheet">
						<?php foreach ( $fenix_sheet as $fenix_row ) : ?>
							<div class="hm-sheet-row"><dt><?php echo esc_html( $fenix_row[0] ); ?></dt><dd><?php echo fenix_home_text( $fenix_row[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></dd></div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<?php if ( '' !== $fenix_principle ) : ?>
					<aside class="hm-principle">
						<?php echo fenix_icon_badge( 'shield', 'green' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div>
							<?php if ( '' !== $fenix_principle_label ) : ?>
								<p class="hm-principle-label"><?php echo esc_html( $fenix_principle_label ); ?></p>
							<?php endif; ?>
							<p class="hm-principle-text"><?php echo fenix_home_text( $fenix_principle ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
						</div>
					</aside>
				<?php endif; ?>
				<?php if ( '' !== $fenix_about_text && '' !== $fenix_about_url ) : ?>
					<p class="hm-actions hm-what-more"><a class="hm-textlink" href="<?php echo esc_url( $fenix_about_url ); ?>"><?php echo esc_html( $fenix_about_text ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
				<?php endif; ?>
			</div>
			<?php if ( $fenix_what_media && ! $fenix_what_wide ) : ?>
				<div class="hm-what-media">
					<?php fenix_media_slot( 'home_what', $fenix_what_args ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 02 · จุดที่แผนมักหลุด (#pain) · แถวปัญหาถูกขีดฆ่าเมื่อเลื่อนถึง + แถวสรุป ============ */ ?>
<?php if ( isset( $fenix_chapters['pain'] ) ) : ?>
	<?php
	$fenix_pains = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$fenix_p_title = trim( (string) fenix_mod( 'pain' . $i . '_title' ) );
		$fenix_p_desc  = trim( (string) fenix_mod( 'pain' . $i . '_desc' ) );
		if ( '' === $fenix_p_title && '' === $fenix_p_desc ) {
			continue;
		}
		$fenix_pains[] = array( $fenix_p_title, $fenix_p_desc );
	}
	$fenix_resolved_label = trim( (string) fenix_mod( 'home_pain_resolved_label' ) );
	$fenix_resolved_text  = trim( (string) fenix_mod( 'home_pain_resolved_text' ) );
	?>
<section class="hm-ch hm-pain<?php echo esc_attr( fenix_home_tone( 'pain' ) ); ?>" id="pain"<?php fenix_home_ch_attrs( 'pain' ); ?>>
	<div class="container hm-pain-grid">
		<?php fenix_home_ch_head( 'pain', fenix_mod( 'pain_title' ), fenix_mod( 'pain_subtitle' ) ); ?>
		<ol class="hm-diag" data-hm-strike>
			<?php foreach ( $fenix_pains as $fenix_pi => $fenix_pain ) : ?>
				<li class="hm-diag-row" style="--i:<?php echo (int) $fenix_pi; ?>">
					<span class="hm-diag-idx" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_pi + 1 ) ); ?></span>
					<i class="hm-led" aria-hidden="true"></i>
					<div class="hm-diag-body">
						<?php if ( '' !== $fenix_pain[0] ) : ?>
							<h3 class="hm-diag-title"><span class="hm-strike"><?php echo fenix_home_text( $fenix_pain[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></h3>
						<?php endif; ?>
						<?php if ( '' !== $fenix_pain[1] ) : ?>
							<p><?php echo fenix_home_text( $fenix_pain[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
			<?php if ( '' !== $fenix_resolved_text ) : ?>
				<li class="hm-diag-row hm-diag-resolved" style="--i:<?php echo (int) count( $fenix_pains ); ?>">
					<span class="hm-diag-idx" aria-hidden="true"><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<i class="hm-led hm-led--ok" aria-hidden="true"></i>
					<div class="hm-diag-body">
						<?php if ( '' !== $fenix_resolved_label ) : ?>
							<p class="hm-diag-label"><?php echo esc_html( $fenix_resolved_label ); ?></p>
						<?php endif; ?>
						<p><?php echo fenix_home_text( $fenix_resolved_text ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
					</div>
				</li>
			<?php endif; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 03 · วงจรการทำงาน (#how + #how-it-works) · หน้าจอบันทึก (aria-hidden) + รายการขั้นตอนคู่แฝด + ภาพ 02 + สิ่งที่ต้องเตรียม ============ */ ?>
<?php if ( isset( $fenix_chapters['how'] ) ) : ?>
	<?php
	$fenix_steps = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$fenix_s_title = trim( (string) fenix_mod( 'step' . $i . '_title' ) );
		$fenix_s_desc  = trim( (string) fenix_mod( 'step' . $i . '_desc' ) );
		if ( '' === $fenix_s_title && '' === $fenix_s_desc ) {
			continue;
		}
		$fenix_steps[] = array(
			'n'     => sprintf( '%02d', count( $fenix_steps ) + 1 ),
			'title' => $fenix_s_title,
			'desc'  => $fenix_s_desc,
		);
	}
	$fenix_log_lines = fenix_home_log_lines( $fenix_steps );
	$fenix_show_log  = fenix_mod( 'home_show_how_log' ) && ! empty( $fenix_log_lines );
	$fenix_req_title = trim( (string) fenix_mod( 'steps_checklist_title' ) );
	$fenix_req_items = fenix_lines( fenix_mod( 'steps_checklist' ) );
	$fenix_how_media = fenix_home_has_media( 'home_how' );
	/* กล่อง "ข้อที่ต้องรู้": ประโยคเรื่องวิธีแก้ออเดอร์ (เปิดออเดอร์เพิ่ม ขยาย Lot ไม่ตัดขาดทุนเอง) + ลิงก์ไปหน้าประกาศความเสี่ยงเมื่อเผยแพร่แล้ว */
	$fenix_rec_label = trim( (string) fenix_mod( 'home_how_recovery_label' ) );
	$fenix_rec_text  = trim( (string) fenix_mod( 'home_how_recovery' ) );
	$fenix_rec_link  = trim( (string) fenix_mod( 'home_how_recovery_link' ) );
	$fenix_rec_url   = '' !== $fenix_rec_link ? fenix_home_link( '/risk-disclosure/' ) : '';
	$fenix_how_side  = $fenix_how_media || '' !== $fenix_rec_text || '' !== $fenix_req_title || ! empty( $fenix_req_items );
	?>
<section class="hm-ch hm-how<?php echo esc_attr( fenix_home_tone( 'how' ) ); ?>" id="how"<?php fenix_home_ch_attrs( 'how' ); ?>>
	<span id="how-it-works" class="hm-anchor" aria-hidden="true"></span>
	<div class="container">
		<?php fenix_home_ch_head( 'how', fenix_mod( 'steps_title' ), fenix_mod( 'steps_subtitle' ) ); ?>
		<div class="hm-how-grid<?php echo $fenix_how_side ? '' : ' is-solo'; ?>">
			<div class="hm-how-main">
				<?php if ( $fenix_show_log ) : ?>
					<div class="hm-log keep-case" aria-hidden="true" data-hm-log data-plays="2" data-lines="<?php echo esc_attr( wp_json_encode( $fenix_log_lines, JSON_UNESCAPED_UNICODE ) ); ?>">
						<div class="hm-log-bar">
							<span class="hm-log-ic"><?php echo fenix_icon( 'robot', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="hm-log-title"><?php echo esc_html( fenix_mod( 'home_how_log_title' ) ); ?></span>
							<span class="hm-log-dots"><i></i><i></i><i></i></span>
						</div>
						<pre class="hm-log-out" data-hm-log-out><?php
						foreach ( $fenix_log_lines as $fenix_li => $fenix_line ) {
							echo ( $fenix_li ? "\n" : '' ) . '<span class="hm-log-line ' . esc_attr( $fenix_line['c'] ) . '">' . esc_html( $fenix_line['t'] ) . '</span>';
						}
						?></pre>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $fenix_steps ) ) : ?>
					<ol class="hm-ledger">
						<?php foreach ( $fenix_steps as $fenix_step ) : ?>
							<li class="hm-ledger-row">
								<span class="hm-ledger-idx"><?php echo esc_html( $fenix_step['n'] ); ?></span>
								<div class="hm-ledger-body">
									<?php if ( '' !== $fenix_step['title'] ) : ?>
										<h3><?php echo fenix_home_text( $fenix_step['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
									<?php endif; ?>
									<?php if ( '' !== $fenix_step['desc'] ) : ?>
										<p><?php echo fenix_home_text( $fenix_step['desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
			<?php if ( $fenix_how_side ) : ?>
				<aside class="hm-how-side">
					<?php
					if ( $fenix_how_media ) {
						fenix_media_slot(
							'home_how',
							fenix_home_media_args(
								'home_how',
								1280,
								800,
								array(
									'label' => trim( $fenix_fig . ' 02' ),
									'group' => 'home',
								)
							)
						);
					}
					?>
					<?php if ( '' !== $fenix_rec_text ) : ?>
						<div class="hm-req hm-recovery" role="note">
							<p class="hm-recovery-head"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php if ( '' !== $fenix_rec_label ) : ?><span><?php echo esc_html( $fenix_rec_label ); ?></span><?php endif; ?></p>
							<p class="hm-recovery-text"><?php echo fenix_home_text( $fenix_rec_text ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
							<?php if ( '' !== $fenix_rec_url ) : ?>
								<a class="hm-textlink hm-recovery-link" href="<?php echo esc_url( $fenix_rec_url ); ?>"><span><?php echo esc_html( $fenix_rec_link ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( '' !== $fenix_req_title || ! empty( $fenix_req_items ) ) : ?>
						<div class="hm-req">
							<?php if ( '' !== $fenix_req_title ) : ?>
								<h3 class="hm-req-title"><?php echo fenix_home_text( $fenix_req_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $fenix_req_items ) ) : ?>
								<ul class="hm-checks">
									<?php foreach ( $fenix_req_items as $fenix_item ) : ?>
										<li><span class="hm-check" aria-hidden="true"><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo fenix_home_text( $fenix_item ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 04 · ส่วนประกอบหลัก (#features) · ตาราง 6 โมดูล เส้นบาง + ไอคอนวงกลม ============ */ ?>
<?php if ( isset( $fenix_chapters['features'] ) ) : ?>
	<?php
	$fenix_modules      = array();
	$fenix_module_icons = array( 'robot', 'monitor', 'layout', 'shield', 'clock', 'users' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fenix_f_title = trim( (string) fenix_mod( 'feat' . $i . '_title' ) );
		$fenix_f_desc  = trim( (string) fenix_mod( 'feat' . $i . '_desc' ) );
		if ( '' === $fenix_f_title && '' === $fenix_f_desc ) {
			continue;
		}
		/* ลิงก์ของโมดูล (ไม่บังคับ): ต้องมีหัวข้อ เพราะลิงก์อยู่ในหัวข้อแล้วขยายพื้นที่กดเต็มการ์ดด้วย CSS */
		$fenix_modules[] = array( $fenix_f_title, $fenix_f_desc, $fenix_module_icons[ $i - 1 ], '' !== $fenix_f_title ? fenix_home_link( fenix_mod( 'home_feat' . $i . '_url' ) ) : '' );
	}
	$fenix_module_label = trim( (string) fenix_mod( 'home_feat_module_label' ) );
	$fenix_module_more  = trim( (string) fenix_mod( 'home_feat_link_text' ) );
	?>
<section class="hm-ch hm-features<?php echo esc_attr( fenix_home_tone( 'features' ) ); ?>" id="features"<?php fenix_home_ch_attrs( 'features' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'features', fenix_mod( 'features_title' ), fenix_mod( 'features_subtitle' ) ); ?>
		<?php if ( ! empty( $fenix_modules ) ) : ?>
			<ul class="hm-modules" style="--mods:<?php echo (int) count( $fenix_modules ); ?>">
				<?php foreach ( $fenix_modules as $fenix_mi => $fenix_module ) : ?>
					<li class="hm-module reveal<?php echo '' !== $fenix_module[3] ? ' has-link' : ''; ?>">
						<div class="hm-module-top">
							<?php echo fenix_icon_badge( $fenix_module[2], 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php /* ป้ายเลขการ์ด ("ข้อ 01") เป็นคำเล็กเหนือหัวข้อของการ์ด: พิมพ์เฉพาะเมื่อเปิด show_kickers (หมวด 1) · ปิด = แถวบนของการ์ดมีแค่ไอคอน */ ?>
							<?php if ( $fenix_labels ) : ?>
								<span class="hm-module-id"><?php echo esc_html( trim( $fenix_module_label . ' ' . sprintf( '%02d', $fenix_mi + 1 ) ) ); ?></span>
							<?php endif; ?>
						</div>
						<?php if ( '' !== $fenix_module[0] ) : ?>
							<h3 class="hm-module-title">
								<?php if ( '' !== $fenix_module[3] ) : ?>
									<a class="hm-module-link" href="<?php echo esc_url( $fenix_module[3] ); ?>"><?php echo fenix_home_text( $fenix_module[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></a>
								<?php else : ?>
									<?php echo fenix_home_text( $fenix_module[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?>
								<?php endif; ?>
							</h3>
						<?php endif; ?>
						<?php if ( '' !== $fenix_module[1] ) : ?>
							<p class="hm-module-desc"><?php echo fenix_home_text( $fenix_module[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
						<?php endif; ?>
						<?php if ( '' !== $fenix_module[3] && '' !== $fenix_module_more ) : ?>
							<p class="hm-module-more" aria-hidden="true"><?php echo esc_html( $fenix_module_more ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 05 · การทดสอบ (#tests) · แท็บ radio ไม่ใช้ JS · ภาพประกอบเท่านั้น ไม่มีตัวเลขผล · tests_note แสดงเสมอ ============ */ ?>
<?php if ( isset( $fenix_chapters['tests'] ) ) : ?>
	<?php
	$fenix_dossier = array(
		'bt' => array(
			'tab'  => trim( (string) fenix_mod( 'home_tests_tab_bt_label' ) ),
			'btn'  => trim( (string) fenix_mod( 'home_tests_bt_btn' ) ),
			'url'  => fenix_home_link( '/backtest/' ),
			'fig'  => '03',
			'pos'  => 1,
		),
		'fw' => array(
			'tab'  => trim( (string) fenix_mod( 'home_tests_tab_fw_label' ) ),
			'btn'  => trim( (string) fenix_mod( 'home_tests_fw_btn' ) ),
			'url'  => fenix_home_link( '/forward-test/' ),
			'fig'  => '04',
			'pos'  => 2,
		),
	);
	$fenix_tests_note = trim( (string) fenix_mod( 'tests_note' ) );
	// ยังไม่มีตัวเลขจริงทั้ง Backtest และ Forward → เติมประโยค "ยังไม่มีผลทดสอบ" ข้างหน้า
	$fenix_no_stats = true;
	foreach ( array( array( 'bt_stat', 8 ), array( 'fw_stat', 6 ) ) as $fenix_sp ) {
		for ( $fenix_si = 1; $fenix_si <= $fenix_sp[1]; $fenix_si++ ) {
			if ( ! fenix_is_placeholder( fenix_mod( $fenix_sp[0] . $fenix_si . '_value' ) ) ) {
				$fenix_no_stats = false;
			}
		}
	}
	/* หน้า Backtest ที่เผยแพร่อยู่มีตารางผลในเนื้อหาแล้ว → ชี้ไปตารางนั้นและแดชบอร์ด (หน้าแรกไม่แสดงตัวเลขเอง) · ยังไม่มี → ข้อความ "ยังไม่มีผลทดสอบ" ตามเดิม */
	$fenix_bt_live  = function_exists( 'fenix_home_backtest_live' ) && fenix_home_backtest_live();
	$fenix_res_note = $fenix_bt_live ? trim( (string) fenix_mod( 'home_tests_results_note' ) ) : '';
	$fenix_res_link = $fenix_bt_live ? trim( (string) fenix_mod( 'home_tests_results_link' ) ) : '';
	$fenix_res_url  = ( '' !== $fenix_res_link && '' !== $fenix_dossier['bt']['url'] ) ? $fenix_dossier['bt']['url'] . '#backtest-results' : '';
	$fenix_res_dash = '';
	/* ลิงก์เข้าชมแดชบอร์ดแสดงเมื่อบทแดชบอร์ดอยู่บนหน้านี้ด้วย (หมายเหตุเรื่องข้อมูลตัวอย่างของโหมดเยี่ยมชมอยู่ในบทนั้น) */
	if ( '' !== $fenix_res_note && isset( $fenix_chapters['dashboard'] ) && function_exists( 'fenix_dashboard_button' ) ) {
		$fenix_res_dash = (string) fenix_dashboard_button(
			'visit',
			array(
				'class' => 'hm-textlink',
				'pos'   => 'home-tests',
				'icon'  => false,
			)
		);
	}
	if ( ! $fenix_bt_live && $fenix_no_stats && trim( (string) fenix_mod( 'tests_pending_note' ) ) ) {
		$fenix_tests_note = trim( trim( (string) fenix_mod( 'tests_pending_note' ) ) . ' · ' . $fenix_tests_note, ' ·' );
	}
	?>
<section class="hm-ch hm-tests<?php echo esc_attr( fenix_home_tone( 'tests' ) ); ?>" id="tests"<?php fenix_home_ch_attrs( 'tests' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'tests', fenix_mod( 'tests_title' ), fenix_mod( 'tests_subtitle' ) ); ?>
		<div class="hm-dossier" role="group" aria-label="<?php echo esc_attr( fenix_mod( 'home_tests_tab_label' ) ); ?>">
			<?php /* radio ต้องอยู่ก่อน .hm-tabs และ .hm-tab-panels ในพาเรนต์เดียวกัน (CSS ใช้ ~) */ ?>
			<?php foreach ( $fenix_dossier as $fenix_tk => $fenix_td ) : ?>
				<input type="radio" class="hm-tab-radio" name="falcon-tests" id="hm-tests-<?php echo esc_attr( $fenix_tk ); ?>"<?php echo 'bt' === $fenix_tk ? ' checked' : ''; ?>>
			<?php endforeach; ?>
			<div class="hm-tabs">
				<?php foreach ( $fenix_dossier as $fenix_tk => $fenix_td ) : ?>
					<label class="hm-tab" for="hm-tests-<?php echo esc_attr( $fenix_tk ); ?>"><span class="hm-tab-idx" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_td['pos'] ) ); ?></span><span class="hm-tab-txt"><?php echo fenix_home_tab_label( $fenix_td['tab'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></label>
				<?php endforeach; ?>
			</div>
			<div class="hm-tab-panels">
				<?php foreach ( $fenix_dossier as $fenix_tk => $fenix_td ) : ?>
					<?php $fenix_t_media = fenix_home_has_media( 'tests_' . $fenix_tk ); ?>
					<article class="hm-tab-panel hm-panel-<?php echo (int) $fenix_td['pos']; ?><?php echo $fenix_t_media ? '' : ' is-solo'; ?>">
						<?php if ( $fenix_t_media ) : ?>
							<div class="hm-dossier-media">
								<?php
								fenix_media_slot(
									'tests_' . $fenix_tk,
									fenix_home_media_args(
										'tests_' . $fenix_tk,
										1280,
										720,
										array(
											'label' => trim( $fenix_fig . ' ' . $fenix_td['fig'] ),
											'group' => 'home',
										)
									)
								);
								?>
							</div>
						<?php endif; ?>
						<div class="hm-dossier-body">
							<h3><?php echo fenix_home_text( fenix_mod( 'tests_' . $fenix_tk . '_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
							<?php fenix_home_paragraphs( fenix_mod( 'tests_' . $fenix_tk . '_text' ) ); ?>
							<?php if ( '' !== $fenix_td['btn'] && '' !== $fenix_td['url'] ) : ?>
								<a class="btn btn-ghost" href="<?php echo esc_url( $fenix_td['url'] ); ?>"><?php echo esc_html( $fenix_td['btn'] ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ( '' !== $fenix_res_note ) : ?>
			<div class="hm-notice hm-notice--info hm-tests-results">
				<?php echo fenix_icon( 'flask', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="hm-tests-results-body">
					<p><?php echo fenix_home_text( $fenix_res_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
					<?php if ( '' !== $fenix_res_url || '' !== $fenix_res_dash ) : ?>
						<p class="hm-tests-results-links">
							<?php if ( '' !== $fenix_res_url ) : ?>
								<a class="hm-textlink" href="<?php echo esc_url( $fenix_res_url ); ?>"><span><?php echo esc_html( $fenix_res_link ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php endif; ?>
							<?php echo $fenix_res_dash; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fenix_dashboard_button ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $fenix_tests_note ) : ?>
			<p class="hm-notice"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_home_text( $fenix_tests_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ เทียบวิธีทำงาน (#compare) · ตารางเทรดด้วยมือ กับให้ EA ทำตามแผน · โมดูลเสริม homeplus ============ */ ?>
<?php
if ( function_exists( 'fenix_homeplus_section' ) ) {
	fenix_homeplus_section( 'compare' );
}
?>

<?php /* ============ เริ่มต้น 6 ขั้น (#install) · แถบภาพเลื่อน (เฉพาะขั้นที่มีรูป) + รายการ "> ขั้น 01" พร้อมลิงก์คู่มือ + กระเบื้องอุปกรณ์ + หมายเหตุมือถือ + ปุ่มคู่มือ ============ */ ?>
<?php if ( isset( $fenix_chapters['install'] ) ) : ?>
	<?php
	$fenix_install = array();
	for ( $i = 1; $i <= fenix_home_step_count(); $i++ ) {
		$fenix_in_title = trim( (string) fenix_mod( 'ih_step' . $i . '_title' ) );
		$fenix_in_desc  = trim( (string) fenix_mod( 'ih_step' . $i . '_desc' ) );
		if ( '' === $fenix_in_title && '' === $fenix_in_desc ) {
			continue;
		}
		$fenix_in_to     = trim( (string) fenix_mod( 'ih_step' . $i . '_url' ) );
		$fenix_in_line   = 'line' === strtolower( $fenix_in_to );
		$fenix_install[] = array(
			'key'   => 'ih_step' . $i,
			'n'     => sprintf( '%02d', count( $fenix_install ) + 1 ),
			'title' => $fenix_in_title,
			'desc'  => $fenix_in_desc,
			'line'  => $fenix_in_line, /* ขั้นที่ใส่ลิงก์เป็นคำว่า line = ปุ่มติดต่อทีมงาน */
			'url'   => $fenix_in_line ? '' : fenix_home_link( $fenix_in_to ),
			'media' => fenix_home_has_media( 'ih_step' . $i ),
		);
	}
	$fenix_install_total = sprintf( '%02d', count( $fenix_install ) );
	$fenix_film_count    = 0;
	foreach ( $fenix_install as $fenix_step ) {
		$fenix_film_count += $fenix_step['media'] ? 1 : 0;
	}
	$fenix_step_label    = trim( (string) fenix_mod( 'home_install_step_label' ) );
	$fenix_step_link     = trim( (string) fenix_mod( 'home_steps_link_text' ) );
	$fenix_install_note  = trim( (string) fenix_mod( 'install_home_note' ) );
	$fenix_install_btn   = trim( (string) fenix_mod( 'home_install_btn' ) );
	$fenix_install_url   = fenix_home_link( fenix_mod( 'home_install_btn_url' ) );
	/* กระเบื้องอุปกรณ์: ชื่อ | บทบาท | ลิงก์คู่มือ (แถวที่มี 2 ช่องและช่องหลังเป็นลิงก์ = ชื่อ | ลิงก์) */
	$fenix_devices = array();
	if ( fenix_mod( 'home_show_devices' ) ) {
		foreach ( fenix_home_rows( fenix_mod( 'home_devices_items' ) ) as $fenix_dev ) {
			if ( '' === $fenix_dev[0] ) {
				continue;
			}
			$fenix_dev_hint = isset( $fenix_dev[1] ) ? $fenix_dev[1] : '';
			$fenix_dev_to   = isset( $fenix_dev[2] ) ? $fenix_dev[2] : '';
			if ( ! isset( $fenix_dev[2] ) && preg_match( '~^(/|#|https?:)~i', $fenix_dev_hint ) ) {
				$fenix_dev_to   = $fenix_dev_hint;
				$fenix_dev_hint = '';
			}
			$fenix_devices[] = array(
				'label' => $fenix_dev[0],
				'hint'  => $fenix_dev_hint,
				'url'   => fenix_home_link( $fenix_dev_to ),
				'icon'  => fenix_home_platform_icon( $fenix_dev[0] ),
			);
		}
	}
	$fenix_devices_title = trim( (string) fenix_mod( 'home_devices_title' ) );
	/* บรรทัด "ใช้ได้สองทาง" ใต้หัวบท · แสดงเมื่อเปิดใช้แดชบอร์ด · ลิงก์ไปหน้า /dashboard/ เมื่อหน้านั้นเผยแพร่แล้ว */
	$fenix_ways_text = ( function_exists( 'fenix_dashboard_enabled' ) && fenix_dashboard_enabled() ) ? trim( (string) fenix_mod( 'home_install_ways_text' ) ) : '';
	$fenix_ways_link = trim( (string) fenix_mod( 'home_install_ways_link' ) );
	$fenix_ways_url  = ( '' !== $fenix_ways_text && '' !== $fenix_ways_link ) ? fenix_home_link( '/dashboard/' ) : '';
	?>
<section class="hm-ch hm-install<?php echo esc_attr( fenix_home_tone( 'install' ) ); ?>" id="install"<?php fenix_home_ch_attrs( 'install' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'install', fenix_mod( 'install_home_title' ), fenix_mod( 'install_home_sub' ) ); ?>
		<?php if ( '' !== $fenix_ways_text ) : ?>
			<p class="hm-ways"><?php echo fenix_icon( 'server', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="hm-ways-text"><?php echo fenix_home_text( $fenix_ways_text ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span><?php if ( '' !== $fenix_ways_url ) : ?><a class="hm-textlink hm-ways-link" href="<?php echo esc_url( $fenix_ways_url ); ?>"><span><?php echo esc_html( $fenix_ways_link ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><?php endif; ?></p>
		<?php endif; ?>
		<?php if ( $fenix_film_count > 0 ) : ?>
			<div class="hm-film" role="group" tabindex="0" aria-label="<?php echo esc_attr( fenix_mod( 'home_install_film_label' ) ); ?>" style="--frames:<?php echo (int) $fenix_film_count; ?>">
				<?php
				foreach ( $fenix_install as $fenix_step ) {
					if ( ! $fenix_step['media'] ) {
						continue;
					}
					fenix_media_slot(
						$fenix_step['key'],
						fenix_home_media_args(
							$fenix_step['key'],
							1280,
							720,
							array(
								'label' => trim( $fenix_fig . ' ' . $fenix_step['n'] . '/' . $fenix_install_total ),
								'group' => 'home-install',
								'class' => 'hm-frame',
							)
						)
					);
				}
				?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $fenix_install ) ) : ?>
			<?php /* --rows = จำนวนคอลัมน์บนจอกว้าง: ไม่เกิน 4 ขั้นเรียงแถวเดียว · 5 ถึง 6 ขั้นเป็น 3 คอลัมน์ 2 แถว */ ?>
			<ol class="hm-ledger hm-install-ledger" style="--rows:<?php echo (int) ( count( $fenix_install ) > 4 ? 3 : count( $fenix_install ) ); ?>">
				<?php foreach ( $fenix_install as $fenix_step ) : ?>
					<li class="hm-ledger-row">
						<span class="hm-ledger-idx"><span class="hm-ledger-caret" aria-hidden="true">&gt;</span> <?php echo esc_html( trim( $fenix_step_label . ' ' . $fenix_step['n'] ) ); ?></span>
						<div class="hm-ledger-body">
							<?php if ( '' !== $fenix_step['title'] ) : ?>
								<h3><?php echo fenix_home_text( $fenix_step['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
							<?php endif; ?>
							<?php if ( '' !== $fenix_step['desc'] ) : ?>
								<p><?php echo fenix_home_text( $fenix_step['desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
							<?php endif; ?>
							<?php if ( $fenix_step['line'] ) : ?>
								<?php fenix_home_contact_link( fenix_mod( 'home_steps_line_text' ), 'hm-textlink hm-ledger-link', 'home-steps', '', false ); ?>
							<?php elseif ( '' !== $fenix_step['url'] && '' !== $fenix_step_link ) : ?>
								<a class="hm-textlink hm-ledger-link" href="<?php echo esc_url( $fenix_step['url'] ); ?>"><span><?php echo esc_html( $fenix_step_link ); ?></span><?php if ( '' !== $fenix_step['title'] ) : ?><span class="sr-only">: <?php echo esc_html( $fenix_step['title'] ); ?></span><?php endif; ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<?php if ( ! empty( $fenix_devices ) ) : ?>
			<div class="hm-devices">
				<?php if ( '' !== $fenix_devices_title ) : ?>
					<h3 class="hm-devices-title"><?php echo fenix_home_text( $fenix_devices_title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h3>
				<?php endif; ?>
				<ul class="hm-device-list" style="--devices:<?php echo (int) min( 5, count( $fenix_devices ) ); ?>">
					<?php foreach ( $fenix_devices as $fenix_dev ) : ?>
						<?php $fenix_dev_tag = '' !== $fenix_dev['url'] ? 'a' : 'span'; /* มีลิงก์ที่เปิดได้ = กดได้ทั้งกระเบื้อง · ไม่มี = ป้ายธรรมดา */ ?>
						<li>
							<<?php echo $fenix_dev_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- a|span ?> class="hm-device"<?php echo 'a' === $fenix_dev_tag ? ' href="' . esc_url( $fenix_dev['url'] ) . '"' : ''; ?>>
								<?php echo fenix_icon_badge( $fenix_dev['icon'], 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span class="hm-device-text">
									<span class="hm-device-name"><?php echo esc_html( $fenix_dev['label'] ); ?></span>
									<?php if ( '' !== $fenix_dev['hint'] ) : ?>
										<span class="hm-device-hint"><?php echo fenix_home_text( $fenix_dev['hint'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span>
									<?php endif; ?>
								</span>
								<?php if ( 'a' === $fenix_dev_tag ) : ?>
									<?php echo fenix_icon( 'arrow', 'icon icon-sm hm-device-go' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php endif; ?>
							</<?php echo $fenix_dev_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- a|span ?>>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $fenix_install_note ) : ?>
			<p class="hm-notice"><?php echo fenix_icon( 'phone', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_home_text( $fenix_install_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
		<?php endif; ?>
		<?php if ( '' !== $fenix_install_btn && '' !== $fenix_install_url ) : ?>
			<p class="hm-actions"><a class="btn btn-ghost" href="<?php echo esc_url( $fenix_install_url ); ?>"><?php echo fenix_icon( 'book', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_install_btn ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ แดชบอร์ด (#dashboard) · หัวบทของหน้าแรก + บล็อกแนะนำของโมดูล dashboard (ข้อความ จุดเด่น ข้อควรรู้ ภาพหน้าจอ ปุ่มเข้าชม ปุ่มสมัคร หมายเหตุ) ============ */ ?>
<?php if ( isset( $fenix_chapters['dashboard'] ) ) : ?>
<section class="hm-ch hm-dash<?php echo esc_attr( fenix_home_tone( 'dashboard' ) ); ?>" id="dashboard"<?php fenix_home_ch_attrs( 'dashboard' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'dashboard', fenix_mod( 'home_dashboard_title' ), fenix_mod( 'home_dashboard_sub' ) ); ?>
		<?php
		/* การ์ดอย่างเดียว (wrap = false) ใน section ของบทนี้ · หัวข้อในการ์ดเป็น h3 ต่อจาก H2 ของบท */
		fenix_dashboard_block(
			'home',
			array(
				'wrap'    => false,
				'heading' => 'h3',
				'pos'     => 'home-dashboard',
				'id'      => 'dashboard-promo',
			)
		);
		?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ เช็กความพร้อม (#ready) · รายการติ๊กได้ + วงความคืบหน้า + ปุ่มติดต่อเมื่อครบ · โมดูลเสริม homeplus ============ */ ?>
<?php
if ( function_exists( 'fenix_homeplus_section' ) ) {
	fenix_homeplus_section( 'ready' );
}
?>

<?php /* ============ แพ็กเกจ (#pricing) · แท็บ radio (<1100px) / ตารางเทียบ (≥1100px) · ราคาจริง คำว่าฟรี หรือข้อความสอบถาม ============ */ ?>
<?php if ( isset( $fenix_chapters['pricing'] ) ) : ?>
	<?php
	$fenix_pmode = fenix_mod( 'pricing_mode' );
	$fenix_tiers = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$fenix_pk_name = trim( (string) fenix_mod( 'pkg' . $i . '_name' ) );
		if ( '' === $fenix_pk_name ) {
			continue;
		}
		$fenix_pk_price = trim( (string) fenix_mod( 'pkg' . $i . '_price' ) );
		$fenix_tiers[]  = array(
			'n'        => count( $fenix_tiers ) + 1,
			'name'     => $fenix_pk_name,
			'tag'      => trim( (string) fenix_mod( 'pkg' . $i . '_tag' ) ),
			'price'    => 'price' === $fenix_pmode && fenix_home_price_ready( $fenix_pk_price ) ? $fenix_pk_price : '',
			'period'   => trim( (string) fenix_mod( 'pkg' . $i . '_period' ) ),
			'features' => fenix_lines( fenix_mod( 'pkg' . $i . '_features' ) ),
			'featured' => (bool) fenix_mod( 'pkg' . $i . '_featured' ),
		);
	}
	/* แท็บที่เลือกตอนโหลด = แพ็กเกจแนะนำตัวแรก ถ้าไม่มีใช้ตัวแรก */
	$fenix_tier_checked = 1;
	foreach ( $fenix_tiers as $fenix_tier ) {
		if ( $fenix_tier['featured'] ) {
			$fenix_tier_checked = $fenix_tier['n'];
			break;
		}
	}
	$fenix_rec_label    = trim( (string) fenix_mod( 'home_pricing_rec_label' ) );
	$fenix_contact_text = trim( (string) fenix_mod( 'home_pricing_contact_text' ) );
	$fenix_margin_note  = trim( (string) fenix_mod( 'home_pricing_margin_note' ) );
	$fenix_more_text    = trim( (string) fenix_mod( 'home_pricing_more_text' ) );
	$fenix_more_url     = fenix_home_link( '/pricing/' );
	$fenix_pricing_note = trim( (string) fenix_mod( 'pricing_note' ) );
	?>
<section class="hm-ch hm-pricing<?php echo esc_attr( fenix_home_tone( 'pricing' ) ); ?>" id="pricing"<?php fenix_home_ch_attrs( 'pricing' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'pricing', fenix_mod( 'pricing_home_title' ), fenix_mod( 'pricing_home_sub' ) ); ?>
		<?php if ( ! empty( $fenix_tiers ) ) : ?>
			<div class="hm-tiers" role="group" aria-label="<?php echo esc_attr( fenix_mod( 'home_pricing_tabs_label' ) ); ?>" style="--tiers:<?php echo (int) count( $fenix_tiers ); ?>">
				<?php foreach ( $fenix_tiers as $fenix_tier ) : ?>
					<input type="radio" class="hm-tab-radio" name="falcon-tier" id="hm-tier-<?php echo (int) $fenix_tier['n']; ?>"<?php echo $fenix_tier['n'] === $fenix_tier_checked ? ' checked' : ''; ?>>
				<?php endforeach; ?>
				<div class="hm-tabs hm-tier-tabs">
					<?php foreach ( $fenix_tiers as $fenix_tier ) : ?>
						<label class="hm-tab" for="hm-tier-<?php echo (int) $fenix_tier['n']; ?>"><span class="hm-tab-idx" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_tier['n'] ) ); ?></span><?php echo esc_html( $fenix_tier['name'] ); ?><?php if ( $fenix_tier['featured'] && '' !== $fenix_rec_label ) : ?><span class="hm-tab-rec"><?php echo esc_html( $fenix_rec_label ); ?></span><?php endif; ?></label>
					<?php endforeach; ?>
				</div>
				<div class="hm-tab-panels hm-tier-panels">
					<?php foreach ( $fenix_tiers as $fenix_tier ) : ?>
						<article class="hm-tab-panel hm-tier hm-panel-<?php echo (int) $fenix_tier['n']; ?><?php echo $fenix_tier['featured'] ? ' is-featured' : ''; ?>">
							<header class="hm-tier-head">
								<h3 class="hm-tier-name"><?php echo esc_html( $fenix_tier['name'] ); ?><?php if ( $fenix_tier['featured'] && '' !== $fenix_rec_label ) : ?> <span class="hm-tier-rec"><?php echo esc_html( $fenix_rec_label ); ?></span><?php endif; ?></h3>
								<?php if ( '' !== $fenix_tier['tag'] ) : ?>
									<p class="hm-tier-tag"><?php echo fenix_home_text( $fenix_tier['tag'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
								<?php endif; ?>
							</header>
							<p class="hm-tier-price">
								<?php if ( '' !== $fenix_tier['price'] ) : ?>
									<span class="hm-tier-amt"><?php echo esc_html( $fenix_tier['price'] ); ?></span><?php if ( '' !== $fenix_tier['period'] ) : ?> <span class="hm-tier-period"><?php echo esc_html( $fenix_tier['period'] ); ?></span><?php endif; ?>
								<?php else : ?>
									<span class="hm-tier-amt hm-tier-amt--contact"><?php echo esc_html( $fenix_contact_text ); ?></span>
								<?php endif; ?>
							</p>
							<?php if ( ! empty( $fenix_tier['features'] ) ) : ?>
								<ul class="hm-checks hm-tier-list">
									<?php foreach ( $fenix_tier['features'] as $fenix_item ) : ?>
										<li><span class="hm-check" aria-hidden="true"><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo fenix_home_text( $fenix_item ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<?php fenix_home_contact_link( fenix_mod( 'pricing_btn_text' ), $fenix_tier['featured'] ? 'btn btn-fire hm-tier-btn' : 'btn btn-ghost hm-tier-btn', 'home-pricing', $fenix_tier['name'] ); ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $fenix_margin_note ) : ?>
			<p class="hm-notice hm-notice--warn"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo fenix_home_text( $fenix_margin_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span></p>
		<?php endif; ?>
		<?php if ( '' !== $fenix_more_text && '' !== $fenix_more_url ) : ?>
			<p class="hm-actions"><a class="hm-textlink" href="<?php echo esc_url( $fenix_more_url ); ?>"><?php echo esc_html( $fenix_more_text ); ?><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
		<?php if ( '' !== $fenix_pricing_note ) : ?>
			<p class="hm-foot"><?php echo fenix_home_text( $fenix_pricing_note ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ถาม-ตอบ (#faq) · มีเลข เปิดได้ทีละข้อ (details name) · 2 คอลัมน์ที่ ≥960px · schema อ่านจาก key เดียวกัน · จำนวนช่อง = fenix_home_faq_count() ============ */ ?>
<?php if ( isset( $fenix_chapters['faq'] ) ) : ?>
<section class="hm-ch hm-faq<?php echo esc_attr( fenix_home_tone( 'faq' ) ); ?>" id="faq"<?php fenix_home_ch_attrs( 'faq' ); ?>>
	<div class="container">
		<?php fenix_home_ch_head( 'faq', fenix_mod( 'faq_title' ), fenix_mod( 'faq_subtitle' ) ); ?>
		<div class="hm-qlog">
			<?php
			$fenix_qn = 0;
			for ( $i = 1; $i <= fenix_home_faq_count(); $i++ ) :
				/* ตัวแทน {brand} {broker} {server} {disclosure} → ค่าจริงก่อน escape · ตรงกับ FAQ schema ใน inc/seo.php ที่แทนค่าแบบเดียวกัน */
				$fenix_q = trim( fenix_tokens( (string) fenix_mod( 'faq' . $i . '_q' ) ) );
				$fenix_a = trim( fenix_tokens( (string) fenix_mod( 'faq' . $i . '_a' ) ) );
				if ( '' === $fenix_q || '' === $fenix_a ) {
					continue;
				}
				$fenix_qn++;
				?>
				<details class="hm-q" name="falcon-faq">
					<summary class="hm-q-sum"><span class="hm-q-idx" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $fenix_qn ) ); ?></span><span class="hm-q-text"><?php echo fenix_home_text( $fenix_q ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></span><i class="hm-q-mark" aria-hidden="true"></i></summary>
					<div class="hm-q-ans"><?php fenix_home_paragraphs( $fenix_a ); ?></div>
				</details>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ ความเสี่ยง (#risk) · บทสุดท้าย · แถบเตือนเต็มความกว้าง นับเป็นบท · ข้อความเต็ม ไม่พับ ไม่ตัด ============ */ ?>
<?php if ( isset( $fenix_chapters['risk'] ) ) : ?>
	<?php
	$fenix_risk_label = trim( (string) fenix_mod( 'home_risk_label' ) );
	$fenix_risk_more  = trim( (string) fenix_mod( 'home_risk_more_text' ) );
	$fenix_risk_url   = fenix_home_link( '/risk-disclosure/' );
	?>
<section class="hm-ch hm-risk" id="risk"<?php fenix_home_ch_attrs( 'risk' ); ?> aria-labelledby="hm-risk-title">
	<div class="container hm-hazard">
		<?php /* ตราเตือน (ไอคอน + คำว่าคำเตือน) บอกว่าส่วนนี้คือคำเตือนความเสี่ยง จึงแสดงเสมอเมื่อมีข้อความ · เลขบทกับป้ายบทที่ต่อท้ายพิมพ์เฉพาะเมื่อเปิด show_kickers (หมวด 1)
		 * ไม่มีทั้งสองอย่าง = ไม่พิมพ์บรรทัดนี้เลย (ไม่เหลือ <p> เปล่า) */ ?>
		<?php if ( '' !== $fenix_risk_label || $fenix_labels ) : ?>
		<p class="hm-hazard-label">
			<?php if ( '' !== $fenix_risk_label ) : ?>
				<span class="hm-hazard-stamp"><?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $fenix_risk_label ); ?></span>
			<?php endif; ?>
			<?php if ( $fenix_labels ) : ?>
				<span class="hm-hazard-topic"><span class="hm-hazard-num"><?php echo esc_html( $fenix_chapters['risk']['n'] . ' / ' . $fenix_ch_total ); ?></span><?php if ( '' !== $fenix_chapters['risk']['label'] ) : ?> · <?php echo esc_html( $fenix_chapters['risk']['label'] ); ?><?php endif; ?></span>
			<?php endif; ?>
		</p>
		<?php endif; ?>
		<h2 class="hm-hazard-title" id="hm-risk-title"><?php echo fenix_home_text( fenix_mod( 'risk_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></h2>
		<p class="hm-hazard-text"><?php echo nl2br( fenix_home_text( fenix_mod( 'risk_text' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?></p>
		<?php if ( '' !== $fenix_risk_more && '' !== $fenix_risk_url ) : ?>
			<a class="btn btn-dark" href="<?php echo esc_url( $fenix_risk_url ); ?>"><?php echo fenix_icon( 'shield', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_risk_more ); ?></span><?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

</main>

<?php
get_footer();

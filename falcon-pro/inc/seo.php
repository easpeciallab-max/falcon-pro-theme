<?php
/**
 * FALCON PRO EA · SEO เพิ่มเติม
 *
 * ทำงานร่วมกับ Yoast/Rank Math ได้ (ไม่ซ้ำกับสิ่งที่ปลั๊กอินสร้าง):
 * - FAQPage        : หน้าแรก (จาก Customizer) + ทุกเพจ/บทความที่มี <details class="faq-item"> ในเนื้อหา
 * - SoftwareApplication : หน้าแรก + หน้าแพ็กเกจ (มี Offer เฉพาะเมื่อแสดงราคาจริง)
 * เฉพาะเมื่อ "ไม่มี" ปลั๊กอิน SEO:
 * - BreadcrumbList, favicon สำรอง (BlogPosting อยู่ใน single.php)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fenix_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' );
}

function fenix_jsonld( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * ดึงคำถาม/คำตอบจากเนื้อหา HTML (details.faq-item)
 */
function fenix_faq_from_html( $html ) {
	$faqs = array();
	if ( false === strpos( (string) $html, 'faq-item' ) ) {
		return $faqs;
	}
	if ( preg_match_all( '#<details[^>]*class="[^"]*faq-item[^"]*"[^>]*>\s*<summary[^>]*>(.*?)</summary>(.*?)</details>#is', $html, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $row ) {
			$q = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $row[1] ) ) );
			$a = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( $row[2] ) ) ) );
			if ( $q && $a ) {
				$faqs[] = array(
					'@type'          => 'Question',
					'name'           => $q,
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $a,
					),
				);
			}
		}
	}
	return $faqs;
}

function fenix_schema_extra() {
	$faqs = array();

	/* FAQ หน้าแรก (Customizer) */
	if ( is_front_page() && fenix_mod( 'show_faq' ) ) {
		for ( $i = 1; $i <= 10; $i++ ) {
			$q = fenix_mod( 'faq' . $i . '_q' );
			$a = fenix_mod( 'faq' . $i . '_a' );
			if ( $q && $a ) {
				$faqs[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $q ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $a ),
					),
				);
			}
		}
	} elseif ( is_singular( array( 'post', 'page' ) ) ) {
		$post = get_queried_object();
		// เพจ/บทความที่ตั้งรหัสผ่าน: ไม่ดึงเนื้อหาไปใส่ schema (กันเนื้อหารั่วใน JSON-LD)
		if ( $post && ! post_password_required( $post ) && ! empty( $post->post_content ) ) {
			$faqs = fenix_faq_from_html( $post->post_content );
		}
	}

	if ( $faqs ) {
		fenix_jsonld(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'@id'        => ( is_front_page() ? home_url( '/' ) : get_permalink() ) . '#faq',
				'mainEntity' => $faqs,
			)
		);
	}

	/* SoftwareApplication (หน้าแรก + แพ็กเกจ) */
	$slug = is_page() ? (string) get_post_field( 'post_name', get_queried_object_id() ) : '';
	if ( is_front_page() || 'pricing' === $slug ) {
		$app = array(
			'@context'             => 'https://schema.org',
			'@type'                => 'SoftwareApplication',
			'@id'                  => home_url( '/' ) . '#product',
			'name'                 => fenix_mod( 'hero_title' ) ? fenix_mod( 'hero_title' ) : 'FALCON PRO EA',
			'applicationCategory'  => 'FinanceApplication',
			'applicationSubCategory' => 'Expert Advisor',
			'operatingSystem'      => 'Windows',
			'softwareRequirements' => 'MetaTrader 5',
			'url'                  => home_url( '/' ),
			'image'                => fenix_share_image(),
			'description'          => fenix_mod( 'og_default_description' ),
			'inLanguage'           => 'th',
		);
		if ( 'price' === fenix_mod( 'pricing_mode' ) ) {
			$prices = array();
			for ( $i = 1; $i <= 3; $i++ ) {
				$p = preg_replace( '/[^0-9.]/', '', (string) fenix_mod( 'pkg' . $i . '_price' ) );
				if ( '' !== $p && is_numeric( $p ) ) {
					$prices[] = (float) $p;
				}
			}
			if ( $prices ) {
				$app['offers'] = array(
					'@type'         => 'AggregateOffer',
					'priceCurrency' => 'THB',
					'lowPrice'      => min( $prices ),
					'highPrice'     => max( $prices ),
					'offerCount'    => count( $prices ),
					'url'           => home_url( '/pricing/' ),
				);
			}
		}
		fenix_jsonld( $app );
	}

	if ( fenix_has_seo_plugin() ) {
		return;
	}

	/* BreadcrumbList */
	if ( ! is_front_page() && ( is_singular() || is_home() ) ) {
		$items = array();
		foreach ( fenix_breadcrumbs( is_home() ? '' : wp_strip_all_tags( get_the_title( get_queried_object_id() ) ) ) as $i => $crumb ) {
			$items[] = array_filter(
				array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $crumb['name'],
					'item'     => $crumb['url'],
				)
			);
		}
		fenix_jsonld(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $items,
			)
		);
	}

	/* Article/BlogPosting: สร้างใน single.php */
}
add_action( 'wp_head', 'fenix_schema_extra', 7 );

/**
 * รูปแชร์เริ่มต้น (Customizer → SEO หรือการ์ดแชร์ของธีม)
 */
function fenix_share_image() {
	$img = fenix_mod( 'og_default_image' );
	return $img ? $img : get_template_directory_uri() . '/assets/img/brand/falcon-pro-share-1200x630.jpg';
}

/**
 * meta description ของหน้าปัจจุบัน (ใช้เมื่อไม่มีปลั๊กอิน SEO)
 */
function fenix_meta_description() {
	if ( is_singular() ) {
		$id   = get_queried_object_id();
		$desc = get_post_meta( $id, 'fenix_meta_description', true );
		if ( $desc ) {
			return $desc;
		}
		$post = get_post( $id );
		if ( $post && post_password_required( $post ) ) {
			return (string) fenix_mod( 'og_default_description' );
		}
		if ( $post && $post->post_excerpt ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}
		if ( $post && ! is_front_page() ) {
			$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) ) );
			if ( $text ) {
				return mb_substr( $text, 0, 155 ) . ( mb_strlen( $text ) > 155 ? '…' : '' );
			}
		}
	}
	return (string) fenix_mod( 'og_default_description' );
}

/**
 * favicon สำรอง (เมื่อยังไม่ได้ตั้ง Site Icon ใน Customizer)
 */
function fenix_favicon_fallback() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/img/brand/';
	echo '<link rel="icon" href="' . esc_url( $base . 'favicon-32.png' ) . '" sizes="32x32">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $base . 'falcon-pro-icon-180.png' ) . '">' . "\n";
	echo '<meta name="theme-color" content="#0B0D10">' . "\n";
}
add_action( 'wp_head', 'fenix_favicon_fallback', 3 );


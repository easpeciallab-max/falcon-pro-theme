<?php
/**
 * FALCON PRO EA · SEO เพิ่มเติม
 *
 * ทำงานร่วมกับ Yoast/Rank Math ได้ (ไม่ซ้ำกับสิ่งที่ปลั๊กอินสร้าง):
 * - FAQPage             : หน้าแรก (จาก Customizer) + ทุกเพจ/บทความที่มี <details class="faq-item"> ในเนื้อหา
 * - SoftwareApplication : หน้าแรก + หน้าแพ็กเกจ · Offer[] ออกเฉพาะเมื่อเจ้าของยืนยันราคาแล้ว (seo_pricing_confirmed)
 * - Organization sameAs : มี Yoast = เติมเข้า Organization ของ Yoast · ไม่มี = ธีมออก Organization + WebSite เอง
 * - robots              : noindex /go/, /articles/ ที่ยังไม่มีบทความ, ค้นหา/ผู้เขียน/วันที่ และหมวด/แท็กที่ว่าง
 *                         ผ่าน wp_robots (Yoast รวมค่าเข้าแท็กเดียว) + ฟิลเตอร์ของ Yoast/Rank Math · ไม่พิมพ์แท็กเอง
 * - sitemap             : ตัดหน้า noindex ออกจาก sitemap ของ Yoast และของคอร์ · ปิด sitemap รายชื่อผู้ใช้
 * - ยืนยันเจ้าของเว็บ    : meta ของ Google Search Console / Bing + ตอบไฟล์ googleXXXX.html ให้เอง
 * - REST                : meta SEO รายหน้าของ Yoast อ่าน/เขียนผ่าน /wp/v2/pages/<id> ได้ (ต้องมีสิทธิ์แก้หน้านั้น)
 * เฉพาะเมื่อ "ไม่มี" ปลั๊กอิน SEO:
 * - Open Graph (og:image:alt จาก Alt Text ใน Media Library), BreadcrumbList, canonical หน้า archive,
 *   บรรทัด Sitemap ใน robots.txt, favicon สำรอง (BlogPosting อยู่ใน single.php)
 *
 * ค่าที่ตั้งได้ใน Customizer (เพิ่มผ่านฟิลเตอร์ fenix_defaults / fenix_customizer_sections):
 * seo_gsc_verify, seo_gsc_file, seo_bing_verify, seo_product_name, seo_product_os,
 * seo_product_requirements, seo_product_version, seo_pricing_confirmed
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
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}

/* ==============================================================
 * 0) ค่าตั้งใน Customizer ของส่วน SEO
 * ============================================================== */

function fenix_seo_default_values( $d ) {
	return array_merge(
		$d,
		array(
			/* ยืนยันเจ้าของเว็บ (ว่าง = ไม่พิมพ์อะไร) */
			'seo_gsc_verify'           => '',
			'seo_gsc_file'             => '',
			'seo_bing_verify'          => '',

			/* ข้อมูลสินค้าในข้อมูลโครงสร้าง (SoftwareApplication) */
			'seo_product_name'         => 'FALCON PRO EA',
			'seo_product_os'           => 'Windows',
			'seo_product_requirements' => 'MetaTrader 5',
			'seo_product_version'      => '',
			'seo_pricing_confirmed'    => false,
		)
	);
}
add_filter( 'fenix_defaults', 'fenix_seo_default_values' );

/**
 * เพิ่ม 2 section ต่อท้าย "SEO / แชร์ลิงก์" (fenix_seo) · เลขข้อตามหัวข้อ fenix_seo เช่น 17.1) 17.2)
 * priority 20 = ทำหลังโมดูลอื่นที่อาจจัดลำดับ section ใหม่
 */
function fenix_seo_customizer_sections( $sections, $d ) {
	unset( $d );

	$num = '';
	if ( isset( $sections['fenix_seo']['title'] ) && preg_match( '/^\s*(\d+)\)/', $sections['fenix_seo']['title'], $m ) ) {
		$num = $m[1];
	}

	if ( isset( $sections['fenix_seo']['fields']['og_default_image'] ) ) {
		$sections['fenix_seo']['fields']['og_default_image'][2] = 'ใส่ข้อความ Alt ของรูปนี้ในคลังสื่อด้วย ธีมส่งข้อความนั้นเป็นคำอธิบายรูปเวลามีคนแชร์ลิงก์';
	}

	$new = array(
		'fenix_seo_verify'  => array(
			'title'       => ( $num ? $num . '.1) ' : '' ) . 'SEO · ยืนยันเจ้าของเว็บ (Google / Bing)',
			'description' => 'กรอกเฉพาะวิธีที่ใช้ยืนยัน ช่องที่เว้นว่างจะไม่แสดงอะไรบนเว็บ · ถ้าตั้งรหัสเดียวกันไว้ใน Yoast แล้ว ธีมจะไม่พิมพ์ซ้ำ',
			'fields'      => array(
				'seo_gsc_verify'  => array( 'รหัสยืนยัน Google Search Console (วิธีแท็ก HTML)', 'text', 'คัดลอกมาเฉพาะตัวรหัสที่อยู่ในเครื่องหมายคำพูดหลัง content= ไม่ต้องวางทั้งแท็ก' ),
				'seo_gsc_file'    => array( 'ชื่อไฟล์ยืนยัน Google Search Console (วิธีไฟล์ HTML)', 'text', 'พิมพ์ชื่อไฟล์ที่ Google ให้ดาวน์โหลด (ขึ้นต้นด้วย google และลงท้ายด้วย .html) ธีมจะตอบไฟล์นี้ให้เองโดยไม่ต้องอัปโหลด · ยืนยันผ่านแล้วอย่าลบค่านี้' ),
				'seo_bing_verify' => array( 'รหัสยืนยัน Bing Webmaster Tools (msvalidate.01)', 'text', 'วางเฉพาะรหัสในช่อง content= · ถ้ายืนยัน Bing ด้วยการนำเข้าจาก Search Console แล้ว เว้นว่างได้' ),
			),
		),
		'fenix_seo_product' => array(
			'title'       => ( $num ? $num . '.2) ' : '' ) . 'SEO · ข้อมูลสินค้าที่ส่งให้ Google',
			'description' => 'ใช้สร้างข้อมูลโครงสร้าง (Schema) ของสินค้าบนหน้าแรกและหน้าแพ็กเกจ ผู้เข้าชมมองไม่เห็นส่วนนี้ · ห้ามใส่ตัวเลขผลเทรด คะแนนรีวิว หรือคำรับประกันกำไร',
			'fields'      => array(
				'seo_product_name'         => array( 'ชื่อสินค้า (เว้นว่าง = ใช้ชื่อเว็บ)', 'text' ),
				'seo_product_os'           => array( 'ระบบปฏิบัติการที่ใช้รัน EA', 'text', 'ใส่ชื่อระบบปฏิบัติการ ส่วน MT5 เป็นโปรแกรมเทรด ให้ใส่ในช่องถัดไป' ),
				'seo_product_requirements' => array( 'โปรแกรมที่ต้องมีก่อนใช้งาน', 'text' ),
				'seo_product_version'      => array( 'เวอร์ชันของ EA (เว้นว่าง = ไม่ระบุ)', 'text' ),
				'seo_pricing_confirmed'    => array( 'ยืนยันแล้วว่าราคาแพ็กเกจถูกต้อง ส่งราคาให้ Google ได้', 'checkbox', 'เปิดเมื่อหน้าแพ็กเกจแสดงราคาจริงเป็นตัวเลขแล้วเท่านั้น · ปิดไว้ = ธีมไม่ประกาศราคาในข้อมูลโครงสร้าง แพ็กเกจที่ราคาไม่ใช่ตัวเลขจะถูกข้ามเสมอ' ),
			),
		),
	);

	if ( ! isset( $sections['fenix_seo'] ) ) {
		return array_merge( $sections, $new );
	}

	$out = array();
	foreach ( $sections as $fenix_id => $fenix_section ) {
		$out[ $fenix_id ] = $fenix_section;
		if ( 'fenix_seo' === $fenix_id ) {
			foreach ( $new as $fenix_new_id => $fenix_new ) {
				$out[ $fenix_new_id ] = $fenix_new;
			}
		}
	}
	return $out;
}
add_filter( 'fenix_customizer_sections', 'fenix_seo_customizer_sections', 20, 2 );

/* ==============================================================
 * 1) FAQPage + SoftwareApplication + BreadcrumbList
 * ============================================================== */

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

/**
 * แปลงราคาแพ็กเกจเป็นตัวเลข · คืน null เมื่อไม่ใช่ราคาจริง (X,XXX / ทัก LINE / ฟรี / 0)
 * รับได้ทั้ง 1,990 · 1990 บาท · ฿1,990 · THB 1990
 */
function fenix_seo_numeric_price( $raw ) {
	$clean = str_replace( array( ',', ' ', "\xc2\xa0" ), '', trim( (string) $raw ) );
	if ( '' === $clean ) {
		return null;
	}
	$stripped = preg_replace( '/^(?:฿|thb|baht)|(?:บาท|thb|baht|฿|\.-|-)$/iu', '', $clean );
	if ( null !== $stripped ) {
		$clean = trim( $stripped );
	}
	if ( ! is_numeric( $clean ) || (float) $clean <= 0 ) {
		return null;
	}
	return (float) $clean;
}

/**
 * ราคาในรูปที่ schema.org รับ (ตัวเลขล้วน จุดทศนิยม ไม่มีคอมมา)
 */
function fenix_seo_schema_price( $price ) {
	$decimals = ( floor( $price ) === (float) $price ) ? 0 : 2;
	return number_format( (float) $price, $decimals, '.', '' );
}

/**
 * เลขแพ็กเกจที่มีในค่าตั้ง (pkg1..pkgN ที่มีคีย์ _name ใน fenix_defaults)
 */
function fenix_seo_package_numbers() {
	$d   = fenix_defaults();
	$out = array();
	for ( $i = 1; $i <= 9; $i++ ) {
		if ( array_key_exists( 'pkg' . $i . '_name', $d ) ) {
			$out[] = $i;
		}
	}
	return $out;
}

/**
 * แพ็กเกจนี้แสดงบนหน้าเว็บหรือไม่ · ไม่มีชื่อ = เทมเพลตไม่แสดง
 * ถ้าโมดูลหน้าแพ็กเกจเพิ่มสวิตช์ pkgN_show / pkgN_hide ภายหลัง จะเคารพค่านั้นเอง
 */
function fenix_seo_package_visible( $n ) {
	$d       = fenix_defaults();
	$visible = '' !== trim( wp_strip_all_tags( (string) fenix_mod( 'pkg' . $n . '_name' ) ) );
	if ( $visible && array_key_exists( 'pkg' . $n . '_show', $d ) ) {
		$visible = (bool) fenix_mod( 'pkg' . $n . '_show' );
	}
	if ( $visible && array_key_exists( 'pkg' . $n . '_hide', $d ) ) {
		$visible = ! fenix_mod( 'pkg' . $n . '_hide' );
	}
	return (bool) apply_filters( 'fenix_seo_package_visible', $visible, $n );
}

/**
 * Offer[] ของสินค้า · ออกเฉพาะเมื่อครบทุกข้อ
 * 1) หน้าเว็บแสดงราคาจริง (pricing_mode = price)
 * 2) เจ้าของติ๊กยืนยันราคาแล้ว (seo_pricing_confirmed หรือฟิลเตอร์ fenix_seo_emit_offers)
 * 3) Offer ละ 1 แพ็กเกจ เฉพาะแพ็กเกจที่แสดงอยู่และราคาเป็นตัวเลขมากกว่า 0 (ราคาอื่นข้ามเสมอ)
 * ไม่ครบ = คืน null → node ไม่มีคีย์ offers (ยังเป็น schema.org ที่ถูกต้อง)
 *
 * @return array|null
 */
function fenix_seo_product_offers() {
	if ( 'price' !== fenix_mod( 'pricing_mode' ) ) {
		return null;
	}
	if ( ! (bool) apply_filters( 'fenix_seo_emit_offers', (bool) fenix_mod( 'seo_pricing_confirmed' ) ) ) {
		return null;
	}

	$currency = (string) apply_filters( 'fenix_seo_price_currency', 'THB' );
	/* หน้าแพ็กเกจยังเป็นร่าง/ไม่มี = ชี้หน้าแรกแทน · ไม่ส่ง URL ที่เปิดแล้ว 404 ให้ Google */
	$url = function_exists( 'fenix_published_page_url' ) ? fenix_published_page_url( 'pricing' ) : '';
	if ( ! $url ) {
		$url = home_url( '/' );
	}

	$offers = array();
	foreach ( fenix_seo_package_numbers() as $n ) {
		if ( ! fenix_seo_package_visible( $n ) ) {
			continue;
		}
		$price = fenix_seo_numeric_price( fenix_mod( 'pkg' . $n . '_price' ) );
		if ( null === $price ) {
			continue;
		}
		$offers[] = array(
			'@type'         => 'Offer',
			'name'          => wp_strip_all_tags( (string) fenix_mod( 'pkg' . $n . '_name' ) ),
			'price'         => fenix_seo_schema_price( $price ),
			'priceCurrency' => $currency,
			'availability'  => 'https://schema.org/InStock',
			'url'           => $url,
		);
	}

	return $offers ? $offers : null;
}

/**
 * ลิงก์โปรไฟล์ของแบรนด์สำหรับ sameAs (ค่าจาก Customizer ส่วนช่องทางติดต่อ)
 * ไม่รวม LINE: หน้าเพิ่มเพื่อนไม่ได้บอกว่าเป็นบริษัทใด จึงไม่ใช่โปรไฟล์ของแบรนด์
 */
function fenix_seo_same_as() {
	$keys = (array) apply_filters( 'fenix_seo_same_as_keys', array( 'facebook_url', 'instagram_url', 'tiktok_url', 'youtube_url' ) );
	$out  = array();
	foreach ( $keys as $key ) {
		$url = trim( (string) fenix_mod( $key ) );
		if ( '' !== $url && preg_match( '#^https?://#i', $url ) ) {
			$out[] = esc_url_raw( $url );
		}
	}
	return array_values( array_unique( array_filter( $out ) ) );
}

/**
 * Organization node ของแบรนด์ (ไม่มี @context เพื่อฝังในกราฟอื่นได้)
 */
function fenix_seo_organization_node() {
	$org  = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/' ) . '#organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
		'logo'  => fenix_logo_url(),
	);
	$same = fenix_seo_same_as();
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	return $org;
}

/**
 * Yoast จะออก Organization (พร้อมโลโก้) เองหรือไม่
 */
function fenix_seo_yoast_prints_organization() {
	if ( ! class_exists( 'WPSEO_Options' ) ) {
		return false;
	}
	if ( false === WPSEO_Options::get( 'enable_schema', true ) ) {
		return false;
	}
	return 'company' === WPSEO_Options::get( 'company_or_person' )
		&& '' !== trim( (string) WPSEO_Options::get( 'company_name' ) )
		&& (int) WPSEO_Options::get( 'company_logo_id' ) > 0;
}

/**
 * publisher ของ node สินค้า
 * - Yoast ออก Organization แน่นอน → อ้าง @id (กราฟรวมเป็นตัวตนเดียว)
 * - มีปลั๊กอิน SEO แต่ไม่แน่ใจว่าออก Organization → ฝัง node เต็ม (@id เดียวกัน ไม่เกิด node ซ้ำ)
 * - ไม่มีปลั๊กอิน → ธีมออก Organization เอง (fenix_seo_site_schema) → อ้าง @id
 */
function fenix_seo_publisher_ref() {
	if ( fenix_seo_yoast_prints_organization() || ! fenix_has_seo_plugin() ) {
		return array( '@id' => home_url( '/' ) . '#organization' );
	}
	return fenix_seo_organization_node();
}

/**
 * หน้าที่ควรมี node สินค้า: หน้าแรกและหน้าแพ็กเกจ
 */
function fenix_seo_is_product_page() {
	if ( is_front_page() || is_page( 'pricing' ) ) {
		return true;
	}
	return function_exists( 'is_page_template' ) && is_page_template( 'template-pricing.php' );
}

/**
 * SoftwareApplication node · ไม่มี aggregateRating/review จนกว่าจะมีรีวิวจริง (ห้ามแต่ง)
 * จึงยังไม่ได้ rich result แต่ช่วยระบุตัวตนของสินค้าให้เสิร์ชเอนจิน
 */
function fenix_seo_product_schema() {
	$name = trim( wp_strip_all_tags( (string) fenix_mod( 'seo_product_name' ) ) );
	if ( '' === $name ) {
		$name = get_bloginfo( 'name' );
	}

	$page_url = home_url( '/' );
	if ( ! is_front_page() ) {
		$permalink = get_permalink();
		if ( $permalink ) {
			$page_url = $permalink;
		}
	}

	$node = array(
		'@context'               => 'https://schema.org',
		'@type'                  => 'SoftwareApplication',
		'@id'                    => home_url( '/' ) . '#product',
		'name'                   => $name,
		'applicationCategory'    => 'FinanceApplication',
		'applicationSubCategory' => 'Expert Advisor',
		'url'                    => home_url( '/' ),
		'mainEntityOfPage'       => $page_url,
		'publisher'              => fenix_seo_publisher_ref(),
		'image'                  => fenix_share_image(),
		'inLanguage'             => 'th',
	);

	$map = array(
		'seo_product_os'           => 'operatingSystem',
		'seo_product_requirements' => 'softwareRequirements',
		'seo_product_version'      => 'softwareVersion',
	);
	foreach ( $map as $key => $prop ) {
		$value = trim( wp_strip_all_tags( (string) fenix_mod( $key ) ) );
		if ( '' !== $value ) {
			$node[ $prop ] = $value;
		}
	}

	$desc = trim( wp_strip_all_tags( (string) fenix_mod( 'og_default_description' ) ) );
	if ( '' !== $desc ) {
		$node['description'] = $desc;
	}

	$offers = fenix_seo_product_offers();
	if ( null !== $offers ) {
		$node['offers'] = $offers;
	}

	return apply_filters( 'fenix_seo_product_schema', $node );
}

function fenix_schema_extra() {
	$faqs = array();

	/* FAQ หน้าแรก (Customizer) */
	if ( is_front_page() && fenix_mod( 'show_faq' ) ) {
		for ( $i = 1; $i <= 10; $i++ ) {
			$q = trim( wp_strip_all_tags( (string) fenix_mod( 'faq' . $i . '_q' ) ) );
			$a = trim( wp_strip_all_tags( (string) fenix_mod( 'faq' . $i . '_a' ) ) );
			if ( '' !== $q && '' !== $a ) {
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

	/* SoftwareApplication (หน้าแรก + แพ็กเกจ) · ออกทั้งตอนมีและไม่มีปลั๊กอิน SEO */
	if ( fenix_seo_is_product_page() ) {
		fenix_jsonld( fenix_seo_product_schema() );
	}

	if ( fenix_has_seo_plugin() ) {
		return;
	}

	/* BreadcrumbList (ปลั๊กอิน SEO ออกให้เองอยู่แล้ว) · ตรงกับ nav.crumbs บนหัวเพจ · /go/ เป็นหน้าเดี่ยวไม่มี breadcrumb จึงข้าม */
	if ( ! is_front_page() && ! fenix_seo_is_go_page() && ( is_singular() || is_home() ) ) {
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

/* ==============================================================
 * 2) Organization + WebSite (ไม่มีปลั๊กอิน) · sameAs เข้า Yoast (มีปลั๊กอิน)
 * ============================================================== */

/**
 * แทน fenix_schema_jsonld() ใน functions.php: เพิ่ม @id และ sameAs ครบทุกช่องโซเชียล
 */
function fenix_seo_site_schema() {
	if ( fenix_has_seo_plugin() ) {
		return;
	}
	fenix_jsonld( array_merge( array( '@context' => 'https://schema.org' ), fenix_seo_organization_node() ) );
	fenix_jsonld(
		array(
			'@context'   => 'https://schema.org',
			'@type'      => 'WebSite',
			'@id'        => home_url( '/' ) . '#website',
			'name'       => get_bloginfo( 'name' ),
			'url'        => home_url( '/' ),
			'inLanguage' => 'th',
			'publisher'  => array( '@id' => home_url( '/' ) . '#organization' ),
		)
	);
}
remove_action( 'wp_head', 'fenix_schema_jsonld', 6 );
add_action( 'wp_head', 'fenix_seo_site_schema', 6 );

/**
 * เติมลิงก์โซเชียลจาก Customizer เข้า sameAs ของ Organization ที่ Yoast ออก
 * (Yoast ไม่อ่านช่องของธีม เจ้าของจึงกรอกที่ Customizer ที่เดียวพอ)
 */
function fenix_seo_yoast_org_same_as( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	$same = ( isset( $data['sameAs'] ) && is_array( $data['sameAs'] ) ) ? $data['sameAs'] : array();
	$same = array_values( array_unique( array_merge( $same, fenix_seo_same_as() ) ) );
	if ( $same ) {
		$data['sameAs'] = $same;
	}
	return $data;
}
add_filter( 'wpseo_schema_organization', 'fenix_seo_yoast_org_same_as' );

/* ==============================================================
 * 3) รูปแชร์ + Open Graph (ไม่มีปลั๊กอิน)
 * ============================================================== */

/**
 * รูปแชร์เริ่มต้น (Customizer → SEO หรือการ์ดแชร์ของธีม)
 */
function fenix_share_image() {
	$img = fenix_mod( 'og_default_image' );
	return $img ? $img : get_template_directory_uri() . '/assets/img/brand/falcon-pro-share-1200x630.jpg';
}

/**
 * Alt กลางที่ FALCON Setup ใส่ให้รูปตั้งต้นทุกรูป (inc/setup.php) · ไม่ได้บอกว่ารูปนั้นเป็นรูปอะไร จึงถือว่า "ยังไม่มี Alt"
 * ใช้ฟิลเตอร์เดียวกับ fenix_pages_featured_alt() ของโมดูลหน้าเนื้อหา · เพิ่มข้อความกลางอื่นที่ฟิลเตอร์นี้แล้วมีผลทั้งสองที่
 */
function fenix_seo_is_generic_alt( $alt ) {
	$generic = (array) apply_filters( 'fenix_pages_generic_alts', array( 'FALCON PRO EA ผู้ช่วยเทรดอัตโนมัติสำหรับ MT5 (ภาพประกอบ)' ) );
	return in_array( trim( (string) $alt ), array_map( 'trim', array_map( 'strval', $generic ) ), true );
}

/**
 * ข้อความ Alt ของรูปจาก Media Library
 * $image = id ของไฟล์ หรือ URL ของรูป · ไม่มี Alt หรือเป็น Alt กลางของ Setup → คืน $fallback
 */
function fenix_seo_image_alt( $image, $fallback = '' ) {
	$id = 0;
	if ( is_numeric( $image ) ) {
		$id = (int) $image;
	} elseif ( is_string( $image ) && '' !== $image && function_exists( 'attachment_url_to_postid' ) ) {
		$id = (int) attachment_url_to_postid( $image );
	}
	if ( $id ) {
		$alt = trim( wp_strip_all_tags( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
		if ( '' !== $alt && ! fenix_seo_is_generic_alt( $alt ) ) {
			return $alt;
		}
	}
	return trim( wp_strip_all_tags( (string) $fallback ) );
}

/**
 * รูปเด่นของบทความในการ์ด/หน้าอื่นที่ส่ง alt = ชื่อบทความมา → ใช้ Alt Text จาก Media Library แทนถ้ามี
 * Alt กลางของ Setup ไม่นับ (คงชื่อบทความไว้ ตรงกับ fenix_pages_featured_alt())
 */
function fenix_seo_thumbnail_alt( $attr, $attachment ) {
	/* alt ว่างที่ตั้งใจใส่ (รูปตกแต่ง) หรือ alt ที่เขียนมาเฉพาะ = ไม่แตะ */
	if ( ! is_array( $attr ) || ! isset( $attr['alt'] ) || ! is_object( $attachment ) || empty( $attachment->ID ) ) {
		return $attr;
	}
	$given = trim( (string) $attr['alt'] );
	if ( '' === $given ) {
		return $attr;
	}
	$titles = array();
	foreach ( array_unique( array( (int) get_the_ID(), ! empty( $attachment->post_parent ) ? (int) $attachment->post_parent : 0 ) ) as $fenix_pid ) {
		if ( $fenix_pid ) {
			$fenix_title = trim( wp_strip_all_tags( (string) get_the_title( $fenix_pid ) ) );
			$titles[]    = $fenix_title;
			$titles[]    = esc_attr( $fenix_title );
		}
	}
	if ( ! in_array( $given, $titles, true ) ) {
		return $attr;
	}
	$alt = fenix_seo_image_alt( (int) $attachment->ID );
	if ( '' !== $alt ) {
		$attr['alt'] = $alt;
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'fenix_seo_thumbnail_alt', 20, 2 );

/**
 * meta description ของหน้าปัจจุบัน (ใช้เมื่อไม่มีปลั๊กอิน SEO)
 */
function fenix_meta_description() {
	/* หน้ารวมบทความ (is_home ไม่ใช่ singular) · ใช้คำอธิบายของเพจ /articles/ ที่ Setup ใส่ไว้ แล้วจึงคำโปรยของหน้า ไม่ซ้ำกับหน้าแรก */
	if ( is_home() && ! is_front_page() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		if ( $posts_page ) {
			$desc = trim( wp_strip_all_tags( (string) get_post_meta( $posts_page, 'fenix_meta_description', true ) ) );
			if ( '' !== $desc ) {
				return $desc;
			}
			$page = get_post( $posts_page );
			if ( $page && ! empty( $page->post_excerpt ) ) {
				return trim( wp_strip_all_tags( $page->post_excerpt ) );
			}
		}
		$sub = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) fenix_mod( 'blog_subtitle' ) ) ) );
		if ( '' !== $sub && ! ( function_exists( 'fenix_is_placeholder' ) && fenix_is_placeholder( $sub ) ) ) {
			return $sub;
		}
		return (string) fenix_mod( 'og_default_description' );
	}
	if ( is_singular() ) {
		$id   = get_queried_object_id();
		$desc = trim( wp_strip_all_tags( (string) get_post_meta( $id, 'fenix_meta_description', true ) ) );
		if ( '' !== $desc ) {
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
 * og:locale ต้องอยู่ในรูป ภาษา_ประเทศ (th_TH) · WordPress ภาษาไทยคืน get_locale() = "th" ซึ่ง Facebook ไม่รับ
 */
function fenix_seo_og_locale() {
	$locale = str_replace( '-', '_', trim( (string) get_locale() ) );
	if ( preg_match( '/^[a-z]{2}$/i', $locale ) ) {
		$map    = array(
			'th' => 'th_TH',
			'en' => 'en_US',
			'ja' => 'ja_JP',
			'zh' => 'zh_CN',
		);
		$lower  = strtolower( $locale );
		$locale = isset( $map[ $lower ] ) ? $map[ $lower ] : $lower . '_' . strtoupper( $lower );
	} elseif ( preg_match( '/^([a-z]{2,3})_([a-z]{2})/i', $locale, $m ) ) {
		$locale = strtolower( $m[1] ) . '_' . strtoupper( $m[2] );
	}
	return (string) apply_filters( 'fenix_seo_og_locale', '' !== $locale ? $locale : 'th_TH' );
}

/**
 * Open Graph / Twitter (แทน fenix_open_graph() ใน functions.php)
 * ต่างจากเดิม: og:image:alt มาจาก Alt Text ใน Media Library · หน้าแรกใช้ชื่อเว็บเป็น og:title
 */
function fenix_seo_open_graph() {
	if ( fenix_has_seo_plugin() ) {
		return;
	}

	$site        = get_bloginfo( 'name' );
	$default_img = fenix_share_image();
	$img         = '';
	$img_alt     = '';

	if ( is_singular() && ! is_front_page() ) {
		$post_id = get_queried_object_id();
		$title   = wp_strip_all_tags( get_the_title( $post_id ) );
		$desc    = fenix_meta_description();
		$url     = get_permalink( $post_id );
		$thumb   = function_exists( 'get_post_thumbnail_id' ) ? (int) get_post_thumbnail_id( $post_id ) : 0;
		if ( $thumb ) {
			$img     = (string) get_the_post_thumbnail_url( $post_id, 'full' );
			$img_alt = fenix_seo_image_alt( $thumb, $title );
		}
		$type = is_singular( 'post' ) ? 'article' : 'website';
	} else {
		$title = is_front_page() ? $site : wp_strip_all_tags( wp_get_document_title() );
		$desc  = ( is_front_page() || is_home() ) ? fenix_meta_description() : fenix_mod( 'og_default_description' );
		$url   = home_url( '/' );
		$type  = 'website';
		if ( ! is_front_page() && is_home() && get_option( 'page_for_posts' ) ) {
			$url = get_permalink( (int) get_option( 'page_for_posts' ) );
		} elseif ( is_category() || ( function_exists( 'is_tag' ) && is_tag() ) || ( function_exists( 'is_tax' ) && is_tax() ) ) {
			$fenix_term_link = get_term_link( get_queried_object() );
			if ( is_string( $fenix_term_link ) ) {
				$url = $fenix_term_link;
			}
		}
	}

	if ( '' === $img ) {
		$img     = $default_img;
		$img_alt = fenix_seo_image_alt( fenix_mod( 'og_default_image' ), $site );
	}

	$desc = trim( (string) $desc );

	if ( '' !== $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	$tags = array(
		'og:site_name'   => $site,
		'og:locale'      => fenix_seo_og_locale(),
		'og:type'        => $type,
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $url,
		'og:image'       => $img,
	);
	foreach ( $tags as $property => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}
		printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $value ) );
	}
	if ( '' !== (string) $img && '' !== $img_alt ) {
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $img_alt ) );
	}

	if ( 'article' === $type ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}

	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( '' !== $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( '' !== (string) $img ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_attr( $img ) );
		if ( '' !== $img_alt ) {
			printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $img_alt ) );
		}
	}
}
remove_action( 'wp_head', 'fenix_open_graph', 5 );
add_action( 'wp_head', 'fenix_seo_open_graph', 5 );

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
	echo '<meta name="theme-color" content="#172125">' . "\n";
}
add_action( 'wp_head', 'fenix_favicon_fallback', 3 );

/* ==============================================================
 * 4) robots: noindex หน้าบาง/ซ้ำ + ตัดออกจาก sitemap
 * ============================================================== */

/**
 * จำนวนบทความที่เผยแพร่แล้ว (ใช้ตัดสิน noindex ของ /articles/)
 */
function fenix_seo_published_post_count() {
	static $count = null;
	if ( null === $count ) {
		$counts = function_exists( 'wp_count_posts' ) ? wp_count_posts( 'post' ) : null;
		$count  = ( $counts && isset( $counts->publish ) ) ? (int) $counts->publish : 0;
	}
	return $count;
}

/**
 * หน้านี้คือ /go/ (ลิงก์รวมสำหรับโปรไฟล์โซเชียล/ยิงแอด) หรือไม่
 */
function fenix_seo_is_go_page() {
	if ( is_page( 'go' ) ) {
		return true;
	}
	return function_exists( 'is_page_template' ) && is_page_template( 'template-go.php' );
}

/**
 * เหตุผลที่หน้าปัจจุบันควร noindex · '' = ปล่อยตามค่าปกติ
 * go / articles-empty / search / author / date / empty-term (แก้เพิ่มได้ด้วยฟิลเตอร์ fenix_seo_noindex_reason)
 */
function fenix_seo_noindex_reason() {
	$reason = '';
	if ( is_front_page() ) {
		$reason = '';
	} elseif ( fenix_seo_is_go_page() ) {
		$reason = apply_filters( 'fenix_seo_noindex_go', true ) ? 'go' : '';
	} elseif ( is_home() ) {
		$reason = 0 === fenix_seo_published_post_count() ? 'articles-empty' : '';
	} elseif ( is_search() ) {
		$reason = 'search';
	} elseif ( function_exists( 'is_author' ) && is_author() ) {
		$reason = 'author';
	} elseif ( function_exists( 'is_date' ) && is_date() ) {
		$reason = 'date';
	} elseif ( is_category() || ( function_exists( 'is_tag' ) && is_tag() ) || ( function_exists( 'is_tax' ) && is_tax() ) ) {
		$term = get_queried_object();
		if ( is_object( $term ) && isset( $term->count, $term->term_id ) && 0 === (int) $term->count ) {
			$reason = 'empty-term';
		}
	}
	return (string) apply_filters( 'fenix_seo_noindex_reason', $reason );
}

/**
 * wp_robots ของคอร์ · Yoast (WP 5.7+) รวมค่าจากฟิลเตอร์นี้เข้า meta robots แท็กเดียวของมันเอง
 * ไม่เติม follow ถ้ามี nofollow อยู่แล้ว (เช่นตอนปิด Search Engine Visibility) กันค่าขัดกันเอง
 */
function fenix_seo_wp_robots( $robots ) {
	if ( '' === fenix_seo_noindex_reason() ) {
		return $robots;
	}
	$robots            = is_array( $robots ) ? $robots : array();
	$robots['noindex'] = true;
	unset( $robots['index'] );
	if ( empty( $robots['nofollow'] ) ) {
		$robots['follow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'fenix_seo_wp_robots', 20 );

/**
 * เงื่อนไขเดียวกันผ่านฟิลเตอร์ของ Yoast / Rank Math (อาร์เรย์คีย์ 'index' => 'noindex')
 * เป็นค่าในอาร์เรย์เดียวกับที่ปลั๊กอินพิมพ์ จึงไม่เกิดแท็กซ้ำ
 */
function fenix_seo_plugin_robots( $robots ) {
	if ( is_array( $robots ) && '' !== fenix_seo_noindex_reason() ) {
		$robots['index'] = 'noindex';
	}
	return $robots;
}
add_filter( 'wpseo_robots_array', 'fenix_seo_plugin_robots', 20 );
add_filter( 'rank_math/frontend/robots', 'fenix_seo_plugin_robots', 20 );

/**
 * id ของเพจที่ noindex ตามกฎด้านบน (/go/ และ /articles/ ที่ยังว่าง) · ใช้ตัดออกจาก sitemap
 */
function fenix_seo_noindex_page_ids() {
	$ids = array();
	if ( apply_filters( 'fenix_seo_noindex_go', true ) ) {
		$go = get_page_by_path( 'go' );
		if ( $go && ! empty( $go->ID ) ) {
			$ids[] = (int) $go->ID;
		}
		// ทุกเพจที่ใช้เทมเพลต /go (noindex ใช้เงื่อนไขเดียวกัน)
		$tpl_pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => 20,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => 'template-go.php', // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		foreach ( (array) $tpl_pages as $tpl_id ) {
			$ids[] = (int) $tpl_id;
		}
	}
	if ( 0 === fenix_seo_published_post_count() ) {
		$ids[] = (int) get_option( 'page_for_posts' );
	}
	return array_values( array_unique( array_filter( $ids ) ) );
}

/* sitemap ของ Yoast สร้างจากตาราง indexable ไม่ใช่ตอน render หน้า → ต้องตัดออกเองให้ตรงกับ noindex */
function fenix_seo_exclude_from_yoast_sitemap( $excluded ) {
	$excluded = is_array( $excluded ) ? array_map( 'intval', $excluded ) : array();
	return array_values( array_unique( array_merge( $excluded, fenix_seo_noindex_page_ids() ) ) );
}
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'fenix_seo_exclude_from_yoast_sitemap' );

function fenix_seo_exclude_from_core_sitemap( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}
	$ids = fenix_seo_noindex_page_ids();
	if ( $ids ) {
		$existing             = ( isset( $args['post__not_in'] ) && is_array( $args['post__not_in'] ) ) ? array_map( 'intval', $args['post__not_in'] ) : array();
		$args['post__not_in'] = array_values( array_unique( array_merge( $existing, $ids ) ) );
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'fenix_seo_exclude_from_core_sitemap', 10, 2 );

/* sitemap ของคอร์มีรายชื่อผู้ใช้ (เปิดเผย username ผู้ดูแล) → ปิด */
function fenix_seo_core_sitemap_providers( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'fenix_seo_core_sitemap_providers', 10, 2 );

/**
 * canonical ของหน้า archive (หมวด/แท็ก/archive ของ post type) ที่คอร์ไม่ออกให้ · เฉพาะไม่มีปลั๊กอิน
 */
function fenix_seo_archive_canonical() {
	if ( fenix_has_seo_plugin() ) {
		return;
	}
	$url = '';
	if ( is_category() || ( function_exists( 'is_tag' ) && is_tag() ) || ( function_exists( 'is_tax' ) && is_tax() ) ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$url = $link;
			}
		}
	} elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_post_type() );
	}
	if ( ! $url ) {
		return;
	}
	$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged > 1 ) {
		$url = trailingslashit( $url ) . 'page/' . $paged . '/';
	}
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'fenix_seo_archive_canonical' );

/**
 * บรรทัด Sitemap ของคอร์ใน robots.txt · เฉพาะไม่มีปลั๊กอิน SEO และเว็บเปิดให้ index
 * คอร์เติมบรรทัดนี้เองในบางกรณี จึงเช็กก่อนกันซ้ำ · esc_url_raw เพราะ robots.txt เป็น plain text
 */
function fenix_seo_robots_txt( $output, $public ) {
	if ( '1' !== (string) $public || fenix_has_seo_plugin() ) {
		return $output;
	}
	if ( false !== strpos( (string) $output, 'wp-sitemap.xml' ) ) {
		return $output;
	}
	/* sitemap ของคอร์ถูกปิด (ฟิลเตอร์ wp_sitemaps_enabled) = ไม่ชี้ไป URL ที่จะได้ 404 */
	if ( function_exists( 'wp_sitemaps_get_server' ) ) {
		$server = wp_sitemaps_get_server();
		if ( is_object( $server ) && method_exists( $server, 'sitemaps_enabled' ) && ! $server->sitemaps_enabled() ) {
			return $output;
		}
	}
	if ( '' !== trim( (string) $output ) && "\n" !== substr( $output, -1 ) ) {
		$output .= "\n";
	}
	return $output . 'Sitemap: ' . esc_url_raw( home_url( '/wp-sitemap.xml' ) ) . "\n";
}
add_filter( 'robots_txt', 'fenix_seo_robots_txt', 10, 2 );

/* ==============================================================
 * 5) ยืนยันเจ้าของเว็บ: Google Search Console / Bing
 * ============================================================== */

/**
 * ตัดให้เหลือเฉพาะตัวรหัส (รองรับกรณีวางมาทั้ง content="...")
 */
function fenix_seo_verify_token( $value ) {
	$value = trim( (string) $value );
	if ( preg_match( '/content\s*=\s*["\']?([^"\'\s>]+)/i', $value, $m ) ) {
		$value = $m[1];
	}
	return (string) preg_replace( '/[^A-Za-z0-9_\-.:=+\/]/', '', $value );
}

/**
 * ค่ายืนยันเดียวกันใน Yoast (SEO → ตั้งค่า → Site connections) · ตั้งไว้แล้ว = ธีมไม่พิมพ์ซ้ำ
 */
function fenix_seo_yoast_has_verify( $option ) {
	return class_exists( 'WPSEO_Options' ) && '' !== trim( (string) WPSEO_Options::get( $option ) );
}

function fenix_seo_verification_meta() {
	$gsc  = fenix_seo_verify_token( fenix_mod( 'seo_gsc_verify' ) );
	$bing = fenix_seo_verify_token( fenix_mod( 'seo_bing_verify' ) );
	if ( '' !== $gsc && ! fenix_seo_yoast_has_verify( 'googleverify' ) ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $gsc ) );
	}
	if ( '' !== $bing && ! fenix_seo_yoast_has_verify( 'msverify' ) ) {
		printf( '<meta name="msvalidate.01" content="%s">' . "\n", esc_attr( $bing ) );
	}
}
add_action( 'wp_head', 'fenix_seo_verification_meta', 1 );

/**
 * ตอบไฟล์ยืนยันของ Search Console (แบบ URL prefix) โดยไม่ต้องอัปโหลดไฟล์เข้ารากเว็บ
 * รับเฉพาะชื่อรูปแบบ googleXXXXXXXX.html กันช่องนี้ถูกใช้เสิร์ฟ path อื่น
 */
function fenix_seo_search_console_file() {
	$name = basename( trim( (string) fenix_mod( 'seo_gsc_file' ) ) );
	if ( '' === $name || ! preg_match( '/^google[0-9a-f]{8,32}\.html$/', $name ) ) {
		return;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ใช้เทียบ path อย่างเดียว
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$home = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
	if ( $home . '/' . $name !== $path ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/html; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex' );
	echo 'google-site-verification: ' . esc_html( $name );
	exit;
}
add_action( 'init', 'fenix_seo_search_console_file', 0 );

/* ==============================================================
 * 6) meta SEO รายหน้าของ Yoast ผ่าน REST (PUT /wp/v2/pages/<id> {"meta":{...}})
 * ============================================================== */

/**
 * ลงทะเบียน meta ของ Yoast ให้ REST อ่าน/เขียนได้ (ปกติเป็น meta ที่ขึ้นต้นด้วย _ จึงถูกซ่อน)
 * มี Yoast = sanitize_callback null ปล่อยให้ Yoast จัดการค่าเอง (รวม %%variables%%)
 * _yoast_wpseo_meta-robots-noindex: "1" = noindex · "2" = index · "" = ตามค่าเริ่มต้นของชนิดเนื้อหา
 */
function fenix_seo_register_yoast_meta_rest() {
	$keys = array(
		'_yoast_wpseo_title',
		'_yoast_wpseo_metadesc',
		'_yoast_wpseo_focuskw',
		'_yoast_wpseo_opengraph-title',
		'_yoast_wpseo_opengraph-description',
		'_yoast_wpseo_twitter-title',
		'_yoast_wpseo_twitter-description',
		'_yoast_wpseo_meta-robots-noindex',
		'_yoast_wpseo_meta-robots-nofollow',
	);
	$args = array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => defined( 'WPSEO_VERSION' ) ? null : 'sanitize_text_field',
		'auth_callback'     => 'fenix_seo_can_edit_meta',
	);
	foreach ( array( 'post', 'page' ) as $post_type ) {
		foreach ( $keys as $key ) {
			register_post_meta( $post_type, $key, $args );
		}
	}
}
add_action( 'init', 'fenix_seo_register_yoast_meta_rest', 20 );

/**
 * สิทธิ์อ่าน/เขียน meta SEO ผ่าน REST = สิทธิ์แก้ไขโพสต์นั้นจริง ๆ (ไม่ใช่ capability กว้าง ๆ)
 */
function fenix_seo_can_edit_meta( $allowed, $meta_key = '', $object_id = 0 ) {
	unset( $allowed, $meta_key );
	$object_id = (int) $object_id;
	if ( ! $object_id ) {
		return current_user_can( 'edit_posts' );
	}
	return current_user_can( 'edit_post', $object_id );
}

/* ==============================================================
 * 7) URL ซ้ำ / redirect / oEmbed
 * ============================================================== */

/**
 * พารามิเตอร์ติดตามผลของ request ปัจจุบัน (utm_*, gclid, fbclid, msclkid, ttclid, ref) ไว้ต่อท้ายปลายทาง redirect
 * ไม่ส่งต่อทั้ง query เพราะคีย์อย่าง page_id / p / s เปลี่ยนสิ่งที่ WordPress แสดงได้
 */
function fenix_seo_request_tracking_query() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return '';
	}
	$query = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_QUERY ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( '' === $query ) {
		return '';
	}
	$args = array();
	wp_parse_str( $query, $args );
	$keep = array();
	foreach ( (array) $args as $key => $value ) {
		if ( is_string( $key ) && ! is_array( $value ) && preg_match( '/^(utm_[a-z_]+|gclid|fbclid|msclkid|ttclid|ref)$/i', $key ) ) {
			$keep[ $key ] = (string) $value;
		}
	}
	return $keep ? build_query( $keep ) : '';
}

/**
 * ตาราง 301 สำหรับ slug ที่เลิกใช้ในอนาคต · ตอนนี้ว่าง (FALCON ไม่มี URL เก่า)
 * เพิ่มได้ด้วย add_filter( 'fenix_redirect_map', fn( $m ) => $m + array( '/old-slug/' => '/new-slug/' ) )
 */
function fenix_seo_redirect_map() {
	$map = apply_filters( 'fenix_redirect_map', array() );
	return is_array( $map ) ? $map : array();
}

function fenix_seo_legacy_redirects() {
	$map = fenix_seo_redirect_map();
	if ( ! $map || is_admin() || is_preview() || is_customize_preview() || wp_doing_ajax() ) {
		return;
	}
	if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( ! empty( $_SERVER['REQUEST_METHOD'] ) && ! in_array( strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ), array( 'GET', 'HEAD' ), true ) ) ) {
		return;
	}
	/* หน้าที่มีอยู่จริงชนะตารางเสมอ (เอา slug เดิมกลับมาใช้ได้โดยไม่ต้องแก้ตาราง) */
	if ( ! is_404() ) {
		return;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$home = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
	if ( '' !== $home && 0 === strpos( $path, $home ) ) {
		$path = substr( $path, strlen( $home ) );
	}
	$path = strtolower( trailingslashit( '/' . ltrim( rawurldecode( $path ), '/' ) ) );
	if ( empty( $map[ $path ] ) ) {
		return;
	}
	$target = (string) $map[ $path ];
	if ( strtolower( trailingslashit( $target ) ) === $path ) {
		return;
	}
	$url = ( 0 === strpos( $target, 'http' ) ) ? $target : home_url( $target );
	if ( strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) ) !== strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ) ) {
		return;
	}
	$query = fenix_seo_request_tracking_query();
	if ( '' !== $query ) {
		$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . $query;
	}
	wp_safe_redirect( esc_url_raw( $url ), 301 );
	exit;
}
add_action( 'template_redirect', 'fenix_seo_legacy_redirects', 1 );

/**
 * หน้าเดี่ยวที่ต่อท้าย /page/N/ → 301 กลับหน้าจริง (กัน URL ซ้ำไม่จำกัด)
 * ยกเว้นหน้ารวมบทความ และเพจที่แบ่งหน้าจริงด้วย <!--nextpage-->
 */
function fenix_seo_redirect_paged_singular() {
	if ( is_admin() || is_home() || ! is_singular() ) {
		return;
	}
	$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged < 2 ) {
		return;
	}
	$post_id = (int) get_queried_object_id();
	$post    = $post_id ? get_post( $post_id ) : null;
	if ( ! $post || false !== strpos( (string) $post->post_content, '<!--nextpage-->' ) ) {
		return;
	}
	$url   = is_front_page() ? home_url( '/' ) : get_permalink( $post );
	$query = fenix_seo_request_tracking_query();
	if ( '' !== $query ) {
		$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . $query;
	}
	wp_safe_redirect( esc_url_raw( $url ), 301 );
	exit;
}
add_action( 'template_redirect', 'fenix_seo_redirect_paged_singular', 2 );

/**
 * ห้ามแคชของโฮสต์เก็บคำตอบที่เป็น redirect
 * แคชที่ใช้คีย์ไม่รวมชื่อโดเมนอาจเก็บ 301 ของ www แล้วส่งให้โดเมนหลัก → วนไม่รู้จบ
 * (ทางถาวร: ให้ Cloudflare ย้าย www → โดเมนหลักเอง)
 */
function fenix_seo_redirect_no_store( $location ) {
	if ( $location && ! headers_sent() ) {
		nocache_headers();
		header( 'X-Accel-Expires: 0' );
	}
	return $location;
}
add_filter( 'wp_redirect', 'fenix_seo_redirect_no_store', 99 );

/**
 * oEmbed ไม่ต้องส่งชื่อ/ลิงก์ผู้เขียน (เปิดเผย slug ของบัญชีผู้ดูแล)
 */
function fenix_seo_oembed_hide_author( $data ) {
	if ( is_array( $data ) ) {
		unset( $data['author_name'], $data['author_url'] );
	}
	return $data;
}
add_filter( 'oembed_response_data', 'fenix_seo_oembed_hide_author', 20 );

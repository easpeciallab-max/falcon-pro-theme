<?php
/**
 * FALCON PRO EA · โมดูลโครงสร้างพื้นฐาน (INFRA)
 *
 * 1) REST สำหรับผู้ดูแล (namespace falcon/v1) · ยืนยันตัวตนด้วย Application Passwords ของ WordPress
 *    - กู้ header Authorization บนโฮสต์ที่ตัดทิ้ง (รับ X-Authorization เป็นทางสำรอง)
 *    - GET  /wp-json/falcon/v1/authcheck   ตรวจว่า header มาถึง PHP (คืน true/false เท่านั้น)
 *    - GET  /wp-json/falcon/v1/mods        อ่านค่า Customizer ทุกคีย์ใน fenix_defaults() (?keys=a,b · ?schema=1)
 *    - POST /wp-json/falcon/v1/mods        {"key":"value"} บันทึก (null = กลับเป็นค่าเริ่มต้น)
 *      รับเฉพาะคีย์ใน fenix_defaults() · sanitize/validate ตามชนิดช่องที่ลงทะเบียนใน Customizer
 *      (รวมฟิลเตอร์ customize_sanitize_{key} / customize_validate_{key} ของโมดูล) · ตอบ saved / rejected / errors
 *      และ warnings เมื่อลิงก์สมัครบัญชีถูกบันทึกไว้แต่ยังไม่มีชื่อโบรกเกอร์จริง (fenix_partner_pending)
 *    - ปิด /wp/v2/users และทางลัด ?author=N สำหรับผู้ที่ไม่ได้ล็อกอิน
 *    - รหัส Application Password เก็บนอก repo เสมอ (เช่น ~/.falcon-wp.env) · ใช้ผ่าน HTTPS เท่านั้น
 *    - เติมช่อง Customizer ให้คีย์กลางที่ยังไม่มีช่อง (contact_fallback_text)
 * 2) Hardening: ปิดคอมเมนต์/pingback (+ เมนูแอดมิน), XML-RPC, ลิงก์ rsd/wlw/generator,
 *    ส่ง Permissions-Policy / X-Content-Type-Options / Referrer-Policy, ลบ X-Pingback / X-Powered-By
 * 3) ลดน้ำหนักหน้าเว็บ: ปิดสคริปต์อีโมจิ, ถอด CSS ของบล็อก Gutenberg เมื่อหน้านั้นไม่ได้ใช้บล็อก
 *    + ครอบตารางที่เจ้าของพิมพ์เองในเนื้อหาด้วย .table-wrap (เลื่อนซ้ายขวาได้บนมือถือ)
 * 4) ฟอนต์ Noto Sans Thai แบบ self-host (assets/fonts · SIL OFL 1.1) แทน Google Fonts
 *    ไม่มีการเรียก fonts.googleapis.com ก่อนผู้เข้าชมให้ความยินยอม · preload 2 ไฟล์ที่ใช้ในจอแรก
 * 5) คงตัวพิมพ์ของคำที่ห้ามเป็นพิมพ์ใหญ่ (อีเมล โดเมน LINE ID ชื่อไฟล์ iPhone ...) ด้วยการห่อ span.keep-case.kc อัตโนมัติ
 *    5.1) กันตัดบรรทัดกลางคำไทย (ออเดอร์ แดชบอร์ด ...) และกลางพาธในชิป <code> ด้วย span.nb + จุดตัดบรรทัดที่ขอบคำ ในบัฟเฟอร์เดียวกัน
 *         รายการคำ = ฟิลเตอร์ fenix_nobreak_words
 * 6) อายุแคชหน้าเว็บของโฮสต์: ไม่เกิน 5 นาที (X-Accel-Expires + Cache-Control s-maxage)
 *    ไม่ส่งกับหน้า 404 คำตอบที่มี Set-Cookie หรือ no-store / private และยกเลิกเมื่อคำตอบกลายเป็นสถานะข้อผิดพลาด
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==============================================================
 * 1) REST · Authorization header + authcheck + mods
 * ============================================================== */

/**
 * กู้ header Authorization บนโฮสต์ที่ตัดทิ้ง (CGI/FastCGI/LiteSpeed/แคชหน้าเว็บ)
 * เพื่อให้ Application Passwords ใช้กับ REST ได้ · ไม่มีผลถ้า PHP เห็น header อยู่แล้ว
 * ทางสำรองสุดท้าย: ไคลเอนต์ส่ง "X-Authorization: Basic ..." แทน
 */
function fenix_restore_authorization_header() {
	if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) || ! empty( $_SERVER['PHP_AUTH_USER'] ) ) {
		return;
	}

	$header = '';
	if ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
		$header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	} elseif ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
		if ( is_array( $headers ) ) {
			foreach ( $headers as $name => $value ) {
				if ( 'authorization' === strtolower( $name ) ) {
					$header = $value;
					break;
				}
			}
		}
	}
	if ( '' === $header && ! empty( $_SERVER['HTTP_X_AUTHORIZATION'] ) ) {
		$header = $_SERVER['HTTP_X_AUTHORIZATION']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}
	if ( '' === $header || ! is_string( $header ) ) {
		return;
	}

	$_SERVER['HTTP_AUTHORIZATION'] = $header;
	if ( function_exists( 'wp_populate_basic_auth_from_authorization_header' ) ) {
		wp_populate_basic_auth_from_authorization_header();
	}
}
fenix_restore_authorization_header();

function fenix_infra_register_rest_routes() {
	register_rest_route(
		'falcon/v1',
		'/authcheck',
		array(
			'methods'             => 'GET',
			'permission_callback' => 'fenix_infra_can_manage',
			'callback'            => 'fenix_infra_authcheck',
		)
	);
	register_rest_route(
		'falcon/v1',
		'/mods',
		array(
			array(
				'methods'             => 'GET',
				'permission_callback' => 'fenix_infra_can_edit_mods',
				'callback'            => 'fenix_infra_mods_get',
			),
			array(
				'methods'             => 'POST',
				'permission_callback' => 'fenix_infra_can_edit_mods',
				'callback'            => 'fenix_infra_mods_update',
			),
		)
	);
}
add_action( 'rest_api_init', 'fenix_infra_register_rest_routes' );

function fenix_infra_can_manage() {
	return current_user_can( 'manage_options' );
}

function fenix_infra_can_edit_mods() {
	return current_user_can( 'edit_theme_options' );
}

/**
 * header Authorization มาถึง PHP หรือไม่ · คืนเฉพาะ true/false ไม่มีค่าลับ
 * ผู้ที่ยังไม่ผ่านการยืนยันตัวตนได้ 401 (บอกได้ในตัวว่า header ไม่มาถึงหรือรหัสผิด)
 */
function fenix_infra_authcheck() {
	$apache_keys = array();
	if ( function_exists( 'apache_request_headers' ) ) {
		$apache_keys = array_map( 'strtolower', array_keys( (array) apache_request_headers() ) );
	}
	return array(
		'http_authorization'     => ! empty( $_SERVER['HTTP_AUTHORIZATION'] ),
		'redirect_authorization' => ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ),
		'x_authorization'        => ! empty( $_SERVER['HTTP_X_AUTHORIZATION'] ),
		'apache_authorization'   => in_array( 'authorization', $apache_keys, true ),
		'php_auth_user_set'      => isset( $_SERVER['PHP_AUTH_USER'] ),
		'logged_in_user_id'      => get_current_user_id(),
	);
}

/**
 * ตัวเก็บข้อมูลช่องของ Customizer: เรียก fenix_customize_register() กับออบเจกต์นี้แทน WP_Customize_Manager
 * เพื่อรู้ชนิดช่อง (text/textarea/url/image/checkbox/radio) และ sanitize_callback ของทุกคีย์ จากนิยามเดียวกับหน้า Customizer
 */
class Falcon_Rest_Customizer_Spy {
	/** @var array id => array( 'type', 'sanitize', 'choices' ) */
	public $fields = array();

	public function add_setting( $id, $args = array() ) {
		if ( is_object( $id ) && isset( $id->id ) ) {
			$id = $id->id;
		}
		if ( is_string( $id ) && '' !== $id ) {
			$this->fields[ $id ] = array(
				'type'     => 'text',
				'sanitize' => isset( $args['sanitize_callback'] ) ? $args['sanitize_callback'] : '',
				'choices'  => array(),
			);
		}
		return null;
	}

	public function add_control( $id, $args = array() ) {
		if ( is_object( $id ) ) {
			$control = $id;
			$id      = isset( $control->id ) ? $control->id : '';
			$type    = ( class_exists( 'WP_Customize_Image_Control' ) && $control instanceof WP_Customize_Image_Control ) ? 'image' : ( isset( $control->type ) ? (string) $control->type : 'text' );
			$choices = isset( $control->choices ) ? (array) $control->choices : array();
		} else {
			$type    = isset( $args['type'] ) ? (string) $args['type'] : 'text';
			$choices = isset( $args['choices'] ) ? (array) $args['choices'] : array();
		}
		if ( is_string( $id ) && isset( $this->fields[ $id ] ) ) {
			$this->fields[ $id ]['type']    = $type;
			$this->fields[ $id ]['choices'] = $choices;
		}
		return null;
	}

	public function get_setting() {
		return null;
	}

	public function __call( $name, $args ) {
		unset( $name, $args );
		return null;
	}

	public function __get( $name ) {
		unset( $name );
		return null;
	}
}

/**
 * ชนิดช่อง + sanitize ของทุกคีย์ที่ลงทะเบียนใน Customizer (แคชต่อ request)
 * ถ้าอ่านนิยามไม่ได้ (เช่นคลาสของ Customizer โหลดไม่ขึ้น) คืน array ว่าง → ใช้กฎสำรองตามชื่อคีย์
 */
function fenix_infra_customizer_fields() {
	static $fields = null;
	if ( null !== $fields ) {
		return $fields;
	}
	$fields = array();
	if ( ! function_exists( 'fenix_customize_register' ) ) {
		return $fields;
	}
	/* คลาสช่องรูปของคอร์ (fenix_customize_register ใช้ WP_Customize_Image_Control) · คอร์เองก็ require_once ไฟล์นี้ */
	if ( ! class_exists( 'WP_Customize_Image_Control' ) && defined( 'WPINC' ) && file_exists( ABSPATH . WPINC . '/class-wp-customize-control.php' ) ) {
		require_once ABSPATH . WPINC . '/class-wp-customize-control.php';
	}
	try {
		$spy = new Falcon_Rest_Customizer_Spy();
		fenix_customize_register( $spy );
		$fields = $spy->fields;
	} catch ( \Throwable $e ) {
		unset( $e );
		$fields = array();
	}
	return $fields;
}

/**
 * ตัว sanitize ของช่อง radio ที่รู้จัก (ใช้ตอนอ่านนิยามช่องจาก Customizer ไม่ได้)
 * ค่าที่ sanitize แล้วไม่เท่าค่าที่ส่งมา = ไม่อยู่ในตัวเลือก → ปฏิเสธ (ไม่แอบเปลี่ยนเป็นค่าอื่น)
 */
function fenix_infra_radio_sanitizers() {
	return (array) apply_filters(
		'fenix_rest_radio_sanitizers',
		array(
			'color_mode'   => 'fenix_sanitize_color_mode',
			'pricing_mode' => 'fenix_sanitize_pricing_mode',
		)
	);
}

/**
 * ตัวแทน WP_Customize_Setting ที่ส่งให้ฟิลเตอร์ customize_sanitize_{id} / customize_validate_{id}
 * (โมดูลอ่านได้แค่ ->id และ ->default เหมือนตอนบันทึกจากหน้า Customizer)
 */
function fenix_infra_setting_stub( $key, $default ) {
	return (object) array(
		'id'      => $key,
		'default' => $default,
		'type'    => 'theme_mod',
	);
}

/**
 * sanitize ค่าหนึ่งคีย์ให้ได้ผลเดียวกับการกดบันทึกใน Customizer · คืน array( ok, value, message )
 * ลำดับ: ตรวจชนิดช่อง → customize_validate_{key} (ค่าดิบ) → sanitize ตามชนิด → customize_sanitize_{key}
 */
function fenix_infra_mods_sanitize( $key, $value, $default ) {
	$fields  = fenix_infra_customizer_fields();
	$field   = isset( $fields[ $key ] ) ? $fields[ $key ] : null;
	$type    = $field ? $field['type'] : '';
	$radios  = fenix_infra_radio_sanitizers();
	$setting = fenix_infra_setting_stub( $key, $default );

	if ( is_bool( $default ) || 'checkbox' === $type ) {
		/* สตริง "false" / "0" ต้องได้ false (fenix_sanitize_checkbox ใช้ (bool) ซึ่งตีเป็น true) */
		if ( ! is_scalar( $value ) ) {
			return array( false, null, 'ช่องนี้รับค่า true / false' );
		}
		$result = array( true, in_array( $value, array( true, 1, '1', 'true', 'on', 'yes' ), true ), '' );
		return (array) apply_filters( 'fenix_rest_mods_sanitize', $result, $key, $value, $type ? $type : 'checkbox' );
	}
	if ( ! is_scalar( $value ) ) {
		return array( false, null, 'ช่องนี้รับเฉพาะข้อความหรือตัวเลข' );
	}

	/* ตัวตรวจของโมดูลที่ผูกกับ Customizer (เช่นรหัส GA4 / Meta Pixel) · ใช้กับค่าดิบเหมือนตอนกดเผยแพร่ */
	if ( class_exists( 'WP_Error' ) ) {
		try {
			$validity = apply_filters( "customize_validate_{$key}", new WP_Error(), $value, $setting );
		} catch ( \Throwable $e ) {
			unset( $e );
			return array( false, null, 'ตรวจค่าของช่องนี้ไม่สำเร็จ ให้แก้ผ่านหน้า Customizer แทน' );
		}
		if ( is_wp_error( $validity ) && method_exists( $validity, 'has_errors' ) && $validity->has_errors() ) {
			return array( false, null, implode( ' · ', $validity->get_error_messages() ) );
		}
	}

	if ( 'radio' === $type ) {
		$choices = array_map( 'strval', array_keys( (array) $field['choices'] ) );
		if ( ! in_array( (string) $value, $choices, true ) ) {
			return array( false, null, 'ค่าที่ใช้ได้: ' . implode( ', ', $choices ) );
		}
		$clean = (string) $value;
	} elseif ( '' === $type && isset( $radios[ $key ] ) && is_callable( $radios[ $key ] ) ) {
		$clean = (string) call_user_func( $radios[ $key ], $value );
		if ( (string) $value !== $clean ) {
			return array( false, null, 'ค่านี้ไม่อยู่ในตัวเลือกของช่อง' );
		}
	} elseif ( is_int( $default ) ) {
		$clean = absint( $value );
	} elseif ( $field && ! empty( $field['sanitize'] ) && is_callable( $field['sanitize'] ) ) {
		$clean = call_user_func( $field['sanitize'], $value );
	} elseif ( preg_match( '/(_url|_img|_img_mobile|_image|_logo|_qr)$|^(wordmark_|mt5_dl_)/', $key ) ) {
		$clean = esc_url_raw( (string) $value );
	} else {
		$clean = sanitize_textarea_field( (string) $value );
	}

	/* sanitize เพิ่มของโมดูล (priority หลังตัวกลางของ customizer.php) */
	try {
		$clean = apply_filters( "customize_sanitize_{$key}", $clean, $setting );
	} catch ( \Throwable $e ) {
		unset( $e );
		return array( false, null, 'ตรวจค่าของช่องนี้ไม่สำเร็จ ให้แก้ผ่านหน้า Customizer แทน' );
	}
	if ( null === $clean || ( class_exists( 'WP_Error' ) && is_wp_error( $clean ) ) ) {
		return array( false, null, 'ค่านี้ใช้ไม่ได้กับช่องนี้' );
	}

	/* ปรับผลสุดท้ายได้ที่นี่ (array( ok, value, message )) */
	return (array) apply_filters( 'fenix_rest_mods_sanitize', array( true, $clean, '' ), $key, $value, $type );
}

function fenix_infra_mods_get( $request ) {
	$defaults = fenix_defaults();
	$keys     = array_keys( $defaults );
	$only     = is_object( $request ) && method_exists( $request, 'get_param' ) ? (string) $request->get_param( 'keys' ) : '';
	if ( '' !== $only ) {
		$keys = array_values( array_intersect( $keys, array_map( 'trim', explode( ',', $only ) ) ) );
	}

	$out = array();
	foreach ( $keys as $key ) {
		$out[ $key ] = fenix_mod( $key );
	}

	/* ?schema=1 → ชนิดช่องและค่าเริ่มต้น (ช่วยสคริปต์เติมเนื้อหา) */
	if ( is_object( $request ) && method_exists( $request, 'get_param' ) && $request->get_param( 'schema' ) ) {
		$fields = fenix_infra_customizer_fields();
		$schema = array();
		foreach ( $keys as $key ) {
			$schema[ $key ] = array(
				'type'    => isset( $fields[ $key ] ) ? $fields[ $key ]['type'] : ( is_bool( $defaults[ $key ] ) ? 'checkbox' : 'text' ),
				'default' => $defaults[ $key ],
			);
			if ( isset( $fields[ $key ] ) && 'radio' === $fields[ $key ]['type'] ) {
				$schema[ $key ]['choices'] = array_keys( (array) $fields[ $key ]['choices'] );
			}
		}
		return array(
			'mods'   => $out,
			'schema' => $schema,
		);
	}

	return $out;
}

function fenix_infra_mods_update( $request ) {
	$defaults = fenix_defaults();
	$data     = is_object( $request ) && method_exists( $request, 'get_json_params' ) ? $request->get_json_params() : null;

	if ( ! is_array( $data ) || empty( $data ) ) {
		return new WP_Error( 'falcon_mods_empty', 'ส่ง JSON object {"key":"value"} อย่างน้อย 1 รายการ', array( 'status' => 400 ) );
	}

	$saved    = array();
	$rejected = array();
	$errors   = array();
	foreach ( $data as $key => $value ) {
		if ( ! is_string( $key ) || ! array_key_exists( $key, $defaults ) ) {
			$rejected[]              = (string) $key;
			$errors[ (string) $key ] = 'ไม่มีคีย์นี้ในค่าตั้งของธีม';
			continue;
		}
		if ( null === $value ) {
			remove_theme_mod( $key );
		} else {
			$clean = fenix_infra_mods_sanitize( $key, $value, $defaults[ $key ] );
			if ( empty( $clean[0] ) ) {
				$rejected[]     = $key;
				$errors[ $key ] = isset( $clean[2] ) && '' !== $clean[2] ? (string) $clean[2] : 'ค่านี้ใช้ไม่ได้กับช่องนี้';
				continue;
			}
			set_theme_mod( $key, $clean[1] );
		}
		$saved[ $key ] = get_theme_mod( $key, $defaults[ $key ] );
	}

	$out = array(
		'saved'    => $saved,
		'rejected' => $rejected,
		'errors'   => $errors,
	);
	/* ลิงก์สมัครบัญชีอยู่ในค่าที่บันทึกไว้ แต่ยังไม่มีชื่อโบรกเกอร์จริง: ค่าถูกบันทึกตามปกติ แต่ปุ่มสมัครและข้อความเปิดเผยยังไม่แสดงบนเว็บ
	 * (คีย์ warnings มีเฉพาะตอนที่มีเรื่องต้องเตือน สคริปต์เดิมที่อ่าน saved / rejected / errors ไม่กระทบ) */
	if ( function_exists( 'fenix_partner_pending' ) && fenix_partner_pending() ) {
		$out['warnings'] = array(
			'broker_signup_url' => 'บันทึกลิงก์สมัครบัญชีแล้ว แต่ broker_name ยังว่างหรือยังเป็นคำกลางของธีม ปุ่มสมัครและข้อความเปิดเผยความเป็นพาร์ตเนอร์จะยังไม่แสดงจนกว่าจะตั้งชื่อโบรกเกอร์จริง',
		);
	}
	return $out;
}

/**
 * ช่องใน Customizer ของคีย์กลางที่ยังไม่มีโมดูลใดสร้างช่องให้ (dev/check-settings.php ต้องไม่เหลือคีย์ตกหล่น)
 * ใส่เฉพาะเมื่อยังไม่มี section ใดมีคีย์นั้น · ถ้าโมดูลเจ้าของหมวดเพิ่มช่องเองภายหลัง ส่วนนี้จะข้ามไปเอง
 */
function fenix_infra_orphan_fields( $sections, $d ) {
	unset( $d );
	$orphans = array(
		'contact_fallback_text' => array(
			'section' => 'fenix_general',
			'after'   => 'line_url',
			'field'   => array( 'ข้อความปุ่มติดต่อเมื่อยังไม่ใส่ลิงก์ LINE', 'text', 'ปุ่มติดต่อทั่วเว็บจะพาไปหน้า /go/ (ถ้าเผยแพร่แล้ว) และใช้ข้อความนี้แทนข้อความปุ่ม LINE' ),
		),
	);
	foreach ( $orphans as $key => $spec ) {
		foreach ( $sections as $section ) {
			if ( isset( $section['fields'][ $key ] ) ) {
				continue 2;
			}
		}
		if ( ! isset( $sections[ $spec['section'] ]['fields'] ) ) {
			continue;
		}
		$fields = array();
		$placed = false;
		foreach ( $sections[ $spec['section'] ]['fields'] as $id => $field ) {
			$fields[ $id ] = $field;
			if ( $id === $spec['after'] ) {
				$fields[ $key ] = $spec['field'];
				$placed         = true;
			}
		}
		if ( ! $placed ) {
			$fields[ $key ] = $spec['field'];
		}
		$sections[ $spec['section'] ]['fields'] = $fields;
	}
	return $sections;
}
add_filter( 'fenix_customizer_sections', 'fenix_infra_orphan_fields', 90, 2 );

/**
 * ปิดรายชื่อผู้ใช้ใน REST (/wp/v2/users) สำหรับผู้ที่ไม่ได้ล็อกอิน · กันการเดา username ผู้ดูแล
 */
function fenix_infra_block_guest_user_rest( $result, $server, $request ) {
	unset( $server );
	if ( is_user_logged_in() || ! is_object( $request ) || ! method_exists( $request, 'get_route' ) ) {
		return $result;
	}
	if ( 0 === strpos( (string) $request->get_route(), '/wp/v2/users' ) ) {
		return new WP_Error( 'rest_user_directory_forbidden', 'รายชื่อผู้ใช้ไม่เปิดเป็นสาธารณะ', array( 'status' => 403 ) );
	}
	return $result;
}
add_filter( 'rest_pre_dispatch', 'fenix_infra_block_guest_user_rest', 10, 3 );

/**
 * ?author=1, ?author=2 … ทางลัดเดา username อีกทาง (คอร์ redirect ไป /author/<username>/)
 * ผู้ที่ไม่ได้ล็อกอิน → กลับหน้าแรก · ปิดได้ด้วย add_filter( 'fenix_block_author_enum', '__return_false' )
 */
function fenix_infra_block_author_enum() {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}
	// หน้า /author/<username>/ ก็เปิดเผยชื่อล็อกอินเช่นกัน (ธีมไม่มีหน้าผู้เขียน · ผู้เขียนบทความคือชื่อทีมใน Customizer)
	if ( ! isset( $_GET['author'] ) && ! is_author() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- อ่านอย่างเดียว ไม่บันทึกอะไร
		return;
	}
	if ( ! apply_filters( 'fenix_block_author_enum', true ) ) {
		return;
	}
	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'fenix_infra_block_author_enum', 1 );

/* --------------------------------------------------------------
 * ชื่อล็อกอินของผู้ดูแลต้องไม่หลุดออกหน้าเว็บ
 * WordPress ตั้ง "ชื่อที่แสดง" ของผู้ใช้ใหม่ = ชื่อล็อกอิน และปลั๊กอิน SEO ใส่ชื่อนี้ใน meta author, schema ของบทความ,
 * RSS และลิงก์ /author/<username>/ → ถ้าชื่อที่แสดงยังเท่ากับชื่อล็อกอิน ให้ใช้ชื่อผู้เขียนจาก Customizer แทน
 * (เจ้าของตั้งชื่อที่แสดงเองในโปรไฟล์เมื่อไร ธีมใช้ชื่อนั้นตามปกติ) · ปิดทั้งชุดด้วย filter fenix_block_author_enum
 * -------------------------------------------------------------- */

/**
 * ชื่อผู้เขียนที่แสดงต่อสาธารณะ: ถ้า $name คือชื่อล็อกอิน/nicename ของผู้ใช้ → ชื่อทีมจาก Customizer (หรือชื่อเว็บ)
 */
function fenix_infra_public_author_name( $name, $user ) {
	if ( ! $user || ! apply_filters( 'fenix_block_author_enum', true ) ) {
		return $name;
	}
	$shown = strtolower( trim( (string) $name ) );
	if ( '' !== $shown && strtolower( (string) $user->user_login ) !== $shown && strtolower( (string) $user->user_nicename ) !== $shown ) {
		return $name;
	}
	$brand = trim( (string) fenix_mod( 'article_author_name' ) );
	return '' !== $brand ? $brand : get_bloginfo( 'name' );
}

function fenix_infra_filter_the_author( $name ) {
	global $authordata;
	return is_admin() || ! is_object( $authordata ) ? $name : fenix_infra_public_author_name( $name, $authordata );
}
add_filter( 'the_author', 'fenix_infra_filter_the_author' );

function fenix_infra_filter_author_display_name( $name, $user_id = 0 ) {
	return is_admin() ? $name : fenix_infra_public_author_name( $name, get_userdata( (int) $user_id ) );
}
add_filter( 'get_the_author_display_name', 'fenix_infra_filter_author_display_name', 10, 2 );

/**
 * ลิงก์หน้าผู้เขียน (ใช้ใน schema/ฟีด) → หน้าเกี่ยวกับเรา หรือหน้าแรก · ไม่มี /author/<username>/ ในหน้าเว็บ
 */
function fenix_infra_filter_author_link( $link ) {
	if ( is_admin() || ! apply_filters( 'fenix_block_author_enum', true ) ) {
		return $link;
	}
	$about = function_exists( 'fenix_published_page_url' ) ? fenix_published_page_url( 'about' ) : '';
	return $about ? $about : home_url( '/' );
}
add_filter( 'author_link', 'fenix_infra_filter_author_link' );

/**
 * Yoast: meta author, schema Person ของผู้เขียน (ชื่อ + รูป Gravatar ที่ผูกกับอีเมลผู้ดูแล) และแผนผังเว็บของผู้เขียน
 */
function fenix_infra_yoast_meta_author( $name, $presentation = null ) {
	$author_id = is_object( $presentation ) && isset( $presentation->source->post_author ) ? (int) $presentation->source->post_author : 0;
	return $author_id ? fenix_infra_public_author_name( $name, get_userdata( $author_id ) ) : $name;
}
add_filter( 'wpseo_meta_author', 'fenix_infra_yoast_meta_author', 10, 2 );

function fenix_infra_yoast_person_data( $data, $user_id = 0 ) {
	$user = get_userdata( (int) $user_id );
	if ( ! $user || ! is_array( $data ) || ! isset( $data['name'] ) ) {
		return $data;
	}
	$public = fenix_infra_public_author_name( $data['name'], $user );
	if ( $public !== $data['name'] ) {
		$data['name'] = $public;
		unset( $data['image'], $data['sameAs'] );
	}
	return $data;
}
add_filter( 'wpseo_schema_person_data', 'fenix_infra_yoast_person_data', 10, 2 );

/**
 * Yoast Article schema อ่าน display_name ของผู้เขียนตรงจากฐานข้อมูล (ไม่ผ่านฟิลเตอร์ของ WordPress) จึงต้องแก้ที่ชิ้น schema เอง
 */
function fenix_infra_yoast_article_author( $data, $context = null ) {
	if ( is_array( $data ) && isset( $data['author']['name'] ) && is_object( $context ) && isset( $context->post->post_author ) ) {
		$data['author']['name'] = fenix_infra_public_author_name( $data['author']['name'], get_userdata( (int) $context->post->post_author ) );
	}
	return $data;
}
add_filter( 'wpseo_schema_article', 'fenix_infra_yoast_article_author', 10, 2 );

function fenix_infra_yoast_no_author_sitemap( $users ) {
	return apply_filters( 'fenix_block_author_enum', true ) ? array() : $users;
}
add_filter( 'wpseo_sitemap_exclude_author', 'fenix_infra_yoast_no_author_sitemap' );

/* ==============================================================
 * 2) Hardening
 * ============================================================== */

/* คอมเมนต์และ pingback: เว็บนี้ไม่เปิดให้คอมเมนต์ ปิดทุกชั้นแม้โพสต์ใดตั้งค่าหลุด */
function fenix_infra_disable_discussion() {
	foreach ( array( 'post', 'page', 'attachment' ) as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'fenix_infra_disable_discussion', 100 );
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

function fenix_infra_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'fenix_infra_remove_comments_admin_menu', 100 );

function fenix_infra_remove_comments_admin_bar( $wp_admin_bar ) {
	if ( is_object( $wp_admin_bar ) && method_exists( $wp_admin_bar, 'remove_node' ) ) {
		$wp_admin_bar->remove_node( 'comments' );
	}
}
add_action( 'admin_bar_menu', 'fenix_infra_remove_comments_admin_bar', 100 );

function fenix_infra_remove_comments_dashboard() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'fenix_infra_remove_comments_dashboard' );

/* XML-RPC + ลิงก์ค้นพบบริการที่ไม่ได้ใช้ + เลขเวอร์ชัน WordPress */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
add_filter( 'the_generator', '__return_empty_string' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );

/**
 * WordPress เดาว่าคนที่เปิด /dashboard /admin หรือ /login แล้วไม่พบหน้า กำลังหาหลังบ้าน จึงส่งต่อไป /wp-admin/ หรือหน้าล็อกอิน
 * (wp_redirect_admin_locations ที่ template_redirect ลำดับ 1000) · เว็บนี้ใช้ /dashboard/ เป็นหน้าของสมาชิก:
 * ช่วงที่เพจนั้นยังไม่ถูกสร้าง เป็นฉบับร่าง หรือถูกถอดออก ผู้อ่านต้องได้หน้า 404 ของธีม ไม่ใช่ฟอร์มล็อกอินของ WordPress
 * ถอดที่ init: ถึงตอนนั้นคอร์ลงทะเบียนตัวส่งต่อไว้แล้วแน่นอน · หาลำดับจริงด้วย has_action() เผื่อคอร์ย้าย (หาไม่ได้ = 1000)
 * ผลที่ตามมาโดยตั้งใจ: /admin และ /login ตอบ 404 เช่นกัน · เข้าหลังบ้านที่ /wp-admin/ หรือ /wp-login.php ตามเดิม
 */
function fenix_infra_no_admin_guess_redirect() {
	$priority = has_action( 'template_redirect', 'wp_redirect_admin_locations' );
	remove_action( 'template_redirect', 'wp_redirect_admin_locations', is_int( $priority ) ? $priority : 1000 );
}
add_action( 'init', 'fenix_infra_no_admin_guess_redirect' );

/**
 * header ของหน้าเว็บฝั่งผู้ชม
 */
function fenix_infra_response_headers( $headers ) {
	if ( ! is_array( $headers ) ) {
		$headers = array();
	}
	unset( $headers['X-Pingback'] );
	$headers['Permissions-Policy']     = 'camera=(), microphone=(), geolocation=()';
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	return $headers;
}
add_filter( 'wp_headers', 'fenix_infra_response_headers', 20 );

/* X-Powered-By (เลขเวอร์ชัน PHP) และ X-Pingback · ลบทั้งหน้าเว็บ, REST และ admin-ajax */
function fenix_infra_remove_powered_by() {
	if ( function_exists( 'header_remove' ) && ! headers_sent() ) {
		header_remove( 'X-Powered-By' );
		header_remove( 'X-Pingback' );
	}
}
add_action( 'init', 'fenix_infra_remove_powered_by', 1 );
add_action( 'send_headers', 'fenix_infra_remove_powered_by', 100 );

function fenix_infra_rest_headers( $served ) {
	fenix_infra_remove_powered_by();
	return $served;
}
add_filter( 'rest_pre_serve_request', 'fenix_infra_rest_headers' );

/* ==============================================================
 * 3) ลดน้ำหนักหน้าเว็บ (ฝั่งผู้ชมเท่านั้น)
 * ============================================================== */

/**
 * หน้านี้ใช้ CSS ของบล็อก Gutenberg หรือไม่ · เนื้อหาตั้งต้นของธีมไม่ใช้บล็อก
 * ถ้าเจ้าของแก้เพจ/บทความด้วยตัวแก้ไขบล็อก has_blocks() จะเป็นจริงแล้วคง CSS ไว้เอง
 */
function fenix_infra_needs_block_styles() {
	if ( is_singular() && function_exists( 'has_blocks' ) && has_blocks( get_queried_object_id() ) ) {
		return true;
	}
	return (bool) apply_filters( 'fenix_keep_block_styles', false );
}

function fenix_infra_trim_front_assets() {
	if ( is_admin() ) {
		return;
	}
	wp_dequeue_style( 'wp-emoji-styles' );
	if ( fenix_infra_needs_block_styles() ) {
		return;
	}
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'fenix_infra_trim_front_assets', 100 );
/* คอร์เติม global-styles กลับที่ wp_footer (ลำดับ 1) → ถอดอีกรอบก่อนพิมพ์ท้ายหน้า */
add_action( 'wp_footer', 'fenix_infra_trim_front_assets', 2 );

/**
 * ปิดสคริปต์ตรวจอีโมจิฝั่งผู้ชม · หา priority จริงด้วย has_action() เผื่อคอร์ย้าย hook
 */
function fenix_infra_disable_emoji() {
	if ( is_admin() ) {
		return;
	}
	$hooks     = array( 'wp_head', 'wp_footer', 'wp_print_styles', 'wp_print_scripts', 'wp_print_head_scripts', 'wp_print_footer_scripts', 'wp_enqueue_scripts' );
	$callbacks = array( 'print_emoji_detection_script', 'wp_print_emoji_detection_script', 'wp_enqueue_emoji_detection_script', 'print_emoji_styles', 'wp_enqueue_emoji_styles' );
	foreach ( $hooks as $hook ) {
		foreach ( $callbacks as $callback ) {
			$priority = has_action( $hook, $callback );
			if ( false !== $priority && true !== $priority ) {
				remove_action( $hook, $callback, $priority );
			}
		}
	}
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'fenix_infra_disable_emoji', 20 );
/* ไม่มีอีโมจิ = ไม่ต้อง dns-prefetch ไป s.w.org */
add_filter( 'emoji_svg_url', '__return_false' );

/* ==============================================================
 * 3.1) ตารางในเนื้อหาที่เจ้าของพิมพ์เอง → ครอบ .table-wrap (เลื่อนซ้ายขวาได้บนจอแคบ)
 * ============================================================== */

/**
 * ครอบ <table> ที่ยังไม่มีกรอบด้วย <div class="table-wrap"> (รูปแบบเดียวกับเนื้อหาตั้งต้นของธีม)
 * priority 22: หลังเก็บสารบัญ (20) ก่อนตัวจัดตารางเป็นการ์ดของหน้าคู่มือ (27) และหน้าเนื้อหา (30) ซึ่งอ่านเฉพาะ .table-wrap
 * ข้าม: ตารางที่อยู่ใน .table-wrap แล้ว · ตารางของตัวแก้ไขบล็อก (figure.wp-block-table มี CSS ของตัวเอง) · ตารางซ้อนตาราง
 */
function fenix_infra_wrap_content_tables( $content ) {
	$content = (string) $content;
	if ( false === stripos( $content, '<table' ) ) {
		return $content;
	}
	$opens = preg_match_all( '#<table\b#i', $content );
	if ( $opens !== preg_match_all( '#</table>#i', $content ) ) {
		return $content;
	}
	$parts = preg_split( '#(<table\b.*?</table>)#is', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! is_array( $parts ) || count( $parts ) < 2 ) {
		return $content;
	}
	$out = '';
	foreach ( $parts as $i => $part ) {
		if ( 1 !== $i % 2 ) {
			$out .= $part;
			continue;
		}
		/* ตารางซ้อน: ช่วงที่จับได้มี <table มากกว่า 1 ตัว → ไม่แตะทั้งเนื้อหา */
		if ( preg_match_all( '#<table\b#i', $part ) > 1 ) {
			return $content;
		}
		$before = $parts[ $i - 1 ];
		if ( preg_match( '#<(?:div|figure)\b[^>]*\bclass="[^"]*\b(?:table-wrap|wp-block-table)\b[^"]*"[^>]*>\s*$#i', $before ) ) {
			$out .= $part;
			continue;
		}
		$out .= '<div class="table-wrap">' . $part . '</div>';
	}
	return $out;
}
add_filter( 'the_content', 'fenix_infra_wrap_content_tables', 22 );

/* ==============================================================
 * 4) ฟอนต์ Noto Sans Thai แบบ self-host
 * ============================================================== */

/**
 * ไฟล์ฟอนต์ในธีม (variable weight 300–800 แยกชุดอักษรด้วย unicode-range) · 2 ไฟล์แรก = preload
 */
function fenix_infra_font_files() {
	return array( 'noto-sans-thai-thai.woff2', 'noto-sans-thai-latin.woff2', 'noto-sans-thai-latin-ext.woff2' );
}

function fenix_infra_fonts_ready() {
	static $ready = null;
	if ( null === $ready ) {
		$ready = file_exists( get_template_directory() . '/assets/css/fonts.css' );
		foreach ( fenix_infra_font_files() as $file ) {
			$ready = $ready && file_exists( get_template_directory() . '/assets/fonts/' . $file );
		}
	}
	return (bool) apply_filters( 'fenix_self_hosted_fonts', $ready );
}

/**
 * แทน handle 'fenix-fonts' (Google Fonts) ด้วย assets/css/fonts.css ของธีม
 * ใช้ handle เดิม เพราะ fenix-style ประกาศ dependency ไว้ที่ชื่อนี้ (ถอดทิ้งเฉย ๆ style.css จะไม่ถูกโหลด)
 * ไฟล์ฟอนต์ไม่ครบ = ปล่อย Google Fonts ไว้ตามเดิม
 */
function fenix_infra_self_hosted_fonts() {
	if ( ! fenix_infra_fonts_ready() ) {
		return;
	}
	$css = '/assets/css/fonts.css';
	wp_dequeue_style( 'fenix-fonts' );
	wp_deregister_style( 'fenix-fonts' );
	wp_enqueue_style( 'fenix-fonts', get_template_directory_uri() . $css, array(), (string) filemtime( get_template_directory() . $css ) );
}
add_action( 'wp_enqueue_scripts', 'fenix_infra_self_hosted_fonts', 20 );

/**
 * preload ฟอนต์ 2 ไฟล์ที่ทุกหน้าใช้ในจอแรก (อักษรไทย + ละติน/ตัวเลข) · URL ต้องตรงกับใน fonts.css
 */
function fenix_infra_preload_fonts() {
	if ( is_admin() || ! fenix_infra_fonts_ready() ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/fonts/';
	foreach ( array_slice( fenix_infra_font_files(), 0, 2 ) as $file ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $base . $file ) );
	}
}
add_action( 'wp_head', 'fenix_infra_preload_fonts', 2 );

/* ==============================================================
 * 5) คงตัวพิมพ์อัตโนมัติ (keep-case)
 * CSS ทำให้ตัวอังกฤษเป็นพิมพ์ใหญ่ (style.css หัวข้อ 27) · ข้อความจริงใน HTML ไม่เปลี่ยน
 * คำที่พิมพ์ใหญ่แล้วอ่านผิดหรือพิมพ์ตามไม่ได้ ถูกห่อด้วย <span class="keep-case kc"> ก่อนส่งหน้าเว็บ:
 * อีเมล · โดเมนและลิงก์ · LINE ID (@xxx) · ชื่อไฟล์และนามสกุล (.ex5 .set .zip ...) · สัญลักษณ์ที่มีตัวท้าย (XAUUSD.c) · iPhone iPad iOS macOS
 * .kc = display: contents (ท้าย style.css) จึงไม่สร้างกล่องใหม่ในแถว flex/grid · ปิดทั้งชุดด้วย add_filter( 'fenix_nocaps_enabled', '__return_false' )
 * (ปิดทั้งบัฟเฟอร์ รวมการกันตัดบรรทัดกลางคำของข้อ 5.1 ด้วย)
 * ข้อจำกัด: ชิ้นส่วนที่โหลดทีหลังด้วย AJAX (ปุ่มโหลดบทความเพิ่ม) ไม่ผ่านบัฟเฟอร์นี้
 * ============================================================== */

/**
 * รูปแบบคำที่ต้องคงตัวพิมพ์ (ฟิลเตอร์ fenix_nocaps_pattern เพิ่มหรือแทนได้)
 * ขอบคำเขียนเป็นชุดอักขระ ASCII เอง: ในโหมด /u อักษรไทยนับเป็นตัวอักษร \b จึงพลาดคำที่พิมพ์ติดกับข้อความไทย
 */
function fenix_nocaps_pattern() {
	$w     = 'A-Za-z0-9_';
	$parts = array(
		'[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}',                                                                           // อีเมล
		'(?<![' . $w . '@.-])(?:https?:\/\/)?(?:[a-z0-9-]+\.)+(?:co|com|me|ee|net|org|io|th|app)(?![' . $w . '-])(?:\/[^\s<]*)?', // โดเมน / ลิงก์
		'(?<![' . $w . '@.])@(?=[a-z0-9._-]*[a-z])[a-z0-9][a-z0-9._-]*',                                                          // LINE ID (ขึ้นต้นด้วยตัวเลขได้)
		'[' . $w . '.-]*\.(?:ex5|ex4|mq5|mq4|set|zip|pdf|txt|dll|exe)(?![' . $w . '])',                                           // ชื่อไฟล์และนามสกุล
		'(?<![' . $w . '])[A-Z]{6}\.[a-z]{1,3}(?![' . $w . '])',                                                                  // สัญลักษณ์ที่มีตัวท้าย เช่น XAUUSD.c
		'(?<=\s)\.[a-z]{1,3}(?=[\s,)])',                                                                                          // ตัวท้ายสัญลักษณ์ที่เขียนลำพัง เช่น .c .m
		'(?<![' . $w . '])(?:iPhone|iPadOS|iPad|iOS|macOS)(?![' . $w . '])',                                                      // ชื่อที่ขึ้นต้นด้วยตัวเล็ก
	);
	return (string) apply_filters( 'fenix_nocaps_pattern', '/' . implode( '|', $parts ) . '/u' );
}

/* --------------------------------------------------------------
 * 5.1) กันตัดบรรทัดกลางคำ (nobreak) · ทำในบัฟเฟอร์เดียวกับข้อ 5 เดินข้อความรอบเดียว
 * ตัวตัดคำไทยของเบราว์เซอร์ใช้พจนานุกรม คำทับศัพท์ที่ไม่อยู่ในนั้นจึงถูกตัดกลางคำที่ปลายบรรทัด ("ออเด" / "อร์" · "แดช" / "บอร์ด")
 * และคำที่อยู่ข้างหน้าพลอยถูกตัดผิดไปด้วย ("จำ" / "นวนออเดอร์")
 * ทุกคำในรายการถูกห่อด้วย <span class="nb"> (white-space: nowrap · ท้าย style.css) และมีจุดตัดบรรทัด (FALCON_NOBREAK_GAP) หน้าและหลังคำ
 * เมื่อคำนั้นพิมพ์ติดกับข้อความไทยข้างเคียง: จุดตัดนี้คือขอบคำที่ถูกต้อง และแยกคำข้างเคียงออกให้ตัวตัดคำอ่านได้ถูก
 * จุดตัด = ช่องว่างกว้างศูนย์ U+200B (ค่าคงที่ FALCON_NOBREAK_GAP) ตัวคั่นคำมาตรฐานของภาษาไทยบนเว็บ · ไม่ใช้ <wbr>:
 *   Chrome ตัดบรรทัดที่ <wbr> แม้อยู่ใน white-space: nowrap (วัดแล้ว) ข้อความที่ตั้งใจให้อยู่บรรทัดเดียวจึงแตกเป็นสองบรรทัด
 *   (breadcrumb ที่ตัดด้วยจุดไข่ปลา · ป้ายของ dock · ปุ่มและหัวตารางที่เป็น nowrap) ส่วน U+200B ทำตาม white-space ของบริบทเสมอ
 * อยู่ระหว่างคำเท่านั้น ไม่มีอักขระมองไม่เห็นกลางคำ: ตัวหนังสือของแต่ละคำยังครบทุกตัว · การค้นหาในหน้าของเบราว์เซอร์มองข้าม U+200B
 * (ลองใน Chrome แล้ว: ค้น "จำนวนออเดอร์" เจอ) โปรแกรมอ่านหน้าจอและ search engine อ่านคำได้ตามเดิม
 * ชิป <code> ที่เป็นพาธยาวใช้ span.nb กับ <wbr> (fenix_nobreak_path): ตัดบรรทัดได้เฉพาะหลัง \ หรือ /
 *   ตรงนี้ใช้ <wbr> เพราะพาธต้องคัดลอกไปวางได้ตรงตัว ห้ามมีอักขระแฝงในข้อความ
 * ไม่แตะ: ทุกอย่างที่ข้อ 5 ข้าม (แอตทริบิวต์ คอมเมนต์ script รวม JSON-LD style textarea select svg) · ส่วน head (บัฟเฟอร์เริ่มที่ <body>)
 *         และข้อความใน pre code option title kbd samp · span.nb / span.nobr / span.kc ที่มีอยู่แล้ว (ไม่ห่อซ้อน)
 * ไม่ทำในหน้าต่างพรีวิวของ Customizer (ข้อความที่ผู้ดูแลกำลังแก้ต้องตรงกับที่พิมพ์) · ฟีด REST หลังบ้าน AJAX ไม่ผ่านบัฟเฟอร์นี้อยู่แล้ว
 * -------------------------------------------------------------- */

/* จุดตัดบรรทัดระหว่างคำไทย: U+200B ZERO WIDTH SPACE · ส่งเป็นตัวอักขระจริง (3 ไบต์) ไม่ใช่ entity &#8203; (7 ไบต์)
 * หน้าหนึ่งมีจุดตัด 300 ถึง 900 จุด: แบบ entity ทำให้ HTML ที่บีบอัดแล้วโตเกิน 6% ในหน้าที่ข้อความแน่น (แพ็กเกจ เงื่อนไขการใช้บริการ)
 * แบบตัวอักขระจริงโตไม่เกิน 6% ทุกหน้า · มองไม่เห็นใน view-source แต่แผง Elements ของเบราว์เซอร์แสดงเป็น &ZeroWidthSpace; ให้ */
if ( ! defined( 'FALCON_NOBREAK_GAP' ) ) {
	define( 'FALCON_NOBREAK_GAP', "\u{200B}" );
}

/**
 * คำที่ห้ามตัดบรรทัดกลางคำ (ฟิลเตอร์ fenix_nobreak_words เพิ่มหรือลดได้ · คืน array ว่าง = ปิดการห่อคำ)
 * ใส่เฉพาะคำเต็มที่เห็นว่าถูกตัดผิดจริงหรือคำทับศัพท์ที่เว็บนี้ใช้บ่อย · หน้าและหลังคำมีจุดตัดบรรทัด คำในรายการจึงต้องไม่ใช่
 * ท่อนต้นหรือท่อนท้ายของคำที่ยาวกว่า: ถ้ามี ให้ใส่คำที่ยาวกว่าไว้ด้วย (คำยาวถูกจับก่อนเสมอ)
 * เช่น มี "เซ็นต์" ต้องมี "เปอร์เซ็นต์" · มี "ผู้ใช้" ต้องมี "ผู้ใช้งาน" · มี "สเปรด" ต้องมี "สเปรดชีต"
 */
function fenix_nobreak_words() {
	/* คำทับศัพท์ที่พจนานุกรมของตัวตัดคำไม่รู้จัก: ถูกหักเป็นท่อน (ออ|เด|อร์ · แด|ช|บอร์ด · รี|ส|ตาร์ต · แพ็ก|เก|จ · เท|รนด์ · เช็|ก) */
	$loan = 'ออเดอร์ แดชบอร์ด แพ็กเกจ รีสตาร์ต รีสตาร์ท รีบูต โบรกเกอร์ โบรก เซิร์ฟเวอร์ เบราว์เซอร์ คอมพิวเตอร์ อินเทอร์เน็ต'
		. ' ดาวน์โหลด ล็อกอิน ปลดล็อก เปอร์เซ็นต์ เซ็นต์ เดโม แชท แชต โรบอทเทรด โรบอท บอทเทรด บอท แคป ครอป เช็กลิสต์ เช็ก เช็ค สเปรดชีต สเปรด เทรนด์'
		. ' คอมมิชชัน มาร์ติงเกล การันตี โฮสติ้ง สติกเกอร์ ทาสก์บาร์ ไดรฟ์ คีย์ลัด ปรับจูน';
	/* คำประสมที่เห็นถูกตัดกลางคำที่ปลายบรรทัดบนจอ 375 และ 360px (ผู้|ใช้ · ทีม|งาน · ตัดสิน|ใจ · ขาด|ทุน · ความ|เสี่ยง · ราย|ละเอียด)
	 * และคำที่ทำให้คำข้างเคียงถูกตัดผิด (ทุกข|นาด · ต้องเต|รียม · น้อยห|รือ) */
	$thai = 'ทีมงาน ผู้ใช้งาน ผู้ใช้ ผู้ให้บริการ ผู้พัฒนา ผู้บริโภค ใช้งาน เยี่ยมชม ตัดสินใจ ขั้นตอน ขั้นต่ำ รหัสผ่าน รหัสเชิญ รหัสยืนยัน'
		. ' ติดตั้ง เริ่มต้น เตรียม ความเสี่ยง ขาดทุน เงินทุน คืนเงินต้น คืนเงิน คู่เงิน สวนทาง รับประกัน หลักประกัน บริบูรณ์ รายละเอียด รายเดือน'
		. ' เครื่องมือ คำสั่ง คำตอบ คำอธิบาย คำปรึกษา คำแนะนำ ข้อจำกัด ข้อผิดพลาด ข้อยกเว้น ผิดพลาด ถูกต้อง ส่งภาพหน้าจอ ภาพหน้าจอ'
		. ' ส่งภาพ หน้าจอ ตัวตน พบบ่อย แจ้งเตือน สูญเสีย เสียหาย แนวโน้ม ผลตอบแทน ผลทดสอบ เปรียบเทียบ คาดการณ์ ตรวจสอบ ใบอนุญาต'
		. ' เหมาะสม สภาพคล่อง ทางการค้า สุดสัปดาห์ ล่วงหน้า เป้าหมาย ฝากถอน ซื้อขาย รับผิดชอบ ใกล้ชิด ครั้งเดียว ครั้งแรก ตลอดเวลา'
		. ' ตัวชี้วัด จำนวนน้อย ขนาด แตกต่างกัน แตกต่าง ต่างกัน ต่างจาก ไม่ได้'
		/* รอบตรวจหลังแก้: คำที่ยังเห็นถูกตัดกลางคำที่ปลายบรรทัด (มือ|ถือ 20 กว่าจุดในหน้าคู่มือและ /go · ตั้ง|ค่า ในหัวข้อ H2 · ย้อน|หลัง · ต่อ|เนื่อง
		 * ผล|รวม ในช่องแคบของตารางผล Backtest · "ไม่มีค่า" / "ใช้จ่าย" ในช่องตารางเทียบแพ็กเกจ) · ไม่ใส่ "การตั้งค่า": จะไปตัดคำที่ลงท้ายด้วย การ (รายการตั้งค่า) */
		. ' มือถือ ตั้งค่า ย้อนหลัง ต่อเนื่อง ผลรวม ค่าใช้จ่าย';
	/* วลีสั้นที่ต้องอยู่ด้วยกัน: "ทีละ..." และ "...ไหน" (หัวข้อและคำถามที่เกลี่ยบรรทัดแล้วเคยถูกแบ่งเป็น "ทีละ" / "ช่อง" · "ติดขั้น" / "ไหน") */
	$pair = 'ทีละขั้นตอน ทีละขั้น ทีละช่อง ทีละจุด ทีละหมวด ทีละข้อ ทีละค่า ทีละส่วน ทีละบรรทัด ทีละวัน ทีละตัว ทีละหน้า'
		. ' คืออะไร แบบไหน ทางไหน ขั้นไหน เรื่องไหน ตรงไหน ที่ไหน วิธีไหน ช่วงไหน ระดับไหน ข้อไหน ชุดไหน แค่ไหน';
	return (array) apply_filters( 'fenix_nobreak_words', explode( ' ', $loan . ' ' . $thai . ' ' . $pair ) );
}

/**
 * regex รวมของทุกคำในรายการ (สร้างครั้งเดียวต่อคำขอ · '' = ไม่มีคำ)
 * กลุ่ม 1 = คำที่พิมพ์ติดกับอักษรไทยข้างหน้า (ต้องมีจุดตัดบรรทัดนำ) · กลุ่ม 2 = คำที่ขึ้นต้นข้อความหรือตามหลังช่องว่าง เครื่องหมาย ตัวอังกฤษ
 * ไม่จับเมื่อคำนั้นเป็นแค่ท่อนหนึ่งของพยางค์อื่น: มีสระหน้า (เ แ โ ใ ไ) นำอยู่ หรือมีสระ/วรรณยุกต์ตามมาต่อกับพยัญชนะตัวท้าย
 */
function fenix_nobreak_pattern() {
	static $pattern = null;
	if ( null !== $pattern ) {
		return $pattern;
	}
	$pattern = '';
	$words   = array();
	foreach ( fenix_nobreak_words() as $word ) {
		$word = trim( (string) $word );
		if ( '' !== $word ) {
			/* ข้อความในหน้าผ่าน esc_html มาแล้ว จึงเทียบกับรูปที่ escape แล้วของคำ */
			$words[ $word ] = preg_quote( esc_html( $word ), '/' );
		}
	}
	if ( ! $words ) {
		return $pattern;
	}
	/* คำยาวมาก่อน: เมื่อคำหนึ่งเป็นส่วนต้นของอีกคำ ให้จับคำที่ยาวกว่า */
	uksort(
		$words,
		function ( $a, $b ) {
			return strlen( $b ) - strlen( $a );
		}
	);
	$alt     = implode( '|', $words );
	$pattern = '/(?<![\x{0E40}-\x{0E44}])(?:(?<=[\x{0E01}-\x{0E3A}\x{0E45}-\x{0E4E}])(' . $alt . ')|(' . $alt . '))(?![\x{0E30}-\x{0E3A}\x{0E45}\x{0E47}-\x{0E4E}])/u';
	return $pattern;
}

/**
 * ห่อคำในรายการที่พบในข้อความหนึ่งช่วง (ข้อความระหว่างแท็ก ไม่มีแท็กปน) · regex ไม่สำเร็จ = คืนของเดิม
 */
function fenix_nobreak_text( $text, $pattern ) {
	$count = 0;
	$done  = preg_replace_callback(
		$pattern,
		function ( $m ) {
			if ( isset( $m[2] ) && '' !== $m[2] ) {
				return '<span class="nb">' . $m[2] . '</span>';
			}
			return FALCON_NOBREAK_GAP . '<span class="nb">' . $m[1] . '</span>';
		},
		$text,
		-1,
		$count
	);
	if ( ! is_string( $done ) ) {
		return $text;
	}
	if ( $count ) {
		/* จุดตัดหลังคำ เมื่อคำถัดไปพิมพ์ติดกัน (ตามด้วยพยัญชนะหรือสระหน้า): ท้ายคำทับศัพท์มักถูกรวมกับต้นคำถัดไป
		 * ("แพ็กเกจอยู่" ถูกแบ่งเป็น แพ็ก|เก|จอ|ยู่) คำถัดไปจึงถูกตัดผิดได้ · ไม่ใส่หน้าไม้ยมก เครื่องหมาย หรือช่องว่าง */
		$after = preg_replace( '/<\/span>(?=[\x{0E01}-\x{0E2E}\x{0E40}-\x{0E44}])/u', '</span>' . FALCON_NOBREAK_GAP, $done );
		if ( is_string( $after ) ) {
			$done = $after;
		}
	}
	return $done;
}

/**
 * ข้อความในชิป <code> ที่เป็นพาธ (C:\Users\...\MQL5\Experts · a/b/c): ตัดบรรทัดได้เฉพาะหลัง \ หรือ /
 * เดิมพาธยาวบนจอแคบถูกหักกลางคำไทยสามจุดและยาวชนขอบคอลัมน์ · แต่ละท่อน (รวมตัวคั่นท้ายท่อน) ห่อ span.nb คั่นด้วย <wbr>
 * ไม่แตะ: ข้อความสั้นกว่า 16 ไบต์ · จุดที่ตัวคั่นตามด้วยตัวเลข (วันที่ 12/10/2026 เศษส่วน 1/2) ช่องว่าง หรือ entity
 * ท่อนที่มีช่องว่าง (ตัดที่ช่องว่างได้อยู่แล้ว) และท่อนยาวเกิน 24 ตัวอักษร (ห้ามตัดแล้วอาจล้นคอลัมน์บนจอ 320px) ปล่อยไว้ตามเดิม
 */
function fenix_nobreak_path( $text ) {
	if ( strlen( $text ) < 16 || false === strpbrk( $text, '\\/' ) ) {
		return $text;
	}
	$pieces = preg_split( '#(?<=[\\\\/])(?=[^\\\\/\s\d&])#u', $text );
	if ( ! is_array( $pieces ) || count( $pieces ) < 2 ) {
		return $text;
	}
	foreach ( $pieces as $idx => $piece ) {
		$length = function_exists( 'mb_strlen' ) ? mb_strlen( $piece, 'UTF-8' ) : strlen( $piece );
		if ( '' !== $piece && $length <= 24 && ! preg_match( '/\s/u', $piece ) ) {
			$pieces[ $idx ] = '<span class="nb">' . $piece . '</span>';
		}
	}
	return implode( '<wbr>', $pieces );
}

/**
 * เขตที่ห้ามห่อคำ: อัปเดตสถานะจากแท็กหนึ่งตัวระหว่างเดินข้อความ
 * $zone = array( ชื่อแท็กที่เปิดเขต หรือ '' เมื่ออยู่นอกเขต, จำนวนแท็กชื่อเดียวกันที่ยังเปิดซ้อนอยู่ )
 * เขตเปิดด้วย pre code option title kbd samp และ span ที่มีคลาส nb / nobr / kc · ปิดเมื่อแท็กปิดชื่อเดียวกันครบคู่
 * HTML ที่ปิดแท็กไม่ครบ = เขตไม่ปิด ส่วนที่เหลือของหน้าไม่ถูกห่อ (หน้าเว็บยังเหมือนเดิมทุกตัวอักษร)
 */
function fenix_nobreak_zone( $tag, array $zone ) {
	if ( ! preg_match( '#^<(/?)([a-zA-Z][a-zA-Z0-9]*)#', $tag, $m ) ) {
		return $zone;
	}
	$name  = strtolower( $m[2] );
	$close = '/' === $m[1];
	if ( '' !== $zone[0] ) {
		if ( $name === $zone[0] ) {
			$zone[1] += $close ? -1 : 1;
			if ( $zone[1] < 1 ) {
				$zone = array( '', 0 );
			}
		}
		return $zone;
	}
	if ( $close ) {
		return $zone;
	}
	if ( in_array( $name, array( 'pre', 'code', 'option', 'title', 'kbd', 'samp' ), true ) ) {
		return array( $name, 1 );
	}
	if ( 'span' === $name && preg_match( '/\sclass\s*=\s*(["\'])(?:[^"\']*\s)?(?:nb|nobr|kc)(?:\s[^"\']*)?\1/i', $tag ) ) {
		return array( $name, 1 );
	}
	return $zone;
}

/**
 * ห่อคำที่ต้องคงตัวพิมพ์ (ข้อ 5) และคำที่ห้ามตัดบรรทัดกลางคำ (ข้อ 5.1) ในส่วน body · แตะเฉพาะข้อความระหว่างแท็ก
 * ไม่แตะ: แอตทริบิวต์ · คอมเมนต์ · script · style · textarea · select (span ใน option ผิดรูป) · svg (title/text ของไอคอน)
 * ทุกขั้นที่ regex ทำงานไม่สำเร็จ (HTML ใหญ่เกิน backtrack limit, ข้อความไม่ใช่ UTF-8) คืนของเดิม หน้าเว็บจึงไม่มีวันหาย
 */
function fenix_nocaps_buffer( $html ) {
	/* บัฟเฟอร์ทั้งหน้ากำลังถูกเทออก = จังหวะสุดท้ายที่ header ยังแก้ได้: ตรวจตัวจำกัดอายุแคชของข้อ 6 อีกรอบ */
	fenix_infra_page_cache_recheck();
	$html = (string) $html;
	$at   = stripos( $html, '<body' );
	if ( false === $at ) {
		return $html;
	}
	$pattern = fenix_nocaps_pattern();
	$skip    = '<!--.*?-->|<script\b.*?</script\s*>|<style\b.*?</style\s*>|<textarea\b.*?</textarea\s*>|<select\b.*?</select\s*>|<svg\b.*?</svg\s*>';
	$parts   = preg_split( '#(' . $skip . '|<[^>]+>)#is', substr( $html, $at ), -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! is_array( $parts ) ) {
		return $html;
	}
	/* ข้อ 5.1 · ปิดในพรีวิวของ Customizer · $nobreak = '' เมื่อรายการคำว่าง (พาธในชิป <code> ยังทำตามปกติ) */
	$nb_on   = ! ( function_exists( 'is_customize_preview' ) && is_customize_preview() );
	$nobreak = $nb_on ? fenix_nobreak_pattern() : '';
	$zone    = array( '', 0 );
	$prev    = '';    // ข้อความเดิมของช่วงก่อนหน้า (ก่อนถูกห่อ)
	$thai    = false; // เขตปัจจุบันคือ span ที่เทมเพลตห่อคำไทยไว้เอง
	$begins  = '/^[\x{0E01}-\x{0E2E}\x{0E40}-\x{0E44}]/u'; // ขึ้นต้นด้วยพยัญชนะหรือสระหน้า = ต้นคำไทย
	foreach ( $parts as $i => $part ) {
		/* ลำดับคี่ = แท็กหรือบล็อกที่ข้าม: ใช้ติดตามว่าข้อความถัดไปอยู่ในเขตห้ามห่อคำหรือไม่ */
		if ( 1 === $i % 2 ) {
			if ( $nb_on ) {
				$was  = $zone[0];
				$zone = fenix_nobreak_zone( $part, $zone );
				$next = isset( $parts[ $i + 1 ] ) ? $parts[ $i + 1 ] : '';
				/* span.nobr ที่เทมเพลตห่อคำไทยไว้เองแล้ว (fenix_dashboard_esc: ออเดอร์ แดชบอร์ด แพ็กเกจ ...) ไม่ถูกห่อซ้ำ
				 * แต่ยังต้องมีจุดตัดบรรทัดหน้าและหลังเหมือนคำที่บัฟเฟอร์นี้ห่อเอง ไม่งั้นคำข้างเคียงยังถูกตัดผิด (จำ|นวนออเดอร์ บนหน้าแรก) */
				if ( '' === $was && 'span' === $zone[0] ) {
					$thai = '' !== $next && 1 === preg_match( $begins, $next );
					if ( $thai && preg_match( '/[\x{0E01}-\x{0E3A}\x{0E45}-\x{0E4E}]$/u', $prev ) ) {
						$parts[ $i ] = FALCON_NOBREAK_GAP . $part;
					}
				} elseif ( 'span' === $was && '' === $zone[0] ) {
					if ( $thai && '' !== $next && preg_match( $begins, $next ) ) {
						$parts[ $i ] = $part . FALCON_NOBREAK_GAP;
					}
					$thai = false;
				}
			}
			continue;
		}
		$prev = $part;
		if ( '' === $part ) {
			continue;
		}
		$text = $part;
		/* ห่อคำห้ามตัดก่อนคงตัวพิมพ์: ตอนนี้ข้อความยังไม่มีแท็กปน · ไบต์ 0xE0 = มีอักษรไทย (คำในรายการที่ไม่ใช่ไทยก็ผ่านได้เมื่อข้อความมีไทยปน) */
		if ( $nb_on ) {
			if ( '' === $zone[0] ) {
				if ( '' !== $nobreak && false !== strpos( $part, "\xE0" ) ) {
					$text = fenix_nobreak_text( $part, $nobreak );
				}
			} elseif ( 'code' === $zone[0] ) {
				$text = fenix_nobreak_path( $part );
			}
		}
		/* ข้อความที่ไม่มีตัวอังกฤษพิมพ์เล็กไม่มีอะไรต้องคงไว้ (ดูจากข้อความเดิม ก่อนมีแท็ก span ที่เพิ่งใส่) */
		if ( preg_match( '/[a-z]/', $part ) ) {
			$done = preg_replace_callback(
				$pattern,
				function ( $m ) {
					return '<span class="keep-case kc">' . $m[0] . '</span>';
				},
				$text
			);
			if ( is_string( $done ) ) {
				$text = $done;
			}
		}
		$parts[ $i ] = $text;
	}
	return substr( $html, 0, $at ) . implode( '', $parts );
}

/**
 * เริ่มบัฟเฟอร์เฉพาะหน้าเว็บฝั่งผู้อ่าน (ไม่ใช่หลังบ้าน ฟีด robots REST AJAX)
 * ไม่ทำในหน้าต่างแก้ไขของ Elementor: ตัวแก้ไขอ่าน HTML ของข้อความกลับไปบันทึก span จะติดเข้าไปในเนื้อหา
 */
function fenix_nocaps_start() {
	if ( is_admin() || is_feed() || is_robots() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( isset( $_GET['elementor-preview'] ) || ! apply_filters( 'fenix_nocaps_enabled', true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- อ่านอย่างเดียว ไม่บันทึกอะไร
		return;
	}
	ob_start( 'fenix_nocaps_buffer' );
}
add_action( 'template_redirect', 'fenix_nocaps_start', 99 );

/* ==============================================================
 * 6) อายุแคชหน้าเว็บของโฮสต์ (header x-cache-status) · ไม่เกิน 5 นาที
 * โฮสต์เก็บ HTML ไว้นานกว่ารอบ deploy และไม่มีปุ่มล้างใน WordPress: หลังอัปเดตธีม หน้าที่แคชไว้ยังอ้างไฟล์รุ่นเก่า
 * และบทความที่ถึงเวลาเผยแพร่ยังไม่ขึ้นหน้ารวมจนกว่าแคชหมดอายุ
 * X-Accel-Expires + Cache-Control s-maxage บอกแคชระดับเซิร์ฟเวอร์ให้เก็บได้ไม่เกิน $ttl วินาที (เบราว์เซอร์ไม่เก็บ: max-age=0)
 * เฉพาะผู้อ่านที่ไม่ได้ล็อกอิน คำขอแบบ GET / HEAD · ฟิลเตอร์ fenix_page_cache_ttl ปรับวินาทีได้ (0 = ไม่ส่ง header ปล่อยตามค่าของโฮสต์)
 *
 * คำตอบที่ต้องไม่ถูกเก็บ (ตัวจำกัดนี้ไม่ส่ง และถ้าส่งไปแล้วจะถูกยกเลิก):
 * - หน้า 404: WordPress ตอบพร้อม no-store อยู่แล้ว ถ้าทับด้วยตัวจำกัด โฮสต์จะเก็บ 404 ไว้ 5 นาที
 *   เพจที่เพิ่งเผยแพร่หรือบทความที่เพิ่งถึงเวลาจึงยังตอบ 404 ต่อ
 * - คำตอบที่มี Set-Cookie หรือมี Cache-Control แบบ no-store / private (เป็นของผู้เปิดคนเดียว) ไม่ว่า header นั้นเข้าคิว
 *   ก่อนจุดที่ส่งตัวจำกัด หรือหลังจากนั้นระหว่างวาดหน้า (ปลั๊กอินตั้งคุกกี้หรือเริ่ม session ใน wp_head / wp_footer):
 *   ตรวจซ้ำตอนคำตอบกำลังจะออก ดู fenix_infra_page_cache_recheck()
 * - สถานะ 4xx / 5xx ที่ตั้งก่อนหรือหลังจุดนี้ และทุกครั้งที่ WordPress ประกาศว่าห้ามแคช (wp_die หน้า error ของ PHP fatal)
 *   X-Accel-Expires ชนะ Cache-Control ที่ชั้นแคชของเซิร์ฟเวอร์ จึงต้องส่งค่า 0 ทับเอง WordPress ไม่รู้จัก header นี้
 * ============================================================== */

/**
 * ตัวจำกัดอายุแคชถูกส่งในคำขอนี้แล้วหรือยัง (ส่งค่าเข้ามา = บันทึกสถานะ)
 */
function fenix_infra_page_cache_capped( $set = null ) {
	static $capped = false;
	if ( null !== $set ) {
		$capped = (bool) $set;
	}
	return $capped;
}

/**
 * header ที่เข้าคิวไว้แล้วบอกว่าคำตอบนี้เป็นของผู้เปิดคนเดียวหรือห้ามเก็บ: Set-Cookie · Cache-Control ที่มี no-store หรือ private
 */
function fenix_infra_response_is_private() {
	foreach ( headers_list() as $line ) {
		if ( 0 === stripos( $line, 'Set-Cookie:' ) ) {
			return true;
		}
		if ( 0 === stripos( $line, 'Cache-Control:' ) && preg_match( '/\b(?:no-store|private)\b/i', $line ) ) {
			return true;
		}
	}
	return false;
}

function fenix_infra_page_cache_ttl() {
	if ( is_404() || is_admin() || is_user_logged_in() || is_feed() || is_preview() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	/* HEAD ได้ header ชุดเดียวกับ GET (ตรวจหลัง deploy ด้วย curl -I ได้) · POST และวิธีอื่นไม่ถูกแคช */
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_key( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
	if ( ! in_array( $method, array( 'GET', 'HEAD' ), true ) || headers_sent() ) {
		return;
	}
	/* สถานะข้อผิดพลาดที่ปลั๊กอินตั้งไว้ก่อนถึงจุดนี้ (เช่น 410 503) และคำตอบที่ประกาศว่าเป็นของผู้เปิดคนเดียว */
	if ( (int) http_response_code() >= 400 || fenix_infra_response_is_private() ) {
		return;
	}
	/* เพจที่ตั้งรหัสผ่าน: เนื้อหาต่างกันตามคุกกี้ของผู้เปิด ห้ามประกาศว่าเก็บร่วมกันได้ · DONOTCACHEPAGE: ปลั๊กอินขอไม่ให้แคชหน้านี้ */
	if ( is_singular() && '' !== (string) get_post_field( 'post_password', get_queried_object_id() ) ) {
		return;
	}
	if ( defined( 'DONOTCACHEPAGE' ) && DONOTCACHEPAGE ) {
		return;
	}
	$ttl = (int) apply_filters( 'fenix_page_cache_ttl', 300 );
	if ( $ttl < 1 ) {
		return;
	}
	header( 'X-Accel-Expires: ' . $ttl );
	header( 'Cache-Control: public, max-age=0, s-maxage=' . $ttl );
	fenix_infra_page_cache_capped( true );
}
add_action( 'template_redirect', 'fenix_infra_page_cache_ttl', 98 );

/**
 * ยกเลิกตัวจำกัด: บอกชั้นแคชของเซิร์ฟเวอร์ว่าห้ามเก็บคำตอบนี้
 * Cache-Control ถูกแทนเฉพาะเมื่อคำขอนี้ส่งตัวจำกัดไปแล้ว (ไม่ทับ header ของ WordPress หรือปลั๊กอินในคำขออื่น)
 */
function fenix_infra_page_cache_cancel() {
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Accel-Expires: 0' );
	if ( fenix_infra_page_cache_capped() ) {
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0, no-store, private' );
		fenix_infra_page_cache_capped( false );
	}
}

/**
 * ทุกครั้งที่ WordPress ประกาศว่าคำตอบห้ามแคช (404 · wp_die · หน้า error ของ PHP fatal · ผู้ที่ล็อกอิน · หลังบ้าน)
 * ให้ชุด header นั้นมี X-Accel-Expires: 0 ด้วย ค่า 300 ที่ส่งไปก่อนจึงถูกแทน
 */
function fenix_infra_nocache_headers( $headers ) {
	if ( is_array( $headers ) ) {
		$headers['X-Accel-Expires'] = '0';
	}
	return $headers;
}
add_filter( 'nocache_headers', 'fenix_infra_nocache_headers' );

/**
 * สถานะ 4xx / 5xx ที่ตั้งผ่าน status_header() หลังตัวจำกัดถูกส่งแล้ว (ปลั๊กอินที่ตอบ 410 / 503 โดยไม่เรียก nocache_headers)
 */
function fenix_infra_page_cache_on_status( $status_header, $code = 200 ) {
	if ( (int) $code >= 400 ) {
		fenix_infra_page_cache_cancel();
	}
	return $status_header;
}
add_filter( 'status_header', 'fenix_infra_page_cache_on_status', 10, 2 );

/**
 * ตรวจซ้ำตอนคำตอบกำลังจะออก: ตัวจำกัดถูกส่งไปแล้ว แต่คำตอบจบไม่เหมือนตอนที่ตัดสินใจส่ง ให้ยกเลิก
 * - สถานะข้อผิดพลาดหรือ PHP fatal ที่ไม่ผ่านสองทางข้างบน เช่น fatal ระหว่างที่ WP Pusher สลับไฟล์ธีม
 *   (โหมด maintenance ตัวจัดการ error ของ WordPress ไม่ทำงาน)
 * - Set-Cookie หรือ Cache-Control แบบ no-store / private ที่เข้าคิวหลังจุดส่งตัวจำกัด: บัฟเฟอร์ทั้งหน้าของข้อ 5 เปิดโอกาสให้
 *   ปลั๊กอินตั้งคุกกี้หรือเริ่ม session ระหว่างวาดหน้าได้ คำตอบนั้นเป็นของผู้เปิดคนเดียว ห้ามออกไปพร้อมคำว่าเก็บร่วมกันได้
 *   (หลังส่งตัวจำกัด Cache-Control ของคำขอนี้คือบรรทัด public ของธีมเอง จึงมีแต่ header ที่มาทีหลังเท่านั้นที่เข้าเงื่อนไขนี้)
 * เรียกจากสองจุด: ตอนบัฟเฟอร์ทั้งหน้าถูกเทออก (fenix_nocaps_buffer) และ shutdown priority 0 ก่อน WordPress เทบัฟเฟอร์
 * ที่ priority 1 · ใช้ได้เฉพาะตอนที่ header ยังไม่ถูกส่ง
 */
function fenix_infra_page_cache_recheck() {
	if ( ! fenix_infra_page_cache_capped() || headers_sent() ) {
		return;
	}
	$error = error_get_last();
	$fatal = is_array( $error ) && in_array( (int) $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ), true );
	if ( $fatal || (int) http_response_code() >= 400 || fenix_infra_response_is_private() ) {
		fenix_infra_page_cache_cancel();
	}
}

function fenix_infra_page_cache_shutdown() {
	fenix_infra_page_cache_recheck();
}
add_action( 'shutdown', 'fenix_infra_page_cache_shutdown', 0 );

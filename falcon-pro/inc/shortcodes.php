<?php
/**
 * FALCON PRO EA · Shortcodes สำหรับใช้ในเนื้อหาเพจ/บทความ
 *
 * [falcon_line pos="xxx"]ข้อความปุ่ม[/falcon_line]  ปุ่ม LINE (ลิงก์จาก Customizer)
 * [falcon_brand]                                   ชื่อแบรนด์
 * [falcon_broker] / [falcon_broker field="server"] ชื่อโบรกเกอร์ / ชื่อเซิร์ฟเวอร์ MT5 (ตั้งค่าใน Customizer)
 * [falcon_calc type="lot|drawdown"]                เครื่องคำนวณ (JS ใน main.js)
 *
 * @package falcon-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fenix_sc_line( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'pos' => 'content' ), $atts, 'falcon_line' );
	$text = trim( wp_strip_all_tags( (string) $content ) );
	if ( '' === $text ) {
		$text = 'สอบถามทีมงานทาง LINE';
	}
	// ไม่มี LINE → ใช้หน้า /go/ แทน · ไม่มีทั้งคู่ = ไม่แสดงปุ่ม (ไม่ชี้ไปที่ '#')
	$target = fenix_contact_target();
	if ( '' === $target['url'] ) {
		return '';
	}
	if ( ! $target['is_line'] ) {
		$text = fenix_mod( 'contact_fallback_text' );
	}
	return '<span class="sc-line"><a class="btn btn-fire" href="' . esc_url( $target['url'] ) . '"' . ( $target['is_line'] ? ' target="_blank" rel="noopener"' : '' ) . ' data-line-pos="' . esc_attr( sanitize_key( $atts['pos'] ) ) . '">'
		. fenix_icon( $target['is_line'] ? 'line' : 'chat' ) . '<span>' . esc_html( $text ) . '</span>' . fenix_icon( 'arrow' ) . '</a></span>';
}
add_shortcode( 'falcon_line', 'fenix_sc_line' );

function fenix_sc_brand() {
	return esc_html( fenix_mod( 'hero_title' ) ? fenix_mod( 'hero_title' ) : 'FALCON PRO EA' );
}
add_shortcode( 'falcon_brand', 'fenix_sc_brand' );

function fenix_sc_broker( $atts ) {
	$atts = shortcode_atts( array( 'field' => 'name' ), $atts, 'falcon_broker' );
	$key  = 'server' === $atts['field'] ? 'broker_server' : ( 'url' === $atts['field'] ? 'broker_signup_url' : 'broker_name' );
	$val  = fenix_mod( $key );
	if ( 'broker_signup_url' === $key ) {
		return $val ? '<a class="btn btn-dark" href="' . esc_url( $val ) . '" target="_blank" rel="noopener sponsored">' . esc_html( fenix_mod( 'broker_signup_text' ) ) . ' ' . fenix_icon( 'external', 'icon icon-sm' ) . '</a>' : '';
	}
	return esc_html( $val );
}
add_shortcode( 'falcon_broker', 'fenix_sc_broker' );

function fenix_sc_calc( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'lot' ), $atts, 'falcon_calc' );
	static $n = 0;
	$n++;
	$id = 'calc-' . $n;

	ob_start();
	if ( 'drawdown' === $atts['type'] ) :
		?>
		<div class="calc" data-calc="drawdown" id="<?php echo esc_attr( $id ); ?>">
			<div class="calc-head">
				<?php echo fenix_icon_badge( 'chart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<strong>คำนวณกำไรที่ต้องทำคืนหลัง Drawdown</strong>
					<small>ขาดทุนไปกี่ % ต้องกำไรกลับกี่ % จึงจะคืนทุน</small>
				</div>
			</div>
			<div class="calc-grid">
				<label class="calc-field">
					<span>Drawdown (%)</span>
					<input type="number" inputmode="decimal" min="0" max="99" step="0.1" value="20" data-in="dd">
				</label>
				<label class="calc-field">
					<span>ทุนก่อนติดลบ (USD) · ไม่บังคับ</span>
					<input type="number" inputmode="decimal" min="0" step="1" value="1000" data-in="balance">
				</label>
			</div>
			<div class="calc-out" aria-live="polite">
				<div><span>ต้องทำกำไรคืน</span><strong data-out="gain">–</strong></div>
				<div><span>ทุนที่เหลือ</span><strong data-out="left">–</strong></div>
			</div>
			<div class="calc-bar" aria-hidden="true"><i data-out="bar"></i></div>
			<p class="calc-note">สูตร: กำไรที่ต้องทำคืน = DD ÷ (1 − DD) · เป็นการคำนวณทางคณิตศาสตร์ ไม่ใช่ผลการเทรด</p>
		</div>
		<?php
	else :
		?>
		<div class="calc" data-calc="lot" id="<?php echo esc_attr( $id ); ?>">
			<div class="calc-head">
				<?php echo fenix_icon_badge( 'calc' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<strong>เครื่องคิด Lot Size ตามความเสี่ยง</strong>
					<small>คำนวณขนาดออเดอร์จากทุน ความเสี่ยงต่อไม้ และระยะ Stop Loss</small>
				</div>
			</div>
			<div class="calc-grid">
				<label class="calc-field">
					<span>ทุนในบัญชี (USD)</span>
					<input type="number" inputmode="decimal" min="0" step="1" value="1000" data-in="balance">
				</label>
				<label class="calc-field">
					<span>ความเสี่ยงต่อไม้ (%)</span>
					<input type="number" inputmode="decimal" min="0" max="100" step="0.1" value="1" data-in="risk">
				</label>
				<label class="calc-field">
					<span>ระยะ Stop Loss (pips)</span>
					<input type="number" inputmode="decimal" min="0" step="0.1" value="50" data-in="sl">
				</label>
				<label class="calc-field">
					<span>สินทรัพย์ (ค่าอ้างอิง)</span>
					<select data-in="preset">
						<option value="10">EURUSD / GBPUSD / AUDUSD · บัญชี USD</option>
						<option value="10">XAUUSD · สัญญา 100 oz (1 pip = 0.10)</option>
						<option value="custom">กำหนดมูลค่า pip เอง</option>
					</select>
				</label>
				<label class="calc-field">
					<span>มูลค่า 1 pip ต่อ 1 lot (USD)</span>
					<input type="number" inputmode="decimal" min="0" step="0.01" value="10" data-in="pipval">
				</label>
				<label class="calc-field">
					<span>Lot ขั้นต่ำ / ขั้นการปรับ</span>
					<input type="number" inputmode="decimal" min="0.001" step="0.01" value="0.01" data-in="step">
				</label>
			</div>
			<div class="calc-out" aria-live="polite">
				<div><span>ความเสี่ยงเป็นเงิน</span><strong data-out="money">–</strong></div>
				<div><span>Lot ที่เหมาะสม</span><strong data-out="lot">–</strong></div>
			</div>
			<p class="calc-note">ผลเป็นการคำนวณเบื้องต้น มูลค่า pip ต่างกันตามสินทรัพย์ โบรกเกอร์ และสกุลเงินบัญชี · ตรวจใน MT5: คลิกขวาที่สินทรัพย์ → Specification</p>
		</div>
		<?php
	endif;
	return ob_get_clean();
}
add_shortcode( 'falcon_calc', 'fenix_sc_calc' );

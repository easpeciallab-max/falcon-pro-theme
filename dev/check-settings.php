<?php
/**
 * Dev check: every key in fenix_defaults() should have a Customizer control, and vice versa.
 * Run: php dev/check-settings.php  (from repo root, with mbstring enabled)
 */
require __DIR__ . '/preview/wp-stubs.php';

class WP_Customize_Image_Control {
	public function __construct() {}
}
class FX_Customizer_Spy {
	public $settings = array();
	public function add_panel() {}
	public function add_section() {}
	public function add_setting( $id ) { $this->settings[] = $id; }
	public function add_control() {}
}

require __DIR__ . '/../falcon-pro/functions.php';

$spy = new FX_Customizer_Spy();
fenix_customize_register( $spy );

$defaults = array_keys( fenix_defaults() );
$missing  = array_diff( $defaults, $spy->settings );
$extra    = array_diff( $spy->settings, $defaults );

echo 'defaults: ' . count( $defaults ) . ' · controls: ' . count( $spy->settings ) . "\n";
echo "defaults without a Customizer control:\n  " . ( $missing ? implode( ', ', $missing ) : '(none)' ) . "\n";
echo "controls without a default:\n  " . ( $extra ? implode( ', ', $extra ) : '(none)' ) . "\n";

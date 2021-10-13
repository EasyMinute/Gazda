<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Timersys' ) ) {
	return new WC_Settings_Timersys();
}

/**
* WC_Settings_Timersys
*/

class WC_Settings_Timersys extends WC_Settings_Page {

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->id    = 'wc_timersys';
		$this->label = __( 'Timersys', 'shiprate' );

		parent::__construct();
		$this->notices();
	}


	/**
	 * Notices.
	 */
	private function notices() {
		do_action('woocommerce_notices_settings_'.$this->id, $this);
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section, $hide_save_button;

		if( $current_section == '' ) {
			$hide_save_button = true;
			
			wc_get_template(
					'admin/layouts/settings_welcome.php',
					false, false, SHIPAREA_PLUGIN_DIR
				);
		} else {

			$settings = $this->get_settings();
			WC_Admin_Settings::output_fields( $settings );
		}
	}
}

return new WC_Settings_Timersys();
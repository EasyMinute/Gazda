<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://proacto.software/
 * @since      1.0.0
 *
 * @package    Prt_Woo_Portmone
 * @subpackage Prt_Woo_Portmone/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Prt_Woo_Portmone
 * @subpackage Prt_Woo_Portmone/includes
 * @author     Proacto <workyura23@gmail.com>
 */
class Prt_Woo_Portmone_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'prt-woo-portmone',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

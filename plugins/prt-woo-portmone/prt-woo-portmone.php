<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://proacto.software/
 * @since             1.0.0
 * @package           Prt_Woo_Portmone
 *
 * @wordpress-plugin
 * Plugin Name:       Splitpay Portmone
 * Plugin URI:        https://doc.clickup.com/d/h/2ek0y-680/3ae868cc688bac7/2ek0y-680
 * Description:        Безготівкова оплата за допомогою платіжної системи Portmone
 * Version:           1.0.0
 * Author:            Proacto
 * Author URI:        https://proacto.software/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-gateway-portmone
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PRT_WOO_PORTMONE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-prt-woo-portmone-activator.php
 */
function activate_prt_woo_portmone() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prt-woo-portmone-activator.php';
	Prt_Woo_Portmone_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-prt-woo-portmone-deactivator.php
 */
function deactivate_prt_woo_portmone() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prt-woo-portmone-deactivator.php';
	Prt_Woo_Portmone_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_prt_woo_portmone' );
register_deactivation_hook( __FILE__, 'deactivate_prt_woo_portmone' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-prt-woo-portmone.php';

/*
* The class for single pay request.
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { 
	require plugin_dir_path( __FILE__ ) . 'includes/class-pm-single-pay.php';
	require plugin_dir_path( __FILE__ ) . 'includes/custom-hooks.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_prt_woo_portmone() {

	$plugin = new Prt_Woo_Portmone();
	$plugin->run();

}
// run_prt_woo_portmone();

add_action('init', 'run_prt_woo_portmone');



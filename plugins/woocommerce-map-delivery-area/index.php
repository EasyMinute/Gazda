<?php
/*
Plugin Name: WooCommerce Map delivery Area
Plugin URI: https://www.timersys.com/
Description: This plugin allows calculate shipping price using delivery areas drawn in Google Maps
Version: 1.0.2.7
Author: timersys
Author URI: https://www.timersys.com/
Developer: Damian Logghe
Developer URI: https://www.timersys.com/
Text Domain: woocommerce-extension
Requires at least: 5.1
Tested up to: 5.5.1
Stable tag: 5.1
WC requires at least: 3.1.0
WC tested up to: 3.9.1
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SHIPAREA_EDD_ID', '59571' );
define( 'SHIPAREA_VERSION', '1.0.2.7' );
define( 'SHIPAREA_UPDATER_API', 'https://timersys.com/' );

define( 'SHIPAREA_PLUGIN_DIR' , plugin_dir_path(__FILE__) );
define( 'SHIPAREA_PLUGIN_URL' , plugin_dir_url(__FILE__) );
define( 'SHIPAREA_PLUGIN_BASE' , plugin_basename( __FILE__ ) );


/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-shiparea.php';


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shiparea-activator.php
 */
function shiparea_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shiparea-activator.php';
	ShipArea_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shiparea-deactivator.php
 */
function shiparea_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shiparea-deactivator.php';
	ShipArea_Deactivator::deactivate();
}


register_activation_hook( __FILE__, 'shiparea_activate' );
register_deactivation_hook( __FILE__, 'shiparea_deactivate' );

/**
 * Store the plugin global
 */
global $shiparea;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */

function ShipArea() {
	return ShipArea::instance();
}

$GLOBALS['shiparea'] = ShipArea();
<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://proacto.software/
 * @since      1.0.0
 *
 * @package    Prt_Woo_Portmone
 * @subpackage Prt_Woo_Portmone/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Prt_Woo_Portmone
 * @subpackage Prt_Woo_Portmone/admin
 * @author     Proacto <workyura23@gmail.com>
 */
class Prt_Woo_Portmone_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;



		// Call function options_page, that generates options page
		add_action( "admin_menu", array($this, "options_page") );


	}

	// Fuction, that generates option page
	public function options_page(){
		// add_options_page( 
		// 	"Portmone Options",
		// 	"Portmone Proacto", 
		// 	"manage_options", 
		// 	"ortmone-options",
		// 	array($this, 'render')
		// );
	}

	// Fuction, that includes main template for plugin options page
	public function render(){
		require plugin_dir_path( dirname(__FILE__) ) . "admin/partials/prt-woo-portmone-admin-display.php";
	}



	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prt_Woo_Portmone_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prt_Woo_Portmone_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prt-woo-portmone-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prt_Woo_Portmone_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prt_Woo_Portmone_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/prt-woo-portmone-admin.js', array( 'jquery' ), $this->version, false );

	}

}

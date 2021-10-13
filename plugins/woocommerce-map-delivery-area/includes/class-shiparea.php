<?php

class ShipArea {

	/**
	 * Plugin Instance
	 */
	protected static $_instance = null;

	/**
	 * Admin Instance
	 */
	public $admin;

	/**
	 * Public Instance
	 */
	public $public;

	/**
	 * License Instance
	 */
	public $license;

	/**
	 * Ajax Instance
	 */
	public $ajax;


	/**
	 * Ensures only one instance is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'shiparea' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'shiparea' ), '2.1' );
	}


	function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->set_objects();

		add_action( 'plugins_loaded', [ $this, 'add_method' ], 0 );
		add_action( 'plugins_loaded', [ $this, 'updater' ] );
	}

	function load_dependencies() {

		require_once SHIPAREA_PLUGIN_DIR . '/vendor/autoload.php';
		require_once SHIPAREA_PLUGIN_DIR . '/includes/functions.php';
		require_once SHIPAREA_PLUGIN_DIR . '/includes/class-shiparea-i18n.php';
		require_once SHIPAREA_PLUGIN_DIR . '/includes/class-shiparea-cpt.php';
		require_once SHIPAREA_PLUGIN_DIR . '/includes/class-shiparea-define.php';
		require_once SHIPAREA_PLUGIN_DIR . '/includes/class-shiparea-ajax.php';
		require_once SHIPAREA_PLUGIN_DIR . '/public/class-shiparea-public.php';

		if( is_admin() ) {
			require_once SHIPAREA_PLUGIN_DIR . '/admin/class-shiparea-license.php';
			require_once SHIPAREA_PLUGIN_DIR . '/admin/class-shiparea-updater.php';
			require_once SHIPAREA_PLUGIN_DIR . '/admin/class-shiparea-admin.php';
			require_once SHIPAREA_PLUGIN_DIR . '/admin/class-shiparea-settings.php';
		}
	}


	function notice_woo() {
		echo '<div class="error"><p>' . sprintf( __( 'Plugin Woocommerce Shipping with Delivery Area depends on the last version of %s to work!', 'shiparea' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
	}

	function add_method() {
		if ( ! class_exists( 'WC_Shipping_Method' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_woo'] );
			return;
		}

		// Method Shipping
		new ShipArea_Define();
	}

	/**
	 * Load plugin updater.
	 *
	 * @since 2.0.0
	 */
	public function updater() {

		if ( ! is_admin() ) {
			return;
		}

		$key = ShipArea()->license->get();

		if ( ! $key ) {
			return;
		}

		// Go ahead and initialize the updater.
		new ShipArea_Updater(
			SHIPAREA_UPDATER_API,
			SHIPAREA_PLUGIN_BASE,
			[
				'version' => SHIPAREA_VERSION,
				'license' => $key,
				'item_id' => SHIPAREA_EDD_ID,
				'author'  => 'Damian Logghe',
				'url'     => home_url(),
			]
		);

		// Fire a hook for Addons to register their updater since we know the key is present.
		do_action( 'shiparea_updater', $key );
	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ShipArea_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

		$plugin_i18n = new ShipArea_i18n();
		$plugin_i18n->set_domain( 'shiparea' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );
	}

	/**
	 * Set all global objects
	 */
	private function set_objects() {
		$this->ajax 	= new ShipArea_Ajax();
		$this->public 	= new ShipArea_Public();

		if( is_admin() ) {
			$this->license 		= new ShipArea_License();
			$this->admin 		= new ShipArea_Admin();
			$this->settings 	= new ShipArea_Settings();
		}
	}
}

?>
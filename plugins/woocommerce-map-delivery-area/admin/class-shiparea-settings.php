<?php
class ShipArea_Settings {
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_filter( 'woocommerce_get_settings_pages', [$this, 'add_class'], 10, 1);
		add_filter( 'woocommerce_get_sections_wc_timersys', [$this, 'add_sections'], 10, 1);
		add_filter( 'woocommerce_get_settings_wc_timersys', [$this, 'add_settings'], 10, 2);
		add_action( 'woocommerce_admin_field_shiparea_license', [$this, 'add_field_license'] );
		add_action( 'woocommerce_admin_field_shiparea_verify', [$this, 'add_field_verify'] );
	}

	
	/**
	 * Enqueue assets for the settings page.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if( !isset($screen->base) || strpos( $screen->base, 'page_wc-settings') === FALSE )
			return;

		if( !isset($_GET['tab']) || $_GET['tab'] != 'wc_timersys' )
			return;

		wp_enqueue_style( 'woocommerce_admin_styles' );

		if( !isset($_GET['section']) || $_GET['section'] != 'shiparea' )
			return;

		wp_enqueue_script( 'shiparea-settings-js', SHIPAREA_PLUGIN_URL . 'admin/assets/js/shiparea-settings.js', [ 'jquery' ], false, true );

		wp_localize_script( 'shiparea-settings-js', 'shiparea_vars',
			[
				'url_ajax'			=> admin_url('admin-ajax.php'),
				'url_loading'		=> admin_url('images/spinner.gif'),
				'url_success'		=> admin_url('images/yes.png'),
				'url_failure'		=> admin_url('images/no.png'),
				'nonce'				=> wp_create_nonce( 'shiparea-wpnonce' ),
			]
		);
	}


	public function add_class($settings = []) {

		$include_tab = false;

		foreach( $settings as $obj ) {
			if( $obj instanceof WC_Settings_Timersys )
				$include_tab = true;
		}

		if( ! $include_tab )
			$settings[] = include SHIPAREA_PLUGIN_DIR . '/admin/class-shiparea-wc-tabs.php';

		return $settings;
	}


	public function add_sections($sections = []) {

		if( count($sections) == 0 )
			$sections[''] = __( 'Welcome', 'shiparea' );

		$sections['shiparea'] = __( 'Shipping Map Delivery Area', 'shiparea' );

		return $sections;
	}

	public function add_settings($settings) {

		global $current_section;

		if( $current_section == 'shiparea' ) {
			$settings = [
							[
								'title'	=> __( 'Shipping Map Delivery Area', 'shiparea' ),
								'type'	=> 'title',
								'id'	=> 'wc_shiparea_page_options',
							],[
								'type' => 'shiparea_license',
							],[
								'type' => 'sectionend',
								'id'   => 'wc_shiparea_page_options',
							],[
								'title'	=> __( 'Google Maps', 'shiparea' ),
								'type'	=> 'title',
								'id'	=> 'wc_shiparea_page_google',
							],[
								'title'		=> __('API GoogleMaps','shiparea'),
								'type'		=> 'text',
								'desc'		=> sprintf(__( 'Enter your ApiKey Google Maps. You can generate a key <a href="%s">here</a>.', 'shiparea' ),'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true&hl=es'),
								'id'       => 'wc_shiparea[apikey]',
								'desc_tip' => false,
							],[
								'type' => 'shiparea_verify',
							],[
								'title'			=> __( 'Enable Autocomplete', 'shiparea' ),
								'desc'			=> __( 'Enable to Billing Address', 'shiparea' ),
								'id'			=> 'wc_shiparea[billing_autocomplete]',
								'type'			=> 'checkbox',
								'default'		=> 'no',
								'desc_tip'		=> __( 'If you check this option, Google Maps Autocomplete will be enable to billing_address_1.', 'shiparea' ),
								'checkboxgroup'	=> 'start',
								'autoload'		=> false,
							],[
								'desc'          => __( 'Enable to Shipping Address', 'shiparea' ),
								'id'            => 'wc_shiparea[shipping_autocomplete]',
								'type'          => 'checkbox',
								'default'       => 'no',
								'desc_tip'      => __( 'If you check this option, Google Maps Autocomplete will be enable to shipping_address_1.', 'shiparea' ),
								'checkboxgroup' => 'end',
								'autoload'      => false,
							],[
								'type' => 'sectionend',
								'id'   => 'wc_shiparea_page_google',
							],[
								'title'	=> __( 'Advanced', 'shiparea' ),
								'type'	=> 'title',
								'id'	=> 'wc_shiparea_page_advance',
							],[
								'title'		=> __('Include Google Maps Library in the Admin Panel','shiparea'),
								'type'		=> 'checkbox',
								'desc'		=> __( 'If you uncheck this option, you should add manually the Google Maps Library in the Admin Panel.', 'shiparea' ),
								'id'		=> 'wc_shiparea[include_admin]',
								'desc_tip'	=> false,
								'default'	=> 'yes',
							],[
								'title'		=> __('Include Google Maps Library in the Front','shiparea'),
								'type'		=> 'checkbox',
								'desc'		=> __( 'If you uncheck this option, you should add manually the Google Maps Library in the front.', 'shiparea' ),
								'id'		=> 'wc_shiparea[include_front]',
								'desc_tip'	=> false,
								'default'	=> 'yes',
							],[
								'type' => 'sectionend',
								'id'   => 'wc_shiparea_page_advance',
							],
						];
		}

		return $settings;
	}

	public function add_field_license() {

		$args_desc = [
				'type' => 'password',
				'desc_tip' => true,
				'desc' => __('Your license key provides access to updates and addons','shiparea'),
			];

		$array_desc = WC_Admin_Settings::get_field_description($args_desc);


		$args_html = [
				'tooltip_html' => $array_desc['tooltip_html'],
				'license_key' => shiparea_settings('key', '', 'shiparea_license'),
			];

		wc_get_template(
			'admin/layouts/view-license-input.php',
			$args_html,
			false,
			SHIPAREA_PLUGIN_DIR
		);
	}


	public function add_field_verify() {

		$apikey = shiparea_settings('apikey','','wc_shiparea');

		if( empty($apikey) )
			return;

		$args_html = [
				'apikey' => $apikey
			];

		wc_get_template(
			'admin/layouts/view-verify-input.php',
			$args_html,
			false,
			SHIPAREA_PLUGIN_DIR
		);
	}
}
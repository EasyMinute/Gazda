<?php

class ShipArea_Ajax {

	public function __construct() {
		add_action('wp_ajax_verify_address', [ $this, 'ajax_verify_address' ] );
		add_action('wp_ajax_nopriv_verify_address', [ $this, 'ajax_verify_address' ] );

		add_action( 'wp_ajax_shiparea_verify_license', [$this, 'verify_license']);
		add_action( 'wp_ajax_shiparea_deactivate_license', [$this, 'deactivate_license'] );
	}

	/**
	 * Activate license.
	 *
	 * @since 2.0.0
	 */
	public function verify_license() {

		// Run a security check.
		check_ajax_referer( 'shiparea-wpnonce', 'wpnonce' );

		// Check for license key.
		if ( empty( $_POST['license'] ) ) {
			wp_send_json_error( esc_html__( 'Please enter a license key.', 'shiparea' ) );
		}

		ShipArea()->license->verify_key( $_POST['license'], true );
	}


	/**
	 * Deactivate license.
	 *
	 * @since 2.0.0
	 */
	function deactivate_license() {

		// Run a security check.
		check_ajax_referer( 'shiprate-wpnonce', 'wpnonce' );

		ShipArea()->license->deactivate_key( true );
	}

	public function ajax_verify_address() {

		if ( ! wp_verify_nonce(  $_POST['wpnonce'], 'shiparea-shortcode' ) ) {
			wp_die('Busted!');
		}

		$location 	= esc_html($_POST['location']);
		$lat 		= esc_html($_POST['lat']);
		$lng 		= esc_html($_POST['lng']);


		if( strpos($location, ':') !== false )
			list($country, $state) = explode(':', $location);
		else
			list($country, $state) = array($location, '');


		$package['destination']['country'] = $country;
		$package['destination']['state'] = $state;
		$package['destination']['postcode'] = null;

		$shipping_zone = WC_Shipping_Zones::get_zone_matching_package($package);
		$shipping_methods = (array)$shipping_zone->get_shipping_methods( true );

		foreach($shipping_methods as $method) {
			if( $method->id == 'shiparea' && $method->enabled == 'yes' ) {

				$area_id = shiparea_get_intersection($lat, $lng);

				if( $area_id != false ) {

					foreach($method->instance_settings['areas'] as $areas) {
						if( in_array( $area_id,$areas['areamaps']) ) {

							$is_minprice = $method->instance_settings['is_minprice'] == 'yes' ? true : false; 

							$args = array(
										'status'		=> 'inarea',
										'shiprice_msg'	=> $areas['label'],
										'shiprice_num'	=> wc_price($areas['shiprice']),
										'is_minprice'	=> $is_minprice,
										'minprice_msg'	=> __('Minimun Price to purchase is ','letsgo'),
										'minprice_num'	=> wc_price($areas['minprice']),
									);
							break 2;
						}
					}

				} else { //Default price or avoid purchase

					$default = $method->instance_settings['default'];

					if( $default['way'] == 'default_price' ) {

						$is_minprice = $method->instance_settings['is_minprice'] == 'yes' ? true : false; 

						$args = array(
									'status'		=> 'default_price',
									'shiprice_msg'	=> $default['values']['label'],
									'shiprice_num'	=> wc_price($default['values']['shiprice']),
									'is_minprice'	=> $is_minprice,
									'minprice_msg'	=> __('Minimun Price to purchase is ','letsgo'),
									'minprice_num'	=> wc_price($default['values']['minprice']),
								);
					} else {
						$args = array(
									'status'		=> 'avoid_purchase',
									'shiprice_msg'	=> $default['error_msg'],
									'shiprice_num'	=> '',
									'is_minprice'	=> '',
									'minprice_msg'	=> '',
									'minprice_num'	=> '',
								);
					}
				}
			}
		}

		if( !isset($args) ) {
			$args = array(
						'status'		=> 'empty',
						'shiprice_msg'	=> __('Shipping Area is not settings','letsgo'),
						'shiprice_num'	=> '',
						'is_minprice'	=> '',
						'minprice_msg'	=> '',
						'minprice_num'	=> '',
					);
		}

		ob_start();
		wc_get_template('/public/layouts/shiprice_address.php', $args, false, SHIPAREA_PLUGIN_DIR );
		$template_shiprice_address = ob_get_clean();

		wp_send_json(
			apply_filters('shiparea/ajax_template/address',
				array(
					'output' => $template_shiprice_address,
				)
			)
		);
	}
}
?>
<?php

class WC_Shipping_Area_Delivery extends WC_Shipping_Method {

	public function __construct( $instance_id = 0 ) {
		$this->id 					= 'shiparea';
		$this->enabled 				= 'yes';
		$this->instance_id 			= absint( $instance_id );
		$this->method_title 		= __('Shipping Map Delivery Area','shiparea');
		$this->method_description	= __('This plugin allows calculate shipping price using delivery areas drawn in Google Maps','shiparea');
		
		$this->init_form_fields();
		$this->init_settings();
		//$this->display_errors();

		$this->title 		= $this->get_option( 'title' , __('Shipping with Delivery Area','shiparea') );
		
		$this->supports 	= [
			'shipping-zones',
			'instance-settings'
			//'instance-settings-modal'
		];

		/*if( apply_filters('shiparea/method/clear_cache', true) ) {
			//delete_transient( 'shipping-transient-version' );
			$transient_value = WC_Cache_Helper::get_transient_version( 'shipping', true );
			WC_Cache_Helper::delete_version_transients($transient_value);


			WC_Cache_Helper::delete_version_transients('shipping-transient-version');
		}*/

		add_action( 'woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ] );
	}


	public function init_form_fields() {

		$this->instance_form_fields = [
			'title' => [
				'title' 		=> __( 'Method Title', 'shiparea' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title which the user sees during shipping', 'shiparea' ),
				'default'		=> __( 'Shipping Map Delivery Area', 'shiparea' ),
				'desc_tip'		=> true,
			],
			'is_minprice' => [
				'title'			=> __('Set minimum price','shiparea'),
				'type'			=> 'checkbox',
				'label' 		=> __( 'Minimum price to purchase with delivery area. If checked, it will appear in the table below a new column called Minimum Price.', 'shiparea' ),
				'default' 		=> 'no',
			],
			'areas'		=> [ 'type' => 'areas', ],
			'default'	=> [ 'type' => 'default', ],
		];
	}


	function generate_areas_html($key, $value) {

		$key_input = $this->get_field_key( $key );
		$areamaps = shiparea_get_areamaps();
		$default = shiparea_default_area();

		$this->areas = $this->get_option( 'areas', $default);
		$this->is_minprice = $this->get_option( 'is_minprice', 'no');

		$css_minprice = $this->is_minprice == 'yes' ? '' : 'display : none !important;';

		$args = [
			'instance_id'	=> $this->instance_id,
			'shipping_id'	=> $this->id,
			'key_input'		=> $key_input,
			'areamaps'		=> $areamaps,
			'areas'			=> $this->areas,
			'css_minprice'	=> $css_minprice,
			'default'		=> $default,
		];

		ob_start();
		wc_get_template('admin/layouts/view-shiparea-manager.php', $args, false, SHIPAREA_PLUGIN_DIR );
		return ob_get_clean();
	}

	function validate_areas_field($key, $array_value) {

		$key_input = $this->get_field_key( $key );
		$array_save = [];

		if( isset($array_value['areas']) && count($array_value['areas']) > 0 ) {

			foreach($array_value['areas'] as $i => $array_fields) {

				if( !isset($array_fields['areamaps']) || count($array_fields['areamaps']) == 0 )
					continue;

				$areamaps 	= array_map( 'strval', $array_fields['areamaps'] );
				$label 		= isset($array_fields['label']) ? wc_clean( $array_fields['label'] ) : '';
		
				if( isset($array_fields['shiprice']) && !empty($array_fields['shiprice']) )		
					$shiprice = wc_format_decimal( $array_fields['shiprice'] );
				else
					$shiprice = 0;

				if( isset($array_fields['minprice']) && !empty($array_fields['minprice']) )		
					$minprice = wc_format_decimal( $array_fields['minprice'] );
				else
					$minprice = 0;

				if( isset($array_fields['free']) && !empty($array_fields['free']) )		
					$free = wc_format_decimal( $array_fields['free'] );
				else
					$free = 0;
			
				$array_save[] = [		
					'areamaps'	=> $areamaps,
					'minprice'	=> $minprice,
					'label'		=> $label,
					'shiprice'	=> $shiprice,
					'free'		=> $free,
				];
			}

			if( count($array_save) == 0 )
				throw new Exception(__('Empty Areas: Please fill the areas table correctly','shiparea'));
		}

		return apply_filters('shiparea/method/validate', $array_save, $key_input, $this->instance_id);
	}


	function generate_default_html($key, $value) {

		$key_input = $this->get_field_key( $key );

		$this->default 		= $this->get_option( 'default', shiparea_default_values() );
		$this->is_minprice	= $this->get_option( 'is_minprice', 'no');

		if( count($this->default) > 0 ) {
			$default_check = wc_clean( $this->default['way'] );
			$default_values = wc_clean( $this->default['values'] );
			$default_error_msg = wc_clean( $this->default['error_msg'] );
			$default_css_yes = $default_check == 'avoid_purchase' ? 'display: none !important;' : '';
			$default_css_no = $default_check == 'default_price' ? 'display: none !important;' : '';
		}

		$css_minprice = $this->is_minprice == 'yes' ? '' : 'display : none !important;';

		$args = [
			'instance_id'		=> $this->instance_id,
			'shipping_id'		=> $this->id,
			'key_input'			=> $key_input,
			'default_check'		=> $default_check,
			'default_values'	=> $default_values,
			'default_error_msg'	=> $default_error_msg,
			'default_css_yes'	=> $default_css_yes,
			'default_css_no'	=> $default_css_no,
			'css_minprice'		=> $css_minprice,
		];

		ob_start();
		wc_get_template('admin/layouts/view-shiparea-default.php', $args, false, SHIPAREA_PLUGIN_DIR );
		return ob_get_clean();
	}


	function validate_default_field($key, $array_value) {
		$key_input = $this->get_field_key( $key );
		
		$array_save = [];
		$default = shiparea_default_values();

		// Way
		if( isset( $array_value['way'] ) && !empty( $array_value['way'] ) )
			$array_save['way'] = wc_clean( $array_value['way'] );
		else
			$array_save['way'] = $default['way'];

		// Error_msg
		if( isset( $array_value['error_msg'] ) && !empty( $array_value['error_msg'] ) )
			$array_save['error_msg'] = wc_clean( $array_value['error_msg'] );
		else
			$array_save['error_msg'] = $default['error_msg'];
		
		// Values
		if( isset( $array_value['values'] ) && count( $array_value['values'] ) > 0 ) {

			extract($array_value['values']);

			// Label
			if( isset($label) && !empty($label) )
				$array_save['values']['label'] = wc_clean( $label );
			else
				$array_save['values']['label'] = $default['values']['label'];

			// Minimum Price
			if( isset($minprice) && !empty($minprice) )
				$array_save['values']['minprice'] = wc_format_decimal( $minprice );
			else
				$array_save['values']['minprice'] = $default['values']['minprice'];

			// Shipping Price
			if( isset($shiprice) && !empty($shiprice) )
				$array_save['values']['shiprice'] = wc_format_decimal( $shiprice );
			else
				$array_save['values']['shiprice'] = $default['values']['shiprice'];

			// Free Delivery
			if( isset($free) && !empty($free) )
				$array_save['values']['free'] = wc_format_decimal( $free );
			else
				$array_save['values']['free'] = $default['values']['free'];
		}

		return $array_save;
	}

	
	function is_available( $package = [] ) {

		$is_available = apply_filters( 'shiparea/method/is_available', true, $package, $this->instance_id );

		return $is_available;
	}

	
	function calculate_shipping( $package = [] ) {

		if( isset($_POST['action']) && $_POST['action'] == 'heartbeat' )
			return false;

		if( ! is_checkout() && ! is_cart() && ! is_ajax() )
			return false;

		$arr_address	= [];
		$apikey 		= shiparea_settings('apikey','','wc_shiparea');
		$this->areas 	= $this->get_option( 'areas', [] );
		
		if( count( $this->areas ) == 0 || empty( $apikey ) )
			return false;

		$this->is_minprice	= $this->get_option( 'is_minprice', 'no' );
		$this->default 		= $this->get_option( 'default', shiparea_default_values() );

		
		if( is_cart() && isset( $_POST['calc_shipping'] ) ) { // Calculator

			// City
			if( isset( $_POST['calc_shipping_city'] ) && ! empty( $_POST['calc_shipping_city'] ) )
				$arr_address[] = wc_clean( $_POST['calc_shipping_city'] );

			// State
			if( isset( $_POST['calc_shipping_state'] ) &&
				! empty( $_POST['calc_shipping_state'] )
			) {	
				$states = [];
				if( isset( $_POST['calc_shipping_country'] ) )
					$states = WC()->countries->get_states( $_POST['calc_shipping_country'] );
				
				$code_state = wc_clean( $_POST['calc_shipping_state'] );

				if( isset( $states[$code_state] ) )
					$arr_address[] = $states[$code_state];
			}

			//postcode
			if( isset( $_POST['calc_shipping_postcode'] ) &&
				! empty( $_POST['calc_shipping_postcode'] )
			)	$arr_address[] = wc_clean( $_POST['calc_shipping_postcode'] );

			//Country
			if( isset( $_POST['calc_shipping_country'] ) ) {

				$countries = WC()->countries->get_countries();
				$code_country = wc_clean( $_POST['calc_shipping_country'] );

				if( isset( $countries[$code_country] ) )
					$arr_address[] = $countries[$code_country];
			}

		} elseif( isset( $_POST['post_data'] ) ) { // Shipping ajax

			wp_parse_str( $_POST['post_data'], $post_data );

			$arr_address = $this->get_address( $post_data, 'shipping' );
		
		} elseif( empty( $_POST ) ) { //shipping no ajax

			$checkout = WC()->checkout();
			$section = 'billing';

			// If it is different of billing
			if ( ! wc_ship_to_billing_address_only() ) {

				$section = get_option( 'woocommerce_ship_to_destination' );

				/********************
					if the shipping fields and the billing fields are differents, the "ship_to" checkbox will be checked by Javascript
				 ******/
				$countries = WC()->countries;

				$billing_keys = array_keys( $countries->get_address_fields(
					$checkout->get_value('billing_country'),
					'billing_'
				));
				
				$shipping_keys = array_keys( $countries->get_address_fields(
					$checkout->get_value('shipping_country'),
					'shipping_'
				));

				// We verify if they are differents
				foreach( $shipping_keys as $shipping_key ) {
					$billing_key = str_replace('shipping', 'billing', $shipping_key);

					if( $checkout->get_value($shipping_key) != $checkout->get_value($billing_key) ) {
						$section = 'shipping';
						break;
					}
				}
			}

			$post_data = [
				$section.'_address_1'	=> $checkout->get_value( $section.'_address_1' ),
				$section.'_city'		=> $checkout->get_value( $section.'_city' ),
				$section.'_state'		=> $checkout->get_value( $section.'_state' ),
				$section.'_country'		=> $checkout->get_value( $section.'_country' ),
				$section.'_postcode'	=> $checkout->get_value( $section.'_postcode' ),
				'ship_to_different_address' => $section == 'shipping' ? 1 : 0,
			];

			$arr_address = $this->get_address( $post_data, 'shipping' );
		
		} elseif( isset($_POST['woocommerce-process-checkout-nonce']) ||
					isset($_POST['payment_method'])
		) { //Checkout

			$post_data = wc_clean($_POST);
			$arr_address = $this->get_address( $post_data, 'checkout' );
		}
		
		$arr_address = apply_filters('shiparea/calculate/set_address', $arr_address, $this->instance_id, $this);

		//Google Maps Geocode
		$dataship = apply_filters('shiparea/calculate/data', $this->get_data_shipping($arr_address, $apikey), $this->instance_id, $this);


		/*	Shipping Conditions	*/
		if( isset($dataship['status']) ) {

			wc_clear_notices();

			switch( $dataship['status'] ) {
				
				case 'inarea' :

					// Minimun Price
					if( $this->is_minprice == 'yes' ) {

						$subtotal = apply_filters('shiparea/calculate/subtotal', WC()->cart->get_subtotal(), $dataship, $this->instance_id );
						
						// Minimun Price to Free Delivery
						if( $dataship['free'] > 0 && $subtotal >= $dataship['free'] ) {
							
							$dataship['label'] = esc_html__('Free Shipping', 'shiparea');
							$dataship['shiprice'] = 0;
						}

						// Minimun Price to Purchase
						if( isset($_POST['shipping_method']) &&
							$this->is_shiparea($_POST['shipping_method']) &&
							$dataship['minprice'] > 0 &&
							$subtotal < $dataship['minprice']
						) {
							$minprice_msg = apply_filters( 'shiparea/calculate/minprice_msg', sprintf( esc_html__( 'For this address a minimum order of %s is required.', 'shiparea' ), wc_price( $dataship['minprice'] ) ), $dataship, $this->instance_id );
						
							wc_add_notice( $minprice_msg, 'error' );
						}
					}

					$args = apply_filters('shiparea/calculate/inarea', [
						//'id'		=> $this->id,
						'label'		=> $dataship['label'],
						'package'	=> $package,
						'cost'		=> $dataship['shiprice'],
						'calc_tax'	=> 'per_order'
					], $dataship, $this->instance_id );

					$this->add_rate( $args );

				break;

				case 'outarea' : //Default
					if( isset( $this->default['way'] ) && $this->default['way'] == 'default_price' ) {

						if( isset($this->default['values']) && count($this->default['values']) > 0 ) {
							
							extract( $this->default['values'] );

							// Minimun Price
							if( $this->is_minprice == 'yes' ) {
								
								$subtotal = apply_filters('shiparea/calculate/default_subtotal', WC()->cart->get_subtotal(), $this->default, $this->instance_id );

								// Minimun Price to Free Delivery
								if( $free > 0 && $subtotal >= $free ) {

									$label = esc_html__( 'Free Shipping', 'shiparea' );
									$shiprice = 0;
								}

								// Minimun Price to Purchase
								if( isset($_POST['shipping_method']) &&
									$this->is_shiparea($_POST['shipping_method']) &&
									$minprice > 0 && $subtotal < $minprice
								) {
									$minprice_msg = apply_filters( 'shiparea/calculate/default_minprice_msg', sprintf( esc_html__( 'For this address a minimum order of %s is required.', 'shiparea' ), wc_price( $minprice ) ), $this->default, $this->instance_id );
								
									if( ! wc_has_notice( $minprice_msg, 'error' ) )
										wc_add_notice( $minprice_msg, 'error' );
								}
							}

							$args = [
								//'id'		=> $this->id,
								'label'		=> isset( $label ) ? $label : '',
								'package'	=> $package,
								'cost'		=> isset( $shiprice ) ? $shiprice : 0,
								'calc_tax'	=> 'per_order',
							];
							
						} else {

							$args = [
								//'id'		=> $this->id,
								'label'		=> __('Default Message','shiparea'),
								'package'	=> $package,
								'cost'		=> 0,
								'calc_tax'	=> 'per_order',
							];
						}

						$args = apply_filters('shiparea/calculate/outarea', $args, $this->default, $this->instance_id);

						$this->add_rate( $args );

					} else {
						if( isset( $_POST['shipping_method'] ) &&
							$this->is_shiparea( $_POST['shipping_method'] )
						) {
							wc_add_notice($this->default['error_msg'],'error');
						}
					}
				break;

				//case 'nogeocode' :
				//case 'noaddress' : 
				//default :
			}
		}
	}


	protected function get_address($post_data, $mode = 'shipping') {

		$arr_address = [];

		$fields = [
			'mode'		=> $mode,
			'address'	=> 'address_1',
			'city'		=> 'city',
			'state'		=> 'state',
			'country'	=> 'country',
			'postcode'	=> 'postcode',
		];

		$fields = apply_filters('shiparea/calculate/fields', $fields, $this->instance_id, $post_data);

		
		/*	Verify if is billing_ or shipping_	*/
		if(	isset($post_data['ship_to_different_address']) &&
			$post_data['ship_to_different_address'] == 1
		)
			$prefix_type = 'shipping_';
		else
			$prefix_type = 'billing_';


		/* Completing the address value */
		if( isset($post_data[$prefix_type.$fields['address']]) &&
			!empty($post_data[$prefix_type.$fields['address']])
		) {
			$arr_address[] = $post_data[$prefix_type.$fields['address']];

			$nodes = shiparea_get_autocomplete_nodes();
		
			if( !in_array($prefix_type.$fields['address'], $nodes) ) {

				// City
				if( isset($post_data[$prefix_type.$fields['city']]) &&
					!empty($post_data[$prefix_type.$fields['city']]) )
					$arr_address[] = $post_data[$prefix_type.$fields['city']];

				// State
				if( isset($post_data[$prefix_type.$fields['state']]) &&
					!empty($post_data[$prefix_type.$fields['state']]) ) {
					
					$states = [];
					if( isset($post_data[$prefix_type.$fields['country']]) )
						$states = WC()->countries->get_states( $post_data[$prefix_type.$fields['country']] );
					
					$code_state = $post_data[$prefix_type.$fields['state']];

					if( isset($states[$code_state]) )
						$arr_address[] = $states[$code_state];
				}

				// Postcode
				if( isset($post_data[$prefix_type.$fields['postcode']]) &&
					!empty($post_data[$prefix_type.$fields['postcode']]) )
					$arr_address[] = $post_data[$prefix_type.$fields['postcode']];


				// Country
				if( isset($post_data[$prefix_type.$fields['country']]) &&
					!empty($post_data[$prefix_type.$fields['country']]) ){

					$countries = WC()->countries->get_countries();
					$code_country = $post_data[$prefix_type.$fields['country']];

					if( isset($countries[$code_country]) )
						$arr_address[] = $countries[$code_country];
				}
			}
		}

		return array_filter($arr_address);
	}


	protected function get_data_shipping($arr_address, $apikey) {

		if( count($arr_address) > 0 ) {
			$this->areas = $this->get_option( 'areas', [] );

			$in_address = urlencode( implode(',', $arr_address) );
			$googleMapUrl = sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s',$in_address, $apikey);

			$geocodeResponseData = file_get_contents($googleMapUrl);
			$responseData = json_decode($geocodeResponseData, true);

			if($responseData['status'] == 'OK' &&
				isset($responseData['results'][0]['geometry']['location']['lat']) &&
				!empty($responseData['results'][0]['geometry']['location']['lat']) &&
				isset($responseData['results'][0]['geometry']['location']['lng']) &&
				!empty($responseData['results'][0]['geometry']['location']['lng'])
			) {
				$lat = $responseData['results'][0]['geometry']['location']['lat'];
				$lng = $responseData['results'][0]['geometry']['location']['lng'];

				// Verify if current position is in/out area
				$area_id = shiparea_get_intersection($lat, $lng);

				if( $area_id != false ) {
					
					foreach($this->areas as $area) {
						if( in_array( $area_id, $area['areamaps'] ) ) {
							
							$output = [
								'status'	=> 'inarea',
								'area_id'	=> $area_id,
								'label'		=> $area['label'],
								'minprice'	=> $area['minprice'],
								'shiprice'	=> $area['shiprice'],
								'free'		=> $area['free'],
							];
							
							return $output;
						}
					}

					return [ 'status' => 'outarea', 'msg' => __('The client\'s position is not within any delivery area','shiparea')];
				} else
					return [ 'status' => 'outarea', 'msg' => __('The client\'s position is not within any delivery area','shiparea')];
			} else
				return ['status' => 'nogeocode', 'msg' => __('The address cannot be processed in Google Maps Geocode','shiparea')];
		}

		return ['status' => 'noaddress', 'msg' => __('No address was sent','shiparea')];
	}


	protected function is_shiparea( $array_shipping = [] ) {

		if( ! is_array( $array_shipping ) || count( $array_shipping ) == 0 )
			return false;

		$reset_ship = reset( $array_shipping );

		if( strpos( $reset_ship, ':' ) != FALSE )
			list($method_id, $instance_id) = explode( ':', $reset_ship);
		else {
			$method_id = $reset_ship;
			$instance_id = 0;
		}

		return $method_id == $this->id;
	}
}	
?>
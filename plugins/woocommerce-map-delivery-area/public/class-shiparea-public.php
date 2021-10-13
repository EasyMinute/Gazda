<?php
use Location\Coordinate;
use Location\Polygon;

class ShipArea_Public {

	public $load_inline = false;
	public $load_key = 0;

	public function __construct() {
		delete_transient( 'shipping-transient-version' );
		
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'update_order_review' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_shortcode( 'shiparea', [ $this, 'add_shiparea_shortcode' ] );
		add_shortcode( 'areamaps', [ $this, 'add_areamaps_shortcode' ] );
	}


	/**
	 * Register the JavaScript for the checkout page.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style(
			'shiparea-style',
			SHIPAREA_PLUGIN_URL . '/public/assets/css/shiparea-shortcode.css',
			[], false, false
		);
				
		if( !is_checkout() )
			return;

		$nodes = shiparea_get_autocomplete_nodes();

		if( count($nodes) > 0 ) {

			$apikey = shiparea_settings('apikey', '', 'wc_shiparea');

			if( empty($apikey) )
				return;

			$ifront = shiparea_settings('include_front', '', 'wc_shiparea');
			$handle_gmap = apply_filters('shiparea/public/handle_gmaps', 'shiparea-gmap');
		

			// If is include_front
			if( $ifront == 'yes' && !wp_script_is( $handle_gmap, 'enqueued' ) &&
				apply_filters('shiparea/public/include_front', true) ) {

				$url_js = sprintf('//maps.googleapis.com/maps/api/js?key=%s&libraries=geometry,places', $apikey);

				wp_enqueue_script($handle_gmap, $url_js, ['jquery'], false, true);
				
			}

			$handle_checkout = apply_filters('shiparea/checkout/handle_js', 'shiparea-checkout');

			wp_enqueue_script($handle_checkout, SHIPAREA_PLUGIN_URL.'/public/assets/js/shiparea-checkout.js', [$handle_gmap, 'jquery'], false, true);

			wp_localize_script( $handle_checkout, 'shiparea_vars',
				apply_filters( 'shiparea/checkout/vars',
					[
						'nodes'		=> $nodes,
						'opts'		=> [ 'types' => ['geocode'] ],
						'filler'	=> []
					]
				)
			);
		}
	}


	/**
	 * Register Shortcode
	 *
	 * @since    1.0.0
	 */
	public function add_areamaps_shortcode($atts) {

		$apikey = shiparea_settings('apikey', '', 'wc_shiparea');

		if( empty($apikey) )
			return false;

		$param = shortcode_atts( array(
			'id'		=> 0,
			'w'			=> '100%',
			'h'			=> '300px',
		), $atts );

		if( !isset($param['id']) || absint($param['id']) == 0 )
			return false;

		
		$id 	= absint($param['id']);
		$style 	= '';
		$style 	.= 'width:' . sanitize_text_field( $param['w'] ) . ';';
		$style 	.= 'height:' . sanitize_text_field( $param['h'] ) . ';';


		$ifront = shiparea_settings('include_front', '', 'wc_shiparea');
		$handle_gmap = apply_filters('shiparea/public/handle_gmaps', 'shiparea-gmap');
		

		// If is include_front
		if( $ifront == 'yes' && !wp_script_is( $handle_gmap, 'enqueued' ) &&
			apply_filters('shiparea/public/include_front', true) ) {

				$url_js = sprintf('//maps.googleapis.com/maps/api/js?key=%s&libraries=geometry,places', $apikey);

				wp_enqueue_script($handle_gmap, $url_js, ['jquery'], false, true);
		}

		
		$meta = shiparea_get_meta($id);

		$acoords = [];
		foreach($meta['coords'] as $coords) {
			list($lat, $lng) = $coords;
			$acoords[] = '{ lat: '.$lat.', lng: '.$lng.'}';
		}

		$inline_js = "\n".'shipmap['.$this->load_key.'] = {
									id: "'.$id.'",
									lat: "'.$meta['lat'].'",
									lng: "'.$meta['lng'].'",
									zoom: "'.$meta['zoom'].'",
									lcolor: "'.$meta['lcolor'].'",
									coords: ['.implode(',',$acoords).']
								};';

		$this->load_key++;

		if( !$this->load_inline ) {
			wp_add_inline_script($handle_gmap, 'var shipmap = [];');
			$this->load_inline = true;
		}

		wp_add_inline_script($handle_gmap, $inline_js);

		
		$handle_areamaps = apply_filters('shiparea/areamaps/handle_js', 'areamaps-child');

		if( !wp_script_is( $handle_areamaps, 'enqueued' ) )
			wp_enqueue_script( $handle_areamaps, SHIPAREA_PLUGIN_URL . 'public/assets/js/shiparea-areamaps.js', [ $handle_gmap, 'jquery' ], false, true );


		ob_start();
		echo sprintf('<div id="areamaps-%d" style="%s"></div>', $id, $style);
		$output = ob_get_clean();
		
		return $output;
	}



	/**
	 * Register Shortcode
	 *
	 * @since    1.0.0
	 */
	public function add_shiparea_shortcode() {

		$apikey = shiparea_settings('apikey', '', 'wc_shiparea');

		if( empty($apikey) )
			return false;

		$ifront = shiparea_settings('include_front', '', 'wc_shiparea');
		$handle_gmap = apply_filters('shiparea/public/handle_gmaps', 'shiparea-gmap');;


		// If is include_front
		if( $ifront == 'yes' && !wp_script_is( $handle_gmap, 'enqueued' ) &&
			apply_filters('shiparea/public/include_front', true) ) {

				$url_js = sprintf('//maps.googleapis.com/maps/api/js?key=%s&libraries=geometry,places', $apikey);

				wp_enqueue_script($handle_gmap, $url_js, ['jquery'], false, true);
		}

		$handle_shortcode = apply_filters('shiparea/shortcode/handle_js', 'shiparea-shortcode');

		wp_enqueue_script($handle_shortcode, SHIPAREA_PLUGIN_URL.'/public/assets/js/shiparea-address.js', [ $handle_gmap, 'jquery' ], false, true);

		wp_localize_script( $handle_shortcode, 'shipaddress_var',
			apply_filters( 'shiparea/shortcode/vars',
				[
					'ajaxurl'	=> admin_url('admin-ajax.php'),
					'action'	=> 'verify_address',
					'input'		=> 'shiparea_verify_address_input',
					'opts'		=> [ 'types' => ['geocode'] ],
					'notfound'	=> __('No details available for input','shiparea'),
					'wpnonce'	=> wp_create_nonce( 'shiparea-shortcode' ),
				]
			)
		);

		
		$args = [
					'base_country'	=> WC()->countries->get_base_country(),
					'base_state'	=> WC()->countries->get_base_state(),
				];


		ob_start();
		wc_get_template('public/layouts/verify_address.php', $args, false, SHIPAREA_PLUGIN_DIR );
		$output = ob_get_clean();
		
		return $output;
	}


	public function update_order_review( $post_data ) {

		$packages = WC()->cart->get_shipping_packages();
		
		foreach ($packages as $package_key => $package ) {
			WC()->session->set( 'shipping_for_package_' . $package_key, false ); // Or true
		}
	}
}
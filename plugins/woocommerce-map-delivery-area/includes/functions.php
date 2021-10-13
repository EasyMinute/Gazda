<?php
use Location\Coordinate;
use Location\Polygon;

/**
*	Get all Areamaps Posts Type from Delivery Area Free Plugin
*
*	@return Array $output
*/
if( !function_exists('shiparea_get_areamaps') ) {
	function shiparea_get_areamaps( $meta = false ) {

		$output = [];
		$count_posts = wp_count_posts('areamaps');

		if( isset($count_posts->publish) && $count_posts->publish == 0 )
			return apply_filters('shiparea/global/empty_areamaps', [], $count_posts);

		$array_posts = get_posts( apply_filters('shiparea/global/args_areamaps',
							[
								'post_status'		=> 'publish',
								'post_type'			=> 'areamaps',
								'posts_per_page'	=> -1
							], $meta )
						);

		foreach( $array_posts as $post ) {
			$output[$post->ID] = [
							'id'	=> $post->ID,
							'text'	=> $post->ID . ' : ' . $post->post_title,
						];

			if( $meta ) {
				$value = shiparea_get_meta($post->ID);
				$extra = [ 'coords' => isset($value['coords']) ? $value['coords'] : [] ];
				$output[$post->ID]['meta'] = $extra;
			}
		}

		return apply_filters('shiparea/global/list_areamaps', $output, $array_posts);
	}
}


/**
*	Get data default to shipping method
*
*	@return Array $default
*/
if( !function_exists('shiparea_default_area') ) {
	function shiparea_default_area() {

		$default = [[
					'areamaps'	=> [],
					'minprice'	=> 0,
					'label'		=> '',
					'shiprice'	=> 0,
					'free'		=> 0,
				]];

		return apply_filters('shiparea/global/default_vars', $default);
	}
}


/**
*	Get data default to shipping method
*	when the address is not inside some area
*
*	@return Array $default
*/
if( !function_exists('shiparea_default_values') ) {
	function shiparea_default_values() {

		$default = [
					'way'		=> 'default_price',
					'error_msg'	=> __('Your address is outside of our delivery area','shiparea'),
					'values'	=> [
									'minprice'	=> 0,
									'label'		=> '',
									'shiprice'	=> 0,
									'free'		=> 0,
								]
					];

		return apply_filters('shiparea/global/default_vars', $default);
	}
}



/**
*	Get Instances data to shipping method
*
*	@return Array $output
*/
if( !function_exists('shiparea_get_instances') ) {
	function shiparea_get_instances() {

		global $wpdb;

		$output = [];

		$query = 'SELECT
						option_value
					FROM
						'.$wpdb->options.'
					WHERE
						option_name LIKE "woocommerce_shiparea_%_settings"';

		$results = $wpdb->get_col($query);

		if( is_array($results) && count($results) > 0 )
			$output = array_map('maybe_unserialize', $results);

		return $output;
	}
}


/**
*	Get Areamap ID where a latitude and longitude is located
*	
*	@param float $lat
*	@param float $lng
*	@return Int $areamap['id']
*/
if( !function_exists('shiparea_get_intersection') ) {
	function shiparea_get_intersection($lat, $lng) {

		$areamaps = (array)shiparea_get_areamaps(true);

		if( count($areamaps) > 0 ) {

			$position = new Coordinate((float)$lat, (float)$lng);

			foreach($areamaps as $areamap) {
				if( isset( $areamap['meta']['coords'] ) ) {

					$polygon = new Polygon();

					foreach($areamap['meta']['coords'] as $latlng) {
						list($lat, $lng) = array_map( 'floatval', $latlng );
						$polygon->addPoint( new Coordinate( $lat, $lng ) );
					}

					if( $polygon->contains($position) )
						return $areamap['id'];
				}
			}
		}

		return false;
	}
}


/**
 * Retrieve a value from options
 *
 * @param $key
 * @param bool $default
 * @param string $option
 *
 * @return bool|mixed
 */
if( !function_exists('shiparea_settings') ) {
	function shiparea_settings( $key, $default = false, $option = 'wc_shiparea' ) {

		$key     = shiparea_sanitize_key( $key );
		$options = get_option( $option, false );
		$value   = is_array( $options ) && isset( $options[ $key ] ) && ( $options[ $key ] === '0' || ! empty( $options[ $key ] ) ) ? $options[ $key ] : $default;

		return apply_filters('shiparea/global/settings', $value);
	}
}

/**
 * Sanitize key, primarily used for looking up options.
 *
 * @param string $key
 *
 * @return string
 */
if( !function_exists('shiparea_sanitize_key') ) {
	function shiparea_sanitize_key( $key = '' ) {

		return preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
	}
}


/**
 * Retrieve a value from postmeta
 *
 * @param $key
 * @param bool $default
 * @param string $option
 *
 * @return bool|mixed
 */
if( !function_exists('shiparea_get_meta') ) {
	function shiparea_get_meta( $post_id, $meta_key = 'areamaps_meta' ) {

		$output = [];
		$meta_value = get_post_meta($post_id, $meta_key, true);

		$defaults = (array)shiparea_get_default_meta();

		foreach( $defaults as $default_key => $default_value ) {

			if( isset($meta_value[$default_key]) ) {

				if( $default_key == 'coords' )
					$output['isdefault'] = false;

				$output[$default_key] = $meta_value[$default_key];
			}
			else
				$output[$default_key] = $default_value;
		}

		return apply_filters('shiparea/global/meta', $output);
	}
}



if( !function_exists('shiparea_get_default_meta') ) {
	function shiparea_get_default_meta() {
		$defaults = [
			'lat'		=> -34.620543,
			'lng'		=> -58.5504494,
			'lcolor'	=> '#00FF00',
			'zoom'		=> 13,
			'isdefault'	=> true,
			'coords'	=> [
							[ +0.03501, +0.000001 ],
							[ +0.02, +0.045 ],
							[ -0.0201, +0.04501 ],
							[ -0.035, +0.0001 ],
							[ -0.02011, -0.0451 ],
							[ +0.02100001, -0.045111 ],
						],
		];

		return apply_filters('shiparea/global/default', $defaults);
	}
}



if( !function_exists('shiparea_get_autocomplete_nodes') ) {
	function shiparea_get_autocomplete_nodes() {

		$billing_autocomplete = shiparea_settings('billing_autocomplete', 'no', 'wc_shiparea');
		$shipping_autocomplete = shiparea_settings('shipping_autocomplete', 'no', 'wc_shiparea');

		$nodes = [];
			
		if( $billing_autocomplete == 'yes' )
			$nodes[] = 'billing_address_1';

		if( $shipping_autocomplete == 'yes' )
			$nodes[] = 'shipping_address_1';

		return apply_filters('shiparea/global/autocomplete_nodes', $nodes);
	}
}
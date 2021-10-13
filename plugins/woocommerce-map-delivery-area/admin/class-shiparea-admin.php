<?php
class ShipArea_Admin {

	function __construct() {
		add_action( 'admin_menu', [$this, 'add_submenu'], 30 );
		add_filter( 'plugin_action_links_'.SHIPAREA_PLUGIN_BASE, [ $this, 'setting_internal_link' ] );

		// Scripts JS
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );

		//	Custom Fields in Areamaps Custom Post Type
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_areamaps', [ $this, 'save_post' ], 10, 3);

		// Include JS Admin
		add_action( 'wp_ajax_areamaps_js', [ $this, 'add_map_js' ] );
		add_action( 'wp_ajax_nopriv_areamaps_js', [ $this, 'add_map_js' ] );

		// Admin Column in Areamaps Custom Post Type
		add_filter('manage_areamaps_posts_columns', [ $this,'add_name_column' ] );
		add_action('manage_areamaps_posts_custom_column', [ $this, 'add_value_column'], 10, 2);
	}


	/**
	 * Register the JavaScript for the areamaps custom post type
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		$page_files = [ 'post-new.php', 'edit.php', 'post.php' ];

		if( get_post_type() === 'areamaps' && in_array( $pagenow, $page_files ) ) {

			$apikey = shiparea_settings('apikey','','wc_shiparea');
			$iadmin = shiparea_settings('include_admin','no','wc_shiparea');

			if( !empty($apikey) && $iadmin == 'yes' ) {

				$handle_gmap = apply_filters('shiparea/admin/handle_gmaps', 'areamaps-gmap');
				$url_js = sprintf('//maps.googleapis.com/maps/api/js?key=%s&libraries=geometry,places', $apikey);

				wp_enqueue_script($handle_gmap, $url_js, ['jquery'], false, true );
			}
		}


		if( isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' &&
			isset( $_GET['tab'] ) && $_GET['tab'] == 'shipping' &&
			isset( $_GET['instance_id'] ) && is_numeric( $_GET['instance_id'] )
		) {

			wp_enqueue_style(
				'shiparea-method-css',
				SHIPAREA_PLUGIN_URL . 'admin/assets/css/shiparea-method.css'
			);

			wp_enqueue_script(
				'shiparea-method-js',
				SHIPAREA_PLUGIN_URL . 'admin/assets/js/shiparea-method.js',
				[ 'jquery' ], false, true
			);

			$areamaps = shiparea_get_areamaps();

			wp_localize_script( 'shiparea-method-js', 'shiparea_var', [ 'areamaps' => $areamaps ] );
		}
	}


	/**
	 * Add submenu to Woocommerce
	 *
	 * @since    1.0.0
	 */
	public function add_submenu() {
		add_submenu_page(
			'woocommerce',
			__('Delivery Areas','shiparea'),
			__('Delivery Areas','shiparea'),
			'manage_options',
			'edit.php?post_type=areamaps'
		);
	}


	/**
	 *	Internal Links
	 * @since    1.0.0
	 */
	public function setting_internal_link( $links ) {
		$settings = [
						'settings' => sprintf('<a href="%s">%s</a>',
							admin_url('admin.php?page=wc-settings&tab=wc_timersys&section=shiparea'),
							__( 'Settings', 'shiparea' )
						),
						'zone' => sprintf('<a href="%s">%s</a>',
							admin_url('admin.php?page=wc-settings&tab=shipping'),
							__( 'Zones', 'shiparea' )
						),
					];
	
		return array_merge( $settings, $links );
	}


	/**
	 * Add columns to areamaps admin panel
	 *
	 * @since    1.0.0
	 */
	public function add_name_column($columns) {

		foreach($columns as $key_column => $value_column) {
			
			$ok_columns[$key_column] = $value_column;

			if( $key_column == 'title' )
				$ok_columns['shortcode'] = __( 'Shortcode', 'shiparea' );
		}
		
		return $ok_columns;
	}

	/**
	 * Add column values to areamaps admin panel
	 *
	 * @since    1.0.0
	 */
	public function add_value_column($column, $post_id) {

		if($column == 'shortcode') {
			$meta_shortcode = '[areamaps id='.$post_id.']';
			echo '<input type="text" size="20" value="'.$meta_shortcode.'" readonly />';
		}
	}


	/**
	 * Add custom fields in areamaps custom post type
	 *
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'areamaps_map',
			__('Map','shiparea'),
			[ $this, 'input_map' ],
			'areamaps',
			'normal',
			'high'
		);

		add_meta_box(
			'areamaps_shortcode',
			__('Shortcode', 'shiparea'),
			[ $this, 'input_shortcode' ],
			'areamaps',
			'side',
			'low'
		);
	}

	/**
	 * Add JS script from Ajax
	 *
	 * @since    1.0.0
	 */
	public function add_map_js() {

		include_once SHIPAREA_PLUGIN_DIR . 'admin/assets/js/shiparea-admin.php';

		die();
	}


	/**
	 * Shortcode custom fields
	 *
	 * @since    1.0.0
	 */
	public function input_shortcode() {
		global $post;

		$opts = shiparea_get_meta($post->ID);
		$meta_shortcode = isset($opts) ? '[areamaps id='.$post->ID.']' : '';

		echo __('Please insert this shortcode in you page', 'shiparea');
		echo '<input type="text" size="25" name="areamaps_shortcode" id="areamaps_shortcode" value="'.$meta_shortcode.'" readonly />';
	}


	/**
	 * Map custom fields
	 *
	 * @since    1.0.0
	 */
	public function input_map() {
		global $post;

		$apikey = shiparea_settings('apikey','','wc_shiparea');
		
		if( !empty($apikey) && isset($post->ID) && is_numeric($post->ID) ) {

			$args_meta = shiparea_get_meta($post->ID);
			$handle_gmap = apply_filters('shiparea/admin/handle_gmaps', 'areamaps-gmap');
			$handle_meta = apply_filters('shiparea/admin/handle_meta', 'areamaps-meta');
			$url_js = sprintf(admin_url('admin-ajax.php?action=areamaps_js&id=%d'), $post->ID);

			wp_enqueue_script($handle_meta, $url_js, [ $handle_gmap, 'jquery' ], false, true );

			wc_get_template(
				'/admin/layouts/view-maps-input.php',
				$args_meta,
				false,
				SHIPAREA_PLUGIN_DIR
			);
		}
		else
			echo sprintf(__('Please enter your ApiKey Google Maps in the <a href="%s" title="%s">settings section</a>','shiparea'), admin_url('admin.php?page=wc-settings&tab=wc_timersys&section=shiparea'), 'settings section' );
	}


	/**
	 * save custom fields at areamaps custom post type
	 *
	 * @since    1.0.0
	 */
	public function save_post($post_id, $post, $update) {

		if ( $post->post_type != 'areamaps' )
        	return;
    	
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return;

		if( wp_is_post_revision( $post_id ) )
			return;
	 
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		if( isset($_POST['areamaps_coords']) && !empty($_POST['areamaps_coords']) ) {

			$array_coords = explode('),(',$_POST['areamaps_coords']);

			if( is_array($array_coords) && count($array_coords) > 0 ) {

				foreach($array_coords as $value_coords) {
					$latlng = str_replace(array("(", ")"), array("",""), $value_coords);
					$array_latlng[] = array_map('sanitize_text_field', explode(',',$latlng));
				}
			} else
				$array_latlng = $this->options_default['coords'];


			$array_save_post = array(
				'lcolor' => !empty($_POST['areamaps_lcolor']) ? sanitize_text_field($_POST['areamaps_lcolor']) : $this->options_default['lcolor'],
				'lat'	 => !empty($_POST['areamaps_lat']) ? sanitize_text_field($_POST['areamaps_lat']) : $this->options_default['lat'],
				'lng'	 => !empty($_POST['areamaps_lng']) ? sanitize_text_field($_POST['areamaps_lng']) : $this->options_default['lng'],
				'coords' => $array_latlng,
				'zoom'	 => !empty($_POST['areamaps_zoom']) ? sanitize_text_field($_POST['areamaps_zoom']) : $this->options_default['zoom']
			);

			update_post_meta($post_id, 'areamaps_meta', $array_save_post);
		}
		
		return $post_id;
	}
}
?>
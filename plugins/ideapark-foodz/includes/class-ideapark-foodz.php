<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Ideapark_foodz {

	private static $_instance = null;
	public $settings = null;
	public $_version;
	public $_token;
	public $file;
	public $dir;
	public $assets_dir;
	public $assets_url;
	public $script_suffix;
	private $sorted_post_types = [];


	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'ideapark_foodz';

		// Load plugin environment variables
		$this->file          = $file;
		$this->dir           = dirname( $this->file );
		$this->assets_dir    = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url    = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, [ $this, 'install' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 10, 1 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_styles' ], 10, 1 );
		add_action( 'wp_ajax_update-post-order', [ $this, 'update_post_order' ] );
		add_action( 'pre_get_posts', [ $this, 'set_default_sorting_mode' ] );

		if ( is_admin() ) {
			$this->admin = new Ideapark_foodz_Admin_API();
		}

		$this->load_plugin_textdomain();
	} // End __construct ()

	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', $options = [] ) {

		if ( !$post_type || !$plural || !$single ) {
			return;
		}

		$post_type = new Ideapark_foodz_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	function update_post_order() {
		global $wpdb;

		parse_str( $_POST['order'], $data );

		if ( !is_array( $data ) ) {
			return false;
		}

		foreach ( $this->sorted_post_types as $object ) {
			$result = $wpdb->get_results( "
					SELECT count(*) as cnt, max(menu_order) as max, min(menu_order) as min 
					FROM $wpdb->posts 
					WHERE post_type = '" . $object . "' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
				" );
			if ( $result[0]->cnt == 0 || $result[0]->cnt == $result[0]->max ) {
				continue;
			}

			$results = $wpdb->get_results( "
					SELECT ID 
					FROM $wpdb->posts 
					WHERE post_type = '" . $object . "' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future') 
					ORDER BY menu_order ASC
				" );
			foreach ( $results as $key => $result ) {
				$wpdb->update( $wpdb->posts, [ 'menu_order' => $key + 1 ], [ 'ID' => $result->ID ] );
			}
		}

		$id_arr = [];
		foreach ( $data as $key => $values ) {
			foreach ( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}

		$menu_order_arr = [];
		foreach ( $id_arr as $key => $id ) {
			$results = $wpdb->get_results( "SELECT menu_order FROM $wpdb->posts WHERE ID = " . intval( $id ) );
			foreach ( $results as $result ) {
				$menu_order_arr[] = $result->menu_order;
			}
		}

		sort( $menu_order_arr );

		foreach ( $data as $key => $values ) {
			foreach ( $values as $position => $id ) {
				$wpdb->update( $wpdb->posts, [ 'menu_order' => $menu_order_arr[$position] ], [ 'ID' => intval( $id ) ] );
			}
		}
	}

	public function set_default_sorting_mode( $wp_query ) {
		if ( !empty( $this->sorted_post_types ) ) {
			if ( is_admin() ) {
				if ( isset( $wp_query->query['post_type'] ) && !isset( $_GET['orderby'] ) ) {
					if ( in_array( $wp_query->query['post_type'], $this->sorted_post_types ) ) {
						$wp_query->set( 'orderby', 'menu_order' );
						$wp_query->set( 'order', 'ASC' );
					}
				}
			} else {
				if ( isset( $wp_query->query['post_type'] ) && !is_array( $wp_query->query['post_type'] ) && in_array( $wp_query->query['post_type'], $this->sorted_post_types ) ) {
					if ( isset( $wp_query->query['suppress_filters'] ) ) {
						if ( $wp_query->get( 'orderby' ) == 'date' ) {
							$wp_query->set( 'orderby', 'menu_order' );
						}
						if ( $wp_query->get( 'order' ) == 'DESC' ) {
							$wp_query->set( 'order', 'ASC' );
						}
					} else {
						if ( !$wp_query->get( 'orderby' ) ) {
							$wp_query->set( 'orderby', 'menu_order' );
						}
						if ( !$wp_query->get( 'order' ) ) {
							$wp_query->set( 'order', 'ASC' );
						}
					}
				}
			}
		}
	}

	public function set_sorted_post_types( $post_types ) {
		$this->sorted_post_types = $post_types;
	}

	public function admin_enqueue_scripts( $hook = '' ) {
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker');
		if ( !empty( $this->sorted_post_types ) && isset( $_GET['post_type'] ) && !isset( $_GET['taxonomy'] ) && in_array( $_GET['post_type'], $this->sorted_post_types ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_register_script( $this->_token . '-sort', esc_url( $this->assets_url ) . 'js/sort' . $this->script_suffix . '.js', [ 'jquery' ], $this->_version, true );
			wp_enqueue_script( $this->_token . '-sort' );
		}
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/settings' . $this->script_suffix . '.js', [ 'jquery' ], $this->_version, true );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()


	public function admin_enqueue_styles( $hook = '' ) {
		wp_enqueue_style( 'wp-color-picker');
		if ( !empty( $this->sorted_post_types ) && isset( $_GET['post_type'] ) && !isset( $_GET['taxonomy'] ) && in_array( $_GET['post_type'], $this->sorted_post_types ) ) {
			wp_register_style( $this->_token . '-sort', esc_url( $this->assets_url ) . 'css/sort.css', [], $this->_version );
			wp_enqueue_style( $this->_token . '-sort' );
		}
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', [], $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()


	public function load_plugin_textdomain() {
		$domain = 'ideapark-foodz';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		if ( !load_textdomain( $domain, get_template_directory() . '/languages/'. $domain . '-' . $locale . '.mo' ) ) {
			load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
		}
	} // End load_plugin_textdomain ()


	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()


	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ideapark-foodz' ), $this->_version );
	} // End __clone ()


	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ideapark-foodz' ), $this->_version );
	} // End __wakeup ()


	public function install() {
		$this->_log_version_number();
	} // End install ()


	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
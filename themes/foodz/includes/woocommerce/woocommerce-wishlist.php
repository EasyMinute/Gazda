<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ideapark_Wishlist' ) ) {

	class Ideapark_Wishlist {

		public $cookie_name = 'ip-wishlist-items';

		private static $_instance = null;
		private $wishlist_ids = [];
		private $view_mode = false;

		public function __construct() {

			add_action( 'wp_ajax_ideapark_wishlist_toggle', [ $this, 'ideapark_toggle' ] );
			add_action( 'wp_ajax_nopriv_ideapark_wishlist_toggle', [ $this, 'ideapark_toggle' ] );

			if ( isset( $_GET['ip_wishlist_share'] ) && ! empty( $_GET['ip_wishlist_share'] ) ) {
				$this->wishlist_ids = $this->get_share_url_ids();
				$this->view_mode    = true;
			} else {
				$this->wishlist_ids = $this->get_products_cookie();
				$this->view_mode    = false;
			}

		}

		function ideapark__button( $button_class = '', $svg_class = '' ) {
			global $product;
			echo '<button class="c-wishlist__btn c-wishlist__item-' . esc_attr( $product->get_id() ) . '-btn ' . $button_class . '" data-product-id="' . esc_attr( $product->get_id() ) . '" data-title="' . esc_attr__( 'Wishlist', 'foodz' ) . '">' . ideapark_svg( 'wishlist', $svg_class ) . '</button>';
		}

		public function ids() {
			return $this->wishlist_ids;
		}

		public function view_mode() {
			return $this->view_mode;
		}

		function ideapark_toggle() {
			$return_data = [];
			$product_id  = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : null;

			if ( $product_id ) {
				$wishlist_ids = $this->get_products_cookie();

				if ( in_array( $product_id, $wishlist_ids ) ) {
					$wishlist_ids          = array_diff( $wishlist_ids, [ $product_id ] );
					$return_data['status'] = "0";
				} else {
					$wishlist_ids[]        = $product_id;
					$return_data['status'] = "1";
				}

				$this->set_products_cookie( $wishlist_ids );

				$return_data['count'] = count( $wishlist_ids );

				if ( $page_id = ideapark_mod( 'wishlist_page' ) ) {
					$return_data['share_link'] = get_permalink( $page_id ) . ( strpos( get_permalink( $page_id ), '?' ) === false ? '?' : '&' ) . 'ip_wishlist_share=' . implode( ',', $wishlist_ids );
				}
			}

			echo json_encode( $return_data );
			exit;
		}

		function set_products_cookie( $wishlist_ids = [] ) {
			$wishlist_ids_json = json_encode( stripslashes_deep( $wishlist_ids ) );
			wc_setcookie( $this->cookie_name, $wishlist_ids_json, time() + 60 * 60 * 24 * 30, false );
		}

		private function get_products_cookie() {
			if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {
				return json_decode( stripslashes( $_COOKIE[ $this->cookie_name ] ), true );
			}

			return [];
		}

		private function get_share_url_ids() {
			$wishlist_ids = [];
			$e            = explode( ',', $_GET['ip_wishlist_share'] );
			foreach ( $e AS $id ) {
				if ( (int) $id > 0 ) {
					$wishlist_ids[] = (int) $id;
				}
			}

			return $wishlist_ids;
		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'foodz' ), IDEAPARK_THEME_VERSION );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'foodz' ), IDEAPARK_THEME_VERSION );
		}
	}
}

if ( ! function_exists( 'ideapark_wishlist' ) ) {
	function ideapark_wishlist() {
		return Ideapark_Wishlist::instance();
	}
}

ideapark_wishlist();

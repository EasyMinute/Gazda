<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function ideapark_woocommerce_functions() {
	if ( ! function_exists( 'wc_get_cart_remove_url' ) ) {
		function wc_get_cart_remove_url( $cart_item_key ) {
			$cart_page_url = wc_get_page_permalink( 'cart' );

			return apply_filters( 'woocommerce_get_remove_url', $cart_page_url ? wp_nonce_url( add_query_arg( 'remove_item', $cart_item_key, $cart_page_url ), 'woocommerce-cart' ) : '' );
		}
	}
}

add_action( 'init', 'ideapark_woocommerce_functions' );
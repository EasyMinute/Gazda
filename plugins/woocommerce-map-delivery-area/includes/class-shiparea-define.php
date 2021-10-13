<?php
class ShipArea_Define {
	function __construct() {
		add_action( 'woocommerce_shipping_init', [$this, 'include_file'] );
		add_filter( 'woocommerce_shipping_methods', [$this, 'include_class'] );
	}


	function include_file() {
		if ( ! class_exists( 'WC_Shipping_Area_Delivery' ) )
			require_once SHIPAREA_PLUGIN_DIR . '/includes/class-shiparea-method.php';
	}

	function include_class( $methods ) {
		$methods['shiparea'] = 'WC_Shipping_Area_Delivery';
		return $methods;
	}
}
?>
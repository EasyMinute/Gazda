<div class="c-quantity">
	<button class="h-cb c-quantity__minus js-quantity-minus">&ndash;</button>
	<input
		type="number"
		class="h-cb c-quantity__value js-quantity-value"
		step="<?php echo esc_attr( $step ); ?>"
		min="<?php echo esc_attr( $min_value ); ?>"
		max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
		name="<?php echo esc_attr( $input_name ); ?>"
		value="<?php echo esc_attr( $input_value ); ?>"
		title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
		size="4"
		pattern="<?php echo esc_attr( $pattern ); ?>"
		inputmode="<?php echo esc_attr( $inputmode ); ?>"
		data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"/>
	<button class="h-cb c-quantity__plus js-quantity-plus">+</button>
</div>


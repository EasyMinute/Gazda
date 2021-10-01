<?php
defined( 'ABSPATH' ) || exit;
global $product;
/**
 * @var $product WC_Product_Variable
 **/
$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
?>
<form
	class="c-variation__form <?php if ( ideapark_mod( 'product_variations_in_grid_selector' ) == 'radio' ) { ?>js-variations-form<?php } else { ?>variations_form cart<?php } ?>"
	action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
	method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>"
	data-product_variations="<?php echo ideapark_wrap( $variations_attr ); // WPCS: XSS ok. ?>">
	<?php if ( empty( $available_variations ) && false !== $available_variations ) { ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php } else { ?>
		<?php $cnt         = 0;
		$sizeof_attributes = sizeof( $attributes ); ?>
		<?php foreach ( $attributes as $attribute => $options ) { ?>
			<?php $cnt ++; ?>
			<?php $name = "attribute_" . sanitize_title( $attribute ); ?>
			<div class="c-variation__wrap variations">
				<?php if ( ideapark_mod( 'product_variations_in_grid_selector' ) == 'radio' ) { ?>
					<ul class="c-variation js-variation" data-attribute_name="<?php echo esc_attr( $name ); ?>"
						data-show_option_none="no">
						<?php
						$selected = $product->get_variation_default_attribute( $attribute );
						/*
						wc_dropdown_variation_attribute_options( array(
							'options'   => $options,
							'attribute' => $attribute_name,
							'product'   => $product,
							'selected'  => $selected
						) );
						*/

						if ( ! empty( $options ) ) {
							if ( $product && taxonomy_exists( $attribute ) ) {
								// Get terms if this is a taxonomy - ordered. We need the names too.
								$terms = wc_get_product_terms( $product->get_id(), $attribute, [
									'fields' => 'all',
								] );

								foreach ( $terms as $term ) {
									if ( in_array( $term->slug, $options, true ) ) { ?>
										<li class="c-variation__val">
											<label>
												<input class="h-cb c-variation__radio" type="radio"
													   name="<?php echo esc_attr( $name ); ?>"
													   value="<?php echo esc_attr( $term->slug ) ?>" <?php checked( sanitize_title( $selected ), $term->slug ); ?>>
												<span
													class="c-variation__title <?php if ( ! ideapark_mod( 'products_in_loop' ) ) { ?>c-variation__title--single<?php } ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ); ?></span>
											</label>
										</li>
									<?php }
								}
							} else {
								foreach ( $options as $option ) {
									$checked = sanitize_title( $selected ) === $selected ? checked( $selected, sanitize_title( $option ), false ) : checked( $selected, $option, false ); ?>
									<li class="c-variation__val">
										<label>
											<input class="h-cb c-variation__radio" type="radio"
												   name="<?php echo esc_attr( $name ); ?>"
												   value="<?php echo esc_attr( $option ) ?>" <?php echo ideapark_wrap( $checked ) ?>>
											<span
												class="c-variation__title <?php if ( ! ideapark_mod( 'products_in_loop' ) ) { ?>c-variation__title--single<?php } ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?></span>
										</label>
									</li>
									<?php
								}
							}
						}
						?>
					</ul>
				<?php } else { ?>
					<div class="c-variation__label">
						<label
							for="<?php echo esc_attr( sanitize_title( $attribute ) ); ?>"><?php echo wc_attribute_label( $attribute ); // WPCS: XSS ok. ?></label>
					</div>
					<div
						class="c-variation__select<?php if ( $sizeof_attributes == $cnt ) { ?> c-variation__select--last<?php } ?>">
						<?php
						wc_dropdown_variation_attribute_options( [
							'options'   => $options,
							'attribute' => $attribute,
							'product'   => $product,
							'id'        => $attribute . '_' . $product->get_id(),
						] );
						?>
					</div>
					<?php echo end( $attribute_keys ) === $attribute ? '<button class="reset_variations h-cb c-variation__reset" type="button">' . ideapark_svg( 'clear', 'c-variation__reset-svg' ) . esc_html__( 'Clear', 'woocommerce' ) . '</button>' : ''; ?>
				<?php } ?>
			</div>
		<?php } ?>

		<div class="c-variation__single single_variation_wrap">
			<?php
			/**
			 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
			 * @since  2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action( 'woocommerce_single_variation' );
			if ( ! empty( $is_loop ) ) {
				wc_get_template( 'loop/variation-add-to-cart-button.php' );
			}
			?>
		</div>

	<?php } ?>
</form>
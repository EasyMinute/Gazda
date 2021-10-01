<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $woocommerce_loop;
/**
 * @var $product WC_Product
 **/

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}


if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

$product_link = ' href="' . esc_url( get_permalink() ) . '"';

$markers = ideapark_product_markers( 'c-product-grid__marker' );

$show_variations = false;

if ( ideapark_mod( 'product_variations_in_grid' ) && $product->is_type( 'variable' ) ) {
	$show_variations = true;
	ideapark_ra( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
	ideapark_ra( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
	ideapark_ra( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	add_action( 'woocommerce_single_variation', 'ideapark_single_variation', 10 );
}

$extra_info_popup = false;

if ( $extra_info = ideapark_product_extra_info() ) {
	$extra_info_popup = '<div class="c-product-grid__marker-popup c-product-grid__marker-popup--' . ideapark_mod( 'product_mobile_layout' ) . ' js-extra-info-popup"><button class="h-cb c-product-grid__marker-popup-close js-extra-info-close" type="button">' . ideapark_svg( 'close-round', 'c-product-grid__marker-popup-svg' ) . '</button><div class="c-product-grid__marker-popup-title">' . esc_html( $extra_info['title'] ) . '</div><div class="c-product-grid__marker-popup-text">' . nl2br( esc_html( $extra_info['content'] ) ) . '</div></div>';
	$markers[]        = '<span class="c-markers__wrap c-product-grid__marker">' . ideapark_wrap( ideapark_svg( 'info' ), '<button class="h-cb h-cb--svg c-product-grid__marker-info-icon js-extra-info" type="button">', '</button>' ) . '</span>';
}

ideapark_mod_set_temp( 'products_in_loop', true );
ideapark_mod_set_temp( 'add_to_cart_class', 'c-product-grid__add-to-cart' );

?>
	<li <?php wc_product_class( 'c-product-grid__item c-product-grid__item--' . ideapark_mod( 'product_mobile_layout' ), $product ); ?>>
		<div class="c-product-grid__item-wrap c-product-grid__item-wrap--<?php echo ideapark_mod( 'product_mobile_layout' ); ?>">
			<?php
			/**
			 * woocommerce_before_shop_loop_item hook.
			 *
			 * @hooked woocommerce_template_loop_product_link_open - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item' );
			?>

			<div class="c-product-grid__thumb-wrap c-product-grid__thumb-wrap--<?php echo ideapark_mod( 'product_mobile_layout' ); ?>">
				<a<?php echo ideapark_wrap( $product_link ); ?>>
					<div class="c-badge__list c-product-grid__badges c-product-grid__badges--<?php echo ideapark_mod( 'product_mobile_layout' ); ?>">
						<?php
						/**
						 * woocommerce_before_shop_loop_item_title hook.
						 *
						 * @hooked woocommerce_show_product_loop_sale_flash - 10
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */
						do_action( 'woocommerce_before_shop_loop_item_title' );
						?>
					</div>

					<?php
					$placeholder_image = apply_filters( 'ideapark_shop_placeholder_img_src', IDEAPARK_THEME_URI . '/assets/img/placeholder.gif' );

					if ( has_post_thumbnail() ) {
						ideapark_wp_scrset_on( 'grid' );
						$product_thumbnail_id     = get_post_thumbnail_id();
						$product_thumbnail        = wp_get_attachment_image_src( $product_thumbnail_id, 'woocommerce_thumbnail' );
						$product_thumbnail_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $product_thumbnail_id, 'woocommerce_thumbnail' ) : false;
						$product_thumbnail_alt    = trim( strip_tags( get_post_meta( $product_thumbnail_id, '_wp_attachment_image_alt', true ) ) );
						ideapark_wp_scrset_off( 'grid' );

						if ( empty( $product_thumbnail_alt ) ) {
							$product_thumbnail_alt = get_the_title();
						}

						$mobile_add = ", 238px";

						switch ( ideapark_mod( 'product_mobile_layout' ) ) {
							case 'layout-product-1':
								$mobile_add = ", 238px";
								break;
							case 'layout-product-2':
								$mobile_add = ",(min-width: 375px) 150px, 238px";
								break;
							case 'layout-product-3':
								$mobile_add = ", 100px";
								break;
						}

						$product_thumbnail_sizes = "(min-width: 1170px) 238px{$mobile_add}";

						if ( ideapark_mod( 'lazyload' ) ) {
							echo '<img src="' . ideapark_empty_gif() . '" data-src="' . esc_url( $product_thumbnail[0] ) . '" ' . ( empty( $product_thumbnail_sizes ) ? 'width="' . esc_attr( $product_thumbnail[1] ) . '" height="' . esc_attr( $product_thumbnail[2] ) . '"' : '' ) . ' alt="' . esc_attr( $product_thumbnail_alt ) . '" class="c-product-grid__thumb c-product-grid__thumb--' . ideapark_mod( 'product_mobile_layout' ) . ' lazyload ' . ( is_front_page() ? 'front' : '' ) . '" ' . ( $product_thumbnail_srcset ? ' data-srcset="' . esc_attr( $product_thumbnail_srcset ) . '"' : '' ) . ( ! empty( $product_thumbnail_sizes ) ? ' data-sizes="' . esc_attr( $product_thumbnail_sizes ) . '"' : '' ) . '/>';
						} else {
							echo '<img src="' . esc_url( $product_thumbnail[0] ) . '" ' . ( empty( $product_thumbnail_sizes ) ? 'width="' . esc_attr( $product_thumbnail[1] ) . '" height="' . esc_attr( $product_thumbnail[2] ) . '"' : '' ) . ' alt="' . esc_attr( $product_thumbnail_alt ) . '" class="c-product-grid__thumb c-product-grid__thumb--' . ideapark_mod( 'product_mobile_layout' ) . ' ' . ( is_front_page() ? 'front' : '' ) . '" ' . ( $product_thumbnail_srcset ? ' srcset="' . esc_attr( $product_thumbnail_srcset ) . '"' : '' ) . ( ! empty( $product_thumbnail_sizes ) ? ' sizes="' . esc_attr( $product_thumbnail_sizes ) . '"' : '' ) . '/>';
						}

					} else {
						if ( wc_placeholder_img_src() ) {
							echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" class="c-product-grid__thumb c-product-grid__thumb--' . ideapark_mod( 'product_mobile_layout' ) . ' c-product-grid__thumb--placeholder" alt="' . esc_html__( 'Awaiting product image', 'woocommerce' ) . '"/>';
						}
					}
					?>
				</a>
				<?php if ( ideapark_mod( 'shop_modal' ) || ideapark_mod( 'wishlist_page' ) ) { ?>
					<div class="c-product-grid__thumb-button-list c-product-grid__thumb-button-list--<?php echo ideapark_mod( 'product_mobile_layout' ); ?>">
						<?php if ( ideapark_mod( 'shop_modal' ) ) { ?>
							<button class="h-cb c-product-grid__thumb-button js-grid-zoom" type="button" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php echo ideapark_svg( 'search', 'c-product-grid__thumb-zoom' ); ?>
							</button>
						<?php } ?>
						<?php if ( ideapark_mod( 'wishlist_page' ) ) { ?>
							<?php echo ideapark_wishlist()->ideapark__button( 'h-cb c-product-grid__thumb-button', 'c-product-grid__thumb-wishlist' ); ?>
						<?php } ?>
					</div>
				<?php } ?>
			</div>

			<div class="c-product-grid__details c-product-grid__details--<?php echo ideapark_mod( 'product_mobile_layout' ); ?>">
				<?php if ( ideapark_mod( 'product_mobile_layout' ) == 'layout-product-3' ) { ?>
					<div class="c-badge__list c-product-grid__badges-alt">
						<?php
						/**
						 * woocommerce_before_shop_loop_item_title hook.
						 *
						 * @hooked woocommerce_show_product_loop_sale_flash - 10
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */
						do_action( 'woocommerce_before_shop_loop_item_title' );
						?>
					</div>
				<?php } ?>
				<div class="c-product-grid__title">
					<a class="c-product-grid__title-link"<?php echo ideapark_wrap( $product_link ); ?>><?php the_title(); ?></a>
				</div>

				<?php if ( $markers ) { ?>
					<div class="c-markers c-product-grid__markers">
						<?php echo ideapark_wrap( implode( '', $markers ) ); ?>
					</div>
					<?php if ( $extra_info_popup ) { ?>
						<?php echo ideapark_wrap( $extra_info_popup ); ?>
					<?php } ?>
				<?php } ?>

				<?php if ( ideapark_mod( 'product_short_description' ) && ( $short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ) ) { ?>
					<div class="c-product-grid__short-desc">
						<?php echo ideapark_wrap( $short_description ); ?>
					</div>
				<?php } ?>

				<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook.
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
				<?php
				/**
				 * @var $product WC_Product_Variable
				 **/
				if ( $show_variations ) {
					ideapark_ra( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

					$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

					wc_get_template( 'loop/variable.php', [
						'available_variations' => $get_variations ? $product->get_available_variations() : false,
						'attributes'           => $product->get_variation_attributes(),
						'is_loop'              => true,
					] );
				}

				/**
				 * woocommerce_after_shop_loop_item hook.
				 *
				 * @hooked woocommerce_template_loop_product_link_close - 5
				 * @hooked woocommerce_template_loop_add_to_cart - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item' );

				if ( $show_variations ) {
					ideapark_ra( 'woocommerce_single_variation', 'ideapark_single_variation', 10 );
					add_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
					add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
					add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}
				?>
				<?php if ( ! $show_variations ) { ?>
					<span class="added_to_cart h-hidden"></span>
				<?php } ?>
				<div class="c-product-grid__quantity-wrap">
					<div class="c-quantity js-product-grid-quantity"></div>
				</div>
			</div>
		</div>
	</li>
<?php
ideapark_mod_set_temp( 'add_to_cart_class', '' );
ideapark_mod_set_temp( 'products_in_loop', false );
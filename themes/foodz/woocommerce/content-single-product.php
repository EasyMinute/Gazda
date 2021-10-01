<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @package       WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit; ?>

<?php
/**
 * woocommerce_before_single_product hook.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

global $post, $product;

if ( post_password_required() ) {
	echo get_the_password_form();

	return;
}

if ( $product->is_type( 'variable' ) ) {
	$show_variations = true;
	ideapark_ra( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
	ideapark_ra( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
	add_action( 'woocommerce_single_variation', 'ideapark_single_variation', 10 );
}

add_action( 'woocommerce_before_add_to_cart_button', 'ideapark_woocommerce_before_add_to_cart_button' );
add_action( 'woocommerce_after_add_to_cart_button', 'ideapark_woocommerce_after_add_to_cart_button' );

$with_sidebar = ideapark_mod( 'product_sidebar' ) && is_active_sidebar( 'product-sidebar' );

?>

	<section data-product_id="<?php the_ID(); ?>" id="product-<?php the_ID(); ?>" <?php wc_product_class( 'c-product', $product ); ?>>
		<div class="c-product__wrap <?php ideapark_class( $with_sidebar, 'c-product__wrap--sidebar' ); ?>">

			<div class="c-product__image <?php ideapark_class( $with_sidebar, 'c-product__image--sidebar' ); ?>">

				<?php
				/**
				 * woocommerce_before_single_product_summary hook.
				 *
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
				?>

			</div>

			<div class="c-product__summary">

				<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_breadcrumb - 3
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>

			</div>

			<?php if ( $with_sidebar ) { ?>
				<div class="c-product__sidebar">
					<?php
					/**
					 * woocommerce_sidebar hook.
					 *
					 * @hooked woocommerce_get_sidebar - 10
					 */
					do_action( 'woocommerce_sidebar' );
					?>
				</div>
			<?php } ?>
		</div>

		<?php
		ideapark_ra( 'woocommerce_before_add_to_cart_button', 'ideapark_woocommerce_before_add_to_cart_button' );
		ideapark_ra( 'woocommerce_after_add_to_cart_button', 'ideapark_woocommerce_after_add_to_cart_button' );

		if ( $product->is_type( 'variable' ) ) {
			ideapark_ra( 'woocommerce_single_variation', 'ideapark_single_variation', 10 );
			ideapark_aa( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
			ideapark_aa( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
		}

		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>

	</section><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

<?php if ( ! empty( $ideapark_video ) ) { ?>
	<?php if ( $embded_video = wp_oembed_get( $ideapark_video ) ) { ?>
		<input type="hidden" id="ip_hidden_product_video" value="<?php echo esc_js( wp_oembed_get( $ideapark_video ) ); ?>">
	<?php } ?>
<?php } ?>
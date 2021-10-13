<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @package       WooCommerce/Templates
 * @version       3.4.0
 */

defined( 'ABSPATH' ) || exit;

ideapark_mod_set_temp( 'shop_where', 'archive' );

get_header( 'shop' );

$with_sidebar = ideapark_mod( 'shop_sidebar' ) && is_active_sidebar( 'shop-sidebar' );
$with_filter = is_active_sidebar( 'filter-sidebar' );

$image = false;

if ( ideapark_mod( 'category_image_enabled' ) && is_product_category() ) {
	global $wp_query;
	$cat           = $wp_query->get_queried_object();
	$attachment_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	$image         = wp_get_attachment_image_src( $attachment_id, 'full' );
	$is_parallax   = ideapark_mod( 'home_big_banner_parallax' ) && ! is_rtl();
}

$subcategories = woocommerce_maybe_show_product_subcategories();
?>

<?php
/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 */
do_action( 'woocommerce_before_main_content' );
?>


<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
	<header
		class="l-section c-page-header c-page-header--category <?php ideapark_class( ideapark_mod( 'category_image_parallax' ) && $image, 'c-page-header--parallax parallax' ); ?>" <?php if ( ideapark_mod( 'category_image_enabled' ) && ideapark_mod( 'category_image_parallax' ) && $image ) { ?> data-z-index="1" data-speed="0.6" data-parallax="scroll" data-image-src="<?php echo esc_url( $image[0] ); ?>"<?php } else if ( $image ) { ?><?php ideapark_style( "background-image: url(" . esc_url( $image[0] ) . ";" ); ?><?php } ?>>
		<?php woocommerce_breadcrumb(); ?>
		<h1 class="c-page-header__title"><?php woocommerce_page_title(); ?></h1>
	</header>
<?php endif; ?>

<?php do_action( 'woocommerce_after_page_title' ); ?>

<div
	class="l-section l-section--container l-section--top-margin<?php if ( $with_sidebar ) { ?> l-section--with-sidebar<?php } ?>">
	<?php if ( $with_sidebar || $with_filter ) { ?>
		<div class="l-section__sidebar">
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


	<div
		class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
		<div
			class="<?php ideapark_class( $with_sidebar && ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar-nearby' ); ?>">
			<?php if ( $subcategories ) { ?>
				<div class="c-sub-categories">
					<ul class="c-sub-categories__list"><?php echo ideapark_wrap( $subcategories ); ?></ul>
				</div>
			<?php } ?>

			<?php get_template_part( 'templates/product-archive-ordering' ); ?>

			<?php

			if ( function_exists( 'woocommerce_product_loop' ) ? woocommerce_product_loop() : have_posts() ) {

				/**
				 * Hook: woocommerce_before_shop_loop.
				 *
				 * @hooked wc_print_notices - 10
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );

				?>
				<div class="c-product-grid"><?php
					woocommerce_product_loop_start();
					if ( ! function_exists( 'wc_get_loop_prop' ) || wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 *
							 * @hooked WC_Structured_Data::generate_product_data() - 10
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();
					?>
				</div>
				<?php
				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			} else {
				/**
				 * Hook: woocommerce_no_products_found.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action( 'woocommerce_no_products_found' );
			}
			?>
		</div>
	</div>
</div>

<div class="l-section l-section--container entry-content">
	<?php
	/**
	 * woocommerce_archive_description hook.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<?php get_footer( 'shop' ); ?>

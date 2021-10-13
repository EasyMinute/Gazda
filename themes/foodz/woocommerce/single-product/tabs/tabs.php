<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', [] );

if ( ! empty( $product_tabs ) ) : ?>

	<div class="l-section l-section--container c-product__tabs woocommerce-tabs wc-tabs-wrapper">
		<div class="c-product__tabs-wrap">
			<ul class="c-product__tabs-list tabs wc-tabs" role="tablist">
				<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
					<li class="c-product__tabs-item <?php echo esc_attr( $key ); ?>_tab"
						id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab"
						aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<a class="c-product__tabs-item-link"
						   href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', preg_replace( '~\((\d+)\)~', '<sup class="c-product__rev-counter">\\1</sup>', $product_tab['title'] ), $key ) ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php $is_first = true; ?>
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<div
				class="c-product__tabs-panel woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel <?php if ( $key == 'description' ) { ?>entry-content<?php } ?> wc-tab <?php if ( $is_first ) { ?> current visible<?php $is_first = false;
				} ?>" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
	</div>

<?php endif; ?>

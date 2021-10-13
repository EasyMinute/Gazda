<?php
/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/sidebar.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php if ( is_product() ? is_active_sidebar( 'product-sidebar' ) : ( is_active_sidebar( 'shop-sidebar' ) || is_active_sidebar( 'filter-sidebar' ) ) ) { ?>
	<?php if ( ! is_product() ) { ?>
		<div
			class="c-sidebar c-shop-sidebar js-shop-sidebar <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar' ); ?>">
			<div class="c-shop-sidebar__shadow"></div>
			<div class="c-shop-sidebar__wrap">
				<div class="c-shop-sidebar__buttons">
					<button type="button" class="h-cb h-cb--svg c-shop-sidebar__close"
							id="ideapark-shop-sidebar-close"><?php echo ideapark_svg( 'close', 'c-shop-sidebar__close-svg' ); ?></button>
				</div>
				<?php if ( ideapark_mod( 'shop_sidebar' ) ) { ?>
					<div class="c-shop-sidebar__content c-shop-sidebar__content--desktop">
						<?php dynamic_sidebar( 'shop-sidebar' ); ?>
					</div>
				<?php } ?>
				<div class="c-shop-sidebar__content c-shop-sidebar__content--mobile">
					<?php dynamic_sidebar( 'filter-sidebar' ); ?>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="c-sidebar c-product__sidebar-wrap">
			<?php dynamic_sidebar( 'product-sidebar' ); ?>
		</div>
	<?php } ?>
<?php } ?>
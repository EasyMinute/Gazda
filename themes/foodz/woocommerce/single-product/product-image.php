<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @package       WooCommerce/Templates
 * @version       3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}
global $post, $product;
$video_url = get_post_meta( $post->ID, '_ip_product_video_url', true );
$index = 0;
?>

	<div class="c-badge__list c-product__badges">
		<?php woocommerce_show_product_sale_flash(); ?>
		<?php ideapark_woocommerce_show_product_loop_badges(); ?>
	</div>

<?php $images = ideapark_product_images(); ?>

	<div class="c-product__gallery images js-single-product-carousel">
		<?php if ( $images ) {
			foreach ( $images as $i => $image ) {
				if ( ideapark_mod( 'shop_product_modal' ) ) {
					$image_wrap_open  = '';
					$image_wrap_close = sprintf( '<a href="%s" class="c-product__image-link %s" data-size="%sx%s" data-index="%s" data-product-id="%s" onclick="return false;">', esc_url( $image['full'][0] ), ideapark_mod( 'shop_product_modal' ) ? ' c-product__image-link--zoom js-product-zoom' : '', intval( $image['full'][1] ), intval( $image['full'][2] ), $index++, $post->ID ) . '</a>';
				} else {
					$image_wrap_open  = '';
					$image_wrap_close = '';
				}

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="c-product__gallery-item woocommerce-product-gallery__image ">%s%s%s</div>', $image_wrap_open, $image['image'], $image_wrap_close ), $post->ID );
			}
		} else {
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="c-product__gallery-item"><img src="%s" alt="%s" /></div>', wc_placeholder_img_src('woocommerce_single'), esc_attr__( 'Placeholder', 'woocommerce' ) ), $post->ID );
		} ?>
	</div>

<?php if ( sizeof( $images ) > 1 || $video_url ) { ?>

	<div class="c-product__thumbs js-product-thumbs-carousel">
		<?php if ( sizeof( $images ) > 1 ) { ?>
			<?php foreach ( $images as $ii => $image ) { ?>
				<?php echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="c-product__thumbs-item"><button type="button" class="h-cb js-single-product-thumb" data-index="' . $ii . '">%s</button></div>', $image['thumb'] ), $image['attachment_id'], $post->ID ); ?>
			<?php } ?>
		<?php } ?>

		<?php if ( $video_url ) { ?>
			<div class="c-product__thumbs-item c-product__thumbs-item--video">
				<a href="<?php echo esc_url( $video_url ); ?>" class="js-product-zoom js-product-zoom-video" data-index="<?php echo esc_attr( $index ); ?>" data-product-id="<?php echo esc_attr( $post->ID ); ?>">
					<span class="c-product__video-spacer"></span>
					<span class="c-product__video-wrap">
						<span class="c-product__video-svg-wrap"><?php echo ideapark_svg( 'play', 'c-product__video-svg' ) ?></span>
						<span class="c-product__video-title"><?php _e( 'Video', 'foodz' ); ?></span>
					</span>
				</a>
			</div>
		<?php } ?>
	</div>
<?php } ?>
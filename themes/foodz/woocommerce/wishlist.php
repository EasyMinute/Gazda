<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

ideapark_mod_set_temp('product_variations_in_grid', false);

if ( $ip_wishlist_ids = ideapark_wishlist()->ids() ) {
	$share_link_url = esc_url( get_permalink() . ( strpos( get_permalink(), '?' ) === false ? '?' : '&' ) . 'ip_wishlist_share=' . implode( ',', $ip_wishlist_ids ) );
	$share_links = [
		'<a href="//www.facebook.com/sharer.php?u=' . $share_link_url . '" target="_blank" title="' . esc_html__( 'Share on Facebook', 'foodz' ) . '">' . ideapark_svg( 'facebook', 'c-wishlist__share-svg c-wishlist__share-svg--facebook' ). '</a>',
		'<a href="//twitter.com/share?url=' . $share_link_url . '" target="_blank" title="' . esc_html__( 'Share on Twitter', 'foodz' ) . '">' . ideapark_svg( 'twitter', 'c-wishlist__share-svg c-wishlist__share-svg--twitter' ). '</a>',
		'<a href="//pinterest.com/pin/create/button/?url=' . $share_link_url . '" target="_blank" title="' . esc_html__( 'Pin on Pinterest', 'foodz' ) . '">' . ideapark_svg( 'pinterest', 'c-wishlist__share-svg c-wishlist__share-svg--pinterest' ). '</a>',
		'<a href="whatsapp://send?text=' . $share_link_url . '" target="_blank" title="' . esc_html__( 'Share on Whatsapp', 'foodz' ) . '">' . ideapark_svg( 'whatsapp', 'c-wishlist__share-svg c-wishlist__share-svg--whatsapp' ) . '</a>',
	];

	$args = [
		'post_type'      => 'product',
		'order'          => 'DESC',
		'orderby'        => 'post__in',
		'posts_per_page' => - 1,
		'post__in'       => $ip_wishlist_ids
	];

	$wishlist_loop = new WP_Query( $args );
} else {
	$wishlist_loop = false;
}

if ( $wishlist_loop && $wishlist_loop->have_posts() ) { ?>

	<div class="c-wishlist" id="ip-wishlist">
		<table class="c-wishlist__table" id="ip-wishlist-table">
			<thead class="c-wishlist__thead">
			<tr class="c-wishlist__tr">
				<th class="c-wishlist__th c-wishlist__th--product-name" colspan="2">
					<span><?php esc_html_e( 'Product', 'foodz' ); ?></span>
				</th>
				<th class="c-wishlist__th c-wishlist__th--product-price">
					<span><?php esc_html_e( 'Price', 'foodz' ); ?></span>
				</th>
				<th class="c-wishlist__th c-wishlist__th--stock">
					<span><?php esc_html_e( 'Stock Status', 'foodz' ); ?></span>
				</th>
				<th class="c-wishlist__th c-wishlist__th--action">
				</th>
			</tr>
			</thead>
			<tbody class="c-wishlist__tbody">
			<?php
			while ( $wishlist_loop->have_posts() ) : $wishlist_loop->the_post();

				global $product;
				?>
				<tr class="product c-wishlist__tr" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<td class="c-wishlist__td c-wishlist__td--product-thumbnail">
						<?php if ( ! ideapark_wishlist()->view_mode() ) { ?>
							<button class="h-cb h-cb--svg ip-wishlist-remove c-wishlist__remove" title="<?php esc_html_e( 'Remove', 'foodz' ); ?>" type="button">
								<?php echo ideapark_svg( 'close' ); ?>
							</button>
						<?php } ?>
						<a href="<?php the_permalink(); ?>"><?php echo ideapark_wrap( $product->get_image( 'thumbnail', [ 'class' => 'c-wishlist__thumb' ] ) ); ?></a>
					</td>
					<td class="c-wishlist__td c-wishlist__td--product-name">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</td>
					<td class="c-wishlist__td c-wishlist__td--product-price">
						<?php
						woocommerce_template_loop_price();
						?>
					</td>
					<td class="ip-product-stock-status c-wishlist__td c-wishlist__td--stock">
						<?php
						ideapark_product_availability();
						?>
					</td>
					<td class="c-wishlist__td c-wishlist__td--actions">
						<div class="c-wishlist__button-wrap">
							<?php woocommerce_template_loop_add_to_cart(); ?>
							<div class="c-quantity js-product-grid-quantity"></div>
							<span class="added_to_cart h-hidden"></span>
						</div>
					</td>
				</tr>
			<?php endwhile; ?>
			</tbody>
		</table>

		<?php if ( ideapark_mod( 'wishlist_share' ) && ! ideapark_wishlist()->view_mode() ) { ?>
			<div class="c-wishlist__share">
				<div class="c-wishlist__share-col-1">
					<span><?php esc_html_e( 'Share Wishlist:', 'foodz' ); ?></span>
					<?php
					foreach ( $share_links as $link ) {
						echo ideapark_wrap( $link );
					}
					?>
				</div>
				<div class="c-wishlist__share-col-2">
					<span><?php esc_html_e( 'Share Link:', 'foodz' ); ?></span>
					<input class="c-wishlist__share-link" type="text" id="ip-wishlist-share-link" value="<?php echo esc_attr( $share_link_url ); ?>" />
				</div>
			</div>
		<?php } ?>
	</div>

<?php } ?>

	<div class="c-wishlist-empty <?php if ( $wishlist_loop && $wishlist_loop->have_posts() ) { ?> h-hidden<?php } ?>" id="ip-wishlist-empty">
		<div class="c-wishlist-empty__image-wrap">
			<?php if ( ideapark_mod( 'wishlist_empty_image' ) ) { ?>
				<img src="<?php echo stripslashes( ideapark_mod( 'wishlist_empty_image' ) ); ?>" alt="<?php esc_html_e( 'The Wishlist is currently empty', 'foodz' ); ?>" class="c-wishlist-empty__image" />
			<?php } else { ?>
				<?php echo ideapark_svg( 'wishlist-empty', 'c-wishlist-empty__svg' ); ?>
			<?php } ?>
		</div>
		<h1 class="c-wishlist-empty__header"><?php esc_html_e( 'The Wishlist is currently empty', 'foodz' ); ?></h1>
		<p class="c-wishlist-empty__note"><?php printf( esc_html__( 'Click the %s icons to add products', 'foodz' ), ideapark_svg( 'wishlist', 'c-wishlist-empty__icon' ) ); ?></p>
		<?php if ( wc_get_page_id( 'shop' ) > 0 ) { ?>
			<a class="c-form__button c-wishlist-empty__backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Return to shop', 'woocommerce' ) ?>
			</a>
		<?php } ?>
	</div>
<?php wp_reset_postdata(); ?>
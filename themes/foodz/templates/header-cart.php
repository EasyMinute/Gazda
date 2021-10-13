<?php if ( ideapark_woocommerce_on() ) { ?>
	<div class="c-header__cart js-cart">
		<a class="c-header__button-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<?php echo ideapark_svg( 'cart', 'c-header__cart-svg' ); ?><?php echo ideapark_cart_info(); ?>
		</a>
		<div class="widget_shopping_cart_content"></div>
	</div>
<?php } ?>
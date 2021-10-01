<?php if ( ideapark_woocommerce_on() && ideapark_mod( 'wishlist_page' ) && ideapark_mod( 'wishlist_enabled' ) ) { ?>
	<div
		class="c-header__wishlist<?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-1' ) { ?> c-header__wishlist--bottom<?php } ?>">
		<a class="c-header__button-link c-header__button-link--wishlist"
		   href="<?php echo esc_url( get_permalink( ideapark_mod( 'wishlist_page' ) ) ); ?>"><?php echo ideapark_svg( 'wishlist', 'c-header__wishlist-svg' ) ?><?php echo ideapark_wishlist_info(); ?></a>
	</div>
<?php } ?>
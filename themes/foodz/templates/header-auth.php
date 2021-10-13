<?php if ( ideapark_woocommerce_on() && ideapark_mod( 'auth_enabled' ) ) { ?>
	<div class="c-header__auth<?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-1' ) { ?> c-header__auth--bottom<?php } ?>"><?php echo ideapark_get_account_link(); ?></div>
<?php } ?>
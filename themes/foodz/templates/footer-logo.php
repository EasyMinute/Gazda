<div class="c-footer__logo">
	<?php if ( !is_front_page() ): ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php endif ?>
		<?php if ( ideapark_mod( 'logo_footer' ) ) { ?>
			<img src="<?php echo stripslashes( ideapark_mod( 'logo_footer' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="c-footer__logo-img" />
		<?php } elseif ( ideapark_mod( 'logo' ) ) { ?>
			<img src="<?php echo stripslashes( ideapark_mod( 'logo' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="c-footer__logo-img" />
		<?php } else { ?>
			<?php echo ideapark_svg( 'logo', 'c-footer__logo-img' ); ?>
		<?php } ?>

		<?php if ( !is_front_page() ): ?></a><?php endif ?>
</div>
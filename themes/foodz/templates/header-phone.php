<?php if ( ideapark_mod( 'header_phone' ) || ideapark_mod( 'header_callback' ) ) { ?>
	<div class="c-header__phone-block">
		<?php if ( ideapark_mod( 'header_phone' ) ) { ?>
			<div class="c-header__phone">
				<?php echo ideapark_svg( 'phone', 'c-header__phone-svg' ) ?><?php echo esc_html( ideapark_mod( 'header_phone' ) ); ?></div>
		<?php } ?>
		<?php if ( ideapark_mod( 'header_callback' ) ) { ?>
			<button type="button" class="h-cb js-callback c-header__callback<?php if ( ideapark_mod( 'header_phone' ) ) { ?> c-header__callback--second<?php } ?>"><?php echo esc_html( ideapark_mod( 'header_callback' ) ); ?></button>
		<?php } ?>
	</div>
<?php } ?>

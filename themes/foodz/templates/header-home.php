<div class="c-header__home<?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-1' ) { ?> c-header__home--bottom<?php } ?>">
	<?php if ( ! is_front_page() ): ?>
	<a class="c-header__button-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php endif ?>
		<?php echo ideapark_svg( 'home', 'c-header__home-svg' ); ?>
		<?php if ( ! is_front_page() ): ?>
	</a>
<?php endif ?>
</div>

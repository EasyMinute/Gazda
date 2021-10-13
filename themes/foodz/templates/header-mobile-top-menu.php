<?php if ( class_exists( 'Ideapark_Megamenu_Walker' ) ) {
	echo str_replace( '<nav', '<nav itemscope itemtype="http://schema.org/SiteNavigationElement"', wp_nav_menu( [
		'container'       => 'nav',
		'container_class' => 'c-mega-menu c-mega-menu--top-menu js-mobile-top-menu',
		'echo'            => false,
		'menu_id'         => 'mobile-top-menu',
		'menu_class'      => 'c-mega-menu__list',
		'theme_location'  => 'primary',
		'walker'          => new Ideapark_Megamenu_Walker(),
		'depth'           => ideapark_mod( 'top_menu_third' ) ? 3 : 2,
		'with_icon'       => false,
		'title_class'     => 'c-mega-menu__title--text-only'
	] ) );
} ?>

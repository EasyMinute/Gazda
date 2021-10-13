<?php if ( class_exists( 'Ideapark_Megamenu_Walker' ) ) {
	wp_reset_query();
	echo str_replace( '<nav', '<nav itemscope itemtype="http://schema.org/SiteNavigationElement"', wp_nav_menu( [
		'container'        => 'nav',
		'container_class'  => 'c-mega-menu c-mega-menu--preload js-mega-menu',
		'echo'             => false,
		'menu_id'          => 'mega-menu' . ( ! empty( $ideapark_var['device'] ) ? '-' . $ideapark_var['device'] : '' ),
		'menu_class'       => 'c-mega-menu__list',
		'theme_location'   => 'megamenu',
		'fallback_cb'      => '',
		'walker'           => new Ideapark_Megamenu_Walker(),
		'depth'            => ideapark_mod( 'main_menu_third' ) !== 'hide' ? 3 : 2,
		'with_icon'        => ideapark_mod( 'main_menu_view' ) == 'icons',
		'label_place'      => ideapark_mod( 'header_type' ) == 'header-type-1' && ( empty( $ideapark_var['device'] ) || $ideapark_var['device'] != 'mobile' ) ? 'icon' : 'text',
		'label_center'     => ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'main_menu_view' ) != 'icons',
		'empty_icon'       => ideapark_mod( 'header_type' ) == 'header-type-1',
		'title_class'      => ideapark_mod( 'main_menu_view' ) != 'icons' ? 'c-mega-menu__title--text-only' : ( ideapark_mod( 'header_type' ) == 'header-type-1' ? 'c-mega-menu__title--vert' : '' ),
		'title_wrap_class' => (ideapark_mod( 'main_menu_view' ) == 'icons' && ideapark_mod( 'header_type' ) == 'header-type-1' ? 'c-mega-menu__title-wrap--vert' : '') . ' c-mega-menu__title-wrap--' . ideapark_mod( 'header_type' ),
		'submenu_class'    => ideapark_mod( 'main_menu_third' ) == 'submenu' ? 'c-mega-menu__submenu--inner' : ( ideapark_mod( 'main_menu_third' ) == 'popup' ? 'c-mega-menu__submenu--popup' : '' ),
	] ) );
} ?>
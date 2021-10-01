<?php

$ideapark_customize_custom_css = [];
$ideapark_customize            = [];
$ideapark_customize_mods       = [];
$ideapark_customize_panels     = [];
$ideapark_customize_mods_def   = [];

if ( ! function_exists( 'ideapark_init_theme_customize' ) ) {
	function ideapark_init_theme_customize() {
		global $ideapark_customize, $ideapark_customize_panels;

		$ideapark_customize_panels = [
			'front_page_builder'       => [
				'priority'    => 85,
				'title'       => __( 'Front Page Builder', 'foodz' ),
				'description' => '',
			],
			'header_and_menu_settings' => [
				'priority'    => 90,
				'title'       => __( 'Header & Menu Settings', 'foodz' ),
				'description' => '',
			]
		];

		$version = md5( ideapark_mtime( __FILE__ ) . '-' . IDEAPARK_THEME_VERSION );
		if ( ( $languages = apply_filters( 'wpml_active_languages', [] ) ) && sizeof( $languages ) >= 2 ) {
			$version .= '_' . implode( '_', array_keys( $languages ) );
		}

		if ( ( $data = get_option( 'ideapark_customize' ) ) && ! empty( $data['version'] ) && ! empty( $data['settings'] ) ) {
			if ( $data['version'] == $version ) {
				$ideapark_customize = $data['settings'];

				return;
			} else {
				delete_option( 'ideapark_customize' );
			}
		}

		$ideapark_customize = [
			[
				'section'  => 'title_tagline',
				'controls' => [
					'logo'        => [
						'label'             => __( 'Logo', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'priority'          => 50,
						'refresh'           => '.c-header__logo',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-logo',
						'refresh_callback'  => 'ideapark_set_header_bg_height_force',
					],
					'logo_mobile' => [
						'label'             => __( 'Mobile Logo', 'foodz' ),
						'description'       => __( 'Leave empty for using main Logo', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'priority'          => 51,
						'refresh'           => '.c-header__logo',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-logo',
						'dependency'        => [
							'logo' => [ 'not_empty' ],
						],
					],
					'logo_size'   => [
						'label'             => __( 'Logo size', 'foodz' ),
						'default'           => 90,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 70,
						'max'               => 200,
						'step'              => 1,
						'priority'          => 51,
						'refresh_css'       => '.c-header__logo',
						'refresh'           => false,
						'refresh_callback'  => 'ideapark_set_header_bg_height_force',
					],
				],
			],
			[
				'section'  => 'background_image',
				'controls' => [
					'hide_inner_background' => [
						'label'             => __( 'Hide background on inner pages', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

				],
			],
			[
				'title'    => __( 'General Theme Settings', 'foodz' ),
				'priority' => 5,
				'controls' => [

					'ajax_search'          => [
						'label'             => __( 'Ajax search', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'to_top_button'        => [
						'label'             => __( 'To Top Button Enable', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'to_top_button_color'  => [
						'label'             => __( 'To Top Button color', 'foodz' ),
						'description'       => __( 'Default color if empty', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#D1333C',
						'dependency'        => [
							'to_top_button' => [ '1' ],
						],
					],
					'disable_block_editor'                => [
						'label'             => __( 'Disable widget block editor', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'sticky_sidebar'       => [
						'label'             => __( 'Sticky sidebar and checkout summary', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'404_image'            => [
						'label'             => __( 'Custom image for 404 page', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'cart_empty_image'     => [
						'label'             => __( 'Custom image for empty cart', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'wishlist_empty_image' => [
						'label'             => __( 'Custom image for empty wishlist', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			],
			[
				'panel'    => 'header_and_menu_settings',
				'title'    => __( 'Header', 'foodz' ),
				'controls' => [
					'header_desktop_settings_info'    => [
						'label'             => __( 'Desktop Header Settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'header_type'                     => [
						'label'             => __( 'Desktop header type', 'foodz' ),
						'default'           => 'header-type-1',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'choices'           => [
							'header-type-1' => IDEAPARK_THEME_URI . '/assets/img/header-1.png',
							'header-type-2' => IDEAPARK_THEME_URI . '/assets/img/header-2.png',
							'header-type-3' => IDEAPARK_THEME_URI . '/assets/img/header-3.png',
							'header-type-4' => IDEAPARK_THEME_URI . '/assets/img/header-4.png',
						],
					],
					'header_row_1_color'              => [
						'label'             => __( 'First header row text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-1'
					],
					'header_row_1_background_color'   => [
						'label'             => __( 'First header row background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#000000',
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-1'
					],
					'header_row_1_background_opacity' => [
						'label'             => __( 'First header row background opacity', 'foodz' ),
						'default'           => 0.05,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 0,
						'max'               => 1,
						'step'              => 0.01,
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-1'
					],
					'header_row_1_border_color'       => [
						'label'             => __( 'First header row bottom border color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh_css'       => '.c-top-menu',
						'refresh'           => false,
					],
					'header_row_2_color'              => [
						'label'             => __( 'Second header row text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-2'
					],
					'header_row_2_background_color'   => [
						'label'             => __( 'Second header row background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-2'
					],
					'header_row_2_background_opacity' => [
						'label'             => __( 'Second header row background opacity', 'foodz' ),
						'default'           => 1,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 0,
						'max'               => 1,
						'step'              => 0.01,
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-2'
					],
					'header_row_3_color'              => [
						'label'             => __( 'Third header row text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-3',
						'dependency'        => [
							'header_type' => [ 'header-type-1', 'header-type-2' ],
						],
					],
					'header_row_3_background_color'   => [
						'label'             => __( 'Third header row background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-3',
						'dependency'        => [
							'header_type' => [ 'header-type-1', 'header-type-2' ],
						],
					],
					'header_row_3_background_opacity' => [
						'label'             => __( 'Third header row background opacity', 'foodz' ),
						'default'           => 1,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 0,
						'max'               => 1,
						'step'              => 0.01,
						'refresh'           => false,
						'refresh_css'       => '.c-header__row-3',
						'dependency'        => [
							'header_type' => [ 'header-type-1', 'header-type-2' ],
						],
					],
					'header_image'                    => [
						'label'             => __( 'Background image', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => true,
					],
					'header_image_height'             => [
						'label'             => __( 'Background image height', 'foodz' ),
						'default'           => '1',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'choices'           => [
							'1'  => IDEAPARK_THEME_URI . '/assets/img/row-1.png',
							'2'  => IDEAPARK_THEME_URI . '/assets/img/row-2.png',
							'3'  => IDEAPARK_THEME_URI . '/assets/img/row-3.png',
							'3+' => IDEAPARK_THEME_URI . '/assets/img/row-4.png',
						],
						'refresh'           => true,
					],
					'header_image_size'               => [
						'label'             => __( 'Background image size', 'foodz' ),
						'type'              => 'select',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => 'auto',
						'choices'           => [
							'auto'  => __( 'Original + repeat', 'foodz' ),
							'cover' => __( 'Fill area (cover)', 'foodz' ),
						],
						'refresh'           => true,
					],

					'header_mobile_settings_info'           => [
						'label'             => __( 'Mobile Header Settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'mobile_layout'                         => [
						'label'             => __( 'Mobile layout', 'foodz' ),
						'default'           => 'layout-type-1',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'choices'           => [
							'layout-type-1' => IDEAPARK_THEME_URI . '/assets/img/mobile-menu-1.png',
							'layout-type-2' => IDEAPARK_THEME_URI . '/assets/img/mobile-menu-2.png',
						],
					],
					'mobile_header_top_color'               => [
						'label'             => __( 'Mobile header top row text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile'
					],
					'mobile_header_top_background_color'    => [
						'label'             => __( 'Mobile header top row background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#D1333C',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile'
					],
					'mobile_header_bottom_color'            => [
						'label'             => __( 'Mobile header bottom row text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile',
						'dependency'        => [
							'mobile_layout' => [ 'layout-type-1' ],
						],
					],
					'mobile_header_bottom_background_color' => [
						'label'             => __( 'Mobile header bottom row background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile',
						'dependency'        => [
							'mobile_layout' => [ 'layout-type-1' ],
						],
					],

					'header_other_settings_info' => [
						'label'             => __( 'Other Header Settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'auth_enabled' => [
						'label'             => __( 'Show Auth button', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-header__auth',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-auth',
					],

					'search_enabled' => [
						'label'             => __( 'Show Search button', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-header__search-button',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-search-button',
					],

					'wishlist_enabled' => [
						'label'             => __( 'Show Wishlist button', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-header__wishlist',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-wishlist',
					],

					'wishlist_is_disabled' => [
						'label'             => wp_kses_post( __( 'Wishlist button is not shown because Wishlist Page is not set ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="wishlist_page">' . __( 'here', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'wishlist_page' => [ 0, '' ],
						],
					],

					'header_phone'                         => [
						'label'             => __( 'Phone number', 'foodz' ),
						'description'       => __( 'At the top of the header and inside mobile menu panel', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__phone-block',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-phone',
					],
					'header_callback'                      => [
						'label'             => __( '"Call me back" button text', 'foodz' ),
						'description'       => __( 'Disabled if empty', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__phone-block',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-phone',
					],
					'header_callback_title'                => [
						'label'             => __( '"Call me back" popup window header', 'foodz' ),
						'description'       => __( 'Disabled if empty', 'foodz' ),
						'type'              => 'text',
						'default'           => 'Call me back',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__phone-block',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-phone',
						'dependency'        => [
							'header_callback' => [ 'not_empty' ]
						],
					],
					'header_callback_shortcode'            => [
						'label'             => __( '"Call me back" form shortcode', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-header__phone-block',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-phone',
						'dependency'        => [
							'header_callback' => [ 'not_empty' ]
						],
					],
					'header_menu_text'                     => [
						'label'             => __( 'Text at the top of the header', 'foodz' ),
						'description'       => __( 'You can put working time, or other information here', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'refresh'           => '.c-header__text',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-text',
						'class'             => 'WP_Customize_Text_Editor_Control',
					],
					'mobile_cart_counter_color'            => [
						'label'             => __( 'Mobile cart counter text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile'
					],
					'mobile_cart_counter_background_color' => [
						'label'             => __( 'Mobile cart counter background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FE8128',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile'
					],
				],
			],
			[
				'panel'    => 'header_and_menu_settings',
				'title'    => __( 'Mega Menu', 'foodz' ),
				'controls' => [
					'menu_settings_info'    => [
						'label'             => __( 'Main Settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'main_menu_view'        => [
						'label'             => __( 'Mega menu view', 'foodz' ),
						'default'           => 'icons',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'icons'     => __( 'Icons with text', 'foodz' ),
							'text-only' => __( 'Only text', 'foodz' ),
						],
						'refresh'           => '.js-header-desktop .js-mega-menu',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-mega-menu',
						'refresh_callback'  => 'ideapark_mega_menu_init'
					],
					'main_menu_label_color' => [
						'label'             => __( 'Mega menu badge color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#74C557',
						'refresh'           => false,
						'refresh_css'       => '.js-header-desktop .js-mega-menu',
					],


					'menu_desktop_settings_info' => [
						'label'             => __( 'Desktop Mega Menu', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'main_menu_third'            => [
						'label'             => __( 'Third level in the mega menu', 'foodz' ),
						'default'           => 'submenu',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'submenu' => __( 'Show as subitems', 'foodz' ),
							'popup'   => __( 'Show in popup submenu', 'foodz' ),
							'hide'    => __( 'Hide', 'foodz' ),
						],
						'refresh'           => '.js-header-desktop .js-mega-menu',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-mega-menu',
						'refresh_callback'  => 'ideapark_mega_menu_init'
					],
					'main_menu_width'            => [
						'label'             => __( 'Menu item max width', 'foodz' ),
						'default'           => 130,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 100,
						'max'               => 300,
						'step'              => 1,
						'refresh'           => false,
						'refresh_css'       => '.js-header-desktop .js-mega-menu',
					],
					'main_menu_space'            => [
						'label'             => __( 'Space between menu items', 'foodz' ),
						'default'           => 25,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 15,
						'max'               => 50,
						'step'              => 1,
						'refresh'           => false,
						'refresh_css'       => '.js-header-desktop .js-mega-menu',
					],
					'main_menu_font_size'        => [
						'label'             => __( 'Menu item font-size', 'foodz' ),
						'default'           => 19,
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 15,
						'max'               => 25,
						'step'              => 1,
						'refresh'           => false,
						'refresh_css'       => '.js-header-desktop .js-mega-menu',
					],
					'mega_menu_submenu_color'    => [
						'label'             => __( 'Mega menu popup text color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
					],
					'mega_menu_submenu_bg_color' => [
						'label'             => __( 'Mega menu popup background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#D1333C',
					],

					'menu_mobile_settings_info'    => [
						'label'             => __( 'Mobile Mega Menu', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'mobile_menu_color'            => [
						'label'             => __( 'Mobile menu text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile'
					],
					'mobile_menu_background_color' => [
						'label'             => __( 'Mobile menu background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-header--mobile'
					],
					'mobile_menu_title_click_expand' => [
						'label'             => __( 'Show child items when the title is clicked', 'foodz' ),
						'description'       => __( 'If the checkbox is unchecked the child elements will be displayed only when the arrow is clicked', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				]
			],
			[
				'panel'    => 'header_and_menu_settings',
				'title'    => __( 'Desktop Top Menu', 'foodz' ),
				'controls' => [
					'top_menu_third'            => [
						'label'             => __( 'Show third level in the top menu', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => '.c-top-menu',
						'refresh_wrapper'   => true,
						'refresh_id'        => 'header-top-menu',
					],
					'top_menu_submenu_color'    => [
						'label'             => __( 'Top menu popup text color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh_css'       => '.c-top-menu',
						'refresh'           => false,
					],
					'top_menu_submenu_bg_color' => [
						'label'             => __( 'Top menu popup background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh_css'       => '.c-top-menu',
						'refresh'           => false,
					],
				]
			],
			[
				'panel'    => 'header_and_menu_settings',
				'title'    => __( 'Sticky Menu', 'foodz' ),
				'controls' => [
					'sticky_menu_desktop' => [
						'label'             => __( 'Sticky menu desktop', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'sticky_type'         => [
						'label'             => __( 'Sticky menu desktop view', 'foodz' ),
						'default'           => 'sticky-type-1',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'sticky-type-1' => __( 'Icons only', 'foodz' ),
							'sticky-type-2' => __( 'Text only', 'foodz' ),
						],
						'dependency'        => [
							'main_menu_view'      => [ 'icons' ],
							'sticky_menu_desktop' => [ 1 ],
						],
					],
					'sticky_menu_mobile'  => [
						'label'             => __( 'Sticky menu mobile', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				]
			],
			[
				'title'    => __( 'Footer', 'foodz' ),
				'priority' => 105,
				'controls' => [
					'footer_minimal'          => [
						'label'             => __( 'Minimal footer', 'foodz' ),
						'description'       => __( 'Without widgets', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => true,
					],
					'logo_footer'             => [
						'label'             => __( 'Footer Logo (optional)', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-footer__logo',
						'refresh_id'        => 'footer-logo',
						'refresh_wrapper'   => true,
					],
					'logo_footer_size'        => [
						'label'             => __( 'Logo size', 'foodz' ),
						'type'              => 'slider',
						'sanitize_callback' => 'sanitize_text_field',
						'class'             => 'WP_Customize_Range_Control',
						'min'               => 70,
						'max'               => 200,
						'step'              => 1,
						'default'           => 90,
						'refresh_css'       => '.c-footer__logo',
						'refresh'           => false,
					],
					'footer_phone'            => [
						'label'             => __( 'Phone', 'foodz' ),
						'type'              => 'textarea',
						'default'           => '',
						'sanitize_callback' => 'sanitize_textarea_field',
						'refresh'           => '.c-footer__phone',
						'refresh_id'        => 'footer-phone',
						'refresh_wrapper'   => true,
					],
					'footer_contacts'         => [
						'label'             => __( 'Contacts', 'foodz' ),
						'description'       => __( 'Only in Widgets Footer Design', 'foodz' ),
						'type'              => 'text_editor',
						'class'             => 'WP_Customize_Text_Editor_Control',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'refresh'           => '.c-footer__contacts',
						'refresh_id'        => 'footer-contacts',
						'refresh_wrapper'   => true,
					],
					'footer_copyright'        => [
						'label'             => __( 'Copyright', 'foodz' ),
						'type'              => 'text',
						'default'           => '&copy; Copyright 2019, Foodz WordPress Theme',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => '.c-footer__copyright',
						'refresh_id'        => 'footer-copyright',
						'refresh_wrapper'   => true,
					],
					'footer_text_color'       => [
						'label'             => __( 'Text color', 'foodz' ),
						'description'       => __( 'Default color if empty', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c_footer'
					],
					'footer_header_color'     => [
						'label'             => __( 'Header color', 'foodz' ),
						'description'       => __( 'Default color if empty', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c_footer'
					],
					'footer_background_color' => [
						'label'             => __( 'Background color', 'foodz' ),
						'description'       => __( 'Default color if empty', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c_footer'
					],
					'footer_image'            => [
						'label'             => __( 'Background image', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'refresh'           => false,
						'refresh_css'       => '.c_footer'
					],
					'footer_image_size'       => [
						'label'             => __( 'Background image size', 'foodz' ),
						'type'              => 'select',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => 'auto',
						'choices'           => [
							'auto'  => __( 'Original + repeat', 'foodz' ),
							'cover' => __( 'Fill area (cover)', 'foodz' ),
						],
						'refresh'           => false,
						'refresh_css'       => '.c_footer'
					],
				],
			],
			[
				'title'           => __( 'Social Media Links', 'foodz' ),
				'description'     => __( 'Add the full url of your social media page e.g http://twitter.com/yoursite', 'foodz' ),
				'refresh'         => '.c-soc',
				'refresh_wrapper' => true,
				'refresh_id'      => 'soc',
				'priority'        => 130,
				'controls'        => [
					'soc_background_color' => [
						'label'             => __( 'Background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#404E65',
						'refresh'           => false,
						'refresh_css'       => '.c-soc',
					],
					'soc_color'            => [
						'label'             => __( 'Icon color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'refresh'           => false,
						'refresh_css'       => '.c-soc',
					],
					'facebook'             => [
						'label'             => __( 'Facebook url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'instagram'            => [
						'label'             => __( 'Instagram url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'vk'                   => [
						'label'             => __( 'VK url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'ok'                   => [
						'label'             => __( 'OK url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'telegram'             => [
						'label'             => __( 'Telegram url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'twitter'              => [
						'label'             => __( 'Twitter url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'whatsapp'             => [
						'label'             => __( 'Whatsapp url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'youtube'              => [
						'label'             => __( 'YouTube url', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'vimeo'                => [
						'label'             => __( 'Vimeo url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'linkedin'             => [
						'label'             => __( 'LinkedIn url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'flickr'               => [
						'label'             => __( 'Flickr url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'pinterest'            => [
						'label'             => __( 'Pinterest url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'tumblr'               => [
						'label'             => __( 'Tumblr url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'dribbble'             => [
						'label'             => __( 'Dribbble url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'github'               => [
						'label'             => __( 'Github url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'custom_soc_info' => [
						'label'             => __( 'Custom Social Icon', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'custom_soc_icon' => [
						'label'             => __( 'Icon', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'custom_soc_url'  => [
						'label'             => __( 'Url', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
				]
			],
			[
				'panel' => 'front_page_builder',
				'title' => __( 'General settings', 'foodz' ),

				'controls' => [

					'front_page_builder_enabled' => [
						'label'             => __( 'Enable Front Page builder', 'foodz' ),
						'description'       => __( 'If Front Page Builder is off - native page content will be shown', 'foodz' ),
						'default'           => true,
						'refresh'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'home_sections' => [
						'label'              => __( 'Sections order', 'foodz' ),
						'description'        => __( 'Drag and drop sections below to set up their order on the Front Page. You can also enable or disable any section.', 'foodz' ),
						'type'               => 'checklist',
						'default'            => 'slider=1|banners=1|product-tabs=1|product-promo=1|brands=1|posts=1|testimonials=1|icons=1|text=1|subscribe=0|html=0|shortcode=0',
						'choices'            => [
							'slider'        => __( 'Slider', 'foodz' ),
							'banners'       => __( 'Banners block', 'foodz' ),
							'product-tabs'  => __( 'Product tabs', 'foodz' ),
							'product-promo' => __( 'Product promo block', 'foodz' ),
							'brands'        => __( 'Brands', 'foodz' ),
							'posts'         => __( 'Blog posts', 'foodz' ),
							'testimonials'  => __( 'Testimonials', 'foodz' ),
							'icons'         => __( 'Icons block', 'foodz' ),
							'text'          => __( 'Home page content', 'foodz' ),
							'html'          => __( 'HTML block', 'foodz' ),
							'shortcode'     => __( 'Shortcode', 'foodz' ),
							'subscribe'     => __( 'Subscribe', 'foodz' ),
						],
						'choices_edit'       => [
							'slider'        => 'slider_is_disabled',
							'banners'       => 'home_banners_is_disabled',
							'product-tabs'  => 'home_tab_products_is_disabled',
							'product-promo' => 'home_products_promo_is_disabled',
							'brands'        => 'home_brands_is_disabled',
							'posts'         => 'home_post_is_disabled',
							'testimonials'  => 'home_testimonials_is_disabled',
							'icons'         => 'home_icons_is_disabled',
							'text'          => 'home_text_is_disabled',
							'html'          => 'home_html_is_disabled',
							'shortcode'     => 'home_shortcode_is_disabled',
							'subscribe'     => 'home_subscribe_is_disabled',
						],
						'can_add_block'      => [
							'banners',
							'product-tabs',
							'product-promo',
							'html',
							'shortcode',
						],
						'add_ajax_action'    => 'ideapark_customizer_add_section',
						'delete_ajax_action' => 'ideapark_customizer_delete_section',
						'sortable'           => true,
						'class'              => 'WP_Customize_Checklist_Control',
						'sanitize_callback'  => 'sanitize_text_field',
					],
				],
			],
			[
				'section_id'      => 'slider',
				'title'           => __( 'Slider', 'foodz' ),
				'panel'           => 'front_page_builder',
				'refresh'         => '.js-home-slider',
				'refresh_id'      => 'home-slider',
				'refresh_wrapper' => true,
				'controls'        => [

					'slider_is_disabled' => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=slider=1' ],
						],
					],

					'slider_fullwidth' => [
						'label'             => __( 'Fullwidth', 'foodz' ),
						'default'           => true,
						'refresh'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'slider_shortcode' => [
						'label'             => __( 'Third-party slider shortcode', 'foodz' ),
						'description'       => __( 'Enter shortcode, if you want to use a third-party slider instead of the theme slider', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'slider_top_margin' => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'      => 'banners',
				'title'           => __( 'Banners', 'foodz' ),
				'panel'           => 'front_page_builder',
				'refresh'         => '#home-banners',
				'refresh_wrapper' => true,
				'refresh_id'      => 'home-banners',
				'controls'        => [
					'home_banners_is_disabled'  => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=banners=1' ],
						],
					],
					'home_banners_layout'       => [
						'label'             => __( 'Layout', 'foodz' ),
						'default'           => '1',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'choices'           => [
							'1' => __( 'Wide banner', 'foodz' ),
							'2' => __( '2 banners', 'foodz' ),
							'3' => __( '3 banners', 'foodz' ),
							'4' => __( '4 banners', 'foodz' ),
						],
					],
					'home_banners_1'            => [
						'label'                => __( 'Banner place - 1', 'foodz' ),
						'description'          => __( 'You can select any banners for random showing in this place.', 'foodz' ),
						'type'                 => 'checklist',
						'choices'              => 'ideapark_customizer_banners',
						'class'                => 'WP_Customize_Checklist_Control',
						'max-height'           => '160',
						'sanitize_callback'    => 'sanitize_text_field',
						'refresh_pre_callback' => 'ideapark_parallax_destroy',
						'refresh_callback'     => 'ideapark_parallax_init',
					],
					'home_banners_2'            => [
						'label'             => __( 'Banner place - 2', 'foodz' ),
						'description'       => __( 'You can select any banners for random showing in this place.', 'foodz' ),
						'type'              => 'checklist',
						'choices'           => 'ideapark_customizer_banners',
						'class'             => 'WP_Customize_Checklist_Control',
						'max-height'        => '160',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_banners_layout' => [ '2', '3', '4' ],
						],
					],
					'home_banners_3'            => [
						'label'             => __( 'Banner place - 3', 'foodz' ),
						'description'       => __( 'You can select any banners for random showing in this place.', 'foodz' ),
						'type'              => 'checklist',
						'choices'           => 'ideapark_customizer_banners',
						'class'             => 'WP_Customize_Checklist_Control',
						'max-height'        => '160',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_banners_layout' => [ '3', '4' ],
						],
					],
					'home_banners_4'            => [
						'label'             => __( 'Banner place - 4', 'foodz' ),
						'description'       => __( 'You can select any banners for random showing in this place.', 'foodz' ),
						'type'              => 'checklist',
						'choices'           => 'ideapark_customizer_banners',
						'class'             => 'WP_Customize_Checklist_Control',
						'max-height'        => '160',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_banners_layout' => [ '4' ],
						],
					],
					'home_banners_1_height'     => [
						'label'                => __( 'Banner Height', 'foodz' ),
						'default'              => 240,
						'type'                 => 'slider',
						'sanitize_callback'    => 'sanitize_text_field',
						'class'                => 'WP_Customize_Range_Control',
						'min'                  => 240,
						'max'                  => 500,
						'step'                 => 1,
						'refresh_pre_callback' => 'ideapark_parallax_destroy',
						'refresh_callback'     => 'ideapark_parallax_init',
						'dependency'           => [
							'home_banners_layout' => [ '1' ],
						],
					],
					'home_banners_1_text_align' => [
						'label'                => __( 'Text align', 'foodz' ),
						'section'              => 'background_image',
						'type'                 => 'select',
						'sanitize_callback'    => 'sanitize_text_field',
						'default'              => 'text-center',
						'choices'              => [
							'text-left'   => __( 'Left', 'foodz' ),
							'text-center' => __( 'Center', 'foodz' ),
							'text-right'  => __( 'Right', 'foodz' ),
						],
						'refresh_pre_callback' => 'ideapark_parallax_destroy',
						'refresh_callback'     => 'ideapark_parallax_init',
						'dependency'           => [
							'home_banners_layout' => [ '1' ],
						],
					],
					'home_banners_1_container'  => [
						'label'                => __( 'Boxed view', 'foodz' ),
						'default'              => false,
						'type'                 => 'checkbox',
						'sanitize_callback'    => 'ideapark_sanitize_checkbox',
						'refresh_pre_callback' => 'ideapark_parallax_destroy',
						'refresh_callback'     => 'ideapark_parallax_init',
						'dependency'           => [
							'home_banners_layout' => [ '1' ],
						],
					],
					'home_banners_1_parallax'   => [
						'label'                => __( 'Parallax', 'foodz' ),
						'default'              => false,
						'type'                 => 'checkbox',
						'sanitize_callback'    => 'ideapark_sanitize_checkbox',
						'refresh_pre_callback' => 'ideapark_parallax_destroy',
						'refresh_callback'     => 'ideapark_parallax_init',
						'dependency'           => [
							'home_banners_layout' => [ '1' ],
						],
					],
					'home_banners_top_margin'   => [
						'label'                => __( 'Add top margin', 'foodz' ),
						'default'              => true,
						'type'                 => 'checkbox',
						'sanitize_callback'    => 'ideapark_sanitize_checkbox',
						'refresh_pre_callback' => 'ideapark_parallax_destroy',
						'refresh_callback'     => 'ideapark_parallax_init',
					],
				],
			],
			[
				'section_id'       => 'product-tabs',
				'title'            => __( 'Product Tabs', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-tabs',
				'refresh_wrapper'  => true,
				'refresh_id'       => 'home-tabs',
				'refresh_callback' => 'ideapark_init_home_tabs',
				'controls'         => [

					'home_tab_products_is_disabled' => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=product-tabs=1' ],
						],
					],

					'home_tab_products' => [
						'label'             => __( 'Products in tab', 'foodz' ),
						'description'       => __( 'The number of products in every tab.', 'foodz' ),
						'default'           => 12,
						'class'             => 'WP_Customize_Number_Control',
						'type'              => 'number',
						'sanitize_callback' => 'absint',
					],

					'home_tab_orderby' => [
						'label'             => __( 'Order by', 'foodz' ),
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							''           => 'default',
							'date'       => 'date the product was published',
							'id'         => 'post ID of the product',
							'menu_order' => 'menu order',
							'popularity' => 'number of purchases',
							'rand'       => 'random',
							'rating'     => 'average product rating',
							'title'      => 'product title',
						],
					],

					'home_tab_order' => [
						'label'             => __( 'Order', 'foodz' ),
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							'ASC'  => 'ASC',
							'DESC' => 'DESC',
						],
						'dependency'        => [
							'home_tab_orderby' => [ 'not_empty' ]
						],
					],

					'home_tab_carousel' => [
						'label'             => __( 'Carousel', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'home_product_order' => [
						'label'             => __( 'Tab list', 'foodz' ),
						'description'       => __( 'Add or delete tab, and then drag and drop tabs below to set up their order. You can also enable or disable any tab', 'foodz' ),
						'type'              => 'checklist',
						'default'           => '',
						'choices'           => [],
						'choices_add'       => 'ideapark_customizer_product_tab_list',
						'sortable'          => true,
						'class'             => 'WP_Customize_Checklist_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],


					'home_featured_title' => [
						'label'             => __( 'Featured products tab header', 'foodz' ),
						'default'           => __( 'Featured', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_product_order' => [ 'search=featured_products=1' ],
						],
					],

					'home_sale_title' => [
						'label'             => __( 'Sale products tab header', 'foodz' ),
						'default'           => __( 'On a Sale', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_product_order' => [ 'search=sale_products=1' ],
						],
					],

					'home_best_selling_title' => [
						'label'             => __( 'Best-Selling products tab header', 'foodz' ),
						'default'           => __( 'Bestsellers', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_product_order' => [ 'search=best_selling_products=1' ],
						],
					],

					'home_recent_title' => [
						'label'             => __( 'Recent products tab header', 'foodz' ),
						'default'           => __( 'Latest', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_product_order' => [ 'search=recent_products=1' ],
						],
					],

					'home_tab_fast_filter' => [
						'label'             => __( 'Show horizontal options filter', 'foodz' ),
						'description'       => __( 'For category tabs only', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'category_fast_filter'           => [ 1 ],
							'category_fast_filter_attribute' => [ 'not_empty' ]
						],
					],

					'home_tab_view_more' => [
						'label'             => __( 'View more button', 'foodz' ),
						'description'       => __( 'For category tabs only', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'home_tab_view_more_item' => [
						'label'             => __( 'Show view more button as last product', 'foodz' ),
						'description'       => __( 'For category tabs with carousel only', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'home_tab_carousel' => [ 1 ],
						],
					],

					'home_tab_fullscreen' => [
						'label'             => __( 'Fullscreen view', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'home_tab_carousel' => [ 0 ],
						],
					],

					'home_tab_top_margin' => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'       => 'product-promo',
				'title'            => __( 'Product Promo Block', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-promo',
				'refresh_wrapper'  => true,
				'refresh_id'       => 'home-promo',
				'refresh_callback' => 'ideapark_init_home_promo',
				'controls'         => [

					'home_products_promo_is_disabled' => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=product-promo=1' ],
						],
					],

					'home_promo_products' => [
						'label'             => __( 'Products in carousel', 'foodz' ),
						'description'       => __( 'The number of products in carousel.', 'foodz' ),
						'default'           => 6,
						'class'             => 'WP_Customize_Number_Control',
						'type'              => 'number',
						'sanitize_callback' => 'absint',
					],

					'home_promo_orderby' => [
						'label'             => __( 'Order by', 'foodz' ),
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							''           => 'default',
							'date'       => 'date the product was published',
							'id'         => 'post ID of the product',
							'menu_order' => 'menu order',
							'popularity' => 'number of purchases',
							'rand'       => 'random',
							'rating'     => 'average product rating',
							'title'      => 'product title',
						],
					],

					'home_promo_order' => [
						'label'             => __( 'Order', 'foodz' ),
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							'ASC'  => 'ASC',
							'DESC' => 'DESC',
						],
						'dependency'        => [
							'home_promo_orderby' => [ 'not_empty' ]
						],
					],

					'home_promo_source' => [
						'label'             => __( 'Products source', 'foodz' ),
						'type'              => 'select',
						'default'           => 'featured_products',
						'choices'           => 'ideapark_customizer_product_tab_list',
						'class'             => 'WP_Customize_Select_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'home_promo_layout' => [
						'label'             => __( 'Layout', 'foodz' ),
						'type'              => 'select',
						'default'           => 'right',
						'choices'           => [
							'left'  => __( 'Products left', 'foodz' ),
							'right' => __( 'Products right', 'foodz' ),
						],
						'sanitize_callback' => 'sanitize_text_field',
					],

					'home_promo_title' => [
						'label'             => __( 'Header', 'foodz' ),
						'default'           => __( 'We serve passion', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field'
					],

					'home_promo_content_type' => [
						'label'             => __( 'Promo content type', 'foodz' ),
						'type'              => 'radio',
						'default'           => 'html',
						'choices'           => [
							'html'      => __( 'HTML', 'foodz' ),
							'shortcode' => __( 'Shortcode', 'foodz' ),
						],
						'sanitize_callback' => 'sanitize_text_field',
					],

					'home_promo_content' => [
						'label'             => __( 'HTML Content', 'foodz' ),
						'description'       => __( 'Also you can paste shortcode, generated with any  plugin (for example, Contacts Form 7)', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'class'             => 'WP_Customize_Text_Editor_Control',
						'dependency'        => [
							'home_promo_content_type' => [ 'html' ]
						],
					],

					'home_promo_shortcode' => [
						'label'             => __( 'Shortcode', 'foodz' ),
						'description'       => __( 'Paste shortcode, generated with any  plugin (for example, Slider Revolution or Contact Form 7)', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_promo_content_type' => [ 'shortcode' ]
						],
					],

					'home_promo_background_image' => [
						'label'             => __( 'Content Background Image', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'home_promo_background_color' => [
						'label'             => __( 'Content Background Color', 'foodz' ),
						'description'       => __( 'Leave empty for transparent background', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'home_promo_view_more' => [
						'label'             => __( 'View more button', 'foodz' ),
						'description'       => __( 'For category tabs only', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'home_promo_top_margin' => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'       => 'brands',
				'title'            => __( 'Brands', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-brands',
				'refresh_wrapper'  => true,
				'refresh_id'       => 'home-brands',
				'refresh_callback' => 'ideapark_init_home_brands_carousel',
				'controls'         => [
					'home_brands_is_disabled' => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=brands=1' ],
						],
					],
					'home_brands_header'      => [
						'label'             => __( 'Section Header', 'foodz' ),
						'default'           => __( 'Brands', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_brands_margins'     => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'       => 'posts',
				'title'            => __( 'Blog Posts', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-posts',
				'refresh_wrapper'  => true,
				'refresh_id'       => 'home-posts',
				'refresh_callback' => 'ideapark_init_masonry',
				'controls'         => [
					'home_post_is_disabled' => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=posts=1' ],
						],
					],
					'home_post_header'      => [
						'label'             => __( 'Section Header', 'foodz' ),
						'default'           => __( 'Blog Posts', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_post_category'    => [
						'label'             => __( 'Posts Category', 'foodz' ),
						'description'       => __( 'Select category if you want the posts from this category to be shown at the bottom of the home page', 'foodz' ),
						'default'           => 0,
						'class'             => 'WP_Customize_Category_Control',
						'sanitize_callback' => 'absint',
					],
					'home_post_count'       => [
						'label'             => __( 'Posts in section', 'foodz' ),
						'default'           => 4,
						'type'              => 'number',
						'sanitize_callback' => 'absint',
					],
				],
			],
			[
				'section_id'       => 'testimonials',
				'title'            => __( 'Testimonials', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-testimonials',
				'refresh_wrapper'  => true,
				'refresh_id'       => 'home-testimonials',
				'refresh_callback' => 'ideapark_init_home_testimonials_carousel',
				'controls'         => [
					'home_testimonials_is_disabled'      => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=testimonials=1' ],
						],
					],
					'home_testimonials_header'           => [
						'label'             => __( 'Section Header', 'foodz' ),
						'default'           => __( 'Testimonials', 'foodz' ),
						'type'              => 'text',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_testimonials_margins'          => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_testimonials_background_color' => [
						'label'             => __( 'Background Color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			],
			[
				'section_id'      => 'icons',
				'title'           => __( 'Icons block', 'foodz' ),
				'description'     => __( 'You can fill from 1 to 4 blocks', 'foodz' ),
				'panel'           => 'front_page_builder',
				'refresh'         => '#home-icons',
				'refresh_wrapper' => true,
				'refresh_id'      => 'home-icons',
				'controls'        => [
					'home_icons_is_disabled'      => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=icons=1' ],
						],
					],
					'home_icons_info_1'           => [
						'label'             => __( 'Block', 'foodz' ) . ' - 1',
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_icon_1'           => [
						'label'             => __( 'Icon', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_header_1'         => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_content_1'        => [
						'label'             => __( 'HTML Content', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'class'             => 'WP_Customize_Text_Editor_Control',
					],
					'home_icons_info_2'           => [
						'label'             => __( 'Block', 'foodz' ) . ' - 2',
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_icon_2'           => [
						'label'             => __( 'Icon', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_header_2'         => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_content_2'        => [
						'label'             => __( 'HTML Content', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'class'             => 'WP_Customize_Text_Editor_Control',
					],
					'home_icons_info_3'           => [
						'label'             => __( 'Block', 'foodz' ) . ' - 3',
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_icon_3'           => [
						'label'             => __( 'Icon', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_header_3'         => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_content_3'        => [
						'label'             => __( 'HTML Content', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'class'             => 'WP_Customize_Text_Editor_Control',
					],
					'home_icons_info_4'           => [
						'label'             => __( 'Block', 'foodz' ) . ' - 4',
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_icon_4'           => [
						'label'             => __( 'Icon', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_header_4'         => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_content_4'        => [
						'label'             => __( 'HTML Content', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'class'             => 'WP_Customize_Text_Editor_Control',
					],
					'home_icons_info_misc'        => [
						'label'             => __( 'General  settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_boxed'            => [
						'label'             => __( 'Boxed view', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_icons_background_image' => [
						'label'             => __( 'Background Image', 'foodz' ),
						'class'             => 'WP_Customize_Image_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_background_color' => [
						'label'             => __( 'Background Color', 'foodz' ),
						'description'       => __( 'Leave empty for transparent background', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_icons_margins'          => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'      => 'text',
				'title'           => __( 'Home page content', 'foodz' ),
				'panel'           => 'front_page_builder',
				'refresh'         => '#home-text',
				'refresh_wrapper' => true,
				'refresh_id'      => 'home-text',
				'controls'        => [
					'home_text_is_disabled'      => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=text=1' ],
						],
					],
					'home_text_background_color' => [
						'label'             => __( 'Background Color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_text_hide_header'      => [
						'label'             => __( 'Hide Header', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'home_text_boxed' => [
						'label'             => __( 'Boxed view', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'home_text_margins' => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

				],
			],
			[
				'section_id'       => 'html',
				'title'            => __( 'HTML block', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-html',
				'refresh_wrapper'  => true,
				'refresh_id'       => 'home-html',
				'refresh_callback' => 'ideapark_third_party_reload',
				'controls'         => [
					'home_html_is_disabled'      => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=html=1' ],
						],
					],
					'home_html_header'           => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_html_content'          => [
						'label'             => __( 'HTML Content', 'foodz' ),
						'description'       => __( 'Also you can paste shortcode, generated with any  plugin (for example, Contacts Form 7)', 'foodz' ),
						'type'              => 'text_editor',
						'default'           => '',
						'sanitize_callback' => 'wp_kses_post',
						'class'             => 'WP_Customize_Text_Editor_Control',
					],
					'home_html_background_color' => [
						'label'             => __( 'Background Color', 'foodz' ),
						'description'       => __( 'Leave empty for transparent background', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_html_container'        => [
						'label'             => __( 'Show html inside container', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_html_boxed'            => [
						'label'             => __( 'Boxed view', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_html_padding'          => [
						'label'             => __( 'Add top and bottom padding', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_html_margins'          => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'       => 'shortcode',
				'title'            => __( 'Shortcode block', 'foodz' ),
				'panel'            => 'front_page_builder',
				'refresh'          => '#home-shortcode',
				'refresh_id'       => 'home-shortcode',
				'refresh_wrapper'  => true,
				'refresh_callback' => 'ideapark_third_party_reload',
				'controls'         => [
					'home_shortcode_is_disabled'      => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=shortcode=1' ],
						],
					],
					'home_shortcode_header'           => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_shortcode_content'          => [
						'label'             => __( 'Shortcode', 'foodz' ),
						'description'       => __( 'Paste shortcode, generated with any  plugin (for example, Contacts Form 7)', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_shortcode_background_color' => [
						'label'             => __( 'Background Color', 'foodz' ),
						'description'       => __( 'Leave empty for transparent background', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_shortcode_container'        => [
						'label'             => __( 'Show code inside container', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_shortcode_boxed'            => [
						'label'             => __( 'Boxed view', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_shortcode_padding'          => [
						'label'             => __( 'Add top and bottom padding', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_shortcode_margins'          => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section_id'      => 'subscribe',
				'title'           => __( 'Subscribe block', 'foodz' ),
				'panel'           => 'front_page_builder',
				'refresh'         => '.c-subscribe',
				'refresh_id'      => 'home-subscribe',
				'refresh_wrapper' => true,
				'controls'        => [
					'home_subscribe_is_disabled'      => [
						'label'             => wp_kses_post( __( 'This section is not shown because it is disabled in ', 'foodz' ) . '<a href="#" class="ideapark-control-focus" data-control="home_sections">' . __( 'General settings', 'foodz' ) . '</a>' ),
						'class'             => 'WP_Customize_Warning_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'dependency'        => [
							'home_sections' => [ 'search!=subscribe=1' ],
						],
					],
					'home_subscribe_header'           => [
						'label'             => __( 'Header', 'foodz' ),
						'type'              => 'text',
						'default'           => 'Subscribe to Newsletter',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_subscribe_subheader'        => [
						'label'             => __( 'Subheader', 'foodz' ),
						'type'              => 'text',
						'default'           => 'Subscribe to the weekly newsletter for all the latest updates',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_subscribe_content'          => [
						'label'             => __( 'Shortcode', 'foodz' ),
						'description'       => __( 'Paste subscribe, generated with any subscribe plugin (for example, MailChimp)', 'foodz' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_subscribe_background_color' => [
						'label'             => __( 'Background Color', 'foodz' ),
						'description'       => __( 'Leave empty for transparent background', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#EFF7FF',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'home_subscribe_container'        => [
						'label'             => __( 'Boxed view', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'home_subscribe_margins'          => [
						'label'             => __( 'Add top margin', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'title'    => __( 'Fonts', 'foodz' ),
				'priority' => 45,
				'controls' => [

					'theme_font_1'          => [
						'label'             => __( 'Header Font 1 (Google Font)', 'foodz' ),
						'default'           => 'Oswald',
						'description'       => __( 'Default font: Oswald', 'foodz' ),
						'sanitize_callback' => 'ideapark_sanitize_font_choice',
						'type'              => 'select',
						'choices'           => 'ideapark_get_font_choices',
					],
					'theme_font_1_weight'   => [
						'label'             => __( 'Header Font Weight', 'foodz' ),
						'default'           => '700',
						'description'       => __( 'Default: 700', 'foodz' ),
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							'100' => '100',
							'200' => '200',
							'300' => '300',
							'400' => '400 (normal)',
							'500' => '500',
							'600' => '600',
							'700' => '700 (bold)',
							'800' => '800',
							'900' => '900',
						],
					],
					'theme_font_1_weight_2' => [
						'label'             => __( 'Header Font Weight (for Subheaders)', 'foodz' ),
						'default'           => '200',
						'description'       => __( 'Default: 200', 'foodz' ),
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							'100' => '100',
							'200' => '200',
							'300' => '300',
							'400' => '400 (normal)',
							'500' => '500',
							'600' => '600',
							'700' => '700 (bold)',
							'800' => '800',
							'900' => '900',
						],
					],
					'theme_font_2'          => [
						'label'             => __( 'Header Font 2 (Google Font)', 'foodz' ),
						'default'           => 'Roboto Condensed',
						'description'       => __( 'Default font: Roboto Condensed', 'foodz' ),
						'sanitize_callback' => 'ideapark_sanitize_font_choice',
						'type'              => 'select',
						'choices'           => 'ideapark_get_font_choices',
					],
					'theme_font_2_weight'   => [
						'label'             => __( 'Header Font Weight', 'foodz' ),
						'default'           => '700',
						'description'       => __( 'Default: 700', 'foodz' ),
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'select',
						'choices'           => [
							'100' => '100',
							'200' => '200',
							'300' => '300',
							'400' => '400 (normal)',
							'500' => '500',
							'600' => '600',
							'700' => '700 (bold)',
							'800' => '800',
							'900' => '900',
						],
					],
					'theme_font_3'          => [
						'label'             => __( 'Content Font (Google Font)', 'foodz' ),
						'default'           => 'Roboto',
						'description'       => __( 'Default font: Roboto', 'foodz' ),
						'sanitize_callback' => 'ideapark_sanitize_font_choice',
						'type'              => 'select',
						'choices'           => 'ideapark_get_font_choices',
					],
					'theme_font_subsets'    => [
						'label'             => __( 'All Fonts subset (if available)', 'foodz' ),
						'default'           => 'latin-ext',
						'description'       => __( 'Default: Latin Extended', 'foodz' ),
						'sanitize_callback' => 'ideapark_sanitize_font_choice',
						'type'              => 'select',
						'choices'           => 'ideapark_get_google_font_subsets',
					],
				],
			],
			[
				'title'    => __( 'Post/Page', 'foodz' ),
				'priority' => 107,
				'controls' => [

					'sidebar_settings' => [
						'label'             => __( 'Sidebar', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'sidebar_blog'     => [
						'label'             => __( 'Sidebar in Blog and Archive', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'sidebar_post'     => [
						'label'             => __( 'Sidebar in Post / Page', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],


					'post_page_settings' => [
						'label'             => __( 'Post settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'post_hide_featured_image' => [
						'label'             => __( 'Hide Featured Image', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_category'       => [
						'label'             => __( 'Hide Category', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_date'           => [
						'label'             => __( 'Hide Date', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_share'          => [
						'label'             => __( 'Hide Share Buttons', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_tags'           => [
						'label'             => __( 'Hide Tags', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_comment'        => [
						'label'             => __( 'Hide Comment Link', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_author'         => [
						'label'             => __( 'Hide Author Info', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'post_hide_postnav'        => [
						'label'             => __( 'Hide Post Navigation', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],

			[
				'title'       => __( 'Performance', 'foodz' ),
				'description' => __( 'Use these options to put your theme to a high speed as well as save your server resources!', 'foodz' ),
				'priority'    => 130,
				'controls'    => [
					'use_minified_css'      => [
						'label'             => __( 'Use minified CSS', 'foodz' ),
						'description'       => __( 'Load all theme css files combined and minified into a single file', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'use_minified_js'       => [
						'label'             => __( 'Use minified JS', 'foodz' ),
						'description'       => __( 'Load all theme js files combined and minified into a single file', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'load_jquery_in_footer' => [
						'label'             => __( 'Load jQuery in footer', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'lazyload'              => [
						'label'             => __( 'Lazy load images', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'disable_wc_block_styles'   => [
						'label'             => __( 'Disable WooCommerce block styles', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'google_fonts_display_swap' => [
						'label'             => __( 'Use parameter display=swap for Google Fonts', 'foodz' ),
						'default'           => false,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
				],
			],
			[
				'section'  => 'colors',
				'controls' => [

					'headers_color' => [
						'label'             => __( 'Headers and main elements color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#3A3D49',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'text_color' => [
						'label'             => __( 'Base text color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#777A83',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'buttons_background_color' => [
						'label'             => __( 'Buttons background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#FFD141',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'buttons_text_color' => [
						'label'             => __( 'Buttons text color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'wave_color' => [
						'label'             => __( 'Wave underline color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#FE8128',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'shadow_color' => [
						'label'             => __( 'Modal window overlay color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#1A212E',
						'sanitize_callback' => 'sanitize_text_field',
					],
				]
			],

			[
				'panel'    => 'woocommerce',
				'section'  => 'woocommerce_store_notice',
				'controls' => [
					'store_notice'                  => [
						'label'             => __( 'Store notice placement', 'foodz' ),
						'default'           => 'top',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'radio',
						'priority'          => 50,
						'choices'           => [
							'top'    => __( 'At the top of the page', 'foodz' ),
							'bottom' => __( 'At the bottom of the screen (fixed)', 'foodz' ),
						],
					],
					'store_notice_color'            => [
						'label'             => __( 'Store notice text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#FFFFFF',
						'priority'          => 51,
					],
					'store_notice_background_color' => [
						'label'             => __( 'Store notice background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '#CF3540',
						'priority'          => 53,
					],
				]
			],

			[
				'panel'       => 'woocommerce',
				'title'       => __( 'Foodz Settings', 'foodz' ),
				'description' => __( 'This is a settings section to change Foodz WooCommerce properties.', 'foodz' ),
				'priority'    => 0,
				'controls'    => [

					'product_tabs' => [
						'label'             => __( 'Product Tabs (Default)', 'foodz' ),
						'description'       => __( 'Enable or disable tab, and then drag and drop tabs below to set up their order', 'foodz' ),
						'type'              => 'checklist',
						'default'           => 'description=1|additional_information=1|reviews=1',
						'choices'           => [
							'description'            => __( 'Description', 'woocommerce' ),
							'additional_information' => __( 'Additional information', 'woocommerce' ),
							'reviews'                => __( 'Reviews', 'woocommerce' ),
						],
						'sortable'          => true,
						'class'             => 'WP_Customize_Checklist_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'shop_modal' => [
						'label'             => __( 'Images modal gallery in product list', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'shop_product_modal' => [
						'label'             => __( 'Images modal gallery on product page', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'hide_uncategorized' => [
						'label'             => __( 'Hide Uncategorized category', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'shop_sidebar' => [
						'label'             => __( 'Show sidebar on product list', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_sidebar' => [
						'label'             => __( 'Show sidebar on product page', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'shop_product_navigation_same_term' => [
						'label'             => __( 'Product navigation in same category', 'foodz' ),
						'description'       => __( 'Keep product navigation within the same category.', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_short_description' => [
						'label'             => __( 'Show product short description in the product list', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_share' => [
						'label'             => __( 'Show share buttons on product page', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'category_fast_filter' => [
						'label'             => __( 'Horizontal options filter', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'category_fast_filter_attribute' => [
						'label'             => __( 'Attribute for horizontal filter', 'foodz' ),
						'type'              => 'select',
						'sanitize_callback' => 'sanitize_text_field',
						'choices'           => 'ideapark_get_all_atributes',
						'dependency'        => [
							'category_fast_filter' => [ 1 ],
						],
					],

					'product_marker_attribute' => [
						'label'             => __( 'Attribute for graphic product markers', 'foodz' ),
						'type'              => 'select',
						'sanitize_callback' => 'sanitize_text_field',
						'choices'           => 'ideapark_get_all_atributes',
						'refresh'           => false
					],

					'product_variations_in_grid' => [
						'label'             => __( 'Show product variations in grid view', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'product_variations_in_grid_selector' => [
						'label'             => __( 'Product variations selector', 'foodz' ),
						'type'              => 'select',
						'default'           => 'radio',
						'choices'           => [
							'select' => __( 'Select', 'foodz' ),
							'radio'  => __( 'Radio buttons', 'foodz' ),
						],
						'sanitize_callback' => 'sanitize_text_field',
					],

					'product_rating_info'    => [
						'label'             => __( 'Product Star Rating', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'product_preview_rating' => [
						'label'             => __( 'Show star rating in the product list', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],
					'star_rating_color'      => [
						'label'             => __( 'Star rating color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#FFD141',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'product_badges_info'  => [
						'label'             => __( 'Product Badges', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'featured_badge_color' => [
						'label'             => __( 'Featured badge color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#D1333C',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'featured_badge_text'  => [
						'label'             => __( 'Featured badge text', 'foodz' ),
						'description'       => __( 'Disabled if empty', 'foodz' ),
						'type'              => 'text',
						'default'           => __( 'Featured', 'foodz' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'sale_badge_color'     => [
						'label'             => __( 'Sale badge color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#FE8128',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'sale_badge_text'      => [
						'label'             => __( 'Sale badge text', 'foodz' ),
						'description'       => __( 'Disabled if empty', 'foodz' ),
						'type'              => 'text',
						'default'           => __( 'Sale', 'foodz' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'new_badge_color'      => [
						'label'             => __( 'New badge color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'default'           => '#74C557',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'new_badge_text'       => [
						'label'             => __( 'New badge text', 'foodz' ),
						'description'       => __( 'Disabled if empty', 'foodz' ),
						'type'              => 'text',
						'default'           => __( 'New', 'foodz' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'product_newness'      => [
						'label'             => __( 'Product newness', 'foodz' ),
						'description'       => __( 'Display the New badge for how many days? Set 0 for disable `NEW` badge.', 'foodz' ),
						'default'           => 5,
						'class'             => 'WP_Customize_Number_Control',
						'type'              => 'number',
						'sanitize_callback' => 'absint',
					],

					'category_settings_info'           => [
						'label'             => __( 'Product Category Header', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'category_image_enabled'           => [
						'label'             => __( 'Category image inside the page title', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'refresh'           => false,
						'refresh_css'       => '.c-page-header--category',
					],
					'category_image_parallax'          => [
						'label'             => __( 'Parallax', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'dependency'        => [
							'category_image_enabled' => [ 1 ],
						],
					],
					'category_image_position'          => [
						'label'             => __( 'Image position', 'foodz' ),
						'type'              => 'select',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => 'right center',
						'choices'           => [
							'left top'      => __( 'Top Left', 'foodz' ),
							'center top'    => __( 'Top', 'foodz' ),
							'right top'     => __( 'Top Right', 'foodz' ),
							'left center'   => __( 'Left', 'foodz' ),
							'center center' => __( 'Center', 'foodz' ),
							'right center'  => __( 'Right', 'foodz' ),
							'left bottom'   => __( 'Bottom Left', 'foodz' ),
							'center bottom' => __( 'Bottom', 'foodz' ),
							'right bottom'  => __( 'Bottom Right', 'foodz' ),
						],
						'refresh'           => false,
						'refresh_css'       => '.c-page-header--category',
						'dependency'        => [
							'category_image_enabled'  => [ 1 ],
							'category_image_parallax' => [ 0 ],
						],
					],
					'category_image_size'              => [
						'label'             => __( 'Image size', 'foodz' ),
						'type'              => 'select',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => 'auto',
						'choices'           => [
							'auto'    => __( 'Original', 'foodz' ),
							'contain' => __( 'Fit to area (contain)', 'foodz' ),
							'cover'   => __( 'Fill area (cover)', 'foodz' ),
						],
						'refresh'           => false,
						'refresh_css'       => '.c-page-header--category',
						'dependency'        => [
							'category_image_enabled'  => [ 1 ],
							'category_image_parallax' => [ 0 ],
						],
					],
					'category_header_color'            => [
						'label'             => __( 'Page header text color ', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-page-header--category'
					],
					'category_header_background_color' => [
						'label'             => __( 'Page header background color', 'foodz' ),
						'class'             => 'WP_Customize_Color_Control',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
						'refresh'           => false,
						'refresh_css'       => '.c-page-header--category'
					],

					'wishlist_settings_info' => [
						'label'             => __( 'Wishlist', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'wishlist_share' => [
						'label'             => __( 'Wishlist Share', 'foodz' ),
						'default'           => true,
						'type'              => 'checkbox',
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
					],

					'wishlist_page' => [
						'label'             => __( 'Wishlist page', 'foodz' ),
						'description'       => __( 'Used to create the share links and wishlist button in header', 'foodz' ),
						'default'           => 0,
						'class'             => 'WP_Customize_Page_Control',
						'sanitize_callback' => 'absint',
					],

					'product_mobile_settings_info' => [
						'label'             => __( 'Mobile Settings', 'foodz' ),
						'class'             => 'WP_Customize_Info_Control',
						'sanitize_callback' => 'sanitize_text_field',
					],

					'product_mobile_single_ajax_add_to_cart' => [
						'label'             => __( 'Enable AJAX add to cart buttons on single product page', 'foodz' ),
						'type'              => 'checkbox',
						'default'           => true,
						'sanitize_callback' => 'sanitize_text_field'
					],

					'product_mobile_layout' => [
						'label'             => __( 'Mobile product grid layout', 'foodz' ),
						'default'           => 'layout-product-1',
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'image-radio',
						'class'             => 'WP_Customize_Image_Radio_Control',
						'choices'           => [
							'layout-product-1' => IDEAPARK_THEME_URI . '/assets/img/mobile-1.png',
							'layout-product-2' => IDEAPARK_THEME_URI . '/assets/img/mobile-2.png',
							'layout-product-3' => IDEAPARK_THEME_URI . '/assets/img/mobile-3.png',
						],
					],

					'is_woocommerce_on' => [
						'label'             => '',
						'description'       => '',
						'type'              => 'hidden',
						'default'           => ideapark_woocommerce_on() ? 1 : 0,
						'sanitize_callback' => 'ideapark_sanitize_checkbox',
						'class'             => 'WP_Customize_Hidden_Control',
					],
				],
			],
		];

		ideapark_parse_added_blocks();

		ideapark_add_last_control();

		add_option( 'ideapark_customize', [
			'version'  => IDEAPARK_THEME_VERSION,
			'settings' => $ideapark_customize
		], '', 'yes' );
	}
}

if ( ! function_exists( 'ideapark_reset_theme_mods' ) ) {
	function ideapark_reset_theme_mods() {
		global $ideapark_customize;

		if ( ! empty( $ideapark_customize ) ) {
			foreach ( $ideapark_customize as $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( isset( $control['default'] ) ) {
							set_theme_mod( $control_name, $control['default'] );
							ideapark_mod_set_temp( $control_name, $control['default'] );
						}
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'ideapark_fix_theme_mods' ) ) {
	function ideapark_fix_theme_mods( $is_force = false ) {

		if ( is_admin() && ! IDEAPARK_THEME_IS_AJAX && $GLOBALS['pagenow'] != 'wp-login.php' ) {
			if ( $is_force || get_option( 'ideapark_fix_theme_mods_ver' ) != IDEAPARK_THEME_VERSION ) {
				update_option( 'ideapark_fix_theme_mods_ver', IDEAPARK_THEME_VERSION );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_init_theme_mods' ) ) {
	function ideapark_init_theme_mods() {
		global $ideapark_customize, $ideapark_customize_mods, $ideapark_customize_mods_def;

		$all_mods_default = [];
		$all_mods_names   = [];
		if ( ! empty( $ideapark_customize ) ) {
			foreach ( $ideapark_customize as $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( isset( $control['default'] ) ) {
							$ideapark_customize_mods_def[ $control_name ] = $all_mods_default[ $control_name ] = $control['default'];
						}
						$all_mods_names[] = $control_name;
					}
				}
			}
		}

		$ideapark_customize_mods = get_theme_mods();

		foreach ( $all_mods_names as $name ) {
			if ( ! is_array( $ideapark_customize_mods ) || ! array_key_exists( $name, $ideapark_customize_mods ) ) {
				$ideapark_customize_mods[ $name ] = apply_filters( "theme_mod_{$name}", array_key_exists( $name, $all_mods_default ) ? $all_mods_default[ $name ] : null );
			} else {
				$ideapark_customize_mods[ $name ] = apply_filters( "theme_mod_{$name}", $ideapark_customize_mods[ $name ] );
			}
		}

		ideapark_fix_theme_mods();
	}

	if ( $GLOBALS['pagenow'] != 'wp-login.php' ) {
		add_action( 'wp_loaded', 'ideapark_init_theme_mods' );
	}
}

if ( ! function_exists( 'ideapark_mod' ) ) {
	function ideapark_mod( $mod_name ) {
		global $ideapark_customize_mods;

		if ( array_key_exists( $mod_name, $ideapark_customize_mods ) ) {
			return $ideapark_customize_mods[ $mod_name ];
		} else {
			return null;
		}
	}
}

if ( ! function_exists( 'ideapark_mod_default' ) ) {
	function ideapark_mod_default( $mod_name ) {
		global $ideapark_customize_mods_def;

		if ( array_key_exists( $mod_name, $ideapark_customize_mods_def ) ) {
			return $ideapark_customize_mods_def[ $mod_name ];
		} else {
			return null;
		}
	}
}

if ( ! function_exists( 'ideapark_mod_set_temp' ) ) {
	function ideapark_mod_set_temp( $mod_name, $value ) {
		global $ideapark_customize_mods;
		if ( $value === null && isset( $ideapark_customize_mods[ $mod_name ] ) ) {
			unset( $ideapark_customize_mods[ $mod_name ] );
		} else {
			$ideapark_customize_mods[ $mod_name ] = $value;
		}
	}
}

if ( ! function_exists( 'ideapark_register_theme_customize' ) ) {
	function ideapark_register_theme_customize( $wp_customize ) {
		global $ideapark_customize_custom_css, $ideapark_customize, $ideapark_customize_panels;

		/**
		 * @var  WP_Customize_Manager $wp_customize
		 **/

		if ( class_exists( 'WP_Customize_Control' ) ) {

			class WP_Customize_Image_Radio_Control extends WP_Customize_Control {
				public $type = 'image-radio';

				public function render_content() {
					$input_id         = '_customize-input-' . $this->id;
					$description_id   = '_customize-description-' . $this->id;
					$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';

					if ( empty( $this->choices ) ) {
						return;
					}

					$name = '_customize-radio-' . $this->id;
					?>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $this->description ) ) : ?>
						<span id="<?php echo esc_attr( $description_id ); ?>"
							  class="description customize-control-description"><?php echo ideapark_wrap( $this->description ); ?></span>
					<?php endif; ?>

					<?php foreach ( $this->choices as $value => $label ) { ?>
						<span class="customize-inside-control-row">
						<label>
						<input
							id="<?php echo esc_attr( $input_id . '-radio-' . $value ); ?>"
							type="radio"
							<?php echo ideapark_wrap( $describedby_attr ); ?>
							value="<?php echo esc_attr( $value ); ?>"
							name="<?php echo esc_attr( $name ); ?>"
							<?php $this->link(); ?>
							<?php checked( $this->value(), $value ); ?>
							/>
						<?php echo( substr( $label, 0, 4 ) == 'http' ? '<img class="ideapark-radio-img" src="' . esc_url( $label ) . '">' : esc_html( $label ) ); ?></label>
						</span><?php
					}
				}
			}

			class WP_Customize_Number_Control extends WP_Customize_Control {
				public $type = 'number';

				public function render_content() {
					?>
					<label>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
						<input type="number" name="quantity" <?php $this->link(); ?>
							   value="<?php echo esc_textarea( $this->value() ); ?>" style="width:70px;">
					</label>
					<?php
				}
			}

			class WP_Customize_Category_Control extends WP_Customize_Control {

				public function render_content() {
					$dropdown = wp_dropdown_categories(
						[
							'name'              => '_customize-dropdown-categories-' . $this->id,
							'echo'              => 0,
							'show_option_none'  => '&mdash; ' . esc_html__( 'Select', 'foodz' ) . ' &mdash;',
							'option_none_value' => '0',
							'selected'          => $this->value(),
						]
					);

					$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

					printf(
						'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
						$this->label,
						$dropdown
					);
				}
			}

			class WP_Customize_Page_Control extends WP_Customize_Control {

				public function render_content() {
					$dropdown = wp_dropdown_pages(
						[
							'name'              => '_customize-dropdown-pages-' . $this->id,
							'echo'              => 0,
							'show_option_none'  => '&mdash; ' . esc_html__( 'Select', 'foodz' ) . ' &mdash;',
							'option_none_value' => '0',
							'selected'          => $this->value(),
						]
					);

					$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

					printf(
						'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
						$this->label,
						$dropdown
					);
				}
			}

			class WP_Customize_Info_Control extends WP_Customize_Control {
				public $type = 'info';

				public function render_content() {
					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'</div>'
					);
				}
			}

			class WP_Customize_Warning_Control extends WP_Customize_Control {
				public $type = 'warning';

				public function render_content() {
					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="notification-message">', '</span>' ),
						'<div class="notice notice-warning ideapark-notice">',
						'</div>'
					);
				}
			}

			class WP_Customize_Text_Editor_Control extends WP_Customize_Control {
				public $type = 'text_editor';

				public function render_content() {

					if ( function_exists( 'wp_enqueue_editor' ) ) {
						wp_enqueue_editor();
					}
					ob_start();
					wp_editor(
						$this->value(), '_customize-text-editor-' . esc_attr( $this->id ), [
							'default_editor' => 'tmce',
							'wpautop'        => isset( $this->input_attrs['wpautop'] ) ? $this->input_attrs['wpautop'] : false,
							'teeny'          => isset( $this->input_attrs['teeny'] ) ? $this->input_attrs['teeny'] : false,
							'textarea_rows'  => isset( $this->input_attrs['rows'] ) && $this->input_attrs['rows'] > 1 ? $this->input_attrs['rows'] : 10,
							'editor_height'  => 16 * ( isset( $this->input_attrs['rows'] ) && $this->input_attrs['rows'] > 1 ? (int) $this->input_attrs['rows'] : 10 ),
							'tinymce'        => [
								'resize'             => false,
								'wp_autoresize_on'   => false,
								'add_unload_trigger' => false,
							],
						]
					);
					$editor_html = ob_get_contents();
					ob_end_clean();

					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'<span class="customize-control-field-wrap">
							<input type="hidden"' . $this->get_link() .
						( ! empty( $this->input_attrs['var_name'] ) ? ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"' : '' ) .
						' value="' . esc_textarea( $this->value() ) . '" />' .

						ideapark_wrap( $editor_html, '<div class="ideapark_text_editor">', '</div>' ) . ' 
					</span></div>'
					);

					ideapark_mod_set_temp( 'need_footer_scripts', true );
				}
			}

			class WP_Customize_Select_Control extends WP_Customize_Control {
				public $type = 'select';

				public function render_content() {
					$input_id         = '_customize-input-' . $this->id;
					$description_id   = '_customize-description-' . $this->id;
					$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
					if ( empty( $this->choices ) ) {
						return;
					}

					?>
					<?php if ( ! empty( $this->label ) ) : ?>
						<label for="<?php echo esc_attr( $input_id ); ?>"
							   class="customize-control-title"><?php echo esc_html( $this->label ); ?></label>
					<?php endif; ?>
					<?php if ( ! empty( $this->description ) ) : ?>
						<span id="<?php echo esc_attr( $description_id ); ?>"
							  class="description customize-control-description"><?php echo ideapark_wrap( $this->description ); ?></span>
					<?php endif; ?>

					<select
						id="<?php echo esc_attr( $input_id ); ?>" <?php echo ideapark_wrap( $describedby_attr ); ?> <?php $this->link(); ?>>
						<?php
						$is_option_group = false;
						foreach ( $this->choices as $value => $label ) {
							if ( strpos( $value, '*' ) === 0 ) {
								if ( $is_option_group ) {
									echo ideapark_wrap( '</optgroup>' );
								}
								echo ideapark_wrap( '<optgroup label="' . $label . '">' );
								$is_option_group = true;
							} else {
								echo ideapark_wrap( '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>' );
							}

						}
						if ( $is_option_group ) {
							echo ideapark_wrap( '</optgroup>' );
						}
						?>
					</select>
					<?php
				}
			}

			class WP_Customize_Hidden_Control extends WP_Customize_Control {
				public $type = 'hidden';

				public function render_content() {
					?>
					<input type="hidden" name="_customize-hidden-<?php echo esc_attr( $this->id ); ?>" value=""
						<?php
						$this->link();
						if ( ! empty( $this->input_attrs['var_name'] ) ) {
							echo ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"';
						}
						?>
					>
					<?php
					if ( 'last_option' == $this->id && ideapark_mod( 'need_footer_scripts' ) ) {
						ideapark_mod_set_temp( 'need_footer_scripts', false );
						do_action( 'admin_print_footer_scripts' );
					}
				}
			}

			class WP_Customize_Range_Control extends WP_Customize_Control {
				public $type = 'range';

				public function render_content() {
					$show_value = ! isset( $this->input_attrs['show_value'] ) || $this->input_attrs['show_value'];
					$output     = '';

					wp_enqueue_script( 'jquery-ui-slider', false, [ 'jquery', 'jquery-ui-core' ], null, true );
					$is_range   = 'range' == $this->input_attrs['type'];
					$field_min  = ! empty( $this->input_attrs['min'] ) ? $this->input_attrs['min'] : 0;
					$field_max  = ! empty( $this->input_attrs['max'] ) ? $this->input_attrs['max'] : 100;
					$field_step = ! empty( $this->input_attrs['step'] ) ? $this->input_attrs['step'] : 1;
					$field_val  = ! empty( $value )
						? ( $value . ( $is_range && strpos( $value, ',' ) === false ? ',' . $field_max : '' ) )
						: ( $is_range ? $field_min . ',' . $field_max : $field_min );
					$output     .= '<div id="' . esc_attr( '_customize-range-' . esc_attr( $this->id ) ) . '"'
					               . ' class="ideapark_range_slider"'
					               . ' data-range="' . esc_attr( $is_range ? 'true' : 'min' ) . '"'
					               . ' data-min="' . esc_attr( $field_min ) . '"'
					               . ' data-max="' . esc_attr( $field_max ) . '"'
					               . ' data-step="' . esc_attr( $field_step ) . '"'
					               . '>'
					               . '<span class="ideapark_range_slider_label ideapark_range_slider_label_min">'
					               . esc_html( $field_min )
					               . '</span>'
					               . '<span class="ideapark_range_slider_label ideapark_range_slider_label_max">'
					               . esc_html( $field_max )
					               . '</span>';
					$values     = explode( ',', $field_val );
					for ( $i = 0; $i < count( $values ); $i ++ ) {
						$output .= '<span class="ideapark_range_slider_label ideapark_range_slider_label_cur">'
						           . esc_html( $values[ $i ] )
						           . '</span>';
					}
					$output .= '</div>';

					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'<span class="customize-control-field-wrap">
							<input type="' . ( ! $show_value ? 'hidden' : 'text' ) . '"' . $this->get_link() .
						( $show_value ? ' class="ideapark_range_slider_value"' : '' ) .
						( ! empty( $this->input_attrs['var_name'] ) ? ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"' : '' ) . '" />' .
						$output . ' 
					</span></div>'
					);

				}
			}

			class WP_Customize_Checklist_Control extends WP_Customize_Control {
				public $type = 'checklist';

				public function render_content() {
					$output = '';
					$value  = $this->value();

					if ( ! empty( $this->input_attrs['sortable'] ) ) {
						wp_enqueue_script( 'jquery-ui-sortable', false, [
							'jquery',
							'jquery-ui-core'
						], null, true );
					}
					$output .= '<div class="ideapark_checklist ' . ( ! empty( $this->input_attrs['max-height'] ) ? 'ideapark_checklist_scroll' : '' ) . ' ideapark_checklist_' . esc_attr( ! empty( $this->input_attrs['dir'] ) ? $this->input_attrs['dir'] : 'vertical' )
					           . ( ! empty( $this->input_attrs['sortable'] ) ? ' ideapark_sortable' : '' )
					           . '"' . ( ! empty( $this->input_attrs['max-height'] ) ? ' style="max-height: ' . trim( esc_attr( $this->input_attrs['max-height'] ) ) . 'px"' : '' )
					           . ( ! empty( $this->input_attrs['add_ajax_action'] ) ? ' data-add-ajax-action="' . esc_attr( $this->input_attrs['add_ajax_action'] ) . '"' : '' )
					           . ( ! empty( $this->input_attrs['delete_ajax_action'] ) ? ' data-delete-ajax-action="' . esc_attr( $this->input_attrs['delete_ajax_action'] ) . '"' : '' )
					           . '>';
					if ( ! is_array( $value ) ) {
						if ( ! empty( $value ) ) {
							parse_str( str_replace( '|', '&', $value ), $value );
						} else {
							$value = [];
						}
					}

					if ( ! empty( $this->input_attrs['choices_add'] ) ) {
						$choices = array_filter( $this->input_attrs['choices_add'], function ( $key ) use ( $value ) {
							return isset( $value[ $key ] );
						}, ARRAY_FILTER_USE_KEY );

						$choices = ideapark_array_merge( $value, $choices );
					} else {
						if ( ! empty( $this->input_attrs['sortable'] ) && is_array( $value ) ) {
							$value = array_filter( $value, function ( $key ) {
								return array_key_exists( $key, $this->input_attrs['choices'] );
							}, ARRAY_FILTER_USE_KEY );

							$this->input_attrs['choices'] = ideapark_array_merge( $value, $this->input_attrs['choices'] );
						}
						$choices = $this->input_attrs['choices'];
					}

					foreach ( $choices as $k => $v ) {
						$output .= '<div class="ideapark_checklist_item_label'
						           . ( ! empty( $this->input_attrs['sortable'] ) ? ' ideapark_sortable_item' : '' )
						           . '"><label>'
						           . '<input type="checkbox" value="1" data-name="' . $k . '"'
						           . ( isset( $value[ $k ] ) && 1 == (int) $value[ $k ] ? ' checked="checked"' : '' )
						           . ' />'
						           . ( substr( $v, 0, 4 ) == 'http' ? '<img src="' . esc_url( $v ) . '">' : esc_html( preg_replace( '~^[ \-]+~u', '', $v ) ) )
						           . '</label>'
						           . ( ! empty( $this->input_attrs['choices_edit'][ $k ] ) ? '<button type="button" class="ideapark_checklist_item_edit" data-control="' . esc_attr( $this->input_attrs['choices_edit'][ $k ] ) . '"><span class="dashicons dashicons-admin-generic"></span></button>' : '' )
						           . ( ! empty( $this->input_attrs['choices_delete'] ) && in_array( $k, $this->input_attrs['choices_delete'] ) || ! empty( $this->input_attrs['choices_add'] ) ? '<button type="button" class="ideapark_checklist_item_delete" data-section="' . esc_attr( $k ) . '"><span class="dashicons dashicons-no-alt"></span></button>' : '' )
						           . '</div>';
					}
					$output .= '</div>';

					$output_add = '';

					if ( ! empty( $this->input_attrs['can_add_block'] ) ) {
						$output_add .= ideapark_wrap(
							ideapark_wrap( esc_html__( 'Please reload the page to see the settings of the new blocks', 'foodz' ), '<span class="notification-message">', '<br><button type="button" data-id="' . esc_attr( $this->id ) . '" class="button-primary button ideapark-customizer-reload">' . esc_html__( 'Reload', 'foodz' ) . '</button></span>' ),
							'<div class="notice notice-warning ideapark-notice ideapark_checklist_add_notice">',
							'</div>'
						);
						$output_add .= '<div class="ideapark_checklist_add_wrap">';
						$output_add .= esc_html__( 'Add new block', 'foodz' );
						$output_add .= '<div class="ideapark_checklist_add_inline"><select class="ideapark_checklist_add_select">';
						$output_add .= '<option value="">' . esc_html__( '- select block -', 'foodz' ) . '</option>';
						foreach ( $this->input_attrs['can_add_block'] as $section_id ) {
							$output_add .= '<option value="' . esc_attr( $section_id ) . '">' . $this->input_attrs['choices'][ $section_id ] . '</option>';
						}
						$output_add .= '</select><button class="button ideapark_checklist_add_button" type="button">' . esc_html__( 'Add', 'foodz' ) . '</button></div>';
						$output_add .= '</div>';
					} elseif ( ! empty( $this->input_attrs['choices_add'] ) ) {
						$output_add      .= '<div class="ideapark_checklist_add_wrap">';
						$output_add      .= esc_html__( 'Add new', 'foodz' );
						$output_add      .= '<div class="ideapark_checklist_add_inline"><select class="ideapark_checklist_add_select">';
						$output_add      .= '<option value="">' . esc_html__( '- select -', 'foodz' ) . '</option>';
						$is_option_group = false;
						foreach ( $this->input_attrs['choices_add'] as $section_id => $section_name ) {
							if ( strpos( $section_id, '*' ) === 0 ) {
								if ( $is_option_group ) {
									$output_add .= '</optgroup>';
								}
								$output_add      .= '<optgroup label="' . $section_name . '">';
								$is_option_group = true;
							} else {
								$output_add .= '<option value="' . esc_attr( $section_id ) . '">' . $section_name . '</option>';
							}
						}
						if ( $is_option_group ) {
							$output_add .= '</optgroup>';
						}
						$output_add .= '</select><button class="button ideapark_checklist_add_button" type="button">' . esc_html__( 'Add', 'foodz' ) . '</button></div>';
						$output_add .= '</div>';
					}


					echo ideapark_wrap(
						ideapark_wrap( $this->label, '<span class="customize-control-title">', '</span>' ) .
						ideapark_wrap( $this->description, '<span class="customize-control-description description">', '</span>' ),
						'<div class="customize-control-wrap">',
						'<span class="customize-control-field-wrap">
							<input type="hidden" ' . $this->get_link() .
						( ! empty( $this->input_attrs['var_name'] ) ? ' data-var_name="' . esc_attr( $this->input_attrs['var_name'] ) . '"' : '' ) . ' />' .
						$output . '</span>' . $output_add . '</div>'
					);
				}
			}
		}

		$panel_priority = 1;

		foreach ( $ideapark_customize_panels as $panel_name => $panel ) {
			$wp_customize->add_panel( $panel_name, [
				'capability'  => 'edit_theme_options',
				'title'       => ! empty( $panel['title'] ) ? $panel['title'] : '',
				'description' => ! empty( $panel['description'] ) ? $panel['description'] : '',
				'priority'    => isset( $panel['priority'] ) ? $panel['priority'] : $panel_priority ++,
			] );
		}

		foreach ( $ideapark_customize as $i_section => $section ) {
			if ( ! empty( $section['controls'] ) ) {

				$panel_name = ! empty( $section['panel'] ) ? $section['panel'] : '';

				if ( ! array_key_exists( 'section', $section ) ) {
					$wp_customize->add_section( $section_name = 'ideapark_section_' . ( ! empty( $section['section_id'] ) ? $section['section_id'] : $i_section ), [
						'panel'       => $panel_name,
						'title'       => ! empty( $section['title'] ) ? $section['title'] : '',
						'description' => ! empty( $section['description'] ) ? $section['description'] : '',
						'priority'    => isset( $section['priority'] ) ? $section['priority'] : 160 + $i_section,
					] );
				} else {
					$section_name = $section['section'];
				}

				$control_priority = 1;
				$control_ids      = [];
				$first_control    = '';
				foreach ( $section['controls'] as $control_name => $control ) {

					if ( ! empty( $control['type'] ) || ! empty( $control['class'] ) ) {

						if ( ! $first_control ) {
							$first_control = $control_name;
						}

						$a = [
							'transport' => isset( $control['transport'] ) ? $control['transport'] : ( ( isset( $section['refresh'] ) && ! isset( $control['refresh'] ) && true !== $section['refresh'] ) || ( isset( $control['refresh'] ) && true !== $control['refresh'] ) ? 'postMessage' : 'refresh' )
						];
						if ( isset( $control['default'] ) ) {
							$a['default'] = $control['default'];
						}
						if ( isset( $control['sanitize_callback'] ) ) {
							$a['sanitize_callback'] = $control['sanitize_callback'];
						} else {
							die( 'No sanitize_callback found!' . print_r( $control, true ) );
						}

						call_user_func( [ $wp_customize, 'add_setting' ], $control_name, $a );

						if ( ! IDEAPARK_THEME_IS_AJAX_HEARTBEAT ) {

							if ( ! empty( $control['choices'] ) && is_string( $control['choices'] ) ) {
								if ( function_exists( $control['choices'] ) ) {
									$control['choices'] = call_user_func( $control['choices'] );
								} else {
									$control['choices'] = [];
								}
							}

							if ( ! empty( $control['choices_add'] ) && is_string( $control['choices_add'] ) ) {
								if ( function_exists( $control['choices_add'] ) ) {
									$control['choices_add'] = call_user_func( $control['choices_add'] );
								} else {
									$control['choices_add'] = [];
								}
							}
						}

						if ( empty( $control['class'] ) ) {
							$wp_customize->add_control(
								new WP_Customize_Control(
									$wp_customize,
									$control_name,
									[
										'label'    => $control['label'],
										'section'  => $section_name,
										'settings' => ! empty( $control['settings'] ) ? $control['settings'] : $control_name,
										'type'     => $control['type'],
										'priority' => ! empty( $control['priority'] ) ? $control['priority'] : $control_priority + 1,
										'choices'  => ! empty( $control['choices'] ) ? $control['choices'] : null,
									]
								)
							);
						} else {

							if ( class_exists( $control['class'] ) ) {
								$wp_customize->add_control(
									new $control['class'](
										$wp_customize,
										$control_name,
										[
											'label'           => $control['label'],
											'section'         => $section_name,
											'settings'        => ! empty( $control['settings'] ) ? $control['settings'] : $control_name,
											'type'            => ! empty( $control['type'] ) ? $control['type'] : null,
											'priority'        => ! empty( $control['priority'] ) ? $control['priority'] : $control_priority + 1,
											'choices'         => ! empty( $control['choices'] ) ? $control['choices'] : null,
											'active_callback' => ! empty( $control['active_callback'] ) ? $control['active_callback'] : '',
											'input_attrs'     => array_merge(
												$control, [
													'value'    => ideapark_mod( $control_name ),
													'var_name' => ! empty( $control['customizer'] ) ? $control['customizer'] : '',
												]
											),
										]
									)
								);
							}
						}

						if ( ! empty( $control['description'] ) ) {
							$ideapark_customize_custom_css[ '#customize-control-' . $control_name . ( ! empty( $control['type'] ) && in_array( $control['type'], [
								'radio',
								'checkbox'
							] ) ? '' : ' .customize-control-title' ) ] = $control['description'];
						}

						$f = false;
						if ( isset( $control['refresh'] ) && is_string( $control['refresh'] )
						     &&
						     (
							     ( $is_auto_load = isset( $control['refresh_id'] ) && ideapark_customizer_check_template_part( $control['refresh_id'] ) )
							     ||
							     function_exists( $f = "ideapark_customizer_partial_refresh_" . ( isset( $control['refresh_id'] ) ? $control['refresh_id'] : $control_name ) )
						     )
						     && isset( $wp_customize->selective_refresh ) ) {
							$wp_customize->selective_refresh->add_partial(
								$control_name, [
									'selector'            => $control['refresh'],
									'settings'            => $control_name,
									'render_callback'     => $is_auto_load ? 'ideapark_customizer_load_template_part' : $f,
									'container_inclusive' => ! empty( $control['refresh_wrapper'] ),
								]
							);
						} elseif ( ! isset( $control['refresh'] ) ) {
							$control_ids[] = $control_name;
						}
					}
				}

				if ( isset( $section['refresh_id'] ) && isset( $section['refresh'] ) && is_string( $section['refresh'] )
				     &&
				     (
					     ( $is_auto_load = ideapark_customizer_check_template_part( $section['refresh_id'] ) )
					     ||
					     function_exists( "ideapark_customizer_partial_refresh_{$section['refresh_id']}" )
				     )
				     && isset( $wp_customize->selective_refresh ) ) {
					$wp_customize->selective_refresh->add_partial(
						$first_control /* first control from this section*/, [
							'selector'            => $section['refresh'],
							'settings'            => $control_ids,
							'render_callback'     => $is_auto_load ? 'ideapark_customizer_load_template_part' : "ideapark_customizer_partial_refresh_{$section['refresh_id']}",
							'container_inclusive' => ! empty( $section['refresh_wrapper'] ),
						]
					);
				}
			}
		}

		$sec = $wp_customize->get_section( 'static_front_page' );
		if ( is_object( $sec ) ) {
			$sec->priority = 87;
		}

		$sec = $wp_customize->get_panel( 'woocommerce' );
		if ( is_object( $sec ) ) {
			$sec->priority = 110;
		}

		$sec = $wp_customize->get_panel( 'nav_menus' );
		if ( is_object( $sec ) ) {
			$sec->priority = 120;
		}

		$sec = $wp_customize->get_panel( 'widgets' );
		if ( is_object( $sec ) ) {
			$sec->priority = 125;
		}
	}
}

if ( ! function_exists( 'ideapark_get_theme_dependencies' ) ) {
	function ideapark_get_theme_dependencies() {
		global $ideapark_customize;
		$result              = [
			'refresh_css'          => [],
			'dependency'           => [],
			'refresh_callback'     => [],
			'refresh_pre_callback' => []
		];
		$partial_refresh     = [];
		$css_refresh         = [];
		$css_refresh_control = [];
		foreach ( $ideapark_customize as $i_section => $section ) {
			$first_control_name = '';
			if ( ! empty( $section['controls'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					if ( ! $first_control_name ) {
						$first_control_name = $control_name;
					}
					if ( ! empty( $control['refresh_css'] ) ) {
						$result['refresh_css'][] = $control_name;
					}
					if ( ! empty( $control['refresh'] ) && is_string( $control['refresh'] ) ) {
						$result['refresh'][ $control_name ] = $control['refresh'];
						$partial_refresh[]                  = trim( $control['refresh'] );
					} elseif ( ! empty( $control['refresh_css'] ) && is_string( $control['refresh_css'] ) ) {
						$result['refresh'][ $control_name ] = $control['refresh_css'];
					}

					if ( ! empty( $control['refresh_css'] ) && is_string( $control['refresh_css'] ) ) {
						$css_refresh[] = $selector = trim( $control['refresh_css'] );
						if ( ! array_key_exists( $selector, $css_refresh_control ) ) {
							$css_refresh_control[ $selector ] = $control_name;
						}
					}

					if ( ! empty( $control['refresh_callback'] ) && is_string( $control['refresh_callback'] ) ) {
						$result['refresh_callback'][ $control_name ] = $control['refresh_callback'];
					}

					if ( ! empty( $control['refresh_pre_callback'] ) && is_string( $control['refresh_pre_callback'] ) ) {
						$result['refresh_pre_callback'][ $control_name ] = $control['refresh_pre_callback'];
					}

					if ( ! empty( $control['dependency'] ) && is_array( $control['dependency'] ) ) {
						$result['dependency'][ $control_name ] = $control['dependency'];
					}
				}
			}

			if ( ! empty( $section['refresh'] ) && is_string( $section['refresh'] ) && $first_control_name ) {
				$result['refresh'][ $first_control_name ] = $section['refresh'];
				$partial_refresh[]                        = trim( $section['refresh'] );
			}

			if ( ! empty( $section['refresh_css'] ) && is_string( $section['refresh_css'] ) && $first_control_name ) {
				$css_refresh[] = $selector = trim( $section['refresh_css'] );
				if ( ! array_key_exists( $selector, $css_refresh_control ) ) {
					$css_refresh_control[ $selector ] = $first_control_name;
				}
			}

			if ( ! empty( $section['refresh_callback'] ) && is_string( $section['refresh_callback'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					$result['refresh_callback'][ $control_name ] = $section['refresh_callback'];
				}
			}

			if ( ! empty( $section['refresh_pre_callback'] ) && is_string( $section['refresh_pre_callback'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					$result['refresh_pre_callback'][ $control_name ] = $section['refresh_pre_callback'];
				}
			}
		}

		$refresh_only_css = array_diff( array_unique( $css_refresh ), array_unique( $partial_refresh ) );

		$result['refresh_only_css'] = [];
		foreach ( $refresh_only_css as $selector ) {
			$result['refresh_only_css'][ $selector ] = $css_refresh_control[ $selector ];
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_customizer_check_template_part' ) ) {
	function ideapark_customizer_check_template_part( $template ) {
		return ideapark_is_file( IDEAPARK_THEME_DIR . '/templates/' . $template . '.php' ) || ideapark_is_file( IDEAPARK_THEME_DIR . '/' . $template . '.php' );
	}
}

if ( ! function_exists( 'ideapark_customizer_load_template_part' ) ) {
	function ideapark_customizer_load_template_part( $_control ) {
		global $ideapark_customize;
		$is_found = false;
		foreach ( $ideapark_customize as $i_section => $section ) {
			if ( ! empty( $section['controls'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					$is_found = $control_name == $_control->id;
					if ( $is_found && ! empty( $control['refresh_id'] ) ) {
						ob_start();
						if ( ideapark_is_file( IDEAPARK_THEME_DIR . '/templates/' . $control['refresh_id'] . '.php' ) ) {
							ideapark_get_template_part( 'templates/' . $control['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
						}
						if ( ideapark_is_file( IDEAPARK_THEME_DIR . '/' . $control['refresh_id'] . '.php' ) ) {
							ideapark_get_template_part( $control['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
						}
						$output = ob_get_contents();
						ob_end_clean();

						return $output;
					}
					if ( $is_found ) {
						break;
					}
				}
			}
			if ( $is_found && ! empty( $section['refresh_id'] ) ) {
				ob_start();
				if ( ideapark_is_file( IDEAPARK_THEME_DIR . '/templates/' . $section['refresh_id'] . '.php' ) ) {
					ideapark_get_template_part( 'templates/' . $section['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
				}
				if ( ideapark_is_file( IDEAPARK_THEME_DIR . '/' . $section['refresh_id'] . '.php' ) ) {
					ideapark_get_template_part( $section['refresh_id'], ! empty( $section['section_id'] ) ? [ 'section_id' => $section['section_id'] ] : null );
				}
				$output = ob_get_contents();
				ob_end_clean();

				return $output;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_customizer_get_template_part' ) ) {
	function ideapark_customizer_get_template_part( $template ) {
		ob_start();
		get_template_part( $template );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}


if ( ! function_exists( 'ideapark_customizer_partial_refresh_top_menu' ) ) {
	function ideapark_customizer_partial_refresh_top_menu() {
		return ideapark_customizer_get_template_part( 'templates/home-top-menu' );
	}
}

if ( ! function_exists( 'ideapark_expanded_alowed_tags' ) ) {
	function ideapark_expanded_alowed_tags() {
		$my_allowed = wp_kses_allowed_html( 'post' );

		$my_allowed['iframe'] = [
			'src'             => [],
			'height'          => [],
			'width'           => [],
			'frameborder'     => [],
			'allowfullscreen' => [],
			'style'           => [],
		];

		return $my_allowed;
	}
}

if ( ! function_exists( 'ideapark_sanitize_embed_field' ) ) {
	function ideapark_sanitize_embed_field( $input ) {
		return wp_kses( $input, ideapark_expanded_alowed_tags() );
	}
}

if ( ! function_exists( 'ideapark_parse_checklist' ) ) {
	function ideapark_parse_checklist( $str ) {
		$values = [];
		if ( ! empty( $str ) ) {
			parse_str( str_replace( '|', '&', $str ), $values );
		}

		return $values;
	}
}

if ( ! function_exists( 'ideapark_sanitize_checkbox' ) ) {
	function ideapark_sanitize_checkbox( $input ) {
		if ( $input ):
			$output = true;
		else:
			$output = false;
		endif;

		return $output;
	}
}

if ( ! function_exists( 'ideapark_customize_admin_style' ) ) {
	function ideapark_customize_admin_style() {
		global $ideapark_customize_custom_css;
		if ( ! empty( $ideapark_customize_custom_css ) && is_array( $ideapark_customize_custom_css ) ) {
			?>
			<style type="text/css">
				<?php foreach ( $ideapark_customize_custom_css as $style_name => $text ) { ?>
				<?php echo esc_attr( $style_name ); ?>:after {
					content: "<?php echo esc_attr($text) ?>";
				}

				<?php } ?>
			</style>
			<?php
		}
	}
}

if ( ! function_exists( 'ideapark_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'ideapark_customizer_preview_js' );
	function ideapark_customizer_preview_js() {
		wp_enqueue_script(
			'ideapark-customizer-preview',
			IDEAPARK_THEME_URI . '/assets/js/admin-customizer-preview.js',
			[ 'customize-preview' ], null, true
		);
	}
}

if ( ! function_exists( 'ideapark_get_all_atributes' ) ) {
	function ideapark_get_all_atributes() {
		$attribute_array = [ '' => '' ];
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return $attribute_array;
		}
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( $taxonomy = wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$attribute_array[ $taxonomy ] = $tax->attribute_name;
				}
			}
		}

		return $attribute_array;
	}
}

if ( ! function_exists( 'ideapark_get_all_fonts' ) ) {
	function ideapark_get_all_fonts() {
		$google_fonts = ideapark_get_google_fonts();

		/**
		 * Allow for developers to modify the full list of fonts.
		 *
		 * @param array $fonts The list of all fonts.
		 *
		 * @since 1.3.0.
		 *
		 */
		return apply_filters( 'ideapark_all_fonts', $google_fonts );
	}
}

if ( ! function_exists( 'ideapark_get_font_choices' ) ) {
	function ideapark_get_font_choices() {
		$fonts   = ideapark_get_all_fonts();
		$choices = [];

		// Repackage the fonts into value/label pairs
		foreach ( $fonts as $key => $font ) {
			$choices[ $key ] = $font['label'];
		}

		return $choices;
	}
}

if ( ! function_exists( 'ideapark_get_google_font_uri' ) ) {
	function ideapark_get_google_font_uri( $fonts ) {

		$fonts = array_unique( $fonts );
		$hash  = md5( implode( ',', $fonts ) . '--' . IDEAPARK_THEME_VERSION );

		if ( ( $data = get_option( 'ideapark_google_font_uri' ) ) && ! empty( $data['version'] ) && ! empty( $data['uri'] ) ) {
			if ( $data['version'] == $hash ) {
				return $data['uri'];
			} else {
				delete_option( 'ideapark_google_font_uri' );
			}
		}

		$allowed_fonts = ideapark_get_google_fonts();
		$family        = [];

		foreach ( $fonts as $font ) {
			$font = trim( $font );

			if ( array_key_exists( $font, $allowed_fonts ) ) {
				$filter   = [ '200', 'regular', '500', '700', '900' ];
				$family[] = urlencode( $font . ':' . join( ',', ideapark_choose_google_font_variants( $font, $allowed_fonts[ $font ]['variants'], $filter ) ) );
			}
		}

		if ( empty( $family ) ) {
			return '';
		} else {
			$request = '//fonts.googleapis.com/css?family=' . implode( rawurlencode( '|' ), $family );
		}

		$subset = ideapark_mod( 'theme_font_subsets' );

		if ( 'all' === $subset ) {
			$subsets_available = ideapark_get_google_font_subsets();

			unset( $subsets_available['all'] );

			$subsets = array_keys( $subsets_available );
		} else {
			$subsets = [
				'latin',
				$subset,
			];
		}

		if ( ! empty( $subsets ) ) {
			$request .= urlencode( '&subset=' . join( ',', $subsets ) );
		}

		if ( ideapark_mod( 'google_fonts_display_swap' ) ) {
			$request .= '&display=swap';
		}

		add_option( 'ideapark_google_font_uri', [
			'version' => $hash,
			'uri'     => esc_url( $request )
		], '', 'yes' );

		return esc_url( $request );
	}
}

if ( ! function_exists( 'ideapark_get_google_font_subsets' ) ) {
	function ideapark_get_google_font_subsets() {
		global $_ideapark_google_fonts_subsets;

		$list = [
			'all' => esc_html__( 'All', 'foodz' ),
		];

		foreach ( $_ideapark_google_fonts_subsets as $subset ) {
			$name = ucfirst( trim( $subset ) );
			if ( preg_match( '~-ext$~', $name ) ) {
				$name = preg_replace( '~-ext$~', ' ' . esc_html__( 'Extended', 'foodz' ), $name );
			}
			$list[ $subset ] = esc_html( $name );
		}

		return $list;
	}
}

if ( ! function_exists( 'ideapark_choose_google_font_variants' ) ) {
	function ideapark_choose_google_font_variants( $font, $variants = [], $filter = [ 'regular', '700' ] ) {
		$chosen_variants = [];
		if ( empty( $variants ) ) {
			$fonts = ideapark_get_google_fonts();

			if ( array_key_exists( $font, $fonts ) ) {
				$variants = $fonts[ $font ]['variants'];
			}
		}

		foreach ( $filter as $var ) {
			if ( in_array( $var, $variants ) && ! array_key_exists( $var, $chosen_variants ) ) {
				$chosen_variants[] = $var;
			}
		}

		if ( empty( $chosen_variants ) ) {
			$variants[0];
		}

		return apply_filters( 'ideapark_font_variants', array_unique( $chosen_variants ), $font, $variants );
	}
}

if ( ! function_exists( 'ideapark_sanitize_font_choice' ) ) {
	function ideapark_sanitize_font_choice( $value ) {
		if ( is_int( $value ) ) {
			// The array key is an integer, so the chosen option is a heading, not a real choice
			return '';
		} else if ( array_key_exists( $value, ideapark_get_font_choices() ) ) {
			return $value;
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'ideapark_customizer_banners' ) ) {
	function ideapark_customizer_banners() {
		$result = [];
		if ( $banners = get_posts( [
			'posts_per_page'   => - 1,
			'post_type'        => 'banner',
			'meta_key'         => '_thumbnail_id',
			'suppress_filters' => false,
			'order'            => 'ASC',
			'orderby'          => 'menu_order'
		] ) ) {
			foreach ( $banners as $banner ) {
				$attachment_id = get_post_thumbnail_id( $banner->ID );
				$image         = wp_get_attachment_image_url( $attachment_id );
				if ( $image ) {
					$result[ $banner->ID ] = $image;
				} elseif ( ! empty( $banner->post_title ) ) {
					$result[ $banner->ID ] = $banner->post_title;
				} elseif ( $image_alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) ) {
					$result[ $banner->ID ] = $image_alt;
				} else {
					$result[ $banner->ID ] = '#' . $banner->ID;
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_customizer_product_tab_list' ) ) {
	function ideapark_customizer_product_tab_list() {
		$list = [
			'*main'                 => esc_html__( 'Main', 'foodz' ),
			'featured_products'     => esc_html__( 'Featured Products', 'foodz' ),
			'sale_products'         => esc_html__( 'Sale Products', 'foodz' ),
			'best_selling_products' => esc_html__( 'Best-Selling Products', 'foodz' ),
			'recent_products'       => esc_html__( 'Recent Products', 'foodz' ),
			'*categories'           => esc_html__( 'Categories', 'foodz' ),
		];

		$args = [
			'taxonomy'     => 'product_cat',
			'orderby'      => 'term_group',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0,
			'exclude'      => get_option( 'default_product_cat' ),
		];
		if ( $all_categories = get_categories( $args ) ) {

			$category_name   = [];
			$category_parent = [];
			foreach ( $all_categories as $cat ) {
				$category_name[ $cat->term_id ]    = esc_html( $cat->name );
				$category_parent[ $cat->parent ][] = $cat->term_id;
			}

			$get_category = function ( $parent = 0, $prefix = '' ) use ( &$list, &$category_parent, &$category_name, &$get_category ) {
				if ( array_key_exists( $parent, $category_parent ) ) {
					$categories = $category_parent[ $parent ];
					foreach ( $categories as $category_id ) {
						$list[ $category_id ] = $prefix . $category_name[ $category_id ];
						$get_category( $category_id, $prefix . ' - ' );
					}
				}
			};

			$get_category();
		}

		return $list;
	}
}

if ( ! function_exists( 'ideapark_add_last_control' ) ) {
	function ideapark_add_last_control() {
		global $ideapark_customize;

		$ideapark_customize[ sizeof( $ideapark_customize ) - 1 ]['controls']['last_option'] = [
			'label'             => '',
			'description'       => '',
			'type'              => 'hidden',
			'default'           => '',
			'sanitize_callback' => 'ideapark_sanitize_checkbox',
			'class'             => 'WP_Customize_Hidden_Control',
		];
	}
}

if ( ! function_exists( 'ideapark_ajax_customizer_add_section' ) ) {
	function ideapark_ajax_customizer_add_section() {
		if ( current_user_can( 'customize' ) && ! empty( $_POST['section'] ) ) {
			if ( $section = ideapark_add_new_section( $_POST['section'] ) ) {
				wp_send_json( $section );
			} else {
				wp_send_json( [ 'error' => esc_html__( 'Something went wrong...', 'foodz' ) ] );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_ajax_customizer_delete_section' ) ) {
	function ideapark_ajax_customizer_delete_section() {
		if ( current_user_can( 'customize' ) && ! empty( $_POST['section'] ) ) {
			if ( $section = ideapark_delete_section( $_POST['section'] ) ) {
				wp_send_json( [ 'success' => 1 ] );
			} else {
				wp_send_json( [ 'error' => esc_html__( 'Something went wrong...', 'foodz' ) ] );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_parse_added_blocks' ) ) {
	function ideapark_parse_added_blocks() {
		global $ideapark_customize;
		if ( $added_blocks = get_option( 'ideapark_added_blocks' ) ) {
			foreach ( $ideapark_customize as $section_index => $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( ! empty( $section['panel'] ) && ! empty( $control['can_add_block'] ) && ! empty( $control['type'] ) && $control['type'] == 'checklist' && array_key_exists( $section['panel'], $added_blocks ) ) {
							foreach ( $added_blocks[ $section['panel'] ] as $item ) {
								$section_orig_id   = $item['section_id'];
								$index             = $item['index'];
								$checklist_control = &$ideapark_customize[ $section_index ]['controls'][ $control_name ];

								foreach ( $ideapark_customize as $_section ) {
									if ( ! empty( $_section['section_id'] ) && $_section['section_id'] == $section_orig_id ) {
										$section_new               = $_section;
										$section_new['section_id'] .= '-' . $index;
										$section_new['title']      .= ' - ' . $index;
										if ( ! empty( $section_new['refresh'] ) ) {
											$section_new['refresh'] .= '-' . $index;
										}
										$new_controls = [];
										if ( ! empty( $section_new['controls'] ) ) {
											foreach ( $section_new['controls'] as $_control_name => $_control ) {
												if ( ! empty( $_control['dependency'] ) ) {
													foreach ( $_control['dependency'] as $key => $val ) {
														if ( $key == $control_name ) {
															$_control['dependency'][ $key ] = [ 'search!=' . $section_orig_id . '-' . $index . '=1' ];
														} elseif ( array_key_exists( $key, $_section['controls'] ) ) {
															$_control['dependency'][ $key . '_' . $index ] = $val;
															unset( $_control['dependency'][ $key ] );
														}
													}
												}
												$new_controls[ $_control_name . '_' . $index ] = $_control;
											}
											$section_new['controls'] = $new_controls;
										}
										$ideapark_customize[] = $section_new;
										break;
									}
								}

								$checklist_control['default']                                    .= '|' . $section_orig_id . '-' . $index . '=0';
								$checklist_control['choices'][ $section_orig_id . '-' . $index ] = $checklist_control['choices'][ $section_orig_id ] . ' - ' . $index;
								if ( ! empty( $checklist_control['choices_edit'][ $section_orig_id ] ) ) {
									$checklist_control['choices_edit'][ $section_orig_id . '-' . $index ] = $checklist_control['choices_edit'][ $section_orig_id ] . '_' . $index;
								}
								if ( empty( $checklist_control['choices_delete'] ) ) {
									$checklist_control['choices_delete'] = [];
								}
								$checklist_control['choices_delete'][] = $section_orig_id . '-' . $index;
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'ideapark_delete_section' ) ) {
	function ideapark_delete_section( $section_id ) {
		$added_blocks = get_option( 'ideapark_added_blocks' );
		$is_changed   = false;
		if ( ! empty( $added_blocks ) ) {
			foreach ( $added_blocks as $panel_name => $items ) {
				foreach ( $items as $item_index => $item ) {
					if ( $item['section_id'] . '-' . $item['index'] == $section_id ) {
						unset( $added_blocks[ $panel_name ][ $item_index ] );
						$is_changed = true;
						break;
					}
				}
			}
		}
		if ( $is_changed ) {
			if ( ! empty( $added_blocks ) ) {
				update_option( 'ideapark_added_blocks', $added_blocks );
			} else {
				delete_option( 'ideapark_added_blocks' );
			}
			delete_option( 'ideapark_customize' );
		}

		return $is_changed;
	}
}

if ( ! function_exists( 'ideapark_add_new_section' ) ) {
	function ideapark_add_new_section( $section_orig_id ) {
		global $ideapark_customize;
		$added_blocks = get_option( 'ideapark_added_blocks' );
		if ( empty( $added_blocks ) ) {
			$added_blocks = [];
		}
		$section_name = '';
		$section_id   = '';
		foreach ( $ideapark_customize as $section ) {
			if ( ! empty( $section['controls'] ) ) {
				foreach ( $section['controls'] as $control_name => $control ) {
					if ( ! empty( $section['panel'] ) && ! empty( $control['can_add_block'] ) && ! empty( $control['type'] ) && $control['type'] == 'checklist' && ! empty( $control['can_add_block'] ) && in_array( $section_orig_id, $control['can_add_block'] ) ) {
						if ( array_key_exists( $section['panel'], $added_blocks ) ) {
							$index = 2;
							foreach ( $added_blocks[ $section['panel'] ] as $item ) {
								if ( $item['section_id'] == $section_orig_id && $item['index'] == $index ) {
									$index ++;
								}
							}
						} else {
							$index = 2;

							$added_blocks[ $section['panel'] ] = [];
						}
						$added_blocks[ $section['panel'] ][] = [
							'section_id' => $section_orig_id,
							'index'      => $index
						];
						$section_name                        = $control['choices'][ $section_orig_id ] . ' - ' . $index;
						$section_id                          = $section_orig_id . '-' . $index;
						break;
					}
				}
			}
		}

		if ( ! empty( $added_blocks ) ) {
			update_option( 'ideapark_added_blocks', $added_blocks );
		} else {
			delete_option( 'ideapark_added_blocks' );
		}

		delete_option( 'ideapark_customize' );

		return $section_name && $section_id ? [
			'name' => $section_name,
			'id'   => $section_id
		] : false;
	}
}

$_ideapark_google_fonts_cache   = false;
$_ideapark_google_fonts_subsets = [];

if ( ! function_exists( 'ideapark_get_google_fonts' ) ) {
	function ideapark_get_google_fonts() {
		global $_ideapark_google_fonts_cache, $_ideapark_google_fonts_subsets;

		if ( $_ideapark_google_fonts_cache ) {
			return $_ideapark_google_fonts_cache;
		}

		if ( ( $data = get_option( 'ideapark_google_fonts' ) ) && ! empty( $data['version'] ) && ! empty( $data['list'] ) && ! empty( $data['subsets'] ) ) {
			if ( $data['version'] == IDEAPARK_THEME_VERSION ) {
				$_ideapark_google_fonts_cache   = $data['list'];
				$_ideapark_google_fonts_subsets = $data['subsets'];

				return $_ideapark_google_fonts_cache;
			} else {
				delete_option( 'ideapark_google_fonts' );
			}
		}

		$decoded_google_fonts = json_decode( ideapark_fgc( IDEAPARK_THEME_DIR . '/includes/customize/webfonts.json' ), true );
		$webfonts             = [];
		foreach ( $decoded_google_fonts['items'] as $key => $value ) {
			$font_family                          = $decoded_google_fonts['items'][ $key ]['family'];
			$webfonts[ $font_family ]             = [];
			$webfonts[ $font_family ]['label']    = $font_family;
			$webfonts[ $font_family ]['variants'] = $decoded_google_fonts['items'][ $key ]['variants'];
			$webfonts[ $font_family ]['subsets']  = $decoded_google_fonts['items'][ $key ]['subsets'];
			$_ideapark_google_fonts_subsets       = array_unique( array_merge( $_ideapark_google_fonts_subsets, $decoded_google_fonts['items'][ $key ]['subsets'] ) );
		}

		sort( $_ideapark_google_fonts_subsets );
		$_ideapark_google_fonts_cache = apply_filters( 'ideapark_get_google_fonts', $webfonts );

		add_option( 'ideapark_google_fonts', [
			'version' => IDEAPARK_THEME_VERSION,
			'list'    => $_ideapark_google_fonts_cache,
			'subsets' => $_ideapark_google_fonts_subsets
		], '', 'yes' );

		return $_ideapark_google_fonts_cache;
	}
}

if ( ! function_exists( 'ideapark_clear_customize_cache' ) ) {
	function ideapark_clear_customize_cache() {
		global $ideapark_customize;
		if ( ! empty( $ideapark_customize ) ) {
			foreach ( $ideapark_customize as $section ) {
				if ( ! empty( $section['controls'] ) ) {
					foreach ( $section['controls'] as $control_name => $control ) {
						if ( isset( $control['class'] ) && $control['class'] == 'WP_Customize_Image_Control' ) {
							if ( ( $url = get_theme_mod( $control_name ) ) && ( $attachment_id = attachment_url_to_postid( $url ) ) ) {
								$params = wp_get_attachment_image_src( $attachment_id, 'full' );
								set_theme_mod( $control_name . '__url', $params[0] );
								set_theme_mod( $control_name . '__attachment_id', $attachment_id );
								set_theme_mod( $control_name . '__width', $params[1] );
								set_theme_mod( $control_name . '__height', $params[2] );
							} else {
								remove_theme_mod( $control_name . '__url' );
								remove_theme_mod( $control_name . '__attachment_id' );
								remove_theme_mod( $control_name . '__width' );
								remove_theme_mod( $control_name . '__height' );
							}
						}
						if ( ! empty( $control['is_option'] ) ) {
							$val = get_theme_mod( $control_name );
							if ( $val === null && isset( $control['default'] ) ) {
								$val = $control['default'];
							}
							if ( $val !== null ) {
								update_option( 'foodz_mod_' . $control_name, $val );
							} else {
								delete_option( 'foodz_mod_' . $control_name );
							}
						}
					}
				}
			}
		}

		delete_option( 'ideapark_customize' );
		delete_option( 'ideapark_google_fonts' );
		delete_option( 'ideapark_google_font_uri' );
		delete_option( 'ideapark_styles_hash' );
		delete_option( 'ideapark_editor_styles_hash' );
		ideapark_init_theme_customize();
		ideapark_editor_style();
	}
}

if ( ! function_exists( 'ideapark_mod_hex_color_norm' ) ) {
	function ideapark_mod_hex_color_norm( $option, $default = 'inherit' ) {
		if ( preg_match( '~^\#[0-9A-F]{3,6}$~i', $option ) ) {
			return $option;
		} elseif ( preg_match( '~^\#[0-9A-F]{3,6}$~i', $color = '#' . ltrim( ideapark_mod( $option ), '#' ) ) ) {
			return $color;
		} else {
			return $default;
		}
	}
}

if ( ! function_exists( 'ideapark_hex_to_rgb_overlay' ) ) {
	function ideapark_hex_to_rgb_overlay( $hex_color_1, $hex_color_2, $alpha_2 ) {
		list( $r_1, $g_1, $b_1 ) = sscanf( $hex_color_1, "#%02x%02x%02x" );
		list( $r_2, $g_2, $b_2 ) = sscanf( $hex_color_2, "#%02x%02x%02x" );

		$r = min( round( $alpha_2 * $r_2 + ( 1 - $alpha_2 ) * $r_1 ), 255 );
		$g = min( round( $alpha_2 * $g_2 + ( 1 - $alpha_2 ) * $g_1 ), 255 );
		$b = min( round( $alpha_2 * $b_2 + ( 1 - $alpha_2 ) * $b_1 ), 255 );

		return "rgb($r, $g, $b)";
	}
}


if ( ! function_exists( 'ideapark_hex_to_rgb_shift' ) ) {
	function ideapark_hex_to_rgb_shift( $hex_color, $k = 1 ) {
		list( $r, $g, $b ) = sscanf( $hex_color, "#%02x%02x%02x" );

		$r = min( round( $r * $k ), 255 );
		$g = min( round( $g * $k ), 255 );
		$b = min( round( $b * $k ), 255 );

		return "rgb($r, $g, $b)";
	}
}

if ( ! function_exists( 'ideapark_hex_to_rgba' ) ) {
	function ideapark_hex_to_rgba( $hex_color, $opacity = 1 ) {
		list( $r, $g, $b ) = sscanf( $hex_color, "#%02x%02x%02x" );

		return "rgba($r, $g, $b, $opacity)";
	}
}

add_action( 'init', 'ideapark_init_theme_customize', 0 );
add_action( 'customize_register', 'ideapark_register_theme_customize', 100 );
add_action( 'customize_controls_print_styles', 'ideapark_customize_admin_style' );
add_action( 'customize_save_after', 'ideapark_clear_customize_cache', 100 );
add_action( 'wp_ajax_ideapark_customizer_add_section', 'ideapark_ajax_customizer_add_section' );
add_action( 'wp_ajax_ideapark_customizer_delete_section', 'ideapark_ajax_customizer_delete_section' );

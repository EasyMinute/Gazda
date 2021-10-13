<?php

/*------------------------------------*\
	Constants & Globals
\*------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$theme_obj = wp_get_theme( 'foodz' );

define( 'IDEAPARK_THEME_DEMO', false );
define( 'IDEAPARK_THEME_IS_AJAX', function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) );
define( 'IDEAPARK_THEME_IS_AJAX_HEARTBEAT', IDEAPARK_THEME_IS_AJAX && ! empty( $_POST['action'] ) && ( $_POST['action'] == 'heartbeat' ) );
define( 'IDEAPARK_THEME_IS_AJAX_SEARCH', IDEAPARK_THEME_IS_AJAX && ! empty( $_POST['action'] ) && ( $_POST['action'] == 'ideapark_ajax_search' ) );
define( 'IDEAPARK_THEME_IS_AJAX_QUANTITY', IDEAPARK_THEME_IS_AJAX && ! empty( $_POST['action'] ) && ( $_POST['action'] == 'ideapark_update_quantity' ) );
define( 'IDEAPARK_THEME_IS_AJAX_CSS', IDEAPARK_THEME_IS_AJAX && ! empty( $_POST['action'] ) && ( $_POST['action'] == 'ideapark_ajax_custom_css' ) );
define( 'IDEAPARK_THEME_IS_AJAX_WISHLIST', IDEAPARK_THEME_IS_AJAX && ! empty( $_POST['action'] ) && ( $_POST['action'] == 'ideapark_wishlist_toggle' ) );
define( 'IDEAPARK_THEME_IS_AJAX_IMAGES', IDEAPARK_THEME_IS_AJAX && ! empty( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'ideapark_product_images' ) );
define( 'IDEAPARK_THEME_IS_AJAX_TAB', IDEAPARK_THEME_IS_AJAX && ! empty( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'ideapark_product_tab' ) );
define( 'IDEAPARK_THEME_NAME', $theme_obj['Name'] );
define( 'IDEAPARK_THEME_DIR', get_template_directory() );
define( 'IDEAPARK_THEME_URI', get_template_directory_uri() );
define( 'IDEAPARK_THEME_MANUAL', 'https://parkofideas.com/foodz/manual/' );
define( 'IDEAPARK_THEME_VERSION', '1.11' );

$wp_upload_arr = wp_get_upload_dir();

define( "IDEAPARK_THEME_UPLOAD_DIR", $wp_upload_arr['basedir'] . "/" . strtolower( sanitize_file_name( IDEAPARK_THEME_NAME ) ) . "/" );
define( "IDEAPARK_THEME_UPLOAD_URL", $wp_upload_arr['baseurl'] . "/" . strtolower( sanitize_file_name( IDEAPARK_THEME_NAME ) ) . "/" );

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

$ideapark_theme_scripts = [];
$ideapark_theme_styles  = [];
$ideapark_is_front_page = false;


if ( ! function_exists( 'ideapark_setup' ) ) {

	function ideapark_setup() {

		if ( ! ideapark_is_dir( IDEAPARK_THEME_UPLOAD_DIR ) ) {
			ideapark_mkdir( IDEAPARK_THEME_UPLOAD_DIR );
		}

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-background' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );

		add_theme_support( 'html5', [ 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ] );
		add_theme_support( 'woocommerce', [
			'thumbnail_image_width'         => 238,
			'gallery_thumbnail_image_width' => 55,
			'single_image_width'            => 445,
			'product_grid'                  => [
				'default_rows'    => 5,
				'min_rows'        => 1,
				'max_rows'        => 100,
				'default_columns' => 4,
				'min_columns'     => 3,
				'max_columns'     => 4,
			],
		] );

		add_image_size( 'ideapark-thumbnail-image-width-2x', 476, 476, true );

		add_image_size( 'ideapark-post', 360, '', false );
		add_image_size( 'ideapark-home-brands', 142, 160 );
		add_image_size( 'ideapark-mega-menu-thumb', 55, 55, true );
		add_image_size( 'ideapark-single-product-thumb', 110, 110, true );

		load_theme_textdomain( 'foodz', IDEAPARK_THEME_DIR . '/languages' );

		add_action( 'load_textdomain_mofile', 'ideapark_correct_tgmpa_mofile', 10, 2 );
		load_theme_textdomain( 'tgmpa', IDEAPARK_THEME_DIR . '/plugins/languages' );
		remove_action( 'load_textdomain_mofile', 'ideapark_correct_tgmpa_mofile', 10 );

		register_nav_menus( [
			'primary'  => esc_html__( 'Top Menu', 'foodz' ),
			'megamenu' => esc_html__( 'Mega Menu (Primary)', 'foodz' ),
		] );
	}
}

if ( ! function_exists( 'ideapark_check_version' ) ) {
	function ideapark_check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ideapark_is_requset( 'admin' ) && ( ( $current_version = get_option( 'ideapark_luchiana_theme_version', '' ) ) || ! $current_version ) && ( version_compare( $current_version, IDEAPARK_THEME_VERSION, '!=' ) ) ) {
			do_action( 'after_update_theme', $current_version, IDEAPARK_THEME_VERSION );
			add_action( 'init', function () use ( $current_version ) {
				do_action( 'after_update_theme_late', $current_version, IDEAPARK_THEME_VERSION );
			}, 999 );
			update_option( 'ideapark_luchiana_theme_version', IDEAPARK_THEME_VERSION );
			$theme = wp_get_theme();
			if ( $theme->parent() ) {
				$theme = $theme->parent();
			}
			update_option( str_replace( '-child', '', $theme->get_stylesheet() ) . '_about_page', 1 );
		}
	}
}

if ( ! function_exists( 'ideapark_set_image_dimensions' ) ) {
	function ideapark_set_image_dimensions() {

		update_option( 'woocommerce_thumbnail_cropping', '1:1' );

		update_option( 'thumbnail_size_w', 55 );
		update_option( 'thumbnail_size_h', 55 );

		update_option( 'medium_size_w', 360 );
		update_option( 'medium_size_h', '' );

		update_option( 'medium_large_size_w', 445 );
		update_option( 'medium_large_size_h', '' );

		update_option( 'large_size_w', '750' );
		update_option( 'large_size_h', '' );
	}
}

// Maximum width for media
if ( ! isset( $content_width ) ) {
	$content_width = 1220; // Pixels
}

require_once( IDEAPARK_THEME_DIR . '/includes/customize/ip_customize_settings.php' );
require_once( IDEAPARK_THEME_DIR . '/includes/customize/ip_customize_style.php' );

if ( ! class_exists( 'Ideaperk_Mega_Menu' ) ) {
	if ( ! is_admin() ) {
		require_once( IDEAPARK_THEME_DIR . '/includes/megamenu/custom_walker.php' );
	} else {
		require_once( IDEAPARK_THEME_DIR . '/includes/megamenu/edit_custom_walker.php' );
	}
	require_once( IDEAPARK_THEME_DIR . '/includes/megamenu/mega-menu.php' );
}

if ( is_admin() && ! IDEAPARK_THEME_IS_AJAX_SEARCH && ! IDEAPARK_THEME_IS_AJAX_CSS && ! IDEAPARK_THEME_IS_AJAX_QUANTITY && ! IDEAPARK_THEME_IS_AJAX_WISHLIST ) {
	require_once IDEAPARK_THEME_DIR . '/plugins/class-tgm-plugin-activation.php';
	add_action( 'tgmpa_register', 'ideapark_register_required_plugins' );
}

if ( is_admin() ) {
	require_once IDEAPARK_THEME_DIR . '/includes/theme-about/theme-about.php';
}

function ideapark_woocommerce_on() {
	return class_exists( 'WooCommerce' );
}

if ( ideapark_woocommerce_on() ) {
	require_once( IDEAPARK_THEME_DIR . '/includes/woocommerce/woocommerce.php' );
	require_once( IDEAPARK_THEME_DIR . '/includes/woocommerce/woocommerce-func.php' );

	if ( is_admin() ) {
		if ( ! IDEAPARK_THEME_IS_AJAX ) {
			require_once( IDEAPARK_THEME_DIR . '/includes/woocommerce/woocommerce-tax-extra-fields.php' );
		}
	}

	if ( ! is_admin() || IDEAPARK_THEME_IS_AJAX_WISHLIST || IDEAPARK_THEME_IS_AJAX_TAB || IDEAPARK_THEME_IS_AJAX_SEARCH ) {
		require_once( IDEAPARK_THEME_DIR . '/includes/woocommerce/woocommerce-wishlist.php' );
	}
}

if ( ! function_exists( 'ideapark_get_required_plugins' ) ) {
	function ideapark_get_required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		return [
			[
				'name'         => esc_html__( 'Foodz Theme Functionality', 'foodz' ),
				'slug'         => 'ideapark-foodz',
				'source'       => IDEAPARK_THEME_DIR . '/plugins/ideapark-foodz.zip',
				'required'     => true,
				'version'      => '1.11',
				'external_url' => '',
				'is_callable'  => '',
			],

			[
				'name'     => esc_html__( 'WooCommerce', 'foodz' ),
				'slug'     => 'woocommerce',
				'required' => true
			],

			[
				'name'     => esc_html__( 'Contact Form 7', 'foodz' ),
				'slug'     => 'contact-form-7',
				'required' => false,
			],

			[
				'name'     => esc_html__( 'Regenerate Thumbnails', 'foodz' ),
				'slug'     => 'regenerate-thumbnails',
				'required' => false
			],

			[
				'name'     => esc_html__( 'MailChimp for WP', 'foodz' ),
				'slug'     => 'mailchimp-for-wp',
				'required' => false
			],

			[
				'name'     => esc_html__( 'Revolution Slider', 'foodz' ),
				'slug'     => 'revslider',
				'source'   => IDEAPARK_THEME_DIR . '/plugins/revslider.zip',
				'version'  => '6.5.8',
				'required' => false,
			],

		];
	}
}

if ( ! function_exists( 'ideapark_register_required_plugins' ) ) {
	function ideapark_register_required_plugins() {
		$plugins = ideapark_get_required_plugins();

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = [
			'id'           => 'foodz',
			// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',
			// Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',
			// Menu slug.
			'parent_slug'  => 'themes.php',
			// Parent menu slug.
			'capability'   => 'edit_theme_options',
			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,
			// Show admin notices or not.
			'dismissable'  => true,
			// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',
			// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,
			// Automatically activate plugins after installation or not.
			'message'      => '',
			// Message to output right before the plugins table.
		];

		tgmpa( $plugins, $config );
	}
}

if ( ! function_exists( 'ideapark_scripts_disable_cf7' ) ) {
	function ideapark_scripts_disable_cf7() {
		if ( ! is_singular() || is_front_page() ) {
			add_filter( 'wpcf7_load_js', '__return_false' );
			add_filter( 'wpcf7_load_css', '__return_false' );
		}
	}
}

if ( ! function_exists( 'ideapark_scripts' ) ) {
	function ideapark_scripts() {

		if ( $GLOBALS['pagenow'] != 'wp-login.php' && ! is_admin() ) {

			if ( ideapark_woocommerce_on() ) {
				if ( ideapark_mod( 'disable_wc_block_styles' ) ) {
					wp_dequeue_style( 'wc-block-style' );
				}
				wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
				wp_dequeue_script( 'prettyPhoto' );
				wp_dequeue_script( 'prettyPhoto-init' );
			}

			ideapark_add_style( 'ideapark-entry-content', IDEAPARK_THEME_URI . '/assets/css/entry-content.css', [], ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/css/entry-content.css' ), 'all' );
			ideapark_add_style( 'ideapark-core', IDEAPARK_THEME_URI . '/style.css', [], ideapark_mtime( IDEAPARK_THEME_DIR . '/style.css' ), 'all' );

			if ( is_rtl() ) {
				ideapark_add_style( 'ideapark-rtl', IDEAPARK_THEME_URI . '/assets/css/rtl.css', [], IDEAPARK_THEME_VERSION . '.1', 'all' );
			}

			ideapark_enqueue_style();

			if ( is_customize_preview() ) {
				wp_enqueue_style( 'ideapark-customize-preview', IDEAPARK_THEME_URI . '/assets/css/admin/admin-customizer-preview.css', [], IDEAPARK_THEME_VERSION . '.1', 'all' );
			}

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply', false, [], false, true );
			}

			ideapark_add_script( 'lazysizes', IDEAPARK_THEME_URI . '/assets/js/lazysizes.min.js', [ 'jquery' ], '1.0', true );
			ideapark_add_script( 'ls-bgset', IDEAPARK_THEME_URI . '/assets/js/ls.bgset.min.js', [ 'jquery' ], '1.0', true );
			ideapark_add_script( 'ls-unveilhooks', IDEAPARK_THEME_URI . '/assets/js/ls.unveilhooks.min.js', [ 'jquery' ], '1.0', true );
			ideapark_add_script( 'owl-carousel', IDEAPARK_THEME_URI . '/assets/js/owl.carousel.min.js', [ 'jquery' ], '2.3.4', true );
			ideapark_add_script( 'fitvids', IDEAPARK_THEME_URI . '/assets/js/jquery.fitvids.min.js', [ 'jquery' ], '1.1', true );
			ideapark_add_script( 'customselect', IDEAPARK_THEME_URI . '/assets/js/jquery.customSelect.min.js', [ 'jquery' ], '0.5.1', true );
			ideapark_add_script( 'simple-parallax', IDEAPARK_THEME_URI . '/assets/js/simpleParallax.min.js', [ 'jquery' ], '5.2.0', true );
			ideapark_add_script( 'body-scroll-lock', IDEAPARK_THEME_URI . '/assets/js/bodyScrollLock.min.js', [ 'jquery' ], '1.0', true );
			ideapark_add_script( 'countdown', IDEAPARK_THEME_URI . '/assets/js/jquery.countdown.min.js', [ 'jquery' ], '2.2.0', true );
			ideapark_add_script( 'requirejs', IDEAPARK_THEME_URI . '/assets/js/requirejs/require.js', [], '2.3.6', true );
			if ( ideapark_woocommerce_on() && ( ideapark_mod( 'product_variations_in_grid' ) || is_product() ) ) {
				add_action( 'wp_print_scripts', function () {
					wc_get_template( 'single-product/add-to-cart/variation.php' );
				}, 5 );
				if ( ideapark_mod( 'product_variations_in_grid_selector' ) == 'radio' ) {
					wp_dequeue_script( 'wc-add-to-cart-variation' );
					$f = 'wp_deregister_' . 'script';
					call_user_func( $f, 'wc-add-to-cart-variation' ); // Replaced to theme modified version
					ideapark_add_script( 'ideapark-add-to-cart-variation', IDEAPARK_THEME_URI . '/assets/js/add-to-cart-variation.js', [
						'jquery',
						'wp-util',
						'jquery-blockui'
					], 'IDEAPARK_THEME_VERSION', true );
				} else {
					wp_enqueue_script( 'wc-add-to-cart-variation' );
				}
			}

			if ( ideapark_woocommerce_on() && ideapark_mod( 'wishlist_page' ) ) {
				ideapark_add_script( 'ideapark-wishlist', IDEAPARK_THEME_URI . '/assets/js/wishlist.js', [ 'jquery' ], IDEAPARK_THEME_VERSION, true );
			}

			ideapark_add_script( 'ideapark-lib', IDEAPARK_THEME_URI . '/assets/js/site-lib.js', [ 'jquery' ], ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/js/site-lib.js' ), true );
			ideapark_add_script( 'ideapark-core', IDEAPARK_THEME_URI . '/assets/js/site.js', [ 'jquery' ], ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/js/site.js' ), true );

			ideapark_enqueue_script();

			if ( ideapark_mod( 'load_jquery_in_footer' ) ) {
				wp_scripts()->add_data( 'jquery', 'group', 1 );
				wp_scripts()->add_data( 'jquery-core', 'group', 1 );
				wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
			}

			wp_localize_script( 'ideapark-core', 'ideapark_wp_vars', ideapark_localize_vars() );
		}
	}
}

if ( ! function_exists( 'ideapark_sprite_loader' ) ) {
	function ideapark_sprite_loader() { ?>
		<script>
			var ideapark_svg_content = "";
			var ajax = new XMLHttpRequest();
			ajax.open("GET", "<?php echo esc_url( ideapark_get_sprite_url() ); ?>", true);
			ajax.send();
			ajax.onload = function (e) {
				ideapark_svg_content = ajax.responseText;
				ideapark_download_svg_onload();
			};

			function ideapark_download_svg_onload() {
				if (typeof document.body != "undefined" && document.body != null && typeof document.body.childNodes != "undefined" && typeof document.body.childNodes[0] != "undefined") {
					var div = document.createElement("div");
					div.className = "svg-sprite-container";
					div.innerHTML = ideapark_svg_content;
					document.body.insertBefore(div, document.body.childNodes[0]);
				} else {
					setTimeout(ideapark_download_svg_onload, 100);
				}
			}

		</script>
	<?php }
}

if ( ! function_exists( 'ideapark_get_sprite_url' ) ) {
	function ideapark_get_sprite_url() {
		return IDEAPARK_THEME_URI . '/assets/img/sprite.svg?v=' . ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/img/sprite.svg' );
	}
}

if ( ! function_exists( 'ideapark_widgets_init' ) ) {
	function ideapark_widgets_init() {

		register_sidebar( [
			'name'          => esc_html__( 'Post/Page Sidebar', 'foodz' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );

		register_sidebar( [
			'name'          => esc_html__( 'Footer', 'foodz' ),
			'id'            => 'footer-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget footer-widget %2$s col-md-3 col-sm-6 col-xs-6">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'description'   => esc_html__( 'Maximum 3 widgets', 'foodz' ),
		] );

		register_sidebar( [
			'name'          => esc_html__( 'Product list (Desktop)', 'foodz' ),
			'id'            => 'shop-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );

		register_sidebar( [
			'name'          => esc_html__( 'Product page', 'foodz' ),
			'id'            => 'product-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );

		register_sidebar( [
			'name'          => esc_html__( 'Product filter (Mobile)', 'foodz' ),
			'id'            => 'filter-sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );

	}
}

if ( ! function_exists( 'ideapark_add_style' ) ) {
	function ideapark_add_style( $handle, $src = '', $deps = [], $ver = false, $media = 'all', $path = '' ) {
		global $ideapark_theme_styles;
		if ( ! array_key_exists( $handle, $ideapark_theme_styles ) ) {
			$ideapark_theme_styles[ $handle ] = [
				'handle' => $handle,
				'src'    => $src,
				'deps'   => $deps,
				'ver'    => $ver,
				'media'  => $media,
				'path'   => $path,
			];
		}
	}
}

if ( ! function_exists( 'ideapark_enqueue_style_hash' ) ) {
	function ideapark_enqueue_style_hash( $styles ) {
		$hash = IDEAPARK_THEME_VERSION . '_' . (string) ideapark_mtime( IDEAPARK_THEME_DIR . '/includes/customize/ip_customize_style.php' ) . '_' . ( IDEAPARK_THEME_DEMO ? 'on' : 'off' );

		if ( ! empty( $styles ) ) {
			foreach ( $styles as $item ) {
				if ( is_array( $item ) ) {
					$hash .= $item['ver'] . '_';
				} else {
					$hash .= (string) ideapark_mtime( IDEAPARK_THEME_DIR . $item ) . '_';
				}
			}
		}

		return $hash ? md5( $hash ) : '';
	}
}

if ( ! function_exists( 'ideapark_editor_style' ) ) {
	function ideapark_editor_style() {

		$screen  = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		$allowed = [ 'page', 'post', 'customize' ];
		if ( is_object( $screen ) && ! empty( $screen->id ) && in_array( $screen->id, $allowed ) ) {
			$styles = [
				'/assets/css/entry-content.css'
			];

			if ( $hash = ideapark_enqueue_style_hash( $styles ) ) {
				if ( ! ideapark_is_dir( IDEAPARK_THEME_UPLOAD_DIR ) ) {
					ideapark_mkdir( IDEAPARK_THEME_UPLOAD_DIR );
				}
				if ( get_option( $option_name = 'ideapark_editor_styles_hash' ) != $hash || ! ideapark_is_file( IDEAPARK_THEME_UPLOAD_DIR . 'editor-styles.min.css' ) ) {
					require_once( IDEAPARK_THEME_DIR . '/includes/lib/cssmin.php' );
					$fonts = [
						ideapark_mod( 'theme_font_1' ),
						ideapark_mod( 'theme_font_2' ),
						ideapark_mod( 'theme_font_3' )
					];

					$google_font_uri = ideapark_get_google_font_uri( $fonts );
					$code            = "@" . "import url('" . esc_url( $google_font_uri ) . "');";
					foreach ( $styles as $style ) {
						$code .= ideapark_fgc( IDEAPARK_THEME_DIR . $style );
					}
					$code .= ideapark_customize_css( true );

					$code = preg_replace( '~\.entry-content[\t\r\n\s]*\{~', 'body {', $code );
					$code = preg_replace( '~\.entry-content[\t\r\n\s]*~', '', $code );
					$code = preg_replace( '~(?<![a-z0-9_-])(button|input\[type=submit\])~i', '\\0:not(.components-button):not([role=presentation]):not(.mce-open)', $code );

					$code .= '
						body {
							background-color: ' . esc_attr( ideapark_mod_hex_color_norm( 'background_color', '#FFFFFF' ) ) . ' !important;
						}
						.editor-post-title {
							padding-left: 0;
							padding-right: 0;
						}
						.editor-post-title__input {
							font-size: 49px;
							line-height: 1.2;
							color: ' . esc_attr( ideapark_mod_hex_color_norm( 'headers_color', 'inherit' ) ) . ';
							font-weight: ' . esc_attr( ideapark_mod( 'theme_font_1_weight' ) ) . ';
							font-family: "' . esc_attr( ideapark_mod( 'theme_font_1' ) ) . '", sans-serif;
							max-width: 600px;
							margin-left:  auto;
							margin-right: auto;
							text-align: center;
						}
						.editor-post-title__block {
							display:flex;
							align-items: center;
						}
						.editor-post-title__block > div {
							flex: 1 1 100%
						}
						';

					if ( ! ideapark_mod( 'sidebar_post' ) ) {
						$code .= '
						*.alignfull, .wp-block[data-align="full"] {
							margin-left:  0 !important;
							margin-right: 0 !important;
							width:        100% !important;
							max-width:    100% !important;
							padding-left: 0;
							padding-right: 0;
						}
						 *.alignwide, .wp-block[data-align="wide"] {
							margin-left:  auto !important;
							margin-right: auto !important;
							width:        100% !important;
							max-width:    914px !important;
						}
						';
					} else {
						$code .= '
						 *.alignfull, *.alignwide, .wp-block[data-align="wide"], .wp-block[data-align="full"] {
							width:        auto !important;
							margin-left:  auto !important;
							margin-right: auto !important;
						}
						';
					}

					$code = CSSMin::compressCSS( $code );

					ideapark_fpc( IDEAPARK_THEME_UPLOAD_DIR . 'editor-styles.min.css', $code );
					if ( get_option( $option_name ) !== null ) {
						update_option( $option_name, $hash );
					} else {
						add_option( $option_name, $hash );
					}
				}
			}

			add_editor_style( IDEAPARK_THEME_UPLOAD_URL . 'editor-styles.min.css' );
		}
	}
}


if ( ! function_exists( 'ideapark_enqueue_style' ) ) {
	function ideapark_enqueue_style() {
		global $ideapark_theme_styles;

		if ( ideapark_mod( 'use_minified_css' ) && ! is_customize_preview() ) {

			$lang_postfix = ideapark_get_lang_postfix();

			if ( $hash = ideapark_enqueue_style_hash( $ideapark_theme_styles ) . $lang_postfix ) {
				if ( ! ideapark_is_dir( IDEAPARK_THEME_UPLOAD_DIR ) ) {
					ideapark_mkdir( IDEAPARK_THEME_UPLOAD_DIR );
				}
				$css_path = IDEAPARK_THEME_UPLOAD_DIR . 'min' . $lang_postfix . '.css';
				$css_url  = IDEAPARK_THEME_UPLOAD_URL . 'min' . $lang_postfix . '.css';
				if ( get_option( $option_name = 'ideapark_styles_hash' . $lang_postfix ) != $hash || ! ideapark_is_file( $css_path ) ) {
					require_once( IDEAPARK_THEME_DIR . '/includes/lib/cssmin.php' );
					$code = "";
					foreach ( $ideapark_theme_styles as $style ) {
						$path = $style['path'] ? $style['path'] : ( IDEAPARK_THEME_DIR . preg_replace( '~^' . preg_quote( IDEAPARK_THEME_URI, '~' ) . '~', '', $style['src'] ) );
						$css  = ideapark_fgc( $path );
						$code .= $css;
					}
					$code .= ideapark_customize_css( true );
					$code = preg_replace( '~\.\./fonts/~', IDEAPARK_THEME_URI . '/fonts/', $code );
					$code = CSSMin::compressCSS( $code );
					ideapark_fpc( $css_path, $code );
					if ( get_option( $option_name ) !== null ) {
						update_option( $option_name, $hash );
					} else {
						add_option( $option_name, $hash );
					}
				}
			}
			wp_enqueue_style( 'ideapark-core', $css_url, [], ideapark_mtime( $css_path ), 'all' );
		} else {
			foreach ( $ideapark_theme_styles as $style ) {
				wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );
			}
			ideapark_customize_css();
		}
	}
}

if ( ! function_exists( 'ideapark_get_lang_postfix' ) ) {
	function ideapark_get_lang_postfix() {
		$lang_postfix = '';
		if ( ( $languages = apply_filters( 'wpml_active_languages', [] ) ) && sizeof( $languages ) >= 2 ) {
			if ( apply_filters( 'wpml_current_language', null ) != apply_filters( 'wpml_default_language', null ) ) {
				$lang_postfix = '_' . apply_filters( 'wpml_current_language', null );
			}
		}

		return $lang_postfix;
	}
}

if ( ! function_exists( 'ideapark_add_script' ) ) {
	function ideapark_add_script( $handle, $src = '', $deps = [], $ver = false, $in_footer = false, $path = '' ) {
		global $ideapark_theme_scripts;
		if ( ! array_key_exists( $handle, $ideapark_theme_scripts ) ) {
			$ideapark_theme_scripts[ $handle ] = [
				'handle'    => $handle,
				'src'       => $src,
				'deps'      => $deps,
				'ver'       => $ver,
				'in_footer' => $in_footer,
				'path'      => $path,
			];
		}
	}
}

if ( ! function_exists( 'ideapark_enqueue_script' ) ) {
	function ideapark_enqueue_script() {
		global $ideapark_theme_scripts;

		$hash = '';

		if ( ideapark_mod( 'use_minified_js' ) ) {
			$deps = [];
			foreach ( $ideapark_theme_scripts as $script ) {
				$hash .= $script['ver'] . '_';
				$deps = array_merge( $deps, $script['deps'] );
			}
			$deps = array_unique( $deps );
			if ( $hash ) {
				$hash = md5( $hash );
				if ( get_option( $option_name = 'ideapark_scripts_hash' ) != $hash || ! ideapark_is_file( IDEAPARK_THEME_UPLOAD_DIR . 'min.js' ) ) {
					require_once( IDEAPARK_THEME_DIR . '/includes/lib/jsmin.php' );
					$code = "";
					foreach ( $ideapark_theme_scripts as $script ) {
						$path        = $script['path'] ? $script['path'] : ( IDEAPARK_THEME_DIR . preg_replace( '~^' . preg_quote( IDEAPARK_THEME_URI, '~' ) . '~', '', $script['src'] ) );
						$script_code = ideapark_fgc( $path );
						$code        .= strpos( $path, '.min' ) !== false ? $script_code : JSMin::minify( $script_code );
					}
					ideapark_fpc( IDEAPARK_THEME_UPLOAD_DIR . 'min.js', $code );
					if ( get_option( $option_name ) !== null ) {
						update_option( $option_name, $hash );
					} else {
						add_option( $option_name, $hash );
					}
				}

				wp_enqueue_script( 'ideapark-core', IDEAPARK_THEME_UPLOAD_URL . 'min.js', $deps, ideapark_mtime( IDEAPARK_THEME_UPLOAD_DIR . 'min.js' ), true );
			}
		}

		if ( ! $hash ) {
			foreach ( $ideapark_theme_scripts as $script ) {
				wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_custom_excerpt_length' ) ) {
	function ideapark_custom_excerpt_length( $length ) {
		return 84;
	}
}

if ( ! function_exists( 'ideapark_excerpt_more' ) ) {
	function ideapark_excerpt_more( $more ) {
		return '&hellip;';
	}
}

if ( ! function_exists( 'ideapark_ajax_search' ) ) {
	function ideapark_ajax_search() {

		if ( ideapark_woocommerce_on() && strlen( ( $s = trim( preg_replace( '~\s\s+~', ' ', $_POST['s'] ) ) ) ) > 0 ) {

			$query_args = [
				's'                   => $s,
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => 'relevance',
				'order'               => 'DESC',
				'fields'              => 'ids',
				'meta_query'          => WC()->query->get_meta_query(),
				'tax_query'           => WC()->query->get_tax_query(),
				'posts_per_page'      => 8,
				'lang'                => isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : ''
			];

			$ordering_args         = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );
			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];
			if ( $ordering_args['meta_key'] ) {
				$query_args['meta_key'] = $ordering_args['meta_key'];
			}

			$query = new WP_Query( $query_args );

			if ( $results = wp_parse_id_list( $query->posts ) ) {
				wc_setup_loop(
					[
						'columns'      => 4,
						'name'         => 'products',
						'is_shortcode' => false,
						'is_search'    => true,
						'is_paginated' => false,
						'total'        => count( $results ),
						'total_pages'  => 1,
						'per_page'     => 8,
						'current_page' => 1,
					]
				);

				ideapark_mod_set_temp( 'product_grid_class', 'c-product-grid__list--ajax-search' );

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					foreach ( $results as $product_id ) {
						$GLOBALS['post'] = get_post( $product_id );
						setup_postdata( $GLOBALS['post'] );
						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end(); ?>
				<div class="c-header-search__view-all">
					<button class="c-form__button js-ajax-search-all"
							type="button"><?php echo esc_html__( 'View all results', 'foodz' ); ?></button>
				</div>
			<?php } else { ?>
				<div class="c-header-search__no-results"><?php echo esc_html__( 'No results found', 'foodz' ); ?></div>
			<?php }

		}
		die();
	}
}

if ( ! function_exists( 'ideapark_category' ) ) {
	function ideapark_category( $separator, $cat = null, $a_calss = '' ) {
		$catetories = [];

		if ( ! $cat ) {
			$cat = get_the_category();
		}
		foreach ( $cat as $category ) {
			$catetories[] = '<a ' . ( $a_calss ? 'class="' . esc_attr( $a_calss ) . '"' : '' ) . ' href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( esc_html__( "View all posts in %s", 'foodz' ), $category->name ) . '" ' . '>' . $category->name . '</a>';
		}

		if ( $catetories ) {
			echo implode( $separator, $catetories );
		}
	}
}

if ( ! function_exists( 'ideapark_corenavi' ) ) {
	function ideapark_corenavi( $custom_query = null ) {
		global $wp_query;

		if ( ! $custom_query ) {
			$custom_query = $wp_query;
		}

		if ( $custom_query->max_num_pages < 2 ) {
			return;
		}

		if ( ! $current = get_query_var( 'paged' ) ) {
			$current = 1;
		}

		$a = [ // WPCS: XSS ok.
			'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
			'add_args'  => false,
			'current'   => $current,
			'total'     => $custom_query->max_num_pages,
			'prev_text' => ideapark_svg( 'arrow-more', 'page-numbers__prev-svg' ),
			'next_text' => ideapark_svg( 'arrow-more', 'page-numbers__next-svg' ),
			'type'      => 'list',
			'end_size'  => 1,
			'mid_size'  => 1,
		];

		$pages = paginate_links( $a );

		echo ideapark_wrap( $pages, '<nav class="page-numbers__wrap">', '</nav>' );
	}
}

if ( ! function_exists( 'ideapark_default_menu' ) ) {
	function ideapark_default_menu() {
		$menu = '';
		$menu .= '<ul class="menu">';

		if ( is_home() ) {
			$menu .= '<li class="current_page_item menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
		} else {
			$menu .= '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
		}

		$menu .= '</ul>';

		return $menu;
	}
}

if ( ! function_exists( 'ideapark_post_nav' ) ) {
	function ideapark_post_nav() {
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}

		?>
		<nav class="c-post__nav" role="navigation">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', '<span class="c-post__nav-meta">' . esc_html__( 'Published In', 'foodz' ) . '</span>%title' );
			else :
				previous_post_link( '<div class="c-post__nav-prev"><div class="c-post__nav-label">' . esc_html__( 'Previous Post', 'foodz' ) . '</div><div class="c-post__nav-title">%link</div></div>' );
				next_post_link( '<div class="c-post__nav-next"><div class="c-post__nav-label">' . esc_html__( 'Next Post', 'foodz' ) . '</div><div class="c-post__nav-title">%link</div></div>' );
			endif;
			?>
		</nav><!-- .navigation -->
		<?php
	}
}

if ( ! function_exists( 'ideapark_html5_comment' ) ) {
	function ideapark_html5_comment( $comment, $args, $depth ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
		?>
		<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" class="comment">
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<header class="comment-meta">
				<div class="comment-author vcard">
					<?php if ( 0 != $args['avatar_size'] ) {
						echo '<div class="author-img">' . get_avatar( $comment, $args['avatar_size'] ) . '</div>';
					} ?>
					<?php printf( '<strong class="author-name">%s</strong>', get_comment_author_link() ); ?>
				</div>

				<div class="comment-metadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID, $args ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>">
							<?php printf( esc_html_x( '%1$s at %2$s', '1: date, 2: time', 'foodz' ), get_comment_date(), get_comment_time() ); ?>
						</time>
					</a>
				</div>

				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'foodz' ); ?></p>
				<?php endif; ?>
			</header>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div>

			<div class="buttons">
				<?php comment_reply_link( array_merge( $args, [
					'reply_text' => ideapark_svg( 'reply', 'reply-svg' ) . esc_html__( 'Reply', 'foodz' ),
					'add_below'  => 'div-comment',
					'depth'      => $depth,
					'max_depth'  => $args['max_depth']
				] ) ); ?>

				<?php edit_comment_link( esc_html__( 'Edit', 'foodz' ), '<span class="edit-link">', '</span>' ); ?>
			</div>

		</article><!-- .comment-body -->
		<?php
	}
}

if ( ! function_exists( 'ideapark_body_class' ) ) {
	function ideapark_body_class( $classes ) {
		$classes[] = ideapark_woocommerce_on() ? 'woocommerce-on' : 'woocommerce-off';

		if ( ideapark_is_wishlist_page() ) {
			$classes[] = 'wishlist-page';
		}

		return $classes;
	}
}

if ( ! function_exists( 'ideapark_empty_menu' ) ) {
	function ideapark_empty_menu() {
	}
}

if ( ! function_exists( 'ideapark_search_form' ) ) {
	function ideapark_search_form( $form ) {
		ob_start();
		do_action( 'wpml_add_language_form_field' );
		$lang = ob_get_clean();
		$form = '<form role="search" method="get" class="js-search-form-entry" action="' . esc_url( home_url( '/' ) ) . '">
				<div class="c-search-form__wrap">
				<label class="c-search-form__label">
					<span class="screen-reader-text">' . esc_html_x( 'Search for:', 'label', 'foodz' ) . '</span>
					<input class="c-form__input c-form__input--full c-form__input--fill" type="search" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder', 'foodz' ) . '" value="' . get_search_query() . '" name="s" />' .
		        ( ideapark_woocommerce_on() ? '<input type="hidden" name="post_type" value="product">' : '' ) .
		        '</label>
				<button type="submit" class="c-form__button c-search-form__button">' . ideapark_svg( 'search', 'c-search-form__svg' ) . '</button>
				</div>' . $lang . '
			</form>';

		return ideapark_wrap( $form, '<div class="c-search-form">', '</div>' );
	}
}

if ( ! function_exists( 'ideapark_search_form_ajax' ) ) {
	function ideapark_search_form_ajax( $form ) {
		ob_start();
		do_action( 'wpml_add_language_form_field' );
		$lang = ob_get_clean();
		$form = '
	<form role="search" class="js-search-form" method="get" action="' . esc_url( home_url( '/' ) ) . '">
		' . ( ideapark_woocommerce_on() ? '<input type="hidden" name="post_type" value="product">' : '' ) . '
		<div class="c-header-search__input-block">
			<input id="ideapark-ajax-search-input" class="h-cb c-header-search__input' . ( ! ideapark_mod( 'ajax_search' ) ? ' c-header-search__input--no-ajax' : '' ) . '" autocomplete="off" type="text" name="s" placeholder="' . esc_attr__( 'Start typing...', 'foodz' ) . '" value="' . esc_attr( ideapark_mod( 'ajax_search' ) ? '' : get_search_query() ) . '" />
			<button id="ideapark-ajax-search-clear" class="h-cb c-header-search__clear' . ( ! ideapark_mod( 'ajax_search' ) ? ' c-header-search__clear--no-ajax' : '' ) . '" type="button">' . ideapark_svg( 'close', 'c-header-search__clear-svg' ) . '<span class="c-header-search__clear-text">' . esc_html__( 'Clear', 'foodz' ) . '</span></button>
			' . ( ! ideapark_mod( 'ajax_search' ) ? '<button type="submit" class="c-header-search__submit h-cb h-cb--svg">' . ideapark_svg( 'search' ) . '</button>' : '' ) . '
		</div>' . $lang . '
	</form>';

		return $form;
	}
}

if ( ! function_exists( 'ideapark_svg_url' ) ) {
	function ideapark_svg_url() {
		return is_customize_preview() ? IDEAPARK_THEME_URI . '/assets/img/sprite.svg' : '';
	}
}

if ( ! function_exists( 'ideapark_get_account_link' ) ) {
	function ideapark_get_account_link( $prefix = '' ) {
		$prefix     = $prefix ? esc_attr( $prefix . '-' ) : '';
		$link_title = ideapark_svg( 'user', 'c-header__' . $prefix . 'auth-svg' ) . ideapark_wrap( is_user_logged_in() ? esc_html__( 'Account', 'foodz' ) : esc_html__( 'Login', 'foodz' ), '<span class="c-header__' . $prefix . 'auth-text">', '</span>' );

		return ideapark_wrap( $link_title, '<a class="c-header__button-link c-header__button-link--account" href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" rel="nofollow">', '</a>' );
	}
}

if ( ! function_exists( 'ideapark_localize_vars' ) ) {
	function ideapark_localize_vars() {
		global $wp_scripts;

		$js_url_imagesloaded   = '';
		$js_url_masonry        = '';
		$js_url_jquery_masonry = '';
		foreach ( $wp_scripts->registered as $handler => $script ) {
			if ( $handler == 'imagesloaded' ) {
				$js_url_imagesloaded = $wp_scripts->base_url . $script->src . ( ! empty( $script->ver ) ? '?v=' . $script->ver : '' );
			}
			if ( $handler == 'masonry' ) {
				$js_url_masonry = $wp_scripts->base_url . $script->src . ( ! empty( $script->ver ) ? '?v=' . $script->ver : '' );
			}
			if ( $handler == 'jquery-masonry' ) {
				$js_url_jquery_masonry = $wp_scripts->base_url . $script->src . ( ! empty( $script->ver ) ? '?v=' . $script->ver : '' );
			}
		}

		$return = [
			'themeDir'             => IDEAPARK_THEME_DIR,
			'themeUri'             => IDEAPARK_THEME_URI,
			'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
			'searchUrl'            => home_url( '?s=' ),
			'svgUrl'               => esc_js( ideapark_svg_url() ),
			'lazyload'             => ideapark_mod( 'lazyload' ),
			'isRtl'                => is_rtl(),
			'searchType'           => ideapark_mod( 'search_type' ),
			'shopProductModal'     => ideapark_mod( 'shop_product_modal' ),
			'stickyMenuDesktop'    => ideapark_mod( 'sticky_menu_desktop' ),
			'stickyMenuMobile'     => ideapark_mod( 'sticky_menu_mobile' ),
			'stickySidebar'        => ideapark_mod( 'sticky_sidebar' ),
			'headerType'           => ideapark_mod( 'header_type' ),
			'productMobileLayout'  => ideapark_mod( 'product_mobile_layout' ),
			'productMobileAjaxATC' => ideapark_mod( 'product_mobile_single_ajax_add_to_cart' ),
			'titleClickExpand'     => ideapark_mod( 'mobile_menu_title_click_expand' ),
			'viewMore'             => esc_html__( 'View More', 'foodz' ),
			'countdownWeek'        => esc_html__( 'week', 'foodz' ),
			'countdownDay'         => esc_html__( 'day', 'foodz' ),
			'countdownHour'        => esc_html__( 'hr', 'foodz' ),
			'countdownMin'         => esc_html__( 'min', 'foodz' ),
			'countdownSec'         => esc_html__( 'sec', 'foodz' ),
			'imagesloadedUrl'      => $js_url_imagesloaded,
			'masonryUrl'           => $js_url_masonry,
			'masonryJQueryUrl'     => $js_url_jquery_masonry,
			'scriptsHash'          => substr( get_option( $option_name = 'ideapark_scripts_hash' ), 0, 8 ),
			'stylesHash'           => substr( get_option( $option_name = 'ideapark_styles_hash' ), 0, 8 ),
		];

		if ( ideapark_woocommerce_on() && ( ideapark_mod( 'product_variations_in_grid' ) || is_product() ) ) {
			$return = array_merge( $return, [
				'wc_add_to_cart_variation_params' =>
					[
						'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
						'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
						'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
						'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
					]
			] );
		}

		if ( ideapark_woocommerce_on() && ideapark_mod( 'wishlist_page' ) ) {
			$return = array_merge( $return, [
				'wishlistCookieName'  => ideapark_wishlist()->cookie_name,
				'wishlistTitleAdd'    => esc_html__( 'Add to Wishlist', 'foodz' ),
				'wishlistTitleRemove' => esc_html__( 'Remove from Wishlist', 'foodz' )
			] );
		}

		return $return;
	}
}

if ( ! function_exists( 'ideapark_disable_background_image' ) ) {
	function ideapark_disable_background_image( $value ) {
		if ( ideapark_mod( 'hide_inner_background' ) && ! is_front_page() && ! is_admin() ) {
			return '';
		} else {
			return $value;
		}
	}
}

if ( ! function_exists( 'ideapark_admin_scripts' ) ) {
	function ideapark_admin_scripts() {
		wp_enqueue_style( 'ideapark-admin', IDEAPARK_THEME_URI . '/assets/css/admin/admin.css', [], ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/css/admin/admin.css' ) );
		wp_enqueue_script( 'ideapark-lib', IDEAPARK_THEME_URI . '/assets/js/site-lib.js', [ 'jquery' ], ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/js/site-lib.js' ), true );
		wp_enqueue_script( 'ideapark-admin-customizer', IDEAPARK_THEME_URI . '/assets/js/admin-customizer.js', [
			'jquery',
			'customize-controls'
		], ideapark_mtime( IDEAPARK_THEME_DIR . '/assets/js/admin-customizer.js' ), true );
		wp_localize_script( 'ideapark-admin-customizer', 'ideapark_dependencies', ideapark_get_theme_dependencies() );
		wp_localize_script( 'ideapark-admin-customizer', 'ideapark_ac_vars', [
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'errorText' => esc_html__( 'Something went wrong...', 'foodz' )
		] );
	}
}

if ( ! function_exists( 'ideapark_exists_theme_addons' ) ) {
	function ideapark_exists_theme_addons() {
		return defined( 'IDEAPARK_THEME_FUNC_VERSION' );
	}
}

if ( ! function_exists( 'ideapark_wrap' ) ) {
	function ideapark_wrap( $str, $before = '', $after = '' ) {
		if ( trim( $str ) != '' ) {
			return sprintf( '%s%s%s', $before, $str, $after );
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'ideapark_style' ) ) {
	function ideapark_style( $str ) {
		if ( is_array( $str ) ) {
			$str = implode( ';', $str );
		}
		if ( trim( $str ) != '' ) {
			echo sprintf( '%s%s%s', ' style' . '="', $str, '" ' );
		} else {
			return;
		}
	}
}

if ( ! function_exists( 'ideapark_bg_image' ) ) {
	function ideapark_bg( $color, $url = '' ) {
		$styles = [];
		$data   = '';
		if ( $color ) {
			$styles[] = sprintf( 'background-color:%s', esc_attr( $color ) );
		}
		if ( trim( $url ) != '' ) {
			if ( ideapark_mod( 'lazyload' ) ) {
				$data = sprintf( 'data-bg="%s"', esc_url( $url ) );
			} else {
				$styles[] = sprintf( 'background-image:url(%s)', esc_url( $url ) );
			}
		}

		return trim( $data . ( $styles ? ' style="' . implode( ';', $styles ) . '"' : '' ) );
	}
}

if ( ! function_exists( 'ideapark_show_customizer_attention' ) ) {
	function ideapark_show_customizer_attention( $type = 'front_page_builder' ) {
		if ( is_customize_preview() ) {
			switch ( $type ) {
				case 'front_page_builder':
					?>
					<div class="container">
						<div class="ip_customizer_attention">
							<span
								class="dashicons dashicons-info"></span> <?php echo wp_kses_data( __( 'Please enable a <b>static page</b> for your homepage and start using <b>Front Page builder</b>', 'foodz' ) ) ?>
							&nbsp;
							<button class="customizer-edit"
									data-control='show_on_front'><?php esc_html_e( 'Enable', 'foodz' ); ?></button>
						</div>
					</div>
					<?php
					break;
			}
		}
	}
}

if ( ! function_exists( 'ideapark_header_metadata' ) ) {
	function ideapark_header_metadata() {

		$fonts = [
			ideapark_mod( 'theme_font_1' ),
			ideapark_mod( 'theme_font_2' ),
			ideapark_mod( 'theme_font_3' )
		];

		$css = ideapark_get_google_font_uri( $fonts );
		?>
		<link rel="stylesheet" href="<?php echo esc_url( $css ); ?>">
		<?php
	}
}

if ( ! function_exists( 'ideapark_init_filesystem' ) ) {
	function ideapark_init_filesystem() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php';
		}
		if ( is_admin() ) {
			$url   = admin_url();
			$creds = false;
			if ( function_exists( 'request_filesystem_credentials' ) ) {
				$creds = @request_filesystem_credentials( $url, '', false, false, [] );
				if ( false === $creds ) {
					return false;
				}
			}
			if ( ! WP_Filesystem( $creds ) ) {
				if ( function_exists( 'request_filesystem_credentials' ) ) {
					@request_filesystem_credentials( $url, '', true, false );
				}

				return false;
			}

			return true;
		} else {
			WP_Filesystem();
		}

		return true;
	}
}

if ( ! function_exists( 'ideapark_fpc' ) ) {
	function ideapark_fpc( $file, $data, $flag = 0 ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return $wp_filesystem->put_contents( $file, ( FILE_APPEND == $flag && $wp_filesystem->exists( $file ) ? $wp_filesystem->get_contents( $file ) : '' ) . $data, false );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'ideapark_fgc' ) ) {
	function ideapark_fgc( $file ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return $wp_filesystem->get_contents( $file );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_is_file' ) ) {
	function ideapark_is_file( $file ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return $wp_filesystem->is_file( $file );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_is_dir' ) ) {
	function ideapark_is_dir( $file ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return $wp_filesystem->is_dir( $file );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_mkdir' ) ) {
	function ideapark_mkdir( $file ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return wp_mkdir_p( $file );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_mtime' ) ) {
	function ideapark_mtime( $file ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return $wp_filesystem->mtime( $file );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ideapark_array_merge' ) ) {
	function ideapark_array_merge( $a1, $a2 ) {
		for ( $i = 1; $i < func_num_args(); $i ++ ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) && count( $arg ) > 0 ) {
				foreach ( $arg as $k => $v ) {
					$a1[ $k ] = $v;
				}
			}
		}

		return $a1;
	}
}

if ( ! function_exists( 'ideapark_ajax_custom_css' ) ) {
	function ideapark_ajax_custom_css() {
		echo ideapark_customize_css( true );
		die();
	}
}

if ( ! function_exists( 'ideapark_correct_tgmpa_mofile' ) ) {
	function ideapark_correct_tgmpa_mofile( $mofile, $domain ) {
		if ( 'tgmpa' !== $domain ) {
			return $mofile;
		}

		return preg_replace( '`/([a-z]{2}_[A-Z]{2}.mo)$`', '/tgmpa-$1', $mofile );
	}
}

if ( ! function_exists( 'ideapark_lazyload_filter' ) ) {
	function ideapark_lazyload_filter( $image ) {
		if ( ! is_admin() && ideapark_mod( 'lazyload' ) && ! preg_match( '~(lazyload|data-src|c-product__gallery-img)~u', $image ) ) {
			$image = preg_replace( '~(src|srcset|sizes)[\s\t]*=~ui', 'data-\\0', $image );
			$image = preg_replace( '~data-src=~ui', 'src="' . ideapark_empty_gif() . '" data-src=', $image );
			$image = preg_replace( '~class[\s\t]*=[\s\t]*[\'"]~ui', '\\0lazyload ', $image );
		}

		return $image;
	}
}

if ( ! function_exists( 'ideapark_get_template_part' ) ) {
	function ideapark_get_template_part( $template, $args = null ) {
		set_query_var( 'ideapark_var', $args );
		get_template_part( $template );
		set_query_var( 'ideapark_var', null );
	}
}

if ( ! function_exists( 'ideapark_af' ) ) {
	function ideapark_af( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		return add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'ideapark_rf' ) ) {
	function ideapark_rf( $tag, $function_to_remove, $priority = 10 ) {
		$f = 'remove_filter';

		return call_user_func( $f, $tag, $function_to_remove, $priority );
	}
}

if ( ! function_exists( 'ideapark_aa' ) ) {
	function ideapark_aa( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		return add_action( $tag, $function_to_add, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'ideapark_ra' ) ) {
	function ideapark_ra( $tag, $function_to_remove, $priority = 10 ) {
		$f = 'remove_action';

		return call_user_func( $f, $tag, $function_to_remove, $priority );
	}
}

if ( ! function_exists( 'ideapark_shortcode' ) ) {
	function ideapark_shortcode( $code ) {
		$f = 'do' . '_shortcode';

		return call_user_func( $f, $code );
	}
}

if ( ! function_exists( 'ideapark_svg' ) ) {
	function ideapark_svg( $name, $class = '', $id = '' ) {
		return '<svg' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . ( $id ? ' id="' . esc_attr( $class ) . '"' : '' ) . '><use xlink:href="' . esc_url( ideapark_svg_url() ) . ( preg_match( '~^svg-~', $name ) ? '#' : '#svg-' ) . esc_attr( $name ) . '" /></svg>';
	}
}

if ( ! function_exists( 'ideapark_menu_item_class' ) ) {
	function ideapark_menu_item_class( $classes, $item, $args, $depth ) {
		global $ideapark_menu_item_depth;
		$ideapark_menu_item_depth = $depth;
		if ( isset( $args->menu_id ) ) {
			if ( preg_match( '~^top-menu~', $args->menu_id ) ) {
				$classes   = array_map( function ( $class ) {
					global $ideapark_menu_item_depth;

					if ( preg_match( '~menu-item-\d+~', $class ) ) {
						return $class;
					} else {
						switch ( $class ) {

							case 'menu-item';
								return ( $ideapark_menu_item_depth > 0 ? 'c-top-menu__subitem' : 'c-top-menu__item' );

							case 'menu-item-has-children';
								return ( $ideapark_menu_item_depth > 0 ? 'c-top-menu__subitem--has-children' : 'c-top-menu__item--has-children' );

							default:
								return '';
						}
					}
				}, $classes );
				$classes[] = 'js-menu-item';

				return array_unique( array_filter( $classes ) );
			} elseif ( preg_match( '~mobile-top-menu~', $args->menu_id ) ) {

				if ( $depth == 0 ) {
					$classes[] = 'c-mega-menu__item--small';
				}

				return array_unique( array_filter( $classes ) );
			}
		}

		return $classes;
	}
}

if ( ! function_exists( 'ideapark_submenu_class' ) ) {
	function ideapark_submenu_class( $classes, $args, $depth ) {

		if ( preg_match( '~^top-menu~', $args->menu_id ) ) {
			$classes = array_map( function ( $class ) {

				switch ( $class ) {

					case 'sub-menu';
						return 'c-top-menu__submenu';

					default:
						return '';
				}
			}, $classes );

			if ( $depth > 0 ) {
				$classes[] = 'c-top-menu__submenu--inner';
			}

			return array_unique( array_filter( $classes ) );
		}

		return $classes;
	}
}

if ( ! function_exists( 'ideapark_menu_item_id' ) ) {
	function ideapark_menu_item_id( $menu_id, $item, $args, $depth ) {
		if ( preg_match( '~^top-menu~', $args->menu_id ) ) {
			return '';
		} else {
			return $menu_id;
		}
	}
}

if ( ! function_exists( 'ideapark_is_requset' ) ) {
	function ideapark_is_requset( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
		}
	}
}

if ( ! function_exists( 'ideapark_class' ) ) {
	function ideapark_class( $cond, $class_yes, $class_no = '' ) {
		echo ideapark_wrap( $cond ? $class_yes : $class_no, ' ', ' ' );
	}
}

if ( ! function_exists( 'ideapark_add_allowed_tags' ) ) {
	function ideapark_add_allowed_tags( $tags, $context_type = '' ) {

		if ( $context_type == 'post' ) {
			$tags['svg'] = [ 'class' => true ];
			$tags['use'] = [ 'xlink:href' => true ];
		}

		return $tags;
	}
}

if ( ! function_exists( 'ideapark_fix_wp_get_attachment_image_svg' ) ) {
	function ideapark_fix_wp_get_attachment_image_svg( $image, $attachment_id, $size, $icon ) {
		if ( is_array( $image ) && preg_match( '/\.svg$/i', $image[0] ) && $image[1] <= 1 ) {
			if ( is_array( $size ) ) {
				$image[1] = $size[0];
				$image[2] = $size[1];
			} elseif ( ( $xml = simplexml_load_string( ideapark_fgc( $image[0] ) ) ) !== false ) {
				$attr     = $xml->attributes();
				$viewbox  = explode( ' ', $attr->viewBox );
				$image[1] = isset( $attr->width ) && preg_match( '/\d+/', $attr->width, $value ) ? (int) $value[0] : ( count( $viewbox ) == 4 ? (int) $viewbox[2] : null );
				$image[2] = isset( $attr->height ) && preg_match( '/\d+/', $attr->height, $value ) ? (int) $value[0] : ( count( $viewbox ) == 4 ? (int) $viewbox[3] : null );
			} else {
				$image[1] = $image[2] = null;
			}
		}

		return $image;
	}
}

if ( ! function_exists( 'ideapark_manu_link_hash_fix' ) ) {
	function ideapark_menu_link_hash_fix( $attr ) {
		global $ideapark_is_front_page;
		if ( ! $ideapark_is_front_page && ! empty( $attr['href'] ) && strpos( $attr['href'], '#' ) === 0 && strlen( $attr['href'] ) > 1 ) {
			$attr['href'] = home_url( '/' ) . $attr['href'];
		}

		return $attr;
	}
}

if ( ! function_exists( 'ideapark_empty_gif' ) ) {
	function ideapark_empty_gif() {
		return 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D';
	}
}

if ( ! function_exists( 'ideapark_check_front_page' ) ) {
	function ideapark_check_front_page() {
		global $ideapark_is_front_page;
		$ideapark_is_front_page = is_front_page();
	}
}

if ( ! function_exists( 'ideapark_pingback_header' ) ) {
	function ideapark_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}
}

if ( ! function_exists( 'ideapark_is_wishlist_page' ) ) {
	function ideapark_is_wishlist_page() {
		global $post;

		return ( is_page() && ideapark_mod( 'wishlist_page' ) && ideapark_mod( 'wishlist_page' ) == $post->ID );
	}
}

if ( ! function_exists( 'ideapark_generator_tag' ) ) {
	function ideapark_generator_tag( $gen, $type ) {
		switch ( $type ) {
			case 'html':
				$gen .= "\n" . '<meta name="generator" content="Foodz ' . esc_attr( IDEAPARK_THEME_VERSION ) . '">';
				break;
			case 'xhtml':
				$gen .= "\n" . '<meta name="generator" content="Foodz ' . esc_attr( IDEAPARK_THEME_VERSION ) . '" />';
				break;
		}

		return $gen;
	}
}

if ( ! function_exists( 'ideapark_get_inline_svg' ) ) {
	function ideapark_get_inline_svg( $attachment_id, $class = '' ) {
		$svg = get_post_meta( $attachment_id, '_ideapark_inline_svg', true );
		if ( empty( $svg ) ) {
			$svg = ideapark_fgc( get_attached_file( $attachment_id ) );
			update_post_meta( $attachment_id, '_ideapark_inline_svg', $svg );
		}

		if ( ! empty( $svg ) ) {
			if ( $class ) {
				if ( preg_match( '~(<svg[^>]+class\s*=\s*[\'"][^\'"]*)([\'"][^>]*>)~i', $svg, $match ) ) {
					$svg = str_replace( $match[1], $match[1] . ' ' . esc_attr( $class ), $svg );
				} else {
					$svg = preg_replace( '~<svg~i', '<svg class="' . esc_attr( $class ) . '"', $svg );
				}
			}
		}

		return $svg;
	}
}

if ( ! function_exists( 'ideapark_disable_block_editor' ) ) {
	function ideapark_disable_block_editor( ) {
		if ( ideapark_mod( 'disable_block_editor' ) ) {
			add_filter( 'gutenberg_use_widgets_block_editor', '__return_false', 100 );
			add_filter( 'use_widgets_block_editor', '__return_false' );
		}
	}
}

if ( IDEAPARK_THEME_DEMO ) {
	add_filter( 'term_links-product_cat', 'ideapark_cut_product_categories', 99, 1 );
}

/*------------------------------------*\
	Actions + Filters
\*------------------------------------*/

if ( IDEAPARK_THEME_IS_AJAX_SEARCH ) {
	add_action( 'wp_ajax_ideapark_ajax_search', 'ideapark_ajax_search' );
	add_action( 'wp_ajax_nopriv_ideapark_ajax_search', 'ideapark_ajax_search' );
} elseif ( IDEAPARK_THEME_IS_AJAX_CSS ) {
	add_action( 'wp_ajax_ideapark_ajax_custom_css', 'ideapark_ajax_custom_css' );
} else {
	add_action( 'wp', 'ideapark_check_front_page' );
	add_action( 'widgets_init', 'ideapark_widgets_init' );
	add_action( 'wp_loaded', 'ideapark_disable_block_editor', 20 );
	add_action( 'after_switch_theme', 'ideapark_set_image_dimensions', 1 );
	add_action( 'admin_init', 'ideapark_set_image_dimensions', 1000 );
	add_action( 'admin_enqueue_scripts', 'ideapark_admin_scripts' );
	add_action( 'wp_enqueue_scripts', 'ideapark_scripts', 99 );
	add_action( 'wp_head', 'ideapark_header_metadata' );
	add_action( 'wp_head', 'ideapark_pingback_header' );
	add_action( 'wp_head', 'ideapark_sprite_loader' );
	add_action( 'current_screen', 'ideapark_editor_style' );
	add_action( 'ideapark_delete_transient', function () {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'" );
	} );

	add_filter( 'body_class', 'ideapark_body_class' );
	add_filter( 'theme_mod_background_image', 'ideapark_disable_background_image', 10, 1 );
	add_filter( 'get_search_form', 'ideapark_search_form', 10 );
	add_filter( 'excerpt_more', 'ideapark_excerpt_more' );
	add_filter( 'excerpt_length', 'ideapark_custom_excerpt_length', 999 );
	add_filter( 'nav_menu_css_class', 'ideapark_menu_item_class', 100, 4 );
	add_filter( 'nav_menu_submenu_css_class', 'ideapark_submenu_class', 100, 3 );
	add_filter( 'nav_menu_item_id', 'ideapark_menu_item_id', 100, 4 );
	add_filter( 'nav_menu_link_attributes', 'ideapark_menu_link_hash_fix' );
	add_filter( 'wp_kses_allowed_html', 'ideapark_add_allowed_tags', 100, 2 );
	add_filter( 'post_thumbnail_html', 'ideapark_lazyload_filter', 10, 1 );
	add_filter( 'wp_get_attachment_image_src', 'ideapark_fix_wp_get_attachment_image_svg', 10, 4 );
	add_filter( 'get_the_generator_html', 'ideapark_generator_tag', 10, 2 );
	add_filter( 'get_the_generator_xhtml', 'ideapark_generator_tag', 10, 2 );
}

add_action( 'after_setup_theme', 'ideapark_init_filesystem', 0 );
add_action( 'after_setup_theme', 'ideapark_check_version', 1 );
add_action( 'after_setup_theme', 'ideapark_setup' );



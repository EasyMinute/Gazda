<?php

defined( 'ABSPATH' ) or die( 'You cannot access this script directly' );

class Ideapark_Importer {

	private $file;
	private $dir;
	private $importer;
	private $importer_dir;
	private $importer_url;
	private $_version;
	private static $_instance = null;
	private $export_path = '';
	private $demo_content_folder = 'data';
	private $slides = [];
	private $options_to_export_page_id = [
		'woocommerce_shop_page_id',
		'woocommerce_cart_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_terms_page_id',
		'page_for_posts',
		'page_on_front'
	];

	private $options_to_export = [
		'show_on_front',
		'posts_per_page',
		'permalink_structure',
		'woocommerce_catalog_columns',
		'woocommerce_catalog_rows',
		'woocommerce_shop_page_display',
		'woocommerce_category_archive_display',
		'woocommerce_enable_myaccount_registration',
		'woocommerce_placeholder_image',
		'woocommerce_currency',
		'woocommerce_currency_pos',
		'woocommerce_price_thousand_sep',
		'woocommerce_price_decimal_sep',
		'woocommerce_price_num_decimals',
		'site_icon',
		'ideapark_added_blocks',
	];

	function __construct( $file, $version = '1.0.0' ) {

		$this->_version     = $version;
		$this->file         = $file;
		$this->dir          = dirname( $this->file );
		$this->importer_dir = trailingslashit( $this->dir ) . 'importer';
		$this->importer_url = trailingslashit( plugins_url( '/importer/', $this->file ) );

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'wp_ajax_ideapark_importer', [ $this, 'importer' ] );
		add_action( 'wp_ajax_ideapark_exporter', [ $this, 'exporter' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
	}

	public static function instance( $file, $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	}

	function admin_menu() {
		add_theme_page( __( 'Import Demo Content', 'ideapark-foodz' ), __( 'Import Demo Content', 'ideapark-foodz' ), 'manage_options', 'ideapark_themes_importer_page', [
			$this,
			'importer_page'
		] );
	}

	public function scripts( $hook ) {
		if ( 'appearance_page_ideapark_themes_importer_page' != $hook ) {
			return;
		}

		wp_enqueue_style( 'ideapark-importer', $this->importer_url . '/importer.css', [], $this->_version, 'all' );
		wp_enqueue_script( 'ideapark-importer', $this->importer_url . '/importer.js', [ 'jquery' ], $this->_version );
		wp_localize_script( 'ideapark-importer', 'ideapark_wp_vars_importer', [
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'please_wait'  => __( 'Please wait...', 'ideapark-foodz' ),
			'are_you_sure' => __( 'Are you sure you want to import?', 'ideapark-foodz' ),
			'importing'    => __( 'Importing ...', 'ideapark-foodz' ),
			'progress'     => __( 'Progress', 'ideapark-foodz' ),
			'output_error' => __( 'Output Error', 'ideapark-foodz' ),
		] );
	}

	public function _sort_importer_page( $a, $b ) {
		if ( $a == $b ) {
			return 0;
		}

		return ( $a < $b ) ? - 1 : 1;
	}

	function importer_page() {

		/* @var WP_Filesystem_Base $wp_filesystem */

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() == 'empty_hostname' ) {
			$output = '';
			$output .= '<div id="ip-import" class="wrap">';
			$output .= '<h1>' . IDEAPARK_THEME_NAME . ' - ' . __( 'One-Click Import Demo Content', 'ideapark-foodz' ) . '</h1>';
			$output .= '<div class="ip-import-block">';
			$output .= 'Error: No access to data folder. Try to add line &quot;<b>define(\'FS_METHOD\',\'direct\');</b>&quot; to the file <b>wp-config.php</b>';
			$output .= '</div>';

			echo ideapark_wrap( $output );

			return;
		}

		$folders = $wp_filesystem->dirlist( $this->importer_dir . '/' );

		$locale       = get_locale();
		$themes       = [];
		$is_revslider = false;
		foreach ( $folders as $name => $folder ) {
			if ( $folder['type'] == 'd' && $wp_filesystem->exists( $theme_title_fn = $this->importer_dir . '/' . $name . '/theme.txt' ) ) {

				if ( preg_match( '~^\w{2}(_\w{2})?$~', $name ) && $name != $locale ) {
					continue;
				}

				$themes[ $name ] = [
					'title' => $wp_filesystem->get_contents( $theme_title_fn )
				];

				if ( $wp_filesystem->exists( $fn = $this->importer_dir . '/' . $name . '/theme_url.txt' ) ) {
					$themes[ $name ]['url'] = $wp_filesystem->get_contents( $fn );
				}

				if ( $wp_filesystem->exists( $fn = $this->importer_dir . '/' . $name . '/theme.jpg' ) ) {
					$themes[ $name ]['screenshot'] = $this->importer_url . '/' . $name . '/theme.jpg?v=' . filemtime( $fn );
				} elseif ( $wp_filesystem->exists( $fn = $this->importer_dir . '/' . $name . '/theme.png' ) ) {
					$themes[ $name ]['screenshot'] = $this->importer_url . '/' . $name . '/theme.png?v=' . filemtime( $fn );
				}

				if ( $wp_filesystem->exists( $fd = $this->importer_dir . '/' . $name . '/revslider/' ) ) {
					$themes[ $name ]['revslider'] = [];
					$sub_folders                  = $wp_filesystem->dirlist( $fd );
					foreach ( $sub_folders as $sub_name => $sub_item ) {
						if ( $sub_item['type'] == 'f' && preg_match( '~\.zip$~', $sub_name ) ) {
							$themes[ $name ]['revslider'][] = $fd . $sub_name;
							$is_revslider                   = true;
						}
					}
				}
			}
		}

		$output = '';
		$output .= '<div id="ip-import" class="wrap">';
		$output .= '<h1>' . IDEAPARK_THEME_NAME . ' - ' . __( 'One-Click Import Demo Content', 'ideapark-foodz' ) . '</h1>';
		$output .= '<div class="ip-import-block">';

		if ( ! empty( $themes ) ) {
			$output   .= '<p><span class="subheader">' . __( 'Select the demo site you want to import: ', 'ideapark-foodz' ) . '</span></p>';
			$output   .= '<ul class="ip-demos">';
			$is_first = true;
			uksort( $themes, [ $this, '_sort_importer_page' ] );
			foreach ( $themes as $name => $theme ) {
				$preview_button = '';
				if ( ! empty( $theme['url'] ) ) {
					$preview_button = '<a class="ip-demo-preview" href="' . esc_url( $theme['url'] ) . '" target="_blank">' . __( 'Preview Demo', 'ideapark-foodz' ) . '</a>';
				}
				if ( ! empty( $theme['screenshot'] ) ) {
					$output .= '<li class="ip-demo" data-revslider="' . ( ! empty( $theme['revslider'] ) ? 'yes' : 'no' ) . '"><label><img class="ip-screenshot" alt="' . esc_attr( $theme['title'] ) . '" src="' . esc_attr( $theme['screenshot'] ) . '" /><input class="ip-import-demo" type="radio" name="import_demo" value="' . esc_attr( $name ) . '" ' . ( $is_first ? 'checked' : '' ) . '/>' . esc_attr( $theme['title'] ) . '</label>' . $preview_button . '</li>';
				} else {
					$output .= '<li class="ip-demo"><label><span class="ip-no-image"></span><input class="ip-import-demo" type="radio" name="import_demo" value="' . esc_attr( $name ) . '`" ' . ( $is_first ? 'checked' : '' ) . '/> ' . esc_attr( $theme['title'] ) . '</label>' . $preview_button . '</li>';
				}

				$is_first = false;
			}
			$output .= '</ul>';
		}

		if ( ! empty( $themes ) || IDEAPARK_THEME_DEMO ) {
			$output .= '<input type="radio" name="import_option" value="all" checked style="opacity: 0"/>';
			$output .= '<p><label><input type="checkbox" value="1" name="import_attachments" checked /> ' . __( 'Import attachments', 'ideapark-foodz' ) . '</label></p>';
			$output .= '<p class="submit"><button class="button button-primary" id="ip-import-submit">' . __( 'Import', 'ideapark-foodz' ) . '</button> ' . ( defined( 'IDEAPARK_THEME_DEMO' ) && IDEAPARK_THEME_DEMO ? '<button class="button button-primary" id="ip-export-submit">' . __( 'Export', 'ideapark-foodz' ) . '</button> ' : '' ) . '</p>';
			$output .= '</div>';
			$output .= '<div class="ip-loading-progress"><div class="ip-loading-bar"><div class="ip-loading-state"></div><div class="ip-loading-info">' . __( 'Progress', 'ideapark-foodz' ) . ': 0%...</div></div><div class="ip-import-output">' . __( 'Prepare data...', 'ideapark-foodz' ) . '</div></div>';
			$output .= '<div class="ip-import-notes">';
			$output .= __( 'Important notes:', 'ideapark-foodz' ) . '<br />';
			$output .= __( 'Please note that import process will take time needed to download all attachments from demo web site.', 'ideapark-foodz' ) . '<br />';
			$output .= __( 'If you plan to use shop, please install WooCommerce before you run import.', 'ideapark-foodz' ) . '<br />';
			$output .= sprintf( wp_kses( __( 'We recommend you to <a href="%s" target="_blank">reset data</a> & clean wp-content/uploads folder before import to prevent duplicate content.', 'ideapark-foodz' ), [ 'a' => [ 'href' => [] ] ] ), esc_url( 'https://wordpress.org/plugins/wp-reset/' ) ) . '<br />';
			$output .= '</div>';
		} else {
			$output .= '<div>' . __( 'No themes data found', 'ideapark-foodz' ) . '</div>';
			$output .= '<div>' . __( 'Try adding this code to the beginning of the file wp-config.php:', 'ideapark-foodz' ) . '</div>';
			$output .= "<p><code>define( 'FS_METHOD', 'direct' );</code></p>";
		}

		echo ideapark_wrap( $output );
	}

	function importer() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

		if ( ! class_exists( 'WP_Importer' ) ) {
			include ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}

		include $this->importer_dir . '/parsers.php';
		include $this->importer_dir . '/wordpress-importer.php';
		include $this->importer_dir . '/wordpress-importer-extend.php';

		if ( ! current_user_can( 'manage_options' ) ) {
			$this->import_response( 'error', __( 'Error: Permission denied', 'ideapark-foodz' ) );
		}

		if ( ! headers_sent() ) {
			if ( ! session_id() ) {
				session_start();
			}
		} else {
			$this->import_response( 'error', __( 'Error: Could not start session! Please try to turn off debug mode and error reporting', 'ideapark-foodz' ) );
		}

		$this->importer = [];
		$code           = 'continue';
		$message        = '';
		$among          = 3;

		if ( isset( $_REQUEST['stage'] ) && $_REQUEST['stage'] == 'start' ) {
			if ( isset( $_REQUEST['import_option'] ) ) {

				if ( ! empty( $_REQUEST['import_demo'] ) ) {
					$this->demo_content_folder = trim( $_REQUEST['import_demo'] );
					if ( ! $wp_filesystem->exists( $theme_title_fn = $this->importer_dir . '/' . $this->demo_content_folder . '/' ) ) {
						$this->import_response( 'error', __( 'Error: Select the demo site you want to import', 'ideapark-foodz' ) );
					}
				}

				switch ( $_REQUEST['import_option'] ) {

					case 'all':
						$this->importer['steps'] = [
							'prepare',
							'terms',
							'post',
							'options',
							'widget',
							'revslider',
							'finish',
							'completed'
						];
						break;

					case 'content':
						$this->importer['steps'] = [
							'prepare',
							'terms',
							'post',
							'finish',
							'completed'
						];
						break;

					case 'options':
						$this->importer['steps'] = [
							'options',
							'completed'
						];
						break;

					case 'widgets':
						$this->importer['steps'] = [
							'widget',
							'completed'
						];
						break;

					case 'revslider':
						$this->importer['steps'] = [
							'revslider',
							'completed'
						];
						break;

					default:
						$this->import_response( 'error', __( 'Error: Select the data you want to import', 'ideapark-foodz' ) );
				}

			} else {
				$this->import_response( 'error', __( 'Error: Select the data you want to import', 'ideapark-foodz' ) );
			}

			$this->importer['base']                       = new Ideapark_Importer_Base();
			$this->importer['base']->step_total           = sizeof( $this->importer['steps'] ) * $among;
			$this->importer['import_attachments']         = ! empty( $_REQUEST['import_attachments'] );
			$this->importer['import_demo_content_folder'] = ! empty( $_REQUEST['import_demo'] ) ? $_REQUEST['import_demo'] : '';

		} else {
			$this->importer                  = unserialize( $_SESSION['ideapark_importer'] );
			$this->importer['base']->message = '';
			$this->demo_content_folder       = $this->importer['import_demo_content_folder'];
		}


		$step = $this->importer['steps'][0];

		if ( empty( $this->importer['steps'][0] ) ) {
			$this->import_response( 'error', __( 'Error: PHP-Session variables not working. Check your PHP settings', 'ideapark-foodz' ) );
		}

		ob_start();

		switch ( $step ) {

			case 'prepare':
				do_action( 'ideapark_before_import_prepare' );
				if ( function_exists( 'ideapark_woocommerce_set_image_dimensions' ) ) {
					ideapark_woocommerce_set_image_dimensions();
				}

				$this->import_options( true );

				if ( function_exists( 'wc_get_page_id' ) ) {
					$ids   = [];
					$ids[] = wc_get_page_id( 'cart' );
					$ids[] = wc_get_page_id( 'checkout' );
					$ids[] = wc_get_page_id( 'myaccount' );
					$ids[] = wc_get_page_id( 'terms' );
					$ids[] = wc_get_page_id( 'shop' );
					$ids   = array_filter( $ids, function ( $val ) {
						return $val > 0;
					} );

					foreach ( $ids as $id ) {
						wp_delete_post( $id );
					}
				}
				foreach ( [ 'hello-world', 'sample-page', 'privacy-policy' ] as $slug ) {
					if ( $defaultPost = get_posts( [
						'name'           => $slug,
						'posts_per_page' => 1,
						'post_type'      => [ 'post', 'page' ],
						'post_status'    => 'any'
					] ) ) {
						wp_delete_post( $defaultPost[0]->ID );
					}
				}

				$this->importer['base']                    = new WP_Importer_Extend();
				$this->importer['base']->fetch_attachments = $this->importer['import_attachments'];
				$this->importer['base']->step_total        += sizeof( $this->importer['steps'] ) * $among;
				$this->importer['base']->placeholder_path  = $this->importer_url . 'img/placeholder.jpg';

				$theme_xml = $this->importer_dir . '/' . $this->demo_content_folder . '/content.xml';
				$this->importer['base']->import_start( $theme_xml );

				array_shift( $this->importer['steps'] );
				$this->importer['base']->step_done = $among;
				$message                           = __( 'Prepared data successfully', 'ideapark-foodz' );
				do_action( 'ideapark_after_import_prepare' );
				break;

			case 'terms':
				do_action( 'ideapark_before_import_terms' );
				$this->importer['base']->import_terms();
				array_shift( $this->importer['steps'] );
				$this->importer['base']->step_done += $among;
				$message                           = __( 'Imported terms successfully', 'ideapark-foodz' );
				do_action( 'ideapark_after_import_terms' );
				break;

			case 'post':
				do_action( 'ideapark_before_import_post' );

				if ( ! $this->importer['base']->importing() ) {
					array_shift( $this->importer['steps'] );
					$message                           = __( 'Imported post data successfully', 'ideapark-foodz' );
					$this->importer['base']->step_done += $among;
				} else {
					$message = $this->importer['base']->message;
				}
				do_action( 'ideapark_after_import_post' );
				break;

			case 'options':
				do_action( 'ideapark_before_import_options' );
				$this->import_options();
				array_shift( $this->importer['steps'] );
				$this->importer['base']->step_done += $among;
				$message                           = __( 'Imported options successfully', 'ideapark-foodz' );
				do_action( 'ideapark_after_import_options' );
				break;

			case 'widget':
				do_action( 'ideapark_before_import_widget' );
				$this->import_widgets();
				array_shift( $this->importer['steps'] );
				$this->importer['base']->step_done += $among;
				$message                           = __( 'Imported widgets successfully', 'ideapark-foodz' );
				do_action( 'ideapark_after_import_widget' );
				break;

			case 'revslider':
				do_action( 'ideapark_before_import_revslider' );
				$message = $this->import_revslider();
				array_shift( $this->importer['steps'] );
				$this->importer['base']->step_done += $among;
				do_action( 'ideapark_after_import_revslider' );
				break;

			case 'finish':
				do_action( 'ideapark_before_import_finish' );
				array_shift( $this->importer['steps'] );
				$this->importer['base']->import_end();
				$this->importer['base']->step_done += $among;
				$this->import_finish();
				do_action( 'ideapark_after_import_finish' );
				break;

			case 'completed':
				do_action( 'ideapark_before_import_completed' );
				$this->importer['base']->step_done = $this->importer['base']->step_total;
				if ( ! count( $this->importer['base']->error_msg ) ) {
					$message = '<b style="color:#444">' . __( 'Cheers! The demo data has been imported successfully! Please reload this page to finish!', 'ideapark-foodz' ) . '</b>';
				} else {
					$message = '<b style="color:#444">' . __( 'Data import completed!', 'ideapark-foodz' ) . '</b><br />' . '<div>' . implode( '', $this->importer['base']->error_msg ) . '</div>';
				}
				$code = 'completed';
				do_action( 'ideapark_after_import_completed' );
				break;

			default:
				$this->import_response( 'error', __( 'Error: step not found: ', 'ideapark-foodz' ) . $step );
				break;
		}

		if ( $output = ob_get_clean() ) {
			$this->importer['base']->error_msg[] = wp_kses( $output, [ 'br' => [] ] );
		}

		/** store state to session */
		$_SESSION['ideapark_importer'] = serialize( $this->importer );

		// calculate processed percent
		$percent = round( ( $this->importer['base']->step_done / $this->importer['base']->step_total ) * 100 );

		/** response to client */
		$this->import_response( $code, $message, $percent );
	}

	function import_finish() {
		global $wp_taxonomies, $wpdb;

		$taxonomy_names = array_keys( $wp_taxonomies );

		foreach ( $taxonomy_names as $taxonomy_name ) {
			$sql = $wpdb->prepare( "
				SELECT term_taxonomy_id
				FROM $wpdb->term_taxonomy
				WHERE taxonomy = %d
			", $taxonomy_name );

			if ( $term_taxonomy_ids = $wpdb->get_col( $sql ) ) {
				wp_update_term_count_now( $term_taxonomy_ids, $taxonomy_name );
			}
		}

		if ( function_exists( 'ideapark_clear_customize_cache' ) ) {
			ideapark_clear_customize_cache();
		}

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'" );
		if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
			if ( ! wc_update_product_lookup_tables_is_running() ) {
				wc_update_product_lookup_tables();
				WC()->queue()->schedule_single(
					time() + 15,
					'ideapark_delete_transient'
				);
			}
		}
		wp_cache_flush();
	}

	function import_options( $is_preliminary = false ) {
		global $wp_filesystem, $wpdb;

		if ( ! $is_preliminary ) {
			$theme_options_fn = $this->importer_dir . '/' . $this->demo_content_folder . '/theme_options.txt';
			if ( $wp_filesystem->exists( $theme_options_fn ) ) {
				$theme_options_txt        = $wp_filesystem->get_contents( $theme_options_fn );
				$options                  = unserialize( base64_decode( $theme_options_txt ) );
				$ideapark_customize_types = $this->_get_customize_types();
				ideapark_reset_theme_mods();

				foreach ( $options as $mod_name => $val ) {
					if ( $mod_name === 'nav_menu_locations' ) {
						$menu_names = [];
						$menus      = wp_get_nav_menus();
						foreach ( $menus as $menu ) {
							$menu_names[ $menu->name ] = $menu->term_id;
						}
						if ( is_array( $val ) ) {
							foreach ( $val as $menu_slug => $menu_name ) {
								if ( array_key_exists( $menu_name, $menu_names ) ) {
									$val[ $menu_slug ] = $menu_names[ $menu_name ];
								}
							}
						}
					} elseif ( preg_match( '~^(home_product_order|home_promo_source)~i', $mod_name ) && preg_match_all( '|\~!(.+)\~\#\~(.+)!\~|Uu', $val, $matches, PREG_SET_ORDER ) ) {
						foreach ( $matches as $match ) {
							if ( $term = get_term_by( 'name', $match[2], $match[1] ) ) {
								$val = str_replace( $match[0], $term->term_id, $val );
							}
						}
					} elseif ( preg_match( '~^home_banners_\d$~i', $mod_name ) && preg_match_all( '~\[\[(.+)\]\]~Uu', $val, $matches, PREG_SET_ORDER ) ) {
						foreach ( $matches as $match ) {
							$page = get_page_by_path( $match[1], OBJECT, 'banner' );
							$val  = str_replace( $match[0], isset( $page->ID ) ? $page->ID : 0, $val );
						}
					} elseif ( array_key_exists( $mod_name, $ideapark_customize_types ) ) {
						if ( $ideapark_customize_types[ $mod_name ] == 'WP_Customize_Image_Control' && strpos( $val, '{{site_url}}' ) !== false ) {
							$val = str_replace( '{{site_url}}', home_url(), $val );
						} elseif ( $ideapark_customize_types[ $mod_name ] == 'WP_Customize_Category_Control' ) {
							$term = get_term_by( 'name', $val, 'category' );
							$val  = isset( $term->term_id ) ? $term->term_id : 0;
						} elseif ( $ideapark_customize_types[ $mod_name ] == 'WP_Customize_Page_Control' ) {
							$page = get_page_by_title( $val );
							$val  = isset( $page->ID ) ? $page->ID : 0;
						}
					}

					if ( is_string( $val ) && preg_match( '~^\[\[([\s\S]+)\]\]$~', $val, $match ) ) {
						$val_orig      = $val;
						$attachment_id = ( $_posts = get_posts( [
							'name'      => $match[1],
							'post_type' => 'attachment'
						] ) ) && sizeof( $_posts ) == 1 ? $_posts[0]->ID : false;
						if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
							$val = wp_get_attachment_url( $attachment_id );
						} else {
							$val = '';
						}
					}

					if ( $mod_name != '0' ) {
						$options[ $mod_name ] = $val;
						set_theme_mod( $mod_name, $val );
					}
				}

				ideapark_fix_theme_mods( true );
			} else {
				$this->import_response( 'error', __( 'Error: file not found: ', 'ideapark-foodz' ) . $theme_options_fn );
			}
		}

		$options_fn  = $this->importer_dir . '/' . $this->demo_content_folder . '/options.txt';
		$options_txt = $wp_filesystem->get_contents( $options_fn );
		$options     = unserialize( base64_decode( $options_txt ) );

		foreach ( $options as $option_name => $val ) {
			if ( $option_name == 'wc_get_attribute_taxonomies' && function_exists( 'wc_get_attribute_taxonomies' ) ) {
				foreach ( $val as $taxonomy ) {
					if ( ! taxonomy_exists( wc_attribute_taxonomy_name( $taxonomy->attribute_name ) ) ) {
						$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', [
							'attribute_name'    => $taxonomy->attribute_name,
							'attribute_label'   => $taxonomy->attribute_label,
							'attribute_type'    => $taxonomy->attribute_type,
							'attribute_orderby' => $taxonomy->attribute_orderby,
							'attribute_public'  => $taxonomy->attribute_public,
						] );
						do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $taxonomy );
						$transient_name       = 'wc_attribute_taxonomies';
						$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies" );
						set_transient( $transient_name, $attribute_taxonomies );
					}
				}
			} elseif ( ! $is_preliminary ) {
				if ( in_array( $option_name, $this->options_to_export_page_id ) ) {
					$page = get_page_by_title( $val );
					$val  = isset( $page->ID ) ? $page->ID : 0;
				}
				update_option( $option_name, $val );
			}
		}

		wp_cache_flush();

		if ( ! $is_preliminary ) {
			delete_option( '_wc_needs_pages' );
			delete_transient( '_wc_activation_redirect' );
			if ( class_exists( 'WC_Admin_Notices' ) ) {
				WC_Admin_Notices::remove_notice( 'template_files' );
				WC_Admin_Notices::remove_notice( 'install' );
			}

			if ( class_exists( 'WooCommerce' ) ) {

				$shop_page_id   = wc_get_page_id( 'shop' );
				$shop_permalink = ( $shop_page_id > 0 && get_post( $shop_page_id ) ) ? get_page_uri( $shop_page_id ) : '';
				if ( $shop_permalink ) {
					$permalinks                 = wc_get_permalink_structure();
					$permalinks['product_base'] = '/' . $shop_permalink;
					update_option( 'woocommerce_permalinks', $permalinks );
					wc_restore_locale();
				}
			}

			flush_rewrite_rules();
			wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
		}
	}

	function import_menu() {

		// Set imported menus to registered theme locations
		$locations = get_theme_mod( 'nav_menu_locations' ); // registered menu locations in theme
		$menus     = wp_get_nav_menus(); // registered menus

		if ( $menus ) {
			foreach ( $menus as $menu ) { // assign menus to theme locations
				if ( $menu->name == 'Main Menu' ) {
					$locations['primary'] = $menu->term_id;
				}
				if ( $menu->name == 'OnePage' ) {
					$locations['onepage'] = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations ); // set menus to locations

	}

	function import_widgets() {
		global $wp_filesystem;

		$widgets_json = $this->importer_dir . '/' . $this->demo_content_folder . '/widgets.txt';
		$widget_data  = $wp_filesystem->get_contents( $widgets_json );
		$this->import_widget_data( $widget_data );
	}

	function import_revslider() {

		/* @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ( $wp_filesystem->exists( $fd = $this->importer_dir . '/' . $this->demo_content_folder . '/revslider/' ) ) {
			$this->slides = [];
			$sub_folders  = $wp_filesystem->dirlist( $fd );
			foreach ( $sub_folders as $sub_name => $sub_item ) {
				if ( $sub_item['type'] == 'f' && preg_match( '~\.zip$~', $sub_name ) ) {
					$this->slides[] = $fd . $sub_name;
				}
			}
		}
		$has_errors = false;

		if ( $this->slides ) {
			if ( class_exists( 'RevSlider' ) ) {


				$slider                         = new RevSlider();
				$updateAnim                     = true;
				$updateStatic                   = 'none';
				$updateNavigation               = true;
				$_FILES['import_file']['error'] = false;


				foreach ( $this->slides as $slide ) {

					$_FILES["import_file"]["tmp_name"] = $slide;

					$response = $slider->importSliderFromPost( $updateAnim, $updateStatic, false, false, false, $updateNavigation );

					$sliderID = intval( $response["sliderID"] );

					//handle error this
					if ( $response["success"] == false ) {
						$this->importer['base']->error_msg[] = $message = $response["error"];
						$has_errors                          = true;
					}
				}

				if ( ! $has_errors ) {
					$message = esc_html__( 'Imported Slider Revolution successfully', 'ideapark-foodz' );
				}

			} else {
				$this->importer['base']->error_msg[] = $message = __( 'The plugin Slider Revolution is not installed', 'ideapark-foodz' );
			}
		} else {
			$message = __( 'There are no slides in this demo', 'ideapark-foodz' );
		}

		return $message;
	}

	function import_response( $code, $message, $percent = 0 ) {
		$response            = [];
		$response['code']    = $code;
		$response['msg']     = $message;
		$response['percent'] = $percent . '%';
		echo json_encode( $response );
		exit;
	}

	function import_widget_data( $widget_data ) {
		$data = unserialize( base64_decode( $widget_data ) );

		$sidebar_data = $data[0];
		$widget_data  = $data[1];

		$menus  = wp_get_nav_menus();
		$new_wg = [];

		foreach ( $widget_data as $key => $tp_widgets ) {
			if ( $key == 'nav_menu' ) {
				foreach ( $tp_widgets as $key => $tp_widget ) {
					foreach ( $menus as $menu ) {
						if ( $tp_widget['nav_menu'] == $menu->name ) {
							$tp_widget['nav_menu'] = $menu->term_id;
							break;
						}
					}
					$new_wg[ $key ] = $tp_widget;
				}
				$widget_data['nav_menu'] = $new_wg;
			} elseif ( $key == 'ip_woocommerce_color_filter' ) {
				foreach ( $tp_widgets as $key => $val ) {
					if ( ! empty( $val['colors'] ) ) {
						$a = [];
						foreach ( $val['colors'] as $color_key => $color_val ) {
							if ( $term = get_term_by( 'name', $color_key, 'pa_color' ) ) {
								$a[ $term->term_id ] = $color_val;
							}
						}
						$tp_widgets[ $key ]['colors'] = $a;
					}
				}
				$widget_data['ip_woocommerce_color_filter'] = $tp_widgets;
			}

		}

		foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
			$widgets[ $widget_data_title ] = [];
			foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
				if ( is_int( $widget_data_key ) ) {
					$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
				}
			}
		}
		unset( $widgets[""] );

		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i ++ ) {
				$widget               = [];
				$widget['type']       = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
				if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
					unset( $sidebar_data[ $title ][ $i ] );
				}
			}
			$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
		}

		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
				$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
			}
		}

		$sidebar_data = [ array_filter( $sidebar_data ), $widgets ];

		$this->parse_import_data( $sidebar_data );
	}

	function parse_import_data( $import_array, $is_allow_clones = false ) {
		global $wp_registered_sidebars;
		$sidebars_data    = $import_array[0];
		$widget_data      = $import_array[1];
		$current_sidebars = $is_allow_clones ? get_option( 'sidebars_widgets' ) : [];
		$new_widgets      = [];

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			foreach ( $import_widgets as $import_widget ) :

				if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) :
					$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );

					if ( $is_allow_clones ) {
						$new_widget_name = $this->get_new_widget_name( $title, $index );
						$new_index       = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );
						if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
							while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
								$new_index ++;
							}
						}
					} else {
						$new_index = $index;
					}

					if ( array_key_exists( $import_sidebar, $current_sidebars ) ) {
						if ( $is_allow_clones || ! is_array( $current_sidebars[ $import_sidebar ] ) || ! in_array( $title . '-' . $new_index, $current_sidebars[ $import_sidebar ] ) ) {
							$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
							if ( array_key_exists( $title, $new_widgets ) ) {
								$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
							} else {
								$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];

								$current_multiwidget = isset( $current_widget_data['_multiwidget'] ) ? $current_widget_data['_multiwidget'] : '';
								$new_multiwidget     = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
								$multiwidget         = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
								unset( $current_widget_data['_multiwidget'] );
								$current_widget_data['_multiwidget'] = $multiwidget;
								$new_widgets[ $title ]               = $current_widget_data;
							}
						} elseif ( in_array( $title . '-' . $new_index, $current_sidebars[ $import_sidebar ] ) ) {
							$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
						}
					} elseif ( array_key_exists( $import_sidebar, $wp_registered_sidebars ) ) {
						$current_sidebars[ $import_sidebar ] = [ $title . '-' . $new_index ];
						if ( array_key_exists( $title, $new_widgets ) ) {
							$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
						} else {
							$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];

							$current_multiwidget = isset( $current_widget_data['_multiwidget'] ) ? $current_widget_data['_multiwidget'] : '';
							$new_multiwidget     = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
							$multiwidget         = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
							unset( $current_widget_data['_multiwidget'] );
							$current_widget_data['_multiwidget'] = $multiwidget;
							$new_widgets[ $title ]               = $current_widget_data;
						}
					}

				endif;
			endforeach;
		endforeach;

		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content ) {
				update_option( 'widget_' . $title, $content );
			}

			return true;
		}

		return false;
	}

	function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = [];
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( ! empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index ++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;

		return $new_widget_name;
	}

	function exporter() {
		global $wp_filesystem, $wpdb;

		$result = $wpdb->get_col( $s = "SELECT ID FROM {$wpdb->posts} WHERE post_title = 'woocommerce_update_marketplace_suggestions'" );

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! function_exists( 'export_wp' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/export.php' );
		}

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

		if ( ! class_exists( 'WP_Importer' ) ) {
			include ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}

		include $this->importer_dir . '/wordpress-importer.php';

		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'export' ) ) {
			$this->import_response( 'error', __( 'Error: Permission denied', 'ideapark-foodz' ) );
		}

		$this->export_path = $this->importer_dir . "/" . $this->demo_content_folder . "/";

		if ( ! $wp_filesystem->is_dir( $this->export_path ) ) {
			if ( ! $wp_filesystem->mkdir( $this->export_path, 0755 ) ) {
				$this->import_response( 'error', __( 'Error: Permission denied', 'ideapark-foodz' ) . ': ' . $this->export_path );
			}
		}

		if ( $count = $wpdb->get_var( $s = "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_title = 'woocommerce_update_marketplace_suggestions'" ) ) {
			$result = $wpdb->get_col( $s = "SELECT ID FROM {$wpdb->posts} WHERE post_title = 'woocommerce_update_marketplace_suggestions' LIMIT 100" );

			foreach ( $result as $_post_id ) {
				wp_delete_post( $_post_id, true );
			}

			$this->import_response( 'continue', 'Deleted: ' . sizeof( $result ) . ' from ' . $count, round( sizeof( $result ) / $count * 100 ) );
		}

		$this->export_options();
		$this->export_content();
		$this->export_widgets();

		$code    = 'completed';
		$message = '<b style="color:#444">' . __( 'The demo data has been exported successfully!', 'ideapark-foodz' ) . '</b>';

		$this->import_response( $code, $message, 100 );
	}

	private
	function _get_customize_types() {
		global $ideapark_customize;

		$ideapark_customize_types = [];
		foreach ( $ideapark_customize as $group ) {
			foreach ( $group['controls'] as $mod_name => $mod ) {
				$ideapark_customize_types[ $mod_name ] = isset( $mod['class'] ) ? $mod['class'] : ( isset( $mod['type'] ) ? $mod['type'] : null );
			}
		}

		return $ideapark_customize_types;
	}

	function export_options() {
		global $wp_filesystem, $ideapark_customize_mods;

//		$theme_title_fn = $this->export_path . "theme.txt";
//		$wp_filesystem->put_contents( $theme_title_fn, get_bloginfo( 'name' ) );

		$theme_title_fn = $this->export_path . "theme_url.txt";
		$wp_filesystem->put_contents( $theme_title_fn, get_site_url() . '/' );

//		$image = wp_get_image_editor( get_template_directory() . '/screenshot.png' );
//		if ( ! is_wp_error( $image ) ) {
//			$image->resize( 300, '' );
//			$image->save( $this->export_path . 'theme.png' );
//		}

		$ideapark_customize_types = $this->_get_customize_types();

		ideapark_init_theme_mods();
		$options = $ideapark_customize_mods;

		foreach ( $options as $mod_name => $val ) {
			if ( array_key_exists( $mod_name, $ideapark_customize_types ) && $ideapark_customize_types[ $mod_name ] == 'WP_Customize_Image_Control' ) {
				if ( $attachment_id = attachment_url_to_postid( $val ) ) {
					$options[ $mod_name ] = '[[' . get_post_field( 'post_name', get_post( $attachment_id ) ) . ']]';
				}
			} elseif ( array_key_exists( $mod_name, $ideapark_customize_types ) && $ideapark_customize_types[ $mod_name ] == 'WP_Customize_Category_Control' ) {
				$options[ $mod_name ] = get_cat_name( $val );
			} elseif ( array_key_exists( $mod_name, $ideapark_customize_types ) && $ideapark_customize_types[ $mod_name ] == 'WP_Customize_Page_Control' ) {
				$options[ $mod_name ] = get_the_title( $val );
			} elseif ( preg_match( '~_post_id$~i', $mod_name ) ) {
				$options[ $mod_name ] = get_the_title( $val );
			} elseif ( preg_match( '~^home_product_order~i', $mod_name ) ) {

				if ( $list = ideapark_parse_checklist( $val ) ) {
					$new_list = [];
					foreach ( $list as $tab => $status ) {
						if ( ( $cat_id = absint( $tab ) ) && ( $cat = get_term_by( 'id', $cat_id, 'product_cat', 'ARRAY_A' ) ) ) {
							$tab = '~!' . $cat['taxonomy'] . '~#~' . $cat['name'] . '!~';
						}

						$new_list[] = $tab . '=' . $status;
					}
					$val = implode( '|', $new_list );
				}
				$options[ $mod_name ] = $val;
			} elseif ( preg_match( '~^home_promo_source~i', $mod_name ) ) {
				$options[ $mod_name ] = ( preg_match( '~^\d+$~', $val ) && ( $cat_id = absint( $val ) ) && ( $cat = get_term_by( 'id', $cat_id, 'product_cat', 'ARRAY_A' ) ) ) ? '~!' . $cat['taxonomy'] . '~#~' . $cat['name'] . '!~' : $val;
			} elseif ( preg_match( '~^home_banners_\d$~i', $mod_name ) ) {

				if ( $list = ideapark_parse_checklist( $val ) ) {
					$new_list = [];
					foreach ( $list as $attachment_id => $status ) {
						$tab        = '[[' . get_post_field( 'post_name', get_post( $attachment_id ) ) . ']]';
						$new_list[] = $tab . '=' . $status;
					}
					$val = implode( '|', $new_list );
				}
				$options[ $mod_name ] = $val;
			} elseif ( is_string( $val ) && preg_match( '~^' . preg_quote( home_url(), '~' ) . '~', $val, $match ) ) {
				if ( $attachment_id = attachment_url_to_postid( $val ) ) {
					$options[ $mod_name ] = '[[' . get_post_field( 'post_name', get_post( $attachment_id ) ) . ']]';
				}
			}
		}

		$menu_names = [];
		$menus      = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			$menu_names[ $menu->term_id ] = $menu->name;
		}

		if ( $menus = get_theme_mod( 'nav_menu_locations' ) ) {
			foreach ( $menus as $menu_slug => $menu_id ) {
				$menus[ $menu_slug ] = $menu_names[ $menu_id ];
			}
			$options['nav_menu_locations'] = $menus;
		}

		$options_fn = $this->export_path . "theme_options.txt";

		if ( $wp_filesystem->exists( $options_fn ) ) {
			$wp_filesystem->delete( $options_fn );
		}

		$wp_filesystem->put_contents( $options_fn, base64_encode( serialize( $options ) ) );

//		if ( IDEAPARK_THEME_DEMO ) {
//			$wp_filesystem->put_contents( $options_fn . '.txt', serialize( $options ) );
//		}

		$options = [];

		foreach ( $this->options_to_export_page_id as $option_name ) {
			$post                    = get_post( (int) get_option( $option_name ) );
			$options[ $option_name ] = $post->post_title;
		}

		foreach ( $this->options_to_export as $option_name ) {
			$options[ $option_name ] = get_option( $option_name );
		}

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$options['wc_get_attribute_taxonomies'] = wc_get_attribute_taxonomies();
		}

		$options_fn = $this->export_path . "options.txt";

		if ( $wp_filesystem->exists( $options_fn ) ) {
			$wp_filesystem->delete( $options_fn );
		}

		$wp_filesystem->put_contents( $options_fn, base64_encode( serialize( $options ) ) );
	}

	function export_content() {
		global $wp_filesystem;

		$args = [ 'content' => 'all' ];
		ob_start();
		export_wp( $args );

		$content_fn = $this->export_path . "content.xml";
		if ( $wp_filesystem->exists( $content_fn ) ) {
			$wp_filesystem->delete( $content_fn );
		}

		$wp_filesystem->put_contents( $content_fn, preg_replace( '#[\x00-\x08\x0B-\x0C\x0E-\x1F]+#is', ' ', ob_get_clean() ) );

		if ( ! headers_sent() ) {
			header_remove( 'Content-Description' );
			header_remove( 'Content-Disposition' );
			header_remove( 'Content-Type' );
			header( 'Content-Type:text/html; charset=UTF-8' );
		}
	}

	function export_widgets() {
		global $wp_filesystem;

		$sidebars_array = get_option( 'sidebars_widgets' );
		$sidebar_export = [];
		$posted_array   = [];
		foreach ( $sidebars_array as $sidebar => $widgets ) {
			if ( ! empty( $widgets ) && is_array( $widgets ) ) {
				foreach ( $widgets as $sidebar_widget ) {
					if ( $sidebar != 'wp_inactive_widgets' ) {
						$sidebar_export[ $sidebar ][] = $sidebar_widget;
						$posted_array[]               = $sidebar_widget;
					}
				}
			}
		}
		$widgets = [];
		foreach ( $posted_array as $k ) {
			$widget                = [];
			$widget['type']        = trim( substr( $k, 0, strrpos( $k, '-' ) ) );
			$widget['type-index']  = trim( substr( $k, strrpos( $k, '-' ) + 1 ) );
			$widget['export_flag'] = true;
			$widgets[]             = $widget;
		}

		$menus = wp_get_nav_menus();

		$widgets_array = [];
		foreach ( $widgets as $widget ) {
			$widget_val = get_option( 'widget_' . $widget['type'] );
			$widget_val = apply_filters( 'widget_data_export', $widget_val, $widget['type'] );

			if ( $widget['type'] == 'nav_menu' ) {
				foreach ( $widget_val as $key => $val ) {
					foreach ( $menus as $menu ) {
						if ( $val['nav_menu'] == $menu->term_id ) {
							$widget_val[ $key ]['nav_menu'] = $menu->name;
							break;
						}
					}
				}
			} elseif ( $widget['type'] == 'ip_woocommerce_color_filter' ) {
				foreach ( $widget_val as $key => $val ) {
					if ( ! empty( $val['colors'] ) ) {
						$a = [];
						foreach ( $val['colors'] as $color_key => $color_val ) {
							if ( $term = get_term_by( 'term_taxonomy_id', $color_key, 'pa_color' ) ) {
								$a[ $term->name ] = $color_val;
							}
						}
						$widget_val[ $key ]['colors'] = $a;
					}
				}
			}

			$multiwidget_val                                           = $widget_val['_multiwidget'];
			$widgets_array[ $widget['type'] ][ $widget['type-index'] ] = $widget_val[ $widget['type-index'] ];
			if ( isset( $widgets_array[ $widget['type'] ]['_multiwidget'] ) ) {
				unset( $widgets_array[ $widget['type'] ]['_multiwidget'] );
			}
			$widgets_array[ $widget['type'] ]['_multiwidget'] = $multiwidget_val;
		}
		unset( $widgets_array['export'] );
		$export_array = [ $sidebar_export, $widgets_array ];

		$options_fn = $this->export_path . "widgets.txt";

		if ( $wp_filesystem->exists( $options_fn ) ) {
			$wp_filesystem->delete( $options_fn );
		}

		$wp_filesystem->put_contents( $options_fn, base64_encode( serialize( $export_array ) ) );
	}
}

class Ideapark_Importer_Base {
	var $message = '';
	var $step_total = 0;
	var $step_done = 0;
	var $error_msg = [];
}
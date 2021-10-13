<?php
/*
 * Plugin Name: Foodz Theme Functionality
 * Version: 1.11
 * Description: Banners, Brands and Testimonials plugin, Widgets, One-Click Demo Import and other functionality for the Foodz theme.
 * Author: parkofideas.com
 * Author URI: http://parkofideas.com
 * Text Domain: ideapark-foodz
 * Domain Path: /lang/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IDEAPARK_THEME_FUNC_VERSION', '1.11' );

$theme_obj = wp_get_theme();

if ( empty( $theme_obj ) || strtolower( $theme_obj->get( 'Name' ) ) != 'foodz' && strtolower( $theme_obj->get( 'Name' ) ) != 'foodz-child' ) {

	function ideapark_foodz_wrong_theme( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			$row_meta = [
				'warning' => '<b>' . esc_html__( 'This plugin works only with Foodz theme', 'ideapark-foodz' ) . '</b>',
			];

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	add_filter( 'plugin_row_meta', 'ideapark_foodz_wrong_theme', 10, 2 );

	return;
}

$ip_dir = dirname( __FILE__ );

require_once( $ip_dir . '/importer/importer.php' );
require_once( $ip_dir . '/includes/class-ideapark-foodz.php' );
require_once( $ip_dir . '/includes/lib/class-ideapark-foodz-admin-api.php' );
require_once( $ip_dir . '/includes/lib/class-ideapark-foodz-post-type.php' );

/**
 * Returns the main instance of ideapark_foodz to prevent the need to use globals.
 *
 * @return object ideapark_foodz
 */
function Ideapark_foodz() {
	$instance = Ideapark_foodz::instance( __FILE__, IDEAPARK_THEME_FUNC_VERSION );

	return $instance;
}

function Ideapark_Importer() {
	$instance = Ideapark_Importer::instance( __FILE__, IDEAPARK_THEME_FUNC_VERSION );

	return $instance;
}

Ideapark_Importer();

add_action( 'widgets_init', 'ideapark_foodz_widgets_init' );

function ideapark_foodz_widgets_init() {
	$ip_dir = dirname( __FILE__ );
	include_once( $ip_dir . "/widgets/latest-posts-widget.php" );
	include_once( $ip_dir . "/widgets/advantages-widget.php" );
}

add_action( 'after_setup_theme', 'ideapark_foodz_init_custom_post_types' );

function ideapark_foodz_init_custom_post_types() {

	Ideapark_foodz()->register_post_type(
		'banner',
		esc_html__( 'Banners', 'ideapark-foodz' ),
		esc_html__( 'Banner', 'ideapark-foodz' ),
		'Home Page Banners',
		[
			'menu_icon'           => 'dashicons-images-alt2',
			'public'              => true,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 4,
			'capability_type'     => 'post',
			'supports'            => [ 'title', 'thumbnail' ],
			'has_archive'         => true,
			'query_var'           => false,
			'can_export'          => true,
		] );

	Ideapark_foodz()->register_post_type(
		'brand',
		esc_html__( 'Brands', 'ideapark-foodz' ),
		esc_html__( 'Brand', 'ideapark-foodz' ),
		'Home Page Brands',
		[
			'menu_icon'           => 'dashicons-images-alt',
			'public'              => true,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 4,
			'capability_type'     => 'post',
			'supports'            => [ 'title', 'thumbnail' ],
			'has_archive'         => true,
			'query_var'           => false,
			'can_export'          => true,
		] );

	Ideapark_foodz()->register_post_type(
		'review',
		esc_html__( 'Testimonials', 'ideapark-foodz' ),
		esc_html__( 'Testimonial', 'ideapark-foodz' ),
		'Home Page Testimonials',
		[
			'menu_icon'           => 'dashicons-editor-quote',
			'public'              => true,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 4,
			'capability_type'     => 'post',
			'supports'            => [ 'title', 'thumbnail', 'excerpt' ],
			'has_archive'         => true,
			'query_var'           => false,
			'can_export'          => true,
		] );


	Ideapark_foodz()->set_sorted_post_types( [ 'banner', 'brand', 'review' ] );
}

add_action( 'add_meta_boxes', 'ideapark_foodz_add_meta_box' );

function ideapark_foodz_add_meta_box() {
	Ideapark_foodz()->admin->add_meta_box( 'ideapark_metabox_banner_details', esc_html__( 'Banner details', 'ideapark-foodz' ), [ "banner" ] );
	Ideapark_foodz()->admin->add_meta_box( 'ideapark_metabox_brand_details', esc_html__( 'Brand details', 'ideapark-foodz' ), [ "brand" ] );
	Ideapark_foodz()->admin->add_meta_box( 'ideapark_metabox_review_details', esc_html__( 'Testimonial details', 'ideapark-foodz' ), [ "review" ] );
}

add_filter( "banner_custom_fields", "ideapark_home_banner_add_custom_fields" );

function ideapark_home_banner_add_custom_fields() {
	$fields = [];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_hide_title",
		'label'   => esc_html__( 'Hide Title', 'ideapark-foodz' ),
		'type'    => 'checkbox',
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_subheader",
		'label'   => esc_html__( 'Subheader', 'ideapark-foodz' ),
		'type'    => 'text',
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_shortcode",
		'label'   => esc_html__( 'Shortcode', 'ideapark-foodz' ),
		'type'    => 'text_secret',
		'default' => ''
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_shortcode_placement",
		'label'   => esc_html__( 'Shortcode placement', 'ideapark-foodz' ),
		'type'    => 'radio',
		'options' => [
			'above' => esc_html__( 'Above of the text', 'ideapark-foodz' ),
			'below' => esc_html__( 'Below of the text', 'ideapark-foodz' ),
		],
		'default' => 'above'
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_button_text",
		'label'   => esc_html__( 'Button Text', 'ideapark-foodz' ),
		'type'    => 'text',
		'default' => 'Shop Now'
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_button_link",
		'label'   => esc_html__( 'Button/Banner Link', 'ideapark-foodz' ),
		'type'    => 'url',
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_color",
		'label'   => esc_html__( 'Text Color', 'ideapark-foodz' ),
		'type'    => 'color',
		'default' => ''
	];

	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_banner_details"
		],
		'id'      => "_ip_banner_background_color",
		'label'   => esc_html__( 'Background Color', 'ideapark-foodz' ),
		'type'    => 'color',
		'default' => ''
	];

	return $fields;
}

add_filter( "brand_custom_fields", "ideapark_home_brand_add_custom_fields" );

function ideapark_home_brand_add_custom_fields() {
	$fields   = [];
	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_brand_details"
		],
		'id'      => "_ip_brand_link",
		'label'   => esc_html__( 'Link', 'ideapark-foodz' ),
		'type'    => 'url',
	];

	return $fields;
}

add_filter( "review_custom_fields", "ideapark_home_review_add_custom_fields" );

function ideapark_home_review_add_custom_fields() {
	$fields   = [];
	$fields[] = [
		"metabox" => [
			'name' => "ideapark_metabox_review_details"
		],
		'id'      => "_ip_review_occupation",
		'label'   => esc_html__( 'Occupation', 'ideapark-foodz' ),
		'type'    => 'text',
	];

	return $fields;
}

add_filter( 'manage_banner_posts_columns', 'ideapark_add_img_column' );
add_filter( 'manage_brand_posts_columns', 'ideapark_add_img_column' );

function ideapark_add_img_column( $columns ) {
	$columns['img'] = esc_html__( 'Featured Image', 'ideapark-foodz' );

	return $columns;
}

add_filter( 'manage_posts_custom_column', 'ideapark_manage_img_column', 10, 2 );

function ideapark_manage_img_column( $column_name, $post_id ) {
	if ( $column_name == 'img' ) {
		echo get_the_post_thumbnail( $post_id, 'thumbnail' );
	}
}

add_action( 'woocommerce_product_options_advanced', 'ideapark_woo_add_custom_advanced_fields' );

function ideapark_woo_add_custom_advanced_fields() {
	echo '<div class="options_group">';
	woocommerce_wp_text_input(
		[
			'id'          => '_ip_product_video_url',
			'label'       => esc_html__( 'Video URL', 'ideapark-foodz' ),
			'placeholder' => 'http://',
			'desc_tip'    => 'true',
			'description' => esc_html__( 'Enter the url to product video (Youtube, Vimeo etc.).', 'ideapark-foodz' )
		]
	);
	echo '</div>';

	echo '<div class="options_group">';
	woocommerce_wp_text_input(
		[
			'id'          => '_ip_product_extra_info_title',
			'label'       => esc_html__( 'Extra info title', 'ideapark-foodz' ),
			'placeholder' => esc_html__( 'Nutritional facts', 'ideapark-foodz' ),
		]
	);
	woocommerce_wp_textarea_input(
		[
			'id'    => '_ip_product_extra_info',
			'label' => esc_html__( 'Extra info text', 'ideapark-foodz' ),
		]
	);
	echo '</div>';
}

add_action( 'woocommerce_process_product_meta', 'ideapark_woo_add_custom_general_fields_save' );

function ideapark_woo_add_custom_general_fields_save( $post_id ) {

	if ( ! empty( $_POST['_ip_product_video_url'] ) ) {
		update_post_meta( $post_id, '_ip_product_video_url', esc_attr( $_POST['_ip_product_video_url'] ) );
	}

	if ( ! empty( $_POST['_ip_product_extra_info_title'] ) ) {
		update_post_meta( $post_id, '_ip_product_extra_info_title', $_POST['_ip_product_extra_info_title'] );
	}

	if ( ! empty( $_POST['_ip_product_extra_info'] ) ) {
		update_post_meta( $post_id, '_ip_product_extra_info', $_POST['_ip_product_extra_info'] );
	}

}

add_filter( 'user_contactmethods', 'ideapark_contactmethods', 10, 1 );

function ideapark_contactmethods( $contactmethods ) {
	global $ideapark_customize;

	$is_founded = false;

	if ( ! empty( $ideapark_customize ) ) {
		foreach ( $ideapark_customize AS $section ) {
			if ( ! empty( $section['controls'] ) && array_key_exists( 'facebook', $section['controls'] ) ) {
				foreach ( $section['controls'] AS $control_name => $control ) {
					if ( strpos( $control_name, 'soc_' ) === false ) {
						$contactmethods[ $control_name ] = $control['label'];
					}
				}
				$is_founded = true;
			}
		}
	}

	if ( ! $is_founded ) {
		$contactmethods['facebook']  = esc_html__( 'Facebook url', 'ideapark-foodz' );
		$contactmethods['instagram'] = esc_html__( 'Instagram url', 'ideapark-foodz' );
		$contactmethods['twitter']   = esc_html__( 'Twitter url', 'ideapark-foodz' );
		$contactmethods['tumblr']    = esc_html__( 'Tumblr url', 'ideapark-foodz' );
		$contactmethods['pinterest'] = esc_html__( 'Pinterest url', 'ideapark-foodz' );
	}

	return $contactmethods;
}


add_shortcode( 'ip-countdown', 'ideapark_shortcode_countdown' );

function ideapark_shortcode_countdown( $atts ) {
	$div_attr = [];
	foreach ( $atts AS $key => $val ) {
		$div_attr[] = 'data-' . esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
	}
	$content = $div_attr ? '<div class="c-countdown js-countdown" ' . implode( ' ', $div_attr ) . '></div>' : '';

	return $content;
}

add_shortcode( 'ip-two-col', 'ideapark_shortcode_two_col' );
add_shortcode( 'ip-left', 'ideapark_shortcode_left' );
add_shortcode( 'ip-right', 'ideapark_shortcode_right' );
add_shortcode( 'ip-post-share', 'ideapark_shortcode_post_share' );

function ideapark_shortcode_two_col( $atts, $content ) {
	$content = '<div class="clear"></div><div class="two-col">' . do_shortcode( $content ) . '</div><div class="clear"></div>';

	return force_balance_tags( $content );
}

function ideapark_shortcode_left( $atts, $content ) {
	$content = '<div class="left"><div>' . $content . '</div></div>';

	return $content;
}

function ideapark_shortcode_right( $atts, $content ) {
	$content = '<div class="right"><div>' . $content . '</div></div>';

	return $content;
}

function ideapark_shortcode_post_share( $atts ) {

	global $post;

	$esc_permalink = esc_url( get_permalink() );
	$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), false, '' );

	$share_links = [
		'<a href="//www.facebook.com/sharer.php?u=' . $esc_permalink . '" target="_blank" title="' . esc_html__( 'Share on Facebook', 'ideapark-foodz' ) . '">' . ideapark_svg( 'facebook', 'c-post__share-svg c-post__share-svg--facebook' ) . '</a>',
		'<a href="//twitter.com/share?url=' . $esc_permalink . '" target="_blank" title="' . esc_html__( 'Share on Twitter', 'ideapark-foodz' ) . '">' . ideapark_svg( 'twitter', 'c-post__share-svg c-post__share-svg--twitter' ) . '</a>',
		'<a href="whatsapp://send?text=' . $esc_permalink . '" target="_blank" title="' . esc_html__( 'Share on Whatsapp', 'ideapark-foodz' ) . '">' . ideapark_svg( 'whatsapp', 'c-post__share-svg c-post__share-svg--whatsapp' ) . '</a>',
	];

	ob_start();
	?>

	<div class="c-post__share">
		<span class="c-post__share-title"><?php echo __( 'Share', 'ideapark-foodz' ); ?>:</span>
		<?php
		foreach ( $share_links as $link ) {
			echo ideapark_wrap( $link );
		}
		?>
	</div>
	<?php

	$content = ob_get_clean();

	return $content;
}

function ideapark_product_share() {
	global $post;

	if ( ! ideapark_mod( 'product_share' ) ) {
		return;
	}

	$esc_permalink = esc_url( get_permalink() );
	$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), false, '' );

	$share_links = [
		'<a href="//www.facebook.com/sharer.php?u=' . $esc_permalink . '" target="_blank" title="' . esc_html__( 'Share on Facebook', 'ideapark-foodz' ) . '">' . ideapark_svg( 'facebook', 'c-product__share-svg c-product__share-svg--facebook' ) . '</a>',
		'<a href="//twitter.com/share?url=' . $esc_permalink . '" target="_blank" title="' . esc_html__( 'Share on Twitter', 'ideapark-foodz' ) . '">' . ideapark_svg( 'twitter', 'c-product__share-svg c-product__share-svg--twitter' ) . '</a>',
		'<a href="whatsapp://send?text=' . $esc_permalink . '" target="_blank" title="' . esc_html__( 'Share on Whatsapp', 'ideapark-foodz' ) . '">' . ideapark_svg( 'whatsapp', 'c-product__share-svg c-product__share-svg--whatsapp' ) . '</a>',
	];
	?>

	<div class="c-product__share">
		<span class="c-product__share-title"><?php echo __( 'Share', 'ideapark-foodz' ); ?></span>
		<?php
		foreach ( $share_links as $link ) {
			echo ideapark_wrap( $link );
		}
		?>
	</div>
	<?php
}

add_action( 'woocommerce_share', 'ideapark_product_share' );

add_filter( 'the_content', 'ideapark_shortcode_empty_paragraph_fix' );

function ideapark_shortcode_empty_paragraph_fix( $content ) {
	$shortcodes = [ 'ip-two-col', 'ip-left', 'ip-right' ];
	foreach ( $shortcodes as $shortcode ) {
		$array   = [
			'<p>[' . $shortcode    => '[' . $shortcode,
			'<p>[/' . $shortcode   => '[/' . $shortcode,
			$shortcode . ']</p>'   => $shortcode . ']',
			$shortcode . ']<br />' => $shortcode . ']'
		];
		$content = strtr( $content, $array );
	}

	return $content;
}

add_filter( 'upload_mimes', 'ideapark_mime_types' );

function ideapark_mime_types( $mimes ) {
	if ( current_user_can( 'administrator' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	return $mimes;
}

add_filter( 'wp_check_filetype_and_ext', 'ideapark_ignore_upload_ext', 10, 4 );

function ideapark_ignore_upload_ext( $checked, $file, $filename, $mimes ) {

	if ( ! $checked['type'] ) {
		$wp_filetype     = wp_check_filetype( $filename, $mimes );
		$ext             = $wp_filetype['ext'];
		$type            = $wp_filetype['type'];
		$proper_filename = $filename;

		if ( $type && 0 === strpos( $type, 'image/' ) && $ext !== 'svg' ) {
			$ext = $type = false;
		}

		$checked = compact( 'ext', 'type', 'proper_filename' );
	}

	return $checked;
}

add_action( 'current_screen', 'ideapark_svgs_display_thumbs', 1000 );

function ideapark_svgs_display_thumbs() {
	$screen = get_current_screen();
	if ( is_object( $screen ) && $screen->id == 'upload' ) {
		function ideapark_svgs_thumbs_filter( $content ) {
			return apply_filters( 'final_output', $content );
		}

		ob_start( 'ideapark_svgs_thumbs_filter' );
		add_filter( 'final_output', 'ideapark_svgs_final_output' );
		function ideapark_svgs_final_output( $content ) {
			$content = str_replace(
				'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
				'<# } else if ( \'svg+xml\' === data.subtype ) { #>
					<img class="details-image" src="{{ data.url }}" draggable="false" />
					<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',

				$content
			);
			$content = str_replace(
				'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
				'<# } else if ( \'svg+xml\' === data.subtype ) { #>
					<div class="centered">
						<img src="{{ data.url }}" class="thumbnail" draggable="false" />
					</div>
					<# } else if ( \'image\' === data.type && data.sizes ) { #>',

				$content
			);

			return $content;
		}
	}
}

function ideapark_svgs_get_dimensions( $svg ) {
	$svg = simplexml_load_file( $svg );
	if ( $svg === false ) {
		$width  = '0';
		$height = '0';
	} else {
		$attributes = $svg->attributes();
		$width      = (string) $attributes->width;
		$height     = (string) $attributes->height;
	}

	return (object) [ 'width' => $width, 'height' => $height ];
}

function ideapark_svgs_response_for_svg( $response, $attachment, $meta ) {
	if ( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ) {
		$svg_path = get_attached_file( $attachment->ID );
		if ( ! file_exists( $svg_path ) ) {
			$svg_path = $response['url'];
		}
		$dimensions        = ideapark_svgs_get_dimensions( $svg_path );
		$response['sizes'] = [
			'full' => [
				'url'         => $response['url'],
				'width'       => $dimensions->width,
				'height'      => $dimensions->height,
				'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
			]
		];
	}

	return $response;
}

add_filter( 'wp_prepare_attachment_for_js', 'ideapark_svgs_response_for_svg', 10, 3 );
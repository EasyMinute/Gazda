<?php
class ShipArea_Cpt {

	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
	}

	public function register_cpt() {
		$labels = array(
			'name'					=> __('Delivery Area', 'shiparea'),
			'menu_name'				=> __('Delivery Area', 'shiparea'),
			'name_admin_bar'		=> __('Areas List', 'shiparea'),
			'all_items'				=> __('Areas List', 'shiparea'),
			'singular_name'			=> __('Areas List', 'shiparea'),
			'add_new'				=> __('Add New Area', 'shiparea'),
			'add_new_item'			=> __('Add New Area','shiparea'),
			'edit_item'				=> __('Edit Area','shiparea'),
			'new_item'				=> __('New Area','shiparea'),
			'view_item'				=> __('View Area','shiparea'),
			'search_items'			=> __('Search Area','shiparea'),
			'not_found'				=> __('Nothing found','shiparea'),
			'not_found_in_trash'	=> __('Nothing found in Trash','shiparea'),
			'parent_item_colon'		=> ''	
		);
		 
		$args = array(
			'labels'				=> $labels,
			'public'				=> true,
			'publicly_queryable'	=> false,
			'show_ui'				=> true,
			'query_var'				=> true,
			//'menu_icon' => plugins_url( 'images/icon_star.png' , __FILE__ ),
			'rewrite'				=> false,
			'hierarchical'			=> false,
			'show_in_menu'			=> false,
			//'menu_position' => 25,
			'supports'				=> ['title', 'author'],
			'exclude_from_search'	=> true,
			'show_in_nav_menus'		=> false,
			'can_export'			=> true,
			'map_meta_cap'			=> true,
			'capability_type'		=> 'areamap',
			'capabilities'			=> apply_filters( 'shiparea/cpt/capabilities', [
										'edit_post'					=> 'edit_areamap',
										'read_post'					=> 'read_areamap',
										'delete_post'				=> 'delete_areamap',
										'edit_posts'				=> 'edit_areamaps',
										'edit_others_posts'			=> 'edit_others_areamaps',
										'publish_posts'				=> 'publish_areamaps',
										'read_private_posts'		=> 'read_private_areamaps',	
										'delete_posts'				=> 'delete_areamaps',
										'delete_private_posts'		=> 'delete_private_areamaps',
										'delete_published_posts'	=> 'delete_published_areamaps',
										'delete_others_posts'		=> 'delete_others_areamaps',
										'edit_private_posts'		=> 'edit_private_areamaps',
										'edit_published_posts'		=> 'edit_published_areamaps',
										'create_posts'				=> 'edit_areamaps',
									]),
		);
		
		register_post_type( 'areamaps' , $args );
	}
}

new ShipArea_Cpt();
?>
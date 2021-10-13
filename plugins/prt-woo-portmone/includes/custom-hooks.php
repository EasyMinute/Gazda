<?php
// Adding custom taxonomy
add_action('init', 'bill_taxonomy');
function bill_taxonomy() {
	$labels = array(
	    'name'                       => __('Рахунки'),
	    'singular_name'              => __('Рахунок'),
	    'menu_name'                  => __('Рахунки'),
	    'all_items'                  => __('Усі рахунки'),
	    'parent_item'                => __('Батьківський рахунок'),
	    'parent_item_colon'          => __('Батьківський рахунок:'),
	    'new_item_name'              => __('Нова назва рахунку'),
	    'add_new_item'               => __('Додати новий рахунок'),
	    'edit_item'                  => __('Редагувати рахунок'),
	    'update_item'                => __('Оновити рахунок'),
	    'search_items'               => __('Шукати рахунки'),
	    'add_or_remove_items'        => __('Додати чи видалити рахунки'),
	    'choose_from_most_used'      => __('Вибрати з-поміж найбільш-використовуваних рахунків'),
	);
	$args = array(
	    'labels'                     => $labels,
	    'description'          		 => __('Додайте сюди до 5 рахунків, на які буде розділятись оплата'),
	    'term_args' => array(
	        'number' => 1,
	    ),
	    'hierarchical'               => true,
	    'public'                     => true,
	    'show_ui'                    => true,
	    'show_admin_column'          => true,
	    'show_in_menu'			     => true,
	    'show_in_nav_menus'          => true,
	    'show_tagcloud'              => true,
	    'meta_box_cb'       		 => 'bill_meta_box',
	    'default_term' 				 => array('name'=>__('Власник сайту'), 'slug'=>__('owner'))
	);
    register_taxonomy( 'bill', 'product', $args );
}


// Changing the front of metabox
function bill_meta_box( $post ) {
	$terms = get_terms( 'bill', array( 'hide_empty' => false ) );

	$post  = get_post();
	$bill = wp_get_object_terms( $post->ID, 'bill', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
	$name  = '';

    if ( ! is_wp_error( $bill ) ) {
    	if ( isset( $bill[0] ) && isset( $bill[0]->name ) ) {
			$name = $bill[0]->name;
	    } else{
			$name = __('Власник сайту');
	    }
    } 

	foreach ( $terms as $term ) {
?>
		<label title='<?php esc_attr_e( $term->name ); ?>'>
		    <input type="radio" name="bill" value="<?php esc_attr_e( $term->name ); ?>" <?php checked( $term->name, $name ); ?>>
			<span><?php esc_html_e( $term->name ); ?></span>
		</label><br>
<?php
    }
}

/**
 * Save the movie meta box results.
 *
 * @param int $post_id The ID of the post that's being saved.
 */
add_action( 'save_post_product', 'save_product_bill' );
function save_product_bill( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['bill'] ) ) {
		return;
	}

	$bill = sanitize_text_field( $_POST['bill'] );
	
	// A valid rating is required, so don't let this get published without one
	if ( empty( $bill ) ) {
		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_product', 'save_product_bill' );

		$postdata = array(
			'ID'          => $post_id,
			'post_status' => 'draft',
		);
		wp_update_post( $postdata );
	} else {
		$term = get_term_by( 'name', $bill, 'bill' );
		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			wp_set_object_terms( $post_id, $term->term_id, 'bill', false );
		}
	}
}


// Limitting the amount of terms, added to tax
add_filter( 'pre_insert_term', 'limit_terms', 10, 2 );
function limit_terms( $term, $taxonomy ){
	
	if ($taxonomy != 'bill') {
		return $term;
	} else {
		$terms = get_terms( [
			'taxonomy' => 'bill',
			'hide_empty' => false,
		] );
		if (count($terms) < 5) {
			return $term;
		} else {
			return new WP_Error( 'term_addition_blocked', __( 'Ви не можете додати більше ніж 5 рахунків' ) );
		}
	}

	
}



?>
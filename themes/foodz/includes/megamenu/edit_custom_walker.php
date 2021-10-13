<?php
/**
 *  /!\ This is a copy of Walker_Nav_Menu_Edit class in core
 * 
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_Ideapark extends Walker_Nav_Menu {

	private $icon_ids = [];

	public function __construct(  ) {
		$filename = get_template_directory() . '/assets/img/sprite.svg';

		if ( is_file( $filename ) ) {
			$t_xml = new DOMDocument();
			$t_xml->load( $filename );
			$symbols = $t_xml->getElementsByTagName( 'symbol' );
			foreach ( $symbols as $symbol ) {
				if ( preg_match( '~^svg-icon-\d+$~', $symbol->getAttribute( 'id' ) ) ) {
					$this->icon_ids[] = $symbol->getAttribute( 'id' );
				}
			}
		}
	}


	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker_Nav_Menu::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	public function start_lvl( &$output, $depth = 0, $args = [] ) {}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker_Nav_Menu::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	public function end_lvl( &$output, $depth = 0, $args = [] ) {}

	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 * @since 3.0.0
	 *
	 * @global int $_wp_nav_menu_max_depth
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 * @param int    $id     Not used.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = [
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		];

		$original_title = false;
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = get_the_title( $original_object->ID );
		} elseif ( 'post_type_archive' == $item->type ) {
			$original_object = get_post_type_object( $item->object );
			if ( $original_object ) {
				$original_title = $original_object->labels->archives;
			}
		}

		$classes = [
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		];

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)', 'foodz' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __( '%s (Pending)', 'foodz' ), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth )
			$submenu_text = 'style="display: none;"';

		?>
	<li id="menu-item-<?php echo ideapark_wrap( $item_id ); ?>" class="<?php echo implode(' ', $classes ); ?>">
		<div class="menu-item-bar">
			<div class="menu-item-handle">
				<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo ideapark_wrap( $submenu_text ); ?>><?php esc_html_e( 'sub item', 'foodz' ); ?></span></span>
				<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									[
										'action' => 'move-up-menu-item',
										'menu-item' => $item_id,
									],
									remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-up" aria-label="<?php esc_attr_e( 'Move up', 'foodz' ) ?>">&#8593;</a>
							|
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									[
										'action' => 'move-down-menu-item',
										'menu-item' => $item_id,
									],
									remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-down" aria-label="<?php esc_attr_e( 'Move down', 'foodz' ) ?>">&#8595;</a>
						</span>
						<a class="item-edit" id="edit-<?php echo ideapark_wrap( $item_id ); ?>" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>" aria-label="<?php esc_attr_e( 'Edit menu item', 'foodz' ); ?>"><?php _e( 'Edit', 'foodz' ); ?></a>
					</span>
			</div>
		</div>

		<div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo ideapark_wrap( $item_id ); ?>">
			<?php if ( 'custom' == $item->type ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo ideapark_wrap( $item_id ); ?>">
						<?php _e( 'URL', 'foodz' ); ?><br />
						<input type="text" id="edit-menu-item-url-<?php echo ideapark_wrap( $item_id ); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-wide">
				<label for="edit-menu-item-title-<?php echo ideapark_wrap( $item_id ); ?>">
					<?php _e( 'Navigation Label', 'foodz' ); ?><br />
					<input type="text" id="edit-menu-item-title-<?php echo ideapark_wrap( $item_id ); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
				</label>
			</p>
			<p class="field-title-attribute field-attr-title description description-wide">
				<label for="edit-menu-item-attr-title-<?php echo ideapark_wrap( $item_id ); ?>">
					<?php _e( 'Title Attribute', 'foodz' ); ?><br />
					<input type="text" id="edit-menu-item-attr-title-<?php echo ideapark_wrap( $item_id ); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
				</label>
			</p>
			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo ideapark_wrap( $item_id ); ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo ideapark_wrap( $item_id ); ?>" value="_blank" name="menu-item-target[<?php echo ideapark_wrap( $item_id ); ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php _e( 'Open link in a new tab', 'foodz' ); ?>
				</label>
			</p>
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-<?php echo ideapark_wrap( $item_id ); ?>">
					<?php _e( 'CSS Classes (optional)', 'foodz' ); ?><br />
					<input type="text" id="edit-menu-item-classes-<?php echo ideapark_wrap( $item_id ); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
				</label>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo ideapark_wrap( $item_id ); ?>">
					<?php _e( 'Link Relationship (XFN)', 'foodz' ); ?><br />
					<input type="text" id="edit-menu-item-xfn-<?php echo ideapark_wrap( $item_id ); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
				</label>
			</p>
			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo ideapark_wrap( $item_id ); ?>">
					<?php _e( 'Description', 'foodz' ); ?><br />
					<textarea id="edit-menu-item-description-<?php echo ideapark_wrap( $item_id ); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo ideapark_wrap( $item_id ); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
					<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', 'foodz'); ?></span>
				</label>
			</p>
			<?php
			/* New fields insertion starts here */
			?>
			<p class="field-custom ip-subheaders">
				<label for="edit-menu-item-subheaders-<?php echo ideapark_wrap( $item_id ); ?>" class="ideapark-menu-label">
					<?php _e( 'Submenu type: ', 'foodz' ); ?>
					<select id="edit-menu-item-subheaders-<?php echo ideapark_wrap( $item_id ); ?>" class="edit-menu-item-subheaders" name="menu-item-subheaders[<?php echo ideapark_wrap( $item_id ); ?>]">
						<option value=""><?php _e('1 column','foodz'); ?></option>
						<option value="col-2" <?php selected("col-2", $item->subheaders); ?>><?php _e('2 columns','foodz'); ?></option>
						<option value="col-3" <?php selected("col-3", $item->subheaders); ?>><?php _e('3 columns','foodz'); ?></option>
						<option value="col-4" <?php selected("col-4", $item->subheaders); ?>><?php _e('4 columns','foodz'); ?></option>
					</select>
				</label>
			</p>

			<div class="field-custom wtef-svg-icons" id="ip-mega-menu-<?php echo ideapark_wrap( $item_id ); ?>">
				<?php if ( !empty($item->svg_id) || !empty($item->img_id)) { ?>
					<?php if ( !empty($item->svg_id) ) { ?>
					<svg>
						<use xlink:href="#<?php echo esc_attr( $item->svg_id ); ?>" />
					</svg>
					<?php } elseif ($image = wp_get_attachment_image_src($item->img_id)) { ?>
						<img src="<?php echo esc_url($image[0]); ?>">
					<?php } ?>
					<a href="#" class="ip-load-mega-menu" data-svg-id="<?php echo esc_attr($item->svg_id); ?>" data-img-id="<?php echo esc_attr($item->img_id); ?>" data-item-id="<?php echo ideapark_wrap( $item_id ); ?>"><?php _e('Change Icon', 'foodz'); ?></a>
					<span class="spinner"></span>
					<a class="clear show clpse" data-item-id="<?php echo ideapark_wrap( $item_id ); ?>" href="#">
						<svg>
							<use xlink:href="#svg-close" />
						</svg>
						<?php _e('clear icon', 'foodz'); ?>
					</a>
					<input type="hidden" name="menu-item-svg-id[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr($item->svg_id ? $item->svg_id : ('custom-' . $item->img_id) ); ?>">
				<?php } else { ?>
					<a href="#" class="ip-load-mega-menu" data-svg-id="" data-img-id="" data-item-id="<?php echo ideapark_wrap( $item_id ); ?>"><?php _e('Select Icon', 'foodz'); ?></a>
					<span class="spinner"></span>
				<?php } ?>
			</div>

			<p class="field-custom ip-label">
				<label for="edit-menu-item-label-<?php echo ideapark_wrap( $item_id ); ?>" class="ideapark-menu-label">
					<?php _e( 'Badge title: ', 'foodz' ); ?>
					<input type="text" id="edit-menu-item-ip-label-<?php echo ideapark_wrap( $item_id ); ?>" class="edit-menu-item-label" name="menu-item-ip-label[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->ip_label ); ?>" />
				</label>
			</p>

			<?php
			/* New fields insertion ends here */
			?>
			<fieldset class="field-move hide-if-no-js description description-wide">
				<span class="field-move-visual-label" aria-hidden="true"><?php _e( 'Move', 'foodz' ); ?></span>
				<button type="button" class="button-link menus-move menus-move-up" data-dir="up"><?php _e( 'Up one', 'foodz' ); ?></button>
				<button type="button" class="button-link menus-move menus-move-down" data-dir="down"><?php _e( 'Down one', 'foodz' ); ?></button>
				<button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
				<button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
				<button type="button" class="button-link menus-move menus-move-top" data-dir="top"><?php _e( 'To the top', 'foodz' ); ?></button>
			</fieldset>

			<div class="menu-item-actions description-wide submitbox">
				<?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
					<p class="link-to-original">
						<?php printf( __('Original: %s', 'foodz'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
					</p>
				<?php endif; ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo ideapark_wrap( $item_id ); ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						[
							'action' => 'delete-menu-item',
							'menu-item' => $item_id,
						],
						admin_url( 'nav-menus.php' )
					),
					'delete-menu_item_' . $item_id
				); ?>"><?php _e( 'Remove', 'foodz' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo ideapark_wrap( $item_id ); ?>" href="<?php echo esc_url( add_query_arg( [ 'edit-menu-item' => $item_id, 'cancel' => time() ], admin_url( 'nav-menus.php' ) ) );
				?>#menu-item-settings-<?php echo ideapark_wrap( $item_id ); ?>"><?php _e('Cancel', 'foodz'); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo ideapark_wrap( $item_id ); ?>" />
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo ideapark_wrap( $item_id ); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

} // Walker_Nav_Menu_Edit

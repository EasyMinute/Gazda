<?php

/**
 * Custom Walker
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
class Ideapark_Megamenu_Walker extends Walker_Nav_Menu {
	private $items_counter = 0;
	private $with_icon;
	private $label_place;
	private $label_center;
	private $empty_icon;
	private $submenu_class;
	private $submenu_class_add;
	private $title_class;
	private $title_wrap_class;

	public function walk( $elements, $max_depth, ...$args ) {
		$args                   = array_slice( func_get_args(), 2 );
		$this->with_icon        = ! empty( $args[0] ) && isset( $args[0]->with_icon ) && $args[0]->with_icon === true;
		$this->empty_icon       = ! empty( $args[0] ) && isset( $args[0]->empty_icon ) && $args[0]->empty_icon === true;
		$this->label_place      = ! empty( $args[0] ) && isset( $args[0]->label_place ) && $args[0]->label_place === 'icon' ? 'icon' : 'title';
		$this->label_center     = ! empty( $args[0] ) && isset( $args[0]->label_center ) && $args[0]->label_center === true;
		$this->title_class      = ! empty( $args[0] ) && isset( $args[0]->title_class ) ? trim( $args[0]->title_class ) : '';
		$this->title_wrap_class = ! empty( $args[0] ) && isset( $args[0]->title_wrap_class ) ? trim( $args[0]->title_wrap_class ) : '';
		$this->submenu_class    = ! empty( $args[0] ) && isset( $args[0]->submenu_class ) ? trim( $args[0]->submenu_class ) : '';

		return call_user_func_array( 'parent::walk', func_get_args() );
	}

	public function start_lvl( &$output, $depth = 0, $args = [] ) {
		$classes = [];
		if ( $depth > 0 && $this->submenu_class ) {
			$classes[] = $this->submenu_class;
		}

		$output                  .= "<ul class=\"c-mega-menu__submenu" . ( ! empty( $classes ) ? ' ' . implode( ' ', $classes ) : '' ) . ( $this->submenu_class_add ? ' ' . esc_attr( $this->submenu_class_add ) : '' ) . "\">";
		$this->submenu_class_add = '';
	}

	public function end_lvl( &$output, $depth = 0, $args = [] ) {
		$output .= "</ul>";
	}

	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {

		if ( $depth == 0 ) {
			$this->items_counter ++;

		}

		$classes = empty( $item->classes ) ? [] : (array) $item->classes;

		global $ideapark_menu_item_depth;
		$ideapark_menu_item_depth = $depth;
		$classes                  = array_map( function ( $class ) {
			global $ideapark_menu_item_depth;

			if ( preg_match( '~menu-item-(\d+)~', $class, $match ) ) {
				return 'c-mega-menu__item--' . $match[1];
			} else {
				switch ( $class ) {

					case 'menu-item';
						return ( $ideapark_menu_item_depth > 0 ? 'c-mega-menu__subitem' : 'c-mega-menu__item' );

					case 'menu-item-has-children';
						return ( $ideapark_menu_item_depth > 0 ? 'c-mega-menu__subitem--has-children' : 'c-mega-menu__item--has-children' );

					default:
						return '';
				}
			}
		}, $classes );

		$classes[] = 'menu-item-' . $item->ID;
		if ( ! empty( $item->subheaders ) ) {
			$this->submenu_class_add = 'c-mega-menu__submenu--' . $item->subheaders;
		} else {
			$this->submenu_class_add = '';
		}
		$classes = array_unique( array_filter( $classes ) );

		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= '<li' . $class_names . '>';


		$classes_link = [];

		if ( $depth > 0 ) {
			$classes_link[] = 'c-mega-menu__sublink';
			if ( $depth > 1 ) {
				$classes_link[] = 'c-mega-menu__sublink--small';
			}
		}

		$atts           = [];
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		if ( $classes_link ) {
			$atts['class'] = esc_attr( implode( ' ', $classes_link ) );
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$is_icon_empty = false;
		if ( $depth == 0 && $this->with_icon ) {

			if ( $this->empty_icon ) {
				$icon          = '<svg class="c-mega-menu__icon c-mega-menu__icon--empty" xmlns="http://www.w3.org/2000/svg" width="1" height="1"/>';
				$is_icon_empty = true;
			} else {
				$icon = '';
			}

			if ( ! empty( $item->svg_id ) ) {
				$icon          = ideapark_svg( $item->svg_id, 'c-mega-menu__icon' );
				$is_icon_empty = false;
			} elseif ( ! empty( $item->img_id ) ) {
				$image         = wp_get_attachment_image_src( $item->img_id, 'ideapark-mega-menu-thumb', true );
				$image_srcset  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $item->img_id, 'ideapark-mega-menu-thumb' ) : false;
				$image_sizes   = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $item->img_id, 'ideapark-mega-menu-thumb' ) : false;
				$icon          = '<img class="c-mega-menu__icon" src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( $item->title ) . '"' . ( $image_srcset ? ' srcset="' . esc_attr( $image_srcset ) . '"' : '' ) . ( $image_sizes ? ' sizes="' . esc_attr( $image_sizes ) . '"' : '' ) . '/>';
				$is_icon_empty = false;
			}
		} else {
			$icon = '';
		}

		if ( $icon && ! $this->label_center ) {
			$this->submenu_class_add .= " c-mega-menu__submenu--has-icon";
		}

		if ( $depth == 0 && ! empty( $item->ip_label ) ) {
			$label = ideapark_wrap( esc_html( $item->ip_label ), '<span class="c-mega-menu__label-wrap' . ( $this->label_center ? ' c-mega-menu__label-wrap--center' : '' ) . '"><span class="c-mega-menu__label' . ( $this->label_center ? ' c-mega-menu__label--center' : '' ) . '">', '</span></span>' );
		} else {
			$label = '';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		if ( $depth == 0 ) {
			$title_class = [ 'c-mega-menu__title' ];
			$wrap_class  = [ 'c-mega-menu__title-wrap' ];

			if ( $this->title_class ) {
				$title_class[] = $this->title_class;
			}

			if ( $this->title_wrap_class ) {
				$wrap_class[] = $this->title_wrap_class;
			}

			$is_label_above_icon = $this->label_place == 'icon' && ! $is_icon_empty && $this->with_icon;
			$item_title_block    = ideapark_wrap( ( $is_label_above_icon ? $label : '' ) . $icon . ideapark_wrap( ( ! $is_label_above_icon ? $label : '' ) . $title, '<span class="' . implode( ' ', $title_class ) . '">', '</span>' ), '<span class="' . implode( ' ', $wrap_class ) . '">', '</span>' );
			$item_output         .= ideapark_wrap( $item_title_block, isset($args->link_before) ? $args->link_before : '', isset($args->link_after) ? $args->link_after : '' );
		} else {
			$item_output .= ideapark_wrap( '<span class="c-mega-menu__subtitle">' . $title . '</span>', isset($args->link_before) ? $args->link_before : '', isset($args->link_after) ? $args->link_after : '' );
		}

		$item_output .= '</a>';
		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function end_el( &$output, $item, $depth = 0, $args = [] ) {
		$output .= "</li>";
	}
}
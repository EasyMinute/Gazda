<?php

if ( ! function_exists( 'ideapark_setup_woocommerce' ) ) {
	function ideapark_setup_woocommerce() {
		if ( ideapark_is_requset( 'frontend' ) && ideapark_woocommerce_on() ) {

			/* Product loop page */
			ideapark_ra( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			ideapark_ra( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			ideapark_ra( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			ideapark_rf( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
			ideapark_ra( 'woocommerce_before_main_content', 'woocommerce_single_product_summary', 20 );
			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
			ideapark_aa( 'woocommerce_after_page_title', 'woocommerce_output_all_notices', 10 );

			/* Product page */
			ideapark_ra( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
			ideapark_ra( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			ideapark_ra( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			ideapark_aa( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 8 );

			/* Cart page */
			ideapark_ra( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
			ideapark_aa( 'woocommerce_before_cart_totals', 'woocommerce_checkout_coupon_form', 10 );

			/* Checkout page */
			ideapark_ra( 'woocommerce_before_checkout_form_cart_notices', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
			ideapark_aa( 'woocommerce_checkout_before_order_review', 'woocommerce_checkout_coupon_form', 10 );

			/* All Account pages */
			ideapark_ra( 'woocommerce_account_content', 'woocommerce_output_all_notices', 5 );

			/* All WC pages */
			ideapark_ra( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
			ideapark_ra( 'woocommerce_before_lost_password_form', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_reset_password_form', 'woocommerce_output_all_notices', 10 );
			ideapark_ra( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10 );

			if ( ! ideapark_mod( 'product_preview_rating' ) ) {
				ideapark_ra( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			}

		}
	}
}

if ( ! function_exists( 'ideapark_ajax_update_quantity' ) ) {
	function ideapark_ajax_update_quantity() {

		ob_start();
		$result = [ 'success' => true ];

		if ( isset( $_POST['quantity'] ) && isset( $_POST['cart_item_key'] ) && ( $cart_item_key = wc_clean( $_POST['cart_item_key'] ) ) ) {
			$quantity = apply_filters( 'woocommerce_stock_amount_cart_item', wc_stock_amount( preg_replace( '/[^0-9\.]/', '', $_POST['quantity'] ) ), $cart_item_key );
			if ( $quantity ) {
				if ( ! WC()->cart->is_empty() ) {
					foreach ( WC()->cart->get_cart() as $_cart_item_key => $values ) {

						if ( $cart_item_key == $_cart_item_key ) {

							if ( $quantity !== $values['quantity'] ) {
								$_product = $values['data'];
								if ( $_product->is_sold_individually() && $quantity > 1 ) {
									$errors = new WP_Error();
									$errors->add( 'sold_individually', sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_name() ) );
									ob_end_clean();
									wp_send_json_error( $errors );
								} else {
									WC()->cart->set_quantity( $cart_item_key, $quantity, true );
									$result['quantity'] = $quantity;
									ob_end_clean();
									wp_send_json( $result );
								}
							}
							break;
						}
					}
					$result['quantity'] = 0;
					ob_end_clean();
					wp_send_json( $result );
				}
			} elseif ( false !== WC()->cart->remove_cart_item( $cart_item_key ) ) {
				$result['quantity'] = $quantity;
				ob_end_clean();
				wp_send_json( $result );
			} else {
				ob_end_clean();
				wp_send_json_error();
			}
		}
		ob_end_clean();
	}
}

if ( ! function_exists( 'ideapark_cart_info' ) ) {
	function ideapark_cart_info() {
		global $woocommerce;

		$cart_total = $woocommerce->cart->get_cart_total();
		$cart_count = $woocommerce->cart->get_cart_contents_count();

		return '<span class="js-cart-info">'
		       . ( ! $woocommerce->cart->is_empty() ? ideapark_wrap( esc_html( $cart_count ), '<span class="c-header__cart-count js-cart-count">', '</span>' ) : '' )
		       . ( ! $woocommerce->cart->is_empty() ? ideapark_wrap( $cart_total, '<span class="c-header__cart-sum">', '</span>' ) : '' ) .
		       '</span>';
	}
}

if ( ! function_exists( 'ideapark_wishlist_info' ) ) {
	function ideapark_wishlist_info() {
		global $woocommerce;

		if ( ideapark_mod( 'wishlist_page' ) ) {
			$count = sizeof( ideapark_wishlist()->ids() );
		} else {
			$count = 0;
		}

		return '<span class="js-wishlist-info">'
		       . ( $count ? ideapark_wrap( $count, '<span class="c-header__cart-count">', '</span>' ) : '' ) .
		       '</span>';
	}
}

if ( ! function_exists( 'ideapark_header_add_to_cart_fragment' ) ) {
	function ideapark_header_add_to_cart_fragment( $fragments ) {
		$fragments['.js-cart-info']     = ideapark_cart_info();
		$fragments['.js-wishlist-info'] = ideapark_wishlist_info();

		if ( ! empty( $_POST['product_id'] ) && ( $product_id = absint( $_POST['product_id'] ) ) ) {
			$product_id   = apply_filters( 'woocommerce_add_to_cart_product_id', $product_id );
			$variation_id = 0;
			$product      = wc_get_product( $product_id );
			if ( $product && 'variation' === $product->get_type() ) {
				$variation_id = $product_id;
				$product_id   = $product->get_parent_id();
			}
			$result = '';
			$args   = false;
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				if ( $_product_id == $product_id && $variation_id == $cart_item['variation_id'] ) {
					$defaults = [
						'input_name'    => 'quantity',
						'input_value'   => $cart_item['quantity'],
						'max_value'     => apply_filters( 'woocommerce_quantity_input_max', - 1, $product ),
						'min_value'     => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
						'step'          => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
						'pattern'       => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
						'inputmode'     => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
						'product_name'  => $product ? $product->get_title() : '',
						'cart_item_key' => $cart_item_key,
					];

					$args = apply_filters( 'woocommerce_quantity_input_args', $defaults, $product );

					$args['min_value'] = max( $args['min_value'], 0 );
					$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

					if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
						$args['max_value'] = $args['min_value'];
					}
					break;
				}
			}
			if ( $args ) {
				ob_start();
				wc_get_template( 'loop/quantity.php', $args );
				$fragments['ideapark_quantity']     = ob_get_clean();
				$fragments['ideapark_variation_id'] = $variation_id;

				ob_start();
				wc_print_notices();
				$fragments['ideapark_notice'] = ob_get_clean();
			}
		}

		return $fragments;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_show_product_loop_badges' ) ) {
	function ideapark_woocommerce_show_product_loop_badges() {
		/**
		 * @var $product WC_Product
		 **/
		global $product;

		if ( ideapark_mod( 'featured_badge_text' ) && $product->is_featured() ) {
			echo '<span class="c-badge c-badge--featured">' . esc_html( ideapark_mod( 'featured_badge_text' ) ) . '</span>';
		}

		$newness = (int) ideapark_mod( 'product_newness' );

		if ( ideapark_mod( 'new_badge_text' ) && $newness > 0 ) {
			$postdate      = get_the_time( 'Y-m-d' );
			$postdatestamp = strtotime( $postdate );
			if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) {
				echo '<span class="c-badge c-badge--new">' . esc_html( ideapark_mod( 'new_badge_text' ) ) . '</span>';
			}
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_breadcrumbs' ) ) {
	function ideapark_woocommerce_breadcrumbs() {
		return [
			'delimiter'   => '',
			'wrap_before' => '<nav class="с-breadcrumb"><ol class= "с-breadcrumb__list">',
			'wrap_after'  => '</ol></nav>',
			'before'      => '<li class= "с-breadcrumb__item">',
			'after'       => '</li>',
			'home'        => esc_html_x( 'Home', 'breadcrumb', 'woocommerce' ),
		];
	}
}

if ( ! function_exists( 'ideapark_woocommerce_account_menu_items' ) ) {
	function ideapark_woocommerce_account_menu_items( $items ) {
		unset( $items['customer-logout'] );

		return $items;
	}
}

if ( ! function_exists( 'ideapark_product_availability' ) ) {
	function ideapark_product_availability() {
		global $product;

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$availability = $product->get_availability();
			if ( $product->is_in_stock() ) {
				$availability_html = '<span class="c-stock c-stock--in-stock ' . esc_attr( $availability['class'] ) . '">' . ( $availability['availability'] ? esc_html( $availability['availability'] ) : esc_html__( 'In stock', 'foodz' ) ) . '</span>';
			} else {
				$availability_html = '<span class="c-stock c-stock--out-of-stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';
			}
		} else {
			$availability_html = '';
		}

		echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}
}

if ( ! function_exists( 'ideapark_cut_product_categories' ) ) {
	function ideapark_cut_product_categories( $links ) {
		if ( ideapark_woocommerce_on() && is_product() ) {
			$links = array_slice( $links, 0, 2 );
		}

		return $links;
	}
}

if ( ! function_exists( 'ideapark_remove_product_description_heading' ) ) {
	function ideapark_remove_product_description_heading() {
		return '';
	}
}

if ( ! function_exists( 'ideapark_woocommerce_search_form' ) ) {
	function ideapark_woocommerce_search_form() {
		if ( is_search() ) {
			echo '<div class="c-product-search-form">';
			get_search_form();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_max_srcset_image_width_768' ) ) {
	function ideapark_woocommerce_max_srcset_image_width_768( $max_width, $size_array ) {
		return 768;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_max_srcset_image_width_360' ) ) {
	function ideapark_woocommerce_max_srcset_image_width_360( $max_width, $size_array ) {
		return 360;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_hide_uncategorized' ) ) {
	function ideapark_woocommerce_hide_uncategorized( $args ) {
		if ( ideapark_mod( 'hide_uncategorized' ) ) {
			$args['exclude'] = get_option( 'default_product_cat' );
			if ( ! empty( $args['include'] ) ) {
				$args['include'] = implode( ',', array_filter( explode( ',', $args['include'] ), function ( $var ) {
					return $var != get_option( 'default_product_cat' );
				} ) );
			}
		}

		return $args;
	}
}

if ( ! function_exists( 'ideapark_subcategory_archive_thumbnail_size' ) ) {
	function ideapark_subcategory_archive_thumbnail_size( $thumbnail_size ) {
		return 'woocommerce_gallery_thumbnail';
	}
}

if ( ! function_exists( 'ideapark_loop_add_to_cart_link' ) ) {
	function ideapark_loop_add_to_cart_link( $text, $product, $args ) {
		return preg_replace( '~(<a[^>]+>)~ui', '\\1' . '<svg class="c-add-to-cart__svg"><use xlink:href="' . esc_url( ideapark_svg_url() ) . '#svg-cart" /></svg>', $text );
	}
}

if ( ! function_exists( 'ideapark_woocommerce_gallery_image_size' ) ) {
	function ideapark_woocommerce_gallery_image_size( $size ) {
		return 'woocommerce_single';
	}
}

if ( ! function_exists( 'ideapark_get_filtered_term_product_counts' ) ) {
	function ideapark_get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type, $tax_query = null, $meta_query = null ) {
		global $wpdb;

		if ( $tax_query === null ) {
			$tax_query = WC_Query::get_main_tax_query();
		}

		if ( $meta_query === null ) {
			$meta_query = WC_Query::get_main_meta_query();
		}

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		$meta_query     = new WP_Meta_Query( $meta_query );
		$tax_query      = new WP_Tax_Query( $tax_query );
		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

		// Generate query.
		$query           = [];
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];

		$query['where'] = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'"
		                  . $tax_query_sql['where'] . $meta_query_sql['where'] .
		                  'AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

		if ( ! empty( WC_Query::$query_vars ) ) {
			$search = WC_Query::get_main_search_query_sql();
			if ( $search ) {
				$query['where'] .= ' AND ' . $search;
			}
		}

		$query['group_by'] = 'GROUP BY terms.term_id';
		$query             = implode( ' ', $query );

		// We have a query - let's see if cached results of this query already exist.
		$query_hash = md5( $query );

		// Maybe store a transient of the count values.
		$cache = apply_filters( 'woocommerce_layered_nav_count_maybe_cache', true );
		if ( true === $cache ) {
			$cached_counts = (array) get_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ) );
		} else {
			$cached_counts = [];
		}

		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
			$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
			$cached_counts[ $query_hash ] = $counts;
			if ( true === $cache ) {
				set_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
			}
		}

		return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
	}
}

if ( ! function_exists( 'ideapark_horizontal_filter' ) ) {
	function ideapark_horizontal_filter( $tax_query = null, $meta_query = null ) {

		$result = [];

		if ( ideapark_mod( 'category_fast_filter' ) && ideapark_mod( 'category_fast_filter_attribute' ) ) {
			if ( get_query_var( 'taxonomy' ) == ideapark_mod( 'category_fast_filter_attribute' ) ) {
				return [];
			}
			$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
			$taxonomy           = ideapark_mod( 'category_fast_filter_attribute' );
			if ( ! taxonomy_exists( $taxonomy ) ) {
				return $result;
			}
			$get_terms_args = [ 'hide_empty' => '1' ];
			$query_type     = 'or';

			$orderby = wc_attribute_orderby( $taxonomy );

			switch ( $orderby ) {
				case 'name':
					$get_terms_args['orderby']    = 'name';
					$get_terms_args['menu_order'] = false;
					break;
				case 'id':
					$get_terms_args['orderby']    = 'id';
					$get_terms_args['order']      = 'ASC';
					$get_terms_args['menu_order'] = false;
					break;
				case 'menu_order':
					$get_terms_args['menu_order'] = 'ASC';
					break;
			}

			$terms = get_terms( $taxonomy, $get_terms_args );

			if ( 0 !== count( $terms ) ) {
				switch ( $orderby ) {
					case 'name_num':
						usort( $terms, '_wc_get_product_terms_name_num_usort_callback' );
						break;
					case 'parent':
						usort( $terms, '_wc_get_product_terms_parent_usort_callback' );
						break;
				}
			}
			if ( $terms ) {
				$term_counts = ideapark_get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type, $tax_query, $meta_query );

				$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : [];

				if ( $tax_query ) {
					$product_cat = 0;
					foreach ( $tax_query as $key => $query ) {
						if ( is_array( $query ) && 'product_cat' === $query['taxonomy'] && ! empty( $query['terms'][0] ) ) {
							$product_cat = absint( $query['terms'][0] );
						}
					}
				} else {
					$product_cat = get_query_var( 'product_cat' );
				}

				$category_link = $product_cat ? get_term_link( $product_cat, 'product_cat' ) : '';

				$result[] = [
					'title'        => esc_html__( 'All', 'foodz' ),
					'link'         => $category_link,
					'product_cat'  => $product_cat,
					'filter_name'  => '',
					'filter_value' => '',
					'chosen'       => empty( $current_values )
				];

				foreach ( $terms as $term ) {
					$option_is_set = in_array( $term->slug, $current_values, true );
					$count         = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;
					if ( 0 < $count ) {

					} elseif ( 0 === $count && ! $option_is_set ) {
						continue;
					}
					$filter_name = 'filter_' . str_replace( 'pa_', '', $taxonomy );

					$link = $category_link;
					$link = add_query_arg( $filter_name, $term->slug, $link );

					$result[] = [
						'title'        => esc_html( $term->name ),
						'link'         => $link,
						'product_cat'  => $product_cat,
						'filter_name'  => str_replace( 'pa_', '', $taxonomy ),
						'filter_value' => $term->slug,
						'chosen'       => $option_is_set
					];
				}

				if ( sizeof( $result ) == 1 ) {
					$result = [];
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_get_term_thumbnail' ) ) {
	$ideapark_get_term_thumbnail_cache = [];
	function ideapark_get_term_thumbnail( $term, $class = '', $archive_taxonomie = '' ) {
		/* @var $term WP_Term */
		global $ideapark_get_term_thumbnail_cache;
		if ( array_key_exists( $term->term_id, $ideapark_get_term_thumbnail_cache ) ) {
			return $ideapark_get_term_thumbnail_cache[ $term->term_id ];
		}
		$image = '';

		if ( $thumbnail_id = absint( get_term_meta( $term->term_id, 'ideapark_thumbnail_id', true ) ) ) {
			$image_src = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
			$image_alt = trim( strip_tags( $term->name ) );
			if ( empty( $image_alt ) ) {
				$image_alt = trim( strip_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
			}

			$content = '<span class="c-markers__title">' . esc_html( $image_alt ) . '</span><img class="c-markers__icon" src="' . esc_url( $image_src[0] ) . '" alt="' . esc_attr( $image_alt ) . '">';
			if ( $archive_taxonomie ) {
				$content = ideapark_wrap( $content, '<a href="' . esc_url( get_term_link( $term->term_id, $archive_taxonomie ) ) . '" rel="tag">', '</a>' );
			}
			$image = '<span class="c-markers__wrap ' . ( $class ? esc_attr( $class ) : '' ) . ' js-marker">' . $content . '</span>';
		}

		$ideapark_get_term_thumbnail_cache[ $term->term_id ] = $image;

		return $image;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_product_add_to_cart_text' ) ) {
	function ideapark_woocommerce_product_add_to_cart_text( $text, $product ) {
		/* @var WC_Product_Variable $product */
		if ( ideapark_mod( 'product_variations_in_grid' ) && $product->is_type( 'variable' ) ) {
			return __( 'Add to cart', 'woocommerce' );
		} else {
			return $text;
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_product_add_to_cart_url' ) ) {
	function ideapark_woocommerce_product_add_to_cart_url( $url, $product ) {
		if ( ideapark_mod( 'product_variations_in_grid' ) && $product->is_type( 'variable' ) ) {
			return $product->is_purchasable() && $product->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product->get_id() ) ) : get_permalink( $product->get_id() );
		} else {
			return $url;
		}
	}
}

if ( ! function_exists( 'ideapark_single_variation' ) ) {
	function ideapark_single_variation() {
		echo '<div class="c-variation__single-info single_variation">';
		woocommerce_template_loop_price();
		echo '</div>';
		echo '<div class="c-variation__single-price">';
		woocommerce_template_loop_price();
		echo '</div>';
	}
}

if ( ! function_exists( 'ideapark_woocommerce_loop_add_to_cart_args' ) ) {
	function ideapark_woocommerce_loop_add_to_cart_args( $args ) {

		if ( ideapark_mod( 'add_to_cart_class' ) ) {
			$args['class'] = esc_attr( ideapark_mod( 'add_to_cart_class' ) ) . ' ' . $args['class'];
		}

		$args['class'] = 'c-add-to-cart disabled ' . $args['class'] . ( strpos( $args['class'], 'single_add_to_cart_button' ) !== false ? ' js-add-to-cart-variation' : '' );

		return $args;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_available_variation' ) ) {
	function ideapark_woocommerce_available_variation( $params, $instance, $variation ) {
		global $product;
		if ( ideapark_mod( 'products_in_loop' ) ) {

			if ( get_post_thumbnail_id() == $params['image_id'] ) {
				unset( $params['image'] );
			} else {
				unset( $params['image']['url'] );
				unset( $params['image']['caption'] );
				unset( $params['image']['src'] );
				unset( $params['image']['srcset'] );
				unset( $params['image']['sizes'] );
				unset( $params['image']['full_src'] );
				unset( $params['image']['full_src_w'] );
				unset( $params['image']['full_src_h'] );
				unset( $params['image']['gallery_thumbnail_src'] );
				unset( $params['image']['gallery_thumbnail_src_w'] );
				unset( $params['image']['gallery_thumbnail_src_h'] );
				unset( $params['image']['thumb_src_w'] );
				unset( $params['image']['thumb_src_h'] );
				unset( $params['image']['src_w'] );
				unset( $params['image']['src_h'] );

				ideapark_wp_scrset_on( 'grid' );
				$params['image']['thumb_srcset'] = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $params['image_id'], 'woocommerce_thumbnail' ) : false;
				ideapark_wp_scrset_off( 'grid' );
			}
		} else {
			$image = wp_get_attachment_image_src( $params['image_id'], apply_filters( 'single_product_small_thumbnail_size', 'ideapark-single-product-thumb' ) );
			if ( ! empty( $image ) ) {
				$params['image']['gallery_thumbnail_src'] = $image[0];
			}
		}

		return $params;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_pagination_args' ) ) {
	function ideapark_woocommerce_pagination_args( $args ) {
		$args['prev_text'] = ideapark_svg( 'arrow-more', 'page-numbers__prev-svg' );
		$args['next_text'] = ideapark_svg( 'arrow-more', 'page-numbers__next-svg' );
		$args['end_size']  = 1;
		$args['mid_size']  = 1;

		return $args;
	}
}

if ( ! function_exists( 'ideapark_ajax_product_images' ) ) {
	function ideapark_ajax_product_images() {
		ob_start();
		if ( isset( $_REQUEST['product_id'] ) && ( $product_id = absint( $_REQUEST['product_id'] ) ) && ( $product = wc_get_product( $product_id ) ) ) {
			$variation_id   = isset( $_REQUEST['variation_id'] ) ? absint( $_REQUEST['variation_id'] ) : 0;
			$attachment_ids = $product->get_gallery_image_ids();
			$images         = [];
			if ( $variation_id && ( $attachment_id = get_post_thumbnail_id( $variation_id ) ) ) {
				array_unshift( $attachment_ids, $attachment_id );
			} else if ( $attachment_id = get_post_thumbnail_id( $product_id ) ) {
				array_unshift( $attachment_ids, $attachment_id );
			}
			foreach ( $attachment_ids as $attachment_id ) {
				$image    = wp_get_attachment_image_src( $attachment_id, 'full' );
				$images[] = [
					'src' => $image[0],
					'w'   => $image[1],
					'h'   => $image[2],
				];
			}

			if ( $video_url = get_post_meta( $product_id, '_ip_product_video_url', true ) ) {
				$images[] = [
					'html' => ideapark_wrap( wp_oembed_get( $video_url ), '<div class="pswp__video-wrap">', '</div>' )
				];
			}
			ob_end_clean();
			wp_send_json( [ 'images' => $images ] );
		}
		ob_end_clean();
	}
}

if ( ! function_exists( 'ideapark_ajax_product_tab' ) ) {
	function ideapark_ajax_product_tab() {
		ob_start();
		$content = '';
		if ( isset( $_REQUEST['tab'] ) && ( $tab = absint( $_REQUEST['tab'] ) ) && isset( $_REQUEST['index'] ) ) {

			if ( ideapark_mod( 'front_page_builder_enabled' ) ) {

				$index_id = abs( $_REQUEST['index'] );

				$sections = ideapark_parse_checklist( ideapark_mod( 'home_sections' ) );
				foreach ( $sections as $section => $is_enable ) {
					if ( $is_enable && $section == 'product-tabs' . ( $index_id ? '-' . $index_id : '' ) ) {


						if ( ! empty( $_REQUEST['filter_name'] ) && ( $filter_name = $_REQUEST['filter_name'] ) && ! empty( $_REQUEST['filter_value'] ) && ( $filter_value = $_REQUEST['filter_value'] ) ) {
							$attribute    = wc_sanitize_taxonomy_name( $filter_name );
							$taxonomy     = wc_attribute_taxonomy_name( $attribute );
							$filter_terms = explode( ',', wc_clean( wp_unslash( $filter_value ) ) );
							$tax_query    = [];

							if ( ! ( empty( $filter_terms ) || ! taxonomy_exists( $taxonomy ) || ! wc_attribute_taxonomy_id_by_name( $attribute ) ) ) {
								ideapark_mod_set_temp( 'shortcode_query_args_tax_query', [
									'taxonomy'         => $taxonomy,
									'terms'            => array_map( 'sanitize_title', $filter_terms ),
									'field'            => 'slug',
									'operator'         => 'AND',
									'include_children' => false,
								] );
							}
						}

						$postfix = $index_id ? '_' . $index_id : '';
						if ( ideapark_mod( 'home_tab_carousel' . $postfix ) ) {
							ideapark_mod_set_temp( 'product_grid_class', ideapark_mod( 'home_tab_carousel' . $postfix ) ? 'c-product-grid__list--carousel' : 'c-product-grid__list--center' );
						}
						$content = ideapark_shortcode( '[products category="' . $tab . '" per_page="' . ideapark_mod( 'home_tab_products' . $postfix ) . '"]' );
						ideapark_mod_set_temp( 'product_grid_class', null );
						break;
					}
				}
			}

			ob_end_clean();
			wp_send_json( [ 'content' => $content ] );
		}
		ob_end_clean();
	}
}

if ( ! function_exists( 'ideapark_woocommerce_save_shortcode_query_args' ) ) {
	function ideapark_woocommerce_save_shortcode_query_args( $query_args, $attributes, $type ) {
		if ( ideapark_mod( 'shortcode_fast_filter' ) ) {
			ideapark_mod_set_temp( 'woocommerce_last_shortcode_query_args', $query_args );
		}

		if ( ideapark_mod( 'shortcode_query_args_tax_query' ) ) {
			$query_args['tax_query'][] = ideapark_mod( 'shortcode_query_args_tax_query' );
		}

		return $query_args;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_shortcode_before_products_loop' ) ) {
	function ideapark_woocommerce_shortcode_before_products_loop() {
		if ( ideapark_mod( 'shortcode_fast_filter' ) && ( $qv = ideapark_mod( 'woocommerce_last_shortcode_query_args' ) ) && ( $filters = ideapark_horizontal_filter( $qv['tax_query'], $qv['meta_query'] ) ) ) { ?>
			<ul class="c-ordering__filter-list c-ordering__filter-list--shortcode">
			<?php foreach ( $filters as $filter ) {
				$content = ideapark_wrap( $filter['title'], '<span class="c-ordering__filter-button ' . ( $filter['chosen'] ? 'c-ordering__filter-button--chosen' : '' ) . '">', '</span>' );
				$content = ideapark_wrap( $content, '<a class="js-product-tab-filter" rel="nofollow" href="' . $filter['link'] . '" data-filter-name="' . esc_attr( $filter['filter_name'] ) . '" data-filter-value="' . esc_attr( $filter['filter_value'] ) . '">', '</a>' );
				echo ideapark_wrap( $content, '<li class="c-ordering__filter-item ' . ( $filter['chosen'] ? 'c-ordering__filter-list--chosen' : '' ) . '">', '</li>' );
			} ?>
			</ul><?php
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_before_widget_product_list' ) ) {
	function ideapark_woocommerce_before_widget_product_list( $content ) {
		return str_replace( 'product_list_widget', 'c-product-list-widget', $content );
	}
}

if ( ! function_exists( 'ideapark_wp_scrset_on' ) ) {
	function ideapark_wp_scrset_on( $name = '' ) {
		$f = 'add_filter';
		$n = 'wp_calculate_image_' . 'srcset';
		call_user_func( $f, $n, 'ideapark_woocommerce_srcset' . ( $name ? '_' : '' ) . $name, 10, 5 );
	}
}

if ( ! function_exists( 'ideapark_wp_scrset_off' ) ) {
	function ideapark_wp_scrset_off( $name = '' ) {
		$f = 'remove_filter';
		$n = 'wp_calculate_image_' . 'srcset';
		call_user_func( $f, $n, 'ideapark_woocommerce_srcset' . ( $name ? '_' : '' ) . $name, 10 );
	}
}

if ( ! function_exists( 'ideapark_wp_max_scrset_on' ) ) {
	function ideapark_wp_max_scrset_on( $name = '' ) {
		$f = 'add_filter';
		$n = 'max_srcset_image_' . 'width';
		call_user_func( $f, $n, 'ideapark_woocommerce_max_srcset_image_width' . ( $name ? '_' : '' ) . $name, 10, 2 );
	}
}

if ( ! function_exists( 'ideapark_wp_max_scrset_off' ) ) {
	function ideapark_wp_max_scrset_off( $name = '' ) {
		$f = 'remove_filter';
		$n = 'max_srcset_image_' . 'width';
		call_user_func( $f, $n, 'ideapark_woocommerce_max_srcset_image_width' . ( $name ? '_' : '' ) . $name, 10 );
	}
}

if ( ! function_exists( 'ideapark_woocommerce_srcset_grid' ) ) {
	function ideapark_woocommerce_srcset_grid( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		foreach ( $sources as $width => $data ) {
			if ( $width == '110' && ideapark_mod( 'product_mobile_layout' ) == 'layout-product-3' ) {

			} else if ( $width != $size_array[0] && $width != $size_array[0] * 2 ) {
				unset( $sources[ $width ] );
			}
		}

		return $sources;
	}
}

if ( ! function_exists( 'ideapark_woocommerce_srcset_retina' ) ) {
	function ideapark_woocommerce_srcset_retina( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		foreach ( $sources as $width => $data ) {
			if ( $width != $size_array[0] && $width != $size_array[0] * 2 ) {
				unset( $sources[ $width ] );
			}
		}

		return $sources;
	}
}

if ( ! function_exists( 'ideapark_product_images' ) ) {
	function ideapark_product_images() {
		global $post, $product;
		$images = [];

		if ( has_post_thumbnail() ) {

			$image = get_the_post_thumbnail( $post->ID, apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' ), [
				'alt'   => get_the_title( $post->ID ),
				'class' => 'c-product__gallery-img'
			] );

			$full = ideapark_mod( 'shop_product_modal' ) ? wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ) : false;

			ideapark_wp_scrset_on( 'retina' );
			$thumb = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_small_thumbnail_size', 'ideapark-single-product-thumb' ), [
				'alt'   => get_the_title( $post->ID ),
				'class' => 'c-product__thumbs-img'
			] );
			ideapark_wp_scrset_off( 'retina' );

			$images[] = [
				'attachment_id' => get_post_thumbnail_id( $post->ID ),
				'image'         => $image,
				'full'          => $full,
				'thumb'         => $thumb
			];
		}

		if ( $attachment_ids = $product->get_gallery_image_ids() ) {
			foreach ( $attachment_ids as $attachment_id ) {
				if ( ! wp_get_attachment_url( $attachment_id ) ) {
					continue;
				}

				$image = wp_get_attachment_image( $attachment_id, apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' ), false, [
					'alt'   => get_the_title( $attachment_id ),
					'class' => 'c-product__gallery-img'
				] );

				$full = ideapark_mod( 'shop_product_modal' ) ? wp_get_attachment_image_src( $attachment_id, 'full' ) : false;

				ideapark_wp_scrset_on( 'retina' );
				$thumb = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'ideapark-single-product-thumb' ), false, [
					'alt'   => get_the_title( $post->ID ),
					'class' => 'c-product__thumbs-img'
				] );
				ideapark_wp_scrset_off( 'retina' );

				$images[] = [
					'attachment_id' => $attachment_id,
					'image'         => $image,
					'full'          => $full,
					'thumb'         => $thumb
				];
			}
		}

		return $images;
	}
}

if ( ! function_exists( 'ideapark_product_markers' ) ) {
	function ideapark_product_markers( $class ) {
		global $product;

		$markers = [];

		if ( ( $marker_taxonomy = ideapark_mod( 'product_marker_attribute' ) ) && taxonomy_exists( $marker_taxonomy ) ) {
			$attributes = $product->get_attributes();
			if ( array_key_exists( $marker_taxonomy, $attributes ) && $attributes[ $marker_taxonomy ]->is_taxonomy() && ! $attributes[ $marker_taxonomy ]->get_variation() ) {
				$archive_taxonomie = '';

				if ( $attributes[ $marker_taxonomy ]->is_taxonomy() ) {
					$attribute_taxonomy = $attributes[ $marker_taxonomy ]->get_taxonomy_object();
					if ( $attribute_taxonomy->attribute_public ) {
						$archive_taxonomie = $marker_taxonomy;
					}
				}
				if ( $terms = $attributes[ $marker_taxonomy ]->get_terms() ) {
					foreach ( $terms as $term ) {
						$markers[] = ideapark_get_term_thumbnail( $term, $class, $archive_taxonomie );
					}
				}
			}
			$markers = array_filter( $markers );
		}

		return $markers;
	}
}

if ( ! function_exists( 'ideapark_product_extra_info' ) ) {
	function ideapark_product_extra_info() {
		global $product;
		if ( $extra_info_content = trim( get_post_meta( $product->get_id(), '_ip_product_extra_info', true ) ) ) {
			$extra_info_title = trim( get_post_meta( $product->get_id(), '_ip_product_extra_info_title', true ) );
			if ( empty( $extra_info_title ) ) {
				$extra_info_title = __( 'Nutritional facts', 'foodz' );
			}

			return [
				'title'   => $extra_info_title,
				'content' => $extra_info_content
			];
		}

		return false;
	}
}

if ( ! function_exists( 'ideapark_single_product_extra_info' ) ) {
	function ideapark_single_product_extra_info() {
		if ( $extra_info = ideapark_product_extra_info() ) { ?>
			<div class="c-product__info">
				<div class="c-product__info-title-wrap">
					<div class="c-product__info-title">
						<?php echo ideapark_svg( 'info', 'c-product__info-svg' ) ?><?php echo esc_html( $extra_info['title'] ) ?>
					</div>
					<div class="c-product__info-toggle">
						<?php echo ideapark_svg( 'select', 'c-product__info-toggle-svg' ) ?>
					</div>
				</div>
				<br>
				<div class="c-product__info-text"><?php echo nl2br( esc_html( $extra_info['content'] ) ); ?></div>
				<a class="c-product__info-link js-extra-info-toggle" href="#" onclick="return false;"></a>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'ideapark_woocommerce_template_single_markers' ) ) {
	function ideapark_woocommerce_template_single_markers() {
		if ( $markers = ideapark_product_markers( 'c-product_marker' ) ) {
			$extra_info_popup = false;
			if ( $extra_info = ideapark_product_extra_info() ) {
				$extra_info_popup = '<div class="c-product-grid__marker-popup js-extra-info-popup"><button class="h-cb c-product-grid__marker-popup-close js-extra-info-close" type="button">' . ideapark_svg( 'close-round', 'c-product-grid__marker-popup-svg' ) . '</button><div class="c-product-grid__marker-popup-title">' . esc_html( $extra_info['title'] ) . '</div><div class="c-product-grid__marker-popup-text">' . nl2br( esc_html( $extra_info['content'] ) ) . '</div></div>';
				$markers[]        = '<span class="c-markers__wrap c-product-grid__marker c-product-grid__marker--extra">' . ideapark_wrap( ideapark_svg( 'info' ), '<button class="h-cb h-cb--svg c-product__marker-info-icon c-product-grid__marker-info-icon js-extra-info" type="button">', '</button>' ) . '</span>';
			}
			?>
			<div class="c-markers c-product__markers">
				<?php echo ideapark_wrap( implode( '', $markers ) ); ?>
			</div>
			<?php if ( $extra_info_popup ) { ?>
				<?php echo ideapark_wrap( $extra_info_popup, '<div class="c-product__marker-popup">', '</div>' ); ?>
			<?php } ?>
		<?php }
	}
}

if ( ! function_exists( 'ideapark_woocommerce_before_add_to_cart_button' ) ) {
	function ideapark_woocommerce_before_add_to_cart_button() { ?>
		<div class="c-product__add-to-cart-wrap h-invisible<?php ideapark_class( ideapark_mod( 'mobile_layout' ) == 'layout-type-2' && ideapark_mod( 'product_mobile_single_ajax_add_to_cart' ), 'c-product__add-to-cart-wrap--sticky' ) ?>">
	<?php }
}

if ( ! function_exists( 'ideapark_woocommerce_after_add_to_cart_button' ) ) {
	function ideapark_woocommerce_after_add_to_cart_button() { ?>
		</div>
	<?php }
}

if ( ! function_exists( 'ideapark_product_wishlist' ) ) {
	function ideapark_product_wishlist() {
		if ( ideapark_mod( 'wishlist_page' ) ) { ?>
			<div
				class="c-product__wishlist"><?php Ideapark_Wishlist()->ideapark__button( 'h-cb c-product__wishlist-button', 'c-product__wishlist-svg' ) ?></div>
		<?php }
	}
}

if ( ! function_exists( 'ideapark_product_quantity' ) ) {
	function ideapark_product_quantity() { ?>
		<div
			class="c-product__quantity-wrap <?php ideapark_class( ideapark_mod( 'mobile_layout' ) == 'layout-type-2' && ideapark_mod( 'product_mobile_single_ajax_add_to_cart' ), 'c-product__quantity-wrap--sticky' ) ?>">
			<div class="c-quantity js-product-quantity"></div>
		</div>
	<?php }
}

if ( ! function_exists( 'ideapark_add_to_cart_ajax_notice' ) ) {
	function ideapark_add_to_cart_ajax_notice( $product_id ) {
		wc_add_to_cart_message( $product_id );
	}
}

if ( ! function_exists( 'ideapark_woocommerce_demo_store' ) ) {
	function ideapark_woocommerce_demo_store( $notice ) {
		return str_replace( 'woocommerce-store-notice ', 'woocommerce-store-notice woocommerce-store-notice--' . ideapark_mod( 'store_notice' ) . ' ', $notice );
	}
}

if ( ! function_exists( 'ideapark_woocommerce_product_tabs' ) ) {
	function ideapark_woocommerce_product_tabs( $tabs ) {
		$theme_tabs = ideapark_parse_checklist( ideapark_mod( 'product_tabs' ) );
		$priority   = 10;
		foreach ( $theme_tabs as $theme_tab_index => $enabled ) {
			if ( array_key_exists( $theme_tab_index, $tabs ) ) {
				if ( $enabled ) {
					$tabs[ $theme_tab_index ]['priority'] = $priority;
				} else {
					unset( $tabs[ $theme_tab_index ] );
				}
			}
			$priority += 10;
		}

		return $tabs;
	}
}

if ( IDEAPARK_THEME_IS_AJAX_IMAGES ) {
	add_action( 'wp_ajax_ideapark_product_images', 'ideapark_ajax_product_images' );
	add_action( 'wp_ajax_nopriv_ideapark_product_images', 'ideapark_ajax_product_images' );
} elseif ( IDEAPARK_THEME_IS_AJAX_QUANTITY ) {
	add_action( 'wp_ajax_ideapark_update_quantity', 'ideapark_ajax_update_quantity' );
	add_action( 'wp_ajax_nopriv_ideapark_update_quantity', 'ideapark_ajax_update_quantity' );
} else {
	add_action( 'wp_loaded', 'ideapark_setup_woocommerce' );
	add_action( 'woocommerce_before_shop_loop_item_title', 'ideapark_woocommerce_show_product_loop_badges', 9 );
	add_action( 'woocommerce_before_shop_loop', 'ideapark_woocommerce_search_form', 30 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );
	add_action( 'woocommerce_single_product_summary', 'ideapark_woocommerce_template_single_markers', 7 );
	add_action( 'woocommerce_single_product_summary', 'ideapark_single_product_extra_info', 19 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
	add_action( 'woocommerce_single_product_summary', 'ideapark_product_quantity', 31 );
	add_action( 'woocommerce_single_product_summary', 'ideapark_product_wishlist', 35 );

	add_action( 'wp_ajax_ideapark_product_tab', 'ideapark_ajax_product_tab' );
	add_action( 'wp_ajax_nopriv_ideapark_product_tab', 'ideapark_ajax_product_tab' );

	add_action( 'woocommerce_ajax_added_to_cart', 'ideapark_add_to_cart_ajax_notice' );

	add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	add_filter( 'woocommerce_show_variation_price', '__return_true' );
	add_filter( 'woocommerce_add_to_cart_fragments', 'ideapark_header_add_to_cart_fragment' );
	add_filter( 'woocommerce_product_add_to_cart_url', 'ideapark_woocommerce_product_add_to_cart_url', 1, 2 );
	add_filter( 'woocommerce_product_add_to_cart_text', 'ideapark_woocommerce_product_add_to_cart_text', 1, 2 );
	add_filter( 'woocommerce_breadcrumb_defaults', 'ideapark_woocommerce_breadcrumbs' );
	add_filter( 'woocommerce_account_menu_items', 'ideapark_woocommerce_account_menu_items' );
	add_filter( 'woocommerce_product_description_heading', 'ideapark_remove_product_description_heading' );
	add_filter( 'woocommerce_loop_add_to_cart_link', 'ideapark_loop_add_to_cart_link', 99, 3 );
	add_filter( 'woocommerce_gallery_image_size', 'ideapark_woocommerce_gallery_image_size', 99, 1 );
	add_filter( 'woocommerce_product_get_image', 'ideapark_lazyload_filter', 10, 1 );
	add_filter( 'woocommerce_loop_add_to_cart_args', 'ideapark_woocommerce_loop_add_to_cart_args', 99 );
	add_filter( 'woocommerce_available_variation', 'ideapark_woocommerce_available_variation', 100, 3 );
	add_filter( 'woocommerce_pagination_args', 'ideapark_woocommerce_pagination_args' );
	add_filter( 'subcategory_archive_thumbnail_size', 'ideapark_subcategory_archive_thumbnail_size', 99, 1 );
	add_filter( 'woocommerce_shortcode_products_query', 'ideapark_woocommerce_save_shortcode_query_args', 100, 3 );
	add_filter( 'woocommerce_shortcode_before_products_loop', 'ideapark_woocommerce_shortcode_before_products_loop' );
	add_filter( 'woocommerce_before_widget_product_list', 'ideapark_woocommerce_before_widget_product_list' );
	add_filter( 'woocommerce_demo_store', 'ideapark_woocommerce_demo_store' );
	add_filter( 'woocommerce_product_tabs', 'ideapark_woocommerce_product_tabs', 11 );
}

add_filter( 'woocommerce_product_subcategories_args', 'ideapark_woocommerce_hide_uncategorized' );
add_filter( 'woocommerce_product_categories_widget_args', 'ideapark_woocommerce_hide_uncategorized' );
add_filter( 'woocommerce_product_categories_widget_dropdown_args', 'ideapark_woocommerce_hide_uncategorized' );
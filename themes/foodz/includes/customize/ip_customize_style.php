<?php

function ideapark_customize_css( $is_return_value = false ) {

	$custom_css = '';

	$background_color         = esc_attr( ideapark_mod_hex_color_norm( 'background_color', '#FFFFFF' ) );
	$text_color               = esc_attr( ideapark_mod_hex_color_norm( 'text_color', '#000000' ) );
	$headers_color            = esc_attr( ideapark_mod_hex_color_norm( 'headers_color', 'inherit' ) );
	$buttons_background_color = esc_attr( ideapark_mod_hex_color_norm( 'buttons_background_color', 'transparent' ) );
	$buttons_text_color       = esc_attr( ideapark_mod_hex_color_norm( 'buttons_text_color', $headers_color ) );

	$custom_css .= '
	<style> 
		/*-- Header (Desktop) Custom Settings --*/
		
		@media (min-width: 1170px) {
			.c-header__row-1 {
				color: ' . ideapark_mod_hex_color_norm( 'header_row_1_color' ) . ';
			}
			.c-header__row-1:before {
				opacity: ' . esc_attr( (float) ( ideapark_mod( 'header_row_1_background_opacity' ) ) ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'header_row_1_background_color', 'transparent' ) . ';
			}
			
			.c-header__row-2 {
				color: ' . ideapark_mod_hex_color_norm( 'header_row_2_color' ) . ';
			}
			.c-header__row-2:before {
				opacity: ' . esc_attr( (float) ( ideapark_mod( 'header_row_2_background_opacity' ) ) ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'header_row_2_background_color', 'transparent' ) . ';
			}
			
			.c-header__row-3 {
				color: ' . ideapark_mod_hex_color_norm( 'header_row_3_color' ) . ';
			}
			.c-header__row-3:before {
				opacity: ' . esc_attr( (float) ( ideapark_mod( 'header_row_3_background_opacity' ) ) ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'header_row_3_background_color', 'transparent' ) . ';
			}
			
			.c-header__row-sticky {
				color: ' . ideapark_mod_hex_color_norm( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-1' ? 'header_row_3_color' : 'header_row_2_color' ) . ';
			}
			.c-header__row-sticky:before {
				opacity: ' . esc_attr( (float) ( ideapark_mod( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-1' ? 'header_row_3_background_opacity' : 'header_row_2_background_opacity' ) ) ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-1' ? 'header_row_3_background_color' : 'header_row_2_background_color', 'transparent' ) . ';
			}
			
			.c-header__row-sticky {
				background-color: ' . $background_color . ';
			}
			
			' . ( ideapark_mod( 'header_image' ) ? '
			
			.c-header__bg,
			.c-header__row-sticky{
				background-position: center center;
				background-image: url("' . esc_url( ideapark_mod( 'header_image' ) ) . '");
				background-size: ' . esc_attr( ideapark_mod( 'header_image_size' ) ) . ';
				background-repeat: ' . ( ideapark_mod( 'header_image_size' ) == 'auto' ? 'repeat' : 'no-repeat' ) . '; 
			}
			
			' : '
			.c-header__bg {
				display: none;
			}
			' ) . '
			
			.c-header__logo-img {
				max-width: ' . esc_attr( round( ideapark_mod( 'logo_size' ) ) ) . 'px; 
				max-height: ' . esc_attr( round( ideapark_mod( 'logo_size' ) ) ) . 'px;
			}
			
			.c-top-menu__submenu {
				color: ' . ideapark_mod_hex_color_norm( 'top_menu_submenu_color', $headers_color ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'top_menu_submenu_bg_color', '#FFFFFF' ) . ';
			}
			
			.c-mega-menu__submenu {
				color: ' . ideapark_mod_hex_color_norm( 'mega_menu_submenu_color', 'inherit' ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'mega_menu_submenu_bg_color', 'transparent' ) . ';
			}
			
			.c-mega-menu__item {
				max-width: ' . esc_attr( round( ideapark_mod( 'main_menu_width' ) ) ) . 'px;
				font-size: ' . ideapark_mod( 'main_menu_font_size' ) . 'px;
			}
		
			.c-mega-menu__item + .c-mega-menu__item {
				margin-left: ' . esc_attr( round( ideapark_mod( 'main_menu_space' ) ) ) . 'px;
			}
			
			.c-page-header {
				color: ' . $headers_color . ';
			}
			
			.c-page-header--category {
				color: ' . ideapark_mod_hex_color_norm( 'category_header_color', $headers_color ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'category_header_background_color', 'transparent' ) . ';
				background-position: ' . esc_attr( ideapark_mod( 'category_image_position' ) ) . ';
				background-size: ' . esc_attr( ideapark_mod( 'category_image_size' ) ) . ';
				background-repeat: no-repeat;
			}
			
			.c-product-grid__thumb-button {
				color: ' . $headers_color . ';
				background-color: ' . $background_color . ';
			} 
			
			/*- Hover -*/
			
			.c-ordering__filter-button:not(.c-ordering__filter-button--chosen):hover,
			.c-variation__title:hover,
			.c-post__tags a:hover,
			a.page-numbers:hover,
			a.post-page-numbers:hover {
				background-color: ' . ideapark_hex_to_rgba( $headers_color, 0.07 ) . ';
			}
			
			.c-header__search-button:hover,
			.c-header__button-link:hover {
				color: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( ideapark_mod( 'header_type' ) == 'header-type-4' ? 'header_row_1_color' : 'header_row_2_color', $headers_color ), 0.55 ) . ';
			}
			
			.c-mega-menu__item:hover > a {
				color: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( ideapark_mod( 'header_type' ) == 'header-type-3' || ideapark_mod( 'header_type' ) == 'header-type-4' ? 'header_row_2_color' : 'header_row_3_color', $headers_color ), 0.55 ) . ';
			}
			
			.c-header__text a:hover,
			.c-header__callback--second:hover,
			.c-header__button-link--account:hover{
				color: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( 'header_row_1_color', $headers_color ), 0.55 ) . ';
			}
			
			.c-variation__radio:checked + .c-variation__title:hover,
			.c-add-to-cart:hover,
			.c-quantity__minus:hover,
			.c-quantity__plus:hover,
			.widget .button.checkout:hover,
			.c-header__cart .widget_shopping_cart_content .button.checkout:hover{
				background-color: ' . ideapark_hex_to_rgb_shift( $buttons_background_color, 0.9 ) . '; 
			}
			
			.entry-content button:not(.h-cb):hover,
			.entry-content input[type=submit]:not(.h-cb):hover,
			.elementor-text-editor input[type=submit]:not(.h-cb):hover,
			.entry-content .wp-block-button__link:hover,
			.elementor-text-editor .wp-block-button__link:hover,
			.c-ordering__filter-button--chosen:hover,
			span.page-numbers:not(.dots):hover,
			span.post-page-numbers:not(.dots):hover,
			.pswp__button--arrow--left:hover,
			.pswp__button--arrow--right:hover,
			.pswp__button--close:hover,
			.pswp__button--zoom:hover,
			.c-form__button:hover,
			.entry-content input[type=submit]:not(.h-cb):hover,
			.comment-form .submit:hover,
			.widget .button:hover,
			.woocommerce-widget-layered-nav-dropdown__submit:hover,
			.wpcf7-form input[type=submit]:hover,
			.wpcf7-form button:hover,
			.woocommerce-Button:not(.woocommerce-Button--previous):not(.woocommerce-Button--next):hover,
			.woocommerce-address-fields .button:hover,
			.c-header__cart .widget_shopping_cart_content .button:hover,
			.c-subscribe .mc4wp-form input[type=submit]:hover,
			body #sb_instagram #sbi_load .sbi_load_btn:hover,
			body #sb_instagram .sbi_follow_btn a:hover,
			.woocommerce-widget-layered-nav-list__item.chosen:hover:before,
			.woocommerce-form-login button:hover {
				background-color: ' . ideapark_hex_to_rgb_shift( $headers_color, 0.7 ) . ';
			} 
			
			.entry-content a:not([class]):hover,
			.c-product__share-svg:hover,
			.product_meta a:hover,
			.woocommerce-product-attributes-item__value a:hover,
			.c-post__share-svg:hover,
			.comment-reply-link:hover,
			.comment-edit-link:hover {
				color: ' . $headers_color . ';
			}
			
		}
		
		/*-- Header (Mobile) Custom Settings --*/
		@media (max-width: 1169px) {
			.c-header__row-1 {
				color: ' . ideapark_mod_hex_color_norm( 'mobile_header_top_color', 'inherit' ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'mobile_header_top_background_color', 'transparent' ) . ';
			}
			
			.c-header__row-2 {
				color: ' . ideapark_mod_hex_color_norm( 'mobile_header_bottom_color', $headers_color ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'mobile_header_bottom_background_color', 'transparent' ) . ';
			}
			
			.c-header__row-2 .c-header__home:after {
				content: "' . esc_attr__( 'Home', 'foodz' ) . '";
			}
						
			.c-header__row-2 .c-header__wishlist:after {
				content: "' . esc_attr__( 'Wishlist', 'foodz' ) . '";
			}
			
			.c-header__row-2 .c-header__cart:after {
				content: "' . esc_attr__( 'Cart', 'foodz' ) . '";
			}
			
			.c-header__menu-wrap,
			.c-mega-menu__submenu {
				color: ' . ideapark_mod_hex_color_norm( 'mobile_menu_color', $headers_color ) . ';
				background-color: ' . ideapark_mod_hex_color_norm( 'mobile_menu_background_color', 'transparent' ) . ';
			}
			
			.c-shop-sidebar__wrap {
				color: ' . $text_color . ';
				background-color: ' . $background_color . ';
			}
			
			.c-page-header {
				color: ' . $headers_color . ';
			}
			
			.c-product-grid__thumb-button:before {
				background-color: ' . $background_color . ';
			}
			
			.c-product-grid__thumb-button {
				color: ' . $headers_color . ';
				background-color: ' . ideapark_hex_to_rgba( $headers_color, 0.07 ) . ';
			}
		}
		
		@media (max-width: 495px) {
			.c-quantity--sticky .c-quantity__value,
			.c-product__add-to-cart-wrap--sticky:before {
				color: ' . $headers_color . ';
				background-color: ' . $background_color . ';
			}
		}
		
		.c-header__callback-popup,
		.c-header-search__shadow,
		.c-header__menu-shadow,
		.pswp__bg,
		.c-shop-sidebar__shadow {
			background-color: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( 'shadow_color', $headers_color ), 0.7 ) . '; 
		}
		
		.c-product-grid__list--loading:before,
		.c-post-list__sticky:before,
		input[type=radio] + i,
		input[type=checkbox] + i{
			color: ' . $headers_color . ';
			background-color: ' . $background_color . ';
		} 
		
		.c-header-search__submit{
			background-color: ' . $headers_color . ';
		}
		
		.c-header__row-1:after {
			border-bottom-color: ' . ideapark_mod_hex_color_norm( 'header_row_1_border_color', 'transparent' ) . ';
		}
		
		.c-footer {
			color: ' . ideapark_mod_hex_color_norm( 'footer_text_color' ) . ';
			background-color: ' . ideapark_mod_hex_color_norm( 'footer_background_color', 'transparent' ) . ';
			' . ( ideapark_mod( 'footer_image' ) ? '
				background-position: center top;
				background-image: url("' . esc_url( ideapark_mod( 'footer_image' ) ) . '");
				background-size: ' . esc_attr( ideapark_mod( 'footer_image_size' ) ) . ';
				background-repeat: ' . ( ideapark_mod( 'footer_image_size' ) == 'auto' ? 'repeat' : 'no-repeat' ) . '; 
			' : '' ) . '
		}
		
		.c-footer__widgets .widget-title,
		.c-footer__widgets .c-product-list-widget__title,
		.c-footer__widgets .widget_calendar caption,
		.c-footer__widgets .woocommerce-widget-layered-nav-list {
			color: ' . ideapark_mod_hex_color_norm( 'footer_header_color' ) . ';
		}
		
		.c-footer__logo-img {
			max-width: ' . esc_attr( round( ideapark_mod( 'logo_footer_size' ) ) ) . 'px; 
			max-height: ' . esc_attr( round( ideapark_mod( 'logo_footer_size' ) ) ) . 'px;
		}
		
		.c-badge--featured {
			background-color: ' . ideapark_mod_hex_color_norm( 'featured_badge_color', 'currentColor' ) . ';
		}
		
		.c-badge--new {
			background-color: ' . ideapark_mod_hex_color_norm( 'new_badge_color', 'currentColor' ) . ';
		}
		
		.c-badge--sale {
			background-color: ' . ideapark_mod_hex_color_norm( 'sale_badge_color', 'currentColor' ) . ';
		}
		
		.c-wishlist__td--product-price del,
		.c-cart__shop-td--product-price del,
		.c-cart__shop-variation,
		.c-cart__shipping-calculator-button,
		.c-cart__totals-product-quantity,
		.c-cart__payment-methods-box,
		.c-header__cart .widget_shopping_cart_content,
		.c-header__cart .c-product-list-widget__total .tax_label,
		.c-header__callback-popup{
			color: ' . $text_color . ';
		}
		
		button,
		input,
		optgroup,
		select,
		textarea,
		.c-header,
		.c-sub-categories,
		.c-header-search__input,
		.c-product-grid__title,
		.c-variation__title,
		.c-variation__select,
		.c-product-grid__item .price,
		.c-product .price,
		.c-home-tabs__title,
		.c-home-tabs__header-list,
		.c-home-tabs__header-select,
		.c-subscribe__header,
		.c-block__header,
		body #sb_instagram .sbi_header_text h3,
		.c-icons__header,
		.c-brands__header,
		.c-testimonials__header,
		.c-testimonials__author,
		.c-posts__header,
		.c-post-list__categories,
		.c-post-list__header,
		.c-post-list__continue,
		.entry-content h1,
		.entry-content h2,
		.entry-content h3,
		.entry-content h4,
		.entry-content h5,
		.entry-content h6,
		.entry-content blockquote,
		.entry-content .wp-block-quote,
		.c-post-list__sticky,
		.widget-title,
		.c-lp-widget__title,
		.widget_calendar caption,
		.c-post__categories,
		.c-post__author,
		.c-product-list-widget__title,
		.woocommerce-widget-layered-nav-list,
		.c-product__title,
		.c-product__quantity,
		.c-product__marker,
		.c-product__tabs-list,
		.c-product__products-title,
		.c-product__tabs-custom-select,
		.c-product__tabs-custom-title,
		.woocommerce-grouped-product-list,
		.woocommerce-review__author,
		.comment-reply-title,
		.c-post__tags a,
		.c-post__nav-title,
		.commentlist .comment-author,
		.comments-title,
		.woocommerce-notice,
		.c-404__header,
		.c-404__svg,
		.c-cart-empty__header,
		.c-cart-empty__svg,
		.c-wishlist__td,
		.c-wishlist-empty__header,
		.c-wishlist-empty__icon,
		.c-cart__shop-td,
		.c-cart__header,
		.c-cart__sub-header,
		.c-cart__collaterals,
		.c-header__cart .c-product-list-widget__total,
		.c-header__callback-header,
		.c-header__callback-close,
		.c-account__navigation,
		.c-account__login-info,
		.c-account legend,
		.c-account__address-title,
		.c-account__address-edit,
		.c-account h3,
		.c-order__result-message,
		.c-order__details-value,
		.woocommerce-table,
		.woocommerce-order-details__title,
		.woocommerce-column__title,
		.woocommerce-orders-table td,
		.woocommerce-bacs-bank-details,
		.woocommerce-button--previous,
		.woocommerce-button--next,
		.c-product__wishlist-button,
		.c-cart__cross-sell h2,
		.c-countdown,
		.c-home-promo__title,
		.c-cart__form .woocommerce-input-wrapper {
			color: ' . $headers_color . ';
		}
		
		.select2-container--default .select2-selection--single .select2-selection__placeholder,
		.select2-container--default .select2-selection--single .select2-selection__rendered {
			color: ' . $headers_color . '!important;
		}
		
		.c-variation__radio:checked + .c-variation__title,
		.c-add-to-cart,
		.c-quantity__minus,
		.c-quantity__plus,
		.widget .button.checkout,
		.c-header__cart .widget_shopping_cart_content .button.checkout {
			background-color: ' . $buttons_background_color . '; 
			color: ' . $buttons_text_color . ';
		}
		
		.woocommerce-notices-wrapper--ajax,
		.blockUI.blockOverlay,
		.c-header__cart .widget_shopping_cart_content:before {
			background-color: ' . $background_color . ' !important;
		} 
		
		.price_slider .ui-slider-handle {
			border-color: ' . $buttons_background_color . ';
			background-color: ' . $background_color . ';
		}
		
		.entry-content button:not(.h-cb),
		.entry-content input[type=submit]:not(.h-cb),
		.elementor-text-editor input[type=submit]:not(.h-cb),
		.entry-content .wp-block-button__link,
		.elementor-text-editor .wp-block-button__link,
		.c-ordering__filter-button--chosen,
		span.page-numbers:not(.dots),
		span.post-page-numbers:not(.dots),
		.pswp__button--arrow--left,
		.pswp__button--arrow--right,
		.pswp__button--close,
		.pswp__button--zoom,
		.c-form__button,
		.entry-content input[type=submit]:not(.h-cb),
		.comment-form .submit,
		.widget .button,
		.woocommerce-widget-layered-nav-dropdown__submit,
		.wpcf7-form input[type=submit],
		.wpcf7-form button,
		.woocommerce-Button:not(.woocommerce-Button--previous):not(.woocommerce-Button--next),
		.woocommerce-address-fields .button,
		.c-header__cart .widget_shopping_cart_content .button,
		.c-subscribe .mc4wp-form input[type=submit],
		body #sb_instagram #sbi_load .sbi_load_btn,
		body #sb_instagram .sbi_follow_btn a,
		.woocommerce-widget-layered-nav-list__item.chosen:before,
		.woocommerce-form-login button {
			background-color: ' . $headers_color . ';
			color: ' . $background_color . ';
		}
		
		.c-home-tabs__view-more-button,
		.c-product-grid__item-view-more{
			color: ' . $background_color . ';
		}
		
		.c-home-tabs__view-more-button:before,
		.c-product-grid__item-view-more:before,
		.c-product-grid__item--view-more:after,
		.entry-content blockquote:before,
		.entry-content .wp-block-quote:before{
			background-color: ' . $headers_color . ';
		}
		
		.c-mega-menu__label {
			background-color: ' . ideapark_mod_hex_color_norm( 'main_menu_label_color', 'currentColor' ) . ';
		}

		.c-mega-menu__label:before {
			border-top-color: ' . ideapark_mod_hex_color_norm( 'main_menu_label_color', 'currentColor' ) . ';
		}
		
		.c-header__cart-count {
			color: ' . ideapark_mod_hex_color_norm( 'mobile_cart_counter_color', $headers_color ) . ';
			background-color: ' . ideapark_mod_hex_color_norm( 'mobile_cart_counter_background_color', 'transparent' ) . ';
		}
		
		.c-page-header__title,
		.c-home-banners__title,
		.c-home-tabs__title,
		.c-home-tabs__header-list,
		.c-home-tabs__header-select,
		.c-product__title,
		.c-404__header,
		.c-cart-empty__header,
		.c-wishlist-empty__header,
		.c-home-promo__title {
			font-family: "' . esc_attr( ideapark_mod( 'theme_font_1' ) ) . '", sans-serif !important;
			font-weight: ' . esc_attr( ideapark_mod( 'theme_font_1_weight' ) ) . ' !important;
		}
		
		.c-home-banners__subheader {
			font-family: "' . esc_attr( ideapark_mod( 'theme_font_1' ) ) . '", sans-serif !important;
			font-weight: ' . esc_attr( ideapark_mod( 'theme_font_1_weight_2' ) ) . ' !important;
		}
		
		.c-header-search__input,
		.c-mega-menu__item,
		.c-sub-categories,
		.c-product-grid__title,
		.c-product-grid__item .price,
		.c-product .price:not(.woocommerce-grouped-product-list-item__price),
		.c-wishlist__td--product-price,
		.c-cart__shop-td--product-price,
		.c-footer__phone,
		.c-home-banners__button,
		.c-subscribe__header,
		.c-block__header,
		body #sb_instagram .sbi_header_text h3,
		.c-icons__header,
		.c-brands__header,
		.c-testimonials__header,
		.c-testimonials__author,
		.c-posts__header,
		.c-post-list__header,
		.entry-content h1,
		.entry-content h2,
		.entry-content h3,
		.entry-content h4,
		.entry-content h5,
		.entry-content h6,
		.entry-content blockquote,
		.entry-content .wp-block-quote,
		.widget-title,
		.c-lp-widget__title,
		.widget_calendar caption,
		.c-product__tabs-list,
		.c-product__products-title,
		.c-product__tabs-custom-select,
		.c-product__tabs-custom-title,
		.comment-reply-title,
		.comments-title,
		.c-cart__totals-price--total strong .amount,
		.c-product-list-widget__total,
		.c-account__navigation,
		.c-order__result-message,
		.c-cart__cross-sell h2,
		.c-countdown {
			font-family: "' . esc_attr( ideapark_mod( 'theme_font_2' ) ) . '", sans-serif !important;
			font-weight: ' . esc_attr( ideapark_mod( 'theme_font_2_weight' ) ) . ' !important;
		}
		
		.c-mega-menu__submenu,
		.c-mega-menu__label,
		.c-product-grid__item .price > del,
		.c-wishlist__td--product-price del,
		.c-cart__shop-td--product-price del,
		.c-product .price:not(.woocommerce-grouped-product-list-item__price) > del,
		.c-footer__widgets .widget-title,
		.c-product__sidebar .c-advantage .widget-title{
			font-family: "' . esc_attr( ideapark_mod( 'theme_font_3' ) ) . '", sans-serif !important;
		}
		
		.c-header-search__clear-text,
		.c-product-grid__item .price > del,
		.c-product .price:not(.woocommerce-grouped-product-list-item__price) > del,
		.c-cart__shop-update,
		.c-cart__collaterals .woocommerce-remove-coupon,
		.c-cart__collaterals .tax_label,
		.c-cart__collaterals .includes_tax,
		.c-cart__shop-table .tax_label,
		.c-cart__shop-th,
		.c-cart__totals-th,
		.woocommerce-table thead th {
			color: ' . $text_color . ';
		} 
		
		body,
		.c-page-header__title-or,
		.c-page-header__tab-login--not-active,
		.c-page-header__tab-register--not-active{
			font-family: "' . esc_attr( ideapark_mod( 'theme_font_3' ) ) . '", sans-serif !important;
			color: ' . $text_color . ';
		}
		
		.c-mega-menu__item--small {
			font-family: "' . esc_attr( ideapark_mod( 'theme_font_3' ) ) . '", sans-serif !important;
			color: ' . ideapark_hex_to_rgba( ideapark_mod_hex_color_norm( 'mobile_menu_color', $headers_color ), 0.7 ) . ';
		}
		
		.star-rating:before,
		.star-rating span:before,
		.comment-form-rating .stars a {
			background-image:  url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg fill="' . ideapark_mod_hex_color_norm( 'star_rating_color', $text_color ) . '" width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5T1385 1619q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5T365 1569q0-6 2-20l86-500L89 695q-25-27-25-48 0-37 56-46l502-73L847 73q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg>' ) . '") !important;
		}
		
		.h-wave:after,
		.entry-content hr{
			background-image:  url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="15" height="6" viewBox="0 0 15 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 5C2.8699 5 4.15585 1 7.5 1C10.8441 1 11.6559 5 15 5" stroke="' . ideapark_mod_hex_color_norm( 'wave_color', $headers_color ) . '" stroke-width="2"/></svg>' ) . '") !important;
		}
	
		.woocommerce-widget-layered-nav-list__item.chosen:after,
		input[type=checkbox]:checked + i:after{
			background-image:  url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="11" height="7" viewBox="0 0 11 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.2072 1.70706L5.20718 6.70706C4.81666 7.09758 4.18349 7.09758 3.79297 6.70706L0.792969 3.70706L2.20718 2.29285L4.50008 4.58574L8.79297 0.292847L10.2072 1.70706Z" fill="' . ideapark_mod_hex_color_norm( $background_color, '#FFFFFF' ) . '"/></svg>' ) . '") !important;
		}
		
		.c-cart__collaterals:after {
			background-image:  url("data:image/svg+xml;base64,' . ideapark_b64enc( '<svg width="20" height="8" xmlns="http://www.w3.org/2000/svg"><path fill="' . ideapark_hex_to_rgb_overlay( $background_color, $headers_color, 0.07 ) . '" fill-rule="nonzero" d="M18 0H0l9 7.5z"/></svg>' ) . '") !important;
		}
		
		.c-product__image .h-after-before-hide {
			background-color: ' . ideapark_hex_to_rgba( $background_color, 0.8 ) . ';
		}
		
		.c-wishlist__th,
		.c-cart__shop-th,
		.c-cart__totals-th,
		.woocommerce-table th,
		.woocommerce-orders-table th {
			border-color: ' . ideapark_hex_to_rgba( $headers_color, 0.3 ) . ';
		}
		
		.c-wishlist__td,
		.c-cart__shop-td,
		.c-cart__coupon:after,
		.c-cart__collaterals-hr:after,
		.c-cart__totals-space--hr:after,
		.woocommerce-table td,
		.woocommerce-table tfoot th,
		.woocommerce-orders-table td,
		.c-product__thumbs-item--video,
		.c-header__callback-wrap {
			border-color: ' . ideapark_hex_to_rgba( $headers_color, 0.15 ) . ' !important;
		}
		
		input[type=search]:not(.c-form__input--fill):not(.h-cb),
		input[type=text]:not(.c-form__input--fill):not(.h-cb),
		input[type=password]:not(.c-form__input--fill):not(.h-cb),
		input[type=email]:not(.c-form__input--fill):not(.h-cb),
		input[type=tel]:not(.c-form__input--fill):not(.h-cb),
		input[type=number]:not(.c-form__input--fill):not(.h-cb),
		input[type=url]:not(.c-form__input--fill):not(.h-cb),
		textarea:not(.c-form__input--fill):not(.h-cb):not([class*="block-editor"]),
		input[type=radio] + i,
		input[type=checkbox] + i,
		select:not(.c-form__input--fill):not(.h-cb):not(.components-select-control__input),
		.select2-selection--single,
		.select2-selection--multiple,
		.c-countdown__item {
			background-color: ' . $background_color . ';
		}
		
		input:-webkit-autofill,
		textarea:-webkit-autofill,
		select:-webkit-autofill {
			-webkit-box-shadow: 0 0 0 1000px ' . $background_color . ' inset, 0 0 3px ' . ideapark_hex_to_rgba( $headers_color, 0.15 ) . ';
			-webkit-text-fill-color: ' . $headers_color . ';
			outline: 2px;
		}
		
		input[type=search]:not(.h-cb),
		input[type=text]:not(.h-cb),
		input[type=password]:not(.h-cb),
		input[type=email]:not(.h-cb),
		input[type=tel]:not(.h-cb),
		input[type=number]:not(.h-cb),
		input[type=url]:not(.h-cb),
		textarea:not(.h-cb),
		input[type=radio] + i,
		input[type=checkbox] + i,
		select:not(.h-cb),
		.select2-selection--single,
		.select2-selection--multiple {
			border-color: ' . ideapark_hex_to_rgb_overlay( $background_color, $headers_color, 0.15 ) . ' !important;
		}
		
		.select2-selection--single,
		.select2-selection--multiple,
		.c-header__callback-wrap,
		.select2-dropdown,
		.c-header-search__wrap {
			background-color: ' . $background_color . ' !important;
		}
		
		input[type=search]:not(.h-cb):focus,
		input[type=text]:not(.h-cb):focus,
		input[type=password]:not(.h-cb):focus,
		input[type=email]:not(.h-cb):focus,
		input[type=tel]:not(.h-cb):focus,
		input[type=number]:not(.h-cb):focus,
		input[type=url]:not(.h-cb):focus,
		textarea:not(.h-cb):focus,
		input[type=radio]:focus + i,
		input[type=checkbox]:focus + i,
		select:not(.h-cb):focus,
		.select2-container--open .select2-selection--multiple, 
		.select2-container--open .select2-selection--single,
		.select2-dropdown {
			border-color: ' . ideapark_hex_to_rgba( $headers_color, 0.5 ) . ' !important;
		}
		
		.select2-results__option[aria-selected=true], 
		.select2-results__option[data-selected=true] {
			background-color: ' . ideapark_hex_to_rgba( $headers_color, 0.15 ) . ' !important;
		}
		
		.select2-results__option--highlighted[aria-selected], 
		.select2-results__option--highlighted[data-selected] {
			background-color: ' . $headers_color . ' !important;
		}
		
		.select2-container--default .select2-results__option--highlighted[aria-selected], 
		.select2-container--default .select2-results__option--highlighted[data-selected] {
			background-color: ' . ideapark_hex_to_rgba( $headers_color, 0.2 ) . ' !important;
		}
		
		.c-cart__collaterals,
		.c-product__quantity-value,
		.c-product__wishlist-button,
		.c-product__video-svg-wrap,
		.c-product__add-to-cart-wrap--sticky:after,
		.c-quantity__value,
		.woocommerce-notice,
		.c-form__textarea--dark,
		.comment-form textarea,
		.c-cart .woocommerce-terms-and-conditions,
		.c-form__input--fill,
		.c-form__textarea--fill,
		.comment-form textarea,
		.comment-form input[type=text],
		.comment-form input[type=tel],
		.comment-form input[type=password],
		.comment-form input[type=email],
		.comment-form input[type=url],
		.wpcf7-form input[type=text],
		.wpcf7-form input[type=tel],
		.wpcf7-form input[type=password],
		.wpcf7-form input[type=number],
		.wpcf7-form input[type=url],
		.wpcf7-form input[type=email],
		.wpcf7-form textarea,
		.woocommerce-ResetPassword input[type=text],
		.c-order__details-item,
		.entry-content pre,
		.entry-content tr.odd td,
		.comment-content tr.odd td {
			background-color: ' . ideapark_hex_to_rgb_overlay( $background_color, $headers_color, 0.07 ) . '!important;
		}
		
		.c-to-top-button {
			background-color: ' . ideapark_mod_hex_color_norm( 'to_top_button_color' ) . ';
			color: ' . $background_color . ';
		}
		
		.woocommerce-store-notice {
			background-color: ' . ideapark_mod_hex_color_norm( 'store_notice_background_color' ) . ';
			color: ' . ideapark_mod_hex_color_norm( 'store_notice_color' ) . ';
		}
		
		.woocommerce-store-notice__dismiss-link {
			background-color: ' . ideapark_mod_hex_color_norm( 'store_notice_color' ) . ';
			color: ' . ideapark_mod_hex_color_norm( 'store_notice_background_color' ) . ';
		}
	</style>';

	$custom_css = preg_replace( '~[\r\n]~', '', preg_replace( '~[\t\s]+~', ' ', str_replace( [
		'<style>',
		'</style>'
	], [ '', '' ], $custom_css ) ) );

	if ( $custom_css ) {
		if ( $is_return_value ) {
			return $custom_css;
		} else {
			wp_add_inline_style( 'ideapark-core', $custom_css );
		}
	}
}

function ideapark_uniord( $u ) {
	$k  = mb_convert_encoding( $u, 'UCS-2LE', 'UTF-8' );
	$k1 = ord( substr( $k, 0, 1 ) );
	$k2 = ord( substr( $k, 1, 1 ) );

	return $k2 * 256 + $k1;
}

function ideapark_b64enc( $input ) {

	$keyStr = "ABCDEFGHIJKLMNOP" .
	          "QRSTUVWXYZabcdef" .
	          "ghijklmnopqrstuv" .
	          "wxyz0123456789+/" .
	          "=";

	$output = "";
	$i      = 0;

	do {
		$chr1 = ord( substr( $input, $i ++, 1 ) );
		$chr2 = $i < strlen( $input ) ? ord( substr( $input, $i ++, 1 ) ) : null;
		$chr3 = $i < strlen( $input ) ? ord( substr( $input, $i ++, 1 ) ) : null;

		$enc1 = $chr1 >> 2;
		$enc2 = ( ( $chr1 & 3 ) << 4 ) | ( $chr2 >> 4 );
		$enc3 = ( ( $chr2 & 15 ) << 2 ) | ( $chr3 >> 6 );
		$enc4 = $chr3 & 63;

		if ( $chr2 === null ) {
			$enc3 = $enc4 = 64;
		} else if ( $chr3 === null ) {
			$enc4 = 64;
		}

		$output = $output .
		          substr( $keyStr, $enc1, 1 ) .
		          substr( $keyStr, $enc2, 1 ) .
		          substr( $keyStr, $enc3, 1 ) .
		          substr( $keyStr, $enc4, 1 );
		$chr1   = $chr2 = $chr3 = "";
		$enc1   = $enc2 = $enc3 = $enc4 = "";
	} while ( $i < strlen( $input ) );

	return $output;
}
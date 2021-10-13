(function ($, root, undefined) {
	"use strict";
	
	$.migrateMute = true;
	$.migrateTrace = false;
	
	var old_define;
	if (!ideapark_empty(requirejs)) {
		requirejs.config({
			baseUrl    : ideapark_wp_vars.themeUri + '/assets/js',
			paths      : {
				text: 'requirejs/text',
				css : 'requirejs/css',
				json: 'requirejs/json'
			},
			urlArgs    : function (id, url) {
				var args = '';
				
				if (url.indexOf('/css/photoswipe/') !== -1) {
					args = 'v=' + ideapark_wp_vars.stylesHash;
				}
				
				if (id === 'photoswipe/photoswipe.min' || id === 'photoswipe/photoswipe-ui-default.min') {
					args = 'v=' + ideapark_wp_vars.scriptsHash;
				}
				
				return args ? (url.indexOf('?') === -1 ? '?' : '&') + args : '';
			},
			waitSeconds: 0
		});
		
		old_define = root.define;
		root.define = null;
	}
	
	root.ideapark_videos = [];
	root.ideapark_players = [];
	root.ideapark_env_init = false;
	root.ideapark_slick_paused = false;
	root.ideapark_is_mobile = false;
	
	root.old_windows_width = 0;
	
	var $window = $(window);
	var ideapark_scroll_busy = true;
	var ideapark_resize_busy = true;
	var ideapark_parallax_on = !!$('.parallax,.parallax-lazy').length && typeof simpleParallax !== 'undefined';
	
	var $ideapark_masonry_grid = $('.js-masonry');
	var ideapark_masonry_grid_on = !!$ideapark_masonry_grid.length;
	var ideapark_is_masonry_init = false;
	var $ideapark_header_bg = $('.js-header-bg');
	var ideapark_header_bg_height_init = false;
	var $ideapark_mobile_menu = $('.js-mobile-menu');
	var $ideapark_mobile_menu_open = $('#ideapark-mobile-menu-button');
	var $ideapark_mobile_menu_close = $('#ideapark-menu-close');
	var $ideapark_mobile_menu_back = $('#ideapark-menu-back');
	var $ideapark_mobile_menu_wrap = $('.c-header__menu-wrap');
	var $ideapark_mobile_menu_content = $('.c-header__menu-content');
	var $ideapark_mobile_submenu = [];
	var ideapark_mobile_menu_initialized = false;
	var ideapark_mobile_menu_active = false;
	var ideapark_shop_sidebar_active = false;
	var $ideapark_shop_sidebar = $('.js-shop-sidebar');
	var $ideapark_shop_sidebar_open = $('#ideapark-shop-sidebar-button');
	var $ideapark_shop_sidebar_close = $('#ideapark-shop-sidebar-close');
	var $ideapark_shop_sidebar_wrap = $('.c-shop-sidebar__wrap');
	var $ideapark_shop_sidebar_content = $('.c-shop-sidebar__content--mobile');
	var ideapark_shop_sidebar_initialized = false;
	var $ideapark_search = $('#ideapark-ajax-search');
	var $ideapark_search_input = $('#ideapark-ajax-search-input');
	var $ideapark_search_result = $('#ideapark-ajax-search-result');
	var $ideapark_search_loader = $('<i class="h-loading c-header-search__loading"></i>');
	var ideapark_search_popup_active = false;
	var ideapark_search_input_filled = false;
	var ideapark_on_transition_end = 'transitionend webkitTransitionEnd oTransitionEnd';
	var $ideapark_callback_popup = $('.c-header__callback-popup');
	var ideapark_is_mobile_layout = window.innerWidth < 1170;
	var ideapark_layout_2 = window.innerWidth <= 495;
	var ideapark_layout_3 = window.innerWidth < 768;
	var ideapark_layout_4 = window.innerWidth < 600;
	var ideapark_mega_menu_break_init = false;
	var ideapark_mega_menu_to_left = false;
	var $ideapark_desktop_mega_menu;
	var ideapark_desktop_mega_menu_top;
	var ideapark_desktop_mega_menu_height;
	var $ideapark_desktop_sticky_row = $('.js-desktop-sticky-menu');
	var $ideapark_desktop_sticky_spacer;
	var $ideapark_desktop_sticky_cart;
	var $ideapark_mobile_sticky_row = $('.js-mobile-sticky-menu');
	var ideapark_sticky_active = false;
	var ideapark_sticky_desktop_init = false;
	var ideapark_sticky_mobile_init = false;
	var $ideapark_admin_bar;
	var $ideapark_quantity_input;
	var ideapark_adminbar_height = 0;
	var ideapark_adminbar_visible_height = 0;
	var ideapark_adminbar_position = 0;
	var $ideapark_to_top_button = $('.js-to-top-button');
	var $ideapark_sticky_sidebar = $('.js-sticky-sidebar');
	var $ideapark_sticky_sidebar_nearby = $('.js-sticky-sidebar-nearby');
	var ideapark_sticky_sidebar_old_style = null;
	var ideapark_notice_top = 0;
	var ideapark_simple_parallax_instances = [];
	
	var $slick_product_single = $('.slick-product-single');
	var $slick_product_single_slides = $('.slide', $slick_product_single);
	var $slick_product_thumbnails = $('.slick-product');
	
	$(function () {
		root.define = old_define;
		
		$ideapark_to_top_button.click(function () {
			$('html, body').animate({scrollTop: 0}, 800);
		});
		
		$('.js-mega-menu a[href^="#"], .js-mobile-top-menu a[href^="#"], .js-top-menu a[href^="#"]').click(ideapark_hash_menu_animate);
		
		$('.c-add-to-cart.disabled:not(.js-add-to-cart-variation)').removeClass('disabled');
		
		$('input[type=radio]:not(.h-cb),input[type=checkbox]:not(.h-cb)').each(function () {
			var $this = $(this);
			if ($this.next().prop("tagName") !== 'I') {
				$this.after('<i></i>');
			}
		});
		
		$('.c-top-menu--loading').each(function () {
			var $this = $(this);
			var containerWidth = $this.outerWidth();
			var sum = 0;
			$this.find('.c-top-menu__item').each(function () {
				var $item = $(this);
				if (sum > containerWidth) {
					$item.addClass('c-top-menu__item--hidden');
					return;
				}
				var width = $item.outerWidth() + parseInt($item.css('margin-left').replace('px', ''));
				sum += width;
				if (sum > containerWidth) {
					$item.addClass('c-top-menu__item--hidden');
				}
			});
			$this.removeClass('c-top-menu--loading');
		});
		
		if ($ideapark_callback_popup.length) {
			$('.c-header__callback-popup--disabled').removeClass('c-header__callback-popup--disabled');
			$('.js-callback').click(function () {
				ideapark_mobile_menu_popup(false);
				$ideapark_callback_popup.addClass('c-header__callback-popup--active');
				bodyScrollLock.disableBodyScroll($('.c-header__callback-wrap')[0]);
			});
			
			$('.js-callback-close').click(function () {
				$('.c-header__callback-popup--disabled').removeClass('c-header__callback-popup--disabled');
				$ideapark_callback_popup.toggleClass('c-header__callback-popup--active');
				bodyScrollLock.clearAllBodyScrollLocks();
			});
			
			$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
				$ideapark_callback_popup.css({
					transform   : 'translateY(' + wpadminbar_height + 'px)',
					'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
				});
			});
		}
		
		$('.js-tab-header').click(function (e) {
			var $this = $(this);
			e.preventDefault();
			if ($this.hasClass('c-page-header__tab-register')) {
				$this.addClass('c-page-header__tab-register--active').removeClass('c-page-header__tab-register--not-active');
				$('.c-page-header__tab-login--active').removeClass('c-page-header__tab-login--active').addClass('c-page-header__tab-login--not-active');
			} else {
				$this.addClass('c-page-header__tab-login--active').removeClass('c-page-header__tab-login--not-active');
				$('.c-page-header__tab-register--active').removeClass('c-page-header__tab-register--active').addClass('c-page-header__tab-register--not-active');
			}
			$('.c-login__form--active').removeClass('c-login__form--active');
			$('.' + $this.data('tab-class')).addClass('c-login__form--active');
		});
		
		$('.single_variation').on('hide_variation', function (e) {
			var $form = $(this).closest('form');
			$form.find('.c-variation__single-info').html($form.find('.c-variation__single-price').html());
		});
		
		$('.c-product__summary .js-variations-form').on('show_variation', function (e, variation) {
			var $this = $(this);
			var $availability_text = $this.find('.woocommerce-variation-availability p');
			var $ip_stock = $('.c-stock');
			
			if ($availability_text.length) {
				var in_stock = $this.find('.woocommerce-variation-availability .in-stock').length;
				var stock_html = $availability_text.html();
				if (in_stock) {
					$ip_stock.removeClass('c-stock--out-of-stock out-of-stock')
						.addClass('c-stock--in-stock in-stock')
						.html(stock_html);
				} else {
					$ip_stock.removeClass('c-stock--in-stock in-stock')
						.addClass('c-stock--out-of-stock out-of-stock')
						.html(stock_html);
				}
			} else {
				$ip_stock.removeClass('c-stock--in-stock in-stock c-stock--out-of-stock out-of-stock')
					.html('');
			}
		});
		
		$('.js-countdown').each(function () {
			var $this = $(this),
				finalDate = $(this).data('date'),
				_w = $(this).data('week'),
				_d = $(this).data('day'),
				_h = $(this).data('hour'),
				_m = $(this).data('minute'),
				_s = $(this).data('second'),
				_bg = $(this).data('bg'),
				_color = $(this).data('color');
			if (finalDate) {
				if (_bg) {
					_bg = ' style="background-color:' + _bg + '" ';
				}
				if (_color) {
					$this.css({color: _color});
				}
				$this.countdown(finalDate, function (event) {
					$this.html(event.strftime(''
						+ (_w === 'no' || _w === 'false' || _w === '0' ? '' : ('<span class="c-countdown__item"' + _bg + '><span class="c-countdown__digits">%-w</span><span class="c-countdown__title">' + ideapark_wp_vars.countdownWeek + '</span></span>'))
						+ (_d === 'no' || _d === 'false' || _d === '0' ? '' : ('<span class="c-countdown__item"' + _bg + '><span class="c-countdown__digits">%-d</span><span class="c-countdown__title">' + ideapark_wp_vars.countdownDay + '</span></span>'))
						+ (_h === 'no' || _h === 'false' || _h === '0' ? '' : ('<span class="c-countdown__item"' + _bg + '><span class="c-countdown__digits">%H</span><span class="c-countdown__title">' + ideapark_wp_vars.countdownHour + '</span></span>'))
						+ (_m === 'no' || _m === 'false' || _m === '0' ? '' : ('<span class="c-countdown__item"' + _bg + '><span class="c-countdown__digits">%M</span><span class="c-countdown__title">' + ideapark_wp_vars.countdownMin + '</span></span>'))
						+ (_s === 'no' || _s === 'false' || _s === '0' ? '' : ('<span class="c-countdown__item"' + _bg + '><span class="c-countdown__digits">%S</span><span class="c-countdown__title">' + ideapark_wp_vars.countdownSec + '</span></span>'))
					));
				});
			}
		});
		
		$(document.body).on('adding_to_cart', function (e, $thisbutton) {
			$thisbutton.ideapark_button('loading', 16);
		}).on('added_to_cart', function (e, fragments, cart_hash, $thisbutton) {
			$thisbutton.ideapark_button('reset');
			if (typeof fragments.ideapark_notice !== 'undefined') {
				ideapark_show_notice(fragments.ideapark_notice);
			}
			if (typeof fragments.ideapark_quantity !== 'undefined') {
				var $form = $thisbutton.closest('form');
				var $product = $thisbutton.closest('.product');
				var is_single = !!$product.find('.js-product-quantity').length;
				var $quantity_wrap = $product.find(is_single ? '.js-product-quantity' : '.js-product-grid-quantity');
				if ($form.length) {
					var $variation_id = $form.find('.variation_id');
					if ($variation_id.length) {
						var current_variation_id = $variation_id.val();
						if (fragments.ideapark_variation_id != current_variation_id) {
							return false;
						}
					}
					$form.on('woocommerce_update_variation_values', ideapark_remove_quantity_updater);
				}
				$product.on('ideapark_remove_quantity_updater', ideapark_remove_quantity_updater);
				$quantity_wrap.html(fragments.ideapark_quantity);
				if (is_single) {
					if (!$quantity_wrap.parent().hasClass('c-product__quantity-wrap--sticky')) {
						$quantity_wrap.find('.c-quantity').addClass('c-quantity--big');
					} else {
						$quantity_wrap.find('.c-quantity').addClass('c-quantity--big c-quantity--sticky');
					}
					$thisbutton.addClass('c-product__add-to-cart--hidden');
				} else {
					$thisbutton.addClass('c-product-grid__add-to-cart--hidden');
				}
			}
		}).on('click', ".js-extra-info", function (e) {
			var $product = $(this).closest('.product');
			$product.find('.js-extra-info-popup').toggleClass('c-product-grid__marker-popup--active');
		}).on('click', ".js-extra-info-close", function (e) {
			var $product = $(this).closest('.product');
			$product.find('.js-extra-info-popup').removeClass('c-product-grid__marker-popup--active');
		}).on('click', ".js-grid-zoom,.js-product-zoom", function (e) {
			e.preventDefault();
			var $button = $(this);
			var $button_loading = $button;
			if ($button.hasClass('js-product-zoom-video')) {
				$button_loading = $button.find('.c-product__video-wrap');
			}
			if ($button.hasClass('js-loading')) {
				return;
			}
			var index = 0;
			if (ideapark_isset($button.data('index'))) {
				$button_loading.ideapark_button('loading', 25);
				index = $button.data('index');
			} else {
				$button_loading.ideapark_button('loading');
			}
			var $product = $button.closest('.product');
			var variation_id = $product.find('.c-product__summary .variation_id').val();
			require([
				'photoswipe/photoswipe.min',
				'photoswipe/photoswipe-ui-default.min',
				'json!' + ideapark_wp_vars.ajaxUrl + '?action=ideapark_product_images&product_id=' + $button.data('product-id') + (!ideapark_empty(variation_id) ? '&variation_id=' + variation_id : '') + '!bust',
				'css!' + ideapark_wp_vars.themeUri + '/assets/css/photoswipe/photoswipe',
				'css!' + ideapark_wp_vars.themeUri + '/assets/css/photoswipe/default-skin/default-skin'
			], function (PhotoSwipe, PhotoSwipeUI_Default, images) {
				$button_loading.ideapark_button('reset');
				if (images.images.length) {
					var options = {
						index              : index ? index : 0,
						showHideOpacity    : true,
						bgOpacity          : 0.7,
						loop               : false,
						closeOnVerticalDrag: false,
						mainClass          : '',
						barsSize           : {top: 0, bottom: 0},
						captionEl          : false,
						fullscreenEl       : false,
						zoomEl             : true,
						shareEl            : false,
						counterEl          : false,
						tapToClose         : true,
						tapToToggleControls: false
					};
					
					var pswpElement = $('.pswp')[0];
					
					ideapark_wpadminbar_resize();
					
					var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, images.images, options);
					gallery.init();
					
					gallery.listen('afterChange', function () {
						if (!ideapark_empty(gallery.currItem.html)) {
							$('.pswp__video-wrap').fitVids();
						}
					});
					
					gallery.listen('close', function () {
						$('.pswp__video-wrap').html('');
					});
					
					$('.pswp__video-wrap').fitVids();
				}
			});
		}).on('click', ".js-add-to-cart-variation", function (e) {
			e.preventDefault();
			var $thisbutton = $(this);
			if (!$thisbutton.is('.disabled')) {
				var $form = $thisbutton.closest('.js-variations-form,.variations_form');
				$thisbutton.data('product_id', $form.find('.variation_id').val());
				$thisbutton.data('quantity', $form.find('.qty').val());
				$thisbutton.data('product_sku', null);
				$thisbutton.removeClass('added');
				$thisbutton.addClass('loading');
				
				var data = {};
				$.each($thisbutton.data(), function (key, value) {
					data[key] = value;
				});
				$(document.body).trigger('adding_to_cart', [$thisbutton, data]);
				$.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data, function (response) {
					if (!response) {
						return;
					}
					if (response.error && response.product_url) {
						window.location = response.product_url;
						return;
					}
					if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
						window.location = wc_add_to_cart_params.cart_url;
						return;
					}
					$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
				});
			}
		}).on('click', ".js-quantity-minus", function (e) {
			e.preventDefault();
			var $input = $(this).parent().find('input[type=number]');
			var quantity = $input.val().trim();
			var min = $input.attr('min');
			quantity--;
			if (quantity < (min !== '' ? min : 1)) {
				quantity = (min !== '' ? min : 1);
			}
			$input.val(quantity);
			$input.trigger('change');
			
		}).on('click', ".js-quantity-plus", function (e) {
			e.preventDefault();
			var $input = $(this).parent().find('input[type=number]');
			var quantity = $input.val().trim();
			var max = $input.attr('max');
			quantity++;
			if ((max !== '') && (quantity > max)) {
				quantity = max;
			}
			if (quantity > 0) {
				$input.val(quantity);
				$input.trigger('change');
			}
		}).on('click', ".js-ajax-search-all", function (e) {
			$('.js-search-form').submit();
		}).on('change keydown', ".js-quantity-value", function (e) {
			e.preventDefault();
			$ideapark_quantity_input = $(this);
			ideapark_update_quantity();
		}).on('click', '.js-notice-close', function (e) {
			e.preventDefault();
			var $notice = $(this).closest('.woocommerce-notice');
			$notice.animate({
				opacity: 0,
			}, 500, function () {
				$notice.remove();
			});
		}).on('click', '.js-cart-coupon', function (e) {
			e.preventDefault();
			var $coupon = $(".c-cart__coupon-from-wrap");
			$coupon.toggleClass('c-cart__coupon-from-wrap--opened');
			$('.c-cart__select-svg').toggleClass('c-cart__select-svg--opened');
			if ($coupon.hasClass('c-cart__coupon-from-wrap--opened')) {
				setTimeout(function () {
					$coupon.find('input[type=text]').first().focus();
				}, 500);
			}
			return false;
		}).on('checkout_error updated_checkout applied_coupon removed_coupon', function () {
			var $notices = $('div.woocommerce-notice:not(.shown), div.woocommerce-error:not(.shown), div.woocommerce-message:not(.shown)');
			if ($notices.length) {
				$notices.detach();
				ideapark_show_notice($notices);
			}
		}).on('change', '#ship-to-different-address input', function () {
			if (ideapark_wp_vars.stickySidebar && $ideapark_sticky_sidebar_nearby.length && $ideapark_sticky_sidebar_nearby.length) {
				setTimeout(function () {
					delete root.ideapark_scroll_offset_last;
					if (ideapark_sticky_sidebar_old_style !== null) {
						$ideapark_sticky_sidebar.attr('style', ideapark_sticky_sidebar_old_style);
						ideapark_sticky_sidebar_old_style = null;
					}
					ideapark_sticky_sidebar();
				}, 1000);
			}
		}).on('click', "#ip-checkout-apply-coupon", function () {
			
			var params = null;
			var is_cart = false;
			
			if (typeof wc_checkout_params != 'undefined') {
				params = wc_checkout_params;
				is_cart = false;
			}
			
			if (typeof wc_cart_params != 'undefined') {
				params = wc_cart_params;
				is_cart = true;
			}
			
			if (!params) {
				return false;
			}
			
			var $collaterals = $(this).closest('.c-cart__collaterals');
			
			if ($collaterals.is('.processing')) {
				return false;
			}
			
			$collaterals.addClass('processing').block({
				message   : null,
				overlayCSS: {
					background: '#fff',
					opacity   : 0.6
				}
			});
			
			var data = {
				security   : params.apply_coupon_nonce,
				coupon_code: $collaterals.find('input[name="coupon_code"]').val()
			};
			
			$.ajax({
				type    : 'POST',
				url     : params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
				data    : data,
				success : function (code) {
					if (code) {
						ideapark_show_notice(code);
						if (is_cart) {
							$.ajax({
								url     : params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_cart_totals'),
								dataType: 'html',
								success : function (response) {
									$collaterals.html(response);
								},
								complete: function () {
									$collaterals.removeClass('processing').unblock();
								}
							});
						} else {
							$collaterals.removeClass('processing').unblock();
							$(document.body).trigger('update_checkout', {update_shipping_method: false});
						}
					}
				},
				dataType: 'html'
			});
			
			return false;
		});
		
		$('.c-product__add-to-cart-wrap .button').each(function () {
			var $this = $(this);
			var $product = $this.closest('.c-product');
			var is_grouped = $product.hasClass('product-type-grouped');
			var $wrap = $this.closest('.c-product__add-to-cart-wrap');
			
			$this.addClass('c-add-to-cart c-add-to-cart--big').prepend('<svg class="c-add-to-cart__svg c-add-to-cart__svg--big"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-cart" /></svg>');
			if ($this.closest('.js-variations-form,.variations_form').length) {
				$this.addClass('js-single-product-add-to-cart-variation');
			} else {
				$this.addClass('js-single-product-add-to-cart-simple' + (is_grouped ? ' is-grouped' : ''));
			}
			ideapark_single_product_add_to_cart_ajax_switch();
			$this.after('<span class="added_to_cart h-hidden"></span>');
			$this.attr('data-product_id', $product.attr('data-product_id'));
			$this.attr('data-product_sku', $product.find('.sku').attr('data-o_content'));
			$this.attr('data-quantity', $product.find('.qty').val());
			if (!is_grouped && ideapark_wp_vars.productMobileAjaxATC) {
				$product.find('.c-product__quantity').addClass('c-product__quantity--hidden');
			}
			
			$wrap.removeClass('h-invisible');
		});
		
		$('.js-extra-info-toggle').click(function () {
			var $info = $(this).closest('.c-product__info');
			$info.find('.c-product__info-text').toggleClass('c-product__info-text--expand');
			$info.find('.c-product__info-toggle-svg').toggleClass('c-product__info-toggle-svg--open');
		});
		
		$(document).on('lazybeforeunveil', function (e) {
			if (ideapark_masonry_grid_on && e.target.className.indexOf('c-post-list__img') > -1) {
				ideapark_init_masonry(true);
			}
			if (ideapark_parallax_on && e.target.className.indexOf('c-home-banners__parallax-img') > -1) {
				setTimeout(function () {
					ideapark_simple_parallax_instances.push(new simpleParallax(e.target, {
						scale   : 1.5,
						overflow: true
					}));
				}, 100);
			}
		});
		
		ideapark_init_custom_select();
		ideapark_set_spacer_width();
		ideapark_set_header_bg_height();
		
		ideapark_wpadminbar_resize();
		ideapark_init_notice();
		ideapark_search_init();
		ideapark_scroll_actions();
		ideapark_resize_actions();
		ideapark_top_menu_init();
		ideapark_mega_menu_init();
		ideapark_mobile_menu_init();
		ideapark_shop_sidebar_init();
		ideapark_init_home_promo();
		ideapark_init_home_tabs();
		ideapark_init_home_brands_carousel();
		ideapark_init_home_testimonials_carousel();
		ideapark_init_product_carousel();
		ideapark_init_masonry();
		ideapark_parallax_init();
		
		
		$(document).on('ideapark.wpadminbar.scroll', ideapark_set_notice_offset);
		
		$(document).trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
		
		$('.lazyloaded[data-bg]').each(function () {
			var $this = $(this);
			if ($this.css('background-image') == 'none') {
				$this.css({'background-image': 'url(' + $this.data('bg') + ')'});
			}
		});
		
		$('.entry-content').fitVids();
	});
	
	$(document.body).on('click', '.wc-tabs li a', function (e) {
		e.preventDefault();
		var $tab = $(this);
		var $tabs_wrapper = $tab.closest('.wc-tabs-wrapper, .woocommerce-tabs');
		$tabs_wrapper.find('.wc-tab.visible').removeClass('visible');
		$tabs_wrapper.find('.wc-tab.current').removeClass('current');
		$tabs_wrapper.find($tab.attr('href')).addClass('current');
		
		setTimeout(function () {
			$tabs_wrapper.find($tab.attr('href')).addClass('visible');
		}, 100);
		
	});
	
	root.ideapark_scroll_actions = function () {
		
		ideapark_wpadminbar_scroll();
		ideapark_mega_menu_sticky();
		ideapark_to_top_button();
		ideapark_sticky_sidebar();
		ideapark_init_masonry();
		
		ideapark_scroll_busy = false;
	};
	
	root.ideapark_resize_actions = function () {
		
		var ideapark_is_mobile_layout_new = (window.innerWidth < 1170);
		var is_layout_changed = (ideapark_is_mobile_layout !== ideapark_is_mobile_layout_new);
		ideapark_is_mobile_layout = ideapark_is_mobile_layout_new;
		
		var ideapark_layout_2_new = (window.innerWidth <= 495);
		var is_layout_changed_2 = (ideapark_layout_2 !== ideapark_layout_2_new);
		ideapark_layout_2 = ideapark_layout_2_new;
		
		var ideapark_layout_3_new = (window.innerWidth < 768);
		var is_layout_changed_3 = (ideapark_layout_3 !== ideapark_layout_3_new);
		ideapark_layout_3 = ideapark_layout_3_new;
		
		var ideapark_layout_4_new = (window.innerWidth < 600);
		var is_layout_changed_4 = (ideapark_layout_4 !== ideapark_layout_4_new);
		ideapark_layout_4 = ideapark_layout_4_new;
		
		
		ideapark_wpadminbar_resize();
		
		if (is_layout_changed) { // switch between mobile and desktop layouts
			
			$(document.body).addClass('block-transition');
			setTimeout(function () {
				$(document.body).removeClass('block-transition');
			}, 500);
			
			ideapark_set_header_bg_height();
			ideapark_search_popup(false);
			ideapark_mega_menu_break();
			ideapark_mega_menu_sticky_init();
			ideapark_mobile_menu_popup(false);
			ideapark_sidebar_popup(false);
			ideapark_set_spacer_width();
			ideapark_mobile_menu_init();
			ideapark_shop_sidebar_init();
			ideapark_set_notice_offset();
			ideapark_sticky_sidebar();
			
			ideapark_init_view_more_item($('.js-view-more-tab'));
			
			$('.hasCustomSelect').trigger('render');
			
			setTimeout(function () {
				ideapark_wpadminbar_resize();
				$(document).trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
			}, 100);
		}
		
		if (is_layout_changed_2) {
			ideapark_init_product_thumbs_carousel();
		}
		
		if (is_layout_changed_3) {
			ideapark_single_product_add_to_cart_ajax_switch();
		}
		
		if (is_layout_changed_4) {
			ideapark_init_masonry();
		}
		
		ideapark_mega_menu_sticky();
		
		ideapark_resize_busy = false;
	};
	
	
	$window.scroll(
		function () {
			if (window.requestAnimationFrame) {
				if (!ideapark_scroll_busy) {
					ideapark_scroll_busy = true;
					window.requestAnimationFrame(ideapark_scroll_actions);
				}
			} else {
				ideapark_scroll_actions();
			}
		}
	);
	
	$window.resize(
		function () {
			if (window.requestAnimationFrame) {
				if (!ideapark_resize_busy) {
					ideapark_resize_busy = true;
					window.requestAnimationFrame(ideapark_resize_actions);
				}
			} else {
				ideapark_resize_actions();
			}
		}
	);
	
	root.ideapark_search_popup = function (show) {
		if (show && !ideapark_search_popup_active) {
			ideapark_search_popup_active = true;
			$ideapark_search.addClass('c-header-search--active');
			$ideapark_search.find('.c-header-search__wrap').addClass('c-header-search__wrap--active');
			bodyScrollLock.disableBodyScroll(ideapark_is_mobile_layout ? $ideapark_search_result[0] : $ideapark_search[0]);
		} else if (ideapark_search_popup_active) {
			ideapark_search_popup_active = false;
			$ideapark_search.removeClass('c-header-search--active');
			$ideapark_search.find('.c-header-search__wrap').removeClass('c-header-search__wrap--active');
			bodyScrollLock.clearAllBodyScrollLocks();
		}
	};
	
	root.ideapark_search_clear = function () {
		$ideapark_search_input.val('').trigger('input').focus();
		$ideapark_search.off(ideapark_on_transition_end, ideapark_search_clear);
	};
	
	root.ideapark_wpadminbar_resize = function () {
		$ideapark_admin_bar = $('#wpadminbar');
		if ($ideapark_admin_bar.length) {
			var window_width = $window.width();
			if (window_width > 782 && $ideapark_admin_bar.hasClass('mobile')) {
				$ideapark_admin_bar.removeClass('mobile');
			} else if (window_width <= 782 && !$ideapark_admin_bar.hasClass('mobile')) {
				$ideapark_admin_bar.addClass('mobile');
			}
			ideapark_adminbar_height = $ideapark_admin_bar.outerHeight();
			ideapark_adminbar_position = $ideapark_admin_bar.css('position');
			
			if (ideapark_adminbar_position === 'fixed' || ideapark_adminbar_position === 'absolute') {
				$(".js-fixed").css({
					top         : ideapark_adminbar_visible_height,
					'max-height': 'calc(100% - ' + ideapark_adminbar_visible_height + 'px)'
				});
			} else {
				$(".js-fixed").css({
					top         : 0,
					'max-height': '100%'
				});
			}
			
			ideapark_wpadminbar_scroll();
		}
	};
	
	root.ideapark_wpadminbar_scroll = function () {
		if ($ideapark_admin_bar === null) {
			$ideapark_admin_bar = $('#wpadminbar');
		}
		if ($ideapark_admin_bar.length) {
			var scroll_top_mobile = window.scrollY;
			var top_new = 0;
			
			if (ideapark_adminbar_position === 'fixed') {
				top_new = ideapark_adminbar_height;
			} else {
				top_new = ideapark_adminbar_height - scroll_top_mobile;
				if (top_new < 0) {
					top_new = 0;
				}
			}
			
			if (ideapark_adminbar_visible_height != top_new) {
				ideapark_adminbar_visible_height = top_new;
				$(document).trigger('ideapark.wpadminbar.scroll', ideapark_adminbar_visible_height);
			}
		}
	};
	
	root.ideapark_open_photo_swipe = function (imageWrap, index) {
		var $this, $a, $img, items = [], size, item;
		$slick_product_single_slides.each(function () {
			$this = $(this);
			$a = $this.children('a');
			$img = $a.children('img');
			size = $a.data('size').split('x');
			
			item = {
				src : $a.attr('href'),
				w   : parseInt(size[0], 10),
				h   : parseInt(size[1], 10),
				msrc: $img.attr('src'),
				el  : $a[0]
			};
			
			items.push(item);
		});
		
		var options = {
			index              : index,
			showHideOpacity    : true,
			bgOpacity          : 1,
			loop               : false,
			closeOnVerticalDrag: false,
			mainClass          : ($slick_product_single_slides.length > 1) ? 'pswp--minimal--dark' : 'pswp--minimal--dark pswp--single--image',
			barsSize           : {top: 0, bottom: 0},
			captionEl          : false,
			fullscreenEl       : false,
			zoomEl             : false,
			shareEl            : false,
			counterEl          : false,
			tapToClose         : true,
			tapToToggleControls: false
		};
		
		var pswpElement = $('.pswp')[0];
		
		var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
		gallery.init();
		
		gallery.listen('initialZoomIn', function () {
			$(this).product_thumbnails_speed = $slick_product_thumbnails.slick('slickGetOption', 'speed');
			$slick_product_thumbnails.slick('slickSetOption', 'speed', 0);
		});
		
		var slide = index;
		gallery.listen('beforeChange', function (dirVal) {
			slide = slide + dirVal;
			$slick_product_single.slick('slickGoTo', slide, true);
		});
		gallery.listen('close', function () {
			$slick_product_thumbnails.slick('slickSetOption', 'speed', $(this).product_thumbnails_speed);
		});
	};
	
	root.ideapark_refresh_parallax = ideapark_debounce(function () {
	}, 500);
	
	root.ajaxSearchFunction = ideapark_debounce(function () {
		var search = $ideapark_search_input.val().trim();
		var $search_form = $ideapark_search_input.closest('form');
		if (ideapark_empty(search)) {
			$ideapark_search_result.html('');
		} else {
			$ideapark_search_loader.insertBefore($ideapark_search_input);
			$.ajax({
				url    : ideapark_wp_vars.ajaxUrl,
				type   : 'POST',
				data   : {
					action: 'ideapark_ajax_search',
					s     : search,
					lang  : $('input[name="lang"]', $search_form).val()
				},
				success: function (results) {
					$ideapark_search_loader.remove();
					$ideapark_search_result.html((ideapark_empty($ideapark_search_input.val().trim())) ? '' : results);
					
					ideapark_init_custom_select();
					if (typeof IP_Wishlist != "undefined") {
						IP_Wishlist.init_product_button();
					}
					$ideapark_search_result.find('.js-variations-form,.variations_form').each(function () {
						$(this).wc_variation_form();
					});
					$ideapark_search_result.find('.c-add-to-cart.disabled:not(.js-add-to-cart-variation)').removeClass('disabled');
				}
			});
		}
	}, 500);
	
	root.ideapark_top_menu_init = function () {
		var $ideapark_top_menu = $('.js-top-menu');
		
		if ($ideapark_top_menu.length) {
			$ideapark_top_menu.find('.c-top-menu__subitem--has-children').each(function () {
				var $li = $(this);
				if ($li.find('ul').length) {
					$li.append('<svg class="c-top-menu__more-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>');
				} else {
					$li.removeClass('c-top-menu__subitem--has-children');
				}
			});
		}
	};
	
	root.ideapark_mega_menu_init = function () {
		
		$ideapark_desktop_mega_menu = $('.js-header-desktop .js-mega-menu');
		
		ideapark_mega_menu_break(true);
		ideapark_mega_menu_sticky_init(true);
	};
	
	root.ideapark_mega_menu_break = function (force) {
		
		if (force) {
			ideapark_mega_menu_break_init = false;
			ideapark_mega_menu_to_left = false;
		}
		
		if (!ideapark_is_mobile_layout && !ideapark_mega_menu_break_init && $ideapark_desktop_mega_menu.length) {
			var main_items = $ideapark_desktop_mega_menu.find('[class*="c-mega-menu__submenu--col-"]');
			if (main_items.length) {
				
				main_items.each(function () {
					var $ul = $(this);
					var cols = 0;
					if ($ul.hasClass('c-mega-menu__submenu--col-2')) {
						cols = 2;
					} else if ($ul.hasClass('c-mega-menu__submenu--col-3')) {
						cols = 3;
					} else if ($ul.hasClass('c-mega-menu__submenu--col-4')) {
						cols = 4;
					}
					
					var padding_top = $ul.css('padding-top') ? parseInt($ul.css('padding-top').replace('px', '')) : 0;
					var padding_bottom = $ul.css('padding-bottom') ? parseInt($ul.css('padding-bottom').replace('px', '')) : 0;
					var heights = [];
					var max_height = 0;
					var all_sum_height = 0;
					$ul.children('li').each(function () {
						var $li = $(this);
						var height = $li.outerHeight();
						if (height > max_height) {
							max_height = height;
						}
						all_sum_height += height;
						heights.push(height);
					});
					var test_cols = 0;
					var cnt = 0;
					var test_height = max_height - 1;
					do {
						test_height++;
						cnt++;
						test_cols = 1;
						var sum_height = 0;
						for (var i = 0; i < heights.length; i++) {
							sum_height += heights[i];
							if (sum_height > test_height) {
								sum_height = 0;
								i--;
								test_cols++;
							}
						}
					} while (test_cols > cols && cnt < 1000);
					
					if (test_cols <= cols && test_height > 0) {
						$ul.css({height: (test_height + padding_top + padding_bottom) + 'px'}).addClass('js-mega-menu-break');
					}
				});
				ideapark_mega_menu_break_init = true;
			}
			
			// Left menu direction
			if (!ideapark_mega_menu_to_left) {
				var $nav = $ideapark_desktop_mega_menu;
				var nav_center = Math.round($nav.offset().left + $nav.width() / 2 - 40);
				$nav.find('.c-mega-menu__item > .c-mega-menu__submenu').each(function () {
					var _ = $(this);
					var is_rtl = false;
					if (_.offset().left > nav_center) {
						_.addClass('c-mega-menu__submenu--rtl');
						_.find('.c-mega-menu__submenu--popup').each(function () {
							$(this).addClass('c-mega-menu__submenu--popup-rtl')
						});
						is_rtl = true;
					}
					
					_.find('.c-mega-menu__subitem--has-children').each(function () {
						var $li = $(this);
						if ($li.find('.c-mega-menu__submenu--popup').length) {
							$li.append('<svg class="c-mega-menu__more-svg' + (is_rtl ? '-rtl' : '') + '"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>');
						}
					});
					
				});
				
				ideapark_mega_menu_to_left = true;
			}
			
			$('.c-mega-menu--preload').removeClass('c-mega-menu--preload');
		}
		
	};
	
	root.ideapark_mega_menu_sticky_wpadmin = function (event, wpadminbar_height) {
		if (ideapark_sticky_mobile_init && ideapark_is_mobile_layout && $ideapark_mobile_sticky_row.length) {
			$ideapark_mobile_sticky_row.css({
				transform: 'translateY(' + wpadminbar_height + 'px)'
			});
		}
	};
	
	root.ideapark_mega_menu_sticky_init = function (force) {
		if ((!ideapark_sticky_desktop_init || force) && !ideapark_is_mobile_layout && ideapark_wp_vars.stickyMenuDesktop && $ideapark_desktop_sticky_row.length && $ideapark_desktop_mega_menu.length) {
			
			ideapark_desktop_mega_menu_top = $ideapark_desktop_mega_menu.length ? $ideapark_desktop_mega_menu.offset().top : 0;
			ideapark_desktop_mega_menu_height = $ideapark_desktop_mega_menu.length ? $ideapark_desktop_mega_menu.outerHeight() : 0;
			
			var $mega_menu = $ideapark_desktop_mega_menu.clone();
			var $container = $ideapark_desktop_sticky_row.find('.c-header__container');
			$container.html('');
			$mega_menu.find('.c-mega-menu__label-wrap').remove();
			$mega_menu.find('.c-mega-menu__icon--empty').remove();
			$mega_menu.find('.c-mega-menu__title-wrap--vert').removeClass('c-mega-menu__title-wrap--vert');
			$mega_menu.find('.c-mega-menu__title--vert').removeClass('c-mega-menu__title--vert');
			$mega_menu.removeClass('js-mega-menu');
			
			$ideapark_desktop_sticky_spacer = $("<div class='js-cart-spacer'></div>");
			$ideapark_desktop_sticky_spacer.appendTo($container);
			
			$mega_menu.appendTo($container);
			
			$ideapark_desktop_sticky_cart = $('.js-header-desktop .js-cart').clone();
			$ideapark_desktop_sticky_cart.appendTo($container);
			
			$ideapark_desktop_sticky_row.removeClass('c-header__row-sticky--active c-header__row-sticky--disabled');
			ideapark_sticky_active = false;
			ideapark_sticky_desktop_init = true;
			
			$('.js-desktop-sticky-menu a[href^="#"]').click(ideapark_hash_menu_animate);
			ideapark_mega_menu_sticky();
		}
		
		if ((!ideapark_sticky_mobile_init || force) && ideapark_is_mobile_layout && ideapark_wp_vars.stickyMenuMobile) {
			if (ideapark_sticky_mobile_init) {
				$(document).off('ideapark.wpadminbar.scroll', ideapark_mega_menu_sticky_wpadmin);
			}
			$(document).on('ideapark.wpadminbar.scroll', ideapark_mega_menu_sticky_wpadmin);
			ideapark_sticky_mobile_init = true;
			
			ideapark_mega_menu_sticky();
		}
	};
	
	root.ideapark_mega_menu_sticky = function () {
		if (ideapark_wp_vars.stickyMenuDesktop && ideapark_sticky_desktop_init && !ideapark_is_mobile_layout) {
			if ($ideapark_desktop_sticky_row.length) {
				var scroll_top = window.scrollY;
				var is_sticky_area = scroll_top + ideapark_adminbar_height >= ideapark_desktop_mega_menu_top + ideapark_desktop_mega_menu_height;
				
				if (ideapark_sticky_active) {
					if (!is_sticky_area) {
						$ideapark_desktop_sticky_row.removeClass('c-header__row-sticky--active');
						ideapark_sticky_active = false
					}
				} else {
					if (is_sticky_area) {
						$ideapark_desktop_sticky_row.addClass('c-header__row-sticky--active');
						$ideapark_desktop_sticky_spacer.css({width: $ideapark_desktop_sticky_cart.outerWidth() + 'px'});
						ideapark_sticky_active = true;
						
						/*if (ideapark_wp_vars.headerType !== 'header-type-3') {
							$cart = $header.find('.c-header__cart');
							if ($cart.length === 1) {
								var container = $header.find('.c-header__container');
								$cart.assClass('c-header__cart--sticky');

							}
						}*/
					}
				}
				ideapark_set_notice_offset();
			}
		}
	};
	
	root.ideapark_search_init = function () {
		$ideapark_search.removeClass('c-header-search--disabled');
		
		$('.js-search-button').click(function () {
			ideapark_search_popup(true);
			setTimeout(function () {
				$ideapark_search_input.focus();
			}, 500);
		});
		
		$('#ideapark-ajax-search-close').click(function () {
			$ideapark_search.on(ideapark_on_transition_end, ideapark_search_clear);
			ideapark_search_popup(false);
		});
		
		$ideapark_search_input.on('keydown', function (e) {
			var $this = $(this);
			var is_not_empty = !ideapark_empty($this.val().trim());
			
			if (e.keyCode == 13) {
				e.preventDefault();
				if ($this.hasClass('c-header-search__input--no-ajax') && is_not_empty) {
					$this.closest('form').submit();
				}
			} else if (e.keyCode == 27) {
				ideapark_search_popup(false);
			}
		}).on('input', function () {
			var $this = $(this);
			var is_not_empty = !ideapark_empty($this.val().trim());
			
			if (is_not_empty && !ideapark_search_input_filled) {
				ideapark_search_input_filled = true;
				$('#ideapark-ajax-search-clear').addClass('c-header-search__clear--active');
				
			} else if (!is_not_empty && ideapark_search_input_filled) {
				ideapark_search_input_filled = false;
				$('#ideapark-ajax-search-clear').removeClass('c-header-search__clear--active');
			}
			ajaxSearchFunction();
		});
		
		$('#ideapark-ajax-search-clear').click(function () {
			ideapark_search_clear();
		});
		
		$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
			$ideapark_search.css({
				transform   : 'translateY(' + wpadminbar_height + 'px)',
				'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
			});
		});
	};
	
	root.ideapark_sidebar_popup = function (show) {
		if (ideapark_shop_sidebar_initialized) {
			if (show && !ideapark_shop_sidebar_active) {
				ideapark_shop_sidebar_active = true;
				$ideapark_shop_sidebar.addClass('c-shop-sidebar--active');
				$ideapark_shop_sidebar_wrap.addClass('c-shop-sidebar__wrap--active');
				bodyScrollLock.disableBodyScroll($ideapark_shop_sidebar_content[0]);
			} else if (ideapark_shop_sidebar_active) {
				ideapark_shop_sidebar_active = false;
				$ideapark_shop_sidebar.removeClass('c-shop-sidebar--active');
				$ideapark_shop_sidebar_wrap.removeClass('c-shop-sidebar__wrap--active');
				bodyScrollLock.clearAllBodyScrollLocks();
			}
		}
	};
	
	root.ideapark_shop_sidebar_init = function () {
		if (ideapark_is_mobile_layout && !ideapark_shop_sidebar_initialized && $ideapark_shop_sidebar.length) {
			$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
				if (ideapark_is_mobile_layout) {
					$ideapark_shop_sidebar.css({
						transform   : 'translateY(' + wpadminbar_height + 'px)',
						'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
					});
				} else {
					$ideapark_shop_sidebar.css({
						transform   : '',
						'max-height': ''
					});
				}
			});
			$ideapark_shop_sidebar_open.click(function () {
				ideapark_sidebar_popup(true);
			});
			
			$ideapark_shop_sidebar_close.click(function () {
				ideapark_sidebar_popup(false);
			});
			ideapark_shop_sidebar_initialized = true;
		}
	};
	
	root.ideapark_mobile_menu_popup = function (show) {
		if (ideapark_mobile_menu_initialized) {
			if (show && !ideapark_mobile_menu_active) {
				ideapark_mobile_menu_active = true;
				$ideapark_mobile_menu.addClass('c-header__menu--active');
				$ideapark_mobile_menu_wrap.addClass('c-header__menu-wrap--active');
				bodyScrollLock.disableBodyScroll($ideapark_mobile_menu_content[0]);
			} else if (ideapark_mobile_menu_active) {
				ideapark_mobile_menu_active = false;
				$ideapark_mobile_menu.removeClass('c-header__menu--active');
				$ideapark_mobile_menu_wrap.removeClass('c-header__menu-wrap--active');
				bodyScrollLock.clearAllBodyScrollLocks();
			}
		}
	};
	
	root.ideapark_mobile_menu_init = function () {
		if (ideapark_is_mobile_layout && !ideapark_mobile_menu_initialized && $ideapark_mobile_menu.length) {
			$(document).on('ideapark.wpadminbar.scroll', function (event, wpadminbar_height) {
				$ideapark_mobile_menu.css({
					transform   : 'translateY(' + wpadminbar_height + 'px)',
					'max-height': 'calc(100% - ' + wpadminbar_height + 'px)'
				});
			});
			
			$ideapark_mobile_menu.find('.c-mega-menu__item--has-children, .c-mega-menu__subitem--has-children').each(function () {
				var $li = $(this);
				if ($li.find('ul').length) {
					var $button = $('<button type="button" class="h-cb c-mega-menu__more js-menu-more"><svg class="c-mega-menu__more-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg></button>');
					$li.append($button);
					$li.children('a').each(function () {
						if (ideapark_empty($(this).attr('href'))) {
							$(this).click(function () {
								$button.trigger('click')
							});
						}
					});
				}
			});
			
			$('.js-menu-more' + (ideapark_wp_vars.titleClickExpand ? ',.c-mega-menu__item--has-children > a,.c-mega-menu__subitem--has-children > a' : '')).click(function (e) {
				e.preventDefault();
				var $submenu = $(this).closest('li').children('ul');
				var $current_submenu = $(this).closest('.c-mega-menu__submenu');
				$ideapark_mobile_submenu.push($submenu);
				var top = $('.c-header__menu-buttons').outerHeight();
				if ($ideapark_mobile_submenu.length == 1) {
					$submenu.css({
						top         : top + 'px',
						'max-height': 'calc(100% - ' + top + 'px)'
					});
				}
				if ($current_submenu.length) {
					$current_submenu.scrollTop(0);
					$current_submenu.addClass('c-mega-menu__submenu--parent');
				}
				$submenu.addClass('c-mega-menu__submenu--active');
				$ideapark_mobile_menu_back.addClass('c-header__menu-back--active');
				bodyScrollLock.clearAllBodyScrollLocks();
				bodyScrollLock.disableBodyScroll($submenu[0]);
			});
			
			$ideapark_mobile_menu_back.click(function () {
				if ($ideapark_mobile_submenu.length) {
					var $submenu = $ideapark_mobile_submenu.pop();
					$submenu.removeClass('c-mega-menu__submenu--active');
					bodyScrollLock.clearAllBodyScrollLocks();
				}
				if (!$ideapark_mobile_submenu.length) {
					bodyScrollLock.disableBodyScroll($ideapark_mobile_menu_content[0]);
					$ideapark_mobile_menu_back.removeClass('c-header__menu-back--active');
				} else {
					var $prev = $ideapark_mobile_submenu[$ideapark_mobile_submenu.length - 1];
					$prev.removeClass('c-mega-menu__submenu--parent');
					bodyScrollLock.disableBodyScroll($prev[0]);
				}
			});
			
			$ideapark_mobile_menu_open.click(function () {
				ideapark_mobile_menu_popup(true);
			});
			
			$ideapark_mobile_menu_close.click(function () {
				ideapark_mobile_menu_popup(false);
			});
			
			ideapark_mobile_menu_initialized = true;
		}
	};
	
	root.ideapark_set_spacer_width = function () {
		$('.c-ordering__spacer').css({width: ideapark_is_mobile_layout ? '' : $('.c-ordering__select').outerWidth() + 'px'});
		$('.c-ordering--preload').removeClass('c-ordering--preload');
	};
	
	root.ideapark_set_header_bg_height = function (force) {
		if ((!ideapark_header_bg_height_init || force) && !ideapark_is_mobile_layout && $ideapark_header_bg.length) {
			var height_type = $ideapark_header_bg.data('height');
			var height = 0;
			if (height_type) {
				switch (height_type + '') {
					case '1':
						height = $('.c-header__row-1 .l-section__container').outerHeight();
						break;
					case '2':
						height = $('.c-header__row-1 .l-section__container').outerHeight() + $('.c-header__row-2 .l-section__container').outerHeight();
						break;
					case '3':
						height = $('.c-header__row-1 .l-section__container').outerHeight() + $('.c-header__row-2 .l-section__container').outerHeight() + $('.c-header__row-3 .l-section__container').outerHeight();
						break;
					case '3+':
						height = $('.c-header__row-1 .l-section__container').outerHeight() + $('.c-header__row-2 .l-section__container').outerHeight() + $('.c-header__row-3 .l-section__container').outerHeight() + $('.c-page-header--category').outerHeight();
						break;
				}
				if (height > 0) {
					$ideapark_header_bg.css({'height': height + 'px'});
				}
				
			}
			ideapark_header_bg_height_init = true;
		}
	};
	
	root.ideapark_set_header_bg_height_force = function () {
		root.ideapark_set_header_bg_height(true);
	};
	
	root.ideapark_wc_variations_image_update = function () {
		if (typeof $.fn.wc_variations_image_update === 'function') {
			$.fn.wc_variations_image_update = function (variation) {
				var $form = this,
					$product = $form.closest('.product'),
					is_single = $product.hasClass('c-product'),
					$product_img = $product.find(is_single ? '.c-product__gallery-img:first' : '.c-product-grid__thumb');
				
				if (variation && variation.image) {
					
					$form.wc_variations_image_reset();
					
					if (is_single) {
						var $product_thumb = $product.find('.c-product__thumbs-img').first();
						if ($product_thumb.length) {
							$product_thumb.wc_set_variation_attr('src', variation.image.gallery_thumbnail_src);
						}
						$product_img.wc_set_variation_attr('src', variation.image.src);
						$product_img.wc_set_variation_attr('height', variation.image.src_h);
						$product_img.wc_set_variation_attr('width', variation.image.src_w);
						$product_img.wc_set_variation_attr('srcset', variation.image.srcset);
						$product_img.wc_set_variation_attr('sizes', variation.image.sizes);
						$product_img.wc_set_variation_attr('title', variation.image.title);
						$product_img.wc_set_variation_attr('data-caption', variation.image.caption);
						$product_img.wc_set_variation_attr('alt', variation.image.alt);
						$product_img.wc_set_variation_attr('data-src', variation.image.full_src);
						$product_img.wc_set_variation_attr('data-large_image', variation.image.full_src);
						$product_img.wc_set_variation_attr('data-large_image_width', variation.image.full_src_w);
						$product_img.wc_set_variation_attr('data-large_image_height', variation.image.full_src_h);
					} else if (variation.image.thumb_src && variation.image.thumb_src.length > 1) {
						$product_img.wc_set_variation_attr('src', variation.image.thumb_src);
						$product_img.wc_set_variation_attr('srcset', variation.image.thumb_srcset);
						$product_img.wc_set_variation_attr('title', variation.image.title);
						$product_img.wc_set_variation_attr('alt', variation.image.alt);
					}
					
				} else {
					$form.wc_variations_image_reset();
				}
			};
			
			$.fn.wc_variations_image_reset = function () {
				var $form = this,
					$product = $form.closest('.product'),
					is_single = $product.hasClass('c-product'),
					$product_img = $product.find(is_single ? '.c-product__gallery-img:first' : '.c-product-grid__thumb');
				
				if (is_single) {
					var $product_thumb = $product.find('.c-product__thumbs-img').first();
					if ($product_thumb.length) {
						$product_thumb.wc_reset_variation_attr('src');
					}
					$product_img.wc_reset_variation_attr('src');
					$product_img.wc_reset_variation_attr('width');
					$product_img.wc_reset_variation_attr('height');
					$product_img.wc_reset_variation_attr('srcset');
					$product_img.wc_reset_variation_attr('sizes');
					$product_img.wc_reset_variation_attr('title');
					$product_img.wc_reset_variation_attr('data-caption');
					$product_img.wc_reset_variation_attr('alt');
					$product_img.wc_reset_variation_attr('data-src');
					$product_img.wc_reset_variation_attr('data-large_image');
					$product_img.wc_reset_variation_attr('data-large_image_width');
					$product_img.wc_reset_variation_attr('data-large_image_height');
				} else {
					$product_img.wc_reset_variation_attr('src');
					$product_img.wc_reset_variation_attr('srcset');
					$product_img.wc_reset_variation_attr('title');
					$product_img.wc_reset_variation_attr('alt');
				}
			};
			
			$.fn.wc_set_variation_attr = function (attr, value) {
				var is_lazy = false;
				if (undefined === this.attr('data-o_' + attr)) {
					if (!this.attr(attr) && !!this.attr('data-' + attr)) {
						this.attr('data-o_' + attr, this.attr('data-' + attr));
						is_lazy = true;
					} else {
						this.attr('data-o_' + attr, (!this.attr(attr)) ? '' : this.attr(attr));
					}
				}
				if (false === value) {
					this.removeAttr(attr);
				} else {
					if (is_lazy) {
						this.attr('data-' + attr, value);
					} else {
						this.attr(attr, value);
					}
				}
			};
		}
	};
	
	root.ideapark_remove_quantity_updater = function () {
		var $this = $(this), $product, $form;
		if ($this.hasClass('product')) {
			$product = $this;
		} else {
			$product = $this.closest('.product');
			$form = $this;
			$form.off('woocommerce_update_variation_values', ideapark_remove_quantity_updater);
		}
		if ($product.length) {
			$product.off('ideapark_remove_quantity_updater', ideapark_remove_quantity_updater);
			var $quantity_updater = $product.find('.js-product-grid-quantity, .js-product-quantity');
			$quantity_updater.html('');
			$product.find('.c-product-grid__add-to-cart--hidden').removeClass('c-product-grid__add-to-cart--hidden');
			$product.find('.c-product__add-to-cart--hidden').removeClass('c-product__add-to-cart--hidden');
		}
	};
	
	root.ideapark_update_quantity = ideapark_debounce(function () {
		
		if ($ideapark_quantity_input && $ideapark_quantity_input.length) {
			var quantity = parseInt($ideapark_quantity_input.val().trim());
			var cart_item_key = $ideapark_quantity_input.data('cart-item-key');
			var $quantity_container = $ideapark_quantity_input.parent();
			if (cart_item_key) {
				
				var remove_loader = false;
				if ($quantity_container.find('.h-loading').length === 0) {
					var $loader = $('<i class="h-loading"></i>');
					$loader.css({
						width        : '16px',
						height       : '16px',
						'margin-left': '10px'
					});
					$quantity_container.append($loader);
					
					remove_loader = function () {
						$quantity_container.find('.h-loading').remove();
					};
				}
				
				$.ajax({
					url    : ideapark_wp_vars.ajaxUrl,
					type   : 'POST',
					data   : {
						action       : 'ideapark_update_quantity',
						quantity     : quantity,
						cart_item_key: cart_item_key
					},
					success: function (response) {
						if (remove_loader) {
							remove_loader();
						}
						if (response.error) {
							alert($error);
						}
						if (response.success) {
							if (ideapark_empty(response.quantity)) {
								$quantity_container.closest('.product').trigger('ideapark_remove_quantity_updater');
							}
							$(document.body).trigger('wc_fragment_refresh');
						}
					}
				});
			}
		}
		
	}, 500);
	
	root.ideapark_parallax_destroy = function () {
		if (ideapark_parallax_on && ideapark_simple_parallax_instances.length) {
		}
	};
	
	root.ideapark_parallax_init = function () {
		if (ideapark_parallax_on) {
			var images = document.querySelectorAll('.parallax');
			ideapark_simple_parallax_instances.push(new simpleParallax(images, {
				scale   : 1.5,
				overflow: true
			}));
		}
		
	};
	
	root.ideapark_init_home_promo_carousel = function ($product_list) {
		var l = ideapark_wp_vars.productMobileLayout;
		$product_list.addClass('owl-carousel').owlCarousel({
			center    : false,
			items     : 2,
			loop      : false,
			margin    : 28,
			nav       : true,
			dots      : false,
			navText   : [
				'<svg class="c-home-promo__prev-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>',
				'<svg class="c-home-promo__next-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>'
			],
			responsive: {
				0   : {
					items: 1
				},
				375 : {
					items: l === 'layout-product-2' ? 2 : 1
				},
				544 : {
					items: l === 'layout-product-2' ? 3 : (l === 'layout-product-3' ? 1 : 2)
				},
				810 : {
					items: l === 'layout-product-2' ? 4 : (l === 'layout-product-3' ? 2 : 3)
				},
				1076: {
					items: l === 'layout-product-2' ? 2 : (l === 'layout-product-3' ? 1 : 2)
				},
				1170: {
					items: 2
				}
			}
		});
	};
	
	root.ideapark_init_home_tab_carousel = function ($product_list) {
		var l = ideapark_wp_vars.productMobileLayout;
		$product_list.addClass('owl-carousel').owlCarousel({
			center    : false,
			items     : 4,
			loop      : false,
			margin    : 28,
			nav       : true,
			dots      : false,
			navText   : [
				'<svg class="h-carousel__prev-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>',
				'<svg class="h-carousel__next-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>'
			],
			responsive: {
				0   : {
					items: 1
				},
				375 : {
					items: l === 'layout-product-2' ? 2 : 1
				},
				544 : {
					items: l === 'layout-product-2' ? 3 : (l === 'layout-product-3' ? 1 : 2)
				},
				810 : {
					items: l === 'layout-product-2' ? 4 : (l === 'layout-product-3' ? 2 : 3)
				},
				1076: {
					items: l === 'layout-product-2' ? 6 : (l === 'layout-product-3' ? 3 : 4)
				},
				1170: {
					items: 4
				}
			}
		});
	};
	
	root.ideapark_init_home_brands_carousel = function () {
		$('.js-brands-carousel:not(.owl-carousel)').addClass('owl-carousel').owlCarousel({
			center    : false,
			items     : 5,
			loop      : false,
			margin    : 70,
			nav       : true,
			dots      : false,
			navText   : [
				'<svg class="h-carousel__prev-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>',
				'<svg class="h-carousel__next-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>'
			],
			responsive: {
				0   : {
					items: 1
				},
				580 : {
					items: 2
				},
				680 : {
					items: 3
				},
				980 : {
					items: 4
				},
				1280: {
					items: 5
				},
			}
		});
	};
	
	root.ideapark_init_home_testimonials_carousel = function () {
		$('.js-testimonials-carousel:not(.owl-carousel)').addClass('owl-carousel').owlCarousel({
			center : false,
			items  : 1,
			loop   : false,
			margin : 0,
			nav    : true,
			dots   : false,
			navText: [
				'<svg class="h-carousel__prev-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>',
				'<svg class="h-carousel__next-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>'
			]
		});
	};
	
	root.ideapark_init_product_thumbs_carousel = function () {
		if (!ideapark_layout_2) {
			$('.js-product-thumbs-carousel:not(.owl-carousel)').addClass('owl-carousel').owlCarousel({
				center : false,
				items  : 3,
				loop   : false,
				margin : 26,
				nav    : true,
				dots   : false,
				navText: [
					'<svg class="c-product__prev-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>',
					'<svg class="c-product__next-svg"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-angle-right" /></svg>'
				]
			});
			$('.js-single-product-thumb:not(.init)').addClass('init').click(function () {
				var index = $(this).data('index');
				$('.js-single-product-carousel').trigger("to.owl.carousel", [index, 300]);
			});
		}
	};
	
	root.ideapark_single_product_add_to_cart_ajax_switch = function () {
		
		if (ideapark_layout_3 && ideapark_wp_vars.productMobileAjaxATC) {
			$('.js-single-product-add-to-cart-variation').addClass('js-add-to-cart-variation');
			$('.js-single-product-add-to-cart-simple:not(.is-grouped)').addClass('add_to_cart_button ajax_add_to_cart');
			$('.js-footer--add-to-cart').addClass('c-footer--add-to-cart');
		} else {
			$('.js-single-product-add-to-cart-variation').removeClass('js-add-to-cart-variation');
			$('.js-single-product-add-to-cart-simple:not(.is-grouped)').removeClass('add_to_cart_button ajax_add_to_cart');
			$('.js-footer--add-to-cart').removeClass('c-footer--add-to-cart');
		}
	};
	
	root.ideapark_init_product_carousel = function () {
		if ($('.c-product__gallery-item').length > 1) {
			$('.js-single-product-carousel:not(.owl-carousel)').addClass('owl-carousel').owlCarousel({
				center: false,
				items : 1,
				loop  : false,
				margin: 0,
				nav   : false,
				dots  : false
			});
		}
		ideapark_init_product_thumbs_carousel();
	};
	
	root.ideapark_init_masonry = function (force) {
		if (ideapark_masonry_grid_on && !ideapark_is_masonry_init && !ideapark_layout_4 && ($window.scrollTop() > 0 || force)) {
			
			ideapark_is_masonry_init = true;
			
			$ideapark_masonry_grid.find('img.lazyload').each(function () {
				var $this = $(this);
				if ($this.data('src')) {
					$this.attr('src', $this.data('src'));
				}
				if ($this.data('srcset')) {
					$this.attr('srcset', $this.data('srcset'));
				}
				if ($this.data('sizes')) {
					$this.attr('sizes', $this.data('sizes'));
				}
				$this.removeClass('lazyload').addClass('lazyloaded');
			});
			
			require([
				ideapark_wp_vars.masonryUrl,
				ideapark_wp_vars.imagesloadedUrl
			], function (Masonry) {
				root.Masonry = Masonry;
				var msnry = new Masonry($ideapark_masonry_grid[0], {
					itemSelector   : '.js-post-item',
					columnWidth    : '.js-post-sizer',
					percentPosition: true
				});
				
				$ideapark_masonry_grid.imagesLoaded().progress(function () {
					msnry.layout();
				});
				
				$ideapark_masonry_grid.imagesLoaded(function () {
					msnry.layout();
				});
			});
		}
	};
	
	root.ideapark_init_view_more_item = function ($tab, href, postfix) {
		if ($tab && $tab.length) {
			var $li = $tab.find('.js-view-more-item');
			var new_item = false;
			if (!$li.length) {
				$li = $('<li class="c-product-grid__item c-product-grid__item--view-more js-view-more-item"><a class="c-product-grid__item-view-more" href="' + href + '">' + ideapark_wp_vars.viewMore + (postfix ? ' ' + postfix : '') + '</a>');
				new_item = true;
			}
			var $grid = $tab.find('.c-product-grid__list');
			
			var max_height = 0;
			$grid.find('.c-product-grid__item:not(.js-view-more-item)').each(function () {
				var height = $(this).outerHeight();
				if (height > max_height) {
					max_height = height;
				}
			});
			
			$li.css({height: max_height});
			
			if (new_item) {
				$grid.append($li);
				$tab.addClass('js-view-more-tab');
			}
		}
	};
	
	root.ideapark_init_home_promo = function () {
		var $promos = $(".js-home-promo-carousel");
		if ($promos.length) {
			$promos.each(function () {
				var $promo = $(this);
				
				var product_count = $promo.find('.c-product-grid__item').length;
				
				if ($promo.data('view-more') && $promo.data('per-page') == product_count) {
					ideapark_init_view_more_item($promo, $promo.data('view-more'));
				}
				
				ideapark_init_home_promo_carousel($promo.find('.c-product-grid__list'));
			});
		}
	};
	
	root.ideapark_init_home_tabs = function () {
		var $tabs = $(".c-home-tabs:not(.init)");
		if ($tabs.length) {
			$tabs.each(function () {
				var $tab = $(this);
				
				if ($tab.hasClass('js-product-carousel')) {
					
					$tab.find('.c-home-tabs__tab').each(function () {
						var $tab = $(this);
						var product_count = $tab.find('.c-product-grid__item').length;
						if ($tab.data('view-more') && $tab.data('per-page') == product_count) {
							ideapark_init_view_more_item($tab, $tab.data('view-more'));
						}
					});
					
					ideapark_init_home_tab_carousel($tab.find('.c-product-grid__list'));
				}
				
				$tab.find('.js-tab-select').change(function () {
					var $tab = $(this).closest('.c-home-tabs');
					$tab.find('.js-tab-title[href="' + $(this).val() + '"]').trigger('click');
				});
				
				$tab.addClass('init').find('.js-tab-title').click(function (e) {
					e.preventDefault();
					var $tab_title = $(this);
					var $tab_content = $($tab_title.attr('href'));
					var $li = $tab_title.parent('li');
					var $tab = $tab_title.closest('.c-home-tabs');
					if ($li.hasClass('c-home-tabs__header-item--active')) {
						return false;
					}
					
					$tab.find('.js-tab-select').val($tab_title.attr('href'));
					
					$tab.find('.c-home-tabs__header-item--active').removeClass('c-home-tabs__header-item--active h-wave');
					$li.addClass('c-home-tabs__header-item--active h-wave');
					
					$tab.find('.c-home-tabs__tab--active').removeClass('c-home-tabs__tab--active');
					$tab.find('.c-home-tabs__tab--visible').removeClass('c-home-tabs__tab--visible');
					$tab_content.addClass('c-home-tabs__tab--visible');
					
					setTimeout(function () {
						$tab_content.addClass('c-home-tabs__tab--active');
						setTimeout(function () {
							$tab_content.find('[data-src]').each(function () {
								var $this = $(this);
								$this.attr('srcset', $this.attr('data-srcset'));
								$this.attr('src', $this.attr('data-src'));
								$this.attr('sizes', $this.attr('data-sizes'));
								$this.removeAttr('data-srcset');
								$this.removeAttr('data-src');
								$this.removeAttr('data-sizes');
							});
						}, 500);
					}, 100);
				});
				
				
				$tab.find('.js-product-tab-filter').click(function (e) {
					e.preventDefault();
					e.stopPropagation();
					var $button = $(this);
					var $tab = $button.closest('.c-home-tabs__tab');
					var $product_list = $tab.find('.c-product-grid__list');
					
					if ($product_list.hasClass('c-product-grid__list--loading')) {
						return;
					}
					
					$button.addClass('loading');
					
					$product_list.addClass('c-product-grid__list--loading');
					
					$button.closest('ul').find('.c-ordering__filter-button--chosen').removeClass('c-ordering__filter-button--chosen');
					$button.find('.c-ordering__filter-button').addClass('c-ordering__filter-button--chosen');
					
					$.ajax({
						url    : ideapark_wp_vars.ajaxUrl,
						type   : 'POST',
						data   : {
							action      : 'ideapark_product_tab',
							tab         : $tab.data('tab'),
							index       : $tab.data('index'),
							filter_name : $button.data('filter-name'),
							filter_value: $button.data('filter-value'),
						},
						success: function (result) {
							$button.removeClass('loading');
							$product_list.removeClass('c-product-grid__list--loading');
							if (result.content) {
								$product_list.replaceWith(result.content);
								$product_list = $tab.find('.c-product-grid__list');
								var product_count = $product_list.find('.c-product-grid__item').length;
								
								if ($tab.data('view-more') && $tab.data('per-page') == product_count) {
									ideapark_init_view_more_item($tab, $button.attr('href'), $button.text());
								}
								var $view_more = $tab.find('.js-tab-view-more');
								if ($view_more.length) {
									$view_more.attr('href', $button.attr('href'));
									if ($tab.data('per-page') == product_count) {
										$view_more.removeClass('c-home-tabs__view-more-button--hide');
									} else {
										$view_more.addClass('c-home-tabs__view-more-button--hide');
									}
								}
								if ($tab.closest('.c-home-tabs').hasClass('js-product-carousel')) {
									ideapark_init_home_tab_carousel($tab.find('.c-product-grid__list'));
								}
								ideapark_init_custom_select();
								if (typeof IP_Wishlist != "undefined") {
									IP_Wishlist.init_product_button();
								}
								$product_list.find('.js-variations-form,.variations_form').each(function () {
									$(this).wc_variation_form();
								});
								$('.c-add-to-cart.disabled:not(.js-add-to-cart-variation)').removeClass('disabled');
							}
							//$ideapark_search_loader.remove();
						}
					});
				});
				
			});
		}
	};
	
	root.ideapark_init_custom_select = function () {
		$('select.styled:not(.hasCustomSelect), .c-ordering__select select:not(.hasCustomSelect), .variations select:not(.hasCustomSelect), .widget select:not(.hasCustomSelect):not(.woocommerce-widget-layered-nav-dropdown)').each(function () {
			$(this).customSelect({
				customClass: "c-custom-select",
				mapClass   : false,
			}).parent().find('.c-custom-select').append('<svg class="c-custom-select__angle"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-select" /></svg>');
		});
		
		$('.js-tab-select:not(.hasCustomSelect)').each(function () {
			$(this).customSelect({
				customClass: "h-wave c-home-tabs__header-custom-title",
				mapClass   : false,
			}).parent().find('.c-home-tabs__header-custom-title').after('<svg class="c-home-tabs__header-custom-angle"><use xlink:href="' + ideapark_wp_vars.svgUrl + '#svg-select" /></svg>');
		});
	};
	
	root.ideapark_get_notice_offset = function () {
		var $notice = $('.woocommerce-notices-wrapper--ajax');
		var offset = ideapark_is_mobile_layout || $notice.length ? ideapark_adminbar_visible_height : 0;
		if ($notice.length) {
			if (!ideapark_is_mobile_layout) {
				if (ideapark_sticky_active) {
					offset = $ideapark_desktop_sticky_row.outerHeight() + ideapark_adminbar_visible_height;
				}
			}
		}
		
		return offset;
	};
	
	root.ideapark_set_notice_offset = function (offset) {
		var $notice = $('.woocommerce-notices-wrapper');
		if ($notice.length) {
			if (typeof offset !== 'number') {
				offset = ideapark_get_notice_offset();
			}
			$notice.css({
				transform: 'translateY(' + offset + 'px)',
			});
		}
	};
	
	root.ideapark_to_top_button = function () {
		if ($ideapark_to_top_button.length) {
			if ($window.scrollTop() > 500) {
				if (!$ideapark_to_top_button.hasClass('c-to-top-button--active')) {
					$ideapark_to_top_button.addClass('c-to-top-button--active');
				}
			} else {
				if ($ideapark_to_top_button.hasClass('c-to-top-button--active')) {
					$ideapark_to_top_button.removeClass('c-to-top-button--active');
				}
			}
		}
	};
	
	root.ideapark_sticky_sidebar = function () {
		
		if (ideapark_wp_vars.stickySidebar && $ideapark_sticky_sidebar.length && $ideapark_sticky_sidebar_nearby.length) {
			
			var sb = $ideapark_sticky_sidebar;
			var content = $ideapark_sticky_sidebar_nearby;
			
			if (ideapark_is_mobile_layout) {
				
				if (ideapark_sticky_sidebar_old_style !== null) {
					sb.attr('style', ideapark_sticky_sidebar_old_style);
					ideapark_sticky_sidebar_old_style = null;
				}
				
			} else {
				
				var sb_height = sb.outerHeight(true);
				var content_height = content.outerHeight(true);
				var content_top = content.offset().top;
				var scroll_offset = $window.scrollTop();
				var window_width = $window.width();
				
				var top_panel_fixed_height = ideapark_sticky_active ? $ideapark_desktop_sticky_row.outerHeight() + ideapark_adminbar_visible_height + 25 : ideapark_adminbar_visible_height;
				
				if (sb_height < content_height && scroll_offset + top_panel_fixed_height > content_top) {
					
					var sb_init = {
						'position': 'undefined',
						'float'   : 'none',
						'top'     : 'auto',
						'bottom'  : 'auto'
					};
					
					if (typeof ideapark_scroll_offset_last == 'undefined') {
						root.ideapark_sb_top_last = content_top;
						root.ideapark_scroll_offset_last = scroll_offset;
						root.ideapark_scroll_dir_last = 1;
						root.ideapark_window_width_last = window_width;
					}
					
					var scroll_dir = scroll_offset - ideapark_scroll_offset_last;
					if (scroll_dir === 0) {
						scroll_dir = ideapark_scroll_dir_last;
					} else {
						scroll_dir = scroll_dir > 0 ? 1 : -1;
					}
					
					var sb_big = sb_height + 30 >= $window.height() - top_panel_fixed_height,
						sb_top = sb.offset().top;
					
					if (sb_top < 0) {
						sb_top = ideapark_sb_top_last;
					}
					
					if (sb_big) {
						
						if (scroll_dir != ideapark_scroll_dir_last && sb.css('position') == 'fixed') {
							sb_init.top = sb_top - content_top;
							sb_init.position = 'absolute';
							
						} else if (scroll_dir > 0) {
							if (scroll_offset + $window.height() >= content_top + content_height + 30) {
								sb_init.bottom = 0;
								sb_init.position = 'absolute';
							} else if (scroll_offset + $window.height() >= (sb.css('position') == 'absolute' ? sb_top : content_top) + sb_height + 30) {
								
								sb_init.bottom = 30;
								sb_init.position = 'fixed';
							}
							
						} else {
							
							if (scroll_offset + top_panel_fixed_height <= sb_top) {
								sb_init.top = top_panel_fixed_height;
								sb_init.position = 'fixed';
							}
						}
						
					} else {
						if (scroll_offset + top_panel_fixed_height >= content_top + content_height - sb_height) {
							sb_init.bottom = 0;
							sb_init.position = 'absolute';
						} else {
							sb_init.top = top_panel_fixed_height;
							sb_init.position = 'fixed';
						}
					}
					
					if (sb_init.position != 'undefined') {
						
						if (sb.css('position') != sb_init.position || ideapark_scroll_dir_last != scroll_dir || ideapark_window_width_last != window_width) {
							
							root.ideapark_window_width_last = window_width;
							sb_init.width = sb.parent().width();
							
							if (ideapark_sticky_sidebar_old_style === null) {
								var style = sb.attr('style');
								if (!style) {
									style = '';
								}
								ideapark_sticky_sidebar_old_style = style;
							}
							sb.css(sb_init);
						}
					}
					
					root.ideapark_sb_top_last = sb_top;
					root.ideapark_scroll_offset_last = scroll_offset;
					root.ideapark_scroll_dir_last = scroll_dir;
					
				} else {
					if (ideapark_sticky_sidebar_old_style !== null) {
						sb.attr('style', ideapark_sticky_sidebar_old_style);
						ideapark_sticky_sidebar_old_style = null;
					}
					
				}
			}
			
		}
	};
	
	root.ideapark_third_party_reload = function () {
		if (typeof root.sbi_init === "function") {
			window.sbiCommentCacheStatus = 0;
			root.sbi_init(function (imagesArr, transientName) {
				root.sbi_cache_all(imagesArr, transientName);
			});
		}
	};
	
	root.ideapark_hash_menu_animate = function (e) {
		var $this = $(this), $el;
		if (ideapark_isset(e)) {
			e.preventDefault();
		}
		if ($this.attr('href').length > 1 && ($el = $($this.attr('href'))) && $el.length) {
			var offset = $el.offset().top - 25 - (ideapark_adminbar_position === 'fixed' ? ideapark_adminbar_height : 0);
			if (ideapark_is_mobile_layout) {
				ideapark_mobile_menu_popup(false);
				if ($ideapark_mobile_sticky_row.length) {
					offset -= $ideapark_mobile_sticky_row.outerHeight();
				}
			} else if (ideapark_wp_vars.stickyMenuDesktop && $ideapark_desktop_sticky_row.length && $ideapark_desktop_mega_menu.length) {
				offset -= $ideapark_desktop_sticky_row.outerHeight();
			}
			$('html, body').animate({scrollTop: offset}, 800);
		}
	};
	
	root.ideapark_init_notice = function () {
		var $notices;
		var $wrapper = $('.woocommerce-notices-wrapper');
		if ($wrapper.length && $wrapper.hasClass('woocommerce-notices-wrapper--ajax')) {
		} else {
			if ($wrapper.length) {
				if ($wrapper.text().trim() != '') {
					$notices = $wrapper.find('.woocommerce-notice').detach();
				}
				$wrapper.remove();
			} else {
				$notices = $('.woocommerce .woocommerce-notice').detach();
			}
			var $header = $('.c-page-header');
			if (!$header.length) {
				$header = $('#main-header');
			}
			$wrapper = $('<div class="woocommerce-notices-wrapper woocommerce-notices-wrapper--ajax"></div>');
			$header.after($wrapper);
		}
		ideapark_notice_top = parseInt($wrapper.css('top').replace('px', ''));
		ideapark_mega_menu_sticky_wpadmin(null, ideapark_adminbar_visible_height);
		if ($notices && $notices.length) {
			ideapark_show_notice($notices);
		}
	};
	
	root.ideapark_show_notice = function (notice) {
		if (ideapark_empty(notice)) {
			return;
		}
		var $wrapper = $('.woocommerce-notices-wrapper');
		var $notices = notice instanceof jQuery ? notice : $(notice);
		var is_new = !$wrapper.find('.woocommerce-notice').length;
		if (is_new) {
			$wrapper.css({display: 'none'});
		}
		$notices.addClass('shown');
		$wrapper.append($notices);
		ideapark_mega_menu_sticky_wpadmin(null, ideapark_adminbar_visible_height);
		if (is_new) {
			var dif = $wrapper.outerHeight() + 150;
			$wrapper.css({top: (ideapark_notice_top - dif) + 'px'});
			$wrapper.css({display: ''});
			$({y: ideapark_notice_top}).animate({y: ideapark_notice_top + dif}, {
				step    : function (y) {
					$wrapper.css({
						top: (y - dif) + 'px',
					});
				},
				duration: 500,
				complete: function () {
					$wrapper.addClass('woocommerce-notices-wrapper--transition');
				}
			});
		}
		
		$notices.find('.js-notice-close').each(function () {
			var $close = $(this);
			setTimeout(function () {
				$close.trigger('click');
			}, 5000);
		});
	};
	
	$.fn.extend({
		ideapark_button: function (option, size) {
			return this.each(function () {
				var $this = $(this);
				if (typeof size === 'undefined') {
					size = 16;
				}
				if (option === 'loading' && !$this.hasClass('js-loading')) {
					$this.data('button', $this.html());
					$this.data('css-width', $this.css('width'));
					$this.data('css-height', $this.css('height'));
					$this.css('height', $this.outerHeight());
					$this.css('width', $this.outerWidth());
					var $loader = $('<i class="h-loading c-add-to-cart__loading"></i>');
					$loader.css({
						width : size + 'px',
						height: size + 'px',
					});
					$this.html($loader);
					$this.addClass('h-after-before-hide js-loading');
				} else if (option === 'reset' && $this.hasClass('js-loading')) {
					var css_width = $this.data('css-width');
					var css_height = $this.data('css-height');
					var content = $this.data('button');
					$this.data('button', '');
					$this.data('css-width', '');
					$this.data('css-height', '');
					$this.html(content);
					$this.removeClass('h-after-before-hide js-loading');
					$this.css('width', css_width);
					$this.css('height', css_height);
				}
			})
		}
	});
	
	ideapark_wc_variations_image_update();
})(jQuery, window);


(function ($, root, undefined) {
	
	$(function () {
		"use strict";
		
		var ajax = new XMLHttpRequest();
		ajax.open("GET", ideapark_wp_vars_mega_menu.themeUri + "/assets/img/sprite.svg", true);
		ajax.send();
		ajax.onload = function (e) {
			var div = document.createElement("div");
			div.className = "wtef-svg-sprite-container";
			div.innerHTML = ajax.responseText;
			document.body.insertBefore(div, document.body.childNodes[0]);
		};
		
		$('#menu-to-edit').on('click', '.wtef-svg-icons input[type=radio]', function () {
			$(this).closest('.wtef-svg-icons').find('.clear').addClass('show');
		});
		
		$('#menu-to-edit').on('click', '.wtef-svg-icons .clear', function () {
			var _ = $(this);
			var $container = _.closest('.wtef-svg-icons');
			if (_.hasClass('clpse')) {
				//$container.find('input[type=hidden]').val('');
				$container.html('<a href="#" class="ip-load-mega-menu" data-svg-id="" data-img-id="" data-item-id="' + _.data('item-id') + '">' + ideapark_wp_vars_mega_menu.select_icon_text + '</a>');
			} else {
				$container.find('input[type=radio]:checked').removeAttr('checked');
				$container.find('.clear').removeClass('show');
			}
			
			return false;
		});
		
		var ip_load_mega_menu_ajax = null;
		
		$('#menu-to-edit').on('click', '.ip-load-mega-menu', function () {
			if (ip_load_mega_menu_ajax) {
				return;
			}
			var _ = $(this);
			var $spinner = _.closest('.wtef-svg-icons').find('.spinner');
			
			$spinner.addClass('is-active');
			
			ip_load_mega_menu_ajax = $.ajax({
				url    : ajaxurl,
				type   : 'POST',
				data   : {
					action : 'ideapark_load_mega_menu',
					item_id: _.data('item-id'),
					svg_id : _.data('svg-id'),
					img_id : _.data('img-id')
				},
				success: function (results) {
					$("#ip-mega-menu-" + _.data('item-id')).html(results);
					ip_load_mega_menu_ajax = null;
				}
			}).always(function () {
				$spinner.removeClass('is-active');
			});
			
			return false;
		});
		
		
		$('#menu-to-edit').on('click', '.wtef-svg-icons .menu-item-custom', function (event) {
			
			event.preventDefault();
			
			var ideapark_file_frame;
			var ideapark_file_frame_item_id;
			
			ideapark_file_frame_item_id = $(this).data('item-id');
			
			ideapark_file_frame = wp.media.frames.downloadable_file = wp.media({
				title   : ideapark_wp_vars_mega_menu.chose_image_text,
				button  : {
					text: ideapark_wp_vars_mega_menu.chose_image_text
				},
				multiple: false
			});
			
			ideapark_file_frame.on('select', function () {
				var attachment = ideapark_file_frame.state().get('selection').first().toJSON();
				var $li = $('#menu-item-custom-' + ideapark_file_frame_item_id);
				$('input[type=radio]', $li).val('custom-' + attachment.id).prop("checked", true);
				if (attachment.mime == "image/svg+xml") {
					$('.img', $li).html('<img src="' + attachment.url + '">');
				} else {
					if (ideapark_empty(attachment.sizes.thumbnail)) {
						$('.img', $li).html('<img src="' + attachment.url + '">');
					} else {
						$('.img', $li).html('<img src="' + attachment.sizes.thumbnail.url + '">');
					}
				}
			});
			
			ideapark_file_frame.open();
		});
		
		$("#locations-megamenu").click(function () {
			if (this.checked) {
				$('#menu-to-edit').addClass('ip-set-megamenu');
			} else {
				$('#menu-to-edit').removeClass('ip-set-megamenu');
			}
		});
		
		if ($("#locations-megamenu:checked").length == 1) {
			$('#menu-to-edit').addClass('ip-set-megamenu');
		}
		
	});
	
	
})(jQuery, this);

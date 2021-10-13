(function ($) {
	var fixHelper = function (e, ui) {
		ui.children().children().each(function () {
			$(this).width($(this).width());
		});
		return ui;
	};
	$('table.posts #the-list').sortable({
		'items' : 'tr',
		'axis'  : 'y',
		'helper': fixHelper,
		'update': function (e, ui) {
			$.post(ajaxurl, {
				action: 'update-post-order',
				order : $('#the-list').sortable('serialize'),
			});
		}
	});
})(jQuery);
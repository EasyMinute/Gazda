(function($) {
	'use strict';

	const ShipAreaAdminJS = {

		/**
		 * Start the engine.
		 *
		 * @since 2.0.0
		 */
		init: function() {

			// Document ready
			$( document ).ready( ShipAreaAdminJS.ready );

			// Page load
			$( window ).on( 'load', ShipAreaAdminJS.load );

			// Document ready
			$( document ).on( 'ShipAreajsReady', ShipAreaAdminJS.start );

			// register events and hooks
			ShipAreaAdminJS.bindEvents();
		},
		/**
		 * Document ready.
		 *
		 * @since 2.0.0
		 */
		ready: function() {

		},
		/**
		 * Page load.
		 *
		 * @since 2.0.0
		 */
		load: function() {

		},

		start: function() {
			// Set user identifier
			$( document ).trigger( 'ShipAreaAdminjsStarted' );
		},

		// --------------------------------------------------------------------//
		// Binds
		// --------------------------------------------------------------------//
		/**
		 * Events bindings.
		 *
		 * @since 2.0.0
		 */
		bindEvents: function() {
			
			$(document).on('click', '#shiparea_button_verify_license', ShipAreaAdminJS.verifyLicense);
			$(document).on('click', '#shiparea_button_deactivate_license', ShipAreaAdminJS.deactivateLicense);
		},

		verifyLicense: function(event) {
			event.preventDefault();

			var license = $('#shiparea_key_license').val();

			$('#shiparea_message_license').empty();
			$('#shiparea_loading_license').empty().append('<img src="' + shiparea_vars.url_loading + '" alt="loading" />');
			$('#shiparea_key_license').attr('disabled', 'disabled');
			$('#shiparea_button_verify_license').attr('disabled', 'disabled');


			$.ajax({
				url : shiparea_vars.url_ajax,
				dataType: 'json',
				type: 'POST',
				data: { action: 'shiparea_verify_license', license: license, wpnonce : shiparea_vars.nonce },
				success: function (response) {

					if( response.success == true ) {
						$('#shiparea_message_license').append('<span style="color:green;"><img src="' + shiparea_vars.url_success + '" alt="yes" />' + response.data + '<span>');
					}
					else
						$('#shiparea_message_license').append('<span style="color:red;"><img src="' + shiparea_vars.url_failure + '" alt="no" />' + response.data + '</span>');

					$('#shiparea_loading_license').empty();
					$('#shiparea_key_license').removeAttr('disabled');
					$('#shiparea_button_verify_license').removeAttr('disabled');
				}
			});
		},

		deactivateLicense:  function(event) {
			event.preventDefault();

			$('#shiparea_message_license').empty();
			$('#shiparea_loading_license').empty().append('<img src="' + shiparea_vars.url_loading + '" alt="loading" />');
			$('#shiparea_key_license').attr('disabled', 'disabled');
			$('#shiparea_button_verify_license').attr('disabled', 'disabled');
			$('#shiparea_button_deactivate_license').attr('disabled', 'disabled');


			$.ajax({
				url : shiparea_vars.url_ajax,
				dataType: 'json',
				type: 'POST',
				data: { action: 'shiparea_deactivate_license', wpnonce : shiparea_vars.nonce },
				success: function (response) {

					if( response.success == true ) {
						$('#shiparea_message_license').append('<span style="color:green;"><img src="' + shiparea_vars.url_success + '" alt="yes" />' + response.data + '<span>');
						$('#shiparea_key_license').val('');
					}
					else
						$('#shiparea_message_license').append('<span style="color:red;"><img src="' + shiparea_vars.url_failure + '" alt="no" />' + response.data + '</span>');

					$('#shiparea_loading_license').empty();
					$('#shiparea_key_license').removeAttr('disabled');
					$('#shiparea_button_verify_license').removeAttr('disabled');
					$('#shiparea_button_deactivate_license').removeAttr('disabled');
				}
			});
		}
	};

	// Initialize.
	ShipAreaAdminJS.init();

	// Add to global scope.
	window.shiprateAdmin = ShipAreaAdminJS;

})(jQuery);
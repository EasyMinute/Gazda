function init() {
	var autocomplete = new google.maps.places.Autocomplete(
			document.getElementById(shipaddress_var.input),
			shipaddress_var.opts);

	autocomplete.addListener('place_changed', function() {

		var place = autocomplete.getPlace();

		if (!place.geometry) {
			// User entered the name of a Place that was not suggested and
			// pressed the Enter key, or the Place Details request failed.
			window.alert(shipaddress_var + ' : ' + place.name);
			return;
		}

		var lat = place.geometry.location.lat();
		var lng = place.geometry.location.lng();

		var CountryState = jQuery('#shiparea_verify_address_select').val();

		jQuery('#shiparea_verify_address_result' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});

		jQuery.post(
			shipaddress_var.ajaxurl,
			{
				action		: shipaddress_var.action,
				location	: CountryState,
				lat 		: lat,
				lng 		: lng,
				wpnonce		: shipaddress_var.wpnonce
			},
			function(response) {		
				jQuery('#shiparea_verify_address_result').html(response.output);
				jQuery('#shiparea_verify_address_result').unblock();
		});

	});
}

google.maps.event.addDomListener(window, 'load', init);
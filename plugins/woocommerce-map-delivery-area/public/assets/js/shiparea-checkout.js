function init_autocomplete() {

	let autocomplete = [], nodes = [], filler, place, component, field_id;

	for (let i = 0; i < shiparea_vars.nodes.length; i++) {

		nodes[i] = document.getElementById(shiparea_vars.nodes[i]);

		autocomplete[i] = new google.maps.places.Autocomplete(nodes[i], shiparea_vars.opts);

		autocomplete[i].setFields(['address_component', 'formatted_address']);

		// If customer clicks in an address
		google.maps.event.addListener(autocomplete[i], 'place_changed', function() {

			// Get all information from the addresss
			place = autocomplete[i].getPlace();

			// Address format is changed
			nodes[i].value = place.formatted_address;


			// If it has to fill external fields
			if( typeof(shiparea_vars.filler[shiparea_vars.nodes[i]]) != 'undefined' ) {

				filler = shiparea_vars.filler[shiparea_vars.nodes[i]];	

				for (let j = 0; j < place.address_components.length; j++) {
					component = place.address_components[j];

					jQuery.each(filler, function(key_component, field_woocommerce){

						field_id = symbol_autocomplete(field_woocommerce);

						if( component.types[0] == key_component &&
							jQuery(field_id).is('input:text') &&
							jQuery(field_id).length > 0
						) {
							jQuery(field_id).val(component.long_name);
						}
					});
				}
			}

			jQuery(document.body).trigger("update_checkout");
		});
	}
}


function symbol_autocomplete( key = '' ) {

	if( key.length == 0 )
		return false;

	if( key.indexOf('#') != -1 )
		return key;

	return '#' + key;
}


google.maps.event.addDomListener(window, 'load', init_autocomplete);
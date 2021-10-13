function init() {
	var map;
	var polygon

	for(var i = 0; i < shipmap.length; i++) {
		map = create_map(i);
		polygon = create_polygon(i);

		polygon.setMap(map);
	}
}

function create_map(i) {

	var mapOptions = {
		center: new google.maps.LatLng(parseFloat(shipmap[i]['lat']), parseFloat(shipmap[i]['lng'])),
		zoom: parseInt(shipmap[i]['zoom']),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: false,
		zoomControl: true,
		zoomControlOptions: { style: google.maps.ZoomControlStyle.DEFAULT }
	};

	console.log(mapOptions);

	var map = new google.maps.Map(document.getElementById('areamaps-' + shipmap[i]['id']), mapOptions);

	return map;
}

function create_polygon(i) {

	var polygon = new google.maps.Polygon({
    	paths: [shipmap[i]['coords']],
    	strokeColor: shipmap[i]['lcolor'],
    	strokeOpacity: 0.8,
    	strokeWeight: 3,
    	fillColor: shipmap[i]['lcolor'],
    	fillOpacity: 0.35
	});

	return polygon;
}


google.maps.Polygon.prototype.getBounds = function() {
	var bounds = new google.maps.LatLngBounds();
	var paths = this.getPaths();
	var path;        
	
	for (var i = 0; i < paths.getLength(); i++) {
		path = paths.getAt(i);
		
		for (var ii = 0; ii < path.getLength(); ii++) {
			bounds.extend(path.getAt(ii));
		}
	}

	return bounds;
}

google.maps.event.addDomListener(window, 'load', init);
$(document).ready(function() {


	var openstreetUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var basemap = new L.TileLayer(openstreetUrl, {maxZoom: 18});

	var map = new L.Map('map', {
		layers: [basemap],
		center: new L.LatLng(-30.036384, -51.216524), zoom: 13
	});

    new L.Control.GeoSearch({
        provider: new L.GeoSearch.Provider.OpenStreetMap()
    }).addTo(map);


   map.on('geosearch_showlocation', function (result) {
    L.marker([result.x, result.y]).addTo(map)
});

});
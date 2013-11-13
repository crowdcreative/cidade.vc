$(document).ready(function() {

	var map = L.map('map').setView([-30.036384, -51.216524], 13);
	L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: false}).addTo(map);

	new L.Control.GeoSearch({
	    provider: new L.GeoSearch.Provider.OpenStreetMap(),
	    position: 'topcenter',
	    showMarker: true
	}).addTo(map);
});
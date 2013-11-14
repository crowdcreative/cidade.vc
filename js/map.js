$(document).ready(function(){

		var geocoder = new google.maps.Geocoder();

		$('#map-canvas').gmap3({
			map: {
				options: {
					center: [-30.036363, -51.214786],
					zoom: 13,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					mapTypeControl: true,
					mapTypeControlOptions: {
						style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
					},
					navigationControl: true,
					scrollwheel: true,
					streetViewControl: true
				}
			},
			marker: {
				latLng: [-30.036363, -51.214786],
				options: {
					draggable: true
				},
				events: {
					dragend: function(marker) {
						$(this).gmap3({
							getaddress: {
								latLng: marker.getPosition(),
								callback: function(results) {
									var map = $(this).gmap3("get"),
										infowindow = $(this).gmap3({
											get: "infowindow"
										}),
										content = results && results[1] ? results && results[0].formatted_address : "no address";
										contentCut = content.split(', Porto Alegre')[0];
									if (infowindow) {
										infowindow.open(map, marker);
										infowindow.setContent(contentCut);
									} else {
										$(this).gmap3({
											infowindow: {
												anchor: marker,
												options: {
													content: content
												}
											}
										});
									}
								}
							}
						});
					},
					drag: function() {
						updateMarkerStatus('Dragging...');
					}

				}
			}
		});

		var map = $(this).gmap3("get");
		

		function geocodePosition(pos) {
		  geocoder.geocode({
		    latLng: pos
		  }, function(responses) {
		    if (responses && responses.length > 0) {
		      updateMarkerAddress(responses[0].formatted_address);
		    } else {
		      updateMarkerAddress('Cannot determine address at this location.');
		    }
		  });
		}

		function updateMarkerStatus(str) {
		  document.getElementById('markerStatus').innerHTML = str;
		}

		function updateMarkerPosition(latLng) {
		  	$("#info").text(latLng);
		
		
		}

		function updateMarkerAddress(str) {
		  $("address").text(str);
		}


		  // Update current position info.
		  updateMarkerPosition(latLng);
		  geocodePosition(latLng);
		  
	

		
	

  });
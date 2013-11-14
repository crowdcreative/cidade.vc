$(document).ready(function(){

		var geocoder = new google.maps.Geocoder();

		function contains(str, text) {
  		 	return (str.indexOf(text) >= 0);
		}

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

									if(contains(content, 'Porto Alegre')) {
										contentEcho = content.split(', Porto Alegre')[0];
									}else{
										contentEcho = content;
									}


									$('#search_address').val(contentEcho);


										
									if (infowindow) {
										infowindow.open(map, marker);
										infowindow.setContent(contentEcho);
									} else {
										$(this).gmap3({
											infowindow: {
												anchor: marker,
												options: {
													content: contentEcho
												}
											}
										});
									}
								}
							},
						});
					}
				}
			}
		});

		var map = $(this).gmap3("get");
		
		function buscaLatlong(endereco) {
			$("#map-canvas").gmap3({
				clear: {
					name: "marker"
				},
				getlatlng: {
					address: endereco,
					callback: function(results) {
						if (!results) return;
						$(this).gmap3({
							marker: {
								latLng: results[0].geometry.location
							}
						});
						var map = $(this).gmap3("get");
						var latLng = results[0].geometry.location; //Makes a latlng
      					map.panTo(latLng); //Make map global

					}
				},
			});
		}
	
		buscaLatlong("Rua Rivadávia Correia 08");
		  
	

		
	

  });
$(document).ready(function(){



 


		// chama o geocoder do Google
		var geocoder = new google.maps.Geocoder();

		// Efetua o corte de parte da string retornada do geocoder - endereço
		function contains(str, text) {
  		 	return (str.indexOf(text) >= 0);
		}

		// Criação do mapa
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
									
									content = results && results[0] ? results && results[0].formatted_address : "no address";

									// Corta a string do endereco se tiver a palavra Porto Alegre
									if(contains(content, 'Porto Alegre')) {
										contentEcho = content.split(', Porto Alegre')[0];
									}else{
										contentEcho = content;
									}

									// Exibe o endereco recortado na barra de busca
									$('#evento_endereco').val(contentEcho);


									// Cria o balão de informação com o endereço do marcador - dragend	
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
						
						var latlng = marker.getPosition();
						$('#evento_latlong').val(latlng);

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
						if (!results){ 
							alert('Endereço não encontrado. Tente buscar outro.');
						}else{
						$(this).gmap3({
							marker: {
								latLng: results[0].geometry.location,
								options:{
									draggable:true
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
													
													content = results && results[0] ? results && results[0].formatted_address : "Endereço não encontrado =(";

													if(contains(content, 'Porto Alegre')) {
														contentEcho = content.split(', Porto Alegre')[0];
													}else{
														contentEcho = content;
													}


													$('#evento_endereco').val(contentEcho); 


														
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
													
													// Coloca o latlong no input
													var latlng = marker.getPosition();
													$('#evento_latlong').val(latlng); 

												}
											},
										});
									}
								}
							}
						});
						}
						var map = $(this).gmap3("get");
						var latLng = results[0].geometry.location; //Makes a latlng
      					map.panTo(latLng); //Make map global

      					
						$('#evento_latlong').val(latLng);

					}
				},
			});
		}
	
		// Botao para chamar o endereço no mapa
		$("#botao").click(function(){
			var endereco = $("#evento_endereco").val();
			buscaLatlong(endereco);
		});
	
		
	

  });
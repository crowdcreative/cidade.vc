<?php
/**
 * Template Name: Criar lugar saúde
 */
?>

	<?php get_header(); ?>

	<?php require 'criar-editar-lugar-functions.php'; ?>

	<!-- Define vars para o mapa -->
	
	<?php
	$latlong = get_post_meta($post->ID, 'latlong', true);

	if($latlong != ''){
		$latlong = str_replace('(', '', $latlong);
		$latlong = str_replace(')', '', $latlong);
	}else{
		$latlong = '-30.036363, -51.214786';
	}
	?>


	<script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/jquery.ui.datepicker.min.js"></script>
	<script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/jquery.maskedinput.min.js"></script>

	<script type="text/javascript">


		jQuery(function($){
			$(".data").mask("99/99/9999");
			$(".hora").mask("99:99");
			$(".telefone").mask("(99)9999.9999");
		});


		jQuery(function($){
			$('.datepicker').datepicker({dateFormat: 'dd/mm/yy', dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'], dayNamesMin: ['D','S','T','Q','Q','S','S','D'], dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'], monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'], monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'], nextText: 'Próximo', prevText: 'Anterior'});
		});



		jQuery(function(jQuery) {  
          
	        jQuery('.custom_upload_image_button').click(function() {  
	            formfield = jQuery(this).siblings('.custom_upload_image');  
	            preview = jQuery(this).siblings('.custom_preview_image');  
	            tb_show('', 'media-upload.php?type=image&TB_iframe=true');  
	            window.send_to_editor = function(html) {  
	                imgurl = jQuery('img',html).attr('src');  
	                classes = jQuery('img', html).attr('class');  
	                id = classes.replace(/(.*?)wp-image-/, '');  
	                formfield.val(id);  
	                preview.attr('src', imgurl);  
	                tb_remove();  
	            }  
	            return false;  
	        });  
	          
	        jQuery('.custom_clear_image_button').click(function() {  
	            var defaultImage = jQuery(this).parent().siblings('.custom_default_image').text();  
	            jQuery(this).parent().siblings('.custom_upload_image').val('');  
	            jQuery(this).parent().siblings('.custom_preview_image').attr('src', defaultImage);  
	            return false;  
	        });  
	      
	    });  



		// Deslizar de forma suave (http://css-tricks.com/snippets/jquery/smooth-scrolling/)	
		$(function() {
			$('a[href*=#]:not([href=#])').click(function() {
				if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
					if (target.length) {
						$('html,body').animate({
							scrollTop: target.offset().top
						}, 1000);
						return false;
					}
				}
			});
		});
	


	$(document).ready(function(){



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
									$('#endereco').val(contentEcho);


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
						$('#latlong').val(latlng);

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


													$('#endereco').val(contentEcho); 


														
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
													$('#latlong').val(latlng); 

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

      					
						$('#latlong').val(latLng);

					}
				},
			});
		}
	
		// Botao para chamar o endereço no mapa
		$("#botao").click(function(){
			var endereco = $("#endereco").val();
			buscaLatlong(endereco);
		});


		$('.especificas_ou_semana input').on('change', function(){
			var marcado = $('input[name=grupo1]:checked', '.especificas_ou_semana').val();
			if(marcado == 'datas_especificas'){
				$('.especificas').fadeIn();
				$('.semana').fadeOut();
			}else{
				$('.especificas').fadeOut();
				$('.semana').fadeIn();
			}
		});

		var top = $('#anchorlinks').offset().top - parseFloat($('#anchorlinks').css('marginTop').replace(/auto/, 0));
		$(window).scroll(function(event) {
			var y = $(this).scrollTop() + 70;
			//if y > top, it means that if we scroll down any more, parts of our element will be outside the viewport
			//so we move the element down so that it remains in view.

			if (y >= top) {
				var difference = y - top;
				$('#anchorlinks').css("position", "fixed");
				$('#anchorlinks').css("top", "70px");
				var widthCopy = $('.copy-width').width();
				$('#anchorlinks').css("width", widthCopy);
			} else {
				$('#anchorlinks').css("position", "relative");
				$('#anchorlinks').css("top", 0);
			}

		});

		// Ajeita o tamnho da sidebar ao alterar o tamanho da janela do browser
		$(window).resize(function(event) {
			var widthCopy = $('.copy-width').width();
			$('#anchorlinks').css("width", widthCopy);
		});


		// chama o geocoder do Google
		var geocoder = new google.maps.Geocoder();

		// Efetua o corte de parte da string retornada do geocoder - endereço
		function contains(str, text) {
  		 	return (str.indexOf(text) >= 0);
		}

		// Mostra o scroll-top após rolar a tela

		$(window).scroll(function(event) {

			var y = $(this).scrollTop();

			if (y >= 500) {
				$('#buttonScroll-top').fadeIn();
			} else {
				$('#buttonScroll-top').fadeOut();
			}
		});



		// Scroll to top
	    $('a[href=#top]').click(function(){
	        $('html, body').animate({scrollTop:0}, 'slow');
	        return false;
	    });


		


  });

</script>



<a href="#top"><div id="buttonScroll-top" class="glyphicon glyphicon-circle-arrow-up" style="display:none"></div></a>


<div id="page" class="single container">
	
		<div class="row">


			<div id="single-col-left" class="col-md-3">

				<div class="panel panel-default copy-width">

					<div class="panel-body">

						<p>Esta é a agenda de eventos e atividades de Porto Alegre, criada de forma colaborativa por todos os que querem contribuir.</p><br/>

						<p>A moderação da publicação se dará por meio do administrador do projeto Cidade.vc.</p><br/>

						<p>Poderão ser cadastradas agendas relativas a: lazer, saúde, esporte, educação, cultura e alimentação.</p> 

					</div>

				</div>

			</div>


			<div class="col-md-9">
				
				<div class="panel panel-default form-group">

						<div class="panel-body">



		<?php require'models/criar-editar/criar-lugar-saude-inputs.php'; ?> 




<?php


if ( isset( $_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
 
 
    $post_information = array(
        'post_title' => wp_strip_all_tags( $_POST['post_title'] ),
        'post_type' => 'lugar-saude',
        'post_status' => 'pending'
    );
 
   	$post_id = wp_insert_post($post_information);



 	require'models/criar-editar/criar-lugar-saude-saves.php';

	
}




?>


					</div>

				</div>

			</div>
		
		</div>




		<?php get_footer(); ?>
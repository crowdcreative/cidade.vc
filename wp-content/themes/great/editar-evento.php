<?php
/**
 * Template Name: Editar evento
 */
?>

<?php get_header(); ?>
<?php $options = get_option('great'); ?>

<!-- Define vars para o mapa -->
	
	<?php


	$query = new WP_Query(array('post_type' => 'eventos', 'posts_per_page' =>'-1', 'post_status' => array('publish', 'pending', 'draft', 'private', 'trash') ) );

	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
	
		if(isset($_GET['post'])) {
			
			if($_GET['post'] == $post->ID){
				
				$current_post = $post->ID;


	$latlong = get_post_meta($post->ID, 'evento_latlong', true);
	$latlong = str_replace('(', '', $latlong);
	$latlong = str_replace(')', '', $latlong);

	
	?>

	<script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/jquery.ui.datepicker.min.js"></script>
	<script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/jquery.maskedinput.min.js"></script>
	

	<script type="text/javascript">


		jQuery(function($){
			$(".data").mask("99/99/9999");
			$(".hora").mask("99:99");
		});


		jQuery(function($){
			$('.datepicker').datepicker({dateFormat: 'dd/mm/yy', dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'], dayNamesMin: ['D','S','T','Q','Q','S','S','D'], dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'], monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'], monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'], nextText: 'Próximo', prevText: 'Anterior'});
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
					center: [<?php echo $latlong; ?>],
					zoom: 14,
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
				latLng: [<?php echo $latlong; ?>],
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

		var map = $("#map-canvas").gmap3("get");




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



		// chama o geocoder do Google
		var geocoder = new google.maps.Geocoder();

		// Efetua o corte de parte da string retornada do geocoder - endereço
		function contains(str, text) {
  		 	return (str.indexOf(text) >= 0);
		}




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
				

				<?php



// Field Array
$prefix = 'evento_';
$custom_meta_fields = array(
	array(
		'label'=> 'Nome do evento',
		'desc'	=> '',
		'id'	=> $prefix.'titulo',
		'type'	=> 'titulo'
	),
	array(
		'label' => 'Categoria',
		'id'	=> 'category',
		'type'	=> 'categoria'
	),
	array(
		'label'=> 'Descrição',
		'desc'	=> 'A description for the field.',
		'id'	=> $prefix.'descricao',
		'type'	=> 'textarea'
	),
	array(
		'label' => 'Bairro',
		'id'	=> 'bairros',
		'type'	=> 'bairros'
	),
	array(
		'label'=> 'Endereço do local',
		'desc'	=> '',
		'id'	=> $prefix.'endereco',
		'type'	=> 'endereco'
	),
	array(
		'label'=> 'Latlong',
		'desc'	=> '',
		'id'	=> $prefix.'latlong',
		'type'	=> 'latlong'
	),
	array(  
        'label' => 'Nome do local (se existir)',  
        'desc' => '',  
        'id'    =>  $prefix.'lugar',  
        'type' => 'lugar',  
        'post_type' => array('lugar-lazer','lugar-saude')  
    ),  
    array(
	'label'=> 'Nome do local (se não cadastrado)',
	'desc'	=> '',
	'id'	=> 'nome_do_local',
	'type'	=> 'nome_do_local'
	),
    array(  
	    'label' => 'Data e hora',  
	    'desc'  => 'A description for the field.',  
	    'id'    => $prefix.'data',  
	    'type'  => 'date'  
    ),
	array(
	'label'=> 'Informações sobre dias e horários',
	'desc'	=> '',
	'id'	=> 'informacao_dias',
	'type'	=> 'informacao_dias'
	),
    array (  
    'label' => 'Checkbox Group',  
    'desc'  => '',  
    'id'    => 'dias_de_funcionamento',  
    'type'  => 'dias_de_funcionamento',  
    'options' => array (  
        'segunda' => array (  
            'label' => 'Segunda-feira',  
            'value' => 'Segunda'  
        ),  
        'terça' => array (  
            'label' => 'Terça-feira',  
            'value' => 'Terça'  
        ),  
        'quarta' => array (  
            'label' => 'Quarta-feira',  
            'value' => 'Quarta'  
        ),
        'quinta' => array (  
            'label' => 'Quinta-feira',  
            'value' => 'Quinta'  
        ),
        'sexta' => array (  
            'label' => 'Sexta-feira',  
            'value' => 'Sexta'  
        ),
        'sábado' => array (  
            'label' => 'Sábado',  
            'value' => 'Sábado'  
        ),
        'domingo' => array (  
            'label' => 'Domingo',  
            'value' => 'Domingo'  
        )
    	)  
	)  
);


// The Callback

global $custom_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	
	// Begin the field table and loop
	echo '<div>';

	echo '<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Editar evento</h2></div>';

	echo '<form action="" id="primaryPostForm" method="POST">';

	foreach ($custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		// begin a table row with
	
					
					
					switch($field['type']) {
						// titulo  
					    case 'titulo':
					    	$titulo = get_the_title($post->ID);
					    	echo '<div class="bloco"><div class="row">'; 
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
						        	echo '<input type="text" class="form-control" name="post_title" id="'.$field['id'].'" value="'.$titulo.'" size="30" />';
						        	echo '<span class="help-block">Este será o nome principal do evento ou atividade. Seja breve, coloque o nome do evento, e se necessário o tipo de evento. Ex:. Feira de antiguidades "nome estranho que sozinho fica sem sentido".</span>';  
						        echo '</div>';
						    echo '</div></div>';
					    break;
					    // categoria  
						case 'categoria':  
							echo '<div class="bloco"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';

							    echo '<select class="form-control" name="'.$field['id'].'" id="'.$field['id'].'"> 
							            <option value="">Selecione uma categoria</option>'; // Select One  
							    $terms = get_terms( 'category', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
							    $selected = wp_get_object_terms($post->ID, 'category');
							    foreach ($terms as $term) {  
							        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
							            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
							        else  
							            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
							    }  
							    $taxonomy = get_taxonomy($field['id']);  
							    echo '</select>'; 
							    echo '<span class="help-block">Escolha a categoria que mais se encaixa com o evento.</span>'; 
								echo '</div>';
							echo '</div></div>';
						break;  
					    // Descrição  
					    case 'textarea': 
					    	echo '<div class="bloco"><div class="row">';
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">'; 
						        echo '<textarea class="form-control" name="'.$field['id'].'" id="'.$field['id'].'" style="width:100%" rows="5">'.$meta.'</textarea>';  
						   		echo '<span class="help-block">Descreva sobre o que acontece no evento, as atividades realizadas e informações importantes.</span>'; 
						   		echo '</div>';
						   	echo '</div></div>';
					    break; 
					} //end switch

		
	} // end foreach

	echo '<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Localização</h2></div>';

	foreach ($custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		// begin a table row with

					switch($field['type']) {
						// bairro  
						case 'bairros':  
							echo '<div class="bloco"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
							    echo '<select class="form-control" name="'.$field['id'].'" id="'.$field['id'].'"> 
							            <option value="">Selecione um bairro</option>'; // Select One  
							    $terms = get_terms( 'bairros', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
							    $selected = wp_get_object_terms($post->ID, $field['id']);  
							    foreach ($terms as $term) {  
								        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
								            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
								        else  
								            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
							    }  
							    $taxonomy = get_taxonomy($field['id']);  
							    echo '</select>';
							    echo '<span class="help-block">Selecione o bairro onde o evento irá acontecer.</span>';  
								echo '</div>';
							echo '</div></div>';
						break;
						// endereço  
					    case 'endereco':
					    	echo '<div class="bloco"><div class="row">'; 
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
						        	echo '<div class="input-group">';
						        		echo '<input class="form-control" type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" placeholder="Ex:. Rua Rivadávia Correia, 08 - Partenon" style="width:100%; text-transform:capitalize;" />';  
						        		echo '<span class="input-group-btn"><button id="botao" class="btn btn-default" type="button">Buscar endereço</button></span>';
						        	echo '</div>';

						        	echo '<div id="map-canvas"></div>';

						        echo '</div>';
						    echo '</div></div>';
					    break;
					    // latlong  
					    case 'latlong':
						 	echo '<input type="hidden" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />'; 
					    break;
					    // lugar  
						case 'lugar':  
						echo '<div class="bloco"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
									$items = get_posts( array (  
									    'post_type' => $field['post_type'],  
									    'posts_per_page' => -1  
									));  
								    echo '<select class="form-control" name="'.$field['id'].'" id="'.$field['id'].'"> 
								            <option value="">Selecione um lugar</option>'; // Select One  
								        foreach($items as $item) {  
								            echo '<option value="'.$item->ID.'"',$meta == $item->ID ? ' selected="selected"' : '','>'.$item->post_title.'</option>';  
								        } // end foreach  
								    echo '</select>';
								    echo '<span class="help-block">Selecione o local onde o evento irá acontecer (se houver a opção).</span>';  
							echo '</div>';
						echo '</div></div>';
						break; 
						case 'nome_do_local':
					    	$titulo = get_the_title($post->ID);
					    	echo '<div class="bloco"><div class="row">'; 
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
						        	echo '<input type="text" class="form-control" name="nome_do_local" id="'.$field['id'].'" value="'.$meta.'" size="30" />';
						        	echo '<span class="help-block">Se não achar o nome do local na opção anterior, escreva-o aqui.</span>';  
						        echo '</div>';
						    echo '</div></div>';
					    break;
					} //end switch


	} // end foreach


	echo '<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Dias e horários</h2></div>';

	?>


	<div class="bloco">
		<div class="row">
			<div class="col-sm-3">
				<label class="pull-right">O evento acontece:</label>
			</div>

			<div class="col-sm-9">
				<div class="especificas_ou_semana">
					<input type="radio" name="grupo1" value="datas_especificas" checked> Em datas específicas (de X até X dia)<br>
			        <input type="radio" name="grupo1" value="datas_semana"> Toda semana (sempre na segunda, terça etc) <br>
				</div>
			</div>
		</div>
	</div>


	<?php

	foreach ($custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		// begin a table row with

					switch($field['type']) {
						// data
						case 'date':
							$evento_inicio_data = get_post_meta($post->ID, 'evento_inicio_dia', true);
							$evento_inicio_hora = get_post_meta($post->ID, 'evento_inicio_hora', true);
							$evento_termino_data = get_post_meta($post->ID, 'evento_termino_dia', true);
							$evento_termino_hora = get_post_meta($post->ID, 'evento_termino_hora', true);
						
							echo '<div class="bloco especificas"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
							 	echo '<div class="row">';
									echo 	'<div class="col-sm-6">
												<div class="row">
													<div class="col-sm-8 form-group">
														<input type="text" class="datepicker form-control data" name="evento_inicio_dia" id="evento_inicio_dia" value="'.$evento_inicio_data.'"  placeholder="Ex:.12/05/2013"/>
														<span class="help-block">Data de início.</span>
													</div>

													<div class="col-sm-4 form-group">
														<input type="time" name="evento_inicio_hora" id="evento_inicio_hora" placeholder="__:__" value="'.$evento_inicio_hora.'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
														<span class="help-block">Hora de início.</span>
													</div>
												</div>
											</div>'; 
									
									echo '<div class="col-sm-6">
											<div class="row">
												<div class="col-sm-8 form-group">
													<input type="text" class="datepicker form-control" name="evento_termino_dia" id="evento_termino_dia" value="'.$evento_termino_data.'" placeholder="Ex:.15/05/2013"/>
													<span class="help-block">Data de termino.</span> 
												</div>
												<div class="col-sm-4 form-group">
													<input type="time" name="evento_termino_hora" id="evento_termino_hora" placeholder="__:__" value="'.$evento_termino_hora.'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control" required>
													<span class="help-block">Hora de termino.</span>
												</div>
											</div>
										</div>'; 

								echo '</div>';
								echo '</div>';
							echo '</div></div>';
						break;
						// Descrição  
					    case 'informacao_dias': 
					    	echo '<div class="bloco especificas"><div class="row">';
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">'; 
						        echo '<textarea class="form-control" name="'.$field['id'].'" id="'.$field['id'].'" style="width:100%" rows="5">'.$meta.'</textarea>';  
						   		echo '<span class="help-block">Caso necessite informar mais detalhes relacionado aos dias e horários, use este espaço.</span>'; 
						   		echo '</div>';
						   	echo '</div></div>';
					    break;
						// dias da semana  
						case 'dias_de_funcionamento':  
						echo '<div class="bloco semana" style="display:none"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" style="text-align:right" for="'.$field['id'].'">Selecione os dias da semana que o evento acontece</label></div>';
						        echo '<div class="col-sm-9">';
								    

					        	    function in_multiarray($needle, $haystack, $strict = false) {
									    foreach ($haystack as $item) {
									        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_multiarray($needle, $item, $strict))) {
									            return true;
									        }
									    }

									    return false;
									}


								    $terms = get_terms( 'dias_de_funcionamento', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
								    $meta = get_post_meta($post->ID, 'evento_dias_da_semana', true);
				

							        foreach ($terms as $term) {  

							        	echo '<div class="checkbox" style="margin-bottom:25px">';
								        	echo '<div class="row">';
								        		echo '<div class="col-sm-3" style="padding-top: 6px;"><input type="checkbox" value="' . $term->name . '" name="checkbox[]" id="' . $term->id . '" '; if(is_array($meta)){ if(in_multiarray($term->name, $meta)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
								            	echo $term->name.'</div>';  
								            	echo '<div class="col-sm-9">
								            				<div class="row">
								            					<div class="col-sm-6">
										            				<input style="width:75px" type="time" name="evento_inicio_hora_'.$term->name.'" id="evento_inicio_hora_sabado" placeholder="__:__" value="'.$meta[keyBairro($term->name)]['horarios']['inicio'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
										            				<span class="help-block">Hora de início</span>
										            			</div>
										            			<div class="col-sm-6">
										            				<input style="width:75px" type="time" name="evento_termino_hora_'.$term->name.'" id="evento_inicio_hora_sabado" placeholder="__:__" value="'.$meta[keyBairro($term->name)]['horarios']['termino'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
										            				<span class="help-block">Hora de termino</span>
										            			</div>
									            			</div>
								            		  </div>';
								            echo '</div>';
								        echo '</div>';
								    }  
				
							echo '</div>';
							echo '</div></div>';

						break;
					} //end switch


	} // end foreach

	

	?>
				<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

				<input type="hidden" name="submitted" id="submitted" value="true" />
 
        		<button type="submit" class="btn btn-success pull-right">Salvar alterações</button>

	<?php


	echo '</form>';

	echo '</div>'; // end table





if ( isset( $_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
 
 

	$post_information = array(
		'ID' => $current_post,
		'post_title' => esc_attr(strip_tags($_POST['post_title'])),
		'post-type' => 'eventos',
		'post_status' => 'pending'
	);

	$post_id = wp_update_post($post_information);


 

	if($post_id){

		// Save the Data

	    global $custom_meta_fields;
		
		
		// loop through fields and save the data
		foreach ($custom_meta_fields as $field) {
			$old = get_post_meta($post_id, $field['id'], true);
			$new = $_POST[$field['id']];
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}

			// salva a categoria
			if($field['type'] == 'categoria'){
				// save taxonomies  
				$post = get_post($post_id);  
				$category = $_POST['category'];  
				wp_set_object_terms( $post_id, $category, 'category' );
			}  

			// salva o bairro
			if($field['type'] == 'bairros'){
				// save taxonomies  
				$post = get_post($post_id);  
				$category = $_POST['bairros'];  
				wp_set_object_terms( $post_id, $category, 'bairros' );
			}  



		} // end foreach

		// salva a data de início
		$old = get_post_meta($post_id, 'evento_inicio_dia', true);
		$new = $_POST['evento_inicio_dia'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'evento_inicio_dia', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'evento_inicio_dia', $old);
		}

		// salva a hora de início
		$old = get_post_meta($post_id, 'evento_inicio_hora', true);
		$new = $_POST['evento_inicio_hora'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'evento_inicio_hora', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'evento_inicio_hora', $old);
		}

		// salva a data de termino
		$old = get_post_meta($post_id, 'evento_termino_dia', true);
		$new = $_POST['evento_termino_dia'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'evento_termino_dia', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'evento_termino_dia', $old);
		}

		// salva a hora de termino
		$old = get_post_meta($post_id, 'evento_termino_hora', true);
		$new = $_POST['evento_termino_hora'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'evento_termino_hora', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'evento_termino_hora', $old);
		}


		// salva os dias da semana
		
		$diasSelecionados = $_POST['checkbox']; // Pega os dias marcados da semana que o evento acontece


		// Pega cada dia marcado e forma os elementos da array adicionando os horários

		foreach ($diasSelecionados as $diaSelecionado) {
			switch ($diaSelecionado) {
				case 'Domingo':
					$arrayDias[0] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;
				case 'Segunda':
					$arrayDias[1] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;
				
				case 'Terça':
					$arrayDias[2] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;

				case 'Quarta':
					$arrayDias[3] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;

				case 'Quinta':
					$arrayDias[4] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;

				case 'Sexta':
					$arrayDias[5] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;

				case 'Sábado':
					$arrayDias[6] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['evento_inicio_hora_'.$diaSelecionado], 'termino' => $_POST['evento_termino_hora_'.$diaSelecionado]) );
					break;

				default:
					# code...
					break;
			}
			
		}

		asort($arrayDias); // Coloca os dias em ordem pela 'key'
		print_r($arrayDias);
		$old = get_post_meta($post_id, 'evento_dias_da_semana', true); // pega a array salva (se existir) no banco de dados

		// Faz a comparação e atualiza ou deleta

		if ($arrayDias && $arrayDias != $old) {
			update_post_meta($post_id, 'evento_dias_da_semana', $arrayDias);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'evento_dias_da_semana', $old);
		}
	
	}


}



		}
	}


endwhile; endif;
wp_reset_query();

global $current_post;




?>
					</div>

				</div>

			</div>
		

		</div>
	




		<?php get_footer(); ?>
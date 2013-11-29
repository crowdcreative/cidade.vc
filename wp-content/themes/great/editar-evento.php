<?php
/**
 * Template Name: Editar evento
 */
?>

<?php get_header(); ?>
<?php $options = get_option('great'); ?>

<!-- Define vars para o mapa -->
	
	<?php
	$latlong = get_field('latlong');
	$latlong = str_replace('(', '', $latlong);
	$latlong = str_replace(')', '', $latlong);
	?>

			<script type="text/javascript" src="/js/jquery.ui.datepicker.min.js"></script>
	<script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/map-dashboard-eventos.js"></script>

		<script type="text/javascript">

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

		// Adiciona o map-canvas abaixo do input buscador
		$("<div id='map-canvas'></div>").insertAfter("#acf-endereço");
		$("#acf-endereço").css({"width":"100%"});
 
		// Adiciona o botao abaixo do mapa
		$("<div id='botao'><span>Buscar endereço</span></div>").insertAfter("#acf-endereço");
		$("#botao").css({"width":"100%"});

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

		// Criação do mapa
		$('#map-canvas').gmap3({
			map: {
				options: {
					center: [<?php echo $latlong; ?>],
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
				latLng: [<?php echo $latlong; ?>],
				options: {
					draggable: true
				}
			}
		});


		// Scroll to top
	    $('a[href=#top]').click(function(){
	        $('html, body').animate({scrollTop:0}, 'slow');
	        return false;
	    });

	    var ajaxUrl = "http://127.0.0.1/projects/cidade.vc/wp-admin/admin-ajax.php";

		var map = $("#map-canvas").gmap3("get");


		// Adiciona o circulo no mapa
		var circle = new google.maps.Circle({
			map: map,
			radius: 600, // metres
			strokeWeight: 0,
			fillOpacity: 0.1,
			clickable: false,
		});

		var marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php echo $latlong; ?>),
		});

		circle.bindTo('center', marker, 'position');


        google.maps.event.addListenerOnce(map, 'idle', function(){
         	var bounds = circle.getBounds();
         	var bounds = bounds.toString();
         	pegaBound(bounds);
		});

		// Url do ajax do wordpress
   		var ajaxUrl = "http://127.0.0.1/projects/cidade.vc/wp-admin/admin-ajax.php";
		

		// jQuey ajax para chamar latlong
		function pegaBound(bounds){


			$.ajax({
				url: ajaxUrl,
				type: 'POST',

				data: {
					'action': 'getbounds',
					'minmaxlatlong': bounds
				},
				success: function(dados) {
					$('#onibus_que_passao_perto ul').html(dados);
						// Habilita a tooltip do bootstrap
						$('[rel=tooltip]').tooltip({placement:'top'});
						$('[rel=tooltip-border]').tooltip({placement:'top'});

						// Adicionar borda pontilhada no holover do link com tooltip
						$('[rel=tooltip-border]').hover(function(){
							$(this).css({'border-bottom':'1px dotted #cccccc','padding-bottom':'2px'});
						},
						function(){
							$(this).css({'border-bottom':'0','padding-bottom':'0'});
						});
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}

			});

		}

		


  });

</script>



<a href="#top"><div id="buttonScroll-top" class="glyphicon glyphicon-circle-arrow-up" style="display:none"></div></a>


<div id="page" class="single container">
	
		<div class="row">


			<?php get_sidebar('left'); ?>


			<div class="col-md-9">

				<div class="panel panel-default form-group">

						<div class="panel-body">
				

				<?php




$query = new WP_Query(array('post_type' => 'eventos', 'posts_per_page' =>'-1', 'post_status' => array('publish', 'pending', 'draft', 'private', 'trash') ) );

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
	
	if(isset($_GET['post'])) {
		
		if($_GET['post'] == $post->ID)
		{
			$current_post = $post->ID;




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
	    'label' => 'Data e hora',  
	    'desc'  => 'A description for the field.',  
	    'id'    => $prefix.'data',  
	    'type'  => 'date'  
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
							    echo '</select><br /><span class="description"><a href="'.get_bloginfo('home').'/wp-admin/edit-tags.php?taxonomy='.$field['id'].'">Manage '.$taxonomy->label.'</a></span>';  
								echo '</div>';
							echo '</div></div>';
						break;
						// endereço  
					    case 'endereco':
					    	echo '<div class="bloco"><div class="row">'; 
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
						        	echo '<input class="form-control" type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" placeholder="Ex:. Rua Rivadávia Correia, 08 - Partenon" style="width:100%" /> 
						            <br /><span class="description">'.$field['desc'].'</span>';  
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
								    echo '</select><br /><span class="description">'.$field['desc'].'</span>'; 
							echo '</div>';
						echo '</div></div>';
						break; 
					} //end switch


	} // end foreach


	echo '<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Dias e horários</h2></div>';


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
						
							echo '<div class="bloco"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';
							 	echo '<div class="row">';
									echo '<div class="col-sm-6"><div class="row"><div class="col-sm-8 form-group"><input type="text" class="datepicker form-control" name="evento_inicio_dia" id="evento_inicio_dia" value="'.$evento_inicio_data.'"  placeholder="Ex:.12/05/2013"/></div><div class="col-sm-4 form-group"><input type="time" name="evento_inicio_hora" id="evento_inicio_hora" placeholder="___:___" value="'.$evento_inicio_hora.'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control" required></div></div></div>'; 
									echo '<div class="col-sm-6"><div class="row"><div class="col-sm-8 form-group"><input type="text" class="datepicker form-control" name="evento_termino_dia" id="evento_termino_dia" value="'.$evento_termino_data.'" placeholder="Ex:.15/05/2013"/></div><div class="col-sm-4 form-group"><input type="time" name="evento_termino_hora" id="evento_termino_hora" placeholder="___:___" value="'.$evento_termino_hora.'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control" required></div></div></div>'; 
								echo '</div>';
								echo '</div>';
							echo '</div></div>';
						break;
						// dias da semana  
						case 'dias_de_funcionamento':  
						echo '<div class="bloco"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" style="text-align:right" for="'.$field['id'].'">O evento acontece toda semana?</label></div>';
						        echo '<div class="col-sm-9">';
								    
								    $meta = get_post_meta($post->ID, 'evento_dias_da_semana', true);
								    $terms = get_terms( 'dias_de_funcionamento', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
							        foreach ($terms as $term) {  
							        	echo '<div class="checkbox">';
								        	echo '<label>';
								        		echo '<input type="checkbox" value="' . $term->name . '" name="checkbox[]" id="' . $term->id . '" '; if(is_array($meta)){ if(in_array($term->name, $meta)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
								            	echo $term->name;  
								            echo '</label>';
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
 
        		<button type="submit" class="btn btn-primary pull-right">Criar evento</button>

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


 

if($post_id)
	{

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
	$old = get_post_meta($post_id, 'evento_dias_da_semana', true);
	$new = $_POST['checkbox'];
	if ($new && $new != $old) {
		update_post_meta($post_id, 'evento_dias_da_semana', $new);
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
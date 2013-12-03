<?php

require_once( dirname( __FILE__ ) . '/theme-options.php' );

if ( ! isset( $content_width ) ) $content_width = 960;

/*-----------------------------------------------------------------------------------*/
/*	Load Translation Text Domain
/*-----------------------------------------------------------------------------------*/

load_theme_textdomain( 'mythemeshop', get_template_directory().'/lang' );

if ( function_exists('add_theme_support') ) add_theme_support('automatic-feed-links');

/*-----------------------------------------------------------------------------------*/
/*	Post Thumbnail Support
/*-----------------------------------------------------------------------------------*/
	if ( function_exists( 'add_theme_support' ) ) { 
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 300, 225, true );
	add_image_size( 'featured', 300, 225, true ); //featured
	add_image_size( 'image-lugar', 500, 1500 ); //Imagem para lugares
	add_image_size( 'related', 50, 50, true ); //related
	}

/*-----------------------------------------------------------------------------------*/
/*	Enable Widgetized sidebar
/*-----------------------------------------------------------------------------------*/
	if ( function_exists('register_sidebar') )
	// Sidebar Widget
	register_sidebar(array('name'=>'Sidebar',
		'before_widget' => '<li class="widget widget-sidebar">',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));


/*-----------------------------------------------------------------------------------*/
/*	Pega o preço para usar o serviço
/*-----------------------------------------------------------------------------------*/

function preco($valorID){
	$valorID = (int)$valorID;
	$valor = get_term($valorID, 'preço'); 
	echo $valor->name;
}



/*-----------------------------------------------------------------------------------*/
/*	Metabox de um mapa
/*-----------------------------------------------------------------------------------*/


// Adicionar scripts do mapa jquery  ESTE SCRIPT ATRAPALHAM O GRAB DO ADVANCE CUSTOM FIELDS =O
function pw_load_scripts($hook) {
	
	global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'lugar-saude' === $post->post_type || 'lugar-lazer' === $post->post_type ) {   
			wp_enqueue_script('jquery-1.10.2.min.js', 'http://code.jquery.com/jquery-1.10.2.min.js');
			wp_enqueue_script('','https://maps.googleapis.com/maps/api/js?key=AIzaSyBiBbZGjRGFtFf4TpVs3CAip3iPBbvgrpU&sensor=true');
			wp_enqueue_script( 'gmap3.js', 'http://127.0.0.1/projects/cidade.vc/js/gmap3.js');
			wp_enqueue_script( 'map.js', 'http://127.0.0.1/projects/cidade.vc/js/map-dashboard.js');
		}
	}
}

add_action('admin_enqueue_scripts', 'pw_load_scripts');



function pw_load_styles() {
	wp_enqueue_style('dashboard.css', 'http://127.0.0.1/projects/cidade.vc/css/dashboard.css');
	wp_enqueue_style('jquery-ui.css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css');
}

add_action('admin_enqueue_scripts', 'pw_load_styles');




if(is_admin()) {
	wp_enqueue_style('jquery-ui-custom', get_template_directory_uri().'/css/jquery-ui-custom.css');
	wp_enqueue_style('bootstrap', 'http://127.0.0.1/projects/cidade.vc/bootstrap/css/bootstrap.css');
}


function eventos_load_scripts($hook) {
	
	global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'eventos' === $post->post_type) {   
			wp_enqueue_script('jquery-1.10.2.min.js', 'http://code.jquery.com/jquery-1.10.2.min.js');
			wp_enqueue_script('jquery.ui.datepicker.min',  get_template_directory_uri().'/js/jquery.ui.datepicker.min.js');
			wp_enqueue_script('','https://maps.googleapis.com/maps/api/js?key=AIzaSyBiBbZGjRGFtFf4TpVs3CAip3iPBbvgrpU&sensor=true');
			wp_enqueue_script( 'gmap3.js', 'http://127.0.0.1/projects/cidade.vc/js/gmap3.js');
			wp_enqueue_script( 'map.js', 'http://127.0.0.1/projects/cidade.vc/js/map-dashboard-eventos.js');
		}
	}
}

add_action('admin_enqueue_scripts', 'eventos_load_scripts');
     



// Cria a metabox 
function add_custom_meta_box() {  
    add_meta_box(  
        'custom_meta_box', // $id  
        'Custom Meta Box', // $title   
        'show_custom_meta_box', // $callback  
        'eventos', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_custom_meta_box'); 



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
function show_custom_meta_box() {
global $custom_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	
	// Begin the field table and loop
	echo '<div>';

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
						        	echo '<input type="text" name="post_title" id="'.$field['id'].'" value="'.$titulo.'" size="30" /> 
						            <br /><span class="description">'.$field['desc'].'</span>';  
						        echo '</div>';
						    echo '</div></div>';
					    break;
					    // categoria  
						case 'categoria':  
							echo '<div class="bloco"><div class="row">';
								echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">';

							    echo '<select name="'.$field['id'].'" id="'.$field['id'].'"> 
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
							    echo '</select><br /><span class="description"><a href="'.get_bloginfo('home').'/wp-admin/edit-tags.php?taxonomy='.$field['id'].'">Manage '.$taxonomy->label.'</a></span>';  
								echo '</div>';
							echo '</div></div>';
						break;  
					    // Descrição  
					    case 'textarea': 
					    	echo '<div class="bloco"><div class="row">';
						    	echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
						        echo '<div class="col-sm-9">'; 
						        echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" style="width:100%" rows="5">'.$meta.'</textarea> 
						            <br /><span class="description">'.$field['desc'].'</span>';  
						   		echo '</div>';
						   	echo '</div></div>';
					    break; 
					} //end switch

		
	} // end foreach

	echo '<div class="bloco"><h2>Localização</h2></div>';

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
							    echo '<select name="'.$field['id'].'" id="'.$field['id'].'"> 
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
						        	echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" placeholder="Ex:. Rua Rivadávia Correia, 08 - Partenon" style="width:100%" /> 
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
								    echo '<select name="'.$field['id'].'" id="'.$field['id'].'"> 
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


	echo '<div class="bloco"><h2>Dias e horários</h2></div>';


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
									echo '<div class="col-sm-6"><input type="text" class="datepicker" name="evento_inicio_dia" id="evento_inicio_dia" value="'.$evento_inicio_data.'" size="15" placeholder="Ex:.12/05/2013"/> <input type="time" name="evento_inicio_hora" id="evento_inicio_hora" placeholder="___:___" value="'.$evento_inicio_hora.'" size="5" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="inputs duration t1 time hrs" required>
											<br /><span class="description">Início</span></div>';
									echo '<div class="col-sm-6"><input type="text" class="datepicker" name="evento_termino_dia" id="evento_termino_dia" value="'.$evento_termino_data.'" size="15" placeholder="Ex:.15/05/2013"/> <input type="time" name="evento_termino_hora" id="evento_termino_hora" placeholder="___:___" value="'.$evento_termino_hora.'" size="5" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="inputs duration t1 time hrs" required>
											<br /><span class="description">Termino</span></div>';
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
							        	echo '<input type="checkbox" value="' . $term->name . '" name="checkbox[]" id="' . $term->id . '" '; if(is_array($meta)){ if(in_array($term->name, $meta)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
							            echo '<span style="margin-right:12px">'.$term->name.'</span>';  
								    }  
				
							echo '</div>';
							echo '</div></div>';

						break;
					} //end switch


	} // end foreach

	echo '</div>'; // end table
}



// Save the Data
function save_custom_meta($post_id) {
    global $custom_meta_fields;
	
	// verify nonce
	if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__))) 
		return $post_id;
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	// check permissions
	if ('page' == $_POST['eventos']) {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
	}
	
	// loop through fields and save the data
	foreach ($custom_meta_fields as $field) {


		// salva a categoria
		if($field['type'] == 'categoria'){
			// save taxonomies  
			$post = get_post($post_id);  
			$category = $_POST['category'];  
			wp_set_object_terms( $post_id, $category, 'category' );
		}
		elseif($field['type'] == 'bairros'){  // salva o bairro
			// save taxonomies  
			$post = get_post($post_id);  
			$category = $_POST['bairros'];  
			wp_set_object_terms( $post_id, $category, 'bairros' );
		}
		else{

			$old = get_post_meta($post_id, $field['id'], true);
			$new = $_POST[$field['id']];
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
			
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
add_action('save_post', 'save_custom_meta');  
  



function add_custom_scripts() {

    global $custom_meta_fields, $post_type;

    if (($_GET['post_type'] == 'eventos') || ($post_type == 'eventos')) :

        $output = '<script type="text/javascript">'; 
        $output.= '$(function() {';  
        $output.= '';
              
	    foreach ($custom_meta_fields as $field) { 
	        if($field['type'] == 'date')  
	            $output .= "$('.datepicker').datepicker({dateFormat: 'dd/mm/yy', dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'], dayNamesMin: ['D','S','T','Q','Q','S','S','D'], dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'], monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'], monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'], nextText: 'Próximo', prevText: 'Anterior'});";
	             
  		}

		$output .= '}); 
		</script>';  
          
    echo $output;  

    endif;

}

add_action('admin_head','add_custom_scripts'); 





// Função para pegar Lat e Long para o mapa

add_action('wp_ajax_getlatlong', 'getlatlong');
add_action('wp_ajax_nopriv_getlatlong', 'getlatlong');

function getlatlong(){
	$waka = $_REQUEST['test'];
	$waka = json_encode($waka);
	echo $waka;
	die();
}



// Função para pegar os bounds do mapa - min e max lat long

add_action('wp_ajax_getbounds', 'getbounds');
add_action('wp_ajax_nopriv_getbounds', 'getbounds');

function getbounds(){
	$bounds = $_REQUEST['minmaxlatlong'];
	$bounds = urlencode($bounds);
	$data = json_decode(file_get_contents('http://www.poatransporte.com.br/php/facades/process.php?a=tp&p='.$bounds));

	// #####  Exibi as linhas próximas ao marcador via 'bounds' do 'circle'

	// Pega o JSON do poatransporte

	$arrayPronta = array(); //Array que criamos para pegar as linhas sem duplicação

	for ($i=0; $i < 20; $i++) { // pegamos no máximo 20 linhas
		
		$linhasArray = $data[$i]->linhas;
		$numerodeLinhas = sizeof($linhasArray);

		for ($iL=0; $iL < $numerodeLinhas; $iL++) { 
			$idLinha = $linhasArray[$iL]->idLinha;
			$codigoLinha = $linhasArray[$iL]->codigoLinha;

			$linha = $linhasArray[$iL]->nomeLinha;

			$arrayPronta[$iL] = array('linha' => $linha, 'codigo' => $codigoLinha, 'id' => $idLinha);
			

		}
	}




	$arrayProntaLen = sizeof($arrayPronta);

	for ($i=0; $i < $arrayProntaLen; $i++) { 
		
		$linha = $arrayPronta[$i]['linha'];
		$codigo = $arrayPronta[$i]['codigo'];

		$linha = trim($linha); // Deixa os caracteres em minúsculo, depois o primeiro em maísculo e depois remove espaços em branco
		$valueLen = strlen($linha); // pega o tamanho da string
		
		if($valueLen > 25){
			$linhaCut = substr($linha, 0, 25); // corta a string
			echo "<li class='col-sm-6'><span class='badge'>".$codigo."</span><span rel='tooltip-border' style='cursor:help' title='".$linha."'> ".$linhaCut." ...</span></li>";
		}else{
			echo "<li class='col-sm-6'><span><span class='badge'>".$codigo."</span> ".$linha."</span></li>";
		}
	
	}


	die();
}



// Deixa a url do 'site' limpa
function NewUrl($x) {
   $url = $x;
   if ( substr($url, 0, 7) == 'http://') { $url = substr($url, 7); }
   if ( substr($url, 0, 8) == 'https://') { $url = substr($url, 8); }
   if ( substr($url, 0, 4) == 'www.') { $url = substr($url, 4); }
   if ( strpos($url, '/') !== false) {
      $ex = explode('/', $url);
      $url = $ex['0'];
   }

      return $url;
}





/*-----------------------------------------------------------------------------------*/
/*	Load Widgets & Shortcodes
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-ad125.php");

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-ad300.php");

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-tabs.php");

// Add the Latest Tweets Custom Widget
include("functions/widget-tweets.php");

// Add Facebook Like box Widget
include("functions/widget-fblikebox.php");

// Add Welcome message
include("functions/welcome-message.php");

// Theme Functions
include("functions/theme-actions.php");

/*-----------------------------------------------------------------------------------*/
/*	Filters customize wp_title
/*-----------------------------------------------------------------------------------*/
// Filter the page title wp_title() in header.php
	if ( ! function_exists('mythemeshop_page_title' ) ) {
		function mythemeshop_page_title( $title ) { 
			$the_page_title = $title;
			if( ! $the_page_title ){
				$the_page_title = get_bloginfo("name");
			}else{
				$the_page_title = $the_page_title;
			}
			return $the_page_title;
		} 
		add_filter('wp_title', 'mythemeshop_page_title');
	}
/*-----------------------------------------------------------------------------------*/
/*	Filters that allow shortcodes in Text Widgets
/*-----------------------------------------------------------------------------------*/

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');
add_filter('the_content_rss', 'do_shortcode');

/*-----------------------------------------------------------------------------------*/
/*	Register Footer widgets
/*-----------------------------------------------------------------------------------*/
if (function_exists('register_sidebar')) {
	$sidebars = array(1, 2, 3);
	foreach($sidebars as $number) {
	register_sidebar(array(
		'name' => 'Footer ' . $number,
		'id' => 'footer-' . $number,
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	}
}
function widgetized_footer() {
?>
		<div class="f-widget f-widget-1">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 1') ) : ?>
			<?php endif; ?>
		</div>
		<div class="f-widget f-widget-2">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 2') ) : ?>
			<?php endif; ?>
		</div>
		<div class="f-widget last">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 3') ) : ?>
			<?php endif; ?>
		</div>
<?php
}

/*  Função para ver se mais de uma imagem em um post  */
function tem_images($postID) {
	global $post;
	
	$attachments = get_children(array('post_parent'=>$postID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));
	$attachmentsTotal = count($attachments);

	if($attachmentsTotal >= 2){
		return "tem";
	}else{
		return "naotem";
	}
}



// Função para deixar datas iguais a do Facebook

function time_ago( $type = 'post' ) {
	$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
	return human_time_diff($d('U'), current_time('timestamp')) . " " . "atrás";
}



// Função que pega a 'key' a partir do nome dos bairros (usado para pegar horarios nas páginas de evento)

function keyBairro($bairro){
	switch ($bairro) {
		case 'Domingo':
			return 0;
		break;
	
		case 'Segunda':
			return 1;
		break;

		case 'Terça':
			return 2;
		break;

		case 'Quarta':
			return '3';
		break;

		case 'Quinta':
			return '4';
		break;

		case 'Sexta':
			return 5;
		break;

		case 'Sábado':
			return 6;
		break;

	}
}


/*-----------------------------------------------------------------------------------*/
/*	Custom Comments template
/*-----------------------------------------------------------------------------------*/
function mytheme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" style="position:relative;">
			<div class="comment-author vcard">
			<?php echo get_avatar( $comment->comment_author_email, 75 ); ?>
			<?php printf(__('<span class="fn">%s</span>', 'mythemeshop'), get_comment_author_link()) ?> 
				<span class="comment-time"><time><?php comment_date(); ?></time> | </span>
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			</div>
			<?php if ($comment->comment_approved == '0') : ?>
				<em><?php _e('Your comment is awaiting moderation.', 'mythemeshop') ?></em><br />
			<?php endif; ?>
			<div class="comment-meta "
				<?php edit_comment_link(__('(Edit)', 'mythemeshop'),'  ','') ?>
			</div>
			<div class="commentmetadata">
				<?php comment_text() ?>
			</div>
		</div>
	</li>
<?php
        }
/*-----------------------------------------------------------------------------------*/
/*	Custom Menu Support
/*-----------------------------------------------------------------------------------*/
	add_theme_support( 'menus' );
	if ( function_exists( 'register_nav_menus' ) ) {
	  	register_nav_menus(
	  		array(
	  		  'primary-menu' => 'Primary Menu'
	  		)
	  	);
	}

/*-----------------------------------------------------------------------------------*/
/*	excerpt
/*-----------------------------------------------------------------------------------*/
function excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt);
  } else {
    $excerpt = implode(" ",$excerpt);
  }
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}

/*-----------------------------------------------------------------------------------*/
/* nofollow to next/previous links
/*-----------------------------------------------------------------------------------*/
function pagination_add_nofollow($content) {
    return 'rel="nofollow"';
}
add_filter('next_posts_link_attributes', 'pagination_add_nofollow' );
add_filter('previous_posts_link_attributes', 'pagination_add_nofollow' );

/*-----------------------------------------------------------------------------------*/
/* Nofollow to category links
/*-----------------------------------------------------------------------------------*/
add_filter( 'the_category', 'add_nofollow_cat' ); 
function add_nofollow_cat( $text ) {
$text = str_replace('rel="category tag"', 'rel="nofollow"', $text); return $text;
}

/*-----------------------------------------------------------------------------------*/	
/* nofollow post author link
/*-----------------------------------------------------------------------------------*/
add_filter('the_author_posts_link', 'mts_nofollow_the_author_posts_link');
function mts_nofollow_the_author_posts_link ($link) {
return str_replace('<a href=', '<a rel="nofollow" href=',$link); 
}

/*-----------------------------------------------------------------------------------*/
/* removes detailed login error information for security
/*-----------------------------------------------------------------------------------*/
	add_filter('login_errors',create_function('$a', "return null;"));
	
/*-----------------------------------------------------------------------------------*/
/* removes the WordPress version from your header for security
/*-----------------------------------------------------------------------------------*/
	function wb_remove_version() {
		return '<!--Theme by MyThemeShop.com-->';
	}
	add_filter('the_generator', 'wb_remove_version');
	
/*-----------------------------------------------------------------------------------*/
/* Removes Trackbacks from the comment count
/*-----------------------------------------------------------------------------------*/
	add_filter('get_comments_number', 'comment_count', 0);
	function comment_count( $count ) {
		if ( ! is_admin() ) {
			global $id;
			$comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
			return count($comments_by_type['comment']);
		} else {
			return $count;
		}
	}
	
/*-----------------------------------------------------------------------------------*/
/* category id in body and post class
/*-----------------------------------------------------------------------------------*/
	function category_id_class($classes) {
		global $post;
		foreach((get_the_category($post->ID)) as $category)
			$classes [] = 'cat-' . $category->cat_ID . '-id';
			return $classes;
	}
	add_filter('post_class', 'category_id_class');
	add_filter('body_class', 'category_id_class');

/*-----------------------------------------------------------------------------------*/
/* adds a class to the post if there is a thumbnail
/*-----------------------------------------------------------------------------------*/
	function has_thumb_class($classes) {
		global $post;
		if( has_post_thumbnail($post->ID) ) { $classes[] = 'has_thumb'; }
			return $classes;
	}
	add_filter('post_class', 'has_thumb_class');

/*-----------------------------------------------------------------------------------*/	
/* Breadcrumb
/*-----------------------------------------------------------------------------------*/
function the_breadcrumb() {
	echo '<a href="';
	echo home_url();
	echo '" rel="nofollow">Home';
	echo "</a>";
		if (is_category() || is_single()) {
			echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
			the_category(' &bull; ');
				if (is_single()) {
					echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
					the_title();
				}
        } elseif (is_page()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
            echo the_title();
		} elseif (is_search()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
			echo '"<em>';
			echo the_search_query();
			echo '</em>"';
        }
    }

/*-----------------------------------------------------------------------------------*/	
/* Pagination
/*-----------------------------------------------------------------------------------*/
function pagination($pages = '', $range = 3)
{ $showitems = ($range * 3)+1;
 global $paged; if(empty($paged)) $paged = 1;
 if($pages == '') {
 global $wp_query; $pages = $wp_query->max_num_pages; if(!$pages)
 { $pages = 1; } }
 if(1 != $pages)
 { echo "<div class='pagination'><ul>";
 if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link(1)."'>&laquo; First</a></li>";
 if($paged > 1 && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link($paged - 1)."' class='inactive'>&lsaquo; Previous</a></li>";
 for ($i=1; $i <= $pages; $i++)
 { if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
 { echo ($paged == $i)? "<li class='current'><span class='currenttext'>".$i."</span></li>":"<li><a rel='nofollow' href='".get_pagenum_link($i)."' class='inactive'>".$i."</a></li>";
 } } if ($paged < $pages && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link($paged + 1)."' class='inactive'>Next &rsaquo;</a></li>";
 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a rel='nofollow' class='inactive' href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
 echo "</ul></div>"; }}

?>
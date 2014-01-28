<?php
/**
 * Template Name: Criar evento
 */
?>

	<?php 

	// pega o cabeçalho do template
	get_header(); 


	// faz a requisição das funções de processamento necessários
	require 'controllers/criar-editar-lugar-functions.php'; 



	// pega a url do template
	$localiza_url = get_template_directory_uri();


	$latlong = get_post_meta($post->ID, 'latlong', true);

	if($latlong != ''){
		$latlong = str_replace('(', '', $latlong);
		$latlong = str_replace(')', '', $latlong);
	}else{
		$latlong = '-30.036363, -51.214786';
	}



	// faz a requisição do conjunto de javascripts (mapa, inputs, events) 
	require '/js/criar-lugar-evento-js.php';

	
	?>





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


			<div class="col-md-9 criar-editar">
				
				<div class="panel panel-default form-group">

						<div class="panel-body">




		<?php require'models/criar-evento/criar-evento-inputs.php' ?> 




				
<?php

if ( isset( $_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
 
 
    $post_information = array(
        'post_title' => wp_strip_all_tags( $_POST['post_title'] ),
        'post_type' => 'eventos',
        'post_status' => 'pending'
    );
 
   	$post_id = wp_insert_post($post_information);


 

	require'models/criar-evento/criar-evento-saves.php'; 

	
}




?>


					</div>

				</div>

			</div>
		
		</div>




		<?php get_footer(); ?>
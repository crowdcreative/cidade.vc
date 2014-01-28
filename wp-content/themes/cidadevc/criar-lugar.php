<?php/**
	 * Template Name: Criar lugar
	 */


	// pega o cabeçalho do template
	get_header(); 


	// faz a requisição das funções de processamento necessários
	require 'controllers/classes/criar-editar-inputs-classe.php';


	// se a página for para editar um lugar
	if(isset($_GET['l'])){

		$query = new WP_Query(array('post_type' => 'lugar-lazer', 'posts_per_page' =>'-1', 'post_status' => array('publish', 'pending', 'draft', 'private', 'trash') ) );

		if ($query->have_posts()){ 

		 	while ($query->have_posts()){ 

		 		$query->the_post();
		
				if($_GET['l'] == $post->ID){
					
					$current_post = $post->ID;

					

				}
			}
		}


	}


	$segmento = $_GET['seg'];


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

						<?php echo $segmento ?>

						<p>Esta é a agenda de eventos e atividades de Porto Alegre, criada de forma colaborativa por todos os que querem contribuir.</p><br/>

						<p>A moderação da publicação se dará por meio do administrador do projeto Cidade.vc.</p><br/>

						<p>Poderão ser cadastradas agendas relativas a: lazer, saúde, esporte, educação, cultura e alimentação.</p> 

					</div>

				</div>

			</div>


			<div class="col-md-9 criar-editar">
				
				
				<?php 

				

				require 'models/criar-lugar/criar-lugar-'.$segmento.'-inputs.php'; ?>
	
	




				<?php


				if ( isset( $_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
				 


					// Publica automaticamente o evento se o usuário for admin
					if(current_user_can('manage_options')){
						$status = 'publish';
					}else{
						$status = 'pending';
					}

				 
					// edita o lugar
					if(isset($_GET['l'])){

						$post_information = array(
							'ID' => $current_post,
							'post_title' => esc_attr(strip_tags($_POST['post_title'])),
							'post-type' => 'lugar-lazer',
							'post_status' => $status
						);

						$post_id = wp_update_post($post_information);

					}

					// cria o lugar
					else{	

					    $post_information = array(
					        'post_title' => wp_strip_all_tags( $_POST['post_title'] ),
					        'post_type' => 'lugar-lazer',
					        'post_status' => $status
					    );
					 
					   	$post_id = wp_insert_post($post_information);

				   	}


				 	require 'models/criar-lugar/criar-lugar-'.$segmento.'-saves.php';

					
				}


				wp_reset_query();


				?>


					

			</div>
		
		</div>




		<?php get_footer(); ?>
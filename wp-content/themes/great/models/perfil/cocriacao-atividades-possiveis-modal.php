<?php
	
	$user_id = get_current_user_id();
	
	$first_name = get_user_meta($user_id, 'first_name', true);
	
	$personaArray = get_user_meta($user_id, 'persona', true);
	if(is_array($personaArray)){
		$persona = $personaArray;
	}

	$bairros = get_terms( 'bairros', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));

?>

<!-- MODAL DO PERFIL DO USUÁRIO -->
<div class="modal fade" id="atividades-possiveis-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">



		    <div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Atividades possíveis de praticar neste lugar</h4>
		    </div>



		<form action="" id="perfil" method="POST">
	      	


	      	<div class="modal-body">




		      		<!-- ATIVIDADES JÁ CADASTRADAS -->

		      		<?php 

		      		global $post; 
					$post_id = get_the_ID();

					// Pega a array com as atividades do banco de dados
					$atividades_possiveis = get_post_meta($post_id, 'atividades_possiveis', true);

		      		?>

		      		<div class="row row-bloco">


		      			<div class="col-sm-4">
		      				
		    	
		    				<?php
						    foreach ($atividades_possiveis as $atividades) {
								
								$term = get_term_by('id', $atividades['id'], 'atividades-possiveis');
								
								$termEcho = $term->name;

								echo '<li style="list-style:none; margin-bottom:3px" term-id="'.$term->term_id.'"><span class="label label-default">' . $termEcho . '</span></li>';
								
							}
						    ?>
		    				

		      			</div>


		      			<div class="col-sm-8">
		      				<span><b>Adicionar atividade:</b></span>

		      				<?php

	      				 	$terms = get_terms( 'atividades-possiveis', array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'parent'  => 0 )); 

						    echo '<div class="checkbox" style="margin-bottom:25px">';
					        	echo '<div class="row">';

				        			foreach ($terms as $term) {  
						        		if(is_array($atividades_possiveis)){ if(!in_multiarray($term->term_id, $atividades_possiveis)){
							        		echo '<div class="col-sm-6" style="margin-bottom: 12px; padding-left:0">
							        				<input type="checkbox" value="'.$term->term_id.'" name="atividades_possiveis[]" id="' . $term->term_id . '">';  
							            			echo $term->name.
							            		 '</div>';   
							            }}
					   				} 
						   	echo '</div>';
						        echo '</div>'; 

						    ?>


						    <span><b>Criar atividade</b></span>

						    <input type="text" class="form-control" name="nova_atividade" id="nova_atividade" value="" size="30" placeholder="Ex:. Nome da atividade ou ação"/>

		      			</div>

		      		</div>



		      		<?php  wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

		      	
	      	</div>



      		<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        		<button type="submit" class="btn btn-primary">Salvar</button>
      		</div>



      	</form>



    	</div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php

	if (isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce') ) {

		

		# salva as ATIVIDADES MARCADAS

		// pega as atividades já cadastradas no BD
		$atividades_cadastradas = get_post_meta($post_id, 'atividades_possiveis', true); 

		// pega as atividades selecionadas na cocriação
		$atividades_selecionadas = $_POST['atividades_possiveis']; 

		// Coloca as atividades em ordem alfabética
		if(isset($atividades_selecionadas)){
			sort($atividades_selecionadas); 
		}

		foreach ($atividades_selecionadas as $atividade_id) {
			array_push($atividades_cadastradas, array('id' => $atividade_id, 'contador' => '', 'usuarios_id' => array()));
		}


		if (is_array($atividades_cadastradas)) {
			update_post_meta($post_id, 'atividades_possiveis', $atividades_cadastradas);
		} 







		# salva a NOVA ATIVIDADE CRIADA

		$nova_atividade = $_POST['nova_atividade'];



	}




?>
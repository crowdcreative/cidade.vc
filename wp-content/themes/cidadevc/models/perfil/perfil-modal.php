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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">



		    <div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Meu perfil</h4>
		    </div>



		<form action="" id="perfil" method="POST">
	      	


	      	<div class="modal-body">
		      	

	      			<!-- NOME -->

		      		<div class="row row-bloco">

		      			<div class="col-sm-3">
		      				<label for="first_name">Seu nome</label>
		      			</div>

		      			<div class="col-sm-9">
		      				<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $first_name ?>" size="30" />
		      			</div>

		      		</div>
		      	



		      		<!-- BAIRRO -->

		      		<div class="row row-bloco">

		      			<div class="col-sm-3">
		      				<label for="first_name">Seu bairro</label>
		      			</div>

		      			<div class="col-sm-9">
		      				
		      				<select class="form-control" name="persona_bairro" id="persona_bairro"> 
		            		<option value="">Selecione um bairro</option>'; 
		    	
		    				<?php
						    foreach ($bairros as $bairro) {  
						        if (!empty($persona) && !strcmp($bairro->slug, $persona['bairro']))   
						            echo '<option value="'.$bairro->slug.'" selected="selected">'.$bairro->name.'</option>';   
						        else  
						            echo '<option value="'.$bairro->slug.'">'.$bairro->name.'</option>';   
						    }
						    ?>
		    				</select>

		      				
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

		# salva a o NOME

		$first_name_new = $_POST['first_name'];
		update_user_meta($user_id, 'first_name', $first_name_new, $first_name);



		# salva a PERSONA

		// pega a array persona do banco de dados 
		$personaArray = get_user_meta($user_id, 'persona', true);
		if(is_array($personaArray)){
			$persona = $personaArray;
		}


		// adiciona a atividade praticada na array
		$persona['bairro'] = $_POST['persona_bairro'];


		// atualiza no banco de dados
		update_user_meta($user_id, 'persona', $persona);

	}




?>
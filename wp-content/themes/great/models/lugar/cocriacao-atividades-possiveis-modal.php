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

					// pega a array com as atividades do banco de dados
					$atividades_possiveis = get_post_meta($post_id, 'atividades_possiveis', true);

					// cria a query que irá pegar as informações do banco de dados
					$query = "SELECT ID, lugar_id, conteudo_moderacao FROM wp_cocriacao WHERE usuario_id = 1 ";
					
					// executa a query
					$result = mysql_query($query);
					
					// cria uma array que armazenará o resultado do select acima
					$result_array = array();

					$i = 0;

					// monta a array com as informações baixadas do banco de dados
					while ($atividade = mysql_fetch_array($result)) {
						$result_array[$i]['ID'] = $atividade[0];
						$result_array[$i]['lugar_id'] = $atividade[1];
						$result_array[$i]['conteudo_moderacao'] = unserialize($atividade[2]);

						$i++;
					}
					
					

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


						    <span style="float: left; height: 30px; margin-top: 25px;"><b>Criar atividade</b></span>

						    <input type="text" class="form-control" name="nova_atividade" id="nova_atividade" value="" size="30" placeholder="Ex:. Nome da atividade ou ação"/>

		      			</div>

		      		</div>



		      		<?php  wp_nonce_field( 'post_nonce', 'post_nonce_field_atividades_possiveis' ); ?>

		      	
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

	if (isset( $_POST['post_nonce_field_atividades_possiveis'] ) && wp_verify_nonce( $_POST['post_nonce_field_atividades_possiveis'], 'post_nonce')) {

		

		# salva as ATIVIDADES MARCADAS

		// pega as atividades já cadastradas no BD
		$atividades_cadastradas = get_post_meta($post_id, 'atividades_possiveis', true); 

		// pega as atividades selecionadas na cocriação
		$atividades_selecionadas = $_POST['atividades_possiveis']; 

		// Coloca as atividades em ordem alfabética
		if(isset($atividades_selecionadas)){
			sort($atividades_selecionadas); 
		}


		// só continua se alguma tividade for selecionada
		if($atividades_selecionadas != ''){

			foreach ($atividades_selecionadas as $atividade_id) {
				array_push($atividades_cadastradas, array('id' => $atividade_id, 'contador' => '', 'usuarios_id' => array()));
			}


			if (is_array($atividades_cadastradas)) {
				update_post_meta($post_id, 'atividades_possiveis', $atividades_cadastradas);
			} 

		}







		# salva a NOVA ATIVIDADE CRIADA


		// pega a atividade enviada pelo form
		$nova_atividade = $_POST['nova_atividade'];

		if (strlen($nova_atividade) < 30) {

			// remove a possibilidade de enviar tags html
			$nova_atividade = htmlspecialchars($nova_atividade);

			// cria uma array que será usada para organizar as informações em moderação
			$conteudo_moderacao = array('atividade' => $nova_atividade);

			// transforma a array em string para ser possível salvar no banco de dados
			$conteudo_moderacao_array = serialize($conteudo_moderacao);

			// cria a query que irá pegar as informações do banco de dados
			$query = "SELECT ID, conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = '$post_id' AND bloco = 'atividades_possiveis' ";
			
			// executa a query
			$result = mysql_query($query);
			
			// cria uma array que armazenará o resultado do select acima
			$result_array = array();

			$i = 0;

			// monta a array com as informações baixadas do banco de dados
			while ($atividade = mysql_fetch_array($result)) {
				$result_array[$i]['ID'] = $atividade[0];
				$result_array[$i]['conteudo_moderacao'] = unserialize($atividade[1]);

				$i++;
			}
			

			if (!in_multiarray($nova_atividade, $result_array)) {
				
				$user_id_array = array(0 => $user_id);

				// envia para o banco de dados na tabela 'wp_cocriacao'
				$envia = "INSERT INTO wp_cocriacao(lugar_id, usuario_id, bloco, conteudo, conteudo_moderacao) VALUES('$post_id', '$user_id_array', 'atividades_possiveis', '$conteudo_array', '$conteudo_moderacao_array')";

				// envia a query para o banco de dados
				mysql_query($envia) or die();

			}

			else{

				echo '<div class="single container" style="padding:0 30px">
					<div class="panel panel-info">
						<div class="panel-body">
							<b>Esta atividade já esta cadastrada. Você pode criar outra atividade clicando <a data-toggle="modal" data-target="#atividades-possiveis-modal" style="cursor:pointer">aqui</a>. =)</b>
						</div>	
					</div>
				  </div>';

			}

			

		}

		else{

			echo '<div class="single container" style="padding:0 30px">
					<div class="panel panel-danger">
						<div class="panel-body">
							<b>O nome das atividades devem ser menor que 30 caracteres. Você pode tentar criar novamente clicando <a data-toggle="modal" data-target="#atividades-possiveis-modal" style="cursor:pointer">aqui</a>. =)</b>
						</div>	
					</div>
				  </div>';

		}

	}




?>
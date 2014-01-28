<?php




	// The Callback

	global $post;


	$input = new CriarEditarInputs;
	$input->post_id = $post->ID;
	$input->segmento = 'lazer';

?>

	<form enctype="multipart/form-data" id="primaryPostForm" method="POST">

		<div class="panel panel-default form-group">

			<div class="panel-body">
		
				<div class="bloco">
					<h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Criar lugar na categoria: lazer</h2>
				</div>

				


					<?php	

					# TITULO DO LUGAR name -> post_title
					
					$input->titulo();			  
					
					?>


					<?php

				    # SUBCATEGORIA name -> subcategoria
					 
					$input->subcategoria(64);
					
					?>				 




					<?php

				    # DESCRIÇÃO name -> descricao
				 
					$input->descricao();
							
					?>
								


				</div>

			</div>
					
				   	

			<div class="panel panel-default form-group">

				<div class="panel-body">

					<div class="bloco">
						<h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Localização</h2>
					</div>



					<?php

					# BAIRRO name -> bairro
					
					$input->bairro();
				
					?>



					<?php

					# ENDEREÇO name -> endereco
				    
					$input->endereco();
				   
					?>




				    
				    <?php

				    # LUGAR PAI name ->  lugar_pai

				    $input->lugar_pai();
					
				    ?>



				    <?php

					# NOME DO LUGAR name -> lugar_pai_nome

					$input->lugar_pai_nome();
				   
					?>	


				</div>

			</div>




			<div class="panel panel-default form-group">

				<div class="panel-body">
				

					<div class="bloco">
						<h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Atividades e Eventos</h2>
					</div>




					<?php

					# ATIVIDADES POSSÍVEIS name -> atividades_possiveis

					$input->atividades_possiveis();

					?>




					<?php

					# TAMBÉM SÃO REALIZADOS name -> servicos_oferecidos_info

					$input->servicos_oferecidos_info();
					
					?>


				</div>

			</div>




			<div class="panel panel-default form-group">

				<div class="panel-body">


					<div class="bloco">
						<h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Como ter acesso</h2>
					</div>



					<?php

					# COMO TER ACESSO name -> acesso

					$input->acesso();
					
					?>



					<?php

				   	# PREÇO name -> preco

				   	$input->preco();

				   	?>



			   	</div>

		   	</div>

				


			<div class="panel panel-default form-group">

				<div class="panel-body">


					<div class="bloco">
						<h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Dias e horários</h2>
					</div>

				




					<!-- SELECIONADOR DE OPÇÕES que mostra ou esconde informações para dias específicos ou semanais -->

					<div class="bloco">
						<div class="row">
							<div class="col-sm-3">
								<label class="pull-right">O lugar funciona:</label>
							</div>

							<div class="col-sm-9">
								<div class="especificas_ou_semana">
									<input type="radio" name="grupo1" value="datas_semana" checked> Todo ano <br>
									<input type="radio" name="grupo1" value="datas_especificas"> Em datas específicas (de X até X dia)<br>
								</div>
							</div>
						</div>
					</div>


					<?php

					# DATAS ESPECÍFICAS name -> inicio_dia, inicio_hora, termino_dia, termino_hora

					$input->datas_especificas();
				

					?>



					<?php

					# DIAS DA SEMANA name -> dias

			    	$input->dias_da_semana();

					?>		



					<?php

					# INFORMAÇÕES SOBRE DATAS name -> data_info

					$input->dias_da_semana_info();
									
					?>	

				</div>

			</div>



			<div class="panel panel-default form-group">

				<div class="panel-body">
				   
					<div class="bloco">
						<h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Contato</h2>
					</div>

					
					<?php

					# TELEFONE E EMAIL SITE E FACEBOOK name -> telefone, name -> email, name -> site, name -> facebook

					$input->contato();

					?>



					<?php

				    # IMAGEM DE CAPA name -> upload_attachment

					$input->imagem_de_capa();

					?>




					<?php

					wp_nonce_field( 'post_nonce', 'post_nonce_field' ); 

					?>

					<input type="hidden" name="submitted" id="submitted" value="true" />

					<button type="submit" class="btn btn-success pull-right">Atualizar informações</button>

			

				

				</div>

			</div>

	</form>
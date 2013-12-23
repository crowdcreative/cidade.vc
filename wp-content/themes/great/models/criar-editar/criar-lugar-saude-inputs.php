<?php




// The Callback

global $post;

	
	?>


<form action="" enctype="multipart/form-data" id="primaryPostForm" method="POST">

	

	<div class="panel panel-default form-group">


		

		<div class="panel-heading">
			<h2>Criar lugar na categoria: saúde</h2>
		</div>


		

		<div class="panel-body">

	
			<?php

						
			# TITULO DO LUGAR name -> post_title
			
			$titulo = get_the_title($post->ID); // pega o valor de 'titulo' salvo do BD

	    	echo '<div class="bloco"><div class="row">'; 
		    	echo '<div class="col-sm-3"><label class="pull-right" for="titulo">Nome do Lugar</label></div>';
		        echo '<div class="col-sm-9">';
		        	echo '<input type="text" class="form-control" name="post_title" id="titulo" value="'.$titulo.'" size="30" />';
		        	echo '<span class="help-block">Coloque o nome oficial do lugar.</span>';  
		        echo '</div>';
		    echo '</div></div>';
						  




		    # SUBCATEGORIA name -> subcategoria
			 
			echo '<div class="bloco"><div class="row">';

				// pega as subcatgeorias da categoria 'saúde'
			    $args = array('child_of' => 13, 'hide_empty' => 0);
				$terms = get_categories( $args );
			    $selected = wp_get_object_terms($post->ID, 'category');


				echo '<div class="col-sm-3"><label class="pull-right" for="subcategoria">Subcategoria</label></div>';
		        echo '<div class="col-sm-9">';

			    echo '<select class="form-control" name="subcategoria" id="subcategoria"> 
			            <option value="">Selecione uma subcategoria</option>'; // Select One  
			    foreach ($terms as $term) {  
			        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug OR $term->slug, $selected[1]->slug))   
			            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
			        else  
			            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
			    }  

			    echo '</select>'; 
			    echo '<span class="help-block">Escolha a subcategoria que mais se encaixa com o lugar.</span>'; 
				echo '</div>';
			echo '</div></div>';
							 






		    # DESCRIÇÃO name -> descricao
		 
			$descricao = get_post_meta($post->ID, 'descricao', true); // pega valor do BD

	    	echo '<div class="bloco"><div class="row">';
		    	echo '<div class="col-sm-3"><label class="pull-right" for="descricao">Descrição</label></div>';
		        echo '<div class="col-sm-9">'; 
		        echo '<textarea class="form-control" name="descricao" id="descricao" style="width:100%" rows="6" placeholder="EX:. Unidades Básicas de Saúde (UBS) são locais onde você pode receber atendimentos básicos e gratuitos em Pediatria, Ginecologia, Clínica Geral, Enfermagem e Odontologia. Esta UBS atende pessoas que moram no Partenon e proximidade.">'.$descricao.'</textarea>';  
		   		echo '<span class="help-block">Descreva o que é, quem atende e o que oferece. Seja breve, deixe para detalhar os serviços oferecidos e como ter acesso em seus respectivos campos.</span>'; 
		   		echo '</div>';
		   	echo '</div></div>';
					

			?>

		</div>

	</div>






	<div class="panel panel-default form-group">

		<div class="panel-body">
			
		   	<!-- H2  Localização -->

			<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Localização</h2></div>



			<?php


			# BAIRRO name -> bairro
			
			$terms = get_terms( 'bairros', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
		    $selected = wp_get_object_terms($post->ID, 'bairros');  

			echo '<div class="bloco"><div class="row">';
				echo '<div class="col-sm-3"><label class="pull-right" for="bairro">Bairro</label></div>';
		        echo '<div class="col-sm-9">';
			    echo '<select class="form-control" name="bairro" id="bairro"> 
			            <option value="">Selecione um bairro</option>'; // Select One  
			    
					    foreach ($terms as $term) {  
					        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
					            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
					        else  
					            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
					    }  
			    echo '</select>';
			    echo '<span class="help-block">Selecione o bairro onde o evento irá acontecer.</span>';  
				echo '</div>';
			echo '</div></div>';
		





			# ENDEREÇO name -> endereco
		    
			$endereco = get_post_meta($post->ID, 'endereco', true); // pega valor do BD

	    	echo '<div class="bloco"><div class="row">'; 
		    	echo '<div class="col-sm-3"><label class="pull-right" for="endereco">Endereço</label></div>';
		        echo '<div class="col-sm-9">';
		        	echo '<div class="input-group">';
		        		echo '<input class="form-control" type="text" name="endereco" id="endereco" value="'.$endereco.'" placeholder="Ex:. Rua Rivadávia Correia, 08 - Partenon" style="width:100%; text-transform:capitalize;" />';  
		        		echo '<span class="input-group-btn"><button id="botao" class="btn btn-default" type="button">Buscar endereço</button></span>';
		        	echo '</div>';

		        	echo '<div id="map-canvas"></div>';

		        echo '</div>';
		    echo '</div></div>';
		   





		    # LATITUDE E LONGITUDE name -> latlong

		    $latlong = get_post_meta($post->ID, 'latlong', true); // pega valor do BD
		    
		 	echo '<input type="hidden" name="latlong" id="latlong" value="'.$latlong.'" size="30" />'; 
		    





		    # LUGAR PAI name ->  lugar_pai

		    $lugar_pai_ID = get_post_meta($post->ID, 'lugar_pai', true); // pega valor do BD
			  
			echo '<div class="bloco"><div class="row">';
					echo '<div class="col-sm-3"><label class="pull-right" for="lugar_pai">Este lugar fica dentro de outro lugar?</label></div>';
			        echo '<div class="col-sm-9">';
						$items = get_posts( array (  
						    'post_type' => array('lugar-lazer','lugar-saude'),  
						    'posts_per_page' => -1  
						));  
					    echo '<select class="form-control" name="lugar_pai" id="lugar_pai"> 
					            <option value="">Selecione um lugar</option>'; // Select One  
					        foreach($items as $item) {  
					            echo '<option value="'.$item->ID.'"',$lugar_pai_ID == $item->ID ? ' selected="selected"' : '','>'.$item->post_title.'</option>';  
					        } // end foreach  
					    echo '</select>';
					    echo '<span class="help-block">Selecione o local onde o evento irá acontecer (se houver a opção).</span>';  
				echo '</div>';
			echo '</div></div>';
			



			$lugar_pai_nome = get_post_meta($post->ID, 'lugar_pai_nome', true); // pega valor do BD

			# NOME DO LUGAR name -> lugar_pai_nome
	    	
	    	echo '<div class="bloco"><div class="row">'; 
		    	echo '<div class="col-sm-3"><label class="pull-right" for="lugar_pai_nome"></label></div>';
		        echo '<div class="col-sm-9">';
		        	echo '<input type="text" class="form-control" name="lugar_pai_nome" id="lugar_pai_nome" value="'.$lugar_pai_nome.'" size="30" placeholder="Ex:. Mercado Público"/>';
		        	echo '<span class="help-block">Se não achar o nome do local na opção anterior, escreva-o aqui.</span>';  
		        echo '</div>';
		    echo '</div></div>';

		    ?>

	   	</div>

	</div>



		    
	   
				
	<div class="panel panel-default form-group">

		<div class="panel-body">


		    <!-- H2 Serviços oferecidos -->

			<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Serviços oferecidos</h2></div>




			<?php

			# SERVIÇOS OFERECIDOS name -> servicos

			$servicos_oferecidos = get_post_meta($post->ID, 'servicos_oferecidos', true); // pega valor do BD

			echo '<div class="bloco"><div class="row">';
			echo '<div class="col-sm-3"><label class="pull-right" style="text-align:right" for="'.$field['id'].'">Selecione os serviços oferecidos neste lugar</label></div>';
	        echo '<div class="col-sm-9">';
			   

			    $terms = get_terms( 'serviços', array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'parent'  => 0 )); 

			    echo '<div class="checkbox" style="margin-bottom:25px">';
		        	echo '<div class="row">';

	        			foreach ($terms as $term) {  
		        		echo '<div class="col-sm-6" style="margin-bottom: 12px;">
		        				<input type="checkbox" value="'.$term->term_id.'" name="servicos[]" id="' . $term->term_id . '" '; if(is_array($servicos_oferecidos)){ if(in_multiarray($term->term_id, $servicos_oferecidos)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
		            			echo $term->name.
		            		 '</div>';   
		   				 } 
			   	echo '</div>';
			        echo '</div>'; 

			echo '</div>';
			echo '</div></div>';








			# TAMBÉM SÃO REALIZADOS name -> servicos_oferecidos_info

			$servicos_oferecidos_info = get_post_meta($post->ID, 'servicos_oferecidos_info', true); // pega valor do BD

			echo '<div class="bloco"><div class="row">';
		    	echo '<div class="col-sm-3"><label class="pull-right" for="servicos_oferecidos_info">Também são realizados</label></div>';
		        echo '<div class="col-sm-9">'; 
		        echo '<textarea class="form-control" name="servicos_oferecidos_info" id="servicos_oferecidos_info" style="width:100%" rows="6" placeholder="Ex:. Agendamento de consultas especializadas, acompanhamento de crianças e gestantes em risco nutricional. Busca de faltosos aos programas, visitas domiciliares dos agentes comunitários de saúde, visitas domiciliares dos profissionais da equipe técnica e grupos de orientação e educação em saúde.">'.$servicos_oferecidos_info.'</textarea>';  
		   		echo '<span class="help-block">Utilize este espaço, se necessário, para informar outros serviços que o lugar oferece ou como complemento aos serviços oferecidos.</span>'; 
		   		echo '</div>';
		   	echo '</div></div>';
		

		   	?>

		</div>

	</div>




	<div class="panel panel-default form-group">

		<div class="panel-body">


		   	<!-- H2 Como ter acesso -->

			<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Como ter acesso</h2></div>



			<?php

			# COMO TER ACESSO name -> acesso

			$acesso = get_post_meta($post->ID, 'acesso', true); // pega valor do BD

			echo '<div class="bloco"><div class="row">';
		    	echo '<div class="col-sm-3"><label class="pull-right" for="acesso">O que é preciso para poder usar os serviços deste lugar</label></div>';
		        echo '<div class="col-sm-9">'; 
		        echo '<textarea class="form-control" name="acesso" id="acesso" style="width:100%" rows="6" placeholder="Ex:. O acesso é feito gratuitamente com o Cartão SUS. Se você não tiver o cartão ele pode ser feito nesta unidade UBS ou em hospitais, clínicas e postos de saúde ou locais definidos pela secretaria municipal de saúde, mediante a apresentação de RG, CPF, certidão de nascimento ou casamento.">'.$acesso.'</textarea>';  
		   		echo '<span class="help-block">O que é preciso e como se dá para usar os serviços oferecidos por este lugar.</span>'; 
		   		echo '</div>';
		   	echo '</div></div>';
			





		   	# PREÇO name -> preco

		   	$selected = wp_get_object_terms($post->ID, 'preço');
			
			echo '<div class="bloco"><div class="row">';
				echo '<div class="col-sm-3"><label class="pull-right" for="preco">Preço</label></div>';
		        echo '<div class="col-sm-9">';
			    echo '<select class="form-control" name="preco" id="preco"> 
			            <option value="">Selecione um valor</option>'; // Select One  
			    $terms = get_terms( 'preço', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
			    $selected = wp_get_object_terms($post->ID, 'preço');  
			    foreach ($terms as $term) {  
				        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
				            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
				        else  
				            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
			    }  
			    echo '</select>';
			    echo '<span class="help-block">Selecione a faixa de preço que melhor representa o lugar.</span>';  
				echo '</div>';
			echo '</div></div>';



			?>	

		</div>

	</div>



	<div class="panel panel-default form-group">

		<div class="panel-body">



		   	<!-- H2 Dias e horários -->

			<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Dias e horários</h2></div>

	




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

			$dataArray = get_post_meta($post->ID, 'data_especifica', true); // pega valor do BD

			if(is_array($dataArray)){
				$data = $dataArray;
			}else{
				unset($data);
			}

			echo '<div class="bloco especificas" style="display:none"><div class="row">';
				echo '<div class="col-sm-3"><label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label></div>';
		        echo '<div class="col-sm-9">';
			 	echo '<div class="row">';
					echo 	'<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-8 form-group">
										<input type="text" class="datepicker form-control data" name="inicio_dia" id="inicio_dia" value="'.$data['inicio_dia'].'"  placeholder="Ex:.12/05/2013"/>
										<span class="help-block">Data de início.</span>
									</div>

									<div class="col-sm-4 form-group">
										<input type="time" name="inicio_hora" id="inicio_hora" placeholder="__:__" value="'.$data['inicio_hora'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
										<span class="help-block">Hora de início.</span>
									</div>
								</div>
							</div>'; 
					
					echo '<div class="col-sm-6">
							<div class="row">
								<div class="col-sm-8 form-group">
									<input type="text" class="datepicker form-control" name="termino_dia" id="termino_dia" value="'.$data['termino_dia'].'" placeholder="Ex:.15/05/2013"/>
									<span class="help-block">Data de termino.</span> 
								</div>
								<div class="col-sm-4 form-group">
									<input type="time" name="termino_hora" id="termino_hora" placeholder="__:__" value="'.$data['termino_hora'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control">
									<span class="help-block">Hora de termino.</span>
								</div>
							</div>
						</div>'; 

				echo '</div>';
				echo '</div>';
			echo '</div></div>';









			# DIAS DA SEMANA name -> dias

			echo '<div class="bloco semana"><div class="row">';
				echo '<div class="col-sm-3"><label class="pull-right" style="text-align:right" for="'.$field['id'].'">Selecione os dias da semana em que o lugar está aberto</label></div>';
		        	echo '<div class="col-sm-9">';
				   
				   		inputs_dias_da_semana($post->ID);

					echo '</div>';
				echo '</div>
			</div>';






			# INFORMAÇÕES SOBRE DATAS name -> data_info

			$data_info = get_post_meta($post->ID, 'data_info', true); // pega valor do BD

			echo '<div class="bloco"><div class="row">';
		    	echo '<div class="col-sm-3"><label class="pull-right" for="data_info">Observações relacionados aos dias de funcionamento</label></div>';
		        echo '<div class="col-sm-9">'; 
		        echo '<textarea class="form-control" name="data_info" id="data_info" style="width:100%" rows="5" placeholder="Ex:. Clinico-Geral : Acolhimento e agendamento diário. Dentista: Adultos quarta-feira. Crianças: Segunda- feira com grupo de escovação. Ginecologista e Obstetrícia: Agenda mensal. Pediatria: Grupo de Asma às quartas-feiras à tarde e acolhimento na sexta-feira.">'.$data_info.'</textarea>'; 
		        echo '<span class="help-block">Caso necessite informar mais detalhes relacionado aos dias e horários, use este espaço.</span>';
		   		echo '</div>';
		   	echo '</div></div>';
					
			?>


		</div>

	</div>		



	<div class="panel panel-default form-group">

		<div class="panel-body">


		   	<!-- H2 Contato -->

			<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Contato</h2></div>

			


			<?php

			# TELEFONE E EMAIL name -> telefone, name -> email

			$contatosArray = get_post_meta($post->ID, 'contatos', true); // pega valor do BD

			if(is_array($contatosArray)){
				$contatos = $contatosArray;
			}else{
				unset($contatos);
			}

			echo '<div class="bloco"><div class="row">'; 
		    	echo '<div class="col-sm-3"><label class="pull-right" for="telefone">Telefone e email</label></div>';
			        echo '<div class="col-sm-9">';
			        	echo '<div class="row">';
			        		echo '<div class="col-sm-6">';
					        	echo '<input type="text" class="form-control telefone" name="telefone" id="telefone" value="'.$contatos['telefone'].'" size="30" placeholder="Ex:. (51)1234.1234"/>';
					        	echo '<span class="help-block">Coloque o número de telefone do lugar, se não houver, o número do orgão responsável.</span>';  
			        		echo '</div>';
			        		echo '<div class="col-sm-6">';
			        			echo '<input type="text" class="form-control email" name="email" id="email" value="'.$contatos['email'].'" size="30" placeholder="Ex:. contato@email.com.br"/>';
					        	echo '<span class="help-block">Coloque o email de contato do lugar, se não houver, o email do orgão responsável.</span>';  
			        		echo '</div>';
			        	echo '</div>';
			        echo '</div>';
		    echo '</div></div>';




		    # SITE E FACEBOOK name -> site, name -> facebook
			
			echo '<div class="bloco"><div class="row">'; 
		    	echo '<div class="col-sm-3"><label class="pull-right" for="telefone">Site e Facebook</label></div>';
			        echo '<div class="col-sm-9">';
			        	echo '<div class="row">';
			        		echo '<div class="col-sm-6">';
					        	echo '<input type="text" class="form-control site" name="site" id="site" value="'.$contatos['site'].'" size="30" placeholder="Ex:. www.sitedolugar.com.br"/>';
					        	echo '<span class="help-block">Coloque o endereço do site.</span>';  
			        		echo '</div>';
			        		echo '<div class="col-sm-6">';
			        			echo '<input type="text" class="form-control facebook" name="facebook" id="facebook" value="'.$contatos['facebook'].'" size="30" placeholder="Ex:. facebook.com/cidade.vc"/>';
					        	echo '<span class="help-block">Coloque o endereço da fanpage do lugar.</span>';  
			        		echo '</div>';
			        	echo '</div>';
			        echo '</div>';
		    echo '</div></div>';






		    # IMAGEM DE CAPA name -> upload_attachment

			$imagemID = get_post_meta($post->ID, 'imagem_capa', true);
			$atachment_url = wp_get_attachment_image_src( $imagemID, 'image-lugar');

		    echo '<div class="bloco"><div class="row">'; 
		    	echo '<div class="col-sm-3"><label class="pull-right" for="telefone">Foto que representa o lugar</label></div>';
			        echo '<div class="col-sm-9">';
			        	echo '<img src="'.$atachment_url[0].'" class="img-thumbnail" width="200px">';
				    	echo '<input type="file" name="upload_attachment[]" />';
						echo '</div>';
			        echo '</div>';
		    echo '</div></div>';







	 wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

		</div>

	</div>


	<input type="hidden" name="submitted" id="submitted" value="true" />

	<button type="submit" class="btn btn-success pull-right">Atualizar informações</button>




</form>

	
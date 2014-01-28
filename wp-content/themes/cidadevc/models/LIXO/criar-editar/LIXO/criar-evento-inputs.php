<?php

// The Callback

global  $post;

	
	// Begin the field table and loop
	echo '<div>';

	echo '<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Editar evento</h2></div>';

	echo '<form action="" id="primaryPostForm" method="POST">';




		# TITULO DO EVENTO name -> post_title
			
    	$titulo = get_the_title($post->ID);

    	echo '<div class="bloco"><div class="row">'; 
	    	echo '<div class="col-sm-3"><label class="pull-right" for="titulo">Titulo do evento</label></div>';
	        echo '<div class="col-sm-9">';
	        	echo '<input type="text" class="form-control" name="post_title" id="post_title" value="'.$titulo.'" size="30" />';
	        	echo '<span class="help-block">Este será o nome principal do evento ou atividade. Seja breve, coloque o nome do evento, e se necessário o tipo de evento. Ex:. Feira de antiguidades "nome estranho que sozinho fica sem sentido".</span>';  
	        echo '</div>';
	    echo '</div></div>';
				












	    # CATEGORIA name -> categoria
					  
		echo '<div class="bloco"><div class="row">';
			echo '<div class="col-sm-3"><label class="pull-right" for="categoria">Categoria</label></div>';
		        echo '<div class="col-sm-9">';

				    echo '<select class="form-control" name="categoria" id="categoria"> 

			            <option value="">Selecione uma categoria</option>'; 

						    $terms = get_terms( 'category', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
						    $selected = wp_get_object_terms($post->ID, 'category');

						    foreach ($terms as $term) {  
						        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
						            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
						        else  
						            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
						    }  
				   
				   
				    echo '</select>'; 
			    echo '<span class="help-block">Escolha a categoria que mais se encaixa com o evento.</span>'; 
			echo '</div>';
		echo '</div></div>';
						









		# DESCRIÇÃO name -> descricao

    	echo '<div class="bloco"><div class="row">';
	    	echo '<div class="col-sm-3"><label class="pull-right" for="descricao">Descrição</label></div>';
	        	echo '<div class="col-sm-9">'; 
	       	 		echo '<textarea class="form-control" name="descricao" id="descricao" style="width:100%" rows="5"></textarea>';  
	   				echo '<span class="help-block">Descreva sobre o que acontece no evento, as atividades realizadas e informações importantes.</span>'; 
	   			echo '</div>';
	   	echo '</div></div>';
					   






		# H2 -> Localização

		echo '<div class="bloco"><h2 style="padding-bottom: 8px; border-bottom: 1px solid rgb(221, 221, 221);">Localização</h2></div>';

		



		# BAIRRO name -> bairro

		$terms = get_terms( 'bairros', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
	    $selected = wp_get_object_terms($post->ID, 'bairros');  

		echo '<div class="bloco"><div class="row">

			<div class="col-sm-3">
				<label class="pull-right" for="bairro">Bairro</label>
			</div>

	        <div class="col-sm-9">

			    <select class="form-control" name="bairro" id="bairro"> 
		            
		            <option value="">Selecione um bairro</option>'; 
			    
				    foreach ($terms as $term) { 

				        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug)){   
				            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
				        }else{  
				            echo '<option value="'.$term->slug.'">'.$term->name.'</option>'; 
				        }  
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







	
	   	# H2 -> Dias e horários

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

	# DATAS ESPECÍFICAS name -> inicio_dia, inicio_hora, termino_dia, termino_hora

		$dataArray = get_post_meta($post->ID, 'data_especifica', true); // pega valor do BD

		if(is_array($dataArray)){
			$data = $dataArray;
		}else{
			unset($data);
		}
	
		echo '<div class="bloco especificas"><div class="row">';
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

		echo '<div class="bloco semana"  style="display:none"><div class="row">';
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
				

		<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

		<input type="hidden" name="submitted" id="submitted" value="true" />

		<button type="submit" class="btn btn-success pull-right">Salvar alterações</button>


	


	</form>

	</div>
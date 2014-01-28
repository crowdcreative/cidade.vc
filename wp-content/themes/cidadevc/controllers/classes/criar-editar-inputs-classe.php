<?php

	/**
	* 	CRIAR EDITAR INPUTS
	*/
	class CriarEditarInputs{
		
		
		public $post_id;
		public $segmento = NULL;


	
		public function titulo(){


			$titulo = get_the_title($this->post_id); // pega o valor de 'titulo' salvo do BD

			
			echo '
	    	<div class="bloco">
				<div class="form-group">

				    	<label class="col-sm-3" for="titulo">
				    		<p class="pull-right input-title">Nome do Lugar</p>
				    	</label>
				       
				        <div class="col-sm-9" style="position:relative">        	
				        	<input type="text" class="form-control verificador" name="post_title" id="titulo" value="'.$titulo.'" tipo="string" tamanhoMax="100" tamanhoMin="3" size="30" />
				        	<div class="input-glyphicon"></div>
				        	<span class="help-block">Coloque o nome oficial do lugar.</span>
				        	<span class="error-block"></span>
				        </div>

		   	 	</div>
	   	 	</div>';

		}





		public function subcategoria($categoria_id){

			echo 
			'<div class="bloco">
				<div class="row">';

					// pega as subcatgeorias da categoria 'saúde'
				    $args = array('child_of' => $categoria_id, 'hide_empty' => 0);
					$terms = get_categories( $args );
				    $selected = wp_get_object_terms($this->post_id, 'category');


					echo '
					<label class="col-sm-3">
						<label class="pull-right" for="subcategoria">Subcategoria</label>
					</label>
			        
			        <div class="col-sm-9">

					    <select class="form-control" name="subcategoria" id="subcategoria"> 
					            <option value="">Selecione uma subcategoria</option>';   
								    foreach ($terms as $term) {  
								        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug OR $term->slug, $selected[1]->slug))   
								            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
								        else  
								            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
								    }  

					    echo '
					    </select>
					    	
				    	<span class="help-block">Escolha a subcategoria que mais se encaixa com o lugar.</span>
					
					</div>
				</div>
			</div>';

		}






		public function descricao(){

			$descricao = get_post_meta($this->post_id, 'descricao', true); // pega valor do BD

	    	echo 
	    	'<div class="bloco">
	    		<div class="row">

		    		<label class="col-sm-3">
		    			<label class="pull-right" for="descricao">Descrição</label>
	    			</label>

		       		<div class="col-sm-9">
		        		<textarea class="form-control" name="descricao" id="descricao" style="width:100%" rows="6" placeholder="EX:. Unidades Básicas de Saúde (UBS) são locais onde você pode receber atendimentos básicos e gratuitos em Pediatria, Ginecologia, Clínica Geral, Enfermagem e Odontologia. Esta UBS atende pessoas que moram no Partenon e proximidade.">'.$descricao.'</textarea>
		   				<span class="help-block">Descreva o que é, quem atende e o que oferece. Seja breve, deixe para detalhar os serviços oferecidos e como ter acesso em seus respectivos campos.</span>
		   			</div>

		   		</div>
		   	</div>';

		}






		public function bairro(){

			$terms = get_terms( 'bairros', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
		    $selected = wp_get_object_terms($this->post_id, 'bairros');  

			echo 
			'<div class="bloco">
				<div class="row">

					<label class="col-sm-3">
						<label class="pull-right" for="bairro">Bairro</label>
					</label>

			        <div class="col-sm-9">
				    	<select class="form-control" name="bairro" id="bairro"> 
				            <option value="">Selecione um bairro</option>';  
				    
						    foreach ($terms as $term) {  
						        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
						            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
						        else  
						            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
						    }  

					    echo 
					    '</select>
				  		
				  		<span class="help-block">Selecione o bairro onde o evento irá acontecer.</span>  
					
					</div>
				</div>
			</div>';

		}




		public function endereco(){
			
			$endereco = get_post_meta($this->post_id, 'endereco', true); // pega valor do BD

	    	echo 
	    	'<div class="bloco">
	    		<div class="row">
		    	
			    	<label class="col-sm-3">
			    		<label class="pull-right" for="endereco">Endereço</label>
		    		</label>
			        
			        <div class="col-sm-9">
			        	<div class="input-group">
			        		<input class="form-control" type="text" name="endereco" id="endereco" value="'.$endereco.'" placeholder="Ex:. Rua Rivadávia Correia, 08 - Partenon" style="width:100%; text-transform:capitalize;" />
			        		<span class="input-group-btn"><button id="botao" class="btn btn-default" type="button">Buscar endereço</button></span>
			        	</div>

			        	<div id="map-canvas"></div>

			   		</div>

		   		</div>
	   		</div>';


	   		# LATITUDE E LONGITUDE name -> latlong

		    $latlong = get_post_meta($this->post_id, 'latlong', true); // pega valor do BD
		    
		 	echo '<input type="hidden" name="latlong" id="latlong" value="'.$latlong.'" size="30" />'; 


		}







		public function lugar_pai(){
			
			$lugar_pai_ID = get_post_meta($this->post_id, 'lugar_pai', true); // pega valor do BD
		  
			echo 
			'<div class="bloco">
				<div class="row">

					<label class="col-sm-3">
						<label class="pull-right" for="lugar_pai">Este lugar fica dentro de outro lugar?</label>
					</label>
			        
			        <div class="col-sm-9">';

						$items = get_posts( array ('post_type' => array('lugar-lazer','lugar-saude'), 'posts_per_page' => -1 ));

						echo '
						<select class="form-control" name="lugar_pai" id="lugar_pai">
					            
				            <option value="">Selecione um lugar</option>';   
					        
						        foreach($items as $item) {  
						            echo '<option value="'.$item->ID.'"',$lugar_pai_ID == $item->ID ? ' selected="selected"' : '','>'.$item->post_title.'</option>';  
						        }  

				    	echo '
				    	</select>
					    
					    <span class="help-block">Selecione o local onde o evento irá acontecer (se houver a opção).</span>  
					
					</div>

				</div>
			</div>';

		}






		public function lugar_pai_nome(){

			$lugar_pai_nome = get_post_meta($this->post_id, 'lugar_pai_nome', true); // pega valor do BD
    	
	    	echo 
	    	'<div class="bloco">
	    		<div class="row">

			    	<label class="col-sm-3">
			    		<label class="pull-right" for="lugar_pai_nome"></label>
		    		</label>

			        <div class="col-sm-9">
			        	<input type="text" class="form-control" name="lugar_pai_nome" id="lugar_pai_nome" value="'.$lugar_pai_nome.'" size="30" placeholder="Ex:. Mercado Público"/>
			        	<span class="help-block">Se não achar o nome do local na opção anterior, escreva-o aqui.</span>
			        </div>

		    	</div>
		    </div>';			
		
		}





		public function atividades_possiveis(){
			
			$atividades_possiveis = get_post_meta($this->post_id, 'atividades_possiveis', true); // pega valor do BD

			echo 
			'<div class="bloco">
				<div class="row">

					<label class="col-sm-3">
						<label class="pull-right" style="text-align:right" for="'.$field['id'].'">Selecione os serviços oferecidos neste lugar</label>
					</label>

	       			<div class="col-sm-9">';
			   
			    	$terms = get_terms( 'atividades-possiveis', array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'parent'  => 0 )); 

				   		echo '
				   		<div class="checkbox" style="margin-bottom:25px">
			        		<div class="row">';

		        			foreach ($terms as $term) {  
			        			echo '
			        			<div class="col-sm-6" style="margin-bottom: 12px;">
			        				<input type="checkbox" value="'.$term->term_id.'" name="atividades_possiveis[]" id="' . $term->term_id . '" '; if(is_array($atividades_possiveis)){ if(in_multiarray($term->term_id, $atividades_possiveis)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
			            			echo $term->name.
			            		'</div>';   
			   				} 

					   		echo '
					   		</div>
				        </div>'; 

					echo '
					</div>
				</div>
			</div>'; 
			
		}





		public function servicos_oferecidos_info(){
			
			$servicos_oferecidos_info = get_post_meta($this->post_id, 'servicos_oferecidos_info', true); // pega valor do BD

			echo '
			<div class="bloco">
				<div class="row">
		    		
		    		<label class="col-sm-3">
		    			<label class="pull-right" for="servicos_oferecidos_info">Também são realizados</label>
	    			</label>
		       
		       	 	<div class="col-sm-9">
		        		<textarea class="form-control" name="servicos_oferecidos_info" id="servicos_oferecidos_info" style="width:100%" rows="6" placeholder="Ex:. Agendamento de consultas especializadas, acompanhamento de crianças e gestantes em risco nutricional. Busca de faltosos aos programas, visitas domiciliares dos agentes comunitários de saúde, visitas domiciliares dos profissionais da equipe técnica e grupos de orientação e educação em saúde.">'.$servicos_oferecidos_info.'</textarea>
		   				<span class="help-block">Utilize este espaço, se necessário, para informar outros serviços que o lugar oferece ou como complemento aos serviços oferecidos.</span>
		   			</div>

		   		</div>
		   	</div>';

		}







		public function acesso(){
		
			$acesso = get_post_meta($this->post_id, 'acesso', true); // pega valor do BD

			echo '
			<div class="bloco">
				<div class="row">

		    	<label class="col-sm-3">
		    		<label class="pull-right" for="acesso">O que é preciso para poder usar os serviços deste lugar</label>
	    		</label>
		       	
		       	<div class="col-sm-9">
		        	<textarea class="form-control" name="acesso" id="acesso" style="width:100%" rows="6" placeholder="Ex:. O acesso é feito gratuitamente com o Cartão SUS. Se você não tiver o cartão ele pode ser feito nesta unidade UBS ou em hospitais, clínicas e postos de saúde ou locais definidos pela secretaria municipal de saúde, mediante a apresentação de RG, CPF, certidão de nascimento ou casamento.">'.$acesso.'</textarea>
		   			<span class="help-block">O que é preciso e como se dá para usar os serviços oferecidos por este lugar.</span>
		   		</div>

		   	</div>
	   	</div>';
			
		}






		public function preco(){

			$selected = wp_get_object_terms($post->ID, 'preço');
		
			echo '<div class="bloco">
				<div class="row">

					<label class="col-sm-3">
						<label class="pull-right" for="preco">Preço</label>
					</label>

		        	<div class="col-sm-9">
			    		<select class="form-control" name="preco" id="preco"> 
			            <option value="">Selecione um valor</option>'; 

					    $terms = get_terms( 'preço', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
					    $selected = wp_get_object_terms($post->ID, 'preço');  

					    foreach ($terms as $term) {  
						        if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))   
						            echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';   
						        else  
						            echo '<option value="'.$term->slug.'">'.$term->name.'</option>';   
					    }  

				    	echo '
				    	</select>
			    		<span class="help-block">Selecione a faixa de preço que melhor representa o lugar.</span>
					
					</div>
				</div>
			</div>';
			
		}








		public function datas_especificas(){
			
			$dataArray = get_post_meta($this->post_id, 'data_especifica', true); // pega valor do BD
	
			if(is_array($dataArray)){
				$data = $dataArray;
			}else{	
				unset($data);
			}


			echo '
			<div class="bloco especificas" style="display:none">
				<div class="row">
					<label class="col-sm-3">
						<label class="pull-right" for="'.$field['id'].'">'.$field['label'].'</label>
					</label>

		         	<div class="col-sm-9">
			 			<div class="row">

							<div class="col-sm-6">
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
							</div>
					
							<div class="col-sm-6">
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
							</div> 

						</div>
					</div>

				</div>
			</div>';
		
		}




		public function dias_da_semana(){

			$terms = get_terms( 'dias_de_funcionamento', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
		    $dataArray = get_post_meta($post_id, 'dias_da_semana', true);
		    
		    if(is_array($dataArray)){
		    	$data = get_post_meta($post_id, 'dias_da_semana', true);
		    }
		    else{
		    	unset($data);
		    }


		    echo '<div class="bloco semana">
	    		<div class="row">

					<label class="col-sm-3">
						<label class="pull-right" style="text-align:right" for="'.$field['id'].'">Selecione os dias da semana em que o lugar está aberto</label>
					</label>

		        	<div class="col-sm-9">';


				        foreach ($terms as $term) {  

				        	echo '<div class="checkbox" style="margin-bottom:25px">';
					        	echo '<div class="row">';
					        		echo '<div class="col-sm-2" style="padding-top: 25px;"><input type="checkbox" value="'.$term->name.'" name="dias[]" id="' . $term->id . '" '; if(is_array($data)){ if(in_multiarray($term->name, $data)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
					            	echo $term->name.'</div>';  
					            	echo '<div class="col-sm-10">
					            				<div class="row">
					            					<div class="col-sm-6">
					            					<span>1º Turno</span>
							            				<div class="row">
							            					<div class="col-sm-6">
									            				<input style="width:75px" type="time" name="inicio_hora_'.$term->name.'" id="inicio_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['horarios']['inicio'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
									            				<span class="help-block">Hora de início</span>
									            			</div>
									            			<div class="col-sm-6">
									            				<input style="width:75px" type="time" name="termino_hora_'.$term->name.'" id="termino_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['horarios']['termino'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
									            				<span class="help-block">Hora de termino</span>
									            			</div>
								            			</div>
							            			</div>
							            			<div class="col-sm-6">
							            			<span rel="tooltip-border" title="Use este espaço se necessário, como quando há intervalo no horário de funcionamento (Ex:. horário de almoço às 12:00h).">2º Turno*</span>
							            				<div class="row">
							            					<div class="col-sm-6">
									            				<input style="width:75px" type="time" name="segundo_inicio_hora_'.$term->name.'" id="segundo_inicio_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['segundohorarios']['inicio'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
									            				<span class="help-block">Hora de início</span>
									            			</div>
									            			<div class="col-sm-6">
									            				<input style="width:75px" type="time" name="segundo_termino_hora_'.$term->name.'" id="segundo_termino_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['segundohorarios']['termino'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
									            				<span class="help-block">Hora de termino</span>
									            			</div>
								            			</div>
							            			</div>
						            			</div>
					            		  </div>';
					            echo '</div>';
					        echo '</div>';
					    }  


		    		echo '</div>
				</div>
			</div>';

		}





		public function dias_da_semana_info(){

			$data_info = get_post_meta($this->post_id, 'data_info', true); // pega valor do BD
	   
	    	echo '<div class="bloco">
	    			<div class="row">
	    				<label class="col-sm-3">
		    				<label class="pull-right" for="data_info">Observações relacionados aos dias de funcionamento</label>
	    				</label>';

		        echo '<div class="col-sm-9">'; 
		        	echo '<textarea class="form-control" name="data_info" id="data_info" style="width:100%" rows="5" placeholder="Ex:. Clinico-Geral : Acolhimento e agendamento diário. Dentista: Adultos quarta-feira. Crianças: Segunda- feira com grupo de escovação. Ginecologista e Obstetrícia: Agenda mensal. Pediatria: Grupo de Asma às quartas-feiras à tarde e acolhimento na sexta-feira.">'.$data_info.'</textarea>'; 
		        echo '<span class="help-block">Caso necessite informar mais detalhes relacionado aos dias e horários, use este espaço.</span>';
		   		echo '</div>';
		   	echo '</div></div>';

		}







		public function contato(){
			
			$contatosArray = get_post_meta($this->post_id, 'contatos', true); // pega valor do BD

			if(is_array($contatosArray)){
				$contatos = get_post_meta($this->post_id, 'contatos', true);
			}

	    	echo '
	    	<div class="bloco">
	    		<div class="row"> 

		    		<label class="col-sm-3">
		    			<label class="pull-right" for="telefone">Telefone e email</label>
		    		</label>

			        <div class="col-sm-9">
			        	<div class="row">

			        		<div class="col-sm-6">
					        	<input type="text" class="form-control telefone" name="telefone" id="telefone" value="'.$contatos['telefone'].'" size="30" placeholder="Ex:. (51)1234.1234"/>
					        	<span class="help-block">Coloque o número de telefone do lugar, se não houver, o número do orgão responsável.</span>  
			        		</div>

			        		<div class="col-sm-6">
			        			<input type="text" class="form-control email" name="email" id="email" value="'.$contatos['email'].'" size="30" placeholder="Ex:. contato@email.com.br"/>
					        	<span class="help-block">Coloque o email de contato do lugar, se não houver, o email do orgão responsável.</span>
			        		</div>

			        	</div>
			        </div>

		    	</div>
	    	</div>';




	    	# SITE E FACEBOOK name -> site, name -> facebook
			
			echo '
			<div class="bloco">
				<div class="row">
		    		<div class="col-sm-3">
		    			<label class="pull-right" for="telefone">Site e Facebook</label>
	    			</div>
			        	
			        	<div class="col-sm-9">
			        		<div class="row">
			        			<div class="col-sm-6">
					        		<input type="text" class="form-control site" name="site" id="site" value="'.$contatos['site'].'" size="30" placeholder="Ex:. www.sitedolugar.com.br"/>
					        		<span class="help-block">Coloque o endereço do site.</span>
			        			</div>

			        			<div class="col-sm-6">
			        				<input type="text" class="form-control facebook" name="facebook" id="facebook" value="'.$contatos['facebook'].'" size="30" placeholder="Ex:. facebook.com/cidade.vc"/>
					        		<span class="help-block">Coloque o endereço da fanpage do lugar.</span>
			        			</div>

			        		</div>
			        	</div>

		    		</div>
	    		</div>';

		}






		public function imagem_de_capa(){
			
			$imagemID = get_post_meta($this->post_id, 'imagem_capa', true);
			$atachment_url = wp_get_attachment_image_src( $imagemID, 'image-lugar');

		    echo '
		    <div class="bloco">
		    	<div class="row">

			    	<div class="col-sm-3">
			    		<label class="pull-right" for="telefone">Foto que representa o lugar</label>
		    		</div>

				    <div class="col-sm-9">
				        <img src="'.$atachment_url[0].'" width="200px">
					    <input type="file" name="upload_attachment[]" />
					</div>

				</div>
			</div>';

		}




	}






























?>

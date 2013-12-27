<?php





	/**
	 * 		DESCRIÇÃO
	 * 		Pega a descrição do lugar
	 */

	function get_descricao($post_id){
		
		$descricao = get_post_meta($post_id, 'descricao', true);
		
		if ($descricao != ''){ 
			echo '<div class="bloco" id="descricao_do_lugar">
						<p>'.$descricao.'</p>
				  </div>';
		}
	}



	/**
	 * 		COCRIAÇÃO - DESCRIÇÃO
	 */

	function cocriacao_descricao($post_id){
		
	}












	/**
	 * 		SERVIÇOS OFERECIDOS
	 * 		Pega os serviços marcados como oferecidos pelo lugar
	 */

	function get_servicos_oferecidos($postID){

		$servicos_oferecidos = get_post_meta($postID, 'servicos_oferecidos', true); // Pega a array com os ids dos serviços

		$servicosArray = array(); // cria uma array que será usada para ordenar os nomes servicos mais tarde

		// coloca o conteudo na array 

		foreach ($servicos_oferecidos as $servicoID) {
			$term = get_term_by('id', $servicoID, 'serviços', ARRAY_A);
			$servicosArray[] = $term['name'];
		}

		sort($servicosArray); // coloca os itens da array por ordem alfabética
		
		if($servicos_oferecidos != ''){ 
			echo '<div class="panel-default panel">
					<div class="panel-body">
						<div class="bloco li-default" id="servicos_oferecidos">
							<h2>Serviços oferecidos<small style="cursor:help" rel="tooltip" title="Alguns dos serviços listados abaixo são oferecidos somente em alguns dias e horários da semana. Sempre ligue antes para confirmar os serviços e dias de atendimento." class="glyphicon glyphicon-exclamation-sign"></small></h2>
							<p>';
								echo '<ul class="row">';
								foreach ($servicosArray as $servico) {

									$servico_len = strlen($servico); // pega o tamanho da string

									$servico_cut = substr($servico, 0, 30); // corta a string

									if($servico_len > 30){
										echo '<li class="col-sm-4"><span style="cursor:help" rel="tooltip-border" title="'.$servico.'">'. $servico_cut .' ...</span></li>';
									}else{
										echo '<li class="col-sm-4"><span>' . $servico . '</span></li>';
									}
							
								}
								echo '</ul>
							</p>
						</div>
					</div>
				  </div>';
		} 

	}







	/**
	 * 		SERVIÇOS OFERECIDOS INFO
	 * 		Pega os serviços oferecidos que não contam no 'checkbox' -> complemento do item anterior
	 */


	function get_servicos_oferecidos_info($postID){

		$servicos_oferecidos_info = get_post_meta($postID, 'servicos_oferecidos_info', true);
		
		if($servicos_oferecidos_info != ''){
		
		echo 	'<div class="panel-default panel">	
					<div class="panel-body">
						<div class="bloco" id="tambem_sao_realizados">
			
							<h2>Também são realizados</h2>
							<p>'.$servicos_oferecidos_info.'</p>
					
						</div>
					</div>
				</div>';
		}
	}








	/**
	 * 		ACESSO
	 * 		Pega a 'string' de como se dá o acesso
	 */

	function get_acesso($postID){
		
		$acesso = get_post_meta($postID, 'acesso', true);

		if($acesso != ''){
		echo '<div class="panel-default panel">	
				<div class="panel-body">
					<div class="bloco" id="como_ter_acesso">
					
						<h2>Como ter acesso</h2>
						<p>'.$acesso.'</p>
					
					</div>
				</div>
			  </div>';
		}
	}






	/*
	 *  	DIAS DA SEMANA
	 *  	Pega os dias da semana e horários de funcionamento
	 */

	function get_dias_da_semana($postID){

		$dataArray = get_post_meta($postID, 'dias_da_semana', false);

	    if(is_array($dataArray) != ''){
	    	$data = $dataArray[0];
	    	if(is_array($data)){
	    		ksort($data);
	    	}
	    }


		if($data != ''){ 

		$observacoes = get_post_meta($postID, 'data_info', true);
		if($observacoes != ''){$col = '6';}else{$col = '3';}
		
		echo '<div class="panel-default panel">	
				<div class="panel-body">
					<div class="bloco li-default" id="dias_de_funcionamento">
			
						<h2>Dias e horários de funcionamento</h2>
					
						<p>
						<div class="row">';
							if($observacoes != ''){echo '<div class="col-sm-6">';}else{echo '<div class="col-sm-12">';}
								echo '<ul class="row">';
									
										foreach ($data as $dia) {
											echo '<div class="col-sm-'.$col.'" style="padding:0 0 15px">
													<span class="badge" style="margin-bottom:12px">'.$dia['dia'].'</span>';
													
													if($dia['horarios']['inicio'] != ''){
														echo '<div>'.$dia['horarios']['inicio'].' até '.$dia['horarios']['termino'].'</div>';
													}
													elseif ($dia['segundohorarios']['inicio'] != '') {
														echo '<div>'.$dia['segundohorarios']['inicio'].' até '.$dia['segundohorarios']['termino'].'</div>';
													}
													else{
														echo '<div>Dia todo</div>';
													}
												  
												  echo '</div>';
										}

									
								echo '</ul>
							</div>

							<div class="col-sm-6">';
							
								if($observacoes != ''){ 
									echo '<span style="margin: 0px 0px 5px; cursor:help" rel="tooltip" title="Informações sobre alguns serviços que são oferecidos somente em alguns dias e horários da semana" class="badge">Observações</span>
									<span style="display:block; font-size:85%">
										<p>'.$observacoes.'</p>
									</span>';
								}
							echo '</div>
						</div>
						
						</p>

			
					</div>
				</div>
			  </div>';
		} 
	}











	/**
	 * 		LOCALIZAÇÃO
	 * 		Pega o endereço e cria o mapa (Google Maps) com um marcador do lugar
	 */

	function get_localizacao($postID){

		$endereco = get_post_meta($postID, 'endereco', true);

		if($endereco != ''){ 
			echo '<div class="panel-default panel">	
					<div class="panel-body">
						<div class="bloco" id="localizacao_no_mapa">
							<h2>Localização no mapa<small>'.$endereco.'</small></h2>
							<div id="map-canvas"> </div>
						</div>
					</div>
				  </div>';
		} 

	}











	/**
	 * 		ÔNIBUS QUE PASSAM PERTO
	 * 		Pega os ônibus que passam perto via 'datapoa' -> a função se encontra no arquivo 'functions.php'
	 */

	function get_onibus($postID){

		$endereco = get_post_meta($postID, 'endereco', true);

		if($endereco != ''){ 
			echo '<div class="panel-default panel">	
					<div class="panel-body">
						<div class="bloco li-default li-default-capitalize" id="onibus_que_passao_perto">
							<h2>Ônibus que passam perto</h2>
							<ul class="row">
								Carregando...
							</ul>
						</div>
					</div>
				  </div>';
		} 
	}











	/**
	 * 		EVENTOS
	 * 		Pega os eventos (posttype) relacionados ao lugar onde esta funcção é colocada
	 */


	function get_eventos($postID){


	    $data = date('D');
	    $mes = date('M');
	    $dia = date('d');
	    $ano = date('Y');
	 
	    $semana = array('Sun' => 'Domingo', 'Mon' => 'Segunda', 'Tue' => 'Terça', 'Wed' => 'Quarta', 'Thu' => 'Quinta', 'Fri' => 'Sexta', 'Sat' => 'Sábado');
	    $hoje =  $semana[$data];
	


		$type = 'eventos';
		$args = array('post_type' => $type, 'post_status' => 'publish', 'posts_per_page' => -1, 'caller_get_posts'=> 1);
		$my_query = null;
		$my_query = new WP_Query($args);



		if( $my_query->have_posts() ) {

			echo '<div class="panel-default panel">	
					<div class="panel-body">
						<div class="bloco" id="atividades_orientadas">
							<h2>Eventos<small> atividades e eventos que acontecem neste local</small></h2>';

							echo '<div class="row">';

						  	while ($my_query->have_posts()) : $my_query->the_post(); 

							    $eventoID = get_the_ID(); 	// pega o id do 'posttype' evento
							    $lugarID = get_post_meta( $eventoID, 'lugar_pai', true ); 	// pega o ID do lugar ao qual o evento pertence
							   
							   	$titulo = get_the_title();


							   	$dataArray = get_post_meta($eventoID, 'dias_da_semana', true);
						    
							    if(is_array($dataArray)){
							    	$data = get_post_meta($eventoID, 'dias_da_semana', true);
							    	ksort($data);
							    }
							    else{
							    	unset($data);
							    }


							    if($postID == $lugarID){

							    	echo '<div class="col-sm-3 evento-caixa" style="margin:15px 0 0 0">';

										    	echo '<div class="evento-caixa-titulo">'.$titulo.'</div>';
										    	

										    	foreach ($data as $dia) {
										    		
										    		if($dia['dia'] == $hoje){

														echo '<div class="evento-caixa-dia"><span>'.$dia['dia']. '</span> <span class="label label-success" style="font-size:50%">HOJE</span></div>';
													
													}

													else{

														echo '<div class="evento-caixa-dia">'.$dia['dia'].'</div>';
													}

													if($dia['horarios']['inicio'] != ''){
														echo '<div class="evento-caixa-horarios">'.$dia['horarios']['inicio'].' até '.$dia['horarios']['termino'].'<br/>
														'.$dia['segundohorarios']['inicio'].' até '.$dia['segundohorarios']['termino'].'</div>';
													}else{
														echo '<div class="evento-caixa-horarios">Dia todo</div>';
													}
													   
												}
									    		

								    		
					   				echo '</div>';
							    }
					  
						  	

							endwhile;

							echo '</div> 
						</div>
					</div>
				</div>';


		}
		
		wp_reset_query();  // Restore global post data stomped by the_post().
		
		
				
		
		
	}













	/**
	 * 		ATIVIDADES POSSÍVEIS
	 * 		Pega os as atividades da taxonomy
	 */

	function get_atividades_possiveis($postID){

		$atividades_possiveis = get_post_meta($postID, 'atividades_possiveis', true);

		if($atividades_possiveis != ''){ 
			echo '<div class="panel-default panel">	
					<div class="panel-body">
						<div class="bloco li-default" id="atividades_possiveis">
							<h2>Atividades possíveis <span id="adicionar-atividade" style="display:none" class="label-button label-default pull-right" data-toggle="modal" data-target="#atividades-possiveis-modal">+ Adicionar atividade</span></h2>';

							echo '<ul class="row">';

							foreach ($atividades_possiveis as $atividades) {
								
								$term = get_term_by('id', $atividades['id'], 'atividades-possiveis');
								
								$termEcho = $term->name;

								echo '<li class="col-sm-4" term-id="'.$term->term_id.'">

									<span class="atividade-name">' . $termEcho . '</span> '; 
									
									if($atividades['viram']['contador'] > 0){ 
										echo ' <span style="cursor:pointer" class="badge-rounded" rel="tooltip" title="'.pega_nome_usuarios_atividades($atividades['viram']['usuarios_id'], $termEcho, 'viram').'">'.$atividades['viram']['contador'].'</span>';
									} 
									else{
										echo ' <span style="cursor:pointer; display:none" class="badge-rounded badge-hide" rel="tooltip" title="'.pega_nome_usuarios_atividades($atividades['viram']['usuarios_id'], $termEcho, 'viram').'">-</span>';
									}
									
									if($atividades['praticam']['contador'] > 0){ 
										echo ' <span style="cursor:pointer; display:none" class="badge-square" rel="tooltip" title="'.pega_nome_usuarios_atividades($atividades['praticam']['usuarios_id'], $termEcho, 'praticam').'">'.$atividades['praticam']['contador'].'</span>'; 
									}
									else{
										echo ' <span style="cursor:pointer; display:none" class="badge-square" rel="tooltip" title="Nenhuma pessoa até agora indicou praticar esta atividade.">-</span>'; 
									}

								echo '</li>';
								
							}

							echo '</ul>
						</div>
					</div>
				</div>';
		} 

	}














	/**
	 * 		PEGA O NOME DOS USUÁRIOS QUE PRATICAM AS ATIVIDADES
	 */
	function pega_nome_usuarios_atividades($usuarios_id_array, $atividade, $viram_ou_praticam){


		// pega o numero de usuários na array
		$i = sizeof($usuarios_id_array);


		// pega as informações dos usuários
		foreach ($usuarios_id_array as $usuario_id) {
			$usuario_info = get_userdata($usuario_id);
		}


		// transforma o texto da atividade em lowercase
		strtolower($atividade);


		if($viram_ou_praticam == 'viram'){
			// monta a exibição em tooltip das pessoas que praticam a atividade
			if($i < 2){
				return $usuario_info->first_name.' já viu pessoas que praticam '.$atividade.' neste lugar.';
			}

			else{
				return $usuario_info->first_name.' já viram pessoas que praticam '.$atividade.' neste lugar.';
			}
		}

		elseif ($viram_ou_praticam == 'praticam') {
			// monta a exibição em tooltip das pessoas que praticam a atividade
			if($i < 2){
				return $usuario_info->first_name.' pratica '.$atividade.' neste lugar.';
			}

			else{
				return $usuario_info->first_name.' praticam '.$atividade.' neste lugar.';
			}
		}


	}





?>
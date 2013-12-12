<?php





	/**
	 * 		DESCRIÇÃO
	 * 		Pega a descrição do lugar
	 */

	function get_descricao($postID){
		
		$descricao = get_post_meta($postID, 'descricao', true);
		
		if ($descricao != ''){ 
			echo '<div class="bloco" id="descricao_do_lugar">
						<p>'.$descricao.'</p>
				  </div>';
		}
	}







	/**
	 * 		SERVIÇOS OFERECIDOS
	 * 		Pega os serviços marcados como oferecidos pelo lugar
	 */

	function get_servicos_oferecidos($postID){

		$servicos_oferecidos = get_post_meta($postID, 'servicos_oferecidos', true);
		
		if($servicos_oferecidos != ''){ 
			echo '<div class="bloco li-default" id="servicos_oferecidos">
					<h2>Serviços oferecidos<small style="cursor:help" rel="tooltip" title="Alguns dos serviços listados abaixo são oferecidos somente em alguns dias e horários da semana. Sempre ligue antes para confirmar os serviços e dias de atendimento." class="glyphicon glyphicon-exclamation-sign"></small></h2>
					<p>';
					echo '<ul class="row">';
					foreach ($servicos_oferecidos as $servico) {
						$term = get_term_by('id', $servico, 'serviços');
						$termEcho = $term->name;
						$termEcholen = strlen($termEcho); // pega o tamanho da string

						$termEchocut = substr($termEcho, 0, 30); // corta a string

						if($termEcholen > 30){
							echo '<li class="col-sm-4"><span style="cursor:help" rel="tooltip-border" title="'.$termEcho.'">'. $termEchocut .' ...</span></li>';
						}else{
							echo '<li class="col-sm-4"><span>' . $termEcho . '</span></li>';
						}
				
					}
					echo '</ul>
					</p>
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
		
		echo 	'<div class="bloco" id="tambem_sao_realizados">
			
						<h2>Também são realizados</h2>
						<p>'.$servicos_oferecidos_info.'</p>
					
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
		echo '<div class="bloco" id="como_ter_acesso">
					
						<h2>Como ter acesso</h2>
						<p>'.$acesso.'</p>
					
				</div>';
		}
	}






	/*
	 *  	DIAS DA SEMANA
	 *  	Pega os dias da semana e horários de funcionamento
	 */

	function get_dias_da_semana($postID){

		$dataArray = get_post_meta($postID, 'dias_da_semana', false);

	    if(is_array($dataArray)){
	    	$data = $dataArray[0];
	    	ksort($data);
	    }

		if($dataArray != ''){ 

		$observacoes = get_post_meta($postID, 'data_info', true);
		if($observacoes != ''){$col = '6';}else{$col = '3';}
		
		echo '<div class="bloco li-default" id="dias_de_funcionamento">
			
				<h2>Dias e horários de funcionamento</h2>
			
				<p>
				<div class="row">';
					if($observacoes != ''){echo '<div class="col-sm-6">';}else{echo '<div class="col-sm-12">';}
						echo '<ul class="row">';
							
								foreach ($data as $dia) {
									echo '<div class="col-sm-'.$col.'" style="padding:0 0 15px">
											<span class="badge" style="margin-bottom:12px">'.$dia['dia'].'</span>';
											
											if($dia['horarios']['inicio'] != ''){
												echo '<div>'.$dia['horarios']['inicio'].' às '.$dia['horarios']['termino'].'<br/>
												'.$dia['segundohorarios']['inicio'].' às '.$dia['segundohorarios']['termino'].'</div>';
											}else{
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
			echo '<div class="bloco" id="localizacao_no_mapa">
				<h2>Localização no mapa<small>'.$endereco.'</small></h2>
				<div id="map-canvas"> </div>
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
			echo '<div class="bloco li-default li-default-capitalize" id="onibus_que_passao_perto">
				<h2>Ônibus que passam perto</h2>
				<ul class="row">
					Carregando...
				</ul>
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
	    $hoje =  $semana["$data"];

		$type = 'eventos';
		$args = array('post_type' => $type, 'post_status' => 'publish', 'posts_per_page' => -1, 'caller_get_posts'=> 1);
		$my_query = null;
		$my_query = new WP_Query($args);



		if( $my_query->have_posts() ) {

			echo '<div class="bloco" id="atividades_orientadas">
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

				    	echo '<div class="col-sm-4" style="margin:15px 0 0 0">';

							    	echo '<b>'.$titulo.'</b><br/>';
							    	
							    	foreach ($data as $dia) {
							    		
							    		if($dia['dia'] == $hoje){
											echo $dia['dia']. ' <span class="badge" style="font-size: 50%; background: none repeat scroll 0% 0% green;">HOJE</span>';
										}else{
											echo $dia['dia'];
										}

										if($dia['horarios']['inicio'] != ''){
											echo '<div style="margin-bottom:12px;">'.$dia['horarios']['inicio'].' às '.$dia['horarios']['termino'].'<br/>
											'.$dia['segundohorarios']['inicio'].' às '.$dia['segundohorarios']['termino'].'</div>';
										}else{
											echo '<div>Dia todo</div>';
										}
										   
									}
						    		

					    		
		   				echo '</div>';
				    }
		  
			  	

				endwhile;

				echo '</div> 

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
			echo '<div class="bloco li-default" id="atividades_possiveis">
					<h2>Atividades possíveis</h2>
					<p>';
					echo '<ul class="row">';
					foreach ($atividades_possiveis as $atividades) {
						$term = get_term_by('id', $atividades, 'atividades-possiveis');
						$termEcho = $term->name;
						$termEcholen = strlen($termEcho); // pega o tamanho da string

						$termEchocut = substr($termEcho, 0, 30); // corta a string

						if($termEcholen > 30){
							echo '<li class="col-sm-4"><span style="cursor:help" rel="tooltip-border" title="'.$termEcho.'">'. $termEchocut .' ...</span></li>';
						}else{
							echo '<li class="col-sm-4"><span>' . $termEcho . '</span></li>';
						}
				
					}
					echo '</ul>
					</p>
			</div>';
		} 

	}









?>
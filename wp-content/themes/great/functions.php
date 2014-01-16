<?php




/*-----------------------------------------------------------------------------------*/
/*	Post Thumbnail Support
/*-----------------------------------------------------------------------------------*/
	if ( function_exists( 'add_theme_support' ) ) { 
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 300, 225, true );
	add_image_size( 'featured', 300, 225, true ); //featured
	add_image_size( 'image-lugar', 500, 1500 ); //Imagem para lugares
	add_image_size( 'related', 50, 50, true ); //related
	}




/*-----------------------------------------------------------------------------------*/
/*	Pega o preço para usar o serviço
/*-----------------------------------------------------------------------------------*/

function preco($valorID){
	$valorID = (int)$valorID;
	$valor = get_term($valorID, 'preço'); 
	echo $valor->name;
}



function pw_load_styles() {
	wp_enqueue_style('dashboard.css', 'http://127.0.0.1/projects/cidade.vc/css/dashboard.css');
	wp_enqueue_style('jquery-ui.css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css');
}

add_action('admin_enqueue_scripts', 'pw_load_styles');




if(is_admin()) {
	wp_enqueue_style('jquery-ui-custom', get_template_directory_uri().'/css/jquery-ui-custom.css');
	wp_enqueue_style('bootstrap', 'http://127.0.0.1/projects/cidade.vc/bootstrap/css/bootstrap.css');
}


function eventos_load_scripts($hook) {
	
	global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'eventos' === $post->post_type) {   
			wp_enqueue_script('jquery-1.10.2.min.js', 'http://code.jquery.com/jquery-1.10.2.min.js');
			wp_enqueue_script('jquery.ui.datepicker.min',  get_template_directory_uri().'/js/jquery.ui.datepicker.min.js');
			wp_enqueue_script('','https://maps.googleapis.com/maps/api/js?key=AIzaSyBiBbZGjRGFtFf4TpVs3CAip3iPBbvgrpU&sensor=true');
			wp_enqueue_script( 'gmap3.js', 'http://127.0.0.1/projects/cidade.vc/js/gmap3.js');
			wp_enqueue_script( 'map.js', 'http://127.0.0.1/projects/cidade.vc/js/map-dashboard-eventos.js');
		}
	}
}

add_action('admin_enqueue_scripts', 'eventos_load_scripts');
     





function add_custom_scripts() {

    global $custom_meta_fields, $post_type;

    if (($_GET['post_type'] == 'eventos') || ($post_type == 'eventos')) :

        $output = '<script type="text/javascript">'; 
        $output.= '$(function() {';  
        $output.= '';
              
	    foreach ($custom_meta_fields as $field) { 
	        if($field['type'] == 'date')  
	            $output .= "$('.datepicker').datepicker({dateFormat: 'dd/mm/yy', dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'], dayNamesMin: ['D','S','T','Q','Q','S','S','D'], dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'], monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'], monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'], nextText: 'Próximo', prevText: 'Anterior'});";
	             
  		}

		$output .= '}); 
		</script>';  
          
    echo $output;  

    endif;

}

add_action('admin_head','add_custom_scripts'); 





// Função para pegar Lat e Long para o mapa

add_action('wp_ajax_getlatlong', 'getlatlong');
add_action('wp_ajax_nopriv_getlatlong', 'getlatlong');

function getlatlong(){
	$waka = $_REQUEST['test'];
	$waka = json_encode($waka);
	echo $waka;
	die();
}



// Função para pegar os bounds do mapa - min e max lat long

add_action('wp_ajax_getbounds', 'getbounds');
add_action('wp_ajax_nopriv_getbounds', 'getbounds');

function getbounds(){
	$bounds = $_REQUEST['minmaxlatlong'];
	$bounds = urlencode($bounds);
	$data = json_decode(file_get_contents('http://www.poatransporte.com.br/php/facades/process.php?a=tp&p='.$bounds));

	// #####  Exibi as linhas próximas ao marcador via 'bounds' do 'circle'

	// Pega o JSON do poatransporte

	$arrayPronta = array(); //Array que criamos para pegar as linhas sem duplicação

	for ($i=0; $i < 20; $i++) { // pegamos no máximo 20 linhas
		
		$linhasArray = $data[$i]->linhas;
		$numerodeLinhas = sizeof($linhasArray);

		for ($iL=0; $iL < $numerodeLinhas; $iL++) { 
			$idLinha = $linhasArray[$iL]->idLinha;
			$codigoLinha = $linhasArray[$iL]->codigoLinha;

			$linha = $linhasArray[$iL]->nomeLinha;

			$arrayPronta[$iL] = array('linha' => $linha, 'codigo' => $codigoLinha, 'id' => $idLinha);
			

		}
	}




	$arrayProntaLen = sizeof($arrayPronta);

	for ($i=0; $i < 20; $i++) { 
		
		$linha = $arrayPronta[$i]['linha'];
		$codigo = $arrayPronta[$i]['codigo'];

		$linha = trim($linha); // Deixa os caracteres em minúsculo, depois o primeiro em maísculo e depois remove espaços em branco
		$valueLen = strlen($linha); // pega o tamanho da string
		
		if($valueLen > 25){
			$linhaCut = substr($linha, 0, 25); // corta a string
			echo "<li class='col-sm-6'><span class='badge'>".$codigo."</span><span rel='tooltip-border' style='cursor:help' title='".$linha."'> ".$linhaCut." ...</span></li>";
		}else{
			echo "<li class='col-sm-6'><span><span class='badge'>".$codigo."</span> ".$linha."</span></li>";
		}
	
	}


	die();
}













	add_action('wp_ajax_atividades_possiveis_votacao', 'atividades_possiveis_votacao');
	add_action('wp_ajax_nopriv_atividades_possiveis_votacao', 'atividades_possiveis_votacao');

	/**
	 * atividades_possiveis_votacao - Sistema de votação nas atividades possíveis listadas nos lugares
	 *
	 * Ao ser executada insere o voto na atividade e ao mesmo tempo insere a atividade (id) praticada na
	 * 'persona' do usuário. Com isto torna-se fácil o resgate das atividades praticadas por um usuário.
	 * 
	 * @return integer - retorna o número de votos
	 */
	function atividades_possiveis_votacao(){

		// Pega os valores enviados por ajax no front-end

		$user_id = $_REQUEST['user-id'];
		$lugar_id = $_REQUEST['post-id'];
		$atividade_id = $_REQUEST['term-id'];
		$viram_ou_praticam = $_REQUEST['viram-ou-praticam'];


		// Pega a array com as atividades do post

		$atividades_possiveis_array = get_post_meta($lugar_id, 'atividades_possiveis', true);

		$i = 0;

		foreach ($atividades_possiveis_array as $atividade) {
			
			if($atividade['id'] == $atividade_id){



				// pega a array com o id dos usuários que praticam as atividades
				$usuarios_id = $atividade[$viram_ou_praticam]['usuarios_id'];


				// se o usuário ainda não tiver registrado na array continua com a função
				if(!in_array($user_id, $usuarios_id)){


					// acrescenta ao contador
					$contador = $atividade[$viram_ou_praticam]['contador'] + 1;

		


					// Adiciona o id do usuário que clicou na lista de usuários que praticam a atividade
					array_push($usuarios_id, $user_id);

		


					// Salva as ids atualizadas na array das atividades
					$atividades_possiveis_array[$i][$viram_ou_praticam]['usuarios_id'] = $usuarios_id;



					// Salva o contador acrescido do click atual
					$atividades_possiveis_array[$i][$viram_ou_praticam]['contador'] = $contador;




					// Salva a nova array com as informações de contador e id do usuário
					update_post_meta($lugar_id, 'atividades_possiveis', $atividades_possiveis_array);



					### adiciona a atividade praticada na persona do usuário

					if($viram_ou_praticam == 'praticam'){

						// pega a array persona do banco de dados 
						$personaArray = get_user_meta($user_id, 'persona', true);
						if(is_array($personaArray)){
							$persona = $personaArray;
						}


						// adiciona a atividade praticada na array
						$persona['atividades_praticadas'][] = array('atividade_id' => $atividade_id, 'lugar_id' => $lugar_id);
						

						// atualiza a nova persona com a atividade pratica no banco de dados
						update_user_meta($user_id, 'persona', $persona);

					}


					// retorna o numero do contador atualizado
					echo $contador;

					exit();

				}

				else{

					
					// acrescenta ao contador
					$contador = $atividade[$viram_ou_praticam]['contador'] - 1;

		


					// Remove o id do usuário que clicou na lista de usuários que praticam a atividade
					if(($key = array_search($user_id, $usuarios_id)) !== false){
						unset($usuarios_id[$key]);
					}

		


					// Salva as ids atualizadas na array das atividades
					$atividades_possiveis_array[$i][$viram_ou_praticam]['usuarios_id'] = $usuarios_id;



					// Salva o contador acrescido do click atual
					$atividades_possiveis_array[$i][$viram_ou_praticam]['contador'] = $contador;




					// Salva a nova array com as informações de contador e id do usuário
					update_post_meta($lugar_id, 'atividades_possiveis', $atividades_possiveis_array);

					
					### Remove a ativida da persona do usuário

					if($viram_ou_praticam == 'praticam'){
						
						// pega a array persona do banco de dados 
						$personaArray = get_user_meta($user_id, 'persona', true);
						if(is_array($personaArray)){

							$persona = $personaArray;
							
						}


						// remove a atividade praticada na array
						foreach($persona['atividades_praticadas'] as $index => $atividade) {

					        if($atividade['atividade_id'] == $atividade_id){
					        
					        	unset($persona['atividades_praticadas'][$index]);
					        
					        }

					    }


						// atualiza a nova persona com a atividade pratica no banco de dados
						update_user_meta($user_id, 'persona', $persona);

					}

					// retorna o número do contador atualizado
					echo $contador;

					exit();

				}

			

			}


			$i++;

		}


		die();
	}












	/**
	 * 		ATIVIDADES POSSÍVEIS COMENTÁRIOS
	 */


	add_action('wp_ajax_atividades_possiveis_comentarios', 'atividades_possiveis_comentarios');
	add_action('wp_ajax_nopriv_atividades_possiveis_comentarios', 'atividades_possiveis_comentarios');

	function atividades_possiveis_comentarios(){


		# salva o novo comentário


		// pega a atividade enviada pelo 'jquery ajax'
		$nova_descricao = $_POST['nova-descricao'];
		$post_id = $_POST['post-id'];
		$user_id = $_POST['user-id'];
		$atividade_id = $_POST['atividade-id'];
		$data = date('Y-m-d H:i:s');

		if(strlen($nova_descricao) < 1500) {


			// cria a query que irá pegar as informações do banco de dados
			$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios' ";
			
			// executa a query
			$result = mysql_query($query);
			
			if($result){

				// cria uma array que armazenará o resultado do select acima
				$result_array = array();

				// monta a array com as informações baixadas do banco de dados
				while ($atividade = mysql_fetch_array($result)) {
					$result_array = unserialize($atividade[0]);
				}

			}

			else{

				die();
			
			}

			
			// Cria o bloco com a array pai quando ainda não houver nenhum registro 

			if(isset($result_array) === true AND empty($result_array) === true){

				// remove a possibilidade de enviar tags html
				$nova_descricao = htmlspecialchars($nova_descricao);
				$atividade_id = htmlspecialchars($atividade_id);

				// cria uma array que será usada para organizar as informações em moderação
				$conteudo_moderacao = array();

				// array com os conteúdos da atividade a se armazenar
				$conteudo_moderacao[$atividade_id][0] = array('usuario_id' => $user_id, 'atividade_id' => $atividade_id, 'data' => $data, 'votacao' => array('usuarios_id' => null, 'votos' => 0), 'descricao' => $nova_descricao) ;

				// transforma a array em string para ser possível salvar no banco de dados
				$conteudo_moderacao_array = serialize($conteudo_moderacao);

				// envia para o banco de dados na tabela 'wp_cocriacao'
				$envia = "INSERT INTO wp_cocriacao(lugar_id, usuario_id, bloco, conteudo, conteudo_moderacao) VALUES('$post_id', '$user_id', 'atividades_possiveis_comentarios', '$conteudo_array', '$conteudo_moderacao_array')";

				// envia a query para o banco de dados
				mysql_query($envia) or die();

			}

			// Se já existir algum comentario da atividade, adiciona os novos comentários na array já existente

			elseif($result_array[$atividade_id]) {

				// remove a possibilidade de enviar tags html
				$nova_descricao = htmlspecialchars($nova_descricao);
				$atividade_id = htmlspecialchars($atividade_id);

				// array com os conteúdos da atividade a se armazenar
				$conteudo_moderacao = array('usuario_id' => $user_id, 'atividade_id' => $atividade_id, 'data' => $data, 'votacao' => array('usuarios_id' => null, 'votos' => 0), 'descricao' => $nova_descricao) ;

				array_push($result_array[$atividade_id], $conteudo_moderacao);

				// serializa a array para poder ser arquivada no banco de dados
				$result_array = serialize($result_array);

				// envia para o banco de dados na tabela 'wp_cocriacao'
				$envia = "UPDATE wp_cocriacao SET conteudo_moderacao = '$result_array' WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios'";

				// envia a query para o banco de dados
				mysql_query($envia) or die();


			# pega os comentários do banco de dados para exibir o comentário recém adicionado

				// cria a query que irá pegar as informações do banco de dados
				$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios' ";
				
				// executa a query
				$result = mysql_query($query);
				
				if($result){

					// cria uma array que armazenará o resultado do select acima
					$result_array = array();

					// monta a array com as informações baixadas do banco de dados
					while ($atividade = mysql_fetch_array($result)) {
						$result_array = unserialize($atividade[0]);
					}

					// posiciona o ponteiro na última key da array
					end($result_array[$atividade_id]);

					// pega o valor da áulmia key
					$ultima_key = key($result_array[$atividade_id]);

					// seleciona somente o comentário atual na array (que é o último)
					$atividade = $result_array[$atividade_id][$ultima_key];

					// pega as informações do usuário
					$user_info = get_userdata($atividade['usuario_id']); 


					// envia para o front-end um item da <ul> com o comentário criado
					echo '<li id="li-show-divider" class="divider" style="display:none"></li>';

					echo '<li id="li-show-comentario" class="media" style="display:none">';

						echo '<div class="media-object pull-left">';
							echo get_avatar($atividade['usuario_id'], 48, null, '' );
						echo '</div>';

						echo '<div class="media-body">';

							echo '<span class="badge pull-right">'.$atividade['votacao']['votos'].'</span>';

							echo '<b>'.$user_info->first_name.'</b><br/>';

							echo $atividade['descricao'].'<br/>';

							echo '<div class="media-body-info">';
									
								echo 'há 2 segundos';
							
								if ($user_id == $atividade['usuario_id']){
									echo ' · <span class="atividades-possiveis-comentarios-excluir" key="'.$key.'" atividade-id="'.$atividade_id.'">Excluir</span>';
								}

							echo '</div>';

							

						echo '</div>';
					

					echo '</li>';


					// dá exit para evitar extravio de informações
					exit();


				}
				

			}

			// se já não existir nenhum comentário para a atividade, cria a array da atividade e adiciona o comentário atual

			else{

				// remove a possibilidade de enviar tags html
				$nova_descricao = htmlspecialchars($nova_descricao);
				$atividade_id = htmlspecialchars($atividade_id);

				// array com os conteúdos da atividade a se armazenar
				$conteudo_moderacao = array('usuario_id' => $user_id, 'atividade_id' => $atividade_id, 'data' => $data, 'votacao' => array('usuarios_id' => null, 'votos' => 0), 'descricao' => $nova_descricao) ;

				// cria a array que abrigará as atividades
				$result_array[$atividade_id] = array();

				// adiciona o novo comentario ao final da array da atividade juntos aos outro comentários
				array_push($result_array[$atividade_id], $conteudo_moderacao);

				// serializa a array para poder ser arquivada no banco de dados
				$result_array = serialize($result_array);

				// envia para o banco de dados na tabela 'wp_cocriacao'
				$envia = "UPDATE wp_cocriacao SET conteudo_moderacao = '$result_array' WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios'";

				// envia a query para o banco de dados
				mysql_query($envia) or die();

				

			# pega os comentários do banco de dados para exibir o comentário recém adicionado

				// cria a query que irá pegar as informações do banco de dados
				$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios' ";
				
				// executa a query
				$result = mysql_query($query);
				
				if($result){

					// cria uma array que armazenará o resultado do select acima
					$result_array = array();

					// monta a array com as informações baixadas do banco de dados
					while ($atividade = mysql_fetch_array($result)) {
						$result_array = unserialize($atividade[0]);
					}

					// posiciona o ponteiro na última key da array
					end($result_array[$atividade_id]);

					// pega o valor da áulmia key
					$ultima_key = key($result_array[$atividade_id]);

					// seleciona somente o comentário atual na array (que é o último)
					$atividade = $result_array[$atividade_id][$ultima_key];

					// pega as informações do usuário
					$user_info = get_userdata($atividade['usuario_id']); 


					// envia para o front-end um item da <ul> com o comentário criado
					echo '<li id="li-show-divider" class="divider" style="display:none"></li>';

					echo '<li id="li-show-comentario" class="media" style="display:none">';

						echo '<div class="media-object pull-left">';
							echo get_avatar($atividade['usuario_id'], 48, null, '' );
						echo '</div>';

						echo '<div class="media-body">';

							echo '<span class="badge pull-right">'.$atividade['votacao']['votos'].'</span>';

							echo '<b>'.$user_info->first_name.'</b><br/>';

							echo $atividade['descricao'].'<br/>';

							echo '<div class="media-body-info">';
									
								echo 'há 2 segundos';
							
								if ($user_id == $atividade['usuario_id']){
									echo ' · <span class="atividades-possiveis-comentarios-excluir" key="'.$key.'" atividade-id="'.$atividade_id.'">Excluir</span>';
								}

							echo '</div>';

							

						echo '</div>';
					

					echo '</li>';


					// dá exit para evitar extravio de informações
					exit();


				}
				



			}

		}

		exit();

	}




	add_action('wp_ajax_atividades_possiveis_comentarios_db', 'atividades_possiveis_comentarios_db');
	add_action('wp_ajax_nopriv_atividades_possiveis_comentarios_db', 'atividades_possiveis_comentarios_db');

	/**
	 * Pega os comentários para colocar no modal das atividades
	 * @return string - retorna uma lista de comentários dentro de uma <ul>
	 */
	function atividades_possiveis_comentarios_db(){

		// pega a atividade enviada pelo 'jquery ajax'
		$post_id = $_POST['post-id'];
		$atividade_id = $_POST['atividade-id'];
		$user_id = $_POST['user-id'];


		
		// cria a query que irá pegar as informações do banco de dados
		$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios' ";
		
		// executa a query
		$result = mysql_query($query);
		
		if($result){

			// cria uma array que armazenará o resultado do select acima
			$result_array = array();

			// monta a array com as informações baixadas do banco de dados
			while ($atividade = mysql_fetch_array($result)) {
				$result_array = unserialize($atividade[0]);
			}


			// verifica se há algum comentário para a atividade
			if(is_array($result_array[$atividade_id])){

				echo '<ul class="media-list">';

				foreach ($result_array[$atividade_id] as $key => $atividade) {

					// pega as informações do usuario do comentario
					$user_info = get_userdata($atividade['usuario_id']); 
					
					echo '<li class="divider"></li>';

					echo '<li class="media">';

						echo '<div class="media-object pull-left">';
							echo get_avatar($atividade['usuario_id'], 48, null, '' );
						echo '</div>';

						echo '<div class="media-body">';

							echo '<span class="badge pull-right">'.$atividade['votacao']['votos'].'</span>';

							echo '<b>'.$user_info->first_name.'</b><br/>';

							echo $atividade['descricao'].'<br/>';

							echo '<div class="media-body-info">';
									
								echo timeAgo(strtotime($atividade['data']));
							
								if ($user_id == $atividade['usuario_id']){
									echo ' · <span class="atividades-possiveis-comentarios-excluir" key="'.$key.'" atividade-id="'.$atividade_id.'">Excluir</span>';
								}

							echo '</div>';

							

						echo '</div>';
					

					echo '</li>';



				}

				echo '</ul>';

				exit();

			}

			else{

				echo '<ul>
						<li id="li-nenhum">
							Nenhum comentário até o momento. Seja o primeo a comentar sobre esta atividade. =)
						</li>
					  </ul>';

				exit();

			}

			

		}

		else{

			die('Invalid query: ' . mysql_error());
		
		}

	}



	add_action('wp_ajax_atividades_possiveis_comentarios_excluir', 'atividades_possiveis_comentarios_excluir');
	add_action('wp_ajax_nopriv_atividades_possiveis_comentarios_excluir', 'atividades_possiveis_comentarios_excluir');

	function atividades_possiveis_comentarios_excluir(){


		// Pega os valores enviados por ajax no front-end

		$user_id = $_REQUEST['user-id'];
		$post_id = $_REQUEST['post-id'];
		$atividade_id = $_REQUEST['atividade-id'];
		$key = $_REQUEST['key'];



		// cria a query que irá pegar as informações do banco de dados
		$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios' ";
		
		// executa a query
		$result = mysql_query($query);
		
		if($result){

			// cria uma array que armazenará o resultado do select acima
			$result_array = array();

			// monta a array com as informações baixadas do banco de dados
			while ($atividade = mysql_fetch_array($result)) {
				$result_array = unserialize($atividade[0]);
			}

		


		
			if(in_array($user_id, $result_array[$atividade_id][$key])){

				// remove o comnetário da array
				unset($result_array[$atividade_id][$key]);

				$result_array = serialize($result_array);

				// envia para o banco de dados na tabela 'wp_cocriacao'
				$envia = "UPDATE wp_cocriacao SET conteudo_moderacao = '$result_array' WHERE lugar_id = $post_id AND bloco = 'atividades_possiveis_comentarios'";

				// envia a query para o banco de dados
				mysql_query($envia) or die();

				exit();

			}

		
		}

		else{

			die();
		
		}


	}






	
	/**
	 * timeAgo - função que humaniza a data de postagem de algo
	 * @param  string $time - deve ser a data no formato 'datetime'
	 * @return string   
	 */
	function timeAgo($time){

    	$time = time() - $time; // to get the time since that moment

	    $tokens = array (
	        31536000 => 'ano',
	        2592000 => 'mês',
	        604800 => 'semana',
	        86400 => 'dia',
	        3600 => 'hora',
	        60 => 'minuto',
	        1 => 'segundo'
	    );

	    foreach ($tokens as $unit => $text) {
	        if ($time < $unit) continue;
	        $numberOfUnits = floor($time / $unit);
	        return 'há ' .$numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
	    }

	}








	// Deixa a url do 'site' limpa
	function NewUrl($x) {
	   $url = $x;
	   if ( substr($url, 0, 7) == 'http://') { $url = substr($url, 7); }
	   if ( substr($url, 0, 8) == 'https://') { $url = substr($url, 8); }
	   if ( substr($url, 0, 4) == 'www.') { $url = substr($url, 4); }
	   if ( strpos($url, '/') !== false) {
	      $ex = explode('/', $url);
	      $url = $ex['0'];
	   }

	      return $url;
	}






/*-----------------------------------------------------------------------------------*/
/*	Filters customize wp_title
/*-----------------------------------------------------------------------------------*/
// Filter the page title wp_title() in header.php
	if ( ! function_exists('mythemeshop_page_title' ) ) {
		function mythemeshop_page_title( $title ) { 
			$the_page_title = $title;
			if( ! $the_page_title ){
				$the_page_title = get_bloginfo("name");
			}else{
				$the_page_title = $the_page_title;
			}
			return $the_page_title;
		} 
		add_filter('wp_title', 'mythemeshop_page_title');
	}




// Função para deixar datas iguais a do Facebook

function time_ago( $type = 'post' ) {
	$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
	return human_time_diff($d('U'), current_time('timestamp')) . " " . "atrás";
}



// Função que pega a 'key' a partir do nome dos bairros (usado para pegar horarios nas páginas de evento)

function keyBairro($bairro){
	switch ($bairro) {
		case 'Domingo':
			return 0;
		break;
	
		case 'Segunda':
			return 1;
		break;

		case 'Terça':
			return 2;
		break;

		case 'Quarta':
			return '3';
		break;

		case 'Quinta':
			return '4';
		break;

		case 'Sexta':
			return 5;
		break;

		case 'Sábado':
			return 6;
		break;

	}
}





// verifica se há algo em arrays multi nível

function in_multiarray($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_multiarray($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}



	# UPLOAD DE IMAGEM NO FRONTEND

	function insert_attachment($file_handler,$post_id,$setthumb='true') {
	 
		// check to make sure its a successful upload
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
		 
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		 
		$attach_id = media_handle_upload( $file_handler, $post_id );
		 
		if ($setthumb) update_post_meta($post_id,'imagem_capa',$attach_id);
		return $attach_id;
	}













	# PERMALINK EDITAR POST SAÚDE

	function get_link_editar($post_id){

		if(current_user_can('manage_options')){

			global $post;

			$categoria = wp_get_object_terms($post_id, 'category');

			if($categoria[0]->slug == 'lazer' OR $categoria[1]->slug == 'lazer'){
				$pagina_editar = get_permalink(154); // id da página de edição do lugar
				echo '<a class="link-editar-lugar" href="'.$pagina_editar.'?post='.$post_id.'">Editar lugar</a>';
			}

			if($categoria[0]->slug == 'saude' OR $categoria[1]->slug == 'saude'){
				$pagina_editar = get_permalink(128); // id da página de edição do lugar
				echo '<a class="link-editar-lugar" href="'.$pagina_editar.'?post='.$post_id.'">Editar lugar</a>';
			}

		}
	}
















/*-----------------------------------------------------------------------------------*/
/* removes detailed login error information for security
/*-----------------------------------------------------------------------------------*/
	add_filter('login_errors',create_function('$a', "return null;"));
	
/*-----------------------------------------------------------------------------------*/
/* removes the WordPress version from your header for security
/*-----------------------------------------------------------------------------------*/
	function wb_remove_version() {
		return '<!--Theme by MyThemeShop.com-->';
	}
	add_filter('the_generator', 'wb_remove_version');
	
/*-----------------------------------------------------------------------------------*/
/* Removes Trackbacks from the comment count
/*-----------------------------------------------------------------------------------*/
	add_filter('get_comments_number', 'comment_count', 0);
	function comment_count( $count ) {
		if ( ! is_admin() ) {
			global $id;
			$comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
			return count($comments_by_type['comment']);
		} else {
			return $count;
		}
	}
	
/*-----------------------------------------------------------------------------------*/
/* category id in body and post class
/*-----------------------------------------------------------------------------------*/
	function category_id_class($classes) {
		global $post;
		foreach((get_the_category($post->ID)) as $category)
			$classes [] = 'cat-' . $category->cat_ID . '-id';
			return $classes;
	}
	add_filter('post_class', 'category_id_class');
	add_filter('body_class', 'category_id_class');

/*-----------------------------------------------------------------------------------*/
/* adds a class to the post if there is a thumbnail
/*-----------------------------------------------------------------------------------*/
	function has_thumb_class($classes) {
		global $post;
		if( has_post_thumbnail($post->ID) ) { $classes[] = 'has_thumb'; }
			return $classes;
	}
	add_filter('post_class', 'has_thumb_class');


/*-----------------------------------------------------------------------------------*/	
/* Pagination
/*-----------------------------------------------------------------------------------*/
function pagination($pages = '', $range = 3)
{ $showitems = ($range * 3)+1;
 global $paged; if(empty($paged)) $paged = 1;
 if($pages == '') {
 global $wp_query; $pages = $wp_query->max_num_pages; if(!$pages)
 { $pages = 1; } }
 if(1 != $pages)
 { echo "<div class='pagination'><ul>";
 if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link(1)."'>&laquo; First</a></li>";
 if($paged > 1 && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link($paged - 1)."' class='inactive'>&lsaquo; Previous</a></li>";
 for ($i=1; $i <= $pages; $i++)
 { if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
 { echo ($paged == $i)? "<li class='current'><span class='currenttext'>".$i."</span></li>":"<li><a rel='nofollow' href='".get_pagenum_link($i)."' class='inactive'>".$i."</a></li>";
 } } if ($paged < $pages && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link($paged + 1)."' class='inactive'>Next &rsaquo;</a></li>";
 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a rel='nofollow' class='inactive' href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
 echo "</ul></div>"; }}

?>
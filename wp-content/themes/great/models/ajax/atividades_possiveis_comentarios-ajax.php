<?php
	
	/*****************************************************************************
	*
	*	SALVA O NOVO COMENTÁRIO DAS ATIVIDADES POSSÍVEIS
	*
	*	Este ajax executa o cadastramento no banco de dados dos comentários enviados 
	*	nas atividades e sua exibição em tempo real após o sucesso do envio.
	*
	* 	Origem da requisição: single.php
	*
	**************************/

	define('SHORTINIT', true);
	require '../../../../../wp-load.php';
	require( ABSPATH . WPINC . '/formatting.php' );
	require( ABSPATH . WPINC . '/meta.php' );
	require( ABSPATH . WPINC . '/post.php' );
	require( ABSPATH . WPINC . '/pluggable.php');
	require( ABSPATH . WPINC . '/capabilities.php');
	require( ABSPATH . WPINC . '/user.php');
	require( ABSPATH . WPINC . '/kses.php');
	wp_plugin_directory_constants();

	session_start();

	/** SEGURANÇA NIVEL 1 
	*************************/

	// verifica se a requisição está sendo via ajax - evita requisições diretas

	if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){

		// verifica se o token de segurança criado no backend (single.php) corresponde ao da sessão do usuário - evita a alteração de dados no caminho da execução e sua repetição

		if($_REQUEST['token_security'] == $_SESSION['token_security']){

			// remove o token de segurança da sessão para evitar replay da solicitação ajax
			unset($_REQUEST['token_security']);

			// pega a atividade enviada pelo 'jquery ajax'
			$nova_descricao = $_POST['nova-descricao'];
			$lugar_id = $_POST['post-id'];
			$user_id = $_POST['user-id'];
			$atividade_id = $_POST['atividade-id'];
			$data = date('Y-m-d H:i:s');


			// remove a possibilidade de se interpretar tags html
			$nova_descricao = mysql_real_escape_string(strip_tags($nova_descricao));
			$atividade_id = mysql_real_escape_string(strip_tags($atividade_id));
			$data = mysql_real_escape_string(strip_tags($data));


			/** SEGURANÇA NIVEL 2 
			*************************/

			// verifica o tamanho de todas as variáveis recebidas via ajax

			if(strlen($nova_descricao) < 1500 AND strlen($lugar_id) < 9 AND strlen($user_id) < 5 AND strlen($atividade_id) < 9 AND strlen($data) < 24) {


				/** SEGURANÇA NIVEL 3
				***************************/

				// verifica o 'type' das variáveis enviadas via ajax

				if(is_numeric($lugar_id) AND is_numeric($user_id) AND is_numeric($atividade_id)){


					// cria a query que irá pegar as informações do banco de dados
					$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $lugar_id AND bloco = 'atividades_possiveis_comentarios' ";
					
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

						// cria uma array que será usada para organizar as informações em moderação
						$conteudo_moderacao = array();

						// array com os conteúdos da atividade a se armazenar
						$conteudo_moderacao[$atividade_id][0] = array('usuario_id' => $user_id, 'atividade_id' => $atividade_id, 'data' => $data, 'votacao' => array('usuarios_id' => null, 'votos' => 0), 'descricao' => $nova_descricao) ;

						// transforma a array em string para ser possível salvar no banco de dados
						$conteudo_moderacao_array = serialize($conteudo_moderacao);

						// envia para o banco de dados na tabela 'wp_cocriacao'
						$envia = "INSERT INTO wp_cocriacao(lugar_id, usuario_id, bloco, conteudo, conteudo_moderacao) VALUES('$lugar_id', '$user_id', 'atividades_possiveis_comentarios', '$conteudo_array', '$conteudo_moderacao_array')";

						// envia a query para o banco de dados
						mysql_query($envia) or die();



						# PEGA OS COMENTÁRIOS DO BANCO DE DADOS PARA EXIBIR O COMENTÁRIO RECÉM ADICIONADO

						// cria a query que irá pegar as informações do banco de dados
						$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $lugar_id AND bloco = 'atividades_possiveis_comentarios' ";
						
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

					// SE JÁ EXISTIR ALGUM COMENTARIO DA ATIVIDADE, ADICIONA OS NOVOS COMENTÁRIOS NA ARRAY JÁ EXISTENTE

					elseif($result_array[$atividade_id]) {



						// array com os conteúdos da atividade a se armazenar
						$conteudo_moderacao = array('usuario_id' => $user_id, 'atividade_id' => $atividade_id, 'data' => $data, 'votacao' => array('usuarios_id' => null, 'votos' => 0), 'descricao' => $nova_descricao) ;

						array_push($result_array[$atividade_id], $conteudo_moderacao);

						// serializa a array para poder ser arquivada no banco de dados
						$result_array = serialize($result_array);

						// envia para o banco de dados na tabela 'wp_cocriacao'
						$envia = "UPDATE wp_cocriacao SET conteudo_moderacao = '$result_array' WHERE lugar_id = $lugar_id AND bloco = 'atividades_possiveis_comentarios'";

						// envia a query para o banco de dados
						mysql_query($envia) or die();


						
						# PEGA OS COMENTÁRIOS DO BANCO DE DADOS PARA EXIBIR O COMENTÁRIO RECÉM ADICIONADO

						// cria a query que irá pegar as informações do banco de dados
						$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $lugar_id AND bloco = 'atividades_possiveis_comentarios' ";
						
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


						// array com os conteúdos da atividade a se armazenar
						$conteudo_moderacao = array('usuario_id' => $user_id, 'atividade_id' => $atividade_id, 'data' => $data, 'votacao' => array('usuarios_id' => null, 'votos' => 0), 'descricao' => $nova_descricao) ;

						// cria a array que abrigará as atividades
						$result_array[$atividade_id] = array();

						// adiciona o novo comentario ao final da array da atividade juntos aos outro comentários
						array_push($result_array[$atividade_id], $conteudo_moderacao);

						// serializa a array para poder ser arquivada no banco de dados
						$result_array = serialize($result_array);

						// envia para o banco de dados na tabela 'wp_cocriacao'
						$envia = "UPDATE wp_cocriacao SET conteudo_moderacao = '$result_array' WHERE lugar_id = $lugar_id AND bloco = 'atividades_possiveis_comentarios'";

						// envia a query para o banco de dados
						mysql_query($envia) or die();

						

						# pega os comentários do banco de dados para exibir o comentário recém adicionado

						// cria a query que irá pegar as informações do banco de dados
						$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = $lugar_id AND bloco = 'atividades_possiveis_comentarios' ";
						
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

					// cria um token de segurança caso ainda não exista
					if(!isset($_SESSION['token_security'])){

						// gera um 'token' aleatoriamente e armazena na sessão do usuário
					  	$_SESSION['token_security'] = md5(uniqid(rand(), true)); 

					}

				}

			}

			exit();

	
		}

	}






?>
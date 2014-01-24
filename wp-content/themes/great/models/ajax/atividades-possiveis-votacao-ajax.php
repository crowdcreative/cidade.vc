<?php
	
	/*****************************************************
	*
	* 	SISTEMA DE VOTAÇÃO NAS 'ATIVIDADES POSSÍVEIS' LISTADAS NOS LUGARES
	*
	* 	Ao ser executada insere o voto na atividade e ao mesmo tempo insere a atividade (id) praticada na
	*  'persona' do usuário. Com isto torna-se fácil o resgate das atividades praticadas por um usuário.
	* 
	* 	@return integer - retorna o número de votos
	* 
	*************************************/


	define('SHORTINIT', true);
	require '../../../../../wp-load.php';



	/** SEGURANÇA NIVEL 1 
	*************************/

	// verifica se a requisição está sendo via ajax - evita requisições diretas

	if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){

		

		// Pega os valores enviados por ajax no front-end

		$user_id = $_REQUEST['user-id'];
		$lugar_id = $_REQUEST['post-id'];
		$atividade_id = $_REQUEST['atividade-id'];
		$viram_ou_praticam = $_REQUEST['viram-ou-praticam'];


		/** SEGURANÇA NIVEL 2
		********************************/

		//Verifica o tamanho da variáveis enviadas por ajax
		if(strlen($user_id) < 7 AND strlen($lugar_id) < 6 AND strlen($atividade_id) < 5 AND strlen($viram_ou_praticam) < 10){


			/** SEGURANÇA NIVEL 3
			********************************/		

			// verifica os tipos das variáveis
			if(is_numeric($user_id) AND is_numeric($lugar_id) AND is_numeric($atividade_id) AND is_string($viram_ou_praticam)){


				// Pega a array com as atividades do post
				$query = "SELECT meta_value FROM wp_postmeta WHERE meta_key = 'atividades_possiveis' AND post_id = $lugar_id";

				$result = mysql_query($query);

				if($result){

					$atividades_possiveis_array = mysql_fetch_array($result);

					$atividades_possiveis_array = unserialize($atividades_possiveis_array[0]);


				}

				else{

					echo 'erro';

					exit();
				}

				$i = 0;

				foreach ($atividades_possiveis_array as $atividade) {
					
					if($atividade['id'] == $atividade_id){


						// criação da array de resposta
						$resposta = array();


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


							// transforma a array em string para ser possível salvar no banco de dados
							$atividades_possiveis_array = serialize($atividades_possiveis_array);

							// Salva a nova array com as informações de contador e id do usuário
							$envia = "UPDATE wp_postmeta SET meta_value = '$atividades_possiveis_array' WHERE meta_key = 'atividades_possiveis' AND post_id = $lugar_id";

							// envia a query para o banco de dados
							mysql_query($envia) or die('erro');

							### adiciona a atividade praticada na perra do usuário

							if($viram_ou_praticam == 'praticam'){

								$query = "SELECT meta_value FROM wp_usermeta WHERE meta_key = 'persona' AND user_id = $user_id";

								$result = mysql_query($query);

								if($result){

									$personaArray = mysql_fetch_array($result);

									$personaArray = unserialize($personaArray[0]);

								}

								else{

									exit();
								}

								if(is_array($personaArray)){
									$persona = $personaArray;
								}


								// adiciona a atividade praticada na array
								$persona['atividades_praticadas'][] = array('atividade_id' => $atividade_id, 'lugar_id' => $lugar_id);
								
								$persona = serialize($persona);

								// atualiza a nova persona com a atividade pratica no banco de dados
								$envia = "UPDATE wp_usermeta SET meta_value = '$persona' WHERE meta_key = 'persona' AND user_id = $user_id";

								// envia a query para o banco de dados
								mysql_query($envia) or die('erro');

							}


							// retorna o numero do contador atualizado
							$resposta['contador'] = $contador;

							// retorna 1 se o usuário agora pratica ou já viu alguém praticando a atividade
							$resposta['gravado'] = 1;

							echo json_encode($resposta);


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

							// transformando a array em string para podermos salvar no banco de dados
							$atividades_possiveis_array = serialize($atividades_possiveis_array);


							// Salva a nova array com as informações de contador e id do usuário
							$envia = "UPDATE wp_postmeta SET meta_value = '$atividades_possiveis_array' WHERE meta_key = 'atividades_possiveis' AND post_id = $lugar_id";

							// envia a query para o banco de dados
							mysql_query($envia) or die('erro');


							
							### Remove a ativida da persona do usuário

							if($viram_ou_praticam == 'praticam'){
								
								
								$query = "SELECT meta_value FROM wp_usermeta WHERE meta_key = 'persona' AND user_id = $user_id";

								$result = mysql_query($query);

								if($result){

									$personaArray = mysql_fetch_array($result);

									$personaArray = unserialize($personaArray[0]);

								}

								else{

									exit();
								}


								if(is_array($personaArray)){

									$persona = $personaArray;
									
								}


								// remove a atividade praticada na array
								foreach($persona['atividades_praticadas'] as $index => $atividade) {

							        if($atividade['atividade_id'] == $atividade_id){
							        
							        	unset($persona['atividades_praticadas'][$index]);
							        
							        }

							    }

							    $persona = serialize($persona);
								
								// atualiza a nova persona com a atividade pratica no banco de dados
								$envia = "UPDATE wp_usermeta SET meta_value = '$persona' WHERE meta_key = 'persona' AND user_id = $user_id";

								// envia a query para o banco de dados
								mysql_query($envia) or die('erro');

							}

							// retorna o numero do contador atualizado
							$resposta['contador'] = $contador;

							// retorna 1 se o usuário agora pratica ou já viu alguém praticando a atividade
							$resposta['gravado'] = 0;

							echo json_encode($resposta);

							exit();

						}

					

					}


					$i++;

				}


				die();

			}

			exit('Erro na verificação dos tipos.');
		
		
		}

		else{

			exit('Erro no tamanho das variáveis.');

		}


	}






?>
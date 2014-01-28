<?php
	

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


	$user_id = $_REQUEST['user-id'];
	$lugar_id = $_REQUEST['post-id'];
	$atividade_id = $_REQUEST['atividade-id'];

	if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){


		// cria a array que será usada como resposta
		$resposta = array();


		/**
		 * 	Pega o número de pessoas que praticam e viram no momento do clique
		 */

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


		foreach ($atividades_possiveis_array as $atividade) {
					
			if($atividade['id'] == $atividade_id){

				// pega o contador de quantas pessoas viram
				$resposta['contadorViram'] = $atividade['viram']['contador'];

				// pega o contador de quantas pessoas viram
				$resposta['contadorPraticam'] = $atividade['praticam']['contador'];

			}

		}	



		// Pega a array com as atividades do post
		$atividades_possiveis_array = get_post_meta($lugar_id, 'atividades_possiveis', true);


		foreach ($atividades_possiveis_array as $atividade) {
			
			if($atividade['id'] == $atividade_id){

				


				// pega a array com o id dos usuários que praticam as atividades
				$usuarios_id = $atividade['praticam']['usuarios_id'];

				// se o usuário ainda não tiver registrado na array continua com a função
				if(in_array($user_id, $usuarios_id)){

					$resposta['praticam'] = 1;

				}

				else{

					$resposta['praticam'] = 0;

				}



				// destoi variável para não haver conflito
				unset($usuarios_id);



				// pega a array com o id dos usuários que viram as pessoas praticando as atividades
				$usuarios_id = $atividade['viram']['usuarios_id'];


				// se o usuário ainda não tiver registrado na array continua com a função
				if(in_array($user_id, $usuarios_id)){

					$resposta['viram'] = 1;

				}

				else{

					$resposta['viram'] = 0;

				}


				echo json_encode($resposta);

				exit();

			}

		}

		exit();

	}

	else{

		exit();

	}

	

	exit();









?>
<?php
	

	define('SHORTINIT', true);
	require '../../../../../wp-load.php';
	require( ABSPATH . WPINC . '/formatting.php' );
	require( ABSPATH . WPINC . '/meta.php' );
	require( ABSPATH . WPINC . '/post.php' );
	wp_plugin_directory_constants();



	$user_id = $_REQUEST['user-id'];
	$lugar_id = $_REQUEST['post-id'];
	$atividade_id = $_REQUEST['atividade-id'];


	// Pega a array com as atividades do post
	$atividades_possiveis_array = get_post_meta($lugar_id, 'atividades_possiveis', true);


	foreach ($atividades_possiveis_array as $atividade) {
		
		if($atividade['id'] == $atividade_id){



			// pega a array com o id dos usuários que praticam as atividades
			$usuarios_id = $atividade['praticam']['usuarios_id'];


			// se o usuário ainda não tiver registrado na array continua com a função
			if(in_array($user_id, $usuarios_id)){

				echo '1';

				exit();

			}

		}

	}

	exit();









?>
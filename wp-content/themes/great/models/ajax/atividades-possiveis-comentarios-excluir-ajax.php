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
	
	session_start();



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


	
	exit();








?>
<?php
	
	session_start();

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


	/**
	 * Pega os comentários para colocar no modal das atividades
	 * @return string - retorna uma lista de comentários dentro de uma <ul>
	 */

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
						
							if($_SESSION['logged'] === true){

								if ($user_id == $atividade['usuario_id']){
									echo ' · <span class="atividades-possiveis-comentarios-excluir" key="'.$key.'" atividade-id="'.$atividade_id.'">Excluir</span>';
								}

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

	
	exit();








?>
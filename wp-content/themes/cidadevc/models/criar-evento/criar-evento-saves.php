<?php

	



	if($post_id){


				
		# salva a CATEGORIA 

		$subcategoria = $_POST['categoria'];  
		wp_set_object_terms( $post_id, $subcategoria, 'category' );
		





		# salva a DESCRIÇÃO

		$old = get_post_meta($post_id, 'descricao', true);
		$new = $_POST['descricao'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'descricao', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'descricao', $old);
		}







		# salva o BAIRRO

		$bairro = $_POST['bairro'];  
		wp_set_object_terms( $post_id, $bairro, 'bairros' );






		# salva o ENDEREÇO

		$old = get_post_meta($post_id, 'endereco', true);
		$new = $_POST['endereco'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'endereco', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'endereco', $old);
		}





		# salva a LATITUDE E LONGITUDE

		$old = get_post_meta($post_id, 'latlong', true);
		$new = $_POST['latlong'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'latlong', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'latlong', $old);
		}




		# salva o LUGAR PAI

		$old = get_post_meta($post_id, 'lugar_pai', true);
		$new = $_POST['lugar_pai'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'lugar_pai', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'lugar_pai', $old);
		}





		# salva o LUGAR PAI NOME

		$old = get_post_meta($post_id, 'lugar_pai_nome', true);
		$new = $_POST['lugar_pai_nome'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'lugar_pai_nome', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'lugar_pai_nome', $old);
		}






		# salva os SERVIÇOS OFERECIDOS
		
		$atividadesSelecionadas = $_POST['atividades_possiveis']; // Pega os dias marcados da semana que o evento acontece

		if(isset($atividadesSelecionadas)){
			sort($atividadesSelecionadas); // Coloca os dias em ordem
		}
		
		$old = get_post_meta($post_id, 'atividades_possiveis', true); 


		if ($atividadesSelecionadas && $atividadesSelecionadas != $old) {
			update_post_meta($post_id, 'atividades_possiveis', $atividadesSelecionadas);
		} elseif ('' == $atividadesSelecionadas && $old) {
			delete_post_meta($post_id, 'atividades_possiveis', $old);
		}






		# salva o SERVIÇOS OFERECIDOS INFO

		$old = get_post_meta($post_id, 'servicos_oferecidos_info', true);
		$new = $_POST['servicos_oferecidos_info'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'servicos_oferecidos_info', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'servicos_oferecidos_info', $old);
		}





		# salva o COMO TER ACESSO

		$old = get_post_meta($post_id, 'acesso', true);
		$new = $_POST['acesso'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'acesso', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'acesso', $old);
		}





		# salva o PREÇO

		$preco = $_POST['preco'];  
		wp_set_object_terms( $post_id, $preco, 'preço' );






		# salva a DATA ESPECÍFICA

		$inicio_dia = $_POST['inicio_dia'];
		$inicio_hora = $_POST['inicio_hora'];
		$termino_dia = $_POST['termino_dia'];
		$termino_hora = $_POST['termino_hora'];

		$dataEspecifica = array('inicio_dia' => $inicio_dia, 'inicio_hora' => $inicio_hora, 'termino_dia' => $termino_dia, 'termino_hora' => $termino_hora);

		$old = get_post_meta($post_id, 'data_especifica', true);

		if ($dataEspecifica && $dataEspecifica != $old) {
			update_post_meta($post_id, 'data_especifica', $dataEspecifica);
		} elseif ('' == $dataEspecifica && $old) {
			delete_post_meta($post_id, 'data_especifica', $old);
		}

		





		# salva os DIAS DA SEMANA
		
		require 'requires/salva-dias-da-semana.php';






		# salva as INFORMAÇÕES SOBRE DATAS 

		$old = get_post_meta($post_id, 'data_info', true);
		$new = $_POST['data_info'];
		if ($new && $new != $old) {
			update_post_meta($post_id, 'data_info', $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, 'data_info', $old);
		}






		# salva os CONTATOS

		$telefone = $_POST['telefone'];
		$email = $_POST['email'];
		$site = $_POST['site'];
		$facebook = $_POST['facebook'];

		$contatos = array('telefone' => $telefone, 'email' => $email, 'site' => $site, 'facebook' => $facebook);

		$old = get_post_meta($post_id, 'contatos', true);
		
		if ($contatos && $contatos != $old) {
			update_post_meta($post_id, 'contatos', $contatos);
		} elseif ('' == $contatos && $old) {
			delete_post_meta($post_id, 'contatos', $old);
		}







		# salva a IMAGEM DE CAPA

		if ( $_FILES ) {
			$files = $_FILES['upload_attachment'];
			foreach ($files['name'] as $key => $value) {
				if ($files['name'][$key]) {
					$file = array(
						'name' => $files['name'][$key],
						'type' => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error' => $files['error'][$key],
						'size' => $files['size'][$key]
					);
				 
					$_FILES = array("upload_attachment" => $file);
				 
					foreach ($_FILES as $file => $array) {
						$newupload = insert_attachment($file,$post_id);
					}
				}
			}
		} 
			
			
	}



?>
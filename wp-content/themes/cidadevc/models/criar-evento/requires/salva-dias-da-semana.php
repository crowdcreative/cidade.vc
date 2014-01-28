<?php


	$diasSelecionados = $_POST['dias']; // Pega os dias marcados da semana que o evento acontece

	// Cria e roganiza a array se tiver conteúdo marcado

	if(isset($diasSelecionados)){

		// Pega cada dia marcado e forma os elementos da array adicionando os horários

		foreach ($diasSelecionados as $diaSelecionado) {
			switch ($diaSelecionado) {
				case 'Domingo':
					$arrayDias[0] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;
				case 'Segunda':
					$arrayDias[1] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;
				
				case 'Terça':
					$arrayDias[2] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;

				case 'Quarta':
					$arrayDias[3] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;

				case 'Quinta':
					$arrayDias[4] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;

				case 'Sexta':
					$arrayDias[5] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;

				case 'Sábado':
					$arrayDias[6] = array('dia' => $diaSelecionado, 'horarios' => array ('inicio' => $_POST['inicio_hora_'.$diaSelecionado], 'termino' => $_POST['termino_hora_'.$diaSelecionado]) );
					break;

			}
			
		}

		asort($arrayDias); // Coloca os dias em ordem pela 'key'
	}
	

	
	$old = get_post_meta($post_id, 'dias_da_semana', true); // pega a array salva (se existir) no banco de dados

	// Faz a comparação e atualiza ou deleta

	if ($arrayDias && $arrayDias != $old) {
		update_post_meta($post_id, 'dias_da_semana', $arrayDias);
	} elseif ('' == $new && $old) {
		delete_post_meta($post_id, 'dias_da_semana', $old);
	}



?>
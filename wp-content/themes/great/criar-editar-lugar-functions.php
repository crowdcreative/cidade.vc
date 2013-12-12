<?php





	# DIAS DA SEMANA

	/**
	 * Cria os inputs dos dias de funcionamento (dias da semana) para criar e editar lugares e eventos
	 * @param  [Pega o ID do post onde for chamada a função] $postID
	 * @return [Retorna os inputs com dados se existirem]
	 */
	function inputs_dias_da_semana($postID){
	
				    
	    $terms = get_terms( 'dias_de_funcionamento', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));  
	    $dataArray = get_post_meta($postID, 'dias_da_semana', true);
	    
	    if(is_array($dataArray)){
	    	$data = get_post_meta($postID, 'dias_da_semana', true);
	    }
	    else{
	    	unset($data);
	    }

        foreach ($terms as $term) {  

        	echo '<div class="checkbox" style="margin-bottom:25px">';
	        	echo '<div class="row">';
	        		echo '<div class="col-sm-2" style="padding-top: 25px;"><input type="checkbox" value="'.$term->name.'" name="dias[]" id="' . $term->id . '" '; if(is_array($data)){ if(in_multiarray($term->name, $data)){echo 'checked="checked"> ';}else{echo '>';}}else{echo '>';}  
	            	echo $term->name.'</div>';  
	            	echo '<div class="col-sm-10">
	            				<div class="row">
	            					<div class="col-sm-6">
	            					<span>1º Turno</span>
			            				<div class="row">
			            					<div class="col-sm-6">
					            				<input style="width:75px" type="time" name="inicio_hora_'.$term->name.'" id="inicio_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['horarios']['inicio'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
					            				<span class="help-block">Hora de início</span>
					            			</div>
					            			<div class="col-sm-6">
					            				<input style="width:75px" type="time" name="termino_hora_'.$term->name.'" id="termino_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['horarios']['termino'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
					            				<span class="help-block">Hora de termino</span>
					            			</div>
				            			</div>
			            			</div>
			            			<div class="col-sm-6">
			            			<span rel="tooltip-border" title="Use este espaço se necessário, como quando há intervalo no horário de funcionamento (Ex:. horário de almoço às 12:00h).">2º Turno*</span>
			            				<div class="row">
			            					<div class="col-sm-6">
					            				<input style="width:75px" type="time" name="segundo_inicio_hora_'.$term->name.'" id="segundo_inicio_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['segundohorarios']['inicio'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
					            				<span class="help-block">Hora de início</span>
					            			</div>
					            			<div class="col-sm-6">
					            				<input style="width:75px" type="time" name="segundo_termino_hora_'.$term->name.'" id="segundo_termino_hora_'.$term->name.'" placeholder="__:__" value="'.$data[keyBairro($term->name)]['segundohorarios']['termino'].'" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" class="form-control hora">
					            				<span class="help-block">Hora de termino</span>
					            			</div>
				            			</div>
			            			</div>
		            			</div>
	            		  </div>';
	            echo '</div>';
	        echo '</div>';
	    }  
	}



	




































?>
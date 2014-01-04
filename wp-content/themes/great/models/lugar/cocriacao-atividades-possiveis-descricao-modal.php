<?php
	
	$user_id = get_current_user_id();
	
	$first_name = get_user_meta($user_id, 'first_name', true);
	


?>

<!-- MODAL DO PERFIL DO USUÁRIO -->
<div class="modal fade" id="atividades-possiveis-descricao-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">

		    <div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Atividades possíveis de praticar neste lugar</h4>
		        <span class="numero-viram"></span><span class="numero-viram-texto" style="font-size: 85%"> pessoas viram alguem praticando esta atividade.</span><br/>
		        <span class="numero-praticam"></span><span class="numero-viram-texto" style="font-size: 85%"> pessoas praticam esta atividade.</span>
		    </div>


		    <div class="modal-body" style="text-align: right">

			    <textarea class="form-control expanding" name="nova_atividade_descricao" id="nova_atividade_descricao" rows="1" placeholder="Descreva o local onde se pratica esta atividade, os melhores horários..."></textarea>

			    <span id="charNum" style="padding: 0 12px 0 0;"></span>

			    <button id="btn-enviar-comentario" style="margin: 6px 0 0 0" class="btn btn-primary">Enviar</button>

			    

		    </div>


		    <div class="comentarios">
			    	
			</div>
			



    	</div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

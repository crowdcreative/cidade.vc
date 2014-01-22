

<!-- MODAL DO PERFIL DO USUÁRIO -->
<div class="modal fade" id="atividades-possiveis-descricao-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">

		    <div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h2 class="modal-title" id="myModalLabel"  style="float: left; width: auto; margin: 0px 15px 0px 0px;"></h2>


			    <div style="margin: -3px 0px 0px;">

			        <div class="btn-group">

			        	<button type="button" class="btn btn-default  btn-sm vi-botao" style="height: 30px">
			        		<span class="glyphicon glyphicon-eye-open"></span> 
			        		<span class="btn-text">Já viu?</span>
			        	</button>
			        
			        	<button type="button" class="btn btn-default btn-sm vi-contador" style="height: 30px">
			        		<span class="numero-viram"></span>
			        	</button>

			        </div>

			        <div class="btn-group">

			        	<button type="button" class="btn btn-default btn-sm pratico-botao" style="height: 30px">
			        		<span class="glyphicon glyphicon-play"></span> 
			        		<span class="btn-text">Você pratica?</span>
			        	</button>
		    
			        	<button type="button" class="btn btn-default btn-sm pratico-contador" style="height: 30px">
			        		<span class="numero-praticam"></span>
			        	</button>

		        	</div>

		        </div>

		    </div>


		    <div class="modal-body" style="text-align: right">

			    <textarea class="form-control expanding" name="nova_atividade_descricao" id="nova_atividade_descricao" rows="1" placeholder="Descreva o local onde se pratica esta atividade, os melhores horários..."></textarea>

			    <span id="charNum" style="padding: 0 12px 0 0;"></span>

			    <button id="btn-enviar-comentario" token-security="<?php echo $_SESSION['token_security'] ?>" style="margin: 6px 0 0 0" class="btn btn-primary">Enviar</button>

			    

		    </div>


		    <div class="comentarios">
			    	
			</div>
			



    	</div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

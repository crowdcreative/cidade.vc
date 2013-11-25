<div id="single-col-left" class="col-md-3">

	<div class="panel panel-default copy-width">

			<?php if(get_field("imagem_de_capa")){ ?>
			<div class="left avatar-box">
				<?php
				$attachment_id = get_field("imagem_de_capa");
				echo wp_get_attachment_image($attachment_id, 'image-lugar') ; 
				?>
				
			</div>
			<?php } ?>


		<div class="panel-body">

			<div class="nofloat endereco-box">

				
				<ul>
					<li><b>Endereço:</b> <?php the_field("endereço"); ?></li>
					<?php if(get_field("telefone")){ ?><li><b>Telefone:</b> <?php the_field("telefone"); ?></li><?php } ?>
					<?php if(get_field("site")){ ?><li><b>Site:</b> <a href="<?php the_field("site"); ?>" target="_blank"><?php $url = get_field("site"); $url = NewUrl($url); echo($url); ?></a></li><?php } ?> 
				</ul>


			</div>

									

		</div>

	</div>

	<nav id="nav" class="relative">
		<ul id="anchorlinks" class="nav nav-tabs nav-stacked affix">
			<?php if(get_field("descrição_do_lugar")){ ?><li class="active"><a href="#descricao_do_lugar" >Descrição do lugar</a></li><?php } ?>
			<?php if(get_field("serviços_oferecidos")){ ?><li><a href="#servicos_oferecidos">Serviços oferecidos</a></li><?php } ?>
			<?php if(get_field("atividades_possiveis")){ ?><li><a href="#atividades_possiveis">Atividades possíveis</a></li><?php } ?>
			<?php if(get_field("tambem_são_realizados")){ ?><li><a href="#tambem_sao_realizados">Também são realizados</a></li><?php } ?>
			<?php if(get_field("como_ter_acesso")){ ?><li><a href="#como_ter_acesso" >Como ter acesso</a></li><?php } ?>
			<?php if(get_field("dias_de_funcionamento")){ ?><li><a href="#dias_de_funcionamento" >Horários de funcionamento</a></li><?php } ?>
			<?php if(get_field("endereço")){ ?><li><a href="#localizacao_no_mapa">Localização no mapa</a></li><?php } ?>
			<?php if(get_field("endereço")){ ?><li><a href="#onibus_que_passao_perto">Ônibus que passam perto</a></li><?php } ?>

		</ul>
	</nav>					

</div>
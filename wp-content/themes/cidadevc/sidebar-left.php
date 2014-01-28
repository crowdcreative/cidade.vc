<?php
	$postID = get_the_ID();
?>

<div id="single-col-left" class="col-md-3">

	<div class="panel panel-default copy-width">

			<?php 
			$imagemID = get_post_meta($postID, 'imagem_capa', true);
			$atachment_url = wp_get_attachment_image_src( $imagemID, 'image-lugar');
			?>
			
			<div class="left avatar-box">

				<?php echo '<img class="avatar" src="'.$atachment_url[0].'">'; ?>
				
			</div>
		


		<div class="panel-body">

			<div class="nofloat endereco-box">

				<?php
				$endereco = get_post_meta($postID, 'endereco', true);
				$contatos = get_post_meta($postID, 'contatos', true);

				?>
				
				<ul>

					<?php if($endereco != ''){ ?>
						<li><b>Endereço:</b> <?php echo $endereco ?> </li>
					<?php } ?>

					<?php if($contatos['telefone'] != ''){ ?>
						<li><b>Telefone:</b> <?php echo $contatos['telefone']; ?></li>
					<?php } ?>

					<?php if($contatos['site'] != ''){ ?>
						<li><b>Site:</b> <a href="<?php echo $contatos['site']; ?>" target="_blank"><?php $url = $contatos['site']; $url = NewUrl($url); echo($url); ?></a></li>
					<?php } ?> 
				</ul>


			</div>

									

		</div>

	</div>

	<nav id="nav" class="relative">
		<ul id="anchorlinks" class="nav nav-tabs nav-stacked affix">

			<?php 
			$descricao = get_post_meta($postID, 'descricao', true);
			if($descricao != ''){ ?>
				<li class="active"><a href="#descricao_do_lugar" >Descrição do lugar</a></li>
			<?php } ?>


			
			<?php 
			$servicos_oferecidos = get_post_meta($postID, 'servicos_oferecidos', true);
			if($servicos_oferecidos != ''){ ?>
				<li><a href="#servicos_oferecidos">Serviços oferecidos</a></li>
			<?php } ?>



			<?php 
			$atividades_possiveis = get_post_meta($postID, 'atividades_possiveis', true);
			if($atividades_possiveis != ''){ ?>
				<li><a href="#atividades_possiveis">Atividades possíveis</a></li>
			<?php } ?>





			
			<?php 
			$servicos_oferecidos_info = get_post_meta($postID, 'servicos_oferecidos_info', true);
			if($servicos_oferecidos_info != ''){ ?>
				<li><a href="#tambem_sao_realizados">Também são realizados</a></li>
			<?php } ?>



			<?php 
			$acesso = get_post_meta($postID, 'acesso', true);
			if($acesso != ''){ ?>
				<li><a href="#como_ter_acesso" >Como ter acesso</a></li>
			<?php } ?>



			<?php 
			$dataArray = get_post_meta($postID, 'dias_da_semana', false);
			if($dataArray != ''){ ?>
				<li><a href="#dias_de_funcionamento" >Horários de funcionamento</a></li>
			<?php } ?>



			<?php if($endereco != ''){ ?>
				<li><a href="#localizacao_no_mapa">Localização no mapa</a></li>
			<?php } ?>



			<?php if($endereco != ''){ ?>
				<li><a href="#onibus_que_passao_perto">Ônibus que passam perto</a></li>
			<?php } ?>

		</ul>
	</nav>					

</div>
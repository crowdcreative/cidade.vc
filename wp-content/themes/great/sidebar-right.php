<div id="single-col-left" class="col-md-3">

	<div class="panel panel-default">

		
			
		
			<?php if(get_field("imagem_de_capa")){ ?>
			<div class="left avatar-box">

				<img id="avatar" src="<?php the_field("imagem_de_capa"); ?>" class=""/>
				
			</div>
			<?php } ?>


		<div class="panel-body">

			<div class="nofloat endereco-box">

				
				<ul>
					<li><b>Endereço:</b> <?php the_field("endereço"); ?></li>
					<li><b>Telefone:</b> <?php the_field("telefone"); ?></li>
					<li><b>Site:</b> <a href="<?php the_field("site"); ?>" target="_blank"><?php $url = get_field("site"); $url = NewUrl($url); echo($url); ?></a></li> 
				</ul>


			</div>

									

		</div>

	</div>

</div>
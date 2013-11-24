
<?php
	$categories = get_terms( 'category', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));
	$bairros = get_terms( 'bairros', array('orderby'    => 'count', 'hide_empty' => 0, 'parent'  => 0 ));
?>

<div id="single-col-left" class="col-md-3">

		

			<div class="form-group">
				
				<?php get_search_form(); ?>

			</div>

		
				
		<nav class="panel panel-default">

			<div class="panel-heading"><b>Categorias</b></div>

			<ul id="anchorlinks" class="list-group">

				<?php
					foreach ($categories as $categoria) {
						if($categoria->name != "Sem categoria"){
							echo "<li class='list-group-item'><a href='#'>".$categoria->name."</a><span class='badge'>".$categoria->count."</span></li>";
						}
					}
				?>

			</ul>
		</nav>

		<nav class="panel panel-default">

			<div class="panel-heading"><b>Bairros</b></div>

			<ul id="anchorlinks" class="list-group">

				<?php
					foreach ($bairros as $bairro) {
						if($bairro->name != "Sem categoria"){
							echo "<li class='list-group-item'><a href='#'>".$bairro->name."</a><span class='badge'>".$bairro->count."</span></li>";
						}
					}
				?>

			</ul>
		</nav>
				

</div>
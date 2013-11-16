<?php get_header(); ?>
<?php $options = get_option('great'); ?>

<!-- Define vars para o mapa -->
	
	<?php
	$latlong = get_field('latlong');
	$latlong = str_replace('(', '', $latlong);
	$latlong = str_replace(')', '', $latlong);
	?>



<script type="text/javascript">

$(document).ready(function(){

		// Adiciona o map-canvas abaixo do input buscador
		$("<div id='map-canvas'></div>").insertAfter("#acf-endereço");
		$("#acf-endereço").css({"width":"100%"});
 
		// Adiciona o botao abaixo do mapa
		$("<div id='botao'><span>Buscar endereço</span></div>").insertAfter("#acf-endereço");
		$("#botao").css({"width":"100%"});

		// chama o geocoder do Google
		var geocoder = new google.maps.Geocoder();

		// Efetua o corte de parte da string retornada do geocoder - endereço
		function contains(str, text) {
  		 	return (str.indexOf(text) >= 0);
		}



		// Criação do mapa
		$('#map-canvas').gmap3({
			map: {
				options: {
					center: [<?php echo $latlong; ?>],
					zoom: 13,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					mapTypeControl: true,
					mapTypeControlOptions: {
						style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
					},
					navigationControl: true,
					scrollwheel: true,
					streetViewControl: true
				}
			},
			marker: {
				latLng: [<?php echo $latlong; ?>],
				options: {
					draggable: true
				}
			}
		});

		var map = $(this).gmap3("get");

  });

</script>


	<div id="map-canvas"> </div>


<div id="page" class="single container">
	
		<div class="row">


			<?php get_sidebar('right'); ?>


			<div class="col-lg-6" >
				<div class="panel-default panel">
					<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
						<div id="post-<?php the_ID(); ?>" class="panel-body">
							<div class="single_post">
								


									<?php 
									global $post; 
									$postID = get_the_ID();
									?>


									
									<div class="bloco">

										<div class="left avatar-box">

											<?php if(get_field("imagem_de_capa")){ ?>
												<img id="avatar" src="<?php the_field("imagem_de_capa"); ?>" class="img-thumbnail"/>
											<?php } ?>

										</div>

										<div class="nofloat endereco-box">

											<h1 id="titulo" class="single-title"><?php the_title(); ?></h1>
											<ul>
												<li><b>Endereço:</b> <?php the_field("endereço"); ?></li>
												<li><b>Telefone:</b> <?php the_field("telefone"); ?></li>
												<li><b>Site:</b> <?php $url = get_field("site"); $url = NewUrl($url); echo($url); ?></li> 
											</ul>


										</div>

									</div>



									
					

								

									

									<div class="bloco" style="display:none">
										<?php if(get_field("bairro")){ ?>
											<h2>Bairro</h2>
											<?php 
											$taxonomyID = get_field("bairro");
											$taxonomyID = (int)$taxonomyID;
											 ?>
											<p><?php $taxonomy = get_term($taxonomyID, 'bairros'); echo $taxonomy->slug; ?></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("descrição_do_lugar")){ ?>
											<h2>Descrição do lugar</h2>
											<p><?php the_field("descrição_do_lugar"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco servicos">
										<?php if(get_field("serviços_oferecidos")){ ?>
											<h2>Serviços oferecidos</h2>
											<p>
											<?php 
											$term_list = wp_get_post_terms($postID, 'serviços', array("fields" => "all")); 
											echo "<ul>";
											foreach ($term_list as $term) {
												$termEcho = $term->name;
												$termEcho = substr($termEcho, 0, 22);
												echo "<li>" . $termEcho . "</li>";
												
											}
											echo "</ul>";
											?></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("tambem_são_realizados")){ ?>
											<h2>Também são realizados</h2>
											<p><?php the_field("tambem_são_realizados"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("como_ter_acesso")){ ?>
											<h2>Como ter acesso</h2>
											<p><?php the_field("como_ter_acesso"); ?></p>
										<?php } ?>
									</div>

								
							</div><!--.post-content box mark-links-->
							
					
					</div><!--.g post-->
					<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
				</div>
			</div>
	
		



		<?php get_footer(); ?>
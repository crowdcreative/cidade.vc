<?php get_header(); ?>
<?php $options = get_option('great'); ?>

<!-- Define vars para o mapa -->
	
	<?php
	$latlong = get_field('latlong');
	$latlong = str_replace('(', '', $latlong);
	$latlong = str_replace(')', '', $latlong);
	?>


		<script type="text/javascript">

	// Deslizar de forma suave (http://css-tricks.com/snippets/jquery/smooth-scrolling/)	
	$(function() {
		$('a[href*=#]:not([href=#])').click(function() {
			if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
				if (target.length) {
					$('html,body').animate({
						scrollTop: target.offset().top
					}, 1000);
					return false;
				}
			}
		});
	});
	


$(document).ready(function(){


		var top = $('#anchorlinks').offset().top - parseFloat($('#anchorlinks').css('marginTop').replace(/auto/, 0));
		$(window).scroll(function(event) {
			var y = $(this).scrollTop() + 30;
			//if y > top, it means that if we scroll down any more, parts of our element will be outside the viewport
			//so we move the element down so that it remains in view.

			if (y >= top) {
				var difference = y - top;
				$('#anchorlinks').css("top", difference);
				$('#anchorlinks').css("position", "absolute");
				var widthCopy = $('.copy-width').width();
				$('#anchorlinks').css("width", widthCopy);
			} else {
				$('#anchorlinks').css("top", 0);
			}

		});

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

		// Mostra o scroll-top após rolar a tela

		$(window).scroll(function(event) {

			var y = $(this).scrollTop();

			if (y >= 500) {
				$('#buttonScroll-top').fadeIn();
			} else {
				$('#buttonScroll-top').fadeOut();
			}
		});

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


		// Scroll to top
	    $('a[href=#top]').click(function(){
	        $('html, body').animate({scrollTop:0}, 'slow');
	        return false;
	    });

	    var ajaxUrl = "http://127.0.0.1/projects/cidade.vc/wp-admin/admin-ajax.php";

		var map = $("#map-canvas").gmap3("get");


		// Adiciona o circulo no mapa
		var circle = new google.maps.Circle({
			map: map,
			radius: 600, // metres
			strokeWeight: 0,
			fillOpacity: 0.1,
			clickable: false,
		});

		var marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php echo $latlong; ?>),
		});

		circle.bindTo('center', marker, 'position');


        google.maps.event.addListener(map, 'bounds_changed', function() {
         	var bounds = circle.getBounds();
         	var bounds = bounds.toString();
         	pegaBound(bounds);
		});

		// Url do ajax do wordpress
   		var ajaxUrl = "http://127.0.0.1/projects/cidade.vc/wp-admin/admin-ajax.php";
		

		// jQuey ajax para chamar latlong
		function pegaBound(bounds){


			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					'action': 'getbounds',
					'minmaxlatlong': bounds
				},
				success: function(dados) {
					console.log(dados);
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}

			});

		}

		


  });

</script>



<a href="#top"><div id="buttonScroll-top" class="glyphicon glyphicon-circle-arrow-up" style="display:none"></div></a>


<div id="page" class="single container">
	
		<div class="row">


			<?php get_sidebar('right'); ?>


			<div class="col-md-9">
				<div class="panel-default panel">
					<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
						
							
								<?php 
								global $post; 
								$postID = get_the_ID();
								$valorID = get_field("preço");
								?>


								<div class="panel-heading">
									<h1 id="titulo" class="single-title panel-title"><?php the_title(); ?><span class="label-small label-success"><?php preco($valorID); ?></span></h1>
								</div>	
									
								<div class="panel-body">

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

									<div class="bloco" id="descricao_do_lugar">
										<?php if(get_field("descrição_do_lugar")){ ?>
										
											<p><?php the_field("descrição_do_lugar"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco li-default" id="servicos_oferecidos">
										<?php if(get_field("serviços_oferecidos")){ ?>
											<h2>Serviços oferecidos</h2>
											<p>
											<?php 
											$term_list = wp_get_post_terms($postID, 'serviços', array("fields" => "all")); 
											echo "<ul class='row'>";
											foreach ($term_list as $term) {
												$termEcho = $term->name;
												$termEcholen = strlen($termEcho); // pega o tamanho da string

												$termEchocut = substr($termEcho, 0, 30); // corta a string

												if($termEcholen > 30){
													echo "<li class='col-sm-4'><span style='cursor:help' rel='tooltip' title='".$termEcho."'>" . $termEchocut . " ...</span></li>";
												}else{
													echo "<li class='col-sm-4'><span>" . $termEcho . "</span></li>";
												}

											}
											echo "</ul>";
											?></p>
										<?php } ?>
									</div>

									<div class="bloco" id="tambem_sao_realizados">
										<?php if(get_field("tambem_são_realizados")){ ?>
											<h2>Também são realizados</h2>
											<p><?php the_field("tambem_são_realizados"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco" id="como_ter_acesso">
										<?php if(get_field("como_ter_acesso")){ ?>
											<h2>Como ter acesso</h2>
											<p><?php the_field("como_ter_acesso"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco li-default" id="dias_de_funcionamento">
										<?php if(get_field("dias_de_funcionamento")){ ?>
											<h2>Dias e horários de funcionamento</h2>
										
											<p>
											<?php // Puxa as taxonomies dos dias de funcionamento
											$term_list = wp_get_object_terms($postID, 'dias_de_funcionamento', array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'all')); 
											$term_listArray = array(); // cria uma array para as taxonomies
											$term_listArray = $term_list; // coloca as taxonomies 
											sort($term_listArray); // coloca as taxonomies (dis de funcionamento) em ordem
											?>
											<div class='row'>
												<div class='col-sm-7'>
													<ul class='row'>
														<?php
														foreach ($term_listArray as $term) {
															$termEcho = $term->name;
															if ($termEcho == 'Segunda'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_segunda') . "</span></li>";
															}
															elseif ($termEcho == 'Terça'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_terca') . "</span></li>";
															}
															elseif ($termEcho == 'Quarta'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_quarta') . "</span></li>";
															}
															elseif ($termEcho == 'Quinta'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_quinta') . "</span></li>";
															}
															elseif ($termEcho == 'Sexta'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_sexta') . "</span></li>";
															}
															elseif ($termEcho == 'Sábado'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_sabado') . "</span></li>";
															}
															elseif ($termEcho == 'Domingo'){ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span> <span style='font-size:85%'>" . get_field('horario_de_funcionamento_na_domingo') . "</span></li>";
															}
															else{ 
																echo "<li class='col-sm-6'><span class='badge'>" . $termEcho . "</span></li>";
															}
														}

														?>
													</ul>
												</div>

												<div class='col-sm-5'>
													

												</div>
											</div>
											
											</p>

										<?php } ?>


								
									</div>


									<div class="bloco" id="localizacao_no_mapa">
										<h2>Localização no mapa<small> <?php the_field("endereço"); ?></small></h2>
										<div id="map-canvas"> </div>
									</div>

									<?php 
										// #####  Exibi as linhas próximas ao marcador via 'bounds' do 'circle'

										// Pega o JSON do poatransporte
										$data = json_decode(file_get_contents("http://www.poatransporte.com.br/php/facades/process.php?a=tp&p=%28%28-30.083747652841197%2C+-51.14574888083473%29%2C+%28-30.065781347158804%2C+-51.1249875191653%29%29"));

										$arrayPronta = array(); //Array que criamos para pegar as linhas sem duplicação

										for ($i=0; $i < 20; $i++) { // pegamos no máximo 20 linhas
											
											$linhasArray = $data[$i]->linhas;
											$numerodeLinhas = sizeof($linhasArray);

											for ($iL=0; $iL < $numerodeLinhas; $iL++) { 
												$idLinha = $linhasArray[$iL]->idLinha;
												$codigoLinha = $linhasArray[$iL]->codigoLinha;

												$linha = $linhasArray[$iL]->nomeLinha;

												$arrayPronta[$iL] = array('linha' => $linha, 'codigo' => $codigoLinha, 'id' => $idLinha);
												
				
											}
										}

					


									?>

									<div class="bloco li-default li-default-capitalize" id="onibus_que_passao_perto">
										<h2>Ônibus que passam perto</h2>
										<ul class="row">
											<?php

												$arrayProntaLen = sizeof($arrayPronta);

												for ($i=0; $i < $arrayProntaLen; $i++) { 
													
													$linha = $arrayPronta[$i]['linha'];
													$codigo = $arrayPronta[$i]['codigo'];

													$linha = trim(ucfirst(strtolower($linha))); // Deixa os caracteres em minúsculo, depois o primeiro em maísculo e depois remove espaços em branco
													$valueLen = strlen($linha); // pega o tamanho da string
													
													if($valueLen > 30){
														$linhaCut = substr($linha, 0, 30); // corta a string
														echo "<li class='col-sm-6'><span rel='tooltip' style='cursor:help' title='".$linha."'><span class='badge'>".$codigo."</span> ".$linhaCut." ...</span></li>";
													}else{
														echo "<li class='col-sm-6'><span><span class='badge'>".$codigo."</span> ".$linha."</span></li>";
													}
												
												}

												
											?>
										</ul>
									</div>



							</div><!--.post-content box mark-links-->
							
					
					
					<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
				</div>
			</div>
	




		<?php get_footer(); ?>
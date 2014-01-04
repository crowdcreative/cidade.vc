<?php require 'controllers/single-functions.php' ?>

<?php get_header(); ?>


<!-- Define vars para o mapa -->
	
	<?php
	$latlong = get_post_meta($post->ID, 'latlong', true);
	$latlong = str_replace('(', '', $latlong);
	$latlong = str_replace(')', '', $latlong);
	$userID = get_current_user_id(); // pega id do usuário
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
						scrollTop: target.offset().top-80
					}, 1000);
					return false;
				}
			}
		});
	});
	


$(document).ready(function(){

		var ajaxUrl = "http://127.0.0.1/projects/cidade.vc/wp-admin/admin-ajax.php";


		// Vota nas atividades possíveis - VIRAM

		$('#atividades_possiveis ul li .badge-rounded').click(function(){

			var termID = $(this).parents('li').attr('term-id');
			var clicado = $(this);

			$.ajax({
				url: ajaxUrl,
				type: 'POST',

				data: {
					'action': 'atividades_possiveis_votacao',
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'term-id': termID,
					'viram-ou-praticam':'viram'
				},
				success: function(dados){
					if(clicado && dados != ''){
						// atualiza o contador
						clicado.html(dados);

						// remove a clase 'badge-hide' responsável por esconder itens no efeito 'hover'
						clicado.removeClass('badge-hide');
					}

					if(clicado && dados == '0'){
						// atualiza o contador
						clicado.html('-');

						// adiciona a classe 'badge-hide'
						clicado.addClass('badge-hide');
					}

					
				},
				error: function(errorThrown) {
				
				}

			});
		});






		// Vota nas atividades possíveis - PRATICAM

		$('#atividades_possiveis ul li .badge-square').click(function(){

			var termID = $(this).parents('li').attr('term-id');
			var clicado = $(this);

			$.ajax({
				url: ajaxUrl,
				type: 'POST',

				data: {
					'action': 'atividades_possiveis_votacao',
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'term-id': termID,
					'viram-ou-praticam':'praticam'
				},
				success: function(dados){
					if(clicado && dados != ''){

						// atualiza o contador
						clicado.html(dados);

					}

					if(clicado && dados == '0'){

						// atualiza o contador
						clicado.html('-');

					}

					
				},
				error: function(errorThrown) {
				
				}

			});
		});







		// adiciona o novo comentário ao banco de dados e no fron-end
		$('#btn-enviar-comentario').click(function(){

			var clicado = $(this);
			var atividade_id = clicado.attr('atividade-id');

			clicado.html('Enviando...').addClass('disabled');

			$.ajax({
				url: ajaxUrl,
				type: 'POST',

				data: {
					'action': 'atividades_possiveis_comentarios',
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'atividade-id': atividade_id,
					'nova-descricao': $('textarea#nova_atividade_descricao').val()
				},
				success: function(dados){
					
						// atualiza o contador
						clicado.removeClass('btn-default, disabled').addClass('btn-success').html('Enviado');
						
						// executa a função após alguns segundos
						setTimeout(
							function(){

								clicado.removeClass('btn-success').addClass('btn-default').html('Enviar');

								$('textarea#nova_atividade_descricao').val('');

								$('#charNum').html('');

							}, 1500
						)
					

						$('#atividades-possiveis-descricao-modal .comentarios ul li:eq(0)').before(dados);
						
						setTimeout(
							function(){
						
								$('#li-show-divider, #li-show-comentario').css('opacity', 0) .slideDown('slow') .animate({ opacity: 1 }, { queue: false, duration: 'slow' } );
							}, 300
						)
				},
				error: function(errorThrown) {
				
				}

			});
		});





		/**
		 * Excluir comentário da atividade - exclui o comentário da atividade se o comentário for do usuário.
		 * 
		 * @return {string} Retorna uma string com a confirmação da exclusão 
		 * e remove o comentário da <div> comentários.
		 */
		

		$(document).on('click', ".atividades-possiveis-comentarios-excluir", function () {

			var clicado = $(this);
			var atividade_id = clicado.attr('atividade-id');
			var key = clicado.attr('key');

			clicado.html('Excluindo...').addClass('disabled');

			$.ajax({
				url: ajaxUrl,
				type: 'POST',

				data: {
					'action': 'atividades_possiveis_comentarios_excluir',
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'atividade-id': atividade_id,
					'key': key
				},
				success: function(dados){

					clicado.parents('li').fadeOut();
					clicado.parents('li').prev('li').fadeOut();
					
				},
				error: function(errorThrown) {
				
				}

			});
		});












		// mostra e esconde o botão de adicionar uma nova  atividade

		$('#atividades_possiveis').hover(
			function(){
				$('#adicionar-atividade').show();
			},
			function(){
				$('#adicionar-atividade').hide();
			}
		);





		// mostra e esconde os badges com os números

		$('#atividades_possiveis ul li').hover(
			function(){
				$(this).find('.badge-square').show();
				$(this).find('.badge-hide').show();
			},
			function(){
				$(this).find('.badge-square').hide();
				$(this).find('.badge-hide').hide();
			}
		);







		// abri o 'modal' e clona alguns conteúdos para os comentários das atividades

		$('#atividades_possiveis .atividade-name').click(
			function(){

				var clicado = $(this);

				// pega a 'atividade_id' 
				var atividade_id = $(this).parents('li').attr('term-id');

				// coloca a 'atividade_id' no botao de enviar do modal
				$('#btn-enviar-comentario').attr('atividade-id', atividade_id);

				// limpa as divs onde será colocado as informações
				$('#atividades-possiveis-descricao-modal .modal-header h4').html('');
				$('#atividades-possiveis-descricao-modal .numero-viram').html('');
				$('#atividades-possiveis-descricao-modal .numero-praticam').html('');

				// pega o nome da atividade e cola no titulo da modal
				$(this).clone().appendTo($('#atividades-possiveis-descricao-modal .modal-header h4'));

				// pega o numero de pessoas que viram pessoas praticando esta atividade
				$(this).parents('li').find('.badge-rounded').clone().appendTo($('#atividades-possiveis-descricao-modal .numero-viram'));
			
				// pega o numero de pessoas que praticam esta atividade
				$(this).parents('li').find('.badge-square').clone().appendTo($('#atividades-possiveis-descricao-modal .numero-praticam'));

				$('#atividades-possiveis-descricao-modal .comentarios').html('<ul><li>Carregando...</li></ul>');

				$.ajax({
					url: ajaxUrl,
					type: 'POST',

					data: {
						'action': 'atividades_possiveis_comentarios_db',
						'user-id': <?php echo $userID; ?>,
						'post-id': <?php echo $post->ID ?>,
						'atividade-id': atividade_id
					},
					success: function(comentarios){

						// atualiza o contador
						$('#atividades-possiveis-descricao-modal .comentarios').html(comentarios);

					},
					error: function(errorThrown) {
						
					}

				});



			}
		);





		$('#nova_atividade_descricao').keyup(function () {
		  	var max = 1000;
		  	var len = $(this).val().length;
		  	if (len > max) {
		  		var char = max - len;
			    $('#charNum').removeClass('text-success').addClass('text-danger').text('Para enviar o texto precisa ter ' + char + ' caracteres.');
		  		$('#btn-enviar-comentario').addClass('disabled');
		  	} 

		  	else if(len == max){
		  		$('#charNum').text('O número de caracteres está perfeito!');
		  		$('#btn-enviar-comentario').removeClass('disabled');
		  	}

		  	else {
			    var char = max - len;
			    $('#charNum').removeClass('text-danger').addClass('text-success').text('O comentário ainda pode ter até ' + char + ' caracteres.');
			    $('#btn-enviar-comentario').removeClass('disabled');
		  	}
		});



		var top = $('#anchorlinks').offset().top - parseFloat($('#anchorlinks').css('marginTop').replace(/auto/, 0));
		$(window).scroll(function(event) {
			var y = $(this).scrollTop() + 70;
			//if y > top, it means that if we scroll down any more, parts of our element will be outside the viewport
			//so we move the element down so that it remains in view.

			if (y >= top) {
				var difference = y - top;
				$('#anchorlinks').css("position", "fixed");
				$('#anchorlinks').css("top", "70px");
				var widthCopy = $('.copy-width').width();
				$('#anchorlinks').css("width", widthCopy);
			} else {
				$('#anchorlinks').css("position", "relative");
				$('#anchorlinks').css("top", 0);
			}

		});

		// Ajeita o tamnho da sidebar ao alterar o tamanho da janela do browser
		$(window).resize(function(event) {
			var widthCopy = $('.copy-width').width();
			$('#anchorlinks').css("width", widthCopy);
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


        google.maps.event.addListenerOnce(map, 'idle', function(){
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
					$('#onibus_que_passao_perto ul').html(dados);
						// Habilita a tooltip do bootstrap
						$('[rel=tooltip]').tooltip({placement:'top'});
						$('[rel=tooltip-border]').tooltip({placement:'top'});

						// Adicionar borda pontilhada no holover do link com tooltip
						$('[rel=tooltip-border]').hover(function(){
							$(this).css({'border-bottom':'1px dotted #cccccc','padding-bottom':'2px'});
						},
						function(){
							$(this).css({'border-bottom':'0','padding-bottom':'0'});
						});
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}

			});

		}






		


  });

</script>


		<!-- Pega o modal da cocriação das atividades possíveis -->
		<?php require 'models/lugar/cocriacao-atividades-possiveis-modal.php' ?>
		<?php require 'models/lugar/cocriacao-atividades-possiveis-descricao-modal.php' ?>


<a href="#top"><div id="buttonScroll-top" class="glyphicon glyphicon-circle-arrow-up" style="display:none"></div></a>


<div id="page" class="single container">
	
		<div class="row">


			<?php get_sidebar('left'); ?>

			<div class="col-md-9">

				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
						
					<?php if ('lugar-saude' == get_post_type()){ ?>

						<?php 
						global $post; 
						$postID = get_the_ID();
						$preco = wp_get_object_terms($post->ID, 'preço');
						?>

						

						<div class="panel-default panel">

							<div class="panel-heading">
								<h1 id="titulo" class="single-title panel-title"><?php the_title(); ?><?php if($preco != ''){ ?><span class="label-small label-success"><?php if(is_array($preco)){ echo $preco[0]->name;} ?></span><?php } ?></h1>
							</div>	
								
							<div class="panel-body">


								<?php get_descricao($postID); ?>

							</div><!-- panel-body -->

						</div> <!-- panel -->


						

						<?php get_servicos_oferecidos($postID); ?>

							

						<?php get_servicos_oferecidos_info($postID); ?>



						<?php get_acesso($postID); ?>

						
						<?php get_dias_da_semana($postID); ?>
						

						<?php get_localizacao($postID); ?>


						<?php get_onibus($postID); ?>



							
			
					<?php }elseif ('lugar-lazer' == get_post_type()){ ?>

						<?php 
						global $post; 
						$postID = get_the_ID();
						$preco = wp_get_object_terms($post->ID, 'preço');
						?>


						


						<div class="panel-default panel">

							<div class="panel-heading">
								<h1 id="titulo" class="single-title panel-title"><?php the_title(); ?><?php if($preco != ''){ ?><span class="label-small label-success"><?php if(is_array($preco)){ echo $preco[0]->name;} ?></span><?php } ?></h1>
							</div>	
								
							<div class="panel-body">


								<?php get_descricao($postID); ?>


							</div><!-- panel-body -->

						</div><!-- panel -->

						<?php

						// cria a query que irá pegar as informações do banco de dados
						$query = "SELECT conteudo_moderacao FROM wp_cocriacao WHERE lugar_id = '$post_id' AND bloco = 'atividades_possiveis_comentarios' ";
						
						// executa a query
						$result = mysql_query($query);
						
						// cria uma array que armazenará o resultado do select acima
						$result_array = array();

						$i = 0;

						// monta a array com as informações baixadas do banco de dados
						while ($atividade = mysql_fetch_array($result)) {
							$result_array = $atividade[0];
						}

						print_r(unserialize($result_array));
						
						?>
								
								<?php get_atividades_possiveis($postID); ?>
							

							
								<?php get_eventos($postID); ?>


							
								<?php get_servicos_oferecidos_info($postID); ?>
								
								

								<?php get_acesso($postID); ?>


								<?php get_dias_da_semana($postID); ?>
								

								<?php get_localizacao($postID); ?>


								<?php get_onibus($postID); ?>


							
			
					<?php } ?>
					
					
					<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
				</div>
			</div>
	




		<?php get_footer(); ?>
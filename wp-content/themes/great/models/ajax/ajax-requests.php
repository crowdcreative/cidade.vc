	<script type="text/javascript">


	/**
	 *	ajax.requests.js
	 *
	 * 	ARQUIVO COM TODAS AS REQUISIÇÕES AJAX VIA JQUERY DO CLIENT-SIDE
	 *
	 * 	Descrição:
	 * 	Este arquivo contem todas as requisições que buscam informações do banco de dados via ajax, 
	 * 	sem alterar toda a página. Ex:. votação na 'atividades possíveis' e envio de comentarios nas páginas dos lugares.
	 *
	 * 	Como usar:
	 * 	O arquivo deve ser solicitado via comando 'require' na linguagem php e na página onde os eventos 
	 * 	(cliques, hover etc) ativam as requisições.
	 *  
	 *  Por: Jonatas Eduardo 
	 *
	 * 	v.0.2 - 21/01/2014 21:46:45
	 * 
	 */
	
	<?php

	$localiza_url = get_template_directory_uri();

	?>


	$(document).ready(function(){

		 
		
	 	/**
	 	 * 	ADICIONA COMENTÁRIOS NAS ATIVIDADES POSSÍVEIS DE PRATICAR - NO BANCO DE DADOS E FRONT END
	 	 *
	 	 *	A função requisita a adição dos comentários nas 'atividades possíveis' adicionando o comentário recebido
	 	 *	no banco de dados e prontamente no front-end.
	 	 *
	 	 * 	Request: 'ajax/atividades-possiveis-comentarios-ajax.php'.
	 	 * 
	 	 */
		$('#btn-enviar-comentario').click(function(){

			var clicado = $(this);
			var atividadeId = clicado.attr('atividade-id');
			var token_security = clicado.attr('token-security');

			clicado.html('Enviando...').addClass('disabled');

			$.ajax({
				url: '<?php echo $localiza_url; ?>/models/ajax/atividades-possiveis-comentarios-ajax.php',
				type: 'POST',
				dataType : 'json',
				data: {
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'atividade-id': atividadeId,
					'nova-descricao': $('textarea#nova_atividade_descricao').val(),
					'token_security': token_security
				},
				success: function(dados){

					// verifica se o usuário está logado
					if(dados.logged == 'true'){ 
					
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
					

						$('#atividades-possiveis-descricao-modal .comentarios ul li:eq(0)').before(dados.comentario);
						
						clicado.attr('token-security', dados.token);

						setTimeout(
							function(){
						
								$('.li-show-divider, .li-show-comentario').css('opacity', 0).slideDown('slow').animate({ opacity: 1 }, { queue: false, duration: 'slow' } );
								$('#li-show-divider, #li-show-comentario').removeClass('li-show-divider li-show-comentario');

							}, 300
						)

						$('#li-nenhum').remove();

					}

					else{

						$('#charNum').html('Você precisa estar logado para comentar. =)').delay(3500).fadeOut();

					}

				},
				error: function(errorThrown) {
				
				}

			});
		});






		
		/**
		 * 	ABRE O 'MODAL' E REQUISITA OS COMETÁRIOS DA ATIVIDADE E SE O USUÁRIO PRATICA A ATIVIDADE
		 *  
		 *  Requests: 'ajax/atividades-possiveis-botoes-viu-praticam-ajax.php';
		 *  		 'ajax/atividades-possiveis-get-comentarios-db-ajax.php'.
		 * 
		 */
		$('#atividades_possiveis .atividade-name').click(
			function(){

				var clicado = $(this);

				// pega a 'atividade_id' 
				var atividadeId = $(this).parents('li').attr('atividade-id');

				// coloca a 'atividade_id' no botao de enviar do modal
				$('#btn-enviar-comentario').attr('atividade-id', atividadeId);

				// limpa as divs onde será colocado as informações
				$('#atividades-possiveis-descricao-modal .modal-header h2').html('');
				$('#atividades-possiveis-descricao-modal .numero-viram').html('');
				$('#atividades-possiveis-descricao-modal .numero-praticam').html('');

				// pega o nome da atividade e cola no titulo da modal
				$('#atividades-possiveis-descricao-modal .modal-header h2').append($(this).text());

				// pega o numero de pessoas que viram pessoas praticando esta atividade
				$('#atividades-possiveis-descricao-modal .numero-viram').append($(this).parents('li').find('.badge-rounded').text());
			
				// pega o numero de pessoas que praticam esta atividade
				$('#atividades-possiveis-descricao-modal .numero-praticam').append($(this).parents('li').find('.badge-square').text());

				// adiciona o id da atividade no botao do 'modal'
				$('#atividades-possiveis-descricao-modal .vi-botao, #atividades-possiveis-descricao-modal .pratico-botao').attr('atividade-id', atividadeId);

				$('#atividades-possiveis-descricao-modal .comentarios').html('<ul><li>Carregando...</li></ul>');

				$('textarea#nova_atividade_descricao').val('');


				$.ajax({
					url: '<?php echo $localiza_url; ?>/models/ajax/atividades-possiveis-botoes-viu-praticam-ajax.php',
					type: 'POST',

					data: {
						'user-id': <?php echo $userID; ?>,
						'post-id': <?php echo $post->ID ?>,
						'atividade-id': atividadeId,
						'token_security': '<?php echo $_SESSION['token_security'] ?>'
					},
					success: function(dados){

						if(dados == '1'){ 
							$('#atividades-possiveis-descricao-modal .modal-header .pratico-botao .btn-text').text('Eu pratico');
							$('#atividades-possiveis-descricao-modal .modal-header .pratico-botao').addClass('btn-success').removeClass('btn-default');
							$('#atividades-possiveis-descricao-modal .modal-header .pratico-contador').addClass('btn-success').removeClass('btn-default');

							$('#atividades-possiveis-descricao-modal .modal-header .pratico-botao').find('.glyphicon').addClass('glyphicon-ok-sign').removeClass('glyphicon-play');
						}
						else{
							$('#atividades-possiveis-descricao-modal .modal-header .pratico-botao .btn-text').text('Você pratica?');
							$('#atividades-possiveis-descricao-modal .modal-header .pratico-botao').addClass('btn-default').removeClass('btn-success');
							$('#atividades-possiveis-descricao-modal .modal-header .pratico-contador').addClass('btn-default').removeClass('btn-success');

							$('#atividades-possiveis-descricao-modal .modal-header .pratico-botao').find('.glyphicon').addClass('glyphicon-play').removeClass('glyphicon-ok-sign');
						}

					},
					error: function(errorThrown) {
						
					}

				});



				$.ajax({
					url: '<?php echo $localiza_url; ?>/models/ajax/atividades-possiveis-get-comentarios-db-ajax.php',
					type: 'POST',

					data: {
						'action': 'atividades_possiveis_comentarios_db',
						'user-id': <?php echo $userID; ?>,
						'post-id': <?php echo $post->ID ?>,
						'logged': '<?php echo $_SESSION['logged'] ?>',
						'atividade-id': atividadeId
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







		/**
		 *	VOTA NAS ATIVIDADES POSSÍVEIS - PRATICAM
		 * 	A requisição possibilita o usuário votar nas atividades possíveis de se pratica no lugar, ao votar adiciona +1.
		 * 	O voto serve para legitimar a atividade, mostrando que ela realmente acontece naquele lugar.
		 *
		 * 	Request: 
		 */
		$('#atividades_possiveis ul li .badge-square').click(function(){

			var atividadeId = $(this).parents('li').attr('atividade-id');
			var clicado = $(this);

			$.ajax({
				url: '<?php echo $localiza_url; ?>/models/ajax/atividades-possiveis-votacao-ajax.php',
				type: 'POST',

				data: {
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'atividade-id': atividadeId,
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




		/**
		 *	VOTA NAS ATIVIDADES POSSÍVEIS - MODAL - PRATICAM
		 * 	A requisição possibilita o usuário votar nas atividades possíveis de se pratica no lugar, ao votar adiciona +1.
		 * 	O voto serve para legitimar a atividade, mostrando que ela realmente acontece naquele lugar.
		 *
		 * 	Request: ajax/atividades-possiveis-votacao-ajax.php
		 */
		$('#atividades-possiveis-descricao-modal .pratico-botao').click(function(){

			var atividadeId = $(this).attr('atividade-id'); // dependente da função 'ABRE O 'MODAL' E REQUISITA OS COMETÁRIOS DA ATIVIDADE E SE O USUÁRIO PRATICA A ATIVIDADE'
			var clicado = $(this);

			$.ajax({
				url: '<?php echo $localiza_url; ?>/models/ajax/atividades-possiveis-votacao-ajax.php',
				type: 'POST',

				data: {
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'atividade-id': atividadeId, // dependente da função 'ABRE O 'MODAL' E REQUISITA OS COMETÁRIOS DA ATIVIDADE E SE O USUÁRIO PRATICA A ATIVIDADE'
					'viram-ou-praticam':'praticam'
				},
				success: function(dados){
					if(clicado && dados != ''){

						// atualiza o botão
						clicado.find('.btn-text').html('Eu pratico');
						clicado.addClass('btn-success').removeClass('btn-default');
						clicado.find('.glyphicon').addClass('glyphicon-ok-sign').removeClass('glyphicon-play');

						// atualiza o contador
						clicado.parents('div').find('.numero-praticam').html(dados);
						clicado.parents('div').find('.pratico-contador').addClass('btn-success').removeClass('btn-default');

					}

					if(clicado && dados == '0'){

						// atualiza o botao
						clicado.find('.btn-text').html('Você pratica?');
						clicado.addClass('btn-default').removeClass('btn-success');
						clicado.find('.glyphicon').addClass('glyphicon-play').removeClass('glyphicon-ok-sign');

						// atualiza o contador
						clicado.parents('div').find('.numero-praticam').html(dados);
						clicado.parents('div').find('.pratico-contador').addClass('btn-default').removeClass('btn-success');
						

					}

					
				},
				error: function(errorThrown) {
				
				}

			});
		});




		/**
		 *	VOTA NAS ATIVIDADES POSSÍVEIS - MODAL - VIRAM
		 * 	A requisição possibilita o usuário votar nas atividades possíveis de se pratica no lugar, ao votar adiciona +1.
		 * 	O voto serve para legitimar a atividade, mostrando que ela realmente acontece naquele lugar.
		 *
		 * 	Request: ajax/atividades-possiveis-votacao-ajax.php
		 */
		$('#atividades-possiveis-descricao-modal .vi-botao').click(function(){

			var atividadeId = $(this).attr('atividade-id'); // dependente da função 'ABRE O 'MODAL' E REQUISITA OS COMETÁRIOS DA ATIVIDADE E SE O USUÁRIO PRATICA A ATIVIDADE'
			var clicado = $(this);

			$.ajax({
				url: '<?php echo $localiza_url; ?>/models/ajax/atividades-possiveis-votacao-ajax.php',
				type: 'POST',

				data: {
					'user-id': <?php echo $userID; ?>,
					'post-id': <?php echo $post->ID ?>,
					'atividade-id': atividadeId, // dependente da função 'ABRE O 'MODAL' E REQUISITA OS COMETÁRIOS DA ATIVIDADE E SE O USUÁRIO PRATICA A ATIVIDADE'
					'viram-ou-praticam':'viram'
				},
				success: function(dados){
					if(clicado && dados != ''){

						// atualiza o botão
						clicado.find('.btn-text').html('Eu já vi');
						clicado.addClass('btn-success').removeClass('btn-default');
						clicado.find('.glyphicon').addClass('glyphicon-ok-sign').removeClass('glyphicon-eye-open');

						// atualiza o contador
						clicado.parents('div').find('.numero-viram').html(dados);
						clicado.parents('div').find('.vi-contador').addClass('btn-success').removeClass('btn-default');

					}

					if(clicado && dados == '0'){

						// atualiza o botao
						clicado.find('.btn-text').html('Você viu?');
						clicado.addClass('btn-default').removeClass('btn-success');
						clicado.find('.glyphicon').addClass('glyphicon-eye-open').removeClass('glyphicon-ok-sign');

						// atualiza o contador
						clicado.parents('div').find('.numero-praticam').html(dados);
						clicado.parents('div').find('.vi-contador').addClass('btn-default').removeClass('btn-success');
						

					}

					
				},
				error: function(errorThrown) {
				
				}

			});
		});






	});




	</script>
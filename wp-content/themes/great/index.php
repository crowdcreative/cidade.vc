<?php $options = get_option('great'); ?>
<?php get_header(); ?>

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



		// Criação do mapa
		$('#map-canvas-home').gmap3({
			map: {
				options: {
					center: [-30.036363, -51.214786],
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
			}
		});

		var map = $("#map-canvas-home").gmap3("get");


	


  });

</script>

<div id="page" class="container">


		<div class="row">

		<?php include get_template_directory().'/sidebar-home.php'; ?>
		
		<article class="col-md-9">

			<div class="panel panel-default">
				<div id="map-canvas-home"></div>
			</div>

			<div class="panel panel-default">

				<div class="panel-heading">
					<h1 id="titulo" class="single-title panel-title">Ultimos lugares criados</h1>
				</div>

				<div class="panel-body">

					<div id="content_box">
					
					
						<?php 
						
						// Mais de um posttype em um Loop --> link: http://wordpress.stackexchange.com/quest
						global $query_string;
						$posts = query_posts( array( 'posts_per_page' => -1, 'post_type' => array('lugar')));

						
						?>
					
					
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						
						
							<?php if ('lugar' == get_post_type()){ ?>

							<?php  
								$latlong = get_field('latlong');
								$latlong = str_replace('(', '', $latlong);
								$latlong = str_replace(')', '', $latlong);
							?>

							<script type="text/javascript">
								// Adiciona o circulo no mapa
								$("#map-canvas-home").gmap3({
									marker: {
										latLng: [<?php echo $latlong ?>],
										options: {
											
										},
										events: {
							
										},
										callback: function() {
											
										}
									}
								});
							</script>

							<?php if(get_field('bairro')){ 
								$taxonomyID = get_field('bairro');
									$taxonomyID = (int)$taxonomyID;
									$taxonomy = get_term($taxonomyID, 'bairros'); 
								$bairro = $taxonomy->name;
								$descricao = get_field("descrição_do_lugar");
									$descricao = strip_tags($descricao);
									$descricao = substr($descricao, 0, 300);
								$postID =  get_the_ID(); 
							} ?>

						
							
							<div class="row">
							
								
								<div class="col-sm-3">
									<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow" id="featured-thumbnail">
									<?php if (get_field('imagem_de_capa')) { ?> 
										<img class="img-thumbnail" src="<?php the_field('imagem_de_capa') ?>">
									<?php } ?>
									</a>
								</div>
						
								<div class="col-sm-9">

									<div class="post-info"> <span class="uppercase"><?php $category = get_the_category(); echo '<a href="'.get_category_link($category[0]->cat_ID).'">' . $category[0]->cat_name .'</a>';?> </span><span><?php echo $bairro; ?></span><span style='display:none'>Compartilhado por <?php the_author_meta("display_name"); ?></span> </div>

									<h2 class="title">
										<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a><span class="leitura help" title="Tempo médio de leitura"><?php the_field("tempo_de_leitura"); ?></span>
									</h2>
										
									<div class="post-content image-caption-format-1">
										<p>
											<?php echo $descricao; ?>
											<a class="readMore" href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow">Ler mais...</a>
										</p>

										<span class="time"><?php echo time_ago(); ?></span>
									</div>
								</div>
								
							</div>

						<?php } ?>  
						
							
						<?php endwhile;  wp_reset_query(); else: ?>
							<div class="post excerpt">
								<div class="no-results">
									<p><strong><?php _e('There has been an error.', 'mythemeshop'); ?></strong></p>
									<p><?php _e('We apologize for any inconvenience, please hit back on your browser or use the search form below.', 'mythemeshop'); ?></p>
									<?php get_search_form(); ?>
								</div><!--noResults-->
							</div>
						<?php endif; ?>
						<?php if ($options['mts_pagenavigation'] == '1') { ?>
							<?php pagination($additional_loop->max_num_pages);?>
						<?php } else { ?>
							<div class="pnavigation2">
								<div class="nav-previous"><?php next_posts_link( __( '&larr; '.'Older posts', 'mythemeshop' ) ); ?></div>
								<div class="nav-next"><?php previous_posts_link( __( 'Newer posts'.' &rarr;', 'mythemeshop' ) ); ?></div>
							</div>
						<?php } ?>			
				</div>

				</div><!-- panel-body -->

				</div>
		</article>


		

		
		
<?php get_footer(); ?>
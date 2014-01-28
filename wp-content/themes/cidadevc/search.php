<?php $options = get_option('great'); ?>
<?php get_header(); ?>

<div id="page" class="container">


		<div class="row">

		<?php include get_template_directory().'/sidebar-home.php'; ?>
		
		<article class="col-md-9">

			<div class="panel panel-default">
				<div class="panel-body">
					<?php get_search_form(); ?>
				</div>
			</div>

			<div class="panel panel-default">

				<div class="panel-heading">
					<h1 id="titulo" class="single-title panel-title"><?php printf( __( 'Search Results for: %s', 'shape' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
				</div>

				<div class="panel-body">

					<div id="content_box">
					
						<?php /* The loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
						
						<?php 
							if ('lugar' == get_post_type()){ 
							$postID =  get_the_ID(); 
							$taxonomyID = get_field('bairro');
							$taxonomyID = (int)$taxonomyID;
							$taxonomy = get_term($taxonomyID, 'bairros'); 
							$bairro = $taxonomy->name;
						?>

							<div class="row">
							
								
								<div class="col-sm-3">
									<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow" id="featured-thumbnail">
									<?php if (get_field('imagem_de_capa')) { ?> 
										<img class="img-thumbnail" src="<?php the_field('imagem_de_capa') ?>">
									<?php } ?>
									</a>
								</div>
						
								<div class="col-sm-9">

									<div class="post-info col-sm-12"> <span class="uppercase"><?php $category = get_the_category(); echo '<a href="'.get_category_link($category[0]->cat_ID).'">' . $category[0]->cat_name .'</a>';?> </span><span><?php echo $bairro; ?></span><span style='display:none'>Compartilhado por <?php the_author_meta("display_name"); ?></span> </div>
								
									<h2 class="title">
										<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a><span class="leitura help" title="Tempo médio de leitura"><?php the_field("tempo_de_leitura"); ?></span>
									</h2>
										
									<div class="post-content image-caption-format-1">
										<?php the_field("descrição_do_lugar"); ?>
										<a class="readMore" href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow">Ler mais...</a><span class="time"><?php echo time_ago(); ?></span>
									</div>
								</div>
								
							</div>

						<?php } ?>


						<?php endwhile; ?>
							
					</div>

				</div><!-- panel-body -->

				</div>
		</article>


		

		
		
<?php get_footer(); ?>
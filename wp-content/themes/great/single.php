<?php get_header(); ?>
<?php $options = get_option('great'); ?>
<div id="page" class="single container">
	
		<div class="row">
			<div class="col-lg-8" >
				<div class="panel-default panel">
					<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
						<div id="post-<?php the_ID(); ?>" class="panel-body">
							<div class="single_post">
								<header>
									<h1 class="single-title"><?php the_title(); ?></h1>
									
								</header><!--.headline_area-->

								<div class="post-single-content box mark-links">

									<?php 
									global $post; 
									$postID = get_the_ID();
									?>
									
									<div class="bloco">
										<?php if(get_field("imagem_de_capa")){ ?>
											<p><img src="<?php the_field("imagem_de_capa"); ?>" class="img-thumbnail"/></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("endereço")){ ?>
											<h2>Endereço</h2>
											<p><?php the_field("endereço"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("telefone")){ ?>
											<h2>Telefone</h2>
											<p><?php the_field("telefone"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("site")){ ?>
											<h2>Site</h2>
											<p>
											<?php 
												$url = get_field("site"); 
												$url = NewUrl($url);
												echo($url);
											?>
											</p>
										<?php } ?>
									</div>

									<div class="bloco">
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

									<div class="bloco">
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
											<h2>Também são realizados:</h2>
											<p><?php the_field("tambem_são_realizados"); ?></p>
										<?php } ?>
									</div>

									<div class="bloco">
										<?php if(get_field("como_ter_acesso")){ ?>
											<h2>Como ter acesso</h2>
											<p><?php the_field("como_ter_acesso"); ?></p>
										<?php } ?>
									</div>

								</div>
							</div><!--.post-content box mark-links-->
							
					
					</div><!--.g post-->
					<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
				</div>
			</div>
	
		<?php get_sidebar('right'); ?>
<?php get_footer(); ?>
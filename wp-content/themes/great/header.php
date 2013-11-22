<?php 	header("Content-type: text/html; charset=utf-8");; 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php wp_title(''); ?></title>
	<?php mts_meta(); ?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_enqueue_script("jquery"); ?>
	<?php if ( is_singular() ) wp_enqueue_script('comment-reply'); ?>
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	
	<link href='http://fonts.googleapis.com/css?family=Gudea:400,400italic,700' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="./wp-content/themes/great/images/favicon.png">
	<link href="http://127.0.0.1/projects/cidade.vc/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">

	<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBiBbZGjRGFtFf4TpVs3CAip3iPBbvgrpU&sensor=true"></script>
	<script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/gmap3.js"></script>
	<!-- <script type="text/javascript" src="http://127.0.0.1/projects/cidade.vc/js/map.js"></script> -->
	<script src="http://127.0.0.1/projects/cidade.vc/bootstrap/js/bootstrap.js"></script>
	
	<?php mts_head(); ?>
	<?php wp_head(); ?>
	


</head>
<?php flush(); ?>
<body id ="blog" <?php body_class('main'); ?> data-offset='80' data-spy="scroll" data-target="#nav">


	
	<div id="bloqueio">
		<div class="conteudo">
			<img src=""/>
			<div class="fechar">X</div>
			<div class="topo">Ir para o topo</div>
		</div>
	</div>

	

	<div id="main-header" class="container">
		<div class="panel panel-default">
			<div class="panel-body">
				
						 	<div id="logo">
								<a href="<?php echo home_url(); ?>">Cidade.vc</a>
							</div><!-- END #logo -->
				
				
				

				<div id="orcamento">
					
				</div>
				
				
				
				
				
				<div class="secondary-navigation" style="display:none">
					<nav id="navigation" >
						<?php if ( has_nav_menu( 'primary-menu' ) ) { ?>
							<?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'menu', 'container' => '' ) ); ?>
						<?php } else { ?>
							<ul class="menu">
								<?php wp_list_categories('title_li='); ?>
							</ul>
						<?php } ?>
					</nav>
				</div>     
			</div>        
		</div><!--#header-->  
	</div>
	<?php if (is_single() || is_page()) { ?>
		
		<div class="container" style="display:none">	
			<div class="panel panel-default">
				<div id="breadcrumb"><?php the_breadcrumb(); ?></div>
			</div>
		</div>

	<?php } ?>

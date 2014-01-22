<?php 	header("Content-type: text/html; charset=utf-8");


	// pega as classes da API do Facebook para ser possível manipulá-lo
	if(!class_exists('Facebook')){
	  	require '/wp-content/plugins/nextend-facebook-connect/sdk/init.php';
	}
	
	// pega a url do template
	$localiza_url = get_template_directory_uri();

	### cria um token para ser verificado no envio de ajax e conteúdos
	### evita duplicação e inserção de dados não autorizados
	
	// verifica se o usuário está logado
	if(is_user_logged_in()){

		// inicia uma sessão
		session_start();

		//informa a sessão que o usuário está logado
		$_SESSION['logged'] = true;

		// cria um token de segurança caso ainda não exista
		if(!isset($_SESSION['token_security'])){

			// gera um 'token' aleatoriamente e armazena na sessão do usuário
		  	$_SESSION['token_security'] = md5(uniqid(rand(), true)); 

		}
	}

	else{

		// inicia uma sessão
		session_start();

		//informa a sessão que o usuário NÃO está logado
		$_SESSION['logged'] = false;

	}


	

  	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<title><?php wp_title(''); ?></title>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<?php wp_enqueue_script("jquery"); ?>
	<?php if ( is_singular() ) wp_enqueue_script('comment-reply'); ?>
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	
	<link href='http://fonts.googleapis.com/css?family=Gudea:400,400italic,700' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="./wp-content/themes/great/images/favicon.png">
	<link href="<?php echo $localiza_url ?>/css/bootstrap.css" rel="stylesheet" media="screen">

	<script type="text/javascript" src="<?php echo $localiza_url ?>/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $localiza_url ?>/js/expanding.js"></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBiBbZGjRGFtFf4TpVs3CAip3iPBbvgrpU&sensor=true"></script>
	<script type="text/javascript" src="<?php echo $localiza_url ?>/js/gmap3.js"></script>
	<script src="<?php echo $localiza_url ?>/js/bootstrap.js"></script>
	<script src="<?php echo $localiza_url ?>/js/customscript.js"></script>
	

	<?php wp_head(); ?>
	


</head>
<?php flush(); ?>
<body id ="blog" <?php body_class('main'); ?> data-offset='110' data-spy="scroll" data-target="#nav">


	
	<div id="bloqueio">
		<div class="conteudo">
			<img src=""/>
			<div class="fechar">X</div>
			<div class="topo">Ir para o topo</div>
		</div>
	</div>

	

	<div id="main-header" class="container">

	 	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
			<div class="row">
					<div class="col-md-3">
						<a class="navbar-brand" href="<?php echo home_url(); ?>">Cidade.vc</a>						
					</div>
					<div class="col-md-9">
						<div class="pull-left">
							<?php 
							$userID = get_current_user_id(); // pega id do usuário
							$userInfo = get_userdata($userID); // vê se está logado
							if (!is_user_logged_in()) {
							    echo '<span class="navbar-text"><a href="http://127.0.0.1/projects/cidade.vc/wp-login.php?loginFacebook=1&redirect=http://127.0.0.1/projects/cidade.vc" onclick="window.location = "http://127.0.0.1/projects/cidade.vc/wp-login.php?loginFacebook=1&redirect="+window.location.href; return false;">Entrar</a></span>';
							} else {
								echo '<span class="navbar-text" style="margin-right:0">'.get_avatar( $userID, 24).'</span>';
							    echo '<span class="navbar-text">'.$userInfo->first_name.'</span>';
							    echo '<span class="navbar-text"><a href="'.wp_logout_url(home_url()).'">Sair</a></span>';
							    echo '<span class="navbar-text" data-toggle="modal" data-target="#myModal">Qual o seu bairro?</span>';
							}
							?>
						</div>
						
							
					

						<div class="btn-group pull-right navbar-btn">
					        <button class="btn btn-primary" type="button">Criar um lugar</button>
					        
					        <button data-toggle="dropdown" style="height:34px" class="btn btn-primary dropdown-toggle" type="button">
					          	<span class="caret"></span>
					          	<span class="sr-only">Toggle Dropdown</span>
					        </button>

					        <ul role="menu" class="dropdown-menu">
					          	<li><a href="#">Criar um evento</a></li>
					          	<li class="divider"></li>
					          	<li><a href="#">Meu perfil</a></li>
					        </ul>
				      	</div>

					</div>
				</div>	
			</div>
		</nav>
		 
	</div>



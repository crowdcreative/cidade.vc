<?php

/*
Plugin Name: .htaccess Redirect
Plugin URI: http://wordpress.org/plugins/htaccess-redirect/
Description: This plugin modifies your .htaccess file to redirect requests to new locations. This is especially useful (and intended) to redirect requests to web locations/pages outside of your WordPress installation to pages now in WordPress.
Author: Aubrey Portwood
Version: 0.3.1
Author URI: http://aubreypwd.com

WP Ref:

Ref:
	http://stackoverflow.com/questions/8217430/how-to-redirect-only-when-exact-url-matches

*/

$htaccess = get_option('olr_htaccess').".htaccess";
$olr = get_option('olr');
$olr_comment = "#A redirect by .htaccess Redirect Plugin";

if(isset($_GET['htaccess'])){
	update_option('olr_htaccess',$_POST['htaccess']);
	header('location:tools.php?page=olr&deleted=true');	
}

function fixUolr($d){	
	$a = str_replace("&","\&",$d);		
	$a = str_replace("$","\$",$a);		
	$a = str_replace("^","\^",$a);			
	return $a;
}

if(isset($_GET['delete'])){
	$id = $_POST['id'];

	//Get which link and redirect to search for (same as below)
	$j=0;
	if(is_array($olr)) foreach($olr as $olr_item){
		$j++;
			if($j == $id){
				$link=$olr_item['link'];
				$redirect=$olr_item['redirect'];
			}
	}
	
	$htacces_error=NULL;
	if(isset($link) && isset($redirect)){
		//Remove it from htaccess
		$old_htaccess = file_get_contents($htaccess);
		$new_htaccess = str_replace("\n\n".$olr_comment."\nRedirectMatch 301 \"^".fixUolr(urldecode($link)).'$" "'.fixUolr(urldecode($redirect)).'"','',$old_htaccess);

			//write the changes
			$htacces_error = "0";
			$fh = fopen($htaccess, 'w')
				or $htacces_error="1";
					fwrite($fh, $new_htaccess);
			fclose($fh);
			
		//Take it out of the DB
		$c=0;
		if($htacces_error=="0"){
			foreach($olr as $olr_item){
				$c++;
					if($c != $id){ 
						$olr_new[]=$olr_item;
					}else{
						$olr_new=array();
					}
			} update_option('olr',$olr_new); 
		}
	}

		
	header("location:tools.php?page=olr&deleted=true&htaccess_error=$htacces_error");
}

function validateURL($URL) {
    $v = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
    return (bool)preg_match($v, $URL);
}

if(isset($_GET['save'])){
	$link = $_POST['link'];
	$redirect = $_POST['redirect'];
	
	//Return the link and redirect back to page
	$olr_linkredirectquery="&link=$link&redirect=$redirect";	
		
	//validate the $link before it's pathed
	if(!validateURL($link)){
		header("location:tools.php?page=olr&noturl=1$olr_linkredirectquery");
		exit();	
	}

	//strip domain/protocol from link
	$parsed = parse_url($link);
	$link = $parsed['path'];
	
	//validate $link if it's okay for htaccess
	if($link[0]!="/"){
		header("location:tools.php?page=olr&noturl=1$olr_linkredirectquery");
		exit();
	}
	
	//valideate $redirect safe with htaccess by making sure it's a url by checking its availibility
	//removed in place of validateURL below. Will remove completely when fully working.
	/*$error_level = error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		$fp = fopen($redirect, 'r');
			if (!$fp) {
				header("location:tools.php?page=olr&redirectnotactive=1$olr_linkredirectquery");
				exit();
			}
		fclose($fp);
	error_reporting($error_level);*/
	
	if(!validateURL($redirect)){
		header("location:tools.php?page=olr&noturl=1$olr_linkredirectquery");
		exit();		
	}
	
	//error checking
	if(!$parsed['path']){
		header("location:tools.php?page=olr&parsed=1$olr_linkredirectquery");
		exit();
	}
	if($link=='' || $redirect==''){
		header("location:tools.php?page=olr&novalue=1$olr_linkredirectquery");
		exit();
	}
	
	//Test if the redirect is already there
	$exists=false;

	if(is_array($olr)) foreach($olr as $olr_item){
		if($link == $olr_item['link']
		&& $redirect == $olr_item['redirect']
		) $exists=true; else $exists=false;
	}

	if(!$exists){
			//Add the RedirectMatch to the .htaccess file
			$old_htaccess = file_get_contents($htaccess);
			$new_htaccess = $old_htaccess 
				. "\n\n".$olr_comment."\nRedirectMatch 301 \"^".fixUolr(urldecode($link)).'$" "'.fixUolr(urldecode($redirect)).'"';
			
					//write the changes
					$htacces_error = "0";
					$fh = fopen($htaccess, 'w')
						or $htacces_error="1";
							fwrite($fh, $new_htaccess);
					fclose($fh);

			// Save the record in the DB
			if($htacces_error=="0"){
				$olr_add['link']=$link;
				$olr_add['redirect']=$redirect;
				$olr[]=$olr_add; //add it to olr array
				update_option('olr',$olr);
			}

		header("location:tools.php?page=olr&saved=true&htaccess_error=$htacces_error");
	}else{
		header("location:tools.php?page=olr&exists=1$olr_linkredirectquery");
	}
}

add_action('admin_menu', 'olr_admin'); function olr_admin(){
	add_submenu_page(
		'tools.php', 
		'.htaccess Redirect', 
		'.htaccess Redirect', 
		'manage_options', 
		'olr', 
		'olr_options'
	);
}

function olr_options(){
	global $olr;
	?>
		<style scoped>
			.links input[type="text"]{
				width: 200px;
			}
		</style>
		<div class="wrap">
		
			<?php if(isset($_GET['htaccess_error']) && $_GET['htaccess_error']=="1"): ?>
				<div class="error"><p>.htaccess Redirect couldn't write to your <code>.htaccess</code> file, please make sure it's writable and try again.</p></div>
			<?php endif; ?>
			
			<?php if(isset($_GET['exists']) && $_GET['exists']=="1"): ?>
				<div class="error"><p>Sorry, but that redirect already exists.</p></div>
			<?php endif; ?>

			<?php if(isset($_GET['novalue']) && $_GET['novalue']=="1"): ?>
				<div class="error"><p>Please make sure to provide valid values in both fields.</p></div>
			<?php endif; ?>

			<?php if(isset($_GET['noturl']) && $_GET['noturl']=="1"): ?>
				<div class="error"><p>One of the fields is not formatted properly. Please make sure and supply properly formatted URL's.<br><strong>Please do not use realtive paths, please use absolute URL's</strong>, they will be coverted automatically. <em>See tooltips on fields for help.</em></p></div>
			<?php endif; ?>
			
			<?php if(isset($_GET['parsed']) && $_GET['parsed']=="1"): ?>
				<div class="error"><p>You are trying to redirect a domain, .htaccess Redirect doesn't do that. Please provide a URL with a path, such as <code>http://example.com/my/path/to/file.html</code></p></div>
			<?php endif; ?>
			
			<h2>.htaccess Redirect</h2>
			<p>
				This plugin modifies your <code>.htaccess</code> file to redirect requests to new locations. This is especially useful (and intended) to redirect requests to web locations and pages outside of your WordPress installation to pages now in WordPress.

				For instance, you could redirect <code>http://example.com/old/raw/web/user/enethrie/my_web_page.html</code> to <code>http://example.com/enethrie/</code> or <code>http://somewhereelse.com/</code>
			</p>
	
			<h3>Direct path to .htaccess</h3>
			<form action="tools.php?page=olr&htaccess=true" method="post" class="links">
				<div class="error">
					<p><strong>Warning:</strong> Please only use this plugin if you know what you're doing and can edit your <code>.htaccess</code> file manually. .htaccess Redirect is currently in beta, and may cause problems for your install.</p>
				</div>
				<p>
					<input type="text" id="htaccess" name="htaccess" value="<?php echo get_option('olr_htaccess'); ?>">
					<input type="submit" value="Save">
					<small>Example: <?php echo str_replace("wp-content/plugins","",dirname(__FILE__)); ?> (keep trailing slash)</small>
				</p>
				<?php if(!file_exists(get_option('olr_htaccess').'.htaccess')): ?>
					<div class="error"><p>Couldn't find your <code>.htaccess</code> file, please check your settings.</p></div>					
				<?php else: ?>
					<div class="updated"><p>Found your <code>.htaccess</code> file, please make sure it's writeable (775).</p></div>
				<?php endif; ?>
			</form>
			
			<h3>Redirects</h3>
			<?php $c=0; if(is_array($olr) && sizeof($olr>0)) foreach($olr as $olr_item): $c++; ?>
				<form action="tools.php?page=olr&delete=true" method="post" class="links">
					<p>
						From <input type="text" name="link" id="link" disabled value="<?php echo $olr_item['link']; ?>" title="Note: the URL was reduced to a direct path on your site.">
						 to 
						<input type="text" name="redirect" id="redirect" disabled value="<?php echo $olr_item['redirect']; ?>">
						<input type="hidden" name="id" id="id" value="<?php echo $c; ?>">
						<input type="submit" value="Delete">						
					</p>
				</form>
			<?php endforeach; else echo "<p>No redirects</p>"; ?>
			
			<form action="tools.php?page=olr&save=true" method="post" class="links" style="border-top: 1px dotted #dadada">

				<p>										
					From <input type="text" name="link" id="link" title="Examples: http://example.com/location/, http://example.com/location/file.php" required type="url" value="<?php if(isset($_GET['link'])) echo $_GET['link'] ?>"> 

					to
					
					<input type="text" name="redirect" id="redirect" title="Examples: http://example.com, http://example.com/location/, http://example.com/location/file.html" required type="url" value="<?php if(isset($_GET['redirect']))  echo $_GET['redirect'] ?>">
					<input type="submit" value="Add">	
					
					<small><a id="fphelpt" href="javascript:jQuery('#fhelp').toggle();">Formatting?</a></small>
				</p>

			</form>

			<div style="">

				<div id="fhelp" style="display:none;">
					<p style="margin-left:20px;">
						<small>
							<strong>From:</strong> the from field must be a URL with a path to a directory or file, you <em>cannot</em> use URL's of domains like <code>http://example.com</code>, you must supply URL's like <code>http://example.com/path/to</code> or <code>http://example.com/path/to/file.html</code>. URL's in the from field will be automatically coverted to relative paths.
						</small>
					</p>
					<p style="margin-left:20px;">
						<small>
							<strong>To:</strong> the to field must be a URL, but does <strong>not</strong> have to supply a path. Domains can be used, like <code>http://example.com</code>.
						</small>
					</p>
				</div>
			</div>
			
			<?php if(file_exists(get_option('olr_htaccess').'.htaccess')): ?>
				<h3>Your .htaccess</h3>
				<p>
					<pre style="height:200px;overflow:auto;background:#eee;padding:10px;"><small><?php global $htaccess; echo $ht = htmlentities(file_get_contents($htaccess)); ?><small></pre>
				</p>
			<?php endif; ?>
			
		</div>
	<?php
}

?>
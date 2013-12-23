<?php




  	function conectado_facebook(){
		if($facebook->getUser()){
			echo 'Tá serto!';
		}else{
			echo 'Fail!';
		}
	}




















?>
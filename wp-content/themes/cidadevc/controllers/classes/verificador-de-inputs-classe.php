<?php

	/**
	 *	VERIFICADOR DE INPUTS - CLASSE
	 * 	esta classe serve para verificar o conteúdos dos inputs enviados via ajax, ele retorna
	 * 	se os testes deram ok, se der falso responde para o front-end e o jquery dá os alertas
	 * 	personalizados conforme a necessidade.
	 *
	 * 	Return: retorna uma array com boolean indicando se passaram ou não nos testes
	 *
	 */



	// verifica se a requisição está sendo feita via ajax
	if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){

		$ajax = true;

	}

	else{

		$ajax = false;

	}




	/**
	 * 	Classe: VerificadorInputs
	 */
	class VerificadorInputs{
		
		// cria os atributos
		public $conteudo;
		public $tamanho;
		public $tipo;

		// cria a array que abrigará as respostas
		public $resposta = array();

		// verifica o tipo
		public function VerificaTipo($tipoExigido){
	        
		    if(is_null($this->conteudo)){
		        $this->tipo = 'null';
		    }

		    elseif(is_string($this->conteudo)){
		        $this->tipo = 'string';
		    }

		    elseif(is_array($this->conteudo)){
		        $this->tipo = 'array';
		    }

		    elseif(is_int($this->conteudo)){
		        $input->tipo = 'integer';
		    }

		    elseif(is_bool($this->conteudo)){
		        $input->tipo = 'boolean';
		    }

		    elseif(is_float($this->conteudo)){
		        $input->tipo = 'float';
		    }

		    elseif(is_resource($this->conteudo)){
		        $input->tipo = 'resource';
		    }

		    else{
			    echo 'unknown'; 
			    exit();
		    }


		    if($this->tipo == $tipoExigido){

		    	$this->resposta['tipo'] = 'true';

		    }

		    else{

		    	$this->resposta['tipo'] = 'false';

		    }


		}


		public function VerificaTamanho($tamanhoMin = null, $tamanhoMax = null){
			
			if($tamanhoMax != null){
				if($this->tamanho <= $tamanhoMax){

					$this->resposta['tamanhoMax'] = 'true';

				}

				else{

					$this->resposta['tamanhoMax'] = 'false';

				}
			}


			if($tamanhoMin != null){			
				if($this->tamanho >= $tamanhoMin){

					$this->resposta['tamanhoMin'] = 'true';

				}

				else{

					$this->resposta['tamanhoMin'] = 'false';

				}
			}


		}


	}




	if($ajax){

		// cria o objeto
		$input = new VerificadorInputs;

		// define o atributo conteúdo que é o conteúdo enviado pelo input
		$input->conteudo = $_POST['conteudo'];

		// define o tamanho do conteúdo enviado pelo input
		$input->tamanho = strlen($input->conteudo);





		// verifica o tipo do conteúdo enviado pelo input
		$input->VerificaTipo($_POST['tipo']);

		// verifica o tamanho do conteúdo do input
		$input->VerificaTamanho($_POST['tamanhoMin'], $_POST['tamanhoMax']);


		echo json_encode($input->resposta);
	}

	else{
		return $input->resposta;
	}


?>
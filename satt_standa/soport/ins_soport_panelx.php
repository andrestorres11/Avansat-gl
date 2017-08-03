<?php
ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);
@session_start();
class SpgPanelControl
{
	var $cod_tercer, 
		$arr_datsol, 
		$_request,  
		$algorithm, 
		$key, 
		$iv_length,	
		$cConexion,
		$cCodAplica,
		$cUsuario,
		$cDespac;

	//function SpgPanelControl()
	//{
		//$this->Main();
	//}
	function __construct($co = NULL, $us = NULL, $ca = NULL){
		$this->cConexion = $co;
		$this->cUsuario = $us;		
		$this->cCodAplica = $ca;
		$this->Main();
	}

		function limpiar_caracteres_especiales($string) {
	 

	    $string = mb_convert_encoding($string, 'UTF-8','');
	 
	    $string = str_replace(
	        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
	        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
	        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
	        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
	        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
	        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
	        $string
	    );
	 
	    $string = str_replace(
	        array('ñ', 'Ñ', 'ç', 'Ç'),
	        array('n', 'N', 'c', 'C'),
	        $string
	    );
	  
	     
	    return $string;
	}
	
	function setArrDatSol(){
		$_SESSION["datos_usuario"]['nom_usuari'] = $this->limpiar_caracteres_especiales( $_SESSION['datos_usuario']['nom_usuari'] );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php' );
		$this->cDespac = new Despac( $this->cConexion, $this->cUsuario, $this->cCodAplica);
		$mUsuTransp = $this->cDespac->getTransp();


		$mTipErrorx = array();

		if( sizeof($mUsuTransp) == 1 )
			$mUsuTransp = $mUsuTransp[0];
		else{
			$mUsuTransp[0] = defined("NIT_TRANSPOR") ? NIT_TRANSPOR : "";
			$mUsuTransp[1] = defined("NOMSAD") ? NOMSAD : "";
		}
		//print_r($mUsuTransp);
		$this->arr_datsol=array(
			"cod_transp"=>$mUsuTransp[0],
			"nom_transp"=>$mUsuTransp[1],
			"url_direc_aplica"=>defined("URL_DIREC_APLICA") ? URL_DIREC_APLICA : "",
			"server_aplica"=>defined("URL_DIREC_APLICA") ? URL_DIREC_APLICA : "",
			"url_logspg"=>defined("URL_INTERF_SPG_PANEL") ? URL_INTERF_SPG_PANEL : "",
			"pwd_logspg"=>defined("PWD_LOGSPG") ? PWD_LOGSPG : "",
			"usr_logspg"=>defined("USR_LOGSPG") ? USR_LOGSPG : "",
			"nom_aplica"=>defined("BASE_DATOS") ? BASE_DATOS : "",
			"url_aplica"=>$_SERVER["PHP_SELF"],//url de retorno para respuesta
			"datos_usuario"=>@$_SESSION["datos_usuario"],
			"url_modules"=>defined("URL_INTERF_SPG_MODULES") ? URL_INTERF_SPG_MODULES : ""
		);
		//print_r($this->arr_datsol);die();
	}
	function Main()
	{
		//detener por no estar logeado
		if(!array_key_exists("datos_usuario", @$_SESSION)){
			die("Your session has expired!");
		}

		//DIR_APLICA_CENTRAL existe cuando esta embedido
		if(defined("DIR_APLICA_CENTRAL")){
			$file="../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc";
			$file2="../".DIR_APLICA_CENTRAL."/lib/ajax.inc";
		}else{
			$file="../lib/general/constantes.inc";
			$file2="../lib/ajax.inc";
		}
		if(file_exists($file) && file_exists($file2)){
			ob_start();
			include( $file );
			include( $file2 );
			ob_clean();
		}
		$error=array();
		if(!defined("URL_INTERF_SPG_PANEL")){
			array_push($error, "SpgPanelControl::Main > Const URL_INTERF_SPG_PANEL does not exists");
		}
		if(!defined("URL_INTERF_PCONTROL")){
			array_push($error, "SpgPanelControl::Main > Const URL_INTERF_PCONTROL does not exists");
		}
		if(!defined("PWD_LOGSPG")){
			array_push($error, "SpgPanelControl::Main > Const PWD_LOGSPG does not exists");
		}
		if(!defined("USR_LOGSPG")){
			array_push($error, "SpgPanelControl::Main > Const USR_LOGSPG does not exists");
		}
		if(!defined("NIT_TRANSPOR")){
			array_push($error, "SpgPanelControl::Main > Const NIT_TRANSPOR does not exists");
		}
		if(!defined("BASE_DATOS")){
			array_push($error, "SpgPanelControl::Main > Const BASE_DATOS does not exists");
		}
		if(!defined("USUARIO")){
			array_push($error, "SpgPanelControl::Main > Const USUARIO does not exists");
		}
		if(!defined("DIR_APLICA_CENTRAL")){
			array_push($error, "SpgPanelControl::Main > Const DIR_APLICA_CENTRAL does not exists");
		}
		/*if(!isset($AjaxConnection)){
			array_push($error, "SpgPanelControl::Main > Var AjaxConnection does not exists");	
		}*/
		if(sizeof($error)>0){
			print implode("<br>",$error);
			exit;
		}

		$this->setArrDatSol();
		
		$this->_request=isset($_REQUEST["cod_servic"]) || isset($_REQUEST["option"]) ? $_REQUEST : ( isset($GLOBALS["cod_servic"]) || isset($GLOBALS["option"]) ? $GLOBALS : null );
		$option=array_key_exists("option", $this->_request) ? $this->_request["option"] : 0;
		settype($option,"integer");
		
		$this->loadSpg();
	}

	function loadSpg(){
		/*$this->algorithm = 'rijndael-128';
	  	$this->key = md5( "intrared2017v1", true);
		$this->iv_length = mcrypt_get_iv_size( $this->algorithm, MCRYPT_MODE_CBC );*/
		header("Location: ".URL_INTERF_PCONTROL."?token=".$this->encrypt(json_encode($this->arr_datsol)));
	}
	
	function encrypt( $string ) {
	  /*$iv = mcrypt_create_iv( $this->iv_length, MCRYPT_RAND );
	  $encrypted = mcrypt_encrypt( $this->algorithm, $this->key, $string, MCRYPT_MODE_CBC, $iv );
	  $result = base64_encode(base64_encode( $iv . $encrypted ));
	  return $result;*/
	  return base64_encode(base64_encode($string));
	}
	//se deja solo para pruebas
	function decrypt( $string ) {
	  /*$string = base64_decode(base64_decode( $string ));
	  $iv = substr( $string, 0, $this->iv_length );
	  $encrypted = substr( $string, $this->iv_length );
	  $result = mcrypt_decrypt( $this->algorithm, $this->key, $encrypted, MCRYPT_MODE_CBC, $iv );
	  return $result;*/
	  return base64_decode(base64_decode($string));
	}
}

$proceso = new SpgPanelControl($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
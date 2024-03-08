<?php
//ini_set('error_reporting', E_ALL);
//ini_set("display_errors", 1);
error_reporting(0);
@session_start();
class InterfSolicitud {
	#Servidor para web service	
	var $wsdl_url = NULL;
	var $client;
	var $params;
	var $result;
	var $__result=array();
	var $mConexion;
	var $developer_mode=true;

	function InterfSolicitud(){
		$this->wsdl_url = $_SERVER['HTTP_HOST'] == 'oet-dev.intrared.net:8083' ? 'https://oet-dev.intrared.net:8083/ap/interf/app/faro/faro.php?wsdl':'https://oet-avansatgl.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl';
		$this->__load();
	}
	//function __construct(){
		//$this->__load();
	//}
	function __load(){
		//ob_start();
		$this->getGlobalConfig();
		//$this->getNusoap();
		$this->getService();
		//ob_clean();
		//$this->getServiceTest($this->wsdl_url);
	}

	function getGlobalConfig(){
		$error=array();
		if(!defined("DIR_APLICA_CENTRAL")){
			array_push($error, "InterfSolicitud::getGlobalConfig > Const DIR_APLICA_CENTRAL does not exists");
		}
		if(sizeof($error)>0){
			print implode("<br>",$error);
			exit;
		}
	}

	/*
	 * fn setRuta metodo que permite crear ruta
	 * Notas:
		//Editado = (E)
		arr_datsol(complejo)
			- cod_usuari: Usuario para la interfaz,
			- pwd_clavex: Clave para la interfaz,
			- cod_transp: Nit Transportadora
			- nom_aplica: Nombre aplicacion
			- url_aplica: URL del servidor para retorno (WS)
			- cod_solici: Codigo usuario en la aplicacion, codigo de avansat
			- nom_solici: nombre del solicitante
			- mai_solici: mail registrado al usuario en avansat
			- fij_solici: telefono en el usuario, digitable
			- cel_solici: celular del usuario, digitable
			-------------------------------------------------
			- (E)cod_tipsol - se agrega mantener el codigo del tab
			- (E)nom_tipsol - nombre que correspondiente al tab

			- (E)cod_usrsol: nombre de usuario del solicitante => para la db, pero en el xml sugiere que lo sea el atributo cod_solici que representa en la tabla tab_solici_datosx el id que le representa como id unico en el sistema de peticiones

		arr_rutsol (Complejo)
		- cod_ciuori: codigo ciudad, tab_genera_ciudad
		- cod_ciudes: codigo ciudad, tab_genera_ciudad
		- nom_viaxxx: texto digitado por el usuario

		- Se notifica esta inconsitencia semantica y Nelson escoje que se nombre como arr_datsol y arr_rutsol respectivamente
		- en el documento suguieren colocar a datUsuari identificar el complejo tns del usuario, pero describen posteriormente al atributo arr_datsol
		- en el documento suguieren colocar a datRutasx identificar el complejo tns la ruta que solicitan, pero describen posteriormente al atributo arr_rutsol para describirla

	*/	
	function setSoliciRuta($arr_datsol,$arr_rutsol){
		/*echo "InterfSolicitud > setRuta > ";
		echo "arr_datsol > ";
		print_r($arr_datsol);
		echo "arr_rutsol > ";
		print_r($arr_rutsol);
		echo "<br>";*/

		//<xsd:element name="arr_datsol" minOccurs="0" maxOccurs="1" type="tns:datUsuari"/>
		//<xsd:element name="arr_rutsol" minOccurs="0" maxOccurs="1" type="tns:datRutasx"/>
		//$consulta = new Consulta( $sql, $this->conexion );

		$params=array("arr_datsol"=>$arr_datsol, "arr_rutsol"=>$arr_rutsol);
		$responseWS = $this->setFnService("setSoliciRuta",$params);
	}
	function setSoliciSegimientoEspecial($arr_datsol,$ind_segesp,$fec_iniseg,$fec_finseg,$lis_placax,$obs_solici){
		/*echo "InterfSolicitud > setSoliciSegimientoEspecial > ";
		echo "\narr_datsol > ";
		echo "\n<br>";
		print_r($arr_datsol);
		echo "\nind_segesp > ";
		echo "\n<br>";
		print_r($ind_segesp);
		echo "\nfec_iniseg > ";
		echo "\n<br>";
		print_r($fec_iniseg);
		echo "\nfec_finseg > ";
		echo "\n<br>";
		print_r($fec_finseg);
		echo "\nlis_placax > ";
		echo "\n<br>";
		print_r($lis_placax);
		echo "\n<br>";
		echo "\nobs_solici > ";
		echo "\n<br>";
		print_r($obs_solici);
		echo "\n<br>\n";
		*/
		$params=array(
			"arr_datsol"=>$arr_datsol, 
			"ind_segesp"=>$ind_segesp,
			"fec_iniseg"=>$fec_iniseg,
			"fec_finseg"=>$fec_finseg,
			"lis_placax"=>$lis_placax,
			"obs_solici"=>base64_encode($obs_solici)
		);
		$this->setFnService("setSoliciSegimientoEspecial",$params);
	}
	function setSoliciPQR($arr_datsol, $ind_pqrsxx, $nom_pqrsxx, $obs_pqrsxx, $fil_archiv){
		/*echo "InterfSolicitud > setSoliciPQR > ";
		echo "arr_datsol > ";
		print_r($arr_datsol);
		echo "ind_pqrsxx > ";
		print_r($ind_pqrsxx);
		echo "nom_pqrsxx > ";
		print_r($nom_pqrsxx);
		echo "obs_pqrsxx > ";
		print_r($obs_pqrsxx);
		echo "fil_archiv > ";
		print_r($fil_archiv);
		echo "<br>";*/
		$params=array(
			"arr_datsol"=>$arr_datsol, 
			"ind_pqrsxx"=>$ind_pqrsxx,
			"nom_pqrsxx"=>base64_encode($nom_pqrsxx),
			"obs_pqrsxx"=>base64_encode($obs_pqrsxx),
			"fil_archiv"=>$fil_archiv
		);
		$this->setFnService("setSoliciPQR",$params);
	}
	function setSoliciOtros($arr_datsol, $nom_otroxx, $obs_otroxx, $fil_archiv){
		/*echo "InterfSolicitud > setSoliciOtros > ";
		echo "arr_datsol > ";
		print_r($arr_datsol);
		echo "nom_otroxx > ";
		print_r($nom_otroxx);
		echo "obs_otroxx > ";
		print_r($obs_otroxx);
		echo "fil_archiv > ";
		print_r($fil_archiv);
		echo "<br>";*/
		$params=array(
			"arr_datsol"=>$arr_datsol, 
			"nom_otroxx"=>base64_encode($nom_otroxx),
			"obs_otroxx"=>base64_encode($obs_otroxx),
			"fil_archiv"=>$fil_archiv
		);
		$this->setFnService("setSoliciOtros",$params);
	}
	function getNusoap(){

		if(stripos($_SERVER["PHP_SELF"],DIR_APLICA_CENTRAL)>-1){
			$file='../lib/nusoap095/nusoap.php';
			$file2='../lib/nusoap095/class.soap_transport_http.php';
			$file3='../lib/nusoap095/class.wsdl.php';
			$file4='../lib/nusoap095/class.wsdlcache.php';
		}else{
			$file='../'.DIR_APLICA_CENTRAL.'/lib/nusoap095/nusoap.php';
			$file2='../'.DIR_APLICA_CENTRAL.'/lib/nusoap095/class.soap_transport_http.php';
			$file3='../'.DIR_APLICA_CENTRAL.'/lib/nusoap095/class.wsdl.php';
			$file4='../'.DIR_APLICA_CENTRAL.'/lib/nusoap095/class.wsdlcache.php';
		}

		if(file_exists($file) && file_exists($file2) && file_exists($file3) && file_exists($file4)){
			ini_set("soap.wsdl_cache_enabled", "0");
			include_once($file);
			include_once($file2);
			include_once($file3);
			include_once($file4);	
		}else{
			echo "<h5>Error, failed load $file,$file2,$file3,$file4 files</h5>";
			exit;
		}
	}

	function getService(){
		try
		{
			#llamado SoapClient Nativo
			$this->client = new SoapClient($this->wsdl_url, array( "encoding" => "utf8"));
		}
		catch(soapFault $fault)
		{
			echo "<h2>Constructor error</h2><pre>";print_r($fault);echo "</pre>";
			//echo "<h2>Constructor error</h2><pre>" . $fault . "</pre>";
		    exit;
		}
		/*$this->client = new nusoap_client($this->wsdl_url,true);
		$error = $this->client->getError();
		if ($error) {
		    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
		    exit;
		}*/
	}

	function getResult(){
		return $this->__result;
	}

	function setFnService($fn,$params){
		try
		{
			if(isset($params)){
				$this->result = $this->client->__soapCall($fn,$params);
			}else{
				$this->result = $this->client->__soapCall($fn);
			}
			$this->__request = htmlspecialchars($this->client->__getLastRequest(), ENT_QUOTES);
			$this->__response = htmlspecialchars($this->client->__getLastResponse(), ENT_QUOTES);
			//$this->__debug = htmlspecialchars($this->client->getDebug(), ENT_QUOTES);
			//$this->__cacheDebug = htmlspecialchars($this->cache->getDebug(), ENT_QUOTES);
			
			$this->__wsLastRqRp=array();
			$this->__wsLastRqRp["response"]=$this->result;
			$this->__wsLastRqRp["ws"]=array();
			$this->__wsLastRqRp["ws"]["set"]=array();
			$this->__wsLastRqRp["ws"]["get"]=array();
			$this->__wsLastRqRp["ws"]["set"]["fn"]=$fn;
			$this->__wsLastRqRp["ws"]["set"]["params"]=$params;
			$this->__wsLastRqRp["ws"]["set"]["request"]=$this->__request;
			$this->__wsLastRqRp["ws"]["get"]["response"]=$this->__response;
			$this->__wsLastRqRp["ws"]["get"]["error"]=null;
			$this->__wsLastRqRp["ws"]["get"]["fault"]=null;
			$this->__wsLastRqRp["ws"]["status"]="success";
			$this->__wsLastRqRp["ws"]["time"]=date("Y-m-d H:i:s",time());
			array_push($this->__result,array("response"=>$this->__wsLastRqRp["response"]));
			/*if($this->getFnServiceException()){
				array_push($this->__result,$this->__wsLastRqRp);
			}else{
				if($this->developer_mode==true){
					array_push($this->__result,$this->__wsLastRqRp);
				}else{
					array_push($this->__result,array("response"=>$this->__wsLastRqRp["response"]));
				}
			}*/
		}
		catch (soapFault $fault)
		{
			echo "<pre>";print_r($fault);echo "</pre>";
			//array_push($this->__result,array("response"=>$fault->message()));
			$this->__wsLastRqRp["ws"]["db"]=array();
			$this->__wsLastRqRp["ws"]["db"]["tab_errorx_solici"]=true;
		}
	}

	function getFnServiceException(){
		$this->__fault = null;
		$this->__error = null;
		if ($this->client->fault) {
			$this->__fault = $this->result;
		} else {
			$__error = $this->client->getError();
			if($__error){
				$this->__error = $__error;
			}
		}
		if( is_null($this->__fault) && is_null($this->__error) ){
			return false;
		}else{
			$this->__wsLastRqRp["ws"]["status"]="error";
			$this->__wsLastRqRp["ws"]["get"]["error"]=$this->__error;
			$this->__wsLastRqRp["ws"]["get"]["fault"]=$this->__fault;
			$this->saveError();
			return true;
		}
	}

	function setTest(){
		$params=array(
			"nom_usuari" => "soporte",
		    "pwd_clavex" => "ifx2016"
	    );
		$this->setFnService("getNovedades",$params);
	}

	function saveError(){

/*
- cod_consec: INT(6), PK
- cod_tipsol: INT(1), not_null
- xml_reques: text, NOT NULL
- xml_respon: text, NOT NULL
- cod_respon: INT(4), NOT NULL
- msg_respon: VARCHAR(100), NOT NULL
- usr_creaci:VARCHAR(20), NOT NULL
- fec_creaci: DATETIME, NOT NULL
*/

/*
		$this->__wsLastRqRp=array();
		$this->__wsLastRqRp["response"]=$this->result;
		$this->__wsLastRqRp["ws"]=array();
		$this->__wsLastRqRp["ws"]["set"]=array();
		$this->__wsLastRqRp["ws"]["get"]=array();
		$this->__wsLastRqRp["ws"]["set"]["fn"]=$fn;
		$this->__wsLastRqRp["ws"]["set"]["params"]=$params;
		$this->__wsLastRqRp["ws"]["set"]["request"]=$this->__request;
		$this->__wsLastRqRp["ws"]["get"]["response"]=$this->__response;
		$this->__wsLastRqRp["ws"]["get"]["error"]=null;
		$this->__wsLastRqRp["ws"]["get"]["fault"]=null;
		$this->__wsLastRqRp["ws"]["status"]="success";
		$this->__wsLastRqRp["ws"]["time"]=date("Y-m-d H:i:s",time());
*/
		$this->__wsLastRqRp["ws"]["db"]=array();
		$this->__wsLastRqRp["ws"]["db"]["tab_errorx_solici"]=true;
	}
}
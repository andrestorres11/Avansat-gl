<?php
//	Archivo php para consumir webservice para retornar los datos de las llamadas que se hacen desde los controladores de faro
//  con Zoiper
//	author: Nelson Liberato
//	date: 2015-03-21
//
Ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
ini_set("soap.wsdl_cache_enabled", 0);
#echo phpinfo();die();
class WsdlCall
{
	#Variables de la clase
	private $cUrlWsdl = 'https://web10.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl';
	private $cUsrCall = 'InterfIndCall';
	private $cPasCall = 'U4f=)Ja.0';
	private $cTokCall = '*K4czOUZxtt{Y|ND5c=q';
	 # Constructor
	function __construct()
	{
 
		try
		{

			$mParam = array("usr_callxx"=>$this -> cUsrCall,
				            "clv_callxx"=>$this -> cPasCall,
				            "tok_callxx"=>$this -> cTokCall,
				            "num_despac"=>$_REQUEST["num_despac"],
				            "num_placax"=>$_REQUEST["num_placax"],
				            "num_telefo"=>$_REQUEST["num_telefo"],
				            "tie_duraci"=>$_REQUEST["tie_duraci"],
				            "idx_llamad"=>$_REQUEST["idx_llemad"],
				            "nom_estado"=>$_REQUEST["nom_estado"],
				            "rut_audiox"=>$_REQUEST["rut_audiox"]
				            );

		    #echo "<pre>PARAMETROS:"; print_r($mParam); echo "</pre>";

			$mSoap = new SoapClient($this -> cUrlWsdl, array("encoding"=>"UTF-8"));

 
			$mResult = $mSoap -> __soapCall("RegistrarCall", $mParam);

			echo "<pre>Result:"; print_r($mResult); echo "</pre>";

			if($mResult -> cod_respon != '1000')
				throw new SoapFault( $mResult -> cod_respon, $mResult -> msg_respon);

			$mParam["tok_callxx"] = '.|.';
			mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net, miguel.romero@intrared.net, fabian.salinas@intrared.net", 
				 "Registro Exitoso llamadas CallCenter Despacho:".$_REQUEST["num_despac"],  
				 var_export($mParam, true));
				
	 
		}
		catch(SoapFault $e)
		{
			echo "<pre>"; print_r("Catch: ".$e -> faultstring); echo "</pre>";
			$mParam["tok_callxx"] = '.|.';
			mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net, miguel.romero@intrared.net, fabian.salinas@intrared.net", 
				 "Error registro llamadas CallCenter Despacho:".$_REQUEST["num_despac"],  
				 "\nCatch:\nCodigo:".$e -> faultcode." - Mensaje: ".$e -> faultstring."\nDatos Wsdl:\n".var_export($mParam, true));
		}
	}
}
$mCall = new WsdlCall();

?>

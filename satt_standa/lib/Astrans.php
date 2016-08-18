<?php

echo "<pre><hr><center>"; print_r("Inicia"); echo "</center><hr></pre>";


if(!$_GET ) {
	die("Parametro invalido");
}
else {
	switch ($_GET["action"]) {
		case 'cargue':
			 cargue();
		break;		
		case 'remesa':
			 remesa();
		break;		
		case 'remision':
			 remision();
		break;		
		default:
			die("Sin parametro");
		break;
	}
}


function cargue()
{
	
	try 
	{		
		$cUrlAstrans = "http://200.32.81.202:8060/ServicioViajes.svc?wsdl";
		$cUrlAstrans = "http://200.32.81.202:8060/ServicioViajes.svc?singleWsdl";

		$mParams["NumeroViaje"] = 'VJ-238853';
		$mParams["CodigoEvento"] = 'SP';
		$mParams["Fecha"] = '2015/01/02 08:00:00';
		$mParams["CodigoNovedad"] = '255';
		$mParams["DescripcionNovedad"] = '31 CUMPLE CITA SE PRESENTO A LASS 006 00 ANM prueba a produccion de OET';


		echo "<pre><hr>"; print_r("DATA"); echo "</pre>";
		echo "<pre>";     print_r($mParams); echo "<hr></pre>";
		echo "<pre>URL:";     print_r($cUrlAstrans); echo "<hr></pre>";


	    #$oSoapClient = new SoapClient( $cUrlAstrans,array( 'cache_wsdl'=>WSDL_CACHE_NONE, 'trace'   => 1,   'encoding' => 'UTF-8',  'soap_version' => SOAP_1_2 , 'exceptions' => 1 ) );
	    $oSoapClient = new SoapClient( $cUrlAstrans,array(  'trace'   => 1,   'encoding' => 'UTF-8' /*,  'soap_version' => SOAP_1_2 */, 'exceptions' => 1 ) );
        # Coloca Los Header del Webservice ----------------------------------------------------------------------------------------------------
        #$actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action','http://tempuri.org/IServicioViajes/RegistrarCargue');    
        #$actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'To'    ,'https://astrans.coronaindustrial.net:8065/ServicioViajes.svc');
        #$oSoapClient->__setSoapHeaders($actionHeader);
        # -------------------------------------------------------------------------------------------------------------------------------------
        # Coloca los datos en el BODY ---------------------------------------------------------------------------------------------------------
        $mParam   = new SoapVar( '<ns1:RegistrarCargue xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                         <ns1:cargue>
                            <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                            <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                            <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                            <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                            <sim:DescripcionNovedad>'.$mParams["DescripcionNovedad"].'</sim:DescripcionNovedad>
                         </ns1:cargue>
                      </ns1:RegistrarCargue>',  XSD_ANYXML, SOAPStruct,NULL,NULL);
         $mResult = $oSoapClient -> RegistrarCargue( new soapParam($mParam, "RegistrarCargue"));

  
         echo "<pre><hr>Last Request:<br>"; print_r( htmlspecialchars( $oSoapClient ->__getLastRequest() ) ); echo "</pre>";

         echo "<hr><pre>Response RegistrarCargue SoapClient:<br>"; print_r($mResult); echo "</pre>";
	} 
	catch (Exception $e) 
	{
		echo "<pre><hr>Last Request Catch:<br>"; print_r($oSoapClient ->__getLastRequest() ); echo "</pre>";
		echo "<pre><hr>Exception:<br>"; print_r($e); echo "</pre>";
	}
}


function remesa()
{	
	try 
	{		
		$cUrlAstrans = "http://200.32.81.202:8060/ServicioViajes.svc?wsdl";


        $mValue["num_despac"] = "VJ-360696";
        $mValue["num_docalt"] = "RE-330303";
        $mValue["fec_cumdes"] = "2014/10/01 00:00:00";
        $mValue["nov_cumdes"] = "256";
        $mValue["obs_cumdes"] = "34>OK CUMPLE CITA A LAS 6 AM, prueba de pros OET a Astrans";

     
        $mParams = array(
                              "NumeroViaje"=> $mValue["num_despac"],
                              "NumeroRemesa"=>$mValue["num_docalt"],
                              "CodigoEvento"=> "FD" ,
                              "Fecha"=> date_format(date_create($mValue["fec_cumdes"]), 'Y/m/d H:i:s'),
                              "CodigoNovedad"=> $mValue["nov_cumdes"],
                              "DescripcionNovedad"=> $mValue["obs_cumdes"]
                            );    


		echo "<pre><hr>"; print_r("DATA"); echo "</pre>";
		echo "<pre>";     print_r($mParams); echo "<hr></pre>";
		echo "<pre>URL:";     print_r($cUrlAstrans); echo "<hr></pre>";


	    #$oSoapClient = new SoapClient( $cUrlAstrans,array( 'cache_wsdl'=>WSDL_CACHE_NONE, 'trace'   => 1,   'encoding' => 'UTF-8',  'soap_version' => SOAP_1_2 , 'exceptions' => 1 ) );
	    $oSoapClient = new SoapClient( $cUrlAstrans,array(  'trace'   => 1,   'encoding' => 'UTF-8' /*,  'soap_version' => SOAP_1_2 */, 'exceptions' => 1 ) );
        # Coloca Los Header del Webservice ----------------------------------------------------------------------------------------------------
        #$actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action','http://tempuri.org/IServicioViajes/RegistrarCargue');    
        #$actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'To'    ,'https://astrans.coronaindustrial.net:8065/ServicioViajes.svc');
        #$oSoapClient->__setSoapHeaders($actionHeader);
        # -------------------------------------------------------------------------------------------------------------------------------------
        # Coloca los datos en el BODY ---------------------------------------------------------------------------------------------------------
        $mParam   = new SoapVar( '<ns1:RegistrarDescargueRemesa xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
			                         <ns1:descargueRemesa>
			                            <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
			                            <sim:NumeroRemesa>'.$mParams["NumeroRemesa"].'</sim:NumeroRemesa>
			                            <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
			                            <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
			                            <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
			                            <sim:DescripcionNovedad>'.$mParams["DescripcionNovedad"].'</sim:DescripcionNovedad>
			                         </ns1:descargueRemesa>
			                      </ns1:RegistrarDescargueRemesa>',  XSD_ANYXML, SOAPStruct,NULL,NULL);

         $mResult = $oSoapClient -> RegistrarDescargueRemesa( new soapParam($mParam, "RegistrarDescargueRemesa"));

  
         echo "<pre><hr>Last Request:<br>"; print_r( htmlspecialchars( $oSoapClient ->__getLastRequest() ) ); echo "</pre>";

         echo "<hr><pre>Response RegistrarDescargueRemesa SoapClient:<br>"; print_r($mResult); echo "</pre>";
	} 
	catch (Exception $e) 
	{
		echo "<pre><hr>Last Request:<br>"; print_r($oSoapClient ->__getLastRequest() ); echo "</pre>";
		echo "<pre><hr>Exception:<br>"; print_r($e); echo "</pre>";
	}	
}

function remision()
{
	try 
	{		
		$cUrlAstrans = "http://200.32.81.202:8060/ServicioViajes.svc?wsdl";


        $mValue["num_despac"] = "VJ-391080";
        $mValue["num_docume"] = "C1-5119535";
        $mValue["cod_evento"] = "LD";
        $mValue["fec_cumdes"] = "2015/06/10 20:37:00";
        $mValue["nov_cumdes"] = "256";
        $mValue["obs_cumdes"] = "30 CUMPLIO CITA prueba de OET porduccion a Astrans";
      
        $mNoveda = array( "LD", "ID", "FD" );
        $mParams = array(
                            "NumeroViaje"=> $mValue["num_despac"],                                    
                            "NumeroRemision"=> $mValue["num_docume"],
                            "CodigoEvento"=> $mNoveda[ rand(0, 2) ],
                            "Fecha"=> date_format(date_create($mValue["fec_cumdes"]), 'Y/m/d H:i:s'),
                            "CodigoNovedad"=> $mValue["nov_cumdes"],
                            "DescripcionNovedad"=> $mValue["obs_cumdes"]  
                          );  

		echo "<pre><hr>"; print_r("DATA"); echo "</pre>";
		echo "<pre>";     print_r($mParams); echo "<hr></pre>";
		echo "<pre>URL:";     print_r($cUrlAstrans); echo "<hr></pre>";


	    #$oSoapClient = new SoapClient( $cUrlAstrans,array( 'cache_wsdl'=>WSDL_CACHE_NONE, 'trace'   => 1,   'encoding' => 'UTF-8',  'soap_version' => SOAP_1_2 , 'exceptions' => 1 ) );
	    $oSoapClient = new SoapClient( $cUrlAstrans,array(  'trace'   => 1,   'encoding' => 'UTF-8' /*,  'soap_version' => SOAP_1_2 */, 'exceptions' => 1 ) );
        # Coloca Los Header del Webservice ----------------------------------------------------------------------------------------------------
        #$actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action','http://tempuri.org/IServicioViajes/RegistrarCargue');    
        #$actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'To'    ,'https://astrans.coronaindustrial.net:8065/ServicioViajes.svc');
        #$oSoapClient->__setSoapHeaders($actionHeader);
        # -------------------------------------------------------------------------------------------------------------------------------------
        # Coloca los datos en el BODY ---------------------------------------------------------------------------------------------------------
        $mParam   = new SoapVar( '<ns1:RegistrarDescargueRemision xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
			                         <ns1:descargueRemision>
			                            <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
			                            <sim:NumeroRemision>'.$mParams["NumeroRemision"].'</sim:NumeroRemision>
			                            <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
			                            <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
			                            <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
			                            <sim:DescripcionNovedad>'.$mParams["DescripcionNovedad"].'</sim:DescripcionNovedad>
			                         </ns1:descargueRemision>
			                      </ns1:RegistrarDescargueRemision>',  XSD_ANYXML, SOAPStruct,NULL,NULL);



         $mResult = $oSoapClient -> RegistrarDescargueRemision( new soapParam($mParam, "RegistrarDescargueRemision"));

  
         echo "<pre><hr>Last Request:<br>"; print_r( htmlspecialchars( $oSoapClient ->__getLastRequest() ) ); echo "</pre>";

         echo "<hr><pre>Response RegistrarDescargueRemision SoapClient:<br>"; print_r($mResult); echo "</pre>";
	} 
	catch (Exception $e) 
	{
		echo "<pre><hr>Last Request:<br>"; print_r($oSoapClient ->__getLastRequest() ); echo "</pre>";
		echo "<pre><hr>Exception:<br>"; print_r($e); echo "</pre>";
	}	
}


?>
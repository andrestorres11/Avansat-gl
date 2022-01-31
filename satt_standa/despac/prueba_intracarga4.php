<?php

    echo "<h2>Inicio Prueba PHP 4</h2><br>";
    require_once("/var/www/html4/ap/satt_standa/lib/nusoap095/nusoap.php");

    $oSoapClient = new nusoap_client( 'http://www.intracarga.com.co/actualizartrafico/service1.asmx?wsdl', true );
    
    $err = $oSoapClient->getError();
    if ($err) {
      echo 'Constructor error<pre>' . $err . '</pre>';
    }

    $fecha = '2010-09-24 08:50:50';
    
    //$usuario = new soapval('A_STRING', 'string', "faro");
    
    $parametros = array( "ReportaPuestoControl" => array( "Usuario" => "faro", 
                                                           "Clave" => "faro2010", 
                                                           "Manifiesto" => "00002", 
                                                           "Fecha" => date( 'Y-m-d',strtotime( $fecha )), 
                                                           "Hora" => date( 'H:i:s',strtotime( $fecha )), 
                                                           "Puesto_control" => "bogota", 
                                                           "Observaciones" => "Prueba desde php4 Intrared 24 09" ) );
    echo "<pre>";
		  print_r( $parametros );
    echo "</pre>";
    
    $respuesta = $oSoapClient -> call( "ReportaPuestoControl", $parametros );
    echo "<hr>";
    print_r( $respuesta );
    
    echo "<hr><pre>";
		   print_r($oSoapClient->request);
    echo "</pre>";
   
    
    if ( $oSoapClient->fault ) {
      echo '<h2>Falla</h2><pre>';
        print_r($respuesta);
      echo '</pre>';
    }
    
    echo "<br/>Fin Prueba";

?>
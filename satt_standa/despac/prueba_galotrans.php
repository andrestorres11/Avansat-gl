<?php

    echo "<h2>Inicio Prueba PHP 4</h2><br>";
     

    
    
    $fecha = '2010-09-24 08:50:50';
    
    //$usuario = new soapval('A_STRING', 'string', "faro");
    
    $parametros = array (
                          'empresa' => 'LOEXA',
                          'usuario' => 'Oet',
                          'clave' => '900204510',
                          'novedad' => 'Ok Vehiculo sin novedad EL CONDUCTOR INFORMA QUE SE ENCUENTRA EN OPON',
                          'hora' => '17:35:00',
                          'fecha' => '2014-09-17',
                          'placa' => 'SVM388',
                          'manifiesto_codigo' => '0101018754',
                          'lugar' => 'PUERTO  ARAUJO'
                        );
 


    echo "<pre>";
      print_r( $parametros );
    echo "</pre>";
    $oSoapClient = new SoapClient('http://www.colombiasoftware.net/base/webservice/reportepuestocontrol.php?wsdl',array('encoding'=>'ISO-8859-1') );
    
    $respuesta = $oSoapClient -> __call( 'novedades_humadea', $parametros );



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
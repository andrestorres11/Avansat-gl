<?php
  ini_set("soap.wsdl_cache_enabled", "0"); //disabling WSDL cache

  echo "Webservice: avansatgl.intrared.net quiere consumir un WS en avansat6.intrared.net, pero este no responde \n";
  try
  {
 
          ini_set("default_socket_timeout", 15);
          $oSoapClient = new soapclient( 'https://avansat6.intrared.net:8083/ap/interf/app/sat/wsdl/sat.wsdl', array( "trace" => "1", 
                                                                                                                      'encoding'=>'ISO-8859-1', 
                                                                                                                      'connection_timeout' =>  15
                                                                                                                    ) 
                                       );
      
          $parametros = array(  
                              'nom_usuari' => '',  
                              'pwd_clavex' => '', 
                              'nom_aplica' => '', 
                              'num_manifi' => '', 
                              'num_placax' => '', 
                              'cod_novbas' => '', 
                              'cod_conbas' => '', 
                              'tim_duraci' => '', 
                              'fec_noveda' => '', 
                              'des_noveda' => '', 
                              'nom_contro' => '', 
                              'nom_sitiox' => '', 
                              'cod_confar' => '', 
                              'cod_novfar' => '', 
                              'nom_noveda' => '', 
                              'ind_alarma' => '', 
                              'ind_tiempo' => '', 
                              'nov_especi_' => '',
                              'ind_manala' => '', 
                              'bin_fotcon' => '', 
                              'bin_fotpre' => '',
                              );

 
                                                                  
          $result = $oSoapClient ->__call( 'setNovedadNC', $parametros );  
          echo "<pre>";
          print_r( $result ); 
          echo "</pre>";
    
//include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.

/*define( "BASE_DATOS", "satt_faro" );

    $fDespac = new DespacSat( $fExcept );

    $fCodRutaxx = $fDespac -> getRuta( "173", "11001000", "23001000", "BOGOTA - MONTERIA" );//Retorna el codigo de ruta exacto que cumpla con los parametros de entrada.

    var_dump( $fCodRutaxx );
*/
    /* *
    echo "<pre>";
      var_dump( $client -> __getLastRequest() );
    echo "</pre>";

    echo "<pre>";
      var_dump( $result );
    echo "</pre>";
    /* */
  }
  catch( SoapFault $e )
  {
   echo '<br>Ocurrio un error: '.$e -> getMessage();
  }

  echo "<br>Chao Mundo";
?>

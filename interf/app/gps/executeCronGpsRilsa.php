<?php
  /***********************************************************************
  * @file server.php                                                     *
  * @brief Cron inserta Novedades de GPS Rilsa                           *
  * @version 0.1                                                         *
  * @date 09 de Marzo de 2011                                            *
  * @modified 20 de Noviembre de 2013                                    *
  * @author Hugo Malagon.                                                *
  ************************************************************************/
  
  include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
  include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker

  $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
  $fExcept -> SetUser( 'InterfGPS' );
  $fExcept -> SetParams( "Sat", "NovedaGPSRilsa" );

  $fLogs = array();

  try
  {
    $db5 = new Consult( array( "server"=> Hostx5, "user"  => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5 ), $fExcept );//instancia de la clase para manejo de consultas de base de datos
    $fQuerySelVehiculos = "SELECT a.cod_operad, a.cod_transp, a.nom_aplica, a.num_despac, " .
                                 "a.num_placax, a.fec_salida, a.usr_gpsxxx, a.clv_gpsxxx, " .
                                 "a.idx_gpsxxx " .
                           "FROM ".BASE_DATOS5.".t_vehicu_gpsxxx a, " .
                                "".BASE_DATOS5.".t_interf_parame b " .
                           "WHERE a.cod_operad = b.cod_operad ".
                             "AND a.cod_transp = b.cod_transp ".
                             "AND a.fec_salida <= NOW()  ".
                             "AND ( a.fec_ultrep < DATE_SUB( NOW(), INTERVAL 50 MINUTE ) OR fec_ultrep IS NULL ) ". 
							 "AND a.fec_creaci >= DATE_SUB( NOW(), INTERVAL 30 DAY ) ".
                             "AND a.cod_operad = 900013074 ". 
                          "ORDER BY a.fec_creaci ASC LIMIT 8";
    $db5 -> ExecuteCons( $fQuerySelVehiculos );
    $fRecorVehiculos = $db5 -> RetMatrix( "a" );
    echo "<hr>Cantidad: ".$db5 -> RetNumRows();
	
    if( 0 != $db5 -> RetNumRows() )
    {
      foreach( $fRecorVehiculos as $fVehiculo )
      {
        try
        {
          $oSoapClient = new soapclient( 'http://web1ws.shareservice.co/HST/wsHistoryGetByPlate.asmx?WSDL', array( "trace" => "1" , 'encoding' => 'UTF-8') );
          $mParam = array( "sLogin"  => $fVehiculo['usr_gpsxxx'],
                           "sPassword"   => base64_decode( $fVehiculo['clv_gpsxxx'] )
                          );
       
          $mResult = $oSoapClient -> HistoyDataLastLocationByUser( $mParam );                  
          $mXml = $mResult -> HistoyDataLastLocationByUserResult -> any;
          $xmlObject = new SimpleXMLElement( $mXml );
       
          if( $xmlObject->Response->Status -> code != '100' )     
             throw new Exception ($xmlObject->Response->Status -> code ." - ".$xmlObject->Response->Status -> description."<br>Posible Soporte".);     
          else 
          {
            unset( $novedaGPS );
            $novedaGPS = array();
            
            for( $a=0; $a < sizeof( $xmlObject->Response-> Plate ); $a++)
            {
              $plate_array = (array) $xmlObject->Response->Plate[$a];
              $mNumPlacax = $plate_array['@attributes']['id'];              
              
              if( $fVehiculo["num_placax"] === (string)$mNumPlacax)
              {
                $novedaGPS['num_placax'] = (string) $plate_array['@attributes']['id'];
                $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject->Response->Plate[$a]->hst-> DateTimeGPS ) );
                $novedaGPS['val_veloci'] = (string) $xmlObject->Response->Plate[$a]->hst->Speed;
                $novedaGPS['val_longit'] = (string) $xmlObject->Response->Plate[$a]->hst->Longitude;
                $novedaGPS['val_latitu'] = (string) $xmlObject->Response->Plate[$a]->hst->Latitude;
                $novedaGPS['det_ubicac'] = (string) $xmlObject->Response->Plate[$a]->hst->Location;
                $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
                $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];    
              }              
            }            
            echo "<pre>"; print_r($novedaGPS); echo "</pre>";  
          }
          
        }
        catch( Exception $e )
        {
          echo '<br/>Hubo un error: '.$e -> getMessage().'<hr>';
          echo "<pre>soapofalut object e<br/>"; var_dump( $e ); echo "</pre>";
        }
        
        # ---------------------------------------------------------------------------------------#
        #                             INICIO ENVIO TRAFICO O AL SAT                              #                 
        # ---------------------------------------------------------------------------------------#
        
        $db5 ->StartTrans();
        $fQueryUpdVehiGps = "UPDATE ".BASE_DATOS5.".t_vehicu_gpsxxx " .
                               "SET fec_ultcon = NOW() " .
                             "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
                               "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
                               "AND num_placax = '".$fVehiculo['num_placax']."' ";

        if( $db5 -> ExecuteCons( $fQueryUpdVehiGps, "R" ) === FALSE )
             throw new Exception( "Error en UPDATE.", "3001" );

        if( isset( $novedaGPS['all_infgps'] ) && $novedaGPS['all_infgps'] !== NULL )
        {
          ini_set( "soap.wsdl_cache_enabled", "0" );
          
          /*if( $fVehiculo['nom_aplica'] == 'satt_prueba_faro' || $fVehiculo['nom_aplica'] == 'satt_faro' )
            $ws = WsdFAR;
          else
            $ws = WsdSAT;*/

          $ws = WsdFAR;
          $oSoapClient = new soapclient( $ws, array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
          $parametros = array( "nom_aplica" => $fVehiculo['nom_aplica'], 
                               "num_despac" => $fVehiculo['num_despac'], 
                               "cod_noveda" => '4999', 
                               "fec_noveda" => date( "Y-m-d H:i", strtotime( $novedaGPS['fec_noveda'] ) ), 
                               "des_noveda" => $novedaGPS['all_infgps'], 
                               "val_longit" => $novedaGPS['val_longit'], 
                               "val_latitu" => $novedaGPS['val_latitu'], 
                               "nom_llavex" => '3c09f78c210a18b686ae2540b0d12358' );//Se usa una llave para que solo oet pueda usar el metodo
          
          $mResultSend = $oSoapClient -> __call( "setNovedadGPS", $parametros );    
          echo "<pre>"; print_r( $mResultSend );  echo "</pre>";                                                   
          
          
          $mResultSend = explode( "; ", $mResultSend );
          $mCodResp    = explode( ":", $mResultSend[0] );
          $mMsgResp    = explode( ":", $mResultSend[1] );
          //$fVehiculo['num_placax'] = 'WHB760';

    
          if( "1000" != $mCodResp[1] )
          {
            $mMessage = "******** Encabezado ******** \n";
            $mMessage .= "Operacion: Insertar Novedad GPS \n";
            $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
            $mMessage .= "Fecha Novedad: ".$parametros["fec_noveda"]." \n";
            $mMessage .= "Aplicacion: ".$parametros["nom_aplica"]." \n";
            $mMessage .= "Despacho: ".$parametros["num_despac"]." \n";
            $mMessage .= "Descripcion Novedad: ".$parametros["des_noveda"]." \n";
            $mMessage .= "Operador: ".$fVehiculo['cod_operad']." \n";
            $mMessage .= "Placa: ".$fVehiculo['num_placax']." \n";
            $mMessage .= "******** Detalle ******** \n";
            $mMessage .= "Codigo de error: ".$mCodResp[1]." \n";
            $mMessage .= "Mensaje de error: ".$mMsgResp[1]." \n";
            mail( NotMai, "Web service GPS setNovedaGPS", $mMessage,'From: soporte.ingenieros@intrared.net' );
          }
          else
          {
            //Se actualiza la fecha del ultimo reporte satisfactorio
            $fQueryUpdVehiGps = "UPDATE ".BASE_DATOS5.".t_vehicu_gpsxxx " .
                                      "SET fec_ultrep = NOW() " .
                                 "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
                                   "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
                                   "AND num_placax = '".$fVehiculo['num_placax']."' ";
             if( $db5 -> ExecuteCons( $fQueryUpdVehiGps , "R" ) === FALSE )
             throw new Exception( "Error en UPDATE.", "3001" );
          }
        }
        $db5 -> Commit();
        # ---------------------------------------------------------------------------------------#
        #                                     FIN ENVIO TRAFICO                                  #                 
        # ---------------------------------------------------------------------------------------#
        # Pausar ejecucion de Script por parametro de consumo WSDL cada 20 Segundos
        sleep(22);
      }
    }
    else
    {
      //echo "No hay vehiculos reportando gps";
    }
  }
  catch( Exception $e )
  {
    $mTrace = $e -> getTrace();
    $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                          $e -> getLine(), $fVehiculo['nom_aplica'], $fVehiculo['num_placax'] );
    return FALSE;
  }
  
?>

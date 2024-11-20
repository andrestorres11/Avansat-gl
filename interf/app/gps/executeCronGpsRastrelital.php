<?php
  /***********************************************************************
  * @file server.php                                                     *
  * @brief Cron inserta Novedades de GPS Rastrelital                     *
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
  $fExcept -> SetParams( "Sat", "NovedaGPSRastrelital" );

  $fLogs = array();

  try
  {
    $db5 = new Consult( array( "server"=> Hostx5, "user"  => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5 ), $fExcept );//instancia de la clase para manejo de consultas de base de datos

    //consulta para traer el registro principal segun estructura en xls
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
                             "AND a.cod_operad = 9004892 ". 
                          "ORDER BY a.fec_creaci ASC ";
    $db5 -> ExecuteCons( $fQuerySelVehiculos );
    $fRecorVehiculos = $db5 -> RetMatrix( "a" );

	echo "<hr>Cantidad".$db5 -> RetNumRows();
	
    if( 0 != $db5 -> RetNumRows() )
    {
      foreach( $fRecorVehiculos as $fVehiculo )
      {
        unset( $novedaGPS );
        $novedaGPS = array();
		
		echo "<hr><pre>Datos Vehiculo";
		print_r( $fVehiculo );
		echo "</pre>";

          
          $oSoapClient = new soapclient( 'http://www.rastrea.net/web%20services/utrax.ws_SAT/ws_SAT.asmx?WSDL', array( "trace" => 1, 'encoding'=>'ISO-8859-1' ) );
            
          $split1=substr($fVehiculo[num_placax], 0, 3);  
          $split2=substr($fVehiculo[num_placax], 3, 6);
          $Plates = $split1." ".$split2;

          $mParams = array
                    (
                      "User" => $fVehiculo['usr_gpsxxx'],
                      "Password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "Plates" => $Plates
                    );   
					
		  echo "<hr><pre>Parametros Enviados";
		  print_r( $mParams );
		  echo "</pre>";                        

          $result = $oSoapClient -> GetLastPosition( $mParams );

		  echo "<hr><pre>Respuesta Webservice";
		  print_r( $result );
		  echo "</pre>";
  
          if( $result -> GetLastPositionResult -> eCode == 1 )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $result -> GetLastPositionResult -> DateTime_GPS) );
            $novedaGPS['val_latitu'] = $result -> GetLastPositionResult -> Latitude;
            $novedaGPS['val_longit'] = $result -> GetLastPositionResult -> Longitude;
            $novedaGPS['val_veloci'] = $result -> GetLastPositionResult -> Speed;
            $novedaGPS['det_ubicac'] = utf8_decode( (string) $result -> GetLastPositionResult -> Reference);        
            
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
          }
        
		  echo "<hr><pre>NovedadGPS";
		  print_r( $novedaGPS );
		  echo "/<pre>";
        
        $db5 ->StartTrans();
        //Se actualiza la fecha del ultimo consumo
        $fQueryUpdVehiGps = "UPDATE ".BASE_DATOS5.".t_vehicu_gpsxxx " .
                               "SET fec_ultcon = NOW() " .
                             "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
                               "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
                               "AND num_placax = '".$fVehiculo['num_placax']."' ";

        if( $db5 -> ExecuteCons( $fQueryUpdVehiGps, "R" ) === FALSE )
             throw new Exception( "Error en UPDATE.", "3001" );

        if( isset( $novedaGPS['all_infgps'] ) && $novedaGPS['all_infgps'] !== NULL )
        {
          //Se envia la novedad GPS a la aplicacion sat
          ini_set( "soap.wsdl_cache_enabled", "0" );
          
          /*if( $fVehiculo['nom_aplica'] == 'satt_prueba_faro' || $fVehiculo['nom_aplica'] == 'satt_faro' )
          else
            $ws = WsdSAT;*/

          $ws = WsdFAR;
          $oSoapClient = new soapclient( $ws, array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );

          //$oSoapClient = new soapclient( WsdSAT, array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
      
          $parametros = array( "nom_aplica" => $fVehiculo['nom_aplica'], 
                               "num_despac" => $fVehiculo['num_despac'], 
                               "cod_noveda" => '4999', 
                               "fec_noveda" => date( "Y-m-d H:i", strtotime( $novedaGPS['fec_noveda'] ) ), 
                               "des_noveda" => $novedaGPS['all_infgps'], 
                               "val_longit" => $novedaGPS['val_longit'], 
                               "val_latitu" => $novedaGPS['val_latitu'], 
                               "nom_llavex" => '3c09f78c210a18b686ae2540b0d12358' );//Se usa una llave para que solo oet pueda usar el metodo
          echo "<pre>";                                                   
          print_r( $parametros );
          echo "</pre>";                                                   
          $mResult = $oSoapClient -> __call( "setNovedadGPS", $parametros );    
          echo "<pre>";                                                   
          print_r( $mResult );
          echo "</pre>";                                                   
          
          
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
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

            /*if( strpos($mMsgResp[1], 'no se encuentra en ruta, o no esta registrado') !== false || strpos($mMsgResp[1], 'Aplicacion no encontrada') !== false  )
            {
              //Se ELIMINA de la lista de reportando GPS si no se encuentra en ruta
              $fQueryDelVehiGps = "DELETE FROM ".BASE_DATOS5.".t_vehicu_gpsxxx " .
                                   "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
                                     "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
                                     "AND num_placax = '".$fVehiculo['num_placax']."' ";

              if( $db5 -> ExecuteCons( $fQueryDelVehiGps, "R" ) === FALSE )
              throw new Exception( "Error en DELETE.", "3001" );
            }*/
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
  function getToken( $db5, $userGPS, $claveGPS )
  {
    $mQuery = "SELECT MAX(cod_consec), num_tokenx, fec_regist 
                 FROM ".BASE_DATOS5.".t_genera_token 
                WHERE fec_regist = '".date("Y-m-d")."' ";
    $db5 -> ExecuteCons( $mQuery  );
    $tokenx = $db5 -> RetMatrix( "a" );
    if( !trim($tokenx[0]['num_tokenx']) )
    {
      $oSoapClient = new soapclient( 'http://www.tsoapi.com/Authentication.asmx?wsdl', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
      
      $mParams = array(
                        "login" => $userGPS, 
                        "password" => $claveGPS,
                        "AppId" => 2
                      );
      $result = $oSoapClient -> ValidateUser( $mParams );
     
      $mQuery = "SELECT MAX(cod_consec) 
                   FROM ".BASE_DATOS5.".t_genera_token";
      $db5 -> ExecuteCons( $mQuery  );
      $max = $db5 -> RetMatrix( "i" );
      $max = (int)$max[0][0] + 1;
      
      $mQuery = "INSERT INTO ".BASE_DATOS5.".t_genera_token ( cod_consec, num_tokenx, fec_regist ) 
                  VALUES ( '".$max."', '".$result -> ValidateUserResult."', DATE(NOW()) ) ";
      $db5 -> ExecuteCons( $mQuery  );
      
      return $result -> ValidateUserResult;
    }
    else
    {
      return $tokenx[0]['num_tokenx'];
    }
    
  }
?>

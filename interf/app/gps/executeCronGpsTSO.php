<?php
  /***********************************************************************
  * @file server.php                                                     *
  * @brief Cron inserta Novedades de GPS TSO Mobile                      *
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
  $fExcept -> SetParams( "Sat", "NovedaGPSTSO" );

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
                             "AND a.cod_operad = 900361602 ". 
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

          echo $TOKENX = getToken( $db5, 'oetapi', '03t4p106' );
          
          if( !$TOKENX )
            throw new Exception( "No se encontro Token para TSO Mobile.", "6001" );
            
          $oSoapClient = new soapclient( 'http://www.tsoapi.com/Units.asmx?WSDL', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
          $mParams = array(
                            "token" => $TOKENX, 
                            "filterBy" => "LICENSEPLATE",
                            "id" => $fVehiculo['num_placax']
                          );
						  
		  echo "<hr><pre>Parametros Enviados";
		  print_r( $mParams );
		  echo "</pre>";  
						  
          $result = $oSoapClient -> GetLastLocation( $mParams );
		  
		  echo "<hr><pre>Respuesta Webservice";
  		  print_r( $result );
  		  echo "</pre>";
		  
          //xml de la respuesta de TSO
          $str = $result -> GetLastLocationResult -> any;
          
          $pos = strpos( $str, "<diffgr" );
          //Se asigna al xml una parte de la respuesta porque viene mal formada
          $xmlObject = new SimpleXMLElement( substr( $str, $pos, strlen( $str ) ) );
          
          //Se transforma la fecha 2012-05-07T16:25:49-04:00 para restarle 5 horas y que sea igual a 2012-05-07 11:25
          $fecha = substr( str_replace( "T", ' ',  $xmlObject -> DocumentElement -> Position -> LastEventDate ), 0, 19 );
          
          $dd=0;
          $mm=0;
          $yy=0;
          $hh=-5;
          $mn=0;
          $ss=0;
          
          $date_r = getdate( strtotime( $fecha ) );
          $fecha = date( 'Y-m-d H:i', mktime( ( $date_r["hours"] + $hh ),( $date_r["minutes"] + $mn ), ( $date_r["seconds"] + $ss ), ( $date_r["mon"] + $mm ), ( $date_r["mday"] + $dd ), ( $date_r["year"] + $yy ) ) );
    
		  if( (string) $xmlObject -> DocumentElement -> Position -> Latitude )
		  {
	          $novedaGPS['fec_noveda'] = $fecha;
	          $novedaGPS['val_latitu'] = (string) $xmlObject -> DocumentElement -> Position -> Latitude;
	          $novedaGPS['val_longit'] = (string) $xmlObject -> DocumentElement -> Position -> Longitude;
	          $novedaGPS['det_ubicac'] = $xmlObject -> DocumentElement -> Position -> Address.', '.$xmlObject -> DocumentElement -> Position -> CountryCode;
	          $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
	          $novedaGPS['all_infgps'] .= ". Velocidad: 0";
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
            $ws = WsdFAR;
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
                WHERE fec_regist = '".date("Y-m-d")."' AND cod_operad = '900361602' ";
    $db5 -> ExecuteCons( $mQuery  );
    $tokenx = $db5 -> RetMatrix( "a" );
	
	echo "<pre>";
	print_r( $tokenx );
	echo "</pre>";
	
    if( !trim($tokenx[0]['num_tokenx']) )
    {
      $oSoapClient = new soapclient( 'http://www.tsoapi.com/Authentication.asmx?wsdl', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
      
      $mParams = array(
                        "login" => $userGPS, 
                        "password" => $claveGPS,
                        "AppId" => 2
                      );
	  echo "<hr><pre>Parametros Token";
	  print_r( $mParams );
	  echo "</pre>"; 
      $result = $oSoapClient -> ValidateUser( $mParams );
	  echo "<hr><pre>Respuesta WS Token";
	  print_r( $result );
	  echo "</pre>"; 
      
     
      $mQuery = "SELECT MAX(cod_consec) 
                   FROM ".BASE_DATOS5.".t_genera_token";
      $db5 -> ExecuteCons( $mQuery  );
      $max = $db5 -> RetMatrix( "i" );
      $max = (int)$max[0][0] + 1;
      
      $mQuery = "INSERT INTO ".BASE_DATOS5.".t_genera_token ( cod_consec, num_tokenx, fec_regist, cod_operad ) 
                  VALUES ( '".$max."', '".$result -> ValidateUserResult."', DATE(NOW()), '900361602' ) ";
      $db5 -> ExecuteCons( $mQuery  );
      
      return $result -> ValidateUserResult;
    }
    else
    {
      return $tokenx[0]['num_tokenx'];
    }
    
  }
?>

<?php
  /***********************************************************************
  * @file server.php                                                     *
  * @brief Cron inserta Novedades de GPS.                                *
  * @version 0.1                                                         *
  * @date 09 de Marzo de 2011                                            *
  * @author Hugo Malagon.                                                *
  ************************************************************************/
  //ini_set('display_errors', true);
  //error_reporting(E_ALL & ~E_NOTICE);

  include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
  include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker


  
  $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
  $fExcept -> SetUser( 'InterfGPS' );
  $fExcept -> SetParams( "Sat", "NovedaGPSAP" );

  $fLogs = array();

  try
  {
    $db5 = new Consult( array( "server"=> Hostx5, "user"  => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5 ), $fExcept );//instancia de la clase para manejo de consultas de base de datos

    //consulta para traer el registro principal segun estructura en xls
    echo $fQuerySelVehiculos = "SELECT a.cod_operad, a.cod_transp, a.nom_aplica, a.num_despac, " .
                                 "a.num_placax, a.fec_salida, a.usr_gpsxxx, a.clv_gpsxxx, " .
                                 "a.idx_gpsxxx " .
                           "FROM ".BASE_DATOS5.".t_vehicu_gpsxxx a, " .
                                "".BASE_DATOS5.".t_interf_parame b " .
                           "WHERE a.cod_operad = b.cod_operad ".
                             "AND a.cod_transp = b.cod_transp ".
                             "AND a.fec_salida <= NOW()  ".
                             "AND (a.fec_ultrep < DATE_SUB( NOW(), INTERVAL 50 MINUTE ) OR fec_ultrep IS NULL ) ". 
                             //" AND a.num_placax = 'SSY674' ".
                          "ORDER BY a.fec_creaci ASC ";
    $db5 -> ExecuteCons( $fQuerySelVehiculos );
    $fRecorVehiculos = $db5 -> RetMatrix( "a" );
    //$db5 -> RetNumRows()
    //throw new Exception( $fMenMai, "1002" );


    if( 0 != $db5 -> RetNumRows() )
    {


      foreach( $fRecorVehiculos as $fVehiculo )
      {
        echo "Despacho=".$fVehiculo['num_despac'].' Placa='.$fVehiculo['num_placax'].' Operador='.$fVehiculo['cod_operad'];
        unset( $novedaGPS );
        $novedaGPS = array();
        if( $fVehiculo['cod_operad'] == '900040838' )
        {
          //Si el operador es Satrack
          ini_set( "soap.wsdl_cache_enabled", "0" ); 
          if (!class_exists('SoapClient'))
          {
            die ("No se encuentra instalado el módulo PHP-SOAP.");
          }

          //Se obtiene la ultima novedad gps
          $oSoapClient = new soapclient( 'http://ww3.satrack.com/webserviceeventos/getEvents.asmx?WSDL', array( "trace" => "1", 'encoding' => 'ISO-8859-1' ) );
         
          $mParametros = new StdClass();
          $mParametros -> UserName = $fVehiculo['usr_gpsxxx'];
          $mParametros -> Password = base64_decode( $fVehiculo['clv_gpsxxx'] );
          $mParametros -> PhysicalID = $fVehiculo['num_placax'];

          //Ese método retorna <NewDataSet /> cuando hay error de autenticacion o no retorna datos
          $result = $oSoapClient -> getLastEventString( $mParametros );
/*
echo "<pre>";
print_r($result);
print_r($mParametros);
echo "</pre>";
*/
          echo $response = utf8_encode( $result -> getLastEventStringResult );
          
          if( $response != NULL )
          {
            $xmlObject = new SimpleXMLElement( $response );

            if( count( $xmlObject->children() ) > 0 )
            {
              //Se extrae del xml retornado la informacion de la novedad si retorna LastEvents(hijo)
              $Ubicacion = utf8_encode( 'Ubicación' );
              $novedaGPS['num_placax'] = (string) $xmlObject -> LastEvents -> Placa;
              $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject -> LastEvents -> Fecha_x0020_GPS ) );
              $novedaGPS['val_veloci'] = utf8_decode( (string) $xmlObject -> LastEvents -> Velocidad_x0020_y_x0020_Sentido );
              $novedaGPS['val_longit'] = (string) $xmlObject -> LastEvents -> Longitud;
              $novedaGPS['val_latitu'] = (string) $xmlObject -> LastEvents -> Latitud;
              $novedaGPS['det_ubicac'] = utf8_decode( (string) $xmlObject -> LastEvents -> $Ubicacion );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            }
          }
        }
        elseif( $fVehiculo['cod_operad'] == '860512330' )
        {
          //Si el operador es Servientrega se obtiene la ultima novedad GPS
          $data = file_get_contents("http://200.31.212.16/servientrega/servicio2.php?placa=".$fVehiculo['num_placax']);
          $data = explode( '<?xml version="1.0" encoding="ISO-8859-1"?>', $data );
          $response = utf8_encode( $data[1] );
          
          $xmlObject = new SimpleXMLElement( $response );
          
          if( count( $xmlObject->children() ) > 0 && (string) $xmlObject -> estado != 520 && (string) $xmlObject -> posicion != 'nodata' )
          {
            //Se extrae del xml retornado la informacion de la novedad si retorna algo
            //El codigo de estado 520 es retornado cuando la placa no retorna datos
            $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject -> tiempo_gps ) );
            $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
            $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
            $novedaGPS['val_latitu'] = (string) $xmlObject -> latitud;
            $novedaGPS['det_ubicac'] = (string) $xmlObject -> posicion;
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
          }
        }
        elseif( $fVehiculo['cod_operad'] == '9004892' )
        {

          //Si el operador es Rastrelital 9004892 se obtiene la ultima novedad GPS
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
//              echo "<pre>";
//              print_r($mParams); 
//              echo "</pre>";
          $result = $oSoapClient -> GetLastPosition( $mParams );

/*
echo "<pre>";
print_r($result); 
echo "</pre>";    
*/

          if( $result -> GetLastPositionResult -> eCode == 1 )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $result -> GetLastPositionResult -> DateTime_GPS) );
            $novedaGPS['val_latitu'] = $result -> GetLastPositionResult -> Latitude;
            $novedaGPS['val_longit'] = $result -> GetLastPositionResult -> Longitude;
            $novedaGPS['val_veloci'] = $result -> GetLastPositionResult -> Speed;
            $novedaGPS['det_ubicac'] = utf8_decode( (string) $result -> GetLastPositionResult -> Reference);        
            
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
//                          echo "<pre>";
//              print_r($novedaGPS); 
//              echo "</pre>"; 
          }
        }

        elseif( $fVehiculo['cod_operad'] == '88372172' ) 
        {            
        //Si el operador es Omnitracs
          $oSoapClient = new soapclient( 'https://www.omnitracsportal.com/oet/Integration.asmx?WSDL', array( "trace" => 1, 'encoding'=>'ISO-8859-1' ) );

          $mParams = array
                    (
                      "Usuario" => $fVehiculo['usr_gpsxxx'],
                      "Clave" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "Placa" => $fVehiculo[num_placax]
                    );    
                    
          echo "<pre>";
          print_r( $mParams );
          echo "</pre>";
          
          $result = $oSoapClient -> GetLastPosition( $mParams );
          $result = $result -> GetLastPositionResult -> any; 
          echo "\nRespuesta<br>".htmlspecialchars( $result, ENT_QUOTES );
          $xmlObject = new SimpleXMLElement( $result );    
          
          if( $xmlObject -> getName() != 'error_gps' )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $xmlObject -> tiempo_gps ) );
            $novedaGPS['val_latitu'] = (string) $xmlObject -> latitud;
            $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
            $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
            $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $xmlObject -> posicion );
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
          }
          echo "<pre>"; print_r($novedaGPS); echo "</pre>"; 
        }
        //WideTech         
        elseif( $fVehiculo['cod_operad'] == '9001387'  )
        {
          echo $i;
          echo $fVehiculo[num_placax];
          try
          {
            echo "> Entra a WideTech OK";
            $oSoapClient = new soapclient( 'http://ws.widetech.com.co/wsHistoryGetByPlate.asmx?WSDL', array( "trace" => "1", 'encoding' => 'ISO-8859-1' ) );
              
            
            $mParams = array
                      (
                        "sLogin" => $fVehiculo['usr_gpsxxx'],
                        "sPassword" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                        "sPlate" => $fVehiculo[num_placax]
                      );
            
            $result = $oSoapClient -> HistoyDataLastLocationByPlate( $mParams );
            $result = $result -> HistoyDataLastLocationByPlateResult -> any;          
            $xmlObject = new SimpleXMLElement( $result );
            
            if( $xmlObject -> Response -> Status -> code == '100' )
            {
              $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $xmlObject -> Response -> Plate -> hst -> DateTimeGPS ) );
              $novedaGPS['val_latitu'] = (string) $xmlObject -> Response -> Plate -> hst -> Latitude;
              $novedaGPS['val_longit'] = (string) $xmlObject -> Response -> Plate -> hst -> Longitude;
              $novedaGPS['val_veloci'] = (string) $xmlObject -> Response -> Plate -> hst -> Speed;
              $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $xmlObject -> Response -> Plate -> hst -> Location -> B );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            }
             
            echo "<pre>"; print_r($novedaGPS); echo "</pre>";        
            sleep(21);
          }
          catch(SoapFault $e )
          {
              $error = $e -> faultstring;
              echo "<pre>"; print_r($error); echo "</pre>";
          } 
        }
        elseif( $fVehiculo['cod_operad'] == '830141109'  )
        {
          //Interfaz GPS con Tracker
          $oSoapClient = new soapclientnusoap( 'http://www.tracker.com.co/gps/ws/servicio_v2.php?wsdl',true );
          $mParams = array
                    (
                      "usuario" => $fVehiculo['usr_gpsxxx'],
                      "clave" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "campos" => "Velocidad",
                      "placa" => $fVehiculo['num_placax']
                    );
          $result = $oSoapClient -> call ( "ultimo_punto", $mParams );
          if( $result !== FALSE )
          {
            $pos1 = strpos( $result, "<estado>", 1 );
            $pos2 = strpos( $result, "</movil>", 1 );
            $result = substr( $result, $pos1, ( $pos2 - $pos1 ) );
            $result = "<root>".$result."</root>";
            $xmlObject = new SimpleXMLElement( $result );
            if( count( $xmlObject->children() ) > 0 && (string) $xmlObject -> estado == 'OK' )
            {
              $novedaGPS['fec_noveda'] = (string) $xmlObject -> fecha_gps;
              $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
              $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
              $novedaGPS['val_latitu'] = (string) $xmlObject -> latitud;
              $novedaGPS['det_ubicac'] = (string) $xmlObject -> georef;
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            }
          }
        }
        elseif( $fVehiculo['cod_operad'] == '830045348'  )
        {
          
          //Interfaz GPS con INTEGRA GPS
          $oSoapClient = new soapclient( 'http://190.145.109.121:81/wsintegra/WSRastreo.asmx?WSDL', array( "trace" => 1, 'encoding'=>'UTF-8' ) );

          $mParams = array
                    (
                      "user" => $fVehiculo['usr_gpsxxx'],
                      "password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "placa" => $fVehiculo['num_placax']
                    );
           echo "<pre>";         
           print_r( $mParams );
           echo "</pre>";         
          $result = $oSoapClient -> getPosition( $mParams );
          $result = $result -> getPositionResult -> any;
          echo "\nRespuesta<br>".htmlspecialchars( $result, ENT_QUOTES );
          $xmlObject = new SimpleXMLElement( $result );
    
          if( $xmlObject -> getName() != 'error' )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $xmlObject -> tiempo_gps ) );
            $novedaGPS['val_latitu'] = (string) $xmlObject -> latitude;
            $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
            $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
            $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $xmlObject -> posicion );
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
          }
        }
        elseif( $fVehiculo['cod_operad'] == '830126626' || $fVehiculo['cod_operad'] == '830076669'  )
        {
          //Si el operador es Rastrack - Grupo OET
          if( $fp = fsockopen( "200.31.91.234", 60, $errno, $errstr, 4 ) )
          {
            //Se elabora el comando a enviar mediante la Interfaz TCP/IP con Rastrack
            $out =  "<Rastrac>";
            $out .= "<RastracMessage>";
            $out .= "<messagetype>RastracCommand</messagetype>";
            $out .= "<seqnum>124</seqnum>";
            $out .= "<command>GetVehicleState</command>";
            $out .= "<id>".$fVehiculo['idx_gpsxxx']."</id>";
            $out .= "</RastracMessage>";
            $out .= "</Rastrac>";
            //echo "\nComando enviado<br>".htmlspecialchars( $out, ENT_QUOTES );
            //Se envia el comando al servidor de Ratrack
            fwrite( $fp, $out );
            
            $caracter = array();
            //Se captura la respuesta del servidor de Rastrack
            for( $i = 0; $i <= 2000; $i++ )
            {
              $caracter[$i] = fgetc( $fp );
              //Se determina el fin de la respuesta se tuvo que hacer de esta forma porque esa respuesta no tiene fin y no tiene una longitud fija
              if( $caracter[$i] == '>' && $caracter[$i-9] == '<' && $caracter[$i-8] == '/' && $caracter[$i-7] == 'R' && $caracter[$i-6] == 'a' && $caracter[$i-5] == 's' && $caracter[$i-4] == 't' && $caracter[$i-3] == 'r' && $caracter[$i-2] == a && $caracter[$i-1] == 'c' )
                break;
            }
            
            $result = implode( '', $caracter );
            
            //echo "\nRespuesta<br>".htmlspecialchars( $result, ENT_QUOTES );
            
            //Se cierra la conexión al servidor Rastrack
            fclose($fp);
            
            //Se lee la respuesta segun la estructura xml
            $result = utf8_encode( $result );
            $xmlObject = new SimpleXMLElement( $result );
            
            $arrayNoReporta = array( 75, 76, 77, 78, 79 );
            
            if( count( $xmlObject -> children() ) > 0 && !in_array( (string)$xmlObject -> RastracMessage -> Event, $arrayNoReporta) && !(string)$xmlObject -> RastracMessage -> ErrorMessage && (string) $xmlObject -> RastracMessage -> Latitude && (string) $xmlObject -> RastracMessage -> Longitude )
            {
              //Se calcula la fecha del reporte GPS reconstruyendo las piezas
              $day = (string) $xmlObject -> RastracMessage -> Day;
              $month = (string) $xmlObject -> RastracMessage -> Month;
              $year = (string) $xmlObject -> RastracMessage -> Year;
              //El tiempo para Rastrack es la cantidad de segundos transcurridos desde las 00:00
              $time = (string) $xmlObject -> RastracMessage -> Time;
              $hourInt = intval( $time / 3600 );
              $hourDecim = $time / 3600;
              $minInt = intval( ( $hourDecim - $hourInt ) * 60 );
              $minDecim = ( $hourDecim - $hourInt ) * 60;
              $segInt = intval( ( $minDecim - $minInt ) * 60 );
              
              $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', mktime( $hourInt, $minInt, $segInt, $month, $day, $year ) );
              $novedaGPS['val_latitu'] = (string) $xmlObject -> RastracMessage -> Latitude;
              $novedaGPS['val_longit'] = (string) $xmlObject -> RastracMessage -> Longitude;
              $novedaGPS['val_veloci'] = (string) $xmlObject -> RastracMessage -> Speed;
              $novedaGPS['det_ubicac'] = utf8_decode( (string) $xmlObject -> RastracMessage -> StreetName . ' ' . (string) $xmlObject -> RastracMessage -> ExAddr );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            }
            else
            {
              echo "\nEl vehiculo no esta reportando GPS";
              echo "\nError: ".(string)$xmlObject -> RastracMessage -> ErrorMessage.'. '.(string)$xmlObject -> RastracMessage -> Result;
            }
          }
          else
          {
            echo 'Error al abrir la conexion a Rastrack<br>';
            echo 'Numero de error: '.$errno;
            echo '<br>Error: '.$errstr;
          }
        }
        elseif(  $fVehiculo['cod_operad'] == '900361602' )
        {
          //Si el operador es TSO Mobile
          $TOKENX = getToken( $db5, $fVehiculo['usr_gpsxxx'], base64_decode( $fVehiculo['clv_gpsxxx'] ) );
          
          if( !$TOKENX )
            throw new Exception( "No se encontro Token para TSO Mobile.", "6001" );
            
          $oSoapClient = new soapclient( 'http://www.tsoapi.com/Units.asmx?WSDL', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
          $mParams = array(
                            "token" => $TOKENX, 
                            "filterBy" => "LICENSEPLATE",
                            "id" => $fVehiculo['num_placax']
                          );
          $result = $oSoapClient -> GetLastLocation( $mParams );
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
    
          $novedaGPS['fec_noveda'] = $fecha;
          $novedaGPS['val_latitu'] = (string) $xmlObject -> DocumentElement -> Position -> Latitude;
          $novedaGPS['val_longit'] = (string) $xmlObject -> DocumentElement -> Position -> Longitude;
          $novedaGPS['det_ubicac'] = $xmlObject -> DocumentElement -> Position -> Address.', '.$xmlObject -> DocumentElement -> Position -> CountryCode;
          $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
          $novedaGPS['all_infgps'] .= ". Velocidad: 0";
        }
        // ---- Autos SURA 
        elseif(  $fVehiculo['cod_operad'] == '811036875' ) 
        {
          echo "Sonar AVL System ( Autos SURA )";
          try
          {
            $oSoapClient = new soapclient( 'https://www.sonaravl.com/b2bsura/Service.asmx?WSDL' , array( "trace" => TRUE, 'encoding' => 'ISO-8859-1' ) );
            $params3 = array(
                              "User"     => $fVehiculo['usr_gpsxxx'],
                              "Password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                              "mId"      => $fVehiculo['idx_gpsxxx']
                            );
                            
            $mResult = $oSoapClient -> GET_LastLocation( $params3 ); 
             
             if( $mResult -> GET_LastLocationResult -> status == 'OK' )
              {
                $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> gps_GMT ) );
                $date_r = getdate(strtotime( $novedaGPS['fec_noveda'] ));
                $date_result = date('Y-m-d H:i', mktime(($date_r["hours"]-5),($date_r["minutes"]+0),($date_r["seconds"]+0),($date_r["mon"]+0),($date_r["mday"]+0),($date_r["year"]+0)));
                $novedaGPS['fec_noveda'] = $date_result;

                $novedaGPS['val_latitu'] = (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> latitude;
                $novedaGPS['val_longit'] = (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> longitude;
                $novedaGPS['val_veloci'] = (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> speed;
                $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> address );
                
                $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
                $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
              }
          }
          catch(SoapFault $e )
          {
              $error = $e -> faultstring;
              echo "<pre>"; print_r($error); echo "</pre>";
          } 
        }
        elseif(  $fVehiculo['cod_operad'] == '900014002' )
        {
          echo "<h>Inicio.... 24Saltétilal --- Despacho ".$fVehiculo['num_despac']."</h><br>";
          try
          {
            $oSoapClient = new soapclient( 'http://www.24satelital.net/ws/server.php?wsdl', array( "trace" => "1" , 'encoding' => 'UTF-8') );
            $mParam = array( "user"  => $fVehiculo['usr_gpsxxx'],
                             "pws"   => $fVehiculo['clv_gpsxxx'],
                             "placa" => $fVehiculo['num_placax']
                            );
            
            $result = $oSoapClient -> __call ( "UltByPlaca", $mParam );      
            $mDataEmpresa = simplexml_load_string( base64_decode($result));
            
            if( $mDataEmpresa->error[0] == 'error' )     
             throw new Exception ($mDataEmpresa->errorDescription[0]);     
            else 
            {
              $novedaGPS['num_placax'] = (string) $mDataEmpresa->placa;
              $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $mDataEmpresa->tiempo_gps ) );
              $novedaGPS['val_veloci'] = utf8_decode( (string) $mDataEmpresa->velocidad );
              $novedaGPS['val_longit'] = (string) $mDataEmpresa->longitud;
              $novedaGPS['val_latitu'] = (string) $mDataEmpresa->latitud;
              $novedaGPS['det_ubicac'] = utf8_decode( (string) $mDataEmpresa->posicion );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];    
              echo "<pre>"; print_r($novedaGPS); echo "</pre>";    
            }
          }
          catch( Exception $e )
          {
            echo '<br/>Hubo un error: '.$e -> getMessage().'<hr>';
            echo "<pre>soapofalut object e<br/>";
              var_dump( $e );
            echo "</pre>";
          }
          
        }
        elseif(  $fVehiculo['cod_operad'] == '900013074' )
        {
          echo "<br><h>Inicio.... Rilsa --- Despacho ".$fVehiculo['num_despac']."</h><br>";
          try
          {
            $oSoapClient = new soapclient( 'http://web1ws.shareservice.co/HST/wsHistoryGetByPlate.asmx?WSDL', array( "trace" => "1" , 'encoding' => 'UTF-8') );
            $mParam = array( "sLogin"  => $fVehiculo['usr_gpsxxx'],
                             "sPassword"   => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                             "sPlate" => $fVehiculo['num_placax']
                            );
            echo "<pre>"; print_r($mParam); echo "</pre>";    
            $mResult = $oSoapClient -> HistoyDataLastLocationByPlate( $mParam );                  
            $mXml = $mResult -> HistoyDataLastLocationByPlateResult -> any;
         
            $xmlObject = new SimpleXMLElement( $mXml );
           
            if( $xmlObject->Response->Status -> code != '100' )     
               throw new Exception ($mDataEmpresa->errorDescription[0]);     
            else 
            {
              $plate_array = (array) $xmlObject->Response->Plate;
              
              $novedaGPS['num_placax'] = (string) $plate_array['@attributes']['id'];
              $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject->Response->Plate->hst-> DateTimeGPS ) );
              $novedaGPS['val_veloci'] = (string) $xmlObject->Response->Plate->hst->Speed;
              $novedaGPS['val_longit'] = (string) $xmlObject->Response->Plate->hst->Longitude;
              $novedaGPS['val_latitu'] = (string) $xmlObject->Response->Plate->hst->Latitude;
              $novedaGPS['det_ubicac'] = (string) $xmlObject->Response->Plate->hst->Location;
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];    
              echo "<pre>"; print_r($novedaGPS); echo "</pre>";    
            }
          }
          catch( Exception $e )
          {
            echo '<br/>Hubo un error: '.$e -> getMessage().'<hr>';
            echo "<pre>soapofalut object e<br/>";
              var_dump( $e );
            echo "</pre>";
          }
          
        }
        
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
          
          if( $fVehiculo['nom_aplica'] == 'satt_prueba_faro' || $fVehiculo['nom_aplica'] == 'satt_faro' )
            $ws = WsdFAR;
          else
            $ws = WsdSAT5;

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
                                                                 
          $mResult = $oSoapClient -> __call( "setNovedadGPS", $parametros );    

echo "<br />novedaGPS----------------------------------<br /><pre>";
print_r($novedaGPS);
echo "</pre>";
echo "<br />mResult----------------------------------<br /><pre>";
print_r($mResult);
echo "</pre>";
echo "<br />parametros----------------------------------<br /><pre>";
print_r($parametros);
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

            //if( strpos($mMsgResp[1], 'no se encuentra en ruta, o no esta registrado') !== false || strpos($mMsgResp[1], 'Aplicacion no encontrada') !== false || strpos($mMsgResp[1], 'Fecha de la novedad') !== false )
            if( strpos($mMsgResp[1], 'no se encuentra en ruta, o no esta registrado') !== false || strpos($mMsgResp[1], 'Fecha de la novedad') !== false )
            {
              //Se ELIMINA de la lista de reportando GPS si no se encuentra en ruta
              $fQueryDelVehiGps = "DELETE FROM ".BASE_DATOS5.".t_vehicu_gpsxxx " .
                                   "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
                                     "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
                                     "AND num_placax = '".$fVehiculo['num_placax']."' ";

              if( $db5 -> ExecuteCons( $fQueryDelVehiGps, "R" ) === FALSE )
              throw new Exception( "Error en DELETE.", "3001" );
            }
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
    echo "<pre>";
    print_r($e);
    echo "</pre>";
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

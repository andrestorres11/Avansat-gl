<?php

/* **********************************************************************
 * @file executeCronGpsDisatel.php                                 *
 * @brief Cron inserta Novedades de Disatel GPS                   *
 * @version 0.1                                                         *
 * @date 01 de Febrero de 2023                                          *
 * @modified 01 de Febrero de 2023                                      *
 * @author Ing. Cristian Andr�s Torres                                  *
 * **********************************************************************/
ini_set("display_errors", true);
error_reporting(E_ALL && ~E_NOTICE);
 header('Content-Type: text/html; charset=utf-8');
include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes propias.
include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker
/*
$fExcept = new Error(array("dirlog" => LogDir, "notlog" => TRUE));
$fExcept->SetUser('InterfGPS');
$fExcept->SetParams("Sat", "NovedaGPSTracker");*/

$fLogs = array();
try {
    
    $db5 = new Consult(array("server" => Hostx5, "user" => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5), $fExcept); //instancia de la clase para manejo de consultas de base de datos
    
    //consulta para traer el registro principal segun estructura en xls
    $fQuerySelVehiculos = "SELECT a.cod_operad, a.cod_transp, a.nom_aplica, a.num_despac, " .
        "a.num_placax, a.fec_salida, a.usr_gpsxxx, a.clv_gpsxxx, " .
        "a.idx_gpsxxx " .
        "FROM " . BASE_DATOS5 . ".t_vehicu_gpsxxx a " .
        "INNER JOIN " . BASE_DATOS5 . ".t_interf_parame b " .
        "ON a.cod_transp = b.cod_transp " .
        "AND a.cod_operad = b.cod_operad " .
        "WHERE a.fec_salida <= NOW() " .
        "AND ( a.fec_ultrep < DATE_SUB( NOW(), INTERVAL 10 MINUTE ) OR fec_ultrep IS NULL ) " .
        "AND a.fec_creaci >= DATE_SUB( NOW(), INTERVAL 30 DAY ) " .
        "AND a.cod_operad = '2981388' " .
        "ORDER BY a.fec_creaci ASC ";
    echo $fQuerySelVehiculos;
    $db5->ExecuteCons($fQuerySelVehiculos);
    $fRecorVehiculos = $db5->RetMatrix("a");
    echo "<hr>Cantidad " . $db5->RetNumRows();
    $dateNow = date('Y/m/d');

    $url = "https://srv.disatelgps.com/megadashboard/?u=gt-bodegas&jason=1";
    // Obtener el contenido del archivo JSON
    $json = file_get_contents($url);
    // Decodificar el contenido JSON
    $registros = json_decode($json, true);
    
    echo "<pre>";
            echo "REGISTROS TOTALES<br>";
            print_r($registros);
            echo "</pre>";

    if ($db5->RetNumRows() != 0) {
        foreach ($fRecorVehiculos as $fVehiculo) {
            $registros_filtrados = NULL;
            //Formateamos la placa en formato asi C410BPH por C-410 BPH
            $formatted_placa = substr_replace($fVehiculo['num_placax'], '-', 1, 0);
            $formatted_placa = substr_replace($formatted_placa, ' ', -3, 0);
            $formatted_placa = $formatted_placa; // 'C-410 BPH'

            foreach ($registros as $registro) {
                if ($registro['Plate'] === $formatted_placa) {
                    $registros_filtrados[0] = $registro;
                }
            }
            
            echo "<pre>";
            echo "REGISTROS FILTADO<br>";
            print_r($registros_filtrados[0]);
            echo "</pre>";
            
            if (sizeof($registros_filtrados) > 0) {
                $info = $registros_filtrados[0];
                $novedaGPS['fec_noveda'] = (string) date('Y-m-d H:i:s',strtotime($info['DateTime']));
                $novedaGPS['val_veloci'] = (string) $info['Speed'];
                $novedaGPS['val_longit'] = (string) $info['Longitude'];
                $novedaGPS['val_latitu'] = (string) $info['Latitude'];
                $novedaGPS['det_ubicac'] = (string) utf8_decode($info['Address']);
                $novedaGPS['all_infgps'] = "Ubicacion: " . $novedaGPS['det_ubicac'];
                $novedaGPS['all_infgps'] .= ". Velocidad: " . $novedaGPS['val_veloci'];

                echo "<pre>";
                print_r($novedaGPS);
                echo "</pre>";
                $db5->StartTrans();
                //Se actualiza la fecha del ultimo consumo
                $fQueryUpdVehiGps = "UPDATE " . BASE_DATOS5 . ".t_vehicu_gpsxxx " .
                    "SET fec_ultcon = NOW() " .
                    "WHERE cod_operad = '" . $fVehiculo['cod_operad'] . "' " .
                    "AND cod_transp = '" . $fVehiculo['cod_transp'] . "' " .
                    "AND num_placax = '" . $fVehiculo['num_placax'] . "' ";

                if($db5->ExecuteCons($fQueryUpdVehiGps, "R") === FALSE){
                    throw new Exception("Error en UPDATE.".$fQueryUpdVehiGps, "3001");
                }



                if (isset($novedaGPS['all_infgps']) && $novedaGPS['all_infgps'] !== NULL) {
                          
                    //DESARROLLO LOGICA DE ENVIO API
                    $userTemp = 'ctorres';
                    $passTemp = 'F];6w=u+ih';
                    $standa = new Consult(array("server" => Hostx5, "user" => $userTemp, "passwd" => $passTemp, "db" => BASE_STANDA), $fExcept); //instancia de la clase para manejo de consultas de base de datos
                    
                    //consulta para traer el registro principal segun estructura en xls
                    $fQueryApiConect = "SELECT * FROM ".BASE_STANDA.".tab_connec_apixxx WHERE nom_baseda LIKE '".$fVehiculo['nom_aplica']."'";
                    $standa->ExecuteCons($fQueryApiConect);
                    $ApiInfo = $standa->RetMatrix("a");

                    if(sizeof($ApiInfo) > 0){
                        
                        $InfoConect = $ApiInfo[0];
                        $token = getTokenAuthApi( $InfoConect['url_apixxx'], $InfoConect['cod_tokenx'], 'InterUpCarga', 'A0c4$sH!f!3B');
                        $dataRespuesta = setCurl([
                            'Url' => $InfoConect['url_apixxx']."/getDispatch/".$fVehiculo['num_despac'],
                            'Headers' => [
                                          'app-id: '.$InfoConect['url_apixxx'],
                                          'Instance: '.$InfoConect['cod_tokenx'],
                                          'token: '.$token,  
                                          'Content-Type: application/json'
                                         ],
                            'Verbo' => 'GET',
                            'Body' => json_encode($objeto, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT) 
                        ]);

                        $info = $dataRespuesta['dat_respon']['data'];
                        

                        if($info['codResp']!=1000){
                            echo "<br>Informacion del despacho no encontrada. ".$fVehiculo['num_despac']." (".$fVehiculo['num_placax'].")";
                            continue;
                        }

                        //Consultamos la informacion del despacho que nos retorna la api
                        $infoDispatch = $info['dispatch_info'][0];

                        $mDespac =  [];
                        $mDespac['dispatch_id']      = $fVehiculo['num_despac'];
                        $mDespac['transporter_id']   = $fVehiculo['cod_transp'];
                        $mDespac['manifest']         = $infoDispatch['cod_manifi'];
                        $mDespac['plate']            = $fVehiculo['num_placax'];
                        $mDespac['novelty_id']       = '9183';
                        $mDespac['date_novelty']     = date("Y-m-d H:i", strtotime($novedaGPS['fec_noveda'])); 
                        $mDespac['observation']      = $novedaGPS['all_infgps'];
                        $mDespac['extra_time']       = 0;
                        $mDespac['place_name']       = 'InfoGPS';
                        $mDespac['destination_code'] = NULL;
                        $mDespac['last_point_time']  = NULL;
                        $mDespac['vehicle_speed']    = NULL;
                        $mDespac['latitude']         = $novedaGPS['val_latitu'];
                        $mDespac['longitude']        = $novedaGPS['val_longit'];
                        
                        
                        //ENVIO A LA API
                        /* 
                        array_walk_recursive($mDespac, function (&$value, $key) {
                            $value = utf8_decode($value);
                        });
                        
                        echo "<pre>";
                        print_r($mDespac);
                        echo "</pre>";
                        
                        $json = json_encode($mDespac, JSON_UNESCAPED_UNICODE);
                        $objeto = json_decode($json);

                        echo "<pre>";
                        print_r($objeto);
                        echo "</pre>";

                        $mResponse = setCurl([
                            'Url' => $InfoConect['url_apixxx']."/novelty-before-of-site",
                            'Headers' => [
                                          'app-id: '.$InfoConect['url_apixxx'],
                                          'Instance: '.$InfoConect['cod_tokenx'],
                                          'token: '.$token,  
                                          'Content-Type: application/json'
                                         ],
                            'Verbo' => 'POST',
                            'Body' => json_encode($objeto, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT) 
                        ]);

                        echo "<pre>";
                        print_r($mResponse);
                        echo "</pre>"; */

                        //Se envia la novedad GPS a la aplicacion sat o faro WS.
                        ini_set("soap.wsdl_cache_enabled", "0");
                            
                        $oSoapClient = new soapclient(WsdFAR, array("trace" => "1", 'encoding' => 'ISO-8859-1'));

                        $parametros = array(
                            "cod_transp" => $fVehiculo['cod_transp'],
                            "num_despac" => $fVehiculo['num_despac'],
                            "cod_noveda" => '9183',
                            "fec_noveda" => date("Y-m-d H:i", strtotime($novedaGPS['fec_noveda'])),
                            "des_noveda" => $novedaGPS['all_infgps'],
                            "val_longit" => $novedaGPS['val_longit'],
                            "val_latitu" => $novedaGPS['val_latitu'],
                            "nom_llavex" => '3c09f78c210a18b686ae2540b0d12358',
                            "cod_operad" => $fVehiculo['cod_operad'],
                            "nom_usuari" => $fVehiculo['usr_gpsxxx'],
                            "pwd_clavex" => $fVehiculo['clv_gpsxxx'],
                            "cod_manifi" => $fVehiculo['cod_manifi'],
                            "num_placax" => $fVehiculo['num_placax'],
                            "nom_aplica" => $fVehiculo['nom_aplica']
                            ); //Se usa una llave para que solo oet pueda usar el metodo
                            /* echo "<pre>";
                            print_r($parametros);
                            echo "</pre>"; */

                        $mResult = $oSoapClient->__call("setNovedadGPS", $parametros);
                        

                        echo "<pre>";
                        print_r($mResult);
                        echo "</pre>";

                        $mResult = explode("; ", $mResult);
                        $mCodResp = explode(":", $mResult[0]);
                        $mMsgResp = explode(":", $mResult[1]);
                    }   

                    if ("1000" != $mCodResp[1]) {
                        $mMessage = "******** Encabezado ******** \n";
                        $mMessage .= "Operacion: Insertar Novedad GPS \n";
                        $mMessage .= "Fecha y hora actual: " . date("Y-m-d H:i") . " \n";
                        $mMessage .= "Fecha Novedad: " . $parametros["fec_noveda"] . " \n";
                        $mMessage .= "Aplicacion: " . $parametros["nom_aplica"] . " \n";
                        $mMessage .= "Despacho: " . $parametros["num_despac"] . " \n";
                        $mMessage .= "Descripcion Novedad: " . $parametros["des_noveda"] . " \n";
                        $mMessage .= "Operador: " . $fVehiculo['cod_operad'] . " \n";
                        $mMessage .= "Placa: " . $fVehiculo['num_placax'] . " \n";
                        $mMessage .= "******** Detalle ******** \n";
                        $mMessage .= "Codigo de error: " .$mResponse[cod_respon] . " \n";
                        $mMessage .= "Mensaje de error: " . $mResponse[msg_respon] . " \n";

                        logError("Web service GPS setNovedaGPS" . var_export($mMessage, true));
                        //mail(NotMai, "Web service GPS setNovedaGPS", $mMessage, 'From: soporte.ingenieros@intrared.net');

                        /* if( strpos($mMsgResp[1], 'no se encuentra en ruta, o no esta registrado') !== false || strpos($mMsgResp[1], 'Aplicacion no encontrada') !== false  )
                          {
                          //Se ELIMINA de la lista de reportando GPS si no se encuentra en ruta
                          $fQueryDelVehiGps = "DELETE FROM ".BASE_DATOS5.".t_vehicu_gpsxxx " .
                          "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
                          "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
                          "AND num_placax = '".$fVehiculo['num_placax']."' ";

                          if( $db5 -> ExecuteCons( $fQueryDelVehiGps, "R" ) === FALSE )
                          throw new Exception( "Error en DELETE.", "3001" );
                          } */
                    } else {
                        //Se actualiza la fecha del ultimo reporte satisfactorio
                        $fQueryUpdVehiGps = "UPDATE " . BASE_DATOS5 . ".t_vehicu_gpsxxx " .
                            "SET fec_ultrep = NOW() " .
                            "WHERE cod_operad = '" . $fVehiculo['cod_operad'] . "' " .
                            "AND cod_transp = '" . $fVehiculo['cod_transp'] . "' " .
                            "AND num_placax = '" . $fVehiculo['num_placax'] . "' ";
                        if ($db5->ExecuteCons($fQueryUpdVehiGps, "R") === FALSE)
                            throw new Exception("Error en UPDATE.".$fQueryUpdVehiGps, "3001");
                    }
                }
                $db5->Commit();















            }
        }
    }
} catch (Exception $e) {
    $mTrace = $e->getTrace();
    $fExcept->CatchError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $fVehiculo['nom_aplica'], $fVehiculo['num_placax']);
    return FALSE;
}

/*! \fn: setCurl
*  \brief: Ejecuta curl para consumir la API
*  \author: Ing. Nelson Liberato
*  \date: 23/03/2022
*  \date modified: dd/mm/aaaa
*  \param: mData  arrayd e datos necesarios para la api
*  \return: Integer
*/
function setCurl($mData = NULL)
{
  try 
  {  
    // echo "<pre>"; print_r($mData);echo "</pre>"; 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL            , $mData['Url']);
    curl_setopt($ch, CURLOPT_HTTPHEADER     , $mData['Headers']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST  , $mData['Verbo']);
    if($mData['Verbo'] == 'POST'){ // si es post se envia BODY. El GET NO usa BODY
    curl_setopt($ch, CURLOPT_POSTFIELDS     , $mData['Body']);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT        , 5);


    $mResponse = curl_exec($ch); 
    curl_close($ch);
    //echo "<pre>"; print_r($mData);echo "</pre>"; 
    //echo "<pre>"; print_r($mResponse);echo "</pre>"; 

    $mResponse = json_decode($mResponse, true);


    return ['cod_respon' => $mResponse['data']['codResp'],  
            'msg_respon' => $mResponse['data']['msgResp'], 
            'num_despac' => $mResponse['data']['dispatch_id'], 
            'dat_respon' => $mResponse 
           ];
  } 
  catch (Exception $e) 
  {
    return ['cod_respon' => $e -> getMessage(),  
            'msg_respon' => $e -> getCode(), 
            'num_despac' => NULL, 
            'dat_respon' => $mResponse 
           ];
  }
}

function getToken($db5, $userGPS, $claveGPS)
{
    $mQuery = "SELECT MAX(cod_consec), num_tokenx, fec_regist 
                FROM " . BASE_DATOS5 . ".t_genera_token 
                WHERE fec_regist = '" . date("Y-m-d") . "' ";
    $db5->ExecuteCons($mQuery);
    $tokenx = $db5->RetMatrix("a");
    if (!trim($tokenx[0]['num_tokenx'])) {
        $oSoapClient = new soapclient('http://www.tsoapi.com/Authentication.asmx?wsdl', array("trace" => "1", 'encoding' => 'ISO-8859-1'));

        $mParams = array(
            "login" => $userGPS,
            "password" => $claveGPS,
            "AppId" => 2
        );
        $result = $oSoapClient->ValidateUser($mParams);

        $mQuery = "SELECT MAX(cod_consec) 
                   FROM " . BASE_DATOS5 . ".t_genera_token";
        $db5->ExecuteCons($mQuery);
        $max = $db5->RetMatrix("i");
        $max = (int) $max[0][0] + 1;

        $mQuery = "INSERT INTO " . BASE_DATOS5 . ".t_genera_token ( cod_consec, num_tokenx, fec_regist ) 
                    VALUES ( '" . $max . "', '" . $result->ValidateUserResult . "', DATE(NOW()) ) ";
        $db5->ExecuteCons($mQuery);

        return $result->ValidateUserResult;
    } else {
        return $tokenx[0]['num_tokenx'];
    }
}

 /*! \fn: getTokenAuthApi
     *  \brief: Metodo para consultar token para usar la API
     *  \author: Ing. Nelson Liberato
     *  \date: 24/03/2022
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \return: Integer
     */
function getTokenAuthApi( $urlApi, $instance, $user, $pass )
{
    try
      {
        $mResponse = setCurl([
                        'Url' => $urlApi."/authenticate?userId=".$user."&pwd=". $pass,
                        'Headers' => [
                                      'Instance: '.$instance, // este es la autorization para decir la aplicacion a cargar: satt_intgps || satt_dingps
                                      'Content-Type: application/json'
                                     ],
                        'Verbo' => 'GET' 
                     ]);          
        if($mResponse['cod_respon'] != 1000){ 
          throw new Exception($mResponse['msg_respon'], $mResponse['cod_respon']);
        }

        $token = $mResponse['dat_respon']['data']['token'];
        return $token;
      }
      catch(Exception $e)
      {
        return ['cod_respon' => $e -> getCode(), 'msg_respon' => "GL API GENERAL - ".$e -> getMessage()];
      }
    }

function logError($error)
{
    /*
    $archivo = "log_" . date('Y-m-d') . ".txt";

    if ($file = fopen("logs/" . $archivo, "a+")) {
        fwrite($file, "_______________________".date('Y-m-d H:i:s')."_______________________" . PHP_EOL);
        fwrite($file, $error . PHP_EOL);
        fwrite($file, "_____________________________________________________________________" . PHP_EOL);
        fclose($file);
    } else {
        echo "no se pudo crear el archivo";
    }*/
}


?>
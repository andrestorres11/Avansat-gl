<?php

/* **********************************************************************
 * @file executeCronGpsTrasat.php                                   *
 * @brief Cron inserta Novedades de GPSur                        *
 * @version 0.1                                                         *
 * @date 21 de Marzo de 2024                                       *
 * @author Ing. Cristian Andrés Torres                                  *
 * **********************************************************************/
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/


include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes propias.
include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker

$fLogs = array();
$userTemp = 'ctorres';
$passTemp = 'F];6w=u+ih';
$url = 'http://181.212.40.220/gondola/data.php';
$code = '15190409';

try {
    
    $db5 = new Consult(array("server" => Hostx5, "user" => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5), $fExcept); //instancia de la clase para manejo de consultas de base de datos
    
    //consulta para traer el registro principal segun estructura en xls
    $fQuerySelVehiculos = "SELECT a.cod_operad, a.cod_transp, a.nom_aplica, a.num_despac, " .
        "a.num_placax, a.fec_salida, a.usr_gpsxxx, a.clv_gpsxxx, " .
        "a.idx_gpsxxx, a.gps_adicio " .
        "FROM " . BASE_DATOS5 . ".t_vehicu_gpsxxx a " .
        "WHERE a.fec_salida <= NOW() " .
        "AND ( a.fec_ultrep < DATE_SUB( NOW(), INTERVAL 10 MINUTE ) OR fec_ultrep IS NULL ) " .
        "AND a.fec_creaci >= DATE_SUB( NOW(), INTERVAL 30 DAY ) " .
        "AND a.cod_operad = '".$code."' " .
        " ORDER BY a.fec_creaci ASC ";
    echo $fQuerySelVehiculos;
    $db5->ExecuteCons($fQuerySelVehiculos);
    $fRecorVehiculos = $db5->RetMatrix("a");
    echo "<hr>Cantidad " . $db5->RetNumRows()."</br>";
    $dateNow = date('Y/m/d');

    
    if ($db5->RetNumRows() > 0) {
        foreach ($fRecorVehiculos as $fVehiculo) {
            echo "Carrito ".$fVehiculo['num_placax']."<br>";
                // Obtiene el contenido de la URL

            // Obtiene el contenido de la URL
            $json = file_get_contents($url);

            // Utiliza una expresión regular para encontrar todos los objetos JSON válidos
            preg_match_all('/\{(?:[^{}]|(?R))*\}/', $json, $matches);

            // Itera sobre los objetos encontrados y decodifícalos
            foreach ($matches[0] as $object) {
                // Decodifica el JSON
                $data = json_decode($object, true);
                
                // Verifica si la decodificación fue exitosa
                if ($data !== null) {
                    if (isset($data['plate']) && $data['plate'] === $fVehiculo['num_placax']) {
                        // Procesa los datos obtenidos
                        $timestamp = $data['timestamp'];
                        $latitude = $data['latitude'];
                        $longitude = $data['longitude'];
                        $speed = $data['speed'];
                        $plate = $data['plate'];
                        $peripherals = $data['peripherals'];
            
                        // Accede a los campos de los periféricos
                        $engine = $peripherals['engine'];
                        $door = $peripherals['door'];
                        
                        $novedaGPS = organizaInfo($data);

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


                        $infoDispatch = $dataRespuesta['data']['data']['dispatch_info'][0];
                       
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
                        $mDespac['vehicle_speed']    = $novedaGPS['val_veloci'];
                        $mDespac['latitude']         = $novedaGPS['val_latitu'];
                        $mDespac['longitude']        = $novedaGPS['val_longit'];

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


                        $mResult = $oSoapClient->__call("setNovedadGPS", $parametros);
                        
                        echo "<pre>";
                        print_r($mResult);
                        echo "</pre>";
                        $mResult = explode("; ", $mResult);
                        $mCodResp = explode(":", $mResult[0]);
                        $mMsgResp = explode(":", $mResult[1]);
                        
                        
                        if($info['codResp']!=1000){
                            echo "<br>Informacion del despacho no encontrada. ".$fVehiculo['num_despac']." (".$fVehiculo['num_placax'].")";
                            continue;
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
                        } else {
                            $fQueryUpdVehiGps = "UPDATE " . BASE_DATOS5 . ".t_vehicu_gpsxxx " .
                                "SET fec_ultrep = NOW() " .
                                "WHERE cod_operad = '" . $fVehiculo['cod_operad'] . "' " .
                                "AND cod_transp = '" . $fVehiculo['cod_transp'] . "' " .
                                "AND num_placax = '" . $fVehiculo['num_placax'] . "' ";
    
                            if($db5->ExecuteCons($fQueryUpdVehiGps, "R") === FALSE){
                                throw new Exception("Error en UPDATE.".$fQueryUpdVehiGps, "3001");
                            }
                            $db5->Commit();
                                
                        }

                    }
                        // Haces lo que necesites con los datos obtenidos
                        echo "Timestamp: $timestamp, Latitude: $latitude, Longitude: $longitude, Speed: $speed, Plate: $plate, Engine: $engine, Door: $door <br>";
                    } else {
                        echo "La placa: ".$fVehiculo['num_placax']." No se enuentra en el json suministrado <br>";
                    }
                } else {
                    // Si la decodificación falla
                    echo "Error al decodificar el JSON: $object <br>";
                }
            }

        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
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


    return ['data' => $mResponse,  
            'status' => $mResponse['success'],
          ];
  } 
  catch (Exception $e) 
  {
    return ['info' => $e -> getMessage(),  
            'code' => $e -> getCode(), 
            'status' => false,
           ];
  }
}

 /*! \fn: getTokenAuthApi
 *  \author: Ing. Nelson Liberato
 *  \brief: Metodo para consultar token para usar la API
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
        if($mResponse['data']['data']['codResp'] != 1000){ 
            throw new Exception($mResponse['msgResp'], $mResponse['codResp']);
        }
        
        $token = $mResponse['data']['data']['token'];
        return $token;
    }
    catch(Exception $e)
    {
        return ['cod_respon' => $e -> getCode(), 'msg_respon' => "GL API GENERAL - ".$e -> getMessage()];
    }
}


function organizaInfo($array){
    
    // Crear un objeto DateTime a partir de la cadena de fecha
    $fecha_objeto = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $array['timestamp']);

    // Verificar si la creación del objeto fue exitosa
    if ($fecha_objeto !== false) {
        // Formatear la fecha según el nuevo formato
        $fecha_formateada = $fecha_objeto->format('Y-m-d H:i:s');
    }

    $novedaGPS['fec_noveda'] = (string) $fecha_formateada;
    $novedaGPS['val_veloci'] = (string) $array['speed'];
    $novedaGPS['val_longit'] = (string) $array['longitude'];
    $novedaGPS['val_latitu'] = (string) $array['latitude'];


    sleep(1);
    $url_ubica = "https://nominatim.openstreetmap.org/reverse?lat=" . $novedaGPS['val_latitu'] . "&lon=" . $novedaGPS['val_longit'] . "&format=json&limit=1&email=cristian.torres@grupooet.com";
    $json_ubica = file_get_contents($url_ubica);
    $data_ubica = json_decode($json_ubica, true);

    if($data_ubica !== null && isset($data_ubica['display_name'])){
        $novedaGPS['det_ubicac'] = (string) $data_ubica['display_name'];
    }else{
        $novedaGPS['det_ubicac'] = 'Sin Direccion.';
    }

    $novedaGPS['all_infgps'] = "Ubicacion: " . $novedaGPS['det_ubicac'];
    $novedaGPS['all_infgps'] .= ". Velocidad: " . $novedaGPS['val_veloci'];

    return $novedaGPS;
}

function sendInfoAvansatGL(){
    
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
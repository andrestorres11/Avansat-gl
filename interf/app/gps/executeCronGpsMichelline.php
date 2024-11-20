<?php

/* **********************************************************************
 * @file executeCronGpsMichelline.php                                *
 * @brief Cron inserta Novedades de Michelline GPS                      *
 * @version 0.1                                                         *
 * @date 30 de Diciembre de 2021                                            *
 * @modified 30 de Diciembre de 2021                                        *
 * @author Ing. Oscar Bocanegra                                            *
 * ***********************************************************************/
ini_set("display_errors", true);
error_reporting(E_ALL && ~E_NOTICE);

include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes propias.
include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker

$fExcept = new Error(array("dirlog" => LogDir, "notlog" => TRUE));
$fExcept->SetUser('InterfGPS');
$fExcept->SetParams("Sat", "NovedaGPSTracker");

$fLogs = array();

try {
    $db5 = new Consult(array("server" => Hostx5, "user" => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5), $fExcept); //instancia de la clase para manejo de consultas de base de datos
    //consulta para traer el registro principal segun estructura en xls
    echo $fQuerySelVehiculos = "SELECT a.cod_operad, a.cod_transp, a.nom_aplica, a.num_despac, " .
        "a.num_placax, a.fec_salida, a.usr_gpsxxx, a.clv_gpsxxx, " .
        "a.idx_gpsxxx " .
        "FROM " . BASE_DATOS5 . ".t_vehicu_gpsxxx a " .
        "INNER JOIN " . BASE_DATOS5 . ".t_interf_parame b " .
        "ON a.cod_transp = b.cod_transp " .
        "AND a.cod_operad = b.cod_operad " .
        "WHERE a.fec_salida <= NOW() " .
        "AND ( a.fec_ultrep < DATE_SUB( NOW(), INTERVAL 10 MINUTE ) OR fec_ultrep IS NULL ) " .
        "AND a.fec_creaci >= DATE_SUB( NOW(), INTERVAL 30 DAY ) " .
        "AND a.cod_operad = '9999119' " .
        "ORDER BY a.fec_creaci ASC ";
        // datos inventados 901282888
    $db5->ExecuteCons($fQuerySelVehiculos);
    $fRecorVehiculos = $db5->RetMatrix("a");

    echo "<hr>Cantidad " . $db5->RetNumRows();

    if ($db5->RetNumRows() != 0) {
        $mParams = array( 
            'usuario'     => $fRecorVehiculos[0]['usr_gpsxxx'], 
            'senha' => base64_decode($fRecorVehiculos[0]['clv_gpsxxx']), 
            'quantidade' => '' 
        );

        $mSoap =  new SoapClient('http://sasintegra.sascar.com.br/SasIntegra/SasIntegraWSService?wsdl' , array( "trace" => TRUE, 'encoding' => 'ISO-8859-1' ) ); 
        $result = $mSoap->obterPacotePosicoesMotoristaComPlaca($mParams);
        //Formatea los datos retornados del webservice y lo convierte en array para poderse manejar.
        $result = json_decode(json_encode($result), true);
        $result = $result['return'];  

        foreach ($fRecorVehiculos as $fVehiculo) {

            echo "<br>".$fVehiculo['num_placax']."<br><br>";

            unset($novedaGPS);
            $novedaGPS = array();

            echo "<hr><pre>Datos Vehiculo";
            print_r($fVehiculo);
            echo "</pre>";
            
            /*echo "<hr><pre>Respuesta Webservice";
            echo "<pre>"; print_r($result); echo "</pre>"; */
                       
            // veo bien el codigo, ya puede continuar con el json que reponde el curl $result
            $validarInfo = TRUE;
            if($result === FALSE || is_null($result)) {
                $validarInfo = FALSE;
            } else if(property_exists($result, "error") || property_exists($result, "mensaje")){
                $validarInfo = FALSE;
            }

            if ($validarInfo) {
                if (sizeof($result) > 0) {
                    $rowVPosicion = array();
                    //Recorre el array y almacena la ultima posicion del vehiculo encontrado por el reporte gps.
                    foreach($result as $elemento){
                        if($elemento['placa'] == $fVehiculo['num_placax']){
                            $rowVPosicion = $elemento;
                            break;
                        }
                    }

                    if(sizeof($rowVPosicion) > 0){
                        $novedaGPS['fec_noveda'] = (string) date('Y-m-d H:i:s',strtotime($rowVPosicion['dataPacote']));
                        $novedaGPS['val_veloci'] = (string) $rowVPosicion['velocidade'];
                        $novedaGPS['val_longit'] = (string) $rowVPosicion['latitude'];
                        $novedaGPS['val_latitu'] = (string) $rowVPosicion['longitude'];
                        $novedaGPS['det_ubicac'] = (string) $rowVPosicion['cidade'];
                        $novedaGPS['all_infgps'] = "Ubicacion: " . $novedaGPS['det_ubicac'];
                        $novedaGPS['all_infgps'] .= ". Velocidad: " . $novedaGPS['val_veloci'];

                        /* SI HAY NOVEDAD, ENTONCES ACTUALICE LA TABLA DE VEHICULOS Y GPS */
                        /*echo "<hr><pre>NovedadGPS";
                        print_r($novedaGPS);
                        echo "/<pre>";*/
                        
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
                            //Se envia la novedad GPS a la aplicacion sat o faro.
                            ini_set("soap.wsdl_cache_enabled", "0");
                            
                            $oSoapClient = new soapclient(WsdFAR, array("trace" => "1", 'encoding' => 'ISO-8859-1'));
    
                            $parametros = array(
                                "cod_transp" => $fVehiculo['cod_transp'],
                                "num_despac" => $fVehiculo['num_despac'],
                                "cod_noveda" => '4999',
                                "fec_noveda" => date("Y-m-d H:i", strtotime($novedaGPS['fec_noveda'].' -2 hour' )),
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
                                echo "<pre>";
                                print_r($parametros);
                                echo "</pre>";

                            $mResult = $oSoapClient->__call("setNovedadGPS", $parametros);
                            

                            echo "<pre>";
                            print_r($mResult);
                            echo "</pre>";
    
                            $mResult = explode("; ", $mResult);
                            $mCodResp = explode(":", $mResult[0]);
                            $mMsgResp = explode(":", $mResult[1]);
                            //$fVehiculo['num_placax'] = 'WHB760';
    
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
                                $mMessage .= "Codigo de error: " . $mCodResp[1] . " \n";
                                $mMessage .= "Mensaje de error: " . $mMsgResp[1] . " \n";
    
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
            else{
                logError(var_export($result,true) . var_export($fVehiculo, true));
            }
        }
    }
    else {
        //echo "No hay vehiculos reportando gps";
    }
} catch (Exception $e) {
    $mTrace = $e->getTrace();
    $fExcept->CatchError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $fVehiculo['nom_aplica'], $fVehiculo['num_placax']);
    return FALSE;
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

function logError($error)
{
    $archivo = "log_" . date('Y-m-d') . ".txt";

    if ($file = fopen("logs/" . $archivo, "a+")) {
        fwrite($file, "_______________________".date('Y-m-d H:i:s')."_______________________" . PHP_EOL);
        fwrite($file, $error . PHP_EOL);
        fwrite($file, "_____________________________________________________________________" . PHP_EOL);
        fclose($file);
    } else {
        echo "no se pudo crear el archivo";
    }
}

?>
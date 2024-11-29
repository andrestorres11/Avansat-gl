<?php
/*********************************************************************************************/
/* @file executeCronItinerario.php                                                           */
/* @brief Cron que valida y envia los despachos al integrador gps                            */
/* @version 1                                                                                */
/* @date 2021/10/14                                                                          */
/* @author Ing. NELSON GABRIEL                                                               */
/*********************************************************************************************/
        //error_reporting(E_ALL);
        //ini_set('display_errors', '1');
class cronItinerario
{
    var $db4 = NULL;
    var $PDF = NULL;
    var $dir = "/var/www/html/ap/interf/app/externo/";
    var $fExcept = NULL;

    var $logFileError = NULL;
    var $logFileSuccess = NULL;
    function __construct()
    {
        $noimport = true;
    
        include_once("/var/www/html/ap/satt_intgps/crones/Config.kons.inc");
        include_once("/var/www/html/ap/interf/lib/funtions/General.fnc.php");
        include_once("/var/www/html/ap/satt_intgps/constantes.inc"); 

        try {
            $this->logFileError = LogIntGPS.'fails/';
            $this->logFileSuccess = LogIntGPS.'success/';
            $this->fExcept = new Error(array("dirlog" => $this->logFileError, "notlog" => TRUE, "logmai" => NotMai));
            $this->fExcept->SetUser('CronItinerario');
            $this->fExcept->SetParams("Faro", "Validación de fechas cita de cargue y creación de itinerario");
            $fLogs = array();
            $this->db4 = new Consult(array("server" => HOST, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS), $this->fExcept);
             
            $this->getDespachos();
        } catch (Exception $e) {

            
            $mTrace = $e->getTrace();
            $this->fExcept->CatchError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            return FALSE;
        }
    }

    function getDespachos()
    {
        $query = "
                     SELECT
                            a.num_despac AS Despacho, a.cod_manifi, 
                            NOW() AS ACTUAL, 
                            DATE_FORMAT( DATE_SUB( DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ) , INTERVAL 24 HOUR) , '%Y-%m-%d %H:%i:%s' ) AS 24_HORAS_ATRAS, 
                            DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ) AS FECHA_CITA_CARGUE,
                            b.num_placax, a.fec_salida AS fec_inicio, DATE_ADD(a.fec_salida, INTERVAL 5 DAY ) AS fec_finali,
                            c.url_webser, c.cod_tokenx, c.ind_deseta, c.tie_report, b.cod_transp,
                            c.int_config, a.gps_operad, d.nom_tercer, b.fec_envint, b.cod_respon
                       FROM 
                                      " . BASE_DATOS . ".tab_despac_despac a 
                           INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac
                           INNER JOIN " . BASE_DATOS . ".tab_interf_parame c ON b.cod_transp = c.cod_transp AND c.ind_estado = '1' AND 
                                                                                                                c.cod_operad = 53
                           INNER JOIN satt_intgps.tab_tercer_tercer d ON b.cod_transp = d.cod_tercer
                       WHERE -- a.num_despac = 399 AND
                               a.fec_salida IS NOT NULL 
                           AND a.fec_salida <= NOW() 
                           AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                           AND a.ind_planru = 'S' 
                           AND a.ind_anulad in ('R')
                           AND b.ind_activo = 'S' 
                           AND b.cod_itiner IS NULL                          
                           AND  ( 
                                        b.msg_itiner NOT LIKE '%El usuario o el password no son correctos.%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%El usuario o el password no son correctos 2.%'  
                                    AND b.msg_itiner NOT LIKE '%El usuario o el password no son correctos o WS no arroja datos CRON OET.%'  
                                    AND b.msg_itiner NOT LIKE '%El Robot ya esta ejecutado%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%La placa no pertenece al usuario%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%No es posible entrar al sistema. Por favor comuníquese con su proveedor%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%Plataforma NO homologada%' /*Plataforma no homologada */
                                    AND b.msg_itiner NOT LIKE '%Punto(s) no georeferenciado(s) o no coinciden las coordenadas recibidas%' /*Punto(s) no georeferenciado(s) */
                                    AND b.msg_itiner NOT LIKE '%El ID del gps no puede ser con letras debe ser solo numerico%' /*El ID del gps no puede ser con letras */
                                    AND b.msg_itiner NOT LIKE '%Cliente no existe en plataforma.%' /*El ID del gps no puede ser con letras */
                                    OR b.msg_itiner IS NULL
                               )
                           AND a.gps_operad IS NOT NULL
                           AND a.gps_usuari IS NOT NULL
                           AND a.gps_paswor IS NOT NULL
                           -- AND (b.cod_respon IS NULL OR b.cod_respon = 201)
                           AND (b.fec_envint IS NULL OR TIMESTAMPDIFF(MINUTE, fec_envint, NOW()) > 10) ;
                      ";
        echo "<pre>".$query."</pre>";   
        $mExec = $this->db4->ExecuteCons($query); 
        $despachos = $this->db4->RetMatrix("a");
        
        // Filtrar los registros que cumplen con la condición
        $despachosFiltrados = array_filter($despachos, function($despacho) {
            $intConfig = json_decode($despacho['int_config'], true);
            return isset($intConfig['gpsIntegration']) && $intConfig['gpsIntegration'] === true;
        });

        // Actualizar la variable $despachos con los registros filtrados
        $despachos = array_values($despachosFiltrados);

        //echo "<pre>Despachos a enviar: "; print_r( count($despachos) ); echo "</pre>";
        
        $mItiner = new IntegradorGps($this->fExcept, $this->db4); // Instancia a integrador GPS    
        $mHUB    = new InterfHubIntegradorGPS($this->db4); // Instancia a HUB GPS    


        // $controlador1 = new DespachoControlador(); // Incluye la api para enviar el despacho a la central
        $mDestin = [];
        $mParam  = [];
        $mGL     = [];

        echo '<table style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">
                <tr>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Despacho</th>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Manifiesto</th>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Placa</th>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Transportadora</th>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Tipo</th>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Tiempo</th>
                    <th style="border: 1px solid #dddddd; background-color: #f2f2f2; padding: 8px;">Respuesta Integrador</th>
                </tr>';
        foreach ($despachos as $key => $value) {
            
            $mIndDesEta = json_decode($value['ind_deseta'], true);
            $sHub = 0;
            $sWid = 0;
            //Ajuste temporal si el operador es DETEKTOR solo integra por tipo HUB con el fin de no usar widetech 
            if($value['gps_operad']=='9010949280'){
                $mIndDesEta['TIPO'] = 'HUB';
                $value["url_webser"] = 'https://oet-central.intrared.net/ap/interf/APIIntegradorGPS/v2/index.php';
            }


            //Parche para no enviar la integracion a widetech
            if($value['cod_transp']=='900306833' && $value['gps_operad']=='8301060079'){
                $mIndDesEta['TIPO'] = 'HUB';
                $value["url_webser"] = 'https://oet-central.intrared.net/ap/interf/APIIntegradorGPS/v2/index.php';
            }

            //Parche para no enviar la integracion a widetech
            if($value['cod_transp']=='900284054' && $value['gps_operad']=='8301060079'){
                $mIndDesEta['TIPO'] = 'HUB';
                $value["url_webser"] = 'https://oet-central.intrared.net/ap/interf/APIIntegradorGPS/v2/index.php';
            }
            
            if($mIndDesEta['TIPO'] == 'FULL' || $mIndDesEta['TIPO'] == 'BASICO_1' || $mIndDesEta['TIPO'] == 'BASICO_2')
            { 
                $mRespItiner = $mItiner->setCarguePlacaIntegradorGPS($value["Despacho"], ['ind_transa' => 'I'], "CronItinerario", $value);
                $sWid = 1;
            }
            else {

                //Error de Fecha menor a fecha actual - 201
                if($value["cod_respon"] == 201){
                    $value["fec_inicio"] = date('Y-m-d H:i:s');
                }

                $mRespItiner = $mHUB -> setTrakingStart([
                                                            'cod_transp' => $value["cod_transp"],
                                                            'cod_tokenx' => $value["cod_tokenx"],
                                                            'num_placax' => $value["num_placax"],
                                                            'num_docume' => $value["cod_manifi"],
                                                            'num_despac' => $value["Despacho"],
                                                            'fec_inicio' => $value["fec_inicio"],
                                                            'fec_finali' => $value["fec_finali"],
                                                            'ind_origen' => '3',
                                                            'tie_report' => $value["tie_report"],
                                                            'url_webser' => $value["url_webser"]
                                                        ]);
                $sHub = 1;
                                                        
            }

            $message = "Despacho: ". $value["Despacho"]."\n";
            $message .= "Manifiesto: ". $value["cod_manifi"]."\n";
            $message .= "Transportadora: ". $value["cod_transp"]."\n";
            $message .= "Placa: ". $value["num_placax"]."\n";
            $message .= "Tipo Send: ". $mIndDesEta['TIPO']."\n";
            $message .= "Resp Send: ". var_export($mRespItiner, true)."\n";
            $respuesta = '';
            if($mRespItiner['code']=='1000' OR $mRespItiner['code_resp']=='1000'){
                $this->log($message,'SUCCESS');
                $respuesta = 'Exito - ';
                $respuesta .= $sHub ? $mRespItiner['message'] : $mRespItiner['msg_resp'];
                $color = '#FFF';
                $this->updateDateSend($value["Despacho"]);
            }else{
                $this->log($message,'ERROR');
                $respuesta = 'Error - ';
                $respuesta .= $sHub ? $mRespItiner['message'] :$mRespItiner[0];
                $color = '#FB5858';
            }
            echo '<tr>
                    <td style="border: 1px solid #dddddd; padding: 8px;">'.$value["Despacho"].'</td>
                    <td style="border: 1px solid #dddddd; padding: 8px;">'.$value["cod_manifi"].'</td>
                    <td style="border: 1px solid #dddddd; padding: 8px;">'.$value["num_placax"].'</td>
                    <td style="border: 1px solid #dddddd; padding: 8px;">'.$value["nom_tercer"].'</td>
                    <td style="border: 1px solid #dddddd; padding: 8px;">'.$mIndDesEta['TIPO'].'</td>
                    <td style="border: 1px solid #dddddd; padding: 8px;">'.$value["tie_report"].'</td>
                    <td style="border: 1px solid #dddddd; padding: 8px; background-color:'.$color.'">'.$respuesta .'</td>
                </tr>'; 
            
        }
        

    }

    // Mï¿½todo para registrar eventos
    function log($message, $type='SUCCESS') {
        $rut_log = $this->logFileSuccess;
        if($type != 'SUCCESS'){
            $rut_log = $this->logFileError;
        }
        // Obtener la fecha y hora actual
        /*
        $date = date('Y-m-d H:i:s');
        $mLogFile = $rut_log."log_".date( "Y_m_d" ).".log";
        $file = fopen( $mLogFile, "a+" );
        // Escribir el mensaje de registro en el archivo
        fwrite($file, "============================================================\n");
        fwrite($file, "[{$date}]\n");
        fwrite($file, "{$message}\n");
        
        // Cerrar el archivo
        fclose($file);*/
    }


    function updateDateSend($num_despac){
        $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige SET fec_envint = NOW() WHERE num_despac = '".$num_despac."' ";
        $mExec = $this->db4->ExecuteCons($query); 
    }

}

$_CRON = new cronItinerario();

?>
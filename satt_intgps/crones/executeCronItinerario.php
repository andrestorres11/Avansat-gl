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
    function __construct()
    {
        $noimport = true;
    
        include_once("/var/www/html/ap/satt_intgps/crones/Config.kons.inc");
        include_once("/var/www/html/ap/interf/lib/funtions/General.fnc.php");
        include_once("/var/www/html/ap/satt_intgps/constantes.inc"); 

        try {
            $this->fExcept = new Error(array("dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai));
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

        //Prueba de envío
        //mail("edgar.clavijo@eltransporte.org", "Prueba", "Envío cron");

        //Create query
 

        $query = "
                     SELECT
                            a.num_despac AS Despacho, a.cod_manifi, 
                            NOW() AS ACTUAL, 
                            DATE_FORMAT( DATE_SUB( DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ) , INTERVAL 24 HOUR) , '%Y-%m-%d %H:%i:%s' ) AS 24_HORAS_ATRAS, 
                            DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ) AS FECHA_CITA_CARGUE,
                            b.num_placax, a.fec_salida AS fec_inicio, DATE_ADD(a.fec_salida, INTERVAL 5 DAY ) AS fec_finali,
                            c.url_webser, c.cod_tokenx, c.ind_deseta, c.tie_report, b.cod_transp

                       FROM 
                                      " . BASE_DATOS . ".tab_despac_despac a 
                           INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac
                           INNER JOIN " . BASE_DATOS . ".tab_interf_parame c ON b.cod_transp = c.cod_transp AND c.ind_estado = '1' AND 
                                                                                                                c.cod_operad = 53
                       WHERE 
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
                                    AND b.msg_itiner NOT LIKE '%El Robot ya esta ejecutado%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%La placa no pertenece al usuario%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%No es posible entrar al sistema. Por favor comuníquese con su proveedor%' /*Reenvia excepto estos mensajes */
                                    AND b.msg_itiner NOT LIKE '%Plataforma NO homologada%' /*Plataforma no homologada */
                                    AND b.msg_itiner NOT LIKE '%Punto(s) no georeferenciado(s) o no coinciden las coordenadas recibidas%' /*Punto(s) no georeferenciado(s) */
                                    AND b.msg_itiner NOT LIKE '%El ID del gps no puede ser con letras debe ser solo numerico%' /*El ID del gps no puede ser con letras */
                                    OR b.msg_itiner IS NULL
                               )
                           AND a.gps_operad IS NOT NULL
                           AND a.gps_usuari IS NOT NULL
                           AND a.gps_paswor IS NOT NULL 
                      ";

 
        $mExec = $this->db4->ExecuteCons($query); 
        $despachos = $this->db4->RetMatrix("a");


        //echo "<pre>Despachos a enviar: "; print_r(  $despachos); echo "</pre>";
      

        
        $mItiner = new IntegradorGps($this->fExcept, $this->db4); // Instancia a integrador GPS    
        $mHUB    = new InterfHubIntegradorGPS($this->db4); // Instancia a HUB GPS    


        // $controlador1 = new DespachoControlador(); // Incluye la api para enviar el despacho a la central
        $mDestin = [];
        $mParam  = [];
        $mGL     = [];
        foreach ($despachos as $key => $value) {
            
            echo "<pre><h1>Despacho: "; print_r($value["Despacho"]  ); echo "   Manifiesto: ".$value["cod_manifi"] ."</pre>";
            // Envío a integrador GPS el despacho si y solo si tiene información de GPS para hacer el primer itinerario para la parte de cargues
            if($value['ind_deseta'] == '1')
            { 
                $mRespItiner = $mItiner->setCarguePlacaIntegradorGPS($value["Despacho"], ['ind_transa' => 'I'], "CronItinerario");
            }
            else {
                $mRespItiner = $mHUB -> setTrakingStart([
                                                            'cod_transp' => $value["cod_transp"],
                                                            'cod_tokenx' => $value["cod_tokenx"],
                                                            'num_placax' => $value["num_placax"],
                                                            'num_docume' => $value["cod_manifi"],
                                                            'fec_inicio' => $value["fec_inicio"],
                                                            'fec_finali' => $value["fec_finali"],
                                                            'ind_origen' => '3',
                                                            'tie_report' => $value["tie_report"],
                                                            'url_webser' => $value["url_webser"]
                                                        ]);
            }

            echo "<pre>Reponse Intgrador: "; print_r($mRespItiner); echo "</pre>";

            // ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            // ----------------------------------------------------       D A T O S  P A R A   E N V I A R.  A.  L A   A P P         -----------------------------------------------------------------------------------
            // ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            /*$response = $controlador1->registrar($this->db4, $value["Despacho"], "860068121");   
            if ($response->cod_respon != '1000') 
            {
                $this->fExcept->CatchError($response->cod_respon, 'DespachoControlador APP :'.$value['Despacho'].' - '.$value['cod_manifi'].' - '.$value['num_placax'].' '.$response->msg_respon , '.....', '168'); 
            }*/
         
            
        }
        

    }
}

$_CRON = new cronItinerario();

?>
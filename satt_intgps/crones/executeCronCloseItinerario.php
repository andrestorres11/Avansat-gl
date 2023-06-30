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
//die("Muere por fastidioso");
class cronCloseItinerario
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
            $this->fExcept->SetUser('cronCloseItinerario');
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
                            a.num_despac AS Despacho, a.cod_manifi, b.num_placax,
                            NOW() AS ACTUAL, 
                            DATE_FORMAT( DATE_SUB( DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ) , INTERVAL 24 HOUR) , '%Y-%m-%d %H:%i:%s' ) AS 24_HORAS_ATRAS, 
                            DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ) AS FECHA_CITA_CARGUE,
                            b.num_placax, a.fec_salida AS fec_inicio, DATE_ADD(a.fec_salida, INTERVAL 5 DAY ) AS fec_finali,
                            c.url_webser, c.cod_tokenx, c.ind_deseta, c.tie_report, b.cod_transp, b.cod_itiner

                       FROM 
                                      " . BASE_DATOS . ".tab_despac_despac a 
                           INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac
                           INNER JOIN " . BASE_DATOS . ".tab_interf_parame c ON b.cod_transp = c.cod_transp AND c.ind_estado = '1' AND 
                                                                                                                c.cod_operad = 53
                       WHERE 
                               a.fec_salida IS NOT NULL 
                           AND a.fec_salida <= NOW()                              
                           AND a.fec_llegad IS NOT NULL  
                           AND a.fec_llegad >= DATE_SUB( NOW(), INTERVAL 4 MINUTE )
                    

                           AND b.cod_itiner IS NOT NULL                          
                            
                           AND a.gps_operad IS NOT NULL
                           AND a.gps_usuari IS NOT NULL
                           AND a.gps_paswor IS NOT NULL 
                      ";
        echo $query;
        //die();
        $mExec = $this->db4->ExecuteCons($query); 
        $despachos = $this->db4->RetMatrix("a");
        

        echo "<pre>Despachos a enviar: "; print_r(  $despachos); echo "</pre>";
      

        
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
                $mRespItiner = $mItiner->setCarguePlacaIntegradorGPS($value["Despacho"], ['ind_transa' => 'Q'], "CronItinerario");
            }
            else {
                $mRespItiner = $mHUB -> setTrakingEnd([
                                                            'cod_transp' => $value["cod_transp"],
                                                            'cod_tokenx' => $value["cod_tokenx"],
                                                            'num_placax' => $value["num_placax"],
                                                            'num_docume' => $value["cod_manifi"],
                                                            'num_despac' => $value["Despacho"],
                                                            'fec_inicio' => $value["fec_inicio"],
                                                            'fec_finali' => $value["fec_finali"],
                                                            'ind_origen' => '3',
                                                            'tie_report' => $value["tie_report"],
                                                            'cod_itiner' => $value["cod_itiner"],
                                                            'url_webser' => $value["url_webser"],
                                                            'obs_cierre' => 'Cerrado por cron',
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

$_CRON = new cronCloseItinerario();

?>
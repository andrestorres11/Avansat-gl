<?php
/*********************************************************************************************/
/* @file executecronSinGPSEnELDespacho.php                                                           */
/* @brief Valida las fechas citas de descargue y consume API de creación de itinerarios.     */
/* @version 1                                                                                */
/* @date 2019/04/06                                                                          */
/* @author EDGAR FELIPE CLAVIJO SANTOYO                                                      */
/*********************************************************************************************/
 
define( "ValDir", "/var/www/html/ap/interf/app/faro/validator/"); 
class cronSinGPSEnELDespacho
{
    var $db4 = NULL;
    var $PDF = NULL;
    var $dir = "/var/www/html/ap/interf/app/externo/";
    var $fExcept = NULL;
    function __construct()
    {
        $noimport = true;
        include_once("/var/www/html/ap/interf/app/faro/lib/Config.kons.php");
        include_once("/var/www/html/ap/interf/app/faro/Config.kons.php");
        include_once("/var/www/html/ap/interf/lib/funtions/General.fnc.php");
        include_once("/var/www/html/ap/satt_intgps/constantes.inc"); 
        include_once("/var/www/html/ap/interf/app/faro/faro.php");


        try {
            $this->fExcept = new Error(array("dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai));
            $this->fExcept->SetUser('CronIntegrador');
            $this->fExcept->SetParams("satt_intgps", "Novedad de despacho sin GPS");
            $fLogs = array();
            $this->db4 = new Consult(array("server" => Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS), $this->fExcept);
            $this->getDespachos();
        } catch (Exception $e) {
            $mTrace = $e->getTrace();
            $this->fExcept->CatchError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            return FALSE;
        }
    }

    function getDespachos()
    {
        $query = " SELECT 
                           a.num_despac, a.cod_manifi, b.num_placax, a.fec_despac, a.fec_salida, c.cod_operad, c.nom_operad, c.ind_intgps,
                           b.cod_transp, d.int_config
                      FROM 
                           ".BASE_DATOS.".tab_despac_despac a
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                LEFT  JOIN ".BD_STANDA.".tab_genera_opegps c ON a.gps_operad = c.cod_operad
                INNER JOIN " . BASE_DATOS . ".tab_interf_parame d ON b.cod_transp = d.cod_transp AND d.ind_estado = '1' AND d.cod_operad = 53 
                     WHERE a.fec_salida IS NOT NULL
                       AND a.fec_llegad IS NULL
                       AND a.ind_planru = 'S'
                       AND a.ind_anulad = 'R'
                       AND b.ind_activo = 'S'
                       AND (  c.cod_operad IS NULL OR c.ind_intgps = 0 )
                       AND a.num_despac NOT IN (SELECT num_despac FROM ".BASE_DATOS.".tab_despac_contro WHERE cod_noveda IN ( 9272 ) )
                   GROUP BY a.num_despac   ";  
      
        $this->db4->ExecuteCons($query);
        $mDespachos = $this->db4->RetMatrix("a");
        
        $despachosFiltrados = array_filter($mDespachos, function($despacho) {
            $intConfig = json_decode($despacho['int_config'], true);
            return isset($intConfig['gpsIntegration']) && $intConfig['gpsIntegration'] === true;
        });

        $mDespachos = array_values($despachosFiltrados);

        foreach ($mDespachos AS $mIndex => $mDespac) 
        {
            $mNovresp = setNovedadNC(  'InterfGpsIntegr', '0zV;Q;%5=zL6TG',
                                       $mDespac['cod_transp'] , $mDespac['cod_manifi'], $mDespac['num_placax'], '9272', 
                                       NULL, NULL, date('Y-m-d H:i:s'), 
                                       'Viaje sin operador GPS asignado.', 
                                       NULL, NULL,'SIN GPS', NULL,NULL, NULL, NULL, NULL, NULL, 
                                       'satt_intgps' );
 
            echo "<pre><hr>Despacho: ".$mDespac['num_despac']."<br>"; print_r( $mNovresp ); echo "</pre>";
            
            
        }
        

    }
}

$_CRON = new cronSinGPSEnELDespacho();

?>
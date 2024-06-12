<?php
/*********************************************************************************************/
/* @file executeCronVehicuDetenido.php                                                       */
/* @brief Valida la fecha de la ultima novedad reportada en los despachos.                   */
/* @version 1                                                                                */
/* @date 2020/04/14                                                                          */
/* @author LUIS CARLOS MANRIQUE BOADA                                                        */
/*********************************************************************************************/
define( "ValDir", "/var/www/html/ap/interf/app/faro/validator/"); 
class cronVehicuDetenido
{
    var $db4 = NULL;
    var $PDF = NULL;
    var $dir = "/var/www/html/ap/interf/app/externo/";
    var $fExcept = NULL;
    function __construct()
    {
        $noimport = true;
        $mInclude = true;

        include_once("/var/www/html/ap/interf/app/faro/lib/Config.kons.php");
        include_once("/var/www/html/ap/interf/app/faro/Config.kons.php");
        include_once("/var/www/html/ap/interf/lib/funtions/General.fnc.php");
        include_once("/var/www/html/ap/satt_intgps/constantes.inc"); 
        include_once("/var/www/html/ap/interf/app/faro/faro.php");

        try {
            $this->fExcept = new Error(array("dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai));
            $this->fExcept->SetUser('CronIntegrador');
            $this->fExcept->SetParams("satt_intgps", "Novedad a depsachos con Novedad más de una hora");
            $fLogs = array();
            $this->db4 = new Consult(array("server" => Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS), $this->fExcept);
            $this->getDespachos();
            //$this->getDespachosDetalle();
        } catch (Exception $e) {
            $mTrace = $e->getTrace();
            $this->fExcept->CatchError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            return FALSE;
        }
    }

    /***********************************************************************
    * Funcion Consulta la ultima novedad con velocidad en 0                *
    * Validando si aplica el registro de la novedad                        *
    * @fn getDespachos.                                                    *
    * @brief  Consulta la ultima novedad con velocidad en 0                *
    * Validando si aplica el registro de la novedad                        *
    * @return string mensaje de respuesta.                                 *
    ************************************************************************/

    function getDespachos()
    {
         
        //error_reporting(E_ALL);
        //ini_set('display_errors', '1');

         $query = "  SELECT 
                        a.num_despac, 
                        a.cod_manifi,
                        b.num_placax, 
                        c.cod_noveda, 
                        c.fec_noveda,
                        c.kms_vehicu,
                        c.usr_creaci,
                        TIMESTAMPDIFF(HOUR, c.fec_noveda, NOW()) AS hor_transc,
                        TIMESTAMPDIFF(MINUTE, c.fec_noveda, NOW()) AS min_transc,
                        b.cod_transp
                    FROM 
                        ".BASE_DATOS.".tab_despac_despac a 
                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                        INNER JOIN (
                                        SELECT b.num_despac, b.cod_noveda, b.fec_noveda, b.kms_vehicu, b.usr_creaci
                                        FROM (
                                                (
                                                      SELECT a.num_despac, d.cod_noveda, d.fec_noveda, d.kms_vehicu, d.usr_creaci
                                                        FROM ".BASE_DATOS.".tab_despac_despac a
                                                  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                                                  INNER JOIN ".BASE_DATOS.".tab_despac_noveda d ON a.num_despac = d.num_despac AND d.kms_vehicu IS NOT NULL
                                                  INNER JOIN (
                                                                SELECT (MAX(fec_noveda)) AS fec_noveda, num_despac
                                                                    FROM ".BASE_DATOS.".tab_despac_noveda 
                                                                    WHERE kms_vehicu IS NOT NULL
                                                                    GROUP BY num_despac
                                                                ) e ON a.num_despac = e.num_despac AND e.fec_noveda = d.fec_noveda
                                                   INNER JOIN (
                                                                SELECT a.num_despac
                                                                  FROM ".BASE_DATOS.".tab_despac_seguim a 
                                                                 WHERE a.cod_contro NOT IN (9999) 
                                                                   AND a.ind_estado = 1
                                                                GROUP BY a.num_despac
                                                            ) f ON a.num_despac = f.num_despac
                                                       WHERE a.fec_salida IS NOT NULL 
                                                             AND a.fec_salida <= NOW() 
                                                             AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                                                             AND a.ind_planru = 'S' 
                                                             AND a.ind_anulad in ('R')
                                                             AND b.ind_activo = 'S' 
                                                             AND a.gps_operad IS NOT NULL
                                                   GROUP BY a.num_despac
                                                )UNION(
                                                      SELECT a.num_despac, d.cod_noveda, (d.fec_contro) AS fec_noveda, d.kms_vehicu, d.usr_creaci
                                                        FROM ".BASE_DATOS.".tab_despac_despac a
                                                  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                                                  INNER JOIN ".BASE_DATOS.".tab_despac_contro d ON a.num_despac = d.num_despac
                                                             AND d.kms_vehicu IS NOT NULL
                                                  INNER JOIN (
                                                                SELECT (MAX(fec_contro)) AS fec_noveda, num_despac
                                                                    FROM ".BASE_DATOS.".tab_despac_contro 
                                                                    WHERE kms_vehicu IS NOT NULL
                                                                    GROUP BY num_despac
                                                                ) e ON a.num_despac = e.num_despac AND e.fec_noveda = d.fec_contro
                                                  INNER JOIN (
                                                                SELECT 
                                                                    a.num_despac
                                                                FROM 
                                                                    ".BASE_DATOS.".tab_despac_seguim a 
                                                                WHERE 
                                                                    a.cod_contro NOT IN (9999) 
                                                                    AND a.ind_estado = 1
                                                                GROUP BY a.num_despac
                                                            ) f ON a.num_despac = f.num_despac
                                                       WHERE a.fec_salida IS NOT NULL 
                                                             AND a.fec_salida <= NOW() 
                                                             AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                                                             AND a.ind_planru = 'S' 
                                                             AND a.ind_anulad in ('R')
                                                             AND b.ind_activo = 'S' 
                                                             AND a.gps_operad IS NOT NULL  
                                                    GROUP BY a.num_despac
                                                ) ORDER BY 1,3 DESC
                                            ) b  
                                            GROUP BY b.num_despac
                                  ) c ON a.num_despac = c.num_despac
                    WHERE 
                        TIMESTAMPDIFF(MINUTE, c.fec_noveda, NOW()) >= 60 
                        AND c.kms_vehicu = 0
                        AND a.fec_salida IS NOT NULL 
                        AND a.fec_salida <= NOW() 
                        AND ( a.fec_llegad IS NULL  OR a.fec_llegad = '0000-00-00 00:00:00' ) 
                        AND a.ind_planru = 'S' 
                        AND a.ind_anulad in ('R') 
                        AND b.ind_activo = 'S' 
                        AND c.cod_noveda != '9183'
                        AND c.usr_creaci = 'InterfGpsIntegr'
                    ORDER BY a.num_despac ASC";
        echo "<pre>query: "; print_r( $query ); echo "</pre>";
       
        $this->db4->ExecuteCons($query);
        $despachos = $this->db4->RetMatrix("a");

        echo "<pre>despachos sin reporte de mas de una hora: "; print_r( $despachos ); echo "</pre>";
       

        echo "<br>\n";
        foreach ($despachos AS $key => $mDespac) 
        {
            //Registra la novedad
            self::regisNovVehDet($mDespac['num_despac'], $mDespac['cod_manifi'], $mDespac['num_placax'], $mDespac['hor_transc'], $despachos, 'getDespachos', $mDespac['cod_transp']);
        }
        

    }

    /***********************************************************************
    * Funcion Consulta el rango de novedadoes con velocidad en 0           *
    * Validando si aplica el registro de la novedad                        *
    * @fn getDespachosDetalle.                                             *
    * @brief  Consulta el rango de novedadoes con velocidad en 0           *
    * Validando si aplica el registro de la novedad.                       *
    * @return string mensaje de respuesta.                                 *
    ************************************************************************/

    function getDespachosDetalle()
    {
         
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/

        $query = "  SELECT 
                        b.num_despac, 
                        b.cod_noveda, 
                        b.fec_noveda, 
                        b.kms_vehicu, 
                        b.cod_manifi,
                        b.num_placax,
                        b.usr_creaci 
                    FROM 
                        (
                            (
                                SELECT 
                                    a.num_despac,  
                                    d.cod_noveda, 
                                    d.fec_noveda, 
                                    d.kms_vehicu, 
                                    a.cod_manifi,
                                    b.num_placax,
                                    d.usr_creaci 
                                FROM 
                                    ".BASE_DATOS.".tab_despac_despac a 
                                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                                    INNER JOIN ".BASE_DATOS.".tab_despac_noveda d ON a.num_despac = d.num_despac 
                                    AND d.kms_vehicu IS NOT NULL 
                                    INNER JOIN ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat 
                                    INNER JOIN (
                                    				SELECT 
														a.num_despac
													FROM 
														".BASE_DATOS.".tab_despac_seguim a 
													WHERE 
														a.cod_contro NOT IN (9999) 
														AND a.ind_estado = 1

													GROUP BY a.num_despac
                                    			) e ON a.num_despac = e.num_despac
                                WHERE 
                                    a.fec_salida IS NOT NULL 
                                    AND a.cod_tipdes IN(3,4)
                                    AND a.fec_salida <= NOW() 
                                    AND (
                                        a.fec_llegad IS NULL 
                                        OR a.fec_llegad = '0000-00-00 00:00:00'
                                    ) 
                                    AND a.ind_planru = 'S' 
                                    AND a.ind_anulad in ('R') 
                                    AND b.ind_activo = 'S' 
                                    AND b.cod_transp = '".NIT_TRANSPOR."' 
                                    AND c.num_despac IS NOT NULL 
                                    AND (
                                        a.gps_operad IS NOT NULL 
                                        OR c.gps_operad IS NOT NULL
                                    ) 
                            ) 
                            UNION 
                                (
                                    SELECT 
                                        a.num_despac, 
                                        d.cod_noveda, 
                                        (d.fec_contro) AS fec_noveda, 
                                        d.kms_vehicu, 
                                        a.cod_manifi,
                                        b.num_placax,
                                        d.usr_creaci 
                                    FROM 
                                        ".BASE_DATOS.".tab_despac_despac a 
                                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                                        INNER JOIN ".BASE_DATOS.".tab_despac_contro d ON a.num_despac = d.num_despac 
                                        AND d.kms_vehicu IS NOT NULL 
                                        INNER JOIN ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat 
                                        INNER JOIN (
                                    				SELECT 
														a.num_despac
													FROM 
														".BASE_DATOS.".tab_despac_seguim a 
													WHERE 
														a.cod_contro NOT IN (9999) 
														AND a.ind_estado = 1

													GROUP BY a.num_despac
                                    			) e ON a.num_despac = e.num_despac
                                    WHERE 
                                        a.fec_salida IS NOT NULL 
                                        AND a.cod_tipdes IN(3,4)
                                        AND a.fec_salida <= NOW() 
                                        AND (
                                            a.fec_llegad IS NULL 
                                            OR a.fec_llegad = '0000-00-00 00:00:00'
                                        ) 
                                        AND a.ind_planru = 'S' 
                                        AND a.ind_anulad in ('R') 
                                        AND b.ind_activo = 'S' 
                                        AND b.cod_transp = '".NIT_TRANSPOR."' 
                                        AND c.num_despac IS NOT NULL 
                                        AND (
                                            a.gps_operad IS NOT NULL 
                                            OR c.gps_operad IS NOT NULL
                                        ) 
                                ) 
                            ORDER BY 
                                1, 
                                3 DESC
                        ) b 

                    WHERE TIMESTAMPDIFF(MINUTE, b.fec_noveda, NOW()) <= 90 
                    AND (b.usr_creaci = 'InterfGpsIntegr' OR b.cod_noveda = 9184)";


        /*echo "<pre>"; print_r( $query ); echo "</pre>";
        die();*/
        $this->db4->ExecuteCons($query);
        $despachos = $this->db4->RetMatrix("a");
        echo "<br>\n";

        
        //declaración de array
        $mData = [];

        //Crea el nuevo arreglo con las posiciones obtenidas dejando el numero de despacho como llave
        foreach ($despachos as $key => $mDespac) {
            $mData[$mDespac['num_despac']][$key]['num_despac'] = $mDespac['num_despac'];
            $mData[$mDespac['num_despac']][$key]['fec_noveda'] = $mDespac['fec_noveda'];
            $mData[$mDespac['num_despac']][$key]['kms_vehicu'] = $mDespac['kms_vehicu'];
            $mData[$mDespac['num_despac']][$key]['cod_manifi'] = $mDespac['cod_manifi']; 
            $mData[$mDespac['num_despac']][$key]['num_placax'] = $mDespac['num_placax']; 
            $mData[$mDespac['num_despac']][$key]['cod_noveda'] = $mDespac['cod_noveda'];
        }



        //Declara variable necesaria
        $num_desant = '';

        //Elimina los casos que no aplica analizar
        foreach ($mData as $despac => $mDespac) {
            
            //Obtiene la primera llave del arreglo a validar
            $OnePosicion = reset(array_keys($mDespac));
            
            //Declara bandera
            $ban = 0;

            //Recorre la información de los despacho
            foreach ($mDespac as $ident => $data) {
                if ($ident == $OnePosicion) { //Valida si es la primera posición
                    if ($data['kms_vehicu'] > 0) { //Elimina la posición si la velocidad es mayor a 0
                        unset($mData[$despac]);
                    }else if ($data['cod_noveda'] == 9184) { //Elimina la posición si la novedad más reciente es Vehiculo detenido
                    	unset($mData[$despac]);
                    }
                }else if ( $data['kms_vehicu'] > 0 OR $data['cod_noveda'] == 9184) { //Elimina los registros que sean mayor a 0 la velocidad para no tenerlos en cuenta o la novedad de vehiculo detenido
                    unset( $mData[$despac][$ident] );
                    $ban = 1;
                }else if ($ban == 1) { //Elimina los registros despues de que el anterior sea mayor a 0 la velocidad
                    unset( $mData[$despac][$ident] );
                }
            }

            //Ordena el arreglo en forma descendente
            krsort($mData[$despac]); 
        }

        //Valida si la novedad más antigua dentro de su despacho es mayor o igual a una hora
        foreach ($mData as $despac => $mDespac) {

            //Obtiene la primera llave del arreglo a validar
            $data = reset($mDespac);

            //Captura y valida diferencia de tiempo entre las dos fechas
            $date1 = new DateTime($data['fec_noveda']);
            $date2 = new DateTime("now");

            $intervalo = $date1->diff($date2);
            $hora = $intervalo->format('%h');

            //Valida si la hora es diferente a 0 para ejecutar la inserción de la novedad
            if ($hora != 0) {
                //Registra la novedad
               	self::regisNovVehDet($data['num_despac'], $data['cod_manifi'], $data['num_placax'], $hora, $mData, 'getDespachosDetalle');
                /*echo "<pre>--------------------------";
                print_r($data);
                print_r("Tiempo diferencia = ".$hora);
                echo "</pre>";*/
            }   
        }
    }

    /***********************************************************************
    * Funcion Inserta la novedad Vehiculo deternido.                       *
    * @fn regisNovVehDet.                                                  *
    * @brief Inserta la novedad Vehiculo deternido.                        *
    * @param $num_despac: string num_despac.                               *
    * @param $cod_manifi: string cod_manifi.                               *
    * @param $hora: string hora de diferencia.                             *
    * @param $mData: array Información del registro                        *
    * @return string mensaje de respuesta.                                 *
    ************************************************************************/

    private function regisNovVehDet($num_despac, $cod_manifi, $num_placax, $hora, $mData, $tipoInforme, $cod_transp){
        $fNomUsuari =  'InterfCron';  
        $fPwdClavex =  'InterfCron_2022';
        $fCodTransp =  $cod_transp;
        $fNumManifi =  $cod_manifi;
        $fNumPlacax =  $num_placax; //$mDespacData["num_placax"];
        $fCodNoveda =  '9184';
        $fCodContro =  NULL;
        $fTimDuraci =  '0';
        $fFecNoveda =  date("Y-m-d H:i:s");
        $fDesNoveda =  'VEHICULO DETENIDO POR MAS DE UNA HORA, TIEMPO: '.$hora." H.";
        $fNomNoveda =  NULL; //'INT GPS -VEHICULO DETENIDO POR MAS DE UNA HORA'; //$mNovedad[ "nom_noveda"];
        $fNomContro =  NULL;
        $fNomSitiox =  NULL;
        $fNumViajex =  NULL;
        $fFotNoveda =  NULL;
        $fCodRemdes =  NULL;
        $fTimSegpun =  NULL;
        $fTimUltpun =  NULL;
                    
        /*echo "<pre>--------------------------";
        print_r($data["num_despac"]." -> ".$data["cod_manifi"]);
        echo "</pre>";*/

        $mRespon = setNovedadNC(
                                    $fNomUsuari, $fPwdClavex, $fCodTransp, $fNumManifi, $fNumPlacax, $fCodNoveda, $fCodContro, $fTimDuraci, 
                                    $fFecNoveda, $fDesNoveda, $fNomNoveda, $fNomContro, $fNomSitiox, $fNumViajex, $fFotNoveda, $fCodRemdes,
                                    $fTimSegpun, $fTimUltpun, NULL, 'satt_intgps'
                                );


        echo "<pre> setNovedadNC: Informe: ".$tipoInforme.": ".$num_despac." : ".$cod_manifi." : ".$num_placax." : ".$hora." : "; print_r( $mRespon ); echo "</pre>\n";

        unset($fNomUsuari);
        unset($fPwdClavex);
        unset($fCodTransp);
        unset($fNumManifi);
        unset($fNumPlacax);
        unset($fCodNoveda);
        unset($fCodContro);
        unset($fTimDuraci);
        unset($fFecNoveda);
        unset($fDesNoveda);
        unset($fNomNoveda);
        unset($fNomContro);
        unset($fNomSitiox);
        unset($fNumViajex);
        unset($fFotNoveda);
        unset($fCodRemdes);
        unset($fTimSegpun);
        unset($fTimUltpun);
        unset($mRespon);

        //$mFile = fopen("/var/www/html/ap/interf/app/corona/cron/logs/CronVehicuDetenido".date("Y_m_d").".txt", "a+");
        //fwrite( $mFile, "---------".date("Y-m-d H:i:s")."----------");
        //fwrite( $mFile, var_export($mData, true)."\n");
        //fwrite( $mFile, "----------------------");
        //fclose($mFile);
    }
}
$_CRON = new cronVehicuDetenido();

?>
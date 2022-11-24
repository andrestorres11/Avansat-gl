<?php
/*ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/
    class FilterData
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        private static $cDespac,$cTypeUser;


        function __construct($co = null, $us = null, $ca = null)
        {

            //Include Connection class
            @include( "../lib/ajax.inc" );
            @include_once('../lib/general/functions.inc');
            //Show errors
            
            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;
            //self::$cTypeUser = $this->typeUser();

            //Switch request options
            switch($_REQUEST[opcion])
            {
                case "1":
                    self::mobileList();
                break;

                case "2":
                    self::loadFields();
                break;

                case "3":
                    self::loadGraphicsData();
                break;

                case "4":
                    self::loadCargueTableData();
                break;

                case "5":
                    self::loadDescargueTableData();
                break;

                case "6":
                    self::cargueGraphics();
                break;

                case "7":
                    self::descargueGraphics();
                break;

                case "8":
                    self::totalGraphics();
                break;
                case "exportExcel":
                    self::exportExcel();
                break;
                default:

                break;
            }
        }

        function mobileList(){
            $data=array();
            @include_once('../lib/general/constantes.inc');
            @include_once('../inform/class_despac_trans3.php');
            $mClassDespac = new Despac(self::$conexion, self::$usuario, self::$cod_aplica );   //$co = null, $us = null, $ca = null
            $mIndEtapa = 'ind_segtra';
            $mTransp = $mClassDespac -> getTranspServic($mIndEtapa);
            $mCodTransp = "";
            $mColor = array('', 'bgT1', 'bgT2', 'bgT3', 'bgT4');
            $mNegTieesp = array(); #neg_tieesp
            $mPosTieesp = array(); #pos_tieesp
            $mNegTiempo = array(); #neg_tiempo
            $mPosTiempo = array(); #pos_tiempo
            $mNegFinrut = array(); #neg_finrut
            $mPosFinrut = array(); #pos_finrut
            $mNegAcargo = array(); #neg_acargo
            $mPosAcargo = array(); #pos_acargo
            
            #array datos precarga
            $con_paradi = array(); #para el dia
            $con_paraco = array(); #para el corte
            $con_anulad = array(); #anuladas
            $con_planta = array(); #llegada en planta
            $enx_planta = array(); #en planta de etapa de cargue
            $con_porter = array(); #en porteria
            $con_sinseg = array(); #sin seguimineto
            $con_tranpl = array(); #transito a planta
            $con_cnnlap = array(); #con novedad no llegada a planta
            $con_cnlapx = array(); #con novedad llegada a planta
            $con_acargo = array(); #A cargo de empresa

            for($i=0; $i<sizeof($mTransp); $i++)
		    {
                $data_r=array();
                $mNameFunction = $mTransp[$i]['ind_segcar'] == '1' && $mTransp[$i]['ind_segdes'] == '1' ? 'getDespacTransiReport2' : 'getDespacTransiReport1';
                
                $mDespac = $mClassDespac ->$mNameFunction( $mTransp[$i] );
                $mDespac = $mClassDespac -> calTimeAlarma( $mDespac, $mTransp[$i], 0,'sinF', $mColor );
                $mNegTieesp = $mDespac['neg_tieesp'] ? array_merge($mNegTieesp, $mDespac['neg_tieesp']) : $mNegTieesp;
                $mPosTieesp = $mDespac['pos_tieesp'] ? array_merge($mPosTieesp, $mDespac['pos_tieesp']) : $mPosTieesp;
                $mNegTiempo = $mDespac['neg_tiempo'] ? array_merge($mNegTiempo, $mDespac['neg_tiempo']) : $mNegTiempo;
                $mPosTiempo = $mDespac['pos_tiempo'] ? array_merge($mPosTiempo, $mDespac['pos_tiempo']) : $mPosTiempo;
                $mNegFinrut = $mDespac['neg_finrut'] ? array_merge($mNegFinrut, $mDespac['neg_finrut']) : $mNegFinrut;
                $mPosFinrut = $mDespac['pos_finrut'] ? array_merge($mPosFinrut, $mDespac['pos_finrut']) : $mPosFinrut;
                $mNegAcargo = $mDespac['neg_acargo'] ? array_merge($mNegAcargo, $mDespac['neg_acargo']) : $mNegAcargo;
                $mPosAcargo = $mDespac['pos_acargo'] ? array_merge($mPosAcargo, $mDespac['pos_acargo']) : $mPosAcargo;

                $mData = $mClassDespac -> orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo );
        
                #Pinta tablas
                for ($u=0; $u < sizeof($mData['tiempo']); $u++) { 
                    if ($mData['tiempo'][$u]['nov_especi'] == '1' && $mData['tiempo'][$u]['ind_alarma'] == 'S' ) {
                        $mData['novesp'][$u] = $mData['tiempo'][$u];
                        unset($mData['tiempo'][$u]);
                    }else{
                        continue;
                    }
                }
                
                $data=$this->dataDespa($mData['tieesp'],$mData['novesp'],$mData['tiempo'],$mData['acargo'],null);
            }
                $data_filter=array();
                $query='';
                if($_REQUEST["num_despac"] != ""){
                    $query .= "
                    AND a.num_despac = '".$_REQUEST["num_despac"]."'";
                }

                if($_REQUEST["num_placax"] != ""){
                    $query .= "
                    AND b.num_placax = '".$_REQUEST["num_placax"]."'";
                }

                if($_REQUEST["num_telmov"] != ""){
                    $query .= "
                    AND e.num_telmov = '".$_REQUEST["num_telmov"]."'";
                }

                if($_REQUEST["num_viajex"] != ""){
                    $query .= "
                    AND n.num_despac = '".$_REQUEST["num_viajex"]."'";
                }

                if($_REQUEST["num_solici"] != ""){
                    $query .= "
                    AND n.num_solici = '".$_REQUEST["num_solici"]."'";
                }

                if($_REQUEST["num_remesi"] != ""){
                    $query .= "
                    AND (f.num_docume = '".$_REQUEST["num_remesi"]."' OR f.num_docalt = '".$_REQUEST["num_remesi"]."') ";
                }

                if($_REQUEST["cod_manifi"] != ""){
                    $query .= "
                    AND a.cod_manifi = '".$_REQUEST["cod_manifi"]."'";
                }

                if($_REQUEST["cod_conduc"] != ""){
                    $query .= "
                    AND b.cod_conduc = '".$_REQUEST["cod_conduc"]."'";
                }

                if($_REQUEST["gps_operad"] != ""){
                    if($_REQUEST["gps_operad"] == "SIN"){
                        $query .= "
                            AND a.gps_operad IS NULL";
                    }else if($_REQUEST["gps_operad"] == "CON"){
                        $query .= "
                            AND a.gps_operad IS NOT NULL";
                    }else{
                        $query .= "
                            AND n.gps_operad = '".$_REQUEST["gps_operad"]."'";
                    }
                }

                if($_REQUEST["ind_itiner"] != ""){
                    if($_REQUEST["ind_itiner"] == 1){
                        $query .= "
                            AND b.cod_itiner IS NOT NULL";
                    }else{
                        $query .= "
                            AND b.cod_itiner IS NULL";
                    }
                }

                if($_REQUEST["tip_transp1"] != "" || $_REQUEST["tip_transp2"] != "" || $_REQUEST["tip_transp3"] != ""){
                    $mTipTransp = '""';
                    $mTipTransp .= $_REQUEST["tip_transp1"] != "" ? ', "'.$_REQUEST["tip_transp1"].'"' : "";
                    $mTipTransp .= $_REQUEST["tip_transp2"] != "" ? ', "'.$_REQUEST["tip_transp2"].'"' : "";
                    $mTipTransp .= $_REQUEST["tip_transp3"] != "" ? ', "'.$_REQUEST["tip_transp3"].'"' : "";

                    if($mTipTransp != ""){
                        $query .= "
                            AND n.tip_transp IN (".$mTipTransp.")";
                    }
                }

                if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                    $query .= "
                    AND n.fec_citcar BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
                }

                if($_REQUEST["cod_tipdes"] != "" && count($_REQUEST["cod_tipdes"]) > 0){
                    
                    for ($i=0; $i < count($_REQUEST["cod_tipdes"]); $i++) { 
                        
                        if($i == 0){
                            $cod_tipdes = "'" . $_REQUEST["cod_tipdes"][$i] . "'";
                        }else{
                            $cod_tipdes .= ", '" . $_REQUEST["cod_tipdes"][$i] . "'";
                        }

                    }

                    $query .= "
                    AND a.cod_tipdes IN ($cod_tipdes)";
                }
                $sqlF="SELECT a.num_despac FROM ".BASE_DATOS.".tab_despac_despac a 
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e ON b.cod_conduc = e.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_despac_corona n ON a.num_despac = n.num_dessat
                LEFT JOIN ".BASE_DATOS.".tab_despac_destin f ON a.num_despac = f.num_despac
                WHERE 1=1 ".$query." GROUP BY a.num_despac";
                $query = new Consulta($sqlF, self::$conexion);
                $results = $query -> ret_matrix('a');
                foreach ($results as $key => $value) {
                    $filter1=$this->searcharray($value[num_despac],'noDespacho',$data);
                    foreach($filter1 as $l1)
                    {
                            array_push($data_filter,$data[$l1]);
                    }   
                }

            
            $po=0;
            $despachos = self::cleanArray($data_filter);
            $json = json_encode($despachos);
            $_SESSION["dashboard"][1]["table"] = $json;
            $_SESSION["dashboard"][1]["filter"] = json_encode(self::cleanArray($_REQUEST));
            echo $json;
            
        }

        function searcharray($value, $key, $array) {
            $keys=array();
            foreach ($array as $k => $val) {
                if ($val[$key] == $value) {
                    array_push($keys,$k);
                }
            }
            if(count($keys)>0)
            return $keys;
            else
            return null;
         }

        function getDataOut($num_desp)
        {
            $sql="SELECT if(d.abr_tercer = '',
            CONCAT(d.nom_tercer, ' ', d.nom_apell1, ' ', d.nom_apell2),
            d.abr_tercer) AS poseedor, if(
                f.nom_operad IS NULL OR f.nom_operad = '',
                'SIN GPS',
                f.nom_operad
             ) AS operadorGPS,if(
                a.usr_ultnov IS NULL,
                '',
                a.usr_ultnov
            ) AS usuarioNovedad,
            a.cod_tipdes
             FROM ".BASE_DATOS.".tab_despac_despac a 
             INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
                     ON a.num_despac = b.num_despac
             INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu c
                     ON b.num_placax = c.num_placax
             INNER JOIN ".BASE_DATOS.".tab_tercer_tercer d
                     ON c.cod_propie = d.cod_tercer
            LEFT JOIN ".BASE_DATOS.".tab_despac_corona e 
                    ON a.num_despac = e.num_dessat
             LEFT JOIN ".BD_STANDA.".tab_genera_opegps f
                     ON e.gps_operad = f.cod_operad
             WHERE a.num_despac = '" . $num_desp . "'";
            $consulta = new Consulta($sql, self::$conexion);
            $result = $consulta -> ret_matrix('a');
            //Assing "fechaDeDescargue" value to total query
            return $results=array(
                0=>$result[0]["poseedor"],
                1=>$result[0]["operadorGPS"],
                2=>$result[0]["usuarioNovedad"],
                3=>$result[0]["cod_tipdes"],
            );
        }

        function getProcesoEntrega($num_desp)
        {
            //Select "procesoDeEntrega"
            $queryProcesoDeEntrega = "
            SELECT COUNT(b.num_despac) AS t,
                COUNT(IF(b.fec_cumdes IS NOT NULL, 1, NULL)) AS d,
                COUNT(IF(b.fec_cumdes IS NULL, 1, NULL)) AS p,
                COUNT(IF(b.fec_cumdes <= DATE_FORMAT(CONCAT(b.fec_citdes, ' ', b.hor_citdes), '%Y-%m-%d %H:%i:%s'), 1, NULL)) AS c,
                COUNT(IF(b.fec_cumdes > DATE_FORMAT(CONCAT(b.fec_citdes, ' ', b.hor_citdes), '%Y-%m-%d %H:%i:%s'), 1, NULL)) AS n
            FROM ".BASE_DATOS.".tab_despac_despac a
            INNER JOIN ".BASE_DATOS.".tab_despac_destin b
                ON a.num_despac = b.num_despac
            WHERE a.num_despac = '" . $num_desp . "'
            ";

            //Execute query
            $queryProcesoDeEntrega = new Consulta($queryProcesoDeEntrega, self::$conexion);
            $procesoDeEntrega = $queryProcesoDeEntrega -> ret_matrix('a');
            //Assing "fechaDeDescargue" value to total query
            return $results=array('t'=>$procesoDeEntrega[0]["t"],'d'=>$procesoDeEntrega[0]["d"],'p'=>$procesoDeEntrega[0]["p"],'c'=>$procesoDeEntrega[0]["c"],'n'=>$procesoDeEntrega[0]["n"]);
        }

        function getFechaDeDescargue($num_desp)
        {
            //Select "fechaDeDescargue"
            $queryFechaDeDescargue = "
            SELECT @fechaDescargue := DATE_FORMAT(CONCAT(b.fec_citdes, ' ', b.hor_citdes), '%Y-%m-%d %H:%i:%s') AS fechaDeDescargue,
                    IF(
                        b.fec_cumdes IS NOT NULL,
                            IF(
                            b.fec_cumdes <= @fechaDescargue,
                            'success',
                            'danger'
                            ),
                        'white'
                    ) AS colorDescargue,
                IF(
                        b.fec_cumdes IS NOT NULL,
                        IF(
                            b.fec_cumdes <= @fechaDescargue,
                            'Cumpli&oacute;',
                            'No Cumpli&oacute;'
                        ),
                        'Pendiente'
                    ) AS estadoDeDescargue
            FROM ".BASE_DATOS.".tab_despac_despac a
            INNER JOIN ".BASE_DATOS.".tab_despac_destin b
                ON a.num_despac = b.num_despac
            WHERE a.num_despac = '" . $num_desp . "'
            ORDER BY `fechaDeDescargue` ASC
            LIMIT 1
            ";

            $queryFechaDeDescargue = "
            SELECT     
            CASE
                WHEN a.cod_noveda = '9264' THEN @colorFechaDescargue := IF( TIMESTAMPDIFF(MINUTE, a.fec_noveda, IF( b.fec_citdes IS NULL, c.fec_citdes, b.fec_citdes  ) ) > 0, 'Cumpli&oacute', 'No cumpli&oacute' )
                WHEN a.cod_noveda IN ('9265', '9263', '9269') THEN
                @colorFechaDescargue := IF(
                    TIMESTAMPDIFF(MINUTE, a.fec_noveda, IF( b.fec_citdes IS NULL, c.fec_citdes, b.fec_citdes  )  ) > 5,
                    IF(
                        TIMESTAMPDIFF(MINUTE, a.fec_noveda, IF( b.fec_citdes IS NULL, c.fec_citdes, b.fec_citdes  )  ) <= 40,
                        'A tiempo',
                        'Adelantado'
                    ),
                    'Atrasado'
                )
                END AS estadoDeDescargue ,
                    IF(b.fec_citdes IS NULL, c.fec_citdes, b.fec_citdes) AS fechaDeDescargue,
                    IF(
                        @colorFechaDescargue = 'Adelantado' OR @colorFechaDescargue = 'A tiempo',
                        'success',
                        IF(
                            @colorFechaDescargue = 'Atrasado',
                            'danger',
                            'white'
                        )
                    ) AS colorDescargue
            FROM  (

                SELECT  
                        a.num_despac, NULL AS cod_noveda , NULL AS cod_remdes , a.fec_alarma AS fec_citdes
                FROM
                        ".BASE_DATOS.".tab_despac_seguim a
                WHERE
                        a.num_despac = '" . $num_desp . "'
                    AND a.cod_contro = '9999'

            ) c LEFT JOIN


            (
                (
                SELECT  
                        a.num_despac, b.cod_noveda, DATE_ADD( b.fec_noveda , INTERVAL b.tiem_duraci MINUTE  ) AS fec_noveda
                    FROM
                        ".BASE_DATOS.".tab_despac_despac a
                        LEFT JOIN
                            tab_despac_noveda b ON a.num_despac = b.num_despac
                    WHERE
                        a.num_despac = '" . $num_desp . "'
                    AND b.cod_noveda IN ('9264')
                )
                UNION
                (

                    SELECT  
                            a.num_despac, b.cod_noveda, DATE_ADD( b.fec_contro , INTERVAL b.tiem_duraci MINUTE  ) AS fec_noveda 
                    FROM
                        ".BASE_DATOS.".tab_despac_despac a
                        LEFT JOIN
                            tab_despac_contro b ON a.num_despac = b.num_despac
                    WHERE
                            a.num_despac = '" . $num_desp . "'
                        AND b.cod_noveda IN ('9265', '9263', '9269')
                )
                ORDER BY fec_noveda DESC LIMIT 1
            ) a ON a.num_despac = c.num_despac  

            LEFT JOIN (
                        SELECT  
                            a.num_despac, NULL AS cod_noveda , a.cod_remdes, DATE_FORMAT( CONCAT(a.fec_citdes, ' ', a.hor_citdes) , '%Y-%m-%d %H:%i:%s' ) AS fec_citdes
                    FROM
                        ".BASE_DATOS.".tab_despac_destin a
                        
                    WHERE
                            a.num_despac = '" . $num_desp . "'
                    AND     a.fec_cumdes IS NULL
                    ORDER BY DATE_FORMAT( CONCAT(a.fec_citdes, ' ', a.hor_citdes) , '%Y-%m-%d %H:%i:%s' ) ASC LIMIT 1

                    ) b ON c.num_despac = b.num_despac
            ";

            //Execute query
            $queryFechaDeDescargue = new Consulta($queryFechaDeDescargue, self::$conexion);
            $planFechaDeDescargue = $queryFechaDeDescargue -> ret_matrix('a');
            $results=array(0=>$planFechaDeDescargue[0]["fechaDeDescargue"],1=>$planFechaDeDescargue[0]["colorDescargue"],2=>$planFechaDeDescargue[0]["estadoDeDescargue"]);
            //Assing "fechaDeDescargue" value to total query
            return $results;
        }

        function getEstadoCarge($num_desp)
        {
            $estadocargue='';
            $queryEstadoCargue = "

                    SELECT a.num_despac,
                            CASE
                                WHEN b.fec_cumcar IS NULL THEN 
                                    IF(
                                        TIMESTAMPDIFF(MINUTE, NOW(), DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' )  ) > 5,
                                        IF(
                                            TIMESTAMPDIFF(MINUTE, NOW(), DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' )  ) <= 40,
                                            'A tiempo',
                                            'Adelantado'
                                        ),
                                        'Atrasado'
                                    )
                                WHEN b.fec_cumcar IS NOT NULL THEN 
                                    IF(
                                        b.fec_cumcar <= DATE_FORMAT( CONCAT(a.fec_citcar, ' ', a.hor_citcar) , '%Y-%m-%d %H:%i:%s' ),
                                        'Cumpli&oacute', 
                                        'No cumpli&oacute'
                                    )
                                ELSE
                                    'PENDIENTE'
                            END AS estadoDeCargue
                    FROM 
                            tab_despac_despac a 
                INNER JOIN  tab_despac_sisext b ON a.num_despac = b.num_despac AND a.num_despac = '".$num_desp."'
                WHERE 
                        a.num_despac = '".$num_desp."' ";

                //Execute query
                $queryEstadoCargue = new Consulta($queryEstadoCargue, self::$conexion);
                $planEstadoCargue = $queryEstadoCargue -> ret_matrix('a');
                
                
                //Assing "estadoCargue" value to total query
                if(count($planEstadoCargue) > 0){
                    $estadocargue = $planEstadoCargue[0]["estadoDeCargue"];
                }else{
                    $estadocargue = "Pendiente";
                }
                return $estadocargue;
        }

        function getFechaCarg($num_desp)
        {
            $sql="SELECT @fechaCargue := DATE_FORMAT(CONCAT(a.fec_citcar, ' ', a.hor_citcar), '%Y-%m-%d %H:%i:%s') AS fechaDeCargue,
            IF(
                b.fec_cumcar IS NOT NULL,
                IF(
                   b.fec_cumcar <= @fechaCargue,
                   'success',
                   'danger'
                ),
                'white'
            ) AS colorCargue
            FROM ".BASE_DATOS.".tab_despac_despac a
            INNER JOIN ".BASE_DATOS.".tab_despac_sisext b ON a.num_despac = b.num_despac
            WHERE a.num_despac = '" . $num_desp . "'";
            $consulta = new Consulta($sql, self::$conexion);
            $result = $consulta -> ret_matrix('a');

            //Assing "localizacion" value to total query
            $results=array(0=>$result[0]["fechaDeCargue"],1=>$result[0]["colorCargue"]);
            return $results;
        }

        function getLocalizacion($num_desp)
        {
            //Select "localizacion"
            $queryLocalizacion = "
            SELECT a.*
            FROM (
                        SELECT a.num_despac, b.cod_contro, b.fec_creaci,
                            b.des_noveda AS localizacion
                        FROM ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN ".BASE_DATOS.".tab_despac_noveda b
                            ON a.num_despac = b.num_despac
                        WHERE a.num_despac = '" . $num_desp . "'
                    UNION
                        SELECT a.num_despac, b.cod_contro, b.fec_creaci,
                            b.obs_contro AS localizacion
                        FROM ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN ".BASE_DATOS.".tab_despac_contro b
                            ON a.num_despac = b.num_despac
                        WHERE a.num_despac = '" . $num_desp . "'
                ) a
            WHERE a.num_despac = '" . $num_desp . "'
            ORDER BY a.fec_creaci DESC
            LIMIT 1
            ";

            //Execute query
            $queryLocalizacion = new Consulta($queryLocalizacion, self::$conexion);
            $planLocalizacion = $queryLocalizacion -> ret_matrix('a');

            //Assing "localizacion" value to total query
            return $planLocalizacion[0]["localizacion"];
        }

        function getCumplimientoDePlanDeRuta($num_desp){
            //Select "cumplimientoDePlanDeRuta"
            $queryRuta = "
            SELECT a.num_despac, COUNT(b.cod_contro) AS a,  COUNT(c.cod_contro) AS b,
                   ROUND(( (COUNT(c.cod_contro) * 100) / COUNT(b.cod_contro) ), 1) AS porcentaje
              FROM ".BASE_DATOS.".tab_despac_despac a 
        INNER JOIN ".BASE_DATOS.".tab_despac_seguim b
                ON a.num_despac = b.num_despac
         LEFT JOIN (
                            SELECT b.cod_contro
                              FROM ".BASE_DATOS.".tab_despac_despac a
                        INNER JOIN ".BASE_DATOS.".tab_despac_noveda b
                                ON a.num_despac = b.num_despac
                             WHERE a.num_despac = '" . $num_desp . "'
                        UNION
                            SELECT b.cod_contro
                              FROM ".BASE_DATOS.".tab_despac_despac a
                        INNER JOIN ".BASE_DATOS.".tab_despac_contro b
                                ON a.num_despac = b.num_despac
                             WHERE a.num_despac = '" . $num_desp . "'
                    ) c
                ON b.cod_contro = c.cod_contro
             WHERE a.num_despac = '" . $num_desp . "'
                ";

            //Execute query
            $queryRuta = new Consulta($queryRuta, self::$conexion);
            $planRuta = $queryRuta -> ret_matrix('a');

            //Assing "cumplimientoDePlanDeRuta" value to total query
            return $planRuta[0]["porcentaje"];
        }

        function getEtapa($num_desp)
        {
            $queryEtapa = "
            SELECT a.num_despac,
                    a.cod_manifi,
                    aa.fec_cumcar,
                    c.fec_noveda,
                    c.cod_contro,
                    c.nom_noveda,
                    c.cod_etapax,
                    IF( c.nom_etapax IS NULL OR c.nom_etapax = '', 'PRECARGE', c.nom_etapax ) AS `Nombre Etapa`,
                    c.nom_etapax,
                    '' AS `etapa`
                FROM ".BASE_DATOS.".tab_despac_despac a  
            INNER JOIN ".BASE_DATOS.".tab_despac_sisext aa
                    ON a.num_despac = aa.num_despac
            
            LEFT JOIN (   
                    ( SELECT a.num_despac, b.cod_contro, c.nom_noveda, d.cod_etapax, d.nom_etapax, b.fec_creaci, b.fec_noveda
                        FROM ".BASE_DATOS.".tab_despac_despac a
                    LEFT JOIN ".BASE_DATOS.".tab_despac_noveda b
                            ON a.num_despac = b.num_despac
                    LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c
                            ON b.cod_noveda = c.cod_noveda
                    LEFT JOIN ".BASE_DATOS.".tab_genera_etapax d
                            ON c.cod_etapax = d.cod_etapax
                        WHERE a.num_despac = '" . $num_desp . "'
                    ) UNION (
                        SELECT a.num_despac, b.cod_contro, c.nom_noveda, d.cod_etapax, d.nom_etapax, b.fec_creaci, b.fec_contro  AS fec_noveda
                        FROM ".BASE_DATOS.".tab_despac_despac a
                    LEFT JOIN ".BASE_DATOS.".tab_despac_contro b 
                            ON a.num_despac = b.num_despac
                    LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c
                            ON b.cod_noveda = c.cod_noveda
                    LEFT JOIN ".BASE_DATOS.".tab_genera_etapax d
                            ON c.cod_etapax = d.cod_etapax
                        WHERE a.num_despac = '" . $num_desp . "'
                        )  ORDER BY fec_creaci
                ) c ON a.num_despac = c.num_despac
                    
                WHERE a.num_despac = '" . $num_desp . "'
            ORDER BY c.fec_noveda DESC LIMIT 1
            ";
                
            
            //Execute query
            $queryEtapa = new Consulta($queryEtapa, self::$conexion);
            $etapa = $queryEtapa -> ret_matrix('a');
            
            //Assing "Reporte" value to total query
            return $etapa[0]["Nombre Etapa"];
            //$despachos[$key]["etapa"] = $etapa[0]["Nombre Etapa"];
        }

        function getReport($num_desp)
        {
            $report='';
            $queryReporte = "
            SELECT b.cod_contro, c.*, d.nom_etapax
                FROM ".BASE_DATOS.".tab_despac_despac a
            LEFT JOIN ".BASE_DATOS.".tab_despac_contro b
                ON a.num_despac = b.num_despac
            LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c
                ON b.cod_noveda = c.cod_noveda
            LEFT JOIN ".BASE_DATOS.".tab_genera_etapax d
                ON c.cod_etapax = d.cod_etapax
                WHERE a.num_despac = '" . $num_desp . "'
                  AND b.val_longit IS NOT NULL
                  AND b.val_latitu IS NOT NULL
            ";
        
            //Execute query
            $queryReporte = new Consulta($queryReporte, self::$conexion);
            $reporte = $queryReporte -> ret_matrix('a');
            
            //Assing "Etapa" value to total query
            if(count($reporte) > 0){
                $report= "success";
            }else{
                $report = "danger";
            }
            return $report;

        }

        function getViaje($num_desp)
        {
            $viaje='';
            $sql ="SELECT num_despac AS viaje FROM ".BASE_DATOS.".tab_despac_corona
            WHERE num_dessat='".$num_desp."'";
            $consulta = new Consulta($sql, self::$conexion);
            $result = $consulta -> ret_matrix('a');
            foreach($result as $value){
                $viaje=$value[viaje];
            }
            return $viaje;
        }

        function dataDespa($data1,$data2,$data3,$data4,$data5)
        {
            $dat=array();
            foreach ($data1 as $row1)
            {    
                if(!in_array($row1[num_despac],$dat['noDespacho']))
                $arrFecCarg=$this->getFechaCarg($row1[num_despac]);
                $arrFecDesc=$this->getFechaDeDescargue($row1[num_despac]);
                $dataOut=$this->getDataOut($row1[num_despac]);
                array_push($dat,
                array(
                    'noDespacho'=>$row1[num_despac],
                    'placa'=>$row1[num_placax],
                    'viaje'=>$this->getViaje($row1[num_despac]),
                    'itinerario'=>$row1[itinerario],
                    'reporte'=>$this->getReport($row1[num_despac]),
                    'etapa'=>$this->getEtapa($row1[num_despac]),
                    'origen'=>$row1[ciu_origen],
                    'destino'=>$row1[ciu_destin],
                    'cumplimientoDePlanDeRuta'=>$this->getCumplimientoDePlanDeRuta($row1[num_despac]),
                    'localizacion'=>$this->getLocalizacion($row1[num_despac]),
                    'fechaDeCargue'=>$arrFecCarg[0],
                    'colorCargue'=>$arrFecCarg[1],
                    'estadoDeCargue'=>$this->getEstadoCarge($row1[num_despac]),
                    'fechaDeDescargue'=>$arrFecDesc[0],
                    'procesoDeEntrega'=>$this->getProcesoEntrega($row1[num_despac]),
                    'despacho'=>$row1[num_despac],
                    'conductor'=>$row1[nom_conduc],
                    'celularConductor'=>$row1[num_telmov],
                    'poseedor'=>$dataOut[0],
                    'operadorGPS'=>$dataOut[1],
                    'usuarioNovedad'=>$dataOut[2],
                    'fechaNovedad'=>$row1[fec_ultnov],
                    'tiempoAlarma'=>$row1[tiempoGl],
                    'colorAlarma'=>$row1[color],
                    'num_placax'=>$row1[num_placax],
                    'num_despac'=>$row1[num_despac],
                    'cod_tipdes'=>$dataOut[3],
                    'nom_noveda'=>($row1[nom_ultnov] ? $row1[nom_ultnov]:'Sin novedad'),
                    'transportadora'=>$row1[nom_transp],

                ));
            }
            foreach ($data2 as $row2)
            {
                if(!in_array($row2[num_despac],$dat['noDespacho']))
                $arrFecCarg=$this->getFechaCarg($row2[num_despac]);
                $arrFecDesc=$this->getFechaDeDescargue($row2[num_despac]);
                $dataOut=$this->getDataOut($row2[num_despac]);
                array_push($dat,
                array(
                    'noDespacho'=>$row2[num_despac],
                    'placa'=>$row2[num_placax],
                    'viaje'=>$this->getViaje($row2[num_despac]),
                    'itinerario'=>$row2[itinerario],
                    'reporte'=>$this->getReport($row2[num_despac]),
                    'etapa'=>$this->getEtapa($row2[num_despac]),
                    'origen'=>$row2[ciu_origen],
                    'destino'=>$row2[ciu_destin],
                    'cumplimientoDePlanDeRuta'=>$this->getCumplimientoDePlanDeRuta($row2[num_despac]),
                    'localizacion'=>$this->getLocalizacion($row2[num_despac]),
                    'fechaDeCargue'=>$arrFecCarg[0],
                    'colorCargue'=>$arrFecCarg[1],
                    'estadoDeCargue'=>$this->getEstadoCarge($row2[num_despac]),
                    'fechaDeDescargue'=>$arrFecDesc[0],
                    'procesoDeEntrega'=>$this->getProcesoEntrega($row2[num_despac]),
                    'despacho'=>$row2[num_despac],
                    'conductor'=>$row2[nom_conduc],
                    'celularConductor'=>$row2[num_telmov],
                    'poseedor'=>$dataOut[0],
                    'operadorGPS'=>$dataOut[1],
                    'usuarioNovedad'=>$dataOut[2],
                    'fechaNovedad'=>$row2[fec_ultnov],
                    'tiempoAlarma'=>$row2[tiempoGl],
                    'colorAlarma'=>$row2[color],
                    'num_placax'=>$row2[num_placax],
                    'num_despac'=>$row2[num_despac],
                    'cod_tipdes'=>$dataOut[3],
                    'nom_noveda'=>($row2[nom_ultnov] ? $row2[nom_ultnov]:'Sin novedad'),
                    'transportadora'=>$row2[nom_transp],
                ));
            }
            foreach ($data3 as $row3)
            {
                if(!in_array($row3[num_despac],$dat['noDespacho']))
                $arrFecCarg=$this->getFechaCarg($row3[num_despac]);
                $arrFecDesc=$this->getFechaDeDescargue($row3[num_despac]);
                $dataOut=$this->getDataOut($row3[num_despac]);
                array_push($dat,
                array(
                    'noDespacho'=>$row3[num_despac],
                    'placa'=>$row3[num_placax],
                    'viaje'=>$this->getViaje($row3[num_despac]),
                    'itinerario'=>$row3[itinerario],
                    'reporte'=>$this->getReport($row3[num_despac]),
                    'etapa'=>$this->getEtapa($row3[num_despac]),
                    'origen'=>$row3[ciu_origen],
                    'destino'=>$row3[ciu_destin],
                    'cumplimientoDePlanDeRuta'=>$this->getCumplimientoDePlanDeRuta($row3[num_despac]),
                    'localizacion'=>$this->getLocalizacion($row3[num_despac]),
                    'fechaDeCargue'=>$arrFecCarg[0],
                    'colorCargue'=>$arrFecCarg[1],
                    'estadoDeCargue'=>$this->getEstadoCarge($row3[num_despac]),
                    'fechaDeDescargue'=>$arrFecDesc[0],
                    'procesoDeEntrega'=>$this->getProcesoEntrega($row3[num_despac]),
                    'despacho'=>$row3[num_despac],
                    'conductor'=>$row3[nom_conduc],
                    'celularConductor'=>$row3[num_telmov],
                    'poseedor'=>$dataOut[0],
                    'operadorGPS'=>$dataOut[1],
                    'usuarioNovedad'=>$dataOut[2],
                    'fechaNovedad'=>$row3[fec_ultnov],
                    'tiempoAlarma'=>$row3[tiempoGl],
                    'colorAlarma'=>$row3[color],
                    'num_placax'=>$row3[num_placax],
                    'num_despac'=>$row3[num_despac],
                    'cod_tipdes'=>$dataOut[3],
                    'nom_noveda'=>($row3[nom_ultnov] ? $row3[nom_ultnov]:'Sin novedad'),
                    'transportadora'=>$row3[nom_transp],
                ));
            }
            foreach ($data4 as $row4)
            {
                if(!in_array($row4[num_despac],$dat['noDespacho']))
                $arrFecCarg=$this->getFechaCarg($row4[num_despac]);
                $arrFecDesc=$this->getFechaDeDescargue($row4[num_despac]);
                $dataOut=$this->getDataOut($row4[num_despac]);
                array_push($dat,
                array(
                    'noDespacho'=>$row4[num_despac],
                    'placa'=>$row4[num_placax],
                    'viaje'=>$this->getViaje($row4[num_despac]),
                    'itinerario'=>$row4[itinerario],
                    'reporte'=>$this->getReport($row4[num_despac]),
                    'etapa'=>$this->getEtapa($row4[num_despac]),
                    'origen'=>$row4[ciu_origen],
                    'destino'=>$row4[ciu_destin],
                    'cumplimientoDePlanDeRuta'=>$this->getCumplimientoDePlanDeRuta($row4[num_despac]),
                    'localizacion'=>$this->getLocalizacion($row4[num_despac]),
                    'fechaDeCargue'=>$arrFecCarg[0],
                    'colorCargue'=>$arrFecCarg[1],
                    'estadoDeCargue'=>$this->getEstadoCarge($row4[num_despac]),
                    'fechaDeDescargue'=>$arrFecDesc[0],
                    'procesoDeEntrega'=>$this->getProcesoEntrega($row4[num_despac]),
                    'despacho'=>$row4[num_despac],
                    'conductor'=>$row4[nom_conduc],
                    'celularConductor'=>$row4[num_telmov],
                    'poseedor'=>$dataOut[0],
                    'operadorGPS'=>$dataOut[1],
                    'usuarioNovedad'=>$dataOut[2],
                    'fechaNovedad'=>$row4[fec_ultnov],
                    'tiempoAlarma'=>$row4[tiempoGl],
                    'colorAlarma'=>$row4[color],
                    'num_placax'=>$row4[num_placax],
                    'num_despac'=>$row4[num_despac],
                    'cod_tipdes'=>$dataOut[3],
                    'nom_noveda'=>($row4[nom_ultnov] ? $row4[nom_ultnov]:'Sin novedad'),
                    'transportadora'=>$row4[nom_transp],
                ));
            }
            foreach ($data5 as $row5)
            {
                if(!in_array($row5[num_despac],$dat['noDespacho']))
                $arrFecCarg=$this->getFechaCarg($row5[num_despac]);
                $arrFecDesc=$this->getFechaDeDescargue($row5[num_despac]);
                $dataOut=$this->getDataOut($row5[num_despac]);
                array_push($dat,
                array(
                    'noDespacho'=>$row5[num_despac],
                    'placa'=>$row5[num_placax],
                    'viaje'=>$this->getViaje($row5[num_despac]),
                    'itinerario'=>$row5[itinerario],
                    'reporte'=>$this->getReport($row5[num_despac]),
                    'etapa'=>$this->getEtapa($row5[num_despac]),
                    'origen'=>$row5[ciu_origen],
                    'destino'=>$row5[ciu_destin],
                    'cumplimientoDePlanDeRuta'=>$this->getCumplimientoDePlanDeRuta($row5[num_despac]),
                    'localizacion'=>$this->getLocalizacion($row5[num_despac]),
                    'fechaDeCargue'=>$arrFecCarg[0],
                    'colorCargue'=>$arrFecCarg[1],
                    'estadoDeCargue'=>$this->getEstadoCarge($row5[num_despac]),
                    'fechaDeDescargue'=>$arrFecDesc[0],
                    'procesoDeEntrega'=>$this->getProcesoEntrega($row5[num_despac]),
                    'despacho'=>$row5[num_despac],
                    'conductor'=>$row5[nom_conduc],
                    'celularConductor'=>$row5[num_telmov],
                    'poseedor'=>$dataOut[0],
                    'operadorGPS'=>$dataOut[1],
                    'usuarioNovedad'=>$dataOut[2],
                    'fechaNovedad'=>$row5[fec_ultnov],
                    'tiempoAlarma'=>$row5[tiempoGl],
                    'colorAlarma'=>$row5[color],
                    'num_placax'=>$row5[num_placax],
                    'num_despac'=>$row5[num_despac],
                    'cod_tipdes'=>$dataOut[3],
                    'nom_noveda'=>($row5[nom_ultnov] ? $row5[nom_ultnov]:'Sin novedad'),
                    'transportadora'=>$row5[nom_transp],
                ));
            }
            return $dat;
        }

        function loadFields(){

            //Create necessary variables
            $json = array();

            //Create consult cod_tipdes
            $query = " 
                SELECT cod_tipdes AS id, nom_tipdes AS value
                  FROM ".BASE_DATOS.".tab_genera_tipdes
                 WHERE cod_tipdes NOT IN(7,8)
            ";

            //Generate consult
            $query = new Consulta($query, self::$conexion);
            $mData = $query -> ret_matrix('a');

            //Create format json
            $json["cod_tipdes"] = array(
                "name" => "Tipo de despacho",
                "type" => "checkbox",
                "container" => "#tipoDeDespacho",
                "rowContainer" => "div",
                "elementContainer" => "label",
                "rowMaxQuantity" => 4,
                "data" => $mData
            );

            //Clean array
            $mData = self::cleanArray($mData);

            //Create consult gps_operad
            $query = " 
                SELECT cod_operad AS id, nom_operad AS value
                  FROM ".BD_STANDA.".tab_genera_opegps
                 WHERE ind_estado = 1
              ORDER BY value
            ";

            $paramGPS = getParameOpeGPS(self::$conexion);
            $parOpeSt = $paramGPS[0];
            $parOpePr = $paramGPS[1];
            $opegpsPropio = NULL;
            $opegpsStanda = NULL;

            if($parOpeSt){
            $query = "SELECT cod_operad as 'id', CONCAT(nom_operad, ' [INTEGRADOR ESTANDAR]') as 'value' 
                    FROM ".BD_STANDA.".tab_genera_opegps
                    WHERE ind_estado = '1'
                ORDER BY nom_operad ASC ";
            $consulta = new Consulta($query, self::$conexion);
            $opegpsStanda = $consulta->ret_matriz("a");
            
            }

            if($parOpePr){
            $query = "SELECT cod_operad as 'id' ,nom_operad as 'value'
                    FROM ".BASE_DATOS.".tab_genera_opegps
                    WHERE ind_estado = '1'
                ORDER BY nom_operad ASC ";
            $consulta = new Consulta($query, self::$conexion);
            $opegpsPropio = $consulta->ret_matriz("a");
            }

            $mData = SortMatrix(arrayMergeIgnoringNull($opegpsStanda,$opegpsPropio), 'value', 'ASC') ;

            $mData = array_merge( [0=>['id' => 'SIN' , 'value' => 'SIN GPS'], 1=> ['id' => 'CON', 'value' => 'CON GPS'] ]  , $mData );

            //Clean array
            $mData = self::cleanArray($mData);

            //Create format json
            $json["gps_operad"] = array(
                "name" => "Operador GPS",
                "type" => "select",
                "container" => "#filtrosEspecificos tr:last-child",
                "rowContainer" => "td",
                "elementContainer" => "td",
                "rowMaxQuantity" => 6,
                "data" => $mData
            );


            $query = " 
                SELECT cod_noveda AS id, nom_noveda AS value
                  FROM ".BASE_DATOS.".tab_genera_noveda
                 WHERE ind_estado = 1
              ORDER BY value
            ";

            //Generate consult
            $query = new Consulta($query, self::$conexion);
            $mData = $query -> ret_matrix('a');

            //Clean array
            $mData = self::cleanArray($mData);

            //Create format json
            $json["cod_noveda"] = array(
                "name" => "Novedades",
                "type" => "select",
                "container" => "#filtrosEspecificos",
                "rowContainer" => "tr",
                "elementContainer" => "td",
                "rowMaxQuantity" => 6,
                "data" => $mData
            );

            $json = json_encode($json);

            echo $json;
        }

        function loadGraphicsData(){

            //Select "Ruta"
            $queryRuta = "
            SELECT a.num_despac, b.cod_contro, d.nom_contro, b.fec_planea, b.fec_alarma, b.ind_estado,
                   CASE 
                        WHEN (b.ind_estado = 1) THEN
                            IF(TIMESTAMPDIFF(MINUTE, b.fec_planea, NOW()) < 10 AND TIMESTAMPDIFF(MINUTE, b.fec_planea, NOW()) > -10, 'A tiempo', 
                                IF(TIMESTAMPDIFF(MINUTE, b.fec_planea, NOW()) > 10, 'Atrasado', 'Adelantado')
                            )
                   ELSE
                        IF(TIMESTAMPDIFF(MINUTE, b.fec_planea, b.fec_alarma) < 10 AND TIMESTAMPDIFF(MINUTE, b.fec_planea, b.fec_alarma) > -10, 'A tiempo', 
                            IF(TIMESTAMPDIFF(MINUTE, b.fec_planea, b.fec_alarma) > 10, 'Atrasado', 'Adelantado')
                        )
                   END
                AS status
              FROM ".BASE_DATOS.".tab_despac_despac a 
        INNER JOIN ".BASE_DATOS.".tab_despac_seguim b
                ON a.num_despac = b.num_despac
         LEFT JOIN (
                            SELECT b.cod_contro
                              FROM ".BASE_DATOS.".tab_despac_despac a
                        INNER JOIN ".BASE_DATOS.".tab_despac_noveda b
                                ON a.num_despac = b.num_despac
                             WHERE a.num_despac = '" . $_REQUEST["num_despac"] . "'
                        UNION
                            SELECT b.cod_contro
                              FROM ".BASE_DATOS.".tab_despac_despac a
                        INNER JOIN ".BASE_DATOS.".tab_despac_contro b
                                ON a.num_despac = b.num_despac
                             WHERE a.num_despac = '" . $_REQUEST["num_despac"] . "'
                    ) c
                ON b.cod_contro = c.cod_contro
        INNER JOIN ".BASE_DATOS.".tab_genera_contro d
        ON b.cod_contro = d.cod_contro
             WHERE a.num_despac = '" . $_REQUEST["num_despac"] . "'
             ORDER BY b.fec_planea ASC
                ";

            //Execute query
            $queryRuta = new Consulta($queryRuta, self::$conexion);
            $ruta = $queryRuta -> ret_matrix('a');

            //Create array skeleton
            $configGraphic = array(
                "Atrasado" => array(
                    "symbolSize" => "25",
                    "type" => "scatter",
                    "color" => "#cb5237",
                    "symbol" => "image://data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjMwIiBoZWlnaHQ9IjYzMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4gPGc+ICA8dGl0bGU+YmFja2dyb3VuZDwvdGl0bGU+ICA8cmVjdCBmaWxsPSJub25lIiBpZD0iY2FudmFzX2JhY2tncm91bmQiIGhlaWdodD0iNDAyIiB3aWR0aD0iNTgyIiB5PSItMSIgeD0iLTEiLz4gPC9nPiA8Zz4gIDx0aXRsZT5MYXllciAxPC90aXRsZT4gIDxjaXJjbGUgaWQ9InN2Z18xIiByPSIzMDAiIGZpbGw9IiNiZjAwMDAiIGN5PSIzMTUiIGN4PSIzMTUiLz4gIDxkZXNjLz4gIDxwYXRoIGlkPSJzdmdfMyIgZmlsbD0iI2JmMDAwMCIgZD0ibTE2Ni45MDY1OCwyMjEuODc5OTdsMTcuNzgzOSwtNjkuMTM3ODdsMzkuMzMyOTMsLTM5LjcxMTc4bDE5Ni4wNDY4OSwwLjc0OTI3bDMxLjE4OTM2LDM4LjU4NzAybDIyLjE3NzY0LDc0LjgwNDE5bDMuMjAzNTYsMTk1LjM1MzEzbC0zMDkuNTI3ODUsLTEwLjU5Njk0bC0wLjIwNjQzLC0xOTAuMDQ3MDJ6Ii8+ICA8cGF0aCBpZD0ic3ZnXzQiIGZpbGw9IiNmZmYiIGQ9Im0xODcuMDEwNTksNTE5LjI2Mzk2Yy00LjU0NDg4LC0xLjk2NTU1IC05LjYyNzY3LC02Ljg4MjIxIC0xMi4zMTA0MiwtMTEuOTA4MDZjLTEuOTAxODgsLTMuNTYyOTcgLTIuMTc2NCwtNS45NjY2MiAtMi41MTI2MiwtMjJjLTAuMjUzNTcsLTEyLjA5MTgzIC0wLjc1NDg1LC0xOC4yNDExNSAtMS41MjcyMSwtMTguNzM0NjljLTAuNjMyMzYsLTAuNDA0MDggLTcuMzUwNSwtMC43NDE1OCAtMTQuOTI5MTgsLTAuNzVsLTEzLjc3OTQ0LC0wLjAxNTMxbDAuMjk3MzgsLTkzLjc1bDAuMjk3MzcsLTkzLjc1bDguNjkxNTEsLTY1YzQuNzgwMzMsLTM1Ljc1MDAxIDkuMzc4NjgsLTY3LjI4MzM0IDEwLjIxODU1LC03MC4wNzQwN2MyLjI1MDU1LC03LjQ3ODEzIDguNjM4NzIsLTE2LjY2OTExIDE1LjI0MDYyLC0yMS45Mjc0M2M2Ljc1OTQ2LC01LjM4MzgyIDI0LjMxNDkzLC0xNC4xNDA1IDM1LjgxMzQ0LC0xNy44NjM3OGMxMC42MTIzMSwtMy40MzYzMiAyOS44NjYzMSwtOC4wMDg5MSA0Ni41MDAwMSwtMTEuMDQzMTljNDUuMTE3MzEsLTguMjMwMiA4MS45NjYwNywtNy44MDg1NSAxMjkuNjIxNzgsMS40ODMyMmM1NC40OTQzLDEwLjYyNTE0IDgxLjY5Mzk2LDI1LjMzNDAzIDg5LjM0MDk5LDQ4LjMxMzM2YzEuMzgwNSw0LjE0ODQyIDQuODc3ODcsMjcuODgyIDEwLjU1MTEsNzEuNjAxMTZsOC40ODYxMyw2NS4zOTU4OGwwLDkzLjMwNzQybDAsOTMuMzA3NDNsLTEzLjI1LDAuMDE1MzFjLTcuMjg3NSwwLjAwODQyIC0xMy43NjY2NywwLjM0NTkyIC0xNC4zOTgxNSwwLjc1Yy0wLjc2NTM1LDAuNDg5NzQgLTEuMjg0OTUsNi41NjkyNSAtMS41NTg0OSwxOC4yMzQ2OWMtMC40NzU1MiwyMC4yNzk4NiAtMS40NTE3NCwyMy43Njg4NSAtOC4zNDkzLDI5Ljg0MDMzYy0xMy4yNjg5OCwxMS42Nzk4MiAtMzYuNTI1MjcsNi4xODI3OCAtNDEuMzAyNjMsLTkuNzYyNjFjLTAuNjQ5NzQsLTIuMTY4NjYgLTEuMTQxNDMsLTEwLjcyODkxIC0xLjE0MTQzLC0xOS44NzIzMWMwLC0xMy4yNjg5OSAtMC4yNzMzLC0xNi4zMzU4NSAtMS41NzE0MywtMTcuNjMzOThjLTEuNDA2MjUsLTEuNDA2MjUgLTExLjQzNzIzLC0xLjU3MTQzIC05NS40Mjg1NywtMS41NzE0M2MtODMuOTkxMzUsMCAtOTQuMDIyMzMsMC4xNjUxOCAtOTUuNDI4NTgsMS41NzE0M2MtMS4zMDIwOSwxLjMwMjA5IC0xLjU3MzEsNC40MzAwNyAtMS41ODEyLDE4LjI1Yy0wLjAwOTAzLDE1LjQzMDgzIC0wLjE4NjE2LDE3LjA1MjYyIC0yLjM2NzY1LDIxLjY3ODU3Yy0yLjgwOTgsNS45NTgzMSAtNS41NzQ4Myw4LjY3NDE4IC0xMS42MjI1OCwxMS40MTU5N2MtNS41MTA2OCwyLjQ5ODMgLTE2Ljc3ODMxLDIuNzUwMzMgLTIyLDAuNDkyMDl6bTE5LjA5MDIxLC0xMTEuNzc5MThjOS40NjU1NiwtMy4zODM1IDE2LjA1MjY1LC0xMi43Nzg3OSAxNi4wNTI2NSwtMjIuODk2MTljMCwtMTQuMDcwMzggLTEwLjUwNjUyLC0yNC43MzI2OSAtMjQuMzcxMzIsLTI0LjczMjY5Yy02Ljg5MDk4LDAgLTExLjA1NjA1LDEuNTU0ODggLTE2LjE1MzE4LDYuMDMwMjJjLTUuODIxMTgsNS4xMTEwNyAtOC4wNTg5NiwxMC4yMzk2OCAtOC4wNTg5NiwxOC40Njk3OGMwLDcuNzczOTkgMi4wNjczNiwxMi45MjU5NSA3LjA5OTAyLDE3LjY5MTA1YzYuNDQ5MTEsNi4xMDc0NyAxNy4xNTQ2NCw4LjM5NjUzIDI1LjQzMTc5LDUuNDM3ODN6bTI0Ni45NzM2MiwtMS4zNzg4OGMxOC4wNzQ2NCwtOS4xNjY1OCAxOC4wNzQ2NCwtMzQuMzMzNDIgMCwtNDMuNWMtMTIuODA2ODMsLTYuNDk1IC0yOC4zMjI3MSwtMC44Nzc4IC0zMy44MzcwOCwxMi4yNWMtMi4yNDIyNCw1LjMzNzk4IC0xLjg5OTA3LDE0Ljk3OTcgMC43MjYxNywyMC40MDI3MmM1Ljg5Mjc0LDEyLjE3Mjc3IDIwLjg0MTExLDE3LjA2OTkyIDMzLjExMDkxLDEwLjg0NzI4em0xLjk5NzY1LC0xMDkuMjYxNjZjMi45MzM5NCwtMC41NTY0MSA2LjM0NDczLC0xLjk2MDc3IDcuNTc5NTMsLTMuMTIwOGMyLjQ3OTI0LC0yLjMyOTEzIDQuMzk0MjgsLTkuNzA1NiAzLjczMDAxLC0xNC4zNjc1NGMtMC4yMzUxLC0xLjY1IC0zLjUzNDI3LC0yNS4zNDU2NyAtNy4zMzE0OCwtNTIuNjU3MDRjLTcuMTA5NjIsLTUxLjEzNTcyIC03Ljg3OTYyLC01NC42Nzc3NSAtMTIuODY2OTIsLTU5LjE4ODA1Yy01LjE0NjYyLC00LjY1NDM3IC0yLjcwMDYxLC00LjU2NDQ5IC0xMjUuNDc5NzksLTQuNjExMDJjLTgzLjA3MzM5LC0wLjAzMTQ5IC0xMTcuMDc4NzgsMC4yNzYyOSAtMTIwLjMwNTg3LDEuMDg4ODljLTUuODg5MTEsMS40ODI4OSAtMTAuMDAzNzQsNS43MTUyOSAtMTEuODQ3MTgsMTIuMTg2MjhjLTIuNDc1NDgsOC42ODk1OSAtMTUuNjkxNTYsMTA2LjM1NDk1IC0xNC45MzY1NiwxMTAuMzc5NDdjMS4wMDI2Myw1LjM0NDQ3IDUuMjAwMTQsOS40ODI3OCAxMC40ODU1NCwxMC4zMzc2NWM3LjE2ODg1LDEuMTU5NTEgMjY0Ljg0NjI4LDEuMTE0MDIgMjcwLjk3MjcyLC0wLjA0Nzg0em0tNTkuNTYxNDcsLTE1NS45NjI4NWM0LjYxNTM3LC0xLjA2OTM3IDcuMTczMTQsLTMuNzY2MjggOC4wNjExMywtOC40OTk2M2MwLjc5MjcyLC00LjIyNTU3IC0xLjc0Njc4LC05LjgzMzc5IC01LjE1NjM3LC0xMS4zODczMWMtMS44MDA2NiwtMC44MjA0MyAtMjMuOTMwNDksLTEuMTMyMzQgLTc5LjIwMTgxLC0xLjExNjMyYy03MS4xNjQ5OCwwLjAyMDYzIC03Ni44NTY5NSwwLjE0Njk4IC03OC44MzU5MSwxLjc1Yy00LjcwODMyLDMuODEzODkgLTUuNjM5NTIsOS42Mjg1NSAtMi4zOTQzMiwxNC45NTA5M2MxLjE3MDQxLDEuOTE5NTggMy4wOSwzLjQ3MTYxIDQuNzQ2MjYsMy44Mzc0N2MxLjUyOTU2LDAuMzM3ODcgMy4yMzEwMiwwLjc2NDQyIDMuNzgxMDIsMC45NDc4OWMyLjQ0ODQ5LDAuODE2NzcgMTQ1LjM5MDA4LDAuMzUzMzggMTQ5LC0wLjQ4MzAzeiIvPiA8L2c+PC9zdmc+"
                ),
                "A tiempo" => array(
                    "symbolSize" => "25",
                    "type" => "scatter",
                    "color" => "#cab130",
                    "symbol" => "image://data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjMwIiBoZWlnaHQ9IjYzMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4gPGc+ICA8dGl0bGU+YmFja2dyb3VuZDwvdGl0bGU+ICA8cmVjdCBmaWxsPSJub25lIiBpZD0iY2FudmFzX2JhY2tncm91bmQiIGhlaWdodD0iNDAyIiB3aWR0aD0iNTgyIiB5PSItMSIgeD0iLTEiLz4gPC9nPiA8Zz4gIDx0aXRsZT5MYXllciAxPC90aXRsZT4gIDxjaXJjbGUgaWQ9InN2Z18xIiByPSIzMDAiIGZpbGw9IiNlNWM0MDkiIGN5PSIzMTUiIGN4PSIzMTUiLz4gIDxkZXNjLz4gIDxwYXRoIGlkPSJzdmdfMyIgZmlsbD0iI2U1YzQwOSIgZD0ibTE2Ni45MDY1OCwyMjEuODc5OTdsMTcuNzgzOSwtNjkuMTM3ODdsMzkuMzMyOTMsLTM5LjcxMTc4bDE5Ni4wNDY4OSwwLjc0OTI3bDMxLjE4OTM2LDM4LjU4NzAybDIyLjE3NzY0LDc0LjgwNDE5bDMuMjAzNTYsMTk1LjM1MzEzbC0zMDkuNTI3ODUsLTEwLjU5Njk0bC0wLjIwNjQzLC0xOTAuMDQ3MDJ6Ii8+ICA8cGF0aCBpZD0ic3ZnXzQiIGZpbGw9IiNmZmYiIGQ9Im0xODcuMDEwNTksNTE5LjI2Mzk2Yy00LjU0NDg4LC0xLjk2NTU1IC05LjYyNzY3LC02Ljg4MjIxIC0xMi4zMTA0MiwtMTEuOTA4MDZjLTEuOTAxODgsLTMuNTYyOTcgLTIuMTc2NCwtNS45NjY2MiAtMi41MTI2MiwtMjJjLTAuMjUzNTcsLTEyLjA5MTgzIC0wLjc1NDg1LC0xOC4yNDExNSAtMS41MjcyMSwtMTguNzM0NjljLTAuNjMyMzYsLTAuNDA0MDggLTcuMzUwNSwtMC43NDE1OCAtMTQuOTI5MTgsLTAuNzVsLTEzLjc3OTQ0LC0wLjAxNTMxbDAuMjk3MzgsLTkzLjc1bDAuMjk3MzcsLTkzLjc1bDguNjkxNTEsLTY1YzQuNzgwMzMsLTM1Ljc1MDAxIDkuMzc4NjgsLTY3LjI4MzM0IDEwLjIxODU1LC03MC4wNzQwN2MyLjI1MDU1LC03LjQ3ODEzIDguNjM4NzIsLTE2LjY2OTExIDE1LjI0MDYyLC0yMS45Mjc0M2M2Ljc1OTQ2LC01LjM4MzgyIDI0LjMxNDkzLC0xNC4xNDA1IDM1LjgxMzQ0LC0xNy44NjM3OGMxMC42MTIzMSwtMy40MzYzMiAyOS44NjYzMSwtOC4wMDg5MSA0Ni41MDAwMSwtMTEuMDQzMTljNDUuMTE3MzEsLTguMjMwMiA4MS45NjYwNywtNy44MDg1NSAxMjkuNjIxNzgsMS40ODMyMmM1NC40OTQzLDEwLjYyNTE0IDgxLjY5Mzk2LDI1LjMzNDAzIDg5LjM0MDk5LDQ4LjMxMzM2YzEuMzgwNSw0LjE0ODQyIDQuODc3ODcsMjcuODgyIDEwLjU1MTEsNzEuNjAxMTZsOC40ODYxMyw2NS4zOTU4OGwwLDkzLjMwNzQybDAsOTMuMzA3NDNsLTEzLjI1LDAuMDE1MzFjLTcuMjg3NSwwLjAwODQyIC0xMy43NjY2NywwLjM0NTkyIC0xNC4zOTgxNSwwLjc1Yy0wLjc2NTM1LDAuNDg5NzQgLTEuMjg0OTUsNi41NjkyNSAtMS41NTg0OSwxOC4yMzQ2OWMtMC40NzU1MiwyMC4yNzk4NiAtMS40NTE3NCwyMy43Njg4NSAtOC4zNDkzLDI5Ljg0MDMzYy0xMy4yNjg5OCwxMS42Nzk4MiAtMzYuNTI1MjcsNi4xODI3OCAtNDEuMzAyNjMsLTkuNzYyNjFjLTAuNjQ5NzQsLTIuMTY4NjYgLTEuMTQxNDMsLTEwLjcyODkxIC0xLjE0MTQzLC0xOS44NzIzMWMwLC0xMy4yNjg5OSAtMC4yNzMzLC0xNi4zMzU4NSAtMS41NzE0MywtMTcuNjMzOThjLTEuNDA2MjUsLTEuNDA2MjUgLTExLjQzNzIzLC0xLjU3MTQzIC05NS40Mjg1NywtMS41NzE0M2MtODMuOTkxMzUsMCAtOTQuMDIyMzMsMC4xNjUxOCAtOTUuNDI4NTgsMS41NzE0M2MtMS4zMDIwOSwxLjMwMjA5IC0xLjU3MzEsNC40MzAwNyAtMS41ODEyLDE4LjI1Yy0wLjAwOTAzLDE1LjQzMDgzIC0wLjE4NjE2LDE3LjA1MjYyIC0yLjM2NzY1LDIxLjY3ODU3Yy0yLjgwOTgsNS45NTgzMSAtNS41NzQ4Myw4LjY3NDE4IC0xMS42MjI1OCwxMS40MTU5N2MtNS41MTA2OCwyLjQ5ODMgLTE2Ljc3ODMxLDIuNzUwMzMgLTIyLDAuNDkyMDl6bTE5LjA5MDIxLC0xMTEuNzc5MThjOS40NjU1NiwtMy4zODM1IDE2LjA1MjY1LC0xMi43Nzg3OSAxNi4wNTI2NSwtMjIuODk2MTljMCwtMTQuMDcwMzggLTEwLjUwNjUyLC0yNC43MzI2OSAtMjQuMzcxMzIsLTI0LjczMjY5Yy02Ljg5MDk4LDAgLTExLjA1NjA1LDEuNTU0ODggLTE2LjE1MzE4LDYuMDMwMjJjLTUuODIxMTgsNS4xMTEwNyAtOC4wNTg5NiwxMC4yMzk2OCAtOC4wNTg5NiwxOC40Njk3OGMwLDcuNzczOTkgMi4wNjczNiwxMi45MjU5NSA3LjA5OTAyLDE3LjY5MTA1YzYuNDQ5MTEsNi4xMDc0NyAxNy4xNTQ2NCw4LjM5NjUzIDI1LjQzMTc5LDUuNDM3ODN6bTI0Ni45NzM2MiwtMS4zNzg4OGMxOC4wNzQ2NCwtOS4xNjY1OCAxOC4wNzQ2NCwtMzQuMzMzNDIgMCwtNDMuNWMtMTIuODA2ODMsLTYuNDk1IC0yOC4zMjI3MSwtMC44Nzc4IC0zMy44MzcwOCwxMi4yNWMtMi4yNDIyNCw1LjMzNzk4IC0xLjg5OTA3LDE0Ljk3OTcgMC43MjYxNywyMC40MDI3MmM1Ljg5Mjc0LDEyLjE3Mjc3IDIwLjg0MTExLDE3LjA2OTkyIDMzLjExMDkxLDEwLjg0NzI4em0xLjk5NzY1LC0xMDkuMjYxNjZjMi45MzM5NCwtMC41NTY0MSA2LjM0NDczLC0xLjk2MDc3IDcuNTc5NTMsLTMuMTIwOGMyLjQ3OTI0LC0yLjMyOTEzIDQuMzk0MjgsLTkuNzA1NiAzLjczMDAxLC0xNC4zNjc1NGMtMC4yMzUxLC0xLjY1IC0zLjUzNDI3LC0yNS4zNDU2NyAtNy4zMzE0OCwtNTIuNjU3MDRjLTcuMTA5NjIsLTUxLjEzNTcyIC03Ljg3OTYyLC01NC42Nzc3NSAtMTIuODY2OTIsLTU5LjE4ODA1Yy01LjE0NjYyLC00LjY1NDM3IC0yLjcwMDYxLC00LjU2NDQ5IC0xMjUuNDc5NzksLTQuNjExMDJjLTgzLjA3MzM5LC0wLjAzMTQ5IC0xMTcuMDc4NzgsMC4yNzYyOSAtMTIwLjMwNTg3LDEuMDg4ODljLTUuODg5MTEsMS40ODI4OSAtMTAuMDAzNzQsNS43MTUyOSAtMTEuODQ3MTgsMTIuMTg2MjhjLTIuNDc1NDgsOC42ODk1OSAtMTUuNjkxNTYsMTA2LjM1NDk1IC0xNC45MzY1NiwxMTAuMzc5NDdjMS4wMDI2Myw1LjM0NDQ3IDUuMjAwMTQsOS40ODI3OCAxMC40ODU1NCwxMC4zMzc2NWM3LjE2ODg1LDEuMTU5NTEgMjY0Ljg0NjI4LDEuMTE0MDIgMjcwLjk3MjcyLC0wLjA0Nzg0em0tNTkuNTYxNDcsLTE1NS45NjI4NWM0LjYxNTM3LC0xLjA2OTM3IDcuMTczMTQsLTMuNzY2MjggOC4wNjExMywtOC40OTk2M2MwLjc5MjcyLC00LjIyNTU3IC0xLjc0Njc4LC05LjgzMzc5IC01LjE1NjM3LC0xMS4zODczMWMtMS44MDA2NiwtMC44MjA0MyAtMjMuOTMwNDksLTEuMTMyMzQgLTc5LjIwMTgxLC0xLjExNjMyYy03MS4xNjQ5OCwwLjAyMDYzIC03Ni44NTY5NSwwLjE0Njk4IC03OC44MzU5MSwxLjc1Yy00LjcwODMyLDMuODEzODkgLTUuNjM5NTIsOS42Mjg1NSAtMi4zOTQzMiwxNC45NTA5M2MxLjE3MDQxLDEuOTE5NTggMy4wOSwzLjQ3MTYxIDQuNzQ2MjYsMy44Mzc0N2MxLjUyOTU2LDAuMzM3ODcgMy4yMzEwMiwwLjc2NDQyIDMuNzgxMDIsMC45NDc4OWMyLjQ0ODQ5LDAuODE2NzcgMTQ1LjM5MDA4LDAuMzUzMzggMTQ5LC0wLjQ4MzAzeiIvPiA8L2c+PC9zdmc+"
                ),
                "Adelantado" => array(
                    "symbolSize" => "25",
                    "type" => "scatter",
                    "color" => "#67ab3d",
                    "symbol" => "image://data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSI2MzAiIHZpZXdCb3g9IjAgMCA2MzAgNjMwIiB3aWR0aD0iNjMwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxjaXJjbGUgY3g9IjMxNSIgY3k9IjMxNSIgZmlsbD0iIzA5MCIgcj0iMzAwIi8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTE1LDY1KSI+PGRlc2MvPjxwYXRoIGQ9Ik0gMTgxLjkwNjU4LDE1Ni44Nzk5NyBMIDE5OS42OTA0OCw4Ny43NDIwOTUgTCAyMzkuMDIzNDEsNDguMDMwMzE1IEwgNDM1LjA3MDMsNDguNzc5NTkzIEwgNDY2LjI1OTY2LDg3LjM2NjYxNSBMIDQ4OC40MzczLDE2Mi4xNzA4IEwgNDkxLjY0MDg2LDM1Ny41MjM5MyBMIDE4Mi4xMTMwMSwzNDYuOTI2OTkgTCAxODEuOTA2NTgsMTU2Ljg3OTk3IHogIiBmaWxsPSIjMDkwIi8+PHBhdGggZD0iTSAyMDIuMDEwNTksNDU0LjI2Mzk2IEMgMTk3LjQ2NTcxLDQ1Mi4yOTg0MSAxOTIuMzgyOTIsNDQ3LjM4MTc1IDE4OS43MDAxNyw0NDIuMzU1OSBDIDE4Ny43OTgyOSw0MzguNzkyOTMgMTg3LjUyMzc3LDQzNi4zODkyOCAxODcuMTg3NTUsNDIwLjM1NTkgQyAxODYuOTMzOTgsNDA4LjI2NDA3IDE4Ni40MzI3LDQwMi4xMTQ3NSAxODUuNjYwMzQsNDAxLjYyMTIxIEMgMTg1LjAyNzk4LDQwMS4yMTcxMyAxNzguMzA5ODQsNDAwLjg3OTYzIDE3MC43MzExNiw0MDAuODcxMjEgTCAxNTYuOTUxNzIsNDAwLjg1NTkgTCAxNTcuMjQ5MSwzMDcuMTA1OSBMIDE1Ny41NDY0NywyMTMuMzU1OSBMIDE2Ni4yMzc5OCwxNDguMzU1OSBDIDE3MS4wMTgzMSwxMTIuNjA1ODkgMTc1LjYxNjY2LDgxLjA3MjU2NSAxNzYuNDU2NTMsNzguMjgxODI3IEMgMTc4LjcwNzA4LDcwLjgwMzY5NSAxODUuMDk1MjUsNjEuNjEyNzE5IDE5MS42OTcxNSw1Ni4zNTQ0MDEgQyAxOTguNDU2NjEsNTAuOTcwNTgxIDIxNi4wMTIwOCw0Mi4yMTM4OTggMjI3LjUxMDU5LDM4LjQ5MDYyMSBDIDIzOC4xMjI5LDM1LjA1NDMwMSAyNTcuMzc2OSwzMC40ODE3MDkgMjc0LjAxMDYsMjcuNDQ3NDI2IEMgMzE5LjEyNzkxLDE5LjIxNzIyNyAzNTUuOTc2NjcsMTkuNjM4ODc2IDQwMy42MzIzOCwyOC45MzA2NDYgQyA0NTguMTI2NjgsMzkuNTU1Nzg1IDQ4NS4zMjYzNCw1NC4yNjQ2NzggNDkyLjk3MzM3LDc3LjI0NDAwOSBDIDQ5NC4zNTM4Nyw4MS4zOTI0MjUgNDk3Ljg1MTI0LDEwNS4xMjYwMSA1MDMuNTI0NDcsMTQ4Ljg0NTE3IEwgNTEyLjAxMDYsMjE0LjI0MTA1IEwgNTEyLjAxMDYsMzA3LjU0ODQ3IEwgNTEyLjAxMDYsNDAwLjg1NTkgTCA0OTguNzYwNiw0MDAuODcxMjEgQyA0OTEuNDczMSw0MDAuODc5NjMgNDg0Ljk5MzkzLDQwMS4yMTcxMyA0ODQuMzYyNDUsNDAxLjYyMTIxIEMgNDgzLjU5NzEsNDAyLjExMDk1IDQ4My4wNzc1LDQwOC4xOTA0NiA0ODIuODAzOTYsNDE5Ljg1NTkgQyA0ODIuMzI4NDQsNDQwLjEzNTc2IDQ4MS4zNTIyMiw0NDMuNjI0NzUgNDc0LjQ1NDY2LDQ0OS42OTYyMyBDIDQ2MS4xODU2OCw0NjEuMzc2MDUgNDM3LjkyOTM5LDQ1NS44NzkwMSA0MzMuMTUyMDMsNDM5LjkzMzYyIEMgNDMyLjUwMjI5LDQzNy43NjQ5NiA0MzIuMDEwNiw0MjkuMjA0NzEgNDMyLjAxMDYsNDIwLjA2MTMxIEMgNDMyLjAxMDYsNDA2Ljc5MjMyIDQzMS43MzczLDQwMy43MjU0NiA0MzAuNDM5MTcsNDAyLjQyNzMzIEMgNDI5LjAzMjkyLDQwMS4wMjEwOCA0MTkuMDAxOTQsNDAwLjg1NTkgMzM1LjAxMDYsNDAwLjg1NTkgQyAyNTEuMDE5MjUsNDAwLjg1NTkgMjQwLjk4ODI3LDQwMS4wMjEwOCAyMzkuNTgyMDIsNDAyLjQyNzMzIEMgMjM4LjI3OTkzLDQwMy43Mjk0MiAyMzguMDA4OTIsNDA2Ljg1NzQgMjM4LjAwMDgyLDQyMC42NzczMyBDIDIzNy45OTE3OSw0MzYuMTA4MTYgMjM3LjgxNDY2LDQzNy43Mjk5NSAyMzUuNjMzMTcsNDQyLjM1NTkgQyAyMzIuODIzMzcsNDQ4LjMxNDIxIDIzMC4wNTgzNCw0NTEuMDMwMDggMjI0LjAxMDU5LDQ1My43NzE4NyBDIDIxOC40OTk5MSw0NTYuMjcwMTcgMjA3LjIzMjI4LDQ1Ni41MjIyIDIwMi4wMTA1OSw0NTQuMjYzOTYgeiBNIDIyMS4xMDA4LDM0Mi40ODQ3OCBDIDIzMC41NjYzNiwzMzkuMTAxMjggMjM3LjE1MzQ1LDMyOS43MDU5OSAyMzcuMTUzNDUsMzE5LjU4ODU5IEMgMjM3LjE1MzQ1LDMwNS41MTgyMSAyMjYuNjQ2OTMsMjk0Ljg1NTkgMjEyLjc4MjEzLDI5NC44NTU5IEMgMjA1Ljg5MTE1LDI5NC44NTU5IDIwMS43MjYwOCwyOTYuNDEwNzggMTk2LjYyODk1LDMwMC44ODYxMiBDIDE5MC44MDc3NywzMDUuOTk3MTkgMTg4LjU2OTk5LDMxMS4xMjU4IDE4OC41Njk5OSwzMTkuMzU1OSBDIDE4OC41Njk5OSwzMjcuMTI5ODkgMTkwLjYzNzM1LDMzMi4yODE4NSAxOTUuNjY5MDEsMzM3LjA0Njk1IEMgMjAyLjExODEyLDM0My4xNTQ0MiAyMTIuODIzNjUsMzQ1LjQ0MzQ4IDIyMS4xMDA4LDM0Mi40ODQ3OCB6IE0gNDY4LjA3NDQyLDM0MS4xMDU5IEMgNDg2LjE0OTA2LDMzMS45MzkzMiA0ODYuMTQ5MDYsMzA2Ljc3MjQ4IDQ2OC4wNzQ0MiwyOTcuNjA1OSBDIDQ1NS4yNjc1OSwyOTEuMTEwOSA0MzkuNzUxNzEsMjk2LjcyODEgNDM0LjIzNzM0LDMwOS44NTU5IEMgNDMxLjk5NTEsMzE1LjE5Mzg4IDQzMi4zMzgyNywzMjQuODM1NiA0MzQuOTYzNTEsMzMwLjI1ODYyIEMgNDQwLjg1NjI1LDM0Mi40MzEzOSA0NTUuODA0NjIsMzQ3LjMyODU0IDQ2OC4wNzQ0MiwzNDEuMTA1OSB6IE0gNDcwLjA3MjA3LDIzMS44NDQyNCBDIDQ3My4wMDYwMSwyMzEuMjg3ODMgNDc2LjQxNjgsMjI5Ljg4MzQ3IDQ3Ny42NTE2LDIyOC43MjM0NCBDIDQ4MC4xMzA4NCwyMjYuMzk0MzEgNDgyLjA0NTg4LDIxOS4wMTc4NCA0ODEuMzgxNjEsMjE0LjM1NTkgQyA0ODEuMTQ2NTEsMjEyLjcwNTkgNDc3Ljg0NzM0LDE4OS4wMTAyMyA0NzQuMDUwMTMsMTYxLjY5ODg2IEMgNDY2Ljk0MDUxLDExMC41NjMxNCA0NjYuMTcwNTEsMTA3LjAyMTExIDQ2MS4xODMyMSwxMDIuNTEwODEgQyA0NTYuMDM2NTksOTcuODU2NDM5IDQ1OC40ODI2LDk3Ljk0NjMyMyAzMzUuNzAzNDIsOTcuODk5Nzg4IEMgMjUyLjYzMDAzLDk3Ljg2ODMwMiAyMTguNjI0NjQsOTguMTc2MDg0IDIxNS4zOTc1NSw5OC45ODg2NzUgQyAyMDkuNTA4NDQsMTAwLjQ3MTU3IDIwNS4zOTM4MSwxMDQuNzAzOTcgMjAzLjU1MDM3LDExMS4xNzQ5NiBDIDIwMS4wNzQ4OSwxMTkuODY0NTUgMTg3Ljg1ODgxLDIxNy41Mjk5MSAxODguNjEzODEsMjIxLjU1NDQzIEMgMTg5LjYxNjQ0LDIyNi44OTg5IDE5My44MTM5NSwyMzEuMDM3MjEgMTk5LjA5OTM1LDIzMS44OTIwOCBDIDIwNi4yNjgyLDIzMy4wNTE1OSA0NjMuOTQ1NjMsMjMzLjAwNjEgNDcwLjA3MjA3LDIzMS44NDQyNCB6IE0gNDEwLjUxMDYsNzUuODgxMzkxIEMgNDE1LjEyNTk3LDc0LjgxMjAxOSA0MTcuNjgzNzQsNzIuMTE1MTEyIDQxOC41NzE3Myw2Ny4zODE3NiBDIDQxOS4zNjQ0NSw2My4xNTYxODkgNDE2LjgyNDk1LDU3LjU0Nzk2NiA0MTMuNDE1MzYsNTUuOTk0NDUyIEMgNDExLjYxNDcsNTUuMTc0MDIgMzg5LjQ4NDg3LDU0Ljg2MjEwOCAzMzQuMjEzNTUsNTQuODc4MTMxIEMgMjYzLjA0ODU3LDU0Ljg5ODc2MSAyNTcuMzU2Niw1NS4wMjUxMTIgMjU1LjM3NzY0LDU2LjYyODEzMSBDIDI1MC42NjkzMiw2MC40NDIwMTcgMjQ5LjczODEyLDY2LjI1NjY3NSAyNTIuOTgzMzIsNzEuNTc5MDU4IEMgMjU0LjE1MzczLDczLjQ5ODYzOCAyNTYuMDczMzIsNzUuMDUwNjcyIDI1Ny43Mjk1OCw3NS40MTY1MjkgQyAyNTkuMjU5MTQsNzUuNzU0Mzk4IDI2MC45NjA2LDc2LjE4MDk0OSAyNjEuNTEwNiw3Ni4zNjQ0MTkgQyAyNjMuOTU5MDksNzcuMTgxMTg5IDQwNi45MDA2OCw3Ni43MTc4MDIgNDEwLjUxMDYsNzUuODgxMzkxIHogIiBzdHlsZT0iZmlsbDojZmZmIi8+PC9nPjwvc3ZnPg=="
                ),
                "xAxis" => array(
                    "name" => "nom_contro",
                    "type" => "category",
                    "formatter" => "{value}"
                ),
                "yAxis" => array(
                    "name" => "status",
                    "type" => "category",
                    "formatter" => "{value}"
                )
            );

            $json = array();

            foreach ($ruta as $key => $value) {

                //Create fechaRegistrada
                $fechaRegistrada = "";
                if($value["ind_estado"] == 0){
                    $fechaRegistrada = $value["fec_alarma"];
                }

                //Create graphic message points
                $graphicMessage = utf8_encode(
                    "<div style='text-align: left; font-size: 10pt; padding: 10px; font-weight: bold;'>" .
                        "<div style='text-align: center; font-weight: bold; font-size: 12pt;'>" .
                            $value[$configGraphic["yAxis"]["name"]] . 
                        "</div>" .
                        "<div>" .
                            "El veh�culo pas� " . 
                            strtolower($value[$configGraphic["yAxis"]["name"]]) . 
                            "<br>por el punto '" . 
                            $value[$configGraphic["xAxis"]["name"]] .
                            "'" .
                        "</div>" .
                        "<div style='display: flex; flex-flow: row nowrap; justify-content: space-between'>" .
                            "<span style='font-size: 11pt; font-weight: bold; margin-right: 5px;'>Hora programada: </span>" .
                            "<span>" . $value["fec_planea"] . "</span>" .
                        "</div>" .
                        "<div style='display: flex; flex-flow: row nowrap; justify-content: space-between'>" .
                            "<span style='font-size: 11pt; font-weight: bold; margin-right: 5px;'>Hora registrada: </span>" .
                            "<span>$fechaRegistrada</span>" .
                        "</div>" .
                    "</div>"
                );

                //Clean values
                foreach ($value as $key1 => $value1) {
                    $value[$key1] = utf8_encode($value1);
                }

                //Create principal positions JSON
                if(!isset($json["data"][$value[$configGraphic["yAxis"]["name"]]])){

                    //Create series positions
                    $json["data"][$value[$configGraphic["yAxis"]["name"]]]["data"] = array();
                    $json["data"][$value[$configGraphic["yAxis"]["name"]]]["name"] = $value[$configGraphic["yAxis"]["name"]];
                    $json["data"][$value[$configGraphic["yAxis"]["name"]]]["type"] = $configGraphic[$value[$configGraphic["yAxis"]["name"]]]["type"];
                    $json["data"][$value[$configGraphic["yAxis"]["name"]]]["symbol"] = $configGraphic[$value[$configGraphic["yAxis"]["name"]]]["symbol"];
                    $json["data"][$value[$configGraphic["yAxis"]["name"]]]["symbolSize"] = $configGraphic[$value[$configGraphic["yAxis"]["name"]]]["symbolSize"];

                    //Create and assign y axis data and positions
                    if(!isset($json["yAxis"])){
                        $json["yAxis"]["data"] = array();
                        $json["yAxis"]["type"] = $configGraphic["yAxis"]["type"];
                        $json["yAxis"]["formatter"] = $configGraphic["yAxis"]["formatter"];
                    }

                    //Create new y axis position
                    if(array_search($value[$configGraphic["yAxis"]["name"]], $json["yAxis"]["data"]) === false){
                        array_push($json["yAxis"]["data"], $value[$configGraphic["yAxis"]["name"]]);
                    }
                }

                //Validate x axis position
                if(!isset($json["data"][$value[$configGraphic["xAxis"]["name"]]])){

                    //Create and assign x axis data and positions
                    if(!isset($json["xAxis"])){
                        $json["xAxis"]["data"] = array();
                        $json["xAxis"]["type"] = $configGraphic["xAxis"]["type"];
                        $json["xAxis"]["formatter"] = $configGraphic["xAxis"]["formatter"];
                    }

                    //Create new x axis position
                    if(array_search($value[$configGraphic["xAxis"]["name"]], $json["xAxis"]["data"]) === false){
                        array_push($json["xAxis"]["data"], $value[$configGraphic["xAxis"]["name"]]);
                    }
                }

                //Fill data series
                array_push($json["data"][$value[$configGraphic["yAxis"]["name"]]]["data"], array(
                    array_search($value[$configGraphic["xAxis"]["name"]], $json["xAxis"]["data"]),
                    array_search($value[$configGraphic["yAxis"]["name"]], $json["yAxis"]["data"]),
                    $graphicMessage
                ));
            }

            //Get necessary data for pie graphic
            $json["data"]["withoutAxes"][0]["data"] = array();
            $json["data"]["withoutAxes"][0]["name"] = "";
            $json["data"]["withoutAxes"][0]["type"] = "pie";
            $json["data"]["withoutAxes"][0]["center"] = array('75%', '12%');
            $json["data"]["withoutAxes"][0]["radius"] = "15%";
            $json["data"]["withoutAxes"][0]["z"] = "100";
            $countTotal = count($ruta);
            foreach($json["data"] as $key => $value){

                if($key != "withoutAxes"){
                    $segment = array(
                        "value" => count($value["data"]) * 100 / $countTotal,
                        "name" => $key,
                        "formatterType" => "percentage",
                        "itemStyle" => array(
                            "color" => $configGraphic[$key]["color"]
                        )
                    );

                    array_push($json["data"]["withoutAxes"][0]["data"], $segment);
                }
                
            }
            
            echo json_encode($json);

        }

        public function getTranspServic( $mTipEtapax = NULL, $mCodTransp = NULL, $mAddWherex = NULL )
        {
            
            $mTipServic = '""';
            $mLisTransp = $this->getTranspCargaControlador();
    
            //Tipo de servicio Horario de gesti�n
            $$mHorTipSer='';
            $arrayHorTipSer = array();
            $_REQUEST[tip_horlab1] == '1' ? $arrayHorTipSer[] = '1' : '';
            $_REQUEST[tip_horlab2] == '1' ? $arrayHorTipSer[] = '2' : '';
            $_REQUEST[tip_horlab3] == '1' ? $arrayHorTipSer[] = '3' : '';
    
            foreach($arrayHorTipSer as $index=>$servicio){
                $mHorTipSer .= $servicio;
                if( $index+1 < count($arrayHorTipSer)){
                    $mHorTipSer .=', ';
                }
            }
    
            $mFilHorasx = $_REQUEST[fil_horasx] == '1' ? 'AND i.hor_ingres = "'.$_REQUEST['hor_inicia'].'" AND i.hor_ingres = "'.$_REQUEST['hor_finalx'].'"': '';
    
            $mSql = "/*OPTIMIZADO  */ 
                        SELECT a.*,
                             GROUP_CONCAT(h.cod_usuari ORDER BY h.cod_usuari ASC SEPARATOR ', ' ) AS usr_asigna
                        FROM (
    
                                            SELECT c.ind_segprc,c.ind_segcar, 
                                                   c.ind_segctr,c.ind_segtra, c.ind_segdes, 
                                                   c.cod_transp, c.num_consec, d.nom_tipser, 
                                                   UPPER(e.abr_tercer) AS nom_transp, c.tie_contro AS tie_nacion,
                                                   c.tie_conurb AS tie_urbano, c.tie_desurb, 
                                                   c.tie_desnac, c.tie_desimp, c.tie_desexp, 
                                                   c.tie_destr1, c.tie_destr2, c.cod_tipser, 
                                                   c.ind_conper, c.hor_pe1urb, c.hor_pe2urb, 
                                                   c.hor_pe1nac, c.hor_pe2nac, c.hor_pe1imp, 
                                                   c.hor_pe2imp, c.hor_pe1exp, c.hor_pe2exp, 
                                                   c.hor_pe1tr1, c.hor_pe2tr1, c.hor_pe1tr2, 
                                                   c.hor_pe2tr2, 
                                                   c.tgl_contro AS tgl_nacion, c.tgl_contro AS tgl_urbano,
                                                   c.tgl_prcnac AS tgl_nacprc, c.tgl_prcurb AS tgl_urbprc
                                              FROM ".BASE_DATOS.".tab_transp_tipser c 
                                        INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
                                                ON c.cod_tipser = d.cod_tipser 
                                        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
                                                ON c.cod_transp = e.cod_tercer 
                                        INNER JOIN (	  SELECT cod_transp , MAX(num_consec) AS num_consec 
                                                            FROM ".BASE_DATOS.".tab_transp_tipser  
                                                        GROUP BY cod_transp 
                                                   ) f ON c.cod_transp = f.cod_transp AND c.num_consec = f.num_consec
                                          GROUP BY c.cod_transp
                             ) a 
                             LEFT JOIN (
                                SELECT 
                                    cod_usuari,cod_transp 
                                FROM 
                                ".BASE_DATOS.".vis_monito_encdet 
                                GROUP BY 
                                    cod_usuari, cod_transp
                            ) h ON a.cod_transp = h.cod_transp 
                            LEFT JOIN ".BASE_DATOS.".tab_config_horlab i ON a.cod_transp = i.cod_tercer 
                        WHERE 1=1 ";
    
            $mSql .= $mTipEtapax == NULL ? "" : " AND a.{$mTipEtapax} = 1 ";
            $mSql .= $mAddWherex == NULL ? "" : $mAddWherex;
    
            #Filtro por codigo de Transportadora
            if( self::$cTypeUser[tip_perfil] == 'CLIENTE' )
                $mSql .= " AND a.cod_transp = '". self::$cTypeUser[cod_transp] ."' ";
            else{
                if( $mCodTransp != NULL )
                    $mSql .= $mCodTransp != 'TODAS' ? " AND a.cod_transp IN ( {$mCodTransp} ) " : "";
                else
                    $mSql .= $_REQUEST[cod_transp] ? " AND a.cod_transp IN ( {$_REQUEST[cod_transp]} ) " : "";
            }
    
            $mCodUsuari = explode(',', $_REQUEST[cod_usuari]);
            $mSinFiltro = false;
            foreach ($mCodUsuari as $key => $value) {
                if( $value == '"SIN"' ){
                    $mSinFiltro = true;
                    break;
                }
            }
    
            #Filtro Por Usuario Asignado
            if( self::$cTypeUser[tip_perfil] == 'CONTROL' || self::$cTypeUser[tip_perfil] == 'EAL' || self::$cTypeUser[tip_perfil] == 'OAL' ){
                $mSql .= " AND h.cod_usuari = '".$_SESSION[datos_usuario][cod_usuari]."' ";
                $mSql .= $mLisTransp != '' && $mLisTransp != null ? " AND a.cod_transp IN ( $mLisTransp ) " : " AND a.cod_transp IN ( '' ) ";
            } elseif( $mSinFiltro == true )
                $mSql .= " AND ( h.cod_transp IS NULL OR UPPER(h.cod_usuari) IN ({$_REQUEST[cod_usuari]}) )";
            else
                $mSql .= $_REQUEST[cod_usuari] ? " AND UPPER(h.cod_usuari) IN ( {$_REQUEST[cod_usuari]} ) " : "";
    
            #Otros Filtros
            $mSql .= $mTipServic != '""' ? " AND a.cod_tipser IN (".$mTipServic.") " : "";
    
            $mSql .= $mHorTipSer != '' ? " AND i.tip_servic IN (".$mHorTipSer.") " : "";
    
            $mSql .= $mFilHorasx;
            
            $mSql .= " GROUP BY a.cod_transp ORDER BY h.cod_usuari, a.nom_transp ASC ";
    
            $mConsult = new Consulta( $mSql, self::$conexion );
            
            return $mConsult -> ret_matrix('a');
        }
        

        function loadCargueTableData(){

            //Select "Cargue"
            $queryCargue = "
            SELECT 
                   c.nom_remdes AS `Sitio de cargue`,
                   GROUP_CONCAT(f.ped_remisi SEPARATOR '<br>') AS `Pedido`, 
                   GROUP_CONCAT(f.num_docume SEPARATOR '<br>') AS `Remision`,
                   d.nom_ciudad AS `Ciudad`,
                   c.dir_remdes AS `Direcci&oacuten`,
                   @fec_llegcar :=(e.fec_cumcar) AS `Llegada a cargue`,
                   @fec_ingcar :=(a.fec_ingcar) AS `Entrada a cargue`,
                   @fec_salcar := (a.fec_salcar) AS `Salida de cargue`,
                    IF(
                        TIMESTAMPDIFF(MINUTE, @fec_llegcar, @fec_ingcar) IS NOT NULL,
                        CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE, @fec_llegcar, @fec_ingcar)/60),'h ',MOD(TIMESTAMPDIFF(MINUTE, @fec_llegcar, @fec_ingcar),60),'m'),
                        0
                    ) AS 'D. Tiempo Lle - Ent',
                    IF(
                        TIMESTAMPDIFF(MINUTE, @fec_ingcar, @fec_salcar) IS NOT NULL,
                        CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE, @fec_ingcar, @fec_salcar)/60),'h ',MOD(TIMESTAMPDIFF(MINUTE, @fec_ingcar, @fec_salcar),60),'m'),
                        0
                    ) AS 'D. Tiempo Ent - Sal',
                   @fechaCargue := CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS `Fecha y Hora Programada`, 
                   IF(
                       e.fec_cumcar IS NOT NULL,
                       IF(
                            e.fec_cumcar <= @fechaCargue,
                            'Cumpli&oacute;',
                            'No Cumpli&oacute;'
                       ),
                       'Pendiente'
                   ) AS `Cumpli&oacute;`,
                   IF(
                       e.fec_cumcar IS NOT NULL,
                       IF(
                            e.fec_cumcar <= @fechaCargue,
                            'success',
                            'danger'
                       ),
                       'white'--   
                   ) AS `color`
              FROM ".BASE_DATOS.".tab_despac_despac a 
        INNER JOIN ".BASE_DATOS.".tab_despac_corona b
                ON a.num_despac = b.num_dessat
        INNER JOIN ".BASE_DATOS.".tab_genera_remdes c
                ON b.nom_sitcar = c.cod_remdes
        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d
                ON c.cod_ciudad = d.cod_ciudad
        INNER JOIN ".BASE_DATOS.".tab_despac_sisext e
                ON a.num_despac = e.num_despac
        INNER JOIN ".BASE_DATOS.".tab_despac_destin f
                ON a.num_despac = f.num_despac
             WHERE a.num_despac = '" . $_REQUEST["num_despac"] . "'
                ";

            //Execute query 
            $queryCargue = new Consulta($queryCargue, self::$conexion);
            $cargueRows = $queryCargue -> ret_matrix('a');

            $cargue = array();
            $json["titles"] = array();

            //Clean data
            foreach ($cargueRows as $key => $value) {
                
                foreach ($value as $key1 => $value1) {
                    
                    //Create titles
                    if($key == 0){
                        array_push($json["titles"], utf8_encode($key1));
                    }

                    //Encode data
                    $cargue[$key][utf8_encode($key1)] = utf8_encode($value1);

                }

            }

            //Assign rows
            $json["rows"] = $cargue;

            echo json_encode($json);

        }

        function loadDescargueTableData(){

            //Select "Descargue"
            $queryDescargue = "
            SELECT  
            c.nom_remdes AS `Nombre del Destinatario`,
            b.ped_remisi AS `Pedido`, 
            b.num_docume AS `Remision`,
            b.num_docalt AS `Remesas (Doc. Alterno)`, d.nom_ciudad AS `Ciudad`, c.dir_remdes AS `Direcci&oacuten`,
            @fec_llecli := b.fec_cumdes AS `Llegada a Descargue`,
            @fec_inides := b.fec_ingdes AS `Entrada a Descargue`,
            @fec_saldes := b.fec_saldes AS `Salida de Descargue`,
            IF(
                    TIMESTAMPDIFF( MINUTE, @fec_llecli, @fec_inides) IS NOT NULL,
                    CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE, @fec_llecli, @fec_inides)/60),'h ',MOD(TIMESTAMPDIFF(MINUTE, @fec_llecli, @fec_inides),60),'m'),
                    0
              ) AS 'D. Tiempo Lle - Ent',
            IF(
                    TIMESTAMPDIFF( MINUTE, @fec_inides, @fec_saldes) IS NOT NULL,
                    CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE, @fec_inides, @fec_saldes)/60),'h ',MOD(TIMESTAMPDIFF(MINUTE, @fec_inides, @fec_saldes),60),'m'),
                    0
              ) AS 'D. Tiempo Ent - Sal',
            e.fec_llegpl AS `Fecha y Hora Est. Ent.`,
            @fechaDescargue := CONCAT(b.fec_citdes, ' ', b.hor_citdes) AS `Fecha y Hora Programada`, IF(b.fec_cumdes IS NOT NULL, IF(b.fec_cumdes <= @fechaDescargue, 'Cumpli&oacute', 'No Cumpli&oacute'), 'Pendiente') AS `Cumpli&oacute`,
            IF(
                b.fec_cumdes IS NOT NULL,
                IF(
                    b.fec_cumdes <= @fechaDescargue,
                    'success',
                    'danger'
                  ), 'white'
              ) AS `color`
                    FROM ".BASE_DATOS.".tab_despac_despac a
                INNER JOIN ".BASE_DATOS.".tab_despac_destin b ON a.num_despac = b.num_despac
                INNER JOIN ".BASE_DATOS.".tab_genera_remdes c ON b.cod_remdes = c.cod_remdes
                INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d ON c.cod_ciudad = d.cod_ciudad
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige e ON a.num_despac = e.num_despac
                LEFT JOIN (
                            (
                                -- LLEGADA DESCARGUE
                                    SELECT MAX(f.fec_contro) AS fec_contro, g.cod_remdes, f.num_despac
                                FROM ".BASE_DATOS.".tab_despac_contro f
                                INNER JOIN tab_despac_destin g ON f.num_despac = g.num_despac
                                AND f.fec_contro = g.fec_llecli
                                WHERE f.cod_noveda = '9263'
                                    AND f.num_despac = '" . $_REQUEST["num_despac"] . "' GROUP BY g.cod_remdes
                            )
                            UNION
                            (
                                -- ENTRADA DESCARGUE
                                    SELECT MAX(f.fec_contro) AS fec_contro, g.cod_remdes, f.num_despac
                                FROM ".BASE_DATOS.".tab_despac_contro f
                                INNER JOIN tab_despac_destin g ON f.num_despac = g.num_despac
                                AND f.fec_contro = g.fec_inides
                                WHERE f.cod_noveda = '9264'
                                    AND f.num_despac = '" . $_REQUEST["num_despac"] . "' GROUP BY g.cod_remdes
                            )
                            UNION
                            (
                                -- SALIDA DESCARGUE
                                SELECT MAX(f.fec_contro) AS fec_contro, g.cod_remdes, f.num_despac
                            FROM ".BASE_DATOS.".tab_despac_contro f
                            INNER JOIN tab_despac_destin g ON f.num_despac = g.num_despac
                            AND f.fec_contro = g.fec_saldes
                            WHERE f.cod_noveda = '9271'
                                AND f.num_despac = '" . $_REQUEST["num_despac"] . "' GROUP BY g.cod_remdes
                            )
                        ) f ON b.num_despac = f.num_despac AND b.cod_remdes = f.cod_remdes
                WHERE a.num_despac = '" . $_REQUEST["num_despac"] . "'
                GROUP BY b.cod_remdes
                ";
            //echo "<pre>".print_r($queryDescargue)."</pre>";
            //Execute query 
            $queryDescargue = new Consulta($queryDescargue, self::$conexion);
            $descargueRows = $queryDescargue -> ret_matrix('a');
            $descargue = array();
            $json["titles"] = array();

            //Clean data
            foreach ($descargueRows as $key => $value) {
                
                foreach ($value as $key1 => $value1) {
                    
                    //Create titles
                    if($key == 0){
                        array_push($json["titles"], utf8_encode($key1));
                    }

                    //Encode data
                    $descargue[$key][utf8_encode($key1)] = utf8_encode($value1);

                }

            }

            //Assign rows
            $json["rows"] = $descargue;

            echo json_encode($json);

        }
        
        function cargueGraphics(){

            //Select "Cargue"
            $queryCargue = "
            SELECT 
                COUNT(e.cod_remdes) AS `Conteo`, 
                e.nom_remdes AS `Sitio`, 
                f.nom_ciudad AS `Ciudad`, 
                @fec_citcar := DATE_FORMAT(
                    CONCAT(a.fec_citcar, ' ', a.hor_citcar), 
                    '%Y-%m-%d %H:%i:%s'
                ) AS `Fecha y Hora Programada`, 
                d.fec_cumcar AS `Fecha y Hora Ejecutada`, 
                IF(
                    d.fec_cumcar IS NOT NULL, 
                    IF(
                        d.fec_cumcar <= @fec_citcar, 'Cumplidas', 
                        'No Cumplidas'
                    ), 
                    'Por Cumplir'
                ) AS Cumplimiento 
            FROM 
                ".BASE_DATOS.".tab_despac_despac a 
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                INNER JOIN ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat 
                INNER JOIN ".BASE_DATOS.".tab_despac_sisext d ON a.num_despac = d.num_despac 
                INNER JOIN ".BASE_DATOS.".tab_genera_remdes e ON c.nom_sitcar = e.cod_remdes 
                INNER JOIN ".BASE_DATOS.".tab_genera_ciudad f ON e.cod_ciudad = f.cod_ciudad 
            WHERE 
                a.ind_planru = 'S' 
                AND a.ind_anulad in ('R') 
                AND b.cod_transp = '".NIT_TRANSPOR."' 
                AND a.fec_salida IS NOT NULL 
                AND a.fec_salida <= NOW() 
                AND a.fec_llegad IS NULL 
            GROUP BY 
                e.cod_remdes
            ";

            //Create necessary variables
            $json = array();

            //Execute query 
            $queryCargue = new Consulta($queryCargue, self::$conexion);
            $cargueRows = $queryCargue -> ret_matrix('a');

            $_SESSION["queryXLS"] = $cargueRows;

            foreach ($cargueRows as $key => $value) {

                //Clean data
                foreach ($value as $key1 => $value1) {
                    $value[$key1] = utf8_encode($value1);
                }
                
                if(!isset($json["stats"][$value["Cumplimiento"]]))
                {
                    $json["stats"]["No Cumplidas"] = 0;
                    $json["stats"]["Cumplidas"] = 0;
                    $json["stats"]["Por Cumplir"] = 0;
                    $json["stats"]["Total Citas"] = 0;
                    $json["citasPorCentro"] = array();
                }
                if(!isset($json["citasPorCentro"][$value["Ciudad"]]))
                {
                    $json["citasPorCentro"][$value["Ciudad"]]["No Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["Por Cumplir"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"] = array();
                }
                if(!isset($json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]))
                {
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]["No Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]["Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]["Por Cumplir"] = 0;
                }

                $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]][$value["Cumplimiento"]] += $value["Conteo"];
                $json["citasPorCentro"][$value["Ciudad"]][$value["Cumplimiento"]] += $value["Conteo"];
                $json["stats"][$value["Cumplimiento"]] += $value["Conteo"];
                $json["stats"]["Total Citas"] += $value["Conteo"];
            }

            echo json_encode(self::cleanArray($json));
        }

        function descargueGraphics(){
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Select "Descargue"
            $queryDescargue = "
                        SELECT 
                                COUNT(e.cod_remdes) AS `Conteo`, 
                                e.nom_remdes AS `Sitio`, 
                                f.nom_ciudad AS `Ciudad`, 
                                @fec_citdes := DATE_FORMAT(
                                    CONCAT(d.fec_citdes, ' ', d.hor_citdes), 
                                    '%Y-%m-%d %H:%i:%s'
                                ) AS `Fecha y Hora Programada`, 
                                d.fec_cumdes AS `Fecha y Hora Ejecutada`, 
                                IF(
                                    d.fec_cumdes IS NOT NULL, 
                                    IF(
                                        d.fec_cumdes <= @fec_citdes, 'Cumplidas', 
                                        'No Cumplidas'
                                    ), 
                                    'Por Cumplir'
                                ) AS Cumplimiento 
                          FROM  ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN  ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                    INNER JOIN  ".BASE_DATOS.".tab_despac_destin d ON a.num_despac = d.num_despac 
                    INNER JOIN  ".BASE_DATOS.".tab_genera_remdes e ON d.cod_remdes = e.cod_remdes 
                    INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad f ON e.cod_ciudad = f.cod_ciudad 
                         WHERE  a.ind_planru = 'S'
                           AND  a.ind_anulad IN ('R')
                           ".$filtransp."
                           AND  a.fec_salida IS NOT NULL
                           AND  a.fec_salida <= NOW()
                           AND  a.fec_llegad IS NULL
                           AND  b.ind_activo = 'S'
                      GROUP BY  e.cod_remdes
                ";

            //Create necessary variables
            $json = array();

            //Execute query 
            $queryDescargue = new Consulta($queryDescargue, self::$conexion);
            $descargueRows = $queryDescargue -> ret_matrix('a');


            $_SESSION["queryXLS"] = $descargueRows;

            foreach ($descargueRows as $key => $value) {

                //Clean data
                foreach ($value as $key1 => $value1) {
                    $value[$key1] = $value1;
                }
                
                if(!isset($json["stats"][$value["Cumplimiento"]]))
                {
                    $json["stats"]["No Cumplidas"] = 0;
                    $json["stats"]["Cumplidas"] = 0;
                    $json["stats"]["Por Cumplir"] = 0;
                    $json["stats"]["Total Citas"] = 0;
                    $json["stats"]["citasPorCentro"] = array();
                }
                if(!isset($json["citasPorCentro"][$value["Ciudad"]]))
                {
                    $json["citasPorCentro"][$value["Ciudad"]]["No Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["Por Cumplir"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"] = array();
                }
                if(!isset($json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]))
                {
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]["No Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]["Cumplidas"] = 0;
                    $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]]["Por Cumplir"] = 0;
                }

                $json["citasPorCentro"][$value["Ciudad"]]["subElements"][$value["Sitio"]][$value["Cumplimiento"]] += $value["Conteo"];
                $json["citasPorCentro"][$value["Ciudad"]][$value["Cumplimiento"]] += $value["Conteo"];
                $json["stats"][$value["Cumplimiento"]] += $value["Conteo"];
                $json["stats"]["Total Citas"] += $value["Conteo"];
            }

            echo json_encode(self::cleanArray($json));
        }
        
        function totalGraphics(){
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
                $filtrans2 = " AND aa.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Select "Total Vehiculos"
            $queryTotalVehiculos = "
                SELECT 
                    IF(
                        g.tip_transp = '1',
                        'Propios',
                        IF(
                            g.tip_transp = '3',
                            'Empresas',
                            'Terceros'
                        )
                    ) AS `stats`,
                    CONCAT(c.nom_ciudad, ' (', LEFT(d.nom_depart, 4), ')') AS `Origen`,
                    CONCAT(e.nom_ciudad, ' (', LEFT(f.nom_depart, 4), ')') AS `Destino`,
                    a.num_despac AS `Despacho`,
                    IF(
                        g.gps_operad IS NOT NULL AND g.gps_operad != '',
                        'Con GPS',
                        'Sin GPS'
                    ) AS `gps`
                FROM 
                    ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                    INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c ON a.cod_ciuori = c.cod_ciudad
                    INNER JOIN ".BASE_DATOS.".tab_genera_depart d ON c.cod_depart = d.cod_depart
                    INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e ON a.cod_ciudes = e.cod_ciudad
                    INNER JOIN ".BASE_DATOS.".tab_genera_depart f ON e.cod_depart = f.cod_depart
                    INNER JOIN ".BASE_DATOS.".tab_despac_corona g ON a.num_despac = g.num_dessat
                WHERE 
                    a.ind_planru = 'S' 
                    AND a.ind_anulad IN ('R') 
                    ".$filtransp."
                    AND a.fec_salida IS NOT NULL 
                    AND a.fec_salida <= NOW() 
                    AND a.fec_llegad IS NULL 
                    AND b.ind_activo = 'S'
                ";

            //Create necessary variables
            $json = array();
            $totalEventos = 0;

            //Execute query 
            $queryTotalVehiculos = new Consulta($queryTotalVehiculos, self::$conexion);
            $totalVehiculosRows = $queryTotalVehiculos -> ret_matrix('a');


            $_SESSION["queryXLS"] = $totalVehiculosRows;

            //Create stats structure
            $json["stats"]["Propios"] = 0;
            $json["stats"]["Terceros"] = 0;
            $json["stats"]["Empresas"] = 0;
            $json["stats"]["Total Vehiculos"] = 0;
            $json["stats"]["Despachos sin ruta"] = 0;

            //Create graphics structure
            $json["graphics"]["Registradas"]["Percentage"] = 0;
            $json["graphics"]["Registradas"]["Quantity"] = 0;
            $json["graphics"]["Ejecutadas"]["Percentage"] = 0;
            $json["graphics"]["Ejecutadas"]["Quantity"] = 0;
            $json["graphics"]["Pendientes"]["Percentage"] = 0;
            $json["graphics"]["Pendientes"]["Quantity"] = 0;

            //Create gps graphics structure
            $json["gps"]["Con GPS"]["Percentage"] = 0;
            $json["gps"]["Con GPS"]["Quantity"] = 0;
            $json["gps"]["Con GPS"]["color"] = "#419645";
            $json["gps"]["Sin GPS"]["Percentage"] = 0;
            $json["gps"]["Sin GPS"]["Quantity"] = 0;
            $json["gps"]["Sin GPS"]["color"] = "#b33b16";

            //"Origenes" and "destinos" format
            $json["origenesFrecuentes"] = array();
            $json["destinosFrecuentes"] = array();

            //Eventos format
            $json["eventos"] = array();

            //Go through data
            foreach ($totalVehiculosRows as $key => $value) {
                
                //Increment stats
                $json["stats"][$value["stats"]]++;
                $json["stats"]["Total Vehiculos"]++;

                //Increments GPS
                $json["gps"][$value["gps"]]["Quantity"]++;

                //Validate "Origenes" existence
                if(!isset($json["origenesFrecuentes"][$value["Origen"]])){
                    $json["origenesFrecuentes"][$value["Origen"]]["Percentage"] = 0;
                    $json["origenesFrecuentes"][$value["Origen"]]["Quantity"] = 0;
                }

                //Increment "Origenes"
                $json["origenesFrecuentes"][$value["Origen"]]["Quantity"]++;

                //Validate "Destinos" existence
                if(!isset($json["destinosFrecuentes"][$value["Destino"]])){
                    $json["destinosFrecuentes"][$value["Destino"]]["Percentage"] = 0;
                    $json["destinosFrecuentes"][$value["Destino"]]["Quantity"] = 0;
                }

                //Increment "Destinos"
                $json["destinosFrecuentes"][$value["Destino"]]["Quantity"]++;
            }
            
            //Assign percentage "Origenes"
            foreach ($json["origenesFrecuentes"] as $key => $value) {
                $json["origenesFrecuentes"][$key]["Percentage"] = round((100 * $value["Quantity"]) / $json["stats"]["Total Vehiculos"], 2);
            }
            
            //Assign percentage "Destinos"
            foreach ($json["destinosFrecuentes"] as $key => $value) {
                $json["destinosFrecuentes"][$key]["Percentage"] = round((100 * $value["Quantity"]) / $json["stats"]["Total Vehiculos"], 2);
            }
            
            //Assign percentage "Con GPS"
            $json["gps"]["Con GPS"]["Percentage"] =  round((100 * $json["gps"]["Con GPS"]["Quantity"]) / $json["stats"]["Total Vehiculos"], 2);
            $json["gps"]["Sin GPS"]["Percentage"] =  round((100 * $json["gps"]["Sin GPS"]["Quantity"]) / $json["stats"]["Total Vehiculos"], 2);

            //Select "Eventos"
            $queryEventos = "
                SELECT 
                    e.nom_noveda AS `Novedad`,
                    COUNT(1) AS `Quantity`
                FROM 
                    ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                    INNER JOIN ".BASE_DATOS.".tab_despac_sisext c ON a.num_despac = c.num_despac 
                    INNER JOIN (
                        SELECT 
                            a.num_despac, 
                            d.nom_noveda,
                            d.cod_etapax, 
                            e.nom_etapax 
                        FROM 
                            ".BASE_DATOS.".tab_despac_despac a 
                            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                            INNER JOIN ".BASE_DATOS.".tab_despac_noveda c ON a.num_despac = c.num_despac 
                            INNER JOIN ".BASE_DATOS.".tab_genera_noveda d ON c.cod_noveda = d.cod_noveda 
                            INNER JOIN ".BASE_DATOS.".tab_genera_etapax e ON d.cod_etapax = e.cod_etapax 
                        WHERE 
                            a.ind_planru = 'S' 
                            AND a.ind_anulad IN ('R') 
                            ".$filtransp."
                            AND a.fec_salida IS NOT NULL 
                            AND a.fec_salida <= NOW() 
                            AND a.fec_llegad IS NULL
                        UNION 
                        SELECT 
                            a.num_despac, 
                            d.nom_noveda,
                            d.cod_etapax, 
                            e.nom_etapax 
                        FROM 
                            ".BASE_DATOS.".tab_despac_despac a 
                            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                            INNER JOIN ".BASE_DATOS.".tab_despac_contro c ON a.num_despac = c.num_despac 
                            INNER JOIN ".BASE_DATOS.".tab_genera_noveda d ON c.cod_noveda = d.cod_noveda 
                            INNER JOIN ".BASE_DATOS.".tab_genera_etapax e ON d.cod_etapax = e.cod_etapax 
                        WHERE 
                            a.ind_planru = 'S' 
                            AND a.ind_anulad IN ('R') 
                            ".$filtransp." 
                            AND a.fec_salida IS NOT NULL 
                            AND a.fec_salida <= NOW() 
                            AND a.fec_llegad IS NULL 
                            AND b.ind_activo = 'S'
                    ) e ON a.num_despac = e.num_despac 
                WHERE 
                    a.ind_planru = 'S' 
                    AND a.ind_anulad IN ('R') 
                    ".$filtransp."
                    AND a.fec_salida IS NOT NULL 
                    AND a.fec_salida <= NOW() 
                    AND a.fec_llegad IS NULL 
                    AND b.ind_activo = 'S'
                    AND e.cod_etapax IN (4,5)
                GROUP BY
                    e.nom_noveda
                ";

            //Execute query 
            $queryEventos = new Consulta($queryEventos, self::$conexion);
            $eventosRows = $queryEventos -> ret_matrix('a');

            foreach ($eventosRows as $key => $value) {
                
                if(!isset($json["eventos"][$value["Novedad"]])){

                    $json["eventos"][$value["Novedad"]]["Percentage"] = 0;
                    $json["eventos"][$value["Novedad"]]["Quantity"] = 0;

                }

                $json["eventos"][$value["Novedad"]]["Quantity"] += $value["Quantity"];
                $totalEventos += $value["Quantity"];
            }

            //Calculate events percentage
            foreach ($json["eventos"] as $key => $value) {
                
                $json["eventos"][$key]["Percentage"] = ($value["Quantity"] * 100) / $totalEventos;

            }

            //Select "Proceso Entreda"
            $queryProcesoEntrega = "
                    SELECT 
                        COUNT(a.num_despac) AS `Registradas`, 
                        COUNT(
                            IF(b.fec_cumdes IS NOT NULL, 1, NULL)
                        ) AS `Ejecutadas`, 
                        COUNT(
                            IF(b.fec_cumdes IS NULL, 1, NULL)
                        ) AS `Pendientes`
                    FROM 
                        ".BASE_DATOS.".tab_despac_despac a 
                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige aa ON a.num_despac = aa.num_despac
                        INNER JOIN ".BASE_DATOS.".tab_despac_destin b ON a.num_despac = b.num_despac 
                    WHERE 
                        a.ind_planru = 'S' 
                        AND a.ind_anulad IN ('R') 
                        ".$filtrans2."
                        AND a.fec_salida IS NOT NULL 
                        AND a.fec_salida <= NOW() 
                        AND a.fec_llegad IS NULL 
                        AND aa.ind_activo = 'S'
                ";

            //Execute query 
            $queryProcesoEntrega = new Consulta($queryProcesoEntrega, self::$conexion);
            $procesoEntregaRows = $queryProcesoEntrega -> ret_matrix('a');

            foreach ($procesoEntregaRows[0] as $key => $value) {
                
                $json["graphics"][$key]["Quantity"] = $value;
                $json["graphics"][$key]["Percentage"] = (100 * $value) / $procesoEntregaRows[0]["Registradas"];

            }

            //Select "Despachos sin ruta"
            $queryVehiculosSinPlanDeRuta = "
                SELECT 
                    COUNT(*) AS `Cantidad despachos sin ruta`
                FROM 
                    ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                WHERE 
                    a.ind_planru = 'N' 
                    AND a.ind_anulad IN ('R') 
                    ".$filtransp."
                    AND a.fec_salida IS NULL 
                    AND a.fec_llegad IS NULL 
                    -- AND b.ind_activo = 'S'
                ";

            
                $queryVehiculosSinPlanDeRuta = "
                SELECT 
                       COUNT(*) AS `Cantidad despachos sin ruta`
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_tercer_tercer c,
                       ".BASE_DATOS.".tab_tercer_tercer d
                 WHERE a.num_despac = b.num_despac AND
                       b.cod_transp = c.cod_tercer AND
                       b.cod_conduc = d.cod_tercer AND
                       a.ind_anulad != 'A' AND
                       a.ind_planru = 'N'
                       AND DATE( a.fec_despac )
                        BETWEEN DATE_SUB( CURDATE( ) , INTERVAL 3 MONTH )
                        AND CURDATE( )
               ";

            //Execute query 
            $queryVehiculosSinPlanDeRuta = new Consulta($queryVehiculosSinPlanDeRuta, self::$conexion);
            $vehiculosSinPlanDeRuta = $queryVehiculosSinPlanDeRuta -> ret_matrix('a');

            $json["stats"]["Despachos sin ruta"] = $vehiculosSinPlanDeRuta[0]["Cantidad despachos sin ruta"];

            echo json_encode(self::cleanArray($json));
        }

        function exportExcel(){

            $mResul = $_SESSION["queryXLS"];   

            $table = "";
            $table .='<table id="exportData"><tr>';
            
            foreach ($mResul as $key => $value) {
                //Genera Titulos
                if($key == 0){
                    foreach ($value as $titulo => $valor) {
                        $table .='<th>'.$titulo.'</th>';
                    }
                    $table .='</tr>';
                }
                $table .='<tr>';
                //Genera Colimnas
                foreach ($value as $valores) {
                    $table .='<td>'.$valores.'</td>';
                }
                $table .='</tr>';
            }
            
            $table .= "</table>";

            $archivo = $_REQUEST['name']."_".date("Y_m_d").".xls";

            header('Content-Type: application/octetstream');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="'.$archivo.'"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo $table;
        }

        function cleanArray($array){

            $arrayReturn = array();

            //Convert function
            $convert = function($value){
                if(is_string($value)){
                    return utf8_encode($value);
                }

                return $value;
            };

            //Go through data
            foreach ($array as $key => $value) {
                
                //Validate sub array
                if(is_array($value)){
                    
                    //Clean sub array
                    $arrayReturn[$convert($key)] = self::cleanArray($value);

                }else{
                    
                    //Clean value
                    $arrayReturn[$convert($key)] = $convert($value);

                }

            }

            //Return array
            return $arrayReturn;

        }
        /*! \fn: getTranspCargaControlador
        *  \brief: trae las transportadoras asignadas como carga laboral de un controlador o eal
        *  \author: Ing. Fabian Salinas
        *  \date: 21/09/2016
        *  \date modified: dd/mm/aaaa
        *  \param: 
        *  \return: string
        */
        private function getTranspCargaControlador() {
            if( self::$cTypeUser[tip_perfil] == 'CONTROL' || self::$cTypeUser[tip_perfil] == 'EAL'|| self::$cTypeUser[tip_perfil] == 'OAL' ) {
                $mSql = "SELECT GROUP_CONCAT(a.cod_transp SEPARATOR ',') AS lis_transp
                           FROM ".BASE_DATOS.".vis_monito_encdet a
                          WHERE a.cod_usuari = '".$_SESSION[datos_usuario][cod_usuari]."' ";
                $mConsult = new Consulta( $mSql, self::$conexion );
                $mResult = $mConsult -> ret_arreglo();
                return $mResult[0];
            } else {
                return null;
            }
        }

        public function typeUser()
        {
            $mPerfil = $_SESSION[datos_usuario][cod_perfil];
            $mResult = array();

            if( $mPerfil == '7' || $mPerfil == '713' )
                $mResult[tip_perfil] = 'CONTROL'; #Perfil Controlador
            elseif( $mPerfil == '70' || $mPerfil == '80' || $mPerfil == '669' )
                $mResult[tip_perfil] = 'EAL'; #Perfil EAL
            else{
                    $mResult[tip_perfil] = 'OTRO'; 
            }

            return $mResult;
        }

    }

    new FilterData();
    
?>
<?php
    class FilterData
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();

        function __construct($co = null, $us = null, $ca = null)
        {

            //Include Connection class
            @include( "../lib/ajax.inc" );
            @include_once('../lib/general/functions.inc');
            //Config display errors true
            ini_set('display_errors', true);
            error_reporting(E_ALL & ~E_NOTICE);

            //Assign values
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Validate fec_inicio and fec_finxxx
            if(empty($_REQUEST["fec_inicio"]) || empty($_REQUEST["fec_finxxx"])){
                $_REQUEST["fec_inicio"] = date("Y-m-d", strtotime("-30 day", time()));
                $_REQUEST["fec_finxxx"] = date("Y-m-d", time());
            }

            //Create dates array
            $fec_inicio = $_REQUEST["fec_inicio"];
            while(strtotime($fec_inicio) <= strtotime($_REQUEST["fec_finxxx"])){

                self::$dates[$fec_inicio] = [$fec_inicio, 0];
                $fec_inicio = date("Y-m-d", strtotime("+1 day", strtotime($fec_inicio)));
            }

            //Switch request options
            switch($_REQUEST[opcion])
            {
                case "1":
                    self::desReaPorTipOpeGraphic();

                    break;

                case "2":
                    self::desFinGraphic();

                    break;

                case "3":
                    self::infUsoVehPorPlaFinGraphic();

                    break;

                case "4":
                    self::citCarGraphic();

                    break;

                case "5":
                    self::citDesGraphic();

                    break;

                case "6":
                    self::itiGraphic();

                    break;

                case "7":
                    self::topEveGraphic();

                    break;

                case "8":
                    self::vehiPenLlegGraphic();

                    break;

                default:

                break;
            }
        }

        function desReaPorTipOpeGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Create query "Despachos realizados por tipo de Operación"
            $query = "
                SELECT
                    c.nom_tipdes AS `nombre`,
                    DATE(a.fec_llegad) AS `fecha`,
                    COUNT(1) AS `cantidad`
                FROM
                    ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN
                        ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                    INNER JOIN
                        ".BASE_DATOS.".tab_genera_tipdes c ON a.cod_tipdes = c.cod_tipdes
                    LEFT JOIN
                        ".BASE_DATOS.".tab_despac_corona n ON a.num_despac = n.num_dessat 
                WHERE
                    a.fec_salida IS NOT NULL
                    AND a.fec_llegad IS NOT NULL
                    AND a.fec_llegad != '0000-00-00 00:00:00'
                    AND a.ind_planru = 'S'
                    AND a.ind_anulad in ('R')
                    AND b.ind_activo = 'S'
                    '".$filtransp."'
                    AND n.num_despac IS NOT NULL
                    AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                GROUP BY
                    a.cod_tipdes,
                    a.fec_llegad
            ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["fecha"]] = [$value["fecha"], $value["cantidad"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();
                $tempData["name"] = $value["name"];

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

        function desFinGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Create query "Despachos finalizados"
            $query = "
                SELECT
                    c.nom_tipdes AS `nombre`,
                    COUNT(1) AS `cantidad`
                FROM
                    ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN
                        ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                    INNER JOIN
                        ".BASE_DATOS.".tab_genera_tipdes c ON a.cod_tipdes = c.cod_tipdes
                    LEFT JOIN
                        ".BASE_DATOS.".tab_despac_corona n ON a.num_despac = n.num_dessat 
                WHERE
                    a.fec_salida IS NOT NULL
                    AND a.fec_llegad IS NOT NULL
                    AND a.fec_llegad != '0000-00-00 00:00:00'
                    AND a.ind_planru = 'S'
                    AND a.ind_anulad in ('R')
                    AND b.ind_activo = 'S'
                    ".$filtransp."
                    AND n.num_despac IS NOT NULL
                    AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                GROUP BY
                    a.cod_tipdes
            ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $json = array();

            foreach ($despachos as $key => $value)
            {
                $json[0]["data"][$key]["name"] = $value["nombre"];
                $json[0]["data"][$key]["value"] = $value["cantidad"];
            }

            echo json_encode(self::cleanArray($json));
        }

        function vehiPenLlegGraphic()
        {
            //Create query "Vehiculos Pendientes por llegada"
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            $query = "
                SELECT
                    c.nom_tipdes AS `nombre`,
                    COUNT(1) AS `cantidad`
                FROM
                    ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN
                        ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                    INNER JOIN
                        ".BASE_DATOS.".tab_genera_tipdes c ON a.cod_tipdes = c.cod_tipdes
                    LEFT JOIN
                        ".BASE_DATOS.".tab_despac_corona n ON a.num_despac = n.num_dessat 
                WHERE
                    a.fec_salida IS NOT NULL
                    AND a.fec_llegad IS NULL
                    AND a.ind_planru = 'S'
                    AND a.ind_anulad in ('R')
                    AND b.ind_activo = 'S'
                    ".$filtransp."
                    AND n.num_despac IS NOT NULL
                    AND a.cod_conult = 9999
                    AND DATE(a.fec_salida) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                GROUP BY
                    a.cod_tipdes
            ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $json = array();

            foreach ($despachos as $key => $value)
            {
                $json[0]["data"][$key]["name"] = $value["nombre"];
                $json[0]["data"][$key]["value"] = $value["cantidad"];
            }

            echo json_encode(self::cleanArray($json));
        }

        function infUsoVehPorPlaFinGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Select "Informe uso de vehï¿½culos por planta"
            $query = "
                SELECT 
                    IF(
                        c.tip_transp = '1',
                        'Propios',
                        IF(
                            c.tip_transp = '3',
                            'Empresas',
                            'Terceros'
                        )
                    ) AS `stats`,
                    d.nom_remdes AS `Planta`
                FROM 
                    ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                    INNER JOIN ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat
                    INNER JOIN ".BASE_DATOS.".tab_genera_remdes d ON c.nom_sitcar = d.cod_remdes
                WHERE 
                    a.ind_planru = 'S' 
                    AND a.ind_anulad IN ('R') 
                    ".$filtransp."
                    AND a.fec_salida IS NOT NULL 
                    AND a.fec_llegad IS NOT NULL 
                    AND a.fec_llegad != '0000-00-00 00:00:00'
                    AND b.ind_activo = 'S'
                    AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $graphic = [];
            $json["stats"]["Propios"] = 0;
            $json["stats"]["Terceros"] = 0;
            $json["stats"]["Empresas"] = 0;
            $json["stats"]["Total Vehiculos"] = 0;
            $json["graphic"] = [];
            $json["xData"] = [];

            //Go through query result
            foreach ($despachos as $key => $value)
            {
                //Increment stats
                $json["stats"][$value["stats"]] ++;
                $json["stats"]["Total Vehiculos"]++;

                //Add xData element
                if(!in_array($value["Planta"], $json["xData"]))
                    $json["xData"][] = $value["Planta"];

                //Create graphic position
                if(!isset($graphic[$value["stat"]])){
                    $graphic[$value["stat"]]["name"] = $value["stat"];
                    $graphic[$value["stat"]]["data"] = [];
                }

                $graphic[$value["stat"]]["data"][array_search($value["Planta"], $json["xData"])] ++;

            }

            foreach ($graphic as $key => $value) {
                $json["graphic"][] = $value;
            }

            echo json_encode(self::cleanArray($json));
        }

        function citCarGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Create query "Citas de cargue"
            $query = "
                    SELECT
                        *,
                        COUNT(1) AS `Conteo`
                    FROM
                        (
                            SELECT
                                    @fechaCitaCargue := CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS `Programada`,
                                    d.fec_cumcar AS `Ejecutada`,
                                    IF(
                                        d.fec_cumcar IS NOT NULL,
                                        IF(
                                            d.fec_cumcar > @fechaCitaCargue,
                                            'No Cumplidas',
                                            'Cumplidas'
                                        ),
                                        'No Cumplidas'
                                    ) AS `Cumplimiento`
                                    
                            FROM  ".BASE_DATOS.".tab_despac_despac a 
                        INNER JOIN  ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac  
                        INNER JOIN  ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat 
                        INNER JOIN  ".BASE_DATOS.".tab_despac_sisext d ON a.num_despac = d.num_despac
                            WHERE  a.ind_planru = 'S' 
                                AND a.ind_anulad in ('R') 
                                ".$filtransp." 
                                AND a.fec_salida IS NOT NULL 
                                AND a.fec_llegad IS NOT NULL
                                AND a.fec_llegad != '0000-00-00 00:00:00'
                                AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                        )sub
                    GROUP BY `Cumplimiento`
                ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $json["Total"] = ["Percentage" => 100, "Quantity" => 0];
            $json["Cumplidas"] = ["Percentage" => 0, "Quantity" => 0];
            $json["No Cumplidas"] = ["Percentage" => 0, "Quantity" => 0];

            foreach ($despachos as $key => $value)
            {
                $json[$value["Cumplimiento"]]["Quantity"] = $value["Conteo"];
                $json["Total"]["Quantity"] += $value["Conteo"];
            }
            
            //Calculate percentages
            if($json["Total"]["Quantity"] > 0)
            {
                foreach ($json as $key => $value)
                {
                    if($key != "Total")
                    {
                        $json[$key]["Percentage"] = ($json[$key]["Quantity"] * 100) / $json["Total"]["Quantity"];
                    }
                }
            }

            echo json_encode(self::cleanArray($json));
        }

        function citDesGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Create query "Citas de descargue"
            $query = "
                        SELECT
                                @fechaCitaDesCargue := CONCAT(d.fec_citdes, ' ', d.hor_citdes) AS `Programada`,
                                d.fec_cumdes AS `Ejecutada`,
                                IF(
                                    d.fec_cumdes IS NOT NULL,
                                    IF(
                                        d.fec_cumdes > @fechaCitaDesCargue,
                                        'No Cumplidas',
                                        'Cumplidas'
                                    ),
                                    'No Cumplidas'
                                ) AS `Cumplimiento`,
                                COUNT(a.num_despac) AS `Conteo`
                          FROM  ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN  ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac  
                    INNER JOIN  ".BASE_DATOS.".tab_despac_destin d ON a.num_despac = d.num_despac 
                         WHERE  a.ind_planru = 'S' 
                            AND a.ind_anulad in ('R') 
                            ".$filtransp."
                            AND a.fec_salida IS NOT NULL 
                            AND a.fec_llegad IS NOT NULL
                            AND a.fec_llegad != '0000-00-00 00:00:00'
                            AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                       GROUP BY `Cumplimiento`
                ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $json["Total"] = ["Percentage" => 100, "Quantity" => 0];
            $json["Cumplidas"] = ["Percentage" => 0, "Quantity" => 0];
            $json["No Cumplidas"] = ["Percentage" => 0, "Quantity" => 0];

            foreach ($despachos as $key => $value)
            {
                $json[$value["Cumplimiento"]]["Quantity"] = $value["Conteo"];
                $json["Total"]["Quantity"] += $value["Conteo"];
            }

            //Calculate percentages
            if($json["Total"]["Quantity"] > 0)
            {
                foreach ($json as $key => $value)
                {
                    if($key != "Total")
                    {
                        $json[$key]["Percentage"] = ($json[$key]["Quantity"] * 100) / $json["Total"]["Quantity"];
                    }
                }
            }

            echo json_encode(self::cleanArray($json));
        }

        function itiGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Select "Itinerario"
            $query = "
                SELECT 
                    IF(b.cod_itiner IS NOT NULL AND b.cod_itiner != 0, 'Con Itinerario', 'Sin Itinerario') AS `Itinerario`,
                    COUNT(1) AS `Conteo`
                FROM 
                    ".BASE_DATOS.".tab_despac_despac a 
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                WHERE 
                    a.ind_planru = 'S' 
                    AND a.ind_anulad IN ('R') 
                    ".$filtransp."
                    AND a.fec_salida IS NOT NULL 
                    AND a.fec_llegad IS NOT NULL 
                    AND a.fec_llegad != '0000-00-00 00:00:00'
                    AND b.ind_activo = 'S'
                    AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                GROUP BY
                    `Itinerario`
                ";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $json["Total Vehiculos"] = ["Percentage" => 100, "Quantity" => 0];
            $json["Con Itinerario"] = ["Percentage" => 0, "Quantity" => 0];
            $json["Sin Itinerario"] = ["Percentage" => 0, "Quantity" => 0];

            foreach ($despachos as $key => $value)
            {
                $json[$value["Itinerario"]]["Quantity"] = $value["Conteo"];
                $json["Total Vehiculos"]["Quantity"] += $value["Conteo"];
            }

            //Calculate percentages
            if($json["Total Vehiculos"]["Quantity"] > 0)
            {
                foreach ($json as $key => $value)
                {
                    if($key != "Total Vehiculos")
                    {
                        $json[$key]["Percentage"] = ($json[$key]["Quantity"] * 100) / $json["Total Vehiculos"]["Quantity"];
                    }
                }
            }

            echo json_encode(self::cleanArray($json));
            
        }

        function topEveGraphic()
        {
            $transp = getTranspPerfil(self::$conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
            //Select "Eventos"
            $query = "
                SELECT 
                    e.nom_noveda AS `nombre`,
                    DATE(a.fec_llegad) AS `fecha`,
                    COUNT(1) AS `cantidad`
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
                            AND a.fec_llegad IS NOT NULL
                            AND a.fec_llegad != '0000-00-00 00:00:00'
                            AND b.ind_activo = 'S'
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
                            AND b.cod_transp = '".NIT_TRANSPOR."' 
                            AND a.fec_salida IS NOT NULL 
                            AND a.fec_llegad IS NOT NULL
                            AND a.fec_llegad != '0000-00-00 00:00:00'
                            AND b.ind_activo = 'S'
                    ) e ON a.num_despac = e.num_despac 
                WHERE 
                    a.ind_planru = 'S' 
                    AND a.ind_anulad IN ('R') 
                    AND b.cod_transp = '".NIT_TRANSPOR."' 
                    AND a.fec_salida IS NOT NULL 
                    AND a.fec_llegad IS NOT NULL
                    AND a.fec_llegad != '0000-00-00 00:00:00'
                    AND b.ind_activo = 'S'
                    AND DATE(a.fec_llegad) BETWEEN '" . $_REQUEST["fec_inicio"] . "' AND '" . $_REQUEST["fec_finxxx"] . "'
                GROUP BY
                    `nombre`,
                    `fecha`
                ORDER BY
                    `cantidad` DESC
                LIMIT 10
                ";

            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');

            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["fecha"]] = [$value["fecha"], $value["cantidad"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();
                $tempData["name"] = $value["name"];

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

        function cleanArray($array){

            $arrayReturn = array();

            //Go through data
            foreach ($array as $key => $value) {
                
                //Validate sub array
                if(is_array($value)){
                    
                    //Clean sub array
                    $arrayReturn[utf8_encode($key)] = self::cleanArray($value);

                }else{
                    
                    //Clean value
                    $arrayReturn[utf8_encode($key)] = utf8_encode($value);

                }

            }

            //Return array
            return $arrayReturn;

        }
    }

    new FilterData();
    
?>
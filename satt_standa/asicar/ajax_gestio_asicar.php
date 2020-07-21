<?php
    /****************************************************************************
    NOMBRE:   AjaxGestioAsiscar
    FUNCION:  Retorna todos los datos necesarios para construir la información
    FECHA DE MODIFICACION: 13/04/2020
    CREADO POR: Ing. Cristian Andrés Torres
    MODIFICADO 
    ****************************************************************************/
    
    /*ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1);*/

    class AjaxGestioAsiscar
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

            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST[opcion])
            {
                case "1":
                    self::informes();
                break;

                case "2":
                    self::loadFields();
                break;
            }
        }

        /*! \fn: informes
           *  \brief: Genera la informaci�n para las tablas de los informes
           *  \author: Ing. Cristian Torres
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */

        function informes(){

            switch ($_REQUEST['tipoInforme']) {
                case 'gen':
                    $json = self::informeGeneral();
                    break;
                case 'esp':
                    $json = self::informeEspecifico();
                    break;
                case 'mod':
                    $json = self::informeModal();
                    break;
                default:
                    $json = self::informeGeneral();
                    break;
            }
            
            echo $json;
        }


        /*! \fn: informeGeneral
           *  \brief: Genera la información para las tabla del informe general
           *  \author: Ing. Cristian Torres
           *  \date: 04-06-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */
        function informeGeneral(){
            //Create total query 
            $query = "SELECT COUNT(*) as 'total' FROM ".BASE_DATOS.".tab_asiste_carret a WHERE 1=1";

            if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                $query .= "
                   AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'
                   GROUP BY(a.est_solici) ORDER BY a.est_solici ASC
                   ";
            }
            //Generate consult

            $query = new Consulta($query, self::$conexion);
            $datos = $query -> ret_matrix('a');

            //Valriable para capturar el valor anterior
            $valorAnterior = '';

            //Recorre consulta para asignar valores
            foreach ($despachos as $ident => $data) {
                foreach ($data as $campo => $valor) {
                    //Identifica si el campo es vacio
                    if ($valor == '') {
                        //Asigna valor para calcular el porcentaje
                        $despachos[$ident][$campo] = round($valorAnterior / reset($data)*100);
                    }
                    $valorAnterior = $valor;
                }
            }

            $despachos = self::cleanArray($despachos);
            $json = json_encode($despachos);

            $_SESSION["dashboard"][1]["table"] = $json;
            $_SESSION["dashboard"][1]["filter"] = json_encode(self::cleanArray($_REQUEST));
            // header('Content-Type: application/json');
            return $json;
        }

        /*! \fn: informeEspecifico
           *  \brief: Genera la informaci�n para las tabla del informe especifico por dia segun los filtros
           *  \author: Ing. Cristian Torres
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */
        function informeEspecifico(){

                $query="SELECT 
                    COUNT(*) as 'reg_genera', 
                    IFNULL(sum(IFNULL(d.num_estbue,0)+IFNULL(d.num_estmal,0)),0) as 'est_retorn', 
                    IFNULL (ROUND(sum(IFNULL(d.num_estbue,0)+IFNULL(d.num_estmal,0)) * 100 / sum(d.num_estsal),0),0) as 'por_est_retorn', 
                    sum(d.num_estsal) as 'est_entreg', 
                    IFNULL(ROUND(sum(d.num_estsal) * 100 / sum(d.num_estsal),0),0) as 'por_est_entreg', 
                    IFNULL(sum(d.num_estsal - (IFNULL(d.num_estbue,0)+IFNULL(d.num_estmal,0))),0) as 'est_pendie', 
                    IFNULL(ROUND(sum(d.num_estsal - (IFNULL(d.num_estbue,0)+IFNULL(d.num_estmal,0))) * 100 / sum(d.num_estsal),0),0) as 'por_est_penret',
                    CAST(a.fec_despac AS DATE) as 'fec_despac'
                    FROM ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN ".BASE_DATOS.".tab_despac_vehige c ON a.num_despac = c.num_despac
                    INNER JOIN ".BASE_DATOS.".tab_despac_destin d ON a.num_despac = d.num_despac
                    ";

                if($_REQUEST["cod_tercer"] != ""){
                    $query .= "
                       AND c.cod_transp = '".$_REQUEST["cod_tercer"]."'";
                }
    
                if($_REQUEST["num_remdes"] != ""){
                    $query .= "
                       AND a.cod_client = '".$_REQUEST["num_remdes"]."'";
                }

                if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                    $query .= "
                       AND DATE(a.fec_despac) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
                }

                $query.="GROUP BY CAST(a.fec_despac AS DATE)";

                $query = new Consulta($query, self::$conexion);
                $despachos = $query -> ret_matrix('a');

            $despachos = self::cleanArray($despachos);
            $json = json_encode($despachos);

            $_SESSION["dashboard"][1]["table"] = $json;
            $_SESSION["dashboard"][1]["filter"] = json_encode(self::cleanArray($_REQUEST));

            return $json;
        }

        /*! \fn: informeModal
           *  \brief: Genera la informaci�n para las tabla del informe en la ventana modal
           *  \author: Ing. Cristian Andres Torres
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */
        function informeModal(){
            $ver=$_REQUEST["ver"];

            //Verificador de Finalizados Si es igual a 1 el sistema filtra los despachos finalizados y los retorna
            $tip=$_REQUEST["tip"];

                $query="SELECT 
                a.num_despac,
                a.cod_manifi,
                IFNULL(CONCAT(b.num_pedido,'-',b.num_lineax),'-') as 'num_pedido',
                IFNULL((SELECT ciu.nom_ciudad FROM tab_genera_ciudad ciu WHERE ciu.cod_ciudad=a.cod_ciuori),'-') as 'ciu_origen',
                IFNULL((SELECT ciu.nom_ciudad FROM tab_genera_ciudad ciu WHERE ciu.cod_ciudad=a.cod_ciudes),'-') as 'ciu_destin',
                c.num_placax,
                IFNULL(CONCAT(d.nom_apell1,' ',d.nom_apell2,' ',d.nom_tercer),'-') as 'nom_conduc',
                IFNULL((SELECT ter.nom_tercer FROM tab_tercer_tercer ter WHERE ter.cod_tercer=c.cod_transp),'-') as 'nom_transp',
                IFNULL((SELECT cli.nom_remdes FROM tab_genera_remdes cli WHERE cli.num_remdes=a.cod_client LIMIT 1),'-') as 'nom_client',
                IF(c.cod_agenci=1,'Despacho Normal','Cliente Retira') as 'tip_pedido',
                 IFNULL(e.nom_produc,'-') as 'nom_produc',
                 IFNULL(b.can_pedida,0) as 'can_pedida',
                 IFNULL(b.pes_cantid_pedida,0) as 'pes_pedida',
                 f.num_estsal as 'est_salida',
                 
                 IFNULL((IFNULL(f.num_estbue,0) + IFNULL(f.num_estmal,0)),0) as 'est_retorn',
                 
                 
                 f.num_estsal-IFNULL((IFNULL(f.num_estbue,0) + IFNULL(f.num_estmal,0)),0) as 'tot_saldox',
                 
                 IFNULL(f.num_estbue,0) as 'num_estbue',
                 IFNULL(f.num_estmal,0) as 'num_estmal'
                    
                          FROM ".BASE_DATOS.".tab_despac_despac a
                          INNER JOIN ".BASE_DATOS.".tab_despac_vehige c ON a.num_despac = c.num_despac
                          LEFT JOIN ".BASE_DATOS.".tab_genera_pedido b ON a.num_despac = b.num_despac
                          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer d ON c.cod_conduc = d.cod_tercer
                          INNER JOIN ".BASE_DATOS.".tab_genera_produc e ON e.cod_produc = b.cod_articu
                          INNER JOIN ".BASE_DATOS.".tab_despac_destin f ON a.num_despac = f.num_despac
                    ";

                if($_REQUEST["cod_tercer"] != ""){
                    $query .= "
                       AND c.cod_transp = '".$_REQUEST["cod_tercer"]."'";
                }
    
                if($_REQUEST["num_remdes"] != ""){
                    $query .= "
                       AND a.cod_client = '".$_REQUEST["cli"]."'";
                }

                if($ver==1){
                    if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                        $query .= "
                           AND DATE(a.fec_despac) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
                    }

                    if($_REQUEST["cli"]!=0 || $_REQUEST["cli"]!=''){
                        $query .= "
                        AND a.cod_client = '".$_REQUEST["cli"]."'";
                    }

                }else{
                    $fec=$_REQUEST["fec"];
                    $query .= "
                           AND DATE(a.fec_despac) BETWEEN '".$fec."' AND '".$fec."'";
                }

                //Filtro para despachos finalizados
                if($tip==1){
                    $query .= "
                    AND a.fec_llegad IS NOT NULL ";
                }
                

                $query = new Consulta($query, self::$conexion);
                $despachos = $query -> ret_matrix('a');
           

            //Generate consult

            $despachos = self::cleanArray($despachos);
            $json = json_encode($despachos);

            $_SESSION["dashboard"][1]["table"] = $json;
            $_SESSION["dashboard"][1]["filter"] = json_encode(self::cleanArray($_REQUEST));
            // header('Content-Type: application/json');
            return $json;
        }

        /*! \fn: loadFields
           *  \brief: Genera los campos necesarios en el modulo
           *  \author: Ing. Luis Manrique
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */

        function loadFields(){

            //Create necessary variables
            $json = array();

            //Create consult cod_tercer
            $mData = self::sqlSelect(
                                      "a.cod_tercer AS id, a.nom_tercer AS value",
                                      "tab_tercer_tercer a INNER JOIN tab_tercer_activi b ON b.cod_tercer = a.cod_tercer",
                                      "a.cod_estado = 1 AND b.cod_activi = 1 
                                    ");

            //Create format json
            $json["cod_tercer"] = array(
                "name" => "Transportadora",
                "type" => "select",
                "container" => "#filtrosEspecificos #oneSelect",
                "rowContainer" => "td",
                "elementContainer" => "td",
                "rowMaxQuantity" => 6,
                "data" => $mData
            );

            //Create consult num_remdes
            $mData = self::sqlSelect("num_remdes AS id, nom_remdes AS value","tab_genera_remdes","ind_estado = 1 AND ind_remdes = 2");

            //Create format json
            $json["num_remdes"] = array(
                "name" => "Clientes",
                "type" => "select",
                "container" => "#filtrosEspecificos #oneSelect",
                "rowContainer" => "td",
                "elementContainer" => "td",
                "rowMaxQuantity" => 6,
                "data" => $mData
            );

            $json = json_encode($json);

            echo $json;
        }

        /*! \fn: sqlSelect
           *  \brief: Genera los datos que se solicitan en la consulta
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $fields => campos de la consulta
           *          $table => tabla de la consulta
           *          $where => condición de la consulta
           *  \return: array
        */

        function sqlSelect($fields,$table,$where){
            $query = " 
                SELECT $fields
                  FROM ".BASE_DATOS.".$table
                 WHERE $where
            ";

            //Generate consult
            $query = new Consulta($query, self::$conexion);
            $mData = $query -> ret_matrix('a');

            //Clean array
            $mData = self::cleanArray($mData);

            return $mData;
        }


        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificación
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que será analizado por la función
           *  \return: array
        */
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
    }

    new AjaxGestioAsiscar();
    
?>
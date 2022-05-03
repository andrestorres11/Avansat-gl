<?php
/****************************************************************************
 NOMBRE:   INF_BANDEJ_SEGUIM.PHP
FUNCION:  Brindar seguimiento a vehiculos y despachos
****************************************************************************/
include_once( __DIR__."/../../lib/bd/seguridad/aplica_filtro_usuari_lib.inc" );
include_once( __DIR__."/../../lib/bd/seguridad/aplica_filtro_perfil_lib.inc" );

class BandejaSegumiento
{
    var $conexion, $usuario, $cod_aplica;//una conexion ya establecida a la base de datos
    function __construct() 
    {
        
        chdir(__DIR__."/../");
        include("../lib/ajax.inc");
        @include_once('../lib/general/functions.inc');
        $this->conexion    = $AjaxConnection;
        $this->usuario     = $_SESSION['datos_usuario'];
        $this->cod_aplica  = $_SESSION['datos_usuario']['cod_aplica'];
        $this -> principal();
    }

    function principal()
    {

        if(!isset($_REQUEST['opcion'])){
            $this->mostrarFormularioInicial();
            return;
        }

        switch ($_REQUEST['opcion']) {
            case 'get_despachos':
                $this->obtenerDespachos();
            break;

            case 'get_recorrido':
                $this->obtenerRecorridoFormatoJson($_REQUEST['num_despac']);
            break;

            case 'get_ciudades':
                $m[] = ['select', 'destinoID', null, $this->getCiudades("onchange")];
                new Xml($m);
            break;

            default:
            $this->mostrarFormularioInicial();        
            break;
        }

    }//FIN FUNCION PRINCIPAL

    function mostrarFormularioInicial(){
        ini_set("memory_limit", "128M");

        
        $coordenadaToCentrarMap=json_encode(["lat" => 5.211306,"lon" => -74.761963, "fecha" => "2018-07-14", "observacion" => utf8_encode("centrado")]);
        $ciudades = $this->getCiudades();

        require_once("html_form_map.php");

    }//FIN FUNCTION CAPTURA


    public function obtenerDespachos()
    {

        //PENDIETE revisar como desacoplar
        if(isset($_REQUEST['manifiesto']) && $_REQUEST['manifiesto']!=""){
            $aditionalFilters = " AND a.cod_manifi='".$_REQUEST['manifiesto']."'";
        }

        $aditionalFilters .= " GROUP BY 1";
        //adiconal del los filtros que se describen aca tambien utiliza unos filtros directo en la fucion
        //obtenidos desde la var _REQUEST PENDIENTE pasarlos por parametro
        $result = $this->consultarMaster($whatYouWant="", $aditionalFilters);


        //Clean data
        foreach ($result as $key => $value) {
            
            foreach ($value as $key1 => $value1) {
                $result[$key][$key1] = utf8_encode($value1);
            }

        }

        $temp = $result;

        /*print_r($temp);
        echo json_encode($temp);*/

        for ($i=0; $i < (count($temp)); $i++) { 
            unset($temp[$i]['gps_operad']);
            unset($temp[$i]['gps_usuari']);
            unset($temp[$i]['gps_paswor']);
            unset($temp[$i]['gps_idxxxx']);

            $temp[$i]['color']=dechex(rand(0x000000, 0xFFFFFF));
            $temp[$i]['observacion'] = $temp[$i]['observacion'];//se usa rawurlencode pq el javascript no decodifico el formato urlencode PENDIENTE revisar
            $temp[$i]['novedad'] = $temp[$i]['novedad'];//se usa rawurlencode pq el javascript no decodifico el formato urlencode PENDIENTE revisar
            $temp[$i]['fecha'] = $temp[$i]['fecha'];

            $check = '<input type="checkbox" name="despacho" id="despacho_checkbox_'.$i.'" onclick=buildDrawMap(this); value=\''.json_encode($temp[$i]).'\'>';
            
            echo "<tr>
                    <td>".$check."</td>
                    <td>".$temp[$i]['placa']."</td>
                    <td><a href=\"index.php?cod_servic=3302&window=central&despac=".$temp[$i]['num_despac']."&opcion=1\" target=\"centralFrame\"><i class='far fa-eye' style=' font-size: large;'></i></a></td>
                    <td><div style='width: 15px; height: 15px; background: #".$temp[$i]['color']."; border: 1px; border-style: solid;'></div> </td>
                  </tr>";
        }
    }

    public function obtenerRecorridoFormatoJson($numDespac)
    {
        $aditionalFilters="";
        //PENDIETE revisar como desacoplar
        if(isset($_REQUEST['manifiesto']) && $_REQUEST['manifiesto']!=""){
            $aditionalFilters = " AND a.cod_manifi='".$_REQUEST['manifiesto']."'";
        }

        $aditionalFilters .= " AND a.num_despac=".$numDespac;

        echo json_encode($this->consultarRecorrido($aditionalFilters));

    }

    /**
    * Devuelve las ciudades para los select del front
    *
    * @param string $from posiles valores initial=cuando se carga por primera vez el form, onchange=cuando se piden las ciudades filtradas
    */
    public function getCiudades($from="initial")
    {

        if($from == "onchange"){
            return $this->consultarMaster("ciudades_destino", "GROUP BY a.cod_ciudes, h.nom_ciudad", "i");    
        }

        $result = $this->consultarMaster("ciudades");

        $data['origen'][] = array('','Seleccione Origen');
        $data['destino'][] = array('','Seleccione Destino');

        $origenes = array();
        $destinos = array();

        foreach ($result as $value) {


            //seccion para guardar solo registros unicos en el array
            if(!isset($origenes[$value['cod_ciuori']])){
                $data['origen'][]=[$value['cod_ciuori'],utf8_decode($value['nom_ciuori'])];
                $origenes[$value['cod_ciuori']]="";
            }

            //seccion para guardar solo registros unicos en el array
            if(!isset($destinos[$value['cod_ciudes']])){
                $data['destino'][]=[$value['cod_ciudes'],utf8_decode($value['nom_ciudes'])];
                $destinos[$value['cod_ciudes']]="";
            }

        }
        
        return $data;
    }

    public function consultarMaster($whatYouWant="", $aditionalFilters="", $typeRetunrMatriz="a")
    {

        $query = $this->buildQuery($whatYouWant );

        $query = $this->filtrosObligatorios($query);

        if($aditionalFilters != ""){
            $query .= $aditionalFilters;
        }

        if($whatYouWant != "ciudades"){
            $query .= " ORDER BY 1";
        }
        
        $consulta = new Consulta($query, $this->conexion);
        $result = $consulta->ret_matriz($typeRetunrMatriz);

        return $result;

    }

    public function buildQuery($whatYouWant )
    {
        //pendiente mejorar la construccion de los campos a mostrar
        $transp = getTranspPerfil($this->conexion,$_SESSION[datos_usuario][cod_perfil]);
            $filtransp = "";
            if(!empty($transp)){
                $filtransp = " AND b.cod_transp = '".$transp['cod_tercer']."' ";
            }
        $fieldsToShow = "a.num_despac, aa.num_placax AS placa, a.cod_manifi AS manifiesto, c.nom_noveda AS novedad, "
                        . "b.obs_contro AS observacion, b.val_latitu AS lat, b.val_longit AS lon , a.gps_operad, "
                        . "a.gps_usuari, a.gps_paswor, a.gps_idxxxx, b.fec_creaci AS fecha ";

        if($whatYouWant == "ciudades"){
            $fieldsToShow = "a.cod_ciuori, g.nom_ciudad AS 'nom_ciuori', a.cod_ciudes, h.nom_ciudad AS 'nom_ciudes'";
        }elseif($whatYouWant == "ciudades_destino"){
            $fieldsToShow = "a.cod_ciudes, h.nom_ciudad AS 'nom_ciudes'";
        }

        // query para buscar despacho en ruta que esten activos y muestra solo le ultimo registro que tenga latitud y longitud, es para ubucar el punto NO LA TRAZA
        $query = "SELECT {$fieldsToShow} FROM
        ".BASE_DATOS.".tab_despac_despac a  INNER JOIN 
        ".BASE_DATOS.".tab_despac_vehige aa ON  a.fec_salida IS NOT NULL AND 
            a.fec_llegad IS NULL AND 
            aa.ind_activo = 'S'
            ".($_REQUEST["origen"]  ? " AND a.cod_ciuori  = '".$_REQUEST["origen"]."'  " : "")."
            ".($_REQUEST["destino"] ? " AND a.cod_ciudes  = '".$_REQUEST["destino"]."'  " : "")."
            ".($_REQUEST["placa"]   ? " AND aa.num_placax = '".$_REQUEST["placa"]."'  " : "")."
            AND a.num_despac = aa.num_despac INNER JOIN 
        ".BASE_DATOS.".tab_despac_contro b ON  aa.num_despac = b.num_despac INNER JOIN 
        (
        SELECT
        a.num_despac, MAX(c.fec_creaci) AS fec_creaci
        FROM 
        tab_despac_despac a INNER JOIN
        tab_despac_vehige b ON a.num_despac = b.num_despac INNER JOIN
        tab_despac_contro c ON b.num_despac = c.num_despac
        WHERE
        a.fec_salida IS NOT NULL AND 
        a.fec_llegad IS NULL AND 
        b.ind_activo = 'S'
        ".$filtransp."
        GROUP BY a.num_despac
        ) ba ON ba.num_despac = b.num_despac AND ba.fec_creaci = b.fec_creaci INNER JOIN
        ".BASE_DATOS.".tab_genera_noveda c ON b.cod_noveda = c.cod_noveda INNER JOIN
        ".BASE_DATOS.".tab_genera_contro d ON b.cod_contro = d.cod_contro LEFT  JOIN 
        ".BD_STANDA .".tab_genera_opegps f ON a.gps_operad = f.cod_operad  LEFT  JOIN
        ".BASE_DATOS .".tab_genera_ciudad g ON a.cod_ciuori = g.cod_ciudad  LEFT  JOIN
        ".BASE_DATOS .".tab_genera_ciudad h ON a.cod_ciudes = h.cod_ciudad  

        WHERE
        b.val_latitu IS NOT NULL AND 
        b.val_longit IS NOT NULL ";

        return $query;
    }

    private function filtrosObligatorios($query)
    {
        $datos_usuario = $_SESSION["datos_usuario"];

        if ($datos_usuario["cod_perfil"] == "") {

            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND aa.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegra = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client IN ( $datos_filtro[clv_filtro] ) ";
            }

            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND aa.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }

        } else {

            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND aa.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegra = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client IN ( $datos_filtro[clv_filtro] ) ";
            }

            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND aa.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }

        }
        return $query;
    }

    public function consultarRecorrido($aditionalFilters="")
    {

        $query = "SELECT a.num_despac, a.cod_manifi AS manifiesto, c.nom_noveda AS novedad, b.obs_contro AS observacion, b.val_latitu AS lat, b.val_longit AS lon, b.fec_creaci AS fecha 
                    FROM ".BASE_DATOS.".tab_despac_despac a  INNER JOIN 
                        ".BASE_DATOS.".tab_despac_vehige aa ON a.fec_salida IS NOT NULL
                                                                AND a.fec_llegad IS NULL 
                                                                AND aa.ind_activo = 'S'
                                                                ".($_REQUEST["origen"]  ? " AND a.cod_ciuori  = '".$_REQUEST["origen"]."'  " : "")."
                                                                ".($_REQUEST["destino"] ? " AND a.cod_ciudes  = '".$_REQUEST["destino"]."'  " : "")."
                                                                ".($_REQUEST["placa"]   ? " AND aa.num_placax = '".$_REQUEST["placa"]."'  " : "")."
                                                                AND a.num_despac = aa.num_despac INNER JOIN
                        ".BASE_DATOS.".tab_despac_contro b ON aa.num_despac = b.num_despac INNER JOIN 
                        ".BASE_DATOS.".tab_genera_noveda c ON b.cod_noveda = c.cod_noveda INNER JOIN
                        ".BASE_DATOS.".tab_genera_contro d ON b.cod_contro = d.cod_contro LEFT  JOIN
                        
                        ".BD_STANDA .".tab_genera_opegps f ON a.gps_operad = f.cod_operad  
                    WHERE b.val_latitu IS NOT NULL AND 
                        b.val_longit IS NOT NULL ";

        $query = $this->filtrosObligatorios($query);

        if($aditionalFilters != ""){
            $query .= $aditionalFilters;
        }

        $query .= " ORDER BY 1";

        $consulta = new Consulta($query, $this->conexion);
        $result = $consulta->ret_matriz("a");

        $result = $this->transformarData($result);

        return $result;

    }

    public function transformarData($result)
    {
        $result_new = array();
        for ($i=0; $i < (count($result)); $i++) { 
                
            $numDespac = $result[$i]['num_despac'];

            $result_new[$i] = array(
                                    'lat'           => $result[$i]['lat'],
                                    'lon'           => $result[$i]['lon'],
                                    'novedad'       => rawurlencode(utf8_encode($result[$i]['novedad'])),
                                    'observacion'   => rawurlencode(utf8_encode($result[$i]['observacion'])),
                                    'fecha'         => rawurlencode($result[$i]['fecha'])
                                    );
        }

        return $result_new;   
    }

}//FIN CLASE PROC_DESPAC

$proceso = new BandejaSegumiento();

?>
<?php
    /****************************************************************************
    NOMBRE:   ajax_transp_tipser
    FUNCION:  Retorna todos los datos necesarios para cargar el formulario y los
              Daatatables
    FECHA DE MODIFICACION: 23/12/2021
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/
    
   /*  ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1); */
    

    class ajax_transp_tipser
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
            include_once('../lib/general/constantes.inc');

            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "1":
                    //verifica si hay datos en la transportadora
                    self::iniSelectDatosBD();
                    break;
                case "2":
                    self::datoshorservic();
                    break;
                case "3":
                    self::datostiempservic();
                    break;
            }
        }
/****************************************************************************
    NOMBRE:   iniSelectDatosBD
    FUNCION:  trae los datos para el multiselect las transportadoras
    FECHA DE MODIFICACION: 23/12/2021
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/
    
        private function iniSelectDatosBD(){
            $sql="select DISTINCT transptiempo.`cod_transp`, transp.`nom_tercer` 
            FROM `tab_tercer_tercer` as transp, `tab_transp_tipser` as transptiempo
            where   transp.`cod_tercer`= transptiempo.`cod_transp`
            order by transp.`nom_tercer` ";
            $query = new Consulta($sql, self::$conexion);

            $datos = $query -> ret_num_rows();
            
            
            if ($datos > 0) {
                $datos = $query -> ret_matrix();
                foreach($datos as $resultadodatos)
                {
                    $resultselect = $resultselect ."<option value='".$resultadodatos['cod_transp']."'>".$resultadodatos['nom_tercer']."</option>";
                
                }


                echo $resultselect;
            }else{
                echo '<option value="" selected>----</option>';
                //echo self::notDatos();
            }
            /*$datos = $query -> ret_matrix('a');
            $json = json_encode($datos);
            echo $json;
            */
        } 
 /****************************************************************************
    NOMBRE:   datoshorservic
    FUNCION:  trae los datos para la tabla de horas en el datatable
    FECHA DE MODIFICACION: 23/12/2021
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/       

        private function datoshorservic(){
            $cod_transp=$_POST['cod_transp'];
            $fec_inicio=$_POST['fec_inicio'];
            $fec_finxxx=$_POST['fec_finxxx'];
            $newdat="";

            if(empty($cod_transp)){
                $newdat .= " date(`fec_modifi`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
            }else{
                foreach($cod_transp as $datcod_transp){
                    $newdat .= " `cod_tercer` LIKE ".$datcod_transp." AND date(`fec_modifi`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
                }
            }
            
            $newdat = rtrim($newdat, "or");
            
            $sql="SELECT '' as cont, `cod_tercer`, `nom_tercer`, `com_diasxx`, `hor_ingres`, `hor_salida`, `usr_modifi`, 
            DATE_FORMAT(`fec_modifi`,'%d/%m/%Y %h:%i %p') as fecha 
            FROM `tab_bitaco_tipser` 
            WHERE ".$newdat;
            
            $query = new Consulta($sql, self::$conexion);

            $datos = $query -> ret_num_rows();
            if ($datos > 0) {
                $datos = $query -> ret_matrix();
                
                foreach ($datos as $key => $value) {
                    $nuevovr="";
                    $vrdescompon=explode("|", $datos[$key][3]);
                    $numregexplode=count($vrdescompon);
                    foreach($vrdescompon as $vrdescompon){
                        switch ($vrdescompon){
                            case "L":
                                $vrremplazar="Lunes";
                                break;
                            case "M":
                                $vrremplazar="Martes";
                                break;
                            case "X":
                                $vrremplazar="Miercoles";
                                break;
                            case "J":
                                $vrremplazar="Jueves";
                                break;
                            case "V":
                                $vrremplazar="Viernes";
                                break;
                            case "S":
                                $vrremplazar="Sabado";
                                break;
                            case "D":
                                $vrremplazar="Domingo";
                                break;
                            case "F":
                                $vrremplazar="Festivo";
                                break;
                         }
                    $nuevovr .=$vrremplazar.", "; 
                    }




                     $datos[$key][3]=$nuevovr;


                    
                }
                //print_r($datos);
                
                echo json_encode(self::cleanArray($datos));
            }else{
                $html="<tr>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                </tr>
                ";
                echo json_encode(self::cleanArray($html));
            }
            
        }
        
/****************************************************************************
    NOMBRE:   datostiempservic
    FUNCION:  trae los datos para la tabla de tiempos en el datatable
    FECHA DE MODIFICACION: 23/12/2021
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/ 
        private function datostiempservic(){
            $cod_transp=$_POST['cod_transp'];
            $fec_inicio=$_POST['fec_inicio'];
            $fec_finxxx=$_POST['fec_finxxx'];
            
            $newdat="";
            if(empty( $cod_transp)){
                $newdat .= " tprin.cod_tercer=subtbl.`cod_transp` AND date(subtbl.`fec_creaci`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
            }else{
                foreach($cod_transp as $datcod_transp){
                    $newdat .= " tprin.cod_tercer=subtbl.`cod_transp` and tprin.cod_tercer LIKE '".$datcod_transp."' AND date(subtbl.`fec_creaci`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
                }
            }
            
            $newdat = rtrim($newdat, "or");
            
            $sql="SELECT '' as contador, subtbl.`cod_transp`, tprin.nom_tercer as empresa, 
            subtbl.`tgl_prcnac`, subtbl.`tgl_prcurb`, subtbl.`tie_prcnac`, subtbl.`tie_prcurb`,
            subtbl.`tgl_carnac`, subtbl.`tgl_carurb`, subtbl.`tie_carnac`, subtbl.`tie_carurb`,
            subtbl.`tgl_contro`, subtbl.`tgl_conurb`, subtbl.`tie_contro`, subtbl.`tie_conurb`,
            subtbl.`tgl_desnac`, subtbl.`tgl_desurb`, subtbl.`tie_desnac`, subtbl.`tie_desurb`,
            subtbl.`usr_creaci`, DATE_FORMAT(subtbl.`fec_creaci`,'%d/%m/%Y %h:%i %p') as fecha      
            FROM `tab_transp_tipser` as subtbl, `tab_tercer_tercer` as tprin 
            WHERE ".$newdat;
                        
            $query = new Consulta($sql, self::$conexion);

            $datos = $query -> ret_num_rows();
            if ($datos > 0) {
                $datos = $query -> ret_matrix();
                
                echo json_encode(self::cleanArray($datos));
            }else{
                $html="<tr>
                <td>''</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>
                <td>No hay Datos</td>                
                </tr>
                ";
                echo json_encode(self::cleanArray($html));
            }
            
        }

        private function notDatos(){
            $htmlencabezado ='
                <div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                            No hay horarios ni tiempos que mostrar... 
                </div>
            
            ';
            
            return $htmlencabezado;
        
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

        private function partewhere(){
            $cod_transp=$_POST['cod_transp'];
            $fec_inicio=$_POST['fec_inicio'];
            $fec_finxxx=$_POST['fec_finxxx'];
            $newdat="";
            foreach($cod_transp as $datcod_transp){
                $newdat .= " `cod_tercer` LIKE ".$datcod_transp." AND date(`fec_modifi`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
            }
            $newdat = rtrim($newdat, "or");
            return $newdat;
        }
    
    
    }

    new ajax_transp_tipser();

    /* $sel=new ajax_transp_tipser();
    $matrizuser=$sel->iniSelectDatosBD;
    */
?>
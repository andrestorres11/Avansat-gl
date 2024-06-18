<?php
    /****************************************************************************
    NOMBRE:   AjaxInsNovedaSeguim
    FUNCION:  Retorna todos los datos necesarios para construir la información
    FECHA DE MODIFICACION: 24/11/2021
    CREADO POR: Ing. Cristian Andrés Torres
    MODIFICADO 
    ****************************************************************************/
    
    /*error_reporting(E_ALL);
    ini_set('display_errors', '1');*/

    class AjaxParameNovedaSeguim
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();
        static private $cod_perfil = null;
        function __construct($co = null, $us = null, $ca = null)
        {
            //Include Connection class
            @include( "../lib/ajax.inc" );
            @include( "../lib/general/src/class.upload.php" );
            include_once('../lib/general/constantes.inc');
            include_once('../lib/general/functions.inc');
            @include_once '../../' . BASE_DATOS . '/constantes.inc';
            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario =  $_SESSION['datos_usuario']['cod_usuari'];
            self::$cod_aplica = $ca;
            self::$cod_perfil = $_SESSION['datos_usuario']['cod_perfil'];
            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "getTransportadoras":
                    self::getTransportadoras();
                    break;
                case "getInfo":
                    self::getInfo();
                    break;
                case "save":
                    self::save();
                    break;
                case "getDV":
                    self::calcularDV();
                    break;
                case "getTransp":
                    self::getTransp();
                    break;
                case "consultaCiudades":
                    self::consultaCiudades();
                    break;
                case "unassignment":
                    self::unassignment();
                    break;

            }
        }

        function getTransportadoras(){
            $busqueda = $_REQUEST['key'];
            $sql="SELECT a.cod_tercer, b.nom_tercer FROM 
                                ".BASE_DATOS.".tab_tercer_emptra a 
                     INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                            ON a.cod_tercer = b.cod_tercer
                            WHERE b.cod_estado = 1 AND b.nom_tercer LIKE '%$busqueda%' AND a.esx_asegur = 1 ORDER BY b.nom_tercer LIMIT 3
                            ";
        
              $resultado = new Consulta($sql, self::$conexion);
              $resultados = $resultado->ret_matriz();
              $htmls='';
              foreach($resultados as $valor){
                $htmls.='<div><a class="suggest-element" data="'.$valor['cod_tercer'].' - '.$valor['nom_tercer'].'" id="'.$valor['cod_tercer'].'">'.$valor['nom_tercer'].'</a></div>';
              }
              echo utf8_decode($htmls);
        }

        function getInfo(){

            $getData = self::armaTabla();
            $html='
            <form type="POST" id="FormParam">
            <div class="accordion">
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Informes
                            </button>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse show m-3" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body" style="width: 100%; overflow: auto;">
                            <table class="table table-bordered conten-table" id="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nit</th> 
                                        <th>Asegurado</th>
                                        <th>Ciudad</th>
                                        <th>Tel�fono</th>
                                        <th>Correo Electr�nico</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>';
            $html.=$getData;

            $html.='             </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            ';
            echo $html;
        }



        function armaTabla(){
            $cod_asegura = self::getAseguradora();
            if(isset($_REQUEST['transp']) || $_REQUEST['transp'] != ''){
                $cod_asegura = $_REQUEST['transp'];
            }
            $sql="SELECT a.cod_asegur, b.cod_tercer, b.nom_tercer, c.nom_ciudad, b.num_telef1, b.dir_emailx FROM ".BASE_DATOS.".tab_asegur_transp a
                                        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_transp = b.cod_tercer
                                        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c ON b.cod_ciudad = c.cod_ciudad
                                                                                     AND b.cod_depart = c.cod_depart
                                                                                     AND b.cod_paisxx = c.cod_paisxx
                WHERE cod_asegur = '$cod_asegura'";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();

            foreach($resultados as $index => $resultado){
                $data .= '<tr data-id="'.$cod_asegura.'-'.$resultado['cod_tercer'].'">
                            <td>'.($index + 1).'</td>
                            <td>'.$resultado['cod_tercer'].'</td>
                            <td>'.$resultado['nom_tercer'].'</td>
                            <td>'.$resultado['nom_ciudad'].'</td>
                            <td>'.$resultado['num_telef1'].'</td>
                            <td>'.$resultado['dir_emailx'].'</td>
                            <td class="text-center"><button type="button" class="mr-3 btn btn-danger btn-sm" onclick="unassignment(`'.$resultado['cod_asegur'].'`, `'.$resultado['cod_tercer'].'`)"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                        </tr>';
            }
                   
            return $data;   
        }

        function armaTablaEstandar(){
            $cod_transp = trim($_POST['transp']);
            
            $cod_etapax = 'novsta';
            $Msql="SELECT a.cod_noveda, a.nom_noveda
                             FROM ".BASE_DATOS.".tab_genera_noveda a
                            WHERE a.ind_estado = 1 AND a.cod_noveda BETWEEN 9000 AND 9999 ";
            $resultado = new Consulta($Msql, self::$conexion);
            $novedades = $resultado->ret_matriz();
            $html = '';
            $data = '';
            foreach($novedades as $valor){
                $cod_novedad = $valor['cod_noveda'];
                $nom_novedad = $valor['nom_noveda'];
                $name =  $valor['cod_noveda'];
                $sqlNov = "SELECT b.ind_status, b.ind_novesp, b.ind_manale, b.ind_fuepla,
                                  b.ind_soltie, b.inf_visins, b.ind_notsup,
                                  b.ind_limpio, b.num_tiempo
                            FROM ".BASE_DATOS.".tab_parame_novseg b
                            WHERE b.cod_transp = '".$cod_transp."' AND b.cod_noveda = '".$cod_novedad."'";
                $resultado = new Consulta($sqlNov, self::$conexion);
                $resultados = $resultado->ret_matriz();
                if(count($resultados)==0){
                    $resultados[0] = array('ind_status'=>NULL, 'ind_novesp'=>NULL, 'ind_manale'=>NULL, 'ind_fuepla'=>NULL, 'ind_soltie'=>NULL, 'inf_visins'=>NULL, 'ind_notsup'=>NULL, 'ind_limpio'=>NULL, 'num_tiempo'=>NULL);
                }
                foreach($resultados as $resultado){
                    if($resultado['num_tiempo'] == '' || $resultado['num_tiempo'] == NULL){
                        $resultado['num_tiempo'] = 0;
                    }
                    $onclick = "";
                    if($cod_etapax==0){
                        $name = $cod_novedad."_T";
                    }

                    $input_disabled = '';
                    if($resultado['ind_manale'] || $resultado['ind_soltie']){
                        $input_disabled = 'disabled';
                        $resultado['num_tiempo'] = 0;
                    }

                    $data .= '<tr>
                                <td><input type="checkbox" class="colcheck_'.$cod_etapax.'" onclick="selectRow(this)" data="'.$cod_etapax.'"></td>
                                <td class="text-center">'.$cod_novedad.'</td>
                                <td>'.$nom_novedad.'</td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' activo_'.$cod_novedad.'" data="activo_'.$cod_novedad.'" name="activo_'.$name.'" '.self::validacheck($resultado['ind_status']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' novesp_'.$cod_novedad.'" data="novesp_'.$cod_novedad.'" name="novesp_'.$name.'" '.self::validacheck($resultado['ind_novesp']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' manale_'.$cod_novedad.'" data="manale_'.$cod_novedad.'" name="manale_'.$name.'" '.self::validacheck($resultado['ind_manale']).' onclick="selectGemelo(this);disableTime(this)" code="'.$cod_novedad.'" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' fuepla_'.$cod_novedad.'" data="fuepla_'.$cod_novedad.'" name="fuepla_'.$name.'" '.self::validacheck($resultado['ind_fuepla']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' soltie_'.$cod_novedad.'" data="soltie_'.$cod_novedad.'" name="soltie_'.$name.'" '.self::validacheck($resultado['ind_soltie']).' onclick="selectGemelo(this);disableTime(this)" code="'.$cod_novedad.'" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' visins_'.$cod_novedad.'" data="visins_'.$cod_novedad.'" name="visins_'.$name.'" '.self::validacheck($resultado['inf_visins']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' notsup_'.$cod_novedad.'" data="notsup_'.$cod_novedad.'" name="notsup_'.$name.'" '.self::validacheck($resultado['ind_notsup']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" send="true" value="1" class="chkb_'.$cod_etapax.' limpio_'.$cod_novedad.'" data="limpio_'.$cod_novedad.'" name="limpio_'.$name.'" '.self::validacheck($resultado['ind_limpio']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td><input type="text" class="tiempo_'.$cod_novedad.'" size="6" name="tiempo_'.$name.'" value="'.$resultado['num_tiempo'].'" onchange="insertGemelo(this)" '.$input_disabled.' onchange="Selector(this)"></td>
                            </tr>';
                    }

            }
           
            $html .= '
                        <table class="table table-bordered conten-table" id="table_'.$cod_etapax.'">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="SelecAll_'.$cod_etapax.'" data="'.$cod_etapax.'" onclick="selectedAll(this)"></th>
                                    <th>C&oacute;digo</th> 
                                    <th>Nombre de la novedad</th>
                                    <th>Activo</th>
                                    <th>Novedad Especial</th>
                                    <th>Mantiene Alerta</th>
                                    <th>Fuera de Plataforma</th>
                                    <th>Solicita Tiempo</th>
                                    <th>Visibilidad (N/S)</th>
                                    <th>Notifica Supervisor</th>
                                    <th>Limpio</th>
                                    <th>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                '.$data.'
                            </tbody>
                        </table>';

            return $html;   
        }

        function validacheck($valor){
            if( $valor=='1'  ){
                return 'checked';
            }
            return '';
        }

        function save(){
            $info=[];
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $dat_ubicac = self::darDatosCiudad(self::separarCodigoCiudad($_REQUEST['cod_ciudad']));
            $pai_transp = $dat_ubicac['cod_paisxx'];
            $dep_transp = $dat_ubicac['cod_depart'];
            $ciu_transp = $dat_ubicac['cod_ciudad'];

            if($_REQUEST['reg_transp']){
                $sql = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer(
                            cod_tercer, num_verifi, cod_tipdoc,
                            cod_terreg, nom_tercer, abr_tercer,
                            dir_domici, num_telef1, dir_emailx, cod_estado,
                            obs_tercer, cod_paisxx, cod_depart,
                            cod_ciudad, usr_creaci, fec_creaci
                        ) VALUES (
                            '".$_REQUEST['cod_transp']."', '".$_REQUEST['dv']."', 'N',
                            '".$_REQUEST['regimen']."', '".$_REQUEST['nom_transp']."', '".$_REQUEST['abr_transp']."',
                            '".$_REQUEST['nom_direcc']."', '".$_REQUEST['num_telefo']."', '".$_REQUEST['cor_transp']."', '1',
                            'Creado desde el m�dulo Asegurados', '".$pai_transp."', '".$dep_transp."',
                            '".$ciu_transp."', '".$usuari."', NOW()
                )";
                $query = new Consulta($sql, self::$conexion);

                $sql = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi(
                    cod_tercer, cod_activi
                ) VALUES (
                    '".$_REQUEST['cod_transp']."', ".COD_FILTRO_EMPTRA."
                )";
                $query = new Consulta($sql, self::$conexion);            
                
                $sql = "INSERT INTO ".BASE_DATOS.".tab_tercer_emptra(
                    cod_tercer, ind_gracon, usr_creaci, fec_creaci
                ) VALUES (
                    '".$_REQUEST['cod_transp']."', 'N', '".$usuari."', NOW()
                )";
                $query = new Consulta($sql, self::$conexion);
                
                // Obtener el último cod_agenci
                $sql = "SELECT cod_agenci
                            FROM " . BASE_DATOS . ".tab_genera_agenci
                            ORDER BY fec_creaci DESC
                            LIMIT 1";
                $query = new Consulta($sql, self::$conexion);
                $resultado = $query->ret_matriz('a');

                // Asumir que el último cod_agenci es el mayor, incrementarlo y verificar si es único
                if (!empty($resultado)) {
                    $cod_agenci = $resultado[0]['cod_agenci'] + 1;
                } else {
                    $cod_agenci = 1; // En caso de que no haya registros, comenzamos con 1
                }

                // Verificar si el cod_agenci ya existe y continuar incrementando hasta encontrar uno que no esté registrado
                while (true) {
                    $sql_check = "SELECT cod_agenci
                            FROM " . BASE_DATOS . ".tab_genera_agenci
                            WHERE cod_agenci = " . $cod_agenci;
                    $query_check = new Consulta($sql_check, self::$conexion);
                    $resultado_check = $query_check->ret_matriz('a');

                    if (empty($resultado_check)) {
                        // Si no hay resultados, el cod_agenci es único y podemos usarlo
                    break;
                    } else {
                        // Si el cod_agenci ya existe, incrementarlo y verificar nuevamente
                        $cod_agenci++;
                    }
                }
                
                $sql = "INSERT INTO ".BASE_DATOS.".tab_genera_agenci(
                    cod_agenci, nom_agenci, cod_paisxx, cod_depart,
                    cod_ciudad, dir_agenci, tel_agenci, usr_creaci,
                    fec_creaci
                ) VALUES (
                    '".$cod_agenci."', '".$_REQUEST['nom_transp']."', '".$pai_transp."', '".$dep_transp."',
                    '".$ciu_transp."', '".$_REQUEST['nom_direcc']."', '".$_REQUEST['num_telefo']."', '".$usuari."',
                    NOW()
                )";
                $query = new Consulta($sql, self::$conexion); 

                $sql = "INSERT INTO ".BASE_DATOS.".tab_transp_agenci(
                    cod_transp, cod_agenci
                ) VALUES (
                    '".$_REQUEST['cod_transp']."', '".$cod_agenci."'
                )";
                $query = new Consulta($sql, self::$conexion); 
 
            }
            $cod_asegura = self::getAseguradora();

            if(isset($_REQUEST['ase_asigna']) || $_REQUEST['ase_asigna'] != ''){
                $cod_asegura = $_REQUEST['ase_asigna'];
            }

            if($cod_asegura != null){
                $sql = "INSERT INTO ".BASE_DATOS.".tab_asegur_transp(
                    cod_asegur, cod_transp, usr_creaci, fec_creaci
                  ) VALUES (
                    '".$cod_asegura."', '".$_REQUEST['cod_transp']."', '".$_SESSION['datos_usuario']['cod_usuari']."' ,NOW()
                 )";
                $query = new Consulta($sql, self::$conexion);
            }
            if($query){
                $info['status']=100;
                $info['info'] = self::getTransp(1,$_REQUEST['cod_transp']);
                $info['response'] = 'Asignacion Exitosa.';
            }else{
                $info['status']=200;
                $info['response'] = 'No fue posible registrar el operador.';
            }
            echo json_encode($info);
        }


        function unassignment(){
            $sql = "DELETE FROM ".BASE_DATOS.".tab_asegur_transp
                        WHERE cod_asegur = '".$_REQUEST['cod_asegur']."' AND cod_transp = '".$_REQUEST['cod_tercer']."'
                   ";
            $query = new Consulta($sql, self::$conexion); 
            if($query){
                $info['status']=100;
                $info['info'] = self::getTransp(1,$_REQUEST['cod_transp']);
                $info['response'] = 'Desasignacion realizada con exito.';
            }else{
                $info['status']=200;
                $info['response'] = 'No fue posible hacer la operacion';
            }
            echo json_encode($info);
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

        function separarCodigoCiudad($dato, $retorno = 1) {
            $regex = '/\((\d+)\-(\d+)\)/';
            if (preg_match($regex, $dato, $matches)) {
                // Almacenar los valores en una sola variable
                $codigo = array(trim($matches[1]), trim($matches[2]));
                // Devolver el valor correspondiente basado en el par?metro $retorno
                return ($retorno == 1) ? $codigo[0] : $codigo[1];
            }
            // Devolver un valor por defecto en caso de que no haya coincidencia
            return '';
        }

        function darDatosCiudad($cod_ciudad){
            $sql = "SELECT cod_paisxx, cod_depart, cod_ciudad
                      FROM ".BASE_DATOS.".tab_genera_ciudad
                    WHERE cod_ciudad = '".$cod_ciudad."'; ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];
            return $resultado;
          }

        private function calcularDV() {
            if (isset($_REQUEST['nit']) && is_numeric($_REQUEST['nit'])) {
                $nit = $_REQUEST['nit'];
                $multiplicadores = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47];
                $nitArray = array_reverse(str_split($nit));
                $suma = 0;
    
                foreach ($nitArray as $index => $digit) {
                    if ($index < count($multiplicadores)) {
                        $suma += intval($digit) * $multiplicadores[$index];
                    }
                }
    
                $residuo = $suma % 11;
                $dv = 11 - $residuo;
    
                if ($dv == 10) {
                    $dv = 1;
                } elseif ($dv == 11) {
                    $dv = 0;
                }
    
                echo $dv;
            } else {
                echo '0';
            }
        }

        private function getTransp($flag = NULL, $cod_transp = NULL){
            $nit = $_REQUEST['nit'];
            if($flag==1){
                $nit = $cod_transp;
            }
            $mSql = "SELECT a.cod_tercer, UPPER(a.nom_tercer) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
                            a.cod_ciudad, a.dir_domici, CONCAT(a.num_telef1, ' ', a.num_telef2) as 'num_telefo',
                            a.dir_emailx, a.cod_terreg, c.nom_ciudad
                      FROM " . BASE_DATOS . ".tab_tercer_tercer a
                      INNER JOIN " . BASE_DATOS . ".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer
                      INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad c ON a.cod_ciudad = c.cod_ciudad
                                                                                     AND a.cod_depart = c.cod_depart
                                                                                     AND a.cod_paisxx = c.cod_paisxx
                            WHERE b.cod_activi = " . COD_FILTRO_EMPTRA .
                            " AND a.cod_tercer LIKE '%".$nit."%'";
            $resultado = new Consulta($mSql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            if($flag==NULL){
                if($resultados) {
                    echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(["error" => "No se encontraron resultados"]);
                }
            }else{
                return json_encode($resultados, JSON_UNESCAPED_UNICODE);
            }
        }

            

        public function consultaCiudades(){
            $busqueda = $_REQUEST['key'];
            $sql="SELECT a.cod_ciudad, a.nom_ciudad, b.nom_depart, a.cod_paisxx, c.nom_paisxx FROM ".BASE_DATOS.".tab_genera_ciudad a
                     INNER JOIN ".BASE_DATOS.".tab_genera_depart b ON a.cod_depart = b.cod_depart
                     INNER JOIN ".BASE_DATOS.".tab_genera_paises c ON a.cod_paisxx = c.cod_paisxx
                     WHERE a.ind_estado = 1 AND a.nom_ciudad LIKE '%$busqueda%' ORDER BY a.nom_ciudad LIMIT 3";
        
              $resultado = new Consulta($sql, self::$conexion);
              $resultados = $resultado->ret_matriz();
              $htmls='';
              foreach($resultados as $valor){
                $htmls.='<div><a class="suggest-element bk-principal_color white-color" data="'.$valor['nom_ciudad'].' ('.$valor['cod_ciudad'].'-'.$valor['cod_paisxx'].')" id="'.$valor['cod_ciudad'].'">('.substr($valor['nom_paisxx'], 0, 3).') - '.substr($valor['nom_depart'], 0, 4).' - '.$valor['nom_ciudad'].'</a></div>';
              }
              echo utf8_decode($htmls);
        
        }

        public function getAseguradora(){
            $sql="SELECT clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_usuari 
                     WHERE cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND cod_filtro = '".COD_FILTRO_ASEGUR."'";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            if(sizeof($resultados)>0){
                return $resultados[0][0];
            }
            return null;
        }
}

new AjaxParameNovedaSeguim();

<?php
    /****************************************************************************
    NOMBRE:   AjaxInsNovedaSeguim
    FUNCION:  Retorna todos los datos necesarios para construir la informaci�n
    FECHA DE MODIFICACION: 24/11/2021
    CREADO POR: Ing. Cristian Andr�s Torres
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
            }
        }

        function getTransportadoras(){
            $busqueda = $_REQUEST['key'];
            $sql="SELECT a.cod_tercer, b.nom_tercer FROM 
                                ".BASE_DATOS.".tab_tercer_emptra a 
                     INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                            ON a.cod_tercer = b.cod_tercer
                            WHERE b.cod_estado = 1 AND b.nom_tercer LIKE '%$busqueda%' ORDER BY b.nom_tercer LIMIT 3";
        
              $resultado = new Consulta($sql, self::$conexion);
              $resultados = $resultado->ret_matriz();
              $htmls='';
              foreach($resultados as $valor){
                $htmls.='<div><a class="suggest-element" data="'.$valor['cod_tercer'].' - '.$valor['nom_tercer'].'" id="'.$valor['cod_tercer'].'">'.$valor['nom_tercer'].'</a></div>';
              }
              echo utf8_decode($htmls);
        }

        function getInfo(){
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
                            <div id="collapsethree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                                <ul class="nav nav-pills mb-3 bk" id="pills-tab" role="tablist">
                                    '.self::creaPestanas().'
                                </ul>
                            </div>
                            <div class="tab-content border" id="pills-tabContent">
                                    '.self::crearContenido().'
                            </div>
                            <div class="row col-md-12 mt-3 mb-3 d-flex justify-content-center"><button type="button" id="btnGuardarParam" onclick="saveInfo()" class="btn btn-success btn-sm">Guardar parametrizacion</button></div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            ';
            echo utf8_decode($html);
        }


        function creaPestanas(){
            $sql="SELECT a.cod_etapax, a.nom_etapax FROM 
                                ".BASE_DATOS.".tab_genera_etapax a 
                            WHERE a.ind_estado = 1";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            $html = '';
            foreach($resultados as $key => $resultado){
                $active = '';
                $aselect = 'false';
                if($key==0){
                    $active = 'active';
                    $aselect = 'true';
                }
                $pestana = $resultado['cod_etapax'];
                $html .= '<li class="nav-item m-2">
                            <a class="btn btn-success btn-sm '.$active.'" id="pills-'.$pestana.'-tab" data-toggle="pill" href="#pills-'.$pestana.'" role="tab" aria-controls="pills-'.$pestana.'" aria-selected="'.$aselect.'">'.$resultado['nom_etapax'].'</a>
                        </li>';
            }

            if(self::$cod_perfil == COD_PERFIL_ADMINIST){
                $html .= '<li class="nav-item m-2">
                            <a class="btn btn-success btn-sm " id="pills-novsta-tab" data-toggle="pill" href="#pills-novsta" role="tab" aria-controls="pills-novsta" aria-selected="false">Novedades Estandar</a>
                        </li>';
            }
            return $html;
        }

        function crearContenido(){
            $sql="SELECT a.cod_etapax, a.nom_etapax FROM 
                                ".BASE_DATOS.".tab_genera_etapax a 
                            WHERE a.ind_estado = 1";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            $html = '';
            foreach($resultados as $key=>$resultado){
                $active = '';
                $show = '';
                if($key==0){
                    $active = 'active';
                    $show = 'show';
                }
                $pestana = $resultado['cod_etapax'];
                $html .= ' <div class="tab-pane fade '.$show.' p-3 '.$active.'" id="pills-'.$pestana.'" role="tabpanel" aria-labelledby="pills-'.$pestana.'-tab" style="width: 100%; overflow: auto;">
                                <div class="row">
                                    <div class="col-md-12">
                                        '.self::armaTabla($resultado['cod_etapax']).'
                                    </div>
                                </div>
                            </div>';
            }

            //Arma la tabla para novedades estandar
            if(self::$cod_perfil == COD_PERFIL_ADMINIST){
            $html .= ' <div class="tab-pane fade p-3 " id="pills-novsta" role="tabpanel" aria-labelledby="pills-novsta-tab" style="width: 100%; overflow: auto;">
                                <div class="row">
                                    <div class="col-md-12">
                                       '.self::armaTablaEstandar().'
                                    </div>
                                </div>
                            </div>';
            }    
            return $html;
        }

        function armaTabla($cod_etapax){
            $cod_transp = trim($_POST['transp']);
            $cond = '';
            
            if($cod_etapax != 0){
                $cond = 'AND a.cod_etapax = "'.$cod_etapax.'" ';
            }
            
            $Msql="SELECT a.cod_noveda, a.nom_noveda
                             FROM ".BASE_DATOS.".tab_genera_noveda a
                            WHERE a.ind_estado = 1 ".$cond;
            if(self::$cod_perfil != COD_PERFIL_ADMINIST){
                $Msql.=" AND a.cod_noveda BETWEEN 1 AND 8999 ";
            }
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
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' activo_'.$cod_novedad.'" data="activo_'.$cod_novedad.'" name="activo_'.$name.'" '.self::validacheck($resultado['ind_status']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' novesp_'.$cod_novedad.'" data="novesp_'.$cod_novedad.'" name="novesp_'.$name.'" '.self::validacheck($resultado['ind_novesp']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' manale_'.$cod_novedad.'" data="manale_'.$cod_novedad.'" name="manale_'.$name.'" '.self::validacheck($resultado['ind_manale']).' onclick="selectGemelo(this);disableTime(this)" code="'.$cod_novedad.'" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' fuepla_'.$cod_novedad.'" data="fuepla_'.$cod_novedad.'" name="fuepla_'.$name.'" '.self::validacheck($resultado['ind_fuepla']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' soltie_'.$cod_novedad.'" data="soltie_'.$cod_novedad.'" name="soltie_'.$name.'" '.self::validacheck($resultado['ind_soltie']).' onclick="selectGemelo(this);disableTime(this)" code="'.$cod_novedad.'" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' visins_'.$cod_novedad.'" data="visins_'.$cod_novedad.'" name="visins_'.$name.'" '.self::validacheck($resultado['inf_visins']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' notsup_'.$cod_novedad.'" data="notsup_'.$cod_novedad.'" name="notsup_'.$name.'" '.self::validacheck($resultado['ind_notsup']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' limpio_'.$cod_novedad.'" data="limpio_'.$cod_novedad.'" name="limpio_'.$name.'" '.self::validacheck($resultado['ind_limpio']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
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
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' activo_'.$cod_novedad.'" data="activo_'.$cod_novedad.'" name="activo_'.$name.'" '.self::validacheck($resultado['ind_status']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' novesp_'.$cod_novedad.'" data="novesp_'.$cod_novedad.'" name="novesp_'.$name.'" '.self::validacheck($resultado['ind_novesp']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' manale_'.$cod_novedad.'" data="manale_'.$cod_novedad.'" name="manale_'.$name.'" '.self::validacheck($resultado['ind_manale']).' onclick="selectGemelo(this);disableTime(this)" code="'.$cod_novedad.'" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' fuepla_'.$cod_novedad.'" data="fuepla_'.$cod_novedad.'" name="fuepla_'.$name.'" '.self::validacheck($resultado['ind_fuepla']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' soltie_'.$cod_novedad.'" data="soltie_'.$cod_novedad.'" name="soltie_'.$name.'" '.self::validacheck($resultado['ind_soltie']).' onclick="selectGemelo(this);disableTime(this)" code="'.$cod_novedad.'" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' visins_'.$cod_novedad.'" data="visins_'.$cod_novedad.'" name="visins_'.$name.'" '.self::validacheck($resultado['inf_visins']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' notsup_'.$cod_novedad.'" data="notsup_'.$cod_novedad.'" name="notsup_'.$name.'" '.self::validacheck($resultado['ind_notsup']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
                                <td class="text-center"><input type="checkbox" value="1" class="chkb_'.$cod_etapax.' limpio_'.$cod_novedad.'" data="limpio_'.$cod_novedad.'" name="limpio_'.$name.'" '.self::validacheck($resultado['ind_limpio']).' onclick="selectGemelo(this)" onchange="Selector(this)"></td>
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
            $consulta = new Consulta("SELECT 1", self::$conexion, "BR");
            $cod_usuari =$_SESSION['datos_usuario']['cod_usuari'];
            $cod_transp = trim($_POST['cod_transp']);
            $sql="SELECT a.cod_etapax FROM 
                            ".BASE_DATOS.".tab_genera_etapax a 
                            WHERE a.ind_estado = 1";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            foreach($resultados as $key=>$etapa){

                $cod_etapax = $etapa['cod_etapax'];
                $sql_2="SELECT a.cod_noveda FROM 
                    ".BASE_DATOS.".tab_genera_noveda a 
                    WHERE a.ind_estado = 1 AND a.cod_etapax = '".$cod_etapax."'";
                $resultado_2 = new Consulta($sql_2, self::$conexion);
                $novedades = $resultado_2->ret_matriz();

                    foreach($novedades as $key_nov => $novedad){
                        
                        $cod_noveda= $novedad['cod_noveda'];

                        $activo = $_POST['activo_'.$cod_noveda] ? $_POST['activo_'.$cod_noveda]:0;
                        if($activo==0){
                        $activo = $_POST['activo_'.$cod_noveda."_T"] ? $_POST['activo_'.$cod_noveda."_T"]:0;
                        }

                        $novesp = $_POST['novesp_'.$cod_noveda]? $_POST['novesp_'.$cod_noveda]:0;
                        if($novesp==0){
                            $novesp = $_POST['novesp_'.$cod_noveda."_T"] ? $_POST['novesp_'.$cod_noveda."_T"]:0;
                        }

                        $manale = $_POST['manale_'.$cod_noveda] ? $_POST['manale_'.$cod_noveda]:0;
                        if($manale==0){
                            $manale = $_POST['manale_'.$cod_noveda."_T"] ? $_POST['manale_'.$cod_noveda."_T"]:0;
                        }

                        $fuepla = $_POST['fuepla_'.$cod_noveda] ? $_POST['fuepla_'.$cod_noveda]:0;
                        if($fuepla==0){
                            $fuepla = $_POST['fuepla_'.$cod_noveda."_T"] ? $_POST['fuepla_'.$cod_noveda."_T"]:0;
                        }

                        $soltie = $_POST['soltie_'.$cod_noveda] ? $_POST['soltie_'.$cod_noveda]:0;
                        if($soltie==0){
                            $soltie = $_POST['soltie_'.$cod_noveda."_T"] ? $_POST['soltie_'.$cod_noveda."_T"]:0;
                        }

                        $visins = $_POST['visins_'.$cod_noveda] ? $_POST['visins_'.$cod_noveda]:0;
                        if($visins==0){
                            $visins = $_POST['visins_'.$cod_noveda."_T"] ? $_POST['visins_'.$cod_noveda."_T"]:0;
                        }
                        $notsup = $_POST['notsup_'.$cod_noveda] ? $_POST['notsup_'.$cod_noveda]:0;
                        if($notsup==0){
                            $notsup = $_POST['notsup_'.$cod_noveda."_T"] ? $_POST['notsup_'.$cod_noveda."_T"]:0;
                        }

                        $limpio = $_POST['limpio_'.$cod_noveda] ? $_POST['limpio_'.$cod_noveda]:0;
                        if($limpio==0){
                            $limpio = $_POST['limpio_'.$cod_noveda."_T"] ? $_POST['limpio_'.$cod_noveda."_T"]:0;
                        }

                        $tiempo = $_POST['tiempo_'.$cod_noveda] ? $_POST['tiempo_'.$cod_noveda] :0;
                        if($tiempo==0){
                            $tiempo = $_POST['tiempo_'.$cod_noveda."_T"] ? $_POST['tiempo_'.$cod_noveda."_T"]:0;
                        }

                        if($tiempo==''){
                        $tiempo=0;
                        }
                        
                        $qInsReg = "INSERT INTO ".BASE_DATOS.".tab_parame_novseg (cod_transp, cod_noveda, ind_status,
                            ind_novesp, ind_manale, ind_fuepla,
                            ind_soltie, inf_visins, ind_notsup,
                            ind_limpio, num_tiempo, usr_creaci,
                            fec_creaci
                           ) VALUES (
                           '".$cod_transp."', '".$cod_noveda."', '".$activo."',
                           '".$novesp."', '".$manale."', '".$fuepla."',
                           '".$soltie."', '".$visins."', '".$notsup."',
                           '".$limpio."', '".$tiempo."', '".$cod_usuari."',
                           NOW()
                           )
                            ON DUPLICATE KEY UPDATE ind_status = '".$activo."', ind_novesp = '".$novesp."', ind_manale = '".$manale."', ind_fuepla = '".$fuepla."',
                            ind_soltie = '".$soltie."', inf_visins = '".$visins."', ind_notsup = '".$notsup."',
                            ind_limpio = '".$limpio."', num_tiempo = '".$tiempo."', usr_modifi = '".$cod_usuari."',
                            fec_modifi = NOW() ;";
                    
                        $resultado = new Consulta($qInsReg, self::$conexion, "R");
                        $activo='';$novesp='';$manale='';$notsup='';$tiempo='';
                        $fuepla='';$soltie='';$visins='';$limpio='';$cod_usuari='';
                    }
            }
            
                $info['status']=200;
                $info['msj']='Error no se pudo registrar la informaci�n.';
                if( $insercion = new Consulta( "COMMIT" , self::$conexion ) ){
                    $info['status']=100;
                    $info['msj']='Se ha actualizado correctamente la parametrizaci�n.';
                }
            echo json_encode(self::cleanArray($info));
        }


        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaci�n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que ser� analizado por la funci�n
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

new AjaxParameNovedaSeguim();

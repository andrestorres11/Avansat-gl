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

    class AjaxInsNovedaSeguim
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
            @include( "../lib/general/src/class.upload.php" );
            include_once('../lib/general/constantes.inc');
            include_once('../lib/general/functions.inc');
            @include_once '../../' . BASE_DATOS . '/constantes.inc';
            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario =  $_SESSION['datos_usuario']['cod_usuari'];
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "getRegistros":
                    self::getRegistros();
                    break;
                case "guardar":
                    self::guardar();
                    break;
                case "disaenable":
                    self::disaenable();
                    break;
                case "getRegistro":
                    self::getRegistro();
                    break;
            }
        }

        function getRegistros(){
            if($_REQUEST['apl_filtro']==1){
                if($_REQUEST['nom_noveda'] != NULL || $_REQUEST['nom_noveda'] != ''){
                    $cond .= " AND a.nom_noveda LIKE '%".$_REQUEST['nom_noveda']."%' ";
                }

                if($_REQUEST['cod_noveda'] != NULL || $_REQUEST['cod_noveda'] != ''){
                    $cond .= " AND a.cod_noveda = '".$_REQUEST['cod_noveda']."' ";
                }

                if(($_REQUEST['cod_etapax'] != NULL) || ($_REQUEST['cod_etapax'] != 0) || ($_REQUEST['cod_etapax'] != '')){
                    $cond .= " AND a.cod_etapax = '".$_REQUEST['cod_etapax']."' ";
                }

                if($_REQUEST['cod_riesgo'] != NULL || $_REQUEST['cod_riesgo'] != ''){
                    $cond .= " AND a.cod_riesgo = '".$_REQUEST['cod_riesgo']."' ";
                }
                if($_REQUEST['nom_tipoxx'] != NULL || $_REQUEST['nom_tipoxx'] != ''){
                    $cond .= " AND d.nom_tipoxx = '".$_REQUEST['nom_tipoxx']."' ";
                }
            }
            if($_SESSION['datos_usuario']['cod_perfil']!=COD_PERFIL_ADMINIST){
                $cond .= " AND a.cod_noveda BETWEEN 1 And 8999 ";
            }

            $sql = "SELECT a.cod_noveda, a.nom_noveda, CONCAT(UPPER(LEFT(b.nom_etapax, 1)), LOWER(SUBSTRING(b.nom_etapax, 2))) as 'nom_etapax',
                           c.nom_riesgo, a.rut_iconox, a.nom_observ,
                           c.nom_riesgo,IFNULL(d.nom_tipoxx,'N/a') as nom_tipoxx, a.rut_iconox, a.nom_observ,
                            a.ind_estado
                           FROM ".BASE_DATOS.".tab_genera_noveda a
                           INNER JOIN ".BASE_DATOS.".tab_genera_etapax b
                             ON a.cod_etapax = b.cod_etapax
                           INNER JOIN ".BASE_DATOS.".tab_genera_riesgo c
                             ON a.cod_riesgo = c.cod_riesgo
                          LEFT JOIN ".BASE_DATOS.".tab_noveda_tipoxx d
                            ON a.cod_tipoxx = d.cod_tipoxx
                         WHERE 1=1 ".$cond."; ";
              $query = new Consulta($sql, self::$conexion);
              $mMatriz = $query -> ret_matrix('a');
            
             foreach ($mMatriz as $key => $datos) {
                foreach ($datos as $campo => $valor) {
                    if($campo=='ind_estado'){
                        $btn = '<button type="button" class="btn btn-info btn-sm" onclick="opeEdit('.$datos['cod_noveda'].',1)"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                        $btn_opcion = '';
                        if($valor==1){
                            $btn_opcion = '<button type="button" class="mr-3 btn btn-danger btn-sm" onclick="logicDelete('.$datos['cod_noveda'].', `disable`)"><i class="fa fa-times" aria-hidden="true"></i></button>';
                        }else{
                            $btn_opcion = '<button type="button" class="mr-3 btn btn-success btn-sm" onclick="logicDelete('.$datos['cod_noveda'].', `enable`)"><i class="fa fa-check" aria-hidden="true"></i></button>';
                        }
                        $data[$key][] = '<center>'.$btn_opcion.''.$btn.'</center>';	
                    }else if($campo=='rut_iconox'){
                        $img = '<center><img src="'.$valor.'" width="25px;"></center>';
                        $data[$key][] = $img;	
                    }else{
                      $data[$key][] = $valor;	
                    }
                } 
              }

              echo json_encode(self::cleanArray($data));

        }

        function guardar(){
            $info=[];
            $rut_files =  "../../".BASE_DATOS."/";
            $nombre = '';
            if($_FILES['rut_iconox'] != null || $_FILES['rut_iconox'] != ''){
                $ext = explode(".", ($_FILES['rut_iconox']['name']));
                $nombre = 'files/img_noveda/'.time().".".end($ext);
                $ruta_completa = $rut_files.''.$nombre;
                if (move_uploaded_file($_FILES['rut_iconox']['tmp_name'], $ruta_completa)) {
                    $info['img_status']=1000;
                    $info['img_msjxxx']='Imagen Cargada';
                    $act_imagen =  "rut_iconox = '".$nombre."', ";
                }else{
                    $info['img_status']=2000;
                    $info['img_msjxxx']='La imagen no pudo ser cargada. Intente nuevamente.';
                }
            }

            $sql = " INSERT INTO ".BASE_DATOS.".tab_genera_noveda(
                cod_noveda, nom_noveda, cod_etapax, cod_riesgo,
                rut_iconox, nom_observ, ind_estado,
                fec_creaci, usr_creaci
            ) 
                VALUES 
            (
                '".self::getLastReg()."', '".$_REQUEST['nom_noveda']."', '".$_REQUEST['cod_etapax']."', '".$_REQUEST['cod_riesgo']."',
                '".$nombre."', '".$_REQUEST['nom_observa']."', 1,
                NOW(), '".self::$usuario."'
            )";

            if($_REQUEST['ind_update']){
                $sql = "UPDATE 
	                        ".BASE_DATOS.".tab_genera_noveda 
                        SET 
                            nom_noveda = '".$_REQUEST['nom_noveda']."', 
                            cod_etapax = '".$_REQUEST['cod_etapax']."', 
                            cod_riesgo = '".$_REQUEST['cod_riesgo']."', 
                            ".$act_imagen."
                            nom_observ = '".$_REQUEST['nom_observa']."', 
                            fec_modifi = NOW(), 
                            usr_modifi = '".self::$usuario."' 
                        WHERE 
                            cod_noveda = '".$_REQUEST['cod_noveda']."'
                ";
            }
            
            $query = new Consulta($sql, self::$conexion);
            if($query){
                $info['status']=1000;
                $info['response']='Informacion guardada con exito.';
            }else{
                $info['status']=2000;
                $info['response']='La informacion no pudo ser guardada. Intente nuevamente.';
            }
            echo json_encode($info);
        }

        function disaenable(){
            $sql = "UPDATE ".BASE_DATOS.".tab_genera_noveda SET
                        ind_estado = '".$_REQUEST['ind_status']."',
                        fec_modifi = NOW(),
                        usr_modifi = '".self::$usuario."'
                    WHERE 
                        cod_noveda = '".$_REQUEST['cod_noveda']."'";
            $query = new Consulta($sql, self::$conexion);
            if($query){
                $info['status']=1000;
                $info['response']='Actualizacion realizada con exito';
            }else{
                $info['status']=2000;
                $info['response']='La informacion no pudo ser guardada. Intente nuevamente.';
            }
            echo json_encode($info);
        }

        function getRegistro(){
            $sql = "SELECT a.cod_noveda, a.nom_noveda, a.cod_etapax,
                           a.cod_riesgo, a.rut_iconox, a.nom_observ,
                           a.ind_estado,a.cod_tipoxx
                          FROM ".BASE_DATOS.".tab_genera_noveda a
                        WHERE a.cod_noveda = '".$_REQUEST['cod_noveda']."'; ";
             $query = new Consulta($sql, self::$conexion);
             $mMatriz = $query -> ret_matrix('a')[0];
             echo json_encode(self::cleanArray($mMatriz));
        }

        function getLastReg(){
            $sql = "SELECT cod_noveda FROM ".BASE_DATOS.".tab_genera_noveda 
                        WHERE cod_noveda < 9000 
                    ORDER BY cod_noveda DESC LIMIT 1";
             $query = new Consulta($sql, self::$conexion);
             $mMatriz = $query -> ret_arreglo();
            return ($mMatriz['cod_noveda']+1);
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

new AjaxInsNovedaSeguim();

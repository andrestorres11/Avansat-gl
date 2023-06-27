<?php
    /****************************************************************************
    NOMBRE:   AjaxInsNovedaSeguim
    FUNCION:  Retorna todos los datos necesarios para construir la informaciï¿½n
    FECHA DE MODIFICACION: 24/11/2021
    CREADO POR: Ing. Cristian Andrï¿½s Torres
    MODIFICADO 
    ****************************************************************************/
    
    
    /*error_reporting(E_ALL);
    ini_set('display_errors', '1');*/

    class AjaxLisDespacCEVA
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
                if($_REQUEST['cod_manifi'] != NULL || $_REQUEST['cod_manifi'] != ''){
                    $cond .= " AND a.cod_manifi = '".$_REQUEST['cod_manifi']."' ";
                }

                if($_REQUEST['fec_inicia'] != NULL || $_REQUEST['fec_finxxx'] != ''){
                    $cond .= " AND a.fec_creaci BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finxxx']." 23:59:59' ";
                }
            }

            $sql = "SELECT a.cod_manifi, a.cod_status, a.obs_noveda,
                           a.fec_creaci, a.cor_enviad, a.fec_enviad
                           FROM ".BASE_DATOS.".tab_bitaco_gendes a
                         WHERE a.cod_transp = '830079716' ".$cond."; ";
              $query = new Consulta($sql, self::$conexion);
              $mMatriz = $query -> ret_matrix('a');
            
             foreach ($mMatriz as $key => $datos) {
                foreach ($datos as $campo => $valor) {
                    if($campo=='cor_enviad'){
                        if($valor==''){
                            $data[$key][] = '<span class="badge badge-warning" style="font-size: 95% !important;">No enviado</span>';
                        }else{
                            $data[$key][] = $valor;
                        }
                        	
                    }else if($campo=='cod_status'){
                        if($valor==1000){
                            $data[$key][] = '<span class="badge badge-success" style="font-size: 95% !important;">¡Exito!</span>';
                        }else{
                            $data[$key][] = '<span class="badge badge-danger" style="font-size: 95% !important;">¡Fallo!</span>';
                        }
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
                rut_iconox, nom_observ, ind_estado,cod_tipoxx,
                fec_creaci, usr_creaci
            ) 
                VALUES 
            (
                '".self::getLastReg()."', '".$_REQUEST['nom_noveda']."', '".$_REQUEST['cod_etapax']."', '".$_REQUEST['cod_riesgo']."',
                '".$nombre."', '".$_REQUEST['nom_observa']."', 1,'".$_REQUEST['cod_tipoxx']."',
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
                            cod_tipoxx = '".$_REQUEST['cod_tipoxx']."',
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
                           a.cod_riesgo,a.cod_tipoxx, a.rut_iconox, a.nom_observ,
                           a.ind_estado
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
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaciÃ³n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que serÃ¡ analizado por la funciÃ³n
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

new AjaxLisDespacCEVA();

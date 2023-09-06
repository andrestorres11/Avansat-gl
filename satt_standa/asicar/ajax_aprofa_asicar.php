<?php
    /****************************************************************************
    NOMBRE:   AjaxAprobaAsicar
    FUNCION:  Retorna todos los datos necesarios para construir la informaci?n
    FECHA DE MODIFICACION: 06/09/2023
    CREADO POR: Ing. Cristian Andrés Torres
    MODIFICADO 
    ****************************************************************************/
    
    
    /*error_reporting(E_ALL);
    ini_set('display_errors', '1');*/

    class AjaxAprobaAsicar
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;

        static private $cod_usuari = null;
        static private $cod_consec = null;
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
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            self::$cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
            self::$cod_consec = $_SESSION['datos_usuario']['cod_consec'];
            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "getInfoSolicitud":
                    self::getInfoSolicitud();
                    break;
                case "getRegistros":
                    self::getRegistros();
                    break;
                case "aprobaAsiste":
                      self::aprobaAsiste();
                      break;
            }
        }

          /*! \fn: getReponsability
          *  \brief: Trae los indicadores de secciones visibles por encargado (Perfil)
          *  \author: Ing. Fabian Salinas
          *  \date:  21/02/2020
          *  \date modified: dd/mm/aaaa
          *  \modified by: 
          *  \param: mCatego   String   campo categoria a retornar
          *  \return: Object
          */
          function getReponsability( $mCatego )
          {
              $mSql = "SELECT a.jso_estseg
                        FROM ".BASE_DATOS.".tab_genera_respon a 
                  INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
                          ON a.cod_respon = b.cod_respon 
                        WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
              $mConsult = new Consulta($mSql, self::$conexion);
              $mData = $mConsult->ret_matrix('a');

              return json_decode($mData[0][$mCatego]);
          }


          function getRegistros(){
            $mPerms = self::getReponsability('jso_estseg');

            if($_REQUEST['num_solici'] != NULL || $_REQUEST['num_solici'] != ''){
              $cond .= " AND a.id = '".$_REQUEST['num_solici']."' ";
            }

            if($_REQUEST['fec_inicio'] != NULL || $_REQUEST['fec_inicio'] != '' && $_REQUEST['fec_finalx'] != NULL || $_REQUEST['fec_finalx'] != ''){
              $cond .= " AND a.fec_creaci BETWEEN '".$_REQUEST['fec_inicio']." 00:00:00' AND '".$_REQUEST['fec_finalx']." 23:59:59' ";
            }

            if($_REQUEST['tipser'] != NULL){
              $cond .= " AND a.tip_solici = '".$_REQUEST['tipser']."' ";
            }

            if($_REQUEST['regional'] != NULL){
              $cond .= " AND b.cod_region  = '".$_REQUEST['regional']."' ";
            }

            if($_SESSION['datos_usuario']['cod_perfil']=='2000'){
              $cond .= " AND d.cod_consec = '".$_SESSION['datos_usuario']['cod_consec']."' ";
            }

            $sql = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_usuari a
                      WHERE a.cod_usuari = '".self::$cod_usuari."' AND a.cod_filtro = 10";
            $query = new Consulta($sql, self::$conexion);
            $mMatriz = $query -> ret_matrix('a');
            if(count($mMatriz)>0){

              $sql = "SELECT a.cod_tercer FROM ".BASE_DATOS.".tab_tercer_emptra a
              WHERE a.cod_consec = '".self::$cod_consec."'";
              $query = new Consulta($sql, self::$conexion);
              $Tercer = $query->ret_matrix('a');
              
              if (!empty($Tercer)) {
                  // Extraer los valores de 'cod_tercer' en un nuevo array
                  $codTercerArray = [];
                  foreach ($Tercer as $row) {
                      $codTercerArray[] = $row['cod_tercer'];
                  }
              
                  // Convertir el nuevo array en una cadena con comas
                  $CodTransp = implode(",", $codTercerArray);
              
                  // Agregar la condición a tu consulta
                  $cond .= " AND a.cod_transp IN (" . $CodTransp . ")";
              }
            }

            $sql = "SELECT a.id as 'num_solici',
                           b.nom_tercer as 'nom_transp',
                           c.nom_asiste as 'tip_servic',
                           IF(e.nom_region IS NULL, 'NO ASIGNADA', e.nom_region) as 'nom_region',
                           a.nom_solici,
                           a.cor_solici,
                           a.cel_solici,
                           a.fec_creaci as 'fec_solici',
                           a.ind_aprofa,
                           a.usr_aprofa,
                           a.obs_aprofa,
                           a.fec_aprofa
                          FROM ".BASE_DATOS.".tab_asiste_carret a
                          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_transp = b.cod_tercer
                          INNER JOIN ".BASE_DATOS.".tab_formul_asiste c ON a.tip_solici = c.id
                          INNER JOIN ".BASE_DATOS.".tab_tercer_emptra d ON b.cod_tercer = d.cod_tercer
                          LEFT JOIN  ".BASE_DATOS.".tab_genera_region e ON d.num_region = e.cod_region
                          WHERE a.est_solici = 5 ".$cond."; ";
                          
              $query = new Consulta($sql, self::$conexion);
              $mMatriz = $query -> ret_matrix('a');
              $dataReturn = array("PorAprobar"=>array(),"Aprobados"=>array());
              $contporAproba = 1;
              $contAproba = 1;
              foreach ($mMatriz as $key => $datos) {
                if(!$datos['ind_aprofa']){
                  $btn = ' <button type="button" class="btn btn-info btn-sm" onclick="openModal(`'.$datos['num_solici'].'`)"><i class="fa fa-eye" aria-hidden="true"></i> APROBAR</button>';
                  $arr_poraprobar = array(
                    0 => $contporAproba,
                    1 => $datos['num_solici'],
                    2 => $datos['tip_servic'],
                    3 => $datos['nom_transp'],
                    4 => $datos['nom_region'],
                    5 => $datos['nom_solici'],
                    6 => $datos['cor_solici'],
                    7 => $datos['cel_solici'],
                    8 => $datos['fec_solici'],
                    9 => 'PENDIENTE POR FACTURAR',
                    10 => $btn,
                  );
                  array_push($dataReturn['PorAprobar'],(array)$arr_poraprobar);
                  $contporAproba++;

                }else{
                  $arr_aprobada = array(
                    0 => $contAproba,
                    1 => $datos['num_solici'],
                    2 => $datos['tip_servic'],
                    3 => $datos['nom_transp'],
                    4 => $datos['nom_region'],
                    5 => $datos['nom_solici'],
                    6 => $datos['cor_solici'],
                    7 => $datos['cel_solici'],
                    8 => $datos['fec_solici'],
                    9 => 'FACTURADO',
                    10 => $datos['fec_aprofa'],
                    11 => $datos['obs_aprofa'],
                    12 => $datos['usr_aprofa']
                  );
                  array_push($dataReturn['Aprobados'],(array)$arr_aprobada);
                  $contAproba++;
                }
              }
              echo json_encode(self::cleanArray($dataReturn));
          }

          private function getInfoSolicitud(){
            $sql = "SELECT a.id as 'num_solici',
                           b.nom_tercer as 'nom_transp',
                           c.nom_asiste as 'tip_servic',
                           IF(e.nom_region IS NULL, 'NO ASIGNADA', e.nom_region) as 'nom_region',
                           a.nom_solici,
                           a.cor_solici,
                           a.tel_solici,
                           a.cel_solici,
                           IF(f.nom_tercer IS NULL, 'NO ENCONTRADA', f.nom_tercer) as 'nom_asegur',
                           a.num_poliza,
                           a.fec_creaci as 'fec_solici',
                           a.fec_modifi as 'fec_finali',
                           a.ind_aprofa,
                           a.usr_aprofa,
                           a.obs_aprofa,
                           a.fec_aprofa
                          FROM ".BASE_DATOS.".tab_asiste_carret a
                          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_transp = b.cod_tercer
                          INNER JOIN ".BASE_DATOS.".tab_formul_asiste c ON a.tip_solici = c.id
                          INNER JOIN ".BASE_DATOS.".tab_tercer_emptra d ON b.cod_tercer = d.cod_tercer
                          LEFT JOIN  ".BASE_DATOS.".tab_genera_region e ON d.num_region = e.cod_region
                          LEFT JOIN  ".BASE_DATOS.".tab_tercer_tercer f ON a.ase_solici = f.cod_tercer
                          WHERE a.est_solici = 5 AND 
                                a.id='".$_REQUEST['num_solici']."'; ";
            $query = new Consulta($sql, self::$conexion);
            $mMatriz = $query -> ret_matrix('a');
            if(sizeof($mMatriz) > 0){
              $mMatriz['servicios'] = self::getServicios($_REQUEST['num_solici']);
            }
            echo json_encode($mMatriz);
          }


          private function getServicios($num_solici){
            $sql = "SELECT a.id,
                           a.cod_servic,
                           a.des_servic,
                           a.val_servic,
                           a.can_servic,
                           IF(a.tip_tarifa = 'diurna', b.tar_diurna, b.tar_noctur) as 'tal_unitar'
                      FROM ".BASE_DATOS.".tab_servic_solasi a
                      INNER JOIN ".BASE_DATOS.".tab_servic_asicar b ON a.cod_servic = b.id
                      WHERE a.cod_solasi='".$num_solici."'; ";
            $query = new Consulta($sql, self::$conexion);
            $mServicios = $query -> ret_matrix('a');
            return $mServicios;
          }

          private function aprobaAsiste(){
            $num_solici = $_REQUEST['num_solici'];
            $obs_aprofa = $_REQUEST['obs_aprofa'];
            $resp = [];
            $sql='UPDATE '.BASE_DATOS.'.tab_asiste_carret 
                        SET  
                          ind_aprofa = 1,
                          obs_aprofa = "'.$obs_aprofa.'",
                          usr_aprofa = "'.self::$cod_usuari.'",
                          fec_aprofa = NOW()
                        WHERE 
                          id = "'.$num_solici.'"';

            if(new Consulta($sql, self::$conexion)){
              $resp['codRespue'] = 1000;
              $resp['msgRespue'] = 'Facturación Registrada Correctamente';
            }else{
              $resp['codRespue'] = 2000;
              $resp['msgRespue'] = 'Ocurrio un error al guardar la información.';
            }

            echo json_encode(self::cleanArray($resp));

          }



        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaci?n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que ser? analizado por la funci?n
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

    new AjaxAprobaAsicar();
    
?>
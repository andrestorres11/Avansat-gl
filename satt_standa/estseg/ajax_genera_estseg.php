<?php
    /****************************************************************************
    NOMBRE:   AjaxGeneraEstSeg
    FUNCION:  Retorna todos los datos necesarios para construir la informaciï¿½n
    FECHA DE MODIFICACION: 16/09/2021
    CREADO POR: Ing. Cristian Andrï¿½s Torres
    MODIFICADO 
    ****************************************************************************/
    
    
    /*error_reporting(E_ALL);
    ini_set('display_errors', '1');*/

    class AjaxGeneraEstSeg
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
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "darTipoDocumento":
                  self::darTipoDocumento();
                  break;
                case "guardarSolicitud":
                    self::guardarSolicitud();
                    break;
                case "getRegistros":
                    self::getRegistros();
                    break;
                case "getListDocuments":
                  self::getListDocuments();
                    break;
                case "armaProcesoSolicitud":
                    self::armaProcesoSolicitud();
                    break;

                case "consultaCiudades":
                    self::consultaCiudades();
                    break;

                case "insReferenciaPyF":
                    self::insReferenciaPyF();
                    break;

                case "insReferenciaLaboral":
                    self::insReferenciaLaboral();
                    break;

                case "getParentesco":
                      self::getParentesco();
                      break;

                case "borrarReferenceFyP":
                        self::borrarReferenceFyP();
                        break;

                case "borrarReferenceLaboral":
                        self::borrarReferenceLaboral();
                        break;

                case "guardarGPS":
                      self::guardarGPS();
                      break;
                
                case "getLineas":
                      self::getLineas();
                        break;

                case "guardadoFase1":
                        self::guardadoFase1();
                        break;

                case "preguardado":
                    self::preguardado();
                    break;

                case "guardado":
                    self::guardado();
                    break;
                
                case "sendEmail":
                      self::sendEmail();
                      break;
                

            }
        }


        private function darTipoDocumento(){
          $sql="SELECT a.cod_tipdoc, a.nom_tipdoc FROM ".BASE_DATOS.".tab_genera_tipdoc a;";
          $resultado = new Consulta($sql, self::$conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $html .= '<option value="'.$registro['cod_tipdoc'].'">'.$registro['nom_tipdoc'].'</option>';
          }
          echo utf8_encode($html);
        }


        public function consultaCiudades(){
          $busqueda = $_REQUEST['key'];
          $sql="SELECT a.cod_ciudad, a.nom_ciudad, b.nom_depart, c.nom_paisxx FROM ".BASE_DATOS.".tab_genera_ciudad a
                   INNER JOIN ".BASE_DATOS.".tab_genera_depart b ON a.cod_depart = b.cod_depart
                   INNER JOIN ".BASE_DATOS.".tab_genera_paises c ON a.cod_paisxx = c.cod_paisxx
                   WHERE a.ind_estado = 1 AND a.nom_ciudad LIKE '%$busqueda%' ORDER BY a.nom_ciudad LIMIT 3";
      
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            $htmls='';
            foreach($resultados as $valor){
              $htmls.='<div><a class="suggest-element" data="'.$valor['cod_ciudad'].' - '.$valor['nom_ciudad'].'" id="'.$valor['cod_ciudad'].'">('.substr($valor['nom_paisxx'], 0, 3).') - '.substr($valor['nom_depart'], 0, 4).' - '.$valor['nom_ciudad'].'</a></div>';
            }
            echo utf8_decode($htmls);
      
        }

        function obtenerTransportadoraPerfil(){
          $sql = "SELECT c.cod_tercer, c.nom_tercer, c.dir_emailx,
                        CONCAT(c.num_telef1,' ', c.num_telef2) as 'num_telefo',
                        c.num_telmov
                  FROM ".BASE_DATOS.".tab_genera_usuari a 
                    INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b ON a.cod_perfil = b.cod_perfil
                    INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.clv_filtro = c.cod_tercer
                  WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."'";
          $consulta = new Consulta($sql, self::$conexion);
          $resultados = $consulta->ret_matriz();
          return $resultados[0];
          }

        function darCodigoSolicitud($cod_estseg){
          $sql = "SELECT a.cod_solici
                FROM ".BASE_DATOS.".tab_relaci_estseg a 
                WHERE a.cod_estseg = '".$cod_estseg."'";
          $consulta = new Consulta($sql, self::$conexion);
          $resultados = $consulta->ret_matriz();
          return $resultados[0][0];
        }

        function guardarSolicitud(){
            $emptra = $this->obtenerTransportadoraPerfil();
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $info=[];
            $cod_solici = self::valAutoincrement('tab_solici_estseg');
            $sql="INSERT INTO ".BASE_DATOS.".tab_solici_estseg(
                      cod_emptra, nom_solici, cor_solici,
                      tel_solici, cel_solici, usr_creaci,
                      fec_creaci
                    ) 
                    VALUES (
                      '".$emptra['cod_tercer']."','".$emptra['nom_tercer']."','".$emptra['dir_emailx']."',
                      '".$emptra['num_telefo']."','".$emptra['num_telmov']."', '".$usuari."',
                      NOW()
                    )";
            $query = new Consulta($sql, self::$conexion);
                         
            foreach($_POST['tip_docume'] as $key=>$value){
            $ult_conduc = self::valAutoincrement('tab_estudi_person');
            $sql = "INSERT INTO ".BASE_DATOS.".tab_estudi_person(
              cod_tipdoc, num_docume, nom_apell1,
              nom_apell2, nom_person, num_telmov,
              num_telmo2, num_telefo, dir_emailx,
              usr_creaci, fec_creaci
            ) 
            VALUES 
              (
                '".$_REQUEST['tip_docume'][$key]."','".$_REQUEST['num_docume'][$key]."','".$_REQUEST['nom_apell1'][$key]."',
                '".$_REQUEST['nom_apell2'][$key]."','".$_REQUEST['nom_person'][$key]."','".$_REQUEST['num_telmov'][$key]."',
                '".$_REQUEST['num_telmo2'][$key]."','','".$_REQUEST['dir_emailx'][$key]."',
                '".$usuari."',NOW()
              )";
            $query = new Consulta($sql, self::$conexion);
            
            $ult_vehicu = self::valAutoincrement('tab_estudi_vehicu');     
            $sql = "INSERT INTO tab_estudi_vehicu(
              num_placax, usr_creaci, fec_creaci
            ) 
            VALUES 
              (
                '".$_REQUEST['num_placax'][$key]."', '".$usuari."',NOW()
              )
            ";
               
            $query = new Consulta($sql, self::$conexion);

            $sql="INSERT INTO ".BASE_DATOS.".tab_relaci_estseg(
                      cod_solici, cod_conduc, cod_vehicu,
                      ind_estudi, ind_status, usr_creaci,
                      fec_creaci
                    ) 
                  VALUES (
                    '".$cod_solici."','".$ult_conduc."','".$ult_vehicu."',
                    'P','1','".$usuari."',
                    NOW()
                  )";
            $query = new Consulta($sql, self::$conexion);

            //Envio correo
            $subject = 'Solicitud de estudio de seguridad No. '.$cod_solici;
            $contenido = self::armaHtmlCreacion($cod_solici,1);
            self::enviarCorreo($cod_solici, 1, $subject, $contenido);  

            $contenido = self::armaHtmlCreacion($cod_solici,2);
            self::enviarCorreo($cod_solici, 2, $subject, $contenido);


            if($query){
              $info['status']=200;
              $info['response']= 'Se ha creado la Solicitud No. '.$cod_solici; 
            }else{
              $info['status']=100; 
            }
          }
            echo json_encode($info);
          }


          function getListDocuments(){
            $cod_estseg = $_REQUEST['cod_estseg'];
            $nom_documen = array('Licencia del vehiculo', 'Tarjeta de propiedad del trailer', 'Tecnomecanica', 'Soat', 'Licencia de transito (conductor)', 'Documento del propietario', 'Documento del conductor', 'Licencia del conductor', 'Planilla de seguridad social', 'Registro fotografico vehiculo', 'Poliza extracontractual');
            $nom_campoxs = array('fil_licveh', 'fil_tartra', 'fil_tecmec', 'fil_soatxx', 'fil_litcon', 'fil_cedpro', 'fil_cedcon', 'fil_liccon', 'fil_plsegs', 'fil_regveh', 'fil_polext');
            $nom_camobss = array('obs_licveh', 'obs_tartra', 'obs_tecmec', 'obs_soatxx', 'obs_litcon', 'obs_cedpro', 'obs_cedcon', 'obs_liccon', 'obs_plsegs', 'obs_regveh', 'obs_polext');
            $con_wherex = ' a.cod_estseg = "'.$cod_estseg.'"';
            foreach($nom_documen as $key=>$value){
              $html.=self::getInfoDocument('tab_estudi_docume', $nom_documen[$key], $nom_campoxs[$key], $nom_camobss[$key],$con_wherex);
            }
            echo utf8_decode($html);
          }

          function getInfoDocument($tablex,$nom_docume,$nom_campox,$nom_camobs,$con_wherex){
            $rut_general = URL_APLICA.'files/adj_estseg/';
            $sql = "SELECT a.".$nom_campox.", a.".$nom_camobs."
                    FROM ".BASE_DATOS.".".$tablex." a
                  WHERE ".$con_wherex." ";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            if(count($resultados)>0 && ($resultados[0][$nom_campox] != NULL || $resultados[0][$nom_campox] != '')){
              $html.='<tr>
                      <td>'.$nom_docume.'</td>
                      <td>'.$resultados[0][$nom_camobs].'</td>
                      <td><center><a href="'.$rut_general.''.$resultados[0][$nom_campox].'" class="btn btn-info info-color btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a></center></td>
                    </tr>';
            }
            return $html;
          }

          function guardarGPS(){
            $info=[];
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            if(!self::validaRegistroOperadorGPS($_REQUEST['nit_operad'])){
              $sql = "INSERT INTO ".BASE_DATOS.".tab_genera_opegps(
                        nom_operad, ind_usaidx, nit_operad,
                        nit_verifi, ind_estado, ind_cronxx,
                        ind_rndcxx, ind_intgps, url_gpsxxx,
                        apl_idxxxx, usr_creaci, fec_creaci
                      ) VALUES (
                        '".$_REQUEST['nom_operad']."', '0', '".$_REQUEST['nit_operad']."',
                        '".self::calcularDV($_REQUEST['nit_operad'])."', '1', '0',
                        '0', '0', '".$_REQUEST['url_gpsxxx']."',
                        '0', '".$usuari."', NOW()
                )";
              $query = new Consulta($sql, self::$conexion);
              if($query){
                $info['status']=200;
                $info['info'] = self::getOpeGps();
                $info['nuevoOpe'] = '<option value="'.$_REQUEST['nit_operad'].''.self::calcularDV($_REQUEST['nit_operad']).'" selected>'.$_REQUEST['nom_operad'].'</option>';
              }else{
                $info['status']=100;
                $info['response'] = 'No fue posible registrar el operador.';
              }
            }else{
                $info['status']=300;
                $info['response'] = 'El operador gps ya se encuentra registrado. No es posible su registro.';
            }
            echo json_encode($info);
          }

          function validaRegistroOperadorGPS($nit){
            $sql = "SELECT * FROM ".BASE_DATOS.".tab_genera_opegps a WHERE a.nit_operad = '".$nit."' AND a.nit_verifi = '".self::calcularDV($_REQUEST['nit_operad'])."'";
            $query = new Consulta($sql, self::$conexion);
            $resultados = $query->ret_matriz('a');
            if(count($resultados) > 0){
              return true;
            }
            return false;
          }

          function calcularDV($nit) {
            if (! is_numeric($nit)) {
                return false;
            }
            $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19, 
            8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
            $x = 0;
            $y = 0;
            $z = strlen($nit);
            $dv = '';
            for ($i=0; $i<$z; $i++) {
                $y = substr($nit, $i, 1);
                $x += ($y*$arr[$z-$i]);
            }
            $y = $x%11;
            
            if ($y > 1) {
                $dv = 11-$y;
                return $dv;
            } else {
                $dv = $y;
                return $dv;
            }
          }

          function getOpeGps(){
            $sql="SELECT a.nit_operad, a.nom_operad FROM ".BD_STANDA.".tab_genera_opegps a WHERE a.ind_estado = 1";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='';
            foreach ($resultados as $registro){
                $selected = '';
                if($cod_opegps != '' || $cod_opegps != NULL){
                  if($registro['nit_operad'] == $cod_opegps){
                  $selected = 'selected';
                  }
                }
                $html .= '<option value="'.$registro['nit_operad'].'" '.$selected.'>'.$registro['nom_operad'].'</option>';
            }
            return utf8_encode($html);
          }

          function consultaUltRegistro($table,$column){
            $sql="SELECT MAX($column) as 'ult_regist' FROM ".BASE_DATOS.".$table";
            $query = new Consulta($sql, self::$conexion);
            $ult_regist = $query -> ret_matrix('a')[0];
            if($ult_regist['ult_regist']==NULL){
              return 1;
            }
            return $ult_regist['ult_regist'];
          }

          function getTotalSolicitud($cod_solici){
            $sql="SELECT COUNT(*) as 'total' FROM ".BASE_DATOS.".tab_relaci_estseg a
                      WHERE a.cod_solici = '".$cod_solici."'";
            $query = new Consulta($sql, self::$conexion);
            $tot_solici= $query -> ret_matrix('a')[0];
            return $tot_solici['total'];  
          }

          function valAutoincrement($table){
            $sql="SELECT `AUTO_INCREMENT`
                    FROM  INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_SCHEMA = '".BASE_DATOS."'
                    AND   TABLE_NAME   = '".$table."'";
            $query = new Consulta($sql, self::$conexion);  
            $auto_increment = $query -> ret_matrix('a')[0];
            return $auto_increment['AUTO_INCREMENT'];
          }

          function getLineas(){
            $cod_marcax  = $_REQUEST['cod_marcax'];
            $sql="SELECT a.cod_lineax, a.nom_lineax FROM ".BASE_DATOS.".tab_vehige_lineas a
                      WHERE a.cod_marcax = '".$cod_marcax."'";
            $query = new Consulta($sql, self::$conexion);
            $registros = $query -> ret_matrix('a');
            $html = '';
            foreach($registros as $registro){
              $html.='<option value="'.$registro['cod_lineax'].'">'.$registro['nom_lineax'].'</option>';
            }
            echo utf8_decode($html);
          }

          function getRegistros(){
            $mPerms = self::getReponsability('jso_estseg');

            $sql = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a
                      WHERE a.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
            $query = new Consulta($sql, self::$conexion);
            $mMatriz = $query -> ret_matrix('a');

            if(count($mMatriz)>0){
              $filtro = $mMatriz[0]['clv_filtro'];
              $cond .= " AND a.cod_emptra = '".$filtro."' ";
            }else{
              if($_REQUEST['transportadora'] != NULL || $_REQUEST['transportadora'] != ''){
                $cond .= " AND a.cod_emptra = '".$_REQUEST['transportadora']."' ";
              }
            }

            if($_REQUEST['num_solici'] != NULL || $_REQUEST['num_solici'] != ''){
              $cond .= " AND a.cod_solici = '".$_REQUEST['num_solici']."' ";
            }

            if($_REQUEST['estado'] != NULL || $_REQUEST['estado'] != ''){
              $_REQUEST['tip_inform'] = $_REQUEST['estado'];
            }

            if($_REQUEST['fecha_inicio'] != NULL || $_REQUEST['fecha_inicio'] != '' && $_REQUEST['fecha_final'] != NULL || $_REQUEST['fecha_final'] != ''){
              $cond .= " AND a.fec_creaci BETWEEN '".$_REQUEST['fecha_inicio']." 00:00:00' AND '".$_REQUEST['fecha_final']." 23:59:59' ";
            }

            $sql = "SELECT a.cod_solici, a.nom_solici,
                          '' as 'tot_solici', 
                          '' as 'avance',
                          0 as 'tie_gestio'
                          FROM ".BASE_DATOS.".tab_solici_estseg a
                        WHERE 1=1 ".$cond."; ";
              $query = new Consulta($sql, self::$conexion);
              $mMatriz = $query -> ret_matrix('a');

              $registradas = array();
              $enprogreso = array();
              $finalizadas = array();
              $ind_pestana=0;
              $ind_fase=0; 
              foreach ($mMatriz as $key => $datos) {
                foreach ($datos as $campo => $valor) {
                  if($campo == "cod_solici"){
                     $btn = '<center><h6><span class="badge badge-pill badge-success c_pointer" data-dato="'.$valor.'" onclick="consInfoSolicitud(this)">'.$valor.'</span></h6></center>';
                     //$btn = '<a href="index.php?cod_servic=20210915&amp;window=central&amp;opcion=formEstSeguridad&cod_estseg='.$valor.'">'.$valor.'</a>';
                     $data[$key][] = $btn;
                  }else if($campo == "tot_solici"){
                    $data[$key][] = '<center>'.self::getTotalSolicitud($datos['cod_solici']).'</center>';
                  }
                  else if($campo == "avance"){
                     $data[$key][] = self::generaProgreso($datos['cod_solici']);
                     $ind_pestana = self::generaProgreso($datos['cod_solici'],1);
                     $ind_fase = self::getPestana($datos['cod_solici']);
                  }else if($campo == "tie_gestio"){
                     $data[$key][] = self::darTiempoTranscurrido($datos['cod_solici']);
                  }else{
                      $data[$key][] = $valor;	
                  }
                }

                if($ind_fase == 1 AND $ind_pestana<=0){
                  array_push($registradas,$data[$key]);
                }else if($ind_fase == 2 AND ($ind_pestana>=0 &&  $ind_pestana <=99)){
                  array_push($enprogreso,$data[$key]);
                }else if($ind_fase == 2 AND $ind_pestana==100){
                  array_push($finalizadas,$data[$key]);
                }

              }

              if($_REQUEST['tip_inform']==1){
                $return = array("data" => $registradas);
              }else if($_REQUEST['tip_inform']==2){
                $return = array("data" => $enprogreso);
              }else if($_REQUEST['tip_inform']==3){
                $return = array("data" => $finalizadas);
              }
             
              echo json_encode(self::cleanArray($return));
          }

          private function darTiempoTranscurrido($cod_solici){
            $sql="SELECT a.fec_creaci FROM ".BASE_DATOS.".tab_solici_estseg a
                  WHERE a.cod_solici = '".$cod_solici."'";
            $query = new Consulta($sql, self::$conexion);
            $mMatriz = $query -> ret_matrix('a')[0];
            $fecha_sol = new DateTime($mMatriz['fec_creaci']);
            $fecha_actual = new DateTime(date('Y-m-d H:i:s'));
            $diff = $fecha_sol->diff($fecha_actual);                         
            return self::get_format($diff);
          }

          private function getPestana($cod_solici){
            $sql = "SELECT a.ind_fasexx, a.ind_estudi, a.ind_status
                    FROM ".BASE_DATOS.".tab_relaci_estseg a
                    WHERE a.cod_solici = '".$cod_solici."' ORDER BY a.ind_fasexx DESC LIMIT 1; ";
            $query = new Consulta($sql, self::$conexion);
            $mMatriz = $query -> ret_matrix('a');

            $pestana = 1;
            foreach($mMatriz as $datos){
              if($datos['ind_fasexx']==1){
                $pestana = 1;
              }else if($datos['ind_fasexx']==2){
                $pestana = 2;
              }
            }
            return $pestana;
          }

          function get_format($df) {

            $str = '';
            $str .= ($df->invert == 1) ? ' - ' : '';
            if ($df->y > 0) {
                // years
                $str .= ($df->y > 1) ? $df->y . ' Años ' : $df->y . ' Año ';
            } if ($df->m > 0) {
                // month
                $str .= ($df->m > 1) ? $df->m . ' Meses ' : $df->m . ' Mes ';
            } if ($df->d > 0) {
                // days
                $str .= ($df->d > 1) ? $df->d . ' Dias ' : $df->d . ' Dia ';
            } if ($df->h > 0) {
                // hours
                $str .= ($df->h > 1) ? $df->h . ' Horas ' : $df->h . ' Hora ';
            } if ($df->i > 0) {
                // minutes
                $str .= ($df->i > 1) ? $df->i . ' Minutos ' : $df->i . ' Minuto ';
            } if ($df->s > 0) {
                // seconds
                $str .= ($df->s > 1) ? $df->s . ' Segundos ' : $df->s . ' Segundo ';
            }
        
            return $str;
        }


          private function generaProgreso($cod_solici,$ind_retval=0){
              $sql="SELECT a.cod_estseg, a.ind_estudi, a.ind_status FROM ".BASE_DATOS.".tab_relaci_estseg a
                      WHERE a.cod_solici = '".$cod_solici."' AND a.ind_status IN (1,2,3,4)";
              $query = new Consulta($sql, self::$conexion);
              $mMatriz = $query -> ret_matrix('a');
              $total = count($mMatriz);
              $terminados = 0;
              $pendientes = 0;
              foreach($mMatriz as $valor){
                if(($valor['ind_status'] == 3 || $valor['ind_status'] == 4) && ($valor['ind_estudi']=='A' || $valor['ind_estudi']=='R' || $valor['ind_estudi']=='C')){
                  $terminados++;
                }else{
                  $pendientes++;
                }
              }

              $avance = round($terminados/$total*100);

              $bg = 'danger';
              if($avance>=0 && $avance<40){
                $bg = 'danger';
              }else if($avance>=40 && $avance <60){
                $bg = 'warning';
              }else if($avance>=60 && $avance <75){
                $bg = 'info';
              }else{
                $bg = 'success';
              }
              if($avance>0){
                $html = '<div class="progress">
                          <div class="progress-bar bg-'.$bg.'" role="progressbar" style="width: '.$avance.'%" aria-valuenow="'.$avance.'" aria-valuemin="0" aria-valuemax="100">'.$avance.'%</div>
                      </div>';
              }else{
                $html = '<div class="progress">
                          <div class="progress-bar" role="progressbar" style="width: 100%;background-color: #e9ecef;color:#000;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">0%</div>
                      </div>';
              }

              if($ind_retval==1){
                return $avance;
              }else{
                return $html;
              }
             
          }

          private function armaProcesoSolicitud(){
            $cod_solici = $_REQUEST['cod_solici'];
            $sql="SELECT 
                      a.cod_estseg,
                      IFNULL(CONCAT(b.nom_apell1, ' ', b.nom_apell2, ' ', b.nom_person),'SIN REGISTRAR') as 'nom_conduc',
                      IFNULL(CONCAT(c.nom_apell1, ' ', c.nom_apell2, ' ', c.nom_person),'SIN REGISTRAR') as 'nom_poseed',
                      IFNULL(CONCAT(d.nom_apell1, ' ', d.nom_apell2, ' ', d.nom_person),'SIN REGISTRAR') as 'nom_propie',
                      e.num_placax,
                      a.ind_estudi
                  FROM ".BASE_DATOS.".tab_relaci_estseg a
                  LEFT JOIN ".BASE_DATOS.".tab_estudi_person b ON
                      a.cod_conduc = b.cod_segper
                  LEFT JOIN ".BASE_DATOS.".tab_estudi_person c ON
                      a.cod_poseed = c.cod_segper
                  LEFT JOIN ".BASE_DATOS.".tab_estudi_person d ON
                      a.cod_propie = d.cod_segper
                  LEFT JOIN ".BASE_DATOS.".tab_estudi_vehicu e ON
                      a.cod_vehicu = e.cod_segveh
                  WHERE a.cod_solici = '".$_REQUEST['cod_solici']."'
                  ;";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='<table class="table table-bordered" style="width:100%">
                      <thead>
                        <tr class="bk-green">
                          <th scope="col">No. Estudio</th>
                          <th scope="col">Estado</th>
                          <th scope="col">Descargar/Ver estudio</th>
                          <th scope="col">Documentación</th>
                          <th scope="col" style="min-width: 200px;">Conductor</th>
                          <th scope="col" style="min-width: 200px;">Poseedor</th>
                          <th scope="col" style="min-width: 200px;">Propietario</th>
                          <th scope="col">Vehiculo</th>
                          <th scope="col" style="min-width: 130px;">Gestión</th>
                          <th scope="col" style="min-width: 200px;">Observación</th>
                          
                        </tr>
                    </thead>
                    <tbody>
            ';
            foreach($resultados as $resultado){
              $bg='secondary';
              $name='Pendiente';
              $link='';
              if($resultado['ind_estudi']=='A'){
                $bg='success';
                $name='Aprobado';
              }else if($resultado['ind_estudi']=='R'){
                $bg='danger';
                $name='Rechazado';
              }else if($resultado['ind_estudi']=='C'){
                $bg='danger';
                $name='Cancelada';
              }

              $mPerms = self::getReponsability('jso_estseg');
              if($resultado['ind_estudi']=='P' && $mPerms->dat_estseg->ind_visibl==1){
                $link = '<a href="index.php?cod_servic=20210915&amp;window=central&amp;opcion=formEstSeguridad&cod_estseg='.$resultado['cod_estseg'].'">'.$resultado['cod_estseg'].'</a>';
              }else{
                $link = $resultado['cod_estseg'];
              }

              $html.='<tr>
                      <td class="text-center">'.$link.'</td>
                      <td style="vertical-align: middle;"><center><h6><span class="badge badge-pill badge-'.$bg.'">'.$name.'</span></h6></center></td>
                      <td style="vertical-align: middle;">'.self::buscaDocumento($resultado['cod_estseg'],$resultado['ind_estudi']).'</td>
                      <td style="vertical-align: middle;">'.self::btnVerDocumentos($resultado['cod_estseg']).'</td>
                      <td>'.$resultado['nom_conduc'].'</td>
                      <td>'.$resultado['nom_poseed'].'</td>
                      <td>'.$resultado['nom_propie'].'</td>
                      <td>'.$resultado['num_placax'].'</td>
                      <td>'.self::changeNullOthers(self::getUltRegistroHistorial($resultado['cod_estseg'])['nom_estado']).'</td>
                      <td>'.self::changeNullOthers(self::getUltRegistroHistorial($resultado['cod_estseg'])['obs_estado']).'</td>
                      </tr>';
            }

            $html.='</tbody>
                  </table>';
            echo $html;
          }

          private function getUltRegistroHistorial($cod_estseg){
            $sql = "SELECT 
                      b.nom_estado,
                      a.obs_estado
                    FROM ".BASE_DATOS.".tab_bitaco_estseg a 
                    INNER JOIN ".BASE_DATOS.".tab_estado_estseg b
                    ON a.cod_estado = b.cod_estado
                  WHERE a.cod_estseg  = '".$cod_estseg."' ORDER BY a.cod_bitaco DESC LIMIT 1";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            return $resultados[0];
          }

          private function buscaDocumento($cod_estseg, $ind_estudi){
            if($ind_estudi=='A' || $ind_estudi=='R'){
              $html.='<center><button class="btn btn-info info-color btn-sm" data-code="'.$cod_estseg.'" data-dato="'.self::darCodigoSolicitud($cod_estseg).'" onclick="openModalViewPdf(this)"><i class="fa fa-eye" aria-hidden="true"></i></button></center>';
            }else{
              $html.='<center>NO DISPONIBLE</center>';
            }
            return $html;
          }

          private function btnVerDocumentos($cod_estseg){
            $sql = "SELECT a.cod_docume
                  FROM ".BASE_DATOS.".tab_estudi_docume a
                  WHERE a.cod_estseg  = '".$cod_estseg."'";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            $html = '';
            if(count($resultados)>0){
              $html.='<center><button class="btn btn-info info-color btn-sm" data-code="'.$cod_estseg.'" data-dato="'.self::darCodigoSolicitud($cod_estseg).'" onclick="openModalViewDocuments(this)"><i class="fa fa-eye" aria-hidden="true"></i></button></center>';
            }else{
              $html.='<center>NO DISPONIBLE</center>';
            }
            return $html;
          }

          private function changeNullOthers($valor){
            if($valor==NULL || $valor==''){
              return 'NO REGISTRADO';
            }else{
              return $valor;
            }
          }

          private function getParentesco(){
            $info=[];
            $sql = "SELECT 
                      a.cod_parent,
                      a.nom_parent
                    FROM ".BASE_DATOS.".tab_genera_parent a 
                    WHERE a.ind_status = 1 AND a.cod_parent = '".$_REQUEST['cod_parent']."'";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a')[0];
            $info['nom_parent']=$resultados['nom_parent']; 
            echo json_encode(self::cleanArray($info));  
          }

          function insReferenciaPyF(){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $info=[];
            $sql = "INSERT INTO ".BASE_DATOS.".tab_estseg_refere(
                      nom_refere, cod_parent, nom_parent,
                      dir_domici, num_telefo, obs_refere,
                      usr_creaci, fec_creaci
                      ) 
                        VALUES 
                      (
                      '".$_REQUEST['nom_refereE']."','".$_REQUEST['cod_parentE']."','".$_REQUEST['nom_parentE']."',
                      '".$_REQUEST['dir_domiciE']."','".$_REQUEST['num_telefoE']."','".$_REQUEST['obs_refereE']."',
                      '".$usuari."',NOW()
                      )";
            $query = new Consulta($sql, self::$conexion);
            $ult_refere = self::consultaUltRegistro('tab_estseg_refere','cod_refere');
            $sql = "INSERT INTO ".BASE_DATOS.".tab_person_refere(
                      cod_person, cod_refere, tip_refere,
                      usr_creaci, fec_creaci
                      ) 
                        VALUES 
                      (
                      '".$_REQUEST['cod_personE']."','".$ult_refere."','".$_REQUEST['cod_refereE']."',
                      '".$usuari."',NOW()
                      )";
              $query = new Consulta($sql, self::$conexion);
              if($query){
                $info['status']=200;
                $info['person']=$_REQUEST['cod_personE'];
                $info['ultimo']=$ult_refere;
                $info['tip_refere']=$_REQUEST['cod_refereE'];
                $info['cod_identi']=$_REQUEST['cod_identiE'];
              }else{
                $info['status']=100; 
              } 
              echo json_encode($info);               

          }

          function insReferenciaLaboral(){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $info=[];
            $sql = "INSERT INTO ".BASE_DATOS.".tab_estseg_reflab(
                      cod_transp, nom_transp, num_telefo,
                      inf_sumini, num_viajes, usr_creaci,
                      fec_creaci
                      ) 
                        VALUES 
                      (
                      NULL,'".$_REQUEST['nom_transpE']."','".$_REQUEST['num_telefoE']."',
                      '".$_REQUEST['inf_suminiE']."','".$_REQUEST['num_viajesE']."','".$usuari."',NOW()
                      )";
            $query = new Consulta($sql, self::$conexion);
            $ult_refere = self::consultaUltRegistro('tab_estseg_reflab','cod_refere');
            $sql = "INSERT INTO ".BASE_DATOS.".tab_person_refere(
                      cod_person, cod_refere, tip_refere,
                      usr_creaci, fec_creaci
                      ) 
                        VALUES 
                      (
                      '".$_REQUEST['cod_personE']."','".$ult_refere."','".$_REQUEST['cod_refereE']."',
                      '".$usuari."',NOW()
                      )";
              $query = new Consulta($sql, self::$conexion);
              if($query){
                $info['status']=200;
                $info['person']=$_REQUEST['cod_personE'];
                $info['ultimo']=$ult_refere;
                $info['tip_refere']=$_REQUEST['cod_refereE'];
                $info['cod_identi']=$_REQUEST['cod_identiE'];
              }else{
                $info['status']=100; 
              } 
              echo json_encode($info);               

          }

          function borrarReferenceFyP(){
            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_person_refere 
                    WHERE cod_person = '".$_REQUEST['cod_person']."' AND cod_refere = '".$_REQUEST['cod_refere']."'; ";
            $query = new Consulta($sql, self::$conexion);
            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_estseg_refere 
                    WHERE cod_refere = '".$_REQUEST['cod_refere']."'; ";
            $query = new Consulta($sql, self::$conexion);
            if($query){
              $info['status']=200; 
            }else{
              $info['status']=100; 
            } 
            echo json_encode($info); 
          }

          function borrarReferenceLaboral(){
            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_person_refere 
                    WHERE cod_person = '".$_REQUEST['cod_person']."' AND cod_refere = '".$_REQUEST['cod_refere']."'; ";
            $query = new Consulta($sql, self::$conexion);
            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_estseg_reflab 
                    WHERE cod_refere = '".$_REQUEST['cod_refere']."'; ";
            $query = new Consulta($sql, self::$conexion);
            if($query){
              $info['status']=200; 
            }else{
              $info['status']=100; 
            } 
            echo json_encode($info); 
          }

          function guardadoFase1(){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $cod_estseg = $_REQUEST['cod_estseg'];
            $sql = "SELECT a.cod_docume FROM ".BASE_DATOS.".tab_estudi_docume a WHERE a.cod_estseg = '".$cod_estseg."'; ";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a')[0];
            
            //En caso de no existir un registro de la base de
            if(count($resultados) <= 0){
              $mSql = "INSERT INTO ".BASE_DATOS.".tab_estudi_docume (
                          cod_estseg, usr_creaci,fec_creaci
                      ) VALUES ('".$cod_estseg."', '".$usuari."', NOW() )";
              $query = new Consulta($mSql, self::$conexion);
            }

            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a')[0];
            $cod_docume = $resultados['cod_docume'];
            
            $arc_docini = array('fil_licveh', 'fil_tartra', 'fil_tecmec', 'fil_soatxx', 'fil_litcon', 'fil_cedpro', 'fil_cedcon', 'fil_liccon', 'fil_plsegs', 'fil_regveh', 'fil_polext');
            self::guardaArchivos($arc_docini, NULL, $cod_estseg, $_REQUEST, 'tab_estudi_docume', 'cod_estseg');

            if(isset($_REQUEST['ind_guarf1']) && $_REQUEST['ind_guarf1']==true){
              self::cambiaEstado($cod_estseg,2,'P',2,'');
              self::registraBitacora($cod_estseg,6,'Fase 1 Completada');
            }else{
              $cod_gestio = $_REQUEST['cod_gestio'];
              self::registraBitacora($cod_estseg, $cod_gestio,$_REQUEST['obs_gestio']);
              if($_REQUEST['ind_cancel']==1){
                self::cambiaEstado($cod_estseg, 1,'C',4,$_REQUEST['obs_gestio']);
                $cod_gestio=8;
                self::registraBitacora($cod_estseg,$cod_gestio,$_REQUEST['obs_gestio']);
                $info['redire']=1;
              }
            }

            
            $info['status']=200;
            $info['response']='Información registrada';
            echo json_encode($info);

          }

          function guardadoDeDocumentos($nom_tablax, $nom_campox, $rut_docume, $nom_camobs, $obs_docume, $con_wherex){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $sql="UPDATE ".BASE_DATOS.".".$nom_tablax."
              SET 
                ".$nom_campox." = '".$rut_docume."',
                ".$nom_camobs." = '".utf8_decode($obs_docume)."', 
                usr_modifi = '".$usuari."', 
                fec_modifi = NOW() 
              WHERE 
              ".$con_wherex."";
            $query = new Consulta($sql, self::$conexion);
          }



          function registraBitacora($cod_estseg, $cod_estado, $obs_estado){
              $usuari = $_SESSION['datos_usuario']['cod_usuari'];
              $sql = "INSERT INTO ".BASE_DATOS.".tab_bitaco_estseg (
                          cod_estseg, cod_estado, obs_estado,
                          usr_creaci, fec_creaci
                       ) VALUES (
                        '".$cod_estseg."', '".$cod_estado."', '".utf8_decode($obs_estado)."',
                        '".$usuari."', NOW() 
                        )";
              $query = new Consulta($sql, self::$conexion);
          }

          function cambiaEstado($cod_estseg, $ind_fase, $ind_estudi, $ind_status, $obs_estudi = ''){
              $rut_general = dirname(dirname(__DIR__)).'/satt_faro/files/adj_estseg/';
              $cod_solici = self::darCodigoSolicitud($cod_estseg);
              $usuari = $_SESSION['datos_usuario']['cod_usuari'];
              $sql="UPDATE ".BASE_DATOS.".tab_relaci_estseg
              SET 
                ind_fasexx = '".$ind_fase."',
                ind_estudi = '".$ind_estudi."',
                obs_estudi = '".$obs_estudi."',
                ind_status = '".$ind_status."', 
                usr_modifi = '".$usuari."', 
                fec_modifi = NOW() 
              WHERE 
              cod_estseg = '".$cod_estseg."'";
              $query = new Consulta($sql, self::$conexion);
              $subject = 'Cambio de estado SOL. '.$cod_solici.' - '.$cod_estseg;
              $contenido = self::armaHtmlCambioEstado($cod_solici,$cod_estseg);
              $files = array();


              $archive_file_name = NULL;

              if($ind_status==3){
                
                $con_wherex = ' a.cod_estseg = "'.$cod_estseg.'"';
                $nom_documen = array('Licencia_del_vehiculo', 'Tarjeta_de_propiedad_del_trailer', 'Tecnomecanica', 'Soat', 'Licencia_de_transito_conductor', 'Documento_del_propietario', 'Documento_del_conductor', 'Licencia_del_conductor', 'Planilla_de_seguridad social', 'Registro_fotografico_vehiculo', 'Poliza_extracontractual');
                $nom_campoxs = array('fil_licveh', 'fil_tartra', 'fil_tecmec', 'fil_soatxx', 'fil_litcon', 'fil_cedpro', 'fil_cedcon', 'fil_liccon', 'fil_plsegs', 'fil_regveh', 'fil_polext');
                foreach($nom_campoxs as $key=>$campo){
                  $sql = "SELECT a.".$campo."
                        FROM ".BASE_DATOS.".tab_estudi_docume a
                      WHERE ".$con_wherex." ";
                  $resultado = new Consulta($sql, self::$conexion);
                  $resultados = $resultado->ret_matriz('a');
                  if(count($resultados) > 0 && ($resultados[0][$campo] != NULL || $resultados[0][$campo] != '')){
                    $ext = explode(".", ($resultados[0][$campo]));
                    $info = array(
                      'name' => $nom_documen[$key].'.'.end($ext),
                      'archivo' => $resultados[0][$campo],
                    );
                    array_push($files, $info);
                  }
                }
              }
              self::enviarCorreo($cod_solici, 1, $subject, $contenido, $files);
          }

          
          /**
          * Guarda los archivos de acuerdo al array enviado del formulario en la tabla de la bd y los sube al servidor en la ruta indicada
          *
          * @return boolean true si la insercion no tuvo novedad
          * @param array $arr_archiv array con los nombre de los inputs
          * @param array $arr_nombdx array con los nombre de los campos de la bd (si es nulo se asume el mismo nombre de $arr_archiv como nombre de la columna de la bd)
          * @param string $cod_indent codigo de indentificador del registro de la bd
          * @param array $data datos enviados del formulario
          * @param string $nom_tablex nombre de la tabla donde hacer la actualizacion del registro
          * @param string $nom_campox nombre del campo para la condicion where
          */
          
          function guardaArchivos($arr_archiv, $arr_nombdx = NULL, $cod_indent, $data, $nom_tablex, $nom_campox){
            $cod_estseg = $data['cod_estseg'];
            $ruta = "../../satt_faro/files/adj_estseg/";
            $errores = false;
            foreach($arr_archiv as $key=>$archivo){
              if($_FILES[$archivo]['name'] != null || $_FILES[$archivo]['name'] != ''){
                $ext = explode(".", ($_FILES[$archivo]['name']));
                $nombre = $ruta.''.$cod_estseg.'_'.$archivo.'_'.time().".".end($ext);
                $nom_final = $cod_estseg.'_'.$archivo.'_'.time().".".end($ext);
                $destino_temporal=tempnam("tmp/","tmp");
                if (move_uploaded_file($_FILES[$archivo]['tmp_name'], $nombre)) {
                  $destin = $ruta.''.$cod_estseg.'_'.$archivo.'_pequenooo_'.time().".".end($ext);
                  if(end($ext)=='jpg' || end($ext)=='png' || end($ext)=='jpeg'){
                    if(self::redimensionarImagen($nombre, $destino_temporal, 640, 480, 50) ){
                      unlink($path);
                      $fp=fopen($nombre,"w");
                      fputs($fp,fread(fopen($destino_temporal,"r"),filesize($destino_temporal)));
                      fclose($fp);
                    } 
                  }
                  $separado = explode("_", $archivo);
                  $nom_obseori = 'obs_'.$separado[1];//obs_simitx -> obs_simcon
                  if($arr_nombdx[$key] != NULL){
                    $archivo = $arr_nombdx[$key];
                  }
                  $separado = explode("_", $archivo);
                  $nom_obse = 'obs_'.$separado[1];//obs_simitx -> obs_simcon
                  $con_wherex = $nom_campox.' = "'.$cod_indent.'" ';
                  self::guardadoDeDocumentos($nom_tablex, $archivo , $nom_final, $nom_obse, $data[$nom_obseori], $con_wherex);
                }else{
                  $errores = true;
                  $doc_nosubi = ' - '.$_FILES[$archivo]['name'];
                }
              }
            }
          }

          /**
           * Funcion para redimensionar imagenes
           *
           * @param string $origin Imagen origen en el disco duro ($_FILES["image1"]["tmp_name"])
           * @param string $destino Imagen destino en el disco duro ($destino=tempnam("tmp/","tmp");)
           * @param integer $newWidth Anchura mï¿½xima de la nueva imagen
           * @param integer $newHeight Altura mï¿½xima de la nueva imagen
           * @param integer $jpgQuality (opcional) Calidad para la imagen jpg
           * @return boolean true = Se ha redimensionada|false = La imagen es mas pequeï¿½a que el nuevo tamaï¿½o
           */
          function redimensionarImagen($origin,$destino,$newWidth,$newHeight,$jpgQuality=100)
          {
              // getimagesize devuelve un array con: anchura,altura,tipo,cadena de 
              // texto con el valor correcto height="yyy" width="xxx"
              $datos=getimagesize($origin);
              // comprobamos que la imagen sea superior a los tamaï¿½os de la nueva imagen
              if($datos[0]>$newWidth || $datos[1]>$newHeight)
              {
                  // creamos una nueva imagen desde el original dependiendo del tipo
                  if($datos[2]==1)
                      $img=imagecreatefromgif($origin);
                  if($datos[2]==2)
                      $img=imagecreatefromjpeg($origin);
                  if($datos[2]==3)
                      $img=imagecreatefrompng($origin);
          
                  // Redimensionamos proporcionalmente
                  if(rad2deg(atan($datos[0]/$datos[1]))>rad2deg(atan($newWidth/$newHeight)))
                  {
                      $anchura=$newWidth;
                      $altura=round(($datos[1]*$newWidth)/$datos[0]);
                  }else{
                      $altura=$newHeight;
                      $anchura=round(($datos[0]*$newHeight)/$datos[1]);
                  }
          
                  // creamos la imagen nueva
                  $newImage = imagecreatetruecolor($anchura,$altura);
          
                  // redimensiona la imagen original copiandola en la imagen
                  imagecopyresampled($newImage, $img, 0, 0, 0, 0, $anchura, $altura, $datos[0], $datos[1]);
          
                  // guardar la nueva imagen redimensionada donde indicia $destino
                  if($datos[2]==1)
                      imagegif($newImage,$destino);
                  if($datos[2]==2)
                      imagejpeg($newImage,$destino,$jpgQuality);
                  if($datos[2]==3)
                      imagepng($newImage,$destino);
          
                  // eliminamos la imagen temporal
                  imagedestroy($newImage);
          
                  return true;
              }
              return false;
          }
          
          
          function preguardado(){
            $cod_estseg = $_REQUEST['cod_estseg'];
            $cod_gestio = $_REQUEST['cod_gestio'];

            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            //Actualiza Informacion del conductor
            self::procesaConductor($_REQUEST);
            //Actualiza Informacion del vehiculo
            self::procesaVehiculo($_REQUEST);

            //Valida si el conductor es poseedor
            if($_REQUEST['check_conposeed']==1){
              self::combinaCondPoseePropie('cod_poseed', $_REQUEST['cod_conduc'], $_REQUEST['cod_estseg']);
            }else{
              self::procesaPoseedor($_REQUEST);
            }
            //Valida si el conductor es propietario
            if($_REQUEST['check_conpropiet']==1){
              self::combinaCondPoseePropie('cod_propie', $_REQUEST['cod_conduc'], $_REQUEST['cod_estseg']);
            }else{
              self::procesaPropietario($_REQUEST);
            }
            //Valida si el poseedor es propietario
            if($_REQUEST['check_pospropiet']==1){
              self::combinaCondPoseePropie('cod_propie', $_REQUEST['cod_poseed'], $_REQUEST['cod_estseg']);
            }else{
              self::procesaPropietario($_REQUEST);
            }
            self::registraBitacora($cod_estseg, $cod_gestio,$_REQUEST['obs_gestio']);

            $sql="UPDATE ".BASE_DATOS.".tab_relaci_estseg
                    SET 
                      cod_conduc = '".$_REQUEST['cod_conduc']."',
                      cod_vehicu = '".$_REQUEST['cod_vehicu']."', 
                      ind_estudi = 'P',
                      ind_status = '2', 
                      usr_modifi = '".$usuari."', 
                      fec_modifi = NOW() 
                WHERE 
                cod_estseg = '".$_REQUEST['cod_estseg']."'";

            if($_REQUEST['ind_cancel']==1){
              self::cambiaEstado($cod_estseg, 1,'C',4,$_REQUEST['obs_gestio']);
              $cod_gestio=8;
              self::registraBitacora($cod_estseg,$cod_gestio,$_REQUEST['obs_gestio']);
              $info['redire']=1;
            }

            

             $query = new Consulta($sql, self::$conexion);
              if($query){
                  $info['status']=200; 
              }else{
                  $info['status']=100; 
              } 
              echo json_encode($info);
          }

          function guardado(){
            $cod_estseg = $_REQUEST['cod_estseg'];
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            self::procesaConductor($_REQUEST);
            self::procesaVehiculo($_REQUEST);

            //Valida si el conductor es poseedor
            if($_REQUEST['check_conposeed']==1){
              self::combinaCondPoseePropie('cod_poseed', $_REQUEST['cod_conduc'], $_REQUEST['cod_estseg']);
            }else{
              self::procesaPoseedor($_REQUEST);
            }
            //Valida si el conductor es propietario
            if($_REQUEST['check_conpropiet']==1){
              self::combinaCondPoseePropie('cod_propie', $_REQUEST['cod_conduc'], $_REQUEST['cod_estseg']);
            }else{
              self::procesaPropietario($_REQUEST);
            }

            //Valida si el poseedor es propietario
            if($_REQUEST['check_pospropiet']==1){
              self::combinaCondPoseePropie('cod_propie', $_REQUEST['cod_poseed'], $_REQUEST['cod_estseg']);
            }else{
              self::procesaPropietario($_REQUEST);
            }
            $sql="UPDATE ".BASE_DATOS.".tab_relaci_estseg
                    SET 
                      cod_conduc = '".$_REQUEST['cod_conduc']."',
                      cod_vehicu = '".$_REQUEST['cod_vehicu']."', 
                      ind_estudi = '".$_REQUEST['ind_estudi']."',
                      usr_modifi = '".$usuari."', 
                      fec_modifi = NOW() 
                WHERE 
                  cod_estseg = '".$_REQUEST['cod_estseg']."'";
            self::registraBitacora($cod_estseg,7,'Fase 2 Completada');      
            self::cambiaEstado($_REQUEST['cod_estseg'],2,$_REQUEST['ind_estudi'],3,$_REQUEST['obs_estudi']);
            $array_generado = self::armaArrayInfo($_REQUEST['cod_estseg']);
            self::createXML($array_generado,'SOL_'.self::darCodigoSolicitud($_REQUEST['cod_estseg']).'_'.$_REQUEST['cod_estseg'].'_'.time().'.xml');
            //$toJSON = json_encode($array_generado);
            $emails = 
            $query = new Consulta($sql, self::$conexion);
              if($query){
                  $info['status']=200;
                  $info['redire']=1;
                  $info['cod_estseg']=$_REQUEST['cod_estseg'];
                  $info['emails'] = implode(",", self::darCorreos(self::darCodigoSolicitud($_REQUEST['cod_estseg']),1));
                  $url = explode("?", $_SERVER['HTTP_REFERER']);
                  $info['page'] = $url[0].'?cod_servic=20210915&window=central';
              }else{
                  $info['status']=100; 
              } 
              echo json_encode($info);
          }

          function createXML($array,$name){
            $ruta_name = "../../satt_faro/files/adj_estseg/xml/".$name;
            $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
            self::array_to_xml($array,$xml_data);
            $result = $xml_data->asXML($ruta_name);

            $server = 'sftpnatest.sterlingcommerce.com';
            $port = '22';
            $username = 'CEVA_CGCOLOMBIA';
            $username = 'Sterling$01t!';
            $remote_file = '/receive/'.$name;
            //self::uploadFTP($server, $port, 10000, $username, $username, $ruta_name, $remote_file);
          }

          function procesaConductor($data){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $ciu_expcon = NULL;
            $pai_reside = NULL;
            $dep_reside = NULL;
            $ciu_reside = NULL;
            if($data['lug_expcon'] != '' || $data['lug_expcon'] != '0 - No Registrada'){
              $ciu_expcon = self::separarCodigoCiudad($data['lug_expcon']);
            }
            if($data['ciu_conduc'] != '' || $data['ciu_conduc'] != '0 - No Registrada'){
              $dat_rescon = self::darDatosCiudad(self::separarCodigoCiudad($data['ciu_conduc']));
              $pai_reside = $dat_rescon['cod_paisxx'];
              $dep_reside = $dat_rescon['cod_depart'];
              $ciu_reside = $dat_rescon['cod_ciudad'];
            }
            $sql="UPDATE ".BASE_DATOS.".tab_estudi_person 
                    SET 
                      cod_tipdoc = '".$data['tip_doccon']."', 
                      ciu_expdoc = '".$ciu_expcon."', 
                      num_docume = '".$data['num_doccon']."', 
                      nom_apell1 = '".$data['nom_ap1con']."', 
                      nom_apell2 = '".$data['nom_ap2con']."', 
                      nom_person = '".$data['nom_nomcon']."', 
                      num_licenc = '".$data['num_licenc']."',
                      cod_catlic = '".$data['tip_catlic']."',
                      fec_venlic = '".date("Y-m-d", strtotime($data['fec_venlic']))."', 
                      nom_arlxxx = '".$data['nom_arlcon']."', 
                      nom_epsxxx = '".$data['nom_epscon']."', 
                      num_telmov = '".$data['num_mo1con']."', 
                      num_telefo = '".$data['num_telcon']."',
                      cod_paisxx = '".$pai_reside."', 
                      cod_depart = '".$dep_reside."', 
                      cod_ciudad = '".$ciu_reside."',
                      dir_domici = '".$data['dir_domcon']."', 
                      dir_emailx = '".$data['dir_emacon']."',
                      ind_precom = '".$data['pregu1con']."', 
                      val_compar = '".$data['val_comcon']."', 
                      ind_preres = '".$data['pregu2con']."',
                      val_resolu = '".$data['val_rescon']."', 
                      usr_modifi = '".$usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_segper = '".$data['cod_conduc']."' ";
            $query = new Consulta($sql, self::$conexion);

            $arc_conduc = array('fil_ritcon', 'fil_simcon', 'fil_procon', 'fil_runcon', 'fil_antcon');
            $nom_arcbdx = array('fil_conrit', 'fil_simitx', 'fil_procur', 'fil_runtxx', 'fil_ajudic');
            self::guardaArchivos($arc_conduc, $nom_arcbdx, $data['cod_conduc'], $data, 'tab_estudi_person', 'cod_segper');
            
          }
          
          function procesaPoseedor($data){
            
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $ciu_expcon = NULL;
            $pai_reside = NULL;
            $dep_reside = NULL;
            $ciu_reside = NULL;
            if($data['lug_exppos'] != '' || $data['lug_exppos'] != '0 - No Registrada'){
              $ciu_expcon = self::separarCodigoCiudad($data['lug_exppos']);
            }
            if($data['ciu_poseed'] != '' || $data['ciu_poseed'] != '0 - No Registrada'){
              $dat_respos = self::darDatosCiudad(self::separarCodigoCiudad($data['ciu_poseed']));
              $pai_reside = $dat_respos['cod_paisxx'];
              $dep_reside = $dat_respos['cod_depart'];
              $ciu_reside = $dat_respos['cod_ciudad'];
            }
            
            $dat_respos = self::darDatosCiudad(self::separarCodigoCiudad($data['ciu_poseed']));
            if($data['cod_poseed'] == NULL || $data['cod_poseed'] == ''){
              $cod_poseed = self::valAutoincrement('tab_estudi_person');
              $sql = "INSERT INTO ".BASE_DATOS.".tab_estudi_person(
                cod_tipdoc, num_docume, nom_apell1,
                nom_apell2, nom_person, num_telmov,
                num_telmo2, num_telefo, dir_emailx,
                ciu_expdoc, dir_domici, cod_paisxx,
                cod_depart, cod_ciudad, usr_creaci,
                fec_creaci
                ) 
                  VALUES 
                (
                  '".$data['tip_docpos']."','".$data['num_docpos']."','".$data['nom_ap1pos']."',
                  '".$data['nom_ap2pos']."','".$data['nom_nompos']."','".$data['num_mo1pos']."',
                  '".$data['num_mo2pos']."','".$data['num_telcon']."','".$data['dir_emapos']."',
                  '".$ciu_expcon."', '".$data['dir_dompos']."', '".$pai_reside."',
                  '".$dep_reside."', '".$ciu_reside."','".$usuari."',
                  NOW()
                )";
                $query = new Consulta($sql, self::$conexion);
            }else{
              $sql="UPDATE ".BASE_DATOS.".tab_estudi_person
                    SET 
                      cod_tipdoc = '".$data['tip_docpos']."',
                      num_docume = '".$data['num_docpos']."', 
                      nom_apell1 = '".$data['nom_ap1pos']."',
                      nom_apell2 = '".$data['nom_ap2pos']."', 
                      nom_person = '".$data['nom_nompos']."', 
                      num_telmov = '".$data['num_mo1pos']."',
                      num_telefo = '".$data['num_telpos']."',
                      dir_emailx = '".$data['dir_emapos']."',
                      ciu_expdoc = '".$ciu_expcon."',
                      dir_domici = '".$data['dir_dompos']."',
                      cod_paisxx = '".$pai_reside."',
                      cod_depart = '".$dep_reside."',
                      cod_ciudad = '".$ciu_reside."',
                      usr_modifi = '".$usuari."',
                      fec_modifi = NOW()
                WHERE 
                  cod_segper = '".$data['cod_poseed']."'";
                  $query = new Consulta($sql, self::$conexion);
                  $cod_poseed  = $data['cod_poseed'];
            }

            $arc_poseed = array('fil_ritpos', 'fil_simpos', 'fil_propos', 'fil_runpos', 'fil_antpos');
            $nom_arcbdx = array('fil_conrit', 'fil_simitx', 'fil_procur', 'fil_runtxx', 'fil_ajudic');
            self::guardaArchivos($arc_poseed, $nom_arcbdx, $cod_poseed, $data, 'tab_estudi_person', 'cod_segper');


            $sql="UPDATE ".BASE_DATOS.".tab_relaci_estseg
                  SET 
                    cod_poseed = '".$cod_poseed."',
                    usr_modifi = '".$usuari."', 
                    fec_modifi = NOW() 
                  WHERE 
                    cod_estseg = '".$data['cod_estseg']."'";
            $query = new Consulta($sql, self::$conexion);
            
          }

          
          function procesaPropietario($data){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $ciu_exppro = NULL;
            $pai_reside = NULL;
            $dep_reside = NULL;
            $ciu_reside = NULL;
            
            if($data['lug_exppro'] != '' || $data['lug_exppro'] != '0 - No Registrada'){
              $ciu_exppro = self::separarCodigoCiudad($data['lug_exppro']);
            }
            if($data['ciu_propie'] != '' || $data['ciu_propie'] != '0 - No Registrada'){
              $dat_respro = self::darDatosCiudad(self::separarCodigoCiudad($data['ciu_propie']));
              $pai_reside = $dat_respro['cod_paisxx'];
              $dep_reside = $dat_respro['cod_depart'];
              $ciu_reside = $dat_respro['cod_ciudad'];
            }

            if($data['cod_propie']==NULL || $data['cod_propie']==''){
              $cod_propie = self::valAutoincrement('tab_estudi_person');
              $sql = "INSERT INTO ".BASE_DATOS.".tab_estudi_person(
                cod_tipdoc, num_docume, nom_apell1,
                nom_apell2, nom_person, num_telmov,
                num_telmo2, num_telefo, dir_emailx,
                ciu_expdoc, dir_domici, cod_paisxx,
                cod_depart, cod_ciudad, usr_creaci,
                fec_creaci
                ) 
                  VALUES 
                (
                  '".$data['tip_docpro']."','".$data['num_docpro']."','".$data['nom_ap1pro']."',
                  '".$data['nom_ap2pro']."','".$data['nom_nompro']."','".$data['num_mo1pro']."',
                  '".$data['num_mo2pro']."','".$data['num_telpro']."','".$data['dir_emapro']."',
                  '".$ciu_exppro."', '".$data['dir_dompro']."', '".$pai_reside."',
                  '".$dep_reside."', '".$ciu_reside."','".$usuari."',
                  NOW()
                )";
            }else{
              $sql="UPDATE ".BASE_DATOS.".tab_estudi_person
                    SET 
                      cod_tipdoc = '".$data['tip_docpro']."',
                      num_docume = '".$data['num_docpro']."', 
                      nom_apell1 = '".$data['nom_ap1pro']."',
                      nom_apell2 = '".$data['nom_ap2pro']."', 
                      nom_person = '".$data['nom_nompro']."', 
                      num_telmov = '".$data['num_mo1pro']."',
                      num_telmo2 = '".$data['num_mo2pro']."',
                      num_telefo = '".$data['num_telpro']."',
                      dir_emailx = '".$data['dir_emapro']."',
                      ciu_expdoc = '".$ciu_exppro."',
                      dir_domici = '".$data['dir_dompro']."',
                      cod_paisxx = '".$pai_reside."',
                      cod_depart = '".$dep_reside."',
                      cod_ciudad = '".$ciu_reside."',
                      usr_modifi = '".$usuari."',
                      fec_modifi = NOW()
                WHERE 
                  cod_segper = '".$data['cod_propie']."'";
                  $cod_propie  = $data['cod_propie'];
            }
           
            $query = new Consulta($sql, self::$conexion);

            $arc_propie = array('fil_ritpro', 'fil_simpro', 'fil_propro', 'fil_runpro', 'fil_antpro');
            $nom_arcbdx = array('fil_conrit', 'fil_simitx', 'fil_procur', 'fil_runtxx', 'fil_ajudic');
            self::guardaArchivos($arc_propie, $nom_arcbdx, $cod_propie, $data, 'tab_estudi_person', 'cod_segper');

            $sql="UPDATE ".BASE_DATOS.".tab_relaci_estseg
                  SET 
                    cod_propie = '".$cod_propie."',
                    usr_modifi = '".$usuari."', 
                    fec_modifi = NOW() 
                  WHERE 
                    cod_estseg = '".$data['cod_estseg']."'";
            $query = new Consulta($sql, self::$conexion);
            
          }
          
          function procesaVehiculo($data){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $sql="UPDATE ".BASE_DATOS.".tab_estudi_vehicu 
                    SET 
                      num_placax = '".$data['num_placax']."', 
                      num_remolq = '".$data['num_remolq']."', 
                      cod_marcax = '".$data['cod_marcax']."',
                      cod_lineax = '".$data['cod_lineax']."',
                      ano_modelo = '".$data['ano_modelo']."', 
                      cod_colorx = '".$data['cod_colorx']."', 
                      cod_carroc = '".$data['cod_carroc']."', 
                      num_config = '".$data['num_config']."',
                      num_chasis = '".$data['num_chasis']."',
                      num_motorx = '".$data['num_motorx']."',
                      num_soatxx = '".$data['num_soatxx']."',
                      fec_vigsoa = '".date("Y-m-d", strtotime($data['fec_vigsoa']))."',
                      num_lictra = '".$data['num_lictra']."',
                      cod_opegps = '".$data['cod_opegps']."', 
                      usr_gpsxxx = '".$data['usr_gpsxxx']."', 
                      clv_gpsxxx = '".$data['clv_gpsxxx']."', 
                      url_gpsxxx = '', 
                      idx_gpsxxx = '".$data['idx_gpsxxx']."',
                      obs_opegps = '".$data['obs_opegps']."',
                      fre_opegps = '".$data['fre_opegps']."',
                      ind_precom = '".$data['pre_comveh']."', 
                      val_compar = '".$data['val_comveh']."', 
                      ind_preres = '".$data['ind_preres']."',
                      val_resolu = '".$data['val_resveh']."', 
                      usr_modifi = '".$usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_segveh = '".$data['cod_vehicu']."' ";
            $query = new Consulta($sql, self::$conexion);
            $arc_vehicu = array('fil_congps', 'fil_conrit', 'fil_runtxx', 'fil_compar');
            self::guardaArchivos($arc_vehicu, null, $data['cod_vehicu'], $data, 'tab_estudi_vehicu', 'cod_segveh');
            
          }
          

          function combinaCondPoseePropie($nam_column, $valor, $cod_estseg){
            $usuari = $_SESSION['datos_usuario']['cod_usuari'];
            if($valor != '' || $valor != NULL){
              $sql="UPDATE ".BASE_DATOS.".tab_relaci_estseg
                    SET 
                      ".$nam_column." = '".$valor."',
                      usr_modifi = '".$usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_estseg = '".$cod_estseg."'";
              $query = new Consulta($sql, self::$conexion);
            }
          }

          function separarCodigoCiudad($dato){
            $cod_ciudad = explode(" ", $dato);
            return trim($cod_ciudad[0]);
          }

          function darDatosCiudad($cod_ciudad){
            $sql = "SELECT cod_paisxx, cod_depart, cod_ciudad
                      FROM ".BASE_DATOS.".tab_genera_ciudad
                    WHERE cod_ciudad = '".$cod_ciudad."'; ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];
            return $resultado;
          }


          /* Arma Array Info */

          function armaArrayInfo($cod_estseg){
            $infoGen = self::darInfoSoliciGeneral($cod_estseg);
            $dataSol = array(
              'application_number' => $infoGen['cod_solici'],
              'company_code' => $infoGen['cod_emptra'],
              'company_name' => $infoGen['nom_solici'],
              'company_email' => $infoGen['cor_solici'],
              'company_phone' => $infoGen['tel_solici'],
              'company_mobile' => $infoGen['cel_solici'],
              'security_study_code' => $cod_estseg,
              'driver' => self::armaArrayPerson($infoGen['cod_conduc'],1),
              'holder' => self::armaArrayPerson($infoGen['cod_poseed']),
              'owner' => self::armaArrayPerson($infoGen['cod_propie']),
              'vehicle' => self::armaArrayVehicu($infoGen['cod_vehicu']),
              'status_study' => $infoGen['ind_estudi'],
              'observation_study' => $infoGen['obs_estudi'],
              'user_study' => $infoGen['usr_estudi']
            );

            return $dataSol;
          }

          function darInfoSoliciGeneral($cod_estseg){
            $sql="SELECT b.cod_solici, b.cod_emptra, b.nom_solici, 
                         b.cor_solici, b.tel_solici, b.cel_solici,
                         a.cod_conduc, a.cod_poseed, a.cod_propie,
                         a.cod_vehicu, a.ind_estudi, a.obs_estudi,
                         a.usr_estudi
                  FROM 
                      ".BASE_DATOS.".tab_relaci_estseg a
                  INNER JOIN ".BASE_DATOS.".tab_solici_estseg b ON
                    a.cod_solici = b.cod_solici
                  WHERE a.cod_estseg = '".$cod_estseg."'";
            $query = new Consulta($sql, self::$conexion);
            $resultados = $query -> ret_matrix('a')[0];
            return $resultados;  
          }

          function armaArrayPerson($cod_person,$ind_person = 0){
            $archivos = array('rit','simit','procuraduria','runt','ajudiciales');
            $nom_camfil = array('fil_conrit','fil_simitx','fil_procur','fil_runtxx','fil_ajudic');
            $obs_camfil = array('obs_conrit','obs_simitx','obs_procur','obs_runtxx','obs_ajudic');
            $con_where = ' cod_segper = '.$cod_person.' ';
            $sql = "SELECT
                      a.cod_tipdoc, b.nom_tipdoc, a.num_docume,
                      a.ciu_expdoc,
                      a.nom_apell1, a.nom_apell2, a.nom_person,
                      a.num_licenc, a.cod_catlic, a.fec_venlic, a.nom_arlxxx,
                      a.nom_epsxxx, a.num_telmov, a.num_telmo2,
                      a.num_telefo, a.cod_paisxx, a.cod_depart,
                      a.cod_ciudad, d.nom_ciudad, a.dir_domici,
                      a.dir_emailx
                    FROM ".BASE_DATOS.".tab_estudi_person a
                INNER JOIN ".BASE_DATOS.".tab_genera_tipdoc b ON
                    a.cod_tipdoc = b.cod_tipdoc
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d ON
                    a.cod_ciudad = d.cod_ciudad AND a.cod_depart = d.cod_depart AND a.cod_paisxx = d.cod_depart
                    WHERE a.cod_segper = '".$cod_person."'";
              $query = new Consulta($sql, self::$conexion);
              $resultados = $query -> ret_matrix('a')[0];
              $dataPerson = array(
                'document_typeID' =>  array(
                                          'id' => $resultados['cod_tipdoc'],
                                          'name' => $resultados['nom_tipdoc']
                ),
                'document_number' => $resultados['num_docume'],
                'expedicion_city' => self::obtenerInfoCiudad($resultados['ciu_expdoc']),
                'surname' => $resultados['nom_apell1'],
                'second_surname' => $resultados['nom_apell2'],
                'names' => $resultados['nom_person'],
                'mobile1' => $resultados['num_telmov'],
                'mobile2' => $resultados['num_telmo2'],
                'phone' => $resultados['num_telefo'],
                'residence_city' => self::obtenerInfoCiudad($resultados['cod_ciudad'], $resultados['cod_depart'], $resultados['cod_paisxx']),
                'address' => $resultados['dir_domici'],
                'email' => $resultados['dir_emailx'],
                'files' => self::getFiles('tab_estudi_person', $archivos, $nom_camfil, $obs_camfil, $con_where),
              );

              if($ind_person==1){
                $arr_conduc = array(
                  'license_number' => $resultados['num_licenc'],
                  'license_category' => $resultados['cod_catlic'],
                  'license_expiration' => $resultados['fec_venlic'],
                  'name_arl' => $resultados['nom_arlxxx'],
                  'name_eps' => $resultados['nom_epsxxx'],
                  'family_referrals' => self::armaReferences($cod_person, 'F'),
                  'personal_referrals' => self::armaReferences($cod_person, 'P'),
                  'laboral_referrals' => self::armaReferenceslaboral($cod_person),
                );
                $dataPerson = (array)$dataPerson + (array)$arr_conduc;
              }
              return $dataPerson;
          }

          function armaArrayVehicu($cod_vehicu){
            $archivos = array('fil_gps','rit','runt','comparendos');
            $nom_camfil = array('fil_congps','fil_conrit','fil_runtxx','fil_compar');
            $obs_camfil = array('obs_congps','obs_conrit','obs_runtxx','obs_compar');
            $con_where = ' cod_segveh = '.$cod_vehicu.' ';
            $sql = "SELECT
                      a.num_placax, a.num_remolq, a.ano_modelo, a.cod_colorx, a.cod_marcax, e.nom_marcax, a.cod_lineax, f.nom_lineax,
                      b.nom_colorx, a.cod_carroc, c.nom_carroc, a.num_config, d.nom_config, a.num_chasis, a.num_motorx, a.num_soatxx,
                      a.fec_vigsoa, a.num_lictra, g.cod_opegps, g.nit_operad, g.nom_operad, a.usr_gpsxxx, a.clv_gpsxxx, a.url_gpsxxx,
                      a.idx_gpsxxx, a.obs_opegps, a.fre_opegps
                    FROM ".BASE_DATOS.".tab_estudi_vehicu a
              LEFT JOIN ".BASE_DATOS.".tab_vehige_colore b ON 
                    a.cod_colorx = b.cod_colorx
              LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc c ON
                    a.cod_carroc = c.cod_carroc
              LEFT JOIN ".BASE_DATOS.".tab_vehige_config d ON
                    a.num_config = d.num_config
              LEFT JOIN ".BASE_DATOS.".tab_genera_marcas e ON
                    a.cod_marcax = e.cod_marcax
              LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas f ON
                    a.cod_lineax = f.cod_lineax AND a.cod_marcax = f.cod_marcax
              LEFT JOIN ".BD_STANDA.".tab_genera_opegps g ON
                    a.cod_opegps = g.nit_operad
                    WHERE a.cod_segveh = '".$cod_vehicu."'";
              $query = new Consulta($sql, self::$conexion);
              $resultados = $query -> ret_matrix('a')[0];
              $dataVehicu = array(
                'placa' => $resultados['num_placax'],
                'trailer_number' => $resultados['num_remolq'],
                'model' => $resultados['ano_modelo'],
                'color' => array(
                    'code' => $resultados['cod_colorx'],
                    'name' => $resultados['nom_colorx']
                ),
                'brand' => array(
                  'code' => $resultados['cod_marcax'],
                  'name' => $resultados['nom_marcax']
                ),
                'line' => array(
                  'code' => $resultados['cod_lineax'],
                  'name' => $resultados['nom_lineax']
                ),
                'bodywork' => array(
                  'code' => $resultados['cod_carroc'],
                  'name' => $resultados['nom_carroc']
                ),
                'configuration' => array(
                  'code' => $resultados['num_config'],
                  'name' => $resultados['nom_config']
                ),
                'chassis_number' => $resultados['num_chasis'],
                'engine_number' => $resultados['num_motorx'],
                'soat_policy_number' => $resultados['num_soatxx'],
                'soat_policy_date' => $resultados['fec_vigsoa'],
                'licence_number' => $resultados['num_lictra'],
                'gps' => array(
                  'code' => $resultados['cod_opegps'],
                  'document_number' => $resultados['nit_operad'],
                  'name' => $resultados['nom_operad'],
                  'username' => $resultados['usr_gpsxxx'],
                  'password' => $resultados['clv_gpsxxx'],
                  'url' => $resultados['url_gpsxxx'],
                  'id' =>  $resultados['idx_gpsxxx'],
                  'observation' => $resultados['obs_opegps'],
                  'frequency' => $resultados['fre_opegps']
                ),
                'files' => self::getFiles('tab_estudi_vehicu', $archivos, $nom_camfil, $obs_camfil, $con_where)
              );
              return $dataVehicu;
          }

          function getFiles($nom_table,$archivos,$nom_camfil,$obs_camfil,$con_where){
            $rut_general = URL_APLICA.'files/adj_estseg/';
            $arrayData = array();
            foreach($archivos as $key => $archivo){
              $sql = "SELECT
                      ".$nom_camfil[$key].", ".$obs_camfil[$key]."
                    FROM ".BASE_DATOS.".".$nom_table."
                    WHERE ".$con_where."";
              $query = new Consulta($sql, self::$conexion);
              $resultados = $query -> ret_matrix('a')[0];
              $datos = array(
                'url_file' => $rut_general.''.$resultados[$nom_camfil[$key]],
                'observation' => $resultados[$obs_camfil[$key]]
              );
              $arrayData[$archivo] = $datos;
            }
            return $arrayData;
          }

          function obtenerInfoCiudad($cod_ciudad, $cod_depart = NULL, $cod_paisxx = NULL){
            $sql = "SELECT a.cod_ciudad, a.nom_ciudad, b.cod_depart,
                           b.nom_depart, c.cod_paisxx, c.nom_paisxx
                    FROM ".BASE_DATOS.".tab_genera_ciudad a
              INNER JOIN ".BASE_DATOS.".tab_genera_depart b ON
                    a.cod_depart = b.cod_depart
              INNER JOIN ".BASE_DATOS.".tab_genera_paises c ON
                    a.cod_paisxx = c.cod_paisxx
                    WHERE a.cod_ciudad = '".$cod_ciudad."'";
            if($cod_depart != NULL){
              $sql .= "AND a.cod_depart = '".$cod_depart."'";
            }

            if($cod_paisxx != NULL){
              $sql .= "AND a.cod_paisxx = '".$cod_paisxx."'";
            }

            $query = new Consulta($sql, self::$conexion);
            $resultados = $query -> ret_matrix('a')[0];

            $array = array(
              'city' => array(
                'code' => $resultados['cod_ciudad'],
                'name' => $resultados['nom_ciudad']
              ),
              'departament' => array(
                'code' => $resultados['cod_depart'],
                'name' => $resultados['nom_depart']
              ),
              'country' => array(
                'code' => $resultados['cod_paisxx'],
                'name' => $resultados['nom_paisxx']
              )
              );

              return $array;
          }

          function armaReferences($cod_person, $tip_refere){
            $sql = "SELECT 
                      b.nom_refere, b.nom_parent, b.dir_domici, 
                      b.num_telefo 
                    FROM 
                      ".BASE_DATOS.".tab_person_refere a, 
                      ".BASE_DATOS.".tab_estseg_refere b 
                    WHERE 
                      a.cod_person = '".$cod_person."'
                      AND a.cod_refere = b.cod_refere
                      AND a.tip_refere = '".$tip_refere."'";
            $query = new Consulta($sql, self::$conexion);
            $resultados = $query -> ret_matrix('a');
            $arrayData = array();
            foreach($resultados as $key => $resultado){
              $datos = array(
                'reference_name' => $resultado['nom_refere'],
                'reference_relationship' => $resultado['nom_parent'],
                'address' => $resultado['dir_domici'],
                'phone' => $resultado['num_telefo']
              );
              array_push($arrayData,$datos);
            }
            return $arrayData;
          }

          function armaReferenceslaboral($cod_person){
            $sql = "SELECT 
                      b.nom_transp, b.num_telefo, b.inf_sumini, 
                      b.num_viajes 
                    FROM 
                      ".BASE_DATOS.".tab_person_refere a, 
                      ".BASE_DATOS.".tab_estseg_reflab b 
                    WHERE 
                      a.cod_person = '".$cod_person."'
                      AND a.cod_refere = b.cod_refere
                      AND a.tip_refere = 'L'";
            $query = new Consulta($sql, self::$conexion);
            $resultados = $query -> ret_matrix('a');
            $arrayData = array();
            foreach($resultados as $key=>$resultado){
              $datos = array(
                'company_name' => $resultado['nom_transp'],
                'phone' => $resultado['num_telefo'],
                'inf_sumini' => $resultado['inf_sumini'],
                'travels' => $resultado['num_viajes']
              );
              array_push($arrayData,$datos);
            }
            return $arrayData;
          }

        function array_to_xml( $data, &$xml_data ) {
          foreach( $data as $key => $value ) {
              if( is_array($value) ) {
                  if( is_numeric($key) ){
                      $key = 'item'.$key; //dealing with <0/>..<n/> issues
                  }
                  $subnode = $xml_data->addChild($key);
                  self::array_to_xml($value, $subnode);
              } else {
                  $xml_data->addChild("$key",htmlspecialchars("$value"));
              }
           }
        }


        function uploadFTP($server, $port, $timeout, $username, $password, $local_file, $remote_file){
        $conn_id = ftp_connect($server);

        // login with username and password
        $login_result = ftp_login($conn_id, $username, $password);

        // upload a file
        if (ftp_put($conn_id, $remote_file, $local_file, FTP_ASCII)) {
            echo "successfully uploaded $local_file\n";
            exit;
        } else {
            echo "There was a problem while uploading $local_file\n";
            exit;
            }
        // close the connection
        ftp_close($conn_id);

      }


        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaciÃƒÂ³n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que serÃƒÂ¡ analizado por la funciÃƒÂ³n
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

        private function darCorreos($cod_solici,$ind_retorn){
          if($ind_retorn==1){
            $sql = "SELECT b.dir_emailx
                      FROM ".BASE_DATOS.".tab_solici_estseg a
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                        ON a.cod_emptra = b.cod_tercer
                    WHERE a.cod_solici = '".$cod_solici."'; ";
          }else{
            $sql = "SELECT a.dir_emailx
                      FROM ".BASE_DATOS.".tab_genera_concor a
                    WHERE a.num_remdes = ''; ";
          }
          $query = new Consulta($sql, self::$conexion);
          $resultado = $query->ret_matriz('a')[0];
          return $resultado;
        }

        private function enviarCorreo($cod_solici, $ind_retorn, $subject, $contenido, $files = NULL) {
            //Trae los correos segun el caso
            $emailsTotal = self::darCorreos($cod_solici,$ind_retorn);
            $emails = explode(",", $emailsTotal['dir_emailx']);
            $tmpl_file = URL_ARCHIV_STANDA.'ctorres/sat-gl-2015/satt_standa/estseg/planti/template-email.html';
            $logo = LOGOFARO;
            $ano = date('Y');
            $thefile = implode("", file( $tmpl_file ) );
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"".$thefile."\";";
            eval( $thefile );
            $mHtml = $r_file;

            require_once(URL_ARCHIV_STANDA."ctorres/sat-gl-2015/satt_standa/planti/class.phpmailer.php");
            $mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';
            $mail->Host = "localhost";
            $mail->From = 'supervisores@eltransporte.org';
            $mail->FromName = 'EST. SEGURIDAD';
            $mail->Subject = utf8_decode($subject) ;
            foreach($emails as $email){
              $mail->AddAddress( $email );
            }
            $mail->Body = $mHtml;
            $mail->IsHTML( true );
            
            if($files != NULL){
              foreach($files as $key=>$file){
                  if(is_readable(URL_ARCHIV_STANDA.'ctorres/sat-gl-2015/satt_faro/files/adj_estseg/'.$file['archivo'])){
                    $mail->addAttachment(URL_ARCHIV_STANDA.'ctorres/sat-gl-2015/satt_faro/files/adj_estseg/'.$file['archivo'], $file['name']);
                  }
              }
            }

            $mail->send();
        }

       
        private function sendEmail(){
          $cod_estseg = $_REQUEST['cod_estseg'];
          $ruta = "../../satt_faro/files/adj_estseg/";
          $path = $ruta.''.$cod_estseg.'_InformeFinal_'.time().".pdf";
          $emailsTotal = $_REQUEST['emails'];
          $subject = 'RESULTADO DE ESTUDIO DE SEGURIDAD No. '.self::darCodigoSolicitud($cod_estseg).' - '.$cod_estseg;

          move_uploaded_file($_FILES['file']['tmp_name'], $path);

          
          $emails = explode(",", $emailsTotal);
          $tmpl_file = URL_ARCHIV_STANDA.'ctorres/sat-gl-2015/satt_standa/estseg/planti/template-email.html';
          
          $contenido = '<p>Centro Logístico FARO hace el envio del documento adjunto en este correo con el resultado del estudio de seguridad No.<strong>'.self::darCodigoSolicitud($cod_estseg).' - '.$cod_estseg.'</strong>.</p>
                        <p>No responder -- Este correo ha sido creado automaticamente.</p>';
          $logo = LOGOFARO;
          $ano = date('Y');
          $thefile = implode("", file( $tmpl_file ) );
          $thefile = addslashes($thefile);
          $thefile = "\$r_file=\"".$thefile."\";";
          eval( $thefile );
          $mHtml = $r_file;

          require_once(URL_ARCHIV_STANDA."ctorres/sat-gl-2015/satt_standa/planti/class.phpmailer.php");
          $mail = new PHPMailer();
          $mail->Host = "localhost";
          $mail->From = 'supervisores@eltransporte.org';
          $mail->FromName = 'EST. SEGURIDAD';
          $mail->Subject = $subject ;
          foreach($emails as $email){
            $mail->AddAddress( $email );
          }
          $mail->Body = $mHtml;
          $mail->IsHTML( true );
          if($path!=NULL){
            $mail->AddAttachment($path);
          }
          $mail->Send();
          unlink($path);

        }

        private function armaHtmlCreacion($cod_solici, $ind_enviox){
          $sql = "SELECT b.nom_tercer
                      FROM ".BASE_DATOS.".tab_solici_estseg a
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                        ON a.cod_emptra = b.cod_tercer
                    WHERE a.cod_solici = '".$cod_solici."'; ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];

          if($ind_enviox==1){
            $html.='<p>Estimado: <strong>'.$resultado['nom_tercer'].'</strong></p>
                    <p>Centro Logístico FARO informa que su solicitud No.<strong>'.$cod_solici.'</strong> se ha creado exitosamente. Le estaremos informando los avances de esta solicitud.</p>';
          }else{
            $html.='<p>Estimado: <strong>CENTRO LOGÍSTICO FARO SAS</strong></p>
                    <p>Se informa se ha creado la solicitud de estudio de seguridad No.<strong>'.$cod_solici.'</strong> solicitada por el cliente '.$resultado['nom_tercer'].' para realizar el proceso de consulta en las principales centrales de riesgo.</p>';
          }
          return $html;  
        }

        private function armaHtmlCambioEstado($cod_solici, $cod_estseg){
          $sql = "SELECT b.nom_tercer
                      FROM ".BASE_DATOS.".tab_solici_estseg a
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                        ON a.cod_emptra = b.cod_tercer
                    WHERE a.cod_solici = '".$cod_solici."'; ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];

            $sql = "SELECT a.ind_status, a.ind_estudi
                      FROM ".BASE_DATOS.".tab_relaci_estseg a
                  WHERE a.cod_solici = '".$cod_solici."' AND a.cod_estseg = '".$cod_estseg."'; ";
            $query = new Consulta($sql, self::$conexion);
            $info_estseg = $query->ret_matriz('a')[0];

            $estado = '';
          switch($info_estseg['ind_status']){
            case 1:
              $estado = 'REGISTRADO';
            break;
            case 2:
              $estado = 'EN PROCESO';
            break;
            case 3:
              $estado = 'FINALIZADO';
            break;
            case 4:
              $estado = 'CANCELADO';
            break;
          }

          $add = '';
          if($info_estseg['ind_status']==3){
            if($info_estseg['ind_estudi']=='A'){
              $add = 'y el recurso ha sido <strong>APROBADO</strong>';
            }
            if($info_estseg['ind_estudi']=='R'){
              $add = 'y el recurso ha sido <strong>RECHAZADO</strong>';
            }
          }

          $html.='<p>Estimado: <strong>'.$resultado['nom_tercer'].'</strong></p>
                  <p>Centro Logístico FARO informa que su solicitud No.<strong>'.$cod_solici.' - '.$cod_estseg.'</strong> ha cambiado a estado <strong>'.$estado.'</strong> '.$add.'</p>
                  <p>Por medio de este correo hacemos llegar como archivo adjuntos los documentos relacionados al estudio de seguridad.</p>';
          
          return $html;  
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

    }

    new AjaxGeneraEstSeg();
    
?>
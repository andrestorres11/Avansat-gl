<?php
    /****************************************************************************
    NOMBRE:   AjaxGeneraEstSeg
    FUNCION:  Retorna todos los datos necesarios para construir la informaci?n
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

        static private $cod_usuari = null;
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

                case "getRutas":
                    self::getRutas();
                    break;
                
                case "consulta_transportadoras":
                      self::consultaTransportadoras();
                    break;
  
                case "getInfoTransportadora":
                    self::getInfoTransportadora();
                    break;
                
                case "getTercer":
                      self::getTercer();
                      break;

                case "getVehicu":
                      self::getVehicu();
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
                case "armaPDF":
                    self::armaPDF();
                case "sendEmail":
                      self::sendEmail();
                      break;
                case "reSendEmail":
                      self::reSendEmail();
                      break;
                case "getPDFGenerado":
                        self::getPDFGenerado();
                        break;
                case "generaZip":
                  self::generaZip();
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

        public function consultaTransportadoras(){
          $busqueda = $_REQUEST['key'];
            $sql="SELECT a.cod_tercer, a.nom_tercer
                    FROM 
                                 ".BASE_DATOS.".tab_tercer_tercer a 
                      INNER JOIN ".BASE_DATOS.".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer 
                      INNER JOIN ".BASE_DATOS.".tab_transp_tipser c ON c.cod_transp = a.cod_tercer 
                    WHERE 
                        b.cod_activi = '".COD_FILTRO_EMPTRA."' 
                        AND a.cod_estado = 1 
                        AND c.num_consec = (
                          SELECT 
                            MAX(num_consec) 
                          FROM 
                            ".BASE_DATOS.".tab_transp_tipser 
                          WHERE 
                            cod_transp = c.cod_transp
                        )
                        AND c.ind_estseg = 1
                        AND a.nom_tercer LIKE '%".$busqueda."%'
                      GROUP BY 
                        c.cod_transp 
                      ORDER BY 
                        c.num_consec DESC";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz();
            $htmls='';
            foreach($resultados as $valor){
              $htmls.='<div><a class="suggest-element bk-principal_color white-color" data="'.$valor['cod_tercer'].' - '.$valor['nom_tercer'].'" id="'.$valor['cod_tercer'].'">'.$valor['cod_tercer'].' - '.$valor['nom_tercer'].'</a></div>';
            }
            echo utf8_decode($htmls);
        }

        public function getInfoTransportadora(){
          $sql = "SELECT a.cod_tercer, a.nom_tercer, a.dir_emailx,
                        CONCAT(a.num_telef1,' ', a.num_telef2) as 'num_telefo',
                        a.num_telmov, b.tip_estseg, c.nom_tipest
                  FROM ".BASE_DATOS.".tab_tercer_tercer a
                  INNER JOIN ".BASE_DATOS.".tab_transp_tipser b ON a.cod_tercer = b.cod_transp
                  INNER JOIN ".BASE_DATOS.".tab_genera_estseg c ON b.tip_estseg = c.cod_estseg
                  WHERE a.cod_tercer = '".$_REQUEST['cod_transp']."' 
                  AND b.num_consec = (
                    SELECT MAX(num_consec)
                    FROM tab_transp_tipser
                    WHERE cod_transp = a.cod_tercer
                  )";
          $consulta = new Consulta($sql, self::$conexion);
          $resultados = $consulta->ret_matriz();
          echo json_encode($resultados[0]);
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

        public function generaZip(){
          try{
            $rut_general = dirname(dirname(__DIR__)).'/'.BASE_DATOS.'/files/adj_estseg/adjs/';
            $cod_solici = $_REQUEST['cod_solici'];
            $temp_name = $cod_solici.'_'.time().'.zip';
            $archive_file_name=$rut_general.''.$temp_name;
            
            $zip = new ZipArchive();
            $zip->open($archive_file_name,ZipArchive::CREATE);
          
            $sql = "SELECT b.nom_fordoc, a.obs_archiv, a.nom_archiv 
                    FROM ".BASE_DATOS.".tab_estseg_docume a 
              INNER JOIN ".BASE_DATOS.".tab_estseg_fordoc b ON a.cod_fordoc = b.cod_fordoc
                      WHERE a.cod_solici = '".$cod_solici."'; ";
            $resultado = new Consulta($sql, self::$conexion);
            $documentos = $resultado->ret_matriz('a');
            foreach($documentos as $value){
                $zip->addFile($rut_general.''.$value['nom_archiv'],$cod_solici .'_'.$value['nom_archiv']);
            }
            $zip->close();
            if(file_exists($archive_file_name)){
              header("Pragma: public");
              header("Expires: 0");
              header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
              header("Cache-Control: public");
              header("Content-Description: File Transfer");
              header("Content-type: application/octet-stream");
              header("Content-Disposition: attachment; filename=\"".$temp_name."\"");
              header("Content-Transfer-Encoding: binary");
              header("Content-Length: ".filesize($archive_file_name));
              ob_end_flush();
              @readfile($archive_file_name);

              /*
              header("Content-type: application/zip"); 
              header("Content-Disposition: attachment; filename={$temp_name}");
              header("Content-length: " . filesize($archive_file_name));
              header("Pragma: no-cache"); 
              header("Expires: 0"); 
              
              header("Cache-Control: public");
              header("Content-Description: File Transfer");
              header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
              header("Content-type: application/zip");
              header("Content-Transfer-Encoding: binary");
              header("Content-disposition: attachment; filename=".$temp_name);
              header('Content-Length: ' . filesize($temp_name));
              ob_end_clean();
              readfile($archive_file_name);
              ob_end_flush();
              */
              unlink($archive_file_name);
              exit();
            }else{
              echo " el archivo no existe";
            }
          }catch(Exception $e){
            echo "Error funcion downloadZip", $e->getMessage(), "\n";
          }
        }

        //usado
        function registroIniVehicu(){
            $cod_tipest = $_REQUEST['tip_estudi'];
            $num_placax = $_REQUEST['num_placax_'.$cod_tipest];
            $cod_poseed = $this->registroIniPoseed();

            if($_REQUEST['esPropie_'.$cod_tipest]){
              $cod_propie = $this->registroIniPoseed();
            }else{
              $cod_propie = $this->registroIniPropiet();
            }
            
            if(!$this->validRegPlaca($num_placax)){
              $mSql = "INSERT INTO ".BASE_DATOS.".tab_estseg_vehicu(
                          num_placax, cod_poseed, cod_propie,
                          usr_creaci, fec_creaci
                        ) VALUES (
                          '".strtoupper($num_placax)."', '".$cod_poseed."', '".$cod_propie."',
                          '".self::$cod_usuari."', NOW()
                        )";
            }else{
              $mSql = "UPDATE ".BASE_DATOS.".tab_estseg_vehicu SET
                          cod_poseed = '".$cod_poseed."', 
                          cod_propie = '".$cod_propie."'
                      WHERE num_placax = '".$num_placax."'";
            }
            new Consulta($mSql, self::$conexion);
            return $num_placax;
        }

        function registroIniDespacho(){
          $mSql = "INSERT INTO ".BASE_DATOS.".tab_estseg_despac(
                      usr_creaci, fec_creaci
                    ) VALUES (
                      '".self::$cod_usuari."', NOW()
                    );";
          new Consulta($mSql, self::$conexion);

          $mSql = "SELECT MAX(cod_despac) FROM ".BASE_DATOS.".tab_estseg_despac";
          $mEjec = new Consulta($mSql, self::$conexion);
          $respuesta = $mEjec->ret_matriz();    
          return $respuesta[0][0] == NULL ? 1 : $respuesta[0][0];
        }

        //usado
        function validRegTercer($cod_tercer){
          $mSql = "SELECT cod_tercer FROM ".BASE_DATOS.".tab_estseg_tercer 
                            WHERE cod_tercer = '".$cod_tercer."'";
          $consulta = new Consulta($mSql, self::$conexion);
          $resultado = $consulta->ret_arreglo();
          if($resultado>0){
            return true;
          }
          return false;           
        }

        //usado
        function validRegPlaca($num_placax){
          $mSql = "SELECT num_placax FROM ".BASE_DATOS.".tab_estseg_vehicu
                            WHERE num_placax = '".$num_placax."'";
          $consulta = new Consulta($mSql, self::$conexion);
          $resultado = $consulta->ret_arreglo();
          if($resultado>0){
            return true;
          }
          return false;           
        }

        //usado
        function getRegTercer($cod_tercer){
          $mSql = "SELECT cod_tercer, cod_tipdoc, ciu_expdoc,
                          nom_apell1, nom_apell2, nom_person,
                          num_licenc, cod_catlic, fec_venlic,
                          nom_arlxxx, nom_epsxxx, num_telmov,
                          num_telmo2, num_telefo, cod_paisxx,
                          cod_depart, cod_ciudad, dir_domici,
                          dir_emailx, ind_precom, val_compar,
                          ind_preres, val_resolu
                         FROM ".BASE_DATOS.".tab_estseg_tercer 
                            WHERE cod_tercer = '".$cod_tercer."'";
          $consulta = new Consulta($mSql, self::$conexion);
          $resultado = $consulta->ret_arreglo();
          return $resultado;
        }

        //usado
        function getTercer(){
          $info = [];
          $info['resp'] = false;

          //indicador si permite crear un nuevo estudio con el tercero
          $info['regi'] = true;
          $cod_tercer = $_REQUEST['cod_tercer'];
          $mSql = "SELECT cod_tercer, cod_tipdoc, ciu_expdoc,
                          nom_apell1, nom_apell2, nom_person,
                          num_licenc, cod_catlic, fec_venlic,
                          nom_arlxxx, nom_epsxxx, num_telmov,
                          num_telmo2, num_telefo, cod_paisxx,
                          cod_depart, cod_ciudad, dir_domici,
                          dir_emailx, ind_precom, val_compar,
                          ind_preres, val_resolu
                         FROM ".BASE_DATOS.".tab_estseg_tercer 
                            WHERE cod_tercer = '".$cod_tercer."'";
          $consulta = new Consulta($mSql, self::$conexion);
          $resultado = $consulta->ret_matriz();
          if(count($resultado)>0){
            //Valida si el tercero tiene un estudio pendiente o finalizado con la fecha de vencimiento vigente.
            $mSql = "SELECT 
                            cod_solici, ind_estseg, fec_venest
                         FROM ".BASE_DATOS.".tab_estseg_solici 
                            WHERE cod_conduc = '".$cod_tercer."'
                            AND ((ind_estseg = 'P') OR (ind_estseg = 'A' AND fec_venest >= '".date('Y-m-d')."'))
                            ORDER BY cod_solici DESC";
            $consulta = new Consulta($mSql, self::$conexion);
            $resultados = $consulta->ret_matriz();

            if(count($resultados)>0){
              $info['regi'] = false;
              self::notifiRecurExiste($cod_tercer, $resultados[0]['cod_solici']);
            }

            $info['resp'] = true;
            $info['data'] = self::cleanArray($resultado[0]);
          }
          echo json_encode($info);
        }

        function getRutas(){
          $cod_ciuori = self::separarCodigoCiudad($_REQUEST['ciu_origen']);
          $cod_paiori = self::separarCodigoCiudad($_REQUEST['ciu_origen'],2);
          $cod_ciudes = self::separarCodigoCiudad($_REQUEST['ciu_destin']);
          $cod_paides = self::separarCodigoCiudad($_REQUEST['ciu_destin'],2);

          $mSql = "SELECT a.cod_rutasx, a.nom_rutasx
                          FROM ".BASE_DATOS.".tab_genera_rutasx a
                            WHERE ind_estado = 1 AND
                                  cod_paiori = '".$cod_paiori."' AND
                                  cod_ciuori = '".$cod_ciuori."' AND
                                  cod_paides = '".$cod_paides."' AND
                                  cod_ciudes = '".$cod_ciudes."'
                                  ";
          $consulta = new Consulta($mSql, self::$conexion);
          $resultados = $consulta->ret_matriz();
          $htmls = '';
          if(count($resultados)>0){
           foreach($resultados as $valor){
            $htmls .= '<option value="'.$valor['cod_rutasx'].'">'.$valor['nom_rutasx'].'</option>';
            }
          }
          echo utf8_decode($htmls);
        }

        function getVehicu(){
          $info = [];
          $info['resp'] = false;
          //indicador si permite crear un nuevo estudio con el tercero
          $info['regi'] = true;
          $num_placax = $_REQUEST['num_placax'];
          $mSql = "SELECT num_placax, cod_poseed, cod_propie,
                          num_remolq, cod_marcax, cod_lineax,
                          ano_modelo, cod_colorx, cod_carroc,
                          num_config, num_chasis, num_motorx,
                          num_soatxx, fec_vigsoa, num_lictra
                         FROM ".BASE_DATOS.".tab_estseg_vehicu 
                            WHERE num_placax = '".$num_placax."'";
          $consulta = new Consulta($mSql, self::$conexion);
          $resultado = $consulta->ret_matriz();
          if(count($resultado)>0){
            //Valida si el tercero tiene un estudio pendiente o finalizado con la fecha de vencimiento vigente.
            $mSql = "SELECT 
                            cod_solici, ind_estseg, fec_venest
                         FROM ".BASE_DATOS.".tab_estseg_solici 
                            WHERE cod_vehicu = '".$num_placax."'
                            AND ((ind_estseg = 'P') OR (ind_estseg = 'A' AND fec_venest >= '".date('Y-m-d')."'))
                            ORDER BY cod_solici DESC";
            $consulta = new Consulta($mSql, self::$conexion);
            $resultados = $consulta->ret_matriz();

            if(count($resultados)>0){
              $info['regi'] = false;
              self::notifiRecurExiste($num_placax, $resultados[0]['cod_solici']);
            }
            $info['resp'] = true;
            $info['data'] = self::cleanArray($resultado[0]);
          }
          echo json_encode($info);
        }

        //usado
        function setRegTercer($information){
          $mSql = 'INSERT INTO '.BASE_DATOS.'.tab_estseg_tercer (
                      cod_tercer, cod_tipdoc, nom_apell1,
                      nom_apell2, nom_person, num_telmov,
                      dir_emailx, fec_creaci, usr_creaci
                    ) VALUE (
                      "'.$information['cod_tercer'].'", "'.$information['tip_docume'].'", "'.$information['nom_apell1'].'",
                      "'.$information['nom_apell2'].'", "'.$information['nom_person'].'", "'.$information['num_telmov'].'",
                      "'.$information['dir_emailx'].'", NOW(), "'.self::$cod_usuari.'"
                    )';
          if(new Consulta($mSql, self::$conexion)){
            return true;
          }
          return false;
        }

        function updateRegConduc($information){
          $mSql = 'UPDATE 
                        '.BASE_DATOS.'.tab_estseg_tercer
                      SET  
                        num_telmov = "'.$information['num_telmov'].'", 
                        dir_emailx = "'.$information['dir_emailx'].'", 
                        usr_modifi = "'.self::$cod_usuari.'", 
                        fec_modifi = NOW() 
                      WHERE 
                        cod_tercer = "'.$information['cod_tercer'].'"';
          new Consulta($mSql, self::$conexion);
        }



        function formatInfoTercer($cod_tercer = '', $cod_tipdoc = '', $nom_apell1 = '', $nom_apell2 = '', $nom_person = '', $num_telmov = '', $dir_emailx = ''){
          $information = [
            'cod_tercer' => $cod_tercer,
            'tip_docume' => $cod_tipdoc,
            'nom_apell1' => utf8_decode( mb_convert_case($nom_apell1, MB_CASE_TITLE, "UTF-8")),
            'nom_apell2' => utf8_decode( mb_convert_case($nom_apell2, MB_CASE_TITLE, "UTF-8")),
            'nom_person' => utf8_decode( mb_convert_case($nom_person, MB_CASE_TITLE, "UTF-8")),
            'num_telmov' => $num_telmov,
            'dir_emailx' => strtolower($dir_emailx)
          ]; 
          return $information;
        }

        //usado
        function registroIniConduc(){
          $cod_tipest = $_REQUEST['tip_estudi'];
          $inf_conduc = NULL;
          $information = $this->formatInfoTercer($_REQUEST['num_documeCon_'.$cod_tipest],$_REQUEST['tip_documeCon_'.$cod_tipest], $_REQUEST['nom_apell1Con_'.$cod_tipest], $_REQUEST['nom_apell2Con_'.$cod_tipest], $_REQUEST['nom_personCon_'.$cod_tipest], $_REQUEST['num_telmovCon_'.$cod_tipest], $_REQUEST['dir_emailxCon_'.$cod_tipest]);
          if(!$this->validRegTercer($_REQUEST['num_documeCon_'.$cod_tipest])){
            $status = $this->setRegTercer($information);
          }else{
             $this->updateRegConduc($information);
             $inf_conduc = $this->getRegTercer($_REQUEST['num_documeCon_'.$cod_tipest]);
          }
          $inf_conduc = $this->getRegTercer($_REQUEST['num_documeCon_'.$cod_tipest]);
          return $inf_conduc['cod_tercer'];
        }

        //usado
        function registroIniPoseed(){
          $cod_tipest = $_REQUEST['tip_estudi'];
          $num_docume = $_REQUEST['num_documePos_'.$cod_tipest];
          $inf_poseed = NULL;
          if(!$this->validRegTercer($num_docume)){
            $information = $this->formatInfoTercer($num_docume,$_REQUEST['tip_documePos_'.$cod_tipest], $_REQUEST['nom_apell1Pos_'.$cod_tipest], $_REQUEST['nom_apell2Pos_'.$cod_tipest], $_REQUEST['nom_personPos_'.$cod_tipest], '', '');
            $status = $this->setRegTercer($information);
            if($status){
              $inf_poseed = $this->getRegTercer($num_docume);
            }
          }else{
             $inf_poseed = $this->getRegTercer($num_docume);
          }
          return $inf_poseed['cod_tercer'];
        }

        function registroIniPropiet(){
          $cod_tipest = $_REQUEST['tip_estudi'];
          $num_docume = $_REQUEST['num_documePro_'.$cod_tipest];
          $inf_propie = NULL;
          if(!$this->validRegTercer($num_docume)){
            $information = $this->formatInfoTercer($num_docume,$_REQUEST['tip_documePro_'.$cod_tipest], $_REQUEST['nom_apell1Pro_'.$cod_tipest], $_REQUEST['nom_apell2Pro_'.$cod_tipest], $_REQUEST['nom_personPro_'.$cod_tipest], '', '');
            $status = $this->setRegTercer($information);
            if($status){
              $inf_propie = $this->getRegTercer($num_docume);
            }
          }else{
            $inf_propie = $this->getRegTercer($num_docume);
          }
          return $inf_propie['cod_tercer'];
        }


        //usado
        function guardarSolicitud(){
            $emptra = $this->obtenerTransportadoraPerfil();
            $cod_tipest = $_REQUEST['tip_estudi'];
            $cod_emptra = $_REQUEST['cod_transp'];
            $cor_solici = $_REQUEST['cor_solici'];
            $tel_solici = $_REQUEST['tel_solici'];
            $cel_solici = $_REQUEST['cel_solici'];

            if($_REQUEST['tip_estudi'] == 'V'){
              $cod_conduc = NULL;
              $cod_vehicu = $this->registroIniVehicu();
            }else if($_REQUEST['tip_estudi'] == 'C'){
              $cod_conduc = $this->registroIniConduc();
              $cod_vehicu = NULL;
            }else{
              $cod_vehicu = $this->registroIniVehicu();
              $cod_conduc = $this->registroIniConduc();
            }

            //Indicador de creación del despachos al finalizar el estudio de seguridad.
            $ind_credes = $_REQUEST['ind_credes'];
            $cod_despac = NULL;
            if($ind_credes==1){
              $cod_despac = $this->registroIniDespacho();
            }


            //Consulta el tipo de estudio configurado para la transportadora de acuerdo al tipo de servicio
            $tip_servic = $this->getConfigTipSer($cod_emptra);
  

            $mSql="INSERT INTO ".BASE_DATOS.".tab_estseg_solici(
                      cod_emptra, cor_solici, tel_solici,
                      cel_solici, cod_estcon, cod_tipest,
                      ind_credes, cod_conduc, cod_vehicu,
                      cod_despac, ind_estseg, usr_creaci,
                      fec_creaci
                    ) VALUES (
                      '".$cod_emptra."', '".$cor_solici."', '".$tel_solici."',
                      '".$cel_solici."', '".$tip_servic['tip_estseg']."', '".$cod_tipest."', 
                      '".$ind_credes."', '".$cod_conduc."', '".$cod_vehicu."',
                      '".$cod_despac."', 'P', '".self::$cod_usuari."', NOW()
                    )";
            $query = new Consulta($mSql, self::$conexion);
            
            $mSql = "SELECT cod_solici FROM ".BASE_DATOS.".tab_estseg_solici
                                WHERE cod_conduc = '".$cod_conduc."' OR 
                                      cod_vehicu = '".$cod_vehicu."' 
                                ORDER BY cod_solici DESC LIMIT 1";
            $consulta = new Consulta($mSql, self::$conexion);
            $resultado = $consulta->ret_arreglo();
            $cod_solici = $resultado['cod_solici'];
            
            //Envio correo
            $subject = 'Solicitud de estudio de seguridad No. '.$cod_solici;
            $contenido = self::armaHtmlCreacion($cod_solici,1);
            self::enviarCorreo($cod_solici, 1, $subject, $contenido);  

            $contenido = self::armaHtmlCreacion($cod_solici,2);
            self::enviarCorreo($cod_solici, 2, $subject, $contenido);
                    
            if($query && ($cod_solici != NULL || $cod_solici != '')){
              $info['status']=100;
              $info['response']= 'Se ha creado la Solicitud No. '.$cod_solici; 
              self::registraBitacora($cod_solici,1,$info['response']);
            }else{
              $info['status']=200;
              $info['response']= 'Error no fue posible crear la solicitud'; 
            }

            echo json_encode($info);
        }

        function getConfigTipSer($cod_transp){
          $mSql="SELECT ind_estseg, tip_estseg, ind_segxml, rut_segxml, rut_estpdf 
                  FROM 
                    ".BASE_DATOS.".tab_transp_tipser 
                  WHERE 
                    cod_transp = '".$cod_transp."' 
                  ORDER BY num_consec DESC 
                  LIMIT 1";
          $query = new Consulta($mSql, self::$conexion);
          return $query->ret_arreglo();
        }


          //usado
          function getListDocuments(){
            $cod_solici = $_REQUEST['cod_solici'];
            $sql = "SELECT b.nom_fordoc, a.obs_archiv, a.nom_archiv 
                        FROM ".BASE_DATOS.".tab_estseg_docume a 
                  INNER JOIN ".BASE_DATOS.".tab_estseg_fordoc b ON a.cod_fordoc = b.cod_fordoc
                      WHERE a.cod_solici = '".$cod_solici."'; ";
            $resultado = new Consulta($sql, self::$conexion);
            $documentos = $resultado->ret_matriz('a');
            $html = '';
            foreach($documentos as $key=>$value){
              $ruta = URL_APLICA."files/adj_estseg/adjs/";
              if($value['obs_archiv']=='' || $value['obs_archiv'] == NULL){
                $value['obs_archiv'] = utf8_encode('**SIN OBSERVACIÓN**');
              }
              $html.='<tr>
                        <td>'.$value['nom_fordoc'].'</td>
                        <td>'.$value['obs_archiv'].'</td>
                        <td><center><a href="'.$ruta.''.$value['nom_archiv'].'" target="_blank" class="btn btn-info info-color btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a></center></td>
                      </tr>';
            }
            $html.='<tr>
                        <td colspan="3"><center><button class="btn btn-info info-color btn-sm" onclick="downloadZip(this)" data-dato="'.$cod_solici.'"><i class="fa fa-download" aria-hidden="true"></i> Descargar Zip</button></center></td>
                      </tr>';
            echo utf8_decode($html);
          }

          function getInfoDocument($tablex,$nom_docume,$nom_campox,$nom_camobs,$con_wherex){
            $rut_general = URL_APLICA.'files/adj_estseg/adjs/';
            $sql = "SELECT a.".$nom_campox.", a.".$nom_camobs."
                    FROM ".BASE_DATOS.".".$tablex." a
                  WHERE ".$con_wherex." ";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            if(count($resultados)>0 && ($resultados[0][$nom_campox] != NULL || $resultados[0][$nom_campox] != '')){
              $html.='<tr>
                      <td>'.$nom_docume.'</td>
                      <td>'.$resultados[0][$nom_camobs].'</td>
                      <td><center><a href="'.$rut_general.''.$resultados[0][$nom_campox].'" target="_blank" class="btn btn-info info-color btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a></center></td>
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
            $sql="SELECT a.cod_operad, a.nom_operad FROM ".BD_STANDA.".tab_genera_opegps a WHERE a.ind_estado = 1";
            $resultado = new Consulta($sql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='';
            foreach ($resultados as $registro){
                $selected = '';
                if($cod_opegps != '' || $cod_opegps != NULL){
                  if($registro['cod_operad'] == $cod_opegps){
                  $selected = 'selected';
                  }
                }
                $html .= '<option value="'.$registro['cod_operad'].'" '.$selected.'>'.$registro['nom_operad'].'</option>';
            }
            return utf8_encode($html);
          }

          //usado
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

          //usada
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

            if($_REQUEST['fec_inicio'] != NULL || $_REQUEST['fec_inicio'] != '' && $_REQUEST['fec_finalx'] != NULL || $_REQUEST['fec_finalx'] != '' && !$_REQUEST['fil_fechas']){
              $cond .= " AND a.fec_creaci BETWEEN '".$_REQUEST['fec_inicio']." 00:00:00' AND '".$_REQUEST['fec_finalx']." 23:59:59' ";
            }

            $sql = "SELECT a.cod_solici, b.nom_tercer,
                           c.nom_tipest, '' as 'col_indent', '' as 'col_nommar',
                           a.fec_creaci, a.cod_tipest, 0 as 'tie_gestio',
                           a.ind_estseg, 
                           e.num_placax,
                           d.cod_tercer as 'num_docume',
                           d.nom_apell1, d.nom_apell2, d.nom_person,
                           IF(f.nom_marcax IS NULL, 'NR', f.nom_marcax) as 'nom_marcax',
                           a.fec_finsol, a.fec_venest
                          FROM ".BASE_DATOS.".tab_estseg_solici a
                          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_emptra = b.cod_tercer
                          INNER JOIN ".BASE_DATOS.".tab_estseg_tipoxx c ON a.cod_tipest = c.cod_tipest
                           LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer d ON a.cod_conduc = d.cod_tercer
                           LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu e ON a.cod_vehicu = e.num_placax
                           LEFT JOIN ".BASE_DATOS.".tab_genera_marcas f ON e.cod_marcax = f.cod_marcax
                        WHERE 1=1 ".$cond."; ";
              $query = new Consulta($sql, self::$conexion);
              $mMatriz = $query -> ret_matrix('a');
              $dataReturn = array("registrados"=>array(),"finalizados"=>array());
              foreach ($mMatriz as $key => $datos) {
                
                $btn = '<center><h6><a href="index.php?cod_servic=20221025&amp;window=central&amp;opcion=formEstSeguridad&cod_solici='.$datos['cod_solici'].'""><span class="badge badge-pill badge-success c_pointer btn-success">'.$datos['cod_solici'].'</span></h6></a></center>';
                if($datos['ind_estseg'] != 'P'){
                  $btn = '<center><h6><span class="badge badge-pill badge-success c_pointer btn-success" data-dato="'.$datos['cod_solici'].'" onclick="consInfoSolicitud(this)">'.$datos['cod_solici'].'</span></h6></center>';
                }

                $col_identi = $datos['num_placax'];
                if($datos['cod_tipest'] != 'V'){
                  $col_identi = $datos['num_docume'];
                }

                $col_nommar = $datos['nom_marcax'];
                if($datos['cod_tipest'] != 'V'){
                  $col_nommar = $datos['nom_person']." ".$datos['nom_apell1']." ".$datos['nom_apell2'];
                }

                $fec_creaci = new DateTime($datos['fec_creaci']);
                $fecha_actual = new DateTime(date('Y-m-d H:i:s'));
                $tie_transcu = $fec_creaci->diff($fecha_actual);
                
                if($datos['ind_estseg']=='P'){
                  $arr_regist = array(
                    0 => $btn,
                    1 => $datos['nom_tercer'],
                    2 => $datos['nom_tipest'],
                    3 => $col_identi,
                    4 => $col_nommar,
                    5 => $datos['fec_creaci'],
                    6 => $this->get_format($tie_transcu),
                  );
                  array_push($dataReturn['registrados'],(array)$arr_regist);
                }else{
                  $view = '<center><h6><span class="badge badge-pill badge-success c_pointer btn-success" data-dato="'.$datos['cod_solici'].'" onclick="openModalViewPdf(this)"><i class="fa fa-eye" aria-hidden="true"></i></span></h6></center>';
                  $viewD = '<center><h6><span class="badge badge-pill badge-success c_pointer btn-success" data-dato="'.$datos['cod_solici'].'" onclick="openModalViewDocuments(this)"><i class="fa fa-eye" aria-hidden="true"></i></span></h6></center>';
                  
                  if($datos['fec_venest']>date('Y-m-d')){
                    $badge_venest = '<center><h6><span class="badge badge-pill badge-success c_pointer btn-success" style="background-color:#4caf50 !important">'.$datos['fec_venest'].'</span></h6></center>';
                  }else{
                    $badge_venest = '<center><h6><span class="badge badge-pill badge-danger c_pointer btn-danger">'.$datos['fec_venest'].'</span></h6></center>';
                  }
                  $fec_finsol = new DateTime($datos['fec_creaci']);
                  $tie_transcu = $fec_finsol->diff($fecha_actual);
                  $arr_regist = array(
                    0 => $btn,
                    1 => $datos['nom_tercer'],
                    2 => $datos['nom_tipest'],
                    3 => $col_identi,
                    4 => $col_nommar,
                    5 => $viewD,
                    6 => $view,
                    7 => $datos['fec_creaci'],
                    8 => $datos['fec_finsol'],
                    9 => $this->get_format($tie_transcu),
                    10 => $badge_venest
                  );
                  array_push($dataReturn['finalizados'],(array)$arr_regist);
                }
              }
              echo json_encode(self::cleanArray($dataReturn));
          }

          //usada
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

          //usado
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
                        <tr class="bk-sure">
                          <th scope="col">No. Estudio</th>
                          <th scope="col">Estado</th>
                          <th scope="col">Descargar/Ver estudio</th>
                          <th scope="col">Documentaciï¿½n</th>
                          <th scope="col" style="min-width: 200px;">Conductor</th>
                          <th scope="col" style="min-width: 200px;">Poseedor</th>
                          <th scope="col" style="min-width: 200px;">Propietario</th>
                          <th scope="col">Vehiculo</th>
                          <th scope="col" style="min-width: 130px;">Gestión</th>
                          <th scope="col" style="min-width: 200px;">Observaci?n</th>
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
                $link = '<a href="index.php?cod_servic=202109152&amp;window=central&amp;opcion=formEstSeguridad&cod_estseg='.$resultado['cod_estseg'].'">'.$resultado['cod_estseg'].'</a>';
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

          //usada
          function relacioRefere($cod_person, $cod_refere, $tip_refere){
            $sql = "INSERT INTO ".BASE_DATOS.".tab_estseg_relref(
              cod_estper, cod_refere, tip_refere, 
              usr_creaci, fec_creaci
            ) 
              VALUES 
            (
            '".$cod_person."','".$cod_refere."','".$tip_refere."',
            '".self::$cod_usuari."',NOW()
            )";
            return new Consulta($sql, self::$conexion);
          }


          //usada
          function insReferenciaPyF(){
            $info=[];
            $sql = "INSERT INTO ".BASE_DATOS.".tab_estseg_refere(
                nom_refere, cod_parent, nom_parent,
                dir_domici, num_telefo, obs_refere,
                usr_creaci, fec_creaci
                ) 
                  VALUES 
                (
                '".$_REQUEST['nom_refereE']."', '".$_REQUEST['cod_parentE']."', '".$_REQUEST['nom_parentE']."',
                '".$_REQUEST['dir_domiciE']."', '".$_REQUEST['num_telefoE']."', '".$_REQUEST['obs_refereE']."',
                '".self::$cod_usuari."', NOW()
                )";
            $query = new Consulta($sql, self::$conexion);

            $ult_refere = self::consultaUltRegistro('tab_estseg_refere','cod_refere');
            $result = self::relacioRefere($_REQUEST['cod_personE'], $ult_refere, $_REQUEST['cod_refereE']);      
              if($result){
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
            $result = self::relacioRefere($_REQUEST['cod_personE'], $ult_refere, $_REQUEST['cod_refereE']);      
              if($result){
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

          //usada
          function borrarReferenceFyP(){
            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_estseg_refere 
                    WHERE cod_refere = '".$_REQUEST['cod_refere']."'; ";
            $query = new Consulta($sql, self::$conexion);

            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_estseg_relref
                    WHERE cod_person = '".$_REQUEST['cod_person']."' AND cod_refere = '".$_REQUEST['cod_refere']."'; ";
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
                    FROM ".BASE_DATOS.".tab_estseg_reflab 
                    WHERE cod_refere = '".$_REQUEST['cod_refere']."'; ";
            $query = new Consulta($sql, self::$conexion);
            $sql = "DELETE
                    FROM ".BASE_DATOS.".tab_estseg_relref
                    WHERE cod_estper = '".$_REQUEST['cod_person']."' AND cod_refere = '".$_REQUEST['cod_refere']."'; ";
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

          function guardadoDeDocumentos($cod_solici, $cod_fordoc, $nom_archiv, $nom_rutfil, $obs_archiv, $nom_tipfil){
            $sql = "SELECT a.cod_fordoc 
                        FROM ".BASE_DATOS.".tab_estseg_docume a 
                      WHERE a.cod_solici = '".$cod_solici."' 
                        AND a.cod_fordoc = '".$cod_fordoc."'; ";
            $resultado = new Consulta($sql, self::$conexion);
            $documentos = $resultado->ret_matriz('a');

            if(count($documentos)>0){
              $sql='UPDATE 
                        '.BASE_DATOS.'.tab_estseg_docume 
                      SET  
                        nom_archiv = "'.$nom_archiv.'", 
                        nom_rutfil = "'.$nom_rutfil.'", 
                        obs_archiv = "'.$obs_archiv.'", 
                        nom_tipfil = "'.$nom_tipfil.'",
                        usr_modifi = "'.self::$cod_usuari.'", 
                        fec_modifi = NOW() 
                      WHERE 
                        cod_solici = "'.$cod_solici.'" AND
                        cod_fordoc = "'.$cod_fordoc.'"';
            }else{
              $sql='INSERT INTO '.BASE_DATOS.'.tab_estseg_docume(
                      cod_solici, cod_fordoc, nom_archiv,
                      nom_rutfil, obs_archiv, nom_tipfil,
                      usr_creaci, fec_creaci
                    ) VALUES (
                          "'.$cod_solici.'", "'.$cod_fordoc.'", "'.$nom_archiv.'",
                          "'.$nom_rutfil.'", "'.$obs_archiv.'", "'.$nom_tipfil.'",
                          "'.self::$cod_usuari.'", NOW()
                    )';
              
            }
            new Consulta($sql, self::$conexion);
          }

          function guardadoObservacion($cod_solici, $cod_fordoc, $obs_archiv){
            $sql = "SELECT a.cod_fordoc 
            FROM ".BASE_DATOS.".tab_estseg_docume a 
          WHERE a.cod_solici = '".$cod_solici."' 
            AND a.cod_fordoc = '".$cod_fordoc."'; ";
              $resultado = new Consulta($sql, self::$conexion);
              $documentos = $resultado->ret_matriz('a');

              if(count($documentos)>0){
                $sql='UPDATE 
                          '.BASE_DATOS.'.tab_estseg_docume 
                        SET  
                          obs_archiv = "'.$obs_archiv.'"
                        WHERE 
                          cod_solici = "'.$cod_solici.'" AND
                          cod_fordoc = "'.$cod_fordoc.'"';
              }else{
                $sql='INSERT INTO '.BASE_DATOS.'.tab_estseg_docume(
                        cod_solici, cod_fordoc, obs_archiv,
                        usr_creaci, fec_creaci
                      ) VALUES (
                            "'.$cod_solici.'", "'.$cod_fordoc.'", "'.$obs_archiv.'",
                            "'.self::$cod_usuari.'", NOW()
                      )';
                
              }
              new Consulta($sql, self::$conexion);
          }



          function registraBitacora($cod_solici, $cod_estado, $obs_estado){
              $sql = "INSERT INTO ".BASE_DATOS.".tab_estseg_bitaco (
                          cod_solici, cod_estado, obs_estado,
                          usr_creaci, fec_creaci
                       ) VALUES (
                        '".$cod_solici."', '".$cod_estado."', '".utf8_decode($obs_estado)."',
                        '".self::$cod_usuari."', NOW() 
                        )";
              $query = new Consulta($sql, self::$conexion);
          }

          function cambiaEstado($cod_estseg, $ind_fase, $ind_estudi, $ind_status, $obs_estudi = ''){
              $rut_general = dirname(dirname(__DIR__)).'/'.BASE_DATOS.'/files/adj_estseg/adjs/';
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
          
          //usada
          function guardaArchivos($cod_solici, $cod_tipper){
            $mSql="SELECT a.cod_fordoc, a.nom_fordoc, a.nom_slugxx FROM ".BASE_DATOS.".tab_estseg_fordoc a
                              WHERE a.ind_status = 1 AND
                                    a.cod_tipper = '".$cod_tipper."'
                              ORDER BY ind_ordenx, a.nom_fordoc ASC
            ";
            $resultado = new Consulta($mSql, self::$conexion);
            $documentos = $resultado->ret_matriz('a');
            $ruta = "../../".BASE_DATOS."/files/adj_estseg/adjs/";
            $errores = false;
            foreach($documentos as $key=>$documento){
              $nom_tipdoc = $documento['nom_slugxx'];
              if($_FILES[$nom_tipdoc]['name'] != null || $_FILES[$nom_tipdoc]['name'] != ''){
                $ext = explode(".", ($_FILES[$nom_tipdoc]['name']));
                $nombre = $ruta.''.$cod_solici.'_'.$nom_tipdoc.'_'.time().".".end($ext);
                $nom_final = $cod_solici.'_'.$nom_tipdoc.'_'.time().".".end($ext);
                $destino_temporal=tempnam("tmp/","tmp");
                if (move_uploaded_file($_FILES[$nom_tipdoc]['tmp_name'], $nombre)) {
                  if(end($ext)=='jpg' || end($ext)=='png' || end($ext)=='jpeg'){
                    if(self::redimensionarImagen($nombre, $destino_temporal, 640, 480, 50) ){
                      unlink($path);
                      $fp=fopen($nombre,"w");
                      fputs($fp,fread(fopen($destino_temporal,"r"),filesize($destino_temporal)));
                      fclose($fp);
                    } 
                  }
                  $observacion = $_REQUEST[$nom_tipdoc."OBS"];
                  $nom_tipfil = strtolower(end($ext));
                  self::guardadoDeDocumentos($cod_solici,$documento['cod_fordoc'], $nom_final, $nombre, $observacion, $nom_tipfil);
              }else{
                $errores = true;
                $doc_nosubi = ' - '.$_FILES[$nom_tipdoc]['name'];
              }
            }
            $observacion = $_REQUEST[$nom_tipdoc."OBS"];
            if($observacion!=''){
              self::guardadoObservacion($cod_solici, $documento['cod_fordoc'], ucfirst(strtolower($observacion)));
            }
          }
          }

          /**
           * Funcion para redimensionar imagenes
           *
           * @param string $origin Imagen origen en el disco duro ($_FILES["image1"]["tmp_name"])
           * @param string $destino Imagen destino en el disco duro ($destino=tempnam("tmp/","tmp");)
           * @param integer $newWidth Anchura m?xima de la nueva imagen
           * @param integer $newHeight Altura m?xima de la nueva imagen
           * @param integer $jpgQuality (opcional) Calidad para la imagen jpg
           * @return boolean true = Se ha redimensionada|false = La imagen es mas peque?a que el nuevo tama?o
           */
          function redimensionarImagen($origin,$destino,$newWidth,$newHeight,$jpgQuality=100)
          {
              // getimagesize devuelve un array con: anchura,altura,tipo,cadena de 
              // texto con el valor correcto height="yyy" width="xxx"
              $datos=getimagesize($origin);
              // comprobamos que la imagen sea superior a los tama?os de la nueva imagen
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
          
          function getInfoSolici($cod_solici){
            $sql = "SELECT 
                      a.cod_solici, a.cod_emptra, c.nom_tercer,
                      a.cor_solici, a.tel_solici, a.cel_solici,
                      a.cod_tipest, a.cod_conduc, a.cod_vehicu,
                      b.cod_poseed, b.cod_propie, a.ind_credes,
                      a.cod_despac, a.ind_estseg, a.obs_estseg,
                      a.usr_estseg, a.fec_venest
                    FROM ".BASE_DATOS.".tab_estseg_solici a
                    LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu b ON 
                      a.cod_vehicu = b.num_placax
                   INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON 
                      a.cod_emptra = c.cod_tercer
                  WHERE a.cod_solici  = '".$cod_solici."'";
            $resultado = new Consulta($sql, self::$conexion);
            $registro = $resultado->ret_matriz('a')[0];
            return $registro;
          }

          //usado
          function preguardado(){
            $cod_solici = $_REQUEST['cod_solici'];
            $inf_solici = self::getInfoSolici($cod_solici);
            $process = false;
            if($inf_solici['cod_tipest'] == 'C'){
              //Actualiza Informacion del conductor
              $process = self::procesaConductor($_REQUEST, $inf_solici);
            }else if($inf_solici['cod_tipest'] == 'V'){
              //Actualiza Informacion del vehiculo
              $process = self::procesaVehiculo($_REQUEST, $inf_solici);
            }else{
              $process = self::procesaVehiculo($_REQUEST, $inf_solici);
              $process = self::procesaConductor($_REQUEST, $inf_solici);
            }

            if($inf_solici['ind_credes']==1){
              $process = self::procesaDespacho($_REQUEST, $inf_solici);
            }

              if($process){
                  $info['status']=200; 
                  self::registraBitacora($cod_solici,2,ucfirst(strtolower($_REQUEST['obs_gestio'])));
              }else{
                  $info['status']=100; 
              } 
              echo json_encode($info);
          }

          //usado
          function guardado(){
            $cod_solici = $_REQUEST['cod_solici'];
            $inf_solici = self::getInfoSolici($cod_solici);
            $inf_tipser = self::getConfigTipSer($inf_solici['cod_emptra']);
            $process = false;
            
            if($inf_solici['cod_tipest'] == 'C'){
              //Actualiza Informacion del conductor
              $process = self::procesaConductor($_REQUEST, $inf_solici);
            }else if($inf_solici['cod_tipest'] == 'V'){
              //Actualiza Informacion del vehiculo
              $process = self::procesaVehiculo($_REQUEST, $inf_solici);
            }else{
              $process = self::procesaVehiculo($_REQUEST, $inf_solici);
              $process = self::procesaConductor($_REQUEST, $inf_solici);
            }

            if($inf_solici['ind_credes']==1){
              $process = self::procesaDespacho($_REQUEST, $inf_solici);
            }
            
            //Consulta los dias habilitados
            $mSql=' SELECT vig_estseg
                    FROM '.BASE_DATOS.'.tab_transp_tipser 
                    WHERE cod_transp = "'.$inf_solici['cod_emptra'].'" 
                    ORDER BY num_consec
                    DESC limit 1';
            $mConsult = new Consulta($mSql, self::$conexion);
            $dias_habilitados = $mConsult->ret_matriz('a')[0][0];

            //Calcula la fecha de vencimiento del estudio en base a los dias configurados
            $dias = $dias_habilitados != NULL ? $dias_habilitados : 30;
            $date = date("Y-m-d");
            $fec_venest = date("Y-m-d",strtotime($date."+ ".$dias." days"));
            if($process){
              $sql="UPDATE ".BASE_DATOS.".tab_estseg_solici
                        SET 
                          ind_estseg = '".$_REQUEST['ind_estudi']."',
                          obs_estseg = '".ucfirst(strtolower($_REQUEST['obs_gestio']))."', 
                          usr_estseg = '".self::$cod_usuari."',
                          fec_finsol = NOW(),
                          fec_venest = '".$fec_venest."',
                          usr_modifi = '".self::$cod_usuari."', 
                          fec_modifi = NOW() 
                    WHERE 
                      cod_solici = '".$cod_solici."'";
                new Consulta($sql, self::$conexion);
              $cod_estado = 3;
              if($_REQUEST['ind_estudi'] == 'C'){
                $cod_estado = 4;
              }
              self::registraBitacora($cod_solici,$cod_estado,ucfirst(strtolower($_REQUEST['obs_gestio'])));
              $info['status']=200; 
            }else{
                $info['status']=100; 
            } 
            $array_generado = self::armaArrayInfo($cod_solici);
            
            if( $inf_solici['ind_credes'] ){
              $mensaje = self::creaDespacho($array_generado);
              if(!$mensaje){
                $info['despac_msg'] = $mensaje['msg'];
                $info['despac_status'] = $mensaje['status'];
              }
            }

            if($inf_tipser['ind_segxml']){
              $ident_xml = '';
              if($inf_solici['cod_tipest'] == 'V'){
                $ident_xml = $inf_solici['cod_vehicu'];
                self::createXML($array_generado,'SOL_'.$ident_xml.'.xml', $inf_tipser['rut_segxml']);
              }else if($inf_solici['cod_tipest'] == 'C'){
                $ident_xml = $inf_solici['cod_conduc'];
                self::createXML($array_generado,'SOL_'.$ident_xml.'.xml', $inf_tipser['rut_segxml']);
              }else{
                $array_generado = self::armaArrayInfo($cod_solici,'V');
                self::createXML($array_generado,'SOL_'.$inf_solici['cod_vehicu'].'.xml', $inf_tipser['rut_segxml']);
                $array_generado = self::armaArrayInfo($cod_solici,'C');
                self::createXML($array_generado,'SOL_'.$inf_solici['cod_conduc'].'.xml', $inf_tipser['rut_segxml']);
              }
            }
            
            if($info['status']==200){
                  $info['status']=200;
                  $info['redire']=1;
                  $info['cod_solici']=$cod_solici;
                  $info['emails'] = implode(",", self::darCorreos($cod_solici,1));
                  $url = explode("?", $_SERVER['HTTP_REFERER']);
                  $info['page'] = $url[0].'?cod_servic=20221025&window=central';
            }else{
                  $info['status']=100; 
            }
            echo json_encode($info);
              
          }

          /* ! \fn: creaDespacho
          *  \brief: guarda la informacion del despacho
          *  \author: Cristian Andrés Torres
          *  \date: 20/12/2017
          *  \date modified: dd/mm/aaaa
          *  \return: json
          */
          private function creaDespacho($data){
            try{
                //DATA
                $num_despac = NULL;
                $dispatch = $data['dispatch'];
                $cod_transp = $data['company_code'];
                $cod_manifi = $dispatch['manifest_code'];
                $cod_tipdes = $dispatch['dispatch_type'];
                $cod_paiori = $dispatch['origin']['country_code'];
                $cod_ciuori = $dispatch['origin']['city_code'];
                $cod_paides = $dispatch['destination']['country_code'];
                $cod_ciudes = $dispatch['destination']['city_code'];
                $cod_rutaxx = $dispatch['route_code'];
                $val_declar = $dispatch['value_declarade'];
                $val_pesoxx = $dispatch['weight'];
                $cod_agenci = $dispatch['agenci_code'];
                $cod_conduc = $data['driver']['document_number'];
                $num_placax = $data['vehicle']['placa'];
                $obs_despac = 'Despacho creado automaticamente a traves del módulo de estudio de seguridad';


                #consulta el ultimo consecutivo del despacho
                $mSelect = "SELECT MAX( num_despac ) AS maximo
                            FROM ".BASE_DATOS.".tab_despac_despac ";

                $consec = new Consulta( $mSelect, self::$conexion, "BR" );
                $ultimo = $consec->ret_matriz();

                #incrementa el consecutivo
                $ultimo_consec = $ultimo[0][0];
                $num_despac = $ultimo_consec + 1;  

                #consulta el pais y el departamento de la ciudad origen
                $mSelect = "SELECT a.cod_paisxx, a.cod_depart
                        FROM ".BASE_DATOS.".tab_genera_ciudad a
                        WHERE a.cod_ciudad = '$cod_ciuori' AND a.cod_paisxx = '$cod_paiori'";
                $consulta = new Consulta( $mSelect, self::$conexion, "R" );
                $paidepori = $consulta->ret_matriz();
                $cod_depori = $paidepori[0][1];

                #consulta el pais y el departamento de la ciudad destino
                $mSelect = "SELECT a.cod_paisxx,a.cod_depart
                        FROM ".BASE_DATOS.".tab_genera_ciudad a
                        WHERE a.cod_ciudad = '$cod_ciudes' AND a.cod_paisxx = '$cod_paides'";
                $consulta = new Consulta($mSelect, self::$conexion, "R");
                $paidepdes = $consulta->ret_matriz();
                $cod_depdes = $paidepdes[0][1];

                $usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];

                //PENDIENTE REMITENTE
                /*
                $mSql = "SELECT UPPER(a.nom_remdes) AS nom_remdes
                        FROM ".BASE_DATOS.".tab_genera_remdes a
                        WHERE  a.ind_remdes = 1 
                        AND a.ind_estado = 1 
                        AND a.cod_remdes != 0 
                        AND a.cod_remdes = '".$datos->cod_sitcar."'   
                    GROUP BY  1";
                $consulta = new Consulta( $mSql, $this->conexion );
                $sitCar = $consulta->ret_matriz();
                $datos->nom_sitcar = $sitCar[0][0];*/

                $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
                ( 
                      num_despac, cod_manifi, fec_despac, cod_tipdes,
                      cod_client, cod_paiori, cod_depori, cod_ciuori,
                      cod_paides, cod_depdes, cod_ciudes, fec_citcar,
                      hor_citcar, nom_sitcar, val_flecon, val_despac,
                      val_antici, val_retefu, nom_carpag, nom_despag,
                      cod_agedes, fec_pagoxx, obs_despac, val_declara,
                      usr_creaci, fec_creaci, val_pesoxx, cod_asegur,
                      num_poliza, fec_salida, ind_planru 
                    ) VALUES (
                      '$num_despac','$cod_manifi','".DATE('Y-m-d H:i:s')."','$cod_tipdes',
                      NULL,'".$cod_paiori."', '".$cod_depori."', '".$cod_ciuori."',
                      '".$cod_paides."','".$cod_depdes."', '".$cod_ciudes."', NULL,
                      NULL, NULL, 0, 0,
                      0, 0, NULL,NULL,
                      '$cod_agenci',NULL, '".$obs_despac."','".$val_declar."',
                      '$usr_creaci', NOW(), '".$val_pesoxx."', NULL,
                      NULL, NULL, 'N' 
                    )"; 
                $consulta = new Consulta($mInsert, self::$conexion, "S");    
                //Valida el registro del conductor y/o actualizacion
                self::setTercero($data['driver'], 4);     
                //Valida el registro del vehiculo y/o actualizacion
                self::setvehicu($data['vehicle'], $cod_conduc);

                $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                                    (
                                    num_despac, cod_transp, cod_agenci, 
                                    cod_rutasx, cod_conduc, num_placax, 
                                    num_trayle, obs_medcom, ind_activo, 
                                    usr_creaci, fec_creaci
                                    )
                                VALUES 
                                    (
                                    '$num_despac', '$cod_transp', '$cod_agenci', 
                                    '$cod_rutaxx', '$cod_conduc', '$num_placax', 
                                    NULL, '', 'S',
                                    '$usr_creaci', NOW() 
                                    )"; 
                $consulta = new Consulta( $mInsert,self::$conexion, "R" );
                                  
                /*
                $can_remesa = count($datos->num_remesa);
                for($i=0; $i < $can_remesa; $i++){

                    $mSql = "SELECT a.cod_remdes,  
                                UPPER (a.nom_remdes) AS nom_remdes
                        FROM ".BASE_DATOS.".tab_genera_remdes a
                    INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b
                            ON  a.cod_ciudad = b.cod_ciudad AND b.ind_estado = 1
                        WHERE  a.ind_remdes = 2
                            AND a.ind_estado = 1 
                            AND a.cod_remdes = '".$datos->cod_destin[$i]."'       
                    GROUP BY  1";
            
                    $consulta = new Consulta( $mSql, $this->conexion );
                    $nomDestin = $consulta->ret_matriz();
                    $datos->nom_destin[$i] = $nomDestin[0][1];
                    $cod_genera = $datos->cod_client != "" && $datos->cod_client != NULL ? "".$datos->cod_client."" : "NULL" ;
                    $nom_genera = strtoupper($this->getInfoTercer($cod_genera)['nom_tercer']);
                    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_destin
                                        (
                                        num_despac, num_docume, num_docalt, cod_genera, nom_genera,
                                        nom_destin, cod_ciudad, dir_destin, num_destin, 
                                        fec_citdes, hor_citdes, usr_creaci, fec_creaci,
                                        cod_remdes
                                        )
                                VALUES
                                        (
                                        '$datos->num_despac', '".$datos->num_remesa[$i]."','".$datos->num_docalt[$i]."', '".$cod_genera."', '".$nom_genera."',
                                        '".$datos->nom_destin[$i]."', '".$datos->cod_ciucli[$i]."','".$datos->nom_dircli[$i]."', '".$datos->cod_destin[$i]."',  
                                        '".$datos->fec_citdes[$i]."', '".$datos->hor_citdes[$i]."','$datos->usr_creaci', NOW(), 
                                        '".$datos->cod_destin[$i]."')";
                    $consulta = new Consulta( $mInsert, $this->conexion, "R" );

                    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_remesa (num_despac,cod_remesa,num_trayle,fec_estent,val_pesoxx,
                                                                        val_volume,des_empaqu,des_mercan,abr_client,
                                                                        usr_creaci,fec_creaci)
                                                                    VALUES('$datos->num_despac','".$datos->num_remesa[$i]."', '".$datos->num_plarem[$i]."','".$datos->fec_citdes[$i]." ".$datos->hor_citdes[$i]."','".$datos->val_pesoxx[$i]."',
                                                                        '".$datos->val_volume[$i]."','".$datos->nom_empaqu[$i]."','".$datos->nom_mercan[$i]."','".$datos->nom_destin[$i]."',
                                                                        '$datos->usr_creaci', NOW())";
                    $consulta = new Consulta( $mInsert, $this->conexion, "R" );

            }*/

            # Agrega los datos de viaje en caso de que exista
            /*
                if( $datos->cod_desext != '' )
                {
                    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_sisext
                                        ( num_despac, num_desext )
                                VALUES( '$datos->num_despac','$datos->cod_desext')";
                    $consulta = new Consulta($mInsert, $this->conexion, "R");
                    $mInsert = "INSERT INTO  ".BASE_DATOS.".tab_despac_viajex 
                                    ( num_despac, num_placax, num_viajex, cod_transp, usr_creaci, fec_creaci ) 
                                VALUES 
                                    ('$datos->num_despac', '$datos->cod_placaxI', '$datos->cod_desext', '$datos->cod_transp', '$datos->usr_creaci', NOW() ) ";
                    $consulta = new Consulta($mInsert, $this -> conexion, "R");
                }*/

                
            
                //Respuesta
                if( $insercion = new Consulta( "COMMIT",self::$conexion ) ){
                    return $data = [
                      'num_despac' => $num_despac,
                      'cod_manifi' => $cod_manifi,
                      'status' => true,
                      'msg' => 'Despacho creado exitosamente'
                    ];
                }
                return false;
            } catch(Exception $e){
                echo "<pre> Error Funcion saveDespac:";print_r($e);echo "</pre>";
            }

          }

          private function setTercero($dataTercer, $cod_activi){
            $cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $mExist = self::getTerceroByID( $dataTercer['document_number'] );
            if(sizeof($mExist) <= 0){
              $mQuery = "INSERT IGNORE ". BASE_DATOS .".tab_tercer_tercer  
              ( 
                cod_tercer, cod_tipdoc, nom_tercer, 
                abr_tercer, nom_apell1, nom_apell2,
                cod_paisxx, cod_depart, cod_ciudad,
                dir_domici, num_telef1, num_telmov,
                cod_estado, usr_creaci, fec_creaci
              ) 
              VALUES 
              (  
                '".$dataTercer['document_number']."', '".$dataTercer['document_typeID']['id']."', '".$dataTercer['names']."', 
                '".$dataTercer['names']." ".$dataTercer['surname']." ".$dataTercer['second_surname']."', '".$dataTercer['surname']."', '".$dataTercer['second_surname']."',
                '".$dataTercer['residence_city']['country']['code']."', '".$dataTercer['residence_city']['departament']['code']."', '".$dataTercer['residence_city']['departament']['city']."',
                '".$dataTercer['address']."', '".$dataTercer['phone']."', '".$dataTercer['mobile1']."',
                1, '".$cod_usuari."' ,NOW()
              ) ";
            } else {
              $mQuery = "UPDATE ". BASE_DATOS .".tab_tercer_tercer  
                            SET
                              cod_tipdoc = '".$dataTercer['document_typeID']['id']."', 
                              nom_tercer = '".$dataTercer['names']."', 
                              abr_tercer = '".$dataTercer['names']." ".$dataTercer['surname']." ".$dataTercer['second_surname']."', 
                              nom_apell1 = '".$dataTercer['surname']."', 
                              nom_apell2 = '".$dataTercer['second_surname']."',
                              dir_domici = '".$dataTercer['address']."', 
                              cod_paisxx = '".$dataTercer['residence_city']['country']['code']."', 
                              cod_depart = '".$dataTercer['residence_city']['departament']['code']."', 
                              cod_ciudad = '".$dataTercer['residence_city']['departament']['city']."', 
                              num_telef1 = '".$dataTercer['phone']."', 
                              num_telmov = '".$dataTercer['mobile1']."', 
                              cod_estado = 1, 
                              usr_modifi = '".$cod_usuari."', 
                              fec_modifi = NOW()      
                              WHERE 
                              cod_tercer = '".$dataTercer['document_number']."' 
                ";
            }
            $query = new Consulta($mQuery, self::$conexion);
            self::setTercDet( $dataTercer['document_number'], $cod_activi);
            return  $query;
          }

          private function setvehicu($dataVehicu, $cod_conduc){
            $cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
            $mExistPropieta = self::getTerceroByID($dataVehicu['owner']['document_number']);
            if(sizeof($mExistPropieta) <= 0){
              self::setTercero($dataVehicu['owner'], 5);
            }
            $mExistPoseedor = self::getTerceroByID($dataVehicu['holder']['document_number']);
            if(sizeof($mExistPoseedor) <= 0){
              self::setTercero($dataVehicu['holder'], 6);
            }
            $mExistVehicu = self::vehicuExists($dataVehicu['placa']);
            if(sizeof($mExistVehicu) <= 0){
              $QueryVehicu = "REPLACE INTO " . BASE_DATOS . ".tab_vehicu_vehicu  
                        ( num_placax, cod_marcax, cod_lineax, 
                          cod_colorx, ano_modelo, num_motorx, 
                          num_chasis, num_poliza, fec_vigfin, 
                          num_config, cod_propie, 
                          cod_tenedo, cod_conduc, 
                          ind_estado, usr_creaci, fec_creaci,
                          cod_opegps, usr_gpsxxx, clv_gpsxxx, idx_gpsxxx
                       )
                      VALUES 
                       ( 
                            '".$dataVehicu['placa']."', '".$dataVehicu['brand']['code']."', '".$dataVehicu['line']['code']."', 
                            '".$dataVehicu['color']['code']."', '".$dataVehicu['model']."', '".$dataVehicu['engine_number']."', 
                            '".$dataVehicu['chassis_number']."', '".$dataVehicu['soat_policy_number']."', '".$dataVehicu['fec_vigsoa']."', 
                            '".$dataVehicu['configuration']['code']."', '".$dataVehicu['owner']['document_number']."',
                            '".$dataVehicu['holder']['document_number']."', '".$cod_conduc."',
                            1, '".$cod_usuari."',NOW(), 
                            '".$dataVehicu['gps']['code']."','".$dataVehicu['gps']['username']."','".$dataVehicu['gps']['password']."','".$dataVehicu['gps']['id']."'
                        )";
            }else{
              $QueryVehicu = "UPDATE " . BASE_DATOS . ".tab_vehicu_vehicu  
                                SET
                                cod_marcax = '".$dataVehicu['brand']['code']."',
                                cod_lineax = '".$dataVehicu['line']['code']."',
                                cod_colorx = '".$dataVehicu['color']['code']."',
                                ano_modelo = '".$dataVehicu['model']."',
                                num_motorx = '".$dataVehicu['engine_number']."',
                                num_chasis = '".$dataVehicu['chassis_number']."',
                                num_poliza = '".$dataVehicu['soat_policy_number']."',
                                fec_vigfin = '".$dataVehicu['fec_vigsoa']."',
                                num_config = '".$dataVehicu['configuration']['code']."',
                                cod_propie = '".$dataVehicu['owner']['document_number']."',
                                cod_tenedo = '".$dataVehicu['holder']['document_number']."',
                                cod_conduc = '".$cod_conduc."'
                                WHERE
                                num_placax = '".$dataVehicu['placa']."'

                        ";
            }
            $query = new Consulta($QueryVehicu, self::$conexion);
            return  $query;
          }

          /* ! \fn: getValidateTercerExist
          *  \brief: Valida si el tercero existe
          *  \author: Cristian Andrés Torres
          *  \date: 20/12/2017
          *  \date modified: dd/mm/aaaa
          *  \return: json
          */
          private function getTercerActivi($cod_tercer, $cod_activi){
              $sql="SELECT cod_tercer, cod_activi  FROM ". BASE_DATOS .".tab_tercer_activi 
                     WHERE cod_tercer =  '".$cod_tercer."'  AND cod_activi =  '".$cod_activi."'  ";
              $query = new Consulta($sql, self::$conexion);
              $resultado = $query->ret_matriz('a')[0];

              return  $resultado;
          }


           /* ! \fn: setTercDet
          *  \brief : Valida que la empresa exista como transprtadora
          *  \author: Ing. Nelson Liberato
          *  \date: 10/09/2019
          *  \param1: code codigo de la novedad request
          *  \return: array con novedad homologada al proceso, bool
          */  
          private function setTercDet( $mCodTercer = NULL, $mCodActivi = NULL)
          {
            $cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
            if( sizeof( self::getTercerActivi($mCodTercer, $mCodActivi ) ) <= 0 ){ 
                  $fTercerActivy  = "INSERT IGNORE ". BASE_DATOS .".tab_tercer_activi 
                                        ( cod_tercer, cod_activi ) VALUES ( '".$mCodTercer."' , '".$mCodActivi."' )";
                  $mReturn = new Consulta($fTercerActivy, self::$conexion);
            } 

            if( "4" == $mCodActivi ){
              if(  sizeof(self::conducExists( $mCodTercer ))  <= 0){
                  $fQueryConduc = "INSERT IGNORE ". BASE_DATOS .".tab_tercer_conduc 
                                  ( cod_tercer, cod_tipsex, num_catlic, usr_creaci, fec_creaci, cod_operad ) 
                                  VALUES 
                                  ( '".$mCodTercer."', 1, NULL, '".$cod_usuari."', NOW(), NULL )";
                                  $mReturn = new Consulta($fQueryConduc, self::$conexion);          
              }
            }
              
          return $mReturn;
          }

          /* ! \fn: conducExists
          *  \brief : Valida si el documento existe en tercer_conduc
          *  \author: Ing. Nelson Liberato
          *  \date: 10/09/2019
          *  \param1: code codigo de la novedad request
          *  \return: array con novedad homologada al proceso, bool
          */ 
          private function conducExists($mCodConduc) { 
              $mQuery = "SELECT cod_tercer FROM ". BASE_DATOS .".tab_tercer_conduc  WHERE cod_tercer =  '".$mCodConduc."' ";
              $query = new Consulta($mQuery, self::$conexion);
              $resultado = $query->ret_matriz('a')[0];
              return  $resultado;
          }

          /* ! \fn: vehicuExists
          *  \brief : Valida si el documento existe en tercer_conduc
          *  \author: Ing. Nelson Liberato
          *  \date: 10/09/2019
          *  \param1: code codigo de la novedad request
          *  \return: array con novedad homologada al proceso, bool
          */ 
          private function vehicuExists($mNumPlacax) { 
            $mQuery = "SELECT num_placax FROM ". BASE_DATOS .".tab_vehicu_vehicu  WHERE num_placax =  '".$mNumPlacax."' ";
            $query = new Consulta($mQuery, self::$conexion);
            $resultado = $query->ret_matriz('a');
            return  $resultado;
          }

          /* ! \fn: getTerceroByID
          *  \brief: Valida si el tercero existe
          *  \author: Cristian Andrés Torres
          *  \date: 20/12/2017
          *  \date modified: dd/mm/aaaa
          *  \return: json
          */
          private function getTerceroByID($cod_tercer){
            $sql="SELECT 
                        a.cod_tercer, a.nom_tercer
                  FROM
                        ". BASE_DATOS .".tab_tercer_tercer a
                  WHERE
                        a.cod_tercer = '".$cod_tercer."' ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a');
            return  $resultado;
          }

          private function getCodePersona($num_docume){
            $sql="SELECT cod_segper FROM ".BASE_DATOS.".tab_estudi_person
                    WHERE 
                      num_docume = '".$num_docume."' ORDER BY cod_segper DESC";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];
            return  $resultado['cod_segper'];
          }

          //usado
          function createXML($array,$name,$ruta){
            $ruta_name = $ruta.''.$name;
            //$ruta_name = "/var/www/html/ap/ctorres/sat-gl-2015/satt_intgps/files/adj_estseg/".$name;
            $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><data></data>');
            self::array_to_xml($array,$xml_data);
            $result = $xml_data->asXML($ruta_name);

            $server = 'na1p50.sftp.b2b.ibmcloud.com';
            $port = '22';
            $username = 'CEVA_CGCOLOMBIA';
            $password = 'Sterling$01p!';
            $remote_file = '/receive/'.$name;
            //self::uploadFTP($server, $port, 10000, $username, $password, $ruta_name, $remote_file);
          }

          function registraTercer($data, $pre, $com){
              $usuari = $_SESSION['datos_usuario']['cod_usuari'];
              $ciu_expcon = NULL;
              $pai_reside = NULL;
              $dep_reside = NULL;
              $ciu_reside = NULL;
              if($data['lug_exp'.$pre] != '' || $data['lug_exp'.$pre] != '0 - No Registrada'){
                $ciu_expcon = self::separarCodigoCiudad($data['lug_exp'.$pre]);
              }
              if($data['ciu_'.$com] != '' || $data['ciu_'.$com] != '0 - No Registrada'){
                $dat_rescon = self::darDatosCiudad(self::separarCodigoCiudad($data['ciu_'.$com]));
                $pai_reside = $dat_rescon['cod_paisxx'];
                $dep_reside = $dat_rescon['cod_depart'];
                $ciu_reside = $dat_rescon['cod_ciudad'];
              }
            
            $sql = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer(
                    cod_tercer, num_verifi, cod_tipdoc, 
                    nom_apell1, nom_apell2, nom_tercer,
                    abr_tercer, dir_domici, num_telef1,
                    num_telef2, num_telmov, num_faxxxx,
                    cod_paisxx, cod_depart, cod_ciudad,
                    dir_emailx, cod_contra, cod_estado,
                    nom_epsxxx, nom_arpxxx, usr_creaci,
                    fec_creaci
                ) VALUES 
                (
                  '".$data['num_doc'.$pre]."', '0', '".$data['tip_doc'.$pre]."',
                  '".utf8_decode($data['nom_ap1'.$pre])."', '".utf8_decode($data['nom_ap2'.$pre])."', '".utf8_decode($data['nom_nom'.$pre])."',
                  '".utf8_decode($data['nom_ap1'.$pre])." ".utf8_decode($data['nom_ap2'.$pre])." ".utf8_decode($data['nom_nom'.$pre])."', '".$data['dir_dom'.$pre]."', '".$data['num_tel'.$pre]."',
                  '', '".$data['num_mo1'.$pre]."', '',
                  '".$pai_reside."', '".$dep_reside."', '".$ciu_reside."',
                  '".$data['dir_ema'.$pre]."', 0, 1,
                  '".$data['nom_eps'.$pre]."', '".$data['nom_arl'.$pre]."', '".$usuari."', NOW()
                ) ON DUPLICATE KEY UPDATE 
                  nom_apell1 = '".$data['nom_ap1'.$pre]."', nom_apell2 = '".$data['nom_ap2'.$pre]."', nom_tercer = '".$data['nom_nom'.$pre]."',
                  abr_tercer = '".$data['nom_ap1'.$pre]." ".$data['nom_ap2'.$pre]." ".$data['nom_nom'.$pre]."', dir_domici = '".$data['dir_dom'.$pre]."', num_telef1 = '".$data['num_tel'.$pre]."',
                  num_telmov = '".$data['num_mo1'.$pre]."', cod_paisxx = '".$pai_reside."', cod_depart = '".$dep_reside."', cod_ciudad = '".$ciu_reside."',
                  dir_emailx = '".$data['dir_ema'.$pre]."', nom_epsxxx = '".$data['nom_eps'.$pre]."', nom_arpxxx = '".$data['nom_arl'.$pre]."',
                  usr_modifi = '".$usuari."', fec_modifi = NOW()
                  ";
                  $query = new Consulta($sql, self::$conexion);
          }

          //usado
          function procesaConductor($data, $dataSol){
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
            $sql="UPDATE ".BASE_DATOS.".tab_estseg_tercer
                    SET 
                      ciu_expdoc = '".$ciu_expcon."', 
                      num_licenc = '".$data['num_licenc']."',
                      cod_catlic = '".$data['tip_catlic']."',
                      fec_venlic = '".date("Y-m-d", strtotime($data['fec_venlic']))."', 
                      nom_arlxxx = '".ucwords(strtolower(utf8_decode($data['nom_arlcon'])))."', 
                      nom_epsxxx = '".ucwords(strtolower(utf8_decode($data['nom_epscon'])))."', 
                      num_telmov = '".$data['num_mo1con']."', 
                      num_telefo = '".$data['num_telcon']."',
                      cod_paisxx = '".$pai_reside."', 
                      cod_depart = '".$dep_reside."', 
                      cod_ciudad = '".$ciu_reside."',
                      dir_domici = '".strtoupper(utf8_decode($data['dir_domcon']))."', 
                      dir_emailx = '".strtolower(utf8_decode($data['dir_emacon']))."',
                      ind_precom = '".$data['pregu1con']."', 
                      val_compar = '".$data['val_comcon']."', 
                      ind_preres = '".$data['pregu2con']."',
                      val_resolu = '".$data['val_rescon']."', 
                      usr_modifi = '".self::$cod_usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_tercer = '".$dataSol['cod_conduc']."' ";
            $query = new Consulta($sql, self::$conexion);
            
            self::guardaArchivos($dataSol['cod_solici'], 2);
            if($query){
              return true;
            }
            return false;
            
          }

          
          function procesaPoseedor($data, $dataSol){
            $ciu_exppos = NULL;
            $pai_reside = NULL;
            $dep_reside = NULL;
            $ciu_reside = NULL;
            if($data['lug_exppos'] != '' || $data['lug_exppos'] != '0 - No Registrada'){
              $ciu_exppos = self::separarCodigoCiudad($data['lug_exppos']);
            }
            if($data['ciu_poseed'] != '' || $data['ciu_poseed'] != '0 - No Registrada'){
              $dat_respos = self::darDatosCiudad(self::separarCodigoCiudad($data['ciu_poseed']));
              $pai_reside = $dat_respos['cod_paisxx'];
              $dep_reside = $dat_respos['cod_depart'];
              $ciu_reside = $dat_respos['cod_ciudad'];
            }
            $sql="UPDATE ".BASE_DATOS.".tab_estseg_tercer
                    SET 
                      ciu_expdoc = '".$ciu_exppos."', 
                      num_telmov = '".$data['num_mo1pos']."', 
                      num_telefo = '".$data['num_telpos']."',
                      cod_paisxx = '".$pai_reside."', 
                      cod_depart = '".$dep_reside."', 
                      cod_ciudad = '".$ciu_reside."',
                      dir_domici = '".strtoupper(utf8_decode($data['dir_dompos']))."', 
                      dir_emailx = '".strtolower(utf8_decode($data['dir_emapos']))."',
                      usr_modifi = '".self::$cod_usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_tercer = '".$dataSol['cod_poseed']."' ";
            $query = new Consulta($sql, self::$conexion);
            
            self::guardaArchivos($dataSol['cod_solici'], 3);
            if($query){
              return true;
            }
            return false;
          }

          
          function procesaPropietario($data, $dataSol){
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
            $sql="UPDATE ".BASE_DATOS.".tab_estseg_tercer
                    SET 
                      ciu_expdoc = '".$ciu_exppro."', 
                      num_telmov = '".$data['num_mo1pro']."', 
                      num_telefo = '".$data['num_telpro']."',
                      cod_paisxx = '".$pai_reside."', 
                      cod_depart = '".$dep_reside."', 
                      cod_ciudad = '".$ciu_reside."',
                      dir_domici = '".strtoupper(utf8_decode($data['dir_dompro']))."', 
                      dir_emailx = '".strtolower(utf8_decode($data['dir_emapro']))."',
                      usr_modifi = '".self::$cod_usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_tercer = '".$dataSol['cod_propie']."' ";
            $query = new Consulta($sql, self::$conexion);
            
            self::guardaArchivos($dataSol['cod_solici'], 4);
            if($query){
              return true;
            }
            return false;
            
          }
          
          //usado
          function procesaVehiculo($data, $dataSol){
            $sql="UPDATE ".BASE_DATOS.".tab_estseg_vehicu
                    SET  
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
                      usr_gpsxxx = '".utf8_decode($data['usr_gpsxxx'])."', 
                      clv_gpsxxx = '".utf8_decode($data['clv_gpsxxx'])."', 
                      url_gpsxxx = '', 
                      idx_gpsxxx = '".$data['idx_gpsxxx']."',
                      obs_opegps = '".$data['obs_opegps']."',
                      fre_opegps = '".$data['fre_opegps']."',
                      ind_precom = '".$data['pre_comveh']."', 
                      val_compar = '".$data['val_comveh']."', 
                      ind_preres = '".$data['ind_preres']."',
                      val_resolu = '".$data['val_resveh']."', 
                      usr_modifi = '".self::$cod_usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      num_placax = '".$dataSol['cod_vehicu']."' ";
            $query = new Consulta($sql, self::$conexion);
            self::guardaArchivos($dataSol['cod_solici'], 1);
            
            if($dataSol['cod_poseed'] != $dataSol['cod_propie']){
              $valposeed = self::procesaPoseedor($data, $dataSol);
              $valpropiet =self::procesaPropietario($data, $dataSol);
            }else{
              $valposeed = self::procesaPoseedor($data, $dataSol);
            }
            
            if($query){
              return true;
            }
            return false;

          }

          function procesaDespacho($data, $dataSol){
            $cod_ciuori = self::separarCodigoCiudad($_REQUEST['ciu_origen']);
            $cod_paiori = self::separarCodigoCiudad($_REQUEST['ciu_origen'],2);
            $cod_depori = self::getInfoUbicacion($cod_ciuori, $cod_paiori)['cod_depart'];
            $cod_ciudes = self::separarCodigoCiudad($_REQUEST['ciu_destin']);
            $cod_paides = self::separarCodigoCiudad($_REQUEST['ciu_destin'],2);
            $cod_depdes = self::getInfoUbicacion($cod_ciudes, $cod_paides)['cod_depart'];
            $code_manifi = str_pad($dataSol['cod_solici'], 6, '0', STR_PAD_LEFT);
            $cod_manifi = 'ES'.$code_manifi;
            $sql="UPDATE ".BASE_DATOS.".tab_estseg_despac
                    SET  
                      cod_manifi = '".$cod_manifi ."', 
                      fec_despac = 'NOW()',
                      cod_tipdes = '".$data['tip_despac']."',
                      cod_paiori = '".$cod_paiori."', 
                      cod_depori = '".$cod_depori."', 
                      cod_ciuori = '".$cod_ciuori."', 
                      cod_paides = '".$cod_paides."',
                      cod_depdes = '".$cod_depdes."',
                      cod_ciudes = '".$cod_ciudes."',
                      cod_rutasx = '".$data['rut_despac']."',
                      val_declar = '".$data['val_declar']."',
                      val_pesoxx = '".$data['val_pesoxx']."',
                      cod_agenci = '".$data['age_despac']."', 
                      cod_genera = '".$data['gen_despac']."', 
                      usr_modifi = '".self::$cod_usuari."', 
                      fec_modifi = NOW() 
                    WHERE 
                      cod_despac = '".$dataSol['cod_despac']."' ";
            $query = new Consulta($sql, self::$conexion);
            if($query){
              return true;
            }
            return false;
          }

          function getInfoUbicacion($cod_ciudad, $cod_paisx){
            $sql = "SELECT cod_paisxx, cod_depart, cod_ciudad
                      FROM ".BASE_DATOS.".tab_genera_ciudad
                    WHERE cod_ciudad = '".$cod_ciudad."' AND
                          cod_paisxx = '".$cod_paisxx."'; ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];
            return $resultado;
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

          function separarCodigoCiudad($dato, $retorno = 1) {
            $regex = '/\((\d+)\-(\d+)\)/';
            if (preg_match($regex, $dato, $matches)) {
                // Almacenar los valores en una sola variable
                $codigo = array(trim($matches[1]), trim($matches[2]));
                // Devolver el valor correspondiente basado en el parámetro $retorno
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

          /* Arma Array Info */

          function armaArrayInfo($cod_solici, $type = NULL){
            $infoGen = self::getInfoSolici($cod_solici);
            $dataSol = array(
              'application_number' => $infoGen['cod_solici'],
              'company_code' => $infoGen['cod_emptra'],
              'company_name' => $infoGen['nom_tercer'],
              'company_email' => $infoGen['cor_solici'],
              'company_phone' => $infoGen['tel_solici'],
              'company_mobile' => $infoGen['cel_solici'],
              'security_study_code' => $infoGen['cod_solici'],
              'status_study' => $infoGen['ind_estseg'],
              'observation_study' => $infoGen['obs_estseg'],
              'user_study' => $infoGen['usr_estseg'],
              'expiration' => $infoGen['fec_venest']
            );

            if($infoGen['cod_tipest'] == 'C'){
              $dataSol['driver'] = self::armaArrayPerson($infoGen['cod_conduc'],1,$infoGen['cod_solici'],2);
            }else if($infoGen['cod_tipest'] == 'V'){
              $dataSol['vehicle'] = self::armaArrayVehicu($infoGen['cod_vehicu'], $infoGen['cod_solici']);
            }else if($infoGen['cod_tipest'] == 'CV'){
              if($type=='C'){
                $dataSol['driver'] = self::armaArrayPerson($infoGen['cod_conduc'],1,$infoGen['cod_solici'],2);
              }else if($type=='V'){
                $dataSol['vehicle'] = self::armaArrayVehicu($infoGen['cod_vehicu'], $infoGen['cod_solici']);
              } 
            }

            if( $infoGen['ind_credes'] ){
              $dataSol['dispatch'] = self::armaArrayDespac($infoGen['cod_despac'],$infoGen['cod_solici']);
            }

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

          //usado
          function armaArrayPerson($cod_tercer,$ind_person = 0, $cod_solici, $cod_tipper){
            $sql = "SELECT
                      a.cod_tipdoc, b.nom_tipdoc, a.cod_tercer,
                      a.ciu_expdoc, a.nom_apell1, a.nom_apell2,
                      a.nom_person, a.num_licenc, a.cod_catlic,
                      a.fec_venlic, a.nom_arlxxx, a.nom_epsxxx,
                      a.num_telmov, a.num_telmo2, a.num_telefo,
                      a.cod_paisxx, a.cod_depart, a.cod_ciudad,
                      d.nom_ciudad, a.dir_domici, a.dir_emailx
                    FROM ".BASE_DATOS.".tab_estseg_tercer a
                INNER JOIN ".BASE_DATOS.".tab_genera_tipdoc b ON
                    a.cod_tipdoc = b.cod_tipdoc
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d ON
                    a.cod_ciudad = d.cod_ciudad AND a.cod_depart = d.cod_depart AND a.cod_paisxx = d.cod_depart
                    WHERE a.cod_tercer = '".$cod_tercer."'";
              $query = new Consulta($sql, self::$conexion);
              $resultados = $query -> ret_matrix('a')[0];
              $dataPerson = array(
                'document_typeID' =>  array(
                                          'id' => $resultados['cod_tipdoc'],
                                          'name' => $resultados['nom_tipdoc']
                ),
                'document_number' => $resultados['cod_tercer'],
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
                'files' => self::getFiles($cod_tipper,$cod_solici)
              );

              if($ind_person==1){
                $arr_conduc = array(
                  'license_number' => $resultados['num_licenc'],
                  'license_category' => $resultados['cod_catlic'],
                  'license_expiration' => $resultados['fec_venlic'],
                  'name_arl' => $resultados['nom_arlxxx'],
                  'name_eps' => $resultados['nom_epsxxx'],
                  'family_referrals' => self::armaReferences($cod_tercer, 'F'),
                  'personal_referrals' => self::armaReferences($cod_tercer, 'P'),
                  'laboral_referrals' => self::armaReferenceslaboral($cod_tercer),
                );
                $dataPerson = (array)$dataPerson + (array)$arr_conduc;
              }
              return $dataPerson;
          }

          //usado
          function armaArrayVehicu($cod_vehicu, $cod_solici){
            $sql = "SELECT
                      a.num_placax, a.cod_poseed, a.cod_propie, a.num_remolq, a.ano_modelo, a.cod_colorx, a.cod_marcax, e.nom_marcax, a.cod_lineax, f.nom_lineax,
                      b.nom_colorx, a.cod_carroc, c.nom_carroc, a.num_config, d.nom_config, a.num_chasis, a.num_motorx, a.num_soatxx,
                      a.fec_vigsoa, a.num_lictra, g.cod_operad as 'cod_opegps', g.nit_operad, g.nom_operad, a.usr_gpsxxx, a.clv_gpsxxx, a.url_gpsxxx,
                      a.idx_gpsxxx, a.obs_opegps, a.fre_opegps
                    FROM ".BASE_DATOS.".tab_estseg_vehicu a
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
              BINARY a.cod_opegps = BINARY g.cod_operad
                    WHERE a.num_placax = '".$cod_vehicu."'";
              $query = new Consulta($sql, self::$conexion);
              $resultados = $query -> ret_matrix('a')[0];
              if($resultados['cod_opegps']==''){
                mail('cristian.torres@grupooet.com','Alerta estudio de seguridad Operador GPS',$sql);
              }
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
                'holder' => self::armaArrayPerson($resultados['cod_poseed'], 0, $cod_solici,3),
                'owner' => self::armaArrayPerson($resultados['cod_propie'], 0, $cod_solici,4),
                'gps' => array(
                  'code' => $resultados['cod_opegps'],
                  'document_number' => $resultados['nit_operad'],
                  'name' => $resultados['nom_operad'],
                  'username' => trim($resultados['usr_gpsxxx']),
                  'password' => trim($resultados['clv_gpsxxx']),
                  'url' => $resultados['url_gpsxxx'],
                  'id' =>  $resultados['idx_gpsxxx'],
                  'observation' => $resultados['obs_opegps'],
                  'frequency' => $resultados['fre_opegps']
                ),
                'files' => self::getFiles(1,$cod_solici)
              );
              return $dataVehicu;
          }

          function armaArrayDespac($cod_despac, $cod_solici){
            $sql = "SELECT
                      a.cod_manifi, a.cod_tipdes, a.cod_paiori, a.cod_ciuori,
                      a.cod_paides, a.cod_ciudes, a.cod_rutasx, a.val_declar,
                      a.val_pesoxx, a.cod_genera, a.cod_agenci
                    FROM ".BASE_DATOS.".tab_estseg_despac a
                      WHERE a.cod_despac = '".$cod_despac."'";
              $query = new Consulta($sql, self::$conexion);
              $resultados = $query -> ret_matrix('a')[0];
             
              $dataDespac= array(
                'manifest_code' => $resultados['cod_manifi'],
                'dispatch_type' => $resultados['cod_tipdes'],
                'origin' => array(
                    'country_code' => $resultados['cod_paiori'],
                    'city_code' => $resultados['cod_ciuori']
                ),
                'destination' => array(
                  'country_code' => $resultados['cod_paides'],
                  'city_code' => $resultados['cod_ciudes']
                ),
                'route_code' => $resultados['cod_rutasx'],
                'value_declarade' =>  $resultados['val_declar'],
                'weight' =>  $resultados['val_pesoxx'],
                'agenci_code' => $resultados['cod_agenci'],
                'generator_code' =>  $resultados['cod_genera']
              );
              return $dataDespac;
          }

          //usado
          function getFiles($cod_person, $cod_solici){
            $mSql="SELECT a.cod_fordoc, a.nom_fordoc, a.nom_slugxx,
                         a.ing_obliga
                  FROM ".BASE_DATOS.".tab_estseg_fordoc a
                              WHERE a.ind_status = 1 AND
                                    a.cod_tipper = '".$cod_person."'
                              ORDER BY ind_ordenx, a.nom_fordoc ASC
            ";
            $resultado = new Consulta($mSql, self::$conexion);
            $resultados = $resultado->ret_matriz('a');

            foreach ($resultados as $registro){
              $mSql="SELECT a.nom_archiv, a.obs_archiv, a.nom_rutfil FROM ".BASE_DATOS.".tab_estseg_docume a
              WHERE a.cod_solici = '".$cod_solici."' AND
                    a.cod_fordoc = '".$registro['cod_fordoc']."'";
              $resul = new Consulta($mSql, self::$conexion);
              $documento = $resul->ret_matriz('a')[0];
              $rut_general = URL_APLICA.'files/adj_estseg/adjs/';
              $datos = array(
                'url_file' => $rut_general.''.$documento['nom_archiv'],
                'observation' => $documento['obs_archiv']
              );
              $arrayData[$registro['nom_slugxx']] = $datos;
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

          //usado
          function armaReferences($cod_person, $tip_refere){
            $sql = "SELECT 
                      b.nom_refere, b.nom_parent, b.dir_domici, 
                      b.num_telefo 
                    FROM 
                      ".BASE_DATOS.".tab_estseg_relref a, 
                      ".BASE_DATOS.".tab_estseg_refere b 
                    WHERE 
                      a.cod_estper = '".$cod_person."'
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

          //usado
          function armaReferenceslaboral($cod_person){
            $sql = "SELECT 
                      b.nom_transp, b.num_telefo, b.inf_sumini, 
                      b.num_viajes 
                    FROM 
                      ".BASE_DATOS.".tab_estseg_relref a, 
                      ".BASE_DATOS.".tab_estseg_reflab b 
                    WHERE 
                      a.cod_estper = '".$cod_person."'
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
        
        //usado
        function array_to_xml( $data, &$xml_data ) {
          foreach( $data as $key => $value ) {
              if( is_array($value) ) {
                  if( is_numeric($key) ){
                      $key = 'item'.$key; //dealing with <0/>..<n/> issues
                  }
                  $subnode = $xml_data->addChild($key);
                  self::array_to_xml($value, $subnode);
              } else {
                  $xml_data->addChild("$key",utf8_encode("$value"));
              }
           }
        }


        function uploadFTP($server, $port, $timeout, $username, $password, $local_file, $remote_file){
        try{
            $conn_id = ftp_connect($server);

            // login with username and password
            $login_result = ftp_login($conn_id, $username, $password);

            if(!$conn_id || !$login_result){
              echo "No se pudo establecer conexion.";
            }
            // upload a file
            if (ftp_put($conn_id, $remote_file, $local_file, FTP_ASCII)) {
                echo "successfully uploaded $local_file\n";
                exit;
            } else {
                echo "There was a problem while uploading $local_file\n";
                echo "<br>".$remote_file;
                exit;
            }
            // close the connection
            ftp_close($conn_id);
        }catch(Exception $e){
          echo "Failure: " . $e->getMessage();
        }
          

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

        //usado
        private function darCorreos($cod_solici,$ind_retorn){
          if($ind_retorn==1){
            //Extrae correo registrado al momento de realizar la solicitud de estudio de seguridad
            $sql = "SELECT a.cor_solici as 'dir_emailx'
                   FROM ".BASE_DATOS.".tab_estseg_solici a
                   WHERE a.cod_solici = '".$cod_solici."' ";
          }else{
            //Extrae correos administrador del sistema NOTIFICACIÃ“N FARO
            $sql = "SELECT a.dir_emailx
                      FROM ".BASE_DATOS.".tab_genera_concor a
                    WHERE a.num_remdes = '' AND ind_estseg = 1; ";
          }
          $query = new Consulta($sql, self::$conexion);
          $correos = $query->ret_matriz('a');
          $cantidad = count($correos);
          foreach($correos as $key=> $value){
            $resultado['dir_emailx'] .= $value['dir_emailx'];
            if($key+1<$cantidad){
              $resultado['dir_emailx'].=',';
            }
          }
          return $resultado;
        }

        //usado
        private function enviarCorreo($cod_solici, $ind_retorn, $subject, $contenido, $files = NULL) {
            //Trae los correos segun el caso
            $emailsTotal = self::darCorreos($cod_solici,$ind_retorn);
            
            $emails = explode(",", $emailsTotal['dir_emailx']);
            //$tmpl_file = URL_ARCHIV_STANDA.'satt_standa/estseg/planti/template-email.html';
            $tmpl_file = 'planti/template-email.html';
            $logo = LOGOFARO;
            $ano = date('Y');
            $thefile = implode("", file( $tmpl_file ) );
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"".$thefile."\";";
            eval( $thefile );
            $mHtml = $r_file;

            //require_once(URL_ARCHIV_STANDA."satt_standa/planti/class.phpmailer.php");
            require_once('../planti/class.phpmailer.php');
            $mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';
            $mail->Host = "localhost";
            $mail->From = 'seguimientos@faro.com.co';
            $mail->FromName = 'EST. SEGURIDAD';
            $mail->Subject = utf8_decode($subject) ;
            foreach($emails as $email){
              $mail->AddAddress( $email );
            }
            $mail->Body = $mHtml;
            $mail->IsHTML( true );
            
            if($files != NULL){
              foreach($files as $key=>$file){
                  if(is_readable(URL_ARCHIV_STANDA.''.BASE_DATOS.'/files/adj_estseg/adjs/'.$file['archivo'])){
                    $mail->addAttachment(URL_ARCHIV_STANDA.''.BASE_DATOS.'/files/adj_estseg/adjs/'.$file['archivo'], $file['name']);
                  }
              }
            }

            $mail->send();
        }

        function getPDFEstSeg( $cod_person, $cod_solici ){
          $mSelect = "SELECT b.nom_archiv, b.nom_tipfil, a.nom_fordoc, b.obs_archiv
                      FROM ".BASE_DATOS.".tab_estseg_fordoc a
                INNER JOIN ".BASE_DATOS.".tab_estseg_docume b ON a.cod_fordoc = b.cod_fordoc
                      WHERE a.cod_tipper = '".$cod_person."' AND b.cod_solici = '".$cod_solici."'
                      AND nom_tipfil IN ('pdf');";
          $query = new Consulta($mSelect, self::$conexion);
          $resultados = $query -> ret_matriz('a');
          return $resultados;
        }

        //usado
        private function armaPDF(){
          try {
                $cod_solici = $_REQUEST['cod_solici'];
                $info = self::getInfoSolici($cod_solici);
                $ruta = URL_ARCHIV."files/adj_estseg/pdfs/";
                $rutaad = URL_ARCHIV."files/adj_estseg/adjs/";
                $name = $cod_solici.'_InformeFinal_'.time()."_Temp.pdf";
                $path = $ruta.''.$name;
                if(!move_uploaded_file($_FILES['file']['tmp_name'], $path)){
                  mail('cristian.torres@grupooet.com', 'Prueba', 'No se pudo guardar el archivo -> ' . $path);
                }
                $fileArray= array($path);
                if($info['cod_tipest']=='V'){
                  $pdf_vehicu = self::getPDFEstSeg(1,$cod_solici);
                  foreach($pdf_vehicu as $docume){
                    array_push($fileArray, $rutaad.$docume['nom_archiv']);
                  }
                  $pdf_poseed = self::getPDFEstSeg(3,$cod_solici);
                  foreach($pdf_poseed as $docume){
                    array_push($fileArray, $rutaad.$docume['nom_archiv']);
                  }
                  $pdf_propie = self::getPDFEstSeg(4,$cod_solici);
                  foreach($pdf_propie as $docume){
                    array_push($fileArray, $rutaad.$docume['nom_archiv']);
                  }
                }else{
                  $pdf_conduc = self::getPDFEstSeg(2,$cod_solici);
                  foreach($pdf_conduc as $docume){
                    array_push($fileArray, $rutaad.$docume['nom_archiv']);
                  }
                }

                $datadir = $ruta;
                $nameFinal = $cod_solici.'_Resultado_'.time().'.pdf';
                $outputName = $datadir.$nameFinal;

                $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputName ";
                //Add each pdf file to the end of the command
                foreach($fileArray as $file) {
                    $cmd .= $file." ";
                }
                $result = shell_exec($cmd);
                unlink($path);
                $sql="UPDATE ".BASE_DATOS.".tab_estseg_solici
                              SET 
                                fil_result = '".$nameFinal."'
                          WHERE 
                            cod_solici = '".$cod_solici."'";
                if(new Consulta($sql, self::$conexion)){
                  return array(
                    'status' => true,
                    'message' => 'PDF Generado Correctamente.'
                  );
                };
          } catch (Exception $e) {
            self::generateLog("Código Solicitud: ".$_REQUEST['cod_solici']." || ".$e->getMessage(), $e->getCode());
            return array(
              'status' => false,
              'error' => array(
                  'code' => $e->getCode(),
                  'message' => $e->getMessage()
              )
            );
          }
        }
       
        //usado
        private function sendEmail(){
          try {
                $dataResp = self::armaPDF();

                if(!$dataResp['status']){
                  throw new Exception("El archivo PDF no pudo ser generado", "2001");
                }
                $cod_solici = $_REQUEST['cod_solici'];
                $mSelect = "SELECT fil_result
                              FROM ".BASE_DATOS.".tab_estseg_solici
                            WHERE 
                            cod_solici = '".$cod_solici."'";
                $query = new Consulta($mSelect, self::$conexion);
                $docume = $query -> ret_matriz('a')[0];

                $emailsTotal = $_REQUEST['emails'];
                $subject = 'RESULTADO DE ESTUDIO DE SEGURIDAD No. '.$cod_solici;

                $emails = explode(",", $emailsTotal);
                $tmp_file = dirname(dirname(__FILE__)).'/estsegv2/planti/template-email.html';
                
                $contenido = '<p>Centro Log&iacute;stico FARO hace el envio del documento adjunto en este correo con el resultado del estudio de seguridad No.<strong>'.$cod_solici.'</strong></p>
                              <p>No responder -- Este correo ha sido creado automaticamente.</p>';
                $logo = LOGOFARO;
                $ano = date('Y');
                $thefile = implode("", file( $tmp_file ) );
                $thefile = addslashes($thefile);
                $thefile = "\$r_file=\"".$thefile."\";";
                eval( $thefile );
                $mHtml = $r_file;
                require_once("../planti/class.phpmailer.php");
                $mail = new PHPMailer();
                $mail->Host = "localhost";
                $mail->From = 'seguimientos@faro.com.co';
                $mail->FromName = 'EST. SEGURIDAD';
                $mail->Subject = $subject ;
                foreach($emails as $email){
                  $mail->AddAddress( $email );
                }
                $mail->Body = $mHtml;
                $mail->IsHTML( true );
                if($docume['fil_result'] != NULL){
                  $mail->AddAttachment(URL_ARCHIV."files/adj_estseg/pdfs/".$docume['fil_result']);
                }
                if($mail->Send()){
                  echo json_encode(array(
                    'status' => true,
                    'message' => 'Correo Enviado Correctamente.'
                  ));
                }else{
                  throw new Exception("No se pudo enviar el correo.", "2001");
                }
          } catch (Exception $e) {
            self::generateLog("Código Solicitud: ".$_REQUEST['cod_solici']." || ".$e->getMessage(), $e->getCode());
            echo json_encode(array(
              'status' => false,
              'error' => array(
                  'code' => $e->getCode(),
                  'message' => $e->getMessage()
              )
            ));
          }
        }

        private function reSendEmail(){
          try {
                  $cod_solici = $_REQUEST['cod_solici'];
                  $mSelect = "SELECT fil_result
                                FROM ".BASE_DATOS.".tab_estseg_solici
                              WHERE 
                              cod_solici = '".$cod_solici."'";
                  $query = new Consulta($mSelect, self::$conexion);
                  $docume = $query -> ret_matriz('a')[0];
                  if($docume['fil_result'] == NULL){
                    throw new Exception("No hay archivo PDF Generado", "2001");
                  }

                  $emailsTotal = $_REQUEST['emails'];
                  $subject = 'REE - :: RESULTADO DE ESTUDIO DE SEGURIDAD No. '.$cod_solici;

                  $emails = explode(",", $emailsTotal);
                  $tmp_file = dirname(dirname(__FILE__)).'/estsegv2/planti/template-email.html';
                  
                  $contenido = '<p>Centro Log&iacute;stico FARO hace el Reenvio del documento adjunto en este correo con el resultado del estudio de seguridad No.<strong>'.$cod_solici.'</strong></p>
                                <p>No responder -- Este correo ha sido creado automaticamente.</p>';
                  $logo = LOGOFARO;
                  $ano = date('Y');
                  $thefile = implode("", file( $tmp_file ) );
                  $thefile = addslashes($thefile);
                  $thefile = "\$r_file=\"".$thefile."\";";
                  eval( $thefile );
                  $mHtml = $r_file;
                  require_once("../planti/class.phpmailer.php");
                  $mail = new PHPMailer();
                  $mail->Host = "localhost";
                  $mail->From = 'seguimientos@faro.com.co';
                  $mail->FromName = 'EST. SEGURIDAD';
                  $mail->Subject = $subject ;
                  foreach($emails as $email){
                    $mail->AddAddress( $email );
                  }
                  $mail->Body = $mHtml;
                  $mail->IsHTML( true );
                  if($docume['fil_result'] != NULL){
                    $mail->AddAttachment(URL_ARCHIV."files/adj_estseg/pdfs/".$docume['fil_result']);
                  }
                  if($mail->Send()){
                    echo json_encode(array(
                      'status' => true,
                      'message' => 'Correo Enviado Correctamente.'
                    ));
                  }else{
                    throw new Exception("No se pudo enviar el correo.", "2001");
                  }
          } catch (Exception $e) {
            self::generateLog("Código Solicitud: ".$_REQUEST['cod_solici']." || ".$e->getMessage(), $e->getCode());
            echo json_encode(array(
              'status' => false,
              'error' => array(
                  'code' => $e->getCode(),
                  'message' => $e->getMessage()
              )
            ));
          }
        }

        private function generateLog($msj, $code){
          $logFile = fopen( URL_ARCHIV."files/adj_estseg/logs/"."log_".date("Y-m-d").".txt", 'a') or die("Error creando archivo");
          $msjLog = "================================================\n";
          $msjLog .= "Fecha: ".date("Y-m-d H:i:s")."\n";
          $msjLog .= "------------------------------------------------\n";
          $msjLog .= "Error::".$msj."\n";
          $msjLog .= "Code::".$code."\n";
          $msjLog .= "\n\n";
          fwrite($logFile, $msjLog) or die("Error escribiendo en el archivo");fclose($logFile);
        }

        //usado
        private function armaHtmlCreacion($cod_solici, $ind_enviox){
          $sql = "SELECT b.nom_tercer
                      FROM ".BASE_DATOS.".tab_estseg_solici a
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                        ON a.cod_emptra = b.cod_tercer
                    WHERE a.cod_solici = '".$cod_solici."'; ";
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query->ret_matriz('a')[0];

          if($ind_enviox==1){
            $html.='<p>Estimado: <strong>'.$resultado['nom_tercer'].'</strong></p>
                    <p>Centro Log&iacute;stico FARO informa que su solicitud No.<strong>'.$cod_solici.'</strong> se ha creado exitosamente. Le estaremos informando los avances de esta solicitud.</p>';
          }else{
            $html.='<p>Estimado: <strong>CENTRO LOG&Iacute;STICO FARO SAS</strong></p>
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
                  <p>Centro Log&iacute;stico FARO informa que su solicitud No.<strong>'.$cod_solici.' - '.$cod_estseg.'</strong> ha cambiado a estado <strong>'.$estado.'</strong> '.$add.'</p>
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

        /*! \fn: getPDFGenerado
        *  \brief: retorna la ubicaciï¿½n del PDF generado del estudio de seguridad
        *  \author: Ing. Cristian Andrï¿½s Torres
        *  \date:  25/11/2022
        *  \date modified: dd/mm/aaaa
        *  \modified by: 
        *  \param: 
        *  \return: json
        */
        function getPDFGenerado(){
          try {
            $cod_solici = $_REQUEST['cod_solici'];
            $mSelect = "SELECT fil_result
                                FROM ".BASE_DATOS.".tab_estseg_solici
                              WHERE 
                              cod_solici = '".$cod_solici."'";
            $query = new Consulta($mSelect, self::$conexion);
            $docume = $query -> ret_matriz('a')[0];
            if($docume['fil_result'] != NULL){
              echo json_encode(array(
                'status' => true,
                'resp' => array(
                  'file_url' => URL_APLICA.'files/adj_estseg/pdfs/'.$docume['fil_result'],
                  'file_name' => $docume['fil_result']
                ),
                'message' => utf8_encode('PDF Abierto en una nueva pestaña')
              ));
            }else{
              throw new Exception("No hay archivo PDF Generado", "2001");
            }
          } catch (Exception $e) {
            self::generateLog("Código Solicitud: ".$_REQUEST['cod_solici']." || ".$e->getMessage(), $e->getCode());            echo json_encode(array(
              'status' => false,
              'error' => array(
                  'code' => $e->getCode(),
                  'message' => $e->getMessage()
              )
            ));
          }
        }

        private function notifiRecurExiste($cod_recurs, $cod_solici){
            try {
              $subject = 'Existencia de recurso';
              $emailsTotal = self::darCorreos(NULL,2);
              $emails = explode(",", $emailsTotal['dir_emailx']);
              $tmp_file = dirname(dirname(__FILE__)).'/estsegv2/planti/template-email.html';
              $contenido = '<p>Centro Log&iacute;stico FARO notifica que el recurso: <strong>' . $cod_recurs . '</strong> tiene un estudio de seguridad vigente por finalizar o uno finalizado no vencido Codigo::('.$cod_solici.'). Agradecemos validar en nuestros registros.</p>';
              $logo = LOGOFARO;
              $ano = date('Y');
              $thefile = implode("", file($tmp_file));
              $thefile = addslashes($thefile);
              $thefile = "\$r_file=\"" . $thefile . "\";";
              eval($thefile);
              $mHtml = $r_file;
              require_once("../planti/class.phpmailer.php");
              $mail = new PHPMailer();
              $mail->Host = "localhost";
              $mail->From = 'seguimientos@faro.com.co';
              $mail->FromName = 'EST. SEGURIDAD';
              $mail->Subject = $subject;
              foreach ($emails as $email) {
                $mail->AddAddress($email);
              }
              $mail->Body = $mHtml;
              $mail->IsHTML(true);
              if (!$mail->Send()) {
                throw new Exception("No se pudo enviar el correo.", "2001");
              }
            }
          catch (Exception $e) {
            self::generateLog("Correo no enviado:::::::::SOLICITUD INICIAL VALIDACION DE RECURSO || ".$e->getMessage(), $e->getCode());
          }
        }

    }

    new AjaxGeneraEstSeg();
    
?>
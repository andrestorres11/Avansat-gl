<?php
    /****************************************************************************
	NOMBRE:   GeneraEstudioSeguridad
	FUNCION:  Muestra las solicitudes del estudio de seguridad. 
	FECHA DE MODIFICACION: 15/09/2020
	CREADO POR: Ing. Cristian Andrés Torres
	MODIFICADO 
	****************************************************************************/
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/
    class GeneraEstudioSeguridad
    {
        
        var $conexion,
        $cod_aplica,
        $usuario;

        function __construct($co, $us, $ca){
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;
            $this -> enrutarPeticion();
            
        }

        private function enrutarPeticion() {
          switch ($_REQUEST['opcion']) {
          case 'formEstSeguridad':
              self::mostrarFormularioEstSeguridad();
              break;
          case 'downloadZip':
                self::downloadZip();
                break;
          default:
              self::principal();
          }
      }

        /*! \fn: styles
		   *  \brief: incluye todos los archivos necesarios para los estilos
		   *  \author: Ing. Cristian Andrés Torres
		   *  \date: 04-06-2020
		   *  \date modified: dd/mm/aaaa
		   *  \param: 
		   *  \return: html
		*/

        private function styles(){
            echo '
                <!-- Bootstrap -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.min.css" rel="stylesheet">

                <!-- Font Awesome -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
                
                <!-- Jquery UI -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
                
                <!-- Datatables all in one-->
                <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.css" rel="stylesheet">

                <!-- Float Menu -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inform_style.css" rel="stylesheet">
                
            ';
        }

        /*! \fn: scripts
		   *  \brief: incluye todos los archivos necesarios para los eeventos js
		   *  \author: Ing. Cristian Andrés Torres
		   *  \date: 04-06-2020
		   *  \date modified: dd/mm/aaaa
		   *  \param: 
		   *  \return: html
		*/
        private function scripts(){

            echo '
                <!-- Moment -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/moment/moment.js"></script>

                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery-ui-1.12.1/jquery.blockUI.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Form validate -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

                <!-- SweetAlert -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>
                
                <!-- Datatables ALL IN ONE-->
                <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.js" ></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_genera_estseg.js"></script>
            ';
        }

        private function downloadZip(){
          try{
            $rut_general = dirname(dirname(__DIR__)).'/'.BASE_DATOS.'/files/adj_estseg/';
            $cod_estseg = $_REQUEST['cod_estseg'];
            $con_wherex = ' a.cod_estseg = "'.$cod_estseg.'"';
            $temp_name = $cod_estseg.'_'.time().'.zip';
            $archive_file_name=$rut_general.''.$temp_name;

            $zip = new ZipArchive();
            $zip->open($archive_file_name,ZipArchive::CREATE);
            $nom_documen = array('Licencia_del_vehiculo', 'Tarjeta_de_propiedad_del_trailer', 'Tecnomecanica', 'Soat', 'Licencia_de_transito_conductor', 'Documento_del_propietario', 'Documento_del_conductor', 'Licencia_del_conductor', 'Planilla_de_seguridad social', 'Registro_fotografico_vehiculo', 'Poliza_extracontractual');
            $nom_campoxs = array('fil_licveh', 'fil_tartra', 'fil_tecmec', 'fil_soatxx', 'fil_litcon', 'fil_cedpro', 'fil_cedcon', 'fil_liccon', 'fil_plsegs', 'fil_regveh', 'fil_polext');
            foreach($nom_campoxs as $key=>$campo){
              $sql = "SELECT a.".$campo."
                    FROM ".BASE_DATOS.".tab_estudi_docume a
                  WHERE ".$con_wherex." ";
              $resultado = new Consulta($sql, $this->conexion);
              $resultados = $resultado->ret_matriz('a');
              if(count($resultados) > 0 && ($resultados[0][$campo] != NULL || $resultados[0][$campo] != '')){
                $ext = explode(".", ($resultados[0][$campo]));
                $zip->addFile($rut_general.''.$resultados[0][$campo],$cod_estseg.'_'.$nom_documen[$key].'.'.end($ext));
              }
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
              unlink($archive_file_name);*/
              exit();
            }else{
              echo " el archivo no existe";
            }

          /*
          header("Cache-Control: public");
          header("Content-Description: File Transfer");
          header("Content-Disposition: attachment; filename=$temp_name");
          header("Content-Type: application/zip");
          header("Content-Transfer-Encoding: binary");

          // Read the file
          readfile($archive_file_name);
          unlink($archive_file_name);
          exit();*/
          }catch(Exception $e){
            echo "Error funcion downloadZip", $e->getMessage(), "\n";
          }

        }
        
        /*! \fn: filtros
		   *  \brief: Crea el html de las tablas filtros y segmentos del modulo
		   *  \author: Ing. Cristian Andrés Torres
		   *  \date: 04-06-2020
		   *  \date modified: dd/mm/aaaa
		   *  \param: 
		   *  \return: html
		*/

        private function principal(){
            self::styles();
            $mDateNOW = date('Y-m-d');
            $mDateTem = date_sub(date_create($mDateNOW), date_interval_create_from_date_string("7 days") );
            
            echo "<pre style='display:none'>"; print_r( $mDateTem ); echo "</pre>"; 
            $mDateYes = date("Y-m-d", strtotime($mDateTem ->date));

            $mPerms = $this->getReponsability('jso_estseg');
            $nueva_solicitudtbn = '';
            if($mPerms->dat_estseg->ind_visibl == 1){
              $nueva_solicitudtbn = '<div class="row mt-2 mb-2">
                                        <div class="col-md-4">
                                          <button type="button" onclick="" class="btn btn-success btn-sm"  data-toggle="modal" data-target="#NuevaSolicitudModal">Nueva Solicitud</button>
                                        </div>
                                      </div>';
            }
            
            //Body
            echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <div class="container" style="min-width: 98%;">
                                '.$nueva_solicitudtbn.'
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Filtros Especificos
                                          </button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">
                                                <form id="filter" method="POST">
                                                <div class="row">
                                                    <div class="col-2 text-right">
                                                        <label>Transportadora:</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <select id="transportadoraID" name="transportadora" style="width:150px;">
                                                            <option value="">Seleccione</option>
                                                            '.$this->darTransportadora().'
                                                        </select>
                                                    </div>
                                                    <div class="col-2 text-right">
                                                        <label>Num. de Solicitud:</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <input type="text" id="num_soliciID" name="num_solici">
                                                    </div>
                                                    <div class="col-1 text-right">
                                                        <label>Estado:</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <select id="estado_ID" name="estado_input" style="width:150px;">
                                                            <option value="">Seleccione</option>
                                                            <option value="1">Registrado</option>
                                                            <option value="2">En proceso</option>
                                                            <option value="3">Finalizado</option>
                                                            <option value="4">Cancelada</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-3 text-right">
                                                        <label>Fecha Inicial:</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <input type="date" id="fec_inicio" name="fec_inicio" value="'.$mDateYes.'">
                                                    </div>
                                                    <div class="col-3 text-right">
                                                        <label>Fecha Final:</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <input type="date" id="fec_finxxx" name="fec_finxxx" value="'.$mDateNOW.'">
                                                    </div>
                                                </div>
                                                <div class="row mt-4">
                                                    <div class="col-12 text-center">
                                                        <button type="button" onclick="executeFilter()" class="btn btn-success btn-sm">Filtrar</button>
                                                    </div>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success btn-sm active" style="background-color:#509334" id="pills-registradas-tab" data-toggle="pill" href="#pills-registradas" role="tab" aria-controls="pills-registradas" aria-selected="true">Registradas</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success btn-sm" style="background-color:#509334" id="pills-enproceso-tab" data-toggle="pill" href="#pills-enproceso" role="tab" aria-controls="pills-enproceso" aria-selected="false">En proceso</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success btn-sm" style="background-color:#509334" id="pills-finalizadas-tab" data-toggle="pill" href="#pills-finalizadas" role="tab" aria-controls="pills-finalizadas" aria-selected="false">Finalizados</a>
                                                </li>
                                            </ul>

                                      <div class="tab-content" id="pills-tabContent">
                                        '.$this->vRegistradas().'
                                        '.$this->vEnProceso().'
                                        '.$this->vFinalizadas().'
                                      </div>

                                        </div>
                                    </div>
                                </div>
                                '.$this->nuevaSolicitudModal().'
                                '.$this->procesoSolicitudModal().'
                                '.$this->modalVisualizarPDF().'
                                '.$this->modalVisualizarDocuments().'
                </td>
            </tr>
                                                ');
            self::scripts();
        }

        private function buscaDocumento($nom_campox, $nom_tablax, $con_where, $cod_indica){
          if($con_where != ''){
          $sql="SELECT ".$nom_campox." FROM ".BASE_DATOS.".".$nom_tablax." WHERE ".$con_where;
          $query = new Consulta($sql, $this->conexion);
          $resultado = $query -> ret_matrix('a')[0];
          $html='';
          if($cod_indica==1){
            if($resultado[$nom_campox]!=NULL){
              $html = '<p class="text-documentos">'.$resultado[$nom_campox].'</p>';
            }
          }else if($cod_indica==2){
            $html = $resultado[$nom_campox];
          }else if($cod_indica==3){
            if($resultado[$nom_campox]==NULL){
              $html = 'ncarg';
            }
          }else if($cod_indica==4){
            if($resultado[$nom_campox]==NULL){
              $html = 'docreq';
            }
          }
        }
          return $html;
        }

        private function getInfoSolicitud($cod_estseg){
              $sql = "SELECT a.cod_estseg, a.cod_solici, b.cod_emptra,
                             a.ind_fasexx,
                             b.nom_solici, b.cor_solici, b.tel_solici,
                             b.cel_solici,
                             a.cod_conduc, c.cod_tipdoc as 'tip_doccon', c.num_docume as 'num_doccon', c.nom_apell1 as 'nom_ap1con',
                             c.nom_apell2 as 'nom_ap2con', c.nom_person as 'nom_nomcon', c.num_telmov as 'num_mo1con',
                             c.num_telefo as 'num_telcon', c.num_licenc as 'num_licenc', c.fec_venlic as 'fec_venlic',
                             c.cod_catlic,
                             c.nom_arlxxx as 'nom_arlcon', c.nom_epsxxx as 'nom_epscon', c.dir_domici as 'dir_domcon',
                             c.dir_emailx as 'dir_emacon', c.ciu_expdoc as 'ciu_expcon', c.cod_ciudad as 'ciu_rescon',
                             c.ind_precom as 'pre_comcon', c.ind_preres as 'pre_rescon', c.val_compar as 'val_comcon',
                             c.val_resolu as 'val_rescon',
                             g.nom_tipdoc as 'nom_tipcon',
                             a.cod_poseed,
                             d.cod_tipdoc as 'tip_docpos', d.num_docume as 'num_docpos', d.nom_apell1 as 'nom_ap1pos',
                             d.nom_apell2 as 'nom_ap2pos', d.nom_person as 'nom_nompos', d.num_telmov as 'num_mo1pos',
                             d.num_telefo as 'num_telpos', d.dir_domici as 'dir_dompos', d.dir_emailx as 'dir_emapos',
                             d.ciu_expdoc as 'ciu_exppos', d.cod_ciudad as 'ciu_respos',
                             a.cod_propie,
                             e.cod_tipdoc as 'tip_docpro', e.num_docume as 'num_docpro', e.nom_apell1 as 'nom_ap1pro',
                             e.nom_apell2 as 'nom_ap2pro', e.nom_person as 'nom_nompro', e.num_telmov as 'num_mo1pro',
                             e.num_telefo as 'num_telpro', e.dir_domici as 'dir_dompro', e.dir_emailx as 'dir_emapro',
                             e.ciu_expdoc as 'ciu_exppro', e.cod_ciudad as 'ciu_respro',
                             a.cod_vehicu, 
                             f.num_placax, f.num_remolq, f.cod_marcax, f.cod_lineax, f.ano_modelo, f.cod_colorx, f.cod_carroc, f.num_config,
                             f.num_chasis, f.num_motorx, f.num_soatxx, f.fec_vigsoa, f.num_lictra,
                             f.cod_opegps, f.usr_gpsxxx, f.clv_gpsxxx, f.url_gpsxxx, f.idx_gpsxxx, f.obs_opegps, f.fre_opegps,
                             f.ind_precom as 'pre_comveh', f.val_compar as 'val_comveh', f.ind_preres as 'pre_resveh', f.val_resolu as 'val_resveh'
              FROM ".BASE_DATOS.".tab_relaci_estseg a
        INNER JOIN ".BASE_DATOS.".tab_solici_estseg b
            ON a.cod_solici = b.cod_solici
        LEFT JOIN ".BASE_DATOS.".tab_estudi_person c
            ON a.cod_conduc = c.cod_segper
        LEFT JOIN ".BASE_DATOS.".tab_estudi_person d
            ON a.cod_poseed = d.cod_segper
        LEFT JOIN ".BASE_DATOS.".tab_estudi_person e
            ON a.cod_propie = e.cod_segper
        LEFT JOIN ".BASE_DATOS.".tab_estudi_vehicu f
            ON a.cod_vehicu = f.cod_segveh
        INNER JOIN ".BASE_DATOS.".tab_genera_tipdoc g
            ON c.cod_tipdoc = g.cod_tipdoc
            WHERE a.cod_estseg = '".$cod_estseg."'; ";
            $query = new Consulta($sql, $this->conexion);
            $resultado = $query -> ret_matrix('a')[0];
            return $resultado;
        }

        private function darCiudadInput($cod_ciudad){
          $sql = "SELECT a.cod_ciudad, a.nom_ciudad 
                  FROM ".BASE_DATOS.".tab_genera_ciudad a
                  WHERE a.cod_ciudad = '".$cod_ciudad."';";
          $query = new Consulta($sql, $this->conexion);
          $resultado = $query -> ret_matrix('a')[0];
          if(count($resultado)>0){
            return $resultado['cod_ciudad'].' - '.$resultado['nom_ciudad'];
          }else{
            return '';
          } 
        }

        private function mostrarFormularioEstSeguridad(){
          self::styles();
          $info = $this->getInfoSolicitud($_REQUEST['cod_estseg']);

          //Indicador de fase
          $disabledTabs1='';
          $disabledTabs2='';
          $activetab1='';
          $activetab2='';
          $ind_fase = $info['ind_fasexx'];
          if($ind_fase==1){
            $disabledTabs2 = 'disabledTab';
            $activetab1='active';
          }else{
            $disabledTabs1 = 'disabledTab';
            $activetab2='active';
          }

          $che_pospro = '';
          if($info['cod_poseed']==$info['cod_propie']){
            $che_pospro = 'checked';
          }

          //Indicador si el vehiculo presenta comparendos
          $ind_precomvehSi='';
          $ind_precomvehNo='';
          if($info['pre_comveh']==1){
            $ind_precomvehSi='checked';
          }else{
            $ind_precomvehNo='checked';
          }

          //Indicador si el vehiculo presenta resoluciones
          $pre_resvehSi='';
          $pre_resvehNo='';
          if($info['pre_resveh']==1){
            $pre_resvehSi='checked';
          }else{
            $pre_resvehNo='checked';
          }

          //Indicadores para busqueda de documentos
          $nom_tabper = 'tab_estudi_person';
          $con_whepos = '';
          $con_whepro = '';
          if($info['cod_poseed'] != '' || $info['cod_poseed'] != NULL){
            $con_whepos = ' cod_segper = '.$info['cod_poseed'];
          }
          
          if($info['cod_propie'] != '' || $info['cod_propie'] != NULL){
            $con_whepro = ' cod_segper = '.$info['cod_propie'];
          }

          $nom_tabveh = 'tab_estudi_vehicu';
          $con_wheveh = ' cod_segveh = '.$info['cod_vehicu'];

          echo '<table style="width: 100%;" id="dashBoardTableTrans">
          <tr>
              <td>
                  <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                      <div class="container" style="min-width: 98%;">
                        '.$this->modalPreGuardadoF1().'
                        '.$this->modalPreGuardadoF2().'
                        '.$this-> modalInfoSolicitudPreview($info).'
                        '.$this->modalRegistrarOperadoresGps().'
                        <div class="card style="margin:15px;">
                          <div class="card-header color-heading text-center">
                            Diligenciar estudio de seguridad
                          </div>
                        <div class="card-body">
                          <form id="dataSolicitud" action="" method="post" enctype="multipart/form-data" onsubmit="validateEstudioSoliciFinal()">

                          <div class="row">
                            <div class="col-6">
                              <div class="form-group">
                                <label for="nom_soliciID" class="labelinput">Nombre del solicitante:</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Nombre del solicitante" id="nom_soliciID" name="nom_solici" disabled value="'.$info['nom_solici'].'">
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="form-group">
                                <label for="nom_soliciID" class="labelinput">Correo electrónico:</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Correo electronico" id="cor_soliciID" name="cor_solici" disabled value="'.$info['cor_solici'].'">
                              </div>
                            </div>
                          </div>
              
                          <div class="row">
                            <div class="col-6">
                              <div class="form-group">
                                <label for="nom_soliciID" class="labelinput">Número de teléfono:</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Número de teléfono" id="tel_soliciID" name="tel_solici" disabled value="'.$info['tel_solici'].'">
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="form-group">
                                <label for="nom_soliciID" class="labelinput">Número de celular:</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Número de celular" id="cel_soliciID" name="cel_solici" disabled value="'.$info['cel_solici'].'">
                              </div>
                            </div>
                          </div>

                          <input type="hidden" value="'.$info['cod_estseg'].'" name="cod_estseg">
            

                        <div id="collapsethree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                          <ul class="nav nav-pills mb-3 bk-sure" id="pills-tab" role="tablist">
                            <li class="nav-item m-2 '.$disabledTabs1.'">
                              <a class="btn btn-success btn-sm btn-success '.$activetab1.'"  id="pills-documentos-tab" data-toggle="pill" href="#pills-documentos" role="tab" aria-controls="pills-documentos" aria-selected="true">Documentos</a>
                            </li>
                            <li class="nav-item m-2 '.$disabledTabs2.' '.$activetab2.'">
                                <a class="btn btn-success btn-sm"  id="pills-conductor-tab" data-toggle="pill" href="#pills-conductor" role="tab" aria-controls="pills-conductor" aria-selected="true">Conductor</a>
                            </li>
                            <li class="nav-item m-2 '.$disabledTabs2.'">
                                <a class="btn btn-success btn-sm"  id="pills-poseedor-tab" data-toggle="pill" href="#pills-poseedor" role="tab" aria-controls="pills-poseedor" aria-selected="false">Poseedor / Tenedor</a>
                            </li>
                            <li class="nav-item m-2 '.$disabledTabs2.'">
                                <a class="btn btn-success btn-sm"  id="pills-propietario-tab" data-toggle="pill" href="#pills-propietario" role="tab" aria-controls="pills-propietario" aria-selected="false">Propietario</a>
                            </li>
                            <li class="nav-item m-2 '.$disabledTabs2.'">
                                <a class="btn btn-success btn-sm"  id="pills-vehiculo-tab" data-toggle="pill" href="#pills-vehiculo" role="tab" aria-controls="pills-vehiculo" aria-selected="false">Vehículo</a>
                            </li>
                            <li class="nav-item m-2">
                                <a class="btn btn-success btn-sm" id="pills-bitacora-tab" data-toggle="pill" href="#pills-bitacora" role="tab" aria-controls="pills-bitacora" aria-selected="false">Bitacora</a>
                            </li>
                          </ul>
                        </div>

                        
                        <div class="tab-content border" id="pills-tabContent">
                          <div class="tab-pane fade show '.$activetab1.' p-3" id="pills-documentos" role="tabpanel" aria-labelledby="pills-documentos-tab">
                            '.$this->viewPillsDocumentos($info).'
                          </div>

                          <div class="tab-pane fade show p-3 '.$activetab2.'" id="pills-conductor" role="tabpanel" aria-labelledby="pills-conductor-tab">
                            '.$this->viewPillsConductor($info).' 
                          </div>

                          <div class="tab-pane fade show p-3" id="pills-poseedor" role="tabpanel" aria-labelledby="pills-poseedor-tab">
                                <div class="container border">
                                <div class="row">
                                  <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                      Datos Básicos del Poseedor/Tenedor
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-3 form-group">
                                      <input type="hidden" name="cod_poseed" value="'.$info['cod_poseed'].'">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Tipo de documento:
                                      </label>
                                      <select class="form-control form-control-sm" id="tip_docposID" name="tip_docpos" sol="true">
                                      '.$this->darTipoDocumento().'
                                      </select>
                                  </div>
                            
                                  <div class="col-2 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        No Documento:
                                      </label>
                                      <input class="form-control form-control-sm" type="text" id="num_docposID" name="num_docpos" value="'.$info['num_docpos'].'" sol="true">
                                  </div>
                            
                            
                                  <div class="col-3 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Ciudad de expedición::
                                      </label>
                                      <input class="form-control form-control-sm" type="text" sol placeholder="De" id="lug_exppos" name="lug_exppos" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_exppos']).'" sol="true">
                                      <div id="lug_exppos-suggestions" class="suggestions" style="top: 50px !important;"></div>
                                  </div>
                            
                            
                                  <div class="col-4 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Nombres
                                      </label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Nombres" id="nom_nomposID" name="nom_nompos" value="'.$info['nom_nompos'].'" sol="true">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-3 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Primer apellido:
                                      </label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Primer apellido" id="nom_ap1posID" name="nom_ap1pos" value="'.$info['nom_ap1pos'].'" sol="true">
                                  </div>
                            
                                  <div class="col-3 form-group">
                                      <label for="nom_soliciID" class="labelinput">Segundo apellido:</label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_ap2posID" name="nom_ap2pos" value="'.$info['nom_ap2pos'].'">
                                  </div>
                            
                                  <div class="col-3 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Numero de celular:
                                      </label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Numero de celular" id="num_mo1posID" name="num_mo1pos" value="'.$info['num_mo1pos'].'" sol="true">
                                  </div>
                            
                                  <div class="col-3 form-group">
                                      <div class="obl">*</div>
                                      <label for="nom_soliciID" class="labelinput">Teléfono</label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Teléfono" id="num_telposID" name="num_telpos" value="'.$info['num_telpos'].'">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-4 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Dirección:
                                      </label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Dirección" id="dir_domposID" name="dir_dompos" value="'.$info['dir_dompos'].'" sol="true">
                                  </div>
                                  <div class="col-4 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Ciudad:
                                      </label>
                                      <input class="form-control form-control-sm" type="text" id="ciu_poseed" name="ciu_poseed" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_respos']).'" sol="true">
                                      <div id="ciu_poseed-suggestions" class="suggestions" style="top: 50px !important;"></div>
                                  </div>
                                  <div class="col-4 form-group">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Email:
                                      </label>
                                      <input class="form-control form-control-sm" type="text" placeholder="Email" id="dir_emaposID" name="dir_emapos" value="'.$info['dir_emapos'].'" sol="true">
                                  </div>
                                </div>
                                <div class="row mt-2 mb-3">
                                  <div class="col-3">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="checkbox" id="check_pospropiet" value="1" name="check_pospropiet" '.$che_pospro.'>
                                      <label class="form-check-label" for="check_pospropiet">Es Propietario</label>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            
                            
                            <div class="container border">
                                <div class="row">
                                  <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                      Estudio de seguridad del Poseedor/Tenedor
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Consulta RIT
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_conrit', $nom_tabper, $con_whepos,4).'" id="fil_ritpos" name="fil_ritpos" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_conrit', $nom_tabper, $con_whepos,1).'
                                  </div>
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Consulta SIMIT:
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_simitx', $nom_tabper, $con_whepos,4).'" id="fil_simpos" name="fil_simpos" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_simitx', $nom_tabper, $con_whepos,1).'
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_ritpos">Comentario:</label>
                                      <textarea class="form-control" id="obs_ritpos" rows="2" name="obs_ritpos">'.$this->buscaDocumento('obs_conrit', $nom_tabper, $con_whepos,2).'</textarea>
                                  </div>
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_simpos">Comentario:</label>
                                      <textarea class="form-control" id="obs_simpos" rows="2" name="obs_simpos">'.$this->buscaDocumento('obs_simitx', $nom_tabper, $con_whepos,2).'</textarea>
                                  </div>
                                </div>
                                <div class="row mt-3">
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Procuraduria
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_procur', $nom_tabper, $con_whepos,4).'" id="fil_propos" name="fil_propos" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_procur', $nom_tabper, $con_whepos,1).'
                                  </div>
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Runt:
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_runtxx', $nom_tabper, $con_whepos,4).'" id="fil_runpos" name="fil_runpos" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_runtxx', $nom_tabper, $con_whepos,1).'
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_propos">Comentario:</label>
                                      <textarea class="form-control" id="obs_propos" rows="2" name="obs_propos">'.$this->buscaDocumento('obs_procur', $nom_tabper, $con_whepos,2).'</textarea>
                                  </div>
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_runpos">Comentario:</label>
                                      <textarea class="form-control" id="obs_runpos" rows="2" name="obs_runpos">'.$this->buscaDocumento('obs_runtxx', $nom_tabper, $con_whepos,2).'</textarea>
                                  </div>
                                </div>
                                <div class="row mt-3">
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        A. Juiciales
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_ajudic', $nom_tabper, $con_whepos,4).'" id="fil_antpos" name="fil_antpos" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_ajudic', $nom_tabper, $con_whepos,1).'
                                  </div>
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_antpos">Comentario:</label>
                                      <textarea class="form-control" id="obs_antpos" rows="2" name="obs_antpos">'.$this->buscaDocumento('obs_ajudic', $nom_tabper, $con_whepos,2).'</textarea>
                                  </div>
                                </div>
                            </div>
                            <div class="row mt-3 mb-3">
                              <div class="col-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="changePestana(0)"><i class="fa fa-caret-left" aria-hidden="true"></i> Anterior</i></button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="changePestana(2)">Siguiente <i class="fa fa-caret-right" aria-hidden="true"></i></button>
                                <button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#modalPreGuardadoF2">Pre Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                              </div>
                            </div>
                          </div>

                          <div class="tab-pane fade show p-3" id="pills-propietario" role="tabpanel" aria-labelledby="pills-propietario-tab">
                            <div class="container border">
                            <div class="row">
                              <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                  Datos Básicos del propietario
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-3 form-group">
                                  <input type="hidden" name="cod_propie" value="'.$info['cod_propie'].'">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Tipo de documento:
                                  </label>
                                  <select class="form-control form-control-sm" id="tip_docproID" name="tip_docpro" sol="true">
                                  '.$this->darTipoDocumento().'
                                  </select>
                              </div>
                        
                              <div class="col-2 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    No Documento:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" id="num_docproID" name="num_docpro" value="'.$info['num_docpro'].'" sol="true">
                              </div>
                        
                        
                              <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Ciudad de expedición:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" placeholder="De" id="lug_exppro" name="lug_exppro" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_exppro']).'" sol="true">
                                  <div id="lug_exppro-suggestions" class="suggestions" style="top: 50px !important;"></div>
                              </div>
                        
                        
                              <div class="col-4 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Nombres
                                  </label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Nombres" id="nom_nomproID" name="nom_nompro" value="'.$info['nom_nompro'].'" sol="true">
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Primer apellido:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Primer apellido" id="nom_ap1proID" name="nom_ap1pro" value="'.$info['nom_ap1pro'].'" sol="true">
                              </div>
                        
                              <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">Segundo apellido:</label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_ap2proID" name="nom_ap2pro" value="'.$info['nom_ap2pro'].'">
                              </div>
                        
                              <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Numero de celular:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Numero de celular" id="num_mo1proID" name="num_mo1pro" value="'.$info['num_mo1pro'].'" sol="true">
                              </div>
                        
                              <div class="col-3 form-group">
                                  <div class="obl">*</div>
                                  <label for="nom_soliciID" class="labelinput">Teléfono</label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Teléfono" id="num_telproID" name="num_telpro" value="'.$info['num_telpro'].'">
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-4 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Dirección:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Dirección" id="dir_domproID" name="dir_dompro" value="'.$info['dir_dompro'].'" sol="true">
                              </div>
                              <div class="col-4 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Ciudad:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" id="ciu_propie" name="ciu_propie" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_respro']).'" sol="true">
                                  <div id="ciu_propie-suggestions" class="suggestions" style="top: 50px !important;"></div>
                              </div>
                              <div class="col-4 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Email:
                                  </label>
                                  <input class="form-control form-control-sm" type="text" placeholder="Email" id="dir_emaproID" name="dir_emapro" value="'.$info['dir_emapro'].'" sol="true">
                              </div>
                            </div>
                          </div>
                            <div class="container border">
                                <div class="row">
                                  <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                      Estudio de seguridad del propietario
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Consulta RIT
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_conrit', $nom_tabper, $con_whepro,4).'" id="fil_ritpro" name="fil_ritpro" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_conrit', $nom_tabper, $con_whepro,1).'
                                  </div>
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Consulta SIMIT:
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_simitx', $nom_tabper, $con_whepro,4).'" id="fil_simpro" name="fil_simpro" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_simitx', $nom_tabper, $con_whepro,1).'
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_ritpro">Comentario:</label>
                                      <textarea class="form-control" id="obs_ritpro" rows="2" name="obs_ritpro">'.$this->buscaDocumento('obs_conrit', $nom_tabper, $con_whepro,2).'</textarea>
                                  </div>
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_simpro">Comentario:</label>
                                      <textarea class="form-control" id="obs_simpro" rows="2" name="obs_simpro">'.$this->buscaDocumento('obs_simitx', $nom_tabper, $con_whepro,2).'</textarea>
                                  </div>
                                </div>
                                <div class="row mt-3">
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Procuraduria
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_procur', $nom_tabper, $con_whepro,4).'" id="fil_propro" name="fil_propro" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_procur', $nom_tabper, $con_whepro,1).'
                                  </div>
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        Runt:
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_runtxx', $nom_tabper, $con_whepro,4).'" id="fil_runpro" name="fil_runpro" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_runtxx', $nom_tabper, $con_whepro,1).'
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_propro">Comentario:</label>
                                      <textarea class="form-control" id="obs_propro" rows="2" name="obs_propro">'.$this->buscaDocumento('obs_procur', $nom_tabper, $con_whepro,2).'</textarea>
                                  </div>
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_runpro">Comentario:</label>
                                      <textarea class="form-control" id="obs_runpro" rows="2" name="obs_runpro">'.$this->buscaDocumento('obs_runtxx', $nom_tabper, $con_whepro,2).'</textarea>
                                  </div>
                                </div>
                                <div class="row mt-3">
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                      <label for="nom_soliciID" class="labelinput">
                                        <div class="obl">*</div>
                                        A. Juiciales
                                      </label>
                                      <input type="file" class="'.$this->buscaDocumento('fil_ajudic', $nom_tabper, $con_whepro,4).'" id="fil_antpro" name="fil_antpro" accept="image/png,image/jpeg, image/jpg">
                                      '.$this->buscaDocumento('fil_ajudic', $nom_tabper, $con_whepro,1).'
                                  </div>
                                  <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-6 form-group">
                                      <label class="labelinput" for="obs_antpro">Comentario:</label>
                                      <textarea class="form-control" id="obs_antpro" rows="2" name="obs_antpro">'.$this->buscaDocumento('obs_ajudic', $nom_tabper, $con_whepro,2).'</textarea>
                                  </div>
                                </div>
                            </div>
                            <div class="row mt-3 mb-3">
                              <div class="col-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="changePestana(4)"><i class="fa fa-caret-left" aria-hidden="true"></i> Anterior</i></button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="changePestana(3)">Siguiente <i class="fa fa-caret-right" aria-hidden="true"></i></button>
                                <button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#modalPreGuardadoF2">Pre Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                              </div>
                            </div>
                          </div>

                          <div class="tab-pane fade show p-3" id="pills-vehiculo" role="tabpanel" aria-labelledby="pills-vehiculo-tab">
                             <div class="container border">
                               <div class="row">
                                <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                    Datos Básicos del vehículo
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-3 form-group">
                                  <input type="hidden" name="cod_vehicu" value="'.$info['cod_vehicu'].'">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                      Placa:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="num_placaxID" name="num_placax" value="'.$info['num_placax'].'">
                                </div>
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                      No remolque:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="num_remolqID" name="num_remolq" value="'.$info['num_remolq'].'">
                                </div>
                                <div class="col-3 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                       Tipo de vehículo:
                                    </label>
                                    <select class="form-control form-control-sm req" id="num_configID" name="num_config">
                                    '.$this->getTipoVehiculo($info['num_config']).'
                                    </select>
                                </div>
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Tipo de Carrocería:
                                  </label>
                                  <select class="form-control form-control-sm req" id="cod_carrocID" name="cod_carroc">
                                  '.$this->getTipoCarroc($info['cod_carroc']).'
                                  </select>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Modelo:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="ano_modeloID" name="ano_modelo" value="'.$info['ano_modelo'].'">
                                </div>
                                <div class="col-3 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                       Color:
                                    </label>
                                    <select class="form-control form-control-sm req" id="cod_colorxID" name="cod_colorx">
                                    '.$this->getColorVehicu($info['cod_colorx']).'
                                    </select>
                                </div>
                                <div class="col-3 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                       Marca:
                                    </label>
                                    <select class="form-control form-control-sm req" id="cod_marcaxID" name="cod_marcax" onchange="traeLineas(this)">
                                    '.$this->getMarcaVehicu($info['cod_marcax']).'
                                    </select>
                                </div>
                                <div class="col-3 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                       Linea:
                                    </label>
                                    <select class="form-control form-control-sm req" id="cod_lineaxID" name="cod_lineax">
                                    '.$this->getLineaVehicu($info['cod_lineax'],$info['cod_marcax']).'
                                    </select>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Número de chasis:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="num_chasisID" name="num_chasis" value="'.$info['num_chasis'].'">
                                </div>
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Número del motor:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="num_motorxID" name="num_motorx" value="'.$info['num_motorx'].'">
                                </div>
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Número del soat:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="num_soatxxID" name="num_soatxx" value="'.$info['num_soatxx'].'">
                                </div>
                                <div class="col-3 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                       Fecha de vigencia del SOAT:
                                    </label>
                                    <input class="form-control form-control-sm req" type="date" id="fec_vigsoaID" name="fec_vigsoa" value="'.$info['fec_vigsoa'].'">
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-3 form-group">
                                  <label for="nom_soliciID" class="labelinput">
                                    <div class="obl">*</div>
                                    Número de licencia de transito:
                                  </label>
                                  <input class="form-control form-control-sm req" type="text" id="num_lictraID" name="num_lictra" value="'.$info['num_lictra'].'">
                                </div>
                              </div>

                            </div>

                            <div class="container border">
                              <div class="row">
                                <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                  Información del operador GPS
                                </div>
                              </div>
                              <div class="row">
                                  <div class="col-4 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>Operador GPS:
                                      <button type="button" class="btn btn-info btn-sm"  data-toggle="modal" data-target="#modalregOpeGps"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                    </label>
                                    <select class="form-control form-control-sm req" id="cod_opegpsID" name="cod_opegps">
                                    '.$this->getCodOpeGPS($info['cod_opegps']).'
                                    </select>
                                  </div>
                                  <div class="col-4 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                        Usuario:
                                    </label>
                                    <input class="form-control form-control-sm req" type="text" id="usr_gpsxxxID" name="usr_gpsxxx" value="'.$info['usr_gpsxxx'].'">
                                  </div>
                                  <div class="col-4 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                        Contraseña:
                                    </label>
                                    <input class="form-control form-control-sm req" type="text" id="clv_gpsxxxID" name="clv_gpsxxx" value="'.$info['clv_gpsxxx'].'">
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-4 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      ID:
                                    </label>
                                    <input class="form-control form-control-sm" type="text" id="idx_gpsxxxID" name="idx_gpsxxx" value="'.$info['idx_gpsxxx'].'">
                                  </div>
                                  <div class="col-6 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                        Observación:
                                    </label>
                                    <input class="form-control form-control-sm req" type="text" id="obs_opegpsID" name="obs_opegps" value="'.$info['obs_opegps'].'">
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-6 form-group">
                                    <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                      Frecuencia:
                                    </label>
                                    <input class="form-control form-control-sm req" type="text" id="fre_opegpsID" name="fre_opegps" value="'.$info['fre_opegps'].'">
                                  </div>
                              </div>

                              <div class="row mt-3">
                                <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                  <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                      Consulta Operador GPS
                                  </label>
                                  <input type="file" class="'.$this->buscaDocumento('fil_congps', $nom_tabveh, $con_wheveh,4).'" id="fil_congps" name="fil_congps" accept="image/png,image/jpeg, image/jpg">
                                  '.$this->buscaDocumento('fil_congps', $nom_tabveh, $con_wheveh,1).'
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-6 form-group">
                                  <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                                  <textarea class="form-control" id="obs_congps" rows="2" name="obs_congps">'.$this->buscaDocumento('obs_congps', $nom_tabveh, $con_wheveh,2).'</textarea>
                                </div>
                              </div>
                            </div>
                            <div class="container border">
                              <div class="row">
                                <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                  Estudio de seguridad del vehículo
                                </div>
                              </div>
                              <div class="row mt-3">
                                <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                  <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                      Consulta RIT
                                  </label>
                                  <input type="file" class="'.$this->buscaDocumento('fil_conrit', $nom_tabveh, $con_wheveh,4).'" id="fil_conrit" name="fil_conrit" accept="image/png,image/jpeg, image/jpg">
                                  '.$this->buscaDocumento('fil_conrit', $nom_tabveh, $con_wheveh,1).'
                                </div>
                                <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                  <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                      Runt:
                                  </label>
                                  <input type="file" class="'.$this->buscaDocumento('fil_runtxx', $nom_tabveh, $con_wheveh,4).'" id="fil_runtxx" name="fil_runtxx" accept="image/png,image/jpeg, image/jpg">
                                  '.$this->buscaDocumento('fil_runtxx', $nom_tabveh, $con_wheveh,1).'
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-6 form-group">
                                  <label class="labelinput" for="obs_conrit">Comentario:</label>
                                  <textarea class="form-control" id="obs_conrit" rows="2" name="obs_conrit">'.$this->buscaDocumento('obs_conrit', $nom_tabveh, $con_wheveh,2).'</textarea>
                                </div>
                                <div class="col-6 form-group">
                                  <label class="labelinput" for="obs_runtxx">Comentario:</label>
                                  <textarea class="form-control" id="obs_runtxx" rows="2" name="obs_runtxx">'.$this->buscaDocumento('obs_runtxx', $nom_tabveh, $con_wheveh,2).'</textarea>
                                </div>
                              </div>
                              <div class="row mt-3">
                                <div class="col-6 form-group" style="margin-bottom: 0 !important;">
                                  <label for="nom_soliciID" class="labelinput">
                                      <div class="obl">*</div>
                                      Comparendos
                                  </label>
                                  <input type="file" class="'.$this->buscaDocumento('fil_compar', $nom_tabveh, $con_wheveh,4).'" id="fil_compar" name="fil_compar" accept="image/png,image/jpeg, image/jpg">
                                  '.$this->buscaDocumento('fil_compar', $nom_tabveh, $con_wheveh,1).'
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-6 form-group">
                                  <label class="labelinput" for="obs_compar">Comentario:</label>
                                  <textarea class="form-control" id="obs_compar" rows="2" name="obs_compar">'.$this->buscaDocumento('obs_compar', $nom_tabveh, $con_wheveh,2).'</textarea>
                                </div>
                                <div class="col-6 form-group">
                                  <div class="row">
                                    <div class="col-8 text-right">
                                      <label class="form-check-label labelinput" for="exampleCheck1">¿El vehículo presenta comparendos?</label>
                                    </div>
                                    <div class="col-4">
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pre_comveh" id="pre_comveh1" value="1" '.$ind_precomvehSi.' onchange="cambioIndicadores(this)">
                                        <label class="form-check-label" for="inlineRadio1">Si</label>
                                      </div>
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pre_comveh" id="pre_comveh2" value="0" '.$ind_precomvehNo.' onchange="cambioIndicadores(this)">
                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="row mt-3 mb-3" id="com_pre_comveh">
                                    <div class="col-8 text-right">
                                      <label class="form-check-label labelinput" for="exampleCheck1">Valor</label>
                                    </div>
                                    <div class="col-4">
                                      <input class="form-control form-control-sm" type="text" id="val_comvehID" name="val_comveh" value="'.$info['val_comveh'].'">
                                    </div>
                                  </div>

                                  <div class="row">
                                    <div class="col-8 text-right">
                                      <label class="form-check-label labelinput" for="exampleCheck1">¿El vehículo presenta resoluciones?</label>
                                    </div>
                                    <div class="col-4">
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ind_preres" id="ind_preres" value="1" '.$pre_resvehSi.' onchange="cambioIndicadores(this)">
                                        <label class="form-check-label" for="inlineRadio1">Si</label>
                                      </div>
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ind_preres" id="ind_preres" value="0" '.$pre_resvehNo.' onchange="cambioIndicadores(this)">
                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="row mt-3 mb-3" id="com_ind_preres">
                                    <div class="col-8 text-right">
                                      <label class="form-check-label labelinput" for="exampleCheck1">Valor</label>
                                    </div>
                                    <div class="col-4">
                                      <input class="form-control form-control-sm" type="text" id="val_resvehID" name="val_resveh" value="'.$info['val_resveh'].'">
                                    </div>
                                  </div>

                                </div>


                              </div>
                            </div>   
                            <div class="container border">
                              <div class="row">
                                <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                                  Estudio Aprobado
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-3">
                                  <label class="form-check-label labelinput" for="exampleCheck1">Estudio de seguridad:</label>
                                </div>
                                <div class="col-9">
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input req" type="radio" name="ind_estudi" id="ind_estudi1" value="A">
                                    <label class="form-check-label" for="inlineRadio1">Recomendado</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input req" type="radio" name="ind_estudi" id="ind_estudi2" value="R">
                                    <label class="form-check-label" for="inlineRadio2">No Recomendado</label>
                                  </div>
                                </div>
                              </div>
                              <div class="row mt-3 mb-3">
                                <div class="col-12">
                                  <label class="labelinput" for="obs_estudiID">Comentario:</label>
                                  <textarea class="form-control req" id="obs_estudi" name="obs_estudi" rows="2"></textarea>
                                </div>
                              </div>
                            </div>
                            <div class="row mt-3 mb-3">
                              <div class="col-12 text-right">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="changePestana(5)"><i class="fa fa-caret-left" aria-hidden="true"></i> Anterior</i></button>
                                <button type="button" class="btn btn-success btn-sm" onclick="validateEstudioSoliciFinal()">Guardar y Terminar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                              </div>
                            </div>
                          </div>

                          <div class="tab-pane fade show p-3" id="pills-bitacora" role="tabpanel" aria-labelledby="pills-bitacora-tab">
                          '.$this->viewPillsBitacora($info).'
                          </div>

                        </div>
                      </div>
                      </form>
                      <form action="myScript_2.php" id="form_2"></form>
              </td>
          </tr>';
          self::scripts();
          
        }

        private function generaReferenceFyP($cod_person,$tip_refere,$cod_identi){
          $llave = $cod_person.'_'.$tip_refere.'_'.$cod_identi;
          $referen = $this->getReferenciasFyP($cod_person, $tip_refere,$cod_identi);
          $html.='
          <div class="row" style="overflow: auto;">
             <div class="col-md-12 col-sm-12">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Nombre</th>
                      <th scope="col">Parentesco</th>
                      <th scope="col">Direccion</th>
                      <th scope="col">Teléfono</th>
                      <th scope="col">Observación</th>
                      <th scope="col">Opciones</th>
                    </tr>
                  </thead>
                  <tbody id="table-referen_'.$cod_person.'_'.$tip_refere.'_'.$cod_identi.'">
                    '.$referen.'
                  </tbody>
                </table>
              </div>
            </div>
          <div id="InsReferenceFyP_'.$llave.'">
          <div class="row">
          <div class="col-md-3 col-sm-12 mb-3 ">
            <label for="nom_soliciID" class="labelinput">Nombre completo:</label>
            <input class="form-control form-control-sm Req_ReferenceFyP" type="text"id="nom_refereID_'.$llave.'" name="nom_refere_'.$llave.'">
          </div>
          <div class="col-md-3 col-sm-12 mb-3 ">
            <label for="nom_soliciID" class="labelinput">Parentesco:</label>
            <select class="form-control form-control-sm Req_ReferenceFyP" id="cod_parentID_'.$llave.'" name="cod_parent_'.$llave.'" onchange="llenaParentesco(this,`'.$cod_person.'`,`'.$tip_refere.'`,`'.$cod_identi.'`)">
            '.$this->getParentesco().'
            </select>
          </div>
          <div class="col-md-3 col-sm-12 mb-3 ">
            <label for="nom_soliciID" class="labelinput">Direccion:</label>
            <input class="form-control form-control-sm Req_ReferenceFyP" type="text" id="dir_domiciID_'.$llave.'" name="dir_domici_'.$llave.'">
          </div>
          <div class="col-md-3 col-sm-12 mb-3 ">
            <label for="nom_soliciID" class="labelinput">Telefono:</label>
            <input class="form-control form-control-sm Req_ReferenceFyP" type="text" id="num_telefoID_'.$llave.'" name="num_telefo_'.$llave.'">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-7 col-sm-12 mb-3 ">
            <label class="labelinput" for="obs_refereID">Observacion:</label>
            <textarea class="form-control Req_ReferenceFyP" id="obs_refereID_'.$llave.'" name="obs_refere_'.$llave.'" rows="2"></textarea>
          </div>
        </div>

        <div id="div-cual-input_'.$llave.'" style="display:none">
          <div class="row mt-2">
            <div class="col-md-3 col-sm-12 pt-1 text-right align-self-center">
              ¿Cual?
            </div>
            <div class="col-md-3 col-sm-12">
              <input class="form-control form-control-sm" type="text" id="nom_parentID_'.$llave.'" name="nom_parent_'.$llave.'" value="Padre">
            </div>
          </div>
        </div>

        <div class="row mt-3 mb-3">
          <div class="col-md-12 col-sm-12 text-center">
            <input type="hidden" id="cod_person_'.$llave.'" value="'.$cod_person.'">
            <input type="hidden" id="cod_refere_'.$llave.'" value="'.$tip_refere.'">
            <input type="hidden" id="cod_identi_'.$llave.'" value="'.$cod_identi.'">
            <button type="button" class="btn btn-primary btn-sm" onclick="registFormDinamic(`'.$cod_person.'`,`'.$tip_refere.'`,`'.$cod_identi.'`)"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
          </div>
        </div>
      </div>
      </div>';
          return $html;
        }


        private function generaReferenceLaborales($cod_person,$tip_refere,$cod_identi){
          $llave = $cod_person.'_'.$tip_refere.'_'.$cod_identi;
          $referen = $this->getReferenciasLaborales($cod_person, $tip_refere,$cod_identi);
          $html.='
          <div class="row">
             <div class="col-md-12">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Transportadora</th>
                      <th scope="col">Teléfono</th>
                      <th scope="col">Información dada por</th>
                      <th scope="col">No Viajes</th>
                      <th scope="col">Opciones</th>
                    </tr>
                  </thead>
                  <tbody id="table-referen_'.$cod_person.'_'.$tip_refere.'_'.$cod_identi.'">
                    '.$referen.'
                  </tbody>
                </table>
              </div>
            </div>
          <div id="InsReferenceLaboral_'.$llave.'">
          <div class="row">
          <div class="col-md-3 col-sm-12 mb-3">
            <label for="nom_soliciID" class="labelinput">Transportadora:</label>
            <input class="form-control form-control-sm Req_ReferenceLaboral" type="text" id="nom_transpID_'.$llave.'" name="nom_transp_'.$llave.'">
          </div>
          <div class="col-md-3 col-sm-12 mb-3">
            <label for="nom_soliciID" class="labelinput">Telefono:</label>
            <input class="form-control form-control-sm Req_ReferenceLaboral" type="text" id="num_telefoID_'.$llave.'" name="num_telefo_'.$llave.'">
          </div>
          <div class="col-md-3 col-sm-12 mb-3">
            <label for="nom_soliciID" class="labelinput">Información dada por:</label>
            <input class="form-control form-control-sm Req_ReferenceLaboral" type="text" id="inf_suminiID_'.$llave.'" name="inf_sumini_'.$llave.'">
          </div>
          <div class="col-md-3 col-sm-12 mb-3">
            <label for="nom_soliciID" class="labelinput">No viajes:</label>
            <input class="form-control form-control-sm Req_ReferenceLaboral" type="text" id="num_viajesID_'.$llave.'" name="num_viajes_'.$llave.'">
          </div>
        </div>

        <div class="row mt-3 mb-3">
          <div class="col-md-12 col-sm-12 text-center">
            <input type="hidden" id="cod_person_'.$llave.'" value="'.$cod_person.'">
            <input type="hidden" id="cod_refere_'.$llave.'" value="'.$tip_refere.'">
            <input type="hidden" id="cod_identi_'.$llave.'" value="'.$cod_identi.'">
            <button type="button" onclick="registFormDinamic(`'.$cod_person.'`,`'.$tip_refere.'`,`'.$cod_identi.'`)" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
          </div>
        </div>
      </div>
      </div>';
          return $html;
        }


        private function viewPillsDocumentos($info){
          //Nombre de variables
          $nom_tablax = 'tab_estudi_docume';
          $con_wherex = ' cod_estseg = '.$info['cod_estseg'];
          $html = '
            <div class="container border">
                <div class="row color-heading bk-sure p-2 mb-3">
                    <div class="col-1 text-center">
                      <button type="button" class="btn btn-info btn-sm"  data-toggle="modal" data-target="#modalInfoSolicitudPreview"><i class="fa fa-info-circle" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-11 text-center">
                      Documentos requeridos estudio de seguridad
                    </div>
                </div>

                <div class="row mb-2">
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Licencia de tránsito del vehículo
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_licveh', $nom_tablax, $con_wherex, 3).'" name="fil_licveh" id="fil_licveh" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_licveh', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_licveh" name="obs_licveh" rows="2" >'.$this->buscaDocumento('obs_licveh', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Tarjeta de Propiedad del Tráiler
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_tartra', $nom_tablax, $con_wherex, 3).'" name="fil_tartra" id="fil_tartra" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_tartra', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_tartra" name="obs_tartra" rows="2" >'.$this->buscaDocumento('obs_tartra', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="row mb-2">
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Tecno mecánica
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_tecmec', $nom_tablax, $con_wherex, 3).'" name="fil_tecmec" id="fil_tecmec" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_tecmec', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_tecmec" rows="2" name="obs_tecmec">'.$this->buscaDocumento('obs_tecmec', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                            SOAT
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_soatxx', $nom_tablax, $con_wherex, 3).'" name="fil_soatxx" id="fil_soatxx" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_soatxx', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_soatxx" rows="2" name="obs_soatxx">'.$this->buscaDocumento('obs_soatxx', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="row mb-2">
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Licencia de transito del Conductor
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_litcon', $nom_tablax, $con_wherex, 3).'" name="fil_litcon" id="fil_litcon" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_litcon', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_litcon" rows="2" name="obs_litcon">'.$this->buscaDocumento('obs_litcon', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                        <div class="obl">*</div>
                          Cedula de ciudadanía del propietario
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_cedpro', $nom_tablax, $con_wherex, 3).'" name="fil_cedpro" id="fil_cedpro" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_cedpro', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_cedpro" rows="2" name="obs_cedpro">'.$this->buscaDocumento('obs_cedpro', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
              </div>


              <div class="row mb-2">
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                            Cédula de ciudadanía del conductor
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_cedcon', $nom_tablax, $con_wherex, 3).'" name="fil_cedcon" id="fil_cedcon" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_cedcon', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_cedcon" rows="2" name="obs_cedcon">'.$this->buscaDocumento('obs_cedcon', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                        <div class="obl">*</div>
                          Licencia de conducción conductor
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_liccon', $nom_tablax, $con_wherex, 3).'" name="fil_liccon" id="fil_liccon" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_liccon', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_liccon" rows="2" name="obs_liccon">'.$this->buscaDocumento('obs_liccon', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="row mb-2">
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                        <div class="obl">*</div>
                          Copia de la planilla de Pago de Seguridad Social
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_plsegs', $nom_tablax, $con_wherex, 3).'" name="fil_plsegs" id="fil_plsegs" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_plsegs', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_plsegs" rows="2" name="obs_plsegs">'.$this->buscaDocumento('obs_plsegs', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Registro Fotográfico del vehículo.
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_regveh', $nom_tablax, $con_wherex, 3).'" name="fil_regveh" id="fil_regveh" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_regveh', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_regveh" rows="2" name="obs_regveh">'.$this->buscaDocumento('obs_regveh', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="row mb-2">
                  <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-2">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                            Póliza extracontractual
                        </label>
                        <input type="file" class="'.$this->buscaDocumento('fil_polext', $nom_tablax, $con_wherex, 3).'" name="fil_polext" id="fil_polext" accept="image/png,image/jpeg, image/jpg">
                        '.$this->buscaDocumento('fil_polext', $nom_tablax, $con_wherex, 1).'
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 col-sm-12 mb-3">
                        <label class="labelinput" for="exampleFormControlTextarea1">Comentario:</label>
                        <textarea class="form-control" id="obs_polext" rows="2" name="obs_polext">'.$this->buscaDocumento('obs_polext', $nom_tablax, $con_wherex, 2).'</textarea>
                      </div>
                    </div>
                  </div>
              </div>
              
                <div class="row mt-3 mb-3">
                    <div class="col-12 text-right">
                      <button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#modalPreGuardadoF1">Pre Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                      <button type="button" class="btn btn-success btn-sm"   onclick="validateFase1()">Guardar y Terminar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>';
          return $html;
        }


        private function viewPillsConductor($info){
          //Nombre de variables
          $nom_tablax = 'tab_estudi_person';
          $con_wherex = ' cod_segper = '.$info['cod_conduc'];
           //Indicadores de checkbox
          //Indicador si el conductor es poseedor/tenedor
          $che_conpos='';
          if($info['cod_conduc']==$info['cod_poseed']){
            $che_conpos = 'checked';
          }
          //Indicador si el conductor es propietario
          $che_conpro='';
          if($info['cod_conduc']==$info['cod_propie']){
            $che_conpro='checked';
          }

          //Indicadores de radio
          //Indicador si el conductor presenta comparendos
          $ind_precomSi='';
          $ind_precomNo='';
          if($info['pre_comcon']==1){
            $ind_precomSi='checked';
          }else{
            $ind_precomNo='checked';
          }

          //Indicador si el conductor presenta resoluciones
          $ind_preresSi='';
          $ind_preresNo='';
          if($info['pre_rescon']==1){
            $ind_preresSi='checked';
          }else{
            $ind_preresNo='checked';
          }

          $html='
          <div class="container border">
            <div class="row">
              <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                  Datos Básicos del Conductor
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-sm-12 form-group">
                <input type="hidden" name="cod_conduc" value="'.$info['cod_conduc'].'">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento:</label>
                <select class="form-control form-control-sm req" id="tip_docconID" name="tip_doccon">
                  '.$this->darTipoDocumento().'
                </select>
              </div>
              <div class="col-md-2 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>No Documento:</label>
                <input class="form-control form-control-sm req num" type="text" id="num_docconID" name="num_doccon" value="'.$info['num_doccon'].'">
              </div>
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad de expedición:</label>
                <input class="form-control form-control-sm req" type="text" placeholder="De" id="lug_expcon" name="lug_expcon" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_expcon']).'">
                <div id="lug_expcon-suggestions" class="suggestions" style="top: 50px !important;"></div>
              </div>
              <div class="col-md-4 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_nomconID" name="nom_nomcon" value="'.$info['nom_nomcon'].'">
              </div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido:</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_ap1conID" name="nom_ap1con" value="'.$info['nom_ap1con'].'">
              </div>
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput">Segundo apellido:</label>
                <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_ap2conID" name="nom_ap2con" value="'.$info['nom_ap2con'].'">
              </div>
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Numero de celular:</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Numero de celular" id="num_mo1conID" name="num_mo1con" value="'.$info['num_mo1con'].'">
              </div>
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Teléfono</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Teléfono" id="num_telconID" name="num_telcon" value="'.$info['num_telcon'].'">
              </div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Licencia de conducción No.:</label>
                <input class="form-control form-control-sm req num" type="text" id="num_licencID" name="num_licenc" value="'.$info['num_licenc'].'">
              </div>
              <div class="col-md-2 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Categoria:</label>
                <select class="form-control form-control-sm req" id="tip_catlicID" name="tip_catlic">
                  '.$this->darCategoriasLicencia($info['cod_catlic']).'
                </select>
              </div>
              <div class="col-md-3 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Fecha de vencimiento:</label>
                <input class="form-control form-control-sm req" type="date" id="fec_venlicID" name="fec_venlic" value="'.$info['fec_venlic'].'">
              </div>
              <div class="col-md-2 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Arl:</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Arl" id="nom_arlconID" name="nom_arlcon" value="'.$info['nom_arlcon'].'">
              </div>
              <div class="col-md-2 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Eps</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Eps" id="nom_epsconID" name="nom_epscon" value="'.$info['nom_epscon'].'">
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Dirección:</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Dirección" id="dir_domconID" name="dir_domcon" value="'.$info['dir_domcon'].'">
              </div>
              <div class="col-md-4 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad:</label>
                <input class="form-control form-control-sm req" type="text" id="ciu_conduc" name="ciu_conduc" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_rescon']).'">
                <div id="ciu_conduc-suggestions" class="suggestions" style="top: 50px !important;"></div>
              </div>
              <div class="col-md-4 col-sm-12 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email:</label>
                <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emaconID" name="dir_emacon" value="'.$info['dir_emacon'].'">
              </div>
            </div>

            <div class="row mt-2 mb-3">
              <div class="col-md-3 col-sm-12 mb-3">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="check_conposeed" value="1" name="check_conposeed" '.$che_conpos.'>
                  <label class="form-check-label" for="check_conposeed">Es Poseedor/Tenedor</label>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="check_conpropiet" value="1" name="check_conpropiet" '.$che_conpro.'>
                  <label class="form-check-label" for="check_conpropiet">Es Propietario</label>
                </div>
              </div>
            </div>
          </div>

            <div class="container border">
              <div class="row">
                <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                    Estudio de seguridad del conductor
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-2">
                      <label for="fil_ritcon" class="labelinput"><div class="obl">*</div>Consulta RIT</label>
                      <input type="file" class="'.$this->buscaDocumento('fil_conrit', $nom_tablax, $con_wherex,4).'" name="fil_ritcon" id="fil_ritcon" accept="image/png,image/jpeg, image/jpg">
                      '.$this->buscaDocumento('fil_conrit', $nom_tablax, $con_wherex,1).'
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-3">
                      <label class="labelinput" for="obs_ritcon">Comentario:</label>
                      <textarea class="form-control" id="obs_ritcon" rows="2" name="obs_ritcon">'.$this->buscaDocumento('obs_conrit', $nom_tablax, $con_wherex,2).'</textarea>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-2">
                      <label for="fil_simcon" class="labelinput"><div class="obl">*</div>Consulta SIMIT:</label>
                      <input type="file" class="'.$this->buscaDocumento('fil_simitx', $nom_tablax, $con_wherex,4).'" name="fil_simcon" id="fil_simcon">
                      '.$this->buscaDocumento('fil_simitx', $nom_tablax, $con_wherex,1).'
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-3">
                      <label class="labelinput" for="obs_simcon">Comentario:</label>
                      <textarea class="form-control" id="obs_simcon" rows="2" name="obs_simcon">'.$this->buscaDocumento('obs_simitx', $nom_tablax, $con_wherex,2).'</textarea>
                    </div>
                  </div>
                </div>
              </div>


              <div class="row mb-2">
                <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-2">
                      <label for="fil_procon" class="labelinput"><div class="obl">*</div>Procuraduria</label>
                      <input type="file" class="'.$this->buscaDocumento('fil_procur', $nom_tablax, $con_wherex,4).'" id="fil_procon" name="fil_procon" accept="image/png,image/jpeg, image/jpg">
                      '.$this->buscaDocumento('fil_procur', $nom_tablax, $con_wherex,1).'
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-3">
                      <label class="labelinput" for="obs_proconr">Comentario:</label>
                      <textarea class="form-control" id="obs_proconr" rows="2" name="obs_proconr">'.$this->buscaDocumento('obs_procur', $nom_tablax, $con_wherex,2).'</textarea>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-2">
                      <label for="fil_runcon" class="labelinput"><div class="obl">*</div>Runt:</label>
                      <input type="file" class="'.$this->buscaDocumento('fil_runtxx', $nom_tablax, $con_wherex,4).'" id="fil_runcon" name="fil_runcon">
                      '.$this->buscaDocumento('fil_runtxx', $nom_tablax, $con_wherex,1).'
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-sm-12 mb-3">
                      <label class="labelinput" for="obs_runcon">Comentario:</label>
                      <textarea class="form-control" id="obs_runcon" rows="2" name="obs_runcon">'.$this->buscaDocumento('obs_runtxx', $nom_tablax, $con_wherex,2).'</textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                  <label for="fil_antcon" class="labelinput"><div class="obl">*</div>A. Juiciales</label>
                  <input type="file" class="'.$this->buscaDocumento('fil_ajudic', $nom_tablax, $con_wherex,4).'" id="fil_antcon" name="fil_antcon" accept="image/png,image/jpeg, image/jpg">
                  '.$this->buscaDocumento('fil_ajudic', $nom_tablax, $con_wherex,1).'
                </div>
                <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12 form-group">
                  <label class="labelinput" for="obs_antcon">Comentario:</label>
                  <textarea class="form-control" id="obs_antcon" rows="2" name="obs_antcon">'.$this->buscaDocumento('obs_ajudic', $nom_tablax, $con_wherex,2).'</textarea>
                </div>
                <div class="col-md-6 col-sm-12 form-group">
                  <div class="row">
                    <div class="col-md-8 col-sm-6 text-right">
                      <label class="form-check-label labelinput" for="exampleCheck1">¿El conductor presenta comparendos?</label>
                    </div>
                    <div class="col-md-4 col-sm-6">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pregu1con" id="pregu1con" value="1" onchange="cambioIndicadores(this)" '.$ind_precomSi.'>
                        <label class="form-check-label" for="inlineRadio1">Si</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pregu1con" id="pregu1con" value="0" onchange="cambioIndicadores(this)" '.$ind_precomNo.'>
                        <label class="form-check-label" for="inlineRadio2">No</label>
                      </div>
                    </div>
                  </div>

                  <div class="row mt-3 mb-3" id="com_pregu1con">
                    <div class="col-md-8 col-sm-6 text-right">
                      <label class="form-check-label labelinput" for="exampleCheck1">Valor</label>
                    </div>
                    <div class="col-md-4 col-sm-6">
                      <input class="form-control form-control-sm" type="text" id="val_comconID" name="val_comcon" value="'.$info['val_comcon'].'">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-8 col-sm-6 text-right">
                      <label class="form-check-label labelinput" for="exampleCheck1">¿El conductor presenta resoluciones?</label>
                    </div>
                    <div class="col-md-4 col-sm-6">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pregu2con" id="pregu2con" value="1" onchange="cambioIndicadores(this)" '.$ind_preresSi.'>
                        <label class="form-check-label" for="inlineRadio1">Si</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pregu2con" id="pregu2con" value="0" onchange="cambioIndicadores(this)" '.$ind_preresNo.'>
                        <label class="form-check-label" for="inlineRadio2">No</label>
                      </div>
                    </div>
                  </div>

                  <div class="row mt-3 mb-3" id="com_pregu2con">
                    <div class="col-md-8 col-sm-6 text-right">
                      <label class="form-check-label labelinput" for="exampleCheck1">Valor</label>
                    </div>
                    <div class="col-md-4 col-sm-6">
                      <input class="form-control form-control-sm" type="text" id="val_resconID" name="val_rescon" value="'.$info['val_rescon'].'">
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="container border">
                <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Referencia familiar del conductor
                  </div>
                </div>
                '.$this->generaReferenceFyP($info['cod_conduc'],'F','1').'

              <div class="container border">
                <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Referencia personal del conductor
                  </div>
                </div>

                '.$this->generaReferenceFyP($info['cod_conduc'],'P','2').'

                <div class="container border">
                <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Referencias laborales del conductor
                  </div>
                </div>

                '.$this->generaReferenceLaborales($info['cod_conduc'],'L','3').'


              <div class="row mt-3 mb-3">
                <div class="col-md-12 col-sm-12 text-right">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="changePestana(1)">Siguiente <i class="fa fa-caret-right" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#modalPreGuardadoF2">Pre Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                </div>
              </div>';
            return $html;
        }


        private function viewPillsBitacora($info){
          $cod_estseg = $info['cod_estseg'];
          $sql = "SELECT 
                      b.nom_estado,
                      a.obs_estado,
                      a.usr_creaci,
                      a.fec_creaci
                  FROM ".BASE_DATOS.".tab_bitaco_estseg a 
                  INNER JOIN ".BASE_DATOS.".tab_estado_estseg b
                  ON a.cod_estado = b.cod_estado
                WHERE a.cod_estseg  = '".$cod_estseg."' ORDER BY a.fec_creaci ASC";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');

          $html.='<div class="container border">
                      <div class="row mt-3 mb-3">
                        <div class="col-12">
                          <table class="table table-bordered" style="width:100%">
                            <thead>
                              <tr class="bk-sure">
                                <th scope="col">Estado</th>
                                <th scope="col">Observación</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Fecha</th>
                              </tr>
                            </thead>
                            <tbody>';
          if(count($resultados)<=0){
            $html.='<tr><td colspan="4" class="text-center">SIN RESULTADOS</td></tr>';
          }else{
            foreach($resultados as $dato){
              $html.='<tr>
                          <td>'.$dato['nom_estado'].'</td>
                          <td>'.$dato['obs_estado'].'</td>
                          <td>'.$dato['usr_creaci'].'</td>
                          <td>'.$dato['fec_creaci'].'</td>
                      </tr>';
            }
          }               
          $html.='          </tbody>
                          </table>
                        </div>
                      </div>
                  </div>';
          return $html;
        }

        private function darTransportadora(){
            $sql="SELECT b.cod_tercer, b.nom_tercer FROM ".BASE_DATOS.".tab_tercer_emptra a INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer ORDER BY b.nom_tercer ASC";
            $resultado = new Consulta($sql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='';
            foreach ($resultados as $registro){
                $html .= '<option value="'.$registro['cod_tercer'].'">'.$registro['nom_tercer'].'</option>';
            }
            return utf8_encode($html);
        }

        function obtenerTransportadoraPerfil(){
          $sql = "SELECT c.cod_tercer, c.nom_tercer, c.dir_emailx,
                        CONCAT(c.num_telef1,' ', c.num_telef2) as 'num_telefo',
                        c.num_telmov
                  FROM ".BASE_DATOS.".tab_genera_usuari a 
                    INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b ON a.cod_perfil = b.cod_perfil
                    INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.clv_filtro = c.cod_tercer
                  WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."'";
          $consulta = new Consulta($sql, $this->conexion);
          $resultados = $consulta->ret_matriz();
          return $resultados[0];
          }
        private function darEstadoGestion(){
          $sql="SELECT a.cod_estado, a.nom_estado FROM ".BASE_DATOS.".tab_estado_estseg a WHERE a.ind_estado = 1 AND a.ind_visibl = 1;";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $html .= '<option value="'.$registro['cod_estado'].'">'.$registro['nom_estado'].'</option>';
          }
          return $html;
        }

        private function darTipoDocumento(){
          $sql="SELECT a.cod_tipdoc, a.nom_tipdoc FROM ".BASE_DATOS.".tab_genera_tipdoc a;";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $html .= '<option value="'.$registro['cod_tipdoc'].'">'.$registro['nom_tipdoc'].'</option>';
          }
          return utf8_encode($html);
        }

        private function darCategoriasLicencia($cod_catlic = NULL){
          $sql="SELECT a.cod_catlic, a.nom_catlic FROM ".BASE_DATOS.".tab_genera_catlic a;";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
            $selected = '';
            if($cod_catlic != '' || $cod_catlic != NULL){
              if($registro['cod_catlic'] == $cod_catlic){
                $selected = 'selected';
              }
            }
            $html .= '<option value="'.$registro['cod_catlic'].'" '.$selected.'>'.$registro['nom_catlic'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getTipoVehiculo($cod_tipveh = NULL){
          $sql="SELECT a.num_config, a.num_config FROM ".BASE_DATOS.".tab_vehige_config a WHERE a.ind_estado = 1;";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_tipveh != '' || $cod_tipveh != NULL){
                if($registro['num_config'] == $cod_tipveh){
                  $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['num_config'].'" '.$selected.'>'.$registro['num_config'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getTipoCarroc($cod_carroc = NULL){
          $sql="SELECT a.cod_carroc, a.nom_carroc FROM ".BASE_DATOS.".tab_vehige_carroc a WHERE a.ind_estado = 1;";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
            $selected = '';
            if($cod_carroc != '' || $cod_carroc != NULL){
              if($registro['cod_carroc'] == $cod_carroc){
                $selected = 'selected';
              }
            }
              $html .= '<option value="'.$registro['cod_carroc'].'" '.$selected.'>'.$registro['nom_carroc'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getColorVehicu($cod_colorx = NULL){
          $sql="SELECT a.cod_colorx, a.nom_colorx FROM ".BASE_DATOS.".tab_vehige_colore a";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_colorx != '' || $cod_colorx != NULL){
                if($registro['cod_colorx'] == $cod_colorx){
                $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['cod_colorx'].'" '.$selected.'>'.$registro['nom_colorx'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getMarcaVehicu($cod_marcax = NULL){
          $sql="SELECT a.cod_marcax, a.nom_marcax FROM ".BASE_DATOS.".tab_genera_marcas a";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_marcax != '' || $cod_marcax != NULL){
                if($registro['cod_marcax'] == $cod_marcax){
                $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['cod_marcax'].'" '.$selected.'>'.$registro['nom_marcax'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getLineaVehicu($cod_lineax = NULL, $cod_marcax = NULL){
          if($cod_lineax == NULL || $cod_lineax ==''){
            return '';
          }
          $sql="SELECT a.cod_lineax, a.nom_lineax FROM ".BASE_DATOS.".tab_vehige_lineas a WHERE a.cod_marcax = '".$cod_marcax."'";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_lineax != '' || $cod_lineax != NULL){
                if($registro['cod_lineax'] == $cod_lineax){
                  $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['cod_lineax'].'" '.$selected.'>'.$registro['nom_lineax'].'</option>';
          }
          return utf8_encode($html);
        }




        private function getCodOpeGPS($cod_opegps = NULL){
          $sql="SELECT a.nit_operad, a.nom_operad FROM ".BD_STANDA.".tab_genera_opegps a WHERE a.ind_estado = 1 ORDER BY a.nom_operad ASC";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          $existe = false;
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_opegps != '' || $cod_opegps != NULL){
                if($registro['nit_operad'] == $cod_opegps){
                $selected = 'selected';
                $existe = true;
                }
              }
              $html .= '<option value="'.$registro['nit_operad'].'" '.$selected.'>'.$registro['nom_operad'].'</option>';
          }

          if((!$existe) && ($cod_opegps != '' ||  $cod_opegps != NULL)){
            $dv = substr($cod_opegps, -1);
            $nit = substr($cod_opegps, 0, -1);
            $sql = "SELECT a.nom_operad FROM ".BASE_DATOS.".tab_genera_opegps a WHERE a.nit_operad = '".$nit."' AND a.nit_verifi = '".$dv."'";
            $resultado = new Consulta($sql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');
            if(count($resultados) > 0){
              $html .= '<option value="'.$cod_opegps.'" selected>'.$resultados[0]['nom_operad'].'</option>';
            }
          }
          return $html;
        }

        private function getParentesco(){
          $sql = "SELECT 
                    a.cod_parent,
                    a.nom_parent
                  FROM ".BASE_DATOS.".tab_genera_parent a 
                  WHERE a.ind_status = 1";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');        
          foreach ($resultados as $key => $registro){
              $html .= '<option value="'.$registro['cod_parent'].'">'.$registro['nom_parent'].'</option>';
          }
          return $html;
        }

        private function getReferenciasFyP($cod_person, $tip_refere, $cod_identi){
           $sql = "SELECT 
                    b.cod_refere,
                    b.nom_refere,
                    b.cod_parent,
                    b.nom_parent,
                    b.dir_domici,
                    b.num_telefo,
                    b.obs_refere
              FROM ".BASE_DATOS.".tab_person_refere a 
              INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON a.cod_refere = b.cod_refere
            WHERE a.cod_person = '".$cod_person."' AND a.tip_refere = '".$tip_refere."'";
          $consulta = new Consulta($sql, $this->conexion);
          $resultados = $consulta->ret_matriz();
          if(count($resultados)==0){
            $html = '<tr id="none_'.$cod_person.'_'.$tip_refere.'_'.$cod_identi.'">
                        <td colspan="5" class="text-center">No hay referencias registradas</td>
                     </tr>';
          }
          foreach($resultados as $resultado){
            $html .= '<tr>
                        <td>'.$resultado['nom_refere'].'</td>
                        <td>'.utf8_decode($resultado['nom_parent']).'</td>
                        <td>'.$resultado['dir_domici'].'</td>
                        <td>'.$resultado['num_telefo'].'</td>
                        <td>'.$resultado['obs_refere'].'</td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="borrarReferenciaFyP(`'.$resultado['cod_refere'].'`,`'.$cod_person.'`,this)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                      </tr>';
          }
          return $html;
        }

        private function getReferenciasLaborales($cod_person, $tip_refere, $cod_identi){
          $sql = "SELECT 
                   b.cod_transp,
                   b.nom_transp,
                   b.num_telefo,
                   b.inf_sumini,
                   b.num_viajes,
                   b.cod_refere
             FROM ".BASE_DATOS.".tab_person_refere a 
             INNER JOIN ".BASE_DATOS.".tab_estseg_reflab b ON a.cod_refere = b.cod_refere
           WHERE a.cod_person = '".$cod_person."' AND a.tip_refere = '".$tip_refere."'";
         $consulta = new Consulta($sql, $this->conexion);
         $resultados = $consulta->ret_matriz();
         if(count($resultados)==0){
           $html = '<tr id="none_'.$cod_person.'_'.$tip_refere.'_'.$cod_identi.'">
                       <td colspan="5" class="text-center">No hay referencias registradas</td>
                    </tr>';
         }
         foreach($resultados as $resultado){
           $html .= '<tr>
                       <td>'.utf8_decode($resultado['nom_transp']).'</td>
                       <td>'.$resultado['num_telefo'].'</td>
                       <td>'.utf8_decode($resultado['inf_sumini']).'</td>
                       <td>'.$resultado['num_viajes'].'</td>
                       <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="borrarReferenciaLaboral(`'.$resultado['cod_refere'].'`,`'.$cod_person.'`,this)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                     </tr>';
         }
         return $html;
       }

        //* FUNCIONES QUE RETORNAN CADA UNA DE LAS VISTAS SEGUN LA PESTAÑA

        private function vRegistradas(){
            $html='<div class="tab-pane fade show active p-3" id="pills-registradas" role="tabpanel" aria-labelledby="pills-registradas-tab">
            <div id="vRegistradas" class="panel-collapse" style="overflow: auto;">
              <table class="table table-bordered" id="tabla_inf_registradas">
                  <thead>
                      <tr>
                          <th>No. Solicitud</th> 
                          <th>Empresa Solicitante</th>
                          <th>Total Solicitudes</th>
                          <th>Avance</th>
                          <th>Tiempo transcurrido</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr id="resultado_info_registradas">
                      </tr>
                  </tbody>
              </table>
            </div>
        </div>';
        return $html;
        }

        private function vEnProceso(){
          $html='<div class="tab-pane fade p-3" id="pills-enproceso" role="tabpanel" aria-labelledby="pills-enproceso-tab">
            <div id="vEnProceso" class="panel-collapse" style="overflow: auto;">
              <table class="table table-bordered" id="tabla_inf_enprogreso">
                  <thead>
                      <tr>
                          <th>No. Solicitud</th> 
                          <th>Empresa Solicitante</th>
                          <th>Total Solicitudes</th>
                          <th>Avance</th>
                          <th>Tiempo transcurrido</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr id="resultado_info_enprogreso">
                      </tr>
                  </tbody>
              </table>
            </div>
        </div>';
          return $html;
        }

        private function vFinalizadas(){
          $html='<div class="tab-pane fade p-3" id="pills-finalizadas" role="tabpanel" aria-labelledby="pills-finalizadas-tab">
          <div id="vFinalizadas" class="panel-collapse" style="overflow: auto;">
            <table class="table table-bordered" id="tabla_inf_finalizadas">
                <thead>
                    <tr>
                        <th>No. Solicitud</th> 
                        <th>Empresa Solicitante</th>
                        <th>Total Solicitudes</th>
                        <th>Avance</th>
                        <th>Tiempo transcurrido</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="resultado_info_finalizadas">
                    </tr>
                </tbody>
            </table>
          </div>
      </div>';
        return $html;
        }

        private function nuevaSolicitudModal(){
            $html = '<!-- Modal Nueva Solicitud-->
            <div class="modal fade" id="NuevaSolicitudModal" role="dialog">
              <div class="modal-dialog modal-lg">
              
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal" class="modal-title"><center>Crear Nueva Solicitud</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <form id="InsSolici"  action="" method="post">
                    <div class="modal-body">
                      '.utf8_encode($this->camposFormulPrincipal()).'
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                      <button type="button" onclick="validateInicial()" class="btn btn-success btn-sm">Crear Solicitud</button>
                    </div>
                  </form>
                </div>
                
              </div>
            </div>';
            return $html;
        }

        private function camposFormulPrincipal(){
            $emptra = $this->obtenerTransportadoraPerfil();
            $mPerms = $this->getReponsability('jso_estseg');
            if($mPerms->dat_regtra->ind_visibl == 1){
            $addTransp = '<div class="row">
                            <div class="col-6">
                              <div class="form-group">
                              <label for="bus_transp" class="labelinput">Seleccione Transportadora:</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Transportadora" id="bus_transp" name="bus_transp" onkeyup="asignaTransportadora(this)" autocomplete="off">
                                <div id="bus_transp-suggestions" class="suggestions" style="top:52px !important"></div>
                              </div>
                            </div>
                          </div>';
            }
            $html='<div class="card" style="margin:5px;">
            <div class="card-header color-heading bk-sure text-center">
              Datos básicos del solicitante
            </div>
          <div class="card-body">
            '.$addTransp.'
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Nombre del solicitante:</label>
                  <input class="form-control form-control-sm" type="text" placeholder="Nombre del solicitante" id="nom_soliciID" name="nom_solici" disabled value="'.$emptra['nom_tercer'].'">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Correo electrónico:</label>
                  <input class="form-control form-control-sm" type="text" placeholder="Correo electronico" id="cor_soliciID" name="cor_solici" disabled value="'.$emptra['dir_emailx'].'">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Número de teléfono:</label>
                  <input class="form-control form-control-sm" type="text" placeholder="Número de teléfono" id="tel_soliciID" name="tel_solici" disabled value="'.$emptra['num_telefo'].'">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Número de celular:</label>
                  <input class="form-control form-control-sm" type="text" placeholder="Número de celular" id="cel_soliciID" name="cel_solici" disabled value="'.$emptra['num_telmov'].'">
                </div>
              </div>
              <input type="hidden" name="cod_transp" id="cod_transp" value="'.$emptra['cod_tercer'].'">
            </div>
          </div>
          </div>

          <div class="card sol_estseg" style="margin:5px;">
            <div class="card-header text-center color-heading bk-sure">
              Estudio de Seguridad No. 1
            </div>
          <div class="card-body">
            <div class="row">
              <div class="col-4 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Conductor:</label>
                <select class="form-control form-control-sm req" id="tip_documeID" name="tip_docume[0]" required>
                  '.$this->darTipoDocumento().'
                </select>
              </div>
              <div class="col-4 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Conductor:</label>
                <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documeID" name="num_docume[0]" required>
              </div>
              <div class="col-4 form-group">
              <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Conductor:</label>
              <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personID" name="nom_person[0]" required>
              </div>
            </div>
            <div class="row">
              <div class="col-4 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Conductor:</label>
                <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1ID" name="nom_apell1[0]" required>
              </div>
              <div class="col-4 form-group">
                <label for="nom_soliciID" class="labelinput">Segundo apellido del Conductor:</label>
                <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2ID" name="nom_apell2[0]">
              </div>
              <div class="col-4 form-group">
              <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de celular del Conductor:</label>
              <input class="form-control form-control-sm req" type="text" placeholder="N° de celular" id="num_telmovID" name="num_telmov[0]" required>
              </div>
            </div>
            <div class="row">
              <div class="col-4 form-group">
                <label for="nom_soliciID" class="labelinput">N° de celular 2 del Conductor:</label>
                <input class="form-control form-control-sm" type="text" placeholder="N° de celular 2" id="num_telmo2ID" name="num_telmo2[0]">
              </div>
              <div class="col-5 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email del Conductor:</label>
                <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emailxID" name="dir_emailx[0]" required>
              </div>
              <div class="col-3 form-group">
                <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Placa:</label>
                <input class="form-control form-control-sm req min6max6" type="text" placeholder="Placa" id="num_placaxID" name="num_placax[0]" required>
              </div>
            </div>
          </div>
          </div>

          <div id="elements-study-seg">
          
          </div>

          <div class="row">
            <div class="col-12 text-center">
              <button type="button" class="btn btn-secondary btn-sm" onclick="generaNuevoEstudio()"><i class="fa fa-plus" aria-hidden="true"></i> Añadir Estudio</button>
            </div>
          </div>


          ';

          return $html;
        }


        private function procesoSolicitudModal(){
          $html = '<!-- Modal Proceso de Solicitud-->
          <div class="modal fade" id="procesoSolicitudModal" role="dialog">
            <div class="modal-dialog modal-lg">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h5 id="title-modal-procSol" class="modal-title text-center"></h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                  <div class="modal-body">
                    <div class="row" style="overflow: auto;">
                      <div class="col-12" id="cont_procesoSolicitudModal" style="margin-right: 30px;">
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                  </div>
              </div>
              
            </div>
          </div>';
          return $html;
      }

      private function modalVisualizarPDF(){
        $html = '<!-- Modal Visualizar PDF y opciones-->
        <div class="modal fade" id="visualizarPDFModal" role="dialog">
          <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <h5 id="title-modal-viewPDF" class="modal-title text-center"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-12 text-center">
                      <p style="margin-bottom:2px; color: #525252;">Descargar PDF</p>
                    </div>
                  </div>
                  <div class="row" style="margin-bottom:40px;">
                    <div class="col-12 text-center">
                      <img src="../satt_standa/imagenes/pdf-icon.png" id="btn-pdf" width="40px" onclick="viewPdf(this)">
                    </div>
                  </div>
                  <div class="row mt-3 mb-3 color-heading bk-sure">
                    <div class="col-12 text-center"><h6>Enviar archivo</h6></div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Correos separados por coma (,)</label>
                      <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-sm" id="ema_envarch">
                        <div class="input-group-append">
                          <button class="btn btn-info info-color btn-sm" id="btn-sendpdf" type="button"  onclick="sendPdfEmail(this)"><i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12" id="resp-sendEmail">
                      
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary secondary-color btn-sm" id="btn-atrasPDF" onclick="atrasModalViewPdf(this)">Atras</button>
                </div>
            </div>
            
          </div>
        </div>';
        return $html;
      }

      private function modalVisualizarDocuments(){
        $html = '<!-- Modal Visualizar Documentos-->
        <div class="modal fade" id="visualizarDocumentos" role="dialog" style="overflow-y: auto;">
          <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <h5 id="title-modal-viewDocuments" class="modal-title text-center"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
                <div class="modal-body">
                  <div class="row mt-3 mb-3 color-heading bk-sure">
                    <div class="col-12 text-center"><h6>Documentos cargados</h6></div>
                  </div>
                  <div class="row ml-2">
                    <table class="table table-bordered" style="width:97%">
                          <thead>
                            <tr class="bk-sure">
                              <th scope="col">Documento</th>
                              <th scope="col">Observaci&oacute;n</th>
                              <th scope="col">Descargar</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_document">
                          </tbody>
                    </table>
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary secondary-color btn-sm" id="btn-atrasDocuments" onclick="atrasModalViewDocuments(this)">Atras</button>
                </div>
            </div>
            
          </div>
        </div>';
        return $html;
      }

      private function modalPreGuardadoF1(){
        $html = '<!-- Modal Proceso de Solicitud-->
        <div class="modal fade" id="modalPreGuardadoF1" role="dialog">
          <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <h5 id="title-modal-preF1" class="modal-title text-center">Pre Guardado</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
                <div class="modal-body">
                  <div class="container">
                    <div class="row">
                      <div class="col-4">
                        <label for="nom_soliciID" class="labelinput">
                            Gestión:
                        </label>
                        <select class="form-control form-control-sm" id="cod_gestio" name="cod_gestio">
                        '.$this->darEstadoGestion().'
                        </select>
                      </div>
                      <div class="col-8 form-group">
                        <label class="labelinput" for="obs_gestio">Observacion:</label>
                        <textarea class="form-control" id="obs_gestio" rows="4" name="obs_gestio"></textarea>
                      </div>
                    </div>

                    <div class="row ml-2 p-3 bg-danger text-white">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="check_canEstSol" value="1" name="check_canEstSol">
                        <label class="form-check-label" for="check_canEstSol" style="font-size:14px;"> Cancelar estudio de seguridad</label>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                  <button type="button" onclick="preguardadoF1()" class="btn btn-success btn-sm">Pre guardar</button>
                </div>
            </div>
          </div>
        </div>';
        return $html;
    }

    private function modalPreGuardadoF2(){
      $html = '<!-- Modal Proceso de Solicitud-->
      <div class="modal fade" id="modalPreGuardadoF2" role="dialog">
        <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h5 id="title-modal-preF1" class="modal-title text-center">Pre Guardado</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
              <div class="modal-body">
                <div class="container">
                  <div class="row">
                    <div class="col-4">
                      <label for="nom_soliciID" class="labelinput">
                          Gestión:
                      </label>
                      <select class="form-control form-control-sm" id="cod_gestioF2" name="cod_gestioF2">
                      '.$this->darEstadoGestion().'
                      </select>
                    </div>
                    <div class="col-8 form-group">
                      <label class="labelinput" for="obs_gestioF2">Observacion:</label>
                      <textarea class="form-control" id="obs_gestioF2" rows="4" name="obs_gestioF2"></textarea>
                    </div>
                  </div>

                  <div class="row ml-2 p-3 bg-danger text-white">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="check_canEstSolF2" value="1" name="check_canEstSolF2">
                      <label class="form-check-label" for="check_canEstSol" style="font-size:14px;"> Cancelar estudio de seguridad</label>
                    </div>
                  </div>

                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="preguardado()" class="btn btn-success btn-sm">Pre guardar</button>
              </div>
          </div>
        </div>
      </div>';
      return $html;
  }

  private function modalRegistrarOperadoresGps(){
    $html = '<!-- Modal Registrar Operadores GPS-->
    <div class="modal fade" id="modalregOpeGps" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h5 id="title-modal-preF1" class="modal-title text-center">Registrar Operador GPS</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
            <form id="regOpeGPS">
            <div class="modal-body">
              <div class="container">
                <div class="row">
                  <div class="col-5 form-group">
                    <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombre del Operador:</label>
                    <input class="form-control form-control-sm req" type="text" placeholder="Nombre del Operador" id="nom_operadID" name="nom_operad" required>
                  </div>
                  <div class="col-7 form-group">
                    <label for="url_gpsxxxID" class="labelinput"><div class="obl">*</div>Url del Operador:</label>
                    <input class="form-control form-control-sm req" type="text" placeholder="Url del operador" id="url_gpsxxxID" name="url_gpsxxx" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-3 form-group">
                    <label for="nit_operadID" class="labelinput"><div class="obl">*</div>Nit del Operador:</label>
                    <input class="form-control form-control-sm req num" type="number" placeholder="Nit" id="nit_operadID" name="nit_operad" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
              <button type="button" onclick="guardarGPS()" class="btn btn-success btn-sm">Registrar</button>
            </div>
        </div>
        </form>
      </div>
    </div>';
    return $html;
}

    private function modalInfoSolicitudPreview($info){
      $html = '<!-- Modal Proceso de Solicitud-->
      <div class="modal fade" id="modalInfoSolicitudPreview" role="dialog">
        <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h5 id="title-modal-infopreview" class="modal-title text-center">Datos de la solicitud</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
              <div class="modal-body">
                <div class="container">

                  <div class="row">
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">
                          Tipo de documento Conductor:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="tip_documeID_preview" value="'.$info['nom_tipcon'].'" disabled>
                    </div>
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">
                          N° de documento Conductor:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="num_documeID_preview" value="'.$info['num_doccon'].'" disabled>
                    </div>
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">
                          Nombres del Conductor:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="nom_personID_preview" value="'.$info['nom_nomcon'].'" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">
                        Primer apellido del Conductor:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="nom_apell1ID_preview" value="'.$info['nom_ap1con'].'" disabled>
                    </div>
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">Segundo apellido del Conductor:</label>
                      <input class="form-control form-control-sm" type="text" id="nom_apell2ID_preview" value="'.$info['nom_ap2con'].'" disabled>
                    </div>
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">
                          N° de celular del Conductor:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="num_telmovID_preview" value="'.$info['num_mo1con'].'" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput">N° de celular 2 del Conductor:</label>
                      <input class="form-control form-control-sm" type="text" id="num_telmo2ID_preview" value="'.$info['num_mo2con'].'" disabled>
                    </div>
                    <div class="col-5 form-group">
                      <label for="nom_soliciID" class="labelinput">
                          Email del Conductor:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="dir_emailxID_preview" value="'.$info['dir_emacon'].'" disabled>
                    </div>
                    <div class="col-3 form-group">
                      <label for="nom_soliciID" class="labelinput">
                          Placa:
                      </label>
                      <input class="form-control form-control-sm" type="text" id="num_placaxID_preview" value="'.$info['num_placax'].'" disabled>
                    </div>
                  </div>

                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
              </div>
          </div>
        </div>
      </div>';
      return $html;
  }


        function BitacoradeRespuestas(){
          $html='<div class="card border border-success" style="margin:15px;">
                  <div class="card-header color-heading bk-sure text-align">
                    Bitacora de Respuestas
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered">
                      <thead>
                        <tr style="background-color:#80c166">
                          <th scope="col">Detalle</th>
                          <th scope="col">Estado</th>
                          <th scope="col">Fecha y Hora</th>
                          <th scope="col">Usuario</th>
                        </tr>
                      </thead>
                      <tbody id="bitacoRespuesta">
                      </tbody>
                    </table>
                  </div>
                </div>';
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
            $mConsult = new Consulta($mSql, $this -> conexion);
            $mData = $mConsult->ret_matrix('a');

            return json_decode($mData[0][$mCatego]);
        }

    }

    new GeneraEstudioSeguridad($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
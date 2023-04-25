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
                <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_genera_estsegV2.js"></script>
            ';
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
            echo '<table style="width: 100%;" id="dashBoardTableTrans">
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
                                                    <div class="col-1">
                                                        <input type="text" id="num_soliciID" name="num_solici" size="8">
                                                    </div>
                                                    <div class="col-2 text-right">
                                                      <label>Placa / Identificación:</label>
                                                    </div>
                                                    <div class="col-2">
                                                      <input type="text" id="num_soliciID" name="num_solici">
                                                    </div>

                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-2 text-right">
                                                        <label>No filtrar por fechas</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <input type="checkbox" id="fil_fechasID" value="fil_fechas">
                                                    </div>
                                                    <div class="col-2 text-right">
                                                        <label>Fecha Inicial:</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <input type="date" id="fec_inicio" name="fec_inicio" value="'.$mDateYes.'">
                                                    </div>
                                                    <div class="col-1 text-right">
                                                        <label>Fecha Final:</label>
                                                    </div>
                                                    <div class="col-2">
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
                                                    <a class="btn btn-success btn-sm" style="background-color:#509334" id="pills-finalizadas-tab" data-toggle="pill" href="#pills-finalizadas" role="tab" aria-controls="pills-finalizadas" aria-selected="false">Finalizados</a>
                                                </li>
                                            </ul>

                                      <div class="tab-content" id="pills-tabContent">
                                        '.$this->vRegistradas().'
                                        '.$this->vFinalizadas().'
                                      </div>

                                        </div>
                                    </div>
                                </div>
                                '.$this->nuevaSolicitudModal().'
                                '.$this->modalVisualizarPDF().'
                                '.$this->modalVisualizarDocuments().'
                </td>
            </tr>
                                                ';
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

        //usado
        private function getInfoSolicitud($cod_solici){
            $sql = "SELECT   a.cod_solici, a.cod_emptra, a.cor_solici,
                             a.tel_solici, a.cel_solici, a.cod_tipest, a.cod_estcon, a.ind_credes,
                             b.nom_tercer as 'nom_solici', c.cod_tercer as 'num_doccon', c.cod_tipdoc as 'cod_tipcon',
                             c.ciu_expdoc as 'ciu_expcon', c.nom_apell1 as 'nom_ap1con',
                             c.nom_apell2 as 'nom_ap2con', c.nom_person as 'nom_nomcon', c.num_licenc as 'num_licenc',
                             c.cod_catlic as 'cod_catlic', c.fec_venlic as 'fec_venlic', c.nom_arlxxx as 'nom_arlcon',
                             c.nom_epsxxx as 'nom_epscon', c.num_telmov as 'num_mo1con', c.num_telmo2,
                             c.num_telefo as 'num_telcon', c.cod_paisxx, c.cod_depart,
                             c.cod_ciudad as 'ciu_rescon', c.dir_domici as 'dir_domcon', c.dir_emailx as 'dir_emacon',
                             c.ind_precom as 'pre_comcon', c.val_compar as 'val_comcon', c.ind_preres as 'pre_rescon',
                             c.val_resolu as 'val_rescon', d.num_placax,
                             d.num_remolq, d.cod_marcax, d.cod_lineax,
                             d.ano_modelo, d.cod_colorx, d.cod_carroc,
                             d.num_config, d.num_chasis, d.num_motorx,
                             d.num_soatxx, d.fec_vigsoa, d.num_lictra,
                             d.cod_opegps, d.usr_gpsxxx, d.clv_gpsxxx,
                             d.url_gpsxxx, d.idx_gpsxxx, d.obs_opegps,
                             d.fre_opegps, d.ind_precom as 'ind_preveh', d.val_compar as 'val_comveh',
                             d.ind_preres as 'ind_preveh', d.val_resolu as 'val_resveh',
                             e.cod_tercer as 'num_docpos', e.cod_tipdoc as 'cod_tippos', e.ciu_expdoc as 'ciu_exppos',
                             e.nom_apell1 as 'nom_ap1pos', e.nom_apell2 as 'nom_ap2pos', e.nom_person as 'nom_nompos',
                             e.num_telmov as 'num_mo1pos', e.num_telmo2 as 'num_mo2pos', e.num_telefo as 'num_telpos',
                             e.cod_paisxx, e.cod_depart, e.cod_ciudad as 'ciu_respos',
                             e.dir_domici as 'dir_dompos', e.dir_emailx as 'dir_emapos',
                             f.cod_tercer as 'num_docpro', f.cod_tipdoc as 'cod_tippro', f.ciu_expdoc as 'ciu_exppro',
                             f.nom_apell1 as 'nom_ap1pro', f.nom_apell2 as 'nom_ap2pro', f.nom_person as 'nom_nompro',
                             f.num_telmov as 'num_mo1pro', f.num_telmo2 as 'num_mo2pro', f.num_telefo as 'num_telpro',
                             f.cod_paisxx, f.cod_depart, f.cod_ciudad as 'ciu_respro',
                             f.dir_domici as 'dir_dompro', f.dir_emailx as 'dir_emapro',
                             g.cod_despac, g.cod_tipdes, g.cod_paiori, g.cod_ciuori,
                             g.cod_paides, g.cod_ciudes, g.cod_rutasx, g.val_declar,
                             g.val_pesoxx, g.cod_genera
              FROM ".BASE_DATOS.".tab_estseg_solici a
        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
            ON a.cod_emptra = b.cod_tercer
        LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer c
            ON a.cod_conduc = c.cod_tercer
        LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu d
            ON a.cod_vehicu = d.num_placax
        LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer e
            ON d.cod_poseed = e.cod_tercer
        LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer f
            ON d.cod_propie = f.cod_tercer
        LEFT JOIN ".BASE_DATOS.".tab_estseg_despac g
            ON a.cod_despac = g.cod_despac
            WHERE a.cod_solici = '".$cod_solici."'; ";
            $query = new Consulta($sql, $this->conexion );
            $resultado = $query -> ret_arreglo();
            return $resultado;
        }

        function getConfigTipServic($cod_transp){
            $sql = "SELECT 
                      a.num_consec, a.cod_transp, a.ind_estseg,
                      a.tip_estseg, a.ind_segxml, a.rut_segxml,
                      a.rut_estpdf
                    FROM ".BASE_DATOS.".tab_transp_tipser a
                  WHERE a.cod_transp  = '".$cod_transp."'
                  ORDER BY a.num_consec DESC
                  LIMIT 1";
            $resultado = new Consulta($sql, $this->conexion);
            $registro = $resultado->ret_matriz('a')[0];
            return $registro;
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

        private function darCiudadPais($cod_ciudad, $cod_paisxx){
          $sql = "SELECT a.cod_ciudad, a.cod_paisxx, a.nom_ciudad 
                  FROM ".BASE_DATOS.".tab_genera_ciudad a
                  WHERE a.cod_ciudad = '".$cod_ciudad."' AND
                        a.cod_paisxx = '".$cod_paisxx."';";
          $query = new Consulta($sql, $this->conexion);
          $resultado = $query -> ret_matrix('a')[0];
          if(count($resultado)>0){
            return $resultado['nom_ciudad']." (".$resultado['cod_ciudad']."-".$resultado['cod_paisxx'].")";
          }else{
            return '';
          }
        }

        //usado
        private function mostrarFormularioEstSeguridad(){
          self::styles();
          $info = $this->getInfoSolicitud($_REQUEST['cod_solici']);
          $infoTipSer = $this->getConfigTipServic($info['cod_emptra']);
          $cod_tipest = $info['cod_tipest'];

          
          /*
          
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
          if($info['cod_poseed']==$info['cod_propie'] && $info['cod_poseed']!=NULL && $info['cod_propie']!=NULL){
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
          */
          
          echo '<table style="width: 100%;" id="dashBoardTableTrans">
                  <tr>
                      <td>
                          <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                              <div class="container" style="min-width: 98%;">
                                <div class="card style="margin:15px;">
                                  <div class="card-header color-heading text-center">
                                    Diligenciar estudio de seguridad #'.$_REQUEST['cod_solici'].'
                                  </div>
                                <div class="card-body">
                                  <form id="dataSolicitud" action="" method="post" enctype="multipart/form-data" onsubmit="validateEstudioSoliciFinal()">
                                  <input type="hidden" name="rut_estpdf" id="rut_estpdfID" value="'.$infoTipSer['rut_estpdf'].'">
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
                                  <input type="hidden" value="'.$info['cod_solici'].'" name="cod_solici">
                                  <div id="collapsethree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                                      <ul class="nav nav-pills mb-3 bk-sure" id="pills-tab" role="tablist">';
                                      
                    if($cod_tipest == 'V'){
                                        $label_pestana = 'Poseedor / Tenedor';
                                        if ($info['num_docpos'] == $info['num_docpro']) {
                                          $label_pestana = 'Poseedor / Propietario';
                                        }

                                        echo '<li class="nav-item m-2">
                                                <a class="btn btn-success btn-sm"  id="pills-vehiculo-tab" data-toggle="pill" href="#pills-vehiculo" role="tab" aria-controls="pills-vehiculo" aria-selected="true">vehículo</a>
                                              </li>
                                              <li class="nav-item m-2">
                                                  <a class="btn btn-success btn-sm"  id="pills-poseedor-tab" data-toggle="pill" href="#pills-poseedor" role="tab" aria-controls="pills-poseedor" aria-selected="false">'.$label_pestana.'</a>
                                              </li>';

                                        if($info['num_docpos'] != $info['num_docpro']){
                                        echo '<li class="nav-item m-2">
                                                  <a class="btn btn-success btn-sm"  id="pills-propietario-tab" data-toggle="pill" href="#pills-propietario" role="tab" aria-controls="pills-propietario" aria-selected="false">Propietario</a>
                                              </li>';
                                            }

                     }else if($cod_tipest == 'C'){
                                        echo '<li class="nav-item m-2">
                                                <a class="btn btn-success btn-sm"  id="pills-conductor-tab" data-toggle="pill" href="#pills-conductor" role="tab" aria-controls="pills-conductor" aria-selected="true">Conductor</a>
                                              </li>';
                     }else{
                        $label_pestana = 'Poseedor / Tenedor';
                        if ($info['num_docpos'] == $info['num_docpro']) {
                          $label_pestana = 'Poseedor / Propietario';
                        }

                        echo '<li class="nav-item m-2">
                                <a class="btn btn-success btn-sm"  id="pills-vehiculo-tab" data-toggle="pill" href="#pills-vehiculo" role="tab" aria-controls="pills-vehiculo" aria-selected="true">vehículo</a>
                              </li>
                              <li class="nav-item m-2">
                                  <a class="btn btn-success btn-sm"  id="pills-poseedor-tab" data-toggle="pill" href="#pills-poseedor" role="tab" aria-controls="pills-poseedor" aria-selected="false">'.$label_pestana.'</a>
                              </li>';

                        if($info['num_docpos'] != $info['num_docpro']){
                        echo '<li class="nav-item m-2">
                                  <a class="btn btn-success btn-sm"  id="pills-propietario-tab" data-toggle="pill" href="#pills-propietario" role="tab" aria-controls="pills-propietario" aria-selected="false">Propietario</a>
                              </li>';
                        }
                        echo '<li class="nav-item m-2">
                                                <a class="btn btn-success btn-sm"  id="pills-conductor-tab" data-toggle="pill" href="#pills-conductor" role="tab" aria-controls="pills-conductor" aria-selected="true">Conductor</a>
                                              </li>';

                     }

                     //Pestaña creación de despacho
                     if($info['ind_credes']==1){
                      echo '<li class="nav-item m-2">
                              <a class="btn btn-success btn-sm"  id="pills-despacho-tab" data-toggle="pill" href="#pills-despacho" role="tab" aria-controls="pills-despacho" aria-selected="true">Despacho</a>
                            </li>';
                     }

                                      echo '<li class="nav-item m-2">
                                              <a class="btn btn-success btn-sm" id="pills-bitacora-tab" data-toggle="pill" href="#pills-bitacora" role="tab" aria-controls="pills-bitacora" aria-selected="false">Bitacora</a>
                                            </li>
                                        </ul>
                                    </div>
                                  <div class="tab-content border" id="pills-tabContent">';
                      if($cod_tipest == 'V'){              
                          echo '<div class="tab-pane fade show p-3 active" id="pills-vehiculo" role="tabpanel" aria-labelledby="pills-vehiculo-tab">
                                    '.$this->viewPillsVehiculo($info).'
                                </div>
                                <div class="tab-pane fade p-3" id="pills-poseedor" role="tabpanel" aria-labelledby="pills-poseedor-tab">
                                    '.$this->viewPillsPoseedor($info).'
                                  </div>';
                          if ($info['num_docpos'] != $info['num_docpro']) {
                                echo '<div class="tab-pane fade p-3" id="pills-propietario" role="tabpanel" aria-labelledby="pills-propietario-tab">
                                       ' . $this->viewPillsPropietario($info) . '
                                      </div>';
                          }
                      }else if($cod_tipest == 'C'){
                               echo '<div class="tab-pane fade show p-3 active" id="pills-conductor" role="tabpanel" aria-labelledby="pills-conductor-tab">
                                        '.$this->viewPillsConductor($info).'
                                     </div>';
                       }else{
                              echo '<div class="tab-pane fade show p-3 active" id="pills-vehiculo" role="tabpanel" aria-labelledby="pills-vehiculo-tab">
                                        '.$this->viewPillsVehiculo($info).'
                                    </div>
                                    <div class="tab-pane fade p-3" id="pills-poseedor" role="tabpanel" aria-labelledby="pills-poseedor-tab">
                                        '.$this->viewPillsPoseedor($info).'
                                    </div>';
                            if ($info['num_docpos'] != $info['num_docpro']) {
                                echo '<div class="tab-pane fade p-3" id="pills-propietario" role="tabpanel" aria-labelledby="pills-propietario-tab">
                                       ' . $this->viewPillsPropietario($info) . '
                                      </div>';
                              }
                              echo '<div class="tab-pane fade show p-3" id="pills-conductor" role="tabpanel" aria-labelledby="pills-conductor-tab">
                                      '.$this->viewPillsConductor($info).'
                                    </div>';
                        }
                        
                        if($info['ind_credes']==1){
                          echo '<div class="tab-pane fade p-3" id="pills-despacho" role="tabpanel" aria-labelledby="pills-despacho-tab">
                                      '.$this->viewPillsDespacho($info).'
                                    </div>';

                        }
                               echo '<div class="tab-pane fade p-3" id="pills-bitacora" role="tabpanel" aria-labelledby="pills-bitacora-tab">
                                        '.$this->viewPillsBitacora($info).'
                                     </div>
                                  </div>
                                    

                                  <div class="row mt-3 mb-3">
                                    <div class="col-12 text-right">
                                      <button type="button" class="btn btn-success btn-sm" onclick="valSaveGestioSolici()">Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                                    </div>
                                 </div>

                                </div>
                            </form>
                            '.$this->modalPreGuardado().'
                            '.$this->modalGuardadoFinal().'
                          </td>
                      </tr>';
          self::scripts();
        }

        //usada
        private function generaReferenceFyP($cod_person,$tip_refere,$cod_identi){
          $llave = $cod_person.'_'.$tip_refere.'_'.$cod_identi;
          $referen = $this->getReferenciasFyP($cod_person, $tip_refere,$cod_identi);
          $html.='
          <div class="container">
          <div class="row">
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
            <label class="labelinput" for="obs_refereID">Observación:</label>
            <textarea class="form-control Req_ReferenceFyP" id="obs_refereID_'.$llave.'" name="obs_refere_'.$llave.'" rows="2"></textarea>
          </div>
        </div>

        <div id="div-cual-input_'.$llave.'" style="display:none">
          <div class="row mt-2">
            <div class="col-md-3 col-sm-12 pt-1 text-right align-self-center">
              Â¿Cual?
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
          <div class="container">
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
                            Pï¿½liza extracontractual
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

        //usado
        private function viewPillsConductor($info){
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
                      <select class="form-control form-control-sm req" id="tip_docconID" name="tip_doccon" disabled>
                          '.$this->darTipoDocumento($info['cod_tipcon']).'
                      </select>
                  </div>
                  <div class="col-md-2 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>No Documento:</label>
                      <input class="form-control form-control-sm req num" type="text" id="num_docconID" name="num_doccon" value="'.$info['num_doccon'].'" disabled>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad de expedición:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="De" id="lug_expcon" name="lug_expcon" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_expcon']).'" validate>
                      <div id="lug_expcon-suggestions" class="suggestions" style="top: 50px !important;"></div>
                  </div>
                  <div class="col-md-4 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_nomconID" name="nom_nomcon" value="'.$info['nom_nomcon'].'" disabled>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_ap1conID" name="nom_ap1con" value="'.$info['nom_ap1con'].'" disabled>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput">Segundo apellido:</label>
                      <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_ap2conID" name="nom_ap2con" value="'.$info['nom_ap2con'].'" disabled>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Numero de celular:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Numero de celular" id="num_mo1conID" name="num_mo1con" value="'.$info['num_mo1con'].'" validate>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Teléfono</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Teléfono" id="num_telconID" name="num_telcon" value="'.$info['num_telcon'].'" validate>
                  </div>
              </div>';

              //Información Complementaria Conductor
              if($info['cod_estcon']=='3'){
                $html.='
                          <div class="row">
                              <div class="col-md-3 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Licencia de conducción No.:</label>
                                  <input class="form-control form-control-sm req num" type="text" id="num_licencID" name="num_licenc" value="'.$info['num_licenc'].'" validate>
                              </div>
                              <div class="col-md-2 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Categoria:</label>
                                  <select class="form-control form-control-sm req" id="tip_catlicID" name="tip_catlic" validate>
                                      '.$this->darCategoriasLicencia($info['cod_catlic']).'
                                  </select>
                              </div>
                              <div class="col-md-3 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Fecha de vencimiento:</label>
                                  <input class="form-control form-control-sm req" type="date" id="fec_venlicID" name="fec_venlic" value="'.$info['fec_venlic'].'" validate>
                              </div>
                              <div class="col-md-2 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Arl:</label>
                                  <input class="form-control form-control-sm req" type="text" placeholder="Arl" id="nom_arlconID" name="nom_arlcon" value="'.$info['nom_arlcon'].'" validate>
                              </div>
                              <div class="col-md-2 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Eps</label>
                                  <input class="form-control form-control-sm req" type="text" placeholder="Eps" id="nom_epsconID" name="nom_epscon" value="'.$info['nom_epscon'].'" validate>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Dirección:</label>
                                  <input class="form-control form-control-sm req" type="text" placeholder="Dirección" id="dir_domconID" name="dir_domcon" value="'.$info['dir_domcon'].'" validate>
                              </div>
                              <div class="col-md-4 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad:</label>
                                  <input class="form-control form-control-sm req" type="text" id="ciu_conduc" name="ciu_conduc" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_rescon']).'" validate>
                                  <div id="ciu_conduc-suggestions" class="suggestions" style="top: 50px !important;"></div>
                              </div>
                              <div class="col-md-4 col-sm-12 form-group">
                                  <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email:</label>
                                  <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emaconID" name="dir_emacon" value="'.$info['dir_emacon'].'" validate>
                              </div>
                          </div>';
          }       


          $html.='
          </div>
          <div class="container border">
              <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Estudio de seguridad del conductor
                  </div>
              </div>
              '.$this->getFormulDocume(2, $info['cod_solici']).'
          </div>

          <div class="container border">
            <div class="row">
                <div class="col-md-6 col-sm-12 form-group">
                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-6 text-left">
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
                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-6 text-left">
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
              </div>';

          //Referencias Conductor
          if($info['cod_estcon']=='3'){    
              $html.='
                        <div class="container border">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                                    Referencia familiar del conductor
                                </div>
                                '.$this->generaReferenceFyP($info['num_doccon'],'F','1').'
                            </div>
                                
                            <div class="row">
                                <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                                      Referencia personal del conductor
                                </div>
                                '.$this->generaReferenceFyP($info['num_doccon'],'P','2').'
                            </div>

                            <div class="row">
                                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                                        Referencias laborales del conductor
                                  </div>
                                  '.$this->generaReferenceLaborales($info['num_doccon'],'L','3').'
                            </div>
                          </div>';
            }
            return $html;
        }

        private function viewPillsVehiculo($info){
          //Indicador si el vehiculo presenta resoluciones
          $pre_resvehSi='';
          $pre_resvehNo='';
          if($info['ind_preveh']==1){
            $pre_resvehSi='checked';
          }else{
            $pre_resvehNo='checked';
          }
          $html.='<div class="container border">
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
                        <input class="form-control form-control-sm req" type="text" id="num_placaxID" name="num_placax" value="'.$info['num_placax'].'" disabled>
                      </div>';
            if($info['cod_estcon']=='3'){
            $html.='   <div class="col-3 form-group">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                            No remolque:
                        </label>
                        <input class="form-control form-control-sm req" type="text" id="num_remolqID" name="num_remolq" value="'.$info['num_remolq'].'" validate>
                      </div>
                      <div class="col-3 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Tipo de vehículo:
                          </label>
                          <select class="form-control form-control-sm req" id="num_configID" name="num_config" validate>
                          '.$this->getTipoVehiculo($info['num_config']).'
                          </select>
                      </div>
                      <div class="col-3 form-group">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Tipo de Carrocería:
                        </label>
                        <select class="form-control form-control-sm req" id="cod_carrocID" name="cod_carroc" validate>
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
                        <input class="form-control form-control-sm req" type="text" id="ano_modeloID" name="ano_modelo" value="'.$info['ano_modelo'].'" validate>
                      </div>
                      <div class="col-3 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Color:
                          </label>
                          <select class="form-control form-control-sm req" id="cod_colorxID" name="cod_colorx" validate>
                          '.$this->getColorVehicu($info['cod_colorx']).'
                          </select>
                      </div>
                      <div class="col-3 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Marca:
                          </label>
                          <select class="form-control form-control-sm req" id="cod_marcaxID" name="cod_marcax" onchange="traeLineas(this)" validate>
                          '.$this->getMarcaVehicu($info['cod_marcax']).'
                          </select>
                      </div>
                      <div class="col-3 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Linea:
                          </label>
                          <select class="form-control form-control-sm req" id="cod_lineaxID" name="cod_lineax" validate>
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
                        <input class="form-control form-control-sm req" type="text" id="num_chasisID" name="num_chasis" value="'.$info['num_chasis'].'" validate>
                      </div>
                      <div class="col-3 form-group">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Número del motor:
                        </label>
                        <input class="form-control form-control-sm req" type="text" id="num_motorxID" name="num_motorx" value="'.$info['num_motorx'].'" validate>
                      </div>
                      <div class="col-3 form-group">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Número del soat:
                        </label>
                        <input class="form-control form-control-sm req" type="text" id="num_soatxxID" name="num_soatxx" value="'.$info['num_soatxx'].'" validate>
                      </div>
                      <div class="col-3 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Fecha de vigencia del SOAT:
                          </label>
                          <input class="form-control form-control-sm req" type="date" id="fec_vigsoaID" name="fec_vigsoa" value="'.$info['fec_vigsoa'].'" validate>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-3 form-group">
                        <label for="nom_soliciID" class="labelinput">
                          <div class="obl">*</div>
                          Número de licencia de transito:
                        </label>
                        <input class="form-control form-control-sm req" type="text" id="num_lictraID" name="num_lictra" value="'.$info['num_lictra'].'" validate>
                      </div>
                    </div>

                  </div>';
            }else{
              $html.='</div></div>';
            }
            
          if($info['cod_estcon']=='3'){
          $html.=' <div class="container border">
                    <div class="row">
                      <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                        Información del operador GPS
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>Operador GPS:
                          </label>
                          <select class="form-control form-control-sm req" id="cod_opegpsID" name="cod_opegps" validate>
                          '.$this->getCodOpeGPS($info['cod_opegps']).'
                          </select>
                        </div>
                        <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Usuario:
                          </label>
                          <input class="form-control form-control-sm req" type="text" id="usr_gpsxxxID" name="usr_gpsxxx" value="'.$info['usr_gpsxxx'].'" validate>
                        </div>
                        <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            <div class="obl">*</div>
                              Contraseña:
                          </label>
                          <input class="form-control form-control-sm req" type="text" id="clv_gpsxxxID" name="clv_gpsxxx" value="'.$info['clv_gpsxxx'].'" validate>
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
                          <input class="form-control form-control-sm req" type="text" id="fre_opegpsID" name="fre_opegps" value="'.$info['fre_opegps'].'" validate>
                        </div>
                    </div>
                  </div>';
          }else{
            $html.=' <div class="container border">
                    <div class="row">
                      <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                        Información del operador GPS
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            Operador GPS:
                          </label>
                          <select class="form-control form-control-sm" id="cod_opegpsID" name="cod_opegps">
                          '.$this->getCodOpeGPS($info['cod_opegps']).'
                          </select>
                        </div>
                        <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">
                              Usuario:
                          </label>
                          <input class="form-control form-control-sm" type="text" id="usr_gpsxxxID" name="usr_gpsxxx" value="'.$info['usr_gpsxxx'].'">
                        </div>
                        <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">
                              Contraseña:
                          </label>
                          <input class="form-control form-control-sm" type="text" id="clv_gpsxxxID" name="clv_gpsxxx" value="'.$info['clv_gpsxxx'].'">
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
                              Observación:
                          </label>
                          <input class="form-control form-control-sm" type="text" id="obs_opegpsID" name="obs_opegps" value="'.$info['obs_opegps'].'">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                          <label for="nom_soliciID" class="labelinput">
                            Frecuencia:
                          </label>
                          <input class="form-control form-control-sm" type="text" id="fre_opegpsID" name="fre_opegps" value="'.$info['fre_opegps'].'">
                        </div>
                    </div>
                  </div>';
          }       

          $html.='<div class="container border">
                    <div class="row">
                      <div class="col-12 color-heading bk-sure text-center p-2 mb-3">
                        Estudio de seguridad del vehículo
                      </div>
                    </div>
                    '.$this->getFormulDocume(1, $info['cod_solici']).'
                  </div>
                  <div class="container border">
                    <div class="row mt-2">
                      <div class="col-md-12 col-sm-12 form-group">
                        <div class="row" style="display: flex; align-items: center;">
                          <div class="col-4 text-left">
                            <label class="form-check-label labelinput" for="exampleCheck1">¿El vehículo presenta resoluciones?</label>
                          </div>
                          <div class="col-2">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ind_preres" id="ind_preres" value="1" '.$pre_resvehSi.' onchange="cambioIndicadores(this)">
                              <label class="form-check-label" for="inlineRadio1">Si</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ind_preres" id="ind_preres" value="0" '.$pre_resvehNo.' onchange="cambioIndicadores(this)">
                              <label class="form-check-label" for="inlineRadio2">No</label>
                            </div>
                          </div>
                          <div class="col-4" id="com_ind_preres">
                            <label class="form-check-label labelinput" for="exampleCheck1">Valor</label>
                            <input class="form-control form-control-sm" type="text" id="val_resvehID" name="val_resveh" value="'.$info['val_resveh'].'">
                          </div>
                        </div>
                      </div>
                      </div>
                    </div>  ';
          
          return $html;
        }

        private function viewPillsPoseedor($info){
          $html='
          <div>
            <div class="container border">
              <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Datos Básicos del Poseedor
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento:</label>
                      <select class="form-control form-control-sm req" id="tip_docposID" name="tip_docpos" disabled>
                          '.$this->darTipoDocumento($info['cod_tippos']).'
                      </select>
                  </div>
                  <div class="col-md-2 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>No Documento:</label>
                      <input class="form-control form-control-sm req num" type="text" id="num_docposID" name="num_docpos" value="'.$info['num_docpos'].'" disabled>
                  </div>';
          if($info['cod_estcon']=='3'){
          $tam = '4';
          $html.='<div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad de expedición:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="De" id="lug_exppos" name="lug_exppos" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_exppos']).'" validate>
                      <div id="lug_exppos-suggestions" class="suggestions" style="top: 50px !important;"></div>
                  </div>';
          }else{
            $tam = '7';
          }  
          $html.='<div class="col-md-'.$tam.' col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_nomposID" name="nom_nompos" value="'.$info['nom_nompos'].'" disabled>
                  </div>
              </div>

              <div class="row">
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_ap1posID" name="nom_ap1pos" value="'.$info['nom_ap1pos'].'" disabled>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput">Segundo apellido:</label>
                      <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_ap2posID" name="nom_ap2pos" value="'.$info['nom_ap2pos'].'" disabled>
                  </div>';
           if($info['cod_estcon']=='3'){
           $html.='<div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Numero de celular:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Numero de celular" id="num_mo1posID" name="num_mo1pos" value="'.$info['num_mo1pos'].'" validate>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Teléfono</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Teléfono" id="num_telposID" name="num_telpos" value="'.$info['num_telpos'].'" validate>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-4 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Dirección:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Dirección" id="dir_domposID" name="dir_dompos" value="'.$info['dir_dompos'].'" validate>
                  </div>
                  <div class="col-md-4 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad:</label>
                      <input class="form-control form-control-sm req" type="text" id="ciu_poseed" name="ciu_poseed" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_respos']).'" validate>
                      <div id="ciu_poseed-suggestions" class="suggestions" style="top: 50px !important;"></div>
                  </div>
                  <div class="col-md-4 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email:</label>
                      <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emaposID" name="dir_emapos" value="'.$info['dir_emapos'].'" validate>
                  </div>
              </div>
          </div>
          <div class="container border">
              <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Estudio de seguridad del Poseedor
                  </div>
              </div>
              '.$this->getFormulDocume(3,  $info['cod_solici']).'    
          </div>';
           }else{
            $html.='</div></div>';
           }
            $html.='</div>';
            return $html;
        }

        private function viewPillsPropietario($info){
          $html='
          <div>
            <div class="container border">
              <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Datos Básicos del Propietario
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento:</label>
                      <select class="form-control form-control-sm req" id="tip_docproID" name="tip_docpro" disabled>
                          '.$this->darTipoDocumento($info['cod_tippro']).'
                      </select>
                  </div>
                  <div class="col-md-2 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>No Documento:</label>
                      <input class="form-control form-control-sm req num" type="text" id="num_docproID" name="num_docpro" value="'.$info['num_docpro'].'" disabled>
                  </div>';
            if($info['cod_estcon']=='3'){
            $tam = '4';
            $html.='<div class="col-md-3 col-sm-12 form-group">
                          <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad de expedición:</label>
                          <input class="form-control form-control-sm req" type="text" placeholder="De" id="lug_exppro" name="lug_exppro" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_exppro']).'" validate>
                          <div id="lug_exppro-suggestions" class="suggestions" style="top: 50px !important;"></div>
                     </div>';
            }else{
              $tam = '7';
            }
          $html.='<div class="col-md-'.$tam.' col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_nomproID" name="nom_nompro" value="'.$info['nom_nompro'].'" disabled>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_ap1proID" name="nom_ap1pro" value="'.$info['nom_ap1pro'].'" disabled>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="nom_soliciID" class="labelinput">Segundo apellido:</label>
                      <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_ap2proID" name="nom_ap2pro" value="'.$info['nom_ap2pro'].'" disabled>
                  </div>';
              //Información Complementaria Poseedor
              if($info['cod_estcon']=='3'){
                $html.='<div class="col-md-3 col-sm-12 form-group">
                            <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Numero de celular:</label>
                            <input class="form-control form-control-sm req" type="text" placeholder="Numero de celular" id="num_mo1proID" name="num_mo1pro" value="'.$info['num_mo1pro'].'" validate>
                        </div>
                        <div class="col-md-3 col-sm-12 form-group">
                            <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Teléfono</label>
                            <input class="form-control form-control-sm req" type="text" placeholder="Teléfono" id="num_telproID" name="num_telpro" value="'.$info['num_telpro'].'" validate>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-12 form-group">
                            <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Dirección:</label>
                            <input class="form-control form-control-sm req" type="text" placeholder="Dirección" id="dir_domproID" name="dir_dompro" value="'.$info['dir_dompro'].'" validate>
                        </div>
                        <div class="col-md-4 col-sm-12 form-group">
                            <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Ciudad:</label>
                            <input class="form-control form-control-sm req" type="text" id="ciu_propie" name="ciu_propie" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadInput($info['ciu_respro']).'" validate>
                            <div id="ciu_propie-suggestions" class="suggestions" style="top: 50px !important;"></div>
                        </div>
                        <div class="col-md-4 col-sm-12 form-group">
                            <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email:</label>
                            <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emaproID" name="dir_emapro" value="'.$info['dir_emapro'].'" validate>
                        </div>
                    </div>
                </div>
                <div class="container border">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                            Estudio de seguridad del Propietario
                        </div>
                    </div>
                        '.$this->getFormulDocume(4,  $info['cod_solici']).'
                </div>';
              }else{
                $html.='</div></div>';
              }
            $html.='</div>';
            return $html;
        }

        private function viewPillsDespacho($info){
          $html='
          <div>
            <div class="container border">
              <div class="row">
                  <div class="col-md-12 col-sm-12 color-heading bk-sure text-center p-2 mb-3">
                      Información Básica Creación del Despacho
                  </div>
              </div>
              <div class="row">
                  <input type="hidden" name="cod_despac" id="cod_despacID" value="'.$info['cod_despac'].'">
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="ciu_origen" class="labelinput"><div class="obl">*</div>Ciudad Origen:</label>
                      <input class="form-control form-control-sm ciu_despac req" type="text" id="ciu_origen" name="ciu_origen" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadPais($info['cod_ciuori'], $info['cod_paiori']).'" validate>
                      <div id="ciu_origen-suggestions" class="suggestions" style="top: 50px !important;"></div>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                      <label for="ciu_destin" class="labelinput"><div class="obl">*</div>Ciudad Destino:</label>
                      <input class="form-control form-control-sm ciu_despac req" type="text" id="ciu_destin" name="ciu_destin" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" autocomplete="off" value="'.$this->darCiudadPais($info['cod_ciudes'], $info['cod_paides']).'" validate>
                      <div id="ciu_destin-suggestions" class="suggestions" style="top: 50px !important;"></div>
                  </div>
                  <div class="col-md-6 col-sm-12 form-group">
                      <label for="rut_despacID" class="labelinput"><div class="obl">*</div>Ruta</label>
                      <select class="form-control form-control-sm req" id="rut_despacID" name="rut_despac">
                        '.$this->getRutas($info).'
                      </select>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-2 col-sm-12 form-group">
                      <label for="tip_despacID" class="labelinput"><div class="obl">*</div>Tipo de Despacho:</label>
                      <select class="form-control form-control-sm req" id="tip_despacID" name="tip_despac">
                          '.$this->getTipDespachos($info['cod_tipdes']).'
                      </select>
                  </div>
                  <div class="col-md-2 col-sm-12 form-group">
                      <label for="val_declarID" class="labelinput">Valor Declarado:</label>
                      <input class="form-control form-control-sm" type="text" placeholder="Valor Declarado" id="val_declarID" name="val_declar" value="'.$info['val_declar'].'" validate>
                  </div>
                  <div class="col-md-2 col-sm-12 form-group">
                        <label for="val_pesoxxID" class="labelinput"><div class="obl">*</div>Peso (Tn):</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Peso " id="val_pesoxxID" name="val_pesoxx" value="'.$info['val_pesoxx'].'" validate>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                        <label for="age_despacID" class="labelinput"><div class="obl">*</div>Agencia</label>
                        <select class="form-control form-control-sm req" id="age_despacID" name="age_despac">
                          '.$this->getAgencias($info['cod_agenci'], $info['cod_emptra']).'
                        </select>
                  </div>
                  <div class="col-md-3 col-sm-12 form-group">
                        <label for="gen_despacID" class="labelinput">Generador de Carga</label>
                        <select class="form-control form-control-sm" id="gen_despacID" name="gen_despac">
                          '.$this->getGenerador($info['cod_genera'], $info['cod_emptra']).'
                        </select>
                  </div>
                </div>
              </div>
            </div>';
            return $html;
        }

        //usado
        private function viewPillsBitacora($info){
          $cod_solici = $info['cod_solici'];
          $sql = "SELECT 
                      b.nom_estado,
                      a.obs_estado,
                      a.usr_creaci,
                      a.fec_creaci
                  FROM ".BASE_DATOS.".tab_estseg_bitaco a 
                  INNER JOIN ".BASE_DATOS.".tab_estseg_estado b
                  ON a.cod_estado = b.cod_estado
                WHERE a.cod_solici  = '".$cod_solici."' ORDER BY a.fec_creaci ASC";
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


        //usado
        private function getFormulDocume($cod_person, $cod_solici){
           $html = '';
           $mSql="SELECT a.cod_fordoc, a.nom_fordoc, a.nom_slugxx,
                         a.ing_obliga
                  FROM ".BASE_DATOS.".tab_estseg_fordoc a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_solici b ON b.cod_solici = '".$cod_solici."'
                  INNER JOIN ".BASE_DATOS.".tab_estseg_fortip c ON c.cod_estseg = b.cod_estcon AND c.cod_fordoc = a.cod_fordoc
                              WHERE a.ind_status = 1 AND
                                    a.cod_tipper = '".$cod_person."'
                              ORDER BY ind_ordenx, a.nom_fordoc ASC
            ";
            $resultado = new Consulta($mSql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');
            
            $indice = 0;
            $max = 2;
            foreach ($resultados as $index=>$registro){
              $mSql="SELECT a.nom_archiv, a.obs_archiv, a.nom_rutfil FROM ".BASE_DATOS.".tab_estseg_docume a
                        WHERE a.cod_solici = '".$cod_solici."' AND
                              a.cod_fordoc = '".$registro['cod_fordoc']."'";
              $resul = new Consulta($mSql, $this->conexion);
              $documento = $resul->ret_matriz('a')[0];

              $attrDoc = '';
              $oblHtml = '';
              $documentohtml = '';
              if($registro['ing_obliga']==1){
                $attrDoc = 'validate';
                $oblHtml = '<div class="obl">*</div>';
              }
              if($documento['nom_rutfil'] != ''){
                $documentohtml = '<div class="row">
                                    <div class="col-md-12 col-sm-12 mb-2">
                                      <div class="d-flex justify-content-between alert alert-success" role="alert">
                                        <div><strong><i class="fa fa-check" aria-hidden="true"></i> Documento Guardado: </strong> '.$documento['nom_archiv'].'</div>
                                        <div><a class="text-right" href="'.URL_APLICA.'files/adj_estseg/adjs/'.$documento['nom_archiv'].'"><i class="fa fa-eye" aria-hidden="true"></i></a></div>
                                      </div>
                                    </div>
                                  </div>';
                 $attrDoc = '';
              }

                if($indice==0){
                  $html .= '<div class="row mb-2">';
                }
                //accept="image/png,image/jpeg, image/jpg"
                $html .= '
                            <div class="col-md-6 col-sm-12 form-group" style="margin-bottom: 0 !important;">
                              <div class="row">
                                <div class="col-md-12 col-sm-12 mb-2">
                                  <label for="'.$registro['nom_slugxx'].'ID" class="labelinput docreq">'.$oblHtml.''.$registro['nom_fordoc'].'</label>
                                  <input type="file" class="inputDocument docreq" id="'.$registro['nom_slugxx'].'ID" name="'.$registro['nom_slugxx'].'" '.$attrDoc.'>
                                </div>
                              </div>
                              '.$documentohtml.'
                              <div class="row">
                                <div class="col-md-12 col-sm-12 mb-3">
                                  <label class="labelinput" for="'.$registro['nom_slugxx'].'OBS_ID">Observación:</label>
                                  <textarea class="form-control" id="'.$registro['nom_slugxx'].'OBS_ID" rows="2" name="'.$registro['nom_slugxx'].'OBS">'.$documento['obs_archiv'].'</textarea>
                                </div>
                              </div>
                            </div>
                          ';
                $indice++;
                if($indice==$max || ($index+1)==count($resultados)){
                  $html .= '</div>';
                  $indice = 0;
                }
                
            }
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
                    INNER JOIN ".BASE_DATOS.".tab_transp_tipser d ON d.cod_transp = c.cod_tercer
                  WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."'
                        AND d.num_consec = (
                          SELECT MAX(num_consec)
                          FROM tab_transp_tipser
                          WHERE cod_transp = d.cod_transp
                        )
                    GROUP BY d.cod_transp
                    ORDER BY d.num_consec DESC;
                  ";
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

        private function darTipoDocumento($cod_tipdoc = ''){
          $sql="SELECT a.cod_tipdoc, a.nom_tipdoc FROM ".BASE_DATOS.".tab_genera_tipdoc a;";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
            $select = '';
            if($cod_tipdoc != '' && $registro['cod_tipdoc'] == $cod_tipdoc){
              $select = 'selected';
            }
            $html .= '<option value="'.$registro['cod_tipdoc'].'" '.$select.'>'.$registro['nom_tipdoc'].'</option>';
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
          $sql="SELECT a.cod_operad, a.nom_operad FROM ".BD_STANDA.".tab_genera_opegps a WHERE a.ind_estado = 1 ORDER BY a.nom_operad ASC";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          $existe = false;
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_opegps != '' || $cod_opegps != NULL){
                if($registro['cod_operad'] == $cod_opegps){
                $selected = 'selected';
                $existe = true;
                }
              }
              $html .= '<option value="'.$registro['cod_operad'].'" '.$selected.'>'.$registro['nom_operad'].'</option>';
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

        private function getRutas($info){
          $mSql = "SELECT a.cod_rutasx, a.nom_rutasx
                          FROM ".BASE_DATOS.".tab_genera_rutasx a
                            WHERE ind_estado = 1 AND
                                  cod_paiori = '".$info['cod_paiori']."' AND
                                  cod_ciuori = '".$info['cod_ciuori']."' AND
                                  cod_paides = '".$info['cod_paides']."' AND
                                  cod_ciudes = '".$info['cod_ciudes']."'
                                  ";
          $consulta = new Consulta($mSql, $this->conexion);
          $resultados = $consulta->ret_matriz();
          $html='<option>Seleccione Ruta...</option>';
          foreach ($resultados as $registro){
              $selected = '';
              if($info['cod_rutasx']  != '' || $info['cod_rutasx']  != NULL){
                if($registro['cod_rutasx'] == $info['cod_rutasx'] ){
                $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['cod_rutasx'].'" '.$selected.'>'.$registro['nom_rutasx'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getTipDespachos($cod_tipdes = NULL){
          $sql="SELECT a.cod_tipdes, a.nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes a WHERE a.ind_estado = 1";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_tipdes  != '' || $cod_tipdes  != NULL){
                if($registro['cod_tipdes'] == $cod_tipdes ){
                $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['cod_tipdes'].'" '.$selected.'>'.$registro['nom_tipdes'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getAgencias($cod_agenci = NULL, $cod_transp = NULL){
          $sql="SELECT b.cod_agenci, b.nom_agenci 
                      FROM ".BASE_DATOS.".tab_transp_agenci a 
                INNER JOIN ".BASE_DATOS.".tab_genera_agenci b ON a.cod_agenci = b.cod_agenci
                WHERE a.cod_transp = '".$cod_transp."'";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $selected = '';
              if($cod_agenci != '' || $cod_agenci  != NULL){
                if($registro['cod_agenci'] == $cod_agenci ){
                $selected = 'selected';
                }
              }
              $html .= '<option value="'.$registro['cod_agenci'].'" '.$selected.'>'.$registro['nom_agenci'].'</option>';
          }
          return utf8_encode($html);
        }

        private function getGenerador($cod_tercer = NULL, $cod_transp = NULL){
          $sql="SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                        ".BASE_DATOS.".tab_tercer_activi b,
                        ".BASE_DATOS.".tab_transp_tercer c
                  WHERE a.cod_tercer = b.cod_tercer AND
                        a.cod_tercer = c.cod_tercer AND
                        c.cod_transp = '".$cod_transp."' AND
                        b.cod_activi = ".COD_FILTRO_CLIENT."";
            $resultado = new Consulta($sql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='';
            $html .= '<option>Seleccione un Generador</option>';
            foreach ($resultados as $registro){
              $selected = '';
              if($cod_tercer != '' || $cod_tercer  != NULL){
                if($registro['cod_tercer'] == $cod_tercer ){
                $selected = 'selected';
                }
              }
              
              $html .= '<option value="'.$registro['cod_tercer'].'" '.$selected.'>'.$registro['nom_tercer'].'</option>';
            }
            return utf8_encode($html);
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

        //usada
        private function getReferenciasFyP($cod_person, $tip_refere, $cod_identi){
           $sql = "SELECT 
                    b.cod_refere,
                    b.nom_refere,
                    b.cod_parent,
                    b.nom_parent,
                    b.dir_domici,
                    b.num_telefo,
                    b.obs_refere
              FROM ".BASE_DATOS.".tab_estseg_relref a 
              INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON a.cod_refere = b.cod_refere
            WHERE a.cod_estper = '".$cod_person."' AND a.tip_refere = '".$tip_refere."'";
          $consulta = new Consulta($sql, $this->conexion);
          $resultados = $consulta->ret_matriz();
          if(count($resultados)==0){
            $html = '<tr id="none_'.$cod_person.'_'.$tip_refere.'_'.$cod_identi.'">
                        <td colspan="6" class="text-center">No hay referencias registradas</td>
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
             FROM ".BASE_DATOS.".tab_estseg_relref a 
             INNER JOIN ".BASE_DATOS.".tab_estseg_reflab b ON a.cod_refere = b.cod_refere
           WHERE a.cod_estper = '".$cod_person."' AND a.tip_refere = '".$tip_refere."'";
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

        //* FUNCIONES QUE RETORNAN CADA UNA DE LAS VISTAS SEGUN LA PESTAÃ‘A

        private function vRegistradas(){
            $html='<div class="tab-pane fade show active p-3" id="pills-registradas" role="tabpanel" aria-labelledby="pills-registradas-tab">
            <div id="vRegistradas" class="panel-collapse" style="overflow: auto;">
              <table class="table table-bordered" id="tabla_inf_registradas">
                  <thead>
                      <tr>
                          <th>No. Solicitud</th> 
                          <th>Empresa Solicitante</th>
                          <th>Tipo de Solicitud</th>
                          <th>Identificación</th>
                          <th>Nombre / Marca</th>
                          <th>Fecha / Hora de Solicitud</th>
                          <th>Tiempo Transcurrido</th>
                      </tr>
                  </thead>
                  <tbody>
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
                        <th>Tipo de Solicitud</th>
                        <th>Identificación</th>
                        <th>Nombre / Marca</th>
                        <th>Documentación</th>
                        <th>Descargar/Ver</th>
                        <th>Fecha / Hora de Solicitud</th>
                        <th>Fecha / Hora de Finalización</th>
                        <th>Tiempo de Respuesta</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
      </div>';
        return $html;
        }

        //usada
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
                      '.$this->camposFormulPrincipal().'
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

        //usada
        private function camposFormulPrincipal(){
            $emptra = $this->obtenerTransportadoraPerfil();
            $mPerms = $this->getReponsability('jso_estseg');
            if($mPerms->dat_regtra->ind_visibl == 1){
            $addTransp = '<div class="row">
                            <div class="col-6">
                              <div class="form-group">
                              <label for="bus_transp" class="labelinput">Seleccione Transportadora:</label>
                                <input class="form-control form-control-sm req" type="text" placeholder="Transportadora" id="bus_transp" name="bus_transp" onkeyup="asignaTransportadora(this)" autocomplete="off" validate>
                                <div id="bus_transp-suggestions" class="suggestions" style="top:52px !important"></div>
                              </div>
                            </div>
                          </div>';
            }
            $html='<div class="card" style="margin:5px;">
            <div class="card-header color-heading bk-sure text-center">
              Datos Básicos del solicitante
            </div>
          <div class="card-body">
            '.$addTransp.'
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Nombre del solicitante:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Nombre del solicitante" id="nom_soliciID" name="nom_solici" validate disabled value="'.$emptra['nom_tercer'].'">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Correo electrónico:</label>
                  <input class="form-control form-control-sm req ema" type="text" placeholder="Correo electronico" id="cor_soliciID" name="cor_solici" validate disabled value="'.$emptra['dir_emailx'].'">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Número de Teléfono:</label>
                  <input class="form-control form-control-sm" type="text" placeholder="Número de Teléfono" id="tel_soliciID" name="tel_solici" disabled value="'.$emptra['num_telefo'].'">
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Número de celular:</label>
                  <input class="form-control form-control-sm" type="text" placeholder="Número de celular" id="cel_soliciID" name="cel_solici" disabled value="'.$emptra['num_telmov'].'" validate>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Tipo de estudio:</label>
                  <input class="form-control form-control-sm" style="color: #cf4343;" type="text" placeholder="Tipo de estudio" id="tip_estudioc" disabled readonly value="'.$emptra['num_telmov'].'">
                </div>
              </div>
              <input type="hidden" name="cod_transp" id="cod_transp" value="'.$emptra['cod_tercer'].'">
            </div>

            <div class="row">
              <div class="col-12">
                <div class="form-group" style="margin-bottom:0px;">
                  <label for="nom_soliciID" class="labelinput">Tipo de estudio:</label>
                  <label class="ml-2 mr-2 radio-inline">
                    <input class="mr-1" type="radio" name="tip_estudi" value="V" checked>Vehículo
                  </label>
                  <label class="ml-2 mr-2 radio-inline">
                    <input class="mr-1" type="radio" name="tip_estudi" value="C">Conductor
                  </label>
                  <label class="ml-2 mr-2 radio-inline">
                    <input class="mr-1" type="radio" name="tip_estudi" value="CV">Combinado (Vehículo/Conductor)
                  </label>
                </div>
              </div>
            </div>

          </div>
          </div>

          <div class="card sol_estseg" style="margin:5px;">
            <div class="card-header text-center color-heading bk-sure">
              Estudio de Seguridad
            </div>
            <div class="card-body" id="formSolicitud">
              
              <div id="formVehicuSolici">
                <div class="row">
                  <div class="col-3 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Placa:</label>
                      <input class="form-control form-control-sm req min6max6" type="text" placeholder="Placa" id="num_placaxID_V" name="num_placax_V" validate>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="header-row">Asignación de Poseedor</div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Poseedor:</label>
                      <select class="form-control form-control-sm req" id="tip_documePosID_V" name="tip_documePos_V" validate>
                        '.$this->darTipoDocumento().'
                      </select>
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Poseedor:</label>
                      <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documePosID_V" name="num_documePos_V" validate>
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Poseedor:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personPosID_V" name="nom_personPos_V" validate>
                  </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Poseedor:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1PosID_V" name="nom_apell1Pos_V" validate>
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput">Segundo apellido del Poseedor:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2PosID_V" name="nom_apell2Pos_V">
                    </div>
                </div>
                <div class="row ml-2">
                  <div class="col-4 form-group">
                    <input type="checkbox" class="form-check-input esPropieClass" name="esPropie_V" showForm="formPropietV">
                    <label class="form-check-label p-1" for="esPropieID">¿El poseedor es propietario?</label>
                  </div>
                </div>
                <div id="formPropietV">
                  <div class="row">
                    <div class="col-12">
                      <div class="header-row">Asignación de Propietario</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Propietario:</label>
                        <select class="form-control form-control-sm req" id="tip_documeProID_V" name="tip_documePro_V" validate>
                          '.$this->darTipoDocumento().'
                        </select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Propietario:</label>
                        <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documeProID_V" name="num_documePro_V" validate>
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Propietario:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personProID_V" name="nom_personPro_V" validate>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Propietario:</label>
                          <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1ProID_v" name="nom_apell1Pro_V" validate>
                      </div>
                      <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">Segundo apellido del Propietario:</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2ProID_V" name="nom_apell2Pro_V">
                      </div>
                  </div>
                </div>
              </div>

              <div id="formConducSolici">
                <div class="row">
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Conductor:</label>
                      <select class="form-control form-control-sm req" id="tip_documeConID_C" name="tip_documeCon_C">
                        '.$this->darTipoDocumento().'
                      </select>
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Conductor:</label>
                      <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documeConID_C" name="num_documeCon_C">
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Conductor:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personConID_C" name="nom_personCon_C">
                  </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Conductor:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1ConID_C" name="nom_apell1Con_C">
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput">Segundo apellido del Conductor:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2ConID_C" name="nom_apell2Con_C">
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Núm. de celular del Conductor:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Núm. de celular" id="num_telmovConID_C" name="num_telmovCon_C">
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput">Núm. de celular 2 del Conductor:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Núm. de celular 2" id="num_telmo2ConID_C" name="num_telmo2Con_C">
                    </div>
                    <div class="col-5 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email:</label>
                        <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emailxConID_C" name="dir_emailxCon_C">
                    </div>
                </div>
              </div>

              <div id="formCombinadoSolici">

                <div class="row">
                  <div class="col-3 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Placa:</label>
                      <input class="form-control form-control-sm req min6max6" type="text" placeholder="Placa" id="num_placaxID_CV" name="num_placax_CV">
                  </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="header-row">Asignación de Conductor</div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Conductor:</label>
                      <select class="form-control form-control-sm req" id="tip_documeConID_CV" name="tip_documeCon_CV">
                        '.$this->darTipoDocumento().'
                      </select>
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Conductor:</label>
                      <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documeConID_CV" name="num_documeCon_CV">
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Conductor:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personConID_CV" name="nom_personCon_CV">
                  </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Conductor:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1ConID_CV" name="nom_apell1Con_CV">
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput">Segundo apellido del Conductor:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2ConID_CV" name="nom_apell2Con_CV">
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Núm. de celular del Conductor:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Núm. de celular" id="num_telmovConID_CV" name="num_telmovCon_CV">
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput">Núm. de celular 2 del Conductor:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Núm.  de celular 2" id="num_telmo2ConID_CV" name="num_telmo2Con_CV">
                    </div>
                    <div class="col-5 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email:</label>
                        <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emailxConID_CV" name="dir_emailxCon_CV">
                    </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="header-row">Asignación de Poseedor</div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Poseedor:</label>
                      <select class="form-control form-control-sm req" id="tip_documePosID_CV" name="tip_documePos_CV">
                        '.$this->darTipoDocumento().'
                      </select>
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Poseedor:</label>
                      <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documePosID_CV" name="num_documePos_CV">
                  </div>
                  <div class="col-4 form-group">
                      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Poseedor:</label>
                      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personPosID_CV" name="nom_personPos_CV">
                  </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Poseedor:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1PosID_CV" name="nom_apell1Pos_CV">
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput">Segundo apellido del Poseedor:</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2PosID_CV" name="nom_apell2Pos_CV">
                    </div>
                </div>
                <div class="row ml-2">
                  <div class="col-4 form-group">
                    <input type="checkbox" class="form-check-input esPropieClass" name="esPropie_CV" showForm="formPropietCom">
                    <label class="form-check-label p-1" for="esPropieID">¿El poseedor es propietario?</label>
                  </div>
                </div>
                <div id="formPropietCom">
                  <div class="row">
                    <div class="col-12">
                      <div class="header-row">Asignación de Propietario</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Propietario:</label>
                        <select class="form-control form-control-sm req" id="tip_documeProID_CV" name="tip_documePro_CV">
                          '.$this->darTipoDocumento().'
                        </select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N° de documento Propietario:</label>
                        <input class="form-control form-control-sm req num" type="text" placeholder="N° de documento" id="num_documeProID_CV" name="num_documePro_CV">
                    </div>
                    <div class="col-4 form-group">
                        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Propietario:</label>
                        <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personProID_CV" name="nom_personPro_CV">
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Propietario:</label>
                          <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1ProID_CV" name="nom_apell1Pro_CV">
                      </div>
                      <div class="col-4 form-group">
                          <label for="nom_soliciID" class="labelinput">Segundo apellido del Propietario:</label>
                          <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2ProID_CV" name="nom_apell2Pro_CV">
                      </div>
                  </div>

                </div>


                <div class="row mt-2 ml-2">
                  <div class="col-6 form-group">
                    <input type="checkbox" class="form-check-input" id="genDespac" name="genDespac">
                    <label class="form-check-label p-1" for="genDespacID"> Generar la creación del despacho al finalizar</label>
                  </div>
                </div>

              </div>



            </div>
          </div>
          ';

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
                      <img src="../satt_standa/imagenes/pdf-icon.png" id="btn-pdf" width="40px" onclick="viewPdf(this)" style="cursor: pointer">
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
                  <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
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
                  <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            
          </div>
        </div>';
        return $html;
      }

      //usado
      private function modalPreGuardado(){
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
                      <div class="col-12 form-group">
                        <label class="labelinput" for="obs_gestio"><div class="obl">*</div> Observacion:</label>
                        <textarea class="form-control" id="obs_gestio" rows="4" name="obs_gestio"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cancelar</button>
                  <button type="button" onclick="preguardado()" class="btn btn-success btn-sm">Pre guardar</button>
                </div>
            </div>
          </div>
        </div>';
        return $html;
      }

      //modalPreGuardadoF2
private function modalGuardadoFinal(){
      $html = '<!-- Modal Proceso de Solicitud-->
      <div class="modal fade" id="modalGuardadoFinal" role="dialog">
        <div class="modal-dialog modal-lg">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h5 id="title-modal-preF1" class="modal-title text-center">Guardar y Terminar</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
              <div class="modal-body">
                <div class="container">
                  <div class="row mb-2">
                    <div class="col-3">
                      <label class="form-check-label labelinput" for="exampleCheck1"><div class="obl">*</div> Resultado del estudio:</label>
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
                      <div class="form-check form-check-inline">
                        <input class="form-check-input req" type="radio" name="ind_estudi" id="ind_estudi3" value="C">
                        <label class="form-check-label" style="color:red" for="inlineRadio2">Cancelado</label>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="mt-2 col-12 form-group">
                      <label class="labelinput" for="obs_gestio"><div class="obl">*</div> Observación:</label>
                      <textarea class="form-control" id="obs_gestio_f" rows="4" name="obs_gestio"></textarea>
                    </div>
                  </div>
                  


                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" onclick="validateEstudioSoliciFinal()" class="btn btn-success btn-sm">Guardar y Terminar</button>
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
                          NÂ° de documento Conductor:
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
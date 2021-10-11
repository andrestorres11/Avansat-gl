<?php
    /****************************************************************************
	NOMBRE:   InfActiviDesarr
	FUNCION:  Llama los componentes principales, los demas son dinamicos de js y ajax 
	FECHA DE MODIFICACION: 07/10/2021
	CREADO POR: Ing. Carlos Nieto
	MODIFICADO 
	****************************************************************************/
	
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/

    class InfActiviDesarr
    {
        
        var $conexion,
        $cod_aplica,
        $usuario;

        function __construct($co, $us, $ca){
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;
            $this -> filtros();
            switch($_REQUEST['opcion'])
            {
              case "1":
                self::formNewActiviModal();
              break;
              case "2":
                self::vGeneral();
              break;
            }
        }

        /*! \fn: styles
		   *  \brief: incluye todos los archivos necesarios para los estilos
		   *  \author: Ing. Carlos Nieto
		   *  \date: 07-10-2021
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
                
                <!-- Datatables -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

                <!-- Float Menu -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">

                <!-- Export Excel -->
                
            ';
        }

        /*! \fn: scripts
		   *  \brief: incluye todos los archivos necesarios para los eeventos js
		   *  \author: Ing. Carlos Nieto
		   *  \date: 07-10-2021
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
                
                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_activi_desarr.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_activi_desarr.js"></script>
            ';
        }


        
        /*! \fn: filtros
		   *  \brief: Crea el html de las tablas filtros y segmentos del modulo
		   *  \author: Ing. Carlos Nieto
		   *  \date: 07-10-2021
		   *  \date modified: dd/mm/aaaa
		   *  \param: 
		   *  \return: html
		*/

        private function filtros(){
            //Links css
            self::styles();

            $mDateNOW = date('Y-m-d');
            $mDateTem = date_sub(date_create($mDateNOW), date_interval_create_from_date_string("7 days") );
            
            echo "<pre style='display:none'>"; print_r( $mDateTem ); echo "</pre>"; 
            $mDateYes = date("Y-m-d", strtotime($mDateTem ->date));
            //Body
            echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <div class="container" style="min-width: 98%;">
                                <div id="accordion">
                                <div style="margin: 5px;">
                                    <a class="btn btn-success active" style="background-color:#509334" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true" onclick="openNewActiviModal(true)">Nueva Actividad</a>
                                </div>
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Filtros Generales                                         </button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">
                                                <form id="filter" method="POST">
                                                <div class="row mt-2">
                                                    <div class="col-3 text-right">
                                                        <label>Fecha Inicial:</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <input type="date" id="fec_inicio" name="fec_inicio" required value="'.$mDateYes.'">
                                                    </div>
                                                    <div class="col-3 text-right">
                                                        <label>Fecha Final:</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <input type="date" id="fec_finxxx" name="fec_finxxx" required value="'.$mDateNOW.'">
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
                                                    <a class="btn btn-success active" style="background-color:#509334" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true" onclick="selectTable(1)">Administrativas</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success active" style="background-color:#509334" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true" onclick="selectTable(2)">Operativas</a>  
                                                </li>
                                            </ul>

                                      <div class="tab-content" id="pills-tabContent">
                                        '.$this->vGeneral().'
                                      </div>

                                        </div>
                                    </div>
                                </div>
                                '.$this->formulPorGestioModal().'
                                '.$this->formNewActiviModal().'
                                '.$this->historyModal().'
                </td>
            </tr>');
            
            //Remote scripts
            self::scripts();
        }

        //* FUNCIONES QUE RETORNAN CADA UNA DE LAS VISTAS SEGUN LA PESTAð‘A

        private function vGeneral(){
          $html='<div class="tab-pane fade show active p-3" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab"></div>';
          return $html;
        }

        private function formulPorGestioModal(){
            $html = '<!-- Modal Por Gestionar-->
            <div class="modal fade" id="PorGestioModal" role="dialog">
              <div class="modal-dialog modal-lg">
              
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal" class="modal-title"><center>Modal Header</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    '.$this->camposFormulPrincipal().'
                  </div>
                </div>
                
              </div>
            </div>';
            return $html;
        }

        private function formNewActiviModal(){
          $html = '<!-- Modal Por Gestionar-->
          <div class="modal fade" id="newActiviModal" role="dialog">
            <div class="modal-dialog modal-lg">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h5 id="title-modal" class="modal-title"><center>Nueva Actividad</center></h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                '.$this->fieldsFormNewActivity(true).'
              </div>
              </div>
              
            </div>
          </div>';
          return $html;
        }

        private function historyModal(){
          $html = '<!-- Modal Por Gestionar-->
          <div class="modal fade" id="historyModal" role="dialog">
            <div class="modal-dialog modal-lg">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h6 id="title-modal-history" class="modal-title"><center>DESCRIPCION DE LA ACTIVIDAD  N° </center></h6>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="IdhistoryModal">
                
                </div>
                <div class="modal-footer" id="buttonNewObj">
                
                </div>
              </div>
              
            </div>
          </div>';
          echo $html;
        }

        private function camposFormulPrincipal(){
            $html='<div class="card text-center" style="margin:5px;">
            <div class="card-header color-heading">
              Datos del Solicitante
            </div>
          <div class="card-body">
            <div class="row">
              <div class="offset-1 col-4">
                <input class="form-control form-control-sm" type="text" placeholder="Tipo de Solicitud" id="tip_soliciID" name="tip_solici" required disabled value="'.$datos_usuari['nom_usuari'].'">
              </div>
              <div class="offset-1 col-4">
                <input class="form-control form-control-sm" type="text" placeholder="Nombre del Solicitante" id="nom_soliciID" name="nom_solici" required disabled value="'.$datos_usuari['nom_usuari'].'">
              </div>
            </div>

            <div class="row mt-3">
              <div class="offset-1 col-4">
                <input class="form-control form-control-sm" type="email" placeholder="Email del Solicitante" id="ema_soliciID" name="ema_solici" required disabled value="'.$datos_usuari['usr_emailx'].'">
              </div>
              <div class="offset-1 col-4">
              <input class="form-control form-control-sm" type="number" placeholder="Telð©fono del Solicitante" id="tel_soliciID" name="tel_solici" disabled>
              </div>
            </div>

            <div class="row mt-3">
              <div class="offset-1 col-4">
                <input class="form-control form-control-sm" type="number" placeholder="Numero de Celular" id="cel_soliciID" name="cel_solici" required disabled>
              </div>
              <div class="offset-1 col-4">
              <input class="form-control form-control-sm" type="text" placeholder="Aseguradora" id="nom_aseguraID" name="nom_asegura" disabled>
              </div>
            </div>

            <div class="row mt-3">
              <div class="offset-1 col-4">
                <input class="form-control form-control-sm" type="text" placeholder="Poliza" id="nom_polizaID" name="nom_poliza" disabled>
              </div>
            </div>

          </div>
          </div>

          <div class="card-header color-heading" style="margin:12px;">
          </div>

          <div class="card text-center" style="margin:15px;">
            <div class="card-header color-heading">
              Datos del Transportista
            </div>
          <div class="card-body">

            <div class="row">
              <div class="offset-1 col-3">
                <input class="form-control form-control-sm" type="number" placeholder="Numero de documento" id="num_transpID" name="num_transp" required disabled>
              </div>
              <div class="col-4">
                <input class="form-control form-control-sm" type="text" placeholder="Nombres del Transportista" id="nom_transpID" name="nom_transp" required disabled>
              </div>
              <div class="col-3">
                <input class="form-control form-control-sm" type="text" placeholder="Primer Apellido" id="ap1_transpID" name="ap1_transp" required disabled>
              </div>
            </div>

            <div class="row mt-3">
              <div class="offset-1 col-3">
                <input class="form-control form-control-sm" type="text" placeholder="Segundo Apellido" id="ap2_transpID" name="ap2_transp" disabled>
              </div>
              <div class="col-4">
                <input class="form-control form-control-sm" type="number" placeholder="Numero Celular 1" id="ce1_transpID" name="ce1_transp" required disabled>
              </div>
              <div class="col-3">
                <input class="form-control form-control-sm" type="text" placeholder="Numero Celular 2" id="ce2_transpID" name="ce2_transp" disabled>
              </div>
            </div>

          </div>
          </div>


          <div class="card text-center" style="margin:15px;">
            <div class="card-header color-heading">
              Datos del Vehð­culo
            </div>
          <div class="card-body">

            <div class="row">
              <div class="offset-1 col-3">
                <input class="form-control form-control-sm mayuscul-input" type="text" placeholder="Placa" id="num_placaID" name="num_placax" maxlength="6" required disabled>
              </div>
              <div class="col-4">
                <input class="form-control form-control-sm" type="text" placeholder="Marca" id="nom_marcaxID" name="nom_marcax" required disabled>
              </div>
              <div class="col-3">
                <input class="form-control form-control-sm" type="text" placeholder="Color" id="nom_colorxID" name="nom_colorx" required disabled>
              </div>
            </div>

            <div class="row mt-3">
              <div class="offset-1 col-3">
                <input class="form-control form-control-sm" type="text" placeholder="Tipo" id="tip_transpID" name="tip_transp" disabled>
              </div>
              <div class="col-4">
                <input class="form-control form-control-sm" type="text" placeholder="Remolque No" id="num_remolqID" name="num_remolq" disabled>
              </div>
            </div>

          </div>
          </div>
          
          <div id="con-formul">
          </div>

          <div id="formul-estado">
          </div>
          
          '.$this->operativeForm().'
          ';

          return $html;
        }

        private function fieldsFormNewActivity($bool){
          $html='
          <form id="newActivityForm" method="POST">
          <div class="container">
            <div class="row text-center">
              <div class="col-sm">
                <label>Seleccione el tipo de Actividad * : </label>
                <div class="form-check form-check-inline">
                  <label class="form-check-label" for="inlineRadio1"> Administrativa: </label>
                  <input class="form-check-input" type="radio" name="RadioOptions" id="inlineRadio1" value="1" onchange="selectFuntion(this)">
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label" for="inlineRadio2"> Operativa: </label>
                  <input class="form-check-input" type="radio" name="RadioOptions" id="inlineRadio2" onchange="selectFuntion(this)" value="2">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-sm">
                <h6>Titulo de la Actividad * :</h6>
                <div id="activityTitleSpace"></div>
              </div>
            </div>

            <div class="row  mt-3">
              <div class="col-sm">
                <div class="form-floating">
                  <h6>Descripcion de la Actividad * :</h6>        
                  <textarea class="form-control" placeholder="Escriba aqui...." id="description" name="description" style="height: 100px"></textarea>
                </div>
              </div>
            </div>

            <div class="row  mt-3">
  
              <div class="col-12 container" id="dateTime"></div>

              <div class="col-12 container" id="ajaxOption"></div>

              <div class="col-12 container" id="dinamycSelects"></div>

            </div>
            <div class="row  mt-3">
              <div class="col-md-5 offset-md-3">
                <div class="modal-footer">
                  <button type="button" class="btn btn-success" onclick="InsertNewActivity()">Programar</button>
                  <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                </div>
              </div>
            </div>
          </div>
          </form>';

        return $html;
      }

        function operativeForm($value){
          if($value == true)
          {
              $html='
              <div class="container">
                <div class="row  mt-3">
                  <div class="col-6">
                    <div class="form-floating">
                      <h6>Empresa de Transporte* :</h6>
                      <input class="form-control form-control-sm" type="text" placeholder="Escriba aqui...." id="ActivityTitle" name="activity_title" required">
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-floating">
                      <h6>Placa*(s) :</h6>
                      <input class="form-control form-control-sm" type="text" placeholder="Escriba aqui...." id="ActivityTitle" name="activity_title" required">
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <h6>Seleccione la Novedad: </h6>        
                    <select class="custom-select" id="gender2">
                      <option selected>Seleccione...</option>
                      <option value="1">Male</option>
                      <option value="2">Female</option>
                    </select>  
                  </div>
                </div>
              </div>
            ';
            return $html;
          }
        }
    }

    new InfActiviDesarr($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
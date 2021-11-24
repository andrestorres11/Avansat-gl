<?php
    /****************************************************************************
	NOMBRE:   TabGeneraActivi
	FUNCION:  Llama los componentes principales, los demas son dinamicos de js y ajax 
	FECHA DE MODIFICACION: 07/10/2021
	CREADO POR: Ing. Carlos Nieto
	MODIFICADO 
	****************************************************************************/
	
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/

    class TabGeneraActivi
    {
        
        var $conexion,
        $cod_aplica,
        $usuario;

        function __construct($co, $us, $ca){
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;
            $this -> filtros();
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
                <script src="../' . DIR_APLICA_CENTRAL . '/js/tab_genera_activi.js"></script>
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

            echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <div class="container" style="min-width: 98%;">
                                <div id="accordion">
                                    <div style="margin: 5px;">
                                        <a class="btn btn-success active" style="background-color:#509334" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true" onclick="openCreateModalJs()">Nuevo Tipo de Actividad</a>
                                    </div>
                                </div>

                                <div class="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                Listado de tipos de actividades
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse show m-3" aria-labelledby="headingTwo" data-parent="#accordion">
                                            <div class="tab-content" id="pills-tabContent">
                                                '.$this->vGeneral().'
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                '.$this->openCreateModal().'
                                '.$this->openEditModal().'

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

        private function openCreateModal(){
            $html = '<!-- Modal Por Gestionar-->
            <div class="modal fade" id="openCreateModal" role="dialog">
              <div class="modal-dialog modal-lg">
              
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal" class="modal-title"><center>Modal Header</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                  '.$this->fieldsFormCreate().'
                  </div>
                </div>
                
              </div>
            </div>';
            return $html;
        }

        private function openEditModal(){
            $html = '<!-- Modal Por Gestionar-->
            <div class="modal fade" id="openEditModal" role="dialog">
              <div class="modal-dialog modal-lg">
              
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal" class="modal-title"><center>Modal Header</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body" id="openEditModalID">
                  </div>
                </div>
                
              </div>
            </div>';
            return $html;
        }

        private function fieldsFormCreate(){
          $html='
          <form id="newActivityForm" method="POST">
          <div class="container">

            <div class="row  mt-3">
              <div class="col-sm">
                <div class="form-floating">
                  <h6>Nombre del nuevo tipo Actividad * :</h6>        
                  <input class="form-control" placeholder="Escriba aqui...." id="description" name="description"></input>
                </div>
              </div>
            </div>

            <div class="row  mt-3">
              <div class="col-md-5 offset-md-3">
                <div class="modal-footer">
                  <button type="button" class="btn btn-success" onclick="InsertNewTitle()">Insertar</button>
                  <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                </div>
              </div>
            </div>
          </div>
          </form>';

        return $html;
      }

    }

    new TabGeneraActivi($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
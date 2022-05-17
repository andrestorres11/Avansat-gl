<?php
    /****************************************************************************
	NOMBRE:   InsNovedadParametrizacion
	FUNCION:  Muestra las solicitudes del estudio de seguridad. 
	FECHA DE MODIFICACION: 15/09/2020
	CREADO POR: Ing. Cristian Andr�s Torres
	MODIFICADO 
	****************************************************************************/
	
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/

    class InsNovedadParametrizacion
    {
        
        var $conexion,
        $cod_aplica,
        $usuario;

        function __construct($co, $us, $ca){
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;
            $this -> principal();
            
        }


        /*! \fn: styles
		   *  \brief: incluye todos los archivos necesarios para los estilos
		   *  \author: Ing. Cristian Andr�s Torres
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
                <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/css/buttons.bootstrap4.min.css"  rel="stylesheet">

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
		   *  \author: Ing. Cristian Andr�s Torres
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
                <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/js/buttons.bootstrap.min.js" ></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_parame_noveda.js"></script>
            ';
        }

        
        /*! \fn: filtros
		   *  \brief: Crea el html de las tablas filtros y segmentos del modulo
		   *  \author: Ing. Cristian Andr�s Torres
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

            //Body
            echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <input type="hidden" id="standaID" value="satt_standa">
                            <div class="container" style="min-width: 98%;">
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Filtro de busqueda
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">
                                                <form id="filter" method="POST">
                                                    <div class="row">
                                                        <div class="offset-md-3 col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4" style="text-align:right;">
                                                                    <label for="emp_transp" class="mt-2">Empresa de transporte</label>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <input type="text" class="form-control form-control-sm" name="emp_transp" id="emp_transp" style="width:200px" placeholder="" onkeyup="autocompletable(this)" autocomplete="off" onclick="vaciaInput(this)">
                                                                    <div id="emp_transp-suggestions" class="suggestions"></div>
                                                                </div>
                                                            </div>
                                                           
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row mt-4">
                                                        <div class="col-12 text-center">
                                                            <button type="button" id="btnBuscar" onclick="consultaInformacion()" class="btn btn-success btn-sm" disabled>Buscar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="cont">
                                </div>

                                </div>
                </td>
            </tr>
                                                ');
            self::scripts();
        }

        private function nuevoRegistroModal(){
            $html = '<!-- Modal Nuevo Registro-->
            <div class="modal fade" id="NuevoRegistro" role="dialog">
              <div class="modal-dialog modal-lg">
              
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal" class="modal-title"><center>Nueva Novedad</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <form id="FormRegist"  action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                      '.utf8_encode($this->camposFormulPrincipal()).'
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                      <button type="button" onclick="validateForm()" id="nom_btnID" class="btn btn-success btn-sm">Crear Novedad</button>
                    </div>
                  </form>
                </div>
                
              </div>
            </div>';
            return $html;
        }

        private function camposFormulPrincipal(){
            $html='<div class="card" style="margin:5px;">
            <div class="card-header color-heading text-center">
              Datos b�sicos de la novedad
            </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-2 col-sm-12">
                <div class="form-group">
                  <input type="hidden" name="ind_update" id="ind_updateID">
                  <input type="hidden" name="cod_noveda" id="cod_novedaID">
                  <label for="cod_novedaID" class="labelinput">C�digo:</label>
                  <input class="form-control form-control-sm" type="text" id="cod_novedaVID" name="cod_novedaV" disabled>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label for="nom_novedaID" class="labelinput">Nombre de la novedad:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Nombre de la novedad" id="nom_novedaID" name="nom_noveda">
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="form-group">
                  <label for="nom_soliciID" class="labelinput">Etapa</label>
                  <select class="form-control form-control-sm req" id="cod_etapaxID" name="cod_etapax" style="width:150px;">
                    '.$this->getEtapax().'
                  </select>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="form-group">
                  <label for="cod_riesgoID" class="labelinput">Riesgo</label>
                  <select class="form-control form-control-sm req" id="cod_riesgoID" name="cod_riesgo" style="width:150px;">
                    '.$this->getRiesgo().'
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-md-12">
                            <img id="previewImagen">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="rut_iconoxID">Icono</label>
                            <input type="file" class="form-control-file" id="rut_iconoxID" name="rut_iconox" accept="image/*" onchange="cargaImagen(this)">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label for="nom_observaID">Observaci�n</label>
                    <textarea class="form-control" id="nom_observaID" name="nom_observa" rows="3"></textarea>
                </div>
            </div>

          ';

          return $html;
        }

        private function getEtapax(){
            $sql = "SELECT a.cod_etapax, a.nom_etapax
                          FROM ".BASE_DATOS.".tab_genera_etapax a
                        WHERE ind_estado = 1 ";
             $query = new Consulta($sql, $this->conexion);
             $mMatriz = $query -> ret_matrix('i');
             return $this->armaSelect($mMatriz);
        }

        private function getRiesgo(){
            $sql = "SELECT a.cod_riesgo, a.nom_riesgo
                          FROM ".BASE_DATOS.".tab_genera_riesgo a
                        WHERE ind_estado = 1; ";
             $query = new Consulta($sql, $this->conexion);
             $mMatriz = $query -> ret_matrix('i');
             return $this->armaSelect($mMatriz);
        }

        private function armaSelect($array){
            $html = '';
            foreach ($array as $dato){
                $html .= '<option value="'.$dato[0].'">'.$dato[1].'</option>';
            }
            return utf8_encode($html);
        }


    }

    new InsNovedadParametrizacion($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php
    /****************************************************************************
	NOMBRE:   AprobarFacturaAsicar
	FUNCION:  Muestra la solicitudes de asistencia en carretera por facturar y facturadas
	FECHA DE MODIFICACION: 06/09/2023
	CREADO POR: Ing. Cristian Andrés Torres 
	MODIFICADO 
	****************************************************************************/
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/
    class AprobarFacturaAsicar
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
              self::principal();
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
                <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_aprofa_asicar.js"></script>
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

            //Body
            echo '<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <div class="container" style="min-width: 98%;">
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
                                                      <label>Tipo de servicio:</label>
                                                    </div>
                                                    <div class="col-2">
                                                      <select id="tipserID" name="tipser" style="width:150px;">
                                                          <option value="">Todos</option>
                                                          '.$this->darTipoDeServicio().'
                                                      </select>
                                                    </div>

                                                </div>
                                                <div class="row mt-3">
                                                  <div class="col-2 text-right">
                                                    <label>Regional</label>
                                                  </div>
                                                  <div class="col-2">
                                                    <select id="regionalID" name="regional" style="width:150px;">
                                                        '.$this->darRegional().'
                                                    </select>
                                                  </div>

                                                    <div class="col-2 text-right">
                                                        <label>Fecha Inicial:</label>
                                                    </div>
                                                    <div class="col-1">
                                                        <input type="date" id="fec_inicio" name="fec_inicio" value="'.$mDateYes.'">
                                                    </div>
                                                    <div class="col-2 text-right">
                                                        <label>Fecha Final:</label>
                                                    </div>
                                                    <div class="col-1">
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
                                                    <a class="btn btn-success btn-sm active" style="background-color:#509334" id="pills-PorAprobar-tab" data-toggle="pill" href="#pills-PorAprobar" role="tab" aria-controls="pills-PorAprobar" aria-selected="true">Por Aprobar</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success btn-sm" style="background-color:#509334" id="pills-Aprobadas-tab" data-toggle="pill" href="#pills-Aprobadas" role="tab" aria-controls="pills-Aprobadas" aria-selected="false">Aprobadas</a>
                                                </li>
                                            </ul>

                                      <div class="tab-content" id="pills-tabContent">
                                        '.$this->vPorAprobar().'
                                        '.$this->vAprobadas().'
                                      </div>

                                        </div>
                                    </div>
                                </div>
                                '.$this->aprobarFacturaModal().'
                </td>
            </tr>
                                                ';
            self::scripts();
        }


        private function darTransportadora(){

            $sql = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_usuari a
            WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND a.cod_filtro = 10 ";
            $query = new Consulta($sql, $this->conexion);
            $mMatriz = $query -> ret_matrix('a');
            if(count($mMatriz)>0){
              $cond = ' AND a.cod_consec = "'.$_SESSION['datos_usuario']['cod_consec'].'"';
            }

            $sql="SELECT b.cod_tercer, b.nom_tercer FROM ".BASE_DATOS.".tab_tercer_emptra a 
                            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer 
                         WHERE 1 = 1 ".$cond."
                  ORDER BY b.nom_tercer ASC";
            $resultado = new Consulta($sql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');

            $html='';

            if(sizeof($resultados) >1 ){
              $html.='<option value="">Todas</option>';
            }

            foreach ($resultados as $registro){
                $html .= '<option value="'.$registro['cod_tercer'].'">'.$registro['nom_tercer'].'</option>';
            }
            return $html;
        }

        private function darTipoDeServicio(){
          $sql="SELECT a.id, a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado = 1";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';
          foreach ($resultados as $registro){
              $html .= '<option value="'.$registro['id'].'">'.$registro['nom_asiste'].'</option>';
          }
          return $html;
        }

        private function darRegional(){
          $sql = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_usuari a
                    WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND a.cod_filtro = 10 ";
          $query = new Consulta($sql, $this->conexion);
          $mMatriz = $query -> ret_matrix('a');
          if(count($mMatriz)>0){
            $cond = ' AND a.cod_region = "'.$mMatriz[0]['clv_filtro'].'"';
          }


          $sql="SELECT a.cod_region, a.nom_region FROM ".BASE_DATOS.".tab_genera_region a WHERE a.ind_estado = 1 ".$cond." ";
          $resultado = new Consulta($sql, $this->conexion);
          $resultados = $resultado->ret_matriz('a');
          $html='';

          if(sizeof($resultados)>1){
            $html.='<option value="">Todas</option>';
          }

          foreach ($resultados as $registro){
              $html .= '<option value="'.$registro['cod_region'].'">'.$registro['nom_region'].'</option>';
          }
          return $html;
        }

        //* FUNCIONES QUE RETORNAN CADA UNA DE LAS VISTAS SEGUN LA PESTAÃ‘A

        private function vPorAprobar(){
            $html='<div class="tab-pane fade show active p-3" id="pills-PorAprobar" role="tabpanel" aria-labelledby="pills-PorAprobar-tab">
            <div id="vPorAprobar" class="panel-collapse" style="overflow: auto;">
              <table class="table table-bordered" id="tabla_inf_PorAprobar">
                  <thead>
                      <tr>
                          <th>#</th> 
                          <th>No. Solicitud</th>
                          <th>Tipo de Servicio</th>
                          <th>Transportadora</th>
                          <th>Regional</th>
                          <th>Nombre del Solicitante</th>
                          <th>Correo del Solicitante</th>
                          <th>Num. Celular</th>
                          <th>Fecha de Solicitud</th>
                          <th>Estado</th>
                          <th>Opciones</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            </div>
        </div>';
        return $html;
        }

        private function vAprobadas(){
          $html='<div class="tab-pane fade p-3" id="pills-Aprobadas" role="tabpanel" aria-labelledby="pills-Aprobadas-tab">
          <div id="vAprobadas" class="panel-collapse" style="overflow: auto;">
            <table class="table table-bordered" id="tabla_inf_Aprobadas">
                <thead>
                    <tr>
                        <th>#</th> 
                        <th>No. Solicitud</th>
                        <th>Tipo de Servicio</th>
                        <th>Transportadora</th>
                        <th>Regional</th>
                        <th>Nombre del Solicitante</th>
                        <th>Correo del Solicitante</th>
                        <th>Num. Celular</th>
                        <th>Fecha de Solicitud</th>
                        <th>Estado</th>
                        <th>Fecha de aprobación</th>
                        <th>Observación</th>
                        <th>Usuario</th>
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
        private function aprobarFacturaModal(){
            $html = '<!-- Modal Aprobar Factura Asicar-->
            <div class="modal fade" id="AprobarFacturaModal" role="dialog">
              <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal-AprobarFacturaModal" class="modal-title"><center>Solicitud #</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <form id="InsSolici"  action="" method="post">
                    <div class="modal-body">
                        <div id="accordionOne">
                        <div class="card">
                            <div class="card-header" id="modalOne" style="text-align: center;">
                                <h5 class="mb-0 btn btn-link">
                                      Datos del Solicitante
                                </h5>
                            </div>
                            <div id="modalOne" class="collapse show" aria-labelledby="modalOne" data-parent="#accordionOne" style="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="nom_transpID" class="labelinput">Transportadora:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Transportadora" id="nom_transpID" disabled value="">
                                          </div>
                                        </div>
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="tip_servicID" class="labelinput">Tipo de Servicio:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Tipo de Servicio" id="tip_servicID" disabled value="">
                                          </div>
                                        </div>
                                      </div>
                    
                                      <div class="row">
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="nom_soliciID" class="labelinput">Nombre del Solicitante:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Nombre del Solicitante" id="nom_soliciID" disabled value="">
                                          </div>
                                        </div>
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="cor_soliciID" class="labelinput">Correo Electrónico:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Correo Electrónico" id="cor_soliciID" disabled value="">
                                          </div>
                                        </div>
                                      </div>
                    
                                      <div class="row">
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="tel_soliciID" class="labelinput">Teléfono:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Teléfono" id="tel_soliciID" disabled value="">
                                          </div>
                                        </div>
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="cel_soliciID" class="labelinput">Celular:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Celular" id="cel_soliciID" disabled value="">
                                          </div>
                                        </div>
                                      </div>
                    
                                      <div class="row">
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="aseguraID" class="labelinput">Aseguradora:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Aseguradora" id="aseguraID" disabled value="">
                                          </div>
                                        </div>
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="polizaID" class="labelinput">N° Poliza:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="No Poliza" id="polizaID" disabled value="">
                                          </div>
                                        </div>
                                      </div>
                    
                                      <div class="row">
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="fec_soliciID" class="labelinput">Fecha de Solicitud:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha de Solicitud" id="fec_soliciID" disabled value="">
                                          </div>
                                        </div>
                                        <div class="col-6">
                                          <div class="form-group">
                                            <label for="fec_finaliID" class="labelinput">Fecha de Finalización:</label>
                                            <input class="form-control form-control-sm" type="text" placeholder="Fecha de finalización" id="fec_finaliID" disabled value="">
                                          </div>
                                        </div>
                                      </div>                                 
                                </div>
                            </div>
                        </div>
                    </div>








                    <div class="mt-2" id="accordionTwo">
                      <div class="card">
                          <div class="card-header" id="modalTwo" style="text-align: center;">
                              <h5 class="mb-0 btn btn-link">
                                    Servicios Solicitados
                              </h5>
                          </div>
                        <div id="modalTwo" class="collapse show" aria-labelledby="modalTwo" data-parent="#accordionTwo" style="">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-12">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #dff0d8;">
                                      <tr>
                                        <th scope="col">N°</th>
                                        <th scope="col">Servicio</th>
                                        <th scope="col">Costo Unitario</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Subtotal</th>
                                      </tr>
                                    </thead>
                                    <tbody id="servicTable">
                                      <tr>
                                        <td colspan="4" style="text-align: right; background-color:#fff">Total</td>
                                        <!-- Calcula el total y colócalo en la siguiente celda -->
                                        <td style="background-color:#fff" id="totalServicRow">$0.0</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-12">
                                    <div class="form-group">
                                      <label for="exampleTextarea">Observación:</label>
                                      <textarea class="form-control" rows="3" name="obs_aproba" id="obs_aprobaID"></textarea>
                                      <input type="hidden" name="num_solici" id="num_soliciID">
                                    </div>
                                  </div>
                                </div>  
                                  
                            </div>
                        </div>
                    </div>
                </div>







                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cancelar</button>
                      <button type="button" onclick="sendAproba()" class="btn btn-success btn-sm">Aprobar</button>
                    </div>
                  </form>
                </div>
                
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

    new AprobarFacturaAsicar($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
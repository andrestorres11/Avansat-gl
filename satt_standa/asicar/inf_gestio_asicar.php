<?php
    /****************************************************************************
	NOMBRE:   DespacEstibas
	FUNCION:  Muestra las estadisticas en control de seguiemiento de los diferentes tipos de asistencia 
	FECHA DE MODIFICACION: 04/06/2020
	CREADO POR: Ing. Cristian Andrés Torres
	MODIFICADO 
	****************************************************************************/
	
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/

    class GetioAsisCar
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
                <link href="../' . DIR_APLICA_CENTRAL . '/css/tableexport.css" rel="stylesheet">
                
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
                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
                
                <!-- jQuery FrameWork -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI2019.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_gestio_asicar.js"></script>
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
                                                    <div class="col-3 text-right">
                                                        <label>Transportadora:</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <select id="cars" name="cars" style="width:150px;">
                                                            <option>Seleccione</option>
                                                            '.$this->darTransportadora().'
                                                        </select>
                                                    </div>
                                                    <div class="col-3 text-right">
                                                        <label>Número de Solicitud:</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <input type="text" id="num_soliciID" name="num_solici">
                                                    </div>
                                                </div>
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
                                                        <button type="button" class="btn btn-success btn-sm"><i class="fas fa-search"></i> Filtrar</button>
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
                                                    <a class="btn btn-success active" style="background-color:#509334" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true">General</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success" style="background-color:#509334" id="pills-porgestion-tab" data-toggle="pill" href="#pills-porgestion" role="tab" aria-controls="pills-porgestion" aria-selected="false">Por Gestionar</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success" style="background-color:#509334" id="pills-PorAsiCliente-tab" data-toggle="pill" href="#pills-PorAsiCliente" role="tab" aria-controls="pills-PorAsiClientet" aria-selected="false">Por Asignar a Cliente</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Asignación a Pro</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">En proceso</a>
                                                </li>
                                                <li class="nav-item m-2">
                                                    <a class="btn btn-success" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Finalizados</a>
                                                </li>
                                            </ul>

                                      <div class="tab-content" id="pills-tabContent">
                                        '.$this->vGeneral().'
                                        '.$this->vPorGestionar().'
                                        '.$this->vPorAsiCliente().'
                                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">...</div>
                                      </div>

                                        </div>
                                    </div>
                                </div> 
                </td>
            </tr>
                                                ');
            
            //Remote scripts
            self::scripts();
        }

        private function darInfoFormulSol(){
            $sql="SELECT a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a ORDER BY a.id ASC";
            $resultado = new Consulta($sql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='';
            foreach ($resultados as $registro){
                $html .= '<th><center>'.$registro['nom_asiste'].'</center></th>';
            }
            return utf8_encode($html);
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

        //* FUNCIONES QUE RETORNAN CADA UNA DE LAS VISTAS SEGUN LA PESTAÑA

        private function vGeneral(){
            $html='<div class="tab-pane fade show active p-3" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
            <table class="table table-bordered" id="tabla_inf_general">
                <thead>
                    <tr>
                        <th colspan="10" style="background-color:#dff0d8; color: #000" id="text_general_fec"><center>INDICADOR DE SOLICITUDES DEL PERIODO AL <center></th> 
                    </tr>
                    <tr>
                        <th>TOTAL GENERADAS</th> 
                        <th>POR GESTIONAR</th>
                        <th>%</th>
                        <th>POR APROBAR CLIENTE</th>
                        <th>%</th>
                        <th>ASIGNACIÓN A PRO</th>
                        <th>%</th>
                        <th>EN PROCESO</th>
                        <th>%</th>
                        <th>FINALIZADAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="resultado_info_general">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered " id="tabla_inf_especifico">
                <thead>
                    <tr>
                        <th colspan="12" style="background-color:#dff0d8; color: #000"><center>DETALLADO POR DIA<center></th>
                    </tr>
                    <tr>
                        <th>CLIENTE</th> 
                        <th>GENERADAS</th>
                        <th>POR GESTIONAR</th>
                        <th>%</th>
                        <th>POR APROBAR CL</th>
                        <th>%</th>
                        <th>ASIGNACIÓN DE PRO</th>
                        <th>%</th>
                        <th>EN PROCESO</th>
                        <th>%</th>
                        <th>FINALIZADAS</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody id="resultado_info_especifico">
        
                </tbody>
            </table>
        </div>';
        return $html;
        }

        private function vPorGestionar(){
            $html='<div class="tab-pane fade p-3" id="pills-porgestion" role="tabpanel" aria-labelledby="pills-profile-tab">
            <table class="table table-bordered" id="tabla_inf_especifico">
                <thead>
                    <tr>
                        <th colspan="12" style="background-color:#dff0d8; color: #000"><center>SERVICIO SOLICITADO<center></th>
                    </tr>
                    <tr>
                        <th>CLIENTE</th> 
                        '.$this->darInfoFormulSol().'
                    </tr>
                </thead>
                <tbody id="resultado_info_especifico">
        
                </tbody>
            </table>
        </div>
            ';
        return $html;
        }

        private function vPorAsiCliente(){
            $html='<div class="tab-pane fade p-3" id="pills-PorAsiCliente" role="tabpanel" aria-labelledby="pills-PorAsiCliente-tab">
            <table class="table table-bordered" id="tabla_inf_especifico">
                <thead>
                    <tr>
                        <th colspan="12" style="background-color:#dff0d8; color: #000"><center>SERVICIO SOLICITADO<center></th>
                    </tr>
                    <tr>
                        <th>CLIENTE</th> 
                        '.$this->darInfoFormulSol().'
                    </tr>
                </thead>
                <tbody id="resultado_info_especifico">
        
                </tbody>
            </table>
        </div>
            ';
        return $html;
        }
    }

    new GetioAsisCar($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
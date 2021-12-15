<?php
    /************************************************/
    /* CREACION DE DAHSBOARD.....                   */
    /************************************************/

    class DashBoard
    {
        
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;

        function __construct($co = null, $us = null, $ca = null)
        {
            self::$conexion = $co;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            /*switch($_REQUEST[opcion])
            {
                case "0":
                    
                break;

                default:
                    self::filtros();
                break;
            }*/

            self::filtros();
        }

        private function styles(){
            echo '
                <!-- Bootstrap -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

                <!-- Font Awesome -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
                
                <!-- Datatables -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

                <!-- DashBoard -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/dashboard.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../' . DIR_APLICA_CENTRAL . '/css/inf_dashbo_finali.css" rel="stylesheet">
            ';
        }

        private function scripts(){
            echo '
                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/jquery/dist/jquery.min.js"></script>
                
                <!-- jQuery FrameWork -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI2019.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                
                <!-- ECharts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/echarts/dist/echarts.min.js"></script>


                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_dashbo_finali.js"></script>
            ';
        }

        private function filtros(){

            //Links css
            self::styles();

            //Body
            echo '
                <div id="finallyGraphics" class="row">
                <div class="panel panel-default">
                    <div class="panel-heading">Filtro de fecha</div>
                    <div class="panel-body graphic">
                        <div class="form-inline" id="formFilter">
                            <div class="input-group" title="Fecha inicial">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="date" id="fec_inicio" class="form-control" id="exampleInputAmount" required>
                            </div>
                            
                            <div class="input-group" title="Fecha final">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="date" id="fec_finxxx" class="form-control" id="exampleInputAmount" required>
                            </div>
                            
                        </div>
                        <button type="button" class="btn btn-success" id="filter" title="Llenar los campos de fecha">Filtrar</button>
                    </div>
                </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Despachos realizados por tipo de operaci&oacute;n</div>
                        <div id="desReaPorTipOpeGraphic" class="panel-body graphic"></div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Informe de uso de veh&iacute;culo por planta</div>
                            <div class="panel-body">
                                <div class="flexWrapRow">
                                    <div class="stats" id="propios">
                                        <div>
                                            <div class="statsCount" style="background-color: #3951af; color: white">
                                                0
                                            </div>
                                            <div class="statsName">
                                                Propios
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stats" id="terceros">
                                        <div>
                                            <div class="statsCount" style="background-color: #fbbb1d; color: white">
                                                0
                                            </div>
                                            <div class="statsName">
                                                Terceros
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stats" id="empresas">
                                        <div>
                                            <div class="statsCount" style="background-color: #6f4fb2; color: white">
                                                0
                                            </div>
                                            <div class="statsName">
                                                Empresas
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stats" id="totalVehiculos">
                                        <div>
                                            <div class="statsCount" style="background-color: #1eaaf9; color: white">
                                                0
                                            </div>
                                            <div class="statsName">
                                                Total Vehiculos
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="infUsoVehPorPlaFinGraphic" class="graphic" style="height: 250px;"></div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="panel panel-default col-md-6 col-sm-12 col-xs-12 col">
                            <div class="panel-heading">Despachos finalizados</div>
                            <div id="desFinGraphic" class="panel-body graphic"></div>
                        </div>
                        <div class="panel panel-default col-md-6 col-sm-12 col-xs-12 col">
                            <div class="panel-heading">Vehiculos Pendientes por llegada</div>
                            <div id="vehiPenLlegGraphic" class="panel-body graphic"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="panel panel-default col-md-4 col-sm-4 col-xs-12 col">
                            <div class="panel-heading">Citas de Cargue</div>
                            <div id="citCarGraphic" class="panel-body graphic" style="height: 150px;"></div>
                        </div>
                        <div class="panel panel-default col-md-4 col-sm-4 col-xs-12 col">
                            <div class="panel-heading">Citas de Descargue</div>
                            <div id="citDesGraphic" class="panel-body graphic" style="height: 150px;"></div>
                        </div>
                        <div class="panel panel-default col-md-4 col-sm-4 col-xs-12 col">
                            <div class="panel-heading">Itinerario</div>
                            <div id="itiGraphic" class="panel-body graphic" style="height: 150px;"></div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Top de Eventos</div>
                        <div id="topEveGraphic" class="panel-body graphic" style="height: 500px;"></div>
                    </div>
                </div>
            ';
            
            //Remote scripts
            self::scripts();
        }
    }






    //new DashBoard($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
    new DashBoard();
?>
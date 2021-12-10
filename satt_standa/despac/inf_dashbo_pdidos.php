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

                <!-- Multiselect -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/multiselect/styles/multiselect.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../' . DIR_APLICA_CENTRAL . '/css/inf_dashbo_pdidos.css" rel="stylesheet" type="text/css"/>
                
                <!-- Easy Zoom -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/lib/plugins/EasyZoom/css/pygments.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/lib/plugins/EasyZoom/css/easyzoom.css" rel="stylesheet">
            ';
        }

        private function scripts(){
            echo '
                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/jquery/dist/jquery.min.js"></script>
                
                <!-- jQuery FrameWork -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI2019.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/multiselect/jquery.multiselect.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                
                <!-- ECharts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/echarts/dist/echarts.min.js"></script>

                <!-- Multiselect -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/multiselect/multiselect.min.js"></script>

                <!-- DOMTOIMAGE -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/domToImage/dom-to-image.js"></script>

                <!--EASYZOOM Zoom imagenes-->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/lib/plugins/EasyZoom/dist/easyzoom.js"></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_dashbo_pdidos.js"></script>
                
            ';
        }

        private function filtros(){
            //Links css
            self::styles();

            //Body
            echo '
                <table width="100%">
                    <tr>
                        <td>
                            <div id="finallyGraphics" class="row">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Filtro</div>
                                    <div class="panel-body graphic">
                                        <div class="form-inline" id="formFirstilter">
                                            <div class="input-group" title="cliente">
                                                <div class="input-group-addon">Cliente</div>
                                                <select id="cliente" multiple class="form-control" required>
                                                </select>
                                            </div>
                                            <div class="input-group" title="Negocios">
                                                <div class="input-group-addon">Productos</div>
                                                <select id="negocios" multiple class="form-control" required>
                                                </select>
                                            </div><br><br> 
                                            <div class="input-group" title="Tipo de Operación">
                                                <div class="input-group-addon">Tipo de Operación</div>
                                                <select id="tipoOperacion" class="form-control" required>
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                        </div><br> 
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="form-inline">
                                                <div class="input-group" title="Origen">
                                                    <div class="input-group-addon">Origen</div>
                                                    <select id="origen" multiple class="form-control" required></select>
                                                </div>
                                                <div class="input-group" title="Destino">
                                                    <div class="input-group-addon">Destino</div>
                                                    <select id="destino" multiple class="form-control" required></select>
                                                </div>
                                            </div>
                                            <br><button type="button" class="btn btn-success" id="filter" title="Llenar los campos de fecha">Filtrar</button>
                                        </div>
                                        <div class="row mt-3">
                                                    <label class="col-sm-20 col-form-label">Pedidos despachados en los ultimos 30 dias</label>
                                        </div>
                                        <div id="rem30DaysLastGraphic" class="panel-body graphic"></div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="form-inline" id="formSecondFilter">
                                                <div class="row">
                                                    <label class="col-sm-20 col-form-label">Consultar el estado de mi Pedido</label>
                                                </div>
                                                <div class="form-control">
                                                    <label class="form-check-label" for="pedido">
                                                        Pedido: 
                                                        <input type="radio" name="pedido-remision" id="pedido" value="pedido"> 
                                                    </label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="search" id="remisionPedido" class="form-control" required>
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-primary" type="button" id="filPedRem">
                                                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Estado de mis pedidos Activos</div>
                                        <div id="estRemGraphic" class="panel-body graphic" style="height: 500px;"></div>
                                    </div>
                                    <div class="row">
                                        <div class="panel panel-default col-md-6 col-sm-12 col-xs-12 col">
                                            <div class="panel-heading">TOP 10 Destinos de Pedidos en los ultimos 30 dias</div>
                                            <div id="topTenDesFreRecCiu" class="panel-body graphic" style="height: 500px;"></div>
                                        </div>
                                        <div class="panel panel-default col-md-6 col-sm-12 col-xs-12 col">
                                            <div class="panel-heading">TOP 10 Configuracion de vehiculo mas usada en los ultimos 30 dias</div>
                                            <div id="topConfiRecu" class="panel-body graphic" style="height: 500px;"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            ';
            
            //Remote scripts
            self::scripts();
        }
    }

    //new DashBoard($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
    new DashBoard();
?>
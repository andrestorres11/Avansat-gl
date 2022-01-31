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
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

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
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/inf_dashbo_dashbo.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/informes.css" rel="stylesheet">
                
            ';
        }

        private function scripts(){

            echo '
                <!-- Get Data -->
                <script>
                    var filterData = ' . ( $_SESSION["dashboard"][1]["filter"] ? 'JSON.parse(\'' . $_SESSION["dashboard"][1]["filter"] . '\'); ' : "null" ) . ' 
                    var tableData = ' . ( $_SESSION["dashboard"][1]["table"] ? 'JSON.parse(\'' . $_SESSION["dashboard"][1]["table"] . '\'); ' : "null" ) . ' 
                    var despacho = "' . ( $_REQUEST["despacho"] && $_REQUEST["despacho"] != "" ? $_REQUEST["despacho"] : null ) . '"
                    var origen = "' . ( $_REQUEST["origen"] && $_REQUEST["origen"] != "" ? $_REQUEST["origen"] : null ) . '"  
                    var viaje = "' . ( $_REQUEST["viaje"] && $_REQUEST["viaje"] != "" ? $_REQUEST["viaje"] : null ) . '"  
                    var destino = "' . ( $_REQUEST["destino"] && $_REQUEST["destino"] != "" ? $_REQUEST["destino"] : null ) . '"  
                    var placa = "' . ( $_REQUEST["placa"] && $_REQUEST["placa"] != "" ? $_REQUEST["placa"] : null ) . '" 
                </script>

                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
                
                <!-- jQuery FrameWork -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI2019.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
                
                <!-- ECharts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/echarts/dist/echarts.min.js"></script>


                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/inf_dashbo_dashbo.js"></script>
            ';
        }

        private function novPorSoluc(){
            $mSql = "
                    SELECT
                            COUNT(b.num_despac) AS cantidad
                      FROM  ".BASE_DATOS.".tab_genera_usuari a
                INNER JOIN  ".BASE_DATOS.".tab_protoc_asigna b
                        ON  a.cod_usuari = b.usr_asigna
                            AND b.ind_ejecuc = 0
                            AND a.cod_perfil = 713
            ";

            $query = new Consulta($mSql, self::$conexion);
            $novedades = $query -> ret_matrix('a');

            return  $novedades[0]['cantidad'];
        }

        private function filtros(){

                        ini_set('display_errors', true);
            error_reporting(E_ALL & ~E_NOTICE);
            //Links css
            self::styles();

            $mDateNOW = date('Y-m-d');
            $mDateTem = date_sub(date_create($mDateNOW), date_interval_create_from_date_string("7 days") );
            
            echo "<pre style='display:none'>"; print_r( $mDateTem ); echo "</pre>"; 
            $mDateYes = date("Y-m-d", strtotime($mDateTem ->date));
            //Body
            echo '
            <table style="width: 100%;" id="dashBoardTableTrans">
                <tr>
                    <td>
                        <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                        <form id="filter">
                            <div class="container" style="min-width: 98%;">
                                <div class="panel panel-default">

                                    <div class="panel-heading" data-toggle="collapse" data-target="#filtrosEspecificos"><h4><b>FILTROS ESPEC&Iacute;FICOS</b></h4></div>
                                    <table class="panel-body collapse" id="filtrosEspecificos">
                                        <tr>

                                            <td class="col-sm-2 nameForm"><label>No. Despacho:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input name="num_despac" id="focusedInput" type="text">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>Placa:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_placax" type="text">
                                            </td>  

                                            <!--<td class="col-sm-2 nameForm"><label>Tiempo:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_tiempo" type="text">
                                            </td>-->

                                            <td class="col-sm-2 nameForm"><label>Celular Conductor:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_telmov" type="text">
                                            </td>

                                        </tr>

                                        <tr>

                                            <td class="col-sm-2 nameForm"><label>No. Viaje:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_viajex" type="text">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>No. Solicitud:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_solici" type="text">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>No. Transporte:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_transp" type="text">
                                            </td>

                                        </tr>

                                        <tr>

                                            <td class="col-sm-2 nameForm"><label>No. Remisi&oacute;n:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="num_remesi" type="text">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>No. Manifiesto:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="cod_manifi" type="text">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>C.C. Conductor:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input id="focusedInput" name="cod_conduc" type="text">
                                            </td>

                                        </tr>

                                        <tr>

                                            <td class="col-sm-2 nameForm"><label>Fecha inicio cargue:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input type="date" id="fec_inicio" name="fec_inicio" required value="'.$mDateYes.'">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>Fecha fin cargue:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input type="date" id="fec_finxxx" name="fec_finxxx" required value="'.$mDateNOW.'">
                                            </td>

                                        </tr>

                                    </table>
                                    <div class="panel-heading" data-toggle="collapse" data-target="#filtrosGenerales"><h4><b>FILTROS GENERALES</b></h4></div>
                                    <table class="panel-body collapse" id="filtrosGenerales">
                                        <tr>

                                            <td class="col-sm-3 nameForm"><label>Empresas:</label></td>
                                            <td class="col-sm-3 fieldForm">
                                                <input id="empresas" name="tip_transp1" type="checkbox" value="2">
                                            </td>

                                            <td class="col-sm-3 nameForm"><label>Flota propia:</label></td>
                                            <td class="col-sm-3 fieldForm">
                                                <input id="flotaPropia" name="tip_transp2" type="checkbox" value="1"
                                            </td>

                                            <td class="col-sm-3 nameForm"><label>Terceros:</label></td>
                                            <td class="col-sm-3 fieldForm">
                                                <input id="terceros" name="tip_transp3" type="checkbox" value="3">
                                            </td>

                                        </tr>

                                        <tr>

                                            <td class="col-sm-2 nameForm"><label>Con itinerario:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input type="radio" id="conItinerario" name="ind_itiner" required value="1">
                                            </td>

                                            <td class="col-sm-2 nameForm"><label>Sin itinerario:</label></td>
                                            <td class="col-sm-2 fieldForm">
                                                <input type="radio" id="sinItinerario" name="ind_itiner" required value="0">
                                            </td>

                                        </tr>

                                    </table>

                                    <div class="panel-heading" data-toggle="collapse" data-target="#tipoDeDespacho"><h4><b>TIPO DE DESPACHO</b></h4></div>
                                    <div class="panel-body collapse" id="tipoDeDespacho"></div>

                                    <div class="panel-heading" data-toggle="collapse" data-target="#listadoDeMoviles"><h4><b>LISTADO DE M&Oacute;VILES <b id="countRowsDataTable"></b></b></h4></div>
                                    <div class="panel-body collapse in" id="listadoDeMoviles">

                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="x_panel">
                                                <div class="x_content">
                                                    <table id="dataTable_DashBoard" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Viaje</th> 
                                                                <th>No. Despacho</th>
                                                                <th>Itinerario</th>
                                                                <th>Reporte</th>
                                                                <th>Etapa</th>
                                                                <th>Tiempo</th>
                                                                <th>Placa</th>
                                                                <th>Operador GPS</th>
                                                                <th>Origen</th>
                                                                <th>Destino</th>
                                                                <th>Estado de Cargue</th>
                                                                <th>Fecha Cargue</th>
                                                                <th>Estado de Descargue</th>
                                                                <th>Fecha Descargue</th>
                                                                <th>Proceso de entrega</th>
                                                                <th>Localizaci&oacute;n</th>
                                                                <th>Fecha Novedad</th>
                                                                <th>Usuario Novedad</th>
                                                                <th>Cumplimiento de Plan de Ruta</th>
                                                                <th>Conductor</th>
                                                                <th>Celular Conductor</th>
                                                                <th>Poseedor</th>
                                                                <th>Ultima Novedad</th>
                                                            </tr>
                                                        </thead>
                                    
                                    
                                                        <tbody>

                                                            
                                                    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="dashBoardDialog dialog">
                            <div>
                                <div class="closeWindow">
                                    <i class="fa fa-times-circle"></i>
                                </div>
                                <div class="dialogTitle">
                                    <div id="viaje" class="primary"></div>
                                    <div id="placa" class="primary"></div>
                                    <div id="ruta">
                                        <span class="primary">Ruta: </span>
                                        <span class="secundary"></span>
                                    </div>
                                    <div id="noDespacho" class="primary"></div>
                                </div>
                                <div class="dialogBody">
                                    <div>
                                        <div id="viaje" class="primary row">
                                            Comportamiento de itinerario
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="x_panel">
                                                    <div class="x_content">
                                    
                                                        <div id="echart_scatter" style="height:350px;"></div>
                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <ul class="nav nav-tabs">
                                            <li class="active cargueEvent"><a data-toggle="tab" href="#cargue">Cargue</a></li>
                                            <li class="descargueEvent"><a data-toggle="tab" href="#descargue">Descargue</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="cargue" class="tab-pane fade in active">
                                                <table class="table table-bordered">
                                                    <thead></thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div id="descargue" class="tab-pane fade">
                                                <table class="table table-bordered">
                                                    <thead></thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <!-- Floating Action Button like Google Material -->
                        <div class="menu pmd-floating-action notif"  role="navigation"> 
                            <a style="background: #f44242;" class="pmd-floating-action-btn btn pmd-btn-fab pmd-btn-raised pmd-ripple-effect btn-primary" data-title="Novedades por Solucionar" href="index.php?window=central&cod_servic=8045&redirec=true"> 
                                <span class="pmd-floating-hidden">Novedades por Solucionar</span>
                                <i style="line-height: 40px; font-weight: bold; font-size: 11pt">'.self::novPorSoluc().'</i> 
                            </a> 
                        </div>
                        <div class="menu pmd-floating-action"  role="navigation"> 
                            <a href="javascript:void(0);" style="background: #2196F3; color: white;" class="pmd-floating-action-btn btn btn-sm pmd-btn-fab pmd-btn-raised pmd-ripple-effect btn-default" id="cargueDialog" data-title="Cargue"> 
                                <span class="pmd-floating-hidden">Cargue</span>
                                <i class="glyphicon glyphicon-save" style="line-height: 17.5px; font-size: 15pt;"></i>
                            </a> 
                            <a href="javascript:void(0);" style="background: #009688; color: white;" class="pmd-floating-action-btn btn btn-sm pmd-btn-fab pmd-btn-raised pmd-ripple-effect btn-default" id="descargueDialog" data-title="Descargue"> 
                                <span class="pmd-floating-hidden">Descargue</span> 
                                <i class="glyphicon glyphicon-open" style="line-height: 17.5px; font-size: 15pt;"></i> 
                            </a> 
                            <a href="javascript:void(0);" style="background: rgb(76, 175, 80); color: white;" class="pmd-floating-action-btn btn btn-sm pmd-btn-fab pmd-btn-raised pmd-ripple-effect btn-default" id="totalDialog" data-title="Operaci&oacute;n"> 
                                <span class="pmd-floating-hidden">Operaci&oacute;n</span> 
                                <i class="glyphicon glyphicon-saved" style="line-height: 17.5px; font-size: 15pt;"></i>
                            </a>
                            <a class="pmd-floating-action-btn btn pmd-btn-fab pmd-btn-raised pmd-ripple-effect btn-primary" data-title="Otros gr&aacute;ficos" href="javascript:void(0);"> 
                                <span class="pmd-floating-hidden">Primary</span>
                                <i class="glyphicon glyphicon-plus" style="line-height: 16px;"></i> 
                            </a> 
                        </div>
                        <div class="cargueDialog dialog">
                            <div>
                                <div class="closeWindow">
                                    <i class="fa fa-times-circle"></i>
                                    <div class="primary">Informaci&oacute;n de citas de cargue <a onclick="exportExcel(\'Citas_de_cargue\')" target="_blank">[Excel]</a></div>
                                </div>
                                <div class="dialogBody">
                                    <div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats totalCitas">
                                            <div>
                                                <div class="statsCount" style="background-color: #3a52ac; color: white" data-color="white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Total citas
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats cumplidas">
                                            <div>
                                                <div class="statsCount" style="background-color: #419645; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Cumplidas
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats porCumplir">
                                            <div>
                                                <div class="statsCount" style="background-color: #f7c81e; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Por cumplir
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats noCumplidas">
                                            <div>
                                                <div class="statsCount" style="background-color: #b33b16; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    No cumplidas
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="row rowGraphics">
                                            <div class="col-md-6 col-sm-12 col-xs-12 siteProgress">
                                                <div class="panel panel-info">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Citas de cargue por centro
                                                    </div>
                                                    <div class="siteProgressView panel-body"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-xs-12 totalPieGraphic">
                                                <div class="panel panel-info">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Proceso de citas por cargue
                                                    </div>
                                                    <div class="pieGraphic panel-body" style="height:273px; width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="descargueDialog dialog">
                            <div>
                                <div class="closeWindow">
                                    <i class="fa fa-times-circle"></i>
                                    <div class="primary">Informaci&oacute;n de citas de descargue <a onclick="exportExcel(\'Citas_de_descargue\')" target="_blank">[Excel]</a></div>
                                </div>
                                <div class="dialogBody">
                                    <div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats totalCitas">
                                            <div>
                                                <div class="statsCount" style="background-color: #3a52ac; color: white" data-color="white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Total citas
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats cumplidas">
                                            <div>
                                                <div class="statsCount" style="background-color: #419645; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Cumplidas
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats porCumplir">
                                            <div>
                                                <div class="statsCount" style="background-color: #f7c81e; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Por cumplir
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6 col-xs-12 stats noCumplidas">
                                            <div>
                                                <div class="statsCount" style="background-color: #b33b16; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    No cumplidas
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="row rowGraphics">
                                            <div class="col-md-6 col-sm-12 col-xs-12 siteProgress">
                                                <div class="panel panel-info">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Citas de descargue por centro
                                                    </div>
                                                    <div class="siteProgressView panel-body"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-xs-12 totalPieGraphic">
                                                <div class="panel panel-info">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Proceso de citas por descargue
                                                    </div>
                                                    <div class="pieGraphic panel-body" style="height:273px; width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="totalDialog dialog">
                            <div>
                                <div class="closeWindow">
                                    <i class="fa fa-times-circle"></i>
                                    <div class="primary">Operaci&oacute;n General <a onclick="exportExcel(\'Operacion_general\')" target="_blank">[Excel]</a></div>
                                </div>
                                <div class="dialogBody">
                                    <div style="display: flex; flex-flow: wrap; justify-content: space-evenly;">
                                        <div class="stats propios">
                                            <div style="padding: 0 15px;">
                                                <div class="statsCount" style="background-color: #3752b2; color: white" data-color="white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Propios
                                                </div>
                                            </div>
                                        </div>
                                        <div class="stats terceros">
                                            <div style="padding: 0 15px;">
                                                <div class="statsCount" style="background-color: #fbbd1d; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Terceros
                                                </div>
                                            </div>
                                        </div>
                                        <div class="stats empresas">
                                            <div style="padding: 0 15px;">
                                                <div class="statsCount" style="background-color: #6f4fb2; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Empresas
                                                </div>
                                            </div>
                                        </div>
                                        <div class="stats totalVehiculos">
                                            <div style="padding: 0 15px;">
                                                <div class="statsCount" style="background-color: #28abf3; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Total Veh&iacute;culos
                                                </div>
                                            </div>
                                        </div>
                                        <div class="stats despachosSinRuta">
                                            <div style="padding: 0 15px;">
                                                <div class="statsCount" style="background-color: #ccc; color: white">
                                                    
                                                </div>
                                                <div class="statsName">
                                                    Despachos sin ruta
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="row rowGraphics">
                                            <div class="col-md-6 col-sm-12 col-xs-12 row siteProgress">
                                                <div class="panel panel-info col-md-6 col-sm-12 col-xs-12">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Origenes frecuentes
                                                    </div>
                                                    <div class="origenesFrecuentesPercentageView panel-body"></div>
                                                </div>
                                                <div class="panel panel-info col-md-6 col-sm-12 col-xs-12">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Destinos frecuentes
                                                    </div>
                                                    <div class="destinosFrecuentesPercentageView panel-body"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 col-xs-12 totalPieGraphic">
                                                <div class="panel row" style="height:175px; margin-bottom: 0; border-color: #bce8f1">
                                                    <div class="panel-info col-md-7 col-sm-7 col-xs-7" style="padding: 0px;">
                                                        <div class="primary panel-heading" style="text-align: center;">
                                                            Informe de entregas
                                                        </div>
                                                        <div class="pieGraphic panel-body" style="height:150px; width: 100%;"></div>
                                                    </div>
                                                    <div class="panel-info col-md-5 col-sm-5 col-xs-5" style="padding: 0px;">
                                                        <div class="primary panel-heading" style="text-align: center;">
                                                            Despachos GPS
                                                        </div>
                                                        <div class="gpsPieGraphics panel-body" style="height:150px; width: 100%;"></div>
                                                    </div>
                                                </div>
                                                <div class="panel panel-info" style="height:175px; margin-bottom: 0;">
                                                    <div class="primary panel-heading" style="text-align: center;">
                                                        Eventos
                                                    </div>
                                                    <div class="eventosPercentageView panel-body" style="height:150px; width: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
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
    new DashBoard($this -> conexion);
?>
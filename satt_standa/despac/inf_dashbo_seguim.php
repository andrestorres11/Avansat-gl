<?php


    class DashBoard_Seguim
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
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

            <!-- DashBoard -->
            <link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/bootstrap.css" type="text/css">

            <!-- Multiselect -->
            <link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/jquery.css" type="text/css">
            <link rel="stylesheet" href="../'. DIR_APLICA_CENTRAL . '/estilos/multiselect/jquery.multiselect.css" type="text/css">
            <link rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/estilos/multiselect/jquery.multiselect.filter.css" type="text/css">
            

            <!-- Custom Theme Style -->
            
            <!-- Easy Zoom -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/lib/plugins/EasyZoom/css/pygments.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/lib/plugins/EasyZoom/css/easyzoom.css" rel="stylesheet">
            <style>
            .ui-widget-header {
                border: 1px solid rgb(204 204 204) !important;
                background: #999 !important; 
            }
            .ui-widget-content {
                border-color: rgb(204 204 204) !important;
                background: #999 !important;
            }
            .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
                
                border-color: rgb(204 204 204) !important;
                background: #999 !important;
            }
            .ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus {
                border: 1px solid #333 !important;
                background: #999 !important;
            }
            .ui-multiselect-filter{
                color:black;
            }
            hr {
                
                margin-left: 16px !important;
                margin-right: 16px !important;
                margin-bottom: 20px !important;
                border-top: 3px solid #333333 !important;
            }
            </style>
            ';
        }

        private function scripts(){

            echo '
                
                <!-- jQuery -->
                <script language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/jquery.js"></script>
                
                <!-- jQuery FrameWork -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI2019.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/multiselect/jquery.multiselect.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
                <script src= "../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
                <script src= "../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
                
                <!-- Apex Chart -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/apexcharts/lib_apex.js"></script>

                <!-- Multiselect -->
                <script language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/multiselect/jquery.multiselect.filter.min.js"></script>
                <script language="JavaScript" src="../'.DIR_APLICA_CENTRAL.'/js/multiselect/jquery.multiselect.min.js"></script>

                <!-- DOMTOIMAGE -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/vendors/domToImage/dom-to-image.js"></script>

                <!--EASYZOOM Zoom imagenes-->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/lib/plugins/EasyZoom/dist/easyzoom.js"></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashbo_libxxx/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/inf_dashbo_seguim.js"></script>
            ';
        }

        private function filtros()
        {
            self::styles();
            //Remote scripts
            echo '
            <table style="width: 100%;" id="dashBoardTableTrans">
                <tr>
                    <td>
                        <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                        <form id="filter">
                            <div class="container" style="min-width: 98%;">
                                <div class="panel panel-default">
                                    <div class="panel-heading" data-toggle="collapse" data-target="#filtros" aria-expanded="true"><h6><b>FILTROS GENERALES</b></h6></div>
                                    <table class="panel-body collapse in" id="filtros" aria-expanded="true">
                                        <div class="row">
                                            <tr>
                                                <td class="col-sm-2 fieldForm">
                                                    <input type="hidden" name="standa" id="standaID" value="'.DIR_APLICA_CENTRAL.'">
                                                </td>
                                                <td class="col-sm-2 nameForm"><label>Transportadoras:</label></td>
                                                <td class="col-sm-2 fieldForm">
                                                    <select name="cod_transp" id="cod_transpID" class="form-control form-control-sm" style="width: 60%;">
                                                        <option value="">--Seleccione--</option>
                                                        '.$this->getTransport().'
                                                    </select>
                                                </td>
                                                <td class="col-sm-2 fieldForm">
                                                </td>
                                            </tr>
                                        </div>
                                    </table>
                                    <div class="panel-heading" data-toggle="collapse" data-target="#filtros3" aria-expanded="true" style="height: 32px;"><h6><b>TIPOS DE SERVICIO</b></h6></div>
                                    <table class="panel-body collapse in" id="filtros3" aria-expanded="true">
                                        <div class="row">
                                            <tr>
                                                <td class="col-sm-2 fieldForm">
                                                </td>';
                                                foreach($this->getTipServ() as $value){
                                                    echo '<td class="col-sm-2 fieldForm">'.$value[1].'</td>';
                                                    echo '<td ><input type="checkbox" class="tipserv" value="1" id="tip_servic'.$value[0].'"></td>';
                                                }
                                           echo '<td class="col-sm-2 fieldForm">
                                                </td>
                                            </tr>
                                        </div>
                                    </table>
                                    <div class="panel-heading" data-toggle="collapse" data-target="#filtros2" aria-expanded="true"><h6><b>FILTROS ESPECIFICOS</b></h6></div>
                                    <table class="panel-body collapse in" id="filtros2" aria-expanded="true">
                                            <tr>
                                                <td class="col-sm-2 nameForm">
                                                    <ul class="nav nav-pills">
                                                        <li class="active">
                                                            <a data-toggle="tab" id="liGenera" href="#tabs-1" tipo="gen">Informe general</a>
                                                        </li>
                                                        <li class="active">
                                                            <a data-toggle="tab" id="liNov" href="#tabs-2" tipo="nov">Informe Novedad</a>
                                                        </li>
                                                    </ul>
                                                    <div class="col-md-12 ui-tabs-panel ui-widget-content ui-corner-bottom" style="background:white !important;" id="GeneraID" >
                                                            <div class="row">
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:300px; width:400px">
                                                                        <div id="Graphic1" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0">
                                                                    <div  style="position: relative; height:300px; width:300px">
                                                                        <div id="Graphic2" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <hr>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:300px; width:300px">
                                                                        <div id="Graphic3" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0">
                                                                    <div  style="position: relative; height:300px; width:300px">
                                                                        <div id="Graphic4" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12" >
                                                                <hr>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:350px; width:400px">
                                                                        <div id="Graphic5" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:350px; width:400px">
                                                                        <div id="Graphic6" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <hr>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:350px; width:400px">
                                                                        <div id="Graphic7" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:350px; width:400px">
                                                                        <div id="Graphic8" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <hr>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:300px; width:300px">
                                                                        <div id="Graphic9" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div  style="position: relative; height:300px; width:300px">
                                                                        <div id="Graphic10" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <hr>
                                                                </div>
                                                                <div class="col-md-12" style="padding:0;margin-left: 31%;">
                                                                    <div  style="position: relative; height:300px; width:300px">
                                                                        <div id="Graphic11" class="panel-body graphic" ></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-12 ui-tabs-panel ui-widget-content ui-corner-bottom" id="NovID" style="background:white !important;padding:0;" >
                                                            <div class="row">
                                                                <div class="col-md-12" style="padding:0">
                                                                    <div  style="position: relative; height:300px; width:100%">
                                                                        <div id="Graphic12" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12" style="padding:0">
                                                                    <hr>
                                                                </div>
                                                                <div class="col-md-12" style="padding:0">
                                                                    <div  style="position: relative; height:300px; width:100%">
                                                                        <div id="Graphic13" class="panel-body graphic"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col-md-12" style="padding:0">
                                                                    <h6 style="color: black;text-align: center;font-size: medium;">DESPACHOS CON NOVEDADES ESPECIALES SIN SOLUCCIONAR</h6>
                                                                    <iframe src="" id="iframe1" style="width: 100%;height: 350px;margin-left: 40px;" frameborder="0" >
                                                                    </iframe>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr>
                                                                </div>
                                                                <div class="col-md-12" style="padding:0">
                                                                    <h6 style="color: black;text-align: center;font-size: medium;">DESPACHOS SIN GESTIÓN</h6>
                                                                    <iframe src="" id="iframe2" style="width: 100%;height: 350px;margin-left: 40px;" frameborder="0" >
                                                                    </iframe>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </td>
                                            </tr>
                                    </table>
                                </div>
                            </div>    
                        </form>
                    </td> 
                </tr>
            </table>';
            self::scripts();
        }

        private function getTransport(){
            $mSql=" SELECT 
                        b.cod_tercer, 
                        b.nom_tercer 
                    FROM 
                        ".BASE_DATOS.".tab_tercer_emptra a 
                    INNER JOIN tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer 
                    AND b.cod_estado = 1
                    ORDER BY b.nom_tercer ASC
                    ;
            ";
            $mConsult = new Consulta($mSql, self::$conexion);
            $mResult = $mConsult->ret_matriz('a');
            $select='';
            foreach($mResult as $value){
                $select.='<option value="'.$value['cod_tercer'].'">'.$value['nom_tercer'].'</option>';
            }
            return $this->utf8_converter($select);
        }

        function utf8_converter($array)
        {
            array_walk_recursive($array, function(&$item, $key){
                if(!mb_detect_encoding($item, 'utf-8', true)){
                        $item = utf8_encode($item);
                }
            });
            return $array;
        }

        public function getTipServ()
        {
            $mSql = "SELECT a.cod_tipser, a.nom_tipser
                       FROM ".BASE_DATOS.".tab_genera_tipser a 
                      WHERE a.ind_estado = '1'
                        ";
            $consulta = new Consulta( $mSql, self::$conexion );
            return $mResult = $consulta -> ret_matrix('i');
        }
        

    }
    new DashBoard_Seguim($this -> conexion);
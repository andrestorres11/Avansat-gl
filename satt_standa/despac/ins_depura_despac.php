<?php
    /****************************************************************************
	NOMBRE:   Ins_depura_despac
	FUNCION:  Permite al usuario dar llegada a los despachos de una empresa
	FECHA DE MODIFICACION: 02/06/2021
	CREADO POR: Ing. Carlos Nieto
	MODIFICADO 
	****************************************************************************/
	
	/* ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1); */

    class InsDepuraDespacho
    {
        
        var $conexion,
        $cod_aplica,
        $usuario;

        function __construct($co, $us, $ca){
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;
            $this -> vista();
        }

        private function styles(){
            echo '
                <!-- Bootstrap -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">

                <!-- Datatables -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

                <!-- Float Menu -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

                <!-- Font Awesome -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">

                
            ';
        }

        private function scripts(){

            echo '
                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery-ui-1.12.1/jquery.blockUI.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.js"></script>
                
                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- Datatables -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
                <script src= "../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
                <script src= "../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>

                <!-- SweetAlert -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>


                <!-- Form validate -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validator.js"></script>


                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_depura_despac.js"></script>
            ';
        }

        private function vista(){
            //Links css
            self::styles();
    
            //Body
            echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
                <tr>
                    <td>
                        <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <div id="accordion" style="margin: 13px;">
                                <div class="card">
                                    <div class="card-header" id="headingOne">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Generar Depuración
                                        </button>
                                        </h5>
                                    </div>
                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                        <div class="card-body text-center">
                                            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#MoDepuracion">
                                                Depurar Empresa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div id="accordion" style="margin: 13px;">
                                <div class="card">
                                    <div class="card-header" id="headingTwo">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                            Generar informe de Depuración
                                        </button>
                                        </h5>
                                    </div>
                                    <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                                        <div class="card-body text-center">
                                        <form id="busquedaFiltro" method="POST">
                                        <div class="card">
                                            <div style="margin:8px">
                                                <div class="row text-center">
                                                    <div class="col-md-6">
                                                        <label for="transportadoras" style="margin-left:-20px"><b>Transportadoras:</b></label>
                                                        <select name="transportadoras" id="transportadoras" style="width:127px">
                                                        <option>Seleccione</option>
                                                                    '.$this->darTransportadora().'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row text-center">
                                                    <div class="col-md-6">
                                                        <label for="FechaInicial"><b>Fecha Inicial:</b></label>
                                                        <input type="date" id="FechaInicial" name="FechaInicial">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="FechaFinal"><b>Fecha Final:</b></label>
                                                        <input type="date" id="FechaFinal" name="FechaFinal">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" style="margin: 13px;">
                                <div class="card-header" id="headingTwo">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" onclick="generarReporte()">
                                            Generar Informe
                                        </button>
                                    </h5>
                                </div>
                                <div class="card-body text-center">    
                                    <div id="contenedor"> 
                                        <div class="panel-group" id="accordion">
                                            <div class="panel panel-default">
                                            <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                                                <div class="panel-body">
                                                <table id="tablaRegistros" class="table table-striped table-bordered" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th>N°</th>
                                                        <th>Nit</th>
                                                        <th>Empresa</th>
                                                        <th>Fecha de Depuración</th>
                                                        <th>N° Despachos Depurados</th>
                                                        <th>Usuario</th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                                </div>
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

            <!-- modal Depuracion-->

            <div class="modal fade bd-example-modal-lg" id="MoDepuracion" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLongTitle">Nueva Depuración</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <form id="filter" method="POST">
                            <div class="row text-center">
                                <div class="col-md-12">
                                    <select  id="empresa" name="empresa" style="width:300px;" onchange="getNombre()">
                                        <option disabled selected>Seleccione la Empresa</option>
                                        '.$this->darTransportadora().'
                                    </select>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-10" style="background:#c5dcb7;border: 1px solid;margin:10px;padding:-23px">
                                    <h5><b>Parametrizacion de depuración Automatica</b></h5>
                                </div>
                                <div class="col-md-1" >
                                </div>
                            </div>
                            <div style="margin:8px">
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <div style="margin-left:60px">
                                            <label for="fechaHasta" style="font-weight: normal">Despachos con salida hasta:</label>
                                            <input type="date" id="fechaHasta" name="fechaHasta">                           
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <input type="checkbox" id="ruta" name="ruta" style="font-weight: normal">
                                        <label for="ruta" style="font-weight: normal">Aplica a despachos en ruta.</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="checkbox" id="despachos" name="despachos">
                                        <label for="despachos" style="font-weight: normal">Aplica a despachos pendientes por llegada.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <p style="margin-left:62px;">Observación:</p>
                                <div class="col-md-12 text-center">
                                    <p><textarea name="descripcion" rows="4" cols="108"></textarea></p>
                                </div>
                            </div>
                            </div>
                            <div class="text-center" style="margin-bottom:30px;">
                                <button type="button" onclick="delReg()" class="btn"  style="background:#bfd6a8"><b>Aceptar</b></button>
                                <button type="button" class="btn" style="background:#ad1918;color: white" data-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Modal Informe-->
            <div class="modal fade bd-example-modal-lg" id="detalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">       
                        <h4 class="modal-title" id="modalInformeTitulo">Despachos depurados empresa de transporte </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <div class="modal-body">
                    <div class="card-body text-center">    
                        <div id="contenedor"> 
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                                    <div class="panel-body">
                                    <table id="tablaModal" class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Despacho</th>
                                            <th>Manifiesto</th>
                                            <th>Viaje</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Transportadora</th>
                                            <th>Placa</th>
                                            <th>Conductor</th>
                                            <th>Celular</th>
                                            <th>Generador</th>
                                            <th>Ult. Novedad</th>
                                            <th>Observación</th>
                                            <th>Fecha Ult. Novedad</th>
                                            <th>Observacion Llegada</th>
                                            <th>Fecha Llegada</th>
                                            <th>Usuario</th>
                                        </tr>
                                        </thead>
                                    </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" style="background:#bfd6a8;" data-dismiss="modal">Cerrar</button>
                </div>
                </div>
            </div>
            </div>');
            
            //Remote scripts
            self::scripts();
        }

        private function darTransportadora(){
            $sql="SELECT b.cod_tercer, b.nom_tercer, b.abr_tercer FROM ".BASE_DATOS.".tab_tercer_emptra a INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer ORDER BY b.nom_tercer ASC";
            $resultado = new Consulta($sql, $this->conexion);
            $resultados = $resultado->ret_matriz('a');
            $html='';
            foreach ($resultados as $registro){
                $html .= '<option value="'.$registro['cod_tercer'].'">'.$registro['abr_tercer'].'</option>';
            }
            return utf8_encode($html);
        }

    }

   

    new InsDepuraDespacho($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>    
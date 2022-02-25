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

    class InfoDashboardAsiscar
    {
        
         //Create necessary variables
         static private $conexion = null;
         static private $cod_aplica = null;
         static private $usuario = null;
         static private $dates = array();

        function __construct($co = null, $us = null, $ca = null){
             //Include Connection class
             @include_once( "../lib/ajax.inc" );
             @include_once( "../lib/general/constantes.inc" );
             @include_once( "../lib/general/functions.inc" );
             @include_once( "../../satt_faro/constantes.inc" );
 
             self::$conexion = $AjaxConnection;
             self::$usuario = $us;
             self::$cod_aplica = $ca;
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
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.min.css" rel="stylesheet">

                <!-- Font Awesome -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
                
                <!-- Datatables -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/datatable/datatables.min.css" rel="stylesheet">
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/datatable/buttons.dataTables.min.css" rel="stylesheet">

                <!-- Chart -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/chart/Chart.css" rel="stylesheet">

                <!-- SweetAlert -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/sweetAlert/sweetalert2.min.css" rel="stylesheet">
                
                <!-- Admin Lte -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/AdminLTE/css/adminlte.min.css" rel="stylesheet">

                <!-- Map Styles -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/estilos/ol.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../../' . DIR_APLICA_CENTRAL . '/estilos/sty_panelx_asiste.css" rel="stylesheet">

                <!-- Ionicons -->
                <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/css/ol.css" type="text/css">
                
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
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/moment/moment.js"></script>

                <!-- jQuery -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.6.0/umd/popper.min.js" integrity="sha512-BmM0/BQlqh02wuK5Gz9yrbe7VyIVwOzD1o40yi1IsTjriX/NGF37NyXHfmFzIlMmoSIBXgqDiG1VNU6kB5dBbA==" crossorigin="anonymous"></script>

                <!-- Bootstrap -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.min.js"></script>

                <!-- font awesome -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/js/all.min.js"></script>

                <!-- chart-->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/chart/Chart.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/chart/Chart.bundle.min.js"></script>

                <!-- SweetAlert -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>
                
                <!-- Datatables -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/datatables.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/dataTables.buttons.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/jszip.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/pdfmake.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/vfs_fonts.js"></script>

                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/buttons.html5.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/buttons.print.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatable/vfs_fonts.js"></script>

                <!-- Admin LTE -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/AdminLTE/js/adminlte.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/AdminLTE/plugins/jquery-knob/jquery.knob.min.js"></script>
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/AdminLTE/plugins/sparklines/sparkline.js"></script>

                <!---Map Script -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/ol.js"></script>

                <!-- Custom Scripts -->
                <script src="../../' . DIR_APLICA_CENTRAL . '/js/inf_dashbo_asicar.js"></script>
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

            //Informacion Básica de la asistencia
            $inf_basica = self::darInformacion($_REQUEST['cod_solici']);
            $rutas = self::getDataRutasServicios($_REQUEST['cod_solici']);

            $title = mb_strtoupper($inf_basica['nom_asiste']);
            $title = str_replace("?", "?", $title);

            $html = '
            <div class="row m-3">
              <div class="col-md-12 text-center"><h3>INFORME DE '. $title .'</h3></div>
            </div>
            <div class="row m-3">
            <div class="col-md-3">
              <div class="small-box bg-info">
                <div class="inner">
                  <h6>Datos de la solicitud</h6>
                  <center>
                    <div class="border border-white" style="display:inline-block; padding:10px;margin:5px;margin-bottom:10px">
                      <h5>'.$_REQUEST['cod_solici'].'
                        <h5>
                          <p class="textpanel" style="margin-top:-8px">No. Solicitud</p>
                        </div>
                      </center>
                      <p class="textpanel boldtext">Fecha de solcitud: '.$inf_basica['fec_creaci'].'</p>
                      <p class="textpanel boldtext">Tipo de solicitud: '.$inf_basica['nom_asiste'].'</p>
                      <p class="textpanel boldtext">Cliente: '.$inf_basica['nom_client'].'</p>
                    </div>
                    <div class="icon">
                      <i style="font-size:100px" class="ion ion-android-clipboard"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h6>Datos del solicitante</h6>
                      <p class="textpanel boldtext">Nombre: '.$inf_basica['nom_solici'].'</p>
                      <p class="textpanel boldtext">E-mail: '.$inf_basica['cor_solici'].'</p>
                      <p class="textpanel boldtext">Telefono: '.$inf_basica['tel_solici'].'</p>
                      <p class="textpanel boldtext">N?mero de celular: '.$inf_basica['cel_solici'].'</p>
                      <p class="textpanel boldtext">Aseguradora: '.$inf_basica['ase_solici'].'</p>
                      <p class="textpanel boldtext">Poliza: '.$inf_basica['num_poliza'].'</p>
                    </div>
                    <div class="icon">
                      <i style="font-size:100px" class="ion ion ion-person-add"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="small-box bg-warning">
                    <div class="inner">
                      <h6>Datos del transportista</h6>
                      <p class="textpanel boldtext">No documento: '.$inf_basica['num_transp'].'</p>
                      <p class="textpanel boldtext">Nombres: '.$inf_basica['nom_transp'].'</p>
                      <p class="textpanel boldtext">Primer apellido: '.$inf_basica['ap1_transp'].'</p>
                      <p class="textpanel boldtext">Segundo apellido: '.$inf_basica['ap2_transp'].'</p>
                      <p class="textpanel boldtext">No. Celular 1: '.$inf_basica['ce1_transp'].'</p>
                      <p class="textpanel boldtext">No. Celular 2: '.$inf_basica['ce2_transp'].'</p>
                    </div>
                    <div class="icon">
                      <i style="font-size:100px" class="ion ion-ios-person"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h6>Dato del vehiculo</h6>
                      <center>
                        <div class="border border-white" style="display:inline-block; padding:10px;margin:5px;margin-bottom:10px">
                          <h5> '.$inf_basica['num_placax'].'
                            <h5>
                              <p class="textpanel" style="margin-top:-8px">Placa</p>
                            </div>
                            
                              </center>
                              <p class="textpanel boldtext">Marca: '.$inf_basica['mar_vehicu'].'</p>
                              <p class="textpanel boldtext">Color: '.$inf_basica['col_vehicu'].'</p>
                              <p class="textpanel boldtext">Tipo vehiculo: '.$inf_basica['col_vehicu'].'</p>
                              <p class="textpanel boldtext">Remolque: '.$inf_basica['num_remolq'].'</p>
                            </div>
                            <div class="icon">
                              <i style="font-size:100px" class="ion ion-android-car"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row m-3">
                        <div class="col-md-7 p-2 bg-white">
                          <div class="row">
                            <div class="col-md-12">
                              <input type="hidden" value="'.base64_encode(json_encode($rutas)).'" name="dat_gpsxxx" id="dat_gpsxxx">
                              <div id="map" class="map border p-2"></div>
                                            <div id="popupContenedor" class="ol-popup">
                                                <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                                                <div id="popupContent"></div>
                                            </div>
                            </div>
                          </div>
                          <div class="row mt-3">
                            <div class="col-md-12">
                              <table class="table table-hover table-border datatable">
                                <thead>
                                  <tr class="columns-header">
                                    <th scope="col" class="tablatraza">No.</th>
                                    <th scope="col" class="tablatraza">Fecha</th>
                                    <th scope="col" class="tablatraza">Hora</th>
                                    <th scope="col" class="tablatraza">Latitud</th>
                                    <th scope="col" class="tablatraza">Logitud</th>
                                    <th scope="col" class="tablatraza">Formulario ejecutado</th>
                                    <th scope="col" class="tablatraza">Usuario</th>
                                  </tr>
                                </thead>
                                <tbody>
                                ';
                                $cont=1;
                                foreach($rutas as $ruta){
                                  
                                  $html.='<tr>
													                  <th scope="row" class="centrado tablatraza">'.$cont.'</th>
													                  <td class="tablatraza">'.$ruta['fec_ubicac'].'</td>
													                  <td class="tablatraza">'.$ruta['hor_ubicac'].'</td>
													                  <td class="tablatraza">'.$ruta['val_latitu'].'</td>
													                  <td class="tablatraza">'.$ruta['val_longit'].'</td>
													                  <td class="tablatraza">'.$ruta['nom_formul'].'</td>
													                  <td class="tablatraza">'.$ruta['usr_ubicac'].'</td>
                                          </tr>';
                                  $cont++;
                                }

                             $html.='</tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-5">
                          <div class="row">
                            <div class="col-md-12">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card">
                                    <div class="card-header bg-success">
                                      <center>
                                        <h3 class="card-title">Ubicación del vehículo</h3>
                                      </center>
                                      <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                          <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                          <i class="fas fa-times"></i>
                                        </button>
                                      </div>
                                    </div>';

            if($inf_basica['tip_solici']==1){
              $html.='<div class="card-body p-0">
                        <div class="row mt-2">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Url Operador GPS: </p>
                          </div>
                          <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['url_opegps'].'</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Operador GPS: </p>
                          </div>
                          <div class="col-md-6">
                            <p class="align-left textpanel boldtext">'.$inf_basica['nom_opegps'].'</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Usuario: </p>
                          </div>
                          <div class="col-md-6">
                            <p class="align-left textpanel boldtext">'.$inf_basica['nom_usuari'].'</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Contraseña: </p>
                          </div>
                          <div class="col-md-6">
                            <p class="align-left textpanel boldtext">'.$inf_basica['con_vehicu'].'</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Ubicación: </p>
                          </div>
                          <div class="col-md-6">
                            <p class="align-left textpanel boldtext">'.$inf_basica['ubi_vehicu'].'</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Punto de referencia: </p>
                          </div>
                          <div class="col-md-6">
                            <p class="align-left textpanel boldtext">'.$inf_basica['pun_refere'].'</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <p class="align-right textpanel boldtext">Observaciones:</p>
                          </div>
                          <div class="col-md-6">
                            <p class="align-left textpanel boldtext">'.$inf_basica['des_asiste'].'</p>
                          </div>
                        </div>
                      </div>';
            }else{
              $html.='<div class="row mt-2">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Fecha Servicio:</p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['fec_servic'].'</p>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Ciudad de Origen:</p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['cod_ciuori'].' - '.$inf_basica['nom_ciuori'].'</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Direccion: </p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['dir_ciuori'].'</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Ciudad Destino: </p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['cod_ciudes'].' - '.$inf_basica['nom_ciudes'].'</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Ubicación: </p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['ubi_vehicu'].'</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Direcci?n: </p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['dir_ciudes'].'</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <p class="align-right textpanel boldtext">Observaciones: </p>
                        </div>
                        <div class="col-md-6">
                          <p class="align-left textpanel boldtext">'.$inf_basica['obs_acompa'].'</p>
                        </div>
                      </div>
                  </div>';
            }


            $html.='</div>
            <!---Cierre card --->';
            
            $servicios = self::darInformacionServicios($_REQUEST['cod_solici']);

            $html.='<div class="col-md-12">
            <div class="card">
              <div class="card-header bg-warning">
                <center>
                  <h3 class="card-title">Servicios Solicitados</h3>
                </center>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              
              <!-- /.card-header -->
              <div class="card-body p-0">
                <table class="m-2" style="width:95%;">
                  <thead class="bg-warning">
                    <tr>
                      <th class="text-center tablaval ">Servicio</th>
                      <th class="text-center tablaval ">Costo Unitario</th>
                      <th class="text-center tablaval ">Cantidad</th>
                      <th class="text-center tablaval ">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>';

                    $total=0;

                  foreach($servicios as $servicio){
                      $total += $servicio['val_servic'];

                      $html.='<tr>
                      <td class="tablaval ">'.self::reemplazar(utf8_decode($servicio['des_servic'])).'</td>';

                      if($servicio['tar_unitar']==0){
                      $html.='<td class="tablaval ">$ '.$servicio['val_servic'].'</td>';
                        }else{
                      $html.='<td class="tablaval ">$ '.$servicio['tar_unitar'].'</td>';
                      }
                      $html.='<td class="text-center tablaval ">'.$servicio['can_servic'].'</td>
                      <td class="tablaval ">$ '.$servicio['val_servic'].'</td>
                    </tr>';
                  }
                  $html.='
                    <tr>
                      <td colspan="3" class=" tablaval align-right">Total</td>
                      <td class="tablaval ">$ '.$total.'</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!---Cierre tab servicios-->
            </div>
          </div>
          <!---Cierre Card servicios--->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>       ';




          foreach($servicios as $servicio){
            $html.='<div class="row m-1">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header bg-info">
								<center>
									<h3 class="card-title">'.htmlentities(utf8_decode($servicio['des_servic'])).'</h3>
								</center>
								<div class="card-tools">
									<button type="button" class="btn btn-tool" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
									</button>
									<button type="button" class="btn btn-tool" data-card-widget="remove">
										<i class="fas fa-times"></i>
									</button>
								</div> </div>
              <div class="card-body"><div class="row">';
                    $res_servic = self::getDataFormulario($servicio['id'],1);
                    $tot_servic = self::getDataFormulario($servicio['id'],2);
                    $res_servic1 = self::getDataFormulariow($_REQUEST['cod_solici'], $servicio['id'],1);
                    $tot_servic1 = self::getDataFormulariow($_REQUEST['cod_solici'], $servicio['id'],2);
                    $rut_imagen = DIREC_APLICA."gesdoc/tab_formul_respue/";
                    $rut_imagen2 = "/ap/satt_faro/files/asicar/"; 
                    if($tot_servic<=0 && $tot_servic1 <=0){
                      $html.='<div class="col-md-12">
                      <div class=" m-3 alert alert-warning" role="alert">
                        No hay información registrada de este servicio.
                      </div>
                    </div>';
                    }               
                      if($tot_servic>0){
                        foreach($res_servic as $data1){
                          if($data1['ind_tipoxx']=='camera'){
                            $datos=$data1['tex_respue'];
                            $arr=json_decode($datos,TRUE);
                            foreach($arr as $image){
                              $html.='<div class="col-md-4 p-2 border border-dark">
                                <a href="'.$rut_imagen.''.$image.'"><img src="'.$rut_imagen.''.$image.'" class="mw-100"></a>
                              </div>';
                            }
                          }
                        }
                      }
                      $count=0;
                      if($tot_servic1>0){
                        foreach($res_servic1 as $image){
                          $html.='<div class="col-md-4 p-2 border border-dark">
                            <a href="'.$rut_imagen2.''.$image['nam_archi1'].'" target="_blank"><img src="'.$rut_imagen2.''.$image['nam_archi1'].'" class="mw-100"></a>
                          </div>';
                          $count++;
                        }
                      }
                      $restot = $tot_servic + $count;                      
              $html.='</div></div>
										  <hr>
                      <div class="row">';
											$html.='<div class="col-md-4">
												<div class="row">
												<div class="col-md-6 text-right"><span style="font-weight:bold;">Total:</span></div>
												<div class="col-md-6 text-left">'.$restot.'</div>
												</div>
											</div>';
							$html.='</div>';
            
							$html.='</div>
						</div>
						</div>
            </div>
            </div>';
          }


          $html.='<div class="row mt-3 mb-3">
					          <div class="col-md-12">
            			    <center><input type="button" name="regresar" value="Regresar" onclick="history.go(-1);" class="oculto-impresion">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="imprimir" value="Imprimir" onclick="window.print();" class="oculto-impresion"></center>
					          </div>
				          </div>';

            //Body
            echo '<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                    '.$html.'                     
                </td>
            </tr>';
            
            //Remote scripts
            self::scripts();
        }


        function darInformacion($cod_solici){
          $sql="SELECT *, 
          b.cod_ciudad as 'cod_ciuori', 
          b.nom_ciudad as 'nom_ciuori', 
          c.cod_ciudad as 'cod_ciudes', 
          c.nom_ciudad as 'nom_ciudes',
          d.nom_asiste as 'nom_asiste',
          e.nom_tercer as 'nom_client'
        FROM 
          ".BASE_DATOS.".tab_asiste_carret a 
        LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad b ON a.ciu_origen = b.cod_ciudad 
        LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad c ON a.ciu_destin = c.cod_ciudad
        INNER JOIN ".BASE_DATOS.".tab_formul_asiste d ON a.tip_solici = d.id
        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e ON a.cod_client = e.cod_tercer
        WHERE 
          a.id = '$cod_solici'";
          $query = new Consulta($sql, self::$conexion);
          $respuestas = $query -> ret_matrix('a')[0];
          $respuestas = self::cleanArray($respuestas);
          return $respuestas;
        }



        function darInformacionServicios($cod_solici){
          $sql="SELECT
          a.id,
          a.cod_servic, 
          a.des_servic, 
          a.tip_tarifa, 
          a.val_servic, 
          a.can_servic, 
        IF(
          a.tip_tarifa = 'diurna', b.tar_diurna, 
          b.tar_noctur
        ) as 'tar_unitar' 
        FROM 
        ".BASE_DATOS.".tab_servic_solasi a 
        LEFT JOIN ".BASE_DATOS.".tab_servic_asicar b ON a.cod_servic = b.id 
        WHERE 
          a.cod_solasi = '$cod_solici'";
          $query = new Consulta($sql, self::$conexion);
          $respuestas = $query -> ret_matrix('a');
          $respuestas = self::cleanArray($respuestas);
          return $respuestas;
        }
        
        function getDataFormulario($cod_servic,$val){
          $sql="SELECT a.cod_campox,a.tex_respue,c.nom_campox, c.ind_tipoxx 
          FROM ".BASE_DATOS.".tab_formul_respue a
          INNER JOIN ".BASE_DATOS.".tab_formul_detail b ON a.cod_campox = b.cod_campox AND
                                                           a.cod_formul = b.cod_formul
          INNER JOIN ".BASE_DATOS.".tab_formul_campos c ON b.cod_campox = c.cod_consec
          WHERE a.cod_servsol = '".$cod_servic."'
          ORDER BY b.num_ordenx ASC";

          $query = new Consulta($sql, self::$conexion);
          $respuestas = $query -> ret_matrix('a');
          $total = $query -> ret_num_rows();
          $respuestas = self::cleanArray($respuestas);
          if($val==1){
            return $respuestas;
          }else{
            return $total;
          }
        }

        function getDataFormulariow($num_solici,$cod_servic,$val){
          $sql="SELECT cod_solici, cod_servic, nam_archi1
          FROM tab_asiste_eviden 
          WHERE cod_solici=$num_solici
          AND cod_servic=$cod_servic";

          $query = new Consulta($sql, self::$conexion);
          $respuestas = $query -> ret_matrix('a');
          $total = $query -> ret_num_rows();
          $respuestas = self::cleanArray($respuestas);
          if($val==1){
            return $respuestas;
          }else{
            return $total;
          }
        }

        function getDataRutasServicios($cod_solici){
          $sql="SELECT 
          a.des_servic, 
          b.val_latitu, 
          b.val_longit,
          b.fec_ubicac,
          b.hor_ubicac,
          b.usr_ubicac,
          d.nom_formul 
        FROM 
        ".BASE_DATOS.".tab_servic_solasi a 
        INNER JOIN ".BASE_DATOS.".tab_solasi_ubicac b ON a.id = b.cod_servsol 
        INNER JOIN ".BASE_DATOS.".tab_servic_asicar c ON a.cod_servic = c.id
        INNER JOIN ".BASE_DATOS.".tab_formul_formul d ON c.cod_formul = d.cod_consec
        WHERE 
          a.cod_solasi = '$cod_solici' 
        ORDER BY 
          b.id ASC";

          $query = new Consulta($sql, self::$conexion);
          $respuestas = $query -> ret_matrix('a');
          $respuestas = self::cleanArray($respuestas);
          return $respuestas;
        }

        private function reemplazar($cadena){
          $falsos = array("ñ");
          $nuevac = str_replace($falsos, "?",$cadena);
          return $nuevac;
        }


         /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificación
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que será analizado por la función
           *  \return: array
        */
        function cleanArray($array){

          $arrayReturn = array();

          //Convert function
          $convert = function($value){
              if(is_string($value)){
                  return utf8_encode($value);
              }
              return $value;
          };

          //Go through data
          foreach ($array as $key => $value) {
              //Validate sub array
              if(is_array($value)){
                  //Clean sub array
                  $arrayReturn[$convert($key)] = self::cleanArray($value);
              }else{
                  //Clean value
                  $arrayReturn[$convert($key)] = $convert($value);
              }
          }
          //Return array
          return $arrayReturn;
      }

    }

    new InfoDashboardAsiscar();
?>
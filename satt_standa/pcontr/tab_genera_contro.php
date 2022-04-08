<?php

class tab_genera_contro
{
 var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
   switch($_REQUEST[opcion])
   {
        default:
          $this -> registros();
          break;
   }
 }

 /*! \fn: styles
       *  \brief: incluye todos los archivos necesarios para los estilos
       *  \author: Ing. Luis Manrique
       *  \date: 27-04-2020
       *  \date modified: dd/mm/aaaa
       *  \param: 
       *  \return: html
    */

    private function styles(){
        echo '
            
            <!-- Datatables -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

            <!-- Float Menu -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

            <!-- Jquery UI -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">

            <!-- Bootstrap -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

            <!-- Datetime picker -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker-standalone.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
            
            
            <!-- Custom Theme Scripts -->
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/tab_genera_contro.css" rel="stylesheet">
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/spectrum.css" rel="stylesheet">
        ';
    }

    /*! \fn: scripts
   *  \brief: incluye todos los archivos necesarios para los eeventos js
   *  \author: Ing. Luis Manrique
   *  \date: 27-04-2020
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
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

            <!-- Datatables -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/spectrum.js"></script>

             <!-- SweetAlert -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>

            <!-- Datetime picker -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

            <!-- Form validate -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

            <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

            <!-- Custom Theme Scripts -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/tab_genera_contro.js?rand='.rand(150, 20000).'"></script>
        ';
    }

  //---------------------------------------------
  /*! \fn: getInterfParame
  *  \brief:Verificar la interfaz con destino seguro esta activa 
  *  \author: Nelson Liberato
  *  \date: 21/12/2015
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  function registros() {
    self::styles();
    $html = '
            <td>
              <div id="contenedor"> 
                  <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a data-toggle="collapse" data-parent="#accordion" href="#tablaDatos">
                              Listado de Puestos De Control
                          </a>
                        </h4>
                      </div>
                      <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                        <div class="panel-body">
                          <table id="tablaRegistros" class="table table-striped table-bordered" style="width: 100%;">
                            <thead>
                              <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Ciudad</th>
                                <th>Direcci&oacute;n</th>
                                <th>Telefono</th>
                                <th>Longitud</th>
                                <th>Latitud</th>
                                <th>url waze</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                '.$this->modalRegistro().'

                <form method="POST" class="FormularioVia">
                <div id="modal-edit">
                </div>
                </form>

              </table>
            </td>';

    echo utf8_decode($html);

    self::scripts();
  }

  function modalRegistro(){
      $datos_option = $this->darOpcionAsistencia();
      $datosTiposPc = $this->tiposPc();

      $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
      $ciudad = $objciud -> getListadoCiudades();
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $ciudades = array_merge($inicio,$ciudad);
      $ciudagen = array_merge($inicio,$ciudad);
    
      if($_REQUEST['ciudad'])
      {
        $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST['ciudad']);
        $ciudades = array_merge($ciudad_a,$ciudades);
      }
      $ciudadesHtml = '';
      foreach ($ciudades as $key => $value) {
        $ciudadesHtml.='<option value="'.$value['cod_ciudad'].'">'.$value[1].'</option>';
      }
      $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="regService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                REGISTRAR NUEVO PUESTO DE CONTROL
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" class="FormularioVia" enctype="multipart/form-data" >
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para el registro del servicio en el sistema.</p>
                        </div>
                    </div>
                    <div class="row margin-top-row">
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Nombre</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Nombre" id="nom_controID" name="nom_contro" required>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="col-12 control-label">Direcci&oacute;n</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Direcci&oacute;n" id="dir_controID" name="dir_contro">
                        </div>
                    </div>

                    <div class="row margin-top-row">
                        <div class="col-md-4">
                            <label class="col-12 control-label"> Telefono</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Telefono" id="tel_controID" name="tel_contro">
                        </div>
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Ciudad</label>
                            <select class="form-control form-control-sm" id="cod_ciudadID" name="cod_ciudad" required>
                                '.$ciudadesHtml.'
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span>Tipo Puesto De control</label>
                            <select class="form-control form-control-sm" id="tipFormulID" name="tipFormul" required>
                              <option value="">Escoja una Opci&oacute;n</option>
                                '.$datosTiposPc.'
                            </select>
                        </div>
                        
                    </div>

                    <hr>
                    <div class="row margin-top-row">
                        <div class="col-md-3">
                            <label class="col-12 control-label"><span class="redObl">*</span> Latitud</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Latitud" id="val_latituID" name="val_latitu" required>
                        </div>
                        <div class="col-md-3">
                            <label class="col-12 control-label"><span class="redObl">*</span> Longitud</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Longitud" id="val_longitID" name="val_longit" required>
                        </div>
                        <div class="col-md-6">
                            <label class="col-12 control-label">Url Waze</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Waze" id="url_wazexxID" name="url_wazexx" >
                        </div>
                    </div>
                    <div class="row margin-top-row">
                        <div class="col-md-3">
                            <label class="col-12 control-label">Color</label>
                            <input class="form-control form-control-sm" type="text" placeholder="color" id="cod_colorxID" name="cod_colorx" required>
                        </div>
                        <div class="col-md-3">
                          <div class="card" style="width: 18rem;">
                            <img src="https://www.sinrumbofijo.com/wp-content/uploads/2016/05/default-placeholder.png" class="card-img-top" alt="..." style="height: 60%; object-fit: cover; width: 200px; object-position: center center;" id="imgUpload">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <input type="file" class="form-control-file" name="image" id="img" accept="image/*">
                        </div>
                        
                    </div>
            </div>
            <div class="modal-footer"><center>
                <button id="guarServic" type="submit" class="swal2-confirm swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(51, 102, 0); border-left-color: rgb(51, 102, 0); border-right-color: rgb(51, 102, 0);">Guardar</button>
                <button type="button" data-dismiss="modal" class="swal2-cancel swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(170, 170, 170);">Cancelar</button></center>
            </div>
            </form>
        </div>
        </form>
      </div>
    </div>';
    return $html;
  }

  function darOpcionAsistencia(){
    $sql="SELECT a.id,a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuesta = $consulta->ret_matriz("a");
    $html='';
    foreach($respuesta as $dato){
      $html.='<option value="'.$dato['id'].'">'.$dato['nom_asiste'].'</option>';
    }
    return utf8_encode($html);
  }

  function tiposPc(){
    $sql="SELECT a.cod_tpcont, a.nom_tpcont FROM ".BASE_DATOS.".tab_tipos_pcontr a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuesta = $consulta->ret_matriz("a");
    $html='';
    foreach($respuesta as $dato){
      $html.='<option value="'.$dato['cod_tpcont'].'">'.$dato['nom_tpcont'].'</option>';
    }
    return utf8_encode($html);
  }

}

$proceso = new tab_genera_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

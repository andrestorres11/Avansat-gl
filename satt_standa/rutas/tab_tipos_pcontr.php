<?php

class tab_tipos_pcontr
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
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/tab_tipos_pcontr.css" rel="stylesheet">
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
            <script src= "../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/pdfmake.min.js" language="javascript"></script>
            <script src= "../' . DIR_APLICA_CENTRAL . '/js/DataTables/js/vfs_fonts.js" language="javascript"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>

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
            <script src="../' . DIR_APLICA_CENTRAL . '/js/tab_tipos_pcontr.js?rand='.rand(150, 20000).'"></script>
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
                              Listado de Tipos PC
                          </a>
                        </h4>
                      </div>
                      <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                        <div class="panel-body">
                          <table id="tablaRegistros" class="table table-striped table-bordered" style="width: 100%;">
                            <thead>
                              <tr>
                                <th>Codigo</th>
                                <th>Via</th>
                                <th>Usuario Creador</th>
                                <th>Fecha Creación</th>
                                <th>Usuario Modificación</th>
                                <th>Fecha Modificación</th>
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
      $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="regService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                REGISTRAR NUEVO TIPO DE PUESTO DE CONTROL
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" class="FormularioVia">
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para el registro del servicio en el sistema.</p>
                        </div>
                    </div>
                    <div class="row margin-top-row">
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Nombre del tipo de pc</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Nombre del tipo de pc" id="nom_tpcontID" name="nom_tpcont" required>
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

}

$proceso = new tab_tipos_pcontr($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

<?php

class parametrizacion_correos
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
       *  \author: Ing. Cristian Andrés Torres
       *  \date: 02-06-2020
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

            <!-- Bootstrap -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
            
        ';
    }

    /*! \fn: scripts
   *  \brief: incluye todos los archivos necesarios para los eeventos js
   *  \author: Ing. Cristian Andrés Torres
   *  \date: 02-06-2020
   *  \date modified: dd/mm/aaaa
   *  \param: 
   *  \return: html
*/

    private function scripts(){

        echo '
            <!-- jQuery -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>

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
            <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_genera_concor.js?rand='.rand(150, 20000).'"></script>
        ';
    }

  //---------------------------------------------
  /*! \fn: registros
  *  \brief: Imprime el html con el encabezado de la tabla
  *  \author: Cristian Andrés Torres Cardozo
  *  \date: 02/06/2020
  *  \date modified: 
  *  \return 
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
                              Registros
                          </a>
                        </h4>
                      </div>
                      <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                        <div class="panel-body">
                          <table id="tablaRegistros" class="table table-striped table-bordered" style="width: 100%;">
                            <thead>
                              <tr>
                                <th>Nit</th>
                                <th>Cliente</th>
                                <th>Correo</th>
                                <th>Seguimiento</th>
                                <th>Inf. Mensual</th>
                                <th>A cargo de empresa</th>
                                <th>Opciones</th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </table>
            </td>';

    echo utf8_decode($html);

    self::scripts();
  }

}

$proceso = new parametrizacion_correos($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

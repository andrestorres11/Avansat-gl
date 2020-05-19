<?php

/* ! \file: inf_rentab_produc
 *  \brief: Permite visualizar los filtros y las pestañas para generar los informes
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 27/04/2020
 *  \bug: 
 *  \warning: 
 */

class rentab_produc
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
            
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inf_rentab_produc.css" rel="stylesheet">
            <!-- Bootstrap -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

            <!-- Select2 -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/select2/dist/css/select2.min.css" rel="stylesheet">

            <!-- Daterangepicker -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">

            <!-- JQuery AutoComplete -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/devbridge-autocomplete/src/styles.css" rel="stylesheet">

            
            
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

           <!-- Daterangepicker -->
          <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/moment/min/moment-with-locales.js"></script> 
          <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js"></script>

          <!-- Select2 -->
          <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/select2/dist/js/select2.full.min.js"></script>

          <!-- Jquery Input Mask -->
          <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery.inputmask/dist/jquery.mask.min.js"></script>

           <!-- Form validate -->
          <script src="../' . DIR_APLICA_CENTRAL . '/js/functions.js"></script>
          <script src="../' . DIR_APLICA_CENTRAL . '/js/validator.js"></script>

          <!-- Custom Theme Scripts -->
          <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_rentab_produc.js?rand='.rand(150, 20000).'"></script>
      ';
    }

  //---------------------------------------------
  /*! \fn: registros
  *  \brief:Crea el contenedor de las pestañas para los informes
  *  \author: Ing. Luis Manrioque
  *  \date: 28/04/2020
  *  \date modified: 
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
                          <a data-toggle="collapse" data-parent="#accordion" href="#filtros">
                              Filtros
                          </a>
                        </h4>
                      </div>
                      <div id="filtros" class="panel-collapse collapse in" style="overflow: auto;">
                        <div class="panel-body" id="fil_campos">
                          
                        </div>
                      </div>
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a data-toggle="collapse" data-parent="#accordion" href="#tablaDatos">
                              Registros
                          </a>
                        </h4>
                      </div>
                      <div id="tablaDatos" class="panel-collapse collapse in" style="overflow: auto;">
                        <div class="panel-body">
                          <div style="padding-top: 1em;">
                            <ul class="nav nav-pills">
                              <li>
                                  <a data-toggle="tab" id="tag_porNovedad" onclick="createTable(this)" href="#div_porNovedad">Por novedad</a>
                              </li>
                              <li>
                                  <a data-toggle="tab" id="tag_porTurno" onclick="createTable(this)" href="#div_porTurno">Por turno</a>
                              </li>
                              <li>
                                  <a data-toggle="tab" id="tag_diferencia" onclick="createTable(this)" href="#div_diferencia">Diferencia</a>
                              </li>
                            </ul>
                            <div class="tab-content">
                              <div id="div_porNovedad" class="tab-pane fade" style="padding-top: 1em;">
                                  
                              </div>
                              <div id="div_porTurno" class="tab-pane fade" style="padding-top: 1em;">
                                      
                              </div>
                              <div id="div_diferencia" class="tab-pane fade" style="padding-top: 1em;">
                                      
                              </div>
                            </div>
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

$proceso = new rentab_produc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

<?php

class ins_asiste_carret
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
   switch($_REQUEST['opcion'])
   {
        default:
          $this -> filtro();
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
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.min.css" rel="stylesheet">

            <!-- Font Awesome -->
            <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">

            <!-- Module Script -->
            <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">
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

            <script>
             $.blockUI({ message: "<div>Espere un momento</div>" })
            </script>
            
            <!-- Bootstrap -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.min.js"></script>

            <!-- Datepicker Espanol -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/datepicker/datepicker-spanish.js"></script>
            
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
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
            <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

            <!-- Custom Theme Scripts -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_asiste_carret.js"></script>
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
  function filtro($mensaje = '') {
    self::styles();
    $datos_option = $this->darOpcionAsistencia();
    $html = '
            <td class="page" onload="loadAjax("finish")">
              <form id="FormularioSolici" method="post">
              <div class="card text-center" style="margin:15px;">
                <div class="card-header color-heading">
                  Datos del Solicitante
                </div>
              <div class="card-body">
                <div class="row">
                  <div class="offset-1 col-4">
                    <select class="form-control form-control-sm" onchange="cargaFormulario(this)" id="tipFormulID" name="tipFormul">
                      <option value="">Escoja Opción</option>
                      '.$datos_option.'
                    </select>
                  </div>
                  <div class="offset-1 col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Nombre del Solicitante" id="nom_soliciID" name="nom_solici" required disabled>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="offset-1 col-4">
                    <input class="form-control form-control-sm" type="email" placeholder="Email del Solicitante" id="ema_soliciID" name="ema_solici" required disabled>
                  </div>
                  <div class="offset-1 col-4">
                  <input class="form-control form-control-sm" type="number" placeholder="Teléfono del Solicitante" id="tel_soliciID" name="tel_solici" disabled>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="offset-1 col-4">
                    <input class="form-control form-control-sm" type="number" placeholder="Numero de Celular" id="cel_soliciID" name="cel_solici" required disabled>
                  </div>
                  <div class="offset-1 col-4">
                  <input class="form-control form-control-sm" type="text" placeholder="Aseguradora" id="nom_aseguraID" name="nom_asegura" disabled>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="offset-1 col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Poliza" id="nom_polizaID" name="nom_poliza" disabled>
                  </div>
                </div>

              </div>
              </div>

              <div class="card-header color-heading" style="margin:12px;">
              </div>

              <div class="card text-center" style="margin:15px;">
                <div class="card-header color-heading">
                  Datos del Transportista
                </div>
              <div class="card-body">

                <div class="row">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="number" placeholder="Numero de documento" id="num_transpID" name="num_transp" required disabled>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Nombres del Transportista" id="nom_transpID" name="nom_transp" required disabled>
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Primer Apellido" id="ap1_transpID" name="ap1_transp" required disabled>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Segundo Apellido" id="ap2_transpID" name="ap2_transp" disabled>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="number" placeholder="Numero Celular 1" id="ce1_transpID" name="ce1_transp" required disabled>
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Numero Celular 2" id="ce2_transpID" name="ce2_transp" disabled>
                  </div>
                </div>

              </div>
              </div>


              <div class="card text-center" style="margin:15px;">
                <div class="card-header color-heading">
                  Datos del Vehículo
                </div>
              <div class="card-body">

                <div class="row">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm mayuscul-input" type="text" placeholder="Placa" id="num_placaID" name="num_placax" maxlength="6" required disabled>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Marca" id="nom_marcaxID" name="nom_marcax" required disabled>
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Color" id="nom_colorxID" name="nom_colorx" required disabled>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Tipo" id="tip_transpID" name="tip_transp" disabled>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Remolque No" id="num_remolqID" name="num_remolq" disabled>
                  </div>
                </div>

              </div>
              </div>
              
              <div id="con-formul">
              </div>
              <input type="hidden" name="tip_solici" id="tip_soliciID">
              <center><input class="small-box-footer btn btn-success btn-sm" type="submit" disabled/></center>
              </form>
            </td>';

    echo utf8_decode($html);
    self::scripts();

  }

  function darOpcionAsistencia(){
    $sql="SELECT a.id,a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuesta = $consulta->ret_matriz("a");
    $html='';
    foreach($respuesta as $dato){
      $html.='<option value="'.$dato['id'].'">'.$dato['nom_asiste'].'</option>';
    }
    return $html;
  }

}

$proceso = new ins_asiste_carret($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

<?php

class estser_contra
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
            
        <!-- Bootstrap 4.1.3 -->
        <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" rel="stylesheet">
        

        <!-- Font Awesome -->
        <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
        
        <!-- Jquery UI -->
        <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
        
        <!-- Datatables all in one-->
        <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.css" rel="stylesheet">
        <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/css/buttons.bootstrap4.min.css"  rel="stylesheet">
        <link  rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css"  rel="stylesheet"
        <!-- Float Menu -->
        <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
        <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">
        <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inform_style.css" rel="stylesheet">
            
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
              
            <!-- Bootstrap 4.1.3-->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.bundle.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

            <!-- bootstrap-progressbar -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

            <!-- SweetAlert -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>
              
            <!-- Datatables ALL IN ONE-->
            <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.js" ></script>
            <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/js/buttons.bootstrap.min.js" ></script>
            <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js" ></script>

            <!-- Custom Theme Scripts -->
            <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_estser_contra.js?rand='.rand(150, 20000).'"></script>
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
            <meta name="viewport" content= "width=device-width, initial-scale=1.0">
            <div class="container" style="min-width: 99%;">
              <div id="accordions">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Estado del Servicio Contratado
                      </button>
                    </h5>
                  </div>

                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                      <form action="'.$_SERVER['PHP_SELF'].'" id="filter" name="filter" method="POST">
                        <div class="row">
                          <div class="col-4 text-right mt-3">
                            <label>Tipo de Servicio:</label>
                            <select id="tip_servic" name="tip_servic"    title="Seleccione de la lista">  
                              <option value="">Seleccione</option>
                              '.$this->getTipser().'
                            </select>
                          </div>
                          <div class="col-4 text-right mt-3">
                            <label>Dias a vencer:</label>
                            <input type="number" id="diastxt" name="diastxt" aria-describedby="basic-addon1" value="0" min="0">
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <div class="accordion">
                <div class="card">
                  <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                      <button onclick="opciontabla()" class="btn btn-success btn-sm text-white" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Generar Informe
                      </button>
                    </h5>
                  </div>
                  <div id="contenedor">
                    <div id="tablaDatos" class="panel-collapse collapse show m-3" style="overflow: auto;"> 
                      <table id="tablaRegistros" class="table table-striped table-bordered">
                        <thead>
                          <tr>
                            <th>Nit Transportadora</th>
                            <th>Transportadora</th>
                            <th>Fecha / Hora Inicio Servicio</th>
                            <th>Fecha / Hora Fin Servicio</th>
                            <th>Valor Registro</th>
                            <th>Valor Despacho</th>
                            <th>Valor Vigencia C.R.</th>
                            <th>Interfaz con</th>
                            <th>N de Aplicación</th>
                            <th>Sol. Poliza</th>
                            <th>Aseguradora</th>
                            <th>N. Poliza</th>
                            <th>T. Servicio</th>
                            <th>Activa</th>
                            <th>Notifi. por Agenda</th>
                            <th>C. Plan de Ruta</th>
                            <th>Ll. Automática</th>
                            <th>S. Biometria</th>
                            <th>Grupo al que Pertenece</th>
                            <th>Tiempo de T.D</th>
                            <th>Prioridad</th>
                            <th>T. Operación</th>
                            <th>OAL Contratadas</th>
                            <th>OAL por Renovar</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Modal Por Gestionar-->
              <div class="modal fade" id="numcontratadas" role="dialog">
                <div class="modal-dialog modal-lg">
                
                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 id="title-modal" class="modal-title"><center>OAL Contratadas</center></h5>
                      <input type="hidden" id="idnumservicio" value="">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                      <table id="tblRegHorCont" class="table table-striped table-bordered">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>OAL</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                    <div class="modal-footer">
                          <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                  
                </div>
              </div>

            </div>
            </td>';

    echo utf8_decode($html);

    self::scripts();
  }

  /*! \fn: getTipser
  *  \brief: Obtiene los tipos de servicio de la BD y retorna un string con el html para el campo tipo select
  *  \author: Cristian Andr�s Torres
  *  \date: 06/07/2022
  *  \date modified: 
  *  \return html
  */
  private function getTipser(){
    $sql="SELECT a.cod_tipser, a.nom_tipser FROM ".BASE_DATOS.".tab_genera_tipser a WHERE a.ind_estado = 1";
    $resultado = new Consulta($sql, $this->conexion);
    $resultados = $resultado->ret_matriz('a');
    $html='';
    foreach ($resultados as $registro){
      $html .= '<option value="'.$registro['cod_tipser'].'">'.$registro['nom_tipser'].'</option>';
    }
    return utf8_encode($html);
  }

}

$proceso = new estser_contra($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

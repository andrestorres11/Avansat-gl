<?php
    /****************************************************************************
	NOMBRE:   InfProgramTurnos
	FUNCION:  Llama los datos que han si actualizado en las horas de servicios, etapas y tipo de seguimiento
            los demas son dinamicos de js y ajax 
	FECHA DE MODIFICACION: 04/01/2022
	CREADO POR: Ing. Oscar Bocanegra
	CREADO 
	****************************************************************************/
    class InfProgramTurnos
    {
        
        var $conexion,
        $cod_aplica,
        $usuario;

        function __construct($co, $us, $ca){
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;
            $this -> principal();
            

        }
    /****************************************************************************
	NOMBRE:   styles
	FUNCION:  son los estilos CSS que va tener nuestro formularios  objetos
	FECHA DE MODIFICACION: 04/01/2022
	CREADO POR: Ing. Oscar Bocanegra
	CREADO 
	****************************************************************************/
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
              <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inf_progra_turnos.css" rel="stylesheet">
          ';
      }

    /****************************************************************************
	NOMBRE:   scrpts
	FUNCION:  son los javascript que va tener nuestro formularios  objetos
	FECHA DE MODIFICACION: 04/01/2022
	CREADO POR: Ing. Oscar Bocanegra
	CREADO 
	****************************************************************************/
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
              <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_progra_turnos.js"></script>
          ';
      }

      
      /****************************************************************************
	NOMBRE:   principal
	FUNCION:  Crea los objetos que se cargan inicialmente  luego ejecuta elLlama los estilos que va tener la vista 
	FECHA DE MODIFICACION: 04/01/2022
	CREADO POR: Ing. Oscar Bocanegra
	CREADO 
	****************************************************************************/


      private function principal(){
          self::styles();
          $mDateNOW = date('Y-m-d');
          $mDateTem = date_sub(date_create($mDateNOW), date_interval_create_from_date_string("8 days") );
          
          echo "<pre style='display:none'>"; print_r( $mDateTem ); echo "</pre>"; 
          $mDateYes = date("Y-m-d", strtotime($mDateTem ->date));

          //Body
          echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
          <tr>
              <td>
                  <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                  <input type="hidden" id="standaID" name="standaID" value="satt_standa">
                    <div class="container" style="min-width: 98%;">
                        <div id="accordion">
                            <div class="card">
                                  <div class="card-header" id="headingOne">
                                      <h5 class="mb-0">
                                          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                              Generar bitacora de modificaciones de tipo de servicio
                                            </button>
                                      </h5>
                                    </div>
                                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                      <div class="card-body">
                                          <form action="'.$_SERVER['PHP_SELF'].'" id="filter" name="filter" method="POST">
                                              <div class="row">
                                                  <div class="col-2 text-right">
                                                      <label>Funcionarios:</label>
                                                  </div>
                                                  <div class="multi_select_box">
                                                      <select id="Cod_funcionario" name="Cod_funcionario" class="multi_select" data-live-search="true" data-size="10"  title="Seleccione de la lista" multiple>
                                                          
                                                          
                                                      </select>
                                                  </div>
                                              </div>

                                              <div class="row mt-2">
                                                  <div class="col-2 text-right">
                                                      <label>Fecha Inicial:</label>
                                                  </div>
                                                  <div class="col-2">
                                                      <input type="date" id="fec_inicio" name="fec_inicio" required value="'.$mDateYes.'" max="'.$mDateNOW.'">
                                                  </div>
                                                  <div class="col-2 text-right">
                                                      <label>Fecha Final:</label>
                                                  </div>
                                                  <div class="col-2">
                                                      <input type="date" id="fec_finxxx" name="fec_finxxx" required value="'.$mDateNOW.'">
                                                  </div>
                                                  
                                                  
                                              </div>

                                              <div class="row mt-2">
                                                  <div class="col-2 text-right">
                                                      <label>Programacion de turnos:</label>
                                                  </div>
                                                  <div class="col-2">
                                                      <input type="radio" name="optradio" value="1" checked>
                                                  </div>
                                                  <div class="col-2 text-right">
                                                      <label>Novedades en Turno:</label>
                                                  </div>
                                                  <div class="col-2">
                                                  <input type="radio" name="optradio" value="2">
                                                  </div>
                                                  
                                                  
                                              </div>
                                              </form>
                                          </div>
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
                                        <div id="collapseTwo" class="collapse show m-3" aria-labelledby="headingTwo" data-parent="#accordion">
                                            <div class="card-body" style="width: 100%; overflow: auto;">
                                                <table class="table table-striped table-bordered" id="table_data" ></table>
                                                <table class="table table-striped table-bordered" id="table_data2" ></table>
                                                
                                              
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              
              </td>
          </tr>
        </table>');
          self::scripts();
      }

      
    


        

    }
 new InfProgramTurnos($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
 

?>
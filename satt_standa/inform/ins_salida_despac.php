<?php
    /****************************************************************************
	NOMBRE:   SaldespacHoras
	FUNCION:  Llama los datos que han si actualizado en las horas de servicios, etapas y tipo de seguimiento
            los demas son dinamicos de js y ajax 
	FECHA DE MODIFICACION: 28/03/2022
	CREADO POR: Ing. Oscar Bocanegra
	CREADO 
	****************************************************************************/
    class SaldespacHoras
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
              
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.5.2/flatly/bootstrap.min.css">

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
              <link href="../' . DIR_APLICA_CENTRAL . '/estilos/informes.css" rel="stylesheet">
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
              <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
              
              <script src="../' . DIR_APLICA_CENTRAL . '/js/inf_salida_despac.js"></script>
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
          
          $fechini= date("Y-m-d",strtotime($mDateNOW."- 8 days")); 



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
                                              Informe salida de despachos por hora
                                            </button>
                                      </h5>
                                    </div>
                                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                      <div class="card-body mt-2">
                                          <form action="'.$_SERVER['PHP_SELF'].'" id="filter" name="filter" method="POST">
                                            <div class="row">

                                                <div class="col-2 text-left">
                                                    <label>Tipo de Servicio:</label>
                                                </div>
                                                <div class="col-4 multi_select_box">
                                                    <select id="Cod_tiposerv" name="Cod_tiposerv" class="multi_select" data-live-search="true" data-size="10"  title="Seleccione de la lista" onchange="changeFunc()" multiple>
                                                    </select>
                                                </div>
                                              
                                                <div class="col-2 text-left">
                                                    <label>Transportadoras:</label>
                                                </div>
                                                <div class="col-4 multi_select_box">
                                                    <select id="Cod_transp" name="Cod_transp" class="multi_select" data-live-search="true" data-size="10"  title="Seleccione de la lista" multiple>
                                                    </select>
                                                </div>
                                                  
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-2 text-left">
                                                    <label>Fecha Inicial:</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="date" id="fec_inicio" name="fec_inicio"  required value="'.$fechini.'" >
                                                </div>
                                                <div class="col-2 text-left">
                                                    <label>Fecha Final:</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="date" id="fec_finxxx" name="fec_finxxx" required value="'.$mDateNOW.'">
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-2 text-left">
                                                    <label>Hora Inicial:</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="number"  id="hor_inicia" name="hor_inicia" min="0" max="23" value=0>
                                                </div>
                                                <div class="col-2 text-left">
                                                    <label>Hora Final:</label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="number" size="6" id="hor_finxxx" name="hor_finxxx" min="0" max="23" value=23>
                                                </div>
                                            </div>
                                          </form>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                          
                                    <div class="card">
                                        <div class="card-header" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button onclick="opciontabla()" class="btn btn-success btn-sm text-white" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                  Generar Informe
                                                </button>    <div class="accordion">
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                                            <div id="conttablas" name="conttablas" class="card-body" style="width: 100%; overflow: auto;">


                                                <table class="table table-striped table-bordered" id="table_data" ></table>
                                                
                                                
                                              
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              
              </td>
          </tr>
        </table>
        
        <div id="numhortransp" name="numhortransp" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titleventmodal" name="titleventmodal">xx  Despacho(s) encontrados de la empresa de transporte xxx</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="contenidotbl" name="contenidotbl">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
                    </div>
          </div>
        </div>
      </div>
        

    <div id="loading" name="loading" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">C A R G A N D O</h5>
              </div>
              <div class="modal-body">
              <img class="rounded mx-auto d-block col-6" src="../satt_standa/imagenes/ajax-loader.gif" />
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
        </div>
    </div>

    <div id="openModal" class="modalDialog">
      <div>
        <img class="rounded mx-auto d-block col-6" src="../satt_standa/imagenes/ajax-loader.gif" />
      </div>
    </div>
        
        
        ');
          self::scripts();
      }

      
    


        

    }
 new SaldespacHoras($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
 

?>
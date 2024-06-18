<?php
    /****************************************************************************
	NOMBRE:   InsGeneraAsegurado
	FUNCION:  Registra el asegurado para 
	FECHA DE MODIFICACION: 15/09/2020
	CREADO POR: Ing. Cristian Andrés Torres
	MODIFICADO 
	****************************************************************************/
	
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/

    class InsGeneraAsegurado
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
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/css/bootstrap.min.css" rel="stylesheet">

                <!-- Font Awesome -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
                
                <!-- Jquery UI -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
                
                <!-- Datatables all in one-->
                <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.css" rel="stylesheet">
                <link  rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/css/buttons.bootstrap4.min.css"  rel="stylesheet">

                <!-- Float Menu -->
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/button.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/js/dashboard/floatMenu.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/estilos_table.css" rel="stylesheet">
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/inform_style.css" rel="stylesheet">
                
            ';

            echo '<style>
              .no-margin-bottom {
                margin-bottom: 0 !important;
              }
            </style>';
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
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/moment/moment.js"></script>

                <!-- jQuery -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/jquery-ui-1.12.1/jquery.blockUI.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.js"></script>

                <!-- Bootstrap -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-4/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Form validate -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.validate.min.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/jquery.form.js"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/validation-scripts/additional-methods.min.js"></script>

                <!-- SweetAlert -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/sweetalert2.all.8.11.8.js"></script>
                
                <!-- Datatables ALL IN ONE-->
                <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/datatables.js" ></script>
                <script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/lib/DataTablesb4/Buttons-1.7.1/js/buttons.bootstrap.min.js" ></script>

                <!-- Custom Theme Scripts -->
                <script src="../' . DIR_APLICA_CENTRAL . '/js/ins_genera_asegur.js"></script>
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

        private function principal(){
            self::styles();
            $mDateNOW = date('Y-m-d');
            $mDateTem = date_sub(date_create($mDateNOW), date_interval_create_from_date_string("7 days") );
            
            echo "<pre style='display:none'>"; print_r( $mDateTem ); echo "</pre>"; 
            $mDateYes = date("Y-m-d", strtotime($mDateTem ->date));

            $aseguradora = self::getAseguradora();
            if($aseguradora!=null){
              $html_asegura = '<input type="hidden" name="ase_asigna" id="ase_asignaID" value="'.$aseguradora.'">';
            }else{
              $html_asegura = '<input type="hidden" name="cod_asegurS" id="cod_asegurSID" value="">';
            }
            
            //Body
            echo utf8_decode('<table style="width: 100%;" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <input type="hidden" id="standaID" value="satt_standa">
                            <div class="container" style="min-width: 98%;">
                                <div class="row mt-2 mb-2">
                                  <div class="col-md-4">
                                    <button id="nuevoAseguradoBtn" type="button" style="display: none;" class="btn btn-success btn-sm"  data-toggle="modal" data-target="#NuevoAseguradoModal">Nuevo Asegurado</button>
                                  </div>
                                </div>
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Filtro de busqueda
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">
                                                <form id="filter" method="POST">
                                                    <div class="row">
                                                        <div class="offset-md-3 col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4" style="text-align:right;">
                                                                    <label for="emp_transp" class="mt-2">Empresa de transporte</label>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <input type="text" class="form-control form-control-sm" name="emp_transp" id="emp_transp" style="width:400px" placeholder="" onkeyup="autocompletable(this)" autocomplete="off" onclick="vaciaInput(this)">
                                                                    <div id="emp_transp-suggestions" class="suggestions"></div>
                                                                </div>
                                                            </div>
                                                           '.$html_asegura.'
                                                        </div>
                                                    </div>
                                                    <div class="row mt-4">
                                                        <div class="col-12 text-center">
                                                            <button type="button" id="btnBuscar" onclick="consultaInformacion()" class="btn btn-success btn-sm" disabled>Buscar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="cont">
                                </div>
                                '.$this->nuevoRegistroModal().'
                                </div>
                </td>
            </tr>
                                                ');
            self::scripts();
        }

        private function nuevoRegistroModal(){
            $html = '<!-- Modal Nuevo Registro-->
            <div class="modal fade" id="NuevoAseguradoModal" role="dialog">
              <div class="modal-dialog modal-lg">
              
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 id="title-modal" class="modal-title"><center>'.utf8_encode('Asignación de Transportadora (Asegurado)').'</center></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <form id="FormRegist"  action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                      '.utf8_encode($this->camposFormulPrincipal()).'
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary secondary-color btn-sm" data-dismiss="modal">Cerrar</button>
                      <button type="button" onclick="saveInfo(`FormRegist`)" id="nom_btnID" class="btn btn-success btn-sm">Asignar Transportadora</button>
                    </div>
                  </form>
                </div>
                
              </div>
            </div>';
            return $html;
        }

        private function camposFormulPrincipal(){
            $html='<div class="card" style="margin:5px;">
            <div class="card-header color-heading text-center">
              Información de la Transportadora
            </div>
          <div class="card-body">

          <div class="alert alert-info" role="alert" style="display:none;">
            <h6 class="no-margin-bottom">La Transportadora ya esta registrada. Puede hacer la asignación.</h6>
          </div>



            <div class="row">
              <div class="col-md-3 col-sm-10">
                <div class="form-group">
                  <label for="cod_transpID" class="labelinput">Nit:</label>
                  <input class="form-control form-control-sm" type="text" id="cod_transpID" name="cod_transp" validate req maxlength="10">
                </div>
              </div>
              <div class="col-md-2 col-sm-2">
                <div class="form-group">
                  <label for="dvID" class="labelinput">Dv:</label>
                  <input class="form-control form-control-sm" type="text" id="dv" name="dv" disabled>
                </div>
              </div>

              <input type="hidden" name="reg_transp" id="reg_transpID" value="1">

            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label for="nom_transpID" class="labelinput">Nombre / Razón Social:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Razón Social" id="nom_transpID" name="nom_transp" disabled validate req>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label for="abr_transpID" class="labelinput">Abreviatura:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Abreviatura" id="abr_transpID" name="abr_transp" disabled validate req>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label for="cod_ciudadID" class="labelinput">Ciudad:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Ciudad" id="cod_ciudadID" name="cod_ciudad" onkeyup="busquedaCiudad(this)" onclick="limpia(this)" disabled validate req>
                  <div id="cod_ciudadID-suggestions" class="suggestions" style="top: 50px !important;"></div>

                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label for="nom_direccID" class="labelinput">Dirección:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Dirección" id="nom_direccID" name="nom_direcc" disabled validate req>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label for="num_telefoID" class="labelinput">Telefóno:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Teléfono" id="num_telefoID" name="num_telefo" disabled validate req>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label for="regimenID" class="labelinput">Regimen:</label>
                  <select class="form-control form-control-sm req" id="regimenID" name="regimen" disabled validate req>
                    <option value="" selected="selected">-----</option>
                    <option value="3">Gran Contribuyente</option>
                    <option value="6">Gran Contribuyente Autorretenedor</option>
                    <option value="1">Regimen Comun</option>
                    <option value="2">Regimen Simplificado</option>
                    <option value="0">Sin regimen</option>
                    <option value="5">Tributario Especial</option> 
                  </select>
                </div>
              </div>
              <div class="col-md-8 col-sm-12">
                <div class="form-group">
                  <label for="cor_transpID" class="labelinput">Correo Electrónico:</label>
                  <input class="form-control form-control-sm req" type="text" placeholder="Correo Electrónico" id="cor_transpID" name="cor_transp" disabled validate req>
                </div>
              </div>
            </div>

          ';

          return $html;
        }
        private function armaSelect($array){
            $html = '';
            foreach ($array as $dato){
                $html .= '<option value="'.$dato[0].'">'.$dato[1].'</option>';
            }
            return utf8_encode($html);
        }


        public function getAseguradora(){
          $sql="SELECT a.clv_filtro, b.nom_tercer FROM ".BASE_DATOS.".tab_aplica_filtro_usuari a
                    INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.clv_filtro = b.cod_tercer
                   WHERE cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND cod_filtro = '".COD_FILTRO_ASEGUR."'";
          $resultado = new Consulta($sql, $this -> conexion);
          $resultados = $resultado->ret_matriz();
          if(sizeof($resultados)>0){
              return $resultados[0][0].' - '.$resultados[0][1];
          }
          return null;
      }


    }

    new InsGeneraAsegurado($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
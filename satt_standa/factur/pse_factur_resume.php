<?php
    /****************************************************************************
	NOMBRE:   PSEFacturResume
	FUNCION:  Muestra los resultados del estado de cartera por empresa transportadora
	FECHA DE MODIFICACION: 26/04/2021
	CREADO POR: Ing. Cristian Andrés Torres
	MODIFICADO 
	****************************************************************************/
	
	/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/

    class PSEFacturResume
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
                <link href="../' . DIR_APLICA_CENTRAL . '/estilos/stylesNew.css" rel="stylesheet">
                
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
                <script src="../' . DIR_APLICA_CENTRAL . '/js/pse_factur_resume.js"></script>
            ';
        }

        
        /*! \fn: principal
        *  \brief: carga la vista principal del formulario de busqueda de transportadora
        *  \author: Ing. Cristian Andrés Torres
        *  \date: 10-02-2022
        *  \date modified: dd/mm/aaaa
        *  \param: 
        *  \return: html
        */
        private function principal(){
            //Carga los estilos
            self::styles();
            echo utf8_decode('<table style="width: 100%;" class="bk-gray" id="dashBoardTableTrans">
            <tr>
                <td>
                    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
                            <div class="container" style="min-width: 100%; min-height:100%">
                                <input type="hidden" name="nom_standa" id="nom_standa" value="satt_standa">
                                <input type="hidden" name="cod_transp" id="cod_transp" value="">
                            '.self::htmlCode().'
                            </div>
                </td>
            </tr>');
            //Carga los scripts
            self::scripts();
        }

        /*! \fn: htmlCode
        *  \brief: crea y retorna el html de la vista inicial
        *  \author: Ing. Cristian Andrés Torres
        *  \date: 10-02-2022
        *  \date modified: dd/mm/aaaa
        *  \param: 
        *  \return: html
        */
        private function htmlCode(){
            if(self::obtenerSiTieneVarias()){
            $html = '
                    <div class="row mt-1">
                        <div class="col-md-12 col-sm-12">
                            <div id="accordion">
                                <div class="card">
                                    <div class="card-header sty1" id="headingOne">
                                            <h5 class="mb-0">Buscar Facturación</h5>
                                    </div>
                                    <div class="collapse show">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-5 col-md-3 d-flex flex-column justify-content-center text-right">
                                                    <h5 class="label-input">Nit/Nombre del cliente:</h5>
                                                </div>
                                                <div class="col-sm-7 col-md-5">
                                                    <input class="form-control form-control-sm" type="text" name="nom_transp" id="nom_transp" placeholder="Seleccione una transportadora"  onkeyup="busTranspor(this)" autocomplete="off">
                                                    <div id="nom_transp-suggestions" class="suggestions"></div>
                                                </div>
                                                <div class="col-sm-12 col-md-1">
                                                    <button type="button" class="btn btn-success btn-sm" disabled id="crearBuscarInfo">Buscar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            }else{
                $cod_transpd = self::obtenerTransportadora();
                if($cod_transpd != ''){
                    $html.='<input type="hidden" id="busdefect" value="'.$cod_transpd.'">';
                }else{
                    $html.='<div class="row m-3 p-2">
                                <div class="col-md-12">
                                    <div class="alert alert-danger" role="alert">
                                        <h6><i class="fa fa-times-circle m-2" aria-hidden="true"></i>  No hay información que mostrar, por favor contacte al administrador.</h6>
                                    </div>
                                </div>
                            </div>';
                }
                
            }    
            $html.='<div class="mt-1" id="bodyViewInfo">
                    </div>';
            return $html;
        }

        function obtenerSiTieneVarias(){
            $resp= false;
            $sql = "SELECT b.clv_filtro, c.nom_tercer FROM ".BASE_DATOS.".tab_genera_usuari a 
                      INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b ON a.cod_perfil = b.cod_perfil AND b.cod_filtro = '".COD_FILTRO_EMPTRA."'
                      LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.clv_filtro = c.cod_tercer AND c.cod_estado = 1
                    WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."'";
            $consulta = new Consulta($sql, $this->conexion);
            $registros = $consulta->ret_matriz();
            $total = count($registros);
            if($total<=0){
                //Valida Perfil
                $sql = "SELECT a.cod_usuari FROM ".BASE_DATOS.".tab_genera_usuari a 
                            WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND a.cod_perfil IN ('".COD_PERFIL_SUPERUSR."', '".COD_PERFIL_ADMINIST."')";
                $consulta = new Consulta($sql, $this->conexion);
                $cantidad = count($consulta->ret_matriz());
                if($cantidad>0){
                    $resp = true;
                }
            }elseif($total>1){
                $resp = true;
            }

            return $resp;
          }

          function obtenerTransportadora(){
            $sql = "SELECT b.clv_filtro, c.nom_tercer FROM ".BASE_DATOS.".tab_genera_usuari a 
            INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b ON a.cod_perfil = b.cod_perfil AND b.cod_filtro = '".COD_FILTRO_EMPTRA."'
            LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.clv_filtro = c.cod_tercer AND c.cod_estado = 1
          WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."'";
            $consulta = new Consulta($sql, $this->conexion);
            $registro = $consulta->ret_matriz();
            if(count($registro)>0){
                $cod_transp = $registro[0]['clv_filtro'];
            }else{
                $cod_transp = '';
            }
            return $cod_transp;
          }

    }

    new PSEFacturResume($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
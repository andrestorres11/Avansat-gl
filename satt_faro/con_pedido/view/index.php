<?php
/* ! \file: index.php
 *  \brief: Clase que genera la interfaz principal de consulta
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 04/09/2020
 *  \warning:
 */
    use Phppot\Captcha;
    use Phppot\Contact;


    class ConsultarPedido
    {
        var $conexion  = NULL;
        
        
        function __construct()
        {
            //Archivos necesarios
            session_start();
            require_once "../../constantes.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/conexion_lib.inc";
            $this->conexion = new Conexion( HOST, USUARIO, CLAVE, BASE_DATOS, BD_STANDA );
            self::validateCaptcha($_REQUEST);
        }

        /*! \fn: validateCaptcha
         *  \brief: Verifica si el codigo de verigficación ingresado es valido
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: N/A
         */
        protected function validateCaptcha($request){
            require_once "../Model/Captcha.php";
            $captcha = new Captcha();

            if (count($request) > 0) {
                $userCaptcha = filter_var($request["captcha_code"], FILTER_SANITIZE_STRING);
                $isValidCaptcha = $captcha->validateCaptcha($userCaptcha);
                if ($isValidCaptcha) {
                    //self::interfaz($request);
                    if($request["opttip"]!=''){
                      if(self::consultaExiste($request["pedido"],$request["opttip"])){
                        //self::prueba($request);
                        if($request["opttip"]==3){
                          self::interfaz($request);
                        }else{
                          $data=self::dataPedRemisi($request["pedido"]);

                          /* Separa los datos para la busqueda */
                          $datos = explode("-", str_replace(' ', '', $data['ped_remisi']));
                          $num_pedido = $datos[0];
                          $num_lineax = $datos[1];
                          $_SESSION['num_pedido']= $num_pedido;
                          $_SESSION['num_linea']= $num_lineax;
                          $_SESSION['busqueda']= 1;
                          header("Location: dashboard.php");
                        }
                        
                      }else{
                        $error_message = "No se ha encontrado el valor buscado";
                        header("Location: ".explode("?", $_SERVER['HTTP_REFERER'])[0]."?message_bus=".$error_message);
                      }
                  }else{
                      $error_message = "Seleccione una opción";
                      header("Location: ".explode("?", $_SERVER['HTTP_REFERER'])[0]."?message_par=".$error_message);
                  }
                  }else {
                    $error_message = "Codigo Captcha Incorrecto";
                    header("Location: ".explode("?", $_SERVER['HTTP_REFERER'])[0]."?message=".$error_message);
                }
            }
        }

        /*! \fn: consultaExiste
         *  \brief: De acuerdo a la opcion ingresada en el formulario hace la consulta de los diferentes metodos.
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function consultaExiste($val_consul,$opttip){
          switch($opttip){
			      case '1':
            $resp = self::conPedido($val_consul);
            return $resp;
				    break;

			      case '2':
            $resp =  self::conRemisi($val_consul);
            return $resp;
				    break;

            case '3':
            $resp = self::conViaje($val_consul);
            return $resp;
				    break;

			      default:
				    return false;
            break;
            
		      }
        }

         /*! \fn: dataPedRemisi
         *  \brief: Verifica si si existe un pedido o remision de la base de datos y retorna un boolean
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function dataPedRemisi($val_consul){
          $mSql = "SELECT ped_remisi,num_docume FROM ".BASE_DATOS.".tab_despac_destin WHERE (ped_remisi = '".$val_consul."' OR num_docume='".$val_consul."');";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo();
          return $mData;
        }


        /*! \fn: conPedido
         *  \brief: Verifica si si existe un pedido de la base de datos y retorna un boolean
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function conPedido($val_consul){
          $datos = explode("-", str_replace(' ', '', $val_consul));
          $num_pedido = $datos[0];
          $num_lineax = $datos[1];
          $mSql = "SELECT COUNT(*) as 'can' FROM ".BASE_DATOS.".tab_genera_pedido WHERE 
                        num_pedido = '".$num_pedido."' AND 
                        num_lineax = '".$num_lineax."' ;";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo()['can'];
          $resp = false;
          if($mData>0){
            $resp = true;
          }
          return $resp;
        }


        /*! \fn: conRemisi
         *  \brief: Verifica si si existe una remision de la tabla tab_despac_destin de la base de datos y retorna un boolean
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function conRemisi($val_consul){
          $remision = str_replace(' ', '', $val_consul);
          $mSql = "SELECT COUNT(*) as 'can' FROM ".BASE_DATOS.".tab_despac_destin WHERE 
                      (ped_remisi = '".$remision."' OR num_docume = '".$remision."');";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo()['can'];
          $resp = false;
          if($mData>0){
            $resp = true;
          }
          return $resp;
        }

        /*! \fn: conViaje
         *  \brief: Verifica si si existe un viaje con el valor como parametro enviado de la base de datos y retorna un boolean
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function conViaje($val_consul){
          $mSql = "SELECT COUNT(*) as 'can' FROM tab_despac_despac WHERE cod_manifi = '".$val_consul."';";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo()['can'];
          $resp = false;
          if($mData>0){
            $resp = true;
          }
          return $resp;
        }

        /*! \fn: style
         *  \brief: Recopila los archivos necesarios de estilos
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: HTML
         */
        protected function style(){
            $randon = rand(1111,9999);
            $style = '<!-- Bootstrap CSS -->
                      <link rel="stylesheet" href="../assets/lib/bootstrap4/css/bootstrap.css">
                      <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/AdminLTE.min.css">
                      <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/skins/_all-skins.min.css">
                      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/adminlte.min.css" integrity="sha256-tDEOZyJ9BuKWB+BOSc6dE4cI0uNznodJMx11eWZ7jJ4=" crossorigin="anonymous" />
                      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/alt/adminlte.plugins.min.css" integrity="sha256-K/rXKcrvSBsdB8WjaU78Ga+3bqjOZ0oyKQ2hpOb2OgU=" crossorigin="anonymous" />
                      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/alt/adminlte.extra-components.min.css" integrity="sha256-NQaR4VO2vLNDjoagWSYPEuUeqU5U7X1bdqJJiQsrmn0=" crossorigin="anonymous" />
                      <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
                      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
                      <link href="../assets/css/style.css?rand='.$randon.'" type="text/css" rel="stylesheet" />';
            echo $style;
        }

        
        /*! \fn: style
         *  \brief: Recopila los archivos necesarios de JavaScript
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: HTML
         */
        protected function script(){
            $script = ' <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>   
                        <script src="../assets/lib/bootstrap4/js/bootstrap.js"></script>
                        <!-- AdminLTE App -->
                        <script type="text/javascript" language="JavaScript" src="https://adminlte.io/themes/AdminLTE/dist/js/adminlte.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/adminlte.min.js" integrity="sha256-Utchz0cr9Hjt+G0gl1YbXb8P2mNugSxobc9AXUfreHc=" crossorigin="anonymous"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/pages/dashboard3.min.js" integrity="sha256-bf6XNqDnwX4g6QZx934mr8BFaRNtjY2Vs88YsjZi9QY=" crossorigin="anonymous"></script>
                        ';
            echo $script;
        }


        /*! \fn: interfaz
         *  \brief: Genera la visual de la consulta del pedido
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: HTML
         */
        protected function interfaz($data){
            /* Consulta y trae los pedidos asociados al viaje */
            $pedidos = self::conViajePedidos($_REQUEST['pedido']);

            $html = '
                    <!------ Include the above in your HEAD tag ---------->

                    <!doctype html>
                    <html lang="en">
                        <head>
                            <!-- Required meta tags -->
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';

                            self:: style();
                            self:: script();

            $html .=   '<title>Consulta Pedido</title>
                        </head>
                        <body class="bodyback">
                          <main class="login-form" style="margin-top:10%">
                            <div class="container">
                              <div class="row justify-content-center">
                                <div class="col-md-11">
                                  <div class="card">
                                    <div class="card-header color-principal text-white text-center"><h5 style="margin-bottom:0px">Pedidos Asociados</h5></div>
                                      <div class="card-body overflow-auto" style="height:445px;">';

                                      $cont = 0;
                                      foreach($pedidos as $pedido){
                                        if( $cont == 0 ){
                                          $html .= ' <div class="row">';
                                        }
                                        
                                        
                                        $html .= '<div class="col-md-4">
                      <div class="box box-solid box-primary">
                        <div class="box-header with-border text-center">
                          <h3 class="box-title ">Pedido No. '.$pedido['ped_remisi'].'</h3>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </div>
                        </div>
                        <div class="box-body">
                          <div class="row">
                            <div class="col-md-12"><h5 class="txtsmll">No. Remisión:</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Numero de remisión" readonly value="'.$pedido['num_docume'].'">
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-12"><h5 class="txtsmll">Nombre Destinatario:</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Nombre destinatario" readonly value="'.$pedido['nom_destin'].'">
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly value="'.$pedido['fec_citdes'].' '.$pedido['hor_citdes'].'">
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly value="'.$pedido['dir_destin'].'">
                            </div>
                          </div>
                          <div class="mt-3 row justify-content-center align-items-center">
                            <div class="col-md-8 offset-md-4">
                              <form method="post" action="dashboard.php">
                              <input type="hidden" name="num_pedidoIn" value="'.$pedido['ped_remisi'].'">
                              <input type="hidden" name="num_remisiIn" value="'.$pedido['num_docume'].'">
                              <input type="hidden" name="busqueda" value="2">
                              <button type="submit" class="btn color-principal text-white txtsmll">
                                Ver estado
                              </button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>';

                      $cont++;
                      $html .= '</div>';
                      if( $cont == 3 ){
                        $html .= ' </div>';
                        $cont = 0;
                      }
                    }


            $html .=  '        </div>
                        <div class="row"><div class="col-md-12 text-center"><a href="../index.php" class="btn color-principal text-white">
                        <i class="zmdi zmdi-caret-left-circle"></i> Regresar
                        </a></div></div> 

                                    </div>
                                  </div>
                                </div>
                            </div>
                      </main>
                    </body>
                  </html>';
            echo utf8_decode($html);
        }


        /*! \fn: conViajePedidos
         *  \brief: Verifica si si existe un viaje con el valor como parametro enviado de la base de datos y retorna un boolean
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function conViajePedidos($val_consul){
          $mSql = "SELECT a.num_despac, b.ped_remisi, b.num_docume, b.nom_destin, b.dir_destin, b.fec_citdes, b.hor_citdes,b.cod_remdes FROM tab_despac_despac a 
                    INNER JOIN tab_despac_destin b ON a.num_despac = b.num_despac
          WHERE a.cod_manifi = '".$val_consul."' GROUP BY b.cod_remdes;";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_matrix('a');
          return $mData;
        }



        protected function armaPedido($data){
          $html .= '<div class="col-md-4">
                      <div class="box box-solid box-primary">
                        <div class="box-header with-border text-center">
                          <h3 class="box-title ">Pedido No. </h3>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </div>
                        </div>
                        <div class="box-body">
                          <div class="row">
                            <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly>
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Fecha estimada de entrega" readonly>
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-12"><h5 class="txtsmll">Dirección</h5></div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly>
                            </div>
                          </div>
                          <div class="mt-3 row justify-content-center align-items-center">
                            <div class="col-md-8 offset-md-4">
                              <button type="submit" class="btn color-principal text-white txtsmll">
                                Ver estado
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>';
          echo utf8_decode($html);
        }
    }  

    $ConsultarPedido = new ConsultarPedido();

?>

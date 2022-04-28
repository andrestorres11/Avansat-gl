<?php
    /****************************************************************************
    NOMBRE:   AjaxFacturResume
    FUNCION:  Retorna todos los datos necesarios para construir la información
    FECHA DE MODIFICACION: 26/04/2022
    CREADO POR: Ing. Cristian Andrés Torres
    MODIFICADO 
    ****************************************************************************/
    
    /*error_reporting(E_ALL);
    ini_set('display_errors', '1');*/

    class AjaxFacturResume
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();

        function __construct($co = null, $us = null, $ca = null)
        {
            //Include Connection class
            @include( "../lib/ajax.inc" );
            @include( "../lib/general/src/class.upload.php" );
            include_once('../lib/general/constantes.inc');
            include_once('../lib/general/functions.inc');
            @include_once '../../' . BASE_DATOS . '/constantes.inc';
            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario =  $_SESSION['datos_usuario']['cod_usuari'];;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "getTransportadoras":
                    self::getTransportadoras();
                    break;
                case "getInfo":
                    self::getInfo();
                    break;
            }
        }

        function getTransportadoras(){
            $busqueda = $_REQUEST['key'];

            $sql = "SELECT a.cod_usuari FROM ".BASE_DATOS.".tab_genera_usuari a 
                            WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND a.cod_perfil IN ('".COD_PERFIL_SUPERUSR."', '".COD_PERFIL_ADMINIST."')";
            $consulta = new Consulta($sql, self::$conexion);
            $cantidad = count($consulta->ret_matriz());
            if($cantidad>0){
                $sql="SELECT a.cod_tercer, b.nom_tercer FROM 
                                ".BASE_DATOS.".tab_tercer_emptra a 
                     INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                            ON a.cod_tercer = b.cod_tercer
                            WHERE b.cod_estado = 1 AND b.nom_tercer LIKE '%$busqueda%' ORDER BY b.nom_tercer LIMIT 3";
            }else{
                $sql = "SELECT b.clv_filtro as 'cod_tercer', c.nom_tercer FROM ".BASE_DATOS.".tab_genera_usuari a 
                INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b ON a.cod_perfil = b.cod_perfil AND b.cod_filtro = '".COD_FILTRO_EMPTRA."'
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.clv_filtro = c.cod_tercer AND c.cod_estado = 1
                WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' AND c.nom_tercer LIKE '%$busqueda%' ORDER BY c.nom_tercer LIMIT 3";
            }

             $resultado = new Consulta($sql, self::$conexion);
             $resultados = $resultado->ret_matriz();  
             $htmls='';
              foreach($resultados as $valor){
                $htmls.='<div><a class="suggest-element" data="'.$valor['cod_tercer'].' - '.$valor['nom_tercer'].'" id="'.$valor['cod_tercer'].'">'.$valor['nom_tercer'].'</a></div>';
              }
              echo utf8_decode($htmls);
        }

        function obtenerTransportadoraPerfil($ver = null){
            $resp = [];
            $resp['status'] = false;
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
                $cantidad = $consulta->ret_matriz();
                if($cantidad>0){
                    $resp['status'] = true;
                }
            }elseif($total>1){
                $resp['status'] = true;
            }

          }

        function getInfoTransp($cod_transp){
            $sql="SELECT a.cod_tercer, b.nom_tercer FROM 
                                ".BASE_DATOS.".tab_tercer_emptra a 
                     INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                            ON a.cod_tercer = b.cod_tercer
                            WHERE a.cod_tercer = '".$cod_transp."'";
        
              $resultado = new Consulta($sql, self::$conexion);
              $resultados = $resultado->ret_matriz()[0];
              return $resultados;
        }

        function getInfo(){
            $inf_transp = self::getInfoTransp($_POST['cod_transp']);
            $filas = self::armaFilas();
            $html='<div class="row mt-1">
                        <div class="col-md-12 col-sm-12">
                            <div id="accordion">
                                <div class="card">
                                    <div class="collapse show">
                                        <div class="card-header headCard">
                                            <div class="row vcenter">
                                                <div class="col-md-12">
                                                    <h3 style="color:#fff !important;    margin-block-end: 0; display: inline; position: absolute; font-weight: 450;">Pagos Centro Logistico Faro</h3>
                                                    <i style="font-size: 75px; word-wrap: break-word;position: absolute; left: 0; top: 0; left: 260;width: 10px; height: 10px;color: #ffffff75;" class="fa fa-money" aria-hidden="true"></i>
                                                    <i style="font-size: 45px; word-wrap: break-word;position: absolute; left: 350; top: -8; width: 10px; height: 10px;color: #ffffff75;" class="fa fa-money" aria-hidden="true"></i>
                                                    <i style="font-size: 35px; word-wrap: break-word;position: absolute; left: 370; top: 35; width: 10px; height: 10px;color: #ffffff75;" class="fa fa-money" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="row vcenter">
                                                <div class="col-md-12">
                                                    <h5 style="color:#fff !important;position: absolute;top: 36;">RESUMEN DE LA FACTURACIÓN: '.utf8_encode($inf_transp['nom_tercer']).'</h5>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="offset-md-1 col-md-10">
                                                    <table class="table table-bordered" id="inf_tabfac">
                                                        <thead class="theadc">
                                                            <tr>
                                                                <th nowrap>Num. Factura</th>
                                                                <th nowrap>Fecha de Facturación</th>
                                                                <th nowrap>Fecha de Vencimiento</th>
                                                                <th nowrap>Valor de la factura</th>
                                                                <th nowrap>Saldo</th>
                                                                <th nowrap>Ver</th>
                                                                <th nowrap>Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            '.$filas.'
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            ';
            echo utf8_decode($html);
        }

        function armaFilas(){
            $cod_tercero = $_POST['cod_transp'];
            $facturas = json_decode(file_get_contents(CONSULTA_FACTURACION_CLIENT . $cod_tercero), true);
            $html.='';
            foreach($facturas as $factura){
                $dir_pdf_bajar = IMPRESION_FACTURA_CLIENTE . $factura['num_factur'];
                $saldo = $factura['saldo'];
                $clase = '';
                if($saldo>0){
                    $clase="rowWarning";
                }
                $html.='<tr class="'.$clase.'">
                            <td class="text-center">'.$factura['num_factur'].'</td>
                            <td>'.$factura['fec_factur'].'</td>
                            <td>'.$factura['fec_vencin'].'</td>
                            <td>$ ' . number_format($factura['val_totalx'], 2) .'</td>
                            <td>';
                            
                if($saldo <= 0){
                    $html.='---';
                }else{
                    $html.= '$ '.number_format($factura['saldo'], 2);
                }
                $html.='    </td>
                            <td class="text-center"><a href="' . $dir_pdf_bajar . '" target="_blank" rel="noopener noreferrer"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                            <td class="text-center">';
                if ($saldo > 0) {
                    $html.='<a href="https://www.psepagos.co/PSEHostingUI/ShowTicketOffice.aspx?ID=11303" target="_blank" ><i class="fa fa-money mr-1"></i> Cancelar</a>';
                            } else {
                    $html.='Cancelada';
                            }
               $html .= '   </td>
                        </tr>';
            }
            return $html;
        }

        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaci�n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que ser� analizado por la funci�n
           *  \return: array
        */
        function cleanArray($array){

            $arrayReturn = array();

            //Convert function
            $convert = function($value){
                if(is_string($value)){
                    return utf8_encode($value);
                }
                return $value;
            };

            //Go through data
            foreach ($array as $key => $value) {
                //Validate sub array
                if(is_array($value)){
                    //Clean sub array
                    $arrayReturn[$convert($key)] = self::cleanArray($value);
                }else{
                    //Clean value
                    $arrayReturn[$convert($key)] = $convert($value);
                }
            }
            //Return array
            return $arrayReturn;
        }
}

new AjaxFacturResume();




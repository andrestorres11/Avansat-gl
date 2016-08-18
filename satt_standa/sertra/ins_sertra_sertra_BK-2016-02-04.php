<?php
/* ! \file: ajax_certra_certra.php
 *  \brief: archivo con multiples funciones para la configuracion del os tipos de servicio de una transportadora
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 19/11/2015
 *  \bug: 
 *  \bug: 
 *  \warning:
 */
    
    session_start();
    
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);





    
    class ins_sertra_sertra{
        var $conexion;
        var $cFunction;


        function __construct($conexion, $us, $ca){

            @include_once('../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php');

            $this->cFunction = new Despac($conexion, $us, $ca);
            $this->conexion = $conexion;
            if(empty($_REQUEST[option]) && !isset($_REQUEST[option]))
                $_REQUEST[option] = 'showFilters';
            
            $this -> $_REQUEST[option]( $_REQUEST );
        }

       
        function showFilters(){
            header('charset: ISO-8859-1');  
            
            $tipoDespacho = $this->cFunction->getTipoDespac();
            $transp = $this->cFunction->getTransp();

            $total = count($transp);
            if( $total == 1 ){
              $mCodTransp = $transp[0][0];
              $mNomTransp = $transp[0][1];
            }
            
            ?>
            </table>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/highcharts.js"></script>            
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/ins_sertra_sertra.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'>
            <div id="acordeonID" class="col-md-12 accordion defecto">
              <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Tipo de Servicio</b></h1>
              <div id="contenido">
                <div  class="Style2DIV">
                  <table width="100%" cellspacing="0" cellpadding="0">
                   
                    <tr class="Style2DIV">
                      <td class="contenido" colspan="6" style="text-align:center">
                        <?php if($total > 1){ ?>
                        <div class="col-md-5" style="text-align:right">Transportadora<font style="color:red">*</font>: </div>
                        <div class="col-md-2" style="text-align:left"><input type="text" id="nom_transpID" name="nom_transp" style="width:100%"></div>
                        <div class="col-md-5" style="text-align:left" id="boton"></div>
                        <?php } ?>                   
                        <input type="hidden" name="standa" id="standaID" value="<?= DIR_APLICA_CENTRAL ?>"> 
                        <input type="hidden" name="window" id="windowID" value="central"> 
                        <input type="hidden" name="cod_transp" id="cod_transpID" value=""> 
                        <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST['cod_servic'] ?>"> 
                        <input type="hidden" name="nom" id="nom_transp" value="<?= $mNomTransp ?>"> 
                      </td>
                    </tr>
                </table>
                </div>
              </div>
            </div>
            <div id="form3" class="col-md-12" style="display:none"></div>            
            <div id="PopUpID" class="col-md-12"></div>            
            <?php
        }
        
    
      
    }
    $service = new ins_sertra_sertra($this->conexion,$this->usuario_aplicacion,$this->codigo);
?> 

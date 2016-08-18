<?php
    
    session_start();
    
   /* ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);*/





    
    class InfEalCumplidasEnRuta{
        var $conexion;
        var $cFunction;
        var $cInform;


        function __construct($conexion, $us, $ca){

            @include_once('../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php');
            @include_once('../' . DIR_APLICA_CENTRAL . '/inform/ajax_inform_inform.php');

            $this->cFunction = new Despac($conexion, $us, $ca);
            $this->cInform = new inform($conexion, $us, $ca);
            $this->conexion = $conexion;
            if(empty($_REQUEST[option]) && !isset($_REQUEST[option]))
                $_REQUEST[option] = 'showFilters';
            
            $this -> $_REQUEST[option]( $_REQUEST );
        }

        function getExcelEal(){
            $archivo = "Informe_Eal_cumplidas_en_ruta_".date( "Y_m_d" ).".xls";
            header('Content-Type: application/octetstream');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="'.$archivo.'"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo $_REQUEST ['LIST_TOTAL'];
        }
        
        function showFilters(){
            
            $tipoDespacho =     $this->cFunction->getTipoDespac();
            $transp =           $this->cFunction->getTransp();
            $tipoTransporte =   $this->cInform->getTipoTransporte();
            $total = count($transp);
            if( $total == 1 ){
              $mCodTransp = $transp[0][0];
              $mNomTransp = $transp[0][1];
            }
            
            ?>
            </table>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/highcharts.js"></script>            
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/inf_ealcum_rutaxx.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/multiselect/jquery.multiselect.filter.min.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/multiselect/jquery.multiselect.min.js"></script>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/multiselect/jquery.multiselect.css' type='text/css'>
            <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>
            <div id="acordeonID" class="col-md-12 accordion ancho">
              <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Eal Cumplidas en Ruta</b></h1>
              <div id="contenido">
                <div  class="Style2DIV">
                  <table width="100%" cellspacing="0" cellpadding="0" class="Style2DIV">
                    <tr>
                        <th class="CellHead" colspan="6" style="text-align:center"><label id="notify"><b>Ingrese los  Par&aacute;metros de consulta</b></label></th>
                    </tr>
                    <tr class="contenido" >
                        <td colspan="6" style="text-align:center">
                        <?php if($total > 1){ ?>
                        <div class="col-md-3 derecha" >Transportadora<font style="color:red">*</font>: </div>
                        <div class="col-md-3 izquierda"><input type="text" id="nom_transpID" name="nom_transp" class="ancho"></div>
                        <?php } ?>
                        <div class="col-md-3 derecha">Tipo de Despacho</div>
                        <div class="col-md-3 izquierda">
                            <select class="multi" id='tip_despacID' name='tip_despac'>
                                <option value="">Todos</option>
                                <?php foreach ($tipoDespacho as $key => $value) {
                                    ?>
                                    <option value="<?= $value[0] ?>"><?= $value[1] ?></option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                        </td>
                    </tr>
                    <tr class="contenido">
                        <td>
                        <div class="col-md-2 derecha">Tipo de Transporte:</div>
                        <div class="col-md-2 izquierda">
                            <select id="tip_transpID" name="tip_transp" class="ancho" onchange="verificar()">
                                <option value="">Todos</option>
                                <?php foreach ($tipoTransporte as $key => $value) {
                                   ?>
                                  <option value="<?= $value['cod_tiptra'] ?>"><?= $value['nom_tiptra'] ?></option>
                                   <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-2 derecha">Poseedores: </div>
                        <div class="col-md-6 derecha" id="poseedores"><b>NO APLICA</b></div>
                        </td>
                    </tr>
                    <tr class="contenido">
                        <td>
                            <div class="col-md-12"></div>
                        <div class="col-md-2">No. Despacho:</div>
                        <div class="col-md-2"><input id='num_despacID' name="num_despac" class="ancho" type="text"></div>
                        <div class="col-md-2">No. Manifiesto</div>
                        <div class="col-md-2"><input id="num_manifiID" name="num_manifi" class="ancho" type="text"></div>
                        <div class="col-md-2">No. Viaje</div>
                        <div class="col-md-2"><input id="num_viajexID" name="num_viajex" class="ancho" type="text"></div>
                        <div class="col-md-12">
                            
                        </div>
                        <div class="col-md-2">Fecha Inicial<font style="color:red">*</font>: </div>
                        <div class="col-md-2"><input class="ancho" type="text" maxlength="10" size="10" id="fec_iniciaID" name="fec_inicia" readonly="" name="fec_inicia" ></div> 
                        <div class="col-md-2">Fecha Final<font style="color:red">*</font>: </div>
                        <div class="col-md-2"><input class="ancho" type="text" maxlength="10" size="10" id="fec_finaliID" name="fec_finali" readonly="" name="fec_finali" ></div>
                            <form style="display:none" id="eal" name="eal" method="post" action="../<?= DIR_APLICA_CENTRAL ?>/lib/exportExcel.php">                   
                            <input type="hidden" name="standa" id="standaID" value="<?= DIR_APLICA_CENTRAL ?>"> 
                            <input type="hidden" name="window" id="windowID" value="central"> 
                            <input type="hidden" name="cod_transp" id="cod_transpID" value="<?= $mCodTransp ?>"> 
                            <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST['cod_servic'] ?>"> 
                            <input type="hidden" name="OptionExcel" id="OptionExcelID" value="_REQUEST"> 
                            <input type="hidden" name="exportExcel" id="exportExcelID" value=""> 
                            <input type="hidden" name="nameFile" id="nameFileID" value="Detalle_eal_cumplidas_en_ruta"> 
                            <input type="hidden" name="option" id="optionID" value="">                    
                        </form>
                      </td>
                    </tr>
                </table>
                </div>
              </div>
            </div>
            <div class="col-md-12 tabs ancho" id="tabs">
               <ul>
                   <li><a id="liGenera" href="#generaID" style="cursor:pointer" onclick="getInforme(1)">REPORTE</a></li>
                   <li><a id="liEsfera" href="#esferaID" style="cursor:pointer" onclick="getInforme(2)">ESFERAS</a></li>
                </ul>
                <div class="col-md-12" id="generaID" ></div>
                <div class="col-md-12" id="esferaID" ></div>
            </div>
            <?php
        }
        
    
      
    }
    $service = new InfEalCumplidasEnRuta($this->conexion,$this->usuario_aplicacion,$this->codigo);
?> 

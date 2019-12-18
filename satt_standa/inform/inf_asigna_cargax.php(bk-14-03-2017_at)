<?php
    
    class inf_asigna_cargax{
        var $conexion;
        var $cInform;

        function __construct($conexion, $us, $ca){
            @include_once('../' . DIR_APLICA_CENTRAL . '/inform/ajax_inform_inform.php');

            $this->cInform = new inform($conexion, $us, $ca);
            $this->conexion = $conexion;

            if(empty($_REQUEST[option]) && !isset($_REQUEST[option])) {
                $_REQUEST[option] = 'showFilters';
            }
            
            $this -> $_REQUEST[option]( $_REQUEST );
        }

        
        /* ! \fn: showFilters
         *  \brief: muestra los filtros iniciales para el informe
         *  \author: Ing. Alexander Correa
         *  \date: 08/02/2016
         *  \date modified: dia/mes/año
         *  \param: 
         *  \return 
         */
        function showFilters(){
            $modalidades = $this->cInform->getModalidades();
            ?>
            </table>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/highcharts.js"></script>            
            <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/inf_asigna_cargax.js"></script>
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
              <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Informe de Asignación de Carga</b></h1>
              <div id="contenido">
                <div  class="Style2DIV">
                  <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <th class="CellHead" colspan="6" style="text-align:center"><label id="notify"><b>Ingrese los  Parámetros de consulta</b></label></th>
                    </tr>
                    <tr class="Style2DIV">
                      <td class="contenido" colspan="6" style="text-align:center">
                        <div class="col-md-2">Fecha Inicial<font style="color:red">*</font>: </div>
                        <div class="col-md-2"><input class="ancho text-center" type="text" maxlength="10" obl="1" minlength="10" validate="date" size="10" id="fec_iniciaID" name="fec_inicia" readonly="" name="fec_inicia" ></div> 
                        <div class="col-md-2">Fecha Final<font style="color:red">*</font>: </div>
                        <div class="col-md-2"><input class="ancho text-center" type="text" maxlength="10" obl="1" minlength="10" validate="date" size="10" id="fec_finaliID" name="fec_finali" readonly="" name="fec_finali" ></div>
                        <div class="col-md-2">Modalidad</div>
                        <div class="col-md-2">
                            <select class="ancho" id="modalidad" name="modalidad">
                                <option value="">Todas</option>
                                <?php
                                foreach ($modalidades as $key => $value) {
                                ?>
                                    <option value="<?= $value['cod_tipdes'] ?>"><?= $value['nom_tipdes'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <form id="asignacarga" name="carga" method="post" action="../<?= DIR_APLICA_CENTRAL ?>/lib/exportExcel.php">                   
                            <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"> 
                            <input type="hidden" name="window" id="windowID" value="central"> 
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
                   <li><a id="liGenera" href="#generaID" style="cursor:pointer" onclick="getInforme(1)">INFORME DESPACHOS</a></li>
                   <li><a id="liGenera" href="#cargaxID" style="cursor:pointer" onclick="getDetalleCargax(1)">INFORME CARGA LABORAL</a></li>
                </ul>
                <div class="col-md-12" id="generaID" ></div>
                <div class="col-md-12" id="cargaxID" ></div>
            </div>
            <?php
        }
        
    
      
    }
    $service = new inf_asigna_cargax($this->conexion,$this->usuario_aplicacion,$this->codigo);
?> 

<?php

require "ajax_extenc_extenc.php";

class Ins_extenc_extenc {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
    	$mHtml = new FormLib(2);
    	#incluyo css y js para las validaciones
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");
        echo $mHtml->MakeHtml();

        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new extenc($co, $us, $ca);
        switch ($_REQUEST[option]) {

          case 'getExcelLlamadas':
              $this->getExcelLlamadas();
          break;

            default:
                $this->filtro();
            break;
        }
    }
    /*! \fn: filtro
     *  \brief: funcion inicial para listar registrar extenciones
     *  \author: Ing. Alexander Correa
     *	\date: 04/12/2015
     *	\date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    
    function filtro() {
    	
        $operaciones = self::$cFunciones->getTipoDeOperacion();
        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("ins_extenc_extenc");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("new_ajax"); 
        $mHtml->SetJs("validator");
        $mHtml->SetJs("bootstrap");
        $mHtml->SetCss("bootstrap");
        $mHtml->SetCss("informes");
        $mHtml->SetCssJq("jquery");
        $mHtml->CloseTable("tr");
        # incluye Css
        $mHtml->Body(array("menubar" => "no"));
        $mHtml->setBody('<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI.js"></script>');
       
        $hoy = date("Y-m-d");
        $inicial = strtotime('-7 day', strtotime($hoy));
        $inicial = date('Y-m-d', $inicial);
        echo "</table>";

        ?>
        <style type="text/css">
          .ui-datepicker-title{
            color: black !important;
          }
        </style>
        <form name="formulario" id="formulario" action="../<?= DIR_APLICA_CENTRAL ?>/lib/exportExcel.php" method="post">
            <input type="hidden" name='nameFile' id='nameFileID' value="informe_llamadas_entrantes">
            <input type="hidden" name='OptionExcel' id='OptionExcelID' value='_REQUEST'>
            <input type="hidden" name='exportExcel' id='exportExcelID' value=''>
        </form>
        <div id="acordeonID" class="col-md-12 accordion">
          <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h1>
          <div id="contenido">
            <div  class="Style2DIV">
              <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:center"><b>Seleccione Par&aacute;metros de B&uacute;squeda</b></th>
                </tr>
                <tr >
                 	<td class="CellHead contenido" style="text-align:center">
                 		<div class="col-md-6">Tipo de Operaci&oacute;n<font style="color:red">*</font>:</div>
                 		<div class="col-md-6"><?= $operaciones ?></div>
                 	</td>
                </tr>
                <tr>
                  <td class="CellHead contenido" colspan="6" style="text-align:center">
                    <div class="col-md-3">Fecha Inicial<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" style="width: 100%" value="<?= $inicial ?>" size="10" id="fec_iniciaID" readonly="" name="fec_inicia"></div>
                    <div class="col-md-3">Fecha Final<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" style="width: 100%" value="<?= $hoy ?>" size="10" id="fec_finaliID" readonly="" name="fec_finali"></div>
                    <div class="col-md-3">Celular: </div>
                    <div class="col-md-3"><input type="text" minlength="10" style="width: 100%" maxlength="10" size="10" id="num_celulaID" name="num_celula"></div>
                    <div class="col-md-3">Sub Operacion: </div>
                    <div class="col-md-3"><select id="cod_subopeID"></select></div>
                  </td>
                </tr>
                    <div id="ocultos" style="display:block">
                      <input type="hidden" name="standa" id="standaID" value="<?= DIR_APLICA_CENTRAL ?>"> 
                      <input type="hidden" name="window" id="windowID" value="central"> 
                      <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST['cod_servic'] ?>"> 
                    </div>
              </table>
            </div>
          </div>
        </div>
          <div class="col-md-12 tabs" id="tabs">
             <ul>
               <li><a id="liGenera" href="#generaID" style="cursor:pointer" onclick="informeLlamadasEntrantes('generaID')">INFORME</a></li>
              <!-- <li><a id="liContes" href="#contesID" onclick="informeLlamadasEntrantes('contesID')">CONTESTADAS</a></li>
               <li><a id="liNocont" href="#nocontID" onclick="informeLlamadasEntrantes('nocontID')">NO CONTESTADAS</a></li>
               <li><a id="liOtrasx" href="#otrasxID" onclick="informeLlamadasEntrantes('otrasxID')">OTRAS</a></li> -->
             </ul>

             <div class="col-md-12" id="generaID"></div>
             <!-- <div class="col-md-12" id="contesID"></div>
             <div class="col-md-12" id="nocontID"></div>
             <div class="col-md-12" id="otrasxID"></div> -->
          </div>
          <div cass="col-md-12" id="hidden" style="display:none"></div>
        <?php
        $mHtml->CloseBody();

        # Muestra Html
        echo $mHtml->MakeHtml();
    }

    function getExcelLlamadas(){
      $archivo = "Listado_de_llamadas.xls";
      header('Content-Type: application/octetstream');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.$archivo.'"');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      flush();
      echo $_REQUEST['datosPintar'];
    }
}

//FIN CLASE
$proceso = new Ins_extenc_extenc($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
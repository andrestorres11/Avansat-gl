<?php
/*! \file: ind_soluci_noveda.php
 *  \brief: Archivo para crear el formulario del informe
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: 
 *  \bug: 
 *  \warning: 
 */
    
require "ajax_soluci_noveda.php";
 
class IndicadorSolucionNovedades {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new noveda($co, $us, $ca);
        switch ($_REQUEST[opcion]) {
            default:
                $this->filtro();
            break;
        }
    }

      /*! \fn: filtro
     *  \brief: funcion inicial para realizar el filtro de el informe
     *  \author: Ing. Alexander Correa
     *  \date: 29/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    function filtro() {

        include_once( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
        echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        // echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/boostrap.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.blockUI.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ind_soluci_noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.table2excel.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/bootstrap.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>";

        $productos =  self::$cFunciones->getProductos();
        $tiposDespachos =  self::$cFunciones->getTiposDespachos();

        $hoy = date("Y-m-d");
        $inicial = strtotime('-7 day', strtotime($hoy));
        $inicial = date('Y-m-d', $inicial);

        ?>

        </table>

        <div id="acordeonID" class="col-md-12 ancho">
          <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h1>
          <div id="contenido">
            <div  class="Style2DIV">
            <form id="form_novedadesID" method="post" action="../<?= DIR_APLICA_CENTRAL ?>/lib/exportExcel.php" name="form_novedades">
                <input id="nameFileID" type="hidden" value="Informe_solucion_novedad" name="nameFile">
                <input id="OptionExcelID" type="hidden" value="_SESSION" name="OptionExcel">
            </form>
              <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:center"><b>Seleccione Par&aacute;metros de B&uacute;squeda</b></th>
                </tr>
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:left">Producto</th>
                </tr>
                <tr class="Style2DIV">
                   <td class="CellHead contenido" colspan="6" style="text-align:right">
                   <?php 
                    foreach ($productos as $key => $value) {
                     ?>
                        <div class="col-md-3" style="text-align:rigth"><?= utf8_encode($value['nom_produc']) ?> :</div>
                        <div class="col-md-1" style="text-align:left" ><input type="checkbox" name="producto[]" value="<?= $value['cod_produc'] ?>" ></div>
                     <?php
                    } 
                    ?>
                    
                  </td>
                </tr>
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:left">Tipo de Despacho</th>
                </tr>
                <tr class="Style2DIV">
                   <td class="CellHead contenido" colspan="6" style="text-align:right">
                   <?php 
                    foreach ($tiposDespachos as $key => $value) {
                     ?>
                        <div class="col-md-3" style="text-align:rigth"><?= utf8_encode($value['nom_tipdes']) ?> :</div>
                        <div class="col-md-1" style="text-align:left " ><input type="checkbox" name="tipdes[]" value="<?= $value['cod_tipdes'] ?>" ></div>
                     <?php
                    } 
                    ?>
                    
                  </td>
                </tr>
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:left">Fechas</th>
                </tr>
                <tr class="Style2DIV">
                  <td class="CellHead contenido" colspan="6" style="text-align:center">
                    <div class="col-md-3">Fecha Inicial<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" value="<?= $inicial ?>" size="10" id="fec_iniciaID" readonly="" name="fec_inicia" ></div>
                    <div class="col-md-3">Fecha Final<font style="color:red">*</font>: </div>
                    <div class="col-md-3"><input type="text" maxlength="10" value="<?= $hoy ?>" size="10" id="fec_finaliID" readonly="" name="fec_finali" ></div>
                  </td>
                </tr>
                <tr class="Style2DIV">
                  <td class="CellHead contenido" colspan="6" style="text-align:center">
                    <div class="col-md-12">&nbsp;</div>
                    <div id="ocultos" style="display:none" class="col-md-12">
                      <input type="hidden" name="standa" id="standaID" value="<?= DIR_APLICA_CENTRAL ?>"> 
                      <input type="hidden" name="window" id="windowID" value="central"> 
                      <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST['cod_servic'] ?>"> 
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
          <div class="col-md-12 tabs ancho" id="tabs">
             <ul>
               <li><a id="liGenera" href="#generaID">GENERAL</a></li>
               <li><a id="liTransi" href="#transiID">TRÁNSITO</a></li>
               <li><a id="liCargue" href="#cargueID">CARGUE</a></li>
               <li><a id="liDescar" href="#descarID">DESCARGUE</a></li>
             </ul>

             <div class="col-md-12" id="generaID"></div>
             <div class="col-md-12" id="transiID"></div>
             <div class="col-md-12" id="cargueID"></div>
             <div class="col-md-12" id="descarID"></div>
          </div>
          <div cass="col-md-12" id="hidden" style="display:none"></div>
          <style type="text/css">
              select.ui-datepicker-year,select.ui-datepicker-month
              {
                color: black;
              }
          </style>
        <?php
    }

}

$proceso = new IndicadorSolucionNovedades($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>
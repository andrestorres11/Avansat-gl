<?php

session_start();

class ins_horari_monito{
  var $conexion;
  var $cFunction;
  var $cInform;


  function __construct($conexion, $us, $ca){
    @include_once('../' . DIR_APLICA_CENTRAL . '/config/ajax_horari_monito.php');
    
    $this->cFunction = new ajax_horari_monito($conexion, $us, $ca);
    
    $this->conexion = $conexion;
    if(empty($_REQUEST[option]) && !isset($_REQUEST[option]))
      $_REQUEST[option] = 'showFilters';

    $this -> $_REQUEST[option]( $_REQUEST );
  }

  function showFilters(){

    ?>
    </table>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/ins_horari_monit2.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/multiselect/jquery.multiselect.filter.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/multiselect/jquery.multiselect.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/dinamic_list.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/dinamic_list.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/multiselect/jquery.multiselect.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>
    <div id="acordeonID" class="col-md-12 accordion ancho">
      <h1 style="padding: 6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Pre-Planeaci&oacute;n carga laboral</b></h1>
      <div id="contenido" style="display: none">
        <div  class="Style2DIV" >  
          <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <th class="CellHead text-center" colspan="6" ><label id="notify"><b>Asignaci&oacute;n de controladores para Pre-Planeaci&oacute;n de la carga laboral</b></label>&nbsp;&nbsp;<input onclick="addUserForm()" type="button" name="agregar" id="agregar" class="small save  ui-widget ui-state-default ui-corner-all" value="Agregar Nuevo"></th>
            </tr>
            <tr class="Style2DIV">
              <td class="contenido" colspan="6" id="primero" style="text-align:center">
                <div class="col-md-12" id="div0">
                  <div class="col-md-1 derecha" >Usuario<font style="color:red">*</font>: </div>
                  <div class="col-md-1 izquierda">
                  <input type="text" id="nom_usuari0ID" validate="dir" obl="1" maxlength="30" minlength="5" name="nom_usuari[]" class="ancho">
                  <input type="hidden" name="cod_consec[]" id="cod_consec0ID" value="">
                  </div>
                  <div class="col-md-1 derecha">Fecha y Hora de Inicio<font style="color:red">*</font>:</div>
                  <div class="col-md-3 izquierda">
                    <div class="col-md-7"><input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fecini[]" id="fecini0"></div>
                    <div class="col-md-5"><input class="ancho hora" validate="dir" obl="1" maxlength="5" minlength="5" type="text" name="horini[]" id="horini0"></div>
                  </div>
                  <div class="col-md-2 derecha">Fecha y Hora de salida<font style="color:red">*</font>:</div>
                  <div class="col-md-3 izquierda">
                    <div class="col-md-7"><input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fecsal[]" id="fecsal0" ></div>
                    <div class="col-md-5"><input class="ancho hora" validate="dir" obl="1" maxlength="5" minlength="5" type="text" name="horsal[]" id="horsal0" ></div>
                  </div>
                  <div class="col-md-1"><img width="16px" height="16px" src="../<?= DIR_APLICA_CENTRAL ?>/images/delete.png" onclick="deleteDiv(0)"></div>
                </div>
              </td>
            </tr>
            <tr>
              <td> <div class="col-md-12 centrado"><input type="button" name="aceptar" id="aceptar" class="small save  ui-widget ui-state-default ui-corner-all" value="Registrar" onclick="registrar()"></div>
              </td>
            </tr>
          </table>
          <form id="form_monito">
            <input type="hidden" name="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>" ></input>  
            <input type="hidden" name="window" value="central" ></input>  
          </form>
          <input type="hidden" name="total" id="total" value="1"></input>
          <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"></input>
        </div>
      </div>
    </div>

    <div id="acordeonID" class="col-md-12 accordion ancho">
    <h1 style="padding: 6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Filtro carga laboral</b></h1>
      <div id="contenido">
        <div  class="Style2DIV">  
          <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <th class="CellHead text-center" colspan="6" ><label id="notify"><b>Filtrar por usuario</b></label>&nbsp;&nbsp;</th>
            </tr>
            <tr class="Style2DIV">
              <td class="contenido" colspan="6" id="primero" style="text-align:center">
                <div class="col-md-12" id="div0">

                  <div class="col-xs-4 col-sm-4 col-md-4">
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="fechinifiltro">Fecha Inicio<font style="color:red">*</font>:</label>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 ">
                      <input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fechinifiltro" id="fechinifiltro">
                    </div>
                  </div>

                  
                  <div class="col-xs-4 col-sm-4 col-md-4">
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="fechfinfiltro">Fecha Salida<font style="color:red">*</font>:</label>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 ">
                      <input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fechfinfiltro" id="fechfinfiltro">
                    </div>
                  </div>

                  <div class="col-xs-4 col-sm-4 col-md-4">
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="user">Usuario<font style="color:red">*</font>:</label>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 ">
                    <input type="text" id="nom_usuari0IDfil" validate="dir" obl="1" maxlength="30" minlength="5" name="nom_usuarifil[]" class="ancho">
                    <input type="hidden" name="cod_consecfil[]" id="cod_consecfil0ID" value="">
                    </div>
                  </div>

                </div>

              </td>
            </tr>
            <tr>
              <td>  <div class="col-md-12 centrado"><input type="button" name="filtrar" id="filtrar" class="small save ui-widget ui-state-default ui-corner-all" value="Filtrar" onclick="valfiltro()"></div>
              </td>
            </tr>
          </table>
          <br>
        </div>
        
      </div>
    </div>

    <div class="col-md-12 ancho" id="lista">oscar</div>
    <?php
  }

}
$service = new ins_horari_monito($this->conexion,$this->usuario_aplicacion,$this->codigo);
?> 

<?php
 
session_start();

class ins_homolo_noveda{
  var $conexion;
  var $cFunction;
  var $cInform;


  function __construct($conexion, $us, $ca){
    @include_once('../' . DIR_APLICA_CENTRAL . '/homnov/ajax_homolo_noveda.php');
    
    $this->cFunction = new ajax_homolo_noveda($conexion, $us, $ca);
    
    $this->conexion = $conexion;
    if(empty($_REQUEST[option]) && !isset($_REQUEST[option]))
      $_REQUEST[option] = 'showFilters';

    $this -> $_REQUEST[option]( $_REQUEST );
  }

  function showFilters(){

    ?>
    </table>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery17.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/ins_homolo_noveda.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>
    <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/multiselect/jquery.multiselect.css' type='text/css'>
    <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>
    <div id="acordeonID" class="col-md-12 accordion ancho ui-accordion ui-widget ui-helper-reset ui-accordion-icons" role="tablist">
      <h1 style="padding: 6px;" class="ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top" style="padding: 6px;" role="tab" aria-expanded="true" tabindex="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Homologar Novdades - Insertar</b></h1>
      <div id="contenido" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" style="height: auto;" role="tabpanel">
        <div class="Style2DIV">
          <input type="hidden" name="total" id="total" value="1"></input>
          <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"></input>
          <form id="tablaHomNov">
            <table width="100%" cellspacing="0" cellpadding="0" id="homEst">
              <tr>
                <th class="CellHead text-center" colspan="6" ><label id="notify"><b>Homologar proceso</b></label>&nbsp;&nbsp;</th>
              </tr>
              <tr class="Style2DIV">
                <td class="contenido" colspan="6" id="primero" style="text-align:center">
                  <div class="col-md-12" id="div0">
                    <div class="col-md-1 centrado">Fila:</div>
                    <div class="col-md-3 centrado">Proceso:</div>
                    <div class="col-md-4 centrado">Nombre Estado<font style="color:red">*</font>:</div>
                    <div class="col-md-4 centrado">Observaci&oacute;n<font style="color:red">*</font>:</div>
                  </div>
                </td>
              </tr>
              <?php 
                $cont = 1;
                foreach ($this->cFunction -> getDataListHom() as $key => $value) {
                  ?>
                    <tr class="Style2DIV">
                      <td class="contenido" colspan="6" id="primero" style="text-align:center">
                        <div class="col-md-12" id="div1">
                          <div class="col-md-0 centrado">
                            <input type="hidden" name="cod_estado[]" value="<?= $value['cod_estado'] ?>" id="cod_estado">
                          </div>
                          <div class="col-md-1 centrado">
                            <div style="padding-top: 10px;"><?= $cont?></div>
                          </div>
                          <div class="col-md-3 centrado">
                            <input type="text" id="proceso" readonly="readonly" validate="dir" obl="1" maxlength="30" minlength="5" name="proceso" class="ancho" value="<?= $value['nom_estado'] ?>">
                          </div>
                          <div class="col-md-4 centrado">
                            <select id="codEstcli_<?= $cont?>" validate="select" obl="1" minlength="1" name="cod_estcli[]" class="ancho">
                            <option value=""></option>
                              <?php 
                                foreach ($this->cFunction -> getSubDataFileNomEst() as $key => $valueOption) {
                                  if($value['cod_estcli'] == $valueOption['cod_estcli']){
                              ?>
                                <option value="<?= $valueOption['cod_estcli']?>" selected><?= $valueOption['nom_estcli']?></option>
                                  <?php 
                                    }else{
                                  ?>
                                <option value="<?= $valueOption['cod_estcli']?>"><?= $valueOption['nom_estcli']?></option>
                              <?php 
                                  }
                                }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-4 centrado">
                            <input type="text" id="obsHompro_<?= $cont?>" validate="dir" obl="1" maxlength="50" minlength="5" name="obs_hompro[]" value="<?= $value['obs_hompro']?>" class="ancho">
                          </div>
                        </div>
                      </td>
                    </tr>
                  <?php
                    $cont++;
                }
              ?>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0" id="homNov">
              <tr>
                <th class="CellHead text-center" colspan="6" ><label id="notify"><b>Homologar novedades</b></label>&nbsp;&nbsp;</th>
              </tr>
              <tr class="Style2DIV">
                <td class="contenido" colspan="6" id="primero" style="text-align:center">
                  <div class="col-md-12" id="div0">
                    <div class="col-md-1 centrado">Fila:</div>
                    <div class="col-md-3 centrado">Novedad<font style="color:red">*</font>:</div>
                    <div class="col-md-3 centrado">Nombre Estado<font style="color:red">*</font>:</div>
                    <div class="col-md-4 centrado">Observaci&oacute;n<font style="color:red">*</font>:</div>
                    <div class="col-md-1 centrado">Eliminar:</div>
                  </div>
                </td>
              </tr>
              <?php 
                $cont = 1;
                if(count($this->cFunction -> getDataFileNov()) == 0){
              ?>
                <tr class="Style2DIV" id="<?= $cont?>">
                  <td class="contenido" colspan="6" id="segundo" style="text-align:center">
                    <div class="col-md-12" id="div1">
                      <div class="col-md-1 centrado">
                        <div class='row' style="padding-top: 10px;"><?= $cont?></div>
                      </div>
                      <div class="col-md-3 centrado">
                        <select id="codNoveda_<?= $cont?>" validate="select" obl="1" maxlength="30" minlength="5" name="cod_noveda[]" class="ancho">
                        <option value=""></option>
                          <?php 
                            foreach ($this->cFunction -> getSubDataFileNov() as $key => $valueOption) {
                          ?>
                              <option value="<?= $valueOption['cod_noveda']?>"><?= $valueOption['cod_noveda'],'-', $valueOption['nom_noveda']?></option>
                          <?php 
                            }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-3 centrado">
                          <select id="codEstcliNov_<?= $cont?>" validate="select" obl="1" maxlength="30" minlength="5" name="cod_estcliNov[]" class="ancho">
                          <option value=""></option>
                            <?php 
                              foreach ($this->cFunction -> getSubDataFileNomEst() as $key => $valueOption) {
                            ?>
                                <option value="<?= $valueOption['cod_estcli']?>"><?= $valueOption['nom_estcli']?></option>
                              <?php 
                              }
                            ?>
                          </select>
                        </div>
                      <div class="col-md-4 centrado">
                        <input type="text" style="width:100%" id="observNov_<?= $cont?>" validate="dir" obl="1" maxlength="50" minlength="5" name="observNov[]" class="ancho">
                      </div>
                      <div class="col-md-1 centrado">
                        <font style="color:red; font-size: 22pt; cursor: pointer;" class="deleteRow" onclick="deleteDuplicateRow(<?= $cont?>)"  >-</font>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php 
                }else{
                  foreach ($this->cFunction -> getDataFileNov() as $key => $value) {
              ?>
                <tr class="Style2DIV" id="<?= $cont?>">
                  <td class="contenido" colspan="6" id="segundo" style="text-align:center">
                    <div class="col-md-12" id="div1">
                      <div class="col-md-1 centrado">
                        <div class='row' style="padding-top: 10px;"><?= $cont?></div>
                      </div>
                      <div class="col-md-3 centrado">
                        <select id="codNoveda_<?= $cont?>" validate="select" obl="1" maxlength="30" minlength="5" name="cod_noveda[]" class="ancho">
                        <option value=""></option>
                          <?php 
                            foreach ($this->cFunction -> getSubDataFileNov() as $key => $valueOption) {
                              if($value['cod_noveda'] == $valueOption['cod_noveda']){
                          ?>
                              <option value="<?= $valueOption['cod_noveda']?>" selected><?= $valueOption['cod_noveda'],'-', $valueOption['nom_noveda']?></option>
                            <?php 
                              }else{
                            ?>
                              <option value="<?= $valueOption['cod_noveda']?>"><?= $valueOption['cod_noveda'],'-', $valueOption['nom_noveda']?></option>
                          <?php 
                              }
                            }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-3 centrado">
                          <select id="codEstcliNov_<?= $cont?>" validate="select" obl="1" maxlength="30" minlength="5" name="cod_estcliNov[]" class="ancho">
                          <option value=""></option>
                            <?php 
                              foreach ($this->cFunction -> getSubDataFileNomEst() as $key => $valueOption) {
                                if($value['cod_estcli'] == $valueOption['cod_estcli']){
                            ?>
                                <option value="<?= $valueOption['cod_estcli']?>" selected><?= $valueOption['nom_estcli']?></option>
                            <?php 
                              }else{
                            ?>
                                <option value="<?= $valueOption['cod_estcli']?>"><?= $valueOption['nom_estcli']?></option>
                              <?php 
                                }
                              }
                            ?>
                          </select>
                        </div>
                      <div class="col-md-4 centrado">
                        <input type="text" style="width:100%" id="observNov_<?= $cont?>" validate="dir" obl="1" maxlength="50" minlength="5" name="observNov[]" class="ancho" value="<?= $value['obs_homnov']?>">
                      </div>
                      <div class="col-md-1 centrado">
                        <font style="color:red; font-size: 22pt; cursor: pointer;" class="deleteRow" onclick="deleteDuplicateRow(<?= $cont?>)"  >-</font>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php 
                    $cont++;
                  }
                }
              ?>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td> <div class="col-md-12 centrado">
                  <font style="color:blue; font-size: 22pt; cursor: pointer;" onclick="duplicateRow('homNov')">+</font>
                </td>
              </tr>
              <tr>
                <td> <div class="col-md-12 centrado"><input type="button" name="aceptar" id="aceptar" class="small save  ui-widget ui-state-default ui-corner-all" value="Registrar" onclick="registrar()"></div>
                </td>
              </tr>
            </table>
          </form>
          <form id="form_monito">
            <input type="hidden" name="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>">
            <input type="hidden" name="window" value="central" >
          </form>
          <input type="hidden" name="total" id="total" value="1">
          <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>">
        </div>
      </div>
    </div>
    <?php
  }
}
$service = new ins_homolo_noveda($this->conexion,$this->usuario_aplicacion,$this->codigo);
?> 

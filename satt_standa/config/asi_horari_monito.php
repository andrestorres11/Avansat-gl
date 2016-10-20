<?php
session_start();

class asi_horari_monito {

    var $conexion;
    var $cFunction;
    var $cInform;

    function __construct($conexion, $us, $ca) {
        @include_once('../' . DIR_APLICA_CENTRAL . '/config/ajax_horari_monito.php');

        $this->cFunction = new ajax_horari_monito($conexion, $us, $ca);
        $this->conexion = $conexion;

        if (empty($_REQUEST[option]) && !isset($_REQUEST[option]))
            $_REQUEST[option] = 'showFilters';

        $this->$_REQUEST[option]($_REQUEST);
    }

    /* ! \fn: showFilters
     *  \brief: muestra los filtros iniciales
     *  \author: Ing. Alexander Correa
     *  \date: 02/03/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return 
     */

    function showFilters() {
        ?>
        </table>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/asi_horari_monito.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>

        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'>

        <div id="contenido" class="accordion">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>PARAMETROS INICIALES</b></h3>
            <div name="sec">
                <div id="formID" class="Style2DIV" distriactual="basic">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr class="Style2DIV">
                            <td class="contenido">
                                <div class="col-md-12 centrado CellHead">
                                    <b>Tipo de Distribuci&oacute;n</b>
                                </div>
                                <div class="col-md-6 text-right">
                                    <label>Distribuci&oacute;n Automatica</label>
                                    <input onclick="limpiarDivs( $(this) )" type="radio" id="tipDistri_Basic" name="tipDistri" value="basic" checked>
                                </div>
                                <div class="col-md-6 text-left">
                                    <input onclick="limpiarDivs( $(this) )" type="radio" id="tipDistri_Manual" name="tipDistri" value="manual">
                                    <label>Distribuci&oacute;n Manual</label>
                                </div>

                                <div class="col-md-12 centrado CellHead">
                                    <b>Horario</b>
                                </div>
                                <div class="col-md-3 derecha">Fecha y Hora de Inicio<font style="color:red">*</font>:</div>
                                <div class="col-md-3 izquierda">
                                    <div class="col-md-7"><input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fec_inicio" id="fec_inicio"></div>
                                    <div class="col-md-5"><input class="ancho time" onclick="removeStyle('hor_inicio')" validate="dir" obl="1" maxlength="5" minlength="5" type="text" name="hor_inicio" id="hor_inicio"></div>
                                </div>
                                <div class="col-md-3 derecha">Fecha y Hora de salida<font style="color:red">*</font>:</div>
                                <div class="col-md-3 izquierda">
                                    <div class="col-md-7"><input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fec_finali" id="fec_finali" ></div>
                                    <div class="col-md-5"><input class="ancho time" onclick="removeStyle('hor_finali')" validate="dir" obl="1" maxlength="5" minlength="5" type="text" name="hor_finali" id="hor_finali" ></div>
                                </div>
                                <div class="col-md-12 centrado"><input type="button" onclick="listUsuarios();" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" value="Continuar" id="nuevo"></div>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>">
                </div><br>
            </div>
        </div>
        <div id="usuarios" class="accordion">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>RECURSOS DISPONIBLES</b></h3>
            <div name="sec">
                <div id="usuariosID" class="Style2DIV"></div><br>
            </div>
        </div>
        <div id="secundarios" class="accordion">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>PARAMETROS SECUNDARIOS</b></h3>
            <div name="sec">
                <div id="secundariosID" class="Style2DIV"></div><br>
            </div>
        </div>
        <div id="datos" class="accordion">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>DISTRIBUCION DE CARGA LABORAL</b></h3>
            <div name="sec">
                <div id="datosID" class="Style2DIV"></div><br>
            </div>
        </div>

        <form id="form_asi_monito">
            <input type="hidden" name="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>" >  
            <input type="hidden" name="window" value="central" >  
        </form>
        <?php
    }

}

$service = new asi_horari_monito($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
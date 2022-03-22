<?php
	
    require "ajax_transp_transp.php";

class Ins_grupox_grupox {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
    	?>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/ins_seguim_adicio.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/new_ajax.js"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/dinamic_list.js"></script>
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/dinamic_list.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/boostrap.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css" rel="stylesheet">
        <?php        
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new trans($co, $us, $ca);
        switch ($_REQUEST[opcion]) {
            default:
                $this->filtro();
                break;
        }
    }
    /*! \fn: filtro
     *  \brief: funcion inicial para listar registrar extenciones
     *  \author: Ing. Alexander Correa
     *	\date: 08/07/2016
     *	\date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    
    function filtro() {
        ?>
        </table>
        <form id="form_searchID" style="display:none" name="form_search" enctype="multipart/form-data" action="index.php" method="post">
            <input id="standa" type="hidden" name="standa" value="<?= DIR_APLICA_CENTRAL ?>" >
            <input id="window" type="hidden" value="central" name="window">
            <input id="cod_servic" type="hidden" value="<?= $_REQUEST['cod_servic'] ?>" name="cod_servic">
            <input id="opcion" type="hidden" value="" name="opcion">
            <input id="cod_consec" type="hidden" value="0" name="cod_consec">
        </form> 
        <div class="accordion">
            <h3 style="padding:6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Registrar Seguimiento Adicional</b></h3>
            <div id="sec1">
                <div class="Style2DIV">
                    <div class="contenido text-center">
                        <div class="col-md-4">
                            <div class="col-md-6 text-right negro">Transportadora<font style="color:red">*</font></div>
                            <div class="col-md-6">
                                <input type="text" id="nom_transp" name="nom_transp" class="text-center" obl="1" validate="dir" maxlength="50" minlength="3">
                                <input id="cod_transp" name="cod_transp" type="hidden" value="" name="cod_transp">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-5 text-right negro">Fecha y Hora incial<font style="color:red">*</font></div>
                            <div class="col-md-7 text-left">
                                <input type="text" class="text-center negro date" readonly id="fec_inicia" name="fec_inicia" size="11" obl="1" validate="fecha" maxlength="10" minlength="10" >&nbsp;&nbsp;&nbsp;
                                <input type="text" class="text-center negro time" onfocus="removeStyle('hor_inicia')" readonly id="hor_inicia" name="hor_inicia" size="6" obl="1" validate="dir" maxlength="5" minlength="5">
                            </div>                
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-5 text-right negro">Fecha y Hora Final<font style="color:red">*</font></div>
                            <div class="col-md-7 text-left">
                                <input type="text" class="text-center negro date" readonly id="fec_finali" name="fec_finali" size="11" obl="1" validate="fecha" maxlength="10" minlength="10" >&nbsp;&nbsp;&nbsp;
                                <input type="text" class="text-center negro time" onfocus="removeStyle('hor_finali')" readonly id="hor_finali" name="hor_finali" size="6" obl="1" validate="dir" maxlength="5" minlength="5">
                            </div>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                        <input type="button" id="registrar" name="boton" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" value="registrar" onclick="registrar()">                    
                    </div>
                </div>
            </div>
        </div>
        <div id="tabla"></div>	 
        <?php 	       
    } 


}

//FIN CLASE
$proceso = new Ins_grupox_grupox($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
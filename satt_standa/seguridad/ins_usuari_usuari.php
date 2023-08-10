<?php
/* ! \archive: ins_usuari_usuari.php
 *  \brief: archivo para el manejo de usuarios
 *  \author: Ing. Alexander Correa
 *  \author: aleander.correa@intrared.net
 *  \date: 08/04/206
 *  \date modified: dia/mes/a?o
 *  \warning:   
 *  \ 
 */
session_start();

require "ajax_perfil_perfil.php";

class ins_usuari_usuari {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
        #importa los  css y js necesarios
        ?>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/dinamic_list.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/usuari.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/new_ajax.js" language="javascript"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/dinamic_list.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css" rel="stylesheet">
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css" rel="stylesheet">
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css' type='text/css'>

        <?php
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new seguri($co, $us, $ca); 
        switch ($_REQUEST[opcion]) {
            case 1: //registrar nuevo
            case 2: //editar            
                $this->Formulario();
                break;
            default:
                $this->lista();
                break;
        }
    }

    /* ! \fn: lista
     *  \brief: funcion inicial que muestra una lista con los perfiles creados
     *  \author: Ing. Alexander Correa
     *  \date: 06/04/2016
     *  \date modified: dia/mes/a?o
     *  \param: 
     *  \return 
     */

    function lista() {

        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];
        ?>
        </table>
        <form action="index.php" method="post" name="form_search" id="form_searchID" enctype="multipart/form-data">        
            <div class="accordion">
                <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>LISTADO DE USUARIOS</b></h3>
                <div class="" id="sec2">
                    <div class="Style2DIV" id="form3">
        <?php
        echo self::$cFunciones->listaUsuarios();
        ?>
                    </div>
                </div>
            </div>            
            <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"></input>
            <input type="hidden" name="window" id="window" value="central"></input>
            <input type="hidden" name="cod_servic" id="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>"></input>
            <input type="hidden" name="opcion" id="opcion" value="1"></input>
            <input type="hidden" name="cod_usuari" id="cod_usuari" value=""></input>
            <input type="hidden" name="nom_usuari" id="nom_usuari" value=""></input>
            <input type="hidden" name="cod_consec" id="cod_consec" value=""></input>
            <input type="hidden" name="obs_histor" id="obs_histor" value=""></input>
        </form>
        <?php
    }

    /* ! \fn: formulario
     *  \brief: pinta el formulario para crear, editar y copiar un usuario
     *  \author: Ing. Alexander Correa
     *  \date: 08/04/2016
     *  \date modified: dia/mes/a?o
     *  \param: 
     *  \return 
     */

    function Formulario() {
        $data = (object) $_POST;       
        if ($data->opcion == 1) {
            $obl = "obl='1'";
            $font = '<font style="color:red">*</font>';
        }
        if($data->cod_usuari){
            $minUsuario = '3';
            $read = "readonly";
            $datos = self::$cFunciones->getDataUsuario($data->cod_consec);
        }else{
            $minUsuario = '5';
            $disp = 'none';
            $data->cod_usuari = 0;
        }
        $perfiles = self::$cFunciones->getPerfiles();
        $grupos = self::$cFunciones->getGrupos();

        ?>
        </table>
        <form style="display:none;" action="index.php" method="post" name="form_search" id="form_searchID" enctype="multipart/form-data">
            <input type="hidden" name="standa" value="<?= DIR_APLICA_CENTRAL ?>" id="standa"></input>
            <input type="hidden" name="window" id="window" value="central"></input>
            <input type="hidden" name="cod_servic" id="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>"></input>
        </form>
        <div class="accordion"  >
            <h1 style="padding:6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>INFORMACI&Oacute;N B&Aacute;SICA DEL USUARIO</b></h1>
            <div id="contenido">
                <div class="Style2DIV">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td style="text-align:center" class="CellHead contenido">
                                    <div class="col-md-12 ancho">
                                        <div class="col-md-3 text-right">Usuario<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center" type="text" <?= $read ?> name="cod_usuari" id="cod_usuari" value="<?= $datos->cod_usuari ?>" obl="1" maxlength="30" minlength="<?= $minUsuario ?>" validate="dir" onkeypress="return validarLetras(event)"></input>
                                            <input type="hidden" name="cod_consec" id="cod_consec" value="<?= $data->cod_consec ?>"></input>
                                        </div>
                                        <div class="col-md-3 text-right">C&eacute;dula:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center" type="text" name="num_cedula" id="num_cedula" value="<?= $datos->num_cedula ?>" validate="dir" maxlength="50" minlength="5"></input>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ancho">
                                        <div class="col-md-3 text-right">Contrase&ntilde;a<?= $font ?>:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center" <?= $obl ?> type="password" name="clv_usuari" id="clv_usuari" value="" maxlength="15" minlength="7" validate="dir"></input>                                    
                                        </div>
                                        <div class="col-md-3 text-right">Confirmar Contrase&ntilde;a<?= $font ?>:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center" <?= $obl ?>  type="password" name="con_passwo" id="con_passwo" value="" validate="dir" maxlength="15" minlength="7"></input>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ancho">
                                        <div class="col-md-3 text-right">Nombre<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center" <?= $obl ?> type="text" name="nom_usuari" obl="1" id="nom_usuari" value="<?= $datos->nom_usuari ?>" maxlength="50" minlength="6" validate="dir"></input>                                    
                                        </div>
                                        <div class="col-md-3 text-right">Correo<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center" <?= $obl ?>  type="text" obl="1" name="usr_emailx" id="usr_emailx" value="<?= $datos->usr_emailx ?>" validate="email" maxlength="50" minlength="8"></input>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ancho">
                                        <div class="col-md-3 text-right">Usuario Interfaz:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center"  type="text" name="usr_interf" id="usr_interf" value="<?= $datos->usr_interf ?>" maxlength="15" minlength="4" validate="texto"></input>                                    
                                        </div>
                                        <div class="col-md-3 text-right">Perfil<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <select id="cod_perfil" obl="1" validate="select" name="cod_perfil" class="ancho" onchange="mostrarOcultos()">
                                                <option value="">Seleccione una Opci&oacute;n</option>
                                                <?php foreach ($perfiles as $key => $value){ 
                                                    $cod_perfil = "";
                                                    if($value['cod_perfil'] == $datos->cod_perfil){
                                                        $cod_perfil = "selected='true'";
                                                    }
                                                    ?>
                                                    <option <?= $cod_perfil ?> value="<?= $value['cod_perfil'] ?>"><?= $value['nom_perfil'] ?></option>
                                                <?php } ?>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-3 text-right">Alias de usuario<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <input class="text-center"  type="text" name="ali_usuari" id="ali_usuari" value="<?= $datos->ali_usuari ?>" maxlength="15" minlength="4" validate="texto"></input>                                    
                                        </div>
                                    </div>                                    
                                    <div class="col-md-12 ancho" id="gru_pri" style="display:none">
                                        <div class="col-md-3 text-right">Prioridad<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <select id="cod_priori" name="cod_priori"  validate="select" class="ancho" >
                                                <option value="">Seleccione una Opci&oacute;n</option>
                                                <?php for ($i = 1; $i<= 3; $i++){ 
                                                    $cod_priori = "";
                                                    if($i == $datos->cod_priori){
                                                        $cod_priori = "selected='true'";
                                                    }
                                                    ?>
                                                    <option <?= $cod_priori ?> value="<?= $i ?>"><?= $i ?></option>
                                                <?php } ?>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-3 text-right">Grupo<font style="color:red">*</font>:</div>
                                        <div class="col-md-3 text-left">
                                            <select id="cod_grupox" name="cod_grupox"  validate="select" class="ancho" >
                                                <option value="">Seleccione una Opci&oacute;n</option>
                                                <?php foreach ($grupos as $key => $value){ 
                                                    $cod_grupox = "";
                                                    if($value['cod_grupox'] == $datos->cod_grupox){
                                                        $cod_grupox = "selected='true'";
                                                    }
                                                    ?>
                                                    <option <?= $cod_grupox ?> value="<?= $value['cod_grupox'] ?>"><?= $value['nom_grupox'] ?></option>
                                                <?php } ?>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div id="servicios" class="col-md-12 ancho text-center" style="display: <?= $disp ?>">
                                         <div class="col-md-12 CellHead"><b>Filtros Asignados Al Perfil</b></div>
                                         <div class="col-md-12"></div>
                                         <div class="col-md-6 CellHead"><b>Nombre del Filtro</b></div>
                                         <div class="col-md-6 CellHead"><b>Valor Asignado</b></div>
                                         <div class="col-md-12 ancho" id="datos">
                                             <?php   if($data->cod_usuari){self::$cFunciones->getDatosFiltro($datos->cod_perfil);} ?>
                                         </div>
                                    </div>
                                    <div id="services" class="col-md-12 ancho text-center" style="display: block">
                                         <div class="col-md-12 CellHead"><b>Filtros Especificos Para Usuario</b></div>
                                         <div class="col-md-12"></div>
                                         <div class="col-md-6 CellHead text-right"><b>Nombre Del Filtro</b></div>
                                         <div class="col-md-6 CellHead text-left"><b>Opci&oacute;n</b></div>
                                         <div class="col-md-12 ancho" id="datos">
                                              <?php self::$cFunciones->getOtherFilters($data->cod_usuari); ?>
                                         </div>
                                    </div>
                                    <div class="col-md-12 ancho text-center">
                                    <div class="col-md-12 CellHead"><b>Caducidad de Contrase&ntilde;a</b></div>
                                        <?php if($datos->num_diasxx == 30){
                                                $treinta = "checked";
                                              }else if ($datos->num_diasxx == 60){
                                                $sesenta = "checked";
                                              } ?>                                        
                                        <div class="col-md-3 text-right">Cada 30 d&iacute;as:</div>
                                        <div class="col-md-3 text-left"><input type="radio" <?= $treinta ?> id="ind_30dias" name="num_diasxx" value="30"></input></div>
                                        <div class="col-md-3 text-right">Cada 60 d&iacute;as:</div>
                                        <div class="col-md-3 text-left"><input type="radio" <?= $sesenta ?> id="ind_60dias" name="num_diasxx" value="60"></input></div>
                                    </div>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <center>
        <?php
        switch ($data->opcion) {
            case 1: //registrar nuevo
                ?>
                    <input type="button" name="registar" value="Registrar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="registrar(1)"></input>
                    <?php
                    break;
                case 2: //editar
                    ?>
                    <input type="button" name="editar" value="Editar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="registrar(2)"></input>
                    <?php
                    break;
                }
                ?>
                    </center>  
                </div>
            </div>
        </div>
        <?php
    }

}
//FIN CLASE
$proceso = new ins_usuari_usuari($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>

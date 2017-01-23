<?php 

/* ! \archive: ins_perfil_perfil.php
 *  \brief: archivo para el manejo de perfiles
 *  \author: Ing. Alexander Correa
 *  \author: aleander.correa@intrared.net
 *  \date: 06/04/2016
 *  \date modified: dia/mes/año
 *  \warning:   
 *  \ 
 */
session_start();

require "ajax_perfil_perfil.php";

class ins_perfil_perfil {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
        #importa los  css y js necesarios
        ?>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/dinamic_list.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/perfil.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/new_ajax.js" language="javascript"></script>
        <script src="../<?= DIR_APLICA_CENTRAL ?>/js/blockUI.jquery.js" language="javascript"></script>
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
            case 3: //copiar
                $this->Formulario();
            break;
            default:
                $this->lista();
                break;
        }
    }
    /*! \fn: lista
     *  \brief: funcion inicial que muestra una lista con los perfiles creados
     *  \author: Ing. Alexander Correa
     *  \date: 06/04/2016
     *  \date modified: dia/mes/año
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
                <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>LISTADO DE PERFILES</b></h3>
                <div class="" id="sec2">
                    <div class="Style2DIV" id="form3">
                        <?php
                            echo self::$cFunciones->listPerfiles();
                         ?>
                    </div>
                </div>
            </div>            
            <input type="hidden" name="standa" id="standa" value="<?= DIR_APLICA_CENTRAL ?>"></input>
            <input type="hidden" name="window" id="window" value="central"></input>
            <input type="hidden" name="cod_servic" id="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>"></input>
            <input type="hidden" name="opcion" id="opcion" value="1"></input>
            <input type="hidden" name="cod_perfil" id="cod_perfil" value=""></input>
            <input type="hidden" name="nom_perfil" id="nom_perfil" value=""></input>
            <input type="hidden" name="cod_respon" id="cod_respon" value=""></input>
        </form>
        <?php
    }


    /* ! \fn: formulario
     *  \brief: pinta el formulario para crear, editar y copiar un perfil
     *  \author: Ing. Alexander Correa
     *  \date: 06/04/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */
    function Formulario() {
        $datos = (object) $_POST;
        
        if($datos->opcion != 2 ){
            $cod_perfil = self::$cFunciones->getNewConsec();
            $datos->nom_perfil = "";
        }else{

            $cod_perfil = $datos->cod_perfil;
        }

        $padres = self::$cFunciones->getServicNivel($datos->cod_perfil);

        $responsables = self::$cFunciones->getResponsables();

        $nov_perfil = self::$cFunciones->getNovPerrfil($cod_perfil);
        $nov_perfil = implode(",",$nov_perfil);
        if($datos->cod_perfil){
            $trans  =   self::$cFunciones->getTransPerfil($datos->cod_perfil);
            $objTrans= (object) $trans;
        }
        

    ?>
    </table>
    <form style="display:none;" action="index.php" method="post" name="form_search" id="form_searchID" enctype="multipart/form-data">
        <input type="hidden" name="standa" value="<?= DIR_APLICA_CENTRAL ?>" id="standa">
        <input type="hidden" name="window" id="window" value="central">
        <input type="hidden" name="cod_servic" id="cod_servic" value="<?= $_REQUEST['cod_servic'] ?>">
        <input type="hidden" name="cod_noveda" id="cod_noveda" value="<?= $nov_perfil ?>">
        <input type="hidden" name="cod_transp" id="cod_transp" value="<?= $objTrans->clv_filtro  ?>">
    </form>
    <div class="accordion"  >
        <h1 style="padding:6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>INFORMACI&Oacute;N B&Aacute;SICA DEL PERFIL</b></h1>
        <div id="contenido">
            <div class="Style2DIV">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="text-align:center" class="CellHead contenido">
                                <div class="col-md-6 text-right">C&oacute;digo<font style="color:red">*</font>:</div>
                                <div class="col-md-6 text-left">
                                    <input class="text-center" type="text" name="cod_perfil" id="cod_perfil" readonly value="<?= $cod_perfil ?>" obl="1" maxlength="5" minlength="1" validate="numero"></input>
                                </div>
                                <div class="col-md-6 text-right">Nombre<font style="color:red">*</font>:</div>
                                <div class="col-md-6 text-left">
                                    <input class="text-center" type="text" name="nom_perfil" id="nom_perfil" value="<?= $datos->nom_perfil ?>" validate="dir" obl="1" maxlength="50" minlength="5"></input>
                                </div>
                                <div class="col-md-6 text-right">Responsable<font style="color:red">*</font>:</div>
                                <div class="col-md-6 text-left">
                                    <select id="cod_respon" name="cod_respon" obl="1" validate="select">
                                        <option>Seleccione una Opci&oacute;n</option>
                                        <?php 
                                        
                                        foreach ($responsables as $key => $value){ 
                                            $cod_respon = "";
                                            if ($datos->cod_respon == $value['cod_respon']){
                                                $cod_respon = "selected='true'";
                                            }
                                            ?>
                                          <option <?= $cod_respon ?> value="<?= $value['cod_respon'] ?>"><?= $value['nom_respon'] ?></option>  
                                        <?php } ?>
                                    </select>
                                </div>
                                <!--nuevo campo-->
                                <div class="col-md-6 text-right">Transportadora:</div>
                                <div class="col-md-6 text-left">
                                    <input class="text-center" type="text" name="trans_perfil" id="trans_perfil" maxlength="50" minlength="5" value="<?= $objTrans->nom_tercer  ?>"></input>
                                </div>
                            </td>
                        </tr>
                    </tbody>
              </table>
            </div>
        </div>
    </div>
    <div class="accordion"  >
        <h1 style="padding:6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>SECCI&Oacute;N DE PERMISOS</b></h1>
        <div id="contenido">
            <div class="Style2DIV">

                <div class="accordion"  >
                    <?php foreach ($padres as $key => $value){
                        self::$cFunciones->cServic = array(); 
                        $padre[0] = $value;
                        self::$cFunciones->getServicChildren($padre, 2, $datos->cod_perfil);
                        $hijos = self::$cFunciones->cServic;
                        
                        ?>               
                            <h1 style="padding:6px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>M&oacute;dulo <?= $value[1] ?></b></h1>
                            <div id="contenido">
                                <div class="Style2DIV">
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <?php foreach ($hijos as $k => $val){ 
                                            $espacio = "";
                                            for ($i=0; $i < $val[2] ; $i++) { 
                                                $espacio .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                            }
                                            ?>
                                                <tr>
                                                    <td class="CellHead contenido text-left">
                                                        <div class="col-md-6 "><?= $espacio."<img src='../".DIR_APLICA_CENTRAL."/images/point$val[2].png'>&nbsp;&nbsp;".$val[1] ?></div>
                                                        <div class="col-md-6 text-left">
                                                            <?php
                                                            $checked = "";
                                                             if($val[3] == 1){
                                                                $checked = "checked='true'";
                                                                } ?>
                                                            <input type="checkbox" <?= $checked ?> value="<?= $val[0] ?>" id="adm_<?= $val[1] ?>" name="cod_servic[]"></input>
                                                         </div>
                                                         <div class="col-md-12" style="background-color: #416F1E"></div>                                                     
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            
                                        </tbody>
                                  </table>
                                </div>
                            </div>
                    <?php } ?>
                </div>
                <br>
                <center>
                    <input type="button" name="registar" value="Novedades" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="novedades(<?= $cod_perfil ?>)"></input>
                    <?php
                    switch ($datos->opcion) {
                    case 1: //registrar nuevo
                        ?>
                        <input type="button" name="registar" value="Registrar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="registrar(1)"></input>
                        <?php
                    break;
                    case 2: //editar
                        ?>
                        <input type="button" name="registar" value="Editar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="registrar(2)"></input>
                        <?php
                    break;
                    case 3: //copiar
                        ?>
                        <input type="button" name="registar" value="Copiar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="registrar(3)"></input>
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
$proceso = new ins_perfil_perfil($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>

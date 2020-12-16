<?php
ini_set('display_errors', true);
error_reporting(E_ALL &~E_NOTICE);
/* ! \file: ajax_certra_certra.php
 *  \brief: archivo con multiples funciones para la configuracion de los tipos de servicio de una transportadora
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 01/02/2016
 *  \bug: 
 *  \bug: 
 *  \warning:
 */

class ajax_certra_certra {

    private static $cConexion,
        $cCodAplica,
        $cUsuario,
        $cTotalDespac,
        $cNull = array(array('', '-----'));

    private static $cIndLaboral1 = 3; # Indicador de configuracion horario laboral 1
    private static $cIndLaboral2 = 4; # Indicador de configuracion horario laboral 2

    var $Week = array(
        'L' => 'Lunes',
        'M' => 'Martes',
        'X' => 'Mi&eacute;rcoles',
        'J' => 'Jueves',
        'V' => 'Viernes',
        'S' => 'S&aacute;bado',
        'D' => 'Domingo',
        'F' => 'Festivo'
    );

    var $Year = array(
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
    );

    function __construct($co = null, $us = null, $ca = null) {
        if ($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on') {

            @include_once( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            @include_once( "../lib/general/functions.inc" );
            self::$cConexion = $AjaxConnection;
        } else {
            self::$cConexion = $co;
            self::$cUsuario = $us;
            self::$cCodAplica = $ca;
        }

        if ($_REQUEST[Ajax] === 'on') {
            $opcion = $_REQUEST[Option];
            if (!$opcion) {
                $opcion = $_REQUEST[operacion];
            }

            switch ($opcion) {
                case "getDataFomrmTipSer";
                    $this->getDataFomrmTipSer();
                    break;
                case "CreateConfig";
                    $this->CreateConfig();
                    break;
                case "CreateContac";
                    $this->CreateContac();
                    break;
                case "getFestivos";
                    $this->getFestivos();
                    break;
                case "InsertFestivo";
                    $this->InsertFestivo();
                    break;
                case "deleteFestivo";
                    $this->deleteFestivo();
                    break;
                case "NewParametrizacion";
                    $this->NewParametrizacion();
                    break;
                case "NewContac";
                    $this->NewContac();
                    break;  
                case "editContac";
                    $this->editContac();
                    break;  
                case "registrarTipoServicio";
                    $this->registrarTipoServicio();
                    break;
                case "deleteConfiguracion";
                    $this->deleteConfiguracion();
                    break;

                case "deleteContac";
                    $this->deleteContac();
                    break;

                default:
                    header('Location: ../../' . BASE_DATOS . '/index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

    /* ! \fn: getDataFomrmTipSer
     *  \brief: funcion para cargar los datos de la ultima configuracion de una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 25/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function getDataFomrmTipSer() {
        $datos = (object) $_POST;

        //trae el consecutivo de la tabla
        $sql = "SELECT MAX(num_consec) AS num_consec
        FROM " . BASE_DATOS . ".tab_transp_tipser a
        WHERE a.cod_transp = '$datos->cod_transp'";
        $consec = new Consulta($sql, self::$cConexion);
        $num_consec = $consec->ret_matrix('i');
        $num_consec = $num_consec[0][0];
        if ($num_consec) {
            $datos->num_consec = $num_consec;
            //consulto los datos de la ultima configuracion de la empresa
            $sql = "SELECT 
            cod_tipser, tie_contro, ind_estado, tie_conurb, ind_llegad, cod_server, 
            ind_notage, tip_factur, tie_prcurb, tie_prcnac, tie_prcimp, tie_prcexp,
            tie_prctr1, tie_prctr2, tie_carurb, tie_carnac, tie_carimp, tie_carexp,
            tie_desurb, tie_desnac, tie_desimp, tie_desexp, tie_trazab, ind_excala, 
            ind_calcon, ind_segcar, ind_segtra, ind_segdes, val_regist, tie_cartr1, 
            val_despac, tie_cartr2, tie_destr1, tie_destr2, ind_camrut, dup_manifi, 
            ind_biomet, can_llaurb, can_llanac, can_llaimp, can_llaexp, can_llatr1, 
            can_llatr2, fec_iniser, hor_iniser, fec_finser, hor_finser, nom_aplica, 
            ind_segprc, ind_planru, tie_traexp, tie_traimp, tie_tratr1, tie_tratr2, 
            cod_grupox, cod_operac, cod_priori, ind_conper, hor_pe1urb, hor_pe2urb, 
            hor_pe1nac, hor_pe2nac, hor_pe1imp, hor_pe2imp, hor_pe1exp, hor_pe2exp, 
            hor_pe1tr1, hor_pe2tr1, hor_pe1tr2, hor_pe2tr2, ind_solpol, cod_asegur, 
            num_poliza, fec_valreg, hab_asicar, fec_asicar
            FROM " . BASE_DATOS . ".tab_transp_tipser 
            WHERE cod_transp = '$datos->cod_transp' 
            AND num_consec = $datos->num_consec";
            $data = new Consulta($sql, self::$cConexion);
            $data = $data->ret_matrix("a");
            $datos->principal = $data[0];
        } else {
            $datos->num_consec = 0;
        }

        //consulto los tipos de servicios y los agrego al objeto principal
        $query = "SELECT cod_tipser, nom_tipser
        FROM " . BASE_DATOS . ".tab_genera_tipser
        WHERE ind_estado = '1'";
        $consulta = new Consulta($query, self::$cConexion);
        $servicios = $consulta->ret_matrix("a");
        $datos->servicios = $servicios;

        //consulto los servidores y los agrego al objeto principal
        $query = "SELECT cod_server, nom_server
        FROM " . CENTRAL . ".tab_genera_server
        WHERE ind_estado = '1'";
        $consulta = new Consulta($query, self::$cConexion);
        $servers = $consulta->ret_matrix("a");
        $datos->servers = $servers;

        $datos->configuracion = $this->getHorarioLaboralTransp(self::$cIndLaboral1, $datos->cod_transp);
        $datos->configuracionAdicional = $this->getHorarioLaboralTransp(self::$cIndLaboral2, $datos->cod_transp);

        //consulto las esferas que tenga configuradas la empresa
        $sql = "SELECT cod_ealxxx, val_ealxxx, fec_inieal, fec_fineal FROM " . BASE_DATOS . ".tab_ealxxx_transp WHERE cod_transp = '$datos->cod_transp'";
        $consulta = new Consulta($sql, self::$cConexion);
        $esferas = $consulta->ret_matrix("a");
        $datos->esferas = $esferas;


        #si no hay datos pueden venir por post algunos
        if (!$datos->principal['fec_iniser'] || $_POST['fec_iniser']) {
            $datos->principal['fec_iniser'] = $_POST['fec_iniser'];
        }
        if (!$datos->principal['hor_iniser'] || $_POST['hor_iniser']) {
            $datos->principal['hor_iniser'] = $_POST['hor_iniser'];
        }
        if (!$datos->principal['fec_finser'] || $_POST['fec_finser']) {
            $datos->principal['fec_finser'] = $_POST['fec_finser'];
        }
        if (!$datos->principal['hor_finser'] || $_POST['hor_finser']) {
            $datos->principal['hor_finser'] = $_POST['hor_finser'];
        }
        if (!$datos->principal['cod_tipser'] || $_POST['cod_tipser']) {
            $datos->principal['cod_tipser'] = $_POST['cod_tipser'];
        }
        if (!$datos->principal['val_despac'] || $_POST['val_despac']) {
            $datos->principal['val_despac'] = $_POST['val_despac'];
        }
        if (!$datos->principal['val_regist'] || $_POST['val_regist']) {
            $datos->principal['val_regist'] = $_POST['val_regist'];
        }
        if (!$datos->principal['cod_server'] || $_POST['cod_server']) {
            $datos->principal['cod_server'] = $_POST['cod_server'];
        }
        if (!$datos->principal['nom_aplica'] || $_POST['nom_aplica']) {
            $datos->principal['nom_aplica'] = $_POST['nom_aplica'];
        }
        if (!$datos->principal['ind_solpol'] || $_POST['ind_solpol']) {
            $datos->principal['ind_solpol'] = $_POST['ind_solpol'];
        }
        if (!$datos->principal['cod_asegur'] || $_POST['cod_asegur']) {
            $datos->principal['cod_asegur'] = $_POST['cod_asegur'];
        }
        if (!$datos->principal['num_poliza'] || $_POST['num_poliza']) {
            $datos->principal['num_poliza'] = $_POST['num_poliza'];
        }

        $this->pintarFormulario($datos);
    }

    /*! \fn: getHorarioLaboralTransp
     *  \brief: consulto los horarios laborales de la transportadora segun indicador de configuracion
     *  \author: Ing. Fabian Salinas
     *  \date: 12/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: indConfig  integer
     *  \param: codTransp  integer
     *  \return: 
     */
    private function getHorarioLaboralTransp($indConfig, $codTransp) {
        $sql = "SELECT com_diasxx, hor_ingres, hor_salida 
                  FROM " . BASE_DATOS . ".tab_config_horlab 
                WHERE cod_tercer = '$codTransp' 
                  AND ind_config = $indConfig 
                  AND cod_ciudad = $indConfig ";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /* ! \fn: pintarFormulario
     *  \brief: funcion para pintar el formulario de insercion de la configuracion de una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 25/01/2016
     *  \date modified: dia/mes/año
     *  \param:
     *  \param: 
     *  \return 
     */
    private function pintarFormulario($datos) {
        $grupos = $this->getGrupos();
        $operaciones = $this->getOperaciones();
        $eals = $this->getEals();
        $option = 0;
        $standa = DIR_APLICA_CENTRAL;
        ?>
        <div id="conf_servicioID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Configuraci&oacute;n del Servicio</b></h3>
            <div id="contenido_conf" style="height: 290px !important">
                <div class="StyleDIV contenido" style="min-height: 200px !important;">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10">
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Fecha Inicio del Servicio<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="fecha text-center ancho" obl="1" name="fec_iniser" validate="date" id="fec_iniserID" maxlength="10" minlength="10" obl="true" value="<?= $datos->principal['fec_iniser'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Hora Inicio del Servicio<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" readonly="true" class="hora text-center ancho" obl="1" name="hor_iniser" validate="dir" id="hor_iniserID" maxlength="5" minlength="5" obl="true" onclick="removeStyle('hor_iniserID')" value="<?= $datos->principal['hor_iniser'] ?>" >
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Fecha Fin del Servicio<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="fecha text-center ancho" obl="1" name="fec_finser" validate="date" id="fec_finserID" maxlength="10" minlength="10" obl="true" value="<?= $datos->principal['fec_finser'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Hora Fin del Servicio<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" readonly="true" obl="1"  class="hora text-center ancho" name="hor_finser" validate="dir" id="hor_finserID" maxlength="5" minlength="5" obl="true" onclick="removeStyle('hor_finserID')" value="<?= $datos->principal['hor_finser'] ?>" >
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Valor del Despacho<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="val_despac" id="val_despacID" validate="numero" obl="1" maxlength="5" minlength="3" value="<?= $datos->principal['val_despac'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Valor del Registro<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="val_regist" id="val_registID" validate="numero" obl="1" maxlength="4" minlength="3" value="<?= $datos->principal['val_regist'] ?>" >
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Interfaz con Servidor<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <select id="cod_serverID" name="cod_server" class="ancho" obl="1" validate="select">
                                    <option value="">Seleccione una Opci&oacute;n.</option>
                                    <?php
                                    foreach ($datos->servers as $key => $value) {
                                        $sel = "";
                                        if ($value['cod_server'] == $datos->principal['cod_server']) {
                                            $sel = "selected";
                                        }
                                        ?>
                                        <option <?= $sel ?> value="<?= $value['cod_server'] ?>"><?= $value['nom_server'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Vigencia Valor del Registro<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="fecha text-center ancho" name="fec_valreg" id="fec_valregID" validate="date" obl="1" maxlength="10" minlength="10" value="<?= $datos->principal['fec_valreg'] ?>" >
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Nombre de la Aplicaci&oacute;n<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" value="<?= $datos->principal['nom_aplica'] ?>" name="nom_aplica" id="nom_aplicaID" validate="alphanumerico" obl="1" maxlength="15" minlength="4" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Activar Solicitud de P&oacute;lizas</div>
                            <div class="col-md-6 text-left">
                                <?php
                                $ind_solpol = "";
                                if ($datos->principal['ind_solpol'] == 1) {
                                    $ind_solpol = "checked='true'";
                                }
                                ?>
                                <input type="checkbox" name="ind_solpol" id="ind_solpol" value="1" <?= $ind_solpol ?> >
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Aseguradora</div>
                            <div class="col-md-6 text-left">
                                <select class="ancho" name="cod_asegur" id="cod_asegur">
                                    <option value="">Seleccione una Opci&oacute;n</option>
                                    <?php
                                    $aseguradoras = $this->getAseguradoras();
                                    foreach ($aseguradoras as $key => $value) {
                                        $sel = "";
                                        if ($value['cod_tercer'] == $datos->principal['cod_asegur']) {
                                            $sel = "selected";
                                        }
                                        ?>
                                        <option <?= $sel ?> value="<?= $value['cod_tercer'] ?>"><?= $value['abr_tercer'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">N&uacute;mero de P&oacute;liza</div>
                            <div class="col-md-6 text-left">
                                <input type="text" name="num_poliza" id="num_poliza" class="text-center ancho" validate="dir" maxlength="20" minlength="5" value="<?= $datos->principal['num_poliza'] ?>" >
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Tipo de Servicio Contratado<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <select id="cod_tipserID" name="cod_tipser" class="ancho" obl="1" validate="select">
                                    <option value="">Seleccione una Opci&oacute;n.</option>
                                    <?php
                                    foreach ($datos->servicios as $key => $value) {
                                        $sel = "";
                                        if ($value['cod_tipser'] == $datos->principal['cod_tipser']) {
                                            $sel = "selected";
                                        }
                                        ?>
                                        <option <?= $sel ?> value="<?= $value['cod_tipser'] ?>"><?= $value['nom_tipser'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                        </div>
                        
                        <div class="row">          
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">S. Asistencia en Carretera</div>
                                <div class="col-md-6 text-left">
                                    <?php
                                        $hab_asicar = "";
                                        if ($datos->principal['hab_asicar'] == 1) {
                                        $hab_asicar = "checked='true'";
                                        }
                                    ?>
                                    <input type="checkbox" name="hab_asicar" id="hab_asicar" value="1" <?= $hab_asicar ?> >
                                </div>
                            </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Fecha de aceptacion T.C.<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="fecha text-center ancho" obl="1" name="fec_asicar" validate="date" id="fec_asicarID" maxlength="10" minlength="10" obl="true" value="<?= $datos->principal['fec_asicar'] ?>" >
                            </div>
                        </div>
                        </div>
                        </div>
                        
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    
            </div>
        </div>
        <div id="conf_servicioID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Configuracion de contactos</b></h3>
            <div id="contenido_serv">
                <div class="StyleDIV contenido" style="min-height: 220px !important;" >
                    <div class="col-md-13 CellHead"  style="text-align:center;"><strong>AGREGAR CONTACTO</strong><input type="button" value="NUEVO CONTACTO" class="small save  ui-state-default ui-corner-all" onclick="CreateContac('<?= $datos->cod_transp ?>', 0, 0)"></div>
                    <?php 
                        $contactos = $this->getContact();
                        /*echo "<pre>";
                            print_r($contactos);
                        echo "</pre>";*/
                        $agencias = $this->getAgencias();
                        if (!$contactos) {     
                    ?>
                        <div class="col-md-12" style="text-align:center;">ACTUALMENTE NO TIENE CONTACTOS PARAMETRIZADOS</div> 
                    <?php } else { ?>
                        <div class="contenido" id="mensaje"></div>
                        <div class="CellHead centrado" id="mensaje"><b>LISTA DE CONTACTOS</b></div>
                        <table class="classTable" align="center" width="100%" cellspacing="1" cellpadding="0" style="border:1px #35650F solid">
                            <tr>
                                <th width="10%" nowrap class="CellHead" align="center">NOMBRE</th>
                                <th width="10%" nowrap class="CellHead" align="center">MOVIL</th>
                                <th width="10%" nowrap class="CellHead" align="center">E-MAIL</th>
                                <th width="10%" nowrap class="CellHead" align="center">CARGO</th>
                                <th width="30%" nowrap class="CellHead" align="center">AGENCIAS</th>
                                <th width="10%" nowrap class="CellHead" align="center">OBSERVACIONES</th>
                                <th width="10%" nowrap class="CellHead" align="center">ELIMINAR</th>
                                <th width="10%" nowrap class="CellHead" align="center">EDITAR</th>
                            </tr>
                        <?php
                        foreach ($contactos as $row => $value) {
                        ?>
                            <tr>
                                <td align="center" width="10%" class="contenido" id="nom_contac<?=$row?>" style="border:1px #35650F solid"><?= strtoupper($value['nom_contac']) ?></td>
                                <td align="center" width="10%" class="contenido" id="tel_contac<?=$row?>" style="border:1px #35650F solid"><?= strtoupper($value['tel_contac']) ?></td>
                                <td align="center" width="10%" class="contenido" id="ema_contac<?=$row?>" style="border:1px #35650F solid"><?= strtoupper($value['ema_contac']) ?></td>
                                <td align="center" width="10%" class="contenido" id="car_contac<?=$row?>" style="border:1px #35650F solid"><?= strtoupper($value['car_contac']) ?></td>
                            <?php
                                $agencias = $this->getAgenContac($value['ema_contac']);
                                $value['nom_agenci'] = "";
                                $cod_agencia = "";
                                $esElPrimero = true;
                                foreach ($agencias as $agencia) {
                                    if ($esElPrimero) {
                                        $cod_agencia .= $agencia['cod_agenci'];
                                        $value['nom_agenci'] .= $agencia['nom_agenci'];
                                        $esElPrimero = !$esElPrimero;
                                    } else {
                                        $value['nom_agenci'] .= ", ".$agencia['nom_agenci'];
                                        $cod_agencia .= "," . $agencia['cod_agenci'];
                                    }
                                }
                            ?>
                            <input type="hidden" id="cod_agenci<?=$row?>" value="<?=$cod_agencia?>">
                            <td align="center" width="40%" class="contenido" id="nom_agenci<?=$row?>" style="border:1px #35650F solid"><?= strtoupper($value['nom_agenci']) ?></td>
                            <td align="center" width="10%" class="contenido" id="obs_contac<?=$row?>" style="border:1px #35650F solid"><?= strtoupper($value['obs_contac']) ?></td>
                            <td align="center" width="5%" class="contenido" style="border:1px #35650F solid"><img class="pointer" width="15px" height="15px" src="../<?= DIR_APLICA_CENTRAL ?>/images/delete.png" onclick="deleteContac(<?= $datos->cod_transp ?>, '<?= $value['ema_contac'] ?>', 3)"></td>
                            <td align="center" width="5%" class="contenido" style="border:1px #35650F solid"><img class="pointer" width="15px" height="15px" src="../<?= DIR_APLICA_CENTRAL ?>/images/edit.png" onclick="EditaContac(<?= $datos->cod_transp ?>, '<?= $value['ema_contac'] ?>' , <?= $row ?> )"></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </table>
                    <?php
                    }
                    ?>
                </div>
            </div>  
        </div>
        <div id="conf_servicioID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Horario del Servicio Contratado</b></h3>
            <div id="contenido_serv">
                <div class="StyleDIV contenido" style="min-height: 180px !important;" >
                    <div class="col-md-12 CellHead"  style="text-align:center;">Horarios Laborales <input type="button" value="Nueva Configuraci&oacute;n" class="small save  ui-state-default ui-corner-all" onclick="CreateConfig('<?= $datos->cod_transp ?>', 3)"></div>
                    <?php if (!$datos->configuracion && !$datos->configuracionAdicional) { ?>
                        <div class="col-md-12" style="text-align:center;">Actualmente no tiene una configuraci&oacute;n parametrizada</div> 
                    <?php } else { ?>
                        <div class="col-md-12 contenido" id="mensaje"></div>
                        <div class="col-md-12 CellHead centrado" id="mensaje"><b>DIAS DE LA SEMANA PARAMETRIZADOS</b></div>
                        <div class="col-md-12 contenido"></div>
                        <div class="col-md-6 CellHead centrado"><b>D&iacute;a</b></div>
                        <div class="col-md-2 CellHead centrado"><b>Hora de Ingreso</b></div>
                        <div class="col-md-2 CellHead centrado"><b>Hora de Salida</b></div>
                        <div class="col-md-2 CellHead centrado"><b>Eliminar</b></div>
                        <?php
                        foreach ($datos->configuracion as $row) {
                            $mDiasxx = '';
                            foreach (explode('|', $row['com_diasxx']) as $nameWeek) {
                                $mDiasxx .= $mDiasxx != '' ? ', ' . $this->Week[$nameWeek] : $this->Week[$nameWeek];
                            }
                            ?>

                            <div class="col-md-6 contenido centrado" ><?= $mDiasxx ?></div> 
                            <div class="col-md-2 contenido centrado"><?= $this->HoraLegible($row['hor_ingres']) ?></div>  
                            <div class="col-md-2 contenido centrado"><?= $this->HoraLegible($row['hor_salida']) ?></div>   
                            <div class="col-md-2 contenido centrado"><img class="pointer" width="12px" height="12px" src="../<?= DIR_APLICA_CENTRAL ?>/images/delete.png" onclick="deleteConfiguracion(<?= $datos->cod_transp ?>, '<?= $row['com_diasxx'] ?>', 3)"></div>   

                            <?php
                        }
                        foreach ($datos->configuracionAdicional as $row) {
                            $mDiasxx = '';
                            foreach (explode('|', $row['com_diasxx']) as $nameWeek) {
                                $mDiasxx .= $mDiasxx != '' ? ', ' . $this->Week[$nameWeek] : $this->Week[$nameWeek];
                            }
                            ?>

                            <div class="col-md-6 contenido centrado" ><?= $mDiasxx ?></div> 
                            <div class="col-md-2 contenido centrado"><?= $this->HoraLegible($row['hor_ingres']) ?></div>  
                            <div class="col-md-2 contenido centrado"><?= $this->HoraLegible($row['hor_salida']) ?></div>   
                            <div class="col-md-2 contenido centrado"><img class="pointer" width="12px" height="12px" src="../<?= DIR_APLICA_CENTRAL ?>/images/delete.png" onclick="deleteConfiguracion(<?= $datos->cod_transp ?>, '<?= $row['com_diasxx'] ?>', 4)"></div>   

                            <?php
                        }
                    }
                    ?>
                    <div class="col-md-12 CellHead"  style="text-align:center;">Festivos por A&ntilde;o</div>
                    <div class="col-md-12" style="text-align:center;">
                        <input id="ind_configID" type="hidden" value="3" name="ind_config">
                        <input id="cod_ciudadID" type="hidden" value="3" name="cod_ciudad">
                        <select id="sel_yearxxID" name="sel_yearxx" onchange="setFestivos()">
                            <option value="">Seleccione una Opci&oacute;n</option>
                            <?php
                            $mYear = date('Y');
                            for ($i = 0; $i < 5; $i++) {
                                ?>
                                <option value="<?= $mYear ?>"><?= $mYear ?></option>
                                <?php
                                $mYear++;
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="conf_etapasID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Etapas y tipo de Seguimiento Contratado</b></h3>
            <div id="contenido_etap">
                <div class="StyleDIV" style="min-height: 485px !important;">
                    <div class="col-md-12 CellHead centrado">
                        PreCargue 
                        <?php
                        if ($datos->principal['ind_segprc'] == 1) {
                            $ind_segprc = "checked='true'";
                        }
                        ?>
                        <input type="checkbox" <?= $ind_segprc ?> name="ind_segprc" id="ind_segprcID" value="1" onclick="enableDisable(6)">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-2 CellHead">Nacional</div>
                    <div class="col-md-2 CellHead">Urbano</div>
                    <div class="col-md-2 CellHead">Exportaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Importaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_prcnac" id="tie_prcnacID" value="<?= $datos->principal['tie_prcnac'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_prcurb" id="tie_prcurbID" value="<?= $datos->principal['tie_prcurb'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_prcexp" id="tie_prcexpID" value="<?= $datos->principal['tie_prcexp'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_prcimp" id="tie_prcimpID" value="<?= $datos->principal['tie_prcimp'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_prctr1" id="tie_prctr1ID" value="<?= $datos->principal['tie_prctr1'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_prctr2" id="tie_prctr2ID" value="<?= $datos->principal['tie_prctr2'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-12 CellHead centrado">
                        Cargue 
                        <?php
                        if ($datos->principal['ind_segcar'] == 1) {
                            $ind_segcar = "checked='true'";
                        }
                        ?>
                        <input type="checkbox" <?= $ind_segcar ?> name="ind_segcar" id="ind_segcarID" value="1" onclick="enableDisable(1)">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-2 CellHead">Nacional</div>
                    <div class="col-md-2 CellHead">Urbano</div>
                    <div class="col-md-2 CellHead">Exportaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Importaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-12 contenido text-center verde"><b>Tiempos</b></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carnac" id="tie_carnacID" value="<?= $datos->principal['tie_carnac'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carurb" id="tie_carurbID" value="<?= $datos->principal['tie_carurb'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carexp" id="tie_carexpID" value="<?= $datos->principal['tie_carexp'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carimp" id="tie_carimpID" value="<?= $datos->principal['tie_carimp'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_cartr1" id="tie_cartr1ID" value="<?= $datos->principal['tie_cartr1'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_cartr2" id="tie_cartr2ID" value="<?= $datos->principal['tie_cartr2'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-12 contenido text-center verde"><b>Cantidad de Llamadas</b></div>
                    <div class="col-md-2 contenido"><input type="text" name="can_llaurb" id="can_llaurbID" value="<?= $datos->principal['can_llaurb'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="can_llanac" id="can_llanacID" value="<?= $datos->principal['can_llanac'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="can_llaexp" id="can_llaexpID" value="<?= $datos->principal['can_llaexp'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="can_llaimp" id="can_llaimpID" value="<?= $datos->principal['can_llaimp'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="can_llatr1" id="can_llatr1ID" value="<?= $datos->principal['can_llatr1'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="can_llatr2" id="can_llatr2ID" value="<?= $datos->principal['can_llatr2'] + 0 ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-6 CellHead centrado" >
                        Tr&aacute;nsito
                        <?php
                        if ($datos->principal['ind_segtra'] != 0) {
                            $ind_segtra = "checked='true'";
                        }
                        ?>
                        <input type="checkbox" <?= $ind_segtra ?> name="ind_segtra" id="ind_segtraID" value="1" onclick="enableDisable(2)">
                    </div>
                    <div class="col-md-6 CellHead centrado" >
                        <?php
                        if ($datos->principal['ind_planru'] == 1) {
                            $ind_planru = "checked='true'";
                        }
                        ?>
                        Recalculo Seg&uacute;n Plan de Ruta 
                        <input <?= $ind_planru ?> type="checkbox" name="ind_planru" id="ind_planruID" value="1" onclick="enableDisable(3)">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-2 CellHead">Nacional</div>
                    <div class="col-md-2 CellHead">Urbano</div>
                    <div class="col-md-2 CellHead">Exportaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Importaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_contro'] + 0 ?>" name="tie_contro" id="tie_controID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_conurb'] + 0 ?>" name="tie_conurb" id="tie_conurbID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_traexp'] + 0 ?>" name="tie_traexp" id="tie_traexpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_traimp'] + 0 ?>" name="tie_traimp" id="tie_traimpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_tratr1'] + 0 ?>" name="tie_tratr1" id="tie_tratr1ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_tratr2'] + 0 ?>" name="tie_tratr2" id="tie_tratr2ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-12 contenido">&nbsp;</div>

                    <div class="col-md-12 CellHead centrado">
                        Descargue
                        <?php
                        if ($datos->principal['ind_segdes'] == 1) {
                            $ind_segdes = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_segdes ?> type="checkbox" name="ind_segdes" id="ind_segdesID" value="1" onclick="enableDisable(4)">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-2 CellHead">Nacional</div>
                    <div class="col-md-2 CellHead">Urbano</div>
                    <div class="col-md-2 CellHead">Exportaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Importaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desnac'] + 0 ?>" name="tie_desnac" id="tie_desnacID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desurb'] + 0 ?>" name="tie_desurb" id="tie_desurbID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desexp'] + 0 ?>" name="tie_desexp" id="tie_desexpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desimp'] + 0 ?>" name="tie_desimp" id="tie_desimpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_destr1'] + 0 ?>" name="tie_destr1" id="tie_destr1ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_destr2'] + 0 ?>" name="tie_destr2" id="tie_destr2ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>

                    <div class="col-md-12 CellHead centrado">
                        Requiere Confirmaci&oacute;n de Pernoctaci&oacute;n
                        <?php
                        if ($datos->principal['ind_conper'] == 1) {
                            $ind_conper = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_conper ?> type="checkbox" name="ind_conper" id="ind_conperID" value="1" onclick="enableDisable(5)">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-2 CellHead">Nacional</div>
                    <div class="col-md-2 CellHead">Urbano</div>
                    <div class="col-md-2 CellHead">Exportaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Importaci&oacute;n</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1nac'] ?>" name="hor_pe1nac" id="hor_pe1nacID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true" onfocus="removeStyle('hor_pe1nacID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2nac'] ?>" name="hor_pe2nac" id="hor_pe2nacID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe2nacID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1urb'] ?>" name="hor_pe1urb" id="hor_pe1urbID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe1urbID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2urb'] ?>" name="hor_pe2urb" id="hor_pe2urbID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe2urbID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1exp'] ?>" name="hor_pe1exp" id="hor_pe1expID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe1expID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2exp'] ?>" name="hor_pe2exp" id="hor_pe2expID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe2expID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1imp'] ?>" name="hor_pe1imp" id="hor_pe1impID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe1impID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2imp'] ?>" name="hor_pe2imp" id="hor_pe2impID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe2impID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1tr1'] ?>" name="hor_pe1tr1" id="hor_pe1tr1ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe1tr1ID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2tr1'] ?>" name="hor_pe2tr1" id="hor_pe2tr1ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe2tr1ID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1tr2'] ?>" name="hor_pe1tr2" id="hor_pe1tr2ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe1tr2ID')">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2tr2'] ?>" name="hor_pe2tr2" id="hor_pe2tr2ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true"  onfocus="removeStyle('hor_pe2tr2ID')">
                    </div>
                </div>
            </div>
        </div>
        <div id="otra_parameID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Otras Parametrizaiones</b></h3>
            <div id="contenido_etap">
                <div class="StyleDIV contenido" style="min-height: 180px !important;" >
                    <div class="col-md-5 derecha">
                        Activa 
                        <?php
                        if ($datos->principal['ind_estado'] == 1) {
                            $ind_estado = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_estado ?> type="checkbox" name="ind_estado" id="ind_estadoID" value="1">
                    </div>
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-5 izquierda">
                        <?php
                        if ($datos->principal['ind_llegad'] == 1) {
                            $ind_llegad = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_llegad ?> type="checkbox" name="ind_llegad" id="ind_llegadID" value="1">
                        Llegada Automatica 
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-5 derecha">
                        Notificaci&oacute;n por Agencia 
                        <?php
                        if ($datos->principal['ind_notage'] == 1) {
                            $ind_notage = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_notage ?> type="checkbox" name="ind_notage" id="ind_notageID" value="1">
                    </div>
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-5 izquierda">
                        <?php
                        if ($datos->principal['ind_biomet'] == 1) {
                            $ind_biomet = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_biomet ?> type="checkbox" name="ind_biomet" id="ind_biometID" value="1">
                        Servicio de Biometria
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-5 derecha">
                        Cambio Plan de Ruta 
                        <?php
                        if ($datos->principal['ind_camrut'] == 1) {
                            $ind_camrut = "checked='true'";
                        }
                        ?>
                        <input <?= $ind_camrut ?> type="checkbox" name="ind_camrut" id="ind_camrutID" value="1">
                    </div>
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-5 izquierda">
                        <?php
                        if ($datos->principal['dup_manifi'] == 1) {
                            $dup_manifi = "checked='true'";
                        }
                        ?>
                        <input <?= $dup_manifi ?> type="checkbox" name="dup_manifi" id="dup_manifiID" value="1">
                        Permitir Duplicar Manifiesto
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-5 derecha">
                        Grupo al que Pertenece <font style="color:red">*</font>
                        <select id="cod_grupoxID" validate="select" obl="1" name="cod_grupox">
                            <option value="">Seleccione una Opci&oacute;n</option>
                            <?php
                            foreach ($grupos as $key => $value) {
                                if ($datos->principal['cod_grupox'] == $value['cod_grupox']) {
                                    $cod_grupox = "selected='true'";
                                } else {
                                    $cod_grupox = "";
                                }
                                ?>
                                <option <?= $cod_grupox ?> value="<?= $value['cod_grupox'] ?>"><?= $value['nom_grupox'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-5 izquierda">
                        <?php $tie_trazab = str_replace(',', '.', round($datos->principal['tie_trazab'] / 60, 1)); ?>
                        <input class="centrado select" value="<?= $tie_trazab ?>"  onblur="validaCampo()" maxlength="4" minlength="1" type="text" name="tie_trazab" id="tie_trazabID" >
                        Tiempo Trazabilidad Diaria
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-5 derecha">
                        Prioridad <font style="color:red">*</font>
                        <select id="cod_prioriID" validate="select" obl="1" name="cod_priori">
                            <option value="">Seleccione una Opci&oacute;n</option>
                            <?php
                            for ($i = 1; $i <= 3; $i++) {
                                if ($datos->principal['cod_priori'] == $i) {
                                    $cod_priori = "selected='true'";
                                } else {
                                    $cod_priori = "";
                                }
                                ?>
                                <option <?= $cod_priori ?> value="<?= $i ?>"><?= $i ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-5 izquierda">
                        <select id="cod_operacID" validate="select" obl="1" name="cod_operac">
                            <option value="">Seleccione una Opci&oacute;n</option>
                            <?php
                            foreach ($operaciones as $key => $value) {
                                if ($value['cod_operac'] == $datos->principal['cod_operac']) {
                                    $cod_operac = "selected='true'";
                                } else {
                                    $cod_operac = "";
                                }
                                ?>
                                <option <?= $cod_operac ?>value="<?= $value['cod_operac'] ?>"> <?= $value['nom_operac'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        Tipo de Operaci&oacute;n <font style="color:red">*</font>
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                </div>
            </div>
        </div>
        <div id="conf_ealID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>EAL Contratadas</b></h3>
            <div id="contenido_serv">
                <div class="StyleDIV contenido" style="min-height: <?= (count($eals) * 25 + 30) ?>px !important;" >
                    <div class="col-md-12">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <table width="100%" cellpadding="1" cellspacing="1">
                                <tr class="CellHead centrado">
                                    <td width="50%" class="blanco">EAL</td>
                                    <td width="10%" class="blanco">Valor</td>
                                    <td width="20%"  class="blanco">Desde</td>
                                    <td width="20%" class="blanco">Hasta</td>
                                </tr>
                                <?php
                                foreach ($eals as $key => $value) {
                                    foreach ($datos->esferas as $k => $val) {
                                        $checked = "";
                                        $precio = "";
                                        $inicio = "";
                                        $fin = "";
                                        if ($value['cod_contro'] == $val['cod_ealxxx']) {
                                            $checked = "checked='true'";
                                            $precio = $val['val_ealxxx'];
                                            $inicio = $val['fec_inieal'];
                                            $fin = $val['fec_fineal'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr class="contenido centrado">
                                        <td width="50%" class="derecha"><?= utf8_encode($value['nom_contro']) ?> : <input value="<?= $value['cod_contro'] ?>" <?= $checked ?> type="checkbox" name="eal[]" id="eal<?= $key ?>" onclick="habilitar(<?= $key ?>)"></td>
                                        <td width="10%"><input type="text" value="<?= $precio ?>" class="ancho centrado" validate="numero" minlength="4" maxlength="6" name="precio[]" id="precio<?= $key ?>"></td> 
                                        <td width="20%"><input type="text" value="<?= $inicio ?>" class="fecha ancho centrado" readonly="true" validate="date" minlength="4" maxlength="6" name="fecini[]" id="fecini<?= $key ?>"></td>
                                        <td width="20%"><input type="text" value="<?= $fin ?>" class="fecha ancho centrado" readonly="true" validate="date" minlength="4" maxlength="6" name="fecfin[]" id="fecfin<?= $key ?>"></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                        <div class="col-md-3"></div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="contenido centrado" >
            <input type="button" value="Registrar" onclick="registrarTipoServicio()" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all">
        </div>
        <?php
    }

    /* ! \fn: getGrupos
     *  \brief: funcion que trae los grupos de la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 28/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return arreglo con los grupos
     */

    private function getGrupos() {
        $sql = "SELECT cod_grupox, nom_grupox FROM " . BASE_DATOS . ".tab_callce_grupox WHERE ind_estado = 1";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /* ! \fn: getOperaciones
     *  \brief: funcion que trae los tipos de operaciones de la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 28/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return arreglo con los tipos de operaciones
     */
    private function getOperaciones() {
        $sql = "SELECT cod_operac, nom_operac FROM " . BASE_DATOS . ".tab_callce_operac WHERE ind_estado = 1";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /* ! \fn: CreateConfig
     *  \brief: configura el orario laboral de una empresa para el tipo de servico
     *  \author: Ing. Alexander Correa
     *  \date: 26/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function CreateConfig() {
        $datos = (object) $_POST;
        $_CONFIG = $this->getHorarioLaboralTransp(self::$cIndLaboral1, $datos->cod_tercer);
        $_CONFIG2 = $this->getHorarioLaboralTransp(self::$cIndLaboral2, $datos->cod_tercer);

        $mActual = '';
        foreach ($_CONFIG as $key => $value) {
            $mActual .= $mActual != '' ? '|' . $value['com_diasxx'] : $value['com_diasxx'];
        }

        $mArrayActual = array();
        foreach (explode('|', $mActual) as $llave) {
            $mArrayActual[] = $llave;
        }
        $mActual2 = '';
        foreach ($_CONFIG2 as $key => $value) {
            $mActual2 .= $mActual2 != '' ? '|' . $value['com_diasxx'] : $value['com_diasxx'];
        }

        $mArrayActua2l = array();
        foreach (explode('|', $mActual2) as $llave) {
            $mArrayActual2[] = $llave;
        }

        if (count($mArrayActual) < count($this->Week) || count($mArrayActual2) < count($this->Week)) {
            ?>
            <div class="StyleDIV col-md-12">
                <div class="col-md-12 CellHead centrado" id="mensaje"><b>DIAS DE LA SEMANA AUN NO PARAMETRIZADOS</b></div>
                <div class="col-md-12">
                    <div class="col-md-3 cellInfo1"></div>
                    <div class="col-md-6 cellInfo1">
                        <?php
                        $count = 0;
                        $contador = 0;
                        foreach ($this->Week as $mDay => $mDia) {
                            if (!in_array($mDay, $mArrayActual)) {
                                ?>
                                <div class="col-md-2 centrado negro" ><?= $this->Week[$mDay] ?></div>
                                <div class="col-md-4 izquierda"><input type="checkbox" ind_config="3" name="nom_diasxx" value="<?= $mDay ?>" /></div>
                                <?php
                            }
                        }
                        ?>
                        <div class="col-md-12 centrado CellHead"><b>Seguimineto Espacial</b></div>
                        <?php
                        foreach ($this->Week as $mDay => $mDia) {
                            if (!in_array($mDay, $mArrayActual2)) {
                                ?>
                                <div class="col-md-2 centrado negro" ><?= $this->Week[$mDay] ?></div>
                                <div class="col-md-4 izquierda"><input type="checkbox" ind_config="4" name="nom_diasxx" value="<?= $mDay ?>" /></div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="col-md-3 cellInfo1"></div>
                    <div class="col-md-12">
                        <div class="col-md-6 CellHead centrado"><b>HORA INICIO</b></div>
                        <div class="col-md-6 CellHead centrado"><b>HORA FINALIZACI&Oacute;N</b></div>
                    </div>

                    <div class="col-md-6 cellInfo1 centrado negro"><input type="text" class="hora centrado" name="hor_ingres" id="hor_ingresID" value="00:00" /></div>                
                    <div class="col-md-6 cellInfo1 centrado negro"><input type="text" class="hora centrado" name="hor_salida" id="hor_salidaID" value="23:59" /></div> 
                    <?php
                } else {
                    die("1");
                }
                ?>      
            </div>
        </div>

        <?php
    }

    /* ! \fn: getFestivos
     *  \brief: Trae los festivos configurados para la empresa
     *  \author: Ing. Alexander Correa
     *  \date: 08/02 /2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */

    private function getFestivos() {
        $datos = (object) $_POST;
        $fecha = date("Y-m-d");
        $sql = "SELECT fec_festiv
        FROM " . BASE_DATOS . ".tab_config_festiv 
        WHERE cod_tercer = 1 
        AND ind_config = 3
        AND cod_ciudad = 3
        AND YEAR( fec_festiv ) = '$datos->sel_yearxx' ";
       
        $consulta = new Consulta($sql, self::$cConexion);
        $mFechas = $consulta->ret_matrix("a");
        ?>

        <div class="col-md-12">
            <div class="col-md-6 centrado">
                <input type="text" style="width:100%" class="fecha centrado contenido negro" value="<?= $fecha ?>" name="fec_insert" id="fec_insertID" readonly="true">
            </div>
            <div class="col-md-6 centrado">
                <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name="Registrar" value="Registrar" onclick="InsertFestivo('<?= $datos->cod_transp ?>', '<?= $datos->ind_config ?>', '<?= $datos->cod_ciudad ?>');">
            </div>
        </div>
        <div class="col-md-12 centrado cellInfo1" >
            <?php
            if ($mFechas) {
                $mMesesConfig = array();
                foreach ($mFechas as $key => $value) {
                    $eFecha = explode('-', $value['fec_festiv']);
                    $mMesesConfig[$eFecha[1]] .= $mMesesConfig[$eFecha[1]] != '' ? '|' . $eFecha[2] : $eFecha[2];
                }
                ?>
                <div class="col-md-12 CellHead">FESTIVOS PARAMETRIZADOS DE A&Ntilde;O <?= $datos->sel_yearxx ?><br>Para Eliminar un Festivo Haga Click sobre el dia dentro del Calendario</div>
                <?php
                $count = 0;
                $contador = 0;
                foreach ($this->Year as $numAno => $nomAno) {
                    $mDayofMonth = array();
                    if ($mMesesConfig[$numAno]) {
                        $mDayofMonth = explode('|', $mMesesConfig[$numAno]);
                    }

                    $mLimit = $this->ValidateNumberofDays($numAno, $datos->sel_yearxx);
                    ?>
                    <div class="col-md-4">
                        <table class="calendario" width="100%" cellpadding="0" cellspacing="1">
                            <tr >
                                <td colspan="7" class="centrado">&nbsp;</td>
                            </tr> <tr class="CellHead">
                                <td colspan="7" class="centrado blanco"><b><?= $nomAno ?></b></td>
                            </tr>
                            <tr>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>L</b></td>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>M</b></td>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>X</b></td>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>J</b></td>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>V</b></td>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>S</b></td>
                                <td width="14%" style="r-bottom:2px solid #000000;" align="center"><b>D</b></td>
                            </tr>
                            <?php
                            $count2 = 0;
                            $counttr = 0;
                            for ($i = 1, $consec = 1; $consec <= $mLimit; $i++) {
                                if ($count2 == 0) {
                                    ?>
                                    <tr>
                                        <?php
                                        $counttr ++;
                                    }
                                    $diaSemana = date("N", mktime(0, 0, 0, $numAno, $consec, $datos->sel_yearxx));
                                    if ($diaSemana > $i) {
                                        ?>
                                        <td width="14%" style="padding-right:10px;padding-top:3px;" align="right">&nbsp;</td>
                                        <?php
                                    } else {
                                        $add = '';
                                        $link = $consec;
                                        $click = '';

                                        if (in_array($consec, $mDayofMonth)) {
                                            $add = 'cursor:pointer;color:#C00000;font-weight: bold;background-color:#FFD5D5;';
                                            $click = 'onclick="deleteFestivo(\'' . $datos->cod_transp . '\', \'' . $datos->ind_config . '\', \'' . $datos->cod_ciudad . '\', \'' . $datos->sel_yearxx . '\', \'' . $numAno . '\', \'' . $consec . '\');"';
                                        }
                                        ?>
                                        <td width="14%" <?= $click ?> style="<?= $add ?>r:1px solid #ADADAD;padding-right:10px;padding-top:3px;" align="right"><?= $link ?></td>
                                        <?php
                                        $consec++;
                                    }
                                    $count2++;
                                    if ($count2 > 6) {
                                        ?>
                                    </tr>
                                    <?php
                                    $count2 = 0;
                                }
                            }
                            if ($counttr <= 5) {
                                ?>
                                <tr><td colspan="7">&nbsp;<br>&nbsp;</td></tr>
                                <?php
                            }
                            ?>
                        </table>
                    </div>

                    <?php
                }
            } else {
                ?>
                <div class="CellHead centrado">NO HAY FESTIVOS PARAMETRIZADOS PARA EL A&Ntilde;O <?= $datos->sel_yearxx ?></div>
                <?php
            }
            ?>

        </div>
        <?php
    }

    /* ! \fn: InserFestivo()
     *  \brief: inserta un festivo para la transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 08/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function InsertFestivo() {
        $datos = (object) $_POST;
        $fec_festiv = explode("/", $datos->fec_insert);
        $datos->fec_insert = $fec_festiv[2] . "-" . $fec_festiv[0] . "-" . $fec_festiv[1];
        $sql = "SELECT 1 
        FROM " . BASE_DATOS . ".tab_config_festiv
        WHERE cod_tercer = 1
        AND ind_config = 3
        AND cod_ciudad = 3
        AND fec_festiv = '$datos->fec_insert' ";
        $consulta = new Consulta($sql, self::$cConexion);
        $mExiste = $consulta->ret_matrix("a");
        if ($mExiste) {
            $result = '9999';
        } else {
            $sql = "INSERT INTO " . BASE_DATOS . ".tab_config_festiv
            ( cod_tercer, fec_festiv, ind_config, cod_ciudad )
            VALUES( 1, '$datos->fec_insert', '3', '3' ) ";
            if ($consulta = new Consulta($sql, self::$cConexion)) {
                $result = '1000';
            } else {
                $result = '1991';
            }
        }
        echo $result;
    }

    /* ! \fn: ValidateNumberofDays
     *  \brief: devuelve el numero de dias de un mes de un año 
     *  \author: Ing. Alexander Correa
     *  \date: 23/02/2016
     *  \date modified: dia/mes/año
     *  \param: $mMes     => string => mes a consultar    
     *  \param: $mAno     => string => año a consultar    
     *  \return return
     */

    private function ValidateNumberofDays($mMes, $mAno) {
        $mTotal = 0;
        switch ((int) $mMes) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                $mTotal = 31;
                break;

            case 4:
            case 6:
            case 9:
            case 11:
                $mTotal = 30;
                break;

            case 2:
                if (date('L', mktime(1, 1, 1, 1, 1, $mAno)) == '1')
                    $mTotal = 29;
                else
                    $mTotal = 28;
                break;
        }

        return $mTotal;
    }

    /* ! \fn: deleteFestivo
     *  \brief: elimina un festivo de una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 02/08/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function deleteFestivo() {
        $mData = $_POST;
        if (strlen($mData['dia']) == '1') {
            $mData['dia'] = '0' . $mData['dia'];
        }
        $mFechaDelete = $mData['ano'] . '-' . $mData['mes'] . '-' . $mData['dia'];

        $sql = "DELETE FROM " . BASE_DATOS . ".tab_config_festiv 
        WHERE cod_tercer = '" . $mData['cod_transp'] . "' 
        AND ind_config = '" . $mData['ind_config'] . "' 
        AND cod_ciudad = '" . $mData['cod_ciudad'] . "' 
        AND fec_festiv = '" . $mFechaDelete . "'  ";

        if ($consulta = new Consulta($sql, self::$cConexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    }

    /* ! \fn: getEals
     *  \brief: trae todas las esferas de la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 08/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return arreglo con las esferas
     */

    private function getEals() {
        $sql = "SELECT cod_contro, nom_contro FROM " . BASE_DATOS . ".tab_genera_contro WHERE ind_virtua = 0 AND nom_contro NOT LIKE '%DEST%' AND ind_estado = 1 AND ind_pcpadr = 1";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /* ! \fn: NewParametrizacion
     *  \brief: inserta una configuracion de horario para una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 08/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function NewParametrizacion() {
        $mData = $_POST;
        $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_config_horlab
        ( cod_tercer, com_diasxx, hor_ingres, 
        hor_salida, usr_creaci, fec_creaci, 
        ind_config, cod_ciudad
        )VALUES( '" . $mData['cod_transp'] . "', '" . $mData['nue_combin'] . "', '" . $mData['hor_ingedi'] . "', 
        '" . $mData['hor_saledi'] . "', '" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW(),
        '" . $mData['ind_config'] . "', '" . $mData['cod_ciudad'] . "')";

        if ($consulta = new Consulta($mInsert, self::$cConexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    }

    /* ! \fn: HoraLegible
     *  \brief: funcion que devuelve la hora en un formato legible
     *  \author: Ing. Alexander Correa
     *  \date: 08/02/2016
     *  \date modified: dia/mes/año
     *  \param: $mHorax     => string => cadena con la hora a convertir    
     *  \return string con la hora legible
     */

    private function HoraLegible($mHoraxx) {
        $mDetalle = explode(':', $mHoraxx);
        $ind = 'am';
        if ((int) $mDetalle[0] > 12) {
            $rem = '0' . (int) $mDetalle[0] - 12;
            $mDetalle[0] = $rem;
            $ind = 'pm';
        }
        return $mDetalle[0] . ':' . $mDetalle[1] . ':' . $mDetalle[2] . ' ' . $ind;
    }

    /* ! \fn: registrarTipoServicio
     *  \brief: guarda la configuración parametrizada de el tipo de servicio de una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 08/02/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return
     */

    private function registrarTipoServicio() {
        $datos = (object) $_REQUEST;
        //trae el consecutivo de la tabla
        $query = "SELECT MAX(num_consec) AS num_consec
        FROM " . BASE_DATOS . ".tab_transp_tipser a
        WHERE a.cod_transp = '$datos->cod_transp'";
        $consec = new Consulta($query, self::$cConexion);
        $ultimo = $consec->ret_matriz();

        $datos->num_consec = $ultimo[0][0] + 1;

        $datos->usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];
        $datos->ind_estado = $datos->ind_estado == '1' ? '1' : '0';
        $datos->ind_llegad = $datos->ind_llegad == '1' ? '1' : '0';
        $datos->ind_notage = $datos->ind_notage == '1' ? '1' : '0';
        $datos->ind_calcon = $datos->ind_calcon == '1' ? '1' : '0';
        $datos->ind_segprc = $datos->ind_segprc == '1' ? '1' : '0';
        $datos->ind_segcar = $datos->ind_segcar == '1' ? '1' : '0';
        $datos->ind_segdes = $datos->ind_segdes == '1' ? '1' : '0';
        $datos->ind_segtra = $datos->ind_segtra == '1' ? '1' : '0';
        $datos->ind_planru = $datos->ind_planru == '1' ? '1' : '0';
        $datos->ind_biomet = $datos->ind_biomet == '1' ? '1' : '0';
        $datos->ind_camrut = $datos->ind_camrut == '1' ? '1' : '0';
        $datos->dup_manifi = $datos->dup_manifi == '1' ? '1' : '0';
        $datos->ind_solpol = $datos->ind_solpol == '1' ? '1' : '0';
        $datos->hab_asicar = $datos->hab_asicar == '1' ? '1' : '0';
        $datos->tie_trazab = str_replace('.', ',', $datos->tie_trazab);
        $datos->tie_trazab = $datos->tie_trazab * 60;

        $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_tipser
        (   num_consec, cod_tipser, tie_contro, ind_estado, tie_conurb, ind_llegad,  
            ind_notage, tip_factur, tie_carurb, tie_carnac, tie_carimp, tie_carexp,
            tie_desurb, tie_desnac, tie_desimp, tie_desexp, tie_trazab, ind_excala, 
            ind_calcon, ind_segcar, ind_segtra, ind_segdes, val_regist, val_despac,
            tie_cartr1, tie_cartr2, tie_destr1, tie_destr2, ind_camrut, dup_manifi, 
            ind_biomet, can_llaurb, can_llanac, can_llaimp, can_llaexp, can_llatr1, 
            can_llatr2, fec_iniser, hor_iniser, fec_finser, hor_finser, nom_aplica, 
            ind_segctr, ind_planru, tie_traexp, tie_traimp, tie_tratr1, tie_tratr2, 
            cod_grupox, cod_operac, cod_server, cod_priori, cod_transp, usr_creaci, 
            fec_creaci, hor_pe1nac, hor_pe2nac, hor_pe1urb, hor_pe2urb, hor_pe1exp, 
            hor_pe2exp, hor_pe1imp, hor_pe2imp, hor_pe1tr1, hor_pe2tr1, hor_pe1tr2, 
            hor_pe2tr2, ind_conper, ind_solpol, cod_asegur, num_poliza, fec_valreg, 
            ind_segprc, tie_prcurb, tie_prcnac, tie_prcimp, tie_prcexp, tie_prctr1, 
            tie_prctr2, hab_asicar, fec_asicar
        ) VALUES  ( 
        '$datos->num_consec', '$datos->cod_tipser', '$datos->tie_contro', '$datos->ind_estado', '$datos->tie_conurb', '$datos->ind_llegad', 
        '$datos->ind_notage', '$datos->tip_factur', '$datos->tie_carurb', '$datos->tie_carnac', '$datos->tie_carimp', '$datos->tie_carexp', 
        '$datos->tie_desurb', '$datos->tie_desnac', '$datos->tie_desimp', '$datos->tie_desexp', '$datos->tie_trazab', '$datos->ind_excala', 
        '$datos->ind_calcon', '$datos->ind_segcar', '$datos->ind_segtra', '$datos->ind_segdes', '$datos->val_regist', '$datos->val_despac', 
        '$datos->tie_cartr1', '$datos->tie_cartr2', '$datos->tie_destr1', '$datos->tie_destr2', '$datos->ind_camrut', '$datos->dup_manifi', 
        '$datos->ind_biomet', '$datos->can_llaurb', '$datos->can_llanac', '$datos->can_llaimp', '$datos->can_llaexp', '$datos->can_llatr1', 
        '$datos->can_llatr2', '$datos->fec_iniser', '$datos->hor_iniser', '$datos->fec_finser', '$datos->hor_finser', '$datos->nom_aplica', 
        '$datos->ind_segtra', '$datos->ind_planru', '$datos->tie_traexp', '$datos->tie_traimp', '$datos->tie_tratr1', '$datos->tie_tratr2', 
        '$datos->cod_grupox', '$datos->cod_operac', '$datos->cod_server', '$datos->cod_priori', '$datos->cod_transp', '$datos->usr_creaci', NOW(), 
        '$datos->hor_pe1nac', '$datos->hor_pe2nac', '$datos->hor_pe1urb', '$datos->hor_pe2urb', '$datos->hor_pe1exp', '$datos->hor_pe2exp', 
        '$datos->hor_pe1imp', '$datos->hor_pe2imp', '$datos->hor_pe1tr1', '$datos->hor_pe2tr1', '$datos->hor_pe1tr2', '$datos->hor_pe2tr2', 
        '$datos->ind_conper', '$datos->ind_solpol', '$datos->cod_asegur', '$datos->num_poliza', '$datos->fec_valreg', '$datos->ind_segprc',
        '$datos->tie_prcurb', '$datos->tie_prcnac', '$datos->tie_prcimp', '$datos->tie_prcexp', '$datos->tie_prctr1', '$datos->tie_prctr2',
        '$datos->hab_asicar', '$datos->fec_asicar'
        )";

        $mSql = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                    SET cod_estado = $datos->ind_estado,
                        usr_modifi = '$datos->usr_creaci',
                        fec_modifi = NOW()
                        WHERE cod_tercer = $datos->cod_transp";
        $insercion = new Consulta($mSql, self::$cConexion, "C");

        if ($datos->eal != array()) {
            $consulta = new Consulta($query, self::$cConexion, "BR");
            $fecini = $datos->fecini;
            $precio = $datos->precio;
            $fecfin = $datos->fecfin;
            //se eliminan los puestos para quecuando actualice no queden los que ya no utilizan
            $sql = "DELETE FROM " . BASE_DATOS . ".tab_ealxxx_transp 
                          WHERE cod_transp = '$datos->cod_transp' ";
            $consulta = new Consulta($sql, self::$cConexion);
            
            foreach ($datos->eal as $key => $value) {
                $sql1 = "SELECT con_ealtra FROM " . BASE_DATOS . ".tab_ealxxx_transp WHERE cod_transp = '$datos->cod_transp' AND cod_ealxxx = '$value'";
                $consulta = new Consulta($sql1, self::$cConexion);
                $consulta = $consulta->ret_matrix("a");
                if (!$consulta) {
                    $sql2 = "INSERT INTO " . BASE_DATOS . ".tab_ealxxx_transp 
                    (cod_ealxxx,cod_transp,val_ealxxx,fec_inieal,
                    fec_fineal,fec_creaci,usr_creaci)VALUES('$value', '$datos->cod_transp', '$precio[$key]', '$fecini[$key]',
                    '$fecfin[$key]', NOW(), '$datos->usr_creaci');";
                } else {
                    $sql2 = "UPDATE " . BASE_DATOS . ".tab_ealxxx_transp SET 
                    val_ealxxx = '$precio[$key]', 
                    fec_inieal = '$fecini[$key]',
                    fec_fineal = '$fecfin[$key]',
                    fec_modifi = NOW(),
                    usr_modifi = '$datos->usr_creaci' 
                    WHERE 
                    cod_transp = '$datos->cod_transp' 
                    AND cod_ealxxx = '$value' ";
                }
                if ($key + 1 == sizeof($datos->eal)) {
                    if ($consulta = new Consulta($sql2, self::$cConexion, "C")) {
                        echo true;
                    } else {
                        echo false;
                    }
                } else {
                    $consulta = new Consulta($sql2, self::$cConexion, "R");
                }
            }
        } else {
            if ($consulta = new Consulta($query, self::$cConexion, "C")) {
                echo true;
            } else {
                echo false;
            }
        }
    }

    /* ! \fn: getAseguradoras
     *  \brief: trae la lista de las empresas con actividad aseguradora
     *  \author: Ing. Alexander Correa
     *  \date: 07/04/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */

    private function getAseguradoras() {

        $sql = "SELECT a.cod_tercer, a.abr_tercer FROM " . BASE_DATOS . ".tab_tercer_tercer a 
                                            INNER JOIN " . BASE_DATOS . ".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer 
                                            INNER JOIN " . BASE_DATOS . ".tab_genera_activi c ON c.cod_activi = b.cod_activi 
                                            WHERE c.cod_activi = 7 ";
        echo "<pre> sql";
        print_r($sql);
        echo "</pre>";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    private function getAgencias() {
        $datos = (object) $_POST;
        $sql = "SELECT a.cod_agenci, a.nom_agenci 
                  FROM " . BASE_DATOS . ".tab_genera_agenci a 
            INNER JOIN " . BASE_DATOS . ".tab_transp_agenci b 
                    ON a.cod_agenci = b.cod_agenci 
            INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c 
                    ON b.cod_transp = c.cod_tercer
                 WHERE c.cod_tercer = '$datos->cod_transp'
                   AND a.cod_estado = '1' 
                   GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($sql, self::$cConexion);
        //return $consulta->ret_matrix("a");
        return $consulta -> ret_matrix('i');
    }
    
    private function getContact() {
        $datos = (object) $_POST;
        $sql = "SELECT a.cod_agenci 
                  FROM " . BASE_DATOS . ".tab_contac_empres a
                WHERE a.cod_transp = '$datos->cod_transp'";
        $consulta = new Consulta($sql, self::$cConexion);
        $agencias = $consulta->ret_matrix("a");
        $temp = array();
        foreach ($agencias as $agencia) {
            $temp[] = $agencia['cod_agenci'];
        }

        $sql = "SELECT a.nom_contac, a.car_contac, a.ema_contac, a.tel_contac, a.obs_contac
                  FROM " . BASE_DATOS . ".tab_contac_empres a 
            INNER JOIN " . BASE_DATOS . ".tab_transp_tipser b ON a.cod_transp = b.cod_transp
            WHERE b.cod_transp = '$datos->cod_transp'
            GROUP BY 1, 3";
        $consulta = new Consulta($sql, self::$cConexion);
        $contactos = $consulta->ret_matrix("a");

        return $contactos;
    }

    private function getAgenContac($ema_contac) {
        $datos = (object) $_POST;
        $sql = "SELECT a.cod_agenci
                  FROM " . BASE_DATOS . ".tab_contac_empres a
                WHERE a.cod_transp = '$datos->cod_transp'
                AND a.ema_contac = '$ema_contac'";
        $consulta = new Consulta($sql, self::$cConexion);
        $agencias = $consulta->ret_matrix("a");

        $temp = array();
        foreach ($agencias as $agencia) {
            $temp[] = $agencia['cod_agenci'];
        }

        $sql = "SELECT c.nom_agenci, a.nom_contac, c.cod_agenci
                  FROM " . BASE_DATOS . ".tab_contac_empres a 
            INNER JOIN " . BASE_DATOS . ".tab_transp_tipser b ON a.cod_transp = b.cod_transp,   
                       " . BASE_DATOS . ".tab_genera_agenci c
            WHERE b.cod_transp = '$datos->cod_transp'
            AND a.ema_contac = '$ema_contac'
            AND c.cod_agenci IN (".join(",", $temp) . ")
            GROUP BY 2, 1";
        $consulta = new Consulta($sql, self::$cConexion);
        $ageContac = $consulta->ret_matrix("a");

        return $ageContac;
    }

    /* ! \fn: deleteConfiguracion
     *  \brief: elimina una configuracion laboral de una empresa
     *  \author: Ing. Alexander Correa
     *  \date: 16/05/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return boolean 
     */

    private function deleteConfiguracion() {
        $datos = (object) $_POST;
        $sql = "DELETE FROM " . BASE_DATOS . ".tab_config_horlab 
    				  WHERE cod_tercer = '$datos->cod_transp' 
    				  AND com_diasxx = '$datos->dia' 
    				  AND ind_config = $datos->ind_config 
    				  AND cod_ciudad = $datos->ind_config ";
        if ($consulta = new Consulta($sql, self::$cConexion)) {
            die('1');
        } else {
            die('0');
        }
    }

    /* ! \fn: deleteConfiguracion
     *  \brief: elimina una configuracion laboral de una empresa
     *  \author: Ing. Alexander Correa
     *  \date: 16/05/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return boolean 
     */

    private function deleteContac() {
        $datos = (object) $_POST;
        $sql = "DELETE FROM " . BASE_DATOS . ".tab_contac_empres
                      WHERE cod_transp = '$datos->cod_transp' 
                      AND ema_contac = '$datos->ema_contac' ";
        if ($consulta = new Consulta($sql, self::$cConexion)) {
            die('1');
        } else {
            die('0');
        }
    }

    /* ! \fn: CreateContac
     *  \brief: inserta un nuevo contacto
     *  \author: Ing. Andres Torres
     *  \date: 12/02/2018
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function CreateContac() {
        $datos = (object) $_POST;
            if ($_POST['ind_edicio'] == '0') {
                ?>
                <div class="StyleDIV contenido" style="min-height: 145px !important;">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10">
                        <div class="col-md-6">  
                            <div class="col-md-6 text-right">Nombre Contacto<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="nom_contac" id="nom_contacID" validate="text" obl="1" maxlength="250" minlength="3">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Movil:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho"  onkeypress="return NumericInput(event)" name="tel_contac" id="tel_contacID" validate="numero" obl="1" maxlength="10" minlength="3">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="col-md-6 text-right">E-mail:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="ema_contac" id="ema_contacID" validate="text" obl="1" maxlength="50" minlength="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Cargo:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="car_contac" id="car_contacID" validate="text" obl="1" maxlength="20" minlength="10">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="col-md-5 text-right">Observaciones:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="obs_contac" id="obs_contacID" validate="text" obl="1" maxlength="250" minlength="10">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="col-md-5 text-right">Agencia:<font style="color:red">*</font></div>
                            <div class="col-md-5 text-left" style="width:170px !important">
                            <?php
                                 $agencias = $this->getAgencias();
                                 $cNull = array( array('25', '-----') );
                                 $mHtml1 = lista( '','cod_agenci', array_merge(self::$cNull,$agencias), 'cellInfo1'   );
                                 echo $mHtml1;
                            ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    </div>
            <?php
            }else{
            ?>
            <div class="StyleDIV contenido" style="min-height: 145px !important;">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10">
                        <div class="col-md-6">  
                            <div class="col-md-6 text-right">Nombre Contacto<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="nom_contac" id="nom_contacID" validate="text" obl="1" maxlength="250" minlength="3" value="<?= $_POST['nom_contac'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Movil:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text"  onkeypress="return NumericInput(event)" class="text-center ancho" name="tel_contac" id="tel_contacID" validate="numero" obl="1" maxlength="10" minlength="3" value="<?= $_POST['tel_contac'] ?>" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="col-md-6 text-right">E-mail:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="mail" class="text-center ancho" name="ema_contac" id="ema_contacID" validate="text" obl="1" maxlength="50" minlength="10" value="<?= $_POST['ema_contac'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Cargo:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="car_contac" id="car_contacID" validate="text" obl="1" maxlength="20" minlength="10" value="<?= $_POST['car_contac'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6 text-right">Observaciones:<font style="color:red">*</font></div>
                            <div class="col-md-6 text-left">
                                <input type="text" class="text-center ancho" name="obs_contac" id="obs_contacID" validate="text" obl="1" maxlength="250" minlength="10" value="<?= $_POST['obs_contac'] ?>" >
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="col-md-5 text-right">Agencia:<font style="color:red">*</font></div>
                            <div class="col-md-5 text-left" style="width:170px !important">
                            <?php
                                 $agencias = $this->getAgencias();
                                 $cNull = array( array('25', '-----') );
                                 $mHtml1 = lista( '','cod_agenci', array_merge(self::$cNull,$agencias), 'cellInfo1');
                                 echo $mHtml1;
                            ?>
                            </div>
                            <input type="hidden" id="sel_agenciID" name="sel_agenci" value="">
                        </div>
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    </div>
        <?php
        }
    }



    /* ! \fn: NewContac
     *  \brief: inserta un contacto de la transportado
     *  \author: Ing. Andres Torres
     *  \date: 08/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function NewContac() {
        $mData = $_POST;

        $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_contac_empres
        ( cod_transp, nom_contac, car_contac, 
        ema_contac, tel_contac, obs_contac, cod_agenci,
        usr_creaci, fec_creaci
        )VALUES( '" . $mData['cod_transp'] . "', '" . $mData['nom_contac'] . "', '" . $mData['car_contac'] . "', 
        '" . $mData['ema_contac'] . "', '".$mData['tel_contac']."', '".$mData['obs_contac']."', '".$mData['cod_agenci']."' ,'" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW() )";

        if ($consulta = new Consulta($mInsert, self::$cConexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    }

        /* ! \fn: editContac
     *  \brief: inserta un contacto de la transportado
     *  \author: Ing. Andres Torres
     *  \date: 08/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function editContac() {
        $mData = $_POST;
        $mUpdate = "UPDATE " . BASE_DATOS . ".tab_contac_empres SET 
                    nom_contac = '".$mData['nom_contac']."', 
                    ema_contac = '".$mData['ema_contac']."', 
                    tel_contac = '".$mData['tel_contac']."', 
                    car_contac = '".$mData['car_contac']."', 
                    obs_contac = '".$mData['obs_contac']."', 
                    cod_agenci = '".$mData['cod_agenci']."', 
                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                    fec_modifi = NOW()
                    WHERE 
                    cod_transp = '".$mData['cod_transp']."'
                    AND ema_contac = '".$mData['email']."'";

        if ($consulta = new Consulta($mUpdate, self::$cConexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    }

}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new ajax_certra_certra();
}
?>

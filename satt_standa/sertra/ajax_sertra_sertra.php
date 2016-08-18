<?php
/* ! \file: ajax_certra_certra.php
 *  \brief: archivo con multiples funciones para la configuracion del os tipos de servicio de una transportadora
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 01/02/2016
 *  \bug: 
 *  \bug: 
 *  \warning:
 */

setlocale(LC_ALL, "es_ES");

class ajax_certra_certra {

    private static $cConexion,
            $cCodAplica,
            $cUsuario,
            $cTotalDespac,
            $cNull = array(array('', '-----'));
    var $Week = array(
        'L' => 'Lunes',
        'M' => 'Martes',
        'X' => 'Mi&eacute;rcoles',
        'J' => 'Jueves',
        'V' => 'Viernes',
        'S' => 'S&aacute;bado',
        'D' => 'Domingo'
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
                case "registrarTipoServicio";
                    $this->registrarTipoServicio();
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
            ind_notage, tip_factur, tie_carurb, tie_carnac, tie_carimp, tie_carexp,
            tie_desurb, tie_desnac, tie_desimp, tie_desexp, tie_trazab, ind_excala, 
            ind_calcon, ind_segcar, ind_segtra, ind_segdes, val_regist, tie_cartr1, 
            tie_cartr2, tie_destr1, tie_destr2, ind_camrut, dup_manifi, ind_biomet, 
            can_llaurb, can_llanac, can_llaimp, can_llaexp, can_llatr1, can_llatr2, 
            fec_iniser, hor_iniser, fec_finser, hor_finser, nom_aplica, 
            ind_planru, tie_traexp, tie_traimp, tie_tratr1, tie_tratr2, cod_grupox, 
            cod_operac, cod_priori, ind_conper, hor_pe1urb, hor_pe2urb, hor_pe1nac, 
            hor_pe2nac, hor_pe1imp, hor_pe2imp, hor_pe1exp, hor_pe2exp, hor_pe1tr1, 
            hor_pe2tr1, hor_pe1tr2, hor_pe2tr2, ind_solpol, cod_asegur, num_poliza
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

        //consulto los horarios laborales de la transportadora y los agrego al objeto principal
        $sql = "SELECT com_diasxx, hor_ingres, hor_salida FROM " . BASE_DATOS . ".tab_config_horlab WHERE cod_tercer = '$datos->cod_transp' AND ind_config = 3 AND cod_ciudad = 3 ";
        $consulta = new Consulta($sql, self::$cConexion);
        $conf = $consulta->ret_matrix("a");
        $datos->configuracion = $conf;

        //consulto las esferas que tenga configuradas la empresa
        $sql = "SELECT cod_ealxxx, val_ealxxx, fec_inieal, fec_fineal FROM " . BASE_DATOS . ".tab_ealxxx_transp WHERE cod_transp = '$datos->cod_transp'";
        $consulta = new Consulta($sql, self::$cConexion);
        $esferas = $consulta->ret_matrix("a");
        $datos->esferas = $esferas;

        $this->pintarFormulario($datos);
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
        ?>
        <div id="conf_servicioID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Configuración del Servicio</b></h3>
            <div id="contenido_conf">
                <div class="StyleDIV contenido" style="min-height: 165px !important;">
                    <div class="col-md-3 centrado">Fecha Inicio del Servicio<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <input type="text" value="<?= $datos->principal['fec_iniser'] ?>" class="fecha centrado ancho" obl="1" name="fec_iniser" validate="date" id="fec_iniserID" maxlength="10" minlength="10" obl="true" >
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Hora Inicio del Servicio<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <input type="text" readonly="true" value="<?= $datos->principal['hor_iniser'] ?>" class="hora centrado ancho" obl="1" name="hor_iniser" validate="dir" id="hor_iniserID" maxlength="5" minlength="5" obl="true" >
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Fecha Fin del Servicio<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <input type="text" value="<?= $datos->principal['fec_finser'] ?>" class="fecha centrado ancho" obl="1" name="fec_finser" validate="date" id="fec_finserID" maxlength="10" minlength="10" obl="true" >
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Hora Fin del Servicio<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <input type="text" readonly="true" value="<?= $datos->principal['hor_finser'] ?>" obl="1"  class="hora centrado ancho" name="hor_finser" validate="dir" id="hor_finserID" maxlength="5" minlength="5" obl="true" >
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Tipo de Servicio Contratado<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <select id="cod_tipserID" name="cod_tipser" obl="1" validate="select">
                            <option value="">Seleccione una Opción.</option>
                            <?php
                            foreach ($datos->servicios as $key => $value) {
                                $sel = "";
                                if ($value['cod_tipser'] == $datos->principal['cod_tipser']) {
                                    $sel = "selected";
                                }
                                ?>
                                <option <?= $sel ?> value="<?= $value['cod_tipser'] ?>"><?= $value['nom_tipser'] ?>.</option>
        <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Valor del Registro<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <input type="text" class="centrado ancho" value="<?= $datos->principal['val_regist'] ?>" name="val_regist" id="val_registID" validate="numero" obl="1" maxlength="4" minlength="3" >
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Interfaz con Servidor<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <select id="cod_serverID" name="cod_server" obl="1" validate="select">
                            <option value="">Seleccione una Opción.</option>
                            <?php
                            foreach ($datos->servers as $key => $value) {
                                $sel = "";
                                if ($value['cod_server'] == $datos->principal['cod_server']) {
                                    $sel = "selected";
                                }
                                ?>
                                <option <?= $sel ?> value="<?= $value['cod_server'] ?>"><?= $value['nom_server'] ?>.</option>
        <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Nombre de la Aplicación<font style="color:red">*</font></div>
                    <div class="col-md-2 centrado">
                        <input type="text" class="centrado ancho" value="<?= $datos->principal['nom_aplica'] ?>" name="nom_aplica" id="nom_aplicaID" validate="texto" obl="1" maxlength="15" minlength="4" >
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Activar Solicitud de Pólizas</div>
                    <div class="col-md-2 centrado">
                        <?php
                            $ind_solpol = "";
                            if ($datos->principal['ind_solpol'] == 1) {
                                $ind_solpol = "checked='true'";
                            }
                        ?>
                        <input <?=  $ind_solpol ?> type="checkbox" name="ind_solpol" id="ind_solpol" value="1"></input>
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Aseguradora</div>
                    <div class="col-md-2 centrado">
                        <select name="cod_asegur" id="cod_asegur">
                            <option value="">Seleccione una Opción</option>
                            <?php
                            $aseguradoras = $this->getAseguradoras();
                            foreach ($aseguradoras as $key => $value) {
                                $sel = "";
                                if ($value['cod_tercer'] == $datos->principal['cod_asegur']) {
                                    $sel = "selected";
                                }
                                ?>
                                <option <?= $sel ?> value="<?= $value['cod_tercer'] ?>"> <?= $value['abr_tercer'] ?></option>
        <?php } ?>
                        </select>
                    </div>                   
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-3 centrado">Número de Póliza</div>
                    <div class="col-md-2 centrado">
                        <input type="text" name="num_poliza" id="num_poliza" value="<?= $datos->principal['num_poliza'] ?>" validate="dir" maxlength="20" minlength="5"></input>
                    </div>
                    <div class="col-md-6">&nbsp;</div>                    
                </div>
            </div>
        </div>
        <div id="conf_servicioID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Horario del Servicio Contratado</b></h3>
            <div id="contenido_serv">
                <div class="StyleDIV contenido" style="min-height: 180px !important;" >
                    <div class="col-md-12 CellHead"  style="text-align:center;">Horarios Laborales <input type="button" value="Nueva Configuración" class="small save  ui-state-default ui-corner-all" onclick="CreateConfig('<?= $datos->cod_transp ?>', 3, 3)"></div>
        <?php if (!$datos->configuracion) { ?>
                        <div class="col-md-12" style="text-align:center;">Actualmente no tiene una configuración parametrizada</div> 
        <?php } else { ?>
                        <div class="col-md-12 contenido" id="mensaje"></div>
                        <div class="col-md-12 CellHead centrado" id="mensaje"><b>DIAS DE LA SEMANA PARAMETRIZADOS</b></div>
                        <div class="col-md-12 contenido"></div>
                        <div class="col-md-6 CellHead centrado"><b>Día</b></div>
                        <div class="col-md-3 CellHead centrado"><b>Hora de Ingreso</b></div>
                        <div class="col-md-3 CellHead centrado"><b>Hora de Salida</b></div>
                        <?php
                        foreach ($datos->configuracion as $row) {
                            $mDiasxx = '';
                            foreach (explode('|', $row['com_diasxx']) as $nameWeek) {
                                $mDiasxx .= $mDiasxx != '' ? ', ' . $this->Week[$nameWeek] : $this->Week[$nameWeek];
                            }
                            ?>

                            <div class="col-md-6 contenido centrado" ><?= $mDiasxx ?></div> 
                            <div class="col-md-3 contenido centrado"><?= $this->HoraLegible($row['hor_ingres']) ?></div>  
                            <div class="col-md-3 contenido centrado"><?= $this->HoraLegible($row['hor_salida']) ?></div>   

                            <?php
                        }
                    }
                    ?>
                    <div class="col-md-12 CellHead"  style="text-align:center;">Festivos por Año</div>
                    <div class="col-md-12" style="text-align:center;">
                        <input id="ind_configID" type="hidden" value="3" name="ind_config">
                        <input id="cod_ciudadID" type="hidden" value="3" name="cod_ciudad">
                        <select id="sel_yearxxID" name="sel_yearxx" onchange="setFestivos()">
                            <option value="">Seleccione una Opción</option>
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
                <div class="StyleDIV" style="min-height: 440px !important;">
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
                    <div class="col-md-2 CellHead">Exportación</div>
                    <div class="col-md-2 CellHead">Importación</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carnac" id="tie_carnacID" value="<?= $datos->principal['tie_carnac'] ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carurb" id="tie_carurbID" value="<?= $datos->principal['tie_carurb'] ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carexp" id="tie_carexpID" value="<?= $datos->principal['tie_carexp'] ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_carimp" id="tie_carimpID" value="<?= $datos->principal['tie_carimp'] ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_cartr1" id="tie_cartr1ID" value="<?= $datos->principal['tie_cartr1'] ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" name="tie_cartr2" id="tie_cartr2ID" value="<?= $datos->principal['tie_cartr2'] ?>" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-6 CellHead centrado" >
                        Tránsito
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
                        Recalculo Según Plan de Ruta 
                        <input <?= $ind_planru ?> type="checkbox" name="ind_planru" id="ind_planruID" value="1" onclick="enableDisable(3)">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>
                    <div class="col-md-2 CellHead">Nacional</div>
                    <div class="col-md-2 CellHead">Urbano</div>
                    <div class="col-md-2 CellHead">Exportación</div>
                    <div class="col-md-2 CellHead">Importación</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_contro'] ?>" name="tie_contro" id="tie_controID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_conurb'] ?>" name="tie_conurb" id="tie_conurbID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_traexp'] ?>" name="tie_traexp" id="tie_traexpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_traimp'] ?>" name="tie_traimp" id="tie_traimpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_tratr1'] ?>" name="tie_tratr1" id="tie_tratr1ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
                    <div class="col-md-2 contenido"><input type="text" value="<?= $datos->principal['tie_tratr2'] ?>" name="tie_tratr2" id="tie_tratr2ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado"></div>
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
                    <div class="col-md-2 CellHead">Exportación</div>
                    <div class="col-md-2 CellHead">Importación</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desnac'] ?>" name="tie_desnac" id="tie_desnacID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desurb'] ?>" name="tie_desurb" id="tie_desurbID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desexp'] ?>" name="tie_desexp" id="tie_desexpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_desimp'] ?>" name="tie_desimp" id="tie_desimpID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_destr1'] ?>" name="tie_destr1" id="tie_destr1ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-2 contenido">
                        <input type="text" value="<?= $datos->principal['tie_destr2'] ?>" name="tie_destr2" id="tie_destr2ID" validate="numero" maxlength="3" minlength="1" class="ancho centrado">
                    </div>
                    <div class="col-md-12 contenido">&nbsp;</div>

                    <div class="col-md-12 CellHead centrado">
                        Requiere Confinración de Pernoctación
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
                    <div class="col-md-2 CellHead">Exportación</div>
                    <div class="col-md-2 CellHead">Importación</div>
                    <div class="col-md-2 CellHead">Tramo D1</div>
                    <div class="col-md-2 CellHead">Tramo D2</div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1nac'] ?>" name="hor_pe1nac" id="hor_pe1nacID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2nac'] ?>" name="hor_pe2nac" id="hor_pe2nacID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1urb'] ?>" name="hor_pe1urb" id="hor_pe1urbID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2urb'] ?>" name="hor_pe2urb" id="hor_pe2urbID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1exp'] ?>" name="hor_pe1exp" id="hor_pe1expID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2exp'] ?>" name="hor_pe2exp" id="hor_pe2expID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1imp'] ?>" name="hor_pe1imp" id="hor_pe1impID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2imp'] ?>" name="hor_pe2imp" id="hor_pe2impID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1tr1'] ?>" name="hor_pe1tr1" id="hor_pe1tr1ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2tr1'] ?>" name="hor_pe2tr1" id="hor_pe2tr1ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe1tr2'] ?>" name="hor_pe1tr2" id="hor_pe1tr2ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                    <div class="col-md-1 contenido">
                        <input type="text" value="<?= $datos->principal['hor_pe2tr2'] ?>" name="hor_pe2tr2" id="hor_pe2tr2ID" validate="dir" maxlength="5" minlength="0" class="ancho centrado hora" placeholder="00:00" readonly="true">
                    </div>
                </div>
            </div>
        </div>
        <div id="conf_etapasID" class="col-md-12 accordion defecto ancho">
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
                        Notificación por Agencia 
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
                        Grupo al que Pertenece 
                        <select id="cod_grupoxID" name="cod_grupox">
                            <option value="">Seleccione una Opción</option>
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
                        Prioridad 
                        <select id="cod_prioriID" name="cod_priori">
                            <option value="">Seleccione una Opción</option>
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
                        <select id="cod_operacID" name="cod_operac">
                            <option value="">Seleccione una Opción</option>
                            <?php
                            foreach ($operaciones as $key => $value) {
                                if ($value['cod_operac'] == $datos->principal['cod_operac']) {
                                    $cod_operac = "selected='true'";
                                } else {
                                    $cod_operac = "";
                                }
                                ?>
                                <option <?= $cod_operac ?>value="<?= $value['cod_operac'] ?>"> <?= utf8_encode($value['nom_operac']) ?></option>
            <?php
        }
        ?>
                        </select>
                        Tipo de Operación
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                </div>
            </div>
        </div>
        <div id="conf_ealID" class="col-md-12 accordion defecto ancho">
            <h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>EAL Contratadas</b></h3>
            <div id="contenido_serv">
                <div class="StyleDIV contenido" style="min-height: <?= (count($eals) * 25 + 70) ?>px !important;" >
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
        $sql = "SELECT com_diasxx 
        FROM " . BASE_DATOS . ".tab_config_horlab
        WHERE cod_tercer = '$datos->cod_tercer' 
        AND ind_config = '$datos->ind_config' 
        AND cod_ciudad = '$datos->cod_ciudad' ";

        $consulta = new Consulta($sql, self::$cConexion);
        $_CONFIG = $consulta->ret_matrix("a");
        $mActual = '';
        foreach ($_CONFIG as $key => $value) {
            $mActual .= $mActual != '' ? '|' . $value['com_diasxx'] : $value['com_diasxx'];
        }

        $mArrayActual = array();
        foreach (explode('|', $mActual) as $llave) {
            $mArrayActual[] = $llave;
        }
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
                            <div class="col-md-4 izquierda"><input type="checkbox" name="nom_diasxx" value="<?= $mDay ?>" /></div>
                <?php
            }
        }
        ?>
                </div>
                <div class="col-md-3 cellInfo1"></div>
                <div class="col-md-12">
                    <div class="col-md-6 CellHead centrado"><b>HORARIO DE ENTRADA</b></div>
                    <div class="col-md-6 CellHead centrado"><b>HORARIO DE SALIDA</b></div>
                </div>

                <div class="col-md-6 cellInfo1 centrado negro"><input type="text" class="hora centrado" name="hor_ingres" id="hor_ingresID" value="08:00" /></div>
                <div class="col-md-6 cellInfo1 centrado negro"><input type="text" class="hora centrado" name="hor_salida" id="hor_salidaID" value="16:00" /></div>       
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
        WHERE cod_tercer = '$datos->cod_transp' 
        AND ind_config = '$datos->ind_config' 
        AND cod_ciudad = '$datos->cod_ciudad' 
        AND YEAR( fec_festiv ) = '$datos->sel_yearxx' ";
        $consulta = new Consulta($sql, self::$cConexion);
        $mFechas = $consulta->ret_matrix("a");
        ?>

        <div class="col-md-12">
            <div class="col-md-6 centrado"><input type="text" style="width:100%" class="fecha centrado negro" value="<?= $fecha ?>" name="fec_insert" id="fec_insertID" readonly="true" /></div>
            <div class="col-md-6 centrado"><input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" name="Registrar" value="Registrar" onclick="InsertFestivo('<?= $datos->cod_transp ?>', '<?= $datos->ind_config ?>', '<?= $datos->cod_ciudad ?>');"/></div>
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
                <div class="col-md-12 CellHead">FESTIVOS PARAMETRIZADOS DE AÑO <?= $datos->sel_yearxx ?><br>Para Eliminar un Festivo Haga Click sobre el dia dentro del Calendario</div>
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
                <div class="CellHead centrado">NO HAY FESTIVOS PARAMETRIZADOS PARA EL AÑO <?= $datos->sel_yearxx ?></div>
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
        $sql = "SELECT 1 
        FROM " . BASE_DATOS . ".tab_config_festiv
        WHERE cod_tercer = '$datos->cod_transp' 
        AND ind_config = '$datos->ind_config' 
        AND cod_ciudad = '$datos->cod_ciudad' 
        AND fec_festiv = '$datos->fec_insert' ";
        $consulta = new Consulta($sql, self::$cConexion);
        $mExiste = $consulta->ret_matrix("a");
        if ($mExiste) {
            $result = '9999';
        } else {
            $sql = "INSERT INTO " . BASE_DATOS . ".tab_config_festiv
            ( cod_tercer, fec_festiv, ind_config, cod_ciudad )
            VALUES( '$datos->cod_transp', '$datos->fec_insert', '$datos->ind_config', '$datos->cod_ciudad' ) ";
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
        $datos->ind_segcar = $datos->ind_segcar == '1' ? '1' : '0';
        $datos->ind_segdes = $datos->ind_segdes == '1' ? '1' : '0';
        $datos->ind_segtra = $datos->ind_segtra == '1' ? '1' : '0';
        $datos->ind_planru = $datos->ind_planru == '1' ? '1' : '0';
        $datos->ind_biomet = $datos->ind_biomet == '1' ? '1' : '0';
        $datos->ind_camrut = $datos->ind_camrut == '1' ? '1' : '0';
        $datos->dup_manifi = $datos->dup_manifi == '1' ? '1' : '0';
        $datos->ind_solpol = $datos->ind_solpol == '1' ? '1' : '0';
        $datos->tie_trazab = str_replace('.', ',', $datos->tie_trazab);
        $datos->tie_trazab = $datos->tie_trazab * 60;

        $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_tipser
        (   num_consec, cod_tipser, tie_contro, ind_estado, tie_conurb, ind_llegad,  
            ind_notage, tip_factur, tie_carurb, tie_carnac, tie_carimp, tie_carexp,
            tie_desurb, tie_desnac, tie_desimp, tie_desexp, tie_trazab, ind_excala, 
            ind_calcon, ind_segcar, ind_segtra, ind_segdes, val_regist, tie_cartr1, 
            tie_cartr2, tie_destr1, tie_destr2, ind_camrut, dup_manifi, ind_biomet, 
            can_llaurb, can_llanac, can_llaimp, can_llaexp, can_llatr1, can_llatr2, 
            fec_iniser, hor_iniser, fec_finser, hor_finser, nom_aplica, 
            ind_planru, tie_traexp, tie_traimp, tie_tratr1, tie_tratr2, cod_grupox, 
            cod_operac, cod_server, cod_priori, cod_transp, usr_creaci, fec_creaci, 
            hor_pe1nac, hor_pe2nac, hor_pe1urb, hor_pe2urb, hor_pe1exp, hor_pe2exp, 
            hor_pe1imp, hor_pe2imp, hor_pe1tr1, hor_pe2tr1, hor_pe1tr2, hor_pe2tr2, 
            ind_conper, ind_solpol, cod_asegur, num_poliza
        ) VALUES  ( 
        '$datos->num_consec', '$datos->cod_tipser', '$datos->tie_contro', '$datos->ind_estado', '$datos->tie_conurb', '$datos->ind_llegad', 
        '$datos->ind_notage', '$datos->tip_factur', '$datos->tie_carurb', '$datos->tie_carnac', '$datos->tie_carimp', '$datos->tie_carexp', 
        '$datos->tie_desurb', '$datos->tie_desnac', '$datos->tie_desimp', '$datos->tie_desexp', '$datos->tie_trazab', '$datos->ind_excala', 
        '$datos->ind_calcon', '$datos->ind_segcar', '$datos->ind_segtra', '$datos->ind_segdes', '$datos->val_regist', '$datos->tie_cartr1', 
        '$datos->tie_cartr2', '$datos->tie_destr1', '$datos->tie_destr2', '$datos->ind_camrut', '$datos->dup_manifi', '$datos->ind_biomet', 
        '$datos->can_llaurb', '$datos->can_llanac', '$datos->can_llaimp', '$datos->can_llaexp', '$datos->can_llatr1', '$datos->can_llatr2', 
        '$datos->fec_iniser', '$datos->hor_iniser', '$datos->fec_finser', '$datos->hor_finser', '$datos->nom_aplica', 
        '$datos->ind_planru', '$datos->tie_traexp', '$datos->tie_traimp', '$datos->tie_tratr1', '$datos->tie_tratr2', '$datos->cod_grupox', 
        '$datos->cod_operac', '$datos->cod_server', '$datos->cod_priori', '$datos->cod_transp', '$datos->usr_creaci', NOW(), 
        '$datos->hor_pe1nac', '$datos->hor_pe2nac', '$datos->hor_pe1urb', '$datos->hor_pe2urb', '$datos->hor_pe1exp', '$datos->hor_pe2exp', 
        '$datos->hor_pe1imp', '$datos->hor_pe2imp', '$datos->hor_pe1tr1', '$datos->hor_pe2tr1', '$datos->hor_pe1tr2', '$datos->hor_pe2tr2', 
        '$datos->ind_conper', '$datos->ind_solpol', '$datos->cod_asegur', '$datos->num_poliza')";

        if ($datos->eal != array()) {
            $consulta = new Consulta($query, self::$cConexion, "BR");
            $fecini = $datos->fecini;
            $precio = $datos->precio;
            $fecfin = $datos->fecfin;
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

}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new ajax_certra_certra();
}
?>

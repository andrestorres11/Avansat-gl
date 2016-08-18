<?php
/* ! \file: ajax_horari_monito.php
 *  \brief: archivo con multiples funciones para la configuracion de usuarios de faro para gestionar transportadoras
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 11/02/2016
 *  \bug: 
 *  \bug: 
 *  \warning:
 */

class ajax_horari_monito {

    private static $cConexion,
    $cCodAplica,
    $cUsuario,
    $cTotalDespac;

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

            switch ($opcion) {
                case "getDataList":
                    $this->getDataList();
                break;
                case "registrar":
                    $this->registrar();
                break;
                case "buscarUsuario":
                    $this->buscarUsuario();
                break;
                case "comprobar":
                    $this->comprobar();
                break;
                case "EliminarConfiguracion":
                    $this->EliminarConfiguracion();
                break;
                case "getData":
                    $this->getData();
                break;
                case "EditarCarga":
                    $this->EditarCarga();
                break;
                case "RegistrarDatos":
                    $this->RegistrarDatos();
                break;
                default:
                header('Location: ../../' . BASE_DATOS . '/index.php?window=central&cod_servic=1366&menant=1366');
                break;
            }
        }
    }

    /* ! \fn: getDataList
     *  \brief: funcion para cargar los datos de usuarios registrados
     *  \author: Ing. Alexander Correa
     *  \date: 11/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function getDataList() {
        $datos = (object) $_POST;
        $hoy = Date("Y-m-d H:i:s");
        $mHtml = new FormLib(2);
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_search",
            "header" => "TRANSPORTADORAS",
            "enctype" => "multipart/form-data"));
        $mHtml->Row("td");

        $mHtml->OpenDiv("id:tabla; class:accordion");
        $mHtml->SetBody("<h2 class='fuente'><center><B>Datos Consignados</B></center></h2>");
        $mHtml->OpenDiv("id:sec2");
        $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
        $mSql = "SELECT  a.cod_consec, a.cod_usuari, IF(c.nom_grupox IS NULL, 'Sin Grupo',c.nom_grupox), b.cod_priori,a.fec_inicia, a.fec_finalx, '1' eliminar FROM " . BASE_DATOS . ".tab_monito_encabe a INNER JOIN " . BASE_DATOS . ".tab_genera_usuari b ON a.cod_usuari = b.cod_usuari LEFT JOIN " . BASE_DATOS . ".tab_callce_grupox c ON c.cod_grupox = b.cod_grupox WHERE a.fec_finalx >= '$hoy'";
        
        $_SESSION["queryXLS"] = $mSql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }
        $list = new DinamicList(self::$cConexion, $mSql, "1", "no", 'ASC');
        $list->SetClose('no');
        $list->SetHeader("Consecutivo", "field:a.cod_consec; width:1%;");
        $list->SetHeader("usuario", "field:a.cod_usuari; width:1%;");
        $list->SetHeader("Grupo", "field:IF(c.nom_grupox IS NULL, 'Sin Grupo',c.nom_grupox); width:1%");
        $list->SetHeader("Prioridad", "field:cod_priori; width:1%");
        $list->SetHeader("Fecha y Hora de Inicio", "field:a.fec_inicia; width:1%");
        $list->SetHeader("Fecha y Hora de Finalización", "field:a.fec_finalx; width:1%");
        $list->SetOption("Opciones", "field:eliminar; width:1%; onclikDisable:EliminarConfiguracion(this)");
        $list->SetHidden("cod_consec", "0");
        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list->GetHtml();
        $mHtml->SetBody($Html);
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->CloseForm();
        echo $mHtml->MakeHtml();
    }

    /* ! \fn: registrar
     *  \brief: funcion para registrar un horario de monitoreo
     *  \author: Ing. Alexander Correa
     *  \date: 12/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return boolen
     */

    private function registrar() {
        $datos = (object) $_POST;
        $datos->usr_creaci = $_SESSION["datos_usuario"]["cod_usuari"];
        $sql = "SELECT MAX(cod_consec) cod FROM " . BASE_DATOS . ".tab_monito_encabe ";
        $consulta = new Consulta($sql, self::$cConexion, 'BR');
        $cod_consec = $consulta->ret_matrix('a');
        $cod_consec = $cod_consec[0]['cod'];
        $fecini = $datos->fecini;
        $fecsal = $datos->fecsal;
        $horini = $datos->horini;
        $horsal = $datos->horsal;
        $contro = true;
        foreach ($datos->nom_usuari as $key => $value) {
            $cod_consec++;
            $sql = "INSERT INTO " . BASE_DATOS . ".tab_monito_encabe( cod_consec, cod_usuari, fec_inicia, fec_finalx, usr_creaci, fec_creaci)VALUES($cod_consec, '$value', '$fecini[$key] $horini[$key]:00', '$fecsal[$key] $horsal[$key]:00','$datos->usr_creaci', NOW())";
            if ($key == count($datos->nom_usuari) - 1) {
                $consulta = new Consulta($sql, self::$cConexion, 'RC');
            } else {
                $consulta = new Consulta($sql, self::$cConexion, 'R');
            }
            if ($consulta != 1) {
                $contro = false;
                break;
            }
        }
        if ($contro == true) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /* ! \fn: buscarUsuario
     *  \brief: funcion para buscar un usuario en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 15/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function buscarUsuario() {
        $mSql = "SELECT cod_consec, cod_usuari 
        FROM " . BASE_DATOS . ".tab_genera_usuari WHERE ind_estado = 1 AND cod_perfil IN(1,7,8,73,70,77,669,713)";
        $mSql .= $_REQUEST[term] ? " AND (cod_usuari LIKE '" . $_REQUEST[term] . "%' OR nom_usuari LIKE '" . $_REQUEST[term] . "%' )" : "";
        $mSql .= " ORDER BY cod_usuari ASC ";
        $consulta = new Consulta($mSql, self::$cConexion);
        $mResult = $consulta->ret_matrix('a');

        if ($_REQUEST[term]) {
            $mUsuario = array();
            for ($i = 0; $i < sizeof($mResult); $i++) {
                $mTxt = utf8_decode($mResult[$i]['cod_usuari']);
                $mUsuario[] = array('value' => utf8_decode($mResult[$i]['cod_usuari']), 'label' => $mTxt, 'id' => $mResult[$i]['cod_consec']);
            }
            echo json_encode($mUsuario);
        } else
        return $mResult;
    }

    /* ! \fn: getUsuarios
     *  \brief: trae un arreglo con la lista de usuarios disponibles para asignar transportadoras en el dia actual
     *  \author: Ing. Alexander Correa
     *  \date: 17/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return arreglo con los usuarios
     */

    public function getUsuarios() {
        $datos = (object) $_POST;
        $cod_grupox = $_SESSION['datos_usuario']['cod_grupox'];
        if(!$cod_grupox){
            ?>
            <div class="col-md-12"></div>
            <div class="col-md-12" style="background-color: #285C00">
                <div id="contenido">
                    <label>&nbsp;</label>
                    <div  class="Style2DIV">  
                        <table width="100%" cellspacing="0" cellpadding="0">    
                            <tr class="Style2DIV">
                                <td class="contenido centrado">      
                                    <h5><b>El usuario <?= $_SESSION['datos_usuario']['cod_usuari'] ?> No tiene grupo asociado</b></h5>
                                </td>
                            </tr>
                        </table>           
                    </div>
                </div>
                <div style class="col-md-12">&nbsp;</div>
            </div>
            <?php
            die;
        }
        $sql = "SELECT a.cod_consec, a.cod_usuari, b.fec_inicia, b.fec_finalx, a.cod_grupox, cod_priori, c.nom_grupox, a.usr_emailx
        FROM " . BASE_DATOS . ".tab_genera_usuari a 
        INNER JOIN " . BASE_DATOS . ".tab_monito_encabe b ON b.cod_usuari = a.cod_usuari 
        INNER JOIN " . BASE_DATOS . ".tab_callce_grupox c ON a.cod_grupox = c.cod_grupox
        WHERE b.fec_inicia >=  '$datos->fec_inicio $datos->hor_inicia:00' AND b.fec_finalx <= '$datos->fec_finali  $datos->hor_finali:59' 
        AND a.cod_grupox = $cod_grupox 
        GROUP BY cod_consec";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }

    /* ! \fn: comprobar
     *  \brief: funcion para comprobar que una configuracion de usuario no se repita o se crue el horario
     *  \author: Ing. Alexander Correa
     *  \date: 15/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function comprobar() {
        $datos = (object) $_POST;
        $sql = "SELECT cod_consec FROM " . BASE_DATOS . ".tab_monito_encabe WHERE cod_usuari = '$datos->usuario' AND (fec_inicia BETWEEN '$datos->fec_inicio' AND '$datos->fec_finali' OR fec_finalx BETWEEN '$datos->fec_inicio' AND '$datos->fec_finali')";
        $consulta = new Consulta($sql, self::$cConexion);
        $mResult = $consulta->ret_matrix('a');

        if ($mResult) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /* ! \fn: EliminarConfiguracion
     *  \brief: elimina una configuracion de usuario registrada en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 16/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */

    private function EliminarConfiguracion() {
        $datos = (object) $_POST;
        $sql = "DELETE FROM " . BASE_DATOS . ".tab_monito_encabe WHERE cod_consec = $datos->cod_consec";
        $consulta = new Consulta($sql, self::$cConexion);
        if ($consulta) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /* ! \fn: getTransportadoras()
     *  \brief: trae las transportadoras a configurar para una fecha ingresada
     *  \author: Ing. Alexander Correa
     *    \date: 23/02/2016
     *    \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return arreglo con las transportadoras
     */

    private function getTransportadoras() {
        $datos = (object) $_POST;
        $cod_grupox = $_SESSION['datos_usuario']['cod_grupox'];
        if(!$cod_grupox){
            ?>
            <div class="col-md-12"></div>
            <div class="col-md-12" style="background-color: #285C00">
                <div id="contenido">
                    <label>&nbsp;</label>
                    <div  class="Style2DIV">  
                        <table width="100%" cellspacing="0" cellpadding="0">    
                            <tr class="Style2DIV">
                                <td class="contenido centrado">      
                                    <h5><b>El usuario <?= $_SESSION['datos_usuario']['cod_usuari'] ?> No tiene grupo asociado</b></h5>
                                </td>
                            </tr>
                        </table>           
                    </div>
                </div>
                <div style class="col-md-12">&nbsp;</div>
            </div>
            <?php
            die;
        }
        $sql = "SELECT b.cod_tercer, b.abr_tercer, count(DISTINCT(c.num_despac)) despac, 
                       a.cod_grupox, a.cod_priori, e.nom_grupox, a.ind_segcar, a.ind_segdes, a.ind_segtra
        FROM " . BASE_DATOS . ".tab_transp_tipser a 
        INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer b ON b.cod_tercer = a.cod_transp 
        LEFT JOIN " . BASE_DATOS . ".tab_despac_vehige c ON c.cod_transp = b.cod_tercer 
        LEFT JOIN " . BASE_DATOS . ".tab_despac_despac d ON d.num_despac = c.num_despac 
        INNER JOIN " . BASE_DATOS . ".tab_callce_grupox e ON e.cod_grupox = a.cod_grupox 
        WHERE a.fec_iniser <= '$datos->fec_inicio'  AND fec_finser >= '$datos->fec_finali'
        AND b.cod_estado = 1  
        AND d.fec_salida IS NOT NULL 
        AND d.fec_salida <= NOW() 
        AND (d.fec_llegad IS NULL OR d.fec_llegad = '0000-00-00 00:00:00')
        AND d.ind_planru = 'S' 
        AND d.ind_anulad = 'R'
        AND c.ind_activo = 'S' 
        AND a.cod_grupox = $cod_grupox 
        GROUP BY b.cod_tercer";
        $consulta = new Consulta($sql, self::$cConexion);

        return $consulta->ret_matrix('a');
    }

    /* ! \fn: getData
     *  \brief: trae la lista de transportadoras con la sugerencia de usuarios para ser asignados
     *  \author: Ing. Alexander Correa
     *  \date: 17/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return arreglo con los datos
     */

    private function getData() {
        $usuarios = $this->getUsuarios();
        $transportadoras = $this->getTransportadoras();
        
        if (!$usuarios) {
            ?>
            <div class="col-md-12"></div>
            <div class="col-md-12" style="background-color: #285C00">
                <div id="contenido">
                    <label>&nbsp;</label>
                    <div  class="Style2DIV">  
                        <table width="100%" cellspacing="0" cellpadding="0">    
                            <tr class="Style2DIV">
                                <td class="contenido centrado">      
                                    <h5><b>No hay usuarios parametrizados para las fechas ingresadas</b></h5>
                                </td>
                            </tr>
                        </table>           
                    </div>
                </div>
                <div style class="col-md-12">&nbsp;</div>
            </div>
            <?php
        } else if (!$transportadoras) {
            ?>
            <div class="col-md-12"></div>
            <div class="col-md-12" style="background-color: #285C00">
                <div id="contenido">
                    <label>&nbsp;</label>
                    <div  class="Style2DIV">  
                        <table width="100%" cellspacing="0" cellpadding="0">    
                            <tr class="Style2DIV">
                                <td class="contenido centrado">      
                                    <h5><b>No hay transportadoras parametrizados para las fechas ingresadas</b></h5>
                                </td>
                            </tr>
                        </table>           
                    </div>
                </div>
                <div style class="col-md-12">&nbsp;</div>
            </div>

            <?php
        } else {
            $datos = $this->ordenarDatos($usuarios, $transportadoras);
            ?>
            <div class="col-md-12"></div>
            <div class="col-md-12 centrado CellHead">
                <b>Asignación de Usuarios por Transportadora</b>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-12 centrado CellHead">
                <div class="col-md-1">No.</div>
                <div class="col-md-2">Usuario</div>
                <div class="col-md-2">Categoría</div>
                <div class="col-md-2">No. de Vehículos en Ruta</div>
                <div class="col-md-2">Empresas Asignadas</div>
                <div class="col-md-2">Etapa</div>
                <div class="col-md-1">Editar</div>

            </div>
            <?php
            $vehicu = 0;
            foreach ($usuarios as $key => $value) {
                $despac += $datos[$value['cod_usuari']]['despachos'];
                ?>
                <div id="di<?= $key ?>" class="col-md-12 centrado contenido borde-inferior">
                    <div class="col-md-1" ><?= $key + 1 ?></div>
                    <div class="col-md-2" ><?= $value['cod_usuari'] ?>
                    <input type="hidden" id="usu<?= $key ?>" name="usuarios[]" value="<?= $value['cod_usuari'] ?>"></input>
                    <input type="hidden" id="ema<?= $key ?>" name="usr_emailx[]" value="<?= $value['usr_emailx'] ?>"></input>
                    </div>
                    <div class="col-md-2" id="categoria<?= $key ?>" ><?= $datos[$value['cod_usuari']]['categoria']?></div>
                    <div class="col-md-2" ><?= $datos[$value['cod_usuari']]['despachos'] ?><input type="hidden" name="despachos[]" id="despa<?= $key ?>" value="<?= trim($datos[$value['cod_usuari']]['des'], ",") ?>"></input></div>
                    <div class="col-md-2" id="empres<?= $key ?>">
                        <?= trim($datos[$value['cod_usuari']]['empresas'],",") ?>
                        <input type="hidden" id="ids<?= $key ?>" name="ids[]" value="<?= trim($datos[$value['cod_usuari']]['nits'], ",") ?>">
                        <input type="hidden" id="cat<?= $key ?>" name="cat[]" value="<?= trim($datos[$value['cod_usuari']]['cat'], ",") ?>">
                        <input type="hidden" id="transp<?= $key ?>" name="transp[]" value="<?= trim($datos[$value['cod_usuari']]['empresas'], ",") ?>">
                        </input>
                    </div>
                    <div class="col-md-2">
                    C <input type="checkbox" name="ind_segcar<?= $key ?>" id="ind_segcar<?= $key ?>" value="1">&nbsp;&nbsp;
                    T <input type="checkbox" name="ind_segtra<?= $key ?>" checked id="ind_segtra<?= $key ?>" value="1">&nbsp;&nbsp;
                    D <input type="checkbox" name="ind_segdes<?= $key ?>" id="ind_segdes<?= $key ?>" value="1">&nbsp;&nbsp;
                    </div>
                    <div class="col-md-1"><img src="../<?= DIR_APLICA_CENTRAL ?>/images/edit.png" onclick="edita(<?= $key ?>)" width="22px" height="22px" class="pointer" ></div>
                </div>
                <hr style="background-color:#35650F">
                <?php
            }
            ?>
            <div class="col-md-12 centrado CellHead">
                <div class="col-md-3">Total <b><?= count($transportadoras) ?></b> Empresas</div>
                <div class="col-md-3">Total Vehiculos en Ruta: <b><?= $despac ?></b></div>
                <div class="col-md-3">Promedio de despachos por Controlador: <b><?= round($despac / count($datos)) ?></b></div>
                <div class="col-md-3">Controladores: <b><?= count($usuarios) ?></div>
            </div>
            <div class="contenido centrado">
                <input class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" type="button" onclick="registrar()" value="Registrar">
            </div>
            <?php
        }
    }

    /* ! \fn: ordenarDatos
     *  \brief: funcion para calcular la asignación sugerida de transportadoras por usuario
     *  \author: Ing. Alexander Correa
     *  \date: 22/02/2016
     *  \date modified: dia/mes/año
     *  \param: $usuarios => arreglo con los usuarios
     *  \param: 4transportadoras => arreglo con las transportadoras
     *  \return arreblo con la informacion ordenada
     */

    private function ordenarDatos($usuarios, $transportadoras) {

        //sumo la cantidad de despachos por prioridad total para nivelar la carga
        $despac = array();
        foreach ($transportadoras as $key => $value) {
            if($value['cod_priori'] == 1){
                $despac[1] += $value['despac'];
            }else if($value['cod_priori'] == 2){
                $despac[2] += $value['despac'];
            }else{
                $despac[3] += $value['despac'];
            }
        }

        //la cantidad de despachos por persona no puede ser superior a esta
        $mMaxDespac = round(($despac[1]+ $despac[2] + $despac[3])/count($usuarios))+1;
       
        
        /*********************************************************************************
        * creo dos arreglos                                                              *
        * $separadas para guardar las transportadoras que ya fueron asignadas            *
        * $grandes para las transportadoras que necesitan mas de un controlador asignado *
        *********************************************************************************/
        $separadas = array();
        $grandes = array();

        #arreglo para almacenar los datos de las transportadoras asignadas a cada usuario
        $datos = array();

        foreach ($usuarios as $k => $val) {
            $val = (object) $val;
            $datos[$val->cod_usuari]['categoria'] = $val->nom_grupox."-".$val->cod_priori;
            $datos[$val->cod_usuari]['usr_emailx'] = $val->usr_emailx;

            $total = 0;
            $bandera1 = true;
            $bandera2 = true;
            $contador1 = 0;
            $contador2 = 0;
            // con este ciclo recorro las transportadoras y compruebo que el usuario cumpla los requisitos para hacerle seguimiento a la misma
            while($bandera1){
                $empresa = (object) $transportadoras[$contador1];

                if($val->cod_priori == $empresa->cod_priori ){
                    if($empresa->despac <= $mMaxDespac){
                        if(!in_array($empresa->cod_tercer, $separadas) && !in_array($empresa->cod_tercer, $grandes) &&  $empresa->despac <= $mMaxDespac ){
                            if($datos[$val->cod_usuari]['despachos'] <= $mMaxDespac ){
                                $datos[$val->cod_usuari]['nits'] .= $empresa->cod_tercer.",";
                                $datos[$val->cod_usuari]['empresas'] .= $empresa->abr_tercer.",";
                                $datos[$val->cod_usuari]['despachos'] += $empresa->despac;
                                $datos[$val->cod_usuari]['des'] .= $empresa->despac.",";
                                $datos[$val->cod_usuari]['cat'] .= $empresa->cod_priori.",";
                                $separadas[] = $empresa->cod_tercer;
                            }                            
                        }
                    }else if($empresa->despac > $mMaxDespac){
                        $personas = ($empresa->despac/$mMaxDespac);
                        if(is_float($personas)) {
                            $personas = 1 + intval($personas);
                        }
                        $despachos = round(($empresa->despac/$personas));
                        if($datos[$val->cod_usuari]['despachos'] <= $mMaxDespac){
                            if(!strpos($datos[$val->cod_usuari]['nits'], $empresa->cod_tercer)){
                                $datos[$val->cod_usuari]['nits'] .= $empresa->cod_tercer.",";
                                $datos[$val->cod_usuari]['empresas'] .= $empresa->abr_tercer.",";
                                $datos[$val->cod_usuari]['despachos'] += $despachos;
                                $datos[$val->cod_usuari]['des'] .= $despachos.",";
                                $datos[$val->cod_usuari]['cat'] .= $empresa->cod_priori.",";
                                if(!in_array($empresa->cod_tercer, $grandes)){
                                    $grandes[] = $empresa->cod_tercer;
                                }
                            }
                        }
                    }
                }
                if($contador1 == (count($transportadoras)-1)){
                    $bandera1 = false;
                }
                $contador1++;
            }
            //este ciclo es para las transportadoras sin usuario con la misma prioridad
            while($bandera2){
                $empresa = (object) $transportadoras[$contador2];
                if($empresa->despac <= $mMaxDespac){
                    if(!in_array($empresa->cod_tercer, $separadas) && !in_array($empresa->cod_tercer, $grandes) &&  $empresa->despac <= $mMaxDespac ){
                        if(($datos[$val->cod_usuari]['despachos'] + $empresa->despac) <= $mMaxDespac ){
                            $datos[$val->cod_usuari]['nits'] .= $empresa->cod_tercer.",";
                            $datos[$val->cod_usuari]['empresas'] .= $empresa->abr_tercer.",";
                            $datos[$val->cod_usuari]['despachos'] += $empresa->despac;
                            $datos[$val->cod_usuari]['des'] .= $empresa->despac.",";
                            $datos[$val->cod_usuari]['cat'] .= $empresa->cod_priori.",";
                            $separadas[] = $empresa->cod_tercer;
                        }                            
                    }
                }else if($empresa->despac > $mMaxDespac){
                    $personas = ($empresa->despac/$mMaxDespac);
                    if(is_float($personas)) {
                        $personas = 1 + intval($personas);
                    }
                    $despachos = round(($empresa->despac/$personas));
                    if( $datos[$val->cod_usuari]['despachos'] <= $mMaxDespac ){
                        $datos[$val->cod_usuari]['nits'] = str_replace($empresa->cod_tercer.",","", $datos[$val->cod_usuari]['nits']); 
                        $datos[$val->cod_usuari]['empresas'] = str_replace($empresa->abr_tercer.",","", $datos[$val->cod_usuari]['empresas']); 
                        $datos[$val->cod_usuari]['nits'] .= $empresa->cod_tercer.",";
                        $datos[$val->cod_usuari]['empresas'] .= $empresa->abr_tercer.",";
                        $datos[$val->cod_usuari]['despachos'] += $despachos;
                        $datos[$val->cod_usuari]['des'] .= $despachos.",";
                        $datos[$val->cod_usuari]['cat'] .= $empresa->cod_priori.",";
                        if(!in_array($empresa->cod_tercer, $grandes)){
                            $grandes[] = $empresa->cod_tercer;
                        }
                    }
                }
                if($contador2 == (count($transportadoras)-1)){
                    $bandera2 = false;
                }
                $contador2++;
            }
        }
        return $datos;
    }

    /* ! \fn: EditarCarga
     *  \brief: funcion para editar la carga laboral generada por el sistema de un usuario
     *  \author: Ing. Alexander Correa
     *  \date: 26/02/2016
     *  \date modified: dia/mes/año
     *  \param:      
     *  \return 
     */
    private function EditarCarga(){
        $datos = (object) $_POST;
        $empresas = explode(",", $datos->transportadoras);
        $nits = explode(",", $datos->ids);
        $despachos = explode(",", $datos->despachos);
        $trans = $this->getTransportadoras();
        $usuarios = $this->getUsuarios();
        $ind_segcar = "";
        $ind_segtra = "";
        $ind_segdes = "";

        if($datos->ind_segcar){
            $ind_segcar = 'checked';
        }
        if($datos->ind_segtra){
            $ind_segtra = 'checked';
        }
        if($datos->ind_segdes){
            $ind_segdes = 'checked';
        }
        foreach ($trans as $key => $value) { 
            $despac += $value['despac']; 
            ?>
            <input type="hidden" id="<?= $value['cod_tercer'] ?>" value="<?= $value['despac'] ?>">
       <?php }
       ?>
       <input type="hidden" id="descac" value="<?= round($despac/count($usuarios))+1 ?>">
       <input type="hidden" id="usuario" value="<?=  $datos->usuario ?>">
       <input type="hidden" id="key" value="<?=  $datos->key ?>">
       <input type="hidden" id="categoria" value="<?=  $datos->categoria ?>">
        <div class="col-md-12">
            <div class="col-md-12 text-center">Editar Carga del Usuario <?= $datos->usuario ?></div>
            <div class="col-md-12">
                <div class="col-md-12 contenido">
                <div class="col-md-12">
                    <div class="col-md-3">Transportadora:</div>
                    <div class="col-md-7">
                        <select id="transp">
                           <option value="">Seleccione una transportadora</option>
                           <?php
                           foreach ($trans as $k => $va) {
                               ?>
                               <option value="<?= $va['cod_tercer'] ?>"><?= $va['abr_tercer'] ?></option>
                               <?php
                           }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 izquierda">  
                         <input type="button" onclick="addDiv()" value="Agregar" class="small save ui-button ui-widget ui-state-default ui-corner-all">
                    </div>

                </div>
                <div class="col-md-12">&nbsp;</div>
                    <div class="Style2DIV">
                        <div  class="cotenido centrado">
                            <div class="col-md-12" id="div">
                                <?php  foreach ($empresas as $key => $value) {
                                  ?>
                                <div class="col-md-12" id="div<?= $key ?>">
                                    <div class="col-md-5"><label class="company"><?= $value ?></label><input type="hidden" class="empresa" name="empresa[]" id="empresa<?= $key ?>" value="<?= $nits[$key] ?>" ></div>
                                    <div class="col-md-5 despacho"><?= $despachos[$key] ?></div>

                                    <div class="col-md-2"><a onclick="eliminarDiv(<?= $key ?>)" class="pointer"><img src="../<?= DIR_APLICA_CENTRAL ?>/images/delete.png" width="16px" height="16px"></a></div>
                                </div>
                                  <?php 
                                } ?>
                            </div>
                            <div class="col-md-12 text-center">
                                C <input type="checkbox" name="ind_segcar" <?= $ind_segtra ?> id="ind_segcar" value="1">&nbsp;&nbsp;
                                T <input type="checkbox" name="ind_segtra" <?= $ind_segcar ?> id="ind_segtra" value="1">&nbsp;&nbsp;
                                D <input type="checkbox" name="ind_segdes" <?= $ind_segdes ?> id="ind_segdes" value="1">&nbsp;&nbsp;
                            </div>
                            <input type="button" onclick="aceptar()" value="Aceptar" class="small save ui-button ui-widget ui-state-default ui-corner-all">
                            <input type="button" onclick="closePopUp()" value="Cancelar" class="small save ui-button ui-widget ui-state-default ui-corner-all">
                            <input type="hidden" name="total" id="total" value="<?= $key ?>">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
       <?php
    }

    /* ! \fn: Registrar
     *  \brief: funcion para almacenar los datos de la carga laboral de los controladores
     *  \author: Ing. Alexander Correa
     *  \date: 01/03/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */
    private function RegistrarDatos(){
        $datos = (object) $_POST;
        $usuarios = $datos->usuarios;
        $empresas = $datos->ids;
        $despachos = $datos->despachos;
        $nom_transp = $datos->transp;
        $categorias = $datos->cat;
        $usr_creaci = $_SESSION["datos_usuario"]["cod_usuari"];
        $usr_emails = $datos->usr_emailx;
        //para los correos
        $fec_inicia = $datos->fec_inicio." ".$datos->hor_inicio;
        $fec_finali = $datos->fec_finali." ".$datos->hor_finali;
        //para los correos

        $contro = true;
        foreach ($usuarios as $key => $value) {
            
            //para los correos
            $controlador = $value;
            $contenido = "";
            $etapa = "";
            $ind_segtra = null;
            $ind_segcar = null;
            $ind_segdes = null;
            //para los correos
            $segCar = "ind_segcar$key";
            $segTra = "ind_segtra$key";
            $segDes = "ind_segdes$key";
            
            $sql = "SELECT MAX(cod_consec) consec FROM ".BASE_DATOS.".tab_monito_encabe WHERE cod_usuari ='$value'";
            $consulta = new Consulta($sql, self::$cConexion, 'BR');
            $max = $consulta->ret_matrix('a');
            $max = $max[0]['consec'];

            $terceros = explode(',', $empresas[$key]);
            $despac = explode(",", $despachos[$key]);
            $transport = explode(',', $nom_transp[$key]);
            $catego = explode(',', $categorias[$key]);
            $ind_segcar = $datos->$segCar;
            $ind_segtra = $datos->$segTra;
            $ind_segdes = $datos->$segDes;
            $usr_emailx = $usr_emails[$key];
            
            if(!$ind_segcar){
                $ind_segcar = 0;
            }else{
                $etapa .= "Cargue, ";
            }
            if(!$ind_segtra){
                $ind_segtra = 0;
            }else{
                $etapa .= "Transito, ";
            }
            if(!$ind_segdes){
                $ind_segdes = 0;
            }else{
                $etapa .= "Descargue";
            }
            
            $etapa = trim($etapa, ',');
            $sql = "UPDATE ".BASE_DATOS.".tab_monito_detall SET ind_estado = 0, usr_modifi = '$usr_creaci', fec_modifi = NOW() WHERE cod_consec = $max";
            $consulta = new Consulta($sql, self::$cConexion, 'R');
            $total = 0;
            foreach ($terceros as $k => $val) {
                $cantidad = $despac[$k];
                $total += $cantidad;
                $class = '';
                if($k%2==0){
                    $clase = 'style="background-color: #EBF8E2 !important;color: #000000 !important;"';
                }
                $contenido .="<tr>";
                    $contenido.="<td style='<?= $clase ?>'>".$transport[$k]."</td>";
                    $contenido.="<td style='<?= $clase ?>'>".$cantidad."</td>";
                    $contenido.="<td style='<?= $clase ?>'>".$catego[$k]."</td>";
                    $contenido.="<td style='<?= $clase ?>'>".$etapa."</td>";
                $contenido .="</tr>";

                $sql = "INSERT INTO ".BASE_DATOS.".tab_monito_detall
                                        (cod_consec, cod_tercer, can_despac,ind_segcar, ind_segdes, ind_segtra, usr_creaci,fec_creaci )
                                        VALUES
                                        ($max, '$val', $cantidad, $ind_segcar, $ind_segdes, $ind_segtra, '$usr_creaci', NOW())";
                if($key == (count($usuarios)-1) && $k == (count($terceros)-1)){
                    $consulta = new Consulta($sql, self::$cConexion, 'RC');
                }else{
                   $consulta = new Consulta($sql, self::$cConexion, 'R'); 
                }
                if ($consulta != 1) {
                    $contro = false;
                    break;
                }
            }
            if($contro == true){
                //envia el corrreo con la carga del controlador
                $this->enviarCorreos($controlador, $usr_emailx, $fec_inicia, $fec_finali, $contenido, $total);
            }
        }
        if($contro == true){
            echo 1;
        }else{
            echo 0;
        }
    }

    /* ! \fn: enviarCorreos
     *  \brief: funcion para enviar los correos a los controaldores con su carga laboral
     *  \author: Ing. Alexander Correa
     *  \date: 14/03/2016
     *  \date modified: dia/mes/año
     *  \param: $controlador = String -> Controlador al que se asigna la carta
     *  \param: $usr_emailx = String -> email del controlador para el envio de la notificacióm
     *  \param: $fec_inicio = String -> fecha de inicio de labores del controlador
     *  \param: $fec_finali = String -> fecha de finalizacion de labores del controlador
     *  \param: $contenido = String -> contenido del correo con los datos de empresas asignadas
     *  \param: $total = int -> total de despachos asignados
     *  \return return
     */
    private function enviarCorreos($controlador,$usr_emailx, $fec_inicio, $fec_finali, $contenido, $total){

        $mCabece  = 'MIME-Version: 1.0' . "\r\n";
        $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $mCabece .= 'From: Centro Logistico FARO <no-reply@eltransporte.org>' . "\r\n";       

        $tmpl_file = '../../' . DIR_APLICA_CENTRAL . '/planti/pla_notifi_contro.html';        
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"" . $thefile . "\";";
        eval($thefile);
        $mHtmlxx = $r_file;
        return mail($usr_emailx, 'Asignación de Carga Laboral', '<div name="10_faro_06">'.$mHtmlxx.'</div>', $mCabece. "\r\n");
        
    }

    
}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new ajax_horari_monito();
}
?>

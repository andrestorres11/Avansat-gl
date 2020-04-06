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
/*ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);*/

class ajax_horari_monito {

    private static $cConexion,
    $cCodAplica,
    $cUsuario,
    $cTotalDespac;

    private static $cTipDespac = array(1 => "ind_desurb", 2 => "ind_desnac", 3 => "ind_desimp", 4 => "ind_desexp", 5 => "ind_desxd1", 6 => "ind_desxd2");

    function __construct($co = null, $us = null, $ca = null) {
        if ($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on') {
            @include_once( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            @include_once( "../lib/general/functions.inc" );

            self::$cConexion = $AjaxConnection;
            self::$cUsuario = $_SESSION['datos_usuario'];
            self::$cCodAplica = $_SESSION['codigo'];
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
                case 'listUsuarios':
                    $this->listUsuarios();
                    break;
                case 'pintarTabDistriManual':
                    $this->pintarTabDistriManual();
                    break;
                case 'pintarTabAignacionVacia':
                    $this->pintarTabAignacionVacia();
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
        $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "TRANSPORTADORAS", "enctype" => "multipart/form-data"));
        $mHtml->Row("td");

        $mHtml->OpenDiv("id:tabla; class:accordion");
        $mHtml->SetBody("<h1 style='padding:6px'><B>Datos Consignados</B></h1>");
        $mHtml->OpenDiv("id:sec2");
        $mHtml->OpenDiv("id:form3; class:contentAccordionForm");

        $mSql = "SELECT a.cod_consec, a.cod_usuari, 
                        IF(c.nom_grupox IS NULL, 'Sin Grupo',c.nom_grupox), 
                        b.cod_priori, a.fec_inicia, a.fec_finalx, '1' eliminar 
                   FROM " . BASE_DATOS . ".tab_monito_encabe a 
             INNER JOIN " . BASE_DATOS . ".tab_genera_usuari b ON a.cod_usuari = b.cod_usuari 
              LEFT JOIN " . BASE_DATOS . ".tab_callce_grupox c ON c.cod_grupox = b.cod_grupox 
                  WHERE a.fec_finalx >= '$hoy'";
        
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
        $mSql = "SELECT cod_consec, cod_usuari, CONCAT(cod_usuari, ' (', nom_usuari, ')') nom_usuari 
                   FROM " . BASE_DATOS . ".tab_genera_usuari WHERE ind_estado = 1 AND cod_perfil IN(1,7,8,73,70,77,669,713)";
        $mSql .= $_REQUEST[term] ? " AND (cod_usuari LIKE '" . $_REQUEST[term] . "%' OR nom_usuari LIKE '" . $_REQUEST[term] . "%' )" : "";
        $mSql .= " ORDER BY cod_usuari ASC ";
        $consulta = new Consulta($mSql, self::$cConexion);
        $mResult = $consulta->ret_matrix('a');

        if ($_REQUEST[term]) {
            $mUsuario = array();
            for ($i = 0; $i < sizeof($mResult); $i++) {
                $mUsuario[] = array('value' => utf8_decode($mResult[$i]['cod_usuari']), 'label' => utf8_decode($mResult[$i]['nom_usuari']), 'id' => $mResult[$i]['cod_consec']);
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
    public function getUsuarios( $cod_consec = null ) {
        $datos = (object) $_POST;
        $cod_grupox = $_SESSION['datos_usuario']['cod_grupox'];

        if(!$cod_grupox){
            $msj = "El usuario ".$_SESSION['datos_usuario']['cod_usuari']." No tiene grupo asociado";
            $this->htmlMensajeSin($msj);
            die;
        }

        $fecInicia = "$datos->fec_inicio $datos->hor_inicio";
        $fecFinali = "$datos->fec_finali $datos->hor_finali";

        $sql = "SELECT a.cod_consec, a.cod_usuari, b.fec_inicia, 
                       b.fec_finalx, a.cod_grupox, cod_priori, 
                       c.nom_grupox, a.usr_emailx, a.nom_usuari
                  FROM " . BASE_DATOS . ".tab_genera_usuari a
            INNER JOIN " . BASE_DATOS . ".tab_monito_encabe b ON b.cod_usuari = a.cod_usuari
            INNER JOIN " . BASE_DATOS . ".tab_callce_grupox c ON a.cod_grupox = c.cod_grupox
                 WHERE a.cod_grupox = $cod_grupox 
                   AND (
                            ('$fecInicia' BETWEEN DATE_FORMAT(b.fec_inicia, '%Y-%m-%d %H:%i') AND DATE_FORMAT(b.fec_finalx, '%Y-%m-%d %H:%i')) OR
                            ('$fecFinali' BETWEEN DATE_FORMAT(b.fec_inicia, '%Y-%m-%d %H:%i') AND DATE_FORMAT(b.fec_finalx, '%Y-%m-%d %H:%i')) OR 
                            ('$fecInicia' <= DATE_FORMAT(b.fec_inicia, '%Y-%m-%d %H:%i') AND '$fecFinali' >= DATE_FORMAT(b.fec_finalx, '%Y-%m-%d %H:%i'))
                       )
                   ".( $cod_consec == null ? "" : " AND a.cod_consec IN ($cod_consec) " )."
              GROUP BY a.cod_consec
              ORDER BY cod_priori";
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
        $sql = "SELECT cod_consec 
                  FROM " . BASE_DATOS . ".tab_monito_encabe 
                 WHERE cod_usuari = '$datos->usuario' 
                   AND (fec_inicia BETWEEN '$datos->fec_inicio' AND '$datos->fec_finali' OR fec_finalx BETWEEN '$datos->fec_inicio' AND '$datos->fec_finali')";
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
     *  \date: 23/02/2016
     *  \date modified: dia/mes/año
     *  \param: listTransp  string  NIT's de las transportadoras separados por ,
     *  \param: 
     *  \return arreglo con las transportadoras
     */
    private function getTransportadoras( $listTransp = null ) {
        $datos = (object) $_POST;
        $cod_grupox = $_SESSION['datos_usuario']['cod_grupox'];

        if(!$cod_grupox){
        	$this->htmlMensajeSin("El usuario ".$_SESSION['datos_usuario']['cod_usuari']." No tiene grupo asociado");
            die;
        }

        $sql = "SELECT * 
                    FROM
                    (
                        (
                            SELECT x.*
                                  FROM (
                                        SELECT b.cod_tercer, b.abr_tercer, count(DISTINCT(c.num_despac)) despac, 
                                               a.cod_grupox, a.cod_priori, e.nom_grupox, a.ind_segprc, a.ind_segcar, a.ind_segdes, a.ind_segtra
                                            FROM (
                                                    SELECT
                                                        a.*
                                                    FROM 
                                                    ".BASE_DATOS.".tab_transp_tipser a INNER JOIN 
                                                    (
                                                        SELECT MAX(num_consec) AS num_consec, b.cod_transp 
                                                        FROM ".BASE_DATOS.".tab_transp_tipser b GROUP BY b.cod_transp 
                                                    ) b ON a.num_consec = b.num_consec AND a.cod_transp = b.cod_transp

                                                ) a 
                                            INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer b ON b.cod_tercer = a.cod_transp 
                                             LEFT JOIN " . BASE_DATOS . ".tab_despac_vehige c ON c.cod_transp = b.cod_tercer AND c.num_despac NOT IN (  SELECT e.num_despac FROM satt_faro.tab_despac_noveda e WHERE e.cod_contro = 9999  )
                                             LEFT JOIN " . BASE_DATOS . ".tab_despac_despac d ON d.num_despac = c.num_despac 
                                            INNER JOIN " . BASE_DATOS . ".tab_callce_grupox e ON e.cod_grupox = a.cod_grupox
                                                 WHERE a.fec_iniser <= '$datos->fec_inicio'  AND fec_finser >= '$datos->fec_finali'
                                                   AND a.ind_estado = 1  
                                                   AND b.cod_estado = 1  
                                                   AND d.fec_salida IS NOT NULL 
                                                   AND d.fec_salida <= NOW() 
                                                   AND (d.fec_llegad IS NULL OR d.fec_llegad = '0000-00-00 00:00:00')
                                                   AND d.ind_planru = 'S' 
                                                   AND d.ind_anulad = 'R'
                                                   AND c.ind_activo = 'S' 
                                                   AND a.cod_grupox = $cod_grupox
                                                       ".( $listTransp == null ? "" : " AND a.cod_transp IN ($listTransp) " )."
                                              GROUP BY b.cod_tercer
                                       ) x
                              ORDER BY x.cod_priori ASC, x.despac DESC
                        )
                        UNION ALL
                        (
                            SELECT x.*
                                  FROM (
                                        SELECT b.cod_tercer, b.abr_tercer, '0' AS despac, 
                                               a.cod_grupox, a.cod_priori, e.nom_grupox, a.ind_segprc, a.ind_segcar, a.ind_segdes, a.ind_segtra
                                            FROM (
                                                    SELECT
                                                        a.*
                                                    FROM 
                                                    ".BASE_DATOS.".tab_transp_tipser a INNER JOIN 
                                                    (
                                                        SELECT MAX(num_consec) AS num_consec, b.cod_transp 
                                                        FROM ".BASE_DATOS.".tab_transp_tipser b GROUP BY b.cod_transp 
                                                    ) b ON a.num_consec = b.num_consec AND a.cod_transp = b.cod_transp

                                                ) a 
                                            INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer b ON b.cod_tercer = a.cod_transp 
                                             LEFT JOIN " . BASE_DATOS . ".tab_despac_vehige c ON c.cod_transp = b.cod_tercer AND c.num_despac NOT IN (  SELECT e.num_despac FROM satt_faro.tab_despac_noveda e WHERE e.cod_contro = 9999  )
                                             LEFT JOIN " . BASE_DATOS . ".tab_despac_despac d ON d.num_despac = c.num_despac 
                                            INNER JOIN " . BASE_DATOS . ".tab_callce_grupox e ON e.cod_grupox = a.cod_grupox
                                                 WHERE a.fec_iniser <= '$datos->fec_inicio'  AND fec_finser >= '$datos->fec_finali'
                                                   AND a.ind_estado = 1  
                                                   AND b.cod_estado = 1  
                                                   /*AND d.fec_salida IS NOT NULL 
                                                   AND d.fec_salida <= NOW() 
                                                   AND d.ind_planru = 'S' 
                                                   AND d.ind_anulad = 'R'
                                                   AND c.ind_activo = 'S'*/ 
                                                   AND a.cod_grupox = $cod_grupox
                                                       ".( $listTransp == null ? "" : " AND a.cod_transp IN ($listTransp) " )."
                                              GROUP BY b.cod_tercer
                                       ) x
                              ORDER BY x.cod_priori ASC, x.despac DESC
                        )
                    ) xx GROUP BY xx.cod_tercer
                ";

        $consulta = new Consulta($sql, self::$cConexion);

        return $consulta->ret_matrix('a');
    }

    /*! \fn: transpConSeguimAdicio
     *  \brief: Verifica si la transportadora tiene activo un seguimiento adicional en el rango de fechas
     *  \author: Ing. Fabian Salinas
     *  \date: 08/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codTercer  integer
     *  \param: fecInicia  datetime
     *  \param: fecFinali  datetime
     *  \return: boolean
     */
    private function transpConSeguimAdicio($codTercer, $fecInicia, $fecFinali) {
        $sql = "SELECT a.cod_consec
                  FROM ".BASE_DATOS.".tab_seguim_adicio a 
                 WHERE a.cod_transp = '$codTercer'
                   AND a.ind_estado = 1
                   AND (    ('$fecInicia' BETWEEN a.fec_inicia AND a.fec_finali) OR 
                            ('$fecFinali' BETWEEN a.fec_inicia AND a.fec_finali) OR 
                            (a.fec_inicia BETWEEN '$fecInicia' AND '$fecFinali') OR 
                            (a.fec_finali BETWEEN '$fecInicia' AND '$fecFinali')
                       )";
        $consulta = new Consulta($sql, self::$cConexion);
        $data = $consulta->ret_matrix('a');

        if(sizeof($data) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*! \fn: getConfigHorlab
     *  \brief: Trae la configuracion del horario de seguimiento de la transportadora
     *  \author: Ing. Fabian Salinas
     *  \date: 09/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codTercer  integer
     *  \param: indHorario  integer  Indicador de tipo de horario a consultar
     *  \return: array
     */
    private function getConfigHorlab($codTercer, $indHorario, $dia) {
        $sql = "SELECT a.hor_ingres, a.hor_salida 
                  FROM ".BASE_DATOS.".tab_config_horlab a 
                 WHERE a.cod_tercer = '$codTercer'
                   AND a.ind_config = $indHorario
                   AND a.com_diasxx LIKE '%$dia%' ";
        $consulta = new Consulta($sql, self::$cConexion);
        $result = $consulta->ret_matrix('a');
        return $result[0];
    }

    /*! \fn: validarDiaFestivoByTransp
     *  \brief: Valida si la fecha esta configurada como festiva, para la transportadaora actual
     *  \author: Ing. Fabian Salinas
     *  \date: 06/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codTercer   integer  Codigo de la transportadora
     *  \param: indHorario  integer
     *  \param: fecha       date
     *  \return: boolean
     */
    private function validarDiaFestivoByTransp($codTercer, $indHorario, $fecha) {
        $sql = "SELECT 1
                  FROM ".BASE_DATOS.".tab_config_festiv 
                 WHERE cod_tercer = 1
                   AND ind_config = $indHorario
                   AND fec_festiv = '$fecha' ";
        $consulta = new Consulta($sql, self::$cConexion);
        $result = $consulta->ret_matrix('a');

        if( sizeof($result) > 0 ){
            return true;
        } else {
            return false;
        }
    }

    /*! \fn: traerHoraioLaboral
     *  \brief: Trae el horario laboral segun el dia
     *  \author: Ing. Fabian Salinas
     *  \date: 06/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codTercer   integer  Codigo de la transportadora
     *  \param: indHorario  integer  Indicador del horario a validar
     *  \param: fecInicia   date
     *  \param: fecFinali   date
     *  \param: festivo     boolean  Indicador de validación por festivos o normal
     *  \return: array|boolean
     */
    private function traerHoraioLaboral($codTercer, $indHorario, $fecInicia, $fecFinali, $festivo) {
        $dias = array('', 'L', 'M', 'X', 'J', 'V', 'S', 'D', 'F');

        $dia1 = $dias[date('N', strtotime($fecInicia))];
        $dia2 = $dias[date('N', strtotime($fecFinali))];

        if( !$festivo ) {
            $data[0] = $this->getConfigHorlab($codTercer, $indHorario, $dia1);
            $data[1] = $this->getConfigHorlab($codTercer, $indHorario, $dia2);
        } else {
            $fechaFestiva = false;

            if( $this->validarDiaFestivoByTransp($codTercer, $indHorario, $fecInicia) ) {
                $data[0] = $this->getConfigHorlab($codTercer, $indHorario, 'F');
                $fechaFestiva = true;
            } else {
                $data[0] = $this->getConfigHorlab($codTercer, $indHorario, $dia1);
            }

            if( $this->validarDiaFestivoByTransp($codTercer, $indHorario, $fecFinali) ) {
                $data[1] = $this->getConfigHorlab($codTercer, $indHorario, 'F');
                $fechaFestiva = true;
            } else {
                $data[1] = $this->getConfigHorlab($codTercer, $indHorario, $dia2);
            }

            if( !$fechaFestiva ) {
                $data = false;
            }
        }

        return $data;
    }

    /*! \fn: transpHorario
     *  \brief: Valida si el horario en el que se distribuira la carga esta entre el horario de seguimiento de la transportadora
     *  \author: Ing. Fabian Salinas
     *  \date: 09/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codTercer   integer  Codigo de la transportadora
     *  \param: indHorario  integer  Indicador del horario a validar
     *  \param: fecInicia   date
     *  \param: horInicia   time
     *  \param: fecFinali   date
     *  \param: horFinali   time
     *  \param: festivo     boolean  Indicador de validación por festivos o normal
     *  \return: boolean
     */
    private function transpHorario($codTercer, $indHorario, $fecInicia, $horInicia, $fecFinali, $horFinali, $festivo) {
        $data = $this->traerHoraioLaboral($codTercer, $indHorario, $fecInicia, $fecFinali, $festivo);
            
        if( !$data ) {
            return false;
        }

        if( sizeof($data[0]) > 0 || sizeof($data[1]) > 0 ) {
            $data[0]['hor_ingres'] = strtotime($data[0]['hor_ingres']);
            $horInicia = strtotime($horInicia.":00");
            $data[0]['hor_salida'] = strtotime($data[0]['hor_salida']);
            $horFinali = strtotime($horFinali.":00");
            $data[1]['hor_salida'] = strtotime($data[1]['hor_salida']);
            $data[1]['hor_ingres'] = strtotime($data[1]['hor_ingres']);
            
            if($fecInicia == $fecFinali) {
                if( $data[0]['hor_ingres'] <= $horInicia && $horInicia <= $data[0]['hor_salida'] ) {
                    return true;
                } elseif( $data[0]['hor_ingres'] >= $horInicia && $data[0]['hor_salida'] <= $horFinali ) {
                    return true;
                } elseif( $data[0]['hor_salida'] >= $horFinali && $horFinali >= $data[0]['hor_ingres'] ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if( $data[0]['hor_ingres'] <= $horInicia && $horInicia <= $data[0]['hor_salida'] ) {
                    return true;
                } elseif( $data[0]['hor_ingres'] >= $horInicia ) {
                    return true;
                } elseif( $data[1]['hor_salida'] >= $horFinali ) {
                    return true;
                } elseif( $data[1]['hor_ingres'] <= $horFinali && $horFinali <= $data[1]['hor_salida'] ) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /*! \fn: getTranspByHorario
     *  \brief: Trae las transportadoras que tiene horario de seguimiento
     *  \author: Ing. Fabian Salinas
     *  \date: 08/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: array
     */
    private function getTranspByHorario($fecInicia, $horInicia, $fecFinali, $horFinali) {
        $transp = $this->getTransportadoras();

        $result = array();
        foreach ($transp as $key => $row) {
            if( $this->transpConSeguimAdicio($row['cod_tercer'], $fecInicia." ".$horInicia, $fecFinali." ".$horFinali) ) 
            { #Valida si la transportadora tiene contratado seguimiento adicional
                $result[] = $row;
            } elseif( $this->transpHorario($row['cod_tercer'], 3, $fecInicia, $horInicia, $fecFinali, $horFinali, false) ) 
            { #Valida si la transportadora tiene contratado seguimineto Normal 1
                $result[] = $row;
            } elseif( $this->transpHorario($row['cod_tercer'], 4, $fecInicia, $horInicia, $fecFinali, $horFinali, false) ) 
            { #Valida si la transportadora tiene contratado seguimineto Normal 2
                $result[] = $row;
            } elseif( $this->transpHorario($row['cod_tercer'], 3, $fecInicia, $horInicia, $fecFinali, $horFinali, true) ) 
            { #Valida si la transportadora tiene contratado seguimineto Festivo 1
                $result[] = $row;
            } elseif( $this->transpHorario($row['cod_tercer'], 4, $fecInicia, $horInicia, $fecFinali, $horFinali, true) ) 
            { #Valida si la transportadora tiene contratado seguimineto Festivo 2
                $result[] = $row;
            }
        }

        return $result;
    }

    /*! \fn: pintarCabeceraTabAsignacio
     *  \brief: Pinta la cabecera de la tabla de asignacion de carga laboral
     *  \author: Ing. Fabian Salinas
     *  \date: 08/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function pintarCabeceraTabAsignacio() {
        ?>
        <div class="col-md-12 centrado CellHead">
            <b>Asignaci&oacute;n de Usuarios por Transportadora</b>
        </div>
        <div class="col-md-12"></div>
        <div class="col-md-12 centrado CellHead">
            <div class="col-md-1">No.</div>
            <div class="col-md-3">Usuario</div>
            <div class="col-md-2">Categoria</div>
            <div class="col-md-2">No. de Vehiculos en Ruta</div>
            <div class="col-md-2">Empresas Asignadas</div>
            <div class="col-md-2">Etapa</div>        </div>
        <?php
    }

    /*! \fn: pintarPieTabAsignacio
     *  \brief: Pinta el pie de la tabla de asignacion de carga laboral
     *  \author: Ing. Fabian Salinas
     *  \date: 08/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: contTransp  integer  Cantidad de Transportadoras
     *  \return: 
     */
    private function pintarPieTabAsignacio( $function, $cantTransp = null, $cantDespac = null, $cantCarga = null, $cantContro = null){
        ?>
        <div class="col-md-12 centrado CellHead">
            <div class="col-md-3">Total <b><?= $cantTransp ?></b> Empresas</div>
            <div class="col-md-3">Total Vehiculos en Ruta: <b><?= $cantDespac ?></b></div>
            <div class="col-md-3">Promedio de despachos por Controlador: <b><?= $cantCarga ?></b></div>
            <div class="col-md-3">Controladores: <b><?= $cantContro ?></div>
        </div>
        <div class="contenido centrado">
            <input class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" type="button" onclick="<?= $function ?>()" value="Registrar">
        </div>
        <?php
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
        $usuarios = $this->getUsuarios( implode(',', $_REQUEST['users']) );
        $transportadoras = $this->getTransportadoras( implode(",", $_REQUEST['transp']) );
        
        if (!$usuarios) {
            $this->htmlMensajeSin("No hay usuarios parametrizados para las fechas ingresadas");
        } else if (!$transportadoras) {
            $this->htmlMensajeSin("No hay transportadoras parametrizados para las fechas ingresadas");
        } else {
            $datos = $this->ordenarDatos($usuarios, $transportadoras);
            ?>

            <div class="hidden">
                <select id="transpID">
                <?php
                    foreach ($transportadoras as $row) {
                        echo '<option value="'.$row['cod_tercer'].'" totDespac="'.$row['despac'].'" cod_grupox="'.$row['cod_grupox'].'" libreDespac="0">'.$row['abr_tercer'].'</option>';
                    }
                ?>
                </select>
            </div>

            <?php
            $this->pintarCabeceraTabAsignacio();

            $vehicu = 0;
            foreach ($usuarios as $key => $value) {
                $despac += $datos[$value['cod_usuari']]['despachos'];
                ?>
                <div id="di<?= $key ?>" class="col-md-12 centrado contenido borde-inferior">
                    <div class="col-md-1" ><?= $key + 1 ?></div>
                    <div class="col-md-2" ><?= $value['cod_usuari'] ?>
                    <input type="hidden" id="usu<?= $key ?>" name="usuarios[]" value="<?= $value['cod_usuari'] ?>">
                    <input type="hidden" id="ema<?= $key ?>" name="usr_emailx[]" value="<?= $value['usr_emailx'] ?>">
                    </div>
                    <div class="col-md-2" id="categoria<?= $key ?>" ><?= $datos[$value['cod_usuari']]['categoria']?></div>
                    <div class="col-md-2" >
                        <label name="cantDespac" id="cantDespac_<?= $key ?>"><?= $datos[$value['cod_usuari']]['despachos'] ?></label>
                        <input type="hidden" name="despachos[]" id="despa<?= $key ?>" value="<?= trim($datos[$value['cod_usuari']]['des'], ",") ?>">
                    </div>
                    <div class="col-md-2 text-left" id="empres<?= $key ?>">

                        <?php
                        foreach ($datos[$value['cod_usuari']]['datos'] as $row) {
                            echo '- '.$row['empresas'].'<br>';
                        }
                        ?>
                        <select class="hidden" name="transpControl" id="transpControl_<?= $key ?>">
                        <?php
                            foreach ($datos[$value['cod_usuari']]['datos'] as $row) {
                                echo '<option value="'.$row['nits'].'" cantDespac="'.$row['des'].'" cod_grupox="'.$row['cat'].'">'.$row['empresas'].'</option>';
                            }
                        ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        PC <input type="checkbox" name="ind_segprc<?= $key ?>" id="ind_segprc<?= $key ?>" value="1">&nbsp;&nbsp;
                        C <input type="checkbox" name="ind_segcar<?= $key ?>" id="ind_segcar<?= $key ?>" value="1">&nbsp;&nbsp;
                        T <input type="checkbox" name="ind_segtra<?= $key ?>" checked id="ind_segtra<?= $key ?>" value="1">&nbsp;&nbsp;
                        D <input type="checkbox" name="ind_segdes<?= $key ?>" id="ind_segdes<?= $key ?>" value="1">&nbsp;&nbsp;
                    </div>
                    <div class="col-md-1">
                        <img src="../<?= DIR_APLICA_CENTRAL ?>/images/edit.png" onclick="edita(<?= $key ?>)" width="22px" height="22px" class="pointer" >
                    </div>
                </div>
                <hr style="background-color:#35650F">
                <?php
            }

            $this->pintarPieTabAsignacio('registrar', count($transportadoras), $despac, round($despac / count($datos)), count($usuarios));
        }
    }

    /*! \fn: htmlMensajeSin
     *  \brief: Crea el html para los mensajes sin distribucion de carga laboral
     *  \author: Ing. Fabian Salinas
     *  \date: 08/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: $msj string
     *  \return: 
     */
    private function htmlMensajeSin($msj) {
    	?>
        <table width="100%" cellspacing="0" cellpadding="0">    
            <tr class="Style2DIV">
                <td class="contenido centrado">      
                    <h5><?= $msj ?></h5>
                </td>
            </tr>
        </table>
        <?php
    }

    /*! \fn: sumDespacByPrioriTransp
     *  \brief: Suma la cantida de despachos por la prioridad de la transportadora
     *  \author: Ing. Fabian Salinas
     *  \date: dd/mm/2016
     *  \date modified: dd/mm/aaaa
     *  \param: nameVar
     *  \return: return
     */
    private function sumDespacByPrioriTransp($transp) {
        $result = array();
        foreach ($transp as $row) {
            switch ($row['cod_priori']) {
                case 1: $result[1] += $row['despac']; break;
                case 2: $result[2] += $row['despac']; break;
                default: $result[3] += $row['despac']; break;
            }
        }

        return $result;
    }

    /* ! \fn: ordenarDatos
     *  \brief: funcion para calcular la asignación sugerida de transportadoras por usuario
     *  \author: Ing. Alexander Correa
     *  \date: 22/02/2016
     *  \modified by: Ing. Fabian Salinas
     *  \date modified: 10/08/2016
     *  \param: $usuarios => arreglo con los usuarios
     *  \param: $transportadoras => arreglo con las transportadoras
     *  \return arreglo con la informacion ordenada
     */
    private function ordenarDatos($users, $transp) {
        $x=0;
        $despac = $this->sumDespacByPrioriTransp($transp); 
        $mMaxDespac = round(($despac[1]+ $despac[2] + $despac[3])/count($users))+1; #la cantidad de despachos por persona no puede ser superior a esta
        $datos = array(); #arreglo para almacenar los datos de las transportadoras asignadas a cada usuario

        #Recorre los Usuarios para agregar la cantidad de despachos y transportadoras a las cuales hacerles seguimiento
        foreach ($users as $i => $user) {
            $user = (object)$user;

            $datos[$user->cod_usuari]['categoria'] = $user->nom_grupox."-".$user->cod_priori;
            $datos[$user->cod_usuari]['usr_emailx'] = $user->usr_emailx;
            $datos[$user->cod_usuari]['despachos'] = 0;

            #Recorre las transportadoras que necesitan seguimiento y asigna al controlador
            foreach ($transp as $j => $trans) {
                $trans = (object)$trans;

                $mCapaci = $mMaxDespac - $datos[$user->cod_usuari]['despachos'];
                $mPorcen = ($trans->despac * 100) / $mMaxDespac;

                if( $mCapaci >= $trans->despac || ($trans->despac > $mMaxDespac && $mPorcen <= 110 && $datos[$user->cod_usuari]['despachos'] == 0 ) ) {
                    $datos[$user->cod_usuari]['datos'][$x]['nits'] = $trans->cod_tercer;
                    $datos[$user->cod_usuari]['datos'][$x]['empresas'] = $trans->abr_tercer;
                    $datos[$user->cod_usuari]['despachos'] += $trans->despac;
                    $datos[$user->cod_usuari]['datos'][$x]['des'] = $trans->despac;
                    $datos[$user->cod_usuari]['datos'][$x]['cat'] = $trans->cod_priori;
                    $x++;

                    unset($transp[$j]);
                } elseif( $trans->despac > $mMaxDespac && $mPorcen > 110 && $datos[$user->cod_usuari]['despachos'] == 0 ) {
                    $transp[$j]['despac'] = $trans->despac - $mMaxDespac;

                    $datos[$user->cod_usuari]['datos'][$x]['nits'] = $trans->cod_tercer;
                    $datos[$user->cod_usuari]['datos'][$x]['empresas'] = $trans->abr_tercer;
                    $datos[$user->cod_usuari]['despachos'] += $mMaxDespac;
                    $datos[$user->cod_usuari]['datos'][$x]['des'] = $mMaxDespac;
                    $datos[$user->cod_usuari]['datos'][$x]['cat'] = $trans->cod_priori;
                    $x++;
                }
            }
        }

        #Recorre las transportadoras pendientes por asignar y las asigna, teniendo como prioridad al usuario que menos despachos tiene asignados
        foreach ($transp as $i => $trans) {
            $trans = (object)$trans;
            $val = 0;
            $key = "";

            foreach ($datos as $j => $row) {
                if($val == 0 || $val > $row['despachos']) {
                    $val = $row['despachos'];
                    $key = $j;
                }
            }

            $datos[$key]['datos'][$x]['nits'] = $trans->cod_tercer;
            $datos[$key]['datos'][$x]['empresas'] = $trans->abr_tercer;
            $datos[$key]['despachos'] += $trans->despac;
            $datos[$key]['datos'][$x]['des'] = $trans->despac;
            $datos[$key]['datos'][$x]['cat'] = $trans->cod_priori;
            $x++;

            unset($transp[$i]);
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
        $ind_segprc = "";
        $ind_segcar = "";
        $ind_segtra = "";
        $ind_segdes = "";

        if($datos->ind_segprc != 'false'){
            $ind_segprc = 'checked';
        }
        if($datos->ind_segcar != 'false'){
            $ind_segcar = 'checked';
        }
        if($datos->ind_segtra != 'false'){
            $ind_segtra = 'checked';
        }
        if($datos->ind_segdes != 'false'){
            $ind_segdes = 'checked';
        }
        foreach ($trans as $key => $value) { 
            $despac += $value['despac']; 
            ?>
            <input type="hidden" id="<?= $value['cod_tercer'] ?>" value="<?= $value['despac'] ?>">
        <?php }
        ?>

        <div class="Style2DIV">
            <input type="hidden" id="descac" value="<?= round($despac/count($usuarios))+1 ?>">
            <input type="hidden" id="usuario" value="<?=  $datos->usuario ?>">
            <input type="hidden" id="key" value="<?=  $datos->key ?>">
            <input type="hidden" id="categoria" value="<?=  $datos->categoria ?>">

            <div class="contenido">
                <div class="col-md-12 text-center cellHead">Editar Carga del Usuario <?= $datos->usuario ?></div>
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
                             <input type="button" onclick="addDiv()" value="&nbsp; Agregar &nbsp;" class="small save ui-button ui-widget ui-state-default ui-corner-all">
                        </div>
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                    <div  class="centrado">
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
                           PC <input type="checkbox" name="ind_segprc" <?= $ind_segprc ?> id="ind_segprc" value="1">&nbsp;&nbsp;
                            C <input type="checkbox" name="ind_segcar" <?= $ind_segcar ?> id="ind_segcar" value="1">&nbsp;&nbsp;
                            T <input type="checkbox" name="ind_segtra" <?= $ind_segtra ?> id="ind_segtra" value="1">&nbsp;&nbsp;
                            D <input type="checkbox" name="ind_segdes" <?= $ind_segdes ?> id="ind_segdes" value="1">&nbsp;&nbsp;
                        </div>
                        <input type="button" onclick="aceptar()" value="&nbsp; Aceptar &nbsp;" class="small save ui-button ui-widget ui-state-default ui-corner-all">
                        <input type="button" onclick="closePopUp()" value="&nbsp; Cancelar &nbsp;" class="small save ui-button ui-widget ui-state-default ui-corner-all">
                        <input type="hidden" name="total" id="total" value="<?= $key ?>">
                    </div>
                </div>
            </div>
        </div>
       <?php
    }

    /*! \fn: actualizarMonitoEncabe
     *  \brief: Actualiza la tabla monito encabe según los tipos de servicio y tipos de despachos seleccionados para el controlador
     *  \author: Ing. Fabian Salinas
     *  \date: 15/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: usuario  string  codUsuari controlador asignado
     *  \return: integer
     */
    private function actualizarMonitoEncabe($usuario, $tipServic = array(), $tipDespac = array()) {
        $sql = "SELECT MAX(cod_consec) consec FROM ".BASE_DATOS.".tab_monito_encabe WHERE cod_usuari = '$usuario' ";
        $consulta = new Consulta($sql, self::$cConexion, 'BR');
        $max = $consulta->ret_matrix('a');
        $max = $max[0]['consec'];

        $set = "";
        foreach ($tipDespac as $val) {
            $set .= ", ". self::$cTipDespac[$val] ." = 1";
        }

        $set = "";
        if( sizeof($tipDespac) < 1 ) {
            foreach (self::$cTipDespac as $key => $val) {
                $set .= ", $val = 1";
            }
        } else {
            foreach (self::$cTipDespac as $key => $val) {
                if( array_key_exists($key, $tipDespac) ) {
                    $set .= ", $val = 1";
                } else {
                    $set .= ", $val = 0";
                }
            }
        }

        $sql = "UPDATE ".BASE_DATOS.".tab_monito_encabe
                   SET ind_prcarg = $tipServic[ind_segprc],
                       ind_cargue = $tipServic[ind_segcar],
                       ind_transi = $tipServic[ind_segtra],
                       ind_descar = $tipServic[ind_segdes],
                       usr_modifi = '".self::$cUsuario['cod_usuari']."',
                       fec_modifi = NOW() $set
                 WHERE cod_consec = $max ";
        $consulta = new Consulta($sql, self::$cConexion, 'R');

        return $max;
    }

    /*! \fn: getUserByConsec
     *  \brief: Trae el usuario por el consecutivo
     *  \author: Ing. Fabian Salinas
     *  \date: 15/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codConsec  integer  Consecutivo del usuario
     *  \return: string
     */
    private function getUserByConsec( $codConsec ) {
        $sql = "SELECT cod_usuari 
                  FROM ".BASE_DATOS.".tab_genera_usuari
                 WHERE cod_consec = $codConsec ";
        $consulta = new Consulta($sql, self::$cConexion);
        $result = $consulta->ret_matrix('a');
        return $result[0]['cod_usuari'];
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
        $datos = (object) $_REQUEST;
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
        foreach ($datos->users as $key => $val) {
            $us = $this->getUserByConsec($val);
            $users[$us]['cod_usuari'] = $val;
            $campo = "tipDespacUser_".$val;
            $users[$us]['tipDespacUser'] = $datos->$campo;
        }

        $contro = true;
        foreach ($usuarios as $key => $value) {
            $tipServic = array("ind_segcar" => 0, "ind_segdes" => 0, "ind_segtra" => 0, "ind_segprc" => 0);
            //para los correos
            $controlador = $value;
            $contenido = "";
            $etapa = "";

            #<Tipo de servicio por usuario>
            $segPrc = "ind_segprc$key";
            $segCar = "ind_segcar$key";
            $segTra = "ind_segtra$key";
            $segDes = "ind_segdes$key";

            if( $datos->$segPrc ) {
                $tipServic['ind_segprc'] = 1;
                $etapa .= "Precargue";
            }
            if( $datos->$segCar ) {
                $tipServic['ind_segcar'] = 1;
                $etapa .= "Cargue, ";
            }
            if( $datos->$segTra ) {
                $tipServic['ind_segtra'] = 1;
                $etapa .= "Transito, ";
            }
            if( $datos->$segDes ) {
                $tipServic['ind_segdes'] = 1;
                $etapa .= "Descargue";
            }
            #</Tipo de servicio por usuario>
            
            $max = $this->actualizarMonitoEncabe($value, $tipServic, $users[$value]['tipDespacUser']);

            $terceros = explode(',', $empresas[$key]);
            $despac = explode(",", $despachos[$key]);
            $transport = explode(',', $nom_transp[$key]);
            $catego = explode(',', $categorias[$key]);

            $usr_emailx = $usr_emails[$key];
            
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
                                        (cod_consec, cod_tercer, can_despac, ind_segprc, ind_segcar, ind_segtra, ind_segdes, usr_creaci,fec_creaci )
                                        VALUES
                                        ($max, '$val', '$cantidad', $tipServic[ind_segprc], $tipServic[ind_segcar], $tipServic[ind_segtra], $tipServic[ind_segdes], '$usr_creaci', NOW())";
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
                if(($_SERVER["SERVER_NAME"] != "dev.intrared.net") && ($_SERVER["SERVER_NAME"] != "qa.intrared.net")){
                    //envia el corrreo con la carga del controlador
                    $this->enviarCorreos($controlador, $usr_emailx, $fec_inicia, $fec_finali, $contenido, $total);
                }
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

    /*! \fn: listUsuarios
     *  \brief: Lista los usuarios que estan disponibles para realizar seguimiento en el horario
     *  \author: Ing. Fabian Salinas
     *  \date: 16/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function listUsuarios() {
        $usuarios = $this->getUsuarios();
        $transp = $this->getTranspByHorario($_REQUEST['fec_inicio'], $_REQUEST['hor_inicio'], $_REQUEST['fec_finali'], $_REQUEST['hor_finali']);

        //Incluye el consumo del API de suspenciones
        require_once '../../'.DIR_APLICA_CENTRAL.'/lib/general/suspensiones.php';
        //Instancia la clase
        $sus_terceros = new suspensiones();
        $emp_suspencion = [];
        //Trae los campos a suspender
        $data = $sus_terceros->SetSuspensiones(null, null, null, 'on');

        //Recorre las transportadoras
        foreach ($transp as $keyTrans => $transpValue) {
            //Recorre los registros suspendidos
            foreach ($data['suspendido'] as $keySusp => $sus_terceros) {
                //Valida si son iguales
                if($transpValue['cod_tercer'] == $sus_terceros['cod_tercer']){
                    //Genera la lista de empresas suspendidas a mostrar en pantalla.
                    $emp_suspencion[] = $sus_terceros['cod_tercer']." - ".$sus_terceros['abr_tercer'];
                    //Elimina la posición de la empresas suspendida
                    unset($transp[$keyTrans]);
                }
            }
        }
        

        if (!$usuarios) {
            $this->htmlMensajeSin("No hay usuarios parametrizados para las fechas ingresadas");
        } else if (!$transp) {
            $this->htmlMensajeSin("No hay transportadoras parametrizados para las fechas ingresadas");
            if(!empty($emp_suspencion)){
                $this->htmlMensajeSin("Las siguientes empresas estan suspendidas:<br><br>".join(', <br>',$emp_suspencion));
            }
        }  else {
            $this->pintarTablaUsuariosDisponibles($usuarios);
            $this->pintarTablaTransportadoras($transp);
            if(!empty($emp_suspencion)){
                $this->htmlMensajeSin("Las siguientes empresas estan suspendidas:<br><br>".join(', <br>',$emp_suspencion));
            }
            ?>

            <div class="contenido centrado">
                <input class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" type="button" onclick="mostrarByTipDistri()" value="Continuar">
            </div>
            <?php
        }
    }

    /*! \fn: pintarTablaTransportadoras
     *  \brief: Pinta la tabla de transportadoras en seguimiento
     *  \author: Ing. Fabian Salinas
     *  \date: 08/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: transp  array  data de las transportadoras
     *  \return: 
     */
    private function pintarTablaTransportadoras($transp) {
        ?>
        <div class="col-md-12">&nbsp;</div>
        <div class="col-md-12 centrado CellHead">
            <b>Transportadoras en Seguimiento {<?php echo sizeof($transp);?>} </b>
        </div>
        <div class="col-md-12"></div>
        <div class="col-md-12 centrado CellHead">
            <div class="col-md-4">Transportadora</div>
            <div class="col-md-4">Grupo</div>
            <div class="col-md-4">Despachos en Ruta</div>
        </div>

        <?php
        foreach ($transp as $key => $row) {
            ?>
            <div class="col-md-12 centrado contenido borde-inferior" name="rowTransp" id="rowTransp_<?= $key ?>">
                <div class="col-md-4 text-left">
                    <input type="checkbox" checked name="transp[]" id="transp_<?= $key ?>" value="<?= $row['cod_tercer'] ?>">
                    <label><?= $row['abr_tercer'] ?></label>
                </div>
                <div class="col-md-4"><?= $row['nom_grupox']."-".$row['cod_priori'] ?></div>
                <div class="col-md-4"><?= $row['despac'] ?></div>
            </div>
            <?php
        }
    }

    /*! \fn: pintarTablaUsuariosDisponibles
     *  \brief: Pinta la tabla de usuarios disponibles para asignar carga
     *  \author: Ing. Fabian Salinas
     *  \date: 08/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: usuarios  array  data de los usuarios dispobiles
     *  \return: 
     */
    private function pintarTablaUsuariosDisponibles($usuarios) {
        ?>
        <div class="col-md-12 centrado CellHead">
            <b>Usuarios Disponibles</b>
        </div>
        <div class="col-md-12"></div>
        <div class="col-md-12 centrado CellHead">
            <div class="col-md-2">Usuario</div>
            <div class="col-md-2">Nombre</div>
            <div class="col-md-2">Grupo</div>
            <div class="col-md-3">Hora Inicio</div>
            <div class="col-md-3">Hora Finalizaci&oacute;n</div>
        </div>

        <?php
        foreach ($usuarios as $key => $row) {
            ?>
            <div class="col-md-12 centrado contenido borde-inferior" id="row_<?= $key ?>">
                <div class="col-md-2 text-left">
                    <input type="checkbox" checked name="users[]" id="usuarios_<?= $key ?>" value="<?= $row['cod_consec'] ?>">
                    <label><?= $row['cod_usuari'] ?></label>
                </div>
                <div class="col-md-2 text-left"><?= $row['nom_usuari'] ?></div>
                <div class="col-md-2"><?= $row['nom_grupox']."-".$row['cod_priori'] ?></div>
                <div class="col-md-2"><?= $row['fec_inicia'] ?></div>
                <div class="col-md-2"><?= $row['fec_finalx'] ?></div>
            </div>
            <hr style="background-color:#35650F">
            <?php
        }
    }

    /*! \fn: pintarTabDistriManual
     *  \brief: Pinta la tabla de distribucion manual
     *  \author: Ing. Fabian Salinas
     *  \date: 09/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function pintarTabDistriManual() {
        $tipdes = $this->getTiposDespac();

        $this->pintarTabUsuariosDistriManual();
        $this->pintarTabAsignarEtapas();
        $this->pintarTabAsignarTipDes($tipdes);

        ?>
        <div class="col-md-12">&nbsp;</div>
        <div class="col-md-12 centrado CellHead">
            <b>Transportadoras</b>
        </div>
        <div class="col-md-12"></div>
        <div id="tabsTranspAsignacion">
        <?php

        $transportadoras = $this->getTransportadoras( implode(",", $_REQUEST['transp']) );

        foreach ($transportadoras as $i => $transp) {
            $this->pintarTabDistriManualTransp($transp, $tipdes);
        }
        ?>
        </div>
        <?php
    }

    /*! \fn: pintarTabAsignarEtapas
     *  \brief: Pinta la tabla de asignacion de etapas
     *  \author: Ing. Fabian Salinas
     *  \date: 15/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function pintarTabAsignarEtapas() {
        ?>
        <div id="tabAsignarEtapas" usrActual="">
            <div class="col-md-12 centrado CellHead">
                <b>Etapas</b>
            </div>
            <div class="col-md-12 contenido">
                <div class="col-md-1">&nbsp;</div><div class="col-md-3 text-left">
                    <input disabled value="tipdes_prcarg" type="checkbox" name="asignar_etapas" onclick="calcularCantDespacPorUsuario( $(this) )" id="asignar_etapas_precargue" >
                    Precargue
                </div>
                <div class="col-md-1">&nbsp;</div><div class="col-md-3 text-left">
                    <input disabled value="tipdes_cargue" type="checkbox" name="asignar_etapas" onclick="calcularCantDespacPorUsuario( $(this) )" id="asignar_etapas_cargue" >
                    Cargue
                </div>
                <div class="col-md-1">&nbsp;</div><div class="col-md-3 text-left">
                    <input disabled value="tipdes_transi" type="checkbox" name="asignar_etapas" onclick="calcularCantDespacPorUsuario( $(this) )" id="asignar_etapas_transi" >
                    Transito
                </div>
                <div class="col-md-1">&nbsp;</div><div class="col-md-3 text-left">
                    <input disabled value="tipdes_descar" type="checkbox" name="asignar_etapas" onclick="calcularCantDespacPorUsuario( $(this) )" id="asignar_etapas_descar" >
                    Descargue
                </div>
            </div>
        </div>
        <?php
    }

    /*! \fn: pintarTabAsignar
     *  \brief: Pinta las tablas de Asignaciones tipos de despachos
     *  \author: Ing. Fabian Salinas
     *  \date: 15/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: tipdes  array  tipos de despachos
     *  \return: 
     */
    private function pintarTabAsignarTipDes($tipdes) {
        ?>
        <div id="tabAsignarTipDes" usrActual="">
            <div class="col-md-12 centrado CellHead">
                <b>Tipo de Despachos</b>
            </div>
            <div class="col-md-12 contenido">

            <?php
            foreach ($tipdes as $i => $row) {
                ?>
                <div class="col-md-1">&nbsp;</div><div class="col-md-3 text-left">
                    <input disabled type="checkbox" name="asignar_tipdes" onclick="calcularCantDespacPorUsuario( $(this) )" id="asignar_tipdes_<?= $i ?>" value="<?= $row['cod_tipdes'] ?>" >
                    <?= $row['nom_tipdes'] ?>
                </div>
                <?php
            }
            ?>

            </div>
        </div>
        <?php
    }

    /*! \fn: pintarTabUsuariosDistriManual
     *  \brief: Pinta la tabla de usuarios para distribucion manual
     *  \author: Ing. Fabian Salinas
     *  \date: 13/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function pintarTabUsuariosDistriManual() {
        $usuarios = $this->getUsuarios( implode(',', $_REQUEST['users']) );

        ?>
        <div id="tabUsuarios" usrActual="">
            <div class="col-md-12 centrado CellHead">
                <b>Usuarios</b>
            </div>
            <div class="col-md-12 contenido">

            <?php
            foreach ($usuarios as $i => $row) {
                ?>
                <div class="col-md-1">&nbsp;</div><div class="col-md-3 text-left">
                    <input type="radio" name="manual_user" onclick="formCargaManualUser( $(this) )" id="manual_user_<?= $i ?>" value="<?= $row['cod_consec'] ?>" >
                    <?= $row['cod_usuari'] ?>
                </div>
                <?php
            }
            ?>

            </div>
        </div>
        <?php
    }

    /*! \fn: pintarTabDistriManualTransp
     *  \brief: Pinta la tabla para la distribucion manual por transportadora, tipo de despacho, Fase transito y sus cantidades
     *  \author: Ing. Fabian Salinas
     *  \date: 13/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: transp  array
     *  \return: 
     */
    private function pintarTabDistriManualTransp($transp, $tipdes) {
        $despac = $this->getDespacTransp($transp);
        ?>
        <div name="transpTipser" id="transpTipser_<?= $transp['cod_tercer'] ?>" cod_transp="<?= $transp['cod_tercer'] ?>" nom_transp="<?= $transp['abr_tercer'] ?>" cod_grupox="<?= $transp['cod_grupox'] ?>">
            <div class="col-md-12 centrado CellHead">
                <b><?= $transp['abr_tercer'] ?></b>
            </div>
            <div class="col-md-4 centrado CellHead"><b>Tipo de Despacho</b></div>
            <div class="col-md-2 centrado CellHead"><b>PreCargue</b></div>
            <div class="col-md-2 centrado CellHead"><b>Cargue</b></div>
            <div class="col-md-2 centrado CellHead"><b>Transito</b></div>
            <div class="col-md-2 centrado CellHead"><b>Descargue</b></div>

            <?php
            foreach ($tipdes as $row) {
                $i = $row['cod_tipdes'];
                $segprc = $despac['segprc']['by_tipdes'][$i]['cant_despac'];
                $segprc = $segprc > 0 ? $segprc : 0;
                $segcar = $despac['segcar']['by_tipdes'][$i]['cant_despac'];
                $segcar = $segcar > 0 ? $segcar : 0;
                $segtra = $despac['segtra']['by_tipdes'][$i]['cant_despac'];
                $segtra = $segtra > 0 ? $segtra : 0;
                $segdes = $despac['segdes']['by_tipdes'][$i]['cant_despac'];
                $segdes = $segdes > 0 ? $segdes : 0;

                ?>
                <div name="rowTipDes" class="col-md-12 text-left contenido borde-inferior" id="rowTipDes_<?= $i ?>" cod_tipdes="<?= $i ?>">
                    <div class="col-md-4"><?= $row['nom_tipdes'] ?></div>
                    <div class="col-md-2">
                        <input type="checkbox" name="tipdes_prcarg" disabled onclick="calcularCantDespacPorUsuario( $(this) )" value="<?= $i ?>" cant_despac="<?= $segprc ?>" cod_tipdes="<?= $i ?>">
                        <label><?= $segprc ?></label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="tipdes_cargue" disabled onclick="calcularCantDespacPorUsuario( $(this) )" value="<?= $i ?>" cant_despac="<?= $segcar ?>" cod_tipdes="<?= $i ?>">
                        <label><?= $segcar ?></label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="tipdes_transi" disabled onclick="calcularCantDespacPorUsuario( $(this) )" value="<?= $i ?>" cant_despac="<?= $segtra ?>" cod_tipdes="<?= $i ?>">
                        <label><?= $segtra ?></label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="tipdes_descar" disabled onclick="calcularCantDespacPorUsuario( $(this) )" value="<?= $i ?>" cant_despac="<?= $segdes ?>" cod_tipdes="<?= $i ?>">
                        <label><?= $segdes ?></label>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <?php
    }

    /*! \fn: getTiposDespac
     *  \brief: Retorna los tipos de despachos
     *  \author: Ing. Fabian Salinas
     *  \date: 13/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: array
     */
    private function getTiposDespac() {
        $sql = "";
        foreach (self::$cTipDespac as $key => $value) {
            $sql .= $key.",";
        }
        $sql = trim($sql, ",");

        $sql = "SELECT a.cod_tipdes, a.nom_tipdes
                  FROM ".BASE_DATOS.".tab_genera_tipdes a
                 WHERE a.cod_tipdes IN ($sql) ";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }

    /*! \fn: getDespacTransp
     *  \brief: Trae los despachos en ruta de una transportadora, por tipo de servicio
     *  \author: Ing. Fabian Salinas
     *  \date: 13/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: transp  array  data de la transportadora
     *  \return: array
     */
    private function getDespacTransp($transp){
        $result = array();
        $_REQUEST['Ajax'] = "off";
        @include_once( '../inform/class_despac_trans3.php' );

        $Despac = new Despac(self::$cConexion, self::$cUsuario, self::$cCodAplica);

        if( $transp['ind_segtra'] == '1' && $transp['ind_segdes'] == '0' && $transp['ind_segcar'] == '0' && $transp['ind_segprc'] == '0' ) {
            $result['segtra'] = $this->getDespacSoloTransito($transp['cod_tercer']);
        } else {
            $result['segprc'] = $this->obtenerArrayCargaPorTipoDespac($Despac, $transp['cod_tercer'], 'ind_segprc');
            //$result['segprc'] = $this->obtenerArrayCargaPorTipoDespac($Despac, $transp['cod_tercer'], 'ind_segcar');
            $result['segcar'] = $this->obtenerArrayCargaPorTipoDespac($Despac, $transp['cod_tercer'], 'ind_segcar');
            $result['segtra'] = $this->obtenerArrayCargaPorTipoDespac($Despac, $transp['cod_tercer'], 'ind_segtra');
            $result['segdes'] = $this->obtenerArrayCargaPorTipoDespac($Despac, $transp['cod_tercer'], 'ind_segdes');
        }

        return $result;
    }

    /*! \fn: obtenerArrayCargaPorTipoDespac
     *  \brief: Valida si la transportadora tiene el tipo de seguimiento del parametro y retorna la info de los despachos en ruta del tipo de seguimineto
     *  \author: Ing. Fabian Salinas
     *  \date: 13/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: Despac  object  Objeto de la clase Despac
     *  \return: array
     */
    private function obtenerArrayCargaPorTipoDespac($Despac, $codTransp, $tipSeguim) {
        $dataTransp = $Despac->getTranspServic($tipSeguim, $codTransp);
        if( sizeof($dataTransp) < 1 ) {
            return array();
        } else {
            switch ($tipSeguim) {
                case 'ind_segprc': $funcion = "getDespacPrcCargue";  break;
                case 'ind_segcar': $funcion = "getDespacCargue";  break;
                case 'ind_segtra': $funcion = "getDespacTransi2"; break;
                case 'ind_segdes': $funcion = "getDespacDescar";  break;
            }

            return $this->depurarArrayDespachos( $Despac->$funcion($dataTransp[0]) );
        }
    }

    /*! \fn: depurarArrayDespachos
     *  \brief: Crea un nuevo array solo con la información necesaria para la distribucion de carga laboral
     *  \author: Ing. Fabian Salinas
     *  \date: 13/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: data  array  data completa de los despachos en ruta
     *  \return: array
     */
    private function depurarArrayDespachos( $data ) {
        $result = array("tot_despac" => 0, "lista" => "", "by_tipdes" => array());

        foreach ($data as $row) {
            #$result['lista'] .= $row['num_despac'].",";
            $result['tot_despac']++;
            $i = $row['cod_tipdes'];

            if( array_key_exists($i, $result['by_tipdes']) ) {
                $result['by_tipdes'][$i]['cant_despac']++;
            } else {
                $result['by_tipdes'][$i] = array("cant_despac" => 1, "cod_tipdes" => $i, "nom_tipdes" => $row['nom_tipdes']);
            }
        }

        $result['lista'] = trim($result['lista'], ',');

        return $result;
    }

    /*! \fn: getDespacSoloTransito
     *  \brief: Trae la cantidad de despachos por modalidad para las empresas que solo tienen parametrizado ind_segtra
     *  \author: Ing. Fabian Salinas
     *  \date: 09/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: codTransp  integer  Codigo de la transportadora
     *  \return: array
     */
    private function getDespacSoloTransito($codTransp) {
        $sql = "SELECT COUNT(a.num_despac) AS cant_despac, a.cod_tipdes, i.nom_tipdes 
                  FROM ".BASE_DATOS.".tab_despac_despac a 
             INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
                     ON a.num_despac = b.num_despac 
                    AND a.fec_salida IS NOT NULL 
                    AND a.fec_salida <= NOW() 
                    AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                    AND a.ind_planru = 'S' 
                    AND a.ind_anulad = 'R'
                    AND b.ind_activo = 'S' 
                    AND b.cod_transp = '$codTransp'
             INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
                     ON b.cod_transp = c.cod_tercer 
             INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
                     ON a.cod_ciuori = d.cod_ciudad 
                    AND a.cod_depori = d.cod_depart 
                    AND a.cod_paiori = d.cod_paisxx 
             INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
                     ON a.cod_ciudes = e.cod_ciudad 
                    AND a.cod_depdes = e.cod_depart 
                    AND a.cod_paides = e.cod_paisxx 
             INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
                     ON a.cod_depori = f.cod_depart 
                    AND a.cod_paiori = f.cod_paisxx 
             INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
                     ON a.cod_depdes = g.cod_depart 
                    AND a.cod_paides = g.cod_paisxx 
             INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
                     ON b.cod_conduc = h.cod_tercer 
             INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
                     ON a.cod_tipdes = i.cod_tipdes 
              GROUP BY a.cod_tipdes ";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }

    /*! \fn: pintarTabAignacionVacia
     *  \brief: Pinta la tabla de asignacion de carga laboral vacia
     *  \author: Ing. Fabian Salinas
     *  \date: 09/09/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function pintarTabAignacionVacia() {
        $usuarios = $this->getUsuarios( implode(',', $_REQUEST['users']) );
        $transportadoras = $this->getTransportadoras( implode(",", $_REQUEST['transp']) );

        $this->pintarCabeceraTabAsignacio();

        foreach ($usuarios as $key => $row) {
            ?>
            <div class="col-md-12 centrado contenido borde-inferior" name="rowUserAsignacion" numrow="<?= $key ?>" id="di<?= $key ?>" consec="<?= $row['cod_consec'] ?>">
                <div class="col-md-1"><?= ($key+1) ?></div>
                <div class="col-md-3">
                    <?= $row['cod_usuari'] ?>
                    <input type="hidden" name="usuarios[]" id="usu<?= $key ?>" value="<?= $row['cod_usuari'] ?>">
                    <input type="hidden" name="usr_emailx[]" id="ema<?= $key ?>" value="<?= $row['usr_emailx'] ?>">
                </div>
                <div class="col-md-2" id="categoria<?= $key ?>"><?= $row['nom_grupox'] ?>-<?= $row['cod_priori'] ?></div>
                <div class="col-md-2">
                    <label name="cantDespac" id="cantDespac_<?= $key ?>">0</label>
                    <input type="hidden" value="" name="despachos[]" id="despa<?= $key ?>">
                    <select name="tip_despac_user" class="hidden" id="tip_despac_user_<?= $key ?>" user="<?= $row['cod_usuari'] ?>" consec_user="<?= $row['cod_consec'] ?>"></select>
                </div>
                <div class="col-md-2 text-left" name="empres" id="empres<?= $key ?>">
                    <select name="transpControl" class="hidden" id="transpControl_<?= $key ?>"></select>
                </div>
                <div class="col-md-2">
                    PC <input disabled type="checkbox" value="1" id="ind_segprc<?= $key ?>" name="ind_segprc<?= $key ?>">&nbsp;&nbsp;
                    C <input disabled type="checkbox" value="1" id="ind_segcar<?= $key ?>" name="ind_segcar<?= $key ?>">&nbsp;&nbsp;
                    T <input disabled type="checkbox" value="1" id="ind_segtra<?= $key ?>" name="ind_segtra<?= $key ?>">&nbsp;&nbsp;
                    D <input disabled type="checkbox" value="1" id="ind_segdes<?= $key ?>" name="ind_segdes<?= $key ?>">&nbsp;&nbsp;
                </div>
            </div>
            <?php
        }

        $this->pintarPieTabAsignacio('registrar');
    }
}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new ajax_horari_monito();
}
?>
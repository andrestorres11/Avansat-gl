<?php
/* ! \file: ajax_soluci_noveda.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 29/10/2015
 *  \bug: 
 *  \bug: 
 *  \warning: al pasar a produccion quitar los comentarios en la query de la fucion getConfigEmpres(), getFestTransp()
 */


setlocale(LC_ALL, "es_ES");

/* ! \class: trans
 *  \brief: Clase trasn que gestiona las diferentes peticiones ajax 
 */
class noveda {

    private static $cConexion,
            $cCodAplica,
            $cUsuario,
            $cNull = array(array('', '-----')),
            $NIT_CORONA = '860068121';

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

                case "getInform";
                    $this->getInform();
                    break;

                case "getDataGeneral";
                    $this->getDataGeneral();
                    break;

                default:
                    header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

    /* ! \fn: getProductos
     *  \brief: retorna un arreglo con la lista de los productos
     *  \author: Ing. Alexander Correa
     *  \date: 29/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return array con los productos $productos
     */
    public function getProductos() {

        $query = "SELECT cod_produc, nom_produc 
                   FROM " . BASE_DATOS . ".tab_genera_produc 
                   WHERE ind_estado = '1' ORDER BY nom_produc";
        $consulta = new Consulta($query, self::$cConexion);

        $productos = $consulta->ret_matriz("a");

        return $productos;
    }

    /* ! \fn: getTiposDespachos
     *  \brief: retorna una lista con los tipos de despacho
     *  \author: Ing. Alexander Correa
     *  \date: 30/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return array con los despachos $despachos
     */
    public function getTiposDespachos() {

        $query = "SELECT cod_tipdes, nom_tipdes 
                    FROM " . BASE_DATOS . ".tab_genera_tipdes 
                ORDER BY nom_tipdes";
        $consulta = new Consulta($query, self::$cConexion);

        $despachos = $consulta->ret_matriz("a");

        return $despachos;
    }

    /* ! \fn: getInform
     *  \brief: Muestra el informe segun los parametros de busqueda establecidos
     *  \author: Ing. Alexander Correa
     *  \date: 31/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return html con el informe $informe
     */
    private function getInform() {
        $respuesta = array();
        $datos = (object) $_POST;

        $datos->cod_produc = explode(",", $datos->cod_produc);
        $datos->cod_produc = join("','", $datos->cod_produc);
        $datos->cod_produc = "'" . $datos->cod_produc . "'";

        $datos->cod_tipdes = explode(",", $datos->cod_tipdes);
        $datos->cod_tipdes = join("','", $datos->cod_tipdes);
        $datos->cod_tipdes = "'" . $datos->cod_tipdes . "'";

        $cEmpresa = $this->getConfigEmpres();

        $festivos = $this->getFest($datos->fec_inicia, $datos->fec_finali);
        $inicial = date("Y-m-d", strtotime($datos->fec_inicia));
        $final = date("Y-m-d", strtotime($datos->fec_finali));
        $contador = 0;
        $key = 0;
        $dia = date("N", strtotime($final));
        $dia = $this->formatDay($dia);

        if ($datos->pestana == "generaID") {
            while ($inicial <= $final) {
                #saca el dia laboral
                $dia = date("N", strtotime($inicial));
                $dia = $this->formatDay($dia);

                #extraigo las horas de ingreso y salida del dia
                $hor_inicia = $cEmpresa[$dia]['hor_ingres'];
                $hor_finali = $cEmpresa[$dia]['hor_salida'];

                if (!in_array($inicial, $festivos)) {
                    #condicional de acuerdo a la pestaña
                    if ($datos->pestana == "generaID") {
                        $respuesta[$key]['dia'] = strftime("%A, %d de %B del %Y", strtotime($inicial));
                        $respuesta[$key]['datos'] = $this->getDataDiaGeneral($datos->cod_produc, $datos->cod_tipdes, $inicial, $hor_inicia, $hor_finali, $contador);
                    }

                    $key ++;
                    $contador = 0;
                } else {
                    $contador ++;
                }

                $inicial = strtotime('+1 day', strtotime($inicial));
                $inicial = date('Y-m-d', $inicial);
            }
        } else {
            $dia = date("N", strtotime($inicial));
            $dia = $this->formatDay($dia);
            #extraigo las horas de ingreso y salida del dia
            $hor_inicia = $cEmpresa[$dia]['hor_ingres'];
            $hor_finali = $cEmpresa[$dia]['hor_salida'];
            $fecha_inicial2 = $this->getDiaHabil($datos->fec_inicia, $festivos, $cEmpresa);
            $fecha_final2 = strtotime($final . " " . $cEmpresa[$dia]['hor_salida']);
            $fecha_final2 = date('Y-m-d H:i:s', $fecha_final2);

            if ($datos->pestana == "transiID") {
                $and = " AND c.cod_etapax IN (0,3) ";
            } else if ($datos->pestana == "cargueID") {
                $and = " AND c.cod_etapax IN (1,2) ";
            } else if ($datos->pestana == "descarID") {
                $and = " AND c.cod_etapax IN (4,5) ";
            }

            $mSelect = "SELECT a.cod_noveda, c.nom_noveda
                          FROM " . BASE_DATOS . ".tab_protoc_asigna a 
                    INNER JOIN " . BASE_DATOS . ".tab_despac_despac b ON a.num_despac = b.num_despac  
                    INNER JOIN " . BASE_DATOS . ".tab_genera_noveda c ON a.cod_noveda = c.cod_noveda  
                     LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext d ON b.num_despac = d.num_despac
                         WHERE (a.fec_noveda BETWEEN '$fecha_inicial2' AND '$fecha_final2') $and AND c.nov_especi = 1";
            $mSelect .= $datos->cod_produc != "''" ? " AND d.cod_mercan IN ($datos->cod_produc) " : '';
            $mSelect .= $datos->cod_tipdes != "''" ? " AND b.cod_tipdes IN ($datos->cod_tipdes) " : '';
            $mSelect .= " GROUP BY c.cod_noveda ";
            $consulta = new Consulta($mSelect, self::$cConexion);
            $novedades = $consulta->ret_matrix("a");
            $respuesta = $this->getDataDiaPestana($datos->cod_produc, $datos->cod_tipdes, $inicial, $hor_inicia, $hor_finali, $novedades, $datos->fec_finali);
        }

        #variables para los datos por columna
        $tgdia = 0;
        $tgacu = 0;
        $tr = "";
        //variables para los datos generales
        $tgenerados = 0;
        $tsolucmenos = 0;
        $tpendientes = 0;
        $psolumenos = 0;
        $ppendientes = 0;

        $tipo = "";
        if ($datos->pestana == "generaID") {
            $tipo = "FECHA";
        } else {
            $this->informePestana($respuesta, $datos->pestana, $datos->fec_inicia, $datos->fec_finali);
        }

        #varialbles para los jQuery de consulta detallasa
        $jSgeneral = 'onclick = "general(\'' . $datos->pestana . '\')"';

        foreach ($respuesta as $key => $value) {

            $tgenerados += ($value['datos']['dia']['total'] + $value['datos']['acumulado']['total']);
            $tsolucmenos += ($value['datos']['dia']['menores'] + $value['datos']['acumulado']['menores']);
            $tpendientes += ($value['datos']['dia']['no_resueltos'] + $value['datos']['acumulado']['no_resueltos']);
            $tgdia += $value['datos']['dia']['total'];
            $tgacu += $value['datos']['acumulado']['total'];

            $tgdiamenores += $value['datos']['dia']['menores'];
            $tgacumenores += $value['datos']['acumulado']['menores'];

            $tgdiamayores += $value['datos']['dia']['mayores'];
            $tgacumayores += $value['datos']['acumulado']['mayores'];

            $tgdiano += $value['datos']['dia']['no_resueltos'];
            $tgacuno += $value['datos']['acumulado']['no_resueltos'];

            $tr .= "<tr align='center' class='fila' >
                        <td class='cellInfo onlyCell'>" . utf8_decode($value['dia']) . "</td>
                        <td class='cellInfo onlyCell'>" . $value['datos']['dia']['total'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'\', \'' . $value['datos']['dia']['fec_inicial'] . '\',\'' . $value['datos']['dia']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['dia']['menores'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'menor\', \'' . $value['datos']['dia']['fec_inicial'] . '\',\'' . $value['datos']['dia']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['dia']['pmenores'] . " %</td>
                    <td class='cellInfo onlyCell'>" . $value['datos']['dia']['mayores'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'mayor\', \'' . $value['datos']['dia']['fec_inicial'] . '\',\'' . $value['datos']['dia']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['dia']['pmayores'] . " %</td>
                    <td class='cellInfo onlyCell'>" . $value['datos']['dia']['no_resueltos'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'Sin\', \'' . $value['datos']['dia']['fec_inicial'] . '\',\'' . $value['datos']['dia']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['dia']['pnoresue'] . " %</td>
                    <td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['total'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'\', \'' . $value['datos']['acumulado']['fec_inicial'] . '\',\'' . $value['datos']['acumulado']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['menores'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'menor\', \'' . $value['datos']['acumulado']['fec_inicial'] . '\',\'' . $value['datos']['acumulado']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['pmenores'] . " %</td>
                    <td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['mayores'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'mayor\', \'' . $value['datos']['acumulado']['fec_inicial'] . '\',\'' . $value['datos']['acumulado']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['pmayores'] . " %</td>
                    <td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['no_resueltos'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'Sin\', \'' . $value['datos']['acumulado']['fec_inicial'] . '\',\'' . $value['datos']['acumulado']['fecha_final'] . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'unico\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['datos']['acumulado']['pnoresue'] . " %</td>
                        <td class='cellInfo onlyCell'>" . ($value['datos']['dia']['total'] + $value['datos']['acumulado']['total']) . "</td>
                    </tr>";
        }

        $tpdiamenores = round((($tgdiamenores * 100) / $tgdia), 2);
        $tpdiamayores = round((($tgdiamayores * 100) / $tgdia), 2);
        $tpdiano = round((($tgdiano * 100) / $tgdia), 2);
        $pacumenores = round((($tgacumenores * 100) / $tgacu), 2);
        $tpacumayores = round((($tgacumayores * 100) / $tgacu), 2);
        $tpacuno = round((($tgacuno * 100) / $tgacu), 2);

        #para la suma final de los resultados
        $tr .= "<tr align='center'>
                    <td class='CellHead2'>TOTAL</td>
                    <td class='CellHead2'>" . $tgdia . "</td>
                    <td class='CellHead2'>" . $tgdiamenores . "</td>
                    <td class='CellHead2'>" . $tpdiamenores . " %</td>
                    <td class='CellHead2'>" . $tgdiamayores . "</td>
                    <td class='CellHead2'>" . $tpdiamayores . " %</td>
                    <td class='CellHead2'>" . $tgdiano . "</td>
                    <td class='CellHead2'>" . $tpdiano . " %</td>
                    <td class='CellHead2'>" . $tgacu . "</td>
                    <td class='CellHead2'>" . $tgacumenores . "</td>
                    <td class='CellHead2'>" . $pacumenores . " %</td>
                    <td class='CellHead2'>" . $tgacumayores . "</td>
                    <td class='CellHead2'>" . $tpacumayores . " %</td>
                    <td class='CellHead2'>" . $tgacuno . "</td>
                    <td class='CellHead2'>" . $tpacuno . " %</td>
                    <td class='CellHead2'>" . $tgenerados . "</td>
                </tr>";

        $tsolumas = ($tgenerados - ($tsolucmenos + $tpendientes));
        $psolumenos = round(($tsolucmenos * 100) / $tgenerados, 2);
        $psolumas = round(($tsolumas * 100) / $tgenerados, 2);
        $ppendientes = round(($tpendientes * 100) / $tgenerados, 2);

        $mHtml .= "<div class='col-md-12 StyleDIV' style='overflow:scroll'>
                    <table class='table' width='100%' cellpadding='0'>
                        <tr>
                            <td class='CellHead2' colspan='7' align='center'>
                                <b>INDICADOR TIEMPO A SOLUCI&Oacute;N DE NOVEDADES COMPRENDIDO ENTRE $datos->fec_inicia y $final </b>
                            </td>
                        </tr>
                        <tr  align='center'>
                            <td class='CellHead2'>GENERADOS</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MENOS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MAS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>PENDIENTES POR SOLUCIONAR</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                        </tr>
                        <tr  align='center'>
                          <td class='cellInfo onlyCell'>" . $tgenerados . " <img src='../" . DIR_APLICA_CENTRAL . "/imagenes/ver.png' width='16px' height='16px' style='cursor:pointer' $jSgeneral/></td>
                          <td class='cellInfo onlyCell'>" . $tsolucmenos . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'menor\', \'' . $datos->fec_inicia . '\',\'' . $datos->fec_finali . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'\', \'\' )"/></td>';
        $mHtml .= "<td class='cellInfo onlyCell'>" . $psolumenos . " %</td>
                    <td class='cellInfo onlyCell'>" . $tsolumas . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'mayor\', \'' . $datos->fec_inicia . '\',\'' . $datos->fec_finali . '\', \'' . $datos->pestana . '\',\'' . '' . '\' , \'\', \'\' )"/></td>';
        $mHtml .= "<td class='cellInfo onlyCell'>" . $psolumas . " %</td>
                    <td class='cellInfo onlyCell'>" . $tpendientes . ' <img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'Sin\', \'' . $datos->fec_inicia . '\',\'' . $datos->fec_finali . '\', \'' . $datos->pestana . '\',\'' . '' . '\', \'\',\'\' )"/></td>';
        $mHtml .= "<td class='cellInfo onlyCell'>" . $ppendientes . " %</td>
                        </tr>
                    </table>
                    <div class='col-md-12'>&nbsp;</div>
                    <table width='100%' class='table hoverTable' cellpadding='0'>
                        <tr>
                            <td class='CellHead2' colspan='16' align='center'><b>DETALLADO POR D&Iacute;AS</b></td>
                        </tr>
                        <tr>
                            <td rowspan='2' class='CellHead2' align='center'>$tipo</td>
                            <td colspan='7' class='CellHead2' align='center'>GENERADOS DEL D&Iacute;A</td>
                            <td colspan='7' class='CellHead2' align='center'>GENERADOS ACOMULADOS</td>
                            <td rowspan='2' class='CellHead2' align='center'>TOTAL</td>
                        </tr>
                        <tr  align='center'>
                            <td class='CellHead2'>GENERADOS</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MENOS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MAS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>PENDIENTES POR SOLUCIONAR</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'>GENERADOS</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MENOS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MAS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>PENDIENTES POR SOLUCIONAR</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                        </tr>" . $tr . "</table>
                </div>
            </div>";
        echo $mHtml;
    }

    private function query($fec_inicia, $fec_finali, $cod_noveda = NULL, $ind_solucion = 0, $cod_produc, $cod_tipdes) {

        $query = "SELECT a.fec_noveda, a.fec_noved2, a.cod_noveda, c.nom_noveda, a.num_consec, b.num_despac
                    FROM " . BASE_DATOS . ".tab_protoc_asigna a 
              INNER JOIN " . BASE_DATOS . ".tab_despac_despac b ON a.num_despac = b.num_despac  
              INNER JOIN " . BASE_DATOS . ".tab_genera_noveda c ON a.cod_noveda = c.cod_noveda  
              INNER JOIN " . BASE_DATOS . ".tab_despac_sisext d ON b.num_despac = d.num_despac
                   WHERE (a.fec_noveda BETWEEN '$fec_inicia' AND '$fec_finali') AND b.ind_anulad != 'A'";

        $query .= $cod_noveda == NULL ? ' AND c.nov_especi = 1 ' : " AND a.cod_noveda = $cod_noveda ";
        $query .= $ind_solucion == 1 ? " AND a.fec_noved2 IS NOT NULL AND a.fec_noved2 <> '0000-00-00 00:00:00' " : " AND (a.fec_noved2 IS NULL OR a.fec_noved2 = '0000-00-00 00:00:00') ";
        $query .= $cod_produc != "''" ? " AND d.cod_mercan IN ($cod_produc) " : '';
        $query .= $cod_tipdes != "''" ? " AND b.cod_tipdes IN ($cod_tipdes) " : '';
        $query .= " GROUP BY a.num_despac, a.num_consec ";
        $consulta = new Consulta($query, self::$cConexion);
        $datos = $consulta->ret_matrix("a");

        return $datos;
    }

    /* ! \fn: informePestana()
     *  \brief: pinta el informe de las pestañas
     *  \author: Ing. Alexander Correa
     *    \date: 13/11/2015
     *    \date modified: dia/mes/año
     *  \param: $datos => arreglo con los datos a pintar
     *  \param: 
     *  \return html
     */
    private function informePestana($datos, $pestana, $fec_inicia, $fec_finali) {

        $jSgeneral = 'onclick = "general(\'' . $pestana . '\')"';
        foreach ($datos as $key => $value) {
            $tgenerados += ( $value['novedadDia']['total'] );
            $tsolucmenos += ($value['novedadDia']['menores'] );
            $tpendientes += ($value['novedadDia']['no_resueltos']);

            $tgdiamenores += $value['novedadDia']['menores'];

            $tgdiamayores += $value['novedadDia']['mayores'];

            $tgdiano += $value['novedadDia']['no_resueltos'];

            $tr .= "<tr align='center' class='fila' >
                        <td class='cellInfo onlyCell'>" . utf8_encode($value['novedad']) . "</td>
                        <td class='cellInfo onlyCell'>" . $value['novedadDia']['total'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'' . $value['cod_noveda'] . '\', \'\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['novedadDia']['menores'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'menor\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'' . $value['cod_noveda'] . '\', \'\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['novedadDia']['pmenores'] . " %</td>
                        <td class='cellInfo onlyCell'>" . $value['novedadDia']['mayores'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'mayor\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'' . $value['cod_noveda'] . '\', \'\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['novedadDia']['pmayores'] . " %</td>
                        <td class='cellInfo onlyCell'>" . $value['novedadDia']['no_resueltos'] . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'Sin\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'' . $value['cod_noveda'] . '\', \'\' )"/></td>';
            $tr .= "<td class='cellInfo onlyCell'>" . $value['novedadDia']['pnoresue'] . " %</td>
                    </tr>";
        }
        $tpdiamenores = round((($tgdiamenores * 100) / $tgenerados), 2);
        $tpdiamayores = round((($tgdiamayores * 100) / $tgenerados), 2);
        $tpdiano = round((($tgdiano * 100) / $tgenerados), 2);

        #para la suma final de los resultados
        $tr .= "<tr align='center'>
                    <td class='CellHead2'>TOTAL</td>
                    <td class='CellHead2'>" . $tgenerados . "</td>
                    <td class='CellHead2'>" . $tgdiamenores . "</td>
                    <td class='CellHead2'>" . $tpdiamenores . " %</td>
                    <td class='CellHead2'>" . $tgdiamayores . "</td>
                    <td class='CellHead2'>" . $tpdiamayores . " %</td>
                    <td class='CellHead2'>" . $tgdiano . "</td>
                    <td class='CellHead2'>" . $tpdiano . " %</td>
                </tr>";

        $tsolumas = ($tgenerados - ($tsolucmenos + $tpendientes));
        $psolumenos = round(($tsolucmenos * 100) / $tgenerados, 2);
        $psolumas = round(($tsolumas * 100) / $tgenerados, 2);
        $ppendientes = round(($tpendientes * 100) / $tgenerados, 2);

        $mHtml = "<div class='col-md-12 StyleDIV' style='overflow:scroll'>
                    <table class='table' width='100%' cellpadding='0'>
                        <tr>
                            <td class='CellHead2' colspan='7' align='center'><b>INDICADOR TIEMPO A SOLUCI&Oacute;N DE NOVEDADES </b></td>
                        </tr>
                        <tr  align='center'>
                            <td class='CellHead2'>GENERADOS</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MENOS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MAS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>PENDIENTES POR SOLUCIONAR</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                        </tr>
                        <tr  align='center'>";
        $mHtml .="<td class='cellInfo onlyCell'>" . $tgenerados . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" ' . $jSgeneral . '/></td>';
        $mHtml .="<td class='cellInfo onlyCell'>" . $tsolucmenos . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'menor\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'\', \'\' )"/></td>
                            <td class="cllInfo onlyCell">' . $psolumenos . " %</td>";
        $mHtml .="<td class='cellInfo onlyCell'>" . $tsolumas . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'mayor\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'\', \'\' )"/></td>
                            <td class="cellInfo onlyCell">' . $psolumas . " %</td>";
        $mHtml .="<td class='cellInfo onlyCell'>" . $tpendientes . '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" onclick="generarReporte(\'Sin\', \'' . $fec_inicia . '\',\'' . $fec_finali . '\', \'' . $pestana . '\',\'\', \'\' )"/></td>';
        $mHtml .="<td class='cellInfo onlyCell'>" . $ppendientes . " %</td>
                        </tr>
                    </table>
                    <div class='col-md-12'>&nbsp;</div>
                    <table width='100%' class='table hoverTable' cellpadding='0'>
                        <tr>
                            <td class='CellHead2' colspan='8' align='center'><b>DETALLADO POR NOVEDAD ENTRE $fec_inicia Y $fec_finali</b></td>
                        </tr>
                        <tr>
                            <td rowspan='2' class='CellHead2' align='center'>NOVEDAD</td>
                            <td colspan='7' class='CellHead2' align='center'>GENERADOS POR NOVEDAD</td>
                        </tr>
                        <tr  align='center'>
                            <td class='CellHead2'>GENERADOS</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MENOS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>SOLUCIONADAS MAS DE 4 HORAS</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                            <td class='CellHead2'><small>PENDIENTES POR SOLUCIONAR</small></td>
                            <td class='CellHead2'>PORCENTAJE</td>
                        </tr>" . $tr . "</table>
                  </div>
              </div>";
        echo $mHtml;
        die;
    }

    /* ! \fn: getDataDiaPestana()
     *  \brief: function que trae los datos dia por dia de las pestañas excepto la general
     *  \author: Ing. Alexander Correa
     *  \date: 05/11/205
     *  \date modified: dia/mes/año
     *  \param: cod_produc String con los códigos de producto seleccionados
     *  \param: cod_tipdes String con los códigos de tipo de despacho seleccionados
     *  \param: fec_inicia dia en el que se va a realizar la consulta
     *  \param: hor_inicia hora de inicio de la consulta
     *  \param: hor_finali hora de finalización de la consulta
     *  \param: pestana pestaña para la cual se esta haciendo la consulta
     *  \param: dias cantidad de días de regresion para los acumulados
     *  \return valor que retorna
     */
    protected function getDataDiaPestana($cod_produc, $cod_tipdes, $fec_inicia, $hor_inicia, $hor_finali, $novedades, $fecha_final) {
        $datos = array();
        $cEmpresa = $this->getConfigEmpres();

        $festivos = $this->getFest($fec_inicia, $fecha_final);
        $fecha_inicia = $this->getDiaHabil($fec_inicia, $festivos, $cEmpresa);
        /* $fecha_inicia = $fec_inicia." ".$hor_inicia; */
        $fecha_final = $fecha_final . " " . $hor_finali;
        #Fin resta la cantidad de dias necesarias para consultar los acumulados

        $x = 0;
        foreach ($novedades as $key => $value) {
            $cod_noveda = $value['cod_noveda'];
            $nom_noveda = $value['nom_noveda'];
            #consulta para los registros ordinarios resueltos
            $datos_noveda_dia = $this->query($fecha_inicia, $fecha_final, $cod_noveda, 1, $cod_produc, $cod_tipdes);

            #consulta para los registros ordinarios sin resolver
            $datos_noveda_dia2 = $this->query($fecha_inicia, $fecha_final, $cod_noveda, 0, $cod_produc, $cod_tipdes);

            #pregunto si llegaron datos del dia o acumulados
            if ($datos_noveda_dia || $datos_noveda_dia2) {
                $datos[$x]['novedad'] = $nom_noveda;
                $datos[$x]['cod_noveda'] = $cod_noveda;
                #calculo los tiempos por si llegaron datos del dia o acumulados                
                if ($datos_noveda_dia) {
                    $menores = 0;
                    foreach ($datos_noveda_dia as $key => $value) {
                        $diferencia = $this->getDiferencia($value['fec_noveda'], $value['fec_noved2']);
                        if ($diferencia < 240) {
                            $menores ++;
                        }
                    }

                    $datos[$x]['novedadDia']['total'] = (count($datos_noveda_dia) + count($datos_noveda_dia2));
                    $datos[$x]['novedadDia']['menores'] = $menores;
                    $datos[$x]['novedadDia']['no_resueltos'] = count($datos_noveda_dia2);
                    $datos[$x]['novedadDia']['pnoresue'] = round(($datos[$x]['novedadDia']['no_resueltos'] * 100) / $datos[$x]['novedadDia']['total'], 2);
                    $datos[$x]['novedadDia']['pmenores'] = round(($datos[$x]['novedadDia']['menores'] * 100) / $datos[$x]['novedadDia']['total'], 2);
                    $datos[$x]['novedadDia']['mayores'] = ($datos[$x]['novedadDia']['total'] - ($menores + $datos[$x]['novedadDia']['no_resueltos']));
                    $datos[$x]['novedadDia']['pmayores'] = round(($datos[$x]['novedadDia']['mayores'] * 100) / $datos[$x]['novedadDia']['total'], 2);
                } else if ($datos_noveda_dia2) {
                    $datos[$x]['novedadDia']['total'] = (count($datos_noveda_dia2));
                    $datos[$x]['novedadDia']['menores'] = 0;
                    $datos[$x]['novedadDia']['no_resueltos'] = count($datos_noveda_dia2);
                    $datos[$x]['novedadDia']['pnoresue'] = round(($datos[$x]['novedadDia']['no_resueltos'] * 100) / $datos['novedadDia']['total'], 2);
                    $datos[$x]['novedadDia']['pmenores'] = 0;
                    $datos[$x]['novedadDia']['mayores'] = 0;
                    $datos[$x]['novedadDia']['pmayores'] = 0;
                }
            }

            $x++;

            unset($cod_noveda, $nom_noveda);
        }

        return $datos;
    }

    /* ! \fn: getDataDiagetDataDiaGeneral()
     *  \brief: funion para traer los datos dia por dia de la pestaña general
     *  \author: Ing. Alexander Correa
     *  \date: 03/11/2015
     *  \date modified: dia/mes/año
     *  \param: cod_produc String con los códigos de producto seleccionados
     *  \param: cod_tipdes String con los códigos de tipo de despacho seleccionados
     *  \param: fec_inicia dia en el que se va a realizar la consulta
     *  \param: hor_inicia hora de inicio de la consulta
     *  \param: hor_finali hora de finalización de la consulta
     *  \param: dias cantidad de días de regresion para los acumulados
     *  \return arreglo con los datos para los dias consultados $datos
     */
    protected function getDataDiaGeneral($cod_produc, $cod_tipdes, $fec_inicia, $hor_inicia, $hor_finali, $dias) {
        $datos = array();
        #arma los string para las fehca inicial y final
        $fecha_inicial = $fec_inicia . " " . $hor_inicia;
        $fecha_final = $fec_inicia . " " . $hor_finali;
        $configuracion = $this->getConfigEmpres();
        $festivos = $this->getFest($fec_inicia, $datos->fec_finali);

        #consulta para los registros ordinarios resueltos
        $datos_dia = $this->query($fecha_inicial, $fecha_final, NULL, 1, $cod_produc, $cod_tipdes);
        #variable para contar los resueltos en menos de 4 horas
        $menores = 0;
        foreach ($datos_dia as $key => $value) {
            $diferencia = $this->getDiferencia($value['fec_noveda'], $value['fec_noved2']);
            if ($diferencia < 240) {
                $menores ++;
            }
        }

        #consulta para los registros ordinarios sin resolver
        $datos_dia2 = $this->query($fecha_inicial, $fecha_final, NULL, 0, $cod_produc, $cod_tipdes);

        $datos['dia'] ['total'] = (count($datos_dia) + count($datos_dia2));
        $datos['dia'] ['fec_inicial'] = $fecha_inicial;
        $datos['dia'] ['fecha_final'] = $fecha_final;
        $datos['dia'] ['menores'] = $menores;
        $datos['dia'] ['no_resueltos'] = count($datos_dia2);
        $datos['dia'] ['mayores'] = ($datos['dia'] ['total'] - ($menores + $datos['dia'] ['no_resueltos']));
        $datos['dia'] ['pmenores'] = round(($datos['dia']['menores'] * 100) / $datos['dia']['total'], 2);
        $datos['dia'] ['pmayores'] = round(($datos['dia']['mayores'] * 100) / $datos['dia']['total'], 2);
        $datos['dia'] ['pnoresue'] = round(($datos['dia']['no_resueltos'] * 100) / $datos['dia']['total'], 2);

        # fin consulta para los registros ordinarios
        #resta la cantidad de dias necesarias para consultar los acumulados
        $fecha_final = strtotime('-1 second', strtotime($fecha_inicial));
        $fecha_final = date('Y-m-d H:i:s', $fecha_final);

        $fecha_inicial = date('Y-m-d', strtotime($fecha_inicial));
        $fecha_inicial = $this->getDiaHabil($fecha_inicial, $festivos, $configuracion);

        #Fin resta la cantidad de dias necesarias para consultar los acumulados
        #conulta para los acumulados resueltos        
        $datos_acumulado = $this->query($fecha_inicial, $fecha_final, NULL, 1, $cod_produc, $cod_tipdes);

        $menores = 0;
        foreach ($datos_acumulado as $key => $value) {
            $diferencia = $this->getDiferencia($value['fec_noveda'], $value['fec_noved2']);
            if ($diferencia < 240) {
                $menores ++;
            }
        }

        #conulta para los acumulados NO resueltos 
        $datos_acumulado2 = $this->query($fecha_inicial, $fecha_final, NULL, 0, $cod_produc, $cod_tipdes);

        $datos['acumulado'] ['total'] = (count($datos_acumulado) + count($datos_acumulado2));
        $datos['acumulado'] ['menores'] = $menores;
        $datos['acumulado'] ['fec_inicial'] = $fecha_inicial;
        $datos['acumulado'] ['fecha_final'] = $fecha_final;
        $datos['acumulado'] ['no_resueltos'] = count($datos_acumulado2);
        $datos['acumulado'] ['mayores'] = ( $datos['acumulado'] ['total'] - ($menores + $datos['acumulado'] ['no_resueltos']));
        $datos['acumulado'] ['pmenores'] = round(($datos['acumulado']['menores'] * 100) / $datos['acumulado']['total'], 2);
        $datos['acumulado'] ['pmayores'] = round(($datos['acumulado']['mayores'] * 100) / $datos['acumulado']['total'], 2);
        $datos['acumulado'] ['pnoresue'] = round(($datos['acumulado']['no_resueltos'] * 100) / $datos['acumulado']['total'], 2);

        return $datos;
    }

    protected function getConfigEmpres() {
        $mSelect = "SELECT com_diasxx, hor_ingres, hor_salida
                  FROM " . BASE_DATOS . ".tab_config_horlab
                 WHERE cod_tercer = '" . self::$NIT_CORONA . "' AND ind_config = 1";
        $consulta = new Consulta($mSelect, self::$cConexion);
        $mConfig = $consulta->ret_matrix("a");

        foreach ($mConfig as $row) {
            $mDias = explode('|', $row['com_diasxx']);

            foreach ($mDias as $key => $dia)
                $mHorario[$dia] = array("hor_ingres" => $row['hor_ingres'], "hor_salida" => $row['hor_salida']);
        }

        return $mHorario;
    }

    private function getFest($mFecInicia = null, $mFecFinali = null) {
        if ($mFecInicia) {
            $mFecInicia = strtotime('-7 day', strtotime($mFecInicia));
            $mFecInicia = date('Y-m-d', $mFecInicia);
        }
        $mSql = "SELECT a.fec_festiv 
                   FROM " . BASE_DATOS . ".tab_config_festiv a 
                  WHERE a.cod_tercer = '" . self::$NIT_CORONA . "' 
                    AND a.cod_ciudad = 1
                " . ($mFecInicia == null ? "" : " AND a.fec_festiv >= '{$mFecInicia}' " ) . "
                " . ($mFecFinali == null ? "" : " AND a.fec_festiv <= '{$mFecFinali}' " ) . "
                ";
        $mConsult = new Consulta($mSql, self::$cConexion);
        $mMatriz = $mConsult->ret_matrix('a');
        $mResult = array();

        foreach ($mMatriz as $row)
            $mResult[] = $row['fec_festiv'];

        return $mResult;
    }

    /* ! \fn: getDiferecia
     *  \brief: devuelve la diferencia en minutos entre dos fechas basado en la jornada laboral       
     *  \author: Ing. Alexander Correa
     *  \date: 04/11/2015   
     *  \date modified: 11/05/2016
     *  \modified by: Ing. Fabian Salinas
     *  \param: $fec_inicia fecha inicial de la consulta
     *  \param: $fec_finali fiecha final de la consulta
     *  \return $diferencia entero con la diferencia en minutos de la operacion
     */
    private function getDiferencia($fec_inicia, $fec_finali) {

        #convierto las fechas a fechas compatibles php
        $fec_inicia = date("Y-m-d H:i:s", strtotime($fec_inicia));
        $fec_finali = date("Y-m-d H:i:s", strtotime($fec_finali));

        #extraigo la jornada laboral y los festivos comprendidos en la fecha inicial y final
        $cEmpresa = $this->getConfigEmpres();
        $festivos = $this->getFest($fec_inicia, $fec_finali);
        $minutos = 0;
        $fecha = date("Y-m-d", strtotime($fec_inicia));
        $ffinal = date("Y-m-d", strtotime($fec_finali));

        $mFecInicia = explode(" ", $fec_inicia); //array con fecha y hora
        $mFecFinali = explode(" ", $fec_finali); //array con fecha y hora
        $tiempo = 0;

        while ($fecha <= $ffinal) {
            if (!in_array($fecha, $festivos)) {#si no es día festivo 
                $dia = date("N", strtotime($fecha));
                $dia = $this->formatDay($dia);

                #extraigo las horas de ingreso y salida del dia
                $hor_inicia = $cEmpresa[$dia]['hor_ingres'];
                $hor_finali = $cEmpresa[$dia]['hor_salida'];

                if ($mFecInicia[0] == $mFecFinali[0]) { # Solucionado el mismo dia de registro
                    if (($mFecInicia[1] < $hor_inicia || $mFecInicia[1] > $hor_finali) && ($mFecFinali[1] < $hor_inicia || $mFecFinali[1] > $hor_finali)) {
                        $tiempo += 0;
                    } else {

                        if ($mFecInicia[1] < $hor_inicia) {
                            $fec1 = $hor_inicia;
                        } else {
                            $fec1 = $mFecInicia[1];
                        }

                        if ($mFecFinali[1] < $hor_finali) {
                            $fec2 = $mFecFinali[1];
                        } else {
                            $fec2 = $hor_finali;
                        }

                        $tiempo += diffHours($fec1, $fec2);
                    }
                } else if ($fecha == $mFecInicia[0]){ #Primer Dia
                    if( $mFecInicia[1] > $hor_finali ){ #Registrado Despues del horario laboral 
                        $tiempo += 0;
                    }else if( $mFecInicia[1] < $hor_inicia ){ #Registrado antes del horario laboral 
                        $tiempo += diffHours($hor_inicia, $hor_finali);
                    }else{ #Registrado en el horario laboral 
                        $tiempo += diffHours($mFecInicia[1], $hor_finali);
                    }
                }else if($fecha == $mFecFinali[0]){ #Ultimo Dia 
                    if( $mFecFinali[1] > $hor_finali ){ #Solucionado Despues del horario laboral 
                        $tiempo += diffHours($hor_inicia, $hor_finali);
                    }else if( $mFecFinali[1] < $hor_inicia ){ #Solucionado antes del horario laboral 
                        $tiempo += 0;
                    }else{ #Solucionado en el horario laboral 
                        $tiempo += diffHours($hor_inicia, $mFecFinali[1]);
                    }
                }else{ #Dia Intermedio
                    $tiempo += diffHours($hor_inicia, $hor_finali);
                }
            }

            #se incrementa la fecha inicial para seguir sumando los minutos
            $fecha = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
        }

        return $tiempo;
    }

    private function formatDay($mNumDia) {

        switch ($mNumDia) {
            case '1':
                $mDay = 'L';
                break;
            case '2':
                $mDay = 'M';
                break;
            case '3':
                $mDay = 'X';
                break;
            case '4':
                $mDay = 'J';
                break;
            case '5':
                $mDay = 'V';
                break;
            case '6':
                $mDay = 'S';
                break;
            case '7':
                $mDay = 'D';
                break;
        }

        return $mDay;
    }

    /* ! \fn: getDataGeneral
     *  \brief: funcion que consulta el detallado general dependiendo de la pestaña en la que se encuentre
     *  \author: Ing. Alexander Correa
     *  \date: 09/11/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return devuelve un html con los datos de la consulta ordendos
     */

    private function getDataGeneral() {

        $data = (object) $_POST;

        $and = "";
        if ($data->pestana == "transiID") {
            $and .= " AND p.cod_etapax IN (0,3) ";
        } else if ($data->pestana == "cargueID") {
            $and .= " AND p.cod_etapax IN (1,2) ";
        } else if ($data->pestana == "descarID") {
            $and .= " AND p.cod_etapax IN (4,5) ";
        }

        if ($data->novedad) {
            $and .= " AND p.cod_noveda = $data->novedad ";
        }

        if (!$data->unico) {
            $cEmpresa = $this->getConfigEmpres(); //configuracion laboral de la empresa
            $festivos = $this->getFest($data->fec_inicia, $data->fec_finali); //festivos
            $finicial = $data->fec_inicia;
            $fec_inicia = $this->getDiaHabil($finicial, $festivos, $cEmpresa); //traigo el ultimo dia habil para iniciar la consulta ahí


            $diafin = date("N", strtotime($data->fec_finali));
            $diafin = $this->formatDay($diafin);

            #la consulta va desde la hora de salida de el ultimo dia de la consulta
            $hor_finali = $cEmpresa[$diafin]['hor_salida'];
            $fec_finali = ($data->fec_finali . " " . $hor_finali);
        } else {
            $fec_inicia = $data->fec_inicia;
            $fec_finali = $data->fec_finali;
        }

        $data->cod_produc = explode(",", $data->cod_produc);
        $data->cod_produc = join("','", $data->cod_produc);
        $data->cod_produc = "'" . $data->cod_produc . "'";

        $data->cod_tipdes = explode(",", $data->cod_tipdes);
        $data->cod_tipdes = join("','", $data->cod_tipdes);
        $data->cod_tipdes = "'" . $data->cod_tipdes . "'";

        $mSelect = "SELECT a.num_despac, 
                       IF(b.num_despac IS NULL, 'NR', b.num_despac) AS num_viajex, 
                       IF(b.cod_manifi IS NULL, a.cod_manifi, b.cod_manifi) AS cod_manifi,
                       IF(b.fec_despac IS NULL, a.fec_despac, b.fec_despac) AS fec_despac, 
                       IF(c.nom_tipdes IS NULL, l.nom_tipdes, c.nom_tipdes) AS nom_tipdes, 
                       IF(d.nom_ciudad IS NULL, m.nom_ciudad, d.nom_ciudad) AS nom_ciuori,
                       IF(e.nom_ciudad IS NULL, n.nom_ciudad, e.nom_ciudad) AS nom_ciudes, 
                       CONCAT( b.fec_citcar, ' ', b.hor_citcar ) AS fec_citcar,
                       IF(b.nom_sitcar IS NULL, a.nom_sitcar, b.nom_sitcar) AS nom_sitcar, 
                       IF(b.val_pesoxx IS NULL, a.val_pesoxx, b.val_pesoxx) AS val_pesoxx, 
                       IF(b.obs_despac IS NULL, a.obs_despac, b.obs_despac) AS obs_despac,
                       IF(b.cod_conduc IS NULL, g.cod_conduc, b.cod_conduc) AS cod_conduc, 
                       IF(b.nom_conduc IS NULL, h.abr_tercer, b.nom_conduc) AS nom_conduc, 
                       IF(b.con_telmov IS NULL, a.con_telmov, b.con_telmov) AS con_telmov, 
                       IF(b.num_solici IS NULL, '-', b.num_solici) AS num_solici, 
                       IF(b.num_pedido IS NULL, '-', b.num_pedido) AS num_pedido, 
                       IF(b.num_placax IS NULL, g.num_placax, b.num_placax) AS num_placax,
                       IF(b.tip_vehicu IS NULL, 'No registrado', b.tip_vehicu) AS tip_vehicu, 
                       IF(b.nom_poseed IS NULL, k.abr_tercer, b.nom_poseed) AS nom_poseed, 
                       b.tip_transp, f.nom_produc, o.fec_noveda,o.fec_noved2,p.nom_noveda,
                       q.ind_cumcar, UPPER( r.nom_usuari )  nom_usrasi , UPPER( s.nom_usuari )  nom_usreje
                  FROM " . BASE_DATOS . ".tab_despac_despac a
             LEFT JOIN " . BASE_DATOS . ".tab_despac_corona b ON a.num_despac = b.num_dessat
             LEFT JOIN " . BASE_DATOS . ".tab_genera_tipdes c ON b.cod_tipdes = c.cod_tipdes 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_ciudad d ON b.cod_ciuori = d.cod_ciudad 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_ciudad e ON b.cod_ciudes = e.cod_ciudad 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_produc f ON b.cod_mercan = f.cod_produc 
             LEFT JOIN " . BASE_DATOS . ".tab_despac_vehige g ON a.num_despac = g.num_despac 
             LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer h ON g.cod_conduc = h.cod_tercer 
             LEFT JOIN " . BASE_DATOS . ".tab_vehicu_vehicu i ON g.num_placax = i.num_placax 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_tipveh j ON i.cod_tipveh = j.cod_tipveh 
             LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer k ON i.cod_tenedo = k.cod_tercer 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_tipdes l ON a.cod_tipdes = l.cod_tipdes 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_ciudad m ON a.cod_ciuori = m.cod_ciudad 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_ciudad n ON a.cod_ciudes = n.cod_ciudad
            INNER JOIN " . BASE_DATOS . ".tab_protoc_asigna o ON a.num_despac = o.num_despac  
            INNER JOIN " . BASE_DATOS . ".tab_genera_noveda p ON p.cod_noveda = o.cod_noveda
            INNER JOIN " . BASE_DATOS . ".tab_despac_sisext q ON q.num_despac = a.num_despac
             LEFT JOIN " . BASE_DATOS . ".tab_genera_usuari r ON o.usr_asigna = r.cod_usuari
             LEFT JOIN " . BASE_DATOS . ".tab_genera_usuari s ON o.usr_ejecut = s.cod_usuari
             WHERE (o.fec_noveda BETWEEN '$fec_inicia' AND '$fec_finali') AND p.nov_especi = 1 AND a.ind_anulad != 'A'";
        $mSelect .= $data->cod_produc != "''" ? " AND q.cod_mercan IN ($data->cod_produc) " : '';
        $mSelect .= $data->cod_tipdes != "''" ? " AND a.cod_tipdes IN ($data->cod_tipdes) " : '';
        $mSelect .= "$and ORDER BY a.num_despac";

        $consulta = new Consulta($mSelect, self::$cConexion);
        $datos = $consulta->ret_matrix("a");

        $result = "<div class='col-md-12' id='data'>
                        <label id='totalID'> </label>
                        <div class='col-md-12' id='data2'>";

        $tabla = "<table class='table' width='100%' cellpadding='0' id='detalle'>
                    <tr>
                        <td  class='CellHead2'>Despacho SAT</td>
                        <td  class='CellHead2'>Viaje</td>
                        <td  class='CellHead2'>Manifiesto</td>
                        <td  class='CellHead2'>Fecha Despacho</td>
                        <td  class='CellHead2'>Tipo Despacho</td>
                        <td  class='CellHead2'>Origen</td>
                        <td  class='CellHead2'>Destino</td>
                        <td  class='CellHead2'>Fecha Cita Cargue</td>
                        <td  class='CellHead2'>cumplimiento Cita Cargue</td>
                        <td  class='CellHead2'>Sitio Cargue</td>
                        <td  class='CellHead2'>Peso(Kg)</td>
                        <td  class='CellHead2'>Observaciones</td>
                        <td  class='CellHead2'>C.C. Conductor</td>
                        <td  class='CellHead2'>Nombre Conductor</td>
                        <td  class='CellHead2'>Celular Conductor</td>
                        <td  class='CellHead2'>Solicitud</td>
                        <td  class='CellHead2'>Pedido</td>
                        <td  class='CellHead2'>Placa</td>
                        <td  class='CellHead2'>Tipo Vehiculo</td>
                        <td  class='CellHead2'>Poseedor</td>
                        <td  class='CellHead2'>Tipo Transportadora</td>
                        <td  class='CellHead2'>Mercancia/Negocio</td>
                        <td  class='CellHead2'>Fecha Novedad Asignada</td>
                        <td  class='CellHead2'>Fecha Novedad Soluci&oacute;n</td>
                        <td  class='CellHead2'>Diferencia</td>
                        <td  class='CellHead2'>Usuario Asignado</td>
                        <td  class='CellHead2'>Usuario que Gestiona</td>
                        <td  class='CellHead2'>Novedad</td>
                        <td  class='CellHead2'>Cumplimiento</td>
                    </tr>";
        $contador = 0;

        foreach ($datos as $key => $value) {
            if ($value['fec_noved2']) {
                $minutos = $this->getDiferencia($value['fec_noveda'], $value['fec_noved2']);
            } else {
                $minutos = "N/A";
            }

            if (!$data->tipo || (   ($data->tipo == 'menor' && $minutos < 240 && $minutos !== 'N/A') || 
                                    ($data->tipo == 'mayor' && $minutos >= 240 && $minutos !== 'N/A') || 
                                    ($data->tipo == 'Sin' && $minutos === 'N/A')
                                )
            ) {

                if ($minutos < 240 && $minutos !== 'N/A')
                    $mTxt = 'Cumplio';
                elseif ($minutos >= 240 && $minutos !== 'N/A')
                    $mTxt = 'Incumplio';
                else
                    $mTxt = 'Pendiente Solucion';

                $contador ++;
                $enlace = '<a href="index.php?cod_servic=3302&window=central&despac=' . $value['num_despac'] . '&tie_ultnov=0&opcion=1"><img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" /></a>';
                $tabla.= "<tr>
                            <td class='cellInfo onlyCell'>" . $value['num_despac'] . "&nbsp;&nbsp;&nbsp;" . $enlace . "</td>
                            <td class='cellInfo onlyCell'>" . $value['num_viajex'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['cod_manifi'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['fec_despac'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_tipdes'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_ciuori'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_ciudes'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['fec_citcar'] . "</td>
                            <td class='cellInfo onlyCell'>" . ( $value['ind_cumcar'] == '1' ? 'SI' : 'NO' ) . "</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_sitcar'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['val_pesoxx'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['obs_despac'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['cod_conduc'] . "</td>
                            <td class='cellInfo onlyCell'>" . utf8_encode($value['nom_conduc']) . "</td>
                            <td class='cellInfo onlyCell'>" . $value['con_telmov'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['num_solici'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['num_pedido'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['num_placax'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['tip_vehicu'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_poseed'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['tip_transp'] . "</td>
                            <td class='cellInfo onlyCell'>" . utf8_encode($value['nom_produc']) . "</td>
                            <td class='cellInfo onlyCell'>" . $value['fec_noveda'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['fec_noved2'] . "</td>
                            <td class='cellInfo onlyCell'>" . $minutos . " Min(s)</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_usrasi'] . "</td>
                            <td class='cellInfo onlyCell'>" . $value['nom_usreje'] . "</td>
                            <td class='cellInfo onlyCell'>" . utf8_encode($value['nom_noveda']) . "</td>
                            <td class='cellInfo onlyCell'>" . $mTxt . "</td>
                          </tr>";
            }
        }

        $tabla.="</table>";
        $result.= $tabla . "</div>
                                <input id='total' type='hidden'value='" . $contador . "'>
                            </div>";
        $_SESSION['exportExcel'] = $tabla;

        $data = $result . "<script>
                                getExcel();                       
                           </script>";

        echo $data;
    }

    private function getDiaHabil($fecha_inicial, $festivos, $configuracion) {

        for ($i = 1; $i < 30; $i++) {
            $fec = strtotime('-' . $i . ' day', strtotime($fecha_inicial));
            $fec = date('Y-m-d', $fec);
            if (!in_array($fec, $festivos)) {
                break;
            }
        }

        $dia = date("N", strtotime($fec));
        $dia = $this->formatDay($dia);
        $hor_finali = $configuracion[$dia]['hor_salida'];

        $fec = strtotime('+1 second', strtotime($fec . " " . $hor_finali));
        $fec = date('Y-m-d H:i:s', $fec);
        return $fec;
    }

}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new noveda();
}
?>

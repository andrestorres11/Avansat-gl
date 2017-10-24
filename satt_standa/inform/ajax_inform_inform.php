<?php

/* ! \file: ajax_inform_inform.php
 *  \brief: archivo con multiples funciones ajax para los informes
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 19/11/2015
 *  \bug: 
 *  \bug: 
 *  \warning:
 */

setlocale(LC_ALL, "es_ES");

class inform {
    private static $cConexion,
    $cCodAplica,
    $cUsuario,
    $cTotalDespac,
    $cNull = array(array('', '-----'));

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
                case "getDetalle";
                    $this->getDetalle();
                    break;
                case "getInformEalCumplidas";
                    $this->getInformEal();
                    break;
                case "getDetalleEal";
                    $this->getDetalleEal();
                    break;
                case "getInformIndSaldes";
                    $this->getInformIndSaldes();
                    break;
                case "getDetalleSalDes";
                    $this->getDetalleSalDes();
                    break;
                case "getPoseedores";
                    $this->getPoseedores();
                    break;
                case "getOrigenes";
                    $this->getOrigenes();
                    break;
                case "GetInformAsignaCargax";
                    $this->GetInformAsignaCargax();
                    break;
                case "getDetalleCargax";
                    $this->getDetalleCargax();
                    break;
                default:
                    header('Location: ../../'.BASE_DATOS.'/index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

    /*! \fn: getInform
     *  \brief: Funcion de trancision para el reporte de No. de Novedades por usuario
     *  \author: Ing. Alexander Correa
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function getInform() {
        $datos = (object) $_POST;
        $intervalo = "";

        if($datos->tipo == 1){
            $datos->hor_finali = str_replace(":00", ":59", $datos->hor_finali);
            $datos->fec_inicia = $datos->fec_inicia." ".$datos->hor_inicia.":00";
            $datos->fec_finali = $datos->fec_finali." ".$datos->hor_finali.":59";
            $intervalo = " 1 day"; // para los dias
        }else if($datos->tipo == 2){
            $intervalo = " 1 week"; // para las semanas
        }else if($datos->tipo == 3){
            $intervalo = " 1 month"; // para los meses
        }

        $data = $this->getDatosInform($datos->fec_inicia, $datos->fec_finali, $datos->usuario, $intervalo, $datos->perfil, $datos->tipInform);

        if( $datos->tipo == 1){
            $this->infoDia($data, $datos);
        }else if($datos->tipo == 2){
            $this->infoSemana($data, $datos);
        }else if($datos->tipo == 3){
            $this->infoMes($data, $datos);
        }
    }

    /*! \fn: getDatosInform
     *  \brief: Devuelve los datos a pintar 
     *  \author: Ing. Alexander Correa
     *  \date: 24/11/2015
     *  \date modified:    
     *  \param: $finicia = fecha inicial de la consulta 
     *  \param: $ffinali = fecha final   de la consulta
     *  \param: $intervalo = intervalo de tiempo de la consulta
     *  \return objeto con los datos de la consulta
     */
    private function getDatosInform($finicia, $ffinali, $usuario, $intervalo, $perfil, $tipInform = NULL){
        $and = "";
        $fec1 = "";
        $group = "";
        $group2 = "";
        $p = "";
        $u = "";

        if($intervalo == " 1 day"){
            $and = ", DATE_FORMAT(a.fec_creaci, '%Y-%m-%d %H') AS fec1";
            $fec1 = ", x.fec1";
            $group2 = " GROUP BY x.usr_creaci $fec1 ";
        }else if($intervalo == " 1 month"){
            $and = ", DATE_FORMAT(a.fec_creaci, '%Y-%m') AS fec1";
            $fec1 = ", x.fec1";
            $group2 = " GROUP BY x.usr_creaci $fec1 ";
        }else if($intervalo == " 1 week"){
            $and = ",  WEEK(a.fec_creaci) AS fec1";
            $fec1 = ", x.fec1";
            $group = "  GROUP BY fec1, a.num_despac, a.usr_creaci  ASC ";
        }

        if($perfil){
            $p = "AND c.cod_perfil IN ($perfil) ";
        }
        if($usuario){
            $u = " AND a.usr_creaci IN ($usuario)";
        }

        $datoSelect = "x.num_despac";
        $datoGroup = "x.usr_creaci"; 
        $datoLabel = "x.usr_creaci";
        $datoCodig = "x.cod_perfil";

        if( $tipInform == "nov"){
            $datoSelect = "x.cod_noveda";
            $datoGroup = "x.cod_noveda"; 
            $datoLabel = "CONVERT(x.nom_noveda  USING utf8) ";
            $datoCodig = "x.cod_noveda";
        }

        $sql = "SELECT COUNT(DISTINCT($datoSelect)) AS can_despac, COUNT($datoSelect) AS can_regist, 
                       $datoLabel AS usr_creaci, $datoCodig AS cod_perfil $fec1
                  FROM (
                            (
                                    SELECT a.cod_noveda, d.nom_noveda, a.num_despac, a.fec_creaci, a.usr_creaci, b.cod_perfil  $and
                                      FROM ".BASE_DATOS.".tab_despac_contro a
                                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci
                                INNER JOIN ".BASE_DATOS.".tab_genera_perfil c ON c.cod_perfil = b.cod_perfil
                                INNER JOIN ".BASE_DATOS.".tab_genera_noveda d ON a.cod_noveda = d.cod_noveda
                                     WHERE a.fec_creaci >= '$finicia' AND  a.fec_creaci < '$ffinali' $p $u
                                           $group
                            )
                            UNION ALL
                            (
                                    SELECT a.cod_noveda, d.nom_noveda, a.num_despac, a.fec_creaci, a.usr_creaci, b.cod_perfil $and
                                      FROM ".BASE_DATOS.".tab_despac_noveda a 
                                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                                INNER JOIN ".BASE_DATOS.".tab_genera_perfil c ON c.cod_perfil = b.cod_perfil
                                INNER JOIN ".BASE_DATOS.".tab_genera_noveda d ON a.cod_noveda = d.cod_noveda
                                     WHERE a.fec_creaci >= '$finicia' AND  a.fec_creaci < '$ffinali' $p $u
                                           $group
                            )
                       ) x 
              GROUP BY $datoGroup $fec1 
              ORDER BY $datoGroup $fec1"; 
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /*! \fn: infoDia
     *  \brief: pinta el informe por dia del reporte de Numero de novedades por usuario
     *  \author: Ing. Alexander Correa
     *  \date: 26/11/2015 
     *  \date modified: dia/mes/año
     *  \param: $datos = datos del post
     *  \param: $data = datos de la consulta para pintar los datos
     *  \return html
     */
    private function infoDia($data, $datos){
        $mAux = array(); //variable auxiliar para alacenar los usuarios
        $mAux2 = array(); //variable auxiliar para alacenar los perfiles
        $mData = array(); // variable auxiliar para ordenar los datos de la consulta
        $despac = 0;
        $regist = 0;
        $fec_inicia_aux =date ( 'Y-m-d' , strtotime($datos->fec_inicia));
        $fec_inicia_aux2 =date ( 'Y-m-d' , strtotime($datos->fec_inicia));

        $hor_inicia = date("H:i:s", strtotime($datos->hor_inicia.":00"));
        $hor_finali = date("H:i:s", strtotime($datos->hor_finali.":00"));
        $usuario = strtolower($data[0]['usr_creaci']);

        foreach ($data as $key => $value) {
            //--------------para agrupar los despachos por dia----------
            $fec1 =  $value["fec1"].":00:00";
            $hora = date("H:i:s", strtotime($fec1));
            $fec1 = date ( 'Y-m-d' , strtotime($fec1));

            if($fec1 == $fec_inicia_aux && $usuario == strtolower($value["usr_creaci"])){
                if($hora >= $hor_inicia && $hora <= $hor_finali){
                    $despac += $value["can_despac"];
                }
            }else{
                $despac = 0;

                if($hora >= $hor_inicia && $hora <= $hor_finali){
                    $despac += $value["can_despac"];
                }
                if($fec1 != $fec_inicia_aux){
                    $fec_inicia_aux = strtotime ( '+1 day' , strtotime ( $fec_inicia_aux ) ) ;
                    $fec_inicia_aux = date ( 'Y-m-d' , $fec_inicia_aux );
                }
                if($usuario != strtolower($value["usr_creaci"])){
                    $usuario = strtolower ($value["usr_creaci"]);
                    $fec_inicia_aux = date ( 'Y-m-d' , strtotime($fec_inicia_aux2) );
                }
            }
            //--------------fin agruacion de despachos por dia--------

            $regist = $value["can_regist"];
            $mData[strtolower ($value["usr_creaci"])][$value["fec1"]]['can_despac'] = $despac;
            $mData[strtolower ($value["usr_creaci"])][$value["fec1"]]['can_regist'] = $regist;
            $mData[strtolower ($value["usr_creaci"])][$value["fec1"]]['cod_perfil'] = $value["cod_perfil"];
            $mData[$value["fec1"]]['can_despac'] += $value["can_despac"];
            $mData[$value["fec1"]]['can_regist'] += $value["can_regist"];

            if(!in_array(strtolower ($value["usr_creaci"]), $mAux)){
                $mAux[] = strtolower ($value["usr_creaci"]);
                $mAux2[] = $value["cod_perfil"];
            }
        }

        $mHtml = '<div id="tabla" class="col-md-12 Style2DIV">
            <label><img src="../'.$_SESSION['DIR_APLICA_CENTRAL'].'/imagenes/excel.jpg"  style="cursor:pointer" onclick="pintarExcel()"/></label>
            <table width="100%" id="TablaDetalle" cellspacing="0" cellpadding="2" border="0" class="table hoverTable">';

        $fec_inicia = $datos->fec_inicia;
        $fec_inicia = date ( 'Y-m-d' , strtotime($fec_inicia) );
        $fec_finali = $datos->fec_finali;
        $fec_finali = date ( 'Y-m-d' , strtotime($fec_finali) );

        while ($fec_inicia <= $fec_finali){
                $j = 0;  
                $usuarios = ""; //variable para concatenar los usuarios
                $mtdespac = 0;
                $mtregist = 0;
                $tDespac = 0;
                $dth = array();

            foreach ($mAux as $key => $value){
                $usuarios .= "$value,";
                $mTr = "";
                $mTfoot = "";
                $mTr3 = "";
                $inici = explode(":", $datos->hor_inicia);
                $minini = number_format($inici[1]);
                $inici = number_format($inici[0]);
                $final = explode(":", $datos->hor_finali);
                $minfin = number_format($final[1]);
                $final = number_format($final[0])+1;

                if(strlen($inici) == 1){
                    $inici = "0".$inici;
                    $hor = $inici;
                }else{
                    $hor= $inici;
                }

                $mTfoot .=$key == (count($mAux)-1)? "<tr style='text-align:center'><th colspan='2' class='CellHead2' style='text-align:center'>TOTAL</th>" : "";
                $mTr .= $j == 0 ? "<tr style='text-align:center'>" : ""; //para los totales del footer

                if( $datos->tipInform == "usr" ){
                    $mTr3 .= $j == 0 ? "<tr style='text-align:center'><th rowspan='2' class='CellHead2'>C&oacute;digo Perfil</th><th class='CellHead2' rowspan='2'>Usuario</th>" : "";
                }else{
                    $mTr3 .= $j == 0 ? "<tr style='text-align:center'><th rowspan='2' class='CellHead2'>C&oacute;digo Novedad</th><th class='CellHead2' rowspan='2'>Novedad</th>" : "";
                }

                $mTr2 = "<tr style='text-align:center'>";
                $mTr2.="<td class='cellInfo onlyCell'>".$mAux2[$key]."</td>
                    <td class='cellInfo onlyCell' >".strtoupper($value)."</td>";

                $i = 0; $x = 0;//variables para saber cuantas horas van en el reporte
                $tDespac = 0;
                $tDespac2 = 0;
                $tRegist = 0;
                $inici2 = $inici; // hora inicial auxiliar para los totales por usuario
                $final2 = ($final);

                while($inici != $final){
                    $mTr .= $j == 0 ? "<th colspan='2' class='CellHead2' style='text-align:center'> $inici:00</th>": "";

                    if ( $datos->tipInform != "usr" ) {
                        $mTr3 .= $j == 0 ? "<th colspan=\"2\" class='CellHead2'>Registros</th>": ""; 
                    }else{
                        $mTr3 .= $j == 0 ? "<th class='CellHead2'>D. Tr&aacute;nsito</th><th class='CellHead2'>Registros</th>": "";
                    }
                    $hora = $inici;
                    if(strlen($inici) == 1){
                        $hora = "0".$inici;
                    }

                    $despachos = 0 + $mData[$value][$fec_inicia." ".$hora]['can_despac'];
                    $registros = 0 + $mData[$value][$fec_inicia." ".$hora]['can_regist'];
                    $dth[$fec_inicia][$hora] += $despachos;

                    $control = $tDespac;

                    if($despachos != 0 ){
                        $tDespac = $despachos; 
                    }

                    $tRegist += $registros;
                    $i ++;
                    $hora_reporte = $hora+1;

                    if(strlen($hora_reporte) == 1){
                        $hora_reporte = "0".$hora_reporte;
                    }

                    $img1 ="";
                    $img2 ="";
                    $img5 ="";
                    $img6 ="";
                    $imgData = '<img src="../' . DIR_APLICA_CENTRAL . '/imagenes/ver.png" width="16px" height="16px" style="cursor:pointer" />';

                    if( $datos->tipInform == "usr" ){
                        if($despachos > 0 ){
                            $img1 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(1,'."'$fec_inicia $inici2'".','."'$fec_inicia ".($hora_reporte)."'".','."'$value'".', '."'$datos->tipInform'".'  )">'.$despachos.'</a>'; 
                        }else{
                            $img1 = 0;
                        }

                        if($registros > 0){
                            $img2 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(2,'."'$fec_inicia $hora'".','."'$fec_inicia ".($hora_reporte)."'".','."'$value'".' , '."'$datos->tipInform'".'  )">'.$registros.'</a>';
                        }else{
                            $img2 = 0;
                        }
                    }else{
                        if($registros > 0){
                            $img2 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(2,'."'$fec_inicia $hora'".','."'$fec_inicia ".($hora_reporte)."'".','."'$mAux2[$key]'".', '."'$datos->tipInform'".'   )">'.$registros.'</a>';
                        }else{
                            $img2 = 0;
                        }
                    }

                    if( $datos->tipInform == "usr" ){
                        $mTr2.="<td class='cellInfo onlyCell'>$img1</td>
                            <td class='cellInfo onlyCell'>$img2</td>";
                    }else{
                        $mTr2.="<td colspan='2' class='cellInfo onlyCell'>$img2</td>";
                    }

                    if( $key == (count($mAux)-1)){
                        $desp_totl = $mData[$fec_inicia." ".$hora]['can_despac'];
                        $regi_totl = $mData[$fec_inicia." ".$hora]['can_regist'];

                        $mtdespac += $desp_totl;
                        $mtregist += $regi_totl;
                        $usuarios = trim($usuarios, ',');

                        if($dth[$fec_inicia][$hora] >0 ){
                            $img1 = '<a style="cursor:pointer;" onclick = "detalle(1,'."'$fec_inicia $inici2'".','."'$fec_inicia " .($hora_reporte)."'".','."'$usuarios'".' , '."'$datos->tipInform'".'  )">'.($dth[$fec_inicia][$hora]+0).'</a>';
                        }else{
                            $img1 = 0;
                        }

                        if($regi_totl > 0){
                            $img2 = '<a style="cursor:pointer;" onclick = "detalle(2,'."'$fec_inicia $hora'".','."'$fec_inicia ".($hora_reporte)."'".','."'$usuarios'".' , '."'$datos->tipInform'".'  )">'.($regi_totl+0).'</a>';
                        }else{
                            $img2 = 0;
                        }
                    }

                    if ($datos->tipInform == "usr") { 
                        $mTfoot .= $key == (count($mAux)-1)? "<th class='CellHead2' style='text-align:center'>$img1</th>
                            <th class='CellHead2' style='text-align:center'>$img2</th>": "";
                    }else{
                        $mTfoot .= $key == (count($mAux)-1)? "<th colspan='2' class='CellHead2' style='text-align:center'>$img2</th>": "";
                    }

                    $inici ++;
                }

                if($mtdespac >0 ){
                    $img5 = '<a style="cursor:pointer;" onclick = "detalle(1,'."'$fec_inicia $datos->hor_inicia'".','."'".$fec_inicia ." ".($datos->hor_finali +1)."'".','."'$usuarios'".' , '."'$datos->tipInform'".'  )">'.$mtdespac.'</a>';
                }else{
                    $img5 = 0;
                }

                if($mtregist > 0){
                    $img6 = '<a style="cursor:pointer;" onclick = "detalle(2,'."'$fec_inicia $datos->hor_inicia'".','."'".$fec_inicia ." ".($datos->hor_finali +1)."'".','."'$usuarios'".' , '."'$datos->tipInform'".'  )">'.$mtregist.'</a>';
                }else{
                    $img6 = 0;
                }

                if ($datos->tipInform == "usr" ) {
                    $mTfoot .= $key == (count($mAux)-1)? "<th class='CellHead2' style='text-align:center'>$img5</th><th class='CellHead2' style='text-align:center'>$img6</th>": "";
                }else{
                    $mTfoot .= $key == (count($mAux)-1)? "<th class='CellHead2' colspan='2' style='text-align:center'>$img6</th>": "";
                }

                $mTfoot .=  $key == (count($mAux)-1)? "</tr><tr><td class='cellInfo onlyCell' colspan='".(($i*2)+4)."'>&nbsp;</td></tr>": "";

                $mTr .= $j == 0 ? "<td colspan='2' class='CellHead2'> Total</td></tr>": "";

                if ( $datos->tipInform != "usr" ) {
                    $mTr3 .= $j == 0 ? "<th colspan=\"2\" class='CellHead2'>Registros</th>": "";
                }else{
                    $mTr3 .= $j == 0 ? "<th class='CellHead2'>D. Tr&aacute;nsito</th><th class='CellHead2'>Registros</th>": "";
                }

                $img3 = "";
                $img4 = "";

                if($datos->tipInform == "usr" ){
                    if($tDespac > 0){
                        $img3 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(1,'."'$fec_inicia $inici2'".','."'$fec_inicia $final2'".','."'$value'".', '."'$datos->tipInform'".' )"> '.$tDespac.'</a>';
                    }else{
                        $img3 = 0;
                    } 

                    if($tRegist > 0){
                        $img4 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(2,'."'$fec_inicia $inici2'".','."'$fec_inicia $final2'".','."'$value'".', '."'$datos->tipInform'".' )"> '.$tRegist.'</a>';
                    }else{
                        $img4 = 0;
                    }
                }else{
                    if($tRegist > 0){
                        $img4 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(2,'."'$fec_inicia $inici2'".','."'$fec_inicia $final2'".','."'$mAux2[$key]'".', '."'$datos->tipInform'".' )"> '.$tRegist.'</a>';
                    }else{
                        $img4 = 0;
                    }
                }

                if( $datos->tipInform == "usr" ){
                    $mTr2 .= "<td class='cellInfo onlyCell'>$img3</td> 
                        <td class='cellInfo onlyCell'>$img4</td>";
                }else{
                    $mTr2 .= "<td colspan='2' class='cellInfo onlyCell'>$img4</td>";
                }
                $mTr2 .= "</tr>";

                if( $j == 0 ){
                    $mtitulo = "<tr>
                            <th class='CellHead2' colspan='".(($i*2)+4)."' style='text-align:center'> <b>$fec_inicia $datos->hor_inicia a $datos->hor_finali </b></th>
                        </tr>";
                }else{
                    $mtitulo= "";
                }

                $mHtml.= $mtitulo.$mTr3.$mTr.$mTr2.$mTfoot;
                $j++; 
            }

            $fec_inicia = strtotime ( '+1 day' , strtotime ( $fec_inicia ) ) ;
            $fec_inicia = date ( 'Y-m-d' , $fec_inicia );
        }

        $mHtml .="</table></div>";

        if(!$mData){
            $mHtml = "<div class='col-md-12 Style2DIV'>
                <b style='text-align:center !important;'>No se encontró información para los parametros de busqueda especificados.</b>
            </div>";
        }

        echo $mHtml;
    }

    /*! \fn: infoSemana
     *  \brief: Funcion para pintar el general semanal del reporte No de Novedades por usuario
     *  \author: Ing. Alexander Correa
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: $data -> arreglo con los datos a pintar
     *  \param: $datos -> rreglo con los datos del post
     *  \return 
     */
    private function infoSemana($data, $datos){
        $fec_inicia = date ( 'Y-m-d' ,strtotime($datos->fec_inicia));
        $fec_finali = date ( 'Y-m-d' ,strtotime($datos->fec_finali));
        $dia  = substr($fec_inicia,8,2);
        $mes  = substr($fec_inicia,5,2);
        $anio = substr($fec_inicia,0,4);
        $sem_inicia = date('W',  mktime(0,0,0,$mes,$dia,$anio)); 
        $dia  = substr($fec_finali,8,2);
        $mes  = substr($fec_finali,5,2);
        $anio = substr($fec_finali,0,4);
        $sem_finali = date('W',  mktime(0,0,0,$mes,$dia,$anio));

        foreach ($data as $key => $value) {
            $mData[$value["usr_creaci"]][$value["fec1"]]['can_despac'] = $value["can_despac"];
            $mData[$value["usr_creaci"]][$value["fec1"]]['can_regist'] = $value["can_regist"];
            $mData[$value["usr_creaci"]][$value["fec1"]]['cod_perfil'] = $value["cod_perfil"];
            $mData[$value["fec1"]]['can_despac'] += $value["can_despac"];
            $mData[$value["fec1"]]['can_regist'] += $value["can_regist"];

            if(!in_array($value["usr_creaci"], $mAux)){
                $mAux[] = $value["usr_creaci"];
                $mAux2[] = $value["cod_perfil"];
            }
        }
        ?>
        <div class="col-md-12 Style2DIV scroll">
            <table width="100%" cellspacing="0" cellpadding="2" border="0" id="detalle" class="table hoverTable">

            <?php
            while($sem_inicia <= $sem_finali){
                ?>
                <tr>
                    <th class='CellHead2' style='text-align:center' colspan="4">Registros de la semana No. <?= $sem_inicia ?></th>
                </tr>

                <?php 
                if( $datos->tipInform == "nov"){
                    ?>
                    <tr>
                        <th class='CellHead2' style='text-align:center'>C&oacute;digo de Novedad</th>
                        <th class='CellHead2' style='text-align:center'>Novedad</th> 
                        <th class='CellHead2' style='text-align:center'>Total de Registros</th>
                    </tr> 
                    <?php
                }else{
                    ?>
                    <tr>
                        <th class='CellHead2' style='text-align:center'>C&oacute;digo de Perfil</th>
                        <th class='CellHead2' style='text-align:center'>Usuario</th>
                        <th class='CellHead2' style='text-align:center'>Total de Despachos En tr&aacute;nsito</th>
                        <th class='CellHead2' style='text-align:center'>Total de Registros</th>
                    </tr> 
                    <?php
                }

                foreach ($mAux as $key => $value) {
                    if( $datos->tipInform == "nov"){
                        ?>
                        <tr>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= $mAux2[$key] ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= $value ?></td> 
                            <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$sem_inicia]['can_regist']+0) ?></td>
                        </tr>
                        <?php
                    }else{
                        ?>
                        <tr>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= $mAux2[$key] ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= $value ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$sem_inicia]['can_despac']+0) ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$sem_inicia]['can_regist']+0) ?></td>
                        </tr>
                        <?php
                    }
                }

                if( $datos->tipInform == "nov"){
                    ?>
                    <tr>
                        <th colspan="2" class='CellHead2' style='text-align:center'>Total</th>  
                        <th class='CellHead2' style='text-align:center'><?= $mData[$sem_inicia]['can_regist'] ?></th>
                    </tr>
                    <?php
                }else{
                    ?>
                    <tr>
                        <th colspan="2" class='CellHead2' style='text-align:center'>Total</th>
                        <th class='CellHead2' style='text-align:center'><?= $mData[$sem_inicia]['can_despac'] ?></th>
                        <th class='CellHead2' style='text-align:center'><?= $mData[$sem_inicia]['can_regist'] ?></th>
                    </tr>
                    <?php
                } 
                ?>

                <tr>
                    <th colspan="4">&nbsp;</th>
                </tr>

                <?php 
                $sem_inicia ++;
            } ?>

            </table>
        </div>
        <?php
    }

    /*! \fn: infoMes
     *  \brief: Funcion para pintar el general mensual del reporte No de Novedades por usuario
     *  \author: Ing. Alexander Correa
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: $data -> arreglo con los datos a pintar
     *  \param: $datos -> rreglo con los datos del post
     *  \return 
     */
    private function infoMes($data, $datos){
        $mAux = array();  // variable auxiliar para alacenar los usuarios
        $mAux2 = array(); // variable auxiliar para alacenar los perfiles
        $mData = array(); // variable auxiliar para ordenar los datos de la consulta

        foreach ($data as $key => $value) {
            $mData[$value["usr_creaci"]][$value["fec1"]]['can_despac'] = $value["can_despac"];
            $mData[$value["usr_creaci"]][$value["fec1"]]['can_regist'] = $value["can_regist"];
            $mData[$value["usr_creaci"]][$value["fec1"]]['cod_perfil'] = $value["cod_perfil"];
            $mData[$value["fec1"]]['can_despac'] += $value["can_despac"];
            $mData[$value["fec1"]]['can_regist'] += $value["can_regist"];

            if(!in_array($value["usr_creaci"], $mAux)){
                $mAux[] = $value["usr_creaci"];
                $mAux2[] = $value["cod_perfil"];
            }
        }
        ?>

        <div class="col-md-12 Style2DIV scroll">
            <table width="100%" cellspacing="0" cellpadding="2" border="0" id="detalle" class="table hoverTable">
                <?php
                $fec_inicia = date ( 'Y-m' ,strtotime($datos->fec_inicia));
                $fec_finali = date ( 'Y-m' ,strtotime($datos->fec_finali));
                while($fec_inicia <= $fec_finali){
                    ?>
                    <tr>
                        <th class='CellHead2' style='text-align:center' colspan="4">Registros del mes <?= $fec_inicia ?></th>
                    </tr>

                    <?php
                    if( $datos->tipInform == "nov"){ 
                        ?>
                        <tr>
                            <th class='CellHead2' style='text-align:center'>C&oacute;digo de Novedad</th>
                            <th class='CellHead2' style='text-align:center'>Novedad</th> 
                            <th class='CellHead2' colspan='2' style='text-align:center'>Total de Registros</th>
                        </tr> 
                        <?php
                    }else{
                        ?>
                        <tr>
                            <th class='CellHead2' style='text-align:center'>C&oacute;digo de Perfil</th>
                            <th class='CellHead2' style='text-align:center'>Usuario</th>
                            <th class='CellHead2' style='text-align:center'>Total de Despachos En tr&aacute;nsito</th>
                            <th class='CellHead2' style='text-align:center'>Total de Registros</th>
                        </tr> 
                        <?php
                    }

                    foreach ($mAux as $key => $value) {
                        if( $datos->tipInform == "nov"){ 
                            ?>
                            <tr>
                                <td class='cellInfo onlyCell' style='text-align:center'><?= $mAux2[$key] ?></td>
                                <td class='cellInfo onlyCell' style='text-align:center'><?= $value ?></td> 
                                <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$fec_inicia]['can_regist']+0) ?></td>
                            </tr> 
                            <?php
                        }else{
                            ?>     
                            <tr>
                                <td class='cellInfo onlyCell' style='text-align:center'><?= $mAux2[$key] ?></td>
                                <td class='cellInfo onlyCell' style='text-align:center'><?= $value ?></td>
                                <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$fec_inicia]['can_despac']+0) ?></td>
                                <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$fec_inicia]['can_regist']+0) ?></td>
                            </tr> 
                            <?php
                        } 
                    }

                    if( $datos->tipInform == "nov"){
                        ?>
                        <tr>
                            <th colspan="2" class='CellHead2' style='text-align:center'>Total</th> 
                            <th class='CellHead2' style='text-align:center'><?= $mData[$fec_inicia]['can_regist'] ?></th>
                        </tr>
                        <?php
                    }else{
                        ?>
                        <tr>
                            <th colspan="2" class='CellHead2' style='text-align:center'>Total</th>
                            <th class='CellHead2' style='text-align:center'><?= $mData[$fec_inicia]['can_despac'] ?></th>
                            <th class='CellHead2' style='text-align:center'><?= $mData[$fec_inicia]['can_regist'] ?></th>
                        </tr>
                        <?php
                    }
                    ?>

                    <tr>
                        <th colspan="4">&nbsp;</th>
                    </tr>

                    <?php 
                    $fec_inicia = strtotime ( '+1 month' , strtotime ( $fec_inicia ) ) ;
                    $fec_inicia = date ( 'Y-m' , $fec_inicia );
                } ?>
            </table>
        </div>
        <?php
    }

    /*! \fn: getDetalle
     *  \brief: Funcion para pintar el detallado del reporte No de Novedades por usuario
     *  \author: Ing. Alexander Correa
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: $data -> arreglo con los datos a pintar
     *  \param: $datos -> rreglo con los datos del post
     *  \return 
     */  
    private function getDetalle(){
        $datos = (object) $_POST;
        $datos->usuarios = str_replace(",", "','", $datos->usuarios);
        $datos->fec_inicia = $datos->fec_inicia.":00:00";
        $datos->fec_finali = $datos->fec_finali.":00:00";
        $datos->fec_finali = strtotime ( '-1 second' , strtotime ( $datos->fec_finali ) ) ;
        $datos->fec_finali = date ( 'Y-m-d H:i:s',$datos->fec_finali );
        $standa = $datos->standa;

        //---------------- filtros ----------------\\
        $where = "a.usr_creaci"; 
        $campo2 = "x.cod_perfil"; 
        //------------ fin filtros ----------------\\ 

        if($datos->tipInform == "nov"){
            $where = "c.cod_noveda"; 
            $campo2 = "x.cod_noveda AS cod_perfil";
        } 

        $sql = "SELECT x.num_despac, x.usr_creaci, $campo2, 
                       e.abr_tercer, x.fec_creaci, c.nom_noveda, 
                       x.fec1, x.obs_noveda
                 FROM (
                        (
                                SELECT a.num_despac, a.fec_creaci, a.usr_creaci, 
                                       a.obs_contro AS obs_noveda, b.cod_perfil, 
                                       a.cod_noveda, DATE_FORMAT(a.fec_creaci, '%Y-%m-%d %H') AS fec1
                                  FROM ".BASE_DATOS.".tab_despac_contro a
                            INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                            INNER JOIN ".BASE_DATOS.".tab_genera_noveda c ON a.cod_noveda = c.cod_noveda
                                 WHERE $where IN ('$datos->usuarios') 
                                   AND a.fec_creaci >= '$datos->fec_inicia' 
                                   AND a.fec_creaci < '$datos->fec_finali'
                                   AND b.cod_perfil IN ($datos->perfil)
                        )
                        UNION
                        (
                                SELECT a.num_despac, a.fec_creaci, a.usr_creaci, 
                                       a.des_noveda AS obs_noveda, b.cod_perfil, 
                                       a.cod_noveda, DATE_FORMAT(a.fec_creaci, '%Y-%m-%d %H') AS fec1
                                  FROM ".BASE_DATOS.".tab_despac_noveda a 
                            INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                            INNER JOIN ".BASE_DATOS.".tab_genera_noveda c ON a.cod_noveda = c.cod_noveda
                                 WHERE $where IN ('$datos->usuarios') 
                                   AND a.fec_creaci >= '$datos->fec_inicia' 
                                   AND a.fec_creaci < '$datos->fec_finali'
                                   AND b.cod_perfil IN ($datos->perfil)
                        )
                      ) x  
            LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c ON x.cod_noveda = c.cod_noveda 
            LEFT JOIN ".BASE_DATOS.".tab_despac_vehige d ON x.num_despac = d.num_despac 
            LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer e ON d.cod_transp = e.cod_tercer ";

        $sql .= $datos->tipo == '1' ? " GROUP BY x.usr_creaci,x.num_despac, x.fec1  " : "";
        $sql .= " ORDER BY x.usr_creaci ";
        $consulta = new Consulta($sql, self::$cConexion);
        $result= $consulta->ret_matrix("a");

        $datos = $result;
        ?>

        <div class="col-md-12 Style2DIV scroll" id="tabla2">
            <div class="col-md-12"><img src="../<?= $standa ?>/imagenes/excel.jpg"  style="cursor:pointer" onclick="exportTableExcel('detalle')"/></div>
            <div class="col-md-12"><font style="color: #000000">Se encontr&oacute; un total de <?= count($datos) ?> registros </font></div>
            <div class="col-md-12" id="registros">
                <table width="100%" cellspacing="0" cellpadding="2" border="0" id="detalle" class="table hoverTable">
                    <tr>
                        <th class='CellHead2' style='text-align:center'>Despacho</th>
                        <th class='CellHead2' style='text-align:center'>Usuario</th>
                        <th class='CellHead2' style='text-align:center'>Transportadora</th>
                        <th class='CellHead2' style='text-align:center'>Fecha</th>
                        <th class='CellHead2' style='text-align:center'>Novedad</th>
                        <th class='CellHead2' style='text-align:center'>Observaci&oacute;n</th>            
                    </tr>

                    <?php
                    foreach ($datos as $key => $value) {
                        $data = (object) $value;
                        ?>
                        <tr>
                            <td class='cellInfo onlyCell' style='text-align:center'>
                                <a href="index.php?cod_servic=3302&window=central&tie_ultnov=0&opcion=1&despac='<?= $data->num_despac ?>'">
                                    <font style="color: #000000; cursor:pointer;"><?= $data->num_despac ?></font>
                                </a>
                            </td>
                            <td  class='cellInfo onlyCell' style='text-align:center'><?= $data->usr_creaci ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= $data->abr_tercer ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= $data->fec_creaci ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= utf8_encode($data->nom_noveda) ?></td>
                            <td class='cellInfo onlyCell' style='text-align:center'><?= utf8_encode($data->obs_noveda) ?></td>
                        </tr>
                        <?php
                    } ?>
                </table>
            </div>
        </div>

        <script type="text/javascript">
            var datos = $('#registros').html();
            $('#hidden').empty();
            $('#hidden').html(datos);
        </script>
        <?php
    }

    /*! \fn: getInformeEal
     *  \brief: Funcion de transicion para pintar el general del infome de Eal
     *  \author: Ing. Alexander Correa
     *  \date: 13/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function getInformEal(){
        $datos = (object) $_POST;
        $datos->tip_despac = trim($datos->tip_despac, ",");
        $datos->cod_poseed = trim($datos->cod_poseed, ",");
        $data = $this->getDataEal($datos); //consulta los registros segun el tipo de informe

        if($datos->tipo == 1){
            $this->getEalDias($datos, $data); //para mostrar el informe por dias
        }else if($datos->tipo == 2){
            $this->getEal($datos, $data); // para mostrar el informe por EAL
        }
    }

    /*! \fn: getDataEal
     *  \brief: esta funcion trae los datos generales de la eal
     *  \author: Ing. Alexander Correa
     *  \date: 13/01/2016
     *  \date modified: dia/mes/año
     *  \param: $datos -> Objeto con los datos del formulario
     *  \param: 
     *  \return objeto con los datos de la consulta
     */  
    private function getDataEal($datos){
        #primero consulto la cantidad de despachos generados para las fechas indicadas y la transportadora
        $group = "";
        $and = "";

        if($datos->tipo == 2){
            $group = "GROUP BY x.nom_contro";
        }else{
            $group = "GROUP BY x.fec_creaci";
        }
        if($datos->num_despac){
            $and .= " AND a.num_despac = '$datos->num_despac'";
        }
        if($datos->num_manifi){
            $and .= " AND a.cod_manifi = '$datos->num_manifi'";
        }
        if($datos->num_viajex){
            $and .= " AND f.num_desext = '$datos->num_viajex'";
        }
        if($datos->tip_despac){
            $and .= " AND a.cod_tipdes IN ($datos->tip_despac)";
        }
        if($datos->tip_transp){
            $and .= " AND g.tip_transp = $datos->tip_transp";
        }
        if($datos->cod_poseed){
            $and .= " AND g.cod_poseed IN ($datos->cod_poseed)";
        }

        $sql = "SELECT count(DISTINCT x.num_despac) AS cant_despac, 
                       x.nom_contro, x.fec_creaci, x.cod_contro,
                       count(x.cod_contro) AS can_regist,
                       sum(IF(x.cod_noveda IS NOT NULL, 1, 0)) AS can_cumpli,
                       sum(IF(x.cod_noveda IS  NULL, 1, 0)) AS can_nocump
                  FROM (
                                SELECT a.num_despac, d.cod_noveda, 
                                       date_format(a.fec_creaci,'%Y-%m-%d') AS fec_creaci, c.nom_contro, c.cod_contro
                                  FROM ".BASE_DATOS.".tab_despac_despac a 
                            INNER JOIN ".BASE_DATOS.".tab_despac_seguim b ON b.num_despac = a.num_despac 
                            INNER JOIN ".BASE_DATOS.".tab_genera_contro c ON c.cod_contro = b.cod_contro  
                             LEFT JOIN ".BASE_DATOS.".tab_despac_noveda d 
                                    ON d.num_despac = a.num_despac 
                                   AND c.cod_contro = d.cod_contro 
                                   AND d.fec_creaci = (
                                                        SELECT MAX(x.fec_creaci)
                                                          FROM ".BASE_DATOS.".tab_despac_noveda x 
                                                         WHERE x.num_despac = d.num_despac
                                                           AND x.cod_contro = d.cod_contro
                                                      )
                            INNER JOIN ".BASE_DATOS.".tab_despac_vehige e ON e.num_despac = a.num_despac
                             LEFT JOIN ".BASE_DATOS.".tab_despac_sisext f ON f.num_despac = a.num_despac
                             LEFT JOIN ".BASE_DATOS.".tab_despac_corona g ON g.num_dessat = a.num_despac AND g.tip_transp IS NOT NULL
                                 WHERE a.ind_anulad = 'R' 
                                   AND c.ind_virtua = 0 $and 
                                   AND e.cod_transp = '$datos->cod_transp'
                                   AND b.ind_estado != 2
                                   AND a.fec_creaci >= '$datos->fec_inicia 00:00:00' 
                                   AND a.fec_creaci <= '$datos->fec_finali 23:59:59' 
                       ) x 
                       $group ";

                $consulta = new Consulta($sql, self::$cConexion);
                $result= $consulta->ret_matrix("a");

        if($datos->tipo == 2){
            $sql = "SELECT count(DISTINCT a.num_despac) 
                      FROM ".BASE_DATOS.".tab_despac_despac a 
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige e ON e.num_despac = a.num_despac
                INNER JOIN ".BASE_DATOS.".tab_despac_seguim b ON b.num_despac = a.num_despac 
                INNER JOIN ".BASE_DATOS.".tab_genera_contro c ON c.cod_contro = b.cod_contro  
                 LEFT JOIN ".BASE_DATOS.".tab_despac_sisext f ON f.num_despac = a.num_despac
                 LEFT JOIN ".BASE_DATOS.".tab_despac_corona g ON g.num_dessat = a.num_despac AND g.tip_transp IS NOT NULL
                     WHERE a.ind_anulad = 'R'
                       AND c.ind_virtua = 0 
                       AND a.fec_creaci BETWEEN '$datos->fec_inicia 00:00:00' AND '$datos->fec_finali 23:59:59'
                       AND e.cod_transp = '$datos->cod_transp'
                           $and
                  GROUP BY c.ind_virtua ";
            $consulta = new Consulta($sql, self::$cConexion);
            $mCant = $consulta->ret_matrix("i");
            self::$cTotalDespac = $mCant[0][0];
        }

        return $result;
    }

    /*! \fn: GetEalDias
     *  \brief: funcion para pintar el informe genera por dias  
     *  \author: Ing. Alexander Correa
     *  \date: 13/01/2016
     *  \date modified: dia/mes/año
     *  \param: $datos -> datos del formulario
     *  \param: $data -> resuntado de la consulta
     *  \return html con la informacion
     */
    private function getEalDias($datos, $data) {//para mostrar el informe por dias
        $mData = array(); // variable auxiliar para ordenar los datos de la consulta
        //ordena los datos para pintarlo de manera mas sencilla
        $tRegist = 0;
        $tcumplidas = 0;
        $tincumplidas = 0;

        foreach ($data as $key => $value) {
            $tRegist += $value['can_regist'];
            $tcumplidas += $value['can_cumpli'];
            $tincumplidas += $value['can_nocump'];
        }
        if ($data) {
            $pcumplidas = round(($tcumplidas * 100) / ($tRegist), 2);
            $pincumplidas = round(($tincumplidas * 100) / ($tRegist), 2);
        }
        ?>

        <div class="col-md-3" ></div>
        <div class="col-md-6" id="container2"></div>
        <div class="col-md-3" ></div>
        <br>
        <div id="acordeon2ID" class="col-md-12 accordion">
            <div id="contenido2">
                <div id="ch" class="Style2DIV">
                    <table width="100%" cellspacing="0" cellpadding="0">

                    <?php
                    if ($data) {
                        ?>
                            <tr>
                                <th class="CellHead" colspan="6" style="text-align:center"><b>Indicador Esferas Cumplidas Entre <?= $datos->fec_inicia ?> y <?= $datos->fec_finali ?></b></th>
                            </tr>
                            <tr class="Style2DIV">
                                <th class="CellHead" style="text-align:center"> Despachos Generados </th>
                                <th class="CellHead" style="text-align:center"> Eal Registradas </th>
                                <th class="CellHead" style="text-align:center"> Eal Cumplidas </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                <th class="CellHead" style="text-align:center"> Eal No Cumplidas </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                            </tr>
                            <tr class="Style2DIV">
                                <td class="cellInfo onlyCell" style="text-align:center" id="despachos"></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= ($tcumplidas + $tincumplidas) ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center" id="tcumplidas"><?= $tcumplidas ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $pcumplidas ?> %</td>
                                <td class="cellInfo onlyCell" style="text-align:center" id="tincumplidas"><?= $tincumplidas ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $pincumplidas ?> %</td>
                            </tr>                    
                        </table>
                        <br>          
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <th class="CellHead" colspan="7" style="text-align:center"><b>Detallado por D&iacute;as</b></th>
                            </tr>
                            <tr class="Style2DIV">
                                <th class="CellHead" style="text-align:center"> Fecha </th>
                                <th class="CellHead" style="text-align:center"> Despachos Generados </th>
                                <th class="CellHead" style="text-align:center"> Eal Registradas </th>
                                <th class="CellHead" style="text-align:center"> Eal Cumplidas </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                <th class="CellHead" style="text-align:center"> Eal No Cumplidas </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                            </tr>
                            <?php
                            $dtotal = 0;
                            foreach ($data as $key => $value) {
                                $pcumplidas = round(($value['can_cumpli'] * 100) / ($value['can_regist']), 2);
                                $pincumplidas = round(( $value['can_nocump'] * 100) / ($value['can_regist']), 2);
                                $despachos = $value['cant_despac'];

                                $dtotal += $despachos;
                                ?> 
                                <tr class="Style2DIV">
                                    <th class="cellInfo onlyCell" style="text-align:center" id="fecha<?= $key ?>"><?= $value['fec_creaci'] ?></th>
                                    <th class="cellInfo onlyCell" style="text-align:center">
                                    <?php
                                    if ($despachos > 0) {
                                        ?>
                                        <a onclick="getDetalleEal('<?= $value['fec_creaci'] ?> 00:00:00', '<?= $value['fec_creaci'] ?> 23:59:59', 0)" style="cursor:pointer;color:green"><?= $despachos ?></a>
                                        <?php
                                    } else {
                                        echo $despachos;
                                    } ?>
                                    </th>
                                    <th class="cellInfo onlyCell" style="text-align:center"><?= ($value['can_regist']) ?></th>
                                    <th class="cellInfo onlyCell" style="text-align:center" id="cumplida<?= $key ?>"><?= $value['can_cumpli'] ?> </th>
                                    <th class="cellInfo onlyCell" style="text-align:center"><?= $pcumplidas ?> % </th>
                                    <th class="cellInfo onlyCell" style="text-align:center" id="nocumplida<?= $key ?>"><?= $value['can_nocump'] ?> </th>
                                    <th class="cellInfo onlyCell" style="text-align:center"><?= $pincumplidas ?> % </th>
                                </tr>
                                <?php
                            }
                            ?>
                            <input type="hidden" name='dtotal' id='dtotal' value="<?= $dtotal ?>" >
                            <input type="hidden" name='total' id='total' value="<?= ( $key ) ?>" >
                            <input type="hidden" name='tipo' id='tipo' value="<?= $datos->tipo ?>" >
                            <?php
                    } else {
                            ?>
                            <tr>
                                <th class="cellInfo onlyCel" colspan="6" style="text-align:center">
                                    <h5>No se encontraron datos para los par&aacute;metros de consulta.</h5>
                                </th>
                            </tr>
                    <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12">&nbsp;</div> 
        <div class="col-md-12" id="container"></div>

        <script type="text/javascript">
            var dtotal = $("#dtotal").val();
            var fec_inicia = $("#fec_iniciaID").val();
            var fec_finali = $("#fec_finaliID").val();

            if (dtotal > 0) {
                $("#despachos").html("<a onClick=\"getDetalleEal(\'" + fec_inicia + " 00:00:00" + "\', \'" + fec_finali + " 23:59:59" + "\', 0)\" style='cursor: pointer; color: green' >" + dtotal + "</a>");
            } else {
                $("#despachos").append('Some text')(dtotal);
            }
        </script>
        <?php
    }

    /*! \fn: getEal
     *  \brief: para mostrar el informe por EAL
     *  \author: 
     *  \date: dd/mm/2016
     *  \date modified: dd/mm/aaaa
     *  \param: datos  
     *  \param: data  
     *  \return: 
     */
    private function getEal($datos, $data) {
        $tDespac = 0;
        $tRegist = 0;
        $tCumpli = 0;
        $tNoCump = 0;

        foreach ($data as $key => $value) {
            $tRegist += $value['can_regist'];
            $tCumpli += $value['can_cumpli'];
            $tNoCump += $value['can_nocump'];
        }

        if ($data) {
            $pcumplidas = round(($tCumpli * 100) / ($tRegist), 2);
            $pincumplidas = round(($tNoCump * 100) / ($tRegist), 2);
        }
        ?>

        <div class="col-md-3" ></div>
        <div class="col-md-6" id="container2"></div>
        <div class="col-md-3" ></div>
        <br>
        <div id="acordeon2ID" class="col-md-12 accordion">
            <div id="contenido2">
                <div id="ch" class="Style2DIV">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <?php if ($data) { ?>
                                <tr>
                                    <th class="CellHead" colspan="6" style="text-align:center">
                                        <b>Indicador Esferas Cumplidas Entre <?= $datos->fec_inicia ?> y <?= $datos->fec_finali ?></b>
                                    </th>
                                </tr>
                                <tr class="Style2DIV">
                                    <th class="CellHead" style="text-align:center"> Despachos Generados </th>
                                    <th class="CellHead" style="text-align:center"> Eal Registradas </th>
                                    <th class="CellHead" style="text-align:center"> Eal Cumplidas </th>
                                    <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                    <th class="CellHead" style="text-align:center"> Eal No Cumplidas </th>
                                    <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                </tr>
                                <tr class="Style2DIV">
                                    <td class="cellInfo onlyCell" style="text-align:center" id="despachos">
                                        <a onclick="getDetalleEal('<?= $datos->fec_inicia ?> 00:00:00', '<?= $datos->fec_finali ?> 23:59:59', 0)" style="cursor:pointer; color:green" ><?= self::$cTotalDespac ?></a>
                                    </td>
                                    <td class="cellInfo onlyCell" style="text-align:center"><?= $tRegist ?></td>
                                    <td class="cellInfo onlyCell" style="text-align:center" id="tcumplidas"><?= $tCumpli ?></td>
                                    <td class="cellInfo onlyCell" style="text-align:center"><?= $pcumplidas ?> %</td>
                                    <td class="cellInfo onlyCell" style="text-align:center" id="tincumplidas"><?= $tNoCump ?></td>
                                    <td class="cellInfo onlyCell" style="text-align:center"><?= $pincumplidas ?> %</td>
                                </tr>                    
                            </table>
                            <br>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <th class="CellHead" colspan="7" style="text-align:center"><b>Detallado por D&iacute;as</b></th>
                                </tr>
                                <tr class="Style2DIV">
                                    <th class="CellHead" style="text-align:center"> Nombre de la Eal </th>
                                    <th class="CellHead" style="text-align:center"> Eal Registradas </th>
                                    <th class="CellHead" style="text-align:center"> Eal Cumplidas </th>
                                    <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                    <th class="CellHead" style="text-align:center"> Eal No Cumplidas </th>
                                    <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                </tr>
                                <?php
                                $dtotao = 0;
                                foreach ($data as $key => $value) {
                                    $pcumplidas = round(( $value['can_cumpli'] * 100) / ($value['can_regist']), 2);
                                    $pincumplidas = round(( $value['can_nocump'] * 100) / ($value['can_regist']), 2);
                                    $dtotal += $despachos;
                                    ?> 
                                    <tr class="Style2DIV">
                                        <th class="cellInfo onlyCell" style="text-align:center" id="eal<?= $key ?>"><a onclick="getDetalleEal('<?= $datos->fec_inicia ?> 00:00:00', '<?= $datos->fec_finali ?> 23:59:59', '<?= $value['nom_contro'] ?>')" style="cursor:pointer; color:green" ><?= $value['nom_contro'] ?></a></th>
                                        <th class="cellInfo onlyCell" style="text-align:center"><?= ($value['can_regist']) ?></th>
                                        <th class="cellInfo onlyCell" style="text-align:center" id="cumplida<?= $key ?>"><?= 0 + ($value['can_cumpli']) ?> </th>
                                        <th class="cellInfo onlyCell" style="text-align:center"><?= $pcumplidas ?> % </th>
                                        <th class="cellInfo onlyCell" style="text-align:center" id="nocumplida<?= $key ?>"><?= 0 + ($value['can_nocump']) ?> </th>
                                        <th class="cellInfo onlyCell" style="text-align:center"><?= $pincumplidas ?> % </th>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <input type="hidden" name='dtotal' id='dtotal' value="<?= $dtotal ?>" >
                                <input type="hidden" name='total' id='total' value="<?= $key ?>" >
                                <input type="hidden" name='tipo' id='tipo' value="<?= $datos->tipo ?>" >
                        <?php } else { ?>
                            <tr>
                                <th class="cellInfo onlyCel" colspan="6" style="text-align:center"><h5>No se encontraron datos para los par&aacute;metros de consulta.</h5></th>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12">&nbsp;</div>
        <div class="col-md-12" id="container"></div>
        <?php
    }

    /*! \fn: getDetalleEal
     *  \brief: pinta el detalle del infomre de eal cumplidas
     *  \author: Ing. Alexander Correa
     *  \date: 20/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return html con el resultado
     */  
    private function GetDetalleEal(){
        $datos = (object) $_POST;
        $datos->eal = 0; // marco que sera por dia la consulta
        $datos->tip_despac = trim($datos->tip_despac, ",");
        $datos->cod_poseed = trim($datos->cod_poseed, ",");
        $data  =$this->getDataDetalleEal($datos);
        ?>

        <div id="acordeon2ID" class="col-md-12 accordion">
            <label><b>Despachos en Ruta</b></label>
            <div id="contenido2">
                <div id="ch" class="Style2DIV">
                    <table id="dataDetalle" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                        <th class="CellHead" colspan="18" style="text-align:center"><b>Se encontró un total de <?= count($data) ?> Registros</b>&nbsp;&nbsp;&nbsp;<a style="cursor:pointer"><img src="../<?= $_SESSION['DIR_APLICA_CENTRAL'] ?>/imagenes/excel.jpg" onclick="getExcelEal();" ></a></th>
                        </tr>
                        <tr class="Style2DIV">
                            <th class="CellHead" style="text-align:center"> Consecutivo </th>
                            <th class="CellHead" style="text-align:center"> Despacho SAT-GL </th>
                            <th class="CellHead" style="text-align:center"> Manifiesto </th>
                            <th class="CellHead" style="text-align:center"> No. Viaje </th>
                            <th class="CellHead" style="text-align:center"> Tipo Despacho </th>
                            <th class="CellHead" style="text-align:center"> Poseedor </th>
                            <th class="CellHead" style="text-align:center"> Ciudad Origen </th>
                            <th class="CellHead" style="text-align:center"> Ciudad Destino </th>
                            <th class="CellHead" style="text-align:center"> Placa </th>
                            <th class="CellHead" style="text-align:center"> Conductor </th>
                            <th class="CellHead" style="text-align:center"> Cédula </th>
                            <th class="CellHead" style="text-align:center"> Celular </th>
                            <th class="CellHead" style="text-align:center"> Fecha de Salida </th>
                            <th class="CellHead" style="text-align:center"> Fecha de llegada </th>
                            <th class="CellHead" style="text-align:center"> Transportadora </th>
                            <th class="CellHead" style="text-align:center"> No. de EAL Registradas </th>
                            <th class="CellHead" style="text-align:center"> No. de EAL Cumplidas </th>
                            <th class="CellHead" style="text-align:center"> Diferencia de las EAL </th>
                        </tr>
                        <?php
                        foreach ($data as $key => $value) {
                        $value = (object) $value;
                        ?>
                        <tr class="Style2DIV">
                            <td class="cellInfo onlyCell" style="text-align:center"><?= ($key+1) ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center">
                                <a style="cursor:pointer; color:green" target="_BLANK"  href="index.php?cod_servic=3302&window=central&despac=<?=$value->num_despac?>&opcion=1"><?= $value->num_despac ?></a>
                            </td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->cod_manifi ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->num_desext ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->nom_tipdes ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->poseedor   ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->origen     ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->destino    ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->num_placax ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->conductor  ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->cedula     ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->celular    ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->fec_salida ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->fec_llegad ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->transporta ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->can_regist ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->can_cumpli ?></td>
                            <td class="cellInfo onlyCell" style="text-align:center"><?= $value->can_incump ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /*! \fn: getDataDetalleEal
     *  \brief: 
     *  \author: 
     *  \date: dd/mm/2016
     *  \date modified: dd/mm/aaaa
     *  \param: datos
     *  \return: 
     */
    private function getDataDetalleEal($datos){
        $and = "";
        if($datos->tipo){
            $and .= " AND l.nom_contro LIKE '$datos->tipo' ";
        }
        if($datos->num_despac){
            $and .= " AND a.num_despac = '$datos->num_despac'";
        }
        if($datos->num_manifi){
            $and .= " AND a.cod_manifi = '$datos->num_manifi'";
        }
        if($datos->num_viajex){
            $and .= " AND b.num_desext = '$datos->num_viajex'";
        }
        if($datos->tip_despac){
            $and .= " AND a.cod_tipdes IN ($datos->tip_despac)";
        }
        if($datos->tip_transp){
            $and .= " AND n.tip_transp = '$datos->tip_transp'";
        }
        if($datos->cod_poseed){
            $and .= " AND n.cod_poseed IN ($datos->cod_poseed)";
        }
        if($datos->cod_transp == "860068121"){
            $adicional = " n.nom_poseed ";
        }else{
            $adicional = " f.abr_tercer ";
        }

        $sql = "SELECT a.num_despac, a.cod_manifi, b.num_desext, 
                       c.nom_tipdes, $adicional AS poseedor, g.nom_ciudad AS origen,
                       h.nom_ciudad AS destino, e.num_placax, i.abr_tercer AS conductor,
                       i.cod_tercer AS cedula, i.num_telmov AS celular, a.fec_salida,
                       a.fec_llegad, j.abr_tercer AS transporta, count(l.cod_contro) AS can_regist,
                       sum(IF(m.cod_noveda IS NOT NULL, 1, 0) ) AS can_cumpli,
                       sum(IF(m.cod_noveda IS NULL, 1, 0) ) AS can_incump
                  FROM ".BASE_DATOS.".tab_despac_despac a 
            LEFT  JOIN ".BASE_DATOS.".tab_despac_sisext b ON b.num_despac = a.num_despac               
            LEFT  JOIN ".BASE_DATOS.".tab_genera_tipdes c ON c.cod_tipdes = a.cod_tipdes 
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige d ON d.num_despac = a.num_despac 
            LEFT  JOIN ".BASE_DATOS.".tab_vehicu_vehicu e ON e.num_placax = d.num_placax 
            LEFT  JOIN ".BASE_DATOS.".tab_tercer_tercer f ON e.cod_tenedo = f.cod_tercer 
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g ON g.cod_ciudad = a.cod_ciuori 
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h ON h.cod_ciudad = a.cod_ciudes 
            LEFT  JOIN ".BASE_DATOS.".tab_tercer_tercer i ON i.cod_tercer = e.cod_conduc
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer j ON j.cod_tercer = d.cod_transp 
            INNER JOIN ".BASE_DATOS.".tab_despac_seguim k ON k.num_despac = a.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_genera_contro l ON l.cod_contro = k.cod_contro 
            LEFT  JOIN ".BASE_DATOS.".tab_despac_noveda m 
                    ON m.num_despac = a.num_despac 
                   AND l.cod_contro = m.cod_contro 
                   AND m.fec_creaci = ( 
                                        SELECT MAX(x.fec_creaci) 
                                          FROM ".BASE_DATOS.".tab_despac_noveda x 
                                         WHERE x.num_despac = m.num_despac 
                                           AND x.cod_contro = m.cod_contro
                                      )
            LEFT  JOIN ".BASE_DATOS.".tab_despac_corona n ON n.num_dessat = a.num_despac
                 WHERE a.ind_anulad = 'R'
                   AND a.fec_creaci BETWEEN '$datos->fec_inicia' AND '$datos->fec_finali' 
                   AND n.tip_transp IS NOT NULL 
                   AND l.ind_virtua = 0
                   AND d.cod_transp = '$datos->cod_transp' 
                   AND k.ind_estado != 2
                       $and 
              GROUP BY a.num_despac";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /*! \fn: getInformIndSaldes
     *  \brief: muestra el general del informe de salida de despachos
     *  \author: Ing. Alexander Correa
     *  \date: 21/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return html com los datos ordemandos de la consulta
     */
    private function getInformIndSaldes() {
        $datos = (object) $_POST;
        $datos->cod_ciuori = trim($datos->cod_ciuori, ",");
        $data = $this->getDataInd($datos);

        $tDespac = 0;
        $tCumpli = 0;

        foreach ($data as $key => $value) {
            $tDespac += $value['total'];
            $tCumpli += $value['cumplidos'];
        }
        $tIncump = $tDespac - $tCumpli;
        if ($data) {
            $pcumplid = round(($tCumpli * 100) / ($tDespac), 2);
            $pincumpl = round(($tIncump * 100) / ($tDespac), 2);
        }
        ?>
        <div class="col-md-3" ></div>
        <div class="col-md-6" id="container2"></div>
        <div class="col-md-3" ></div>
        <br>
        <div id="acordeon2ID" class="col-md-12 accordion">
            <div id="contenido2">
                <div id="ch" class="Style2DIV">
                    <table width="100%" cellspacing="0" cellpadding="0">
                    <?php if ($data) { ?>
                            <tr>
                                <th class="CellHead" colspan="5" style="text-align:center"><b>Indicador de Salida de Despachos Comprendido Entre <?= $datos->fec_inicia ?> y <?= $datos->fec_finali ?></b></th>
                            </tr>
                            <tr class="Style2DIV">
                                <th class="CellHead" style="text-align:center"> Despachos Generados </th>
                                <th class="CellHead" style="text-align:center"> Despachos con el Tiempo Acordado </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                <th class="CellHead" style="text-align:center"> Despachos Fuera del Tiempo Acordado </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                            </tr>
                            <tr class="Style2DIV">
                                <td class="cellInfo onlyCell" style="text-align:center" ><a onclick="getDetalleSalDes('<?= $datos->fec_inicia ?> 00:00:00', '<?= $datos->fec_finali ?> 23:59:59', 0)" style="cursor:pointer; color:green" ><?= $tDespac ?></a></td>
                                <td class="cellInfo onlyCell" style="text-align:center" id="tcumplidas"> <a onclick="getDetalleSalDes('<?= $datos->fec_inicia ?> 00:00:00', '<?= $datos->fec_finali ?> 23:59:59', 1)" style="cursor:pointer; color:green" ><?= $tCumpli ?></a></td>
                                <td class="cellInfo onlyCell" style="text-align:center" ><?= $pcumplid ?>%</td>
                                <td class="cellInfo onlyCell" style="text-align:center" id="tincumplidas"><a onclick="getDetalleSalDes('<?= $datos->fec_inicia ?> 00:00:00', '<?= $datos->fec_finali ?> 23:59:59', 2)" style="cursor:pointer; color:green" ><?= $tIncump ?></a> </td>
                                <td class="cellInfo onlyCell" style="text-align:center" ><?= $pincumpl ?> %</td>
                            </tr>                    
                        </table>
                        <br>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <th class="CellHead" colspan="7" style="text-align:center"><b>Detallado por D&iacute;as</b></th>
                            </tr>
                            <tr class="Style2DIV">
                                <th class="CellHead" style="text-align:center"> Fecha </th>
                                <th class="CellHead" style="text-align:center"> Despachos Generados </th>
                                <th class="CellHead" style="text-align:center"> Despachos con el Tiempo Acordado </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                                <th class="CellHead" style="text-align:center"> Despachos Fuera del Tiempo Acordado </th>
                                <th class="CellHead" style="text-align:center"> Porcentaje </th>
                            </tr>
                            <?php
                            $dtotao = 0;
                            foreach ($data as $key => $value) {
                                $pcumplid = round(( $value['cumplidos'] * 100) / ($value['total']), 2);
                                $pincumpl = round(( ($value['total'] - $value['cumplidos']) * 100) / ($value['total']), 2);
                                ?> 
                                <tr class="Style2DIV">
                                    <th class="cellInfo onlyCell" style="text-align:center" id="fecha<?= $key ?>"><?= $value['fec_creaci'] ?></th>
                                    <th class="cellInfo onlyCell" style="text-align:center"><a onclick="getDetalleSalDes('<?= $value['fec_creaci'] ?> 00:00:00', '<?= $value['fec_creaci'] ?> 23:59:59', 0)" style="cursor:pointer; color:green" ><?= ($value['total']) ?></a></th>
                                    <th class="cellInfo onlyCell" style="text-align:center" id="cumplida<?= $key ?>"><a onclick="getDetalleSalDes('<?= $value['fec_creaci'] ?> 00:00:00', '<?= $value['fec_creaci'] ?> 23:59:59', 1)" style="cursor:pointer; color:green" ><?= 0 + ($value['cumplidos']) ?></a></th>
                                    <th class="cellInfo onlyCell" style="text-align:center"><?= $pcumplid ?> % </th>
                                    <th class="cellInfo onlyCell" style="text-align:center" id="nocumplida<?= $key ?>"><a onclick="getDetalleSalDes('<?= $value['fec_creaci'] ?> 00:00:00', '<?= $value['fec_creaci'] ?> 23:59:59', 2)" style="cursor:pointer; color:green" ><?= 0 + (($value['total'] - $value['cumplidos'])) ?></a> </th>
                                    <th class="cellInfo onlyCell" style="text-align:center"><?= $pincumpl ?> % </th>
                                </tr>
                                <?php
                            }
                            ?>
                            <input type="hidden" name='dtotal' id='dtotal' value="<?= $dtotal ?>" >
                            <input type="hidden" name='total' id='total' value="<?= $key ?>" >
                            <input type="hidden" name='tipo' id='tipo' value="1" >
                    <?php } else { ?>
                            <tr>
                                <th class="cellInfo onlyCel" colspan="6" style="text-align:center"><h5>No se encontraron datos para los par&aacute;metros de consulta.</h5></th>
                            </tr>
                    <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12">&nbsp;</div> 
        <div class="col-md-12" id="container"></div>
        <?php
    }

    /*! \fn: getDataInd
     *  \brief: Funcion que estrae los datos para el informe general de indicador de salia de despachos
     *  \author: Ing. Alexander Correa
     *  \date: 21/01/2016
     *  \date modified: dia/mes/año
     *  \param: $datos -> objeto con los parametros de consulta
     *  \param: 
     *  \return array con el resultado de la consulta
     */
    private function getDataInd($datos){
        $datos->cod_transp = trim($datos->cod_transp);
        $tipSer = getTransTipser( self::$cConexion, " AND a.cod_transp = '$datos->cod_transp'" );

        $and = "";
        if($datos->num_despac){
            $and .= " AND a.num_despac = '$datos->num_despac'";
        }
        if($datos->num_manifi){
            $and .= " AND a.cod_manifi = '$datos->num_manifi'";
        }
        if($datos->num_viajex){
            $and .= " AND b.num_desext = '$datos->num_viajex'";
        }
        if($datos->tip_despac){
            $and .= " AND a.cod_tipdes = '$datos->tip_despac'";
        }
        if($datos->tip_transp){
            $and .= " AND e.tip_transp = $datos->tip_transp ";
        }
        if($datos->cod_ciuori){
            $and .= " AND a.cod_ciuori IN ($datos->cod_ciuori)";
        }

        $sql = "SELECT SUM(
                            IF(
                                (
                                    TIMESTAMPDIFF(MINUTE, a.fec_creaci, CONCAT(a.fec_citcar, ' ', a.hor_citcar)) >= 
                                    (
                                        CASE a.cod_tipdes when 1 then ".$tipSer[0]['tie_carurb']."
                                            when 2 then ".$tipSer[0]['tie_carnac']."
                                            when 3 then ".$tipSer[0]['tie_carimp']."
                                            when 4 then ".$tipSer[0]['tie_carexp']."
                                            when 5 then ".$tipSer[0]['tie_cartr1']."
                                            when 6 then ".$tipSer[0]['tie_cartr2']."
                                        END
                                    )
                                ), 1, 0
                            )
                       ) cumplidos, count(a.num_despac) total, DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') fec_creaci 
                  FROM ".BASE_DATOS.".tab_despac_despac a 
            INNER JOIN ".BASE_DATOS.".tab_despac_sisext b ON b.num_despac = a.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige c ON c.num_despac = a.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer d ON d.cod_tercer = c.cod_transp
             LEFT JOIN ".BASE_DATOS.".tab_despac_corona e ON e.num_dessat = a.num_despac
                 WHERE a.fec_creaci BETWEEN '$datos->fec_inicia 00:00:00' AND '$datos->fec_finali 23:59:59'
                   AND c.cod_transp = '$datos->cod_transp' 
                   AND e.tip_transp IS NOT NULL
                   AND a.fec_citcar IS NOT NULL AND a.fec_salsis IS NOT NULL
                       $and 
              GROUP BY fec_creaci";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /*! \fn: getDetalleSalDes
     *  \brief: Trae el detalle del informe del indicador de salida de despachos
     *  \author: Ing. Alexander Correa
     *  \date: 22/01/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return html con los datos de la consulta
     */
    private function getDetalleSalDes() {
        $datos = (object) $_POST;
        $datos->cod_ciuori = trim($datos->cod_ciuori, ",");
        $data = $this->getDataSalDes($datos);
        ?>
        <div id="acordeon2ID" class="col-md-12 accordion">
            <label><b>Despachos en Ruta</b></label>
            <div id="contenido2">
                <div id="ch" class="Style2DIV">
                    <table id="dataDetalle" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <th class="CellHead" colspan="18" style="text-align:center">
                                <b>Se encontró un total de <?= count($data) ?> Registros</b>&nbsp;&nbsp;&nbsp;
                                <a style="cursor:pointer">
                                    <img src="../<?= $_SESSION['DIR_APLICA_CENTRAL'] ?>/imagenes/excel.jpg" onclick="getExcelSalDes();" >
                                </a>
                            </th>
                        </tr>
                        <tr class="Style2DIV">
                            <th class="CellHead" style="text-align:center"> Consecutivo </th>
                            <th class="CellHead" style="text-align:center"> Despacho SAT-GL </th>
                            <th class="CellHead" style="text-align:center"> Manifiesto </th>
                            <th class="CellHead" style="text-align:center"> No. Viaje </th>
                            <th class="CellHead" style="text-align:center"> Tipo Despacho </th>
                            <th class="CellHead" style="text-align:center"> Poseedor </th>
                            <th class="CellHead" style="text-align:center"> Ciudad Origen </th>
                            <th class="CellHead" style="text-align:center"> Ciudad Destino </th>
                            <th class="CellHead" style="text-align:center"> Placa </th>
                            <th class="CellHead" style="text-align:center"> Conductor </th>
                            <th class="CellHead" style="text-align:center"> Cédula </th>
                            <th class="CellHead" style="text-align:center"> Celular </th>
                            <th class="CellHead" style="text-align:center"> Fecha de Creación del Despacho </th>
                            <th class="CellHead" style="text-align:center"> Fecha de Cita de Cargue </th>
                            <th class="CellHead" style="text-align:center"> Cumplimiento  </th>
                            <th class="CellHead" style="text-align:center"> Diferencia De Tiempo  </th>
                        </tr>
                        <?php
                        foreach ($data as $key => $value) {
                            $value = (object) $value;
                            ?>
                            <tr class="Style2DIV">
                                <td class="cellInfo onlyCell" style="text-align:center"><?= ($key + 1) ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center">
                                    <a style="cursor:pointer; color:green" target="_BLANK" href="index.php?cod_servic=3302&window=central&despac=<?= $value->num_despac ?>&opcion=1"><?= $value->num_despac ?></a>
                                </td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->cod_manifi ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->num_desext ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->nom_tipdes ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->poseedor ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->origen ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->destino ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->num_placax ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->conductor ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->cedula ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->celular ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->fec_creaci ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->fec_citcar ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->ind_cumpli ?></td>
                                <td class="cellInfo onlyCell" style="text-align:center"><?= $value->diferencia ?>(min)</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /*! \fn: getDataSalDes
     *  \brief: funcion que extrae los datos para pintar en el detallado
     *  \author: Ing. Alexander Correa
     *  \date: 22/01/2016
     *  \date modified: dia/mes/año
     *  \param: $datos -> objeto con los datos del post
     *  \param: 
     *  \return arreglo con los datos de la consulta
     */
    private function getDataSalDes($datos){
        $tipSer = getTransTipser( self::$cConexion, $mSqlWhere = "AND a.cod_transp = '$datos->cod_transp'" );
        $and = "";

        if($datos->num_despac){
            $and .= " AND a.num_despac = '$datos->num_despac'";
        }
        if($datos->num_manifi){
            $and .= " AND a.cod_manifi = '$datos->num_manifi'";
        }
        if($datos->num_viajex){
            $and .= " AND b.num_desext = '$datos->num_viajex'";
        }
        if($datos->tip_despac){
            $and .= " AND a.cod_tipdes = '$datos->tip_despac'";
        }
        if($datos->tip_transp){
            $and .= " AND n.tip_transp = $datos->tip_transp ";
        }
        if($datos->cod_ciuori){
            $and .= " AND a.cod_ciuori IN ($datos->cod_ciuori)";
        }

        if($datos->indicador == 1){
            $WHERE .= " WHERE x.ind_cumpli = 'Cumplió'";
        }else if($datos->indicador == 2){
            $WHERE .= " WHERE x.ind_cumpli = 'Incumplió'";
        }

        $sql = "SELECT x.num_despac, x.cod_manifi, x.num_desext, x.nom_tipdes, x.poseedor, x.origen, x.destino,
                       x.num_placax, x.conductor, x.cedula, x.celular, x.fec_salida, x.fec_llegad, x.transporta,
                       x.diferencia, x.fec_creaci, x.fec_citcar, x.ind_cumpli 
                  FROM (
                                SELECT a.num_despac, a.cod_manifi, b.num_desext, 
                                       c.nom_tipdes, f.abr_tercer AS poseedor, g.nom_ciudad AS origen,
                                       h.nom_ciudad AS destino, e.num_placax, i.abr_tercer AS conductor,
                                       i.cod_tercer AS cedula, i.num_telmov AS celular, a.fec_salida,
                                       a.fec_llegad, j.abr_tercer AS transporta,
                                       TIMESTAMPDIFF(MINUTE, a.fec_creaci, CONCAT(a.fec_citcar, ' ', a.hor_citcar)) AS diferencia, 
                                       a.fec_creaci, CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS fec_citcar,
                                       IF(
                                            (
                                                TIMESTAMPDIFF(MINUTE, a.fec_creaci, CONCAT(a.fec_citcar, ' ', a.hor_citcar)) >= 
                                                (
                                                   CASE a.cod_tipdes when 1 then ".$tipSer[0]['tie_carurb']."
                                                       when 2 then ".$tipSer[0]['tie_carnac']."
                                                       when 3 then ".$tipSer[0]['tie_carimp']."
                                                       when 4 then ".$tipSer[0]['tie_carexp']."
                                                       when 5 then ".$tipSer[0]['tie_cartr1']."
                                                       when 6 then ".$tipSer[0]['tie_cartr2']."
                                                   END
                                                )
                                            ), 'Cumplió', 'Incumplió'
                                       ) AS ind_cumpli
                                  FROM ".BASE_DATOS.".tab_despac_despac a 
                            INNER JOIN ".BASE_DATOS.".tab_despac_sisext b ON b.num_despac = a.num_despac 
                            LEFT  JOIN ".BASE_DATOS.".tab_genera_tipdes c ON c.cod_tipdes = a.cod_tipdes 
                            INNER JOIN ".BASE_DATOS.".tab_despac_vehige d ON d.num_despac = a.num_despac 
                            LEFT  JOIN ".BASE_DATOS.".tab_vehicu_vehicu e ON e.num_placax = d.num_placax 
                            LEFT  JOIN ".BASE_DATOS.".tab_tercer_tercer f ON e.cod_tenedo = f.cod_tercer 
                            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g ON g.cod_ciudad = a.cod_ciuori 
                            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h ON h.cod_ciudad = a.cod_ciudes 
                            LEFT  JOIN ".BASE_DATOS.".tab_tercer_tercer i ON i.cod_tercer = e.cod_conduc
                            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer j ON j.cod_tercer = d.cod_transp 
                            INNER JOIN ".BASE_DATOS.".tab_despac_seguim k ON k.num_despac = a.num_despac 
                            INNER JOIN ".BASE_DATOS.".tab_genera_contro l ON l.cod_contro = k.cod_contro 
                            LEFT  JOIN ".BASE_DATOS.".tab_despac_noveda m ON m.num_despac = a.num_despac AND l.cod_contro = m.cod_contro 
                            LEFT  JOIN ".BASE_DATOS.".tab_despac_corona n ON n.num_dessat = a.num_despac
                                 WHERE a.fec_creaci BETWEEN '$datos->fec_inicia' AND '$datos->fec_finali'
                                   AND d.cod_transp = '$datos->cod_transp' 
                                   AND k.ind_estado != 2 
                                   AND n.tip_transp IS NOT NULL 
                                   AND a.fec_citcar IS NOT NULL
                                   AND a.fec_salsis IS NOT NULL
                                       $and 
                              GROUP BY a.num_despac
                       ) x 
                       $WHERE ";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /*! \fn: getTipoTransporte  
     *  \brief: extrae los tipos de transprte de la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 02/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return arreglo con los tipos de transporte
     */
    public function getTipoTransporte(){
        $sql ="SELECT cod_tiptra, nom_tiptra FROM ".BASE_DATOS.".tab_genera_tiptra WHERE ind_estado = 1";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a"); 
    }

    /*! \fn: getPoseedores
     *  \brief: devuelve la lista de los poseedores de una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 02/02/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return multi select con los poseedores
     */
    private function getPoseedores(){
        $datos = (object) $_POST;
        $sql = "SELECT a.cod_poseed, a.nom_poseed 
                  FROM ".BASE_DATOS.".tab_despac_corona a 
            INNER JOIN ".BASE_DATOS.".tab_tercer_activi b ON b.cod_tercer = a.cod_poseed
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige c ON c.num_despac = a.num_dessat 
                 WHERE b.cod_activi = 1
                   AND c.cod_transp = '$datos->cod_transp'
              GROUP BY cod_poseed ";
        $consulta = new Consulta($sql, self::$cConexion);
        $data =  $consulta->ret_matrix("a"); 

        ?>
        <select id="cod_poseedID" name="cod_poseed" class="multi">
            <option value="">Todos</option>
            <?php 
            foreach ($data as $key => $value) { ?>
                <option value="<?= $value['cod_poseed'] ?>"><?= $value['nom_poseed'] ?></option>
                <?php
            }
            ?>
        </select>
        <?php
    }

    /*! \fn: getOrigenes
     *  \brief: 
     *  \author: 
     *  \date: dd/mm/2016
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function getOrigenes(){
        $datos = (object) $_POST;

        $sql = "SELECT a.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),')') abr_ciudad
                  FROM ".BASE_DATOS.".tab_transp_origen a,
                       ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                 WHERE a.cod_ciudad = b.cod_ciudad 
                   AND b.cod_depart = d.cod_depart 
                   AND b.cod_paisxx = d.cod_paisxx 
                   AND d.cod_paisxx = e.cod_paisxx 
                   AND b.ind_estado = '1' 
                   AND a.cod_transp = '$datos->cod_transp'
              GROUP BY 1";
        $consulta = new Consulta($sql, self::$cConexion);
        $data =  $consulta->ret_matrix("a"); 

        ?>
        <select id="cod_ciuoriID" name="cod_ciuori" class="multi">
            <option value="">Todas</option>
            <?php 
            foreach ($data as $key => $value) { ?>
                <option value="<?= $value['cod_ciudad'] ?>"><?= $value['abr_ciudad'] ?></option>
                <?php
            }
            ?>
        </select>
        <?php
    }

    /*! \fn: getModalidades
     *  \brief: funcion para pintar las mosalidades de los despachos
     *  \author: Ing. Alexander Correa
     *  \date: 03/03/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */
    public function getModalidades(){
        $sql = "SELECT cod_tipdes, nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes ";
        $consulta = new Consulta($sql, self::$cConexion);
        return  $consulta->ret_matrix("a");
    }

    /*! \fn: GetInformAsignaCargax
     *  \brief: trae los datos generales de el informe de asinacion decarga
     *  \author: Ing. Alexander Correa
     *  \date: 02/03/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return 
     */
    private function GetInformAsignaCargax() {
        $datos = (object) $_POST;
        $data = $this->getDatosAsignaCarga($datos->fec_inicia, $datos->fec_finali);
        ?>

        <div class="col-md-12 text-center Style2DIV">
            <div class="col-md-12 CellHead">GENERAL OPERACIÓN GENERADORES DE CARGA</div>
            <div class="col-md-12"></div>
            <div class="col-md-12 CellHead">
                <div class="col-md-6">
                    <div class="col-md-4">DESPACHOS GENERADOS</div>
                    <div class="col-md-4">FINALIZADOS</div>
                    <div class="col-md-4">PORCENTAJE</div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-3">EN TRÁNSITO</div>
                    <div class="col-md-3">PORCENTAJE</div>
                    <div class="col-md-3">PENDIENTES POR LLEGADA</div>
                    <div class="col-md-3">PORCENTAJE</div>
                </div>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-12 contenido">
                <div class="col-md-6">
                    <div class="col-md-4" id="genera"></div>
                    <div class="col-md-4" id="finali"></div>
                    <div class="col-md-4" id="pfinal"></div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-3" id="transi"></div>
                    <div class="col-md-3" id="ptrans"></div>
                    <div class="col-md-3" id="pendie"></div>
                    <div class="col-md-3" id="ppendi"></div>
                </div>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-12 CellHead">DETALLADO POR DÍAS</div>
            <div class="col-md-12"></div>
            <div class="col-md-6 CellHead">
                <div class="col-md-3">FECHA</div>
                <div class="col-md-3">DESPACHOS GENERADOS</div>
                <div class="col-md-3">FINALIZADOS</div>
                <div class="col-md-3">PORCENTAJE</div>
            </div>
            <div class="col-md-6 CellHead">
                <div class="col-md-3">EN TRÁNSITO</div>
                <div class="col-md-3">PORCENTAJE</div>
                <div class="col-md-3">PENDIENTES POR LLEGADA</div>
                <div class="col-md-3">PORCENTAJE</div>
            </div>

            <?php
            $tgenera = 0;
            $tfinali = 0;
            $ttransi = 0;
            $tpendie = 0;
            foreach ($data as $key => $value) {
                $finali = 0 + $value['finalizados'];
                $transi = 0 + $value['transito'];
                $pendie = 0 + $value['pendientes'];
                $genera = $finali + $transi + $pendie;
                $tgenera += $genera;
                $tfinali += $finali;
                $ttransi += $transi;
                $tpendie += $pendie;
                $ptransi = round(($transi * 100) / $genera, 1);
                $pfinali = round(($finali * 100) / $genera, 1);
                $ppendie = round(($pendie * 100) / $genera, 1);
                ?>
                <div class="col-md-6 contenido">
                    <div class="col-md-3"><?= $value['fecha'] ?></div>
                    <div class="col-md-3"><?= $genera ?></div>
                    <div class="col-md-3"><?= $finali ?></div>
                    <div class="col-md-3"><?= $pfinali ?> %</div>
                </div>
                <div class="col-md-6 contenido">
                    <div class="col-md-3"><?= $transi ?></div>
                    <div class="col-md-3"><?= $ptransi ?> %</div>
                    <div class="col-md-3"><?= $pendie ?></div>
                    <div class="col-md-3"><?= $ppendie ?> %</div>
                </div>
                <div class="col-md-12 borde-inferior"></div>
                <?php
            }
            ?>
        </div>

        <input type="hidden" id="generados" value="<?= $tgenera ?>">
        <input type="hidden" id="finalizados" value="<?= $tfinali ?>">
        <input type="hidden" id="transito" value="<?= $ttransi ?>">
        <input type="hidden" id="pendientes" value="<?= $tpendie ?>">
        <?php
    }

    /* ! \fn: getDatosAsignaCarga
     *  \brief: trae los datos de la carga asignada a los usuarios para la funcion GetInformAsignaCargax
     *  \author: Ing. Alexander Correa
     *  \date: 03/03/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return $datos->arreglo con los datos de la consulta
     */
    private function getDatosAsignaCarga($fec_inicia, $fec_finali, $modalidad = null) {
        $and = " AND DATE_FORMAT(a.fec_despac,'%Y-%m-%d') BETWEEN '$fec_inicia' AND '$fec_finali'";

        if ($modalidad) {
            $and .= " AND a.cod_tipdes = $modalidad ";
        }

        $sql = "SELECT count(x.ind_llegada) cantidad, x.ind_llegada, x.fecha 
                  FROM (
                                SELECT IF( a.fec_llegad IS NOT NULL AND a.fec_llegad != '0000-00-00 00:00:00', 
                                           'Finalizados', 
                                           IF(c.cod_contro IS NULL, 'En Transito', 'Pendientes por Llegada')
                                        ) ind_llegada, 
                                        DATE_FORMAT(a.fec_despac,'%Y-%m-%d') fecha  
                                  FROM " . BASE_DATOS . ".tab_despac_despac a
                            INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac 
                             LEFT JOIN " . BASE_DATOS . ".tab_despac_noveda c ON c.num_despac = a.num_despac AND c.cod_contro = 9999
                                 WHERE a.fec_salida IS NOT NULL
                                   AND a.fec_salida <= NOW()
                                   AND a.ind_planru = 'S'
                                   AND a.ind_anulad = 'R' 
                                       $and 
                                   AND b.ind_activo = 'S'
                       ) x
              GROUP BY x.fecha, x.ind_llegada ";
        $consulta = new Consulta($sql, self::$cConexion);
        $data = $consulta->ret_matrix("a");
        $datos = array();

        foreach ($data as $key => $value) {
            $datos[$value['fecha']]['fecha'] = $value['fecha'];

            if ($value['ind_llegada'] == 'Finalizados') {
                $datos[$value['fecha']]['finalizados'] += $value['cantidad'];
            } else if ($value['ind_llegada'] == 'En Transito') {
                $datos[$value['fecha']]['transito'] += $value['cantidad'];
            } else {
                $datos[$value['fecha']]['pendientes'] += $value['cantidad'];
            }
        }

        return $datos;
    }

    /*! \fn: getDetalleCargax
     *  \brief: funcion para mostrar el detallado del informe de de distribucion de carga laboral
     *  \author: Ing. Alexander Correa
     *  \date: 07/03/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */
    private function getDetalleCargax() {
        $datos = (object) $_POST;
        $data = $this->getDataDetalleCarga();
        ?>
        <div class="col-md-12 text-center Style2DIV">

            <?php
            foreach ($data as $key => $value) {
                $x = 1;
                ?>          
                <table  width="100%" class="text-center" cellspacing="1" >
                    <tr class="CellHead">
                        <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;" colspan="<?= 4 + (count($this->cHoras) * 3) ?>">DETALLADO DE DISTRIBUCIÓN DE CARGA DEL DÍA <?= $key ?></td>
                    </tr>
                    <tr class="CellHead">
                        <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;" colspan="4">DATOS BASICOS</td>
                        <?php foreach ($this->cHoras as $k => $val) { ?>
                            <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;" colspan="3"><?= $val ?>:00</td>
                        <?php } ?>
                    </tr>
                    <tr class="CellHead">
                        <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">CONSECUTIVO</td>
                        <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">TRANSPORTADORA</td>
                        <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">GRUPO</td>
                        <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">PRIORIDAD</td>
                        <?php foreach ($this->cHoras as $k => $val) { ?>
                            <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">DESPACHOS EN TRANSITO</td>
                            <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">CONTROLADOR ASIGNADO</td>
                            <td height="30px" style="color: #FFFFFF; border: #FFFFFF 1px solid;">SUPERVISOR QUE GENERÓ</td>
                        <?php } ?>
                    </tr>

                    <?php foreach ($value as $k => $v) { ?>
                        <tr class="Style2DIV">
                            <td class="contenido" height="20px"><?= $x ?></td>
                            <td class="contenido" height="20px" align="LEFT"><?= $v['nom_transp'] ?></td>
                            <td class="contenido" height="20px"><?= $v['nom_grupox'] ?></td>
                            <td class="contenido" height="20px"><?= $v['cod_priori'] ?></td>
                            <?php foreach ($this->cHoras as $key => $va) { ?>
                                <td class="contenido" height="20px"><?= $v['data'][$va]['desp'] ?></td>
                                <td class="contenido" height="20px"><?= trim($v['data'][$va]['cont'], " , ") ?></td>
                                <td  class="contenido"height="20px"><?= $v['data'][$va]['supe'] ?></td>
                            <?php } ?>
                        </tr>
                        <?php
                        $x ++;
                    }
                    ?>
                </table>
        <?php } ?>
        </div>
        <?php
    }

    /* ! \fn: getDataDetalleCarga
     *  \brief: extrae de la base de datos la informacion para getDetalleCargax     
     *  \author: Ing. Alexander Correa
     *  \date: 07/03/2016
     *  \date modified: dia/mes/año
     *  \param:    
     *  \return arreglo con los datos ordenados
     */
    private function getDataDetalleCarga() {
        $datos = (object) $_POST;

        $sql = "SELECT c.abr_tercer, a.can_despac, d.cod_priori, f.nom_grupox, b.fec_inicia, 
                       b.fec_creaci, DATE_FORMAT(a.fec_creaci, '%H') hor_calcul, b.cod_usuari,
                       DATE_FORMAT(b.fec_inicia, '%Y-%m-%d') fec_inici2, a.usr_creaci, c.cod_tercer 
                  FROM " . BASE_DATOS . ".tab_monito_detall a 
            INNER JOIN " . BASE_DATOS . ".tab_monito_encabe b ON b.cod_consec = a.cod_consec 
            INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c ON c.cod_tercer = a.cod_tercer 
            INNER JOIN " . BASE_DATOS . ".tab_transp_tipser d ON d.cod_transp = a.cod_tercer 
            INNER JOIN " . BASE_DATOS . ".tab_callce_grupox f ON f.cod_grupox = d.cod_grupox 
                 WHERE DATE_FORMAT(b.fec_inicia, '%Y-%m-%d') >= '$datos->fec_inicia' 
                   AND DATE_FORMAT(b.fec_inicia, '%Y-%m-%d') <= '$datos->fec_finali' 
              GROUP BY b.cod_usuari, hor_calcul, c.abr_tercer  
              ORDER BY fec_inici2 ASC";
        $consulta = new Consulta($sql, self::$cConexion);
        $data = $consulta->ret_matrix("a");

        $datos = array();
        $horas = array();
        foreach ($data as $key => $value) {
            $datos[$value['fec_inici2']][$value['cod_tercer']]['nom_transp'] = $value['abr_tercer'];
            $datos[$value['fec_inici2']][$value['cod_tercer']]['cod_priori'] = $value['cod_priori'];
            $datos[$value['fec_inici2']][$value['cod_tercer']]['nom_grupox'] = $value['nom_grupox'];
            $datos[$value['fec_inici2']][$value['cod_tercer']]['fec_monito'] = $value['fec_inici2'];

            $datos[$value['fec_inici2']][$value['cod_tercer']]['data'][$value['hor_calcul']]['desp'] = $value['can_despac'];
            $datos[$value['fec_inici2']][$value['cod_tercer']]['data'][$value['hor_calcul']]['cont'] .= $value['cod_usuari'] . ", ";
            $datos[$value['fec_inici2']][$value['cod_tercer']]['data'][$value['hor_calcul']]['supe'] = $value['usr_creaci'];

            if (!in_array($value['hor_calcul'], $horas)) {
                $horas[] = $value['hor_calcul'];
            }
        }
        
        asort($horas);
        $this->cHoras = $horas;
        return $datos;
    }
}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new inform();
}
?>
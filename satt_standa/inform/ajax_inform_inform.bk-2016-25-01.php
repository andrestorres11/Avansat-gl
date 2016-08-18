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

header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
setlocale(LC_ALL, "es_ES");
ini_set('memory_limit', '2048M');
set_time_limit(300);

class inform {

    private static $cConexion,
                   $cCodAplica,
                   $cUsuario,
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

                default:
                    header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

   
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
      
      $data = $this->getDatosInform($datos->fec_inicia, $datos->fec_finali, $datos->usuario, $intervalo, $datos->perfil);
       
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
    
    private function getDatosInform($finicia, $ffinali, $usuario, $intervalo, $perfil){
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
        
         $sql = "SELECT COUNT(DISTINCT(x.num_despac)) AS can_despac, COUNT(x.num_despac) AS can_regist, 
                        x.usr_creaci, x.cod_perfil $fec1
                  FROM (
                          (SELECT a.num_despac, a.fec_creaci, a.usr_creaci, b.cod_perfil  $and
                                FROM ".BASE_DATOS.".tab_despac_contro a
                                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci
                                INNER JOIN ".BASE_DATOS.".tab_genera_perfil c ON c.cod_perfil = b.cod_perfil 
                                WHERE  a.fec_creaci >= '$finicia' AND  a.fec_creaci < '$ffinali' $p $u
                                $group
                          )
                         UNION 
                         (SELECT a.num_despac, a.fec_creaci, a.usr_creaci, b.cod_perfil $and
                                FROM ".BASE_DATOS.".tab_despac_noveda a 
                                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                                INNER JOIN ".BASE_DATOS.".tab_genera_perfil c ON c.cod_perfil = b.cod_perfil 
                                WHERE a.fec_creaci >= '$finicia' AND  a.fec_creaci < '$ffinali' $p $u
                                $group
                          )
                      ) x 
                  GROUP BY x.usr_creaci $fec1 
                  ORDER BY x.usr_creaci $fec1";
                  //echo "<pre>".$sql."</pre>";die;
       $consulta = new Consulta($sql, self::$cConexion);
        $result= $consulta->ret_matrix("a");
        $datos = $result;
        return $datos;
    }

    /*! \fn: infoDia
     *  \brief: pinta el informe por dia
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
        //echo "<pre>".$fec_inicia_aux." -> ".$fec1." . ".$usuario." -> ".$value["usr_creaci"]." despachos ->".$despac."</pre>";
       

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

      //echo "<pre>";print_r($mAux);echo "</pre>";die;
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
          $mTr3 .= $j == 0 ? "<tr style='text-align:center'><th rowspan='2' class='CellHead2'>C&oacute;digo Perfil</th><th class='CellHead2' rowspan='2'>Usuario</th>" : "";
          $mTr2 = "<tr style='text-align:center'>";
          $mTr2.="<td class='cellInfo onlyCell'>".$mAux2[$key]."</td>
                  <td class='cellInfo onlyCell' >$value</td>";
          $i = 0; $x = 0;//variables para saber cuantas horas van en el reporte
          $tDespac = 0;
          $tDespac2 = 0;
          $tRegist = 0;
          $inici2 = $inici; // hora inicial auxiliar para los totales por usuario
          $final2 = ($final);
         while($inici != $final){
            $mTr .= $j == 0 ? "<th colspan='2' class='CellHead2' style='text-align:center'> $inici:00</th>": "";
            $mTr3 .= $j == 0 ? "<th class='CellHead2'>D. Tr&aacute;nsito</th><th class='CellHead2'>Registros</th>": "";
            $hora = $inici;
            if(strlen($inici) == 1){
              $hora = "0".$inici;
            }
            //echo "$mData[$value][$fec_inicia $hora]['can_despac']"."<br>";
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
            if($despachos > 0){
                $img1 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(1,'."'$fec_inicia $inici2'".','."'$fec_inicia ".($hora_reporte)."'".','."'$value'".'  )">'.$despachos.'</a>'; 
            }else{
            	$img1 = 0;
            }
            if($registros > 0){
                $img2 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(2,'."'$fec_inicia $hora'".','."'$fec_inicia ".($hora_reporte)."'".','."'$value'".'  )">'.$registros.'</a>';
            }else{
            	$img2 = 0;
            }
            $mTr2.="<td class='cellInfo onlyCell'>$img1</td>
                    <td class='cellInfo onlyCell'>$img2</td>";
            if( $key == (count($mAux)-1)){
                $desp_totl = $mData[$fec_inicia." ".$hora]['can_despac'];
                $regi_totl = $mData[$fec_inicia." ".$hora]['can_regist'];

                $mtdespac += $desp_totl;
                $mtregist += $regi_totl;
                $usuarios = trim($usuarios, ',');
                if($dth[$fec_inicia][$hora] >0 ){
                    $img1 = '<a style="cursor:pointer;color: #000000  !important;" onclick = "detalle(1,'."'$fec_inicia $inici2'".','."'$fec_inicia " .($hora_reporte)."'".','."'$usuarios'".'  )">'.($dth[$fec_inicia][$hora]+0).'</a>';
                }else{
                	$img1 = 0;
                }
                if($regi_totl > 0){
                  $img2 = '<a style="cursor:pointer;color: #000000 !important;" onclick = "detalle(2,'."'$fec_inicia $hora'".','."'$fec_inicia ".($hora_reporte)."'".','."'$usuarios'".'  )">'.($regi_totl+0).'</a>';
                }else{
                	$img2 = 0;
                }
            }
            $mTfoot .= $key == (count($mAux)-1)? "<th class='CellHead2' style='text-align:center'>$img1</th>
                                                  <th class='CellHead2' style='text-align:center'>$img2</th>": "";
            
            /*if($inici == 24){
              $inici = 0;
            }else{*/
              $inici ++;
            // }
            
          }
          $hor_inicial = $datos->hor_inicia +0;
          if(strlen($hor_inicial) == 1){
          		$hor_inicial = "0".$hor_inicial;
        	}
          if($mtdespac >0 ){
            $img5 = '<a style="cursor:pointer;color:#000000 !important;" onclick = "detalle(1,'."'$fec_inicia $hor_inicial'".','."'".$fec_inicia ." ".($datos->hor_finali +1)."'".','."'$usuarios'".'  )">'.$mtdespac.'</a>';
          }else{
          	$img5 = 0;
          }
          if($mtregist > 0){
            $img6 = '<a style="cursor:pointer;color:#000000 !important;" onclick = "detalle(2,'."'$fec_inicia $hor_inicial'".','."'".$fec_inicia ." ".($datos->hor_finali +1)."'".','."'$usuarios'".'  )">'.$mtregist.'</a>';
          }else{
          	$img6 = 0;
          }
          
          $mTfoot .= $key == (count($mAux)-1)? "<th class='CellHead2' style='text-align:center'>$img5</th><th class='CellHead2' style='text-align:center'>$img6</th>": "";
          $mTfoot .=  $key == (count($mAux)-1)? "</tr><tr><td class='cellInfo onlyCell' colspan='".(($i*2)+4)."'>&nbsp;</td></tr>": "";
          $mTr .= $j == 0 ? "<td colspan='2' class='CellHead2'> Total</td></tr>": "";
          $mTr3 .= $j == 0 ? "<th class='CellHead2'>D. Tr&aacute;nsito</th><th class='CellHead2'>Registros</th>": "";

          $img3 = "";
          $img4 = "";
          if($tDespac > 0){
              $img3 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(1,'."'$fec_inicia $inici2'".','."'$fec_inicia $final2'".','."'$value'".')"> '.$tDespac.'</a>';
	      }else{
	      	$img3 = 0;
	      }
	      if($tRegist > 0){
	          $img4 = '<a style="cursor:pointer;color:#285C00 !important;" onclick = "detalle(2,'."'$fec_inicia $inici2'".','."'$fec_inicia $final2'".','."'$value'".')"> '.$tRegist.'</a>';
	      }else{
	      	$img4 = 0;
	      }
          $mTr2 .= "<td class='cellInfo onlyCell'>$img3</td> 
                    <td class='cellInfo onlyCell'>$img4</td>";
          $mTr2 .= "</tr>";
          
          if( $j == 0 ){
            $mtitulo = "<tr>
                           <th class='CellHead2' colspan='".(($i*2)+4)."' style='text-align:center'> <b>$fec_inicia $datos->hor_inicia a $datos->hor_finali</b></th>
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
                        <b style='color:#000000 !important; text-align:center !important;'>No se encontró información para los parametros de busqueda especificados.</b>
                  </div>";
      }

      echo $mHtml;
    }

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
           <tr>
            <th class='CellHead2' style='text-align:center'>C&oacute;digo de Perfil</th>
            <th class='CellHead2' style='text-align:center'>Usuario</th>
            <th class='CellHead2' style='text-align:center'>Total de Despachos En tr&aacute;nsito</th>
            <th class='CellHead2' style='text-align:center'>Total de Registros</th>
          </tr> 
      <?php
          foreach ($mAux as $key => $value) {
            ?>
               <tr>
                 <td class='cellInfo onlyCell' style='text-align:center'><?= $mAux2[$key] ?></td>
                 <td class='cellInfo onlyCell' style='text-align:center'><?= $value ?></td>
                 <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$sem_inicia]['can_despac']+0) ?></td>
                 <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$sem_inicia]['can_regist']+0) ?></td>
               </tr>
            <?php
          }
          ?>
           <tr>
               <th colspan="2" class='CellHead2' style='text-align:center'>Total</th>
               <th class='CellHead2' style='text-align:center'><?= $mData[$sem_inicia]['can_despac'] ?></th>
               <th class='CellHead2' style='text-align:center'><?= $mData[$sem_inicia]['can_regist'] ?></th>
           </tr>
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
                 <tr>
                  <th class='CellHead2' style='text-align:center'>C&oacute;digo de Perfil</th>
                  <th class='CellHead2' style='text-align:center'>Usuario</th>
                  <th class='CellHead2' style='text-align:center'>Total de Despachos En tr&aacute;nsito</th>
                  <th class='CellHead2' style='text-align:center'>Total de Registros</th>
                </tr> 
            <?php
                foreach ($mAux as $key => $value) {
                 ?>
                 <tr>
                   <td class='cellInfo onlyCell' style='text-align:center'><?= $mAux2[$key] ?></td>
                   <td class='cellInfo onlyCell' style='text-align:center'><?= $value ?></td>
                   <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$fec_inicia]['can_despac']+0) ?></td>
                   <td class='cellInfo onlyCell' style='text-align:center'><?= ($mData[$value][$fec_inicia]['can_regist']+0) ?></td>
                 </tr>
                 <?php
                }
               ?>
               <tr>
                   <th colspan="2" class='CellHead2' style='text-align:center'>Total</th>
                   <th class='CellHead2' style='text-align:center'><?= $mData[$fec_inicia]['can_despac'] ?></th>
                   <th class='CellHead2' style='text-align:center'><?= $mData[$fec_inicia]['can_regist'] ?></th>
               </tr>
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

  private function getDetalle(){
    $datos = (object) $_POST;
    $datos->usuarios = str_replace(",", "','", $datos->usuarios);
    $datos->fec_inicia = $datos->fec_inicia.":00:00";
    $datos->fec_finali = $datos->fec_finali.":00:00";
    $datos->fec_finali = strtotime ( '-1 second' , strtotime ( $datos->fec_finali ) ) ;
    $datos->fec_finali = date ( 'Y-m-d H:i:s',$datos->fec_finali );
    $standa = $datos->standa;
    $sql = "SELECT x.num_despac, x.usr_creaci, x.cod_perfil, e.abr_tercer, x.fec_creaci, c.nom_noveda, x.fec1, x.obs_noveda
              FROM (
                      (SELECT a.num_despac, a.fec_creaci, a.usr_creaci, a.obs_contro obs_noveda, b.cod_perfil, a.cod_noveda, DATE_FORMAT(a.fec_creaci, '%Y-%m-%d %H') AS fec1
                            FROM ".BASE_DATOS.".tab_despac_contro a
                            INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                            WHERE a.usr_creaci IN ('$datos->usuarios') 
                              AND a.fec_creaci >= '$datos->fec_inicia' AND a.fec_creaci < '$datos->fec_finali'
                      )
                     UNION 
                      (SELECT a.num_despac, a.fec_creaci, a.usr_creaci, a.des_noveda obs_noveda, b.cod_perfil, a.cod_noveda, DATE_FORMAT(a.fec_creaci, '%Y-%m-%d %H') AS fec1
                            FROM ".BASE_DATOS.".tab_despac_noveda a 
                            INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                            WHERE a.usr_creaci IN ('$datos->usuarios') 
                              AND a.fec_creaci >= '$datos->fec_inicia' AND a.fec_creaci < '$datos->fec_finali'
                      )
                   ) x  
        LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c ON x.cod_noveda = c.cod_noveda 
        LEFT JOIN ".BASE_DATOS.".tab_despac_vehige d ON x.num_despac = d.num_despac 
        LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer e ON d.cod_transp = e.cod_tercer 
            ";
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
          <?php foreach ($datos as $key => $value) {
            $data = (object) $value;
            ?>
            <tr>
              <td class='cellInfo onlyCell' style='text-align:center'><a href="index.php?cod_servic=3302&window=central&despac='<?= $data->num_despac ?>'&tie_ultnov=0&opcion=1"><font style="color: #000000; cursor:pointer;"><?= $data->num_despac ?></font></a></td>
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

}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new inform();
}
?>

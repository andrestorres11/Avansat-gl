<?php  
setlocale(LC_ALL,"es_ES");

class extenc{

  private static  $cConexion,
                  $cCodAplica,
                  $cUsuario,
                  $cNull = array( array('','Seleccione un Elemento de la Lista') );


  function __construct($co = null, $us = null, $ca = null)
  {
    if($_REQUEST[Ajax] === 'on' ){
      @include_once( "../lib/ajax.inc" );
      @include_once( "../lib/general/constantes.inc" );
      self::$cConexion = $AjaxConnection;
    }else{
      self::$cConexion = $co;
      self::$cUsuario = $us;
      self::$cCodAplica = $ca;
    }

    if($_REQUEST[Ajax] === 'on' ){
        $opcion = $_REQUEST[Option];
        if(!$opcion){
          $opcion = $_REQUEST[operacion];
        }
      switch($opcion){
        case 'listExtensions':
          $cod_transp = $_REQUEST['transp'];
          self::listExtensions($cod_transp);
         break;

         case 'buscarUsuario':
          self::buscarUsuario();
         break;

         case 'registrarExtencion':
          self::registrarExtencion();
         break;

         case 'registrarOperacion':
          self::registrarOperacion();
         break;

         case 'registrarGrupo':
          self::registrarGrupo();
         break;

         case 'activar':
          self::activar();
         break;

         case 'inactivar':
          self::inactivar();
         break;

         case 'inactivarOperacion':
          self::inactivarOperacion();
         break;

         case 'activarOperacion':
          self::activarOperacion();
         break;

         case 'inactivarGrupo':
          self::inactivarGrupo();
         break;

         case 'activarGrupo':
          self::activarGrupo();
         break;

         case 'informeLlamadasEntrantes':
          self::informeLlamadasEntrantes();
         break;

         case 'GetDetalle':
          self::GetDetalle();
         break;

         case 'LoadCallPlay':
          self::LoadCallPlay();
         break;
         case 'getSubOperad':
          self::getSubOperad();
           break;

        default:
          header('Location: index.php?window=central&cod_servic=1366&menant=1366');
          break;
      }
    }
  }

 /***********************************************************************************
 *   \fn: registrarExtencion                                                        *
 *  \brief: funcion para registrar una extenion para un usuario                     *
 *  \author: Ing. Alexander Correa                                                  *
 *  \date:  4/12/2015                                                               *
 *  \date modified:                                                                 *
 *  \param: 										                                *     
 *  \param:                                                                         * 
 *  \return confirmacion de la insercion correcta o posible error                   *
 ***********************************************************************************/

    private function registrarExtencion(){
    	$datos = (object) $_POST; 
    	$usuario = $_SESSION['datos_usuario']['cod_usuari'];
    	$sql = "SELECT cod_extenc FROM ".BASE_DATOS.".tab_callce_extenc 
    			WHERE num_extenc = '$datos->num_extenc' 
    			/*AND cod_operac = '$datos->cod_operac' 
    			AND cod_grupox = '$datos->cod_grupox' */
    			AND ind_estado = 1 ";
    	$consulta = new Consulta($sql, self::$cConexion);
        $extencion = $consulta->ret_matrix("a");
        if(!$extencion){
        	#si no existe la extencion ingreso la nueva
        	$sql = "INSERT INTO ".BASE_DATOS.".tab_callce_extenc 
        			(usr_extenc, cod_operac, cod_grupox, num_extenc, usr_creaci, fec_creaci, cod_subope) 
        			VALUES ('$datos->usr_extenc', '$datos->cod_operac', '$datos->cod_grupox', '$datos->num_extenc', '$usuario', NOW(), '$datos->cod_subope' )";
			if( $insercion = new Consulta($sql, self::$cConexion, "R")){
					die('1'); // procedimiento correcto
			}else{
					die('2'); //errror al registrar en la base de datos
			}
        }else{
        	die("0"); //para avisar que ya la existe una extensión identica para el usuario seleccionado y debe inhabilitarse antes
        } 
    }


 /***********************************************************************************
 *   \fn: getTipoDeOperacion                                                        *
 *  \brief: funcion para listar los tipos de operacion del call-center              *
 *  \author: Ing. Alexander Correa                                                  *
 *  \date:  4/12/2015                                                               *
 *  \date modified:                                                                 *
 *  \param:                            												*     
 *  \param:                                                                         * 
 *  \returnlista de los tipos de operación                        				    *
 ***********************************************************************************/
    public function getTipoDeOperacion(){
      $sesion = (object) $_SESSION['datos_usuario'];
 
    	$sql = "SELECT cod_operac, nom_operac FROM ".BASE_DATOS.".tab_callce_operac WHERE ind_estado = 1";
    	$consulta = new Consulta($sql, self::$cConexion);
        $operaciones = $consulta->ret_matrix("a");
        $option = "";
        foreach ($operaciones as $key => $value) {
          if($sesion->cod_perfil == '712' && $value['cod_operac'] == '4'){
            $option.= "<option value='$value[cod_operac]' selected = 'selected'>".utf8_encode($value[nom_operac])."</option>";
          }
        	$option.= "<option value='$value[cod_operac]'>".utf8_encode($value[nom_operac])."</option>";
        }
        if($sesion->cod_perfil == '712'){
           $select = "<select style='width:100%' id='cod_operacID' name='cod_operac' validate='select' obl='1' disabled='true'>
                  <option value=''>Seleccione un tipo de Operación</option>
                  $option
                   </select>";
        }else{
          $select = "<select style='width:100%' id='cod_operacID' name='cod_operac' validate='select' obl='1'>
                	<option value=''>Seleccione un tipo de Operación</option>
                	$option
                   </select>";
       }
       

        return $select;
    }


 /***********************************************************************************
 *   \fn: getGrupos                                                                 *
 *  \brief: funcion para listar los grupos del call-center                          *
 *  \author: Ing. Alexander Correa                                                  *
 *  \date:  4/12/2015                                                               *
 *  \date modified:                                                                 *
 *  \param:                            												*     
 *  \param:                                                                         * 
 *  \returnlista de los tipos de operación                        				    *
 ***********************************************************************************/
    public function getGrupos(){

    	$sql = "SELECT cod_grupox, nom_grupox FROM ".BASE_DATOS.".tab_callce_grupox WHERE ind_estado = 1";
    	$consulta = new Consulta($sql, self::$cConexion);
        $grupos = $consulta->ret_matrix("a");
        $option = "";
        foreach ($grupos as $key => $value) {
        	$option.= "<option value='$value[cod_grupox]'>".utf8_encode($value[nom_grupox])."</option>";
        }
        
        $select = "<select style='width:100%' id='cod_grupox' name='cod_grupox' validate='select' obl='1'>
                	<option value=''>Seleccione un grupo</option>
                	$option
                   </select>";
       

        return $select;
    }

    private function buscarUsuario(){

    	$mSql = "SELECT cod_usuari cod_usuari, 
                        CONCAT(nom_usuari,'-',cod_usuari) usuario
                   FROM ".BASE_DATOS.".tab_genera_usuari 
                  WHERE 1=1 ";

        $mSql .= $_REQUEST[term] ? " AND cod_usuari LIKE '%".$_REQUEST[term]."%' " : "";

        $mSql .= " ORDER BY cod_usuari ";

        $mConsult = new Consulta($mSql, self::$cConexion );
        $mResult = $mConsult -> ret_matrix('a');

        if( $_REQUEST[term] )
        {
            $usuarios = array();
            for($i=0; $i<sizeof( $mResult ); $i++){
                $mTxt = $mResult[$i][cod_usuari]." - ".utf8_decode($mResult[$i][usuario]);
                $usuarios[] = array('value' => utf8_decode($mResult[$i][usuario]), 'label' => $mTxt, 'id' => $mResult[$i][cod_usuari] );
            }
            echo json_encode( $usuarios );
        }
        else{
            return $mResult;
        }
    }

    private function inactivar(){
    	$cod_extenc = $_POST['cod_extenc'];
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
       
        #inactiva la extensión
        $query = "UPDATE ".BASE_DATOS.".tab_callce_extenc 
                        SET ind_estado = 0,
                            usr_modifi = '$usuario',
                            fec_modifi = '$fec_actual'
                            WHERE cod_extenc = '$cod_extenc' ";
        if($insercion = new Consulta($query, self::$cConexion, "R")) {
           $mensaje = "Se insctivó la extensión exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR EXTENSIÓN",$mensaje);
        }
    }

    private function inactivarOperacion(){
    	$cod_operac = $_POST['cod_operac'];
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
       
        #inactiva la OPERACION
        $query = "UPDATE ".BASE_DATOS.".tab_callce_operac 
                        SET ind_estado = 0,
                            usr_modifi = '$usuario',
                            fec_modifi = '$fec_actual'
                            WHERE cod_operac = '$cod_operac' ";
        if($insercion = new Consulta($query, self::$cConexion, "R")) {
           $mensaje = "Se inactivó la operación exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR OPERACÓN",$mensaje);
        }
    }

    private function activarOperacion(){
    	$cod_operac = $_POST['cod_operac'];
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
       
        #inactiva la OPERACION
        $query = "UPDATE ".BASE_DATOS.".tab_callce_operac 
                        SET ind_estado = 1,
                            usr_modifi = '$usuario',
                            fec_modifi = '$fec_actual'
                            WHERE cod_operac = '$cod_operac' ";
        if($insercion = new Consulta($query, self::$cConexion, "R")) {
           $mensaje = "Se activó la operación exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR OPERACÓN",$mensaje);
        }
    }

    private function registrarOperacion(){
    	$nom_operac = $_POST['nom_operac'];
    	$fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];

        $sql = "SELECT cod_operac FROM ".BASE_DATOS.".tab_callce_operac WHERE nom_operac = '$nom_operac'";
        $consulta = new Consulta($sql, self::$cConexion);
        $operacion = $consulta->ret_matrix("a");
        if(!$operacion){
        	$sql = "INSERT INTO ".BASE_DATOS.".tab_callce_operac (nom_operac, usr_creaci, fec_creaci) VALUES ('$nom_operac', '$usuario', '$fec_actual')";
        	if( $insercion = new Consulta($sql, self::$cConexion, "R")){
					die('1'); // procedimiento correcto
			}else{
					die('2'); //errror al registrar en la base de datos
			}
        }else{
        	die('0');#ya existe la operacion a registrar
        }
    }

    private function registrarGrupo(){
    	$nom_grupox = $_POST['nom_grupox'];
    	$fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];

        $sql = "SELECT cod_grupox FROM ".BASE_DATOS.".tab_callce_grupox WHERE nom_grupox = '$nom_grupox'";
        $consulta = new Consulta($sql, self::$cConexion);
        $operacion = $consulta->ret_matrix("a");
        if(!$operacion){
        	$sql = "INSERT INTO ".BASE_DATOS.".tab_callce_grupox (nom_grupox, usr_creaci, fec_creaci) VALUES ('$nom_grupox', '$usuario', '$fec_actual')";
        	if( $insercion = new Consulta($sql, self::$cConexion, "R")){
					die('1'); // procedimiento correcto
			}else{
					die('2'); //errror al registrar en la base de datos
			}
        }else{
        	die('0');#ya existe el grupo a registrar
        }
    }

    private function inactivarGrupo(){
    	$cod_grupox = $_POST['cod_grupox'];
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
       
        #inactiva la extensión
        $query = "UPDATE ".BASE_DATOS.".tab_callce_grupox 
                        SET ind_estado = 0,
                            usr_modifi = '$usuario',
                            fec_modifi = '$fec_actual'
                            WHERE cod_grupox = '$cod_grupox' ";
        if($insercion = new Consulta($query, self::$cConexion, "R")) {
           $mensaje = "Se inactivó el grupo exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR GRUPO",$mensaje);
        }
    }

    private function activarGrupo(){
    	$cod_grupox = $_POST['cod_grupox'];
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
       
        #inactiva la extensión
        $query = "UPDATE ".BASE_DATOS.".tab_callce_grupox 
                        SET ind_estado = 1,
                            usr_modifi = '$usuario',
                            fec_modifi = '$fec_actual'
                            WHERE cod_grupox = '$cod_grupox' ";
        if($insercion = new Consulta($query, self::$cConexion, "R")) {
           $mensaje = "Se activó el grupo exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR GRUPO",$mensaje);
        }
    }

     /***********************************************************************************
     *   \fn: informeLlamadasEntrantes                                                  *
     *  \brief: funcion para mostrar los datos de la consulta de llamadas entrantes     *
     *  \author: Ing. Alexander Correa                                                  *
     *  \date:  23/12/2015                                                               *
     *  \date modified:                                                                 *
     *  \param:                                                                         *     
     *  \param:                                                                         * 
     *  \return html con los datos de la consulta                                       *
     ***********************************************************************************/
    private function informeLlamadasEntrantes(){

      $datos = (object) $_REQUEST; 

      $info = $this->getInfomr($datos->fec_inicia, $datos->fec_finali,$datos->cod_operac,$datos->num_celula, $datos->pestana, $datos->cod_subope);

      if($info){
       if($datos->pestana == "generaID"){
        $this->pintarGeneral($info, $datos);
       }else{
        $this->pintarOtras($info, $datos);
       }
      }else{
        ?>
        <div class="col-md-12 Style2DIV">
          <label style="color:black">No se encontró información para los parametros de busqueda especificados.</label>
        </div>
        <?php 
      }

    }

    private function getInfomr($fec_inicia, $fec_finali, $cod_operac, $num_celula, $pestana, $cod_subope){
      if($num_celula){
        $num_celula = "AND num_telefo LIKE '%$num_celula%'";
      }
      if($cod_subope){
        $subope = "AND b.cod_subope = '$cod_subope'";
        $subope2 = "AND d.cod_subope = '$cod_subope'";
      }

        $sql = "SELECT x.cantidad, x.estado, x.fecha 
                  FROM (
                          ( 
                                SELECT COUNT(a.num_telefo) AS cantidad, 'ANSWERED' AS estado, DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') AS fecha 
                                  FROM ".BASE_DATOS.".tab_despac_callin a 
                            INNER JOIN ".BASE_DATOS.".tab_callce_extenc b 
                                    ON b.num_extenc = a.cod_extenc 
                                 WHERE (a.nom_estado = 'ANSWERED' OR a.nom_estado = 'ANSWER' )
                                   AND b.cod_operac = '$cod_operac'
                                   AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$fec_inicia' AND '$fec_finali' 
                                       $num_celula 
                                       $subope
                              GROUP BY fecha 
                          )
                          UNION ALL
                          (
                                SELECT COUNT(c.num_telefo) AS cantidad, 'NOANSWER' AS estado, DATE_FORMAT(c.fec_creaci, '%Y-%m-%d') AS fecha 
                                  FROM ".BASE_DATOS.".tab_despac_callin c 
                             LEFT JOIN ".BASE_DATOS.".tab_callce_extenc d 
                                    ON d.num_extenc = c.cod_extenc 
                                 WHERE c.nom_estado = 'NOANSWER' 
                                   AND c.cod_extenc = '0'
                                   AND DATE_FORMAT(c.fec_creaci, '%Y-%m-%d') BETWEEN '$fec_inicia' AND '$fec_finali' 
                                       $num_celula  
                                       $subope2
                                   AND c.num_telefo IN (
                                                              SELECT DISTINCT(e.num_telefo) AS num_telefo 
                                                                FROM ".BASE_DATOS.".tab_despac_callin e 
                                                          INNER JOIN ".BASE_DATOS.".tab_callce_extenc f 
                                                                  ON f.num_extenc = e.cod_extenc 
                                                               WHERE DATE_FORMAT(e.fec_creaci, '%Y-%m-%d') BETWEEN '$fec_inicia' AND '$fec_finali' 
                                                                 AND e.nom_estado = 'ANSWER' 
                                                                 AND f.cod_operac = '$cod_operac' 
                                                                     $num_celula
                                                        )
                              GROUP BY fecha
                          )
                       ) x";

      $consulta = new Consulta($sql, self::$cConexion);
      return $consulta->ret_matrix("a");
    }

    /*! \fn: pintarGeneral
     *  \brief: Pinta el informe general
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: datos  Matriz
     *  \param: post   Array
     *  \return: 
     */
    private function pintarGeneral($datos,$post){
     
      $contestadas = 0;
      $nocontestadas = 0;
      $mdata = array();
      foreach ($datos as $key => $value) {
        if($value['estado'] == 'NOANSWER' ){
          $nocontestadas += $value['cantidad'];
          $mdata[$value['fecha']]['nocontestadas'] = $value['cantidad'];
        }else{
          $contestadas += $value['cantidad'];
          $mdata[$value['fecha']]['contestadas'] = $value['cantidad'];
        }
      }
      $cod_operac = $post->cod_operac;

      $pcontestadas = round(($contestadas * 100)/($contestadas+$nocontestadas),2); 
      $pnocontestadas = round(($nocontestadas * 100)/($contestadas+$nocontestadas),2); 

     
      ?>
      <div class="col-md-12 Style2DIV">
      <div id="tabla">
        <label><img src="../<?= $_SESSION['DIR_APLICA_CENTRAL']?>/imagenes/excel.jpg"  style="cursor:pointer" onclick="pintarExcel('TablaInforme')"/></label>
      </div>
        <table id="" class="table hoverTable" width="100%" cellspacing="0" cellpadding="2" border="0">
          <tr>
            <th colspan="6" class="CellHead2" style="text-align:center">Informe General del <?= $post->fec_inicia ?> al <?= $post->fec_finali ?> </th>
          </tr>
          <tr>
            <th colspan="2" class="CellHead2" style="text-align:center">Llamadas Generadas</th>
            <th class="CellHead2" style="text-align:center">Contestadas</th>
            <th class="CellHead2" style="text-align:center">Porentaje</th>
            <th class="CellHead2" style="text-align:center">No Contestadas</th>
            <th class="CellHead2" style="text-align:center">Porentaje</th>
          </tr>
          <tr>
            <th colspan="2" class="cellInfo onlyCell" style="text-align:center"><?= ($contestadas+$nocontestadas)?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $contestadas ?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $pcontestadas ?> %</th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $nocontestadas ?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $pnocontestadas ?> %</th>
          </tr>
          <tr>
            <th colspan="6" class="cellInfo onlyCell" style="text-align:center"> &nbsp; </th>
          </tr>
        </table>
        <table id="TablaInforme" class="table hoverTable" width="100%" cellspacing="0" cellpadding="2" border="0">
          <tr>
              <th class="CellHead2" style="text-align:center">Fecha</th>
              <th class="CellHead2" style="text-align:center">Llamadas Generadas</th>
              <th class="CellHead2" style="text-align:center">Contestadas</th>
              <th class="CellHead2" style="text-align:center">Porentaje</th>
              <th class="CellHead2" style="text-align:center">No Contestadas</th>
              <th class="CellHead2" style="text-align:center">Porentaje</th>
          </tr>

          <?php

          $mFec = $post->fec_inicia;

          while ( $mFec <= $post->fec_finali ) {
            $img = "";
            $img2 = "";
            $img3 = "<a><img src='../".DIR_APLICA_CENTRAL."/imagenes/ver.png' style='cursor:pointer' width='14px' height='14px' onclick='detalle($cod_operac,\"$mFec\",\"$mFec\", \"todas\")' </a>";
            if($mdata[$mFec]['contestadas'] > 0){
              $img = "<a><img src='../".DIR_APLICA_CENTRAL."/imagenes/ver.png' style='cursor:pointer' width='14px' height='14px' onclick='detalle($cod_operac,\"$mFec\",\"$mFec\", \"ANSWER\")' </a>";
            }
            if($mdata[$mFec]['nocontestadas'] > 0){
              $img2 = "<a><img src='../".DIR_APLICA_CENTRAL."/imagenes/ver.png' style='cursor:pointer' width='14px' height='14px' onclick='detalle($cod_operac,\"$mFec\",\"$mFec\", \"NOANSWER\")' </a>";
            }

            $pcontestadas2 = round(($mdata[$mFec]['contestadas']*100)/($mdata[$mFec]['contestadas']+$mdata[$mFec]['nocontestadas']),2);
            $pnocontestadas2 = round(($mdata[$mFec]['nocontestadas']*100)/($mdata[$mFec]['contestadas']+$mdata[$mFec]['nocontestadas']),2);
            ?>
            <tr>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $mFec ?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= ($mdata[$mFec]['contestadas']+$mdata[$mFec]['nocontestadas']).$img3 ?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $mdata[$mFec]['contestadas'] . $img ?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $pcontestadas2 ?> %</th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $mdata[$mFec]['nocontestadas'] . $img2 ?></th>
            <th class="cellInfo onlyCell" style="text-align:center"><?= $pnocontestadas2 ?> %</th>
          </tr>
            <?php
            $mFec = strtotime ( '+1 day' , strtotime ( $mFec ) ) ;
            $mFec = date ( 'Y-m-d' , $mFec );

          }
          $img = "";
          $img2 = "";
          $img3 = "<a><img src='../".DIR_APLICA_CENTRAL."/imagenes/ver.png' style='cursor:pointer' width='14px' height='14px' onclick='detalle($cod_operac,\"$post->fec_inicia\",\"$post->fec_finali\", \"todas\")' </a>";
          if($contestadas > 0){
            $img = "<a><img src='../".DIR_APLICA_CENTRAL."/imagenes/ver.png' style='cursor:pointer' width='14px' height='14px' onclick='detalle($cod_operac,\"$post->fec_inicia\",\"$post->fec_finali\", \"ANSWER\")' </a>";
          }
          if($nocontestadas > 0){
            $img2 = "<a><img src='../".DIR_APLICA_CENTRAL."/imagenes/ver.png' style='cursor:pointer' width='14px' height='14px' onclick='detalle($cod_operac,\"$post->fec_inicia\",\"$post->fec_finali\", \"NOANSWER\")' </a>";
          }
          ?>
           <tr>
              <th class="CellHead2" style="text-align:center">TOTAL</th>
              <th class="CellHead2" style="text-align:center"><?= ($contestadas+$nocontestadas).$img3 ?></th>
              <th class="CellHead2" style="text-align:center"><?= $contestadas.$img ?></th>
              <th class="CellHead2" style="text-align:center"><?= $pcontestadas ?> %</th>
              <th class="CellHead2" style="text-align:center"><?= $nocontestadas.$img2 ?></th>
              <th class="CellHead2" style="text-align:center"><?= $pnocontestadas ?> %</th>
            </tr>
        </table>
      </div>
      <?php

    }

    /*! \fn: GetDetalle
     *  \brief: Pinta el informe detallado
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function GetDetalle(){

      $post = (object) $_POST;
      
      $data = $this->getInfromacionDetallada($post);

      if($post->tipo == 'todas'){
        $detalle = "Detallado General de llamadas entrantes para el día $post->fec_inicia al $post->fec_finali";
      }else if ($post->tipo == 'ANSWER'){
        $detalle = "Detallado de llamadas entrantes contestadas para el día $post->fec_inicia al $post->fec_finali";
      }else{
        $detalle = "Detallado de llamadas entrantes no contestadas para el día $post->fec_inicia al $post->fec_finali";
      }
      ?>
      <div class="col-md-12">
        <br>
        <a onclick="pintarExcel('tabla_llamadasID')" style="cursor:pointer"><img border="0" src="../<?= DIR_APLICA_CENTRAL ?>/imagenes/excel.jpg"></a>
        <br>
        <label>Se encontró un total de <?= count($data) ?> registros</label>
        <div id="padre">
          <table id="tabla_llamadasID" class="table hoverTable" width="100%" cellspacing="0" cellpadding="2" border="0">
            <tr>
              <th colspan="8" class="CellHead2" style="text-align:center"> <?= $detalle ?> </th>
            </tr>
            <tr>
              <th class="CellHead2" style="text-align:center">#</th>
              <th class="CellHead2" style="text-align:center">ID Llamada</th>
              <th class="CellHead2" style="text-align:center">Extensi&oacute;n</th>
              <th class="CellHead2" style="text-align:center">No. de Celular</th>
              <th class="CellHead2" style="text-align:center">Duraci&oacute;n</th>
              <th class="CellHead2" style="text-align:center">Estado de Llamada</th>
              <th class="CellHead2" style="text-align:center">Fecha de Llamada</th>
              <th class="CellHead2" style="text-align:center">Conversaci&oacute;n</th>
            </tr>
            <?php
                foreach ($data as $key => $value) {
                 ?>
                  <tr>
                    <th class="cellInfo onlyCell" style="text-align:center"><?= ($key+1) ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center"><?= $value['cod_consec'] ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center"><?= $value['cod_extenc'] ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center"><?= $value['num_telefo'] ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center"><?= $value['tie_duraci'] ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center"><?php if($value['nom_estado'] == "ANSWER"){echo "Contestada";}else{echo "No Contestada";} ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center"><?= $value['fec_creaci'] ?></th>
                    <th class="cellInfo onlyCell" style="text-align:center">
                      <a>
                        <img src= "../<?= DIR_APLICA_CENTRAL ?>/imagenes/image_play.gif" width="20px" height="20px" onclick="getFileAudio('<?= $value['num_telefo'] ?>','<?= $value['cod_consec'] ?>','<?= DIR_APLICA_CENTRAL ?>')" />
                      </a>
                    </th>
                  </tr>
                 <?php
                }
             ?>
          </table>
        </div>
      </div>
      <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
        <div class="ui-dialog-buttonset">
        <button  type="button" onclick="cerrarElPopUp();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
          <span class="ui-button-text">Cerrar</span>
        </button>
        </div>
      </div>
       <?php
    }

    private function getInfromacionDetallada($post){
      $and = "";
      if($post->tipo != 'todas'){
        $and = " AND x.nom_estado = '$post->tipo'";
      }
      if($post->num_celula){
        $and = " AND x.num_telefo LIKE '%$post->num_celula%'";
      }
      if($post->cod_subope){
        $subope = "AND b.cod_subope = '$post->cod_subope'";
        $subope2 = "AND d.cod_subope = '$post->cod_subope'";
      }

        $sql = "SELECT x.cod_consec, x.num_telefo, x.tie_duraci, 
                       x.idx_llamad, x.nom_estado, x.rut_audiox, 
                       x.cod_extenc, x.idx_servic, x.fec_creaci, 
                       x.estado  
                  FROM (
                          (     SELECT a.cod_consec, a.num_telefo, a.tie_duraci, 
                                       a.idx_llamad, a.nom_estado, a.rut_audiox, 
                                       a.cod_extenc, a.idx_servic, a.fec_creaci, 
                                       'ANSWER' AS estado 
                                  FROM ".BASE_DATOS.".tab_despac_callin a 
                            INNER JOIN ".BASE_DATOS.".tab_callce_extenc b 
                                    ON b.num_extenc = a.cod_extenc 
                                 WHERE a.nom_estado = 'ANSWER' 
                                   AND b.cod_operac = '$post->cod_operac'
                                   AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$post->fec_inicia' AND '$post->fec_finali' 
                                   $subope
                          )
                          UNION ALL
                          (
                                SELECT c.cod_consec, c.num_telefo, c.tie_duraci, 
                                       c.idx_llamad, c.nom_estado, c.rut_audiox, 
                                       c.cod_extenc, c.idx_servic, c.fec_creaci, 
                                       'NOANSWER' AS estado 
                                  FROM ".BASE_DATOS.".tab_despac_callin c 
                             LEFT JOIN ".BASE_DATOS.".tab_callce_extenc d 
                                    ON d.num_extenc = c.cod_extenc 
                                 WHERE c.nom_estado = 'NOANSWER' 
                                  AND c.cod_extenc = '0'
                                  AND DATE_FORMAT(c.fec_creaci, '%Y-%m-%d') BETWEEN '$post->fec_inicia' AND '$post->fec_finali' 
                                  $subope2
                                  AND c.num_telefo IN (
                                                            SELECT DISTINCT(e.num_telefo) 
                                                              FROM ".BASE_DATOS.".tab_despac_callin e 
                                                        INNER JOIN ".BASE_DATOS.".tab_callce_extenc f 
                                                                ON f.num_extenc = e.cod_extenc 
                                                             WHERE DATE_FORMAT(e.fec_creaci, '%Y-%m-%d') BETWEEN '$post->fecha_inicial' AND '$post->fecha_final' 
                                                               AND f.cod_operac = '$post->cod_operac' 
                                                                   $num_celula
                                                      )
                          )
                       ) x 
                 WHERE 1=1 $and ";
 
      $consulta = new Consulta($sql, self::$cConexion);
      return $consulta->ret_matrix("a");
    }

    private function LoadCallPlay(){
      $post = (object) $_POST;
      $mSelect = "SELECT a.cod_consec, a.num_telefo, a.tie_duraci, a.idx_llamad, 
                         a.nom_estado, a.rut_audiox, a.cod_extenc, a.idx_servic, 
                         a.fec_creaci
                    FROM ".BASE_DATOS.".tab_despac_callin a 
                   WHERE a.cod_consec = '$post->cod_consec' AND num_telefo = '$post->num_telefo' ";
      $consulta = new Consulta( $mSelect, self::$cConexion);
      $mDataCall = $consulta -> ret_matrix('a');
      # Funcionalidad con S3-------------------------------
      @include_once("../lib/InterfS3Amazon.inc");
      $mS3 = new InterfS3Amazon(self::$cConexion, $mDataCall[0], "CallCenter");
      $mAudio = $mS3 -> getAudioLoaded();
    
      if($mAudio["cod_respon"] != '1000') {
        $mObjetAudio = $mAudio["msg_respon"];
      }
      else {
         # Crea el elemento de reproduccion del audio que se descargó de S3 -----------------------------------------
         if($_SERVER["HTTP_HOST"] == "dev.intrared.net:8083"){
          $ruta = "/ap/dev/";
         }else{
          $ruta = "/ap/";
         }
         $mSSL = $_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://';
         $mURL = $mSSL.$_SERVER["HTTP_HOST"].$ruta.DIR_APLICA_CENTRAL.$mAudio["fil_audio"];
         $mObjetAudio = '<audio controls> <source src="'.$mURL.'" type="audio/wav">
                        Su navegador no soporta elementos de Audio
                      </audio>';
      }
      echo $mObjetAudio;
    }

    private function getSubOperad(){

      $post = (object) $_REQUEST;

      $mSelect = "SELECT a.cod_subope, a.nom_subope
                    FROM ".BASE_DATOS.".tab_operad_subope a 
                   WHERE a.cod_operac = '$post->cod_operad' AND a.ind_estado = 1 ";
      $consulta = new Consulta( $mSelect, self::$cConexion);
      $mDataCall = $consulta -> ret_matrix('i'); 

 

      $mData = array( 
                      array( "select", "cod_subopeID", "", $mDataCall   ) 
                    );

     $xml = new Xml( $mData );


    }

  }

if($_REQUEST[Ajax] === 'on' )
  $_INFORM = new extenc();

?>


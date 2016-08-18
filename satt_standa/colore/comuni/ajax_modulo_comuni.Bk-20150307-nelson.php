<?php
ini_set('display_errors', false);

class AjaxModuloComunicaciones
{
  var $conexion;
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
    include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }

  protected function getUsuari( $_AJAX )
  {
    $mSelect = "SELECT cod_usuari, nom_usuari, usr_emailx, cod_perfil
                  FROM ".BASE_DATOS.".tab_genera_usuari WHERE ind_estado = '1' ";
    
    $mSelect .= $_AJAX['tip_consul'] == 'cod' ? " AND cod_usuari LIKE '%".$_AJAX['term']."%'" : "";
    $mSelect .= $_AJAX['tip_consul'] == 'nom' ? " AND nom_usuari LIKE '%".$_AJAX['term']."%'" : "";
    $mSelect .= $_AJAX['tip_consul'] == 'ema' ? " AND usr_emailx LIKE '%".$_AJAX['term']."%'" : "";
    
    if($_AJAX['tip_consul'] == 'nom')
      $citerio = 'nom_usuari';
    elseif($_AJAX['tip_consul'] == 'ema')
      $citerio = 'usr_emailx';

    $mSelect .= " ORDER BY ".$citerio;

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $transpor = $consulta -> ret_matriz();

    $data = array();
    $counter = 0;
    $arr_existe = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
      $_FILTER = $this -> VerifyFilter( $transpor[$i]['cod_usuari'], $transpor[$i]['cod_perfil'] );

      if( $_FILTER['clv_filtro'] == $_AJAX['cod_transp'] && !in_array( $transpor[$i][$citerio], $arr_existe ) )
      {
        $arr_existe[$transpor[$i][$citerio]] = $transpor[$i][$citerio];
        $data [] = '{"label":"'.utf8_encode($transpor[$i][$citerio]).'","value":"'. utf8_encode($transpor[$i][$citerio]).'"}'; 
        $counter++;
      }
      if( $counter >= 10 )
        break;  
    }
    echo '['.join(', ',$data).']';

  }

  protected function getTransp( $_AJAX )
  {
    $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    CONCAT( a.cod_tercer ,' - ', UPPER( a.abr_tercer ) ) LIKE '%". $_AJAX['term'] ."%'
           ORDER BY 2 
              LIMIT 10";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
       $data [] = '{"label":"'.$transpor[$i][0].' - '.$transpor[$i][1].'","value":"'. $transpor[$i][0].' - '.$transpor[$i][1].'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }

  private function Style()
  {
    echo '
      <style>
      
      .label-tr
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:12px;
        border-bottom: 1px solid #CDCDCD;
        border-top: 1px solid #FFFFFF;
      }
      
      .label-info
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:12px;
      }

      .displayDIV
      {
        background-color:#f0f0f0;
        border:1px solid #c9c9c9;
        padding:5px;
        min-height:200px;
        -moz-border-radius:5px 5px 5px 5px;
        -webkit-border-radius:5px 5px 5px 5px;
        border-top-left-radius:5px;
        border-top-right-radius:5px;
        border-bottom-right-radius:5px;
        border-bottom-left-radius:5px;
      }

      .displayDIV2
      {
        background-color:#D9D9D9;
        border:1px solid #9F9F9F;
        padding:5px;
        min-height:50px;
        -moz-border-radius:5px 5px 5px 5px;
        -webkit-border-radius:5px 5px 5px 5px;
        border-top-left-radius:5px;
        border-top-right-radius:5px;
        border-bottom-right-radius:5px;
        border-bottom-left-radius:5px;
      }
      
      .label-tr2
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:10px;
        border-bottom: 1px solid #CDCDCD;
        border-top: 1px solid #FFFFFF;
      }
      
      .CellHead
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .cellInfo1
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #EBF8E2;
          padding: 2px;
        }
        
        .cellInfo2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #DEDFDE;
          padding: 2px;
        }
      
      </style>
    ';
  }

  protected function MainForm( $_AJAX )
  {
    $this -> Style();
    $mHtml  = '';

    $mHtml .= '<script>
               $( "#nom_usuariID" ).autocomplete({
                 source: "../satt_standa/comuni/ajax_modulo_comuni.php?option=getUsuari&standa=satt_standa&tip_consul=nom&cod_transp='.$_AJAX['cod_transp'].'",
                 minLength: 2, 
                 delay: 100
               }).bind( "autocompleteclose", function(event, ui){SetUsuari( $(this), \'nom\', \''.$_AJAX['cod_transp'].'\'  );} );

               $( "#ema_usuariID" ).autocomplete({
                 source: "../satt_standa/comuni/ajax_modulo_comuni.php?option=getUsuari&standa=satt_standa&tip_consul=ema&cod_transp='.$_AJAX['cod_transp'].'",
                 minLength: 2, 
                 delay: 100
               }).bind( "autocompleteclose", function(event, ui){SetUsuari( $(this), \'ema\', \''.$_AJAX['cod_transp'].'\'  );} );

			   $( "#nom_usuariID" ).bind( "autocompletechange", function(event, ui){ SetUsuari( $(this), \'nom\', \''.$_AJAX['cod_transp'].'\'  ); } );
			   $( "#ema_usuariID" ).bind( "autocompletechange", function(event, ui){ SetUsuari( $(this), \'ema\', \''.$_AJAX['cod_transp'].'\'  ); } );
               </script>';

    $mHtml  .= '<center>';
      $mHtml  .= '<table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:18px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>'.str_replace('/-/','&',utf8_decode($_AJAX['nom_transp'])).'</i></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '</table>';
      $mHtml .= '</center><br><div id="AlarmaID" style="display:none;"></div>';

    $mHtml  .= '<center>';
      $mHtml  .= '<br><div id="filtrosID"><table width="70%" cellspacing="1" cellpadding="0">';
        
        $mHtml  .= '<tr>';
        $mHtml  .= '<td align="center" colspan="4" class="CellHead">FILTROS DE BUSQUEDA POR USUARIO</td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">NOMBRE:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="35" maxlength="70" name="nom_usuari" id="nom_usuariID" onchange="SetUsuari( $(this), \'nom\', \''.$_AJAX['cod_transp'].'\' );"/></td>';  
        $mHtml  .= '<td class="cellInfo1" align="right">CORREO:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="20" maxlength="50" name="ema_usuari" id="ema_usuariID" onchange="SetUsuari( $(this), \'ema\', \''.$_AJAX['cod_transp'].'\'  );"/></td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">USUARIO:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="15" maxlength="25" name="cod_usuari" id="cod_usuariID" onchange="SetUsuari( $(this), \'cod\', \''.$_AJAX['cod_transp'].'\'  );"/></td>';  
        $mHtml  .= '<td class="cellInfo1">&nbsp;</td>';  
        $mHtml  .= '<td class="cellInfo1">&nbsp;</td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '</table></div>';
        
        $mHtml  .= '<div id="FormElementsID" style="display:none;"></div>';

    $mHtml  .= '</center>';
    
    echo $mHtml;
  }

  private function getNameNoveda( $cod_noveda )
  {
    $mSelect = "SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda 
            FROM ".BASE_DATOS.".tab_genera_noveda 
           WHERE ind_visibl = '1' AND cod_noveda = '".$cod_noveda."'  
           ORDER BY 2";
  
  $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TRANSI = $consulta -> ret_matriz();
    return $_TRANSI[0]['nom_noveda'];
  }

  protected function setFormElements( $_AJAX )
  {
    $this -> Style();

    // REVISION DE LA CONFIGURACION PARA LA PERSONA SELECCIONADA -------
    // Trae la posible parametrizacion que ya tenga la persona ---------
    $mSelect = "SELECT cod_consec, cod_noveda, cod_criter, 
               val_criter, ind_tipres 
            FROM ".BASE_DATOS.".tab_detail_modcom 
           WHERE cod_usuari = '".$_AJAX['cod_usuari']."'";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_ACTCON = $consulta -> ret_matriz();

    $arr_actcon = array();
    foreach( $_ACTCON as $row )
    {
      $arr_actcon[$row['cod_noveda']]['ind_tipres'] = $row['ind_tipres'];
      $arr_actcon[$row['cod_noveda']][$row['cod_criter']][$row['val_criter']] = $row['val_criter'];
    }

    // PRIMERA ETAPA DEL FORMULARIO ---------------------
    // Etapas de los viajes -----------------------------
    // --------------------------------------------------
    $mSelect = "SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda 
            FROM ".BASE_DATOS.".tab_genera_noveda 
           WHERE ind_visibl = '1' AND nom_noveda LIKE '%NER /%' 
           ORDER BY 2";
  
  $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TRANSI = $consulta -> ret_matriz();

    $mSelect = "SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda 
            FROM ".BASE_DATOS.".tab_genera_noveda 
           WHERE ind_visibl = '1' AND 
               ( nom_noveda LIKE '%NEC /%' OR 
                 nom_noveda LIKE '%NICC /%' )
         ORDER BY 2";
  
  $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CARGUE = $consulta -> ret_matriz();

    $mSelect = "SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda 
            FROM ".BASE_DATOS.".tab_genera_noveda 
           WHERE ind_visibl = '1' AND 
               ( nom_noveda LIKE '%NED /%' )
         ORDER BY 2";
  
  $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESCAR = $consulta -> ret_matriz();

    $mHtml  = '';
    $mHtml .= '<script> $(function() {$( "#EtapasID" ).tabs();});</script>';
    $mHtml .= '<script> $(function() {$( "#rCargue" ).buttonset();});</script>';
    $mHtml .= '<script> $(function() {$( "#cCargue" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#cTransi" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#cDescar" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_tipopeID" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_producID" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_tiptraID" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_zonaxxID" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_canalxID" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_origenID" ).button();});</script>';
    $mHtml .= '<script> $(function() {$( "#all_deposiID" ).button();});</script>';
    
    $mHtml .= '<script>
               $( "#cod_ciudesID" ).autocomplete({
                 source: "../satt_standa/transp/ajax_transp_origen.php?option=getOrigen&standa=satt_standa",
                 minLength: 2, 
                 delay: 100
               });

               $( "#cod_ciudesID" ).bind( "autocompletechange", function(event, ui){SaveCiudad();} );

               </script>';
    $mHtml .= '<script>$(function() {$( "#upID" ).button({
         icons: {
          primary: "ui-icon-plusthick",
          },
          text: false
        });});</script>';
    $mHtml .= '<script>$(function() {$( "#enviarID" ).button({
         icons: {
          primary: "ui-icon-disk",
          }
        });});</script>';

    $mHtml .= '<br>';
    
    if( $_AJAX['ind_editar'] != '' )
    {
      $_CONFIG = $arr_actcon[$_AJAX['cod_noveda']];
      
      $mHtml .= '<script>$(function() {$( "#CancelarID" ).button({
         icons: {
          primary: "ui-icon-closethick",
          }
        });});</script>';

    $mHtml .= '<div class="displayDIV2">';
      $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';

        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4">NOVEDAD:'.$_AJAX['cod_noveda'].' - '.$this -> getNameNoveda( $_AJAX['cod_noveda'] ).'</td>';
        $mHtml .= '</tr><br>';

        $mHtml .= '<tr>';
          $mHtml .= '<td width="4%">Para</td>';
          $mHtml .= '<td width="4%">Copia</td>';
          $mHtml .= '<td width="52%"><b>&nbsp;</b></td>';
          $mHtml .= '<td width="40%"><b>&nbsp;</b></td>';
        $mHtml .= '</tr>';

        $checkedP = $_CONFIG['ind_tipres'] == 'P' ? 'checked="checked"': '';
        $checkedS = $_CONFIG['ind_tipres'] == 'S' ? 'checked="checked"': '';

        $checkP = '<input class="cargueP" '.$checkedP.' type="radio" name="'.$_AJAX['cod_noveda'].'" id="'.$_AJAX['cod_noveda'].'ID" value="P" >';
      $checkS = '<input class="cargueS" '.$checkedS.' type="radio" name="'.$_AJAX['cod_noveda'].'" id="'.$_AJAX['cod_noveda'].'ID" value="S" >';
        $namexx = $this -> getNameNoveda( $_AJAX['cod_noveda'] );

      $mHtml .= '<tr>';
          $mHtml .= '<td>'.$checkP.'</td>';
          $mHtml .= '<td>'.$checkS.'</td>';
          $mHtml .= '<td>'.$namexx.'</td>';
          $mHtml .= '<td><button id="CancelarID" onclick="CancelEditForm();" ><small>CANCELAR</small></button></td>';
        $mHtml .= '</tr>';

      $mHtml .= '</table>';
      $mHtml .= '</div>';
    }
    else
    {
      $mHtml .= '<div id="EtapasID" width="100%">';
      
      $mHtml .= '<ul>';
    $mHtml .= '<li><a href="#CargueID" onclick="$(function() {$( \'#rCargue\' ).buttonset();});">NOVEDADES EN CARGUE</a></li>';
    $mHtml .= '<li><a href="#TransitoID" onclick="$(function() {$( \'#rTransi\' ).buttonset();});">NOVEDADES EN TRANSITO</a></li>';
    $mHtml .= '<li><a href="#DescargueID" onclick="$(function() {$( \'#rDescar\' ).buttonset();});">NOVEDADES EN DESCARGUE</a></li>';
    $mHtml .= '</ul>';
      
      $mHtml .= '<div id="CargueID">';
        $mHtml .= '<div class="displayDIV">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
          
          $mHtml .= '<tr>';
          $mHtml .= '<td colspan="2">';
            
            $mHtml .= '<div id="rCargue">';
              $mHtml .= '<input type="radio" id="rCargueP" onclick="SelectAll(\'cargueP\', $(this) );" name="all_cargue" /><label for="rCargueP"><small>PARA</small></label>';
              $mHtml .= '<input type="radio" id="rCargueS" onclick="SelectAll(\'cargueS\', $(this) );" name="all_cargue" /><label for="rCargueS"><small>COPIA</small></label>';
            $mHtml .= '</div>';
          
          $mHtml .= '</td>'; 

          $mHtml .= '<td colspan="7">';
            $mHtml .= '<input type="button" id="cCargue" onclick="DeSelectAll(\'cargue\', $(this), \'rCargue\' );" name="des_cargue" value="ANULAR SELECCI&Oacute;N"/><br>';
          $mHtml .= '</td>'; 

          $mHtml .= '</tr>';

          $mHtml .= '<tr>';
          $mHtml .= '<td><small>Para</small></td>';
          $mHtml .= '<td><small>Copia</small></td>';
          $mHtml .= '<td><b>&nbsp;</b></td>';
          $mHtml .= '<td><small>Para</small></td>';
          $mHtml .= '<td><small>Copia</small></td>';
          $mHtml .= '<td>&nbsp;</td>';
          $mHtml .= '<td><small>Para</small></td>';
          $mHtml .= '<td><small>Copia</small></td>';
          $mHtml .= '<td>&nbsp;</td>';
          $mHtml .= '</tr>';
          $count = 0;

          foreach( $_CARGUE as $row )
          {
            if( $count == 0 )
            $mHtml .= '<tr>';
            
            $color = ''; 
            $checkP = '<input class="cargueP" type="radio" name="'.$row['cod_noveda'].'" id="'.$row['cod_noveda'].'ID" value="P" >';
            $checkS = '<input class="cargueS" type="radio" name="'.$row['cod_noveda'].'" id="'.$row['cod_noveda'].'ID" value="S" >';
            $namexx = utf8_encode( $row['nom_noveda'] );
            
            if( $arr_actcon[$row['cod_noveda']]['ind_tipres'] != '' )
            {
              $color  = 'style="background-color:#CBEEFF;"';
              $checkP = $arr_actcon[$row['cod_noveda']]['ind_tipres'] == 'P' ? '<span style="text-align:left;">&nbsp;&nbsp;&nbsp;X</span>' : '&nbsp';
              $checkS = $arr_actcon[$row['cod_noveda']]['ind_tipres'] == 'S' ? '<span style="text-align:left;">&nbsp;&nbsp;&nbsp;X</span>' : '&nbsp';
              $namexx .= '<div style="float:right;"><a style="text-decoration:none;cursor:pointer;color:#336600;" onclick="EditConfig(\''.$row['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\' );"><small>Editar</small></a><small>&nbsp;|&nbsp;</small><a style="text-decoration:none;cursor:pointer;color:#336600;" onclick="DeleteConfig(\''.$row['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\');"><small>Eliminar</small></a></div>';
            }

            $mHtml .= '<td '.$color.'>'.$checkP.'</td>';
            $mHtml .= '<td '.$color.'>'.$checkS.'</td>';
            $mHtml .= '<td '.$color.'>'.$namexx.'</td>';

            if( $count == 2 )
            {
              $mHtml .= '</tr>';
              $count = -1;
            }

            $count++;
          }

          $mHtml .= '</table>';
          $mHtml .= '</div>';
        $mHtml .= '</div>';
        
        $mHtml .= '<div id="TransitoID">';
          $mHtml .= '<div class="displayDIV">';
          $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
            
            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="2">';
              $mHtml .= '<div id="rTransi">';
                $mHtml .= '<input type="radio" id="rTransiP" onclick="SelectAll(\'transiP\', $(this) );" name="all_transi" /><label for="rTransiP"><small>PARA</small></label>';
                $mHtml .= '<input type="radio" id="rTransiS" onclick="SelectAll(\'transiS\', $(this) );" name="all_transi" /><label for="rTransiS"><small>COPIA</small></label>';
              $mHtml .= '</div>';
            $mHtml .= '</td>'; 
            $mHtml .= '<td colspan="7">';
            $mHtml .= '<input type="button" id="cTransi" onclick="DeSelectAll(\'transi\', $(this), \'rTransi\' );" name="des_transi" value="ANULAR SELECCI&Oacute;N"/><br>';
            $mHtml .= '</td>'; 
            $mHtml .= '</tr>';

            $mHtml .= '<tr>';
            $mHtml .= '<td><small>Para</small></td>';
            $mHtml .= '<td><small>Copia</small></td>';
            $mHtml .= '<td><b>&nbsp;</b></td>';
            $mHtml .= '<td><small>Para</small></td>';
            $mHtml .= '<td><small>Copia</small></td>';
            $mHtml .= '<td>&nbsp;</td>';
            $mHtml .= '<td><small>Para</small></td>';
            $mHtml .= '<td><small>Copia</small></td>';
            $mHtml .= '<td>&nbsp;</td>';
            $mHtml .= '</tr>';
            $count = 0;
            foreach( $_TRANSI as $row )
            {
              if( $count == 0 )
              $mHtml .= '<tr>';
              
              $color = ''; 
              $checkP = '<input class="transiP" type="radio" name="'.$row['cod_noveda'].'" id="'.$row['cod_noveda'].'ID" value="P" >';
              $checkS = '<input class="transiS" type="radio" name="'.$row['cod_noveda'].'" id="'.$row['cod_noveda'].'ID" value="S" >';
              $namexx = utf8_encode( $row['nom_noveda'] );
              
              if( $arr_actcon[$row['cod_noveda']]['ind_tipres'] != '' )
              {
                $color  = 'style="background-color:#CBEEFF;"';
                $checkP = $arr_actcon[$row['cod_noveda']]['ind_tipres'] == 'P' ? '<span style="text-align:left;">&nbsp;&nbsp;&nbsp;X</span>' : '&nbsp';
                $checkS = $arr_actcon[$row['cod_noveda']]['ind_tipres'] == 'S' ? '<span style="text-align:left;">&nbsp;&nbsp;&nbsp;X</span>' : '&nbsp';
                $namexx .= '<div style="float:right;"><a style="text-decoration:none;cursor:pointer;color:#336600;" onclick="EditConfig(\''.$row['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\' );"><small>Editar</small></a><small>&nbsp;|&nbsp;</small><a style="text-decoration:none;cursor:pointer;color:#336600;" onclick="DeleteConfig(\''.$row['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\');"><small>Eliminar</small></a></div>';
              }

              $mHtml .= '<td '.$color.'>'.$checkP.'</td>';
              $mHtml .= '<td '.$color.'>'.$checkS.'</td>';
              $mHtml .= '<td '.$color.'>'.$namexx.'</td>';

              if( $count == 2 )
              {
                $mHtml .= '</tr>';
                $count = -1;
              }

              $count++;
            }

          $mHtml .= '</table>';       
          $mHtml .= '</div>';
        $mHtml .= '</div>';
        
        $mHtml .= '<div id="DescargueID">';
          $mHtml .= '<div class="displayDIV">';
          $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
            
            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="2">';
              $mHtml .= '<div id="rDescar">';
                $mHtml .= '<input type="radio" id="rDescarP" onclick="SelectAll(\'descarP\', $(this) );" name="all_descar" /><label for="rDescarP"><small>PARA</small></label>';
                $mHtml .= '<input type="radio" id="rDescarS" onclick="SelectAll(\'descarS\', $(this) );" name="all_descar" /><label for="rDescarS"><small>COPIA</small></label>';
              $mHtml .= '</div>';
            $mHtml .= '</td>'; 
            $mHtml .= '<td colspan="7">';
            $mHtml .= '<input type="button" id="cDescar" onclick="DeSelectAll(\'descar\', $(this), \'rDescar\' );" name="des_descar" value="ANULAR SELECCI&Oacute;N"/><br>';
            $mHtml .= '</td>'; 
            $mHtml .= '</tr>';

            $mHtml .= '<tr>';
            $mHtml .= '<td><small>Para</small></td>';
            $mHtml .= '<td><small>Copia</small></td>';
            $mHtml .= '<td><b>&nbsp;</b></td>';
            $mHtml .= '<td><small>Para</small></td>';
            $mHtml .= '<td><small>Copia</small></td>';
            $mHtml .= '<td>&nbsp;</td>';
            $mHtml .= '<td><small>Para</small></td>';
            $mHtml .= '<td><small>Copia</small></td>';
            $mHtml .= '<td>&nbsp;</td>';
            $mHtml .= '</tr>';
            $count = 0;
            foreach( $_DESCAR as $row )
            {
              if( $count == 0 )
              $mHtml .= '<tr>';
              
              $color = ''; 
              $checkP = '<input class="descarP" type="radio" name="'.$row['cod_noveda'].'" id="'.$row['cod_noveda'].'ID" value="P" >';
              $checkS = '<input class="descarS" type="radio" name="'.$row['cod_noveda'].'" id="'.$row['cod_noveda'].'ID" value="S" >';
              $namexx = utf8_encode( $row['nom_noveda'] );
              
              if( $arr_actcon[$row['cod_noveda']]['ind_tipres'] != '' )
              {
                $color  = 'style="background-color:#CBEEFF;"';
                $checkP = $arr_actcon[$row['cod_noveda']]['ind_tipres'] == 'P' ? '<span style="text-align:left;">&nbsp;&nbsp;&nbsp;X</span>' : '&nbsp';
                $checkS = $arr_actcon[$row['cod_noveda']]['ind_tipres'] == 'S' ? '<span style="text-align:left;">&nbsp;&nbsp;&nbsp;X</span>' : '&nbsp';
                $namexx .= '<div style="float:right;"><a style="text-decoration:none;cursor:pointer;color:#336600;" onclick="EditConfig(\''.$row['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\' );"><small>Editar</small></a><small>&nbsp;|&nbsp;</small><a style="text-decoration:none;cursor:pointer;color:#336600;" onclick="DeleteConfig(\''.$row['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\');"><small>Eliminar</small></a></div>';
              }

              $mHtml .= '<td '.$color.'>'.$checkP.'</td>';
              $mHtml .= '<td '.$color.'>'.$checkS.'</td>';
              $mHtml .= '<td '.$color.'>'.$namexx.'</td>';

              if( $count == 2 )
              {
                $mHtml .= '</tr>';
                $count = -1;
              }

              $count++;
            }

          $mHtml .= '</table>';       
          $mHtml .= '</div>';
        $mHtml .= '</div>';

      $mHtml .= '</div>';
    }
    
    // SEGUNDA ETAPA DEL FORMULARIO ---------------------
    // Variables qjue intervienen en el proceso ---------
    // --------------------------------------------------
    $mHtml .= '<br>';

    $mHtml .= '<div id="VariablesID" style="overflow:hidden;width:100%;">';
    
      // Tipo de Operacion
      $mSelect = "SELECT cod_tipdes, nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes WHERE 1 = 1 ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_TIPDES = $consulta -> ret_matriz();

      $mHtml .= '<div style="float:left;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>TIPO DE OPERACI&Oacute;N</b>&nbsp;&nbsp;&nbsp;&nbsp;Seleccione por lo menos un Campo:</td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="20%"><input type="checkbox" name="all_tipope" id="all_tipopeID" onclick="SelectAll(\'tipope\', $(this) );" /><label for="all_tipopeID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="30%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="20%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="30%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_TIPDES as $row )
        {
          $checked = '';
      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[4][ $row['cod_tipdes'] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="20%">'.$row['nom_tipdes'].'</td>';
          $mHtml .= '<td width="30%"><input '.$checked.' type="checkbox" class="tipope" name="tipope'.$row['cod_tipdes'].'" id="tipope'.$row['cod_tipdes'].'ID" value="'.$row['cod_tipdes'].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';
      
      // Producto
      $mSelect = "SELECT cod_produc, nom_produc FROM ".BASE_DATOS.".tab_genera_produc WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_PRODUC = $consulta -> ret_matriz();
      
      $mHtml .= '<div style="margin-left:2%;float:right;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>NEGOCIO Y/O PRODUCTO</b>&nbsp;&nbsp;&nbsp;&nbsp;Seleccione por lo menos un Campo:</td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="35%"><input type="checkbox" name="all_produc" id="all_producID" onclick="SelectAll(\'produc\', $(this) );" /><label for="all_producID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="35%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_PRODUC as $row )
        {
          $checked = '';
      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[2][ $row['cod_produc'] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="35%">'.utf8_encode($row['nom_produc']).'</td>';
          $mHtml .= '<td width="15%"><input '.$checked.' type="checkbox" class="produc" name="produc'.$row['cod_produc'].'" id="produc'.$row['cod_produc'].'ID" value="'.$row['cod_produc'].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';

      // Zona
      $mSelect = "SELECT cod_zonaxx, nom_zonaxx FROM ".BASE_DATOS.".tab_genera_zonasx WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ZONASX = $consulta -> ret_matriz();

      $mHtml .= '<div style="margin-top:1%;float:left;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>ZONA</b></td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="35%"><input type="checkbox" name="all_zonaxx" id="all_zonaxxID" onclick="SelectAll(\'zonaxx\', $(this) );" /><label for="all_zonaxxID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="35%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_ZONASX as $row )
        {
          $checked = '';
      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[5][ $row['cod_zonaxx'] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="35%">'.utf8_encode($row['nom_zonaxx']).'</td>';
          $mHtml .= '<td width="15%"><input '.$checked.' type="checkbox" class="zonaxx" name="zonaxx'.$row['cod_zonaxx'].'" id="zonaxx'.$row['cod_zonaxx'].'ID" value="'.$row['cod_zonaxx'].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';
      
      // Tipo Transportadora
      $mSelect = "SELECT cod_tiptra, nom_tiptra FROM ".BASE_DATOS.".tab_genera_tiptra WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_TIPTRA = $consulta -> ret_matriz();

      $mHtml .= '<div style="margin-top:1%;margin-left:2%;float:right;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>TIPO TRANSPORTE</b></td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="35%"><input type="checkbox" name="all_tiptra" id="all_tiptraID" onclick="SelectAll(\'tiptra\', $(this) );" /><label for="all_tiptraID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="35%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_TIPTRA as $row )
        {
          $checked = '';
      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[7][ $row['cod_tiptra'] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="35%">'.utf8_encode($row['nom_tiptra']).'</td>';
          $mHtml .= '<td width="15%"><input '.$checked.' type="checkbox" class="tiptra" name="tiptra'.$row['cod_tiptra'].'" id="tiptra'.$row['cod_tiptra'].'ID" value="'.$row['cod_tiptra'].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';
      
      // Canal
      $mSelect = "SELECT con_consec, nom_canalx FROM ".BASE_DATOS.".tab_genera_canalx WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_CANALX = $consulta -> ret_matriz();

      $mHtml .= '<div style="margin-top:1%;float:left;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>CANAL</b></td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="35%"><input type="checkbox" name="all_canalx" id="all_canalxID" onclick="SelectAll(\'canalx\', $(this) );" /><label for="all_canalxID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="35%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_CANALX as $row )
        {
      $checked = '';
      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[6][ $row['con_consec'] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="35%">'.utf8_encode($row['nom_canalx']).'</td>';
          $mHtml .= '<td width="15%"><input '.$checked.' type="checkbox" class="canalx" name="canalx'.$row['con_consec'].'" id="canalx'.$row['con_consec'].'ID" value="'.$row['con_consec'].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';

      // ORIGENES
      $mSelect = "SELECT a.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                  FROM ".BASE_DATOS.".tab_transp_origen a,
                       ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                   WHERE a.cod_ciudad = b.cod_ciudad AND
                       b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       a.cod_transp = '".$_AJAX['cod_transp']."'
                   GROUP BY 1";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ORIGEN = $consulta -> ret_matriz();

      $mHtml .= '<div style="margin-top:1%;margin-left:2%;float:right;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>ORIGEN</b>&nbsp;&nbsp;&nbsp;&nbsp;Seleccione por lo menos un Campo:</td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="35%"><input type="checkbox" name="all_origen" id="all_origenID" onclick="SelectAll(\'origen\', $(this) );" /><label for="all_origenID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="35%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_ORIGEN as $row )
        {
      $checked = '';

      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[1][ $row[0] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="35%">'.utf8_encode( $row[1] ).'</td>';
          $mHtml .= '<td width="15%"><input '.$checked.' type="checkbox" class="origen" name="origen'.$row[0].'" id="origen'.$row[0].'ID" value="'.$row[0].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';

      $mHtml .= '<div style="margin-top:1%;float:left;width:46%;" class="displayDIV2">';
        $mHtml .= '<table id="gridDestinID" width="100%" border="0" cellspacing="1" cellpadding="0">';
          
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" colspan="4"><b>DESTINO</b></td>';
          $mHtml .= '</tr>';

          if( $_AJAX['ind_editar'] != '' && sizeof( $_CONFIG[3] ) > 0 )
          {
            foreach( $_CONFIG[3] as $cod_ciudes => $ciu_destin )
            {
              $mHtml .= '<tr>';
                $mHtml .= '<td style="padding:5px;">'.$this -> getNomCiudad( $cod_ciudes ).'</td>';
                $mHtml .= '<td style="padding:5px;" colspan="3"><input checked="checked" type="checkbox" checked="checked" class="destin" name="destin'.$cod_ciudes.'" id="destin'.$cod_ciudes.'ID" value="'.$cod_ciudes.'" /></td>';
              $mHtml .= '</tr>';
            }
          }

		  //DESTINOS 
          $mHtml .= '<tr>';
            $mHtml .= '<td style="padding:5px;">Digite y/o Seleccione una Ciudad</td>';
            $mHtml .= '<td style="padding:5px;" colspan="3"><input type="text" size="35" maxlength="70" name="cod_ciudes" id="cod_ciudesID" />&nbsp;&nbsp;<button onclick="UpCiudes();" name="up" id="upID" >&nbsp;</button></td>';
          $mHtml .= '</tr>';
        
        $mHtml .= '</table>';
      $mHtml .= '</div>';

	  // Depositos
	  $mSelect = "SELECT cod_deposi, nom_deposi FROM ".BASE_DATOS.".tab_genera_deposi WHERE ind_estado = '1' ORDER BY 2";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_DEPOSI = $consulta -> ret_matriz();

      $mHtml .= '<div style="margin-top:1%;float:left;width:46%;" class="displayDIV2">';
        $mHtml .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="4"><b>DEPOSITOS</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
          $mHtml .= '<td style="padding:5px;" width="35%"><input type="checkbox" name="all_deposi" id="all_deposiID" onclick="SelectAll(\'deposi\', $(this) );" /><label for="all_deposiID"><small>TODOS</small></label></td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="35%">&nbsp;</td>';
          $mHtml .= '<td style="padding:5px;" width="15%">&nbsp;</td>';
        $mHtml .= '</tr>';
        $count = 0;       
        foreach( $_DEPOSI as $row )
        {
      $checked = '';

      if( $count == 0 )
          $mHtml .= '<tr>';

        if( $_AJAX['ind_editar'] != '' && $_CONFIG[8][ $row[0] ] != '' )
          $checked = 'checked="checked"';

          $mHtml .= '<td width="35%">'.utf8_encode( $row[1] ).'</td>';
          $mHtml .= '<td width="15%"><input '.$checked.' type="checkbox" class="deposi" name="deposi'.$row[0].'" id="deposi'.$row[0].'ID" value="'.$row[0].'" /></td>';
          
          if( $count == 1 )
        {
          $mHtml .= '</tr>';
          $count = -1;
        }
        $count++;
        }

        $mHtml .= '</table>';
      $mHtml .= '</div>';

    if( $_AJAX['ind_editar'] != '' )
      {
        $mHtml .= '<div style="margin-top:1%;margin-left:2%;float:right;width:46%;">';
        $mHtml .= '<button onclick="UpdateConfig(\''.$_AJAX['cod_noveda'].'\', \''.$_AJAX['cod_usuari'].'\');" name="enviar" id="enviarID" ><small>ACTUALIZAR</small></button>';
        $mHtml .= '</div>';
      }
      else
      {
        $mHtml .= '<div style="margin-top:1%;margin-left:2%;float:right;width:46%;">';
        $mHtml .= '<button onclick="InsertConfig();" name="enviar" id="enviarID" ><small>REGISTRAR</small></button>';
        $mHtml .= '</div>';
      }

    $mHtml .= '</div>';

    echo $mHtml; 
  }

  protected function UpdateConfig( $_AJAX )
  {
    $consulta = new Consulta( "SELECT 1", $this -> conexion, "BR");

    $mSelect = "SELECT cod_consec 
                  FROM ".BASE_DATOS.".tab_detail_modcom 
                 WHERE cod_usuari = '".$_AJAX['cod_usuari']."' 
                   AND cod_noveda = '".$_AJAX['cod_noveda']."'
                 LIMIT 1";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_ULTCON = $consulta -> ret_matriz();
    $mUltcon = $_ULTCON[0]['cod_consec'];

    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_detail_modcom 
                 WHERE cod_usuari = '".$_AJAX['cod_usuari']."' 
                   AND cod_noveda = '".$_AJAX['cod_noveda']."'";

    $consulta = new Consulta( $mDelete, $this -> conexion, "R");

    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_genera_modcom 
                 WHERE cod_usuari = '".$_AJAX['cod_usuari']."' 
                   AND cod_consec = '".$mUltcon."'";

    $consulta = new Consulta( $mDelete, $this -> conexion, "R");

    $mCriter = $this -> getCriterio();

    $mSelect = "SELECT MAX( cod_consec ) AS num_consec 
          FROM ".BASE_DATOS.".tab_genera_modcom 
         WHERE cod_usuari = '".$_AJAX['cod_usuari']."'";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_MAXCON = $consulta -> ret_matriz();
    $num_consec = $_MAXCON[0]['num_consec'] + 1;

    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_genera_modcom
              ( cod_usuari, cod_consec, usr_creaci, fec_creaci)
        VALUES( '".$_AJAX['cod_usuari']."', '".$num_consec."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
      
    $consulta = new Consulta( $mInsert, $this -> conexion, "R");


    foreach( $mCriter as $nom_criter => $cod_criter )
    {
      if( sizeof( $_AJAX[$nom_criter] ) > 0 )
      {
        foreach( $_AJAX[$nom_criter] as $val_criter )
        {
            $mInsert = "INSERT INTO ".BASE_DATOS.".tab_detail_modcom
                                  ( cod_usuari, cod_consec, cod_noveda, 
                                    cod_criter, val_criter, ind_tipres, 
                                    usr_creaci, fec_creaci )
                            VALUES( '".$_AJAX['cod_usuari']."', '".$num_consec."', '".$_AJAX['cod_noveda']."',
                                    '".$cod_criter."', '".$val_criter."', '".$_AJAX['cod_tipres']."', 
                                    '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
            $consulta = new Consulta( $mInsert, $this -> conexion, "R");
        }
      }
    }
    if( $insercion = new Consulta( "COMMIT", $this -> conexion ) )
    {
      $mHtml  .= '<center>';
      $mHtml  .= '<div style="font-family:Trebuchet MS, Verdana, Arial;font-size:14px;background-color:#C0F7CF;border:2px solid #00A42C;padding:5px;width:98%;min-height:50px;-moz-border-radius:5px 5px 5px 5px;-webkit-border-radius:5px 5px 5px 5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">';
      $mHtml  .= '<span><br>La Parametrizaci&oacute;n ha sido Actualizada Exitosamente</span>';
      $mHtml  .= '</div>';
      $mHtml  .= '</center>';
    }
    else
    {
      $mHtml  .= '<center>';
      $mHtml  .= '<div style="font-family:Trebuchet MS, Verdana, Arial;font-size:14px;background-color:#FFB0B0;border:2px solid #900000;padding:5px;width:98%;min-height:50px;-moz-border-radius:5px 5px 5px 5px;-webkit-border-radius:5px 5px 5px 5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">';
      $mHtml  .= '<span><br>La Parametrizaci&oacute;n NO ha sido Actualizada, Por favor intente nuevamente</span>';
      $mHtml  .= '</div>';
      $mHtml  .= '</center>';
    }
    echo $mHtml;
  }

  protected function validateResponse( $mData )
  {
    /*$mCruzar = array();
    $mListad = array();
    $mCriter = $this -> getCriterio();

    foreach( $mCriter as $mNomcri => $mCodcri )
    {
      if( count( $mData[ $mNomcri ] ) > 0 )
      {
        $mCruzar[$mCodcri] = $mData[ $mNomcri ];
      }
    }

    foreach( $mData['inf_noveda'] as $inf_noveda )
    {
      if( $inf_noveda['cod_tipres'] == 'P' )
      {
        $mSelect = "SELECT cod_usuari, cod_consec, cod_noveda, 
                           cod_criter, val_criter, ind_tipres 
                      FROM ".BASE_DATOS.".tab_detail_modcom 
                     WHERE cod_noveda = '".$inf_noveda['cod_noveda']."'
                       AND ind_tipres = '".$inf_noveda['cod_tipres']."'";

        $consulta = new Consulta( $mSelect, $this -> conexion );
        $mExiste = $consulta -> ret_matriz();

        if( sizeof( $mExiste ) > 0 )
        {
          foreach( $mExiste as $row )
          {
            if( count( $mCruzar[$row['cod_criter']] ) > 0 )
            {
              foreach( $mCruzar[$row['cod_criter']] as $mCruce )
              {
                if( $row['val_criter'] == $mCruce )
                {
                  $mListad[$inf_noveda['cod_noveda']][$row['cod_criter']][$row['val_criter']] = $row['val_criter'];
                }
              }
            }
          }
        }
      }
    }
    $mResult = array();
    foreach( $mCriter as $mNomcri => $mCodcri )
    {
      foreach( $mListad as $cod_noveda => $mList )
      {
        if( count( $mList[$mCodcri] ) > 0 )
        {
          $mResult[$cod_noveda][$mNomcri] = $mList[$mCodcri];
        }
      }
    }

    if( sizeof( $mResult ) > 0 )
    {
      $this -> Style();
      
      $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0">';
        $mHtml .= '<tr>';
          $mHtml .= '<td class="CellHead" style="text-align:left;" colspan="4">LA CONFIGURACI&Oacute;N NO HA SIDO EXITOSA DEBIDO A QUE LAS SIGUIENTES NOVEDADES YA SE ENCUENTRAN CON UN RESPONSABLE:</td>';
        $mHtml .= '</tr>';
      $mHtml .= '</table>';
      
        foreach( $mResult as $cod_noveda => $mRestri )
        {
          $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';
          $colspan = sizeof( $mRestri );
          
          $mHtml .= '<tr>';
          $mHtml .= '<td class="CellHead" style="text-align:left;" colspan="'.$colspan.'"><strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$this -> getNomNoveda( $cod_noveda ).'</strong></td>';
          $mHtml .= '</tr>';

          $mHtml .= '<tr>';
          foreach( $mRestri as $tip_criter => $res_aplica )
          {
            $mTitle = $this -> getTitle( $tip_criter );
            $mHtml .= '<td class="CellHead" align="center"><small>'.$mTitle.'</small></td>';
          }
          $mHtml .= '</tr>';

          $mHtml .= '<tr>';
          foreach( $mRestri as $tip_criter => $res_aplica )
          {
            $content = '';
            foreach( $res_aplica as $repetido )
            {
              $content .= $this -> getReadValue( $tip_criter, $repetido ).'<br>';
            }
            $mHtml .= '<td class="CellInfo1" align="center">'.$content.'</td>';
          }
          $mHtml .= '</tr>';

          $mHtml .= '</table>';
        }
      
      echo $mHtml;
    }
    else
    {*/
      echo "n";
    //}
  }

  protected function getReadValue( $tip_criter, $val_campox )
  {
    switch( $tip_criter )
    {
      case 'cod_ciuori':
        $return = $this -> getNomCiudad( $val_campox );
      break;
      
      case 'cod_produc':
        $mSelect = "SELECT cod_produc, nom_produc 
                      FROM ".BASE_DATOS.".tab_genera_produc 
                     WHERE ind_estado = '1' 
                       AND cod_produc = '".$val_campox."' 
                     ORDER BY 2";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_PRODUC = $consulta -> ret_matriz();
        $return = $_PRODUC[0]['nom_produc'];
      break;      
      
      case 'cod_ciudes':
        $return = $this -> getNomCiudad( $val_campox );
      break;      

      case 'cod_tipdes':
        $mSelect = "SELECT cod_tipdes, nom_tipdes 
                      FROM ".BASE_DATOS.".tab_genera_tipdes 
                     WHERE cod_tipdes = '".$val_campox."' 
                     ORDER BY 2";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_TIPDES = $consulta -> ret_matriz();
        $return = $_TIPDES[0]['nom_tipdes'];
      break;      

      case 'cod_zonaxx':
        $mSelect = "SELECT cod_zonaxx, nom_zonaxx 
                      FROM ".BASE_DATOS.".tab_genera_zonasx 
                     WHERE ind_estado = '1'
                       AND cod_zonaxx = '".$val_campox."'
                     ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ZONASX = $consulta -> ret_matriz();
      $return = $_ZONASX[0]['nom_zonaxx'];
      break;      

      case 'cod_canalx':
        $mSelect = "SELECT cod_canalx, nom_canalx 
                      FROM ".BASE_DATOS.".tab_genera_canalx 
                     WHERE ind_estado = '1' 
                       AND cod_canalx = '".$val_campox."'
                     ORDER BY 2";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_CANALX = $consulta -> ret_matriz();
        $return = $_CANALX[0]['nom_canalx'];
      break;

      case 'cod_tiptra':
        $mSelect = "SELECT cod_tiptra, nom_tiptra 
                      FROM ".BASE_DATOS.".tab_genera_tiptra 
                     WHERE ind_estado = '1' 
                       AND cod_tiptra = '".$val_campox."'
                     ORDER BY 2";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_TIPTRA = $consulta -> ret_matriz();
        $return = $_TIPTRA[0]['nom_tiptra'];
      break;
    }
    return utf8_encode( $return );
  }

  protected function getTitle( $tip_criter )
  {
    $mTitle = '';
    switch( $tip_criter )
    {
      case 'cod_ciuori':
        $mTitle = 'CIUDAD DE ORIGEN';
      break;
      
      case 'cod_produc':
        $mTitle = 'NEGOCIO Y/O PRODUCTO';
      break;      
      
      case 'cod_ciudes':
        $mTitle = 'CIUDAD DE DESTINO';
      break;      

      case 'cod_tipdes':
        $mTitle = 'TIPO DE OPERACI&Oacute;N';
      break;      

      case 'cod_zonaxx':
        $mTitle = 'ZONA';
      break;      

      case 'cod_canalx':
        $mTitle = 'CANAL';
      break;

      case 'cod_tiptra':
        $mTitle = 'TIPO TRANSPORTE';
      break;
    }
    return $mTitle;
  }

  protected function getNomNoveda( $cod_noveda = NULL )
  {
    $mSelect = "SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda 
            FROM ".BASE_DATOS.".tab_genera_noveda 
           WHERE ind_visibl = '1'";

    if( $cod_noveda != NULL )
      $mSelect .= " AND cod_noveda = '".$cod_noveda."'";
      
    $mSelect .= " ORDER BY 2";
  
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDA = $consulta -> ret_matriz();

    return $_NOVEDA[0]['nom_noveda'];
  }

  protected function DeleteConfig( $_AJAX )
  {
    $mSelect = "SELECT cod_usuari, cod_consec 
              FROM ".BASE_DATOS.".tab_detail_modcom 
           WHERE cod_usuari = '".$_AJAX['cod_usuari']."' 
             AND cod_noveda = '".$_AJAX['cod_noveda']."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mLlavex = $consulta -> ret_matriz();


    $consulta = new Consulta("SELECT 1", $this -> conexion, "BR");

    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_detail_modcom 
           WHERE cod_usuari = '".$_AJAX['cod_usuari']."' 
             AND cod_noveda = '".$_AJAX['cod_noveda']."'
             AND cod_consec = '".$mLlavex[0]['cod_consec']."'";
    $consulta = new Consulta( $mDelete, $this -> conexion, "R");

    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_genera_modcom 
           WHERE cod_usuari = '".$_AJAX['cod_usuari']."' 
             AND cod_consec = '".$mLlavex[0]['cod_consec']."'";
    $consulta = new Consulta( $mDelete, $this -> conexion, "R");

    if( $insercion = new Consulta( "COMMIT", $this -> conexion ) )
    {
      $mHtml  .= '<center>';
      $mHtml  .= '<div style="font-family:Trebuchet MS, Verdana, Arial;font-size:14px;background-color:#C0F7CF;border:2px solid #00A42C;padding:5px;width:98%;min-height:50px;-moz-border-radius:5px 5px 5px 5px;-webkit-border-radius:5px 5px 5px 5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">';
      $mHtml  .= '<span><br>La Parametrizaci&oacute;n ha sido Eliminada Exitosamente</span>';
      $mHtml  .= '</div>';
      $mHtml  .= '</center>';
    }
    else
    {
      $mHtml  .= '<center>';
      $mHtml  .= '<div style="font-family:Trebuchet MS, Verdana, Arial;font-size:14px;background-color:#FFB0B0;border:2px solid #900000;padding:5px;width:98%;min-height:50px;-moz-border-radius:5px 5px 5px 5px;-webkit-border-radius:5px 5px 5px 5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">';
      $mHtml  .= '<span><br>La Parametrizaci&oacute;n NO ha sido Eliminada, Por favor intente nuevamente</span>';
      $mHtml  .= '</div>';
      $mHtml  .= '</center>';
    }
    
    echo $mHtml;

  }

  private function getCriterio()
  {
    $mCriter = array();
    $mSelect = "SELECT cod_criter, nom_criter FROM ".BASE_DATOS.".tab_config_modcom WHERE 1 = 1";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CRITER = $consulta -> ret_matriz();
    foreach( $_CRITER as $criterio )
    {
      $mCriter[ $criterio['nom_criter'] ] = $criterio['cod_criter'];
    }

    return $mCriter; 
  }

  protected function InsertConfig( $_AJAX )
  {
    $mCriter = $this -> getCriterio();

    $consulta = new Consulta("SELECT 1", $this -> conexion, "BR");

    foreach( $_AJAX['inf_noveda'] as $des_noveda )
    {
      $mSelect = "SELECT MAX( cod_consec ) AS num_consec 
          FROM ".BASE_DATOS.".tab_genera_modcom 
         WHERE cod_usuari = '".$_AJAX['cod_usuari']."'";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_MAXCON = $consulta -> ret_matriz();
      $num_consec = $_MAXCON[0]['num_consec'] + 1;

      $mInsert = "INSERT INTO ".BASE_DATOS.".tab_genera_modcom
                ( cod_usuari, cod_consec, usr_creaci, fec_creaci)
              VALUES( '".$_AJAX['cod_usuari']."', '".$num_consec."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
      
      $consulta = new Consulta( $mInsert, $this -> conexion, "R");


      foreach( $mCriter as $nom_criter => $cod_criter )
      {
        if( sizeof( $_AJAX[$nom_criter] ) > 0 )
        {
          foreach( $_AJAX[$nom_criter] as $val_criter )
          {
              $mInsert = "INSERT INTO ".BASE_DATOS.".tab_detail_modcom
                                  ( cod_usuari, cod_consec, cod_noveda, 
                          cod_criter, val_criter, ind_tipres, 
                          usr_creaci, fec_creaci )
                    VALUES( '".$_AJAX['cod_usuari']."', '".$num_consec."', '".$des_noveda['cod_noveda']."',
                            '".$cod_criter."', '".$val_criter."', '".$des_noveda['cod_tipres']."', 
                            '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
          $consulta = new Consulta( $mInsert, $this -> conexion, "R");
          }
        }
      }
    }
    
    if( $insercion = new Consulta( "COMMIT" , $this -> conexion ) )
    {
      $mHtml  .= '<center>';
      $mHtml  .= '<div style="font-family:Trebuchet MS, Verdana, Arial;font-size:14px;background-color:#C0F7CF;border:2px solid #00A42C;padding:5px;width:98%;min-height:50px;-moz-border-radius:5px 5px 5px 5px;-webkit-border-radius:5px 5px 5px 5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">';
      $mHtml  .= '<span><br>La Parametrizaci&oacute;n ha sido Configurada Exitosamente</span>';
      $mHtml  .= '</div>';
      $mHtml  .= '</center>';
    }
    else
    {
      $mHtml  .= '<center>';
      $mHtml  .= '<div style="font-family:Trebuchet MS, Verdana, Arial;font-size:14px;background-color:#FFB0B0;border:2px solid #900000;padding:5px;width:98%;min-height:50px;-moz-border-radius:5px 5px 5px 5px;-webkit-border-radius:5px 5px 5px 5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">';
      $mHtml  .= '<span><br>La Parametrizaci&oacute;n NO ha sido Configurada, Por favor intente nuevamente</span>';
      $mHtml  .= '</div>';
      $mHtml  .= '</center>';
    }
    
    echo $mHtml;
  }

  protected function getNomCiudad( $cod_ciudad )
  {
    $mSelect = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                  FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                 WHERE b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       b.cod_ciudad = '".$cod_ciudad."'
                 GROUP BY 1";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESTIN = $consulta -> ret_matriz();
    return $_DESTIN[0][1];
  }

  protected function UpCiudes( $_AJAX )
  {
    $mSelect = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                  FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                 WHERE b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       b.cod_ciudad = '".$_AJAX['cod_ciudes']."'
                 GROUP BY 1";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESTIN = $consulta -> ret_matriz();

    if( sizeof( $_DESTIN ) > 0 )
    {
    $mHtml  = '<tr>';
        $mHtml .= '<td style="padding:5px;">'.utf8_encode( $_DESTIN[0][1] ).'</td>';
        $mHtml .= '<td style="padding:5px;" colspan="3"><input type="checkbox" checked="checked" class="destin" name="destin'.$_DESTIN[0][0].'" id="destin'.$_DESTIN[0][0].'ID" value="'.$_DESTIN[0][0].'" /></td>';
      $mHtml .= '</tr>';
    
    echo $mHtml;
    }
    else
      echo 'n';
  }

  private function VerifyFilter( $cod_usuari, $cod_perfil )
  {
    if ( $cod_perfil == NULL ) {
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $cod_usuari );
      if ( $filtro -> listar( $this -> conexion ) ) : 
        $datos_filtro = $filtro -> retornar();
      endif;
    }
    else { 
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $cod_perfil );
      if ( $filtro -> listar( $this -> conexion ) ) : 
        $datos_filtro = $filtro -> retornar();
      endif;
    }
    return $datos_filtro;
  }

  protected function SetUsuari( $_AJAX )
  {
    $mSelect = "SELECT cod_usuari, nom_usuari, usr_emailx, cod_perfil
                  FROM ".BASE_DATOS.".tab_genera_usuari WHERE ind_estado = '1' ";
    
    $mSelect .= $_AJAX['tip_consul'] == 'cod' ? " AND cod_usuari = '".$_AJAX['nom_campox']."'" : "";
    $mSelect .= $_AJAX['tip_consul'] == 'nom' ? " AND nom_usuari LIKE '%".$_AJAX['nom_campox']."%'" : "";
    $mSelect .= $_AJAX['tip_consul'] == 'ema' ? " AND usr_emailx = '".$_AJAX['nom_campox']."'" : "";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_USUARI = $consulta -> ret_matriz();

    if( sizeof( $_USUARI ) == 1 )
    {
      $_FILTER = $this -> VerifyFilter( $_USUARI[0]['cod_usuari'], $_USUARI[0]['cod_perfil'] );
      
      if( $_FILTER['clv_filtro'] == $_AJAX['cod_transp'] )
        echo $_USUARI[0]['cod_usuari'].'|'.$_USUARI[0]['nom_usuari'].'|'.$_USUARI[0]['usr_emailx']; 
      else
      echo "r";
    }
    else
      echo "n";
  }

  protected function ValidateTransp( $_AJAX )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    a.cod_tercer = '". trim($_AJAX['cod_transp']) ."'";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    if( sizeof( $transpor ) > 0 )
      echo 'y';
    else
      echo 'n';
  }

}

$proceso = new AjaxModuloComunicaciones();

?>
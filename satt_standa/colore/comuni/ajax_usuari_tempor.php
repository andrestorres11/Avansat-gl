<?php
ini_set('display_errors', false);

class AjaxInactivarUsuarioTemporal
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

 

  protected function MainForm( $_AJAX )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $mHtml  = '';
    
    $mHtml .= '<script>
               $( "#nom_usuariID, #nom_reemplID" ).autocomplete({
                 source: "../satt_standa/comuni/ajax_modulo_comuni.php?option=getUsuari&standa=satt_standa&tip_consul=nom&cod_transp='.$_AJAX['cod_transp'].'",
                 minLength: 2, 
                 delay: 100
               });

               $( "#ema_usuariID, #ema_reemplID" ).autocomplete({
                 source: "../satt_standa/comuni/ajax_modulo_comuni.php?option=getUsuari&standa=satt_standa&tip_consul=ema&cod_transp='.$_AJAX['cod_transp'].'",
                 minLength: 2, 
                 delay: 100
               });
               </script>';
    
    $mHtml  .= '<script>
                  jQuery(function($) 
                  {
                    $(".ui-datepicker-week-col").css( "color", "#FFFFFF" );
                    
                    $( "#fec_finaliID, #fec_iniciaID" ).datepicker({
                      changeMonth: true,
                      changeYear: true,
                      minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).') 
                    });
                    
                    $.mask.definitions["A"]="[12]";
                    $.mask.definitions["M"]="[01]";
                    $.mask.definitions["D"]="[0123]";
          
                    $.mask.definitions["H"]="[012]";
                    $.mask.definitions["N"]="[012345]";
                    $.mask.definitions["n"]="[0123456789]";
          
                    $( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
                  });
                </script>';

    $mHtml  .= '<script>$(function() {$( "#InsertarID" ).button({
				 icons: {
					primary: "ui-icon-disk",
					}
				});});</script>';

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
        $mHtml  .= '<td align="center" colspan="4" class="CellHead">USUARIO A REEMPLAZAR</td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">NOMBRE:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="35" maxlength="70" name="nom_usuari" id="nom_usuariID" onchange="SetUsuari( $(this), \'nom\', \''.$_AJAX['cod_transp'].'\', 1 );"/></td>';  
        $mHtml  .= '<td class="cellInfo1" align="right">CORREO:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="20" maxlength="50" name="ema_usuari" id="ema_usuariID" onchange="SetUsuari( $(this), \'ema\', \''.$_AJAX['cod_transp'].'\', 1 );"/></td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">USUARIO:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="15" maxlength="25" name="cod_usuari" id="cod_usuariID" onchange="SetUsuari( $(this), \'cod\', \''.$_AJAX['cod_transp'].'\', 1 );"/></td>';  
        $mHtml  .= '<td class="cellInfo1" align="right">MOTIVO:</td>';  
        $mSelect = '<select name="des_motivo" id="des_motivoID">
                    <option value="">- Seleccione -</option>
                    <option value="V">Vacaciones</option>
                    <option value="I">Incapacidad</option>
                    <option value="L">Licencia</option>
                    <option value="C">Cambio de Rol</option>
                    <option value="R">Retiro</option>
                    </select>';
        $mHtml  .= '<td class="cellInfo1" align="left">'.$mSelect.'</td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '<tr>';
        $mHtml  .= '<td align="center" colspan="4" class="CellHead">RANGOS DE TIEMPO</td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">FECHA INCIAL:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="10" name="fec_inicia" id="fec_iniciaID" /></td>';  
        $mHtml  .= '<td class="cellInfo1" align="right">FECHA FINAL:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="10" name="fec_finali" id="fec_finaliID" /></td>';  
        $mHtml  .= '</tr>';

        $mHtml  .= '<tr>';
        $mHtml  .= '<td align="center" colspan="4" class="CellHead">USUARIO REEMPLAZANTE</td>';  
        $mHtml  .= '</tr>';
        
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">NOMBRE:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="35" maxlength="70" name="nom_reempl" id="nom_reemplID" onchange="SetUsuari( $(this), \'nom\', \''.$_AJAX['cod_transp'].'\', 2 );"/></td>';  
        $mHtml  .= '<td class="cellInfo1" align="right">CORREO:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="20" maxlength="50" name="ema_reempl" id="ema_reemplID" onchange="SetUsuari( $(this), \'ema\', \''.$_AJAX['cod_transp'].'\', 2 );"/></td>';  
        $mHtml  .= '</tr>';
        
        $mHtml  .= '<tr>';
        $mHtml  .= '<td class="cellInfo1" align="right">USUARIO:</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left"><input type="text" size="15" maxlength="25" name="cod_reempl" id="cod_reemplID" onchange="SetUsuari( $(this), \'cod\', \''.$_AJAX['cod_transp'].'\', 2 );"/></td>';  
        $mHtml  .= '<td class="cellInfo1" align="right">&nbsp;</td>';  
        $mHtml  .= '<td class="cellInfo1" align="left">&nbsp;</td>';  
        $mHtml  .= '</tr>';

        $mHtml  .= '<tr>';
        $mHtml  .= '<td colspan="4" align="center"><br><button onclick="validateFechas();" id="InsertarID" style="display:none;"><small>INSERTAR</small></button></td>';  
        $mHtml  .= '</tr>';
      
        $mHtml  .= '</table></div>';
        
        $mHtml  .= '<div id="messageID" style="display:none;padding-top:10px;"></div>';
        $mHtml  .= '<div id="FormElementsID" style="display:none;padding-top:10px;"></div>';

    $mHtml  .= '</center>';
    
    echo $mHtml;
  }

  protected function validateFechas( $_AJAX )
  {	
    $mSelect = "SELECT MAX( cod_consec ) 
  				  FROM ".BASE_DATOS.".tab_restri_modcom 
  				 WHERE cod_usuari = '".$_AJAX['cod_usuari']."' ";
  	
  	$consulta = new Consulta( $mSelect, $this -> conexion );
    $_CONSEC = $consulta -> ret_matriz();
    $mConsec = $_CONSEC[0][0] + 1;

    $consulta = new Consulta("SELECT 1", $this -> conexion, "BR");

    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_restri_modcom
                          ( cod_usuari, cod_consec, fec_inicia, 
                          	fec_finali, des_motivo, cod_reempl,
                            usr_creaci, fec_creaci )
					VALUES( '".$_AJAX['cod_usuari']."', '".$mConsec."', '".$_AJAX['fec_inicia']." 00:00:00',
						    '".$_AJAX['fec_finali']." 23:59:59', '".$_AJAX['des_motivo']."', '".$_AJAX['cod_reempl']."',
                 '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
  	$consulta = new Consulta( $mInsert, $this -> conexion, "R");

  	if( $insercion = new Consulta( "COMMIT", $this -> conexion ) )
  	{
  	  echo "y";
  	}
  	else
  	{
  	  echo "n";
  	}
  }

  protected function ShowDataForm( $_AJAX )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

    $mSelect = "SELECT a.cod_consec, a.fec_inicia, a.fec_finali, 
                       a.des_motivo, a.cod_reempl, b.nom_usuari
                  FROM ".BASE_DATOS.".tab_restri_modcom a,
                       ".BASE_DATOS.".tab_genera_usuari b 
                 WHERE a.cod_reempl = b.cod_usuari
                   AND a.cod_usuari = '".$_AJAX['cod_usuari']."' ";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_INFORM = $consulta -> ret_matriz();
    
    $mHtml .= '<center>';
      $mHtml .= '<div class="displayDIV2">';
      
      $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';
      
        if( sizeof( $_INFORM ) > 0 )
        {
          $mHtml .= '<tr>';
          $mHtml .= '<td width="20%" class="CellHead" align="center">Fecha de Inicio</td>';
          $mHtml .= '<td width="20%" class="CellHead" align="center">Fecha de Finalizaci&oacute;n</td>';
          $mHtml .= '<td width="20%" class="CellHead" align="center">Motivo</td>';
          $mHtml .= '<td width="20%" class="CellHead" align="center">Estado</td>';
          $mHtml .= '<td width="20%" class="CellHead" align="center">Reemplazante</td>';
          $mHtml .= '</tr>';
        
          $count = 0;
          foreach ( $_INFORM as $row ) 
          {
            $fec_actual = date('Y-m-d H:s:i');
            
            if( strtotime( $fec_actual ) <= strtotime( $row['fec_inicia'] ) )
              $estado = '<span style="background-color:#B0FFD2;">ACTIVO</span>';
            elseif( strtotime( $fec_actual ) > strtotime( $row['fec_finali'] ) ) 
              $estado = '<span style="background-color:#FFD3D3;">FINALIZADO</span>';
            else
              $estado = '<span style="background-color:#FBFFD3;">EN EJECUCI&Oacute;N</span>';
            
            $motivo = '';
            switch ( $row['des_motivo'] ) 
            {
              case 'V':$motivo = 'Vacaciones';break;
              case 'I':$motivo = 'Incapacidad';break;
              case 'L':$motivo = 'Licencia';break;
              case 'C':$motivo = 'Cambio de Rol';break;
              case 'R':$motivo = 'Retiro';break;
            }

            $reempl = $row['nom_usuari'];
            $style = $count % 2 == 0 ? 'cellInfo2' : 'cellInfo1';
            $mHtml .= '<tr>';
            $mHtml .= '<td class="'.$style.'" align="center">'.$this -> readDate( $row['fec_inicia'] ).'</td>';
            $mHtml .= '<td class="'.$style.'" align="center">'.$this -> readDate( $row['fec_finali'] ).'</td>';
            $mHtml .= '<td class="'.$style.'" align="center">'.$motivo.'</td>';
            $mHtml .= '<td class="'.$style.'" align="center">'.$estado.'</td>';
            $mHtml .= '<td class="'.$style.'" align="center">'.$reempl.'</td>';
            $mHtml .= '</tr>';
            $count++;
          } 
        }
        else
        {
          $mHtml .= '<tr>';
          $mHtml .= '<td class="CellHead" align="center">NO SE ENCONTRARON REGISTROS</td>';
          $mHtml .= '</tr>';
        }
      
      $mHtml .= '</table>';

      $mHtml .= '</div>';
    $mHtml .= '</center>';

    echo $mHtml;

  }

  function readDate( $mDate )
  {
    $Data = explode(' ', $mDate );
    $mDate = $Data[0]; 
    $week = date( 'w', strtotime( $mDate ) );
    $date = explode('-', $mDate);
    
    switch ( $week ) 
    {
      case 0: $dia = 'Domingo';          break;
      case 1: $dia = 'Lunes';            break;
      case 2: $dia = 'Martes';           break;
      case 3: $dia = 'Mi&eacute;rcoles'; break;
      case 4: $dia = 'Jueves';           break;
      case 5: $dia = 'Viernes';          break;
      case 6: $dia = 'Sabado';           break;
    }

    switch ( $date[1] ) 
    { 
      case 1:  $mes = 'Enero';      break;
      case 2:  $mes = 'Febrero';    break;
      case 3:  $mes = 'Marzo';      break;
      case 4:  $mes = 'Abril';      break;
      case 5:  $mes = 'Mayo';       break;
      case 6:  $mes = 'Junio';      break;
      case 7:  $mes = 'Julio';      break;
      case 8:  $mes = 'Agosto';     break;
      case 9:  $mes = 'Septiembre'; break;
      case 10: $mes = 'Octubre';    break;
      case 11: $mes = 'Noviembre';  break;
      case 12: $mes = 'Diciembre';  break;
    }
    return $dia.", ".$date[2]." de ".$mes." de ".$date[0];
  }

  public function Matriz( $mat )
  {
    echo "<pre>";
    print_r( $mat );
    echo "</pre>";
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

$proceso = new AjaxInactivarUsuarioTemporal();

?>
<?php

class AjaxProtocMatcom
{
  var $conexion;
  var $ind_estado = array();
  var $ind_estado_ = array();
  
  public function __construct()
  {
    $this -> ind_estado[0][0] = '';
    $this -> ind_estado[0][1] = '--';
    
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  protected function getData( $_AJAX )
  {
    $mSelect = "SELECT a.usr_emailx, a.cod_usuari 
                  FROM ".BASE_DATOS.".tab_genera_usuari a
                 WHERE 1 = 1";
                 
    if( $_AJAX['nom_usuari'] != '' )
      $mSelect .= " AND a.cod_usuari = '".$_AJAX['nom_usuari']."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_VALIDATE = $consulta -> ret_matriz();
    
    if( sizeof( $_VALIDATE ) > 1 || sizeof( $_VALIDATE ) <= 0 )
    {
      echo "|";
    }
    else
    {
      echo $_VALIDATE[0]['cod_usuari']."|".$_VALIDATE[0]['usr_emailx'];
    }
    
  }
  
  protected function getCorreos( $_AJAX )
  {
    
    $dir_coract = $_AJAX['dir_coract'];
    $dir_cornue = $_AJAX['dir_cornue'];
    
    $mSelect = "SELECT a.cod_protoc, b.des_protoc, a.num_consec, 
                       a.cod_ciuori, a.cod_ciudes, a.cod_produc,
                       c.nom_produc, a.cod_tipdes, d.nom_tipdes,
                       a.ema_conpri, a.ema_otrcon 
                  FROM ".BASE_DATOS.".tab_genera_protoc b,
                       ".BASE_DATOS.".tab_protoc_matcom a
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc c
                    ON a.cod_produc = c.cod_produc
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes d
                    ON a.cod_tipdes = d.cod_tipdes
                 WHERE a.cod_protoc = b.cod_protoc 
                 AND ( a.ema_conpri LIKE '%".$dir_coract."%' 
                    OR a.ema_otrcon LIKE '%".$dir_coract."%' )";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_PROTOC = $consulta -> ret_matriz();
    
    $mHtml  = '<table width="100%" cellspacing="0" cellpadding="0" border="1">';
    if( sizeof( $_PROTOC ) > 0 )
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" colspan="3">PROTOCOLO</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">ORIGEN</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">DESTINO</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">PRODUCTO</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">TIPO DESPACHO</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">CONT. PRINCIPAL<br>ACTUAL</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">CONT. PRINCIPAL<br>NUEVO</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">OTROS CONTACTOS<br>ACTUAL</td>';
        $mHtml .= '<td align="center" class="cellHead" rowspan="2">OTROS CONTACTOS<br>NUEVO</td>';
      $mHtml .= '</tr>';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead"><small>MARQUE</small><br><input type="checkbox" onclick="CheckAll( $( this ) );"></td>';
        $mHtml .= '<td align="center" class="cellHead">CONSECUTIVO</td>';
        $mHtml .= '<td align="center" class="cellHead">DESCRIPCION</td>';
      $mHtml .= '</tr>';
      $count = 0;
      foreach( $_PROTOC as $row )
      {
        $class = $count % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
        $mHtml .= '<tr>';
          //-------------------------------------------------------------------------
          // Llave compuesta para cada campo, separada por '|': cod_protoc|num_consec
          $llave = $row['cod_protoc']."|".$row['num_consec'];
          //-------------------------------------------------------------------------
          $ciu_origen = $this -> getCiudad( $row['cod_ciuori'] );
          $ciu_destin = $this -> getCiudad( $row['cod_ciudes'] );
          
          $mHtml .= '<td align="center" class="'.$class.'"><input type="checkbox" class="ck" id="key_'.$count.'ID" name="key_'.$count.'" value="'.$llave.'"></td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.$row['num_consec'].'</td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.( $row['cod_protoc']." - ".$row['des_protoc'] ).'</td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.( $ciu_origen[0][1] != '' ? $ciu_origen[0][1] : 'N/A' ).'</td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.( $ciu_destin[0][1] != '' ? $ciu_destin[0][1] : 'N/A' ).'</td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.( $row['nom_produc'] != '' ? $row['nom_produc'] : 'N/A' ).'</td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.$row['nom_tipdes'].'</td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.( str_replace( ',', ',<br>', $row['ema_conpri'] ) ).'</td>';
          $mHtml .= '<td align="center" class="'.$class.'"><textarea id="ema_conpri_'.$count.'ID" rows="2" cols="25">'.( str_replace( $dir_coract, $dir_cornue, $row['ema_conpri'] ) ).'</textarea></td>';
          $mHtml .= '<td align="center" class="'.$class.'">'.( str_replace( ',', ',<br>', $row['ema_otrcon'] ) ).'</td>';
          $mHtml .= '<td align="center" class="'.$class.'"><textarea id="ema_otrcon_'.$count.'ID" rows="2" cols="25">'.( str_replace( $dir_coract, $dir_cornue, $row['ema_otrcon'] ) ).'</textarea></td>';
        $mHtml .= '</tr>';
        $count++;
      }
      $mHtml .= '<input type="hidden" name="total" id="totalID" value="'.$count.'">';
    }
    else
    {
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead">NO SE ENCONTRARON RESULTADOS</td>';
      $mHtml .= '</tr>';
    }
    $mHtml .= '</table>';
    $mHtml .= '<br><br><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="ActualizaContacto();"/>';
    echo $mHtml;
  }
  
  function ActualizaContacto( $_AJAX )
  {
    // echo "<pre>";
    // print_r( $_AJAX );
    // echo "</pre>";
    $tot_regist = $_AJAX['tot'];
    $key_compue = $_AJAX['key'];
    $ema_conpri = $_AJAX['ema_conpri'];
    $ema_otrcon = $_AJAX['ema_otrcon'];
    
    $consec = new Consulta( "SELECT 1", $this -> conexion, "BR" );
    
    for( $i = 0; $i < $tot_regist; $i++ )
    {
      if( $key_compue[$i] != '' )
      {
        $llave = explode( '|', $key_compue[$i] );
        $mUpdate = "UPDATE ".BASE_DATOS.".tab_protoc_matcom 
                       SET ema_conpri = '".$ema_conpri[$i]."',
                           ema_otrcon = '".$ema_otrcon[$i]."', 
                           usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                           fec_modifi = NOW()
                     WHERE cod_protoc = '".$llave[0]."' 
                       AND num_consec = '".$llave[1]."'";
        $consulta = new Consulta( $mUpdate, $this -> conexion, "R" );
      }
    }
    
    if( $consulta = new Consulta( "SELECT 1", $this -> conexion, "RC" ) )
    {
      $mHtml  = '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
      $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >La Informaci&oacute;n ha Sido Actualizada Exitosamente.</i></td>';
      $mHtml .= '</tr>';
      $mHtml .= '</table></center>';
      echo $mHtml;
    }
  }
  
  private function getCiudad( $cod_ciudad = NULL )
  {
    $mSql = "SELECT a.cod_ciudad, CONCAT( UPPER( a.nom_ciudad ), '- (', LEFT(b.abr_depart, 4), ')' ) AS nom_ciudad
               FROM ".BASE_DATOS.".tab_genera_ciudad a,
                    ".BASE_DATOS.".tab_genera_depart b
              WHERE a.ind_estado = '1'
                AND a.cod_depart  = b.cod_depart 
              ";
    if( $cod_ciudad != NULL && $cod_ciudad != '' )
    {
      $mSql .= " AND cod_ciudad = ".$cod_ciudad;      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  protected function getTransp( $_AJAX )
  {
    $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = '1' AND
                    CONCAT( a.cod_tercer ,' - ', UPPER( a.abr_tercer ) ) LIKE '%". $_AJAX['term'] ."%'
           ORDER BY 2
           LIMIT 10";
		
		$consulta = new Consulta( $mSql, $this -> conexion );
		$transpor = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i < $len; $i++ )
    {
      $data[] = '{"label":"'.$transpor[$i][0].' - '.$transpor[$i][1].'","value":"'. $transpor[$i][0].' - '.$transpor[$i][1].'"}'; 
    }
    echo '['.join(', ',$data).']';
  }
}

$proceso = new AjaxProtocMatcom();
 ?>
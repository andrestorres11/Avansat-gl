<?php

class AjaxDespacDestin
{
  var $conexion;
  var $ind_estado = array();
  var $ind_estado_ = array();
  
  public function __construct()
  {
    $this -> ind_estado_[0][0] = '';
    $this -> ind_estado_[0][1] = '--';
    $this -> ind_estado[1][0] = 'PENDIENTE';
    $this -> ind_estado[1][1] = 'PENDIENTE';
    $this -> ind_estado[2][0] = 'ACTUALIZADO';
    $this -> ind_estado[2][1] = 'ACTUALIZADO';
    
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  protected function SetDestinatarios( $_AJAX )
  {
    $mSelect = "SELECT num_docume, num_docalt, cod_genera,
                       nom_destin, cod_ciudad, dir_destin, 
                       num_destin, fec_citdes, hor_citdes,
                       fec_findes
                  FROM ".BASE_DATOS.".tab_despac_destin 
                 WHERE num_despac = '".$_AJAX['num_despac']."' 
                 ORDER BY fec_citdes ASC, hor_citdes ASC";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_DESTIN = $consulta -> ret_matriz();


    # Datos Destinatari del webservice -------------------------------------------------
    $mQuery = "SELECT a.num_docume, a.num_docalt, a.cod_genera,
                       a.nom_destin, a.cod_ciudad, a.dir_destin, 
                       a.num_destin, a.fec_citdes, a.hor_citdes
                  FROM ".BASE_DATOS.".tab_despac_cordes a, 
                       ".BASE_DATOS.".tab_despac_despac b 
                 WHERE a.num_despac = b.cod_manifi AND
                       b.num_despac = '".$_AJAX['num_despac']."' ";
    
    $consulta = new Consulta( $mQuery, $this -> conexion );
    $_DESTINDATA = $consulta -> ret_matriz();


    foreach ($_DESTINDATA as $fKey => $fData) {
      $mDatDestin[] = "Destinatario: ".($fKey + 1)."\nNum Documento: ".$fData["num_docume"].", Documento Alt: ".$fData["num_docalt"]." Destino: ".$fData["nom_destin"]."\n".
                      "Direccion Destino: ".$fData["dir_destin"]." Num Destinatario: ".$fData["num_destin"]." Fecha: ".$fData["fec_citdes"]." Hora: ".$fData["hor_citdes"];
    }

    $mSelect = "SELECT obs_despac
                  FROM ".BASE_DATOS.".tab_despac_despac
                 WHERE num_despac = '".$_AJAX['num_despac']."' ";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_OBSERV = $consulta -> ret_matriz();
    
    
    $mHtml = "<div class='StyleDIV' id='DestinID'>";
    
    $mHtml .= "<textarea name='obs' id='obsID' cols='100' rows='9' readonly>".$_OBSERV[0]['obs_despac']."\n\n\n".utf8_decode( (join("\n",  $mDatDestin))  )."</textarea>";
    
    $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="left" width="100%" class="label-info" colspan="10">Destinatarios asignados al Despacho. Para agregar otro haga click <a style="color:#285C00; text-decoration:none; cursor:pointer;" onclick="AddGrid();">aqu&iacute;</a><br>&nbsp;</td>';
    $mHtml .= '</tr>';
    
    $mHtml  .= '</table>';


    # normal
    $count = 0;
    foreach( $_DESTIN as $row )
    {
      $_AJAX['counter'] = $count;
      $mHtml  .= $this -> ShowDestin( $_AJAX, $row );   
      $numDocume[$count] = $row[0];
      $count++;
      
    }

    #descripcion
    $countb = $count;
    foreach( $_DESTINDATA as $rowx )
    {
      $bandera = 0;
      for ($i=0; $i<$count; $i++)
      {
        $bandera = $rowx[0] == $numDocume[$i] ? $bandera+1 : $bandera;
      }
      if ($bandera == '0')
      {
        $_AJAX['counter'] = $countb;
        $mHtml  .= $this -> ShowDestin( $_AJAX, $rowx ); 
        $countb++;
      }
    }

    $_AJAX['counter'] = $countb;
    $mHtml  .= $this -> ShowDestin( $_AJAX );
    
    $mHtml .= '<input type="hidden" id="counterID" value="'.$countb.'" />';
    $mHtml .= "</div>";
    
    $mHtml  .= "<div class='StyleDIV'>";
    $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td align="left" width="100%" class="label-info" colspan="10">Destinatarios asignados al Despacho. Para agregar otro haga click <a style="color:#285C00; text-decoration:none; cursor:pointer;" onclick="AddGrid();">aqu&iacute;</a><br>&nbsp;</td>';
    $mHtml .= '</tr>';
    
    $mHtml  .= '</table>';
    
    $mHtml .= '<br><center><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InserDestin();"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="crmButton small save" type="button" id="CancelID" value="Cancelar" onclick="$(\'#PopUpID\').dialog(\'close\');"/></center>';
    $mHtml .= "</div>";
   
    echo $mHtml;
  }
  
  private function VerifyDestin( $num_despac, $num_docume )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_despac_destin 
              WHERE num_despac = '".$num_despac."' 
                AND num_docume ='".$num_docume."'";
    $consulta = new Consulta( $mSql, $this -> conexion );
		$VER = $consulta -> ret_matriz();
    
    return sizeof( $VER ) > 0 ? true : false;
  }
  
  protected function InserDestin( $_AJAX )
  {
    // echo "<pre>";
    // print_r( $_AJAX );
    // echo "</pre>";
    // die();
    
    $consec = new Consulta( "SELECT 1", $this -> conexion, "BR" );
    
    $num_despac = $_AJAX['num_despac'];
    
    
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_despac_destin
                      WHERE num_despac = '".$num_despac."'
                        AND ( fec_findes = '0000-00-00 00:00:00' 
                         OR fec_findes IS NULL )";
    //echo $mDelete."<hr>";                     
    $consulta = new Consulta( $mDelete, $this -> conexion, "R" );
    
    for( $k = 0; $k <= $_AJAX['counter']; $k++ )
    {
      if( $_AJAX[ 'num_factur'.$k ] != NULL && $_AJAX[ 'num_factur'.$k ] != 'undefined' )
      {
        if( !$this -> VerifyDestin($num_despac, $_AJAX[ 'num_factur'.$k ] ) )
        {
          $cod_genera = $_AJAX[ 'cod_genera'.$k ] != "" && $_AJAX[ 'cod_genera'.$k ] != NULL ? "'".$_AJAX[ 'cod_genera'.$k ]."'" : "NULL" ;
          $cod_ciudad = $_AJAX[ 'cod_ciudad'.$k ] != "" && $_AJAX[ 'cod_ciudad'.$k ] != NULL ? "'".$_AJAX[ 'cod_ciudad'.$k ]."'" : "NULL" ;
          $mInsert[] = " (
                          '".$_AJAX['num_despac']."', '".$_AJAX[ 'num_factur'.$k ]."','".$_AJAX[ 'num_docalt'.$k ]."', ".$cod_genera.",  
                          '".$_AJAX[ 'nom_destin'.$k ]."', ".$cod_ciudad.",'".$_AJAX[ 'dir_destin'.$k ]."', '".$_AJAX[ 'nom_contac'.$k ]."',  
                          '".$_AJAX[ 'fec_citdes'.$k ]."', '".$_AJAX[ 'hor_citcar'.$k ]."','".$_SESSION['datos_usuario']['cod_usuari']."', NOW(),
                          '1'
                          )";
           
          //$consulta = new Consulta( $mInsert, $this -> conexion, "R" );
        }
      }
    }
    
    $mInsertF = "INSERT INTO ".BASE_DATOS.".tab_despac_destin
                    (
                      num_despac, num_docume, num_docalt, cod_genera,
                      nom_destin, cod_ciudad, dir_destin, num_destin, 
                      fec_citdes, hor_citdes, usr_creaci, fec_creaci,
                      ind_modifi 
                    )
                   VALUES ".join(',', $mInsert).";";
                   
    if( sizeof($mInsert) <= 0) 
        $mInsertF = "SELECT 1";
        
    if( $consulta = new Consulta( $mInsertF, $this -> conexion, "RC" ) )
    {
      $mHtml  .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
      $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >La Informaci&oacute;n ha Sido Insertada Exitosamente.</i></td>';
      $mHtml .= '</tr>';
      $mHtml .= '</table></center>';
      echo $mHtml;
    }
    
  }
  
  private function getCiudad( $cod_ciudad = NULL )
  {
    $mSql = "SELECT cod_ciudad, UPPER( nom_ciudad ) AS nom_ciudad
               FROM ".BASE_DATOS.".tab_genera_ciudad 
              WHERE ind_estado = '1'";
    if( $cod_ciudad != NULL )
    {
      $mSql .= " AND cod_ciudad = ".$cod_ciudad;      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function getGenera( $cod_transp, $cod_genera = NULL )
  {
    $query = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tercer c
               WHERE a.cod_tercer = b.cod_tercer AND
                     a.cod_tercer = c.cod_tercer AND
                     c.cod_transp = '".$cod_transp."' AND
                     b.cod_activi = ".COD_FILTRO_CLIENT."";
    if( $cod_genera != NULL )
    {
      $query .= " AND a.cod_tercer = '".$cod_genera."'";
    }
    $query .= " ORDER BY 2 ASC";
    
    $consulta = new Consulta( $query, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  private function GenerateSelect( $arr_select, $name, $key = NULL, $events = NULL, $disabled = NULL )
  {
    $mHtml  = '<select name="'.$name.'" id="'.$name.'ID" '.$events.' '.$disabled.'>';
    $mHtml .= '<option value="">- Seleccione -</option>';
    foreach( $arr_select as $row )
    {
      $selected = '';
      if( $row[0] == $key )
        $selected = 'selected="selected"';
      
      $mHtml .= '<option value="'.$row[0].'" '.$selected.'>'.utf8_encode( $row[1] ).'</option>';
    }
    $mHtml .= '</select>';
    return $mHtml;
  }
  
  protected function ShowDestin( $_AJAX, $mData = NULL )
  {
    $readonly = '';
    if( $mData != NULL )
    {
      if( $mData['fec_findes'] != '0000-00-00 00:00:00' && $mData['fec_findes'] != '' )
      {
        $readonly = ' disabled ';
      }
    }
    
    if( $_AJAX['counter'] == '' )
    {
      $_AJAX['counter'] = 0;
    }
    
    $_AJAX['cod_transp'] = '860068121';
    
    $style = $_AJAX['counter'] % 2 == 0 ? 'cellInfo1' : 'cellInfo2' ;
    
    $ciudad = $this -> getCiudad();
    $genera = $this -> getGenera( $_AJAX['cod_transp'] );
    
    
    $mHtml  .= '
    <script>
      $(function() {
        
        $( ".date" ).datepicker({ minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).') });
        
        $( ".time" ).timepicker();
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");
        $( ".time" ).mask("Hn:Nn:Nn");

      });
    </script>';
    
    $mHtml .= '<div id="datdes'.$_AJAX['counter'].'ID">';
      $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left" colspan="5" class="cellHead" width="10%">DESTINATARIO No. '. ( $_AJAX['counter'] + 1 );
        if( $readonly == '' )
        {
          $mHtml .= '&nbsp;&nbsp;&nbsp;<a style="color:#FFFFFF; text-decoration:none; cursor:pointer;" onclick="DropGrid(\''.$_AJAX['counter'].'\');">[Eliminar]</a>';
        }
        $mHtml .= '</td>';   
      $mHtml .= '</tr>';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">* No. FACTURA/ REMISION</td>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">DOC. ALTERNO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="40%">GENERADOR</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">DESTINATARIO</td>';
        
      $mHtml .= '</tr>';

      
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' size="10" name="num_factur'.$_AJAX['counter'].'" id="num_factur'.$_AJAX['counter'].'ID" value="'.$mData['num_docume'].'" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' size="10" name="num_docalt'.$_AJAX['counter'].'" id="num_docalt'.$_AJAX['counter'].'ID" value="'.$mData['num_docalt'].'" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'">'.$this -> GenerateSelect( $genera, 'cod_genera'.$_AJAX['counter'], $mData['cod_genera'], $readonly ).'</td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' size="30" name="nom_destin'.$_AJAX['counter'].'" id="nom_destin'.$_AJAX['counter'].'ID" value="'.$mData['nom_destin'].'" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml  .= '</table>';
      
      $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
       
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">* CIUDAD</td>';
        $mHtml .= '<td align="center" class="cellHead" width="30%">DIRECCI&Oacute;N</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">NUMERO CONTACTO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">* FECHA CITA DESCARGUE</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">* HORA CITA DESCARGUE</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="'.$style.'">'.$this -> GenerateSelect( $ciudad, 'cod_ciudad'.$_AJAX['counter'], $mData['cod_ciudad'], $readonly ).'</td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' size="40" name="dir_destin'.$_AJAX['counter'].'" value="'.$mData['dir_destin'].'" id="dir_destin'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' size="15" name="nom_contac'.$_AJAX['counter'].'" value="'.$mData['num_destin'].'" id="nom_contac'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' name="fec_citdes'.$_AJAX['counter'].'" value="'.$mData['fec_citdes'].'" id="fec_citdes'.$_AJAX['counter'].'ID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" '.$readonly.' name="hor_citdes'.$_AJAX['counter'].'" value="'.$mData['hor_citdes'].'" id="hor_citcar'.$_AJAX['counter'].'ID" class="time" size="15" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '</table><br>';
      
    $mHtml .= '</div>';
    if( $_AJAX['ind_ajax'] == '1' )
    {
      echo $mHtml;
    }
    else
    {
      return $mHtml;
    }
  }
  
  protected function mainList( $_AJAX )
  {
    echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['Standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/functions.js\"></script>\n";
    
    $mSql = "SELECT * 
               FROM (
                SELECT a.num_despac, IF( z.num_desext IS NOT NULL , z.num_desext,  'N/A' ) as num_desext , IF( f.ind_modifi =  '1', 'ACTUALIZADO', 'PENDIENTE' ) as ind_modifi, b.num_placax, a.cod_manifi, a.fec_despac, c.nom_tipdes, d.nom_ciudad AS nom_ciuori, e.nom_ciudad AS nom_ciudes
                  FROM satt_faro.tab_despac_despac a
             LEFT JOIN satt_faro.tab_despac_sisext z ON a.num_despac = z.num_despac
             LEFT JOIN satt_faro.tab_despac_destin f ON a.num_despac = f.num_despac, satt_faro.tab_despac_vehige b, satt_faro.tab_genera_tipdes c, satt_faro.tab_genera_ciudad d, satt_faro.tab_genera_ciudad e
                 WHERE a.cod_tipdes = c.cod_tipdes
                   AND a.cod_ciuori = d.cod_ciudad
                   AND a.cod_ciudes = e.cod_ciudad
                   AND a.num_despac = b.num_despac
                   AND b.cod_transp =  '860068121'
                   AND a.fec_llegad IS NULL 
                   AND a.ind_anulad !=  'A'
                   AND a.fec_despac BETWEEN  '".$_AJAX['fec_inicia']." 00:00:00' AND '".$_AJAX['fec_finali']." 23:59:59'
                  ORDER BY 1 , f.ind_modifi DESC
                  ) AS w WHERE 1 = 1
              GROUP BY w.num_despac";
    
    echo "<div style='display:none;'>".$mSql."</div>";
    
    $_SESSION["queryXLS"] = $mSql;
    $list = new DinamicList($this->conexion, $mSql, 1 );
    $list->SetClose('no');
    $list->SetHeader("Despacho", "field:num_despac; type:link; onclick:SetDestinatarios( $(this) )");
    $list->SetHeader("No. Viaje", "field:num_desext");
    $list->SetHeader("Estado","field:ind_modifi; width:1%", array_merge( $this -> ind_estado_, $this -> ind_estado ) );
    $list->SetHeader("Placa", "field:num_placax");
    $list->SetHeader("Manifiesto", "field:cod_manifi");
    $list->SetHeader("Fecha", "field:fec_despac");
    $list->SetHeader("Tipo Despacho", "field:nom_tipdes" );
    $list->SetHeader("Origen", "field:nom_ciuori");
    $list->SetHeader("Destino","field:nom_ciudes");
    
    $list->Display($this->conexion);

    $_SESSION["DINAMIC_LIST"] = $list;
    echo "<td>";
    echo $list->GetHtml();
    echo "</td>";
  }
  
}

$proceso = new AjaxDespacDestin();
 ?>
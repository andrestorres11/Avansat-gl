<?php

class AjaxInsertProtocolo
{
  var $conexion;
  var $ind_estado = array();
  var $ind_estado_ = array();
  
  public function __construct()
  {
    $this -> ind_estado_[0][0] = '';
    $this -> ind_estado_[0][1] = '--';
    $this -> ind_estado[1][0] = '2';
    $this -> ind_estado[1][1] = 'INACTIVO';
    $this -> ind_estado[2][0] = '1';
    $this -> ind_estado[2][1] = 'ACTIVO';
    
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );

    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
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
  
  private function VerifyContacto( $cod_protoc, $cod_ciuori, $cod_ciudes, $cod_produc, $cod_tipdes )
  {
    $mSelect = "SELECT 1 FROM tab_protoc_matcom WHERE cod_tipdes = '".$cod_tipdes."' AND cod_protoc = '".$cod_protoc."'";
    
    if( $cod_ciuori == '' )
      $mSelect .= " AND cod_ciuori = '0' ";
    else
      $mSelect .= " AND cod_ciuori = '".$cod_ciuori."'";
  
    if( $cod_ciudes == '' )
      $mSelect .= " AND cod_ciudes = '0' ";
    else
      $mSelect .= " AND cod_ciudes = '".$cod_ciudes."'";
  
    if( $cod_produc == '' )
      $mSelect .= " AND cod_produc = '' ";
    else
      $mSelect .= " AND cod_produc = '".$cod_produc."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $MATCOM = $consulta -> ret_matriz();
    
    if( sizeof( $MATCOM ) > 0 )
      return false;
    else
      return true;
  }
  
  protected function ChangeProtocolo( $_AJAX )
  {
    
    $mDelete = "DELETE FROM ". BASE_DATOS .".tab_protoc_matcom WHERE cod_protoc = '".$_AJAX['cod_protoc']."'";
    $consulta = new Consulta( $mDelete, $this -> conexion, "BR" );
    
    for( $i = 0; $i < $_AJAX['counter']; $i++ )
    {
      $_AJAX['cod_produc'.$i] = str_replace( '/-/', '&', $_AJAX['cod_produc'.$i] );
      if( $this -> VerifyContacto( $_AJAX['cod_protoc'], $_AJAX['cod_ciuori'.$i], $_AJAX['cod_ciudes'.$i], $_AJAX['cod_produc'.$i], $_AJAX['cod_tipdes'.$i] ) )
      {
        $mSelect = "SELECT MAX(num_consec) FROM ". BASE_DATOS .".tab_protoc_matcom WHERE cod_protoc = '".$_AJAX['cod_protoc']."'";
        $consulta = new Consulta( $mSelect, $this -> conexion, "R" );
        $consec = $consulta -> ret_matriz();
        
        $mInsert = "INSERT INTO ". BASE_DATOS .".tab_protoc_matcom
                              ( cod_protoc, num_consec, cod_ciuori, cod_ciudes,
                                cod_produc, cod_tipdes, ema_conpri, ema_otrcon,
                                usr_creaci, fec_creaci
                              )
                        VALUES( '".$_AJAX['cod_protoc']."', '".($consec[0][0]+1)."', '".$_AJAX['cod_ciuori'.$i]."', '".$_AJAX['cod_ciudes'.$i]."',  
                                '".$_AJAX['cod_produc'.$i]."', '".$_AJAX['cod_tipdes'.$i]."', '".$_AJAX['ema_conpri'.$i]."', '".$_AJAX['ema_otrcon'.$i]."', 
                                '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                              )";
        $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
      }
    }
    
    $mUpdate = "UPDATE ". BASE_DATOS .".tab_genera_protoc
                   SET des_protoc = '".utf8_decode($_AJAX['des_protoc'])."', 
                       tex_protoc = '".utf8_decode($_AJAX['tex_protoc'])."', 
                       /*cod_respon = '".$_AJAX['cod_respon']."',*/
                       tie_protoc = '".$_AJAX['tie_respon']."', 
                       ind_activo = '".$_AJAX['ind_estado']."',
                       usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
                       fec_modifi = NOW()
                 WHERE cod_protoc = '".$_AJAX['cod_protoc']."'";
    $consulta = new Consulta( $mUpdate, $this -> conexion, "R" );
    if(  $insercion = new Consulta( "COMMIT", $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }  
  protected function FormEdit( $_AJAX )
  {
    $this -> Style();
    
    // $respon = $this -> getRespon();
    
    $mSelect = "SELECT cod_protoc, des_protoc, tex_protoc, 
                       cod_respon, tie_protoc, ind_activo 
                  FROM ". BASE_DATOS .".tab_genera_protoc
                 WHERE cod_protoc = '".$_AJAX['cod_protoc']."'";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_PROTOC = $consulta -> ret_matriz();
                 
    $mHtml =  '<div class="StyleDIV" id="FormDivID">';
      $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
      
        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Descripci&oacute;n:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" size="40" name="des_protoc" id="des_protoc_ID" value="'.$_PROTOC[0]['des_protoc'].'" placeholder="Descripci&oacute;n del Protocolo"/></td>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> Texto:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="40%" class="TRform" ><textarea style="resize: none;" cols="30" rows="2" name="tex_protoc" id="tex_protoc_ID" placeholder="Texto del Protocolo" >'.$_PROTOC[0]['tex_protoc'].'</textarea></td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
          /*$mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Responsable:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" >'.$this -> GenerateSelect( $respon, 'cod_respon_', $_PROTOC[0]['cod_respon'], NULL ).'</td>';*/
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Tiempo(Mins):&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" size="10" maxlength="3" name="tie_respon" id="tie_respon_ID" value="'.$_PROTOC[0]['tie_protoc'].'" placeholder="Minutos" /></td>';
        $mHtml .= '</tr>';
      
        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Estado:&nbsp;&nbsp;</b> <input type="hidden" name="cod_protoc" id="cod_protoc_ID" value="'.$_AJAX['cod_protoc'].'"/></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" >'.$this -> GenerateSelect( $this -> ind_estado, 'ind_estado_', $_PROTOC[0]['ind_activo'], NULL ).'</td>';
        $mHtml .= '</tr>';
        
      $mHtml .= '</table>';
      
      /* MATRIZ DE COMUNICACION */
      
      $mSql = "SELECT num_consec, cod_ciuori, cod_ciudes, 
                      cod_produc, cod_tipdes, ema_conpri,
                      ema_otrcon 
                 FROM ".BASE_DATOS.".tab_protoc_matcom 
                WHERE cod_protoc = '".$_AJAX['cod_protoc']."' ORDER BY num_consec";
      
      $consulta = new Consulta( $mSql, $this -> conexion );
      $_CONTACTOS = $consulta -> ret_matriz();

      $mHtml .= '<br><br><table id="matcomID" width="100%" border="0" cellpadding="0" cellspacing="0">';    
      
      $mHtml .= '<tr>'; 
        $mHtml .= '<td align="center" class="cellHead" width="100%" colspan="7">CONTACTOS ASIGNADOS AL PROTOCOLO.&nbsp;&nbsp;&nbsp; <a onclick="AddGrid();" style="text-decoration:none; cursor:pointer; color:#FFFFFF;">[Agregar Otro]</a></td>';
      $mHtml .= '</tr>'; 
      
      if( sizeof( $_CONTACTOS ) > 0 )
      {
        $_AJAX['counter'] = 0;
        foreach( $_CONTACTOS as $row )
        {
          $_AJAX['counter']++;
          $mHtml .= $this -> generateDynamicGrid( $_AJAX, $row );
        }
      }
      else
      {
         $_AJAX['counter'] = 1;
         $mHtml .= $this -> generateDynamicGrid( $_AJAX );
      }
      
      $mHtml .= '</table>';
      $mHtml .= '<input type="hidden" name="counter" id="counterID" value="'.$_AJAX['counter'].'" />';
      
    $mHtml .= '</div>';
    
    echo $mHtml;
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
  
  private function getProduc( $cod_produc = NULL )
  {
    $mSql = "SELECT cod_produc, UPPER( nom_produc ) AS nom_produc
               FROM ".BASE_DATOS.".tab_genera_produc 
              WHERE ind_estado = '1'";
    if( $cod_produc != NULL && $cod_produc != '' )
    {
      $mSql .= " AND cod_produc = '".$cod_produc."'";      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function getTipdes( $cod_tipdes = NULL )
  {
    $mSql = "SELECT cod_tipdes, UPPER( nom_tipdes ) AS nom_tipdes
               FROM ".BASE_DATOS.".tab_genera_tipdes 
              WHERE 1 = 1";
    if( $cod_tipdes != NULL && $cod_tipdes != '' )
    {
      $mSql .= " AND cod_tipdes = '".$cod_tipdes."'";      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function generateDynamicGrid( $_AJAX, $values = NULL )
  {
    $ciuori = $this -> getCiudad( );
    $ciudes = $this -> getCiudad( );
    $produc = $this -> getProduc( );
    $tipdes = $this -> getTipdes( );
    
    $_AJAX['counter'] -= 1; 
    $style = $_AJAX['counter'] % 2 == 0 ? 'cellInfo1' : 'cellInfo2' ;
    $mHtml = '<tr>'; 
    $mHtml .= '<td align="center" class="'.$style.'">Origen:<br>'.$this -> GenerateSelect( $ciuori, 'cod_ciuori'.$_AJAX['counter'], $values['cod_ciuori'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">Destino:<br>'.$this -> GenerateSelect( $ciudes, 'cod_ciudes'.$_AJAX['counter'], $values['cod_ciudes'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">Producto:<br>'.$this -> GenerateSelect( $produc, 'cod_produc'.$_AJAX['counter'], $values['cod_produc'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">* Tipo Despacho:<br>'.$this -> GenerateSelect( $tipdes, 'cod_tipdes'.$_AJAX['counter'], $values['cod_tipdes'], NULL ).'</td>';
    $mHtml .= '<td align="center" class="'.$style.'">Contacto Principal:<br><textarea name="ema_conpri'.$_AJAX['counter'].'" id="ema_conpri'.$_AJAX['counter'].'ID" cols="15" rows="1" >'.$values['ema_conpri'].'</textarea></td>';
    $mHtml .= '<td align="center" class="'.$style.'">Otros Contactos:<br><textarea name="ema_otrcon'.$_AJAX['counter'].'" id="ema_otrcon'.$_AJAX['counter'].'ID" cols="15" rows="1" >'.$values['ema_otrcon'].'</textarea></td>';
    $mHtml .= '</tr>'; 
    
    if( $_AJAX['ind_ajax'] == 'yes' )
      echo $mHtml;
    else
      return $mHtml;
  }

  private function GenerateSelect( $arr_select, $name, $key = NULL, $events = NULL  )
  {
    $mHtml  = '<select name="'.$name.'" id="'.$name.'ID" '.$events.'>';
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
  
  private function getRespon( $cod_respon = NULL )
  {
    $query = "SELECT a.cod_respon, a.nom_respon
                FROM ".BASE_DATOS.".tab_genera_respon a
               WHERE a.ind_activo = '1'";
    
    if( $cod_respon != NULL )
    {
      $query .= " AND a.cod_respon = '".$cod_respon."' ";
    }
    
    $query .= " ORDER BY 2";
    
    $consulta = new Consulta( $query, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  protected function MainLoad( $_AJAX )
  {
    // $respon = $this -> getRespon();
    
    $mHtml =  '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Descripci&oacute;n:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" size="60" name="des_protoc" id="des_protocID" placeholder="Descripci&oacute;n del Protocolo"/></td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> Texto:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><textarea cols="50" rows="2" name="tex_protoc" id="tex_protocID" placeholder="Texto del Protocolo" ></textarea></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
        /*$mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Responsable:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="20%" class="TRform" >'.$this -> GenerateSelect( $respon, 'cod_respon', NULL, NULL ).'</td>';*/
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Tiempo(Mins):&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" size="10" maxlength="3" name="tie_respon" id="tie_responID" placeholder="Minutos" /></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center"  colspan="4" width="100%" class="TRform" ><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertProtocolo();"/></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" ><div id="messageID" style="display:none;"></div></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp; <b>NOTA:</b> PARA ASIGNAR CORREOS A LA MATRIZ DE COMUNICACI&Oacute;N Y/O ACTUALIZAR LA INFORMACI&Oacute;N DEL PROTOCOLO, HAGA CLIC EN EL CONSECUTIVO. </td>';
      $mHtml .= '</tr>';
      
    
    $mHtml .= '</table>';
    
    $mHtml .=  '<center> <div id="mainListID">';
		$mHtml .= '</div></center>';
    
    echo $mHtml;
  }
  
  protected function Insert( $_AJAX )
  {
    $mSelect = "SELECT MAX( cod_protoc ) FROM ". BASE_DATOS .".tab_genera_protoc";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $con = $consulta -> ret_matriz();
    
    $consec = $con[0][0] + 1;
    
    $mInsert = "INSERT INTO ". BASE_DATOS .".tab_genera_protoc
                       ( cod_protoc, des_protoc, tex_protoc,
                         tie_protoc, usr_creaci, fec_creaci
                       )
                VALUES ( '".$consec."', '".utf8_decode($_AJAX['des_protoc'])."', '".utf8_decode($_AJAX['tex_protoc'])."',
                         '".$_AJAX['tie_respon']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                       )";
    
    if( $consulta = new Consulta( $mInsert, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  protected function mainList( $_AJAX )
  {
    echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['Standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/functions.js\"></script>\n";
    
    $mSql = "SELECT a.cod_protoc, a.des_protoc, a.tex_protoc, 
                    a.tie_protoc,
                    IF( a.ind_activo != '1', 'INACTIVO', 'ACTIVO' ) AS ind_activo
               FROM ". BASE_DATOS .".tab_genera_protoc a
              WHERE 1 = 1 ";
    //ema_pronac ema_prourb ema_proexp 	ema_proimp
    $_SESSION["queryXLS"] = $mSql;
    $list = new DinamicList($this->conexion, $mSql, 1 );
    $list->SetClose('no');
    $list->SetHeader("Consecutivo", "field:a.cod_protoc; width:1%; type:link; onclick:EditProtocolo( $(this) )");
    $list->SetHeader("Descripcion", "field:a.des_protoc; width:1%");
    $list->SetHeader("Texto", "field:a.tex_protoc; width:1%");
    //$list->SetHeader("Responsable", "field:b.nom_respon; width:1%" );
    $list->SetHeader("Tiempo(Mins)", "field:a.tie_protoc; width:1%");
    $list->SetHeader("Estado","field:a.ind_activo; width:1%", array_merge( $this -> ind_estado_, $this -> ind_estado ) );

    $list->Display($this->conexion);

    $_SESSION["DINAMIC_LIST"] = $list;
    //echo "<td>";
    echo $list->GetHtml();
    //echo "</td>";
   
    
  }
  
}

$proceso = new AjaxInsertProtocolo();
 ?>
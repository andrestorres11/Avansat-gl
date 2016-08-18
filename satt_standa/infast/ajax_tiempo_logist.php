<?php
class AjaxTiemposLogisticos
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
  
  private function GetProduc( $cod_produc = NULL )
  {
    $mSelect = "SELECT cod_produc, nom_produc 
                  FROM ".BASE_DATOS.".tab_genera_produc 
                 WHERE ind_estado = '1' ";
    
    if( $cod_produc != NULL )
    {
      $mSelect .= " AND cod_produc = '".$cod_produc."'";
    }
    
    $mSelect .= " ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  private function GetClases( $cod_clasex = NULL )
  {
    $mSelect = "SELECT cod_clasex, nom_clasex
                  FROM ".BASE_DATOS.".tab_vehige_clases 
                 WHERE ind_estado = '1' ";
    
    if( $cod_clasex != NULL )
    {
      $mSelect .= " AND cod_clasex = '".$cod_clasex."'";
    }
    
    $mSelect .= " ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  protected function MainLoad( $_AJAX )
  {
    /***************************/
    $_PRODUC = $this -> GetProduc();
    $_CLASEX = $this -> GetClases();
    /***************************/
    
    $mHtml =  '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Producto:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" >'.$this -> GenerateSelect( $_PRODUC, 'cod_produc' ).'</td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Clase:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" >'.$this -> GenerateSelect( $_CLASEX, 'cod_clasex' ).'</td>';  
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Horas Estimadas:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input name="hor_estima" id="hor_estimaID" size="6" maxlength="5" /></td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" >&nbsp;</td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" >&nbsp;</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center"  colspan="4" width="100%" class="TRform" ><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertHomolo();"/></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" ><div id="messageID" style="display:none;"></div></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp;Si desea Editar o Eliminar alguna Configuraci&oacute;n, haga click en el campo "EDITAR".</td>';
      $mHtml .= '</tr>';
      
    
    $mHtml .= '</table>';
    
    $mHtml .=  '<center> <div id="mainListID">';
		$mHtml .= '</div></center>';
    
    echo $mHtml;
  }
  
  protected function ValidateHomolo( $mData )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_produc_tiempo 
                 WHERE cod_produc = '".(str_replace('/-/', '&', $mData['cod_produc'] ) )."'
                   AND cod_clasex = '".$mData['cod_clasex']."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $result = $consulta -> ret_matriz();
    
    echo $to_return = sizeof( $result ) > 0 ? 'yes' : 'no';
  }
  
  protected function InsertHomolo( $_AJAX )
  {
    $mSql = "INSERT INTO ".BASE_DATOS.".tab_produc_tiempo
                       ( cod_produc, cod_clasex, hor_estima,
                         usr_creaci, fec_creaci ) 
                 VALUES( '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."','".$_AJAX['cod_clasex']."','".$_AJAX['hor_estima']."', 
                         '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                       )";
    
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  protected function ChangeTiempos( $_AJAX )
  {
    $mSql = "UPDATE ".BASE_DATOS.".tab_produc_tiempo 
                SET hor_estima = '".$_AJAX['hor_estima']."',
                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                    fec_modifi = NOW() 
              WHERE cod_produc = '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."' 
                AND cod_clasex = '".$_AJAX['cod_clasex']."'";
                
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  protected function DropTiempos( $_AJAX )
  {
    $mSql = "DELETE FROM ".BASE_DATOS.".tab_produc_tiempo
              WHERE cod_produc = '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."' 
                AND cod_clasex = '".$_AJAX['cod_clasex']."'";
                
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  protected function FormEdit( $mData )
  {
    /***************************/
    $_PRODUC = $this -> GetProduc( str_replace( '/-/', '&', $mData['cod_produc'] ) );
    $_CLASEX = $this -> GetClases( $mData['cod_clasex'] );
    /***************************/
    
    $mSelect = "SELECT hor_estima 
                  FROM ".BASE_DATOS.".tab_produc_tiempo 
                 WHERE cod_produc = '".str_replace( '/-/', '&', $mData['cod_produc'] )."'
                   AND cod_clasex = '".$mData['cod_clasex']."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TIEMPOX = $consulta -> ret_matriz();
    
    $mHtml =  '<center> <div id="mainFormID" class="StyleDIV"><table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Producto:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" readonly name="nom_produc_" id="nom_produc_ID" value="'.$_PRODUC[0][1].'" /><input type="hidden" name="cod_produc_" id="cod_produc_ID" value="'.$_PRODUC[0][0].'" /></td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Clase:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" readonly name="nom_clasex_" id="nom_clasex_ID" value="'.$_CLASEX[0][1].'" /><input type="hidden" name="cod_clasex_" id="cod_clasex_ID" value="'.$_CLASEX[0][0].'" /></td>';  
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Horas Estimadas:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input name="hor_estima_" id="hor_estima_ID" size="6" maxlength="5" value="'.$_TIEMPOX[0][0].'" /></td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" >&nbsp;</td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" >&nbsp;</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" ><div id="messageID" style="display:none;"></div></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp;Si desea Editar o Eliminar alguna Configuraci&oacute;n, haga click en el campo "EDITAR".</td>';
      $mHtml .= '</tr>';
      
    
    $mHtml .= '</table></div></center>';
    
    echo $mHtml;
    
  }
  
  protected function mainList( $_AJAX )
  {
    
    echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['Standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/functions.js\"></script>\n";
    
    $mSelect = "SELECT c.nom_produc, b.nom_clasex, a.hor_estima, 'Editar' as editar, c.cod_produc, b.cod_clasex
                  FROM ". BASE_DATOS .".tab_produc_tiempo a 
            INNER JOIN ". BASE_DATOS .".tab_vehige_clases b
                    ON a.cod_clasex = b.cod_clasex
            INNER JOIN ". BASE_DATOS .".tab_genera_produc c
                    ON a.cod_produc = c.cod_produc";
    
    $_SESSION["queryXLS"] = $mSelect;
    
    $list = new DinamicList( $this -> conexion, $mSelect, 1 );
    $list -> SetClose('no');
    $list -> SetHeader( "Producto", "field:c.nom_produc; width:1%" );
    $list -> SetHeader( "Clase",    "field:b.nom_clasex; width:1%" );
    $list -> SetHeader( "Tiempo",   "field:a.hor_estima; width:1%" );
    $list -> SetHeader( "Editar",   "field:editar; width:1%; type:link; onclick:EditHomolo( $(this) );" );
    $list -> SetHidden( "cod_produc", "cod_produc" );
    $list -> SetHidden( "cod_clasex", "cod_clasex" );
    
    $list->Display($this->conexion);

    $_SESSION["DINAMIC_LIST"] = $list;
    echo $list->GetHtml();
  }
}

$proceso = new AjaxTiemposLogisticos();
 ?>
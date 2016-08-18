<?php
class AjaxCanales
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

  private function DropCanal( $_AJAX )
  {
    $mSql = "DELETE FROM ".BASE_DATOS.".tab_genera_canalx
                   WHERE cod_canalx = '".str_replace( '/-/', '&', $_AJAX['cod_canalx'] )."' 
				     AND cod_produc = '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."'";
    
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  private function EditCanal( $_AJAX )
  {
    $mSql = "UPDATE ".BASE_DATOS.".tab_genera_canalx
                SET nom_canalx = '".$_AJAX['nom_canalx']."',
                    ind_estado = '".$_AJAX['ind_estado']."'
              WHERE cod_canalx = '".str_replace( '/-/', '&', $_AJAX['cod_canalx'] )."'
			    AND cod_produc = '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."'";
    
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  private function InsertCanal( $_AJAX )
  {
	$mSelect = "SELECT MAX(con_consec) FROM ".BASE_DATOS.".tab_genera_canalx";
	$consulta = new Consulta( $mSelect, $this -> conexion );
    $_consec = $consulta -> ret_matriz();
	
	$_id = (int)$_consec[0][0] + 1;
	
    $mSql = "INSERT INTO ".BASE_DATOS.".tab_genera_canalx
                       ( con_consec, cod_canalx, cod_produc, 
					     nom_canalx, ind_estado, usr_creaci, 
						 fec_creaci ) 
                 VALUES( '".$_id."', '".str_replace( '/-/', '&', $_AJAX['cod_canalx'] )."', '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."','".$_AJAX['nom_canalx']."',
						 '1', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
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
  
  private function ValidateCanal( $_AJAX )
  {
    // echo "<pre>";
    // print_r( $_AJAX );
    // echo "</pre>";
    
    $mSql = "SELECT 1 
               FROM ".BASE_DATOS.".tab_genera_canalx 
              WHERE cod_canalx = '".str_replace( '/-/', '&', $_AJAX['cod_canalx'] )."' 
			    AND cod_produc = '".str_replace( '/-/', '&', $_AJAX['cod_produc'] )."' ";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $_canalx = $consulta -> ret_matriz();
    
    if( sizeof( $_canalx ) > 0 )
      echo 'yes';
    else
      echo 'no';
  }
    
  protected function FormEdit( $_AJAX )
  {
	$mSelect = "SELECT cod_canalx, nom_canalx, ind_estado 
                  FROM ". BASE_DATOS .".tab_genera_canalx
                 WHERE cod_canalx = '".str_replace( '/-/', '&', $_AJAX['cod_canalx'] )."'";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_canalx = $consulta -> ret_matriz();
      
    $mHtml =  '<div class="StyleDIV" id="FormDivID">';
      $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
      
        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Producto:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" >'.$this -> SelectProductoOther( $_AJAX['cod_produc'] ).'</td>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * C&oacute;digo:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" maxlenght="8" size="15" name="cod_canalx_" id="cod_canalx_ID" value="'.$_canalx[0]['cod_canalx'].'" readonly/></td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Canal:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" maxlenght="30" size="35" name="nom_canalx_" id="nom_canalx_ID" value="'.$_canalx[0]['nom_canalx'].'"/></td>';
		  $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Estado:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" >'.$this -> GenerateSelect( $this -> ind_estado, 'ind_estado_', $_canalx[0]['ind_estado'], NULL ).'</td>';
        $mHtml .= '</tr>';
        
      $mHtml .= '</table>';
    $mHtml .= '</div>';
    
    echo $mHtml;
  }
  
  private function GenerateSelect( $arr_select, $name, $key = NULL, $events = NULL, $ind_ajax = false, $ind_unique = true  )
  {
    $mHtml  = '<select name="'.$name.'" id="'.$name.'ID" '.$events.'>';
    if( $ind_unique === true )
	  $mHtml .= '<option value="">- Seleccione -</option>';
    
	foreach( $arr_select as $row )
    {
      $selected = '';
      if( $row[0] == $key )
        $selected = 'selected="selected"';
      
      $mHtml .= '<option value="'.$row[0].'" '.$selected.'>'.( $ind_ajax === true ? utf8_encode( $row[1] ) :  $row[1] ).'</option>';
    }
    $mHtml .= '</select>';
    return $mHtml;
  }
  
  protected function SelectProducto( $cod_produc = NULL )
  {
	$mSelect = "SELECT cod_produc, nom_produc 
				  FROM ".BASE_DATOS.".tab_genera_produc 
				 WHERE ind_estado = '1' ";
	
	if( $cod_produc != NULL )
      $mSelect .= " AND cod_produc = '".$cod_produc."' ";

	$consulta = new Consulta( $mSelect, $this -> conexion );
    $PRODUC = $consulta -> ret_matriz();
	
	return $this -> GenerateSelect( $PRODUC, 'cod_produc' );
  }
  protected function SelectProductoOther( $cod_produc = NULL )
  {
	$mSelect = "SELECT cod_produc, nom_produc 
				  FROM ".BASE_DATOS.".tab_genera_produc 
				 WHERE ind_estado = '1' ";
	
	if( $cod_produc != NULL )
      $mSelect .= " AND cod_produc = '".$cod_produc."' ";

	$consulta = new Consulta( $mSelect, $this -> conexion );
    $PRODUC = $consulta -> ret_matriz();
	
	return $this -> GenerateSelect( $PRODUC, 'cod_produc_', $cod_produc, 'readonly', true, false );
  }
  
  protected function MainLoad( $_AJAX )
  {
    
    $mHtml =  '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="16%" class="TRform" ><b> * Producto:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="16%" class="TRform" >'.$this -> SelectProducto().'</td>';
        $mHtml .= '<td align="right" width="16%" class="TRform" ><b> * C&oacute;digo:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="16%" class="TRform" ><input type="text" maxlenght="8" size="15" name="cod_canalx" id="cod_canalxID" /></td>';
        $mHtml .= '<td align="right" width="16%" class="TRform" ><b> * Canal:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="17%" class="TRform" ><input type="text" maxlenght="30" size="35" name="nom_canalx" id="nom_canalxID" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center"  colspan="6" width="100%" class="TRform" ><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertCanal();"/></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="6" width="100%" class="TRform" ><div id="messageID" style="display:none;"></div></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="6" width="100%" class="TRform" >&nbsp;&nbsp; Si desea Actualizar o Eliminar un Canal, haga click en el C&oacute;digo.</td>';
      $mHtml .= '</tr>';
      
    
    $mHtml .= '</table>';
    
    $mHtml .=  '<center> <div id="mainListID">';
		$mHtml .= '</div></center>';
    
    echo $mHtml;
  }
  
  protected function mainList( $_AJAX )
  {
    echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['Standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/functions.js\"></script>\n";
    
    $mSql = "SELECT b.nom_produc, a.cod_canalx, a.nom_canalx,
                    IF( a.ind_estado != '1', 'INACTIVO', 'ACTIVO' ) AS ind_estado,
					b.cod_produc
               FROM ". BASE_DATOS .".tab_genera_canalx a,
					". BASE_DATOS .".tab_genera_produc b 
			  WHERE a.cod_produc = b.cod_produc";
    $_SESSION["queryXLS"] = $mSql;
    $list = new DinamicList($this->conexion, $mSql, 1 );
    $list->SetClose('no');
	
    $list->SetHeader("Producto", "field:b.nom_produc; width:1%;");
    $list->SetHeader("Codigo", "field:a.cod_canalx; width:1%; type:link; onclick:EditCanal( $(this) );");
    $list->SetHeader("Nombre", "field:a.nom_canalx; width:1%");
    $list->SetHeader("Estado","field:a.ind_estado; width:1%", array_merge( $this -> ind_estado_, $this -> ind_estado ) );
    $list->SetHidden("cod_produc", "cod_produc");

    $list->Display($this->conexion);

    $_SESSION["DINAMIC_LIST"] = $list;
    echo $list->GetHtml();
  }
}

$proceso = new AjaxCanales();
 ?>
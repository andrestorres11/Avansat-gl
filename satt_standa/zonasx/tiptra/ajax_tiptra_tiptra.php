<?php
class AjaxTiptra
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

  private function DropTiptra( $_AJAX )
  {
    $mSql = "DELETE FROM ".BASE_DATOS.".tab_genera_tiptra
                   WHERE cod_tiptra = '". $_AJAX['cod_tiptra'] ."'";
    
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  private function EditTiptra( $_AJAX )
  {
    $mSql = "UPDATE ".BASE_DATOS.".tab_genera_tiptra
                SET nom_tiptra = '".$_AJAX['nom_tiptra']."',
                    ind_estado = '".$_AJAX['ind_estado']."'
              WHERE cod_tiptra = '".$_AJAX['cod_tiptra']."'";
    
    if( $consulta = new Consulta( $mSql, $this -> conexion ) )
    {
      echo "y";
    }
    else
    {
      echo "n";
    }
  }
  
  private function InsertTiptra( $_AJAX )
  {
    $mSql = "INSERT INTO ".BASE_DATOS.".tab_genera_tiptra
                       ( cod_tiptra, nom_tiptra, ind_estado,
                         usr_creaci, fec_creaci) 
                 VALUES( '".$_AJAX['cod_tiptra']."','".$_AJAX['nom_tiptra']."','1', 
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
  
  private function ValidateTiptra( $_AJAX )
  {
    $mSql = "SELECT 1 
               FROM ".BASE_DATOS.".tab_genera_tiptra 
              WHERE cod_tiptra = '".$_AJAX['cod_tiptra']."'";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $_PRODUC = $consulta -> ret_matriz();
    
    if( sizeof( $_PRODUC ) > 0 )
      echo 'yes';
    else
      echo 'no';
  }
    
  protected function FormEdit( $_AJAX )
  {
    $mSelect = "SELECT cod_tiptra, nom_tiptra, ind_estado 
                  FROM ". BASE_DATOS .".tab_genera_tiptra
                 WHERE cod_tiptra = '".$_AJAX['cod_tiptra']."'";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_PRODUC = $consulta -> ret_matriz();
      
    $mHtml =  '<div class="StyleDIV" id="FormDivID">';
      $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
      
        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * C&oacute;digo:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" maxlenght="8" size="15" name="cod_tiptra_" id="cod_tiptra_ID" value="'.$_PRODUC[0]['cod_tiptra'].'" readonly/></td>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Tipo Transporte:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" maxlenght="30" size="35" name="nom_tiptra_" id="nom_tiptra_ID" value="'.$_PRODUC[0]['nom_tiptra'].'"/></td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Estado:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" >'.$this -> GenerateSelect( $this -> ind_estado, 'ind_estado_', $_PRODUC[0]['ind_estado'], NULL ).'</td>';
        $mHtml .= '</tr>';
        
      $mHtml .= '</table>';
    $mHtml .= '</div>';
    
    echo $mHtml;
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
  
  protected function MainLoad( $_AJAX )
  {
    
    $mHtml =  '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * C&oacute;digo:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" maxlenght="8" size="15" name="cod_tiptra" id="cod_tiptraID" /></td>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Tipo Transporte:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" maxlenght="30" size="35" name="nom_tiptra" id="nom_tiptraID" /></td>';
      $mHtml .= '</tr>';
     
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center"  colspan="4" width="100%" class="TRform" ><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertTiptra();"/></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" ><div id="messageID" style="display:none;"></div></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp; Si desea Actualizar o Eliminar un Tipo de Transporte, haga click en el C&oacute;digo.</td>';
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
    
    $mSql = "SELECT a.cod_tiptra, a.nom_tiptra,
                    IF( a.ind_estado != '1', 'INACTIVO', 'ACTIVO' ) AS ind_estado
               FROM ". BASE_DATOS .".tab_genera_tiptra a WHERE 1 = 1";
    $_SESSION["queryXLS"] = $mSql;
    $list = new DinamicList($this -> conexion, $mSql, 1 );
    $list -> SetClose('no');
    $list -> SetHeader("Codigo", "field:a.cod_tiptra; width:1%; type:link; onclick:EditTiptra( $(this) );");
    $list -> SetHeader("Nombre", "field:a.nom_tiptra; width:1%");
    $list -> SetHeader("Estado","field:a.ind_estado; width:1%", array_merge( $this -> ind_estado_, $this -> ind_estado ) );

    $list -> Display( $this -> conexion );

    $_SESSION["DINAMIC_LIST"] = $list;
    echo $list -> GetHtml();
  }
}

$proceso = new AjaxTiptra();
 ?>
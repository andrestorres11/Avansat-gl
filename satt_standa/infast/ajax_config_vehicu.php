<?php
class AjaxHomologarConfiguracion
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
  
  function getTipveh( $num_config = NULL )
  {
    $mSelect = "SELECT num_config, nom_config 
                  FROM ".BASE_DATOS.".tab_vehige_config 
                 WHERE ind_estado = '1'";
    
    if( $num_config != NULL )
    {
      $mSelect .= " AND num_config = '".$num_config."'";
    }
    
    $mSelect .= " ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  function getClases( $cod_clasex = NULL )
  {
    $mSelect = "SELECT cod_clasex, nom_clasex 
                  FROM ".BASE_DATOS.".tab_vehige_clases
                 WHERE ind_estado = '1'";
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
    /*********************************/
    $_CLASES = $this -> getClases();
    /*********************************/
    
    $mHtml =  '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Homologaci&oacute;n:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" name="cod_homolo" id="cod_homoloID" size="50" maxlength="40" /></td>';  
        $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Clase:&nbsp;&nbsp;</b></td>';
        $mHtml .= '<td align="left"  width="40%" class="TRform" >'.$this -> GenerateSelect( $_CLASES, 'cod_clasex' ).'</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center"  colspan="4" width="100%" class="TRform" ><input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InsertHomolo();"/></td>';
      $mHtml .= '</tr>';
    
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" ><div id="messageID" style="display:none;"></div></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp;Si desea Eliminar alguna Configuraci&oacute;n, haga click en el campo "ELIMINAR".</td>';
      $mHtml .= '</tr>';
      
    
    $mHtml .= '</table>';
    
    $mHtml .=  '<center> <div id="mainListID">';
		$mHtml .= '</div></center>';
    
    echo $mHtml;
  }
  
  protected function InsertHomolo( $mData )
  {
    $mInsert = "INSERT INTO ".BASE_DATOS.".tab_homolo_config
                       ( cod_homolo, cod_clasex,
                         usr_creaci, fec_creaci ) 
                 VALUES( '".$mData['cod_homolo']."','".$mData['cod_clasex']."', 
                         '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
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
  
  protected function DeleteHomolo( $mData )
  {
    $mSelect = "SELECT cod_clasex 
                  FROM ".BASE_DATOS.".tab_vehige_clases 
                 WHERE nom_clasex = '".$mData['cod_clasex']."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CLASEX = $consulta -> ret_matriz();
    
    $mDelete = "DELETE FROM ".BASE_DATOS.".tab_homolo_config 
                 WHERE cod_homolo = '".$mData['cod_homolo']."'
                   AND cod_clasex = '".$_CLASEX[0]['cod_clasex']."'";
    
    if( $consulta = new Consulta( $mDelete, $this -> conexion ) )
      echo "y";
    else
      echo "n";
  }
  
  protected function ValidateHomolo( $mData )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_homolo_config 
                 WHERE cod_homolo = '".$mData['cod_homolo']."' 
                   AND cod_clasex = '".$mData['cod_clasex']."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_HOMOLO = $consulta -> ret_matriz();
    
    if( sizeof( $_HOMOLO ) > 0 )
      echo 'yes';
    else
      echo 'no';
  }
  
  protected function mainList( $_AJAX )
  {
    echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['Standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/functions.js\"></script>\n";
    
    $mSelect = "SELECT a.cod_homolo, c.nom_clasex, 'ELIMINAR' as eliminar
                  FROM ". BASE_DATOS .".tab_homolo_config a, 
                       ". BASE_DATOS .".tab_vehige_clases c
                 WHERE a.cod_clasex = c.cod_clasex";
    
    $_SESSION["queryXLS"] = $mSelect;
    
    $list = new DinamicList( $this -> conexion, $mSelect, 1 );
    $list -> SetClose('no');
    $list -> SetHeader( "Homologación",  "field:a.cod_homolo; width:1%" );
    $list -> SetHeader( "Clase",         "field:c.nom_clasex; width:1%" );
    $list -> SetHeader( "Eliminar",      "field:eliminar; width:1%; type:link; onclick:DeleteHomolo( $(this) );" );

    $list->Display($this->conexion);

    $_SESSION["DINAMIC_LIST"] = $list;
    echo $list->GetHtml();
  }
}

$proceso = new AjaxHomologarConfiguracion();
 ?>
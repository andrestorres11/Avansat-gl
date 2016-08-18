<?php
/***************************************/
/* HOMOLOGACION DE CAUSAS DE SAFERBO ***/
/* ENERO 16 DE 2015                  ***/
/* ING. ANDRÉS FELIPE MALAVER        ***/
/***************************************/
class AjaxHomologacion
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
  
  protected function DeleteCausa( $mData )
  {
	$mDelete = "DELETE FROM ".BASE_DATOS.".tab_homolo_causas WHERE cod_causaf = '".$mData['elemento']."' ";
	
	if( $consulta = new Consulta( $mDelete, $this -> conexion ) )
	  echo '1000';
    else
	  echo '9999';
  }
  
  protected function MainLoad( $_AJAX )
  {
	echo "<link rel=\"stylesheet\" href=\"../".$_AJAX['Standa']."/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".$_AJAX['Standa']."/js/functions.js\"></script>\n";
    
    $mSql = "SELECT a.cod_causaf, a.nom_causaf, b.cod_noveda, 
					b.nom_noveda
               FROM ". BASE_DATOS .".tab_homolo_causas a,
					". BASE_DATOS .".tab_genera_noveda b
					WHERE a.cod_noveda = b.cod_noveda ";
    $_SESSION["queryXLS"] = $mSql;
    $list = new DinamicList($this -> conexion, $mSql, 1 );
    $list -> SetClose('no');
    $list -> SetHeader("Codigo Causa", "field:a.cod_causaf; width:1%; type:link; onclick:DeleteCausa( $(this) );");
    $list -> SetHeader("Nombre Causa", "field:a.nom_causaf; width:1%");
    $list -> SetHeader("Codigo Novedad SAT GL", "field:b.cod_noveda; width:1%");
    $list -> SetHeader("Nombre Novedad SAT GL", "field:b.nom_noveda; width:1%");

    $list -> Display( $this -> conexion );

    $_SESSION["DINAMIC_LIST"] = $list;
    
    $mHtml =  '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
    
	$mHtml .= '<tr>';
      $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp; Si desea Crear uno o m&aacute;s registros, haga click <a style="color:#000000;cursor:pointer;" onclick="CreateRegistro();"><b>AQU&Iacute;</b></a></td>';
    $mHtml .= '</tr>';
      
	$mHtml .= '<tr>';
      $mHtml .= '<td align="left"  colspan="4" width="100%" class="TRform" >&nbsp;&nbsp; Si desea Eliminar una Homologaci&oacute;n, haga click en el C&oacute;digo de la causa de SAFERBO.</td>';
	$mHtml .= '</tr>';
    
	$mHtml .= '</table>';
    
    $mHtml .=  '<div id="messageID" style="display:none;"></div><center> <div id="mainListID">';
	$mHtml .= $list -> GetHtml();
	$mHtml .= '</div></center>';
    
    echo $mHtml;
  }
  
  private function Style()
  {
    echo '
        <style>
        .ui-tabs-vertical { width: 75%; }
        .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
        .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
        .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
        .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
        .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 75%;}
        #tabs li .ui-icon-close { float: right; margin: 0.4em 0.2em 0 0; cursor: pointer; }
        .CellHead
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
		  border: 1px solid #35650F;
        }
        
        .cellInfo1
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #EBF8E2;
          padding: 2px;
		  border: 1px solid #35650F;
        }
        
        .cellInfo2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #DEDFDE;
          padding: 2px;
        }
        
        .StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .Style2DIV
          {
            background-color: #FFFFFF;
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 96%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .TRform
          {
            padding-right:3px; 
            padding-top:15px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
        </style>';
  }
  
  protected function FormCreate( $mData )
  {
	$this -> Style();
	
	echo '<script>$( "#novedaID" ).autocomplete({
            source: "../'.$mData['Standa'].'/protoc/ajax_protoc_transp.php?option=getNoveda",
            minLength: 1, 
            delay: 100
          });</script>';
	
	$mHtml =  '<div class="StyleDIV" id="FormDivID">';
      $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';    
        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * C&oacute;digo Causa SAFERBO:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" ><input type="text" maxlenght="8" size="15" name="cod_causax" id="cod_causaxID" /></td>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Nombre Causa SAFERBO:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="40%" class="TRform" ><input type="text" maxlenght="50" size="35" name="nom_causax" id="nom_causaxID" /></td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
          $mHtml .= '<td align="right" width="20%" class="TRform" ><b> * Novedad SAT GL:&nbsp;&nbsp;</b></td>';
          $mHtml .= '<td align="left"  width="20%" class="TRform" colspan="2"><input type="text" id="novedaID" name="noveda" size="50"/></td>';
          $mHtml .= '<td align="center" width="20%" class="TRform" ><input class="crmButton small save" type="button" id="InsertID" value="Agregar" onclick="SetHomologacion();"/></td>';
        $mHtml .= '</tr>';
      $mHtml .= '</table><br>';
      
	  $mHtml .= '<center><table id="HomologaTableID" width="100%" border="0" cellpadding="0" cellspacing="0">';
	  
	    $mHtml .= '<tr>';
	    $mHtml .= '<td class="CellHead" width="15%" align="center">C&oacute;digo Causa SAFERBO</td>';
	    $mHtml .= '<td class="CellHead" width="40%" align="center">Nombre Causa SAFERBO</td>';
	    $mHtml .= '<td class="CellHead" width="45%" align="center">Novedad SAT GL</td>';
	    $mHtml .= '</tr>';
      
	  $mHtml .= '</table></center><br><br>';
    $mHtml .= '</div>';
	
	echo $mHtml;
  }
  
  protected function SaveRegistros( $mData )
  {
	$consulta = new Consulta("SELECT 1", $this -> conexion, "BR");
	$counter = 0;
	foreach( $mData['comb'] as $contenido )
	{
	  $datos = explode( "||", $contenido );
	  $mSelect = "SELECT a.cod_causaf, a.nom_causaf, b.cod_noveda, b.nom_noveda
               FROM ". BASE_DATOS .".tab_homolo_causas a,
					". BASE_DATOS .".tab_genera_noveda b
					WHERE a.cod_noveda = b.cod_noveda 
					  AND a.cod_causaf = '".$datos[0]."' ";
	
	  $consulta = new Consulta( $mSelect, $this -> conexion );
      $noveda = $consulta -> ret_matriz();
	  
	  $mSelect = "SELECT 1
               FROM ". BASE_DATOS .".tab_genera_noveda b
					WHERE b.cod_noveda = '".$datos[2]."' ";
	
	  $consulta = new Consulta( $mSelect, $this -> conexion );
      $existenoveda = $consulta -> ret_matriz();
	  
	  if( sizeof( $noveda ) <= 0 && sizeof( $existenoveda ) > 0 )
	  {
		$mInsert = "INSERT INTO ". BASE_DATOS .".tab_homolo_causas
							  ( cod_causaf, nom_causaf, cod_noveda, 
							    usr_creaci, fec_creaci
					   )VALUES( '".$datos[0]."', '".$datos[1]."', '".$datos[2]."', 
					            '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
		$consulta = new Consulta( $mInsert, $this -> conexion, "R" );
		$counter++;
	  }
	}
	if( $insercion = new Consulta( "COMMIT" , $this -> conexion ) )
		$msj = "SE HAN INSERTADO ".$counter." HOMOLOGACION(ES).";
	  else 
		$msj = "LA(S) HOMOLOGACION(ES) NO HAN SIDO INSERTADAS CON EXITO.";
	
	  echo '<center><div width="100%" class="StyleDIV" style="min-height:10px;" ><span style="color:#000000;">'.$msj.'</span></div><center>';
  }
  
  protected function ValidaHomologacion( $mData )
  {
	$dat_noveda = explode( "-", $mData['noveda'] ); 
	$cod_noveda = trim( $dat_noveda[0] );
	
    $mSelect = "SELECT a.cod_causaf, a.nom_causaf, b.cod_noveda, b.nom_noveda
               FROM ". BASE_DATOS .".tab_homolo_causas a,
					". BASE_DATOS .".tab_genera_noveda b
					WHERE a.cod_noveda = b.cod_noveda AND a.cod_causaf = '".$mData['cod_causax']."' ";
	
	$consulta = new Consulta( $mSelect, $this -> conexion );
    $noveda = $consulta -> ret_matriz();
	
	if( sizeof( $noveda ) > 0 )
	  echo $noveda[0]['cod_causaf']."||".$noveda[0]['nom_causaf']."||".$noveda[0]['cod_noveda']." - ".$noveda[0]['nom_noveda'];
	else
	 echo "y";
	}
  
  protected function getNoveda( $_AJAX )
  {
    $mSelect = "SELECT cod_noveda,  CONCAT( CONVERT( nom_noveda USING utf8), 
						  '', if (nov_especi = '1', '(NE)', '' ), 
						  if( ind_alarma = 'S', '(GA)', '' ), 
						  if( ind_manala = '1', '(MA)', '' ),
						  if( ind_tiempo = '1', '(ST)', '' ) ) , 
						  ind_tiempo
				   FROM " . BASE_DATOS . ".tab_genera_noveda 
				   WHERE ind_visibl = '1' AND
                 ( CONCAT( CONVERT( nom_noveda USING utf8), 
                   '', if (nov_especi = '1', '(NE)', '' ), 
                   if( ind_alarma = 'S', '(GA)', '' ), 
                   if( ind_manala = '1', '(MA)', '' ),
                   if( ind_tiempo = '1', '(ST)', '' ) ) LIKE '%". $_AJAX['term'] ."%' OR cod_noveda LIKE '%". $_AJAX['term'] ."%') ";
				   
    if ($_SESSION['datos_usuario']['cod_perfil'] != COD_PERFIL_SUPERUSR && $_SESSION['datos_usuario']['cod_perfil'] != COD_PERFIL_ADMINIST && $_SESSION['datos_usuario']['cod_perfil'] != COD_PERFIL_SUPEFARO)
        $mSelect .=" AND cod_noveda !='" . CONS_NOVEDA_ACAEMP . "' ";
        
    $mSelect .=" ORDER BY 2 ASC LIMIT 10";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $noveda = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($noveda); $i<$len; $i++){
       $data [] = '{"label":"'.$noveda[$i][0].' - '.utf8_encode($noveda[$i][1]).'","value":"'. $noveda[$i][0].' - '.utf8_encode($noveda[$i][1]).'"}'; 
    }
    echo '['.join(', ',$data).']';
  }
}

$proceso = new AjaxHomologacion();
 ?>
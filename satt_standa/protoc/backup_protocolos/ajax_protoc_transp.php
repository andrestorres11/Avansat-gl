<?php

class AjaxTranspProtoc
{
  var $conexion;
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  protected function getNewRow( $_AJAX )
  {
    $cod_noveda = $_AJAX['cod_noveda'];
    $AllProtocols = $this -> getAllProtocols();
    $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="3" class="cellInfo1" >PROTOCOLOS</td>';
    $mHtml .= '</tr>';

    $mHtml .= '<tr>';
    $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">'.$this -> multipleSelect('all_protoc'.$cod_noveda, $AllProtocols, 'AsignaProtocolo('.$cod_noveda.');' ).'</td>';
    $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="10%" align="center"><br><br><input type="button" onclick="AsignaProtocolo('.$cod_noveda.');" value=">>" /><br><br><input type="button" onclick="DerogaProtocolo('.$cod_noveda.');" value="<<"/></td>';
    $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">'.$this -> multipleSelect('asi_protoc-'.$cod_noveda, NULL, 'DerogaProtocolo('.$cod_noveda.');', "item" ).'</td>';
    $mHtml .= '</tr>';

    $mHtml .= '</table>';
    echo $mHtml;
  }
  
  protected function ShowMainList( $_AJAX )
  {
    $_NOVEDA = $this -> getNovedaProtoco( $_AJAX['cod_transp'] );
    
    $this -> Style();
    echo '<script>
          $(function() {
            $( "#tabs" ).accordion({
              collapsible:true,
              active: false
            });
          });
          </script>';
    
    
    $mHtml = '<label for="textID" style="font-size:12px; font-family:Trebuchet MS, Verdana, Arial;">Listado de Novedades con Protocolos asociados, asignadas a la Transportadora<br>Si desea agregar una nueva novedad, haga click <a onclick="NewNovedad();" href="#" style="color:#285C00; text-decoration:none; cursor:pointer;" >aqu&iacute;</a><br>&nbsp;</label>';
    $mHtml .= '<div id="tabs" width="100%">';
    
    foreach( $_NOVEDA as $cod_noveda => $des_noveda )
    {
      $mHtml .= '<div>&nbsp;<br>'.$cod_noveda.' - '.$des_noveda['nombre'].'<br>&nbsp;</div>';
      
      $_PROTOC = $des_noveda['protoc'];
      $ActiveProtocols = array();
      $_A_P = array();
      $i = 0;
      
      foreach( $_PROTOC as $cod_protoc => $nom_protoc )
      {
        $_A_P[] = $cod_protoc;
        $ActiveProtocols[ $i ][0] = $cod_protoc; 
        $ActiveProtocols[ $i ][1] = $nom_protoc; 
        $i++;
      }
      
      $AllProtocols = $this -> getAllProtocols( $_A_P );
      
      $mHtml .= '<div id="tabs-'.$cod_noveda.'">';      
        $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="3" class="cellInfo1" >PROTOCOLOS</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">'.$this -> multipleSelect('all_protoc'.$cod_noveda, $AllProtocols, 'AsignaProtocolo('.$cod_noveda.');' ).'</td>';
          $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="10%" align="center"><br><br><input type="button" onclick="AsignaProtocolo('.$cod_noveda.');" value=">>" /><br><br><input type="button" onclick="DerogaProtocolo('.$cod_noveda.');" value="<<"/></td>';
          $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">'.$this -> multipleSelect('asi_protoc-'.$cod_noveda, $ActiveProtocols, 'DerogaProtocolo('.$cod_noveda.');', "item" ).'</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '</table>';
      $mHtml .= '</div>';	
    }


    #----ENCABEZADOS LISTA
    /*$mHtml .= '<ul>';
    foreach( $_NOVEDA as $cod_noveda => $des_noveda )
    {
      $mHtml .= '<li aria-controls="tabs-'.$cod_noveda.'"><a href="#tabs-'.$cod_noveda.'">'.$cod_noveda.' - '.$des_noveda['nombre'].'</a><span class="ui-icon ui-icon-close" role="presentation"></span></li>';
    }
    $mHtml .= '</ul>';
    
    #----CUERPO LISTA
    foreach( $_NOVEDA as $cod_noveda => $des_noveda )
    {
      $_PROTOC = $des_noveda['protoc'];
      $ActiveProtocols = array();
      $_A_P = array();
      $i = 0;
      
      foreach( $_PROTOC as $cod_protoc => $nom_protoc )
      {
        $_A_P[] = $cod_protoc;
        $ActiveProtocols[ $i ][0] = $cod_protoc; 
        $ActiveProtocols[ $i ][1] = $nom_protoc; 
        $i++;
      }
      
      $AllProtocols = $this -> getAllProtocols( $_A_P );
      
      $mHtml .= '<div id="tabs-'.$cod_noveda.'">';      
        $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
        $mHtml .= '<tr>';
          $mHtml .= '<td colspan="3" class="cellInfo1" >PROTOCOLOS</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">'.$this -> multipleSelect('all_protoc'.$cod_noveda, $AllProtocols, 'AsignaProtocolo('.$cod_noveda.');' ).'</td>';
          $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="10%" align="center"><br><br><input type="button" onclick="AsignaProtocolo('.$cod_noveda.');" value=">>" /><br><br><input type="button" onclick="DerogaProtocolo('.$cod_noveda.');" value="<<"/></td>';
          $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">'.$this -> multipleSelect('asi_protoc-'.$cod_noveda, $ActiveProtocols, 'DerogaProtocolo('.$cod_noveda.');', "item" ).'</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '</table>';
      $mHtml .= '</div>';
    }    */
   
    $mHtml .= '</div>';
    
    $mHtml .= "<br><input class='crmButton small save' style='cursor:pointer;' type='button' value='Guardar' onclick='SaveAllProtocols();'/>";
    
    echo $mHtml;
    
  }

  protected function SaveAllProtocols( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";
    die();*/

    $mSelect = "SELECT cod_transp, cod_noveda, cod_protoc 
                 FROM ". BASE_DATOS .".tab_noveda_protoc 
                WHERE cod_transp = '".$_AJAX['cod_transp']."'";
                
    $consulta = new Consulta( $mSelect, $this -> conexion, "BR");
		$ant_protoc = $consulta -> ret_matriz();

    if( sizeof( $ant_protoc ) > 0 )
    {
      $mSelect = "SELECT MAX( num_consec ) AS num_consec 
                 FROM ". BASE_DATOS .".tab_bitaco_novpro 
                WHERE cod_transp = '".$_AJAX['cod_transp']."'";
                
      $consulta = new Consulta( $mSelect, $this -> conexion, "R" );
      $ant_consec = $consulta -> ret_matriz();
      
      $nue_consec = sizeof( $ant_protoc ) > 0 ? $ant_consec[0][0] + 1 : 1;
      
      foreach( $ant_protoc as $row )
      {
        $mInsert = "INSERT INTO ". BASE_DATOS .".tab_bitaco_novpro
                              ( num_consec, cod_transp, cod_noveda, 
                                cod_protoc, usr_modifi, fec_modifi )
                        VALUES( '".$nue_consec."', '".$row['cod_transp']."', '".$row['cod_noveda']."',
                                '".$row['cod_protoc']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        
        $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
      }
    }
    
    $mDelete = "DELETE FROM ". BASE_DATOS .".tab_noveda_protoc 
                WHERE cod_transp = '".$_AJAX['cod_transp']."'";
    
    $consulta = new Consulta( $mDelete, $this -> conexion, "R" );
 
    if( sizeof( $_AJAX['noveda'] ) > 0 )
    {
      foreach( $_AJAX['noveda'] as $cod_noveda => $protoco )
      {
        if( is_array( $protoco ) )
        {
          foreach( $protoco as $cod_protoco )
          {
            $mInsert = "INSERT INTO ". BASE_DATOS .".tab_noveda_protoc
                                  ( cod_transp, cod_noveda, 
                                    cod_protoc, usr_creaci, fec_creaci )
                            VALUES( '".$_AJAX['cod_transp']."', '".$cod_noveda."',
                                    '".$cod_protoco."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";

            $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
          }        
        }    
      }    
    }
    
    if( $insercion = new Consulta( "COMMIT", $this -> conexion ) )
    {
      $mHtml  .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
    
      $mHtml .= '<tr>';
      $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >La Informaci&oacute;n ha sido Registrada Exitosamente.</i></td>';
      $mHtml .= '</tr>';
      $mHtml .= '</table></center>';
      echo $mHtml;
    }
    
  }
  
  protected function NewNovedad( $_AJAX )
  {
    // echo "<pre>";
    // print_r(  $_AJAX  );
    // echo "</pre>";
    // echo "-> ".getcwd();
    
    // $this -> Style();
    echo '<script>$( "#novedaID" ).autocomplete({
            source: "../'.$_AJAX['standa'].'/protoc/ajax_protoc_transp.php?option=getNoveda",
            minLength: 1, 
            delay: 100
          });</script>';
          
    $mHtml .= '<div class="StyleDIV" id="FormNovedaID">';
    $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
      $mHtml .= '<td class="TRform" align="right">Novedad:&nbsp;&nbsp;</td>';
      $mHtml .= '<td class="TRform" align="left"><input type="text" id="novedaID" name="noveda" size="50"/></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    $mHtml .= '</div>';
    
    echo $mHtml;
  }
  
  private function getAllProtocols( $notinarray = NULL )
  {
    // echo "<pre>";
    // print_r( $notinarray );
    // echo "</pre>";
    $mSelect = "SELECT cod_protoc, des_protoc 
                  FROM ".BASE_DATOS.".tab_genera_protoc 
                 WHERE ind_activo = '1' ";
                 
    if( $notinarray != NULL )
      $mSelect .= " AND cod_protoc NOT IN( ".implode(',' ,$notinarray) ." ) ";
    
    $mSelect .= " ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function multipleSelect( $name, $protoc = NULL, $dbclick = NULL, $class = NULL )
  {
    $mHtml = '';
    $mHtml .= '<select style="width:75%;" name="'. $name .'" id="'. $name .'-ID" size="8" multiple';
    if( $dbclick )
    {
      $mHtml .= ' ondblclick="'. $dbclick .'"';
    }
    if( $class )
    {
      $mHtml .= ' class="'. $class .'"';
    }
    $mHtml .= ' >';
    
    if( $protoc != NULL )
    {
      foreach( $protoc as $row )
      {
        $mHtml .= '<option value="'.$row[0].'">'.utf8_decode( $row[1] ).'</option>';
      }    
    }
    $mHtml .= '</select>';
    return $mHtml;
  }
  
  private function getNovedaProtoco( $cod_transp )
  {
    $_PROTOC = array();
    $mSelect = " SELECT c.cod_protoc, c.des_protoc, b.cod_noveda, 
                        b.nom_noveda, a.cod_transp
                   FROM ".BASE_DATOS.".tab_noveda_protoc a, 
                        ".BASE_DATOS.".tab_genera_noveda b, 
                        ".BASE_DATOS.".tab_genera_protoc c
                  WHERE a.cod_noveda = b.cod_noveda 
                    AND a.cod_protoc = c.cod_protoc 
                    AND c.ind_activo = '1'
                    AND a.cod_transp = '".$cod_transp."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_NOVEDA = $consulta -> ret_matriz();
    
    foreach( $_NOVEDA as $noveda )
    {
      $_PROTOC[ $noveda['cod_noveda'] ]['nombre'] = $noveda['nom_noveda'];
      $_PROTOC[ $noveda['cod_noveda'] ]['protoc'][ $noveda['cod_protoc'] ] = $noveda['des_protoc']; 
    }
    return $_PROTOC;
  }
  
  protected function ValidateTransp( $_AJAX )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    a.cod_tercer = '". $_AJAX['cod_transp'] ."'";
		
		$consulta = new Consulta( $mSql, $this -> conexion );
		$transpor = $consulta -> ret_matriz();
    if( sizeof( $transpor ) > 0 )
      echo 'y';
    else
      echo 'n';
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
  
  protected function getTransp( $_AJAX )
  {
    $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    CONCAT( a.cod_tercer ,' - ', UPPER( a.abr_tercer ) ) LIKE '%". $_AJAX['term'] ."%'
           ORDER BY 2";
		
		$consulta = new Consulta( $mSql, $this -> conexion );
		$transpor = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
       $data [] = '{"label":"'.$transpor[$i][0].' - '.utf8_encode($transpor[$i][1]).'","value":"'. $transpor[$i][0].' - '.utf8_encode($transpor[$i][1]).'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }
}

$proceso = new AjaxTranspProtoc();

?>
<?php
ini_set('display_errors', false);

class AjaxDespacNoveda
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
  
  protected function ValidaAsignadoUsuario( $_AJAX )
  {
     $mSelect = "SELECT num_consec, cod_contro, cod_sitiox, fec_noveda
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE num_despac = '".$_AJAX['num_despac']."'
                   /* AND usr_asigna = '".$_AJAX['cod_usuari']."' */
                   AND ind_ejecuc = '0'";
  	
  	$consulta = new Consulta($mSelect, $this -> conexion );
    $_DATACON = $consulta -> ret_matriz();

  	echo $var = sizeof( $_DATACON ) > 0 ? 'y' : 'n';
  }
  
  protected function ShowNovedaSoluci( $_AJAX )
  {
  	echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
  	
  	$mSelect = "SELECT a.num_consec, a.cod_contro, a.cod_noveda, a.fec_noveda, 
  					   b.nom_contro, c.nom_noveda
                  FROM ".BASE_DATOS.".tab_protoc_asigna a 
             LEFT JOIN ".BASE_DATOS.".tab_genera_contro b
         			ON a.cod_contro = b.cod_contro
             LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c
         			ON a.cod_noveda = c.cod_noveda
                 WHERE a.num_despac = '".$_AJAX['num_despac']."'
                   /* AND a.usr_asigna = '".$_AJAX['cod_usuari']."' */
                   AND a.ind_ejecuc = '0'";
  	
  	$consulta = new Consulta($mSelect, $this -> conexion );
    $_PENDIE = $consulta -> ret_matriz();

    $mHtml  = '<div class="StyleDIV" id="ResultID">';
    $i = 0;
    $mHtml .= '<br><table width="100%" cellspacing="2px" cellpadding="0">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" align="center">Seleccione</td>';
    $mHtml .= '<td class="CellHead" align="center">#</td>';
    $mHtml .= '<td class="CellHead" align="center">Puesto Control</td>';
    $mHtml .= '<td class="CellHead" align="center">Novedad</td>';
    $mHtml .= '<td class="CellHead" align="center">Fecha Novedad</td>';
    $mHtml .= '</tr>'; 
    
    foreach( $_PENDIE as $row )
    {
      $class = $i % 2 == 0 ? 'CellInfo1' : 'CellInfo2';
      $mHtml .= '<tr>';
      $mHtml .= '<td class="'.$class.'"><input type="checkbox" name="key_'.$i.'" id="key_'.$i.'ID" value="'.$row['num_consec'].'|'.$row['cod_contro'].'|'.$row['cod_noveda'].'"></td>';
      $mHtml .= '<td class="'.$class.'">'.$row['num_consec'].'</td>';
      $mHtml .= '<td class="'.$class.'">'.$row['nom_contro'].'</td>';
      $mHtml .= '<td class="'.$class.'">'.$row['nom_noveda'].'</td>';
      $mHtml .= '<td class="'.$class.'">'.$row['fec_noveda'].'</td>';
      $mHtml .= '</tr>';
      $i++;
    }
    
    $mHtml .= '</table>';    
    $mHtml .= '<input type="hidden" name="tot_pendie" id="tot_pendieID" value="'.sizeof( $_PENDIE ).'">';
    $mHtml .= '<input type="hidden" name="num_despac" id="num_despacID" value="'.$_AJAX['num_despac'].'">';
    $mHtml .= '<hr><center><input class="crmButton small save" type="button" onclick="SaveAsignaciones();" value="Aceptar" name="Aceptar"></center>';
    
    
    $mHtml .= '</div>';
    echo $mHtml;


  }

  protected function SaveProtocols( $_AJAX )
  {
    
    $query = "SELECT a.num_despac, a.cod_manifi, 
                 IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                 IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                 IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                     e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax
                FROM " . BASE_DATOS . ".tab_despac_despac a,
                     " . BASE_DATOS . ".tab_despac_vehige b,
                     " . BASE_DATOS . ".tab_tercer_tercer c,
                     ". BASE_DATOS .".tab_tercer_conduc d 
           LEFT JOIN ".  BASE_DATOS .".tab_genera_califi f ON f.cod_califi = d.cod_califi
           LEFT JOIN ".  BASE_DATOS .".tab_operad_operad e ON e.cod_operad = d.cod_operad
               WHERE a.num_despac = b.num_despac AND
                     b.cod_conduc = c.cod_tercer AND
                     d.cod_tercer = c.cod_tercer AND
                     a.num_despac = '" . $_AJAX['despac'] . "'";
      
    $consulta = new Consulta($query, $this->conexion);
    $_DATACON = $consulta -> ret_matriz();

    $query = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                  if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
         DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
                FROM " . BASE_DATOS . ".tab_despac_vehige b,
                     " . BASE_DATOS . ".tab_genera_rutasx e,
                     " . BASE_DATOS . ".tab_genera_agenci g,
                     " . BASE_DATOS . ".tab_despac_despac a  
               WHERE a.num_despac = b.num_despac AND
                     b.cod_rutasx = e.cod_rutasx AND
                     b.cod_agenci = g.cod_agenci AND
                     a.num_despac = '" . $_AJAX['despac'] . "'"; 

    $consulta = new Consulta($query, $this->conexion);
    $_DATARUT = $consulta -> ret_matriz();
    
    $info  = "PROTOCOLO \n";
    $info .= "Fecha y Hora: ".date('Y-m-d H:i:s')." \n";
    $info .= "Despacho No.: ".$_AJAX['despac']." \n";
    $info .= "Ruta: ".$_DATARUT[0]['nom_rutasx']." \n";
    $info .= "Placa: ".$_DATACON[0]['num_placax']." \n";
    $info .= "Conductor: ".$_DATACON[0]['cod_tercer']." - ".$_DATACON[0]['abr_tercer']." \n";
    $info .= "Celular: ".$_DATACON[0]['telmov']." \n";
    

    switch( $_AJAX['sit'] )
    {
      case 'A':
      
        $mSelect = "SELECT MAX(cod_consec) 
                      FROM ".BASE_DATOS.".tab_despac_procon
                     WHERE num_despac = '".$_AJAX['despac']."'";
        
        $consulta = new Consulta( $mSelect, $this -> conexion, "BR");
        $consec = $consulta -> ret_matriz();
        
        $nue_consec = $consec[0][0] + 1;
        
        for( $k = 0; $k < $_AJAX['tot_protoc']; $k++ )
        {
          $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_procon
                                ( num_despac, cod_contro, cod_rutasx, 
                                  cod_consec, cod_noveda, fec_contro,
                                  cod_protoc, ind_aproba, usr_creaci, 
                                  fec_creaci 
                                )
                          VALUES( '".$_AJAX['despac']."', '".$_AJAX['cod_contro']."', '".$_AJAX['rutax']."', 
                                  '".$nue_consec."', '".$_AJAX['cod_noveda']."', NOW(), 
                                  '".$_AJAX[ 'cod_protoc'.$k ]."', '".$_AJAX[ 'ind_activo'.$k ]."', '".$_SESSION['datos_usuario']['cod_usuari']."',
                                  NOW() 
                                )";
          $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
          
          if( $_AJAX[ 'ind_activo'.$k ] == 'S' )
          {
            $message  = $info;
            $message .= "Protocolo: ".$this -> GetNameProtoc( $_AJAX[ 'cod_protoc'.$k ] )." \n";
            $message .= "-------------------------------- \n";
            $message .= "El Protocolo fue Asignado al Supervisor. \n";
            $message .= "Asignado Por: ".$_SESSION['datos_usuario']['cod_usuari']." \n";
            $message .= "-------------------------------- \n";
            
            $asunto = "PROTOCOLO ASIGNADO";

            //mail( MAIL_SUPERVISORES, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
            
            $_CENOPE = $this -> getMailProtoc( $_AJAX[ 'cod_protoc'.$k ] );
            //mail( $_CENOPE, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
          }
          else
          {
            $message  = $info;
            $message .= "Protocolo: ".$this -> GetNameProtoc( $_AJAX[ 'cod_protoc'.$k ] )." \n";
            $message .= "-------------------------------- \n";
            $message .= "El Protocolo fue Ejecutado. \n";
            $message .= "Ejecutado Por: ".$_SESSION['datos_usuario']['cod_usuari']." \n";
            $message .= "-------------------------------- \n";
            
            $asunto = "PROTOCOLO EJECUTADO";

            //mail( MAIL_SUPERVISORES, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
            
            $_CENOPE = $this -> getMailProtoc( $_AJAX[ 'cod_protoc'.$k ] );
            //mail( $_CENOPE, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
          }
        }
      break;
      
      case 'S':
        $consulta = new Consulta( "SELECT NOW()", $this -> conexion, "BR");
        
        for( $k = 0; $k < $_AJAX['tot_protoc']; $k++ )
        {
          $mInsert = "INSERT INTO ".BASE_DATOS.".tab_despac_pronov
                                ( num_despac, cod_contro, cod_rutasx, 
                                  cod_noveda, fec_noveda, cod_protoc,
                                  ind_aproba, usr_creaci, fec_creaci 
                                )
                          VALUES( '".$_AJAX['despac']."', '".$_AJAX['cod_contro']."', '".$_AJAX['rutax']."', 
                                  '".$_AJAX['cod_noveda']."', NOW(), '".$_AJAX[ 'cod_protoc'.$k ]."', 
                                  '".$_AJAX[ 'ind_activo'.$k ]."', '".$_SESSION['datos_usuario']['cod_usuari']."',
                                  NOW() 
                                )";
          $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
          
          if( $_AJAX[ 'ind_activo'.$k ] == 'S' )
          {
            $message  = $info;
            $message .= "Protocolo: ".$this -> GetNameProtoc( $_AJAX[ 'cod_protoc'.$k ] )." \n";
            $message .= "-------------------------------- \n";
            $message .= "El Protocolo fue Asignado al Supervisor. \n";
            $message .= "Asignado Por: ".$_SESSION['datos_usuario']['cod_usuari']." \n";
            $message .= "-------------------------------- \n";
            
            $asunto = "PROTOCOLO ASIGNADO";

            //mail( MAIL_SUPERVISORES, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
            
            $_CENOPE = $this -> getMailProtoc( $_AJAX[ 'cod_protoc'.$k ] );
            //mail( $_CENOPE, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
          }
          else
          {
            $message  = $info;
            $message .= "Protocolo: ".$this -> GetNameProtoc( $_AJAX[ 'cod_protoc'.$k ] )." \n";
            $message .= "-------------------------------- \n";
            $message .= "El Protocolo fue Ejecutado. \n";
            $message .= "Ejecutado Por: ".$_SESSION['datos_usuario']['cod_usuari']." \n";
            $message .= "-------------------------------- \n";
            
            $asunto = "PROTOCOLO EJECUTADO";

            //mail( MAIL_SUPERVISORES, $asunto, $message, 'From: faroavansat@eltransporte.com');
            mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
            
            $_CENOPE = $this -> getMailProtoc( $_AJAX[ 'cod_protoc'.$k ] );
            if( $_CENOPE != '' && $_CENOPE != NULL )
            {
              //mail( $_CENOPE, $asunto, $message, 'From: faroavansat@eltransporte.com');
              mail( "felipe.malaver@intrared.net", $asunto, $message, 'From: faroavansat@eltransporte.com');
            }
          }
        }
      break;
    }
    
    if( $insercion = new Consulta( "COMMIT", $this -> conexion ) )
      echo 'y';
    else
      echo 'n';
        
  }
  
  private function getMailProtoc( $cod_protoc )
  {
    $mSelect = "SELECT a.ema_protoc
                  FROM ".BASE_DATOS.".tab_genera_protoc a
                 WHERE a.cod_protoc = '".$cod_protoc."'";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_EMAIL = $consulta -> ret_matriz();

    return $_EMAIL[0][0];
  }
  
  
  private function GetNameProtoc( $cod_protoc )
  {
    $mSelect = "SELECT a.des_protoc 
                  FROM ".BASE_DATOS.".tab_genera_protoc a
                 WHERE a.cod_protoc = '".$cod_protoc."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_PROTOC = $consulta -> ret_matriz();
    
    return $_PROTOC[0][0];
  }
  
  protected function ShowProtocNoveda( $_AJAX )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    
    $mSelect = "SELECT a.cod_respon, a.nom_respon 
                  FROM ".BASE_DATOS.".tab_genera_respon a, 
                       ".BASE_DATOS.".tab_genera_perfil b
                 WHERE a.cod_respon = b.cod_respon AND
                       b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_PERFIL = $consulta -> ret_matriz();
    
    $mSelect = "SELECT a.cod_protoc, b.des_protoc, b.tex_protoc
                  FROM ". BASE_DATOS .".tab_noveda_protoc a, 
                       ". BASE_DATOS .".tab_genera_protoc b
                 WHERE a.cod_protoc = b.cod_protoc
                   AND a.cod_transp = '".$_AJAX['cod_transp']."'
                   AND a.cod_noveda = '".$_AJAX['cod_noveda']."'";
                   
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_PROTOC = $consulta -> ret_matriz();
    $mHtml  = '<center><div class="StyleDIV" id="ResultID">';
    $i = 0;
    $mHtml .= '<br><table width="100%" cellspacing="2px" cellpadding="0">';
    foreach( $_PROTOC as $row )
    {
      $class = $i % 2 == 0 ? 'CellInfo1' : 'CellInfo2';
      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" colspan="4"><label id="des_protoc'.$i.'ID">'.($row['des_protoc']).'</label><input type="hidden" name="protoc'.$i.'" id="protoc'.$i.'ID" value="'.$row['cod_protoc'].'"></td>';
      $mHtml .= '</tr>'; 
      
      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" colspan="4"><label id="acc_protoc'.$i.'ID"><b>ACCIONES: </b></label></td>';
      $mHtml .= '</tr>';
      
      $mSelect = "SELECT a.cod_ordenx, a.cod_protoc, a.cod_subcau,
                         b.cod_tipoxx, b.tex_encabe, b.ind_requer, 
                         b.des_texto,  b.htm_config 
                    FROM ".BASE_DATOS.".tab_asigna_subcau a,
                         ".BASE_DATOS.".tab_genera_subcau b 
                   WHERE a.cod_subcau = b.cod_consec
                     AND a.cod_protoc = '".$row['cod_protoc']."'
                   ORDER BY 1";
      
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $mFormulario = $consulta -> ret_matriz();

      foreach( $mFormulario as $mElemento )
      {
        $adicioInputs = '
          <input type="hidden" name="ind_requer'.$mElemento['cod_subcau'].'" id="ind_requer'.$mElemento['cod_subcau'].'ID" value="'.$mElemento['ind_requer'].'">
          <input type="hidden" name="cod_tipoxx'.$mElemento['cod_subcau'].'" id="cod_tipoxx'.$mElemento['cod_subcau'].'ID" value="'.$mElemento['cod_tipoxx'].'">
          <input type="hidden" name="cod_subcau'.$mElemento['cod_subcau'].'" id="cod_subcau'.$mElemento['cod_subcau'].'ID" value="'.$mElemento['cod_subcau'].'">
          <input type="hidden" name="tex_encabe'.$mElemento['cod_subcau'].'" id="tex_encabe'.$mElemento['cod_subcau'].'ID" value="'.$mElemento['tex_encabe'].'">
          <input type="hidden" name="des_texto'.$mElemento['cod_subcau'].'"  id="des_texto'.$mElemento['cod_subcau'].'ID"  value="'.$mElemento['des_texto'].'">
          ';
        
        $requer = $mElemento['ind_requer'] == '1' ? '<b>*</b>': '';
        
      $mHtml .= '<tr>';   
        $mHtml .= '<td colspan="2" class="'.$class.'" align="right">'.$mElemento['tex_encabe'].'&nbsp;'.$requer.'&nbsp;:&nbsp;&nbsp;</td>';
        $mHtml .= '<td colspan="2" class="'.$class.'" align="left">'.$mElemento['htm_config'].''.$adicioInputs.'</td>';
      $mHtml .= '</tr>';   
      }
      
      $i++;
    }
      
    $mHtml .= '<tr>';
    $mHtml .= '<td class="cellInfo1"><input type="radio" name="ind_activo" id="ind_activoID" value="R" />Realizado</td>';
    
    $perfiles = array(1,7,8,73,74,713, 705);

    if( in_array( $_SESSION['datos_usuario']['cod_perfil'], $perfiles ) )
    $mHtml .= '<td class="cellInfo1"><input type="radio" name="ind_activo" id="ind_activoID" value="S" />Cliente</td>';
    
    $mHtml .= '<td class="cellInfo1">&nbsp;</td>';
    $mHtml .= '<td class="cellInfo1">&nbsp;</td>';
    
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';    
    $mHtml .= '<input type="hidden" name="tot_protoc" id="tot_protocID" value="'.sizeof( $_PROTOC ).'">';
    $mHtml .= '<hr><center><input class="crmButton small save" type="button" onclick="SaveProtocols();" value="Aceptar" name="Aceptar"></center>';
    
    
    $mHtml .= '</div></center>';
    echo $mHtml;
  }
  
  protected function ValidaProtocNoveda( $_AJAX )
  {
    $mSelect = "SELECT a.cod_respon, a.nom_respon 
                  FROM ".BASE_DATOS.".tab_genera_respon a, 
                       ".BASE_DATOS.".tab_genera_perfil b
                 WHERE a.cod_respon = b.cod_respon AND
                       b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_PERFIL = $consulta -> ret_matriz();
                       
    $mSelect = "SELECT a.cod_protoc, b.des_protoc
                  FROM ". BASE_DATOS .".tab_noveda_protoc a, 
                       ". BASE_DATOS .".tab_genera_protoc b
                 WHERE a.cod_protoc = b.cod_protoc
                   AND a.cod_transp = '".$_AJAX['cod_transp']."'
                   AND a.cod_noveda = '".$_AJAX['cod_noveda']."'";
                   
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_PROTOC = $consulta -> ret_matriz();

    if( sizeof( $_PROTOC ) > 0 /*&& sizeof( $_PERFIL ) > 0 */)
    {
      $TO_RETURN = 'y';
    }
    else
    {
      $TO_RETURN = 'n';
    }
    echo $TO_RETURN;
  }
}

$proceso = new AjaxDespacNoveda();
 ?>
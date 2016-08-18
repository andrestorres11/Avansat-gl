<?php
#--------------------------------------------------------------------
#@class: AjaxCentroNotificacion                                 -----
#@company: Intrared.net                                         -----
#author: Felipe Malaver (felipe.malaver@intrared.net )          -----       
#@date:  2013-12-11                                             -----                                
#@brief: Clase que hace las transacciones del módulo            -----                                          
#        'Centros de Notificación'                              -----                                          
#--------------------------------------------------------------------


class AjaxCentroNotificacion
{
  var $conexion;
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  protected function ValidateTransp( $_AJAX )
  {
    $mSql = "SELECT 1
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = ".$_AJAX['filter']." AND
                    a.cod_tercer = '". trim($_AJAX['cod_transp']) ."'";
		
		$consulta = new Consulta( $mSql, $this -> conexion );
		$transpor = $consulta -> ret_matriz();
    if( sizeof( $transpor ) > 0 )
      echo 'y';
    else
      echo 'n';
  }
  
  protected function InsertCennot( $_AJAX )
  {  
    switch(  $_AJAX['opc'] )
    {
      case 'update':
        $mUpdate = "UPDATE ".BASE_DATOS.".tab_genera_cennot 
                       SET nom_cennot = '".$_AJAX['nom_cennot']."', 
                           des_cennot = '".$_AJAX['des_cennot']."',
                           usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                           fec_modifi = NOW()
                     WHERE cod_transp = '".trim($_AJAX['transp'])."'
                       AND cod_cennot = '".$_AJAX['notify']."'";
        
        $consulta = new Consulta( $mUpdate, $this -> conexion );
      break;      
      
      case 'delete':
        $mDelete = "DELETE FROM ".BASE_DATOS.".tab_contac_cennot 
                     WHERE cod_transp = '".trim($_AJAX['transp'])."'
                       AND cod_cennot = '".$_AJAX['notify']."'";

        $consulta = new Consulta( $mDelete, $this -> conexion );
        
        $mDelete = "DELETE FROM ".BASE_DATOS.".tab_genera_cennot 
                     WHERE cod_transp = '".trim($_AJAX['transp'])."'
                       AND cod_cennot = '".$_AJAX['notify']."'";
        
        $consulta = new Consulta( $mDelete, $this -> conexion );
      break;      
      
      case 'insert':
        $mSql = "SELECT MAX( cod_cennot ) FROM ".BASE_DATOS.".tab_genera_cennot WHERE cod_transp = '".trim($_AJAX['transp'])."'";
        $consulta = new Consulta( $mSql, $this -> conexion );
        $num_consec = $consulta -> ret_matriz();
        $consecutivo = $num_consec[0][0] + 1; 
        
        
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_genera_cennot
                              ( cod_transp, cod_cennot, nom_cennot, 
                                des_cennot, usr_creaci, fec_creaci ) 
                       VALUES ( '".trim($_AJAX['transp'])."', ".$consecutivo.", '".$_AJAX['nom_cennot']."', 
                                '".$_AJAX['des_cennot']."','".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        
        $consulta = new Consulta( $mInsert, $this -> conexion );
      break;
    }
  }
  
  protected function FormManageNotify( $_AJAX )
  {
    $_CENTRO = $this -> getCentros( $_AJAX['cod_transp'], $_AJAX['id_notify'] );
    $mHtml  = '<div style="background-color: rgb(240, 240, 240); border: 1px solid rgb(201, 201, 201); padding: 5px; width: 98%; min-height: 50px; border-radius: 5px 5px 5px 5px;" >';
      $mHtml .= '<form name="form" id="formID">';
        $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
     
    if( $_AJAX['opc'] == 'delete' )
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td align="center" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;">Si Realmente desea Eliminar el Centro de Operaci&oacute;n <<b>'.utf8_encode( $_CENTRO[0]['nom_cennot'] ).'</b>> Haga click en \'CONTINUAR\', de lo Contrario Presione \'CERRAR\'</td>';
      $mHtml .= '</tr>';
    }
    else
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Nombre:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><input type="text" id="nom_cennotID" size="50" name="nom_cennot" onblur="ClearForm();" value="'.utf8_encode( $_CENTRO[0]['nom_cennot'] ).'"/></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Descripci&oacute;n:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><textarea id="des_cennotID"  onblur="ClearForm();"  rows="4" cols="47" name="des_cennot" >'.utf8_encode( $_CENTRO[0]['des_cennot'] ).'</textarea></td>';
      $mHtml .= '</tr>';
    }
          $mHtml .= '<tr>';
          $mHtml .= '<td>
                     <input type="hidden" id="notifyID" value="'.$_CENTRO[0]['cod_cennot'].'"/>
                     <input type="hidden" id="cod_transpID" value="'.$_AJAX['cod_transp'].'"/></td>
                     <input type="hidden" id="opcID" value="'.$_AJAX['opc'].'"/></td>';
          
          $mHtml .= '</tr>';
        $mHtml .= '</table>';
      $mHtml .= '</form>';
    $mHtml .= '</div>';

    echo $mHtml;
  }
  
  protected function LoadNotify( $_AJAX )
  {
    $_AJAX['cod_transp'] = trim( $_AJAX['cod_transp'] );
    
    $_CENTROS = $this -> getCentros( $_AJAX['cod_transp'] );
    
    $Message = '<br>Si desea un nuevo Centro de Operaci&oacute;n haga click <a onclick="Manage(\'insert\', \'0\');" style="text-decoration:none; cursor:pointer;">aqu&iacute;</a></p>';
    
    if( sizeof( $_CENTROS ) > 0 )
    {
      $this -> Style();
      $mHtml = '<p align="left" style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"> A continuaci&oacute;n usted encuentra una lista de todos los centros de Operaci&oacute;n asignados a la Transportadora.';
      $mHtml .= $Message;
      $mHtml .= '
        <script>
          $(function() {
            $( "#NotifyID" ).tabs();
          });
        </script>';
      $mHtml .= '<div id="NotifyID">';
      $mHtml .= '<ul>';
      foreach( $_CENTROS as $centro )
      {
        $mHtml .= '<li><a href="#centro-'.$centro['cod_cennot'].'">'.utf8_encode( $centro['nom_cennot'] ).'</a></li>';
      }
      $mHtml .= '</ul>';
      
      foreach( $_CENTROS as $centro )
      {
        $mHtml .= '<div id="centro-'.$centro['cod_cennot'].'">';
          $mHtml .= '<div style="overflow: hidden;">';
            $mHtml .= '<div style=" float:left; background-color: rgb(240, 240, 240); border: 1px solid rgb(201, 201, 201); padding: 5px; width: 20%; min-height: 50px; border-radius: 5px 5px 5px 5px;" >';
            $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
            $mHtml .= '<tr>';
            $mHtml .= '<td width="100%" style="border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" align="center">'.utf8_encode( $centro['nom_cennot'] ).'</td>';
            $mHtml .= '</tr>';
            
            $mHtml .= '<tr>';
            $mHtml .= '<td align="left" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b>Descripci&oacute;n:</b></td>';
            $mHtml .= '</tr>';
            
            $mHtml .= '<tr>';
            $mHtml .= '<td width="20%" style="text-align:justify; padding-right:3px; padding-top:3px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;">'.utf8_encode( $centro['des_cennot'] ).'</td>';
            $mHtml .= '</tr>';
            
            $mHtml .= '<tr>';
            $mHtml .= '<td width="20%" style="text-align:right; padding-right:3px; padding-top:3px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><a href="#" style="color:#285C00; text-decoration:none;" onclick="Manage(\'update\', \''.$centro['cod_cennot'].'\');" >Editar</a>     |    <a href="#" style="color:#285C00; text-decoration:none;" onclick="Manage(\'delete\', \''.$centro['cod_cennot'].'\');" >Eliminar</a></td>';
            $mHtml .= '</tr>';
            
            $mHtml .= '</table>';
            $mHtml .= '</div>';
            
            $mHtml .= '<div style=" margin-left:21%; background-color: rgb(240, 240, 240); border: 1px solid rgb(201, 201, 201); padding: 5px; width: 77%; min-height: 50px; border-radius: 5px 5px 5px 5px;" >';
            $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
            $mHtml .= '<tr>';
            $mHtml .= '<td width="100%" style="border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" colspan="8" align="center">Contactos</td>';
            $mHtml .= '</tr>';
            
            $MessCon = '<br>Si desea un nuevo Contacto haga click <a onclick="Contact(\'insert\', \''.$_AJAX['cod_transp'].'\', \''.$centro['cod_cennot'].'\', \'0\');" style="color:#285C00; text-decoration:none; cursor:pointer;">aqu&iacute;</a></p><br></td></tr>';
            $_CONTAC = $this -> getContactos( $_AJAX['cod_transp'],$centro['cod_cennot'] );

            if( sizeof( $_CONTAC ) > 0 )
            {
              $mHtml .= '<tr><td colspan="8"><p align="left" style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"> A continuaci&oacute;n usted encuentra una lista de todos los Contactos asigandos al Centro de Operaci&oacute;n <<b>'.utf8_encode( $centro['nom_cennot'] ).'</b>>.';
              $mHtml .= $MessCon;
              
              $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead" align="center"> NOMBRES </td>';
              $mHtml .= '<td class="CellHead" align="center"> CARGO </td>';
              $mHtml .= '<td class="CellHead" align="center"> EMAIL </td>';
              $mHtml .= '<td class="CellHead" align="center"> DIRECCI&Oacute;N </td>';
              $mHtml .= '<td class="CellHead" align="center"> TEL&Eacute;FONO </td>';
              $mHtml .= '<td class="CellHead" align="center"> TIPO </td>';
              $mHtml .= '<td class="CellHead" align="center"> CIUDAD </td>';
              $mHtml .= '<td class="CellHead" align="center"> ACCIONES </td>';
              $mHtml .= '</tr>';
              
              $i = 0;
              foreach( $_CONTAC as $contacto )
              {
                $style = $i % 2 == 0 ? 'cellInfo1' : 'cellInfo2' ; 
                $tipdes = $this -> getTipDes( $contacto['cod_tipdes'] );
                $ciudad = $this -> getCiudad( $contacto['cod_ciucon'] );
                $mHtml .= '<tr>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.utf8_encode( $contacto['nom_contac'] ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.utf8_encode( $contacto['car_contac'] ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.utf8_encode( $contacto['ema_contac'] ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.utf8_encode( $contacto['dir_contac'] ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.$contacto['tel_contac'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.$tipdes[0]['nom_tipdes'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" >'.utf8_encode( $ciudad[0]['nom_ciudad'] ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center" ><a onclick="Contact(\'update\', \''.$_AJAX['cod_transp'].'\', \''.$centro['cod_cennot'].'\', \''.$contacto['cod_contac'].'\');" href="#" style="color:#285C00; text-decoration:none;">Editar</a> | <a onclick="Contact(\'delete\', \''.$_AJAX['cod_transp'].'\', \''.$centro['cod_cennot'].'\', \''.$contacto['cod_contac'].'\');" href="#" style="color:#285C00; text-decoration:none;">Eliminar</a></td>';
                $mHtml .= '</tr>';
                $i++;
              }
              
            }
            else
            {
              $mHtml .= '<tr><td colspan="8"><p align="left" style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px;">El Centro de Operaci&oacute;n <<b>'.utf8_encode( $centro['nom_cennot'] ).'</b>> no tiene Contactos asignados.';
              $mHtml .= $MessCon;
            }
            
            $mHtml .= '</table>';
            $mHtml .= '</div>';
          $mHtml .= '</div>';
        $mHtml .= '</div>';
      }

      $mHtml .= '</div>';
    }
    else
    {
      $mHtml = '<p align="left" style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"> La transportadora no tiene Centros de Operaci&oacute;n asociados.';
      $mHtml .= $Message;
    }
    echo $mHtml;
  }
  
  private function getTipDes( $cod_tipdes = NULL )
  {
    $mSql = "SELECT cod_tipdes, nom_tipdes 
               FROM ".BASE_DATOS.".tab_genera_tipdes";
    if( $cod_tipdes != NULL )
    {
      $mSql .= " WHERE cod_tipdes = ".$cod_tipdes;      
    }
    $mSql .= " ORDER BY 2";
    $consulta = new Consulta( $mSql, $this -> conexion );
		return $consulta -> ret_matriz();
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
  
  protected function InsertContact( $_AJAX )
  {
    echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";
    $_AJAX['transp'] = trim( $_AJAX['transp'] ); 
    switch( $_AJAX['opc'] )
    {
      case 'update':
        echo $mUpdate = "UPDATE ".BASE_DATOS.".tab_contac_cennot
                    SET nom_contac = '".$_AJAX['nom_contac']."', 
                        car_contac = '".$_AJAX['car_contac']."',
                        ema_contac = '".$_AJAX['ema_contac']."',
                        dir_contac = '".$_AJAX['dir_contac']."',
                        tel_contac = '".$_AJAX['tel_contac']."',
                        cod_tipdes = '".$_AJAX['cod_tipdes']."',
                        cod_ciucon = '".$_AJAX['cod_ciucon']."' ,
                        usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                        fec_modifi = NOW()
                  WHERE cod_transp = '".$_AJAX['transp']."'
                    AND cod_cennot = '".$_AJAX['cod_cennot']."'
                    AND cod_contac = '".$_AJAX['contact_id']."'";
        
        $consulta = new Consulta( $mUpdate, $this -> conexion );
      break;
      
      case 'delete':
        echo $mDelete = "DELETE FROM ".BASE_DATOS.".tab_contac_cennot
                  WHERE cod_transp = '".$_AJAX['transp']."'
                    AND cod_cennot = '".$_AJAX['cod_cennot']."'
                    AND cod_contac = '".$_AJAX['contact_id']."'";
        
        $consulta = new Consulta( $mDelete, $this -> conexion );
      break;
      
      case 'insert':
        $mSql = "SELECT MAX( cod_contac ) FROM ".BASE_DATOS.".tab_contac_cennot WHERE cod_transp = '".$_AJAX['transp']."' AND cod_cennot = '".$_AJAX['cod_cennot']."'";
        $consulta = new Consulta( $mSql, $this -> conexion );
        $num_consec = $consulta -> ret_matriz();
        $consecutivo = $num_consec[0][0] + 1; 
        
        
        $mInsert = "INSERT INTO ".BASE_DATOS.".tab_contac_cennot 
                              ( cod_transp, cod_cennot, cod_contac,
                                nom_contac, car_contac, ema_contac, 
                                dir_contac, tel_contac, cod_tipdes,
                                cod_ciucon, usr_creaci, fec_creaci) 
                       VALUES ( '".$_AJAX['transp']."', '".$_AJAX['cod_cennot']."', ".$consecutivo.",
                                '".$_AJAX['nom_contac']."', '".$_AJAX['car_contac']."', '".$_AJAX['ema_contac']."',
                                '".$_AJAX['dir_contac']."', '".$_AJAX['tel_contac']."', '".$_AJAX['cod_tipdes']."',
                                '".$_AJAX['cod_ciucon']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
        
        $consulta = new Consulta( $mInsert, $this -> conexion );
      break;
      
      
    }
  }
  
  protected function FormManageContact( $_AJAX )
  {
    /*echo "<pre>";
    print_r( $_AJAX );
    echo "</pre>";*/
    
    $_CONTACT = $this -> getContactos( $_AJAX['cod_transp'], $_AJAX['cod_cennot'], $_AJAX['contact_id']);
    
    /*echo "<pre>";
    print_r( $_CONTACT );
    echo "</pre>";*/
    
    $mHtml  = '<div style="background-color: rgb(240, 240, 240); border: 1px solid rgb(201, 201, 201); padding: 5px; width: 98%; min-height: 50px; border-radius: 5px 5px 5px 5px;" >';
      $mHtml .= '<form name="form" id="formID">';
        $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">';

    if( $_AJAX['opc'] == 'delete' )
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td align="center" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;">Si Realmente desea Eliminar el Contacto <<b>'.utf8_encode( $_CONTACT[0]['nom_contac'] ).'</b>> Haga click en \'CONTINUAR\', de lo Contrario Presione \'CERRAR\'</td>';
      $mHtml .= '</tr>';
    }
    else
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Nombre:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><input type="text" id="nom_contacID" size="70" name="nom_contac" onblur="ClearContact();" value="'.utf8_encode( $_CONTACT[0]['nom_contac'] ).'"/></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Cargo:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><input type="text" id="car_contacID" size="45" name="car_contac" onblur="ClearContact();" value="'.utf8_encode( $_CONTACT[0]['car_contac'] ).'"/></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Correo Electr&oacute;nico:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><input type="text" id="ema_contacID" size="50" name="ema_contac" onblur="ClearContact();" value="'.utf8_encode( $_CONTACT[0]['ema_contac'] ).'"/></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Direcci&oacute;n:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><input type="text" id="dir_contacID" size="40" name="dir_contac" onblur="ClearContact();" value="'.utf8_encode( $_CONTACT[0]['dir_contac'] ).'"/></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Tel&eacute;fono:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><input type="text" id="tel_contacID" size="20" name="tel_contac" onblur="ClearContact();" value="'.utf8_encode( $_CONTACT[0]['tel_contac'] ).'"/></td>';
      $mHtml .= '</tr>';
      
      $tipdes = $this -> getTipDes();
      $ciudad = $this -> getCiudad();
      
      $SelectTipdes  = '<select id="cod_tipdesID" name="cod_tipdes" onblur="ClearContact();">';
      $SelectTipdes .= '<option value="">Seleccione</option>';
      
      foreach( $tipdes as $tip )
      {
        $selected = '';
        if( $tip['cod_tipdes'] == $_CONTACT[0]['cod_tipdes'] )
          $selected = 'selected';
        
        $SelectTipdes .= '<option value="'.$tip['cod_tipdes'].'" '.$selected.'>'.$tip['nom_tipdes'].'</option>';  
      }
      $SelectTipdes .= '</select>';
      
      $SelectCiudad  = '<select id="cod_ciuconID" name="cod_ciucon" onblur="ClearContact();">';
      $SelectCiudad .= '<option value="">- Seleccione -</option>';
      
      foreach( $ciudad as $ciu )
      {
        $selected = '';
        if( $ciu['cod_ciudad'] == $_CONTACT[0]['cod_ciucon'] )
          $selected = 'selected';
        
        $SelectCiudad .= '<option value="'.$ciu['cod_ciudad'].'" '.$selected.'>'.$ciu['nom_ciudad'].'</option>';  
      }
      $SelectCiudad .= '</select>';
      
      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Tipo:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;">'.$SelectTipdes.'</td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td align="right" width="20%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;"><b> * Ciudad:&nbsp;&nbsp;</b></td>';
      $mHtml .= '<td align="left" width="80%" style="padding-right:3px; padding-top:15px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;">'.$SelectCiudad.'</td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td>
      <input type="hidden" id="notifyID" value="'.$_CENTRO[0]['cod_cennot'].'"/>
      <input type="hidden" id="cod_transpID" value="'.$_AJAX['cod_transp'].'"/></td>
      <input type="hidden" id="opcID" value="'.$_AJAX['opc'].'"/></td>';

      $mHtml .= '</tr>';
    }
        $mHtml .= '</table>';
      $mHtml .= '</form>';
    $mHtml .= '</div>';
    echo $mHtml;
  }
  
  private function Style()
  {
    echo '
        <style>
        
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
        </style>';
  }
  private function getContactos( $cod_transp, $cod_cennot, $cod_contac = NULL )
  {
    $mQuery = "SELECT cod_contac, nom_contac, car_contac, 
                      ema_contac, dir_contac, tel_contac, 
                      cod_tipdes, cod_ciucon
                 FROM ".BASE_DATOS.".tab_contac_cennot 
                WHERE cod_transp  = '". $cod_transp ."' 
                  AND cod_cennot = '".$cod_cennot."'";
    if( $cod_contac != NULL )
    {
      $mQuery .= " AND cod_contac = '".$cod_contac."'";
    }
    $consulta = new Consulta( $mQuery, $this -> conexion );
		return $consulta -> ret_matriz();
  }
  
  private function getCentros( $cod_transp, $id_notify = NULL )
  {
    $mQuery = "SELECT cod_cennot, nom_cennot, des_cennot 
                 FROM ".BASE_DATOS.".tab_genera_cennot 
                WHERE cod_transp = '".$cod_transp."'";
    if( $id_notify != NULL )
    {
      $mQuery .= " AND cod_cennot = ".$id_notify;
    }
    $consulta = new Consulta( $mQuery, $this -> conexion );
		return $consulta -> ret_matriz();
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
       $data [] = '{"label":"'.$transpor[$i][0].' - '.$transpor[$i][1].'","value":"'. $transpor[$i][0].' - '.$transpor[$i][1].'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }
}

$proceso = new AjaxCentroNotificacion();
 ?>
<?php
/******************************************************************************************************
 * Clase NewPassword para funcionalidad de cambio de contraseña                                       *
 * @brief la clase verifica el login del usuario y pide el cambio de contraseña si está ha expirado.  *
 * @version 0.1                                                                                       *
 * @ultima_modificacion 27 de Abril de 2010                                                           *
 * @author Christiam Barrera Arango( The Messias )                                                    *
 ******************************************************************************************************/
class NewPassword
{
  var $conexion    =  NULL;
  var $cod_usuari  =  NULL;
  var $clv_usuari  =  NULL;
  var $app_namexx  =  NULL;
  var $opt_namexx  =  NULL;
  var $val_submit  =  NULL;
  var $num_credit  =  NULL;
  
  function __construct( $mData )
  {
    /*
    echo "<pre>";
    print_r( $mData );
    echo "</pre>";
    */
    //---------------------------------------------------------------------------------------
    //@SI NO LLEGAN LAS VARIABLES DE LOGIN O SI LA ACCION ES MAIN
    if ( $mData['usuario'] == NULL || $mData['clave'] == NULL || $mData['action'] == 'mainSATT' )
    {
      return FALSE;
    }
    if ( !file_exists( 'constantes.inc' ) )
    {
      die( 'El Archivo de Constantes del Directorio Cliente no existe o no ha sido debidamente configurado.' );
    }
    //include( 'constantes.inc' );
    //--------------------------------------------------------------------------
    $this -> conexion = new Conexion( "bd10.intrared.net:3306", USUARIO, CLAVE, BASE_DATOS );
    //--------------------------------------------------------------------------
    $this -> cod_usuari  =  $mData['usuario'];
    $this -> clv_usuari  =  $mData['clave'];
    $this -> app_namexx  =  $mData['app'];
    $this -> opt_namexx  =  $mData['op'];
    $this -> val_submit  =  $mData['Submit'];
    //---------------------------------------------------------------------------------------------------
    //@SI LA AUTENTICACIÓN NO ES CORRECTA O SI NO ESTA ACTIVO EL PARAMETRO DE AUTORIZACION
    if ( !$this -> VerifyUser( $mData['usuario'], $mData['clave'] ) /*|| !$this -> VerifyPasswordAutori()*/ )
    {
      return FALSE;
    }
    
    
    //---------------------------------------------------------------------------------------
    //@SE OBTIENE LA INFORMACIÓN DEL USUARIO AUTENTICADO
    $_USER  =  $this -> GetUserData( $this -> cod_usuari, $this -> clv_usuari );
    
    /*
    echo '<pre>';
    print_r( $_USER );
    echo '</pre>';
    */
    
    
    $_USER['num_credit'] = $this -> GetCredits( $_USER['num_diasxx'], $_USER['fec_cambio'] );
    $this -> num_credit  = $_USER['num_credit'];
    //---------------------------------------------------------------------------------------
    //@SI NO SE HA ACTIVADO EL INDICADOR DE CAMBIO DE PASSWORD O SI HAY CERO DIAS PARA CADUCAR
    if ( $_USER['ind_cambio'] == '0' || $_USER['num_diasxx'] == '0' )
    {
      return FALSE;
    }
    //---------------------------------------------------------------------------------------
    //@SI NO SE HA INICIADO CONTEO DE CREDITOS, ES DECIR FECHA ACTUAL MENOR POR MAS DE 5 DIAS
    //echo "<h1>num_credit : " . $this -> num_credit . "</h1>";;
    if ( $this -> num_credit === FALSE )
    {
      return FALSE;
    }
    //---------------------------------------------------------------------------------------
    $mData['action'] = $mData['action'] == NULL ? 'notify' : $mData['action'];
    switch ( $mData['action'] )
    {
      case 'notify' :
        $form   = NULL;
        $onload = 'ConfirmCredit( \''.$this -> cod_usuari.'\', '.$this -> num_credit.' )';
        $html   = $this -> LaunchForm( $form, $onload );
        echo $html;
        die();
      break;
      case 'form' :

        
        
        $t_style = 'border:5px solid #0000ff; color:#333333; font-family:Arial; font-size:12px; font-weight:bold; background:#FFFFFF;';
        $h_style = 'background:#0000ff; color:#FFFFFF; font-family:Arial; font-size:13px; font-weight:bold;';
        $i_style = 'border:1px solid #CCCCCC; color:#333333; font-family:Arial; font-size:12px; font-weight:bold;';
        $b_style = 'border:1px solid #0000ff; color:#FFFFFF; font-family:Arial; font-size:12px; font-weight:bold; background:#0000ff; width:100px; cursor:pointer;';
        
        $newline  = "\n        ";
        $newline .= '<tr><td align="left" width="100%" colspan="2">&nbsp;</td></tr>';
        
        $form   = NULL;
        $form  .= '<br /><br /><br /><br /><br /><br />';
        $form  .= '<table align="center" width="55%" style="'.$t_style.'">';
        $form  .= "\n        ";
        $form  .= '<tr>';
        $form  .= "\n          ";
        $form  .= '<th align="left" width="100%" colspan="2" style="'.$h_style.'">CAMBIO DE CONTRASEÑA</th>';
        $form  .= "\n        ";
        $form  .= '</tr>';
        
        $form  .= $newline;
        
        $form  .= "\n        ";
        $form  .= '<tr>';
        $form  .= "\n          ";
        $form  .= '<td align="right" width="40%"><label>Nombre de Usuario:</label></td>';
        $form  .= "\n          ";
        $form  .= '<td align="left" width="60%"><label style="color:#0000ff;">'.$this -> cod_usuari.'</label></td>';
        $form  .= "\n        ";
        $form  .= '</tr>';
        
        $form  .= $newline;
        
        $form  .= "\n        ";
        $form  .= '<tr>';
        $form  .= "\n          ";
        $form  .= '<td align="right" width="40%"><label style="cursor:pointer" for="clv_anteriID">* Contraseña Actual:</label></td>';
        $form  .= "\n          ";
        $form  .= '<td align="left" width="60%"><input type="password" name="clv_anteri" id="clv_anteriID" maxlength="20" size="35" style="'.$i_style.'" /></td>';
        $form  .= "\n        ";
        $form  .= '</tr>';
        
        $form  .= $newline;
        
        $form  .= "\n        ";
        $form  .= '<tr>';
        $form  .= "\n          ";
        $form  .= '<td align="right" width="40%"><label style="cursor:pointer" for="clv_usuariID">* Contraseña Nueva:</label></td>';
        $form  .= "\n          ";
        $form  .= '<td align="left" width="60%"><input type="password" name="clv_usuari" id="clv_usuariID" maxlength="20" size="35" style="'.$i_style.'" onblur="ValidatePassword( this );" /></td>';
        $form  .= "\n        ";
        $form  .= '</tr>';
        
        $form  .= $newline;
        
        $form  .= "\n        ";
        $form  .= '<tr>';
        $form  .= "\n          ";
        $form  .= '<td align="right" width="40%"><label style="cursor:pointer" for="clv_retypeID">* Confirmar Contraseña Nueva:</label></td>';
        $form  .= "\n          ";
        $form  .= '<td align="left" width="60%"><input type="password" name="clv_retype" id="clv_retypeID" maxlength="20" size="35" style="'.$i_style.'" onblur="ValidatePassword( this );" /></td>';
        $form  .= "\n        ";
        $form  .= '</tr>';
        
        $form  .= $newline;
        
        $form  .= "\n        ";
        $form  .= '<tr>';
        $form  .= "\n          ";
        $form  .= '<td align="center" width="100%" colspan="2">';
        $form  .= "\n            ";
        $form  .= '<input type="button" name="update" id="updateID" value="Actualizar" onclick="javascript:UpdatePassword();" style="'.$b_style.'" />';
        if ( $this -> num_credit > 0 )
        {
          $form  .= "\n            ";
          $form  .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
          $form  .= "\n            ";
          $form  .= '<input type="button" name="gotosat" id="gotosatID" value="Ir al SAT" onclick="javascript:document.getElementById( \'actionID\' ).value = \'mainSATB\'; document.getElementById( \'frm_changeID\' ).submit();" style="'.$b_style.'" />';
        }
        $form  .= "\n          ";
        $form  .= '</td>';
        $form  .= "\n        ";
        $form  .= '</tr>';
        
        $form  .= $newline;
        
        $form  .= "\n      ";
        $form  .= '</table>';
        
        $onload = "document.getElementById( 'clv_anteriID' ).focus()";
        $html   = $this -> LaunchForm( $form, $onload, $mData['action'] );
        echo $html;
        die();
      break;
      case 'update_password' :
        if ( $mData['clv_anteri'] == NULL || $mData['clv_usuari'] == NULL || $mData['clv_retype'] == NULL )
        {
          die( 'Acción abortada por INTRARED.NET. Alguno de los datos no ha sido diligenciado.' );
        }
        if ( $mData['clv_anteri'] !== $mData['clave'] )
        {
          die( 'Acción abortada por INTRARED.NET. Autentificación de Contraseña Incorrecta.' );
        }
        if ( $mData['clv_usuari'] !== $mData['clv_retype'] )
        {
          die( 'Acción abortada por INTRARED.NET. La Confirmación de la Nueva Contraseña es Incorrecta.' );
        }
        if ( $mData['clv_usuari'] == $mData['clv_anteri'] )
        {
          die( 'Acción abortada por INTRARED.NET. La Clave Nueva no puede ser igual a la clave anterior.' );
        }
        if ( $mData['clv_usuari'] == $this -> cod_usuari )
        {
          die( 'Acción abortada por INTRARED.NET. La Clave Nueva no puede ser igual al nombre del usuario.' );
        }
        if ( strlen( $mData['clv_usuari'] ) > 20 )
        {
          die( 'Acción abortada por INTRARED.NET. La Contraseña debe ser maximo de 20 caracteres.' );
        }
        if ( strlen( $mData['clv_usuari'] ) < 8 )
        {
          die( 'Acción abortada por INTRARED.NET. La Contraseña debe ser minimo de 8 caracteres.' );
        }
        
        
        $file = fopen( "../".DIR_APLICA_CENTRAL."/lib/general/password.lst", "rb" ) or die( "error abriendo el archivo" );
        while( !feof( $file ) )
        {
         $linea = trim( fgets( $file ) );
         if ( $linea == $mData['clv_usuari'] ) {
           die( 'Acción abortada por INTRARED.NET. Su Contraseña es muy débil y puede ser deducida con facilidad.' );
         }
        }
        fclose( $file );
        
        
        
        
        //-------------------------------------------------------------------------------------------------
        //@ACTUALIZACIÓN DE LA CONTRASEÑA EN LA TABLA DE USUARIOS
        $mSql  = "UPDATE ".BASE_DATOS.".tab_genera_usuari 
                     SET clv_usuari = '".base64_encode( $mData['clv_usuari'] )."', 
                         clv_anteri = '".base64_encode( $mData['clv_anteri'] )."', 
                         fec_cambio = '".date( 'Y-m-d H:i:s' )."' 
                   WHERE cod_usuari = '".$this -> cod_usuari."' 
                     AND clv_usuari = '".base64_encode( $this -> clv_usuari )."' ";
        $execute = new Consulta( $mSql, $this -> conexion, "BRC" );
        //-------------------------------------------------------------------------------------------------
        //@CARGA EL FORMULARIO DE CONFIRMACIÓN DE ACTUALIZACIÓN      
        $form   = NULL;
        $onload = 'ConfirmUpdatePassword( \''.$this -> cod_usuari.'\', \''.$_USER['num_diasxx'].'\' )';
        $html   = $this -> LaunchForm( $form, $onload );
        echo $html;
        die();
      break;
    }
  }
  
  
  function LaunchForm( $form = NULL, $onload = NULL, $action = NULL )
  {
    $html  = NULL;
    $html .= '<html>';
    $html .= "\n  ";
    $html .= '<head>';
    $html .= "\n    ";
    $html .= '<title>SAT TRAFICO - Autenticación y Cambio de Contraseña</title>';
    $html .= "\n    ";
    $html .= '<script src="../'.DIR_APLICA_CENTRAL.'/js/functions.js'.'" type="text/javascript"></script>';
    $html .= "\n    ";
    $html .= '<script src="../'.DIR_APLICA_CENTRAL.'/js/new_password.js'.'" type="text/javascript"></script>';
    $html .= "\n  ";
    $html .= '</head>';
    $html .= "\n  ";
    $html .= '<body onload="javascript:'.$onload.'" background="../'.DIR_APLICA_CENTRAL.'/imagenes/new_password/fondo_satb.jpg">';
    $visibility = $action == 'form' ? 'hidden' : 'visible';
    $html .= "\n    ";
    $html .= '<div id="central_transparencyDIV" style="position: absolute; filter:alpha(opacity=30); opacity:0.2; left:0px; top:0px; width:102%; height:100%; z-index:2; visibility:'.$visibility.'; border:1px solid black; background:#CCCCCC; "></div>';
    $html .= "\n    ";
    $html .= '<form name="frm_change" id="frm_changeID" action="?" method="post">';
    $html .= "\n      ";
    $html .= $form;
    $html .= '<input type="hidden" name="usuario" id="usuarioID" value="'.$this -> cod_usuari.'" />';
    $html .= "\n      ";
    $html .= '<input type="hidden" name="clave" id="claveID" value="'.$this -> clv_usuari.'" />';
    $html .= "\n      ";
    $html .= '<input type="hidden" name="app" id="appID" value="'.$this -> app_namexx.'" />';
    $html .= "\n      ";
    $html .= '<input type="hidden" name="op" id="opID" value="'.$this -> opt_namexx.'" />';
    $html .= "\n      ";
    $html .= '<input type="hidden" name="Submit" id="SubmitID" value="'.$this -> val_submit.'" />';
    $html .= "\n      ";
    $html .= '<input type="hidden" name="action" id="actionID" value="'.$action.'" />';
    $html .= "\n    ";
    $html .= '</form>';
    $html .= "\n  ";
    $html .= '</body>';
    $html .= "\n";
    $html .= '</html>';
    return $html;
  }
  
  /************************************************************************************************************
   * Metodo Privado que Verifica el Código de Usuario                                                         *
   * @fn VerifyUser                                                                                           *
   * @param $cod_usuari  : string Cadena con el nombre de usuario                                             *
   * @param $clv_usuari  : string Cadena con el password del usuario                                          *
   * @brief Consulta en tab_genera_usuari la existencia del código de Usuario en cod_usuari                   *
   * @return bool : TRUE si existe, FALSE si no existe                                                        *
   ************************************************************************************************************/
  function VerifyUser()
  {
    $mSql  = "SELECT 1 
                FROM ".BASE_DATOS.".tab_genera_usuari a 
               WHERE a.cod_usuari = '".$this -> cod_usuari."' 
                 AND a.clv_usuari = '".base64_encode( $this -> clv_usuari )."' ";
    $consulta = new Consulta( $mSql, $this -> conexion );
    $matrix   = $consulta -> ret_matriz( 'a' );
    return count( $matrix ) == 0 ? FALSE : TRUE;
  }
  
  /************************************************************************************************************
   * Metodo Privado que retorna los datos del Usuario                                                         *
   * @fn GetUserDat                                                                                           *
   * @brief Consulta en tab_genera_usuari la información del usuario autenticado                              *
   * @return array : arreglo asociativo con los datos del usuario                                             *
   ************************************************************************************************************/
  function GetUserData()
  {
    $mSql  = "SELECT *  
                FROM ".BASE_DATOS.".tab_genera_usuari a 
               WHERE a.cod_usuari = '".$this -> cod_usuari."' 
                 AND a.clv_usuari = '".base64_encode( $this -> clv_usuari )."' ";
    $consulta = new Consulta( $mSql, $this -> conexion );
    $matrix   = $consulta -> ret_matriz( 'a' );
    return $matrix[0];
  }
  
  /************************************************************************************************************
   * Metodo que verifica si existe autorización para validar expiración de contraseñas                        *
   * @fn VerifyPasswordAutori                                                                                 *  
   ************************************************************************************************************/
  /*function VerifyPasswordAutori()
  {
    $mSql = "SELECT a.val_parame FROM ".BASE_DATOS.".tab_genera_parame a WHERE a.nom_parame = 'ind_passwo' AND a.ind_estado = '1' ";
    $mMatriz = new Consulta( $mSql, $this -> conexion );
    $mMatriz = $mMatriz -> ret_matriz( "a" );
    return count( $mMatriz ) == 0 ? FALSE : TRUE;
  }
  
  /************************************************************************************************************
   * Metodo Privado que obtiene el número de días que restan para restablecer la contraseña                   *
   * @fn GetCredits                                                                                           *
   * @return int : entero con la cantidad de días restantes                                                   *
   ************************************************************************************************************/
  function GetCredits( $num_diasxx, $fec_cambio )
  {
    $fec_limite = date( "Y-m-d", strtotime( $fec_cambio . " +" . $num_diasxx . " days" ) );
    $fec_inicia = date( "Y-m-d", strtotime( $fec_cambio . " +" . ( $num_diasxx - 5 ) . " days" ) );
    $fec_actual = date( "Y-m-d" );
    /*
    echo "<hr />fec_limite: " . $fec_limite;
    echo "<hr />fec_inicia: " . $fec_inicia;
    echo "<hr />fec_actual: " . $fec_actual;
    */
    if ( $fec_inicia == $fec_actual )
    {
      return 5;
    }
    elseif ( $fec_inicia > $fec_actual )
    {
      return FALSE;
    }
    else
    {
      $c = 0;
      $fec_varian = $fec_inicia;
      while( $fec_varian < $fec_actual )
      {
        $fec_varian = date( "Y-m-d", strtotime( $fec_varian . " +1 day" ) );
        $c ++;
      }
      return 5 - $c;
    }
  }
  
}

$_DATA = count( $_POST ) == 0 ? $_GET : $_POST;
$new_password  =  new NewPassword( $_DATA );

?>
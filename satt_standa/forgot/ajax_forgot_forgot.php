<?php
ini_set('display_errors', FALSE );

class AjaxForgotPass
{
  var $conexion;
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    if( $_AJAX['ind_otherx'] )
    {
      include_once('../satt_standa/lib/general/constantes.inc');
      include_once('constantes.inc');
      include_once( "../satt_standa/lib/general/conexion_lib.inc" );
      include_once( "../satt_standa/lib/general/functions.inc" );
      include_once( "../satt_standa/lib/general/selects.inc" );
      include_once( "../satt_standa/lib/general/dinamic_list.inc" );
      include_once( "../satt_standa/lib/general/form_lib.inc" );
      include_once( "../satt_standa/lib/GeneralFunctions.inc" );
      include_once( "../satt_standa/lib/general/tabla_lib.inc" );
      include_once( "../satt_standa/lib/general/xml.inc" );
      include_once( "../satt_standa/lib/general/xmlparser.inc" );
      include_once( "../satt_standa/lib/bd/seguridad/aplica_filtro_perfil_lib.inc" );
      include_once( "../satt_standa/lib/bd/seguridad/aplica_filtro_usuari_lib.inc" );
      
      $AjaxConnection = new Conexion( HOST, USUARIO, CLAVE, BASE_DATOS );
    }
    else
    {
      include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
      include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
      include_once('../lib/general/constantes.inc');
      include_once('../../satt_faro/constantes.inc');
      include_once('../lib/ajax.inc');  
    }
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }

  protected function setForgot( $mData )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

    echo '<script>
          $(function() {
            $( "#validarID" ).button({
              icons: {
                primary: "ui-icon-check",
              }
            });
          });
          </script>';

    $mHtml  = '<div class="displayDIV">';
    
      $mHtml .= '<table width="100%" cellpadding="0" cellspacing="1">';
        
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="2" align="center">DATOS B&Aacute;SICOS</td>';
        $mHtml .= '</tr>';
        
        $mHtml .= '<tr>';
        $mHtml .= '<td class="cellInfo1" width="35%" align="right"><b>* Usuario:</b>&nbsp;&nbsp;</td>';
        $mHtml .= '<td class="cellInfo1" width="65%" align="left"><input type="text" name="cod_usuari" id="cod_usuariID" maxlength="25" size="20" /></td>';
        $mHtml .= '</tr>';
      
        $mHtml .= '<tr>';
        $mHtml .= '<td class="cellInfo1" width="35%" align="right"><b>* Correo Registrado:</b>&nbsp;&nbsp;</td>';
        $mHtml .= '<td class="cellInfo1" width="65%" align="left"><input type="text" name="ema_usuari" id="ema_usuariID" size="35" /></td>';
        $mHtml .= '</tr>';
      
      $mHtml .= '</table>';
      
      $mHtml .= '<center><div style="padding-top:10px;padding-bottom:5px;">';
        $mHtml .= '<button onclick="sendForgot();" id="validarID"><span style="font-size:10px;">VALIDAR</span></button>';
        $mHtml .= '</div></center>';

    $mHtml .= '</div>';

    echo $mHtml;
  }

  protected function shortURL( $url, $login, $appkey, $format = 'xml', $version = '2.0.1' )
  {
    $bitly = 'http://api.bit.ly/shorten?version='.$version.'&amp;longUrl='.urlencode($url).'&amp;login='.$login.'&amp;apiKey='.$appkey.'&amp;format='.$format;
    $response = file_get_contents( $bitly );

    if( strtolower( $format ) == 'json' )
    {
      $json = json_decode( $response, true );

      return $json['results'][$url]['shortUrl'];
    }
    else
    {
      $xml = simplexml_load_string($response);
      return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
    }
  }

  protected function sendForgot( $mData )
  {
    $mSelect = "SELECT cod_usuari 
                  FROM ".BASE_DATOS.".tab_genera_usuari
                 WHERE LOWER( cod_usuari ) = '".$mData['cod_usuari']."'
                   AND LOWER( usr_emailx ) = '".$mData['ema_usuari']."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mValida = $consulta -> ret_matriz();

    if( sizeof( $mValida ) > 0 )
    {
      //$mUrl = str_replace('http', 'https', DIREC_APLICA ).'forgot.php?cod_usuari='.base64_encode($mData['cod_usuari']).'&ema_usuari='.base64_encode($mData['ema_usuari']).'&token='.sha1( $mData['cod_usuari'] );
      $mUrl = 'https://avansatgl.intrared.net/ap/satt_faro/forgot.php?cod_usuari='.base64_encode( $mData['cod_usuari'] ).'&ema_usuari='.base64_encode( $mData['ema_usuari'] ).'&token='.sha1( $mData['cod_usuari'] );
      $mShortURL = $this -> shortURL( $mUrl, 'intrared', 'R_bded69919c544634ba7ffe709e74d4b4', 'xml' );      

      $cod_usuari = $mData['cod_usuari'];
      $tmpl_file = '../planti/pla_restau_clavex.html';
      $thefile = implode("", file( $tmpl_file ) );
      $thefile = addslashes($thefile);
      $thefile = "\$r_file=\"".$thefile."\";";
      eval( $thefile );
      $mHtml = $r_file;

      require_once("../planti/class.phpmailer.php");

      $mail = new PHPMailer();
      $mail->Host = "localhost";
      $mail->From = "no-reply@eltransporte.org";
      $mail->FromName = "Centro Logistico FARO";
      $mail->Subject = "Recuperar Contraseña";
      $mail->AddAddress( $mData['ema_usuari'] );
      $mail->Body = $mHtml;
      $mail->IsHTML( true );

      if( $mail->Send() )
        echo "y";
      else
        echo "n";
    }
    else
    {
      echo "n";
    }
  }

  protected function initialValidation( $mData )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    $mSelect = "SELECT cod_usuari 
                  FROM ".BASE_DATOS.".tab_genera_usuari
                 WHERE LOWER( cod_usuari ) = '".base64_decode( $mData['cod_usuari'] )."'
                   AND LOWER( usr_emailx ) = '".base64_decode( $mData['ema_usuari'] )."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mValida = $consulta -> ret_matriz();

    $mHtml  = '';
    $mHtml .= '<!DOCTYPE html>';
    $mHtml .= '<head>';
    $mHtml .= '<title>S.A.T. TRAFICO - M&oacute;dulo de Seguridad</title>';
    $mHtml .= '<script src="../'.BD_STANDA.'/js/min.js"></script>';
    $mHtml .= '<script src="../'.BD_STANDA.'/js/jquery.js"></script>';
    $mHtml .= '<script>
                 function changePass()
                 {
                   try
                   {
                     var nue_clavex = $("#nue_clavexID");
                     var rep_clavex = $("#rep_clavexID");
                     var validate_pass = /(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{6,15})$/;
                     
                     if( nue_clavex.val() == "" )
                     {
                       alert("Digite la Nueva Clave");
                       nue_clavex.focus();
                       return false; 
                     }
                     else if( rep_clavex.val() == "" )
                     {
                       alert("Repita la Clave");
                       rep_clavex.focus();
                       return false; 
                     }
                     else if( nue_clavex.val() != rep_clavex. val() )
                     {
                       alert("Las Claves no Coinciden");
                       rep_clavex.val("");
                       rep_clavex.focus();
                       return false;
                     }
                     else if( !nue_clavex.val().match( validate_pass ) )
                     {
                       alert("La Clave es Incorrecta, por favor rectifique:\n- Minimo 6 Caracteres\n- Maximo 15 Caracteres\n- Por lo menos un Digito\n- Por lo menos una letra\n- No se adminten caracteres especiales");
                       nue_clavex.val("");
                       rep_clavex.val("");
                       nue_clavex.focus();
                       return false;
                     }
                     else
                     {
                       $.ajax({
                        type: "POST",
                        url: "../'.BD_STANDA.'/forgot/ajax_forgot_forgot.php",
                        data: "option=changePass&cod_usuari='.$mData['cod_usuari'].'&nue_clavex="+nue_clavex.val(),
                        async: false,
                        success: function( datos )
                        {
                          $("#resultID").html( datos );
                        }
                      });
                     }
                   }
                   catch( e )
                   {
                     console.log( e.message );
                     return false;
                   }

                 }                   
               </script>';
    $mHtml .= '</head>';
    $mHtml .= '<body>';
    $mHtml .= '<br><br><br><br><br><br><div id="resultID" width="100%" class="displayDIV2">';
    $mHtml .= '<table width="100%" cellpadding="0" cellspacing="0">';
    
    if( $mData['token'] == '' || $mData['cod_usuari'] == '' || $mData['ema_usuari'] == '' || ( sha1( base64_decode( $mData['cod_usuari'] ) ) != $mData['token'] ) || sizeof( $mValida ) <= 0 ) 
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" align="center" style="padding:30px;font-size:50px;"><b>Acceso Denegado</b></td>';
      $mHtml .= '</tr>';
    }
    else
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" align="center" style="padding:10px;" colspan="2">CAMBIO DE CONTRASE&Ntilde;A</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
      $mHtml .= '<td class="cellInfo1" style="padding:10px;" align="right" width="40%">Nueva Clave:&nbsp;&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td class="cellInfo1" style="padding:10px;" align="left" width="60%"><input type="password" name="nue_clavex" id="nue_clavexID" maxlength="15" /></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td class="cellInfo1" style="padding:10px;" align="right" width="40%">Repita Clave:&nbsp;&nbsp;&nbsp;&nbsp;</td>';
      $mHtml .= '<td class="cellInfo1" style="padding:10px;" align="left" width="60%"><input type="password" name="rep_clavex" id="rep_clavexID" maxlength="15" /></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td class="cellInfo1" style="padding:10px;" align="center" colspan="2"><button class="boton" onclick="changePass();">ACEPTAR</button></td>';
      $mHtml .= '</tr>';
    }  
    $mHtml .= '</table>';
    $mHtml .= '</div>';
    $mHtml .= '</body>';
    $mHtml .= '</html>';
    echo $mHtml;
  }

  protected function changePass( $mData )
  {
    $mSelect = "SELECT 1 
                  FROM ".BASE_DATOS.".tab_genera_usuari 
                 WHERE cod_usuari = '".base64_decode( $mData['cod_usuari'] )."' 
                   AND clv_usuari = '".base64_encode( $mData['nue_clavex'] )."'";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mValida = $consulta -> ret_matriz();

    $mHtml  = '';
    $mHtml .= '<!DOCTYPE html>';
    $mHtml .= '<head>';
    $mHtml .= '<title>S.A.T. TRAFICO - M&oacute;dulo de Seguridad</title>';
    $mHtml .= '<script src="../'.BD_STANDA.'/js/min.js"></script>';
    $mHtml .= '<script src="../'.BD_STANDA.'/js/jquery.js"></script>';
    $mHtml .= '</head>';
    $mHtml .= '<body>';
    $mHtml .= '';
    $mHtml .= '<table width="100%" cellpadding="0" cellspacing="0">';
    
    if( sizeof( $mValida ) > 0 )
    {
      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" align="center" style="padding:30px;font-size:50px;"><b>Error en Autenticaci&oacute;n</b></td>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" align="center" style="padding:5px;font-size:14px;">La Nueva Contrase&ntilde;a NO debe ser id&eacute;ntica a la Anterior. Si desea volver a realizar el cambio, por favor refresque el navegador.</td>';
      $mHtml .= '</tr>';
    }
    else
    {
      $mSelect = "SELECT clv_usuari
                  FROM ".BASE_DATOS.".tab_genera_usuari 
                 WHERE cod_usuari = '".base64_decode( $mData['cod_usuari'] )."'";
    
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $mAnteri = $consulta -> ret_matriz();

      $mUpdate = "UPDATE ".BASE_DATOS.".tab_genera_usuari 
                     SET clv_usuari = '".base64_encode( $mData['nue_clavex'] )."', 
                         fec_cambio = NOW(), 
                         clv_anteri = '".$mAnteri[0][0]."'
                   WHERE cod_usuari = '".base64_decode( $mData['cod_usuari'] )."'";

      if( $consulta = new Consulta( $mUpdate, $this -> conexion ) )
      {
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" align="center" style="padding:30px;font-size:50px;"><b>La Contrase&ntilde;a fue Cambiada Satisfactoriamente</b></td>';
        $mHtml .= '</tr>';

        //$mUrl = DIREC_APLICA.'index.php';
        $mUrl = 'https://avansatgl.intrared.net/ap/satt_faro/index.php';
        
        $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" align="center" style="padding:5px;font-size:14px;"><a href="'.$mUrl.'" style="cursor:pointer;text-decoration:none;color:#FFFFFF;">Inciar Sesi&oacute;n en SAT TRAFICO</a></td>';
        $mHtml .= '</tr>';
      }
    }

    $mHtml .= '</table>';
    $mHtml .= '</body>';
    $mHtml .= '</html>';
    echo $mHtml;
  }

}

$proceso = new AjaxForgotPass();

?>
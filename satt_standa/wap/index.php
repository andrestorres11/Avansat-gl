<?php
error_reporting( E_ERROR );

include( "../constantes.inc" );
include( "../../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc" );
include( "../../".DIR_APLICA_CENTRAL."/lib/general/conexion_lib.inc" );
include( "../../".DIR_APLICA_CENTRAL."/lib/interfaz_lib_sat.inc" );
include( "../../GPS/lib/generales.inc" );
include( "../../".DIR_APLICA_CENTRAL."/despac/Despachos.inc" );
include( "../../".DIR_APLICA_CENTRAL."/lib/EnvioMensajes.inc" );
include( "../../".DIR_APLICA_CENTRAL."/lib/interfaz_lib.inc" );
include( "../../".DIR_APLICA_CENTRAL."/lib/general/paginador_lib.inc" );
//require_once("../../".DIR_APLICA_CENTRAL."/lib/nuSOAP/nusoap.php"); 
/* */
if( file_exists( "../../".DIR_APLICA_CENTRAL."/lib/GeneralFunctions.inc" ) )
  include( "../../".DIR_APLICA_CENTRAL."/lib/GeneralFunctions.inc" );

if ( file_exists( "../../".DIR_APLICA_CENTRAL."/wap/cache.inc" ) )
  include_once( "../../".DIR_APLICA_CENTRAL."/wap/cache.inc" );
/* */

if( !isset( $servicio ) )
{
  $link = new Conexion( "bd10.intrared.net:3306", USUARIO, CLAVE, BASE_DATOS );
}

function menu( $usuario, $fi1, $fi2, $fi3 )
{
    echo "<wml>\n"
        ." <card id=\"menu\" title=\"".NOM_TRANMENU."\">\n"
        ."  <p>\n"
        ."   <b>:: MENU ::</b>\n"
        ."   <select name=\"op\">\n";
     echo"    <option value=\"1\">Novedad</option>\n";
     if(!$fi2 && !$fi1 && !$fi3)
     echo"    <option value=\"2\">Salida</option>\n";
     if(!$fi2 && !$fi1 && !$fi3)
     echo"    <option value=\"3\">LLegada</option>\n";
     echo"    <option value=\"4\">Salir</option>\n"
        ."   </select>\n"
        ."   <do type=\"accept\" label=\"Aceptar\">\n"
        ."    <go href=\"index.php\">\n"
        ."     <postfield name=\"op\" value=\"\$op\"/>\n"
        ."     <postfield name=\"usuario\" value=\"$usuario\"/>\n"
        ."     <postfield name=\"fi1\" value=\"$fi1\"/>\n"
        ."     <postfield name=\"fi2\" value=\"$fi2\"/>\n"
        ."     <postfield name=\"fi3\" value=\"$fi3\"/>\n"
        ."     <postfield name=\"sad_bd\" value=\"".BASE_DATOS."\"/>\n"
        ."    </go>\n"
        ."   </do>\n"
        ."  </p>\n"
        ." </card>\n"
        ."</wml>";
}

function login()
{
  echo "<wml>\n"
      ." <card id=\"login\" title=\"".NOM_TRANMENU."\">\n"
      ."  <p>\n"
      ."     <b>Usuario:</b><input type=\"text\" name=\"usuario\"/>\n"
      ."     <b>Clave:</b><input type=\"password\" name=\"clave\"/>\n"
      ."  </p>\n"
      ."  <do type=\"accept\" label=\"Aceptar\">\n"
      ."   <go href=\"index.php\">\n"
      ."    <postfield name=\"usuario\" value=\"\$usuario\"/>\n"
      ."    <postfield name=\"clave\" value=\"\$clave\"/>\n"
      ."    <postfield name=\"fi1\" value=\"$fi1\"/>\n"
      ."    <postfield name=\"fi2\" value=\"$fi2\"/>\n"
      ."    <postfield name=\"fi3\" value=\"$fi3\"/>\n"
      ."    <postfield name=\"sad_bd\" value=\"$sad_bd\"/>\n"
      ."   </go>\n"
      ."  </do>\n"
      ." </card>"
      ."</wml>";
}//fin function login

function salir()
{
    unset($usuario);
    unset($clave);
    echo "<wml>\n"
        ." <card id=\"Salida\" title=\"".NOM_TRANMENU."\">\n"
        ."  <p align=\"center\">\n"
        ."   <br/><b>Esta Desconectado</b>\n"
        ."  </p>\n"
        ." </card>\n"
        ."</wml>";
}//fin function salir

if (isset($usuario) && isset($clave))
{
   $query = "SELECT cod_clavex,idx_filtro,cod_filtro
               FROM ".BASE_DATOS.".tab_usuari_wapxxx
              WHERE cod_usuari = '".$usuario."'";

   $consulta = new Consulta($query, $link);
   $result   = $consulta -> ret_vector();

   if (!$result)
   {
    echo "<wml>\n"
        ." <card id=\"fallo\" title=\"".NOM_TRANMENU."\">\n"
        ."  <p align=\"center\">\n"
        ."   <br/><b>El Usuario no Existe</b>\n"
        ."  </p>\n"
        ." </card>\n"
        ."</wml>";
    exit;
   }//fin if
   else
   {
     $clave_enc = base64_decode($result[0]);

     if ($clave_enc == $clave && $result[0] != "")
     {
         $op = "ok";

         if($result[1] == 1)
          $fi1 = $result[2];
         else if($result[1] == 2)
          $fi2 = $result[2];
         else if($result[1] == 3)
          $fi3 = $result[2];

     }//fin if
     else
     {
	echo "<wml>\n"
        ." <card id=\"fallo\" title=\"".NOM_TRANMENU."\">\n"
        ."  <p align=\"center\">\n"
        ."   <br/><b>La Contrasea es Incorrecta</b>\n"
        ."  </p>\n"
        ." </card>\n"
        ."</wml>";
    exit;
     }
    }//fin else
}//fin if

//var_dump( $op );
if( isset($op) )
{
  switch ($op)
  {
    case "1":
      include( "../../".DIR_APLICA_CENTRAL."/wap/novedad.php" );
    break;
    case "2":
      include( "../../".DIR_APLICA_CENTRAL."/wap/salida.php" );
    break;
    case "3":
      //var_dump( file_exists( "../../".DIR_APLICA_CENTRAL."/wap/llegada.php" ) );
      //require( "../../".DIR_APLICA_CENTRAL."/wap/llegada.php" );
      include( "../../".DIR_APLICA_CENTRAL."/wap/llegada.php" );
    break;
    case "4":
      salir();
    break;
    default:
      menu($usuario, $fi1, $fi2, $fi3);
    break;
  }//fin switch
}//fin if
else
  login();
/***************************FINAL DEL ARCHIVO**********************************/
?>

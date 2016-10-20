<?php
/****************************************************************************
NOMBRE:   MODULO_CONFIGURACION DE ALERTAS.PHP
FUNCION:  INSERTAR Y COFIGURAR ALERTAS
AUTOR: LEONARDO ROMERO
FECHA CREACION : 22 FEBRERO 2005
FECHA MODIFICACION : 24 AGOSTO 2005
****************************************************************************/
class Proc_alerta
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $datos_usuario = $this -> usuario -> retornar();
  $this -> principal();
 }
//********METODOS
 function principal()
 {
  if(!isset($_REQUEST[opcion]))
     $this -> Formulario();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Insertar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************
// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA
// *****************************************************
 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/alerta.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Configuracion de Alertas Visuales","ins_alert");
   $formulario -> linea("Datos Basicos de Alertas",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> texto ("Nombre de Alarma:","text","nom_ala",0,10,10,"","$_REQUEST[tiemp1]");
   $formulario -> texto ("Tiempo de Alarma:","text","tiempo",0,3,3,"","$_REQUEST[tiemp1]");
   $formulario -> texto ("Color:","text","color\"  onBlur=\"setColor();\"",0,7,7,"","$_REQUEST[color]");
   echo "<td nowrap='nowrap'>
         <a href='#' onClick=\"newwin=open('../".DIR_APLICA_CENTRAL."/config/colores.php?nom=color', 'calwin', 'width=320, height=300, scollbars=false');\" class='etiqueta'>Cambiar Color</a>
         </td>
         <td nowrap='nowrap'>
         <span id='test' title='test' style=\"background:#;\">
         <a href=\"#\" onClick=\"newwin=open('../".DIR_APLICA_CENTRAL."/config/colores.php?nom=color', 'calwin', 'width=320, height=300, scollbars=false');\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/shim.gif\" border=\"1\" width=\"40\" height=\"20\" /></a></span>
        </td>";
   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],1);
   $formulario -> botoni("Insertar","aceptar_insert()",0);
   $formulario -> botoni("Borrar","ins_alert.reset()",1);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA
// *****************************************************
//FUNCION INSERTAR
// *****************************************************
 function Insertar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $fec_actual = date("Y-m-d H:i:s");
   //trae el consecutivo de la tabla
  $query = "SELECT Max(cod_alarma) AS maximo
               FROM ".BASE_DATOS.".tab_genera_alarma";
   $consec = new Consulta($query, $this -> conexion);
   $ultimo = $consec -> ret_matriz();
   $ultimo_consec = $ultimo[0][0];
   $nuevo_consec = $ultimo_consec+1;

   //query de insercion
   $query = "INSERT INTO ".BASE_DATOS.".tab_genera_alarma( cod_alarma, nom_alarma, cod_colorx, cant_tiempo, usr_creaci, fec_creaci )
             VALUES ( '$nuevo_consec','$_REQUEST[nom_ala]','$_REQUEST[color]',
             '$_REQUEST[tiempo]', '$usuario', NOW() ) ";
   $consulta = new Consulta($query, $this -> conexion,"BR");

   if($insercion = new Consulta("COMMIT", $this -> conexion))
    {

      /*
      //Se inserta la alarma en los standas de sat basico y sat trafico
      $data['nom_proces'] = "Insertar Alarma";
      $mParams = array( "cod_alarma" => $nuevo_consec, "nom_alarma" => $_REQUEST[nom_ala], 
                        "cod_colorx" => $_REQUEST[color], "can_tiempo" => $_REQUEST[tiempo], "nom_llavex" => 'f74ca8ee40d8e9c9b2cd529ce297a9a8' );

      $oSoapClient = new soapclient( URL_INTERF_SATAPX, true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "setAlarma", $mParams );
      
      if ( $oSoapClient -> fault )
      {
        //Notifica Fallos
        $data['error'] = $data['nom_proces'].': '.$oSoapClient -> faultcode.':'.$oSoapClient -> faultdetail.':'.$oSoapClient -> faultstring;
      }
      else
      {
        $err = $oSoapClient -> getError();
        if ( $err ) 
        {
          // Notifica errores
          $data['error'] = $data['nom_proces'].': '.$err;
        }
        else 
        {
          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
        }
      }
      */

      /********************* PARAMETROS SOAP *********************/
      $data['nom_proces'] = "Insertar Alarma";
      $mParams = array( "cod_alarma" => $nuevo_consec,
                        "nom_alarma" => $_REQUEST[nom_ala],
                        "cod_colorx" => $_REQUEST[color],
                        "can_tiempo" => $_REQUEST[tiempo],
                        "nom_llavex" => 'f74ca8ee40d8e9c9b2cd529ce297a9a8'
                        );

      /********************* TRATAMIENTO SOAP *********************/
      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
      try
      {

          //Ruta Web Service en DEV.
          $url_webser = URL_INTERF_SATAPX;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'setAlarma', $mParams );
          /*
          echo 1;
          print_r($respuesta);
          */

          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
      }
      catch( SoapFault $e )
      {
          /*
          echo 2;
          print_r($e);
          */

          $data['error'] = $e -> getMessage();

      }

      if( $data['error'] )
      {
        $mMessage = "******** Encabezado ******** \n";
        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
        $mMessage .= "Proceso: " . $data['nom_proces'] . " \n";
        $mMessage .= "******** Detalle ******** \n";
        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
        $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";

        //echo $mMessage;
        mail("engmiguelgarcia@gmail.com", "Error WebService DEV", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }

      /********************* **************** *********************/




      /*
      $oSoapClient = new soapclient( URL_INTERF_SATFLI, true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "setAlarma", $mParams );
      
      if ( $oSoapClient -> fault )
      {
        //Notifica Fallos
        $data['error'] = $data['nom_proces'].': '.$oSoapClient -> faultcode.':'.$oSoapClient -> faultdetail.':'.$oSoapClient -> faultstring;
      }
      else
      {
        $err = $oSoapClient -> getError();
        if ( $err ) 
        {
          // Notifica errores
          $data['error'] = $data['nom_proces'].': '.$err;
        }
        else 
        {
          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
        }
      }
      */
      /********************* TRATAMIENTO SOAP *********************/
      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
      try
      {

          //Ruta Web Service en FLIRED.
          $url_webser = URL_INTERF_SATFLI;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'setAlarma', $mParams );
          /*
          echo 1;
          print_r($respuesta);
          */

          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
      }
      catch( SoapFault $e )
      {
          /*
          echo 2;
          print_r($e);
          */

          $data['error'] = $e -> getMessage();

          $mMessage = "******** Encabezado ******** \n";
          $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
          $mMessage .= "Proceso: " . $data['nom_proces'] . " \n";
          $mMessage .= "******** Detalle ******** \n";
          $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
          $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";

          //echo $mMessage;
          mail("engmiguelgarcia@gmail.com", "Error WebService FLIRED", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/





      /*
      $oSoapClient = new soapclient( URL_INTERF_SATSER, true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "setAlarma", $mParams );
      
      if ( $oSoapClient -> fault )
      {
        //Notifica Fallos
        $data['error'] = $data['nom_proces'].': '.$oSoapClient -> faultcode.':'.$oSoapClient -> faultdetail.':'.$oSoapClient -> faultstring;
      }
      else
      {
        $err = $oSoapClient -> getError();
        if ( $err ) 
        {
          // Notifica errores
          $data['error'] = $data['nom_proces'].': '.$err;
        }
        else 
        {
          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
        }
      }
      */
      /********************* TRATAMIENTO SOAP *********************/
      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
      try
      {

          //Ruta Web Service en SERVER.
          $url_webser = URL_INTERF_SATSER;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'setAlarma', $mParams );
          /*
          echo 1;
          print_r($respuesta);
          */

          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
      }
      catch( SoapFault $e )
      {
          /*
          echo 2;
          print_r($e);
          */

          $data['error'] = $e -> getMessage();

          $mMessage = "******** Encabezado ******** \n";
          $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
          $mMessage .= "Proceso: " . $data['nom_proces'] . " \n";
          $mMessage .= "******** Detalle ******** \n";
          $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
          $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";

          //echo $mMessage;
          mail("engmiguelgarcia@gmail.com", "Error WebService SERVER", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/




      /*
      $oSoapClient = new soapclient( URL_INTERF_SATCLO, true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "setAlarma", $mParams );
      
      if ( $oSoapClient -> fault )
      {
        //Notifica Fallos
        $data['error'] = $data['nom_proces'].': '.$oSoapClient -> faultcode.':'.$oSoapClient -> faultdetail.':'.$oSoapClient -> faultstring;
      }
      else
      {
        $err = $oSoapClient -> getError();
        if ( $err ) 
        {
          // Notifica errores
          $data['error'] = $data['nom_proces'].': '.$err;
        }
        else 
        {
          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
        }
      }
      */
      /********************* TRATAMIENTO SOAP *********************/
      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
      try
      {

          //Ruta Web Service en CLOUD.
          $url_webser = URL_INTERF_SATCLO;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'setAlarma', $mParams );
          /*
          echo 1;
          print_r($respuesta);
          */

          //Procesa el resultado del WS
          $mResult = explode( "; ", $mResult );
          $mCodResp = explode( ":", $mResult[0] );
          $mMsgResp = explode( ":", $mResult[1] );
          $data['cod_errorx'] = $mCodResp[1];
    
          if( "1000" != $mCodResp[1] )
          {
            //Notifica Errores retornados por el WS
            $data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
          }
      }
      catch( SoapFault $e )
      {
          /*
          echo 2;
          print_r($e);
          */

          $data['error'] = $e -> getMessage();

          $mMessage = "******** Encabezado ******** \n";
          $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
          $mMessage .= "Proceso: " . $data['nom_proces'] . " \n";
          $mMessage .= "******** Detalle ******** \n";
          $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
          $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";

          //echo $mMessage;
          mail("engmiguelgarcia@gmail.com", "Error WebService CLOUD", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/





      
      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otra Alerta</a></b>";

      $mensaje =  "Se Inserto la Alerta <b>".$_REQUEST[nom_ala]."</b> con Exito".$link_a;
      
      if( $data['error'] )
        $mensaje .= "<br><b>Error en ".$data['error']."</b>";
      $mens = new mensajes();
      $mens -> correcto("INSERTAR ALERTA",$mensaje);
    }
 }//FIN FUNCTION INSERTAR

}//FIN CLASE Proc_alerta
     $proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>

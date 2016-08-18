
<?php
/****************************************************************************
NOMBRE:   MODULO_ACTUALIZAR DE ALARMAS.PHP
FUNCION:  ACTUALIZAR ALARMAS
AUTOR: LEONARDO ROMERO
FECHA CREACION : 24 AGOSTO
****************************************************************************/
class Proc_alerta
{
 var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
    //Metodos
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
  if(!isset($GLOBALS[opcion]))
     $this -> Resultado();
  else
     {
      switch($GLOBALS[opcion])
       {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Actualizar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
 function Resultado()

 {

   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_alarma,a.nom_alarma,a.cant_tiempo,a.cod_colorx
            FROM ".BASE_DATOS.".tab_genera_alarma a
               ORDER BY 3";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  for ($i =0 ; $i < sizeof($matriz); $i++)

  for($i=0;$i<sizeof($matriz);$i++)

        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&alarma=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";


   $formulario = new Formulario ("index.php","post","Listar Alarmas","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Alarma(s)",0,"t2");
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Codigo Alarma",0,"t");

   $formulario -> linea("Nombre",0,"t");

   $formulario -> linea("Tiempo de Alarma",1,"t");

   for($i=0;$i<sizeof($matriz);$i++)

   {
   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	$formulario -> linea($matriz[$i][2],1,"i");

   }//fin for

   }//fin if

   $formulario -> nueva_tabla();

   $formulario -> botoni("volver","javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",1,0);

   $formulario -> oculto("valor",$valor,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCION LISTAR


  function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //Trae los datos de las alarmas
   $query = "SELECT a.nom_alarma, a.cant_tiempo, a.cod_colorx
             FROM tab_genera_alarma a
             WHERE a.cod_alarma = '$GLOBALS[alarma]'";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();


   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/alerta.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Configuracion de Alertas Visuales","ins_alert");
   $formulario -> linea("Datos Basicos de Alertas",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> texto ("Nombre de Alarma:","text","nom_ala",0,10,10,"",$matriz[0][0]);
   $formulario -> texto ("Tiempo de Alarma:","text","tiempo",0,3,3,"",$matriz[0][1]);
   $formulario -> texto ("Color:","text","color\"  onBlur=\"setColor();\"",0,7,7,"",$matriz[0][2]);
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
   $formulario -> oculto("cod_alarma",$GLOBALS[alarma],0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],1);
   $formulario -> botoni("Actualizar","aceptar_insert() ",0);
   $formulario -> botoni("Borrar","ins_alert.reset()",1);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA


 function Actualizar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //query de insercion
   $query = "UPDATE ".BASE_DATOS.".tab_genera_alarma
             SET nom_alarma = '$GLOBALS[nom_ala]',
                 cod_colorx = '$GLOBALS[color]',
                 cant_tiempo= '$GLOBALS[tiempo]',
                 usr_modifi= '".$usuario."',
                 fec_modifi= NOW()
             WHERE cod_alarma = '$GLOBALS[cod_alarma]' ";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
       //Se actualiza la alarma en los standas de sat basico y sat trafico
      $data['nom_proces'] = "Actualizar Alarma";
      $mParams = array( "cod_alarma" => $GLOBALS[cod_alarma],
                        "nom_alarma" => $GLOBALS[nom_ala],
                        "cod_colorx" => $GLOBALS[color],
                        "can_tiempo" => $GLOBALS[tiempo],
                        "nom_llavex" => 'f74ca8ee40d8e9c9b2cd529ce297a9a8'
                        );


      /*
      $oSoapClient = new soapclient( 'https://ap.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "updateAlarma", $mParams );
      
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
          //Ruta Web Service en AP.
          $url_webser = URL_INTERF_SATAPX;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'updateAlarma', $mParams );

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
        mail("soporte.ingenieros@intrared.net", "Error WebService AP", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/




      /*
      $oSoapClient = new soapclient( 'https://flired.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "updateAlarma", $mParams );
      
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
          $mResult = $oSoapClient -> __call( 'updateAlarma', $mParams );

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
        mail("soporte.ingenieros@intrared.net", "Error WebService FLIRED", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/




      /*
      $oSoapClient = new soapclient( 'https://server.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "updateAlarma", $mParams );
      
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
          $mResult = $oSoapClient -> __call( 'updateAlarma', $mParams );

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
        mail("soporte.ingenieros@intrared.net", "Error WebService SERVER", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/




      /********************* TRATAMIENTO SOAP *********************/
      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
      try
      {
          //Ruta Web Service en CLOUD.
          $url_webser = URL_INTERF_SATCLO;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'updateAlarma', $mParams );

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
        mail("soporte.ingenieros@intrared.net", "Error WebService CLOUD", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      /********************* **************** *********************/


      
      
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otra Alerta</a></b>";

     $mensaje =  "Se Actualizo la Alerta <b>".$GLOBALS[nom_ala]."</b> con Exito".$link_a;
     
     if( $data['error'] )
       $mensaje .= "<br><b>Error en ".$data['error']."</b>";
     $mens = new mensajes();
     $mens -> correcto("ACTUALIZAR ALERTA",$mensaje);
    }
 }//FIN FUNCTION INSERTAR



}//FIN CLASE Proc_alerta
     $proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>


<?php
/****************************************************************************
NOMBRE:   MODULO_ELIMINAR DE ALARMAS.PHP
FUNCION:  ELIMINAR ALARMAS
AUTOR: LEONARDO ROMERO
FECHA CREACION : 25 AGOSTO 2005
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
          $this -> Eliminar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_alarma,a.nom_alarma,a.cant_tiempo
            FROM ".BASE_DATOS.".tab_genera_alarma a
               ORDER BY 3";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $query = "SELECT a.ind_estado
  			  FROM ".BD_STANDA.".tab_mensaj_bdsata a
  			 WHERE a.cod_transp = '".NIT_TRANSPOR."' AND
  			 	   a.nom_bdsata = '".BASE_DATOS."' AND
  			 	   a.ind_estado = '1'
  		   ";

  $consec = new Consulta($query, $this -> conexion);
  $menact = $consec -> ret_matriz();

  for ($i =0 ; $i < sizeof($matriz); $i++)

  for($i=0;$i<sizeof($matriz);$i++)
  {
   $okelim = 1;

   if($menact)
   {
    $query = "SELECT a.cod_alarma
    			FROM ".BD_STANDA.".tab_mensaj_alarma a
    		   WHERE a.cod_transp = '".NIT_TRANSPOR."' AND
    		   		 a.nom_bdsata = '".BASE_DATOS."' AND
    		   		 a.cod_alarma = ".$matriz[$i][0]."
    		 ";



    $consec = new Consulta($query, $this -> conexion);
    $alaexi = $consec -> ret_matriz();

    if($alaexi)
     $okelim = 0;
   }

   if($okelim)
   {
    $matriz[$i][3]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&cod_alarma=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\" onClick=\"javascript:
    if(confirm('Desea Eliminar la Alarma'))
    {
     submit();
     return true
    }
    else
    {return false}
        \">Eliminar</a>";
   }
   else
    $matriz[$i][3] = "Imposible Eliminar";
  }



   $formulario = new Formulario ("index.php","post","Eliminar Alarmas","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Alarma(s)",0,"t2");
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Codigo Alarma",0,"t");

   $formulario -> linea("Nombre",0,"t");

   $formulario -> linea("Tiempo de Alarma",0,"t");

    $formulario -> linea("",1,"t");

   for($i=0;$i<sizeof($matriz);$i++)

   {
   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	$formulario -> linea($matriz[$i][2],0,"i");
   	$formulario -> linea($matriz[$i][3],1,"i");
   }//fin for

   }//fin if

   $formulario -> nueva_tabla();

   $formulario -> botoni("volver","javascript:history.go(-2)",0);

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",1,0);

   $formulario -> oculto("valor",$valor,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCION LISTAR

 function Eliminar()
 {
   $query = "DELETE FROM ".BD_STANDA.".tab_mensaj_alarma
                   WHERE cod_transp = '".NIT_TRANSPOR."' AND
                         nom_bdsata = '".BASE_DATOS."' AND
                         cod_alarma = ".$GLOBALS[cod_alarma]."";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   $query = "DELETE FROM ".BASE_DATOS.".tab_genera_alarma
             WHERE cod_alarma = '$GLOBALS[cod_alarma]' ";

   $consulta = new Consulta($query, $this -> conexion,"R");

   if($consulta = new Consulta("COMMIT", $this -> conexion))
   {
     //Se inserta la alarma en los standas de sat basico y sat trafico
      $data['nom_proces'] = "Eliminar Alarma";
      $mParams = array( "cod_alarma" => $GLOBALS[cod_alarma],
                        "nom_llavex" => 'f74ca8ee40d8e9c9b2cd529ce297a9a8'
                        );

      /*
      $oSoapClient = new soapclient( 'https://ap.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "deleteAlarma", $mParams );
      
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
          $mResult = $oSoapClient -> __call( 'deleteAlarma', $mParams );

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
      
      $mResult = $oSoapClient -> call( "deleteAlarma", $mParams );
      
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
          $mResult = $oSoapClient -> __call( 'deleteAlarma', $mParams );

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
      $oSoapClient = new soapclient( 'https://server.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl', true );
      $oSoapClient -> soap_defencoding = 'ISO-8859-1';
      
      $mResult = $oSoapClient -> call( "deleteAlarma", $mParams );
      
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
          $mResult = $oSoapClient -> __call( 'deleteAlarma', $mParams );

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




      /********************* TRATAMIENTO SOAP *********************/
      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
      try
      {
          //Ruta Web Service en CLOUD.
          $url_webser = URL_INTERF_SATCLO;
          $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

          //Métodos disponibles en el WS
          $mResult = $oSoapClient -> __call( 'deleteAlarma', $mParams );

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



     
      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Eliminar Otra Alerta</a></b>";

     $mensaje =  "Transaccion Exitosa La Alarma $GLOBALS[nom_ala] Fue Eliminada".$link_a;
     if( $data['error'] )
      $mensaje .= "<br><b>Error en ".$data['error']."</b>";
     
     $mens = new mensajes();
     $mens -> correcto("ELIMINAR ALARMA", $mensaje);
   }
 }//FIN FUNCTION INSERTAR

}//FIN CLASE Proc_alerta
     $proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>

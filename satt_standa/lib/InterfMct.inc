<?php

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);


/*! \Class: InterfMct
*  \brief: Clase encargada de hacer interfaz con astrans
*  \author: Ing. Nelson Liberato
*  \date: 09/03/2015   
*  \param: $cConection  -  Variable de clase que almacena la conexion de la Base de datos
*  \comment: Se incluyen dos librerias que se usan en los webservices para generar los logs en archivo plano
*  \         Los logs se crean por fecha ano-mes-dia (Fichero = x dia), se crea el log cuando ay una excepcion del try-catch
*  \comment: Esta clase usa la libreria de CURL ya que se debe consumir una API REST (Json), Usa Headers
*  \comment: cHeader, almacena los datos que se necesitan para enviar en el header con el cUrl
*  \comment: cData  , almacena los datos de autenticacion usuario y clave, de debe hacer un merge con los datos del manifiesto que entra a la clase
*  \return array
*/

class InterfMct
{
  private static $cConection = NULL;
  #private static $cUrlMCT = "http://186.117.158.13/mctAPI/v1/ptoc";
  private static $cUrlMCT = "http://200.122.249.34:8088/mctAPI/v1/ptoc";
  private static $cExcept = NULL;
  private static $cNotMail = "soporte@intrared.net";
  private static $cLogDir  = "/var/www/html/ap/satt_faro/logs";
  private static $cHeader = array("Token: TkOET_EAL","Auth: $2dIMJMZQcHLY");
  private static $cData = array(  'user' => 'esferas.oet', 'password' => '.*oEt-20_15*_' );
  private static $cReturn = NULL;

  /*! \fn: __construct
  *  \brief: Metodo principal para la Clase - Constructor
  *  \author: Ing. Nelson Liberato
  *  \date: 16/07/2015   
  *  \param: fConection  : Conexion de base de datos 
  *  \param: mParams     : Array con los datos a enviar 
  *  \return n/a
  */
  function __construct( $fConection = NULL, $fParams = NULL, $mAditional = NULL )
  {
    #include_once("/var/www/html/ap/interf/lib/funtions/General.fnc.php"); #incluye libreria de funciones generales 
    #include_once("/var/www/html/ap/interf/lib/Error.class.php"); # Incluye clase para generar los archivos Logs
    self::$cConection = $fConection;
    #self::$cExcept = new Error( array( "dirlog" => self::$cLogDir, "notlog" => FALSE, "logmai" =>self::$cNotMail ) );
    #self::$cExcept -> SetUser( $_SESSION["datos_usuario"]["cod_usuari"] );
    #self::$cExcept -> SetParams( "faro", "Novedades_PC_MCT" );

    switch ($mAditional["tip_noveda"]) {
      case 'PC': self::setNovedadPC( $fParams , $mAditional ); break;
      case 'NC': self::setNovedadNC( $fParams , $mAditional ); break;      
      default:  return true; break;
    }
    
     
  }

  /*! \fn: setNovedadPC
  *  \brief: Metodo para enviar la novedad de puesto de control a MCT
  *  \author: Ing. Nelson Liberato
  *  \date: 16/07/2015   
  *  \param: mParams  : Array con datos que se necesitan para ser enviados a MCT
  *  \param: mAditional  : Array con datos que se necesitan para ser enviados a MCT, se usa en la interfaz /ap/interf/app/faro/faro.php
  *  \return n/a
  */
  function setNovedadPC($mParams = NULL, $mAditional = NULL)
  {
    try
    {   # Datos del despacho + autenticacion ---------------------------------------------------------------------
        self::$cData = array_merge(self::$cData, $mParams); 

        # Recorre las variables para concatenarlas en un solo string como si fuera un GET ------------------------
        foreach(self::$cData as $key=>$value) { 
          $mParamsString .= $key.'='.$value.'&'; 
        }

        # Quita el ultimo ampersan, de la cadena de datos
        rtrim($mParamsString, '&');

        # Inicio de cURL para la API -----------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://200.122.249.34:8088/mctAPI/v1/ptoc");
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::$cHeader);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, count(self::$cData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $raw_data = curl_exec($ch);
        $error = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        # Asigna el mensaje del response y lo retorna ------------------------------------------------------------
        self::$cReturn = json_decode($raw_data);

        # Notifica Correo cada vez que se hace uso de la clase MCT
        //mail("nelson.liberato@intrared.net, ricardo.gomez@mct.com.co", "Uso MCT FARO PC InterfMct.inc ", "Parametros enviados Data: ".var_export(self::$cData, true)." \n Response API: ".$raw_data );

        # Verificacion de reporte a email
        # echo "<pre>"; print_r(self::$cReturn); echo "</pre>";
        if( self::$cReturn -> error == true ) {
          self::sendNotify( self::$cData , $mAditional);
        }

           
    }
    catch(SoapFault $e) { 
      self::$cReturn = new stdObject();
      self::$cReturn -> error = false;
      self::$cReturn -> message =  "Codigo: ".$e -> faultcode."; Error:".$e -> faultstring;
      #self::$cExcept -> CatchError( $e -> faultstring, $e -> faultcode, $e -> getFile(), $e -> getLine(), "satt_faro", $mParams["NumeroViaje"] );
    }
    return self::$cReturn;
  }


  /*! \fn: setNovedadNC
  *  \brief: Metodo para enviar la novedad de las notas de controlador -NC
  *  \author: Ing. Nelson Liberato
  *  \date: 06/10/2015   
  *  \param: mParams     : Array con datos que se necesitan para ser enviados a MCT
  *  \param: mAditional  : Array con datos que se necesitan para ser enviados a MCT, se usa en la interfaz /ap/interf/app/faro/faro.php
  *  \return n/a
  */
  function setNovedadNC($mParams = NULL, $mAditional = NULL)
  {
    try
    {   # Datos del despacho + autenticacion ---------------------------------------------------------------------
        self::$cData = array_merge(self::$cData, $mParams); 
        # Recorre las variables para concatenarlas en un solo string como si fuera un GET ------------------------
        foreach(self::$cData as $key=>$value) { 
          $mParamsString .= $key.'='.$value.'&'; 
        }

        # Quita el ultimo ampersan, de la cadena de datos
        rtrim($mParamsString, '&');

        # Inicio de cURL para la API -----------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://200.122.249.34:8088/mctAPI/v1/nov"); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::$cHeader);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, count(self::$cData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $raw_data = curl_exec($ch);
        $error = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        # Asigna el mensaje del response y lo retorna ------------------------------------------------------------
        self::$cReturn = json_decode($raw_data);

        # Notifica Correo cada vez que se hace uso de la clase MCT
        //mail("nelson.liberato@intrared.net, ricardo.gomez@mct.com.co", "Uso MCT FARO NC InterfMct.inc ", "Parametros enviados Data: ".var_export(self::$cData, true)." \n Response API: ".$raw_data );

        # Verificacion de reporte a email
        # echo "<pre>"; print_r(self::$cReturn); echo "</pre>";
        if( self::$cReturn -> error == true ) {
          self::sendNotify( self::$cData , $mAditional);
        }           
    }
    catch(SoapFault $e) { 
      self::$cReturn = new stdObject();
      self::$cReturn -> error = false;
      self::$cReturn -> message =  "Codigo: ".$e -> faultcode."; Error:".$e -> faultstring;
      #self::$cExcept -> CatchError( $e -> faultstring, $e -> faultcode, $e -> getFile(), $e -> getLine(), "satt_faro", $mParams["NumeroViaje"] );
    }
    return self::$cReturn;
  }

  /*! \fn: getResponMct
  *  \brief: Metodo retornar los mensajes cuando se usa un metodo
  *  \author: Ing. Nelson Liberato
  *  \date: 16/07/2015   
  *  \param: mParams  : object con datos de la respuesta de MCT
  *  \return array
  */
  function getResponMct()
  {
    return (array)self::$cReturn;
  }

  /*! \fn: sendNotify
  *  \brief: Metodo retornar los mensajes cuando se usa un metodo
  *  \author: Ing. Nelson Liberato
  *  \date: 16/07/2015   
  *  \param: mParams  : object con datos de la respuesta de MCT
  *  \return array
  */
  function sendNotify( $mDataMct, $mAditional = NULL )
  {
     
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Fecha y hora de la novedad: " . date("Y-m-d H:i", strtotime($mDataMct["ptoc_fecha"])) . " \n";
      $mMessage .= "Empresa de transporte: 830004861 \n";
      $mMessage .= "Numero de manifiesto: " . $mDataMct["manifiesto_codigo"] . " \n";
      $mMessage .= "Placa del vehiculo: " . $mAditional["num_placax"] . " \n";
      $mMessage .= "Codigo puesto de control: " . $mDataMct["ptoc_codigo"] . " \n";
      $mMessage .= "Nombre puesto de control: " . $mDataMct["ptoc_nombre"] . " \n";
      $mMessage .= "Observacion.: " . $mDataMct["ptoc_observacion"] . " \n";
      $mMessage .= "******** Detalle ******** \n";
      $mMessage .= "Cod Error: ".self::$cReturn -> error." \n";
      $mMessage .= "Error: " . self::$cReturn -> message. " \n";
      

      //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
      mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", 
           "Web service MCT Error Catch From WEBSERVICE DE EAL (ESFERAS)", 
           $mMessage, 
           'From: soporte.ingenieros@intrared.net');
                                
  }

  

} 
?>

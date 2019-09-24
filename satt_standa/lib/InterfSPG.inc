<?php

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);


/*! \Class: InterfSPG
*  \brief: Clase encargada de conectar con el WS de SPG para crear tareas
*  \author: Ing. Nelson Liberato
*  \date: 28/01/2016   
*  \param: $cConection  -  Variable de clase que almacena la conexion de la Base de datos
*  \comment: cUrlSPG  , direccion http del ws de SPG
*  \comment: cExcept  , Varible instanciada para manejo de logs de las excepciones
*  \comment: cNotMail , mail al cual se notifica una excepcion
*  \comment: cLogDir  , directorio de almacenamiento de los LOGs
*  \comment: cData    , datos para conexion al ws de SPG
*  \return array
*/

class InterfSPG
{
  private static $cConection = NULL;
  private static $cUrlSPG = "https://interno.intrared.net:8083/ap/interf/app/spg/wsdl/spg2.wsdl";
  private static $cExcept = NULL;
  private static $cNotMail = "soporte@intrared.net";
  private static $cLogDir  = "/var/www/html/ap/satt_faro/logs"; 
  private static $cData = array(  'USR_LOGSPG' => 'usr_seguim_empres', 'PWD_LOGSPG' => '7f403c30fc28639297d4d7d63d3397ec' );
  private static $cUserSat = NULL;
  private static $cRutScrip = NULL;
  public  static $cReturn = NULL;
 

  /*! \fn: __construct
  *  \brief: Metodo principal para la Clase - Constructor
  *  \author: Ing. Nelson Liberato
  *  \date: 28/01/2016
  *  \param: fConection  : Conexion de base de datos 
  *  \param: mParams     : Array con los datos a enviar 
  *  \param: mAditional  : datos distintos a al creacion del ID del SPG 
  *  \return n/a
  */
  function __construct( $fConection = NULL, $fParams = NULL, $mAditional = NULL )
  {
    
    self::$cConection = $fConection;  
    self::$cUserSat   = $_SESSION["datos_usuario"];   
    self::setSoporte( $fParams , $mAditional );  
     
     
  }

  /*! \fn: setSoporte
  *  \brief: Metodo para almacenar un string en una variable de clase
  *  \author: Ing. Nelson Liberato
  *  \date: 28/01/2016   
  *  \param: mRuta  : string cadena de texto a almacenar
  *  \return n/a
  */
  private function setRutaScript( $mRuta )
  {
    self::$cRutScrip = $mRuta;
  }

  /*! \fn: getRutaScript
  *  \brief: Metodo para retornar el valor de una variable de clase
  *  \author: Ing. Nelson Liberato
  *  \date: 28/01/2016   
  *  \return string
  */
  private function getRutaScript()
  {
    return self::$cRutScrip;
  }

  /*! \fn: setSoporte
  *  \brief: Metodo para enviar la solicutd de Soporte
  *  \author: Ing. Nelson Liberato
  *  \date: 28/01/2016   
  *  \param: mParams  : Array con datos que se necesitan para ser enviados a MCT
  *  \param: mAditional  : Array con datos que se necesitan para ser enviados a MCT, se usa en la interfaz /ap/interf/app/faro/faro.php
  *  \return n/a
  */
  function setSoporte($mParams = NULL, $mAditional = NULL)
  {
    try
    {         
        $sql = NULL;
        $sql = 'SELECT
                  cod_servic, nom_servic , rut_archiv 
                FROM
                  '.BD_STANDA.'.tab_genera_servic
                WHERE 
                  cod_servic = "'.$mParams['cod_servicx'].'" ';

        $consul = new Consulta( $sql, self::$cConection );
        $mServic = $consul -> ret_matrix( 'a' );
        
        $mDataScript = self::getRutaServic( $mParams['cod_servicx'] );
    
        
        $sendData = NULL;
        $sendData["nom_usulog"] = self::$cData["USR_LOGSPG"];
        $sendData["pwd_usulog"] = self::$cData["PWD_LOGSPG"];
        $sendData["nom_ususat"] = self::$cUserSat["cod_usuari"];
        $sendData["nom_usuari"] = self::$cUserSat["nom_usuari"];
        $sendData["ema_usuari"] = self::$cUserSat["usr_emailx"];  
        $sendData["nom_browse"] = $mParams["nom_navega"];
        $sendData["ver_browse"] = $_SERVER["HTTP_USER_AGENT"];
        $sendData["nom_servic"] = $mServic[0]["nom_servic"];
        $sendData["cod_servic"] = $mServic[0]["cod_servic"];
        $sendData["rut_servic"] = self::getRutaScript();
        $sendData["rut_script"] = $mServic[0]["rut_archiv"];
        $sendData["tip_errorx"] = $mParams["cod_errorx"];
        $sendData["fil_attach"] = $mAditional["doc_adjunt"]["name"];
        $sendData["tex_messag"] = $mParams["obs_mensaj"];
        $sendData["nit_transp"] = $mParams["nit_transp"];
        $sendData["nom_aplica"] = $mParams["nom_aplica"];
        $sendData["nom_server"] = $mParams["server"];
        $sendData["nom_transp"] = $mParams["nom_empres"];
        $sendData["asu_messag"] = $mParams["obs_asunto"];
       
 
      
        if( sizeof($mAditional["doc_adjunt"]) > 0 )
        {
          $sendData["tip_attach"] = $mAditional["doc_adjunt"]["type"];
          $sendData["siz_attach"] = $mAditional["doc_adjunt"]["size"];
          $sendData["bin_attach"] = base64_encode(@file_get_contents( $mAditional["doc_adjunt"]["tmp_name"] ) ) ;  
        }
        else 
        {
          $sendData["tip_attach"] = NULL ;
          $sendData["siz_attach"] = NULL ;
          $sendData["bin_attach"] = NULL ;        
        }
        
        $sendData["tel_usuari"] = $mParams['tel_usuari'] ; 
        $sendData["cel_usuari"] = $mParams['cel_usuari'] ;


        
       
        # Inicia instancia del WS de SPG
        $oSoapClient = new soapClient( self::$cUrlSPG, array("encoding"=> "ISO-8859-1") );  
        # Consume metodo de ws de SPG  
        $mResult = $oSoapClient -> __call( "setSoport", $sendData );
        #$mResult = "code_resp:1000; msg_resp:196948";
        
        $mResponse = explode(";", $mResult);
        $mCode = explode(":", $mResponse[0]);
        $mMesa = explode(":", $mResponse[1]);
       
        self::$cReturn = array("cod_respon"=>$mCode[1], "msg_respon"=>$mMesa[1]);
       
                
    }
    catch(SoapFault $e) { 
      
       
      self::$cReturn =  array( "cod_respon" => $e -> faultcode, "msg_respon" => $e -> getMessage() );
      
    } 
    return self::$cReturn;
  }


  /*! \fn: getRutaServic
  *  \brief: Metodo buscar la ruta del servicio
  *  \author: Ing. Nelson Liberato
  *  \date: 29/01/2016   
  *  \param: mParams  : object con datos de la respuesta de MCT
  *  \return array
  */
  function getRutaServic( $cod_servic = NULL , $nivel = 0 , $rutaScript = NULL )
  {
    
    //------------------------------------------------------------
    // Sacando el nombre del servicio que se envia como parametro
    //------------------------------------------------------------
    $sql = NULL;
    $sql = 'SELECT
              nom_servic
            FROM
              '.BD_STANDA.'.tab_genera_servic
            WHERE 
              cod_servic = "'.$cod_servic.'" '; 
    $consul = new Consulta( $sql, self::$cConection );                     
    $nom_servic = $consul -> ret_matrix( 'a' );
    $nom_servic = $nom_servic[0]['nom_servic'];
    //------------------------------------------------------------
    
    if( $nivel == 1 )
    {
      $ruta = $nom_servic.' > '.$rutaScript;        
    }
    else
    {
      $ruta = $nom_servic ;
    }
    
    
    $sql = NULL;
    $sql = 'SELECT 
              * 
            FROM
              '.BD_STANDA.'.tab_servic_servic
            WHERE 
              cod_serhij = "'.$cod_servic.'"';

    $consul = new Consulta( $sql, self::$cConection );
    $padre = $consul -> ret_num_rows();
    
    if( $padre )
    {
      $consul = new Consulta( $sql, self::$cConection );
      $cod_servic = $consul -> ret_matrix( 'a' );
      $cod_servic = $cod_servic[0]['cod_serpad'];
      self::getRutaServic( $cod_servic , $nivel = 1 , $ruta );
    }
    else 
    {
      self::setRutaScript($ruta);  
    }
              
  }//FIN FUNCION getRutaServic    


   

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
      mail("faroavansat@eltransporte.com, soporte.ingenieros@intrared.net", 
           "Web service MCT Error Catch From WEBSERVICE DE EAL (ESFERAS)", 
           $mMessage, 
           'From: soporte.ingenieros@intrared.net');
                                
  }

  

} 
?>

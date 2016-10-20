<?php

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);


/*! \Class: InterfSimplexity
*  \brief: Clase encargada de hacer interfaz con astrans
*  \author: Ing. Nelson Liberato
*  \date: 09/03/2015   
*  \param: $cConection  -  Variable de clase que almacena la conexion de la Base de datos
*  \comment: Se incluyen dos librerias que se usan en los webservices para generar los logs en archivo plano
*  \         Los logs se crean por fecha ano-mes-dia (Fichero = x dia), se crea el log cuando ay una excepcion del try-catch
*  \return array
*/

class InterfSimplexity
{
  private static $cConection = NULL;
  private static $cUrlAstrans =    "https://astrans.coronaindustrial.net:8065/ServicioViajes.svc?wsdl";
  #private static $cUrlAstrans =    "https://200.32.81.202:8065/ServicioViajes.svc?wsdl";
  private static $cUrlAstransSVC = "https://astrans.coronaindustrial.net:8065/ServicioViajes.svc";
  private static $cUserApl = NULL;
  private static $cNotMail = "soporte@intrared.net";
  private static $cLogDir  = "/var/www/html/ap/satt_faro/logs";
  private static $cPatron    = array("(\¬)", "(\.)", "(\,)", "(\ )", "(ñ)", "(Ñ)", "(\°)", "(\º)", "(&)", "(Â)", "(\()", "(\))", "(\/)", "(\´)", "(\¤)", "(\Ã)", "(\‘)", "(\ƒ)", "(\â)", "(\€)", "(\˜)", "(\¥)", "(Ò)", "(Í)", "(\É)", "(\Ãƒâ€šÃ‚Â)", "(\·)", "(\ª)", "(\-)", "(\+)", "(\Ó)", "(\ü)", "(\Ü)", "(\é)", "(\;)", "(\¡)", "(\!)", "(\`)", "(\<)", "(\>)", "(\_)", "(\#)", "(\ö)", "(\À)", "(\¿)", "(\Ã±)", "(\±)", "(\*)", "(Ú)", "(\%)", "(\|)", "(\ò)", "(\Ì)", "(\:)", "(\Á)", "(\×)", "(\@)", "(\ )", "(\Ù)", "(\á)", "(\–)", "(\")", "(\È)", "(\])", "(\')", "(\í)", "(\Ç)","(\Nš)","(\‚)", "(\ó)", "(\ )", "(\ )", "(\ï½)", "(\?)", "(\ï¿½)" );
  private static $cReemplazo = array(" ", " ", " ", " ", "n", "N", " ", " ", "Y", "", "", "", "", "", "", "", "", "", "", "", "", "", "O", "I", "E", " ", "", "a", " ", " ", "O","U","U", "e", " ", "", "", "", "", "", "", "", "", "A", "", "", "", "", "", "", "", "", "I", "", "A", "", "", " ", "U", "a", " ", "", "E", " ", " ", "i", "", "N"," ", " ", " ", " ", " " , "", "", "N"  );  
  
  private static $cPatronRemi    = array("(\.)","(\:)", "(\ )", "(\,)", "/\s+/"  );
  private static $cReemplazoRemi = array(""    , ""   , ""    , ""    , ""        );

 

  /*! \fn: envioIndividual
  *  \brief: Metodo principal para la Clase - Constructor
  *  \author: Ing. Nelson Liberato
  *  \date: 09/03/2015   
  *  \param: fConection  : Conexion de base de datos 
  *  \return n/a
  */
  function __construct( $fConection = NULL)
  {
    self::$cConection = $fConection;    
    self::$cUserApl = $_SESSION["datos_usuario"]["cod_usuari"];  
     
  }

    /*! \fn: isDomainAvailible
    *  \brief:  Valida si la URL se encuentra online, ya que los Hptas de RNDC estan caidos a cada rato
    *  \author: Ing. Nelson Liberato
    *  \param: mUrlRndc : 
    *  \date: 04/07/2015    
    *  \return Bool (True/false)
    */
    function isDomainAvailible($mUrlRndc)
    {
        try
        {
            # Valida si la URL es correcta sintacticamente ----------------------------------------------------
            if(!filter_var($mUrlRndc, FILTER_VALIDATE_URL)) {
               return false;
            }

            //initialize curl
            $mCurlIni = curl_init($mUrlRndc);
            curl_setopt($mCurlIni,CURLOPT_CONNECTTIMEOUT,4); # Espera 4 segundos conectandose
            curl_setopt($mCurlIni,CURLOPT_HEADER,false);
            curl_setopt($mCurlIni,CURLOPT_NOBODY,true);
            curl_setopt($mCurlIni,CURLOPT_RETURNTRANSFER,true);

            # Ejecuta CURL ------------------------------------------------------------------------------------
            $mResponse = curl_exec($mCurlIni);

            curl_close($mCurlIni);

            if ($mResponse) return true;

            return false;
        }
        catch( Exception $e)
        {
            return false;
        }
       
    }

  /*! \fn: setRegistrarCargue
  *  \brief: Metodo para registrar el cumplido de la cita de cargue en astrans
  *  \author: Ing. Nelson Liberato
  *  \date: 09/03/2015   
  *  \param: mParams  : Datos que se necesitan para ser enviados
  *  \return n/a
  */
  function setRegistrarCargue($mParams = NULL)
  {
    try
    { 
        #if( !self::isDomainAvailible( self::$cUrlAstrans ) ) {            
        #    return array("cod_respon" => "9000", "msg_respon" => "La URL: ".self::$cUrlAstrans.", no se encuentra disponible ");
        #}  


        /****************************************************************************************************************************************************
        # Se inactiva el consumo por SoapClient ya que se debe buscar la manera de que no valide la SSL como se hace con cURL 
        # Inicia SoapClient Nativo de PHP -----------------------------------------------------------------------------------------------------
        
        $oSoapClient = new SoapClient(self::$cUrlAstrans,array( 'cache_wsdl'=>WSDL_CACHE_NONE, 'trace'   => 1,   'encoding' => 'UTF-8',  'soap_version' => SOAP_1_2 , 'exceptions' => 1 ) );
        # Coloca Los Header del Webservice ----------------------------------------------------------------------------------------------------
        $actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action','http://tempuri.org/IServicioViajes/RegistrarCargue');    
        $actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'To'    ,'https://astrans.coronaindustrial.net:8065/ServicioViajes.svc');
        $oSoapClient->__setSoapHeaders($actionHeader);
        # -------------------------------------------------------------------------------------------------------------------------------------
        # Coloca los datos en el BODY ---------------------------------------------------------------------------------------------------------
        $mParam   = new SoapVar( '<tem:RegistrarCargue xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                         <tem:cargue>
                            <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                            <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                            <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                            <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                            <sim:DescripcionNovedad>'.@preg_replace( self::$cPatron, self::$cReemplazo, $mParams["DescripcionNovedad"] ).'</sim:DescripcionNovedad>
                         </tem:cargue>
                      </tem:RegistrarCargue>',  XSD_ANYXML, SOAPStruct,NULL,NULL);
         $mResult = $oSoapClient -> RegistrarCargue( new soapParam($mParam, "LOL"));

         echo "<pre>Response RegistrarCargue SoapClient:<br>"; print_r($mResult); echo "</pre>";
        

        # Toma las variables que responde el webservice de Simplexity con el SoapClient -
        $mTypeRespon = $mResult -> RegistrarCargueResult -> Type;
        $mMesaRespon = $mResult -> RegistrarCargueResult -> Message;
        /****************************************************************************************************************************************************/
        
        
        # -------------------------------------------------------------
        # Consumo del Webservice de Astrans con cURL de PHP5 Nativo --------------------------------------------------------------------------------------------------------------------------------
        $mMessage = NULL;
        $mNumberTransa = NULL;
        $mType = NULL;
        $mXmlEnvelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/" xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
                        <wsa:Action>http://tempuri.org/IServicioViajes/RegistrarCargue</wsa:Action>
                        <wsa:To>https://astrans.coronaindustrial.net:8065/ServicioViajes.svc</wsa:To>
                       </soap:Header>
                       <soap:Body>
                          <tem:RegistrarCargue>
                             <tem:cargue>
                                <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                                <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                                <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                                <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                                <sim:DescripcionNovedad>'.@preg_replace( self::$cPatron, self::$cReemplazo, $mParams["DescripcionNovedad"] ).'</sim:DescripcionNovedad>
                             </tem:cargue>
                          </tem:RegistrarCargue>
                       </soap:Body>
                    </soap:Envelope>';

          $mSoapAnstran = curl_init( self::$cUrlAstrans );
          curl_setopt($mSoapAnstran, CURLOPT_URL,            self::$cUrlAstrans );
          curl_setopt($mSoapAnstran, CURLOPT_USERAGENT, 'php');
          curl_setopt($mSoapAnstran, CURLOPT_CONNECTTIMEOUT, 6);
          curl_setopt($mSoapAnstran, CURLOPT_TIMEOUT,        6);
          curl_setopt($mSoapAnstran, CURLOPT_RETURNTRANSFER, true );
          curl_setopt($mSoapAnstran, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($mSoapAnstran, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($mSoapAnstran, CURLOPT_POST,           true );            
          curl_setopt($mSoapAnstran, CURLOPT_POSTFIELDS,     $mXmlEnvelope); 
          curl_setopt($mSoapAnstran, CURLOPT_VERBOSE, TRUE); 
          curl_setopt($mSoapAnstran, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml","SOAPAction: http://tempuri.org/IServicioViajes/RegistrarCargue", "Content-length: ".strlen($mXmlEnvelope))); 

          $mResultSoap = curl_exec($mSoapAnstran);
          curl_close($mSoapAnstran);
 
          # Convierte el Xml a Array --------------------------------------------------------------------------------------------------------------------------
          $mXml2 = self::XMLtoArray($mResultSoap);
 
         
          # Captura de los valores que retorna Mensage, numero transaccion Tipo
          $mMessage =      $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARCARGUERESPONSE"]["REGISTRARCARGUERESULT"]["B:MESSAGE"];
          $mNumberTransa = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARCARGUERESPONSE"]["REGISTRARCARGUERESULT"]["B:TRANSACTIONNUMBER"]["I:NIL"];
          $mType =         $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARCARGUERESPONSE"]["REGISTRARCARGUERESULT"]["B:TYPE"];

          # Valida el campo del mensage.
          $mMessage = is_array($mMessage) ? $mMessage["I:NIL"] : $mMessage;
  
          # Si el Type es diferente a 1, se debe generar log ---------------------------
          if( $mType !=  1) 
            throw new SoapFault( $mMessage, $mType);
          
          $mReturn = array("cod_respon" => $mType, "msg_respon" =>  $mMessage  );

          /*mail("nelson.liberato@intrared.net, maribel.garcia@eltransporte.org", 
               "Novedad Cargue OET - Corona", 
               "Datos Enviados: ".var_export( $mXmlEnvelope, true )."\nRespuesta: ".var_export($mReturn, true) );*/


        
    }
    catch(SoapFault $e) {
      # Si estan cambiadas las variables, raro no?      
      $mXml = array( "xml_respon"=> $mXmlEnvelope,"xml_reques"=> $mResultSoap ,"xml_arrayx"=> var_export($mXml2, true)  );
      $mReturn = array("cod_respon" => $e -> faultstring,  "msg_respon" => $e -> faultcode ); 
      self::LogDataError( $mParams, $mReturn, "1", $mXml);
    }

    unset($mMessage);
    unset($mNumberTransa);
    unset($mType);

    return $mReturn;
  }

  /*! \fn: setRegistDesReme
  *  \brief: Metodo para registrar el descargue de la remesa en Astrans
  *  \author: Ing. Nelson Liberato
  *  \date: 09/03/2015   
  *  \param: mParams  : Datos que se necesitan para ser enviados 
  *  \return n/a
  */ 
  function setRegistDesReme($mParams = NULL)
  {
    try
    {
        /****************************************************************************************************************************************************
        # Se inactiva el consumo por SoapClient ya que se debe buscar la manera de que no valide la SSL como se hace con cURL 
        # Inicia SoapClient Nativo de PHP -----------------------------------------------------------------------------------------------------
        $oSoapClient = new SoapClient( self::$cUrlAstrans ,array('trace'   => 1,  'encoding' => 'UTF-8', 'soap_version' => SOAP_1_2   ) );     
        # Coloca Los Header del Webservice ----------------------------------------------------------------------------------------------------
        $actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action','http://tempuri.org/IServicioViajes/RegistrarDescargueRemesa');    
        $actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'To'    ,'https://astrans.coronaindustrial.net:8065/ServicioViajes.svc');
        $oSoapClient->__setSoapHeaders($actionHeader);
        # -------------------------------------------------------------------------------------------------------------------------------------
        # Coloca los datos en el BODY ---------------------------------------------------------------------------------------------------------
        $mParam   = new SoapVar( '<ns1:RegistrarDescargueRemesa xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                         <ns1:descargueRemesa>
                            <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                            <sim:NumeroRemesa>'.$mParams["NumeroRemesa"].'</sim:NumeroRemesa>
                            <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                            <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                            <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                            <sim:DescripcionNovedad>'.@preg_replace( self::$cPatron, self::$cReemplazo, $mParams["DescripcionNovedad"] ).'</sim:DescripcionNovedad>
                         </ns1:descargueRemesa>
                      </ns1:RegistrarDescargueRemesa>',  XSD_ANYXML, SOAPStruct,NULL,NULL);
         $mResult = $oSoapClient -> RegistrarDescargueRemesa( new soapParam($mParam, "LOL"));
         #echo "<pre>Response RegistrarDescargueRemesa:<br>"; print_r($mResult); echo "</pre>";
        # Toma las variables que responde el webservice de Simplexity -
        $mTypeRespon = $mResult -> RegistrarDescargueRemesaResult -> Type;
        $mMesaRespon = $mResult -> RegistrarDescargueRemesaResult -> Message;
        /****************************************************************************************************************************************************/



        # -------------------------------------------------------------
        # Consumo del Webservice de Astrans con cURL de PHP5 Nativo --------------------------------------------------------------------------------------------------------------------------------
        $mMessage = NULL;
        $mNumberTransa = NULL;
        $mType = NULL;
        $mXmlEnvelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/" xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                           <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
                              <wsa:Action>http://tempuri.org/IServicioViajes/RegistrarDescargueRemesa</wsa:Action>
                              <wsa:To>https://astrans.coronaindustrial.net:8065/ServicioViajes.svc</wsa:To>
                           </soap:Header>
                           <soap:Body>
                              <tem:RegistrarDescargueRemesa>
                                 <tem:descargueRemesa>
                                    <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                                    <sim:NumeroRemesa>'.$mParams["NumeroRemesa"].'</sim:NumeroRemesa>
                                    <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                                    <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                                    <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                                    <sim:DescripcionNovedad>'.@preg_replace( self::$cPatron, self::$cReemplazo, $mParams["DescripcionNovedad"] ).'</sim:DescripcionNovedad>
                                 </tem:descargueRemesa>
                              </tem:RegistrarDescargueRemesa>
                           </soap:Body>
                        </soap:Envelope>';

          $mSoapAnstran = curl_init( self::$cUrlAstrans );
          curl_setopt($mSoapAnstran, CURLOPT_URL,            self::$cUrlAstrans );
          curl_setopt($mSoapAnstran, CURLOPT_USERAGENT, 'php');
          curl_setopt($mSoapAnstran, CURLOPT_CONNECTTIMEOUT, 6);
          curl_setopt($mSoapAnstran, CURLOPT_TIMEOUT,        6);
          curl_setopt($mSoapAnstran, CURLOPT_RETURNTRANSFER, true );
          curl_setopt($mSoapAnstran, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($mSoapAnstran, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($mSoapAnstran, CURLOPT_POST,           true );            
          curl_setopt($mSoapAnstran, CURLOPT_POSTFIELDS,     $mXmlEnvelope); 
          curl_setopt($mSoapAnstran, CURLOPT_VERBOSE, TRUE); 
          curl_setopt($mSoapAnstran, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml","SOAPAction: http://tempuri.org/IServicioViajes/RegistrarDescargueRemesa", "Content-length: ".strlen($mXmlEnvelope))); 

          $mResultSoap = curl_exec($mSoapAnstran);
          curl_close($mSoapAnstran);

          # Convierte el Xml a Array --------------------------------------------------------------------------------------------------------------------------
          $mXml2 = self::XMLtoArray($mResultSoap);

          # Captura de los valores que retorna Mensage, numero transaccion Tipo
          $mMessage = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARDESCARGUEREMESARESPONSE"]["REGISTRARDESCARGUEREMESARESULT"]["B:MESSAGE"];
          $mNumberTransa = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARDESCARGUEREMESARESPONSE"]["REGISTRARDESCARGUEREMESARESULT"]["B:TRANSACTIONNUMBER"]["I:NIL"];
          $mType = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARDESCARGUEREMESARESPONSE"]["REGISTRARDESCARGUEREMESARESULT"]["B:TYPE"];

          # Valida el campo del mensage.
          $mMessage = is_array($mMessage) ? $mMessage["I:NIL"] : $mMessage;
  
          # Si el Type es diferente a 1, se debe generar log ---------------------------
          if( $mType !=  1) 
            throw new SoapFault( $mMessage, $mType);

          $mReturn = array("cod_respon" => $mType, "msg_respon" =>  $mMessage  );

          /*mail("nelson.liberato@intrared.net, maribel.garcia@eltransporte.org", 
               "Novedad Remesa OET - Corona", 
               "Datos Enviados: ".var_export( $mXmlEnvelope, true )."\nRespuesta: ".var_export($mReturn, true) );*/
        
    }
    catch(SoapFault $e) {
      # Si estan cambiadas las variables, raro no?
      $mXml = array( "xml_respon"=> $mXmlEnvelope,"xml_reques"=> $mResultSoap ,"xml_arrayx"=> var_export($mXml2, true)  );
      $mReturn = array("cod_respon" => $e -> faultstring,  "msg_respon" => $e -> faultcode ); 
      self::LogDataError( $mParams, $mReturn, "2", $mXml);
    }

    unset($mMessage);
    unset($mNumberTransa);
    unset($mType);

    return $mReturn;    
  }
 
  /*! \fn: setRegistDesRemi
  *  \brief: Metodo para registrar el descargue de la remesa en Astrans
  *  \author: Ing. Nelson Liberato
  *  \date: 09/03/2015   
  *  \param: mParams  : Datos que se necesitan para ser enviados 
  *  \return n/a
  */
  function setRegistDesRemi($mParams = NULL)
  {
    try
    {
        /****************************************************************************************************************************************************
        # Se inactiva el consumo por SoapClient ya que se debe buscar la manera de que no valide la SSL como se hace con cURL 
        # Inicia SoapClient Nativo de PHP -----------------------------------------------------------------------------------------------------
        $oSoapClient = new SoapClient( self::$cUrlAstrans ,array('trace' => 1, 'encoding' => 'UTF-8', 'soap_version' => SOAP_1_2   ) );     
        # Coloca Los Header del Webservice ----------------------------------------------------------------------------------------------------
        $actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action','http://tempuri.org/IServicioViajes/RegistrarDescargueRemision');    
        $actionHeader[] = new SoapHeader('http://www.w3.org/2005/08/addressing', 'To'    ,'https://astrans.coronaindustrial.net:8065/ServicioViajes.svc');
        $oSoapClient->__setSoapHeaders($actionHeader);
        # -------------------------------------------------------------------------------------------------------------------------------------
        # Coloca los datos en el BODY ---------------------------------------------------------------------------------------------------------
        $mParam   = new SoapVar( '<ns1:RegistrarDescargueRemision xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                         <ns1:descargueRemision>
                            <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                            <sim:NumeroRemision>'.@preg_replace( self::$cPatronRemi, self::$cReemplazoRemi, $mParams["NumeroRemision"] ).'</sim:NumeroRemision>
                            <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                            <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                            <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                            <sim:DescripcionNovedad>'.@preg_replace( self::$cPatron, self::$cReemplazo, $mParams["DescripcionNovedad"] ) .'</sim:DescripcionNovedad>
                         </ns1:descargueRemision>
                      </ns1:RegistrarDescargueRemision>',  XSD_ANYXML, SOAPStruct,NULL,NULL);
         $mResult = $oSoapClient -> RegistrarDescargueRemision( new soapParam($mParam, "LOL"));
         #echo "<pre>Response RegistrarDescargueRemision:<br>"; print_r($mResult); echo "</pre>";
        # Toma las variables que responde el webservice de Simplexity -
        $mTypeRespon = $mResult -> RegistrarDescargueRemisionResult -> Type;
        $mMesaRespon = $mResult -> RegistrarDescargueRemisionResult -> Message;
        /*****************************************************************************************************************************************************/

        # -------------------------------------------------------------
        # Consumo del Webservice de Astrans con cURL de PHP5 Nativo --------------------------------------------------------------------------------------------------------------------------------
        $mMessage = NULL;
        $mNumberTransa = NULL;
        $mType = NULL;
        $mXmlEnvelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/" xmlns:sim="http://schemas.datacontract.org/2004/07/Simplexity.AsTrans.Corona.Application.Main.DTOs">
                            <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
                                <wsa:Action>http://tempuri.org/IServicioViajes/RegistrarDescargueRemision</wsa:Action>
                                <wsa:To>https://astrans.coronaindustrial.net:8065/ServicioViajes.svc</wsa:To>
                            </soap:Header>
                             <soap:Body>
                                <tem:RegistrarDescargueRemision>
                                   <tem:descargueRemision>
                                      <sim:NumeroViaje>'.$mParams["NumeroViaje"].'</sim:NumeroViaje>
                                      <sim:NumeroRemision>'.@preg_replace( self::$cPatronRemi, self::$cReemplazoRemi, $mParams["NumeroRemision"] ).'</sim:NumeroRemision>
                                      <sim:CodigoEvento>'.$mParams["CodigoEvento"].'</sim:CodigoEvento>
                                      <sim:Fecha>'.$mParams["Fecha"].'</sim:Fecha>
                                      <sim:CodigoNovedad>'.$mParams["CodigoNovedad"].'</sim:CodigoNovedad>
                                      <sim:DescripcionNovedad>'.@preg_replace( self::$cPatron, self::$cReemplazo, $mParams["DescripcionNovedad"] ) .'</sim:DescripcionNovedad>
                                   </tem:descargueRemision>
                                </tem:RegistrarDescargueRemision>
                             </soap:Body>
                          </soap:Envelope>';

          $mSoapAnstran = curl_init( self::$cUrlAstrans );
          curl_setopt($mSoapAnstran, CURLOPT_URL,            self::$cUrlAstrans );
          curl_setopt($mSoapAnstran, CURLOPT_USERAGENT, 'php');
          curl_setopt($mSoapAnstran, CURLOPT_CONNECTTIMEOUT, 6);
          curl_setopt($mSoapAnstran, CURLOPT_TIMEOUT,        6);
          curl_setopt($mSoapAnstran, CURLOPT_RETURNTRANSFER, true );
          curl_setopt($mSoapAnstran, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($mSoapAnstran, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($mSoapAnstran, CURLOPT_POST,           true );            
          curl_setopt($mSoapAnstran, CURLOPT_POSTFIELDS,     $mXmlEnvelope); 
          curl_setopt($mSoapAnstran, CURLOPT_VERBOSE, TRUE); 
          curl_setopt($mSoapAnstran, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml","SOAPAction: http://tempuri.org/IServicioViajes/RegistrarDescargueRemision", "Content-length: ".strlen($mXmlEnvelope))); 

          $mResultSoap = curl_exec($mSoapAnstran);
          curl_close($mSoapAnstran);
 
          # Convierte el Xml a Array --------------------------------------------------------------------------------------------------------------------------
          $mXml2 = self::XMLtoArray($mResultSoap);
        

          # Captura de los valores que retorna Mensage, numero transaccion Tipo
          $mMessage = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARDESCARGUEREMISIONRESPONSE"]["REGISTRARDESCARGUEREMISIONRESULT"]["B:MESSAGE"];
          $mNumberTransa = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARDESCARGUEREMISIONRESPONSE"]["REGISTRARDESCARGUEREMISIONRESULT"]["B:TRANSACTIONNUMBER"]["I:NIL"];
          $mType = $mXml2["S:ENVELOPE"]["S:BODY"]["REGISTRARDESCARGUEREMISIONRESPONSE"]["REGISTRARDESCARGUEREMISIONRESULT"]["B:TYPE"];

          # Valida el campo del mensage.
          $mMessage = is_array($mMessage) ? $mMessage["I:NIL"] : $mMessage;
  
          # Si el Type es diferente a 1, se debe generar log ---------------------------
          if( $mType !=  1) 
            throw new SoapFault( $mMessage, $mType);

          $mReturn = array("cod_respon" => $mType, "msg_respon" =>  $mMessage  );

          /*mail("nelson.liberato@intrared.net, maribel.garcia@eltransporte.org", 
               "Novedad Remisi OET - Corona", 
               "Datos Enviados: ".var_export( $mXmlEnvelope, true )."\nRespuesta: ".var_export($mReturn, true) );*/
        
    }
    catch(SoapFault $e) {
      # Si estan cambiadas las variables, raro no?
      $mXml = array( "xml_respon"=> $mXmlEnvelope,"xml_reques"=> $mResultSoap ,"xml_arrayx"=> var_export($mXml2, true)  );
      $mReturn = array("cod_respon" => $e -> faultstring,  "msg_respon" => $e -> faultcode ); 
      self::LogDataError( $mParams, $mReturn, "3", $mXml);
    }
    unset($mMessage);
    unset($mNumberTransa);
    unset($mType);
    return $mReturn;
  } 

  /*! \fn: XMLtoArray
  *  \brief: Metodo para convertir el xml en Array
  *  \author: Ing. Nelson Liberato -StockWaveflow
  *  \date: 09/03/2015   
  *  \param: XML  : String del XML
  *  \return array
  */
  private function XMLtoArray($XML)
  {
      $xml_parser = xml_parser_create();
      xml_parse_into_struct($xml_parser, $XML, $vals);
      xml_parser_free($xml_parser);
      // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
      $_tmp='';
      foreach ($vals as $xml_elem) {
          $x_tag=$xml_elem['tag'];
          $x_level=$xml_elem['level'];
          $x_type=$xml_elem['type'];
          if ($x_level!=1 && $x_type == 'close') {
              if (isset($multi_key[$x_tag][$x_level]))
                  $multi_key[$x_tag][$x_level]=1;
              else
                  $multi_key[$x_tag][$x_level]=0;
          }
          if ($x_level!=1 && $x_type == 'complete') {
              if ($_tmp==$x_tag)
                  $multi_key[$x_tag][$x_level]=1;
              $_tmp=$x_tag;
          }
      }
      // jedziemy po tablicy
      foreach ($vals as $xml_elem) {
          $x_tag=$xml_elem['tag'];
          $x_level=$xml_elem['level'];
          $x_type=$xml_elem['type'];
          if ($x_type == 'open')
              $level[$x_level] = $x_tag;
          $start_level = 1;
          $php_stmt = '$xml_array';
          if ($x_type=='close' && $x_level!=1)
              $multi_key[$x_tag][$x_level]++;
          while ($start_level < $x_level) {
              $php_stmt .= '[$level['.$start_level.']]';
              if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                  $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
              $start_level++;
          }
          $add='';
          if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
              if (!isset($multi_key2[$x_tag][$x_level]))
                  $multi_key2[$x_tag][$x_level]=0;
              else
                  $multi_key2[$x_tag][$x_level]++;
              $add='['.$multi_key2[$x_tag][$x_level].']';
          }
          if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
              if ($x_type == 'open')
                  $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
              else
                  $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
              eval($php_stmt_main);
          }
          if (array_key_exists('attributes', $xml_elem)) {
              if (isset($xml_elem['value'])) {
                  $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                  eval($php_stmt_main);
              }
              foreach ($xml_elem['attributes'] as $key=>$value) {
                  $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                  eval($php_stmt_att);
              }
          }
      }
      return $xml_array;
  }



  /*! \fn: indent
  *  \brief: Metodo para identar un xml en caso que se quiera mostrar en pantalla
  *  \author: Ing. Nelson Liberato
  *  \date: 09/03/2015   
  *  \param: text  : xml que se quiere identar (string)
  *  \return string
  */
  function indent($text)
  {
      // Create new lines where necessary
      $find = array('>', '</', "\n\n");
      $replace = array(">\n", "\n</", "\n");
      $text = str_replace($find, $replace, $text);
      $text = trim($text); // for the \n that was added after the final tag

      $text_array = explode("\n", $text);
      $open_tags = 0;
      foreach ($text_array AS $key => $line)
      {
        if (($key == 0) || ($key == 1)) // The first line shouldn't affect the indentation
          $tabs = '';
        else
        {
          for ($i = 1; $i <= $open_tags; $i++)
            $tabs .= "\t";
        }

        if ($key != 0)
        {
          if ((strpos($line, '</') === false) && (strpos($line, '>') !== false))
            $open_tags++;
          else if ($open_tags > 0)
            $open_tags--;
        }

        $new_array[] = $tabs . $line;

        unset($tabs);
      }
      $indented_text = implode("\n", $new_array);

      return $indented_text;
  }



  /*! \fn: RegistLog
  *  \brief: Metodo para registrar Log de errores Cuando no pasan a Astrans
  *  \author: Ing. Nelson Liberato
  *  \date: 09/03/2015   
  *  \param: mData      : Datos los cuales se enviaron en el XML 
  *  \param: mProccess  : Proceso el cual se hacer el registro Cumplido cita, descargue remesa, descargue remision 
  *  \return n/a
  */
  function LogDataError( $mData = NULL, $mReturn =NULL, $mProccess = 4, $mDataXml = NULL)
  {
    try
    {
        $mConsec = 'SELECT IF( MAX( cod_consec ) IS NULL , "1", MAX( cod_consec ) + 1 ) AS cod_consec
                    FROM '.BASE_DATOS.'.tab_errorx_simxty 
                    WHERE num_viajex = "'.$mData["NumeroViaje"].'"  ';
        $mExecute = new Consulta( $mConsec , self::$cConection);
        $mConsec = $mExecute -> ret_matriz("a");
        $mConsec = $mConsec[0]["cod_consec"];
   
        $mQuery = 'INSERT INTO '.BASE_DATOS.'.tab_errorx_simxty 
                      ( cod_consec, num_viajex, cod_evento, cod_noveda, 
                        fec_noveda, num_remesa, num_remisi, 
                        des_noveda, cod_respon, msg_respon, tip_regist,  
                        xml_respon, xml_reques, xml_arrayx,
                        usr_creaci, fec_creaci ) 
                      VALUES 
                      ( "'.$mConsec.'", "'.$mData["NumeroViaje"].'", "'.$mData["CodigoEvento"].'", "'.$mData["CodigoNovedad"].'", 
                        "'.$mData["Fecha"].'", "'.$mData["NumeroRemesa"].'", "'.$mData["NumeroRemision"].'" , 
                        "'.$mData["DescripcionNovedad"].'", "'.$mReturn["cod_respon"].'" , "'.addslashes(utf8_decode($mReturn["msg_respon"])).'" , "'.$mProccess.'", 
                        "'.addslashes($mDataXml["xml_respon"]).'", "'.addslashes($mDataXml["xml_reques"]).'","'.$mDataXml["xml_arrayx"].'",
                        "'.self::$cUserApl.'", NOW()  ) ';
        $mLog = new Consulta( $mQuery , self::$cConection); 
        if(!$mLog ) {
          throw new Exception("Error al generar Log:\n".$mQuery, "3001");
        }
            
  
    }
    catch(Exception $e)
    {
      
      $mReturn = array("cod_respon" => "3", "msg_respon" => "Error en el registro Log: ".$mData["process"] );
    }
    return true;
  }


}

 

  
?>

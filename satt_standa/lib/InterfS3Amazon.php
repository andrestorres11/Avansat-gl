<?php

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

  /*! \Class: InterfS3Amazon
  *  \brief: Clase encargada de hacer la respectiva conexion a servidor S3 de Amazon
  *  \author: Ing. Nelson Liberato
  *  \date: 09/06/2015   
  *  \param: $cBucket  -  Nombre del bucket
  *  \param: $cUserxx  -  NOmbre usuario de conexion al bucket
  *  \param: $cAcckey  -  Llave publica
  *  \param: $cPrikey  -  Llave Privada
  *  \param: $cConexion  -  Variable de la conexion para la clase en general
  *  \param: $cMsgError  -  Variable de mensaje en caso de error
  *  \return array
  */
  class InterfS3Amazon
  {
    # Variables de coneccion a S3
    private $cBucket = "grabaciones.asterisk.intrared.s3";
    private $cUserxx = "grabacionesasterisk";
    private $cAcckey = "AKIAIJ34RW6BAKOKS4OQ";
    private $cSeckey = "QI8dbD+svURJQWX2lEQwq/C3VH1H54+FI81JlgzL";

    # Variable de almacenamiento de datos de S3
    private $cListBucket = NULL;

    # Variables de la clase
    private $cConexion = NULL;
    private $cMsgError = "";
    private $cFileFolde = "audios/";
    private $cFileAudio = "2015/05/28/out-3113850011-126-20150528001215-00-1432789935.64891.wav";
    
    /*! \fn: __construct
    *  \brief: Constructorea de la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mCo  -  Nombre del bucket     
    *  \return array
    */
    function __construct( $mBdxConnect = NULL, $mNumDespac = NULL, $mNumConsec = NULL )
    {
         $this -> cConexion = $mBdxConnect;
         InterfS3Amazon::ConnectS3Amazon($mNumDespac, $mNumConsec);
    }

    /*! \fn: setMsgError
    *  \brief: Asigna mensaje de error de manera global en la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function setMsgError( $mMsgError )
    {
      $this -> cMsgError .= $mMsgError;
    }

    /*! \fn: getMsgError
    *  \brief: Recupera mensaje de error de manera global en la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */
    function getMsgError()
    {
      return $this -> cMsgError;
    }

    /*! \fn: getListBucket
    *  \brief: Retorna la Lista de Buckets creados en S3
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015           
    *  \return Array
    */
    function getListBucket()
    {
      return $this -> cListBucket;
    }


    /*! \fn: sendError
    *  \brief: Notifica en caso de que algun proceso genere error
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */
    function sendError( $mSendData, $mAditionalData, $mArraData = NULL )
    {
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Operacion: Interfaz Trafico - PAD \n";
      $mMessage .= "WSDL: ".$this->cUrlWsdlA." \n";
      $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
      $mMessage .= "Empresa de transporte: Trafico \n";
      $mMessage .= "Aplicacion: satt_faro \n";
      $mMessage .= "Despacho: ".$mSendData["num_despac"]." \n";
      $mMessage .= "Placa: ".$mSendData["num_placax"]." \n";
      $mMessage .= "Operador: ".$mAditionalData['nom_operad']." \n";
      $mMessage .= "******** Detalle ******** \n";
      $mMessage .= "Codigo de error: ".$mAditionalData['cod_errorx']." \n";
      $mMessage .= "Mensaje de error: ".$this -> getMsgError()." \n";
      $mMessage .= "Datos Array: ".var_export($mArraData, true)." \n";
      mail( "nelson.liberato@intrared.net", "Web service Faro - PAD ERROR (ASISTR) WEB10", $mMessage,'From: InterfazInterfS3Amazon@intrared.net' );
      
    }

    /*! \fn: getDataDespac
    *  \brief: Inicia para traer las llamadas de S3
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */         
    function ConnectS3Amazon( $mNumDespac = NULL )
    {
      try
      {        
        # Verifica existencia de la clase de S3 Amazon ------------------------------------------------------------------
        if (!class_exists('S3')) require_once 's3/s3-php5-curl/S3.php';


        # Verifica cUrl -------------------------------------------------------------------------------------------------
        if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
          throw new Exception("ERROR: CURL extension not loaded","3001");
 

        # Verifica existencia de las llaves -String ---------------------------------------------------------------------        
        if ($this -> cAcckey == '' || $this -> cSeckey == '')
          throw new Exception("Por favor verifique la llave de acceso y la llave privada","3001");

        # Inicia la clase de S3Amazon -----------------------------------------------------------------------------------
        $mS3Amazon = new S3($this -> cAcckey, $this -> cSeckey, false);

        # Lista los Buckets existentes en S3 ----------------------------------------------------------------------------
        $this -> cListBucket = $mS3Amazon::listBuckets();

        # Consulta el Bucket de las llamadas Faro -----------------------------------------------------------------------
        $mFiles = $mS3Amazon::getBucket($this -> cBucket);        
        if(!$mFiles) {
          $mError = $mS3Amazon::getErrorBucket();
          throw new Exception($mError['message'], $mError['code']);
        }

        # Se trae el objeto a descargar del bucket, se retorna el objeto como string
        $mFilesSound = $mS3Amazon::getObject($this -> cBucket, $this -> cFileAudio );       
        if($mFilesSound)
        {
        	# Crea el objeto - audio ------------------------------------------------------------------
        	$mWavFile = fopen($this -> cFileFolde.substr($this -> cFileAudio, 11), "w+");
        	if(!$mWavFile ) {
        		throw new Exception("No se pudo crear el archivo de audio en local", "2001");
        	}
        	# Escribe el string del objeto retornado por S3 --------------------------------------------------------
        	if(!fwrite($mWavFile, $mFilesSound-> body) ) {
        		throw new Exception("No se logro escribir el archivo de audio en local", "2001");
        	}

        	# Cierra la escritura del archivo ----------------------------------------------------------------------
        	fclose($mWavFile);
        }
        else
        	throw new Exception("No Se Encontro el archivo de audio: ".$this -> cFileAudio, "3001");


   
        # Crea el elemento de reproduccion del audio que se descargó de S3 -----------------------------------------
        $mObjetAudio = '<audio controls>
                          <source src="'.$this -> cFileFolde.substr($this -> cFileAudio, 11).'" type="audio/wav">
					      Su navegador no soporta elementos de Audio
				        </audio>';

               

          
		return array("cod_respon"=> "1000", "msg_respon"=>"Audio Descargado Correctamente", "dat_audio" => $mObjetAudio ); 

      }
      catch(Exception $e)
      {
        $mReturn =  array("cod_respon"=> $e->getCode(), "msg_respon" => $e->getMessage() );
        echo "<pre>Catch error: "; print_r($mReturn); echo "</pre>";
        return $mReturn;
      }
            
    }
      
    /*! \fn: Consulta
    *  \brief: Metodo para hacer la respectiva conexion a la base de datos de manera local para el script
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */      
    /*   
    function Consulta( $mQuery = NULL, $mRetorna = true)
    {       
      $mError = array();
      $mLink = mysql_connect("bd10.intrared.net:3306", "nliberato", ",oMaXe,o"); 
      if( !$mLink)
      {
        $mError["ID"] = false;
        $mError["Code"] = mysql_errno($mLink );
        $mError["Error"] = mysql_error($mLink );
        return $mError;
      }
      mysql_select_db("satt_faro");

      $mDataArray = array();
      $mCont = 0;
      $mData = mysql_query($mQuery);

      if( $mRetorna ) 
      {
        while ($mRow = mysql_fetch_assoc($mData)) 
        {
          $mDataArray[$mCont++] = $mRow;         
        }
      }

      mysql_close($mLink);
      return $mDataArray;
    }*/

  }

  $S3Amazon = new InterfS3Amazon();

?>
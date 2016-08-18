<?php
# Incluye constantes de configuración
include("lib/Config.Kons.inc");
include("lib/Connect.class.inc");

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

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
    # Variables de conexion a S3
    private static $cBucket = BUCKET;
    private static $cUserxx = USERBT;
    private static $cAcckey = ACCEBT;
    private static $cSeckey = SECRBT;
    private static $cS3Instan = NULL;

    # Variable de almacenamiento de datos de S3
    private static $cListBucket = NULL;

    # Variables de la clase
    private static $cConexion = NULL;
    private static $cMsgError = "";
    private static $cFileFolde = "audios/";
    private static $cFileAudio = NULL;

    # variables de datos que ingresan por S3 
    private static $cUniqueID = NULL;
    /*private static $cFileAudio = "2015/05/28/out-3113850011-126-20150528001215-00-1432789935.64891.wav";
    private static $cFileAudio = "2015/05/28/out-3113850011-126-20150528001215-00-1432789935.64891.wav";*/
    
    /*! \fn: __construct
    *  \brief: Constructorea de la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mCo  -  Nombre del bucket     
    *  \return array
    */
    function __construct( )
    {
    	# inicia clase de conexion
    	#InterfS3Amazon::$cConexion = new ClassConection();

    	# Captura datos GET, Variables de entrada
    	InterfS3Amazon::$cUniqueID = $_REQUEST["uniqueid"] = "1467938741.37534";
     


    	# Consulta la llamada a Subir
    	#InterfS3Amazon::getCall();

    	# Verifica si hay datos para subir
    	if(!count(InterfS3Amazon::$cFileAudio ) > 0 && !InterfS3Amazon::$cFileAudio["recordingfile"] != ''){ 
    		if(InterfS3Amazon::ConnectS3Amazon() ) {
    			InterfS3Amazon::setUploadFile();
    		}
    	}        
    }

    /*! \fn: getCall
    *  \brief: Consulta datos de la llamada realizada en base de datos
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function getCall( )
    {
      $mQuery = 'SELECT * FROM cdr WHERE uniqueid = "'.InterfS3Amazon::$cUniqueID.'" ';
      InterfS3Amazon::$cFileAudio = InterfS3Amazon::$cConexion -> setExecute( $mQuery, true );
      InterfS3Amazon::$cFileAudio = InterfS3Amazon::$cFileAudio[0];
      echo "<pre>Retorna query"; print_r( InterfS3Amazon::$cFileAudio ); echo "</pre>";      
      return true;
    }    


    /*! \fn: ConnectS3Amazon
    *  \brief: Inicia conexion para traer las llamadas de S3
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */         
    function ConnectS3Amazon(   )
    {
      try
      {        
        # Verifica existencia de la clase de S3 Amazon ------------------------------------------------------------------
        if (!class_exists('S3')) require_once 'lib/s3_PHP5/S3.php';
        # Verifica cUrl -------------------------------------------------------------------------------------------------
        if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) {
          throw new Exception("ERROR: CURL extension not loaded","3001"); 
        }
        # Verifica existencia de las llaves -String ---------------------------------------------------------------------        
        if (InterfS3Amazon::$cAcckey == '' || InterfS3Amazon::$cSeckey == '') {
          throw new Exception("Por favor verifique la llave de acceso y la llave privada","3001");
        }
        # Inicia la clase de S3Amazon -----------------------------------------------------------------------------------
        InterfS3Amazon::$cS3Instan = new S3(InterfS3Amazon::$cAcckey, InterfS3Amazon::$cSeckey, false);
        # Lista los Buckets existentes en S3 ----------------------------------------------------------------------------
        #echo "<pre>Lista de Buckets:<br>"; print_r(  InterfS3Amazon::$cS3Instan->listBuckets()   ); echo "</pre>"; 
        #echo "<pre>Lista archivos del bucket<br>"; print_r(  InterfS3Amazon::$cS3Instan->getBucket(InterfS3Amazon::$cBucket) ); echo "</pre>"; die();
        #echo "<pre>Lista archivos del bucket<br>"; print_r(  InterfS3Amazon::$cS3Instan->listObjects(array("Bucket"=> InterfS3Amazon::$cBucket, "MaxKeys" => "2" ) ) ); echo "</pre>"; die();
        return true; 

      }
      catch(Exception $e)
      {
      	InterfS3Amazon::$cConexion -> setLog($e->getCode(), $e->getMessage()); 
      	return false;
      }
            
    }
    
    /*! \fn: setUploadFile
    *  \brief: Subida del archivo a S3
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */         
    function setUploadFile(   )
    {
		try
		{    
			$mDateFile = explode("-",substr(InterfS3Amazon::$cFileAudio["calldate"], 0 ,10));  # Carpetas segun la fecha de la llamada
			#$mFileWav  = InterfS3Amazon::$cFileAudio["recordingfile"];                         # Ruta del audio wav, fisico  
			$mFileWav  = "ExecuteS3Upload.php";                         # Ruta del audio wav, fisico  
			$mNameFile = baseName($mFileWav);

	
			#$mPathS3   = $mDateFile[0]."/".$mDateFile[1]."/".$mDateFile[2]."/".$mNameFile;
			$mPathS3   = "/".$mNameFile;
			 
			echo "<pre>File on S3:"; print_r($mPathS3); echo "</pre>";

			$mUploadToS3 = InterfS3Amazon::$cS3Instan -> putObjectFile($mFileWav, 
				                                                       InterfS3Amazon::$cBucket, 
				                                                       $mNameFile, 
				                                                       S3::ACL_PUBLIC_READ
				                                                      );

		 
 
			echo "<pre>Subida S3"; print_r( var_dump( $mUploadToS3 ) ); echo "</pre>";
			#echo "<pre>Error PUT S3"; print_r(InterfS3Amazon::$cS3Instan -> getErrorBucket()); echo "</pre>";
		}
		catch(Exception $e)
		{
			InterfS3Amazon::$cConexion -> setLog($e->getCode(), $e->getMessage()); 
			return false;
		}
            
    }
 







 

  }

  $S3Amazon = new InterfS3Amazon();

?>
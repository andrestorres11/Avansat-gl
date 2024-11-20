<?php
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
#-----------------------------------------------------------------
# @brief clase para consumir webservice de GPS Syscaf para Corona
# @autor Nelson Liberato
# @fecha 2014-10-03
# @nota  una mierda para consumir esa vuelta
#-----------------------------------------------------------------

/*
    define("Hostx5","aglbd.intrared.net");
    define("USUARIO5","satt_faro");
    define("CLAVE5","sattfaro");
    define("BASE_DATOS5","satt_faro");*/
    //define("WsdFAR","https://avansatgl.intrared.net/ap/interf/app/sat/wsdl/sat.php");



ini_set( "soap.wsdl_cache_enabled", "0" );

class SyscafCorona
{
	private $cConexion = NULL;
	private $cUsariSys = "coronaws";
	private $cClaveSys = "corona2019" ; //"corona123"; // "jsalcedo123";
	private $cAplicSys = NULL;
	private $cErrowsdl = NULL;
	private $cProcesox = 0;


	function __construct( $fConexion = NULL)
	{
		@include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
		@include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes propias.
		@include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
		@include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker

		# Proceso de Logs - Configuracion
		$fExcept = new Error( array( "dirlog" => LogDir, "notlog" => false, "logmai" => NotMai ) );
		$fExcept -> SetUser( 'InterfGPS' );
		$fExcept -> SetParams( "Sat", "NovedaGPSSyscaf" );


		$this -> cConexion = new Consult( array( "server"=> Hostx5, "user"  => USUARIO5, "passwd" => CLAVE5, "db" => BASE_DATOS5 ), $fExcept );
		SyscafCorona::PrincipalVoid( $fExcept  );
	}

	function PrincipalVoid( $fExcept  )
	{
		try
		{

			 
			echo "<pre>"; print_r("Listo para funcionar "); echo "</pre>"; 
 			/*
		    $fQuerySelVehiculos = "  SELECT a.num_despac, 
				                     b.cod_transp, 
				                     b.num_placax,
				                     a.gps_operad AS cod_operad
				                FROM ".BASE_DATOS5.".tab_despac_despac a 
				                     INNER JOIN ".BASE_DATOS5.".tab_despac_vehige b ON a.num_despac = b.num_despac
				                   
				                                  
				               WHERE a.fec_salida IS NOT NULL 
				                 AND a.fec_salida <= NOW() 
				                 AND a.fec_llegad IS NULL 
				                 #AND a.fec_llegad <= '2015-02-01 00:00:00' 
				                 AND a.ind_planru = 'S' 
				                 AND a.ind_anulad = 'R'
				                 AND b.ind_activo = 'S'  
				                 AND b.cod_transp = '860068121' 
				                 AND a.gps_operad = '900084087'
                                
				                 ";*/

		   $fQuerySelVehiculos = " SELECT a.cod_operad, a.cod_transp, a.nom_aplica, a.num_despac,  
                                 a.num_placax, a.fec_salida, a.usr_gpsxxx, a.clv_gpsxxx,  
                                 a.idx_gpsxxx  
                           FROM ".BASE_DATOS5.".t_vehicu_gpsxxx a,  
                                ".BASE_DATOS5.".t_interf_parame b  
                           WHERE a.cod_operad = b.cod_operad  
                             AND a.cod_transp = b.cod_transp 
                             AND a.fec_salida <= NOW()   
                             AND ( a.fec_ultrep < DATE_SUB( NOW(), INTERVAL 50 MINUTE ) OR fec_ultrep IS NULL )   
							 AND a.fec_creaci >= DATE_SUB( NOW(), INTERVAL 15 DAY )
                             AND a.cod_operad = 900084087   
                             AND a.cod_transp = 860068121                               
                          ORDER BY a.fec_creaci ASC  ";


			echo "<pre>"; print_r($fQuerySelVehiculos); echo "</pre>"; 
		    $this -> cConexion -> ExecuteCons( $fQuerySelVehiculos );
		    $fRecorVehiculos = $this -> cConexion -> RetMatrix( "a" );

			echo "<hr>Cantidad vehiculos en Ruta: ".sizeof($fRecorVehiculos)."<hr>";			 
			echo "<pre>Lista: "; print_r($fRecorVehiculos); echo "</pre>"; 
			
			#die();

			if( 0 != $this -> cConexion -> RetNumRows() )
		    {
		      foreach( $fRecorVehiculos as $fVehiculo )
		      {
		        unset( $novedaGPS );
		        $novedaGPS = array();
				
				echo "<hr><pre>Datos Vehiculo"; print_r( $fVehiculo ); echo "</pre>";

				# Formateo de la Placa Segun Syscaf ---------------------------------------------------------------------------------------
				$mNumPlacax = SyscafCorona::PlateFormat( $fVehiculo["num_placax"]);
				echo "<pre>Formato Placa:  "; print_r( $mNumPlacax ); echo "</pre>";
				# -------------------------------------------------------------------------------------------------------------------------


				# Paso 1: Peticion Token --------------------------------------------------------------------------------------------------
				$mToken = SyscafCorona::getToken();
				if( !$mToken) throw new Exception( "Error Usuario y/o Clave -  InvalidLogin", "2001" );
				# -------------------------------------------------------------------------------------------------------------------------


				# Paso 2 Lista de Vehiculos -----------------------------------------------------------------------------------------------
				#echo "<pre>Token:  "; print_r( $mToken ); echo "</pre>";
				$mIdVehicle = SyscafCorona::getListIds( $mToken, $mNumPlacax );

					# -------------------------------------------------------------------------------------------------------------------------
					# Paso 3 Datos del GPS del Vehiculo con el ID asignado del paso 2 ---------------------------------------------------------
					#echo "<pre>ID:  "; print_r( $mIdVehicle ); echo "</pre>";
				if( $mIdVehicle ) #en caso que se joda el Id del vehiculo 
				{
					$mResult = SyscafCorona::getDataGpsVehicle( $mToken, $mIdVehicle);
					#if( !$mResult )	throw new Exception( $this -> cErrowsdl, "2001" );
					if( !$mResult ) echo $this -> cErrowsdl;

					# -------------------------------------------------------------------------------------------------------------------------
					# Paso Adicional - Traer el detalle de la ubicacion segun coordenadas
					$mDetail = SyscafCorona::getDataCoordenada( $mToken, 
						                                        $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Latitude,
	                                                            $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Longitude,
	                                                            $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> ID);
					#echo "<pre>Detalle GPS:  "; print_r( $mDetail ); echo "</pre>";
					#if( !$mDetail )	throw new Exception( $this -> cErrowsdl, "2001" );
					if( !$mDetail ) echo $this -> cErrowsdl;

					$mDetailX  = $mDetail -> GetReverseGeoForGPSPositionsResult -> GeoLocation -> Country." - ";
					$mDetailX .= $mDetail -> GetReverseGeoForGPSPositionsResult -> GeoLocation -> Town." - ";
					$mDetailX .= $mDetail -> GetReverseGeoForGPSPositionsResult -> GeoLocation -> City." - ";
					$mDetailX .= $mDetail -> GetReverseGeoForGPSPositionsResult -> GeoLocation -> Zip;
					# -------------------------------------------------------------------------------------------------------------------------

				}
				else
				{
					echo $this -> cErrowsdl;

				   $fQueryDelVehiGps = "DELETE FROM ".BASE_DATOS5.".t_vehicu_gpsxxx " .
			                                  "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
			                                    "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
			                                    "AND num_placax = '".$fVehiculo['num_placax']."' ".
			                                    "AND num_despac = '".$fVehiculo['num_despac']."' ";

			            if( $this -> cConexion -> ExecuteCons( $fQueryDelVehiGps, "R" ) === FALSE ) 
			             throw new Exception( "Error en DELETE.", "3001" );
				}


				# Captura datos para guadar en las novedades ------------------------------------------------------------------------------
				#echo "<pre>Datos GPS:  "; print_r( $mResult ); echo "</pre>";
				# Se transforma la fecha 2012-05-07T16:25:49-04:00 para restarle 5 horas y que sea igual a 2012-05-07 11:25
				$fecha = substr( str_replace( "T", ' ',  $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Time ), 0, 19 );
				
				$dd=0;
				$mm=0;
				$yy=0;
				$hh=-6;
				$mn=0;
				$ss=0;

				$date_r = getdate( strtotime( $fecha ) );
				$fecha = date( 'Y-m-d H:i', mktime( ( $date_r["hours"] + $hh ),( $date_r["minutes"] + $mn ), ( $date_r["seconds"] + $ss ), ( $date_r["mon"] + $mm ), ( $date_r["mday"] + $dd ), ( $date_r["year"] + $yy ) ) );

				 
				$novedaGPS['fec_noveda'] = $fecha;
				$novedaGPS['val_latitu'] = (string) $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Latitude;
				$novedaGPS['val_longit'] = (string) $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Longitude;
				$novedaGPS['det_ubicac'] = $mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Address.', '.$mDetailX;
				$novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
				$novedaGPS['all_infgps'] .= ". Velocidad: ".$mResult -> GetLatestPositionPerVehicleResult -> GPSPosition -> Velocity;


				 
				# inicia transaccion al sat de las novedades GPS ---------------------------------------------------------------------------------
				//$this -> cConexion ->StartTrans();
				# --------------------------------------------------------------------------------------------------------------------------------


				#$this -> cConexion ->StartTrans();

		        //Se actualiza la fecha del ultimo consumo
		        $fQueryUpdVehiGps = "UPDATE ".BASE_DATOS5.".t_vehicu_gpsxxx " .
		                               "SET fec_ultcon = NOW() " .
		                             "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
		                               "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
		                               "AND num_placax = '".$fVehiculo['num_placax']."' ";

		        if( $this -> cConexion -> ExecuteCons( $fQueryUpdVehiGps, "R" ) === FALSE )
		             throw new Exception( "Error en UPDATE: ".$fQueryUpdVehiGps, "3001" );

		        if( isset( $novedaGPS['all_infgps'] ) && $novedaGPS['all_infgps'] !== NULL && $mIdVehicle != false )
		        {

		        	//Se envia la novedad GPS a la aplicacion sat
			        ini_set( "soap.wsdl_cache_enabled", "0" );
			        
			        $ws = WsdFAR;
			        $oSoapClient = new soapclient( $ws, array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
			        //$oSoapClient = new soapclient( WsdSAT, array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
			    
			        $parametros = array( "nom_aplica" => $fVehiculo['nom_aplica'], 
			                             "num_despac" => $fVehiculo['num_despac'], 
			                             "cod_noveda" => '4999', 
			                             "fec_noveda" => date( "Y-m-d H:i", strtotime( $novedaGPS['fec_noveda'] ) ), 
			                             "des_noveda" => $novedaGPS['all_infgps'], 
			                             "val_longit" => $novedaGPS['val_longit'], 
			                             "val_latitu" => $novedaGPS['val_latitu'], 
			                             "nom_llavex" => '3c09f78c210a18b686ae2540b0d12358' );//Se usa una llave para que solo oet pueda usar el metodo
			        echo "<pre>";                                                   
			        print_r( $parametros );
			        echo "</pre>";                                                   
			        $mResult = $oSoapClient -> __call( "setNovedadGPS", $parametros );    
			        echo "<pre>";                                                   
			        print_r( $mResult );
			        echo "</pre>";                                                   
			        
			        
			        $mResult = explode( "; ", $mResult );
			        $mCodResp = explode( ":", $mResult[0] );
			        $mMsgResp = explode( ":", $mResult[1] );
			        //$fVehiculo['num_placax'] = 'WHB760';
			  
			        if( "1000" != $mCodResp[1] )
			        {
			          $mMessage = "******** Encabezado ******** \n";
			          $mMessage .= "Operacion: Insertar Novedad GPS \n";
			          $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
			          $mMessage .= "Fecha Novedad: ".$parametros["fec_noveda"]." \n";
			          $mMessage .= "Aplicacion: ".$parametros["nom_aplica"]." \n";
			          $mMessage .= "Despacho: ".$parametros["num_despac"]." \n";
			          $mMessage .= "Descripcion Novedad: ".$parametros["des_noveda"]." \n";
			          $mMessage .= "Operador: ".$fVehiculo['cod_operad']." \n";
			          $mMessage .= "Placa: ".$fVehiculo['num_placax']." \n";
			          $mMessage .= "******** Detalle ******** \n";
			          $mMessage .= "Codigo de error: ".$mCodResp[1]." \n";
			          $mMessage .= "Mensaje de error: ".$mMsgResp[1]." \n";
			           #mail( NotMai, "Web service GPS setNovedaGPS CORONA", $mMessage,'From: soporte.ingenieros@intrared.net' );
			           if( strpos($mMsgResp[1], 'no se encuentra en ruta, o no esta registrado') !== false || strpos($mMsgResp[1], 'Aplicacion no encontrada') !== false  )
			           {
			             //Se ELIMINA de la lista de reportando GPS si no se encuentra en ruta
			             $fQueryDelVehiGps = "DELETE FROM ".BASE_DATOS5.".t_vehicu_gpsxxx " .
			                                  "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
			                                    "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
			                                    "AND num_placax = '".$fVehiculo['num_placax']."' ".
			                                    "AND num_despac = '".$fVehiculo['num_despac']."' ";
			           	
			           	 echo "Delete Por el webservice: ".$fQueryDelVehiGps;
			             if( $this -> cConexion -> ExecuteCons( $fQueryDelVehiGps, "R" ) === FALSE ) 
			             	throw new Exception( "Error en DELETE.", "3001" );
			            }
		            }
		            else
		            {
		              //Se actualiza la fecha del ultimo reporte satisfactorio
		              $fQueryUpdVehiGps = "UPDATE ".BASE_DATOS5.".t_vehicu_gpsxxx " .
		                                      "SET fec_ultrep = NOW() " .
		                                 "WHERE cod_operad = '".$fVehiculo['cod_operad']."' ".
		                                   "AND cod_transp = '".$fVehiculo['cod_transp']."' ".
		                                   "AND num_placax = '".$fVehiculo['num_placax']."' ";
		             if( $this -> cConexion -> ExecuteCons( $fQueryUpdVehiGps , "R" ) === FALSE )
		             throw new Exception( "Error en UPDATE.", "3001" );
		            }

		        }
		        #$this -> cConexion ->Commit();



			  }#Fin foreach

			}# Fin if
		}
		catch( Exception $e)
		{
			echo "<pre>"; print_r($e ); echo "</pre>";
			$mTrace = $e -> getTrace();
		    $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
		                          $e -> getLine(), $fVehiculo['nom_aplica'], $fVehiculo['num_placax'] );
		    
		    return FALSE;
		}
	}


	function PlateFormat( $mNumPlacax = NULL)
	{
		 
		$mSplit = str_split($mNumPlacax, 3);
		$mNumPlacax = join("-", $mSplit);
		return $mNumPlacax;
	}


	function getToken(   )
	{
		$this -> cProcesox = '1';
		$mQuery = "SELECT MAX(cod_consec), num_tokenx, fec_regist 
		             FROM ".BASE_DATOS5.".t_genera_token 
		            WHERE fec_regist = '".date("Y-m-d")."'  AND cod_operad = '900084087'";
		$this -> cConexion -> ExecuteCons( $mQuery  );
		$tokenx = $this -> cConexion -> RetMatrix( "a" );
 

		if( !trim($tokenx[0]['num_tokenx']) )
		{
			$mIdVehicle = fasle;
			$mAcVehicle = fasle;
		    $oSoapClient = new soapclient( 'https://api.fm-web.us/webservices/CoreWebSvc/CoreWS.asmx?WSDL', array( 'trace' => 1, 
                                                                                                             'encoding'=>'ISO-8859-1' ) );
    		# Parametros de logueo para el token
		    $mParams = new stdClass();
		    $mParams -> UserName = $this -> cUsariSys;
		    $mParams -> Password = $this -> cClaveSys;
		    $mParams -> ApplicationID = NULl;
		     
		    $result = $oSoapClient -> Login( $mParams );
		    

		    echo "<pre>__getLastRequest<br>"; print_r( htmlspecialchars( $oSoapClient -> __getLastRequest() ) ); echo "</pre>";
		    echo "<pre>__getLastResponse<br>"; print_r( htmlspecialchars( $oSoapClient -> __getLastResponse() ) ); echo "</pre>";
		    # Error de Logueo  
		    if( !$result -> LoginResult -> Token )		  
		    	return false;
		    
		    # Captura Token
		    $mToken = $result -> LoginResult -> Token;
		  
 			# Guarda el Token
			$mQuery = "SELECT MAX(cod_consec) 
			           FROM ".BASE_DATOS5.".t_genera_token";
			$this -> cConexion -> ExecuteCons( $mQuery  );
			$max = $this -> cConexion -> RetMatrix( "i" );
			$max = (int)$max[0][0] + 1;

			$mQuery = "INSERT INTO ".BASE_DATOS5.".t_genera_token ( cod_consec, num_tokenx, fec_regist , cod_operad) 
			          VALUES ( '".$max."', '".$mToken."', DATE(NOW()), '900084087' ) ";
			$this -> cConexion -> ExecuteCons( $mQuery  );
		  
		  return $mToken;
		}
		else
		{
		  return $tokenx[0]['num_tokenx'];
		}

	}


	function getListIds($mToken, $mPlacax)
	{
		#$mPlacax = 'TGK-777';
		$oSoapClient2 = new soapclient( 'https://api.fm-web.us/webservices/AssetDataWebSvc/VehicleProcessesWS.asmx?WSDL', array('trace'   => 1,                                                                                                                           
                                                                                                                              'encoding'=>'ISO-8859-1',
                                                                                                                              'soap_version' => SOAP_1_2 )   );  
	    $objVar = new SoapVar('<TokenHeader xmlns="http://www.omnibridge.com/SDKWebServices/AssetData"><Token>'.$mToken.'</Token></TokenHeader>', XSD_ANYXML, null, null, null);
	   
	    $mResult = $oSoapClient2 ->  __soapCall("GetVehiclesList",array(), null, new SoapHeader('http://www.omnibridge.com/SDKWebServices/AssetData', 'TokenHeader', $objVar ));

	  
	    $mListIDs = $mResult -> GetVehiclesListResult -> Vehicle;
	    for($i = 0; $i <= sizeof($mListIDs); $i++)
	    {
	        /*if( $mListIDs[$i] -> RegistrationNumber == $mPlacax )
	        {
	            $mIdVehicle = $mListIDs[$i] -> ID;
	            $mAcVehicle = $mListIDs[$i] -> Active;
	        }*/
	        //if( $mListIDs[$i] -> RegistrationNumber == str_replace("-", "", $mPlacax) )
	        if( $mListIDs[$i] -> RegistrationNumber == $mPlacax )
	        {	
	            $mIdVehicle = $mListIDs[$i] -> ID;
	            $mAcVehicle = $mListIDs[$i] -> Active;
	        }
	    }

	    # No encuentra la Placa
	    if( !$mIdVehicle )
	    {
	        $this -> cErrowsdl = "Vehiculo (Placa ".$mPlacax.") No Encontrado En El Systema Syscaf";
	        return false;
	    }

	    # Encuentra la placa pero está Inactiva
	    if( !$mAcVehicle )
	    {	         
	        $this -> cErrowsdl = "Vehiculo (Placa ".$mPlacax.") No Activo En El Sistema de Syscaf";
	        return false;

	    }
 
	    return $mIdVehicle;
	}

	function getDataGpsVehicle( $mToken, $mIdVehicle)
	{
		$oSoapClient3 = new soapclient("https://api.fm-web.us/webservices/PositioningWebSvc/PositioningWS.asmx?WSDL", array( 'trace' => 1, 
                                                                                                                     'encoding'=>'ISO-8859-1',
                                                                                                                     'soap_version' => SOAP_1_2)
                                                                                                                      );
	    $mHeader  = new SoapVar('<TokenHeader xmlns="http://www.omnibridge.com/SDKWebServices/Positioning"><Token>'.$mToken.'</Token></TokenHeader>', XSD_ANYXML, null, null, null);
	    $oSoapClient3->__setSoapHeaders(new SoapHeader('http://www.omnibridge.com/SDKWebServices/Positioning', 'TokenHeader', $mHeader ));

	    $mParams   = new SoapVar( '<GetLatestPositionPerVehicle xmlns="http://www.omnibridge.com/SDKWebServices/Positioning"><SpecificVehicleIDs><short>'.$mIdVehicle.'</short></SpecificVehicleIDs></GetLatestPositionPerVehicle>',  XSD_ANYXML, NULL,NULL,NULL);
	    $mReturn = $oSoapClient3 -> GetLatestPositionPerVehicle( new soapParam($mParams, "nelson"));
	    return $mReturn;
	}

	function getDataCoordenada( $mToken, $mLatitud, $mLongitud, $mIDGpsPosition)
	{
		$oSoapClient4 = new soapclient("https://api.fm-web.us/webservices/PositioningWebSvc/PositioningWS.asmx?WSDL", array( 'trace' => 1, 
                                                                                                                     'encoding'=>'ISO-8859-1',
                                                                                                                     'soap_version' => SOAP_1_2)
                                                                                                                      );
	    $mHeader  = new SoapVar('<ns1:TokenHeader xmlns="http://www.omnibridge.com/SDKWebServices/Positioning"><ns1:Token>'.$mToken.'</ns1:Token></ns1:TokenHeader>', XSD_ANYXML, null, null, null);
	    $oSoapClient4->__setSoapHeaders(new SoapHeader('http://www.omnibridge.com/SDKWebServices/Positioning', 'TokenHeader', $mHeader ));

	    /*$mParams   = new SoapVar( '<GetReverseGeoForCoordinates xmlns="http://www.omnibridge.com/SDKWebServices/Positioning"><Coordinates><Coordinate Latitude="'.$mLatitud.'" Longitude="'.$mLongitud.'"/></Coordinates></GetReverseGeoForCoordinates>',  XSD_ANYXML, NULL,NULL,NULL);
	    $mReturn = $oSoapClient4 -> GetReverseGeoForCoordinates( new soapParam($mParams, "nelson")); */

	    $mParams   = new SoapVar( '<ns1:GetReverseGeoForGPSPositions><ns1:GPSIDs><ns1:long>'.$mIDGpsPosition.'</ns1:long></ns1:GPSIDs></ns1:GetReverseGeoForGPSPositions>',  XSD_ANYXML, NULL,NULL,NULL);
	    $mReturn = $oSoapClient4 -> GetReverseGeoForGPSPositions( new soapParam($mParams, "nelson"));
	    return $mReturn;
	}
}





$mStart = new SyscafCorona( );

?>
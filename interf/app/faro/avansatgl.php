<?php
//ini_set('display_errors', false);
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("memory_limit", "4086MB");
	//------------------------------
	require_once("faro.php" );
	require_once('lib/nusoap.php'); 
	#include("lib/constantes.inc");
	//------------------------------

	//------------------------------
	$server = new nusoap_server;
	$namespace = "urn:faro_base";
	$server -> debug_flag = false;
	$server -> configureWSDL( 'faro', $namespace ); 
	$server -> wsdl -> schemaTargetNamespace = $namespace; 
	//------------------------------

 


	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//complejos ------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------

		$server -> wsdl -> addComplexType( 'DatRespon', 'complexType', 'struct', 'all', '', 
											array( 'cod_respon' => array( 'name' => 'cod_respon', 'type' => 'xsd:string' ), 
												   'msg_respon' => array( 'name' => 'msg_respon', 'type' => 'xsd:string' ) 
										 ));	

 	// SETSEGUIM ------------------------------------------------------------------------------------------------------------------------------------
		$server -> wsdl -> addComplexType( 'arrayControsSeguim', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), 
											array( array( 'ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:ControSeguim[]' ) ), 'tns:ControSeguim');

		$server -> wsdl -> addComplexType( 'ControSeguim', 'complexType', 'struct', 'all', '', 
											array( 'cod_contro' => array( 'name' => 'cod_contro', 'type' => 'xsd:string' ), 
												   'val_duraci' => array( 'name' => 'val_duraci', 'type' => 'xsd:string' ),
												   'ind_virtua' => array( 'name' => 'ind_virtua', 'type' => 'xsd:string' )
										 ));

		$server -> wsdl -> addComplexType( 'arrayDataAgencia', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), 
											array( array( 'ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:DataAgencia[]' ) ), 'tns:DataAgencia');

		$server -> wsdl -> addComplexType( 'DataAgencia', 'complexType', 'struct', 'all', '', 
											array( 'cod_agenci' => array( 'name' => 'cod_agenci', 'type' => 'xsd:string' ), 
												   'nom_agenci' => array( 'name' => 'nom_agenci', 'type' => 'xsd:string' ),
												   'cod_ciudad' => array( 'name' => 'cod_ciudad', 'type' => 'xsd:string' ),
												   'dir_agenci' => array( 'name' => 'dir_agenci', 'type' => 'xsd:string' ),
												   'tel_agenci' => array( 'name' => 'tel_agenci', 'type' => 'xsd:string' ),
												   'con_agenci' => array( 'name' => 'con_agenci', 'type' => 'xsd:string' ),
												   'dir_emailx' => array( 'name' => 'dir_emailx', 'type' => 'xsd:string' ),
												   'num_faxxxx' => array( 'name' => 'num_faxxxx', 'type' => 'xsd:string' ),
										 ));

		$server -> wsdl -> addComplexType( 'arrayDataGps', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), 
											array( array( 'ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:DataGps[]' ) ), 'tns:DataGps');

		$server -> wsdl -> addComplexType( 'DataGps', 'complexType', 'struct', 'all', '', 
											array( 'cod_opegps' => array( 'name' => 'cod_opegps', 'type' => 'xsd:string' ), 
												   'nom_usrgps' => array( 'name' => 'nom_usrgps', 'type' => 'xsd:string' ),
												   'clv_usrgps' => array( 'name' => 'clv_usrgps', 'type' => 'xsd:string' ),
												   'idx_gpsxxx' => array( 'name' => 'idx_gpsxxx', 'type' => 'xsd:string' )  
										 ));	

		$server -> wsdl -> addComplexType( 'arrayDataRem', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), 
											array( array( 'ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:DataRem[]' ) ), 'tns:DataRem');

		$server -> wsdl -> addComplexType( 'DataRem', 'complexType', 'struct', 'all', '', 
											array( 'cod_remesa' => array( 'name' => 'cod_remesa', 'type' => 'xsd:string' ), 
												   'pes_cargax' => array( 'name' => 'pes_cargax', 'type' => 'xsd:string' ),
												   'vol_cargax' => array( 'name' => 'vol_cargax', 'type' => 'xsd:string' ),
												   'nom_empaqu' => array( 'name' => 'nom_empaqu', 'type' => 'xsd:string' ),  
												   'abr_mercan' => array( 'name' => 'abr_mercan', 'type' => 'xsd:string' ),  
												   'abr_tercer' => array( 'name' => 'abr_tercer', 'type' => 'xsd:string' ),  
												   'nom_remite' => array( 'name' => 'nom_remite', 'type' => 'xsd:string' ),  
												   'nom_destin' => array( 'name' => 'nom_destin', 'type' => 'xsd:string' ),  
												   'fec_estent' => array( 'name' => 'fec_estent', 'type' => 'xsd:string' ),  
												   'fec_lledes' => array( 'name' => 'fec_lledes', 'type' => 'xsd:string' ),  
												   'fec_saldes' => array( 'name' => 'fec_saldes', 'type' => 'xsd:string' ),  
												   'fec_ldesti' => array( 'name' => 'fec_ldesti', 'type' => 'xsd:string' ),  
												   'dir_emailx' => array( 'name' => 'dir_emailx', 'type' => 'xsd:string' ),  
												   'cod_client' => array( 'name' => 'cod_client', 'type' => 'xsd:string' ),  
												   'dir_destin' => array( 'name' => 'dir_destin', 'type' => 'xsd:string' ),  
												   'ciu_destin' => array( 'name' => 'ciu_destin', 'type' => 'xsd:string' ),  
										 ));


		$server -> wsdl -> addComplexType( 'DataGps2', 'complexType', 'struct', 'all', '', 
											array( 'nom_operad' => array( 'name' => 'nom_operad', 'type' => 'xsd:string' ), 
												   'nom_usrgps' => array( 'name' => 'nom_usrgps', 'type' => 'xsd:string' ),
												   'clv_usrgps' => array( 'name' => 'clv_usrgps', 'type' => 'xsd:string' ),
												   'idx_gpsxxx' => array( 'name' => 'idx_gpsxxx', 'type' => 'xsd:string' ),  
												   'gps_urlxxx' => array( 'name' => 'gps_urlxxx', 'type' => 'xsd:string' ),  
										 ));		

		$server -> wsdl -> addComplexType( 'BinFotcon', 'complexType', 'struct', 'all', '', 
											array( 'bin_fotcon' => array( 'name' => 'bin_fotcon', 'type' => 'tns:DescripcionFoto' ), 
										 ));		

 
 
		$server -> wsdl -> addComplexType( 'BinFotveh', 'complexType', 'struct', 'all', '', 
											array( 'bin_fotfre' => array( 'name' => 'bin_fotfre', 'type' => 'tns:DescripcionFoto' ), 
												   'bin_fotizq' => array( 'name' => 'bin_fotizq', 'type' => 'tns:DescripcionFoto' ),
												   'bin_fotder' => array( 'name' => 'bin_fotder', 'type' => 'tns:DescripcionFoto' ),
												   'bin_fotpos' => array( 'name' => 'bin_fotpos', 'type' => 'tns:DescripcionFoto' ),  
										 ));		


		$server -> wsdl -> addComplexType( 'DescripcionFoto', 'complexType', 'struct', 'all', '', 
											array( 'fot_namexx' => array( 'name' => 'fot_namexx', 'type' => 'xsd:string' ), 
												   'fot_typexx' => array( 'name' => 'fot_typexx', 'type' => 'xsd:string' ),
												   'fot_sizexx' => array( 'name' => 'fot_sizexx', 'type' => 'xsd:string' ),
												   'fot_binary' => array( 'name' => 'fot_binary', 'type' => 'xsd:string' ),  
										 ));
 	// -------------------------------------------------------------------------------------------------------------------------------------------


#metodo setSegim
 
 
		$server -> register(	'setSeguim', 
						  	array( 	
						  			'nom_usuari' => 'xsd:string', 
									'pwd_clavex' => 'xsd:string',
								   	'cod_tranps' => 'xsd:string',
								   	'cod_manifi' => 'xsd:string',
								   	'dat_fechax' => 'xsd:string',
								   	'cod_ciuori' => 'xsd:string',
								   	'cod_ciudes' => 'xsd:string',
								   	'cod_placax' => 'xsd:string',
								   	'num_modelo' => 'xsd:string', 
								   	'cod_marcax' => 'xsd:string', 
								   	'cod_lineax' => 'xsd:string', 
								   	'cod_colorx' => 'xsd:string', 
								   	'cod_conduc' => 'xsd:string', 
								   	'nom_conduc' => 'xsd:string',
								   	'ciu_conduc' => 'xsd:string',
								   	'tel_conduc' => 'xsd:string',
								   	'mov_conduc' => 'xsd:string',
								   	'obs_coment' => 'xsd:string',
								   	'cod_rutaxx' => 'xsd:string',
								   	'nom_rutaxx' => 'tns:string',
								   	'ind_naturb' => 'xsd:string',
								   	'num_config' => 'xsd:string',
								   	'cod_carroc' => 'xsd:string',
								   	'num_chasis' => 'xsd:string',
								   	'num_motorx' => 'xsd:string',
								   	'num_soatxx' => 'xsd:string',
								   	'dat_vigsoa' => 'xsd:string',
								   	'nom_ciasoa' => 'xsd:string',
								   	'num_tarpro' => 'xsd:string',
								   	'num_trayle' => 'xsd:string',
								   	'cat_licenc' => 'xsd:string',
								   	'dir_conduc' => 'xsd:string',
								   	'cod_poseed' => 'xsd:string',
								   	'nom_poseed' => 'xsd:string',
								   	'ciu_poseed' => 'xsd:string',
								   	'dir_poseed' => 'xsd:string',
								   	'cod_agedes' => 'xsd:string',
								   	'cod_contrs' => 'tns:arrayControsSeguim',
								   	'cod_agenci' => 'tns:arrayDataAgencia',
								   	'cod_operad' => 'xsd:string',
								   	'cod_gpsxxx' => 'tns:arrayDataGps',
								   	'cod_remesa' => 'tns:arrayDataRem',
								   	'bin_huella' => 'xsd:string',
								   	'num_viajex' => 'xsd:string',
								   	'dat_gps2xx' => 'tns:DataGps2',
								   	'nom_aplica' => 'xsd:string',
								   	'fot_conduc' => 'tns:BinFotcon',
								   	'fot_vehicu' => 'tns:BinFotveh',
								  ),
						  	array(  'dat_respon' => 'tns:DatRespon' ), 
							$namespace, $namespace."#setSeguim", "rpc", "encoded", "Metodo para crear un nuevo despacho" ); 	
#metodo setLlegada
 
		$server -> register(	'setLlegada', 
						  	array( 	'nom_usuari' => 'xsd:string', 
									'pwd_clavex' => 'xsd:string',
									'cod_tranps' => 'xsd:string',
								    'cod_manifi' => 'xsd:string',
								    'fec_llegad' => 'xsd:string',
								    'obs_llegad' => 'xsd:string',
								    'num_placax' => 'xsd:string',
								  ),
						  	array(  'dat_respon' => 'tns:DatRespon' ), 
							$namespace, $namespace."#setLlegada", "rpc", "encoded", "Metodo para generar llegada a un despacho" ); 	


#metodo setAnulad
		$server -> register(	'setAnulad', 
						  	array( 	'nom_usuari' => 'xsd:string', 
									'pwd_clavex' => 'xsd:string',
									'cod_tranps' => 'xsd:string',
								    'cod_manifi' => 'xsd:string',
								    'num_placax' => 'xsd:string',
								    'fec_anulad' => 'xsd:string',
								    'obs_anulad' => 'xsd:string',
								  ),
						  	array(  'dat_respon' => 'tns:DatRespon' ), 
							$namespace, $namespace."#setAnulad", "rpc", "encoded", "Metodo para generar anular un despacho" ); 	

	 
// -----------------------------------------------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------------------------------------

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : ''; 
	$server -> service( $HTTP_RAW_POST_DATA );
?>
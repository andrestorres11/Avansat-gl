<?php

//include_once '../vendor/econea/nusoap/src/nusoap.php';
//include_once '../lib/corona.fn.php';

// con php nativo de php >= 5
  ini_set( "soap.wsdl_cache_enabled", "0" );
try 
{
	$mSoap = new SoapClient( 'https://avansatgl.intrared.net/ap/interf/app/faro/faroNETDesarrollo.php?wsdl', ['exception' => true, 'trace'=> 1] );
	//$mSoap = new SoapClient( 'https://dev.intrared.net:8083/ap/interf/app/faro/faro.php?wsdl', ['exception' => true, 'trace'=> 1] );
	//$mSoap = new SoapClient( 'https://dev.intrared.net:8083/ap/interf/app/faro/faroNET.php?wsdl', ['exception' => true, 'trace'=> 1] );
	//$mSoap = new SoapClient( 'https://dev.intrared.net:8083/ap/interf/app/faro/avansatgl.php?wsdl', ['exception' => true, 'trace'=> 1] );
 
	$data = [
				'nom_usuari' => 'InterfJwExclusive',
				'pwd_clavex' => 'Jw3xClus1Ve.2022$',
				'cod_tranps' => '805027046',
				'cod_manifi' => '1122334666',
				'dat_fechax' => '2020-12-21 11:45:11',
				'cod_ciuori' => '19142000',
				'cod_ciudes' => '17001000',
				'cod_placax' => 'ZNL190',
				'num_modelo' => '2013',
				'cod_marcax' => 'FV',
				'cod_lineax' => '999',
				'cod_colorx' => '735',
				'cod_conduc' => '94326270',
				'nom_conduc' => 'JORGE CABRERA',
				'ciu_conduc' => '76248000',
				'tel_conduc' => '3226158555',
				'mov_conduc' => '3215434840',
				'obs_coment' => "", 
				'cod_rutaxx' => '0', 
				'nom_rutaxx' => 'CALOTO - MANIZALES', 
				'ind_naturb' => '1', 
				'num_config' => 50, 
				'cod_carroc' => '0', 
				'num_chasis' => 'LVBV4PDB2DE002555',
				'num_motorx' => 'HC527609XA23',
				'num_soatxx' => '14305300002680',
				'dat_vigsoa' => '2021-01-14',
				'nom_ciasoa' => 'SEGUROS DEL ESTADO S.A. SEGURO',
				'num_tarpro' => '1004916246',
				'num_trayle' => '',
				'cat_licenc' => '5',
				'dir_conduc' => 'CR 5 3 79',
				'cod_poseed' => '94326270', 
				'nom_poseed' => 'JORGE CABRERA', 
				'ciu_poseed' => '76248000', 
				'dir_poseed' => ' CR 5 3 79', 
				'cod_agedes' => 344, 
				'cod_contrs' => NULL,
				'cod_agenci' => NULL,
				'cod_operad' => NULL,
				'cod_gpsxxx' => NULL,
				'cod_remesa' => [ 0 => [
											"cod_remesa" => "0101287183",
											"pes_cargax" => "4000",
											"vol_cargax" => "0",
											"nom_empaqu" => "BULTO",
											"abr_mercan" => "PRODUCTOS",
											"nom_remite" => "PRODUCTOS YUPI SAS",
											"nom_destin" => "PRODUCTOS YUPI SAS",
											"fec_estent" => "2020-12-21 11:45:12",
											"fec_saldes" => "2020-12-21 11:45:12",
											"fec_ldesti" => "2020-12-21 11:45:12",
											"dir_emailx" => "bernardom@yupi.com.co",
											"cod_client" => "890307885",
											"dir_destin" => "CR 40 14 167 URB ACOPI",
											"ciu_destin" => "17001000",
										],
									1 => [
											"cod_remesa" => "01012871",
											"pes_cargax" => "4000",
											"vol_cargax" => "0",
											"nom_empaqu" => "BULTO",
											"abr_mercan" => "PRODUCTOS",
											"nom_remite" => "PRODUCTOS YUPI SAS",
											"nom_destin" => "PRODUCTOS YUPI SAS",
											"fec_estent" => "2020-12-21 11:45:12",
											"fec_saldes" => "2020-12-21 11:45:12",
											"fec_ldesti" => "2020-12-21 11:45:12",
											"dir_emailx" => "bernardom@yupi.com.co",
											"cod_client" => "890307885",
											"dir_destin" => "CR 40 14 167 URB ACOPI",
											"ciu_destin" => "17001000",
										],
								],
				'bin_huella' => NULL,
				'num_viajex' => NULL,
				'dat_gps2xx' => [
									'nom_operad' => 'SATRACK',
									'nom_usrgps' => 'USUARIO GPS',
									'clv_usrgps' => 'CLAVE_GPS',
									'idx_gpsxxx' => NULL,
									'gps_urlxxx' => 'https://www.satrack.com.co/',
								],
				'nom_aplica' => 'satt_faro',
				'fot_conduc' => NULL,
				'fot_vehicu' => NULL
			];
 echo "<pre>"; print_r($data); echo "</pre>";   

	$mRespon = $mSoap -> __soapCall('setSeguim', $data );


	echo "<pre> CoronaWS.__getLastRequest: <br>"; print_r( formatXmlString( htmlspecialchars($mSoap -> __getLastRequest())) ); echo "</pre>";
	echo "<pre> CoronaWS.setSeguimResponse: "; print_r( $mRespon ); echo "</pre>";


	echo "<pre> CoronaWS.__getLastResponse: "; print_r( htmlspecialchars($mSoap -> __getLastResponse()) ); echo "</pre>";



} 
catch (Exception $e) 
{
	 
	echo "<pre> Exception.__getLastRequest: <br>"; print_r(   htmlspecialchars($mSoap -> __getLastRequest() ) ); echo "</pre>";
	echo "<pre>Exception: "; print_r( $e ); echo "</pre>";
}

function formatXmlString($xml) {

  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

  // now indent the tags
  $token      = strtok($xml, "\n");
  $result     = ''; // holds formatted version as it is built
  $pad        = 0; // initial indent
  $matches    = array(); // returns from preg_matches()

  // scan each line and adjust indent based on opening/closing tags
  while ($token !== false) :

    // test for the various tag states

    // 1. open and closing tags on same line - no change
    if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
      $indent=0;
    // 2. closing tag - outdent now
    elseif (preg_match('/^<\/\w/', $token, $matches)) :
      $pad--;
    // 3. opening tag - don't pad this one, only subsequent tags
    elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
      $indent=1;
    // 4. no indentation needed
    else :
      $indent = 0;
    endif;

    // pad the line with the required number of leading spaces
    $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
    $result .= $line . "\n"; // add to the cumulative result, with linefeed
    $token   = strtok("\n"); // get the next token
    $pad    += $indent; // update the pad size for subsequent lines
  endwhile;

  return $result;
}

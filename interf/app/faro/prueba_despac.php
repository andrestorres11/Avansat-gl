<?php
  ini_set("soap.wsdl_cache_enabled", "0"); //disabling WSDL cache

  //echo "Hola Mundo<br>";
  try
  {
    
    /*
    $fNomUsuari = "InterfGmtToo";
    $fPwdClavex = "fnhs_yt3$njkg";
    $fCodTranps = "830101959";
    $fCodManifi = "401336";    
    $fDatFechax = "2019-03-11 12:06";
    $fCodCiuori = "11001000";
    $fCodCiudes = "5001000";
    $fCodPlacax = "UPS492";    
    $fNumModelo = "2008";
    $fCodMarcax = "CH";
    $fCodLineax = "999";
    $fCodColorx = "8";    
    $fCodConduc = "79851869";
    $fNomConduc = "GARZON PIRAFAN CARLOS ALFONSO";
    $fCiuConduc = "11001000";
    $fTelConduc = "3173316742";    
    $fMovConduc = "3173316742";
    $fObsComent = NULL;
    $fCodRutaxx = "0";
    $fNomRutaxx = NULL;    
    $fIndNaturb = "1";
    $fNumConfig = "2";
    $fCodCarroc = "0";
    $fNumChasis = NULL;    
    $fNumMotorx = NULL;
    $fNumSoatxx = NULL;
    $fDatVigsoa = NULL;
    $fNomCiasoa = NULL;    
    $fNumTarpro = NULL;
    $fNumTrayle = NULL;
    $fCatLicenc = NULL;
    $fDirConduc = NULL;    
    $fCodPoseed = "79851869";
    $fNomPoseed = "GARZON PIRAFAN CARLOS ALFONSO";
    $fCiuPoseed = "11001000";
    $fDirPoseed = NULL;    
    $mCodAgedes = NULL;
    $mCodContrs = NULL;
    $mCodAgenci = NULL;
    $fCodoperad = "0";    
    $mCodGpsxxx = (object)array
                    (
                        'cod_opegps' => "8300596993",
                        'nom_usrgps' => "cgrazonp",
                        'clv_usrgps' => "c81110",
                        'idx_gpsxxx' => ""
                    );

    $mCodRemesa = NULL;
    $fBinHuella = NULL;
    $fNumViajex = NULL;
    include_once( "faro.php" ); //Funciones generales.


    $mRespon = setSeguim( $fNomUsuari, $fPwdClavex, $fCodTranps, $fCodManifi,
                    $fDatFechax, $fCodCiuori, $fCodCiudes, $fCodPlacax, 
                    $fNumModelo, $fCodMarcax, $fCodLineax, $fCodColorx, 
                    $fCodConduc, $fNomConduc, $fCiuConduc, $fTelConduc, 
                    $fMovConduc, $fObsComent, $fCodRutaxx, $fNomRutaxx, 
                    $fIndNaturb, $fNumConfig ,$fCodCarroc, $fNumChasis, 
                    $fNumMotorx, $fNumSoatxx, $fDatVigsoa, $fNomCiasoa, 
                    $fNumTarpro, $fNumTrayle, $fCatLicenc, $fDirConduc, 
                    $fCodPoseed, $fNomPoseed, $fCiuPoseed, $fDirPoseed,
                    $mCodAgedes, $mCodContrs, $mCodAgenci, $fCodoperad,
                    $mCodGpsxxx, $mCodRemesa, $fBinHuella, $fNumViajex );
    echo "<pre>setNovedadNC Respon: "; print_r($mRespon); echo "</pre>";

    */
    $mParam = [];
    $mParam["nom_usuari"] = "InterfGmtToo";
    $mParam["pwd_clavex"] = "fnhs_yt3$njkg"; // Anterior
    $mParam["pwd_clavex"] = "1;XlYf0GeF"; // nuevo
    $mParam["cod_tranps"] = "830101959";
    $mParam["cod_manifi"] = "401336";    
    $mParam["dat_fechax"] = "2019-03-11 12:06";
    $mParam["cod_ciuori"] = "11001000";
    $mParam["cod_ciudes"] = "5001000";
    $mParam["cod_placax"] = "UPS492";    
    $mParam["num_modelo"] = "2008";
    $mParam["cod_marcax"] = "CH";
    $mParam["cod_lineax"] = "999";
    $mParam["cod_colorx"] = "8";    
    $mParam["cod_conduc"] = "79851869";
    $mParam["nom_conduc"] = "GARZON PIRAFAN CARLOS ALFONSO";
    $mParam["ciu_conduc"] = "11001000";
    $mParam["tel_conduc"] = "3173316742";    
    $mParam["mov_conduc"] = "3173316742";
    $mParam["obs_coment"] = NULL;
    $mParam["cod_rutaxx"] = "0";
    $mParam["nom_rutaxx"] = NULL;    
    $mParam["ind_naturb"] = "1";
    $mParam["num_config"] = "2";
    $mParam["cod_carroc"] = "0";
    $mParam["num_chasis"] = NULL;    
    $mParam["num_motorx"] = NULL;
    $mParam["num_soatxx"] = NULL;
    $mParam["dat_vigsoa"] = NULL;
    $mParam["nom_ciasoa"] = NULL;    
    $mParam["num_tarpro"] = NULL;
    $mParam["num_trayle"] = NULL;
    $mParam["cat_licenc"] = NULL;
    $mParam["dir_conduc"] = NULL;    
    $mParam["cod_poseed"] = "79851869";
    $mParam["nom_poseed"] = "GARZON PIRAFAN CARLOS ALFONSO";
    $mParam["ciu_poseed"] = "11001000";
    $mParam["dir_poseed"] = NULL;    
    $mParam["cod_agedes"] = NULL;
    $mParam["cod_contrs"] = NULL;
    $mParam["cod_agenci"] = NULL;
    $mParam["cod_operad"] = "0";    
    $mParam["cod_gpsxxx"] = [
                              0 =>[
                                    'cod_opegps' => "8300596993",
                                    'nom_usrgps' => "cgrazonp",
                                    'clv_usrgps' => "c81110",
                                    'idx_gpsxxx' => ""                                
                                 ]
                            ] ;
    $mParam = [];
    $mParam["nom_usuari"] = "intertercer";
    $mParam["pwd_clavex"] = "7f64rj6.*";
    $mParam["cod_tranps"] = "900437294";
    $mParam["cod_manifi"] = "13668";
    $mParam["dat_fechax"] = "2019-01-10 12:00";
    $mParam["cod_ciuori"] = "11001000";
    $mParam["cod_ciudes"] = "76001000";
    $mParam["cod_placax"] = "WLL022";
    $mParam["num_modelo"] = "2015";
    $mParam["cod_marcax"] = "375";
    $mParam["cod_lineax"] = "150";
    $mParam["cod_colorx"] = "12250";
    $mParam["cod_conduc"] = "80015435";
    $mParam["nom_conduc"] = "DIEGO ALBERTO ESCOBAR RIANO";
    $mParam["ciu_conduc"] = "11001000";
    $mParam["tel_conduc"] = "3148758083";
    $mParam["mov_conduc"] = "3148758083";
    $mParam["obs_coment"] = NULL;
    $mParam["cod_rutaxx"] = NULL;
    $mParam["nom_rutaxx"] = NULL;    
    $mParam["ind_naturb"] = "1";
    $mParam["num_config"] = "2";
    $mParam["cod_carroc"] = '0';
    $mParam["num_chasis"] = 'LVBV8JE62FE002154';    
    $mParam["num_motorx"] = 'B406014431';
    $mParam["num_soatxx"] = '13663000000830';
    $mParam["dat_vigsoa"] = '2020-04-02';
    $mParam["nom_ciasoa"] = 'SEGUROS DEL ESTADO S.A.';    
    $mParam["num_tarpro"] = '10008878844';
    $mParam["num_trayle"] = NULL;
    $mParam["cat_licenc"] = 'C2';
    $mParam["dir_conduc"] = 'CR 119 A 17 F 45 P 2';    
    $mParam["cod_poseed"] = "17184239";
    $mParam["nom_poseed"] = "LUIS ALBERTO ESCOBAR GIRALDO";
    $mParam["ciu_poseed"] = "11001000";
    $mParam["dir_poseed"] = "CR 119 A 17 F 45 P 2";
    $mParam["cod_agedes"] = "118";
    $mParam["cod_contrs"] = NULL;
    $mParam["cod_agenci"] = NULL;
    $mParam["cod_operad"] = "0";    
    $mParam["cod_gpsxxx"] = [
                              0 =>[
                                    'cod_opegps' => "900040838",
                                    'nom_usrgps' => "user",
                                    'clv_usrgps' => "pass01",
                                    'idx_gpsxxx' => ""                                
                                 ]
                            ] ;
    $mParam["cod_remesa"] = [
                                [
                                    "cod_remesa" => "13668",
                                    "pes_cargax" => "1000",
                                    "vol_cargax" => NULL,
                                    "nom_empaqu" => "cajas",
                                    "abr_mercan" => "aseo",
                                    "abr_tercer" => "maslogistica",
                                    "nom_remite" => "quala",
                                    "nom_destin" => "sandra",
                                    "fec_estent" => "2020-01-18 12:00",
                                    "fec_lledes" => NULL,
                                    "fec_saldes" => NULL,
                                    "fec_ldesti" => NULL,
                                    "dir_emailx" => NULL,
                                    "cod_client" => NULL,
                                    "dir_destin" => NULL,
                                    "ciu_destin" => NULL,
                                ]
                            ];
    $mParam['bin_huella'] = NULL;
    $mParam['num_viajex'] = NULL;
    $mParam['dat_gps2xx'] = [
                                "nom_operad" => NULL,
                                "nom_usrgps" => NULL,
                                "clv_usrgps" => NULL,
                                "idx_gpsxxx" => NULL,
                            ];

                             
    echo "<pre>setSeguim Respon:<br> "; print_r($mParam); echo "</pre>";



    $mSoap = new SoapClient( 'https://avansatgl.intrared.net/ap/interf/app/faro/faro.php?wsdl', ['expection' => true, 'trace'=> 1] );

    $mRespon = $mSoap -> __soapCall('setSeguim', $mParam);

    echo "<pre>setSeguim XML REQUEST:<br> "; print_r( htmlspecialchars( $mSoap -> __getLastRequest() ) ) ; echo "</pre>";
    echo "<pre>setSeguim Respon:<br> "; print_r($mRespon); echo "</pre>";




  }
  catch( SoapFault $e )
  {
   echo 'Ocurrio un error: '.$e -> getMessage();
  }

  echo "<br>Chao Mundo";
?>

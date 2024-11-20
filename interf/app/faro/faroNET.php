<?php
 /************************************************************************
  * @file despac.php                                                     *
  * @brief Servidor de Servicios (WebService server).                    *
  * @version 0.1                                                         *
  * @date 19 de Marzo de 2010                                          *
  * @author Carlos A. Mock-kow M.                                        *
  * @bug usa funciones que desapareceran en php6                         *
  ************************************************************************/

  //turn off the wsdl cache
  ini_set( "soap.wsdl_cache_enabled", "0" );

  //include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.  
  include_once( "/var/www/html/ap/interf/app/faro/protoc.class.inc" );     //Constantes propias.

 /************************************************************************
  * Funcion Inserta despacho                                             *
  * @fn setSeguim                                                        *
  * @brief Inserta un despacho en Sat trafico.                           *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Numero del manifiesto.                    *
  * @param $fDatFechax: date   Fecha del despacho.                       *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodPlacax: string Matricula del vehiculo.                   *
  * @param $fNumModelo: int    Modelo del vehiculo.                      *
  * @param $fCodMarcax: string Codigo de la marca del vehiculo.          *
  * @param $fCodColorx: string Codigo del color del vehiculo.            *
  * @param $fCodConduc: string Documento del conductor del vehiculo.     *
  * @param $fNomConduc: string Nombre del conductor del vehiculo.        *
  * @param $fCiuConduc: string Codigo dane de la ciudad del conductor.   *
  * @param $fTelConduc: string Numero de telefono del conductor.         *
  * @param $fMovConduc: string Numero de telefono movil del conductor.   *
  * @param $fObsComent: string Obsevaciones.                             *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fIndNaturb: bool   Ind nacional urbano.                      *
  * @param $fNumConfig: str¡ng Configuracion del vehiculo.               *
  * @param $fCodCarroc: string Codigo de la carroceria del vehiculo.     *
  * @param $fNumChasis: int    Numero del chasis del vehiculo.           *
  * @param $fNumMotorx: str¡ng Serial del motor vehiculo.                *
  * @param $fNumSoatxx: str¡ng Seguro obligatorio.                       *
  * @param $fDatVigsoa: date   Fecha vigencia Soat.                      *
  * @param $fNomCiasoa: string Nombre compania de seguro Soat.           *
  * @param $fNumTarpro: str¡ng Numero tarjeta de proipedad.              *
  * @param $fNumTrayle: str¡ng Matricula remolque del vehiculo.          *
  * @param $fCatLicenc: string Numero categoria licencia del conductor.  *
  * @param $fDirConduc: string Direccion del conductor del vehiculo.     *
  * @param $fCodPoseed: string Documento del poseedor del vehiculo.      *
  * @param $fNomPoseed: string Nombre del poseedor del vehiculo.         *
  * @param $fCiuPoseed: string Codigo dane de la ciudad del poseedor.    *
  * @param $fDirPoseed: string Direccion del poseedor.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
 function setSeguim( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL,
                      $fDatFechax = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fCodPlacax = NULL, 
                      $fNumModelo = NULL, $fCodMarcax = NULL, $fCodLineax = NULL, $fCodColorx = NULL, 
                      $fCodConduc = NULL, $fNomConduc = NULL, $fCiuConduc = NULL, $fTelConduc = NULL, 
                      $fMovConduc = NULL, $fObsComent = NULL, $fCodRutaxx = NULL, $fNomRutaxx = NULL, 
                      $fIndNaturb = 1,    $fNumConfig = "3S3",$fCodCarroc = 0,    $fNumChasis = 1111, 
                      $fNumMotorx = 1111, $fNumSoatxx = 1111, $fDatVigsoa = NULL, $fNomCiasoa = "NA", 
                      $fNumTarpro = 1111, $fNumTrayle = NULL, $fCatLicenc = 1,    $fDirConduc = "NA", 
                      $fCodPoseed = NULL, $fNomPoseed = NULL, $fCiuPoseed = NULL, $fDirPoseed = "NA",
                      $mCodAgedes = NULL, $mCodContrs = NULL, $mCodAgenci = NULL, $fCodoperad = NULL,
                      $mCodGpsxxx = NULL, $mCodRemesa = NULL, $fBinHuella = NULL, $fNumViajex = NULL, $fDatgps2xx = NULL, $fNumIntent = 0  )
  {
    $file = fopen('/var/www/html/ap/interf/app/faro/logs/faroNET_'.$fNomUsuari.'_'.date("Y_m_d").'.txt', 'a+');
    fwrite($file,  "----------------------------------------  FECHA LOG ".date("Y-m-d H:i:s")." ------------------------------------------\n");
    fwrite($file,  'fNomUsuari  :'.$fNomUsuari."\n");
    fwrite($file,  'fPwdClavex  :'.$fPwdClavex."\n");
    fwrite($file,  'fCodTranps  :'.$fCodTranps."\n");
    fwrite($file,  'fCodManifi  :'.$fCodManifi."\n");
    fwrite($file,  'fDatFechax  :'.$fDatFechax."\n");
    fwrite($file,  'fCodCiuori  :'.$fCodCiuori."\n");
    fwrite($file,  'fCodCiudes  :'.$fCodCiudes."\n");
    fwrite($file,  'fCodPlacax  :'.$fCodPlacax."\n");
    fwrite($file,  'fNumModelo  :'.$fNumModelo."\n");
    fwrite($file,  'fCodMarcax  :'.$fCodMarcax."\n");
    fwrite($file,  'fCodLineax  :'.$fCodLineax."\n");
    fwrite($file,  'fCodColorx  :'.$fCodColorx."\n");
    fwrite($file,  'fCodConduc  :'.$fCodConduc."\n");
    fwrite($file,  'fNomConduc  :'.$fNomConduc."\n");
    fwrite($file,  'fCiuConduc  :'.$fCiuConduc."\n");
    fwrite($file,  'fTelConduc  :'.$fTelConduc."\n");
    fwrite($file,  'fMovConduc  :'.$fMovConduc."\n");
    fwrite($file,  'fObsComent  :'.$fObsComent."\n");
    fwrite($file,  'fCodRutaxx  :'.$fCodRutaxx."\n");
    fwrite($file,  'fNomRutaxx  :'.$fNomRutaxx."\n");
    fwrite($file,  'fIndNaturb  :'.$fIndNaturb."\n");
    fwrite($file,  'fNumConfig  :'.$fNumConfig."\n");
    fwrite($file,  'fCodCarroc  :'.$fCodCarroc."\n");
    fwrite($file,  'fNumChasis  :'.$fNumChasis."\n");
    fwrite($file,  'fNumMotorx  :'.$fNumMotorx."\n");
    fwrite($file,  'fNumSoatxx  :'.$fNumSoatxx."\n");
    fwrite($file,  'fDatVigsoa  :'.$fDatVigsoa."\n");
    fwrite($file,  'fNomCiasoa  :'.$fNomCiasoa."\n");
    fwrite($file,  'fNumTarpro  :'.$fNumTarpro."\n");
    fwrite($file,  'fNumTrayle  :'.$fNumTrayle."\n");
    fwrite($file,  'fCatLicenc  :'.$fCatLicenc."\n");
    fwrite($file,  'fDirConduc  :'.$fDirConduc."\n");
    fwrite($file,  'fCodPoseed  :'.$fCodPoseed."\n");
    fwrite($file,  'fNomPoseed  :'.$fNomPoseed."\n");
    fwrite($file,  'fCiuPoseed  :'.$fCiuPoseed."\n");
    fwrite($file,  'fDirPoseed  :'.$fDirPoseed."\n");
    fwrite($file,  'mCodAgedes  :'.$mCodAgedes."\n");
    fwrite($file,  'mCodContrs  :'.var_export($mCodContrs, true) ."\n");
    fwrite($file,  'mCodAgenci  :'.var_export($mCodAgenci, true) ."\n");
    fwrite($file,  'fCodoperad  :'.$fCodoperad."\n");
    fwrite($file,  'mCodGpsxxx  :'.var_export($mCodGpsxxx, true) ."\n");
    fwrite($file,  'mCodRemesa  :'.var_export($mCodRemesa, true) ."\n");
    fwrite($file,  'fBinHuella  :'.$fBinHuella."\n");
    fwrite($file,  'fNumViajex  :'.$fNumViajex."\n"); 
    fwrite($file,  'fDatgps2xx  :'.var_export($fDatgps2xx)."\n"); 
    fclose($file);


    # Parche - Cuando la empresa sea Intracarga se deben colocar NULL los campos
    if( $fCodTranps == '802017639' )
    {
      $mCodAgedes = NULL;
      $mCodContrs = NULL;
      $mCodAgenci = NULL;
      $fCodoperad = NULL;
      $mCodGpsxxx = NULL;
      $mCodRemesa = NULL;
      $fBinHuella = NULL;
      $fNumViajex = NULL;
    }

    $mCodContrs = $mCodContrs != NULL ? json_decode(json_encode($mCodContrs)) : $mCodContrs;
    $mCodAgenci = $mCodAgenci != NULL ? json_decode(json_encode($mCodAgenci)) : $mCodAgenci;
    $mCodGpsxxx = $mCodGpsxxx != NULL ? json_decode(json_encode($mCodGpsxxx)) : $mCodGpsxxx;
    $mCodRemesa = $mCodRemesa != NULL ? json_decode(json_encode($mCodRemesa)) : $mCodRemesa;

    $fCodPoseed = str_replace("-", "", $fCodPoseed);
    $fCodPoseed = str_replace(" ", "", $fCodPoseed);
    $fMessagesGps = array();
    $fMessagesRem =  array();    
    $fMessagesSeguim = array();
	
	//Se realiza ajuste para la Interfaz de Transportes FW ellos envian la fecha en formatos raros por lo que se acuerda en reunion 21-01-2014 con Argos 
	//tomar la fecha en la que nos envien el despacho como la fecha de salida sin importar lo que ellos nos envien
    if( $fCodTranps == '900243606' )
	{
		$fDatFechax = date( "Y-m-d H:i:s" );
	}
	
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, 
                      "cod_manifi" => $fCodManifi, "dat_fechax" => $fDatFechax, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "cod_placax" => $fCodPlacax, "num_modelo" => $fNumModelo, 
                      "cod_marcax" => $fCodMarcax, "cod_lineax" => $fCodLineax, "cod_colorx" => $fCodColorx, 
                      "cod_conduc" => $fCodConduc, "nom_conduc" => $fNomConduc, "ciu_conduc" => $fCiuConduc, 
                      "tel_conduc" => $fTelConduc, "mov_conduc" => $fMovConduc, "obs_coment" => $fObsComent, 
                      "cod_rutaxx" => $fCodRutaxx, "nom_rutaxx" => $fNomRutaxx, "ind_naturb" => $fIndNaturb, 
                      "num_config" => $fNumConfig, "cod_carroc" => $fCodCarroc, "num_chasis" => $fNumChasis, 
                      "num_motorx" => $fNumMotorx, "num_soatxx" => $fNumSoatxx, "dat_vigsoa" => $fDatVigsoa, 
                      "nom_ciasoa" => $fNomCiasoa, "num_tarpro" => $fNumTarpro, "num_trayle" => $fNumTrayle, 
                      "cat_licenc" => $fCatLicenc, "dir_conduc" => $fDirConduc, "cod_poseed" => $fCodPoseed, 
                      "nom_poseed" => $fNomPoseed, "ciu_poseed" => $fCiuPoseed, "dir_poseed" => $fDirPoseed,
                      "cod_agedes" => $mCodAgedes, "cod_operad" => $fCodoperad );

    if( $mCodContrs !== NULL && count( $mCodContrs ) > 0  )
    {
      $mCodContrs = $mCodContrs -> ControSeguim;
      $i = 0;
      foreach ( $mCodContrs as $mCodContr) 
      {
        $fArrContrs[$i]['cod_contro'] = $mCodContr -> cod_contro;
        $fArrContrs[$i]['val_duraci'] = $mCodContr -> val_duraci;
        $fArrContrs[$i]['ind_virtua'] = $mCodContr -> ind_virtua;
        $i++;
      }
    }
    else
    {
      $fArrContrs = NULL;
    }
    
    //----------------------------------------------------------------------
    if( $mCodAgenci !== NULL && count( $mCodAgenci ) > 0  )
    {
      $mCodAgenci = $mCodAgenci -> DataAgencia; 
      $i = 0;
      foreach ( $mCodAgenci as $mCodAgen) 
      {
        $fInputs['cod_agenci'] = $mCodAgen -> cod_agenci;
        $fInputs['nom_agenci'] = $mCodAgen -> nom_agenci;
        $fInputs['cod_ciudad'] = $mCodAgen -> cod_ciudad;
        $fInputs['dir_agenci'] = $mCodAgen -> dir_agenci;
        $fInputs['tel_agenci'] = $mCodAgen -> tel_agenci;
        $fInputs['con_agenci'] = $mCodAgen -> con_agenci;
        $fInputs['dir_emailx'] = $mCodAgen -> dir_emailx;
        $fInputs['num_faxxxx'] = $mCodAgen -> num_faxxxx;
         $i++;
      }
      
    }
    else
    {
      $mCodAgenci = NULL;
    }
    
    
    //------------------------- Datos Compejos del GPS -----------------------------
    if( $mCodGpsxxx !== NULL && count( $mCodGpsxxx ) > 0  )
    {  
      //$mCodGpsxxx = $mCodGpsxxx -> DataGps;

      
      if(sizeof($mCodGpsxxx -> DataGps) == '1' )
      {
          $mCodGpstemp = $mCodGpsxxx -> DataGps;
          unset($mCodGpsxxx);
          $mCodGpsxxx[0] = $mCodGpstemp;
      }else{
          $mCodGpsxxx = $mCodGpsxxx -> DataGps;
      }

      $i = 0;
      
      foreach ( $mCodGpsxxx as $mCodGps) 
      {
     
        $fInputsGps = array (
                      'cod_opegps' => $mCodGps -> cod_opegps,
                      'nom_usrgps' => $mCodGps -> nom_usrgps,
                      'clv_usrgps' => $mCodGps -> clv_usrgps,
                      'idx_gpsxxx' => $mCodGps -> idx_gpsxxx  
                      );
        
        $fValidatorGps = new Validator( $fInputsGps, "gps_valida.txt" );
        $fMessagesGps[] = $fValidatorGps -> GetMessages();
        
        $fMessageGps[] = $fMessagesGps[$i]["code"]; // Crea array con los c�digos de validaci�n de cada array que entra
       
        $i++;
      }
    }
    else
    {
      $fInputsGps = NULL;
    }

    //------------------------- Datos Compejos del GPS2 -----------------------------
    if( $fDatgps2xx !== NULL && count( $fDatgps2xx ) > 0  )
    { 
    
      $i = 0;
      $fMessageGps2 = [];
      foreach ( $fDatgps2xx as $mCodGps2) 
      {
        $fInputsGps2 = array (
                      'nom_operad' => $mCodGps2 -> nom_operad,
                      'nom_usrgps' => $mCodGps2 -> nom_usrgps,
                      'clv_usrgps' => $mCodGps2 -> clv_usrgps,
                      'idx_gpsxxx' => $mCodGps2 -> idx_gpsxxx,
                      'gps_urlxxx' => $mCodGps2 -> gps_urlxxx  
                      );
        
        $fValidatorGps2 = new Validator( $fInputsGps2, "gps_valida2.txt" );
        $fMessagesGps2[] = $fValidatorGps2 -> GetMessages();        
        $fMessageGps2[] = $fMessagesGps2[0]; // Crea array con los c�digos de validaci�n de cada array que entra
        $i++;
      }
    }
    else
    {
      $fInputsGps2 = NULL;
    }
    // -------------------------------------------------------------------------------

    //Valida Datos Remesas
    if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  )
    { 
      if(sizeof($mCodRemesa -> DataRem) == '1' )
      {
          $mCodRemesaTemp = $mCodRemesa -> DataRem;
          unset($mCodRemesa);
          $mCodRemesa[0] = $mCodRemesaTemp;
      }else{
          $mCodRemesa = $mCodRemesa -> DataRem;
      }
      $i = 0;      
      foreach ( $mCodRemesa as $mCodRem) 
      {
        
        $fInputsRem = array (
                      'cod_remesa' => $mCodRem -> cod_remesa,
                      'pes_cargax' => $mCodRem -> pes_cargax,
                      'vol_cargax' => $mCodRem -> vol_cargax,
                      'nom_empaqu' => $mCodRem -> nom_empaqu,  
                      'abr_mercan' => $mCodRem -> abr_mercan,
                      'abr_tercer' => $mCodRem -> abr_tercer,
                      'nom_remite' => $mCodRem -> nom_remite,
                      'nom_destin' => $mCodRem -> nom_destin,
                      'fec_estent' => $mCodRem -> fec_estent,
                      'fec_lledes' => $mCodRem -> fec_lledes,
                      'fec_saldes' => $mCodRem -> fec_saldes,
                      'fec_ldesti' => $mCodRem -> fec_ldesti,  
                      'dir_emailx' => $mCodRem -> dir_emailx,  
                      'cod_client' => $mCodRem -> cod_client  
                      );
        
        $fValidatorRem = new Validator( $fInputsRem, "rem_valida.txt" );
        $fMessagesRem[] = $fValidatorRem -> GetMessages();
        
        $fMessageRem[] = $fMessagesRem[$i]["code"]; // Crea array con los c�digos de validaci�n de cada array que entra
       
        $i++;
      }
    }
    else
    {
      $fInputsRem = NULL;
    }
   
   
   
    
    $fValidator = new Validator( $fInputs, "seguim_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    
    

    if( $fNomUsuari == 'InterfConalca'){
      mail("nelson.liberato@intrared.net", "Cagadas de InterfConalca Produccion", var_export($fInputs, true));
    }


    if("1000" === $fMessages["code"])
    {      
      $mCodAgenc = array();
      $mCodAgenc[cod_agenci] = $fInputs['cod_agenci'];
      $mCodAgenc[nom_agenci] = $fInputs['nom_agenci'];
      $mCodAgenc[cod_ciudad] = $fInputs['cod_ciudad'];
      $mCodAgenc[dir_agenci] = $fInputs['dir_agenci'];
      $mCodAgenc[tel_agenci] = $fInputs['tel_agenci'];
      $mCodAgenc[con_agenci] = $fInputs['con_agenci'];
      $mCodAgenc[dir_emailx] = $fInputs['dir_emailx'];
      $mCodAgenc[num_faxxxx] = $fInputs['num_faxxxx'];      
    }   
    
    $fMessagesSeguim[0] = $fMessages["code"]; // Mensage de validacion seguim_valida.txt  
    
    $fMessagess[] = $fMessages;
    
       
    $fMessagesFinally = array_merge( $fMessagess, $fMessagesGps, $fMessagesRem );  // Union de los codigos de respuesta  
   
    unset( $fInputs, $fValidator );
     
    $flagCode = 0;
    foreach ($fMessagesFinally AS $mCode)  //
    {
      if($mCode[code] !== '1000')
         $flagCode = 1;      
    }
    
    
     
    if( $flagCode === 0  )//AND count($mNumMessageGps) ===  1
    {
      try
      { 
        include_once( AplKon );
        
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguim" );

        $fReturn = NULL;
        $fSalidAut = TRUE;

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
        
        $fLocation = getLocation(  $mCodAgenc[cod_ciudad], $fExcept );
        
        if( $fLocation["CodPaisxx"] !== '' && $fLocation["CodDepart"] !== '' )
        {
          $mCodAgenc[cod_paisxx] = $fLocation["CodPaisxx"];
          $mCodAgenc[cod_depart] = $fLocation["CodDepart"];
          unset( $fLocation );
          
        }
        else             
          throw new Exception( "La ciudad de la agencia no se encuentra registrada: ".$mCodAgenc[cod_ciudad], '6001' );
        
        

        //if($mCodAgenc[dir_emailx] === '')
        //   throw new Exception( "La agencia no tiene direccion Email.", "6001" );
          
        
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fCodActivi = array( 4, 6 );

          //Verifica que se ingresa un poseedor y que este no sea el mismo conductor.
          if( NULL !== $fCodPoseed )
          {
            if( $fCodPoseed != $fCodConduc )
            {
              $fLocation = getLocation( $fCiuPoseed, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaisxx = $fLocation["CodPaisxx"];
                $fCodDeptox = $fLocation["CodDepart"];
                $fCodCiudad = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad del poseedor no se encuentra registrada: ".$fCiuPoseed, '6001' );

              $fTercer -> setTercer( $fCodPoseed, $fNomPoseed, $fNomPoseed, $fDirPoseed, 
                                     $fCodPaisxx, $fCodDeptox, $fCodCiudad, $fNomUsuari,
                                     6, NULL, NULL, NULL, 1, "C", 1, TRUE, $fCodTranps,NULL );

              $fCodActivi = "4";
            }
          }
          else
            $fCodPoseed = $fCodConduc;

          $fLocation = getLocation( $fCiuConduc, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaisxx = $fLocation["CodPaisxx"];
            $fCodDeptox = $fLocation["CodDepart"];
            $fCodCiudad = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else 
            throw new Exception( "La ciudad del conductor no se encuentra registrada: ".$fCiuConduc, '6001' );          	
          
          
          // Operadora celular
         
          if( $fCodoperad == NULL || $fCodoperad =='' )
            $fCodoperad = '10';         
         
          if(  $fTercer -> OperadExist( $fCodoperad ) !== TRUE)
            throw new Exception( "La Operadora telefonica no existe.", "6001" );
         
        
            $fTercer -> setTercer( $fCodConduc, $fNomConduc, $fNomConduc, $fDirConduc, 
                                 $fCodPaisxx, $fCodDeptox, $fCodCiudad, $fNomUsuari,
                                 $fCodActivi, $fTelConduc, $fMovConduc, $fCatLicenc, 
                                 1, "C", 1, TRUE,NULL, $fCodoperad );

          $fVehicu = new Vehicu( $fExcept );

          $fCodMarcax = $fVehicu -> getMarca( $fCodMarcax );//Valida que la marca exista o manda por defecto kw.

          $fCodLineax = $fVehicu -> getLinea( $fCodMarcax, $fCodLineax );//Valida que la linea exista o asigna la ultima linea de la marca.

          $fCodColorx = $fVehicu -> getCodColor( $fCodColorx );//Valida el color del vehiculo o retorna el color por defecto.
          
          $fCodCarroc = $fVehicu -> getCodCarroc( $fCodCarroc );//Valida la carroceria del vehiculo o retorna la carroceria por defecto.

          $fQueryInsVehicu = $fVehicu -> setVehicuSatt( $fCodPlacax, $fCodMarcax, $fCodLineax, $fCodColorx, $fNumModelo, $fCodPoseed, $fCodConduc, $fNomUsuari, 
                                                    $fNumMotorx, $fNumChasis, $fNumSoatxx, $fDatVigsoa, $fNomCiasoa, $fNumConfig, $fNumTarpro, $fCodCarroc );

          $fSeguim = new Seguim( $fExcept );
          
          
          if ($fSeguim -> despacFinalizado( $fCodManifi, $fCodTranps, $fCodPlacax )) 
            throw new Exception( "Numero de manifiesto se encuentra Finalizado en la plataforrma.", "6001" );
           
          
          if ($fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax )) 
            throw new Exception( "Numero de manifiesto repetido.", "6001" );
 
         
          //return  $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax);
          
          $fLocation = getLocation( $fCodCiuori, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaiori = $fLocation["CodPaisxx"];
            $fCodDepori = $fLocation["CodDepart"];
            $fCodCiuori = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

          $fLocation = getLocation( $fCodCiudes, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaides = $fLocation["CodPaisxx"];
            $fCodDepdes = $fLocation["CodDepart"];
            $fCodCiudes = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "La ciudad de destino no se encuentra registrada: ".$fCodCiudes, '6001' );

          $fDespac = new DespacSat( $fExcept );
          
          if( NULL === $fCodRutaxx || '0' === $fCodRutaxx )
          {
            $fSalidAut = FALSE;
          }
          else
          {
          }
          $fCodRutaxx = $fDespac -> getRuta( trim( $fCodRutaxx ), trim( $fCodCiuori ), trim( $fCodCiudes ), trim( $fNomRutaxx ), trim( $fCodTranps ) );//Retorna el codigo de ruta exacto que cumpla con los parametros de entrada.
          
          if( FALSE === $fCodRutaxx )
          {
            $fCodRutaxx = $fSeguim ->  getRoute( trim( $fCodCiuori ), trim( $fCodCiudes ) );//Retorna la primera ruta que se encuentre para el origen y destino dado.
            if( FALSE === $fCodRutaxx )
            {
              $fSeguim -> notifyRoutInterf( $fCodCiuori, $fCodCiudes, $fCodTranps);
              throw new Exception( "No se encuentra ruta disponible para origen ".$fCodCiuori." y destino ".$fCodCiudes.".", "6001" );
              break;
            }
            else
            {
              $fSalidAut = TRUE;
            }
          }
          else
          {
            $fSalidAut = TRUE;
          }
          
          // inicia transaccion para genera el num_despac
          $fConsult -> StartTrans();

          // manda el insert en despac_despac
          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari,
                                2, $fLastCodAgenci, 0, 0, "N", "R", FALSE, $fObsComent, $fMovConduc, $fDatgps2xx);

          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert.", "3001" ); 
          }
          
          // obtiene el ID de despac_despac, el del autoconsecutivo
          $fNumDespac = $fSeguim -> getNextNumDespac();    
         
          
       
          
          $fLastCodAgenci = $fSeguim -> SetDatAgenc($mCodAgenc ,$fNomUsuari ,$fCodTranps, TRUE ); // Agencia
          
          //$fQueryDespacTramo = $fSeguim -> setDespacTramo($fNumDespac ,$fCodTranps, $fCodRutaxx, $fCodCiuori, $fCodCiudes, $fNomUsuari, FALSE ); // Despacho Tramo

          
          if( $fLastCodAgenci == NULL || $fLastCodAgenci== 0 || $fLastCodAgenci== '' ){
            $fLastCodAgenci = 1;
          }
     
          


          // insert en despac_sisext para funcionamiento con la aoo y las etapas de precargue y cargue, trazabilidad integral
          $fQueryInsDespacSisext = $fSeguim -> setDespacSisext( $fNumDespac, $fCodManifi, FALSE);

          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, $fCodConduc, 
                                                    $fCodPlacax, $fObsComent, $fNomUsuari, $fLastCodAgenci );

          $fQueryInsDesRem = $fSeguim -> setDesRem( $fNumDespac, $mCodRemesa, $fNomUsuari);


          // Volcado de datos de remesas a estructura de destinatarios (Especie de homologacion) 2017 12 07 ID:261848
          $fArrDestin = array();
          $fQueryInsDestin = array();
          $fNitFajobe = '800232356';
          $fArrGenera = array(); // variable para almacenar los cod_genera de las remesas
          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  )
          {     
            foreach ( $mCodRemesa as $mRemesa) 
            {
              // parche ya que los NIT que llegan por este campo, no existen en tercer_tercer y por referencia de llave, generar error de insert en tab_despac_destin, se deja el tercero 9999999=no registra
              $mRemesa -> cod_client = $mRemesa -> cod_client == $fNitFajobe ? $mRemesa -> cod_client : '9999999';
              $fArrDestin[] = array (
                            'num_docume' => $mRemesa -> cod_remesa,
                            'num_docalt' => $mRemesa -> cod_remesa,
                            'cod_genera' => $mRemesa -> cod_client,
                            'nom_genera' => utf8_encode($mRemesa -> abr_tercer),
                            'nom_destin' => utf8_encode($mRemesa -> nom_destin),  
                            'cod_ciudad' => $mRemesa -> ciu_destin, // Nuevo
                            'dir_destin' => utf8_encode($mRemesa -> dir_destin), // Nuevo
                            'num_destin' => $mRemesa -> num_destin,
                            'fec_citdes' => date("Y-m-d", strtotime($mRemesa -> fec_estent) ),
                            'hor_citdes' => date("H:i:s", strtotime($mRemesa -> fec_estent) ) 
                            );
            }
            // genera en despac_destin las remesas que ingresa al despacho, para la app
            $fQueryInsDestin = $fSeguim -> setDesDestin( $fNumDespac, json_decode(json_encode($fArrDestin)), $fNomUsuari);
          }
                 
                                                                        
          if( NULL !== $fQueryInsVehicu ) {
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );
          }
          
        

          if ( $fConsult -> ExecuteCons( $fQueryInsDespacSisext, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert Sisext.", "3001" );
          }
            
          if ( $fConsult -> ExecuteCons( $fQueryInsDesVehi, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert.", "3001" );
          }
          
          if( $mCodGpsxxx !== NULL && count( $mCodGpsxxx ) > 0  ) 
          {
          	$fQueryInsGps = $fSeguim -> SetGps($fNumDespac, $mCodGpsxxx, $fNomUsuari);  //Gps
            if($fQueryInsGps === FALSE) {
              throw new Exception( "Error en Insert.", "3001" );
            }

          }
                    
          
          /*for($t = 0; $t<sizeof($fQueryDespacTramo); $t++)
          {          
            if($fConsult -> ExecuteCons( $fQueryDespacTramo[$t], "R" ) === FALSE )  // Insercion despacho tramo
              throw new Exception( "Error en Insert.", "3001" );
          }*/
          
          if( $fQueryInsDesRem != NULL && count( $fQueryInsDesRem ) > 0 )
          {
            foreach( $fQueryInsDesRem as $fQuery )
            {
              if ( $fConsult -> ExecuteCons( $fQuery, "R" ) === FALSE )
                throw new Exception( "Error en Insert.", "3001" );
            }
            
          }

          if( $fBinHuella != NULL )
          {
            $mSetHuella = $fSeguim -> setHuellaConductor(  $fCodConduc , $fBinHuella );
            if ( $fConsult -> ExecuteCons( $mSetHuella, "R" ) === FALSE )
                throw new Exception( "Error en Insert Huella Biometrico.", "3001" );
          }          

          # Si el cliente con SAT envia el despacho y tienen numero de viaje de corona se hace la relacion para replicar las novedades, NO QUITAR EL IF
          if( $fNumViajex != NULL )
          {
            $mSetViajeTercer = $fSeguim -> setViajexTercer( $fNumDespac, $fNumViajex, $fCodTranps, $fNomUsuari  );
            if ( $fConsult -> ExecuteCons( $mSetViajeTercer, "R" ) === FALSE )
                throw new Exception( "Error en Relacion Viaje corona con despacho cliente (Empresa tercera de corona).", "3001" );
          }
          
          if( $fQueryInsDestin != NULL && count( $fQueryInsDestin ) > 0 )
          {
            foreach( $fQueryInsDestin as $fQuery )
            {
              if ( $fConsult -> ExecuteCons( $fQuery, "R" ) === FALSE )
                throw new Exception( "Error en Insert Destin.", "3001" );
            }
            
          }


          // validación para enviar despacho a la central para la app, pero para fajober
          //if( in_array( $fNitFajobe, $fArrGenera ) ) {
            setDespachoAPP($fCodTranps,$fNumDespac,$fConsult );
          //}



          $fConsult -> Commit();

          if( $fSalidAut )
          {
            //Intracarga solo tiene contratados puestos fisicos
            #$fIndVirtua = $fCodTranps == '802017639' ? FALSE : TRUE;
            $fIndVirtua =   TRUE;
            
            $mResult = $fSeguim -> setDesSegu( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $fArrContrs, $fCodPlacax, $fCodTranps ) ;
            
            if( $mResult === 'SI')
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.".$fCodRutaxx."-".$fNumDespac ;
            else if( $mResult === 'NO')
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, queda pendiente darle salida.";
            else  
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control.";
          }
          else
            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta.";
            
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          

      }
      catch( Exception $e )
      {
        
        if( "3001" == $e -> getCode() )
        {
          //mail( "hugo.malagon@intrared.net", "Pruebas", 'lanza excepcion='.$fNumDespac.' Intent='.$fNumIntent.' Manifi='.$fCodManifi.' Transportadora='.$fCodTranps,'From: soporte.ingenieros@intrared.net' );
          if( $fNumIntent < 3 )
          {
            $fNumIntent++;
           
            $fReturn = setSeguim( $fNomUsuari, $fPwdClavex, $fCodTranps, $fCodManifi,
                                  $fDatFechax, $fCodCiuori, $fCodCiudes, $fCodPlacax, 
                                  $fNumModelo, $fCodMarcax, $fCodLineax, $fCodColorx, 
                                  $fCodConduc, $fNomConduc, $fCiuConduc, $fTelConduc, 
                                  $fMovConduc, $fObsComent, $fCodRutaxx, $fNomRutaxx, 
                                  $fIndNaturb, $fNumConfig, $fCodCarroc, $fNumChasis, 
                                  $fNumMotorx, $fNumSoatxx, $fDatVigsoa, $fNomCiasoa, 
                                  $fNumTarpro, $fNumTrayle, $fCatLicenc, $fDirConduc, 
                                  $fCodPoseed, $fNomPoseed, $fCiuPoseed, $fDirPoseed,
                                  $mCodAgedes, $mCodContrs, $mCodAgenci, $fCodoperad, 
                                  $mCodGpsxxx, $mCodRemesa, $fBinHuella, $fNumViajex, $fDatgps2xx, $fNumIntent );
            
          }
          else
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          
        }
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }
      return $fReturn;
    }
    else
    {
       
       $fMessageFinally = $fMessagesFinally;
       
//       if( $fCodPlacax == 'UPQ075' )
//         return $fMessageFinally;
      if( $flagCode === 1 )
      {
        //Separa los errores de codigo 6001
        foreach ($fMessageFinally AS $code) {
           if($code[code] === '6001')
              $Result[] =   $code;           
        }
        //Separa el �rea del mensaje del error
        for ($i = 0; $i<sizeof($Result); $i++) {
          for($j = 0; $j < sizeof($Result) ; $j++ ) {
            $Messages[] = $Result[$i][message][$j];  
          } 
        }
        //Concadena los errores retornados de la validaci�n
        foreach ($Messages AS $fRow) {
          if($fRow["Col"] != '')
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."|<br> ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
        
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
     
    
    }
  }


  /***********************************************************************
  * Funcion Inserta despacho con datos minimos                           *
  * @fn setSeguimPC                                                      *
  * @brief Inserta un despacho y le da salida automatica.                *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fDatFechax    : string Fecha de la salida YYYY-MM-DD HH:MM.  *
  * @param $fCodPlacax    : string Matricula del vehiculo.               *
  * @param $fCodRutaxx    : int    Codigo de la ruta faro.               *
  * @param $fObsComent    : string Observaciones.                        *
  * @param $fCodCiuori    : string Codigo ciudad origen.                 *
  * @param $fCodCiudes    : string Codigo ciudad destino.                *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setSeguimPC( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, 
                        $fDatFechax = NULL, $fCodPlacax = NULL, $fCodRutaxx = NULL, $fObsComent = NULL, 
                        $fCodCiuori = NULL, $fCodCiudes = NULL, $fConTelmov = NULL, $fNomConduc = NULL )
  {
    $fCodCiuori = trim( $fCodCiuori ) == '' ? NULL : $fCodCiuori; 
    $fCodCiudes = trim( $fCodCiudes ) == '' ? NULL : $fCodCiudes;
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "dat_fechax" => $fDatFechax, "cod_placax" => $fCodPlacax, "cod_rutaxx" => $fCodRutaxx, "obs_coment" => $fObsComent, 
                      "cod_ciuori" => $fCodCiuori, "cod_ciudes" => $fCodCiudes, "con_telmov" => $fConTelmov, "nom_conduc" => $fNomConduc );
    $fValidator = new Validator( $fInputs, "seguimpc_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    //unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguimPC" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          

          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fVehicu = new Vehicu( $fExcept );
          if( !$fVehicu -> vehicuExists( $fCodPlacax ) )
          {
            $fQueryInsVehicu = $fVehicu -> setVehicu( $fCodPlacax, "KW", 999, 0, 2010, 1001, 1001, $fNomUsuari );
          }
          else
            $fQueryInsVehicu = NULL;

          $fSeguim = new Seguim( $fExcept );

          if( $fSeguim -> placaEnRuta( $fCodTranps, $fCodPlacax ) )
          {
            //Si intentan crear un despacho para una placa que esta en ruta, se le da llegada a los despachos que estan en ruta y se crea el despacho con la nueva informacion
            $fUpdLlegada = "UPDATE ".BASE_DATOS.".tab_despac_despac a, ".BASE_DATOS.".tab_despac_vehige b ".
                                 "SET a.fec_llegad = NOW(), ".
                                     "a.obs_llegad = 'Se ingreso otro despacho con esa placa, se le da llegada automatica' ".
                               "WHERE a.num_despac = b.num_despac ".
                                 "AND b.num_placax = '".$fCodPlacax."' ".
                                 "AND b.cod_transp = '".$fCodTranps."' ".
                                 "AND a.fec_salida IS NOT NULL ".
                                 "AND a.ind_anulad = 'R' ".
                                 "AND a.fec_llegad IS NULL".
                                 "";
            $fConsult -> ExecuteCons( $fUpdLlegada, "R" );
          }
          elseif( $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax ) )
          {
            throw new Exception( "Numero de manifiesto repetido.", "6001" );
          }

          if( $fSeguim -> rutaExists( $fCodRutaxx ) )
          {
            $fRoutCities = $fSeguim ->  getRoutCities( $fCodRutaxx );

            if( NULL == $fCodCiuori && NULL == $fCodCiudes )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];
              $fCodCiuori = $fRoutCities["cod_ciuori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
              $fCodCiudes = $fRoutCities["cod_ciudes"];
            }
            elseif( $fCodCiuori == $fRoutCities["cod_ciuori"] && $fCodCiudes == $fRoutCities["cod_ciudes"] )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
            }
            else
              throw new Exception( "La ciudad de origen y destino no corresponden con la ruta seleccionada.", "6001" );
          }
          elseif( NULL !== $fCodCiuori && NULL !== $fCodCiudes )
          {
            $fCodRutaxx = $fSeguim ->  getRoute( $fCodCiuori, $fCodCiudes, $fCodTranps );
            if( FALSE !== $fCodRutaxx )
            {
              $fLocation = getLocation( $fCodCiuori, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaiori = $fLocation["CodPaisxx"];
                $fCodDepori = $fLocation["CodDepart"];
                $fCodCiuori = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

              $fLocation = getLocation( $fCodCiudes, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaides = $fLocation["CodPaisxx"];
                $fCodDepdes = $fLocation["CodDepart"];
                $fCodCiudes = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de destino no se encuentra registrada: ".$fCodCiudes, '6001' );
            }
            else
            {
              $fSeguim -> notifyRout( $fCodCiuori, $fCodCiudes, $fCodTranps, "satt_faro" );
              throw new Exception( "No se encuentra ruta disponible para origen ".$fCodCiuori." y destino ".$fCodCiudes.".", "6001" );
            }

          }
          else
            throw new Exception( "No se envio codigo de ruta y/o ciudad de origen y destino.", "6001" );

          $fNumDespac = $fSeguim -> getNextNumDespac();

          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                    $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari );


          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, 1001, 
                                                      $fCodPlacax, $fObsComent, $fNomUsuari );
          $fConsult -> StartTrans();

          if( NULL != $fQueryInsVehicu )
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );

          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
            
          if ( $fConsult -> ExecuteCons( $fQueryInsDesVehi, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
                      
          if( $fConTelmov != NULL )
          {
            //Se actualiza el numero de movil en el despacho para que le puedan realizar MA
            $fQueryUpDespac = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                 "SET con_telmov = '".$fConTelmov."' ".
                               "WHERE num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQueryUpDespac, "R" );
          }
          if( $fNomConduc != NULL )
          {
            //Se actualiza el nombre del conductor en el despacho para que le puedan realizar MA
            $fQueryUpDespacV = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                                 "SET nom_conduc = '".$fNomConduc."' ".
                               "WHERE num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQueryUpDespacV, "R" );
          }

          $fConsult -> Commit();
          
          //coounidas tiene contratado monitoreo activo
          $fIndVirtua = $fCodTranps == '900014965' ? TRUE : FALSE;
          if( FALSE !== $fSeguim -> setDesSegu( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $mCodContrs, $fCodPlacax, 
                              $fCodTranps ) )

            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.";
          else
          {
            $fSeguim -> notificarRutaSinPcs( $fCodCiuori, $fCodCiudes, $fCodTranps, "satt_faro" );
            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control.";
          }

        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          //mail( "hugo.malagon@intrared.net", "Error Supervisar", $fReturn."\n".http_build_query( $fInputs, '', "\n" ),'From: soporte.ingenieros@intrared.net' );
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
       //mail( "hugo.malagon@intrared.net", "Error Supervisar", "code_resp:6001; msg_resp:".$fMessage."\n".http_build_query( $fInputs, '', "\n" ),'From: soporte.ingenieros@intrared.net' );
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
      {
        //mail( "hugo.malagon@intrared.net", "Error Supervisar", "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"]."\n".http_build_query( $fInputs, '', "\n" ),'From: soporte.ingenieros@intrared.net' );
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
      }
    }
  }

  /***********************************************************************
  * Funcion Inserta despacho con datos minimos                           *
  * @fn setSeguimFTP                                                     *
  * @brief Inserta un despacho y le da salida automatica.                *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fDatFechax    : string Fecha de la salida YYYY-MM-DD HH:MM.  *
  * @param $fCodPlacax    : string Matricula del vehiculo.               *
  * @param $fCodRutaxx    : int    Codigo de la ruta faro.               *
  * @param $fObsComent    : string Observaciones.                        *
  * @param $fCodCiuori    : string Codigo ciudad origen.                 *
  * @param $fCodCiudes    : string Codigo ciudad destino.                *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setSeguimFTP( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, 
                        $fDatFechax = NULL, $fCodPlacax = NULL, $fCodRutaxx = NULL, $fObsComent = NULL, 
                        $fCodCiuori = NULL, $fCodCiudes = NULL, $fConTelmov = NULL, $fNomConduc = NULL,
                        $fCodCondu2 = NULL, $fCodClien2 = NULL, $fNomClient = NULL, $fEmaClient = NULL )
  {
    $fCodCiuori = trim( $fCodCiuori ) == '' ? NULL : $fCodCiuori; 
    $fCodCiudes = trim( $fCodCiudes ) == '' ? NULL : $fCodCiudes;
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "dat_fechax" => $fDatFechax, "cod_placax" => $fCodPlacax, "cod_rutaxx" => $fCodRutaxx, "obs_coment" => $fObsComent, 
                      "cod_ciuori" => $fCodCiuori, "cod_ciudes" => $fCodCiudes, "con_telmov" => $fConTelmov, "nom_conduc" => $fNomConduc );
    $fValidator = new Validator( $fInputs, "seguimpc_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    //unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguimPC" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          

          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fVehicu = new Vehicu( $fExcept );
          if( !$fVehicu -> vehicuExists( $fCodPlacax ) )
          {
            $fQueryInsVehicu = $fVehicu -> setVehicu( $fCodPlacax, "KW", 999, 0, 2010, 1001, 1001, $fNomUsuari );
          }
          else
            $fQueryInsVehicu = NULL;

          $fSeguim = new Seguim( $fExcept );

          if( $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax ) )
          {
            return "code_resp:6001; msg_resp:Numero de manifiesto repetido.";
          }
          
          $fQuerySelFinali = "SELECT 1 
                               FROM ".BASE_DATOS.".tab_despac_despac a, 
                                    ".BASE_DATOS.".tab_despac_vehige b 
                              WHERE a.num_despac = b.num_despac 
                                AND a.cod_manifi = '".$fCodManifi."' 
                                AND b.cod_transp = '".$fCodTranps."' 
                                AND b.num_placax = '".$fCodPlacax."' 
                                AND a.ind_anulad = 'R' 
                                AND b.ind_activo = 'S' 
                                AND a.fec_llegad IS NOT NULL 
                                AND a.fec_salida IS NOT NULL";
                          
          $fConsult -> ExecuteCons( $fQuerySelFinali );
          $finali = $fConsult -> RetMatrix( 'a' );
          
          if( count( $finali ) > 0 )
          {
            return "code_resp:6001; msg_resp:Numero de manifiesto finalizado.";
          }

          if( $fSeguim -> rutaExists( $fCodRutaxx ) )
          {
            $fRoutCities = $fSeguim ->  getRoutCities( $fCodRutaxx );

            if( NULL == $fCodCiuori && NULL == $fCodCiudes )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];
              $fCodCiuori = $fRoutCities["cod_ciuori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
              $fCodCiudes = $fRoutCities["cod_ciudes"];
            }
            elseif( $fCodCiuori == $fRoutCities["cod_ciuori"] && $fCodCiudes == $fRoutCities["cod_ciudes"] )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
            }
            else
              throw new Exception( "La ciudad de origen y destino no corresponden con la ruta seleccionada.", "6001" );
          }
          elseif( NULL !== $fCodCiuori && NULL !== $fCodCiudes )
          {
			  
            $fCodRutaxx = $fSeguim ->  getRouteFTP( $fCodCiuori, $fCodCiudes, $fCodTranps );
            if( FALSE !== $fCodRutaxx )
            {
              $fLocation = getLocation( $fCodCiuori, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaiori = $fLocation["CodPaisxx"];
                $fCodDepori = $fLocation["CodDepart"];
                $fCodCiuori = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

              $fLocation = getLocation( $fCodCiudes, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaides = $fLocation["CodPaisxx"];
                $fCodDepdes = $fLocation["CodDepart"];
                $fCodCiudes = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de destino no se encuentra registrada: ".$fCodCiudes, '6001' );
            }
            else
            {
              $mNomRutaxx = $fSeguim -> notifyRoutInterf3( $fCodCiuori, $fCodCiudes, $fCodTranps );
              throw new Exception( "No se encuentra ruta disponible para origen ".$fCodCiuori." y destino ".$fCodCiudes.". ".$mNomRutaxx, "6001" );
            }

          }
          else
            throw new Exception( "No se envio codigo de ruta y/o ciudad de origen y destino.", "6001" );

          $fNumDespac = $fSeguim -> getNextNumDespac();

          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                    $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari );


          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, 1001, 
                                                      $fCodPlacax, $fObsComent, $fNomUsuari );
          $fConsult -> StartTrans();

          if( NULL != $fQueryInsVehicu )
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );

          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
            
          if ( $fConsult -> ExecuteCons( $fQueryInsDesVehi, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
                      
          
          $fConTelmov = $fConTelmov == '' || $fConTelmov == NULL ? 'NULL' : "'".$fConTelmov."'";
          $fEmaClient = $fEmaClient == '' || $fEmaClient == NULL ? 'NULL' : "'".$fEmaClient."'";
          $fCodClien2 = $fCodClien2 == '' || $fCodClien2 == NULL ? 'NULL' : "'".$fCodClien2."'";
          $fNomClient = $fNomClient == '' || $fNomClient == NULL ? 'NULL' : "'".$fNomClient."'";
          //Se actualiza el numero de movil en el despacho para que le puedan realizar MA
          $fQueryUpDespac = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                               "SET con_telmov = ".$fConTelmov.", ".
                               "    ema_client = ".$fEmaClient.", ".
                               "    cod_client2 = ".$fCodClien2.", ".
                               "    nom_client = ".$fNomClient." ".
                             "WHERE num_despac = '".$fNumDespac."' ";

          $fConsult -> ExecuteCons( $fQueryUpDespac, "R" );
          
          
          $fNomConduc = $fNomConduc == '' || $fNomConduc == NULL ? 'NULL' : "'".$fNomConduc."'";
          $fCodCondu2 = $fCodCondu2 == '' || $fCodCondu2 == NULL ? 'NULL' : "'".$fCodCondu2."'";
          //Se actualiza el nombre del conductor en el despacho para que le puedan realizar MA
          $fQueryUpDespacV = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                               "SET nom_conduc = ".$fNomConduc.", ".
                               "    cod_condu2 = ".$fCodCondu2." ".
                             "WHERE num_despac = '".$fNumDespac."' ";

          $fConsult -> ExecuteCons( $fQueryUpDespacV, "R" );
         

          $fConsult -> Commit();
          
          //Dependiendo del servicio contratado de la transportadora se asignan los puestos de control
          $mResultTipser = $fSeguim -> getTipser( $fCodTranps, 1 );
          if( $mResultTipser["cod_tipser"] == 2 || $mResultTipser["cod_tipser"] == 3 )
            $fIndVirtua = TRUE;
          elseif( $mResultTipser["cod_tipser"] == 1 ) 
            $fIndVirtua = FALSE;

          if( FALSE !== $fSeguim -> setDesSeguFTP( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $mCodContrs, $fCodPlacax, 
                              $fCodTranps ) )

            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.";
          else
          {
            $fSeguim -> notificarRutaSinPcs( $fCodCiuori, $fCodCiudes, $fCodTranps, "satt_faro" );
            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control para la ruta ".$fCodRutaxx;
          }

        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
      {
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
      }
    }
  }

  /***********************************************************************
  * Funcion Inserta una llegada a un despacho                            *
  * @fn setLlegada                                                       *
  * @brief Inserta una llegada a un despacho.                            *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fFecLlegad    : string Fecha de la llegada YYYY-MM-DD HH:MM. *
  * @param $fObsLlegad    : string Observacion de la llegada.            *
  * @param $fNumPlacax    : string Placa.                                *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setLlegada( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fFecLlegad = NULL, $fObsLlegad = NULL, $fNumPlacax = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "fec_llegad" => $fFecLlegad, "obs_llegad" => $fObsLlegad, "num_placax" => $fNumPlacax );

    $fValidator = new Validator( $fInputs, "llegada_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setLlegada" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ";
          if( $fNumPlacax !== NULL )
          {
            $fQuerySelNumDes .= " AND b.num_placax = '".$fNumPlacax."' ";
          }
          $fQuerySelNumDes .=   "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NULL ".
                                "AND a.fec_salida IS NOT NULL ".
                                "";

          $fConsult -> ExecuteCons( $fQuerySelNumDes );

          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );

            $fQueryUpdLleg = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                "SET fec_llegad = '".$fFecLlegad."', ".
                                    "obs_llegad = '".$fObsLlegad."', ".
                                    "usr_modifi = '".$fNomUsuari."', ".
                                    "fec_modifi = NOW() ".
                              "WHERE num_despac = '".$fNumDespac[0]["num_despac"]."' ";

            if( $fConsult -> ExecuteCons( $fQueryUpdLleg, "BRC" ) ) {
              $fReturn = "code_resp:1000; msg_resp:Se dio llegada con exito en Sat Trafico";
              setFinalizaDespachoAPP($fCodTranps, $fNumDespac[0]["num_despac"], $fConsult );
            }
            else
              $fReturn = "code_resp:1999; msg_resp:No se pudo dar llegada en Sat Trafico";
          }
          else
          {
            $fQuerySelFinali = "SELECT 1 ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ";
            if( $fNumPlacax !== NULL )
            {
              $fQuerySelFinali .= " AND b.num_placax = '".$fNumPlacax."' ";
            }
            $fQuerySelFinali .= "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NOT NULL ".
                                "AND a.fec_salida IS NOT NULL ";

            $fConsult -> ExecuteCons( $fQuerySelFinali );
            
            if( 0 != $fConsult -> RetNumRows() )
              $fReturn = "code_resp:1999; msg_resp:El despacho con manifiesto ".$fCodManifi." se encuentra finalizado en Sat Trafico";
            else
              $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho para el manifiesto ".$fCodManifi." en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /***********************************************************************
  * Funcion Anula un despacho                                            *
  * @fn setAnulad                                                         *
  * @brief Anula a un despacho.                                          *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fNumPlacax    : string Placa.                                *
  * @param $fFecAnulad    : string Fecha de la llegada YYYY-MM-DD HH:MM. *
  * @param $fObsanulad    : string Observacion de la llegada.            *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setAnulad( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fNumPlacax = NULL, $fFecAnulad = NULL, $fObsAnulad = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_anulad" => $fFecAnulad, "obs_anulad" => $fObsAnulad  );

    $fValidator = new Validator( $fInputs, "anulad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setAnulad" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_llegad IS NULL";

          $fConsult -> ExecuteCons( $fQuerySelNumDes );
          
          
          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                         SET ind_activo = 'N',
                             obs_anulad = '".$fObsAnulad."',
                             usr_modifi = '".$fNomUsuari."',
                             fec_modifi = NOW()
                       WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'";

            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
          
            $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                         SET  ind_anulad = 'A',
                              usr_modifi = '".$fNomUsuari."',
                              fec_modifi = NOW()
                        WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'";
          
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
            
            $fConsult -> Commit();
            $fReturn = "code_resp:1000; msg_resp:Se anulo con exito en Sat Trafico";
            setFinalizaDespachoAPP($fCodTranps, $fNumDespac[0]["num_despac"], $fConsult );
          }
          else
          {
            $fQuerySelFinali = "SELECT 1 ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NOT NULL ".
                                "AND a.fec_salida IS NOT NULL ";

            $fConsult -> ExecuteCons( $fQuerySelFinali );
            
            if( 0 != $fConsult -> RetNumRows() )
              $fReturn = "code_resp:1999; msg_resp:El despacho con manifiesto ".$fCodManifi." se encuentra finalizado en Sat Trafico";
            else
              $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho para el manifiesto ".$fCodManifi." en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /***********************************************************************
  * Funcion Reversa la salida de un despacho                             *
  * @fn setRevSalida                                                     *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fNumPlacax    : string Placa.                                *
  * @param $fFecAnulad    : string Fecha de la llegada YYYY-MM-DD HH:MM. *
  * @param $fObsanulad    : string Observacion de la llegada.            *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setRevSalida( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fNumPlacax = NULL, $fFecAnulad = NULL, $fObsAnulad = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_anulad" => $fFecAnulad, "obs_anulad" => $fObsAnulad  );

    $fValidator = new Validator( $fInputs, "anulad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setRevSalida" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

            $fQuerySelNumDes = "SELECT a.num_despac, a.fec_salida ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_salida IS NOT NULL ".
                                "AND a.fec_llegad IS NULL ".
                                "AND ( SELECT COUNT( y.cod_contro ) FROM ".BASE_DATOS.".tab_despac_noveda y WHERE y.num_despac = a.num_despac ) = 0 ".
                                "AND ( SELECT COUNT( z.cod_contro ) FROM ".BASE_DATOS.".tab_despac_contro z WHERE z.num_despac = a.num_despac ) = 0 ";
          $fConsult -> ExecuteCons( $fQuerySelNumDes );

          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                         SET ind_activo = 'R',
                             obs_anulad = '".$fObsAnulad.'.Fecha Salida: '.$fNumDespac[0]["fec_salida"]."\n',
                             usr_modifi = '".$fNomUsuari."',
                             fec_modifi = NOW()
                       WHERE num_despac = ".$fNumDespac[0]["num_despac"]."";
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );

            $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                         SET fec_salida = NULL,
                             ind_anulad = 'A',
                             usr_modifi = '".$fNomUsuari."',
                             fec_modifi = NOW()
                       WHERE num_despac = ".$fNumDespac[0]["num_despac"]."";

            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
   
            $fConsult -> Commit();
            $fReturn = "code_resp:1000; msg_resp:Se reverso la salida con exito en Sat Trafico";
            setFinalizaDespachoAPP($fCodTranps, $fNumDespac[0]["num_despac"], $fConsult );
          }
          else
          {
            $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho que cumpla con los requisitos para reversar salida ( que este en ruta y sin novedades ) en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /*************************************************************************
  * Funcion Reversa la llegada de un despacho                              *
  * @fn setRevLlegada                                                      *
  * @param $fNomUsuari    : string Usuario.                                *
  * @param $fPwdClavex    : string Clave.                                  *
  * @param $fCodTranps    : int    Nit Transportadora.                     *
  * @param $fCodManifi    : string Numero del manifiesto.                  *
  * @param $fNumPlacax    : string Placa.                                  *
  * @param $fFecAnulad    : string Fecha de la reversion YYYY-MM-DD HH:MM. *
  * @param $fObsanulad    : string Observacion de la reversion.            *
  * @return string mensaje de respuesta.                                   *
  **************************************************************************/
  function setRevLlegada( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fNumPlacax = NULL, $fFecAnulad = NULL, $fObsAnulad = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_anulad" => $fFecAnulad, "obs_anulad" => $fObsAnulad  );

    $fValidator = new Validator( $fInputs, "anulad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setRevLlegada" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

            $fSeguim = new Seguim( $fExcept );
            if( $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fNumPlacax ) )
              throw new Exception( "Fallo la reversion de la llegada en SAT Trafico. Ya se encuentra en ruta un despacho con manifiesto ".$fCodManifi." y placa ".$fNumPlacax.".", "6001" );
            
            $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_llegad IS NOT NULL ".
                           "ORDER BY a.fec_creaci DESC";
          $fConsult -> ExecuteCons( $fQuerySelNumDes );

          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta finalizado para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET fec_llegad = NULL,
                     usr_modifi = '".$fNomUsuari."',
                     fec_modifi = NOW()
               WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'";
            
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );

   
            $fConsult -> Commit();
            $fReturn = "code_resp:1000; msg_resp:Se reverso la llegada con exito en Sat Trafico";
            setDespachoAPP($fCodTranps, $fNumDespac[0]["num_despac"], $fConsult );
          }
          else
          {
            $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho finalizado para reversar llegada en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }



  /***********************************************************************
  * Funcion para finalizar un despacho en la APP de avansat en central   *
  * @fn setDespacPdf                                                     *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Codigo de la ruta Faro.                   *
  * @param $fCodPlacax: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/  
  function setDespachoAPP($fNitEmpresa = '', $fNumDespac = '', $fConsult )  
  {
    try {
          // busca si hay registros de fNumDespac con ese fNitEmpresa en tabla de destinatarios
          $mSql = "SELECT num_despac  FROM ".BASE_DATOS.".tab_despac_vehige a WHERE a.num_despac = '".$fNumDespac."' AND a.cod_transp = '".$fNitEmpresa."' "; 
          $fConsult -> ExecuteCons( $mSql );
          $mMatriz = $fConsult -> RetMatrix( 'a' ); 

          // si hay resultado de registros, se procede a finalizar el despacho en la central
          if(sizeof($mMatriz) > 0)
          {
            // Consulta si la empresa (del nit) tiene activa interfaz con Movil para despachos
            $mSql = "SELECT ind_estado FROM ".BASE_DATOS.".tab_interf_parame a WHERE a.cod_operad = '85' AND a.cod_transp = '".$fNitEmpresa."' AND ind_estado = 1 "; 
            $fConsult -> ExecuteCons( $mSql );
            $mMatriz = $fConsult -> RetMatrix( 'a' ); 
            if ( $mMatriz[0]['ind_estado']) 
            {
              // Incluye la api para enviar el despacho a la central
              require_once "/var/www/html/ap/interf/app/APIClienteApp/controlador/DespachoControlador.php";
              $controlador = new DespachoControlador();
              $response    = $controlador->registrar($fConsult, $fNumDespac, $fNitEmpresa);    
              $opfl = fopen("/var/www/html/ap/interf/app/faro/logs/Despacho_".$fNitEmpresa."_".date("Ymd").".txt", "a+");
              fwrite($opfl, "---------------------------".date("Y-m-d H:i:s")."-------------------------------\n");
              fwrite($opfl, "Accion           : Generar despacho en APP");
              fwrite($opfl, "Despacho         : ".$fNumDespac."\n");
              fwrite($opfl, "Manifiesto       : ".$fCodManifi."\n");
              fwrite($opfl, "Placa            : ".$fCodPlacax."\n");
              fwrite($opfl, "Conductor ID     : ".$fCodConduc."\n");
              fwrite($opfl, "Conductor Nom    : ".$fNomConduc."\n");
              fwrite($opfl, "Response API APP : ".json_encode($response)."\n"); 
              fclose($opfl);

            }
          }

    } catch (Exception $e) {
      
    }
  }  


  /***********************************************************************
  * Funcion para finalizar un despacho en la APP de avansat en central   *
  * @fn setDespacPdf                                                     *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Codigo de la ruta Faro.                   *
  * @param $fCodPlacax: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/  
  function setFinalizaDespachoAPP($fNitEmpresa = '', $fNumDespac = '', $fConsult )  
  {
    try {
          // busca si hay registros de fNumDespac con ese fNitEmpresa en tabla de destinatarios
          $mSql = "SELECT num_despac, num_docume  FROM ".BASE_DATOS.".tab_despac_vehige a WHERE a.num_despac = '".$fNumDespac."' AND a.cod_genera = '".$fNitEmpresa."' "; 
          $fConsult -> ExecuteCons( $mSql );
          $mMatriz = $fConsult -> RetMatrix( 'a' ); 

          // si hay resultado de registros, se procede a finalizar el despacho en la central
          if(sizeof($mMatriz) > 0)
          {
            // Consulta si la empresa (del nit) tiene activa interfaz con Movil para despachos
            $mSql = "SELECT ind_estado FROM ".BASE_DATOS.".tab_interf_parame a WHERE a.cod_operad = '85' AND a.cod_transp = '".$fNitEmpresa."' AND ind_estado = 1 "; 
            $fConsult -> ExecuteCons( $mSql );
            $mMatriz = $fConsult -> RetMatrix( 'a' ); 
            if ( $mMatriz[0]['ind_estado']) 
            {
              // Incluye la api para enviar el despacho a la central
              require_once "/var/www/html/ap/interf/app/APIClienteApp/controlador/DespachoControlador.php";
              $controlador = new DespachoControlador();
              $response    = $controlador->finalizar($fConsult, $fNumDespac, $fNitEmpresa);    
              $opfl = fopen("/var/www/html/ap/interf/app/faro/logs/Despacho_Fajober_".date("Ymd").".txt", "a+");
              fwrite($opfl, "---------------------------".date("Y-m-d H:i:s")."-------------------------------\n");
              fwrite($opfl, "Accion           : Finalizar despacho en APP");
              fwrite($opfl, "Despacho         : ".$fNumDespac."\n");
              fwrite($opfl, "Manifiesto       : ".$fCodManifi."\n");
              fwrite($opfl, "Placa            : ".$fCodPlacax."\n");
              fwrite($opfl, "Conductor ID     : ".$fCodConduc."\n");
              fwrite($opfl, "Conductor Nom    : ".$fNomConduc."\n");
              fwrite($opfl, "Response API APP : ".json_encode($response)."\n"); 
              fclose($opfl);

            }
          }
    } catch (Exception $e) {
      
    }
  }
  // PDF 
  /***********************************************************************
  * Funcion Retorna Url para descargar archivo PDF                       *
  * @fn setDespacPdf                                                     *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Codigo de la ruta Faro.                   *
  * @param $fCodPlacax: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/  
  function setDespacPdf ($fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fCodPlacax = NULL)
  {
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, "cod_manifi" => $fCodManifi,"cod_placax" => $fCodPlacax );
   
    $fValidator = new Validator( $fInputs, "seguim_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    
    if("1000" === $fMessages["code"])
    {
     try
      {
        include_once( AplKon );        
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setDespacPdf" );      

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );      
        
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fSeguim = new Seguim( $fExcept );
          $CodPerfilUsuari = getCodPerfil($fNomUsuari, $fPwdClavex, $fExcept ); // /var/www/html/ap/interf/lib/funtions/General.fnc,php
         
          $fQueryTramoPdf = $fSeguim -> setDataPdf( $fCodTranps, $fCodManifi , $fCodPlacax, $CodPerfilUsuari );
         //return $fQueryTramoPdf;
          if($fQueryTramoPdf == NULL)
          { 
            return FALSE;
          }
          else
          {
            $fDataPdf = $fSeguim -> setMakePdf( $fQueryTramoPdf ); 

            return $fDataPdf ; //file_get_contents( 
             
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );        
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Verifica ruta                                                *
  * @fn routExists                                                       *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fNomRutaxx: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function routExists( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fCodRutaxx, $fNomRutaxx = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "cod_rutaxx" => $fCodRutaxx, "nom_rutaxx" => $fNomRutaxx );

    $fValidator = new Validator( $fInputs, "routexists_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "routExists" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fLocation = getLocation( $fCodCiuori, $fExcept );
          if( FALSE === $fLocation )
            throw new Exception( "Ciudad de origen no existente.", "6001" );

          $fLocation = getLocation( $fCodCiudes, $fExcept );
          if( FALSE === $fLocation )
            throw new Exception( "Ciudad de destino no existente.", "6001" );

          $fDespac = new DespacSat( $fExcept );

          $fCodRutaxx = $fDespac -> getRuta( trim( $fCodRutaxx ), trim( $fCodCiuori ), trim( $fCodCiudes ), trim( $fNomRutaxx ) );//Retorna el codigo de ruta exacto que cumpla con los parametros de entrada.
          if( FALSE === $fCodRutaxx )
            $fReturn = "code_resp:1999; msg_resp:La ruta proporcionada no existente.";
          else
            $fReturn = "code_resp:1000; msg_resp:La ruta existe en el sistema.";
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Inserta ruta                                                 *
  * @fn setRout                                                          *
  * @brief Inserta una nueva ruta.                                       *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fNomRutaxx: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function setRout( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fNomRutaxx = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "nom_rutaxx" => $fNomRutaxx );

    $fValidator = new Validator( $fInputs, "routexists_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setRout" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fLocation = getLocation( $fCodCiuori, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaiori = $fLocation["CodPaisxx"];
            $fCodDepori = $fLocation["CodDepart"];
            $fCodCiuori = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "Ciudad de origen no existente.", "6001" );

          $fLocation = getLocation( $fCodCiudes, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaides = $fLocation["CodPaisxx"];
            $fCodDepdes = $fLocation["CodDepart"];
            $fCodCiudes = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "Ciudad de destino no existente.", "6001" );

          $fQuerySelMAxRoute = "SELECT MAX( cod_rutasx ) AS cod_rutasx ".
                                 "FROM ".BASE_DATOS.".tab_genera_rutasx ";

          $fConsult -> ExecuteCons( $fQuerySelMAxRoute );
          $fCodRutasx = $fConsult -> RetMatrix( "a" );
          $fCodRutasx[0]["cod_rutasx"]++;

          $fQueryInsRoute = "INSERT INTO ".BASE_DATOS.".tab_genera_rutasx ".
                              "( cod_rutasx, nom_rutasx, cod_paiori, cod_depori, ".
                                "cod_ciuori, cod_paides, cod_depdes, cod_ciudes, ".
                                "ind_estado, usr_creaci, fec_creaci ) ".
                            "VALUES ( '".$fCodRutasx[0]["cod_rutasx"]."', '".$fNomRutaxx."', ".
                                     "'".$fCodPaiori."', '".$fCodDepori."', '".$fCodCiuori."', ".
                                     "'".$fCodPaides."', '".$fCodDepdes."', '".$fCodCiudes."', ".
                                     "'1', '".$fNomUsuari."', NOW() ) ";

          if( $fConsult -> ExecuteCons( $fQueryInsRoute, "BRC" ) )
            $fReturn = "code_resp:1000; msg_resp:".$fCodRutasx[0]["cod_rutasx"];
          else
            $fReturn = "code_resp:1999; msg_resp:La ruta no pudo ser creada.";

        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Inserta ruta                                                 *
  * @fn setRout                                                          *
  * @brief Inserta una nueva ruta.                                       *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fNomRutaxx: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function setPc( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodRutaxx = NULL, 
                  $fCodContro = NULL, $fNomContro = NULL, $fValDuraci = NULL, $fValDistan = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_rutaxx" => $fCodRutaxx, 
                      "cod_contro" => $fCodContro, "nom_contro" => $fNomContro, "val_duraci" => $fValDuraci, 
                      "val_distan" => $fValDistan, "cod_tranps" => $fCodTranps );

    $fValidator = new Validator( $fInputs, "puesto_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setPC" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fDespac = new DespacSat( $fExcept );
          $fCodContro = $fDespac -> getPcontro( trim( $fCodContro ), trim( $fNomContro ) );

          if( FALSE !== $fCodContro )
          {
            $fQuerySelContro = "SELECT cod_contro ".
                                 "FROM ".BASE_DATOS.".tab_tercer_contro ".
                                "WHERE cod_contro = '".$fCodContro."' ".
                                  "AND cod_tercer = '".trim( $fCodTranps )."' ";

            $fConsult -> ExecuteCons( $fQuerySelContro );
            $fContro = $fConsult -> RetMatrix( "a" );

            if( 0 !== count( $fContro ) )
            {
              $fQueryInsRoute = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon ".
                                  "( cod_rutasx, cod_contro, val_duraci, val_distan, ind_estado, ".
                                    "usr_creaci, fec_creaci ) ".
                                "VALUES ( '".$fCodRutaxx."', '".$fCodContro."', '".$fValDuraci."', '".$fValDistan."', ".
                                         "'1', '".$fNomUsuari."', NOW() ) ";

              if( $fConsult -> ExecuteCons( $fQueryInsRoute, "BRC" ) )
                $fReturn = "code_resp:1000; msg_resp:Puesto de control asignado con exito.";
              else
                $fReturn = "code_resp:1999; msg_resp:Puesto de control no asignado.";
            }
            else
              $fReturn = "code_resp:6001; msg_resp:El puesto de control no esta contratado por la empresa.";
          }
          else
            $fReturn = "code_resp:6001; msg_resp:El puesto de control no existe.";
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
  
  /***********************************************************************
  * Funcion Inserta una novedad en Intracarga                            *
  * @fn setPCIntracarga                                                  *
  * @brief Inserta una novedad en Intracarga.                            *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fFecNoveda    : string Fecha de la llegada YYYY-MM-DD.       *
  * @param $fHorNoveda    : string Hora de la llegada HH:MM:SS.          *
  * @param $fNomContro    : string Nombre del Puesto de Control.         *
  * @param $fDesObserv    : string Observacion de la Novedad.            *
  * @param $fNomLlavex    : string Llave para autenticacion.             *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setPCIntracarga( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodManifi = NULL, 
                            $fFecNoveda = NULL, $fHorNoveda = NULL, $fNomContro = NULL, 
                            $fDesObserv = NULL, $fNomLlavex = NULL, $fFecPronov = NULL, 
                            $fHorPronov = NULL, $fNumPlacax = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_manifi" => $fCodManifi, 
                      "fec_noveda" => $fFecNoveda, "hor_noveda" => $fHorNoveda, "nom_contro" => $fNomContro,
                      "des_observ" => $fDesObserv, "nom_llavex" => $fNomLlavex, "fec_pronov" => $fFecPronov,
                      "hor_pronov" => $fHorPronov, "num_placax" => $fNumPlacax);

    $fValidator = new Validator( $fInputs, "pcintracarga_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setPCIntracarga" );
        $fReturn = NULL;

        if( $fNomLlavex == '59a68t7j95s4t96dS2g9A' )
        {
          ini_set( "soap.wsdl_cache_enabled", "0" );
          $oSoapClient = new soapclient( 'http://www.intracarga.com.co/actualizartrafico/service1.asmx?wsdl', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
      
          $parametros = array( "ReportaPuestoControlProx" => array(  "Usuario" => $fNomUsuari, 
                                                                 "Clave" => $fPwdClavex, 
                                                                 "Manifiesto" => $fCodManifi, 
                                                                 "Fecha" => $fFecNoveda, 
                                                                 "Hora" => $fHorNoveda, 
                                                                 "Puesto_control" => $fNomContro, 
                                                                 "Observaciones" => $fDesObserv,
                                                                 "FechaProximoPC" => $fFecPronov,
                                                                 "HoraProximoPC" => $fHorPronov,
                                                                 "strPlacaVehiculo" => $fNumPlacax ) );
                                                                 
          $result = $oSoapClient -> __call( "ReportaPuestoControlProx", $parametros );    
          
          $mCodResp = explode( ":", $result -> ReportaPuestoControlProxResult );
          
          if( "000125" != $mCodResp[0] )
            $fReturn = "code_resp:1999; msg_resp:Ocurrio error en el metodo ReportaPuestoControlProx de Intracarga - ".$result -> ReportaPuestoControlProxResult;
          else
            $fReturn = "code_resp:1000; msg_resp:Se reporto la novedad a Intracarga con exito";
        }
        else
          throw new Exception( "Autenticacion fallida.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), ' ', $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /***********************************************************************
  * Funcion Obtiene los Puestos de Control Contratados                   *
  * @fn getPCsContratados                                                *
  * @brief Obtiene los Puestos de Control Contratados                    *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTransp    : string Nit Transportadora.                   *
  * @param $fCodTipdes    : string Codigo tipo de despacho.              *
  * @param $fCodRutaxx    : string Codigo de la ruta.                    *
  * @param $fTieHolgur    : int    Tiempo de holgura.                    *
  * @return string or Array mensaje de respuesta.                        *
  ************************************************************************/
  function getPCsContratados( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodTipdes = NULL, $fCodRutaxx = NULL, $fTieHolgur = 20 )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTransp, 
                  "cod_tipdes" => $fCodTipdes, "cod_rutaxx" => $fCodRutaxx, "tie_holgur" => $fTieHolgur );

    $fValidator = new Validator( $fInputs, "pccontratados_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getPCsContratados" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTransp ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
            
          if( 1 != $fCodTipdes && 2 != $fCodTipdes )
            throw new Exception( "El tipo de Despacho debe ser 1 (Urbano), 2 (Nacional).", "6001" );
            
          $fSeguim = new Seguim( $fExcept );
          
          if( $fTieHolgur == '' || $fTieHolgur == NULL )
          {
            $fTieHolgur = 20;
          }

          $fPCs = $fSeguim -> getPCFromTipser( $fCodTransp, $fCodTipdes, $fCodRutaxx, $fTieHolgur );
          if( FALSE === $fPCs )
          {
            throw new Exception( "No se encuentran configuraciones activas para la transportadora", "6001" );
            break;
          }
          else
          {
            if( $fPCs['tie_contro'] <= $fTieHolgur && $fPCs['cod_tipser'] != '1' )
              throw new Exception( "El tiempo de seguimiento es menor o igual al tiempo de holgura", "6001" );
            
            if( count( $fPCs['pcs'] ) == 0 )
              throw new Exception( "No se encontraron puestos de control para el servicio contratado por la transportadora en la ruta. Comuniquese con un operador al ".DatFar, "6001" );
          
            $fReturn['arr_pcscon'] = $fPCs['pcs'];
            $fReturn['tie_contro'] = $fPCs['tie_contro'];
            $fReturn['cod_tipser'] = $fPCs['cod_tipser'];
            $fReturn['cod_rutaxx'] = $fPCs['cod_rutaxx'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retornaron puestos satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
  /***********************************************************************
  * Funcion Obtiene todas las rutas activas segun origen y destino             *
  * @fn getRutas                                                               *
  * @param $fNomUsuari    : string Usuario.                                    *
  * @param $fPwdClavex    : string Clave.                                      *
  * @param $fCodCiuori    : string Codigo ciudad de origen.                    *
  * @param $fCodCiudes    : string Codigo ciudad de destino.                   *
  * @param $fNotRutasx    : string string de rutas para excluir del resultado. *
  * @return string or Array mensaje de respuesta.                              *
  *****************************************************************************/
  function getRutas( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, 
                     $fNotRutasx = NULL, $fCodTransp = NULL, $fCodTipdes = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "cod_transp" => $fCodTransp, "cod_tipdes" => $fCodTipdes );

    $fValidator = new Validator( $fInputs, "getrutas_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
		    //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getRutas" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

		    //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTransp ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
            
          if( 1 != $fCodTipdes && 2 != $fCodTipdes )
            throw new Exception( "El tipo de Despacho debe ser 1 (Urbano), 2 (Nacional).", "6001" );
            
          $fSeguim = new Seguim( $fExcept );
          $fRutasx = $fSeguim -> getRutas( $fCodCiuori, $fCodCiudes, $fNotRutasx, $fCodTransp, $fCodTipdes );
          if( !is_array( $fRutasx ) )
          {
            $fReturn['cod_respon'] = '6001';
            $fReturn['msg_respon'] = $fRutasx;
            return $fReturn;
          }
          else
          {
            $fReturn['arr_rutasx'] = $fRutasx['rutas'];
            $fReturn['cod_tipser'] = $fRutasx['tipser']['cod_tipser'];
            $fReturn['tie_contro'] = $fRutasx['tipser']['tie_contro'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retornaron rutas satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos. USUARIO=".$fNomUsuari."CLAVE=". $fPwdClavex, "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
  /***********************************************************************
  * Funcion Obtiene el tipo de servicio y el tiempo contratado           *
  * @fn getTipser                                                        *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTransp    : string Codigo transportadora.                *
  * @param $fCodTipdes    : string Codigo Tipo despacho.                 *
  * @return string mensaje de respuesta.                                 *
  ***********************************************************************/
  function getTipser( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodTipdes = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                      "cod_tipdes" => $fCodTipdes );

    $fValidator = new Validator( $fInputs, "gettipser_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getTipser" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTransp ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
            
          if( 1 != $fCodTipdes && 2 != $fCodTipdes )
            throw new Exception( "El tipo de Despacho debe ser 1 (Urbano), 2 (Nacional).", "6001" );
            
          $fSeguim = new Seguim( $fExcept );
          $fTipser = $fSeguim -> getTipser( $fCodTransp, $fCodTipdes );
          if( $fTipser == FALSE )
          {
            throw new Exception( "No existen configuraciones activas en Sat Trafico para la transportadora", "6001" );
            break;
          }
          else
          {
            $fReturn['cod_tipser'] = $fTipser['cod_tipser'];
            $fReturn['tie_contro'] = $fTipser['tie_contro'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retorno el tipo de servicio satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }

  /***********************************************************************
  * Funcion Obtiene todas las novedades                                  *
  * @fn getRutas                                                         *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @return string or Array mensaje de respuesta.                        *
  ************************************************************************/
  function getNovedades( $fNomUsuari = NULL, $fPwdClavex = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex );

    $fValidator = new Validator( $fInputs, "getnovedades_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getNovedades" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fSeguim = new Seguim( $fExcept );
          
          $fNovedades = $fSeguim -> getNovedades( );
          if( FALSE === $fNovedades )
          {
            throw new Exception( "No se encuentran novedades en Sat Trafico", "6001" );
            break;
          }
          else
          {
            $fReturn['arr_noveda'] = $fNovedades;
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retornaron novedades satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
    
  /***********************************************************************
  * Funcion Inserta una novedad GPS en una aplicacion SAT.               *
  * @fn setNovedadGPS.                                                   *
  * @brief Inserta una novedad GPS en una aplicacion SAT.                *
  * @param $fNomAplica: string Nombre aplicacion.                        *
  * @param $fNumDespac: integer Numero del despacho.                     *
  * @param $fCodNoveda: integer Codigo de la novedad.                    *
  * @param $fFecNoveda: string Fecha de la novedad.                      *
  * @param $fDesNoveda: string Descripcion de la novedad.                *
  * @param $fValLongit: double Valor de la longitud.                     *
  * @param $fValLatitu: double Valor de la latitud.                      *
  * @param $fNomLlavex: string Llave de autenticacion.                   *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setNovedadGPS( $fCodTransp = NULL, $fNumDespac = NULL, $fCodNoveda = NULL, $fFecNoveda = NULL, 
                          $fDesNoveda = NULL, $fValLongit = NULL, $fValLatitu = NULL, $fNomLlavex = NULL,
                          $fCodOperad = NULL, $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodManifi = NULL,
                          $fNumPlacax)
  {

    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( 'InterfGPS' );
    $fExcept -> SetParams( "Faro", "setNovedadGPS" );

    $fDatValida = array( "nom_aplica" => $fNomAplica, "num_despac" => $fNumDespac, "cod_noveda" => $fCodNoveda, 
                         "fec_noveda" => $fFecNoveda, "des_noveda" => $fDesNoveda, "val_longit" => $fValLongit,
                         "val_latitu" => $fValLatitu, "nom_llavex" => $fNomLlavex, "cod_operad" => $fCodOperad, 
                         "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_manifi" => $fCodManifi,
                         "num_placax" => $fNumPlacax); 

    
    $fValidator = new Validator( $fDatValida, "novedadgps_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
       //if( $fNomAplica == 'satt_faro' )
          include_once( AplKon );
        /*else
        {
          throw new Exception( "Aplicacion no encontrada o invalida", "1999" );
          break;
        }*/
        
        
        // ------------------------------------------   Validacion Cuando se resibe la llave o usr y pwd -----------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------------------
        $mFlag = false;
        if(($fNomLlavex == NULL && $fNomUsuari == NULL && $fPwdClavex == NULL) || ($fNomLlavex != NULL && $fNomUsuari != NULL && $fPwdClavex != NULL) )       
          throw new Exception( "Debe enviar solo la LLAVE &oacute; USUARIO y CLAVE para continuar.", "1002" );        
        else if($fNomLlavex != NULL && (($fNomUsuari != NULL && $fPwdClavex == NULL) || ($fNomUsuari == NULL && $fPwdClavex != NULL)))  
          throw new Exception( "Debe enviar solo la llave.", "1002" );       
        else if($fNomLlavex != NULL && ($fNomUsuari == NULL && $fPwdClavex == NULL))
        {
          if( $fNomLlavex != '3c09f78c210a18b686ae2540b0d12358' )         
            throw new Exception( "No esta autorizado para usar este metodo, Envie la llave correcta.", "1002" );          
          else 
            $mFlag = true;        
        }
        else if($fNomLlavex == NULL && ($fNomUsuari != NULL && $fPwdClavex == NULL) ||($fNomUsuari == NULL && $fPwdClavex != NULL) )    
          throw new Exception( "Debe enviar El Usuario y Clave.", "1002" );     
        else if($fNomLlavex == NULL && ($fNomUsuari != NULL && $fPwdClavex != NULL))
        {
          if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )                   
            $mFlag = true;
          else
            throw new Exception( "No esta autorizado para usar este metodo, Clave y/o usuario incorrectos.", "1002" );
        }
        // -----------------------------------------------------  Fin Validación Usuarios y Llave  ---------------------------------------------------------
        // -------------------------------------------------------------------------------------------------------------------------------------------------
        
        if($mFlag === TRUE)
        {   
            
            $fDespac = new DespacSat( $fExcept );
            $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
            
          
            if( $fCodOperad != NULL && $fNomUsuari != NULL && $fPwdClavex != NULL )
            {
              // Validacion del operador
              $fCodOperadExist = $fDespac -> getOperadorGps( $fCodOperad );
              if( $fCodOperadExist === FALSE )
              {
                throw new Exception( "El Operador ".$fCodOperad." no existe.", "6001" );
                break;
              }
              
              //Validación del numero del codigo de  manifiesto y placa
              if ( $fCodManifi == NULL && $fNumPlacax == NULL  ) {             
                throw new Exception( "Debe enviar el Numero de Plantilla o Numero de Manifiesto Y la matricula del Vehiculo.", "6001" ); break;}        
             
              if ( $fCodManifi == NULL ) {             
                throw new Exception( "Debe enviar el Numero de Plantilla o Numero de Manifiesto.", "6001" ); break;}              
              
              if ( $fNumPlacax == NULL ) {
                throw new Exception( "Debe enviar la Matricula del Vehiculo.", "6001" ); break; }            
              
              //Busca el despacho Con placa, manifiesto y trasportadora
              $fQuerySelNumDes = "SELECT a.num_despac 
                               FROM ".BASE_DATOS.".tab_despac_despac a, 
                                    ".BASE_DATOS.".tab_despac_vehige b 
                              WHERE a.num_despac = b.num_despac 
                                AND a.cod_manifi = '".$fCodManifi."' 
                                AND b.cod_transp = '".$fCodTransp."' 
                                AND b.num_placax = '".$fNumPlacax."' 
                                AND a.ind_anulad != 'A' 
                                AND a.fec_llegad IS NULL";

              $fConsult -> ExecuteCons( $fQuerySelNumDes );
              if( 0 != $fConsult -> RetNumRows() ) {
                $mRetunDespac = $fConsult -> RetMatrix( "a" );
                $fNumDespac = $mRetunDespac[0]["num_despac"];                
              }
              else 
              {              
                throw new Exception( "No se encontro un despacho para el Manifiesto No: ".$fCodManifi.", con placa: ".$fNumPlacax." en Sat Trafico.", "6001" ); break; 
              }              
            }
            
            $fFecNoveda = NULL == $fFecNoveda ? date( "Y-m-d H:i" ) : $fFecNoveda;            
            $fFecActual = date( "Y-m-d H:i" );         
            
            //Se define la cantidad de minutos de holgura con respecto a la hora de otros servidores
            $fMinuteHolgur = 10;
            
            $fFecNovedaTime = strtotime( $fFecNoveda );
            $fFecActualTime = strtotime( $fFecActual );
            
            $fDifMinute = abs( ( $fFecActualTime - $fFecNovedaTime ) / 60);
            
            if( ( $fFecActualTime < $fFecNovedaTime ) && ( $fDifMinute > $fMinuteHolgur ) )
            {
              throw new Exception( "Fecha de la novedad ".$fFecNoveda." mayor que la fecha actual ".$fFecActual. " por ".$fDifMinute." minutos", "6001" );
              break;
            }
            else if( $fDifMinute <= $fMinuteHolgur )
            {
              $fFecNoveda = $fFecActual;
            }

            
            if( !$fDespac -> despacInRout( $fNumDespac ) )
            {
              throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
              break;
            }

            $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp );

            $fNoveda = $fDespac -> getNovedadGps( $fCodNoveda, $fNomNoveda, $fCodOperad , $fNomLlavex);
            if( FALSE === $fNoveda )
            {
              if($fCodNoveda != '4999' && $fCodOperad != null)
                throw new Exception( "Novedad ". $fCodNoveda.' Con operador '.$fCodOperad ." no existente.", "6001" );
              else
                throw new Exception( "Novedad ". $fCodNoveda.' '.$fNomNoveda ." no existente.", "6001" );
              
              break;
            }
            

            //Se inserta el sitio reporte GPS
            if($fCodNoveda != '4999' && $fCodOperad != null)
            {
              $fNomSitiox = 'REPORTE PROTEKTO';
              $fNomUsuari = 'InterfPROTEKTO';
            }
            else
            {
              $fNomSitiox = 'REPORTE GPS';
              $fNomUsuari = 'InterfGPS';
            }

            $fQuerySelCodSitiox = "SELECT cod_sitiox ".
                                  "FROM ".BASE_DATOS.".tab_despac_sitio ".
                                 "WHERE nom_sitiox = '".$fNomSitiox."' ";
                                 
            $fConsult -> ExecuteCons( $fQuerySelCodSitiox );

            if( 0 != $fConsult -> RetNumRows() )
            {
              $fCodSitiox = $fConsult -> RetMatrix( "a" );
              $fCodSitiox = $fCodSitiox[0]['cod_sitiox'];
            }
            else
            {
              $fQuerySelMaxSitio = "SELECT MAX( cod_sitiox ) + 1 AS cod_sitiox ".
                                       "FROM ".BASE_DATOS.".tab_despac_sitio";
    
              $fConsult -> ExecuteCons( $fQuerySelMaxSitio );
              $fMaxSitiox = $fConsult -> RetMatrix( "a" );
              $fMaxSitiox = $fMaxSitiox[0]['cod_sitiox'];
              if( $fMaxSitiox == 0 )
                $fMaxSitiox = 1;
              
               $fQueryInsSitio = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio ".
                                            "( cod_sitiox, nom_sitiox ) ".
                                     "VALUES ( '".$fMaxSitiox."', '".$fNomSitiox."' ) ";
    
              $resultInsSitio = $fConsult -> ExecuteCons( $fQueryInsSitio, "BRC" );
              if( $resultInsSitio )
              $fCodSitiox = $fMaxSitiox;
            }
          
          
            $fCodContro = $fDespac -> getPcontroFromDespac( $fCodContro, $fNomContro, $fNumDespac );

            $fQuerySelFecLastCont = "SELECT MAX( d.fec_contro ) AS fec_contro ".
                                      "FROM ".BASE_DATOS.".tab_despac_contro d ".
                                     "WHERE  d.num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQuerySelFecLastCont );
            $fFecLastCont = $fConsult -> RetMatrix( "a" );

            $fQuerySelFecLastNov = "SELECT MAX( d.fec_noveda ) AS fec_contro ".
                                     "FROM ".BASE_DATOS.".tab_despac_noveda d ".
                                    "WHERE  d.num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQuerySelFecLastNov );
            $fFecLastNov = $fConsult -> RetMatrix( "a" );

            if( NULL == $fFecLastCont[0]["fec_contro"] && NULL == $fFecLastNov[0]["fec_contro"] )
            {
              $fQueryCodContro = "SELECT a.cod_contro ".
                                   "FROM ".BASE_DATOS.".tab_despac_contro a ".
                                  "WHERE a.num_despac = '".$fNumDespac."' ";

              $fConsult -> ExecuteCons( $fQueryCodContro );  
            }
            elseif( $fFecLastCont[0]["fec_contro"] >= $fFecLastNov[0]["fec_contro"] )
            {
              $fQueryCodContro = "SELECT a.cod_contro ".
                                   "FROM ".BASE_DATOS.".tab_despac_contro a ".
                                  "WHERE a.num_despac = '".$fNumDespac."' ".
                                    "AND a.fec_contro = '".$fFecLastCont[0]["fec_contro"]."' ";

              $fConsult -> ExecuteCons( $fQueryCodContro );
            }
            else
            {
              $fQueryCodContro = "SELECT c.val_duraci ".
                                   "FROM ".BASE_DATOS.".tab_despac_noveda a, ".
                                        "".BASE_DATOS.".tab_despac_vehige b, ".
                                        "".BASE_DATOS.".tab_genera_rutcon c ".
                                  "WHERE a.num_despac = b.num_despac ".
                                    "AND a.cod_contro = c.cod_contro ".
                                    "AND b.cod_rutasx = c.cod_rutasx ".
                                    "AND a.num_despac = '".$fNumDespac."' ".
                                    "AND a.fec_noveda = '".$fFecLastNov[0]["fec_contro"]."' ";

              $fConsult -> ExecuteCons( $fQueryCodContro );
              $fResultCodContro = $fConsult -> RetMatrix( "a" );

              $fQueryCodContro = "SELECT a.cod_contro ".
                                   "FROM ".BASE_DATOS.".tab_despac_seguim a, ".
                                        "".BASE_DATOS.".tab_despac_vehige b, ".
                                        "".BASE_DATOS.".tab_genera_rutcon c ".
                                 "WHERE a.num_despac = b.num_despac ".
                                   "AND a.cod_contro = c.cod_contro ".
                                   "AND b.cod_rutasx = c.cod_rutasx ".
                                   "AND a.num_despac = '".$fNumDespac."' ".
                                   "AND c.val_duraci > '".$fResultCodContro[0]["val_duraci"]."' ORDER BY c.val_duraci ASC ";
    
              $fConsult -> ExecuteCons( $fQueryCodContro );
            }

            if( 0 == $fConsult -> RetNumRows() )
            {
              $fQueryCodContro = "SELECT a.cod_contro, c.val_duraci ".
                                   "FROM ".BASE_DATOS.".tab_despac_seguim a, ".
                                        "".BASE_DATOS.".tab_despac_vehige b, ".
                                        "".BASE_DATOS.".tab_genera_rutcon c ".
                                  "WHERE a.num_despac = b.num_despac ".
                                    "AND a.cod_contro = c.cod_contro ".
                                    "AND b.cod_rutasx = c.cod_rutasx ".
                                    "AND a.num_despac = '".$fNumDespac."' ".
                                    "AND a.fec_planea = ( SELECT MIN( d.fec_planea ) ".
                                                           "FROM ".BASE_DATOS.".tab_despac_seguim  d ".
                                                          "WHERE d.num_despac = '".$fNumDespac."' ) ";

              $fConsult -> ExecuteCons( $fQueryCodContro );
            }

            $fResultCodContro = $fConsult -> RetMatrix( "a" );
            $fCodContro = $fResultCodContro[0]["cod_contro"];

            //Se obtiene el ultimo consecutivo del despacho
            $query = "SELECT MAX( a.cod_consec ) AS cod_consec
                       FROM ".BASE_DATOS.".tab_despac_contro a,
                            ".BASE_DATOS.".tab_despac_vehige b
                      WHERE a.num_despac = b.num_despac AND
                            a.cod_rutasx = b.cod_rutasx AND
                            b.num_despac = ".$fNumDespac."";
        
            $consec = $fConsult -> ExecuteCons( $query );
            $ultimo = $fConsult -> RetMatrix( 'a' );
        
            $ultimo = $ultimo[0]['cod_consec'];
            $fConse = $ultimo + 1;
            
            $fSitioF = "cod_sitiox,";
            $fSitioV = "'".$fCodSitiox."',";
          
            $fQueryInsNoved = "INSERT INTO ".BASE_DATOS.".tab_despac_contro ".
                                "( num_despac,  cod_rutasx, cod_contro, cod_consec, ".
                                  "obs_contro,  val_longit, val_latitu, cod_noveda, ".
                                  "tiem_duraci, fec_contro, ".$fSitioF." usr_creaci, ".
                                  "fec_creaci ) ".
                              "VALUES ( '".$fNumDespac."', '".$fDesp["cod_rutasx"]."', '".$fCodContro."', '".$fConse."', ".
                                       "'".$fDesNoveda."', '".$fValLongit."', '".$fValLatitu."', '".$fNoveda["cod_noveda"]."', ".
                                       "'0', '".$fFecNoveda."', ".$fSitioV." '".$fNomUsuari."', ".
                                       "NOW() ) ";

            $mUpdateSet[] = "cod_consec = '".$fConse."'";
            $mUpdateSet[] = "usr_modifi = '".$fNomUsuari."'";
            $mUpdateSet[] = "fec_modifi = NOW()";
            

            //Sat basico tiene nuevo modulo de Seguimiento
            $mUpdateSet[] = "fec_manala = NULL";
            $mUpdateSet[] = "fec_ultnov = '".$fFecNoveda."'";
          
            $fQueryDespacDes = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                   "SET ";
            $fQueryDespacDes .= implode( ', ', $mUpdateSet );
            $fQueryDespacDes .= " WHERE num_despac = '".$fNumDespac."' ";

            $resultInsNoveda = $fConsult -> ExecuteCons( $fQueryInsNoved, "BR" );
            
            if( $resultInsNoveda && $fConsult -> ExecuteCons( $fQueryDespacDes, "RC" ) )
              $fReturn = "code_resp:1000; msg_resp:Se inserto la novedad Pc de forma satisfactoria.";
            else
              $fReturn = "code_resp:1999; msg_resp:No se inserto la novedad Pc.";
        }
        
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
  
   /***********************************************************************
  * Funcion Inserta una novedad PC en una aplicacion SAT.                *
  * @fn setNovedadPC.                                                    *
  * @brief Inserta una novedad PC en una aplicacion SAT.                 *
  * @param $fNomUsuari: string Usuario.                                  *
  * @param $fPwdClavex: string Clave.                                    *
  * @param $fCodTransp: string Codigo de la transportadora.              *
  * @param $fNumManifi: string Numero del manifiesto                     *
  * @param $fNumPlacax: string Numero de la placa.                       *
  * @param $fCodNoveda: string Codigo de la novedad.                     *
  * @param $fCodContro: string Codigo puest control.                     *
  * @param $fTimDuraci: string Tiempo duracion.                          *
  * @param $fFecNoveda: string Fecha de la novedad.                      *
  * @param $fDesNoveda: string Descripcion de la novedad.                *
  * @param $fNomNoveda: string Nombre novedad.                           *
  * @param $fNomContro: string Nombre puesto de control.                 *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setNovedadPC( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fNumManifi = NULL,
                         $fNumPlacax = NULL, $fCodNoveda = NULL, $fCodContro = NULL, $fTimDuraci = NULL, 
                         $fFecNoveda = NULL, $fDesNoveda = "", $fNomNoveda = NULL, $fNomContro = NULL,
                         $fNomSitiox = NULL, $fNumViajex = NULL )
  {

    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setNovedadPC" );

    $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                         "num_manifi" => $fNumManifi, "num_placax" => $fNumPlacax, "cod_contro" => $fCodContro, 
                         "cod_noveda" => $fCodNoveda, "fec_noveda" => $fFecNoveda, "des_noveda" => $fDesNoveda, 
                         "tim_duraci" => $fTimDuraci, "nom_contro" => $fNomContro, "nom_noveda" => $fNomNoveda,
                         "nom_sitiox" => $fNomSitiox );

    $fValidator = new Validator( $fDatValida, "novedad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fFecNoveda = NULL == $fFecNoveda ? date( "Y-m-d H:i" ) : $fFecNoveda;
        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        //Se consulta el numero de despacho
        $fQueryNumDespac = "SELECT a.num_despac ".
                             "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                  "".BASE_DATOS.".tab_despac_vehige b ".
                            "WHERE a.num_despac = b.num_despac ".
                              "AND a.cod_manifi = '".trim( $fNumManifi )."' ".
                              "AND b.cod_transp = '".trim( $fCodTransp )."' ".
                              "AND b.num_placax = '".trim( $fNumPlacax )."' ".
                              "AND a.fec_salida IS NOT NULL ".
                              "AND a.fec_llegad IS NULL ".
                              "AND b.ind_activo = 'S' ";
        $fConsult -> ExecuteCons( $fQueryNumDespac );

        if( 0 != $fConsult -> RetNumRows() )
        {
          $fResultNumDespac = $fConsult -> RetMatrix( "a" );
          $fNumDespac = $fResultNumDespac[0]["num_despac"];   
        }
        else
        {
          throw new Exception( "No se encuentra numero de despacho.", "1999" );
          break;
        }

        $fDespac = new DespacSat( $fExcept );

        if( !$fDespac -> despacInRout( $fNumDespac ) )
        {
          throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
          break;
        }

        $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp );

        $fNoveda = $fDespac -> getNovedad( $fCodNoveda, $fNomNoveda );
        if( FALSE === $fNoveda )
        {
          throw new Exception( "Novedad ". $fNomNoveda ." no existente.", "6001" );
          break;
        }
        
        //valida si el nombre del sitio existe
         $query = "SELECT cod_sitiox ".
                    "FROM ".BASE_DATOS.".tab_despac_sitio ".
                   "WHERE nom_sitiox = '".$fNomSitiox."'";
         $consulta = $fConsult -> ExecuteCons( $query );
         $sitio = $fConsult -> RetMatrix( );
         
         //$fConsult -> ExecuteCons( "SELECT 1", "BR" );
         $fConsult -> StartTrans();
         
         if( !$sitio )
         {
           $query = "SELECT MAX( cod_sitiox ) ".
                      "FROM ".BASE_DATOS.".tab_despac_sitio ";
           $consulta = $fConsult -> ExecuteCons( $query );
           $sitio = $fConsult -> RetMatrix( );
           
           $maxsit = $sitio[0][0] + 1;
           $query = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio".
                   "( cod_sitiox, nom_sitiox ) VALUES ( $maxsit, '".$fNomSitiox."' ) ";
           if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
             throw new Exception( "Error en Insert.", "3001" );
         }
         else
         {
           $maxsit = $sitio[0][0];
         }
         //Se hacen ajustes para las alarmas cuando la novedad mantiene alarma
         $tieadi = $fTimDuraci;
        $query = "SELECT fec_manala, fec_ultnov, fec_salida ".
                   "FROM ".BASE_DATOS.".tab_despac_despac
                   WHERE num_despac = '".$fNumDespac."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $fec_manala = $fConsult -> RetMatrix( );
        $fec_ultnov = "'".$fec_manala[0][1]."'";
        $fec_salida = "'".$fec_manala[0][2]."'";
        $fec_manala = "'".$fec_manala[0][0]."'";
     
        $query = "SELECT ind_manala, ind_notsup, nom_noveda ".
        //$query = "SELECT ind_manala".
                   " FROM ".BASE_DATOS.".tab_genera_noveda
                   WHERE cod_noveda = '".$fCodNoveda."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $ind_manala = $fConsult -> RetMatrix( );
        
        $ind_notsup = $ind_manala[0][1]; //Jorge 120769
        $nove = $ind_manala[0][2]; 
        $ind_manala = $ind_manala[0][0];
     
        $query = "( SELECT tiem_duraci, fec_contro
                      FROM  ".BASE_DATOS.".tab_despac_contro
                     WHERE num_despac = ".$fNumDespac." )
                   UNION
                  ( SELECT tiem_duraci, fec_noveda
                      FROM  ".BASE_DATOS.".tab_despac_noveda 
                     WHERE num_despac = ".$fNumDespac." )
                  ORDER BY 2 DESC
                     LIMIT 1 ";
                    
        $consulta = $fConsult -> ExecuteCons( $query );
        $valretras = $fConsult -> RetMatrix( );
        $valretras = $valretras[0][0];
     
        if( $ind_manala == '0' )
        {
          $fec_manala = "NULL"; 
        }
        else
        {
          if( $fec_manala == "''" )
          {
            if( $fec_ultnov == '' ||  $fec_ultnov == "''" )
              $fec_manala = $fec_salida;
            else
              $fec_manala = $fec_ultnov; 
          }
          if( $valretras == "" )
            $valretras = 0;
          $tieadi = $valretras;
        }
        if( $fec_manala == '' )
          $fec_manala = "NULL";
        if( $fTimDuraci == '' )
          $tieadi = 0;
        
        if ($ind_notsup == 1)//Jorge 120769
        {
            $query = "SELECT a.cod_tercer,a.abr_tercer ,b.num_placax, c.con_telmov, d.abr_tercer AS nom_conduc, c.cod_manifi
                                  FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                                              " . BASE_DATOS . ".tab_despac_vehige b,
                                              " . BASE_DATOS . ".tab_despac_despac c,
                                              " . BASE_DATOS . ".tab_tercer_tercer d
                              WHERE b.num_despac = '" . $fNumDespac . "' 
                                    AND b.num_despac = c.num_despac
                                    AND b.cod_conduc = d.cod_tercer
                                    AND a.cod_tercer = b.cod_transp";
            $empre = $fConsult -> ExecuteCons( $query );
            $empre = $fConsult -> RetMatrix( );
            
            $info .="EMPRESA: " . $empre[0][1] . " \n";
            $info .="DESPACHO: " . $fNumDespac . " \n";
            $info .="MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
            $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
            $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
            $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
            $info .="SITIO DE SEGUIMIENTO: " . $fNomSitiox . " \n";
            $info .="FECHA DE LA NOVEDAD: " . $fFecNoveda . " \n";
            $info .="NOVEDAD: " . $nove . " \n";
            $info .="USUARIO: " . $fNomUsuari . " \n";
            $info .="OBSERVACION:  \n";
            $info .=" " . $fDesNoveda . " \n";

            $asunto = "NOTIFICACION SUPERVISOR";

            //mail(MAIL_SUPERVISORES, $asunto, $info, 'From: faroavansat@eltransporte.com');
            mail(FarMai, $asunto, $info, 'From: faroavansat@eltransporte.com');
        }
        
          //Manejo de consecutivo de verificacion
        $query = "SELECT cod_verpcx
                    FROM ".BASE_DATOS.".tab_config_parame ";
        $consulta =  $fConsult -> ExecuteCons( $query );
        $fCodVerifi = $fConsult -> RetMatrix( );
        $fCodVerifi = (int)$fCodVerifi[0]['cod_verpcx'] + 1;

        # Upate Consec Verificacion OC 
        $query = "UPDATE ".BASE_DATOS.".tab_config_parame
                     SET cod_verpcx = '".$fCodVerifi."'";                     
        if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
          throw new Exception( "Error en Update.", "3001" );



        $fQueryInsNoved = "INSERT INTO ".BASE_DATOS.".tab_despac_noveda ".
                              "( num_despac, cod_rutasx,  cod_contro, cod_noveda, fec_noveda, ".
                                "des_noveda, tiem_duraci, cod_sitiox, usr_creaci, fec_creaci, cod_verpcx ) ".
                            "VALUES ( '".$fNumDespac."', '".$fDesp["cod_rutasx"]."', '".$fCodContro."', ".
                                     "'".$fNoveda["cod_noveda"]."', '".$fFecNoveda."', 'INTERFAZ - ".$fDesNoveda."', ".
                                     "'".$tieadi."', '".$maxsit."', '".$fNomUsuari."', NOW(), '".$fCodVerifi."' )";
         if( $fConsult -> ExecuteCons( $fQueryInsNoved, "R" ) === FALSE )
           throw new Exception( "Error en Insert.", "3001" );
        

        
        $condAcargoFaro = $fNoveda["cod_noveda"] == NovAcarFar ? " ind_defini = '0', " : '';
        #$condAcargoFaro = $fNoveda["cod_noveda"] == NovAcarFar ? " ind_defini = '0', " : "  ind_defini = '1', ";
        
        $fQueryDespacDes = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                              "SET cod_conult = ".$fCodContro.", ".
                                  "cod_ultnov = ".$fNoveda["cod_noveda"].", ".
                                  "fec_ultnov = '".$fFecNoveda."', ".
                                  "fec_manala = ".$fec_manala.", ".
                                  $condAcargoFaro.
                                  "usr_ultnov = '".$fNomUsuari."' ".
                            "WHERE num_despac = '".$fNumDespac."' ";

        if( $fConsult -> ExecuteCons( $fQueryDespacDes, "R" ) === FALSE )
          throw new Exception( "Error en Insert.", "3001" );
        
        $query= "SELECT cod_contro
                   FROM ".BASE_DATOS.".tab_despac_seguim
                  WHERE num_despac = '".$fNumDespac."' 
                    AND cod_rutasx = '".$fDesp["cod_rutasx"]."'
               ORDER BY fec_planea";
        $consulta = $fConsult -> ExecuteCons( $query );
        $seguim = $fConsult -> RetMatrix( );        
        for( $i = 0; $i < sizeof( $seguim ); $i++ )
        {
          $msg .= "\n\n".$fCodContro.'='.$seguim[$i][0];
          if( $fCodContro == $seguim[$i][0] )
          {
            break;
          }
          else
          {
            $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                         SET ind_estado = '0'
                       WHERE num_despac = '".$fNumDespac."' 
                         AND cod_rutasx = '".$fDesp["cod_rutasx"]."' 
                         AND cod_contro = '".$seguim[$i][0]."' ";
                         
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
          }
        }
        if( $fNoveda["cod_noveda"] == '9998' )
        {
          $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                       SET ind_estado = '0'
                     WHERE num_despac = '".$fNumDespac."' AND
                           cod_rutasx = '".$fDesp["cod_rutasx"]."'  ";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
          $query = "SELECT b.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_seguim b
                   WHERE num_despac = '".$fNumDespac."' AND
                         fec_creaci =( SELECT MAX( fec_creaci ) FROM ".BASE_DATOS.".tab_despac_seguim WHERE num_despac = '".$fNumDespac."' LIMIT 1 )";
          $consulta =  $fConsult -> ExecuteCons( $query );
          $cod_rutasx = $fConsult -> RetMatrix( );
          $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                       SET cod_rutasx = ".$cod_rutasx[0][0].",
                           usr_modifi = '".$fNomUsuari."',
                           fec_modifi = NOW()
                     WHERE num_despac = ".$fNumDespac."";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
        }
        
                //se ajusta fec_planea y fec_alarma
        $query = "SELECT a.fec_planea
                    FROM ".BASE_DATOS.".tab_despac_seguim a
                   WHERE a.num_despac = ".$fNumDespac."
                     AND a.cod_rutasx = ".$fDesp["cod_rutasx"]."
                     AND a.cod_contro = ".$fCodContro."";
        $consulta =  $fConsult -> ExecuteCons( $query );
        $planru_c = $fConsult -> RetMatrix( );

        $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                     SET fec_alarma = '".$fFecNoveda."',
                         usr_modifi = '".$fNomUsuari."',
                         fec_modifi = NOW()
                   WHERE num_despac = ".$fNumDespac."
                     AND cod_rutasx = ".$fDesp["cod_rutasx"]."
                     AND cod_contro = ".$fCodContro."";

        if ( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );

        //trae el plan de ruta a actualizar
        $query = "SELECT a.cod_contro,( TIME_TO_SEC( TIMEDIFF( a.fec_planea, '".$planru_c[0][0]."' ) ) / 60 )
                    FROM ".BASE_DATOS.".tab_despac_seguim a
                   WHERE a.num_despac = ".$fNumDespac."
                     AND a.cod_rutasx = ".$fDesp["cod_rutasx"]."
                     AND a.fec_planea > '".$planru_c[0][0]."'
                ORDER BY a.fec_planea";

        $consulta =  $fConsult -> ExecuteCons( $query );
        $planru_p = $fConsult -> RetMatrix( );

        $tiemacu = 0;
     
        for( $i = 0; $i < sizeof( $planru_p ); $i++ )
        {
          $tiemacu = $planru_p[$i][1] + $tieadi;

          $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                       SET fec_alarma = DATE_ADD('".$fFecNoveda."', INTERVAL ".$tiemacu." MINUTE),
                           usr_modifi = '".$fNomUsuari."',
                           fec_modifi = now()
                     WHERE num_despac = ".$fNumDespac."
                       AND cod_rutasx = ".$fDesp["cod_rutasx"]."
                       AND cod_contro = ".$planru_p[$i][0]."";

          if ( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
        }

        
        # fecha: 2015-08-06
        # userx: nelson.liberato
        # Proceso para notificar las novedades que ingresan de los PC a las empresas terceras
        # Para este caso solo va a servir para la interfaz con MCT, codigo operadora Interf 58
        # Bug: se debe crear una libreria que se encargue del envio ws a los otros proveedores (silogtran, onlinetool, destino seguro etc)
        #      se puede guiar con el script ../satt_standa/despac/InsertNovedad.inc
        # si presenta error comentarear todo el if
        $mVerifyInterf = 'SELECT b.cod_operad, b.cod_transp, b.nom_usuari, b.clv_usuari  
                            FROM '.BASE_DATOS.'.tab_despac_vehige a, '.BASE_DATOS.'.tab_interf_parame b
                            WHERE a.cod_transp = b.cod_transp AND
                                  b.cod_operad = "58" AND
                                  b.ind_estado = "1" AND
                                  a.num_despac = "'.$fNumDespac.'" ';
        $mVerifyInterf =  $fConsult -> ExecuteCons( $mVerifyInterf );
        $mVerifyInterf =  $fConsult -> RetMatrix( "a");
        if(sizeof($mVerifyInterf) > 0)
        {
            $mQueryMct = "SELECT a.num_placax,b.cod_manifi 
                FROM ".BASE_DATOS.".tab_despac_vehige a,
                     ".BASE_DATOS.".tab_despac_despac b
               WHERE a.num_despac = " . $fNumDespac . " AND
                     a.num_despac = b.num_despac ";
        
            $mMct =  $fConsult -> ExecuteCons( $mQueryMct );
            $mMct =  $fConsult -> RetMatrix( "a" );


   


            # Consulta del puesto Padre de la homologacion
            $query = "SELECT  a.cod_contro
                        FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
                       WHERE a.cod_homolo = '".$fCodContro."'  
                         OR a.cod_contro =  '".$fCodContro."' ";
            
            $mControPadre = $fConsult -> ExecuteCons($query);
            $mControPadre = $fConsult -> RetMatrix("a");


            # Consulta nombre, puesto control -----------------
            $mQuerySelNomPc = "SELECT nom_contro  
                                 FROM ".BASE_DATOS.".tab_genera_contro  
                                WHERE cod_contro = '".$mControPadre[0]["cod_contro"]."' ";

            $consulta = $fConsult -> ExecuteCons($mQuerySelNomPc);
            $mNomPc = $fConsult->RetMatrix("a");


            # Imagenes de las fotos de un despacho
            $mQuery = 'SELECT bin_fotoxx, bin_fotox2 FROM '.BASE_DATOS.'.tab_despac_images WHERE num_despac = "'.$fNumDespac.'" AND cod_contro = "'.$fCodContro.'"  ';
            $consulta = $fConsult -> ExecuteCons($mQuery);
            $mFotoDespac = $fConsult->RetMatrix("a");


            # Agrega datos adicionales en array para enviar en caso de notificacion de error
            $mAditional["num_placax"] = $mMct[0]["num_placax"];
            $mAditional["tip_noveda"] = "PC";

            # Trama de datos a enviar a MCT
            $mDataMCT = array(
                'manifiesto_codigo'    =>  urlencode( $mMct[0]["cod_manifi"] ),            
                'ptoc_codigo'          =>  urlencode( $mControPadre[0]["cod_contro"] ),
                'ptoc_nombre'          =>  urlencode( ($mNomPc[0]["nom_contro"] == '' ? 'Children PC WS: '.$mControPadre[0]["cod_contro"] : $mNomPc[0]["nom_contro"] ) ),
                'ptoc_fecha'           =>  urlencode( date("Y-m-d H:i", strtotime( $fFecNoveda ) ) ),
                'ptoc_observacion'     =>  urlencode( ($fDesNoveda == '' ? 'Registro WS: '.$mControPadre[0]["cod_contro"].' - '.$mNomPc[0][0] : 'Registro WS: '.$fDesNoveda ) ),
                'ptoc_imagenconductor' =>  urlencode( "{$mFotoDespac[0]['bin_fotoxx']}" ),
                'ptoc_imagenvehiculo'  =>  urlencode( "{$mFotoDespac[0]['bin_fotox2']}" ),
                'ptoc_imagenprecinto'  =>  urlencode( NULL )
            );      
            # Incluye la clase encargada de la interfaz toca importarla de satt_standa/lib/ del standa del framework de faro        
            include_once("/var/www/html/ap/satt_standa/lib/InterfMct.inc");
            $mMct = new InterfMct($fConsult, $mDataMCT, $mAditional);
            $mReturn = $mMct -> getResponMct();
        }

        # Replicacion de novedad a Crona como NC 2016-07-25 nelson

        # Valida NUmero de viaje -----------------------------------------------------------------------------------------------------------------
        if($fNumViajex != NULL){         
          $mValidaViaje = ValidaNumViajeExist($fCodTransp, $fNumViajex, $fNumManifi, $fNumPlacax, $fConsult);          
          if($mValidaViaje[cod_respon] == '1000') {
              $mNoveCorona =  setNovedadNC( $fNomUsuari, $fPwdClavex, $mValidaViaje["msg_respon"]["cod_transp"], $mValidaViaje["msg_respon"]["cod_manifi"],
                                            $mValidaViaje["msg_respon"]["num_placax"], $fCodNoveda, $fCodContro, $fTimDuraci, 
                                            $fFecNoveda, $fDesNoveda , $fNomNoveda, $fNomContro,
                                            $fNomSitiox, NULL );
              
               
          }
        }

        # Retorna string de mensaje satisfactorio
        $fConsult -> Commit();
        $fReturn = "code_resp:1000; msg_resp:Se inserto la novedad PC de forma satisfactoria; cod_verifi: ".$fCodVerifi;



      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
   /***********************************************************************
  * Funcion Inserta los puestos para cambio de ruta en tab_despac_seguim. *
  * @fn setCambioRuta.                                                    *
  * @param $fNomUsuari: string Usuario.                                   *
  * @param $fPwdClavex: string Clave.                                     *
  * @param $fCodTransp: string Codigo de la transportadora.               *
  * @param $fNumManifi: string Numero del manifiesto                      *
  * @param $fNumPlacax: string Numero de la placa.                        *
  * @param $fCodRutasx: string Codigo de la novedad.                      *
  * @param $fTimDuraci: string Codigo de la novedad.                      *
  * @param $fCodPcbase: string Codigo puestos de empalme.                 *
  * @param $fCodContrs: string Codigo puestos de control.                 *
  * @return string mensaje de respuesta.                                  *
  ************************************************************************/
  function setCambioRuta( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fNumManifi = NULL,
                          $fNumPlacax = NULL, $fCodRutasx = NULL, $fTimDuraci = NULL, $fCodPcbase = NULL,
                          $fCodContrs = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setCambioRuta" );

    $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                         "num_manifi" => $fNumManifi, "num_placax" => $fNumPlacax, "cod_rutasx" => $fCodRutasx, 
                         "tim_duraci" => $fTimDuraci, "cod_pcbase" => $fCodPcbase, "cod_contrs" => $fCodContrs );
    $fValidator = new Validator( $fDatValida, "cambioruta_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        //Se consulta el numero de despacho
        $fQueryNumDespac = "SELECT a.num_despac ".
                             "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                  "".BASE_DATOS.".tab_despac_vehige b ".
                            "WHERE a.num_despac = b.num_despac ".
                              "AND a.cod_manifi = '".trim( $fNumManifi )."' ".
                              "AND b.cod_transp = '".trim( $fCodTransp )."' ".
                              "AND b.num_placax = '".trim( $fNumPlacax )."' ".
                              "AND a.fec_salida IS NOT NULL ".
                              "AND a.fec_llegad IS NULL ".
                              "AND b.ind_activo = 'S' ";
        $fConsult -> ExecuteCons( $fQueryNumDespac );

        if( 0 != $fConsult -> RetNumRows() )
        {
          $fResultNumDespac = $fConsult -> RetMatrix( "a" );
          $fNumDespac = $fResultNumDespac[0]["num_despac"];   
        }
        else
        {
          throw new Exception( "No se encuentra numero de despacho.", "1999" );
          break;
        }

        $fDespac = new DespacSat( $fExcept );

        if( !$fDespac -> despacInRout( $fNumDespac ) )
        {
          throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
          break;
        }

        $fec_cambru = date("Y-m-d H:i:s");
        $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp );

        $fConsult -> StartTrans();
        
        $query = "SELECT b.cod_contro,b.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_vehige a,
                         ".BASE_DATOS.".tab_despac_seguim b
                   WHERE a.num_despac = '".$fNumDespac."' 
                     AND b.num_despac = a.num_despac 
                     AND b.cod_rutasx = a.cod_rutasx";
        $consulta = $fConsult -> ExecuteCons( $query, "R" );
        $antplaru = $fConsult -> RetMatrix();
      
        $query = "SELECT a.val_duraci
                    FROM ".BASE_DATOS.".tab_genera_rutcon a
                   WHERE a.cod_rutasx = '".$fCodRutasx."' AND
                         a.cod_contro = '".$fCodPcbase."'";
        $consulta = $fConsult -> ExecuteCons( $query );
        $pcduracibase = $fConsult -> RetMatrix();
        $tiemacu = 0;
  
        $fCodContrs = explode( ',', $fCodContrs);
  
        for( $i = 0; $i < sizeof( $fCodContrs ); $i++ )
        {
          $query = "SELECT a.val_duraci
                      FROM ".BASE_DATOS.".tab_genera_rutcon a
                     WHERE a.cod_rutasx = '".$fCodRutasx."' AND
                           a.cod_contro = '".$fCodContrs[$i]."'";
          $consulta = $fConsult -> ExecuteCons( $query );
          $pcduraci = $fConsult -> RetMatrix();
          if( $fCodPcbase == $fCodContrs[$i] )
          {
            $tiempcum = $fTimDuraci;
          }
          else
          {
            $tiempcum = $tiempcum + ( $pcduraci[0][0] - $pcduracibase[0][0] ) + $fTimDuraci;
          }
          $query = "SELECT DATE_ADD( '".$fec_cambru."', INTERVAL ".$tiempcum." MINUTE )";
          $consulta = $fConsult -> ExecuteCons( $query );
          $timemost = $fConsult -> RetMatrix();
          $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
                    ( num_despac, cod_contro, cod_rutasx, fec_planea,
                      fec_alarma, ind_estado, usr_creaci, fec_creaci )
             VALUES (".$fNumDespac.", ".$fCodContrs[$i].", ".$fCodRutasx.",
                    '".$timemost[0][0]."', '".$timemost[0][0]."', '1', '".$fNomUsuari."',
                    '".$fec_cambru."' )";
        if ( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
          throw new Exception( "Error en Insert.", "3001" );
        }
     
        $fConsult -> Commit();
        $fReturn = "code_resp:1000; msg_resp:Se inserto el cambio de ruta satisfactoriamente.";
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
   /***********************************************************************
  * Funcion Inserta una novedad NC en una aplicacion SAT.                *
  * @fn setNovedadNC.                                                    *
  * @brief Inserta una novedad NC en una aplicacion SAT.                 *
  * @param $fNomUsuari: string Usuario.                                  *
  * @param $fPwdClavex: string Clave.                                    *
  * @param $fCodTransp: string Codigo de la transportadora.              *
  * @param $fNumManifi: string Numero del manifiesto                     *
  * @param $fNumPlacax: string Numero de la placa.                       *
  * @param $fCodNoveda: string Codigo de la novedad.                     *
  * @param $fCodContro: string Codigo puest control.                     *
  * @param $fTimDuraci: string Tiempo duracion.                          *
  * @param $fFecNoveda: string Fecha de la novedad.                      *
  * @param $fDesNoveda: string Descripcion de la novedad.                *
  * @param $fNomNoveda: string Nombre novedad.                           *
  * @param $fNomContro: string Nombre puesto de control.                 *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setNovedadNC( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fNumManifi = NULL,
                         $fNumPlacax = NULL, $fCodNoveda = NULL, $fCodContro = NULL, $fTimDuraci = NULL, 
                         $fFecNoveda = NULL, $fDesNoveda = "", $fNomNoveda = NULL, $fNomContro = NULL,
                         $fNomSitiox = NULL, $fNumViajex = NULL, $fFotNoveda = NULL)
  {

    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setNovedadNC" );

    $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                         "num_manifi" => $fNumManifi, "num_placax" => $fNumPlacax, "cod_contro" => $fCodContro, 
                         "cod_noveda" => $fCodNoveda, "fec_noveda" => $fFecNoveda, "des_noveda" => $fDesNoveda, 
                         "tim_duraci" => $fTimDuraci, "nom_contro" => $fNomContro, "nom_noveda" => $fNomNoveda,
                         "nom_sitiox" => $fNomSitiox );

    $fValidator = new Validator( $fDatValida, "novedad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fFecNoveda = NULL == $fFecNoveda ? date( "Y-m-d H:i" ) : $fFecNoveda;
        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        $fResultPosee = NULL;
        # Consulta si la transportadora es una tercera de corona, usa true o false
        # si true= puede guardar novedades sin importar que el despacho tenga llegada(Finalizado)
        $fQueryPoseedCoro = " SELECT cod_poseed FROM ".BASE_DATOS.".tab_despac_corona WHERE cod_poseed = '".$fCodTransp."' GROUP BY cod_poseed ";
        $fConsult -> ExecuteCons( $fQueryPoseedCoro );        
        $fResultPosee = $fConsult -> RetArray();  
        # Se filtra segun si el tercero le transporta a corona; busca el despacho

        $mFinaly = $fResultPosee["cod_poseed"] != NULL ? true : false; 

        //Se consulta el numero de despacho
        $fQueryNumDespac = "SELECT a.num_despac ".
                             "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                  "".BASE_DATOS.".tab_despac_vehige b ".
                            "WHERE a.num_despac = b.num_despac ".
                              "AND a.cod_manifi = '".trim( $fNumManifi )."' ".
                              "AND b.cod_transp = '".trim( $fCodTransp )."' ".
                              "AND b.num_placax = '".trim( $fNumPlacax )."' ".
                              "AND a.fec_salida IS NOT NULL ";
        $fQueryNumDespac .=   ($mFinaly == true ? " " : "AND a.fec_llegad IS NULL "); # si true trae el despacho sin importar si esta finalizado
        $fQueryNumDespac .=   "AND b.ind_activo = 'S' ";

        $fConsult -> ExecuteCons( $fQueryNumDespac );
        if( 0 != $fConsult -> RetNumRows() )
        {
          $fResultNumDespac = $fConsult -> RetMatrix( "a" );
          $fNumDespac = $fResultNumDespac[0]["num_despac"];
        }
        else
        {
          //Se consulta el numero de despacho Consolidador
          $fQueryNumDespacConsol = "SELECT a.cod_despad 
                                      FROM ".BASE_DATOS.".tab_consol_despac a 
                                INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
                                        ON a.cod_deshij = b.num_despac 
                                INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
                                        ON b.num_despac = c.num_despac 
                                     WHERE b.cod_manifi = '".trim( $fNumManifi )."' 
                                       AND c.cod_transp = '".trim( $fCodTransp )."'
                                       AND c.num_placax = '".trim( $fNumPlacax )."'
                                       AND b.fec_salida IS NOT NULL
                                       #AND c.ind_activo = 'S' ";
          $fQueryNumDespacConsol .=   ($mFinaly == true ? " " : " AND b.fec_llegad IS NULL "); # si true trae el despacho sin importar si esta finalizado

          
          $fConsult -> ExecuteCons( $fQueryNumDespacConsol );
          if( 0 != $fConsult -> RetNumRows() )
          {
            $fResultNumDespac = $fConsult -> RetMatrix( "a" );
            # Coloca el despacho padre - Consolidado, en caso que sea un consolidado
            $fNumDespac = $fResultNumDespac[0]["cod_despad"];
          }else{
            # Si no encuentra un consolidado y no encuentra el despacho vigente debe acabar la ejecucion con el catch
            throw new Exception( "No se encuentra numero de despacho.", "1999" );
            break;
          }
        }

        $fDespac = new DespacSat( $fExcept );

        # Se parchea el if para que valide cuando la empresa no sea tercera de corona
 
        if( !$fDespac -> despacInRout( $fNumDespac ) && $mFinaly == false )
        {
          throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
          break;
        }

        $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp ); 

            
         
        $fNoveda = $fDespac -> getNovedad( $fCodNoveda, $fNomNoveda );
        if( FALSE === $fNoveda )
        {
          throw new Exception( "Novedad ". $fNomNoveda ." no existente.", "6001" );
          break;
        }
        
        # Se parcha este pedaso ya que cuando se trate de una empresa tercera de corona debe reportar antes del puesto habilitado
        # por el moemnto solo es para mct pero se debe consultar las empresas terceras de corona para que entre al if y cambie el codigo PC
        if( $fCodTransp == '830004861' || $fCodTransp == '860068121')
        {
          # Trae el puesto habilitado y lo cambia por el que entra, el que nos envian
          $mCodControHab = getNextPC($fNumDespac, $fExcept);
          $fCodContro = $mCodControHab["cod_contro"];
          $fNomSitiox = $fNomSitiox == NULL ? $mCodControHab["nom_contro"] : $fNomSitiox;          
        } 

        
        if( ( $fCodContro === '' || $fCodContro === NULL ) && ( $fNomContro === '' || $fNomContro === NULL ) )
        {
         //Se obtiene el ultimo cod_contro
          $fQueryCodContro = "(SELECT cod_contro, fec_contro, 'nc' FROM ".BASE_DATOS.".tab_despac_contro WHERE num_despac = '".$fNumDespac."')
                                UNION
                              (SELECT cod_contro, fec_noveda, 'pc' FROM ".BASE_DATOS.".tab_despac_noveda WHERE num_despac = '".$fNumDespac."')
                              ORDER BY 2 DESC";
          $fConsult -> ExecuteCons( $fQueryCodContro );
          
          
          if( 0 == $fConsult -> RetNumRows() )
          {
           //Si no hay novedades se busca el primer puesto del plan de ruta
            $fQueryCodContro = "SELECT a.cod_contro ".
                                 "FROM ".BASE_DATOS.".tab_despac_seguim a ".
                                "WHERE a.num_despac = '".$fNumDespac."' ".
                                  "AND a.fec_planea = ( SELECT MIN( d.fec_planea ) ".
                                                         "FROM ".BASE_DATOS.".tab_despac_seguim  d ".
                                                        "WHERE d.num_despac = '".$fNumDespac."' ) ";

            $fConsult -> ExecuteCons( $fQueryCodContro );
          }
          
          $fResultCodContro = $fConsult -> RetMatrix( "a" );
          $fCodContro = $fResultCodContro[0]["cod_contro"];
        }
        elseif( $fCodContro == '0' )
        {
          $fCodContro = $fDespac -> getPcontroFromDespac( $fCodContro, $fNomContro, $fNumDespac );
          if( FALSE === $fCodContro )
          {
            throw new Exception( "Puesto de control ".$fNomContro." no existente.", "6001" );
            break;
          }
        }

        
        
        #mail("nelson.liberato@intrared.net", "Puesto COntrol MCT Habilitado", $fCodTransp."\nPuesto Habilitado: ".var_export($mCodControHab, true) );

        $fQueryCodConsec = "SELECT MAX( cod_consec ) AS cod_consec ".
                             "FROM ".BASE_DATOS.".tab_despac_contro ".
                            "WHERE num_despac = '".$fNumDespac."' ".
                              "AND cod_rutasx = '".$fDesp["cod_rutasx"]."' ";

        $fConsult -> ExecuteCons( $fQueryCodConsec );
        $fResultCodConsec = $fConsult -> RetMatrix( "a" );

        if( 0 != $fConsult -> RetNumRows() )
          $fConse = $fResultCodConsec[0]["cod_consec"]+1;
        else  
          $fConse = 1;

        //valida si el nombre del sitio existe
        $query = "SELECT cod_sitiox ".
                   "FROM ".BASE_DATOS.".tab_despac_sitio ".
                  "WHERE nom_sitiox = '".$fNomSitiox."'";
        $consulta = $fConsult -> ExecuteCons( $query );
        $sitio = $fConsult -> RetMatrix( );
       
        $fConsult -> StartTrans();
       
        if( !$sitio )
        {
          $query = "SELECT MAX( cod_sitiox ) ".
                     "FROM ".BASE_DATOS.".tab_despac_sitio ";
          $consulta = $fConsult -> ExecuteCons( $query );
          $sitio = $fConsult -> RetMatrix( );
         
          $maxsit = $sitio[0][0] + 1;
          $query = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio".
                  "( cod_sitiox, nom_sitiox ) VALUES ( $maxsit, '".$fNomSitiox."' ) ";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert 1.", "3001" );
        }
        else
        {
          $maxsit = $sitio[0][0];
        }
         
        //Se hacen ajustes para las alarmas cuando la novedad mantiene alarma
        $tieadi = $fTimDuraci;
        $query = "SELECT fec_manala, fec_ultnov, fec_salida ".
                   "FROM ".BASE_DATOS.".tab_despac_despac
                   WHERE num_despac = '".$fNumDespac."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $fec_manala = $fConsult -> RetMatrix( );
        $fec_ultnov = "'".$fec_manala[0][1]."'";
        $fec_salida = "'".$fec_manala[0][2]."'";
        $fec_manala = "'".$fec_manala[0][0]."'";
     
        $query = "SELECT ind_manala, ind_notsup, nom_noveda ".
        //$query = "SELECT ind_manala".
                   "FROM ".BASE_DATOS.".tab_genera_noveda
                   WHERE cod_noveda = '".$fCodNoveda."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $ind_manala = $fConsult -> RetMatrix( );
        $ind_notsup = $ind_manala[0][1];//Jorge 120769
        $nove = $ind_manala[0][2];
        $ind_manala = $ind_manala[0][0];
        
     
        $query = "( SELECT tiem_duraci, fec_contro
                      FROM  ".BASE_DATOS.".tab_despac_contro
                     WHERE num_despac = ".$fNumDespac." )
                   UNION
                  ( SELECT tiem_duraci, fec_noveda
                      FROM  ".BASE_DATOS.".tab_despac_noveda 
                     WHERE num_despac = ".$fNumDespac." )
                  ORDER BY 2 DESC
                     LIMIT 1 ";
                    
        $consulta = $fConsult -> ExecuteCons( $query );
        $valretras = $fConsult -> RetMatrix( );
        $valretras = $valretras[0][0];
     
        if( $ind_manala == '0' )
        {
          $fec_manala = "NULL"; 
        }
        else
        {
          if( $fec_manala == "''" )
          {
            if( $fec_ultnov == '' ||  $fec_ultnov == "''" )
              $fec_manala = $fec_salida;
            else
              $fec_manala = $fec_ultnov; 
          }
          if( $valretras == "" )
            $valretras = 0;
          $tieadi = $valretras;
        }
        if( $fec_manala == '' )
          $fec_manala = "NULL";
        if( $fTimDuraci == '' )
          $tieadi = 0;
          
        if ($ind_notsup == 1)//Jorge 120769
        {
            $query = "SELECT a.cod_tercer,a.abr_tercer ,b.num_placax, c.con_telmov, d.abr_tercer AS nom_conduc, c.cod_manifi
                                  FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                                              " . BASE_DATOS . ".tab_despac_vehige b,
                                              " . BASE_DATOS . ".tab_despac_despac c,
                                              " . BASE_DATOS . ".tab_tercer_tercer d
                              WHERE b.num_despac = '" . $fNumDespac . "' 
                                    AND b.num_despac = c.num_despac
                                    AND b.cod_conduc = d.cod_tercer
                                    AND a.cod_tercer = b.cod_transp";
            $empre = $fConsult -> ExecuteCons( $query );
            $empre = $fConsult -> RetMatrix( );
            
            $info .="EMPRESA: " . $empre[0][1] . " \n";
            $info .="DESPACHO: " . $fNumDespac . " \n";
            $info .="MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
            $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
            $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
            $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
            $info .="SITIO DE SEGUIMIENTO: " . $fNomSitiox . " \n";
            $info .="FECHA DE LA NOVEDAD: " . $fFecNoveda . " \n";
            $info .="NOVEDAD: " . $nove . " \n";
            $info .="USUARIO: " . $fNomUsuari . " \n";
            $info .="OBSERVACION:  \n";
            $info .=" " . $fDesNoveda . " \n";

            $asunto = "NOTIFICACION SUPERVISOR";

            //mail(MAIL_SUPERVISORES, $asunto, $info, 'From: faroavansat@eltransporte.com');
            mail(FarMai, $asunto, $info, 'From: faroavansat@eltransporte.com');
        }
        # Parche si la novedad es para corona 

        $fQueryInsNoved = "INSERT INTO ".BASE_DATOS.".tab_despac_contro ".
                            "( num_despac , cod_rutasx , cod_contro , cod_consec , ".
                              "obs_contro , cod_noveda , tiem_duraci , fec_contro , ".
                              "cod_sitiox, usr_creaci , fec_creaci ) ".
                          "VALUES ( '".$fNumDespac."', '".$fDesp["cod_rutasx"]."', ".
                                   "'".$fCodContro."', '".$fConse."', ".
                                   "'INTERFAZ - ".str_replace("'", "", $fDesNoveda)." ', '".$fNoveda["cod_noveda"]."', ".
                                   "'".$tieadi."', '".$fFecNoveda."', ".
                                   "'".$maxsit."', '".$fNomUsuari."', NOW() ) ON DUPLICATE KEY UPDATE fec_creaci = DATE_ADD( VALUES(fec_creaci), INTERVAL 1 SECOND ) ";
        if( $fConsult -> ExecuteCons( $fQueryInsNoved, "R" ) === FALSE ) {
          throw new Exception( "Error en Insert 2.".$fCodTransp." Pre: ".$fQueryInsNoved, "3001" );
        }

        

        #$condAcargoFaro = $fNoveda["cod_noveda"] == NovAcarFar ? " ind_defini = '0', " : ''; 


        switch ($fNoveda["cod_noveda"]) 
        {
             case '9996':
               $condAcargoFaro = " ind_defini = '1', ";
               break;
             case '9995':
               $condAcargoFaro = " ind_defini = '0', ";
               break;
             default:
               $condAcargoFaro = "   ";
               break;
        }

   
        $fQueryDespacDes = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                              "SET cod_consec = '".$fConse."', ".
                                  "cod_conult = '".$fCodContro."', ".
                                  "usr_ultnov = '".$fNomUsuari."', ".
                                  "fec_ultnov = '".$fFecNoveda."', ".
                                  "cod_ultnov = '".$fNoveda["cod_noveda"]."', ".
                                  "fec_manala = ".$fec_manala.", ".
                                  "usr_modifi = '".$fNomUsuari."', ".
                                  $condAcargoFaro.
                                  "fec_modifi = NOW() ".
                            "WHERE num_despac = '".$fNumDespac."' ";

   

        if( $fConsult -> ExecuteCons( $fQueryDespacDes, "R" ) === FALSE )
          throw new Exception( "Error en Insert 3.", "3001" );
        
        $query= "SELECT cod_contro
                   FROM ".BASE_DATOS.".tab_despac_seguim
                  WHERE num_despac = '".$fNumDespac."' 
                    AND cod_rutasx = '".$fDesp["cod_rutasx"]."'
               ORDER BY fec_planea";
        $consulta = $fConsult -> ExecuteCons( $query );
        $seguim = $fConsult -> RetMatrix( );          
        for( $i = 0; $i < sizeof( $seguim ); $i++ )
        {
          if( $fCodContro == $seguim[$i][0] )
          {
            break;
          }
          else
          {
            $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                         SET ind_estado = '0'
                       WHERE num_despac = '".$fNumDespac."' 
                         AND cod_rutasx = '".$fDesp["cod_rutasx"]."' 
                         AND cod_contro = '".$seguim[$i][0]."' ";
                         
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert 4.", "3001" );
          }
        }
        
        if( $fNoveda["cod_noveda"] == '9998' )
        {
          $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                       SET ind_estado = '0'
                     WHERE num_despac = '".$fNumDespac."' AND
                           cod_rutasx = '".$fDesp["cod_rutasx"]."'  ";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert 5.", "3001" );
            
          $query = "SELECT b.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_seguim b
                   WHERE num_despac = '".$fNumDespac."' AND
                         fec_creaci =( SELECT MAX( fec_creaci ) FROM ".BASE_DATOS.".tab_despac_seguim WHERE num_despac = '".$fNumDespac."' LIMIT 1 )";
          $consulta =  $fConsult -> ExecuteCons( $query );
          $cod_rutasx = $fConsult -> RetMatrix( );
          $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                       SET cod_rutasx = ".$cod_rutasx[0][0].",
                           usr_modifi = '".$fNomUsuari."',
                           fec_modifi = NOW()
                     WHERE num_despac = ".$fNumDespac."";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert 6.", "3001" );
        }
        
        $tieAdi2 = 30 + (int)$tieadi;
        
        //Se recalcula la fecha planeada
//        $query = "SELECT DATE_ADD( fec_planea, INTERVAL ".$tieAdi2." MINUTE )
//                    FROM ".BASE_DATOS.".tab_despac_seguim 
//                   WHERE num_despac = '".$fNumDespac."' 
//                     AND cod_contro = '".$fCodContro."'
//                     AND cod_rutasx = '".$fDesp["cod_rutasx"]."'";
//        $consulta = $fConsult -> ExecuteCons( $query );
//        $fec_planea = $fConsult -> RetMatrix( );
//        
//        $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim 
//                     SET fec_planea = '".$fec_planea[0][0]."'
//                   WHERE num_despac = '".$fNumDespac."'
//                     AND cod_contro = '".$fCodContro."'
//                     AND cod_rutasx = '".$fDesp["cod_rutasx"]."'";
//        if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
//              throw new Exception( "Error en Insert.", "3001" );
 
        $query = "SELECT b.cod_contro, DATE_ADD( b.fec_alarma, INTERVAL ".$tieAdi2." MINUTE )
                   FROM ".BASE_DATOS.".tab_despac_seguim b
                  WHERE b.num_despac = ".$fNumDespac." 
                    AND b.fec_planea >= ( SELECT a.fec_planea
                                            FROM ".BASE_DATOS.".tab_despac_seguim a
                                           WHERE a.num_despac = b.num_despac 
                                             AND a.cod_contro = ".$fCodContro." 
                                             AND a.cod_rutasx = '".$fDesp["cod_rutasx"]."')
               GROUP BY 1 
               ORDER BY 2";
       
        $consulta = $fConsult -> ExecuteCons( $query );
        $planrut = $fConsult -> RetMatrix( );
       
        $tiemacu = $tieadi;
        $actact = 0;
        if( $ind_manala == '0' )
          for($i = 0; $i < sizeof( $planrut ); $i++)
          {
            $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                        SET fec_alarma = '".$planrut[$i][1]."',
                            usr_modifi = '".$fNomUsuari."',
                            fec_modifi = NOW()
                      WHERE num_despac = '".$fNumDespac."' 
                        AND cod_rutasx = '".$fDesp["cod_rutasx"]."' 
                        AND cod_contro = '".$planrut[$i][0]."' ";
    
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
                throw new Exception( "Error en Insert 7.", "3001" );
          }

            /////////////////////////////Registro de fotos//////////////////////////////


           if( $fFotNoveda != NULL ){

              $fotos = base64_decode($fFotNoveda);
              $fotos = json_decode($fotos);
              $pc = getNextPC($fNumDespac, $fExcept);

              if($fotos -> ind_fotoxx == 'cumplidos'){ 

                foreach ($fotos as $llave => $foto) {
                  foreach ($foto as $key => $value) {

                    $query = "SELECT MAX( a.num_consec ) as num_consec
                                FROM ".BASE_DATOS.".tab_cumpli_despac a
                               WHERE a.num_despac = '" . $fNumDespac . "'
                                 AND a.num_docume = '" . $llave . "'";
      
                    $consec = $fConsult -> ExecuteCons( $query );  
                    $consec = $fConsult -> RetMatrix( );                  
                    $consec = ((int)$consec[0]['num_consec']) + 1;
 
       
                    $insert = "INSERT INTO  ".BASE_DATOS.".tab_cumpli_despac
                                (
                                    num_despac, num_docume, num_consec,
                                    url_imagex, usr_creaci, fec_creaci
                                )
                                VALUES 
                                (
                                    '" . $fNumDespac . "', '" . $llave . "', '" . $consec . "',
                                    '" . $value . "', '" . $fNomUsuari . "',  NOW()
                                )";

                    //mail("miguel.romero@intrared.net", "if ok", $insert);
                    if( $fConsult -> ExecuteCons( $insert ) === FALSE )
                      throw new Exception( "Error en Insert 8.", "3001" );
                  }

                } 
              }else{

                foreach ($fotos as $key => $foto) {

                  $query = "SELECT MAX( a.num_consec ) as num_consec
                              FROM ".BASE_DATOS.".tab_despac_images a
                             WHERE a.num_despac = '".$fNumDespac."'
                               AND a.cod_contro = '".$pc['cod_contro']."'";
    
                  $consec = $fConsult -> ExecuteCons( $query );  
                  $consec = $fConsult -> RetMatrix( );                  
                  $consec = ((int)$consec[0]['num_consec']) + 1;
     
                  $insert = "INSERT INTO  ".BASE_DATOS.".tab_despac_images 
                              (
                                  num_despac , cod_contro , num_consec, usr_creaci ,
                                  fec_creaci ,bin_fotox2
                              )
                              VALUES 
                              (
                                  '".$fNumDespac."',  '".$pc['cod_contro']."', '".$consec."',  '" . $fNomUsuari . "',  
                                  NOW(), '".$foto."'
                              )";   
                  if( $fConsult -> ExecuteCons( $insert ) === FALSE )
                    throw new Exception( "Error en Insert 8.", "3001" );
                } 
              }
          } 
   
          /////////////////////////////Registro de fotos//////////////////////////////



        $fConsult -> Commit();

        
        # Proceso de matriz de comunicacion por novedad y trasnportadora
        # valida si el codigo de la novedad genera protoco, si esi ejecuta protocolos
        $mProtoc = new Protocol ($fConsult  , $fNomUsuari, $fCodTransp, $fNumDespac , $fNumManifi, $fNumPlacax, 
                                 $fCodNoveda, $fCodContro, $fTimDuraci, $fFecNoveda, $fDesNoveda, $fNomNoveda, 
                                 $fNomContro, $fNomSitiox, $fNumViajex);
       $mRespon = $mProtoc -> getResponse();

        if($mRespon["cod_respon"] == '1000' ) {
          $mMsgProtoc = "; ind_protoc:". $mRespon["msg_respon"];
        }

        $fReturn = "code_resp:1000; msg_resp:Se inserto la novedad NC de forma satisfactoria.".$mMsgProtoc;

        # Valida NUmero de viaje -----------------------------------------------------------------------------------------------------------------
        if($fNumViajex != NULL){
         
          $mValidaViaje = ValidaNumViajeExist($fCodTransp, $fNumViajex, $fNumManifi, $fNumPlacax, $fConsult);          
          if($mValidaViaje[cod_respon] == '1000') {
              $mNoveCorona =  setNovedadNC( $fNomUsuari, $fPwdClavex, $mValidaViaje["msg_respon"]["cod_transp"], $mValidaViaje["msg_respon"]["cod_manifi"],
                                            $mValidaViaje["msg_respon"]["num_placax"], $fCodNoveda, $fCodContro, $fTimDuraci, 
                                            $fFecNoveda, $fDesNoveda , $fNomNoveda, $fNomContro,
                                            $fNomSitiox, NULL );
              
               
          }
        }


      }
      catch( Exception $e )
      {
         
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.".$e -> getMessage();
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /*! \fn: ValidaNumViajeExist
  *  \brief: Inserta novedad al despacho propio y despacho de corona en caso de exista el viaje
  *  \author: Ing. Nelson Liberato
  *  \date: 1/06/2015
  *  \date modified: 01/06/2015
  *  \param: $Conductor
  *  \param: $CodEmp
  *  \param: $USER
  *  \return array
  */
  function ValidaNumViajeExist( $fCodTransp, $fNumViajex, $fNumManifi, $fNumPlacax, $fConsult)
  {
    try
    {

      $mFindDessat = " SELECT num_dessat FROM ".BASE_DATOS.".tab_despac_corona  WHERE num_despac = '".$fNumViajex."' ";
      $fConsult -> ExecuteCons( $mFindDessat );
      if( 0 != $fConsult -> RetNumRows() )
      {
        $mFindDessat = $fConsult -> RetMatrix( "a" );
        $fNumDespac = $mFindDessat[0]["num_dessat"];   
      }
      else
      {
        throw new Exception( "No se encuentra numero de Viaje ".$fNumViajex.".".$mFindDessat, "1999" );
        break;
      }


      $mQuery = 'SELECT a.num_despac, 
                     b.cod_transp, 
                     a.cod_manifi,
                     b.num_placax 
                FROM ' . BASE_DATOS . '.tab_despac_despac a 
                     INNER JOIN ' . BASE_DATOS . '.tab_despac_vehige b ON a.num_despac = b.num_despac
                     INNER JOIN ' . BASE_DATOS . '.tab_tercer_tercer c ON b.cod_transp = c.cod_tercer                   
               WHERE a.fec_salida IS NOT NULL 
                 AND a.fec_salida <= NOW() 
                /* Nelson AND (a.fec_llegad IS NULL OR a.fec_llegad = "0000-00-00 00:00:00") */
                 AND a.ind_planru = "S" 
                 AND a.ind_anulad = "R"
                 AND b.ind_activo = "S"  
                 AND a.num_despac = "' . $fNumDespac . '" ';
      
      $fConsult -> ExecuteCons( $mQuery );

      if( 0 != $fConsult -> RetNumRows() )
      {
        $fResultNumDespac = $fConsult -> RetMatrix( "a" );
        $fNumDespac = $fResultNumDespac[0];   
      }
      else
      {
        throw new Exception( "No se encuentra numero de despacho del Viaje.", "1999" );
        break;
      }

      return array('cod_respon' => '1000', 'msg_respon' => $fNumDespac );  
    }
    catch(Exception $e)
    {
      return array('cod_respon' => '6001', 'msg_respon' => $e->getMessage() );
    }
  }
 
  
  /**************************************************************************
  * Funcion Copia la Ruta en Sat Trafico.                                   *
  * @fn setRutaFaro.                                                        *
  * @brief Inserta una novedad NC en una aplicacion SAT.                    *
  * @param $fCodTranps: string Nit transportadora.                          *
  * @param $fCodRutasx: string Codigo Ruta.                                 *
  * @param $fNomRutasx: string Nombre Ruta.                                 *
  * @param $fCodPaiori: string Codigo Pais Origen.                          *
  * @param $fCodDepori: string Codigo Departamento Origen.                  *
  * @param $fCodCiuori: string Codigo Ciudad Origen.                        *
  * @param $fCodPaides: string Codigo Pais Destino.                         *
  * @param $fCodDepdes: string Codigo Departamento Destino.                 *
  * @param $fCodCiudes: string Codigo Ciudad Destino.                       *
  * @param $fArrPcontr[cod_contro]: string Codigo del puesto de control.    *
  * @param $fArrPcontr[nom_contro]: string Nombre del puesto de control.    *
  * @param $fArrPcontr[cod_ciudad]: string Codigo de la ciudad del puesto.  *
  * @param $fArrPcontr[dir_contro]: string Direccion del puesto de control. *
  * @param $fArrPcontr[ind_virtua]: string Indicador virtual.               *
  * @param $fArrPcontr[ind_estado]: string Indicador Estado.                *
  * @param $fArrPcontr[ind_urbano]: string Indicador Urbano.                *
  * @param $fArrPcontr[val_duraci]: string Duracion en minutos.             *
  * @param $fArrPcontr[val_distan]: string Distancia en metros.             *
  * @return string mensaje de respuesta o array de rutas y puestos.         *
  ***************************************************************************/
  function setRutaFaro( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                        $fCodRutasx = NULL, $fNomRutasx = NULL, $fCodPaiori = NULL,
                        $fCodDepori = NULL, $fCodCiuori = NULL, $fCodPaides = NULL,
                        $fCodDepdes = NULL, $fCodCiudes = NULL, $fObjPcontr = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setRutaFaro" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, 
                      "cod_rutasx" => $fCodRutasx, "nom_rutasx" => $fNomRutasx, "cod_paiori" => $fCodPaiori,
                      "cod_depori" => $fCodDepori, "cod_ciuori" => $fCodCiuori, "cod_paides" => $fCodPaides,
                      "cod_depdes" => $fCodDepdes, "cod_ciudes" => $fCodCiudes );
    
    $fValidator = new Validator( $fInputs, "setrutafaro_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fReturn = FALSE;
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );
  
          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
  
          //Se convierte de objeto a arreglo
          $i = 0;
          $fArrPcontr = array();
          
          foreach( $fObjPcontr as $fPc )
          {
            $fArrPcontr[$i]['cod_contro'] = $fPc -> cod_contro;
            $fArrPcontr[$i]['nom_contro'] = $fPc -> nom_contro;
            $fArrPcontr[$i]['cod_ciudad'] = $fPc -> cod_ciudad;
            $fArrPcontr[$i]['dir_contro'] = $fPc -> dir_contro;
            $fArrPcontr[$i]['ind_virtua'] = $fPc -> ind_virtua;
            $fArrPcontr[$i]['ind_estado'] = $fPc -> ind_estado;
            $fArrPcontr[$i]['ind_urbano'] = $fPc -> ind_urbano;
            $fArrPcontr[$i]['val_duraci'] = $fPc -> val_duraci;
            $fArrPcontr[$i]['val_distan'] = $fPc -> val_distan;

            $i++;
          }
          
          $InterfTrafico = new InterfTrafico( $fConsult, $fExcept );
          $InterfTrafico -> setNomUsuar( $fNomUsuari );
          $fCodRutfar = $InterfTrafico -> getRutaHomolo( $fCodTranps, $fCodRutasx, $fNomRutasx, 
                                                         $fCodPaiori, $fCodDepori, $fCodCiuori, 
                                                         $fCodPaides, $fCodDepdes, $fCodCiudes, 
                                                         $fArrPcontr );
          
          $fHomoloData = $InterfTrafico -> getHomoloData( $fCodTranps, $fCodRutfar );
          
          if( NULL !== $fHomoloData )
          {
            $fReturn['arr_homolo'] = $fHomoloData;
            $fReturn['cod_rutbas'] = $fHomoloData[0]['cod_rutbas'];
            $fReturn['cod_rutfar'] = $fHomoloData[0]['cod_rutfar'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se copio la ruta satisfactoriamente';
          }
          else
            throw new Exception( "Fallo el copiado de rutas. No hay datos en la homologacion.", "1999" );
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;
      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }

     /**************************************************************************
  * Funcion Actualiza el telefono celular en la informacion de los despachos *
  * @fn setTelConduc                                                         *
  * @brief Se actualizan despachos en ruta asignando el nuevo celular.       *
  * @param $fNomUsuari    : string Usuario.                                  *
  * @param $fPwdClavex    : string Clave.                                    *
  * @param $fCodTranps    : int    Nit Transportadora.                       *
  * @param $fCodConduc    : int    Cedula conductor.                         *
  * @param $fTelConduc    : string Telefono celular.                         *
  * @return string mensaje de respuesta.                                     *
  ****************************************************************************/
  function setTelConduc( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                         $fCodConduc = NULL, $fTelConduc = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, 
                      "cod_conduc" => $fCodConduc, "tel_conduc" => $fTelConduc );

    $fValidator = new Validator( $fInputs, "settelconduc_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setTelConduc" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          //Se consulta el numero de telefono actual
          $fQueryTelChanged = "SELECT a.cod_manifi, b.num_placax, a.con_telmov, a.num_despac
                                 FROM ".BASE_DATOS.".tab_despac_despac a,
                                      ".BASE_DATOS.".tab_despac_vehige b
                           WHERE a.num_despac = b.num_despac
                             AND b.cod_transp = '".$fCodTranps."'
                             AND a.fec_llegad IS NULL
                             AND a.ind_anulad = 'R'
                             AND b.ind_activo = 'S'
                             AND a.fec_salida IS NOT NULL
                             AND a.ind_planru = 'S'
                             AND b.cod_conduc = '".$fCodConduc."'
                             AND a.con_telmov <> '".$fTelConduc."'";

          $fConsult -> ExecuteCons( $fQueryTelChanged );
          $fManifis = $fConsult -> RetMatrix( "a" );
          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el numero que esta guardado es diferente al que estan enviando se hace el update
            $fQueryUpd = "UPDATE ".BASE_DATOS.".tab_despac_despac a,
                                 ".BASE_DATOS.".tab_despac_vehige b
                             SET a.con_telmov = '".$fTelConduc."',
                                 a.fec_modifi = NOW()
                           WHERE a.num_despac = b.num_despac
                             AND b.cod_transp = '".$fCodTranps."'
                             AND a.fec_llegad IS NULL
                             AND a.ind_anulad = 'R'
                             AND b.ind_activo = 'S'
                             AND a.fec_salida IS NOT NULL
                             AND a.ind_planru = 'S'
                             AND b.cod_conduc = '".$fCodConduc."'
                             AND a.con_telmov <> '".$fTelConduc."'";
            
            if( $fConsult -> ExecuteCons( $fQueryUpd, "BRC" ) )
              $fReturn = "code_resp:1000; msg_resp:Se actualizo el celular con exito";
            else
              $fReturn = "code_resp:1999; msg_resp:No se pudo actualizar el celular del conductor en SAT Trafico";
            
            
            
            for( $i = 0, $total = count( $fManifis ); $i < $total; $i++ )
            {
              //Se ingresa la novedad cambio de celular
              $query = "SELECT a.cod_contro, b.nom_contro
                          FROM ".BASE_DATOS.".tab_despac_seguim a,
                               ".BASE_DATOS.".tab_genera_contro b
                         WHERE a.cod_contro = b.cod_contro
                           AND a.ind_estado = '1'
                           AND a.num_despac = '".$fManifis[$i]['num_despac']."'
                      ORDER BY a.fec_planea ASC
                         LIMIT 1";
          
              $fCodContro = $fConsult -> ExecuteCons( $query );
              $fCodContro = $fConsult -> RetMatrix( 'a' );
          
              $resultNoveda = setNovedadNC( $fNomUsuari, $fPwdClavex, $fCodTranps, $fManifis[$i]['cod_manifi'],
                                     $fManifis[$i]['num_placax'], CamCel, $fCodContro[0]['cod_contro'], 0, 
                                     date("Y-m-d H:i"), 
                                     "Se cambio el celular del conductor en la plataforma del cliente. Antes: ".$fManifis[$i]['con_telmov'].". Despues: ".$fTelConduc,
                                     NULL, NULL, $fCodContro[0]['nom_contro'] );
                                     
              //Procesa el resultado del WS
              $mResult  = explode( "; ", $resultNoveda );
              $mCodResp = explode( ":", $mResult[0] );
              $mMsgResp = explode( ":", $mResult[1] );
        
              if( "1000" != $mCodResp[1] )
              {
                //Notifica Errores retornados por el WS
                throw new Exception( $mMsgResp[1], "1999" );
              }
            }
          }
          else
          {
            $fReturn = "code_resp:1000; msg_resp:No es necesario cambiar el celular";
          }


        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
  
    
  /*****************************************************************************
  * Funcion Copia la informacion de la homologacion en trafico.                *
  * @fn setHomoloData.                                                         *
  * @brief Inserta las equivalencias entre puestos y rutas de trafico y basico *
  * @param $fNomUsuari: string Usuario.                                        *
  * @param $fPwdClavex: string Clave.                                          *
  * @param $fCodTranps: string Nit transportadora.                             *
  * @param $fCodRutfar: string Codigo Ruta Faro.                               *
  * @param $fCodRutbas: string codigo Ruta Basico.                             *
  * @param $fObjHomolo[cod_pcxfar]: int Codigo Puesto Trafico.                 *
  * @param $fObjHomolo[cod_pcxbas]: int Codigo Puesto Basico.                  *
  * @return string mensaje de respuesta o array de rutas y puestos.            *
  *****************************************************************************/
  function setHomoloData( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                          $fCodRutfar = NULL, $fCodRutbas = NULL, $fObjHomolo = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setHomoloData" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps,
                      "cod_rutfar" => $fCodRutfar, "cod_rutbas" => $fCodRutbas );
    
    $fValidator = new Validator( $fInputs, "sethomolodata_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fReturn = FALSE;
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );
  
          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
  
          //Se convierte de objeto a arreglo
          $i = 0;
          $fArrPcontr = array();
          
          foreach( $fObjHomolo as $fRow )
          {
            $fArrHomolo[$i]['cod_pcxfar'] = $fRow -> cod_pcxfar;
            $fArrHomolo[$i]['cod_pcxbas'] = $fRow -> cod_pcxbas;
            $fArrHomolo[$i]['ind_estado'] = $fRow -> ind_estado;

            $i++;
          }
          
          if( count( $fArrHomolo ) == 0 )
            throw new Exception( "Se debe enviar minimo un puesto de control para insertar la homologacion.", "6001" );
            
          $fConsult -> StartTrans();
          
          $query = "DELETE FROM ".BASE_DATOS.".tab_homolo_trafico
                     WHERE cod_rutbas = '".$fCodRutbas."'
                       AND cod_rutfar = '".$fCodRutfar."'
                       AND cod_transp = '".$fCodTranps."'";
          
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Delete.", "3001" );
      
          $query = "INSERT INTO ".BASE_DATOS.".tab_homolo_trafico
                    (
                      cod_transp, cod_rutfar, cod_rutbas, 
                      cod_pcxfar, cod_pcxbas, ind_estado, 
                      usr_creaci, fec_creaci
                    )VALUES";

          for( $i = 0, $total = count( $fArrHomolo ); $i < $total; $i++ )
          {
             $queryConsult = "SELECT 1 
                               FROM ".BASE_DATOS.".tab_genera_rutcon
                              WHERE cod_rutasx = '".$fCodRutfar."'
                                AND cod_contro = '".$fArrHomolo[$i]['cod_pcxfar']."'";
                         
            $fConsult -> ExecuteCons( $queryConsult );

            if( 0 != $fConsult -> RetNumRows() )
            {
              $query .= "( '".$fCodTranps."', '".$fCodRutfar."', '".$fCodRutbas."',
                         '".$fArrHomolo[$i]['cod_pcxfar']."', '".$fArrHomolo[$i]['cod_pcxbas']."', '".$fArrHomolo[$i]['ind_estado']."',
                         '".$fNomUsuari."', NOW()
                       ),";
             }
           }
           //Se elimina la ultima coma
           $query = substr( $query, 0, strlen( $query ) - 1 );
      
           if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
             throw new Exception( "Error en Insert.", "3001" );
          
           $fConsult -> Commit();
            
           $fReturn = "code_resp:1000; msg_resp:Se Insertaron los datos de homologacion satisfactoriamente.";
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;
      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
    function getHomoloData( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                            $fCodRutasx = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "getHomoloData" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, 
                      "cod_rutasx" => $fCodRutasx );
    
    $fValidator = new Validator( $fInputs, "gethomolodata_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fReturn = FALSE;
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );
  
          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
  
          $InterfTrafico = new InterfTrafico( $fConsult, $fExcept );
          $InterfTrafico -> setNomUsuar( $fNomUsuari );
          $fHomoloData = $InterfTrafico -> getHomoloData2( $fCodTranps, $fCodRutasx );
          
          if( NULL !== $fHomoloData )
          {
            $fReturn['arr_homolo'] = $fHomoloData;
            $fReturn['cod_rutbas'] = $fHomoloData[0]['cod_rutbas'];
            $fReturn['cod_rutfar'] = $fHomoloData[0]['cod_rutfar'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retorno la informacion de '.count($fHomoloData).' puestos satisfactoriamente';
          }
          else
            throw new Exception( "No hay datos en la homologacion.", "1999" );
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos web10.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;
      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
  /**********************************************************************
  * Funcion Dar salida al despacho                                       *
  * @fn setSalida                                                        *
  * @brief Da salida a un despacho ya ingresado anteriormente.           *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  ************************************************************************/
  function setSalida( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodManifi = NULL, $fNumPlacax  = NULL, $fFecSalida = NULL, $fObsSalida = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setSalida" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                      "cod_manifi" => $fCodManifi,  "num_placax" => $fNumPlacax, "fec_salida" => $fFecSalida,
                      "obs_salida" => $fObsSalida);

    $fValidator = new Validator( $fInputs, "salida_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    


    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      { 
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
        
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {

        $queryPlacaEnRuta = "SELECT a.num_despac 
                      FROM ".BASE_DATOS.".tab_despac_vehige a,
                           ".BASE_DATOS.".tab_despac_despac b 
                     WHERE a.num_placax = '".$fNumPlacax."'
                       AND b.cod_manifi = '".$fCodManifi."'
                       AND a.cod_transp = '".$fCodTransp."' 
                       AND a.num_despac = b.num_despac 
                       AND a.ind_activo = 'S' 
                       AND b.ind_anulad = 'R' 
                       AND b.fec_salida IS NOT NULL 
                       AND b.fec_llegad IS NULL";
                       
          $fConsult -> ExecuteCons( $queryPlacaEnRuta );
          
          if( $fConsult -> RetNumRows() >= 1 )
          {
            //La placa ya se encuentra en ruta
            $fReturn = "code_resp:1999; msg_resp:El Vechiculo Placas ".$fNumPlacax." se Encuentra en Ruta Actualmente, Reporte Primero su Llegada.";
          }
          else
          { 
            $mQueryDespacSalida = "SELECT a.num_despac ".
                                     "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                          "".BASE_DATOS.".tab_despac_vehige b ".
                                    "WHERE a.num_despac = b.num_despac ".
                                      "AND a.cod_manifi = '".$fCodManifi."' ".
                                      "AND b.cod_transp = '".$fCodTransp."' ".
                                      "AND b.num_placax = '".$fNumPlacax."' ".
                                      "AND a.fec_salida IS NULL ".
                                      "AND a.ind_anulad = 'R' ".
                                      "AND a.fec_llegad IS NULL ";
                                 
            $fConsult -> ExecuteCons( $mQueryDespacSalida );
            
            if( 0 != $fConsult -> RetNumRows() )
            {
              $fNumDespac = $fConsult -> RetMatrix( "a" );
              $fConsult -> StartTrans();
                
              $fQueryUpdSalida = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                    "SET fec_salida = '".$fFecSalida."', ".
                                        "fec_salsis = NOW(), ".
                                        "fec_ultnov = '".$fFecSalida."', ".
                                        "obs_salida = '".$fObsSalida."', ".
                                        "usr_modifi = '".$fNomUsuari."', ".
                                        "ind_planru = 'S', ".
                                        "fec_modifi = NOW() ".
                                  "WHERE num_despac = '".$fNumDespac[0]["num_despac"]."' ";
              
              if( $fConsult -> ExecuteCons( $fQueryUpdSalida, "R" ) === FALSE )
                throw new Exception( "Error en Update tab_despac_despac.", "3001" );
              
              $fQueryUpdDesVeh = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                                      "SET ind_activo = 'S', ".
                                          "usr_modifi = '".$fNomUsuari."', ".
                                          "fec_modifi = NOW() ".
                                    "WHERE num_despac = '".$fNumDespac[0]["num_despac"]."' ";
                                
              if( $fConsult -> ExecuteCons( $fQueryUpdDesVeh, "R" ) === FALSE )
                throw new Exception( "Error en Update tab_despac_vehige.", "3001" );
                
              $fConsult -> Commit();
                  
              $fReturn = "code_resp:1000; msg_resp:Se dio salida con exito";
            }
            else
            {
              $fReturn = "code_resp:1000; msg_resp:No se encontro un despacho para el manifiesto ".$fCodManifi." con placa ".$fNumPlacax;
            }
              
              
         
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
 
  function RegistrarCall( $fUsrCallxx = NULL, $fClvCallxx = NULL, $fTokCallxx = NULL, $fNumDespac = NULL, $fNumPlacax = NULL, $fNumTelefo = NULL,
                          $fTieDuraci = NULL, $fIdxLlamad = NULL, $fNomEstado = NULL, $fRutAudiox = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fUsrCallxx );
    $fExcept -> SetParams( "Faro", "RegistrarCall" );

    $mDatenow = date("Y-m-d H:i:s");

    $fMessages["code"] = '1000';

    if( "1000" === $fMessages["code"] )
    {
      try
      { 
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          #break;
        }

        if($fTokCallxx != '*K4czOUZxtt{Y|ND5c=q'){
          #Token No coincide
          return array("cod_respon" => "2001", "msg_respon" => "EL Token no coincide para el despacho: ".$fNumDespac.". Fecha: ".$mDatenow." ", "dat_respon" => NULL );
        }

        # Genera conexion a BD ---------------------------------------------------------------------------------------------------------
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        # array datos del registro de la llamada -----------------------------------------------------------------------------------------------
        $mData = array( "num_despac" => $fNumDespac,"cod_consec" => $fCodConsec ,"cod_transp" => $fCodTransp,"num_placax" => $fNumPlacax ,
                        "num_telefo" => $fNumTelefo,"tie_duraci" => $fTieDuraci ,"idx_llamad" => $fIdxLlamad,"nom_estado" => $fNomEstado ,
                        "rut_audiox" => $fRutAudiox,"usr_creaci" => $fUsrCallxx ,"fec_creaci" => $mDatenow  
                      ) ;
        
      
        if( !getAutentica( $fUsrCallxx, $fClvCallxx, $fExcept ) ) {
          $mData = array_merge( $mData,  array("sql_queryx" => "No Query", "sql_errorx" => "Clave y/o usuario incorrectos" ) );
          $mLog = LogCallcenter($mData, $fConsult);
          throw new Exception("Clave y/o usuario incorrectos.\n Data:\n ".var_export( $mData,true)."\n Query: ".$mLog, 1002); 
        }       

          $fQuery = "SELECT a.cod_transp 
                       FROM ".BASE_DATOS.".tab_despac_vehige a 
                      WHERE a.num_despac = '$fNumDespac'
                      ";
          $fConsult -> ExecuteCons( $fQuery );
          $fResult = $fConsult -> RetMatrix('a');
          $fCodTransp = $fResult[0]['cod_transp'];

          if(!$fCodTransp){
            #No se encontro Transportadora 
            $mData = array_merge( $mData,  array("sql_queryx" => "No Query", "sql_errorx" => "No se encontro transportadora para el despacho: ".$fNumDespac.". \nDatos:".var_export( $mData,true)." \n Fecha: ".$mDatenow  ) );
            LogCallcenter($mData, $fConsult);        
            throw new Exception("No se encontro transportadora para el despacho: ".$fNumDespac.". \nDatos:".var_export( $mData,true)." \n Fecha: ".$mDatenow , 2002);     
          }



          $fQuery = "SELECT cod_consec 
                       FROM ".BASE_DATOS.".tab_despac_callnov 
                      WHERE num_despac = '$fNumDespac' 
                        AND cod_transp = '$fCodTransp' 
                   ORDER BY cod_consec DESC 
                      LIMIT 0, 1
                    ";
          $fConsult -> ExecuteCons( $fQuery ); 
          $fResult = $fConsult -> RetMatrix('a');
          $fCodConsec = $fResult[0]['cod_consec'] == NULL ? '1' : $fResult[0]['cod_consec'] + 1 ;

          $fQuery = "INSERT INTO ".BASE_DATOS.".tab_despac_callnov 
                     (num_despac, cod_consec, cod_transp, 
                      num_placax, num_telefo, tie_duraci, 
                      idx_llamad, nom_estado, rut_audiox, 
                      usr_creaci, fec_creaci) 
                     VALUES 
                     ('$fNumDespac', '$fCodConsec', '$fCodTransp', 
                      '$fNumPlacax', '$fNumTelefo', '$fTieDuraci', 
                      '$fIdxLlamad', '$fNomEstado', '$fRutAudiox', 
                      '$fUsrCallxx', now() )
                    ";

          $mInsert = $fConsult -> ExecuteCons( $fQuery );
          if( !$mInsert ) {
            $mData = array_merge( $mData,  array("sql_queryx" => $fQuery, "sql_errorx" => mysql_error() ) );
            LogCallcenter($mData, $fConsult);
            throw new Exception($fQuery."\n ".var_export( $mInsert, true )." Fecha: ".$mDatenow." Mysql: ".mysql_error() , "2004");            
          }
          else
            $mFinal = array("cod_respon" => "1000", "msg_respon" => "Se registro de manera exitosa", "dat_respon" => " OK " );

          return $mFinal;
          
          
      
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );

          mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net", "Error Registro Llamada Faro -  Despacho: ".$fNumDespac, $e -> getMessage());
          $fReturn = array("cod_respon" => "2004", "msg_respon" => $e -> getMessage(), "dat_respon" => NULL);
            
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }


 

  function RegistrarCallIn( $fUsrCallxx = NULL, $fClvCallxx = NULL, $fTokCallxx = NULL, $fNumTelefo = NULL,
                            $fTieDuraci = NULL, $fIdxLlamad = NULL, $fNomEstado = NULL, $fRutAudiox = NULL,
                            $fCodExtenc = NULL, $fIdxServic = NULL)
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fUsrCallxx );
    $fExcept -> SetParams( "Faro", "RegistrarCallIn" );

    $mDatenow = date("Y-m-d H:i:s");

    $fMessages["code"] = '1000';

    if( "1000" === $fMessages["code"] )
    {
      try
      { 
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          #break;
        }

        if($fTokCallxx != '*K4czOUZxtt{Y|ND5c=q'){
          #Token No coincide
          return array("cod_respon" => "2001", "msg_respon" => "EL Token no coincide para el despacho: ".$fNumDespac.". Fecha: ".$mDatenow." ", "dat_respon" => NULL );
        }

        # Genera conexion a BD ---------------------------------------------------------------------------------------------------------
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        # array datos del registro de la llamada -----------------------------------------------------------------------------------------------
        $mData = array( "cod_consec" => $fCodConsec,"num_telefo" => $fNumTelefo ,"tie_duraci" => $fTieDuraci,
                        "idx_llamad" => $fIdxLlamad,"nom_estado" => $fNomEstado ,"rut_audiox" => $fRutAudiox,
                        "cod_extenc" => $fCodExtenc,"idx_servic" => $fIdxServic ,"usr_creaci" => $fUsrCallxx,"fec_creaci" => $mDatenow  
                      ) ;
        
      
        if( !getAutentica( $fUsrCallxx, $fClvCallxx, $fExcept ) ) {
          $mData = array_merge( $mData,  array("sql_queryx" => "No Query", "sql_errorx" => "Clave y/o usuario incorrectos", "ind_llamad" =>"2" ) );
          $mLog = LogCallcenter($mData, $fConsult);
          throw new Exception("Clave y/o usuario incorrectos.\n Data:\n ".var_export( $mData,true)."\n Query: ".$mLog, 1002); 
        }       

         


          $fQuery = "SELECT cod_consec 
                       FROM ".BASE_DATOS.".tab_despac_callin 
                      WHERE num_telefo = '{$fNumTelefo}'                         
                   ORDER BY cod_consec DESC 
                      LIMIT 0, 1
                    ";
          $fConsult -> ExecuteCons( $fQuery ); 
          $fResult = $fConsult -> RetMatrix('a');
          $fCodConsec = $fResult[0]['cod_consec'] == NULL ? '1' : $fResult[0]['cod_consec'] + 1 ;

          $fQuery = "INSERT INTO ".BASE_DATOS.".tab_despac_callin 
                     (cod_consec, num_telefo, tie_duraci, 
                      idx_llamad, nom_estado, rut_audiox, 
                      cod_extenc, idx_servic, 
                      usr_creaci, fec_creaci) 
                     VALUES 
                     ('{$fCodConsec}', '{$fNumTelefo}', '{$fTieDuraci}', 
                      '{$fIdxLlamad}', '{$fNomEstado}', '{$fRutAudiox}', 
                      '{$fCodExtenc}', '{$fIdxServic}', 
                      '{$fUsrCallxx}', NOW() )
                    ";

          $mInsert = $fConsult -> ExecuteCons( $fQuery );
          if( !$mInsert ) {
            $mData = array_merge( $mData,  array("sql_queryx" => $fQuery, "sql_errorx" => mysql_error(), "ind_llamad" =>"2" ) );
            LogCallcenter($mData, $fConsult);
            throw new Exception($fQuery."\n ".var_export( $mInsert, true )." Fecha: ".$mDatenow." Mysql: ".mysql_error() , "2004");            
          }
          else
            $mFinal = array("cod_respon" => "1000", "msg_respon" => "Se registro de manera exitosa", "dat_respon" => " OK " );

          return $mFinal;
          
          
      
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );

          mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net", "Error Registro Llamada Faro -  Despacho: ".$fNumDespac, $e -> getMessage());
          $fReturn = array("cod_respon" => "2004", "msg_respon" => $e -> getMessage(), "dat_respon" => NULL);
            
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }


  function LogCallcenter($fDataCallcenter = NULL, $fConsult = NULL, $fActionLog = 'Add')
  {
       
        $mLog = 'INSERT INTO '.BASE_DATOS.'.tab_error_calnov 
                 (num_despac, cod_consec, cod_transp, 
                  num_placax, num_telefo, tie_duraci, 
                  idx_llamad, nom_estado, rut_audiox, 
                  fec_creaci, sql_queryx, sql_errorx) 
                 VALUES 
                 (
                  "'.$fDataCallcenter["num_despac"].'", "'.$fDataCallcenter["cod_consec"].'", "'.$fDataCallcenter["cod_transp"].'", 
                  "'.$fDataCallcenter["num_placax"].'", "'.$fDataCallcenter["num_telefo"].'", "'.$fDataCallcenter["tie_duraci"].'", 
                  "'.$fDataCallcenter["idx_llamad"].'", "'.$fDataCallcenter["nom_estado"].'", "'.$fDataCallcenter["rut_audiox"].'", 
                  "'.$fDataCallcenter["fec_creaci"].'", "'.$fDataCallcenter["sql_queryx"].'", "'.$fDataCallcenter["sql_errorx"].'"
                  )';

      $fConsult -> ExecuteCons( $mLog );
      
  }

    /*! \fn: getDataDestin
    *  \brief: Trae la Data para los destinatarios
    *  \author: 
    *  \date: dia/mes/ano
    *  \date modified: dia/mes/ano
    *  \param: mNumDespac  Integer  Numero del despacho
    *  \param: mNomDestin  String   Nombre del destinatario
    *  \par am: mIndGroupx  Boolean  Indicador de agrupacion
    *  \return: Matriz
    */
    function getDestin( $mUser = NULL, $mPass = NULL, $mNumDespac = NULL )
    {

      try {
         
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $mUser );
        $fExcept -> SetParams( "Faro", "getDestin" );

        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        if( !getAutentica( $mUser, $mPass, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }
        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        $mSql = "  SELECT a.nom_destin, c.abr_ciudad, a.dir_destin,
                          a.num_destin, CONCAT(a.fec_citdes,' ',a.hor_citdes) AS fec_citdes2,
                          GROUP_CONCAT(a.num_docume) AS num_docume, a.num_docalt, 
                          a.cod_genera, UPPER(b.abr_tercer) AS abr_tercer,
                          IF(
                                NOW() < DATE_ADD(CONCAT(a.fec_citdes,' ',a.hor_citdes),INTERVAL 15 MINUTE) AND
                                NOW() > DATE_ADD(CONCAT(a.fec_citdes,' ',a.hor_citdes),INTERVAL -15 MINUTE),
                                1,0
                              ) AS ind_descar,
                          IF(a.fec_llecli = '0000-00-00 00:00:00', 1, 0 ) AS ind_entrad, 
                          IF(a.fec_inides IS NULL , 1, 0) AS ind_inides, 
                          IF(a.fec_findes IS NULL , 1, 0) AS ind_findes 
                     FROM ".BASE_DATOS.".tab_despac_destin a 
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer b 
                       ON a.cod_genera = b.cod_tercer 
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad c 
                       ON a.cod_ciudad = c.cod_ciudad 
                    WHERE a.num_despac = '{$mNumDespac}'
                 GROUP BY a.nom_destin 
                 ORDER BY fec_citdes2, a.nom_destin, a.num_docume ";
 

        $fConsult -> ExecuteCons( $mSql );
        $fReturn = array( "dat_respon" => $fConsult -> RetMatrix('a'));
 
        if (sizeof($fReturn) < 1 ) {
          throw new Exception( "No se encuentran los clientes del despacho", "1999" );
        }

         

      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_respon'] = array();


      }
          return $fReturn;
    }
    
    
    /*! \fn: setFecAditio
    *  \brief: Trae la Data para los destinatarios
    *  \author: 
    *  \date: dia/mes/no
    *  \date modified: dia/mes/no
    *  \param: mNumDespac  Integer  Numero del despacho
    *  \param: mNomDestin  String   Nombre del destinatario
    *  \par am: mIndGroupx  Boolean  Indicador de agrupacion
    *  \return: Matriz
    */  
    function setFecAditio( $mUser = NULL, $mPass = NULL, $mNumDespac = NULL, $mTipFecha = NULL, $mNumFecha = NULL, $mNumFactur = NULL)
    {

      try {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $mUser );
        $fExcept -> SetParams( "Faro", "setFecAditio" );

        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        if( !getAutentica( $mUser, $mPass, $fExcept ) )
        {
          
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }
        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
 
         $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_destin
                        SET ".$mTipFecha." = '".$mNumFecha."',
                            usr_modifi = '".$mUser."',
                            fec_modifi = NOW()
                      WHERE num_despac = '".$mNumDespac."'
                        AND num_docume IN ( ".$mNumFactur." )";

       
        
        if( $fConsult -> ExecuteCons( $mUpdate, "R" ) != FALSE )
        { 
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";

        } 
 
        if (sizeof($fReturn) < 1 ) {
          throw new Exception( "Ha ocurrido un error en la transaccion", "1999" );
        }

         

      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_respon'] = array();


      }
          return $fReturn;
    }  

    /*! \fn: setValidaCredenciales
    *  \brief: validar las credenciales
    *  \author: 
    *  \date: dia/mes/no
    *  \date modified: dia/mes/no
    *  \param: mNumDespac  Integer  Numero del despacho
    *  \param: mNomDestin  String   Nombre del destinatario
    *  \par am: mIndGroupx  Boolean  Indicador de agrupacion
    *  \return: Matriz
    */  
    function setValidaCredenciales( $mUser = NULL, $mPass = NULL, $mNomPalica = NULL)
    {

      try {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $mUser );
        $fExcept -> SetParams( "Faro", "setValidaCredenciales" );
        $fReturn = [];

        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        if( !getAutentica( $mUser, $mPass, $fExcept ) )
        {
          
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Credenciales correctas";         

      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
      }
          return $fReturn;
    }

    
  try
  {
    $server = new SoapServer( WsDirx."faroNET.wsdl", array('encoding'=>'ISO-8859-1') );
    $server -> addFunction( "setSeguim" );
    $server -> addFunction( "setSeguimFTP" );
    $server -> addFunction( "setSeguimPC" );
    $server -> addFunction( "setLlegada" );
    $server -> addFunction( "routExists" );
    $server -> addFunction( "setRout" );
    $server -> addFunction( "setPc" );
    $server -> addFunction( "setPCIntracarga" );
    $server -> addFunction( "getPCsContratados" );
    $server -> addFunction( "getTipser" );
    $server -> addFunction( "getRutas" );
    $server -> addFunction( "setNovedadPC" );
    $server -> addFunction( "setNovedadGPS" );
    $server -> addFunction( "setNovedadNC" );
    $server -> addFunction( "getNovedades" );
    $server -> addFunction( "setCambioRuta" );
    $server -> addFunction( "setAnulad" );
    $server -> addFunction( "setRevSalida" );
    $server -> addFunction( "setRevLlegada" );
    $server -> addFunction( "setRutaFaro" );
    $server -> addFunction( "setTelConduc" );
    $server -> addFunction( "setHomoloData" );
    $server -> addFunction( "setHomoloData" );
    $server -> addFunction( "getHomoloData" );
    $server -> addFunction( "setSalida" );
    $server -> addFunction( "setDespacPdf" );     
    $server -> addFunction( "RegistrarCall" );  
    $server -> addFunction( "RegistrarCallIn" );  
    $server -> addFunction( "getDestin" );  
    $server -> addFunction( "setFecAditio" );  
    $server -> addFunction( "setValidaCredenciales" );  
    $server -> handle();

    $file = fopen('/var/www/html/ap/interf/app/faro/logs/faroNET_XML_'.date("Y_m_d").'.txt', 'a+');
    fwrite($file,  "----------------------------------------  FECHA LOG ".date("Y-m-d H:i:s")." ------------------------------------------\n");
    fwrite($file, 'Se imprime el xml para el consumo del cliente  :'."\n");
    fwrite($file, $HTTP_RAW_POST_DATA);
    fwrite($file, file_get_contents('php://input'));
    
    fclose($file);
  }
  catch( Exception $e )
  {
    mail( "soporte.ingenieros@intrared.net", "Error-webservice", "ocurrio un error: ".$e -> getMessage() );
  }
?>

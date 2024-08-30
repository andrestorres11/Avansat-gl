<?php
// ini_set('display_errors', false);
// error_reporting(E_ALL & ~E_NOTICE);


/************************************************************************
 * @file avansatgl.class.php                                            *
 * @brief Servicio de WS para NET "nueva version".                      *
 * @version 0.1                                                         *
 * @date 04 de Agosto de 2021                                           *
 * @author Nelson Liberato                                              *
 ************************************************************************/



  //turn off the wsdl cache
  ini_set( "soap.wsdl_cache_enabled", "0" );

  //include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.  
  include_once( "/var/www/html/ap/interf/app/faro/protoc.class.inc" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/tracki.class.php");

 

 /************************************************************************
  * Funcion Inserta despacho                                             *
  * @fn setSeguim                                                        *
  * @brief Inserta un despacho en Sat trafico.                           *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex = NULL, 
  * @param $fCodTranps = NULL, 
  * @param $fCodManifi = NULL,
  * @param $fDatFechax = NULL, 
  * @param $fCodCiuori = NULL, 
  * @param $fCodCiudes = NULL, 
  * @param $fCodPlacax = NULL, 
  * @param $fNumModelo = NULL, 
  * @param $fCodMarcax = NULL, 
  * @param $fCodLineax = NULL, 
  * @param $fCodColorx = NULL, 
  * @param $fCodConduc = NULL, 
  * @param $fNomConduc = NULL, 
  * @param $fCiuConduc = NULL, 
  * @param $fTelConduc = NULL, 
  * @param $fMovConduc = NULL, 
  * @param $fObsComent = NULL, 
  * @param $fCodRutaxx = NULL, 
  * @param $fNomRutaxx = NULL, 
  * @param $fIndNaturb = 1,    
  * @param $fNumConfig = "3S3",
  * @param $fCodCarroc = 0,    
  * @param $fNumChasis = 1111, 
  * @param $fNumMotorx = 1111, 
  * @param $fNumSoatxx = 1111, 
  * @param $fDatVigsoa = NULL, 
  * @param $fNomCiasoa = "NA", 
  * @param $fNumTarpro = 1111, 
  * @param $fNumTrayle = NULL, 
  * @param $fCatLicenc = 1,    
  * @param $fDirConduc = "NA", 
  * @param $fCodPoseed = NULL, 
  * @param $fNomPoseed = NULL, 
  * @param $fCiuPoseed = NULL, 
  * @param $fDirPoseed = "NA",
  * @param $mCodAgedes = NULL, 
  * @param $mCodContrs = NULL, 
  * @param $mCodAgenci = NULL, 
  * @param $fCodoperad = NULL,
  * @param $mCodGpsxxx = NULL, 
  * @param $mCodRemesa = NULL, 
  * @param $fBinHuella = NULL, 
  * @param $fNumViajex = NULL, 
  * @param $fDatgps2xx = NULL, 
  * @param $fNomAplica = NULL, 
  * @param $fFotConduc = NULL, 
  * @param $fFotVehicu = NULL,
  * @param $fNumIntent = 0  
  * @return string respuesta del webservice.                             *
  ************************************************************************/
 function setSeguim(  $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL,
                      $fDatFechax = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fCodPlacax = NULL, 
                      $fNumModelo = NULL, $fCodMarcax = NULL, $fCodLineax = NULL, $fCodColorx = NULL, 
                      $fCodConduc = NULL, $fNomConduc = NULL, $fCiuConduc = NULL, $fTelConduc = NULL, 
                      $fMovConduc = NULL, $fObsComent = NULL, $fCodRutaxx = NULL, $fNomRutaxx = NULL, 
                      $fIndNaturb = 1,    $fNumConfig = "3S3",$fCodCarroc = 0,    $fNumChasis = 1111, 
                      $fNumMotorx = 1111, $fNumSoatxx = 1111, $fDatVigsoa = NULL, $fNomCiasoa = "NA", 
                      $fNumTarpro = 1111, $fNumTrayle = NULL, $fCatLicenc = 1,    $fDirConduc = "NA", 
                      $fCodPoseed = NULL, $fNomPoseed = NULL, $fCiuPoseed = NULL, $fDirPoseed = "NA",
                      $mCodAgedes = NULL, $mCodContrs = NULL, $mCodAgenci = NULL, $fCodoperad = NULL,
                      $mCodGpsxxx = NULL, $mCodRemesa = NULL, $fBinHuella = NULL, $fNumViajex = NULL, 
                      $fDatgps2xx = NULL, $fNomAplica = NULL, $fFotConduc = NULL, $fFotVehicu = NULL,
                      $fNumIntent = 0  )
  {
 
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

    // if ($fCodTranps == '805027046'){
    //   mail("andres.torres@eltransporte.org", "gl-remesa", var_export($mCodRemesa, true));
    // }

    $fCodPoseed = str_replace("-", "", $fCodPoseed);
    $fCodPoseed = str_replace(" ", "", $fCodPoseed);
    $fMessagesGps = array();
    $fMessagesGps2 = array();
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
    
      $i = 0;
      $fMessageGps = [];
      foreach ( $mCodGpsxxx as $mCodGps) 
      {
        $mCodGps -> nom_usrgps = substr($mCodGps -> nom_usrgps, 0, 63);
        //mail("nelson.liberato@eltransporte.org", "dasdasdasdas", $mCodGps -> cod_opegps."|".$mCodGps -> nom_usrgps."|".$mCodGps -> clv_usrgps."|".$mCodGps -> idx_gpsxxx);
        $fInputsGps = array (
                      'cod_opegps' => $mCodGps -> cod_opegps,
                      'nom_usrgps' => $mCodGps -> nom_usrgps,
                      'clv_usrgps' => $mCodGps -> clv_usrgps,
                      'idx_gpsxxx' => $mCodGps -> idx_gpsxxx  
                      );
        
        $fValidatorGps = new Validator( $fInputsGps, "gps_valida.txt" );
        $fMessagesGps[] = $fValidatorGps -> GetMessages();
        //mail("nelson.liberato@eltransporte.org", "GetMessages",  var_export($fMessagesGps, true ) );
        
        $fMessageGps[] = $fMessagesGps[0]; // Crea array con los c�digos de validaci�n de cada array que entra
       
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
        $mCodGps2 -> nom_usrgps = substr($mCodGps -> nom_usrgps, 0,  63);
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
   
    //------------------------- DATOS DE FOTOS CONDUCTOR -----------------------------
    if( $fFotConduc !== NULL && count( $fFotConduc ) > 0  )
    { 
      $fInputDatosFotoConductor = array (
                                          'fot_namexx' => $fFotConduc -> bin_fotcon -> fot_namexx,
                                          'fot_typexx' => $fFotConduc -> bin_fotcon -> fot_typexx,
                                          'fot_sizexx' => $fFotConduc -> bin_fotcon -> fot_sizexx,
                                          'fot_binary' => $fFotConduc -> bin_fotcon -> fot_binary,
                                          );
      
      $fValidatorFotoVehiculo = new Validator( $fInputDatosFotoConductor, "fotoRecurso_valida.txt" );
      $fMessagesFotoCondutor[]  = $fValidatorFotoVehiculo -> GetMessages();         
    }
    else
    {
      $fMessagesFotoCondutor[] = NULL;
    }
    // -------------------------------------------------------------------------------

    //------------------------- DATOS DE FOTOS VEHICU -----------------------------
    if( $fFotVehicu !== NULL && count( $fFotVehicu ) > 0  )
    { 
    
      $i = 0;
      $fMessageVehicu = [];
      foreach ( $fFotVehicu as $mDataFoto) 
      {
        $fInputDescricionFotoVehicu = array (
                                            'fot_namexx' => $mDataFoto -> fot_namexx,
                                            'fot_typexx' => $mDataFoto -> fot_typexx,
                                            'fot_sizexx' => $mDataFoto -> fot_sizexx,
                                            'fot_binary' => $mDataFoto -> fot_binary,
                                            );
        
        $fValidatorFotoVehiculo = new Validator( $fInputDescricionFotoVehicu, "fotoRecurso_valida.txt" );
        $fMessageValidadorFotoVehiculo[] = $fValidatorFotoVehiculo -> GetMessages();        
        $fMessageVehicu[] = $fMessageValidadorFotoVehiculo[0]; // Crea array con los c�digos de validaci�n de cada array que entra
        $i++;
      }
    }
    else
    {
      $fMessageVehicu = [];
    }

    // -------------------------------------------------------------------------------
   
   
    
    $fValidator = new Validator( $fInputs, "seguim_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    
 
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
    
    
       
    $fMessagesFinally = array_merge( $fMessagess, $fMessagesGps, $fMessagesRem , $fMessagesGps2, $fMessagesFotoCondutor, $fMessageVehicu);  // Union de los codigos de respuesta  
   
    
    unset( $fInputs, $fValidator );
     
    $flagCode = 0;
    foreach ($fMessagesFinally AS $mCode)  //
    {
      if($mCode[code] !== '1000')
         $flagCode = 1;      
    }
    
    //return "code_resp:1000; msg_respss:".var_export([$fNumIntent, $flagCode, $fMessagesFinally], true);
     
      //unset( $fMessagesFinally );
    if( $flagCode === 0  )//AND count($mNumMessageGps) ===  1
    {
      try
      { 

        //include_once( AplKon );
        
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguim" );





         if( $fNomAplica != NULL) // SI NO ES FARO , CARGA EL CLIENTE SATT_TQALA, EJEMPLO
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }
        else if(file_exists( '/var/www/html/ap/nliberato/satt_faro/constantes.inc' ) ){ //else if(file_exists( AplKon ) ){  <----- cambiar
          include_once('/var/www/html/ap/nliberato/satt_faro/constantes.inc');// include_once( AplKon );<----- cambiar
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada", "1999" );
          break;
        }
 
        $fReturn = NULL;
        $fSalidAut = TRUE;
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        $fSeguim = new Seguim( $fExcept );
        // parche para generar llegada a una placa que ya esta en ruta con otro manifiesto, para cargaantioquia, para dejar solo un manifiesto activo a la placa
        // ID 301858 carga antioquia
        // ID 354683 tevsa
        if(in_array($fCodTranps, ['890935085', '900138913']))
        {
          $mData = $fSeguim->placaEnRutaDespacho( $fCodTranps, $fCodPlacax);
          if(is_array($mData)) //si el metodo retorna un array es porque hay datos y toca darle llegada a esa placa con ese manifi
          {
            foreach ($mData AS $IndexLlegada => $mDataDespac) 
            {
              setLlegada($fNomUsuari, $fPwdClavex, $fCodTranps, $mDataDespac['cod_manifi'], date('Y-m-d H:i'), 
                                  'Llegada automatica porque hay otro manifiesto nuevo para la misma placa, Man nuevo '.$fCodManifi, $fCodPlacax,
                                  NULL, NULL, NULL, NULL  );
            }
          }

          unset($mData);
          unset($mDataDespac);
        }


        
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
          
        //mail("nelson.liberato@intrared.net", "Cagadas de InterfConalca Produccion", $fNomUsuari." --- ".$fPwdClavex);
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
         
        
            $mAlgo = $fTercer -> setTercer( $fCodConduc, $fNomConduc, $fNomConduc, $fDirConduc, 
                                 $fCodPaisxx, $fCodDeptox, $fCodCiudad, $fNomUsuari,
                                 $fCodActivi, $fTelConduc, $fMovConduc, $fCatLicenc, 
                                 1, "C", 1, TRUE,NULL, $fCodoperad, $fFotConduc );
//return var_export( $mAlgo, true);
          $fVehicu = new Vehicu( $fExcept );

          $fCodMarcax = $fVehicu -> getMarca( $fCodMarcax );//Valida que la marca exista o manda por defecto kw.

          $fCodLineax = $fVehicu -> getLinea( $fCodMarcax, $fCodLineax );//Valida que la linea exista o asigna la ultima linea de la marca.

          $fCodColorx = $fVehicu -> getCodColor( $fCodColorx );//Valida el color del vehiculo o retorna el color por defecto.
          
          $fCodCarroc = $fVehicu -> getCodCarroc( $fCodCarroc );//Valida la carroceria del vehiculo o retorna la carroceria por defecto.

          $fQueryInsVehicu = $fVehicu -> setVehicuSatt( $fCodPlacax, $fCodMarcax, $fCodLineax, $fCodColorx, $fNumModelo, $fCodPoseed, $fCodConduc, $fNomUsuari, 
                                                    $fNumMotorx, $fNumChasis, $fNumSoatxx, $fDatVigsoa, $fNomCiasoa, $fNumConfig, $fNumTarpro, $fCodCarroc, 1,
                                                    $fFotVehicu );
    
          

          if ($fSeguim -> despacFinalizado( $fCodManifi, $fCodTranps, $fCodPlacax )) 
            throw new Exception( "Numero de manifiesto se encuentra Finalizado en la plataforrma. $fCodManifi $fCodTranps $fCodPlacax", "6001" );
          
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
          
          // Inicia la transaccion --------------------------------------------------------------------------------------------------------------------------------

          if($mCodAgenc["cod_agenci"] != '' ||$mCodAgenc["cod_agenci"] != NULL  ) {
            $fLastCodAgenci = $fSeguim -> SetDatAgenc($mCodAgenc ,$fNomUsuari ,$fCodTranps, TRUE ); // Agencia
          }
          

          if( $fLastCodAgenci == NULL || $fLastCodAgenci== 0 || $fLastCodAgenci== '' )
            $fLastCodAgenci = 1;

          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  ){
            foreach( $mCodRemesa as $fRow )
            {
              $codClient = $fRow -> cod_client; //se agrega parche para enviar el cliente (generador) si llega la remesa.
            }
          }
     
          $fConsult -> StartTrans();
          $fIndNaturb = $fCodTranps == '860068121' ?  $fIndNaturb  : 2  ;
          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                    $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari,
                                                    $fIndNaturb, $fLastCodAgenci, 0, 0, "N", "R", FALSE, $fObsComent, $fMovConduc, $fDatgps2xx, $fInputsGps, $codClient);
          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert.", "3001" );    
          }
          $fNumDespac = $fSeguim -> getNextNumDespac(); 



          //$fQueryDespacTramo = $fSeguim -> setDespacTramo($fNumDespac ,$fCodTranps, $fCodRutaxx, $fCodCiuori, $fCodCiudes, $fNomUsuari, FALSE ); // Despacho Tramo


          // insert en despac_sisext para funcionamiento con la aoo y las etapas de precargue y cargue, trazabilidad integral
          $fQueryInsDespacSisext = $fSeguim -> setDespacSisext( $fNumDespac, $fCodManifi, FALSE);
 

          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, $fCodConduc, 
                                                    $fCodPlacax, $fObsComent, $fNomUsuari, $fLastCodAgenci );

          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  ){
          $fQueryInsDesRem = $fSeguim -> setDesRem( $fNumDespac, $mCodRemesa, $fNomUsuari);
          }                                           
            

          // Volcado de datos de remesas a estructura de destinatarios (Especie de homologacion) 2017 12 07 ID:261848
          $fArrDestin = array();
          $fQueryInsDestin = array();
          $fNitFajobe = '800232356';
          $fArrGenera = array(); // variable para almacenar los cod_genera de las remesas
          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0 )
          {     
            foreach ( $mCodRemesa as $mRemesa) 
            {
              // parche ya que los NIT que llegan por este campo, no existen en tercer_tercer y por referencia de llave, generar error de insert en tab_despac_destin, se deja el tercero 9999999=no registra
              //$mRemesa -> cod_client = $mRemesa -> cod_client == $fNitFajobe ? $mRemesa -> cod_client : '9999999';
              
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
                            'hor_citdes' => date("H:i:s", strtotime($mRemesa -> fec_estent) ),
                            'cod_transp' => $fCodTranps
                            );
              $fArrGenera[] = $mRemesa -> cod_client;
            }
            // genera en despac_destin las remesas que ingresa al despacho, para la app
            $fQueryInsDestin = $fSeguim -> setDesDestin( $fNumDespac, json_decode(json_encode($fArrDestin)), $fNomUsuari);

            $fFecCitcar = date("Y-m-d", strtotime( $fDatFechax ) );
            $fHorCitcar = date("H:i:s", strtotime( $fDatFechax ) );
            $fNomSitcar = utf8_encode($fArrDestin[0]['abr_tercer']);
          }

          $fQueryCorona = $fSeguim -> setDespacCorona( $fNumDespac , $fCodManifi    , $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                      $fCodCiuori , $fCodPaides    , $fCodDepdes, $fCodCiudes, $fNomUsuari, 
                                                      $fIndNaturb , $fLastCodAgenci,           0,           0,         "R",  
                                                      "R"         , FALSE          , $fObsComent, $fMovConduc,
                                                      $fIndNaturb , $fFecCitcar    , $fHorCitcar, $fNomSitcar, 
                                                      0    , 0 , 0, 0 , NULL , 
                                                      NULL , 0 , 0,     NULL
                                                    );

                                                             
          //$fConsult -> StartTrans();

          //if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE ) {
          //  throw new Exception( "Error en Insert.", "3001" );    
          //}

          if( NULL !== $fQueryInsVehicu ) {
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );
          }
                   

          if ( $fConsult -> ExecuteCons( $fQueryInsDespacSisext, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert. Despacho Sistema Externo. ".$fQueryInsDespacSisext, "3001" );
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
              throw new Exception( "Error en Insert. despactramo", "3008" );
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

          // Insercion de datos de remesa como destinatario para que la app funcione 2017 12 07 ID:261848
          if( $fQueryInsDestin != NULL && count( $fQueryInsDestin ) > 0 )
          {
             
            foreach( $fQueryInsDestin as $fQuery1 ) // insert de genera_remdes
            {
              if ( $fConsult -> ExecuteCons( $fQuery1['fQueryRemdes'], "R" ) === FALSE )
                throw new Exception( "Error en Insert RemDes.", "5555" );
            }

            foreach( $fQueryInsDestin as $fQuery2 ) // insert a despac_destin
            {
              if ( $fConsult -> ExecuteCons( $fQuery2['mQueryDestin'], "R" ) === FALSE )
                throw new Exception( "Error en Insert DespacDestin. ", "6666" );
            }
            
          }          

          // Insercion en despac_corona
          if( $fQueryCorona != NULL )
          {
              if ( $fConsult -> ExecuteCons( $fQueryCorona, "R" ) === FALSE ) {
                throw new Exception( "Error en Insert DespacCorona.".$fQueryCorona, "5558" );
              }
          }



          $fConsult -> Commit();


          // validación para enviar despacho a la central para la app, pero para fajober
          $fNitFajobe = '860017005';
          if( in_array( $fNitFajobe, $fArrGenera ) ) {
            setDespachoAPP($fNitFajobe,$fNumDespac,$fConsult );
          }

          if( $fSalidAut )
          {
            //Intracarga solo tiene contratados puestos fisicos
            #$fIndVirtua = $fCodTranps == '802017639' ? FALSE : TRUE;
            $fIndVirtua =   TRUE;
            
            $mResult = $fSeguim -> setDesSegu( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $fArrContrs, $fCodPlacax, $fCodTranps ) ;
            
            if( $mResult === 'SI') 
              $fReturn = ['cod_respon' => "1000", 'msg_respon' => "Se inserto el despacho, Y se da salida automatica.".$fCodRutaxx."-".$fNumDespac ];  
            else if( $mResult === 'NO') 
              $fReturn = ['cod_respon' => "1000", 'msg_respon' => "Se inserto el despacho, queda pendiente darle salida" ];  
            else   
              $fReturn = ['cod_respon' => "1000", 'msg_respon' => "Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control"]; 
          }
          else 
            $fReturn = ['cod_respon' => "1000", 'msg_respon' => "Se inserto el despacho pero no se da plan de ruta." ]; 
            
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
                                  $mCodGpsxxx, $mCodRemesa, $fBinHuella, $fNumViajex, $fDatgps2xx, $fNomAplica, 
                                  $fFotConduc, $fFotVehicu, $fNumIntent );


          }
          else {

            
            $fReturn = ['cod_respon' => $e -> getCode(), 'msg_respon' => "Se ha presentado un error el cual ya fue notificado. Line ".$e -> getLine() ];
            $fExcept -> CatchError( $e -> getCode(), "CATCHING: ".$e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
          }
          
        }
        else
        { 
          $fReturn = ['cod_respon' => $e -> getCode(), 'msg_respon' => $e -> getMessage() ];

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }
      return $fReturn;
    }
    else
    {
       
       $fMessageFinally = $fMessagesFinally;
       //return "code_resp:1000; hijueputa else:".var_export([$fNumIntent, $fMessageFinally], true);
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
        return ['cod_respon' => "6001", 'msg_respon' => $fMessage ];
        
      }
      else 
      return ['cod_respon' => $fMessages["code"], 'msg_respon' => $fMessages["message"] ];
     
    
    }
  }

  function setAnulad()
  {
     return  [ 'cod_respon' => 1000 , 'msg_respon' => 'okz' ];
  }


?>
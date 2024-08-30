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

  include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.

 /************************************************************************
  * Funcion Inserta despacho                                             *
  * @fn setDespac                                                        *
  * @brief Inserta un despacho en Sat trafico.                           *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fDatFechax: date   Fecha del despacho.                       *
  * @param $fNumPlanix: string Numero del manifiesto.                    *
  * @param $fIndNaturb: bool   Ind nacional urbano.                      *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodPlacax: string Matricula del vehiculo.                   *
  * @param $fCodMarcax: string Codigo de la marca del vehiculo.          *
  * @param $fCodCarroc: string Codigo de la carroceria del vehiculo.     *
  * @param $fCodColorx: string Codigo del color del vehiculo.            *
  * @param $fNumModelo: int    Modelo del vehiculo.                      *
  * @param $fNumChasis: int    Numero del chasis del vehiculo.           *
  * @param $fNumConfig: str¡ng Configuracion del vehiculo.               *
  * @param $fNumTarpro: str¡ng Numero tarjeta de proipedad.              *
  * @param $fNumMotorx: str¡ng Serial del motor vehiculo.                *
  * @param $fNumTrayle: str¡ng Matricula remolque del vehiculo.          *
  * @param $fNumSoatxx: str¡ng Seguro obligatorio.                       *
  * @param $fDatVigsoa: date   Fecha vigencia Soat.                      *
  * @param $fNomCiasoa: string Nombre compania de seguro Soat.           *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fNomTransp: string Nombre de la transportadora.              *
  * @param $fCiuTransp: string Codigo dane de la ciudad de la trans.     *
  * @param $fDirTransp: string Direccion de la transportadora.           *
  * @param $fCodPoseed: string Documento del poseedor del vehiculo.      *
  * @param $fNomPoseed: string Nombre del poseedor del vehiculo.         *
  * @param $fCiuPoseed: string Codigo dane de la ciudad del poseedor.    *
  * @param $fDirPoseed: string Direccion del poseedor.                   *
  * @param $fCodConduc: string Documento del conductor del vehiculo.     *
  * @param $fNomConduc: string Nombre del conductor del vehiculo.        *
  * @param $fCiuConduc: string Codigo dane de la ciudad del conductor.   *
  * @param $fDirConduc: string Direccion del conductor del vehiculo.     *
  * @param $fTelConduc: string Numero de telefono del conductor.         *
  * @param $fMovConduc: string Numero de telefono movil del conductor.   *
  * @param $fCatLicenc: string Numero categoria licencia del conductor.  *
  * @param $fCodMercan: string Codigo mercancia del despacho.            *
  * @param $fNumPesoxx: string Peso de la  mercancia del despacho.       *
  * @param $fObsComent: string Obsevaciones.                             *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function setDespac( $fNomUsuari = NULL, $fPwdClavex = NULL, $fNumPlanix = "1",  $fCodRutaxx = NULL, 
                      $fCodCiuori = NULL, $fCodCiudes = NULL, $fCodPlacax = NULL, $fCodTranps = NULL,
                      $fCodConduc = NULL, $fNomConduc = NULL, $fCiuConduc = NULL, $fDirConduc = "NA", 
                      $fTelConduc = NULL, $fMovConduc = NULL, $fCatLicenc = "1",  $fIndNaturb = "1",  
                      $fDatFechax = NULL, $fCodMarcax = "11", $fCodCarroc = "0",  $fCodColorx = 0,   
                      $fNumModelo = 2000, $fNumChasis = "1",  $fNumConfig = "3s3",$fNumTarpro = 1111,  
                      $fNumMotorx = 1111, $fNumTrayle = NULL, $fNumSoatxx = 1111, $fDatVigsoa = NULL, 
                      $fNomCiasoa = "NA", $fCodPoseed = NULL, $fNomPoseed = NULL, $fCiuPoseed = NULL, 
                      $fDirPoseed = "NA", $fCodMercan = 1,  $fNumPesoxx = "35", $fObsComent = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );

    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "dat_fechax" => $fDatFechax,
                      "num_planix" => $fNumPlanix, "cod_rutaxx" => $fCodRutaxx, "cod_placax" => $fCodPlacax, 
                      "cod_marcax" => $fCodMarcax, "cod_carroc" => $fCodCarroc, "cod_colorx" => $fCodColorx,
                      "num_modelo" => $fNumModelo, "num_chasis" => $fNumChasis, "num_config" => $fNumConfig,
                      "num_tarpro" => $fNumTarpro, "num_motorx" => $fNumMotorx, "num_trayle" => $fNumTrayle,
                      "num_soatxx" => $fNumSoatxx, "dat_vigsoa" => $fDatVigsoa, "nom_ciasoa" => $fNomCiasoa,
                      "cod_tranps" => $fCodTranps, "cod_poseed" => $fCodPoseed, "nom_poseed" => $fNomPoseed,
                      "ciu_poseed" => $fCiuPoseed, "dir_poseed" => $fDirPoseed, "cod_conduc" => $fCodConduc,
                      "nom_conduc" => $fNomConduc, "ciu_conduc" => $fCiuConduc, "dir_conduc" => $fDirConduc,
                      "tel_conduc" => $fTelConduc, "mov_conduc" => $fMovConduc, "cat_licenc" => $fCatLicenc,
                      "cod_mercan" => $fCodMercan, "num_pesoxx" => $fNumPesoxx, "obs_coment" => $fObsComent,
                      "cod_ciuori" => $fCodCiuori, "cod_ciudes" => $fCodCiudes, "ind_naturb" => $fIndNaturb );

    $fValidator = new Validator( $fInputs, "despac_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      $fReturn = NULL;
      try
      {
        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> "127.0.0.1:3305", "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        $fQuerySelAu = "SELECT 1 ".
                         "FROM ".BASE_DATOS.".tab_genera_usuari a ".
                        "WHERE a.cod_usuari = '".$fNomUsuari."' ".
                          "AND a.clv_usuari = '".base64_encode( $fPwdClavex )."' ";

        $fConsult -> ExecuteCons( $fQuerySelAu );

        if( 0 != $fConsult -> RetNumRows() )
        {
          $fQuerySelTransp = "SELECT 1 ".
                              "FROM ".BASE_DATOS.".tab_tercer_tercer ".
                             "WHERE cod_tercer = '".$fCodTranps."' ";

          $fConsult -> ExecuteCons( $fQuerySelTransp );
          if( 0 != $fConsult -> RetNumRows() )
          {
            $fLocation = getLocation( $fCiuPoseed, $fIdConection );
            if( FALSE !== $fLocation )
            {
              $fPaiPoseed = $fLocation["CodPaisxx"];
              $fDepPoseed = $fLocation["CodDepart"];
              $fCiuPoseed = $fLocation["CodCiudad"];
              unset( $fLocation );
            }
            else
              throw new Exception( "La ciudad del Poseedor no se encuentra registrada: ".$fCiuPoseed, '6001' );

            $fQuerySelPoseed = "SELECT 1 ".
                                 "FROM ".BASE_DATOS.".tab_tercer_tercer ".
                                "WHERE cod_tercer = '".$fCodPoseed."' ";

            $fConsult -> ExecuteCons( $fQuerySelPoseed );
            if( 0 == $fConsult -> RetNumRows() )
            {
              $fQueryInsPoseed = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer ".
                                   "( cod_tercer, cod_tipdoc, nom_tercer, abr_tercer, dir_domici, 
                                      cod_paisxx, cod_depart, cod_ciudad, cod_estado, usr_creaci, fec_creaci )".
                                 "VALUES ( '".$fCodPoseed."', 'C' ,'".$fNomPoseed."', '".$fNomPoseed."', 
                                           '".$fDirPoseed."', '".$fPaiPoseed."', '".$fDepPoseed."', '".$fCiuPoseed."', ".
                                          "'1', '".$fNomUsuari."', NOW() ) ";

              if( !$fConsult -> ExecuteCons( $fQueryInsPoseed, "BR" ) )
                throw new Exception( '1', '3001' );

              $fQueryInsActivi = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi ".
                                   "( cod_tercer, cod_activi )".
                                 "VALUES ( '".$fCodPoseed."' , '6' )";

              if( !$fConsult -> ExecuteCons( $fQueryInsActivi, "R" ) )
                throw new Exception( '2', '3001' );
            }
            else
            {
              $fQueryUpdPoseed = "UPDATE ".BASE_DATOS.".tab_tercer_tercer ".
                                    "SET dir_domici = '".$fDirPoseed."', cod_paisxx = '".$fPaiPoseed."', ".
                                        "cod_depart = '".$fDepPoseed."', cod_ciudad = '".$fCiuPoseed."', ".
                                        "usr_modifi = '".$fNomUsuari."', fec_modifi = NOW() ".
                                  "WHERE cod_tercer = '".$fCodPoseed."' ";

              if( !$fConsult -> ExecuteCons( $fQueryUpdPoseed, "BR" ) )
                throw new Exception( '3', '3001' );
            }

            $fLocation = getLocation( $fCiuConduc, $fExcept );
            if( FALSE !== $fLocation )
            {
              $fPaiConduc = $fLocation["CodPaisxx"];
              $fDepConduc = $fLocation["CodDepart"];
              $fCiuConduc = $fLocation["CodCiudad"];
              unset( $fLocation );
            }
            else
              throw new Exception( "La ciudad del Conductor no se encuentra registrada: ".$fCiuConduc, '6001' );

            $fQuerySelConduc = "SELECT 1 ".
                                 "FROM ".BASE_DATOS.".tab_tercer_tercer ".
                                "WHERE cod_tercer = '".$fCodConduc."' ";

            $fConsult -> ExecuteCons( $fQuerySelConduc );

            if( 0 == $fConsult -> RetNumRows() )
            {
              $fQueryInsConduc = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer ".
                                   "( cod_tercer, cod_tipdoc, nom_tercer, abr_tercer, dir_domici, 
                                      cod_paisxx, cod_depart, cod_ciudad, cod_estado, num_telef1, ".
                                     "num_telmov, usr_creaci, fec_creaci )".
                                 "VALUES ( '".$fCodConduc."', 'C' ,'".$fNomConduc."', '".$fNomConduc."', 
                                           '".$fDirConduc."', '".$fPaiConduc."', '".$fDepConduc."', '".$fCiuConduc."', ".
                                          "'1', '".$fTelConduc."', '".$fMovConduc."', '".$fNomUsuari."', NOW() ) ";

              if( $fConsult -> ExecuteCons( $fQueryInsConduc, "R" ) )
                throw new Exception( '4', '3001' );

              $fQueryInsActivi = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi ".
                                   "( cod_tercer, cod_activi )".
                                 "VALUES ( '".$fCodConduc."' , '4' )";

              if( $fConsult -> ExecuteCons( $fQueryInsActivi, "R" ) )
                throw new Exception( '5', '3001' );

              $fQueryInsConCon = "INSERT INTO ".BASE_DATOS.".tab_tercer_conduc ".
                                   "( cod_tercer, cod_tipsex, num_catlic, usr_creaci, fec_creaci ) ".
                                 "VALUES ( '".$fCodConduc."', '1', '".$fCatLicenc."', '".$fNomUsuari."', NOW() ) ";

              if( $fConsult -> ExecuteCons( $fQueryInsConCon, "R" ) )
                throw new Exception( '6', '3001' );
            }
            else
            {
              $fQueryUpdConduc = "UPDATE ".BASE_DATOS.".tab_tercer_tercer ".
                                    "SET dir_domici = '".$fDirConduc."', cod_paisxx = '".$fPaiConduc."', ".
                                        "cod_depart = '".$fDepConduc."', cod_ciudad = '".$fCiuConduc."', ".
                                        "usr_modifi = '".$fNomUsuari."', fec_modifi = NOW() ".
                                  "WHERE cod_tercer = '".$fCodConduc."' ";

              if( !$fConsult -> ExecuteCons( $fQueryUpdConduc, "R" ) )
                throw new Exception( '7', '3001' );

              $fQueryUpdConCon = "UPDATE ".BASE_DATOS.".tab_tercer_conduc ".
                                   "SET num_catlic = '".$fCatLicenc."', usr_modifi = '".$fNomUsuari."', fec_modifi = NOW() ".
                                 "WHERE cod_tercer = '".$fCodConduc."' ";

              if( !$fConsult -> ExecuteCons( $fQueryUpdConCon, "R" ) )
                throw new Exception( '8', '3001' );
            }

            $fColor = getCodColor( $fCodColorx, $fExcept );

            if( FALSE !== $fColor )
            {
              $fCodColorx = $fColor;
              unset( $fColor );
            }
            else
              throw new Exception( "Codigo de color no existente: ".$fCodColorx, '6001' );

            $fQuerySelVehicu = "SELECT 1 ".
                                 "FROM ".BASE_DATOS.".tab_vehicu_vehicu ".
                                "WHERE num_placax = '".$fCodPlacax."' ";

            $fConsult -> ExecuteCons( $fQuerySelVehicu );
            if( 0 == $fConsult -> RetNumRows() )
            {
              $QueryInsVehicu = "INSERT INTO ".BASE_DATOS.".tab_vehicu_vehicu ".
                                  "( num_placax, cod_marcax, cod_colorx, ano_modelo, num_motorx, num_chasis, ".
                                    "num_poliza, fec_vigfin, nom_asesoa, num_config, cod_tenedo, cod_conduc, ".
                                    "num_tarpro, ind_estado, usr_creaci, fec_creaci ) ".
                                "VALUES ( '".$fCodPlacax."', '".$fCodMarcax."', '".$fCodColorx."', '".$fNumModelo."', ".
                                         "'".$fNumMotorx."', '".$fNumChasis."', '".$fNumSoatxx."', '".$fDatVigsoa."', ".
                                         "'".$fNomCiasoa."', '".$fNumConfig."', '".$fCodPoseed."', '".$fCodConduc."', ".
                                         "'".$fNumTarpro."', '1', '".$fNomUsuari."', NOW() ) ";

              if( !$fConsult -> ExecuteCons( $QueryInsVehicu, "R" ) )
                throw new Exception( '9', '3001' );
            }
            else
            {
              $QueryUpdVehicu = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu ".
                                  "SET cod_marcax = '".$fCodMarcax."', cod_colorx = '".$fCodColorx."', ".
                                      "num_poliza = '".$fNumSoatxx."', fec_vigfin = '".$fDatVigsoa."', ".
                                      "nom_asesoa = '".$fNomCiasoa."', cod_tenedo = '".$fCodPoseed."', ".
                                      "cod_conduc = '".$fCodConduc."', num_tarpro = '".$fNumTarpro."', ".
                                      "ind_estado = '1', usr_modifi = '".$fNomUsuari."', fec_modifi = NOW() ".
                                "WHERE num_placax = '".$fCodPlacax."' ";

              if( !$fConsult -> ExecuteCons( $QueryUpdVehicu, "R" ) )
                throw new Exception( '10', '3001' );
            }
          }
          else
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

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
            throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

          $fNumDespac = getNextNumDespac( $fExcept );

          $fQuerySelDespac = "SELECT 1 ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fNumPlanix."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fCodPlacax."' ";

          $fConsult -> ExecuteCons( $fQuerySelDespac );

          if( 0 == $fConsult -> RetNumRows() )
          {
            $fQueryInsDespac = "INSERT INTO ".BASE_DATOS.".tab_despac_despac ".
                                 "( num_despac, fec_despac, cod_tipdes, cod_paiori, cod_depori, cod_ciuori, cod_paides, ".
                                   "cod_depdes, cod_ciudes, cod_agedes, num_carava, ind_camrut, ind_planru, ind_anulad, ".
                                   "usr_creaci, fec_creaci, cod_manifi ) ".
                               "VALUES ( '".$fNumDespac."', '".$fDatFechax."', '2', '".$fCodPaiori."', '".$fCodDepori."', ".
                                        "'".$fCodCiuori."', '".$fCodPaides."', '".$fCodDepdes."', '".$fCodCiudes."', ".
                                        "'1', '0', '0', 'N', 'R', '".$fNomUsuari."', NOW(), '".$fNumPlanix."' ) ";

            if( !$fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) )
              throw new Exception( '11', '3001' );

            $fQuerySelRutasx = "SELECT 1 ".
                                 "FROM ".BASE_DATOS.".tab_genera_rutasx ".
                                "WHERE cod_rutasx = '".$fCodRutaxx."' ".
                                  "AND ind_estado = '1' ";

            $fConsult -> ExecuteCons( $fQuerySelRutasx );

            if( 0 == $fConsult -> RetNumRows() )
            {
              $fRutaExist = 0;
              $fQuerySelRutaxx = "SELECT cod_rutasx ".
                                   "FROM ".BASE_DATOS.".tab_genera_rutasx ".
                                  "WHERE cod_ciuori = '".$fCodCiuori."' ".
                                    "AND cod_ciudes = '".$fCodCiudes."' ".
                                    "AND ind_estado = '1' ";

              $fConsult -> ExecuteCons( $fQuerySelRutasx );
              if( 0 == $fConsult -> RetNumRows() )
              {
                $fResultRutax = $fConsult -> RetMatrix( "a" );
                $fCodRutaxx = $fResultRutax[0]["cod_rutasx"];
              }
            }
            else
              $fRutaExist = 1;

            $fQueryInsDesVehi = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige ".
                                 "( num_despac, cod_transp, cod_agenci, cod_rutasx, cod_conduc, num_placax, ".
                                   "ind_activo, obs_anulad, usr_creaci, fec_creaci )".
                               "VALUES ( '".$fNumDespac."', '".$fCodTranps."', '1', '".$fCodRutaxx."', ".
                                        "'".$fCodConduc."', '".$fCodPlacax."', 'N', '".$fObsComent."', ".
                                        "'".$fNomUsuari."', NOW() )";

              if( !$fConsult -> ExecuteCons( $fQueryInsDesVehi, "RC" ) )
                throw new Exception( '12', '3001' );

            if( $fRutaExist )
            {

              $fQuerySelPcont = "SELECT cod_contro ".
                                  "FROM ".BASE_DATOS.".tab_genera_rutcon ".
                                 "WHERE cod_rutasx = '".$fCodRutaxx."' ";

              $fConsult -> ExecuteCons( $fQuerySelPcont, "BR" );

              if( 0 != $fConsult -> RetNumRows() )
              {
                $fResultPconts = $fConsult -> RetMatrix( "a" );
                foreach( $fResultPconts as $fResultPcont )
                {
                  $fQueryInsDespSegu = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim ".
                                         "( num_despac, cod_contro, cod_rutasx, fec_planea, fec_alarma, ".
                                           "usr_creaci, fec_creaci ) ".
                                       "VALUES ( '".$fNumDespac."', '".$fResultPcont["cod_contro"]."', ".
                                                "'".$fCodRutaxx."', '".$fDatFechax."', ".
                                                "'".$fDatFechax."', '".$fNomUsuari."', NOW() ) ";

                  if( $fConsult -> ExecuteCons( $fQueryInsDespSegu, "R" ) )
                    throw new Exception( '13', '3001' );
                }

                $fQueryUpdDespac = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                      "SET fec_salida = '".date( "Y-m-d H:i:s" )."', ".
                                          "obs_salida = 'Salida automatica generada por interfaz', ".
                                          "ind_planru = 'S' ".
                                    "WHERE num_despac = '".$fNumDespac."' ";

                if( !$fConsult -> ExecuteCons( $fQueryUpdDespac, "R" ) )
                  throw new Exception( '14', '3001' );

                $fQueryUpdDespvehi = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                                        "SET ind_activo = 'S' ".
                                      "WHERE num_despac = '".$fNumDespac."' ";

                if( !$fConsult -> ExecuteCons( $fQueryUpdDespac, "RC" ) )
                  throw new Exception( '15', '3001' );

                $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.";
              }
            }
            else
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta.";
          }
          else
            throw new Exception( "Numero de manifiesto repetido.", "6001" );
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
                                  $e -> getLine(), NULL, NULL );
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
          $fMessage .= "Campo:".$fRow["Col"].", Detalle:".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Siguiente numero de despacho                                 *
  * @fn getNextNumDespac                                                 *
  * @brief Obtiene el siguiente numero de despacho.                      *
  * @param $fExcept    : obj Error.                                      *
  * @return integer numero de despacho.                                  *
  ************************************************************************/
  function getNextNumDespac( $fExcept )
  {
    $fConsult = new Consult( array( "server"=> "127.0.0.1:3305", "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

    $fQuerySelNumDes = "SELECT MAX( num_despac ) AS num_despac ".
                         "FROM ".BASE_DATOS.".tab_despac_despac ";

    $fConsult -> ExecuteCons( $fQuerySelNumDes );
    if( 0 == $fConsult -> RetNumRows() )
      $fReturn = FALSE;
    else
    {
      $fResultNumDespac = $fConsult -> RetMatrix( "a" );
      $fReturn = $fResultNumDespac[0]["num_despac"]+1;
    }

    unset( $fConsult, $fQuerySelNumDes, $fResultNumDespac );
    return $fReturn;
  }

  try
  {
    $server = new SoapServer( WsDirx."despac.wsdl" );
    $server -> addFunction( "setDespac" );
    $server -> handle();
  }
  catch( Exception $e )
  {
    mail( "carlos.mock@intrared.net", "Error-webservice 5", "ocurrio un error: ".$e -> getMessage() );
  }
?>

<?php
   /************************************************************************************
   * Clase para manejo de la interfaz con Protekto                                     *
   * @brief Clase que contiene todos los métodos de gestión de escoltas electrónicos   *
   * @version 0.1                                                                      *
   * @Fecha de Creación 09 de Septiembre de 2013                                       *
   * @author Felipe Malaver                                                            *
   ************************************************************************************/
  class InterfProtekto
  {
    var $cConexion           = NULL;
    var $cMsgError           = NULL;
    var $BASE_DATOS          = NULL;
    var $NOMSAD              = NULL;
    var $NIT_TRANSPOR        = NULL;
    var $NOM_URL_APLICA      = NULL;
    var $COD_INTERF_PROTEK   = NULL;
    var $NOT_MAIL            = NULL;
    var $URL_INTERF_PROTEK   = NULL;
    var $BD_STANDA           = NULL;
    var $PERMISOS_INSERTS    = NULL;
    var $LISTA_ERRORES       = array ( '-100' => 'OETCrearViajeRutaNoExistente', 
                                       '-101' => 'OETCrearViajeNoSePudoCrearCliente', 
                                       '-102' => 'OETCrearViajeClienteYNITVacio', 
                                       '-103' => 'OETCrearViajeClienteSinNitAsociado',
                                       '-104' => 'OETCrearViajeClienteVacioNitNoEncontrado',
                                       '-105' => 'OETCrearViajeNoSePudoCrearTransportador', 
                                       '-106' => 'OETCrearViajeTransportadorYNITVacio', 
                                       '-107' => 'OETCrearViajeTransportadorSinNitAsociado',
                                       '-108' => 'OETCrearViajeTransportadorVacioNitNoEncontrado', 
                                       '-109' => 'OETCrearViajeNoSePudoCrearConductor', 
                                       '-110' => 'OETCrearViajeConductorYCedulaVacio', 
                                       '-111' => 'OETCrearViajeConductorSinCedulaAsociado', 
                                       '-112' => 'OETCrearViajeConductorVacioCedulaNoEncontrado', 
                                       '-20'  => 'CrearViajeMovilAsociadoCliente', 
                                       '-30'  =>  'CrearViajeProblemaCreandoContenedor', 
                                       '-40'  => 'CrearViajeProblemaCreandoMovilContenedor', 
                                       '-50'  => 'CrearViajeProblemaCreandoViaje',
                                       '-70'  =>  'CrearViajeProblemaAgregandoConductor',
                                       '-90'  =>  'CrearViajeProblemaAsociarMovilCliente',                              
                                       '-113' =>  'OETAnularViajePlacaNoAsociadaViaje',                              
                                       '-114' =>  'OETAnularViajeViajeNoExistente',                              
                                       '-115' =>  'OETAnularViajeFalloEnCierre'                              
                                      );
    
    function __construct( $mConexion, $constantes = NULL )
    {
      if( isset( $constantes ) && $constantes !== NULL )
      {
        //Si la clase es instanciada desde afuera de las aplicaciones SAT (independientemente)
        $this -> BASE_DATOS = $constantes['BASE_DATOS'];
        $this -> COD_INTERF_PROTEK = $constantes['COD_INTERF_PROTEK'];
        $this -> NIT_TRANSPOR = $constantes['NIT_TRANSPOR'];
        $this -> NOMSAD = $constantes['NOMSAD'];
        $this -> NOM_URL_APLICA = $constantes['NOM_URL_APLICA'];
        $this -> NOT_MAIL = $constantes['NOT_MAIL'];
        $this -> URL_INTERF_PROTEK = $constantes['URL_INTERF_PROTEK'];
        $this -> BD_STANDA = $constantes['BD_STANDA'];
      }
      else
      {
        //Si la clase es instanciada desde el framework de los sat
        $this -> BASE_DATOS = BASE_DATOS;
        $this -> COD_INTERF_PROTEK = COD_INTERF_PROTEK;
        $this -> NIT_TRANSPOR = NIT_TRANSPOR;
        $this -> NOMSAD = NOMSAD;
        $this -> NOM_URL_APLICA = NOM_URL_APLICA;
        $this -> NOT_MAIL = NOT_MAIL;
        $this -> URL_INTERF_PROTEK = URL_INTERF_PROTEK;
        $this -> BD_STANDA = BD_STANDA;
      }
      $this -> cConexion = $mConexion;
    }
    
    function setConexion( $mConexion )
    {
      $this -> cConexion = $mConexion;
    }
    
    function getConexion()
    {
      return $this -> cConexion;
    }
    
    function setMsgError( $mMsgError )
    {
      $this -> cMsgError = $mMsgError;
    }
    
    function getMsgError()
    {
      return $this -> cMsgError;
    }
    
    /********************************************************************************
    * Funcion Verifica si tiene activa la interfaz Protekto                         *
    * @fn verifyInterfProtek                                                        *
    * @return array nom_usuari y clv_usuari o FALSE                                 *
    ********************************************************************************/
    function verifyInterfProtek( )
    {
      //Se verifica si tiene activa interfaz Protekto
      $mSql = "SELECT nom_usuari, clv_usuari 
              FROM ".$this -> BASE_DATOS.".tab_interf_parame 
              WHERE cod_operad = '".$this -> COD_INTERF_PROTEK."'
                AND cod_transp = '".$this -> NIT_TRANSPOR."'
                AND ind_estado = '1'";
      $consulta = new Consulta( $mSql, $this -> cConexion );
      return $consulta -> ret_matriz( 'a' );
    }
    
    /********************************************************************************
    * Funcion Retorna un listado con todas las rutas activas en Protekto            *
    * @fn RutasActivasProtekto                                                      *
    * @return matriz asociativa con las rutas activas                               *
    ********************************************************************************/
    function RutasActivasProtekto( )
    {
      try
      {
        $ToReturn = array();
        $mData = $this -> verifyInterfProtek();
        $mParams = array( 'usuario'       => base64_decode( $mData[0]["nom_usuari"] ),
                          'contrasenaMD5' => strtoupper( $mData[0]["clv_usuari"] ) );
        ini_set( "soap.wsdl_cache_enabled", "0" );
        $oSoapClient = new soapclient( $this -> URL_INTERF_PROTEK, array( 'encoding'=>'ISO-8859-1' ) );
        $mResult = $oSoapClient -> __call( "traerRutasActivas", $mParams );
        $mRutas = $mResult -> traerRutasActivasResult -> ComponenteRuta;
        $j = 0;
        foreach ( $mRutas as $row )
        {
          $ToReturn[$j][0] = $row -> id;
          $ToReturn[$j][1] = $row -> nombre;
          $j++;
        }
        
        return $ToReturn;
      }
      catch( SoapFault $e )
      {
        $error = $e -> faultstring;
        if ( $error ) 
        {
          $data['error'] = $data['nom_proces'].': '.$error;
          $this -> setMsgError( $data['error'] );
        }
        elseif ( $e -> fault )
        {
          $data['error'] = $data['nom_proces'].': '.$e -> faultcode.':'.$e -> faultdetail.':'.$e -> faultstring;
          $this -> setMsgError( $data['error'] );
        }
        return FALSE;
      }
    }
    
    /*************************************************************************************
    * Funcion Retorna un array con datos acerca de la solicitud de un escolta            *
    * @fn SolicitaEscolta                                                                *
    * @return array con un código y una descripcion acerca de la solicitud de un escolta *
    *************************************************************************************/
    function SolicitaEscolta( $mData )
    {
      try
      {
        $mVerify = $this -> verifyInterfProtek();
        
        $mUsuario = base64_decode( $mVerify[0]["nom_usuari"]);
        $mClavexx = strtoupper( $mVerify[0]["clv_usuari"] );
        $mNclient = $mData['nom_client'];
        $mNitclie = $mData['cod_client'];
        $mNtransp = $mData['nom_transp'];
        $mNittran = $mData['cod_transp'];
        $mPlacaxx = $mData['num_placax'];
        $mDoccond = $mData['cod_conduc'];
        $mNomcond = $mData['nom_conduc'];
        $mTelcond = $mData['tel_conduc'];
        $mNumcont = $mData['num_conten'];
        $mNumremo = $mData['num_remolq'];
        $mRutaxxx = $mData['cod_rutax'];
        $mManifix = $mData['num_manifi'];
        $mOrdenxx = $mData['num_ordenc'];
        $mFechaxx = $mData['fec_progra'];

        
        $mParams = array( 'usuario'           => $mUsuario,
                          'contrasenaMD5'     => $mClavexx,
                          'nombrecliente'     => $mNclient,
                          'nitcliente'        => $mNitclie,
                          'nomtransportador'  => $mNtransp,
                          'nittransportador'  => $mNittran,
                          'placavehiculo'     => $mPlacaxx,
                          'cedulaconductor'   => $mDoccond,
                          'nombreconductor'   => $mNomcond,
                          'telefonoconductor' => $mTelcond,
                          'numerocontenedor'  => $mNumcont,
                          'numeroremolque'    => $mNumremo,
                          'rutaid'            => $mRutaxxx,
                          'manifiesto'        => $mManifix,
                          'numeroorden'       => $mOrdenxx,
                          'fechaprogramada'   => $mFechaxx
                        );
            
        ini_set( "soap.wsdl_cache_enabled", "0" );
        $oSoapClient = new soapclient( $this -> URL_INTERF_PROTEK, array( 'encoding'=>'ISO-8859-1' ) );
        $mResult = $oSoapClient -> crearViaje( $mParams );
        $mIdSolici = $mResult -> crearViajeResult;
          
        if( $mIdSolici <= 0 )
          return array( 'ind_solici' => $mIdSolici, 'men_solici' => $this -> LISTA_ERRORES[$mIdSolici] );
        else
          return array( 'ind_solici' => $mIdSolici, 'men_solici' => 'Éxito' );
      }
      catch( SoapFault $e )
      {
        $error = $e -> faultstring;
        if ( $error ) 
        {
          $data['error'] = $data['nom_proces'].': '.$error;
          $this -> setMsgError( $data['error'] );
        }
        elseif ( $e -> fault )
        {
          $data['error'] = $data['nom_proces'].': '.$e -> faultcode.':'.$e -> faultdetail.':'.$e -> faultstring;
          $this -> setMsgError( $data['error'] );
        }
        return $this -> cMsgError;
      }
      
    } 
    
    /*************************************************************************************
    * Funcion Retorna un array con datos acerca de la anulacion de un escolta            *
    * @fn AnulaEscolta                                                                   *
    * @return array con un código y una descripcion acerca de la anulacion de un escolta *
    *************************************************************************************/
    function AnulaEscolta( $mData )
    {
      try
      {
        $mVerify = $this -> verifyInterfProtek();
        
        $mUsuario = base64_decode( $mVerify[0]["nom_usuari"]);
        $mClavexx = strtoupper( $mVerify[0]["clv_usuari"] );
        $mViajeid = $mData['num_solici'];
        $mPlacaxx = $mData['num_placax'];
        
        $mParams = array( 'usuario'           => $mUsuario,
                          'contrasenaMD5'     => $mClavexx,
                          'idviaje'           => $mViajeid,
                          'placa'             => $mPlacaxx
                        );
                          
        ini_set( "soap.wsdl_cache_enabled", "0" );
        $oSoapClient = new soapclient( $this -> URL_INTERF_PROTEK, array( 'encoding'=>'ISO-8859-1' ) );
        $mResult = $oSoapClient -> anularViaje( $mParams );
        $mIdSolici = $mResult -> anularViajeResult;
        $_ID = explode( '-' , $mIdSolici );
        if( trim( $_ID[0] ) == 'OK' )
          $mIdSolici = trim( $_ID[1] );
        if( $mIdSolici <= 0 )
          return array( 'ind_solici' => $mIdSolici, 'men_solici' => $this -> LISTA_ERRORES[$mIdSolici] );
        else
          return array( 'ind_solici' => $mIdSolici, 'men_solici' => 'Éxito' );
      }
      catch( SoapFault $e )
      {
        $error = $e -> faultstring;
        if ( $error ) 
        {
          $data['error'] = $data['nom_proces'].': '.$error;
          $this -> setMsgError( $data['error'] );
        }
        elseif ( $e -> fault )
        {
          $data['error'] = $data['nom_proces'].': '.$e -> faultcode.':'.$e -> faultdetail.':'.$e -> faultstring;
          $this -> setMsgError( $data['error'] );
        }
        return $this -> cMsgError;
      }
      
    }
  }
?>   

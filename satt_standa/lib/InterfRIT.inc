<?php
   /************************************************************************************
   * Clase para manejo del webservice RIT                                              *
   * @brief Esta es la clase                                                           *
   * @version 0.1                                                                      *
   * @creacion 18 de Mayo de 2013                                                      *
   * @author Nelson Liberato.                                                          *
   ************************************************************************************/
 
  class InterfRIT
  {
    var $cConexion  = NULL;
    var $cMsgError  = NULL;
  
    function InterfRIT( $mConexion )
    {
      
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
    
    function sendError( $mSendData, $mAditionalData )
    {
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Operacion: ".$mAditionalData['nom_proces']." \n";
      $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
      $mMessage .= "Fecha Salida: ".$mSendData["fec_salida"]." \n";
      $mMessage .= "Empresa de transporte: ".NIT_TRANSPOR." \n";
      $mMessage .= "Aplicacion: ".NOM_URL_APLICA." \n";
      $mMessage .= "Despacho: ".$mSendData["num_despac"]." \n";
      $mMessage .= "Placa: ".$mSendData["num_placax"]." \n";
      $mMessage .= "Operador: ".$mAditionalData['nom_operad']." \n";
      $mMessage .= "******** Detalle ******** \n";
      $mMessage .= "Codigo de error: ".$mAditionalData['cod_errorx']." \n";
      $mMessage .= "Mensaje de error: ".$this -> getMsgError()." \n";
      mail( NOT_MAIL.",nelson.liberato@intrared.net", "Web service RIT", $mMessage,'From: soporte.ingenieros@intrared.net' );
      
    }
    

    

    
    //Metodo que agrega la placa al listado de vehiculos reportando gps en MySQL 5 mediante WS
    function setDespacFinali( $mNumDespac = NULL, $mAditionalData = NULL)
    {
      try
      {
        $mDataRit = array(); 
        $mDataDespac = $this -> getDataManifi( $mNumDespac );   
        $mDataRit    = $this -> getMakeArray ( $mDataDespac , 'setDespacFinali', $mAditionalData);

        //Se consultan datos del despacho     
        ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache    
        $oSoapClient = new soapclient( URL_INTERF_RITXXX, array( "trace" => TRUE, 'encoding' => 'ISO-8859-1' ) );
        $mResult = $oSoapClient -> __soapCall("setDespacFinali", $mDataRit );     
        
        $mResponse = explode(";", $mResult);    
        $mCodeResp = explode(":",$mResponse[0]);    
        $mMessResp = explode(":",$mResponse[1]);   

        if($mCodeResp[1] != '1000')
        {
          $mAditionalData['cod_errorx'] = $mCodResp[1];         
          $this -> setMsgError( (string)$mResponse[1] );
          $this -> sendError( $mDataDespac, $mAditionalData );
        }
    
        return $mResult;
      }
      catch(SoapFault $e)
      {
        $error = $e->getMessage();
        
        $this -> setMsgError( $error );
        $this -> sendError( $mDataDespac, $mAditionalData );
            
        $mReturn[0] = true; 
        $mReturn[1] = "code_resp:6001; msg_resp:".$error;        
        return $mReturn;
      }
    }
    
    function setCalifiConduc( $mNumDespac = NULL, $mAditionalData = NULL)
    {
      try
      {
      
        //Se consultan datos del despacho   
        $mReturn = array();
        $mDataRit = array(); 
        $mDataCalifi = $this -> getDataManifi( $mNumDespac );   
        $mDataRit    = $this -> getMakeArray ( $mDataCalifi , 'setCalifiConduc', $mAditionalData);
      
        ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache    
        $oSoapClient = new soapclient( URL_INTERF_RITXXX, array( "trace" => TRUE, 'encoding' => 'ISO-8859-1' ) );
        $mResult = $oSoapClient -> __soapCall("setCalifiConduc", $mDataRit );     
        $mResultx = explode(";", $mResult);  
        $mCodResp = explode(":",$mResultx[0]);    
        $mMsgResp = explode(":",$mResultx[1]);
        $mAditionalData['cod_errorx'] = $mCodResp[1];
            
        if( $mCodResp[1] !== '1000')
          throw new SoapFault($mCodResp[1], $mMsgResp[1]);          
          
        $mReturn[0] = true; 
        $mReturn[1] = $mMsgResp[1]; 
        $mReturn[2] = $mDataCalifi[nom_conduc]." ".$mDataCalifi[ape_conduc];
        $mReturn[3] = $mDataRit;
        return $mReturn;
      }
      catch(SoapFault $e)
      {
        $this -> cMsgError = $e->faultstring;
        $mReturn[0] = false; 
        $mReturn[1] = $e -> faultstring;
        $mReturn[2] = $mDataCalifi[nom_conduc]." ".$mDataCalifi[ape_conduc];
        return $mReturn;
      }
    }
    
    function getDataManifi ( $mNumDespac )
    {
      $mSql = " SELECT '--Propietario--',
                        d.cod_tercer AS cod_propie, 
                        d.cod_tipdoc AS cod_tippro, 
                        d.cod_ciudad AS cod_ciupro,
                        'N' AS cod_genpro, 
                        TRIM(IF(d.abr_tercer IS NULL, CONCAT(d.nom_tercer,' ',d.nom_apell1,'',d.nom_apell2), d.abr_tercer )) AS nom_propie, 
                        IF(d.dir_domici IS NULL OR d.dir_domici = '' ,'Sin Especificar', d.dir_domici ) AS dir_propie,
                        IF(d.num_telef1 IS NULL OR d.num_telef1 = '' ,'0000000', d.num_telef1 ) AS fij_propie, 
                        IF(d.num_telmov IS NULL OR d.num_telmov = '' ,'0000000000', d.num_telmov ) AS mov_propie,
                       
                         '--Tenedor--',
                        e.cod_tercer AS cod_tenedo, 
                        e.cod_tipdoc AS cod_tipten, 
                        e.cod_ciudad AS cod_ciuten,
                        'N' AS cod_genten, 
                        TRIM(IF(e.abr_tercer IS NULL, CONCAT(e.nom_tercer,' ',e.nom_apell1,'',e.nom_apell2), e.abr_tercer )) AS nom_tenedo, 
                        IF(e.dir_domici IS NULL OR e.dir_domici = '','Sin Especificar', e.dir_domici ) AS dir_proten,
                        IF(e.num_telef1 IS NULL OR e.num_telef1 = '' ,'0000000', e.num_telef1) AS fij_proten, 
                        IF(e.num_telmov IS NULL OR e.num_telmov = '' ,'0000000000', e.num_telmov)AS mov_proten,
                        
                        '****Conductor****',
                        f.cod_tercer AS cod_conduc, f.cod_tipdoc AS cod_tipcon, f.cod_ciudad AS cod_ciucon, 'N' AS cod_gencon,
                        TRIM(f.nom_tercer) AS nom_conduc, IF(f.nom_apell1 IS NULL OR f.nom_apell1 = '','No Registra',TRIM(f.nom_apell1) ) AS ape_conduc, 
                        IF(f.dir_domici IS NULL OR f.dir_domici = '', 'No registra', f.dir_domici) AS dir_conduc, 
                        IF(f.num_telef1 IS NULL OR f.num_telef1 = '', '0000000', f.num_telef1  ) AS fij_conduc, 
                        IF(f.num_telmov IS NULL OR f.num_telmov = '', '0000000000', f.num_telmov) AS mov_conduc,
                        IF(l.num_catlic IS NULL OR l.num_catlic = '', '1', l.num_catlic ) AS cod_catlic, 
                        IF(l.num_licenc IS NULL OR l.num_licenc = '', '0000000', l.num_licenc) AS num_licenc, 
                        IF(l.fec_venlic IS NULL OR l.fec_venlic = '', '0000-00-00', l.fec_venlic) AS fec_venlic,
                        
                        '****Vehiculo*****',
                        b.num_placax,
                        c.ano_modelo,
                        IF(c.num_motorx IS NULL OR c.num_motorx = '', '000000', c.num_motorx) AS num_motorx,
                        IF(c.num_poliza IS NULL OR c.num_poliza = '', '000000', c.num_poliza) AS num_poliza,
                        '1' AS cod_asesoa,
                        c.cod_ciusoa,
                        '********************',
                        
                        a.cod_manifi, a.cod_ciuori, a.cod_ciudes, m.nom_ciudad AS ciu_origen, n.nom_ciudad AS ciu_destin,
                        UPPER(o.abr_tercer) AS nom_transp,a.num_despac, a.fec_llegad, a.obs_llegad, a.usr_modifi,b.cod_transp
                  
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_vehicu_vehicu c,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_tercer_tercer e,
                       ".BASE_DATOS.".tab_tercer_tercer f,
                       ".BASE_DATOS.".tab_tercer_conduc l,
                       ".BASE_DATOS.".tab_genera_ciudad m,
                       ".BASE_DATOS.".tab_genera_ciudad n,
                       ".BASE_DATOS.".tab_tercer_tercer o
                       
                       
                  WHERE a.num_despac = b.num_despac AND
                        b.num_placax = c.num_placax AND
                        c.cod_propie = d.cod_tercer AND
                        c.cod_tenedo = e.cod_tercer AND
                        b.cod_conduc = f.cod_tercer AND
                        b.cod_conduc = l.cod_tercer AND
                        a.cod_ciuori = m.cod_ciudad AND
                        a.cod_ciudes = n.cod_ciudad AND
                        b.cod_transp = o.cod_tercer AND
                        a.num_despac = '".$mNumDespac."' ";
      $consulta = new Consulta( $mSql, $this -> cConexion );
      $datos    = $consulta -> ret_matriz( 'a' );
      $datos    = $datos[0];
      
      $datos['num_diasxx']   = $this -> GetEstado( $datos, 1 );
      
      return $datos;
      
    }
    
    function getMakeArray ( $mDataCalifi, $mTypeMethodArray = NULL, $mAditionalData = NULL )
    {
      
      $mDataRit = array();
      // Datos Propietario -------------------------------------------------------------------------------------------------------------------------------------
      $mDataPropie = array(
                            "cod_tercer" => $mDataCalifi['cod_propie'],  "cod_tipdoc" => $mDataCalifi['cod_tippro'],  "cod_ciudad" => $mDataCalifi['cod_ciupro'],  
                            "cod_genero" => $mDataCalifi['cod_genpro'],  "nom_tercer" => $mDataCalifi['nom_propie'],  "dir_domici" => $mDataCalifi['dir_propie'],  
                            "num_telef1" => $mDataCalifi['fij_propie'],  "num_telmov" => $mDataCalifi['mov_propie'] 
                           );
      // Datos Tenedor -------------------------------------------------------------------------------------------------------------------------------------
      $mDataTenedo = array(
                            "cod_tercer" => $mDataCalifi['cod_tenedo'],  "cod_tipdoc" => $mDataCalifi['cod_tipten'],  "cod_ciudad" => $mDataCalifi['cod_ciuten'], 
                            "cod_genero" => $mDataCalifi['cod_genten'],  "nom_tercer" => $mDataCalifi['nom_tenedo'],  "dir_domici" => $mDataCalifi['dir_proten'], 
                            "num_telef1" => $mDataCalifi['fij_proten'],  "num_telmov" => $mDataCalifi['mov_proten'] 
                           );
      // Datos Conductor -------------------------------------------------------------------------------------------------------------------------------------
      $mDataConduc = array(
                            'cod_tercer' =>  $mDataCalifi['cod_conduc'], 'cod_tipdoc' =>  $mDataCalifi['cod_tipcon'], 'cod_ciudad' =>  $mDataCalifi['cod_ciucon'],
                            'cod_genero' =>  $mDataCalifi['cod_gencon'], 'nom_tercer' =>  $mDataCalifi['nom_conduc'], 'nom_apell1' =>  $mDataCalifi['ape_conduc'],
                            'dir_domici' =>  $mDataCalifi['dir_conduc'], 'num_telef1' =>  $mDataCalifi['fij_conduc'], 'num_telmov' =>  $mDataCalifi['mov_conduc'],
                            'cod_catlic' =>  $mDataCalifi['cod_catlic'], 'num_licenc' =>  $mDataCalifi['num_licenc'], 'fec_venlic' =>  $mDataCalifi['fec_venlic']
                          );
      // Datos Vehiculo -------------------------------------------------------------------------------------------------------------------------------------
      $mDataVehicu = array(
                            'num_placax' => $mDataCalifi['num_placax'], 'ano_modelo' => $mDataCalifi['ano_modelo'], 'num_motorx' => $mDataCalifi['num_motorx'],
                            'num_poliza' => $mDataCalifi['num_poliza'], 'cod_asesoa' => $mDataCalifi['cod_asesoa'], 'cod_ciusoa' => $mDataCalifi['cod_ciusoa']
                          );
                                                      
      // Array Para el  RIT WSDL ----------------------------------------------------------------------------------------------------------------------------
      if($mTypeMethodArray  == 'setCalifiConduc')
      {
        $mDataRit = array (                      
                          "nom_aplica" => "sat_trafico",
                          "nom_usuari" => URL_USERXX_RITXXX,
                          "pwd_clavex" => URL_PASSXX_RITXXX,
                          "cod_transp" => $mDataCalifi['cod_transp'], 
                          "cod_manifi" => $mDataCalifi['cod_manifi'],  
                          "cod_propie" => $mDataPropie,                    
                          "cod_tenedo" => $mDataTenedo,                    
                          "cod_conduc" => $mDataConduc,                    
                          "num_placax" => $mDataVehicu,                    
                          "cod_ciuori" => $mDataCalifi['cod_ciuori'] ,
                          "cod_ciudes" => $mDataCalifi['cod_ciudes'] ,
                          "num_califi" => $mAditionalData['num_califi'] ,
                          "des_noveda" => $mAditionalData['obs_califi'] ,
                          "ind_estado" => "1" ,
                          "nom_usrapl" => $mAditionalData['nom_usuari'] ,
                          "fec_califi" => date("Y-m-d") 
                        );
      }
      else if($mTypeMethodArray  == 'setDespacFinali')
      {
        $mDataRit = array (                      
                          "nom_aplica" => "sat_trafico",
                          "nom_usuari" => URL_USERXX_RITXXX,
                          "pwd_clavex" => URL_PASSXX_RITXXX,
                          "cod_transp" => $mDataCalifi['cod_transp'],//NIT_TRANSPOR, 
                          "cod_manifi" => $mDataCalifi['cod_manifi'],  
                          "cod_propie" => $mDataPropie,                    
                          "cod_tenedo" => $mDataTenedo,                    
                          "cod_conduc" => $mDataConduc,                    
                          "num_placax" => $mDataVehicu,                    
                          "cod_ciuori" => $mDataCalifi['cod_ciuori'] ,
                          "cod_ciudes" => $mDataCalifi['cod_ciudes'] ,
                          "num_despac" => $mDataCalifi['num_despac'] ,
                          "fec_llegad" => $mDataCalifi['fec_llegad'],
                          "obs_llegad" => $mDataCalifi['obs_llegad'] ,
                          "nom_usrapl" => $mDataCalifi['usr_modifi']
                        );
      }
      
    
      return $mDataRit;
     
    }
    
  /************************************************************************************************************
  * Metodo Privado que retorna los Estados                                                                   *
  * @fn GetEstado                                                                                            *
  * @param $mData Array : Arreglo POST                                                                       *
  * @return String : Cadena con ls diferencia de días                                                        *
  ************************************************************************************************************/
  function GetEstado( $mData, $mode = NULL )
  {
    $fec_actual = date( "Y:m:d" );
    $tie_actual = date( "H:i:s" );
  
    if ( $mData['fec_llegad1'] < $fec_actual )
    {
      $mSql = " SELECT ( DATEDIFF( '".$fec_actual."', '".$mData['fec_llegad1']."' ) ) AS diferencia ";
    }
    else
    {
      $mSql = " SELECT ( DATEDIFF( '".$mData['fec_llegad1']."', '".$fec_actual."' ) ) AS diferencia ";
    }
    $consulta = new Consulta( $mSql, $this -> cConexion );
    $diaven = $consulta -> ret_matriz( 'a' );
    $diaven = $diaven[0];

    $mSql = " SELECT TIMEDIFF('".$tie_actual."', '".$mData['fec_llegad2']."' ) AS tiempo ";
    $consulta = new Consulta( $mSql, $this -> cConexion );
    $horven = $consulta -> ret_matriz( 'a' );
    $horven = $horven[0];
    
    if( $mData['fec_llegad1'] < $fec_actual )
    {
      return $mode == NULL ? '<font color="#BB0000">' . $diaven['diferencia'] . " día(s) con " . $horven['tiempo'] . '</font>' : $diaven['diferencia'];
    }
    else
    {
      return $mode == NULL ? $diaven['diferencia'] . " día(s) con " . $horven['tiempo'] : $diaven['diferencia'];
    }
  }
  
}
?>
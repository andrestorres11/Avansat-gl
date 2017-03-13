<?php
   /*********************************************************************
  * @file InterfADSTrayectJLT.inc                                       *
  * @brief Libreria para consumir wsdl JLT - poliza por trayectos.      *
  * @version 0.1                                                        *
  * @date 30 de Mayo de 2015                                            *
  * @author Nelson Liberato                                             *
  **********************************************************************/
 
  class IntefADSTrayectJLT
  {
    var $cConexion  = NULL;
    var $cMsgError  = NULL;       
    var $cUrlWsdlx  = "https://web7.intrared.net:8083/ap/interf/app/jlt/wsdl/jlt.wsdl";
    var $cod_transp = NULL;
    /*! \fn: IntefADSTrayectJLT
    *  \brief: Contrucgtor para PHP4
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mConexion 
    *  \param: constantes
    *  \return BOOL
    */
    function __construct( $mConexion, $constantes = NULL )
    {     
      $this -> cConexion = $mConexion;
      $this->cod_transp = $constantes;

    }

    /*! \fn: sendError
    *  \brief: Indicador de notificacion Email
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \return BOOL
    */
    function sendError()
    {
      return false;
    }

    /*! \fn: setConexion
    *  \brief: funcion para reestableces la conexion a la BD
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mConexion 
    *  \return BOOL
    */    
    function setConexion( $mConexion )
    {
      $this -> cConexion = $mConexion;
    }
    
    /*! \fn: getConexion
    *  \brief: funcion para retornar la conexion a la BD
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mConexion 
    *  \return BOOL
    */ 
    function getConexion()
    {
      return $this -> cConexion;
    }
    
    /*! \fn: setMsgError
    *  \brief: funcion para almacenar String de errores a variable de clase
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mMsgError String 
    *  \return BOOL
    */ 
    function setMsgError( $mMsgError )
    {
      $this -> cMsgError = $mMsgError;
    }
    
    /*! \fn: getMsgError
    *  \brief: funcion para retornar String de errores de la variable de clase
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \return BOOL
    */ 
    function getMsgError()
    {
      return $this -> cMsgError;
    }
    

    /*! \fn: getValidaParam
    *  \brief: funcion valida envio automatico de solicitud JLT a ADS
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mNumDespac
    *  \param: mNumManifi
    *  \return array
    */ 
    function getValidaParam()
    {
      $mSql = 'SELECT val_parame FROM '.BASE_DATOS.'.tab_genera_parame WHERE nom_parame = "ind_autjlt" AND ind_estado = "1" ';
      $consulta = new Consulta( $mSql, $this -> cConexion );
      $mData = $consulta -> ret_matriz( 'a' );       
      return $mData[0]["val_parame"] == '1' ? true : false ;
    }


    /*! \fn: setPoliza
    *  \brief: funcion principal para la solictud de la poliza
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mNumDespac
    *  \param: mNumManifi
    *  \return array
    */ 
    function setPoliza( $mNumDespac = NULL,  $mNumManifi = NULL )
    {
      try
      {
          $mUsuari = array();
          $mPoliza = array();
          $mVehicu = array();
          $mTerpro = array();
          $mConduc = array();
          $mDespac = array();
          $mClient = array();
          $mRemesa = array();
          $mBenefi = array();
          $mColorx = array();
          $mLineax = array(); 

          $mUsuari = $this -> getDataUsuari();
          $mPoliza = $this -> getDataPoliza();
          $mVehicu = $this -> getDataVehicu( $mNumDespac );
          $mTerpro = $this -> getDataTercer( 'Propietario',$mVehicu["cod_propie"] );      
          $mConduc = $this -> getDataConduc( $mVehicu["cod_conduc"] );
          $mDespac = $this -> getDataDespac( $mNumDespac );
          $mClient = $this -> getDataClient( $mNumDespac );
          $mRemesa = $this -> getDataRemesa( $mNumDespac );
          $mBenefi = NULL; #$this -> getDataBenefi( $mNumDespac );  # Quien en el beneficiario ?
          $mColorx = NULL; #$this -> getDataColorx( $mNumDespac );
          $mLineax = NULL; #$this -> getDataLineax( $mNumDespac );
          /*
          $mUsuari =  array(  'cod_user' => "interfJlt" ,
                              'pas_user' => "interfJlt2015" ,
                              'cod_terc' => "800118909" ,
                              'cod_aseg' => "8600284155",
                              "url_webser" => "https://web7.intrared.net:8083/ap/interf/app/jlt/wsdl/jlt.wsdl"
                           );

          $mPoliza =  array(   
                             'cod_asegur'=>'8600284155',
                             'cod_tomado'=>'800118909'
                           );
          */
          $aParametros = array( "dat_usuari" => $mUsuari,
                                "dat_poliza" => $mPoliza, 
                                "dat_vehicu" => $mVehicu,
                                "dat_propie" => $mTerpro,
                                "dat_conduc" => $mConduc,
                                "dat_despac" => $mDespac,
                                "dat_client" => $mClient,
                                "dat_remesa" => $mRemesa,
                                "dat_benefi" => $mBenefi,
                                "dat_colorx" => $mColorx,
                                "dat_lineax" => $mLineax
                                );
          #Valida si debe hacer interfaz con JLT, en tab_interf_parame_temp con el codigo 70
          //if($mUsuari["url_webser"] == '' || $this -> getValidaParam() == false  ) {
          if($mUsuari["url_webser"] == '' ) {
            return array("cod_respon"=>"9999", "msg_respon"=>"No Usa JLT");
          }
          /*if( $this -> getValidaParam("ind_autjlt") == false ) { 
            # Si la solcitud viene de un servicio que no sea el de solicitud de seguros -> Solcitud, entra a evaluar el check de plan de ruta
            if($_REQUEST["cod_servic"] != '5049') {  #cod_servic = 10216: Solicitud de Seguros -> insertar
                  # evalua el check de plan de ruta para hacer una solicitud
                // if($_POST["ind_soltra"] != 'on') {
                return array("cod_respon"=>"9999", "msg_respon"=>"No hace solicitud de Poliza por trayectos");
                //} 
            }               
          }*/

 
          $oSoapClient = new SoapClient( $mUsuari["url_webser"], array( "encoding" => "utf8") );
          $mRespon = $oSoapClient -> __soapCall( "setPoliza", $aParametros );



          //Procesa el resultado del WS
          if( "1000" != $mRespon["cod_respons"] )
          {
            $this -> setDataErrorWsdl( $mNumDespac, $mNumManifi, $aParametros, $mRespon, NULL ); 
          }
          else
          {
            $this -> setDataOkWsdl( $mNumDespac, $mNumManifi, $aParametros, $mRespon );
          }
          $mReturn = array("cod_respon"=>$mRespon["cod_respons"], 
                            "msg_respon"=>$mRespon["msg_respons"],
                            "num_certif"=>$mRespon["dat_polizax"]["num_certif"]
                            );
      }
      catch(soapFault $e)
      {
        $mReturn = array("cod_respon"=>"3001", "msg_respon"=>$e -> getMessage(),"pol_respon"=>array() );
        $this -> setDataErrorWsdl( $mNumDespac, $mNumManifi, $aParametros, $mRespon, $e -> getMessage() ); 
      }
      return $mReturn;
    }

    /*! \fn: setDataErrorWsdl
    *  \brief: funcion para resgistrar datos error del webservice
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \return array
    */ 
    function setDataErrorWsdl($mNumDespac, $mNumManifi, $aParametros, $mRespon, $mFault = NULL)
    {
      
      if($mFault == NULL)
      {
        $mQuery = ' INSERT INTO '.BASE_DATOS.'.tab_errorx_poltra 
                    (num_despac, cod_respon, msg_respon, dat_solici, usr_creaci, fec_creaci) 
                    VALUES 
                    ( "'.$mNumDespac.'", "'.$mRespon["cod_respons"].'", "'.$mRespon["msg_respons"].'", "'.var_export($aParametros, true).'",
                      "'.$_SESSION["datos_usuario"]["cod_usuari"].'", NOW() )';
      }
      else
      {
         $mQuery = ' INSERT INTO '.BASE_DATOS.'.tab_errorx_poltra 
                    (num_despac, cod_respon, msg_respon, dat_solici, usr_creaci, fec_creaci) 
                    VALUES 
                    ( "'.$mNumDespac.'", "3001", "'.$mFault.'", "'.var_export($aParametros, true).'",
                      "'.$_SESSION["datos_usuario"]["cod_usuari"].'", NOW() )';
      }
       
      $consulta = new Consulta( $mQuery, $this -> cConexion );
    }

    /*! \fn: setDataOkWsdl
    *  \brief: funcion para registrar datos exitosos de solictud de JLT
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \return array
    */ 
    function setDataOkWsdl($mNumDespac, $mNumManifi, $aParametros, $mRespon)
    {
      $mQuery = ' INSERT INTO '.BASE_DATOS.'.tab_manifi_poltra 
                  (num_despac, cod_manifi, num_poliza, num_certif, usr_creaci, fec_creaci) 
                  VALUES 
                  ( "'.$mNumDespac.'", "'.$mNumManifi.'", "'.$mRespon["dat_polizax"]["num_poliza"].'", 
                    "'.$mRespon["dat_polizax"]["num_certif"].'", "'.$_SESSION["datos_usuario"]["cod_usuari"].'", NOW() )';
      $consulta = new Consulta( $mQuery, $this -> cConexion );
    }

    /*! \fn: getDataUsuari
    *  \brief: funcion para consultar datos de conexion al wsdl
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \return array
    */ 
    function getDataUsuari()
    {
        $mQuery = "SELECT  a.nom_usuari AS cod_user,a.clv_usuari AS pas_user ,a.cod_transp AS cod_terc, '8600284155' AS cod_aseg, a.url_webser
                     FROM ".BASE_DATOS.".tab_interf_parame a  
                     WHERE a.cod_operad = '70' AND 
                           a.ind_estado = '1' AND
                           a.cod_transp ='".$this->cod_transp."' ";         
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );       
        return $mData[0];      
    }

    /*! \fn: getDataPoliza
    *  \brief: funcion para consultar datos la aseguradora y tomador de la poliza
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \return array
    */ 
    function getDataPoliza()
    {
        $mQuery = "SELECT  '8600284155' AS cod_asegur,a.cod_tercer AS cod_tomado 
                     FROM ".BASE_DATOS.".tab_tercer_tercer a 
                    WHERE a.cod_tercer ='".$this->cod_transp."'    ";         
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );       
        return $mData[0];      
    }


    /*! \fn: getBinFoto
    *  \brief: funcion para Codificar a base64 las imagenes fisicas almacenadas 
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mType
    *  \param: mPosicion
    *  \param: mDirUrlFot
    *  \return String
    */ 
    function getBinFoto($mType = NULL, $mPosicion = NULL, $mDirUrlFot = NULL  )
    {
        switch ($mType) 
        {
          case 'vehiculo':      
                 if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/".BASE_DATOS."/fotveh/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/".BASE_DATOS."/fotveh/".$mDirUrlFot) );
                 }
          break;
          case 'trayler':
                if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/".BASE_DATOS."/remolque/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/".BASE_DATOS."/remolque/".$mDirUrlFot) );
                 }
          break; 
          case 'conductor':
                if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/".BASE_DATOS."/fotcon/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/".BASE_DATOS."/fotcon/".$mDirUrlFot) );
                 }
          break;         
          
        }
    }

    /*! \fn: getDataVehicu
    *  \brief: funcion para consultar datos del vehiculo
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mNumDespac
    *  \return array
    */ 
    function getDataVehicu($mNumDespac = NULL)
    {
      $mQuery = "SELECT   b.num_placax, b.cod_marcax, b.cod_lineax, b.cod_carroc, b.ano_modelo,b.ano_repote, 
                          (
                            SELECT num_trayle  FROM ".BASE_DATOS.".tab_trayle_placas c 
                             WHERE c.num_placax = b.num_placax ORDER BY num_noveda DESC LIMIT 1 
                          ) AS num_remolq, 
                          b.num_config, if(b.num_motorx='','1234567',b.num_motorx) AS num_motorx, b.cod_colorx, b.val_pesove, b.num_seriex, 
                          b.cod_propie, b.cod_tenedo, a.cod_conduc
                     FROM                           
                          ".BASE_DATOS.".tab_despac_vehige a, 
                          ".BASE_DATOS.".tab_vehicu_vehicu b                           
                    WHERE a.num_placax = b.num_placax AND
                          a.num_despac = '{$mNumDespac}' "; 

        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );
        return $mData[0];       
    }

    /*! \fn: getDataTercer
    *  \brief: funcion para consultar datos de terceros Propietario, Tenedor
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mType
    *  \param: mCodTercer
    *  \return array
    */ 
    function getDataTercer($mType = NULL, $mCodTercer = NULL)
    {
      $mTipTercer = ($mType == 'Propietario' ? 'propie' : ($mType == 'Tenedor' ? 'tenedo' : ( $mType == 'Conductor'? 'conduc' : 'ninguno' ) ) );
      switch ($mType) {
        case 'Propietario':
               $mQuery = "SELECT  a.cod_tercer AS cod_propie, a.cod_tipdoc AS cod_docpro, a.nom_tercer AS nom_propie, 
                                  a.abr_tercer AS abr_prorie, CONCAT(a.nom_apell1,' ',a.nom_apell2) AS ape_propie,
                                  a.num_telmov AS cel_propie, a.dir_domici AS dir_propie, 
                                  a.cod_paisxx AS pai_propie, a.cod_depart AS dep_propie, a.cod_ciudad AS ciu_propie,
                                  IF( a.num_telef1 = '' , a.num_telef2, a.num_telef1 ) AS tel_propie, 'M' AS gen_propie,
                                  '".$mTipTercer."' AS tip_tercer
                            FROM ".BASE_DATOS.".tab_tercer_tercer a  
                           WHERE a.cod_tercer = '{$mCodTercer}' "; 
        break;
       
      }
     
      $consulta = new Consulta( $mQuery, $this -> cConexion );
      $mData = $consulta -> ret_matriz( 'a' );           
      return $mData[0];
    }

   /*! \fn: getDataConduc
    *  \brief: funcion para consultar datos del conductor
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: mCodConduc
    *  \return array
    */ 
    function getDataConduc($mCodConduc = NULL)
    {      
        $mQuery = "SELECT  a.cod_tercer AS cod_conduc, a.cod_tipdoc AS cod_tipdoc, 
                          a.cod_paisxx AS pai_conduc, a.cod_depart AS dep_conduc, a.cod_ciudad AS ciu_conduc,
                          IF(b.cod_tipsex = '1' , 'M', 'F') AS gen_conduc, b.fec_venlic,
                          a.nom_tercer AS nom_conduc, b.num_catlic AS cod_catego, b.num_licenc AS lic_conduc,
                          CONCAT(a.nom_apell1,' ',a.nom_apell2) AS ape_conduc, a.abr_tercer AS abr_conduc,
                          a.dir_domici AS dir_conduc,
                          IF( a.num_telef1 = '' , a.num_telef2, a.num_telef1 ) AS tel_conduc,
                          a.num_telmov AS cel_conduc,
                          a.dir_emailx AS ema_conduc 
                    FROM ".BASE_DATOS.".tab_tercer_tercer a ,
                         ".BASE_DATOS.".tab_tercer_conduc b 
                   WHERE a.cod_tercer = b.cod_tercer AND
                         b.cod_tercer = '{$mCodConduc}' ";  
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );            
        return $mData[0];
    } 

    /*! \fn: getDataDespac
    *  \brief: Consulta datos del despacho
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: $mNumDespac
    *  \return array
    */
    function getDataDespac($mNumDespac = NULL)
    {      
        $mQuery = "SELECT a.cod_manifi AS num_manifi, a.val_flecon AS val_declar, 
                         IF(a.cod_tipdes = '0' , '1', IF( a.cod_tipdes = '1', '2', '1' )  ) AS cod_despac,
                         date(a.fec_salida) AS fec_salida, 
                         date(b.fec_llegpl) AS fec_entreg, 
                         a.cod_ciuori, a.cod_ciudes 
                    FROM ".BASE_DATOS.".tab_despac_despac a ,
                         ".BASE_DATOS.".tab_despac_vehige b 
                   WHERE a.num_despac = b.num_despac AND
                         b.num_despac = '{$mNumDespac}' ";  
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );            
        return $mData[0];
    }
    
    /*! \fn: getDataClient
    *  \brief: Consulta datos de los clientes de cada remesa que tiene el manifiesto
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: $mNumDespac
    *  \return array
    */
    function getDataClient($mNumDespac = NULL)
    {      
        $mQuery = "SELECT IF(e.cod_tipdoc IN ('C', 'T'), 'N', 'J'  ) AS tip_cliente,
                          e.cod_tipdoc AS cod_doccli ,
                          e.cod_tercer AS cod_cliente,
                          e.nom_tercer AS nom_cliente,
                          CONCAT(e.nom_apell1,' ', e.nom_apell2) AS ape_cliente,
                          e.num_telmov AS cel_cliente,
                          e.abr_tercer AS abr_cliente,
                          e.dir_domici AS dir_cliente,
                          e.cod_paisxx AS pai_cliente,
                          e.cod_depart AS dep_cliente,
                          e.cod_ciudad AS ciu_cliente,
                          e.num_telef1 AS tel_cliente,
                          'N' AS gen_cliente
                    FROM ".BASE_DATOS.".tab_despac_despac a ,                    
                         ".BASE_DATOS.".tab_despac_destin d ,
                         ".BASE_DATOS.".tab_tercer_tercer e

                   WHERE a.num_despac = d.num_despac AND 
                         d.cod_genera = e.cod_tercer AND
                         a.num_despac = '{$mNumDespac}' ";  
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );            
        return $mData;
    }

    /*! \fn: getDataRemesa
    *  \brief: Consulta datos de las remesas asignadas al manifiesto
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: $mNumDespac
    *  \return array
    */
    function getDataRemesa($mNumDespac = NULL)
    {      
        $mQuery = "SELECT d.num_docume AS dan_remesa,
                          d.cod_genera AS nit_propie,
                          b.abr_tercer AS dan_client,
                          if(e.des_mercan ='','varios',e.des_mercan) AS dan_mercan
                    FROM ".BASE_DATOS.".tab_despac_despac a 
                         LEFT JOIN ".BASE_DATOS.".tab_despac_remesa e ON a.num_despac = e.num_despac, 
                         ".BASE_DATOS.".tab_tercer_tercer b ,                
                         ".BASE_DATOS.".tab_despac_destin d  
                   WHERE a.num_despac = d.num_despac AND  
                         d.cod_genera = b.cod_tercer AND                       
                         a.num_despac = '{$mNumDespac}' ";  
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );            
        return $mData;
    }

    /*! \fn: getDataBenefi
    *  \brief: Consulta datos de beneficiario y nombre de la mercancia
    *  \author: Nelson Liberato
    *  \date: 30/05/2015
    *  \date modified: 30/05/2015
    *  \param: $mNumDespac
    *  \return array
    */
    function getDataBenefi($mNumDespac = NULL)
    {      
        $mQuery = "SELECT d.cod_remesa AS dan_remesa,
                          d.cod_client AS nit_propie,
                          d.cod_client AS dan_client,
                          e.abr_mercan AS dan_mercan
                    FROM ".BASE_DATOS.".tab_despac_despac a ,                    
                         ".BASE_DATOS.".tab_manifi_cargax b ,
                         ".BASE_DATOS.".tab_relaci_remman c ,
                         ".BASE_DATOS.".tab_remesa_oguiax d , 
                         ".BASE_DATOS.".tab_genera_mercan e 
                   WHERE a.cod_manifi = b.cod_manifi AND
                         b.cod_manifi = c.cod_manifi AND
                         c.cod_remesa = d.cod_remesa AND  
                         d.cod_mercan = e.cod_mercan AND                       
                         a.num_despac = '{$mNumDespac}' ";  
        $consulta = new Consulta( $mQuery, $this -> cConexion );
        $mData = $consulta -> ret_matriz( 'a' );            
        return $mData;
    }
   
    /*! \fn: getCertifData
     *  \brief: trae la informacion del manifiesto
     *  \author: Ing. Jose Luis Guerrero
     *    \date: 11/07/2016
     *    \date modified: dia/mes/año
     *  \param: parametro 1
     *  \param: parametro 2
     *  \return valor que retorna
     */
    
    function getCertifData($num_manifi){

      $mUsuari = $this -> getDataUsuari();
      $aParametros = array(
                        "dat_usuari" => $mUsuari,
                        "cod_manifi" => $num_manifi
                     ); 
      $oSoapClient = new SoapClient( $mUsuari["url_webser"], array( "encoding" => "utf8") );
      $mRespon = $oSoapClient -> __soapCall ( "DatosPoliza", $aParametros );      
      return array("cod_respon"=>$mRespon["cod_respons"], "msg_respon"=>$mRespon["msg_respons"], 
                        'dat_poliza' => $mRespon['dat_poliza'], 'dat_amparos' => $mRespon['dat_amparos']);     
    }    

    function setAnulaCertif($num_manifi,$obs_estado,$cod_transp){

      $mUsuari = $this -> getDataUsuari();
      $aParametros = array(
                        "num_manifi" => $num_manifi,
                        "num_certif" => '',
                        "obs_estado" => $obs_estado,
                        "cod_motivo" => '7',
                        "cod_asegur" => '8600284155',
                        "cod_tomado" => $cod_transp,
                        "dat_usuari" => $mUsuari,
                     );
                           
      $oSoapClient = new SoapClient( $mUsuari["url_webser"], array( "encoding" => "utf8") );
      $mRespon = $oSoapClient -> __soapCall ( "anulaPoliza", $aParametros );
      return array("cod_respon"=>$mRespon["cod_respons"], "msg_respon"=>$mRespon["msg_respons"], 
                        'dat_poliza' => $mRespon['dat_poliza'], 'dat_amparos' => $mRespon['dat_amparos']);

    }
 

  }

?>
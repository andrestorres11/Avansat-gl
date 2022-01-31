<?php

  ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache

  class TraficoPad
  {
    private $cConexion = NULL;
    private $cMsgError = "";
    private $cUrlWsdlA = "https://oet.intrared.net/ap/pad_asistr/ws/server.wsdl";
    private $cParamRec = "";
    private $cParamDis = "";

    private static $cBD;

    function __construct( $mCo = NULL)
    {
        $mHost = $_SERVER['HTTP_HOST'];
        $mHost = explode('.', $mHost);

        switch ($mHost[0]) {
            case 'web7':      self::$cBD = "bd7.intrared.net:3306";  break;
            case 'web13':     self::$cBD = "bd13.intrared.net:3306"; break;
            case 'avansatgl': self::$cBD = "aglbd.intrared.net";     break;
            default:          self::$cBD = "demo.intrared.net";      break;
        }

         $this -> cConexion = $mCo;
    }

    function setMsgError( $mMsgError )
    {
      $this -> cMsgError = $mMsgError;
    }

    function getMsgError()
    {
      return $this -> cMsgError;
    }


    function sendError( $mSendData, $mAditionalData, $mArraData = NULL )
    {
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Operacion: Interfaz Trafico - PAD \n";
      $mMessage .= "WSDL: ".$this->cUrlWsdlA." \n";
      $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
      $mMessage .= "Empresa de transporte: Trafico \n";
      $mMessage .= "Aplicacion: satt_faro \n";
      $mMessage .= "Despacho: ".$mSendData["num_despac"]." \n";
      $mMessage .= "Placa: ".$mSendData["num_placax"]." \n";
      $mMessage .= "Operador: ".$mAditionalData['nom_operad']." \n";
      $mMessage .= "******** Detalle ******** \n";
      $mMessage .= "Codigo de error: ".$mAditionalData['cod_errorx']." \n";
      $mMessage .= "Mensaje de error: ".$this -> getMsgError()." \n";
      $mMessage .= "Datos Array: ".var_export($mArraData, true)." \n";
      //mail( "nelson.liberato@intrared.net", "Web service Faro - PAD ERROR (ASISTR) FROM WEB10", $mMessage,'From: InterfazTraficoPad@intrared.net' );
      
    }

    function sendOk( $mSendData, $mAditionalData )
    {
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Operacion: Interfaz Trafico - PAD \n";
      $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";     
      $mMessage .= "Empresa de transporte: Trafico \n";
      $mMessage .= "Aplicacion: satt_faro \n";
      $mMessage .= "Despacho: ".$mSendData["num_despac"]." \n";
      $mMessage .= "Placa: ".$mSendData["num_placax"]." \n";
      $mMessage .= "******** Detalle ******** \n";       
      $mMessage .= "Mensaje: ".$this -> getMsgError()." \n";
     
     // mail( "omar.olarte@eltransporte.org, nelson.liberato@intrared.net", "Web service Faro - PAD (ASISTR) OK FROM WEB10 ", $mMessage,'From: InterfazTraficoPad@intrared.net' );
      
    }
    

    
    # Funcion para Traer datos de consultas ---------------------------------------------------------------------------------------------------------------------
    function getDataDespac( $mNumDespac )
    {
      $mQuery = "SELECT     a.num_despac, a.cod_manifi, a.fec_despac, 
                            a.cod_paiori, a.cod_depori, a.cod_ciuori, 
                            a.cod_paides, a.cod_depdes, a.cod_ciudes,
                            a.cod_agedes, a.fec_salida, a.obs_salida,
                            a.fec_llegad, a.con_telmov, 
                            b.cod_transp, b.cod_rutasx,
                            b.num_placax, b.num_trayle AS num_trayle,
                            c.cod_propie, 
                            c.cod_tenedo,
                            b.cod_conduc 
                     FROM   tab_despac_despac a, 
                            tab_despac_vehige b,
                            tab_vehicu_vehicu c 
                    WHERE   a.num_despac = b.num_despac AND
                            b.num_placax = c.num_placax AND
                            a.num_despac = '{$mNumDespac}' ";      
      $mData = TraficoPad::Consulta( $mQuery, true);        
      return $mData[0];       
    }

    function getDataVehicu( $mNumPlacax = NULL)
    {
        $mQuery = "SELECT a.num_placax, a.cod_marcax, a.cod_lineax, a.ano_modelo,
                          a.cod_colorx, a.cod_carroc, a.num_motorx, a.num_seriex,
                          a.num_chasis, a.val_pesove, a.val_capaci, a.reg_nalcar,
                          a.num_poliza, a.nom_asesoa, a.cod_ciusoa, a.fec_vigfin,
                          a.ano_repote, a.num_config, a.cod_propie, a.cod_tenedo,
                          a.cod_conduc, a.nom_vincul, a.num_tarpro, a.num_tarope,
                          a.cod_califi, a.fec_vigvin, a.num_polirc, a.fec_venprc,
                          a.cod_aseprc, 
                          a.dir_fotfre,
                          a.dir_fotizq,
                          a.dir_fotder,
                          a.dir_fotpos,
                          a.cod_tipveh, a.ind_chelis, a.fec_revmec,
                          a.num_agases, a.fec_vengas, a.obs_vehicu, a.ind_estado,
                          a.obs_estado, a.cod_paisxx , b.nom_marcax, c.nom_lineax, d.nom_colorx, e.nom_carroc
                     FROM tab_vehicu_vehicu a INNER JOIN tab_genera_marcas b  ON a.cod_marcax = b.cod_marcax 
                          LEFT JOIN tab_vehige_lineas c  ON a.cod_marcax = c.cod_marcax AND a.cod_lineax = c.cod_lineax 
                          LEFT JOIN tab_vehige_colore d  ON a.cod_colorx = d.cod_colorx 
                          LEFT JOIN tab_vehige_carroc e  ON a.cod_carroc = e.cod_carroc 
                    WHERE num_placax = '{$mNumPlacax}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);  

        # Homologa codigos RNDC
        $mMarcLinea = TraficoPad::getMarcaxLineax( $mData[0]["nom_marcax"],  $mData[0]["nom_lineax"] );
        $mCodColorx = TraficoPad::getColorx( $mData[0]["nom_colorx"] );
        $mCodCarroc = TraficoPad::getCarroc( $mData[0]["nom_carroc"] );


        $mData[0]["cod_marcax"] = $mMarcLinea[marcax][0][cod_marcax];
        $mData[0]["cod_lineax"] = $mMarcLinea[lineax][0][cod_lineax];
        $mData[0]["cod_colorx"] = $mCodColorx[0][cod_colorx];
        $mData[0]["cod_carroc"] = $mCodCarroc[0][cod_carroc];




        return $mData[0];       
    }

    function getMarcaxLineax($mNomMarcax, $mNomLineax)
    {
      $mSql = "SELECT a.cod_marcax, a.nom_marcax 
             FROM satt_standa.tab_genera_marcas a 
            WHERE a.nom_marcax = '$mNomMarcax' ;";
       
      $mResult[marcax] = TraficoPad::Consulta( $mSql, true); 

      if(!$mResult[marcax])
      {
        $mSql = "SELECT a.cod_marcax, a.nom_marcax 
               FROM satt_standa.tab_genera_marcas a 
              WHERE a.nom_marcax LIKE '$mNomMarcax%' ;";
       
        $mResult[marcax] = TraficoPad::Consulta( $mSql, true); 

        if(!$mResult[marcax]){
          $mResult[marcax] = array( 0 => array('cod_marcax' => '1', 'nom_marcax' => 'CHEVROLET' ) );
          $mResult[lineax] = array( 0 => array('cod_lineax' => '353', 'nom_lineax' => 'SIN LINEA' ) );
        }else{
          $mSql = "SELECT a.cod_lineax, a.nom_lineax 
                 FROM satt_standa.tab_genera_lineas a 
                WHERE a.nom_lineax = 'SIN LINEA' 
                AND a.cod_marcax = '".$mResult[marcax][0][cod_marcax]."' "; 
          $mResult[lineax] = TraficoPad::Consulta( $mSql, true); 

          if(!$mResult[lineax]){
            $mSql = "SELECT a.cod_lineax, a.nom_lineax 
                   FROM satt_standa.tab_genera_lineas a 
                  WHERE a.cod_marcax = '".$mResult[marcax][0][cod_marcax]."' 
                  LIMIT 1 ";             
            $mResult[lineax] = TraficoPad::Consulta( $mSql, true); 
          }

        }
      }else{
        $mSql = "SELECT a.cod_lineax, a.nom_lineax 
               FROM satt_standa.tab_genera_lineas a 
              WHERE a.nom_lineax = '$mNomLineax' 
              AND a.cod_marcax = '".$mResult[marcax][0][cod_marcax]."' ";        
        $mResult[lineax] = TraficoPad::Consulta( $mSql, true); 

        if(!$mResult[lineax]){
          $mSql = "SELECT a.cod_lineax, a.nom_lineax 
                 FROM satt_standa.tab_genera_lineas a 
                WHERE a.nom_lineax = 'SIN LINEA' 
                AND a.cod_marcax = '".$mResult[marcax][0][cod_marcax]."' ";
          
          $mResult[lineax] = TraficoPad::Consulta( $mSql, true); 

          if(!$mResult[lineax]){
            $mSql = "SELECT a.cod_lineax, a.nom_lineax 
                   FROM satt_standa.tab_genera_lineas a 
                  WHERE a.cod_marcax = '".$mResult[marcax][0][cod_marcax]."' 
                  LIMIT 1 ";
            
            $mResult[lineax] = TraficoPad::Consulta( $mSql, true); 
          }
        }
      }

      return $mResult;
    }

    function getColorx($mNomColorx)
    {
      $mSql = "SELECT a.cod_colorx, a.nom_colorx 
             FROM satt_standa.tab_genera_colorx a 
            WHERE a.nom_colorx LIKE '$mNomColorx' ;";
       
      $mResult = TraficoPad::Consulta( $mSql, true); 

      if(!$mResult){
        $mSql = "SELECT a.cod_colorx, a.nom_colorx 
               FROM satt_standa.tab_genera_colorx a 
              WHERE a.nom_colorx LIKE '$mNomColorx%' 
              LIMIT 1 ";
       
        $mResult =  TraficoPad::Consulta( $mSql, true); 

        if(!$mResult){
          $mResult = array('0' => array( 'cod_colorx' => '2815', 'nom_colorx' => 'SIN COLOR EXTERIOR' ) );
        }
      }
      return $mResult;
    }

    function getCarroc($mNomCarroc)
    {
      $mSql = "SELECT a.cod_carroc, a.nom_carroc 
             FROM satt_standa.tab_genera_carroc a 
            WHERE a.nom_carroc LIKE '$mNomCarroc' ;";       
      $mResult = TraficoPad::Consulta( $mSql, true); 

      if(!$mResult){
        
        $mSql = "SELECT a.cod_carroc, a.nom_carroc 
               FROM satt_standa.tab_genera_carroc a 
              WHERE a.nom_carroc LIKE '$mNomCarroc%' ;";
         
        $mResult = TraficoPad::Consulta( $mSql, true); 

        if(!$mResult){
          $mResult = array( 0 => array( 'cod_carroc' => '0', 'nom_carroc' => 'NO REGISTRADO' ) );
        }
      }
      return $mResult;
    }



    function getDataTrayle( $mNumTrayle = NULL )
    {
        $mQuery = "SELECT a.num_trayle, a.cod_marcax, a.cod_colore, a.dir_fottra,
                          a.cod_carroc, a.ano_modelo, a.nro_ejes, a.ser_chasis,
                          a.tra_anchox, a.tra_altoxx, a.tra_largox, a.tra_volpos,
                          a.tra_pesoxx, a.tra_capaci, a.tip_tramit, a.nom_propie,
                          a.cod_config, a.ind_estado 
                     FROM tab_vehige_trayle a  
                    WHERE num_trayle = '{$mNumTrayle}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];       
    }

    function getDataTercero($mType = NULL, $mCodTercer = NULL)
    {
        $mQuery = "SELECT a.cod_tercer, a.num_verifi, a.cod_tipdoc, a.cod_terreg,
                        a.nom_apell1, a.nom_apell2, a.nom_tercer, a.abr_tercer,
                        a.dir_domici, a.num_telef1, a.num_telef2, a.num_telmov,
                        a.num_faxxxx, a.cod_paisxx, a.cod_depart, IF( a.cod_ciudad = '0' , '1', a.cod_ciudad ) AS cod_ciudad,
                        a.dir_emailx, a.dir_urlweb, a.cod_estado, a.dir_ultfot,
                        a.obs_tercer 
                   FROM tab_tercer_tercer a  
                  WHERE a.cod_tercer = '{$mCodTercer}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];
    }

    function getDataConduc($mCodConduc = NULL)
    {      

        $mQuery = "SELECT a.cod_tercer, a.cod_tipsex, a.cod_grupsa, a.num_licenc,
                        a.num_catlic, a.fec_venlic, a.cod_califi, a.num_libtri,
                        a.fec_ventri, a.obs_habili, a.nom_epsxxx, a.nom_arpxxx,
                        a.nom_pensio, a.num_pasado, a.fec_venpas, a.nom_refper,
                        a.tel_refper, a.obs_conduc, a.cod_operad  
                   FROM tab_tercer_conduc a  
                  WHERE a.cod_tercer = '{$mCodConduc}' ";         
        $mData = TraficoPad::Consulta( $mQuery, true);        
        return $mData[0];
    }

    function getBinFoto($mType = NULL, $mPosicion = NULL, $mDirUrlFot = NULL  )
    {
        switch ($mType) 
        {
          case 'vehiculo':      
                 if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotveh/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotveh/".$mDirUrlFot) );
                 }
          break;
          case 'trayler':
                if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/remolque/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/remolque/".$mDirUrlFot) );
                 }
          break; 
          case 'conductor':
                if( file_exists($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotcon/".$mDirUrlFot)){
                  return base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/ap/satt_faro/fotcon/".$mDirUrlFot) );
                 }
          break;         
          
        }
    }

    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # Funcion que inserta Los datos de los recursos -------------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    function SetDataRecursos( $mDataDespac )
    {       

      # Datos del Despacho -------------------------------------------------------------------------------------------------------------------------
      $mDespac = TraficoPad::getDataDespac( $mDataDespac["num_despac"]);
     
      # Datos del vehiculo -------------------------------------------------------------------------------------------------------------------------
      $mVehicu = TraficoPad::getDataVehicu( $mDespac["num_placax"] );
      $mVehicu["bin_fotfre"] = $mVehicu["dir_fotfre"] ? TraficoPad::getBinFoto( 'vehiculo', 'frentexxx',$mVehicu["dir_fotfre"] ) : '';
      $mVehicu["bin_fotizq"] = $mVehicu["dir_fotizq"] ? TraficoPad::getBinFoto( 'vehiculo', 'izquierda',$mVehicu["dir_fotizq"] ) : '';
      $mVehicu["bin_fotder"] = $mVehicu["dir_fotder"] ? TraficoPad::getBinFoto( 'vehiculo', 'derechaxx',$mVehicu["dir_fotder"] ) : '';
      $mVehicu["bin_fotpos"] = $mVehicu["dir_fotpos"] ? TraficoPad::getBinFoto( 'vehiculo', 'porterior',$mVehicu["dir_fotpos"] ) : '';
      
      # Datos del trayler --------------------------------------------------------------------------------------------------------------------------
      if( $mDespac["num_trayle"] ) {
        $mtrayle = TraficoPad::getDataTrayle( $mDespac["num_trayle"]); 
        $mtrayle["bin_fottra"] = $mtrayle["dir_fottra"] ? TraficoPad::getBinFoto( 'trayler', 'generalx', $mtrayle["dir_fottra"] ) : '';
      }
      # Datos Propietario --------------------------------------------------------------------------------------------------------------------------
      $mTerpro = TraficoPad::getDataTercero('Propietario',$mVehicu["cod_propie"]);      
      
      # Datos Tenedor ------------------------------------------------------------------------------------------------------------------------------
      $mTerten = TraficoPad::getDataTercero('Tenedor',$mVehicu["cod_tenedo"]);      


      # Datos Conductor ----------------------------------------------------------------------------------------------------------------------------
      $mTercon = TraficoPad::getDataTercero('Conductor',$mDespac["cod_conduc"]);  
      $mTercon["bin_ultfot"] = $mTercon["dir_ultfot"] ? TraficoPad::getBinFoto( 'conductor', 'frente', $mTercon["dir_ultfot"] ) : '';
      $mConduc = TraficoPad::getDataConduc($mTercon["cod_tercer"]);
        
      

      # Matriz de Datos ----------------------------------------------------------------------------------------------------------------------------
      $this -> cParamRec  = array(
                        "cod_usuari" => "faro-pad",
                        "clv_passwd" => "13f11366ba",                        
                        "cod_aplica" => "pad",                        
                        "dat_despac" => $mDespac,
                        "dat_vehicu" => $mVehicu,
                        "dat_trayle" => $mtrayle,
                        "dat_terpro" => $mTerpro,
                        "dat_terten" => $mTerten,
                        "dat_tercon" => $mTercon,
                        "dat_conduc" => $mConduc
                     );
     
      try
      {
        $oSoapClient = new soapclient( $this -> cUrlWsdlA, array( 'encoding'=>'ISO-8859-1' ) );       
        $mReturn = $oSoapClient -> __call( 'setRecursos', $this -> cParamRec );
         
        $mReturn = explode(";", $mReturn);
        $mCodere = explode(":", $mReturn[0]);
        $mMesage = explode(":", $mReturn[1]);

        if($mCodere[1] !== '1000')
        {
          $mAditionalData['nom_proces'] = 'Interf Faro - Pad';
          $mAditionalData['nom_operad'] = 'Faro: InterfPad';
          $mAditionalData['cod_errorx'] = 'Interf Faro - Pad';
          TraficoPad::setMsgError( $mReturn[1] );
          TraficoPad::sendError( $mDataDespac, $mAditionalData, $this -> cParamRec );
              
          $mReturn[0] = false; 
          $mReturn[1] = $mMesage[1];        
          return $mReturn;
        }
        else
        {
            # Llama el segundo metodo para dejar la placa como disponible
            return TraficoPad::Processing( $mDataDespac );
        }
         

      }
      catch( SoapFault $e )
      {
          $error_ = $e -> getMessage();
          $mAditionalData['nom_proces'] = 'Interf Faro - Pad';
          $mAditionalData['nom_operad'] = 'Faro: InterfPad';
          $mAditionalData['cod_errorx'] = 'Interf Faro - Pad';
          TraficoPad::setMsgError( $error_  );
          TraficoPad::sendError( $mDataDespac, $mAditionalData , $this -> cParamRec);
              
          $mReturn[0] = false; 
          $mReturn[1] = $error_;        
        
          return $mReturn;
  
            
             
      }


    }


    


    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    # Funcion que inserta Los datos vehiculo Disponible ---------------------------------------------------------------------------------------------------------
    # -----------------------------------------------------------------------------------------------------------------------------------------------------------
    function Processing( $mDataDespac )
    { 
      try
      {
        $mDespac = TraficoPad::getDataDespac( $mDataDespac["num_despac"] );      
        $this -> cParamDis = array("inputs" => "num_placax:" . $mDespac["num_placax"] . "; cod_ciuori:" . $mDespac["cod_ciudes"],
                            "aplica" => "pad",
                            "module" => "dispo",
                            "action" => "insert",
                            "cod_usuari" => "faro-pad",
                            "clv_passwd" => "13f11366ba"
                            );
         
       
        $oSoapClient = new soapclient( $this -> cUrlWsdlA, array( 'encoding'=>'ISO-8859-1' ) );         
        $mReturn = $oSoapClient -> __call( 'Processing', $this -> cParamDis );
         
        $mReturn = explode(";", $mReturn);
        $mCodere = explode(":", $mReturn[0]);
        $mMesage = explode(":", $mReturn[1]);

        if($mCodere[1] !== '1000')
        {
          $mAditionalData['nom_proces'] = 'Interf Faro - Pad';
          $mAditionalData['nom_operad'] = 'Faro: InterfPad';
          $mAditionalData['cod_errorx'] = 'Interf Faro - Pad';
          TraficoPad::setMsgError( $mReturn[1] );
          TraficoPad::sendError( $mDataDespac, $mAditionalData, $this -> cParamDis );
              
          $mReturnx[0] = false; 
          $mReturnx[1] = $mMesage[1];        

          return $mReturnx;
        }

        $mReturnx[0] = true; 
        $mReturnx[1] = $mMesage[1];
        TraficoPad::setMsgError( $mMesage[1] ); 
        TraficoPad::sendOk( $mDataDespac, $mAditionalData, array(NULL) );
        
        return $mReturnx;
       
      }
      catch( SoapFault $e )
      {
          $error_ = $e -> getMessage();
          $mAditionalData['nom_proces'] = 'Interf Faro - Pad';
          $mAditionalData['nom_operad'] = 'Faro: InterfPad';
          $mAditionalData['cod_errorx'] = 'Interf Faro - Pad';
          TraficoPad::setMsgError( $error_  );
          TraficoPad::sendError( $mDataDespac, $mAditionalData, $this -> cParamDis );
              
          $mReturnx[0] = false; 
          $mReturnx[1] = $error_;        
        
          return $mReturnx;
      }
      
    }

    function Consulta( $mQuery = NULL, $mRetorna = true)
    {       
      $mError = array();
      $mLink = mysql_connect(self::$cBD, USUARIO, CLAVE); 
      if( !$mLink)
      {
        $mError["ID"] = false;
        $mError["Code"] = mysql_errno($mLink );
        $mError["Error"] = mysql_error($mLink );
        return $mError;
      }
      mysql_select_db(BASE_DATOS);

      $mDataArray = array();
      $mCont = 0;
      $mData = mysql_query($mQuery);

      if( $mRetorna ) 
      {
        while ($mRow = mysql_fetch_assoc($mData)) 
        {
          $mDataArray[$mCont++] = $mRow;         
        }
      }

      mysql_close($mLink);
      return $mDataArray;
    }

  }

?>

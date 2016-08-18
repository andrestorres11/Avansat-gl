<?php
   /************************************************************************************
   * Clase para manejo del webservice GPS                                              *
   * @brief Esta es la clase padre de donde se agregan o remueven placas para rep GPS  *
   * @version 0.1                                                                      *
   * @ultima_modificacion 17 de Marzo de 2011                                          *
   * @author Hugo Malagon.                                                             *
   ************************************************************************************/
  
  class InterfGPS
  {
    var $cConexion  = NULL;
    var $cMsgError  = NULL;
  
    function InterfGPS( $mConexion )
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
    
    function sendError( $sendData, $aditionalData )
    {
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Operacion: ".$aditionalData['nom_proces']." \n";
      $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
      $mMessage .= "Fecha Salida: ".$sendData["fec_salida"]." \n";
      $mMessage .= "Empresa de transporte: ".$sendData["cod_transp"]." \n";
      $mMessage .= "Aplicacion: ".NOM_URL_APLICA." \n";
      $mMessage .= "Despacho: ".$sendData["num_despac"]." \n";
      $mMessage .= "Placa: ".$sendData["num_placax"]." \n";
      $mMessage .= "Operador: ".$aditionalData['nom_operad']." \n";
      $mMessage .= "******** Detalle ******** \n";
      $mMessage .= "Codigo de error: ".$aditionalData['cod_errorx']." \n";
      $mMessage .= "Mensaje de error: ".$this -> getMsgError()." \n";
      //mail( NOT_MAIL, "Web service GPS", $mMessage,'From: soporte.ingenieros@intrared.net' );
    }
    
    function getDataDespac( $num_despac )
    {
      $query = "SELECT a.num_placax, b.cod_opegps, c.nom_operad, 
                       DATE_FORMAT( b.fec_salida, '%Y-%m-%d %h:%i' ) AS fec_salida,
                       c.ind_usaidx, b.idx_gpsxxx, b.usr_gpsxxx, b.clv_gpsxxx
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
                       ".BASE_DATOS.".tab_despac_despac b LEFT JOIN
                       ".BD_STANDA.".tab_genera_opegps c ON
                       b.cod_opegps = c.cod_operad
                 WHERE a.num_despac = ".$num_despac." AND
                       a.num_despac = b.num_despac ";
      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataDespac = $consulta -> ret_arreglo( 'i' );
      return $mDataDespac;
    }
    
    function getDataGPS( $cod_opegps, $cod_transp )
    {
      $query = "SELECT a.nom_usuari, a.clv_usuari
                  FROM ".BASE_DATOS.".tab_interf_gps a
                 WHERE a.cod_operad = '".$cod_opegps."'
                   AND a.cod_transp = '".$cod_transp."'";
      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataGPS = $consulta -> ret_arreglo( 'i' );
      return $mDataGPS;
    }
    
    //Metodo que agrega la placa al listado de vehiculos reportando gps en MySQL 5 mediante WS
    function setPlacaGPS( $numDespac, $aditionalData )
    {
      //Se consultan datos del despacho
      $mDataDespac = $this -> getDataDespac( $numDespac );
      if( $mDataDespac["cod_opegps"] )
      {
        //Se consultan los datos para la conexion al operador de esa transportadora
        //$mDataGPS = $this -> getDataGPS( $mDataDespac["cod_opegps"], NIT_TRANSPOR );
        $sendData["cod_operad"] = $mDataDespac["cod_opegps"];
        //$sendData["usr_gpsxxx"] = $mDataGPS['nom_usuari'];
        //$sendData["clv_gpsxxx"] = base64_encode( $mDataGPS['clv_usuari'] );
        $sendData["usr_gpsxxx"] = $mDataDespac['usr_gpsxxx'];
        $sendData["clv_gpsxxx"] = base64_encode( $mDataDespac['clv_gpsxxx'] );
        $sendData["cod_transp"] = NIT_TRANSPOR;
        $sendData["nom_aplica"] = NOM_URL_APLICA;
        $sendData["num_placax"] = $mDataDespac["num_placax"];
        $sendData["nom_aplica"] = NOM_URL_APLICA;
        $sendData['fec_salida'] = $mDataDespac['fec_salida'];
        $sendData["num_despac"] = $numDespac;
        $sendData['idx_gpsxxx'] = $mDataDespac['idx_gpsxxx'];

        
        $aditionalData["nom_operad"] = $mDataDespac["nom_operad"];
      
        $oSoapClient = new soapclient( URL_INTERF_GPSXXX, true );
        $oSoapClient -> soap_defencoding = 'ISO-8859-1';
        
        $mResult = $oSoapClient -> call( "setPlacaGPS", $sendData );
  
        if ( $oSoapClient -> fault )
        {
          //Notifica Fallos
          $this -> setMsgError( $oSoapClient -> faultcode.':'.$oSoapClient -> faultdetail.':'.$oSoapClient -> faultstring );
          $this -> sendError( $sendData, $aditionalData );
          return FALSE;
        }
        else
        {
          $err = $oSoapClient -> getError();
          if ( $err ) 
          {
            // Notifica errores
            $this -> setMsgError( $err );
            $this -> sendError( $sendData, $aditionalData );
          } 
          else 
          {
            //Procesa el resultado del WS
            $mResult = explode( "; ", $mResult );
            $mCodResp = explode( ":", $mResult[0] );
            $mMsgResp = explode( ":", $mResult[1] );
            $aditionalData['cod_errorx'] = $mCodResp[1];
      
            if( "1000" != $mCodResp[1] )
            {
              //Notifica Errores retornados por el WS
              $this -> setMsgError( $mMsgResp[1] );
              $this -> sendError( $sendData, $aditionalData );
              return FALSE;
            }
            else
            {
              $this -> setMsgError( NULL );
              return TRUE;
            }
          }
        }
      }
    }
    
    //Metodo que remueve la placa al listado de vehiculos reportando gps en MySQL 5 mediante WS
    function removePlacaGPS( $numDespac, $aditionalData )
    {
      //Se consultan datos del despacho
      $mDataDespac = $this -> getDataDespac( $numDespac );
      if( $mDataDespac["cod_opegps"] )
      {
        $sendData["cod_operad"] = $mDataDespac["cod_opegps"];
        $sendData["cod_transp"] = NIT_TRANSPOR;
        $sendData["num_placax"] = $mDataDespac["num_placax"];
        $sendData["num_despac"] = $numDespac;
        
        $aditionalData["nom_operad"] = $mDataDespac["nom_operad"];
      
        $oSoapClient = new soapclient( URL_INTERF_GPSXXX, true );
        $oSoapClient -> soap_defencoding = 'ISO-8859-1';
        
        $mResult = $oSoapClient -> call( "removePlacaGPS", $sendData );
        $sendData["num_despac"] = $aditionalData['num_despac'];
        if ( $oSoapClient -> fault )
        {
          //Notifica Fallos
          $this -> setMsgError( $oSoapClient -> faultcode.':'.$oSoapClient -> faultdetail.':'.$oSoapClient -> faultstring );
          $this -> sendError( $sendData, $aditionalData );
          return FALSE;
        }
        else
        {
          $err = $oSoapClient -> getError();
          if ( $err ) 
          {
            // Notifica errores
            $this -> setMsgError( $err );
            $this -> sendError( $sendData, $aditionalData );
          } 
          else 
          {
            //Procesa el resultado del WS
            $mResult = explode( "; ", $mResult );
            $mCodResp = explode( ":", $mResult[0] );
            $mMsgResp = explode( ":", $mResult[1] );
            $aditionalData['cod_errorx'] = $mCodResp[1];
      
            if( "1000" != $mCodResp[1] )
            {
              //Notifica Errores retornados por el WS
              $this -> setMsgError( $mMsgResp[1] );
              $this -> sendError( $sendData, $aditionalData );
              return FALSE;
            }
            else
            {
              return TRUE;
            }
          }
        }
      }
    }
  }
?>
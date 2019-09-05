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

    /*! \fn: getDataDespacIntegradorGPS
    *  \brief: funcion para consultar los datos que pide el integrador para crear el itinerario
    *  \author: Ing. Nelson Liberato
    *  \date: 2017-04-11
    *  \param:   $numDespac Numero del despacho del cliente
    *  \param:   $aditionalData array con datos adicionales que complementan la informacion del despacho
    *  \return:  bool
    */
    function getDataDespacIntegradorGPS( $num_despac )
    {

      $query = "  SELECT a.num_placax, 
                         IF( d.num_despac IS NOT NULL, '860068121', a.cod_transp ) AS cod_transp,
                         IF( d.num_despac IS NOT NULL, e.nom_operad, c.nom_operad ) AS nom_operad, 
                         DATE_FORMAT( b.fec_salida, '%Y-%m-%d %h:%i' ) AS fec_salida,
                         IF( d.num_despac IS NOT NULL, e.ind_usaidx, c.ind_usaidx ) AS ind_usaidx, 
                         IF( d.num_despac IS NOT NULL, d.gps_operad, b.gps_operad ) AS cod_opegps,
                         IF( d.num_despac IS NOT NULL, d.gps_usuari, b.gps_usuari ) AS gps_usuari,
                         IF( d.num_despac IS NOT NULL, d.gps_paswor, b.gps_paswor ) AS gps_paswor,
                         IF( d.num_despac IS NOT NULL, IF(d.gps_idxxxx = 'n/a' , NULL, d.gps_idxxxx), b.gps_otroxx ) AS gps_otroxx,
                         IF( d.num_despac IS NOT NULL, d.cod_manifi, b.cod_manifi ) AS cod_manifi,
                         IF( d.num_despac IS NOT NULL, 'corona', 'normal' ) AS ind_tiptra, /* Tipo de despacho, corona o normal*/
                         f.nom_tercer AS nom_conduc,
                         CONCAT(f.nom_apell1, ' ', f.nom_apell2) AS nom_apelli,
                         a.cod_conduc,
                         f.num_telmov AS num_telmov,
                         UNIX_TIMESTAMP(a.fec_llegpl) AS DateFinal,
                         UNIX_TIMESTAMP(b.fec_salida) AS DateInitial,
                         b.obs_despac AS Description,
                         a.cod_rutasx,
                         '' AS Origin,
                         '' AS Destiny,
                         '' AS LstPoint, /*Son los destinatarios de de ese despacho*/
                         b.cod_tipdes AS TypeDispacth,
                         IF( d.tip_transp IS NULL OR d.tip_transp = '', '1', d.tip_transp ) AS Owner

                    FROM ".BASE_DATOS.".tab_despac_vehige a 
              INNER JOIN ".BASE_DATOS.".tab_despac_despac b ON a.num_despac = b.num_despac 
               LEFT JOIN ".BD_STANDA. ".tab_genera_opegps c ON b.gps_operad = c.cod_operad
               LEFT JOIN ".BASE_DATOS.".tab_despac_corona d ON d.num_dessat = a.num_despac AND d.cod_manifi = b.cod_manifi
               LEFT JOIN ".BD_STANDA. ".tab_genera_opegps e ON d.gps_operad = e.cod_operad
               LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer f ON a.cod_conduc = f.cod_tercer
                   WHERE a.num_despac = ".$num_despac." ";
                   
      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataDespac = $consulta -> ret_objeto( 'i' );
      $mDataDespac = (array)$mDataDespac;
 

      $mDataDespac['Origin']   = $this -> getDataCiudadDespacho($num_despac, 'origen'  , $mDataDespac['ind_tiptra'] );
      $mDataDespac['Destiny']  = $this -> getDataCiudadDespacho($num_despac, 'destino' , $mDataDespac['ind_tiptra'] );
      $mDataDespac['LstPoint'] = $this -> getDataDestinatariosDespacho($num_despac, $mDataDespac['ind_tiptra'] );
      //$mDataDespac['Transponder'] = $this -> getDataGps($num_despac, $mDataDespac['ind_tiptra'] );
      // Consulta datos de origen y destino
      return $mDataDespac;
    }


    /*! \fn: getDataCiudadDespacho
    *  \brief: funcion para consultar datos de ciudad de origen y destino
    *  \author: Ing. Nelson Liberato
    *  \date: 2017-04-11
    *  \param:   $numDespac Numero del despacho del cliente
    *  \param:   $aditionalData array con datos adicionales que complementan la informacion del despacho
    *  \return:  bool
    */
    function getDataCiudadDespacho( $mNumDespac = NULL, $mPunto = 'origen', $mIndTiptra = 'normal')
    {
     

      switch ($mIndTiptra) {
        case 'normal':
                       $query = "SELECT 
                                      '' AS dir_ciuori,
                                      b.nom_ciudad AS nom_ciuori, 
                                      b.cod_ciudad AS cod_origen,
                                      d.abr_remite AS nom_origen,
                                      b.cod_latitu AS lat_origen,
                                      b.cod_longit AS lon_origen, 
                                      '' AS Geometry_origen,
                                      
                                      f.dir_destin AS dir_ciudes,
                                      c.nom_ciudad AS nom_ciudes,
                                      d.num_docume as cod_destin, 
                                      d.abr_destin AS nom_destin,                                      
                                      c.cod_latitu AS lat_destin, 
                                      c.cod_longit AS lon_destin,
                                      '' AS Geometry_destin
                                FROM 
                                      ".BASE_DATOS.".tab_despac_despac a
                          INNER JOIN  ".BASE_DATOS.".tab_despac_vehige e ON a.num_despac = e.num_despac 
                          INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b ON a.cod_ciuori = b.cod_ciudad 
                          INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad c ON a.cod_ciudes = c.cod_ciudad 
                          INNER JOIN  ".BASE_DATOS.".tab_genera_remdes d ON e.cod_transp = d.cod_transp  
                          LEFT  JOIN  ".BASE_DATOS.".tab_despac_destin f ON d.num_remdes = f.num_destin AND a.num_despac = f.num_despac  
                               WHERE 
                                      a.num_despac = '".$mNumDespac."' LIMIT 1  ";  
        break;
        
        case 'corona':
                     $query = "SELECT 
                                      '' AS dir_ciuori,
                                      b.nom_ciudad AS nom_ciuori, 
                                      d.nom_sitcar AS cod_origen,
                                      d.des_sitcar AS nom_origen,
                                      b.cod_latitu AS lat_origen, 
                                      b.cod_longit AS lon_origen,
                                      '' AS Geometry_origen,
                                      
                                      '' AS dir_ciudes,
                                      c.nom_ciudad AS nom_ciudes,
                                      c.cod_ciudad AS cod_destin, 
                                      c.nom_ciudad AS nom_destin,
                                      c.cod_latitu AS lat_destin, 
                                      c.cod_longit AS lon_destin,
                                      '' AS Geometry_destin
                            FROM 
                                  ".BASE_DATOS.".tab_despac_despac a
                      INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b ON a.cod_ciuori = b.cod_ciudad 
                      INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad c ON a.cod_ciudes = c.cod_ciudad 
                      INNER JOIN  ".BASE_DATOS.".tab_despac_corona d ON a.num_despac = d.num_dessat 
                           WHERE a.num_despac = '".$mNumDespac."'  ";
          break;
      }


      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataGPS = $consulta -> ret_arreglo( 'i' );
      switch ($mPunto) 
      {
        case 'origen':
          $mDataPoint = [
                          'Latitude' => $mDataGPS['lat_origen'],
                          'Longitude' => $mDataGPS['lon_origen'],
                          'LocationName' => $mDataGPS['nom_ciuori'],
                          'Type' => '2',
                          'Radio' => '50',
                          'Address' => $mDataGPS['dir_ciudes'],
                          'City' => $mDataGPS['nom_ciuori'],
                          'CustomCode' => $mDataGPS['cod_origen'],
                          'GeometryText' => $mDataGPS['Geometry_origen']
                        ];
          break;        

          case 'destino':
          $mDataPoint = [
                          'Latitude'     => $mDataGPS['lat_destin'],
                          'Longitude'    => $mDataGPS['lon_destin'],
                          'LocationName' => $mDataGPS['nom_destin'],
                          'Type'         => '2',
                          'Radio'        => '50',
                          'Address'      => $mDataGPS['dir_ciudes'],
                          'City'         => $mDataGPS['nom_ciudes'],
                          'CustomCode'   => $mDataGPS['cod_destin'],
                          'GeometryText'   => $mDataGPS['Geometry_destin']
                        ];
          break;
      }
      return $mDataPoint;
    }
    
    function getDataDestinatariosDespacho($mNumDespac = NULL, $mIndTiptra = 'normal')
    {
      
 
      switch ($mIndTiptra) 
      {
        case 'corona':
         $query = "SELECT 
                          b.cod_latitu AS Latitude,
                          b.cod_longit AS Longitude,
                          a.nom_destin AS LocationName, 
                          '2' AS Type, 
                          '50' AS Radio, 
                          a.dir_destin AS Address,
                          b.nom_ciudad AS City,
                          a.num_destin AS CustomCode,
                          '' AS GeometryText
                  FROM 
                        ".BASE_DATOS.".tab_despac_destin a
            INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b ON a.cod_ciudad = b.cod_ciudad  
                 WHERE a.num_despac = '".$mNumDespac."' GROUP BY a.nom_destin, a.num_destin ";
          break;        

          case 'normal':
          $query = "SELECT 
                           b.cod_latitu AS Latitude, 
                           b.cod_longit AS Longitude
                           c.nom_ciudad AS LocationName,
                          '2' AS Type, 
                          '50' AS Radio, 
                           '' AS Address,
                           b.nom_ciudad AS City,
                           a.cod_ciuori AS CustomCode,
                           '' AS GeometryText
                           
                  FROM 
                        ".BASE_DATOS.".tab_despac_despac a
            INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b ON a.cod_ciuori = b.cod_ciudad 
            INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad c ON a.cod_ciudes = c.cod_ciudad 
                 WHERE a.num_despac = '".$mNumDespac."'  ";
          break;
      }


      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataPoints = $consulta -> ret_matrix( 'a' );
      // foreach ($mDataPoints AS $mCon => $mList) 
      // {
      //     $mDataPoints[$mCon]['_GeoPoint'] = json_decode($mList['_GeoPoint'], true);
      // }

      return $mDataPoints;      
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



    /*! \fn: setPlacaparaevento
    *  \brief: funcion para registar un despacho a la central para eventos GPS, se tiene en cuenta si tiene parametro de novedades GPS tab_genera_parame.cod_parame = ind_evegps, ind_estado=1
    *  \author: Ing. Nelson Liberato
    *  \date: 2017-04-11
    *  \param:   $numDespac Numero del despacho del cliente
    *  \param:   $aditionalData array con datos adicionales que complementan la informacion del despacho
    *  \return:  bool
    */
    function setPlacaparaevento($numDespac, $aditionalData )
    {
      try 
      {

        $mDataDespac = $this -> getDataDespac( $numDespac );

        if( $mDataDespac["cod_opegps"] )
        {

          $this -> setMsgError( false );
          $sendData = array(
              "cod_tokenx" => "0zV;Q;%5=zL6TG",
              "nit_transp" => $mDataDespac['cod_transp'],
              "ifx_server" => 'AVANSATGL', 
              "nom_aplica" => 'satt_faro', 
              "usr_gpsxxx" => "InterfGPSEvento", 
              "pwd_gpsxxx" => "0zV;Q;%5=zL6TG", 
              "num_placax" => $mDataDespac["num_placax"],    
              "num_manifi" => $mDataDespac['cod_manifi'], 
              "num_despac" => $numDespac, 
              "ind_segfar" => $mDataDespac['ind_segfar'],
              "cod_opegps" => '900138726', // Se quema NIT de Widetech para el tema de integrador //$mDataDespac['cod_opegps'],
              "nit_opegps" => $mDataDespac['cod_opegps'], // Nit del operador GPS del despacho
              "usr_opegps" => $mDataDespac['gps_usuari'],
              "clv_opegps" => $mDataDespac['gps_paswor'],
              "idx_opegps" => $mDataDespac['gps_otroxx'],
              "cod_rutasx" => $mDataDespac['cod_rutasx'],
              "cod_agenci" => $mDataDespac['cod_agenci'],
              "nom_conduc" => $mDataDespac['nom_conduc'],
              "cel_conduc" => (int)$mDataDespac['num_telmov'],
              "can_pesoxx" => $mDataDespac['sum_remesa'],
              "reg_descar" => $mDataDespac['nom_ciudad'],
              "ind_transa" => $aditionalData["ind_transa"]
            );


  
          $cHeader = array("Token: TkOET_EAL","Auth: $2dIMJMZQcHLY");
          
          # Recorre las variables para concatenarlas en un solo string como si fuera un GET para enviarla por cUrl------------------------
          $mParamsString = "inputAvansat=".json_encode($sendData);
          
           
          # Inicio de cURL para la API -----------------------------------------------------------------------------
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, URL_INTERF_GPSEVE ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
          curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
          curl_setopt($ch, CURLOPT_VERBOSE, 1);
          curl_setopt($ch, CURLOPT_POST, true );
          curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          $raw_data = curl_exec($ch);
          $error = curl_error($ch);
          $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

           
          echo "<pre>setPlacaparaevento: "; print_r($raw_data); echo "</pre>";
          # Asigna el mensaje del response y lo retorna ------------------------------------------------------------       
          $cReturn = json_decode($raw_data);
          if( $cReturn -> code_resp != "1000" ) {
            $this -> setMsgError( $cReturn -> msg_resp );
            $this -> sendError( $sendData, $aditionalData );
            return FALSE;
          }           
          else{
            return true;
          }
                
           
        }
        return true;

      } 
      catch (Exception $e) 
      {
        
      }
    }



    /*! \fn: setPlacaIntegradorGPS
    *  \brief: funcion para enviar los datos a central para registralo al integrador GPS
    *  \author: Ing. Nelson Liberato
    *  \date: 2017-04-11
    *  \param:   $numDespac Numero del despacho del cliente
    *  \param:   $aditionalData array con datos adicionales que complementan la informacion del despacho
    *  \return:  bool
    */
    function setPlacaIntegradorGPS($numDespac, $aditionalData )
    {
      try 
      {

        $mDataDespac = $this -> getDataDespacIntegradorGPS( $numDespac );

        if( $mDataDespac["cod_opegps"] )
        {

          if( $mDataDespac['nom_apelli'] == '')
          {
            $mApell = explode(" ", $mDataDespac['nom_conduc']);
            $mDataDespac['nom_apelli'] = $mApell[0]." ".$mApell[1];
          }
          $this -> setMsgError( false );
          $sendData = array(
              "cod_tokenx" => "0zV;Q;%5=zL6TG",
              "nit_transp" => $mDataDespac['cod_transp'],
              "ifx_server" => 'AVANSATGL', 
              "nom_aplica" => 'satt_faro', 
              "usr_gpsxxx" => "InterfGpsIntegr", 
              "pwd_gpsxxx" => "0zV;Q;%5=zL6TG", 
              "num_placax" => $mDataDespac["num_placax"],    
              "num_manifi" => $mDataDespac['cod_manifi'], 
              "num_despac" => $numDespac, 
              "ind_segfar" => $mDataDespac['ind_segfar'],
              "nit_opegps" => '9001387267', // Nit del operador GPS del despacho
              "cod_opegps" => $mDataDespac['cod_opegps'], // Se quema NIT de Widetech para el tema de integrador //$mDataDespac['cod_opegps'],
              "usr_opegps" => $mDataDespac['gps_usuari'],
              "clv_opegps" => $mDataDespac['gps_paswor'],
              "idx_opegps" => $mDataDespac['gps_otroxx'],
              "cod_rutasx" => $mDataDespac['cod_rutasx'],
              "cod_agenci" => $mDataDespac['cod_agenci'],
              "cod_conduc" => $mDataDespac['cod_conduc'],
              "nom_conduc" => $mDataDespac['nom_conduc'],
              "ape_conduc" => $mDataDespac['nom_apelli'],
              "cel_conduc" => (int)$mDataDespac['num_telmov'],
              "can_pesoxx" => $mDataDespac['sum_remesa'],
              "reg_descar" => $mDataDespac['nom_ciudad'],
              "ind_transa" => $aditionalData["ind_transa"],


              "DateFinal" => '/Date('.$mDataDespac["DateFinal"].')/',
              "DateInitial" => '/Date('.$mDataDespac["DateInitial"].')/',
              "Description" => $mDataDespac["Description"],
              "Origin" => $mDataDespac["Origin"],
              "Destiny" => $mDataDespac["Destiny"],
              "LstPoint" => $mDataDespac["LstPoint"],
              "TypeDispacth" => $mDataDespac["TypeDispacth"],
              "Owner" => $mDataDespac["Owner"],
            );


          // echo "<pre>"; print_r($sendData); echo "</pre>";
          // die();
  
          $cHeader = array("Token: TkOET_EAL","Auth: $2dIMJMZQcHLY",
                           "Authorization: e14804819d57fc7497bb747204ce337b", 
                           "usuario: *WidetechInt3grador*", 
                           "clave: lxdG-+gJX:oYju+b5n"
                          );
          
          # Recorre las variables para concatenarlas en un solo string como si fuera un GET para enviarla por cUrl------------------------
          //$mParamsString = "inputAvansat=".json_encode($sendData);
          $mParamsString = json_encode($sendData);
           
          # Inicio de cURL para la API -----------------------------------------------------------------------------
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, URL_INTERF_GPSEVE."/setItinerary  " ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
          curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
          curl_setopt($ch, CURLOPT_VERBOSE, 1);
          curl_setopt($ch, CURLOPT_POST, true );
          curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $raw_data = curl_exec($ch);
          $error = curl_error($ch);
          $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

           
          echo "<pre>mParamsString: "; print_r(URL_INTERF_GPSEVE."/setItinerary  " ); echo "</pre>";
          echo "<pre>mParamsString: "; print_r($mParamsString); echo "</pre>";
          echo "<pre>setPlacaparaevento: "; print_r($raw_data); echo "</pre>";
          # Asigna el mensaje del response y lo retorna ------------------------------------------------------------       
          $cReturn = json_decode($raw_data);
          if( $cReturn -> code_resp != "1000" ) {
            $this -> setMsgError( $cReturn -> msg_resp );
            $this -> sendError( $sendData, $aditionalData );
            return FALSE;
          }           
          else{
            return true;
          }
                
           
        }
        return true;

      } 
      catch (Exception $e) 
      {
        
      }
    }


  }
?>
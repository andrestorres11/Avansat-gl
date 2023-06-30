<?php
   /************************************************************************************
   * Clase para manejo del webservice GPS                                              *
   * @brief Esta es la clase padre de donde se agregan o remueven placas para rep GPS  *
   * @version 0.1                                                                      *
   * @ultima_modificacion 17 de Marzo de 2011                                          *
   * @author Hugo Malagon.                                                             *
   ************************************************************************************/
  // echo date("Y-m-d H:i:s");
  class InterfGPS
  {
    var $cConexion  = NULL;
    var $cMsgError  = NULL;
    var $cTokenCliente = NULL;
  
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

      // https://currentmillis.com/
      $query = "  SELECT a.num_placax, 
                         a.cod_transp,
                         IF( d.num_despac IS NOT NULL, e.nom_operad, c.nom_operad ) AS nom_operad, 
                         DATE_FORMAT( b.fec_salida, '%Y-%m-%d %h:%i' ) AS fec_salida,
                         IF( d.num_despac IS NOT NULL, e.ind_usaidx, c.ind_usaidx ) AS ind_usaidx, 
                         IF( d.num_despac IS NOT NULL, IF(e.nit_operad IS NOT NULL, e.nit_operad, c.nit_operad)   /*d.gps_operad*/, b.gps_operad ) AS cod_opegps,
                         IF( d.num_despac IS NOT NULL, IF(d.gps_usuari IS NOT NULL, d.gps_usuari, b.gps_usuari), b.gps_usuari ) AS gps_usuari,
                         IF( d.num_despac IS NOT NULL, IF(d.gps_paswor IS NOT NULL, d.gps_paswor, b.gps_paswor), b.gps_paswor ) AS gps_paswor,
                         IF( d.num_despac IS NOT NULL, IF(d.gps_idxxxx = 'n/a' , NULL, d.gps_idxxxx), b.gps_otroxx ) AS gps_otroxx,
                         IF( d.num_despac IS NOT NULL, d.cod_manifi, b.cod_manifi ) AS cod_manifi,
                         IF( d.num_despac IS NOT NULL, 'corona', 'normal' ) AS ind_tiptra, /* Tipo de despacho, corona o normal*/
                         f.nom_tercer AS nom_conduc,
                         CONCAT(f.nom_apell1, ' ', f.nom_apell2) AS nom_apelli,
                         a.cod_conduc,
                         f.num_telmov AS num_telmov,
                         -- Se realiza parche ya que la fecha incial puede ser del d�a anterior a la fecha actual y widetech no permite crear itinerarios con despachos viejos
                         -- Si fecha de salida del despacho es menor de un dia a atras a la actual, se debe cambiar y enviar la fecha actual, igualmente si cambiamos la inicial toca correr tambien la final

                         -- Reajuste a DateInitial
                         CASE
                              WHEN ( DATE(b.fec_salida) < DATE( NOW() ) ) THEN (UNIX_TIMESTAMP(NOW()) * 1000)
                         ELSE (UNIX_TIMESTAMP(b.fec_salida) * 1000) END AS DateInitial, 

                         -- Reajuste a DateFinal
                         CASE
                              WHEN ( DATE(b.fec_salida) < DATE( NOW() ) ) THEN (UNIX_TIMESTAMP( DATE_ADD(NOW(), INTERVAL ( TIMESTAMPDIFF( MINUTE, b.fec_salida, a.fec_llegpl) ) MINUTE ) ) * 1000)
                         ELSE (UNIX_TIMESTAMP(a.fec_llegpl) * 1000) END AS DateFinal, 

                         b.obs_despac AS Description,
                         a.cod_rutasx,
                         '' AS Origin,
                         '' AS Destiny,
                         '' AS LstPoint, /*Son los destinatarios de de ese despacho*/
                         b.cod_tipdes AS TypeDispacth,
                         IF( d.tip_transp IS NULL OR d.tip_transp = '', '1', d.tip_transp ) AS Owner,
                         d.num_despac AS num_viajex,
                         IF(d.num_pedido IS NULL OR d.num_pedido = '' , d.num_solici, d.num_pedido ) AS OrdenCargue,
                         IF( a.cod_itiner IS NULL OR a.cod_itiner = '' , a.cod_integr , a.cod_itiner ) AS ItineraryID

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
                                      IFNULL(e.dir_remdes, b.nom_ciudad ) AS dir_ciuori,
                                      b.nom_ciudad AS nom_ciuori, 
                                      IFNULL(e.cod_remdes, b.cod_ciudad ) AS cod_origen,
                                      IFNULL(e.nom_remdes, b.nom_ciudad )  AS nom_origen,
                                      IFNULL(e.cod_latitu, b.cod_latitu ) AS lat_origen,
                                      IFNULL(e.cod_longit, b.cod_longit ) AS lon_origen,
                                      '' AS Geometry_origen,
                                      
                                      c.nom_ciudad AS dir_ciudes,
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
                       LEFT JOIN  ".BASE_DATOS.".tab_genera_remdes e ON d.nom_sitcar = e.cod_remdes 
                           WHERE a.num_despac = '".$mNumDespac."'";
          break;
      }


      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataGPS = $consulta -> ret_arreglo( 'i' );
      switch ($mPunto) 
      {
        case 'origen':
                  // 2019-06-21 validaci�n de eventos ya reportados en cargue (Origin)
          $mStatucaCargue = "  SELECT
                                      CONCAT( '{' , GROUP_CONCAT(a.EventStatus ), '}' ) AS EventStatus
                                 FROM (
                                          SELECT  

                                  @fec_aprlle := IF(a.fec_aprlle IS NOT NULL, CONCAT('\"TXA\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_aprlle) * 1000), ')/\"' , ','), ''),
                                  @fec_plalle := IF(b.fec_plalle IS NOT NULL, CONCAT('\"TXL\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(b.fec_plalle) * 1000), ')/\"' , ','), ''),
                                  @fec_ingcar := IF(a.fec_ingcar IS NOT NULL, CONCAT('\"TXE\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_ingcar) * 1000), ')/\"' , ','), ''),
                                  @fec_salcar := IF(a.fec_salcar IS NOT NULL, CONCAT('\"TXS\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_salcar) * 1000), ')/\"' , ','), ''),
                                  @EventStatus := CONCAT(@fec_aprlle, @fec_plalle, @fec_ingcar, @fec_salcar), 
                                  SUBSTR(@EventStatus,1, LENGTH(@EventStatus) -1) AS EventStatus
                                            FROM  ".BASE_DATOS.".tab_despac_despac a
                                      INNER JOIN  ".BASE_DATOS.".tab_despac_sisext b 
                                            ON  a.num_despac = b.num_despac
                                           WHERE  a.num_despac = '".$mNumDespac."' 
                                        ORDER BY  1
                                      ) a ";

          $consulta = new Consulta( $mStatucaCargue, $this -> cConexion );
          $mCargue = $consulta -> ret_matrix( 'a' );
          $mCargue = $mCargue[0]['EventStatus'] != '' ? json_decode($mCargue[0]['EventStatus'], true) : NULL;


          $mDataPoint = [
                          'Latitude' => $mDataGPS['lat_origen'],
                          'Longitude' => $mDataGPS['lon_origen'],
                          'LocationName' => utf8_encode($mDataGPS['nom_origen']),
                          'Type' => '2',
                          'Radio' => '2500',
                          'Address' => utf8_encode($mDataGPS['dir_ciuori']),
                          'City' => $mDataGPS['nom_ciuori'],
                          'CustomCode' => $mDataGPS['cod_origen'],
                          'GeometryText' => $mDataGPS['Geometry_origen'],
                          'EventStatus'   => $mCargue
                        ];
          break;        

          case 'destino':
          $mDataPoint = [
                          'Latitude'     => $mDataGPS['lat_destin'],
                          'Longitude'    => $mDataGPS['lon_destin'],
                          'LocationName' => utf8_encode($mDataGPS['nom_destin']),
                          'Type'         => '2',
                          'Radio'        => '50',
                          'Address'      => utf8_encode($mDataGPS['dir_ciudes']),
                          'City'         => $mDataGPS['nom_ciudes'],
                          'CustomCode'   => $mDataGPS['cod_destin'],
                          'GeometryText' => $mDataGPS['Geometry_destin'],
                          'EventStatus'  => NULL
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
          $query = "
                SELECT 
                    @consec:= (@consec + 1) AS Sequence ,
                    IF(d.cod_latitu IS NULL, c.cod_latitu, d.cod_latitu) AS Latitude,
                    IF(d.cod_longit IS NULL, c.cod_longit, d.cod_longit) AS Longitude,
                    c.nom_remdes AS LocationName, 
                    '2' AS Type, 
                    '50' AS Radio, 
                    IF(d.dir_remdes IS NULL, c.dir_remdes, d.dir_remdes) AS Address,
                    b.nom_ciudad AS City,
                    a.cod_remdes AS CustomCode,
                    '' AS GeometryText,
                    CAST( CONCAT( a.fec_citdes, ' ', a.hor_citdes ) AS datetime ) AS fec_citdes,
                    IF(e.EventStatus = '', NULL, CONCAT( '{' , GROUP_CONCAT(e.EventStatus ), '}' )) AS EventStatus
            FROM  ".BASE_DATOS.".tab_despac_destin a
      INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad b 
          ON  a.cod_ciudad = b.cod_ciudad  
      INNER JOIN  ".BASE_DATOS.".tab_genera_remdes c 
          ON  a.cod_remdes = c.cod_remdes  
       LEFT JOIN  ".BASE_DATOS.".tab_genera_remtmp d 
          ON  a.cod_remdes = d.cod_rempad 
            AND a.num_despac = d.num_despac  
       LEFT JOIN  (
              SELECT  a.num_despac, 
                    a.cod_remdes,
                        @fec_aprlle := IF(a.fec_aprdes IS NOT NULL, CONCAT('\"TXA\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_aprdes) * 1000), ')/\"' , ','), ''),
                        @fec_plalle := IF(a.fec_llecli IS NOT NULL, CONCAT('\"TXL\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_llecli) * 1000), ')/\"' , ','), ''),
                        @fec_ingcar := IF(a.fec_ingdes IS NOT NULL, CONCAT('\"TXE\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_ingdes) * 1000), ')/\"' , ','), ''),
                        @fec_salcar := IF(a.fec_saldes IS NOT NULL, CONCAT('\"TXS\"', ':', '\"/Date(' , (UNIX_TIMESTAMP(a.fec_saldes) * 1000), ')/\"' , ','), ''),
                        @EventStatus := CONCAT(@fec_aprlle, @fec_plalle, @fec_ingcar, @fec_salcar), 
                        SUBSTR(@EventStatus,1, LENGTH(@EventStatus) -1) AS EventStatus
                      FROM  ".BASE_DATOS.".tab_despac_destin a
                     WHERE  a.num_despac = '".$mNumDespac."'
              ) e 
          ON  a.num_despac = e.num_despac AND 
            a.cod_remdes = e.cod_remdes
           WHERE  a.num_despac = '".$mNumDespac."' 
           GROUP  BY a.cod_remdes ORDER BY 10 ASC ";
          break;          

          case 'normal':
          $query = " 
                    SELECT 
                          @consec:= (@consec + 1) AS Sequence ,
                           b.cod_latitu AS Latitude, 
                           b.cod_longit AS Longitude,
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
                 WHERE a.num_despac = '".$mNumDespac."' 
                   ";
          break;
      }

      $queryConsec = "SET @consec:= 0;"; 
      new Consulta( $queryConsec, $this -> cConexion );

      $consulta = new Consulta( $query, $this -> cConexion );
      $mDataPoints = $consulta -> ret_matrix( 'a' );
      
      foreach ($mDataPoints AS $mCon => $mList) 
      {
          //$mDataPoints[$mCon]['_GeoPoint'] = json_decode($mList['_GeoPoint'], true);
          $mDataPoints[$mCon]['LocationName'] = utf8_encode($mList['LocationName']);
          $mDataPoints[$mCon]['Address']      = utf8_encode($mList['Address']);
          $mDataPoints[$mCon]['City']         = utf8_encode($mList['City']);
          $mDataPoints[$mCon]['EventStatus']  = $mList['EventStatus'] != '' ? json_decode($mList['EventStatus'], true) : NULL;
      }

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


          // echo "<pre>"; print_r( $sendData ); echo "</pre>";
          
          # Recorre las variables para concatenarlas en un solo string como si fuera un GET para enviarla por cUrl------------------------
          // $mParamsString = "inputAvansat=".json_encode($sendData, JSON_UNESCAPED_UNICODE);
          $mParamsString = json_encode($sendData);
          
           
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
        $parameRutfar = $this->verifyParameIntgps(53,$mDataDespac['cod_transp']);

        if( $mDataDespac["cod_opegps"] )
        {

          if( $mDataDespac['nom_apelli'] == '')
          {
            $mApell = explode(" ", $mDataDespac['nom_conduc']);
            $mDataDespac['nom_apelli'] = $mApell[0]." ".$mApell[1];
          }
          $this -> setMsgError( false );

          // Se parcha los malditos caracteres especiales ya que no se dejan hacer json_encode


        // parametros de itinerario FULL O BASICO 1 O BASICO 2
 
        
        $mConfig = json_decode( $parameRutfar[0]["ind_deseta"], true ); 
        if(!$mConfig){
          return ['code_resp' => 2005, 'msg_resp' =>'NO HAY PARAMETRIZACION DE TIPO INTEGRADOR GPS EN LA INTERFAZ DEL CLIENTE' ];
        }

        /*
         ________________________________________________________________________________________________________________________________                         
        |TIPO     |  ETA    | TIEMPOS_LOGISTICOS  | PASO POR PUNTO INTERES (ORIGEN, DESTINO)  | DESTINATARIOS |    TIEMPO_REPORTE       |
        |________________________________________________________________________________________________________________________________                         
        |FULL     |  X      |   X                 |                 X                         |       X       |                         |   v1
        |________________________________________________________________________________________________________________________________                         
        |BASICO_1 |         |   X                 |                 X                         |       X       |          X              |   v1
        |________________________________________________________________________________________________________________________________                         
        |BASICO_2 |         |                     |                 X                         |               |          X              |   v1
        |_______________________________________________________________________________________________________________________________                          
        |HUB      |         |                     |                                           |               |          X              |   v2 class InterfHubIntegradorGPS  
        |_______________________________________________________________________________________________________________________________  
        */
        

          switch ($mConfig['TIPO']) 
          {
            case 'FULL':
                $mEtaxxx = '1';
                $mLstPoi = sizeof($mDataDespac["LstPoint"]) > 0 ? $mDataDespac["LstPoint"] : null;
                $mTieRep = 0;
                $mType = '1';
            break;          
            case 'BASICO_1':
                $mEtaxxx = '0';
                $mLstPoi = sizeof($mDataDespac["LstPoint"]) > 0 ? $mDataDespac["LstPoint"] : null;
                $mTieRep = $parameRutfar[0]["tie_report"];
                $mType = '2';
            break;          
            case 'BASICO_2':
                $mEtaxxx = '0';
                $mLstPoi = null;
                $mTieRep = $parameRutfar[0]["tie_report"];
                $mType = '3';
            break;
            case 'HUB':
              return ['code_resp' => 2005, 'msg_resp' =>'EL TIPO DE CONFIGURACION HUB DEBE SER URL v2 e ind_operad = 3, POR FAVOR VALIDAR CON SU PROVEEDOR ESTE MENSAJE.' ];
            break;
            
            default:
               return ['code_resp' => 2005, 'msg_resp' =>'NO EXISTE LA CONFIGURACION TIPO '.$mConfig['TIPO'] ];
            break;
          }



          $sendData = array(
              "cod_tokenx" => "0zV;Q;%5=zL6TG",
              "nit_transp" => $parameRutfar[0]['cod_transp'],  // Nit del cliente, osea el de GL
              "nit_gligps" => '830076669',
              "ifx_server" => 'AVANSATGL', 
              "nom_aplica" => BASE_DATOS, 
              "usr_gpsxxx" => "InterfGpsIntegr", 
              "pwd_gpsxxx" => "0zV;Q;%5=zL6TG", 
              "num_placax" => $mDataDespac["num_placax"],    
              "nit_despac" => $mDataDespac['cod_transp'], // nit de la mepresa que lleva el despacho
              "num_manifi" => $mDataDespac['cod_manifi'], 
              "num_ordenx" => $mDataDespac['cod_manifi'], //num_ordenx
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
              "nom_conduc" => utf8_encode($mDataDespac['nom_conduc']),
              "ape_conduc" => utf8_encode($mDataDespac['nom_apelli']),
              "cel_conduc" => (int)$mDataDespac['num_telmov'],
              "can_pesoxx" => $mDataDespac['sum_remesa'],
              "reg_descar" => $mDataDespac['nom_ciudad'],
              "ind_transa" => $aditionalData["ind_transa"],
              "num_viajex" => $mDataDespac["num_viajex"],
              "ItineraryID" => $mDataDespac["ItineraryID"],

              "Order"       => $mDataDespac['cod_manifi'], //$mDataDespac["OrdenCargue"],
              "DateInitial" => '/Date('.$mDataDespac["DateInitial"].')/',
              "DateFinal"   => '/Date('.$mDataDespac["DateFinal"].')/',
              "Description" => utf8_encode($mDataDespac["Description"]),
              "Origin"      => $mDataDespac["Origin"],
              "Destiny"     => $mDataDespac["Destiny"],
              "LstPoint"    => $mLstPoi, //sizeof($mDataDespac["LstPoint"]) > 0 ? $mDataDespac["LstPoint"] : null,
              "TypeDispacth" => $mDataDespac["TypeDispacth"],
              "Owner"       => $mDataDespac["Owner"],
              "TokenCliente" => ($this -> cTokenCliente == NULL && $this -> cTokenCliente == '' ? $parameRutfar[0]["cod_tokenx"] : $this -> cTokenCliente ),
              "SendETA" => $mEtaxxx ,
              "timeReport" => $mTieRep, 
              "tipoItinerario" => $mType
            );

          $cHeader = array("Token: TkOET_EAL","Auth: $2dIMJMZQcHLY",
                           "Authorization: e14804819d57fc7497bb747204ce337b", 
                           "usuario: *WidetechInt3grador*", 
                           "clave: lxdG-+gJX:oYju+b5n"
                          );
          mail('cristian.torres@grupooet.com','SendManual',URL_INTERF_GPSEVE."/setItinerary");                
          # Recorre las variables para concatenarlas en un solo string como si fuera un GET para enviarla por cUrl------------------------
          //$mParamsString = "inputAvansat=".json_encode($sendData);
          $mParamsString = json_encode($sendData);
          # Inicio de cURL para la API -----------------------------------------------------------------------------
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, URL_INTERF_GPSEVE."/setItinerary" ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
          curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
          curl_setopt($ch, CURLOPT_VERBOSE, 1);
          curl_setopt($ch, CURLOPT_POST, true );
          curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $raw_data = curl_exec($ch);
          $error = curl_error($ch);
          $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);
          //echo "<pre>mParamsString: "; print_r(URL_INTERF_GPSEVE."/setItinerary  " ); echo "</pre>";
         // echo "<pre>mParamsString: "; print_r($mParamsString); echo "</pre>";
          //echo "<pre>setPlacaparaevento: "; print_r($raw_data); echo "</pre>";
          # Asigna el mensaje del response y lo retorna ------------------------------------------------------------       
          return json_decode($raw_data, true);
          // if( $cReturn -> code_resp != "1000" ) {
          //   $this -> setMsgError( $cReturn -> msg_resp );
          //   $this -> sendError( $sendData, $aditionalData );
          //   return FALSE;
          // }           
          // else{
          //   return true;
          // }
                
           
        }
        return true;

      } 
      catch (Exception $e) 
      {
        
      }
    }

        /*! \fn: setPuntoCumplidoIntegrador
    *  \brief: funcion para enviar el cumplido de punto a integrador GPS
    *  \author: Ing. Nelson Liberato
    *  \date: 2019-10-04
    *  \param:   $mDatPoint array con el codigo cod_remdes como CustomCode e cod_integr como ItineraryID 
    *  \return:  array
    */
    function setPuntoCumplidoIntegrador( $mDatPoint = NULL, $mDataDespac  = NULL)
    {
      try 
      {
          $sendData = array(
              "cod_tokenx" => "0zV;Q;%5=zL6TG",
              "nit_transp" => $mDataDespac['cod_transp'],
              "ifx_server" => 'CORONA', 
              "nom_aplica" => BASE_DATOS, 
              "usr_gpsxxx" => "InterfGpsIntegr", 
              "pwd_gpsxxx" => "0zV;Q;%5=zL6TG", 
              "num_placax" => $mDataDespac["num_placax"],    
              "num_manifi" => $mDataDespac['cod_manifi'], 
              "num_despac" => $mDataDespac['num_despac'], 
              "lstPointVisited" => $mDatPoint
            );

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
          curl_setopt($ch, CURLOPT_URL, URL_INTERF_GPSEVE."/SetVisitedPoint  " ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
          curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
          curl_setopt($ch, CURLOPT_VERBOSE, 1);
          curl_setopt($ch, CURLOPT_POST, true );
          curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $raw_data = curl_exec($ch);
          $error = curl_error($ch);
          $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

           
          //   echo "<pre>mParamsString: "; print_r(URL_INTERF_GPSEVE."/setItinerary  " ); echo "</pre>"; 
          //   echo "<pre>mParamsString: "; print_r( $raw_data ); echo "</pre>"; 
          # Asigna el mensaje del response y lo retorna ------------------------------------------------------------       
          return json_decode($raw_data, true);
      } 
      catch (Exception $e) 
      {
        
      }
    }

        /*! \fn: map_princi
    *  \brief: funcion para generar id en el mapa principal
    *  \author: Ing. Luis Manrique
    *  \date: 2020-01-13
    *  \param:   $data array con las plavas
    *  \return:  array
    */
    public function map_princi($data){
      try{
        if(!constant("URL_INTERF_GPSEVE")){
          include_once 'general/constantes.inc';
        }

            $sendData = array(
                "cod_tokenx" => "0zV;Q;%5=zL6TG",
                "nit_transp" => $data['cod_transp'],
                "ifx_server" => 'CORONA', 
                "nom_aplica" => BASE_DATOS, 
                "usr_gpsxxx" => "InterfGpsIntegr", 
                "pwd_gpsxxx" => "0zV;Q;%5=zL6TG",  
                "PlateList" => $data['placax']
              );


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
            curl_setopt($ch, CURLOPT_URL, URL_INTERF_GPSEVE."/getMapPrincipal" ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
            curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_POST, true );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $raw_data = curl_exec($ch);
            $error = curl_error($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $raw_data;
      }catch(Exception $e){
          return "Errores map_princi: ".$e;
      }
    }

    /********************************************************************************
    * Funcion Verifica si tiene activa la interfaz y el parametro ind_rutfar activo *
    * @fn verifyParameIntgps                                                        *
    * @return array nom_usuari y clv_usuari o FALSE                                 *
    ********************************************************************************/
    public function verifyParameIntgps( $mCodOperad = '53', $mCodTransp =NULL )
    {
      $mSql = "SELECT a.cod_transp, c.nom_tercer, a.nom_usuari, a.clv_usuari, a.cod_tokenx, a.url_webser, a.ind_deseta, IFNULL( b.tie_repgps, 30) AS tie_report
              FROM ".BASE_DATOS.".tab_interf_parame a
              INNER JOIN (
                            SELECT a.cod_transp, a.tie_repgps
                            FROM
                                  ".BASE_DATOS.".tab_transp_tipser a 
                                  INNER JOIN 
                                  (
                                    SELECT MAX(x.num_consec) AS num_consec
                                      FROM ".BASE_DATOS.".tab_transp_tipser x
                                     WHERE x.cod_transp LIKE '".$mCodTransp."'
                                  ) b ON a.num_consec = b.num_consec
                         ) b ON a.cod_transp = b.cod_transp
              INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON c.cod_tercer = b.cod_transp
              WHERE a.cod_operad = '".$mCodOperad."'
                AND a.cod_transp = '".$mCodTransp."'
                AND a.ind_estado = '1'";
      $consulta = new Consulta( $mSql, $this -> cConexion ); 
      return $consulta -> ret_matrix( 'a' );
    }
  }



  // ------------------------------------------------------------------------------------------------------------------------------------------
  // ------------------------------------------------------------------------------------------------------------------------------------------
  // ------------------------------------------------------------------------------------------------------------------------------------------
  // -------------------------------------------------- [  CLASE HUB GPS  ] -------------------------------------------------------------------
  // ------------------------------------------------------------------------------------------------------------------------------------------
  // ------------------------------------------------------------------------------------------------------------------------------------------
  // ------------------------------------------------------------------------------------------------------------------------------------------
  

  /**
  * Clase para conexion de integrador GPS con HUB para solo reportes de ubicacion
  * cod_operad = 53
  * nom_operad = INTERFAZ HUB INTEGRADOR GPS
  * ind_operad = 3
  * clv_usuari = TOKEN CREADO Y ACTIVADO EN CENTRAL gps_apieve.t_client_token.
  * @package default
  * @author nelson.liberato
  * brief para esta interfaz se requeire que en el cliente esté el token de integrador y el ind_operad = 3 para solo HUB
  * 
  * ------------------------------------------------ INSERT EN EL CLIENTE -----------------------------------------------------------------
  * INSERT INTO tab_interf_parame_temp 
  * (
  *   cod_operad, cod_transp, nom_operad, nom_usuari, clv_usuari,  
  *   val_timtra, ind_intind, ind_operad, ind_progps, cod_asegur, ind_estado, 
  *   url_webser, fec_inicio, fec_finali, cod_tokenx, ind_deseta, tie_report
  * )
  * VALUES 
  * ('53', '900119170', 'INTERFAZ HUB INTEGRADOR GPS', 'InterfIntGpsCargam', 'c3348832e955386a24108508cf87f510',  
  *   0, '1', '3', '0', NULL, '1',
  *   'https://central.intrared.net/ap/interf/APIIntegradorGPS/v2/index.php', NULL, NULL, 'TOKEN DE WT', 0, 0
  * );
  * 
  * --------------------------------------------------------------------------------------------------------------------------------------
  * 
  **/
  class InterfHubIntegradorGPS  
  {
  
    var $cConexion  = NULL;
    var $cMsgError  = NULL;
    var $BASE_DATOS  = NULL;
    var $NOM_TRANSPOR  = NULL;
    var $NIT_TRANSPOR  = NULL;
    var $NOM_URL_APLICA  = NULL;
    var $CONS_OPERAD_INTGPS  = NULL;
    var $NOT_MAIL  = NULL;
    var $URL_INTERF_INTGPS  = NULL;
    var $URL_INTERF_FAJOBE  = NULL;
    var $BD_STANDA  = NULL;
    var $PERMISOS_INSERTS  = NULL;
  
    function __construct( $mConexion, $constantes = NULL )
    {
       
        //Si la clase es instanciada desde el framework de los sat
        $this -> BASE_DATOS = BASE_DATOS;
        $this -> CONS_OPERAD_INTGPS = '53';
        $this -> NIT_TRANSPOR = $constantes['cod_transp']; 
        $this -> NOM_TRANSPOR = $constantes['nom_transp'];
        $this -> NOM_URL_APLICA = NOM_URL_APLICA;
        $this -> NOT_MAIL = MAIL_SUPERVISORES;
        $this -> URL_INTERF_INTGPS = NULL;
        $this -> URL_INTERF_FAJOBE = NULL;
        $this -> BD_STANDA = BD_STANDA;
       
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
    
    private function setMsgError( $mMsgError )
    {
      $this -> cMsgError = $mMsgError;
    }
    
    function getMsgError()
    {
      return $this -> cMsgError;
    }
   
    /*! \fn: verifyParameIntgps    
    *  \brief: Verifica los datos de interfaz con el intergador gps , cod_operad = 53
    *  \author: Nelson Liberato
    *  \date: 09/03/2016
    *  \return array
    */


    public function verifyParameIntgps( $mCodOperad = '53' )
    {
      //Se verifica si tiene activa interfaz con FARO 
      $mSql = "SELECT a.cod_transp, c.nom_tercer, a.nom_usuari, a.clv_usuari, a.cod_tokenx, a.url_webser, a.ind_deseta, IFNULL( b.tie_repgps, 30) AS tie_report
              FROM ".BASE_DATOS.".tab_interf_parame a
              INNER JOIN (
                            SELECT a.cod_transp, a.tie_repgps
                            FROM
                                  ".BASE_DATOS.".tab_transp_tipser a 
                                  INNER JOIN 
                                  (
                                    SELECT MAX(x.num_consec) AS num_consec
                                      FROM ".BASE_DATOS.".tab_transp_tipser x
                                     WHERE x.cod_transp LIKE '".$this -> NIT_TRANSPOR."'
                                  ) b ON a.num_consec = b.num_consec
                         ) b ON a.cod_transp = b.cod_transp
              INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON c.cod_tercer = b.cod_transp
              WHERE a.cod_operad = '".$mCodOperad."'
                AND a.cod_transp = '".$this -> NIT_TRANSPOR."'
                AND a.ind_estado = '1'";
      $consulta = new Consulta( $mSql, $this -> cConexion ); 
      return $consulta -> ret_matriz( 'a' );
    }
    
    /*! \fn: sendError    
    *  \brief: Envia correo con errores generados por el WS de la interfaz con faro
    *  \author: Nelson Liberato
    *  \date: 09/03/2016
    *  \return array
    */
    private function sendError( $data )
    {
      $mMessage = "******** Encabezado ******** \n";
      $mMessage .= "Operacion: ".$data['nom_proces']." \n";
      $mMessage .= "Fecha y hora actual: ".date( "Y-m-d H:i" )." \n";
      if( isset( $data["fec_salida"] ) && $data["fec_salida"] !== NULL )
        $mMessage .= "Fecha Salida: ".$data["fec_salida"]." \n";
      $mMessage .= "Empresa de transporte: ".$this -> NIT_TRANSPOR." - ".$this -> NOM_TRANSPOR." \n";
      $mMessage .= "Aplicacion: ".$this -> NOM_URL_APLICA." \n";
      if( isset( $data["num_despac"] ) && $data["num_despac"] !== NULL )
        $mMessage .= "Despacho: ".$data["num_despac"]." \n";
      if( isset( $data["num_manifi"] ) && $data["num_manifi"] !== NULL )
        $mMessage .= "Manifiesto: ".$data["num_manifi"]." \n";
      if( isset( $data["num_placax"] ) && $data["num_placax"] !== NULL )
        $mMessage .= "Placa: ".$data["num_placax"]." \n";
      if( isset( $data["nom_rutasx"] ) && $data["nom_rutasx"] !== NULL )
        $mMessage .= "Ruta: ".$data["nom_rutasx"].' '.$data["cod_ciurut"]." \n";
      if( isset( $data["fec_noveda"] ) && $data["fec_noveda"] !== NULL )
        $mMessage .= "Fecha Novedad: ".$data["fec_noveda"]." \n";
      if( isset( $data["cod_novbas"] ) && $data["cod_novbas"] !== NULL )
        $mMessage .= "Novedad: ".$data["cod_novbas"].' - '.$data["nom_noveda"]." \n";
      if( isset( $data["cod_pcxbas"] ) && $data["cod_pcxbas"] !== NULL )
        $mMessage .= "Puesto de Control: ".$data["cod_pcxbas"].' - '.$data["nom_contro"]." \n";
      if( isset( $data["obs_noveda"] ) && $data["obs_noveda"] !== NULL )
        $mMessage .= "Observacion Novedad: ".$data["obs_noveda"]." \n";
      $mMessage .= "******** Detalle ******** \n";
      $mMessage .= "Codigo de error: ".$data['cod_errorx']." \n";
      $mMessage .= "Mensaje de error: ".$data['error']." \n";
      //mail( $this -> NOT_MAIL, $data['nom_proces'], $mMessage,'From: soporte.ingenieros@intrared.net' );
      return $mMessage;
    }

    /*! \fn: setTrakingStart    
    *  \brief: crea un itinerario en HUB, solo reportes de ubicación
    *  \author: Nelson Liberato
    *  \date: 11/05/2022
    *  \return array
    */
    public function setTrakingStart( $mData = NULL )
    {
      try
      {
        $parameRutfar = self::verifyParameIntgps();
        $mDataVehicu  = self::getDataVehicu($mData['num_despac']);
        

        $data['nom_proces'] = "Webservice - HUB Insertar Salida";
        $data['fec_inicio'] = $mData['fec_inicio'];
        $data['num_despac'] = $mData['num_despac'];
        $data['nit_transp'] = $parameRutfar[0]['cod_transp'];
        $data['nom_transp'] = $parameRutfar[0]['nom_tercer'];

    
        if( $mDataVehicu['nit_operad']  == '' || $mDataVehicu['usr_gpsxxx'] == '' || $mDataVehicu['clv_gpsxxx'] == '' ) { 
          return ['code' => 2005, 
                  'message' =>'NO se envia a HUB, porque el Veh�culo '.$mData['num_placax'].' no tiene GPS asignado' ];
        }


        $mConfig = json_decode( $parameRutfar[0]["ind_deseta"], true ); 
        if(!$mConfig){
          return ['code' => 2005, 'message' =>'NO HAY PARAMETRIZACION DE TIPO INTEGRADOR GPS EN LA INTERFAZ DEL CLIENTE' ];
        }

        $sendData = [
                      //informacion empresa
                      "cod_client"                => $parameRutfar[0]['cod_transp'], // NIT DEL CLIENTE DEL DESPACHO PARA TRANSACCIONES CON CENTRAL (WS
                      "nit_gligps"                => '830076669', // NIT DE FARO, PORQUE SE CREAN LOS ITINERARIO A NOMBRE DE FARO
                      "nom_server"                => 'AVANSATGL',
                      "nom_aplica"                => BASE_DATOS,
                      "nom_client"                => $parameRutfar[0]['nom_tercer'],
                      "tok_integr"                => $parameRutfar[0]["cod_tokenx"],
                      "num_docume"                => $mData["num_docume"],
                      "num_despac"                => $mData['num_despac'],
                      // informacion viaje
                      "asset"                     => $mDataVehicu["num_placax"],
                      "begin_tracking"            => $mData["fec_inicio"],
                      "finish_tracking"           => $mData["fec_finali"],
                      "status"                    => "enable",
                      /************* OBLIGATORIOS PARA HUB ********************/
                      // informacion gps
                      "gps_id"                    => $mDataVehicu['nit_operad'],
                      "gps_user"                  => self::remove_utf8_bom($mDataVehicu['usr_gpsxxx']),
                      "gps_password"              => self::remove_utf8_bom($mDataVehicu['clv_gpsxxx']),
                      "gps_key"                   => null,
                      /************* OBLIGATORIOS PARA HUB ********************/
                      // informacion conductor
                      "gps_driver_id"             => $mDataVehicu['cod_tercer'],
                      "gps_driver_name"           => utf8_encode($mDataVehicu['nom_tercer']),
                      "gps_driver_first_surname"  => utf8_encode($mDataVehicu['nom_apell1']),
                      "gps_driver_secod_surname"  => utf8_encode($mDataVehicu['nom_apell2']),
                      // Tiempo de reporte de ubicacion
                      "ind_origen"                => $mData["ind_origen"],
                      "timeReport"                => $parameRutfar[0]["tie_report"]
                    ];

        $cHeader = [                  
                      "Authorization: 8606c7de060ec2d798ffdc1f29776fb2", 
                      "Authorization-app: ".$parameRutfar[0]["clv_usuari"]
                    ];         
        // codifica en json el array para mandarlo por la api
        $mParamsString = json_encode($sendData, JSON_PRETTY_PRINT);
        //echo "<pre>cHeader"; print_r($cHeader); echo "</pre>"; 
        //echo "<pre>mParamsString"; print_r($mParamsString); echo "</pre>";  die();

        # Inicio de cURL para la API -----------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $parameRutfar[0]["url_webser"]."/trackingstart" );  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $raw_data = curl_exec($ch);
        $error = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
       
        $cReturn = json_decode($raw_data, true);         
        //---------------------------------
        $ret = FALSE; //VARIABLE QUE CONTENDR� EL VALOR

        //Procesa el resultado del WS
        $mStatus  = $cReturn['status'];
        $mCodResp = $cReturn['code_resp'];
        $mMsgResp = utf8_decode($cReturn['msg_resp']);
        $data['cod_errorx'] = $mCodResp;
                    
        if( false != $mStatus)
        {
          throw new Exception($mMsgResp, $mCodResp );
        } 
       
        return ['status' => $mStatus, 'code' => $mCodResp, 'message' =>$mMsgResp ];
      }
      catch( Exception $e )
      { 
        //Se registran los erores en una tabla para poder hacer un mejor seguimiento
        $data['det_respon'] = $e -> getMessage();
        self::setDespacError( $data );
        return ['status' => $mStatus, 'code' => $mCodResp, 'message' =>$mMsgResp ];
        //----------
      }
    }
 
    /*! \fn: getDataVehicu
    *  \brief: Consulta datos del vehiculo
    *  \author: Ing. Nelson Liberato
    *  \date: 11/05/2022
    *  \date modified: dd/mm/aaaa
    *  \param: mNumDespac  Integer  Numero del despacho
    *  \return: Integer
    */
    private function getDataVehicu($mNumDespac = NULL)
    {
      $mQueryVehicu = "SELECT 
                              b.num_placax,
                              d.nit_operad,
                              x.gps_usuari AS usr_gpsxxx,
                              x.gps_paswor AS clv_gpsxxx,
                              '' AS idx_gpsxxx,
                              c.cod_tercer,
                              c.nom_tercer,
                              c.nom_apell1,
                              c.nom_apell2
                         FROM ".BASE_DATOS .".tab_despac_vehige a
                   INNER JOIN ".BASE_DATOS .".tab_vehicu_vehicu b ON a.num_placax = b.num_placax
                   INNER JOIN ".BASE_DATOS .".tab_tercer_tercer c ON a.cod_conduc = c.cod_tercer
                   INNER JOIN ".BASE_DATOS .".tab_despac_despac x ON x.num_despac = a.num_despac
                   LEFT  JOIN ".BD_STANDA  .".tab_genera_opegps d ON d.cod_operad = x.gps_operad
                        WHERE a.num_despac LIKE '".$mNumDespac."'  ";

      $consulta = new Consulta( $mQueryVehicu, $this -> cConexion ); 
      return $consulta -> ret_matriz( 'a' )[0];
    }


    /*! \fn: remove_utf8_bom
    *  \brief: Elimina caracteres BOOM
    *  \author: Ing. Nelson Liberato
    *  \date: 2019-06-04
    *  \param:   $numDespac Numero del despacho del cliente
    *  \param:   $aditionalData array con datos adicionales que complementan la informacion del despacho
    *  \return:  bool
    */
    private function remove_utf8_bom($str)
    {
        @mb_internal_encoding('UTF-8');

        // pre filter
        if (empty($str)) {
            return $str;
        }

        // get charset
        $charset = @mb_detect_encoding($str, array('ISO-8859-1', 'UTF-8', 'ASCII'));

        if (stristr($charset, 'utf') || stristr($charset, 'iso')) {
            $str = iconv('ISO-8859-1', 'UTF-8//TRANSLIT', utf8_decode($str));
        } else {
            $str = @mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        }

        // remove BOM
        $str = urldecode(str_replace(["%C2%81", "?"], '', urlencode($str)));

        // prepare string
        return $str; 
 

    }

    /*! \fn: getTrakingAsset 
    *  \brief: Consulta los reportes de ubicación de una placa
    *  \author: Nelson Liberato
    *  \date: 11/05/2022
    *  \return array
    */
    public function getTrakingAsset( $mData = NULL )
    {
      try
      {
        $parameRutfar = self::verifyParameIntgps(); 
        
        $data['nom_proces'] = "Webservice - HUB Cosultar Reportes";
        $data['num_despac'] = $mData['num_placax'];
    

        $sendData = [
                      // informacion viaje
                      "asset"                     => $mData['num_placax'],
                      "begin_tracking"            => $mData["fec_inicio"],
                      "finish_tracking"           => $mData["fec_finali"]
                    ];

        //echo "<pre>"; print_r($sendData); echo "</pre>";  die();
        $cHeader = [                  
                      "Authorization: 8606c7de060ec2d798ffdc1f29776fb2", 
                      "Authorization-app: ".$parameRutfar[0]["clv_usuari"]
                    ];
        
        // codifica en json el array para mandarlo por la api
        $mParamsString = json_encode($sendData);

        # Inicio de cURL para la API -----------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $parameRutfar[0]["url_webser"]."/trackingasset" );  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $raw_data = curl_exec($ch);
        $error = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
       
        $cReturn = json_decode($raw_data, true);
 

        //---------------------------------
        $ret = FALSE; //VARIABLE QUE CONTENDR� EL VALOR

        //Procesa el resultado del WS
         
        $mStatus = $cReturn['status'];
        $mCodResp = $cReturn['code_resp'];
        $mMsgResp = $cReturn['msg_resp'];
        $data['cod_errorx'] = $mCodResp;
  
        if( false != $mStatus)
        {
          throw new Exception($mMsgResp, $mCodResp );
        } 
       
        return ['status' => $mStatus, 'code' => $mCodResp, 'message' =>$mMsgResp, 'data' => $cReturn['history'] ];
      }
      catch( Exception $e )
      { 
        //Se registran los erores en una tabla para poder hacer un mejor seguimiento
        $data['det_respon'] = $e -> getMessage();
        //self::setDespacError( $data );
        return ['status' => $mStatus, 'code' => $mCodResp, 'message' =>$mMsgResp ];
      }
    }


    /*! \fn: setTrakingEnd    
    *  \brief: Cierra un itinerario en HUB, solo reportes de ubicación
    *  \author: Nelson Liberato
    *  \date: 11/05/2022
    *  \return array
    */
    public function setTrakingEnd( $mData = NULL )
    {
      try
      {

        $parameRutfar = self::verifyParameIntgps();
        $mDataVehicu  = self::getDataVehicu($mData['num_despac']);
        
        $data['nom_proces'] = "Webservice - HUB finalizar itinerario";
        $data['num_placax'] = $mData['num_placax'];
        $data['fec_inicio'] = $mData['fec_inicio'];
        $data['num_despac'] = $mData['num_despac'];
        $data['nit_transp'] = $parameRutfar[0]['cod_transp'];
        $data['nom_transp'] = $parameRutfar[0]['nom_tercer'];
    
        if( $mDataVehicu['nit_operad']  == NULL ) { 
          return ['code' => 2005, 'message' =>'NO se envia a HUB, porque el Veh�culo  '.$mData['num_placax'].' no tiene GPS asignado' ];
        }
 

        $sendData = [
                      "cod_client"                => $parameRutfar[0]['cod_transp'], // NIT DEL CLIENTE DEL DESPACHO PARA TRANSACCIONES CON CENTRAL (WS
                      "nom_server"                => '830076669', // NIT DE FARO, PORQUE SE CREAN LOS ITINERARIO A NOMBRE DE FARO
                      "nom_aplica"                => BASE_DATOS,
                      "nom_client"                => $parameRutfa[0]['nom_tercer'],
                      "tok_integr"                => $parameRutfar[0]["cod_tokenx"],
                      "num_docume"                => $mData["num_docume"],
                      // informacion viaje
                      "asset"                     => $mData['num_placax'],
                      "begin_tracking"            => $mData["fec_inicio"],
                      "finish_tracking"           => $mData["fec_finali"],
                      "cod_integr"                => $mData["cod_itiner"],
                      "clo_reason"                => isset($mData["obs_cierre"]) ? $mData["obs_cierre"] : "No especificado",
                    ];

        //echo "<pre>"; print_r($sendData); echo "</pre>";  die();
        $cHeader = [                  
                      "Authorization: 8606c7de060ec2d798ffdc1f29776fb2", 
                      "Authorization-app: ".$parameRutfar[0]["clv_usuari"] 
                    ];
        
        // codifica en json el array para mandarlo por la api
        $mParamsString = json_encode($sendData);

        # Inicio de cURL para la API -----------------------------------------------------------------------------
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $parameRutfar[0]["url_webser"]."/trackingend" );  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $raw_data = curl_exec($ch);
        $error = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
       
        $cReturn = json_decode($raw_data, true);
 

        //---------------------------------
        $ret = FALSE; //VARIABLE QUE CONTENDR� EL VALOR

        //Procesa el resultado del WS
         
        $mStatus = $cReturn['status'];
        $mCodResp = $cReturn['code_resp'];
        $mMsgResp = $cReturn['msg_resp'];
        $data['cod_errorx'] = $mCodResp;
  
        if( false != $mStatus)
        {
          throw new Exception($mMsgResp, $mCodResp );
        } 
       
        return ['status' => $mStatus, 'code' => $mCodResp, 'message' =>$mMsgResp ];
      }
      catch( Exception $e )
      { 
        $data['det_respon'] = $e -> getMessage();
        self::setDespacError( $data );
        return ['status' => $mStatus, 'code' => $mCodResp, 'message' =>$mMsgResp ];
      }
    }
 


    /*! \fn: setDespacError
    *  \brief:    registra el error de crear itinerario en sate_standa.tab_errorx_despac
    *  \author:   Ing. Nelson Liberato
    *  \date:     2021-04-11
    *  \param:    $data array de datos para la tabla de log de error de despacho 
    *  \return:   bool
    */
    private function setDespacError( $data )
    {
      if( strpos( $data['error'], 'Numero de manifiesto repetido' ) === false )
      {
        $data['det_respon'] = str_replace("'", "", $data['det_respon']);
        $query = "REPLACE INTO  ".BASE_DATOS.".tab_errorx_despac
                  ( nom_bdsatx, num_despac, nit_transp, 
                    nom_transp, nom_aplica, det_errorx, 
                    fec_creaci, cod_operad  )
                  VALUES( '".BASE_DATOS."', '".$data['num_despac']."', '".NIT_TRANSPOR."', 
                          '".NOM_TRANSPOR."', '".BASE_DATOS."', '".$data['det_respon']."',
                          NOW(), '53' )";
        $insercion = new Consulta( $query, $this -> cConexion, "BRC" );
      }
    } 
  }


?>
<?php 

  /*******************************************************************************
  * @file server.php                                                             *
  * @brief Cron para consulta de gps de los despachos.                           *
  * @version 0.1                                                                 *
  * @date 12 de Febrero de 2013                                                    *
  * @author Nelson Liberato.                                                     *
  *******************************************************************************/  
  /*ini_set('display_errors', true);
  error_reporting(E_ALL & ~E_NOTICE);*/
  $noimport=true;
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
  include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes para tabla de gps.
  include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
  include_once("PHPMailer/class.phpmailer.php");
  include_once("/var/www/html/ap/satt_faro/constantes.inc");
  include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker

  $dir="/var/www/html/ap/interf/app/faro/"; // Direcotorio donde se encuentra el cron.
  $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
  $fExcept -> SetUser( 'CronNovedaPendie' );
  $fExcept -> SetParams( "Faro", "Cron para Retransmitir novedades erroneas" );
  $fLogs = array();
  
  try
  {    
  	# 900308992
  	# 811039011
  	# 900220423



  	# 900306833
  	# 900557551
  	# 900118604
  	# 900669189
    $db4   = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS ), $fExcept );
    $mensaje = '';
    $email = array();
    $fecha = date('Y-m-d H:i');
    $fQueryErrors  = "SELECT *, UPPER( nom_metodo ) AS nom_metodo2
                            FROM ".BASE_DATOS.".tab_errorx_noveda
                           WHERE  1 = 1 /*fec_creaci >= DATE_SUB( NOW(), INTERVAL 2 DAY ) AND   
                                  fec_creaci >='2014-12-11 00:00:00'  AND 
                                  ( det_respon LIKE '%Not Found%' OR 
                                    det_respon LIKE '%Couldnt load from%' OR 
                                    det_respon LIKE '%Could not connect to host%' OR 
                                    det_respon LIKE '%No se encuentra numero de despacho%' OR 
                                    det_respon LIKE '%Clave y/o usuario incorrectos%' )  
                                    det_respon LIKE '%Aplicacion%' ) */
                                    /*AND cod_transp = '830081825' */
                                    /*AND nom_aplica = 'satb_acarlt'*/
                                    /*AND fec_creaci BETWEEN '2019-07-22 14:00:00' AND '2019-07-23 17:00:00 '*/
                                    /*AND num_despac IN ('3689887')*/
                                    AND cod_transp = '900046733'
                                    AND cod_consec = '287601'

                                     ";     

 
echo "<pre>"; print_r($fQueryErrors); echo "</pre>";
//die();

    $db4 -> ExecuteCons( $fQueryErrors );
    $Errors = $db4 -> RetMatrix(  );
    echo "<pre>Cantidad=";echo count( $Errors ); /*print_r($Errors); */echo "</pre>";  
    //die();
    if( 0 != $db4 -> RetNumRows() )
    {
      $i = 0;
      echo "<BR><b>Novedades con Errores:</b> <br>";
      echo "<BR>";
      foreach($Errors as $fError)
      { 
        $error_ = NULL;
        echo "______________________________CONSEC".$i."_ N°_".$fError['cod_consec']."____________________<br>";
        
        
          ini_set( "soap.wsdl_cache_enabled", "0" ); 
          if (!class_exists('SoapClient'))
          {
            die ("No se encuentra instalado el módulo PHP-SOAP.");
          }
          
          //Se determina en que servidor esta la aplicacion
          $query = "SELECT a.url_webser	
										  FROM ".BD_STANDA.".tab_genera_server a,
											   ".BASE_DATOS.".tab_transp_tipser b
										  WHERE a.cod_server = b.cod_server AND
												b.cod_transp = '".$fError['cod_transp']."' 
										  ORDER BY b.fec_creaci DESC ";

          $db4 -> ExecuteCons( $query );
          $url_webser = $db4 -> RetMatrix(  );
          $url_webser =  $url_webser[0][0];//URL DEL WSDL. 

          ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
          //Se verificar si la aplicacion existe
          try
          {
            echo $url_webser;
            $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

            //Métodos disponibles en el WS
            $mResult = $oSoapClient -> __call( 'aplicaExists', array( "nom_aplica" => $fError['nom_aplica'] ) );
            $mResult = explode("; ", $mResult);
            $mCodResp = explode(":", $mResult[0]);
            $mMsgResp = explode(":", $mResult[1]);

            if ("1000" != $mCodResp[1])
            {
              $error_ = $mMsgResp[1];
            }
          }
          catch( SoapFault $e )
          {
            $error_ = $e -> getMessage();
          }
          
          //Se alistan los datos para enviar la novedad
          
          $query = "SELECT a.cod_manifi, b.num_placax " .
                  "FROM " . BASE_DATOS . ".tab_despac_despac a, " .
                  "" . BASE_DATOS . ".tab_despac_vehige b " .
                  "WHERE a.num_despac = b.num_despac " .
                  "AND a.num_despac = '" . $fError['num_despac'] . "' ";
          $db4 -> ExecuteCons( $query );
          $mSalida = $db4 -> RetMatrix(  );
                  
                  
          $mQuerySelNomNov = "SELECT nom_noveda, ind_alarma, ind_tiempo, nov_especi, ind_manala  " .
                  "FROM " . BASE_DATOS . ".tab_genera_noveda " .
                  "WHERE cod_noveda = '" . $fError['cod_novfar'] . "' ";

          $db4 -> ExecuteCons( $mQuerySelNomNov );
          $mNomNov = $db4 -> RetMatrix(  );
          
                  
          $mQuerySelNomPc = "SELECT nom_contro " .
                  "FROM " . BASE_DATOS . ".tab_genera_contro " .
                  "WHERE cod_contro = '" . $fError["cod_contro"] . "' ";

          $db4 -> ExecuteCons( $mQuerySelNomPc );
          $mNomPc = $db4 -> RetMatrix(  );
                  
          $mQuerySelPcxbas = "SELECT cod_pcxbas 
             FROM " . BASE_DATOS . ".tab_homolo_trafico 
            WHERE cod_transp = '" . $fError["cod_transp"] . "'
              AND cod_pcxfar = '" . $fError["cod_contro"] . "'
              AND cod_rutfar = '" . $fError["cod_rutasx"] . "'
            ";

          $db4 -> ExecuteCons( $mQuerySelPcxbas );
          $mCodPcxbas = $db4 -> RetMatrix(  );
          

          $parametros = array(  "nom_usuari" => $fError["nom_usuari"],
                                "pwd_clavex" => $fError["pwd_clavex"],
                                "nom_aplica" => $fError["nom_aplica"],
                                "num_manifi" => $fError['num_manifi'],
                                "num_placax" => $fError['num_placax'],
                                "cod_novbas" => 0,
                                "cod_conbas" => $mCodPcxbas[0][0],
                                "tim_duraci" => $fError["tim_duraci"],
                                "fec_noveda" => date('Y-m-d H:i', strtotime($fError["fec_noveda"])),
                                "des_noveda" => str_replace("'", "", $fError["des_noveda"] ),
                                "nom_contro" => $fError['nom_contro'],
                                "nom_sitiox" => $fError['nom_sitiox'],
                                "cod_confar" => NULL,
                                'cod_novfar' => $fError['cod_novfar'],
                                'nom_noveda' => $fError['nom_noveda'],
                                'ind_alarma' => $fError['ind_alarma'],
                                'ind_tiempo' => $fError['ind_tiempo'],
                                'nov_especi_' => $fError['nov_especi_'],
                                'ind_manala' => $fError['ind_manala']
                              );//ARRAY

          echo "<pre>parametros: Metodo:".$fError["nom_metodo2"]." ";
          print_r( $parametros );
          echo "</pre>";

          if (!$error_)
          {
          
              for( $index1 = 0; $index1 < count( $mCodPcxbas ); $index1++ )
              {
                $parametros['cod_conbas'] = $mCodPcxbas[$index1][0];
                //Consumo Web Service.
                try
                {
                    $respuesta = $oSoapClient -> __call( 'setNovedad'.$fError["nom_metodo2"], $parametros );
                    echo "<pre>"; print_r($respuesta); echo "</pre>";
                    $mResult = explode("; ", $respuesta);
                    $mCodResp = explode(":", $mResult[0]);
                    $mMsgResp = explode(":", $mResult[1]);

                    if ("1000" != $mCodResp[1]) 
                    {
                        $error_ = $mMsgResp[1];
                        echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
                        $mMessage = "******** Encabezado ******** \n";
                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                        $mMessage .= "Empresa de transporte: " . $fError["cod_transp"] . " \n";
                        $mMessage .= "Aplicacion: " . $fError["nom_aplica"] . " \n";
                        $mMessage .= "Numero de despacho SAT: " . $fError["num_despac"] . " \n";
                        $mMessage .= "Placa del vehiculo: " . $fError["num_placax"] . " \n";
                        $mMessage .= "Codigo puesto de control: " . $fError["cod_contro"] . " \n";
                        $mMessage .= "Codigo novedad: " . $fError["cod_novfar"] . " \n";
                        $mMessage .= "Nombre novedad: " . $fError["nom_noveda"] . " \n";
                        $mMessage .= "******** Detalle ******** \n";
                        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                        $mMessage .= "Mesaje de error: " . $error_ . " \n";

                        $novedaError['cod_respon'] = $mCodResp[1];
                        $novedaError['msg_respon'] = $error_;
                        $novedaError['det_respon'] = $mMessage;

                        //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                        //mail("hugo.malagon@intrared.net", "Web service TRAFICO - SAT - NOVEDAD PC", $mMessage, 'From: soporte.ingenieros@intrared.net');
                        
                        if ( strpos( $error_, 'cuentra numero de despacho') !== false ) 
                        {
                           echo "Entra porque no se encontro el despacho";
                           echo $delete =  "DELETE FROM ".BASE_DATOS.".tab_errorx_noveda 
                                     WHERE cod_consec = ".$fError['cod_consec']."";
                           //$db4 -> ExecuteCons( $delete, "BRC" );
                        }
                        
                    }
                    else
                    {
                        echo "Entra porque se transmitio bien<br>";
                        echo $delete =  "DELETE FROM ".BASE_DATOS.".tab_errorx_noveda 
                                     WHERE cod_consec = ".$fError['cod_consec']."";
                        $db4 -> ExecuteCons( $delete, "BRC" );
                        break;
                    }
                    
                }
                catch( SoapFault $e )
                {
                    $error_ = $e -> getMessage();

                    if ($error_ != NULL)
                    {
                        echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
                        $mMessage = "******** Encabezado ******** \n";
                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                        $mMessage .= "Empresa de transporte: " . $fError["cod_transp"] . " \n";
                        $mMessage .= "Aplicacion: " . $fError["nom_aplica"] . " \n";
                        $mMessage .= "Numero de despacho SAT: " . $fError["num_despac"] . " \n";
                        $mMessage .= "Placa del vehiculo: " . $fError["num_placax"] . " \n";
                        $mMessage .= "Codigo puesto de control: " . $fError["cod_contro"] . " \n";
                        $mMessage .= "Codigo novedad: " . $fError["cod_novfar"] . " \n";
                        $mMessage .= "Nombre novedad: " . $fError["nom_noveda"] . " \n";
                        $mMessage .= "******** Detalle ******** \n";
                        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                        $mMessage .= "Mesaje de error: " . $error_ . " \n";

                        $novedaError['cod_respon'] = $mCodResp[1];
                        $novedaError['msg_respon'] = $error_;
                        $novedaError['det_respon'] = $mMessage;
                        //Se registran errores de la interfaz en la BD
                      
                        //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                        //mail("hugo.malagon@intrared.net", "Web service TRAFICO - SAT - NOVEDAD PC", $mMessage, 'From: soporte.ingenieros@intrared.net');
                        //mail( "faroavansat@eltransporte.com, soporte.ingenieros@intrared.net", "Web service TRAFICO - SAT", $mMessage,'From: soporte.ingenieros@intrared.net' );
                    }
                }
              }
          }
          else
          {
            $novedaError['cod_respon'] = '';
            $novedaError['msg_respon'] = '';
            $novedaError['det_respon'] = $error_;
            
            //$this->setNovedadError($parametros, $regist, $novedaError, 'pc');
            echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
          }
      }
    }
    else
    {
      echo "<BR>No hay Errores pendientes por retransmitir";
    }
  }
  catch( Exception $e )
  {
    $mTrace = $e -> getTrace();
    $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),$e -> getLine());
    return FALSE;
  }
  
  
  
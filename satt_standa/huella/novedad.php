<?php
ini_set('display_errors', 1);
ini_set('apc.cache_by_default', 0);

include( "../lib/general/tabla_lib.inc" );
include( "conexion_lib.inc" );

//die("BB");

class Novedad
{

    var $conexion = null;

    function __construct($conexion)
    {
        $this->conexion = $conexion;
        $this->Menu();

    }

    function Menu()
    {
        $this->Registrar();
        //echo $this -> mensaje;
    }

    function Registrar()
    {
        $this -> mensaje = '';
        $nMessage = ' | Transportadora: ' . $_POST['cod_transp'] . ' | Conductor: ' . $_POST['cod_tercer'] . ' |';


        define("URL_INTERF_FAROXX", "https://ap.intrared.net:444/ap/interf/app/faro/wsdl/faro.wsdl");

        //---------------------
        $inPuestos = array();
        $inPuestos[] = 0;//Agrego el puesto de control al que pertenece el usuario.
        $inPuestos[] = $_POST["cod_contro"];//Agrego el puesto de control al que pertenece el usuario.
        
        $puestos = "SELECT cod_contro
                      FROM tab_homolo_pcxeal 
                     WHERE ( cod_homolo = '$_POST[cod_contro]' OR cod_contro = '$_POST[cod_contro]' ) ";
          
        //$puestos = $this -> conexion -> Consultar( $puestos, "a", TRUE  );
        $puestos = new Consulta( $puestos, $this -> conexion );
        $puestos = $puestos -> ret_matriz("a");

        $cod_padre = $puestos[0]["cod_contro"];

        if( empty( $cod_padre ) || ( $cod_padre == NULL ) )
          $cod_padre = 0;

        //$_POST[cod_contro] = $cod_padre;
        
        $puestos = "SELECT cod_homolo
                      FROM tab_homolo_pcxeal 
                     WHERE cod_contro = '$cod_padre' ";    
          
        //$puestos = $this -> conexion -> Consultar( $puestos, "a", TRUE  );
        $puestos = new Consulta( $puestos, $this -> conexion );
        $puestos = $puestos -> ret_matriz("a");
        
        if( $puestos )
          foreach( $puestos as $puesto )
            $inPuestos[] = $puesto["cod_homolo"];
        
        $inPuestos = implode( ", ", $inPuestos );

        $inPuestos = empty( $inPuestos ) ? $cod_padre : $inPuestos . ', ' . $cod_padre;



        //---------------------
        $query = "SELECT nom_operad, nom_usuari, clv_usuari 
                  FROM tab_interf_parame 
                  WHERE cod_operad = '50' AND 
                        ind_estado = '1' AND        
                        cod_transp = '$_POST[cod_transp]' ";
        
        $data_homolo = new Consulta($query, $this->conexion);        
        $data_homolo = $data_homolo ->ret_matriz("a");
        $data_homolo = $data_homolo[0];
        
        if( $data_homolo )
        {
            $query = "SELECT d.cod_pcxfar, d.cod_pcxbas
                      FROM tab_despac_despac a,
                           tab_despac_vehige b,
                           tab_interf_parame c,
                           tab_homolo_trafico d,
                           tab_transp_tipser f
                      WHERE a.num_despac = b.num_despac AND 
                            a.fec_salida IS NOT NULL AND
                            a.fec_llegad IS NULL AND
                            a.ind_anulad = 'R' AND
                            b.ind_activo = 'S' AND
                            c.cod_transp = b.cod_transp AND 
                            c.cod_operad = 50 AND
                            c.ind_estado = '1' AND
                            d.cod_pcxfar IN ( $inPuestos ) AND
                            b.cod_conduc = '$_POST[cod_tercer]' AND
                            d.cod_transp = b.cod_transp AND
                            d.cod_rutfar = b.cod_rutasx AND
                            f.cod_transp = b.cod_transp AND
                            f.num_consec = ( SELECT MAX( z.num_consec ) AS num_consec FROM tab_transp_tipser z WHERE z.cod_transp = b.cod_transp ) AND
                            f.cod_tipser = '1' ";

          //---------------------
      
          $codContro = new Consulta($query, $this->conexion);
          $codContro = $codContro->ret_matriz("a");
          $codContro = $codContro[0];

          if ($codContro)
          {
              $_POST[cod_pcxbas] = $codContro['cod_pcxbas'];
              $_POST[cod_contro] = $codContro['cod_pcxfar'];
          }

        }

        $query = "SELECT b.cod_transp, a.cod_manifi, c.ind_estado, 
                         d.cod_noveda, a.num_despac, b.cod_rutasx, 
                         b.num_placax, c.cod_contro
                  FROM tab_despac_vehige b,
                       tab_despac_seguim c,
                       tab_despac_despac a 
                       LEFT JOIN tab_despac_noveda d
                       ON a.num_despac = d.num_despac AND
                          d.cod_noveda = '71' AND
                          d.cod_contro IN ( $inPuestos )
                  WHERE b.cod_conduc = '$_POST[cod_tercer]' AND
                        a.num_despac = b.num_despac AND 
                        a.num_despac = c.num_despac AND 
                        a.fec_salida IS NOT NULL AND
                        a.fec_llegad IS NULL AND
                        a.ind_anulad = 'R' AND
                        b.ind_activo = 'S' AND
                        c.cod_contro IN ( $inPuestos ) ";

        $data = new Consulta( $query, $this -> conexion );
        $data = $data->ret_matriz("a");
        $data = $data[0];
        
        $_POST[placa] = $data[num_placax];

        $_POST[num_manifi] = $data[cod_manifi];
        $_POST[cod_transp] = $data[cod_transp];
        $_POST[num_despac] = $data[num_despac];
        $_POST[cod_rutasx] = $data[cod_rutasx];

        //---------------------------------------------
        if ( !$data_homolo )
          $_POST[cod_contro] = $data[cod_contro];
        //---------------------------------------------

        $query = "SELECT a.nom_contro
          FROM tab_genera_contro a
          WHERE a.cod_contro = '$_POST[cod_contro]'  ";

        $contro = new Consulta($query, $this->conexion);
        
        $contro = $contro->ret_matriz("a");
        $contro = $contro[0];

        if ($data)
        {
            if ($data[ind_estado])
            {
                if ($data[cod_noveda] != 71)
                {
                    $mParams = array("nom_usuari" => $_POST[userLogin],
                        "pwd_clavex" => base64_decode( $_POST[passLogin] ),
                        "cod_transp" => $_POST[cod_transp],
                        "num_manifi" => $_POST[num_manifi],
                        "num_placax" => $_POST[placa],
                        "cod_noveda" => 71,
                        "cod_contro" => $_POST[cod_contro],
                        "tim_duraci" => 0,
                        "fec_noveda" => date("Y-m-d H:i:s"),
                        "des_noveda" => "Registrado desde Biometria.",
                        "nom_sitiox" => substr($contro[nom_contro], 0, 50));
                    //--------------------------------------

                    try
                    {
                            $mResult = NULL;

                            $url_webser = URL_INTERF_FAROXX;
                            $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                            //$mResult = $oSoapClient -> __call( "setNovedadPC", $mParams );

                            //-------------------------------------------------------------------------------------------
                            if( $mResult = $oSoapClient -> __call( "setNovedadPC", $mParams ) )
                            {
                                $query = "SELECT max(a.cod_verpcx)
                                          FROM tab_config_parame a
                                         ";

                                $contro = new Consulta($query, $this->conexion);
                                
                                $contro = $contro->ret_matriz("a");
                                $codigo = $contro[0];

                                //echo $this->mensaje = "La Novedad OK se Registro Exitosamente. Codigo de Verificacion $codigo.";
                            }
                            else
                             $this -> mensaje .= 'Se ha presentado un Error en __call setNovedadPC.'; 
                            //-------------------------------------------------------------------------------------------

                            //posible error
                            if( is_object($mResult))
                                $mResult = String($mResult);

                            //Procesa el resultado del WS
                            $mResult = explode("; ", $mResult);
                            $mCodResp = explode(":", $mResult[0]);
                            $mMsgResp = explode(":", $mResult[1]);
                            $data['cod_errorx'] = $mCodResp[1];

                            $codigo = explode(":", $mResult[2]);
                            $codigo = $codigo[1];

                            if ("1000" != $mCodResp[1])
                            {
                                //Notifica Errores retornados por el WS
                                $data['error'] = $data['nom_proces'] . ': ' . $mMsgResp[1];
                                $this -> mensaje .= '[1] Se ha presentado un Error WS: ' . $data['error'];
                            }
                            else
                            {
                                //$this->mensaje = "La Novedad OK se Registro Exitosamente. Codigo de Verificacion $codigo.";   //ORIGINAL
                                $this -> mensaje .= "La Novedad OK se Registro Exitosamente. Codigo de Verificacion $codigo.";
                            }

                    }
                    catch( SoapFault $e )
                    {
                        //----------
                        $error = $e -> faultstring;
                        if ( $error ) 
                        {
                          // Notifica errores soap
                          $data['error'] = $data['nom_proces'].': '.$error;
                        }
                        elseif ( $e -> fault )
                        {
                          //Notifica Fallos
                          $data['error'] = $data['nom_proces'].': '.$e -> faultcode.':'.$e -> faultdetail.':'.$e -> faultstring;
                        }
                        //----------

                       $this -> mensaje .= '[2] Se ha presentado un Error: ' . $data['error']; 
                    }
                    //--------------------------------------

                    //echo $data['error'];
                    //Se reporta la novedad a la aplicacion del cliente
                    $query = "SELECT nom_operad, nom_usuari, clv_usuari 
                              FROM tab_interf_parame 
                              WHERE cod_operad = '50' AND 
                                    ind_estado = '1' AND        
                                    cod_transp = '$_POST[cod_transp]' ";
                    
                    $data = new Consulta($query, $this->conexion);        
                    $data = $data ->ret_matriz("a");
                    $data = $data[0];
                    
                    $mExecute = TRUE;
                    //Ruta Web Service.
                    if ($data)
                    {
                          $query = "SELECT  a.url_webser 
                                      FROM  tab_genera_server a,
                                            tab_transp_tipser b
                                     WHERE  a.cod_server = b.cod_server AND
                                            b.cod_transp = '$_POST[cod_transp]' 
                                  ORDER BY  b.fec_creaci DESC ";

                          $url_webser = new Consulta($query, $this->conexion);       
                          $url_webser = $url_webser ->ret_matriz("a");
                          $url_webser = $url_webser[0]["url_webser"];

                          try
                          {
                            $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                            $mResult = $oSoapClient -> __call( "aplicaExists", array( "nom_aplica" => $data['nom_operad'] ) );

                            //Procesa el resultado del WS
                            $mResult = explode("; ", $mResult);
                            $mCodResp = explode(":", $mResult[0]);
                            $mMsgResp = explode(":", $mResult[1]);

                            if ("1000" != $mCodResp[1])
                            {
                              $error_ = $mMsgResp[1];
                              $this -> mensaje .= $error_; 
                            }
                          }
                          catch( SoapFault $e )
                          {
                            //----------
                            $error = $e -> faultstring;
                            if ( $error ) 
                            {
                              // Notifica errores
                              $error_ = $err;
                            }
                            elseif ( $e -> fault )
                            {
                              //Notifica Fallos
                              $error_ = $e->faultcode . ':' . $e->faultdetail . ':' . $e->faultstring;
                            }
                            //----------
                            
                            $this -> mensaje .= $error_; 
                          }
                      
                        //--------------------------------------

                        
                        if ( !$error_ )
                        {
                            $query = "SELECT a.cod_manifi, b.num_placax " .
                                    "FROM tab_despac_despac a, " .
                                    "tab_despac_vehige b " .
                                    "WHERE a.num_despac = b.num_despac " .
                                    "AND a.num_despac = '" . $_POST[num_despac] . "' ";
                            //$consulta = new Consulta( $query, $this -> conexion );
                            //$mSalida = $this->conexion->Consultar($query, "a");
                            
                            

                            $mQuerySelNomNov = "SELECT nom_noveda, ind_alarma, ind_tiempo, nov_especi, ind_manala  " .
                                    "FROM tab_genera_noveda " .
                                    "WHERE cod_noveda = '71' ";

                            //$consulta = new Consulta( $mQuerySelNomNov, $this -> conexion );
                            //$mNomNov = $this->conexion->Consultar($mQuerySelNomNov, "a");
                            
                            $mNomNov = new Consulta($mQuerySelNomNov, $this->conexion);        
                            $mNomNov = $mNomNov ->ret_matriz("a");
                            $mNomNov = $mNomNov[0];

                            $mQuerySelNomPc = "SELECT nom_contro " .
                                    "FROM tab_genera_contro " .
                                    "WHERE cod_contro = '" . $_POST[cod_contro] . "' ";

                            //$consulta = new Consulta( $mQuerySelNomPc, $this -> conexion );
                            //$mNomPc = $this->conexion->Consultar($mQuerySelNomPc, "a");
                            
                            $mNomPc = new Consulta($mQuerySelNomPc, $this->conexion);        
                            $mNomPc = $mNomPc ->ret_matriz("a");
                            $mNomPc = $mNomPc[0];

                            $mQuerySelPcxbas = "SELECT cod_pcxbas 
                                   FROM tab_homolo_trafico 
                                  WHERE cod_transp = '" . $_POST['cod_transp'] . "'
                                    AND cod_pcxfar = '" . $_POST['cod_contro'] . "'
                                    AND cod_rutfar = '" . $_POST['cod_rutasx'] . "'
                                  ";
                            
                            //$mCodPcxbas = $this->conexion->Consultar($mQuerySelPcxbas, "a");
                            $mCodPcxbas = new Consulta($mQuerySelPcxbas, $this->conexion);        
                            $mCodPcxbas = $mCodPcxbas ->ret_matriz("a");
                            //$mCodPcxbas = $mCodPcxbas[0];

                            $parametros = array("nom_usuari" => $data['nom_usuari'],
                                "pwd_clavex" => $data['clv_usuari'],
                                "nom_aplica" => $data['nom_operad'],
                                "num_manifi" => $_POST[num_manifi],
                                "num_placax" => $_POST[placa],
                                "cod_novbas" => 0,
                                "cod_conbas" => $mCodPcxbas[0]['cod_pcxbas'],
                                "tim_duraci" => $mParams["tim_duraci"],
                                "fec_noveda" => date('Y-m-d H:i', strtotime($mParams["fec_noveda"])),
                                "des_noveda" => $mParams["des_noveda"],
                                "nom_contro" => $mNomPc['nom_contro'],
                                "nom_sitiox" => substr($mNomPc['nom_contro'], 0, 50),
                                "cod_confar" => NULL,
                                'cod_novfar' => $mParams['cod_noveda'],
                                'nom_noveda' => $mNomNov['nom_noveda'],
                                'ind_alarma' => $mNomNov['ind_alarma'],
                                'ind_tiempo' => $mNomNov['ind_tiempo'],
                                'nov_especi_' => $mNomNov['nov_especi'],
                                'ind_manala' => $mNomNov['ind_manala']
                            );
                            
                            /*
                            //Consumo Web Service.
                            $respuesta = $oSoapClient->call("setNovedadPC", $parametros);
                            */
                            for( $index1 = 0; $index1 < count( $mCodPcxbas ); $index1++ )
                            {
                              $parametros['cod_conbas'] = $mCodPcxbas[$index1]['cod_pcxbas'];
                              try
                              {
                                  $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                  $mResult = $oSoapClient -> __call( "setNovedadPC", $parametros );

                                  if ($oSoapClient->fault)
                                  {
                                      //Notifica Fallos
                                      $error_ = $oSoapClient->faultcode . ':' . $oSoapClient->faultdetail . ':' . $oSoapClient->faultstring;
                                      $this -> mensaje .= $error_; 
                                  }
                                  else
                                  {
                                      $err = $oSoapClient->getError();
                                      if ($err)
                                      {
                                          // Notifica errores
                                          $error_ = $err;
                                          $this -> mensaje .= $error_; 
                                      }
                                      else
                                      {
                                          //Procesa el resultado del WS
                                          $mResult = explode("; ", $respuesta);
                                          $mCodResp = explode(":", $mResult[0]);
                                          $mMsgResp = explode(":", $mResult[1]);

                                          if ("1000" != $mCodResp[1])
                                          {
                                              $error_ = $mMsgResp[1];
                                              $this -> mensaje .= $error_; 
                                          }
                                          else
                                          {
                                              echo "OK";
                                              //$this -> mensaje .= "OK"; 
                                              break;
                                          }
                                      }
                                  }
                              }
                              catch( SoapFault $e )
                              {
                                  //----------
                                  $error = $e -> faultstring;
                                  if ( $error ) 
                                  {
                                    // Notifica errores soap
                                    $data['error'] = $data['nom_proces'].': '.$error;
                                  }
                                  elseif ( $e -> fault )
                                  {
                                    //Notifica Fallos
                                    $data['error'] = $data['nom_proces'].': '.$e -> faultcode.':'.$e -> faultdetail.':'.$e -> faultstring;
                                  }
                                  //----------
                                  $this -> mensaje .= $data['error']; 
                              }
                            
                            }


                            if ($error_ != NULL)
                            {
                                echo $error_;
                                $this -> mensaje .= $error_; 
                            }
                        }
                    }



                    //--------------------------------------
                    //--- BEGIN - NOVEDADES COLOMBIA SOFTWARE
                    //--------------------------------------
                    $query = "SELECT nom_operad, nom_usuari, clv_usuari 
                              FROM tab_interf_parame 
                             WHERE cod_operad = '51'
                               AND ind_estado = '1'
                               AND cod_transp = '".$_POST["cod_transp"]."'";

                    $dataColSof = new Consulta($query, $this->conexion);        
                    $dataColSof = $dataColSof ->ret_matriz("a");
                    $dataColSof = $dataColSof[0];
                            
                    if( $dataColSof )
                    {
                    $query = "SELECT a.num_placax, a.fec_salipl, b.cod_manifi 
                               FROM tab_despac_vehige a, 
                                    tab_despac_despac b 
                              WHERE a.num_despac = '".$_POST[num_despac]."' 
                                AND a.num_despac = b.num_despac ";

                    $mSalida = new Consulta($query, $this->conexion);        
                    $mSalida = $mSalida ->ret_matriz("a");
                    $mSalida = $mSalida[0];


                    //Consulta de Detalle de Novedad
                    $query = "SELECT a.nom_noveda
                                FROM tab_genera_noveda a 
                               WHERE a.cod_noveda = '71'";

                    $mNoveda = new Consulta($query, $this->conexion);        
                    $mNoveda = $mNoveda ->ret_matriz("a");
                    $mNoveda = $mNoveda[0];


                    //Consulta del puesto Padre de la homologacion
                    $query = "SELECT a.cod_contro
                                FROM tab_homolo_pcxeal a 
                               WHERE a.cod_homolo = '".$_POST["cod_contro"]."'
                                  OR a.cod_contro = '".$_POST["cod_contro"]."'";

                    $mControPadre = new Consulta($query, $this->conexion);        
                    $mControPadre = $mControPadre ->ret_matriz("a");
                    $mControPadre = $mControPadre[0];

                    if( !$mControPadre )
                      $mControPadre['cod_contro'] = $_POST["cod_contro"];

                    $mQuerySelNomPc = "SELECT nom_contro " .
                                        "FROM tab_genera_contro " .
                                        "WHERE cod_contro = '" . $mControPadre['cod_contro'] . "' ";

                    $mNomPc = new Consulta($mQuerySelNomPc, $this->conexion);        
                    $mNomPc = $mNomPc ->ret_matriz("a");
                    $mNomPc = $mNomPc[0];

                    //------- Obtenemos la latitud y longitud del puesto ---------
                    $query = "SELECT  a.val_latitu, a.val_longit
                                FROM tab_genera_contro a 
                               WHERE a.cod_contro = '".$mControPadre['cod_contro'] ."' ";

                    $mGeo = new Consulta($query, $this->conexion);        
                    $mGeo = $mGeo ->ret_matriz("a");
                    $mGeo = $mGeo[0];
                    //--------------------------------------
                      
                      
                      
                      /********************* TRATAMIENTO SOAP *********************/
                      ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                      try
                      {
                        //Ruta Web Service Colocar e-com.
                        $url_webser = "http://www.colombiasoftware.net/base/webservice/reportepuestocontrol.php?wsdl";
                        $mFecha[0] = date( 'Y-m-d' );
                        $mFecha[1] = date( 'H:i:s' );
                        //echo "<pre>";print_r($interfaz);echo "</pre>";
                        $parametros = array();
                        $parametros["empresa"]=$dataColSof["nom_operad"];
                        $parametros["usuario"]=$dataColSof["nom_usuari"];
                        $parametros["clave"]=$dataColSof["clv_usuari"];
                        $parametros["sentido"]="0";
                        $parametros["direccion"]=$mNomPc['nom_contro'];
                        $parametros["tipo_evento"] ="0";
                        $parametros["kilometraje"] ="0";
                        $parametros["velocidad"] ="0";
                        $parametros["codigo_puestocontrol"] =$mControPadre['cod_contro'];
                        $parametros["novedad"] = $mNoveda["nom_noveda"].' '."Registrado desde Biometria."; 
                        $parametros["longitud"]=$mGeo['val_longit'];
                        $parametros["latitud"] =$mGeo['val_latitu'];
                        $parametros["hora"] =$mFecha[1];
                        $parametros["fecha"] =$mFecha[0];
                        $parametros["placa"] =$mSalida['num_placax'];
                        $parametros["manifiesto_codigo"] =$mSalida['cod_manifi'];

                        $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                        
                        //Métodos disponibles en el WS
                        $respuesta = $oSoapClient -> __call( 'puestocontrol_humadea', $parametros );
//$this->mensaje = $respuesta;
                        /*
                        echo "<pre>"; print_r( $respuesta ); echo "</pre>";
                        echo "<pre>";print_r( $parametros ); echo "</pre>";
                        */
                        
                        if ( $respuesta != 'OK REPORTO' )
                        {
                            $mMessage = "******** Encabezado ******** \n";
                            $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                            $mMessage .= "Fecha y hora Novedad: " . $mFecha[0].' '.$mFecha[1] . " \n";
                            $mMessage .= "Empresa de transporte: " . $_POST["cod_transp"] . " \n";
                            $mMessage .= "Numero de manifiesto: " . $mSalida['cod_manifi'] . " \n";
                            $mMessage .= "Placa del vehiculo: " . $mSalida['num_placax'] . " \n";
                            $mMessage .= "Codigo puesto de control del despacho: " .$_POST["cod_contro"]. " \n";
                            $mMessage .= "Codigo puesto de control padre: " . $mControPadre['cod_contro'] . " \n";
                            $mMessage .= "Codigo novedad: 71 \n";
                            $mMessage .= "Nombre novedad: " . $mNoveda["nom_noveda"] . " \n";
                            $mMessage .= "Observación enviada: " . 'Interfaz - ' . $parametros["novedad"] . " \n";
                            $mMessage .= "******** Detalle ******** \n";
                            $mMessage .= "Mensaje de error: " . $respuesta . " \n";
                            //echo $mMessage;
                            mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error: Esferas Humadea - Colombiasoftware satt_movil", $mMessage, 'From: soporte.ingenieros@intrared.net');
                        }
                      }
                      catch( SoapFault $e )
                      {
                          $error_ = $e -> getMessage();
                          $this -> mensaje .= $error_; 
                          mail("miguel.garcia@intrared.net", "Error: Esferas - Biometria", $this -> mensaje, 'From: soporte.ingenieros@intrared.net');
                      }
                    }
                    //--------------------------------------
                    //--- END - NOVEDADES COLOMBIA SOFTWARE
                    //--------------------------------------


                }
                else
                {
                  $this -> mensaje = "El Vehiculo ya Registro Novedad en el Puesto de Control.";
                  mail("miguel.garcia@intrared.net", "Error: Esferas - Biometria", "Puesto de Control [ " . $_POST['cod_contro'] . " ]: " . $this -> mensaje . $nMessage, 'From: soporte.ingenieros@intrared.net');
                }
            }
            else
            {
              //$this -> mensaje = "El Vehiculo se encuentra en el siguiente Puesto de Control.";
              $this -> mensaje = "El Vehiculo ya se ha reportado en un Puesto de Control posterior a este.";
              mail("miguel.garcia@intrared.net", "Error: Esferas - Biometria", "Puesto de Control [ " . $_POST['cod_contro'] . " ]: " . $this -> mensaje . $nMessage, 'From: soporte.ingenieros@intrared.net');
            }
        }
        else
        {
          $this -> mensaje = "La Placa No Se Encuentra Disponible... .";
          mail("miguel.garcia@intrared.net", "Error: Esferas - Biometria", "Puesto de Control [ " . $_POST['cod_contro'] . " ]: " . $this -> mensaje . $nMessage, 'From: soporte.ingenieros@intrared.net');
        }

        if ($this -> mensaje)
          echo $this -> mensaje;

        //mail("miguel.garcia@intrared.net", "Error: Esferas - Biometria", $this -> mensaje, 'From: soporte.ingenieros@intrared.net');

    }

}


$new = new Conexion("bd10.intrared.net:3306", $_POST[user], $_POST[pass], $_POST[base]);

$remesa = new Novedad( $new );

//-----------------------------------------
ini_set('apc.cache_by_default', 1);
//-----------------------------------------

?>
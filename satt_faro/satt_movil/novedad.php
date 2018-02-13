<?php
/* ! \file: novedad.php
 *  \brief: Archivo principal para reportar desde EAL (satt_movil)
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: 
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/* ! \class: novedad
 *  \brief: Clase principal para reportar desde EAL (satt_movil)
 */

class Novedad {

    var $conexion = null;
    var $mensaje = null;
    var $HOST_WEB = "";
    var $HOST_WEB_PRO = "avansatgl.intrared.net";

    function __construct($conexion) {
        $this->conexion = $conexion;
        $this->HOST_WEB = $_SERVER['HTTP_HOST'];
        echo "<link rel='stylesheet' href='js/jquery-ui.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='js/jquery-ui.min.css' type='text/css'>\n";

        echo '<script src="js/external/jquery/jquery.js"></script>';
        echo '<script src="js/jquery-ui.js"></script>';
        echo '<script src="js/novedad.js"></script>';

        switch ($_POST["option"]) {
            case "1":
                $this->Registrar();
                break;

            default:
                $this->Buscar();
                break;
        }
    }

    /* ! \fn: Registrar
     *  \brief: Guarda la novedad por medio de WebServic
     *  \author: 
     *     \date: dia/mes/año
     *     \date modified: 25/05/2015
     *  \param: 
     *  \return:
     */

    function Registrar() {
        $inPuestos = array();
        $inPuestos = $this->getPuestos($_POST["cod_contro"]);
        $data = $this->getDespac($_POST['placa'], $inPuestos);

        $mSizeData = sizeof($data);

        for ($i = 0; $i < $mSizeData; $i++) {
            $_POST[$i]["num_manifi"] = $data[$i]["cod_manifi"];
            $_POST[$i]["cod_transp"] = $data[$i]["cod_transp"];
            $_POST[$i]["num_despac"] = $data[$i]["num_despac"];
            $_POST[$i]["cod_rutasx"] = $data[$i]["cod_rutasx"];
            $_POST[$i]["cod_contro"] = $data[$i]["cod_contro"];
        }

        define("URL_INTERF_FAROXX", "https://$_SERVER[HTTP_HOST]/ap/interf/app/faro/wsdl/faro.wsdl");


        $query = "SELECT a.nom_contro FROM tab_genera_contro a WHERE a.cod_contro = '" . $_POST[0][cod_contro] . "'";
        $contro = $this->conexion->Consultar($query, "a");

        if ($data) {

            for ($i = 0; $i < $mSizeData; $i++) {
                $query = "SELECT MAX( a.num_consec ) as num_consec
                            FROM tab_despac_images a
                           WHERE a.num_despac = '" . $_POST[$i][num_despac] . "'
                             AND a.cod_contro = '" . $_POST[$i][cod_contro] . "'";

                $consec = $this->conexion->Consultar($query, "a");
                $consec = ((int) $consec['num_consec']) + 1;

                $insert = "INSERT INTO  tab_despac_images 
                            (
                                num_despac , cod_contro , num_consec, usr_creaci ,
                                fec_creaci , bin_fotoxx, bin_fotox2
                            )
                            VALUES 
                            (
                                '" . $_POST[$i][num_despac] . "',  '" . $_POST[$i][cod_contro] . "', '" . $consec . "',  '" . $_SESSION["satt_movil"]["cod_usuari"] . "',  
                                NOW(), '" . $_REQUEST[img_foto01] . "', '" . $_REQUEST[img_foto02] . "'
                            )";

                $insert = $this->conexion->Consultar($insert, "a");

                $this->mensaje = "La Imagen se registro exitosamente.";
                //--- NOVEDADES COLOMBIA SOFTWARE ---
                $query = "SELECT nom_operad, nom_usuari, clv_usuari 
                            FROM tab_interf_parame 
                           WHERE cod_operad = '51'
                             AND ind_estado = '1'
                             AND cod_transp = '" . $_POST[$i]["cod_transp"] . "'";
                $dataColSof = $this->conexion->Consultar($query, "a");

                if ($dataColSof) {
                    $query = "SELECT a.num_placax, a.fec_salipl, b.cod_manifi 
                                FROM tab_despac_vehige a, 
                                     tab_despac_despac b 
                               WHERE a.num_despac = '" . $_POST[$i][num_despac] . "' 
                                 AND a.num_despac = b.num_despac ";

                    $mSalida = $this->conexion->Consultar($query, "a");


                    //Consulta de Detalle de Novedad
                    $query = "SELECT a.nom_noveda
                                FROM tab_genera_noveda a 
                               WHERE a.cod_noveda = '71'";

                    $mNoveda = $this->conexion->Consultar($query, "a");


                    //Consulta del puesto Padre de la homologacion
                    $query = "SELECT a.cod_contro
                                FROM tab_homolo_pcxeal a 
                               WHERE a.cod_homolo = '" . $_POST[$i]["cod_contro"] . "'
                                  OR a.cod_contro = '" . $_POST[$i]["cod_contro"] . "'";

                    $mControPadre = $this->conexion->Consultar($query, "a");
                    if (!$mControPadre)
                        $mControPadre['cod_contro'] = $_POST[$i]["cod_contro"];

                    $mQuerySelNomPc = "SELECT nom_contro " .
                            "FROM tab_genera_contro " .
                            "WHERE cod_contro = '" . $mControPadre['cod_contro'] . "' ";

                    $mNomPc = $this->conexion->Consultar($mQuerySelNomPc, "a");

                    //------- Obtenemos la latitud y longitud del puesto ---------
                    $query = "SELECT  a.val_latitu, a.val_longit
                                FROM tab_genera_contro a 
                               WHERE a.cod_contro = '" . $mControPadre['cod_contro'] . "' ";

                    $mGeo = $this->conexion->Consultar($query, "a");


                    ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        $mFile = fopen("/var/www/html/ap/satt_faro/satt_movil/logs/error-".$parametros['empresa_codigocs'].$mFecha[0].".txt", "a+");
                    try {
                        //Ruta Web Service Colocar e-com.
                        $url_webser = "http://www.colombiasoftware.net/base/webservice/ReportePuestoControlCS.php?wsdl";
                        $mFecha[0] = date('Y-m-d');
                        $mFecha[1] = date('H:i:s');
                        $parametros = array();
                        $parametros["empresa_codigocs"] = $dataColSof["nom_operad"];
                        $parametros["usuario"] = $dataColSof["nom_usuari"];
                        $parametros["clave"] = $dataColSof["clv_usuari"];
                        $parametros["novedad"] = $mNoveda["nom_noveda"] . ' ' . "Registrado desde <br>Dispositivo Movil " . $_POST[device] . ". <br>IP:" . $_SERVER["REMOTE_ADDR"];
                        $parametros["hora"] = $mFecha[1];
                        $parametros["fecha"] = $mFecha[0];
                        $parametros["placa"] = $mSalida['num_placax'];
                        $parametros["ubicacion"] = $mNomPc['nom_contro']." lat ".$mGeo['val_longit']." long ".$mGeo['val_latitu'];
                        //$parametros["codigo_puestocontrol"] = $mControPadre['cod_contro'];
                        //$parametros["manifiesto_codigo"] = $mSalida['cod_manifi'];

                        fwrite($mFile, "parametros enviados:------------------------------------------ \n");
                        fwrite($mFile, var_export($parametros, true)." \n");


                        $oSoapClient = new SoapClient($url_webser, array('encoding' => 'ISO-8859-1'));

                        //Métodos disponibles en el WS
                        $respuesta = $oSoapClient->__soapCall('novedadConUbicacion', $parametros);

                        fwrite($mFile, "Respuesta del WS de colombiasoftware:------------------------------------------ \n");
                        fwrite($mFile, var_export($respuesta, true)." \n");  
                        fwrite($mFile, "----------------------------------- Fin Log ----------------------------------\n");
                        
                        if ($respuesta != 'OK REPORTO.') {
                            $mMessage = "******** Encabezado ******** \n";
                            $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                            $mMessage .= "Fecha y hora Novedad: " . $mFecha[0] . ' ' . $mFecha[1] . " \n";
                            $mMessage .= "Empresa de transporte: " . $_POST[$i]["cod_transp"] . " \n";
                            $mMessage .= "Numero de manifiesto: " . $mSalida['cod_manifi'] . " \n";
                            $mMessage .= "Placa del vehiculo: " . $mSalida['num_placax'] . " \n";
                            $mMessage .= "Codigo puesto de control del despacho: " . $_POST[$i]["cod_contro"] . " \n";
                            $mMessage .= "Codigo puesto de control padre: " . $mControPadre['cod_contro'] . " \n";
                            $mMessage .= "Codigo novedad: 71 \n";
                            $mMessage .= "Nombre novedad: " . $mNoveda["nom_noveda"] . " \n";
                            $mMessage .= "Observación enviada: " . 'Interfaz - ' . $parametros["novedad"] . " \n";
                            $mMessage .= "******** Detalle ******** \n";
                            $mMessage .= "Mensaje de error: " . $respuesta . " \n";
                            mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error Web service Humadea - Colombiasoftware satt_movil", $mMessage, 'From: soporte.ingenieros@intrared.net');
                        }
                    } catch (SoapFault $e) {
                        fwrite($mFile, "catch:------------------------------------------ \n");
                        fwrite($mFile, var_export($e, true)." \n");
                        fwrite($mFile, "finlog------------------------------------------ \n");
                        fclose($mFile);
                        $error_ = $e->getMessage();
                    }
                }

                try {
                    $mParams = array("nom_usuari" => $_SESSION["satt_movil"]["cod_usuari"],
                        "pwd_clavex" => base64_decode($_SESSION["satt_movil"]["clv_usuari"]),
                        "cod_transp" => $_POST[$i]["cod_transp"],
                        "num_manifi" => $_POST[$i]["num_manifi"],
                        "num_placax" => $_POST["placa"],
                        "cod_noveda" => 71,
                        "cod_contro" => $_POST[$i]["cod_contro"],
                        "tim_duraci" => 0,
                        "fec_noveda" => date("Y-m-d H:i:s"),
                        "des_noveda" => "Registrado desde <br>Dispositivo Movil $_POST[device]. <br>IP:" . $_SERVER["REMOTE_ADDR"],
                        "nom_sitiox" => substr($contro["nom_contro"], 0, 50)
                    );

                    $url_webser = URL_INTERF_FAROXX;
                    $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));
                    $mResult = $oSoapClient->__call("setNovedadPC", $mParams);

                    //Procesa el resultado del WS
                    $mResult = explode("; ", $mResult);
                    $mCodResp = explode(":", $mResult[0]);
                    $mMsgResp = explode(":", $mResult[1]);
                    $data['cod_errorx'] = $mCodResp[1];

                    $codigo = explode(":", $mResult[2]);
                    $codigo = $codigo[1];

                    if ("1000" != $mCodResp[1]) {
                        //Notifica Errores retornados por el WS
                        $data['error'] = $data['nom_proces'] . ': ' . $mMsgResp[1];                        
                    } else {
                        if ( ($_SESSION["satt_movil"]["cod_usuari"] == 'eclaltodeltrigo' || $_SESSION["satt_movil"]["cod_usuari"] == 'ealgranada' || $_SESSION["satt_movil"]["cod_usuari"] == 'GRANADA1'  ) && 
                              $_POST[$i]["cod_transp"] == 900503325) {
                            $objMail = $this->getDataDespac($_POST[$i]["num_despac"]);
                            $this->sendEmail($objMail, $_POST[$i]["cod_transp"]);
                        }
                        $this->mensaje = "La Novedad <b>OK</b> se Registro Exitosamente.<br>Codigo de Verificaci&oacute;n <b>$codigo</b>.<br>";
                    }
                } catch (SoapFault $e) {
                    $error = $e->faultstring;
                    if ($error) {
                        // Notifica errores
                        $data['error'] = $data['nom_proces'] . ': ' . $error;
                    } elseif ($e->fault) {
                        //Notifica Fallos
                        $data['error'] = $data['nom_proces'] . ': ' . $e->faultcode . ':' . $e->faultdetail . ':' . $e->faultstring;
                    }
                }

                echo $data['error'];


                //Se reporta la novedad a la aplicacion del cliente
                $query = "SELECT nom_operad, nom_usuari, clv_usuari 
                            FROM tab_interf_parame 
                           WHERE cod_operad = '50'
                             AND ind_estado = '1'
                             AND cod_transp = '" . $_POST[$i]["cod_transp"] . "'";

                $data = $this->conexion->Consultar($query, "a");

                $mExecute = TRUE;
                //Ruta Web Service.
                if ($data) {
                    //CONSULTAR URL WSDL.
                    $query = "SELECT a.url_webser    
                                FROM satt_standa.tab_genera_server a
                          INNER JOIN tab_transp_tipser b ON a.cod_server = b.cod_server
                               WHERE b.cod_transp = '" . $_POST[$i][cod_transp] . "'
                                 AND a.ind_estado = 1 
                            ORDER BY b.fec_creaci DESC ";

                    $url_webser = $this->conexion->Consultar($query, "a");
                    $url_webser = $url_webser["url_webser"];


                    $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));
                    try {
                        $mResult = $oSoapClient->__call("aplicaExists", array("nom_aplica" => $data['nom_operad']));

                        //Procesa el resultado del WS
                        $mResult = explode("; ", $respuesta);
                        $mCodResp = explode(":", $mResult[0]);
                        $mMsgResp = explode(":", $mResult[1]);

                        if ("1000" != $mCodResp[1]) {
                            $error_ = $mMsgResp[1];
                        }
                    } catch (SoapFault $e) {
                        $error = $e->faultstring;
                        if ($error) {
                            // Notifica errores
                            $error_ = $err;
                        } elseif ($e->fault) {
                            //Notifica Fallos
                            $error_ = $e->faultcode . ':' . $e->faultdetail . ':' . $e->faultstring;
                        }
                    }

                    if (!$error_) {
                        $query = "SELECT a.cod_manifi, b.num_placax 
                                    FROM tab_despac_despac a
                              INNER JOIN tab_despac_vehige b ON a.num_despac = b.num_despac
                                   WHERE a.num_despac = '" . $_POST[$i]["num_despac"] . "' ";
                        $mSalida = $this->conexion->Consultar($query, "a");

                        $mQuerySelNomNov = "SELECT nom_noveda, ind_alarma, ind_tiempo, nov_especi, ind_manala  " .
                                "FROM tab_genera_noveda " .
                                "WHERE cod_noveda = '71' ";
                        $mNomNov = $this->conexion->Consultar($mQuerySelNomNov, "a");

                        $mQuerySelNomPc = "SELECT nom_contro " .
                                "FROM tab_genera_contro " .
                                "WHERE cod_contro = '" . $_POST[$i]["cod_contro"] . "' ";
                        $mNomPc = $this->conexion->Consultar($mQuerySelNomPc, "a");

                        $mQuerySelPcxbas = "SELECT cod_pcxbas 
                                              FROM tab_homolo_trafico 
                                             WHERE cod_transp = '" . $_POST[$i]['cod_transp'] . "'
                                               AND cod_pcxfar = '$_POST[cod_contro]'
                                               AND cod_rutfar = '" . $_POST[$i]['cod_rutasx'] . "'";
                        $mCodPcxbas = $this->conexion->Consultar($mQuerySelPcxbas, "a", TRUE);

                        if (!$mCodPcxbas) {
                            $mQuerySelPcxbas = "SELECT a.cod_pcxbas 
                                                  FROM tab_homolo_trafico a
                                            INNER JOIN tab_genera_contro b ON a.cod_pcxfar = b.cod_contro
                                                 WHERE a.cod_transp = '" . $_POST[$i]['cod_transp'] . "'
                                                   AND b.nom_contro = '" . $mNomPc['nom_contro'] . "'
                                                   AND a.cod_rutfar = '" . $_POST[$i]['cod_rutasx'] . "'";
                            $mCodPcxbas = $this->conexion->Consultar($mQuerySelPcxbas, "a", TRUE);
                        }

                        $parametros = array("nom_usuari" => $data['nom_usuari'],
                            "pwd_clavex" => $data['clv_usuari'],
                            "nom_aplica" => $data['nom_operad'],
                            "num_manifi" => $_POST[$i]["num_manifi"],
                            "num_placax" => $_POST["placa"],
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
                            'ind_manala' => $mNomNov['ind_manala'],
                            'bin_fotcon' => base64_encode($_REQUEST["img_foto01"]),
                            'bin_fotpre' => base64_encode($_REQUEST["img_foto02"])
                        );

                        for ($index1 = 0; $index1 < count($mCodPcxbas); $index1++) {
                            $parametros['cod_conbas'] = $mCodPcxbas[$index1]['cod_pcxbas'];
                            try {

                                //Consumo Web Service.
                                $respuesta = $oSoapClient->__call("setNovedadPC", $parametros);

                                //Procesa el resultado del WS
                                $mResult = explode("; ", $respuesta);
                                $mCodResp = explode(":", $mResult[0]);
                                $mMsgResp = explode(":", $mResult[1]);

                                if ("1000" != $mCodResp[1]) {
                                    $error_ = $mMsgResp[1];

                                    //--------- Se reenvia la novedad con otro puesto de control en caso que Retorne Error de Puesto de control no existente
                                } else {
                                    $error_ = NULL;
                                    break;
                                }
                            } catch (SoapFault $e) {
                                $error = $e->faultstring;
                                if ($error) {
                                    // Notifica errores
                                    $error_ = $error;
                                } elseif ($e->fault) {
                                    //Notifica Fallos
                                    $error_ = $e->faultcode . ':' . $e->faultdetail . ':' . $e->faultstring;
                                }
                            }
                        }


                        if ($error_ != NULL) {
                            echo $error_;
                            $mMessage = "******** Encabezado ******** \n";
                            $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                            $mMessage .= "Empresa de transporte: " . $_POST[$i]['cod_transp'] . " \n";
                            $mMessage .= "Aplicacion: " . $data['nom_operad'] . " \n";
                            $mMessage .= "Numero de despacho FARO: " . $_POST[$i]["num_despac"] . " \n";
                            $mMessage .= "Placa del vehiculo: " . $_POST["placa"] . " \n";
                            $mMessage .= "Codigo puesto de control: " . $_POST[$i]['cod_contro'] . " \n";
                            $mMessage .= "Codigo novedad: " . $mParams['cod_noveda'] . " \n";
                            $mMessage .= "******** Detalle ******** \n";
                            $mMessage .= "Codigo de error: " . $mCodResp[1] . " \n";
                            $mMessage .= "Mesaje de error: " . $error_ . " \n";

                            $novedaError['cod_respon'] = $mCodResp[1];
                            $novedaError['msg_respon'] = $error_;
                            $novedaError['det_respon'] = $mMessage;
                            //Se registran errores de la interfaz en la BD
                            $this->setNovedadError($parametros, $_POST[$i], $novedaError, $data, 'pc');

                            mail("hugo.malagon@intrared.net", "Web service TRAFICO - SAT - NOVEDAD PC BIOMETRIA", $mMessage, 'From: soporte.ingenieros@intrared.net');
                        }
                    } else {
                        $novedaError['cod_respon'] = '';
                        $novedaError['msg_respon'] = '';
                        $novedaError['det_respon'] = $error_;

                        $this->setNovedadError($parametros, $_POST[$i], $novedaError, $data, 'pc');
                        echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $data['nom_operad'] . ':' . $error_ . "</b></div>";
                    }
                }
            }
        } else {
            $this->mensaje = "La Placa No Se Encuentra Disponible";
        }
        $this->Buscar();
    }

    /* ! \fn: setNovedadError
     *  \brief: Guarda los errores de las novedades
     *  \author: 
     *     \date: dia/mes/año
     *     \date modified: dia/mes/año
     *  \param: 
     *  \return:
     */

    function setNovedadError($parametros, $regist, $novedaError, $data, $metodo) {
        $query = "INSERT INTO tab_errorx_noveda 
                    (     cod_transp, num_despac,  cod_rutasx,
                        cod_contro, nom_metodo,  cod_respon,
                        msg_respon, det_respon,  nom_usuari,
                        pwd_clavex, nom_aplica,  num_manifi,
                        num_placax, cod_novbas,  cod_conbas,
                        tim_duraci, fec_noveda,  des_noveda,
                        nom_contro, nom_sitiox,  cod_confar,
                        cod_novfar, nom_noveda,  ind_alarma,
                        ind_tiempo, nov_especi_, ind_manala,
                        usr_creaci, fec_creaci
                    )
                    VALUES
                    (    '" . $regist['cod_transp'] . "', '" . $regist['num_despac'] . "', '" . $regist['cod_rutasx'] . "',
                        '" . $regist["cod_contro"] . "', '" . $metodo . "', '" . $novedaError['cod_respon'] . "',
                        '" . $novedaError['msg_respon'] . "', '" . $novedaError['det_respon'] . "', '" . $parametros['nom_usuari'] . "',
                        '" . $parametros['pwd_clavex'] . "', '" . $parametros['nom_aplica'] . "', '" . $parametros['num_manifi'] . "',
                        '" . $parametros['num_placax'] . "', '" . $parametros['cod_novbas'] . "', '" . $parametros['cod_conbas'] . "',
                        '" . $parametros['tim_duraci'] . "', '" . $parametros['fec_noveda'] . "', '" . $parametros['des_noveda'] . "',
                        '" . $parametros['nom_contro'] . "', '" . $parametros['nom_sitiox'] . "', '" . $parametros['cod_confar'] . "',
                        '" . $parametros['cod_novfar'] . "', '" . $parametros['nom_noveda'] . "', '" . $parametros['ind_alarma'] . "',
                        '" . $parametros['ind_tiempo'] . "', '" . $parametros['nov_especi_'] . "', '" . $parametros['ind_manala'] . "',
                        '" . $data['nom_usuari'] . "', NOW() 
                    )";
        $this->conexion->Start();
        $insercion = $this->conexion->Consultar($query);
        if ($insercion)
            $this->conexion->Commit();
    }

    /* ! \fn: Buscar
     *  \brief: Busca el despacho o los despachos segun el puesto de control al cual esta ligado 
     *             el usuario y la placa para reportar la novedad desde EAL,
     *             Si el o los despacho tienen recomendaciones muestra un PopUp para dar solucion
     *             Solicita foto de confirmacion en la EAL
     *  \author: 
     *     \date: dia/mes/año
     *     \date modified: 25/05/2015
     *  \param: 
     *  \return:
     */

    function Buscar() {
        $_POST["placa"] = strtoupper($_POST["placa"]);
        $_POST["placa"] = trim($_POST["placa"]);
        $cod_usuari = $_SESSION["satt_movil"]["cod_usuari"];

        $pc = $this->getPC();

        if ($pc) {

            $mCodContros = $this->getPuestos($pc['clv_filtro']);
            $data = $this->getDespac($_POST['placa'], $mCodContros);


            /*
             *  \brief: Valida si el despacho tiene Recomendaciones sin ejecutar en el puesto de control
             *  \warning: 
             */
            if ($data != null) {
                $mNumDespac = '';
                for ($i = 0; $i < sizeof($data); $i++) {
                    $mNumDespac .= $mNumDespac == '' ? $data[$i][num_despac] : "," . $data[$i][num_despac];
                }

                $mSql = "SELECT b.des_texto,  b.htm_config, b.ind_requer, 
                                a.num_condes, a.num_despac, a.cod_contro, 
                                a.cod_noveda, a.cod_rutasx, a.cod_recome, 
                                b.cod_tipoxx 
                           FROM tab_recome_asigna a 
                     INNER JOIN tab_genera_recome b ON a.cod_recome = b.cod_consec 
                          WHERE a.num_despac IN ( " . $mNumDespac . " )
                            AND a.cod_contro IN ( " . $mCodContros . " ) 
                            AND a.ind_ejecuc = 0 
                       GROUP BY a.cod_recome ";
                $mRecomeAsigna = $this->conexion->Consultar($mSql, "a", TRUE);
            }
        }

        if ($pc) {
            if ($this->mensaje)
                echo "<div align=center style='color:#900' ><h3>" . $this->mensaje . "</h3></div>";
            ?>

            <form name="form"  id="form" method="post" action="index.php" enctype="multipart/form-data">
                <table align=center >
                    <tr><td align="center" colspan="2" ><h4>ESFERA: <?= $pc["nom_contro"]; ?></h4></td></tr>
                    <tr>
                        <td align="right" >
                            <label><b>Placa:</b></label>
                        </td>
                        <td align="left" >
                            <?= "<input type='text' maxlength='6' size='6' class='campo' name='placa' id='placa' value='$_POST[placa]' onchange='form.submit()' >"; ?>
                        </td>
                    </tr>
                    <?php if ($data): ?>
                        <?php
                        if ($mRecomeAsigna != NULL) {
                            $_SESSION[RecomeAsigna] = $mRecomeAsigna;

                            echo "<script>
                                    showDespacRecome('" . $mNumDespac . "');   
                                  </script> ";
                        }
                        for ($i = 0; $i < sizeof($data); $i++) {
                            echo "
                                <tr>
                                    <td align='right' >
                                        <label><b>Manifiesto " . ($i + 1) . ":</b></label>
                                    </td>
                                    <td align='left' >" . $data[$i][cod_manifi] . "</td>
                                </tr>
                                <tr>
                                    <td align='right' >
                                        <label><b>Despacho " . ($i + 1) . ":</b></label>
                                    </td>
                                    <td align='left' >" . $data[$i][num_despac] . "</td>
                                </tr>
                            ";
                        }
                        ?>
                        <tr>
                            <td align="right" >
                                <label><b>Foto Conductor:</b></label>
                            </td>
                            <td align="left" >
                                <span onclick="showCamara(1)">
                                    <img width="25px" border="0" src="../satt_movil/imagenes/camara.ico">
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" >
                                <label><b>Foto Precinto:</b></label>
                            </td>
                            <td align="left" >
                                <span onclick="showCamara(2)">
                                    <img width="25px" border="0" src="../satt_movil/imagenes/camara.ico">
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="2" >
                                </br>
                                <input type="button" value="Novedad" onclick='validar()' >
                                </br>
                            </td>
                        </tr>

                    <?php endif; ?>
                </table>
                <input type="hidden" name="option" id="option" value="0" >
                <input type="hidden" name="device" id="device"  >
                <input type="hidden" name="cod_contro" value="<?= $data["0"]["cod_contro"]; ?>" >
                <input type="hidden" name="img_foto01" id="img_foto01ID" value="<?= $data["img_foto01"]; ?>" >
                <input type="hidden" name="img_foto02" id="img_foto02ID" value="<?= $data["img_foto02"]; ?>" >
            </form>        
            <script>
                document.getElementById("device").value = navigator.platform.toLowerCase();
            </script>
            <?php
        } else {
            echo "<div class='error' >El Usuario <b>$cod_usuari</b> No esta Relacionado a un Puesto de Control.</div>";
        }
    }

    /* ! \fn: getPC
     *  \brief: trea el puesto de control de una esfera
     *  \author: Ing. Alexander Correa
     *  \date: 20/06/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return codigo del puesto de control
     */

    private function getPC() {

        $cod_usuari = $_SESSION["satt_movil"]["cod_usuari"];

        $clv_filtro = "SELECT a.clv_filtro, b.nom_contro, b.cod_contro
                         FROM tab_aplica_filtro_usuari a
                   INNER JOIN tab_genera_contro b ON a.clv_filtro = b.cod_contro
                        WHERE a.cod_aplica = '1' 
                          AND a.cod_filtro = '7' 
                          AND a.cod_usuari = '$cod_usuari' 
                          AND a.clv_filtro != 0";
        return $this->conexion->Consultar($clv_filtro, "a");
    }

    /* ! \fn: getPuestos
     *  \brief: trae los puesos de control hijos o padres aignados al Usuario
     *  \author: Ing. Alexander Correa
     *  \date: 20/06/2016
     *  \date modified: dia/mes/año
     *  \param: $pc => int => identificador del puesto de control    
     *  \return return
     */

    private function getPuestos($pc) {
        $query = "  (
                            SELECT x.cod_homolo 
                              FROM tab_homolo_pcxeal x 
                             WHERE x.cod_contro = '$pc'
                        )
                        UNION
                        (
                            SELECT y.cod_contro 
                              FROM tab_homolo_pcxeal y 
                             WHERE y.cod_homolo = '$pc'
                        )  ";
        $datos = $this->conexion->Consultar($query, "a", TRUE);

        $mCodContros = $pc;
        foreach ($datos as $key => $value) {
            $puestos .= "," . $value['cod_homolo'];
        }
        $mCodContros .= $puestos;
        return $mCodContros;
    }

    /* ! \fn: getDespac
     *  \brief: trae los despachos en ruta que tengan habilitado el puesto de control actual
     *  \author: Ing. Alexander Correa
     *  \date: 20/06/2016
     *  \date modified: dia/mes/año
     *  \param: num_placax => string => placa del vehiculo    
     *  \return array con los despachos
     */

    private function getDespac($num_placax, $cod_contros) {
        $query = "SELECT x.cod_transp, x.cod_manifi, x.ind_estado, 
                             x.cod_noveda, x.num_despac, x.cod_rutasx,
                             x.cod_contro, x.con_telmov 
                        FROM (
                                      SELECT b.cod_transp, a.cod_manifi, c.ind_estado, 
                                             d.cod_noveda, a.num_despac, b.cod_rutasx,
                                             c.cod_contro, a.con_telmov 
                                        FROM tab_despac_despac a 
                                  INNER JOIN tab_despac_vehige b ON a.num_despac = b.num_despac 
                                  INNER JOIN tab_despac_seguim c ON a.num_despac = c.num_despac
                                   LEFT JOIN tab_despac_noveda d ON a.num_despac = d.num_despac AND d.cod_noveda = '71' AND d.cod_contro IN ( $cod_contros )
                                       WHERE b.num_placax = '$num_placax' 
                                         AND a.fec_salida IS NOT NULL 
                                         AND a.fec_salida <= NOW() 
                                         AND ( a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00' )
                                         AND a.ind_anulad = 'R' 
                                         AND b.ind_activo = 'S' 
                                         AND c.cod_contro IN ( $cod_contros) 
                                         AND (a.fec_citcar IS NULL OR DATE_FORMAT(CONCAT(a.fec_citcar, ' ', a.hor_citcar), '%Y-%m-%d %H:%i:%s') <= NOW())
                                    GROUP BY a.num_despac 
                                    ORDER BY a.num_despac DESC 
                              ) x 
                       WHERE x.cod_noveda IS NULL";
        $datos = $this->conexion->Consultar($query, "a", TRUE);

        foreach ($datos as $row) {
            $mSqlB = "SELECT a.cod_contro 
                       FROM tab_despac_seguim a 
                 INNER JOIN tab_genera_rutcon b ON a.cod_rutasx = b.cod_rutasx AND a.cod_contro = b.cod_contro 
                      WHERE a.num_despac = '{$row[num_despac]}' 
                        /*AND b.ind_estado = 1*/
                        AND b.val_duraci > (
                                             SELECT c.val_duraci 
                                               FROM tab_despac_seguim d 
                                         INNER JOIN tab_genera_rutcon c ON d.cod_rutasx = c.cod_rutasx  AND d.cod_contro = c.cod_contro 
                                              WHERE d.num_despac = '{$row[num_despac]}' 
                                                /*AND c.ind_estado = 1*/
                                                AND d.cod_contro IN ( $cod_contros ) 
                                           )
                   ORDER BY b.val_duraci ";

            $mSql = "(
                         SELECT x.num_despac 
                           FROM tab_despac_noveda x 
                          WHERE x.num_despac = '{$row[num_despac]}'
                            AND x.cod_contro IN ( {$mSqlB} )
                     )
                     UNION
                     (
                         SELECT x.num_despac 
                           FROM tab_despac_contro x 
                          WHERE x.num_despac = '{$row[num_despac]}'
                            AND x.cod_contro IN ( {$mSqlB} )
                     ) ";

            $mRutaPost = $this->conexion->Consultar($mSql, "a", TRUE);
            if (!$mRutaPost) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /* ! \fn: sendEmail
     *  \brief: envia un email al cliente
     *  \author: Ing. Alexander Correa
     *  \date: 27/06/2016
     *  \date modified: dia/mes/año
     *  \param: $datos => array => datos necesarios para enviar el email   
     *  \param: $cod_tercer => string => para sacar la direccion de email de la transportadora   
     *  \return
     */

    private function sendEmail($datos, $cod_tercer) {
       
        $sql = "SELECT dir_emailx FROM tab_tercer_tercer WHERE cod_tercer = $cod_tercer";
        $email = $this->conexion->Consultar($sql, "a", TRUE);       
        $email = $email[0]['dir_emailx'];
        
        $html = "<table width='100%' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td colspan='2' style='background-color:#35650f;color:#ffffff;font-family:Times New Roman;font-size:14px;padding:4px;text-align: center;'>
                            <b>Reporte EAL Alto del Trigo</b>
                        </td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Despacho:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>".$datos['num_despac']."</td>
                    </tr>
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Puesto de Control:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>EAL ALTO DEL TRIGO</td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Placa:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>".$datos['num_placax']."</td>
                    </tr>
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Conductor:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>".$datos['abr_tercer']."</td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Novedad:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>OK EAL SIN NOVEDAD</td>
                    </tr>                   
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Fecha de Novedad:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>" . date('Y-m-d H:i:s') . "</td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Fecha Actual:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>" . date('Y-m-d H:i:s') . "</td>
                    </tr>                    
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Observaci&oacute;n:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Registrado desde la apliación Movil.</td>
                    </tr>                   
                 </table>";        
        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'From: supervisores@eltransporte.org';

        if ($this->HOST_WEB == $this->HOST_WEB_PRO) {
            return mail($email, 'Reporte Novedad EAL Alto del Trigo', $html, $cabeceras);
        } else {
            return mail("leidy.acosta@intrared.net", 'Reporte Novedad EAL Alto del Trigo',$html, $cabeceras);
        }
    }

    /* ! \fn: getDataDespac
     *  \brief: trae la informacion de un despacho para enviar un email
     *  \author: Ing. Alexander Correa
     *  \date: 28/06/2016
     *  \date modified: dia/mes/año
     *  \param: num_despac => integer => numero de despacho     
     *  \return arreglo con la informacion del despacho
     */

    private function getDataDespac($num_despac) {
        $sql = "SELECT a.num_despac, a.num_placax, c.abr_tercer 
                  FROM tab_despac_vehige a 
            INNER JOIN tab_vehicu_vehicu b ON b.num_placax = a.num_placax 
            INNER JOIN tab_tercer_tercer c ON b.cod_conduc = c.cod_tercer
                 WHERE a.num_despac = $num_despac
              GROUP BY a.num_despac";
        $datos = $this->conexion->Consultar($sql, "a", TRUE);
        return $datos[0];
    }

}

$remesa = new Novedad($this->conexion);
?> 
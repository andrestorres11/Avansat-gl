<?php
/* ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/
class Novedad
{
    var $conexion = null;
    var $mensaje = null;

    function __construct( $conexion )
    {
        $this -> conexion = $conexion;

        switch( $_POST["option"] )
        {
            case "1":
                $this -> Registrar();
                break;
            
            default:
                $this -> Buscar();
                break;
        }
    }
    
    function Buscar()
    {
        $frm_titulo = '';
        $nom_rutasx = '';
        $cod_contro = '';
        $nom_contro = '';
        $fec_planea = '';
        $tip_noveda = NULL;
        $ind_noveda = NULL;
        $this -> mensaje = "";
        
        $_POST["placa"] = strtoupper( $_POST["placa"] );
        $_POST["placa"] = trim( $_POST["placa"] );
        
        $cod_usuari = $_SESSION["satt_movil"]["num_placax"];
        $num_despac = $_SESSION["satt_movil"]["num_despac"];
        $OPT_NOVEDA = $this -> getNovedad( 'admin' );

        $pue_contro = "SELECT a.cod_contro, b.nom_contro, a.cod_rutasx, c.nom_rutasx, a.fec_planea 
                         FROM tab_despac_seguim a,
                              tab_genera_contro b,
                              tab_genera_rutasx c
                        WHERE a.cod_contro = b.cod_contro 
                          AND a.cod_rutasx = c.cod_rutasx 
                          AND a.num_despac = '".$num_despac."'
                     ORDER BY a.fec_planea ASC  ";
        $pc = $this -> conexion -> Consultar( $pue_contro, "a", TRUE );

        /*echo "<pre>";
        print_r($pc);
        echo "</pre>";
        echo "<hr>".sizeof($pc)."<hr>";*/

        if( $pc )
        {
            $ind_cargue = $this -> getCargue( $num_despac );
            $ent_cargue = $ind_cargue[0]['fec_inicar'];
            $sal_cargue = $ind_cargue[0]['fec_fincar'];
            
            $cen_contro = "SELECT a.cod_contro, b.nom_contro, a.fec_planea
                             FROM tab_despac_seguim a, 
                                  tab_genera_contro b,
                                  (
                                    SELECT x.* from
                                                (SELECT c.cod_contro, c.fec_creaci
                                                FROM tab_despac_noveda c
                                                WHERE c.num_despac ='".$num_despac."'
                                                UNION 
                                                SELECT d.cod_contro, d.fec_creaci
                                                FROM tab_despac_contro d
                                                WHERE d.num_despac ='".$num_despac."'
                                                ) AS x
                                                ORDER BY x.fec_creaci DESC
                                                LIMIT 1
                                    ) AS y
                            WHERE a.cod_contro = b.cod_contro
                              AND a.num_despac =  '".$num_despac."'
                              AND a.cod_contro = y.cod_contro
                          ";
            $act_puecon = $this -> conexion -> Consultar( $cen_contro, "a" );
            
            if( !$ent_cargue )
            {
                $frm_titulo = 'Control Inicio Sitio de Cargue';
                $nom_rutasx = $pc[0]['nom_rutasx'];
                $cod_contro = $pc[0]['cod_contro'];
                $nom_contro = $pc[0]['nom_contro'];
                $fec_planea = $pc[0]['fec_planea'];
                $tip_noveda = 'NC';
                $ind_noveda = 1;
                $ind_menuxx = NULL;
            }
            elseif( !$sal_cargue )
            {
                $frm_titulo = 'Control Salida Sitio de Cargue';
                $nom_rutasx = $pc[0]['nom_rutasx'];
                $cod_contro = $pc[0]['cod_contro'];
                $nom_contro = $pc[0]['nom_contro'];
                $fec_planea = $pc[0]['fec_planea'];
                $tip_noveda = 'PC';
                $ind_noveda = 2;
                $ind_menuxx = NULL;
            }
            else
            {
                $ind_menuxx = 1;
                if ( $_POST["action"] == 1)
                {
                    if($act_puecon && $act_puecon['cod_contro'] != $pc[(sizeof($pc)-1)]['cod_contro'])
                    {
                        $frm_titulo = 'Novedades en Ruta';
                        $nom_rutasx = $pc[0]['nom_rutasx'];
                        $cod_contro = $act_puecon['cod_contro'];
                        $nom_contro = $act_puecon['nom_contro'];
                        $fec_planea = $act_puecon['fec_planea'];
                        $tip_noveda = 'NC';
                        $ind_noveda = 3;
                        $ind_menuxx = NULL;
                    }
                    else
                    {
                        $pc = NULL;
                    }
                }
                if ( $_POST["action"] == 2)
                {
                    //if($act_puecon && $act_puecon['cod_contro'] == $pc[(sizeof($pc)-1)]['cod_contro'])
                    if($pc[(sizeof($pc)-1)]['cod_contro'])
                    {
                        $IND_DESLLE = $this -> getDestinatario( $num_despac, 2 );
                        $OPT_DESTIN = $this -> getDestinatario( $num_despac, 1 );
                        if( sizeof( $IND_DESLLE ) > 0 )
                        {
                            $frm_titulo = 'Control Salida de Sitio de Descargue';
                            
                            if( sizeof( $OPT_DESTIN ) == 1 )
                                $tip_noveda = 'PC';
                            else
                                $tip_noveda = 'NC';
                            
                            $ind_noveda = 5;
                        }
                        else
                        {
                            $frm_titulo = 'Control Llegada a Sitio de Descargue';
                            $tip_noveda = 'NC';
                            $ind_noveda = 4;
                        }
                        $nom_rutasx = $pc[(sizeof($pc)-1)]['nom_rutasx'];
                        $cod_contro = $pc[(sizeof($pc)-1)]['cod_contro'];
                        $nom_contro = $pc[(sizeof($pc)-1)]['nom_contro'];
                        $fec_planea = $pc[(sizeof($pc)-1)]['fec_planea'];
                        $ind_menuxx = NULL;
                    }
                    else
                    {
                        $pc = NULL;
                        $this -> mensaje = "La Placa <b>".$cod_usuari."</b> No tiene un Puesto de Control pendiente.";
                    }
                }
            }
        }

        
        if( $pc )
        {
            JS( "novedad" );
            if( !$ind_menuxx )
            {
                $form = new Form( array( 'name'=>'form', 'enctype'=>'multipart/form-data' ) );
                    $form -> Row();
                        $form -> Title( array( "title" => $frm_titulo, "colspan" => 2 ) );
                    $form -> closeRow();
                    $form -> Row();
                        $form -> Label(  array( "label" => "No. Despacho:" ) );
                        $form -> Info(   array( "value" => $num_despac  ) );
                    $form -> closeRow();
                    
                    if( $ind_noveda == 4 )
                    {
                        $INF_DESTIN = $this -> getInfoDestinatario( $num_despac, $_REQUEST['num_docume'] );
                        $form -> Row();
                            $form -> Label(  array( "label" => "No. Remision:" ) );
                            $form -> Info(   array( "value" => $INF_DESTIN[0][2] ) );
                        $form -> closeRow();
                        $form -> Row();
                            $form -> Label(  array( "label" => "No. Remesa:" ) );
                            $form -> Info(   array( "value" => $INF_DESTIN[0][3] ) );
                        $form -> closeRow();
                    }
                    
                    if( $ind_noveda == 5 )
                    {
                        $form -> Row();
                            $form -> Label(  array( "label" => "No. Remision:" ) );
                            $form -> Info(   array( "value" => $IND_DESLLE[0][2]  ) );
                        $form -> closeRow();
                        $form -> Row();
                            $form -> Label(  array( "label" => "No. Remesa:" ) );
                            $form -> Info(   array( "value" => $IND_DESLLE[0][3]  ) );
                        $form -> closeRow();
                    }
                    
                    $form -> Row();
                        $form -> Label(  array( "label" => "Ruta:" ) );
                        $form -> Info(   array( "value" => $nom_rutasx  ) );
                    $form -> closeRow();
                    $form -> Row();
                        $form -> Label(  array( "label" => "Sitio:" ) );
                        $form -> Info(   array( "value" => $nom_contro  ) );
                    $form -> closeRow();
                    $form -> Row();
                        $form -> Label(  array( "label" => "Fecha y Hora Planeada:" ) );
                        $form -> Info(   array( "value" => $fec_planea  ) );
                    $form -> closeRow();
                    
                    $form -> Row();
                        if( $ind_noveda == 3 )
                        {
                            $form -> Label(  array( "label" => "Novedad:" ) );
                            $form -> Select( array( "name" => "cod_noveda" ), $OPT_NOVEDA, NULL );
                        }
                        else
                        {
                            $form -> Hidden( array( "name" => "cod_noveda", "value" => 2, "colspan" => 2 )); //Novedad OK Vehiculo Sin Novedad
                        }
                    $form -> closeRow();

                    if( $ind_noveda == 4 )
                    {
                        $form -> Row();
                            $form -> Label(  array( "label" => "Destinatario:" ) );
                            $form -> Select( array( "name" => "num_docume", "onchange" => "Llegada();" ), $OPT_DESTIN, $_REQUEST['num_docume'] );
                            $form -> Hidden( array( "name" => "action" ));
                        $form -> closeRow();
                    }
                    
                    if( $ind_noveda == 5 )
                    {
                        $form -> Row();
                            $form -> Label(  array( "label" => "Destinatario:" ) );
                            $form -> Info(   array( "value" => $IND_DESLLE[0][1]  ) );
                            $form -> Hidden( array( "name" => "num_docume", "value" => $IND_DESLLE[0][0], "colspan" => 2 ));
                        $form -> closeRow();
                        $form -> Row();
                            $form -> Label(  array( "label" => "Adjuntar Foto:" ) );
                            $form -> File(   array( "name" => "foto"  ) );
                        $form -> closeRow();
                    }

                    $form -> Row();
                        $form -> Boton(  array( "label" => "Confirmación", "onclick" => "validar()", "colspan" => 2 ) );
                    $form -> closeRow();

                    if( $this -> mensaje )
                        $form -> msg( $this -> mensaje );
                    
                    $form -> Row();
                        $form -> Hidden( array( "name" => "device" ));
                        $form -> Hidden( array( "name" => "option" ));
                        $form -> Hidden( array( "name" => "cod_contro", "value" => $cod_contro ));
                        $form -> Hidden( array( "name" => "tip_noveda", "value" => $tip_noveda ));
                        $form -> Hidden( array( "name" => "ind_noveda", "value" => $ind_noveda ));
                    $form -> closeRow();
                $form -> closeForm();
                $form -> JS( 'document.getElementById( "device" ).value = navigator.platform.toLowerCase();' );
            }
            else
            {
                $form = new Form( array( 'name'=>'form', 'enctype'=>'multipart/form-data' ) );
                    $form -> Row();
                        $form -> Boton(  array( "label" => "Novedades en Ruta", "onclick" => "Puestoactual()", "colspan" => 2 ) );
                    $form -> closeRow();
                    $form -> Row();
                        $form -> Boton(  array( "label" => "Control Sitio de Descargue", "onclick" => "Llegada()", "colspan" => 2 ) );
                    $form -> closeRow();
                    $form -> Row();
                        $form -> Hidden( array( "name" => "option" ));
                        $form -> Hidden( array( "name" => "action" ));
                    $form -> closeRow();
                $form -> closeForm();
            }
        }
        else
        {
            //$this -> mensaje = "La Placa <b>".$cod_usuari."</b> No tiene un Puesto de Control pendiente.";
            $form = new Form( array( 'name'=>'form', 'id'=>'form', 'enctype'=>'multipart/form-data' ) );
                if( $this -> mensaje )
                    $form -> msg( $this -> mensaje );
                $form -> Row();
                    $form -> Boton(  array( "label" => "Aceptar", "link" => "", "icon" => "home", "onclick" => "form.submit();" ) );
                    $form -> Hidden( array( "name" => "option" ));
                $form -> closeRow();
            $form -> closeForm();
            $this -> mensaje = NULL;
        }
    }
    
    function Registrar()
    {
        $_POST["placa"] = $_SESSION["satt_movil"]["num_placax"];
        $_POST["num_despac"] = $_SESSION["satt_movil"]["num_despac"];
        
        $pue_contro = "SELECT a.cod_contro, b.nom_contro, a.cod_rutasx, 
                              c.nom_rutasx, a.fec_planea, d.cod_transp,
                              e.cod_manifi, e.cod_client, e.cod_ciudes,
                              d.fec_llegpl
                         FROM tab_despac_seguim a,
                              tab_genera_contro b,
                              tab_genera_rutasx c,
                              tab_despac_vehige d,
                              tab_despac_despac e
                        WHERE a.num_despac = '".$_POST["num_despac"]."'
                          AND a.cod_contro = '".$_POST["cod_contro"]."'
                          AND a.cod_contro = b.cod_contro
                          AND a.cod_rutasx = c.cod_rutasx 
                          AND a.num_despac = d.num_despac
                          AND a.num_despac = e.num_despac
                      ";
        //echo "<hr>".$pue_contro."<hr>";
        $pc = $this -> conexion -> Consultar( $pue_contro, "a" );
        
        $_POST["cod_transp"] = $pc["cod_transp"];
        $_POST["num_manifi"] = $pc["cod_manifi"];
        $_POST["cod_client"] = $pc["cod_client"];
        $_POST["cod_ciudes"] = $pc["cod_ciudes"];
        $_POST["fec_llegpl"] = $pc["fec_llegpl"];

        define( "URL_INTERF_FAROXX", "https://avansatgl.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl" );
        //define( "URL_INTERF_FAROXX", "https://dev.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl" );
        try
        {
            $mParams = array( "nom_usuari" => 'InterfMovilx', 
                              "pwd_clavex" => 'M0qw_238cmd',
                              "cod_transp" => $_POST["cod_transp"], 
                              "num_manifi" => $_POST["num_manifi"], 
                              "num_placax" => $_POST["placa"], 
                              "cod_noveda" => $_POST["cod_noveda"], 
                              "cod_contro" => $_POST["cod_contro"],
                              "tim_duraci" => 0, 
                              //"cod_conbas" => 0, 
                              "fec_noveda" => date( "Y-m-d H:i:s" ), 
                              "des_noveda" => "Registrado desde <br>Dispositivo Movil ".$_POST['device'].". <br>IP:".$_SERVER["REMOTE_ADDR"], 
                              "nom_sitiox" => substr( $pc["nom_contro"], 0, 50 ) );
            
            $mParams['cod_rutasx'] = $pc["cod_rutasx"];
            $mParams['nom_aplica'] = substr( $pc["nom_contro"], 0, 50 );
            $data['nom_usuari'] = 'SATT_MOVILX';
            $url_webser = URL_INTERF_FAROXX;
            $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

            if( $_POST['tip_noveda'] == 'NC' )
                $mResult = $oSoapClient -> __call( "setNovedadNC", $mParams );//Antes del Puesto de control
            else
                $mResult = $oSoapClient -> __call( "setNovedadPC", $mParams );//En el Puesto de control

            //Procesa el resultado del WS
            $mResult = explode("; ", $mResult);
            $mCodResp = explode(":", $mResult[0]);
            $mMsgResp = explode(":", $mResult[1]);
            $data['cod_errorx'] = $mCodResp[1];

            $codigo = explode( ":", $mResult[2] );
            $codigo = $codigo[1];

            if ("1000" != $mCodResp[1])
            {
                //Notifica Errores retornados por el WS
                //$data['error'] = $data['nom_proces'].': '.$mMsgResp[1];
                $error_ = $mMsgResp[1];
            }
            else
            {
                $this -> mensaje = "La Novedad <b>OK</b> se Registro Exitosamente.<br>Codigo de Verificaci&oacute;n <b>$codigo</b>.<br>";
            }
        }
        catch( SoapFault $e )
        {
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
        }
        
        if( $error_ != NULL )
        {
            echo $error_;
            $mMessage = "******** Encabezado ******** \n";
            $mMessage .= "Fecha y hora: ".date( "Y-m-d H:i" )." \n";
            $mMessage .= "Empresa de transporte: ".$_POST['cod_transp']." \n";
            //$mMessage .= "Aplicacion: ".$data['nom_operad']." \n";
            $mMessage .= "Numero de despacho FARO: ".$_POST["num_despac"]." \n";
            $mMessage .= "Placa del vehiculo: ".$_POST["placa"]." \n";
            $mMessage .= "Codigo puesto de control: ".$_POST['cod_contro']." \n";
            $mMessage .= "Codigo novedad: ".$mParams['cod_noveda']." \n";
            $mMessage .= "******** Detalle ******** \n";
            $mMessage .= "Codigo de error: ".$mCodResp[1]." \n";
            $mMessage .= "Mesaje de error: ".$error_." \n";

            $novedaError['cod_respon'] = $mCodResp[1];
            $novedaError['msg_respon'] = $error_;
            $novedaError['det_respon'] = $mMessage;
            //Se registran errores de la interfaz en la BD
            $this -> setNovedadError( $mParams, $_POST, $novedaError, $data, $_POST['tip_noveda'] );

            mail( "jorge.preciado@intrared.net", "Web service TRAFICO - SAT - MOVILX", $mMessage,'From: soporte.ingenieros@intrared.net' );
        }
        //--------------------------------------

        switch( $_POST["ind_noveda"] )
        {
            case "1":
                $this -> setFechaInicioFinCargue( $_POST["num_despac"], 'fec_inicar' );
                break;
            case "2":
                $this -> setFechaInicioFinCargue( $_POST["num_despac"], 'fec_fincar' );
                break;
            case "4":
                $this -> setFechaInicioFinDescargue( $_POST["num_despac"], $_POST["num_docume"], 'fec_inides' );
                break;
            case "5":
                $this -> setFechaInicioFinDescargue( $_POST["num_despac"], $_POST["num_docume"], 'fec_findes' );
                $this -> setFotodestinatario( $_POST["num_despac"], $_POST["num_docume"] );
                break;
        }
        
        $DESTIN = $this -> getDestinatario( $_POST["num_despac"] );
        if( sizeof($DESTIN) == 0 )
        {
            $this -> setDestinatarios( $_POST );
        }

        $form = new Form( array( 'name'=>'form', 'id'=>'form', 'enctype'=>'multipart/form-data' ) );
            if( $this -> mensaje )
                $form -> msg( $this -> mensaje );
            $form -> Row();
                $form -> Boton(  array( "label" => "Aceptar", "link" => "", "icon" => "home", "onclick" => "form.submit();" ) );
                $form -> Hidden( array( "name" => "option" ));
            $form -> closeRow();
        $form -> closeForm();
    }
    
    function setNovedadError( $parametros, $regist, $novedaError, $data, $metodo )
    {
        $query = "INSERT INTO tab_errorx_movilx
                  ( cod_transp, num_despac,  cod_rutasx,
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
                  ( '".$regist['cod_transp']."', '".$regist['num_despac']."', '".$regist['cod_rutasx']."',
                    '".$regist["cod_contro"]."', '".$metodo."', '".$novedaError['cod_respon']."',
                    '".$novedaError['msg_respon']."', '".$novedaError['det_respon']."', '".$parametros['nom_usuari']."',
                    '".$parametros['pwd_clavex']."', '".$parametros['nom_aplica']."', '".$parametros['num_manifi']."',
                    '".$parametros['num_placax']."', '".$parametros['cod_novbas']."', '".$parametros['cod_conbas']."',
                    '".$parametros['tim_duraci']."', '".$parametros['fec_noveda']."', '".$parametros['des_noveda']."',
                    '".$parametros['nom_contro']."', '".$parametros['nom_sitiox']."', '".$parametros['cod_confar']."',
                    '".$parametros['cod_novfar']."', '".$parametros['nom_noveda']."', '".$parametros['ind_alarma']."',
                    '".$parametros['ind_tiempo']."', '".$parametros['nov_especi_']."', '".$parametros['ind_manala']."',
                    '".$data['nom_usuari']."', NOW() )";
        //echo "<hr>".$query."<hr>";
        $this -> conexion -> Start();
        $insercion = $this -> conexion -> Consultar( $query );
        if( $insercion )
            $this -> conexion -> Commit();
    }
    
    function setFechaInicioFinCargue(  $num_despac = NULL, $nom_campox = NULL )
    {
        $query = "UPDATE tab_despac_despac 
                     SET ".$nom_campox." =  NOW() 
                   WHERE num_despac = '".$num_despac."' " ;
        //echo "<hr>".$query."<hr>";
        $this -> conexion -> Start();
        $insercion = $this -> conexion -> Consultar( $query );
        if( $insercion )
            $this -> conexion -> Commit();
    }
    
    function setFechaInicioFinDescargue(  $num_despac = NULL, $num_docume = NULL, $nom_campox = NULL )
    {
        $query = "UPDATE tab_despac_destin 
                     SET ".$nom_campox." =  NOW() 
                   WHERE num_despac = '".$num_despac."' 
                     AND num_docume = '".$num_docume."'  " ;
        //echo "<hr>".$query."<hr>";
        $this -> conexion -> Start();
        $insercion = $this -> conexion -> Consultar( $query );
        if( $insercion )
            $this -> conexion -> Commit();
    }
    
    function setFotodestinatario(  $num_despac = NULL, $num_docume = NULL )
    {
        //-------SE SUBE LA FOTO-----------------
        // Temporary file name stored on the server
        $tmpName  = $_FILES['foto']['tmp_name'];  
        if( $tmpName ) 
        {
            $size = $_FILES["foto"]["size"];

            $fp = fopen( $tmpName , "rb"); // extrae el arreglo de bites del archivo y lo almacena en una variable temporal
            $contenido = fread($fp, $size); //crea el objeto con el arreglo de bits, y le asigna su longitud;
            $contenido = addslashes($contenido); //establece el contenido como arreglo;
            fclose($fp); //cierra el archive abierto para ya no consumer recursos
            
            $query = "UPDATE tab_despac_destin 
                         SET bin_fotoxx =  '".$contenido."' 
                       WHERE num_despac = '".$num_despac."' 
                         AND num_docume = '".$num_docume."'  " ;
            //echo "<hr>".$query."<hr>";
            $this -> conexion -> Start();
            $insercion = $this -> conexion -> Consultar( $query );
            if( $insercion )
                $this -> conexion -> Commit();
        }
    }

    function Select( $name, $options, $value )
    {
        $HTML = "<select name='".$name."' id='".$name."'>";
        foreach( $options as $row )
        {
            if( $row[0] == $value ) 
                $selected = " selected ";
            $HTML .= "<option value='".$row[0]."' ".$selected.">".( $row[1] )."</option>";
            $selected = "";
        }
        return $HTML .= "</select>";
    }
    
    function getNovedad( $usuario = NULL )
    {
        /*$mSQL = "SELECT a.cod_noveda, a.nom_noveda
                         FROM tab_genera_noveda a,
                              tab_perfil_noveda b,
                              tab_genera_usuari c
                        WHERE a.cod_noveda = b.cod_noveda
                          AND b.cod_perfil = c.cod_perfil
                          AND c.cod_usuari = '".$usuario."'
                 ";*/

        $mSQL = "SELECT a.cod_noveda, a.nom_noveda
                   FROM tab_genera_noveda a ";
        return array_merge ( array( array( 0 => "" ) ), $this -> conexion -> Consultar( $mSQL, "i", TRUE ) );
    }
    
    function getDestinatario( $num_despac = NULL, $accion = NULL )
    {
        $mSQL = "SELECT a.num_docume, a.nom_destin, a.num_docume, a.num_docalt
                         FROM tab_despac_destin a
                        WHERE a.num_despac = '".$num_despac."'
                ";
        if( $accion == 1 )
        {
            $mSQL .= " AND a.fec_inides IS NULL ";
            $mSQL .= " AND a.fec_findes IS NULL ";
            //echo "<hr>".$mSQL."<hr>";
            return array_merge ( array( array( 0 => "" ) ), $this -> conexion -> Consultar( $mSQL, "i", TRUE ) );
        }
        elseif( $accion == 2 )
        {
            $mSQL .= " AND a.fec_inides IS NOT NULL ";
            $mSQL .= " AND a.fec_findes IS NULL ";
            //echo "<hr>".$mSQL."<hr>";
            return $this -> conexion -> Consultar( $mSQL, "i", TRUE );
        }
        else
        {
            //echo "<hr>".$mSQL."<hr>";
            return $this -> conexion -> Consultar( $mSQL, "i", TRUE );
        }
    }
    
    function getCargue( $num_despac = NULL )
    {
        $mSQL = "SELECT a.fec_inicar, a.fec_fincar
                         FROM tab_despac_despac a
                        WHERE a.num_despac = '".$num_despac."'
                ";
        //echo "<hr>".$mSQL."<hr>";
        return $this -> conexion -> Consultar( $mSQL, "a", TRUE );
    }
    
    function setDestinatarios( $mData )
    {
        $date = $mData["fec_llegpl"];
        $mData["cod_client"] = !$mData["cod_client"] ? 'NULL': "'".$mData["cod_client"]."'";
        $query = "INSERT INTO tab_despac_destin (
                                                 num_despac, num_docume, 
                                                 cod_genera, nom_destin, cod_ciudad, 
                                                 fec_citdes, 
                                                 hor_citdes, usr_creaci, fec_creaci) 
                                         VALUES (
                                                 '".$mData["num_despac"]."', '".$mData["num_manifi"]."', 
                                                 ".$mData["cod_client"].", 'Lugar Descargue', '".$mData["cod_ciudes"]."',
                                                 '".substr($date, 0, 10)."',
                                                 '".substr($date, 12, 8)."', 'SATT_MOVILX', NOW()
                                                ) " ;
        //echo "<hr>".$query."<hr>";
        $this -> conexion -> Start();
        $insercion = $this -> conexion -> Consultar( $query );
        if( $insercion )
            $this -> conexion -> Commit();
    }
    
    function getInfoDestinatario( $num_despac = NULL, $num_docume = NULL )
    {
        $mSQL = "SELECT a.num_docume, a.nom_destin, a.num_docume, a.num_docalt
                         FROM tab_despac_destin a
                        WHERE a.num_despac = '".$num_despac."'
                          AND a.num_docume = '".$num_docume."'
                ";
        //echo "<hr>".$mSQL."<hr>";
        return $this -> conexion -> Consultar( $mSQL, "i", TRUE );
    }
}
$remesa =  new Novedad( $this -> conexion );
?>
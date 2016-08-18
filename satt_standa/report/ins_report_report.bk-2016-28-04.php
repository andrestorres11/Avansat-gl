<?php
class Reporte
{
	var $conexion = NULL;
	
	function __construct( $conexion )
	{
		$this -> conexion = $conexion;
		$this -> Menu();
	}
	
	function Menu()
	{
		switch( $_POST[opcion] )
		{
			case "2":
				$this -> Registrar();
				$this -> Formulario();
			break;
			default:
				$this -> Formulario();
			break;
		}
	}
	
	function Registrar()
	{		
		echo "------------------------------------------";
		$fec_actual = date("Y-m-d H:i:s");

		//Codigo del Tercero.
		$cod_tercer = explode( "-" , $_POST[cod_tercer] );
		$cod_tercer = trim( $cod_tercer[0] );
		//Codigo del Puesto de Control.
		$cod_contro = explode( "-" , $_POST[cod_contro] );
		$nom_contro = trim( $cod_contro[1] );
		$cod_contro = trim( $cod_contro[0] );
		//Codigo de la Novedad.
		$cod_noveda = explode( "-" , $_POST[cod_noveda] );
		$nom_noveda = trim( $cod_noveda[1] );
		$cod_noveda = trim( $cod_noveda[0] );
		//Validar Existencias.
		//Validar Transportadora.
		$validar = "SELECT 1
					FROM ".BASE_DATOS.".tab_tercer_tercer 
					WHERE cod_tercer = '$cod_tercer' ";
		
		$validar = new Consulta( $validar, $this -> conexion );      
		$validar = $validar -> ret_matriz( 'a' );
		
		if( !$validar )
		{
			$_POST[cod_tercer] = "";
			$this -> Formulario();			
			die();
		}
		//Validar Puesto de Control
		$validar = "SELECT 1
					FROM ".BASE_DATOS.".tab_genera_contro 
					WHERE cod_contro = '$cod_contro' ";
		
		$validar = new Consulta( $validar, $this -> conexion );      
		$validar = $validar -> ret_matriz( 'a' );
		
		if( !$validar )
		{
			$_POST[cod_contro] = "";
			$this -> Formulario();			
			die();
		}
		//Validar Novedad
		$validar = "SELECT 1
					FROM ".BASE_DATOS.".tab_genera_noveda 
					WHERE cod_noveda = '$cod_noveda' ";
		
		$validar = new Consulta( $validar, $this -> conexion );      
		$validar = $validar -> ret_matriz( 'a' );
		
		if( !$validar )
		{
			$_POST[cod_noveda] = "";
			$this -> Formulario();			
			die();
		}
		//Fin de Validaciones
		$usuario   = $_SESSION["datos_usuario"]["cod_usuari"];
		
		$query = "SELECT MAX( num_repnov ) + 1 AS max_consec 
				  FROM ".BASE_DATOS.".tab_report_noveda a ";
		
		$consult = new Consulta( $query, $this -> conexion );      
		$num_repnov = $consult -> ret_matriz( 'a' );
		$num_repnov = $num_repnov[0][max_consec];
		
		if( !$num_repnov )
			$num_repnov = date( "Ymd" );
		
		$_POST[num_placax] = strtoupper( $_POST[num_placax] );
	
		$insert = "INSERT INTO ".BASE_DATOS.".tab_report_noveda 
					(
						num_repnov , cod_tercer , cod_contro ,
						num_placax , fec_repnov , cod_noveda ,
						obs_repnov , fec_contro , usr_creaci ,
						fec_creaci 
					)
					VALUES 
					(
						'$num_repnov', '$cod_tercer', '$cod_contro', 
						'$_POST[num_placax]', '$_POST[date] $_POST[hora]', '$cod_noveda', 
						'$_POST[obs_repnov]', NOW(), '$usuario', 
						NOW()
					)";
		
		$insert = new Consulta( $insert, $this -> conexion, "BRC" ); 	
		
		$query = "SELECT a.dir_emailx
				  FROM ".BASE_DATOS.".tab_tercer_tercer a
				  WHERE a.cod_tercer = '$cod_tercer' ";
		
		$consult = new Consulta( $query, $this -> conexion );      
		$correo = $consult -> ret_matriz( 'a' );
		
		$correo = $correo[0][dir_emailx];
        
		if( $correo )
		{
			$to = $correo.","."eclfaro@grupooet.com";

			$subject = 'Reporte de Novedad';

			$headers  = 'MIME-Version: 1.0' . "\r\n";
	        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	        $headers .= 'From: seguimientosfaro@eltransporte.org\r\n';

			$message = "";
		
					$message .= "<h2>$subject</h2>";
					$message .= "<b>Fecha Reporte:</b> ".$fec_actual." <br>";
					$message .= "<b>Transportadora:</b> $_POST[cod_tercer]<br>";
					$message .= "<b>Puesto de Control:</b> $_POST[cod_contro]<br>";
					$message .= "<b>Placa:</b> $_POST[num_placax]<br>";
					$message .= "<b>Fecha Novedad:</b> $_POST[date] $_POST[hora]<br>";
					$message .= "<b>Novedad:</b> $_POST[cod_noveda]<br>";
					$message .= "<b>Nro. Verificacion:</b> $num_repnov<br>";
					$message .= "<b>Observacion:</b> $_POST[obs_repnov]<br>";
				
			
            if ( $cod_tercer != "900419270" ){//SI ES "K Logistics".)
                mail( $to, $subject, $message, $headers );
                mail( 'alexander.correa@intrared.net', $subject, $message, $headers );
            }
                
            //if ( $cod_tercer == "900419270" )//SI ES "K Logistics".
            $no_correos = array('9996','9995','111',
                                      '68','50','53','70',
                                      '74','126','52','127',
                                      '96','97','1','28','14',
                                      '27','60','13','62','64',
                                      '49','12','41','9','4',
                                      '124','22','117','99');
            if ($cod_tercer == "900419270" && !in_array( $cod_noveda, $no_correos) )//SI ES "K Logistics" OJO REEMPLAZAR EN EL IF -> 900419270.
            {
				
                $fech_nov = str_replace( "-", "/", $_POST[date] ) ;
                $fech_nov = explode( "/", $fech_nov );
                $fech_nov = $fech_nov[2]."/".$fech_nov[1]."/".$fech_nov[0];	
                
                $select = "SELECT b.nom_carroc, d.abr_tercer, d.num_telmov 
                             FROM ".$base_datos.".tab_vehicu_vehicu a,
                                  ".$base_datos.".tab_vehige_carroc b,
                                  ".$base_datos.".tab_tercer_tercer d
                            WHERE a.num_placax = '".$_POST[num_placax]."' 
                              AND b.cod_carroc = a.cod_carroc 
                              AND a.cod_conduc = d.cod_tercer
                           ";
                $consulta = new Consulta( $select, $this->conexion);
                $vehicul = $consulta -> ret_matriz();
                //$vehicul = $vehicul[0][0];
                
                $mensaje = '
                <div id="mensaje" style="color:#7F7F7F; border:0px; padding:0px; width:700px; text-align:justify;">
                    <img src="https://ap.intrared.net:444/ap/satt_standa/imagenes/klogis/image001.jpg">
                    <div style="color:#7F7F7F; font-family: Arial, sans-serif; font-size: 12pt;">
                        <div style="padding:20px" >
                            
                            <strong>Fecha </strong> ' . $fech_nov . '<br/>
                            <strong>Hora Novedad </strong> ' . $_POST[hora] . '<br/><br/>

                            <strong>Conductor </strong> ' .ucwords( strtolower( $vehicul[0][1] ) ) . '<br/>
                            <strong>Veh&iacute;culo </strong> '.ucwords( strtolower($vehicul[0][0])).'<br/>
                            <strong>Tel&eacute;fono </strong> ' . ucwords( strtolower($vehicul[0][2])) . '<br/>
                            <strong>Placa </strong> ' . strtoupper($_POST[num_placax]) . '<br/><br/>

                             <strong>Puesto de Control </strong> ' . ucwords( strtolower(trim( $nom_contro))) . '<br/><br/>

                            <strong>NOVEDAD: </strong> ' . trim( $nom_noveda ) . '<br/><br/>
                            <strong>Nro. Verificacion: </strong> ' . $num_repnov . '<br/><br/>
                            
                        </div>
                        <img src="https://ap.intrared.net:444/ap/satt_standa/imagenes/klogis/image002.jpg">
                    </div>

                </div>';
                $to .= ", miguel.chavez@klogistics.com.co";
                //mail( $to, $subject, $mensaje, $headers ); 
                //mail( 'jorge.preciado@intrared.net', $subject, $mensaje, $headers ); 
            }
      
		}
    
    $datos_interf = $this -> getInterfData( $cod_tercer, '25' );
    
    if( $datos_interf ) 
    {
      $nom_contro = $this -> getNomContro( $cod_contro );
      $cod_manifi = $this -> getCodManifiFromPlacax( $_POST['num_placax'] );
      $cod_manifi = $cod_manifi == '' ? '9999999': $cod_manifi;
      $aditionalData['cod_tercer'] = $cod_tercer;
      $aditionalData['cod_contro'] = $cod_contro;
      $aditionalData['num_repnov'] = $num_repnov;
      //Se va a enviar NULL el manifiesto puesto que siempre vamos a enviar la placa
      $sendData['nom_usuari'] = $datos_interf['nom_usuari'];
      $sendData['pwd_clavex'] = $datos_interf['clv_usuari'];
      $sendData['cod_manifi'] = '';
      $sendData['fec_noveda'] = date( 'Y-m-d',strtotime( $_POST[date].' '.$_POST[hora] ) );
      $sendData['hor_noveda'] = date( 'H:i:s',strtotime( $_POST[date].' '.$_POST[hora] ) );
      $sendData['nom_contro'] = $nom_contro;
      $sendData['des_observ'] = $_POST['obs_repnov'];
      $sendData['nom_llavex'] = '59a68t7j95s4t96dS2g9A';
      $sendData['fec_pronov'] = date( 'Y-m-d',strtotime( $_POST[date].' '.$_POST[hora] ) );
      $sendData['hor_pronov'] = date( 'H:i:s',strtotime( $_POST[date].' '.$_POST[hora] ) );
      $sendData['num_placax'] = $_POST['num_placax'];
      //Interfaz Intracarga envio de Novedades
      $this -> sendNovedaIntracarga( $sendData, $aditionalData );
    }
		$mensaje =  "Se Registro la Novedad con Exito<br>. Nro. de Verificacion <h3>".$num_repnov."</h3>";
		$mens = new mensajes();
		$mens -> correcto( "Registrar Novedades", $mensaje );
		$_POST = NULL;
	}
  
  //Retorna usuario y contraseña
  function getInterfData( $cod_tercer, $cod_operad )
  {
    $query = "SELECT b.nom_usuari, b.clv_usuari
                FROM ".BASE_DATOS.".tab_interf_parame b
               WHERE b.cod_transp = '".$cod_tercer."'
                 AND b.cod_operad = '".$cod_operad."' ";

    $consulta = new Consulta( $query, $this -> conexion );
    $result = $consulta -> ret_matriz( 'a' );
    $result = $result[0];
    return $result;
  }
  
  //Retorna el nombre del puesto de control dado el codigo
  function getNomContro( $cod_contro )
  {
    $query = "SELECT nom_contro ".
               "FROM ".BASE_DATOS.".tab_genera_contro ".
              "WHERE cod_contro = '".$cod_contro."' ";

    $consulta = new Consulta( $query, $this -> conexion );
    $result = $consulta -> ret_matriz( 'a' );
    $result = $result[0]['nom_contro'];
    return $result;
  }
  
  //Retorna el codigo del manifiesto buscando por la placa un despacho que este en ruta
  function getCodManifiFromPlacax( $num_placax )
  {
    $query = "SELECT a.cod_manifi
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_despac_vehige b
               WHERE b.num_placax = '".$num_placax."' 
                 AND b.num_despac = a.num_despac
                 AND a.fec_salida IS NOT NULL
                 AND a.fec_llegad IS NULL
                 AND a.ind_anulad = 'R'
            ORDER BY a.fec_salida DESC
            LIMIT 1";

    $consulta = new Consulta( $query, $this -> conexion );
    $result = $consulta -> ret_matriz( 'a' );
    $result = $result[0]['cod_manifi'];
    return $result;
  }

  function sendNovedaIntracarga( $sendData, $aditionalData )
  {
      try
      {
      	$data['nom_proces'] = "Interfaz faro - sendNovedaIntracarga";

		    $url_webser =  "https://web10.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl";
		    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

		    $result = $oSoapClient -> __call( "setPCIntracarga", $sendData );
		    $result2 = explode( "; ", $result );
		    $mCodResp = explode( ":", $result2[0] );
		    $mMsgResp = explode( ":", $result2[1] );
		    
		    if( "1000" != trim( $mCodResp[1] ) )
		    {
		      $mMessage = "******** Encabezado ******** \n";
		      $mMessage .= "Fecha de la novedad:".$sendData['fec_noveda'].". \n";
		      $mMessage .= "Hora de la novedad:".$sendData['hor_noveda'].". \n";
		      $mMessage .= "Empresa de transporte: ".$aditionalData["cod_tercer"]." \n";
		      $mMessage .= "Numero de manifiesto: ".$sendData["cod_manifi"]." \n";
		      $mMessage .= "Placa del vehiculo: ".$sendData["num_placax"]." \n";
		      $mMessage .= "Codigo puesto de control: ".$aditionalData["cod_contro"]." \n";
		      $mMessage .= "Nombre puesto de control: ".$sendData["nom_contro"]." \n";
		      $mMessage .= "Observacion.: ".$sendData["des_observ"]." \n";
		      $mMessage .= "******** Detalle ******** \n";
		      $mMessage .= "Modulo: Reporte \n";
		      $mMessage .= "Cod Error: ".$mCodResp[1]." \n";
		      $mMessage .= "Error: ".$mMsgResp[1]." \n";
		      $mMessage .= "Error Detallado: ".$result." \n";
		      mail( "soporte.ingenieros@intrared.net", "Web service Intracarga", $mMessage,'From: soporte.ingenieros@intrared.net' );
		      
		      $mensaje  = "Se Registro la Novedad con Exito<br>Nro. de Verificacion <h3>".$aditionalData["num_repnov"]."</h3>";
		      $mensaje .= "<br><h3>No se pudo reportar la novedad mediante la interfaz en Intracarga</h3>.";
		      $mensaje .= "<br>Error:".$result;
		      $mens = new mensajes();
		      $mens -> advert( "Registrar Novedades", $mensaje );
		      die();
		    }
		  }
      catch( SoapFault $e )
      {
        //----------

        $error = $e -> faultstring;
        $var_errorx = false;
        
        if ( $error ) 
        	$var_errorx = true;
        elseif ( $e -> fault )
					$var_errorx = true;

				if( $var_errorx == true )
				{
		      $mMessage = "******** Encabezado ******** \n";
		      $mMessage .= "Fecha de la novedad:".$sendData['fec_noveda'].". \n";
		      $mMessage .= "Hora de la novedad:".$sendData['hor_noveda'].". \n";
		      $mMessage .= "Empresa de transporte: ".$aditionalData["cod_tercer"]." \n";
		      $mMessage .= "Numero de manifiesto: ".$sendData["cod_manifi"]." \n";
		      $mMessage .= "Placa del vehiculo: ".$sendData["num_placax"]." \n";
		      $mMessage .= "Codigo puesto de control: ".$aditionalData["cod_contro"]." \n";
		      $mMessage .= "Nombre puesto de control: ".$sendData["nom_contro"]." \n";
		      $mMessage .= "Observacion.: ".$sendData["des_observ"]." \n";
		      $mMessage .= "******** Detalle ******** \n";
		      $mMessage .= "Modulo: Reporte \n";
		      $mMessage .= "Cod Error: ". $e -> faultcode ." \n";
		      $mMessage .= "Error: ". $e -> faultstring ." \n";
		      $mMessage .= "Error Detallado: ".$result." \n";
		      mail( "soporte.ingenieros@intrared.net", "Web service Intracarga", $mMessage,'From: soporte.ingenieros@intrared.net' );
		      
		      $mensaje  = "Se Registro la Novedad con Exito<br>Nro. de Verificacion <h3>".$aditionalData["num_repnov"]."</h3>";
		      $mensaje .= "<br><h3>No se pudo reportar la novedad mediante la interfaz en Intracarga</h3>.";
		      $mensaje .= "<br>Error:".$result;
		      $mens = new mensajes();
		      $mens -> advert( "Registrar Novedades", $mensaje );
					die();
		     }
        //----------
      }
      //--------------------------------------


  }
  
	function Formulario()
	{	
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		
		
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/report.js\"></script>\n";
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/homolo.css' type='text/css'>";
		
		$query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
				  FROM ".BASE_DATOS.".tab_tercer_tercer a,
					   ".BASE_DATOS.".tab_tercer_activi b
				  WHERE a.cod_tercer = b.cod_tercer AND
						b.cod_activi = ".COD_FILTRO_EMPTRA."
				  ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$transpor = $consulta -> ret_matriz();
		
		$query = "SELECT a.cod_contro, UPPER( a.nom_contro ) as nom_contro
				  FROM ".BASE_DATOS.".tab_genera_contro a
				  WHERE a.ind_estado = '1' 
				  	AND a.ind_pcpadr = '1' 
				  	AND a.ind_virtua = '0'
				  ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$contro = $consulta -> ret_matriz();


		$query = "SELECT a.cod_noveda, 
						 UPPER( a.nom_noveda ) as nom_noveda 
			        FROM ".BASE_DATOS.".tab_genera_noveda a 
			   LEFT JOIN ".BASE_DATOS.".tab_perfil_noveda b 
					  ON a.cod_noveda = b.cod_noveda 
				   WHERE a.cod_noveda != '9998'
				     /*AND a.ind_manala = 0 */
		             AND a.ind_visibl = '1'";
		$query .= $_SESSION['datos_usuario']['cod_perfil'] == '' ? "" : " AND b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
		$query .= " ORDER BY 2";
		$consulta = new Consulta( $query, $this -> conexion );
		$noveda = $consulta -> ret_matriz();
	
		
		echo '
		<script>
		$(function() {
			var tranportadoras = 
			[';
		
			if( $transpor )
			{
				echo "\"Ninguna\"";
				foreach( $transpor as $row )
				{
					echo ", \"$row[cod_tercer] - $row[abr_tercer]\"";
				}			
			}
		
		echo '];
		
		var contro = 
			[';
			
			echo "\"Ninguno\"";
			
			if( $contro )
			{
				foreach( $contro as $row )
				{
					echo ", \"$row[cod_contro] - $row[nom_contro]\"";
				}			
			}
		
		echo '];
		
		var noveda = 
			[';
			
			echo "\"Ninguno\"";
			
			if( $noveda )
			{
				foreach( $noveda as $row )
				{
					echo ", \"$row[cod_noveda] - ".( $row[nom_noveda] )."\"";
				}			
			}
		
		echo '];
			
			$( "#cod_tercer" ).autocomplete({
				source: tranportadoras,
				delay: 100
			});
			
			$( "#cod_contro" ).autocomplete({
				source: contro,
        minLength: 4, 
				delay: 100
			});
			
			$( "#cod_noveda" ).autocomplete({
				source: noveda,
				delay: 100
			});
			
			
			
			$( "#date" ).datepicker();			
			$( "#hora" ).timepicker();
			
			$.mask.definitions["A"]="[12]";
			$.mask.definitions["M"]="[01]";
		    $.mask.definitions["D"]="[0123]";
			
			$.mask.definitions["H"]="[012]";
		    $.mask.definitions["N"]="[012345]";
		    $.mask.definitions["n"]="[0123456789]";
			
			$( "#date" ).mask("Annn-Mn-Dn");
			$( "#hora" ).mask("Hn:Nn:Nn");

		});
		</script>';
		
		//Formulario.
		$formulario = new Formulario ( "index.php", "post", "Novedades de Reporte", "formulario" );
	    $formulario -> oculto( "window", "central" , 0 );
	    $formulario -> oculto( "opcion", 2 , 0 );
	    $formulario -> oculto( "cod_servic", $_REQUEST[cod_servic] , 0 );
      $formulario -> oculto("dateSystem\" id=\"dateSystemID\" ",date('Y-m-d H:i:s'),0);
		$formulario -> linea( "-Datos Generales", 1, "t2" );//Subtitulo.		
		$formulario -> nueva_tabla();
		
		/*Etiquetas*/
		echo "<th class='celda_titulo2' >Transportadora</th>";
		echo "<th class='celda_titulo2' >Placa</th>";
		echo "<th class='celda_titulo2' >Sitio Seguimiento</th>";		
		echo "<th class='celda_titulo2' >Fecha/Hora Novedad</th>";
		echo "<th class='celda_titulo2' >Novedad</th>";
		echo "</tr>";
		/*Campos*/
		echo "<tr>";
		//Transportadora.
		echo "<td class='celda_info' >";
		echo "<input type='text' class='campo' style='width:98%;' id='cod_tercer' name='cod_tercer' 
				value='$_POST[cod_tercer]'  >";
		echo "</td>";
		//Placa.
		echo "<td class='celda_info' >";
		echo "<input type='text' class='campo' maxlength='6' size='6' id='num_placax' value='$_REQUEST[num_placax]' 
				name='num_placax' >";
		echo "</td>";
		//Puesto de Control.
		echo "<td class='celda_info' >";
		echo "<input type='text' class='campo' style='width:98%;' id='cod_contro' name='cod_contro' value='$_POST[cod_contro]' >";
		echo "</td>";
		//Fecha y Hora.
		$date = date( "Y-m-d" );
		$hora = date( "H:i:s" );
		echo "<td class='celda_info' >";
		echo "<input type='text' class='campo' size='10' id='date' value='$date' name='date' >";
		echo "<input type='text' class='campo' size='10' id='hora' value='$hora' name='hora' >";
		echo "</td>";
		//Novedad.
		echo "<td class='celda_info' >";
		echo "<input type='text' class='campo' style='width:98%;' id='cod_noveda' name='cod_noveda' value='$_POST[cod_noveda]' >";
		echo "</td>";
		
		//Obeservaciones
		echo "</tr>";
		echo "<tr>";
		echo "<th class='celda_titulo2' colspan='5' >Observaci&oacute;n</th>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='celda_info' colspan='5' >";
		echo "<textarea class='campo' style='width:98%;' rows='5' cols='80' id='obs_repnov' name='obs_repnov' >$_POST[obs_repnov]</textarea>";
		echo "</td>";
		
		echo "</tr>";
		echo "<tr>";
		echo "<td class='celda_info' colspan='5' >";
		echo "<input type='button' value='Registrar' class='crmButton small save' onclick='Registrar()' >";
		echo "</td>";
		
		$formulario -> cerrar();
	}
}

$pagina = new Reporte( $this -> conexion );

?>
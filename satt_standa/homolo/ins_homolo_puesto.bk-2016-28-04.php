<?php
ini_set('display_errors', false);
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
		switch( $GLOBALS[opcion] )
		{
			case "3":
				$this -> Registrar();
			break;
			
			case "2":
				$this -> Homologar();
			break;
			
			default:
				$this -> Formulario();
			break;
		}
	}
	
	function getPuestos( $cod_contro )
	{
		$query = "SELECT cod_homolo 
				  FROM ".BASE_DATOS.".tab_homolo_pcxeal  
				  WHERE cod_contro = '$cod_contro' ";

		$consulta = new Consulta( $query, $this -> conexion );
		$result = $consulta -> ret_matriz( 'i' );
		
		return $result;
	}
	
	function Homologar()
	{	
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/homolo/homolo.js\"></script>\n";
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		
		//Definicion de Estilos.
		echo "<style>
				.celda_titulo2
				{
					border-right:1px solid #AAA;
					font-size:12px;
					width:20%;
				}
				
				.celda_info
				{
					text-align:justify;
				}
				
				.campo
				{
					border:1px solid #CCC;		
					text-transform:uppercase;
				}
				
				.info
				{
					border:0px;		
					text-align:center;					
				}			
				
				.ui-autocomplete-loading 
				{ 
					background: white url('../".DIR_APLICA_CENTRAL."/estilos/images/ui-anim_basic_16x16.gif') right center no-repeat; 
				}	
				
				.ui-corner-all
				{
					cursor:pointer;
				}
				
				/*.ui-autocomplete 
				{
					max-height: 200px;
					height: 200px;
					overflow-y: auto;
				}*/
			  </style>";
		
		$query = "SELECT a.cod_contro, UPPER( a.nom_contro ), UPPER( b.nom_ciudad ), a.nom_encarg,
						  a.tel_contro
				  FROM ".BASE_DATOS.".tab_genera_contro a,
					   ".BASE_DATOS.".tab_genera_ciudad b
				  WHERE a.ind_estado = '1' AND
						a.ind_virtua = '0' AND
						a.cod_ciudad = b.cod_ciudad AND
						a.cod_contro = '$GLOBALS[cod_contro]' ";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$homolo = $consulta -> ret_matriz();
		$homolo  = $homolo[0];
			 
		if( !isset( $_POST[nom_contro] ) ) $_POST[nom_contro] = $homolo[1];
		
		if( !isset( $_POST[puesto] ) )
		{
			$puestos = $this -> getPuestos( $GLOBALS[cod_contro] );	
			
			if( $puestos )
			foreach( $puestos as $puesto )
				$_POST[puesto][$puesto[0]] = $puesto[0];
			
		}	
		
		
		
		$query = "SELECT a.cod_contro, UPPER( a.nom_contro ), UPPER( b.nom_ciudad ), a.nom_encarg,
						  a.tel_contro
				  FROM ".BASE_DATOS.".tab_genera_contro a,
					   ".BASE_DATOS.".tab_genera_ciudad b
				  WHERE a.ind_estado = '1' AND
						a.ind_virtua = '0' AND
						a.cod_ciudad = b.cod_ciudad AND
						a.cod_contro != '$GLOBALS[cod_contro]' ";
		
		if( !$_POST[seleccion] )
		{
			if( $_POST[nom_contro] )
				$query .= " AND a.nom_contro LIKE '%$_POST[nom_contro]%' ";
			
			if( $_POST[cod_ciudad] )
				$query .= " AND a.cod_ciudad = '$_POST[cod_ciudad]' ";
		}
		else
		{
			$_POST[nom_contro] = "";
			$_POST[cod_ciudad] = "";
			
			$checkeados = array();
			
			if( $_POST[puesto] )
			{					
				foreach( $_POST[puesto] as $puesto )
				{
					if( $puesto )
						$checkeados[] = $puesto;
				}
				
				$checkeados = implode( ", ", $checkeados );
				
				$query .= " AND a.cod_contro IN ( $checkeados ) ";					
			}		
		}
		
		$query .= " ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$contro = $consulta -> ret_matriz();
		
		$listados = array();//Codigos listados.
		
		if( $contro )
		foreach( $contro as $codigo )
		{
			$listados[] = $codigo[0];
		}
		
		$listados = implode( ", ", $listados );		
		
		if( !$listados ) $listados = $GLOBALS[cod_contro];
		
		$query = "SELECT a.cod_contro
				  FROM ".BASE_DATOS.".tab_genera_contro a,
					   ".BASE_DATOS.".tab_genera_ciudad b
				  WHERE a.ind_estado = '1' AND
						a.ind_virtua = '0' AND
						a.cod_ciudad = b.cod_ciudad AND
						a.cod_contro NOT IN ( $listados ) AND
						a.cod_contro != '$GLOBALS[cod_contro]' ";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$contro2 = $consulta -> ret_matriz();
		
		$query = "SELECT a.cod_ciudad, UPPER( b.nom_ciudad )
				  FROM ".BASE_DATOS.".tab_genera_contro a,
					   ".BASE_DATOS.".tab_genera_ciudad b
				  WHERE a.ind_estado = '1' AND
						a.ind_virtua = '0' AND
						a.cod_ciudad = b.cod_ciudad 
				  GROUP BY 1
				  ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$ciudades = array_merge( array( array( "", "---" ) ), $consulta -> ret_matriz() );			
		
		//Formulario.
		$formulario = new Formulario ( "index.php", "post", "Homologar PC Fisico", "formulario\" id=\"formulario" );
		$formulario -> oculto( "window", "central" );
		$formulario -> oculto( "cod_contro", $GLOBALS[cod_contro] );
		$formulario -> oculto( "opcion\" id=\"opcion", 2 );
		$formulario -> oculto( "cod_servic", $GLOBALS[cod_servic] );

		$formulario -> nueva_tabla();
		$formulario -> linea( "Puesto a Homologar", 1, "t2" );//Subtitulo.	
		
		$formulario -> nueva_tabla();
		$formulario -> linea( "Codigo: $homolo[0]",0,"t");
		$formulario -> linea( "Puesto de Control: $homolo[1]",1,"t");
		$formulario -> linea( "Ciudad: $homolo[2]",0,"t");
		$formulario -> linea( "Encargado: $homolo[3]",0,"t");
		   
		
		$formulario -> nueva_tabla();
		$formulario -> linea( "Filtros", 1, "t2" );//Subtitulo.
		
		
		$formulario -> nueva_tabla();
		$formulario -> texto ( "Puesto de Control: ","text", "nom_contro", 0, 20, 20, "", $_POST[nom_contro], "", "", NULL, NULL );
		$formulario -> lista ( "Ciudad: ", "cod_ciudad", $ciudades, 1, 0 );
		$formulario -> caja ( "Mostrar Chekeados: ", "seleccion", 1, $_POST[seleccion], 0 );
		
		$formulario -> nueva_tabla();
		$formulario -> boton( "Buscar",		"button\" onclick=\"formulario.submit()",0);
		$formulario -> boton( "Homologar", 	"button\" onclick=\"HomologarPuesto()",0);
		
		
		$formulario -> nueva_tabla();
		$formulario -> linea( "Lista de Puestos Fisicos para Homologar", 1, "t2" );//Subtitulo.
		$formulario -> linea( sizeof( $contro )." Puestos Encontrados", 1, "t2" );//Subtitulo.
		
		$formulario -> nueva_tabla();
		echo "<th class='celda_titulo2' >Codigo</th>";
		echo "<th class='celda_titulo2' >Puesto de Control</th>";
		echo "<th class='celda_titulo2' >Ciudad</th>";
		echo "<th class='celda_titulo2' >Encargado</th>";		
		echo "<th class='celda_titulo2' >Tel&eacute;fono</th>";
		echo "</tr>";
		
		$id = 0;
		
		if( $contro )
		foreach( $contro as $row )
		{
			$link = "<a href='index.php?cod_servic=$GLOBALS[cod_servic]&window=central&opcion=2&cod_contro=$row[0]' >$row[0]</a>";
			
			$checked = "";
			
			if( $_POST[puesto][$row[0]] ) $checked = " checked ";
			
			echo "<tr>";
			echo "<td class=celda_info ><input type='checkbox' $checked name='puesto[".$row[0]."]' id='puesto_$id' value=$row[0] > <b>$row[0]</b></td>";
			echo "<td class=celda_info >$row[1]</td>";
			echo "<td class=celda_info >$row[2]</td>";
			echo "<td class=celda_info >$row[3]</td>";
			echo "<td class=celda_info >$row[4]</td>";
			echo "</tr>";
			
			$id++;
		}
		
		if( $contro2  )
		foreach( $contro2 as $codigo )
		{
			$valor = $codigo[0];
			if( !$_POST[puesto][$codigo[0]] ) $valor = "";
			
			$formulario -> oculto( "puesto[".$codigo[0]."]\" id=\"puesto_$id",  $valor );
			$id++;
		}
		
		$formulario -> oculto( "puestos\" id=\"puestos", ( sizeof( $contro ) + sizeof( $contro2 ) ) );
		
		$formulario -> cerrar();
	}	
	
	
	function Registrar()
	{		
					
		$usuario = $_SESSION[datos_usuario][cod_usuari];
		
		$query = "DELETE FROM tab_homolo_pcxeal
				  WHERE cod_contro = '$_POST[cod_contro]' ";
				  
		$delete = new Consulta( $query, $this -> conexion,"BR");
		
		if( $_POST[puesto] )
		{
			foreach( $_POST[puesto] as $puesto )
			{
				if( $puesto )
				{
					$insert = "INSERT INTO tab_homolo_pcxeal 
								(
									cod_contro , cod_homolo , fec_creaci , usr_creaci
								)
								VALUES 
								(
									'$_POST[cod_contro]',  '$puesto',  NOW(),  '$usuario'
								)";
					
					$insert = new Consulta( $insert, $this -> conexion,"R");
					
					
				}
			}
		}
		
		
		//$insercion = new Consulta( "ROLLBACK", $this -> conexion );
		$insercion = new Consulta("COMMIT", $this -> conexion);
		
		$link_a = "<br><b>
					<a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">
					Hacer otra Homologacion</a></b>";

	   $mensaje =  "La Homologacion se realizo exitosamente".$link_a;
	   $mens = new mensajes();
	   $mens -> correcto("HOMOLOGAR PUESTOS DE CONTROL",$mensaje);
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
    //Ruta Web Service Faro (Internamente consume al de Intracarga porque ese WS no se puede consumir desde PHP4).
    $oSoapClient = new soapclient( 'https://ap.intrared.net:444/ap/interf/app/faro/wsdl/faro.wsdl', true ); 
    
    //Consumo Web Service Faro Metodo que consume WS de Intracarga desde PHP 5.
    
    $result = $oSoapClient -> call( "setPCIntracarga", $sendData );
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
  
	function Formulario()
	{	
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/report.js\"></script>\n";
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		
		//Definicion de Estilos.
		echo "<style>
				.celda_titulo2
				{
					border-right:1px solid #AAA;
					font-size:12px;
					width:20%;
				}
				
				.celda_info
				{
					text-align:justify;
				}
				
				.campo
				{
					border:1px solid #CCC;		
					text-transform:uppercase;
				}
				
				.info
				{
					border:0px;		
					text-align:center;					
				}			
				
				.ui-autocomplete-loading 
				{ 
					background: white url('../".DIR_APLICA_CENTRAL."/estilos/images/ui-anim_basic_16x16.gif') right center no-repeat; 
				}	
				
				.ui-corner-all
				{
					cursor:pointer;
				}
				
				/*.ui-autocomplete 
				{
					max-height: 200px;
					height: 200px;
					overflow-y: auto;
				}*/
			  </style>";

		$query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
				  FROM ".BASE_DATOS.".tab_tercer_tercer a,
					   ".BASE_DATOS.".tab_tercer_activi b
				  WHERE a.cod_tercer = b.cod_tercer AND
						b.cod_activi = ".COD_FILTRO_EMPTRA."
				  ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$transpor = $consulta -> ret_matriz();
		
		$query = "SELECT a.cod_contro, UPPER( a.nom_contro ), UPPER( b.nom_ciudad ), a.nom_encarg,
						  a.tel_contro
				  FROM ".BASE_DATOS.".tab_genera_ciudad b,
			  			".BASE_DATOS.".tab_genera_contro a
					   LEFT JOIN ".BASE_DATOS.".tab_homolo_pcxeal c
					   ON a.cod_contro = c.cod_homolo
				  WHERE a.ind_estado = '1' AND
						a.ind_virtua = '0' AND
						a.cod_ciudad = b.cod_ciudad AND
						c.cod_homolo IS NULL ";
		
		if( $_POST[nom_contro] )
			$query .= " AND a.nom_contro LIKE '%$_POST[nom_contro]%' ";
		
		if( $_POST[cod_ciudad] )
			$query .= " AND a.cod_ciudad = '$_POST[cod_ciudad]' ";
		
		$query .= " ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$contro = $consulta -> ret_matriz();
		
		$query = "SELECT a.cod_ciudad, UPPER( b.nom_ciudad )
				  FROM ".BASE_DATOS.".tab_genera_contro a,
					   ".BASE_DATOS.".tab_genera_ciudad b
				  WHERE a.ind_estado = '1' AND
						a.ind_virtua = '0' AND
						a.cod_ciudad = b.cod_ciudad
				  GROUP BY 1
				  ORDER BY 2";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$ciudades = array_merge( array( array( "", "---" ) ), $consulta -> ret_matriz() );
			
		//Formulario.
		$formulario = new Formulario ( "index.php", "post", "Homologar PC Fisico", "formulario" );
		$formulario -> oculto( "window", "central" );
		$formulario -> oculto( "opcion", 1 );
		$formulario -> oculto( "cod_servic", $GLOBALS[cod_servic] );	
		
		$formulario -> nueva_tabla();
		$formulario -> linea( "Filtros", 1, "t2" );//Subtitulo.
		
		
		$formulario -> nueva_tabla();
		$formulario -> texto ("Puesto de Control: ","text", "nom_contro", 0, 20, 20, "", $GLOBALS[nom_contro], "", "", NULL, NULL );
		$formulario -> lista ("Ciudad: ", "cod_ciudad", $ciudades, 0, 0 );
		
		$formulario -> nueva_tabla();
		$formulario -> boton("Aceptar","button\" onclick=\"formulario.submit()",0);
		
		
		$formulario -> nueva_tabla();
		$formulario -> linea( "Lista de Puestos Fisicos", 1, "t2" );//Subtitulo.
		$formulario -> linea( sizeof( $contro )." Puestos Encontrados", 1, "t2" );//Subtitulo.
		
		$formulario -> nueva_tabla();
		echo "<th class='celda_titulo2' >Codigo</th>";
		echo "<th class='celda_titulo2' >Homologados</th>";
		echo "<th class='celda_titulo2' >Puesto de Control</th>";
		echo "<th class='celda_titulo2' >Ciudad</th>";
		echo "<th class='celda_titulo2' >Encargado</th>";		
		echo "<th class='celda_titulo2' >Tel&eacute;fono</th>";
		echo "</tr>";
		
		if( $contro )
		foreach( $contro as $row )
		{
			$link = "<a href='index.php?cod_servic=$GLOBALS[cod_servic]&window=central&opcion=2&cod_contro=$row[0]' >$row[0]</a>";
			$homolo = $this -> getHomolo( $row[0] );
			
			$bg = "";
			
			if( !$homolo ) $homolo = "";
			else
				$bg = "background-color:#360; color:#fff";
			
			
			echo "<tr>";
			echo "<td class=celda_info ><b>$link</b></td>";
			echo "<td class=celda_info style='text-align:center; $bg' >".$homolo."</td>";
			echo "<td class=celda_info >$row[1]</td>";
			echo "<td class=celda_info >$row[2]</td>";
			echo "<td class=celda_info >$row[3]</td>";
			echo "<td class=celda_info >$row[4]</td>";
			echo "</tr>";
		}
		
		$formulario -> cerrar();
	}
	
	function getHomolo( $cod_contro )
	{
		$query = "SELECT COUNT( 1 ) 
				  FROM ".BASE_DATOS.".tab_homolo_pcxeal  
				  WHERE cod_contro = '$cod_contro' ";

		$consulta = new Consulta( $query, $this -> conexion );
		$result = $consulta -> ret_matriz( 'i' );
		
		return $result[0][0];
	}
}

$pagina = new Reporte( $this -> conexion );

?>
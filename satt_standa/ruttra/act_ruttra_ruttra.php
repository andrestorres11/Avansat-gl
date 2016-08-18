 <?php
class Actualizar
{
	var $conexion,
		$cod_aplica,
		$usuario;
	
	function __construct( $co )
	{
		$this -> conexion = $co;
		$this -> principal();
	}
	
	function principal()
	{
		switch( $GLOBALS[opcion] )
		{
			case "insert":
				$this -> ActualizarRuta();
			break;
			case "listar":
				$this -> Listar();
			break;			
			case "ruta":
				$this -> ListarRutas();
			break;
			default:
				$this -> Buscar();
			break;
		}
	}
	
	function ActualizarRuta()
	{		
		$fec_actual = date("Y-m-d H:i:s");
		$asigna = $GLOBALS[asigna];
		
		$_POST[usr_creaci] = $_SESSION[datos_usuario][cod_usuari];
		
		$query = "DELETE FROM ".BASE_DATOS.".tab_genera_ruttra
  		   		  WHERE cod_rutasx = '$GLOBALS[cod_rutasx]' AND
  		   			    cod_transp = '$GLOBALS[cod_transp]' ";

		$insercion = new Consulta($query, $this -> conexion,"BR");
		
		for($i = 0; $i < $GLOBALS[maximo]; $i++)
		{
			if($asigna[$i])
			{
				$query = "INSERT INTO ".BASE_DATOS.".tab_genera_ruttra
						  (
							cod_rutasx, cod_contro, cod_transp,
							usr_creaci, fec_creaci 
						  )
						  VALUES 
						  (
							".$GLOBALS[cod_rutasx].", ".$asigna[$i].", '".$GLOBALS[cod_transp]."',
							'$_POST[usr_creaci]', NOW()
						  ) ";
				
				$insercion = new Consulta($query, $this -> conexion,"R");
			}
		}
		
		if($insercion = new Consulta("COMMIT", $this -> conexion))
		{
			$link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otra Ruta</a></b>";
			
			$mensaje =  "Se Actualizo a la Ruta con Exito".$link_a;
			$mens = new mensajes();
			$mens -> correcto("ACTUALIZAR RUTA",$mensaje);
		}
	}
	
	function getPuestosControl( $cod_rutasx )
	{
	    $query = "SELECT IF( e.ind_virtua = 0, e.nom_contro, CONCAT( e.nom_contro, ' (VIRTUAL)' ) ) AS nom_contro, d.val_duraci, e.nom_encarg, 
	             LEFT( e.dir_contro, 100 ) , e.tel_contro, d.cod_contro
	          FROM ".BASE_DATOS.".tab_genera_rutasx a,
	             ".BASE_DATOS.".tab_genera_rutcon d,
	             ".BASE_DATOS.".tab_genera_contro e
	          WHERE a.cod_rutasx = d.cod_rutasx AND
	            d.cod_contro = e.cod_contro AND
	            a.cod_rutasx = '$cod_rutasx' AND
	            d.ind_estado = '1'
	          ORDER BY 2 ";
		
		$control = new Consulta( $query, $this -> conexion );
		$control = $control -> ret_matriz();
		
		return $control;
	}
	
	function Listar()
	{
		$info = $this -> GetInfo( $_GET[cod_transp], $_GET[cod_rutasx] );
		echo "<script language=\"JavaScript\" src=\"../satt_standa/js/ruttra.js\"></script>\n";
		$formulario = new Formulario ( "index.php", "post", "Actualizar Ruta Asignada", "formulario\" id=\"formularioID" );
		$formulario -> nueva_tabla();
		$formulario -> linea("Informacion General",0,"t2");
		
		$formulario -> nueva_tabla();
		$formulario -> linea("NIT:",0,"t");
		$formulario -> linea( $info[cod_transp],0,"i");
		$formulario -> linea("Transportadora:",0,"t");
		$formulario -> linea( $info[abr_tercer],1,"i");
		
		$formulario -> linea("Dirección:",0,"t");
		$formulario -> linea( $info[dir_domici],0,"i");
		$formulario -> linea("Telefono:",0,"t");
		$formulario -> linea( $info[num_telef1],1,"i");
		
		$formulario -> linea("Codigo Ruta:",0,"t");
		$formulario -> linea( $info[cod_rutasx],0,"i");
		$formulario -> linea("Ruta:",0,"t");
		$formulario -> linea( $info[nom_rutasx],1,"i");
		
		$formulario -> linea("Origen:",0,"t");
		$formulario -> linea( $info[origen],0,"i");
		$formulario -> linea("Destino:",0,"t");
		$formulario -> linea( $info[destino],1,"i");
		
		$puestos = $this -> getPuestosControl( $_GET[cod_rutasx] );
		
		$formulario -> nueva_tabla();
		$formulario -> linea("Puestos de Control",0,"t2");
		
		$formulario -> nueva_tabla();
		$formulario -> linea("",0,"t", "5%" );
		$formulario -> linea("",0,"t", "5%" );
		$formulario -> linea("Nombre",0,"t","10%");
		$formulario -> linea("Min - Origen",0,"t","10%");
		$formulario -> linea("Encargado",0,"t","20%");
		$formulario -> linea("Direcci&oacute;n",0,"t","30%");
		$formulario -> linea("T&eacute;lefono",1,"t","20%");
		
		for( $i = 0; $i < sizeof( $puestos ); $i++ )
		{
			if( $puestos[$i][5] == CONS_CODIGO_PCLLEG )
			{
				$formulario -> caja("","asigna[$i]\" disabled ", "".$puestos[$i][5]."", 1,0 );
				$formulario -> oculto("asigna[$i]",$puestos[$i][5],0);
			}
			else
			{
				$checked = $this -> GetCheck( $_GET[cod_transp], $_GET[cod_rutasx], $puestos[$i][cod_contro] );		
				$formulario -> caja("","asigna[$i]",$puestos[$i][5], $checked ,0 );
			}
			
			$formulario -> linea($puestos[$i][0],0,"i");
			$formulario -> linea($puestos[$i][1],0,"i");
			$formulario -> linea($puestos[$i][2],0,"i");
			$formulario -> linea($puestos[$i][3],0,"i");
			$formulario -> linea($puestos[$i][4],1,"i");
		}
		
		$formulario -> nueva_tabla();
		$formulario -> oculto("maximo","".sizeof($puestos)."",0);
		$formulario -> oculto("opcion","insert",0);
		$formulario -> oculto("cod_rutasx",$GLOBALS[cod_rutasx],0);
		$formulario -> oculto("cod_transp",$GLOBALS[cod_transp],0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
		$formulario -> boton("Actualizar","button\" onClick=\"ActualizarRuta()",1);	
		$formulario -> cerrar();
	}
	
	function GetCheck( $cod_transp, $cod_rutasx, $cod_contro )
	{
		$query = "SELECT 1
				  FROM ".BASE_DATOS.".tab_genera_ruttra a
				  WHERE a.cod_transp = '$cod_transp' AND
						a.cod_rutasx = '$cod_rutasx' AND
						a.cod_contro = '$cod_contro' ";
		
		$select = new Consulta( $query, $this -> conexion);
		$select = $select -> ret_matriz();
		
		if( $select ) return 1;
		return 0;
	}
	
	function ListarRutas()
	{
		$rutas = $this -> GetRutasAsignadas( $_GET[cod_transp] );
		$num_rutas = sizeof( $rutas );
		
		$formulario = new Formulario ( "index.php", "post", "Listado de Rutas por Empresa", "formulario" );
		$formulario -> nueva_tabla();
		$formulario -> linea("Se Encontraron $num_rutas Asignadas a la Empresa.",0,"t2");		
		
		$formulario -> nueva_tabla();
		$formulario -> linea("Codigo Ruta",0,"t", "10%" );
		$formulario -> linea("Ruta",0,"t", "30%" );
		$formulario -> linea("Ciudad Origen",0,"t","20%");
		$formulario -> linea("Ciudad Destino",1,"t","20%");
		
		if( $rutas )
		foreach( $rutas as $row )
		{
			$link = "<a href='index.php?cod_servic=$GLOBALS[cod_servic]&window=central&cod_rutasx=$row[0]&opcion=listar&cod_transp=$_GET[cod_transp]' >$row[0]</a>";
			
			$formulario -> linea($link ,0,"i");
			$formulario -> linea($row[1],0,"i");
			$formulario -> linea($row[2],0,"i");
			$formulario -> linea($row[3],1,"i");
		}

		$formulario -> cerrar();
	}
	
	function GetInfo( $cod_transp, $cod_rutasx )
	{
		$query = "SELECT a.cod_transp, a.cod_rutasx, 
						 UPPER( b.nom_rutasx ) as nom_rutasx, 
						 UPPER( c.nom_ciudad ) as origen, 
						 UPPER( d.nom_ciudad ) as destino,
						 UPPER( e.abr_tercer ) as abr_tercer, e.dir_domici, e.num_telef1
				  FROM ".BASE_DATOS.".tab_genera_ruttra a,
					   ".BASE_DATOS.".tab_genera_rutasx b,
					   ".BASE_DATOS.".tab_genera_ciudad c,
					   ".BASE_DATOS.".tab_genera_ciudad d,
					   ".BASE_DATOS.".tab_tercer_tercer e
				  WHERE a.cod_rutasx = b.cod_rutasx AND
						b.cod_ciuori = c.cod_ciudad AND
						b.cod_paiori = c.cod_paisxx AND
						b.cod_depori = c.cod_depart AND
						b.cod_ciudes = d.cod_ciudad AND
						b.cod_paides = d.cod_paisxx AND
						b.cod_depdes = d.cod_depart AND
						a.cod_transp = e.cod_tercer AND
						a.cod_transp = '$cod_transp' AND
						a.cod_rutasx = '$cod_rutasx'
				  GROUP BY a.cod_rutasx ";		
		
		$select = new Consulta( $query, $this -> conexion);
		$select = $select -> ret_matriz();
		$select = $select[0];
		
		return $select;
	}
	
	function GetRutasAsignadas( $cod_transp )
	{
		$query = "SELECT a.cod_rutasx, UPPER( b.nom_rutasx ), 
						 UPPER( c.nom_ciudad ), 
						 UPPER( d.nom_ciudad )
				  FROM ".BASE_DATOS.".tab_genera_ruttra a,
					   ".BASE_DATOS.".tab_genera_rutasx b,
					   ".BASE_DATOS.".tab_genera_ciudad c,
					   ".BASE_DATOS.".tab_genera_ciudad d
				  WHERE a.cod_rutasx = b.cod_rutasx AND
						b.cod_ciuori = c.cod_ciudad AND
						b.cod_paiori = c.cod_paisxx AND
						b.cod_depori = c.cod_depart AND
						b.cod_ciudes = d.cod_ciudad AND
						b.cod_paides = d.cod_paisxx AND
						b.cod_depdes = d.cod_depart AND
						a.cod_transp = '$cod_transp' 
				  GROUP BY a.cod_rutasx
				  ORDER BY b.nom_rutasx ";
		
		$select = new Consulta( $query, $this -> conexion);
		$select = $select -> ret_matriz();	
		
		return $select;
	}
	
	function GetTransportadorasAsignadas()
	{
		$query = "SELECT a.cod_transp, UPPER( b.abr_tercer ), b.dir_domici,
						 b.num_telef1, c.nom_ciudad
				  FROM ".BASE_DATOS.".tab_genera_ruttra a,
					   ".BASE_DATOS.".tab_tercer_tercer b,
					   ".BASE_DATOS.".tab_genera_ciudad c
				  WHERE a.cod_transp = b.cod_tercer AND
						b.cod_ciudad = c.cod_ciudad 
				  GROUP BY a.cod_transp 
				  ORDER BY b.abr_tercer";
		
		$select = new Consulta( $query, $this -> conexion);
		$select = $select -> ret_matriz();
		
		return $select;
	}
	
	function Buscar()
	{
		//Transportadoras con rutas asignadas.
		$transportadoras = $this -> GetTransportadorasAsignadas();
		$num_transp = sizeof( $transportadoras );		
		
		$formulario = new Formulario ( "index.php", "post", "Listado de Empresas con Rutas Asignadas", "formulario" );
		$formulario -> nueva_tabla();
		$formulario -> linea("Se Encuentras $num_transp Empresas con Rutas Asignadas.",0,"t2");		
		
		$formulario -> nueva_tabla();
		$formulario -> linea("NIT",0,"t", "10%" );
		$formulario -> linea("Transportadora",0,"t", "30%" );
		$formulario -> linea("Direccion",0,"t","20%");
		$formulario -> linea("Telefono",0,"t","20%");
		$formulario -> linea("Ciudad",01,"t","20%");
		
		if( $transportadoras )
		foreach( $transportadoras as $row )
		{
			$link = "<a href='index.php?cod_servic=$GLOBALS[cod_servic]&window=central&cod_transp=$row[0]&opcion=ruta' >$row[0]</a>";
			
			$formulario -> linea($link ,0,"i");
			$formulario -> linea($row[1],0,"i");
			$formulario -> linea($row[2],0,"i");
			$formulario -> linea($row[3],0,"i");
			$formulario -> linea($row[4],1,"i");
		}

		$formulario -> cerrar();
	}
}

$proceso = new Actualizar( $this -> conexion );

?>
 <?

ini_set('display_errors', true);  
error_reporting(E_ALL & ~E_NOTICE);

 class Insertar
 {
	var $ciudad = NULL;
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
        	case "insert":
				$this -> Registrar();
			break;
            default:
                $this -> Formulario();
            break;
        }
	}
	
	function Registrar()
	{
		$_POST[usr_creaci] = $_SESSION[datos_usuario][cod_usuari];
		
		$insert = " INSERT INTO ".BASE_DATOS.".tab_genera_ciudad 
					(
						cod_paisxx , cod_depart , cod_ciudad ,
						nom_ciudad , abr_ciudad , ind_estado ,
						val_icaxxx , usr_creaci , fec_creaci 
					)
					VALUES 
					(
						'$_POST[cod_paisxx]', '$_POST[cod_depart]', '$_POST[cod_ciudad]', 
						'$_POST[nom_ciudad]', '$_POST[abr_ciudad]', '$_POST[cod_estado]', 
						'$_POST[val_icaxxx]', '$_POST[usr_creaci]', NOW()
					)";
		
		$insert = new Consulta( $insert, $this -> conexion, "BRC" );
		
		$mensaje =  "<h3>La Ciudad $_POST[nom_ciudad] se Reqistro Exitosamente.</h3>";
		$mens = new mensajes();
		$mens -> correcto( "Registrar Novedades", $mensaje );
	}

    function Formulario()
    {
    	echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/ciudad.js\"></script>\n";	
		
        $formulario = new Formulario( "index.php", "post", "Registrar Ciudad", "formulario" );
        $formulario -> linea( "Insertar Nueva Ciudad", 0, "t2");
		$formulario -> oculto( "window", "central", 0);
		$formulario -> oculto( "cod_servic", $GLOBALS["cod_servic"], 0);
		$formulario -> oculto( "opcion", "insert", 0);
		$formulario -> oculto( "cod_paisxx", 3, 0);//Codigo del Pais 3 = Colombia.
		
		$formulario -> nueva_tabla();
		
		$cod_ciudad = $this -> getCodCiudad();
		
		$formulario -> texto ( "Codigo:", "text", "cod_ciudad\" readonly", 0, 15, 15, NULL, $cod_ciudad, NULL, 1 );
		$formulario -> lista( "Departamento:", "cod_depart", $this -> getDepartamentos( $_POST[cod_depart] ), 1, 0 );
		
		$formulario -> texto ( "Nombre:", "text", "nom_ciudad", 0, 30, 50, $_POST[nom_ciudad], "", NULL, 1 );
		$formulario -> texto ( "Abreviatura:", "text", "abr_ciudad", 1, 30, 50, $_POST[abr_ciudad], "", NULL, 1 );
		
		$formulario -> caja( "Estado:", "cod_estado", 1, 1, 0 );
		$formulario -> texto ( "ICA:", "text", "val_icaxxx", 0, 5, 5, "", "", NULL, 1 );
		
		$formulario -> nueva_tabla();
		$formulario -> botoni("Aceptar","RegistrarCiudad()",0);
		$formulario -> botoni("Borrar","formulario.reset()",0);
		$formulario -> cerrar();
    }
	
	function getCodCiudad()
	{
		$query = "SELECT ( MAX( cod_ciudad ) + 1 ) as cod_ciudad 
				  FROM ".BASE_DATOS.".tab_genera_ciudad ";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$select = $consulta -> ret_matriz();
		
		return $select[0][cod_ciudad];
	}
	
	function getDepartamentos()
	{
		$null = array( array( "", "---" ) );
		
		$query = "SELECT cod_depart, nom_depart
				  FROM ".BASE_DATOS.".tab_genera_depart
				  WHERE cod_paisxx = '3'";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$select = $consulta -> ret_matriz();
		
		$select = array_merge( $null, $select );
		
		return $select;
	}
 }
 
 $pagina = new Insertar( $this -> conexion );
 
 ?>
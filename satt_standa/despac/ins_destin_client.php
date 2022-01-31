<?php
/*ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/

class InsertDestin
{
	var $conexion,
	$cod_aplica,
	$usuario;

	function __construct($co, $us, $ca)
	{
		$this -> conexion = $co;
		$this -> usuario = $us;
		$this -> cod_aplica = $ca;
		InsertDestin::principal();
	}

	function principal()
	{
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ins_destin_client.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

		switch($_REQUEST[opcion])
		{
			case "1":
				InsertDestin::insert();
				break;

			default:
				InsertDestin::Formulario();
				break;
		}//FIN SWITCH
	}//FIN FUNCION PRINCIPAL

/*! \fn: Formulario
 *  \brief: pinta el formulario
 *  \author: Ing. Miguel Romero
 *	\date: 08/05/2015
 *	\date modified: 08/05/2015
 */


	function Formulario(){

/*		echo "<pre>";
		print_r($_REQUEST);
		print_r($_SESSION);
		echo "</pre>";*/

			$formulario = new Formulario("index.php", "post", "Insercion de clientes(destinatarios corona) ", "form_item", "", "", "100%");

			$formulario -> nueva_tabla();
			$formulario -> linea("Datos del Cliente", 1, "t1" );
			$formulario -> nueva_tabla();
			$formulario -> texto("Codigo", "text", "cod_client\" onkeypress=\"return justNumbers(event);", 0, 11, 100, "" );
			$formulario -> texto("Nombre", "text", "nom_client", 1, 20, 100, "" );
			$formulario -> nueva_tabla();
			$formulario -> boton("Guardar", "button\" onclick=\"registrar();", 0);

			$formulario -> oculto("window","central",0);
			$formulario -> oculto("opcion\"  id=\"opcionID",99,0);
			$formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);

		 	$formulario -> cerrar();
	}

	/*! \fn: insert
	 *  \brief: Inserta los Mamafokas datos en la base de datos
	 *  \author: Ing. Miguel Romero
	 *	\date: 08/05/2015
	 *	\date modified: 08/05/2015
	 */
	

	function insert(){

/*		echo "<pre>";
		print_r($_REQUEST);
		print_r($_SESSION);
		echo "</pre>";*/
		
		$mens = new mensajes();
		$mQuerySelect = "SELECT cod_client 
							FROM 
							".BASE_DATOS.".tab_destin_client
							 WHERE cod_client = '".$_REQUEST['cod_client']."' ";

		$consulta = new Consulta($mQuerySelect, $this -> conexion);
		$mData = $consulta-> ret_arreglo();


		if ($mData[0] != $_REQUEST['cod_client'] ){


			$mQueryInsert = "INSERT INTO ".BASE_DATOS.".tab_destin_client(
								cod_client,
								nom_client,
								usr_creaci,
								fec_creaci
								)
	                 			VALUES 
	                 			('".$_REQUEST['cod_client']."', '".$_REQUEST['nom_client']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";

			if($consulta = new Consulta($mQueryInsert, $this -> conexion)){
			
				$mens -> correcto("INGRESAR CLIENTE","El Cliente se ha Registrado");
			}
		}
		else{
			$mens ->  error("INGRESAR CLIENTE","El Cliente ya se ha Registrado Previamente");			
		}
	}
}//FIN CLASE InsertDestin


$proceso = new InsertDestin($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
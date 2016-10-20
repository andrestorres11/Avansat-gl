<?php
#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

class ActualizarCliente
{
	var  $conexion,
	$cod_aplica,
	$usuario;

	function __construct($co, $us, $ca)
	{
		$this -> conexion = $co;
		$this -> usuario = $us;
		$this -> cod_aplica = $ca;
		ActualizarCliente::principal();
	}

	function principal()
	{
		include_once( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/funcitons.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/act_destin_client.js\"></script>\n";

		switch($_REQUEST[opcion])
		{
			case "1":
				ActualizarCliente::formActua();
				break;
			case "2":
				ActualizarCliente::actualizar();
				break;

			default:
				ActualizarCliente::Formulario();
				break;
		}//FIN SWITCH
	}//FIN FUNCION PRINCIPAL

	function Formulario(){
		$query = "SELECT a.cod_client, a.nom_client
					FROM tab_destin_client a
						WHERE 1=1 ";


		$formulario = new Formulario ("index.php","post","Actualizar Usuarios","formData");
		$_SESSION["queryXLS"] = $query;
		$list = new DinamicList($this -> conexion, $query, 1 );
		$list -> SetClose('no');
		$list -> SetHeader("Codigo del Cliente", "field:a.cod_client; type:link; onclick:editar( );  ");
		$list -> SetHeader("Nombre del Cliente", "field:a.nom_client;");
		$list -> SetHidden("cod_client", "0" );
		$list -> Display( $this -> conexion );

		$_SESSION["DINAMIC_LIST"] = $list;
		echo "<td>";
		echo $list-> GetHtml();
		echo "</td>";

		$formulario -> oculto("cod_client\" id=\"cod_client","",0);

		$formulario -> oculto("window","central",0);
		$formulario -> oculto("opcion\"  id=\"opcionID",99,0);
		$formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);

		$formulario -> cerrar();
	}

	function formActua(){


		$cod_client = $_REQUEST['cod_client'];

		$mQuerySelect = "SELECT a.cod_client, a.nom_client
							FROM tab_destin_client a
							WHERE a.cod_client = '" .$cod_client. "' ";

		$consulta = new Consulta($mQuerySelect, $this -> conexion);
		$mData = $consulta-> ret_arreglo();

		$formulario = new Formulario("index.php", "post", "Insercion de clientes(destinatarios corona) ", "form_item", "", "", "100%");

		$formulario -> nueva_tabla();
		$formulario -> linea("Datos del Cliente", 1, "t1" );
		$formulario -> nueva_tabla();
		$formulario -> texto("Codigo", "text", "cod_client\"  readonly=readonly value=\"$mData[cod_client]", 0, 11, 20, "" );
		$formulario -> texto("Nombre", "text", "nom_client\"  value=\"$mData[nom_client]", 1, 20, 20, "" );
		$formulario -> nueva_tabla();
		$formulario -> boton("Guardar", "button\" onClick=\"actualizar();", 0);

		$formulario -> oculto("window","central",0);
		$formulario -> oculto("opcion\"  id=\"opcionID",99,0);
		$formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);

	 	$formulario -> cerrar();
	
	}

	function actualizar(){


		$mens = new mensajes();
		$mQueryInsert = "UPDATE ".BASE_DATOS.".tab_destin_client
								SET nom_client = '".$_REQUEST['nom_client']."' 
								WHERE cod_client = '" . $_REQUEST['cod_client'] . "'";
								

		if($consulta = new Consulta($mQueryInsert, $this -> conexion)){
			
			$mens -> correcto("INGRESAR CLIENTE","El Cliente se ha Registrado");
		}

	}

}//FIN CLASE ActualizarCliente


$proceso = new ActualizarCliente($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
<?php
#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

class ListarClientes
{
	var  $conexion,
	$cod_aplica,
	$usuario;

	function __construct($co, $us, $ca)
	{
		$this -> conexion = $co;
		$this -> usuario = $us;
		$this -> cod_aplica = $ca;
		ListarClientes::principal();
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

		ListarClientes::Formulario();

	}//FIN FUNCION PRINCIPAL

	function Formulario(){

		$query = "SELECT a.cod_client, a.nom_client 
					FROM tab_destin_client a
						WHERE 1=1 ";


		$formulario = new Formulario ("index.php","post","Imprimir Plan de Ruta","form\" id=\"formID");
		$_SESSION["queryXLS"] = $query;
		$list = new DinamicList($this -> conexion, $query, 1 );
		$list -> SetClose('no');
		$list -> SetHeader("Codigo del Cliente", "field:a.cod_client;");
		$list -> SetHeader("Nombre del Cliente", "field:a.nom_client;");

		$list -> Display( $this -> conexion );

		$_SESSION["DINAMIC_LIST"] = $list;
		echo "<td>";
		echo $list-> GetHtml();
		echo "</td>";

		$formulario -> cerrar();
	}

}//FIN CLASE ListarClientes


$proceso = new ListarClientes($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>	
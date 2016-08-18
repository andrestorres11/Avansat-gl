<?
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
class Login
{
	var $conexion = NULL;
	var $mensaje = "";
	
	function Login( $conexion )
	{
		$this -> conexion = $conexion;
	
		switch( $_POST[option] )
		{
			case "in":
				$this -> Validar();
			break;
			
			default:
				$this -> Formulario();
			break;
		}
	}
	
	function Validar()
	{		
		$_POST[pass] = str_replace( "'","", $_POST[pass] );
		$_POST[user] = str_replace( "'","", $_POST[user] );
		
		$_POST[pass] = base64_encode( $_POST[pass] );
		
		$query = "SELECT a.cod_usuari, a.clv_usuari, a.nom_usuari, 	
						 a.usr_emailx, a.cod_perfil, a.cod_inicio
				  FROM  tab_genera_usuari a 
				  WHERE a.cod_usuari = '$_POST[user]' AND
				  		a.clv_usuari = '$_POST[pass]' ";
		
		$validar = $this -> conexion -> Consultar( $query, "a" );
		
		if( $validar )
		{
			$_SESSION[satt_movil] = $validar;
			header( "location:index.php" );
		}
		else
		{
			$this -> mensaje = "El <b>Usuario</b> y <b>Clave</b> no se Encuentran Registrados.";
			$this -> Formulario();
		}		
	}
	
	function Formulario()
	{
		?> 
		<div style='text-align:center' >
			<form method="post" action="index.php">
				<table align=center>
					<tr>
						<td align="right" ><label><b>Usuario:</b></label></td>
						<td align="left" ><? echo "<input type='text' class='campo' name='user' value='$_POST[user]'  >"; ?></td>
					</tr>
					<tr>
						<td align="right" ><label><b>Clave:</b></label></td>
						<td align="left" ><? echo "<input type='password' class='campo' name='pass' value=''  >"; ?></td>
					</tr>
					<tr>
						<td align="center" colspan="2" >
							<input type="submit" value="Ingresar" >
						</td>
					</tr>
				</table>
				<input type="hidden" name="option" value="in" >
			</form>        
        </div>        
        <?
	}
}

$login = new Login( $this -> conexion );

?>


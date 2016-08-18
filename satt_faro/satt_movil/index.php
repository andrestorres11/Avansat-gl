<? include( "header.php" ); ?>
<div id="body" >
<?

class Index
{
	var $conexion = null;
	
	function Index()
	{
		$this -> conexion = new Conexion( "bd10.intrared.net:3306", "satb_movil", "satb_movil1", "satt_faro", "" );
		
		if( !$_SESSION[satt_movil] )

			include( "login.php" );
		else
		{	
			include( "novedad.php" );
		}
	}
}

$index =  new Index();

?>
</div>
<? include( "footer.php" ); ?>
            

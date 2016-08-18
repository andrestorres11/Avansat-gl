<?PHP
include( "header.php" ); 

echo '<div id="body" >';

class Index
{
    var $conexion = null;

    function Index()
    {
        //$this -> conexion = new Conexion( "localhost:3306", "satb_movil", "satb_movil1", "satt_faro", "" );
        //$this -> conexion = new Conexion( "bd10.intrared.net:3306", "satt_faro", "faro", "satt_faro", "" );
        $this -> conexion = new Conexion( "bd10.intrared.net:3306", "esferas_huella", "Hu3ll@5_35f3r@5", "satt_faro", "" );
        
        if( !$_SESSION[satt_movil] )
            include( "login.php" );
        else
        {
            if( $_SESSION[satt_movil][cc] == 0 )
                include( "novedad.php" );
            else
                include( "login.php" );
        }
    }
}
$index =  new Index();

echo '</div>';
include( "footer.php" ); 
?>
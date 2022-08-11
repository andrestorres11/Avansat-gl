<? include( "header.php" ); ?>
<div id="body" >
    <?

    class Index {

        var $conexion = null;
        private static $cBD;

        function __construct() {
            $mHost = $_SERVER['HTTP_HOST'];
            $mHost = explode('.', $mHost);

            switch ($mHost[0]) {
                case 'web7':      self::$cBD = "bd7.intrared.net:3306";  break;
                case 'web13':     self::$cBD = "bd13.intrared.net:3306"; break;
                case 'avansatgl': self::$cBD = "aglbd.intrared.net";     break;
                default:          self::$cBD = "demo.intrared.net";      break;
            }
            
            $this->conexion = new Conexion(self::$cBD, "satb_movil", "satb_movil1", "satt_faro", "");

            if (!$_SESSION[satt_movil]){
                include( "login.php" );
            } else {
                include( "novedad.php" );
            }
        }

    }

    $index = new Index();
    ?>
</div>
    <? include( "footer.php" ); ?>
            

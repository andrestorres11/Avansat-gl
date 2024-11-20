<?php  
class executeDeletePDF
{
  var $db4 = NULL;
  function __construct()
  {
    $noimport = true;
    include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );       
    include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); 
    include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
    include_once( "/var/www/html/ap/interf/app/faro/fpdf/fpdf.php");
    include_once( "/var/www/html/ap/satt_faro/constantes.inc");
    try
    {
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( 'CronDeletePDF' );
      $fExcept -> SetParams( "Faro", "Nueva funcionalidad Eliminar PDF" );
      $fLogs = array();
      $this -> db4 = new Consult( array( "server" => Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS ), $fExcept );
      $this -> DeletePDF( date('Y:m:d H:i:s') );
    }
    catch( Exception $e )
    {
      $mTrace = $e -> getTrace();
      $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),$e -> getLine());
      return FALSE;
    } 
  }
  
  function getArchivosFTP()
  {
    $mSelect = "SELECT dir_pdfftp, DATEDIFF( NOW(), fec_enviox ) AS diferencia,
                       num_despac, num_docume, num_docalt
                  FROM ".BASE_DATOS.".tab_despac_destin
                 WHERE ind_sendxx = '1'";
    $this -> db4 -> ExecuteCons( $mSelect );
    return $this -> db4 -> RetMatrix();
  }
  function DeletePDF( $fec_actual )
  {
    $_ARCHIVOS = $this -> getArchivosFTP();
    
    foreach( $_ARCHIVOS as $row )
    {
      if( $row['diferencia'] > 90 )
      {
        if( unlink( $row['dir_pdfftp'] ) )
        {
          $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_destin 
                       SET ind_sendxx = 'E',
                           usr_modifi = 'Cron Eliminar PDF',
                           fec_modifi = NOW()
                     WHERE num_despac = '".$row['num_despac']."'
                       AND num_docume = '".$row['num_docume']."' 
                       AND num_docalt = '".$row['num_docalt']."'";
          if( $this -> db4 -> ExecuteCons( $mUpdate ) )
          {
            echo "PDF ELIMINADO: ".$row['dir_pdfftp']."<hr>";
          }
        }
      }
    }
  }
}

$_CRON = new executeDeletePDF();

?>
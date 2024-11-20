<?php 

  /*******************************************************************************
  * @file server.php                                                             *
  * @brief Cron para consulta de gps de los despachos.                           *
  * @version 0.1                                                                 *
  * @date 12 de Febrero de 2013                                                    *
  * @author Nelson Liberato.                                                     *
  *******************************************************************************/  
  /*ini_set('display_errors', true);
  error_reporting(E_ALL & ~E_NOTICE);*/
  $noimport=true;
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
  include_once( "/var/www/html/ap/interf/app/gps/Config.kons.php" );     //Constantes para tabla de gps.
  include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
  include_once("PHPMailer/class.phpmailer.php");
  include_once("/var/www/html/ap/satt_faro/constantes.inc");
  include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker

  $dir="/var/www/html/ap/interf/app/faro/"; // Direcotorio donde se encuentra el cron.
  $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
  $fExcept -> SetUser( 'ShellConsecutivosFotos' );
  $fExcept -> SetParams( "Faro", "Cron para corregir los consecutivos de las fotos de biometria" );
  $fLogs = array();
  
  try
  {    
    $db4   = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS ), $fExcept );
    $mensaje = '';
    $email = array();
    $fecha = date('Y-m-d H:i');
    echo "<hr>".$fQuery  = "SELECT num_despac, cod_contro, COUNT( cod_contro ) AS repetidos
                             FROM ".BASE_DATOS.".tab_despac_images
                            WHERE 1 
                         GROUP BY num_despac, cod_contro
                           HAVING repetidos > 1
                           ORDER BY repetidos DESC";     
    $db4 -> ExecuteCons( $fQuery );
    $Despachos = $db4 -> RetMatrix(  );
    echo "<pre>Cantidad=";echo count( $Despachos ); echo "</pre>";
    
    if( 0 != $db4 -> RetNumRows() )
    {
      $i = 0;
      echo "<BR><b>Despachos a arreglar:</b> <br>";
      echo "<BR>";
      foreach( $Despachos as $fDespac )
      { 
        $error_ = NULL;
        echo "<hr>_____DESPACHO N°_".$fDespac['num_despac']."_____<br>";
        
          //Se consulta cada registro
          $query = "SELECT a.fec_creaci
										  FROM ".BASE_DATOS.".tab_despac_images a
										  WHERE a.num_despac = '".$fDespac['num_despac']."'
                        AND a.cod_contro = '".$fDespac['cod_contro']."'
										  ORDER BY a.fec_creaci DESC ";

          $db4 -> ExecuteCons( $query );
          $Fotos = $db4 -> RetMatrix(  );
          
          $consec = 1;
          foreach( $Fotos as $Foto )
          {
            echo "<hr>".$update = "UPDATE ".BASE_DATOS.".tab_despac_images
                                      SET num_consec = '".$consec."'
                                    WHERE num_despac = '".$fDespac['num_despac']."'
                                      AND cod_contro = '".$fDespac['cod_contro']."'
                                      AND fec_creaci = '".$Foto['fec_creaci']."'";
            //$db4 -> ExecuteCons( $update, "BRC" );
            $consec++;
          }
       }
    }
  }
  catch( Exception $e )
  {
    $mTrace = $e -> getTrace();
    $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),$e -> getLine());
    return FALSE;
  }
  
  
  
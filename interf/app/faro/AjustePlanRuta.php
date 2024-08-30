<?php 
  ini_set('display_errors', true);
  error_reporting(E_ALL & ~E_NOTICE);
  $noimport=true;
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );  
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" );
  include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
  include_once("/var/www/html/ap/satt_faro/constantes.inc");

  $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
  $fExcept -> SetUser( 'Despachos_en_Ruta' );
  $fExcept -> SetParams( "Faro", "Despachos_en_Ruta_Dañados" );
  $fLogs = array();
    
  try
  {
    
    $db4   = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS ), $fExcept );
    
    $mSelect = "SELECT a.num_despac
                  FROM ".BASE_DATOS.".tab_despac_despac a, 
                       ".BASE_DATOS.".tab_despac_vehige b
                 WHERE a.num_despac = b.num_despac
                   AND a.fec_salida IS NOT NULL
                   AND a.fec_llegad IS NULL
                   AND a.ind_anulad = 'R'
                   AND b.ind_activo = 'S'
                 GROUP BY 1";     
    $db4 -> ExecuteCons( $mSelect);
    $_DESPAC = $db4 -> RetMatrix();
    
    $db4 -> StartTrans();
    
    foreach( $_DESPAC as $despac )
    {
      $mSelect = "SELECT a.cod_contro, a.ind_estado, b.nom_contro
                  FROM ".BASE_DATOS.".tab_despac_seguim a,
                       ".BASE_DATOS.".tab_genera_contro b
                 WHERE a.num_despac = '".$despac['num_despac']."' 
                   AND a.cod_contro = b.cod_contro
                 ORDER BY a.fec_planea DESC 
                 LIMIT 1";     
      $db4 -> ExecuteCons( $mSelect);
      $_ULTIPC = $db4 -> RetMatrix();
      
      // if($despac['num_despac'] == '776417' )
      // {
        if( $_ULTIPC[0]['nom_contro'] != 'LUGAR ENTREGA' && $_ULTIPC[0]['nom_contro'] != 'LUGAR DE ENTREGA' && $_ULTIPC[0]['cod_contro'] != '21081' && $_ULTIPC[0]['cod_contro'] != '19437' && $_ULTIPC[0]['cod_contro'] != '14433' )
        {
          echo "<br><br><br><br><br><br><hr>".$mUpdate = "UPDATE 
                     ".BASE_DATOS.".tab_despac_seguim a, 
                     ".BASE_DATOS.".tab_genera_rutcon b, 
                     ".BASE_DATOS.".tab_despac_despac c 
                     SET 
                     a.fec_planea = DATE_ADD( c.fec_salida, INTERVAL b.val_duraci MINUTE ), 
                     a.fec_alarma = DATE_ADD( c.fec_salida, INTERVAL b.val_duraci MINUTE )
                     WHERE 
                     a.num_despac = c.num_despac AND
                     a.cod_contro = b.cod_contro AND 
                     a.cod_rutasx = b.cod_rutasx AND 
                     a.num_despac = '".$despac['num_despac']."'";
          
          if( $db4 -> ExecuteCons( $mUpdate, "R" ) === FALSE )
            throw new Exception( "Error en Update.", "3001" );
            
          echo "<hr>".$mSelect = "SELECT cod_contro, fec_creaci
                        FROM ".BASE_DATOS.".tab_despac_contro
                       WHERE num_despac = '".$despac['num_despac']."'
                       UNION 
                      SELECT cod_contro, fec_creaci
                        FROM ".BASE_DATOS.".tab_despac_noveda
                       WHERE num_despac = '".$despac['num_despac']."'
                       ORDER BY 2 DESC 
                       LIMIT 1";
          
          $db4 -> ExecuteCons( $mSelect);
          $_PC = $db4 -> RetMatrix();
          
           $mSelect = "SELECT a.cod_contro, a.ind_estado, b.nom_contro, a.fec_planea
                    FROM ".BASE_DATOS.".tab_despac_seguim a,
                         ".BASE_DATOS.".tab_genera_contro b
                   WHERE a.num_despac = '".$despac['num_despac']."' 
                     AND a.cod_contro = b.cod_contro
                     AND a.cod_contro = '".$_PC[0]['cod_contro']."' ";     
          $db4 -> ExecuteCons( $mSelect);
          $_FECPLA = $db4 -> RetMatrix();
          
          echo "<hr>".$mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_seguim a 
                       SET a.ind_estado = '1'
                      WHERE a.fec_planea >= '".$_FECPLA[0]['fec_planea']."'
                        AND a.num_despac = '".$despac['num_despac']."' ";
          if( $db4 -> ExecuteCons( $mUpdate, "R" ) === FALSE )
            throw new Exception( "Error en Update.", "3001" );
            
          echo "<hr>".$mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_seguim a 
                       SET a.ind_estado = '0'
                      WHERE a.fec_planea < '".$_FECPLA[0]['fec_planea']."'
                        AND a.num_despac = '".$despac['num_despac']."' ";
          if( $db4 -> ExecuteCons( $mUpdate, "R" ) === FALSE )
            throw new Exception( "Error en Update.", "3001" );
        }
      // }
    }
    $db4 -> Commit();
  }
  catch( Exception $e )
  {
    $mTrace = $e -> getTrace();
    $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),$e -> getLine());
    return FALSE;
  }

?>
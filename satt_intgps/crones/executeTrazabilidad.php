<?php 
/*********************************************************************************************/
/* @file executeTrazabilidadFaro.php                                                         */
/* @brief Cron que realiza el nuevo informe de trazabilidad diaria para FARO ID SPG: 125499  */
/* @version 0.1                                                                              */
/* @date 04 de Diciembre de 2013                                                             */
/* @author Andrï¿½s Felipe Malaver                                                             */
/*********************************************************************************************/  
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
//die();
class Trazabilidad
{
  var $db4 = NULL;
  var $PDF = NULL;
  var $dir = "/var/www/html/ap/interf/app/faro/";
  function __construct()
  {
    $noimport = true;
    include_once("/var/www/html/ap/satt_intgps/crones/Config.kons.inc");    
    include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); 
    include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
    include_once("/var/www/html/ap/interf/app/faro/fpdf/multicell_fpdf.php");
    include_once("/var/www/html/ap/interf/app/faro/PHPMailer/class.phpmailer.php");
    include_once("/var/www/html/ap/satt_intgps/constantes.inc");
    try
    {
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( 'CronTrazabilidadFaro' );
      $fExcept -> SetParams( "Faro", "Nuevo Informe Trazabilidad Diaria Despachos En Ruta CLFARO" );
      $fLogs = array();
      $this->db4 =   new Consult( array( "server" => HOST, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS), $this->fExcept);
      $this -> SendTrazabilidadDiaria();
    }
    catch( Exception $e )
    {
      $mTrace = $e -> getTrace();
      $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),$e -> getLine());
      return FALSE;
    }
  }
  
  function ValidateTransportadora( $cod_transp, $tie_trazab )
  {
    $mQuery = "SELECT INTERVAL ".$tie_trazab." MINUTE + a.fec_creaci FROM ".BASE_DATOS.".tab_bitaco_trazab a WHERE a.nit_transp = '".$cod_transp."' AND a.num_consec = (SELECT MAX(b.num_consec) FROM ".BASE_DATOS.".tab_bitaco_trazab b WHERE b.nit_transp = '".$cod_transp."' ) ";
    $this -> db4 -> ExecuteCons( $mQuery );
    $time = $this -> db4 -> RetMatrix();
    $act = date('Y-m-d H:i:s');
    if( sizeof( $time ) > 0 )
    {
      if( strtotime( $time[0][0] ) <= strtotime( $act )   )
      {
        return TRUE;
      }
      else
        return FALSE;
    }
    else
    {
      return TRUE;
    }
  }
  
  function SendTrazabilidadDiaria()
  {
    $_TRANSP = $this -> getTransportadoras();
    echo "<pre> TRASNPORTADORAS CON TRAZABILIDAD DIARIA PARAMETRIZADA"; print_r(  $_TRANSP); echo "</pre>";
      foreach( $_TRANSP as $row )
      { 
        if( $this -> ValidateTransportadora( $row['cod_transp'], $row['tie_trazab'] ) )
        {
          $_CLIENT = $this -> getClientesAgrupados( $row['cod_transp'] );
          echo "<pre><hr>Clientes de ".$row['cod_transp']."  " ;print_r(  $_CLIENT);echo "</pre>";
          
          if( sizeof( $_CLIENT ) > 0 )
          {
            $_EMAIL = '';
            $_DESPA = '';
            foreach( $_CLIENT as $key => $cliente )
            {
              $this -> GeneratePDF( $cliente, $key );
              $_EMAIL .= $_EMAIL != '' ? ', '.$cliente['emailx'] : $cliente['emailx'];
              $_DESPA .= $_DESPA != '' ? ', '.$cliente['despac'] : $cliente['despac'];
            }
            $mConsec = "SELECT MAX(b.num_consec) FROM ".BASE_DATOS.".tab_bitaco_trazab b WHERE b.nit_transp = '".$row['cod_transp']."'";
            $this -> db4 -> ExecuteCons( $mConsec );
            $_consec = $this -> db4 -> RetMatrix();
            $consec = $_consec[0][0] + 1;
            
            $mInsert = "INSERT INTO ".BASE_DATOS.".tab_bitaco_trazab
                                  ( num_consec, nit_transp, cod_client, dir_emailx, num_despac, usr_creaci, fec_creaci ) 
                           VALUES ( ".$consec.", '".$row['cod_transp']."','".$key."' ,'".$_EMAIL."', '".$_DESPA."', 'CronTrazab', NOW() )";
            $this -> db4 -> ExecuteCons( $mInsert );
          }
        }
      }
  }
  
  function GeneratePDF( $arr_client, $key )
  {
    $num_despac = $arr_client['despac'];
    $dir_correo = $arr_client['emailx'];
    
    $mQuery = "SELECT a.num_despac, 
                      a.fec_salida, 
                     (SELECT a1.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a1 WHERE a1.cod_ciudad = a.cod_ciuori) AS origen,
                     (SELECT nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = a.cod_ciudes) AS destin, 
                     IF( d.fec_llegpl IS NULL ,'0',d.fec_llegpl) AS fec_llegpl,
                      f.abr_tercer, 
                      d.num_placax,
                      CONCAT(e.nom_tercer, ' ', e.nom_apell1, ' ', e.nom_apell2) AS abr_tercer,
                     IF(DATEDIFF(d.fec_llegpl , a.fec_salida) IS NULL ,'N/A',DATEDIFF(d.fec_llegpl , a.fec_salida)),
                     IF(DATEDIFF( NOW(), d.fec_llegpl) IS NULL ,'N/A',DATEDIFF(NOW() , d.fec_llegpl)), r.abr_tercer As nom_genera, d.cod_transp                     
                FROM " . BASE_DATOS . ".tab_despac_seguim b,
                     " . BASE_DATOS . ".tab_despac_vehige d,
                     " . BASE_DATOS . ".tab_tercer_tercer e,
                     " . BASE_DATOS . ".tab_tercer_tercer f,
                     " . BASE_DATOS . ".tab_despac_despac a
                      LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n 
                      ON n.cod_tercer = a.cod_asegur
                      LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer r
                      ON r.cod_tercer = a.cod_client
               WHERE a.num_despac = d.num_despac AND
                     a.num_despac = b.num_despac AND
                     d.cod_conduc = e.cod_tercer AND
                     d.cod_transp = f.cod_tercer AND                   
                     a.fec_salida Is Not Null AND
                     a.fec_llegad IS NULL AND
                     a.ind_anulad = 'R' AND
                     a.num_despac IN( ".$num_despac." )
           GROUP BY 1 ORDER BY b.fec_alarma,1 ";
           
    $this -> db4 -> ExecuteCons( $mQuery );
    $pedidos = $this -> db4 -> RetMatrix();
    
    /**********************************************************/
    /****************** CONSTRUCCIï¿½N DEL PDF ******************/
    $fecha = date('Y-m-d H:i');
    $autorizacion = $this->getAutorizacionUsuario($pedidos[0]['cod_transp']);
    $validaPermis = false;
    if($autorizacion)
    {
      if($autorizacion->inf_tradia->ind_visibl)
      {
        $validaPermis = true;
      }
    }
    
    $this -> PDF = new PDF_MC_Table('L','mm','letter');
    $this -> PDF -> AddPage();
    $this -> PDF -> SetLeftMargin(5);
    $this -> PDF -> SetFont('Arial','B',11);
    $this -> PDF -> SetTextColor(0);
    $this -> PDF -> SetDrawColor(0);
    $this -> PDF -> SetFillColor(235);
    
    $this -> PDF -> Cell(250,5,"INFORME DE TRAZABILIDAD",0,1,'C',0);
    $this -> PDF -> Cell(260,5," DE LA FECHA ".$fecha,0,1,'C',0);
    $this -> PDF -> Ln();
    $this -> PDF -> Ln();
    $this -> PDF -> SetFont('Arial','B',6);
    $this -> PDF -> Cell(4,4,"#","LTR",0,'C',1);
    $this -> PDF -> Cell(15,4,"Despacho","LTR",0,'C',1);
    $this -> PDF -> Cell(18,4,"Generador","LTR",0,'C',1);
    $this -> PDF -> Cell(20,4,"Fecha/ Hora Salida","LTR",0,'C',1);
    $this -> PDF -> Cell(25,4,"Origen","LTR",0,'C',1);
    $this -> PDF -> Cell(25,4,"Destino","LTR",0,'C',1);
    $this -> PDF -> Cell(27,4,"Estimado de Llegada","LTR",0,'C',1);
    $this -> PDF -> Cell(9,4,"Placa","LTR",0,'C',1);
    $this -> PDF -> Cell(34,4,"Conductor","LTR",0,'C',1);
    $this -> PDF -> Cell(30,4,"Ubicacion","LTR",0,'C',1);
    if($validaPermis ==true)
    {
      $this -> PDF -> Cell(20,4,"Ultimo Seguimiento","LTR",0,'C',1);
    }
    $this -> PDF -> Cell(35,4,"Seguimiento Trafico","LTR",1,'C',1);
    
    //--------------------------------------------------------------------
    $this -> PDF -> Cell(4,4,"","LBR",0,'C',1);     // conse
    $this -> PDF -> Cell(15,4,"","LBR",0,'C',1);     //despacho
    $this -> PDF -> Cell(18,4,"","LBR",0,'C',1);     //Generador

    $this -> PDF -> Cell(10,4,"Fecha",1,0,'C',1);    // fecha
    $this -> PDF -> Cell(10,4,"Hora",1,0,'C',1);     // hora

    $this -> PDF -> Cell(25,4,"","LBR",0,'C',1);     //origen
    $this -> PDF -> Cell(25,4,"","LBR",0,'C',1);     // destino

    $this -> PDF -> Cell(10,4,"Fecha",1,0,'C',1);    //Fecha
    $this -> PDF -> Cell(10,4,"Duración",1,0,'C',1); //Duraciï¿½n
    $this -> PDF -> Cell(7,4,"Días",1,0,'C',1);     //Dï¿½as

    $this -> PDF -> Cell(9,4,"","LBR",0,'C',1);     //Placa
    $this -> PDF -> Cell(34,4,"","LBR",0,'C',1);     //Conductor
    $this -> PDF -> Cell(30,4,"","LBR",0,'C',1);     //Ubicacion
    if($validaPermis ==true)
    {
      $this -> PDF -> Cell(10,4,"Fecha",1,0,'C',1);    // fecha
      $this -> PDF -> Cell(10,4,"Hora",1,0,'C',1);     // hora
    }

    $this -> PDF -> Cell(35,4,"","LBR",1,'C',1);     //Seguimiento Trafico
    //--------------------------------------------------------------------
    $i = 1;
    foreach($pedidos as $pedido)
    {
      #seguimiento
      $inf_despac = getNovedadesDespac($this -> db4, $pedido[0], '2');
      $fec_noveda = array();          
      if($validaPermis ==true)
      {
        if($inf_despac['fec_noveda'] != '' && $inf_despac['fec_noveda'] != NULL)
        {
          $fec_noveda =  $this -> toFecha($inf_despac['fec_noveda']);
        }
      }
      #fin seguimiento
      $ubicacion = $this -> GetUbicacion( $pedido[0] );
      $fec_sal  = $this -> toFecha( $pedido[1] );
      $fec_lle  = $this -> toFecha( $pedido[4]);
      $obs_despac = $ubicacion[0]['obs'];
      $nom_conduc = substr( $pedido[7], 0, 40 );
      $dataPedido = array(
                            $i,                 //Consecutivo
                            $pedido[0],         //Despacho
                            $pedido['nom_genera'],         //generador
                            $fec_sal[0],        //Fecha salida
                            $fec_sal[1],        //Hora salida
                            $pedido[2],         //Origen
                            $pedido[3],         //Destino
                            $fec_lle[0],        //Fecha
                            $pedido[8],         //Duraciï¿½n
                            $pedido[9],         //Dï¿½as
                            $pedido[6],         //Placa
                            $nom_conduc,        //Conductor
                            $ubicacion[0][1],   //Ubicacion
                            $fec_noveda[0],     //Fecha seguimiento
                            $fec_noveda[0],     //Hora seguimiento
                            $obs_despac         //Seguimiento Trafico
                        );
      $dataDimenci = array(4,15,18,10,10,25,25,10,10,7,9,34,30,10,10,35);
      if($validaPermis != true)
      {
        $dataPedido[13]  = $dataPedido[15];
        $dataDimenci[13] = $dataDimenci[15];
        unset($dataPedido[14],$dataPedido[15]);
        unset($dataDimenci[14],$dataDimenci[15]);
      }
      $this -> PDF -> SetWidths($dataDimenci);// Ancho de las tabla
            srand(microtime()*1000000);
      $this -> PDF -> SetFont('Arial','',5);
      $this -> PDF -> row($dataPedido);// Ancho de las tabla
      $i++;   
    }
        
    $file = $this -> dir."informe_faro/".$key.".pdf";
    $this -> PDF -> Output($file, 'F');
    $this -> PDF -> Close();
    chmod ($file, 0777);
    
    $mail = new PHPMailer();
    $message = ''; 
    foreach( explode( ',', $dir_correo ) as $__emails)
    { 
      
      $mail -> From = "faroavansat@eltransporte.com";
      $mail -> FromName = "Control Trafico Faro";
      $mail -> Subject = "Informe de Trazabilidad en Diario";
      $mail -> IsHTML(true);
      $mail -> Host ='localhost';
      $mail -> AddAttachment($file,'InformeTranzabilidadDiaria.pdf',"base64");
      $body = "
              <BR>
              Por medio del presente enviamos el reporte de Trazabilidad Diario de su empresa, de acuerdo al seguimiento realizado por 
              nuestro departamento de seguridad y trafico CLF.
              <BR> 
              <BR>
              "; 
              
      $mail -> Body = $body;
      //$mail -> AddAddress("edward.serrano@intrared.net");
      $mail -> AddAddress("maribel.garcia@eltransporte.org");
      $mail -> AddAddress($__emails);
      $message = $__emails."<hr>";
      
      if(!$mail -> Send())
        echo $mail -> ErrorInfo;
      else  
        echo $message;
      
       $mail->ClearAddresses();
       $mail->ClearAttachments();
    }
    /**********************************************************/
  }
  
  function getClientesAgrupados( $cod_transp = NULL )
  {
    $mQuery = "SELECT a.num_despac, 
                      b.cod_transp, 
                      UPPER( TRIM( c.abr_tercer ) ) AS nom_transp
                 FROM ".BASE_DATOS.".tab_despac_despac a, 
                      ".BASE_DATOS.".vis_despac_seguim d, 
                      ".BASE_DATOS.".tab_despac_vehige b, 
                      ".BASE_DATOS.".tab_tercer_tercer c 
                WHERE a.num_despac = d.num_despac 
                  AND a.num_despac = b.num_despac 
                  AND b.cod_transp = c.cod_tercer 
                  AND a.fec_salida IS NOT NULL 
                  AND a.fec_salida <= NOW() 
                  AND a.fec_llegad IS NULL 
                  AND a.ind_planru = 'S' 
                  AND a.ind_anulad = 'R' ";
      
    if ( $cod_transp )
      $mQuery .= $cod_transp == NULL ? NULL : " AND b.cod_transp = '".$cod_transp."' ";

      $mQuery .= " GROUP BY a.num_despac ORDER BY 3 ";
    
    $this -> db4 -> ExecuteCons( $mQuery );
    $_DESPAC = $this -> db4 -> RetMatrix();
    $_TO_RETURN = array();
    foreach( $_DESPAC as $row )
    {
      $_DATACL = $this -> getDataClient( $row['num_despac'] );
      echo "<pre>";
      print_r($_DATACL);
      echo "</pre>";
      if( count( $_DATACL ) > 0 )
      {
        foreach( $_DATACL as $datos )
        {
          if( $datos[0] != NULL && $datos[0] != '' )
          {
            $_TO_RETURN[ $datos[1] ]['emailx'] = $datos[0];
            $_TO_RETURN[ $datos[1] ]['despac'] .= $_TO_RETURN[ $datos[1] ]['despac'] == '' ? $row['num_despac'] : ', '.$row['num_despac'] ;         
          }
        }
      }
    }
    return $_TO_RETURN;
  }
  
  function GetUbicacion( $pedido )
  {
    $mQuery = "( SELECT a.fec_contro, UPPER( b.nom_sitiox ), a.obs_contro as 'obs'
                   FROM ".BASE_DATOS.".tab_despac_contro a,
                        ".BASE_DATOS.".tab_despac_sitio b
                  WHERE a.cod_sitiox = b.cod_sitiox AND
                        a.num_despac = '$pedido' )
                UNION
               ( SELECT a.fec_noveda, UPPER( b.nom_contro ), a.des_noveda as 'obs'
                   FROM ".BASE_DATOS.".tab_despac_noveda a,
                        ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro AND
                        a.num_despac = '$pedido' )
               ORDER BY 1 DESC ";
     $this -> db4 -> ExecuteCons( $mQuery );
     return $this -> db4 -> RetMatrix();
  }
  
  function getDataClient( $num_despac )
  {
    $mQuery = "SELECT c.dir_emailx
                 FROM ".BASE_DATOS.".tab_despac_despac a
                 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                 INNER JOIN ".BASE_DATOS.".tab_genera_concor c ON b.cod_transp = c.num_remdes
                WHERE a.num_despac = '".$num_despac."' AND c.ind_trazab = 1";
    $this -> db4 -> ExecuteCons( $mQuery );
    $_email = $this -> db4 -> RetMatrix();
    return $_email;
  }
  
  function getTransportadoras()
  {
    $mQuery = "SELECT a.cod_transp, a.tie_trazab
                 FROM ".BASE_DATOS.".tab_transp_tipser a
                WHERE a.tie_trazab > 0 
                  AND a.num_consec = ( SELECT MAX( b.num_consec ) FROM ".BASE_DATOS.".tab_transp_tipser b WHERE a.cod_transp = b.cod_transp )
                  AND CURRENT_TIME BETWEEN a.ini_trazab AND a.fin_trazab;";
    $this -> db4 -> ExecuteCons( $mQuery );
    return $this -> db4 -> RetMatrix();
  }
  
  function toFecha($date)
  {
      $fecha = explode("-", $date);
      $dia = $fecha[2];
      $mes = $fecha[1];
      $ano = $fecha[0];     
      
      $dia = explode(" ",$dia);
      
      $hora = explode(" ",$date);        
      $letra_mes = "";

      switch ($mes)
      {
          case "1": $letra_mes = "ENE";
              break;
          case "2": $letra_mes = "FEB";
              break;
          case "3": $letra_mes = "MAR";
              break;
          case "4": $letra_mes = "ABR";
              break;
          case "5": $letra_mes = "MAY";
              break;
          case "6": $letra_mes = "JUN";
              break;
          case "7": $letra_mes = "JUL";
              break;
          case "8": $letra_mes = "AGO";
              break;
          case "9": $letra_mes = "SEP";
              break;
          case "10": $letra_mes = "OCT";
              break;
          case "11": $letra_mes = "NOV";
              break;
          case "12": $letra_mes = "DIC";
              break;
      }
            
      $salida[0] = "$dia[0]-$letra_mes";
      $salida[1] = "$hora[1]";
      
      return $salida;
  }

  function getAutorizacionUsuario($cod_transp)
  {
    $sql = "SELECT a.jso_autori 
                      FROM ".BASE_DATOS.".tab_autori_usuari a 
                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b 
                    ON a.cod_conusu = b.cod_consec
                INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil c ON b.cod_perfil = c.cod_perfil
                WHERE c.clv_filtro ='".$cod_transp."'";
    $this -> db4 -> ExecuteCons( $sql );
    $aut = $this -> db4 -> RetMatrix();
    if(sizeof($aut)>0)
    {
      return json_decode($aut[0][0]);
    }
    else
    {
      return false;
    }
  }

  

}
$_CRON = new Trazabilidad(); 

?>
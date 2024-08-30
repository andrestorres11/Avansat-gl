<?php 

  /*******************************************************************************
  * @file server.php                                                             *
  * @brief Cron Para el Informe de Trazabilidad Diairia para Faro.               *
  * @version 0.1                                                                 *
  * @date 31 de Enero de 2013                                                    *
  * @author Nelson Liberato.                                                     *
  *******************************************************************************/  
  ini_set('display_errors', true);
  error_reporting(E_ALL & ~E_NOTICE);
  $noimport=true;
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.
  include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
  include_once("fpdf/fpdf.php");
  include_once("PHPMailer/class.phpmailer.php");
//  include_once("/var/www/html/ap/dev/satt_faro/constantes.inc");
  include_once("/var/www/html/ap/satt_faro/constantes.inc");

  $dir="/var/www/html/ap/interf/app/faro/"; // Direcotorio donde se encuentra el cron.
  
  $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
  $fExcept -> SetUser( 'CronDespachosFaro' );
  $fExcept -> SetParams( "Faro", "Informe Trazabilidad Diaria Despachos En Ruta" );
  $fLogs = array();
    
  try
  {
    
    $db4   = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS ), $fExcept );

    $mensaje = '';
    $email = array();
    $fecha = date('Y-m-d H:i');
    $sql  = "SELECT a.cod_transp, b.abr_tercer 
               FROM ".BASE_DATOS.".tab_despac_vehige a,
                    ".BASE_DATOS.".tab_tercer_tercer b
              WHERE a.cod_transp = b.cod_tercer
              GROUP BY 1";     
    $db4 -> ExecuteCons( $sql);
    $cliente = $db4 -> RetMatrix(  );
    $j= 1;
    foreach($cliente as $Clientes)
    {   echo "<br>";
        echo "Correo N°: ".$j." Al cliente: ".$Clientes[1];
        $pedidos = GetTranportadora( $Clientes[0] );
        $db4 -> ExecuteCons( $pedidos);
        $pedidos = $db4 -> RetMatrix();  
         
      if(sizeof($pedidos) != 0)
      {  
        $pdf = new FPDF('L','mm','letter');
        $pdf->AddPage();
        $pdf->SetLeftMargin(5);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0);
        $pdf->SetFillColor(235);
        //$pdf->Image( $dir."logo_agrins.jpg", 5, 5, 15, 20 );
        $pdf->Cell(250,5,"INFORME DE TRAZABILIDAD DE ".$Clientes[1],0,1,'C',0);
        $pdf->Cell(250,5," DE LA FECHA ".$fecha,0,1,'C',0);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial','B',6);
        $pdf->Cell(3,4,"#","LTR",0,'C',1);
        $pdf->Cell(25,4,"N° Documento","LTR",0,'C',1);
        $pdf->Cell(40,4,"Fecha/ Hora Salida","LTR",0,'C',1);
        $pdf->Cell(35,4,"Origen","LTR",0,'C',1);
        $pdf->Cell(35,4,"Destino","LTR",0,'C',1);
        $pdf->Cell(39,4,"Estimado de Llegada","LTR",0,'C',1);
        $pdf->Cell(35,4,"Ubicacion","LTR",0,'C',1);
        $pdf->Cell(8,4,"Placa","LTR",0,'C',1);
        $pdf->Cell(40,4,"Conductor","LTR",1,'C',1);
          
        //--------------------------------------------------------------------
          $pdf->Cell(3,4,"","LBR",0,'C',1);     // conse
          $pdf->Cell(25,4,"","LBR",0,'C',1);     //despacho
          
          $pdf->Cell(20,4,"Fecha",1,0,'C',1);    // fecha
          $pdf->Cell(20,4,"Hora",1,0,'C',1);     // hora
          
          $pdf->Cell(35,4,"","LBR",0,'C',1);     //origen
          $pdf->Cell(35,4,"","LBR",0,'C',1);     // destino
            
                   
          $pdf->Cell(13,4,"Fecha",1,0,'C',1);    //Fecha
          $pdf->Cell(13,4,"Duración",1,0,'C',1); //Duración
          $pdf->Cell(13,4,"Días",1,0,'C',1);     //Días
          
          
          $pdf->Cell(35,4,"","LBR",0,'C',1);     //Ubicacion
          $pdf->Cell(8,4,"","LBR",0,'C',1);     //Placa
          $pdf->Cell(40,4,"","LBR",1,'C',1);     //Conductor
        //--------------------------------------------------------------------
        $i = 1;
        foreach($pedidos as $pedido)
        {
          $Ubicacion = GetUbicacion( $pedido[0] );
          $db4 -> ExecuteCons($Ubicacion);
          $ubicacion = $db4 -> RetMatrix(  ); 
          
          $pdf->SetFont('Arial','',5);
          $pdf->Cell(3,4,$i,1,0,'L',0);                              //Consecutivo
          $pdf->Cell(25,4,$pedido[0],1,0,'L',0);                      //Despacho
          
          $fec_sal  = toFecha( $pedido[1] );
          $fec_lle  = toFecha( $pedido[4]);
          
          $pdf->Cell(20,4,$fec_sal[0],1,0,'L',0);                      //Fecha salida
          $pdf->Cell(20,4,$fec_sal[1],1,0,'L',0);                      //Hora salida
          
          $pdf->Cell(35,4,$pedido[2],1,0,'L',0);                       //Origen
          $pdf->Cell(35,4,$pedido[3],1,0,'C',0);                       //Destino
          
          $pdf->Cell(13,4,$fec_lle[0],1,0,'C',0);                      //Fecha
          $pdf->Cell(13,4,$pedido[8],1,0,'C',0);                       //Duración
          $pdf->Cell(13,4,$pedido[9],1,0,'C',0);                       //Días
                       
          $pdf->Cell(35,4,$ubicacion[0][1],1,0,'C',0);                 //Ubicacion
          $pdf->Cell(8,4,$pedido[6],1,0,'L',0);                        //Placa
          $pdf->Cell(40,4,$pedido[7],1,1,'L',0);                       //Conductor
          
          /*if($pdf->GetY()>= 240)
          {
            $pdf->AddPage();
            $pdf->SetLeftMargin(5);
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(230);
            $pdf->SetFillColor(235);
            //$pdf->Image( $dir."logo_agrins.jpg", 5, 5, 15, 20 );
            $pdf->Cell(250,5,"INFORME DE TRAZABILIDAD DE ".$Clientes[1],0,1,'C',0);
            $pdf->Cell(250,5," DE LA FECHA ".$fecha,0,1,'C',0);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFont('Arial','B',6);
            $pdf->Cell(15,4,"Manifiesto",1,0,'C',1);
            $pdf->Cell(25,4,"Fecha de Salida",1,0,'C',1);
            $pdf->Cell(20,4,"Origen",1,0,'C',1);
            $pdf->Cell(20,4,"Destino",1,0,'C',1);
            $pdf->Cell(30,4,"Estimado de Llegada",1,0,'C',1);
            $pdf->Cell(15,4,"Duracion",1,0,'C',1);
            $pdf->Cell(15,4,"Dias",1,0,'C',1);
            $pdf->Cell(20,4,"Ubicacion",1,0,'C',1);
            $pdf->Cell(40,4,"Producto",1,0,'C',1);
            $pdf->Cell(15,4,"Placa",1,0,'C',1);
            $pdf->Cell(40,4,"Conductor",1,1,'C',1);
          } */
           $i++;   
        }
        
        $file= $dir."informe_faro/".$Clientes[1].".pdf";
        $pdf->Output($file, 'F'); //$file, 'F'
        $pdf->Close();
        chmod ($file, 0777);
        
        //------------------------------------------------------------------------------------
        $Correos = GetCorreos( $Clientes[0] );        
        $db4 -> ExecuteCons( $Correos);
        $correos = $db4 -> RetMatrix('i');  
       
        $ema='';
        $aux='';
        if($correos)
        {
          foreach($correos as $correo)
          {
             if($ema!='')
                $aux=","; 
                $ema .= $aux.$correo[0];
           }   
        }
        //------------------------------------------------------------------------------------
        
        $mail=new PHPMailer();//creamos instancia de PHPMailer
        //$_emails_ = "nelson.liberato@intrared.net";
        $_emails_ = "miguel.garcia@intrared.net, hugo.malagon@intrared.net";
        
        if(!empty($ema))
        {
          $_emails_ .= ',' . $ema;
        }    

        foreach(explode(',', $_emails_) as $__emails)
        { 
          $mail->From = "faroavansat@eltransporte.com";//le decimos correo de  kien lo envia 
          $mail->FromName = "Control Trafico Faro";//le decimos nombre de kien envía
          $mail->Subject = "Informe de Trazabilidad Diario";//le damos asunto
          $mail->IsHTML(true);//decimos que será html
          $mail->Host ='localhost';
          $mail->AddAttachment($file,'InformeTranzabilidadDiaria.pdf',"base64");//adjuntamos el archivo
          $body=" Señor(es): ".$Clientes[1]."
                  <br /><br />
                  Por medio del presente enviamos el reporte de Trazabilidad Diario de su empresa, de acuerdo al seguimiento realizado por 
                  nuestro departamento de seguridad y trafico CLF.
                  <br /><br />
                  "; 
                  
          $mail->Body = $body;

          if(!$mail->Send())
            echo $mail->ErrorInfo;
          else  
            echo "<br />Se Envio el email";

          echo "<br /><br />";

          $mail->ClearAddresses();
          $mail->ClearAttachments();
        } 
      }
      $j++;
    }
  }
  catch( Exception $e )
  {
    $mTrace = $e -> getTrace();
    $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),$e -> getLine());
    return FALSE;
  }
  
  function GetTranportadora( $Clientes )
  {
    $anidado = "( SELECT MAX( aa.fec_alarma ) 
                FROM ".BASE_DATOS.".tab_despac_seguim aa
                WHERE aa.num_despac = e.num_despac )"; 
    $mSql  =  "SELECT a.num_despac, 
                     a.fec_salida, 
                     (SELECT a1.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a1 WHERE a1.cod_ciudad = a.cod_ciuori) AS origen,
                     (SELECT nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = a.cod_ciudes) AS destin, 
                     IF( d.fec_llegpl IS NULL ,'0',d.fec_llegpl) AS fec_llegpl,
                     f.abr_tercer, 
                     d.num_placax,
                     IF( d.nom_conduc IS NOT NULL, d.nom_conduc, e.abr_tercer) AS abr_tercer,
                     IF(DATEDIFF(d.fec_llegpl , a.fec_salida) IS NULL ,'N/A',DATEDIFF(d.fec_llegpl , a.fec_salida)),
                     IF(DATEDIFF( NOW(), d.fec_llegpl) IS NULL ,'N/A',DATEDIFF(NOW() , d.fec_llegpl))                     
              FROM " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e,
                   " . BASE_DATOS . ".tab_tercer_tercer f,
                   " . BASE_DATOS . ".tab_despac_despac a
                    LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n 
                   ON n.cod_tercer = a.cod_asegur
             WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                   d.cod_conduc = e.cod_tercer AND
                   d.cod_transp = f.cod_tercer AND                   
                   a.fec_salida Is Not Null AND
                   a.fec_llegad IS NULL AND
                   a.ind_anulad = 'R' AND
                   d.cod_transp = '".$Clientes."'
          GROUP BY 1 ORDER BY b.fec_alarma,1 "; 
    
    return $mSql;
  }
  
  
  function GetUbicacion( $pedido )
  {
    $mSql = "( SELECT a.fec_contro, UPPER( b.nom_sitiox )
                   FROM ".BASE_DATOS.".tab_despac_contro a,
                        ".BASE_DATOS.".tab_despac_sitio b
                  WHERE a.cod_sitiox = b.cod_sitiox AND
                        a.num_despac = '$pedido' )
                UNION
               ( SELECT a.fec_noveda, UPPER( b.nom_contro )
                   FROM ".BASE_DATOS.".tab_despac_noveda a,
                        ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro AND
                        a.num_despac = '$pedido' )
               ORDER BY 1 DESC ";
     return $mSql;
  }
  
  function GetCorreos( $Clientes )
  {
     $mSql  = "SELECT dir_emailx 
               FROM ".BASE_DATOS.".tab_tercer_tercer 
                WHERE cod_tercer = '".$Clientes."' "; 
     return $mSql;
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
  
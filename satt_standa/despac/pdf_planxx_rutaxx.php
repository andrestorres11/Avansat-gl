<?php 

session_start();

class AjaxPDF
{
  var $PDF;
  var $CENTRAL = NULL;
  var $BASE_DATOS = NULL;
  var $conexion = NULL;
  
  function __construct()
  {
    include_once("../lib/FPDF/fpdf.php");
    include( "../lib/general/conexion_lib.inc" );
    include( "../lib/general/tabla_lib.inc" );
    include( "../lib/general/constantes.inc" );
    $this -> CENTRAL = $_REQUEST['standa'];
    $this -> BASE_DATOS = $_REQUEST['aplica'];
    $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION['USUARIO'], $_SESSION['CLAVE'], $this -> BASE_DATOS );
    $this -> GeneratePDF();
  }

  function GeneratePDF()
  {
    /***********************************************/
    $dir = "../../".$this -> BASE_DATOS."/imagenes/"; 
    $fecha = date('Y-m-d H:i');
    /***********************************************/
    $this -> PDF = new FPDF( 'L', 'mm', 'Legal' );
    $this -> PDF -> AddPage();
    $this -> PDF -> SetLeftMargin(3);
    
    $this -> PDF -> SetFont('Arial','B',18);
  
    $this -> PDF -> SetTextColor(0);
    $this -> PDF -> SetDrawColor(210);
    $this -> PDF -> SetFillColor(235);
    $this -> PDF -> Image( $dir."logo.gif", 5, 2, 80, 25 );
        
    $this -> PDF -> SetXY( 60, 8 );
    $this -> PDF -> MultiCell(280, 7, "INFORMACIÓN DEL DESPACHO\nNo.".$_REQUEST['despac'], 0, 'C' );
            
    $this -> PDF -> SetXY( 270, 24 );
    $this -> PDF -> SetFont( 'Arial', '', 10 );
    $this -> PDF -> Cell( 80, 5, "FECHA: ".$fecha, 0, 1, 'R', 0 );
    
    $this -> PDF -> Line( 5, 30, 349, 30 );
    
    $_IN_Y = $this -> DrawEncabezado();
    
    $this -> DrawPlanDeRuta( $_IN_Y );
    
    /**************************************************/
    
    $_file = '../'. $this -> BASE_DATOS .'/planos/Despacho_No_'.$_REQUEST['despac'].'.pdf';
    $this -> PDF -> Output( "../".$_file, 'F' );
    chmod( "../".$_file, 0777 );
    $this -> PDF -> Close();
    echo $_file;
  }
  
  function getTotalRutas( $despac )
  {
    $query = "SELECT a.cod_rutasx
	      FROM " . $this -> BASE_DATOS . ".tab_despac_seguim a
	     WHERE a.num_despac = " . $despac . "
		   GROUP BY 1";

    $consulta = new Consulta( $query, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  function getTotalMatriz( $camporder, $despac, $totrutas )
  {
    $query = "SELECT IF( ( 
                           SELECT z.nom_contro 
                             FROM " . $this -> BASE_DATOS . ".tab_genera_contro z,
                                  " . $this -> BASE_DATOS . ".tab_homolo_ealxxx y,
                                  " . $this -> BASE_DATOS . ".tab_homolo_trafico x
                            WHERE z.cod_contro = y.cod_pcxfar 
                              AND y.cod_pcxcli = x.cod_pcxbas
                              AND y.cod_tercer = x.cod_transp
                              AND x.cod_pcxfar = a.cod_contro
                              AND x.cod_rutfar = a.cod_rutasx
                              AND x.cod_transp = e.cod_transp
                            LIMIT 1
                           ) IS NULL, IF( c.ind_virtua = '1',CONCAT( c.nom_contro, ' (Virtual)' ), c.nom_contro ), 
                         ( SELECT z.nom_contro 
                             FROM " . $this -> BASE_DATOS . ".tab_genera_contro z,
                                  " . $this -> BASE_DATOS . ".tab_homolo_ealxxx y,
                                  " . $this -> BASE_DATOS . ".tab_homolo_trafico x
                            WHERE z.cod_contro = y.cod_pcxfar 
                              AND y.cod_pcxcli = x.cod_pcxbas
                              AND y.cod_tercer = x.cod_transp
                              AND x.cod_pcxfar = a.cod_contro
                              AND x.cod_rutfar = a.cod_rutasx
                              AND x.cod_transp = e.cod_transp
                            LIMIT 1
                          ) ),
						 DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ),
						 if(b.fec_noveda IS NOT NULL,DATE_FORMAT(b.fec_noveda,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_alarma,'%H:%i %d-%m-%Y')),
						 d.nom_noveda,DATE_FORMAT(b.fec_creaci,'%H:%i %d-%m-%Y'),
						 b.des_noveda,a.fec_planea,a.cod_contro, ";
    if (sizeof($totrutas) < 2)
        $query .= "a.fec_planea,";
    else
        $query .= "if(b.fec_noveda IS NOT NULL,b.fec_noveda,a." . $camporder . "),";
        
    $query .= "b.usr_creaci,a.fec_alarma,'indlink',c.ind_urbano,b.val_retras,
         b.fec_noveda as fec,c.ind_virtua,e.cod_transp,a.ind_estado,
         DATE_FORMAT( a.fec_planea, '%H:%i' )as hora, a.cod_rutasx, c.cod_colorx
          FROM " . $this -> BASE_DATOS . ".tab_despac_vehige e,
               " . $this -> BASE_DATOS . ".tab_genera_contro c,
               " . $this -> BASE_DATOS . ".tab_despac_seguim a LEFT JOIN
               " . $this -> BASE_DATOS . ".tab_despac_noveda b ON
               a.num_despac = b.num_despac AND
               a.cod_contro = b.cod_contro LEFT JOIN
               " . $this -> BASE_DATOS . ".tab_genera_noveda d ON
               b.cod_noveda = d.cod_noveda
         WHERE a.cod_contro = c.cod_contro AND
               a.num_despac = e.num_despac AND
               e.num_despac = " . $despac . " AND
               c.cod_contro = a.cod_contro AND 
               a.num_despac = e.num_despac";

    $query .= " ORDER BY a.fec_creaci, 7, b.fec_creaci";

    if ( sizeof( $totrutas ) < 2 )
        $query .= ",11,15";
            
    $consulta = new Consulta( $query, $this->conexion );

    return $consulta -> ret_matriz();
  }
  
  function getMatrizLink( $despac )
  {
    $query = "SELECT b.cod_contro,b.nom_contro, d.cod_rutasx
              FROM " . $this -> BASE_DATOS . ".tab_genera_contro b,
                   " . $this -> BASE_DATOS . ".tab_despac_seguim d,
                   " . $this -> BASE_DATOS . ".tab_despac_vehige e
             WHERE b.cod_contro = d.cod_contro AND
                   e.num_despac = d.num_despac AND
                   e.num_despac = " . $despac . " AND
                   d.ind_estado = 1";
        $query = $query . " ORDER BY d.fec_planea ";
        $consulta = new Consulta( $query, $this -> conexion );
        return $consulta -> ret_matriz();

  }
  
  function DrawPlanDeRuta ( $_IN_Y )
  {
    $observ = $this -> getObservaciones( $_REQUEST['despac'] );
    $seguim = $this -> getSeguimientos( $_REQUEST['despac'] );
    $totrutas = $this -> getTotalRutas( $_REQUEST['despac'] );
    $matrizlink = $this -> getMatrizLink( $_REQUEST['despac'] );

    if ( sizeof( $totrutas ) < 2 )
      $camporder = "fec_planea";
    else
      $camporder = "fec_alarma";
    
    $matriz = $this -> getTotalMatriz( $camporder, $_REQUEST['despac'], $totrutas );
    
    if(sizeof( $matriz ) > 0 )
    {
      $_IN_X = 5;
      $_ALTO = 6;
      
      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
      $this -> PDF -> Cell(344,$_ALTO,"INFORMACIÓN DEL PLAN DE RUTA",1,1,'L',1);
      
      $_IN_Y += $_ALTO;
      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
      $this -> PDF -> Cell(85,$_ALTO,"Sitio de Seguimiento",1,1,'C',1);
      $this -> PDF -> SetXY( $_IN_X+85, $_IN_Y );
      $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Prog.",1,1,'C',1);
      $this -> PDF -> SetXY( $_IN_X+125, $_IN_Y );
      $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Control",1,1,'C',1);
      $this -> PDF -> SetXY( $_IN_X+165, $_IN_Y );
      $this -> PDF -> Cell(25,$_ALTO,"Tiempo",1,1,'C',1);
      $this -> PDF -> SetXY( $_IN_X+190, $_IN_Y );
      $this -> PDF -> Cell(80,$_ALTO,"Novedad",1,1,'C',1);
      $this -> PDF -> SetXY( $_IN_X+270, $_IN_Y );
      $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Sistema",1,1,'C',1);
      $this -> PDF -> SetXY( $_IN_X+310, $_IN_Y );
      $this -> PDF -> Cell(34,$_ALTO,"Usuario",1,1,'C',1);
      
      for ( $i = 0; $i < sizeof( $matriz ); $i++ )
      {
        $asignado_variurb = 0;
        $nomost = 0;
        $x = 0;
        for ( $j = $i; $j < sizeof( $matriz ); $j++ )
        {
          if ( $matriz[$i][7] == $matriz[$j][7] )
          {
              $matriz[$j][11] = 1;
              for ( $k = $j - 1; $k > 0; $k-- )
                if ( $matriz[$j][7] == $matriz[$k][7] )
                  $matriz[$k][11] = 0;
          }
        }
      }
      
      
      $salida = $observ[0][4];
      $tiemdif = 0;
      $ini = 1;
      for ( $i = 0; $i < sizeof( $matriz ); $i++ )
      {
        $asignado_variurb = 0;
        $nomost = 0;
        $x = 0;
        $aux = "href";
          
        if($matriz[$i][20] != NULL)
          $RowColor = $matriz[$i][20];
        else
          $RowColor = "";
            
        for ( $j = 0; $j < sizeof( $matrizlink ); $j++ )
        {
          if (!$asignado_variurb)
          {
            $asignado_variurb = 1;
          }

          if ($matriz[$i][7] != $matrizlink[$j][0])
            if ($matriz[$i][3] == NULL)
            {
              $matriz[$i][2] = "";
              $nomost = 1;
            }

          if ( $matriz[$i][4] == NULL )
          {
            $matriz[$i][4] = "00:00:00 00-00-0000";

            if ( $matriz[$i][5] == NULL )
                $matriz[$i][5] = "No Reportado";
          }
        }
        $query = "SELECT a.cod_colorx,a.cant_tiempo
                    FROM " . $this -> BASE_DATOS . ".tab_genera_alarma a
                ORDER BY 2 ";

        $consulta = new Consulta( $query, $this->conexion );
        $timecolo = $consulta -> ret_matriz();

        $alarma_color = NULL;

        if ($matriz[$i][13])
        {
          if ( $matriz[$i][13] > 0 )
          {
            for ( $j = 0; $j < sizeof($timecolo); $j++ )
            {
              if ( $matriz[$i][13] <= $timecolo[$j][1] )
              {
                $alarma_color = $timecolo[$j][0];
                $j = sizeof( $timecolo );
              }
            }

            if (!$alarma_color)
              $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
          }
        }

        if (!$alarma_color)
            $alarma_color = "FFFFFF";
              
        if($matriz[$i][20] != NULL)
          $RowColor = $matriz[$i][20];
        else
          $RowColor2 = "D8DFEA";
            
        if ( $sit != $matriz[$i][7] )
        {  
          $query = "SELECT IF( UPPER( IF( a.cod_noveda = 4999, REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) ) IS NULL, d.nom_contro, UPPER( IF( a.cod_noveda = 4999, REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) )) AS nom_sitiox,
                           DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y'),b.nom_noveda,
                           DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),a.usr_creaci,a.val_retras,a.fec_creaci as fec
                      FROM " . $this -> BASE_DATOS . ".tab_despac_contro a,
                           " . $this -> BASE_DATOS . ".tab_genera_noveda b,
                           " . $this -> BASE_DATOS . ".tab_despac_sitio c,
                           " . $this -> BASE_DATOS . ".tab_genera_contro d
                     WHERE a.cod_noveda=b.cod_noveda AND 
                           a.cod_sitiox=c.cod_sitiox AND 
                           a.cod_contro = d.cod_contro AND
                           a.num_despac ='".$_REQUEST['despac']."' AND 
                           a.cod_contro ='" . $matriz[$i][7] . "' AND
                           a.cod_rutasx = '" . $matriz[$i]['cod_rutasx'] . "'
                  ORDER BY a.fec_creaci";
          $consulta = new Consulta( $query, $this -> conexion );
          $sitios = $consulta -> ret_matriz();

          $sit = $matriz[$i][7];
            
            
          for ( $z = 0; $z < sizeof($sitios); $z++ )
          {
            if ($ini == 1)
            {
                $query = "SELECT TIMEDIFF(  '" . $sitios[$z]["fec"] . "' ,'$salida' ) ";
                $ini = 0;
            }
            else
            {
                $query = "SELECT TIMEDIFF( '" . $sitios[$z]["fec"] . "','$salida'  ) ";
            }
            $consulta = new Consulta($query, $this->conexion);
            $tiemdif = $consulta->ret_matriz();
            $tiemdif = explode(":", $tiemdif[0][0]);

            if ($sitios[$z]["fec"] != "")
                $salida = $sitios[$z]["fec"];
                  
            
            $tiemdif = $tiemdif[0] * 60 + $tiemdif[1];
            
            $this -> PDF -> SetFillColor( 216, 223, 234 );
            $_IN_Y += $_ALTO;
            $this -> PDF -> SetFont('Arial','',9);
            $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
            $this -> PDF -> Cell(85,$_ALTO,$sitios[$z][0],1,1,'L',1);
            $this -> PDF -> SetXY( $_IN_X+85, $_IN_Y );
            $this -> PDF -> Cell(40,$_ALTO,$matriz[$i][1],1,1,'L',1);
            $this -> PDF -> SetXY( $_IN_X+125, $_IN_Y );
            $this -> PDF -> Cell(40,$_ALTO,$sitios[$z][1],1,1,'L',1);
            $this -> PDF -> SetXY( $_IN_X+165, $_IN_Y );
            $this -> PDF -> Cell(25,$_ALTO,number_format($tiemdif) . "Min(s)",1,1,'L',1);
            $this -> PDF -> SetXY( $_IN_X+190, $_IN_Y );
            $this -> PDF -> Cell(80,$_ALTO,$sitios[$z][2],1,1,'L',1);
            $this -> PDF -> SetXY( $_IN_X+270, $_IN_Y );
            $this -> PDF -> Cell(40,$_ALTO,$sitios[$z][3],1,1,'L',1);
            $this -> PDF -> SetXY( $_IN_X+310, $_IN_Y );
            $this -> PDF -> Cell(34,$_ALTO,$sitios[$z][4],1,1,'L',1);
            $this -> PDF -> SetFillColor(235);
            if( $_IN_Y >= 185 )
            {
              $_IN_Y = 5;
              $this -> PDF -> AddPage();
              $this -> PDF -> SetFont('Arial','B',10);
              $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
              $this -> PDF -> Cell(344,$_ALTO,"INFORMACIÓN DEL PLAN DE RUTA",1,1,'L',1);
              
              $_IN_Y += $_ALTO;
              $this -> PDF -> SetFont('Arial','B',10);
              $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
              $this -> PDF -> Cell(85,$_ALTO,"Sitio de Seguimiento",1,1,'C',1);
              $this -> PDF -> SetXY( $_IN_X+85, $_IN_Y );
              $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Prog.",1,1,'C',1);
              $this -> PDF -> SetXY( $_IN_X+125, $_IN_Y );
              $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Control",1,1,'C',1);
              $this -> PDF -> SetXY( $_IN_X+165, $_IN_Y );
              $this -> PDF -> Cell(25,$_ALTO,"Tiempo",1,1,'C',1);
              $this -> PDF -> SetXY( $_IN_X+190, $_IN_Y );
              $this -> PDF -> Cell(80,$_ALTO,"Novedad",1,1,'C',1);
              $this -> PDF -> SetXY( $_IN_X+270, $_IN_Y );
              $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Sistema",1,1,'C',1);
              $this -> PDF -> SetXY( $_IN_X+310, $_IN_Y );
              $this -> PDF -> Cell(34,$_ALTO,"Usuario",1,1,'C',1);
            }
          }
        }
        
        if ($matriz[$i][3] != "NULL")
        {
          if ($ini == 1)
          {
            $query = "SELECT TIMEDIFF(  '" . $matriz[$i]["fec"] . "','$salida' ) ";
            $ini = 0;
          }
          else
          {
            $query = "SELECT TIMEDIFF( '" . $matriz[$i]["fec"] . "','$salida' ) ";
          }
          $consulta = new Consulta($query, $this->conexion);
          $tiemdif = $consulta->ret_matriz();
          $tiemdif = explode(":", $tiemdif[0][0]);
          
          if ($matriz[$i]["fec"] != "")
              $salida = $matriz[$i]["fec"];
          
          $tiemdif = $tiemdif[0] * 60 + $tiemdif[1];
          $tiemdif = number_format($tiemdif) . "Min(s)";
        }
        else
        {
            $tiemdif = "";
        }
          
        if($matriz[$i][3] == NULL )
        {
          if($matriz[$i][20] == NULL)
             $RowColor = '';
          else
             $RowColor = $matriz[$i][20];
        }
        else 
        {
          $estilo = "#D8DFEA";
        }
 
        if ($matriz[$i][3] == NULL)
        {
            $matriz[$i][4] = "";
            $matriz[$i][9] = "";
            $tiemdif = "";
        }
        
        $ind_fill = $matriz[$i][3] != NULL ? 1 : 0;
        $this -> PDF -> SetFillColor( 216, 223, 234 );
        $_IN_Y += $_ALTO;
        $this -> PDF -> SetFont('Arial','',9);
        $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
        $this -> PDF -> Cell(85,$_ALTO,$matriz[$i][0],1,1,'L',$ind_fill);
        $this -> PDF -> SetXY( $_IN_X+85, $_IN_Y );
        $this -> PDF -> Cell(40,$_ALTO,$matriz[$i][1],1,1,'L',$ind_fill);
        $this -> PDF -> SetXY( $_IN_X+125, $_IN_Y );
        $this -> PDF -> Cell(40,$_ALTO,$matriz[$i][2],1,1,'L',$ind_fill);
        $this -> PDF -> SetXY( $_IN_X+165, $_IN_Y );
        $this -> PDF -> Cell(25,$_ALTO,$tiemdif,1,1,'L',$ind_fill);
        $this -> PDF -> SetXY( $_IN_X+190, $_IN_Y );
        $this -> PDF -> Cell(80,$_ALTO,$matriz[$i][3],1,1,'L',$ind_fill);
        $this -> PDF -> SetXY( $_IN_X+270, $_IN_Y );
        $this -> PDF -> Cell(40,$_ALTO,$matriz[$i][4],1,1,'L',$ind_fill);
        $this -> PDF -> SetXY( $_IN_X+310, $_IN_Y );
        $this -> PDF -> Cell(34,$_ALTO,$matriz[$i][9],1,1,'L',$ind_fill);
        $this -> PDF -> SetFillColor(235);
        if( $_IN_Y >= 185 )
        {
          $_IN_Y = 5;
          $this -> PDF -> AddPage();
          $this -> PDF -> SetFont('Arial','B',10);
          $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
          $this -> PDF -> Cell(344,$_ALTO,"INFORMACIÓN DEL PLAN DE RUTA",1,1,'L',1);
          
          $_IN_Y += $_ALTO;
          $this -> PDF -> SetFont('Arial','B',10);
          $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
          $this -> PDF -> Cell(85,$_ALTO,"Sitio de Seguimiento",1,1,'C',1);
          $this -> PDF -> SetXY( $_IN_X+85, $_IN_Y );
          $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Prog.",1,1,'C',1);
          $this -> PDF -> SetXY( $_IN_X+125, $_IN_Y );
          $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Control",1,1,'C',1);
          $this -> PDF -> SetXY( $_IN_X+165, $_IN_Y );
          $this -> PDF -> Cell(25,$_ALTO,"Tiempo",1,1,'C',1);
          $this -> PDF -> SetXY( $_IN_X+190, $_IN_Y );
          $this -> PDF -> Cell(80,$_ALTO,"Novedad",1,1,'C',1);
          $this -> PDF -> SetXY( $_IN_X+270, $_IN_Y );
          $this -> PDF -> Cell(40,$_ALTO,"Hora/Fecha Sistema",1,1,'C',1);
          $this -> PDF -> SetXY( $_IN_X+310, $_IN_Y );
          $this -> PDF -> Cell(34,$_ALTO,"Usuario",1,1,'C',1);
        }
      } 
    }
  }
  
  function getSeguimientos( $despac )
  {
    $query = "(SELECT b.nom_contro,c.nom_noveda,a.tiem_duraci,a.obs_contro as des,
                      DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y') as fec,a.usr_creaci,a.val_retras,d.nom_sitiox as nom_sitiox,
                      a.fec_contro
                FROM  " . $this -> BASE_DATOS . ".tab_genera_contro b,
                      " . $this -> BASE_DATOS . ".tab_genera_noveda c, 
                      " . $this -> BASE_DATOS . ".tab_despac_contro a LEFT JOIN " . $this -> BASE_DATOS . ".tab_despac_sitio d ON a.cod_sitiox = d.cod_sitiox  
                WHERE a.cod_contro = b.cod_contro AND
                      a.cod_noveda = c.cod_noveda AND
                      a.num_despac = " . $despac . " AND 
                      a.obs_contro != '')
              UNION
              (SELECT b.nom_contro,c.nom_noveda,a.tiem_duraci,a.des_noveda as des,
                      DATE_FORMAT(a.fec_noveda,'%H:%i %d-%m-%Y') as fec,a.usr_creaci,a.val_retras,b.nom_contro as nom_sitiox,
                      a.fec_noveda
                FROM  " . $this -> BASE_DATOS . ".tab_genera_contro b,
                      " . $this -> BASE_DATOS . ".tab_genera_noveda c,
                      " . $this -> BASE_DATOS . ".tab_despac_noveda a 
                WHERE a.cod_contro = b.cod_contro AND
                      a.cod_noveda = c.cod_noveda AND
                      a.num_despac = " . $despac . " AND 
                      a.des_noveda != '')
    ORDER BY 9 DESC ";
    
    $consulta = new Consulta( $query, $this -> conexion );
    return $consulta -> ret_matriz();
  
  }
  
  function getObservaciones( $despac )
  {
    $query = "SELECT a.obs_despac,b.obs_medcom,b.obs_proesp,a.obs_llegad, a.fec_salida 
                FROM " . $this -> BASE_DATOS . ".tab_despac_despac a,
                     " . $this -> BASE_DATOS . ".tab_despac_vehige b
               WHERE a.num_despac = b.num_despac AND
                     a.num_despac = " . $despac . " ";
    $consulta = new Consulta( $query, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  function DrawEncabezado()
  {
    $_ENCABEZADO = $this -> getHeader( $_REQUEST );
    $_IN_X = 5;
    $_IN_Y = 33;
    $_ALTO = 6;
    $this -> PDF -> SetDrawColor(0);
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(344,$_ALTO,"INFORMACIÓN DEL DESPACHO",1,1,'L',1);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Manifiesto",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['cod_manifi'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Agencia",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_agenci'],1,1,'L',0);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Orígen",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_ciuori'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Destino",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_ciudes'],1,1,'L',0);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Ruta",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',7);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_rutaxx'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Transportadora",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_transp'],1,1,'L',0);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Conductor",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_conduc'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Número Documento",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['doc_conduc'],1,1,'L',0);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Calificación",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['des_califi'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Celular",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['num_telmov'],1,1,'L',0);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Teléfono",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['num_telefo'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Fecha Salida",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['fec_salida'],1,1,'L',0);
    
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Fecha Creación",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['fec_creaci'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Fecha Planeada Llegada",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['fec_llegpl'],1,1,'L',0);
     
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Placa",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['num_placax'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Marca",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_marcax'],1,1,'L',0);
     
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Configuración",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['des_config'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Línea",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_lineax'],1,1,'L',0);
     
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Carrocería",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_carroc'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Color",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_colorx'],1,1,'L',0);
     
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Modelo",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['num_modelo'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Operador GPS",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_opegps'],1,1,'L',0);
     
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Usuario",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_usuari'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Contraseña GPS",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['gps_contra'],1,1,'L',0);
     
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"No. Novedades",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['num_noveda'],1,1,'L',0);

    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"Aseguradora",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_asegur'],1,1,'L',0);
   
    $_IN_Y += $_ALTO;
    $this -> PDF -> SetFont('Arial','B',10);
    $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
    $this -> PDF -> Cell(45,$_ALTO,"No. Póliza",1,1,'R',1);

    $this -> PDF -> SetFont('Arial','',10);
    $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
    $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['num_poliza'],1,1,'L',0);

    if( $_ENCABEZADO['ind_poliza'] == 1 )
    {
      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
      $this -> PDF -> Cell(45,$_ALTO,"Tomador Póliza",1,1,'R',1);

      $this -> PDF -> SetFont('Arial','',10);
      $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
      $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['tom_poliza'],1,1,'L',0);

      $_IN_Y += $_ALTO;
      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
      $this -> PDF -> Cell(45,$_ALTO,"Valor Declarado",1,1,'R',1);

      $this -> PDF -> SetFont('Arial','',10);
      $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
      $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['val_declar'],1,1,'L',0);

      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
      $this -> PDF -> Cell(45,$_ALTO,"Mercancía",1,1,'R',1);

      $this -> PDF -> SetFont('Arial','',10);
      $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
      $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['nom_mercan'],1,1,'L',0);   
    }
    
    if( $_ENCABEZADO['ind_tiemod'] == 1 )
    {
      $_IN_Y += $_ALTO;
      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X, $_IN_Y );
      $this -> PDF -> Cell(45,$_ALTO,"Tiempo Modificado",1,1,'R',1);

      $this -> PDF -> SetFont('Arial','',10);
      $this -> PDF -> SetXY( $_IN_X+45, $_IN_Y );
      $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['tie_modifi'],1,1,'L',0);

      $this -> PDF -> SetFont('Arial','B',10);
      $this -> PDF -> SetXY( $_IN_X+172, $_IN_Y );
      $this -> PDF -> Cell(45,$_ALTO,"Observaciones Modificación",1,1,'R',1);

      $this -> PDF -> SetFont('Arial','',10);
      $this -> PDF -> SetXY( $_IN_X+217, $_IN_Y );
      $this -> PDF -> Cell(127,$_ALTO,$_ENCABEZADO['obs_modifi'],1,1,'L',0);   
    }
    return $_IN_Y += $_ALTO + 2;
  }
  
  function getHeader( $mData )
  {
    $_ENCABEZADO = array();
    $mQuery = "SELECT a.num_despac, a.cod_manifi, 
                      IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                      IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ),
                      IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                      e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod
                 FROM " . $this -> BASE_DATOS . ".tab_despac_despac a,
                      " . $this -> BASE_DATOS . ".tab_despac_vehige b,
                      " . $this -> BASE_DATOS . ".tab_tercer_tercer c,
                      " . $this -> BASE_DATOS . ".tab_tercer_conduc d 
            LEFT JOIN ".  $this -> BASE_DATOS .".tab_genera_califi f ON f.cod_califi = d.cod_califi
            LEFT JOIN ".  $this -> BASE_DATOS .".tab_operad_operad e ON e.cod_operad = d.cod_operad
                WHERE a.num_despac = b.num_despac AND
                      b.cod_conduc = c.cod_tercer AND
                      d.cod_tercer = c.cod_tercer AND
                      a.num_despac = " . $mData['despac'] . " ";
                      
    $consulta = new Consulta( $mQuery, $this -> conexion );
    $encab1 = $consulta -> ret_matriz();
    $mQuery = "SELECT b.num_placax,d.nom_marcax,e.nom_lineax,f.nom_colorx,
                      g.nom_config,h.nom_carroc,c.ano_modelo
                 FROM " . $this -> BASE_DATOS . ".tab_despac_vehige b,
                      " . $this -> BASE_DATOS . ".tab_genera_marcas d,
                      " . $this -> BASE_DATOS . ".tab_vehige_lineas e,
                      " . $this -> BASE_DATOS . ".tab_vehige_colore f,
                      " . $this -> BASE_DATOS . ".tab_vehige_carroc h,
                      " . $this -> BASE_DATOS . ".tab_vehicu_vehicu c LEFT JOIN " . $this -> BASE_DATOS . ".tab_vehige_config g ON c.num_config = g.num_config 
                WHERE b.num_placax = c.num_placax AND
                      c.cod_marcax = d.cod_marcax AND
                      c.cod_marcax = e.cod_marcax AND
                      c.cod_lineax = e.cod_lineax AND
                      c.cod_colorx = f.cod_colorx AND
                      c.cod_carroc = h.cod_carroc AND
                      b.num_despac = " . $mData['despac'] . " ";

    $consulta = new Consulta( $mQuery, $this -> conexion );
    $encab2 = $consulta -> ret_matriz();

    $mQuery = "SELECT g.nom_agenci,e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                      if(a.fec_salida Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y')),
                      if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
                      DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y'),
                      a.gps_operad,a.gps_usuari,a.gps_paswor,DATE_FORMAT(a.fec_salsis,'%H:%i %d-%m-%Y'),
                      b.cod_transp, h.abr_tercer AS aseguradora, a.num_poliza, i.nom_operad AS nom_opegps
                 FROM " . $this -> BASE_DATOS . ".tab_despac_vehige b,
                      " . $this -> BASE_DATOS . ".tab_genera_rutasx e,
                      " . $this -> BASE_DATOS . ".tab_genera_agenci g,
                      " . $this -> BASE_DATOS . ".tab_despac_despac a LEFT JOIN
                      " . $this -> BASE_DATOS . ".tab_despac_gpsxxx k ON a.num_despac = k.num_despac 
            LEFT JOIN " . $this -> CENTRAL  . ".tab_genera_opegps i ON k.cod_opegps = i.cod_operad
            LEFT JOIN " . $this -> BASE_DATOS . ".tab_tercer_tercer h ON a.cod_asegur = h.cod_tercer 
                WHERE a.num_despac = b.num_despac AND
                      b.cod_rutasx = e.cod_rutasx AND
                      b.cod_agenci = g.cod_agenci AND
                      a.num_despac = " . $mData['despac'] . " "; 

    $consulta = new Consulta( $mQuery, $this -> conexion );
    $encab3 = $consulta -> ret_matriz();
    
    $query0 = "SELECT c.abr_tercer, a.val_declar, a.nom_mercan, a.num_poliza
                 FROM " . $this -> BASE_DATOS . ".tab_despac_poliza a,
                      " . $this -> BASE_DATOS . ".tab_despac_despac b,
                      " . $this -> BASE_DATOS . ".tab_tercer_tercer c
                WHERE a.num_despac = b.num_despac
                  AND a.cod_tomado = c.cod_tercer
                  AND a.num_despac = '". $mData['despac'] ."' ";

    $consulta = new Consulta( $query0, $this -> conexion );
    $encab99 = $consulta -> ret_matriz();
   
    /**********************************************************/
    $origen = $this -> getCiudad( $encab3[0][2] );
    $destin = $this -> getCiudad( $encab3[0][3] );
    $transp = $this -> getTransportadora( $mData['despac'] );
    $novedades = $this -> getNovedades( $mData['despac'] );
    $tipfactur = $this -> getTipFactur( $transp[1] );
    $_ENCABEZADO['ind_poliza'] = 0;
    $_ENCABEZADO['ind_tiemod'] = 0;
    if( count( $encab99 ) > 0 )
    {
			$encab3[0]["num_poliza"] = $encab99[0]["num_poliza"];
      $_ENCABEZADO['ind_poliza'] = 1;
		}
    if( $encab1[0][10] )
		{
			$_ENCABEZADO['ind_tiemod'] = 1;
			$_ENCABEZADO['tie_modifi'] = $encab1[0][9]." min(s).";
			$_ENCABEZADO['obs_modifi'] = $encab1[0][11];
		}
    
    $_ENCABEZADO['cod_manifi'] = $encab1[0][1];   //MANIFIESTO
    $_ENCABEZADO['nom_ciuori'] = $origen[1];      //ORIGEN
    $_ENCABEZADO['nom_ciudes'] = $destin[1];      //DESTINO
    $_ENCABEZADO['nom_agenci'] = $encab3[0][0];   //AGENCIA
    $_ENCABEZADO['nom_transp'] = $transp[0];      //TRANSPORTADORA
    $_ENCABEZADO['nom_rutaxx'] = $encab3[0][1];   //RUTA
    $_ENCABEZADO['doc_conduc'] = $encab1[0][3];   //CONDUCTOR
    $_ENCABEZADO['nom_conduc'] = $encab1[0][2];   //CONDUCTOR
    $_ENCABEZADO['fec_sistem'] = $encab3[0][11];   //H/F SISTEMA
    $_ENCABEZADO['des_califi'] = $encab1[0][8] != '' ? $encab1[0][8] : "--";   //CALIFICACION
    $_ENCABEZADO['fec_salida'] = $encab3[0][4];   //H/F SALIDA
    $_ENCABEZADO['fec_llegpl'] = $encab3[0][5];   //H/F PLANEADA LLEGADA
    $_ENCABEZADO['num_telmov'] = $encab1[0][7]." - ".$encab1[0][4];   //CELULAR
    $_ENCABEZADO['fec_creaci'] = $encab3[0][6];   //H/F CREACION
    $_ENCABEZADO['num_telefo'] = $encab1[0][5];   //H/F CREACION
    $_ENCABEZADO['num_placax'] = $encab2[0][0];   //PLACA
    $_ENCABEZADO['nom_marcax'] = $encab2[0][1];   //MARCA
    $_ENCABEZADO['des_config'] = $encab2[0][4];   //CONFIGURACION
    $_ENCABEZADO['nom_lineax'] = $encab2[0][2];   //LINEA
    $_ENCABEZADO['nom_carroc'] = $encab2[0][5];   //CARROCERIA
    $_ENCABEZADO['nom_colorx'] = $encab2[0][3];   //COLOR
    $_ENCABEZADO['num_modelo'] = $encab2[0][6];   //MODELO
    $_ENCABEZADO['nom_opegps'] = trim($encab3[0][8]) != '' && $encab3[0][8] != NULL ? $encab3[0][8] : $encab3[0]['nom_opegps'] ;   //GPS
    $_ENCABEZADO['gps_contra'] = $encab3[0][10];   //CONTRASEÑA GPS
    $_ENCABEZADO['nom_usuari'] = $encab3[0][9];   //USUARIO
    $_ENCABEZADO['num_noveda'] = sizeof( $novedades ).$tipfactur;   
    $_ENCABEZADO['num_poliza'] = $encab3[0]["num_poliza"];   
    $_ENCABEZADO['nom_asegur'] = $encab3[0]["aseguradora"];   
    $_ENCABEZADO['tom_poliza'] = $encab99[0]["abr_tercer"];   
    $_ENCABEZADO['val_declar'] = $encab99[0]["val_declar"];   
    $_ENCABEZADO['nom_mercan'] = $encab99[0]["nom_mercan"];   

    /**********************************************************/
    
    return $_ENCABEZADO;
  }
  
  function getTipFactur( $transp )
  {
    $subquery = "SELECT MAX(num_consec)
                   FROM ". $this -> BASE_DATOS .".tab_transp_tipser
                  WHERE ind_estado = '1' AND
                  cod_transp = '".$transp."' ";

    $query = "SELECT tip_factur
    FROM ". $this -> BASE_DATOS .".tab_transp_tipser
    WHERE ind_estado = '1' AND
    num_consec = (".$subquery.") AND
    cod_transp = '".$transp."' ";


    $consul = new Consulta($query, $this -> conexion);
    $consul = $consul -> ret_matriz();
    $tipfactur = $consul[0][0];
    if( $tipfactur == '0')
    $tipfactur = " ";
    elseif( $tipfactur == '1')
    $tipfactur = " - (Por Despacho)";
    elseif( $tipfactur == '2')
    $tipfactur = " - (Por Registro)";
    
    return $tipfactur;
  }
  
  function getNovedades( $despac )
  {
    $sql = "(SELECT b.num_despac
              FROM  " . $this -> BASE_DATOS . ".tab_despac_contro b
              WHERE b.num_despac = '".$despac."') 
        UNION ALL
            (SELECT b.num_despac
               FROM " . $this -> BASE_DATOS . ".tab_despac_noveda b
              WHERE b.num_despac = '".$despac."')
      ORDER BY 1";
    $consulta = new Consulta( $sql, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  function getTransportadora( $despac )
  {
    $mQuery = "SELECT nom_tercer, b.cod_tercer
         FROM " . $this -> BASE_DATOS . ".tab_despac_vehige a,
              " . $this -> BASE_DATOS . ".tab_tercer_tercer b
        WHERE a.cod_transp = b.cod_tercer
          AND a.num_despac = " . $despac . "";

    $consulta = new Consulta( $mQuery, $this -> conexion );
    $transp = $consulta -> ret_matriz();
    return $transp[0];
  }
        
  function getCiudad( $ciudad )
  {
    $mQuery = "SELECT a.cod_ciudad, CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad)
                FROM " . $this -> BASE_DATOS . ".tab_genera_ciudad a,
                     " . $this -> BASE_DATOS . ".tab_genera_depart b,
                     " . $this -> BASE_DATOS . ".tab_genera_paises c
               WHERE a.cod_depart = b.cod_depart AND
                     a.cod_paisxx = b.cod_paisxx AND
                     b.cod_paisxx = c.cod_paisxx AND
                     a.cod_ciudad = '" . $ciudad . "'";

    $mQuery .=" GROUP BY 1 ORDER BY 2";

    $consulta = new Consulta( $mQuery, $this -> conexion );
    $ciudades = $consulta -> ret_matriz();

    return $ciudades[0];
  }

}

$ajax = new AjaxPDF();

?>
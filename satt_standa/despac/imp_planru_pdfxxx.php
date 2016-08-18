<?php
session_start();

class PDFPlanRuta
{
  var $conexion;
  
  function __construct( $_AJAX )
  {  
    
    $_AJAX['num_despac'] = $_SESSION['num_despac'];
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );
    include_once('../lib/FPDF/fpdf.php');
    include_once('../lib/general/constantes.inc');
    include_once('../../satt_faro/constantes.inc');

    $this -> conexion = $AjaxConnection;
    $OP = base64_decode( $_AJAX['option'] );
    
    $this -> $OP( $_AJAX );
  }
  
  function pdfCorona( $_AJAX )
  {
    $mData = $this -> getDataDespac( $_AJAX['num_despac'] );
    
    /* CREACION PDF */
    ob_clean();
    $pdf = new FPDF();
    
    $pdf -> AddPage('L','Letter');
    /*   LOGO PLAN DE RUTA  */
    
    $pdf -> Image('../../'. $mData['dir_logoxx'], 5, 5, 70 );
      
    $_IN_X = 10;
    $_IN_Y = 5;
    $_ALTO = 5;
   
    $pdf -> SetFont('Arial','B',10);
    $pdf -> SetXY(80, $_IN_Y );
    $pdf -> Cell(90,$_ALTO,substr( $mData['nom_transp'], 0, 30 ),0,0,'C');
    $pdf -> SetXY(80, $_IN_Y+$_ALTO );
    $pdf -> Cell(90,$_ALTO,substr( $mData['nom_transp'], 30, 50 ),0,0,'C');
    
    $_IN_Y += $_ALTO*2;
    $pdf -> SetFont('Arial','B',9);
    $pdf -> SetXY(80, $_IN_Y );
    $pdf -> Cell(90,$_ALTO,$mData['nit_transp'],0,0,'C');
    
    $_IN_Y += $_ALTO;
    $pdf -> SetXY(80, $_IN_Y );
    $pdf -> Cell(90,$_ALTO,substr( $mData['dir_transp'], 0, 44 ),0,0,'C');
    $_IN_Y += $_ALTO;
    $pdf -> SetXY(80, $_IN_Y );
    $pdf -> Cell(90,$_ALTO,substr( $mData['dir_transp'], 44, 100 ),0,0,'C');

    $_IN_Y += $_ALTO;
    $pdf -> SetXY(80, $_IN_Y );
    $pdf -> Cell(90,$_ALTO,$mData['tel_transp'],0,0,'C');
    
    $_IN_Y += $_ALTO+1;
    $pdf -> SetFont('Arial','B',14);
    $pdf -> SetXY(80, $_IN_Y );
    $pdf -> Cell(90,7,"PLAN DE RUTA",1,0,'C');      
   
    $_IN_Y = 5;
    $pdf -> SetFont('Arial','',9);
    $pdf -> SetXY(173, $_IN_Y );
    $pdf -> Cell(44,$_ALTO,"FOTO DEL VEHÍCULO",0,0,'C');
    $pdf -> SetXY(174, $_IN_Y );
    $pdf -> Cell(42,38,"",1,0,'C');
    $pdf -> Image('../../'.$mData['img_vehicu'],175 ,9,40,33);
    
    
    $pdf -> SetFont('Arial','',9);
    $pdf -> SetXY(220, $_IN_Y );
    $pdf -> Cell(44,$_ALTO,"FOTO DEL CONDUCTOR",0,0,'C');
    $pdf -> SetXY(221, $_IN_Y );
    $pdf -> Cell(42,38,"",1,0,'C');
    $pdf -> Image('../../'.$mData['img_conduc'],222 ,9,40,33);
    $_IN_Y = 45;
    $pdf -> SetFont('Arial','B',9);
    $pdf -> SetXY(10, $_IN_Y );
    $pdf -> Cell(253,$_ALTO,"DATOS DEL DESPACHO No.".$mData['num_despac'],1,0,'C');
    
    $_IN_Y += $_ALTO;
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf->Cell(25,$_ALTO,"DOCUMENTO No.",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+25, $_IN_Y );
    $pdf->Cell(25,$_ALTO,$mData['cod_manifi'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+50, $_IN_Y );
    $pdf->Cell(15,$_ALTO,"ORIGEN: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+65, $_IN_Y );
    $pdf->Cell(55,$_ALTO,$mData['nom_ciuori'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+120, $_IN_Y );
    $pdf->Cell(15,$_ALTO,"DESTINO: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+135, $_IN_Y );
    $pdf->Cell(55,$_ALTO,$mData['nom_ciudes'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+190, $_IN_Y );
    $pdf->Cell(25,$_ALTO,"MODALIDAD: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+215, $_IN_Y );
    $pdf->Cell(38,$_ALTO,$mData['nom_tipdes'],'RTB',0,'L');
      
    $_IN_Y += $_ALTO;
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf->Cell(12,$_ALTO,"PLACA: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+12, $_IN_Y );
    $pdf->Cell(18,$_ALTO,$mData['num_placax'],'RTB',0,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+30, $_IN_Y );
    $pdf->Cell(12,$_ALTO,"MARCA: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+42, $_IN_Y );
    $pdf->Cell(32,$_ALTO,$mData['nom_marcax'],'RTB',0,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+74, $_IN_Y );
    $pdf->Cell(15,$_ALTO,"MODELO: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+89, $_IN_Y );
    $pdf->Cell(10,$_ALTO,$mData['num_modelo'],'RTB',0,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+99, $_IN_Y );
    $pdf->Cell(12,$_ALTO,"COLOR: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+111, $_IN_Y );
    $pdf->Cell(25,$_ALTO,$mData['num_colorx'],'RTB',0,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+136, $_IN_Y );
    $pdf->Cell(21,$_ALTO,"CARROCERIA: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',6);
    $pdf -> SetXY($_IN_X+157, $_IN_Y );
    $pdf->Cell(22,$_ALTO,$mData['cod_carroc'],'RTB',0,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+179, $_IN_Y );
    $pdf->Cell(24,$_ALTO,"No. REMOLQUE: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+203, $_IN_Y );
    $pdf->Cell(12,$_ALTO,$mData['num_remolq'],'RTB',0,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+215, $_IN_Y );
    $pdf->Cell(10,$_ALTO,"LÍNEA: ",'LTB',0,'L');

    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+225, $_IN_Y );
    $pdf->Cell(28,$_ALTO,$mData['nom_lineax'],'RTB',0,'L');

    $_IN_Y += $_ALTO;
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf->Cell(20,$_ALTO,"CONDUCTOR: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+20, $_IN_Y );
    $pdf->Cell(67,$_ALTO,$mData['nom_conduc'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+87, $_IN_Y );
    $pdf->Cell(6,$_ALTO,"C.C. ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+93, $_IN_Y );
    $pdf->Cell(18,$_ALTO,$mData['num_conduc'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+111, $_IN_Y );
    $pdf->Cell(40,$_ALTO,"LICENCIA DE CONDUCCIÓN:",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+151, $_IN_Y );
    $pdf->Cell(28,$_ALTO,$mData['lic_conduc'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+179, $_IN_Y );
    $pdf->Cell(19,$_ALTO,"CATEGORÍA:",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+198, $_IN_Y );
    $pdf->Cell(17,$_ALTO,$mData['cat_liccon'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+215, $_IN_Y );
    $pdf->Cell(8,$_ALTO,"TEL: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+223, $_IN_Y );
    $pdf->Cell(30,$_ALTO,$mData['con_telmov'],'RTB',0,'L');
    
    $_IN_Y += $_ALTO;
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf->Cell(50,$_ALTO,"FECHA DE SALIDA PROGRAMADA: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+50, $_IN_Y );
    $pdf->Cell(45,$_ALTO,$mData['fec_salpla'],'RTB',0,'L');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+95, $_IN_Y );
    $pdf->Cell(53,$_ALTO,"FECHA DE LLEGADA PROGRAMADA: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+148, $_IN_Y );
    $pdf->Cell(42,$_ALTO,$mData['fec_llegpl'],'RTB',0,'L');
   
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetXY($_IN_X+190, $_IN_Y );
    $pdf->Cell(23,$_ALTO,"VALOR MULTA: ",'LTB',0,'L');
    
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X+213, $_IN_Y );
    $pdf->Cell(40,$_ALTO,"$ ".$mData['val_multax'],'RTB',0,'L');
    
    $_IN_Y += $_ALTO+2;
    
    $pdf -> SetFont('Arial','B',10);
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf->Cell(253,6,"PUESTOS DE CONTROL",1,0,'C');
    
    $_IN_Y += 6;
    $_ANCHO = 50.6;
    $_ALTO = 27;
    
    $l=0;
    for( $i = 0, $k = 0; $i < 3; $i++ )
    {
      for( $j = 0; $j < 5; $j++ )
      {
        $_OBSERV = str_replace( "<small><br>", "\n", $mData['pue_contro'][$k++] );
        $_OBSERV = str_replace( "<br>", "\n", $_OBSERV );
        $_OBSERV = str_replace( "</small>", "", $_OBSERV );
        $pdf -> SetFont('Arial','',8);
        $pdf -> SetXY($_IN_X, $_IN_Y+1 );
        $pdf -> MultiCell( $_ANCHO, 3, $_OBSERV,0 ,'C' );
        $pdf -> SetXY($_IN_X, $_IN_Y );
        $pdf -> Cell($_ANCHO,$_ALTO,"",1,0,'C');
        $_IN_X += $_ANCHO;
      }
      $_IN_X = 10;
      $_IN_Y += $_ALTO;
    }
    $_IN_Y += 2;
    
    $pdf -> SetFont('Arial','B',7);
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf -> Cell(253,4,"OBSERVACIONES",'LRT',0,'L');
    
    $arr_observ = array( 
                  '&Aacute;'=>'Á',
                  '&Eacute;'=>'É',
                  '&Iacute;'=>'Í',
                  '&Oacute;'=>'Ó',
                  '&Uacute;'=>'Ú',
                  '&Ntilde;'=>'Ñ',
                  "<br><br><ul style='margin: 0; padding: 0; font-size: 9px; font-weight: bold;'><li>" => "\n",
                  "***<br>**" => "***\n**",
                  "</li></ul>" => " "
                  );
      
    $OBSERV = strtr( $mData['obs_despac'], $arr_observ );

    
    $pdf -> SetFont('Arial','',7);
    $_IN_Y += 4;
    $pdf -> SetXY($_IN_X, $_IN_Y );
    $pdf -> MultiCell(253,3,$OBSERV,'LRB','L');
    
    $_IN_Y = $pdf->GetY()+2;
    $pdf -> SetFont('Arial','',8);
    $pdf -> SetXY($_IN_X, $_IN_Y  );           
    $pdf -> MultiCell(126.5,4,"FIRMA Y SELLO AUTORIZADOS POR LA EMPRESA\n\n",1,'C');
    $pdf -> SetXY($_IN_X+126.5, $_IN_Y  );           
    $pdf -> MultiCell(126.5,4,"FIRMA Y No. CÉDULA CONDUCTOR\n\n",1,'C');
    
    if( sizeof( $mData['pue_contro'] ) > 15 )
	  {
      $pdf -> AddPage('L','Letter');
      //$pdf -> Image('plandeviaje_fondo.jpg',40,25,200);
      
      $_IN_X = 10;
      $_IN_Y = 5;
      $_ALTO = 5;
      $pdf -> SetFont('Arial','B',14);
      $pdf -> SetXY(100, $_IN_Y );
      $pdf->Cell(70,7,"PLAN DE RUTA",0,0,'C');
      $_IN_Y += 10;
      
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetXY($_IN_X, $_IN_Y );
      $pdf -> Cell(126.5,$_ALTO,"DESPACHO No. ".$mData['num_despac'],1,0,'C');
      $pdf -> SetXY($_IN_X+126.5, $_IN_Y );
      $pdf -> Cell(126.5 ,$_ALTO,"ANEXO No. 1",1,0,'C');
      
      $_IN_Y += $_ALTO;
      
      $pdf -> SetFont('Arial','',7);
      $pdf -> SetXY($_IN_X, $_IN_Y );
      $pdf -> Cell(126.5,$_ALTO,"Documento No: ".$mData['cod_manifi'],1,0,'L');
      $pdf -> SetXY($_IN_X+126.5, $_IN_Y );
      $pdf -> Cell(126.5 ,$_ALTO,"Placa: ".$mData['num_placax'],1,0,'L');
      
      $_IN_Y += $_ALTO+4;
      
      $_ANCHO = 50.6;
      $_ALTO = 26;
      $l=15;
      for( $i = 0, $k = 15; $i < 6; $i++ )
      {
        for( $j = 0; $j < 5; $j++ )
        {
          $_OBSERV = str_replace( "<small><br>", "\n", $mData['pue_contro'][$k++] );
          $_OBSERV = str_replace( "<br>", "\n", $_OBSERV );
          $_OBSERV = str_replace( "</small>", "", $_OBSERV );
          $pdf -> SetFont('Arial','',8);
          $pdf -> SetXY($_IN_X, $_IN_Y+1 );
          $pdf -> MultiCell( $_ANCHO, 3, $_OBSERV,0 ,'C' );
          $pdf -> SetXY($_IN_X, $_IN_Y );
          $pdf -> Cell($_ANCHO,$_ALTO,"",1,0,'C');
          $_IN_X += $_ANCHO;
        }
        $_IN_X = 10;
        $_IN_Y += $_ALTO;
      }
	  }
    
    $_PDF = 'PlanRuta_'.$mData['num_despac'].'.pdf';
    
    $pdf -> Close();
    $pdf -> Output( $_PDF,'D' );
    
    // header('Location: index.php');
    
  }
  
  function getDataDespac( $num_despac )
  {
    $mSelect = "SELECT a.cod_transp
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.num_despac = ".$num_despac." 
                   AND b.cod_tercer = a.cod_transp
                 GROUP BY 1";

    $transpor = new Consulta($mSelect, $this -> conexion);
    $transpor = $transpor -> ret_matriz();
    $mCodTransp = $transpor[0][0];
    
    $mSelect = "SELECT '', b.nom_tercer, b.cod_tercer,
                       b.num_verifi, b.dir_domici, b.num_telef1,
                       c.nom_ciudad, b.num_telef1, b.num_telef2
                  FROM ".BASE_DATOS.".tab_tercer_tercer b,
                       ".BASE_DATOS.".tab_genera_ciudad c
                 WHERE b.cod_tercer = '".$mCodTransp."' 
                   AND b.cod_ciudad = c.cod_ciudad";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $obsplan = $consulta -> ret_arreglo();

    $query = "SELECT a.cod_manifi, a.cod_ciuori, a.cod_ciudes,
                     DATE_FORMAT(b.fec_salipl,'%Y-%m-%d %H:%i'),
                     b.num_placax, h.nom_marcax, i.ano_modelo, j.nom_colorx,
                     k.nom_carroc, d.nom_tercer, b.cod_conduc, c.num_licenc,
                     e.nom_catlic, d.num_telmov, d.dir_ultfot, i.dir_fotfre,
                     IF(a.num_carava='0','Sin Caravana',a.num_carava),
                     DATE_FORMAT(b.fec_llegpl,'%Y-%m-%d %H:%i'),
                     a.obs_despac, m.nom_lineax, d.nom_apell1, d.nom_apell2, 
                     b.cod_agenci, b.nom_conduc, a.con_telmov, 
                     z.nom_tipdes
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_despac_vehige b,
                     ".BASE_DATOS.".tab_tercer_tercer d,
                     ".BASE_DATOS.".tab_genera_marcas h,
                     ".BASE_DATOS.".tab_vehicu_vehicu i,
                     ".BASE_DATOS.".tab_vehige_colore j,
                     ".BASE_DATOS.".tab_vehige_carroc k,
                     ".BASE_DATOS.".tab_vehige_lineas m,
                     ".BASE_DATOS.".tab_genera_tipdes z,
                     ".BASE_DATOS.".tab_tercer_conduc c 
           LEFT JOIN ".BASE_DATOS.".tab_genera_catlic e 
                  ON c.num_catlic = e.cod_catlic 
               WHERE b.cod_conduc = c.cod_tercer AND
                     a.num_despac = b.num_despac AND
                     a.cod_tipdes = z.cod_tipdes AND
                     d.cod_tercer = c.cod_tercer AND
                     b.num_placax = i.num_placax AND
                     h.cod_marcax = i.cod_marcax AND
                     i.cod_colorx = j.cod_colorx AND
                     i.cod_carroc = k.cod_carroc AND
                     i.cod_lineax = m.cod_lineax AND
                     i.cod_marcax = m.cod_marcax AND
                     a.num_despac = '".$num_despac."'";
    
    $consulta = new Consulta($query, $this -> conexion);
    $matriz = $consulta -> ret_matriz();
      

    $query = "SELECT a.num_trayle
                FROM ".BASE_DATOS.".tab_despac_vehige a
               WHERE a.num_despac = ".$num_despac."";

    $consec = new Consulta($query, $this -> conexion);
    $trayler = $consec -> ret_matriz();

    $query = "SELECT a.val_multpc,a.obs_planru
              FROM ".BASE_DATOS.".tab_config_parame a";

    $consec = new Consulta($query, $this -> conexion);
    $paramet = $consec -> ret_matriz();
    
    $ciudad_o = $this -> getCiudad( $matriz[0][1] );
    $ciudad_d = $this -> getCiudad( $matriz[0][2] );
    
    $mSelect = "SELECT rut_format, rut_anexox, obs_adicio, 
                         ind_pdfxxx, rut_pdfxxx, dir_logoxx, 
                         ind_telage, ind_segpue, lim_anexox,
                         cam_especi
                    FROM ".BASE_DATOS.".tab_config_planru 
                   WHERE cod_transp = '".$mCodTransp."'
                     AND ind_activo = '1'";
    $consult = new Consulta( $mSelect, $this -> conexion );
    $ind_forpro = $consult -> ret_matriz();
    $_FORMAT = $ind_forpro[0];
    
    
    
    if( $_FORMAT['ind_telage'] == '0' )
      $e4 = "Teléfonos: ".$obsplan[7]." / ".$obsplan[8];
    else
    {
      $query = "SELECT tel_agenci 
                  FROM ".BASE_DATOS.".tab_genera_agenci
                 WHERE cod_agenci = '".$matriz[0]['cod_agenci']."'";

      $consec = new Consulta($query, $this -> conexion);
      $telef = $consec -> ret_matriz();
      $telef = $telef[0][0];
      $e4 = "Teléfono: ".$telef;
    }
    
    // echo "<pre>";
    // print_r( $matriz[0] );
    // echo "</pre>";
    
    $_DATA = array();
    $_DATA['nom_transp'] = $obsplan[1];
    $_DATA['nit_transp'] = "NIT: ".$obsplan[2]."-".$obsplan[3];
    $_DATA['dir_transp'] = "Dirección: ".$obsplan[4]." / ".$obsplan[6];
    $_DATA['tel_transp'] = $e4;
    $_DATA['dir_logoxx'] = file_exists( "../../".NOM_URL_APLICA."/". $_FORMAT['dir_logoxx'] ) && $_FORMAT['dir_logoxx'] != '' ? NOM_URL_APLICA."/". $_FORMAT['dir_logoxx'] :  NOM_URL_APLICA."imagenes/logo.gif";
    $_DATA['img_conduc'] = file_exists( "../../".NOM_URL_APLICA."/". URL_CONDUC . $matriz[0][14] ) && $matriz[0][14] != '' ? NOM_URL_APLICA."/". URL_CONDUC . $matriz[0][14] :  DIR_APLICA_CENTRAL."/imagenes/conduc.jpg";
    $_DATA['img_vehicu'] = file_exists( "../../".NOM_URL_APLICA."/". URL_VEHICU . $matriz[0][15] ) && $matriz[0][15] != '' ? NOM_URL_APLICA."/". URL_VEHICU . $matriz[0][15] :  DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
    $_DATA['num_despac'] = $num_despac;
    $_DATA['cod_manifi'] = $matriz[0][0];
    $_DATA['nom_ciuori'] = $ciudad_o[0][1];
    $_DATA['nom_ciudes'] = $ciudad_d[0][1];
    $_DATA['fec_salpla'] = $matriz[0][3];
    $_DATA['num_placax'] = $matriz[0][4];
    $_DATA['nom_marcax'] = $matriz[0][5];
    $_DATA['num_modelo'] = $matriz[0][6];
    $_DATA['num_colorx'] = $matriz[0][7];
    $_DATA['cod_carroc'] = $matriz[0][8];
    $_DATA['nom_conduc'] = $matriz[0]['nom_conduc'] ? $matriz[0]['nom_conduc'] : $matriz[0][9]." ".$matriz[0][20]." ".$matriz[0][21];
    $_DATA['num_conduc'] = $matriz[0][10];
    $_DATA['lic_conduc'] = $matriz[0][11];
    $_DATA['cat_liccon'] = $matriz[0][12];
    $_DATA['con_telmov'] = $matriz[0]['con_telmov'] ? $matriz[0]['con_telmov'] : $matriz[0][13];
    $_DATA['obs_despac'] = $paramet[0][1] != '' ? $paramet[0][1]."\n". $matriz[0][18].$_FORMAT['obs_adicio'] : $matriz[0][18].$_FORMAT['obs_adicio'];
    $_DATA['nom_tipdes'] = $matriz[0]['nom_tipdes'];
    $_DATA['fec_llegpl'] = $matriz[0][17];
    $_DATA['val_multax'] = number_format($paramet[0][0]);
    $_DATA['num_remolq'] = $trayler[0][0];
    $_DATA['nom_lineax'] = $matriz[0][19];
    $_DATA['pue_contro'] = $_SESSION['mat'];

    return $_DATA;
  }
  
  function getCiudad( $ciudad )
  {
    $query = "SELECT a.cod_ciudad, 
                     CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad), 
                     UPPER( a.abr_ciudad )
                FROM " . BASE_DATOS . ".tab_genera_ciudad a,
                     " . BASE_DATOS . ".tab_genera_depart b,
                     " . BASE_DATOS . ".tab_genera_paises c
               WHERE a.cod_depart = b.cod_depart AND
                     a.cod_paisxx = b.cod_paisxx AND
                     b.cod_paisxx = c.cod_paisxx AND
                     a.cod_ciudad = '" . $ciudad . "'";
      
      $query .=" GROUP BY 1 ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion );
      $ciudades = $consulta -> ret_matriz();
      return $ciudades;
  }
}
$_AJAX = $_REQUEST;
$PDF = new PDFPlanRuta( $_AJAX );
?>
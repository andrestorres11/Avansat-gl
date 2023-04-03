<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

//
//

// setup the autoload function

require_once('../lib/FPDF/fpdf.php');

class PDF extends FPDF{
  
	var $widths;
	var $aligns;

	function SetWidths($w)
	{
//Set the array of column widths
		$this->widths=$w;
	}

  
	function SetAligns($a)
	{
//Set the array of column alignments
		$this->aligns=$a;
	}

	function Row($data)
	{
//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=4*$nb;
//Issue a page break first if needed
		$this->CheckPageBreak($h);
//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'J';
	//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
	//Draw the border
			$this->Rect($x,$y,$w,$h);
	//Print the text
			$this->MultiCell($w,4,$data[$i],0,$a);
	//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage("L","Legal");
	}

	function NbLines($w,$txt)
	{
//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}

  function Header()
  {
    $rut_general = URL_APLICA.'logos/';
    $nom_logo = 'LogoCLFaroOET.png';
    $this -> SetFont('Arial','B',9);
    $this -> Cell(50,30,$this->Image($rut_general.''.$nom_logo, $this->GetX()+5, $this->GetY()+2,40,0),1,0,'C');
    $this -> SetFontSize(13);
    $this -> Cell(90,30,'VerificaciÛn de Recursos',1,0,'C');
    $this -> SetFontSize(10);
    $_x = $this -> GetX();
    $this -> Cell(28,6,'CÛdigo',1,0,'C');
    $this -> Cell(28,6,'CLF-FR-60',1,1,'C');
    $this -> SetX($_x);
    $this -> Cell(28,6,'VersiÛn',1,0,'C');
    $this -> Cell(28,6,'2',1,1,'C');
    $this -> SetX($_x);
    $this -> Cell(28,6,'Fecha',1,0,'C');
    $this -> Cell(28,6,'04/10/2022',1,1,'C');
    $this -> SetX($_x);
    $this -> SetFillColor(255, 255, 0);
    $this -> Cell(56, 6, 'INFORMACI”N INTERNA', 'R,T,L', 1, 'C', true);
    $this -> SetX($_x);
    $this -> Cell(56, 6, 'P·gina '.$this ->PageNo().' de '."{nb}", 'R,L,B', 0, 'C', true);
    $this -> Ln(10);
  }
}


class PDFInformeEstudioSeguridad extends PDF
{
  var $conexion;
  

  function __construct( $_AJAX )
  {  
    $_AJAX['cod_estseg'] = $_SESSION['cod_estseg'];
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );
    include_once('../lib/general/constantes.inc');
    include_once('../../'.BASE_DATOS.'/constantes.inc');

    $this -> conexion = $AjaxConnection;
    
    $this -> pdfResultadosEstudio( $_AJAX );
  }
  
  

  function pdfResultadosEstudio(){
    $info = $this -> getDataEstudio($_REQUEST['cod_solici']);
    /* CREACION PDF */
    
    ob_clean();
    $_IN_X = 10;
    $_IN_Y = 5;
    $_ALTO = 5;
    
    $pdf = new PDF();
    $pdf -> AddPage('P','Letter');
    $pdf->AliasNbPages('{nb}');

    $pdf -> SetFont('Arial','B',9);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(98,6,utf8_decode('DATOS GENERALES DE LA SOLICITUD'),1,0,'C',1);
    $clx =  $pdf -> GetX();
    $pdf -> Cell(98,6,'DATOS DEL CONDUCTOR / VEHÕCULO',1,1,'C',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> Cell(40,6,utf8_decode('Orden de Servicio No.:'),1,0,'L');
    $pdf -> Cell(58,6,$_REQUEST['cod_solici'],1,0,'L');
    $pdf -> Cell(40,6,utf8_decode('Nombre y Apellido:'),1,0,'L');
    $pdf -> Cell(58,6,$info['nom_nomcon']." ".$info['nom_ap1con']." ".$info['nom_ap2con'],1,1,'L');

    $pdf -> Cell(40,6,utf8_decode('Fecha y Hora:'),1,0,'L');
    $pdf -> Cell(58,6,utf8_decode(date('Y-m-d H:i:s')),1,0,'L');
    $pdf -> Cell(40,6,utf8_decode('Tipo y No. de Documento:'),1,0,'L');
    $pdf -> Cell(58,6,utf8_decode($info['cod_tipcon']." ".$info['num_doccon']),1,1,'L');

    $pdf -> Cell(40,6,utf8_decode('Tomador:'),1,0,'L');
    $pdf -> Cell(58,6,utf8_decode(''),1,0,'L');
    $pdf -> Cell(40,6,utf8_decode('Celular'),1,0,'L');
    $pdf -> Cell(58,6,utf8_decode($info['num_mo1con']." ".$info['num_telcon']),1,1,'L');

    $pdf -> Cell(40,6,utf8_decode('Transportadora:'),1,0,'L');
    $pdf -> Cell(58,6,$info['nom_transp'],1,0,'L');
    $pdf -> Cell(40,6,utf8_decode('Placa'),1,0,'L');
    $pdf -> Cell(58,6,utf8_decode($info['num_placax']),1,1,'L');

    $pdf -> Cell(40,6,utf8_decode('Generador:'),1,0,'L');
    $pdf -> Cell(58,6,utf8_decode(''),1,0,'L');
    $pdf -> Cell(40,6,utf8_decode('Origen'),1,0,'L');
    $pdf -> Cell(58,6,$info['ciu_orides'],1,1,'L');
    $pdf -> SetX($clx);
    $pdf -> Cell(40,6,utf8_decode('Destino'),1,0,'L');
    $pdf -> Cell(58,6,$info['ciu_desdes'],1,1,'L');


    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,6,'RESULTADO DE CONSULTAS',1,1,'C',1);
    $pdf -> SetTextColor(0,0,0);

    $pdf -> Cell(148,6,'øEl conductor presenta comparendos? : '.$info['pre_comcon'],1,0,'L');
    $pdf -> Cell(16,6,'Valor:',1,0,'R');
    $pdf -> Cell(32,6,'$'.$info['val_comcon'],1,1,'L');

    $pdf -> Cell(148,6,'øEl conductor presenta resoluciones? : '.$info['pre_rescon'],1,0,'L');
    $pdf -> Cell(16,6,'Valor:',1,0,'R');
    $pdf -> Cell(32,6,'$'.$info['val_rescon'],1,1,'L');

    $pdf -> Cell(148,6,'øEl vehÌculo presenta comparendos? : '.$info['ind_preveh'],1,0,'L');
    $pdf -> Cell(16,6,'Valor:',1,0,'R');
    $pdf -> Cell(32,6,'$'.$info['val_comveh'],1,1,'L');

    $pdf -> Cell(148,6,'øEl vehÌculo presenta resoluciones? : '.$info['ind_preres'],1,0,'L');
    $clx =  $pdf -> GetX();
    $pdf -> Cell(16,6,'Valor:',1,0,'R');
    $pdf -> Cell(32,6,'$'.$info['val_resveh'],1,1,'L');

    $totalv = $info['val_comcon'] + $info['val_rescon'] + $info['val_comveh'] + $info['val_resveh'];

    $pdf -> SetX($clx);
    $pdf -> Cell(16,6,'Total:',1,0,'R');
    $pdf -> Cell(32,6,'$'.$totalv,1,1,'L');

    $pdf -> Ln(8);
    $pdf -> SetWidths(array(196));
    $pdf -> Row(array('OBSERVACI”N FINAL: '.$info['obs_estseg']),1,'J',0);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $resultado = $info['ind_estseg'] == 'A' ? 'RECOMENDADO' : 'NO RECOMENDADO';
    $pdf -> Cell(45,6,'RESULTADO DE ESTUDIO: ',1,0,'L',1);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> Cell(151,6, $resultado,1,1,'L');
    $pdf -> Ln(5);

    $pdf -> SetFont('Arial','',8);
    $txt="NOTA: PARA EL ESTUDIO PRELIMINAR ADJUNTAR LOS SIGUIENTES DOCUMENTOS: C√âDULAS DE CIUDADAN√çA - LICENCIA CONDUCCI√ìN - LICENCIA DE TR√ÅNSITO - FORMATO APERTURA HOJA DE VIDA. LOS ANTERIORES DOCUMENTOS DEBEN SER ESCANEADOS EN ALTA RESOLUCION Y A COLOR.";
    $pdf -> MultiCell(196,4,utf8_decode($txt) ,1, 'J',0);
    $txt="DECLARACI”N DE TRATAMIENTO DE DATOS: La presente polÌtica de privacidad y protecciÛn de datos se rige por la Ley 1581 de 2012 y el Decreto Reglamentario 1377 de 2013, que establecen los principios, obligaciones y procedimientos para la protecciÛn de los datos personales en Colombia. En cumplimiento de estas leyes y normas, informamos que la recolecciÛn, almacenamiento, uso, circulaciÛn y supresiÛn de datos personales en el presente estudio de seguridad de conductor y/o vehÌculo se realizar· con estricto respeto a los derechos y garantÌas constitucionales de los participantes, si desea m·s informaciÛn acerca del cumplimiento de la polÌtica de tratamiento de datos aquÌ relacionado por favor ingrese al siguiente link:\nhttps://www.grupooet.com/politicas_de_tratamiento_de_datos.php";
    $pdf -> MultiCell(196,4,$txt ,1, 'J',0);
    $pdf -> SetFont('Arial','',9);
    
    $rut_general = URL_APLICA.'files/adj_estseg/adjs/';
    
    if($info['cod_tipest']=='V'){
      $img_vehicu = $this->getImagenesEstSeg(1, $_REQUEST['cod_solici']);
      $img_poseed = $this->getImagenesEstSeg(3, $_REQUEST['cod_solici']);
      $img_propie = $this->getImagenesEstSeg(4, $_REQUEST['cod_solici']);
      $pdf -> AddPage('P','Letter');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS VEH√çCULO'),1,1,'C',1);

      for($i = 0; $i < count($img_vehicu); $i++){
        if($pdf->GetY()>=145){
          $pdf -> AddPage('P','Letter');
        }
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,$img_vehicu[$i]['nom_fordoc'],1,1,'C',1);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_vehicu[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
        $pdf -> SetWidths(array(196));
        $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_vehicu[$i]['obs_archiv'])));

      }

      if(count($img_poseed)>0){
        $pdf -> AddPage('P','Letter');
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS POSEEDOR'),1,1,'C',1);
        for($i = 0; $i < count($img_poseed); $i++){
          if($pdf->GetY()>=145){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_poseed[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_poseed[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_poseed[$i]['obs_archiv'])));

        }
      }

      if(count($img_propie)>0){
        $pdf -> AddPage('P','Letter');
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS PROPIETARIO'),1,1,'C',1);

        for($i = 0; $i < count($img_propie); $i++){
          if($pdf->GetY()>=145){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_propie[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_propie[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_propie[$i]['obs_archiv'])));

        }
      }

    }else if($info['cod_tipest']=='C'){
      $pdf -> AddPage('P','Letter');
      $img_conduc = $this->getImagenesEstSeg(2, $_REQUEST['cod_solici']);
      $pdf_conduc = $this->getPDFEstSeg(2, $_REQUEST['cod_solici']);

      if(count($img_conduc)>0){
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS CONDUCTOR'),1,1,'C',1);

        for($i = 0; $i < count($img_conduc); $i++){
          if($pdf->GetY()>=145){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_conduc[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_conduc[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_conduc[$i]['obs_archiv'])));

        }
      }

    }else{
      $img_vehicu = $this->getImagenesEstSeg(1, $_REQUEST['cod_solici']);
      $img_poseed = $this->getImagenesEstSeg(3, $_REQUEST['cod_solici']);
      $img_propie = $this->getImagenesEstSeg(4, $_REQUEST['cod_solici']);
      $pdf -> AddPage('P','Letter');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS VEH√çCULO'),1,1,'C',1);

      for($i = 0; $i < count($img_vehicu); $i++){
        if($pdf->GetY()>=145){
          $pdf -> AddPage('P','Letter');
        }
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,$img_vehicu[$i]['nom_fordoc'],1,1,'C',1);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_vehicu[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
        $pdf -> SetWidths(array(196));
        $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_vehicu[$i]['obs_archiv'])));

      }

      if(count($img_poseed)>0){
        $pdf -> AddPage('P','Letter');
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS POSEEDOR'),1,1,'C',1);
        for($i = 0; $i < count($img_poseed); $i++){
          if($pdf->GetY()>=145){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_poseed[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_poseed[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_poseed[$i]['obs_archiv'])));

        }
      }

      if(count($img_propie)>0){
        $pdf -> AddPage('P','Letter');
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS PROPIETARIO'),1,1,'C',1);

        for($i = 0; $i < count($img_propie); $i++){
          if($pdf->GetY()>=145){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_propie[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_propie[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_propie[$i]['obs_archiv'])));

        }
      }

      $pdf -> AddPage('P','Letter');
      $img_conduc = $this->getImagenesEstSeg(2, $_REQUEST['cod_solici']);
      $pdf_conduc = $this->getPDFEstSeg(2, $_REQUEST['cod_solici']);

      if(count($img_conduc)>0){
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS CONDUCTOR'),1,1,'C',1);

        for($i = 0; $i < count($img_conduc); $i++){
          if($pdf->GetY()>=145){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_conduc[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,84, $pdf->Image($rut_general.$img_conduc[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,80),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACI√ìN: '.$img_conduc[$i]['obs_archiv'])));

        }
      }


    }  
    $_PDF = 'Resultados_EstudioSeguridad_'.$info['cod_solici'].'.pdf';

   

    $pdf -> Close();
    $pdf -> Output( $_PDF,'D' );
  }

  function getImagenesEstSeg( $cod_person, $cod_solici ){
    $mSelect = "SELECT b.nom_archiv, b.nom_tipfil, a.nom_fordoc, b.obs_archiv
                FROM ".BASE_DATOS.".tab_estseg_fordoc a
          INNER JOIN ".BASE_DATOS.".tab_estseg_docume b ON a.cod_fordoc = b.cod_fordoc
                WHERE a.cod_tipper = '".$cod_person."' AND b.cod_solici = '".$cod_solici."'
                AND b.nom_tipfil IN ('png','jpg','jpeg');";
    $query = new Consulta($mSelect, $this -> conexion);
    $resultados = $query -> ret_matriz('a');
    return $resultados;
  }

  function getPDFEstSeg( $cod_person, $cod_solici ){
    $mSelect = "SELECT b.nom_archiv, b.nom_tipfil, a.nom_fordoc, b.obs_archiv
                FROM ".BASE_DATOS.".tab_estseg_fordoc a
          INNER JOIN ".BASE_DATOS.".tab_estseg_docume b ON a.cod_fordoc = b.cod_fordoc
                WHERE a.cod_tipper = '".$cod_person."' AND b.cod_solici = '".$cod_solici."'
                AND nom_tipfil IN ('pdf');";
    $query = new Consulta($mSelect, $this -> conexion);
    $resultados = $query -> ret_matriz('a');
    return $resultados;
  }

  function getImagenesVehicu( $cod_vehicu ){
    $mSelect = "SELECT a.fil_congps, a.obs_congps, a.fil_conrit,
                       a.obs_conrit, a.fil_runtxx, a.obs_runtxx,
                       a.fil_compar, a.obs_compar
                FROM ".BASE_DATOS.".tab_estudi_vehicu a
                WHERE a.cod_segveh = '".$cod_vehicu."';";
    $query = new Consulta($mSelect, $this -> conexion);
    $resultados = $query -> ret_matriz('a')[0];
    return $resultados;
  }
  
  function getDataEstudio( $cod_solici )
  {   
    $sql = "SELECT  a.cod_solici, a.cod_emptra, a.cor_solici,
                    a.tel_solici, a.cel_solici, a.cod_tipest,
                    a.obs_estseg, a.ind_estseg, a.cod_estcon, 
                    b.nom_tercer 'nom_transp', c.cod_tercer as 'num_doccon', c.cod_tipdoc as 'cod_tipcon',
                    c.ciu_expdoc as 'ciu_expcon', n.nom_ciudad as 'ciu_expcon', c.nom_apell1 as 'nom_ap1con',
                    c.nom_apell2 as 'nom_ap2con', c.nom_person as 'nom_nomcon', c.num_licenc as 'num_licenc',
                    c.cod_catlic as 'cod_catlic', c.fec_venlic as 'fec_venlic', c.nom_arlxxx as 'nom_arlxxx',
                    c.nom_epsxxx as 'nom_epsxxx', c.num_telmov as 'num_mo1con', c.num_telmo2,
                    c.num_telefo as 'num_telcon', c.cod_paisxx, c.cod_depart,
                    c.cod_ciudad as 'ciu_rescon', o.nom_ciudad as 'ciu_rescon', c.dir_domici as 'dir_domcon', c.dir_emailx as 'dir_emacon',
                    IF(c.ind_precom = 1, 'SI', 'NO') as 'pre_comcon',
                    c.val_compar as 'val_comcon',
                    IF(c.ind_preres = 1, 'SI', 'NO') as 'pre_rescon',
                    c.val_resolu as 'val_rescon', d.num_placax,
                    d.num_remolq, h.nom_config, g.nom_carroc, d.cod_marcax, d.cod_lineax,
                    d.ano_modelo, d.cod_colorx, i.nom_colorx, d.cod_carroc,
                    d.num_config, d.num_chasis, d.num_motorx,
                    d.num_soatxx, d.fec_vigsoa, d.num_lictra,
                    d.cod_opegps, d.usr_gpsxxx, d.clv_gpsxxx,
                    d.url_gpsxxx, d.idx_gpsxxx, d.obs_opegps,
                    d.fre_opegps, 
                    IF(d.ind_precom = 1, 'SI', 'NO') as 'ind_preveh',
                    d.val_compar as 'val_comveh',
                    IF(d.ind_preres = 1, 'SI', 'NO') as 'ind_preres',
                    d.val_resolu as 'val_resveh',
                    e.cod_tercer as 'num_docpos', e.cod_tipdoc as 'cod_tippos', e.ciu_expdoc as 'ciu_exppos',
                    e.nom_apell1 as 'nom_ap1pos', e.nom_apell2 as 'nom_ap2pos', e.nom_person as 'nom_nompos',
                    e.num_telmov as 'num_mo1pos', e.num_telmo2 as 'num_mo2pos', e.num_telefo as 'num_telpos',
                    e.cod_paisxx, e.cod_depart, e.cod_ciudad as 'ciu_respos', j.nom_ciudad as 'ciu_exppos',
                    k.nom_ciudad as 'ciu_respos',
                    e.dir_domici as 'dir_dompos', e.dir_emailx as 'dir_emapos',
                    f.cod_tercer as 'num_docpro', f.cod_tipdoc as 'cod_tippro', f.ciu_expdoc as 'ciu_exppro',
                    f.nom_apell1 as 'nom_ap1pro', f.nom_apell2 as 'nom_ap2pro', f.nom_person as 'nom_nompro',
                    f.num_telmov as 'num_mo1pro', f.num_telmo2 as 'num_mo2pro', f.num_telefo as 'num_telpro',
                    f.cod_paisxx, f.cod_depart, f.cod_ciudad as 'ciu_respro', l.nom_ciudad as 'ciu_exppro',
                    m.nom_ciudad as 'ciu_respos', f.dir_domici as 'dir_dompro', f.dir_emailx as 'dir_emapro',

                    q.nom_ciudad as 'ciu_orides', r.nom_ciudad as 'ciu_desdes'
                FROM ".BASE_DATOS.".tab_estseg_solici a
                INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
                ON a.cod_emptra = b.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer c
                ON a.cod_conduc = c.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu d
                ON a.cod_vehicu = d.num_placax
                LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer e
                ON d.cod_poseed = e.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer f
                ON d.cod_propie = f.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc g 
                ON d.cod_carroc = g.cod_carroc
                LEFT JOIN ".BASE_DATOS.".tab_vehige_config h ON
                  d.num_config = h.num_config
                LEFT JOIN ".BASE_DATOS.".tab_vehige_colore i ON
                  d.cod_colorx = i.cod_colorx
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad j ON
                  e.ciu_expdoc = j.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad k ON
                  e.cod_ciudad = k.cod_ciudad AND e.cod_depart = k.cod_depart AND e.cod_paisxx = k.cod_paisxx
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad l ON
                  f.ciu_expdoc = l.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad m ON
                  f.cod_ciudad = m.cod_ciudad AND f.cod_depart = m.cod_depart AND f.cod_paisxx = m.cod_paisxx
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad n ON
                  c.ciu_expdoc = n.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad o ON
                  c.cod_ciudad = o.cod_ciudad AND c.cod_depart = o.cod_depart AND c.cod_paisxx = o.cod_paisxx
                LEFT JOIN ".BASE_DATOS.".tab_estseg_despac p ON
                  a.cod_despac = p.cod_despac
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad q ON
                  p.cod_ciuori = q.cod_ciudad AND p.cod_paiori = q.cod_paisxx
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad r ON
                  p.cod_ciudes = r.cod_ciudad AND p.cod_paides = r.cod_paisxx
                WHERE a.cod_solici = '".$cod_solici."'; ";
    $query = new Consulta($sql, $this -> conexion);
    $resultados = $query -> ret_matriz('a')[0];
    return $resultados;
    
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

  function validaImagen($img_namexx){
    $rut_general_arch = URL_ARCHIV_STANDA.''.BASE_DATOS;
    $rut_general = URL_APLICA;
    if($img_namexx!=''){
      $ruta_prueba = $rut_general_arch.'/files/adj_estseg/adjs/'.$img_namexx;

      if (file_exists($ruta_prueba)) {
        $rut_imagen = $rut_general.'files/adj_estseg/adjs/'.$img_namexx;
      } else {
        $rut_imagen = $rut_general.'imagenes/noimg.png';
      }
    }else{
      $rut_imagen = $rut_general.'imagenes/noimg.png';
    }
    return $rut_imagen;
  }

}

$_AJAX = $_REQUEST;
$PDF = new PDFInformeEstudioSeguridad( $_AJAX );
?>
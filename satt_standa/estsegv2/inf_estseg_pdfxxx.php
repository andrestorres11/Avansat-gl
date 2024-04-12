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
    $nom_logo = 'LOGO_CEVA_LOGISTICS.png';
    $this -> SetFont('Arial','B',9);
    $this -> Cell(39,9,utf8_decode('Versión: 1.0'),1,0,'C');
    $this -> Cell(91,9,'Ceva Freight Management de Colombia S.A.S',1,0,'C');
    $this -> Cell(30,9,utf8_decode('Código documento'),1,0,'C');
    $this -> Cell(36,16,$this->Image($rut_general.''.$nom_logo, $this->GetX(), $this->GetY(),40,0),1,1,'C');
    $this -> SetY(19);
    $this -> Cell(39,7,utf8_decode('Fecha: 14/07/2021'),1,0,'C');
    $this -> Cell(91,7,'FORMATO APERTURA HOJA DE VIDA',1,0,'C');
    $this -> Cell(30,7,utf8_decode('FRM-TRA-03 V1'),1,0,'C');
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
    
    $pdf -> SetFont('Arial','B',7);
    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> Cell(39,6,utf8_decode('Motivo Apertura Hoja de Vida:'),1,0,'C',1);
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(52,6,utf8_decode('Fidelizar:'),1,0,'L');
    $pdf -> Cell(39,6,utf8_decode('Despacho:'),1,0,'L');
    $pdf -> Cell(30,6,utf8_decode('Referido por:'),1,0,'C');
    $pdf -> Cell(36,6,'',1,1,'C');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> Cell(196,6,utf8_decode('Si es para despacho diligenciar los siguientes campos:'),1,1,'C',1);

    $pdf -> Cell(39,5,utf8_decode('Cliente a cargar'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode(''),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('Ruta'),1,0,'L',1);
    $pdf -> Cell(66,5,'',1,1,'L');

    $pdf -> Cell(39,5,utf8_decode('Valor despacho'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode(''),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('Tipo de mercancia'),1,0,'L',1);
    $pdf -> Cell(66,5,'',1,1,'L');

    if($info['cod_tipest']=='V'){
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL VEHÓCULO'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(39,5,utf8_decode('Placa'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_placax']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Remolque'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['num_remolq']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Vehículo Tipo'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['nom_config']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Carrocería Tipo'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['nom_carroc']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Modelo (a&ntilde;o) repotenciado'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['ano_modelo']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Color'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['nom_colorx']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Usuario de GPS'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['usr_gpsxxx']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Contraseña de GPS'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['clv_gpsxxx']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Plataforma GPS (URL)'),1,0,'L',1);
      $pdf -> Cell(157,5,utf8_decode($info['nom_operad'].' ('.$info['url_operad'].')'),1,1,'L');
      
      if($info['obs_opegps'] == ''){
        $info['obs_opegps'] = '**SIN OBSERVACIÓN**';
      }

      $pdf -> setX(49);
      $Y = $pdf -> GetY();
      $pdf -> MultiCell(157,4,utf8_decode($info['obs_opegps']) ,1, 'J',0);
      $H = $pdf->GetY();
      $height= $H-$Y;
      $pdf->SetXY(10,$Y);
      $pdf->Cell(39,$height,utf8_decode('OBSERVACIÓN GPS'),1,0,'L',1); 
      $pdf -> setY($H);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL POSEEDOR TENEDOR'),1,1,'C',1);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
      $pdf -> Cell(52,5,$info['nom_ap1pos']." ".$info['nom_ap2pos'],1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
      $pdf -> Cell(66,5,$info['nom_nompos'],1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_docpos']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_exppos']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['dir_dompos']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respos']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_telpos']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respos']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL PROPIETARIO'),1,1,'C',1);
      $pdf -> SetFont('Arial','B',8);
      
      $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
      $pdf -> Cell(52,5,$info['nom_ap1pro']." ".$info['nom_ap2pro'],1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
      $pdf -> Cell(66,5,$info['nom_nompro'],1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_docpro']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_exppro']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['dir_dompro']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respos']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_telpro']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respro']),1,1,'L');
    }else if($info['cod_tipest']=='C'){
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL CONDUCTOR'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
      $pdf -> Cell(52,5,$info['nom_ap1con']." ".$info['nom_ap2con'],1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
      $pdf -> Cell(66,5,$info['nom_nomcon'],1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_doccon']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_expcon']),1,1,'L');

      $pdf -> SetFont('Arial','B',6);
      $pdf -> Cell(39,5,utf8_decode('LICENCIA DE CONDUCCIÓN No.'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_licenc']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('VENCE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['fec_venlic']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('ARL'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['nom_arlxxx']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('EPS'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['nom_epsxxx']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['dir_domcon']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_rescon']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('TELÓFONO DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_telcon']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_rescon']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('REFERENCIAS FAMILIARES DEL CONDUCTOR'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $mSelect = "SELECT b.nom_refere, b.nom_parent, b.dir_domici, b.num_telefo, b.obs_refere
                      FROM ".BASE_DATOS.".tab_estseg_relref a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON
                      a.cod_refere = b.cod_refere
              WHERE a.cod_estper = ".$info['num_doccon']." AND a.tip_refere = 'F'; ";
      $query = new Consulta($mSelect, $this -> conexion);
      $resultados = $query -> ret_matriz('a');

      foreach($resultados as $key => $registro){
        $pdf -> Ln(2);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> Cell(196,6,utf8_decode('REFERENCIAS #'.($key+1)),1,1,'C',1);

        $pdf -> Cell(39,5,utf8_decode('NOMBRE COMPLETO'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['nom_refere']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('PARENTESCO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['nom_parent']),1,1,'L');

        $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['dir_domici']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');

        $pdf -> MultiCell(196,5,utf8_decode('OBSERVACIÓN: '.$registro['obs_refere']),1, 'J',0);
      }

      $pdf -> Ln(2);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('REFERENCIAS PERSONALES DEL CONDUCTOR'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $mSelect = "SELECT b.nom_refere, b.nom_parent, b.dir_domici, b.num_telefo, b.obs_refere
                      FROM ".BASE_DATOS.".tab_estseg_relref a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON
                      a.cod_refere = b.cod_refere
              WHERE a.cod_estper = ".$info['num_doccon']." AND a.tip_refere = 'P'; ";
      $query = new Consulta($mSelect, $this -> conexion);
      $resultados = $query -> ret_matriz('a');

      foreach($resultados as $key => $registro){
        $pdf -> Ln(2);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> Cell(196,6,utf8_decode('REFERENCIAS #'.($key+1)),1,1,'C',1);

        $pdf -> Cell(39,5,utf8_decode('NOMBRE COMPLETO'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['nom_refere']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('PARENTESCO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['nom_parent']),1,1,'L');

        $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['dir_domici']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');

        $pdf -> MultiCell(196,5,utf8_decode('OBSERVACIÓN: '.$registro['obs_refere']),1, 'J',0);
      }

      $pdf -> Ln(2);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('REFERENCIAS LABORALES DEL CONDUCTOR'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $mSelect = "SELECT b.nom_transp, b.num_telefo, b.inf_sumini, b.num_viajes
                      FROM ".BASE_DATOS.".tab_estseg_relref a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_reflab b ON
                      a.cod_refere = b.cod_refere
              WHERE a.cod_estper = ".$info['num_doccon']." AND a.tip_refere = 'L'; ";
      $query = new Consulta($mSelect, $this -> conexion);
      $resultados = $query -> ret_matriz('a');

      foreach($resultados as $key=>$registro){
        $pdf -> Cell(39,5,utf8_decode(($key+1).'. TRANSPORTADORA'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['nom_transp']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');

        $pdf -> Cell(39,5,utf8_decode('INFORMACIÓN DADA POR'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['inf_sumini']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('No. DE VIAJES'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_viajes']),1,1,'L');
      }
    }else if($info['cod_tipest']=='CV'){
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL VEHÍCULO'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(39,5,utf8_decode('Placa'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_placax']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Remolque'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['num_remolq']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Vehículo Tipo'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['nom_config']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Carrocería Tipo'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['nom_carroc']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Modelo (año) repotenciado'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['ano_modelo']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Color'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['nom_colorx']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Usuario de GPS'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['usr_gpsxxx']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('Contraseña de GPS'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['clv_gpsxxx']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('Plataforma GPS (URL)'),1,0,'L',1);
      $pdf -> Cell(157,5,utf8_decode($info['nom_operad'].' ('.$info['url_operad'].')'),1,1,'L');
      
      if($info['obs_opegps'] == ''){
        $info['obs_opegps'] = '**SIN OBSERVACIÓN**';
      }

      $pdf -> setX(49);
      $Y = $pdf -> GetY();
      $pdf -> MultiCell(157,4,utf8_decode($info['obs_opegps']) ,1, 'J',0);
      $H = $pdf->GetY();
      $height= $H-$Y;
      $pdf->SetXY(10,$Y);
      $pdf->Cell(39,$height,utf8_decode('OBSERVACION GPS'),1,0,'L',1); 
      $pdf -> setY($H);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL POSEEDOR TENEDOR'),1,1,'C',1);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
      $pdf -> Cell(52,5,$info['nom_ap1pos']." ".$info['nom_ap2pos'],1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
      $pdf -> Cell(66,5,$info['nom_nompos'],1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_docpos']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_exppos']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['dir_dompos']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respos']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_telpos']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respos']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL PROPIETARIO'),1,1,'C',1);
      $pdf -> SetFont('Arial','B',8);
      
      $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
      $pdf -> Cell(52,5,$info['nom_ap1pro']." ".$info['nom_ap2pro'],1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
      $pdf -> Cell(66,5,$info['nom_nompro'],1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_docpro']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_exppro']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['dir_dompro']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respos']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_telpro']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_respro']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL CONDUCTOR'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
      $pdf -> Cell(52,5,$info['nom_ap1con']." ".$info['nom_ap2con'],1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
      $pdf -> Cell(66,5,$info['nom_nomcon'],1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_doccon']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_expcon']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('LICENCIA DE CONDUCCIÓN No.'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_licenc']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('VENCE'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['fec_venlic']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('ARL'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['nom_arlxxx']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('EPS'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['nom_epsxxx']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['dir_domcon']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_rescon']),1,1,'L');

      $pdf -> SetFont('Arial','B',7);
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO DE RESIDENCIA'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($info['num_telcon']),1,0,'L');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(39,5,utf8_decode('CIUDAD'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($info['ciu_rescon']),1,1,'L');

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('REFERENCIAS FAMILIARES DEL CONDUCTOR'),1,1,'C',1);

      
      $mSelect = "SELECT b.nom_refere, b.nom_parent, b.dir_domici, b.num_telefo, b.obs_refere
                      FROM ".BASE_DATOS.".tab_estseg_relref a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON
                      a.cod_refere = b.cod_refere
              WHERE a.cod_estper = ".$info['num_doccon']." AND a.tip_refere = 'F'; ";
      $query = new Consulta($mSelect, $this -> conexion);
      $resultados = $query -> ret_matriz('a');

      foreach($resultados as $key => $registro){
      
        $pdf -> Ln(2);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,6,utf8_decode('REFERENCIAS #'.($key+1)),1,1,'C',1);

        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);

        $pdf -> Cell(39,5,utf8_decode('NOMBRE COMPLETO'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['nom_refere']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('PARENTESCO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['nom_parent']),1,1,'L');

        $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['dir_domici']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');

        $pdf -> MultiCell(196,5,utf8_decode('OBSERVACIÓN: '.$registro['obs_refere']),1, 'J',0);
      }

      $pdf -> Ln(2);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('REFERENCIAS PERSONALES DEL CONDUCTOR'),1,1,'C',1);

    
      $mSelect = "SELECT b.nom_refere, b.nom_parent, b.dir_domici, b.num_telefo, b.obs_refere
                      FROM ".BASE_DATOS.".tab_estseg_relref a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON
                      a.cod_refere = b.cod_refere
              WHERE a.cod_estper = ".$info['num_doccon']." AND a.tip_refere = 'P'; ";
      $query = new Consulta($mSelect, $this -> conexion);
      $resultados = $query -> ret_matriz('a');

      foreach($resultados as $key => $registro){
        $pdf -> Ln(2);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,6,utf8_decode('REFERENCIAS #'.($key+1)),1,1,'C',1);

        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);

        $pdf -> Cell(39,5,utf8_decode('NOMBRE COMPLETO'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['nom_refere']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('PARENTESCO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['nom_parent']),1,1,'L');

        $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['dir_domici']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');

        $pdf -> MultiCell(196,5,utf8_decode('OBSERVACIÓN: '.$registro['obs_refere']),1, 'J',0);
      }

      $pdf -> Ln(2);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,6,utf8_decode('REFERENCIAS LABORALES DEL CONDUCTOR'),1,1,'C',1);

      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $mSelect = "SELECT b.nom_transp, b.num_telefo, b.inf_sumini, b.num_viajes
                      FROM ".BASE_DATOS.".tab_estseg_relref a
                  INNER JOIN ".BASE_DATOS.".tab_estseg_reflab b ON
                      a.cod_refere = b.cod_refere
              WHERE a.cod_estper = ".$info['num_doccon']." AND a.tip_refere = 'L'; ";
      $query = new Consulta($mSelect, $this -> conexion);
      $resultados = $query -> ret_matriz('a');

      foreach($resultados as $key=>$registro){
        $pdf -> Cell(39,5,utf8_decode(($key+1).'. TRANSPORTADORA'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['nom_transp']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');

        $pdf -> Cell(39,5,utf8_decode('INFORMACIÓN DADA POR'),1,0,'L',1);
        $pdf -> Cell(52,5,utf8_decode($registro['inf_sumini']),1,0,'L');
        $pdf -> Cell(39,5,utf8_decode('No. DE VIAJES'),1,0,'L',1);
        $pdf -> Cell(66,5,utf8_decode($registro['num_viajes']),1,1,'L');
      }

    }

    $pdf -> Ln(2);
    $pdf -> SetWidths(array(196));
    $pdf -> Row(array(utf8_decode('OBSERVACIÓN FINAL: '.$info['obs_estseg'])),1,'J',0);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $resultado = $info['ind_estseg'] == 'A' ? 'RECOMENDADO' : 'NO RECOMENDADO';
    $pdf -> Cell(45,6,'RESULTADO DE ESTUDIO: ',1,0,'L',1);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> Cell(151,6, $resultado,1,1,'L');
    $pdf -> Ln(1);

    $pdf -> SetFont('Arial','',8);
    $txt="NOTA: PARA EL ESTUDIO PRELIMINAR ADJUNTAR LOS SIGUIENTES DOCUMENTOS: CÉDULAS DE CIUDADANÍA - LICENCIA CONDUCCIÓN - LICENCIA DE TRÁNSITO - FORMATO APERTURA HOJA DE VIDA. LOS ANTERIORES DOCUMENTOS DEBEN SER ESCANEADOS EN ALTA RESOLUCION Y A COLOR.";
    $pdf -> MultiCell(196,4,utf8_decode($txt) ,1, 'J',0);
    $txt="DECLARACIÓN DE TRATAMIENTO DE DATOS: De acuerdo con la ley 1581 de 2012, el Decreto reglamentario 1377 de 2015 y las demás normas que lo modifiquen o adicionen, autorizo para que se le dé tratamiento respectivo a mis datos personales y demás información solicitada, en el proceso de registro de asociado de negocio, operación o cualquier información adicional a la que se pueda llegar a tener acceso como consecuencia de la relación comercial. Confirmo y acepto por medio de este documento que he leído y comprendido la polática para el manejo de datos personales de la organización.";
    $pdf -> MultiCell(196,4,utf8_decode($txt) ,1, 'J',0);
    $pdf -> SetFont('Arial','',9);
    $txt="AUTORIZACION: Yo, ............................................................................, mayor de edad, identificado con la cédula de ciudadanía número ................................, de ......................................, en calidad de propietario del vehículo de placas XXX-000, AUTORIZO al conductor ya identificado,, para que CEVA FREIGH MANANGMENT SAS, entregue o consigne las sumas correspondientes a saldos y/o pagos de los servicios por concepto de prestación de servicios de transporte terrestre por carretera a esta sociedad.
    Autoriza,                                                            Acepta,";
    $pdf -> MultiCell(196,5,utf8_decode($txt) ,1, 'J',0);


    
    
    $rut_general = URL_APLICA.'files/adj_estseg/adjs/';
    
    if($info['cod_tipest']=='V'){
      $img_vehicu = $this->getImagenesEstSeg(1, $_REQUEST['cod_solici']);
      $img_poseed = $this->getImagenesEstSeg(3, $_REQUEST['cod_solici']);
      $img_propie = $this->getImagenesEstSeg(4, $_REQUEST['cod_solici']);
      $pdf -> AddPage('P','Letter');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS VEHÍCULO'),1,1,'C',1);

      for($i = 0; $i < count($img_vehicu); $i++){
        if($pdf->GetY()>=155){
          $pdf -> AddPage('P','Letter');
        }
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,$img_vehicu[$i]['nom_fordoc'],1,1,'C',1);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> Cell(196,100, $pdf->Image($rut_general.$img_vehicu[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,96),1,1,'C');
        $pdf -> SetWidths(array(196));
        $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_vehicu[$i]['obs_archiv'])));

      }

      /*
      for($i = 0; $i < count($img_vehicu); $i+=2){
        if(array_key_exists($i, $img_vehicu) AND array_key_exists($i+1, $img_vehicu)){
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_vehicu[$i]['nom_fordoc'],1,0,'C',1);
          $pdf -> Cell(98,5,$img_vehicu[$i+1]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_vehicu[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_vehicu[$i+1]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_vehicu[$i]['obs_archiv']),utf8_decode('OBSERVACI?N: '.$img_vehicu[$i+1]['obs_simitx'])));
        }else{
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_vehicu[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_vehicu[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_vehicu[$i]['obs_archiv'])));
        }
      }*/

      $pdf -> AddPage('P','Letter');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS POSEEDOR'),1,1,'C',1);

      for($i = 0; $i < count($img_poseed); $i++){
        if($pdf->GetY()>=155){
          $pdf -> AddPage('P','Letter');
        }
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,$img_poseed[$i]['nom_fordoc'],1,1,'C',1);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> Cell(196,100, $pdf->Image($rut_general.$img_poseed[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,96),1,1,'C');
        $pdf -> SetWidths(array(196));
        $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_poseed[$i]['obs_archiv'])));

      }

      /*
      for($i = 0; $i < count($img_poseed); $i+=2){
        if(array_key_exists($i, $img_poseed) AND array_key_exists($i+1, $img_poseed)){
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_poseed[$i]['nom_fordoc'],1,0,'C',1);
          $pdf -> Cell(98,5,$img_poseed[$i+1]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_poseed[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_poseed[$i+1]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_poseed[$i]['obs_archiv']),utf8_decode('OBSERVACI?N: '.$img_poseed[$i+1]['obs_simitx'])));
        }else{
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_poseed[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_poseed[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_poseed[$i]['obs_archiv'])));
        }
      }
      */

      if($info['num_docpos']!=$info['num_docpro']){
        $pdf -> AddPage('P','Letter');
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,utf8_decode('ADJUNTOS PROPIETARIO'),1,1,'C',1);

        for($i = 0; $i < count($img_propie); $i++){
          if($pdf->GetY()>=155){
            $pdf -> AddPage('P','Letter');
          }
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(196,5,$img_propie[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(196,100, $pdf->Image($rut_general.$img_propie[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,96),1,1,'C');
          $pdf -> SetWidths(array(196));
          $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_propie[$i]['obs_archiv'])));

        }
      }

      
      /* for($i = 0; $i < count($img_propie); $i+=2){
        if(array_key_exists($i, $img_propie) AND array_key_exists($i+1, $img_propie)){
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_propie[$i]['nom_fordoc'],1,0,'C',1);
          $pdf -> Cell(98,5,$img_propie[$i+1]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_propie[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_propie[$i+1]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_propie[$i]['obs_archiv']),utf8_decode('OBSERVACI?N: '.$img_propie[$i+1]['obs_simitx'])));
        }else{
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_propie[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_propie[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_propie[$i]['obs_archiv'])));
        }
      } */
    }else if($info['cod_tipest']=='C'){
      $pdf -> AddPage('P','Letter');
      $img_conduc = $this->getImagenesEstSeg(2, $_REQUEST['cod_solici']);
      $pdf_conduc = $this->getPDFEstSeg(2, $_REQUEST['cod_solici']);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS CONDUCTOR'),1,1,'C',1);

      for($i = 0; $i < count($img_conduc); $i++){
        if($pdf->GetY()>=155){
          $pdf -> AddPage('P','Letter');
        }
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,$img_conduc[$i]['nom_fordoc'],1,1,'C',1);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> Cell(196,100, $pdf->Image($rut_general.$img_conduc[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,96),1,1,'C');
        $pdf -> SetWidths(array(196));
        $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_conduc[$i]['obs_archiv'])));

      }

      /* for($i = 0; $i < count($img_conduc); $i+=2){
        if(array_key_exists($i, $img_conduc) AND array_key_exists($i+1, $img_conduc)){
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_conduc[$i]['nom_fordoc'],1,0,'C',1);
          $pdf -> Cell(98,5,$img_conduc[$i+1]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_conduc[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_conduc[$i+1]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_conduc[$i]['obs_archiv']),utf8_decode('OBSERVACI?N: '.$img_conduc[$i+1]['obs_archiv'])));
        }else{
          $pdf -> SetFont('Arial','B',8);
          $pdf -> SetFillColor(1, 11, 64);
          $pdf -> SetTextColor(255,255,255);
          $pdf -> Cell(98,5,$img_conduc[$i]['nom_fordoc'],1,1,'C',1);
          $pdf -> SetFillColor(180, 181, 179);
          $pdf -> SetTextColor(0,0,0);
          $pdf -> SetFont('Arial','B',8);
          $pdf -> Cell(98,50, $pdf->Image($rut_general.$img_conduc[$i]['nom_archiv'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
          $pdf -> SetWidths(array(98,98));
          $pdf -> Row(array(utf8_decode('OBSERVACI?N: '.$img_conduc[$i]['obs_archiv'])));
        }
      } */
    } else if($info['cod_tipest']=='CV'){
      

      
      $img_vehicu = $this->getImagenesEstSeg(1, $_REQUEST['cod_solici']);
      $img_poseed = $this->getImagenesEstSeg(3, $_REQUEST['cod_solici']);
      $img_propie = $this->getImagenesEstSeg(4, $_REQUEST['cod_solici']);
      $pdf -> AddPage('P','Letter');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS VEHÍCULO'),1,1,'C',1);

      for($i = 0; $i < count($img_vehicu); $i++){
        if($pdf->GetY()>=155){
          $pdf -> AddPage('P','Letter');
        }
        $pdf -> SetFont('Arial','B',8);
        $pdf -> SetFillColor(1, 11, 64);
        $pdf -> SetTextColor(255,255,255);
        $pdf -> Cell(196,5,$img_vehicu[$i]['nom_fordoc'],1,1,'C',1);
        $pdf -> SetFillColor(180, 181, 179);
        $pdf -> SetTextColor(0,0,0);
        $pdf -> SetFont('Arial','B',8);
        $pdf -> Cell(196,100, $pdf->Image($rut_general.$img_vehicu[$i]['nom_archiv'], $pdf->GetX()+2, $pdf->GetY()+2,192,96),1,1,'C');
        $pdf -> SetWidths(array(196));
        $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_vehicu[$i]['obs_archiv'])));

      }

      
      $pdf -> AddPage('P','Letter');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS POSEEDOR'),1,1,'C',1);

      foreach($img_poseed as $imagencita){
        if ($pdf->GetY() >= 155) {
          $pdf->AddPage('P', 'Letter');
        }
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(1, 11, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(196, 5, $imagencita['nom_fordoc'], 1, 1, 'C', 1);
        
        // Save current position
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        
        $pdf->SetFillColor(180, 181, 179);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
        
        
        // Output the image and get its height
        $imagePath = $rut_general . $imagencita['nom_archiv'];
        $imageHeight = 96; // Adjust this value as needed
        
        $pdf->Cell(196, $imageHeight + 4, '', 1, 1, 'C'); // Empty cell as placeholder for the image
        $pdf->Image($imagencita['nom_rutfil'], $x + 2, $y + 2, 192, $imageHeight);

        $pdf->SetWidths(array(196));
        $pdf->Row(array(utf8_decode('OBSERVACIÓN: ' . $imagencita['obs_archiv'])));
      }

      
      if ($info['num_docpos'] != $info['num_docpro']) {
        $pdf->AddPage('P', 'Letter');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(1, 11, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(196, 5, utf8_decode('ADJUNTOS PROPIETARIO'), 1, 1, 'C', 1);
    
        for ($i = 0; $i < count($img_propie); $i++) {
            if ($pdf->GetY() >= 155) {
                $pdf->AddPage('P', 'Letter');
            }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(1, 11, 64);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(196, 5, $img_propie[$i]['nom_fordoc'], 1, 1, 'C', 1);
            $pdf->SetFillColor(180, 181, 179);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 8);
    
            // Save current position
            $x = $pdf->GetX();
            $y = $pdf->GetY();
    
            // Output the image and get its height
            $imagePath = $rut_general . $img_propie[$i]['nom_archiv'];
            $imageHeight = 96; // Adjust this value as needed
    
            // Empty cell as a placeholder for the image
            $pdf->Cell(196, $imageHeight + 4, '', 1, 1, 'C');
    
            // Place the image inside the cell at the specified position
            $pdf->Image($imagePath, $x + 2, $y + 2, 192, $imageHeight);
    
            $pdf->SetWidths(array(196));
            $pdf->Row(array(utf8_decode('OBSERVACIÓN: ' . $img_propie[$i]['obs_archiv'])));
        }
      }
      
      
      $pdf -> AddPage('P','Letter');
      $img_conduc = $this->getImagenesEstSeg(2, $_REQUEST['cod_solici']);
      $pdf_conduc = $this->getPDFEstSeg(2, $_REQUEST['cod_solici']);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('ADJUNTOS CONDUCTOR'),1,1,'C',1);

      $texto = '';

      for ($i = 0; $i < count($img_conduc); $i++) {

        if ($pdf->GetY() >= 155) {
            $pdf->AddPage('P', 'Letter');
        }
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(1, 11, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(196, 5, utf8_decode($img_conduc[$i]['nom_fordoc']), 1, 1, 'C', 1);
        $pdf->SetFillColor(180, 181, 179);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
    
        // Save current position
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Obtener la ruta de la imagen
        $imagePath = $rut_general . $img_conduc[$i]['nom_archiv'];

        //$imagePath = realpath($imagePath);
        $imageHeight = 96; // Adjust this value as needed
    
        // Empty cell as a placeholder for the image
        $pdf->Cell(196, $imageHeight + 4, '', 1, 1, 'C');
        
        $pdf->Image(URL_ARCHIV."files/adj_estseg/adjs/".$img_conduc[$i]['nom_archiv'], $x + 2, $y + 2, 192, $imageHeight);
        
        $pdf->SetWidths(array(196));
        $pdf->Row(array(utf8_decode('OBSERVACIÓN: ' . $img_conduc[$i]['obs_archiv'])));

      }

    } 

    $_PDF = 'Resultados_EstudioSeguridad_'.$info['cod_solici'].'.pdf';

   

    $pdf -> Close();
    $pdf -> Output( $_PDF,'D' );
  }

  function getImagenesEstSeg( $cod_person, $cod_solici ){
    $mSelect = "SELECT b.nom_archiv, b.nom_tipfil, b.nom_rutfil, a.nom_fordoc, b.obs_archiv
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
                    a.obs_estseg, a.ind_estseg,
                    b.nom_tercer, c.cod_tercer as 'num_doccon', c.cod_tipdoc as 'cod_tipcon',
                    c.ciu_expdoc as 'ciu_expcon', n.nom_ciudad as 'ciu_expcon', c.nom_apell1 as 'nom_ap1con',
                    c.nom_apell2 as 'nom_ap2con', c.nom_person as 'nom_nomcon', c.num_licenc as 'num_licenc',
                    c.cod_catlic as 'cod_catlic', c.fec_venlic as 'fec_venlic', c.nom_arlxxx as 'nom_arlxxx',
                    c.nom_epsxxx as 'nom_epsxxx', c.num_telmov as 'num_mo1con', c.num_telmo2,
                    c.num_telefo as 'num_telcon', c.cod_paisxx, c.cod_depart,
                    c.cod_ciudad as 'ciu_rescon', o.nom_ciudad as 'ciu_rescon', c.dir_domici as 'dir_domcon', c.dir_emailx as 'dir_emacon',
                    c.ind_precom as 'pre_comcon', c.val_compar as 'val_comcon', c.ind_preres as 'pre_rescon',
                    c.val_resolu as 'val_rescon', d.num_placax,
                    d.num_remolq, h.nom_config, g.nom_carroc, d.cod_marcax, d.cod_lineax,
                    d.ano_modelo, d.cod_colorx, i.nom_colorx, d.cod_carroc,
                    d.num_config, d.num_chasis, d.num_motorx,
                    d.num_soatxx, d.fec_vigsoa, d.num_lictra,
                    p.nom_operad, p.url_operad, d.cod_opegps,
                    d.usr_gpsxxx, d.clv_gpsxxx, d.url_gpsxxx, 
                    d.idx_gpsxxx, d.obs_opegps,
                    d.fre_opegps, d.ind_precom as 'ind_preveh', d.val_compar as 'val_comveh',
                    d.ind_preres as 'ind_preveh', d.val_resolu as 'val_resveh',
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
                    m.nom_ciudad as 'ciu_respos', f.dir_domici as 'dir_dompro', f.dir_emailx as 'dir_emapro'
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
                LEFT JOIN ".BD_STANDA.".tab_genera_opegps p ON p.cod_operad = d.cod_opegps
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
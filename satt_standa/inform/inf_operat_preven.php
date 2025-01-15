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
    $this -> Cell(90,30,'Solicitud de Operativo Preventivo',1,0,'C');
    $this -> SetFontSize(10);
    $_x = $this -> GetX();
    $this -> Cell(28,6,'Código',1,0,'C');
    $this -> Cell(28,6,'',1,1,'C');
    $this -> SetX($_x);
    $this -> Cell(28,6,'Versión',1,0,'C');
    $this -> Cell(28,6,'',1,1,'C');
    $this -> SetX($_x);
    $this -> Cell(28,6,'Fecha',1,0,'C');
    $this -> Cell(28,6,'',1,1,'C');
    $this -> SetX($_x);
    $this -> SetFillColor(255, 255, 0);
    $this -> Cell(56, 6, 'INFORMACIÓN INTERNA', 'R,T,L', 1, 'C', true);
    $this -> SetX($_x);
    $this -> Cell(56, 6, 'Página '.$this ->PageNo().' de '."{nb}", 'R,L,B', 0, 'C', true);
    $this -> Ln(10);
  }
}


class PDFInformeOperativoPreventivo extends PDF
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
    
    $this -> pdfResultadosEstudio();
  }
  
  

  function pdfResultadosEstudio(){
    $info = $this -> getDataInforme($_REQUEST['cod_consec']);
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
    $pdf -> Cell(196,6,'Información Básica de la empresa',1,1,'C',1);

    $pdf -> SetFont('Arial','',9);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Empresa que reporta el caso:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_empres'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Persona que impulsa el caso:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_person'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Número de contacto:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['num_contac'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Generador de la carga:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['gen_cargax'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Mercancía Transportada:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(148,6,$info['mer_transp'],1,1,'L',1);


    $pdf -> SetFont('Arial','B',9);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,6,'Información del Conductor',1,1,'C',1);

    $pdf -> SetFont('Arial','',9);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Nombre del Conductor:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$this->eliminarRepetidos($info['nom_conduc']),1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Apellidos:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_apell1'].' '.$info['nom_apell2'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Número de cédula conductor:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['cod_tercer'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'N° Celular Conductor:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['num_telmov'],1,1,'L',1);

    $pdf -> SetFont('Arial','B',9);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,6,'Información del Vehículo',1,1,'C',1);

    $pdf -> SetFont('Arial','',9);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Placa:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['num_placax'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Configuración:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['num_config'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Carrocería:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_carroc'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Marca:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_marcax'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Color:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_colorx'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Placa del remolque:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['num_trayle'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Operador GPS:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['nom_operad'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Usuario:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['gps_usuari'],1,1,'L',1);

    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Contraseña:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['gps_paswor'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,'',1,1,'L',1);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(1, 11, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(196, 6, 'Información de última ubicación del vehículo', 1, 1, 'C', true);

    // Variables de contenido
    $ultimo_reporte = $info['ult_report'];
    $cell_width_label = 48; // Ancho de la celda de "Último reporte:"
    $cell_width_content = 148; // Ancho de la celda del contenido
    $line_height = 7; // Altura de las líneas

    // Calcular la altura dinámica de la celda del contenido (MultiCell)
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(215, 215, 215);

    $lines = $pdf->GetStringWidth($ultimo_reporte) / $cell_width_content;
    $cell_height = ceil($lines) * $line_height;

    // Establecer la posición inicial
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Dibujar celda de "Último reporte"
    $pdf->Cell($cell_width_label, $cell_height, 'Último reporte:', 1, 0, 'R', true);

    // Dibujar celda del contenido de "Último reporte" con MultiCell
    $pdf->SetXY($x + $cell_width_label, $y); // Ajustar la posición para la celda del contenido
    $pdf -> SetFillColor(255, 255, 255);
    $pdf->MultiCell($cell_width_content, $line_height, $ultimo_reporte, 1, 'L', true);

    // Volver a la posición inicial para futuras celdas
    $pdf->SetXY($x, $y + $cell_height);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Latitud:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['val_latitu'],1,0,'L',1);
    $pdf -> SetFillColor(215, 215, 215);
    $pdf -> Cell(48,6,'Longitud:',1,0,'R',1);
    $pdf -> SetFillColor(255, 255, 255);
    $pdf -> Cell(50,6,$info['val_longit'],1,1,'L',1);

    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(215, 215, 215);

    $lines = $pdf->GetStringWidth($ultimo_reporte) / $cell_width_content;
    $cell_height = ceil($lines) * $line_height;

    // Establecer la posición inicial
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Dibujar celda de "Último reporte"
    $pdf->Cell($cell_width_label, $cell_height, 'Descripción del caso:', 1, 0, 'R', true);

    // Dibujar celda del contenido de "Último reporte" con MultiCell
    $pdf->SetXY($x + $cell_width_label, $y); // Ajustar la posición para la celda del contenido
    $pdf -> SetFillColor(255, 255, 255);
    $pdf->MultiCell($cell_width_content, $line_height, $info['des_casoxx'], 1, 'L', true);

    
    $_PDF = 'InformeOperativoPreventivo_'.$_REQUEST['cod_consec'].'.pdf';

   

    $pdf -> Close();
    $pdf -> Output( $_PDF,'I' );
  }

  
  
  function getDataInforme( $cod_consec )
  {   
    $sql = "SELECT  a.nom_person, a.num_contac, a.gen_cargax,
                    a.mer_transp, a.ult_report, a.des_casoxx,
                    a.val_latitu, a.val_longit, d.nom_tercer as 'nom_conduc',
                    d.nom_apell1, d.nom_apell2, d.cod_tercer,
                    d.num_telmov, e.num_placax, f.nom_carroc,
                    g.nom_marcax, h.nom_colorx, c.num_trayle,
                    i.nom_operad, b.gps_usuari, b.gps_paswor,
                    j.nom_tercer as 'nom_empres', e.num_config
                FROM ".BASE_DATOS.".tab_noveda_opprev a 
                INNER JOIN ".BASE_DATOS.".tab_despac_despac b ON a.num_despac = b.num_despac
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige c ON a.num_despac = c.num_despac
                INNER JOIN ".BASE_DATOS.".tab_tercer_tercer d ON c.cod_conduc = d.cod_tercer
                INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu e ON c.num_placax = e.num_placax
                INNER JOIN ".BASE_DATOS.".tab_vehige_carroc f ON e.cod_carroc = f.cod_carroc
                INNER JOIN ".BASE_DATOS.".tab_genera_marcas g ON e.cod_marcax = g.cod_marcax
                INNER JOIN ".BASE_DATOS.".tab_vehige_colore h ON e.cod_colorx = h.cod_colorx
                INNER JOIN satt_standa.tab_genera_opegps i ON b.gps_operad = i.nit_gpsglx
                INNER JOIN ".BASE_DATOS.".tab_tercer_tercer j ON c.cod_transp = j.cod_tercer
                WHERE a.cod_consec = ".$cod_consec;
    $query = new Consulta($sql, $this -> conexion);
    $resultados = $query -> ret_matriz('a')[0];
    return $resultados;
  }

  function eliminarRepetidos($string) {
    // Utilizamos una expresión regular para capturar palabras o frases repetidas
    $resultado = preg_replace('/\b(\w+\s\w+)\s\1\b/', '$1', $string);
    return $resultado;
  }
}

$_AJAX = $_REQUEST;
$PDF = new PDFInformeOperativoPreventivo( $_AJAX );
?>
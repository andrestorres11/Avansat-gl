<?php
session_start();
include_once('../lib/FPDF/fpdf.php');

class PDF extends FPDF
{
  
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
    $info = $this -> getDataEstudio($_REQUEST['cod_estseg']);
    /* CREACION PDF */
    
    ob_clean();
    $_IN_X = 10;
    $_IN_Y = 5;
    $_ALTO = 5;

    $pdf = new PDF();
    $pdf -> AddPage('P','Legal');
    
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

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL VEHÍCULO'),1,1,'C',1);

    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);

    $pdf -> Cell(39,5,utf8_decode('Placa'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['num_placax']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('R'),1,0,'L',1);
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
    $pdf -> Cell(52,5,utf8_decode($info['url_gpsxxx']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('Observaciones GPS'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['obs_opegps']),1,1,'L');

    $pdf -> SetFont('Arial','B',8);
    $pdf -> Cell(196,6,utf8_decode('INFORMACIÓN DEL POSEEDOR TENEDOR'),1,1,'C',1);
    $pdf -> SetFont('Arial','B',8);

    $pdf -> Cell(39,5,utf8_decode('APELLIDOS'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['nom_apepos']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['nom_nompos']),1,1,'L');

    $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['num_cedpos']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['ciu_exppos']),1,1,'L');

    $pdf -> SetFont('Arial','B',7);
    $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['dir_respos']),1,0,'L');
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
    $pdf -> Cell(52,5,utf8_decode($info['nom_apepro']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['nom_nompro']),1,1,'L');

    $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['num_cedpro']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('DE'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['ciu_exppro']),1,1,'L');

    $pdf -> SetFont('Arial','B',7);
    $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN DE RESIDENCIA'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['dir_respro']),1,0,'L');
    $pdf -> SetFont('Arial','B',8);
    $pdf -> Cell(39,5,utf8_decode('CIUDAD DE RESIDENCIA'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['ciu_respro']),1,1,'L');

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
    $pdf -> Cell(52,5,utf8_decode($info['nom_apecon']),1,0,'L');
    $pdf -> Cell(39,5,utf8_decode('NOMBRES'),1,0,'L',1);
    $pdf -> Cell(66,5,utf8_decode($info['nom_nomcon']),1,1,'L');

    $pdf -> Cell(39,5,utf8_decode('CÉDULA'),1,0,'L',1);
    $pdf -> Cell(52,5,utf8_decode($info['num_cedcon']),1,0,'L');
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
    $pdf -> Cell(52,5,utf8_decode($info['dir_rescon']),1,0,'L');
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

    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);

    $mSelect = "SELECT b.nom_refere, b.nom_parent, b.dir_domici, b.num_telefo
                    FROM ".BASE_DATOS.".tab_person_refere a
                 INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON
                    a.cod_refere = b.cod_refere
            WHERE a.cod_person = ".$info['cod_conduc']." AND a.tip_refere = 'F'; ";
    $query = new Consulta($mSelect, $this -> conexion);
    $resultados = $query -> ret_matriz('a');

    foreach($resultados as $registro){
      $pdf -> Cell(39,5,utf8_decode('NOMBRE COMPLETO'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($registro['nom_refere']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('PARENTESCO'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($registro['nom_parent']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($registro['dir_domici']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');
    }

    

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,6,utf8_decode('REFERENCIAS PERSONALES DEL CONDUCTOR'),1,1,'C',1);

    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);

    $mSelect = "SELECT b.nom_refere, b.nom_parent, b.dir_domici, b.num_telefo
                    FROM ".BASE_DATOS.".tab_person_refere a
                 INNER JOIN ".BASE_DATOS.".tab_estseg_refere b ON
                    a.cod_refere = b.cod_refere
            WHERE a.cod_person = ".$info['cod_conduc']." AND a.tip_refere = 'P'; ";
    $query = new Consulta($mSelect, $this -> conexion);
    $resultados = $query -> ret_matriz('a');

    foreach($resultados as $registro){
      $pdf -> Cell(39,5,utf8_decode('NOMBRE COMPLETO'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($registro['nom_refere']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('PARENTESCO'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($registro['nom_parent']),1,1,'L');

      $pdf -> Cell(39,5,utf8_decode('DIRECCIÓN'),1,0,'L',1);
      $pdf -> Cell(52,5,utf8_decode($registro['dir_domici']),1,0,'L');
      $pdf -> Cell(39,5,utf8_decode('TELÉFONO'),1,0,'L',1);
      $pdf -> Cell(66,5,utf8_decode($registro['num_telefo']),1,1,'L');
    }

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,6,utf8_decode('REFERENCIAS LABORALES DEL CONDUCTOR'),1,1,'C',1);

    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);

    $mSelect = "SELECT b.nom_transp, b.num_telefo, b.inf_sumini, b.num_viajes
                    FROM ".BASE_DATOS.".tab_person_refere a
                 INNER JOIN ".BASE_DATOS.".tab_estseg_reflab b ON
                    a.cod_refere = b.cod_refere
            WHERE a.cod_person = ".$info['cod_conduc']." AND a.tip_refere = 'L'; ";
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

    $pdf -> SetFont('Arial','',8);
    $txt="NOTA: PARA EL ESTUDIO PRELIMINAR ADJUNTAR LOS SIGUIENTES DOCUMENTOS: CÉDULAS DE CIUDADANÍA - LICENCIA CONDUCCIÓN - LICENCIA DE TRÁNSITO - FORMATO APERTURA HOJA DE VIDA. LOS ANTERIORES DOCUMENTOS DEBEN SER ESCANEADOS EN ALTA RESOLUCION Y A COLOR.";
    $pdf -> MultiCell(196,4,utf8_decode($txt) ,1, 'J',0);
    $txt="DECLARACIÓN DE TRATAMIENTO DE DATOS: De acuerdo con la ley 1581 de 2012, el Decreto reglamentario 1377 de 2015 y las demás normas que lo modifiquen o adicionen, autorizo para que se le dé tratamiento respectivo a mis datos personales y demás información solicitada, en el proceso de registro de asociado de negocio, operación o cualquier información adicional a la que se pueda llegar a tener acceso como consecuencia de la relación comercial. Confirmo y acepto por medio de este documento que he leído y comprendido la política para el manejo de datos personales de la organización.";
    $pdf -> MultiCell(196,4,utf8_decode($txt) ,1, 'J',0);
    $pdf -> SetFont('Arial','',9);
    $txt="AUTORIZACION: Yo, ............................................................................, mayor de edad, identificado con la cédula de ciudadanía número ................................, de ......................................, en calidad de propietario del vehículo de placas XXX-000, AUTORIZO al conductor ya identificado,, para que CEVA FREIGH MANANGMENT SAS, entregue o consigne las sumas correspondientes a saldos y/o pagos de los servicios por concepto de prestación de servicios de transporte terrestre por carretera a esta sociedad.
    Autoriza,                                                            Acepta,";
    $pdf -> MultiCell(196,5,utf8_decode($txt) ,1, 'J',0);
    
    $pdf -> AddPage('P','Legal');
    $img_estcon = $this->getImagenesPerson($info['cod_conduc']);
    $rut_general = URL_APLICA.'files/adj_estseg/';

    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA RIT CONDUCTOR'),1,0,'C',1);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA SIMIT CONDUCTOR'),1,1,'C',1);
    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estcon['fil_conrit'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estcon['fil_simitx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
    $pdf -> SetWidths(array(98,98));
    $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estcon['obs_conrit']),utf8_decode('OBSERVACIÓN: '.$img_estcon['obs_simitx'])));
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA PROCURADURIA CONDUCTOR'),1,0,'C',1);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA RUNT CONDUCTOR'),1,1,'C',1);
    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);

    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estcon['fil_procur'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estcon['fil_runtxx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
    $pdf -> SetWidths(array(98,98));
    $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estcon['obs_procur']),utf8_decode('OBSERVACIÓN: '.$img_estcon['obs_runtxx'])));
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(196,5,utf8_decode('CONSULTA ANTECEDENTES JUDICIALES CONDUCTOR'),1,1,'C',1);
    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);

    $pdf -> Cell(196,50,$pdf->Image($rut_general.''.$img_estcon['fil_ajudic'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
    $pdf -> SetWidths(array(196));
    $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estcon['obs_ajudic'])));

    if($info['cod_poseed'] != $info['cod_conduc']){
      $img_estpos = $this->getImagenesPerson($info['cod_poseed']);
      $pdf -> AddPage('P','Legal');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA RIT POSEEDOR'),1,0,'C',1);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA SIMIT POSEEDOR'),1,1,'C',1);
      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpos['fil_conrit'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpos['fil_simitx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
      $pdf -> SetWidths(array(98,98));
      $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estpos['obs_conrit']),utf8_decode('OBSERVACIÓN: '.$img_estpos['obs_simitx'])));

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA PROCURADURIA POSEEDOR'),1,0,'C',1);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA RUNT POSEEDOR'),1,1,'C',1);
      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpos['fil_procur'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpos['fil_runtxx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
      $pdf -> SetWidths(array(98,98));
      $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estpos['obs_procur']),utf8_decode('OBSERVACIÓN: '.$img_estpos['obs_runtxx'])));

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('CONSULTA ANTECEDENTES JUDICIALES POSEEDOR'),1,1,'C',1);
      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(196,50,$pdf->Image($rut_general.''.$img_estpos['fil_ajudic'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
      $pdf -> SetWidths(array(196));
      $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estpos['obs_ajudic'])));
    }

    if($info['cod_propie'] != $info['cod_conduc'] || $info['cod_propie'] != $info['cod_poseed']){
      $img_estpro = $this->getImagenesPerson($info['cod_propie']);
      $pdf -> AddPage('P','Legal');
      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA RIT PROPIETARIO'),1,0,'C',1);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA SIMIT PROPIETARIO'),1,1,'C',1);
      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpro['fil_conrit'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpro['fil_simitx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
      $pdf -> SetWidths(array(98,98));
      $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estpro['obs_conrit']),utf8_decode('OBSERVACIÓN: '.$img_estpro['obs_simitx'])));

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA PROCURADURIA PROPIETARIO'),1,0,'C',1);
      $pdf -> Cell(98,5,utf8_decode('CONSULTA RUNT PROPIETARIO'),1,1,'C',1);
      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);

      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpro['fil_procur'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
      $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estpro['fil_runtxx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
      $pdf -> SetWidths(array(98,98));
      $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estpro['obs_procur']),utf8_decode('OBSERVACIÓN: '.$img_estpro['obs_runtxx'])));

      $pdf -> SetFont('Arial','B',8);
      $pdf -> SetFillColor(1, 11, 64);
      $pdf -> SetTextColor(255,255,255);
      $pdf -> Cell(196,5,utf8_decode('CONSULTA ANTECEDENTES JUDICIALES PROPIETARIO'),1,1,'C',1);
      $pdf -> SetFillColor(180, 181, 179);
      $pdf -> SetTextColor(0,0,0);
      $pdf -> SetFont('Arial','B',8);
      $pdf -> Cell(196,50,$pdf->Image($rut_general.''.$img_estpro['fil_ajudic'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
      $pdf -> SetWidths(array(196));
      $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estpro['obs_ajudic'])));
    }

    $img_estveh = $this -> getImagenesVehicu( $info['cod_vehicu'] );
    $pdf -> AddPage('P','Legal');
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA OPERADOR GPS'),1,0,'C',1);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA RIT VEHICULO'),1,1,'C',1);
    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);
    
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estveh['fil_congps'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estveh['fil_conrit'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
    $pdf -> SetWidths(array(98,98));
    $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estveh['obs_congps']),utf8_decode('OBSERVACIÓN: '.$img_estveh['obs_conrit'])));
    
    $pdf -> SetFont('Arial','B',8);
    $pdf -> SetFillColor(1, 11, 64);
    $pdf -> SetTextColor(255,255,255);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA RUNT VEHICULO'),1,0,'C',1);
    $pdf -> Cell(98,5,utf8_decode('CONSULTA COMPARENDOS VEHICULO'),1,1,'C',1);
    $pdf -> SetFillColor(180, 181, 179);
    $pdf -> SetTextColor(0,0,0);
    $pdf -> SetFont('Arial','B',8);
    
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estveh['fil_runtxx'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,0,'C');
    $pdf -> Cell(98,50, $pdf->Image($rut_general.''.$img_estveh['fil_compar'], $pdf->GetX(), $pdf->GetY()+2,97,46),1,1,'C');
    $pdf -> SetWidths(array(98,98));
    $pdf -> Row(array(utf8_decode('OBSERVACIÓN: '.$img_estveh['obs_runtxx']),utf8_decode('OBSERVACIÓN: '.$img_estveh['obs_compar'])));
    
    
    $_PDF = 'Resultados_EstudioSeguridad_'.$mData['cod_estseg'].'.pdf';
    $pdf -> Close();
    $pdf -> Output( $_PDF,'D' );
  }

  function getImagenesPerson( $cod_person ){
    $mSelect = "SELECT a.fil_conrit, a.obs_conrit, a.fil_simitx,
                       a.obs_simitx, a.fil_procur, a.obs_procur,
                       a.fil_runtxx, a.obs_runtxx, a.fil_ajudic,
                       a.obs_ajudic
                FROM ".BASE_DATOS.".tab_estudi_person a
                WHERE a.cod_segper = '".$cod_person."';";
    $query = new Consulta($mSelect, $this -> conexion);
    $resultados = $query -> ret_matriz('a')[0];
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
  
  function getDataEstudio( $cod_estseg )
  {
    
    $mSelect = "SELECT b.cod_segveh,
                       a.cod_vehicu,
                       b.num_placax, b.num_remolq, c.nom_config,
                       d.nom_carroc, b.ano_modelo, e.nom_colorx,
                       b.usr_gpsxxx, b.clv_gpsxxx, b.url_gpsxxx,
                       b.obs_opegps,
                       a.cod_poseed,
                       CONCAT(f.nom_apell1, ' ', f.nom_apell2) as 'nom_apepos',
                       f.nom_person as 'nom_nompos', f.num_docume as 'num_cedpos',
                       g.nom_ciudad as 'ciu_exppos', f.dir_domici as 'dir_respos',
                       h.nom_ciudad as 'ciu_respos', f.num_telefo as 'num_telpos',
                       a.cod_propie,
                       CONCAT(i.nom_apell1, ' ', i.nom_apell2) as 'nom_apepro',
                       i.nom_person as 'nom_nompro', i.num_docume as 'num_cedpro',
                       j.nom_ciudad as 'ciu_exppro', i.dir_domici as 'dir_respro',
                       k.nom_ciudad as 'ciu_respro', i.num_telefo as 'num_telpro',
                       a.cod_conduc,
                       CONCAT(l.nom_apell1, ' ', l.nom_apell2) as 'nom_apecon',
                       l.nom_person as 'nom_nomcon', i.num_docume as 'num_cedcon',
                       m.nom_ciudad as 'ciu_expcon', l.num_licenc, l.fec_venlic,
                       l.nom_arlxxx, l.nom_epsxxx, l.dir_domici as 'dir_rescon',
                       n.nom_ciudad as 'ciu_rescon', l.num_telefo as 'num_telcon'
                  FROM ".BASE_DATOS.".tab_relaci_estseg a
            INNER JOIN ".BASE_DATOS.".tab_estudi_vehicu b ON 
                  a.cod_vehicu = b.cod_segveh
            LEFT JOIN ".BASE_DATOS.".tab_vehige_config c ON
                  b.num_config = c.num_config
            LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc d ON
                  b.cod_carroc = d.cod_carroc
            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore e ON
                  b.cod_colorx = e.cod_colorx
            LEFT JOIN ".BASE_DATOS.".tab_estudi_person f ON
                  a.cod_poseed = f.cod_segper
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad g ON
                 f.ciu_expdoc = g.cod_ciudad
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad h ON
                 f.cod_ciudad = h.cod_ciudad AND f.cod_depart = h.cod_depart AND f.cod_paisxx = h.cod_paisxx
            LEFT JOIN ".BASE_DATOS.".tab_estudi_person i ON
                 a.cod_propie = i.cod_segper
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad j ON
                i.ciu_expdoc = j.cod_ciudad
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad k ON
                i.cod_ciudad = k.cod_ciudad AND i.cod_depart = k.cod_depart AND i.cod_paisxx = k.cod_paisxx
            INNER JOIN ".BASE_DATOS.".tab_estudi_person l ON
                a.cod_conduc = l.cod_segper
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad m ON
                l.ciu_expdoc = m.cod_ciudad
            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad n ON
                l.cod_ciudad = n.cod_ciudad AND l.cod_depart = n.cod_depart AND l.cod_paisxx = n.cod_paisxx
            WHERE a.cod_estseg = ".$cod_estseg."; ";

    $query = new Consulta($mSelect, $this -> conexion);
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

}

$_AJAX = $_REQUEST;
$PDF = new PDFInformeEstudioSeguridad( $_AJAX );
?>
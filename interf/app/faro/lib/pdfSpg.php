<?php
ini_set('display_errors', false);
error_reporting(E_ALL & ~E_NOTICE);
			// se incluye el dompdf

      // ---------------------------------------------------------------------------------------------------------------------------------
      // Generar el PDF de las respuestas ------------------------------------------------------------------------------------------------
      // ---------------------------------------------------------------------------------------------------------------------------------

      //include_once('pdfResponseForm.php');
      //$mPdf = new pdfSpg($fConsult, $mCodForans );
      //$mPdf = $mPdf -> getPdfResponseForm();
      //echo $mPdf;



class pdfSpg
{
	private static $fConsult =  NULL;
	private static $mDataToPdf =  NULL;
	private static $fUsuari =  NULL;
	private static $cPdf =  NULL;
	private static $cLogoOetPdf =  NULL;

	public function __construct($mConecction = NULL, $mDataToPdf = NULL)
	{
		self::$fConsult = $mConecction;
		self::$mDataToPdf = $mDataToPdf;
		self::$fUsuari = $fUsuari; 
		self::$cLogoOetPdf = 'Logo_oet_pdf_spg.PNG'; 
		if(!require_once( '/var/www/html/ap/interf/app/spg_new/fpdf_2021/alphapdf.php') ) {
			die('pailas pdf');
		}


		//return self::getPdfResponseForm( );
	}


	public function getPdfResponseForm()
	{  
 		try 
 		{ 
 			// inicia la clase de fpdf, AlphaPDF es una extencion de fpdf para poder meter marcas de agua, lo demas sigue funcionando igual
			self::$cPdf = new AlphaPDF('P', 'mm', 'letter' );
			self::$cPdf -> SetDrawColor(50, 50, 50);
			self::$cPdf -> SetTextColor(0); 
 			 
		
			// ------------------------------------------------------ P A G IN A  1  ---------------------------------------------------------------------
			self::$cPdf -> AddPage();

			//        ----------------------------------------------- C A B E C E R A --------------------------------------------------------------------
						// ------ L O G O ----------
			//oet_logo.png  
			list($w1, $h1) = getimagesize('plantillas/'.self::$cLogoOetPdf);
			$w1 = $w1 / 6.5;
			$h1 = $h1 / 6.5;
			self::$cPdf -> Image('plantillas/'.self::$cLogoOetPdf , 16, 15, $w1, $h1);



			self::$cPdf -> SetLineWidth(0.8);
			//	self::$cPdf -> AddFont('Comic', 'I');
			self::$cPdf -> SetFont('Arial', 'B', 10);
			self::$cPdf -> SetFont('Courier', 'B', 14);
			self::$cPdf -> SetXY(5 ,self::$cPdf -> GetY() - 5);
			self::$cPdf -> MultiCell(55,30, '','LTB','C');
			self::$cPdf -> SetXY(self::$cPdf -> GetX() + 50 ,self::$cPdf -> GetY() - 30 );
			self::$cPdf -> MultiCell(100,30, self::$mDataToPdf['dataCabecera']['nam_formxx'],1,'C'); 
			self::$cPdf -> SetFont('Arial', 'B', 6);
			self::$cPdf -> SetXY(self::$cPdf -> GetX() + 150 ,self::$cPdf -> GetY() - 30 );
			self::$cPdf -> Cell(20, 7.5, utf8_decode('CÓDIGO:'), 1, 0, 'C');
			self::$cPdf -> Cell(30, 7.5, utf8_decode('REG-FR-01'), 1, 1, 'C');


			// ---------------------------------------------------------------------------------------------------------------------------------------

			self::$cPdf -> SetXY(self::$cPdf -> GetX() + 150   ,self::$cPdf -> GetY()  );
			 
			self::$cPdf -> Cell(20, 7.5, utf8_decode('VERSIÓN:'), 1, 0, 'C');
			self::$cPdf -> Cell(30, 7.5, utf8_decode('6'), 1, 1, 'C');	 

			self::$cPdf -> SetXY(self::$cPdf -> GetX() + 150   ,self::$cPdf -> GetY()  );
			self::$cPdf -> Cell(20, 7.5, utf8_decode('FECHA:'), 1, 0, 'C');
			self::$cPdf -> Cell(30, 7.5, utf8_decode('31/01/2018'), 1, 1, 'C');
			//self::$cPdf -> SetDrawColor(200,220,255);
			self::$cPdf -> SetXY(self::$cPdf -> GetX() + 150   ,self::$cPdf -> GetY()  );
			self::$cPdf -> Cell(50, 3.75, utf8_decode('INFORMACION INTERNA'), 'RT', 1, 'C');			 
			self::$cPdf -> SetXY(self::$cPdf -> GetX() + 150   ,self::$cPdf -> GetY()  );
			self::$cPdf -> Cell(50, 3.75, utf8_decode('PAGINA 1 de 2'), 'RB', 1, 'C');			 
			//  ----------------------------------------------- F I N  C A B E C E R A -------------------------------------------------------------

			self::$cPdf -> Cell(200, 1, '', 0,1, 'L');			
		 
 			 
  			// ------------------------------------------ D A T O S  D E  C A B E C E R A  --------------------------------------------------------- 
			self::$cPdf -> SetLineWidth(0.2);
			self::$cPdf -> SetDrawColor(222, 218, 195);
			$mX = 5;
			self::$cPdf -> SetFont('Arial', 'B', 10);
			//self::$cPdf -> SetX($mX);	
			self::$cPdf -> Cell(100, 5, 'Usuario', 'TB', 0, 'L'); 
			self::$cPdf -> Cell(100, 5, utf8_decode(self::$mDataToPdf['dataCabecera']['nom_usuari']), 'TB', 1, 'L');
 			
 			//self::$cPdf -> SetX($mX);
			//self::$cPdf -> Cell(100, 5, 'Mapa', 0, 0, 'L');
			//self::$cPdf -> Cell(100, 5, 'Mapa', 'TB', 1, 'L');
 			//self::$cPdf -> SetX($mX);
			// self::$cPdf -> Cell(100, 5, 'Nombre del cliente', 'TB', 0, 'L');
			// self::$cPdf -> Cell(100, 5, utf8_decode(self::$mDataToPdf['dataCabecera']['company_name']), 'TB', 1, 'L'); 			
			//self::$cPdf -> SetX($mX);
			// self::$cPdf -> Cell(100, 5, 'Numero de Telefono', 'TB', 0, 'L');
			// self::$cPdf -> Cell(100, 5, utf8_decode(self::$mDataToPdf['dataCabecera']['company_phone1']), 'TB', 1, 'L');	
			//echo "<pre>"; print_r(self::$mDataToPdf['dataCabecera']); echo "</pre>";  die();

			// recorre respuestas a mostrar -------------------------------------------------------------------------------------------------------
			foreach (self::$mDataToPdf['dataRespuestas'] AS $mIndex => $mFormRespon) 
			{ 
				//self::$cPdf -> SetX($mX);
				$folder = '/usr/local/apache2/htdocs/spg/files/' . $mFormRespon['cod_projec'];
				
				if ($mFormRespon['cod_typfie'] == 'CAMERA') {
					self::$cPdf -> Cell(100, 70, utf8_decode(utf8_encode($mFormRespon['nam_forfie'])), 'TB', 0, 'L');
					self::$cPdf -> SetFont('Arial', '', 10);
					$nameImage = self::getNameImage($mFormRespon);
					$nameFile = $folder."/".$nameImage;
					self::$cPdf -> MultiCell(0, 70, self::$cPdf -> Image($nameFile ,  self::$cPdf->GetX()+5, self::$cPdf->GetY()+5, 0, 40, 'jpeg'), 1, 'R');
				}else if ($mFormRespon['cod_typfie'] == 'FIRM') {
					self::$cPdf -> Cell(100, 70, utf8_decode(utf8_encode($mFormRespon['nam_forfie'])), 'TB', 0, 'L');
					self::$cPdf -> SetFont('Arial', '', 10);
					$nameImage = self::getNameImage($mFormRespon);
					$nameFile = $folder."/".$nameImage;
					self::$cPdf -> MultiCell(0, 70, self::$cPdf -> Image($nameFile ,  self::$cPdf->GetX()+5, self::$cPdf->GetY()+5, 0, 15, 'png'), 1, 'R');
				}else{
					self::$cPdf -> Cell(100, 5, utf8_decode(utf8_encode($mFormRespon['nam_forfie'])), 'TB', 0, 'L');
					self::$cPdf -> SetFont('Arial', '', 10);
					self::$cPdf -> MultiCell(100, 5, utf8_decode(utf8_encode($mFormRespon['val_forans'])), 1, 'L');
				}
			}		
 			// -------------------------------------------------------------------------------------------------------------------------------------
 			 
 			// ------------------------------------------------------------------------------------------------------------------------------------------------
 			
 			
 		 
			/*
			// Logo de vigilado -----------------------------------------------------------------------------------------------------------------------------
			list($w11, $h11) = getimagesize($mLogovigilado);
			$w11 = $w11 / 9;
			$h11 = $h11 / 9;
			self::$cPdf -> Image($mLogovigilado , 5, 95, $w11, $h11);
			// ---------------------------------------------------------------------------------------------------------------------------------------------
			// FIRMA DIGIAL    -----------------------------------------------------------------------------------------------------------------------------
			list($w12, $h12) = getimagesize($firma);
			$w12 = $w12 / 9;
			$h12 = $h12 / 9;
			self::$cPdf -> Image($firma , $mLineFila + 9, $pdf -> getY() - 6, $w12, $h12);
			*/
 
			
		 	self::$cPdf -> Ln();
		  
      
			// nombre del PDF
			//echo $filename = 'Acta_Visita_'.self::$mDataToPdf['dataCabecera']['nam_formxx'].'_'.self::$mDataToPdf['dataCabecera']['company_name'].'.pdf'; 
			//die();
			//$output = self::$cPdf -> Output($filename, 'I');
			$output = self::$cPdf -> Output($filename, 'S');
			
 
			// devuelve el string
				//throw new Exception('NO HAY DATOS DE CERTIFICADO EN EL PDF', 1001 );
 			return   base64_encode($output);
			 
 		} 
 		catch (Exception $e) 
 		{
			return $e -> getMessage();
 		}

	}

	public function GetAmparoDataMapfre($codpolsol, $codProduc) 
	{

		$amparo = "SELECT c.nom_amparo, b.val_asegur, a.num_poliza, a.num_certif,
             a.cod_proveh, UPPER(a.nom_proveh), a.cod_conduc, UPPER(a.nom_conduc),
             UPPER(a.ape_conduc), a.ind_estado, e.val_deduci, a.idx_ventax, a.num_cermap, c.cod_amparo , e.id_deduci
                 FROM " . BDADS . ".t_trayec_solici a,
                      " . BDADS . ".t_trasol_amparo b,
                      " . BDADS . ".t_genera_amparo c,
                      " . BDADS . ".t_trayec_tarifa e

                WHERE a.cod_polsol = b.cod_polsol
                  AND b.cod_amparo = c.cod_amparo
                  AND e.num_poliza = a.num_poliza
                  AND e.cod_asegur = a.cod_asegur
                  AND e.cod_tercer = a.cod_tercer
                  AND e.cod_amparo = c.cod_amparo
                  AND a.cod_consek = e.cod_consec

                  AND a.cod_polsol = '" . $codpolsol . "'
                  AND e.cod_produc = '" . $codProduc . "' ORDER BY c.cod_amparo ";
		//echo "<br>".$amparo."<br>";
		self::$fConsult->ExecuteCons($amparo);
		return self::$fConsult->RetMatrix( );
	}

	public function GetDeduci($Deduci_id) {
		$query = "SELECT nom_decuci 
		            FROM " . BDADS . ".t_tipo_deduci
                   WHERE id_deduci = '" . $Deduci_id . "' ";
		self::$fConsult->ExecuteCons($query);
		$Deduci = self::$fConsult->RetMatrix( );
		return $Deduci[0][0]; 
	}

	public function getNameImage($mFormRespon){
		try{
			$sql = "SELECT file_real_filename 
							  FROM files 
							 -- WHERE file_project = ':fileProjec' 
							   WHERE file_name = ':fileName' 
							   	AND file_project = ':fileProjec'
							    AND file_task = ':fileTask' 
							   ORDER BY  file_id DESC
							   LIMIT 1";		
							   
			$data = [
				":fileProjec" => $mFormRespon['cod_projec'],
				":fileTask"   => self::$mDataToPdf['dataCabecera']['task_idxxxx'],
				":fileName"   => $mFormRespon['val_forans']
			];

			$sql = str_replace(array_keys($data), array_values($data), $sql);

			self::$fConsult->ExecuteCons($sql);
			$nameFile1 = self::$fConsult->RetMatrix('a');
			//mail("andres.torres@eltransporte.org", "holaaaaaa", var_export($nameFile1[0]['file_real_filename'], true));
			return $nameFile1[0]['file_real_filename'];
		}catch (Exception $e) {
			return array("cod_respon"=>$e->getCode(),"msg_respon"=>$e->getMessage(), "dat_status" =>false, "bin_pdfxxx" => NULL);
		}
	}
}


 
?>
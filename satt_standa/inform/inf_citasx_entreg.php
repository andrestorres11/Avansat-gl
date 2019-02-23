<?php
/*! \file: inf_gescir_entreg.php
 *  \brief: Clase encargada de generar informe de citas de entrega, tipo indicador de cumplimiento
 *  \author: Ing. Nelson Liberato
 *  \author: nelson.liberato@eltransporte.org
 *  \version: 2.0
 *  \date: 19/02/2019
 *  \bug: 
 *  \warning: 
 */
date_default_timezone_get('America/Bogota');

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

class InfGestionCitasDeEntrega
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cDespac,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion  = $AjaxConnection;
			self::$cUsuario   = $_SESSION['datos_usuario'];
			self::$cCodAplica = $_SESSION['codigo'];
		}else{ 

			 
			self::$cConexion  = $co;
			self::$cUsuario   = $us;
			self::$cCodAplica = $ca;
		}

		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		switch($_REQUEST['Option']){
			case 'informGeneral':
				self::informGeneral();
				break;

			case 'expTabExcelReport':
				self::expTabExcelReport();
				break;
			case 'infDetail':
				self::infDetail();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para aplicar los filtros del informe
	 *  \author: Ing. Nelson Liberato
	 *  \date: 05/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formulario(){ 
		 
		$mTD = array("class"=>"cellInfo1", "width"=>"25%");

		$mClienteCita = self::getClientesCitas();
		$mNegocioCita = self::getNegociosCitas();
		


		echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jqplot/jquery.jqplot.js"></script>';
		echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jqplot/plugins/jqplot.pieRenderer.js"></script>';
		echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jqplot/plugins/jqplot.enhancedPieLegendRenderer.js"></script>';
		echo '<link rel="stylesheet" type="text/css" href="../'.DIR_APLICA_CENTRAL.'/js/jqplot/jquery.jqplot.css" />';

		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'inf_citas_entreg.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";


		// echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>';





		$mHtml = new Formlib(2);
		
		$mHtml->SetCss("jquery");
		$mHtml->SetCss("informes");
		$mHtml->SetCss("validator");

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->SetBody('<form name="form_InfGestionCitasDeEntrega" id="form_InfGestionCitasDeEntregaID"  method="post">');
		$mHtml->OpenDiv("class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->OpenDiv("id:formID; class:Style2DIV");

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros</th></tr>');

						$mHtml->Label( "Cliente: ", $mTD );
						$mHtml->Select2( $mClienteCita, array_merge($mTD, array("name"=>"cod_client", "id"=>"cod_clientID", "class"=>"cellInfo1 multiSel")) );
						$mHtml->Label( "Negocio: ", $mTD );
						$mHtml->Select2( $mNegocioCita, array_merge($mTD, array("name"=>"cod_negoci", "id"=>"cod_negociID", "class"=>"cellInfo1 multiSel", "end"=>true)) );

 
						$mSql = "SELECT DATE(DATE_SUB( NOW(), INTERVAL 5 DAY ) ) AS fec_inicia,  DATE(NOW()) AS fec_finali"; 
						$mConsult = new Consulta($mSql, self::$cConexion );
						$mFecha = $mConsult -> ret_matrix('a');
						$mFecha = $mFecha[0];

						$mHtml->Label( "Fecha Inicial: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date", "readonly"=>"true", "value"=> $mFecha['fec_inicia'])) );
						$mHtml->Label( "Fecha Final: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date", "readonly"=>"true", "value"=> $mFecha['fec_finali'])) );

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>"central" ) ); 
						$mHtml->Hidden( array("name"=>"Option", "id"=>"OptionID", "value"=>"expTabExcelReport") ); 
						$mHtml->Hidden( array("name"=>"Ajax", "id"=>"AjaxID", "value"=>"on") ); 
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();


				#Tabs -----------------
				$mHtml->OpenDiv("id:tabs");
					$mHtml->SetBody('<ul>');
						$mHtml->SetBody('<li><a id="liReport" href="#tabs-report" style="cursor:pointer" onclick="report(\'report\', \'tabs-report\')">REPORTE</a></li>');
					$mHtml->SetBody('</ul>');

					$mHtml->SetBody('<div id="tabs-report"></div>'); #DIV REPORTE
				$mHtml->CloseDiv();
				// -------------------

			$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();
		$mHtml->SetBody('</form>');
		echo $mHtml->MakeHtml();
	}

	/*! \fn: getClientesCitas
	 *  \brief: Trae clientes registrados en la tabla de citas
	 *  \author: Ing. Nelson Liberato
	 *  \date: 20/02/2019
	 *  \date modified: dd/mm/aaaa
	 *  \param: mCodContro  String  Codigo de PC
	 *  \return: Matriz
	 */
	private function getClientesCitas()
	{
		$mSql = "SELECT MD5(a.nom_client) AS codclie, a.nom_client
				   FROM ".BASE_DATOS.".tab_citasx_entreg a 
				  WHERE  1 = 1
			   GROUP BY  1 
			   ORDER BY  2"; 
		$mConsult = new Consulta($mSql, self::$cConexion );
		return array_merge( [0=>['','Seleccione']], $mConsult -> ret_matrix('i') );
	}

	/*! \fn: getNegociosCitas
	 *  \brief: Trae negocios registrados en la tabla de citas
	 *  \author: Ing. Nelson Liberato
	 *  \date: 20/02/2019
	 *  \date modified: dd/mm/aaaa
	 *  \param: mCodContro  String  Codigo de PC
	 *  \return: Matriz
	 */
	private function getNegociosCitas()
	{
		$mSql = "SELECT MD5(a.nom_negoci) AS codclie, a.nom_negoci
				   FROM ".BASE_DATOS.".tab_citasx_entreg a 
				  WHERE  1 = 1
			   GROUP BY  1 
			   ORDER BY  2"; 
		$mConsult = new Consulta($mSql, self::$cConexion );
		return array_merge( [0=>['','Seleccione']], $mConsult -> ret_matrix('i') );
	}


	/*! \fn: informGeneral
	 *  \brief: Pinta el informe general
	 *  \author: Ing. Nelson Liberato
	 *  \date: 18/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:
	 *  \return: 
	 */
	private function informGeneral()
	{ 

		$mCount = self::getCounts();

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");
		 
			if( $mCount['total_generadas'] <= 0 )
			{
				$mHtml->Table('tr');
					$mHtml->Label( "No se encontraron Registros.", array("class"=>"cellInfo1", "align"=>"center") );
				$mHtml->CloseTable('tr');
			}
			else
			{

				$mHtml->SetBody('<center>
									<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelReportx( 1 );" style="cursor:pointer">
								 </center><br>');



 
				$mDaysx = self::getDetalleDias();
				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------			
				// ------------------------------------------------------  I N I C I O    T A B L A      U N O   ------------------------------------------------------------------------------------------------------			
				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------		
				$mHtmlx  = '<table width="100%" border="1">';
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td rowspan="2" align="center" class="CellHead" >Generadas</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >Cumplidas</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >Porcentaje</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >No Cumplidas</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >Porcentaje</td>';
					$mHtmlx .= '</tr>';					
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
					$mHtmlx .= '</tr>';			
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td align="center">'.$mCount['total_generadas'].'</td>';
						$mHtmlx .= '<td align="center">'.$mCount['cum_repro'].'</td>';
						$mHtmlx .= '<td align="center">'.$mCount['cum_solic'].'</td>';
						$mHtmlx .= '<td align="center">'.$mCount['cum_repro_per'].'%</td>';
						$mHtmlx .= '<td align="center">'.$mCount['cum_solic_per'].'%</td>';
						$mHtmlx .= '<td align="center">'.$mCount['no_cum_repro'].'</td>';
						$mHtmlx .= '<td align="center">'.$mCount['no_cum_solic'].'</td>';
						$mHtmlx .= '<td align="center">'.$mCount['no_cum_repro_per'].'%</td>';
						$mHtmlx .= '<td align="center">'.$mCount['no_cum_solic_per'].'%</td>';
					$mHtmlx .= '</tr>'; 
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td align="center" class="CellHead">Total</td>';
						$mHtmlx .= '<td colspan="2" align="center">'.($mCount['cum_repro'] + $mCount['cum_solic']).' 
										<input type="hidden" id="cumplidosID" value="'.($mCount['cum_repro'] + $mCount['cum_solic']).'" />
									</td>';
						$mHtmlx .= '<td colspan="2" align="center">'.($mCount['cum_repro_per'] + $mCount['cum_solic_per']).'%</td>';
						$mHtmlx .= '<td colspan="2" align="center">'.($mCount['no_cum_repro'] + $mCount['no_cum_solic']).' 
										<input type="hidden" id="No_cumplidosID" value="'.($mCount['no_cum_repro'] + $mCount['no_cum_solic']).'" />
									</td>';
						$mHtmlx .= '<td colspan="2" align="center">'.($mCount['no_cum_repro_per'] + $mCount['no_cum_solic_per']).'%</td>'; 
					$mHtmlx .= '</tr>';
				$mHtmlx .= '</table>';


				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------			
				// ------------------------------------------------------  I N I C I O    T A B L A     G R A F I C A  ------------------------------------------------------------------------------------------------			
				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------	

				$mHtmlx .= '<table width="25%" border="1" align="center">';
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td><div id="chart1"></div></td>';
					$mHtmlx .= '</tr>';	
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td><div id="plotdiv" style="display:none">aca_va_la_imagen</div></td>';
					$mHtmlx .= '</tr>';
				$mHtmlx .= '</table>';


				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------			
				// ------------------------------------------------------  I N I C I O    T A B L A    D O S  ---------------------------------------------------------------------------------------------------------			
				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------	

				$mHtmlx  .= '<table width="100%" border="1">';
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td rowspan="2" align="center" class="CellHead" >Fecha Solicitud</td>';
						$mHtmlx .= '<td rowspan="2" align="center" class="CellHead" >Generadas</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >Cumplidas</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >Porcentaje</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >No Cumplidas</td>';
						$mHtmlx .= '<td colspan="2" align="center" class="CellHead" >Porcentaje</td>';
					$mHtmlx .= '</tr>';					
					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Reprogramadas</td>';
						$mHtmlx .= '<td align="center" class="CellHead" >Solicitadas</td>';
					$mHtmlx .= '</tr>';	
			 		$mTotalGeneral = 0;
			 		$mTotalCumplidasx = 0;
			 		$mPorceCumplidasx = 0;
			 		$mTotalNoCumplida = 0;
			 		$mPorceNoCumplida = 0;
					foreach ($mDaysx AS $mIndex => $mDay ) 
					{
						$mHtmlx .= '<tr>';
							$mHtmlx .= '<td align="center">'.$mIndex.'</td>';
							$mHtmlx .= '<td align="center">'.$mDay['total_generadas'].'</td>';
							$mHtmlx .= '<td align="center">'.$mDay['cum_repro'].'</td>';
							$mHtmlx .= '<td align="center">'.$mDay['cum_solic'].'</td>';
							$mHtmlx .= '<td align="center">'.$mDay['cum_repro_per'].'%</td>';
							$mHtmlx .= '<td align="center">'.$mDay['cum_solic_per'].'%</td>';
							$mHtmlx .= '<td align="center">'.$mDay['no_cum_repro'].'</td>';
							$mHtmlx .= '<td align="center">'.$mDay['no_cum_solic'].'</td>';
							$mHtmlx .= '<td align="center">'.$mDay['no_cum_repro_per'].'%</td>';
							$mHtmlx .= '<td align="center">'.$mDay['no_cum_solic_per'].'%</td>';
						$mHtmlx .= '</tr>';	


						$mTotalGeneral 	  += $mDay['total_generadas'];
						$mTotalCumplidasx += ( $mDay['cum_repro'] 		 + $mDay['cum_solic'] 		 );
						$mPorceCumplidasx += ( $mDay['cum_repro_per'] 	 + $mDay['cum_solic_per'] 	 );
						$mTotalNoCumplida += ( $mDay['no_cum_repro'] 	 + $mDay['no_cum_solic'] 	 );
						$mPorceNoCumplida += ( $mDay['no_cum_repro_per'] + $mDay['no_cum_solic_per'] );
					}

					$mHtmlx .= '<tr>';
						$mHtmlx .= '<td align="center" class="CellHead">Total</td>';
						$mHtmlx .= '<td align="center" class="CellHead">'.$mTotalGeneral.'</td>';
						$mHtmlx .= '<td align="center" colspan="2" class="CellHead">'.$mTotalCumplidasx.'</td>';
						$mHtmlx .= '<td align="center" colspan="2" class="CellHead">'.$mPorceCumplidasx.'%</td>';
						$mHtmlx .= '<td align="center" colspan="2" class="CellHead">'.$mTotalNoCumplida.'</td>';
						$mHtmlx .= '<td align="center" colspan="2" class="CellHead">'.$mPorceNoCumplida.'%</td>'; 
					$mHtmlx .= '</tr>';	

				$mHtmlx .= '</table>';

				$_SESSION['indicadorCitaEntrega'] = $mHtmlx;
				 
				$mHtml->SetBody( $mHtmlx);
				$mHtml->SetBody( '<textarea name="imageBinPlot" id="imageBinPlotID" style="display:none"></textarea>' );
				#</totales>
 
			}

		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getCounts
	 *  \brief: Trae los tados para el informe de transportadoras vs esferas
	 *  \author: Ing. Nelson Liberato
	 *  \date: 19/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mContro  String  Lista de PC 
	 *  \return: Matriz
	 */
	private function getCounts($mType = 'total')
	{

		$mReturn = [];
		$mReturn['total_generadas'] = 0;
		$mReturn['cum_repro'] = 0;
		$mReturn['cum_repro_per'] = 0;
		$mReturn['cum_solic'] = 0;
		$mReturn['cum_solic_per'] = 0; 

		$mReturn['no_cum_repro'] = 0;
		$mReturn['no_cum_repro_per'] = 0;		
		$mReturn['no_cum_solic'] = 0;
		$mReturn['no_cum_solic_per'] = 0;

		$mFiltroCli = $_REQUEST['cod_client'] ? " AND MD5( a.nom_client ) IN  ('".join( "','", explode(",", $_REQUEST['cod_client'] ) )."') ": NULL; 
		$mFiltroNeg = $_REQUEST['cod_negoci'] ? " AND MD5( a.nom_negoci ) IN  ('".join( "','", explode(",", $_REQUEST['cod_negoci'] ) )."') ": NULL; 

		// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
		// TOTAL GENERADAS ------------------------------------------------------------------------------------------------------------------------------------------------
		$mQuery1 ="		
				SELECT 
						COUNT(a.num_consec) AS total_generadas, GROUP_CONCAT(a.num_consec) AS num_consec
				FROM
						tab_citasx_entreg a					
				WHERE
						DATE(a.fec_creaci) BETWEEN '".$_REQUEST['fec_inicia']."' AND '".$_REQUEST['fec_finali']."' 
						$mFiltroCli
						$mFiltroNeg

				" ; 
		$mConsult = new Consulta($mQuery1, self::$cConexion );		
		$total_generadas = $mConsult -> ret_matrix('a');
		$total_generadas = $total_generadas[0];
		$mReturn['total_generadas'] = $total_generadas['total_generadas'];


		if($mReturn['total_generadas'] <= 0) {
			return $mReturn;
		}


		// ****************************************************************************************************************************************************************************************************************************************************************
		// GENERADAS CUMPLIDAS REPROGRAMADAS ******************************************************************************************************************************************************************************************************************************
		$mQuery2 = "SELECT 
							COUNT( a.num_consec ) AS cum_repro
					  FROM 
					  		tab_citasx_entreg a 
				INNER JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
					 WHERE
					 		a.num_consec IN ( ".$total_generadas['num_consec']." )
					 	AND a.ind_cumple = 1 
					 	AND b.ind_reprog = 1
					 	AND b.ind_cumple = 1

					";
		$mConsult = new Consulta($mQuery2, self::$cConexion );		
		$cumplidas_reprogramadas = $mConsult -> ret_matrix('a');
		$cumplidas_reprogramadas = $cumplidas_reprogramadas[0];
		$mReturn['cum_repro'] = $cumplidas_reprogramadas['cum_repro'];
		$mReturn['cum_repro_per'] = $cumplidas_reprogramadas['cum_repro'] == 0 ? $cumplidas_reprogramadas['cum_repro'] :  ( ( $cumplidas_reprogramadas['cum_repro'] * 100 ) / $total_generadas['total_generadas']   );
		$mReturn['cum_repro_per'] = number_format($mReturn['cum_repro_per'], 2);


		// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
		// GENERADAS CUMPLIDAS SOLICITADAS --------------------------------------------------------------------------------------------------------------------------------
		$mQuery3 = "SELECT 
							COUNT( a.num_consec ) AS cum_solic
					  FROM 
					  		tab_citasx_entreg a 
				LEFT  JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
					 WHERE
					 		a.num_consec IN ( ".$total_generadas['num_consec']." )
					 	AND a.ind_cumple = 1 
					 	AND b.ind_cumple IS NULL
					";
		$mConsult = new Consulta($mQuery3, self::$cConexion );		
		$cum_solic = $mConsult -> ret_matrix('a');
		$cum_solic = $cum_solic[0];
		$mReturn['cum_solic'] = $cum_solic['cum_solic'];
		$mReturn['cum_solic_per'] = $cum_solic['cum_solic'] == 0 ? $cum_solic['cum_solic'] :  ( ( $cum_solic['cum_solic'] * 100 ) / $total_generadas['total_generadas']  );
		$mReturn['cum_solic_per'] = number_format($mReturn['cum_solic_per'], 2);




		// ****************************************************************************************************************************************************************************************************************************************************************
		// GENERADAS NO CUMPLIDAS REPROGRAMADAS ***************************************************************************************************************************************************************************************************************************
		$mQuery2 = "SELECT 
							COUNT( a.num_consec ) AS no_cum_repro
					  FROM 
					  		tab_citasx_entreg a 
				INNER JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
					 WHERE
					 		a.num_consec IN ( ".$total_generadas['num_consec']." )
					 	AND a.ind_cumple = 0 
					 	AND b.ind_reprog = 1
					 	AND b.ind_cumple = 0
					";
		$mConsult = new Consulta($mQuery2, self::$cConexion );		
		$no_cum_repro = $mConsult -> ret_matrix('a');
		$no_cum_repro = $no_cum_repro[0];
		$mReturn['no_cum_repro'] = $no_cum_repro['no_cum_repro'];
		$mReturn['no_cum_repro_per'] = $no_cum_repro['no_cum_repro'] == 0 ? $no_cum_repro['no_cum_repro'] :  ( ( $no_cum_repro['no_cum_repro'] * 100 ) / $total_generadas['total_generadas'] );
		$mReturn['no_cum_repro_per'] = number_format($mReturn['no_cum_repro_per'], 2);


		// -------------------------------------------------------------------------------------------------------------------------------------------------------------------
		// GENERADAS NO CUMPLIDAS SOLICITADAS --------------------------------------------------------------------------------------------------------------------------------
		$mQuery3 = "SELECT 
							COUNT( a.num_consec ) AS no_cum_solic
					  FROM 
					  		tab_citasx_entreg a 
				INNER JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
					 WHERE
					 		a.num_consec IN ( ".$total_generadas['num_consec']." )
					 	AND a.ind_cumple = 0 
					 	AND b.ind_cumple = 0
					 	AND b.ind_reprog = 0
					";
		$mConsult = new Consulta($mQuery3, self::$cConexion );		
		$no_cum_solic = $mConsult -> ret_matrix('a');
		$no_cum_solic = $no_cum_solic[0];
		$mReturn['no_cum_solic'] = $no_cum_solic['no_cum_solic'];
		$mReturn['no_cum_solic_per'] = $no_cum_solic['no_cum_solic'] == 0 ? $no_cum_solic['no_cum_solic'] :  ( ( $no_cum_solic['no_cum_solic'] * 100 ) / $total_generadas['total_generadas'] );
		$mReturn['no_cum_solic_per'] = number_format($mReturn['no_cum_solic_per'], 2);

		// echo "<pre>"; print_r($mReturn); echo "</pre>";
		return $mReturn;
	}


	/*! \fn: getDetalleDias
	 *  \brief: Trae lo mismo pero por días
	 *  \author: Ing. Nelson Liberato
	 *  \date: 19/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mContro  String  Lista de PC 
	 *  \return: Matriz
	 */
	private function getDetalleDias($mType = 'total')
	{

		$mReturn = []; 
		$mFiltroCli = $_REQUEST['cod_client'] ? " AND MD5( a.nom_client ) IN  ('".join( "','", explode(",", $_REQUEST['cod_client'] ) )."') ": NULL; 
		$mFiltroNeg = $_REQUEST['cod_negoci'] ? " AND MD5( a.nom_negoci ) IN  ('".join( "','", explode(",", $_REQUEST['cod_negoci'] ) )."') ": NULL; 

 
		$date = $_REQUEST['fec_inicia']; 
		$end_date =$_REQUEST['fec_finali']; 
		// recore los días en la fecha inicial y la final para sacar las solicitudes registradas dia por dia
		//
		while (strtotime($date) <= strtotime($end_date)) 
		{
			// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
			// TOTAL GENERADAS ------------------------------------------------------------------------------------------------------------------------------------------------
			$mQuery1 ="		
					SELECT 
							COUNT(a.num_consec) AS total_generadas, GROUP_CONCAT(a.num_consec) AS num_consec
					FROM
							tab_citasx_entreg a					
					WHERE
							DATE(a.fec_creaci) = '".$date."'
							$mFiltroCli
							$mFiltroNeg
					" ;
			$mConsult = new Consulta($mQuery1, self::$cConexion );		
			$total_generadas = $mConsult -> ret_matrix('a');
			$total_generadas = $total_generadas[0];
			$mReturn[$date]['total_generadas'] = $total_generadas['total_generadas'];


			// ****************************************************************************************************************************************************************************************************************************************************************
			// GENERADAS CUMPLIDAS REPROGRAMADAS ******************************************************************************************************************************************************************************************************************************
			$mQuery2 = "SELECT 
								COUNT( a.num_consec ) AS cum_repro
						  FROM 
						  		tab_citasx_entreg a 
					INNER JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
						 WHERE
						 		DATE(a.fec_creaci) = '".$date."'
						 	AND a.ind_cumple = 1 
						 	AND b.ind_reprog = 1
						 	AND b.ind_cumple = 1
							$mFiltroCli
							$mFiltroNeg
						";
			$mConsult = new Consulta($mQuery2, self::$cConexion );		
			$cumplidas_reprogramadas = $mConsult -> ret_matrix('a');
			$cumplidas_reprogramadas = $cumplidas_reprogramadas[0];
			$mReturn[$date]['cum_repro'] = $cumplidas_reprogramadas['cum_repro'];
			$mReturn[$date]['cum_repro_per'] = $cumplidas_reprogramadas['cum_repro'] == 0 ? $cumplidas_reprogramadas['cum_repro'] :  ( ( $cumplidas_reprogramadas['cum_repro'] * 100 ) / $mReturn[$date]['total_generadas']  );
			$mReturn[$date]['cum_repro_per'] = number_format($mReturn[$date]['cum_repro_per'], 2);


			// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
			// GENERADAS CUMPLIDAS SOLICITADAS --------------------------------------------------------------------------------------------------------------------------------
			$mQuery3 = "SELECT 
								COUNT( a.num_consec ) AS cum_solic
						  FROM 
						  		tab_citasx_entreg a 
					LEFT  JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
						 WHERE
						 		DATE(a.fec_creaci) = '".$date."'
						 	AND a.ind_cumple = 1 
						 	AND b.ind_cumple IS NULL 
							$mFiltroCli
							$mFiltroNeg						 	
						";
			$mConsult = new Consulta($mQuery3, self::$cConexion );		
			$cum_solic = $mConsult -> ret_matrix('a');
			$cum_solic = $cum_solic[0];
			$mReturn[$date]['cum_solic'] = $cum_solic['cum_solic'];
			$mReturn[$date]['cum_solic_per'] = $cum_solic['cum_solic'] == 0 ? $cum_solic['cum_solic'] :  ( ( $cum_solic['cum_solic'] * 100 ) / $mReturn[$date]['total_generadas']  );
			$mReturn[$date]['cum_solic_per'] = number_format($mReturn[$date]['cum_solic_per'], 2);


			// ****************************************************************************************************************************************************************************************************************************************************************
			// GENERADAS NO CUMPLIDAS REPROGRAMADAS ***************************************************************************************************************************************************************************************************************************
			$mQuery2 = "SELECT 
								COUNT( a.num_consec ) AS no_cum_repro
						  FROM 
						  		tab_citasx_entreg a 
					INNER JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
						 WHERE
						 		DATE(a.fec_creaci) = '".$date."'
						 	AND a.ind_cumple = 0 
						 	AND b.ind_reprog = 1
						 	AND b.ind_cumple = 0
							$mFiltroCli
							$mFiltroNeg						 	
						";
			$mConsult = new Consulta($mQuery2, self::$cConexion );		
			$no_cum_repro = $mConsult -> ret_matrix('a');
			$no_cum_repro = $no_cum_repro[0];
			$mReturn[$date]['no_cum_repro'] = $no_cum_repro['no_cum_repro'];
			$mReturn[$date]['no_cum_repro_per'] = $no_cum_repro['no_cum_repro'] == 0 ? $no_cum_repro['no_cum_repro'] :  ( ( $no_cum_repro['no_cum_repro'] * 100 ) / $mReturn[$date]['total_generadas']  );
			$mReturn[$date]['no_cum_repro_per'] = number_format($mReturn[$date]['no_cum_repro_per'], 2);


			// -------------------------------------------------------------------------------------------------------------------------------------------------------------------
			// GENERADAS NO CUMPLIDAS SOLICITADAS --------------------------------------------------------------------------------------------------------------------------------
			$mQuery3 = "SELECT 
								COUNT( a.num_consec ) AS no_cum_solic
						  FROM 
						  		tab_citasx_entreg a 
					INNER JOIN  tab_citasx_gestio b ON a.num_consec = b.num_consec
						 WHERE
						 		DATE(a.fec_creaci) = '".$date."'
						 	AND a.ind_cumple = 0 
						 	AND b.ind_cumple = 0
						 	AND b.ind_reprog = 0
							$mFiltroCli
							$mFiltroNeg						 	
						";
			$mConsult = new Consulta($mQuery3, self::$cConexion );		
			$no_cum_solic = $mConsult -> ret_matrix('a');
			$no_cum_solic = $no_cum_solic[0];
			$mReturn[$date]['no_cum_solic'] = $no_cum_solic['no_cum_solic'];
			$mReturn[$date]['no_cum_solic_per'] = $no_cum_solic['no_cum_solic'] == 0 ? $no_cum_solic['no_cum_solic'] :  ( ( $no_cum_solic['no_cum_solic'] * 100 ) / $mReturn[$date]['total_generadas']  );
			$mReturn[$date]['no_cum_solic_per'] = number_format($mReturn[$date]['no_cum_solic_per'], 2);
 
			$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
		}




		// echo "<pre>"; print_r($mReturn); echo "</pre>";
		return $mReturn;
	}


	/*! \fn: expTabExcelReport
	 *  \brief: Exporta el informe
	 *  \author: Ing. Nelson Liberato
	 *  \date: 19/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function expTabExcelReport(  )
	{
		 


		// $mPlotFolder = '/temp/'.md5(date("ymd_His"));
		// $mPlot = fopen( $mPlotFolder, 'a+');
		// fwrite($mPlot, $_REQUEST['imageBinPlot']);
		// fclose($mPlot); 
		$data = $_REQUEST['imageBinPlot'];
		$temp = "logs/".$_SESSION['datos_usuario']['cod_usuari'].'.png';
		
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);

		file_put_contents($temp, $data);

 

		//$mURL = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
		$mURL = URL_APLICA.$temp;

		$filename = "indicador_citas_entrega_".date("Y_m_d").".xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		
		ob_end_clean() ;
		echo str_replace(['aca_va_la_imagen', 'display:none'], ['<img src="'.$mURL.'" />', ''], $_SESSION['indicadorCitaEntrega']);
		ob_end_flush();
		ob_clean();
		unlink($temp);
		die();

	}	


 
	/*! \fn: infDetail
	 *  \brief: Pinta el informe detallado
	 *  \author: Ing. Nelson Liberato
	 *  \date: 20/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:     
	 *  \return: 
	 */
	private function infDetail()
	{
		 
		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");
			$mHtml->SetBody('<center>
								<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelReportEsfera( \''.$mIdxTab.'\' );" style="cursor:pointer">
							 </center><br>');

			$mHtml->SetBody("<label style='color: black'>Se encontraron $mSize registros.</label><br>");

			$mHtml->SetBody('<table id="'.$mIdxTab.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');

				foreach ($mTittle as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
				$mHtml->CloseRow('tr');

				$i=1;
				foreach ($mData as $row) {
					if( $i % 2 == 1 ){
						$mClass = 'cellInfo1 izquierda';
					}else{
						$mClass = 'cellInfo2 izquierda';
					}

					$row['num_despac'] = '<a style="color:#000000;" href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&tie_ultnov=0&opcion=1">'.$row['num_despac'].'</a>';

					foreach ($mTittle as $key => $tit) {
						$mHtml->Label( utf8_encode($row[$key]), array("class"=>$mClass) );
					}
					$mHtml->CloseRow('tr');
					$i++;
				}

			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfGestionCitasDeEntrega();
else
	$_INFORM = new InfGestionCitasDeEntrega( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>

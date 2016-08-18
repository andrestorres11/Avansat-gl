<?php 
/*! \file: inf_gerenc_callce.php
 *  \brief: Archivo para generar el informe gerencial Callcenter
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 18/08/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');

/*! \class: infCallCe
 *  \brief: Clase para generar el informe gerencial Callcenter
 */
class infCallCe
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$cCallCe,
					$cNull = array( array('', '-----') );

	function __construct($co, $us, $ca)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_gerenc_callce.php' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		self::$cCallCe = new CallCe( $co, $us, $ca );
		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		self::$cCallCe -> style();
		
		IncludeJS( 'jquery.js' );
		IncludeJS( 'es.js' );
		IncludeJS( 'mask.js' );
		IncludeJS( 'inf_gerenc_callce.js' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

		switch($_REQUEST[Option])
		{
			case 'exportExcel':
				self::exportExcel();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: formulario de filtros
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function formulario()
	{
		$mEstado = self::$cCallCe -> getEstadoLlamada();
		$mTransp = self::$cCallCe -> getTransp();
		$mTipDes = self::$cCallCe -> getTipoDespac();
		$mTipTra = self::$cCallCe -> getTipoTransp();
		$mHtml1 = '';
		$mHtml2 = '';
		$mScript = '';

		if( sizeof($mTransp) != 1 ){
			$mTransp = array();
			$mDisabl = false;
		}else
			$mDisabl = true;

		foreach ($mTipDes as $row)
		{
			$mID = str_replace(' ', '_', $row[1]);
			$mHtml1 .= '<li class="ui-state-default ui-corner-top"><a id="'.$mID.'ID" href="#tabs-'.$row[0].'">'.$row[1].'</a></li>'; #Pestaña Tipo de despacho
			$mHtml2 .= '<div id="tabs-'.$row[0].'"></div>'; #DIV Tipo de despacho

			$mScript .= ' $("#'.$mID.'ID").click(function(){
							report( "'.$row[0].'", "tabs-'.$row[0].'" );
						  }); ';
		}

		#<div FILTROS>
			$mHtml = '</table><div id="accordionID">';
				$mHtml .= '<h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h1>';
				$mHtml .= '<div>';
					$mHtml .= '<div  class="Style2DIV">';
					$mHtml .= '<table width="100%" cellspacing="0" cellpadding="0">';

						$mHtml .= '<tr><th class="CellHead" colspan="8" style="text-align:left">Seleccione Par&aacute;metros de B&uacute;squeda</th></tr>';

						$mHtml .= '<tr>';
						$mHtml .= self::$cCallCe -> texto( 'Transportadora:', 'nom_transp', $mTransp[0]['nom_tercer'], 'cellInfo1', 1, 50, 3, $mDisabl );
						$mHtml .= '</tr>';

						$mHtml .= '<tr>';
						$mHtml .= self::$cCallCe -> texto( 'No. Despacho:', 'num_despac', '', 'cellInfo1' );
						$mHtml .= self::$cCallCe -> texto( 'No. Manifiesto:', 'num_manifi', '', 'cellInfo1' );
						$mHtml .= self::$cCallCe -> texto( 'No. Viaje:', 'num_viajex', '', 'cellInfo1' );
						$mHtml .= '</tr>';

						$mHtml .= '<tr>';
						$mHtml .= self::$cCallCe -> texto( 'Placa:', 'num_placax', '', 'cellInfo1', 0, 6 );
						$mHtml .= self::$cCallCe -> texto( 'Fecha Inicial:', 'fec_inicia', '', 'cellInfo1', 0, 10 );
						$mHtml .= self::$cCallCe -> texto( 'Fecha Final:', 'fec_finalx', '', 'cellInfo1', 0, 10 );
						$mHtml .= '</tr>';

						$mHtml .= '<tr>';
						$mHtml .= self::$cCallCe -> lista( 'Tipo de Despacho:', 'cod_tipdes', array_merge( self::$cNull, $mTipDes), 'cellInfo1' );
						$mHtml .= self::$cCallCe -> lista( 'Tipo de Transporte:', 'cod_tiptra', array_merge( self::$cNull, $mTipTra), 'cellInfo1' );
						$mHtml .= self::$cCallCe -> lista( 'Estado Llamada:', 'nom_estado', array_merge( self::$cNull, $mEstado), 'cellInfo1' );
						$mHtml .= '</tr>';

						$mHtml .= '<input id="windowID" type="hidden" value="central" name="window">';
						$mHtml .= '<input id="standaID" type="hidden" value="'.DIR_APLICA_CENTRAL.'" name="standa">';
						$mHtml .= '<input id="cod_transpID" type="hidden" value="'.$mTransp[0]['cod_tercer'].'" name="cod_transp">';
						$mHtml .= '<input id="ind_filactID" type="hidden" value="" name="ind_filact">';
						$mHtml .= '<input id="cod_servicID" type="hidden" value="'.$_REQUEST[cod_servic].'" name="cod_servic">';

					$mHtml .= '</table>';
					$mHtml .= '</div>';
				$mHtml .= '</div>';
			$mHtml .= '</div>';
		#</div FILTROS>

		#<Informe>
			$mHtml .= '<div id="tabs">';
				$mHtml .= '<ul>';
					$mHtml .= '<li><a id="liGenera" href="#tabs-0">GENERAL</a></li>';
					$mHtml .= $mHtml1;
				$mHtml .= '</ul>';

				$mHtml .= '<div id="tabs-0"></div>'; #DIV General
				$mHtml .= $mHtml2;
			$mHtml .= '</div>';
		#</Informe>

		echo $mHtml;
		echo '<script> '.$mScript.' </script>';
	}

	/*! \fn: exportExcel
	 *  \brief: Esporta contenido de la tabla del informe en un archivo Excel
	 *  \author: Ing. Fabian Salinas
	 *	\date: 21/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function exportExcel()
	{
		$archivo = "informe_CallCenter".date( "Y_m_d_H_i" ).".xls";
		header('Content-type: application/vnd.ms-excel');
		header('Expires: 0');
		header('Content-Disposition: attachment; filename="'.$archivo.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_clean();
		echo $HTML = strip_tags($_SESSION[excelCallce], '<table><tr><th><td>');
	}
}

$_INFORM = new infCallCe( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
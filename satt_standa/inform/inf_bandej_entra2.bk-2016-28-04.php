<?php 
/*! \file: inf_bandej_entra2.php
 *  \brief: Archivo principal para la bandeja de entrada
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 16/06/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');

/*! \class: infBandeja
 *  \brief: Clase principal para la bandeja de entrada
 */
class infBandeja
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$cDespac,
					$cNull = array( array('', '-----') );

	function __construct($co, $us, $ca)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		infBandeja::$cDespac = new Despac( $co, $us, $ca );
		infBandeja::$cConexion = $co;
		infBandeja::$cUsuario = $us;
		infBandeja::$cCodAplica = $ca;
		infBandeja::$cDespac -> style();
		
		IncludeJS( 'jquery.js' );
		IncludeJS( 'inf_bandej_entra3.js' );
		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

		switch($_REQUEST[opcion])
		{
			default:
				infBandeja::bandeja();
				break;
		}
	}

	/*! \fn: bandeja
	 *  \brief: Funcion principal para cargar la bandeja
	 *  \author: Ing. Fabian Salinas
	 *	\date: 16/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function bandeja()
	{
		$mTypeUser = infBandeja::$cDespac -> typeUser();
		$mTipoDespac = infBandeja::$cDespac -> getTipoDespac();
		$mArrayTransp = infBandeja::$cDespac -> getTransp();
		$mArrayUserAs = infBandeja::$cDespac -> getUserAsig();
		$mUsrSinAsig = array( array('SIN', 'NO REGISTRA') );
		$mView = infBandeja::$cDespac -> getView('jso_bandej');

		#<div FILTROS GENERALES>
		if( $mView->fil_genera->ind_visibl == 1 )
		{
			$mHtml1 = '<h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS GENERALES</b></h1>';
			$mHtml1 .= '<div>';
				$mHtml1 .= '<div  class="Style2DIV">';
				$mHtml1 .= '<table width="100%" cellspacing="0" cellpadding="0">';

					if( $mView->fil_genera->sub->tip_despac == 1 )
					{
						$mHtml1 .= '<tr><th class="CellHead" colspan="8" style="text-align:left">Tipo de Despacho</th></tr>';
						$i=0;
						$j=1;
						foreach ($mTipoDespac as $row)
						{
							$mHtml1 .= $i==0 || ( $i % 4 == 0) ? '<tr>' : '';
							$mHtml1 .= '	<td class="cellInfo1" width="12.5%" align="right">'.$row[1].'</td>';
							$mHtml1 .= '	<td class="cellInfo1" width="12.5%"><input type="checkbox" value="1" name="tip_despac'.$row[0].'" '.($_REQUEST["tip_despac".$row[0]] == $row[0] ? 'checked' : '').' ></td>';
							$mHtml1 .= ($j == sizeof($mTipoDespac)) || ( $j % 4 == 0) ? '</tr>' : '';
							$i++;
							$j++;
						}
					}

					if( $mView->fil_genera->sub->tip_servic == 1 )
					{
						$mHtml1 .= '<tr><th class="CellHead" colspan="8" style="text-align:left">Tipo de Servicio</th></tr>';
						$mHtml1 .= '<tr>';
						$mHtml1 .= '	<td class="cellInfo1" align="right">EAL</td>';
						$mHtml1 .= '	<td class="cellInfo1"><input type="checkbox" value="1" name="tip_servic1" '.($_REQUEST[tip_servic1] == 1 ? 'checked' : '').' ></td>';
						$mHtml1 .= '	<td class="cellInfo1" align="right">MA</td>';
						$mHtml1 .= '	<td class="cellInfo1"><input type="checkbox" value="1" name="tip_servic2" '.($_REQUEST[tip_servic2] == 1 ? 'checked' : '').' ></td>';
						$mHtml1 .= '	<td class="cellInfo1" align="right">EAL/MA</td>';
						$mHtml1 .= '	<td class="cellInfo1"><input type="checkbox" value="1" name="tip_servic3" '.($_REQUEST[tip_servic3] == 1 ? 'checked' : '').' ></td>';
						$mHtml1 .= '	<td class="cellInfo1" colspan="2">&nbsp;</td>';
						$mHtml1 .= '</tr>';
					}

					if( $mView->fil_genera->sub->otr_filtro == 1 )
					{
						$mHtml1 .= '</table>';
						$mHtml1 .= '<table width="100%" cellspacing="0" cellpadding="0">';

						$mHtml1 .= '<tr><th class="CellHead" colspan="4" style="text-align:left">Otros Filtros</th></tr>';
						$mHtml1 .= '<tr>';
						$mHtml1 .= '	<td class="cellInfo1" width="25%" align="right">Limpios</td>';
						$mHtml1 .= '	<td class="cellInfo1" width="25%"><input type="radio" value="1" name="ind_limpio" id="ind_limpioID" '.($_REQUEST[ind_limpio] === '1' ? 'checked' : '').' ></td>';
						$mHtml1 .= '	<td class="cellInfo1" width="25%" align="right">No Limpios</td>';
						$mHtml1 .= '	<td class="cellInfo1" width="25%"><input type="radio" value="0" name="ind_limpio" id="ind_limpioID" '.($_REQUEST[ind_limpio] === '0' ? 'checked' : '').' ></td>';
						$mHtml1 .= '</tr>';

						$mHtml1 .= '<tr>';
						$mHtml1 .= infBandeja::$cDespac -> lista( 'Transportadora:', 'cod_transp', array_merge( infBandeja::$cNull, $mArrayTransp), 'cellInfo1' );
						if( $mTypeUser[tip_perfil] != 'CONTROL' && $mTypeUser[tip_perfil] != 'EAL' )
							$mHtml1 .= infBandeja::$cDespac -> lista( 'Usuarios Asignados:', 'cod_usuari', array_merge( infBandeja::$cNull, $mUsrSinAsig, $mArrayUserAs), 'cellInfo1' );
						$mHtml1 .= '</tr>';
					}

				$mHtml1 .= '</table>';
				$mHtml1 .= '</div>';
			$mHtml1 .= '</div>';
		}else
			$mHtml1 = '';
		#</div FILTROS GENERALES>

		#<div FILTROS ESPECIFICOS>
		if( $mView->fil_especi->ind_visibl == 1 )
		{
			$mHtml2  = '<h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS ESPEC&Iacute;FICOS</b></h1>';
			$mHtml2 .= '<div>';
				$mHtml2 .= '<div  class="Style2DIV">';
				$mHtml2 .= '<table width="100%" cellspacing="0" cellpadding="0">';

				$mHtml2 .= '<tr>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Despacho:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="15" value="" size="15" onkeypress="return justNumbers( event );" id="num_despacID" name="num_despac" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">Placa:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="6" value="" size="6" id="num_placaxID" name="num_placax" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">Celular Conductor:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="10" value="" size="10" onkeypress="return justNumbers( event );" id="num_celconID" name="num_celcon" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '</tr>';

				$mHtml2 .= '<tr>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Viaje:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="15" value="" size="15" id="num_viajexID" name="num_viajex" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Solicitud:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="15" value="" size="15" id="num_soliciID" name="num_solici" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Pedido:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="15" value="" size="15" id="num_pedidoID" name="num_pedido" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '</tr>';

				$mHtml2 .= '<tr>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Factura:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="15" value="" size="15" id="num_facturID" name="num_factur" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">En Tr&aacute;nsito:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="checkbox" value="1" id="ind_entranID" name="ind_entran" checked >';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">Finalizados:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="checkbox" value="1" id="ind_fintraID" name="ind_fintra" >';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '</tr>';

				$mHtml2 .= '</table>';
				$mHtml2 .= '</div>';
			$mHtml2 .= '</div>';
		}else
			$mHtml2 = '';
		#</div FILTROS ESPECIFICOS>
		
		#<Formulario>
			$mHtml = '</table><div id="accordionID">';

			if( $mTypeUser[tip_perfil] == 'OTRO' )
				$mHtml .= $mHtml1.$mHtml2;
			else
				$mHtml .= $mHtml2.$mHtml1;

			$mHtml .= '<input id="windowID" type="hidden" value="central" name="window">';
			$mHtml .= '<input id="standaID" type="hidden" value="'.DIR_APLICA_CENTRAL.'" name="standa">';
			$mHtml .= '<input id="ind_filactID" type="hidden" value="" name="ind_filact">';
			$mHtml .= '<input id="sel_transpID" type="hidden" value="'.$_REQUEST[cod_transp].'" name="sel_transp">';
			$mHtml .= '<input id="sel_usuariID" type="hidden" value="'.$_REQUEST[cod_usuari].'" name="sel_usuari">';

			$mHtml .= '</div>';

			echo $mHtml;
		#</Formulario>

		#<Bandeja>
		if( $mView->sec_inform->ind_visibl == 1 )
		{
			$mBand  = '</table><div id="tabs">';
				$mBand .= '<ul>';
					$mBand .= $mView->sec_inform->sub->pes_genera == 1 ? '<li><a id="liGenera" href="#tabs-1">GENERAL</a></li>' : '';
					$mBand .= $mView->sec_inform->sub->pes_cargax == 1 ? '<li class="ui-state-default ui-corner-top"><a id="liCargue" href="#tabs-2">CARGUE</a></li>' : '';
					$mBand .= $mView->sec_inform->sub->pes_transi == 1 ? '<li class="ui-state-default ui-corner-top"><a id="liTransi" href="#tabs-3">TRANSITO</a></li>' : '';
					$mBand .= $mView->sec_inform->sub->pes_descar == 1 ? '<li class="ui-state-default ui-corner-top"><a id="liDescar" href="#tabs-4">DESCARGUE</a></li>' : '';
				$mBand .= '</ul>';

				$mBand .= $mView->sec_inform->sub->pes_genera == 1 ? '<div id="tabs-1"></div>' : ''; #DIV General
				$mBand .= $mView->sec_inform->sub->pes_cargax == 1 ? '<div id="tabs-2"></div>' : ''; #DIV Etapa Cargue
				$mBand .= $mView->sec_inform->sub->pes_transi == 1 ? '<div id="tabs-3"></div>' : ''; #DIV Etapa Transito
				$mBand .= $mView->sec_inform->sub->pes_descar == 1 ? '<div id="tabs-4"></div>' : ''; #DIV Etapa Descargue

			$mBand .= '</div>';

			echo $mBand;
		}
		#</Bandeja>
	}
}

$_INFORM = new infBandeja( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
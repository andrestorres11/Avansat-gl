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
		self::$cDespac = new Despac( $co, $us, $ca );
		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;		
		
		IncludeJS( 'jquery.js' );
		IncludeJS( 'inf_bandej_entra3.js' );
		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'time.js', '../'.DIR_APLICA_CENTRAL.'/js/' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

		switch($_REQUEST[opcion])
		{
			case "10":
                $this->exportExcel();
			default:
				self::bandeja();
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
		$mTypeUser = self::$cDespac -> typeUser();
		$mTipoDespac = self::$cDespac -> getTipoDespac();
		$mArrayTransp = self::$cDespac -> getTransp();
		$mArrayUserAs = self::$cDespac -> getUserAsig();
		$mUsrSinAsig = array( array('SIN', 'SIN ASIGNAR') );
		$mView = self::$cDespac -> getView('jso_bandej');

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
						$mHtml1 .= '	<td class="cellInfo1"><input type="checkbox" value="1" name="tip_servic1" '.($_REQUEST['tip_servic1'] == 1 ? 'checked' : '').' ></td>';
						$mHtml1 .= '	<td class="cellInfo1" align="right">MA</td>';
						$mHtml1 .= '	<td class="cellInfo1"><input type="checkbox" value="1" name="tip_servic2" '.($_REQUEST['tip_servic2'] == 1 ? 'checked' : '').' ></td>';
						$mHtml1 .= '	<td class="cellInfo1" align="right">EAL/MA</td>';
						$mHtml1 .= '	<td class="cellInfo1"><input type="checkbox" value="1" name="tip_servic3" '.($_REQUEST['tip_servic3'] == 1 ? 'checked' : '').' ></td>';
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
						$mHtml1 .= self::$cDespac -> lista( 'Transportadora:', 'cod_transp', array_merge( self::$cNull, $mArrayTransp), 'cellInfo1' );
						if( $mTypeUser['tip_perfil'] != 'CONTROL' && $mTypeUser['tip_perfil'] != 'EAL' )
							$mHtml1 .= self::$cDespac -> lista( 'Usuarios Asignados:', 'cod_usuari', array_merge( self::$cNull, $mUsrSinAsig, $mArrayUserAs), 'cellInfo1' );
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
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Transporte:</td>';
				$mHtml2 .= '	<td class="cellInfo1">';
				$mHtml2 .= '		<input type="text" maxlength="15" value="" size="15" id="num_pedidoID" name="num_pedido" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'">';
				$mHtml2 .= '	</td>';
				$mHtml2 .= '</tr>';

				$mHtml2 .= '<tr>';
				$mHtml2 .= '	<td class="cellInfo1" align="right">No. Remisi&oacute;n:</td>';
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

		#<FormularioPrecarge>;
		echo '
            <script>
              jQuery(function($) { 
                $( "#hor_inicio,#hor_finxxx" ).timepicker();      
                })
             </script>';
		$mHtml3  = '<div>';
			$mHtml3 .= '<div>';
				$mHtml3 .= '<div  class="Style4DIV">';
				$mHtml3 .= '<table width="100%" cellspacing="0" cellpadding="0">';
				
				$mHtml3 .= '<tr><th class="CellHead" colspan="8" style="text-align:left">Filtros Especificos > Precargue</th></tr>';
				
				$mHtml3 .= '<tr>';
				$mHtml3 .=  self::$cDespac ->lista( 'Punto de Cargue:', 'pun_cargue', self::$cNull, 'cellInfo1' );
				$mHtml3 .= '	<td class="cellInfo1" align="right">Hora desde</td>';
				$mHtml3 .= '	<td class="cellInfo1">';
				$mHtml3 .= '		<input validate="date" obl="1" maxlength="10" minlength="10" type="text" name="hor_inicio" id="hor_inicio">';
				$mHtml3 .= '	</td>';
				$mHtml3 .= '	<td class="cellInfo1" align="right">hasta</td>';
				$mHtml3 .= '	<td class="cellInfo1">';
				$mHtml3 .= '		<input validate="date" obl="1" maxlength="10" minlength="10" type="text" name="hor_fin" id="hor_finxxx">';
				$mHtml3 .= '	</td>';
				$mHtml3 .=  self::$cDespac ->lista( 'Producto:', 'tip_produc', array_merge( self::$cNull, self::$cDespac ->getLisProductos()), 'cellInfo1' );
				$mHtml3 .= '</tr>';

				$mHtml3 .= '<tr>';
				$mHtml3 .= '<td class="cellInfo1" align="right" colspan="4"><input type="button" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" value="GENERAR" id="generarprc"></td>';
				$mHtml3 .= '<td class="cellInfo1" align="left" colspan="4"><input type="button" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" value="EXCEL" id="excelprcGeneral" onclick="exportExcel()"></td>';
				$mHtml3 .= '</tr>';

				$mHtml3 .= '</table>';
				$mHtml3 .= '</div>';
			$mHtml3 .= '</div><br>';
			$mHtml3 .= '<div id="tabs-6-a"></div>';
		$mHtml3 .= '</div>';

		#</FormularioPrecarge>


		#Estilos bage
		self::styleBandeja();
		#si el contador del badge esta vacio se pinta sin la clase
		#<Bandeja>
		if( $mView->sec_inform->ind_visibl == 1 )
		{
			$mBand  = '</table><div id="tabs">';
				$mBand .= '<ul>';
					if( $mView->sec_inform->sub->pes_genera == 1 )
						$mBand .= '<li><a id="liGenera" href="#tabs-1">GENERAL</a></li>';
					if( $mView->sec_inform->sub->pes_prcarg == 1 )
						$mbadge = self::$cDespac ->getConteoNem('1', $mArrayTransp);
						$mBand .= '<li class="ui-state-default ui-corner-top"><a id="liPreCar" href="#tabs-6">PRECARGUE '.($mbadge>0?'<span class="badge">'.$mbadge.'</span>':'<span></span>').'</a></li>';
					if( $mView->sec_inform->sub->pes_cargax == 1 )
						$mbadge = self::$cDespac ->getConteoNem('2', $mArrayTransp);
						$mBand .= '<li class="ui-state-default ui-corner-top"><a id="liCargue" href="#tabs-2">CARGUE '.($mbadge>0?'<span class="badge">'.$mbadge.'</span>':'<span></span>').'</a></li>';
					if( $mView->sec_inform->sub->pes_transi == 1 )
						$mbadge = self::$cDespac ->getConteoNem('3', $mArrayTransp);
						$mBand .= '<li class="ui-state-default ui-corner-top"><a id="liTransi" href="#tabs-3">TRANSITO '.($mbadge>0?'<span class="badge">'.$mbadge.'</span>':'<span></span>').'</a></li>';
					if( $mView->sec_inform->sub->pes_descar == 1 )
						$mbadge = self::$cDespac ->getConteoNem('5', $mArrayTransp);
						$mBand .= '<li class="ui-state-default ui-corner-top"><a id="liDescar" href="#tabs-4">DESCARGUE '.($mbadge>0?'<span class="badge">'.$mbadge.'</span>':'<span></span>').'</a></li>';
					if( $mView->sec_inform->sub->pes_pernoc == 1 )
						$mBand .= '<li class="ui-state-default ui-corner-top"><a id="liPernoc" href="#tabs-5">C. PERNOTACION</a></li>';
				$mBand .= '</ul>';

				$mBand .= $mView->sec_inform->sub->pes_genera == 1 ? '<div id="tabs-1"></div>' : ''; #DIV General
				$mBand .= $mView->sec_inform->sub->pes_cargax == 1 ? '<div id="tabs-2"></div>' : ''; #DIV Etapa Cargue
				$mBand .= $mView->sec_inform->sub->pes_transi == 1 ? '<div id="tabs-3"></div>' : ''; #DIV Etapa Transito
				$mBand .= $mView->sec_inform->sub->pes_descar == 1 ? '<div id="tabs-4"></div>' : ''; #DIV Etapa Descargue
				$mBand .= $mView->sec_inform->sub->pes_pernoc == 1 ? '<div id="tabs-5"></div>' : ''; #DIV c. Pernotacion
				$mBand .= $mView->sec_inform->sub->pes_prcarg == 1 ? '<div id="tabs-6">'.$mHtml3.'</div>' : ''; #DIV Etapa PreCargue

			$mBand .= '</div>';

			echo $mBand;
		}
		#</Bandeja>
	}

	/*! \fn: exportExcel
	 *  \brief: Funcion para exportar a excel
	 *  \author: Edward Serrano
	 *	\date: 15/03/2017
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function exportExcel()
    {
    	 $filename = "Precargue_General_" . date('Ymd') . ".xls";

  		header("Content-Disposition: attachment; filename=\"$filename\"");
  		header("Content-Type: application/vnd.ms-excel");
        ob_clean();
       	echo $_SESSION['precargue']['general'];
       	die;
    }

    /*! \fn: styleBandeja
	 *  \brief: Estilos para bandeje de entrada
	 *  \author: Edward Serrano
	 *	\date: 15/05/2017
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	function styleBandeja()
    {
    	 echo "<style>
    	 			.badge {
						  display: inline-block;
						  min-width: 8px;
						  padding: 2px 4px;
						  font-size: 10px;
						  font-weight: bold;
						  line-height: 1;
						  color: #A01C0F;
						  text-align: center;
						  white-space: nowrap;
						  vertical-align: middle;
						  background-color: #F3F70A;
						  border-radius: 20px;
						}

    	 	   </style>";
    }
}

$_INFORM = new infBandeja( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
<?php
/*! \file: inf_cumpli_segcar.php
 *  \brief: Verificar el cumpliento del seguimiento en Cargue
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 22/01/2016
 *  \bug: 
 *  \warning: 
 */

/*! \class: CumpliSeguimCargue
 *  \brief: Clase para verificar el cumpliento del seguimiento en Cargue
 */
class CumpliSeguimCargue
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cFunction;

	function __construct($co = null, $us = null, $ca = null)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php' );

			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
			self::$cFunction = new Despac(self::$cConexion, $us, $ca);
		}
		

		switch($_REQUEST['Option']){
			case 'validateCumpliSeguimCargue':
				self::validateCumpliSeguimCargue();
				break;

			case 'confirmCumpliSeguimCargue':
				self::confirmCumpliSeguimCargue();
				break;

			case 'saveObsSeguimCargue':
				self::saveObsSeguimCargue();
				break;

			case 'generateReportG':
				self::generateReportG();
				break;

			case 'showDetail':
				self::showDetail();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para los filtros del informe
	 *  \author: Ing. Fabian Salinas
	 *  \date:  26/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formulario(){
        $tipoDespacho = self::$cFunction->getTipoDespac();
        $transp = self::$cFunction->getTransp();

        $total = count($transp);
        if( $total == 1 ){
          $mCodTransp = $transp[0][0];
          $mNomTransp = $transp[0][1];
        }
        
        ?>
        </table>

        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/time.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/validator.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/highcharts.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/inf_cumpli_segcar.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery.blockUI.js"></script>
        <script type="text/javascript" language="JavaScript" src="../<?= DIR_APLICA_CENTRAL ?>/js/functions.js"></script>

        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/bootstrap.css' type='text/css'>
        <link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'>

        <div id="acordeonID" class="col-md-12 accordion ancho">
          <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h1>
          <div id="contenido">
            <div  class="Style2DIV">
              <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <th class="CellHead" colspan="6" style="text-align:center" id="notify"><b>Ingrese los  Par&aacute;metros de consulta</b></th>
                </tr>
                <tr class="Style2DIV">
                  <td class="contenido" colspan="6" style="text-align:center">
                  <?php if($total > 1){ ?>
                    <div class="col-md-6" style="text-align:right">Transportadora<font style="color:red">*</font>: </div>
                    <div class="col-md-6" style="text-align:left"><input type="text" id="nom_transpID" name="nom_transp" style="width:50%"></div>
                    <?php } ?>
                    <div class="col-md-2 derecha">No. Despacho:</div>
                    <div class="col-md-2"><input id='num_despacID' name="num_despac" class="ancho" type="text"></div>
                    <div class="col-md-2 derecha">No. Manifiesto</div>
                    <div class="col-md-2"><input id="num_manifiID" name="num_manifi" class="ancho" type="text"></div>
                    <div class="col-md-2 derecha">No. Viaje</div>
                    <div class="col-md-2"><input id="num_viajexID" name="num_viajex" class="ancho" type="text"></div>
                    <div class="col-md-2 derecha">Tipo de Despacho</div>
                    <div class="col-md-2">
                        <select class="ancho" id='tip_despacID' name='tip_despac'>
                            <option value="">Todos</option>
                            <?php foreach ($tipoDespacho as $key => $value) {
                                ?>
                                <option value="<?= $value[0] ?>"><?= $value[1] ?></option>
                                <?php
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-2 derecha">Fecha Inicial<font style="color:red">*</font>: </div>
                    <div class="col-md-2"><input class="ancho" type="text" maxlength="10" size="10" id="fec_iniciaID" name="fec_inicia" readonly="" name="fec_inicia" ></div> 
                    <div class="col-md-2 derecha">Fecha Final<font style="color:red">*</font>: </div>
                    <div class="col-md-2"><input class="ancho" type="text" maxlength="10" size="10" id="fec_finaliID" name="fec_finali" readonly="" name="fec_finali" ></div>
                    
                    <form id="formSeguimCargueID" name="formSeguimCargue" method="post" action="../<?= DIR_APLICA_CENTRAL ?>/lib/exportExcel.php">                   
                        <input type="hidden" name="standa" id="standaID" value="<?= DIR_APLICA_CENTRAL ?>"> 
                        <input type="hidden" name="window" id="windowID" value="central"> 
                        <input type="hidden" name="cod_transp" id="cod_transpID" value="<?= $mCodTransp ?>"> 
                        <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST['cod_servic'] ?>"> 
                        <input type="hidden" name="OptionExcel" id="OptionExcelID" value="_REQUEST"> 
                        <input type="hidden" name="exportExcel" id="exportExcelID" value=""> 
                        <input type="hidden" name="nameFile" id="nameFileID" value="Inf_Cumplimiento_Seguimiento_Cargue"> 
                        <input type="hidden" name="option" id="optionID" value="">                    
                    </form>

                  </td>
                </tr>
            </table>
            </div>
          </div>
        </div>
        <div class="col-md-12 tabs ancho" id="tabs">
           <ul>
               <li><a id="liGenera" href="#generaID" style="cursor:pointer" onclick="report('g', 'generaID')">INFORME</a></li>                   
            </ul>
            <div class="col-md-12" id="generaID" ></div>
        </div>
        <?php
    }

	/*! \fn: generateReportG
	 *  \brief: Pinta el resultado del informe General
	 *  \author: Ing. Fabian Salinas
	 *  \date:  26/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function generateReportG(){
		$mData = self::getData();

		if( $mData ){
			if( !$_REQUEST['fec_inicia'] && !$_REQUEST['fec_finali'] ){
				foreach ($mData as $row) {
					$mFecInicia = $row['fec_despac'];
					$mFecFinalx = $row['fec_despac'];
					break;
				}
			}else{
				$mFecInicia = $_REQUEST['fec_inicia'];
				$mFecFinalx = $_REQUEST['fec_finali'];
			}

			?>
			<div id="contenido2">
				<div class="Style2DIV" id="divTableID" style="background-color: #EBF8E2 !important;">
					<div class="col-md-3" ></div>
					<div class="col-md-6" id="container2"></div>
					<div class="col-md-3" ></div>
					<div class="col-md-12">&nbsp;</div>

					<table width="100%" cellspacing="1" cellpadding="0">
						<tbody>
						<tr>
							<th style="text-align:center" colspan="7" class="CellHead"><b>INDICADOR LLAMADAS A CITA DE CARGUE COMPRENDIDO ENTRE <?= $_REQUEST['fec_inicia'] ?> Y <?= $_REQUEST['fec_finali'] ?></b></th>
						</tr>

						<tr class="Style2DIV">
							<th style="text-align:center" class="CellHead"> Despachos Generados </th>
							<th style="text-align:center" class="CellHead"  id="name_segcar"> Cumplimiento de Llamadas </th>
							<th style="text-align:center" class="CellHead"> Porcentaje </th>
							<th style="text-align:center" class="CellHead"  id="name_segfar"> Incumplimiento Responsabilidad OET </th>
							<th style="text-align:center" class="CellHead"> Porcentaje </th>
							<th style="text-align:center" class="CellHead"  id="name_seglyt"> Incumplimiento Responsabilidad Corona </th>
							<th style="text-align:center" class="CellHead"> Porcentaje </th>
						</tr>

						<tr class="Style2DIV">
							<th id="tot_candes" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData['tot_candes'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 0)">'.$mData['tot_candes'].'</a>') ?>
							</th>
							<th id="tot_segcar" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData['tot_segcar'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 1)">'.$mData['tot_segcar'].'</a>') ?>
							</th>
							<th style="text-align:center" class="cellInfo onlyCell"><?= (!$mData['por_segcar'] ? '0' : $mData['por_segcar']) ?> %</th>
							<th id="tot_segfar" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData['tot_segfar'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 2)">'.$mData['tot_segfar'].'</a>') ?>
							</th>
							<th style="text-align:center" class="cellInfo onlyCell"><?= (!$mData['por_segfar'] ? '0' : $mData['por_segfar']) ?> %</th>
							<th id="tot_seglyt" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData['tot_seglyt'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 3)">'.$mData['tot_seglyt'].'</a>') ?>
							</th>
							<th style="text-align:center" class="cellInfo onlyCell"><?= (!$mData['por_seglyt'] ? '0' : $mData['por_seglyt']) ?> %</th>
						</tr>
						</tbody>
					</table>
					<br>
					<table width="100%" cellspacing="1" cellpadding="0">
						<tbody>
						<tr>
							<th style="text-align:center" colspan="8" class="CellHead"><b>DETALLADO POR DIAS</b></th>
						</tr>

						<tr class="Style2DIV">
							<th style="text-align:center" class="CellHead" rowspan="2"> Fecha </th>
							<th style="text-align:center" class="CellHead" rowspan="2"> Despachos Generados </th>
							<th style="text-align:center" class="CellHead" rowspan="2"> Cumplimiento de Llamadas </th>
							<th style="text-align:center" class="CellHead" rowspan="2"> Porcentaje </th>
							<th style="text-align:center" class="CellHead" colspan="4"> Incumplimiento de Llamadas </th>
						</tr>

						<tr class="Style2DIV">
							<th style="text-align:center" class="CellHead"> Responsabilidad OET </th>
							<th style="text-align:center" class="CellHead"> Porcentaje </th>
							<th style="text-align:center" class="CellHead"> Responsabilidad Corona </th>
							<th style="text-align:center" class="CellHead"> Porcentaje </th>
						</tr>
					<?php
					$mDate = $mFecInicia;
					$i=0;
					while( $mDate <= $mFecFinalx )
					{
						?>
						<tr class="Style2DIV">
							<td id="fecha<?= $i ?>" style="text-align:center" class="cellInfo onlyCell"><?= $mDate ?></td>
							<td id="can_despac<?= $i ?>" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData[$mDate]['can_despac'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mDate.'\', \''.$mDate.'\', 0)">'.$mData[$mDate]['can_despac'].'</a>') ?>
							</td>
							<td id="cum_segcar<?= $i ?>" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData[$mDate]['cum_segcar'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mDate.'\', \''.$mDate.'\', 1)">'.$mData[$mDate]['cum_segcar'].'</a>') ?>
							</td>
							<td style="text-align:center" class="cellInfo onlyCell"><?= (!$mData[$mDate]['por_segcar'] ? '0' : $mData[$mDate]['por_segcar']) ?> %</td>
							<td id="inc_segfar<?= $i ?>" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData[$mDate]['inc_segfar'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mDate.'\', \''.$mDate.'\', 2)">'.$mData[$mDate]['inc_segfar'].'</a>') ?>
							</td>
							<td style="text-align:center" class="cellInfo onlyCell"><?= (!$mData[$mDate]['por_segfar'] ? '0' : $mData[$mDate]['por_segfar']) ?> %</td>
							<td id="inc_seglyt<?= $i ?>" style="text-align:center" class="cellInfo onlyCell">
								<?= (!$mData[$mDate]['inc_seglyt'] ? '<label>0</label>' : '<a style="cursor:pointer; color:green" onclick="showDetail(\''.$mDate.'\', \''.$mDate.'\', 3)">'.$mData[$mDate]['inc_seglyt'].'</a>') ?>
							</td>
							<td style="text-align:center" class="cellInfo onlyCell"><?= (!$mData[$mDate]['por_seglyt'] ? '0' : $mData[$mDate]['por_seglyt']) ?> %</td>
						</tr>
						<?php
						$mDate = date ( 'Y-m-d', strtotime( '+1 day', strtotime($mDate) ) );
						$i++;
					}
					?>
						<tr class="Style2DIV">
							<td style="text-align:center" class="CellHead">TOTALES</td>
							<td style="text-align:center" class="CellHead">
								<?= (!$mData['tot_candes'] ? '<label>0</label>' : '<a style="cursor:pointer;" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 0)">'.$mData['tot_candes'].'</a>') ?>
							</td>
							<td style="text-align:center" class="CellHead">
								<?= (!$mData['tot_segcar'] ? '<label>0</label>' : '<a style="cursor:pointer;" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 1)">'.$mData['tot_segcar'].'</a>') ?>
							</td>
							<td style="text-align:center" class="CellHead"><?= (!$mData['por_segcar'] ? '0' : $mData['por_segcar']) ?> %</td>
							<td style="text-align:center" class="CellHead">
								<?= (!$mData['tot_segfar'] ? '<label>0</label>' : '<a style="cursor:pointer;" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 2)">'.$mData['tot_segfar'].'</a>') ?>
							</td>
							<td style="text-align:center" class="CellHead"><?= (!$mData['por_segfar'] ? '0' : $mData['por_segfar']) ?> %</td>
							<td style="text-align:center" class="CellHead">
								<?= (!$mData['tot_seglyt'] ? '<label>0</label>' : '<a style="cursor:pointer;" onclick="showDetail(\''.$mFecInicia.'\', \''.$mFecFinalx.'\', 3)">'.$mData['tot_seglyt'].'</a>') ?>
							</td>
							<td style="text-align:center" class="CellHead"><?= (!$mData['por_seglyt'] ? '0' : $mData['por_seglyt']) ?> %</td>
						</tr>

						<input type="hidden" value="G" id="tipo" name="tipo">
						<input type="hidden" value="<?= ($i-1) ?>" id="total" name="total">
						</tbody>
					</table>
				</div>
				<div class="col-md-12">&nbsp;</div> 
				<div class="col-md-12" id="container"></div>
			</div>
			<?php
		}else{
			?>
			<div id="contenido2">
				<div class="Style2DIV" id="ch">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tbody>
							<tr>
								<th style="text-align:center" colspan="6" class="cellInfo onlyCel"><h5>No se encontraron datos para los parámetros de consulta.</h5></th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}
	}

	/*! \fn: validateCumpliSeguimCargue
	 *  \brief: Valida el cumplimiento del seguimiento de cargue
	 *  \author: Ing. Fabian Salinas
	 *  \date:  22/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function validateCumpliSeguimCargue(){
		$mResult = '1';

		#Primer puesto de control del plan de ruta para el despacho + indicador cumplio seguimiento en cargue 
		$mSql = "SELECT c.cod_contro, a.ind_cumcar, b.cod_transp 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_seguim c 
					 ON a.num_despac = c.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_genera_rutcon d 
					 ON c.cod_rutasx = d.cod_rutasx 
					AND c.cod_contro = d.cod_contro 
				  WHERE a.num_despac = $_REQUEST[num_despac] 
					AND c.ind_estado != 2 
			   ORDER BY d.val_duraci ASC 
				  LIMIT 1 ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mCodContr1 = $mConsult -> ret_matrix('i');

		if( ($_REQUEST['cod_contro'] == $mCodContr1[0][0] || !$_REQUEST['cod_contro'] ) && $mCodContr1[0][1] == '0' )
		{ #El PC en el que se registra la novedad es igual al primer PC del plan de ruta
			$mSerTransp = getTransTipser( self::$cConexion, " AND a.cod_transp = ".$mCodContr1[0][2]." ", 
										  array('a.can_llaurb', 'a.can_llanac', 'a.can_llaimp', 'a.can_llaexp', 'a.can_llatr1', 'a.can_llatr2', 
										  		'a.tie_carurb', 'a.tie_carnac', 'a.tie_carimp', 'a.tie_carexp', 'a.tie_cartr1', 'a.tie_cartr2' ) 
										);
			$mCanLlamad = array( # Codigo Tipo despacho => array( Cantidad de llamadas Cargue, Tiempo para las llamadas en cargue )
								 1 => array($mSerTransp[0]['can_llaurb'], $mSerTransp[0]['tie_carurb']),
								 2 => array($mSerTransp[0]['can_llanac'], $mSerTransp[0]['tie_carnac']),
								 3 => array($mSerTransp[0]['can_llaimp'], $mSerTransp[0]['tie_carimp']),
								 4 => array($mSerTransp[0]['can_llaexp'], $mSerTransp[0]['tie_carexp']),
								 5 => array($mSerTransp[0]['can_llatr1'], $mSerTransp[0]['tie_cartr1']),
								 6 => array($mSerTransp[0]['can_llatr2'], $mSerTransp[0]['tie_cartr2']) 
							   );

			$mSql = "SELECT a.cod_tipdes, CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS fec_citcar 
					   FROM ".BASE_DATOS.".tab_despac_despac a 
					  WHERE a.num_despac = $_REQUEST[num_despac] ";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mDatDespac = $mConsult -> ret_matrix('a');
			$mCodTipdes = $mDatDespac[0]['cod_tipdes'];

			if( $mCanLlamad[$mCodTipdes][0] > 0 )
			{ #Cantidad de llamadas parametrizadas para seguimiento de cargue segun tipo de despacho sea mayor a 0
				$mSql = "(SELECT a.cod_contro 
							FROM ".BASE_DATOS.".tab_despac_contro a 
						   WHERE a.num_despac = $_REQUEST[num_despac] 
							 AND a.cod_contro = ".$mCodContr1[0][0]." ) 
						 UNION ALL 
						 (SELECT b.cod_contro 
							FROM ".BASE_DATOS.".tab_despac_noveda b 
						   WHERE b.num_despac = $_REQUEST[num_despac] 
							 AND b.cod_contro = ".$mCodContr1[0][0]." ) ";
				$mConsult = new Consulta($mSql, self::$cConexion );
				$mNovContr1 = $mConsult -> ret_matrix('a');
				$mNNov = sizeof($mNovContr1); #Cantidad de novedades del despacho

				if( $mNNov < $mCanLlamad[$mCodTipdes][0] )
				{ #Cantidad de Novedades menor a cantidad de llamadas parametrizadas
					$n = $mCanLlamad[$mCodTipdes][0] - $mNNov; #Cantidad de llamadas pendientes
					if( $n == 1 ){
						$mFec = $mDatDespac[0]['fec_citcar'];
					}else{
						$mMin = ($mCanLlamad[$mCodTipdes][1] / $mCanLlamad[$mCodTipdes][0]) * ($n-1);
						$mFec = strtotime( "-$mMin minute" , strtotime($mDatDespac[0]['fec_citcar']) );
						$mFec = date('Y-m-d H:i:s', $mFec);
					}
					# $mFec: Fecha Maxima para cumplir el seguimiento

					$mFecAct = date('Y-m-d H:i:s');
					if( $mFecAct <= $mFec && $n == 1 )
					{ #Cumple seguimiento y el despacho tiene una llamada pendiente (Actual)
						self::complyFollow( $_REQUEST['num_despac'] );
					}elseif( $mFecAct <= $mFec )
					{ #Cumple seguimeitno Y quedan pendientes llamadas
						$mResult = '0';
					}
				}
			}
		}

		echo $mResult;
	}

	/*! \fn: confirmCumpliSeguimCargue
	 *  \brief: Formulario para digitar la observacion del cumplimiento del seguimiento de cargue
	 *  \author: Ing. Fabian Salinas
	 *  \date:  25/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function confirmCumpliSeguimCargue(){
		?>
		<link rel='stylesheet' href='../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css' type='text/css'><br/>

		<table width="100%" cellspacing="1" cellpadding="0" border="0">
			<tr>
				<td class="CellHead" align="center">Observaci&oacute;n de Cumplimiento <br/>Seguimiento en Cargue</td>
			</tr>

			<tr>
				<td class="cellInfo">
					<select name="obs_cumcar" id="obs_cumcarID">
						<option value="">-----</option>
						<option value="Vehiculo se encuentra en planta">Vehiculo se encuentra en planta</option>
						<option value="Viaje Cancelado">Viaje Cancelado</option>
						<option value="Inconsistencia en la Contratacion">Inconsistencia en la Contratacion</option>
					</select>
				</td>
			</tr>

			<tr><td colspan="3" align="center">
				<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="saveObsSeguimCargue(<?= $_REQUEST[num_despac] ?>, <?= $_REQUEST[ind] ?>);" />
			</td></tr>
		</table>
		<?php
	}

	/*! \fn: saveObsSeguimCargue
	 *  \brief: Guarda la observacion cumplimiento anticipado seguimiento Cargue
	 *  \author: Ing. Fabian Salinas
	 *  \date:  25/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function saveObsSeguimCargue(){
		$mSql = "UPDATE ".BASE_DATOS.".tab_despac_despac 
					SET obs_cumcar = '$_REQUEST[obs_cumcar]', 
						ind_cumcar = 1 
				  WHERE num_despac = $_REQUEST[num_despac] ";
		$mConsult = new Consulta($mSql, self::$cConexion, "RC");
	}

	/*! \fn: complyFollow
	 *  \brief: update al indicador de cumplimiento de seguimiento en cargue
	 *  \author: Ing. Fabian Salinas
	 *  \date:  25/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNumDespac  Integer  Numero del despacho
	 *  \return: 
	 */
	private function complyFollow( $mNumDespac = null ){
		$mSql = "UPDATE ".BASE_DATOS.".tab_despac_despac 
					SET ind_cumcar = 1 
				  WHERE num_despac = $mNumDespac ";
		$mConsult = new Consulta($mSql, self::$cConexion, "RC");
	}

	/*! \fn: getData
	 *  \brief: Trae la data del informe general
	 *  \author: Ing. Fabian Salinas
	 *  \date:  27/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getData(){
		$mSerTransp = getTransTipser( self::$cConexion, " AND a.cod_transp = '$_REQUEST[cod_transp]' ", 
									  array('a.tie_carurb', 'a.tie_carnac', 'a.tie_carimp', 'a.tie_carexp', 'a.tie_cartr1', 'a.tie_cartr2' ) 
									);

		$mWhere  = !$_REQUEST['fec_inicia'] && !$_REQUEST['fec_finali'] ? "" : " AND DATE_FORMAT(a.fec_despac, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";
		$mWhere .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '$_REQUEST[num_despac]' ";
		$mWhere .= !$_REQUEST['num_manifi'] ? "" : " AND a.cod_manifi = '$_REQUEST[num_manifi]' ";
		$mWhere .= !$_REQUEST['num_viajex'] ? "" : " AND c.num_desext = '$_REQUEST[num_viajex]' ";
		$mWhere .= !$_REQUEST['tip_despac'] ? "" : " AND a.cod_tipdes = '$_REQUEST[tip_despac]' ";

		#cum_segcar => Cumplio Seguimiento en Cargue
		#inc_segfar => Faro Incumplio Seguimiento en Cargue
		#inc_seglyt => Corona  Incumplio Seguimiento en Cargue
		$mSql = "SELECT y.fec_despac, COUNT(y.num_despac) AS can_despac, 
						SUM(IF(y.ind_cumcar = 1, 1, 0)) AS cum_segcar, 
						SUM(IF(y.fec_creaci <= y.fec_propue AND y.ind_cumcar = 0, 1, 0)) AS inc_segfar, 
						SUM(IF(y.fec_creaci >  y.fec_propue AND y.ind_cumcar = 0, 1, 0)) AS inc_seglyt 
				   FROM (
						 SELECT x.*, 
								DATE_SUB(x.fec_citcar, INTERVAL x.tie_segcar MINUTE ) AS fec_propue
						   FROM (
										 SELECT a.num_despac, a.ind_cumcar, a.fec_creaci, 
												DATE_FORMAT(a.fec_despac, '%Y-%m-%d') AS fec_despac, 
												CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS fec_citcar, 
												CASE a.cod_tipdes 
													WHEN 1 THEN ".$mSerTransp[0]['tie_carurb']."
													WHEN 2 THEN ".$mSerTransp[0]['tie_carnac']."
													WHEN 3 THEN ".$mSerTransp[0]['tie_carimp']."
													WHEN 4 THEN ".$mSerTransp[0]['tie_carexp']."
													WHEN 5 THEN ".$mSerTransp[0]['tie_cartr1']."
													WHEN 6 THEN ".$mSerTransp[0]['tie_cartr2']."
													ELSE ".$mSerTransp[0]['tie_carurb']."
												END AS tie_segcar
										   FROM ".BASE_DATOS.".tab_despac_despac a 
									 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
											 ON a.num_despac = b.num_despac 
									  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext c 
											 ON a.num_despac = c.num_despac 
										  WHERE b.cod_transp = '$_REQUEST[cod_transp]' 
											AND a.ind_anulad = 'R' 
											AND a.fec_salida <= NOW()
										  		$mWhere
										  ORDER BY a.fec_despac 
								) x
						) y
			   GROUP BY y.fec_despac ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mData = $mConsult -> ret_matrix('a');
		
		if( sizeof($mData) > 0 ){
			$mResult = array();
			$mResult['tot_candes'] = 0;
			$mResult['tot_segcar'] = 0;
			$mResult['tot_segfar'] = 0;
			$mResult['tot_seglyt'] = 0;

			foreach ($mData as $row){
				$mResult[$row['fec_despac']] = $row;
				$mResult[$row['fec_despac']]['por_segcar'] = round(($row['cum_segcar'] * 100)/ $row['can_despac']); #por_segcar => Porcentaje Cumplio Seguimiento en Cargue
				$mResult[$row['fec_despac']]['por_segfar'] = round(($row['inc_segfar'] * 100)/ $row['can_despac']); #por_segfar => Porcentaje Faro Incumplio Seguimiento en Cargue
				$mResult[$row['fec_despac']]['por_seglyt'] = round(($row['inc_seglyt'] * 100)/ $row['can_despac']); #por_seglyt => Porcentaje Corona  Incumplio Seguimiento en Cargue
				$mResult['tot_candes'] += $row['can_despac'];
				$mResult['tot_segcar'] += $row['cum_segcar'];
				$mResult['tot_segfar'] += $row['inc_segfar'];
				$mResult['tot_seglyt'] += $row['inc_seglyt'];
			}

			$mResult['por_segcar'] = round(($mResult['tot_segcar'] * 100)/ $mResult['tot_candes']); #Porcentaje Total Cumplio Seguimiento en Cargue
			$mResult['por_segfar'] = round(($mResult['tot_segfar'] * 100)/ $mResult['tot_candes']); #Porcentaje Total Faro Incumplio Seguimiento en Cargue
			$mResult['por_seglyt'] = round(($mResult['tot_seglyt'] * 100)/ $mResult['tot_candes']); #Porcentaje Total Corona  Incumplio Seguimiento en Cargue
		}else{
			$mResult = false;
		}

		return $mResult;
	}

	/*! \fn: getDataDetail
	 *  \brief: Trae la data del informe detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date:  28/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataDetail(){
		$mTxt = array('Cumplio Seguimiento Cargue', 'Incumplio Faro', 'Incumplio Corona');
		$mSerTransp = getTransTipser( self::$cConexion, " AND a.cod_transp = '$_REQUEST[cod_transp]' ", 
									  array('a.tie_carurb', 'a.tie_carnac', 'a.tie_carimp', 'a.tie_carexp', 'a.tie_cartr1', 'a.tie_cartr2' ) 
									);

		$mWhere  = !$_REQUEST['fec_inicia'] && !$_REQUEST['fec_finali'] ? "" : " AND DATE_FORMAT(a.fec_despac, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";
		$mWhere .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '$_REQUEST[num_despac]' ";
		$mWhere .= !$_REQUEST['num_manifi'] ? "" : " AND a.cod_manifi = '$_REQUEST[num_manifi]' ";
		$mWhere .= !$_REQUEST['num_viajex'] ? "" : " AND c.num_desext = '$_REQUEST[num_viajex]' ";
		$mWhere .= !$_REQUEST['tip_despac'] ? "" : " AND a.cod_tipdes = '$_REQUEST[tip_despac]' ";

		$mSql = "SELECT z.*, CASE z.cum_segcar 
								WHEN 1 THEN 'Cumplio Seguimiento Cargue'
								WHEN 2 THEN 'Incumplio Faro'
								WHEN 3 THEN 'Incumplio Corona'
							 END AS seg_cargue 
				   FROM (
							 SELECT y.*, 
									IF(y.ind_cumcar = 1, '1', IF(y.fec_creaci <= y.fec_propue, '2', '3')) AS cum_segcar 
							   FROM (
									 SELECT x.*, 
											TIMESTAMPDIFF(MINUTE, x.fec_citcar, x.fec_creaci) AS dif_tiempo, 
											DATE_SUB(x.fec_citcar, INTERVAL x.tie_segcar MINUTE ) AS fec_propue 
									   FROM (
													 SELECT a.num_despac, a.ind_cumcar, a.fec_creaci, 
															a.cod_manifi, c.num_desext, b.num_placax, 
															e.abr_tercer AS nom_poseed, b.cod_conduc, 
															a.con_telmov, k.nom_tipdes, a.fec_despac, 
															a.obs_cumcar, 
															CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS fec_citcar, 
															IF(b.nom_conduc IS NULL, j.abr_tercer, b.nom_conduc) AS nom_conduc, 
															CONCAT(f.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_origen, 
															CONCAT(h.abr_ciudad, ' (', UPPER(LEFT(i.abr_depart, 4)), ')') AS ciu_destin, 
															CASE a.cod_tipdes 
																WHEN 1 THEN ".$mSerTransp[0]['tie_carurb']." 
																WHEN 2 THEN ".$mSerTransp[0]['tie_carnac']." 
																WHEN 3 THEN ".$mSerTransp[0]['tie_carimp']." 
																WHEN 4 THEN ".$mSerTransp[0]['tie_carexp']." 
																WHEN 5 THEN ".$mSerTransp[0]['tie_cartr1']." 
																WHEN 6 THEN ".$mSerTransp[0]['tie_cartr2']." 
																ELSE ".$mSerTransp[0]['tie_carurb']." 
															END AS tie_segcar 
													   FROM ".BASE_DATOS.".tab_despac_despac a 
												 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
														 ON a.num_despac = b.num_despac 
												  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext c 
														 ON a.num_despac = c.num_despac 
												  LEFT JOIN ".BASE_DATOS.".tab_vehicu_vehicu d 
														 ON b.num_placax = d.num_placax 
												  LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer e 
														 ON d.cod_tenedo = e.cod_tercer 
												 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad f 
														 ON a.cod_paiori = f.cod_paisxx 
														AND a.cod_depori = f.cod_depart 
														AND a.cod_ciuori = f.cod_ciudad 
												 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
														 ON a.cod_paiori = g.cod_paisxx 
														AND a.cod_depori = g.cod_depart 
												 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h 
														 ON a.cod_paides = h.cod_paisxx 
														AND a.cod_depdes = h.cod_depart 
														AND a.cod_ciudes = h.cod_ciudad 
												 INNER JOIN ".BASE_DATOS.".tab_genera_depart i 
														 ON a.cod_paides = i.cod_paisxx 
														AND a.cod_depdes = i.cod_depart 
												  LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer j 
														 ON b.cod_conduc = j.cod_tercer 
												 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes k 
														 ON a.cod_tipdes = k.cod_tipdes 
													  WHERE b.cod_transp = '$_REQUEST[cod_transp]' 
														AND a.ind_anulad = 'R' 
														AND a.fec_salida <= NOW()
															$mWhere 
											) x
									) y
				   		) z 
				  WHERE 1=1 
				";
		$mSql .= $_REQUEST['cum_segcar'] == '0' ? "" : " AND z.cum_segcar = $_REQUEST[cum_segcar] ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: showDetail
	 *  \brief: Crea HTML del detallado del informe
	 *  \author: Ing. Fabian Salinas
	 *  \date:  28/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function showDetail(){
		$mData = self::getDataDetail();
		$mCant = sizeof($mData);

		$mTittle = array(	"num_despac" => "Numero Despacho", 
							"cod_manifi" => "Manifiesto", 
							"num_desext" => "Viaje", 
							"fec_despac" => "Fecha del Despacho", 
							"nom_poseed" => "Poseedor", 
							"ciu_origen" => "Origen", 
							"ciu_destin" => "Destino", 
							"num_placax" => "Placa", 
							"nom_conduc" => "Conductor", 
							"cod_conduc" => "C.C. Conductor", 
							"con_telmov" => "Cel. Conductor", 
							"nom_tipdes" => "Tipo de Despacho", 
							"fec_citcar" => "Fecha Cita de Cargue", 
							"fec_creaci" => "Fecha Creacion Despacho", 
							"fec_propue" => "Fecha Propuesta Creacion", 
							"tie_segcar" => "Tiempo Seguimiento Cargue", 
							"dif_tiempo" => "Diferencia Tiempo", 
							"seg_cargue" => "Seguimiento Cargue", 
							"obs_cumcar" => "Observacion Cumplimiento Seguimiento Cargue"
						);

		$mStyle = "<style>
					.bg1 { background-color: #EBF8E2; }
					.bg2 { background-color: #DEDFDE; }
				</style>";


		$mHtml = new Formlib(2);
		
		$mHtml->SetBody($mStyle);
		$mHtml->SetBody('<center><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0" onclick="exportTableExcel( \'TabDetail'.$_REQUEST['tipo'].'ID\' );" style="cursor:pointer"></center><br/>');

		if( $mCant == 1 )
			$mHtml->SetBody('Se encontro 1 registro.<br/>');
		else
			$mHtml->SetBody('Se encontraron '.$mCant.' registros.<br/>');

		$mHtml->OpenDiv();
			$mHtml->SetBody('<table id="TabDetail'.$_REQUEST['tipo'].'ID" width="100%" align="center" cellspacing="1" cellpadding="3" border="0">');
				$mHtml->Row();
					$mHtml->Label( "#", array("class"=>"CellHead", "align"=>"center") );

					foreach ($mTittle as $key => $tittle)
						$mHtml->Label( $tittle, array("class"=>"CellHead", "align"=>"center") );
				$mHtml->CloseRow();

				$i=0;
				foreach ($mData as $row){
					$mBg = $i % 2 == 0 ? "bg1" : "bg2";

					$mHtml->Row();
						$mHtml->Label( ($i+1), array("class"=>"cellInfo onlyCell $mBg", "align"=>"left", "rowspan"=>$mSize) );

						foreach ($mTittle as $key => $tittle){
							if( $key != 'num_despac' )
								$mHtml->Label( htmlentities($row[$key]), array("class"=>"cellInfo onlyCell $mBg", "align"=>"left", "rowspan"=>$mSize) );
							else
								$mHtml->Label( '<a style="color:blue" href="index.php?cod_servic=3302&window=central&despac='.$row[$key].'&tie_ultnov=0&opcion=1">'.$row[$key].'</a>', array("class"=>"cellInfo onlyCell $mBg", "align"=>"left", "rowspan"=>$mSize) );
						}
					$mHtml->CloseRow();
					$i++;
				}

			$mHtml->SetBody('</table>');
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new CumpliSeguimCargue();
else
	$_INFORM = new CumpliSeguimCargue( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
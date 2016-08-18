<?php
/*! \file: class_califi_califi.php
 *  \brief: Clase principal para calificar
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 03/02/2016
 *  \bug: 
 *  \warning: 
 */


class Califi
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}

		switch($_REQUEST['Option']){
			case 'formAuditarDespac':
				self::formAuditarDespac();
				break;

			case 'getListActivi':
				self::getListActivi();
				break;

			case 'tableCalifiDespac':
				$mCalifi = self::getCalifiDesUse( array("num_despac"=>$_REQUEST['num_despac'], "cod_activi"=>$_REQUEST['cod_activi']) );
				if( sizeof($mCalifi) > 0 ) #Verifica que la actividad ya esta calificada
					self::tableCalificado( $mCalifi[0] ); #Tabla Calificada
				else 
					self::tableCalifi('despac'); #Tabla para calificar
				break;

			case 'insertCalifiDesUsu':
				self::insertCalifiDesUsu();
				break;

			case 'tableCalifiUsuari':
				self::tableCalifiUsuari();
				break;

			case 'informGeneral':
				self::informGeneral();
				break;

			case 'informDetail':
				self::informDetail();
				break;
		}
	}

	/*! \fn: formAuditarDespac
	 *  \brief: Formulario para auditar despachos
	 *  \author: Ing. Fabian Salinas
	 *  \date:  03/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formAuditarDespac(){
		$mOperac = self::getOperacion();

		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Label( "<b>Filtros</b>", array("class"=>"CellHead", "align"=>"left", "colspan"=>"4", "end"=>true) );

			$mHtml->Label( "<font style='color:red'>*</font> Tipo de Operaci&oacute;n: ", array("class"=>"cellInfo1", "width"=>"25%") );
			$mHtml->Select2( array_merge(self::$cNull, $mOperac), array("name"=>"cod_operac", "id"=>"cod_operacID", "obl"=>"1", "validate"=>"select", "class"=>"cellInfo1", "width"=>"25%", "onchange"=>"getListActivi($(this));") );
			$mHtml->Label( "<font style='color:red'>*</font> Actividad: ", array("class"=>"cellInfo1", "width"=>"25%") );
			$mHtml->Select2( self::$cNull, array("name"=>"cod_activi", "id"=>"cod_activiID", "obl"=>"1", "validate"=>"select", "class"=>"cellInfo1", "width"=>"25%") );
		$mHtml->CloseTable('tr');
		$mHtml->setBody('<br>');

		$mHtml->setBody('<table name="tab_audita" id="tab_auditaID" width="100%" cellspacing="0" cellpadding="3" border="0" align="center">');
		$mHtml->setBody('</table>');

		$mHtml->setBody('<center><br>
							<input type="button" onclick="closePopUp(\'popupAuditarID\')" value=" Cerrar " class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="cursor:pointer">
						</center>');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getOperacion
	 *  \brief: Trae las operaciones activas
	 *  \author: Ing. Fabian Salinas
	 *  \date:  29/01/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getOperacion(){
		$mSql = "SELECT a.cod_operac, a.nom_operac 
				   FROM ".BASE_DATOS.".tab_callce_operac a 
				  WHERE a.ind_estado = 1 
			   ORDER BY a.nom_operac ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getListActivi
	 *  \brief: Crea la lista de actividades
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: HTML <select>
	 */
	private function getListActivi(){
		if( $_REQUEST['cod_operac'] != '' )
			$mActivi = self::getActivi( $_REQUEST['cod_operac'] );

		$mHtml  = '<select validate="select" id="cod_activiID" obl="1" name="cod_activi" class="form_01" onchange="'.$_REQUEST['fun_javasc'].'">';
        	$mHtml .= '<option value="">-----</option>';

        if( $_REQUEST['cod_operac'] != '' ){
			foreach ($mActivi as $row)
				$mHtml .= '<option value="'.$row[0].'">'.$row[1].'</option>';
        }

        $mHtml .= '</select>';

        echo $mHtml;
	}

	/*! \fn: getActivi
	 *  \brief: Trae las actividades de las operaciones
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param:
	 *  \return: Matriz
	 */
	private function getActivi( $mCodOperac = null ){
		$mSql = "SELECT a.cod_activi, a.nom_activi 
				   FROM ".BASE_DATOS.".tab_activi_activi a 
				  WHERE a.ind_estado = 1 ";
		$mSql .= !$mCodOperac ? "" : " AND a.cod_operac = $mCodOperac ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: tableCalifi
	 *  \brief: Crea la tabla para calificar el despacho segun Operacion->Actividad Items
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mIndCalifi  String   despac => Calificar Despachos; usuari => Calificar Usuarios
	 *  \param: mTabTitlex  String   Titulo de la tabla
	 *  \param: mC 			Integer  Consecutivo para el ID
	 *  \param: mIndReturn  Boolean  true retorna html, false imprime html
	 *  \return: 
	 */
	private function tableCalifi( $mIndCalifi = null, $mTabTitlex = 'Auditar', $mC = '0', $mIndReturn = false ){
		$mItemsx = self::getItems($_REQUEST['cod_activi']);
		$mCodItemsx = array();

		$mHtml = new Formlib(2);

		$mHtml->Row();
			$mHtml->Label( "<b>$mTabTitlex</b>", array("class"=>"CellHead", "align"=>"left", "colspan"=>"5", "end"=>true) );

			$mHtml->Label( "<b>No.</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Items</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Cumplimiento</b>", array("class"=>"CellHead", "align"=>"center", "colspan"=>"3", "end"=>true) );

		foreach ($mItemsx as $row) {
			$mCodItemsx[] = $row[0];

			$mHtml->Label( $row[0], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $row[1], array("class"=>"cellInfo1", "align"=>"left", "id"=>"nom_items$row[0]ID") );
			$mHtml->setBody('<td align="right" class="cellInfo1" id="ind'.$mC.'_opcion'.$row[0].'TD">&nbsp;&nbsp;&nbsp;&nbsp;SI
								<input type="radio" value="SI" id="ind'.$mC.'_opcion'.$row[0].'ID" name="ind'.$mC.'_opcion'.$row[0].'" cod_itemsx="'.$row[0].'" val_porcen="'.$row[2].'">
							</td>');
			$mHtml->setBody('<td align="right" class="cellInfo1" id="ind'.$mC.'_opcion'.$row[0].'TD">&nbsp;&nbsp;NO
								<input type="radio" value="NO" id="ind'.$mC.'_opcion'.$row[0].'ID" name="ind'.$mC.'_opcion'.$row[0].'" cod_itemsx="'.$row[0].'" val_porcen="'.$row[2].'">
							</td>');
			$mHtml->setBody('<td align="right" class="cellInfo1" id="ind'.$mC.'_opcion'.$row[0].'TD">&nbsp;&nbsp;NA
								<input type="radio" value="NA" id="ind'.$mC.'_opcion'.$row[0].'ID" name="ind'.$mC.'_opcion'.$row[0].'" cod_itemsx="'.$row[0].'" val_porcen="'.$row[2].'">
							</td></tr>');
		}
		$mCodItemsx = implode('|', $mCodItemsx);
		
		$mHtml->Label( "Observacion:", array("class"=>"cellInfo1", "align"=>"left", "colspan"=>"5", "end"=>true) );
		$mHtml->TextArea( "", array("width"=>"100%", "align"=>"left", "class"=>"cellInfo1", "colspan"=>"5", "cols"=>"70", "rows"=>"2", "name"=>"obs_califi", "id"=>"obs_califiID", "end"=>true) );

		if( $mIndCalifi == 'despac' )
			$mHtml->Button( array("value"=>" Actualizar ", "colspan"=>"5", "onclick"=>"califiDespac($_REQUEST[cod_activi], '$mCodItemsx')", "class2"=>"CellHead", "align"=>"center", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", "end"=>true) );

		if( $mIndReturn == true )
			return $mHtml->MakeHtml();
		else
			echo $mHtml->MakeHtml();
	}

	/*! \fn: tableCalificado
	 *  \brief: Crea la tabla que muestra la calificacion del despacho segun Operacion->Actividad Items
	 *  \author: Ing. Fabian Salinas
	 *  \date:  08/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCalifi     Array    Data de la calificacion   
	 *  \param: mIndReturn  Boolean  true retorna html, false imprime html
	 *  \return: 
	 */
	private function tableCalificado( $mCalifi = null, $mIndReturn = false ){
		$mTxt = $mCalifi['num_despac'] == '' ? 'Usuario: '.$mCalifi['usr_califi'] : 'Despacho: '.$mCalifi['num_despac'];
		$mData = json_decode($mCalifi['jso_califi']);

		$mHtml = new Formlib(2);

		$mHtml->Row();
			$mHtml->Label( "$mTxt - Auditado por ".$mCalifi['usr_creaci']." el ".$mCalifi['fec_creaci'], array("class"=>"CellHead", "align"=>"left", "colspan"=>"4", "end"=>true) );

			$mHtml->Label( "<b>No.</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Items</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Cumplio</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>%</b>", array("class"=>"CellHead", "align"=>"center", "end"=>true) );

		for ($i=0; $i < sizeof($mData->cod); $i++) {
			$mItem = self::getItems($_REQUEST['cod_activi'], $mData->cod[$i]);

			$mHtml->Label( $mData->cod[$i], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $mItem[0][1], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $mData->val[$i], array("class"=>"cellInfo1", "align"=>"center") );
			$mHtml->Label( $mData->por[$i]." %", array("class"=>"cellInfo1", "align"=>"right", "end"=>true) );
		}

		$mHtml->Label( "<b>Observaci&oacute;n:</b> ".$mCalifi['obs_califi'], array("class"=>"cellInfo1", "align"=>"left", "colspan"=>"4", "end"=>true) );
		$mHtml->Label( "Cumplimiento del ".$mData->tsi." %", array("class"=>"CellHead", "align"=>"right", "colspan"=>"4", "end"=>true) );

		if( $mIndReturn == true )
			return $mHtml->MakeHtml();
		else
			echo $mHtml->MakeHtml();
	}

	/*! \fn: getItems
	 *  \brief: Trae los items de una actividad
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param:
	 *  \return: Matriz
	 */
	private function getItems($mCodActivi = null, $mCodItemsx = null){
		$mSql = "SELECT a.cod_itemsx, a.nom_itemsx, a.val_porcen, a.cod_activi 
				   FROM ".BASE_DATOS.".tab_activi_itemsx a 
				  WHERE a.ind_estado = 1 ";
		$mSql .= !$mCodActivi ? "" : " AND a.cod_activi IN ($mCodActivi) ";
		$mSql .= !$mCodItemsx ? "" : " AND a.cod_itemsx IN ($mCodItemsx) ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: insertCalifiDesUsu
	 *  \brief: Inserta la calificacion del despacho o usuario
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function insertCalifiDesUsu(){
		if( $_REQUEST['num_despac'] ){
			$mSql = "INSERT INTO ".BASE_DATOS.".tab_califi_desusr 
							( cod_activi, jso_califi, obs_califi, 
							  num_despac, val_cumpli, 
							  usr_creaci, fec_creaci ) 
					 VALUES ( '$_REQUEST[cod_activi]', '".json_encode($_REQUEST['itemsx'])."', '$_REQUEST[obs_califi]', 
							  '$_REQUEST[num_despac]', '$_REQUEST[val_cumpli]', 
							  '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
			$mConsult = new Consulta($mSql, self::$cConexion);
		}elseif( $_REQUEST['usr_califi'] ){
			$mUsuari = explode('|', $_REQUEST['usr_califi']);
			foreach ($mUsuari as $key => $val) {
				$mSql = "INSERT INTO ".BASE_DATOS.".tab_califi_desusr 
								( cod_activi, jso_califi, obs_califi, 
								  usr_califi, val_cumpli, 
								  usr_creaci, fec_creaci ) 
						 VALUES ( '$_REQUEST[cod_activi]', '".json_encode($_REQUEST['itemsx'][$val])."', '".$_REQUEST['obs_califi'][$val]."', 
								  '$val', '".$_REQUEST['itemsx'][$val]['tsi']."', 
								  '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
				$mConsult = new Consulta($mSql, self::$cConexion);
			}
		}

		if( $_REQUEST['num_despac'] || $_REQUEST['usr_califi'] ){
			echo '1';
		}else
			echo 'Despacho o Usuario a calificar no Selecionado';
	}

	/*! \fn: getCalifiDesUse
	 *  \brief: Trae las calificaciones de los despacho o usuarios
	 *  \author: Ing. Fabian Salinas
	 *  \date:  08/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData    Array   Campos del where
	 *  \param: mSqlAdi  String  Sql adicional se agrega al final de la sentencia 
	 *  \return: Matriz
	 */
	private function getCalifiDesUse( $mData = null, $mSqlAdi = null ){
		$mSql = "SELECT a.cod_activi, a.jso_califi, a.obs_califi, 
						a.usr_creaci, a.fec_creaci, a.usr_califi, 
						a.num_despac 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
				  WHERE 1=1 ";
		$mSql .= !$mData['cod_activi'] ? "" : " AND a.cod_activi = '$mData[cod_activi]' ";
		$mSql .= !$mData['num_despac'] ? "" : " AND a.num_despac = '$mData[num_despac]' ";
		$mSql .= !$mData['usr_creaci'] ? "" : " AND a.usr_creaci = '$mData[usr_creaci]' ";
		$mSql .= !$mData['usr_califi'] ? "" : " AND a.usr_califi IN ($mData[usr_califi]) ";
		$mSql .= !$mSqlAdi ? "" : $mSqlAdi;

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: getUsuari
	 *  \brief: Trae los usuarios activos
	 *  \author: Ing. Fabian Salinas
	 *  \date:  09/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mWhere  String  Iniciar con AND para agregar condiciones
	 *  \return: Matriz
	 */
	public function getUsuari( $mWhere = null ){
		$mSql = "SELECT a.cod_usuari, a.cod_usuari 
				   FROM ".BASE_DATOS.".tab_genera_usuari a 
				  WHERE a.ind_estado = 1 $mWhere 
			   ORDER BY a.cod_usuari ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: tableCalifiUsuari
	 *  \brief: Crea las tablas de las calificaciones para los usuarios
	 *  \author: Ing. Fabian Salinas
	 *  \date:  10/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function tableCalifiUsuari(){
		$mPorCalifi = false;
		$mUsrCalifi = explode('|', $_REQUEST['usr_califi']);

		
		$mHtml = new Formlib(2);

		$i=0;
		$j=0;
		foreach ($mUsrCalifi as $key => $usu) {
			$mCalifi = self::getCalifiDesUse( array("usr_califi"=>"'$usu'", "cod_activi"=>$_REQUEST['cod_activi']), " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') " );
			
			#Verifica que la actividad ya esta calificada
			if( sizeof($mCalifi) > 0 ){
				$mHtml->SetBody( '<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center" id="tab'.$j.'_okcalifiID" name="'.$usu.'" class="tab_okcalifi" consec="'.$j.'">' );
				$mHtml->SetBody( self::tableCalificado( $mCalifi[0], true ) ); #Tabla Calificada
				$j++;
			}
			else {
				$mPorCalifi = true;
				$mHtml->SetBody( '<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center" id="tab'.$i.'_porcalifID" name="'.$usu.'" class="tab_porcalif" consec="'.$i.'">' );
				$mHtml->SetBody( self::tableCalifi('usuari', $usu, $i, true) ); #Tabla para calificar
				$i++;
			}

			$mHtml->setBody('</table><br>');
		}

		if( $mPorCalifi == true ){
			$mSql = "SELECT GROUP_CONCAT(a.cod_itemsx SEPARATOR '|') AS cod_itemsx 
					   FROM ".BASE_DATOS.".tab_activi_itemsx a 
					  WHERE a.ind_estado = 1 
						AND a.cod_activi = $_REQUEST[cod_activi] 
				   GROUP BY a.cod_activi ";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mCodItemsx = $mConsult -> ret_matrix('i');

			$mHtml->Table('tr');
				$mHtml->Button( array("value"=>" Actualizar ", "colspan"=>"5", "onclick"=>"califiUsuari($_REQUEST[cod_activi], '".$mCodItemsx[0][0]."')", "class2"=>"CellHead", "align"=>"center", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only") );
			$mHtml->closeTable('tr');
		}

		echo $mHtml->MakeHtml();
	}

	/*! \fn: informGeneral
	 *  \brief: Pinta el informe general inf_califi_desusu.php
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function informGeneral(){
		$mData = self::getDataG();
		$mTd1 = 'align="right" style="background: #00660F; color: #FFF;"';

		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Label( "DETALLADO DE LAS ACTIVIDADES", array("class"=>"CellHead", "colspan"=>"6", "end"=>true) );

			$mHtml->Label( "Actividad", array("class"=>"CellHead", "rowspan"=>"2") );
			$mHtml->Label( "Total Registros", array("class"=>"CellHead", "rowspan"=>"2") );
			$mHtml->Label( "Cumple", array("class"=>"CellHead", "colspan"=>"2") );
			$mHtml->Label( "No Cumple", array("class"=>"CellHead", "colspan"=>"2", "end"=>true) );

			$mHtml->Label( "Cantidad", array("class"=>"CellHead") );
			$mHtml->Label( "Porcentaje", array("class"=>"CellHead") );
			$mHtml->Label( "Cantidad", array("class"=>"CellHead") );
			$mHtml->Label( "Porcentaje", array("class"=>"CellHead", "end"=>true) );

			$mTotalx = array('tot'=>0, 'cum'=>0, 'noc'=>0);
			foreach ($mData as $key => $row) {
				$mTot = $row['0'] + $row['1'];
				$mTotalx['tot'] += $mTot;
				$mTotalx['cum'] += $row['1'];
				$mTotalx['noc'] += $row['0'];

				$mHtml->Label( $row['nom_activi'], array("class"=>"cellInfo1", "align"=>"left") );
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'totalx\', \''.$key.'\')">'.$mTot.'</td>');
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'cumple\', \''.$key.'\')">'.$row['1'].'</td>');
				$mHtml->Label( round(($row['1']*100/$mTot),1)." %", array("class"=>"cellInfo1") );
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'nocump\', \''.$key.'\')">'.$row['0'].'</td>');
				$mHtml->Label( round(($row['0']*100/$mTot),1)." %", array("class"=>"cellInfo1", "end"=>true) );

			}

			$mHtml->SetBody('<td align="right" style="background: #00660F; color: #FFF;">TOTAL</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'totalx\', \''.$_REQUEST['cod_activi'].'\')">'.$mTotalx['tot'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'cumple\', \''.$_REQUEST['cod_activi'].'\')">'.$mTotalx['cum'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.'>'.round(($mTotalx['cum']*100/$mTot),1).' %</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'nocump\', \''.$_REQUEST['cod_activi'].'\')">'.$mTotalx['noc'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.'>'.round(($mTotalx['noc']*100/$mTot),1).' %</td>');
		$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getUsuariCalif
	 *  \brief: Trae los usuarios calificados o calificadores
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNomCampo  String  Nombre del campo a llamar
	 *  \return: Matriz
	 */
	public function getUsuariCalif($mNomCampo = null){
		$mSql = "SELECT a.$mNomCampo, a.$mNomCampo 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
				".( $mNomCampo == 'usr_califi' ? ' WHERE a.usr_califi IS NOT NULL ' : '' )."
			   GROUP BY a.$mNomCampo 
			   ORDER BY a.$mNomCampo ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: getDataG
	 *  \brief: Trae la data para el reporte general
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataG(){
		$mSql = "SELECT a.cod_activi, e.nom_activi, 
						IF(a.val_cumpli > 90, 1, 0 ) AS ind_cumpli 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON b.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_activi_activi e 
					 ON a.cod_activi = e.cod_activi 
				  WHERE 1=1 
				";

		$mSql .= !$_REQUEST['cod_activi'] ? "" : " AND a.cod_activi IN ($_REQUEST[cod_activi]) ";
		$mSql .= !$_REQUEST['usr_creaci'] ? "" : " AND a.usr_creaci IN ($_REQUEST[usr_creaci]) ";
		$mSql .= !$_REQUEST['fec_inicia'] || !$_REQUEST['fec_finali'] ? "" : " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";

		if( $_REQUEST['ind_pestan'] == 'despac' ){
			$mSql .= " AND a.usr_califi IS NULL AND a.num_despac IS NOT NULL ";
			$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = $_REQUEST[num_despac] ";
			$mSql .= !$_REQUEST['cod_tipdes'] ? "" : " AND b.cod_tipdes IN ($_REQUEST[cod_tipdes]) ";
			$mSql .= !$_REQUEST['cod_transp'] ? "" : " AND c.cod_transp IN ($_REQUEST[cod_transp]) ";
			$mSql .= !$_REQUEST['num_placax'] ? "" : " AND c.num_placax LIKE '$_REQUEST[num_placax]' ";
			$mSql .= !$_REQUEST['num_pedido'] ? "" : " AND d.num_pedido LIKE '$_REQUEST[num_pedido]' ";
		}elseif( $_REQUEST['ind_pestan'] == 'usuari' ){
			$mSql .= " AND a.usr_califi IS NOT NULL AND a.num_despac IS NULL ";
			$mSql .= !$_REQUEST['usr_califi'] ? "" : " AND a.usr_califi IN ($_REQUEST[usr_califi]) ";
		}

		$mSql = "SELECT x.cod_activi, x.nom_activi, x.ind_cumpli, 
						COUNT(x.ind_cumpli) AS can_regist 
				   FROM ( $mSql ) x 
			   GROUP BY x.cod_activi, x.ind_cumpli ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mData = $mConsult -> ret_matrix('a');

		$mResult = array();
		foreach ($mData as $row) {
			$mResult[$row['cod_activi']]['nom_activi'] = $row['nom_activi'];
			$mResult[$row['cod_activi']][$row['ind_cumpli']] = $row['can_regist'];
		}

		return $mResult;
	}

	/*! \fn: informDetail
	 *  \brief: Pinta el informe Detallado inf_califi_desusu.php
	 *  \author: Ing. Fabian Salinas
	 *  \date:  16/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function informDetail(){
		$mTitle[0] = array( "num_consec" => "#", 
							"obj_califi" => "Calificado",
							"nom_activi" => "Actividad", 
							"val_cumpli" => "Cumplimiento", 
							"obs_califi" => "Observacion", 
							"usr_creaci" => "Calificado por", 
							"fec_creaci" => "Fecha Calificacion"
						   );
		$mTitle[1] = array( "Item", "Calificacion", "Porcentaje" );

		$mIdxTablex = "tabCalifi".$_REQUEST['ind_pestan']."ID";
		$mData = self::getDataDetail();
		$mItems = self::getItems($_REQUEST['cod_activi']);
		$mItem = array();
		foreach ($mItems as $row) {
			$mItem[$row[3]][$row[0]] = $row[1];
		}

		$mHtml = new Formlib(2);

		$mHtml->SetBody('<center>
							<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="exportTableExcel( \''.$mIdxTablex.'\' );" style="cursor:pointer">
						 </center><br>');

		$mHtml->setBody("Se Encontraron ".sizeof($mData)." Registros");

		$mHtml->Table('tr');
		$mHtml->setBody('<table id="'.$mIdxTablex.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');

				foreach ($mTitle[0] as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead", "rowspan"=>"2") );
				}
				$mHtml->Label( "Items", array("class"=>"CellHead", "colspan"=>"3") );
			$mHtml->CloseRow('td');

				foreach ($mTitle[1] as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
			$mHtml->CloseRow('td');

			$x=0;
			foreach ($mData as $row) {
				$mBg = $x % 2 == 0 ? 'bgTD1' : 'bgTD2';
				$mJson = json_decode($row['jso_califi']);
				$mSize = sizeof($mJson->cod);

				foreach ($mTitle[0] as $key => $tit) {
					$mHtml->Label($row[$key], array("class"=>"TD $mBg", "rowspan"=>$mSize) );
				}

				$y=0;
				for ($i=0; $i < $mSize; $i++) { 
					if( $x % 2 == 0 ){
						$mBg = $y % 2 == 0 ? 'bgTD3' : 'bgTD4';
					}else{
						$mBg = $y % 2 == 0 ? 'bgTD5' : 'bgTD6';
					}

					$j = $mJson->cod[$i];
					$k = $row['cod_activi'];

						$mHtml->Label( $mItem[$k][$j], array("class"=>"TD $mBg") );
						$mHtml->Label( $mJson->val[$i], array("class"=>"TD $mBg") );
						$mHtml->Label( $mJson->por[$i]." %", array("class"=>"TD $mBg") );
					$mHtml->CloseRow('td');
					$y++;
				}
				$x++;
			}

		$mHtml->SetBody('</table>');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDataDetail
	 *  \brief: Trae la data para el reporte Detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date:  16/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataDetail(){
		$mSql = "SELECT a.cod_activi, a.jso_califi, e.nom_activi, 
						a.obs_califi, a.usr_creaci, a.fec_creaci, 
						CONCAT(a.val_cumpli, ' %') AS val_cumpli, 
						IF(a.val_cumpli > 90, 1, 0 ) AS ind_cumpli, 
						IF(a.num_despac IS NULL, a.usr_califi, a.num_despac) AS obj_califi 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON b.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_activi_activi e 
					 ON a.cod_activi = e.cod_activi 
				  WHERE 1=1 
				";

		$mSql .= !$_REQUEST['cod_activi'] ? "" : " AND a.cod_activi IN ($_REQUEST[cod_activi]) ";
		$mSql .= !$_REQUEST['usr_creaci'] ? "" : " AND a.usr_creaci IN ($_REQUEST[usr_creaci]) ";
		$mSql .= !$_REQUEST['fec_inicia'] || !$_REQUEST['fec_finali'] ? "" : " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";

		if( $_REQUEST['ind_pestan'] == 'despac' ){
			$mSql .= " AND a.usr_califi IS NULL AND a.num_despac IS NOT NULL ";
			$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = $_REQUEST[num_despac] ";
			$mSql .= !$_REQUEST['cod_tipdes'] ? "" : " AND b.cod_tipdes IN ($_REQUEST[cod_tipdes]) ";
			$mSql .= !$_REQUEST['cod_transp'] ? "" : " AND c.cod_transp IN ($_REQUEST[cod_transp]) ";
			$mSql .= !$_REQUEST['num_placax'] ? "" : " AND c.num_placax LIKE '$_REQUEST[num_placax]' ";
			$mSql .= !$_REQUEST['num_pedido'] ? "" : " AND d.num_pedido LIKE '$_REQUEST[num_pedido]' ";
		}elseif( $_REQUEST['ind_pestan'] == 'usuari' ){
			$mSql .= " AND a.usr_califi IS NOT NULL AND a.num_despac IS NULL ";
			$mSql .= !$_REQUEST['usr_califi'] ? "" : " AND a.usr_califi IN ($_REQUEST[usr_califi]) ";
		}

		$mSql = "SELECT @rownum := @rownum + 1 AS num_consec, x.* 
				   FROM ( SELECT @rownum :=0 ) r, 
						( $mSql ) x 
				  WHERE 1=1 ";

		switch ($_REQUEST['ind_column']) {
			case 'cumple': $mSql .= " AND x.ind_cumpli = 1 "; break;
			case 'nocump': $mSql .= " AND x.ind_cumpli = 0 "; break;
		}

		$mSql .= " ORDER BY x.obj_califi, fec_creaci ASC ";

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: getPermisosRespon
	 *  \brief: Trae los permisos de visualizacion del usuario por responsable
	 *  \author: Ing. Fabian Salinas
	 *  \date:  18/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	public function getPermisosRespon(){
		$mSql = "SELECT a.jso_infcal 
				   FROM ".BASE_DATOS.".tab_genera_respon a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
					 ON a.cod_respon = b.cod_respon 
				  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix('i');

		return json_decode($mData[0][0]);
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new Califi();
else
	$_INFORM = new Califi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );	

?>
<?php
/*! \file: par_activi_activi.php
 *  \brief: inserta y actualiza las actividades segun operacion
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 01/02/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
#setlocale(LC_ALL,"es_ES");

class configActivi
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
			case 'editItem':
				self::editItem();
				break;

			case 'editActivi':
				self::editActivi();
				break;

			case 'registActivi':
				self::registActivi();
				self::formulario();
				break;

			case 'formEditItem':
				self::formEditItem();
				break;

			case 'registItem':
				self::registItem();
				break;

			case 'updateItems':
				self::updateItems();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para registrar las actividades
	 *  \author: Ing. Fabian Salinas
	 *  \date:  01/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formulario(){
		@include_once( '../' . DIR_APLICA_CENTRAL . '/califi/class_califi_califi.php' );
		$mCalifi = new Califi( self::$cConexion, self::$cUsuario, self::$cCodAplica );
		$mOperac = $mCalifi->getOperacion();
		$mTD = "cellInfo1";

		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'par_activi_activi.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'new_ajax.js' );
		IncludeJS( 'dinamic_list.js' );

		$mHtml = new Formlib(2);
		$mHtml->CloseTable('tr');

		$mHtml->SetCss('jquery');
		$mHtml->SetCss('informes');
		$mHtml->SetCss("dinamic_list");
		$mHtml->SetCss("validator");

		#Acordion 1
		$mHtml->OpenDiv("class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FORMULARIO</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->OpenDiv("id:formID; class:Style2DIV");
					$mHtml->SetBody('<form id="form_RegistActiviID" name="form_RegistActivi" action="?" method="POST">');
						$mHtml->Table('tr');
							$mHtml->Label( "<b>Agregar Actividad a Evaluar</b>", array("class"=>"CellHead", "colspan"=>"4", "align"=>"left", "end"=>true) );

							$mHtml->Label( "<font style='color:red'>*</font> Tipo de Operaci&oacute;n: ", array("class"=>$mTD, "width"=>"15%") );
							$mHtml->Select2( array_merge(self::$cNull, $mOperac), array("name"=>"cod_operac", "id"=>"cod_operacID", "obl"=>"1", "validate"=>"select", "class"=>$mTD) );
							$mHtml->Label( "<font style='color:red'>*</font> Actividad: ", array("class"=>$mTD, "width"=>"15%") );
							$mHtml->Input( array("name"=>"nom_activi", "id"=>"nom_activiID", "value"=>"", "size"=>"50", "class"=>$mTD, "width"=>"35%", "obl"=>"1", "validate"=>"dir", "minlength"=>"3", "maxlength"=>"200", "end"=>true) );

							$mHtml->Button( array("value"=>" Registrar ", "onclick"=>"registActivi()", "colspan"=>"4", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", "class2"=>"cellInfo1", "align"=>"center", "end"=>true) );

							$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
							$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
							$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
							$mHtml->Hidden( array("name"=>"Option", "id"=>"OptionID", "value"=>"registActivi") );
						$mHtml->CloseTable('tr');
					$mHtml->SetBody('</form>');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Acordion 2
		$mHtml->OpenDiv("class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>REGISTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->OpenDiv("id:formID; class:Style2DIV");
						$mHtml->SetBody( self::getTableActivi() );
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getTableActivi
	 *  \brief: Muestra la tabla de las actividades registradas
	 *  \author: Ing. Fabian Salinas
	 *  \date:  01/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function getTableActivi(){
		$mSql = "SELECT a.cod_activi, a.nom_activi, b.nom_operac, 
						IF(a.ind_estado = 1, 'Activa', 'Inactiva') AS nom_estado, 
						a.ind_estado 
				   FROM ".BASE_DATOS.".tab_activi_activi a 
			 INNER JOIN ".BASE_DATOS.".tab_callce_operac b 
					 ON a.cod_operac = b.cod_operac ";

		$_SESSION["queryXLS"] = $mSql;

		if(!class_exists(DinamicList))
			include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");
		
		$list = new DinamicList( self::$cConexion, $mSql, "1" , "no", 'ASC');

		$list->SetClose('no');
		$list->SetHeader("Código", "field:a.cod_activi; width:1%;  ");
		$list->SetHeader("Actividad", "field:a.nom_activi; width:1%");
		$list->SetHeader("Tipo de Operación", "field:b.nom_operac; width:1%");
		$list->SetHeader("Estado", "field:IF(a.ind_estado = 1, 'Activa', 'Inactiva')" );
		$list->SetOption("Opciones","field:a.ind_estado; width:1%; onclikDisable:editActivi( '0', $(this).parent().next() ); onclikEnable:editActivi( '1', $(this).parent().next() ); onclikEdit:formEditItem( $(this).parent().next() )" );
		$list->SetHidden("cod_activi", "0" );

		$list->Display(self::$cConexion);

		$_SESSION["DINAMIC_LIST"] = $list;

		return $list -> GetHtml();
	}

	/*! \fn: registActivi
	 *  \brief: Registra las actividades
	 *  \author: Ing. Fabian Salinas
	 *  \date:  01/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function registActivi(){
		$mSql = "SELECT a.cod_activi 
				   FROM ".BASE_DATOS.".tab_activi_activi a 
				  WHERE a.nom_activi LIKE '$_REQUEST[nom_activi]' 
					AND a.cod_operac = '$_REQUEST[cod_operac]' ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mCodActivi = $mConsult -> ret_matrix('i');

		if( sizeof($mCodActivi) < 1 ){
			$mSql = "INSERT INTO ".BASE_DATOS.".tab_activi_activi 
							( nom_activi, cod_operac, usr_creaci, fec_creaci )
					 VALUES ( '$_REQUEST[nom_activi]', '$_REQUEST[cod_operac]', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() ) ";
			$mConsult = new Consulta($mSql, self::$cConexion );
		}
	}

	/*! \fn: editActivi
	 *  \brief: Edita las actividades, solo se puede cambiar el estado, el nombre y el operador no deben ser modificados
	 *  \author: Ing. Fabian Salinas
	 *  \date:  01/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function editActivi(){
		$mSql = "UPDATE ".BASE_DATOS.".tab_activi_activi 
					SET ind_estado = $_REQUEST[ind_estado] 
				  WHERE cod_activi = $_REQUEST[cod_activi] ";
		$mConsult = new Consulta($mSql, self::$cConexion );
	}

	/*! \fn: formEditItem
	 *  \brief: Formulario apra editar los items de una actividade
	 *  \author: Ing. Fabian Salinas
	 *  \date: 02/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formEditItem(){
		$mDatItemsx = self::getItemsActivi( $_REQUEST['cod_activi'] ); #Data, Items de la actividad
		$mArray1 = array("class"=>"CellHead", "align"=>"center");

		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Label( "<b>Registrar Nuevo Item</b>", array("class"=>"CellHead", "colspan"=>"3", "align"=>"left", "end"=>true) );

			$mHtml->Label( "<font style='color:red'>*</font> Item: ", array("class"=>"cellInfo1", "width"=>"35%") );
			$mHtml->Input( array("name"=>"nom_itemsx", "id"=>"nom_itemsxID", "value"=>"", "size"=>"50", "class"=>"cellInfo1", "width"=>"35%", "obl"=>"1", "validate"=>"dir", "minlength"=>"3", "maxlength"=>"50") );
			$mHtml->Button( array("value"=>" Registrar ", "onclick"=>"verifyItem( $_REQUEST[cod_activi] )", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", "class2"=>"cellInfo1", "align"=>"left", "end"=>true) );
		$mHtml->CloseTable('tr');
		$mHtml->SetBody('<br>');

		$mHtml->Table('tr');
			$mHtml->Label( "<b>Items de la Actividad a Evaluar</b>", array("class"=>"CellHead", "colspan"=>"5", "align"=>"left", "end"=>true) );

			$mHtml->Label( "Consecutivo", $mArray1 );
			$mHtml->Label( "Item", $mArray1 );
			$mHtml->Label( "Porcentaje", array_merge($mArray1, array("id"=>"por_totalxID")) );
			$mHtml->Label( "Estado", $mArray1 );
			$mHtml->Label( "Opciones", array_merge($mArray1, array("end"=>true)) );

		$mTotPorcen = 0;
		foreach ($mDatItemsx as $row) {
			if( $row['ind_estado'] == 1 ){
				$mImg = array('0', 'delete', 'Inactivar');
				$mTotPorcen += $row['val_porcen'];
			}
			else{
				$mImg = array('1', 'active', 'Activar');
			}

			$mVal = '<img class="ImagenDinamicList" onclick="confirmGL(\'Esta Seguro que Desea '.$mImg[2].' el Item?\', \'editItem('.$mImg[0].', '.$row['cod_itemsx'].', '.$_REQUEST['cod_activi'].')\' )" src="../satt_standa/images/'.$mImg[1].'.png" title="'.$mImg[2].'">';

			$mHtml->Label( $row['cod_itemsx'], array("class"=>"cellInfo1", "align"=>"center") );
			$mHtml->Label( $row['nom_itemsx'], array("class"=>"cellInfo1", "align"=>"left") );

			if( $row['ind_estado'] == 1 )
				$mHtml->Input( array("class"=>"cellInfo1", "align"=>"left", "size"=>"3", "value"=>$row['val_porcen'], "id"=>"val_porcenID".$row['cod_itemsx'], "name"=>$row['cod_itemsx'], "onchange"=>"calPorcentaje()") );
			else
				$mHtml->Label( $row['val_porcen'], array("class"=>"cellInfo1", "align"=>"left") );
			
			$mHtml->Label( $row['nom_estado'], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $mVal, array("class"=>"cellInfo1", "align"=>"center", "end"=>true) );
		}

		$mHtml->Label( "Total", array("class"=>"CellHead", "colspan"=>"2", "align"=>"right") );
		$mHtml->Label( $mTotPorcen." %", array("class"=>"CellHead", "colspan"=>"3", "align"=>"left", "id"=>"totPorcenID", "end"=>true) );

			$mHtml->Button( array("value"=>" Actualizar / Cerrar", "onclick"=>"updateItems( $_REQUEST[cod_activi] )", "colspan"=>"5", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", "class2"=>"CellHead", "align"=>"center") );
		$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getItemsActivi
	 *  \brief: Trae los items de una actividad
	 *  \author: Ing. Fabian Salinas
	 *  \date:  02/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCodActivi  Integer  Codigo de la actividad
	 *  \return: Matriz
	 */
	private function getItemsActivi( $mCodActivi = null ){
		$mSql = "SELECT a.cod_itemsx, a.nom_itemsx, b.nom_activi, 
						a.ind_estado, a.val_porcen, 
						IF(a.ind_estado = 1, 'Activo', 'Inactivo') AS nom_estado  
				   FROM ".BASE_DATOS.".tab_activi_itemsx a 
			 INNER JOIN ".BASE_DATOS.".tab_activi_activi b 
					 ON a.cod_activi = b.cod_activi 
				  WHERE a.cod_activi = '$mCodActivi' ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: registItem
	 *  \brief: Registra los Items segun actividad
	 *  \author: Ing. Fabian Salinas
	 *  \date:  02/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function registItem(){
		$mSql = "SELECT a.cod_itemsx 
				   FROM ".BASE_DATOS.".tab_activi_itemsx a 
				  WHERE a.nom_itemsx LIKE '$_REQUEST[nom_itemsx]' 
					AND a.cod_activi = '$_REQUEST[cod_activi]' ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mCodActivi = $mConsult -> ret_matrix('i');

		$mTxt = 'El Item que intenta registrar ya esta asignado a esta Actividad.<br><br>';

		if( sizeof($mCodActivi) < 1 ){
			$mSql = "SELECT (100 - SUM(a.val_porcen)) AS pen_porcen 
					   FROM ".BASE_DATOS.".tab_activi_itemsx a 
					  WHERE a.cod_activi = '$_REQUEST[cod_activi]' 
						AND a.ind_estado = 1 
				   GROUP BY a.cod_activi ";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mPorcent = $mConsult -> ret_matrix('i');
			$mPorcent = $mPorcent[0][0];

			$mSql = "INSERT INTO ".BASE_DATOS.".tab_activi_itemsx 
							( nom_itemsx, cod_activi, val_porcen, 
							  usr_creaci, fec_creaci )
					 VALUES ( '$_REQUEST[nom_itemsx]', '$_REQUEST[cod_activi]', '$mPorcent', 
					 		  '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() ) ";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mTxt = 'Item Registrado con Exito<br><br>';
		}

		echo $mTxt;
		echo '<center><input type="button" style="cursor:pointer" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" name="tag0" id="tag0ID" value=" Cerrar " onclick="closePopUp(\'popupItemOkID\'); ajaxEditItem( '.$_REQUEST['cod_activi'].' )"></center>';
	}

	/*! \fn: editItem
	 *  \brief: Edita los item de las actividades
	 *  \author: Ing. Fabian Salinas
	 *  \date:  02/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function editItem(){
		$mSql = "UPDATE ".BASE_DATOS.".tab_activi_itemsx 
					SET ind_estado = $_REQUEST[ind_estado], 
						val_porcen = 0 
				  WHERE cod_itemsx = $_REQUEST[cod_itemsx] ";
		$mConsult = new Consulta($mSql, self::$cConexion );
	}

	/*! \fn: updateItems
	 *  \brief: Actualiza los porcentajes de los items
	 *  \author: Ing. Fabian Salinas
	 *  \date:  03/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function updateItems(){
		for ($i=0; $i < sizeof($_REQUEST['cod_itemsx']); $i++) { 
			$mSql = "UPDATE ".BASE_DATOS.".tab_activi_itemsx 
						SET val_porcen = ".$_REQUEST['val_porcen'][$i]." 
					  WHERE cod_itemsx = ".$_REQUEST['cod_itemsx'][$i]." ";
			$mConsult = new Consulta($mSql, self::$cConexion );
		}
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new configActivi();
else
	$_INFORM = new configActivi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );	

?>
<?php
/*! \file: ins_formul_formul
 *  \brief: Controladora del CRUD para formularios personalizados
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 26/08/2016
 *  \bug: 
 *  \warning: 
 */

class ControladorFormularios
{
	private $conexion,
			$codAplica,
			$usuario;

	private static $null = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );

			$this->conexion = $AjaxConnection;
			$this->usuario = $_SESSION['datos_usuario'];
			$this->codAplica = $_SESSION['codigo'];
		}else{
			$this->conexion  = $co;
			$this->usuario   = $us;
			$this->codAplica = $ca;
		}

		@include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );

		switch($_REQUEST['Option']){
			case 'activarFormul':
				$this->activarFormularios();
				break;
			case 'informFormulariosRegistrados':
				$this->informFormulariosRegistrados();
				break;
			case 'saveFormul':
				$this->saveFormulFormul();
				break;
			case 'saveEditFormul':
				$this->saveEditFormulFormul();
				break;
			case 'formularioEditarFormul':
				$this->formularioEditarFormul();
				break;
			default:
				$this->formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Pinta el formulario para registrar nuevos formularios
	 *  \author: Ing. Fabian Salinas
	 *  \date: 31/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formulario() {
		$tablas = $this->getFormulTablas();
		$mTD = array("class"=>"cellInfo1", "width"=>"25%");
		$mAs = '<label style="color: red">* </label>';

		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'sweetalert-dev.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'new_ajax.js' );
		IncludeJS( 'dinamic_list.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'ins_formul_formul.js' );

		$mHtml = new Formlib(2);
		
		$mHtml->SetCss("jquery");
		$mHtml->SetCss("informes");
		$mHtml->SetCss("validator");
		$mHtml->SetCss("sweetalert");
		$mHtml->SetCss("dinamic_list");

		$mHtml->SetBody('
			<style>
				#formul_list, #formul_selected, #formul_list_edit, #formul_selected_edit {
					border: 1px solid #eee;
					width: 330px;
					min-height: 20px;
					list-style-type: none;
					margin: 0;
					padding: 5px 0 0 0;
					float: left;
					margin-right: 10px;
				}
				#formul_list li, #formul_selected li, #formul_list_edit li, #formul_selected_edit li {
					margin: 0 5px 5px 5px;
					padding: 5px;
					font-size: 1.2em;
					width: 308px;
				}
			</style>');

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("id:accFormID; class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>REGISTRAR FORMULARIO</b></h3>");
			$mHtml->OpenDiv("id:secFormID");
				$mHtml->OpenDiv("id:formID; class:Style2DIV");
					$mHtml->Table('tr');
						$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:left">Definir Formulario</th></tr><tr>');

						$mHtml->Label( $mAs."Nombre del Formulario: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"formul[nombre]", "id"=>"formul_nombre", "size"=>"50",
							"onkeyup"=>"llenarBocetoFormulario($(this));", "obl"=>true, "minlength"=>"5", "maxlength"=>"50", "validate"=>"texto")) );
						$mHtml->Label( $mAs."Tipo de Formulario: ", $mTD );
						$mHtml->Select2( array_merge(self::$null, $tablas), array_merge($mTD, array("name"=>"formul[tipo]", "id"=>"formul_tipo", 
							"validate"=>"select", "obl"=>true, "end"=>true)) );

						$mHtml->Label( "&nbsp;", array_merge($mTD, array("colspan"=>"4", "end"=>true)) );

						$mHtml->Label( "&nbsp;", $mTD );
						$mHtml->SetBody('<th class="CellHead">Campos Disponibles</th>');
						$mHtml->SetBody('<th class="CellHead">Campos Seleccionados</th>');
						$mHtml->Label( "&nbsp;", array_merge($mTD, array("end"=>true)) );

						$mHtml->Label( "&nbsp;", $mTD );
						$mHtml->SetBody('<td class="cellInfo1" style="vertical-align:top" align="right">');
						$mHtml->SetBody( $this->buildSortable( 'formul_list', 'connectedSortable', $this->getFormulCamposAll() ) );
						$mHtml->SetBody('</td><td class="cellInfo1" style="vertical-align:top">');
						$mHtml->SetBody( $this->buildSortable( 'formul_selected', 'connectedSortable' ) );
						$mHtml->SetBody('</td>');
						$mHtml->Label( "&nbsp;", $mTD );
					$mHtml->CloseTable('tr');

					$mHtml->Table('tr');
						$mHtml->SetBody('<th id="tituloBoceto" class="CellHead" colspan="2" style="text-align:left">Boceto del Formulario</th>');
					$mHtml->CloseTable('tr');

					$mHtml->SetBody('<table id="tabBoceto" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"></table>');
					
					$mHtml->Table('tr');
						$mHtml->Button( array("value"=>"Guardar", "class2"=>"cellInfo1", "align"=>"center", "onclick"=>"saveFormul();", "colspan"=>"6", "end"=>true) );
						$mHtml->SetBody('
							<td align="left" colspan="6" class="cellInfo1">
								<label>Para los campos obligatorios, seleccionar el check correspondiente</label>
							</td>');
					$mHtml->CloseTable('tr');

					$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
					$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
					$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		$mHtml->OpenDiv("id:accInfoID; class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FORMULARIOS REGISTRADOS</b></h3>");
			$mHtml->OpenDiv("id:secInfoID");
				$mHtml->OpenDiv("id:infoID; class:Style2DIV");
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		$mHtml->Javascript('
				$("body").ready(function() {
					$("#tabOption").hide();
					$("#secFormID").height(\'auto\');
					$("#secInfoID").height(\'auto\');
					$("#accFormID h3").trigger( "click" );
					printInformFormulariosRegistrados();

					$( "#formul_list, #formul_selected" ).sortable({
						connectWith: ".connectedSortable",
						stop: function() {
							pintarBocetoFormulario(\'\');
						}
					}).disableSelection();
				});
			');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: buildSortable
	 *  \brief: Construye el html para el sortable
	 *  \author: Ing. Fabian Salinas
	 *  \date: 31/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: id     string  Id del UL
	 *  \param: data   array   Datos del sortable
	 *  \return: string
	 */
	private function buildSortable($id, $class, $data = null, $property = null) {
		$html  = '<ul class="'.$class.' ui-sortable" id="'.$id.'">';

		foreach ($data as $row) {
			$html .= '<li class="ui-state-highlight ui-sortable-handle" cod_consec="'.$row['cod_consec'].'" property="'.$property.'" 
				obl="'.$row['ind_obliga'].'" val_htmlxx="'.htmlentities($row['val_htmlxx']).'" >';
			$html .= $row['nom_campox'];
			$html .= '</li>';
		}
		
		$html .= '</ul>';

		return $html;
	}

	/*! \fn: getFormulCamposAll
	 *  \brief: Trae los campos de formularios registrados
	 *  \author: Ing. Fabian Salinas
	 *  \date: 31/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: array
	 */
	private function getFormulCamposAll() {
		$sql = "SELECT a.cod_consec, a.nom_campox, a.val_htmlxx,
					   0 AS ind_obliga
			      FROM ".BASE_DATOS.".tab_formul_campos a
			     WHERE a.ind_estado = 1
			  ORDER BY a.nom_campox ";
		$consult = new Consulta($sql, $this->conexion );
		return $consult->ret_matrix('a');
	}

	/*! \fn: saveFormulFormul
	 *  \brief: Guarda el nuevo formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 31/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function saveFormulFormul() {
		$formul = $this->insertFormulFormul($_REQUEST['nombre'], $_REQUEST['tipo']);

		$i=1;
		foreach ($_REQUEST['campos'] as $campo => $obl) {
			$this->insertFormulDetail($formul, $campo, $obl, $i);
			$i++;
		}

		echo "1";
	}

	/*! \fn: insertFormulDetail
	 *  \brief: Insert para tab_formul_detail
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: formul  integer  ID tab_formul_formul
	 *  \param: campo   integer  ID tab_formul_campos
	 *  \param: obl     integer  Obligatorio 0 o 1
	 *  \return: integer
	 */
	private function insertFormulDetail($formul, $campo, $obl = 0, $orden = 0) {
		$sql = "INSERT INTO ".BASE_DATOS.".tab_formul_detail
					(cod_formul, cod_campox, ind_obliga, num_ordenx)
				VALUES
					($formul, $campo, $obl, $orden) ";
		new Consulta($sql, $this->conexion );

		return mysql_insert_id();
	}

	/*! \fn: insertFormulFormul
	 *  \brief: Inserta el Formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: nombre  string   Nombre del Formulario
	 *  \param: tipo    integer  ID de tab_formul_tablas
	 *  \return: 
	 */
	private function insertFormulFormul($nombre, $tipo) {
		$sql = "INSERT INTO ".BASE_DATOS.".tab_formul_formul 
					(cod_tablas, nom_formul, 
					 usr_creaci, fec_creaci)
				VALUES
					($tipo, '$nombre',
					 '".$this->usuario['cod_usuari']."', NOW() )";
		new Consulta($sql, $this->conexion );

		return mysql_insert_id();
	}

	/*! \fn: getFormulTablas
	 *  \brief: Trae la lista de tipos de formularios
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: array
	 */
	private function getFormulTablas() {
		$sql = "SELECT a.cod_consec, a.nom_tablax
				  FROM ".BASE_DATOS.".tab_formul_tablas a
			  ORDER BY a.nom_tablax ";
		$consult = new Consulta($sql, $this->conexion );
		return $consult->ret_matrix('i');
	}

	/*! \fn: informFormulariosRegistrados
	 *  \brief: Pinta el informe de formularios personalizados registrados
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function informFormulariosRegistrados() {
		$_SESSION["queryXLS"] = $this->construirSqlFormulFormulAll();

		if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }

        $list = new DinamicList($this->conexion, $_SESSION["queryXLS"], "1", "no", 'ASC');
        $list->SetClose('no');

        $list->SetHeader("Tipo", "field:b.nom_tablax; width:1%");
        $list->SetHeader("Nombre Formulario", "field:a.nom_formul; width:1%");
        $list->SetHeader("Estado", "field:IF(a.ind_estado = 1, 'ACTIVO', 'INACTIVO'); width:1%");
        $list->SetOption("Opciones", "field:ind_estado; width:1%; onclikDisable:activarFormul( this, 0 ); onclikEnable:activarFormul( this, 1 ); onclikEdit:formularioEditarFormul( this )");
        $list->SetHidden("cod_consec", "3");
        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        echo $list->GetHtml();
	}

	/*! \fn: construirSqlFormulFormulAll
	 *  \brief: Construye la query para listar todos los formularios personalizados registrados
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: string
	 */
	private function construirSqlFormulFormulAll() {
		return "SELECT b.nom_tablax, a.nom_formul, 
					   IF(a.ind_estado = 1, 'ACTIVO', 'INACTIVO') AS estado,
					   a.cod_consec, a.ind_estado
				  FROM ".BASE_DATOS.".tab_formul_formul a
			INNER JOIN ".BASE_DATOS.".tab_formul_tablas b ON a.cod_tablas = b.cod_consec
				 WHERE 1 ";
	}

	/*! \fn: activarFormularios
	 *  \brief: Activa e Inactiva los formularios personalizados registrados
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function activarFormularios() {
		$sql = "UPDATE ".BASE_DATOS.".tab_formul_formul 
				   SET ind_estado = $_REQUEST[estado],
					   usr_modifi = '".$this->usuario['cod_usuari']."',
					   fec_modifi = NOW()
				 WHERE cod_consec = $_REQUEST[consec] ";
		new Consulta($sql, $this->conexion );

		echo "1";
	}

	/*! \fn: formularioEditarFormul
	 *  \brief: Pinta el formulario para editar formularios personalizados
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formularioEditarFormul() {
		$formul = $this->getFormulFormul($_REQUEST['consec']);
		$mTD = array("class"=>"cellInfo1", "width"=>"25%");
		$mAs = '<label style="color: red">* </label>';

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:form_editID; class:Style2DIV");
			$mHtml->Table('tr');
					$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:left">Definir Formulario</th></tr><tr>');

					$mHtml->Label( $mAs."Nombre del Formulario: ", $mTD );
					$mHtml->Input( array_merge($mTD, array("name"=>"formul[nombre]", "id"=>"formul_nombre_edit", "size"=>"50", "value"=> $formul['nom_formul'],
						"onkeyup"=>"llenarBocetoFormulario($(this));", "obl"=>true, "minlength"=>"5", "maxlength"=>"50", "validate"=>"texto")) );
					$mHtml->Label( $mAs."Tipo de Formulario: ", $mTD );
					$mHtml->Label( $formul['nom_tablax'], array_merge($mTD, array("align"=>"left")) );
				$mHtml->CloseRow();

				$mHtml->Row();
					$mHtml->Label( "&nbsp;", array_merge($mTD, array("colspan"=>"4")) );
				$mHtml->CloseRow();

				$mHtml->Row();
					$mHtml->Label( "&nbsp;", $mTD );
					$mHtml->SetBody('<th class="CellHead">Campos Disponibles</th>');
					$mHtml->SetBody('<th class="CellHead">Campos Seleccionados</th>');
					$mHtml->Label( "&nbsp;", array_merge($mTD, array("end"=>true)) );

					$mHtml->Label( "&nbsp;", $mTD );
					$mHtml->SetBody('<td class="cellInfo1" style="vertical-align:top" align="right">');
					$mHtml->SetBody( $this->buildSortable( 'formul_list_edit', 'connectedSortableEdit', $this->getFormulCampos($_REQUEST['consec'], true) ) );
					$mHtml->SetBody('</td><td class="cellInfo1" style="vertical-align:top">');
					$mHtml->SetBody( $this->buildSortable( 'formul_selected_edit', 'connectedSortableEdit', $this->getFormulCampos($_REQUEST['consec']), "selected" ) );
					$mHtml->SetBody('</td>');
					$mHtml->Label( "&nbsp;", $mTD );
			$mHtml->CloseTable('tr');

			$mHtml->Table('tr');
					$mHtml->SetBody('<th id="tituloBoceto_edit" class="CellHead" colspan="2" style="text-align:left">Boceto del Formulario</th>');
				$mHtml->CloseTable('tr');

				$mHtml->SetBody('<table id="tabBoceto_edit" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"></table>');
				
				$mHtml->Table('tr');
					$mHtml->Button( array("value"=>"Guardar", "class2"=>"cellInfo1", "align"=>"center", "onclick"=>"saveEditFormul();", "colspan"=>"6") );
				$mHtml->CloseRow();

				$mHtml->Row();
					$mHtml->SetBody('
						<td align="left" colspan="6" class="cellInfo1">
							<label>Para los campos obligatorios, seleccionar el check correspondiente</label>
						</td>');
			$mHtml->CloseTable('tr');

			$mHtml->Hidden( array("name"=>"formul_edit[consec]", "id"=>"formul_consec_edit", "value"=>$_REQUEST['consec']) );
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getFormulCampos
	 *  \brief: Trae los campos de formularios registrados, para un formulario especifico
	 *  \author: Ing. Fabian Salinas
	 *  \date: 31/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: array
	 */
	private function getFormulCampos($formul, $null = false) {
		$sql = "SELECT a.cod_consec, a.nom_campox, a.val_htmlxx, 
					   b.ind_obliga
			      FROM ".BASE_DATOS.".tab_formul_campos a
			 LEFT JOIN ".BASE_DATOS.".tab_formul_detail b ON a.cod_consec = b.cod_campox AND b.cod_formul = $formul
			     WHERE a.ind_estado = 1
				   AND b.cod_consec ".( $null ? " IS NULL " : " IS NOT NULL " )."
			  ORDER BY b.num_ordenx, a.nom_campox ";
		$consult = new Consulta($sql, $this->conexion );
		return $consult->ret_matrix('a');
	}

	/*! \fn: getFormulFormul
	 *  \brief: Trae el formulario por ID
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: formul   integer  ID
	 *  \return: array
	 */
	private function getFormulFormul($formul) {
		$sql = "SELECT a.nom_formul, b.nom_tablax
				  FROM ".BASE_DATOS.".tab_formul_formul a
			INNER JOIN ".BASE_DATOS.".tab_formul_tablas b ON a.cod_tablas = b.cod_consec
				 WHERE a.cod_consec = $formul ";
		$consult = new Consulta($sql, $this->conexion );
		$result = $consult->ret_matrix('a');

		return $result[0];
	}

	/*! \fn: saveEditFormulFormul
	 *  \brief: Guarda la edicion de un formulario personalizado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function saveEditFormulFormul() {
		$this->updateNombreFormul($_REQUEST['consec'], $_REQUEST['nombre']);

		$i=1;
		foreach ($_REQUEST['campos'] as $campo => $obl) {
			$consec = $this->getConsecFormulCampo($_REQUEST['consec'], $campo);

			if( sizeof($consec) > 0 ){
				$this->updateFormulDetail($consec, $obl, $i);
			} else {
				$this->insertFormulDetail($_REQUEST['consec'], $campo, $obl, $i);
			}

			$i++;
		}

		echo "1";
	}

	/*! \fn: updateNombreFormul
	 *  \brief: Actualiza el nombre del formulario personalizado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: consec  integer  ID del Formulario
	 *  \param: nombre  string   Nombre del formulario
	 *  \return: 
	 */
	private function updateNombreFormul($consec, $nombre) {
		$sql = "UPDATE ".BASE_DATOS.".tab_formul_formul 
				   SET nom_formul = '$nombre',
					   usr_modifi = '".$this->usuario['cod_usuari']."',
					   fec_modifi = NOW()
				 WHERE cod_consec = $consec ";
		new Consulta($sql, $this->conexion );
	}

	/*! \fn: getConsecFormulCampo
	 *  \brief: Trae el consecutivo del de FormulCampos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: formul  integer  ID del Formulario
	 *  \param: campo   integer  ID del Campo
	 *  \return: boolean
	 */
	private function getConsecFormulCampo($formul, $campo) {
		$sql = "SELECT cod_consec
				  FROM ".BASE_DATOS.".tab_formul_detail
				 WHERE cod_formul = $formul
				   AND cod_campox = $campo ";
		$consult = new Consulta($sql, $this->conexion );
		$result = $consult->ret_matrix('i');

		return $result[0][0];
	}

	/*! \fn: updateFormulDetail
	 *  \brief: Actualiza el orden y el indicador de campo obligatorio 
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: consec  integer  ID formulDetail
	 *  \param: obl     integer  1 o 0
	 *  \param: orden   integer  Orden del campo dentro del formulario
	 *  \return: 
	 */
	private function updateFormulDetail($consec, $obl, $orden) {
		$sql = "UPDATE ".BASE_DATOS.".tab_formul_detail
				   SET num_ordenx = $orden,
					   ind_obliga = $obl
				 WHERE cod_consec = $consec ";
		new Consulta($sql, $this->conexion );
	}
}

if($_REQUEST['Ajax'] === 'on' ){
	new ControladorFormularios();
}else{
	new ControladorFormularios( $this->conexion, $this->usuario_aplicacion, $this->codigo );
}

?>
<?php 

/*! \file: ins_formul_campos
 *  \brief: CRUD para los campos de formularios personalizados
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/08/2016
 *  \bug: 
 *  \warning: 
 */

class FormularioCampos 
{
	private $conexion,
			$codAplica,
			$usuario;

	private static $null = array( array('', '-----') );

	private static $typeData = array(
			array('number', 'Numero'),
			array('text', 'Texto'),
			array('alpha', 'Alfanumerico'),
			array('date', 'Fecha'),
			array('hour', 'Hora'),
			array('radio', 'Si/No'),
			array('textarea', 'Area de Texto'),
			array('checkbox', 'Check'),
			array('select', 'Lista'),
			array('file', 'Adjunto'),
			array('camera', 'Camara')
		);

	private static $typeDataLength = array('number', 'text', 'alpha', 'textarea'); #Tipos de datos a los que aplica la min y max de caracteres

	function __construct($co = null, $us = null, $ca = null) {
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
			case 'saveCampo':
				$this->saveCampo();
				break;
			case 'informCamposRegistrados':
				$this->informCamposRegistrados();
				break;
			case 'activarFormulCampo':
				$this->activarFormulCampos();
				break;
			case 'formularioEditarCampo':
				$this->formularioEditarCampos();
				break;
			case 'saveEditCampo':
				$this->saveEditCampo();
				break;
			default:
				$this->formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para registrar nuevos campos de formularios dinamicos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 22/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formulario() {
		$mTD = array("class"=>"cellInfo1", "width"=>"20%");
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

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("id:accFormID; class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>REGISTRAR CAMPO</b></h3>");
			$mHtml->OpenDiv("id:secFormID");
				$mHtml->OpenDiv("id:formID; class:Style2DIV");
					$mHtml->Table('tr');
						$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:left">Definir Campo</th></tr>');

						$mHtml->Label( $mAs."Tipo de Dato: ", $mTD );
						$mHtml->Select2( array_merge(self::$null, self::$typeData), array("name"=>"campo[tipo]", "id"=>"campo_tipo", "width"=>"30%", 
							"class"=>"cellInfo1", "onchange"=>"llenarBoceto($(this));", "validate"=>"select", "obl"=>true) );
						$mHtml->Label( $mAs."Nombre del Campo: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"campo[nombre]", "id"=>"campo_nombre", "size"=>"25", 
							"onkeyup"=>"llenarBoceto($(this));", "obl"=>true, "minlength"=>"3", "maxlength"=>"30", "validate"=>"texto", "end"=>true) );

						$mHtml->Label( "Número minímo de Caracteres: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"campo[min]", "id"=>"campo_min", "size"=>"25",
							"validate"=>"numero", "obl"=>false, "minlength"=>"1", "maxlength"=>"255") );
						$mHtml->Label( "Número maxímo de Caracteres: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"campo[max]", "id"=>"campo_max", "size"=>"25",
							"validate"=>"numero", "obl"=>false, "minlength"=>"1", "maxlength"=>"3") );
					$mHtml->CloseTable('tr');

					$mHtml->SetBody('<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center" id="tabOption" >');
						$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:left">Opciones</th></tr>');
						$mHtml->Label( "Opción: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"opcion", "id"=>"opcionID", "size"=>"25") );
						$mHtml->Button( array("value"=>"Agregar", "class2"=>"cellInfo1", "align"=>"left", "onclick"=>"addOptionFormulCampos();", "colspan"=>"2") );
					$mHtml->CloseTable('tr');

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="2" style="text-align:left">Boceto del Elemento</th></tr>');
						$mHtml->Label( ": ", array_merge($mTD, array("id"=>"boceto_label")) );
						$mHtml->SetBody('<td id="boceto_input" class="cellInfo1">&nbsp;</td></tr>');
						$mHtml->Button( array("value"=>"Guardar", "class2"=>"cellInfo1", "align"=>"center", "onclick"=>"saveFormulCampo();", "colspan"=>"4") );
					$mHtml->CloseTable('tr');

					$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
					$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
					$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		$mHtml->OpenDiv("id:accInfoID; class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>CAMPOS REGISTRADOS</b></h3>");
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
					printInformCamposRegistrados();
				});
			');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: formularioEditarCampos
	 *  \brief: Pinta el formulario para editar campos de formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 24/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formularioEditarCampos() {
		$data = $this->getFormulCampo($_REQUEST['consec']);
		$mTD = array("class"=>"cellInfo1", "width"=>"20%");
		$mAs = '<label style="color: red">* </label>';
		$indLong = 0;

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:secEditID; class:Style2DIV");
			$mHtml->Table('tr');
				$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:left">Definir Campo</th></tr>');

				$mHtml->Label( $mAs."Tipo de Dato: ", $mTD );
				$mHtml->Label( $data['tipo'], array_merge($mTD, array("align"=>"left")) );
				$mHtml->Label( $mAs."Nombre del Campo: ", $mTD );
				$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"campo[nombre]", "id"=>"edit_campo_nombre", "size"=>"25", "value"=>$data['nom_campox'],
					"onkeyup"=>"llenarBoceto($(this));", "obl"=>true, "minlength"=>"3", "maxlength"=>"30", "end"=>true) );

			if( in_array($data['ind_tipoxx'], self::$typeDataLength) ) {
				$mHtml->Label( "Número minímo de Caracteres: ", $mTD );
				$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"campo[min]", "id"=>"edit_campo_min", "size"=>"25",
					"obl"=>false, "minlength"=>"1", "maxlength"=>"255", "value"=>$data['val_minimo']) );
				$mHtml->Label( "Número maxímo de Caracteres: ", $mTD );
				$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"campo[max]", "id"=>"edit_campo_max", "size"=>"25",
					"obl"=>false, "minlength"=>"1", "maxlength"=>"3", "value"=>$data['val_maximo'], "end"=>true) );

				$indLong = 1;
			}

				$mHtml->Button( array("value"=>"Guardar", "class2"=>"cellInfo1", "align"=>"center", "onclick"=>"saveEditFormulCampo( $_REQUEST[consec], $indLong );", "colspan"=>"4") );
				
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getFormulCampo
	 *  \brief: Trae la data de un campo de formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 24/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: consec  integer
	 *  \return: array
	 */
	private function getFormulCampo($consec) {
		$sql = "";
		foreach (self::$typeData as $row) {
			$sql .= "
					 WHEN '$row[0]' THEN '$row[1]' ";
		}

		$sql = "SELECT a.nom_campox, a.ind_tipoxx, a.val_maximo, 
					   a.val_minimo,
					   CASE a.ind_tipoxx $sql
							ELSE 'Sin Definir'
						END AS tipo
				  FROM ".BASE_DATOS.".tab_formul_campos a 
				 WHERE a.cod_consec = $consec ";
		$consult = new Consulta($sql, $this->conexion );
		$result = $consult -> ret_matrix('a');
		return $result[0];
	}

	/*! \fn: saveCampo
	 *  \brief: Guarda el nuevo campo para los formularios dinamicos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 23/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function saveCampo() {
		$id = $this->getIdTabFormulCampos();
		$html = $this->builderHtml($_REQUEST['campo'], $id);
		$this->insertFormulCampos($_REQUEST['campo'], $html);
		echo "1";
	}

	/*! \fn: builderHtml
	 *  \brief: Construye el html según el tipo de dato
	 *  \author: Ing. Fabian Salinas
	 *  \date: 23/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: data  array
	 *  \param: id    integer
	 *  \return: string
	 */
	private function builderHtml($data, $id) {
		$name = 'name="formulcampo['.$id.']" id="formul_campo_'.$id.'" ';

		switch ($data['tipo']) {
            case 'number':
                return '<input '.$name.' class="campo_texto" type="text" minlength="'.$data['min'].'" maxlength="'.$data['max'].'" validate="numero" dataAttr>';
            case 'text':
                return '<input '.$name.' class="campo_texto" type="text" minlength="'.$data['min'].'" maxlength="'.$data['max'].'" validate="texto" dataAttr>';
            case 'alpha':
                return '<input '.$name.' class="campo_texto" type="text" minlength="'.$data['min'].'" maxlength="'.$data['max'].'" validate="alpha" dataAttr>';
            case 'date':
                return '<input '.$name.' class="campo_texto fechapicker" type="text" placeholder="aaaa-mm-dd" validate="date" dataAttr>';
            case 'hour':
                return '<input '.$name.' class="campo_texto horapicker" type="text" placeholder="00:00:00" validate="hora" minlength="0" maxlength="0" dataAttr>';
            case 'radio':
            	$tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                return '<input '.$name.' type="radio" value="Si" dataAttr> Si '.$tab.'<input '.$name.' type="radio" value="No" dataAttr> No';
            case 'textarea':
                return '<textarea '.$name.' class="campo_texto" dataAttr></textarea>';
            case 'checkbox':
                return '<input '.$name.' type="checkbox" value="'.$data['nombre'].'" dataAttr>';
            case 'select':
            	return '<select '.$name.' dataAttr>'.$data['html'].'</select>';
            case 'file':
                return '<input '.$name.' type="file" dataAttr>';
            default:
                return '';
        }
	}

	/*! \fn: getIdTabFormulCampos
	 *  \brief: Trae el Nuevo ID para el registro de los campos de formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 23/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: integer
	 */
	private function getIdTabFormulCampos() {
		$sql = "SELECT MAX(a.cod_consec)
				  FROM ".BASE_DATOS.".tab_formul_campos a ";
		$consult = new Consulta($sql, $this->conexion );
		$result = $consult -> ret_arreglo();

		if( sizeof($result) < 1 ) {
			return 1;
		} else {
			return $result[0] + 1;
		}
	}

	/*! \fn: insertFormulCampos
	 *  \brief: Inserta el nuevo campo
	 *  \author: Ing. Fabian Salinas
	 *  \date: 23/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: data  array   data a insertar
	 *  \param: html  string  HTML del input
	 *  \return: boolean
	 */
	private function insertFormulCampos($data, $html) {
		$sql = "INSERT INTO ".BASE_DATOS.".tab_formul_campos 
					(nom_campox, ind_tipoxx, val_htmlxx, 
					 val_maximo, val_minimo, usr_creaci, 
					 fec_creaci)
				VALUES
					('$data[nombre]', '$data[tipo]', '".utf8_decode($html)."',
					 '$data[max]', '$data[min]', '".$this->usuario['cod_usuari']."', 
					 NOW()) ";
		new Consulta($sql, $this->conexion );

		return mysql_insert_id();
	}

	/*! \fn: informCamposRegistrados
	 *  \brief: Pinta el dinamicList para los campos registrados
	 *  \author: Ing. Fabian Salinas
	 *  \date: 24/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function informCamposRegistrados() {
		$_SESSION["queryXLS"] = $this->construirSqlFormulCamposAll();

		if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }

        $list = new DinamicList($this->conexion, $_SESSION["queryXLS"], "1", "no", 'ASC');
        $list->SetClose('no');

        $list->SetHeader("Nombre Campo", "field:a.nom_campox; width:1%");
        $list->SetHeader("Tipo", "field:a.ind_tipoxx; width:1%", array_merge(self::$null, self::$typeData));
        $list->SetHeader(utf8_decode("Caracteres Mínimos"), "field:a.val_minimo; width:1%");
        $list->SetHeader(utf8_decode("Caracteres Máximos"), "field:a.val_maximo; width:1%");
        $list->SetHeader("Estado", "field:IF(a.ind_estado = 1, 'ACTIVO', 'INACTIVO'); width:1%");
        $list->SetOption("Opciones", "field:ind_estado; width:1%; onclikDisable:activarFormulCampo( this, 0 ); onclikEnable:activarFormulCampo( this, 1 ); onclikEdit:formularioEditarCampo( this )");
        $list->SetHidden("cod_consec", "5");
        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        echo $list->GetHtml();
	}

	/*! \fn: construirSqlFormulCamposAll
	 *  \brief: Construye la query para traer todos los campos de formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 24/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: string
	 */
	private function construirSqlFormulCamposAll() {
		$sql = "";
		foreach (self::$typeData as $row) {
			$sql .= "
					 WHEN '$row[0]' THEN '$row[1]' ";
		}

		return " SELECT a.nom_campox, 
						CASE a.ind_tipoxx $sql
							ELSE 'Sin Definir'
						END AS tipo, 
						a.val_minimo, a.val_maximo, 
						IF(a.ind_estado = 1, 'ACTIVO', 'INACTIVO') AS estado,
						a.cod_consec, a.ind_estado
				   FROM ".BASE_DATOS.".tab_formul_campos a 
				  WHERE 1 ";
	}

	/*! \fn: activarFormulCampos
	 *  \brief: Activa o Desactiva un Campo
	 *  \author: Ing. Fabian Salinas
	 *  \date: 24/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function activarFormulCampos() {
		$sql = "UPDATE ".BASE_DATOS.".tab_formul_campos 
				   SET ind_estado = $_REQUEST[estado],
					   usr_modifi = '".$this->usuario['cod_usuari']."',
					   fec_modifi = NOW()
				 WHERE cod_consec = $_REQUEST[consec] ";
		new Consulta($sql, $this->conexion );

		echo "1";
	}

	/*! \fn: saveEditCampo
	 *  \brief: Guarda la edicion de un campo
	 *  \author: Ing. Fabian Salinas
	 *  \date: 25/08/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function saveEditCampo() {
		$campo = $this->getFormulCampo($_REQUEST['campo']['consec']);
		$data = array_merge($_REQUEST['campo'], array('tipo' => $campo['ind_tipoxx']) );
		$html = $this->builderHtml($data, $data['consec']);
		
		$sql = "UPDATE ".BASE_DATOS.".tab_formul_campos 
				   SET nom_campox = '$data[nombre]',
					   val_htmlxx = '$html',
					   val_minimo = '$data[min]',
					   val_maximo = '$data[max]',
					   usr_modifi = '".$this->usuario['cod_usuari']."',
					   fec_modifi = NOW()
				 WHERE cod_consec = $data[consec] ";
		new Consulta($sql, $this->conexion );

		echo "1";
	}
}

if($_REQUEST['Ajax'] === 'on' ){
	new FormularioCampos();
}else{
	new FormularioCampos( $this->conexion, $this->usuario_aplicacion, $this->codigo );
}

?>
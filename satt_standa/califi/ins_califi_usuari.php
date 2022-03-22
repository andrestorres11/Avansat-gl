<?php
/*! \file: ins_califi_usuari.php
 *  \brief: Formulario para registrar las calificaciones por usuario
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 09/02/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
#setlocale(LC_ALL,"es_ES");

class CalifiUsuari
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cCalifi,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/califi/class_califi_califi.php' );

		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		self::$cCalifi = new Califi($co, $us, $ca);

		switch($_REQUEST['Option']){
			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para registrar las calificaciones por usuario
	 *  \author: Ing. Fabian Salinas
	 *  \date:  09/02/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formulario(){
		$mUsuari = self::$cCalifi->getUsuari(' AND a.cod_perfil IN (7,8,713) ');
		$mOperac = self::$cCalifi->getOperacion();
		$x = "<font style='color:red'>*</font>";

		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'par_califi_califi.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

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
							$mHtml->Label( "<b>Agregar Actividad a Evaluar</b>", array("class"=>"CellHead", "colspan"=>"6", "align"=>"left", "end"=>true) );

							$mHtml->Label( "$x Usuarios: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Select2( array_merge(self::$cNull, $mUsuari), array("name"=>"cod_consec", "id"=>"cod_consecID", "class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Label( "$x Tipo de Operaci&oacute;n: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Select2( array_merge(self::$cNull, $mOperac), array("name"=>"cod_operac", "id"=>"cod_operacID", "obl"=>"1", "validate"=>"select", "class"=>"cellInfo1", "width"=>"16.6%", "onchange"=>"getListActivi($(this));") );
							$mHtml->Label( "$x Actividad: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Select2( self::$cNull, array("name"=>"cod_activi", "id"=>"cod_activiID", "obl"=>"1", "validate"=>"select", "class"=>"cellInfo1", "width"=>"16.6%", "end"=>true) );

							$mHtml->Button( array("value"=>" Auditar ", "onclick"=>"tableCalifiUsuari()", "colspan"=>"6", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", "class2"=>"cellInfo1", "align"=>"center", "end"=>true) );

							$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
							$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
							$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->CloseTable('tr');
					$mHtml->SetBody('</form>');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Acordion 2
		$mHtml->OpenDiv("class:accordion; id:divCalifiID");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>AUDITORIA</b></h3>");
			$mHtml->OpenDiv("id:secCalifiID");
				$mHtml->OpenDiv("id:formCalifiID; class:Style2DIV");
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		$mHtml->Javascript('$("body").ready(function() {
								//Acordion
								$(".accordion").accordion({
									heightStyle: "content",
									collapsible: true
								});

								$("#cod_consecID").multiselect().multiselectfilter();
							});
						  ');

		echo $mHtml->MakeHtml();
	}
}

$_INFORM = new CalifiUsuari( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );	

?>
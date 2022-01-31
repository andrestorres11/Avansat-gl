<?php

class InfCalifiDesUsu
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cDespac,
					$cCalifi,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null){
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/califi/class_califi_califi.php' );

		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		self::$cDespac = new Despac( $co, $us, $ca );
		self::$cCalifi = new Califi( $co, $us, $ca );

		switch($_REQUEST['Option']){
			default:
				self::formulario();
				break;
		}
	}

	private function formulario(){
		$mTipoDespac = self::$cDespac -> getTipoDespac();
		$mTransp = self::$cDespac -> getTransp();
		$mOperac = self::$cCalifi -> getOperacion();
		$mView = self::$cCalifi -> getPermisosRespon();
		$x = "<font style='color:red'>*</font>";
		$mTD = array("class"=>"cellInfo1", "width"=>"25%");

		if( sizeof($mTransp) != 1 ){
			$mTransp = array_merge(self::$cNull, $mTransp);
			$mDisabl = false;
		}else
			$mDisabl = true;

		if( $mView->sec_inform->sub->pes_usuari == 1 )
			$mUsuariCalifi = self::$cCalifi -> getUsuariCalif('usr_califi');
		else
			$mUsuariCalifi = false;

		if( $mView->fil_genera->sub->usu_regist == 1 ){
			$mUsuariCreaci = self::$cCalifi -> getUsuariCalif('usr_creaci');
			$mUsuari = array_merge(self::$cNull, $mUsuariCreaci);
		}
		else{
			$mUsuari[0] = array($_SESSION['datos_usuario']['cod_usuari'],$_SESSION['datos_usuario']['cod_usuari']);
		}


		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'inf_califi_desusu.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

		#Pinta HTML
		$mHtml = new Formlib(2);

		$mHtml->setBody('<style>
							.bgTD1 {
								background-color: #DBF9C5;
							}
							.bgTD2 {
								background-color: #DCE2D7;
							}
							.bgTD3 {
								background-color: #EBF8E2;
							}
							.bgTD4 {
								background-color: #E3F7D4;
							}
							.bgTD5 {
								background-color: #E8E3E3;
							}
							.bgTD6 {
								background-color: #EDE8E8;
							}
							.TD {
								font-family: Trebuchet MS,Verdana,Arial;
								font-size: 11px;
								padding: 2px;
							}
						</style>');

		$mHtml->SetCss("jquery");
		$mHtml->SetCss("informes");
		$mHtml->SetCss("validator");

		$mHtml->CloseTable('tr');

		#Acordion
		if( $mView->fil_genera->ind_visibl == 1 ){
			$mHtml->OpenDiv("class:accordion");
				$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
				$mHtml->OpenDiv("id:secID");
					$mHtml->SetBody('<form name="form_InfCalifiDesUsu" id="form_InfCalifiDesUsuID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
					$mHtml->OpenDiv("id:formID; class:Style2DIV");

					if( $mView->fil_genera->sub->tip_despac == 1 && $mView->sec_inform->sub->pes_despac == 1 ){
						$mHtml->Table('tr');

							$mHtml->SetBody('<th class="CellHead" colspan="8" style="text-align:left">Tipo de Despacho</th></tr>');
							$i=0;
							foreach ($mTipoDespac as $row){
								if( $i==0 || ( $i % 4 == 0) )
									$mHtml->SetBody('<tr>');
								
								$mHtml->Label( $row[1], array("class"=>"cellInfo1", "width"=>"12.5%", "align"=>"right") );
								$mHtml->CheckBox( array("class"=>"cellInfo1", "width"=>"12.5%", "name"=>"cod_tipdes$row[0]", "value"=>$row[0]) );

								if( ($i+1) == sizeof($mTipoDespac) || ( ($i+1) % 4 == 0) )
									$mHtml->SetBody('</tr>');
								$i++;
							}
						$mHtml->SetBody('</table>');
					}

						$mHtml->Table('tr');
							$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Otros Filtros</th></tr>');

							$mHtml->Label( "&nbsp;", $mTD );
							$mHtml->Label( "Transportadoras: ", $mTD );
							$mHtml->Select2( $mTransp, array_merge($mTD, array("name"=>"cod_transp", "id"=>"cod_transpID", "class"=>"cellInfo1 multiSel")) );
							$mHtml->Label( "&nbsp;", array_merge($mTD, array("end"=>true)) );

							$mHtml->Label( "Usuarios Calificadores: ", $mTD );
							$mHtml->Select2( $mUsuari, array_merge($mTD, array("name"=>"usr_creaci", "id"=>"usr_creaciID", "class"=>"cellInfo1 multiSel")) );

						if( $mView->sec_inform->sub->pes_usuari == 1 ){
							$mHtml->Label( "Usuarios Calificados: ", $mTD );
							$mHtml->Select2( array_merge(self::$cNull, $mUsuariCalifi), array_merge($mTD, array("name"=>"usr_califi", "id"=>"usr_califiID", "class"=>"cellInfo1 multiSel", "end"=>true)) );
						}else{
							$mHtml->Label( "&nbsp;", array("class"=>"cellInfo1", "width"=>"50%", "colspan"=>"2", "end"=>true) );
						}

							$mHtml->Label( "Tipo de Operaci&oacute;n: ", $mTD );
							$mHtml->Select2( array_merge(self::$cNull, $mOperac), array_merge($mTD, array("name"=>"tip_operac", "id"=>"tip_operacID", "onchange"=>"getListActivi($(this));", "obl"=>"1", "validate"=>"select")) );
							$mHtml->Label( "Actividad: ", $mTD );
							$mHtml->Select2( self::$cNull, array_merge($mTD, array("name"=>"cod_activi", "id"=>"cod_activiID", "class"=>"cellInfo1 multiSel", "obl"=>"1", "validate"=>"select", "end"=>true)) );

							$mHtml->Label( "Fecha Inicial: ", $mTD );
							$mHtml->Input( array_merge($mTD, array("name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date")) );
							$mHtml->Label( "Fecha Final: ", $mTD );
							$mHtml->Input( array_merge($mTD, array("name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date")) );
						$mHtml->CloseTable('tr');

					if( $mView->sec_inform->sub->pes_despac == 1 ){
						$mHtml->Table('tr');
							$mHtml->SetBody('<tr><th class="CellHead" colspan="6" style="text-align:left">Filtros Especificos</th></tr>');

							$mHtml->Label( "Despacho: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Input( array("name"=>"num_despac", "id"=>"num_despacID", "class"=>"cellInfo1", "width"=>"16.6%", "size"=>"10", "minlength"=>"6", "maxlength"=>"10", "validate"=>"numero") );
							$mHtml->Label( "Placa: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Input( array("name"=>"num_placax", "id"=>"num_placaxID", "class"=>"cellInfo1", "width"=>"16.6%", "size"=>"6", "validate"=>"placa") );
							$mHtml->Label( "Pedido: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
							$mHtml->Input( array("name"=>"num_pedido", "id"=>"num_pedidoID", "class"=>"cellInfo1", "width"=>"16.6%", "size"=>"10", "maxlength"=>"15", "end"=>true) );
						$mHtml->CloseTable('tr');
					}

							$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
							$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
							$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
							$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_CrossDoking_".date('Y-m-d_H:i') ) );
							$mHtml->Hidden( array("name"=>"OptionExcel", "id"=>"OptionExcelID", "value"=>"_REQUEST") );
							$mHtml->Hidden( array("name"=>"exportExcel", "id"=>"exportExcelID", "value"=>"") );
					$mHtml->CloseDiv();
					$mHtml->SetBody('</form>');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		}

		if( $mView->sec_inform->ind_visibl == 1 ){
			#Tabs
			$mHtml->OpenDiv("id:tabs");
				$mHtml->SetBody('<ul>');

				if( $mView->sec_inform->sub->pes_despac == 1 )
					$mHtml->SetBody('<li><a id="liDespac" href="#tabs-despac" style="cursor:pointer" onclick="report(\'despac\', \'tabs-despac\')">DESPACHOS</a></li>');

				if( $mView->sec_inform->sub->pes_usuari == 1 )
					$mHtml->SetBody('<li><a id="liUsuari" href="#tabs-usuari" style="cursor:pointer" onclick="report(\'usuari\', \'tabs-usuari\')">USUARIOS</a></li>');

				$mHtml->SetBody('</ul>');

				if( $mView->sec_inform->sub->pes_despac == 1 )
					$mHtml->SetBody('<div id="tabs-despac"></div>'); #DIV DESPACHOS

				if( $mView->sec_inform->sub->pes_usuari == 1 )
					$mHtml->SetBody('<div id="tabs-usuari"></div>'); #DIV USUARIOS

			$mHtml->CloseDiv();
		}

		echo $mHtml->MakeHtml();
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfCalifiDesUsu();
else
	$_INFORM = new InfCalifiDesUsu( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
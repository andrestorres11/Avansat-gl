<?php
/*! \file: inf_crossx_doking.php
 *  \brief: Arma el formulario para el informe Cross Doking
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 07/10/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');

/*! \class: InfCrossDoking
 *  \brief: Arma el formulario para el informe Cross Doking
 */
class InfCrossDoking
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cCrossDoking,
					$cCallce,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/ClassCrossDoking.php' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_gerenc_callce.php' );

		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.table2excel.js' );
		IncludeJS( 'inf_crossx_doking.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'jquery.multiselect.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/validator.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		self::$cCrossDoking = new ClassCrossDoking($co, $us, $ca);
		self::$cCallce = new CallCe($co, $us, $ca);

		switch($_REQUEST['Option'])
		{
			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario Informe Cross Doking
	 *  \author: Ing. Fabian Salinas
	 *  \date:  07/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formulario()
	{
		$mTipTra = self::$cCallce -> getTipoTransp();
		$mTransp = self::$cCallce -> getTransp();
		$mProduc = self::$cCrossDoking -> getProduc();
		$mTipDes = self::$cCrossDoking -> getTipoDespac();
		$mTD = "cellInfo1";
		$mScript = '';

		foreach ($mTipDes as $row)
		{
			$mID = str_replace(' ', '_', $row[1]);
			$mHtml1 .= '<li class="ui-state-default ui-corner-top"><a id="'.$mID.'ID" href="#tabs-'.$row[0].'">'.$row[1].'</a></li>'; #Pestaña Tipo de despacho
			$mHtml2 .= '<div id="tabs-'.$row[0].'"></div>'; #DIV Tipo de despacho

			$mScript .= ' $("#'.$mID.'ID").click(function(){
							report( "'.$row[0].'", "tabs-'.$row[0].'" );
						  }); ';
		}

		if( sizeof($mTransp) != 1 ){
			$mTransp = array();
			$mDisabl = false;
		}else
			$mDisabl = true;

		#Pinta HTML
		$mHtml = new Formlib(2);

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("id:accordionID");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->SetBody('<form id="form_CrossDokingID" name="form_CrossDoking" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");
					$mHtml->Table('tr');
						$mHtml->Label( "<b>Seleccione Par&aacute;metros de B&uacute;squeda</b>", array("class"=>"CellHead", "colspan"=>"6", "align"=>"left", "end"=>true) );

						$mHtml->Label( "* Transportadora: ", array("class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"nom_transp", "id"=>"nom_transpID", "value"=>$mTransp[0]['nom_tercer'], "size"=>"35", "class"=>$mTD, "width"=>"16.6%", "disabled"=>$mDisabl) );
						$mHtml->Label( "Centro de Distribuci&oacute;n: ", array("class"=>$mTD, "width"=>"16.6%") );
						$mHtml->SetBody('<td id="ciudadTD" class="'.$mTD.'" align="left" width="16.6%"></td>');
						$mHtml->Label( "Tipo de Transporte: ", array("class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Select2( array_merge(self::$cNull, $mTipTra), array("name"=>"cod_tiptra", "id"=>"cod_tiptraID", "class"=>$mTD, "width"=>"16.6%", "end"=>true) );

						$mHtml->Label( "No. Despacho: ", array("class"=>$mTD) );
						$mHtml->Input( array("name"=>"num_despac", "id"=>"num_despacID", "class"=>$mTD) );
						$mHtml->Label( "No. Manifiesto: ", array("class"=>$mTD) );
						$mHtml->Input( array("name"=>"num_manifi", "id"=>"num_manifiID", "class"=>$mTD) );
						$mHtml->Label( "No. Viaje: ", array("class"=>$mTD) );
						$mHtml->Input( array("name"=>"num_viajex", "id"=>"num_viajexID", "class"=>$mTD, "end"=>true) );

						$mHtml->Label( "Placa: ", array("class"=>$mTD) );
						$mHtml->Input( array("name"=>"num_placax", "id"=>"num_placaxID", "size"=>"6", "maxlength"=>"6", "class"=>$mTD) );
						$mHtml->Label( "Fecha Inicial: ", array("class"=>$mTD) );
						$mHtml->Input( array("name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10", "maxlength"=>"10", "class"=>$mTD) );
						$mHtml->Label( "Fecha Final: ", array("class"=>$mTD) );
						$mHtml->Input( array("name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10", "maxlength"=>"10", "class"=>$mTD, "end"=>true) );

						$mHtml->Label( "Producto:", array("class"=>$mTD) );
						$mHtml->Select2( array_merge(self::$cNull, $mProduc), array("name"=>"cod_produc", "id"=>"cod_producID", "class"=>$mTD) );
						$mHtml->Label( "&nbsp;", array("class"=>"cellInfo1", "colspan"=>"4", "end"=>true) );

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"cod_transp", "id"=>"cod_transpID", "value"=>$mTransp[0]['cod_tercer']) );
						$mHtml->Hidden( array("name"=>"ind_filact", "id"=>"ind_filactID", "value"=>"") );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_CrossDoking_".date('Y-m-d_H:i:00') ) );
						$mHtml->Hidden( array("name"=>"OptionExcel", "id"=>"OptionExcelID", "value"=>"_REQUEST") );
						$mHtml->Hidden( array("name"=>"exportExcel", "id"=>"exportExcelID", "value"=>"") );
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Tabs
		$mHtml->OpenDiv("id:tabs");
			$mHtml->SetBody('<ul>');
				$mHtml->SetBody('<li><a id="liGenera" href="#tabs-g" style="cursor:pointer">GENERAL</a></li>');
				$mHtml->SetBody('<li><a id="liCrossD" href="#tabs-0" style="cursor:pointer">CROSS DOKING</a></li>');
				$mHtml->SetBody( $mHtml1 );
			$mHtml->SetBody('</ul>');

			$mHtml->SetBody('<div id="tabs-g"></div>'); #DIV GENERAL
			$mHtml->SetBody('<div id="tabs-0"></div>'); #DIV CROSS DOKING
			$mHtml->SetBody( $mHtml2 );
		$mHtml->CloseDiv();

		$mHtml->SetBody( '<script> '.$mScript.' </script>' );

		echo $mHtml->MakeHtml();

		#Si el usuario pertenece a una transportadora carga la lista de ciudades (Centros de distribucion)
		if( $mDisabl == true )
			echo "<script> centroDistri(); </script>";
	}
}

$_INFORM = new InfCrossDoking( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
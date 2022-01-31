<?php  
/*! \file: inf_despac_finali.php
 *  \brief: Informe Despachos Finalizados (Informes > Operacion trafico > Finalizados)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 2.0
 *  \date: 22/04/2016
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

class infDespacFinali
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cDespac,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion  = $AjaxConnection;
			self::$cUsuario   = $_SESSION['datos_usuario'];
			self::$cCodAplica = $_SESSION['codigo'];
		}else{
			@include_once( '../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php' );

			self::$cDespac = new Despac( $co, $us, $ca );
			self::$cConexion  = $co;
			self::$cUsuario   = $us;
			self::$cCodAplica = $ca;
		}

		@include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );

		switch($_REQUEST['Option']){
			case 'inform':
				self::inform();
				break;

			case 'getConduc':
				self::getConduc();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para aplicar los filtros al informe
	 *  \author: Ing. Fabian Salinas
	 *  \date: 22/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:    
	 *  \return: 
	 */
	private function formulario(){
		$mTD = array("class"=>"cellInfo1", "width"=>"20%");
		$mAs = '<label style="color: red">* </label>';
		$mTransp = self::$cDespac -> getTransp();
		$mConduc = self::getConduc();

		if( sizeof($mTransp) != 1 ){
			$mTransp = array_merge(self::$cNull, $mTransp);
		}
		

		$mHtml = new Formlib(2);


		//IncludeJS( 'jquery.js' );
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/jquery-3.3.1.js'></script>");
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/jquery-ui.js'></script>");
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/jquery.dataTables.min.js'></script>");


		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'validator.js' );
		
		//Data Table
		IncludeJS( 'inf_usuari_appxxx.js' );
		$mHtml->SetBody("<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/js/DataTables/css/jquery.dataTables.min.css' type='text/css'>\n");
		$mHtml->SetCss("jquery");
		$mHtml->SetCss("informes");
		$mHtml->SetCss("validator");

		//Data Table
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/dataTables.buttons.min.js'></script>");
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/buttons.html5.min.js'></script>"); //Copy Csv
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/jszip.min.js'></script>"); //Excel
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/pdfmake.min.js'></script>");//PDF
		$mHtml->SetBody("<script src='../".DIR_APLICA_CENTRAL."/js/DataTables/js/vfs_fonts.js'></script>");//PDF
		

		$mHtml->CloseTable('tr');

		#Acordion
		/*$mHtml->OpenDiv("id:TransparencyDIV");
		$mHtml->CloseDiv("");*/
		$mHtml->OpenDiv("class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->SetBody('<form name="form_InfUsuariAppxxx" id="form_InfUsuariAppxxxID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Generales</th></tr>');

						$mHtml->Label( "Fecha Inicial: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"fec_inicia", "size"=>"10") );
						$mHtml->Label( "Conductor: ", $mTD );
						$mHtml->Select2( $mConduc, array("name"=>"cod_tercer", "width"=>"30%", "class"=>"cellInfo1", "end" => true) );

						$mHtml->Label( "Fecha Final: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"fec_finali", "size"=>"10") );
						$mHtml->Label( "Transportadora: ", $mTD );
						$mHtml->Select2( $mTransp, array("name"=>"nit_transp", "width"=>"30%", "class"=>"cellInfo1", "end" => true) );


						$mHtml->Label( "APP Activa Sin Registro: ", $mTD );
						$mHtml->Radio( array("class"=>"cellInfo1", "name" => "tipoInforme", "id"=>"tipoInforme1ID", "size"=>"10", "value"=>"usuariosSinRegistros", "align" => "left"));
						$mHtml->Label( "APP Activa Con Registro: ", $mTD );
						$mHtml->Radio( array("class"=>"cellInfo1", "name" => "tipoInforme", "id"=>"tipoInforme2ID", "size"=>"10", "value"=>"usuariosConRegistros", "align" => "left", "end" => true));

						$mHtml->Label( "APP Inactiva: ", $mTD );
						$mHtml->Radio( array("class"=>"cellInfo1", "name" => "tipoInforme", "id"=>"tipoInforme3ID", "size"=>"10", "value"=>"usuariosInactivos", "align" => "left"));
						$mHtml->Label( "", $mTD );
						$mHtml->Label( "", $mTD );

					$mHtml->CloseTable('tr');

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Tabs
		$mHtml->OpenDiv("id:tabs");
			$mHtml->SetBody('<ul>');
				$mHtml->SetBody('<li><a id="liReport" href="#tabs-report" style="cursor:pointer" onclick="report(\'form_InfUsuariAppxxxID\')">REPORTE</a></li>');
			$mHtml->SetBody('</ul>');

			$mHtml->SetBody('<div id="tabs-report" class="Style2DIV" style="width: calc(100% - 50px)!important;margin: 10px;"><table id="dataTableReport" class="display" style="width:100%"><thead></thead><tbody></tbody></table></div>'); #DIV REPORTE
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getConduc
	 *  \brief: Trae los conductores activos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 29/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getConduc(){
		$mSql = "SELECT a.cod_tercer AS codex, 
						UPPER(a.abr_tercer) AS value, 
						CONCAT(a.cod_tercer, ' - ', UPPER(a.abr_tercer)) AS label 
				   FROM ".BASE_DATOS.".tab_tercer_tercer a 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_activi b 
					 ON a.cod_tercer = b.cod_tercer 
				  WHERE b.cod_activi = ".COD_FILTRO_CONDUC." 
					AND a.cod_estado = ".COD_ESTADO_ACTIVO." 
			".( !$_REQUEST['term'] ? "" : " AND (a.abr_tercer LIKE '%$_REQUEST[term]%' OR a.cod_tercer LIKE '$_REQUEST[term]%') " )."
			   ORDER BY a.abr_tercer ASC ";
		$mSql .= !$_REQUEST['term'] ? "" : " LIMIT 30 ";

		$mConsult = new Consulta($mSql, self::$cConexion );
		$result = $mConsult -> ret_matrix('i');
		array_push($result, "");
		sort($result);
		return $result;
	}

	private function inform(){

		$cHeader = array("Token: TkOET_EAL","Auth: $2dIMJMZQcHLY",
                           "Authorization: e14804819d57fc7497bb747204ce337b", 
                           "usuario: *WidetechInt3grador*", 
                           "clave: lxdG-+gJX:oYju+b5n"
                          );
          
		# Recorre las variables para concatenarlas en un solo string como si fuera un GET para enviarla por cUrl------------------------
		//$mParamsString = "inputAvansat=".json_encode($sendData);
		$mParamsString = json_encode($_REQUEST["data"]);
		
		# Inicio de cURL para la API -----------------------------------------------------------------------------
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://central.intrared.net/ap/interf/apispg/V3/controlador/AplicacionControlador.php" ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
		curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$raw_data = curl_exec($ch);
		$error = curl_error($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//Convert json to array
		$data = json_decode($raw_data, true);

		//Create necessary variables
		$arrayQuery = array();
		$arrayData = array();
		$structure = array(
			"Id" => 0,
			"Usuario" => "",
			"Nombre del Conductor" => "",
			"Correo Electronico" => "",
			"Celular" => "",
			"Tipo Transporte" => "",
			"Cantidad Registros" => 0
		);
		
		//Go through data
		foreach ($data["data"] as $key => $value) {

			//Fill array data
			$arrayData[$key] = $structure;
			$arrayData[$key]["Id"] = $key + 1;
			$arrayData[$key]["Cantidad Registros"] = $value["num_regist"];

			//Create IN segment
			if($key == 0){
				$in = "'" . $value["cod_tercer"] . "'";
			}else{
				$in .= ", '" . $value["cod_tercer"] . "'";
			}
		}

		//Create query
		$query = "
			SELECT
				DISTINCT
					a.cod_tercer AS `Key`,
					c.cod_usuari AS `Usuario`,
					IF(
						a.abr_tercer IS NOT NULL,
						a.abr_tercer,
						CONCAT(a.nom_tercer, ' ', a.nom_apell1, ' ', a.nom_apell2)
					) AS `Nombre del Conductor`,
					a.dir_emailx AS `Correo Electronico`,
					a.num_telmov AS `Celular`,
					IF(
						b.tip_transp = '1',
						'Propio',
						IF(
							b.tip_transp = '3',
							'Empresa',
							'Tercero'
						)
					) AS `Tipo Transporte`
			FROM
				".BASE_DATOS.".tab_tercer_tercer a 
				INNER JOIN ".BASE_DATOS.".tab_despac_corona b ON b.cod_conduc = a.cod_tercer
				INNER JOIN ".BASE_DATOS.".tab_usuari_movilx c ON c.cod_tercer = a.cod_tercer
			WHERE
				a.cod_tercer IN ($in)
		";

		//Execute query 
		$query = new Consulta($query, self::$cConexion);
		$rows = $query -> ret_matrix('a');

		//Transform arrays
		foreach ($rows as $key => $value) {
			
			//Create position
			$arrayQuery[$value["Key"]] = array();

			//Go through fields
			foreach ($value as $key1 => $value1) {
				
				//Validate key
				if($key1 != "Key"){

					//Create value
					$arrayQuery[$value["Key"]][$key1] = utf8_encode($value1);
				}
			}
		}
		
		//Go through data
		foreach ($data["data"] as $key => $value) {

			//Validate exist data
			if(isset($arrayQuery[$value["cod_tercer"]])){

				//Go through aditional data
				foreach ($arrayQuery[$value["cod_tercer"]] as $key1 => $value1) {

					//Assign values
					$arrayData[$key][$key1] = $value1;
				}

			}
		}
		
		//Return data
		echo json_encode($arrayData);

	}
}

if( $_REQUEST['Ajax'] === 'on' )
	$_INFORM = new infDespacFinali();
else
	$_INFORM = new infDespacFinali( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );


?>
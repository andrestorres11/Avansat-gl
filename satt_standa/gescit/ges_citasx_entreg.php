<?php
/*! \file: GestionCitasEntrega.php
 *  \brief: clase controladora de las citas de wntrega que fueron importadas al sistema
 *  \author: Ing. Nelson Liberato
 *  \author: nelson.liberato@eltransporte.org
 *  \version: 1.0
 *  \date: 05/02/2019
 *  \bug: 
 *  \warning1:  Para realizar el filtro por Despachos limpios o no limpios actualmente no se realiza con el ind_limpio de tab_despac_despac 
 *				ya que no esta siendo actualizado correctamente en InsertNovedad.inc 
 *				Para cuando este bug se corriga en InsertNovedad.inc Descomentariar lineas de las querys que traen los despachos.
 *				Buscar #warning1 para ubicar las lineas afectadas
 *  \warning2:	InsertNovedad.inc no recalcula bien el tiempo de alarma, para disfrazar este bug se opto por validar si la fecha de la
 *				ultima novedad es mayor a la fecha de alarma, si esto es verdadero se recalcula el tiempo de alarma según el tipo de 
 *				novedad; si la novedad solicita tiempo se recalcula con el tiempo dado, si no se recalcula con el tiempo del array de 
 *				clase $cTime.
 *				Buscar #warning2 para ubicar las lineas afectadas
 */ 
date_default_timezone_get('America/Bogota');

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*! \class: Despac
 *  \brief: Clase que realiza las consultas para retornar la información de los Despachos en Cargue, Transito o Descargue
 */
class GestionCitasEntrega
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cTypeUser,
					$cControlador,
					$cTipDespac = '""',
					$cTipDespacContro = '""', #Tipo de Despachos asignados al controlador, Aplica para cTypeUser[tip_perfil] == 'CONTROL'
					$cNull = array( array('', '-----') ), 
					$cTime = array( 'ind_desurb' => '30', 'ind_desnac' => '60' ),
					$cSession; #warning2
	private static  $cFileTypes  = [ 
										'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',     
										'application/vnd.ms-excel',
										'application/msexcel',
										'application/x-msexcel',
										'application/x-ms-excel',
										'application/x-excel',
										'application/x-dos_ms_excel',
										'application/xls',
										'application/x-xls',
										'text/csv'
									];
	private static  $cCodTipdes  = 	[
										'URBANO'   				=> ['cod_tipdes' => '1', 'nom_tipdes' => 'URBANO' ],
										'NACIONAL' 				=> ['cod_tipdes' => '2', 'nom_tipdes' => 'NACIONAL' ],
										'CROSSDOCKING TRAMO 1' 	=> ['cod_tipdes' => '5', 'nom_tipdes' => 'XD Tramo 1' ],
										'CROSSDOCKING TRAMO 2' 	=> ['cod_tipdes' => '6', 'nom_tipdes' => 'XD Tramo 2' ],
									];	

	private static  $cEstSolici  = 	[
										'1' => 'Confirmada',
										'0' => 'Pendiente' 
									];
	private static  $cIndCumple  = 	[
										'SI'   					=> ['ind_cumple' => '1'  ],
										'NO' 					=> ['ind_cumple' => '0'  ]
									];	

	private static  $cCodNegoci  = 	[
										'BAÑOS y COCINAS'	=> ['cod_negoci' => 'BC'  ],
										'SUMICOl' 			=> ['cod_negoci' => 'SM'  ],
										'REVESTIMIENTO' 	=> ['cod_negoci' => 'RT'  ],
										'CORLANC' 			=> ['cod_negoci' => 'CN'  ]
									];

	public function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cCodAplica = $ca;
			self::$cUsuario = $us;
		}
		self::$cSession = $_SESSION["datos_usuario"]; 

		 
		switch($_REQUEST['Option'])
		{
			case 'loadTabInfo':
				self::loadTabInfo();
			break;
			case 'loadFormGestionar':
				self::loadFormGestionar();
			break;			
			case 'registrarGestion':
				self::registrarGestion();
			break;			
			case 'getInfoCausas':
				self::getInfoCausas();
			break;			
			case 'GetLastNovedad':
				self::getLastNovedad();
			break;

			default:
				self::formInput();
				break;
		}
				//header('Location: index.php?window=central&cod_servic=1366&menant=1366');
		
	}

 

	/*! \fn: formInput
	 *  \brief: Formulario inicial
	 *  \author: Ing. Nelson Liberato
	 *	\date: 05/02/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function formInput()
	{
		try 
		{
			// echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/min.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/jquery.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/config.js'></script>\n"; 
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/functions.js'></script>\n"; 
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js'></script>\n";

			// INclución de js y css de jquery table
			// echo "<script type='text/javascript' language='javascript' src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js'></script>\n";
			// echo "<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'>\n";
			 echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>\n";

			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/ges_citasx_entreg.js'></script>\n";


			$mHtml = new FormLib(2);

			$mHtml->SetCss("dinamic_list");
			$mHtml->CloseTable("tr");
			# incluye Css
			$mHtml->SetCssJq("jquery");
			$mHtml->Body(array("menubar" => "no"));

			 
        

			#creo el acordeon para el filtro
			#<DIV fitro>
       		#abre formulario
	       	$mHtml->Form(array("action" => "index.php",
	            "method" => "post",
	            "name" => "form_areas",
	            "header" => "Importación de archivo",
	            "enctype" => "multipart/form-data")); 

		        $mHtml->Row("td");
			      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
			      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
				        	$mHtml->SetBody("<h2 style='padding:6px;'><b>Filtros generales</b></h2>");
								$mHtml->OpenDiv("id:sec2"); #Div3
									$mHtml->Table('tr');
										 
										$mQuery = "SELECT  DATE_SUB(DATE(NOW()), INTERVAL 5 DAY ) AS fec_inixxx,  DATE(NOW()) AS fec_finxxx ";
										$mConsult = new Consulta( $mQuery, self::$cConexion );
										$mFechas = $mConsult -> ret_matrix('a');
										$mFechas = $mFechas[0]; 

										$mHtml->Label("Nº solicitud: ", array('for' =>'num_soliciID', 'width' => '15%') );
										$mHtml->Input( array('name' =>'num_solici', 'width' => '15%', 'align' => 'left') );
										$mHtml->Label("Nº Viaje: ", array('for' =>'num_viajexID', 'width' => '15%') );
										$mHtml->Input( array('name' =>'num_viajex', 'width' => '15%', 'align' => 'left', 'end'=> 'yes') );										


										$mHtml->Label("Estado solicitud: ", array('for' =>'est_soliciID', 'width' => '15%') );
										$mHtml->Select( [ ['', '---'], ['0', 'Pendiente'], ['1', 'Conformada']  ], array('name' =>'est_solici', 'width' => '15%', 'align' => 'left' ) );
										$mHtml->Label("Tipo operación: ", array('for' =>'cod_tipopeID', 'width' => '15%') );
										$mHtml->Select( self::loadFiltrosSelect('Operacion'), array('name' =>'cod_tipope', 'width' => '15%', 'align' => 'left', 'end'=> 'yes') );

										$mHtml->Label("Origen: ", array('for' =>'cod_ciuoriID', 'width' => '15%') );
										$mHtml->Select( self::loadFiltrosSelect('Origen'), array('name' =>'cod_ciuori', 'width' => '15%', 'align' => 'left' ) );
										$mHtml->Label("Destino: ", array('for' =>'cod_ciudesID', 'width' => '15%') );
										$mHtml->Select( self::loadFiltrosSelect('Destino'), array('name' =>'cod_ciudes', 'width' => '15%', 'align' => 'left', 'end'=> 'yes') );


										$mHtml->Label("Fecha Inicio:", array('for' =>'fec_inixxxID', 'width' => '15%') );
										$mHtml->Input( array('name' =>'fec_inixxx', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mFechas['fec_inixxx']) );
										$mHtml->Label("Fecha Fin:", array('for' =>'fec_finxxxID', 'width' => '15%') );
										$mHtml->Input( array('name' =>'fec_finxxx', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mFechas['fec_finxxx'], 'end'=> 'yes') );										

								

										//$mHtml->Button(array('name' =>'subir', 'value'=>'Importar', 'colspan' => '4', 'align' => 'center', 'onclick' => 'ValidaImportar()'));


									$mHtml->CloseTable('tr');
								$mHtml->CloseDiv();	 #Div3

						$mHtml->CloseDiv();	 #Div2
			      	$mHtml->CloseDiv();	#Div1
			    $mHtml->CloseRow("td");

		        $mHtml->Row("td");
			      	$mHtml->OpenDiv("id:tabs; "); #Div1
						$mHtml->SetBody("<ul>");
							$mHtml->SetBody("<li><a href='#tabs-1' onClick='getDataTab(\"1\", 1)'>Citas pendientes</a></li>");
							$mHtml->SetBody("<li><a href='#tabs-2' onClick='getDataTab(\"2\")'>Citas cumplidas</a></li>");
							$mHtml->SetBody("<li><a href='#tabs-3' onClick='getDataTab(\"3\")'>Citas NO cumplidas</a></li>");
							//$mHtml->SetBody("<li><a href='#tabs-4' onClick='getDataTab(\"4\")'>Generar reprogramación</a></li>");
						$mHtml->SetBody("</ul>");			      		 
						$mHtml->OpenDiv("id:tabs-1; "); #tab-1
							$mHtml->SetBody("Contenido de tab 1");
						$mHtml->CloseDiv(); #tab-1						
						$mHtml->OpenDiv("id:tabs-2; "); #tab-2
							$mHtml->SetBody("Contenido de tab 2");
						$mHtml->CloseDiv(); #tab-2					
						$mHtml->OpenDiv("id:tabs-3; "); #tab-3
							$mHtml->SetBody("Contenido de tab 3");
						$mHtml->CloseDiv(); #tab-3					
						//$mHtml->OpenDiv("id:tabs-4; "); #tab-4
						//	$mHtml->SetBody("Contenido de tab 4");
						//$mHtml->CloseDiv(); #tab-4
			      	$mHtml->CloseDiv();	#Div1
			    $mHtml->CloseRow("td");

			    $mHtml->Row("td");			        
					$mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
					$mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
					$mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
					$mHtml->Hidden(array( "name" => "Option", "id" => "OptionID", 'value'=>'')); 
					$mHtml->Hidden(array( "name" => "cod_clasifi", "id" => "cod_clasifiID", 'value'=>'nan')); 
					$mHtml->Hidden(array( "name" => "nom_clasifi", "id" => "nom_clasifiID", 'value'=>'nan')); 
					$mHtml->Hidden(array( "name" => "row_dinami", "id" => "row_dinamiID", 'value'=>'nan')); 				 	
				$mHtml->CloseRow("td");
				# Cierra formulario
			$mHtml->CloseForm();
			# Cierra Body
			$mHtml->CloseBody();

			# Muestra Html
			echo $mHtml->MakeHtml();


		} 
		catch (Exception $e) 
		{
			
		}
	}


	/*! \fn: loadTabInfo
	 *  \brief: consulta datos segun el tab seleccioando
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function loadFiltrosSelect( $mFiltro = 'Estado' )
	{
		switch ($mFiltro) {
			case "Origen":
				$mSql = "SELECT ori_solici, ori_solici FROM ".BASE_DATOS.".tab_citasx_entreg GROUP BY 1 ORDER BY 2";
			break;
			case "Destino":
				$mSql = "SELECT des_solici, des_solici FROM ".BASE_DATOS.".tab_citasx_entreg GROUP BY 1 ORDER BY 2";
			break;
			case "Operacion":
				$mSql = "SELECT cod_tipdes, nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes ORDER BY 2 ";
			break;
		}

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mReturn = $mConsult -> ret_matrix('i');
		return array_merge( [ 0 => ['', '----'] ], $mReturn );
	}

	/*! \fn: loadTabInfo
	 *  \brief: consulta datos segun el tab seleccioando
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function loadTabInfo()
	{	
		try 
		{

			$mHtml = new FormLib(2); 
			$mHtml->CloseTable("tr");
			# incluye Css
			 
			$mHtml->Body(array("menubar" => "no"));

			#creo el acordeon para el filtro
			#<DIV fitro>
       		#abre formulario
			if(!class_exists(DinamicList)) {
				include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");
			}

	       	$mHtml->Form([], true); 

				$mItera = $_REQUEST['tab_id'] == '1' ? 2 : 1; // El primer tab debe mostrar dos dinamicList

				// filtros si el tab es el 1
				if($_REQUEST['tab_id'] == '1')
				{
					$mChecked1 = $_REQUEST['tip_citdes'] == '1' ? 'true' : false;
					$mChecked2 = $_REQUEST['tip_citdes'] == '2' ? 'true' : false;

			        $mHtml->Row("td");
						$mHtml->OpenDiv("id:form2_".$i."; class:contentAccordionForm");
							$mHtml->Table('tr');
								$mHtml->Label("Citas Nueva: ", array('for' =>'tip_citdes1ID', 'width' => '15%') );
								$mHtml->Radio( array('name' =>'tip_citdes',
													 'id' =>'tip_citdes1ID',
													 'width' => '15%', 
													 'value' => '1', 
													 'onClick'=> 'getDataTab( '.$_REQUEST['tab_id'].', 1 )',
													 'checked'=> $mChecked1,
													 'align' => 'left' ) );
								$mHtml->Label("Citas Reprogramadas: ", array('for' =>'tip_citdes2ID', 'width' => '15%') );
								$mHtml->Radio( array('name' =>'tip_citdes',
													 'id' =>'tip_citdes2ID',
													 'width' => '15%', 
													 'value' => '2', 
													 'onClick'=> 'getDataTab( '.$_REQUEST['tab_id'].', 2 )',
													 'checked'=> $mChecked2,
													 'align' => 'left', 'end'=> 'yes') );
							$mHtml->CloseTable('tr');
						$mHtml->CloseDiv();
			        $mHtml->CloseRow("td");

				}	
 
		        $mHtml->Row("td");
					$mHtml->OpenDiv("id:form3; class:contentAccordionForm");

					$_SESSION["queryXLS"] = $mSql = self::getDataCitasImportadas('string', $_REQUEST['tip_citdes']);
					$list = new DinamicList( self::$cConexion, $mSql, "1" , "no", 'ASC');

					$list -> SetClose('no');
					// $list -> SetCreate("Crear empresa", "onclick:formulario()");
					$list -> SetHeader("GESTIONAR"					, "field:hand_up; width:1%; align:center;");
					$list -> SetHeader("SOLICITUD"					, "field:a.num_solici; width:1%");
					$list -> SetHeader("ESTADO DE LA SOLICITUD"		, "field:IF( a.est_solici = '1', 'Confirmada' , IF( a.est_solici = '0', 'Pendiente', 'Sin estado' ) ) ; width:1%");
					$list -> SetHeader("FECHA DE LA SOLICITUD"		, "field:DATE(a.fec_solici); width:1%");
					$list -> SetHeader("FECHA CARGUE DE LA SOLICITUD", "field:DATE(a.fec_carsol); width:1%");
					$list -> SetHeader("TIPO DE OPERACIÓN"			, "field:b.nom_tipdes; width:1%");
					$list -> SetHeader("ORIGEN"						, "field:a.ori_solici; width:1%");
					$list -> SetHeader("DESTINO"					, "field:a.des_solici; width:1%");
					$list -> SetHeader("OBSERVACIONES"				, "field:a.obs_solici; width:1%");
					$list -> SetHeader("VIAJE"						, "field:a.num_viajex; width:1%");
					$list -> SetHeader("PLACA"						, "field:a.num_placax; width:1%");
					$list -> SetHeader("FECHA CITA DE DESCARGUE"	, "field:a.fec_sinnom; width:1%");
					$list -> SetHeader("CLIENTE"					, "field:a.nom_client; width:1%");
					$list -> SetHeader("CANAL DE DISTRIBUCIÓN"		, "field:a.nom_canalx; width:1%");
					$list -> SetHeader("NEGOCIO"					, "field:a.nom_negoci; width:1%");
					$list -> SetHeader("CUMPLE"						, "field:IF(a.ind_cumple = '1', 'SI', 'NO'); width:1%");
					$list -> SetHeader("SEGUIMIENTO"				, "field:a.ind_seguim; width:1%");
					$list -> SetHeader("CAUSA"						, "field:a.cod_causax; width:1%");
					$list -> SetHeader("ASIGNADA "					, "field:a.ind_asigna; width:1%");
					$list -> SetHeader("ABECEDARIO"					, "field:a.cod_abece; width:1%");
					$list -> SetHeader("CONS"						, "field:a.cod_consxx; width:1%");
					$list -> SetHeader("MES"						, "field:a.num_mesxxx; width:1%");
					$list -> SetHeader("DIA"						, "field:a.num_diaxxx; width:1%");
					$list -> SetHeader("CITA REPROGRAMACIÓN"		, "field:a.cit_reprog; width:1%");
					$list -> SetHeader("TIPOLOGIA"					, "field:a.cod_tipogl; width:1%");
					$list -> SetHeader("PROPIETARIO"				, "field:a.cod_propie; width:1%");
					$list -> SetHeader("NOMBRE CONDUCTOR"			, "field:a.nom_conduc; width:1%");
					$list -> SetHeader("CELULAR CONDUCTOR"			, "field:a.cel_conduc; width:1%");
					$list -> SetHeader("AQUÍ"						, "field:a.nom_aquixx; width:1%"); 
					//$list -> SetOption("Opciones"	, "field:ind_estado; width:1%; onclikDisable:editarClasificacion( 2, this ); onclikEnable:editarClasificacion( 1, this ); onclikEdit:editarClasificacion( 99, this )" );
					$list -> SetHidden("num_consec"	, "29" ); 
					$list -> Display(self::$cConexion);
					$_SESSION["DINAMIC_LIST"] = $list ;
						$Html = $list -> GetHtml();
					$mHtml->SetBody($Html."</br>");
							
				    $mHtml->CloseDiv();
			    $mHtml->CloseRow("td");
				
			    
				# Cierra formulario
			$mHtml->CloseForm();
			# Cierra Body
			$mHtml->CloseBody();

			# Muestra Html
			echo $mHtml->MakeHtml();




		} 
		catch (Exception $e) 
		{
			new Consulta( 'ROLLBACK;', self::$cConexion );
			echo "<pre>Exception: "; print_r($e); echo "</pre>";
		}
	}

	/*! \fn: getDataCitasImportadas
	 *  \brief: Consulta las citas importadas y las cruza con despachos creados en GL
	 *  \author: Ing. Nelson Liberato
	 *	\date: 05/02/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getDataCitasImportadas( $mType = 'string', $mTpiCitdes = '1' )
	{ 

		/*
			(
					SELECT 
							x.num_gestio, x.num_consec, x.ind_cumple, x.nov_cumple
					  FROM 
							".BASE_DATOS.".tab_citasx_gestio x
			    INNER JOIN  (
				    			SELECT 
					      			y.num_consec, MAX(y.fec_creaci) AS fec_creaci
								  FROM 
										".BASE_DATOS.".tab_citasx_gestio y
								 WHERE  1 = 1
								 GROUP BY y.num_consec
				      		 	    
				      	) z ON x.num_consec = z.num_consec AND x.fec_creaci = z.fec_creaci
				 WHERE  x.ind_cumple = '0'
				 GROUP BY x.num_consec

			) c ON a.num_consec = c.num_consec
		*/
		
		switch ($_REQUEST['tab_id']) 
		{
			case '1':
					$mSql = "	  
						  SELECT 
									'bell' AS hand_up, /* <i class='fa fa-bell' aria-hidden='false' style='font-size:36px'></i> */
									a.num_solici,
									IF( a.est_solici = '1', 'Confirmada' , IF( a.est_solici = '0', 'Pendiente', 'Sin estado' ) ) AS est_solici,
									DATE(a.fec_solici) AS fec_solici,DATE(a.fec_carsol) AS fec_carsol,b.nom_tipdes,a.ori_solici,
									a.des_solici,a.obs_solici,a.num_viajex,a.num_placax,a.fec_sinnom,a.nom_client,
									a.nom_canalx,a.nom_negoci,
									IF(a.ind_cumple = '1', 'SI', 'NO') AS ind_cumple,
									a.ind_seguim,a.cod_causax,a.ind_asigna,a.cod_abece ,
									a.cod_consxx,a.num_mesxxx,a.num_diaxxx,a.cit_reprog,a.cod_tipogl,a.cod_propie,a.nom_conduc,a.cel_conduc,
									a.nom_aquixx,
									a.num_consec,
									COUNT(c.num_gestio)
							FROM 	
									".BASE_DATOS.".tab_citasx_entreg a
					  INNER JOIN 	".BASE_DATOS.".tab_genera_tipdes b ON a.cod_tipdes = b.cod_tipdes
					  LEFT JOIN 	".BASE_DATOS.".tab_citasx_gestio c ON a.num_consec = c.num_consec
						   WHERE
						   			1 = 1
						   	 AND 	( a.est_solici = 0 OR a.ind_cumple = 0 ) ";
	 
 
					$mSql .= $_REQUEST["fec_inixxx"] != '' && $_REQUEST['fec_finxxx'] != '' ? " AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inixxx"]."' AND '".$_REQUEST['fec_finxxx']."' " : "";

					$mSql .= $_REQUEST['num_consec'] != '' ? " AND a.num_consec = '".$_REQUEST["num_consec"]."' " : ' ';
					$mSql .= $_REQUEST['num_solici'] != '' ? " AND a.num_solici = '".$_REQUEST["num_solici"]."' " : ' ';
					$mSql .= $_REQUEST['num_viajex'] != '' ? " AND a.num_viajex = '".$_REQUEST["num_viajex"]."' " : ' ';
					$mSql .= $_REQUEST['est_solici'] != '' ? " AND a.est_solici = '".$_REQUEST["est_solici"]."' " : ' ';
					$mSql .= $_REQUEST['cod_tipope'] != '' ? " AND a.cod_tipdes = '".$_REQUEST["cod_tipope"]."' " : ' ';
					$mSql .= $_REQUEST['cod_ciuori'] != '' ? " AND a.ori_solici = '".$_REQUEST["cod_ciuori"]."' " : ' ';
					$mSql .= $_REQUEST['cod_ciudes'] != '' ? " AND a.des_solici = '".$_REQUEST["cod_ciudes"]."' " : ' ';

					$mSql .= "	GROUP BY a.num_consec ";

					$mSql .= $mTpiCitdes == '1' ? "	HAVING  COUNT(c.num_gestio) = 0  " : " HAVING COUNT(c.num_gestio) > 0 ";
				 
				 
				
			break;
			case '2':
				$mSql = " SELECT 
									'hand-up' AS hand_up, /* <i class='fa fa-thumbs-o-up' style='font-size:24px'></i> */
									a.num_solici,
									IF( a.est_solici = '1', 'Confirmada' , IF( a.est_solici = '0', 'Pendiente', 'Sin estado' ) ) AS est_solici,
									DATE(a.fec_solici),DATE(a.fec_carsol),b.nom_tipdes,a.ori_solici,
									a.des_solici,a.obs_solici,a.num_viajex,a.num_placax,a.fec_sinnom,a.nom_client,
									a.nom_canalx,a.nom_negoci,
									IF(a.ind_cumple = '1', 'SI', 'NO') AS ind_cumple,
									a.ind_seguim,a.cod_causax,a.ind_asigna,a.cod_abece ,
									a.cod_consxx,a.num_mesxxx,a.num_diaxxx,a.cit_reprog,a.cod_tipogl,a.cod_propie,a.nom_conduc,a.cel_conduc,
									a.nom_aquixx,
									a.num_consec
							FROM 	
									".BASE_DATOS.".tab_citasx_entreg a
					  INNER JOIN 	".BASE_DATOS.".tab_genera_tipdes b ON a.cod_tipdes = b.cod_tipdes
						   WHERE
						   			1 = 1
						   	 AND 	ind_cumple = 1 ";
					$mSql .= $_REQUEST["fec_inixxx"] != '' && $_REQUEST['fec_finxxx'] != '' ? " AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inixxx"]."' AND '".$_REQUEST['fec_finxxx']."' " : "";

					$mSql .= $_REQUEST['num_consec'] != '' ? " AND a.num_consec = '".$_REQUEST["num_consec"]."' " : ' ';
					$mSql .= $_REQUEST['num_solici'] != '' ? " AND a.num_solici = '".$_REQUEST["num_solici"]."' " : ' ';
					$mSql .= $_REQUEST['num_viajex'] != '' ? " AND a.num_viajex = '".$_REQUEST["num_viajex"]."' " : ' ';
					$mSql .= $_REQUEST['est_solici'] != '' ? " AND a.est_solici = '".$_REQUEST["est_solici"]."' " : ' ';
					$mSql .= $_REQUEST['cod_tipope'] != '' ? " AND a.cod_tipdes = '".$_REQUEST["cod_tipope"]."' " : ' ';
					$mSql .= $_REQUEST['cod_ciuori'] != '' ? " AND a.ori_solici = '".$_REQUEST["cod_ciuori"]."' " : ' ';
					$mSql .= $_REQUEST['cod_ciudes'] != '' ? " AND a.des_solici = '".$_REQUEST["cod_ciudes"]."' " : ' ';
			break;
			case '3':
				$mSql = " SELECT 
									'hand-up' AS hand_up, /* <i class='fa fa-thumbs-o-up' style='font-size:24px'></i> */
									a.num_solici,
									IF( a.est_solici = '1', 'Confirmada' , IF( a.est_solici = '0', 'Pendiente', 'Sin estado' ) ) AS est_solici,
									DATE(a.fec_solici),DATE(a.fec_carsol),b.nom_tipdes,a.ori_solici,
									a.des_solici,a.obs_solici,a.num_viajex,a.num_placax,a.fec_sinnom,a.nom_client,
									a.nom_canalx,a.nom_negoci,
									IF(a.ind_cumple = '1', 'SI', 'NO') AS ind_cumple,
									a.ind_seguim,a.cod_causax,a.ind_asigna,a.cod_abece ,
									a.cod_consxx,a.num_mesxxx,a.num_diaxxx,a.cit_reprog,a.cod_tipogl,a.cod_propie,a.nom_conduc,a.cel_conduc,
									a.nom_aquixx,
									a.num_consec,
									a.ind_cumple AS indicador_cumple
							FROM 	
									".BASE_DATOS.".tab_citasx_entreg a
					  INNER JOIN 	".BASE_DATOS.".tab_genera_tipdes b ON a.cod_tipdes = b.cod_tipdes
						   WHERE
						   			1 = 1
						   	 AND 	ind_cumple = 0 ";
					$mSql .= $_REQUEST["fec_inixxx"] != '' && $_REQUEST['fec_finxxx'] != '' ? " AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inixxx"]."' AND '".$_REQUEST['fec_finxxx']."' " : "";

					$mSql .= $_REQUEST['num_consec'] != '' ? " AND a.num_consec = '".$_REQUEST["num_consec"]."' " : ' ';
					$mSql .= $_REQUEST['num_solici'] != '' ? " AND a.num_solici = '".$_REQUEST["num_solici"]."' " : ' ';
					$mSql .= $_REQUEST['num_viajex'] != '' ? " AND a.num_viajex = '".$_REQUEST["num_viajex"]."' " : ' ';
					$mSql .= $_REQUEST['est_solici'] != '' ? " AND a.est_solici = '".$_REQUEST["est_solici"]."' " : ' ';
					$mSql .= $_REQUEST['cod_tipope'] != '' ? " AND a.cod_tipdes = '".$_REQUEST["cod_tipope"]."' " : ' ';
					$mSql .= $_REQUEST['cod_ciuori'] != '' ? " AND a.ori_solici = '".$_REQUEST["cod_ciuori"]."' " : ' ';
					$mSql .= $_REQUEST['cod_ciudes'] != '' ? " AND a.des_solici = '".$_REQUEST["cod_ciudes"]."' " : ' ';
			break;
			case '4':
				$mSql = "SELECT 1
								                    ";
			break;
		}

		

		//$mSql .= $_REQUEST['num_consec'] != '' ? " AND a.num_consec = '".$_REQUEST["num_consec"]."' " : ' ';
		

		// echo "<pre>"; print_r($mSql); echo "</pre>";
		if($mType == 'string') {
			return $mSql; 
		}
		else{
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mReturn = $mConsult -> ret_matrix('a');
			return $mReturn[0];
		}

	}

	
	/*! \fn: loadFormGestionar
	*  \brief: Formulario para gestionar las citas de entrega
	*  \author: Ing. Nelson Liberato
	*	\date: 12/12/2018
	*	\date modified: dia/mes/año
	*  \param: 
	*  \return:
	*/
	private function loadFormGestionar($date = NULL) 
	{
		// echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/min.js'></script>\n";
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/jquery.js'></script>\n";
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/config.js'></script>\n"; 
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/functions.js'></script>\n"; 
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js'></script>\n";
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js'></script>\n";
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/timepicker.js'></script>\n";

		// INclución de js y css de jquery table
		// echo "<script type='text/javascript' language='javascript' src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js'></script>\n";
		// echo "<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'>\n";
		 echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>\n";

		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/ges_citasx_entreg.js'></script>\n";
 
		$mData = self::getDataCitasImportadas('array', $_REQUEST['tip_citdes']);
		//echo "<pre>"; print_r($mData); echo "</pre>";

		$mHtml = new FormLib(2);

		$mHtml->SetCss("dinamic_list");
		$mHtml->CloseTable("tr");
		# incluye Css
		$mHtml->SetCssJq("jquery");
		$mHtml->Body(array("menubar" => "no"));
    

		#creo el acordeon para el filtro
		#<DIV fitro>
   		#abre formulario
       	$mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_gestionar",
            "header" => "Gestionar Cita de entrega",
            "enctype" => "multipart/form-data")); 

	        $mHtml->Row("td");
		      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
		      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
			        	$mHtml->SetBody("<h2 style='padding:6px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Datos de la cita de entrega</b></h2>");
							$mHtml->OpenDiv("id:sec2"); #Div3
								$mHtml->Table('tr');
									 
							 

									$mHtml->Label("Nº solicitud: ", array('for' =>'num_soliciID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'num_solici', 'width' => '15%', 'align' => 'left', 'value' => $mData['num_solici']) );
									$mHtml->Label("Estado de la solicitud: ", array('for' =>'est_soliciID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'est_solici', 'width' => '15%', 'align' => 'left', 'value' => $mData['est_solici'], 'end'=> 'yes') );									

									$mHtml->Label("Viaje:", array('for' =>'num_viajexID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'num_viajex', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['num_viajex']) );
									$mHtml->Label("Fecha cargue de la solicitud:", array('for' =>'fec_soliciID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'fec_solici', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['fec_carsol'], 'end'=> 'yes') );										

									$mHtml->Label("Origen:", array('for' =>'ori_soliciID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'ori_solici', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['ori_solici']) );
									$mHtml->Label("Destino:", array('for' =>'des_soliciID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'des_solici', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['des_solici'], 'end'=> 'yes') );										

									$mHtml->Label("Nombre Conductor:", array('for' =>'nom_conducID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'nom_conduc', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['nom_conduc']) );
									$mHtml->Label("Celular Conductor:", array('for' =>'cel_conducID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'cel_conduc', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['cel_conduc'], 'end'=> 'yes') );										

									$mHtml->Label("Tipo de operación:", array('for' =>'nom_tipdesID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'nom_tipdes', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['nom_tipdes']) );
									$mHtml->Label("Placa:", array('for' =>'num_placaxID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'num_placax', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['num_placax'], 'end'=> 'yes') );										

									$mHtml->Label("Negocio:", array('for' =>'nom_negociID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'nom_negoci', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['nom_negoci']) );
									$mHtml->Label("Cliente:", array('for' =>'nom_clientID', 'width' => '15%') );
									$mHtml->Info( array('name' =>'nom_client', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['nom_client'], 'end'=> 'yes') );										
 
									$mHtml->Label("Canal de distribución:", array('for' =>'nom_canalxID', 'width' => '15%', ) );
									$mHtml->Info( array('name' =>'nom_canalx', 'width' => '15%', 'align' => 'left','colspan'=>'3', 'value' => $mData['nom_canalx'], 'end'=> 'yes') );										
 
									$mHtml->Label("Observaciones:", array('for' =>'obs_soliciID', 'width' => '15%') );
									$mHtml->TextArea($mData['obs_solici'], array('name' =>'obs_solici', 'cols' => '110', 'rows' => '10', 'colspan'=>'3' ) );										

									// $mHtml->Button(array('name' =>'subir', 'value'=>'Importar', 'colspan' => '4', 'align' => 'center', 'onclick' => 'ValidaImportar()'));


								$mHtml->CloseTable('tr');
							$mHtml->CloseDiv();	 #Div3

					$mHtml->CloseDiv();	 #Div2
		      	$mHtml->CloseDiv();	#Div1
		    $mHtml->CloseRow("td");
 			
 			// if($_REQUEST['tab_id'] == '1') // Muestra solo seccion de CUMPLE, NOVEDAD y OBSERVACIÓN, NO PUEDE REPROGRAMAR NADA NI CAUSAR
 			// {
		        $mHtml->Row("td");
			      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
			      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
				        	$mHtml->SetBody("<h2 style='padding:6px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Datos y gestión de la cita de descargue</b></h2>");
								$mHtml->OpenDiv("id:sec2"); #Div3
									$mHtml->Table('tr');

										$mHtml->Label("Fecha cita de descargue:", array('for' =>'fec_sinnomID', 'width' => '15%') );
										$mHtml->Input( array('name' =>'fec_sinnom', 'width' => '15%', 'colspan'=> '3', 'align' => 'left', 'value' => $mData['fec_sinnom'], 'end'=> 'yes') ); // fec_citdes
										//$mHtml->Input( array('name' =>'fec_sinnom_time', 'width' => '15%', 'colspan'=> '3', 'align' => 'left', 'value' => date('H:i', strtotime($mData['fec_sinnom'] ) ) , 'end'=> 'yes') ); // fec_citdes
	
										$mHtml->Label("Cumple: ", array('for' =>'ind_cumpleID', 'width' => '15%') );
										$mHtml->Select( [ 0 => ['', '--'], 1 => ['1', 'SI'], 2 => ['0', 'NO']], array('name' =>'ind_cumple', 'width' => '15%', 'align' => 'left', 'onChange' => 'getNovedades()', 'key' => $mData['indicador_cumple'] ) );
										$mHtml->Label("Novedad: ", array('for' =>'cod_novedaID', 'width' => '15%') );
										$mHtml->Select( [ 0 => [ '', '--' ] ], array('name' =>'cod_noveda', 'width' => '15%', 'align' => 'left', 'end'=> 'yes') );
	 
										$mHtml->Label("Observación:", array('for' =>'obs_gestioID', 'width' => '15%') );
										$mHtml->TextArea('', array('name' =>'obs_gestio', 'cols' => '110', 'rows' => '10', 'colspan'=>'3', 'value' => $mFechas['fec_finxxx']) );
										 
									$mHtml->CloseTable('tr');
								$mHtml->CloseDiv();	 #Div3
	
						$mHtml->CloseDiv();	 #Div2
			      	$mHtml->CloseDiv();	#Div1		      	 
			    $mHtml->CloseRow("td");
 			// }

    		if($_REQUEST['tab_id'] == '3') // Muestra reprogramación y causa, aplica para el man de medellin, FARO NO REPROGRAMA NADA
			{	
				$mHtml->Row("td");
			      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
			      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
				        	$mHtml->SetBody("<h2 style='padding:6px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Datos y gestión de la cita de descargue</b></h2>");
								$mHtml->OpenDiv("id:sec2"); #Div3
									$mHtml->Table('tr');

										$mHtml->Label("Reprogramar cita:", array('for' =>'chk_reprogID', 'width' => '15%') );
										$mHtml->CheckBox( array('name' =>'chk_reprog', 'width' => '15%', 'align' => 'left', 'value' => $mData['fec_sinnom']) ); // fec_citdes
										$mHtml->Input( array('name' =>'fec_newcit', 'width' => '15%', 'colspan'=> '3', 'align' => 'left', 'value' => date('Y-m-d H:i:s', strtotime($mData['fec_sinnom'] ) ) , 'end'=> 'yes') ); // fec_citdes
	 
										$mHtml->Label("Observación:", array('for' =>'obs_gestioID', 'width' => '15%') );
										$mHtml->TextArea('', array('name' =>'obs_gestio', 'cols' => '110', 'rows' => '10', 'colspan'=>'3', 'value' => $mFechas['fec_finxxx']) );
										 
									$mHtml->CloseTable('tr');
								$mHtml->CloseDiv();	 #Div3
	
						$mHtml->CloseDiv();	 #Div2
			      	$mHtml->CloseDiv();	#Div1		      	 
			    $mHtml->CloseRow("td");

				$mHtml->Row("td");
				  	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
				  		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
				        	$mHtml->SetBody("<h2 style='padding:6px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Asignar no cumplimiento</b></h2>");
								$mHtml->OpenDiv("id:sec2"); #Div3
									$mHtml->Table('tr');
										 
								 
										$mHtml->Label("Descripción causa: ", array('for' =>'cod_causaxID', 'width' => '15%') );
										$mHtml->Select(  self::getCausas() , array('name' =>'cod_causax', 'width' => '15%', 'align' => 'left', 'colspan' =>'3', 'onChange' => 'getInfoCausas()', 'end' => 'yes' ) );
								 	
									 	$mHtml->Label("Área:", array('for' =>'nom_areaxxID', 'width' => '15%') );
										$mHtml->Info( array('name' =>'nom_areaxx', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['nom_areaxx']) );
										$mHtml->Label("Clasificación:", array('for' =>'nom_clasifID', 'width' => '15%') );
										$mHtml->Info( array('name' =>'nom_clasif', 'width' => '15%', 'align' => 'left','readonly' => 'readonly', 'value' => $mData['nom_clasif'], 'end'=> 'yes') );										


										$mHtml->Label("Observación:", array('for' =>'obs_causalID', 'width' => '15%') );
										$mHtml->TextArea('', array('name' =>'obs_causal', 'cols' => '110', 'rows' => '10', 'colspan'=>'3', 'value' => $mData['obs_causal']) );
										 
									$mHtml->CloseTable('tr');
								$mHtml->CloseDiv();	 #Div3

						$mHtml->CloseDiv();	 #Div2
				  	$mHtml->CloseDiv();	#Div1		      	 
				$mHtml->CloseRow("td");
			}




	        $mHtml->Row("td");
		      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
		      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
			        	$mHtml->SetBody("<h2 style='padding:6px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Datos grilla de descargue y últimos dos seguimientos</b></h2>");
							$mHtml->OpenDiv("id:sec2"); #Div3
								$mHtml->Table('tr');

									$mHtml->Row("td");
										$mHtml->OpenDiv("id:form5_1; class:contentAccordionForm");
											$mHtml->Table('tr');
											 
												$mData = self::getEstadoDescargue();
												$mHtml->Label("Estado descargue: ", array('for' =>'ind_cumdesID', 'width' => '15%') );
												$mHtml->Info( array('name' =>'ind_cumdes', 'width' => '15%', 'align' => 'left', 'value' => $mData['ind_cumdes']) );
												$mHtml->Label("Fecha ejecutada: ", array('for' =>'fec_cumdesID', 'width' => '15%') );
												$mHtml->Info( array('name' =>'fec_cumdes', 'width' => '15%', 'align' => 'left', 'value' => $mData['fec_cumdes'], 'end'=>'yes') );
												$mHtml->Label("Novedad ", array('for' =>'nom_novedaID', 'width' => '15%') );
												$mHtml->Info( array('name' =>'nom_noveda', 'width' => '15%', 'align' => 'left', 'value' => $mData['nom_noveda'] ) );
												$mHtml->Label("Usuario: ", array('for' =>'usr_cumdesID', 'width' => '15%') );
												$mHtml->Info( array('name' =>'usr_cumdes', 'width' => '15%', 'align' => 'left', 'value' => $mData['usr_cumdes'], 'end'=>'yes') );
												$mHtml->Label("Observación: ", array('for' =>'obs_cumdesID', 'width' => '15%') );
												$mHtml->TextArea($mData['obs_cumdes'], array('name' =>'obs_cumdes', 'cols' => '110', 'rows' => '10', 'colspan'=>'3' ) );
											
											$mHtml->CloseTable('tr');
										$mHtml->CloseDiv();
							        $mHtml->CloseRow("td");
									 

								$mHtml->CloseTable('tr');
							$mHtml->CloseDiv();	 #Div3

					$mHtml->CloseDiv();	 #Div2
		      	$mHtml->CloseDiv();	#Div1		      	 
		    $mHtml->CloseRow("td");

	        $mHtml->Row("td");
		      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
		      		 
					$mHtml->Table('tr');

						if($_REQUEST['tab_id'] == '3') {
							$mHtml->Button(array('name' =>'Guardar' , 'value'=>'Guardar' , 'align' => 'center', 'onclick' => 'registrar_no_cumplidas()'));
						}
						else {
							$mHtml->Button(array('name' =>'Guardar' , 'value'=>'Guardar' , 'align' => 'center', 'onclick' => 'registrar()'));
						}

						$mHtml->Button(array('name' =>'Cancelar', 'value'=>'Cancelar', 'align' => 'center', 'onclick' => 'cancelar()'));

					$mHtml->CloseTable('tr');
							 
		      	$mHtml->CloseDiv();	#Div1		      	 
		    $mHtml->CloseRow("td");

		    $mHtml->Row("td");			        
				$mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
				$mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
				$mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
				$mHtml->Hidden(array( "name" => "Opcion", "id" => "OpcionID", 'value'=>'registrarGestion')); 
				$mHtml->Hidden(array( "name" => "num_consec", "id" => "num_consecID", 'value'=>$_REQUEST['num_consec']));  	 	
				$mHtml->Hidden(array( "name" => "tab_id", "id" => "tab_idID", 'value'=>$_REQUEST['tab_id']));  	 	
			$mHtml->CloseRow("td");
			# Cierra formulario
		$mHtml->CloseForm();
		# Cierra Body
		$mHtml->CloseBody();

		# Muestra Html
		$mHtml -> SetBody('<script>getNovedades(true);</script>');
		echo $mHtml->MakeHtml();
    }


	/*! \fn: getLastNovedad
	 *  \brief: Consulta la ultima novedadregistrada a un seguimiento
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getLastNovedad( )
	{
		try
		{
			$mSql = "   SELECT 
								x.num_gestio, x.num_consec, x.ind_cumple, x.nov_cumple
						  FROM 
								".BASE_DATOS.".tab_citasx_gestio x
				    INNER JOIN  (
					    			SELECT 
						      			y.num_consec, MAX(y.fec_creaci) AS fec_creaci
									  FROM 
											".BASE_DATOS.".tab_citasx_gestio y
									 WHERE  1 = 1
									 GROUP BY y.num_consec
					      		 	    
					      	) z ON x.num_consec = z.num_consec AND x.fec_creaci = z.fec_creaci
					 WHERE  x.num_consec = '".$_REQUEST['num_consec']."' ";

			//echo "<pre>"; print_r($mSql); echo "</pre>";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mReturn = $mConsult -> ret_matrix('a');
			echo json_encode(['status'=>true, 'cod_noveda' => $mReturn[0]['nov_cumple']]);
		} 
		catch (Exception $e) 
		{
			
		}		
	}


	/*! \fn: getEstadoDescargue
	 *  \brief: >Consulta el estado de descargue de la solicitud
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getEstadoDescargue( )
	{
		try
		{
			$mSql = "   SELECT  
								CASE 
									WHEN c.ind_cumdes IS NULL THEN 'SIN GESTIONAR'
									WHEN c.ind_cumdes = '0' THEN 'NO CUMPLIÓ LA CITA'
									WHEN c.ind_cumdes = '1' THEN 'CUMPLIÓ LA CITA'
								END AS ind_cumdes,
								c.ind_citdes,
								c.fec_cumdes,
								d.nom_noveda,
								c.obs_cumdes,
								c.usr_cumdes
						  FROM 
								".BASE_DATOS.".tab_citasx_entreg a
				    LEFT JOIN   ".BASE_DATOS.".tab_despac_sisext b ON a.num_solici =  b.num_solici
				    LEFT JOIN   ".BASE_DATOS.".tab_despac_destin c ON b.num_despac =  c.num_despac
				    LEFT JOIN   ".BASE_DATOS.".tab_genera_noveda d ON c.nov_cumdes =  d.cod_noveda
					 WHERE  a.num_consec = '".$_REQUEST['num_consec']."' ";

			//echo "<pre>"; print_r($mSql); echo "</pre>";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mReturn = $mConsult -> ret_matrix('a');
			return $mReturn[0];
		} 
		catch (Exception $e) 
		{
			
		}		
	}

	/*! \fn: getCausas
	 *  \brief: Lista las causas para la cita de descargue
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getCausas( )
	{
		try 
		{
			$mSql = "  
					   SELECT 
							   a.cod_causax, a.des_causax
			             FROM  ".BASE_DATOS.".tab_genera_causax a 
			       INNER JOIN  ".BASE_DATOS.".tab_genera_areasx b ON a.cod_areasx = b.cod_areasx
			       INNER JOIN  ".BASE_DATOS.".tab_genera_clasif c ON a.cod_clasif = c.cod_clasif
			            WHERE
			            	   a.ind_estado = '1' ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mReturn = $mConsult -> ret_matrix('i');
			return array_merge(  [ 0 => [ '','---' ] ], $mReturn );
		} 
		catch (Exception $e) 
		{
			
		}
	}	


	/*! \fn: getInfoCausas
	 *  \brief: Lista las causas para la cita de descargue
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getInfoCausas( )
	{
		try 
		{
			$mSql = "  
					   SELECT 
							   b.nom_areasx, c.nom_clasif
			             FROM  ".BASE_DATOS.".tab_genera_causax a 
			       INNER JOIN  ".BASE_DATOS.".tab_genera_areasx b ON a.cod_areasx = b.cod_areasx
			       INNER JOIN  ".BASE_DATOS.".tab_genera_clasif c ON a.cod_clasif = c.cod_clasif
			            WHERE
			            	   a.ind_estado = '1' 
			              AND  a.cod_causax = '".$_REQUEST['cod_causax']."' ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mReturn = $mConsult -> ret_matrix('a');
			echo json_encode($mReturn[0]);
		} 
		catch (Exception $e) 
		{
			
		}
	}

	/*! \fn: registrarGestion
	 *  \brief: registra la gestion, tab_citasx_entreg.num_consec, de la cita en la BD
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function registrarGestion(   )
	{
		try 
		{
			
			/*genera regsitro de la gestion en bitacora*/
// num_gestio
// num_consec
// ind_cumple
// nov_cumple
// obs_cumple

// ind_reprog
// fec_repcit
// obs_reprog
// cod_causax
// obs_causax
// usr_creaci
// fec_creaci
// 
// {"Ajax":"on","Option":"registrarGestion","Standa":"satt_standa","fec_sinnom":"2018-01-09 04:00:00","ind_cumple":"0","cod_noveda":"223","obs_gestio":"","num_consec":"2","tab_id":"1"}

			/*Si la gestion es del tab 1, es el controlador de faro, diligencia solo unos campos*/
			if($_REQUEST['tab_id'] == '1')
			{ 

				$mQueryInsert =  sprintf( "INSERT INTO ".BASE_DATOS.".tab_citasx_gestio 
											( num_consec, ind_cumple, nov_cumple, 
											  obs_cumple, usr_creaci, fec_creaci ) 
											VALUES
											( \"%s\",  \"%s\",  \"%s\",  
											  \"%s\",  \"%s\",  NOW()
										    )", 
	                                            $_REQUEST['num_consec']  ,$_REQUEST['ind_cumple'], $_REQUEST['cod_noveda'],
	                                            $_REQUEST['obs_gestio']  ,self::$cSession['cod_usuari']
                                        ); 
				$mConsult = new Consulta( $mQueryInsert, self::$cConexion );	

			}
			else
			{

				$mQueryInsert =  sprintf( "INSERT INTO ".BASE_DATOS.".tab_citasx_gestio 
											( num_consec, 
											  ind_cumple, nov_cumple, obs_cumple, 
											  ind_reprog, fec_repcit, obs_reprog,
											  cod_causax, obs_causax,
											  usr_creaci, fec_creaci 
											) 
											VALUES
											( \"%s\",  
											  \"%s\",  \"%s\",  \"%s\",  
											  \"%s\",  \"%s\",  \"%s\",  
											  \"%s\",   \"%s\",   
											  \"%s\",  NOW()
										    )", 
	                                            $_REQUEST['num_consec'],
	                                            $_REQUEST['ind_cumple'], $_REQUEST['cod_noveda'], $_REQUEST['obs_gestio'],
	                                            $_REQUEST['chk_reprog'], $_REQUEST['fec_newcit'], $_REQUEST['obs_reprog'],
	                                            $_REQUEST['cod_causax'], $_REQUEST['obs_causal'],
	                                            self::$cSession['cod_usuari']
                                        ); 

				$mConsult = new Consulta( $mQueryInsert, self::$cConexion );

			}

			$mUpdate = sprintf( "UPDATE ".BASE_DATOS.".tab_citasx_entreg 
									SET
									    ind_cumple = \"%s\", 
									    usr_modifi = \"%s\", 
									    fec_modifi = NOW()
								  WHERE
										num_consec = \"%s\"; " , 
										
										$_REQUEST['ind_cumple']      ,
										self::$cSession['cod_usuari'],
										$_REQUEST['num_consec']
                                    );
			$mConsult = new Consulta( $mUpdate, self::$cConexion );

			echo json_encode(['status' => true, 'message' => 'Gestion almacenada con exito!']);
		} 
		catch (Exception $e) 
		{	
			echo json_encode(['status' => false, 'message' => 'Gestion no almacenada!']);
		}
	}
































	/*! \fn: validallaveCreada
	 *  \brief: Valida que el registro exista en la BD
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function validallaveCreada( $mData = [], $mReturn = 'bool' )
	{
		try 
		{
			$mSolici = [];
			$mQuery = "
				SELECT 
						num_solici, fec_solici, cod_tipdes, num_viajex, num_placax,
						ind_cumple, ind_seguim, cod_causax, ind_asigna, num_consec
				  FROM
				  		".BASE_DATOS.".tab_citasx_entreg

				 WHERE  1 = 1
				   AND  num_solici = '{$mData["num_solici"]}'
				   AND  fec_solici = '{$mData["fec_solici"]}'
				   AND  cod_tipdes = '{$mData["cod_tipdes"]}'
				   AND  num_viajex = '{$mData["num_viajex"]}'
				   AND  num_placax = '{$mData["num_placax"]}' ";
			$mConsult = new Consulta( $mQuery, self::$cConexion );
			$mSolici = $mConsult -> ret_matrix('a');

			if($mReturn == 'bool'){
				return sizeof($mSolici) > 0 ? true : false;
			}
			else{
				return $mSolici[0];
			}

		} 
		catch (Exception $e) 
		{
			
		}
	}

	/*! \fn: validaEstadoViaje
	 *  \brief: Valida estado o creacion del viaje, para subir la informacion a esta
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function validaEstadoViaje( $mData = [] )
	{
		try 
		{
			$mQuery = "
						SELECT 
								a.num_solici, a.num_despac, a.num_placax, 
								a.num_dessat, a.ind_anulad, b.fec_salida, 
								b.fec_llegad
						  FROM
						  		".BASE_DATOS.".tab_despac_corona a
				    INNER JOIN  ".BASE_DATOS.".tab_despac_despac b ON a.num_dessat = b.num_despac
				    INNER JOIN  ".BASE_DATOS.".tab_despac_vehige c ON b.num_despac = c.num_despac AND a.num_placax = c.num_placax

						 WHERE  1 = 1
						   AND  a.num_despac = '{$mData["num_viajex"]}'
						   AND  a.num_solici = '{$mData["num_solici"]}'
						   AND  a.num_placax = '{$mData["num_placax"]}' ";
			$mConsult = new Consulta( $mQuery, self::$cConexion );
			$mViajex = $mConsult -> ret_matrix('a');

			return $mViajex[0];
		} 
		catch (Exception $e) 
		{
			
		}
	}


}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new GestionCitasEntrega();
else
	$_INFORM = new GestionCitasEntrega($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);


?>
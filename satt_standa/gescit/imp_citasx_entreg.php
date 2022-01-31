<?php
/*! \file: class_despac_trans3.php
 *  \brief: Archivo con las consultas de los Despachos en Cargue, Transito o Descargue 
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 16/06/2015
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
class ImportarCitasEntrega
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cHoy,
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
										'Confirmada'   			=> ['est_solici' => '1'  ],
										'' 						=> ['est_solici' => '0'  ]
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
		if($_REQUEST[Ajax] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cCodAplica = $ca;
			self::$cUsuario = $us;
		}
		self::$cSession = $_SESSION["datos_usuario"];
		self::$cHoy = date("Y-m-d H:i:s");
		//self::$cTypeUser = self::typeUser();

		 
		switch($_REQUEST['Option'])
		{
			case 'importarArchivo':
				self::importarArchivo();
				break;

			default:
				self::formInput();
				break;
		}
				//header('Location: index.php?window=central&cod_servic=1366&menant=1366');
		
	}


	/*! \fn: typeUser
	 *  \brief: Retorna el tipo de perfil del usuario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 09/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: array
	 */
	public function typeUser()
	{
		$mPerfil = $_SESSION['datos_usuario']['cod_perfil'];
		$mResult = array();

		if( $mPerfil == '7' || $mPerfil == '713' )
			$mResult['tip_perfil'] = 'CONTROL'; #Perfil Controlador
		elseif( $mPerfil == '70' || $mPerfil == '80' || $mPerfil == '669' )
			$mResult['tip_perfil'] = 'EAL'; #Perfil EAL
		else{
			$mTransp = self::getTransp();
			if( sizeof($mTransp) == '1' ){
				$mResult['tip_perfil'] = 'CLIENTE'; #Perfil Cliente
				$mResult['cod_transp'] = $mTransp[0][0];
			}else
				$mResult['tip_perfil'] = 'OTRO'; 
		}

		return $mResult;
	}

	/*! \fn: formInput
	 *  \brief: Formulario inicial
	 *  \author: Ing. Nelson Liberato
	 *	\date: 10/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function formInput()
	{
		/*try 
		{
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/imp_citasx_entreg.js'></script>\n";

			$mHtml = new Formlib(2);
			$mHtml->Body(array("menubar" => "no"));
			$mHtml->Table( );
				$mHtml->Row( 'tr' );
					$mHtml->Form("action:index.php; method:post; name:formulario; enctype:multipart/form-data; header:dasdas");

						$mHtml->Table('tr');
							$mHtml->Hidden(array( "name" => "cod_servic", "id"=>"cod_servicID", "value"=> $_REQUEST['cod_servic']));
							$mHtml->Hidden(array( "name" => "window", "id"=>"windowID", "value"=>"central"));
							$mHtml->Hidden(array( "name" => "Option", "id"=>"OptionID", "value"=>"1" ) ) ;
							$mHtml->Hidden(array( "name" => "cod_tercer", "id"=>"cod_tercerID", "value"=>($_REQUEST['cod_transp']!=""?$_REQUEST['cod_transp']:self::getTranspUsuari()[0][0])));
							$mHtml->Hidden(array( "name" => "Standa", "id"=>"StandaID", "value"=> DIR_APLICA_CENTRAL) );
						$mHtml->CloseTable('tr');


						$mHtml->Table('tr');
							$mHtml->Line("- nnnnnnsnsnsnsnsnsn a d asd ", "t2", 0, 0, "left", "Ocultar Area", "ShowSection( '1' )", "SectionLink1");
						$mHtml->CloseTable('tr');

						$mHtml->Table("tr");
								$mHtml->Label("Ruta archivo XLS/CSV: ", array('for' =>'', 'width' => '15%') );
								$mHtml->File( array('name' => 'fileCitas', 'width' => '25%', 'end' => 'yes') );

								$mHtml->Button(array('name' =>'subir', 'value'=>'Importar', 'colspan' => '4', 'align' => 'center', 'onclick' => 'ValidaImportar()'));
						$mHtml->CloseTable("tr");

					$mHtml -> CloseForm();
				$mHtml->CloseRow( );
			$mHtml->CloseBody();

			echo $mHtml->MakeHtml();
		} 
		catch (Exception $e) 
		{
			
		}*/



		try 
		{
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/min.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/config.js'></script>\n"; 
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/jquery.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/functions.js'></script>\n"; 
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/imp_citasx_entreg.js'></script>\n";

			$mHtml = new FormLib(2);

			# incluye JS
			//-- $mHtml->SetJs("min");
			//-- $mHtml->SetJs("config");
			//-- $mHtml->SetJs("fecha");
			//-- $mHtml->SetJs("jquery");
			//-- $mHtml->SetJs("functions");
			// $mHtml->SetJs("ajax_transp_transp");
			// $mHtml->SetJs("InsertProtocolo");
			//-- $mHtml->SetJs("new_ajax"); 
			//-- $mHtml->SetJs("dinamic_list"); 
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
				        	$mHtml->SetBody("<h2 style='padding:6px;'><B>Importar archivo</B></h2>");
								$mHtml->OpenDiv("id:sec2"); #Div3
									$mHtml->Table('tr');
										// $mHtml->Label("* Nombre clasificación: ", array('for' =>'nom_clasifiID', 'width' => '15%', 'align' => 'right', 'maxlength'=>'50' ) );
										// $mHtml->Input( array('name' =>'nom_clasifi', 'width' => '15%', 'align' => 'left', 'end'=>'yes','size' => 90) );

										// $mHtml->StyleButton("colspan:3; name:send; id:registrarID; value:Guardar; onclick:registrar('registrar'); align:center;  class:crmButton small save");


										$mHtml->Label("Ruta archivo XLS/CSV: ", array('for' =>'', 'width' => '15%') );
										$mHtml->File( array('name' => 'fileCitas', 'width' => '25%', 'end' => 'yes') );

										$mHtml->Button(array('name' =>'subir', 'value'=>'Importar', 'colspan' => '4', 'align' => 'center', 'onclick' => 'ValidaImportar()'));


									$mHtml->CloseTable('tr');
								$mHtml->CloseDiv();	 #Div3
						$mHtml->CloseDiv();	 #Div2
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


	/*! \fn: importarArchivo
	 *  \brief: recibe el archivo a importar
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function importarArchivo()
	{	
		try 
		{
			// ini_set('memory_limit', '3400MB');
			// ini_set('max_execution_time', 100); 
			//set_time_limit(0);

			// echo "<pre>"; print_r(phpinfo() ); echo "</pre>"; die();
			// ******************************************************************************************************************************************************************************
			// Validaciones del archivo antes de continuar
			// ******************************************************************************************************************************************************************************
			$mError = [];
			if(!in_array($_FILES['fileCitas']['type'], self::$cFileTypes)) // valida el tipo de archivo
			{
				throw new Exception("Por favor valide el tipo de archivo a importar, debe ser un archivo CSV!", 3001);				
			}

			if( $_FILES['fileCitas']['size'] <= 0) // valida el peso del archivo
			{
				throw new Exception("Por favor valide el archivo a importar, puede ser que no contenga datos para importar!", 3002);				
			}
			 
			// se debe mover el archivo porque la libreria lee archivos con extencion XLS o XLSX
			$mFileTemp = getcwd().'/temp/'.$_FILES['fileCitas']['name'];
			//$mFileTemp = '/temp/'.$_FILES['fileCitas']['name'];

			if(!move_uploaded_file($_FILES['fileCitas']['tmp_name'], $mFileTemp ) )
			{
				$mLastError = error_get_last();
				throw new Exception("No se pudo copiar el archivo al servidor: ".$mLastError['message'] , 3004);
			}

			$mQueryDML = []; // Array de updates o inserts a ejecutar al final del ciclo	 

			new Consulta( 'START TRANSACTION;', self::$cConexion );

			$mSearch  = array("(\¬)", "(\.)", "(\,)", "(\ )", "(ñ)", "(Ñ)", "(\°)", "(\º)", "(&)", "(Â)", "(\()", "(\))", "(\/)", "(\´)", "(\¤)", "(\Ã)", "(\‘)", "(\ƒ)", "(\â)", "(\€)", "(\˜)", "(\¥)", "(Ò)", 
						    "(Í)", "(\É)", "(\Ãƒâ€šÃ‚Â)", "(\·)", "(\ª)", "(\-)", "(\+)", "(\Ó)", "(\ü)", "(\Ü)", "(\é)", "(\;)", "(\¡)", "(\!)", "(\`)", "(\<)", "(\>)", "(\_)", "(\#)", "(\ö)", "(\À)", "(\¿)", 
						    "(\Ã±)", "(\±)", "(\*)", "(Ú)", "(\%)", "(\|)", "(\ò)", "(\Ì)", "(\:)", "(\Á)", "(\×)", "(\@)", "(\ )", "(\Ù)", "(\á)", "(\–)", "(\")", "(\È)", "(\])", "(\')", "(\í)", "(\Ç)",
						    "(\Nš)","(\‚)", "(\ó)", "(\ )", "(\ )", "(\ï½)", "(\?)" );
			$mReplace = array(" ", " ", " ", " ", "n", "N", " ", " ", "Y", "", "", "", "", "", "", "", "", "", "", "", "", "", "O", "I", "E", " ", "", "a", " ", " ", 
							"O","U","U", "e", " ", "", "", "", "", "", "", "", "", "A", "", "", "", "", "", "", "", "", "I", "", "A", "", "", " ", "U", "a", " ", "", 
							"E", " ", " ", "i", "", "N"," ", " ", " ", " ", " " , "", ""  );  


			if (($handle = fopen($mFileTemp, "r")) !== FALSE) 
			{	
				$mCont = 0;
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) 
				{
					if( $mCont > 0 )
					{

						$Row = self::setDatos($data); 

						$mViaje = self::validaEstadoViaje($Row, $mCont);
						//echo "<pre>"; print_r( $mViaje ); echo "</pre>";

						if($mViaje['num_despac'] == '')
						{
							$mMessage['num_viajex'][] = ['num_viajex' => $Row['num_viajex'], 'obs_messag' => 'El Viaje: <b>'.$Row['num_viajex'].'</b> con solicitud: <b>'.$Row['num_solici'].'</b>, no se encuentra registrado en el sistema o no ha sido liberado.'];
						}
						else if($mViaje['ind_anulad'] == 'A')
						{
							$mMessage['ind_anulad'][] = ['num_viajex' => $Row['num_viajex'], 'obs_messag' => 'El Viaje: <b>'.$Row['num_viajex'].'</b>, se encuentra anulado'];
						}
						else if($mViaje['fec_llegad'] != '')
						{
							$mMessage['fec_llegad'][] = ['num_viajex' => $Row['num_viajex'], 'obs_messag' => 'El Viaje: <b>'.$Row['num_viajex'].'</b>, está finalizado'];
						}
						else
						{
							$mNumConsec = self::validallaveCreada($Row, 'array');

							// Se limpia caracteres especiales que totean el código o el insert/update
							$mRow['des_solici'] = preg_replace($mSearch, $mReplace, $Row['des_solici']);
							$mRow['obs_solici'] = preg_replace($mSearch, $mReplace, $Row['obs_solici']);
							$mRow['nom_client'] = preg_replace($mSearch, $mReplace, $Row['nom_client']);
							$mRow['nom_negoci'] = preg_replace($mSearch, $mReplace, $Row['nom_negoci']);

							if( sizeof($mNumConsec) >= 1  ) // Valida si ya existe en la BD, hace update
							{ 
								$mQuery = sprintf("UPDATE ".BASE_DATOS.".tab_citasx_entreg 
																   SET 
																num_solici = \"%s\",
																est_solici = \"%s\",
																fec_solici = \"%s\",
																fec_carsol = \"%s\",
																cod_tipdes = \"%s\",
																ori_solici = \"%s\",
																cod_orisol = \"%s\",						
																des_solici = \"%s\",
																cod_dessol = \"%s\",
																obs_solici = \"%s\",
																num_viajex = \"%s\",
																num_placax = \"%s\",
																fec_sinnom = \"%s\",
																nom_client = \"%s\",
																cod_client = \"%s\",						
																nom_canalx = \"%s\",
																nom_negoci = \"%s\",
																cod_negoci = \"%s\",
																ind_cumple = \"%s\",
																ind_seguim = \"%s\",
																cod_causax = \"%s\",
																ind_asigna = \"%s\",
																cod_abece  = \"%s\",						
																cod_consxx = \"%s\",
																num_mesxxx = \"%s\",
																num_diaxxx = \"%s\",
																cit_reprog = \"%s\",
																cod_tipogl = \"%s\",
																cod_propie = \"%s\",
																nom_conduc = \"%s\",
																cel_conduc = \"%s\",
																nom_aquixx = \"%s\",
																usr_modifi = \"%s\",
																fec_modifi = NOW()
																WHERE 
																num_consec = \"%s\" ", 
							 											$Row['num_solici'],$Row['est_solici'],$Row['fec_solici'],$Row['fec_carsol'],$Row['cod_tipdes'],$Row['ori_solici'],$Row['cod_orisol'],
							 											$Row['des_solici'],$Row['cod_dessol'],preg_replace("/\n/", "", $Row['obs_solici']),$Row['num_viajex'],$Row['num_placax'],$Row['fec_sinnom'],$Row['nom_client'],$Row['cod_client'],
							 											$Row['nom_canalx'],$Row['nom_negoci'],$Row['cod_negoci'],$Row['ind_cumple'],$Row['ind_seguim'],$Row['cod_causax'],$Row['ind_asigna'],$Row['cod_abece'],
							 											$Row['cod_consxx'],$Row['num_mesxxx'],$Row['num_diaxxx'],$Row['cit_reprog'],$Row['cod_tipogl'],$Row['cod_propie'],$Row['nom_conduc'],$Row['cel_conduc'],
							 											$Row['nom_aquixx'],self::$cSession['cod_usuari'],$mNumConsec['num_consec']
							 										);
								$mQueryDML['update'][] = $mQuery;
								$mMessage['sql_update'][] = ['num_viajex' => $Row['num_viajex'], 'obs_messag' => 'Cita actualizada para el viaje: <b>'.$Row['num_viajex'].'</b>'];
							}
							else  // Inserta la solicitud nueva en la BD
							{ 
							 	$mQuery = sprintf("INSERT INTO ".BASE_DATOS.".tab_citasx_entreg 
																(
																	num_consec,num_solici,est_solici,fec_solici,fec_carsol,cod_tipdes,ori_solici,cod_orisol,
																	des_solici,cod_dessol,obs_solici,num_viajex,num_placax,fec_sinnom,nom_client,cod_client,
																	nom_canalx,nom_negoci,cod_negoci,ind_cumple,ind_seguim,cod_causax,ind_asigna,cod_abece ,
																	cod_consxx,num_mesxxx,num_diaxxx,cit_reprog,cod_tipogl,cod_propie,nom_conduc,cel_conduc,
																	nom_aquixx,usr_creaci,fec_creaci
																) 
																VALUES 
																( 
																	\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", 
																	\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", 
																	\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", 
																	\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", 
																	\"%s\", \"%s\", %s
																) "
																, 
						 											$Row['num_consec'],$Row['num_solici'],$Row['est_solici'],$Row['fec_solici'],$Row['fec_carsol'],$Row['cod_tipdes'],$Row['ori_solici'],$Row['cod_orisol'],
						 											$Row['des_solici'],$Row['cod_dessol'],preg_replace("/\n/", "", $Row['obs_solici']),$Row['num_viajex'],$Row['num_placax'],$Row['fec_sinnom'],$Row['nom_client'],$Row['cod_client'],
						 											$Row['nom_canalx'],$Row['nom_negoci'],$Row['cod_negoci'],$Row['ind_cumple'],$Row['ind_seguim'],$Row['cod_causax'],$Row['ind_asigna'],$Row['cod_abece'],
						 											$Row['cod_consxx'],$Row['num_mesxxx'],$Row['num_diaxxx'],$Row['cit_reprog'],$Row['cod_tipogl'],$Row['cod_propie'],$Row['nom_conduc'],$Row['cel_conduc'],
						 											$Row['nom_aquixx'],self::$cSession['cod_usuari'],"NOW()"
							 								 );
							 	$mQueryDML['insert'][] = $mQuery;
							 	$mMessage['sql_insert'][] = ['num_viajex' => $Row['num_viajex'], 'obs_messag' => 'Cita creada para el viaje: <b>'.$Row['num_viajex'].'</b>'];
							}

							// Ejecuta la query
							$mExecute = new Consulta( $mQuery, self::$cConexion );
							if(!$mExecute){
								throw new Exception("Error SQL importando los datos", '3001');						
							}
						}					 
					}
					$mCont++;
				}
				fclose($handle);
			}
			else{
				die('Imposible leer el archivo!');
			}

        	new Consulta( 'COMMIT;', self::$cConexion );

        	// Se ejecutan los inserts y updates generados
        	//echo "<pre>insert: "; print_r($mQueryDML['insert']); echo "</pre>";
        	//echo "<pre>update: "; print_r($mQueryDML['update']); echo "</pre>";


        	/*echo '.Style2DIV {
					    background-color: #FFFFFF !important;
					    border: 1px solid rgb(201, 201, 201) !important;
					    padding: 5px;
					    width: 99% !important;
					    min-height: 50px !important;
					    border-radius: 5px 5px 5px 5px !important;
					}';*/

			$mTitulo = ['num_viajex' => 'Validación de viajes', 'ind_anulad' => 'Viajes Anulados', 'fec_llegad' => 'Viajes finalizados', 'sql_update' => 'Citas actualizadas', 'sql_insert' => 'Citas creadas'];


        	echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/min.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/config.js'></script>\n"; 
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/jquery.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/functions.js'></script>\n"; 
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/imp_citasx_entreg.js'></script>\n";

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
	            "name" => "form_import",
	            "header" => "Importación de archivo",
	            "enctype" => "multipart/form-data")); 

		        $mHtml->Row("td");
			      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
			      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2

						foreach ( $mMessage AS $mTipo => $mInfoFinal) 
						{
							$mHtml->SetBody("<h2 style='padding:6px;'><B>".$mTitulo[$mTipo]."</B></h2>");
							$mHtml->OpenDiv("id:sec2"); #Div3
								$mHtml->Table('tr');
									
									$mHtml->SetBody("<div style='Style2DIV' >");
										$mHtml->SetBody("<ul>");
										foreach ($mInfoFinal AS $mInfo) 
										{
											$mHtml->SetBody("<li>");
											$mHtml->SetBody($mInfo['obs_messag']);
											$mHtml->SetBody("</li>");
											
										}
										$mHtml->SetBody("</ul>");
									$mHtml->SetBody("</div>");

								$mHtml->CloseTable('tr');
							$mHtml->CloseDiv();	 #Div3
						}

						$mHtml->CloseDiv();	 #Div2
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
			new Consulta( 'ROLLBACK;', self::$cConexion );
			echo "<pre>Exception: "; print_r($e); echo "</pre>";
		}
	}

	/*! \fn: setDatos
	 *  \brief: Hace vocado de datos cambiando la llave indexada por una asociativa
	 *  \author: Ing. Nelson Liberato
	 *	\date: 12/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function setDatos( $mRow = [] )
	{
		$mCells = [
					'num_solici','est_solici','fec_solici','fec_carsol','cod_tipdes','ori_solici','des_solici',
					'obs_solici','num_viajex','num_placax','fec_sinnom','nom_client','nom_canalx','nom_negoci',
					'ind_cumple','ind_seguim','cod_causax','ind_asigna','cod_abece', 'cod_consxx','num_mesxxx','num_diaxxx',
					'cit_reprog','cod_tipogl','cod_propie','nom_conduc','cel_conduc','nom_aquixx','usr_creaci','fec_creaci','usr_modifi',
					'fec_modifi'
				  ];
		$mNew = []; // NUevo array con el volcado de datos

		foreach ( $mCells AS $mKey => $mCell ) // Ejecuta volcado
		{ 
			$mNew[$mCell] =  str_replace(array("\n", "_x000D_"), '', $mRow[$mKey]);
		} 

		// Ejecuta homologacion de algunos datos
		$mNew["fec_solici"] =  self::con2mysql( $mNew['fec_solici'] );
		$mNew["fec_carsol"] =  self::con2mysql( $mNew['fec_carsol'] );
		$mNew["fec_sinnom"] =  date( 'Y-m-d H:i:s', strtotime($mNew['fec_sinnom']) ) ;

		$mNew["cod_tipdes"] = self::$cCodTipdes[$mNew["cod_tipdes"]]['cod_tipdes'];
		$mNew["est_solici"] = self::$cEstSolici[$mNew["est_solici"]]['est_solici'];
		$mNew["ind_cumple"] = self::$cIndCumple[$mNew["ind_cumple"]]['ind_cumple'];
		$mNew["cod_negoci"] = self::$cCodNegoci[$mNew["nom_negoci"]]['cod_negoci']; // este es el mismo producto o mercancia de corona
		 

		return $mNew;
	}


	
	/*! \fn: con2mysql
	*  \brief: formatea la maldita fecha del maldito excel de microsoft
	*  \author: Ing. Nelson Liberato
	*	\date: 12/12/2018
	*	\date modified: dia/mes/año
	*  \param: 
	*  \return:
	*/
	private function con2mysql($date = NULL) 
	{
		$date = explode("/",$date);
		if ( strlen($date[0]) <= 1) { 
			$date[0]="0".$date[0]; 
		}
		if ( strlen($date[1]) <= 1) { 
			$date[1]="0".$date[1]; 
		}

		$date = array('20'.$date[2], $date[1], $date[0]);

		return $n_date=implode("-", $date);
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
						/*validallaveCreada*/ num_solici, fec_solici, cod_tipdes, num_viajex, num_placax,
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
	public function validaEstadoViaje( $mData = [] , $mCont = 0)
	{
		try 
		{
			$mQuery = "
						SELECT 
								/*validaEstadoViaje ".$mCont." */ a.num_solici, a.num_despac, a.num_placax, 
								a.num_dessat, a.ind_anulad, b.fec_salida, 
								b.fec_llegad
						  FROM
						  		".BASE_DATOS.".tab_despac_corona a
				    INNER JOIN  ".BASE_DATOS.".tab_despac_despac b ON a.num_dessat = b.num_despac
				    INNER JOIN  ".BASE_DATOS.".tab_despac_vehige c ON b.num_despac = c.num_despac AND a.num_placax = c.num_placax

						 WHERE  1 = 1
						   AND  ( a.num_despac = '{$mData["num_viajex"]}' OR  a.num_solici = '{$mData["num_solici"]}' )
						   -- AND  a.num_placax = '{$mData["num_placax"]}' ";
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
	$_INFORM = new ImportarCitasEntrega();
else
	$_INFORM = new ImportarCitasEntrega($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);


?>
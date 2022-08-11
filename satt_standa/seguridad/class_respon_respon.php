<?php
/*! \file: class_respon_respon.php
 *  \brief: Clase principal de responsables
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 21/09/2015
 *  \bug: 
 *  \warning: 
 */

/*! \class: responsable
 *  \brief: Clase principal de responsables
 */
class responsable
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co, $us, $ca)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		if($_REQUEST['Ajax'] === 'on' )
		{
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;

			switch($_REQUEST['Option'])
			{
				case 'saveRespon':
					self::saveRespon();
					break;

				case 'edicionRespon':
					self::edicionRespon();
					break;

				default:
					header('Location: index.php?window=central&cod_servic=1366&menant=1366');
					break;
			}
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}
	}

	private function style()
	{
		echo '<style>
				.cellHead {
					background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #00660f 0%, #00660f 100%) repeat scroll 0 0;
					color: #fff;
					padding: 5px 10px;
					text-align: center;
				}

				.cellInfo1 {
					background-color: #DEDFDE;
					font-family: Verdana,Arial;
					font-size: 11px;
					padding: 2px;
				}

				.cellInfo2 {
					background-color: #EBF8E2;
					font-family: Verdana,Arial;
					font-size: 11px;
					padding: 2px;
				}
			</style>';
	}

	/*! \fn: saveRespon
	 *  \brief: actualiza o guarda nuevos responsables
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/09/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: HTML
	 */
	private function saveRespon()
	{
		if( $_REQUEST['cod_respon'] == '' )
		{ #Nuevo Registro
			$mSql = "SELECT MAX(a.cod_respon) 
					   FROM ".BASE_DATOS.".tab_genera_respon a 
					";
			$mConsult = new Consulta($mSql, self::$cConexion);
			$mCodRespon = $mConsult->ret_arreglo();

			$mSql = "INSERT INTO ".BASE_DATOS.".tab_genera_respon 
						( cod_respon, nom_respon, ind_activo, 
						  jso_bandej, jso_encabe, jso_plarut, 
						  jso_infcal, jso_notifi, jso_contac, jso_partic, jso_obsgen, edt_gpsxxx, jso_progra, jso_estseg, usr_creaci, fec_creaci ) 
					VALUES 
						( '".($mCodRespon[0]+1)."', '".$_REQUEST['nom_respon']."', '".$_REQUEST['ind_activo']."', 
						  '".json_encode($_REQUEST['jso_bandej'])."', '".json_encode($_REQUEST['jso_encabe'])."', '".json_encode($_REQUEST['jso_plarut'])."', 
						  '".json_encode($_REQUEST['jso_infcal'])."', '".json_encode($_REQUEST['jso_notifi'])."', '".json_encode($_REQUEST['jso_contac'])."', '".json_encode($_REQUEST['jso_partic'])."','".json_encode($_REQUEST['jso_obsgen'])."','".json_encode($_REQUEST['edt_gpsxxx'])."' ,'".json_encode($_REQUEST['jso_progra'])."' ,
						  '".json_encode($_REQUEST['jso_estseg'])."','".$_SESSION['datos_usuario']['cod_usuari']."', NOW() ) 	
					";
			$mConsult = new Consulta($mSql, self::$cConexion);
		}
		else
		{
			$mSql = "UPDATE ".BASE_DATOS.".tab_genera_respon 
					SET nom_respon = '".$_REQUEST['nom_respon']."', 
						ind_activo = '".$_REQUEST['ind_activo']."', 
						usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
			".( $_REQUEST['ind_editor'] == '1' ? " 	jso_bandej = '".json_encode($_REQUEST['jso_bandej'])."', 
													jso_encabe = '".json_encode($_REQUEST['jso_encabe'])."', 
													jso_plarut = '".json_encode($_REQUEST['jso_plarut'])."', 
													jso_infcal = '".json_encode($_REQUEST['jso_infcal'])."',
													jso_notifi = '".json_encode($_REQUEST['jso_notifi'])."',
													jso_contac = '".json_encode($_REQUEST['jso_contac'])."',
													jso_partic = '".json_encode($_REQUEST['jso_partic'])."',
													edt_gpsxxx = '".json_encode($_REQUEST['edt_gpsxxx'])."',
													jso_progra = '".json_encode($_REQUEST['jso_progra'])."',
													jso_obsgen = '".json_encode($_REQUEST['jso_obsgen'])."',
													jso_estseg = '".json_encode($_REQUEST['jso_estseg'])."'," : NULL )."
						fec_modifi = NOW() 
					WHERE cod_respon = '".$_REQUEST['cod_respon']."' ";
			$mConsult = new Consulta($mSql, self::$cConexion);
		}

		if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
			$mensaje = "<font color='#000000'>Se Guardo el Responsable Exitosamente.<br></font>";
			$mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
			$mens = new mensajes();
			echo $mens->correcto2("RESPONSABLES", $mensaje);
		}else{
			$mensaje = "<font color='#000000'>Ocurrio un Error Inesperado al Guardar el Responsable</font>";
			$mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
			$mens = new mensajes();
			echo $mens->error2("RESPONSABLES", $mensaje);
		}
	}

	/*! \fn: edicionRespon
	 *  \brief: Crea formulario para editar o guardar nuevos responsables
	 *  \author: Ing. Fabian Salinas
	 *	\date: 21/09/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: HTML
	 */
	private function edicionRespon()
	{
		$mSql = "SELECT jso_bandej, jso_encabe, jso_plarut, jso_infcal, jso_notifi, jso_contac, jso_partic, jso_obsgen, edt_gpsxxx, jso_progra, jso_estseg
				   FROM ".BASE_DATOS.".tab_genera_respon 
				  WHERE cod_respon = '".$_REQUEST['cod_respon']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix('a');

		$mData = $mData[0];
		$mData['jso_bandej'] = json_decode($mData['jso_bandej']);
		$mData['jso_encabe'] = json_decode($mData['jso_encabe']);
		$mData['jso_plarut'] = json_decode($mData['jso_plarut']);
		$mData['jso_infcal'] = json_decode($mData['jso_infcal']);
		$mData['jso_notifi'] = json_decode($mData['jso_notifi']);
		$mData['jso_contac'] = json_decode($mData['jso_contac']);
		$mData['edt_gpsxxx'] = json_decode($mData['edt_gpsxxx']);
		$mData['jso_progra'] = json_decode($mData['jso_progra']);
		$mData['jso_partic'] = json_decode($mData['jso_partic']);
		$mData['jso_obsgen'] = json_decode($mData['jso_obsgen']);
		$mData['jso_estseg'] = json_decode($mData['jso_estseg']);

		self::style();
		$mCategoria = array("jso_bandej"=>'Bandeja', "jso_encabe"=>'Encabezado', "jso_plarut"=>'Plan de Ruta', "jso_infcal"=>'Informe Auditorias General', "jso_notifi"=>'de notificaciones', "jso_contac"=>'contactos', "jso_partic"=>'particularidades', "jso_obsgen"=>'Observacion general', "edt_gpsxxx"=>'Editar GPS', "jso_progra"=>'Configuracion Programacion', "jso_estseg"=>"Estudio de seguridad");
		$mChecked = $_REQUEST['ind_activo'] == '1' ? 'true' : 'false';

		$mHtml = new Formlib(2);

			$mHtml->Table("tr");
				$mHtml->Label( "Datos Basicos del Responsable".$val, array("colspan"=>"4", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
				$mHtml->CloseRow();

				$mHtml->Row();
				$mHtml->Label( "Responsable:", array("align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
				$mHtml->Input( array("name"=>"nom_respon", "id"=>"nom_responID", "width"=>"25%", "value"=>$_REQUEST['nom_respon'], "class"=>"cellInfo2") );
				$mHtml->Label( "Estado:", array("align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
				$mHtml->CheckBox( array("name"=>"ind_activo", "id"=>"ind_activoID", "value"=>"1", "checked"=>$mChecked, "width"=>"25%", "class"=>"cellInfo2") );
			$mHtml->CloseTable('tr');

			#Configuracion visibilidad por responsable
			foreach ($mCategoria as $keyCat => $namCat)
			{#Recorre las categorias
				$mArray = self::armaArray( $keyCat );

				$mHtml->Table("tr");
				$mHtml->Label( "Visibilidad ".$namCat, array("colspan"=>"4", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
				$mHtml->CloseRow();

				foreach ($mArray[0] as $key => $value)
				{#Recorre las secciones
					$mHtml->Row();
					$mHtml->Label( utf8_decode($mArray[0][$key]['name']), array("colspan"=>"2", "align"=>"right", "width"=>"50%", "class"=>"cellInfo2") );
					$mHtml->CheckBox( array("colspan"=>"2", "name"=>$keyCat."[$key][ind_visibl]", "id"=>$key."ID", "value"=>"1", "checked"=>$mData[$keyCat]->$key->ind_visibl, "width"=>"50%", "class"=>"cellInfo2") );

					if( sizeof($mArray[0][$key]['sub']) > 0 ){
						$i=0;
						foreach ($mArray[0][$key]['sub'] as $keySub => $valSub)
						{#Recorre las subsecciones
							if( ($i % 2) == 0 ){
								$mHtml->CloseRow();
								$mHtml->Row();
							}
							$mHtml->Label( utf8_decode($mArray[0][$key]['sub'][$keySub]), array("align"=>"right", "width"=>"25%", "class"=>"cellInfo1") );
							$mHtml->CheckBox( array("name"=>$keyCat."[$key][sub][".$keySub."]", "id"=>$keySub."ID", "value"=>"1", "checked"=>$mData[$keyCat]->$key->sub->$keySub, "width"=>"25%", "class"=>"cellInfo1") );
							$i++;
						}
						if( ($i % 2) != 0 )
							$mHtml->Label( "&nbsp;", array("colspan"=>"2", "align"=>"right", "width"=>"25%", "class"=>"cellInfo1") );	
					}
				}
				$mHtml->CloseTable('tr');
			}

			$mHtml->Table("tr");
				$mHtml->Button( array("align"=>"center", "value"=>"Guardar", "onclick"=>"verify()", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all") );

				$mHtml->Hidden( array("name"=>"cod_respon", "id"=>"cod_responID", "value"=>$_REQUEST['cod_respon']) );
				$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>"central") );
				$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
				$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
			$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: armaArray
	 *  \brief: Arma la Matriz de configuracion de visibilidad, para agregar un nuevo campo de visibilidad agregarlo en los dos arrays dentro de una categoria
	 *  \author: Ing. Fabian Salinas
	 *  \date:  21/09/2015
	 *  \date modified: 03/02/2016
	 *  \modified by: Ing. Fabian Salinas
	 *  \param: mNameArray  String  Nombre de la matriz a armar
	 *  \return: Matriz
	 */
	private function armaArray( $mNameArray )
	{
		switch ($mNameArray)
		{
			case 'jso_bandej':
				$mArray[0] = array( "fil_genera"=> array( "name"=>"Filtros Generales", 
														  "sub"=>array(
																		"tip_despac"=>"Tipo de Despacho", 
																		"tip_servic"=>"Tipo de Servicio", 
																		"otr_filtro"=>"Otros Filtros",
																		"fil_gencar"=>"Generadores de carga" 
																	  )
														),
									"fil_especi"=> array( "name"=>"Filtros Especificos", 
														  "sub"=>array()
														),
									"sec_inform"=> array( "name"=>"Pestañas Informes", 
														  "sub"=>array(
																		"pes_genera"=>"Pestaña General",
																		"pes_prcarg"=>"Pestaña PreCargue",
																		"pes_cargax"=>"Pestaña Cargue", 
																		"pes_transi"=>"Pestaña Transito", 
																		"pes_descar"=>"Pestaña Descargue", 
																		"pes_pernoc"=>"Pestaña C. Pernotacion", 
																		"pes_contro"=>"Pestaña Control Operacion" 
																	  )
														),
									"sec_detail"=>array( "name"=>"Detalle",
														 "sub"=>array(
														 				"lla_cargue"=>"Llamadas Cargue"
														 				)
														 ),
									"tie_alarma"=>array( "name"=>"Tiempo Alarma GL"),
									"col_itiner"=>array( "name"=>"Columna itinerario")
								  );
				
				$mArray[1] = array( "fil_genera"=>array("ind_visibl"=>1, "sub"=>array("tip_despac"=>1, "tip_servic"=>1, "otr_filtro"=>1, "fil_gencar"=>1) ),
									"fil_especi"=>array("ind_visibl"=>1, "sub"=>array() ),
									"sec_inform"=>array("ind_visibl"=>1, "sub"=>array("pes_genera"=>1, "pes_cargax"=>1, "pes_transi"=>1, "pes_descar"=>1, "pes_pernoc"=>1, "pes_contro"=>1) ),
									"sec_detail"=>array("ind_visibl"=>1, "sub"=>array("lla_cargue"=>1)),
									"tie_alarma"=>array( "ind_visibl"=>1)
								   );
			break;

			case 'jso_encabe':
				$mArray[0] = array( "dat_basico"=>array("name"=>"Datos Basicos del Despacho", "sub"=>array() ),
									"dat_comple"=>array("name"=>"Datos Complementarios del Despacho", "sub"=>array() ),
									"dat_fechas"=>array("name"=>"Fechas del Despacho", "sub"=>array() ),
									"gri_cargax"=>array("name"=>"Grilla Cargue", "sub"=>array() ),
									"gri_descar"=>array("name"=>"Grilla Descargue", "sub"=>array() ),
									"bit_cambio"=>array("name"=>"Bitacora de cambios", "sub"=>array() ),
									"est_servic"=>array("name"=>"Estado del Servicio", "sub"=>array() ),
									"sec_fotosx"=>array("name"=>"Fotos", "sub"=>array() ),
									"sec_mapaxx"=>array("name"=>"Mapa", "sub"=>array()),
									"dat_telefo"=>array("name"=>"Telefonos", "sub"=>array())
								  );

				$mArray[1] = array( "dat_basico"=>array("ind_visibl"=>1, "sub"=>array()), 
									"dat_comple"=>array("ind_visibl"=>1, "sub"=>array()), 
									"dat_fechas"=>array("ind_visibl"=>1, "sub"=>array()), 
									"gri_cargax"=>array("ind_visibl"=>1, "sub"=>array()), 
									"gri_descar"=>array("ind_visibl"=>1, "sub"=>array()), 
									"bit_cambio"=>array("ind_visibl"=>1, "sub"=>array()), 
									"est_servic"=>array("ind_visibl"=>1, "sub"=>array()), 
									"sec_fotosx"=>array("ind_visibl"=>1, "sub"=>array()),
									"sec_mapaxx"=>array("ind_visibl"=>1, "sub"=>array()),
									"dat_telefo"=>array("ind_visibl"=>1, "sub"=>array())
								   );
			break;

			case 'jso_plarut':
				$mArray[0] = array( "inf_planru"=>array("name"=>"Informacion del Plan de Ruta", "sub"=>array(
																							"usr_creaci"=>"Usuario",
																							"ali_usuari"=>"Alias de usuario"
																						 ) 
														),
									"inf_llegad"=>array("name"=>"Llegada", "sub"=>array() ),
									"inf_notcon"=>array("name"=>"Informacion de Notas de Controlador", "sub"=>array(
																							"usr_creaci"=>"Usuario",
																							"ali_usuari"=>"Alias de usuario",
																							"cop_notcon"=>"Copiar observaci&oacute;n"
																						 ) 
														),
									"inf_crodok"=>array("name"=>"Trazabilidad Cross Doking", "sub"=>array() ),
									"obs_genera"=>array("name"=>"Observaciones Generales", "sub"=>array() ),
									"img_adjunt"=>array("name"=>"Imagenes Adjuntas al Despacho", "sub"=>array() ),
									"pop_califi"=>array("name"=>"Auditoria", "sub"=>array(
																							"usr_califi"=>"Calificar Usuarios"
																						 ) 
														),
									"ind_novnem"=>array("name"=>"Novedades en Moviles (NEM)", "sub"=>array() )
								  );

				$mArray[1] = array( "inf_planru"=>array("ind_visibl"=>1, "sub"=>array("usr_creaci"=>1,"ali_usuari"=>1)), 
									"inf_llegad"=>array("ind_visibl"=>1, "sub"=>array()), 
									"inf_notcon"=>array("ind_visibl"=>1, "sub"=>array("usr_creaci"=>1,"ali_usuari"=>1,"cop_notcon"=>1)), 
									"inf_crodok"=>array("ind_visibl"=>1, "sub"=>array()), 
									"obs_genera"=>array("ind_visibl"=>1, "sub"=>array()), 
									"img_adjunt"=>array("ind_visibl"=>1, "sub"=>array()), 
									"pop_califi"=>array("ind_visibl"=>1, "sub"=>array("usr_califi"=>1)), 
									"ind_novnem"=>array("ind_visibl"=>1, "sub"=>array()), 
								   ); 
			break;

			case 'jso_infcal':
				$mArray[0] = array( "fil_genera"=> array( "name"=>"Filtros Generales", 
														  "sub"=>array(
																		"tip_despac"=>"Tipo de Despacho",
																		"usu_regist"=>"Usuarios Calificadores"
																	  )
														),
									"sec_inform"=> array( "name"=>"Pestañas Informes", 
														  "sub"=>array(
																		"pes_despac"=>"Pestaña Despachos", 
																		"pes_usuari"=>"Pestaña Usuarios"
																	  )
														)
								  );

				$mArray[1] = array( "fil_genera"=>array("ind_visibl"=>1, "sub"=>array("tip_despac"=>1) ),
									"sec_inform"=>array("ind_visibl"=>1, "sub"=>array("pes_despac"=>1, "pes_usuari"=>1) )
								   );
			break;

			case 'jso_notifi':
				$mArray[0] = array( "fil_genera"=> array( "name"=>"General", 
														  "sub"=>array()
														),
									"sec_infoet"=> array( "name"=>"Informacion OET", 
														  "sub"=>array(
																		"oet_ins"=>"Insertar", 
																		"oet_idi"=>"Editar",
																		"oet_rep"=>"Responder",
																		"oet_eli"=>"Eliminar"
																	  )
														),
									"sec_infclf"=> array( "name"=>"Informacion CLF", 
														  "sub"=>array(
																		"clf_ins"=>"Insertar", 
																		"clf_idi"=>"Editar",
																		"clf_rep"=>"Responder",
																		"clf_eli"=>"Eliminar"
																	  )
														),
									"sec_infsup"=> array( "name"=>"Supervisores", 
														  "sub"=>array(
																		"sup_ins"=>"Insertar", 
																		"sup_idi"=>"Editar",
																		"sup_rep"=>"Responder",
																		"sup_eli"=>"Eliminar"
																	  )
														),
									"sec_infcon"=> array( "name"=>"Controladores", 
														  "sub"=>array(
																		"con_ins"=>"Insertar", 
																		"con_idi"=>"Editar",
																		"con_rep"=>"Responder",
																		"con_eli"=>"Eliminar"
																	  )
														),
									"sec_infcli"=> array( "name"=>"Clientes", 
														  "sub"=>array(
																		"cli_ins"=>"Insertar", 
																		"cli_idi"=>"Editar",
																		"cli_rep"=>"Responder",
																		"cli_eli"=>"Eliminar"
																	  )
														)
								  );

				$mArray[1] = array( "fil_genera"=>array("ind_visibl"=>1, "sub"=>array() ),
									"sec_infoet"=>array("ind_visibl"=>1, "sub"=>array("opt_insoet"=>1,"opt_idioet"=>1,"opt_repoet"=>1,"opt_elioet"=>1) ),
									"sec_infclf"=>array("ind_visibl"=>1, "sub"=>array("opt_insclf"=>1,"opt_idiclf"=>1,"opt_repclf"=>1,"opt_eliclf"=>1) ),
									"sec_infsup"=>array("ind_visibl"=>1, "sub"=>array("opt_inssup"=>1,"opt_idisup"=>1,"opt_repsup"=>1,"opt_elisup"=>1) ),
									"sec_infcon"=>array("ind_visibl"=>1, "sub"=>array("opt_inscon"=>1,"opt_idicon"=>1,"opt_repcon"=>1,"opt_elicon"=>1) ),
									"sec_infcli"=>array("ind_visibl"=>1, "sub"=>array("opt_inscli"=>1,"opt_idicli"=>1,"opt_repcli"=>1,"opt_elicli"=>1) )

								   );

			break;

			case 'jso_contac':
				$mArray[0] = array( "dat_contac"=>array("name"=>"Visualizar", "sub"=>array() )
								  );

				$mArray[1] = array( "dat_contac"=>array("ind_visibl"=>1, "sub"=>array())
								   );
			break;

			case 'edt_gpsxxx':
				$mArray[0] = array( "dat_gpsxxx"=>array("name"=>"Visualizar", "sub"=>array() )
								  );
				$mArray[1] = array( "dat_gpsxxx"=>array("ind_visibl"=>1, "sub"=>array())
								);
				break;
			case 'jso_progra':
				$mArray[0] = array( "dat_progra"=>array("name"=>"Visualizar", "sub"=>array() ),
									"dat_elimin"=>array("name"=>"Eliminar", "sub"=>array() )
									);
				$mArray[1] = array( "dat_progra"=>array("ind_visibl"=>1, "sub"=>array()),
									"dat_elimim"=>array("ind_visibl"=>1, "sub"=>array()),
								);
				break;

				
			case 'jso_partic':
				$mArray[0] = array( "dat_partic"=>array("name"=>"Visualizar", "sub"=>array() )
								  );

				$mArray[1] = array( "dat_partic"=>array("ind_visibl"=>1, "sub"=>array())
								   );
			break;

			case 'jso_obsgen':
				$mArray[0] = array( "dat_obsgen"=>array("name"=>"Visualizar", "sub"=>array() )
								  );

				$mArray[1] = array( "dat_obsgen"=>array("ind_visibl"=>1, "sub"=>array())
								   );
			break;

			case 'jso_estseg':
				$mArray[0] = array( "dat_estseg"=>array("name"=>"Gestionar", "sub"=>array() ),
									"dat_regtra"=>array("name"=>"Seleccionar transportadora", "sub"=>array() )
								  );
				$mArray[1] = array( "dat_estseg"=>array("ind_gessol"=>1, "sub"=>array()),
									"dat_regtra"=>array("ind_visibl"=>1, "sub"=>array()),
								   );
			break;

		}

		return $mArray;
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new responsable();

?>
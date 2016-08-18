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

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

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
					font-family: Trebuchet MS,Verdana,Arial;
					font-size: 11px;
					padding: 2px;
				}

				.cellInfo2 {
					background-color: #EBF8E2;
					font-family: Trebuchet MS,Verdana,Arial;
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
						  jso_infcal, usr_creaci, fec_creaci ) 
					VALUES 
						( '".($mCodRespon[0]+1)."', '".$_REQUEST['nom_respon']."', '".$_REQUEST['ind_activo']."', 
						  '".json_encode($_REQUEST['jso_bandej'])."', '".json_encode($_REQUEST['jso_encabe'])."', '".json_encode($_REQUEST['jso_plarut'])."', 
						  '".json_encode($_REQUEST['jso_infcal'])."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() ) 	
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
													jso_infcal = '".json_encode($_REQUEST['jso_infcal'])."',  " : "" )."
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
			$mensaje = "<font color='#000000'>Ocurrió un Error Inesperado al Guardar el Responsable</font>";
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
		$mSql = "SELECT jso_bandej, jso_encabe, jso_plarut, jso_infcal 
				   FROM ".BASE_DATOS.".tab_genera_respon 
				  WHERE cod_respon = '".$_REQUEST['cod_respon']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix('a');

		$mData = $mData[0];
		$mData['jso_bandej'] = json_decode($mData['jso_bandej']);
		$mData['jso_encabe'] = json_decode($mData['jso_encabe']);
		$mData['jso_plarut'] = json_decode($mData['jso_plarut']);
		$mData['jso_infcal'] = json_decode($mData['jso_infcal']);


		self::style();
		$mCategoria = array("jso_bandej"=>'Bandeja', "jso_encabe"=>'Encabezado', "jso_plarut"=>'Plan de Ruta', "jso_infcal"=>'Informe Auditorias General');
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
					$mHtml->Label( $mArray[0][$key]['name'], array("colspan"=>"2", "align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
					$mHtml->CheckBox( array("colspan"=>"2", "name"=>$keyCat."[$key][ind_visibl]", "id"=>$key."ID", "value"=>"1", "checked"=>$mData[$keyCat]->$key->ind_visibl, "width"=>"25%", "class"=>"cellInfo2") );

					if( sizeof($mArray[0][$key]['sub']) > 0 ){
						$i=0;
						foreach ($mArray[0][$key]['sub'] as $keySub => $valSub)
						{#Recorre las subsecciones
							if( ($i % 2) == 0 ){
								$mHtml->CloseRow();
								$mHtml->Row();
							}
							$mHtml->Label( $mArray[0][$key]['sub'][$keySub], array("align"=>"right", "width"=>"25%", "class"=>"cellInfo1") );
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
																		"otr_filtro"=>"Otros Filtros" 
																	  )
														),
									"fil_especi"=> array( "name"=>"Filtros Especificos", 
														  "sub"=>array()
														),
									"sec_inform"=> array( "name"=>"Pestañas Informes", 
														  "sub"=>array(
																		"pes_genera"=>"Pestaña General", 
																		"pes_cargax"=>"Pestaña Cargue", 
																		"pes_transi"=>"Pestaña Transito", 
																		"pes_descar"=>"Pestaña Descargue" 
																	  )
														)
								  );

				$mArray[1] = array( "fil_genera"=>array("ind_visibl"=>1, "sub"=>array("tip_despac"=>1, "tip_servic"=>1, "otr_filtro"=>1) ),
									"fil_especi"=>array("ind_visibl"=>1, "sub"=>array() ),
									"sec_inform"=>array("ind_visibl"=>1, "sub"=>array("pes_genera"=>1, "pes_cargax"=>1, "pes_transi"=>1, "pes_descar"=>1) )
								   );
			break;

			case 'jso_encabe':
				$mArray[0] = array( "dat_basico"=>array("name"=>"Datos Basicos del Despacho", "sub"=>array() ),
									"dat_comple"=>array("name"=>"Datos Complementarios del Despacho", "sub"=>array() ),
									"dat_fechas"=>array("name"=>"Fechas del Despacho", "sub"=>array() ),
									"gri_cargax"=>array("name"=>"Grilla Cargue", "sub"=>array() ),
									"gri_descar"=>array("name"=>"Grilla Descargue", "sub"=>array() ),
									"bit_cambio"=>array("name"=>"Bitacora de cambios", "sub"=>array() ),
									"sec_fotosx"=>array("name"=>"Fotos", "sub"=>array() )
								  );

				$mArray[1] = array( "dat_basico"=>array("ind_visibl"=>1, "sub"=>array()), 
									"dat_comple"=>array("ind_visibl"=>1, "sub"=>array()), 
									"dat_fechas"=>array("ind_visibl"=>1, "sub"=>array()), 
									"gri_cargax"=>array("ind_visibl"=>1, "sub"=>array()), 
									"gri_descar"=>array("ind_visibl"=>1, "sub"=>array()), 
									"bit_cambio"=>array("ind_visibl"=>1, "sub"=>array()), 
									"sec_fotosx"=>array("ind_visibl"=>1, "sub"=>array())
								   );
			break;

			case 'jso_plarut':
				$mArray[0] = array( "inf_planru"=>array("name"=>"Informacion del Plan de Ruta", "sub"=>array() ),
									"inf_llegad"=>array("name"=>"Llegada", "sub"=>array() ),
									"inf_notcon"=>array("name"=>"Informacion de Notas de Controlador", "sub"=>array() ),
									"inf_crodok"=>array("name"=>"Trazabilidad Cross Doking", "sub"=>array() ),
									"obs_genera"=>array("name"=>"Observaciones Generales", "sub"=>array() ),
									"img_adjunt"=>array("name"=>"Imagenes Adjuntas al Despacho", "sub"=>array() ),
									"pop_califi"=>array("name"=>"Auditoria", "sub"=>array() )
								  );

				$mArray[1] = array( "inf_planru"=>array("ind_visibl"=>1, "sub"=>array()), 
									"inf_llegad"=>array("ind_visibl"=>1, "sub"=>array()), 
									"inf_notcon"=>array("ind_visibl"=>1, "sub"=>array()), 
									"inf_crodok"=>array("ind_visibl"=>1, "sub"=>array()), 
									"obs_genera"=>array("ind_visibl"=>1, "sub"=>array()), 
									"img_adjunt"=>array("ind_visibl"=>1, "sub"=>array()), 
									"pop_califi"=>array("ind_visibl"=>1, "sub"=>array()) 
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
		}

		return $mArray;
	}

}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new responsable();

?>
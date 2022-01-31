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
 
// ini_set('display_errors', true);
// error_reporting(E_ALL & ~E_NOTICE);

/*! \class: Despac
 *  \brief: Clase que realiza las consultas para retornar la información de los Despachos en Cargue, Transito o Descargue
 */
class insDescripcion
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
 

	public function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}
		self::$cSession = $_SESSION["datos_usuario"];
		self::$cHoy = date("Y-m-d H:i:s");
		self::$cTypeUser = self::typeUser();

		 
		switch($_REQUEST['Option'])
		{
			case 'registrar':		
			case 'activar':
			case 'inactivar':
			case 'editar':
				self::save( $_REQUEST['Option'] );
			break;
			case 'cargar':
				self::cargarCausa( $_REQUEST['Option'] );
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

	/*! \fn: getTransp
	 *  \brief: Trae las transportadoras
	 *  \author: Ing. Fabian Salinas
	 *	\date: 17/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getTransp()
	{
		$mSql = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer 
				   FROM ".BASE_DATOS.".tab_tercer_tercer a 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_activi b 
					 ON a.cod_tercer = b.cod_tercer 
				  WHERE b.cod_activi = ".COD_FILTRO_EMPTRA." 
					AND a.cod_estado = ".COD_ESTADO_ACTIVO."
					";
		if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) 
		{#PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_usuari] );
			if ( $filtro -> listar( self::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}else{#PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil] );
			if ( $filtro -> listar( self::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}
		$mSql .= " ORDER BY a.abr_tercer ASC ";

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
		try 
		{
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/ins_descri_gescit.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/min.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/config.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/fecha.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/jquery.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/functions.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js'></script>\n";
			echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js'></script>\n";

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
	            "name" => "form_descripcion",
	            "header" => "Áreas",
	            "enctype" => "multipart/form-data"));
		        $mHtml->Row("td");
			      	$mHtml->OpenDiv("id:tabla1ID; class:contentAccordion"); #Div1
			      		$mHtml->OpenDiv("id:tabla; class:accordion"); #Div2
				        	$mHtml->SetBody("<h2 style='padding:6px;'><B>Área</B></h2>");
								$mHtml->OpenDiv("id:sec2"); #Div3
									$mHtml->Table('tr');
										$mHtml->Label("* Descripción Causa: ", array('for' =>'des_causasID', 'width' => '25%', 'align' => 'right', 'maxlength'=>'50' ) );
										$mHtml->Input( array('name' =>'des_causax', 'width' => '75%', 'end' => 'yes', 'align' => 'left','size' => 90, 'colspan' => '3' ) );

										$mHtml->Label("* Área: ", array('for' =>'cod_areaxxID', 'width' => '25%', 'align' => 'right', 'maxlength'=>'50' ) );
										$mHtml->Select(self::cargarAreas(), array('name' =>'cod_areaxx', 'width' => '25%', 'align' => 'left', 'maxlength'=>'50' ) );										
										$mHtml->Label("* Clasificación: ", array('for' =>'cod_clasifID', 'width' => '25%', 'align' => 'right', 'maxlength'=>'50' ) );
										$mHtml->Select(self::cargarClasificaciones(), array('name' =>'cod_clasif', 'width' => '25%', 'end' => 'yes', 'align' => 'left', 'maxlength'=>'50' ) );

										$mHtml->StyleButton("colspan:4; name:send; id:registrarID; value:Guardar; onclick:registrar('registrar'); align:center;  class:crmButton small save");

									$mHtml->CloseTable('tr');
								$mHtml->CloseDiv();	 #Div3
						$mHtml->CloseDiv();	 #Div2
			      	$mHtml->CloseDiv();	#Div1
			    $mHtml->CloseRow("td");

			    $mHtml->Row("td");
			        $mHtml->OpenDiv("id:tablaID; class:contentAccordion");
				        $mHtml->OpenDiv("id:tabla; class:accordion");
				        	$mHtml->SetBody("<h2 style='padding:6px;'><B>LISTA DE CLASIFICACIONES</B></h2>");
				        		$mHtml->OpenDiv("id:sec2");
				        			$mHtml->OpenDiv("id:form3; class:contentAccordionForm");
				        				 $mSql = "SELECT 
				        				 					a.cod_causax, a.des_causax, b.nom_areasx, c.nom_clasif, a.ind_estado			                          
								                    FROM 
								                    		".BASE_DATOS.".tab_genera_causax a
								              INNER JOIN 	".BASE_DATOS.".tab_genera_areasx b ON a.cod_areasx = b.cod_areasx
								              INNER JOIN 	".BASE_DATOS.".tab_genera_clasif c ON a.cod_clasif = c.cod_clasif
								                   WHERE
								                   			1 = 1
								                    ";
								                         
									      $_SESSION["queryXLS"] = $mSql;

									      if(!class_exists(DinamicList)) {
									      	include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");									  	  	
									  	  }
									  	  $list = new DinamicList( self::$cConexion, $mSql, "1" , "no", 'ASC');
									      
									      $list->SetClose('no');
									      // $list->SetCreate("Crear empresa", "onclick:formulario()");


									      $list->SetHeader("Código"		  , "field:a.cod_causax; width:1%");
									      $list->SetHeader("Causa"		  , "field:a.des_causax; width:1%");
									      $list->SetHeader("Área"		  , "field:b.nom_areasx; width:1%");
									      $list->SetHeader("Clasificación", "field:c.nom_clasif; width:1%");
									      $list->SetOption("Opciones"	  , "field:a.ind_estado; width:1%; onclikDisable:editarCausa( 2, this ); onclikEnable:editarCausa( 1, this ); onclikEdit:editarCausa( 99, this )" );

									      $list->SetHidden("cod_causax"	, "0" );
									      $list->SetHidden("des_causax"	, "1" );
									      $list->Display(self::$cConexion);

									      $_SESSION["DINAMIC_LIST"] = $list;

									      $Html = $list -> GetHtml();

									 
									      $mHtml->SetBody($Html);
      									
				        			$mHtml->CloseDiv();
				        		$mHtml->CloseDiv();
				        	$mHtml->CloseDiv();
				        $mHtml->CloseDiv();				     
				#$mHtml->CloseRow("td");
			#fin de acordeon con la lista de las tranportadoras
			#</div> 
			$mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
			$mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
			$mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
			$mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>'')); 
			$mHtml->Hidden(array( "name" => "cod_causas", "id" => "cod_causasID", 'value'=>'nan')); 
			$mHtml->Hidden(array( "name" => "des_causas", "id" => "des_causasID", 'value'=>'nan')); 
			$mHtml->Hidden(array( "name" => "row_dinami", "id" => "row_dinamiID", 'value'=>'nan')); 
			 	
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


	/*! \fn: cargarAreas
	 *  \brief: Lista de areas
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function cargarAreas( $mData = NULL)
	{
		try 
		{
			$mQuery = "SELECT cod_areasx, nom_areasx FROM ".BASE_DATOS.".tab_genera_areasx WHERE ind_estado = '1' ";
			$consulta = new Consulta( $mQuery, self::$cConexion );
		    return  array_merge([0 => ['', 'Seleccione']], $consulta -> ret_matrix('i') ) ; 
		} 
		catch (Exception $e) 
		{
				
		}	
	}

	/*! \fn: cargarClasificaciones
	 *  \brief: lista de clasificaciones
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function cargarClasificaciones( $mData = NULL)
	{
		try 
		{
			$mQuery = "SELECT cod_clasif, nom_clasif FROM ".BASE_DATOS.".tab_genera_clasif WHERE ind_estado = '1' ";
			$consulta = new Consulta( $mQuery, self::$cConexion );
		    return  array_merge([0 => ['', 'Seleccione']], $consulta -> ret_matrix('i') ) ; 
		} 
		catch (Exception $e) 
		{
				
		}	
	}

	/*! \fn: cargarCausa
	 *  \brief: logica de guardar el dato en la BD
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function cargarCausa( $mData = NULL)
	{
		try 
		{
			$mQuery = "SELECT cod_causax, des_causax, cod_areasx, cod_clasif
						 FROM ".BASE_DATOS.".tab_genera_causax 
						WHERE cod_causax = '{$_REQUEST["cod_causax"]}' ";
			$consulta = new Consulta( $mQuery, self::$cConexion );
		    $mResult = $consulta -> ret_matrix('a');
		    echo json_encode( ['status' => ( sizeof($mResult) <= 0 ? false : true ), 'data' => $mResult[0] ] );
		} 
		catch (Exception $e) 
		{
				
		}	
	}
	
	/*! \fn: save
	 *  \brief: logica de guardar el dato en la BD
	 *  \author: Ing. Nelson Liberato
	 *	\date: 11/12/2018
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function save( $mOption = NULL)
	{	
		try 
		{
			
			switch ($mOption) {
				case 'registrar':
					$mQuery = "INSERT INTO 
							  ".BASE_DATOS.".tab_genera_causax 
							  ( cod_causax, 
							  	des_causax, cod_areasx, cod_clasif, ind_estado, usr_creaci, fec_creaci
							  )
							  VALUES
							  ( '', 
							  	'{$_REQUEST["des_causax"]}', '{$_REQUEST["cod_areasx"]}', '{$_REQUEST["cod_clasif"]}', '1', '{$_SESSION["datos_usuario"]["cod_usuari"]}', NOW() 
							  )
							  ";

				break;
				case 'activar':
				case 'inactivar': 
					$mQuery = "UPDATE ".BASE_DATOS.".tab_genera_causax 
							   SET 
							   			ind_estado = '".($mOption == 'activar' ? '1' : '0')."' 
							   WHERE 	
							   			cod_causax = '{$_REQUEST["cod_causax"]}'
							   ";
					
				break;				
				case 'editar':
					$mQuery = "UPDATE ".BASE_DATOS.".tab_genera_causax 
							   SET 
							   		des_causax = '{$_REQUEST["des_causax"]}',
							   		cod_areasx = '{$_REQUEST["cod_areasx"]}',
							   		cod_clasif = '{$_REQUEST["cod_clasif"]}' 
							   WHERE 
							   		cod_causax = '{$_REQUEST["cod_causax"]}' ";
					
				break;
				 
			}
			
			// echo json_encode($_REQUEST);
			// echo json_encode($mQuery);
 
			if(!new Consulta( $mQuery, self::$cConexion )) {
				$mReturn = ['status' => false, 'message' => 'Error al '.$mOption.' la causa: '.$_REQUEST["nom_clasif"] ];
			}else{
				$mReturn = ['status' => true, 'message' => 'Exito al '.$mOption.' la causa: '.$_REQUEST["nom_clasif"] ];
			}

			echo json_encode($mReturn);


		} 
		catch (Exception $e) 
		{
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
			$mNew[$mCell] =  str_replace(array("\n", "_x000D_"), array(""," "), $mRow[$mKey]);
		} 

		// Ejecuta homologacion de algunos datos
		$mNew["cod_tipdes"] = self::$cCodTipdes[$mNew["cod_tipdes"]]['cod_tipdes'];
		$mNew["est_solici"] = self::$cEstSolici[$mNew["est_solici"]]['est_solici'];
		$mNew["ind_cumple"] = self::$cIndCumple[$mNew["ind_cumple"]]['ind_cumple'];
		$mNew["cod_negoci"] = self::$cCodNegoci[$mNew["nom_negoci"]]['cod_negoci']; // este es el mismo producto o mercancia de corona


		return $mNew;
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
						ind_cumple, ind_seguim, cod_causax, ind_asigna
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
				return $mSolici;
			}

		} 
		catch (Exception $e) 
		{
			
		}
	}

}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new insDescripcion();
else
	$_INFORM = new insDescripcion($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);


?>
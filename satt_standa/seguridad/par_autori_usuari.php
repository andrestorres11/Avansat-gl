<?php
/*! \file: par_autori_usuari.php
 *  \brief: Inserta, lista, actualiza Autorizaciones adicionales por usuario
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 22/02/2016
 *  \bug: 
 *  \warning: 
 */

class LisAutoriUsuari
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co, $us, $ca)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}

		switch($_REQUEST['Option']){
			case 'editAutoriUsuari':
				self::editAutoriUsuari();
				break;

			case 'saveAutoriUsuari':
				self::saveAutoriUsuari();
				break;

			default:
				self::lista();
				break;
		}
	}

	

	/*! \fn: lista
	 *  \brief: Lista Usuarios Parametrizados
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/02/2016
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function lista(){
		$mSql = "SELECT a.cod_consec, b.cod_usuari 
				   FROM ".BASE_DATOS.".tab_autori_usuari a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_usuari b 
					 ON a.cod_conusu = b.cod_consec ";

		$_SESSION["queryXLS"] = $mSql;

		if(!class_exists(DinamicList))
			include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");

		$mList = new DinamicList(self::$cConexion, $mSql, "a.cod_consec" , "no", 'ASC');
		$mList->SetClose('no');
		$mList->SetCreate("Agregar", "onclick:editAutoriUsuari('new')");
		$mList->SetHeader(utf8_decode("Codigo"), "field:a.cod_consec; width:25%;  ");
		$mList->SetHeader(utf8_decode("Nombre"), "field:b.cod_usuari; width:25%;  ");
		$mList->SetOption(utf8_decode("Opciones"),"width:1%; onclikEdit:editAutoriUsuari( 'update', this );" );

		$mList->Display(self::$cConexion);

		$_SESSION["DINAMIC_LIST"] = $mList;


		#HTML
		$mHtml = new Formlib(2);

		$mHtml->SetJs("jquery");
		$mHtml->SetJs("functions");
		$mHtml->SetJs("par_autori_usuari");
		$mHtml->SetJs("dinamic_list");

		$mHtml->SetCss("jquery");
		$mHtml->SetCss("dinamic_list");

		$mHtml->CloseTable('tr');
		$mHtml->Table("tr");
			$mHtml->SetBody("<td>");

				$mHtml->OpenDiv("id:contentID; class:contentAccordion");
					$mHtml->OpenDiv("id:responID; class:accordion");
						$mHtml->SetBody("<h3 style='padding:6px;'><center>AUTORIZACIONES USUARIOS</center></h3>");
						$mHtml->OpenDiv("id:secID");
							$mHtml->OpenDiv("id:sub_responID; class:contentAccordionForm");
								$mHtml->SetBody( $mList->GetHtml() );

								$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>"central") );
								$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
								$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();

			$mHtml->SetBody('</td>');
		$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: editAutoriUsuari
	 *  \brief: Crea formulario para editar o guardar nuevas Autorizaciones por usuario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/02/2016
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: HTML
	 */
	private function editAutoriUsuari()
	{
		$mSql = "SELECT a.jso_autori, a.cod_conusu
				   FROM ".BASE_DATOS.".tab_autori_usuari a 
				  WHERE a.cod_consec = '".$_REQUEST['cod_consec']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix('a');

		if( sizeof($mData) < 1 )
			$mUsuari = self::getListUsuari();
		else{
			$mData = $mData[0];
			$mData['jso_autori'] = json_decode($mData['jso_autori']);

			$mUsuari = self::getListUsuari( $mData['cod_conusu'] );
		}

		$mHtml = new Formlib(2);
		$mHtml->SetCss("jquery");
		$mHtml->SetCss("informes");
		$mHtml->OpenDiv("id:DatosBasicosID; class:contentAccordionForm");
			$mHtml->Table("tr");
				$mHtml->Label( "Datos Basicos de la Autorización", array("colspan"=>"4", "align"=>"center", "class"=>"CellHead") );
				$mHtml->CloseRow();

				$mHtml->Row();
				$mHtml->Label( "Autorización:", array("align"=>"right", "width"=>"50%", "class"=>"cellInfo2") );

			if( sizeof($mData) < 1 )
				$mHtml->Select2(array_merge(self::$cNull, $mUsuari), array("name"=>"cod_conusu", "id"=>"cod_conusuID", "class"=>"cellInfo2") );
			else{
				$mHtml->Label( $mUsuari[0][1], array("align"=>"left", "width"=>"50%", "class"=>"cellInfo2") );
				$mHtml->Hidden( array("name"=>"cod_conusu", "id"=>"cod_conusuID", "value"=>$mData['cod_conusu']) );
			}

				$mHtml->Hidden( array("name"=>"cod_consec", "id"=>"cod_consecID", "value"=>$_REQUEST['cod_consec']) );
			$mHtml->CloseTable('tr');


			$mArray = self::armaArray();

			$mHtml->SetBody('<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center">');

			foreach ($mArray[0] as $key => $value)
			{#Recorre las Autorización
				$mHtml->Row();
				$mHtml->Label( $mArray[0][$key]['name'], array("colspan"=>"2", "align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
				$mHtml->CheckBox( array("colspan"=>"2", "name"=>"jso_autori[$key][ind_visibl]", "id"=>$key."ID", "value"=>"1", "checked"=>$mData['jso_autori']->$key->ind_visibl, "width"=>"25%", "class"=>"cellInfo2") );

				if( sizeof($mArray[0][$key]['sub']) > 0 ){
					$i=0;
					foreach ($mArray[0][$key]['sub'] as $keySub => $valSub)
					{#Recorre las subsecciones
						if( ($i % 2) == 0 ){
							$mHtml->CloseRow();
							$mHtml->Row();
						}
						$mHtml->Label( $mArray[0][$key]['sub'][$keySub], array("align"=>"right", "width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->CheckBox( array("name"=>"jso_autori[$key][sub][".$keySub."]", "id"=>$keySub."ID", "value"=>"1", "checked"=>$mData['jso_autori']->$key->sub->$keySub, "width"=>"25%", "class"=>"cellInfo1") );
						$i++;
					}
					if( ($i % 2) != 0 )
						$mHtml->Label( "&nbsp;", array("colspan"=>"2", "align"=>"right", "width"=>"25%", "class"=>"cellInfo1") );	
				}
			}
			$mHtml->CloseTable('tr');

			$mHtml->Table("tr");
				$mHtml->Button( array("align"=>"center", "value"=>"Guardar", "onclick"=>"saveAutoriUsuari()", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all") );

				$mHtml->Hidden( array("name"=>"cod_respon", "id"=>"cod_responID", "value"=>$_REQUEST['cod_respon']) );
				$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>"central") );
				$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
				$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
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
	private function armaArray()
	{
		$mArray[0] = array( "edi_noveda"=> array( "name"=>"Editar Novedad", 
												  "sub"=>array()
												),
							"inf_tradia"=> array( "name"=>"Fecha y hora seguimiento trazabilidad Diaria", 
												  "sub"=>array()
												)
						  );

		$mArray[1] = array( "edi_noveda"=>array("ind_visibl"=>1, "sub"=>array() ),
							"inf_tradia"=>array("ind_visibl"=>1, "sub"=>array() )
						  );

		return $mArray;
	}

	/*! \fn: saveAutoriUsuari
	 *  \brief: actualiza o guarda las autorizaciones especificas por usuario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 23/02/2016
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: HTML
	 */
	private function saveAutoriUsuari(){
		if( $_REQUEST['cod_consec'] == '' )
		{ #Nuevo Registro
			$mSql = "INSERT INTO ".BASE_DATOS.".tab_autori_usuari 
						( cod_conusu, jso_autori, 
						  usr_creaci, fec_creaci ) 
					VALUES 
						( '".$_REQUEST['cod_conusu']."', '".json_encode($_REQUEST['jso_autori'])."', 
						  '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() ) 	
					";
			$mConsult = new Consulta($mSql, self::$cConexion);
		}
		else
		{
			$mSql = "UPDATE ".BASE_DATOS.".tab_autori_usuari 
					SET jso_autori = '".json_encode($_REQUEST['jso_autori'])."', 
						usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
						fec_modifi = NOW() 
					WHERE cod_consec = '".$_REQUEST['cod_consec']."' ";
			$mConsult = new Consulta($mSql, self::$cConexion);
		}

		if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
			$mensaje = "<font color='#000000'>Se Guardo la Autorización Exitosamente.<br></font>";
			$mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
			$mens = new mensajes();
			echo $mens->correcto2("AUTORIZACIONES", $mensaje);
		}else{
			$mensaje = "<font color='#000000'>Ocurrió un Error Inesperado al Guardar la Autorización</font>";
			$mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
			$mens = new mensajes();
			echo $mens->error2("AUTORIZACIONES", $mensaje);
		}
	}

	/*! \fn: getListUsuari
	 *  \brief: Trae los usuarios activos
	 *  \author: Ing. Fabian Salinas
	 *  \date:  23/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getListUsuari( $mCodConsec = null ){
		$mSql =	"SELECT a.cod_consec, a.cod_usuari 
				   FROM ".BASE_DATOS.".tab_genera_usuari a 
				  WHERE a.ind_estado = 1 ";

		if( $mCodConsec == null ){
			$mSql .= "  AND a.cod_consec NOT IN (   SELECT b.cod_conusu 
													  FROM ".BASE_DATOS.".tab_autori_usuari b 
												) ";
		}else{
			$mSql .= " AND a.cod_consec = $mCodConsec ";
		}
		
		$mSql .= " ORDER BY a.cod_usuari ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		return $mConsult->ret_matrix('i');
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new LisAutoriUsuari();
else
	$_INFORM = new LisAutoriUsuari( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
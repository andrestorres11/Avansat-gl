<?php

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');

class infBandeja
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$cDespac,
					$cServic, 
					$cNull = array( array('', '-----') );

	private static $cLisNavega = array( array('Mozilla', 'Mozilla Firefox'), 
										array('IE', 	 'Internet Explorer'), 
										array('Chrome',  'Google Chrome'), 
										array('Safari',  'Safari') 
									  );

	private static $cTipErrorx = array(  );

	function __construct($co = NULL, $us = NULL, $ca = NULL)
	{

		self::$cTipErrorx = array( # son de soporte
										array('132', 'Ajustes Sistema'), 
										array('130', 'Cambios de Informacion'), 
										array('8',	 'Error del Sistema') , 
										# Son de Faro
										array('286',	 'Creacion de usuarios') ,
										array('287',	 'Actualizacion de Claves') ,
										array('288',	 'Creacion de Ruta') ,
										array('289',	 'Vehiculo Recomendado') ,
										array('290',	 'Actualizacion de Causas') ,
										array('291',	 'Eliminacion de Causas') ,
										array('292',	 'Actas Operativas') ,
										array('293',	 'Analisis general de un Despacho') ,
										array('294',	 'Creacion masiva de Usuarios') ,
										array('295',	 'Creacion masiva de Rutas') ,
										array('296',	 'Plan de accion correctivas') ,
										array('297',	 'Solicitud de Grabaciones') ,
										array('298',	 'PQR del servicio (Plan de Mejoras)') ,
										array('299',	 'Analisis de Auditorias y (plan de Mejoras)') 
										 
									  );



		if($_REQUEST["Ajax"] == 'on') {			 
			include('../lib/ajax.inc');
			self::$cConexion  = $AjaxConnection;
			self::$cUsuario   = $_SESSION["datos_usuario"];
			self::$cCodAplica = "1";		 
		}  
		else {
			self::$cConexion = $co;
			self::$cUsuario = $us;		
			self::$cCodAplica = $ca;
		}
		
 
		 

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/bootstrap.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

		switch($_REQUEST['option'])
		{
			case "sendTask": 
				self::sendTask(); 
				break;
			default:
				self::buildForm();
				break;
		}
	}

	private function buildForm()
	{	

		
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php' );

		IncludeJS( 'jquery.js' );
		IncludeJS( 'ins_soport_solici.js' );


		self::getServicNivel(); #Llena la variable cServic

		self::$cDespac = new Despac( self::$cConexion, self::$cUsuario, self::$cCodAplica);
		$mServer = explode('.', $_SERVER['HTTP_HOST']);
		$mUsuTransp = self::$cDespac->getTransp();


		$mTipErrorx = array();

		if( sizeof($mUsuTransp) == 1 )
			$mUsuTransp = $mUsuTransp[0];
		else
			$mUsuTransp = array();

		echo '	<style> 
					.Style3DIV {
						left: 10px !important;
						top: 10px !important;
						position: relative !important;
						width: 98% !important;
					}
				</style> ';

		$mHtml = new Formlib(2);

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("id:accordionID");
			$mHtml->SetBody("<h3 style='padding:6px;' class='ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Solicitudes a Mesa de Apoyo</b></h3>");
			$mHtml->OpenDiv( array("id"=>"secID", "class"=>"ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active") );
				$mHtml->SetBody('<form name="form_solici" id="form_soliciID" action="index.php" method="post" enctype="multipart/form-data">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV Style3DIV");
					$mHtml->Table('tr');
						$mHtml->Label( "<b>Solicitudes a Mesa de Apoyo</b>", array("class"=>"CellHead", "colspan"=>"8", "align"=>"left", "end"=>true) );

						$mHtml->Label( "* Nombre Completo: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Input( array("name"=>"nom_usuari", "id"=>"nom_usuariID", "class"=>"cellInfo1", "size"=>"15", "width"=>"25%", "disabled"=>"disabled", "value"=>$_SESSION['datos_usuario']['nom_usuari']) );
						$mHtml->Label( "* E-mail: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Input( array("name"=>"ema_usuari", "id"=>"ema_usuariID", "class"=>"cellInfo1", "size"=>"30", "width"=>"25%", "disabled"=>"disabled", "value"=>$_SESSION['datos_usuario']['usr_emailx'], "end"=>true) );

						$mHtml->Label( "Telefono Fijo: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Input( array("name"=>"tel_usuari", "id"=>"tel_usuariID", "class"=>"cellInfo1", "size"=>"15", "width"=>"25%") );
						$mHtml->Label( "* Telefono Celular: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Input( array("name"=>"cel_usuari", "id"=>"cel_usuariID", "class"=>"cellInfo1", "size"=>"15", "width"=>"25%", "end"=>true) );

						$mHtml->Label( "* Navegador: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Select2( array_merge(self::$cNull, self::$cLisNavega ), array("name"=>"nom_navega", "id"=>"nom_navegaID", "class"=>"cellInfo1", "width"=>"25%") );
						$mHtml->Label( "Servicio: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->SetBody( self::Select( array_merge(self::$cNull, self::$cServic) )."</tr>" );

						$mHtml->Label( "* Tipo de Error: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Select2( array_merge(self::$cNull, self::$cTipErrorx ), array("name"=>"cod_errorx", "id"=>"cod_errorxID", "class"=>"cellInfo1", "width"=>"25%") );
						$mHtml->Label( "* Asunto: ", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->Input( array("name"=>"obs_asunto", "id"=>"obs_asuntoID", "class"=>"cellInfo1", "size"=>"30", "width"=>"25%", "end"=>true) );

						$mHtml->Label( "* Mensaje:", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->TextArea( "", array("name"=>"obs_mensaj", "id"=>"obs_mensajID", "class"=>"cellInfo1", "rows"=>"3", "cols"=>"35") );
						$mHtml->Label( "Archivo Adjunto:", array("width"=>"25%", "class"=>"cellInfo1") );
						$mHtml->File( array("name"=>"doc_adjunt", "id"=>"doc_adjuntID", "class"=>"cellInfo1", "end"=>true) );

						$mHtml->Button( array("name"=>"bot_enviar", "id"=>"bot_enviarID", "class2"=>"cellInfo1", "value"=>" &nbsp;&nbsp; Enviar &nbsp;&nbsp; ", "colspan"=>"4", "align"=>"center", "onclick"=>"transmitir_spg( '".BASE_DATOS."' )", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only") );

						$mHtml->Hidden( array("name"=>"standar", 	"id"=>"standarID", 	  "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"dir_aplica", "id"=>"dir_aplicaID", "value"=>NOM_URL_APLICA) );
						$mHtml->Hidden( array("name"=>"nom_aplica", "id"=>"nom_aplicaID", "value"=>BASE_DATOS) );
						$mHtml->Hidden( array("name"=>"nit_transp", "id"=>"nit_transpID", "value"=>$mUsuTransp[0]) );
						$mHtml->Hidden( array("name"=>"nom_empres", "id"=>"nom_empresID", "value"=>$mUsuTransp[1]) );
						$mHtml->Hidden( array("name"=>"server", 	"id"=>"serverID", 	  "value"=>$mServer[0]) );
						$mHtml->Hidden( array("name"=>"option", 	"id"=>"optionID", 	  "value"=>"sendTask") );
						$mHtml->Hidden( array("name"=>"cod_servic", 	"id"=>"cod_servicID", 	  "value"=>$_REQUEST["cod_servic"]) );
						$mHtml->Hidden( array("name"=>"window", 	"id"=>"windowID", 	  "value"=>"central") ); 
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getServicNivel
	 *  \brief: Trae los servicios por niveles
	 *  \author: Ing. Fabian Salinas
	 *  \date:  12/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function getServicNivel()
	{
		$mSql = "SELECT a.cod_servic, a.nom_servic, '1' AS cod_nivelx 
				   FROM ".CENTRAL.".tab_genera_servic a 
			 INNER JOIN ".BASE_DATOS.".tab_perfil_servic b 
					 ON a.cod_servic = b.cod_servic 
				  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."'
				  	AND a.cod_servic NOT IN ( SELECT c.cod_serhij 
				  								FROM ".CENTRAL.".tab_servic_servic c 
				  							)
				";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('i');

		self::getServicChildren( $mResult, 2 );
	}

	/*! \fn: getServicChildren
	 *  \brief: Trae los servicios hijos
	 *  \author: Ing. Fabian Salinas
	 *  \date: dd/mm/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData   Array    Data Servic Padres
	 *  \param: mNivel  Integer  Nivel del servicio
	 *  \return: 
	 */
	private function getServicChildren( $mData = null, $mNivel )
	{
		for ($i=0; $i < sizeof($mData); $i++) { 
			self::$cServic[] = $mData[$i];

			$mSql = "SELECT a.cod_servic, a.nom_servic, '$mNivel' AS cod_nivelx 
					   FROM ".CENTRAL.".tab_genera_servic a 
				 INNER JOIN ".CENTRAL.".tab_servic_servic b 
						 ON a.cod_servic = b.cod_serhij 
				 INNER JOIN ".BASE_DATOS.".tab_perfil_servic c 
						 ON a.cod_servic = c.cod_servic 
					  WHERE b.cod_serpad = '".$mData[$i][0]."' 
						AND c.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."'
					";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('i');

			self::getServicChildren( $mResult, $mNivel+1 );
		}
	}

	/*! \fn: Select
	 *  \brief: Constructor de select Servicio
	 *  \author: Ing. Fabian Salinas
	 *  \date:  12/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData  Array  Servicios
	 *  \return: html
	 */
	private function Select( $mData = null )
	{
		$mTab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		$mHtml  = '<td id="cod_servicIDTD" class="Nivel1 cellInfo1" width="25%" valign="" height="" align="left" colspan="" rowspan="">';
			$mHtml .= '<select id="cod_servicxID" class="form_01" name="cod_servicx">';

			foreach ($mData as $i) {
				$Tab = '';
				for ($j=1; $j < $i[2]; $j++) { 
					$Tab .= $mTab;
				}

				$mHtml .= '<option value="'.$i[0].'">'.$Tab.$i[1].'</option>';
			}

			$mHtml .= '</select>';
		$mHtml .= '</td>';

		return $mHtml;
	}

	/*! \fn: sendTask
	 *  \brief: metodo de encargado de interactuar con la libreria de la conexion al ws de SPG
	 *  \author: Ing. Nelson Liberato
	 *  \date:  28/01/2016 
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData  Array  de REQUEST
	 *  \return: N/A
	 */
	private function sendTask( $mData = null )
	{
 		 
		include("../".DIR_APLICA_CENTRAL."/lib/InterfSPG.inc");
		$mSpgWs = new InterfSPG(self::$cConexion, $_REQUEST, $_FILES);

	 
		echo '	<style> 
					.Style3DIV {
						left: 10px !important;
						top: 10px !important;
						position: relative !important;
						width: 98% !important;
					}
				</style> ';

		$mHtml = new Formlib(2);

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("id:accordionID");
			$mHtml->SetBody("<h3 style='padding:6px;' class='ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Respuesta de Mesa de Apoyo</b></h3>");
			$mHtml->OpenDiv( array("id"=>"secID", "class"=>"ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active") );
				$mHtml->SetBody('<form name="form_solici" id="form_soliciID" action="index.php" method="post" enctype="multipart/form-data">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV Style3DIV");
					$mHtml->Table('tr');	
 
						if(InterfSPG::$cReturn["cod_respon"] == '1000') {
							$mHtml->Label( "<b>Señor usuario se ha creado con éxito su solicitud radicada con número: [ ".InterfSPG::$cReturn["msg_respon"]." ]  </b>", array("class"=>"CellHead", "colspan"=>"8", "align"=>"left", "end"=>true) );
						}
						else {
							$mHtml->Label( "<b>Señor usuario a ocurrido un error en su solicitud: [ ".InterfSPG::$cReturn["msg_respon"]." ] </b>", array("class"=>"CellHead", "colspan"=>"8", "align"=>"left", "end"=>true) );
						}
 
					 
						$mHtml->Hidden( array("name"=>"standar", 	"id"=>"standarID", 	  "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"dir_aplica", "id"=>"dir_aplicaID", "value"=>NOM_URL_APLICA) );
						$mHtml->Hidden( array("name"=>"nom_aplica", "id"=>"nom_aplicaID", "value"=>BASE_DATOS) );
						$mHtml->Hidden( array("name"=>"nit_transp", "id"=>"nit_transpID", "value"=>$mUsuTransp[0]) );
						$mHtml->Hidden( array("name"=>"nom_empres", "id"=>"nom_empresID", "value"=>$mUsuTransp[1]) );
						$mHtml->Hidden( array("name"=>"server", 	"id"=>"serverID", 	  "value"=>$mServer[0]) );
						$mHtml->Hidden( array("name"=>"option", 	"id"=>"optionID", 	  "value"=>"sendTask") );
						$mHtml->Hidden( array("name"=>"cod_servic", 	"id"=>"cod_servicID", 	  "value"=>$_REQUEST["cod_servic"]) );
						$mHtml->Hidden( array("name"=>"window", 	"id"=>"windowID", 	  "value"=>"central") ); 
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();

		 

	}
}

	if($_REQUEST["Ajax"] == 'on') {
		$_INFORM = new infBandeja(   );
	}
	else {
		$_INFORM = new infBandeja( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
	} 

?>
<?php
/*! \file: ajax_notifi_notifi.php
 *  \brief: procesos para la generacion de informacion
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 05/01/2017
 *  \bug: 
 *  \warning: 
 */
//ini_set('memory_limit', '512M');
//ini_set('display_errors', true);

/*! \class: AjaxNotifiNotifi
 *  \brief: ajax
 */
class AjaxNotifiNotifi
{
  	private static  $cConexion,
	                $cCodAplica,
	                $cUsuario;

	public function __construct()
	{
		$_AJAX=$_REQUEST;
	    @include_once( "../lib/ajax.inc" );
	    @include_once( "../lib/general/constantes.inc" );
	    @include_once( "../lib/general/functions.inc" );
	    self::$cConexion = $AjaxConnection;

		switch($_AJAX['option'])
		{
		    case 'getFormGeneral':
		       	self::getFormGeneral();
		    break;

			case 'getForm':
		       	self::getForm();
		    break;

			case 'getFormNuevaNotifi':
		       	self::getFormNuevaNotifi();
		    break;

		    case 'getNomUsuario':
		       	self::getNomUsuario();
		    break;

		    case 'NuevaNotifiComun':
		       	self::NuevaNotifiComun();
		    break;

		    case 'NuevaNotifiExten':
		    	self::NuevaNotifiExten();
	    	break;

	    	case 'EditNotifiComun':
		       	self::EditNotifiComun();
		    break;

		    case 'EditNotifiExten':
		    	self::EditNotifiExten();
	    	break;

	    	case 'elimiNotifi':
		    	self::elimiNotifi();
	    	break;

	    	case 'responderNotifi':
		    	self::responderNotifi();
	    	break;

	    	case 'getRefDocumet':
		    	self::getRefDocumet();
	    	break;

		    default:
		      	#header('Location: index.php?window=central&cod_servic=20151235&menant=20151235');
		    break;
		}
	}

	/*! \fn: getFormGeneral
	 *  \brief: retorna formulario general
	 *  \author: Edward Serrano
	 *	\date:  05/01/2017
	 *	\date modified: dia/mes/año
	 */
	public function getFormGeneral()
	{
		$datos = (object) $_POST;
		$titulos = array('Nivel1' => array('Generados' => 'Generados', 'OET' => 'Informacion OET', 'PorcOET' => '%', 'CFL' => 'Informacion CFL', 'PorcCFL' => '%', 'SUPER' => 'Informacion Supervisores', 'PorSUPER' => '%', 'CONTRO' => 'Informacion Controladores', 'PorcCONTRO' => '%', 'CLIENT' => 'Informacion Clientes', 'PorcCLIENT' => '%', 'OTROS' => 'OTROS', 'PorcOTROS' => '%', ), 'Nivel2' => array());
		$mHtml = new FormLib();
		#print_r($datos->fec_iniID);
		#echo "fecha inicial: ". $datos->fec_iniID ."; fecha fin: ". $datos->fec_finID;
		$mHtml->Table("tr");
			$mHtml->Label( "NOTIFICACIONES GENERADAS EN EL PERIDODO DEL ". $datos->fec_iniID. " AL ".$datos->fec_finID, array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
			$mHtml->CloseRow();
				foreach ($titulos as $keyN1 => $valueN1) {
					if($keyN1=='Nivel1')
					{
						$mHtml->Row();
						foreach ($valueN1 as $keySub1 => $valueSub1) {
							$mHtml->Label( $valueSub1,  array("align"=>"right", "class"=>"celda_titulo") );	
						}
						$mHtml->CloseRow();

					}	
				}	
		$mHtml->CloseTable('tr');
		echo $mHtml->MakeHtml();
	}

	/*! \fn: getGeneralNotifi
	 *  \brief: retorna consulta general de notificaciones
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getGeneralNotifi(){
		$sql = "SELECT 'cod_notifi', COUNT('cod_notifi') FROM 'tab_notifi_notifi' GROUP BY cod_notifi";
	}

	/*! \fn: getNotifi
	 *  \brief: retorna consulta de notificaciones por tipo
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getNotifi($mDatosGN){
		$mSql = "SELECT a.cod_notifi,a.nom_asunto,a.fec_creaci,b.cod_usuari
					 FROM ".BASE_DATOS.".tab_notifi_notifi a
					 	INNER JOIN ".BASE_DATOS.".tab_genera_usuari b 
					 		ON a.usr_creaci=b.cod_consec
					 			WHERE a.cod_tipnot=".$mDatosGN->cod_notifi." AND a.fec_creaci>='".$mDatosGN->fec_iniID."' AND a.fec_creaci<='".$mDatosGN->fec_finID." 23:59:59'";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult;
	}

	/*! \fn: getNotifi
	 *  \brief: retorna consulta de notificaciones por tipo
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getCodUsuario($cod_usuari){
		$mSql = "SELECT a.cod_consec
					 FROM ".BASE_DATOS.".tab_genera_usuari a
					 	WHERE a.cod_usuari='".$cod_usuari."'";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult[0];
	}

	/*! \fn: getForm
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getForm()
	{
		$datos = (object) $_POST;
		$titulos = array('Nivel1' => array('Consecutivo' => 'Consecutivo', 'Asunto' => 'Asunto', 'Fecha' => 'Fecha y hora', 'notificacion' => 'Notificado por'));
		$mHtml = new Formlib(2);
		$mHtml->Table("tr",array("class"=>"displayDIV2"));
			$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
			$mHtml->CloseRow();
				#Opciones para la pestana OET
				if($datos->cod_notifi==1)
				{
					if(self::getmPermOet()['ins']==1)
					{
						$mHtml->Row();
							$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNoetID","name"=>"btnNoet", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(1)") );
						$mHtml->CloseRow();	
					}
					$mHtml->OpenDiv("id:sec1");
						$mHtml->OpenDiv("id:DinamicListDIV");
						echo self::getDinamiList(self::getmPermOet(),$datos);
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				}
				#Opciones para la pestana faro
				if($datos->cod_notifi==2)
				{
					if(self::getmPermClf()['ins']==1)
					{
						$mHtml->Row();
							$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNclfID","name"=>"btnNclf", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(2)") );
						$mHtml->CloseRow();
					}
					$mHtml->OpenDiv("id:sec2");
						$mHtml->OpenDiv("id:DinamicListDIV");
						echo self::getDinamiList(self::getmPermClf(),$datos);
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				}
				#Opciones para la pestana supervisores
				if($datos->cod_notifi==3)
				{
					if(self::getmPermSup()['ins']==1)
					{
						$mHtml->Row();
							$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNsupID","name"=>"btnNsup", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(3)") );
						$mHtml->CloseRow();
					}
					$mHtml->OpenDiv("id:sec3");
						$mHtml->OpenDiv("id:DinamicListDIV");
						echo self::getDinamiList(self::getmPermSup(),$datos);
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();

				}
				#Opciones para la pestana controladores
				if($datos->cod_notifi==4)
				{
					if(self::getmPermCon()['ins']==1)
					{
						$mHtml->Row();
							$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNconID","name"=>"btnNcon", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(4)") );
						$mHtml->CloseRow();
					}
					$mHtml->OpenDiv("id:sec4");
						$mHtml->OpenDiv("id:DinamicListDIV");
						echo self::getDinamiList(self::getmPermCon(),$datos);
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
					
				}
				#Opciones para la pestana clientes
				if($datos->cod_notifi==5)
				{
					if(self::getmPermCli()['ins']==1)
					{
						$mHtml->Row();
							$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNcliID","name"=>"btnNcli", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(5)") );
						$mHtml->CloseRow();
					}
					$mHtml->OpenDiv("id:sec5");
						$mHtml->OpenDiv("id:DinamicListDIV");
						echo self::getDinamiList(self::getmPermCli(),$datos);
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
					
				}
		$mHtml->CloseTable('tr');
		$mHtml->OpenDiv("id:popID");
		$mHtml->CloseDiv();
		#echo "<pre>";print_r($_SESSION);echo "</pre>";
		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDinamiList
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  18/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getDinamiList($permisosActuales,$datos)
	{	
		$permActivado="field:cod_notifi; width:1%";
		$permActivado.=($permisosActuales['idi']==1)?";onclikEdit:editarNotifi( this )":"";
		$permActivado.=($permisosActuales['rep']==1)?";onclickCopy:FormResponNotifi( this )":"";
		$permActivado.=($permisosActuales['eli']==1)?";onclikPrint:FormeliminarNotifi( this )":"";
		#print_r($permActivado);
		if (!class_exists('DinamicList')) 
		{
		    include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
		}
		$sql ="SELECT a.cod_notifi,a.nom_asunto,a.fec_creaci,b.cod_usuari,a.cod_tipnot,a.ind_notres,a.ind_notusr
				 FROM ".BASE_DATOS.".tab_notifi_notifi a
				 	INNER JOIN tab_genera_usuari b 
				 		ON a.usr_creaci=b.cod_consec
				 			WHERE a.ind_estado=1 AND a.cod_tipnot=".$datos->cod_notifi." and a.fec_creaci>='".$datos->fec_iniID."' and a.fec_creaci<='".$datos->fec_finID." 23:59:59'";
		$list = new DinamicList(self::$cConexion, $sql, "2", "no", 'ASC');
		$list->SetClose('no');
		//$list->SetCreate("Crear Perfil", "onclick:formulario()");
		$list->SetHeader("Consecutivo", "field:cod_notifi; width:1%;  ");
		$list->SetHeader("Asunto", "field:nom_asunto; width:1%");
		$list->SetHeader("Fecha y hora", "field:fec_creaci; width:1%");
		$list->SetHeader("Notificado por ", "field:cod_usuari; width:1%");
		$list->SetOption("Opciones", $permActivado);
		$list->SetHidden("cod_notifi", "cod_notifi");
        $list->SetHidden("cod_tipnot", "cod_tipnot");
        $list->SetHidden("nom_asunto", "nom_asunto");
        $list->SetHidden("ind_notres", "ind_notres");
        $list->SetHidden("ind_notusr", "ind_notusr");
		$list->Display(self::$cConexion);
		return $list->GetHtml();
	}

	/*! \fn: getFormNuevaNotifi
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifi()
	{	
		$datos = (object) $_REQUEST;
		if($datos->idForm=="3" || $datos->idForm=="4")
		{
			self::getFormNuevaNotifiExt($datos);
		}
		else
		{
			self::getFormNuevaNotifiComun($datos);
		}
	}

	/*! \fn: getFormNuevaNotifiExt
	 *  \brief: identifica el formulario correspondiete a supervisores y controladores y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifiExt($ActionForm=NULL)
	{
		$datosConsult=NULL;
		$readonly ="";
		$disabled ="";
		$Json=NULL;
		$SUPERVISORES=NULL;
		$CONTROLADORES=NULL;
		$ENCUESTAS=NULL;
		$ESPECIFICAS=NULL;
		$ASISTENCIAS=NULL;
		$ESTADO_VEHICULOS=NULL;
		$RECURSOS_ASIGNADOS=NULL;
		if($ActionForm->ActionForm=="idi")
		{
			#realizar consulta
			$datosConsult =self::getInfoNotifiEdit($ActionForm);
			$Json=json_decode($datosConsult[0][13]);
			$SUPERVISORES = self::JsonRecor($Json,"SUPERVISORES");
			$CONTROLADORES = self::JsonRecor($Json,"CONTROLADORES");
			$ENCUESTAS = self::JsonRecor($Json,"ENCUESTAS");
			$ESPECIFICAS = self::JsonRecor($Json,"ESPECIFICAS");
			$ASISTENCIAS = self::JsonRecor($Json,"ASISTENCIAS");
			$ESTADO_VEHICULOS = self::JsonRecor($Json,"ESTADO_VEHICULOS");
			$RECURSOS_ASIGNADOS = self::JsonRecor($Json,"RECURSOS_ASIGNADOS");
		}
		if($ActionForm->ActionForm=="eli" || $ActionForm->ActionForm=="rep")
		{
			#realizar consulta
			$datosConsult = self::getInfoNotifiEdit($ActionForm);
			$readonly = "readonly";
			$disabled = "disabled";
			$Json=json_decode($datosConsult[0][13]);
			$SUPERVISORES = self::JsonRecor($Json,"SUPERVISORES");
			$CONTROLADORES = self::JsonRecor($Json,"CONTROLADORES");
			$ENCUESTAS = self::JsonRecor($Json,"ENCUESTAS");
			$ESPECIFICAS = self::JsonRecor($Json,"ESPECIFICAS");
			$ASISTENCIAS = self::JsonRecor($Json,"ASISTENCIAS");
			$ESTADO_VEHICULOS = self::JsonRecor($Json,"ESTADO_VEHICULOS");
			$RECURSOS_ASIGNADOS = self::JsonRecor($Json,"RECURSOS_ASIGNADOS");
		}
		$date = new DateTime();
		$mHtml = new Formlib(2);
		$mHtml->OpenDiv("id:newNotifi");
			$mHtml->Hidden(array( "name" => "usr_creaci", "id" => "usr_creaciID", "value"=>self::getCodUsuario($_SESSION['datos_usuario']['cod_usuari'])['cod_consec']));
			$mHtml->Hidden(array( "name" => "cod_tipnot", "id" => "cod_tipnotID", "value"=>$ActionForm->idForm));
			$mHtml->Hidden(array( "name" => "cod_notifi", "id" => "cod_notifiID", "value"=>($ActionForm->cod_notifi!="")?$ActionForm->cod_notifi:""));
			$mHtml->Table("tr");
				#Radio buton entrega de turno
				$mHtml->Row();
					$mHtml->Radio(array("value"=>"1","name" => "ind_enttur", "id" => "ind_entturID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="ins")?"checked":($datosConsult[0][9]==1)?"checked":"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Label( "ENTREGA DE TURNO",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Radio(array("value"=>"2","name" => "ind_enttur", "id" => "ind_entturID", "width" => "100%", "colspan"=>"2"));
					$mHtml->Label( "OTRA NOTIFICACION",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"3","checked"=>($ActionForm->ActionForm=="idi")?($datosConsult[0][9]==2)?"checked":"":"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				#Cuerpo de la notificacion
				$mHtml->Row();
					$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
	                $mHtml->Input(array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%", "value"=>($datosConsult[0][4]!="")?$datosConsult[0][4]:"" ,"colspan"=>"6", "readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "Fecha de Notificacion:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
                	$mHtml->Input(array("value"=>$date->format('Y-m-d H:i:s'),"name" => "fec_creaci", "id" => "fec_creaciID", "width" => "100%", "readonly"=>"readonly", "disabled"=>"disabled","colspan"=>"6"));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "Notificado por:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
                	$mHtml->Input(array("value"=>$_SESSION['datos_usuario']['cod_usuari'],"name" => "usr_creaci", "id" => "usr_creaciID", "width" => "100%", "readonly"=>"readonly", "disabled"=>"disabled", "colspan"=>"4"));
                	$mHtml->Label( "Horas laboradas:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
                	$mHtml->Input(array("name" => "num_horlab", "id" => "num_horlabID", "width" => "100%", "colspan"=>"3", "value"=>($datosConsult[0][7]!="")?$datosConsult[0][7]:"", "readonly"=>($datosConsult[0][7]!="")?"readonly":"", "disabled"=>($datosConsult[0][7]!="")?"disabled":""));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "*Publicar a:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Select2 (self::getLisRespon(),  array("name" => "cod_asires", "width" => "25%","colspan"=>"1") );
					#si es supervisor pinta campos adicionales
					if($ActionForm->idForm==3){
						$mHtml->Label( "Usuarios:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Select2 ("",  array("name" => "ind_notusr", "width" => "25%","colspan"=>"4") );
					}
					else
					{
						$mHtml->Label( "",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"5") );
					}
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "*Vigencia hasta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Input(array("name" => "fec_vigenc", "id" => "fec_vigencID", "width" => "100%", "colspan"=>"1", "value"=>($datosConsult[0][6]!="")?$datosConsult[0][6]:"","onclick"=>($datosConsult[0][6]!="")?"":"getFechaDatapick('fec_vigencID')", "readonly"=>($datosConsult[0][6]!="" || $readonly!="")?"readonly":"","disabled"=>($datosConsult[0][6]!="" || $readonly!="")?$disabled:""));
					$mHtml->Label( "*Requiere Respuesta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "SI",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Radio(array("value"=>"1","name" => "ind_respue", "id" => "ind_respuesID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="ins")?"checked":($datosConsult[0][7]==1)?"checked":"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Label( "NO",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Radio(array("value"=>"0","name" => "ind_respue", "id" => "ind_respuenID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="idi")?($datosConsult[0][7]==0)?"checked":"":"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		#Formulario de diligenciamiento
		#si es supervisor pinta campos adicionales
		if($ActionForm->idForm==3){
			$mHtml->OpenDiv("id:jsonFormDigi");
				$mHtml->Table("tr");
					$mHtml->Row();
						$mHtml->Label( "*FORMULARIO DE DILIGENCIAMIENTO:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					
					$mHtml->Row();
						$mHtml->Label( "SUPERVISORES",  array("align"=>"center", "class"=>"celda_titulo infJson","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "Supervisor Entrante",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
						$mHtml->Label( "Controlador Master Entrante",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
						$mHtml->Label( "Supervisor Saliente",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Input(array("name" => "supe_entrante", "id" => "supe_entranteID", "width" => "100%", "colspan"=>"2", "value"=>(self::JsonRecor($SUPERVISORES,"supe_entrante")!="")?self::JsonRecor($SUPERVISORES,"supe_entrante"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "cont_Mentrant", "id" => "cont_MentrantID", "width" => "100%", "colspan"=>"2","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "supe_saliente", "id" => "supe_salienteID", "width" => "100%", "colspan"=>"3","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->CloseRow();
				$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:jsonContro");
				$mHtml->Table("tr");
					$mHtml->Row();
						$mHtml->Label( "CONTROLADORES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "En Turno",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Ausentes",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Incapacitados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Input(array("name" => "numb_enturno", "id" => "numb_enturnoID", "width" => "10%", "colspan"=>"1","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "cont_enturno", "id" => "cont_enturnoID", "width" => "100%", "colspan"=>"1","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_ausente", "id" => "numb_ausenteID", "width" => "10%", "colspan"=>"1","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "cont_ausente", "id" => "cont_ausenteID", "width" => "100%", "colspan"=>"1","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_incapac", "id" => "numb_incapacID", "width" => "10%", "colspan"=>"1","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "supe_incapac", "id" => "supe_incapacID", "width" => "100%", "colspan"=>"2","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->CloseRow();
				$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
		}
		$mHtml->OpenDiv("id:jsonEstVehi");
			$mHtml->Table("tr");
				$mHtml->Row();
					$mHtml->Label( "ESTADO DE VEHICULOS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Cargue",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Transito",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Descargue",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "numb_cargue", "id" => "numb_cargueID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_cargue")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_cargue"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_cargue", "id" => "vehi_cargueID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_cargue")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_cargue"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_transi", "id" => "numb_transiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_transi")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_transi"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_transi", "id" => "vehi_transiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_transi")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_transi"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_descar", "id" => "numb_descarID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_descar")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_descar"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_descar", "id" => "vehi_descarID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_descar")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_descar"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "A cargo de empresa",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Pendientes por dar llegada",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "En Pernotacion",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "numb_carempr", "id" => "numb_caremprID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_carempr")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_carempr"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_carempr", "id" => "vehi_caremprID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_carempr")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_carempr"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_penlleg", "id" => "numb_penllegID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_penlleg")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_penlleg"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_penlleg", "id" => "vehi_penllegID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_penlleg")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_penlleg"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_pernota", "id" => "numb_pernotaID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_pernota")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_pernota"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_pernota", "id" => "vehi_pernotaID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_pernota")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_pernota"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "En Seguimiento Especial",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Recomendados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Preventivo",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "numb_seguesp", "id" => "numb_seguespID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_seguesp")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_seguesp"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_seguesp", "id" => "vehi_seguespID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_seguesp")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_seguesp"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_recomen", "id" => "numb_recomenID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_recomen")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_recomen"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_recomen", "id" => "vehi_recomenID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_recomen")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_recomen"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_prevent", "id" => "numb_preventID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_prevent")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_prevent"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_prevent", "id" => "vehi_preventID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_prevent")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_prevent"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Hurtados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "Accidentados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "En transbordo",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "numb_hurtado", "id" => "numb_hurtadoID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_hurtado")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_hurtado"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_hurtado", "id" => "vehi_hurtadoID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_hurtado")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_hurtado"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_acciden", "id" => "numb_accidenID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_acciden")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_acciden"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_acciden", "id" => "vehi_accidenID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_acciden")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_acciden"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_transbo", "id" => "numb_transboID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_transbo")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_transbo"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "vehi_transbo", "id" => "vehi_transboID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_transbo")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_transbo"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		#si es supervisor pinta campos adicionales
		if($ActionForm->idForm==3){
			$mHtml->OpenDiv("id:jsonEncu");
				$mHtml->Table("tr");
					$mHtml->Row();
						$mHtml->Label( "ENCUENTAS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Realizadas",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Registradas",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Por subir a SPG",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Input(array("name" => "numb_enreali", "id" => "numb_enrealiID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUENTAS,"numb_enreali")!="")?self::JsonRecor($ENCUENTAS,"numb_enreali"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "encu_enreali", "id" => "encu_enrealiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUENTAS,"encu_enreali")!="")?self::JsonRecor($ENCUENTAS,"encu_enreali"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_registr", "id" => "numb_registrID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUENTAS,"numb_registr")!="")?self::JsonRecor($ENCUENTAS,"numb_registr"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "encu_registr", "id" => "encu_registrID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUENTAS,"encu_registr")!="")?self::JsonRecor($ENCUENTAS,"encu_registr"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_subaspg", "id" => "numb_subaspgID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUENTAS,"numb_subaspg")!="")?self::JsonRecor($ENCUENTAS,"numb_subaspg"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "encu_subaspg", "id" => "encu_subaspgID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ENCUENTAS,"encu_subaspg")!="")?self::JsonRecor($ENCUENTAS,"encu_subaspg"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->CloseRow();
				$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:jsonEspeci");
				$mHtml->Table("tr");
					$mHtml->Row();
						$mHtml->Label( "ESPECIFICAS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Realizadas",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Consecutivo",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Pendientes por despacho",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Pendientes por atender",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Input(array("name" => "numb_esreali", "id" => "numb_esrealiID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_esreali")!="")?self::JsonRecor($ESPECIFICAS,"numb_esreali"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "espe_esreali", "id" => "espe_esrealiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"espe_esreali")!="")?self::JsonRecor($ESPECIFICAS,"espe_esreali"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_esconsc", "id" => "numb_esconscID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_esconsc")!="")?self::JsonRecor($ESPECIFICAS,"numb_esconsc"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_espende", "id" => "numb_espendeID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_espende")!="")?self::JsonRecor($ESPECIFICAS,"numb_espende"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "espe_espende", "id" => "espe_espendeID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"espe_espende")!="")?self::JsonRecor($ESPECIFICAS,"espe_espende"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_espenda", "id" => "numb_espendaID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_espenda")!="")?self::JsonRecor($ESPECIFICAS,"numb_espenda"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "espe_espenda", "id" => "espe_espendaID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"espe_espenda")!="")?self::JsonRecor($ESPECIFICAS,"espe_espenda"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->CloseRow();
				$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:jsonAsist");
				$mHtml->Table("tr");
					$mHtml->Row();
						$mHtml->Label( "ASISTENCIAS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Realizadas",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Consecutivo",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Pendientes por despacho",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
						$mHtml->Label( "Pendientes por atender",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Input(array("name" => "numb_asreali", "id" => "numb_asrealiID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_asreali")!="")?self::JsonRecor($ASISTENCIAS,"numb_asreali"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "espe_asreali", "id" => "espe_asrealiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"espe_asreali")!="")?self::JsonRecor($ASISTENCIAS,"espe_asreali"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_asconsc", "id" => "numb_asconscID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_asconsc")!="")?self::JsonRecor($ASISTENCIAS,"numb_asconsc"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_aspende", "id" => "numb_aspendeID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_aspende")!="")?self::JsonRecor($ASISTENCIAS,"numb_aspende"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "espe_aspende", "id" => "espe_aspendeID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"espe_aspende")!="")?self::JsonRecor($ASISTENCIAS,"espe_aspende"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "numb_aspenda", "id" => "numb_aspendaID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_aspenda")!="")?self::JsonRecor($ASISTENCIAS,"numb_aspenda"):"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->Input(array("name" => "espe_aspenda", "id" => "espe_aspendaID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"espe_aspenda")!="")?self::JsonRecor($ASISTENCIAS,"espe_aspenda"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->CloseRow();
				$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
		}
		
		$mHtml->OpenDiv("id:jsonRecurAsi");
			$mHtml->Table("tr");
				$mHtml->Row();
					$mHtml->Label( "RECURSOS ASIGNADOS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "N° de puesto Asignado",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->Label( "Estado de la diadema",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->Label( "Estado del mouse",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "reas_npuestoa", "id" => "reas_npuestoaID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_npuestoa")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_npuestoa"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "reas_ediadema", "id" => "reas_ediademaID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_ediadema")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_ediadema"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "reas_estmouse", "id" => "reas_estmouseID", "width" => "100%", "colspan"=>"3","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estmouse")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estmouse"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "Estado del Equipo P/C",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->Label( "Estado del teclado",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->Label( "Estado de la silla",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "reas_equipcx", "id" => "reas_equipcxID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_equipcx")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_equipcx"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "reas_teclado", "id" => "reas_tecladoID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_teclado")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_teclado"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "reas_essilla", "id" => "reas_essillaID", "width" => "100%", "colspan"=>"3","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_essilla")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_essilla"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "Estado del Pad Mouse",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->Label( "Aseo",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
					$mHtml->Label( "N° de registros realizados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Input(array("name" => "reas_padmous", "id" => "reas_padmousID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_padmous")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_padmous"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "reas_estaseo", "id" => "reas_estaseoID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estaseo")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estaseo"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "numb_regisre", "id" => "reas_regisreID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"numb_regisre")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"numb_regisre"):"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Input(array("name" => "reas_regisre", "id" => "reas_regisreID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_regisre")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_regisre"):"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		$mHtml->OpenDiv("id:jsonOtrasObserv");
			$mHtml->Table("tr");
				#detalle de la notificacion
				$mHtml->Row();
					$mHtml->Label( "OTRAS OBSERVACIONES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
				$mHtml->CloseRow();
				$mHtml->Row();
                	$mHtml->TextArea(($datosConsult[0][8]!="")?$datosConsult[0][8]:"", array("name" => "obs_notifi", "id" => "obs_notifiID", "colspan"=>"7", "readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				#documentos
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		#Formulario de diligenciamiento
		$mHtml->OpenDiv("id:Document");
			$mHtml->Table("tr");
				if($ActionForm->ActionForm=="ins")
				{
					$mHtml->Row();
						$mHtml->Label( "ADJUNTO 1 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
	                	$mHtml->file(array("name" => "file_1", "id" => "file_1ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
					$mHtml->CloseRow();
					#si es supervisor pinta campos adicionales
					if($ActionForm->idForm==3){
						$mHtml->Row();
							$mHtml->Label( "ADJUNTO 2 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		                	$mHtml->file(array("name" => "file_2", "id" => "file_2ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "ADJUNTO 3 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		                	$mHtml->file(array("name" => "file_3", "id" => "file_3ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "ADJUNTO 4 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		                	$mHtml->file(array("name" => "file_4", "id" => "file_4ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
						$mHtml->CloseRow();
					}
				}
				else
				{
					$document=self::getDocument($ActionForm);
					if($document)
					{
						foreach ($document as $keyDA => $valueDA) 
						{
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO 1 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
								if($ActionForm->ActionForm=="idi")
								{
									$mHtml->Button(array("value"=>"Cambiar", "id"=>"Vfile_1ID","name"=>"Vfile_1", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"1","onclick"=>"verArchivos(".$valueDA['cod_consec'].")") );
									$mHtml->Button(array("value"=>"Eliminar", "id"=>"Vfile_1ID","name"=>"Vfile_1", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"1","onclick"=>"verArchivos(".$valueDA['cod_consec'].")") );
								}
								$mHtml->Button(array("value"=>"Vizualizar", "id"=>"Vfile_1ID","name"=>"Vfile_1", "class"=>"crmButton small save", "align"=>"right", "colspan"=>(($ActionForm->ActionForm=="idi")?"4":"6"),"onclick"=>"verArchivos(".$valueDA['cod_consec'].")") );
							$mHtml->CloseRow();
						}
					}
				}	
				
				
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		if($ActionForm->ActionForm=="rep")
		{
			$mHtml->OpenDiv("id:reponNotif");
				$mHtml->Table("tr");
					$hisNotifi=self::getHistoNotifi($ActionForm);
					if($hisNotifi)
					{
						$mHtml->Row();
							$mHtml->Label( "HISTORIAL NOTIFICACIONES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();
						foreach ($hisNotifi as $keyHN => $valueHN) 
						{
							foreach ($valueHN as $keyRH => $valueRH) 
							{
								$mHtml->Row();
									$mHtml->Label( "Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
					            	$mHtml->Input(array("name" => "nom_asunto".$keyRH, "width" => "100%", "value"=>$valueRH ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
			            		$mHtml->CloseRow();
							}
						}
					}
					$mHtml->Row();
						$mHtml->Label( "RESPUESTA NOTIFICACION",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		            	$mHtml->Input(array("name" => "nom_asuntoN", "id" => "nom_asuntoNID", "width" => "100%", "value"=>$datosConsult[0][8] ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
            		$mHtml->CloseRow();
            		$mHtml->Row();
						$mHtml->Label( "*DETALLE",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
	                	$mHtml->TextArea("", array("name" => "obs_respon", "id" => "obs_responID", "colspan"=>"7"));
					$mHtml->CloseRow();
            	$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
		}
		$mHtml->OpenDiv("id:btnNotifi");
			$mHtml->Table("tr");
				$mHtml->Row();
					if($ActionForm->ActionForm=="ins")
					{
						$mHtml->Button( array("value"=>"GUARDAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"ValidateFormComun(".(($datosConsult[0])?"'idi'":"'ins'").")") );
					}
					if($ActionForm->ActionForm=="idi")
					{
						$mHtml->Button( array("value"=>"EDITAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"ValidateFormComun(".(($datosConsult[0])?"'idi'":"'ins'").")") );
					}
					if($ActionForm->ActionForm=="eli")
					{
						$mHtml->Button( array("value"=>"ELIMINAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"eliminarNotifi()") );
					}
					if($ActionForm->ActionForm=="rep")
					{
						$mHtml->Button( array("value"=>"RESPONDER", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"responNotifi()") );
					}
					$mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"5","onclick"=>"limpiarForm()") );
				$mHtml->CloseRow();
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		echo $mHtml->MakeHtml();
	}

	/*! \fn: getFormNuevaNotifiComun
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifiComun($ActionForm=NULL)
	{
		$datosConsult;
		$readonly ="";
		$disabled ="";
		if($ActionForm->ActionForm=="idi")
		{
			#realizar consulta
			$datosConsult =self::getInfoNotifiEdit($ActionForm);
		}
		if($ActionForm->ActionForm=="eli" || $ActionForm->ActionForm=="rep")
		{
			#realizar consulta
			$datosConsult = self::getInfoNotifiEdit($ActionForm);
			$readonly = "readonly";
			$disabled = "disabled";
		}
		#print_r($datosConsult[0]);
		$date = new DateTime();
		$mHtml = new Formlib(2);
		$mHtml->OpenDiv("id:newNotifi");
			$mHtml->Hidden(array( "name" => "usr_creaci", "id" => "usr_creaciID", "value"=>self::getCodUsuario($_SESSION['datos_usuario']['cod_usuari'])['cod_consec']));
			$mHtml->Hidden(array( "name" => "cod_tipnot", "id" => "cod_tipnotID", "value"=>$ActionForm->idForm));
			$mHtml->Hidden(array( "name" => "cod_notifi", "id" => "cod_notifiID", "value"=>($ActionForm->cod_notifi!="")?$ActionForm->cod_notifi:""));
			$mHtml->Table("tr");
				#ASUNTO
				$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
                $mHtml->Input(array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%", "value"=>($datosConsult[0][4]!="")?$datosConsult[0][4]:"" ,"colspan"=>"6", "readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				#FECHA DE MODIFICACION
				$mHtml->Row();
					$mHtml->Label( "Fecha de Notificacion:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
                	$mHtml->Input(array("value"=>$date->format('Y-m-d H:i:s'),"name" => "fec_creaci", "id" => "fec_creaciID", "width" => "100%", "readonly"=>"readonly", "disabled"=>"disabled","colspan"=>"6"));
				$mHtml->CloseRow();
				#NOTIFICADO POR
				$mHtml->Row();
					$mHtml->Label( "Notificado por:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
                	$mHtml->Input(array("value"=>$_SESSION['datos_usuario']['cod_usuari'],"name" => "usr_creaci", "id" => "usr_creaciID", "width" => "100%", "readonly"=>"readonly", "disabled"=>"disabled", "colspan"=>"6"));
				$mHtml->CloseRow();
				#PUBLICAR A Y USUARIOS
				$mHtml->Row();
					$mHtml->Label( "*Publicar a:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Select2 (self::getLisRespon(),  array("name" => "cod_asires", "width" => "25%","colspan"=>"1") );
					$mHtml->Label( "Usuarios:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Select2 ("",  array("name" => "ind_notusr", "width" => "25%","colspan"=>"4") );
				$mHtml->CloseRow();
				#VIGENCIA HASTA
				$mHtml->Row();
					$mHtml->Label( "*Vigencia hasta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Input(array("name" => "fec_vigenc", "id" => "fec_vigencID", "width" => "100%", "colspan"=>"1", "value"=>($datosConsult[0][6]!="")?$datosConsult[0][6]:"","onclick"=>($datosConsult[0][6]!="")?"":"getFechaDatapick('fec_vigencID')", "readonly"=>($datosConsult[0][6]!="" || $readonly!="")?"readonly":"","disabled"=>($datosConsult[0][6]!="" || $readonly!="")?$disabled:""));
					$mHtml->Label( "*Requiere Respuesta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Label( "SI",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Radio(array("value"=>"1","name" => "ind_respue", "id" => "ind_respuesID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="ins")?"checked":($datosConsult[0][7]==1)?"checked":"","readonly"=>$readonly, "disabled"=>$disabled));
					$mHtml->Label( "NO",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
					$mHtml->Radio(array("value"=>"0","name" => "ind_respue", "id" => "ind_respuenID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="idi")?($datosConsult[0][7]==0)?"checked":"":"","readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "*DETALLE DE LA NOTIFICACION:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
				$mHtml->CloseRow();
				$mHtml->Row();
                	$mHtml->TextArea(($datosConsult[0][8]!="")?$datosConsult[0][8]:"", array("name" => "obs_notifi", "id" => "obs_notifiID", "colspan"=>"7", "readonly"=>$readonly, "disabled"=>$disabled));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "*Documentos:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
				$mHtml->CloseRow();
				if($ActionForm->ActionForm=="ins")
				{
					$mHtml->Row();
						$mHtml->Label( "ADJUNTO 1 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
	                	$mHtml->file(array("name" => "file_1", "id" => "file_1ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "ADJUNTO 2 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
	                	$mHtml->file(array("name" => "file_2", "id" => "file_2ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "ADJUNTO 3 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
	                	$mHtml->file(array("name" => "file_3", "id" => "file_3ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "ADJUNTO 4 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
	                	$mHtml->file(array("name" => "file_4", "id" => "file_4ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
					$mHtml->CloseRow();
				}
				else
				{
					$document=self::getDocument($ActionForm);
					if($document)
					{
						foreach ($document as $keyDA => $valueDA) 
						{
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO 1 :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
								if($ActionForm->ActionForm=="idi")
								{
									$mHtml->Button(array("value"=>"Cambiar", "id"=>"Vfile_1ID","name"=>"Vfile_1", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"1","onclick"=>"verArchivos(".$valueDA['cod_consec'].")") );
									$mHtml->Button(array("value"=>"Eliminar", "id"=>"Vfile_1ID","name"=>"Vfile_1", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"1","onclick"=>"verArchivos(".$valueDA['cod_consec'].")") );
								}
								$mHtml->Button(array("value"=>"Vizualizar", "id"=>"Vfile_1ID","name"=>"Vfile_1", "class"=>"crmButton small save", "align"=>"right", "colspan"=>(($ActionForm->ActionForm=="idi")?"4":"6"),"onclick"=>"verArchivos(".$valueDA['cod_consec'].")") );
							$mHtml->CloseRow();
						}
					}
				}					
				
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		if($ActionForm->ActionForm=="rep")
		{
			$mHtml->OpenDiv("id:reponNotif");
				$mHtml->Table("tr");
					$hisNotifi=self::getHistoNotifi($ActionForm);
					if($hisNotifi)
					{
						$mHtml->Row();
							$mHtml->Label( "HISTORIAL NOTIFICACIONES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();
						foreach ($hisNotifi as $keyHN => $valueHN) {
							foreach ($valueHN as $keyRH => $valueRH) {
								$mHtml->Row();
									$mHtml->Label( "Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
					            	$mHtml->Input(array("name" => "nom_asunto".$keyRH, "width" => "100%", "value"=>$valueRH ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
			            		$mHtml->CloseRow();
							}
						}
					}
					$mHtml->Row();
						$mHtml->Label( "RESPUESTA NOTIFICACION",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		            	$mHtml->Input(array("name" => "nom_asuntoN", "id" => "nom_asuntoNID", "width" => "100%", "value"=>$datosConsult[0][8] ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
            		$mHtml->CloseRow();
            		$mHtml->Row();
						$mHtml->Label( "*DETALLE",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
					$mHtml->CloseRow();
					$mHtml->Row();
	                	$mHtml->TextArea("", array("name" => "obs_respon", "id" => "obs_responID", "colspan"=>"7"));
					$mHtml->CloseRow();
            	$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();
		}
		$mHtml->OpenDiv("id:btnNotifi");
			$mHtml->Table("tr");
				$mHtml->Row();
					if($ActionForm->ActionForm=="ins")
					{
						$mHtml->Button( array("value"=>"ENVIAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"ValidateFormComun(".(($datosConsult[0])?"'idi'":"'ins'").")") );
					}
					if($ActionForm->ActionForm=="idi")
					{
						$mHtml->Button( array("value"=>"ENVIAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"ValidateFormComun(".(($datosConsult[0])?"'idi'":"'ins'").")") );
					}
					if($ActionForm->ActionForm=="eli")
					{
						$mHtml->Button( array("value"=>"ELIMINAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"eliminarNotifi()") );
					}
					if($ActionForm->ActionForm=="rep")
					{
						$mHtml->Button( array("value"=>"RESPONDER", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"responNotifi()") );
					}
					
					$mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"5","onclick"=>"limpiarForm()") );
				$mHtml->CloseRow();
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		$mHtml->OpenDiv("id:frameDocument");
		$mHtml->CloseDiv();
		echo $mHtml->MakeHtml();
	}

	/*! \fn: getLisRespon
	 *  \brief: devuelve array de responsables
	 *  \author: Edward Serrano
	 *	\date:  10/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getLisRespon ( $mCodNivel = NULL)
	{
        $mSelect = "SELECT cod_respon, nom_respon FROM ".BASE_DATOS.".tab_genera_respon";
	    $mConsult = new Consulta($mSelect, self::$cConexion );
	    $_RESPON = $mConsult -> ret_matrix("i");
	    $inicio[0][0]=0;
	    $inicio[0][1]='-';
	    $_RESPON=array_merge($inicio,$_RESPON);
	    return $_RESPON;
	}

	/*! \fn: getInfoNotifiEdit
	 *  \brief: devuelve array de notificaciones
	 *  \author: Edward Serrano
	 *	\date:  18/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getInfoNotifiEdit ( $mData = NULL)
	{
        $mSelect;
        if($mData->idForm==3 || $mData->idForm==4)
        {
        	$mSelect = "SELECT  a.cod_notifi AS cod_notifi ,a.cod_tipnot AS cod_tipnot,a.ind_notres AS ind_notres,
          					    a.ind_notusr AS ind_notusr ,a.nom_asunto AS nom_asunto,a.num_horlab AS num_horlab,
          					    a.fec_vigenc AS fec_vigenc ,a.ind_respue AS ind_respue,a.obs_notifi AS obs_notifi,
          					    a.ind_enttur AS ind_enttur ,a.usr_creaci AS usr_creaci,a.fec_creaci AS fec_creaci,
          					    b.cod_notde,b.jso_notifi 
        					    FROM ".BASE_DATOS.".tab_notifi_notifi a
        					    	INNER JOIN ".BASE_DATOS.".tab_notifi_detail b
        					    	ON a.cod_notifi=b.cod_notifi 
        					    		WHERE a.cod_notifi=$mData->cod_notifi AND a.nom_asunto='$mData->nom_asunto' AND cod_tipnot=$mData->idForm";
        }
        else
        {
        	$mSelect = "SELECT  a.cod_notifi AS cod_notifi ,a.cod_tipnot AS cod_tipnot,a.ind_notres AS ind_notres,
          						a.ind_notusr AS ind_notusr ,a.nom_asunto AS nom_asunto,a.num_horlab AS num_horlab,
          						a.fec_vigenc AS fec_vigenc ,a.ind_respue AS ind_respue,a.obs_notifi AS obs_notifi,
          						a.ind_enttur AS ind_enttur ,a.usr_creaci AS usr_creaci,a.fec_creaci AS fec_creaci 
        					FROM ".BASE_DATOS.".tab_notifi_notifi a
        						WHERE a.cod_notifi=$mData->cod_notifi AND a.nom_asunto='$mData->nom_asunto' AND cod_tipnot=$mData->idForm";
        }
	    $mConsult = new Consulta($mSelect, self::$cConexion );
	    $_RESPON = $mConsult -> ret_matrix("i");
	    return $_RESPON;

	}


	/*! \fn: getNomUsuario
	 *  \brief: devuelve array de usuarios
	 *  \author: Edward Serrano
	 *	\date:  10/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getNomUsuario()
	{  
		$datos = (object) $_POST;
		$cod_respon= substr($datos->cod_Respon, 1);
	    $mSql = "SELECT a.cod_consec, UPPER(a.nom_usuari) AS nom_tercer 
	    				FROM ".BASE_DATOS.".tab_genera_usuari a 
	    					INNER JOIN ".BASE_DATOS.".tab_genera_perfil b ON a.cod_consec=b.cod_perfil 
	    						WHERE b.cod_respon IN (".$cod_respon.") GROUP BY a.cod_usuari";
	    $consulta = new Consulta( $mSql, self::$cConexion);
	    $mResult = $consulta -> ret_matrix('i');
	    $retr="<option value='0' selected='selected'>-</option>";
	    foreach ($mResult as $key => $value) {
	    	$retr.="<option value='".$value[0]."'>".$value[1]."</option>";
	    }
	    echo $retr;
	}

	/*! \fn: NuevaNotifiComun
	 *  \brief: alamcena las nuevas notificaciones
	 *  \author: Edward Serrano
	 *	\date:  12/01/2017
	 *	\date modified: dia/mes/año
	 */
	function NuevaNotifiComun()
	{
		$datos = (object) $_REQUEST;
		$files = array();
		$Estados = array();
		$dirServ = "../../".DIR_APLICA_CENTRAL."/lib/filnot/";
		if($datos->nom_asunto!="" && $datos->fec_creaci!="" && $datos->usr_creaci!="" && $datos->cod_asires!="" && $datos->ind_notusr!="" && $datos->fec_vigenc!="" && $datos->ind_respue!="" && $datos->obs_notifi!="" && $datos->cod_tipnot!="")
		{
			$sql ="INSERT INTO " . BASE_DATOS . ".tab_notifi_notifi 
                                        (cod_tipnot,	ind_notres,		ind_notusr,
                                         nom_asunto,	fec_vigenc,		ind_respue,	
                                         obs_notifi,	ind_estado,		usr_creaci,	
                                         fec_creaci)
                                VALUES  
                                		($datos->cod_tipnot,'".str_replace("'", "", substr($datos->cod_asires, 1))."','".str_replace("'", "", substr($datos->ind_notusr, 1))."',
                                		'$datos->nom_asunto', '$datos->fec_vigenc',$datos->ind_respue, 
                                		'$datos->obs_notifi', 1,$datos->usr_creaci, 
                                		'$datos->fec_creaci')" ;
            $consulta = new Consulta($sql, self::$cConexion, "RC");
            if($consulta)
            {
            	if(count($_FILES)>0)
	            {
	            	#consulto el utimo registro de la tabla tab_notifi_notifi y con la fecha alamcenada
	            	$sqlNotifi = 'SELECT MAX(cod_notifi) AS cod_notifi FROM ' . BASE_DATOS . '.tab_notifi_notifi WHERE fec_creaci="'.$datos->fec_creaci.'"';
	            	$consultaNotifi = new Consulta( $sqlNotifi, self::$cConexion);
		    		$mResultNotifi = $consultaNotifi -> ret_matrix('i');
		    		$rCod_notifi=$mResultNotifi[0][0];
	            	$sqlFile = 'INSERT INTO ' . BASE_DATOS . '.tab_notifi_ficher (cod_notifi,nom_ficher,tip_ficher,url_ficher)
	            												VALUES ';
	            	#Recorro los archivos adjuntos
	            	foreach ($_FILES as $keyFile => $valueFile) 
	            	{
	            		#separo por punto el mombre del archivo
	            		$type = explode(".", basename($_FILES[$keyFile]["name"] ));
	            		#capturo el nombre original del archivo
	            		$nameFile =$_FILES[$keyFile]["name"];
	            		#asigno nuevo combre al archivo con codificacion MD5
	            		$_FILES[$keyFile]["name"]=md5($_FILES[$keyFile]["name"].$datos->fec_creaci).".".$type[1];
	            		#Asigno ubicacion del archivo en el servidor con el nombre codificado
	            		$ubicacion = $dirServ.basename($_FILES[$keyFile]["name"] );
	            		#Preparo la consulta
	            		$sqlFile.=' (' . $rCod_notifi . ', "' . $nameFile . '", "' .$type[1] . '", "' . $ubicacion . '"),';
	            		#almaceno el nombre el un arrray para despues mover los archivos
	            		$files[$keyFile]=$_FILES[$keyFile]['name'];
	            	}
	            	$sqlFile = substr($sqlFile, 0, -1);
	            	$consultaFile = new Consulta($sqlFile, self::$cConexion, "RC");
	            	if($consultaFile)
	            	{
	            		foreach ($files as $keyAr => $valueAr) {
	            			if ( move_uploaded_file($_FILES[$keyAr]['tmp_name'], $dirServ.$valueAr) )
		            		{
		            			$Estados["OK"]["carga"]="se cargo el archivo:".$valueAr;
		            		}
		            		else
		            		{
		            			$Estados["ERROR"]["carga"]="error cargar archivo:".$valueAr;
		            		}
	            		}
	            	}
	            }
            }
            else
            {
            	$Estados["ERROR"]["consulta"]="error en consulta generar notificaion";
            }
		}
		else
		{
			$Estados["ERROR"]["validacion"]="Campos obligatios";
		
		}
		if(!$Estados["ERROR"]){
			$Estados["OK"]="OK";
			echo "OK";
		}else
		{
			echo "ERROR";
		}
	}

	/*! \fn: NuevaNotifiExten
	 *  \brief: alamcena las nuevas notificaciones para supervisores y controladores
	 *  \author: Edward Serrano
	 *	\date:  12/01/2017
	 *	\date modified: dia/mes/año
	 */
	function NuevaNotifiExten(){
		$datos = (object) $_REQUEST;
		$files = array();
		$Estados = array();
		$dirServ = "../../".DIR_APLICA_CENTRAL."/lib/filnot/";
		if($datos->nom_asunto!="" && $datos->fec_creaci!="" && $datos->usr_creaci!="" && $datos->cod_asires!="" && $datos->fec_vigenc!="" && $datos->ind_respue!="" && $datos->obs_notifi!="" && $datos->cod_tipnot!="" && $datos->num_horlab!="" && $datos->jso_notifi!="")
		{
			$sql ="INSERT INTO " . BASE_DATOS . ".tab_notifi_notifi 
                                        (cod_tipnot,	ind_notres,		num_horlab,
                                         nom_asunto,	fec_vigenc,		ind_respue,	
                                         obs_notifi,	ind_estado,		usr_creaci,	
                                         fec_creaci,	ind_enttur)
                                VALUES  
                                		($datos->cod_tipnot,'".str_replace("'", "", substr($datos->cod_asires, 1))."',$datos->num_horlab,
                                		'$datos->nom_asunto', '$datos->fec_vigenc',$datos->ind_respue, 
                                		'$datos->obs_notifi', 1,$datos->usr_creaci, 
                                		'$datos->fec_creaci', $datos->ind_enttur)" ;
            $consulta = new Consulta($sql, self::$cConexion, "RC");
            if($consulta)
            {
            	#consulto el utimo registro de la tabla tab_notifi_notifi y con la fecha alamcenada
	            $sqlNotifi = 'SELECT MAX(cod_notifi) AS cod_notifi FROM ' . BASE_DATOS . '.tab_notifi_notifi WHERE fec_creaci="'.$datos->fec_creaci.'"';
	            $consultaNotifi = new Consulta( $sqlNotifi, self::$cConexion);
		    	$mResultNotifi = $consultaNotifi -> ret_matrix('i');
		    	$rCod_notifi=$mResultNotifi[0][0];

				$sqlJson = "INSERT INTO " . BASE_DATOS . ".tab_notifi_detail 
										(cod_notifi, jso_notifi)
								VALUES
										(". $rCod_notifi .", '$datos->jso_notifi')";
				$consultaJson = new Consulta($sqlJson, self::$cConexion, "RC");
	            if($consultaJson)
	            {
	            	if(count($_FILES)>0)
		            {
		            	$sqlFile = 'INSERT INTO ' . BASE_DATOS . '.tab_notifi_ficher (cod_notifi,nom_ficher,tip_ficher,url_ficher)
		            												VALUES ';
		            	#Recorro los archivos adjuntos
		            	foreach ($_FILES as $keyFile => $valueFile) 
		            	{
		            		#separo por punto el mombre del archivo
		            		$type = explode(".", basename($_FILES[$keyFile]["name"] ));
		            		#capturo el nombre original del archivo
		            		$nameFile =$_FILES[$keyFile]["name"];
		            		#asigno nuevo combre al archivo con codificacion MD5
		            		$_FILES[$keyFile]["name"]=md5($_FILES[$keyFile]["name"].$datos->fec_creaci).".".$type[1];
		            		#Asigno ubicacion del archivo en el servidor con el nombre codificado
		            		$ubicacion = $dirServ.basename($_FILES[$keyFile]["name"] );
		            		#Preparo la consulta
		            		$sqlFile.=' (' . $rCod_notifi . ', "' . $nameFile . '", "' .$type[1] . '", "' . $ubicacion . '"),';
		            		#almaceno el nombre el un arrray para despues mover los archivos
		            		$files[$keyFile]=$_FILES[$keyFile]['name'];
		            	}
		            	$sqlFile = substr($sqlFile, 0, -1);
		            	$consultaFile = new Consulta($sqlFile, self::$cConexion, "RC");
		            	if($consultaFile)
		            	{
		            		foreach ($files as $keyAr => $valueAr) {
		            			if ( move_uploaded_file($_FILES[$keyAr]['tmp_name'], $dirServ.$valueAr) )
			            		{
			            			$Estados["OK"]["carga"]="se cargo el archivo:".$valueAr;
			            		}
			            		else
			            		{
			            			$Estados["ERROR"]["carga"]="error cargar archivo:".$valueAr;
			            		}
		            		}
		            	}
		            }
	            }
	            else
	            {
	            	$Estados["ERROR"]["consulta"]="error en consulta generar JSon";
	            }
            }else
            {
            	$Estados["ERROR"]["consulta"]="error en consulta generar notificaion";
            }
		}
		else
		{
			$Estados["ERROR"]["validacion"]="Campos obligatios";
		}
		if(!$Estados["ERROR"]){
			$Estados["OK"]="OK";
			echo "OK";
		}else
		{
			echo "ERROR";
		}
	}

	/*! \fn: elimiNotifi
	 *  \brief: elimina las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	function elimiNotifi(){
		$datos = (object) $_REQUEST;
		$files = array();
		$Estados = array();
		if($datos->cod_notifi!="" && $datos->nom_asunto!="" && $datos->cod_tipnot!="" && $datos->ActionForm=="eli")
		{
			$sql ="UPDATE " . BASE_DATOS . ".tab_notifi_notifi 
                            SET ind_estado=0 
                            WHERE cod_notifi = $datos->cod_notifi 
                            AND   nom_asunto = '$datos->nom_asunto'
                            AND   cod_tipnot = $datos->cod_tipnot" ;
            $consulta = new Consulta($sql, self::$cConexion, "RC");
			$Estados["OK"]="OK";
		}
		else
		{
			$Estados["ERROR"]["validacion"]="Campos obligatios";
		}
		if(!$Estados["ERROR"]){
			$Estados["OK"]="OK";
			echo "OK";
		}else
		{
			echo "ERROR";
		}
		#print_r($datos);
	}

	/*! \fn: JsonRecor
	 *  \brief: recorrer json de las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	private function JsonRecor($json=NULL, $param=NULL){
		if($json!=NULL && $param!=NULL)
		{
			foreach ($json as $key => $value) {
				if($key==$param){
					return $value;
				}
			}	
		}
		else
		{
			//echo "no hay datos";
			return 0;
		}
	}

	/*! \fn: JsonRecor
	 *  \brief: recorrer json de las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  20/01/2017
	 *	\date modified: dia/mes/año
	 */
	private function getHistoNotifi($ActionForm=NULL){
		$mSql = "SELECT a.obs_respon
					 FROM ".BASE_DATOS.".tab_notifi_respon a
					 	INNER JOIN ".BASE_DATOS.".tab_notifi_notifi b 
					 		ON a.cod_notifi=b.cod_notifi
					 			WHERE a.cod_notifi=".$ActionForm->cod_notifi." AND b.cod_tipnot=".$ActionForm->idForm;
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult;
	}

	/*! \fn: getDocument
	 *  \brief: documente asociados
	 *  \author: Edward Serrano
	 *	\date:  23/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getDocument($ActionForm=NULL)
	{
		$mSql = "SELECT a.cod_consec,a.cod_notifi,a.nom_ficher,a.tip_ficher,a.url_ficher
					 FROM ".BASE_DATOS.".tab_notifi_ficher a
					 		WHERE a.cod_notifi=".$ActionForm->cod_notifi." ".(($ActionForm->cod_consec)?" AND a.cod_consec=".$ActionForm->cod_consec:"");
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult;
	}
	

	/*! \fn: EditNotifiComun
	 *  \brief: edita las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  23/01/2017
	 *	\date modified: dia/mes/año
	 */
	function responderNotifi()
	{
		$datos = (object) $_REQUEST;
		$Estados = array();
		if($datos->cod_notifi!="" && $datos->nom_asunto!="" && $datos->obs_respon!="" && $datos->ActionForm=="rep")
		{
			$sql ="INSERT INTO " . BASE_DATOS . ".tab_notifi_respon 
                                        (cod_notifi,		obs_respon)
                                VALUES  
                                		($datos->cod_notifi, '$datos->obs_respon')" ;
            $consulta = new Consulta($sql, self::$cConexion, "RC");
            if($consulta)
            {
				$Estados["OK"]["guardar"]="Se realiza la consulta correctamente";
            }
            else
            {
            	$Estados["ERROR"]["consulta"]="error en consulta generar notificaion";
            }
		}
		else
		{
			$Estados["ERROR"]["validacion"]="Campos obligatios";
		}
		
		if(!$Estados["ERROR"]){
			$Estados["OK"]="OK";
			echo "OK";
		}else
		{
			echo "ERROR";
		}
		//print_r($datos);
	}

	/*! \fn: EditNotifiComun
	 *  \brief: edita las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	public function getRefDocumet(){
		$datos = (object) $_REQUEST;
		$Refdocument=self::getDocument($datos);
		/*switch ($Refdocument[0]['tip_ficher']) {
			case 'jpg' : case 'jpeg': case 'bmp' : case 'tiff' : case 'png' : case 'pdf' :
				echo "<embed src='".substr($Refdocument[0]['url_ficher'], 3)."' width='".$datos->width."' height='400'>";
			break;

			case 'doc' : case 'docx' : case 'xls' : case 'xlsx' : case 'cvs' : case 'zip' : case 'rar' :
				echo "<a href='".substr($Refdocument[0]['url_ficher'], 3)."'>Download Here</a>";
			break;
			
			default:
				# code...
				break;
		}*/
		//echo $Refdocument[0]['url_ficher'];
	}

	/*! \fn: EditNotifiComun
	 *  \brief: edita las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	function EditNotifiComun(){
		echo "llego funcion edit comum";
	}

	/*! \fn: EditNotifiExten
	 *  \brief: edita notificaciones para supervisores y controladores
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	function EditNotifiExten(){
		echo "llego funcion edit extent";
	}

	#obejectos para la administracion de perfiles

	/*! \fn: getmPermOet
	 *  \brief: devuelve objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getmPermOet(){
		return $_SESSION['subNotifi']['PermOet'];
	}

	/*! \fn: setmPermOet
	 *  \brief: devuelve objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function setmPermOet($mPermOet = NULL){
		$_SESSION['subNotifi']['PermOet']=$mPermOet;
	}

	/*! \fn: getmPermClf
	 *  \brief: asigna objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getmPermClf(){
		return $_SESSION['subNotifi']['PermClf'];
	}

	/*! \fn: setmPermClf
	 *  \brief: devuelve objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function setmPermClf($mPermClf = NULL){
		$_SESSION['subNotifi']['PermClf']=$mPermClf;
	}

	/*! \fn: getmPermSup
	 *  \brief: asigna objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getmPermSup(){
		return $_SESSION['subNotifi']['PermSup'];
	}

	/*! \fn: setmPermSup
	 *  \brief: devuelve objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function setmPermSup($mPermSup = NULL){
		$_SESSION['subNotifi']['PermSup']=$mPermSup;
	}

	/*! \fn: getmPermCon
	 *  \brief: asigna objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getmPermCon(){
		return $_SESSION['subNotifi']['PermCon'];
	}

	/*! \fn: setmPermCon
	 *  \brief: devuelve objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function setmPermCon($mPermCon = NULL){
		$_SESSION['subNotifi']['PermCon']=$mPermCon;
	}

	/*! \fn: getmPermCli
	 *  \brief: asigna objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getmPermCli(){
		return $_SESSION['subNotifi']['PermCli'];
	}

	/*! \fn: setmPermCli
	 *  \brief: devuelve objecto de permisos
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	function setmPermCli($mPermCli = NULL){
		$_SESSION['subNotifi']['PermCli']=$mPermCli;
	}
	
}
$notifi = new AjaxNotifiNotifi( );
?>
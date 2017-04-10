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
		try
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

		    	case 'elimiDocument':
			    	self::elimiDocument();
		    	break;

			    default:
			      	#header('Location: index.php?window=central&cod_servic=20151235&menant=20151235');
			    break;
			}
		} catch (Exception $e) {
			echo "error __construct :".$e;
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
		try 
		{
			#captura en objecto de los datos enviados
			$datos = (object) $_POST;
			#Array de los titulos de la tabla
			$titulos = array('Nivel1' => array('Generados' => 'Generados', 'OET' => 'Informacion OET', 'PorcOET' => '%', 'CFL' => 'Informacion CFL', 'PorcCFL' => '%', 'SUPER' => 'Informacion Supervisores', 'PorSUPER' => '%', 'CONTRO' => 'Informacion Controladores', 'PorcCONTRO' => '%', 'CLIENT' => 'Informacion Clientes', 'PorcCLIENT' => '%', 'OTROS' => 'OTROS', 'PorcOTROS' => '%', ), 'Nivel2' => array());
			#inicio del Formulario
			$mHtml = new FormLib();
			$mHtml->Table("tr");
				$mHtml->Label( "NOTIFICACIONES GENERADAS EN EL PERIDODO DEL ". $datos->fec_iniID. " AL ".$datos->fec_finID, array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
				$mHtml->CloseRow();
					#Pintar los titulos
					foreach ($titulos as $keyN1 => $valueN1) {
						if($keyN1=='Nivel1')
						{
							$mHtml->Row();
							foreach ($valueN1 as $keySub1 => $valueSub1) {
								$mHtml->Label( $valueSub1,  array("align"=>"center", "class"=>"celda_titulo") );	
							}
							$mHtml->CloseRow();
						}	
					}
				#array de datos consultados para el informe
				$mInfo = array('total'=>0);
				#recorrer los tipos de categorias
				for($xI=1;$xI<=5;$xI++)
				{
					#consulto por categoria la cantidad solicitada
					$mResult=self::getGeneralNotifi($datos,$xI,"1");
					$mInfo['total']=$mResult['cant']+$mInfo['total'];
					$mInfo[$xI]=$mResult['cant'];
				}
				#recorro array de resultados
				foreach ($mInfo as $keyinf => $valueinf) 
				{
					if ($keyinf=='total') 
					{
						#pinto el total general
						$mHtml->Label( $valueinf,  array("align"=>"center", "class"=>"celda_titulo") );	
					}
					else
					{
						#pito los totales por categoria
						$mHtml->Label( $valueinf,  array("align"=>"center", "class"=>"celda_titulo") );	
						$mHtml->Label( round((($valueinf*100)/$mInfo['total']),2)."%",  array("align"=>"center", "class"=>"celda_titulo") );	
					}
				}
				#pinto la categoria otros
				$mHtml->Label( "0",  array("align"=>"center", "class"=>"celda_titulo") );	
				$mHtml->Label( "0%",  array("align"=>"center", "class"=>"celda_titulo") );
			$mHtml->CloseTable('tr');
			#tabla de resultados por fecha
			$mHtml->Table("tr");
				#pinto los titulos
				$mHtml->Row();
					$mHtml->Label( "DETALLE DE NOTIFICACION POR FECHA", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "FECHA DE NOTIFICACION",  array("align"=>"center", "class"=>"celda_titulo") );	
					foreach ($titulos as $keyN1 => $valueN1) {
						if($keyN1=='Nivel1')
						{
							foreach ($valueN1 as $keySub1 => $valueSub1) {
								$mHtml->Label( $valueSub1,  array("align"=>"center", "class"=>"celda_titulo") );	
							}
						}	
					}
				$mHtml->CloseRow();
				#parametrizo las variables para recorrer por fecha
				$fechaInicio=strtotime($datos->fec_iniID);
				$fechaFin=strtotime($datos->fec_finID);
				#recorro las fechas por dias
				for($iDate=$fechaInicio; $iDate<=$fechaFin; $iDate+=86400)
				{
					$mHtml->Row();
					#array de resultados
	    			$mInfoDias = array('total'=>0);
	    			$datos->fecha_serch=date("Y-m-d", $iDate);
	    			#pinto la fecha
	    			$mHtml->Label( self::getFormatFecha($iDate+86400),  array("align"=>"center", "class"=>"celda_titulo") );
		    		#recorrer los tipos de categorias
		    		for($xID=1;$xID<=5;$xID++)
					{
						#consulto por categoria la cantidad solicitada
						$mResult=self::getGeneralNotifi($datos,$xID,"2");
						$mInfoDias['total']=$mResult['cant']+$mInfoDias['total'];
						$mInfoDias[$xID]=$mResult['cant'];
					}
					#recorro array de resultados
					foreach ($mInfoDias as $keyinfD => $valueinfD) 
					{
						if ($keyinfD=='total') 
						{
							$mHtml->Label( $valueinfD,  array("align"=>"center", "class"=>"celda_titulo") );	
						}
						else
						{
							$mHtml->Label( $valueinfD,  array("align"=>"center", "class"=>"celda_titulo") );	
							$mHtml->Label( round((($valueinfD*100)/$mInfoDias['total']),2)."%",  array("align"=>"center", "class"=>"celda_titulo") );	
						}
					}
					$mHtml->Label( "0",  array("align"=>"center", "class"=>"celda_titulo") );	
					$mHtml->Label( "0%",  array("align"=>"center", "class"=>"celda_titulo") );
						
					$mHtml->CloseRow();
				} 
				
			$mHtml->CloseTable("tr");

			echo $mHtml->MakeHtml();
		} catch (Exception $e) {
			echo "error getFormGeneral :".$e;
		}
	}

	/*! \fn: getGeneralNotifi
	 *  \brief: retorna consulta general de notificaciones
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getGeneralNotifi($data = NULL,$cod_tipnot = NULL,$option = NULL)
	{
		try 
		{
			if($option=="1")
			{
				$mSql = "SELECT COUNT('cod_tipnot') AS cant FROM ".BASE_DATOS.".tab_notifi_notifi WHERE ".(($cod_tipnot!=NULL)?" cod_tipnot=".$cod_tipnot." AND ":"")." ind_estado=1 AND fec_creaci BETWEEN '$data->fec_iniID 00:00:00' AND '$data->fec_finID 23:59:59'";
			}
			else if ($option=="2") {
				$mSql = "SELECT COUNT('cod_tipnot') AS cant FROM ".BASE_DATOS.".tab_notifi_notifi WHERE ".(($cod_tipnot!=NULL)?" cod_tipnot=".$cod_tipnot." AND ":"")." ind_estado=1 AND fec_creaci BETWEEN '$data->fecha_serch 00:00:00' AND  '$data->fecha_serch 23:59:59'";
			}
			
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('a');
			return $mResult[0];
		} catch (Exception $e) {
			echo "error getGeneralNotifi :".$e;
		}
	}

	/*! \fn: getNotifi
	 *  \brief: retorna consulta de notificaciones por tipo
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getNotifi($mDatosGN)
	{
		try
		{
			$mSql = "SELECT a.cod_notifi,a.nom_asunto,a.fec_creaci,b.cod_usuari
						 FROM ".BASE_DATOS.".tab_notifi_notifi a
						 	INNER JOIN ".BASE_DATOS.".tab_genera_usuari b 
						 		ON a.usr_creaci=b.cod_consec
						 			WHERE a.cod_tipnot=".$mDatosGN->cod_notifi." AND a.fec_creaci>='".$mDatosGN->fec_iniID."' AND a.fec_creaci<='".$mDatosGN->fec_finID." 23:59:59'";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('a');
			return $mResult;
		} catch (Exception $e) {
			echo "error getNotifi :".$e;
		}
	}

	/*! \fn: getNotifi
	 *  \brief: retorna consulta de notificaciones por tipo
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getCodUsuario($cod_usuari){
		try
		{
			$mSql = "SELECT a.cod_consec
						 FROM ".BASE_DATOS.".tab_genera_usuari a
						 	WHERE a.cod_usuari='".$cod_usuari."'";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('a');
			return $mResult[0];
		} catch (Exception $e) {
			echo "error getCodUsuario :".$e;
		}
	}

	/*! \fn: getForm
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getForm()
	{
		try
		{
			$datos = (object) $_POST;
			$titulos = array('Nivel1' => array('Consecutivo' => 'Consecutivo', 'Asunto' => 'Asunto', 'Fecha' => 'Fecha y hora', 'notificacion' => 'Notificado por'));
			$mHtml = new Formlib(2, "yes",TRUE);
					#Opciones para la pestana OET
					if($datos->cod_notifi==1)
					{
						$mHtml->OpenDiv("id:tabOET");
							if(self::getmPermOet()['ins']==1)
							{
								$mHtml->Table("tr",array("class"=>"displayDIV2"));
									$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNoetID","name"=>"btnNoet", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(1)") );
									$mHtml->CloseRow();
								$mHtml->CloseTable('tr');
							}
							$mHtml->OpenDiv("id:tabOETdl");
								$mHtml->SetBody(self::getDinamiList(self::getmPermOet(),$datos));
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();	
					}
					#Opciones para la pestana faro
					if($datos->cod_notifi==2)
					{
						$mHtml->OpenDiv("id:tabCLF");
							if(self::getmPermClf()['ins']==1)
							{
								$mHtml->Table("tr",array("class"=>"displayDIV2"));
									$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNclfID","name"=>"btnNclf", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(2)") );
									$mHtml->CloseRow();
								$mHtml->CloseTable('tr');
							}
							$mHtml->OpenDiv("id:tabCLFdl");
								$mHtml->SetBody(self::getDinamiList(self::getmPermClf(),$datos));
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();	
					}
					#Opciones para la pestana supervisores
					if($datos->cod_notifi==3)
					{
						$mHtml->OpenDiv("id:tabSUP");
							if(self::getmPermSup()['ins']==1)
							{
								$mHtml->Table("tr",array("class"=>"displayDIV2"));
									$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNsupID","name"=>"btnNsup", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(3)") );
									$mHtml->CloseRow();
								$mHtml->CloseTable('tr');
							}
							$mHtml->OpenDiv("id:tabSUPdl");
								$mHtml->SetBody(self::getDinamiList(self::getmPermSup(),$datos));
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();
					}
					#Opciones para la pestana controladores
					if($datos->cod_notifi==4)
					{
						$mHtml->OpenDiv("id:tabCON");
							if(self::getmPermCon()['ins']==1)
							{
								$mHtml->Table("tr",array("class"=>"displayDIV2"));
									$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
									$mHtml->CloseRow();		
									$mHtml->Row();
										$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNconID","name"=>"btnNcon", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(4)") );
									$mHtml->CloseRow();
								$mHtml->CloseTable('tr');
							}
							$mHtml->OpenDiv("id:tabCONdl");
								$mHtml->SetBody(self::getDinamiList(self::getmPermCon(),$datos));
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();
					}
					#Opciones para la pestana clientes
					if($datos->cod_notifi==5)
					{
						$mHtml->OpenDiv("id:tabCLI");
							if(self::getmPermCli()['ins']==1)
							{
								$mHtml->Table("tr",array("class"=>"displayDIV2"));
									$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
									$mHtml->CloseRow();		
									$mHtml->Row();
										$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNcliID","name"=>"btnNcli", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(5)") );
									$mHtml->CloseRow();
								$mHtml->CloseTable('tr');
							
							}
							$mHtml->OpenDiv("id:tabCLIdl");
								$mHtml->SetBody(self::getDinamiList(self::getmPermCli(),$datos));
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();
					}
			$mHtml->OpenDiv("id:popID");
			$mHtml->CloseDiv();
			$mHtml->SetBody('<style>
	                      #tabResult{
								border: 1px solid rgb(201, 201, 201);
								padding: 3px;
								width: 100%;
								min-height: 50px;
								border-radius: 5px;
								background-color: rgb(240, 240, 240);
							}
	                    </style>');
			echo $mHtml->MakeHtml();
		} catch (Exception $e) {
			echo "error getForm :".$e;
		}
	}

	/*! \fn: getDinamiList
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  18/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getDinamiList($permisosActuales,$datos)
	{	
		try
		{
			$sql ="SELECT a.cod_notifi,a.nom_asunto,a.fec_creaci,b.cod_usuari,a.cod_tipnot,a.ind_notres,a.ind_notusr,a.ind_estado
					 FROM ".BASE_DATOS.".tab_notifi_notifi a
					 	INNER JOIN tab_genera_usuari b 
					 		ON a.usr_creaci=b.cod_consec
					 			WHERE a.ind_estado=1 AND a.cod_tipnot=".$datos->cod_notifi." and a.fec_creaci>='".$datos->fec_iniID." 00:00:00' AND a.fec_creaci<='".$datos->fec_finID." 23:59:59'";
			$_SESSION["queryXLS"] = $sql;
			$permActivado="field:cod_notifi; width:1%";
			$permActivado.=($permisosActuales['idi']==1)?";onclikEdit:editarNotifi( this )":"";
			$permActivado.=($permisosActuales['rep']==1)?";onclickCopy:FormResponNotifi( this )":"";
			$permActivado.=($permisosActuales['eli']==1)?";onclikDisable:FormeliminarNotifi( this )":"";
			if (!class_exists(DinamicList)) 
			{
			    include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
			}
			$list = new DinamicList(self::$cConexion, $sql, "1", "no", 'ASC');
			$list->SetClose('no');
			//$list->SetCreate("Crear Notificacion", "onclick:NuevaNoti(".$datos->cod_notifi.")");
			$list->SetHeader("Consecutivo", "field:cod_notifi; width:1%;type:link; onclick:verNotifi( $(this) )");
			$list->SetHeader("Asunto", "field:nom_asunto; width:1%");
			$list->SetHeader("Fecha y hora de creacion", "field:fec_creaci; width:1%");
			$list->SetHeader("Notificado por ", "field:cod_usuari; width:1%");
			$list->SetOption("Opciones", $permActivado);
			$list->SetHidden("cod_notifi", "cod_notifi");
	        $list->SetHidden("cod_tipnot", "cod_tipnot");
	        $list->SetHidden("nom_asunto", "nom_asunto");
	        $list->SetHidden("ind_notres", "ind_notres");
	        $list->SetHidden("ind_notusr", "ind_notusr");
			$list->Display(self::$cConexion);

			$_SESSION["DINAMIC_LIST"] = $list;

			$Html = $list->GetHtml();

        	return $Html;
			//return $list->GetHtml();
		} catch (Exception $e) {
			echo "error getDinamiList :".$e;
		}
	}

	/*! \fn: getFormNuevaNotifi
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifi()
	{	
		try
		{
			$datos = (object) $_REQUEST;
			#identifico el tipo de formulario a pintar
			if($datos->idForm=="3" || $datos->idForm=="4")
			{
				self::getFormNuevaNotifiSelect($datos);
			}
			else
			{
				self::getFormNuevaNotifiComun($datos);
			}
		} catch (Exception $e) {
			echo "error getFormNuevaNotifi :".$e;
		}
	}

	/*! \fn: getFormNuevaNotifiExt
	 *  \brief: identifica el formulario correspondiete a supervisores y controladores y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifiSelect($ActionForm=NULL)
	{
		try
		{
			#inicializo la fechas
			$date = new DateTime();
			#inicializo el formulario
			$mHtml = new Formlib(2);
			#identifico el tipo de ejecucion para realizar la consulta de la info
			if($ActionForm->ActionForm=="idi" || $ActionForm->ActionForm=="eli" || $ActionForm->ActionForm=="rep" || $ActionForm->ActionForm=="ver")
			{
				$datosConsult =self::getInfoNotifiEdit($ActionForm);
			}
			#pinto las opciones de formulario para supervisores y controladores
			if($ActionForm->ActionForm=="ins")
			{
				$mHtml->OpenDiv("id:newNotifiSelect");
					$mHtml->Table("tr");
					#Radio buton entrega de turno
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "ENTREGA DE TURNO",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Radio(array("value"=>"1","name" => "ind_enttur", "id" => "ind_entturID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="ins" || $ActionForm->radio=="")?"checked":($datosConsult[0][9]==1)?"checked":"","readonly"=>($ActionForm->ActionForm=="idi")?"readonly":$readonly, "disabled"=>($ActionForm->ActionForm=="idi")?"disabled":$disabled,"onclick"=>"CambioForm(1)"));
							$mHtml->Label( "OTRA NOTIFICACION",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"3"));
							$mHtml->Radio(array("value"=>"2","name" => "ind_enttur", "id" => "ind_entturID", "width" => "100%", "colspan"=>"2","checked"=>(($datosConsult[0][9]==2 || $ActionForm->radio==2)?"checked":""),"readonly"=>($ActionForm->ActionForm=="idi")?"readonly":$readonly, "disabled"=>($ActionForm->ActionForm=="idi")?"disabled":$disabled,"onclick"=>"CambioForm(2)"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			}
			else
			{
				$mHtml->Hidden(array( "name" => "ind_enttur", "value"=>$datosConsult[0][9]));
			}
			if((  $ActionForm->radio==1 || $datosConsult[0][9]==1)  )
			{
				$mHtml->SetBody(self::getFormNuevaNotifiExt($ActionForm));
			}
			
			else if(($ActionForm->radio==2 || $datosConsult[0][9]==2)  )
			{
				$mHtml->SetBody(self::getFormNuevaNotifiComun($ActionForm));
			}
			else
			{
				$mHtml->SetBody(self::getFormNuevaNotifiExt($ActionForm));
			}
			echo $mHtml->MakeHtml();
		} catch (Exception $e) {
			echo "error getFormNuevaNotifiSelect :".$e;
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
		try
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
				#decodifico el JSON y los subdibido
				$Json=json_decode($datosConsult[0][13]);
				$SUPERVISORES = self::JsonRecor($Json,"SUPERVISORES");
				$CONTROLADORES = self::JsonRecor($Json,"CONTROLADORES");
				$ENCUESTAS = self::JsonRecor($Json,"ENCUESTAS");
				$ESPECIFICAS = self::JsonRecor($Json,"ESPECIFICAS");
				$ASISTENCIAS = self::JsonRecor($Json,"ASISTENCIAS");
				$ESTADO_VEHICULOS = self::JsonRecor($Json,"ESTADO_VEHICULOS");
				$RECURSOS_ASIGNADOS = self::JsonRecor($Json,"RECURSOS_ASIGNADOS");
			}
			if($ActionForm->ActionForm=="eli" || $ActionForm->ActionForm=="rep" || $ActionForm->ActionForm=="ver")
			{
				#realizar consulta
				$datosConsult = self::getInfoNotifiEdit($ActionForm);
				#activacion para la no edicion de la info
				$readonly = "readonly";
				$disabled = "disabled";
				#decodifico el JSON y los subdibido
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
			$mHtml = new Formlib(2, "yes",TRUE);
			$mHtml->OpenDiv("id:Notificontainer1; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>INFORMACION BASICA</center></h3>");
				$mHtml->OpenDiv("id:newNotifi");
					$mHtml->Hidden(array( "name" => "usr_creaci", "id" => "usr_creaciID", "value"=>self::getCodUsuario($_SESSION['datos_usuario']['cod_usuari'])['cod_consec']));
					$mHtml->Hidden(array( "name" => "cod_tipnot", "id" => "cod_tipnotID", "value"=>$ActionForm->idForm));
					$mHtml->Hidden(array( "name" => "cod_notifi", "id" => "cod_notifiID", "value"=>($ActionForm->cod_notifi!="")?$ActionForm->cod_notifi:""));
					$mHtml->Table("tr");
						#Cuerpo de la notificacion
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
							$mHtml->TextArea(($datosConsult[0][4]!="")?$datosConsult[0][4]:"", array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%", "value"=>($datosConsult[0][4]!="")?$datosConsult[0][4]:"" ,"colspan"=>"6", "readonly"=>$readonly, "disabled"=>$disabled));
			                //$mHtml->Input(array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%", "value"=>($datosConsult[0][4]!="")?$datosConsult[0][4]:"" ,"colspan"=>"6", "readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "Fecha de Notificacion:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		                	$mHtml->Input(array("value"=>$date->format('Y-m-d H:i:s'),"name" => "fec_creaci", "id" => "fec_creaciID", "width" => "100%", "readonly"=>"readonly", "disabled"=>"disabled","colspan"=>"6"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "Notificado por:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		                	$mHtml->Input(array("value"=>$_SESSION['datos_usuario']['cod_usuari'],"name" => "usr_creaci", "id" => "usr_creaciID", "width" => "100%", "readonly"=>"readonly", "disabled"=>"disabled", "colspan"=>"1"));
		                	$mHtml->Label( "Horas laboradas:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
		                	$mHtml->Input(array("name" => "num_horlab", "id" => "num_horlabID", "width" => "100%", "colspan"=>"4", "value"=>($datosConsult[0][5]!="")?$datosConsult[0][5]:"", "readonly"=>($datosConsult[0][5]!="")?"readonly":"", "disabled"=>($datosConsult[0][5]!="")?"disabled":"", "onkeyup"=>"validarKey(1,2,'num','num_horlabID')"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "*Vigencia hasta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Input(array("name" => "fec_vigenc", "id" => "fec_vigencID", "width" => "100%", "colspan"=>"1", "value"=>($datosConsult[0][6]!="")?$datosConsult[0][6]:""/*,"onclick"=>"getFechaDatapick('fec_vigencID')"*/,"readonly"=>$readonly, "disabled"=>$disabled));
							$mHtml->Label( "*Requiere Respuesta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "SI",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Radio(array("value"=>"1","name" => "ind_respue", "id" => "ind_respuesID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="ins")?"checked":($datosConsult[0][7]==1)?"checked":"","readonly"=>$readonly, "disabled"=>$disabled));
							$mHtml->Label( "NO",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Radio(array("value"=>"0","name" => "ind_respue", "id" => "ind_respuenID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm!="ins")?($datosConsult[0][7]==0)?"checked":"":"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "*Publicar a:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Select2 (self::getLisRespon(),  array("name" => "cod_asires", "width" => "25%","colspan"=>"1") );
							#si es supervisor pinta campos adicionales
							$mHtml->Label( "Usuarios:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Select2 ("",  array("name" => "ind_notusr", "width" => "25%","colspan"=>"4") );
							
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			#Formulario de diligenciamiento
			#si es supervisor pinta campos adicionales
			if($ActionForm->idForm==3){
				$mHtml->OpenDiv("id:Notificontainer2; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>SUPERVISORES</center></h3>");
					$mHtml->OpenDiv("id:jsonFormDigi");
						$mHtml->Table("tr");
							/*$mHtml->Row();
								$mHtml->Label( "*FORMULARIO DE DILIGENCIAMIENTO:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
							$mHtml->CloseRow();
							*/
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
							/*$mHtml->Row();
								$mHtml->Label( "SUPERVISORES",  array("align"=>"center", "class"=>"celda_titulo infJson","colspan"=>"7") );
							$mHtml->CloseRow();*/
							$mHtml->Row();
								$mHtml->Label( "Supervisor Entrante",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
								$mHtml->Label( "Controlador Master Entrante",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
								$mHtml->Label( "Supervisor Saliente",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Input(array("name" => "supe_entrante", "id" => "supe_entranteID", "width" => "100%", "colspan"=>"2", "value"=>(self::JsonRecor($SUPERVISORES,"supe_entrante")!="")?self::JsonRecor($SUPERVISORES,"supe_entrante"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,20,'alfa','supe_entranteID')"));
								$mHtml->Input(array("name" => "cont_Mentrant", "id" => "cont_MentrantID", "width" => "100%", "colspan"=>"2", "value"=>(self::JsonRecor($SUPERVISORES,"cont_Mentrant")!="")?self::JsonRecor($SUPERVISORES,"cont_Mentrant"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,20,'alfa','cont_MentrantID')"));
								$mHtml->Input(array("name" => "supe_saliente", "id" => "supe_salienteID", "width" => "100%", "colspan"=>"3", "value"=>(self::JsonRecor($SUPERVISORES,"supe_saliente")!="")?self::JsonRecor($SUPERVISORES,"supe_saliente"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,20,'alfa','supe_salienteID')"));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
				$mHtml->OpenDiv("id:Notificontainer3; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>CONTROLADORES</center></h3>");
					$mHtml->OpenDiv("id:jsonContro");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
							/*$mHtml->Row();
								$mHtml->Label( "CONTROLADORES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
							$mHtml->CloseRow();*/
							$mHtml->Row();
								$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "En Turno",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "Ausentes",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "Incapacitados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Input(array("name" => "numb_enturno", "id" => "numb_enturnoID", "width" => "10%", "colspan"=>"1", "value"=>(self::JsonRecor($CONTROLADORES,"numb_enturno")!="")?self::JsonRecor($CONTROLADORES,"numb_enturno"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_enturnoID')"));
								$mHtml->Input(array("name" => "cont_enturno", "id" => "cont_enturnoID", "width" => "100%", "colspan"=>"1", "value"=>(self::JsonRecor($CONTROLADORES,"cont_enturno")!="")?self::JsonRecor($CONTROLADORES,"cont_enturno"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,50,'alfa','cont_enturnoID')"));
								$mHtml->Input(array("name" => "numb_ausente", "id" => "numb_ausenteID", "width" => "10%", "colspan"=>"1", "value"=>(self::JsonRecor($CONTROLADORES,"numb_ausente")!="")?self::JsonRecor($CONTROLADORES,"numb_ausente"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_ausenteID')"));
								$mHtml->Input(array("name" => "cont_ausente", "id" => "cont_ausenteID", "width" => "100%", "colspan"=>"1", "value"=>(self::JsonRecor($CONTROLADORES,"cont_ausente")!="")?self::JsonRecor($CONTROLADORES,"cont_ausente"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,50,'alfa','cont_ausenteID')"));
								$mHtml->Input(array("name" => "numb_incapac", "id" => "numb_incapacID", "width" => "10%", "colspan"=>"1", "value"=>(self::JsonRecor($CONTROLADORES,"numb_incapac")!="")?self::JsonRecor($CONTROLADORES,"numb_incapac"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_incapacID')"));
								$mHtml->Input(array("name" => "supe_incapac", "id" => "supe_incapacID", "width" => "100%", "colspan"=>"2", "value"=>(self::JsonRecor($CONTROLADORES,"supe_incapac")!="")?self::JsonRecor($CONTROLADORES,"supe_incapac"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,50,'alfa','supe_incapacID')"));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			}
			$mHtml->OpenDiv("id:Notificontainer4; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>ESTADO DE VEHICULOS</center></h3>");
				$mHtml->OpenDiv("id:jsonEstVehi");
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						/*$mHtml->Row();
							$mHtml->Label( "ESTADO DE VEHICULOS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();*/
						$mHtml->Row();
							$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "Cargue",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "Transito",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "Descargue",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Input(array("name" => "numb_cargue", "id" => "numb_cargueID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_cargue")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_cargue"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_cargueID')"));
							$mHtml->Input(array("name" => "vehi_cargue", "id" => "vehi_cargueID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_cargue")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_cargue"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_cargueID')"));
							$mHtml->Input(array("name" => "numb_transi", "id" => "numb_transiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_transi")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_transi"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_transiID')"));
							$mHtml->Input(array("name" => "vehi_transi", "id" => "vehi_transiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_transi")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_transi"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_transiID')"));
							$mHtml->Input(array("name" => "numb_descar", "id" => "numb_descarID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_descar")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_descar"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_descarID')"));
							$mHtml->Input(array("name" => "vehi_descar", "id" => "vehi_descarID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_descar")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_descar"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_descarID')"));
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
							$mHtml->Input(array("name" => "numb_carempr", "id" => "numb_caremprID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_carempr")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_carempr"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_caremprID')"));
							$mHtml->Input(array("name" => "vehi_carempr", "id" => "vehi_caremprID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_carempr")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_carempr"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_caremprID')"));
							$mHtml->Input(array("name" => "numb_penlleg", "id" => "numb_penllegID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_penlleg")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_penlleg"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_penllegID')"));
							$mHtml->Input(array("name" => "vehi_penlleg", "id" => "vehi_penllegID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_penlleg")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_penlleg"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_penllegID')"));
							$mHtml->Input(array("name" => "numb_pernota", "id" => "numb_pernotaID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_pernota")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_pernota"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_pernotaID')"));
							$mHtml->Input(array("name" => "vehi_pernota", "id" => "vehi_pernotaID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_pernota")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_pernota"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_pernotaID')"));
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
							$mHtml->Input(array("name" => "numb_seguesp", "id" => "numb_seguespID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_seguesp")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_seguesp"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_seguespID')"));
							$mHtml->Input(array("name" => "vehi_seguesp", "id" => "vehi_seguespID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_seguesp")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_seguesp"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_seguespID')"));
							$mHtml->Input(array("name" => "numb_recomen", "id" => "numb_recomenID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_recomen")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_recomen"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_recomenID')"));
							$mHtml->Input(array("name" => "vehi_recomen", "id" => "vehi_recomenID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_recomen")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_recomen"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_recomenID')"));
							$mHtml->Input(array("name" => "numb_prevent", "id" => "numb_preventID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_prevent")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_prevent"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_preventID')"));
							$mHtml->Input(array("name" => "vehi_prevent", "id" => "vehi_preventID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_prevent")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_prevent"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_preventID')"));
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
							$mHtml->Input(array("name" => "numb_hurtado", "id" => "numb_hurtadoID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_hurtado")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_hurtado"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_hurtadoID')"));
							$mHtml->Input(array("name" => "vehi_hurtado", "id" => "vehi_hurtadoID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_hurtado")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_hurtado"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_hurtadoID')"));
							$mHtml->Input(array("name" => "numb_acciden", "id" => "numb_accidenID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_acciden")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_acciden"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_accidenID')"));
							$mHtml->Input(array("name" => "vehi_acciden", "id" => "vehi_accidenID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_acciden")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_acciden"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_accidenID')"));
							$mHtml->Input(array("name" => "numb_transbo", "id" => "numb_transboID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"numb_transbo")!="")?self::JsonRecor($ESTADO_VEHICULOS,"numb_transbo"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_transboID')"));
							$mHtml->Input(array("name" => "vehi_transbo", "id" => "vehi_transboID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ESTADO_VEHICULOS,"vehi_transbo")!="")?self::JsonRecor($ESTADO_VEHICULOS,"vehi_transbo"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','vehi_transboID')"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			#si es supervisor pinta campos adicionales
			if($ActionForm->idForm==3){
				$mHtml->OpenDiv("id:Notificontainer5; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>ENCUESTAS</center></h3>");
					$mHtml->OpenDiv("id:jsonEncu");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
							/*$mHtml->Row();
								$mHtml->Label( "ENCUESTAS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
							$mHtml->CloseRow();*/
							$mHtml->Row();
								$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "Realizadas",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "Registradas",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
								$mHtml->Label( "Por subir a SPG",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Input(array("name" => "numb_enreali", "id" => "numb_enrealiID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUESTAS,"numb_enreali")!="")?self::JsonRecor($ENCUESTAS,"numb_enreali"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_enrealiID')"));
								$mHtml->Input(array("name" => "encu_enreali", "id" => "encu_enrealiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUESTAS,"encu_enreali")!="")?self::JsonRecor($ENCUESTAS,"encu_enreali"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','encu_enrealiID')"));
								$mHtml->Input(array("name" => "numb_registr", "id" => "numb_registrID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUESTAS,"numb_registr")!="")?self::JsonRecor($ENCUESTAS,"numb_registr"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_registrID')"));
								$mHtml->Input(array("name" => "encu_registr", "id" => "encu_registrID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUESTAS,"encu_registr")!="")?self::JsonRecor($ENCUESTAS,"encu_registr"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','encu_registrID')"));
								$mHtml->Input(array("name" => "numb_subaspg", "id" => "numb_subaspgID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ENCUESTAS,"numb_subaspg")!="")?self::JsonRecor($ENCUESTAS,"numb_subaspg"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_subaspgID')"));
								$mHtml->Input(array("name" => "encu_subaspg", "id" => "encu_subaspgID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($ENCUESTAS,"encu_subaspg")!="")?self::JsonRecor($ENCUESTAS,"encu_subaspg"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','encu_subaspgID')"));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
				$mHtml->OpenDiv("id:Notificontainer6; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>ESPECIFICAS</center></h3>");
					$mHtml->OpenDiv("id:jsonEspeci");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
							/*$mHtml->Row();
								$mHtml->Label( "ESPECIFICAS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
							$mHtml->CloseRow();*/
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
								$mHtml->Input(array("name" => "numb_esreali", "id" => "numb_esrealiID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_esreali")!="")?self::JsonRecor($ESPECIFICAS,"numb_esreali"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_esrealiID')"));
								$mHtml->Input(array("name" => "espe_esreali", "id" => "espe_esrealiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"espe_esreali")!="")?self::JsonRecor($ESPECIFICAS,"espe_esreali"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','espe_esrealiID')"));
								$mHtml->Input(array("name" => "numb_esconsc", "id" => "numb_esconscID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_esconsc")!="")?self::JsonRecor($ESPECIFICAS,"numb_esconsc"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,6,'num','numb_esconscID')"));
								$mHtml->Input(array("name" => "numb_espende", "id" => "numb_espendeID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_espende")!="")?self::JsonRecor($ESPECIFICAS,"numb_espende"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_espendeID')"));
								$mHtml->Input(array("name" => "espe_espende", "id" => "espe_espendeID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"espe_espende")!="")?self::JsonRecor($ESPECIFICAS,"espe_espende"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','espe_espendeID')"));
								$mHtml->Input(array("name" => "numb_espenda", "id" => "numb_espendaID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"numb_espenda")!="")?self::JsonRecor($ESPECIFICAS,"numb_espenda"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_espendaID')"));
								$mHtml->Input(array("name" => "espe_espenda", "id" => "espe_espendaID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ESPECIFICAS,"espe_espenda")!="")?self::JsonRecor($ESPECIFICAS,"espe_espenda"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','espe_espendaID')"));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
				$mHtml->OpenDiv("id:Notificontainer7; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>ASISTENCIAS</center></h3>");
					$mHtml->OpenDiv("id:jsonAsist");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
							/*$mHtml->Row();
								$mHtml->Label( "ASISTENCIAS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
							$mHtml->CloseRow();*/
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
								$mHtml->Input(array("name" => "numb_asreali", "id" => "numb_asrealiID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_asreali")!="")?self::JsonRecor($ASISTENCIAS,"numb_asreali"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_asrealiID')"));
								$mHtml->Input(array("name" => "espe_asreali", "id" => "espe_asrealiID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"espe_asreali")!="")?self::JsonRecor($ASISTENCIAS,"espe_asreali"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','espe_asrealiID')"));
								$mHtml->Input(array("name" => "numb_asconsc", "id" => "numb_asconscID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_asconsc")!="")?self::JsonRecor($ASISTENCIAS,"numb_asconsc"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,6,'num','numb_asconscID')"));
								$mHtml->Input(array("name" => "numb_aspende", "id" => "numb_aspendeID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_aspende")!="")?self::JsonRecor($ASISTENCIAS,"numb_aspende"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_aspendeID')"));
								$mHtml->Input(array("name" => "espe_aspende", "id" => "espe_aspendeID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"espe_aspende")!="")?self::JsonRecor($ASISTENCIAS,"espe_aspende"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','espe_aspendeID')"));
								$mHtml->Input(array("name" => "numb_aspenda", "id" => "numb_aspendaID", "width" => "10%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"numb_aspenda")!="")?self::JsonRecor($ASISTENCIAS,"numb_aspenda"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','numb_aspendaID')"));
								$mHtml->Input(array("name" => "espe_aspenda", "id" => "espe_aspendaID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($ASISTENCIAS,"espe_aspenda")!="")?self::JsonRecor($ASISTENCIAS,"espe_aspenda"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,150,'alfa','espe_aspendaID')"));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			}
			$mHtml->OpenDiv("id:Notificontainer8; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>RECURSOS ASIGNADOS</center></h3>");
				$mHtml->OpenDiv("id:jsonRecurAsi");
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						/*$mHtml->Row();
							$mHtml->Label( "RECURSOS ASIGNADOS",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();*/
						$mHtml->Row();
							$mHtml->Label( "N° de puesto Asignado",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->Label( "Estado de la diadema",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->Label( "Estado del mouse",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Input(array("name" => "reas_npuestoa", "id" => "reas_npuestoaID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_npuestoa")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_npuestoa"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,2,'num','reas_npuestoaID')"));
							$mHtml->Input(array("name" => "reas_ediadema", "id" => "reas_ediademaID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_ediadema")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_ediadema"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_ediademaID')"));
							$mHtml->Input(array("name" => "reas_estmouse", "id" => "reas_estmouseID", "width" => "100%", "colspan"=>"3","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estmouse")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estmouse"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_estmouseID')"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "Estado del Equipo P/C",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->Label( "Estado del teclado",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->Label( "Estado de la silla",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"3") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Input(array("name" => "reas_equipcx", "id" => "reas_equipcxID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_equipcx")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_equipcx"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_equipcxID')"));
							$mHtml->Input(array("name" => "reas_teclado", "id" => "reas_tecladoID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_teclado")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_teclado"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_tecladoID')"));
							$mHtml->Input(array("name" => "reas_essilla", "id" => "reas_essillaID", "width" => "100%", "colspan"=>"3","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_essilla")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_essilla"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_essillaID')"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "Estado del Pad Mouse",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->Label( "Aseo",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
							$mHtml->Label( "N°",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "Registros realizados",  array("align"=>"left", "class"=>"celda_titulo","colspan"=>"2") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Input(array("name" => "reas_padmous", "id" => "reas_padmousID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_padmous")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_padmous"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_padmousID')"));
							$mHtml->Input(array("name" => "reas_estaseo", "id" => "reas_estaseoID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estaseo")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_estaseo"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(4,20,'alfa','reas_estaseoID')"));
							$mHtml->Input(array("name" => "numb_regisre", "id" => "numb_regisreID", "width" => "100%", "colspan"=>"1","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"numb_regisre")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"numb_regisre"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,5,'num','numb_regisreID')"));
							$mHtml->Input(array("name" => "reas_regisre", "id" => "reas_regisreID", "width" => "100%", "colspan"=>"2","value"=>(self::JsonRecor($RECURSOS_ASIGNADOS,"reas_regisre")!="")?self::JsonRecor($RECURSOS_ASIGNADOS,"reas_regisre"):"","readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(1,20,'alfa','reas_regisreID')"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:Notificontainer9; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>OTRAS OBSERVACIONES</center></h3>");
				$mHtml->OpenDiv("id:jsonOtrasObserv");
					$mHtml->Table("tr");
						#detalle de la notificacion
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						/*$mHtml->Row();
							$mHtml->Label( "OTRAS OBSERVACIONES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();*/
						$mHtml->Row();
		                	$mHtml->TextArea(($datosConsult[0][8]!="")?$datosConsult[0][8]:"", array("name" => "obs_notifi", "id" => "obs_notifiID", "colspan"=>"7", "readonly"=>$readonly, "disabled"=>$disabled, "onkeyup"=>"validarKey(5,500,'alfa','obs_notifiID')"));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						#documentos
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:Notificontainer10; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>DOCUMENTOS</center></h3>");
				$mHtml->OpenDiv("id:Document");
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						if($ActionForm->ActionForm=="ins")
						{
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
			                	$mHtml->file(array("name" => "file_1", "id" => "file_1ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
							$mHtml->CloseRow();
							#si es supervisor pinta campos adicionales
							if($ActionForm->idForm==3){
								$mHtml->Row();
									$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
				                	$mHtml->file(array("name" => "file_2", "id" => "file_2ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
								$mHtml->CloseRow();
								$mHtml->Row();
									$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
				                	$mHtml->file(array("name" => "file_3", "id" => "file_3ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
								$mHtml->CloseRow();
								$mHtml->Row();
									$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
				                	$mHtml->file(array("name" => "file_4", "id" => "file_4ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
								$mHtml->CloseRow();
							}
						}
						else
						{
							$document=self::getDocument($ActionForm);
							$numDocAct=sizeof($document);
							if($document)
							{
								foreach ($document as $keyDA => $valueDA) 
								{
									$mHtml->Row();
										$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->Label( $valueDA['nom_ficher'],  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
										if($ActionForm->ActionForm=="idi")
										{
											$mHtml->Image(array("value"=>"Eliminar", "name"=>"Efile_".$valueDA['cod_consec'], "align"=>"right","iwidth"=>"20","iheight"=>"20", "colspan"=>"2","onclick"=>"delArchivos(".$valueDA['cod_consec'].")", "src"=> "../".DIR_APLICA_CENTRAL."/images/delete.png") );
										}
										$mHtml->Image(array("value"=>"Vizualizar", "id"=>"Vfile_1ID","name"=>"Vfile_".$valueDA['cod_consec'], "iwidth"=>"20","iheight"=>"20", "colspan"=>(($ActionForm->ActionForm=="idi")?"3":"5"),"onclick"=>"verArchivos(".$valueDA['cod_consec'].")", "src"=> "../".DIR_APLICA_CENTRAL."/imagenes/ver.png") );
									$mHtml->CloseRow();
								}
							}
							if($ActionForm->idForm==3 || $ActionForm->idForm==4)
							{
								$validar;
								($ActionForm->idForm==3)?$validar=4:$validar=1;
								($numDocAct=="")?"0":$numDocAct;
								if($numDocAct<=$validar)
								{
									for($ad=$numDocAct;$ad<$validar;$ad++)
									{	
										$mHtml->Row();
											$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
							               	$mHtml->file(array("name" => "file_".$ad, "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
										$mHtml->CloseRow();
									}
								}
							}
						}
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();	
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			if($ActionForm->ActionForm=="rep")
			{
				$mHtml->OpenDiv("id:Notificontainer11; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>HISTORIAL NOTIFICACIONES</center></h3>");
					$mHtml->OpenDiv("id:reponNotif");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
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
											$mHtml->Label( "Historial:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
							            	//$mHtml->Input(array("name" => "nom_asunto".$keyRH, "width" => "100%", "value"=>$valueRH ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
							            	$mHtml->TextArea($valueRH, array("name" => "nom_asunto".$keyRH, "width" => "100%", "value"=>$valueRH ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
					            		$mHtml->CloseRow();
					            		$mHtml->SetBody('<style type="text/css">
														  #'.'nom_asunto'.$keyRH.'ID'.' {
														    height: 50px;
	    													width: 100%;
														}
														  </style>');
									}
								}
							}
							if($datosConsult[0][7]==1)
							{
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
							}
							$mHtml->Row();
								$mHtml->line("","i",0,7);
							$mHtml->CloseRow();
		            	$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			}
			$mHtml->OpenDiv("id:btnNotifi");
				$mHtml->Table("tr");
					$mHtml->Row();
						$mHtml->line("","i",0,7);
					$mHtml->CloseRow();
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
							if($datosConsult[0][7]==1)
							{
								$mHtml->Button( array("value"=>"RESPONDER", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"responNotifi()") );
							}
						}
						$mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"5","onclick"=>"limpiarForm()") );
					$mHtml->CloseRow();
					$mHtml->Row();
						$mHtml->line("","i",0,7);
					$mHtml->CloseRow();
				$mHtml->CloseTable('tr');
			$mHtml->CloseDiv();	

			$mHtml->SetBody('<script>
							$("#fec_vigencID").datepicker({
						        changeMonth: true,
						        changeYear: true,
						        dateFormat: "yy-mm-dd",
						      });
	                        $( function() {
							    $( "#Notificontainer1, #Notificontainer2, #Notificontainer3, #Notificontainer4, #Notificontainer5, #Notificontainer6, #Notificontainer7, #Notificontainer8, #Notificontainer9, #Notificontainer10, #Notificontainer11" ).accordion({
							      	autoHeight: false,
								    collapsible: true
								    //,active: false
							    });
							} );
	                    </script>');
			return $mHtml->MakeHtml();
		} catch (Exception $e) {
			echo "error getFormNuevaNotifiExt :".$e;
		}
	}

	/*! \fn: getFormNuevaNotifiComun
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifiComun($ActionForm=NULL)
	{
		try
		{
			$datosConsult;
			$readonly ="";
			$disabled ="";
			if($ActionForm->ActionForm=="idi")
			{
				#realizar consulta
				$datosConsult =self::getInfoNotifiEdit($ActionForm);
			}
			if($ActionForm->ActionForm=="eli" || $ActionForm->ActionForm=="rep" || $ActionForm->ActionForm=="ver")
			{
				#realizar consulta
				$datosConsult = self::getInfoNotifiEdit($ActionForm);
				$readonly = "readonly";
				$disabled = "disabled";
			}
			#print_r($datosConsult[0]);
			$date = new DateTime();
			if($ActionForm->idForm==3 || $ActionForm->idForm==4)
			{
				$mHtml = new Formlib(2, "yes",TRUE);
			}
			else
			{
				$mHtml = new Formlib(2);
			}
			$mHtml->OpenDiv("id:Notificontainer1; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>INFORMACION BASICA</center></h3>");
				$mHtml->OpenDiv("id:newNotifi; class:accordian");
					$mHtml->Hidden(array( "name" => "usr_creaci", "id" => "usr_creaciID", "value"=>self::getCodUsuario($_SESSION['datos_usuario']['cod_usuari'])['cod_consec']));
					$mHtml->Hidden(array( "name" => "cod_tipnot", "id" => "cod_tipnotID", "value"=>$ActionForm->idForm));
					$mHtml->Hidden(array( "name" => "cod_notifi", "id" => "cod_notifiID", "value"=>($ActionForm->cod_notifi!="")?$ActionForm->cod_notifi:""));
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						#ASUNTO
						$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
						$mHtml->TextArea(($datosConsult[0][4]!="")?$datosConsult[0][4]:"", array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%", "value"=>($datosConsult[0][4]!="")?$datosConsult[0][4]:"" ,"colspan"=>"6", "readonly"=>$readonly, "disabled"=>$disabled));
		                //$mHtml->Input(array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%", "value"=>($datosConsult[0][4]!="")?$datosConsult[0][4]:"" ,"colspan"=>"6", "readonly"=>$readonly, "disabled"=>$disabled));
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
						#VIGENCIA HASTA
						$mHtml->Row();
							$mHtml->Label( "*Vigencia hasta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Input(array("name" => "fec_vigenc", "id" => "fec_vigencID", "width" => "100%", "colspan"=>"1", "value"=>($datosConsult[0][6]!="")?$datosConsult[0][6]:""/*,"onclick"=>"getFechaDatapick('fec_vigencID')"*/,"readonly"=>$readonly, "disabled"=>$disabled));
							$mHtml->Label( "*Requiere Respuesta:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Label( "SI",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Radio(array("value"=>"1","name" => "ind_respue", "id" => "ind_respuesID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm=="ins")?"checked":($datosConsult[0][7]==1)?"checked":"","readonly"=>$readonly, "disabled"=>$disabled));
							$mHtml->Label( "NO",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Radio(array("value"=>"0","name" => "ind_respue", "id" => "ind_respuenID", "width" => "100%", "colspan"=>"1","checked"=>($ActionForm->ActionForm!="ins" )?($datosConsult[0][7]==0)?"checked":"":"","readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->CloseRow();
						#PUBLICAR A Y USUARIOS
						$mHtml->Row();
							$mHtml->Label( "*Publicar a:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Select2 (self::getLisRespon(),  array("name" => "cod_asires", "width" => "25%","colspan"=>"1") );
							$mHtml->Label( "Usuarios:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Select2 ("",  array("name" => "ind_notusr", "width" => "25%","colspan"=>"4") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "*DETALLE DE LA NOTIFICACION:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();
						$mHtml->Row();
		                	$mHtml->TextArea(($datosConsult[0][8]!="")?$datosConsult[0][8]:"", array("name" => "obs_notifi", "id" => "obs_notifiID", "colspan"=>"7", "readonly"=>$readonly, "disabled"=>$disabled));
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:Notificontainer2; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>DOCUMENTOS</center></h3>");
				$mHtml->OpenDiv("id:Document");
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						/*$mHtml->Row();
							$mHtml->Label( "Documentos:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
						$mHtml->CloseRow();*/
						if($ActionForm->ActionForm=="ins")
						{
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
			                	$mHtml->file(array("name" => "file_1", "id" => "file_1ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
			                	$mHtml->file(array("name" => "file_2", "id" => "file_2ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
			                	$mHtml->file(array("name" => "file_3", "id" => "file_3ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
			                	$mHtml->file(array("name" => "file_4", "id" => "file_4ID", "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
							$mHtml->CloseRow();
						}
						else
						{
							$document=self::getDocument($ActionForm);
							$numDocAct=sizeof($document);
							if($document)
							{
								foreach ($document as $keyDA => $valueDA) 
								{
									$mHtml->Row();
										$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->Label( $valueDA['nom_ficher'],  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
										if($ActionForm->ActionForm=="idi")
										{
											$mHtml->Image(array("value"=>"Eliminar", "name"=>"Efile_".$valueDA['cod_consec'], "align"=>"right","iwidth"=>"20","iheight"=>"20", "colspan"=>"2","onclick"=>"delArchivos(".$valueDA['cod_consec'].")", "src"=> "../".DIR_APLICA_CENTRAL."/images/delete.png") );
										}
										$mHtml->Image(array("value"=>"Vizualizar", "id"=>"Vfile_1ID","name"=>"Vfile_".$valueDA['cod_consec'], "iwidth"=>"20","iheight"=>"20", "colspan"=>(($ActionForm->ActionForm=="idi")?"3":"5"),"onclick"=>"verArchivos(".$valueDA['cod_consec'].")", "src"=> "../".DIR_APLICA_CENTRAL."/imagenes/ver.png") );
										
									$mHtml->CloseRow();
								}
							}
							($numDocAct=="")?"0":$numDocAct;
							if($numDocAct<=4)
							{
								for($ad=$numDocAct;$ad<4;$ad++)
								{
									$mHtml->Row();
										$mHtml->Label( "ADJUNTO :",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
						               	$mHtml->file(array("name" => "file_".$ad, "width" => "100%", "colspan"=>"6","readonly"=>$readonly,"disabled"=>$disabled));
									$mHtml->CloseRow();
								}
							}
						}
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();					
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			$mHtml->OpenDiv("id:btnoption");
				if($ActionForm->ActionForm=="rep")
				{
					$mHtml->OpenDiv("id:Notificontainer3; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>HISTORIAL NOTIFICACIONES</center></h3>");
						$mHtml->OpenDiv("id:reponNotif");
							$mHtml->Table("tr");
								$mHtml->Row();
									$mHtml->line("","i",0,7);
								$mHtml->CloseRow();
								$hisNotifi=self::getHistoNotifi($ActionForm);
								if($hisNotifi)
								{
									$mHtml->Row();
										$mHtml->Label( "HISTORIAL NOTIFICACIONES",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"7") );
									$mHtml->CloseRow();
									foreach ($hisNotifi as $keyHN => $valueHN) {
										foreach ($valueHN as $keyRH => $valueRH) {
											$mHtml->Row();
												$mHtml->Label( "Historial:",  array("align"=>"right", "class"=>"celda_titulo", "colspan"=>"1") );
								            	//$mHtml->Input(array("name" => "nom_asunto".$keyRH, "width" => "100%", "value"=>$valueRH ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
								            	$mHtml->TextArea($valueRH, array("name" => "nom_asunto".$keyRH, "width" => "100%", "value"=>$valueRH ,"colspan"=>"6", "readonly"=>"readonly", "disabled"=>"disabled"));
						            		$mHtml->CloseRow();
						            		$mHtml->SetBody('<style type="text/css">
															  #'.'nom_asunto'.$keyRH.'ID'.' {
															    height: 50px;
		    													width: 100%;
															}
															  </style>');
										}
									}
								}
								if($datosConsult[0][7]==1)
								{
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
								}
								$mHtml->Row();
									$mHtml->line("","i",0,7);
								$mHtml->CloseRow();
			            	$mHtml->CloseTable('tr');
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				}
				$mHtml->OpenDiv("id:btnNotifi");
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
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
								if($datosConsult[0][7]==1)
								{
									$mHtml->Button( array("value"=>"RESPONDER", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"2","onclick"=>"responNotifi()") );
								}
							}
							
							$mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save", "align"=>"right", "colspan"=>"5","onclick"=>"limpiarForm()") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			$mHtml->SetBody('<script>
							$("#fec_vigencID").datepicker({
						        changeMonth: true,
						        changeYear: true,
						        dateFormat: "yy-mm-dd",
						      });
	                        $( function() {
							    $( "#Notificontainer1, #Notificontainer2, #Notificontainer3" ).accordion({
							      	autoHeight: false
								    ,collapsible: true
							    });
							} );
	                    </script>');

			if($ActionForm->idForm==3 || $ActionForm->idForm==4)
			{
				return $mHtml->MakeHtml();
			}
			else
			{
				echo $mHtml->MakeHtml();
			}
		} catch (Exception $e) {
			echo "error getFormNuevaNotifiComun :".$e;
		}
	}

	/*! \fn: getLisRespon
	 *  \brief: devuelve array de responsables
	 *  \author: Edward Serrano
	 *	\date:  10/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getLisRespon ( $mCodNivel = NULL)
	{
	    try
	    {
	        $mSelect = "SELECT cod_respon, nom_respon FROM ".BASE_DATOS.".tab_genera_respon";
		    $mConsult = new Consulta($mSelect, self::$cConexion );
		    $_RESPON = $mConsult -> ret_matrix("i");
		    $inicio[0][0]=0;
		    $inicio[0][1]='-';
		    $_RESPON=array_merge($inicio,$_RESPON);
		    return $_RESPON;
		} catch (Exception $e) {
			echo "error getLisRespon :".$e;
		}
	}

	/*! \fn: getInfoNotifiEdit
	 *  \brief: devuelve array de notificaciones
	 *  \author: Edward Serrano
	 *	\date:  18/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getInfoNotifiEdit ( $mData = NULL)
	{
		try
		{
	        $mSelect = "SELECT  a.ind_enttur AS ind_enttur
	        					FROM ".BASE_DATOS.".tab_notifi_notifi a
	        						WHERE a.cod_notifi=$mData->cod_notifi AND a.nom_asunto='$mData->nom_asunto' AND cod_tipnot=$mData->idForm";
	        $mConsult = new Consulta($mSelect, self::$cConexion );
		    $_RESPON = $mConsult -> ret_matrix("i");
	        if($_RESPON[0][0]==1)
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
	        else if($_RESPON[0][0]==2)
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
		} catch (Exception $e) {
			echo "error getInfoNotifiEdit :".$e;
		}
	}


	/*! \fn: getNomUsuario
	 *  \brief: devuelve array de usuarios
	 *  \author: Edward Serrano
	 *	\date:  10/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getNomUsuario()
	{  
		try
		{
			$datos = (object) $_POST;
			$cod_respon= substr($datos->cod_Respon, 1);
		    $mSql = "SELECT a.cod_consec, UPPER(a.nom_usuari) AS nom_tercer 
		    				FROM ".BASE_DATOS.".tab_genera_usuari a 
		    					INNER JOIN ".BASE_DATOS.".tab_genera_perfil b ON a.cod_perfil=b.cod_perfil 
		    						WHERE b.cod_respon IN (".$cod_respon.") AND a.ind_estado=1";
		    $consulta = new Consulta( $mSql, self::$cConexion);
		    $mResult = $consulta -> ret_matrix('i');
		    $retr="<option value='0' selected='selected'>-</option>";
		    foreach ($mResult as $key => $value) {
		    	$retr.="<option value='".$value[0]."'>".$value[1]."</option>";
		    }
		    echo $retr;
		} catch (Exception $e) {
			echo "error getNomUsuario :".$e;
		}
	}

	/*! \fn: NuevaNotifiComun
	 *  \brief: alamcena las nuevas notificaciones
	 *  \author: Edward Serrano
	 *	\date:  12/01/2017
	 *	\date modified: dia/mes/año
	 */
	function NuevaNotifiComun()
	{
		try
		{
			$datos = (object) $_REQUEST;
			$files = array();
			$Estados = array();
			$dirServ = "../../".BASE_DATOS."/filnot/";
			if($datos->nom_asunto!="" && $datos->fec_creaci!="" && $datos->usr_creaci!="" && $datos->cod_asires!="" && $datos->ind_notusr!="" && $datos->fec_vigenc!="" && $datos->ind_respue!="" && $datos->obs_notifi!="" && $datos->cod_tipnot!="")
			{
				$sql ="INSERT INTO " . BASE_DATOS . ".tab_notifi_notifi 
	                                        (cod_tipnot,	ind_notres,		ind_notusr,
	                                         nom_asunto,	fec_vigenc,		ind_respue,	
	                                         obs_notifi,	ind_estado,		usr_creaci,	
	                                         fec_creaci,    ind_enttur)
	                                VALUES  
	                                		($datos->cod_tipnot,'".str_replace("'", "", substr($datos->cod_asires, 1))."','".str_replace("'", "", substr($datos->ind_notusr, 1))."',
	                                		'$datos->nom_asunto', '$datos->fec_vigenc',$datos->ind_respue, 
	                                		'$datos->obs_notifi', 1,$datos->usr_creaci, 
	                                		NOW(), 2)" ;
	            $consulta = new Consulta($sql, self::$cConexion, "BR");
	            if($consulta)
	            {
	            	#consulto el utimo registro de la tabla tab_notifi_notifi y con la fecha alamcenada
		        	$sqlNotifi = 'SELECT MAX(cod_notifi) AS cod_notifi FROM ' . BASE_DATOS . '.tab_notifi_notifi';
		        	$consultaNotifi = new Consulta( $sqlNotifi, self::$cConexion);
			    	$mResultNotifi = $consultaNotifi -> ret_matrix('i');
			    	$rCod_notifi=$mResultNotifi[0][0];
			    	$datos->cod_notifi=$rCod_notifi;
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
	            	$Estados["ERROR"]["consulta"]="error en consulta generar notificaion";
	            }
			}
			else
			{
				$Estados["ERROR"]["validacion"]="Campos obligatios";
			
			}
			if(!$Estados["ERROR"]){
				$Estados["OK"]="OK";
				$consultaFinal = new Consulta("COMMIT", self::$cConexion);
				self::getPlantillaEnvio($datos);
				echo "OK";
			}else
			{
				$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
				echo "ERROR";
			}
		} catch (Exception $e) {
			echo "error NuevaNotifiComun :".$e;
		}
	}

	/*! \fn: NuevaNotifiExten
	 *  \brief: alamcena las nuevas notificaciones para supervisores y controladores
	 *  \author: Edward Serrano
	 *	\date:  12/01/2017
	 *	\date modified: dia/mes/año
	 */
	function NuevaNotifiExten()
	{
		try
		{
			$datos = (object) $_REQUEST;
			$files = array();
			$Estados = array();
			$dirServ = "../../".BASE_DATOS."/filnot/";
			if($datos->nom_asunto!="" && $datos->fec_creaci!="" && $datos->usr_creaci!="" && $datos->cod_asires!="" && $datos->fec_vigenc!="" && $datos->ind_respue!="" && $datos->obs_notifi!="" && $datos->cod_tipnot!="" && $datos->num_horlab!="" && $datos->jso_notifi!="")
			{
				$sql ="INSERT INTO " . BASE_DATOS . ".tab_notifi_notifi 
	                                        (cod_tipnot,	ind_notres,		num_horlab,
	                                         nom_asunto,	fec_vigenc,		ind_respue,	
	                                         obs_notifi,	ind_estado,		usr_creaci,	
	                                         fec_creaci,	ind_enttur, 	ind_notusr)
	                                VALUES  
	                                		($datos->cod_tipnot,'".str_replace("'", "", substr($datos->cod_asires, 1))."',$datos->num_horlab,
	                                		'$datos->nom_asunto', '$datos->fec_vigenc',$datos->ind_respue, 
	                                		'$datos->obs_notifi', 1,$datos->usr_creaci, 
	                                		NOW(), $datos->ind_enttur ,'".str_replace("'", "", substr($datos->ind_notusr, 1))."')";
	            $consulta = new Consulta($sql, self::$cConexion, "BR");
	            if($consulta)
	            {
	            	#consulto el utimo registro de la tabla tab_notifi_notifi y con la fecha alamcenada
		            $sqlNotifi = 'SELECT MAX(cod_notifi) AS cod_notifi FROM ' . BASE_DATOS . '.tab_notifi_notifi';
		            $consultaNotifi = new Consulta( $sqlNotifi, self::$cConexion);
			    	$mResultNotifi = $consultaNotifi -> ret_matrix('i');
			    	$rCod_notifi=$mResultNotifi[0][0];
			    	$datos->cod_notifi=$rCod_notifi;

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
				$consultaFinal = new Consulta("COMMIT", self::$cConexion);
				self::getPlantillaEnvio($datos);
				echo "OK";
			}else
			{
				$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
				echo "ERROR";
			}
		} catch (Exception $e) {
			echo "error NuevaNotifiExten :".$e;
		}
	}

	/*! \fn: elimiNotifi
	 *  \brief: elimina las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	function elimiNotifi()
	{
		try
		{
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
	           	self::getPlantillaEnvio($datos);
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
		} catch (Exception $e) {
			echo "error elimiNotifi :".$e;
		}
	}

	/*! \fn: JsonRecor
	 *  \brief: recorrer json de las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	private function JsonRecor($json=NULL, $param=NULL)
	{
		try
		{
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
		} catch (Exception $e) {
			echo "error JsonRecor :".$e;
		}
	}

	/*! \fn: JsonRecor
	 *  \brief: recorrer json de las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  20/01/2017
	 *	\date modified: dia/mes/año
	 */
	private function getHistoNotifi($ActionForm=NULL)
	{
		try
		{
			$mSql = "SELECT a.obs_respon
						 FROM ".BASE_DATOS.".tab_notifi_respon a
						 	INNER JOIN ".BASE_DATOS.".tab_notifi_notifi b 
						 		ON a.cod_notifi=b.cod_notifi
						 			WHERE a.cod_notifi=".$ActionForm->cod_notifi." AND b.cod_tipnot=".$ActionForm->idForm;
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('a');
			return $mResult;
		} catch (Exception $e) {
			echo "error getHistoNotifi :".$e;
		}
	}

	/*! \fn: getDocument
	 *  \brief: documente asociados
	 *  \author: Edward Serrano
	 *	\date:  23/01/2017
	 *	\date modified: dia/mes/año
	 */
	function getDocument($ActionForm=NULL)
	{
		try
		{
			$mSql = "SELECT a.cod_consec,a.cod_notifi,a.nom_ficher,a.tip_ficher,a.url_ficher
						 FROM ".BASE_DATOS.".tab_notifi_ficher a
						 		WHERE a.cod_notifi=".$ActionForm->cod_notifi." ".(($ActionForm->cod_consec)?" AND a.cod_consec=".$ActionForm->cod_consec:"");
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('a');
			return $mResult;
		} catch (Exception $e) {
			echo "error getDocument :".$e;
		}
	}
	

	/*! \fn: responderNotifi
	 *  \brief: edita las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  23/01/2017
	 *	\date modified: dia/mes/año
	 */
	function responderNotifi()
	{
		try
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
		} catch (Exception $e) {
			echo "error responderNotifi :".$e;
		}
	}

	/*! \fn: getRefDocumet
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

	/*! \fn: elimiDocument
	 *  \brief: edita las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  25/01/2017
	 *	\date modified: dia/mes/año
	 */
	public function elimiDocument()
	{
		try
		{
			$datos = (object) $_REQUEST;
			$files = array();
			$Estados = array();
			$DocEliminar="";
			if($datos->cod_notifi!="" && $datos->cod_tipnot!="" && $datos->cod_consec!="")
			{
				#Busco informacion realacionada a la notificacion y documento
				$Refdocument=self::getDocument($datos);
				#Elimino el documento del servidor
				$DocEliminar = unlink($Refdocument[0]['url_ficher']);
				if($DocEliminar==TRUE)
				{
					$query = "DELETE FROM ".BASE_DATOS.".tab_notifi_ficher WHERE cod_notifi = $datos->cod_notifi AND cod_consec= $datos->cod_consec";
			        $delete = new Consulta($query,self::$cConexion,"RC");
			        if($delete)
			        {
			        	$Estados["OK"]="OK";
			        }
					else
					{
						$Estados["ERROR"]["consulta"]="Error al eliminar el archivo de la bd";	
					}
				}
				else
				{
					$Estados["ERROR"]["eliminar"]="Error al eliminar el archivo del servidor";	
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
		} catch (Exception $e) {
			echo "error elimiDocument :".$e;
		}
	}

	/*! \fn: EditNotifiComun
	 *  \brief: edita las notificaciones
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	function EditNotifiComun()
	{
		try
		{
			$datos = (object) $_REQUEST;
			$files = array();
			$Estados = array();
			$dirServ = "../../".BASE_DATOS."/filnot/";
			if($datos->nom_asunto!="" && $datos->fec_creaci!="" && $datos->usr_creaci!="" && $datos->cod_asires!="" && $datos->fec_vigenc!="" && $datos->ind_respue!="" && $datos->obs_notifi!="" && $datos->cod_tipnot!="" && $datos->cod_notifi!="" && $datos->ind_notusr!="")
			{
				$sql ="UPDATE " . BASE_DATOS . ".tab_notifi_notifi 
	                            SET
		                            nom_asunto='$datos->nom_asunto',
		                            obs_notifi='$datos->obs_notifi',
		                            fec_vigenc='$datos->fec_vigenc',
		                            ind_notres='".str_replace("'", "", substr($datos->cod_asires, 1))."',
		                            ind_notusr='".str_replace("'", "", substr($datos->ind_notusr, 1))."',
		                            ind_respue=$datos->ind_respue,	
		                            usr_modifi=$datos->usr_creaci,	
		                            fec_modifi=NOW()
	                            WHERE
	                            	cod_notifi=$datos->cod_notifi
	                            	AND cod_tipnot=$datos->cod_tipnot
	                            	AND ind_estado=1";  
	            $consulta = new Consulta($sql, self::$cConexion, "BR");
	            if($consulta)
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
			        		$sqlFile.=' (' . $datos->cod_notifi . ', "' . $nameFile . '", "' .$type[1] . '", "' . $ubicacion . '"),';
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
				$consultaFinal = new Consulta("COMMIT", self::$cConexion);
				echo "OK";
			}else
			{
				$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
				echo "ERROR";
			}
		} catch (Exception $e) {
			echo "error EditNotifiComun :".$e;
		}
	}

	/*! \fn: EditNotifiExten
	 *  \brief: edita notificaciones para supervisores y controladores
	 *  \author: Edward Serrano
	 *	\date:  19/01/2017
	 *	\date modified: dia/mes/año
	 */
	function EditNotifiExten()
	{
		try
		{
			$datos = (object) $_REQUEST;
			$files = array();
			$Estados = array();
			$dirServ = "../../".BASE_DATOS."/filnot/";
			if($datos->nom_asunto!="" && $datos->fec_creaci!="" && $datos->usr_creaci!="" && $datos->cod_asires!="" && $datos->fec_vigenc!="" && $datos->ind_respue!="" && $datos->obs_notifi!="" && $datos->cod_tipnot!="" && $datos->num_horlab!="" && $datos->jso_notifi!="" && $datos->cod_notifi!="" && $datos->ind_notusr!="")
			{
				$sql ="UPDATE " . BASE_DATOS . ".tab_notifi_notifi 
	                            SET
		                            nom_asunto='$datos->nom_asunto',
		                            obs_notifi='$datos->obs_notifi',
		                            fec_vigenc='$datos->fec_vigenc',
		                            ind_notres='".str_replace("'", "", substr($datos->cod_asires, 1))."',
		                            ind_notusr='".str_replace("'", "", substr($datos->ind_notusr, 1))."',
		                            ind_respue=$datos->ind_respue,	
		                            usr_modifi=$datos->usr_creaci,	
		                            fec_modifi=NOW()
	                            WHERE
	                            	cod_notifi=$datos->cod_notifi
	                            	AND cod_tipnot=$datos->cod_tipnot
	                            	AND ind_estado=1";  
	            $consulta = new Consulta($sql, self::$cConexion, "BR");
	            if($consulta)
	            {
	            	$sqlJson = "UPDATE " . BASE_DATOS . ".tab_notifi_detail
	            				SET
	            					jso_notifi='$datos->jso_notifi'
								WHERE
									cod_notifi=$datos->cod_notifi";
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
				        		$sqlFile.=' (' . $datos->cod_notifi . ', "' . $nameFile . '", "' .$type[1] . '", "' . $ubicacion . '"),';
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
				$consultaFinal = new Consulta("COMMIT", self::$cConexion);
				echo "OK";
			}else
			{
				$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
				echo "ERROR";
			}
		} catch (Exception $e) {
			echo "error EditNotifiExten :".$e;
		}
	}

	/*! \fn: getFormatFecha
	 *  \brief: devuelve la fecha en texto
	 *  \author: Edward Serrano
	 *	\date:  30/01/2017
	 *	\date modified: dia/mes/año
	 */
	private function getFormatFecha($fechaFormat)
	{
		try
		{
			setlocale(LC_TIME, 'es_ES.iso-8859-1');
			$mResult=strftime("%A, %d de %B de %Y" ,gmmktime(0, 0, 0, date("m", $fechaFormat), date("d", $fechaFormat), date("Y", $fechaFormat)));
	  		/*echo "mes:".date("m", $fechaFormat);
	  		echo "dia:".date("d", $fechaFormat);
	  		echo "año:".date("Y", $fechaFormat);
	  		echo $fechaFormat."----------------".$mResult."||||||||";*/
			return $mResult;
		} catch (Exception $e) {
			echo "error getFormatFecha :".$e;
		}
	}

	/*! \fn: getPlantillaEnvio
	 *  \brief: envia correo de notificacion
	 *  \author: Edward Serrano
	 *	\date:  01/02/2017
	 *	\date modified: dia/mes/año
	 */
	private function getPlantillaEnvio($datos)
	{
		try {
			$mCabece  = 'MIME-Version: 1.0' . "\r\n";
			$mCabece .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$mCabece .= 'From: AVANSAT <avansat@intrared.net>' . "\r\n";
			$plantilla = 'pla_notifi_notifi.html';
			/*variables*/
			switch ($datos->cod_tipnot) {
				case '1':
					$cod_tipnot="Notificacion OET";
					break;

				case '2':
					$cod_tipnot="Notificacion CLF";
					break;

				case '3':
					$cod_tipnot="Notificacion Supervisores";
					break;

				case '4':
					$cod_tipnot="Notificacion Controladores";
					break;

				case '5':
					$cod_tipnot="Notificacion Clientes";
					break;
				
				default:
					# code...
					break;
			}
			
			switch ($datos->ActionForm) {
				case 'idi':
					$mAccion=" editada";
					break;

				case 'eli':
					$mAccion=" eliminada";
					break;

				case 'ins':
					$mAccion=" creada";
					break;

				case 'rep':
					$mAccion=" respuesta";
					break;
				
				default:
					# code...
					break;
			}
			$mAsunto="Notificacion ".$mAccion;
			$mMessage="La notificacion ha sido".$mAccion;
			$usr_creaci=$_SESSION['datos_usuario']['cod_usuari'];
			$cod_notifi=$datos->cod_notifi;
			$nom_asunto=$datos->nom_asunto;
			$fec_creaci=$datos->fec_creaci;
			$fec_vigenc=$datos->fec_vigenc;
			$ind_respue=($datos->ind_respue=="0")?"No":"Si";
			$obs_notifi=$datos->obs_notifi;
			$mYear = date("Y");
			$mDinamic='<table width="100%" cellpadding="0" cellspacing="0">
					        <tr>
					          <td colspan="2" style="border: 1px solid #35650F; font-family:Gotham, Helvetica, Arial, sans-serif; font-size:18px; background-color: #35650F; color:#FFFFFF; padding: 4px;">Observacion de la Notificacion</td>
					        </tr>

					        <tr>
					        <td colspan="2" style="border-left:  1px solid #35650F; border-bottom: 1px solid #35650F; font-family:Gotham, Helvetica, Arial, sans-serif; font-size:16px; background-color: #FFFFFF; padding: 2px;" width="25%" align="right"><center id="content" style="margin-top: 1%;"><b>'.$mMessage.'</b></center></td>
					        </tr>
					         
					      </table>';

			$temporal = getcwd();
			if ($temporal == '/var/www/html/ap/satt_standa/notifi')
			{
        	    $tmpl_file = '../planti/' . $plantilla;
			}
        	else
        	{
        	    $tmpl_file = '../../' . DIR_APLICA_CENTRAL . '/planti/' . $plantilla;
        	}
        	$thefile = implode("", file($tmpl_file));
        	$thefile = addslashes($thefile);
        	$thefile = "\$r_file=\"" . $thefile . "\";";
        	eval($thefile);
        	$mHtmlxx = $r_file;
        	//$mailSend='supervisores@eltransporte.org';
        	$mailSend='edward.serrano@intrared.net';
        	mail($mailSend, $mAsunto, '<div name="pruebaNotifi">' . $mHtmlxx . '</div>', $mCabece);
		} catch (Exception $e) {
			echo "error getPlantillaEnvio :".$e;
		}
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
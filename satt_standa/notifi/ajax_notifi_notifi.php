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
	protected function getNotifi($cod_notifi){
		$mSql = "SELECT a.cod_notifi,a.nom_asunto,a.fec_creaci,b.cod_usuari
					 FROM ".BASE_DATOS.".tab_notifi_notifi a
					 	INNER JOIN tab_genera_usuari b 
					 		ON a.usr_creaci=b.cod_consec
					 			WHERE cod_notifi=".$cod_notifi;
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
		print_r($datos->permoetID);
		$titulos = array('Nivel1' => array('Consecutivo' => 'Consecutivo', 'Asunto' => 'Asunto', 'Fecha' => 'Fecha y hora', 'notificacion' => 'Notificado por'));
		$mHtml = new Formlib(2);
		#print_r($datos->fec_iniID);
		#echo "fecha inicial: ". $datos->fec_iniID ."; fecha fin: ". $datos->fec_finID;
		$mHtml->Table("tr",array("class"=>"displayDIV2"));
			$mHtml->Label( "DETALLE DE NOTIFICACIONES ", array("colspan"=>sizeof($titulos['Nivel1']), "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
			$mHtml->CloseRow();
				foreach ($titulos as $keyN1 => $valueN1) {
					if($keyN1=='Nivel1')
					{
						$mHtml->Row();
						foreach ($valueN1 as $keySub1 => $valueSub1) {
							$mHtml->Label( $valueSub1,  array("align"=>"right", "class"=>"CellHead") );	
						}
						$mHtml->CloseRow();
					}	
				}
				$notificaciones=self::getNotifi($datos->cod_notifi);
				if(sizeof($notificaciones))
				{
					foreach ($notificaciones as $keyNot => $valueNot) {
						$mHtml->Row();
							$mHtml->Label( $valueNot['cod_notifi'],  array("align"=>"right", "class"=>"CellInfo1") );
							$mHtml->Label( $valueNot['nom_asunto'],  array("align"=>"right", "class"=>"CellInfo1") );
							$mHtml->Label( $valueNot['fec_creaci'],  array("align"=>"right", "class"=>"CellInfo1") );
							$mHtml->Label( $valueNot['cod_usuari'],  array("align"=>"right", "class"=>"CellInfo1") );
						$mHtml->CloseRow();
					}
				}
				else
				{
					$mHtml->Row();
						$mHtml->Label( "NO SE ENCONTRARON NOTIFICACIONES",  array("align"=>"right", "class"=>"CellInfo1") );
						
					$mHtml->CloseRow();
				}
				if($datos->cod_notifi==1 && self::getmPermOet()['ins']==1)
				{
					$mHtml->Row();
						$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNoetID","name"=>"btnNoet", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(1)") );
					$mHtml->CloseRow();
				}
				if($datos->cod_notifi==2 && self::getmPermClf()['ins']==1)
				{
					$mHtml->Row();
						$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNclfID","name"=>"btnNclf", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(2)") );
					$mHtml->CloseRow();
				}
				if($datos->cod_notifi==3 && self::getmPermSup()['ins']==1)
				{
					$mHtml->Row();
						$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNsupID","name"=>"btnNsup", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(3)") );
					$mHtml->CloseRow();
				}
				if($datos->cod_notifi==4 && self::getmPermCon()['ins']==1)
				{
					$mHtml->Row();
						$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNconID","name"=>"btnNcon", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(4)") );
					$mHtml->CloseRow();
				}
				if($datos->cod_notifi==5 && self::getmPermCli()['ins']==1)
				{
					$mHtml->Row();
						$mHtml->Button( array("value"=>"NUEVA NOTIFICACION", "id"=>"btnNcliID","name"=>"btnNcli", "class"=>"crmButton small save", "align"=>"right", "colspan"=>sizeof($titulos['Nivel1']),"onclick"=>"NuevaNoti(5)") );
					$mHtml->CloseRow();
				}
		$mHtml->CloseTable('tr');
		$mHtml->OpenDiv("id:popID");
		$mHtml->CloseDiv();
		#echo "<pre>";print_r($_SESSION);echo "</pre>";
		echo $mHtml->MakeHtml();
	}

	/*! \fn: getFormNuevaNotifi
	 *  \brief: identifica el formulario correspondiete y lo pinta
	 *  \author: Edward Serrano
	 *	\date:  06/01/2017
	 *	\date modified: dia/mes/año
	 */
	protected function getFormNuevaNotifi()
	{
		$date = new DateTime();
		$mHtml = new Formlib(2);
		$mHtml->Hidden(array( "name" => "usr_creaci", "id" => "usr_creaciID", "value"=>self::getCodUsuario($_SESSION['datos_usuario']['cod_usuari'])['cod_consec']));
		$mHtml->OpenDiv("id:newNotifi");
			$mHtml->Table("tr");
				$mHtml->Label( "*Asunto:",  array("align"=>"right", "class"=>"celda_titulo") );
                $mHtml->Input(array("name" => "nom_asunto", "id" => "nom_asuntoID", "width" => "100%"));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "Fecha de Notificacion:",  array("align"=>"right", "class"=>"celda_titulo") );
                	$mHtml->Input(array("value"=>$date->format('Y-m-d H:i:s'),"name" => "fec_creaci", "id" => "fec_creaciID", "width" => "100%", "readonly"=>"readonly"));
				$mHtml->CloseRow();
				$mHtml->Row();
					$mHtml->Label( "Notificado por:",  array("align"=>"right", "class"=>"celda_titulo") );
                	$mHtml->Input(array("value"=>$_SESSION['datos_usuario']['cod_usuari'],"name" => "NotificadoPor", "id" => "NotificadoPorID", "width" => "100%", "readonly"=>"readonly"));
				$mHtml->CloseRow();
			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();
		echo $mHtml->MakeHtml();
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
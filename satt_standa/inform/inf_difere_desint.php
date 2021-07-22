<?php
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
/*! \file: inf_difere_desint.php
 *  \brief: Clase encargada de consultar los despachos de los tms y compara la cantidad que hay en GL, si hay diferencias en cantidad envia correo de notificacion
 *  \author: Ing. Nelson Liberato
 *  \author: nelson.liberato@eltransporte.org
 *  \version: 1.0
 *  \date: 2021-07-08
 *  \bug: 
 *  \warning1:  La consulta de los depachos de los TMS lo hace mediante un curl llamando un script que se puede conectar a todas las BD de los TMS 
 *				Ubicacion de script: central -> https://central.intrared.net/ap/interf/DespachosActivosTms.php
 *				Este script en central retorna un JSON con los clientes que tienen despachos en ruta y activos
 *				Este informe lee ese json y lo muestra en pantalla
 */

date_default_timezone_get('America/Bogota');

/*! \class: Despac
 *  \brief: Clase que realiza las consultas de despachos y comparar las cantidades
 */
class infDiferenciaDespachosTmsConFaroInterfaz
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
	private static $null = array( array('', '-----') );
	private static $mListaDespachosTMS = [];

	function __construct($co = null, $us = null, $ca = null)
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
	
		@include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );
  
 
		switch($_REQUEST['Option'])
		{
			case "informeGeral":
				self::getmostrarInformeGeneral();
				break;			
			case "informeDetallado":
				self::getmostrarInformeDetallado();
				break;
			default:
				self::getFiltros();
				break;
	  
	    }
	}

	/*! \fn: getFiltros
	 *  \brief: Formulario de filtros
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-08 
	 *  \param: 
	 *  \return:
	 */
	private function getFiltros()
	{
		try 
		{	
			$mEmpresas = self::getEmpresasConInterfazTms();
			$mTD = array("class"=>"cellInfo1", "width"=>"25%");
			$mAs = '<label style="color: red">* </label>';

			IncludeJS( 'jquery.js' );
			IncludeJS( 'jquery.blockUI.js' );
			IncludeJS( 'sweetalert-dev.js' );
			IncludeJS( 'functions.js' );
			IncludeJS( 'new_ajax.js' );
			IncludeJS( 'dinamic_list.js' );
			IncludeJS( 'validator.js' );
			IncludeJS( 'inf_difere_desint.js' );

			$mHtml = new Formlib(2);
			
			$mHtml->SetCss("jquery");
			$mHtml->SetCss("informes");
			$mHtml->SetCss("validator");
			$mHtml->SetCss("sweetalert");
			$mHtml->SetCss("dinamic_list");

			$mHtml->SetBody('
				<style>
					#formul_list, #formul_selected, #formul_list_edit, #formul_selected_edit {
						border: 1px solid #eee;
						width: 330px;
						min-height: 20px;
						list-style-type: none;
						margin: 0;
						padding: 5px 0 0 0;
						float: left;
						margin-right: 10px;
					}
					#formul_list li, #formul_selected li, #formul_list_edit li, #formul_selected_edit li {
						margin: 0 5px 5px 5px;
						padding: 5px;
						font-size: 1.2em;
						width: 308px;
					}
				</style>');

			$mHtml->CloseTable('tr');

			#Acordion
			$mHtml->OpenDiv("id:accFormID; class:accordion");
				$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS DE BUSQUEDA</b></h3>");
				$mHtml->OpenDiv("id:secFormID");
					$mHtml->OpenDiv("id:formID; class:Style2DIV");
						/*$mHtml->Table('tr');
							$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:left">Definir Formulario</th></tr><tr>');

							$mHtml->Label( $mAs."Empresa: ", $mTD );
							$mHtml->Select2( array_merge(self::$null, $mEmpresas), array_merge($mTD, array("name"=>"cod_tercer",  "end"=>true)) );
						$mHtml->CloseTable('tr');*/
						$mHtml->Table('tr');
							$mHtml->Button( array("value"=>"Generar informe", "class2"=>"cellInfo1", "align"=>"center", "onclick"=>"generarInforme();", "colspan"=>"6", "end"=>true) );
						$mHtml->CloseTable('tr');

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();

			$mHtml->OpenDiv("id:accInfoID; class:accordion");
				$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>INFORME ENCONTRADO</b></h3>");
				$mHtml->OpenDiv("id:secInfoID");
					$mHtml->OpenDiv("id:infoID; class:Style2DIV");
						$mHtml->Table('tr');
							$mHtml->SetBody('<th class="CellHead" colspan="4" style="text-align:center">ESPERANDO</th></tr><tr>');
						$mHtml->CloseTable('tr');
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();

 

			echo $mHtml->MakeHtml();
		
		} 
		catch (Exception $e) 
		{
			
		}
	}

	/*! \fn: getEmpresasConInterfazTms
	 *  \brief: Consulta las empresas que tienen interfaz activa cod_operad = 50
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-08 
	 *  \param: 
	 *  \return:
	 */
	private function getEmpresasConInterfazTms()
	{
		try 
		{
			$sql = "SELECT a.cod_transp, UPPER(IF(b.nom_tercer   IS NULL OR b.nom_tercer = '', b.abr_tercer, b.nom_tercer))  AS nom_tercer

					  FROM ".BASE_DATOS.".tab_interf_parame a
			    INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_transp = b.cod_tercer
					  WHERE a.cod_operad = '50'
					    AND a.ind_estado = '1'
				  ORDER BY b.nom_tercer ";
			$consult = new Consulta($sql, self::$cConexion );
			return $consult->ret_matrix('i');
		} 
		catch (Exception $e) 
		{
			
		}
	}

	/*! \fn: getmostrarInformeGeneral
	 *  \brief: Genera el informe 
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-08 
	 *  \param: 
	 *  \return:
	 */
	private function getmostrarInformeGeneral()
	{
		try 
		{
			self::$mListaDespachosTMS = self::getCurl([
											'PARAMS' => ['codTercer' => $_REQUEST['cod_tercer']  ]
										]);
			//echo "<pre>"; print_r($mDataDespac); echo "</pre>";  
			$mContadorDespachosPorEmpresa = [];
			$mDataEmpresaTMS  = [];
			$mDataDespachosDeEmpresaTms  = [];
			//echo "<pre class='cellInfo1' style='color: blue;'>NORMAL: "; print_r(self::$mListaDespachosTMS); echo "</pre>";  
			foreach (self::$mListaDespachosTMS AS $mServer => $mDataServer) 
			{
				foreach ($mDataServer['data'] AS $mIndex => $mDespacho) 
				{	
					// Empresas
					$mContadorDespachosPorEmpresa[] =   $mDespacho[ 'nom_transp' ]  ;
					$mDataEmpresaTMS[$mDespacho[ 'nom_transp' ] ]  = [ 'cod_transp' => $mDespacho['cod_transp']];
					$mDataDespachosDeEmpresaTms[$mDespacho[ 'nom_transp' ] ][]  = [ 
																			'cod_transp' => $mDespacho['cod_transp'],
																			'nom_transp' => $mDespacho['nom_transp'],
																			'num_despac' => $mDespacho['num_despac'],
																			'cod_manifi' => $mDespacho['cod_manifi'],
																			'nom_ciuori' => $mDespacho['nom_ciuori'],
																			'nom_ciudes' => $mDespacho['nom_ciudes'],
																			'num_placax' => $mDespacho['num_placax'],
																			'nom_conduc' => $mDespacho['nom_conduc'],
																			'cod_conduc' => $mDespacho['cod_conduc'],
																			'cod_ciuori' => $mDespacho['cod_ciuori'],
																			'cod_ciudes' => $mDespacho['cod_ciudes'],
																	   ];
				}
			}
			$_SESSION['DespachosTms'] = $mDataDespachosDeEmpresaTms;
			$mContadorDespachosPorEmpresa =  array_count_values( $mContadorDespachosPorEmpresa ) ;
			ksort($mContadorDespachosPorEmpresa, SORT_NATURAL | SORT_FLAG_CASE );
			//echo "<pre class='cellInfo1' style='color: blue;'>ajustado"; print_r( $mContadorDespachosPorEmpresa ); echo "</pre>";  
	 
			$mHtml  = "<table class='cellInfo1' width='100%' sty>";
				$mHtml .= "<tr>";
					$mHtml .= "<th>No</th>";
					$mHtml .= "<th>Nombre Transportadora</th>";
					$mHtml .= "<th>Despachos TMS</th>";
					$mHtml .= "<th>Despachos GL</th>";
					$mHtml .= "<th>Diferencia</th>";
				$mHtml .= "</tr>";
				$mRow = 1;
				foreach ($mContadorDespachosPorEmpresa AS $mEmpresa => $mCantidadDespachosTms) 
				{
						$mCantidadDespachosGL = self::getcantidadDespachosEnRutaGL( $mDataEmpresaTMS[$mEmpresa]  );
			//echo "<pre class='cellInfo1' style='color: blue;'>ajustado".$mEmpresa; print_r( $mDataEmpresaTMS ); echo "</pre>";  
					 
						$mHtml .= "<tr>";
							$mHtml .= "<td>".($mRow ++)."</td>";
							$mHtml .= "<td>".utf8_decode($mEmpresa) ."</td>";
							$mHtml .= "<td onclick=\"verDespachos( '".utf8_decode($mEmpresa)."' )\" >".$mCantidadDespachosTms."</td>";
							$mHtml .= "<td>".$mCantidadDespachosGL."</td>";
							$mHtml .= "<td>".($mCantidadDespachosTms - $mCantidadDespachosGL)."</td>";
						$mHtml .= "</tr>";
					 
				}
			echo $mHtml .= "</table>";

			// busca las diferencias en despacho tms y no GL y manda correo con esos faltantes para que el cleinte los reenvie
			self::setEnviarNotificacionDiferenciaDeDespachos($mDataDespachosDeEmpresaTms);
		} 
		catch (Exception $e) 
		{
			
		}
	}

	 /*! \fn: getmostrarInformeDetallado
	 *  \brief: muestra los datos detallados osea los despachos de una empresa de TMS 
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-08 
	 *  \param: 
	 *  \return:
	 */
	private function getmostrarInformeDetallado( )
	{
		//echo "<pre class='cellInfo1' style='color: blue;'>ajustado"; print_r($_REQUEST); echo "</pre>";  
		//echo "<pre class='cellInfo1' style='color: blue;'>ajustado"; print_r($_SESSION['DespachosTms']); echo "</pre>";  
		$mHtml  = "<table class='cellInfo1' width='100%' sty>";
		$mHtml .= "<tr>";
			$mHtml .= "<th>No</th>";
			$mHtml .= "<th>Nro Despacho</th>";
			$mHtml .= "<th>Nro Manifiesto</th>";
			$mHtml .= "<th>Origen</th>";
			$mHtml .= "<th>Destino</th>";
			$mHtml .= "<th>Placa</th>";
			$mHtml .= "<th>Conductor</th>";
		$mHtml .= "</tr>";
		$mCont = 1;
		foreach ($_SESSION['DespachosTms'][$_REQUEST['nom_tercer']] AS $mIndex => $mDespacho) 
		{
			$mDiff = self::getcantidadDespachosEnRutaGL($mDespacho);
			$mRow = ((int)$mDiff == 0 ? "#FF7342" : "#E0FEF0 ");
			$mHtml .= "<tr style='background-color:$mRow'>";
				$mHtml .= "<td>".($mCont ++)."</td>";
				$mHtml .= "<td>".$mDespacho['num_despac']."</td>";
				$mHtml .= "<td>".$mDespacho['cod_manifi']."</td>";
				$mHtml .= "<td>".$mDespacho['nom_ciuori']."</td>";
				$mHtml .= "<td>".$mDespacho['nom_ciudes']."</td>";
				$mHtml .= "<td>".$mDespacho['num_placax']."</td>";
				$mHtml .= "<td>".$mDespacho['nom_conduc']."</td>";
			$mHtml .= "</tr>";
		}
	echo $mHtml .= "</table>";
	}

 
	/*! \fn: getCurl
	 *  \brief: Genera el informe 
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-08 
	 *  \param: 
	 *  \return:
	 */
	private function getCurl($mData = NULL)
	{
		try 
		{
			$s = curl_init();
			curl_setopt($s,CURLOPT_URL, URL_INTERF_CENTRAL.'/ap/interf/DespachosActivosTms.php');
			curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
			curl_setopt($s,CURLOPT_TIMEOUT,30); 
			curl_setopt($s,CURLOPT_RETURNTRANSFER,true);  
			curl_setopt($s,CURLOPT_POST,true);
			curl_setopt($s,CURLOPT_POSTFIELDS, http_build_query($mData['PARAMS']));
			$mResponse = curl_exec($s);
			curl_close($s);
			//echo "<pre class='vainaRara' style='color: blue;'>MIerda: "; print_r( $mResponse ); echo "</pre>";  

			return json_decode($mResponse, true);
		} 
		catch (Exception $e) 
		{
			
		}
	}	

	/*! \fn: getcantidadDespachosEnRutaGL
	 *  \brief: Genera el informe 
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-09 
	 *  \param: 
	 *  \return:
	 */
	private function getcantidadDespachosEnRutaGL($mData = NULL)
	{
		try 
		{
			$sql = "
			SELECT SUM(a.can_despac) AS can_despac
			  FROM 
				( 
					SELECT COUNT(b.num_despac) AS can_despac 
				 					  FROM ".BASE_DATOS.".tab_despac_vehige a
				 			    INNER JOIN ".BASE_DATOS.".tab_despac_despac b ON a.num_despac = b.num_despac
				 					  WHERE 1 = 1
				 					  	AND a.cod_transp = '".$mData['cod_transp']."'
				 					  	".($mData['cod_conduc'] != '' ? " AND a.cod_conduc = '".$mData['cod_conduc']."' " : '')." 
				 					  	AND a.ind_activo = 'S'
				 					  	".($mData['cod_manifi'] != '' ? " AND b.cod_manifi = '".$mData['cod_manifi']."' " : '')."   
				 					  	".($mData['cod_ciuori'] != '' ? " AND b.cod_ciuori = '".$mData['cod_ciuori']."' " : '')."   
				 					  	".($mData['cod_ciudes'] != '' ? " AND b.cod_ciudes = '".$mData['cod_ciudes']."' " : '')."   
				 					  	AND b.ind_planru = 'S'
				 					  	AND b.ind_anulad = 'R'
				 					  	AND b.fec_salida IS NOT NULL
				 					  	AND b.fec_llegad IS NULL
				 				   GROUP BY b.num_despac
				) a ";
				//echo "<pre class='cellInfo1' style='color: blue;'>ajustado"; print_r($sql); echo "</pre>"; 
			$consult = new Consulta($sql, self::$cConexion );
			$mCantidad = $consult->ret_matrix('a');
			return $mCantidad[0]['can_despac'];
		} 
		catch (Exception $e) 
		{
			
		}
	}	
	 /*! \fn: setEnviarNotificacionDiferenciaDeDespachos
	 *  \brief: Envia correo con empresas encontradas con diferencia de despachos
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-09 
	 *  \param: 
	 *  \return:
	 */
	private function setEnviarNotificacionDiferenciaDeDespachos($mData = NULL)
	{
		try 
		{
			$mHtml  = "<table class='cellInfo1' width='100%' style='  background-color:#FFFFFF'>";
				$mHtml .= "<tr style='font-size: 20px; background-color:#E3E3E3'>";
					$mHtml .= "<td valign='middle'  style='font-size: 20px'>
									<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>DIFERENCIA DE <span style='color: #F5AD08'>DESPACHOS</span></b></span>
									 
							  </td>";					
					$mHtml .= "<th>
									<span><img src='https://lirp.cdn-website.com/0192771f/dms3rep/multi/opt/logo-clfaro-1920w.png' /></span>
							  </th>";
				$mHtml .= "</tr>";				

				$mHtml .= "<tr>";
					$mHtml .= "<td colspan='2'  style='font-size: 15px'>
								<br>
								".utf8_decode('Señores: Centro Logistico Faro SAS.')."
								<br><br><br>
								
								".utf8_decode('Se Informa que el sistema Avansat GL ha encontrado una diferencia de despachos vs la aplicacion usada  por el cliente
								a continuación se muestra le detalle de la información')." <br><br><br>
					           </td>";
				$mHtml .= "</tr>";					

				$mHtml .= "<tr>";
					$mHtml .= "<td colspan='2'>";
						$mHtml .= "<table width='100%'>";
							$mHtml .= "<tr  style='background-color: #C2C2C2;' >";
								$mHtml .= "<th>Nro</th>";
								$mHtml .= "<th>Empresa de transporte</th>";
								$mHtml .= "<th>Nro de Despacho TMS</th>";
								$mHtml .= "<th>Nro Manifiesto</th>";
								$mHtml .= "<th>Origen</th>";
								$mHtml .= "<th>Destino</th>";
								$mHtml .= "<th>Placa</th>";
								$mHtml .= "<th>Conductor</th>";
							$mHtml .= "</tr>";	
										
								$mCont = 1;
								foreach ($mData  AS $mEmpresas => $mDespachoEmpresa) 
								{ 
									foreach ($mDespachoEmpresa  AS  $mDespacho) 
									{
										$mDiff = (int)self::getcantidadDespachosEnRutaGL($mDespacho);
										if($mDiff == 0)
										{
											$mHtml .= "<tr>";
												$mHtml .= "<td style='border-bottom: 1px solid #000; border-left: 1px solid #000;'>".($mCont++)."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000;'>".utf8_decode($mEmpresas)."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000;'>".$mDespacho['num_despac']."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000;'>".$mDespacho['cod_manifi']."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000;'>".$mDespacho['nom_ciuori']."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000;'>".$mDespacho['nom_ciudes']."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000;'>".$mDespacho['num_placax']."</td>";
												$mHtml .= "<td style='border-bottom: 1px solid #000; border-right: 1px solid #000;'>".strip_tags($mDespacho['nom_conduc'])."</td>";
											$mHtml .= "</tr>";	
										}
									}
								}
						$mHtml .= "</table>";
					$mHtml .= "</td>";
				$mHtml .= "</tr>";
				$mHtml .= "<tr>";
				$mHtml .= "<td colspan='2'> 
				 					<b>".utf8_decode('Importante:</b> Realizarle la notificación al cliente para que realice nuevamente el reenvio del despacho  a la aplicación
				 					Avansat GL')."
						  </td>";
				$mHtml .= "</tr>";
				$mHtml .= "<tr>";
					$mHtml .= "<td colspan='2'><img src='../satt_standa/estilos/Verde/imagenes/backg_menu_top.jpg' /></td>";
				$mHtml .= "</tr>";				
				$mHtml .= "<tr>";
					$mHtml .= "<td colspan='2' align='center'>Copyrigth ".date('Y').". Todos los derechos reservados. Diseñado y desarrollado por GRUPO OET  S.A.S </td>";
				$mHtml .= "</tr>";

			    $mHtml .= "</table>";


			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	 
			mail(MAIL_SUPERVISORES_ELTRANSPORTE, 'NOTIFICACION DE DIFERENCIA DE DESPACHO DEL CLIENTE TMS VS AVANSAT GL', $mHtml, $cabeceras);
		} 
		catch (Exception $e) 
		{
			
		}
	}
}

 
if($_REQUEST['Ajax'] === 'on' )
	new infDiferenciaDespachosTmsConFaroInterfaz();
else
	new infDiferenciaDespachosTmsConFaroInterfaz( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );


?>
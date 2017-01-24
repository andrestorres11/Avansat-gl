<?php
/*! \file: ins_notifi_notifi.php
 *  \brief: Insertar, Editar, Responder, Eliminar Notificaciones
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 27/12/2016
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*! \class: notifi
 *  \brief: Lista notificaciones
 */
class notifi
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$mANotifi;
					
	function __construct($co = null, $us = null, $ca = null)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/constantes.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
		@include_once( '../' . DIR_APLICA_CENTRAL . '/notifi/ajax_notifi_notifi.php' );
		self::$mANotifi = new AjaxNotifiNotifi();
		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;

		IncludeJS( 'jquery17.js' );
		IncludeJS( 'jquery.js' );
			
		IncludeJS( 'functions.js' );
		IncludeJS( 'ins_notifi_notifi.js' );
		IncludeJS( 'dinamic_list.js' );
		IncludeJS( 'new_ajax.js' );
		IncludeJS( 'sweetalert-dev.js' );

		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/sweetalert.css' type='text/css'>\n";

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";
		
		
		
		if($_REQUEST['cod_consec'])
		{
			self::verDocumentos();
		}
		else
		{
			
			self::lista();
		}
		
	
		
	}

	/*! \fn: lista
	 *  \brief: Vista principal de modulo
	 *  \author: Edward serrano
	 *	\date: 	
	 *	\date modified: dia/mes/a�o
	 */
	private function lista()
	{
		echo self::GridStyle();
		$responseJson=self::getRespond();
		$Json=json_decode($responseJson);

		#captura de fechas
		$fActual=date('Y-m-j');
		$fFin=strtotime ( '0 day' , strtotime ( $fActual ) ) ;
		$fFin = date ( 'Y-m-j' , $fFin );
		$nuevafecha = strtotime ( '-8 day' , strtotime ( $fActual ) ) ;
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
		#HTML
		$mHtml = new Formlib(2);
		$mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
		$mHtml->CloseTable('tr');
		$mHtml->Table("tr");
			$mHtml->SetBody("<td>");

				$mHtml->OpenDiv("id:contentID; class:contentAccordion");
					$mHtml->OpenDiv("id:notifiID; class:accordion");
						$mHtml->SetBody("<h3 style='padding:6px;'><center>Notificaciones</center></h3>");
						$mHtml->OpenDiv("id:secID");
							$mHtml->OpenDiv("id:form_notifiID; class:contentAccordionForm");
								$mHtml->Table("tr");
									$mHtml->Label( "Datos Basicos del Responsable".$val, array("colspan"=>"4", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
									$mHtml->CloseRow();

									$mHtml->Row();
									$mHtml->Label( "* Fecha inicio:", array("align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
									$mHtml->Input( array("name"=>"fec_ini", "id"=>"fec_iniID", "width"=>"25%", "value"=>$nuevafecha , "class"=>"cellInfo2") );
									$mHtml->Label( "* Fecha fin:", array("align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
									$mHtml->Input( array("name"=>"fec_fin", "id"=>"fec_finID", "width"=>"25%", "value"=>$fFin , "class"=>"cellInfo2") );
								$mHtml->CloseTable('tr');
							$mHtml->CloseDiv();
							$mHtml->OpenDiv("id:tabs");
							#tabs
								$srtTbs="<ul>";
								foreach ($Json as $nivel1 => $value1) 
								{
									#echo "<pre>";print_r($value1->jso_notifi);echo "</prev>";
									if($nivel1=='jso_notifi')
									{
										$Json2=json_decode($value1->jso_notifi);				
										#echo "<pre>";print_r($Json2);echo "</prev>";
										foreach ($Json2 as $nivel2 => $value2) 
										{
											if($nivel2=='fil_genera')
											{
												$tabgeneral=self::recorrerJson($value2, 'ind_visibl', $arrayName = array());
												if($tabgeneral['ind_visibl']==TRUE)
												{
													$srtTbs.="<li><a href='#tabgeneral' onclick='btnGeneral()'>GENERAL</a></li>";
												}
											}
											if($nivel2=='sec_infoet')
											{
												$tabinfoet=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("oet","ins","idi","rep","eli"));
												if($tabinfoet['ind_visibl']==TRUE)
												{
													$srtTbs.="<li><a href='#tabinfoet' onclick='btnSubModulos(1)'>INFORMACION OET</a></li>";
												}
												self::$mANotifi->setmPermOet($tabinfoet);
												#print_r( self::$mANotifi->getmPermOet());
											}
											if($nivel2=='sec_infclf')
											{
												$tabinfclf=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("clf","ins","idi","rep","eli" ));
												if($tabinfclf['ind_visibl']==TRUE)
												{
													$srtTbs.="<li><a href='#tabinfclf' onclick='btnSubModulos(2)'>INFORMACION CLF</a></li>";
												}
												self::$mANotifi->setmPermClf($tabinfclf);
											}
											if($nivel2=='sec_infsup')
											{												
												$tabinfsup=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("sup","ins","idi","rep","eli" ));
												if($tabinfsup['ind_visibl']==TRUE)
												{
													$srtTbs.="<li><a href='#tabinfsup' onclick='btnSubModulos(3)'>SUPERVISORES</a></li>";
												}
												self::$mANotifi->setmPermSup($tabinfsup);
											}
											if($nivel2=='sec_infcon')
											{												
												$tabinfcon=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("con","ins","idi","rep","eli" ));
												if($tabinfcon['ind_visibl']==TRUE)
												{
													$srtTbs.="<li><a href='#tabinfcon' onclick='btnSubModulos(4)'>CONTROLADORES</a></li>";
												}
												self::$mANotifi->setmPermCon($tabinfcon);
											}
											if($nivel2=='sec_infcli')
											{
												$tabinfcli=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("cli","ins","idi","rep","eli" ));
												if($tabinfcli['ind_visibl']==TRUE)
												{
													$srtTbs.="<li><a href='#tabinfcli' onclick='btnSubModulos(5)'>CLIENTES</a></li>";
												}
												self::$mANotifi->setmPermCli($tabinfcli);
											}
										}	
									}
								}
								$srtTbs.="</ul>";
								$mHtml->SetBody($srtTbs);
								if($tabgeneral['ind_visibl']==TRUE)
								{
									$mHtml->OpenDiv("id:tabgeneral");
										
							        $mHtml->CloseDiv();
								}
								if($tabinfoet['ind_visibl']==TRUE)
								{
									$mHtml->OpenDiv("id:tabinfoet");
									
                            		$mHtml->CloseDiv();
								}
								if($tabinfclf['ind_visibl']==TRUE)
								{
									$mHtml->OpenDiv("id:tabinfclf");
									
                            		$mHtml->CloseDiv();
								}
								if($tabinfsup['ind_visibl']==TRUE)
								{
									$mHtml->OpenDiv("id:tabinfsup");
									
                            		$mHtml->CloseDiv();
								}
								if($tabinfcon['ind_visibl']==TRUE)
								{
									$mHtml->OpenDiv("id:tabinfcon");
									
                            		$mHtml->CloseDiv();
								}
								if($tabinfcli['ind_visibl']==TRUE)
								{
									$mHtml->OpenDiv("id:tabinfcli");
									
                            		$mHtml->CloseDiv();
								}
								#print_r($tabinfoet);
							#cierra tabs
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();

			$mHtml->SetBody('</td>');
		$mHtml->CloseTable('tr');

		$mHtml->SetBody('<script>
                      $(function() {
                        $("#tabs").tabs();
                      } );
                    </script>');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getRespond
	 *  \brief: retorna los permisos sobre los submodulos
	 *  \author: Edward Serrano
	 *	\date: 
	 *	\date modified: dia/mes/a�o
	 *  \return:
	 */
	protected function getRespond()
	{
		$mSql = "SELECT a.jso_notifi 
				   FROM ".BASE_DATOS.".tab_genera_respon a 
				   		INNER JOIN 
				   		".BASE_DATOS.".tab_genera_perfil b 
				   		ON a.cod_respon=b.cod_respon
				   		WHERE b.cod_perfil=".$_SESSION['datos_usuario']['cod_perfil']." ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix();

		return json_encode($mData);	
	}

	/*! \fn: recorrerJson
	 *  \brief: Reccorre json con los permisos asignados
	 *  \author: Edward Serrano
	 *	\date: 
	 *	\date modified: dia/mes/a�o
	 *  \param: $JsonRe-> json de datos
	 *  \param: $Panel-> subnivel del json
	 *  \param: arrSub-> Paramentros de bsuqueda
	 *  \return:
	 */
	protected function recorrerJson($JsonRe = NULL, $Panel = NULL, $arrSub = NULL)
	{
		#print_r($JsonRe);// echo "; Panel:".$Panel."; array:".$arrSub;
		$resp=NULL; //= array('ins' =>0 ,'idi'=>0, 'rep'=>0, 'eli'=>0  );
		foreach ($JsonRe as $nivel3A => $value3A) 
		{
			if($nivel3A==$Panel)
			{
				$resp[$nivel3A]=TRUE;
				#echo "existe ind_visibl: ";print_r($value3A);
			}
			if($nivel3A=='sub')
			{
				#echo "existe subnivel ";
				
				foreach ($value3A as $nivel3B => $value3B) 
				{
					foreach ($arrSub as $kComparacion => $Vcompa) {
						if($nivel3B==$arrSub[0]."_".$Vcompa)
						{ 
							$resp[$Vcompa]=$value3B;
							#echo $Vcompa." ".$value3B." ;";
						}
					}
				}
			}
		}
		return $resp;
	}

	function GridStyle()
    {
        echo "<style>
                .cellth-ltb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .cellth-lb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                }
                .cellth-b{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                }
                .cellth-tb{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-ltb{
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-tb{
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-lb{
                     border-bottom: 1px solid #999999; 
                     border-left: 1px solid #999999;
                }
                .celltd-l{
                     border-left: 1px solid #999999;
                }
                .fontbold{
                    font-weight: bold;
                }
                .divGrilla{
                    margin: 0;
                    padding: 0;
                    border: none;
                    border-top: 1px solid #999999;
                    border-bottom: 1px solid #999999;
                }

                .CellHead {
                    background-color: #35650f;
                    color: #ffffff;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 4px;
                }
                .cellInfo1 {
                    background-color: #ebf8e2;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 2px;
                    height: 10px;
                }
                /*.campo_texto {
                    background-color: #ffffff;
                    border: 1px solid #bababa;
                    color: #000000;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding-left: 5px;
                }*/
                .crmButton {
                	width:25%;
                	height: 20px;
                }
                #obs_notifiID {
                	height: 100px;
    				width: 100%;
            	}
            	#obs_responID {
                	height: 100px;
    				width: 100%;
            	}
            	.error{
				    background-color: #45930b;
				    border-radius: 4px 4px 4px 4px;
				    color: white;
				    font-weight: bold;
				    margin-left: 6px;
				    margin-top: 3px;
				    padding: 3px 6px;
				    position: absolute;
				}
            	.error:before{
				    border-color: transparent #45930b transparent transparent;
				    border-style: solid;
				    border-width: 3px 4px;
				    content: '';
				    display: block;
				    height: 0;
				    left: -16px;
				    position: absolute;
				    top: 4px;
				    width: 0;
				}
				.campo_texto{
					border: 1px solid #DBE1EB;
					font-size: 10px;
					font-family: Arial, Verdana;
					padding-left: 5px;
					padding-right: 5px;
					padding-top: 5px;
					padding-bottom: 5px;
					border-radius: 4px;
					-moz-border-radius: 4px;
					-webkit-border-radius: 4px;
					-o-border-radius: 4px;
					background: #FFFFFF;
					background: linear-gradient(left, #FFFFFF, #F7F9FA);
					background: -moz-linear-gradient(left, #FFFFFF, #F7F9FA);
					background: -webkit-linear-gradient(left, #FFFFFF, #F7F9FA);
					background: -o-linear-gradient(left, #FFFFFF, #F7F9FA);
					color: #2E3133;
				}
				.CellInfohref{
					cursor:pointer;
					background-color: #ebf8e2;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 2px;
                    height: 10px;
				}
              </style>";
    }
    function verDocumentos()
    {
    	$datos = (object) $_REQUEST;
		print_r($datos);
		$Refdocument=self::getDocument($datos);
		print_r($Refdocument);
		//echo "header('Content-Disposition: attachment; filename='".$Refdocument[0]['nom_ficher']."');";
		switch ($Refdocument[0]['tip_ficher']) {
			case 'pdf' :
				/*header('Content-Disposition: attachment; filename="'.$Refdocument[0]['nom_ficher'].'"');
				header ("Content-Type: application/octet-stream");
				readfile(substr($Refdocument[0]['url_ficher'], 3));*/
				/*echo "<object width='900' height='800' type='application/pdf' data='".substr($Refdocument[0]['url_ficher'], 3)."'>
						<param name='src' value='".substr($Refdocument[0]['url_ficher'], 3)."' />
						<p>N o PDF available</p>
					 </object>";*/
				echo "<embed src='".substr($Refdocument[0]['url_ficher'], 3)."' width='".$datos->width."' height='400'>";
			break;
			case 'jpg' : case 'jpeg': case 'bmp' : case 'tiff' : case 'png' :

				//echo "<iframe src></iframe>";
				//echo "<embed src='".substr($Refdocument[0]['url_ficher'], 3)."' width='".$datos->width."' height='400'>";
			break;

			case 'doc' : case 'docx' : case 'xls' : case 'xlsx' : case 'cvs' : case 'zip' : case 'rar' :
				header('Content-Disposition: attachment; filename="'.$Refdocument[0]['nom_ficher'].'"');
				header('Content-Type: application/msword');
				//ob_flush();
				//ob_clean(); 
				flush();
				echo file_get_contents(substr($Refdocument[0]['url_ficher'], 3));
				//readfile(substr($Refdocument[0]['url_ficher'], 3));
				//echo "<a href='".substr($Refdocument[0]['url_ficher'], 3)."'>Download Here</a>";
				/*header('Content-Disposition: attachment; filename="'.$Refdocument[0]['nom_ficher'].'"');
				//header('Content-type: application/pdf');
				header('Content-Transfer-Encoding: binary');
				header ("Content-Type: application/octet-stream");
				header('Accept-Ranges: bytes');
				readfile(substr($Refdocument[0]['url_ficher'], 3));*/
			break;
			
			default:
				# code...
				break;
		}
		
		//$datos = file_get_contents(substr($Refdocument[0]['url_ficher'], 3));
		//file_put_contents($Refdocument[0]['nom_ficher'], $datos);

		//header("Content-type: MIME");
		//readfile(substr($Refdocument[0]['url_ficher'], 3));
    }

    /*! \fn: getDocument
	 *  \brief: documente asociados
	 *  \author: Edward Serrano
	 *	\date:  23/01/2017
	 *	\date modified: dia/mes/a�o
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
}

$_NOTIFI = new notifi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
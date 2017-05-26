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

//ini_set('display_errors', true);
//error_reporting(E_ALL & ~E_NOTICE);

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
		try 
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
		} catch (Exception $e) {
			echo "error __construct :".$e;
		}
	}

	/*! \fn: lista
	 *  \brief: Vista principal de modulo
	 *  \author: Edward serrano
	 *	\date: 	
	 *	\date modified: dia/mes/año
	 */
	private function lista()
	{
		try 
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
										if($nivel1=='jso_notifi')
										{
											$Json2=json_decode($value1->jso_notifi);				
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
														$srtTbs.="<li><a href='#tabResult' onclick='btnSubModulos(1)'>INFORMACION OET</a></li>";
													}
													self::$mANotifi->setmPermOet($tabinfoet);
												}
												if($nivel2=='sec_infclf')
												{
													$tabinfclf=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("clf","ins","idi","rep","eli" ));
													if($tabinfclf['ind_visibl']==TRUE)
													{
														$srtTbs.="<li><a href='#tabResult' onclick='btnSubModulos(2)'>INFORMACION CLF</a></li>";
													}
													self::$mANotifi->setmPermClf($tabinfclf);
												}
												if($nivel2=='sec_infsup')
												{												
													$tabinfsup=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("sup","ins","idi","rep","eli" ));
													if($tabinfsup['ind_visibl']==TRUE)
													{
														$srtTbs.="<li><a href='#tabResult' onclick='btnSubModulos(3)'>SUPERVISORES</a></li>";
													}
													self::$mANotifi->setmPermSup($tabinfsup);
												}
												if($nivel2=='sec_infcon')
												{												
													$tabinfcon=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("con","ins","idi","rep","eli" ));
													if($tabinfcon['ind_visibl']==TRUE)
													{
														$srtTbs.="<li><a href='#tabResult' onclick='btnSubModulos(4)'>CONTROLADORES</a></li>";
													}
													self::$mANotifi->setmPermCon($tabinfcon);
												}
												if($nivel2=='sec_infcli')
												{
													$tabinfcli=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("cli","ins","idi","rep","eli" ));
													if($tabinfcli['ind_visibl']==TRUE)
													{
														$srtTbs.="<li><a href='#tabResult' onclick='btnSubModulos(5)'>CLIENTES</a></li>";
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
									$mHtml->OpenDiv("id:tabResult");
	                            	$mHtml->CloseDiv();
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
		} catch (Exception $e) {
			echo "error lista :".$e;	
		}
	}

	/*! \fn: getRespond
	 *  \brief: retorna los permisos sobre los submodulos
	 *  \author: Edward Serrano
	 *	\date: 
	 *	\date modified: dia/mes/año
	 *  \return:
	 */
	protected function getRespond()
	{
		try 
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
		} catch (Exception $e) {
			echo "error getRespond :".$e;
		}
	}

	/*! \fn: recorrerJson
	 *  \brief: Reccorre json con los permisos asignados
	 *  \author: Edward Serrano
	 *	\date: 
	 *	\date modified: dia/mes/año
	 *  \param: $JsonRe-> json de datos
	 *  \param: $Panel-> subnivel del json
	 *  \param: arrSub-> Paramentros de bsuqueda
	 *  \return:
	 */
	protected function recorrerJson($JsonRe = NULL, $Panel = NULL, $arrSub = NULL)
	{
		try 
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
		} catch (Exception $e) {
			echo "error recorrerJson :".$e;
		}
	}

	/*! \fn: GridStyle
	 *  \brief: estilos adicionales para el framework
	 *  \author: Edward Serrano
	 *	\date: 
	 *	\date modified: dia/mes/año
	 *  \return:
	 */
	function GridStyle()
    {
    	try {
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
	                .celda_titulo {
	                    border-bottom: 1px solid #35650F;
						background-color: #EBF8E2;
						background-image: url('');
						color: #333333;
						font-weight: bold;
						width: 25%;
						padding: 3px 10px;
						/*white-space: nowrap;*/
	                }
	                .cellInfo1 {
	                    background-color: #ebf8e2;
	                    font-family: Times New Roman;
	                    font-size: 11px;
	                    padding: 2px;
	                    height: 10px;
	                }
	                .celda_info{
	                	background-color: #EBF8E2;
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
	            	#nom_asuntoID {
	                	height: 25px;
	    				width: 80%;
	            	}
	            	#nom_asuntoNID
	            	{
	            		height: 25px;
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
					#nom_asuntoID{
						text-transform: uppercase;
					}
					label, input[type=text], textarea{
						text-transform: uppercase;
					}
					
	              </style>";
	    } catch (Exception $e) {
			echo "error GridStyle :".$e;
		}
    }

    /*! \fn: verDocumentos
	 *  \brief: descarga de archivos adjuntos a la notificacion
	 *  \author: Edward Serrano
	 *	\date:  23/01/2017
	 *	\date modified: dia/mes/año
	 */
    function verDocumentos()
    {
    	try
    	{
	    	$datos = (object) $_REQUEST;
			$Refdocument=self::getDocument($datos);
			$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
			$detected_type = finfo_file( $fileInfo, substr($Refdocument[0]['url_ficher'], 3) );
			switch ($Refdocument[0]['tip_ficher']) {				
				case 'jpg' : case 'jpeg': case 'bmp' : case 'tiff' : case 'png' : case 'pdf' : case 'zip' : case 'rar' :  
					header("MIME-Version: 1.0");
				    header( "Content-type: '".$detected_type."'" );
				    header( "Content-transfer-encoding: 8bit");
				    header( "Content-disposition: inline; filename=".str_replace("+","_",urlencode($Refdocument[0]['nom_ficher'])) );
				    ob_end_clean();
				    $handle = fopen(substr($Refdocument[0]['url_ficher'], 3), 'rb');
				    while ( !feof($handle) ) {
				        print fread($handle, 8192);
				    }
				    fclose($handle);
				    //@readfile(substr($Refdocument[0]['url_ficher'], 3) );
				break;
				
				default:
					$zip = new ZipArchive();
 					$nameFileZip=explode(".", $Refdocument[0]['nom_ficher']);
					$filename = '../'.BASE_DATOS.'/filnot/'.$nameFileZip[0].'.zip';
					 
					if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
					    $zip->addFile(substr($Refdocument[0]['url_ficher'], 3),$Refdocument[0]['nom_ficher']);
					    $zip->close();
					    if(file_exists($filename))
					    {
					    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						    header("Content-type: application/zip");
						    header("Content-Transfer-Encoding: binary");
							header("Content-disposition: attachment; filename=".str_replace(" ","_",$nameFileZip[0]).".zip");
							ob_end_clean();
							readfile($filename);
							ob_end_flush();
							unlink($filename);	
					    }
					    else
					    {
					    	echo " el archivo no existe";
					    }
					}
					else 
					{
					    echo 'Error creando '.$filename;
					}
				break;
			}
		}catch (Exception $e) {
			echo "error verDocumentos ".$e;
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
		try {
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
}

$_NOTIFI = new notifi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
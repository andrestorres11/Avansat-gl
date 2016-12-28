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

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);


/*! \class: notifi
 *  \brief: Lista notificaciones
 */
class notifi
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario;
					
	function __construct($co, $us, $ca)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		
		IncludeJS( 'jquery.js' );
		IncludeJS( 'functions.js' );
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
		self::lista();
	}

	private function lista()
	{
		$responseJson=self::getRespond();
		$Json=json_decode($responseJson);
		
		#HTML
		$mHtml = new Formlib(2);

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
									$mHtml->Input( array("name"=>"nom_respon", "id"=>"nom_responID", "width"=>"25%", "value"=>'', "class"=>"cellInfo2") );
									$mHtml->Label( "* Fecha fin:", array("align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
									$mHtml->Input( array("name"=>"nom_respon", "id"=>"nom_responID", "width"=>"25%", "value"=>'', "class"=>"cellInfo2") );
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
												$srtTbs.="<li><a href='#tabgeneral'>GENERAL</a></li>";
												$tabgeneral=self::recorrerJson($value2, 'ind_visibl', $arrayName = array());
											}
											if($nivel2=='sec_infoet')
											{
												$srtTbs.="<li><a href='#tabinfoet'>INFORMACION OET</a></li>";
												$tabinfoet=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("oet","ins","idi","rep","eli"));
											}
											if($nivel2=='sec_infclf')
											{
												$srtTbs.="<li><a href='#tabinfclf'>INFORMACION CLF</a></li>";
												$tabinfclf=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("clf","ins","idi","rep","eli" ));
											}
											if($nivel2=='sec_infsup')
											{
												$srtTbs.="<li><a href='#tabinfsup'>SUPERVISORES</a></li>";
												$tabinfsup=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("sup","ins","idi","rep","eli" ));
											}
											if($nivel2=='sec_infcon')
											{
												$srtTbs.="<li><a href='#tabinfcon'>CONTROLADORES</a></li>";
												$tabinfcon=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("con","ins","idi","rep","eli" ));
											}
											if($nivel2=='sec_infcli')
											{
												$srtTbs.="<li><a href='#tabinfcli'>CLIENTES</a></li>";
												$tabinfcli=self::recorrerJson($value2, 'ind_visibl', $arrayName = array("cli","ins","idi","rep","eli" ));
											}
										}	
									}
								}
								$srtTbs.="</ul>";
								$mHtml->SetBody($srtTbs);
								print_r("dato: ".$tabgeneral);
								if($tabgeneral)
								{
									$mHtml->OpenDiv("id:tabgeneral");
										$mHtml->SetBody("<h3>general</h3>");
                            		$mHtml->CloseDiv();
								}
								if($tabinfoet)
								{
									$mHtml->OpenDiv("id:tabinfoet");
									foreach ($tabinfoet as $Kinfoet => $Vinfoet) 
									{
										$mHtml->SetBody("<h3>".$Kinfoet."</h3>");
									}	
                            		$mHtml->CloseDiv();
								}
								if($tabinfclf)
								{
									$mHtml->OpenDiv("id:tabinfclf");
									foreach ($tabinfclf as $Kinfoet => $Vinfoet) 
									{
										$mHtml->SetBody("<h3>".$Kinfoet."</h3>");
									}	
                            		$mHtml->CloseDiv();
								}
								if($tabinfsup)
								{
									$mHtml->OpenDiv("id:tabinfsup");
									foreach ($tabinfsup as $Kinfoet => $Vinfoet) 
									{
										$mHtml->SetBody("<h3>".$Kinfoet."</h3>");
									}	
                            		$mHtml->CloseDiv();
								}
								if($tabinfcon)
								{
									$mHtml->OpenDiv("id:tabinfcon");
									foreach ($tabinfcon as $Kinfoet => $Vinfoet) 
									{
										$mHtml->SetBody("<h3>".$Kinfoet."</h3>");
									}	
                            		$mHtml->CloseDiv();
								}
								if($tabinfcli)
								{
									$mHtml->OpenDiv("id:tabinfcli");
									foreach ($tabinfcli as $Kinfoet => $Vinfoet) 
									{
										$mHtml->SetBody("<h3>".$Kinfoet."</h3>");
									}	
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

	protected function recorrerJson($JsonRe = NULL, $Panel = NULL, $arrSub = NULL)
	{
		#print_r($JsonRe); echo "; Panel:".$Panel."; array:".$arrSub;
		$resp=NULL; //= array('ins' =>0 ,'idi'=>0, 'rep'=>0, 'eli'=>0  );
		foreach ($JsonRe as $nivel3A => $value3A) 
		{
			if($nivel3A==$Panel)
			{
				$resp[$ind_visibl]=$value3A;
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

}

$_NOTIFI = new notifi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
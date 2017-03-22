<?php
/*! \file: ajax_despachos.php
 *  \brief: Archivo para las funciones Ajax relacionadas con despachos
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: 
 *  \bug: 
 *  \warning: 
 */

global $HTTP_POST_FILES;
session_start();
define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
define ('ESTILO', $_SESSION['ESTILO']);
define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
define ('BD_STANDA', $_REQUEST['dir_aplica']);
define ('CENTRAL', $_REQUEST['dir_aplica']);

@include( "../lib/ajax.inc" );


class AjaxDespachos{

	var $conexion = NULL;

	function __construct(){
		$mHost = explode('.', $_SERVER['HTTP_HOST']);
		switch ($mHost[0]) {
			case 'dev': 	$mBD = "devbd.intrared.net:3306"; break;
			case 'qa': 	$mBD = "qabd.intrared.net:3306"; break;
			case 'avansatgl': 	$mBD = "aglbd.intrared.net"; break;
			default: $mBD = "devbd.intrared.net"; break;
		}
		$this -> conexion = new Conexion( $mBD, $_SESSION['USUARIO'], $_SESSION['CLAVE'], $_SESSION['BASE_DATOS']  );
		$this -> $_REQUEST[option]( $_REQUEST );
	}

	/*! \fn: GetNovedades
	 *  \brief: 
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: JSON
	 */
	function GetNovedades(){
		$mSql = "SELECT cod_noveda, nom_noveda
				   FROM ".BASE_DATOS.".tab_genera_noveda
				  WHERE ind_visibl = 1 ";
		if($_REQUEST[cita]=='C')
	   		if((int)$_REQUEST[ind_cumpli] == 1){
	   			$mSql .= " AND cod_noveda IN(255) ";
	   		}else{
	   			$mSql .= " AND nom_noveda LIKE 'NICC%' ";
	   		}
	   	else
	   		if((int)$_REQUEST[ind_cumpli] == 1){
	   			$mSql .= " AND cod_noveda IN(256) ";
	   		}else{
	   			$mSql .= " AND nom_noveda LIKE 'NER%' ";
	   		}

   		$mSql .= " ORDER BY 2 ";

		$consulta  = new Consulta($mSql, $this -> conexion);
		$novedadx  = $consulta -> ret_matriz();

		$novedades = array();
		for($i=0; $i<count( $novedadx ); $i++){
			$novedades[] = array('value' => $novedadx[$i][cod_noveda], 'label' => utf8_decode($novedadx[$i][nom_noveda]) );
		}
		echo json_encode( $novedades );
	}

	/*! \fn: GetNovedadesCliente
	 *  \brief: 
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: JSON
	 */
	function GetNovedadesCliente(){
		$mSql = "SELECT cod_noveda, nom_noveda
				   FROM ".BASE_DATOS.".tab_genera_noveda
				  WHERE 1=1 ";
		if($_REQUEST[cita]=='C')
	   		if((int)$_REQUEST[ind_cumpli] == 1){
	   			$mSql .= " AND cod_noveda IN(255) ";
	   		}else{
	   			$mSql .= " AND nom_noveda LIKE 'NICC%' ";
	   		}
	   	else
	   		if((int)$_REQUEST[ind_cumpli] == 1){
	   			$mSql .= " AND cod_noveda IN(256) ";
	   		}else{
	   			$mSql .= " AND cod_noveda IN(326) ";
	   		} 
   		$mSql .= " ORDER BY 2 "; 
		$consulta  = new Consulta($mSql, $this -> conexion);
		$novedadx  = $consulta -> ret_matriz(); 
		$novedades = array();
		for($i=0; $i<count( $novedadx ); $i++){
			$novedades[] = array('value' => $novedadx[$i][cod_noveda], 'label' => utf8_decode($novedadx[$i][nom_noveda]) );
		}
		echo json_encode( $novedades );
	}

	/*! \fn: GetNovedadesDescargue
	 *  \brief: 
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: JSON
	 */
	function GetNovedadesDescargue(){
		$mSql = "SELECT cod_noveda, nom_noveda
				   FROM ".BASE_DATOS.".tab_genera_noveda
				  WHERE 1=1 ";
		if($_REQUEST[cita]=='C')
	   		if((int)$_REQUEST[ind_cumpli] == 1){
	   			$mSql .= " AND cod_noveda IN(255) ";
	   		}else{
	   			$mSql .= " AND nom_noveda LIKE 'NICC%' ";
	   		}
	   	else
	   		if((int)$_REQUEST[ind_cumpli] == 1){
	   			$mSql .= " AND cod_noveda IN(256) ";
	   		}else{
	   			$mSql .= " AND cod_noveda IN(326) ";
	   		}
			$mSql .= " ORDER BY 2 ";
		$consulta  = new Consulta($mSql, $this -> conexion);
		$novedadx  = $consulta -> ret_matriz();
		$novedades = array();
		for($i=0; $i<count( $novedadx ); $i++){
			$novedades[] = array('value' => $novedadx[$i][cod_noveda], 'label' => utf8_decode($novedadx[$i][nom_noveda]) );
		}
		echo json_encode( $novedades );
	}

	/*! \fn: SaveFechaAdicio
	 *  \brief: 
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: 
	 */
	function SaveFechaAdicio( $mData )
	{
		$mData['fec_comple'] = $mData['fec_comple'].' '.$mData['hor_comple'];

		$_REQUEST['nom_destin'] = base64_decode($_REQUEST['nom_destin']);
		$_REQUEST['num_docum2'] = base64_decode($_REQUEST['num_docum2']);


		$mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_destin
					   SET ".$mData['tip_fechax']." = '".$mData['fec_comple']."',
					       usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
					       fec_modifi = NOW()
				     WHERE num_despac = '".$mData['num_despac']."'
				  	   AND num_docume IN ( ".$_REQUEST['num_docum2']." )";
		$consulta  = new Consulta($mUpdate, $this -> conexion);
		
		@include("../lib/InterfSimplexity.inc"); # Libreria de la interfaz encargada del ws a Astrans
   		if( class_exists("InterfSimplexity") )
   		{
   			
   			switch ($mData['tip_fechax']) {
   				case 'fec_inides':  $mCodEvent = 'ID'; $mCodnoveda = '9014'; $mObsNoveda = 'NED - Inicio Descargue'; break;
   				case 'fec_findes':  $mCodEvent = 'FD'; $mCodnoveda = '9015'; $mObsNoveda = 'NED - Fin Descargue';    break;       				
   				default: echo "200"; die(); break;
   			}

   			$mSimplexity = new InterfSimplexity( $this -> conexion ); # Inicia la clase de la interfaz enviando la conexion de BD
            # Valida si el documento es una remesa o remision
            # remesa es cuando la factura es igual en los campos num_docume y num_docalt de la misma tabla (tab_despac_destin, tab_despac_cordes)

   			$mListDocumen = "SELECT a.num_dessat AS num_despac, 
									a.num_despac AS num_viajex,  
									b.num_docume, 
									b.num_docalt, 
									IF(b.num_docume = b.num_docalt, '2' ,'3') AS tip_docume
							    FROM 
									".BASE_DATOS.".tab_despac_corona a,
									".BASE_DATOS.".tab_despac_destin b
							   WHERE
									a.num_dessat = b.num_despac AND
									b.num_despac = '{$mData['num_despac']}' AND 
									b.num_docume IN (". $_REQUEST["num_docum2"] .")";												 			 
            $consulta = new Consulta($mListDocumen, $this->conexion);
        	$mDatDocu = $consulta->ret_matriz("a");

        	foreach ($mDatDocu AS $mKey => $mValue) 
        	{ 

        		switch ($mValue["tip_docume"]) 
        		{
        			case '2':
    						$mDataSimplexityData = array(
		                                        "NumeroViaje"=> $mValue["num_viajex"],
		                                        "NumeroRemesa"=>$mValue["num_docalt"],
		                                        "CodigoEvento"=> $mCodEvent ,
		                                        "Fecha"=> date_format(date_create($mData['fec_comple'].":00"), 'Y/m/d H:i:s'),
		                                        "CodigoNovedad"=> $mCodnoveda,
		                                        "DescripcionNovedad"=> $mObsNoveda
		                                  );   
    						$mSimplex = $mSimplexity -> setRegistDesReme($mDataSimplexityData);     
        			break;
        			case '3':
    						$mDataSimplexityData = array(
			                                    "NumeroViaje"=> $mValue["num_viajex"],                                    
			                                    "NumeroRemision"=> $mValue["num_docume"],
			                                    "CodigoEvento"=> $mCodEvent,
			                                    "Fecha"=> date_format(date_create($mData['fec_comple'].":00"), 'Y/m/d H:i:s'),
			                                    "CodigoNovedad"=> $mCodnoveda,
			                                    "DescripcionNovedad"=> $mObsNoveda
			                                  );     
    						$mSimplex = $mSimplexity -> setRegistDesRemi($mDataSimplexityData);      
        				break;
        			
        			default: $mSimplex = NULL; break;
        		}
					
					if($mSimplex["cod_respon"] != "1") {
						mail("nelson.liberato@intrared.net", "Error eventos descargue", "Evento:".var_export($mData['tip_fechax'], true)."\nEnviados: ".var_export($mDataSimplexityData, true)."\nRespuesta:".var_export($mSimplex, true) );
					}
                 
        	}      
   		}
   		else {
   			mail("nelson.liberato@intrared.net", "No existe la clase InterfSimplexity ajax_despachos.php", 
            	 "No se pudo incluir InterfSimplexity.inc\n".var_export($mDataSimplexityCargue, true)  );
   		}
   		
		 
		if( $consulta )
		  $_MESSAGE = '200';
		else
		  $_MESSAGE = '500';
		
		echo $_MESSAGE;
	}

	/*! \fn: paintSoluci
	 *  \brief: pinta el HTML del popUp
	 *  \author: Ing. Miguel Romero "mamafoka" 
	 *  \date: 21/07/2015
	 *  \date modified: 
	 *  \param: 
	 *  \return: HTML
	 */
	function paintSoluci(){
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
		$mResult = '<div class="StyleDIV">';
		$mResult .= 	'<table>';
		$mResult .= 	'  <tr>';
		$mResult .= 	'    <td class="CellHead" width="1000px"  align="right"><center>SOLUCION NO EFECTIVA</center></td>';  
		$mResult .= 	'  </tr>';			
		$mResult .= 	'  <tr>';
		$mResult .= 	'    <td class="CellHead" width="1000px"  align="right"><center>OBSERVACION</center></td>';  
		$mResult .= 	'  </tr>';			
		$mResult .= 	'  <tr>';
		$mResult .= 	'    <td><center><textarea rows="10" cols="100"  align="right" id="des_novPop"></textarea></center></td>';  
		$mResult .= 	'  </tr>';			
		$mResult .= 	'  <tr>';
		$mResult .= 	'    <td><center><input type="radio" name="radioClientePopup">Cliente</center></td>';  
		$mResult .= 	'  </tr>';
		$mResult .= 	'</table>';
		$mResult .= '</div>';
		echo $mResult;
	}

	/*! \fn: setNovedaEfecti
	 *  \brief: inserta en la base de datos cuando una novedad no es efectiva 
	 *  \author: Ing. Miguel Romero "mamafoka" 
	 *  \date: 21/07/2015
	 *  \date modified: 
	 *  \param: 
	 *  \return: 
	 */
	function setNovedaEfecti()
	{
		include( "../lib/interfaz_lib_sat.inc" );
		include( "../lib/EnvioMensajes.inc" );
		include( "InsertNovedad.inc" );

		$mUpdate = "UPDATE ".BASE_DATOS.".tab_protoc_asigna
					   SET ind_solnov = 0
				     WHERE num_despac = '".$_REQUEST['num_despac']."'
				       AND num_consec = '".$_REQUEST['num_consec']."'";
		
		if(  $consulta  = new Consulta($mUpdate, $this -> conexion) )
		  $_MESSAGE = '200';
		else
		  $_MESSAGE = '500';


        $mSelect = "SELECT cod_noveda 
                      FROM ".$base_datos.".tab_protoc_asigna
                     WHERE num_despac = '".$_REQUEST['num_despac']."'
                       AND num_consec = '".$_REQUEST['num_consec']."'";

		$consulta = new Consulta($mSelect, $this->conexion);
		$cod_noveda_new = $consulta->ret_matriz();

		  	$mSqlProto = "SELECT a.cod_protoc
	  			 	    FROM ".BASE_DATOS.".tab_noveda_protoc a 
		  			   WHERE a.cod_noveda = '".$cod_noveda_new[0]['cod_noveda']."'
		  		       ";

	  	$consulta  = new Consulta($mSqlProto, $this -> conexion, 'R');
		$proto  = $consulta -> ret_matriz();

		$_REQUEST['protoc0_']     = $proto[0]['cod_protoc'];
		$_REQUEST['obs_protoc0_'] = $_REQUEST['obs_noveda'];
		$_REQUEST['ind_activo_']  = 'S';
		$_REQUEST['tot_protoc_']  = 1;

		$mSigPC = getNextPC( $this -> conexion, $_REQUEST[num_despac]); # Novedades del Despacho -- Script /lib/general/function.inc

	  	$mSql = "SELECT b.cod_contro, a.cod_rutasx, b.nom_contro 
	  			   FROM ".BASE_DATOS.".tab_despac_seguim a,
	  			        ".BASE_DATOS.".tab_genera_contro b
	  			  WHERE a.num_despac = '{$_REQUEST[num_despac]}'
	  			    AND b.cod_contro = '".$mSigPC[cod_contro]."'
	  			 ORDER BY a.fec_planea ASC 
	  			 LIMIT 1 ";

	  	$consulta  = new Consulta($mSql, $this -> conexion, 'R');
		$_controx  = $consulta -> ret_matriz();

		$regist["despac"] = $_REQUEST[num_despac];
		$regist["contro"] = $mSigPC[cod_contro];
		$regist["noveda"] = 342;
		$regist["tieadi"] = 240;
		$regist["fecact"] = date('Y-m-d H:i:s');
		$regist["fecnov"] = date('Y-m-d H:i:s');
		$regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
		$regist["nittra"] = $_REQUEST[cod_transp];
		$regist["indsit"] = "1";
		$regist["sitio"]  = $_controx[0][nom_contro];
		$regist["tie_ultnov"] = 0;
		$regist["tiem"]   = 0;
        $regist["observ"] = $_REQUEST[obs_noveda];
		$regist["rutax"]  = $_controx[0][cod_rutasx];
		$regist['consecProtoc'] = $_REQUEST['num_consec'];

		$transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);
		$RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 2);

	  	$sqlMaxConsec = "SELECT MAX(a.cod_consec) AS maximo
	  			 	    FROM ".BASE_DATOS.".tab_protoc_asigna a 
		  			   WHERE a.num_despac = '".$_REQUEST['num_despac']."'
		  		       ";

	  	$consulta  = new Consulta($sqlMaxConsec, $this -> conexion, 'R');
		$mMaxConsec  = $consulta -> ret_matriz();

		$mUpdate = "UPDATE ".BASE_DATOS.".tab_protoc_asigna
					   SET ind_solnov = 0, num_conant = ".$_REQUEST['num_consec']."
				     WHERE num_despac = '".$_REQUEST['num_despac']."'
				       AND num_consec = '".$mMaxConsec[0]['maximo']."'";

		if(  $consulta  = new Consulta($mUpdate, $this -> conexion) )
		  $_MESSAGE = '200';
		else
		  $_MESSAGE = '500';					       

		echo $RESPON;
	}

	/*! \fn: solucionaEfectiva
	 *  \brief: inswerta en la BD cuando una novedad es efectiva
	 *  \author: Ing. Miguel Romero "mamafoka" 
	 *  \date: 21/07/2015
	 *  \date modified: 
	 *  \param: 
	 *  \return: 
	 */
	function solucionaEfectiva(){
		$mUpdate = "UPDATE ".BASE_DATOS.".tab_protoc_asigna
					   SET ind_solnov = 1
				     WHERE num_despac = '".$_REQUEST['num_despac']."'
				       AND num_consec = '".$_REQUEST['num_consec']."'";

		if(  $consulta  = new Consulta($mUpdate, $this -> conexion) )
		  $_MESSAGE = '200';
		else
		  $_MESSAGE = '500';	
	}
	
	/*! \fn: SetCitaCargue
	 *  \brief: Guarda la Novedad de la grilla cita de Cargue
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: JSON
	 */
	function SetCitaCargue(){
		include( "../lib/interfaz_lib_sat.inc" );
		include( "../lib/EnvioMensajes.inc" );
		include( "InsertNovedad.inc" );

		$_REQUEST[ind_cumcar] = $_REQUEST[ind_cumpli];
		$_REQUEST[fec_cumcar] = $_REQUEST[fec_cumpli].' '.$_REQUEST[hor_cumpli];;
		$_REQUEST[nov_cumcar] = $_REQUEST[nov_cumpli];
		$_REQUEST[obs_cumcar] = addslashes($_REQUEST[obs_cumpli]);

		if( $_REQUEST['ind_timext'] == '1' ){
			$mFec1 = $_REQUEST['fec_extrax'].' '.$_REQUEST['hor_extrax'];
			$mFec2 = date("Y-m-d H:i:s");
			$mTieadi = round( abs(strtotime($mFec1) - strtotime($mFec2)) / 60, 0);
			$_REQUEST[obs_cumcar] = $_REQUEST[obs_cumcar].". Tiempo Generado: $mTieadi Minutos";
		}else{
			$mTieadi = '0';
		}

	  	$mSql = "SELECT a.cod_contro, a.cod_rutasx, b.nom_contro, a.fec_planea
	  			   FROM ".BASE_DATOS.".tab_despac_seguim a,
	  			        ".BASE_DATOS.".tab_genera_contro b,
	  			        ".BASE_DATOS.".tab_despac_noveda c
	  			  WHERE a.num_despac = '{$_REQUEST[num_despac]}'
	  			    AND a.cod_contro = b.cod_contro
	  			    AND b.ind_virtua = 0
	  			    AND c.num_despac = a.num_despac
					AND c.cod_contro = a.cod_contro
				UNION
				SELECT a.cod_contro, a.cod_rutasx, b.nom_contro, a.fec_planea
	  			  FROM ".BASE_DATOS.".tab_despac_seguim a,
	  			       ".BASE_DATOS.".tab_genera_contro b,
	  			       ".BASE_DATOS.".tab_despac_contro c
	  			 WHERE a.num_despac = '{$_REQUEST[num_despac]}'
	  			   AND a.cod_contro = b.cod_contro
	  			   AND b.ind_virtua = 0
	  			   AND c.num_despac = a.num_despac
				   AND c.cod_contro = a.cod_contro
	  			ORDER BY 4 ASC 
	  			LIMIT 1 ";

	  	$consulta  = new Consulta($mSql, $this -> conexion, 'R');
		$valContro  = $consulta -> ret_matriz();


		if(count($valContro)>0){
		  	$_MESSAGE = array('cod_respon' => '500', 'msg_respon' => 'No se Puede Registrar la Cita de Cargue, Debido a que el Puesto de Control Fisico '.$valContro[0][nom_contro].', contiene una Novedad' );
	  		echo json_encode($_MESSAGE); 
			die();
		}


	  	$mSql = "SELECT c.cod_contro, a.cod_rutasx, b.nom_contro, c.val_duraci
				   FROM ".BASE_DATOS.".tab_despac_seguim a
		     INNER JOIN ".BASE_DATOS.".tab_genera_rutcon c 
			  	     ON a.cod_rutasx = c.cod_rutasx 
			  	    AND a.cod_contro = c.cod_contro 
		     INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
		     		 ON c.cod_contro = b.cod_contro
	  			  WHERE a.num_despac = '{$_REQUEST[num_despac]}'
		   	   	  GROUP BY c.cod_contro
		   	   	  ORDER BY c.val_duraci ASC ";
	  	$consulta  = new Consulta($mSql, $this -> conexion, 'R');
		$_controx  = $consulta -> ret_matriz();

		$query = "SELECT a.cod_transp
		 		    FROM ".BASE_DATOS.".tab_despac_vehige a
		 		   WHERE a.num_despac = '{$_REQUEST[num_despac]}'";
        $consulta = new Consulta($query, $this->conexion);
        $nitransp = $consulta->ret_matriz();


        $query = "SELECT cod_protoc
        			FROM ".BASE_DATOS.".tab_noveda_protoc
        		   WHERE cod_transp = '{$nitransp[0][0]}'
        		     AND cod_noveda = '{$_REQUEST[nov_cumcar]}'  ";

	    $consulta = new Consulta($query, $this->conexion);
        $protocol = $consulta->ret_matriz();

	  	$regist["habPAD"] = 0;
        $regist["faro"]   = '1';
        $regist["despac"] = $_REQUEST['num_despac'];
        $regist["contro"] = $_controx[0]['cod_contro'];
        $regist["noveda"] = $_REQUEST['nov_cumcar'];
        $regist["tieadi"] = $mTieadi;
        $regist["observ"] = $_REQUEST['obs_cumcar'];
        $regist["fecnov"] = $_REQUEST['fec_cumcar'];
        $regist["fecact"] = date('Y-m-d H:i:s');
        $regist["ultrep"] = '';
        $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
        $regist["sitio"]  = $_controx[0]['nom_contro'];
        $regist["rutax"]  = $_controx[0]['cod_rutasx'];
        $regist["tie_ultnov"] = 0;
        $regist["nittra"]  = $nitransp[0][0];
        $regist["ind_grides"]  = "1"; // Indicador si se envia la novedad desde una grilla (en este caso grilla de descargue)
        #-----------------------------------------------------------
        $_REQUEST['ind_protoc']   = (count($protocol)>0 ? 'yes' : 'no');
        $_REQUEST['tot_protoc_']  = 1;
        $_REQUEST['ind_activo_']  = 'S';
        $_REQUEST['obs_protoc0_'] = $_REQUEST['obs_cumcar'];
        $_REQUEST['protoc0_'] = $protocol[0][0];
        #-----------------------------------------------------------



		$transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);
		if($_REQUEST['ind_cumpli'] == 0){
			$RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 2);
		}else{
        	$RESPON = $transac_nov->InsertarNovedadPC(BASE_DATOS, $regist, 0);
		}

        if ($RESPON[0]["indica"] == 1){
			$consulta = new Consulta("SELECT 1", $this->conexion, "BR");

			$mSql = "UPDATE ".BASE_DATOS.".tab_despac_sisext
						SET ind_cumcar = '{$_REQUEST[ind_cumcar]}',
						    fec_cumcar = '{$_REQUEST[fec_cumcar]}:00',
						    nov_cumcar = '{$_REQUEST[nov_cumcar]}',
						    obs_cumcar = '{$_REQUEST[obs_cumcar]}',
						    usr_cumcar = '{$_SESSION['datos_usuario']['cod_usuari']}'
					  WHERE num_despac = '{$_REQUEST[num_despac]}'";
		  	$consulta  = new Consulta($mSql, $this -> conexion);
            $consulta = new Consulta("COMMIT", $this->conexion);
        }

       	$_MESSAGE = array('cod_respon' => ( (int)$RESPON[0]["indica"] == 1 ? '200' : '500'), 'msg_respon' => $RESPON[0][mensaj] );

       	#Lineas para enviar el cumplido de cita de cargue a Astrans, OMG!! ------------------------------------------------------------
       	if((int)$RESPON[0]["indica"] == 1)
       	{
       		@include("../lib/InterfSimplexity.inc"); # Libreria de la interfaz encargada del ws a Astrans
       		if( class_exists("InterfSimplexity") )
       		{
       			$mSimplexity = new InterfSimplexity( $this -> conexion ); # Inicia la clase de la interfaz enviando la conexion de BD
                # Consulta en numero de despacho de Astras, para nostros es el VJ
				$mDataCumpliCar = 'SELECT num_desext, fec_cumcar, nov_cumcar, obs_cumcar  
                             FROM '.BASE_DATOS.'.tab_despac_sisext 
                            WHERE num_despac = "'.$_REQUEST["num_despac"].'" ';
                $consulta = new Consulta($mDataCumpliCar, $this->conexion);
            	$mDataCar = $consulta->ret_matriz("a");
       			
                # Llena array con los datos necesarios para hacer un cumplido de cargue a astrans
                $mDataSimplexityCargue  =  array(
			                                      "NumeroViaje"=> $mDataCar[0]["num_desext"],
			                                      "CodigoEvento"=>  "LP",
			                                      "Fecha"=> date_format(date_create($_REQUEST["fec_cumcar"].":00"), 'Y/m/d H:i:s'),
			                                      "CodigoNovedad"=> $_REQUEST["nov_cumcar"],
			                                      "DescripcionNovedad"=> $_REQUEST["obs_cumcar"]
			                                    );     
			    # Envia el array de datos al metodo correspondiente de los cumplidos de cargue
			    # El metodo retorna un array con codigo y mensaje de respuesta del ws de astras, de la transaccion    
                $mCargue = $mSimplexity -> setRegistrarCargue( $mDataSimplexityCargue ); 
                 
       		}
       		else {
       			mail("nelson.liberato@intrared.net", "No existe la clase InterfSimplexity ajax_despachos.php", 
                	 "No se pudo incluir InterfSimplexity.inc\n".var_export($mDataSimplexityCargue, true)  );
       		}

       	}   

       	echo json_encode($_MESSAGE); 
	}

	/*! \fn: SetCitaDescargue
	 *  \brief: Guarda la Novedad de la grilla cita de Descargue
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: JSON
	 */
	function SetCitaDescargue(){
		 
		include( "../lib/interfaz_lib_sat.inc" );
		include( "../lib/EnvioMensajes.inc" );
		include( "InsertNovedad.inc" );

		$_REQUEST['fec_cumdes'] = $_REQUEST['fec_cumdes'].' '.$_REQUEST['hor_cumdes']; 
		$_REQUEST['obs_cumdes'] = addslashes($_REQUEST['obs_cumdes']);
		$_REQUEST['num_docume'] = "'".join("','", explode("|", $_REQUEST['num_docume']))."'";
		$_REQUEST['nom_destin'] = base64_decode($_REQUEST['nom_destin']);
		$_REQUEST['num_docum2'] = base64_decode($_REQUEST['num_docum2']);


		$consulta = new Consulta("SELECT 1", $this->conexion, "BR");

		$mSqlU = "UPDATE ".BASE_DATOS.".tab_despac_destin
					SET ind_cumdes = '{$_REQUEST['ind_cumdes']}',
					    fec_cumdes = '{$_REQUEST['fec_cumdes']}:00',
					    nov_cumdes = '{$_REQUEST['nov_cumdes']}',
					    obs_cumdes = '{$_REQUEST['obs_cumdes']}',
					    usr_cumdes = '{$_SESSION['datos_usuario']['cod_usuari']}'
				  WHERE num_despac = '{$_REQUEST['num_despac']}' 
					/*AND nom_destin = '{$_REQUEST['nom_destin']}' */
					AND num_docume IN ({$_REQUEST['num_docum2']}) ";
		 
	  		$consulta  = new Consulta($mSqlU, $this -> conexion, "R");


		if( $_REQUEST['otr_destin'] != '' ){
			$mDesti1 = explode('|', $_REQUEST['otr_destin']);
			$mDesti2 = '';
			foreach ($mDesti1 as $i => $val) {
				if($mDesti2 == ''){
					$mDesti2 = "'".base64_decode($val)."'";
				}else{
					$mDesti2 .= ",'".base64_decode($val)."'";
				}
			}
			
		  	$mSqlU = "UPDATE ".BASE_DATOS.".tab_despac_destin
						SET ind_cumdes = '{$_REQUEST['ind_cumdes']}',
						    fec_cumdes = '{$_REQUEST['fec_cumdes']}:00',
						    nov_cumdes = '{$_REQUEST['nov_cumdes']}',
						    obs_cumdes = 'MERCANCIA ENTREGADA',
						    usr_cumdes = '{$_SESSION['datos_usuario']['cod_usuari']}'
					  WHERE num_despac = '{$_REQUEST['num_despac']}' 
						AND nom_destin IN (". $mDesti2 .") ";
	  		$consulta  = new Consulta($mSqlU, $this -> conexion, "R"); 
		}
     
    
	  	$msql = "SELECT COUNT(*) AS ROW
	  			   FROM ".BASE_DATOS.".tab_despac_destin
	  			  WHERE num_despac = '$_REQUEST[num_despac]'
	  			    AND ind_cumdes IS NULL ";
	  	$consulta   = new Consulta($msql, $this -> conexion);
		$destinxx   = $consulta -> ret_matriz();
		$NUM_DESTIN = (int)$destinxx[0][ROW];

		$mSql = " SELECT a.cod_rutasx 
					FROM ".BASE_DATOS.".tab_despac_seguim a 
				   WHERE a.num_despac = '{$_REQUEST['num_despac']}' 
					 AND a.ind_estado != 2 
				GROUP BY a.cod_rutasx 
				ORDER BY a.fec_creaci ";
		$mConsult = new Consulta( $mSql, $this->conexion );
		$mRuta = $mConsult -> ret_matrix('i');
		$mRuta = $mRuta[0][0];

		if( $_REQUEST['ind_finali'] == '1' ){
			$mSql = " SELECT a.cod_contro, b.nom_contro 
						FROM ".BASE_DATOS.".tab_genera_rutcon a 
				  INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
						  ON a.cod_contro = b.cod_contro 
					   WHERE a.cod_rutasx = $mRuta 
					ORDER BY a.val_duraci DESC 
					   LIMIT 1 ";
			$mConsult = new Consulta( $mSql, $this->conexion );
			$_controx = $mConsult -> ret_matrix('a');
			$_controx = $_controx[0];
		}else{
			$_controx = getNextPC( $this -> conexion, $_REQUEST['num_despac'] );
		}
  

		$query = "SELECT a.cod_transp
		 		    FROM ".BASE_DATOS.".tab_despac_vehige a
		 		   WHERE a.num_despac = '{$_REQUEST[num_despac]}'";
        $consulta = new Consulta($query, $this->conexion);
        $nitransp = $consulta->ret_matriz();

        $query = "SELECT cod_protoc
        			FROM ".BASE_DATOS.".tab_noveda_protoc
        		   WHERE cod_transp = '{$nitransp[0][0]}'
        		     AND cod_noveda = '{$_REQUEST[nov_cumdes]}'  ";
	    $consulta = new Consulta($query, $this->conexion, "RC");
        $protocol = $consulta->ret_matriz();

        #-----------------------------------------------------------
        $_REQUEST['ind_protoc']   =  (count($protocol)>0 ? 'yes' : 'no');
        $_REQUEST['tot_protoc_']  = 1;
        $_REQUEST['ind_activo_']  = 'S';
        $_REQUEST['obs_protoc0_'] = $_REQUEST[obs_cumdes];
        $_REQUEST['protoc0_'] = $protocol[0][0];
        #-----------------------------------------------------------

        if($NUM_DESTIN > 1){
            $regist["despac"] = $_REQUEST[num_despac];
            $regist["contro"] = $_controx[cod_contro];
            $regist["noveda"] = $_REQUEST[nov_cumdes];
            $regist["tieadi"] = 0;
            $regist["fecact"] = date('Y-m-d H:i:s');
            $regist["fecnov"] = $_REQUEST[fec_cumdes];
            $regist["observ"] = $_REQUEST[obs_cumdes];
            $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
            $regist["nittra"] = $nitransp[0][0];
            $regist["indsit"] = "1";
            $regist["sitio"]  = $_controx[nom_contro] .' - '. $_REQUEST[nom_destin];
            $regist["tie_ultnov"] = 0;
            $regist["tiem"]   = 0;
            $regist["rutax"]  = $mRuta;
            $regist["ind_grides"]  = "1"; // Indicador si se envia la novedad desde una grilla (en este caso grilla de descargue)
        
            $transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);

             
            if( $_REQUEST['ind_finali'] == '1' ){
            	$RESPON = $transac_nov->InsertarNovedadPC(BASE_DATOS, $regist, 2);
            }else{
            	$RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 2);
            }
        }else{
    		$regist["habPAD"] = 0;
            $regist["faro"]   = '1';
            $regist["despac"] = $_REQUEST[num_despac];
            $regist["contro"] = $_controx[cod_contro];
            $regist["noveda"] = $_REQUEST[nov_cumdes];
            $regist["tieadi"] = 0;
            $regist["observ"] = $_REQUEST[obs_cumdes];
            $regist["fecnov"] = $_REQUEST[fec_cumdes];
            $regist["fecact"] = date('Y-m-d H:i:s');
            $regist["ultrep"] = '';
            $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
            $regist["sitio"]  = $_controx[nom_contro].' - '. $_REQUEST[nom_destin];
            $regist["rutax"]  = $mRuta;
            $regist["tie_ultnov"] = 0;
            $regist["nittra"] = $nitransp[0][0];
            $regist["ind_grides"]  = "1"; // Indicador si se envia la novedad desde una grilla (en este caso grilla de descargue)
        
            $transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);
            
            if( $_REQUEST['ind_finali'] == '1' ){
            	$RESPON = $transac_nov->InsertarNovedadPC(BASE_DATOS, $regist, 2);
            }else{
            	$RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 2);
            }
        }
        
        if ($RESPON[0]["indica"])
            $consulta = new Consulta("COMMIT", $this->conexion);


       	$_MESSAGE = array('cod_respon' => ( (int)$RESPON[0]["indica"] == 1 ? '200' : '500'), 'msg_respon' => $RESPON[0][mensaj] );

       	
       	#Lineas para enviar el cumplido de cita de cargue a Astrans, OMG!! ------------------------------------------------------------
       	if((int)$RESPON[0]["indica"] == 1)
       	{
       		@include("../lib/InterfSimplexity.inc"); # Libreria de la interfaz encargada del ws a Astrans
       		if( class_exists("InterfSimplexity") )
       		{
       			$mSimplexity = new InterfSimplexity( $this -> conexion ); # Inicia la clase de la interfaz enviando la conexion de BD
                # Valida si el documento es una remesa o remision
                # remesa es cuando la factura es igual en los campos num_docume y num_docalt de la misma tabla (tab_despac_destin, tab_despac_cordes)

       			$mListDocumen = "SELECT a.num_dessat AS num_despac, 
										a.num_despac AS num_viajex,  
										b.num_docume, 
										b.num_docalt, 
										IF(b.num_docume = b.num_docalt, '2' ,'3') AS tip_docume
								    FROM 
										".BASE_DATOS.".tab_despac_corona a,
										".BASE_DATOS.".tab_despac_destin b
								   WHERE
										a.num_dessat = b.num_despac AND
										b.num_despac = '{$_REQUEST[num_despac]}' AND 
										b.num_docume IN (". $_REQUEST["num_docum2"] .")";		
										  
										 			 
                $consulta = new Consulta($mListDocumen, $this->conexion);
            	$mDatDocu = $consulta->ret_matriz("a");

            	foreach ($mDatDocu AS $mKey => $mValue) 
            	{
            		switch ($mValue["tip_docume"]) 
            		{
            			case '2':
        						$mDataSimplexityData = array(
			                                        "NumeroViaje"=> $mValue["num_viajex"],
			                                        "NumeroRemesa"=>$mValue["num_docalt"],
			                                        "CodigoEvento"=> "LD" ,
			                                        "Fecha"=> date_format(date_create($_REQUEST["fec_cumdes"].":00"), 'Y/m/d H:i:s'),
			                                        "CodigoNovedad"=> $_REQUEST["nov_cumdes"],
			                                        "DescripcionNovedad"=> $_REQUEST["obs_cumdes"]
			                                  );   
        						$mSimplex = $mSimplexity -> setRegistDesReme($mDataSimplexityData);     
            			break;
            			case '3':
        						$mDataSimplexityData = array(
				                                    "NumeroViaje"=> $mValue["num_viajex"],                                    
				                                    "NumeroRemision"=> $mValue["num_docume"],
				                                    "CodigoEvento"=> "LD",
				                                    "Fecha"=> date_format(date_create($_REQUEST["fec_cumdes"].":00"), 'Y/m/d H:i:s'),
				                                    "CodigoNovedad"=> $_REQUEST["nov_cumdes"],
				                                    "DescripcionNovedad"=> $_REQUEST["obs_cumdes"]
				                                  );     
        						$mSimplex = $mSimplexity -> setRegistDesRemi($mDataSimplexityData);      
            				break;
            			
            			default: $mSimplex = NULL; break;
            		}
            	}      
       		}
       		else {
       			mail("nelson.liberato@intrared.net", "No existe la clase InterfSimplexity ajax_despachos.php", 
                	 "No se pudo incluir InterfSimplexity.inc\n".var_export($mDataSimplexityCargue, true)  );
       		}
       	}
        
       	echo json_encode($_MESSAGE);
	}

	/*! \fn: SetCitaDescargue
	 *  \brief: Guarda la Novedad de la grilla cita de Descargue por cliente
	 *  \author: 
	 *  \date: 
	 *  \date modified: 
	 *  \param: 
	 *  \return: JSON
	 */
	function SetCitaDescargueClientes(){
		include( "../lib/interfaz_lib_sat.inc" );
		include( "../lib/EnvioMensajes.inc" );
		include( "InsertNovedad.inc" );
		$_REQUEST[ind_cumdes] = $_REQUEST[ind_cliente_cumdes];
		$_REQUEST[fec_cumdes] = $_REQUEST[fec_cumdes_cliente];			
		$_REQUEST[hor_cumdes] = $_REQUEST[hor_cumdes_cliente];
		$_REQUEST[nov_cumdes] = $_REQUEST[nov_cumdes_cliente];
		$_REQUEST[obs_cumdes] = addslashes($_REQUEST[obs_cumdes_cliente]); 

		$consulta = new Consulta("SELECT 1", $this->conexion, "BR");

		$mSql = "UPDATE ".BASE_DATOS.".tab_despac_inddes
					SET ind_cumpli = '{$_REQUEST[ind_cumdes]}',
					    fec_citeje = '{$_REQUEST[fec_cumdes]}',
					    hor_citeje = '{$_REQUEST[hor_cumdes]}',
					    cod_noveda = '{$_REQUEST[nov_cumdes]}',
					    obs_noveda = '{$_REQUEST[obs_cumdes]}',
					    usr_modifi = '{$_SESSION['datos_usuario']['cod_usuari']}'
				  WHERE num_despac = '{$_REQUEST[num_despac]}'
				    AND cod_client = '{$_REQUEST[cod_cliente]}'";
	  	$consulta  = new Consulta($mSql, $this -> conexion);
		

	  	$msql = "SELECT COUNT(*) AS ROW
	  			   FROM ".BASE_DATOS.".tab_despac_destin
	  			  WHERE num_despac = '$_REQUEST[num_despac]'
	  			    AND ind_cumdes IS NULL ";

	  	$consulta   = new Consulta($msql, $this -> conexion, 'R');
		$destinxx   = $consulta -> ret_matriz();
		$NUM_DESTIN = (int)$destinxx[0][ROW];
		

		if($_REQUEST[nov_cumdes] == 326){
			#Codigo del ultimo puesto de control con novedad
			$mSql = " SELECT a.cod_conult as cod_contro, b.cod_rutasx, c.nom_contro 
			         	FROM ".BASE_DATOS.".tab_despac_despac a 
			      INNER JOIN ".BASE_DATOS.".tab_despac_seguim b 
			      		  ON a.num_despac = b.num_despac
			      INNER JOIN ".BASE_DATOS.".tab_genera_contro c 
			      		  ON a.cod_conult = c.cod_contro 
			           WHERE a.num_despac = '$_REQUEST[num_despac]' 
			        	 AND b.ind_estado != 2
			           LIMIT 1";
			$mConsult = new Consulta( $mSql, $this -> conexion );
			$mUltContro = $mConsult -> ret_matrix('a');				


		    $mSql = " SELECT a.cod_noveda
			         FROM ".BASE_DATOS.".tab_despac_noveda a 
			        WHERE a.num_despac = '$_REQUEST[num_despac]' 
			          AND a.cod_contro = '{$mUltContro[0][cod_contro]}' 
			     ORDER BY a.fec_noveda ";

			$mConsult = new Consulta( $mSql, $this -> conexion );
			$mValidaNovSitio = $mConsult -> ret_matrix('a');

			if ($mValidaNovSitio) {

				  	$mSql = "SELECT a.cod_contro, a.cod_rutasx, b.nom_contro 
				  			   FROM ".BASE_DATOS.".tab_despac_seguim a
				  	     INNER JOIN ".BASE_DATOS.".tab_genera_contro b
				  		         ON a.cod_contro = b.cod_contro 
				  		 INNER JOIN ".BASE_DATOS.".tab_genera_rutcon c 
				  		 		 ON a.cod_rutasx = c.cod_rutasx
				  			  WHERE a.num_despac = '{$_REQUEST[num_despac]}'
				  			  	AND a.ind_estado != 2
				  			    AND a.fec_planea > (
				  			    						SELECT x.fec_planea
				  			    						  FROM ".BASE_DATOS.".tab_despac_seguim x 
				  								    INNER JOIN ".BASE_DATOS.".tab_genera_rutcon y 
				  								    		ON x.cod_rutasx = y.cod_rutasx 
				  			    						 WHERE x.num_despac = '{$_REQUEST[num_despac]}'
				  			    						   AND x.cod_contro = '{$mUltContro[0][cod_contro]}' 
				  			    						   AND x.ind_estado != 2
				  			    						   LIMIT 1
				  			    					)
				  			  ORDER BY c.val_duraci ASC
				  			  LIMIT 1 ";
				$mConsult = new Consulta( $mSql, $this -> conexion );
				$mUltContro = $mConsult -> ret_matrix('a');	
			}

			$_controx = $mUltContro;
		}else{
			$mSql = "SELECT a.cod_contro, a.cod_rutasx, b.nom_contro
					  FROM ".BASE_DATOS.".tab_despac_seguim a 
			    INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
					    ON a.cod_contro = b.cod_contro 
			    INNER JOIN ".BASE_DATOS.".tab_genera_rutcon c 
					    ON a.cod_rutasx = c.cod_rutasx 
					   AND a.cod_contro = c.cod_contro
					 WHERE a.num_despac = '{$_REQUEST[num_despac]}' 
				  GROUP BY c.val_duraci
				  ORDER BY c.val_duraci DESC
					 LIMIT 1";
			$consulta  = new Consulta($mSql, $this -> conexion, 'R');
			$_controx  = $consulta -> ret_matriz();
		}


		$query = "SELECT a.cod_transp
		 		    FROM ".BASE_DATOS.".tab_despac_vehige a
		 		   WHERE a.num_despac = '{$_REQUEST[num_despac]}'";
        $consulta = new Consulta($query, $this->conexion);
        $nitransp = $consulta->ret_matriz();
		

        $query = "SELECT cod_protoc
        			FROM ".BASE_DATOS.".tab_noveda_protoc
        		   WHERE cod_transp = '{$nitransp[0][0]}'
        		     AND cod_noveda = '{$_REQUEST[nov_cumdes]}'  ";
	    $consulta = new Consulta($query, $this->conexion);
        $protocol = $consulta->ret_matriz();
		

        #-----------------------------------------------------------
        $_REQUEST['ind_protoc']   =  (count($protocol)>0 ? 'yes' : 'no');
        $_REQUEST['tot_protoc_']  = 1;
        $_REQUEST['ind_activo_']  = 'S';
        $_REQUEST['obs_protoc0_'] = $_REQUEST[obs_cumdes_cliente];
        $_REQUEST['protoc0_'] = $protocol[0][0];
        #-----------------------------------------------------------
		

        if($NUM_DESTIN > 1){
            $regist["despac"] = $_REQUEST[num_despac];
            $regist["contro"] = $_controx[0][cod_contro];
            $regist["noveda"] = $_REQUEST[nov_cumdes];
            $regist["observ"] = $_REQUEST[obs_cumdes_cliente];
            $regist["tieadi"] = 0;
            $regist["fecact"] = date('Y-m-d H:i:s');
            $regist["fecnov"] = $_REQUEST[fec_cumdes]."-".$_REQUEST[hor_cumdes];
            $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
            $regist["nittra"] = $nitransp[0][0];
            $regist["indsit"] = "1";
            $regist["sitio"]  = $_controx[0][nom_contro] .' - '. $_REQUEST[nom_destin];
            $regist["tie_ultnov"] = 0;
            $regist["tiem"]   = 0;
            $regist["rutax"]  = $_controx[0][cod_rutasx];
            $regist["ind_grides"]  = "1"; // Indicador si se envia la novedad desde una grilla (en este caso grilla de descargue - Cliente)
            
            $transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);

            $RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 2);
        }else{
    		$regist["habPAD"] = 0;
            $regist["faro"]   = '1';
            $regist["despac"] = $_REQUEST[num_despac];
            $regist["contro"] = $_controx[0][cod_contro];
            $regist["noveda"] = $_REQUEST[nov_cumdes];
            $regist["tieadi"] = 0;
            $regist["observ"] = $_REQUEST[obs_cumdes_cliente];
            $regist["fecnov"] = $_REQUEST[fec_cumdes]."-".$_REQUEST[hor_cumdes];
            $regist["fecact"] = date('Y-m-d H:i:s');
            $regist["ultrep"] = '';
            $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
            $regist["sitio"]  = $_controx[0][nom_contro];
            $regist["rutax"]  = $_controx[0][cod_rutasx];
            $regist["tie_ultnov"] = 0;
            $regist["nittra"] = $nitransp[0][0];
            $regist["ind_grides"]  = "1"; // Indicador si se envia la novedad desde una grilla (en este caso grilla de descargue - Cliente)

            $transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);
        	$RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 2);
        }
		
        
        if ($RESPON[0]["indica"])
            $consulta = new Consulta("COMMIT", $this->conexion);


       	$_MESSAGE = array('cod_respon' => ( (int)$RESPON[0]["indica"] == 1 ? '200' : '500'), 'msg_respon' => $RESPON[0][mensaj] );
        
       	echo json_encode($_MESSAGE);
	}

	/*! \fn: timeExtra
	 *  \brief: Crea HTML para el tiempo extra en la grilla de cargue
	 *  \author: Ing. Fabian Salinas
	 *  \date:  01/12/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData  Array  _REQUEST
	 *  \return: Json
	 */
	function timeExtra( $mData = null )
	{
		$mHtml = array( 'tdHourExtID' => '<input type="text" id="fec_extraxID" name="fec_extrax" size="10" maxlength="10" value="'.date('Y-m-d').'" />', 
										'tdDateExtID' => '<input type="text" id="hor_extraxID" name="hor_extrax" size="8"  maxlength="8"  value="'.date('H:i:00').'" />',
										'hidden' => '<input type="hidden" id="dateActualID" name="dateActual" value="'.date('Y-m-d H:i:00').'" />'
									);

		echo json_encode($mHtml);
	}

	/*! \fn: getOtrosDestin
	 *  \brief: Valida si existen otros destinatarios pendientes por diligenciar grilla de descargue, si falta retorna HTML 
	 *  \author: Ing. Fabian Salinas
	 *  \date:  14/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData  Array  Data POST
	 *  \return: 
	 */
	function getOtrosDestin( $mData = null ){
		$mSql = "SELECT a.nom_destin
				   FROM ".BASE_DATOS.".tab_despac_destin a 
				  WHERE a.nom_destin != '".base64_decode($mData['nom_destin'])."' 
					AND a.num_despac = '".$mData['num_despac']."'
					AND a.nom_destin NOT IN (
												 SELECT b.nom_destin
												   FROM ".BASE_DATOS.".tab_despac_destin b 
												  WHERE b.ind_cumdes IS NOT NULL 
													AND b.num_despac = '".$mData['num_despac']."'
											)
			   GROUP BY a.nom_destin ";
	  	$mConsult  = new Consulta($mSql, $this -> conexion);
		$mDestin  = $mConsult -> ret_matrix('i');
		$x = sizeof($mDestin);

		if( $x > 0 ){
			echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

			$mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';
				$mHtml .= '<tr>';
					$mHtml .= '<td class="CellHead">Seleccione</td>';
					$mHtml .= '<td class="CellHead">#</td>';
					$mHtml .= '<td class="CellHead">Nombre del Cliente</td>';
				$mHtml .= '</tr>';

				for ($i=0; $i < $x; $i++) { 
					$mHtml .= '<tr>';
						$mHtml .= '<td class="cellInfo"><input type="checkbox" name="cod_destin'.$i.'" id="cod_destin'.$i.'" value="'.base64_encode($mDestin[$i][0]).'" /></td>';
						$mHtml .= '<td class="cellInfo">'.($i+1).'</td>';
						$mHtml .= '<td class="cellInfo">'.$mDestin[$i][0].'</td>';
					$mHtml .= '</tr>';
				}

				$mHtml .= '<tr><td colspan="3" align="center">';
					$mHtml .= '<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button" value="Aceptar" onclick="validateDestin('.$mData['index'].');" />';
				$mHtml .= '</td></tr>';
			$mHtml .= '</table>';
		}else{
			$mHtml = '1';
		}

		echo $mHtml;
	}

	/*! \fn: pendienteDestin
	 *  \brief: Verifica un despacho tien destinatarios pendientes por grilla, imprime 1 si hay pendientes
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/01/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData  Array  Data POST
	 *  \return: 
	 */
	function pendienteDestin( $mData = null ){
		$mSql = "SELECT a.cod_respon 
				   FROM ".BASE_DATOS.".tab_genera_perfil a 
				  WHERE a.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
	  	$mConsult  = new Consulta($mSql, $this -> conexion);
		$mRespon  = $mConsult -> ret_matrix('i');

		$mSql = "SELECT a.nom_destin
				   FROM ".BASE_DATOS.".tab_despac_destin a 
				  WHERE a.num_despac = '".$mData['num_despac']."' 
					AND a.ind_cumdes IS NULL
			   GROUP BY a.nom_destin ";
	  	$mConsult  = new Consulta($mSql, $this -> conexion);
		$mDestin  = $mConsult -> ret_matrix('i');

		if( in_array($mRespon[0][0], array(7,9)) ){
			echo '0';
		}elseif( sizeof($mDestin) > 0 ){
			echo '1';
		}else{
			echo '0';
		}
	}
}

$execute = new AjaxDespachos();

?>
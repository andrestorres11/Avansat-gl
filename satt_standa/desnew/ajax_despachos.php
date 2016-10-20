<?php

global $HTTP_POST_FILES;
session_start();
define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
define ('ESTILO', $_SESSION['ESTILO']);
define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
define ('BD_STANDA', $_REQUEST['dir_aplica']);
define ('CENTRAL', $_REQUEST['dir_aplica']);

include( "../lib/general/conexion_lib.inc" );
include( "../lib/general/form_lib.inc" );
include( "../lib/general/tabla_lib.inc" );

class AjaxDespachos{

	var $conexion = NULL;

	function __construct(){

		 $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION['USUARIO'], $_SESSION['CLAVE'], $_SESSION['BASE_DATOS']  );
		 $this -> $_REQUEST[option]( $_REQUEST );
	}

	function GetNovedades(){
		$mSql = "SELECT cod_noveda, nom_noveda
				   FROM ".BASE_DATOS.".tab_genera_noveda
				  WHERE 1=1 ";
		if($_REQUEST[cita]=='C')
	   		if((int)$_REQUEST[ind_cumpli]){
	   			$mSql .= " AND cod_noveda IN(255) ";
	   		}else{
	   			$mSql .= " AND nom_noveda LIKE 'NICC%' ";
	   		}
	   	else
	   		if((int)$_REQUEST[ind_cumpli]){
	   			$mSql .= " AND cod_noveda IN(256) ";
	   		}else{
	   			$mSql .= " AND nom_noveda LIKE 'NED%' ";
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


	function SetCitaCargue(){

		include( "../lib/interfaz_lib_sat.inc" );
		include( "../lib/EnvioMensajes.inc" );
		include( "InsertNovedad.inc" );

		$_REQUEST[ind_cumcar] = $_REQUEST[ind_cumpli];
		$_REQUEST[fec_cumcar] = $_REQUEST[fec_cumpli].' '.$_REQUEST[hor_cumpli];;
		$_REQUEST[nov_cumcar] = $_REQUEST[nov_cumpli];
		$_REQUEST[obs_cumcar] = addslashes($_REQUEST[obs_cumpli]);


		$consulta = new Consulta("SELECT 1", $this->conexion, "BR");

		$mSql = "UPDATE ".BASE_DATOS.".tab_despac_sisext
					SET ind_cumcar = '{$_REQUEST[ind_cumcar]}',
					    fec_cumcar = '{$_REQUEST[fec_cumcar]}:00',
					    nov_cumcar = '{$_REQUEST[nov_cumcar]}',
					    obs_cumcar = '{$_REQUEST[obs_cumcar]}',
					    usr_cumcar = '{$_SESSION['datos_usuario']['cod_usuari']}'
				  WHERE num_despac = '{$_REQUEST[num_despac]}'";

	  

	  	$consulta  = new Consulta($mSql, $this -> conexion);


	  	$mSql = "SELECT a.cod_contro, a.cod_rutasx, b.nom_contro 
	  			   FROM ".BASE_DATOS.".tab_despac_seguim a,
	  			        ".BASE_DATOS.".tab_genera_contro b
	  			  WHERE a.num_despac = '{$_REQUEST[num_despac]}'
	  			    AND a.cod_contro = b.cod_contro
	  			 ORDER BY a.fec_planea ASC 
	  			 LIMIT 1 ";

	  	$consulta  = new Consulta($mSql, $this -> conexion, 'R');
		$_controx  = $consulta -> ret_matriz();


	  	$regist["habPAD"] = 0;
        $regist["faro"]   = '1';
        $regist["despac"] = $_REQUEST[num_despac];
        $regist["contro"] = $_controx[0][cod_contro];
        $regist["noveda"] = $_REQUEST[nov_cumcar];
        $regist["tieadi"] = 0;
        $regist["observ"] = $_REQUEST[obs_cumcar];
        $regist["fecnov"] = $_REQUEST[fec_cumcar];
        $regist["fecact"] = date('Y-m-d H:i:s');
        $regist["ultrep"] = '';
        $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
        $regist["sitio"]  = $_controx[0][nom_contro];
        $regist["rutax"]  = $_controx[0][cod_rutasx];
        $regist["tie_ultnov"] = 0;


		$transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);
        $RESPON = $transac_nov->InsertarNovedadPC(BASE_DATOS, $regist, 0);

        if ($RESPON[0]["indica"])
            $consulta = new Consulta("COMMIT", $this->conexion);

       	$_MESSAGE = array('cod_respon' => ( (int)$RESPON[0]["indica"] == 1 ? '200' : '500'), 'msg_respon' => $RESPON[0][mensaj] );

       	echo json_encode($_MESSAGE); 

	}


	function SetCitaDescargue(){

		include( "../lib/interfaz_lib_sat.inc" );
		include( "../lib/EnvioMensajes.inc" );
		include( "InsertNovedad.inc" );

		$_REQUEST[ind_cumdes] = $_REQUEST[ind_cumdes];
		$_REQUEST[fec_cumdes] = $_REQUEST[fec_cumdes].' '.$_REQUEST[hor_cumdes];;
		$_REQUEST[nov_cumdes] = $_REQUEST[nov_cumdes];
		$_REQUEST[obs_cumdes] = addslashes($_REQUEST[obs_cumdes]);


		$consulta = new Consulta("SELECT 1", $this->conexion, "BR");

		$mSql = "UPDATE ".BASE_DATOS.".tab_despac_destin
					SET ind_cumdes = '{$_REQUEST[ind_cumdes]}',
					    fec_cumdes = '{$_REQUEST[fec_cumdes]}:00',
					    nov_cumdes = '{$_REQUEST[nov_cumdes]}',
					    obs_cumdes = '{$_REQUEST[obs_cumdes]}',
					    usr_cumdes = '{$_SESSION['datos_usuario']['cod_usuari']}'
				  WHERE num_despac = '{$_REQUEST[num_despac]}'
				  	AND num_docume = '{$_REQUEST[num_docume]}'";

	  	$consulta  = new Consulta($mSql, $this -> conexion);


	  	$mSql = "SELECT a.cod_contro, a.cod_rutasx, b.nom_contro 
	  			   FROM ".BASE_DATOS.".tab_despac_seguim a,
	  			        ".BASE_DATOS.".tab_genera_contro b
	  			  WHERE a.num_despac = '{$_REQUEST[num_despac]}'
	  			    AND a.cod_contro = b.cod_contro
	  			 ORDER BY a.fec_planea DESC 
	  			 LIMIT 1 ";

	  	$consulta  = new Consulta($mSql, $this -> conexion, 'R');
		$_controx  = $consulta -> ret_matriz();

		$query = "SELECT a.cod_transp
		 		    FROM ".BASE_DATOS.".tab_despac_vehige a
		 		   WHERE a.num_despac = '{$_REQUEST[num_despac]}'";

        $consulta = new Consulta($query, $this->conexion);
        $nitransp = $consulta->ret_matriz();

        $regist["despac"] = $_REQUEST[num_despac];
        $regist["contro"] = $_controx[0][cod_contro];
        $regist["noveda"] = $_REQUEST[nov_cumdes];
        $regist["tieadi"] = 0;
        $regist["fecact"] = date('Y-m-d H:i:s');
        $regist["fecnov"] = $_REQUEST[fec_cumdes];
        $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
        $regist["nittra"] = $nitransp[0][0];
        $regist["indsit"] = "1";
        $regist["sitio"]  = $_controx[0][nom_contro] .' - '. $_REQUEST[nom_destin];
        $regist["tie_ultnov"] = 0;
        $regist["tiem"]   = 0;
        $regist["rutax"]  = $_controx[0][cod_rutasx];


        $transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['opcion'], $_SESSION['codigo'], $this->conexion);
        $RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 0);

        if ($RESPON[0]["indica"])
            $consulta = new Consulta("COMMIT", $this->conexion);

       	$_MESSAGE = array('cod_respon' => ( (int)$RESPON[0]["indica"] == 1 ? '200' : '500'), 'msg_respon' => $RESPON[0][mensaj] );

       	echo json_encode($_MESSAGE); 

	}


	function SendMail(){

	}

}

$execute = new AjaxDespachos();
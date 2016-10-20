<?php
class Proc_despac
{
	var $conexion, $cod_aplica, $usuario;
	
	function __construct($co, $us, $ca)
	{
		$this -> conexion = $co;
		$this -> usuario = $us;
		$this -> cod_aplica = $ca;
		$this -> principal();
	}
	
	function principal()
	{       
echo "....";
		if(!isset($_REQUEST[opcion]))
			$this -> Listar();
		else
		{
			switch($_REQUEST[opcion])
			{
				case "1":
					$this -> Datos();
				break;
				
				case "informe";
					$this -> Informe();
				break;
				
				default:
					$this -> Listar();
				break;
			}
		}
	}
	
	function Informe()
	{
		session_start();
		include( "../lib/general/conexion_lib.inc" );
		include ("../lib/bd/seguridad/aplica_filtro_usuari_lib.inc");
		include ("../lib/bd/seguridad/aplica_filtro_perfil_lib.inc");
		include( "../lib/general/dinamic_list.inc" );
		include( "../lib/general/form_lib.inc" );
		include( "../lib/GeneralFunctions.inc" );
		include( "../lib/general/paginador_lib.inc" );
		include( "../despac/Despachos.inc" );

		define("COD_PERFIL_SUPERUSR", "666");// constante para perfil administrador del sistema
		define("COD_PERFIL_ADMINIST", "1");// constante para perfil administrador de empresa
		
		define("COD_FILTRO_EMPTRA", "1");// constante para filtro de Transportadora
		define("COD_FILTRO_AGENCI", "2");// constante para filtro de Agencias de la transportadora
		define("COD_FILTRO_CLIENT", "3");// constante para filtro de Generadores de Carga
		define("COD_FILTRO_CONDUC", "4");//constante para filtro de conductores
		define("COD_FILTRO_PROPIE", "5");//constante para filtro de propietarios
		define("COD_FILTRO_POSEED", "6");//constante para filtro de poseedores
		
		define("COD_ESTADO_PENDIE", "0");//constante para el estado Pendiente
		define("COD_ESTADO_ACTIVO", "1");//constante para el estado Activo
		define("COD_ESTADO_INACTI", "2");//constante para el estado Inactivo
		
		define("COD_APLICACION", "1");// constante para el codigo de la aplicacion
		
		
		
		$datos_usuario = $_SESSION[datos_usuario];
		
	
				
		$US = base64_decode($_POST[sachu]);
		$CL = base64_decode($_POST[petro]);
		$BD = base64_decode($_POST[oplix]);
		$CN = base64_decode($_POST[central]);
		$ES = base64_decode($_POST[estilo]);	
		
		define( DIR_APLICA_CENTRAL, $CN );
		define( BASE_DATOS, $BD );
		define( ESTILO, $ES );
		
        $this -> conexion = new Conexion( $_SESSION['HOST'], $US, $CL , $BD );
				
		
		$this -> aplica = 1;
		
		$objciud = new Despachos( $_REQUEST[cod_servic], $_REQUEST[opcion], $this -> aplica, $this -> conexion );
		$fechoract = date("d-M-Y h:i:s A");
		
		$query = "SELECT a.ind_remdes
		  		     FROM ".BASE_DATOS.".tab_config_parame a
		  		    WHERE a.ind_remdes = '1'";
		
		  $consulta = new Consulta($query, $this -> conexion);
		  $manredes = $consulta -> ret_matriz();
		
		  $query = "SELECT a.ind_desurb
		  		      FROM ".BASE_DATOS.".tab_config_parame a
		  		     WHERE a.ind_desurb = '1' ";
		
		  $consulta = new Consulta($query, $this -> conexion);
		  $desurb = $consulta -> ret_matriz();
		
		  $query = "SELECT a.ind_restra
		  		     FROM ".BASE_DATOS.".tab_config_parame a
		  		    WHERE a.ind_restra = '1'
		  		   ";
		
		$consulta = new Consulta($query, $this -> conexion);
		$resptran = $consulta -> ret_matriz();

		$query = "SELECT j.cod_tercer,j.abr_tercer,j.cod_ciudad
		              FROM ".BASE_DATOS.".tab_despac_despac a,
		                   ".BASE_DATOS.".tab_despac_seguim b,
		                   ".BASE_DATOS.".tab_despac_vehige d,
		                   ".BASE_DATOS.".tab_vehicu_vehicu i,
		                   ".BASE_DATOS.".tab_tercer_tercer j
		             WHERE a.num_despac = d.num_despac AND
		             	   a.num_despac = b.num_despac AND
		                   i.num_placax = d.num_placax AND
		                   d.cod_transp = j.cod_tercer AND
		                   a.fec_salida Is Not Null AND
		                   a.fec_salida <= NOW() AND
		                   a.fec_llegad Is Null AND
		                   a.ind_anulad = 'R' AND
		                   a.ind_planru = 'S' ";
						   
		
		
		if( $datos_usuario["cod_perfil"] == "" )
		{
			$this -> cod_aplica = '1';
			
			//PARA EL FILTRO DE CONDUCTOR
			$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DE PROPIETARIO
			$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DE POSEEDOR
			$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
			}
			
			
			
			
			//PARA EL FILTRO DE ASEGURADORA
			$filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DEL CLIENTE
			$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DE LA AGENCIA
			$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
			}
		}
		else
		{
			$this -> cod_aplica = '1';
			//PARA EL FILTRO DE CONDUCTOR
			$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DE PROPIETARIO
			$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DE POSEEDOR
			$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
			}
			echo "<pre style='display:none' id='jovidio' >";
		print_r( $this -> cod_aplica );
		echo "</pre>";
			//PARA EL FILTRO DE ASEGURADORA
			$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DEL CLIENTE
			$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
			}
			
			//PARA EL FILTRO DE LA AGENCIA
			$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
			if($filtro -> listar($this -> conexion))
			{
				$datos_filtro = $filtro -> retornar();
				$query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
			}
		}
		
		$query = $query." GROUP BY 1 ORDER BY 2";
		
		echo "<div style='display:none' >$query</div>";
		
		$consulta = new Consulta($query, $this -> conexion);
		$transpor = $consulta -> ret_matriz();
		$query_exp = base64_encode($query);
		
		$exp .= "url=".NOM_URL_APLICA."&db=".BASE_DATOS."&nomarchive=Despachos_en_Transito&query_exp=".$query_exp."";
		$query = "SELECT a.cod_alarma,a.nom_alarma,a.cod_colorx,a.cant_tiempo
				  FROM ".BASE_DATOS.".tab_genera_alarma a
				  ORDER BY 4 ";
				  
		$consulta = new Consulta($query, $this -> conexion);
		$alarmas = $consulta -> ret_matriz();
		
		$totaldes = $totporll = $totsires = 0;
		
		for($i = 0; $i < sizeof($transpor); $i++)
		{
			$transpor[$i][3] = $transpor[$i][4] = $transpor[$i][5] = 0;
			
			$query = "SELECT a.num_despac 
					  FROM ".BASE_DATOS.".tab_despac_despac a, 
					  	   ".BASE_DATOS.".tab_despac_seguim b, 
						   ".BASE_DATOS.".tab_despac_vehige d, 
						   ".BASE_DATOS.".tab_vehicu_vehicu i 
					  WHERE a.num_despac = d.num_despac AND 
					  	    a.num_despac = b.num_despac AND 
							i.num_placax = d.num_placax AND
							a.fec_salida Is Not Null AND 
							a.fec_salida <= NOW() AND 
							a.fec_llegad Is Null AND 
							a.ind_anulad = 'R' AND 
							a.ind_planru = 'S' AND 
							d.cod_transp = '".$transpor[$i][0]."' 
					  GROUP BY 1 ";
			
			$consulta = new Consulta($query, $this -> conexion); 
			$despacho = $consulta -> ret_matriz();
			
			for($j = 0; $j < sizeof($despacho); $j++)
			{
				$transpor[$i][3]++;
				$totaldes++;
				
				$query = "SELECT a.cod_rutasx 
						  FROM ".BASE_DATOS.".tab_despac_seguim a 
						  WHERE a.num_despac = ".$despacho[$j][0]." GROUP BY 1 ";
				
				$consulta = new Consulta($query, $this -> conexion);
				$totrutas = $consulta -> ret_matriz();
				
				if( sizeof($totrutas) < 2 ) $camporder = "fec_planea";
				else $camporder = "fec_alarma";
				
				$query = "SELECT a.cod_contro 
						  FROM ".BASE_DATOS.".tab_despac_seguim a, 
						  	   ".BASE_DATOS.".tab_despac_vehige c 
						  WHERE a.num_despac = c.num_despac AND 
						  		c.num_despac = ".$despacho[$j][0]." AND 
								a.".$camporder." = (SELECT MAX(b.".$camporder.") 
								FROM ".BASE_DATOS.".tab_despac_seguim b 
								WHERE a.num_despac = b.num_despac ) ";
				
				$consulta = new Consulta($query, $this -> conexion);
				$ultimopc = $consulta -> ret_matriz();
				
				$query = "SELECT a.cod_contro, a.fec_noveda, d.fec_planea 
						  FROM ".BASE_DATOS.".tab_despac_noveda a, 
						  	   ".BASE_DATOS.".tab_despac_vehige c, 
							   ".BASE_DATOS.".tab_despac_seguim d 
						  WHERE a.num_despac = c.num_despac AND 
						  		a.num_despac = d.num_despac AND 
								a.cod_rutasx = d.cod_rutasx AND 
								a.cod_contro = d.cod_contro AND 
								c.num_despac = ".$despacho[$j][0]." AND 
								a.fec_noveda = (SELECT MAX(b.fec_noveda) 
								FROM ".BASE_DATOS.".tab_despac_noveda b 
								WHERE a.num_despac = b.num_despac ) ";
				
				$consulta = new Consulta($query, $this -> conexion);
				$ultimnov = $consulta -> ret_matriz();
				
				if($ultimnov)
				{
					$query = "SELECT a.ind_urbano 
							  FROM ".BASE_DATOS.".tab_genera_contro a 
							  WHERE a.cod_contro = ".$ultimnov[0][0]." AND 
							  		a.ind_urbano = '1' ";
									
					$consulta = new Consulta($query, $this -> conexion); 
					$pcontrurb = $consulta -> ret_matriz();
					
					$query = "SELECT b.fec_alarma 
							  FROM ".BASE_DATOS.".tab_despac_despac a, 
							  	   ".BASE_DATOS.".tab_despac_seguim b, 
								   ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.num_despac = c.num_despac AND
		      		  c.num_despac = b.num_despac AND
                      a.num_despac = ".$despacho[$j][0]." AND
                      b.fec_planea > '".$ultimnov[0][2]."'
                      ORDER BY 1 ";

     $consulta = new Consulta($query, $this -> conexion);
     $pfechala = $consulta -> ret_matriz();
	}
	else
	 $pcontrurb = NULL;

	if($manredes && $desurb && $pcontrurb)
	 $pcomparar = $ultimnov[0][0];
	else
     $pcomparar = $ultimopc[0][0];

    if($pcomparar == $ultimnov[0][0] || $ultimnov[0][0] == CONS_CODIGO_PCLLEG)
    {
     $transpor[$i][5]++;
     $totporll++;
    }
    else
    {
     if(!$ultimnov)
     {
      $query = "SELECT MIN(a.fec_alarma)
      		      FROM ".BASE_DATOS.".tab_despac_seguim a
      		     WHERE a.num_despac = ".$despacho[$j][0]."
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $fecalarm = $consulta -> ret_matriz();

      $tiempo_proxnov = $fecalarm[0][0];
     }
     else
      $tiempo_proxnov = $pfechala[0][0];

     $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '".$tiempo_proxnov."')) / 60";

     $tiempo = new Consulta($query, $this -> conexion);
     $tiemp_demora = $tiempo -> ret_arreglo();

     $tiemp_alarma = NULL;

     if($tiemp_demora[0] >= 0)
     {
      for($l = 0, $totalalarm = sizeof($alarmas); $l < $totalalarm; $l++)
      {      	
       if( $tiemp_demora[0] < $alarmas[0][3] )
       {
        $transpor[$i][6]++;
        $totalarm[0]++;
        $tiemp_alarma = 1;
        $l = sizeof($alarmas);
       }
       else if($tiemp_demora[0] > $alarmas[$l][3] && $tiemp_demora[0] < $alarmas[$l + 1][3])
       {       	
        $transpor[$i][7 + $l]++;
        $totalarm[$l+1]++;
        $tiemp_alarma = 1;
        $l = sizeof($alarmas);
       }
      }

      if(!$tiemp_alarma)
      {
       if($resptran)
	   {
        $query = "SELECT a.cant_tiempo
     	            FROM ".BASE_DATOS.".tab_genera_alarma a
     	     	      	 ORDER BY 1
     		     ";

        $consulta = new Consulta ($query, $this -> conexion);
        $color_maximo  = $consulta -> ret_matriz();

        $query = "SELECT a.cod_contro
      	            FROM ".BASE_DATOS.".tab_despac_contro a
      		       WHERE a.cod_noveda = ".CONS_NOVEDA_CAMALA." AND
      		             a.num_despac = ".$despacho[$j][0]."
      		     ";

        $consulta = new Consulta ($query, $this -> conexion);
        $existcambala  = $consulta -> ret_matriz();

	    if($tiemp_demora[0] > $color_maximo[sizeof($color_maximo) - 1][0] && !$existcambala)
	    {
	     $transpor[$i][6 + (sizeof($alarmas) - 2)]++;
         $totalarm[sizeof($alarmas) - 2]++;
         $tiemp_alarma = 1;
         $l = sizeof($alarmas);
	    }
        else
        {
         $query = "SELECT MAX(a.fec_contro)
	                 FROM ".BASE_DATOS.".tab_despac_contro a
	                WHERE a.num_despac = ".$despacho[$j][0]." AND
	  	        		  a.cod_noveda = ".CONS_NOVEDA_CAMALA."
	         	  ";

         $consulta = new Consulta ($query, $this -> conexion);
         $ultima_generada  = $consulta -> ret_matriz();

         if($ultimnov[0][1] < $ultima_generada[0][0])
         {
	      $transpor[$i][6 + (sizeof($alarmas) - 1)]++;
          $totalarm[sizeof($alarmas) - 1]++;
          $tiemp_alarma = 1;
          $l = sizeof($alarmas);
         }
        }
	   }
	   else
	   {
        $transpor[$i][6 + (sizeof($alarmas) - 1)]++;
        $totalarm[sizeof($alarmas) - 1]++;
	   }
      }
     }
     else
     {
      $transpor[$i][4]++;
      $totsires++;
     }
    }
   }
  }
  		
		echo "<TABLE  BORDER='0' CELLPADDING='0' CELLSPACING='0' WIDTH='100%'>";
		
		$formulario = new Formulario ("index.php","post","Despachos en Transito","form_despac");
		$_SESSION[ind_atras] = "si";
		//$formulario -> nueva_tabla();
		//$formulario -> imagen("Exportar","../".DIR_APLICA_CENTRAL."/imagenes/boton_excel.jpg",  "Exportar",120,30,0,  "onClick=\"top.window.open('../".DIR_APLICA_CENTRAL."/export/exp_despac_transi.php?".$exp."')\"",1,0);
		
		$formulario -> nueva_tabla();
		$formulario -> linea ("Fecha y Hora Reporte :: ".$fechoract,1,"t2");
		
		$formulario -> nueva_tabla();
		
		if(!$totsires) $totsires = "-";
		if(!$totporll) $totporll = "-";
		
		$formulario -> linea("",0,"t");
		$formulario -> linea("",0,"t");
		$formulario -> linea("TOTALES",0,"t",0,0,"right");
		$formulario -> linea($totaldes,0,"t",0,0,"center");
		$formulario -> linea($totsires,0,"t",0,0,"center");
		for($i = 0; $i < sizeof($alarmas); $i++)
		{
			if(!$totalarm[$i]) $totalarm[$i] = "-";
				$formulario -> linea($totalarm[$i],0,"t",0,0,"center");
		}
		
		$formulario -> linea($totporll,1,"t",0,0,"center");
		
		$formulario -> linea ("No.",0,"t");
		$formulario -> linea ("Transportadora",0,"t");
		$formulario -> linea ("Ciudad",0,"t");
		$formulario -> linea ("No. Despachos",0,"t");
		$formulario -> linea ("Sin Retraso",0,"t");
		
		for($i = 0; $i < sizeof($alarmas); $i++)
			$formulario -> linea ($alarmas[$i][1]." - ".$alarmas[$i][3]." Min",0,"i",0,0,"center",$alarmas[$i][2]);
			
		$formulario -> linea ("Por Llegada",1,"t");
		
		for($i = 0; $i < sizeof($transpor); $i++)
		{
			$ciudad_a = $objciud -> getSeleccCiudad($transpor[$i][2]);
			
			if(!$transpor[$i][4]) $transpor[$i][4] = "-";
			else
				$transpor[$i][4] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
								   "&alacla=S&totregif=".$transpor[$i][4]." \"target=\"centralFrame\">".$transpor[$i][4]."</a>";
			if(!$transpor[$i][5]) $transpor[$i][5] = "-";
			else
				$transpor[$i][5] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
								   "&alacla=L&totregif=".$transpor[$i][5]." \"target=\"centralFrame\">".$transpor[$i][5]."</a>";
								   
			$transpor[$i][3] = "<a href=\"index.php?cod_servic=3302&window=central&atras=si&transp=".$transpor[$i][0].
							   "&totregif=".$transpor[$i][3]." \"target=\"centralFrame\">".$transpor[$i][3]."</a>";
							   
			$formulario -> linea (($i+1),0,"i");
			$formulario -> linea ($transpor[$i][1],0,"i");
			$formulario -> linea (htmlentities($ciudad_a[0][1]),0,"i");
			$formulario -> linea ($transpor[$i][3],0,"i",0,0,"center");
			$formulario -> linea ($transpor[$i][4],0,"i",0,0,"center");
			
			for($j = 0; $j < sizeof($alarmas); $j++)
			{
				if(!$transpor[$i][6 + $j]) $transpor[$i][6 + $j] = "-";
				else
					$transpor[$i][6 + $j] = "<a href=\"index.php?cod_servic=3302&atras=si&window=central&transp=".$transpor[$i][0].
											"&alacla=".$alarmas[$j][0]."&totregif=".$transpor[$i][6 + $j]." \"target=\"centralFrame\">".$transpor[$i][6 + $j]."</a>";
											
				$formulario -> linea ($transpor[$i][6 + $j],0,"i",0,0,"center");
			}
			
			$formulario -> linea ($transpor[$i][5],1,"i",0,0,"center");
		}
		
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
		$formulario -> oculto("opcion",$_REQUEST[opcion],0);
		
		$formulario -> cerrar();
		echo "<TABLE>";
	}
	
	function Listar()
	{
		session_start();
		$_SESSION[inf_conexio] =  $this -> conexion ;
		$_SESSION[inf_usuario] = $this -> usuario -> retornar();
		$_SESSION[inf_aplica] = $this -> cod_aplica;
		
		$BD = $_SESSION["BASE_DATOS"];
		$US = $_SESSION["USUARIO"];
		$CL = $_SESSION["CLAVE"];
		
		echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/new_ajax.js'></script> ";
		echo " <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/comet.js'></script> ";
		
		echo " <TR><TD>";
		echo "<input type='hidden' name='petro' id='petroID' value='".base64_encode( $CL )."'>";
		echo "<input type='hidden' name='oplix' id='oplixID' value='".base64_encode( $BD )."'>";
		echo "<input type='hidden' name='sachu' id='sachuID' value='".base64_encode( $US )."'>";
		echo "<input type='hidden' name='central' id='centralID' value='".base64_encode( DIR_APLICA_CENTRAL )."'>";
		echo "<input type='hidden' name='estilo' id='estiloID' value='".base64_encode( ESTILO )."'>";
		
		echo "<TABLE  BORDER='0' CELLPADDING='0' CELLSPACING='0' WIDTH='100%'>";
		echo " <TR><TD width='10px' class='barra' onclick='Hide()'>";
			echo "&nbsp;</TD><TD><div id='informeID'>";
			echo "<script type='text/javascript'>";
				echo "
						LoadComet(); ";
			echo "</script>";
	
			echo "</div>";
			echo " </TD></TR>";	
		echo "</TABLE>";
		
		echo " </TD></TR>";	
	}

	function Datos()
	{
		$datos_usuario = $this -> usuario -> retornar();
		$formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");
		$listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> aplica,$this -> conexion);
		$listado_prin  -> Encabezado($_REQUEST[despac],$formulario,$datos_usuario,0,"Despachos en Ruta");
		$listado_prin  -> PlanDeRuta($_REQUEST[despac],$formulario,0);
		
		$formulario -> nueva_tabla();
		$formulario -> oculto("despac",$_REQUEST[despac],0);
		$formulario -> oculto("opcion",$_REQUEST[opcion],0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
		
		$formulario -> cerrar();
	}
}

$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

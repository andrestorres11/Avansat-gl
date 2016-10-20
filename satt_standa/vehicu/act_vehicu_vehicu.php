<?php
ini_set("memory_limit", "1024M");

class Act_vehicu_vehicu
{
 var $conexion,
 	 $cod_aplica,
     $usuario;
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
  switch($_REQUEST[opcion])
   {
    case "1":
      $this -> Resultado();
      break;
    case "2":
      $this -> Datos();
      break;
    case "3":
      $this -> Actualizar();
     break;
    default:
     $this -> Buscar();
     break;
  }//FIN SWITCH
 }//FIN FUNCION PRINCIPAL

function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $fechaini=new fecha();
   $fechafin=new fecha();

   $inicio[0][0]='0';
   $inicio[0][1]='-';

   $query = "SELECT a.cod_marcax, a.nom_marcax
               FROM ".BASE_DATOS.".tab_genera_marcas a,
            		".BASE_DATOS.".tab_vehicu_vehicu b
      		  WHERE b.cod_marcax = a.cod_marcax ";

   if($datos_usuario["cod_perfil"] == "")
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }
   else
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }

   $query = $query." GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $marcax = $consulta -> ret_matriz();
   $marcax = array_merge($inicio,$marcax);

   $query = "SELECT a.cod_colorx, a.nom_colorx
       		   FROM ".BASE_DATOS.".tab_vehige_colore a,
            		".BASE_DATOS.".tab_vehicu_vehicu b
      		  WHERE b.cod_colorx = a.cod_colorx
	";

   if($datos_usuario["cod_perfil"] == "")
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }
   else
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }

   $query = $query." GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $colorx = $consulta -> ret_matriz();
   $colorx = array_merge($inicio,$colorx);

   $query = "SELECT a.num_config, a.nom_config
       FROM ".BASE_DATOS.".tab_vehige_config a,
            ".BASE_DATOS.".tab_vehicu_vehicu b
      WHERE b.num_config = a.num_config
	";

   if($datos_usuario["cod_perfil"] == "")
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }
   else
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }

   $query = $query." GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $config = $consulta -> ret_matriz();
   $config = array_merge($inicio,$config);

   $query = "SELECT a.cod_carroc,a.nom_carroc
       		   FROM ".BASE_DATOS.".tab_vehige_carroc a,
            		".BASE_DATOS.".tab_vehicu_vehicu b
      		  WHERE b.cod_carroc = a.cod_carroc
	";

   if($datos_usuario["cod_perfil"] == "")
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }
   else
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND b.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }

   $query = $query." GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $carroc = $consulta -> ret_matriz();
   $carroc = array_merge($inicio,$carroc);

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/vehicu.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","BUSCAR VEHICULOS","form_list");

   $formulario -> linea("Seleccione los Valores de Busqueda.",1,"t2");

   if($datos_usuario["cod_perfil"] == "")
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   else
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

   if($filtro -> listar($this -> conexion))
   {
     $datos_filtro = $filtro -> retornar();
     $formulario -> oculto("transp",$datos_filtro[clv_filtro],0);
   }
   else
   {
   	$query = "SELECT a.cod_tercer,a.abr_tercer
   			    FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			         ".BASE_DATOS.".tab_tercer_activi b,
   			         ".BASE_DATOS.".tab_transp_vehicu c
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			   		 a.cod_tercer = c.cod_transp AND
   			         b.cod_activi = ".COD_FILTRO_EMPTRA."
   			         GROUP BY 1 ORDER BY 2
   			 ";

    $consulta = new Consulta($query, $this -> conexion);
    $transpor = $consulta -> ret_matriz();
    $transpor = array_merge($inicio,$transpor);

    if($_REQUEST[transp])
    {
     $query = "SELECT a.cod_tercer,a.abr_tercer
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			          ".BASE_DATOS.".tab_tercer_activi b
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          b.cod_activi = ".COD_FILTRO_EMPTRA." AND
   			          a.cod_tercer = '".$_REQUEST[transp]."'
   			          ORDER BY 2
   			  ";

     $consulta = new Consulta($query, $this -> conexion);
     $transp_a = $consulta -> ret_matriz();
     $transpor = array_merge($transp_a,$transpor);
    }

    $formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp",$transpor,1);
   }

   $formulario -> nueva_tabla();
   $formulario -> lista ("Marca","marcax",$marcax,1);
   $formulario -> lista ("Carrocer&iacute;a","carroc",$carroc,1);
   $formulario -> lista ("Color","colorx",$colorx,1);
   $formulario -> lista ("Configuraci&oacute;n","config",$config,1);
   $formulario -> caja ("Modelo","por_modelo\" onclick='if(form_list.por_modelo.checked == false){form_list.mod1.disabled = true; form_list.mod2.disabled = true;} else {form_list.mod1.disabled = false; form_list.mod2.disabled = false;}'","1",0,1);
   $formulario -> texto ("Entre","text","mod1\"'",1,6,6,"","");
   $formulario -> texto ("Y","text","mod2",1,6,6,"","");

   $formulario -> nueva_tabla();
   $formulario -> linea("Inserte un texto para iniciar la B&uacute;squeda",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> texto ("Placa","text","placa",1,6,6,"","");

   $formulario -> nueva_tabla();
    $formulario -> caja ("Filtrar por Fecha S/N:","por_fecha","1",0,0);

    $formulario -> nueva_tabla();
    $formulario -> linea("Fecha Inicial",0);
    $fechaini -> pedir_fecha("ano","mes","dia");
    $formulario -> linea("",1);
    $formulario -> linea("Fecha Final",0);
    $fechafin-> pedir_fecha("ano2","mes2","dia2");
    $formulario -> linea("",1);
    $formulario -> nueva_tabla();



   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Buscar","if(form_list.por_modelo.checked){if(form_list.mod1.value == '' || form_list.mod2.value == '')alert('Por favor Ingrese el Rango de los Modelos a Filtrar.'); else form_list.submit()}else if(form_list.mod1.value != '' || form_list.mod2.value != '')alert('Por Favor Activa La Casilla De Modelo si Desea Filtrar un Rango de Modelos.'); else form_list.submit();",0);
   $formulario -> cerrar();
 }//FIN FUNCION
	
	function Resultado()
	{
		$datos_usuario = $this -> usuario -> retornar();
		$fec_actual = date("Y-m-d");
		$fecha1 = $_REQUEST[ano]."-".$_REQUEST[mes]."-".$_REQUEST[dia]." 00:00:00";
		$fecha2 = $_REQUEST[ano2]."-".$_REQUEST[mes2]."-".$_REQUEST[dia2]." 23:59:59";
		
		$query = "SELECT a.num_placax, g.abr_tercer, g.num_telef1, 
						 h.abr_tercer, h.num_telmov, b.nom_marcax, 
						 c.nom_lineax, d.nom_colorx, e.nom_carroc, 
						 a.ano_modelo, a.ind_estado, a.fec_creaci 
				  FROM ".BASE_DATOS.".tab_vehicu_vehicu a
							LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b 
							ON a.cod_marcax = b.cod_marcax
							LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc e
							ON a.cod_carroc = e.cod_carroc
							LEFT JOIN ".BASE_DATOS.".tab_vehige_colore d 
							ON a.cod_colorx = d.cod_colorx
							LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c 
							ON a.cod_marcax = c.cod_marcax AND a.cod_lineax = c.cod_lineax 
							LEFT JOIN ".BASE_DATOS.".tab_vehige_config i 
							ON a.num_config = i.num_config
							LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer g 
							ON a.cod_tenedo = g.cod_tercer
							LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer h 
							ON a.cod_conduc = h.cod_tercer 
							LEFT JOIN ".BASE_DATOS.".tab_transp_vehicu j 
							ON a.num_placax = j.num_placax
				  WHERE a.num_placax LIKE '%".$_REQUEST[placa]."%'";

	   if($_REQUEST[transp])
        $query = $query." AND j.cod_transp = '".$_REQUEST[transp]."'";
       if($_REQUEST[por_fecha])
        $query = $query." AND a.fec_creaci  BETWEEN '".$fecha1."' AND '".$fecha2."'";
       if($_REQUEST[marcax])
          $query = $query." AND a.cod_marcax = '".$_REQUEST[marcax]."'";
       if($_REQUEST[carroc])
          $query = $query." AND a.cod_carroc = '".$_REQUEST[carroc]."'";
       if($_REQUEST[colorx])
          $query = $query." AND a.cod_colorx = '".$_REQUEST[colorx]."'";
       if($_REQUEST[config])
          $query = $query." AND a.num_config = '".$_REQUEST[config]."'";
       if($_REQUEST[por_modelo])
          $query = $query." AND a.ano_modelo >= ".$_REQUEST[mod1]." AND a.ano_modelo <= ".$_REQUEST[mod2];

   if($datos_usuario["cod_perfil"] == "")
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }
   else
   {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_conduc = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE PROPIETARIO
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_propie = '$datos_filtro[clv_filtro]' ";
      	}
	//PARA EL FILTRO DE POSEEDOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      	}
   }

  $query .= " GROUP BY 1 ORDER BY 1";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

  $query = "SELECT cod_perfil
              FROM ".BASE_DATOS.".tab_autori_perfil
             WHERE cod_perfil = '".$this->usuario->cod_perfil."' AND
		    	   cod_autori = '4'";

  $consulta = new Consulta($query, $this -> conexion);
  $parfecsal = $consulta -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE VEHICULOS","form_item");

   $formulario -> linea("Se Encontraron ".sizeof($matriz)." Registros",1,"t2");
   if($_REQUEST[por_fecha])
   $formulario -> linea("Fecha Inicial $fecha1 Fecha Final $fecha2",0);

   $formulario -> nueva_tabla();
  if(sizeof($matriz) > 0)
  {
     $formulario -> linea("Placa",0,"t");
     $formulario -> linea("Poseedor",0,"t");
     $formulario -> linea("Tel&eacute;fono",0,"t");
     $formulario -> linea("Conductor",0,"t");
     $formulario -> linea("Celular",0,"t");
     $formulario -> linea("Marca",0,"t");
     $formulario -> linea("L&iacute;nea",0,"t");
     $formulario -> linea("Color",0,"t");
     $formulario -> linea("Carrocer&iacute;nea",0,"t");
     $formulario -> linea("Modelo",0,"t");
     $formulario -> linea("Remolque",0,"t");
     $formulario -> linea("Estado",1,"t");

   for($i=0;$i<sizeof($matriz);$i++)
   {
     $query = "SELECT a.num_trayle
		         FROM ".BASE_DATOS.".tab_trayle_placas a
		 	    WHERE a.num_placax = '".$matriz[$i][0]."' AND
		       		  a.fec_asigna = (SELECT MAX(b.fec_asigna)
									    FROM ".BASE_DATOS.".tab_trayle_placas b
			 					       WHERE a.num_placax = b.num_placax
				      			     )
		       		  GROUP BY 1
	       ";

     $consulta = new Consulta($query, $this -> conexion);
     $remolque = $consulta -> ret_matriz();

     if($matriz[$i][10] != COD_ESTADO_ACTIVO)
      $estilo = "ie";
     else
     {
      $fechaadic = date("Y-m-d", strtotime("".$fec_actual." -".DIAS_VALID_NUEVOS." day"))." 00:00:00";

      if(($matriz[$i][11] >= $fechaadic && $matriz[$i][11] <= $fec_actual." 23:59:59") && $parfecsal)
       $estilo = "in";
      else
       $estilo = "i";
     }

     if($matriz[$i][10] == COD_ESTADO_ACTIVO)
      $estado = "Activo";
     else if($matriz[$i][10] == COD_ESTADO_INACTI)
      $estado = "Inactivo";
     else if($matriz[$i][10] == COD_ESTADO_PENDIE)
      $estado = "Pendiente";

     $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&placa=".$matriz[$i][0]."&opcion=2&transp=".$_REQUEST[transp]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

     $formulario -> linea($matriz[$i][0],0,$estilo);
     $formulario -> linea($matriz[$i][1],0,$estilo);
     $formulario -> linea($matriz[$i][2],0,$estilo);
     $formulario -> linea($matriz[$i][3],0,$estilo);
     $formulario -> linea($matriz[$i][4],0,$estilo);
     $formulario -> linea($matriz[$i][5],0,$estilo);
     $formulario -> linea($matriz[$i][6],0,$estilo);
     $formulario -> linea($matriz[$i][7],0,$estilo);
     $formulario -> linea($matriz[$i][8],0,$estilo);
     $formulario -> linea($matriz[$i][9],0,$estilo);
     $formulario -> linea($remolque[0][0],0,$estilo);
     $formulario -> linea($estado,1,$estilo);
   }//fin for
  }//fin if

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("transp",$_REQUEST[transp],0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Volver","form_item.opcion.value='0';form_item.submit()",1);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   if(!isset($_REQUEST[marca]))
   {
      //datos
      $query = "SELECT a.num_placax,a.cod_marcax,a.cod_lineax,
                       a.ano_modelo,a.ano_repote,a.cod_tipveh,
                       a.cod_carroc,a.num_motorx,a.num_seriex,
                       a.val_pesove,a.val_capaci,a.num_config,
                       a.fec_revmec,a.nom_vincul,a.fec_vigvin,
                       a.num_agases,a.fec_vengas,a.reg_nalcar,
                       a.num_tarope,a.cod_califi,a.ind_chelis,
                       a.num_poliza,a.nom_asesoa,a.fec_vigfin,
                       a.num_polirc,a.cod_aseprc,a.fec_venprc,
                       a.cod_tenedo,a.cod_propie,a.cod_conduc,
                       a.cod_colorx,a.num_tarpro,a.obs_vehicu,
                       a.usr_creaci, a.fec_creaci,a.usr_modifi, a.fec_modifi
                  FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                 WHERE a.num_placax = '$_REQUEST[placa]'
              ORDER BY 2";
      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();

      $_REQUEST[marca]=$matriz[0][1];
      $_REQUEST[linea]=$matriz[0][2];
      $_REQUEST[modelo]=$matriz[0][3];
      $_REQUEST[repote]=$matriz[0][4];
      $_REQUEST[tipveh]=$matriz[0][5];
      $_REQUEST[carroc]=$matriz[0][6];
      $_REQUEST[motor]=$matriz[0][7];
      $_REQUEST[serie]=$matriz[0][8];
      $_REQUEST[pesva]=$matriz[0][9];
      $_REQUEST[capaci]=$matriz[0][10];
      $_REQUEST[config]=$matriz[0][11];
      $_REQUEST[revmec]=$matriz[0][12];
      $_REQUEST[vincula]=$matriz[0][13];
      $_REQUEST[vigvinv]=$matriz[0][14];
      $_REQUEST[gases]=$matriz[0][15];
      $_REQUEST[viggas]=$matriz[0][16];
      $_REQUEST[regnal]=$matriz[0][17];
      $_REQUEST[tarope]=$matriz[0][18];
      $_REQUEST[califi]=$matriz[0][19];
      $_REQUEST[chelis]=$matriz[0][20];
      $_REQUEST[numsoa]=$matriz[0][21];
      $_REQUEST[asesoa]=$matriz[0][22];
      $_REQUEST[vigsoa]=$matriz[0][23];
      $_REQUEST[polirc]=$matriz[0][24];
      $_REQUEST[aseprc]=$matriz[0][25];
      $_REQUEST[vigprc]=$matriz[0][26];
      $_REQUEST[tenedo]=$matriz[0][27];
      $_REQUEST[propie]=$matriz[0][28];
      $_REQUEST[conduc]=$matriz[0][29];
      $_REQUEST[colorx]=$matriz[0][30];
      $_REQUEST[tarpro]=$matriz[0][31];
      $_REQUEST[obs]=$matriz[0][32];
    }

   $inicio[0][0]='0';
   $inicio[0][1]='-';


   echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/dinamic_list.css" type="text/css">';
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/vehiculos.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fecha.js\"></script>\n";

   $formulario = new Formulario ("index.php","post\" enctype=\"multipart/form-data","INFORMACION DEL VEHICULO","form_actua");

   $formulario -> nueva_tabla();
   $formulario -> linea("Creado por",0,"t");
   $formulario -> linea($matriz[0][33],0,"i");
   $formulario -> linea("Fecha",0,"t");
   $formulario -> linea($matriz[0][34],1,"i");
   $formulario -> linea("Actualizado por",0,"t");
   $formulario -> linea($matriz[0][35],0,"i");
   $formulario -> linea("Fecha",0,"t");
   $formulario -> linea($matriz[0][36],1,"i");


   //------------------------------
   if( !$_REQUEST['transp'] )
   {
      //--------------------------
      $query = "SELECT a.cod_tercer,a.abr_tercer
                  FROM ".BASE_DATOS.".tab_tercer_tercer a,
                       ".BASE_DATOS.".tab_tercer_emptra b
                 WHERE a.cod_tercer = b.cod_tercer AND
                       a.cod_estado != '".COD_ESTADO_INACTI."'
              ORDER BY 2
                ";
      $consulta = new Consulta($query, $this -> conexion);
      $listtran = $consulta -> ret_matriz();
      //--------------------------

      //--------------------------
      $SQL = "SELECT  c.cod_tercer, c.abr_tercer
                FROM  ".BASE_DATOS.".tab_transp_vehicu a JOIN
                      ".BASE_DATOS.".tab_tercer_emptra b ON a.cod_transp = b.cod_tercer JOIN 
                      ".BASE_DATOS.".tab_tercer_tercer c ON a.cod_transp = c.cod_tercer
               WHERE  a.num_placax = '" . $_REQUEST['placa'] . "'";

      $consulta = new Consulta( $SQL, $this -> conexion );
      $trareg = $consulta -> ret_matriz();  // Transportadora Regsitrada para la Placa
      //--------------------------

      //--------------------------
      if( count( $trareg ) == 0 )
        $listtran = array_merge( $inicio, $listtran );
      else
      {
        //$listtran = array_merge( $trareg, $inicio, $listtran );
        if( !$_REQUEST['transpor'] )
        $_REQUEST['transpor'] = $trareg[0][0];
      }
      //--------------------------

      if( $_REQUEST['transpor'] )
      {
        $query = "SELECT  a.cod_tercer,a.abr_tercer
                     FROM ".BASE_DATOS.".tab_tercer_tercer a,
                          ".BASE_DATOS.".tab_tercer_emptra b
                    WHERE a.cod_tercer = b.cod_tercer AND
                          a.cod_estado != '".COD_ESTADO_INACTI."' AND
                          a.cod_tercer = '".$_REQUEST['transpor']."'
                    ";

        $consulta = new Consulta($query, $this -> conexion);
        $listtran_a = $consulta -> ret_matriz();

        $listtran = array_merge( $listtran_a, $listtran );
      }
    }
    else
    {
      $_REQUEST['transpor'] = $_REQUEST['transp'];
      $formulario -> oculto("transpor",$_REQUEST['transpor'],0);

      //$formulario -> linea("$_REQUEST[transpor]",0,"t2");
    }
   //------------------------------



   $query = "SELECT b.num_trayle,b.num_trayle
               FROM ".BASE_DATOS.".tab_trayle_placas b
              WHERE b.num_placax = '$_REQUEST[placa]' AND
                    b.ind_actual = 'S' AND
                    b.num_noveda = ( SELECT Max(d.num_noveda)
                                                     FROM ".BASE_DATOS.".tab_trayle_placas d
                                                                         WHERE d.num_placax = b.num_placax )
                                   ORDER BY 2";
   $consulta = new Consulta($query, $this -> conexion);
   $trayle_a = $consulta -> ret_matriz();

   $query = "SELECT a.num_trayle,a.num_trayle
               FROM ".BASE_DATOS.".tab_vehige_trayle a
           ORDER BY 2";
   $consulta = new Consulta($query, $this -> conexion);
   $traylers= $consulta -> ret_matriz();
   $traylers = array_merge($trayle_a,$inicio,$traylers);

   $query = "SELECT cod_tipveh,nom_tipveh
               FROM ".BASE_DATOS.".tab_genera_tipveh
              WHERE cod_tipveh = '".$_REQUEST[tipveh]."' ";

   $consulta = new Consulta($query, $this -> conexion);
   $tipveh_a = $consulta -> ret_matriz();

   $query = "SELECT cod_tipveh,nom_tipveh
               FROM ".BASE_DATOS.".tab_genera_tipveh
               ORDER BY 2";
   $consulta = new Consulta($query, $this -> conexion);
   $tipveh = $consulta -> ret_matriz();
   $tipveh = array_merge($tipveh_a,$inicio,$tipveh);

   //trae la marca anterior
   $query = "SELECT cod_marcax,nom_marcax
               FROM ".BASE_DATOS.".tab_genera_marcas
              WHERE cod_marcax = '$_REQUEST[marca]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $marca_a = $consulta -> ret_matriz();

   //trae las marcas
   $query = "SELECT cod_marcax,nom_marcax
               FROM ".BASE_DATOS.".tab_genera_marcas
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $marcas = $consulta -> ret_matriz();
   $marcas = array_merge($marca_a,$inicio,$marcas);

   //trae la linea anterior
   $query = "SELECT cod_lineax,nom_lineax
               FROM ".BASE_DATOS.".tab_vehige_lineas
              WHERE cod_lineax = '$_REQUEST[linea]'
                AND cod_marcax = '$_REQUEST[marca]'";
   $consulta = new Consulta($query, $this -> conexion);
   $linea_a = $consulta -> ret_matriz();

   //trae las lineas de la marca escogida
   $query = "SELECT cod_lineax,nom_lineax
               FROM ".BASE_DATOS.".tab_vehige_lineas
              WHERE cod_marcax = '$_REQUEST[marca]'
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $lineas = $consulta -> ret_matriz();
   $linea = array_merge($linea_a,$inicio,$lineas);

   //trae el color anterior
   $query = "SELECT cod_colorx,nom_colorx
               FROM ".BASE_DATOS.".tab_vehige_colore
           WHERE cod_colorx = '$_REQUEST[colorx]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $color_a = $consulta -> ret_matriz();

   //trae los colores de vehiculos
   $query = "SELECT cod_colorx,nom_colorx
               FROM ".BASE_DATOS.".tab_vehige_colore
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $colore = $consulta -> ret_matriz();
   $colorx = array_merge($color_a,$inicio,$colore);

   $query = "SELECT num_config,num_config
               FROM ".BASE_DATOS.".tab_vehige_config
               WHERE ind_estado = '1'
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $config = $consulta -> ret_matriz();

   $query = "SELECT num_config,num_config
               FROM ".BASE_DATOS.".tab_vehige_config
           WHERE num_config = '$_REQUEST[config]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $config_a= $consulta -> ret_matriz();
   $config = array_merge($config_a,$inicio,$config);



   //trae la carroceria anterior
   $query = "SELECT cod_carroc,nom_carroc
               FROM ".BASE_DATOS.".tab_vehige_carroc
           WHERE cod_carroc = '$_REQUEST[carroc]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $carroc_a = $consulta -> ret_matriz();

   //trae las carrocerias de vehiculos
   $query = "SELECT cod_carroc,nom_carroc
               FROM ".BASE_DATOS.".tab_vehige_carroc
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $carrocerias = $consulta -> ret_matriz();
   $carrocerias = array_merge($carroc_a,$inicio,$carrocerias);

   //------------------
    $SQL_CONDUC = "SELECT a.cod_tercer, CONCAT(a.abr_tercer,' - ',a.cod_tercer),
                          a.abr_tercer, a.num_telef1, a.dir_domici
                     FROM ".BASE_DATOS.".tab_tercer_tercer a,
                          ".BASE_DATOS.".tab_tercer_activi b,
                          ".BASE_DATOS.".tab_tercer_conduc c,
                          ".BASE_DATOS.".tab_transp_tercer d
                    WHERE a.cod_tercer = b.cod_tercer AND
                          a.cod_tercer = d.cod_tercer AND
                          d.cod_transp = '$_REQUEST[transpor]' AND
                          a.cod_tercer = c.cod_tercer AND
                          a.cod_estado = '1' AND
                          b.cod_activi = ".COD_FILTRO_CONDUC."
                 GROUP BY 1,2 ";

    $query = $SQL_CONDUC . " ORDER BY 2 ";
   //------------------

   $consulta = new Consulta($query, $this -> conexion);
   $conductores = $consulta -> ret_matriz();

    $query = "SELECT a.cod_tercer,CONCAT(a.abr_tercer,' - ',a.cod_tercer)
               FROM ".BASE_DATOS.".tab_tercer_tercer a
              WHERE a.cod_tercer = '$_REQUEST[conduc]'";
   $consulta = new Consulta($query, $this -> conexion);
   $conduc_a = $consulta -> ret_matriz();
   $conductores = array_merge($conduc_a,$inicio,$conductores);



   $query = "SELECT a.cod_tercer,CONCAT(a.abr_tercer,' - ',a.cod_tercer)
               FROM ".BASE_DATOS.".tab_tercer_tercer a
              WHERE a.cod_tercer = '$_REQUEST[propie]'";
   $consulta = new Consulta($query, $this -> conexion);
   $propie_a = $consulta -> ret_matriz();

   //------------------
   $query = "SELECT a.cod_tercer,CONCAT(a.abr_tercer,' - ',a.cod_tercer)
               FROM ".BASE_DATOS.".tab_tercer_tercer a
              WHERE a.cod_tercer = '$_REQUEST[tenedo]'";
   $consulta = new Consulta($query, $this -> conexion);
   $tenedo_a = $consulta -> ret_matriz();
   //------------------

   //------------------
   $SQL_PROPIE = "SELECT  a.cod_tercer, CONCAT(a.abr_tercer,' - ',a.cod_tercer),
                          a.abr_tercer, a.num_telef1, a.dir_domici
                     FROM ".BASE_DATOS.".tab_tercer_tercer a,
                          ".BASE_DATOS.".tab_tercer_activi b,
                          ".BASE_DATOS.".tab_transp_tercer d
                    WHERE a.cod_tercer = b.cod_tercer AND
                          a.cod_tercer = d.cod_tercer AND
                          d.cod_transp = '$_REQUEST[transpor]' AND
                          a.cod_estado = '1' AND
                          b.cod_activi = ".COD_FILTRO_PROPIE."
                 GROUP BY 1,2";
    
   $query = $SQL_PROPIE . ' ORDER BY 2';
   $consulta = new Consulta($query, $this -> conexion);
   $terceros = $consulta -> ret_matriz();
   //------------------

   $propie = array_merge($propie_a,$inicio,$terceros);

   //------------------
   $SQL_TENEDO = "SELECT  a.cod_tercer, CONCAT(a.abr_tercer,' - ',a.cod_tercer),
                          a.abr_tercer, a.num_telef1, a.dir_domici
                     FROM ".BASE_DATOS.".tab_tercer_tercer a,
                          ".BASE_DATOS.".tab_tercer_activi b,
                          ".BASE_DATOS.".tab_transp_tercer d
                    WHERE a.cod_tercer = b.cod_tercer AND
                          a.cod_tercer = d.cod_tercer AND
                          d.cod_transp = '$_REQUEST[transpor]' AND
                          a.cod_estado = '1' AND
                          b.cod_activi = ".COD_FILTRO_POSEED."
                 GROUP BY 1,2";
   
   $query = $SQL_TENEDO . ' ORDER BY 2';
   $consulta = new Consulta($query, $this -> conexion);
   $tenedo = $consulta -> ret_matriz();
   //------------------

   $tenedo = array_merge($tenedo_a,$inicio,$tenedo);

   //las calificacion anterior
   $query = "SELECT a.cod_califi,a.nom_califi
               FROM ".BASE_DATOS.".tab_genera_califi a
              WHERE a.cod_califi = '$_REQUEST[califi]'";
   $consulta = new Consulta($query, $this -> conexion);
   $califi_a = $consulta -> ret_matriz();


    //las calificacion
   $query = "SELECT a.cod_califi,a.nom_califi
               FROM ".BASE_DATOS.".tab_genera_califi a";
   $consulta = new Consulta($query, $this -> conexion);
   $califi = $consulta -> ret_matriz();

   //----------------------------------------------------
   //$asesoa = array_merge($asegra_a,$inicio,$asegra);
   
    if( isset( $asegra_a ) && isset( $asegra ) )
      $asesoa = array_merge( $asegra_a, $inicio, $asegra );
    elseif( isset( $asegra ) )
      $asesoa = array_merge( $inicio, $asegra );
    else
      $asesoa = $inicio;
   //----------------------------------------------------

   //----------------------------------------------------
   //$aseprc = array_merge($aseprc_a,$inicio,$asegra);

    if( isset( $aseprc_a ) && isset( $asegra ) )
      $aseprc = array_merge( $aseprc_a, $inicio, $asegra );
    elseif( isset( $asegra ) )
      $aseprc = array_merge( $inicio, $asegra );
    else
      $aseprc = $inicio;
   //----------------------------------------------------

   $califi = array_merge($califi_a,$inicio,$califi);



   $formulario -> nueva_tabla();
   $formulario -> linea("Datos B&aacute;sicos del Veh&iacute;culo",1,"t2");

    if( !$_REQUEST['transp'] )
    {
      $formulario -> nueva_tabla();
      $formulario -> lista("Transportadora","transpor\" onChange=\"form_actua.submit()",$listtran,1);
    }

   $formulario -> nueva_tabla();
   $formulario -> texto("Placa(AAA000):","text","placa\" readonly ",0,6,6,"","$_REQUEST[placa]");
   $formulario -> lista("Marca:","marca\" onBlur=\"form_actua.submit() ",$marcas,1,1);
   $formulario -> lista("L&iacute;nea:","linea\" id=\"lineaID",$linea,0,1);
   $formulario -> texto("Modelo:","text","modelo\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,4,4,"","$_REQUEST[modelo]","","",NULL,1);
   $formulario -> texto("Repotenciado a:","text","repote\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,4,4,"","$_REQUEST[repote]");
   $formulario -> lista("Color:", "colorx",$colorx,1,1);
   $formulario -> lista("Tipo Vinculaci&oacute;n:", "tipveh",$tipveh,0,1);
   $formulario -> lista("Tip. Carrocer&iacute;a:", "carroc",$carrocerias,1,1);
   $formulario -> texto("N&uacute;mero Motor:","text","motor",0,15,25,"","$_REQUEST[motor]","","",NULL,1);
   $formulario -> texto("N&uacute;mero Serie:","text","serie",1,15,25,"","$_REQUEST[serie]","","",NULL,1);
   $formulario -> texto("Peso Vacio(Tn):","text","pesva\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,6,6,"","$_REQUEST[pesva]","","",NULL,1);
   $formulario -> texto("Capacidad(Tn):","text","capaci\" onChange=\"form_vehiculos.submit()\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,4,"","$_REQUEST[capaci]","","",NULL,1);
   $formulario -> lista("Configuraci&oacute;n:","config",$config,1,1);
   $formulario -> texto("Vinculado a:","text","vincula",0,20,50,"","$_REQUEST[vincula]");
   $formulario -> fecha_calendar("Fecha Vencimiento","vigvinv","form_actua",$_REQUEST[vigvinv],"yyyy-mm-dd",1);
   $formulario -> texto("Revisi&oacute;n Tecno Mecanica:","text","gases",0,20,50,"","$_REQUEST[gases]");
   $formulario -> fecha_calendar("Fecha Vencimiento","revmec","form_actua",$_REQUEST[revmec],"yyyy-mm-dd",1);
   $formulario -> texto("Reg.Nal.Carga:","text","regnal",0,10,10,"","$_REQUEST[regnal]","","",NULL,1);
   $formulario -> texto("Lic. Transito:","text","tarpro",1,11,11,"","$_REQUEST[tarpro]");
   $formulario -> texto("Tarjeta de Operaci&oacute;n:","text","tarope",0,10,10,"","$_REQUEST[tarope]");
   $formulario -> lista("Calificaci&oacute;n:","califi",$califi,1);
   $formulario -> caja("Check List Express","chelis",1,"$_REQUEST[chelis]",0);

  $formulario -> nueva_tabla();
  $formulario -> linea("Seguros",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> texto("SOAT","text","numsoa",0,10,30,"","$_REQUEST[numsoa]","","",NULL,1);
  $formulario -> texto("Aseguradora","text","asesoa",0,30,30,"","$_REQUEST[asesoa]","","",NULL,1);
  $formulario -> fecha_calendar("Fecha Vencimiento","vigsoa","form_actua",$_REQUEST[vigsoa],"yyyy-mm-dd",1,0,1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Selecci&oacute;n del Remolque",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> lista("Remolque:","trayler",$traylers, 1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Datos Terceros",0,"t2");

   $formulario -> nueva_tabla();
   /*
   $formulario -> lista("Propietario:", "propie", $propie, 1,1);
   $formulario -> lista("Poseedor:", "tenedo", $tenedo, 1,1);
   $formulario -> lista("Conductor:", "conduc", $conductores, 1,1);
  */


  //-----------------------------------
  // PROPIETARIO
  $query = "SELECT  a.cod_tercer, a.abr_tercer, IF(a.cod_tipdoc = 'N', 'NIT', 'CC'), a.cod_estado
              FROM  ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b,
                    ".BASE_DATOS.".tab_transp_tercer d
              WHERE a.cod_tercer = b.cod_tercer AND
                    a.cod_tercer = d.cod_tercer AND
                    d.cod_transp = '$_REQUEST[transpor]' AND
                    b.cod_activi = ".COD_FILTRO_PROPIE." AND
                    a.cod_estado = '1' AND 
                    a.cod_tercer = '$_REQUEST[propie]' ";

  $consulta = new Consulta( $query, $this -> conexion );
  $propie_a = $consulta -> ret_matriz();
  //-----------------------------------
  if( count( $propie_a ) > 0 )
    $mDATA['propie'] = $_REQUEST['propie'];
  else
    $mDATA['propie'] = '';
  //-----------------------------------
  echo '<tr>
            <td align="right" class="celda_titulo">
              Propietario:
            </td>
            
            <td class="celda_titulo">
              <input type="text" name="propie" id="propie" value="' . $mDATA['propie'] . '" size="10" maxlength="20" onkeyup="BlurNumeric(this)"  onBlur="form_actua.submit()">
              <input type="text" value="' . $propie_a[0]['abr_tercer'] . '" size="30" onchange="form_actua.submit()" id="nomprop" name="nomprop" readonly>&nbsp;
              <input type="button" title="Buscar" class="popupButton" onclick="ListPropie();">
              <input type="hidden" name="consul_propie" id="consul_propieID" value="' . base64_encode( $SQL_PROPIE ) . '">
            </td>

            <td align="right" class="celda_titulo">&nbsp;</td>
        </tr>';  
  //-----------------------------------


  //-----------------------------------
  // TENEDOR
  $query = "SELECT  a.cod_tercer, a.abr_tercer, IF(a.cod_tipdoc = 'N', 'NIT', 'CC'), a.cod_estado
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_activi b,
                    ".BASE_DATOS.".tab_transp_tercer d
              WHERE a.cod_tercer = b.cod_tercer AND
                    a.cod_tercer = d.cod_tercer AND
                    d.cod_transp = '$_REQUEST[transpor]' AND
                    b.cod_activi = ".COD_FILTRO_POSEED." AND
                    a.cod_estado = '1' AND 
                    a.cod_tercer = '$_REQUEST[tenedo]' ";

  $consulta = new Consulta( $query, $this -> conexion );
  $tenedo_a = $consulta -> ret_matriz();
  //-----------------------------------
  if( count( $tenedo_a ) > 0 )
    $mDATA['tenedo'] = $_REQUEST['tenedo'];
  else
    $mDATA['tenedo'] = '';
  //-----------------------------------
  echo '<tr>
            <td align="right" class="celda_titulo">
              Poseedor:
            </td>
            
            <td class="celda_titulo">
              <input type="text" name="tenedo" id="tenedo" value="'.$mDATA['tenedo'].'" size="10" maxlength="20" onkeyup="BlurNumeric(this)"  onBlur="form_actua.submit()">
              <input type="text" value="'.$tenedo_a[0][abr_tercer].'" size="30" onchange="form_actua.submit()" id="nomtene" name="nomtene" readonly>&nbsp;
              <input type="button" title="Buscar" class="popupButton" onclick="ListTenedo();">
              <input type="hidden" name="consul_tenedo" id="consul_tenedoID" value="' . base64_encode( $SQL_TENEDO ) . '">
            </td>

            <td align="right" class="celda_titulo">&nbsp;</td>
        </tr>';     
  //-----------------------------------


  //-----------------------------------
  // CONDUCTOR
  $query = "SELECT  a.cod_tercer, a.abr_tercer, CONCAT(a.abr_tercer,' - ',a.cod_tercer)
            FROM  ".BASE_DATOS.".tab_tercer_tercer a,
                  ".BASE_DATOS.".tab_tercer_activi b,
                  ".BASE_DATOS.".tab_tercer_conduc c,
                  ".BASE_DATOS.".tab_transp_tercer d
            WHERE a.cod_tercer = b.cod_tercer AND
                  a.cod_tercer = d.cod_tercer AND
                  d.cod_transp = '$_REQUEST[transpor]' AND
                  a.cod_tercer = c.cod_tercer AND
                  a.cod_estado = '1' AND 
                  a.cod_tercer = '$_REQUEST[conduc]' ";

  $consulta = new Consulta( $query, $this -> conexion );
  $conduc_a = $consulta -> ret_matriz();
  //-----------------------------------
  if( count( $conduc_a ) > 0 )
    $mDATA['conduc'] = $_REQUEST['conduc'];
  else
    $mDATA['conduc'] = '';
  //-----------------------------------
   echo '<tr>
            <td align="right" class="celda_titulo">
              Conductor:
            </td>
            
            <td class="celda_titulo">
              <input type="text" name="conduc" id="conduc" value="'.$mDATA['conduc'].'" size="10" maxlength="20" onkeyup="BlurNumeric(this)"  onBlur="form_actua.submit()">
              <input type="text" value="'.$conduc_a[0][abr_tercer].'" size="30" onchange="form_actua.submit()" id="nomcond" name="nomcond" readonly>&nbsp;
              <input type="button" title="Buscar" class="popupButton" onclick="ListConduc();">
              <input type="hidden" name="consul_conduc" id="consul_conducID" value="' . base64_encode( $SQL_CONDUC ) . '">
            </td>
                  
            <td align="right" class="celda_titulo">&nbsp;</td>
        </tr>';              
  //-----------------------------------



   $formulario -> nueva_tabla();
   $formulario -> linea("Fotos del Veh&iacute;culo",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> archivo("Foto Frente:","f_frente",12,200,'',0);
   $formulario -> archivo("Foto Izquierda:","f_izqui",12,200,'',1);
   $formulario -> archivo("Foto Derecha:","f_derec",12,200,'',0);
   $formulario -> archivo("Foto Posterior:","f_poster",12,200,'',1);
   $formulario -> oculto("MAX_FILE_SIZE", "2000000", 0);

/*
   //Manejo de la Interfaz GPS
    $interf_gps = new Interfaz_GPS();
    $interf_gps -> Interfaz_GPS_envio($_REQUEST[transpor],BASE_DATOS,$usuario,$this -> conexion);

    if($interf_gps -> cant_interf > 0 && $_REQUEST[transpor])
    {
     $formulario -> nueva_tabla();
     echo "<hr>";
     $formulario -> linea("Operadores GPS - Activaci&oacute;n de Veh&iacute; con Operadores",1,"t2");

	 $formulario -> nueva_tabla();

     for($i = 0; $i < $interf_gps -> cant_interf; $i++)
     {
	  if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_PLACAX)
	  {
	   if($interf_gps -> getVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]))
		$activo_v = 1;
	   else
	    $activo_v = 0;

	   $formulario -> caja("Activar Vehiculo con el Operador GPS <b>".$interf_gps -> nom_operad[$i][0]."</b>: ","operador_gps[$i]",$interf_gps -> cod_operad[$i][0],$activo_v,1);
	  }
	  else if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_IDGPSX)
	  {
	   $idgps = $interf_gps -> getIdGPS($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]);

	   if($interf_gps -> getVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]))
		$activo_v = 1;
	   else
	    $activo_v = 0;

	   $formulario -> caja("Activar Veh&iacute;culo con el Operador GPS <b>".$interf_gps -> nom_operad[$i][0]."</b>: ","operador_gps[$i]",$interf_gps -> cod_operad[$i][0],$activo_v,0);
	   $formulario -> texto("ID Dispositivo GPS","text","idgpsx[$i]",1,10,10,"",$idgps);
	  }
     }
    }
*/
   $formulario -> nueva_tabla();
   $formulario -> texto("Observaciones:","textarea","$_REQUEST[obs]",0,50,3,"","$_REQUEST[obs]");
   $formulario -> nueva_tabla();
   $formulario -> oculto("fStandar\" id=\"fStandarID\"",DIR_APLICA_CENTRAL,0);
   $formulario -> oculto("ActualRow\" id=\"ActualRowID\"","0",0);
   $formulario -> oculto("fec_actual",date("Y-m-d H:i:s"),0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("cod_servic\" id=\"cod_servicID",$_REQUEST["cod_servic"],0);
   
   //---------------------------------------------------------------------------------
   //echo "<pre>"; print_r( $this -> usuario ); echo "</pre>";
   $m_perfil = (array) $this -> usuario;
   if( $m_perfil["cod_perfil"] == 8 || $m_perfil["cod_perfil"] == 1 )
    $formulario -> botoni("Actualizar","aceptar_update_sinValidar()",0);
   else
    $formulario -> botoni("Actualizar","aceptar_update()",0);
   //---------------------------------------------------------------------------------

   $formulario -> botoni("Cancelar","form_actua.opcion.value='0';form_actua.submit()",1);
   $formulario -> cerrar();

  //--------------------------
  //Para la carga del Popup
  echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';
  //--------------------------


 }//FIN FUNCION

 function Actualizar()
 {
   $operador_gps = $_REQUEST[operador_gps];
   $codidgps = $_REQUEST[idgpsx];

   if(!$_REQUEST[vigvinv])
    $_REQUEST[vigvinv] = "NULL";
   else
    $_REQUEST[vigvinv] = "'".$_REQUEST[vigvinv]."'";
   if(!$_REQUEST[revmec])
    $_REQUEST[revmec] = "NULL";
   else
    $_REQUEST[revmec] = "'".$_REQUEST[revmec]."'";

   $query = "SELECT a.dir_fotfre,a.dir_fotizq,a.dir_fotder,a.dir_fotpos
                FROM ".BASE_DATOS.".tab_vehicu_vehicu a
               WHERE a.num_placax = '$_REQUEST[placa]'";
   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

 if($_REQUEST[f_frente])
   {
    if(move_uploaded_file($_REQUEST[f_frente],URL_VEHICU."foto1_".$_REQUEST[placa].".jpg"))
     $_REQUEST[f_frente] = "'foto1_".$_REQUEST[placa].".jpg'";
    else
     $_REQUEST[f_frente] = "NULL";
   }
   else if($matriz[0][0])
    $_REQUEST[f_frente] = "'".$matriz[0][0]."'";
   else
    $_REQUEST[f_frente] = "NULL";

   if($_REQUEST[f_izqui])
   {
    if(move_uploaded_file($_REQUEST[f_izqui],URL_VEHICU."foto2_".$_REQUEST[placa].".jpg"))
     $_REQUEST[f_izqui] = "'foto2_".$_REQUEST[placa].".jpg'";
    else
     $_REQUEST[f_izqui] = "NULL";
   }
   else if($matriz[0][1])
    $_REQUEST[f_izqui] = "'".$matriz[0][1]."'";
   else
    $_REQUEST[f_izqui] = "NULL";

   if($_REQUEST[f_derec])
   {
    if(move_uploaded_file($_REQUEST[f_derec],URL_VEHICU."foto3_".$_REQUEST[placa].".jpg"))
     $_REQUEST[f_derec] = "'foto3_".$_REQUEST[placa].".jpg'";
    else
     $_REQUEST[f_derec] = "NULL";
   }
   else if($matriz[0][2])
    $_REQUEST[f_derec] = "'".$matriz[0][2]."'";
   else
    $_REQUEST[f_derec] = "NULL";

   if($_REQUEST[f_poster])
   {
    if(move_uploaded_file($_REQUEST[f_poster],URL_VEHICU."foto4_".$_REQUEST[placa].".jpg"))
     $_REQUEST[f_poster] = "'foto4_".$_REQUEST[placa].".jpg'";
    else
     $_REQUEST[f_poster] = "NULL";
   }
   else if($matriz[0][3])
    $_REQUEST[f_poster] = "'".$matriz[0][3]."'";
   else
    $_REQUEST[f_poster] = "NULL";

  if(!$_REQUEST[califi])
   $_REQUEST[califi] = "NULL";
  else
   $_REQUEST[califi] = "'".$_REQUEST[califi]."'";


  $fec_actual = date("Y-m-d H:i:s");
  //query de insercion de despacho
  $query = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu
               SET cod_marcax = '$_REQUEST[marca]',
                   cod_lineax = '$_REQUEST[linea]',
                   ano_modelo = '$_REQUEST[modelo]',
                   ano_repote = '$_REQUEST[repote]',
                   cod_tipveh = '$_REQUEST[tipveh]',
                   cod_carroc = '$_REQUEST[carroc]',
                   cod_colorx = '$_REQUEST[colorx]',
                   num_motorx = '$_REQUEST[motor]',
                   num_seriex = '$_REQUEST[serie]',
                   val_pesove = '$_REQUEST[pesva]',
                   val_capaci = '$_REQUEST[capaci]',
                   num_config = '$_REQUEST[config]',
                   fec_revmec = ".$_REQUEST[revmec].",
                   nom_vincul = '$_REQUEST[vincula]',
                   fec_vigvin = ".$_REQUEST[vigvinv].",
                   num_agases = '$_REQUEST[gases]',
                   num_poliza = '".$_REQUEST[numsoa]."',
                   nom_asesoa = '".$_REQUEST[asesoa]."',
                   fec_vigfin = '".$_REQUEST[vigsoa]."',
                   reg_nalcar = '$_REQUEST[regnal]',
                   fec_vigfin = '$_REQUEST[vigsoa]',
                   num_polirc = '$_REQUEST[polirc]',
                   cod_aseprc = '$_REQUEST[aseprc]',
                   fec_venprc = '$_REQUEST[vigprc]',
                   cod_propie = '$_REQUEST[propie]',
                   cod_tenedo = '$_REQUEST[tenedo]',
                   cod_conduc = '$_REQUEST[conduc]',
                   num_tarpro = '$_REQUEST[tarpro]',
                   num_tarope = '$_REQUEST[tarope]',
                   cod_califi = ".$_REQUEST[califi].",
                   ind_chelis = '$_REQUEST[chelis]',
                   obs_vehicu = '$_REQUEST[obs]',
                   dir_fotfre = $_REQUEST[f_frente],
                   dir_fotizq = $_REQUEST[f_izqui],
                   dir_fotder = $_REQUEST[f_derec],
                   dir_fotpos = $_REQUEST[f_poster],
                   usr_modifi = '$_REQUEST[usuario]',
                   fec_modifi = '$fec_actual'
             WHERE num_placax = '$_REQUEST[placa]'";
  $insercion = new Consulta($query, $this -> conexion,"BR");

    // trae la ultima novedad de acuerdo al vehiculo
   $query = "SELECT Max(a.num_noveda) AS maximo
                    FROM ".BASE_DATOS.".tab_trayle_placas a
                    WHERE a.num_placax = '$_REQUEST[placa]'";
        //obtiene el consecutivo
        $consec = new Consulta($query, $this -> conexion);
        $ultimo = $consec -> ret_matriz();
        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec+1;

    if($_REQUEST[trayler])
    {
                  //Query de insercion
           $query = "INSERT ".BASE_DATOS.".tab_trayle_placas
                               VALUES ('$_REQUEST[placa]','$_REQUEST[trayler]','$nuevo_consec','$fec_actual','S')";
                  $insercion = new Consulta($query, $this -> conexion,"R");
    }
    else
    {
      $query = "SELECT Max(a.num_noveda) AS maximo
                  FROM ".BASE_DATOS.".tab_trayle_placas a
                 WHERE a.num_placax = '$_REQUEST[placa]'";
      //obtiene el consecutivo
      $consec = new Consulta($query, $this -> conexion);
      $ultimo = $consec -> ret_matriz();
      $ultimo_consec = $ultimo[0][0];
      $nuevo_consec = $ultimo_consec;

      $query = "UPDATE ".BASE_DATOS.".tab_trayle_placas
               SET ind_actual = 'S'
             WHERE num_placax = '$_REQUEST[placa]' AND
                   num_noveda = '".$nuevo_consec."'";
      $insercion = new Consulta($query, $this -> conexion,"R");
    }//fin else


    //--------------------------
    $SQL =  ' SELECT  cod_transp
                FROM  '.BASE_DATOS.'.tab_transp_vehicu
               WHERE  cod_transp = "' . $_REQUEST['transpor'] . '" AND 
                      num_placax = "' . $_REQUEST['placa'] . '"
            ';
    $consul = new Consulta( $SQL, $this -> conexion );
    $consul = $consul -> ret_matriz();  // Transportadora Regsitrada para la Placa

    if( count( $consul ) == 0 )
    {
      //--------------------------
      $datos_usuario = $this -> usuario -> retornar();
      $usuario = $datos_usuario["cod_usuari"];

      $SQL = "SELECT  c.cod_tercer, c.abr_tercer
                FROM  ".BASE_DATOS.".tab_transp_vehicu a JOIN
                      ".BASE_DATOS.".tab_tercer_emptra b ON a.cod_transp = b.cod_tercer JOIN 
                      ".BASE_DATOS.".tab_tercer_tercer c ON a.cod_transp = c.cod_tercer
               WHERE  a.num_placax = '" . $_REQUEST['placa'] . "'";
      $consulta = new Consulta( $SQL, $this -> conexion );
      $trareg = $consulta -> ret_matriz();  // Transportadora Regsitrada para la Placa

      if( count( $trareg ) > 0 )
      {
        $SQL =  '
                  UPDATE  '.BASE_DATOS.'.tab_transp_vehicu
                     SET  cod_transp = "' . $_REQUEST['transpor'] . '",
                          usr_modifi = "' . $usuario . '",
                          fec_modifi = NOW()
                   WHERE  num_placax = "' . $_REQUEST['placa'] . '"
                ';
      }
      else
      {
        $SQL =  '
                  INSERT INTO '.BASE_DATOS.'.tab_transp_vehicu( cod_transp, num_placax, usr_creaci, fec_creaci )
                  VALUES( "' . $_REQUEST['transpor'] . '", "' . $_REQUEST['placa'] . '", "' . $usuario . '", NOW() )
                ';
      }
      $actual = new Consulta( $SQL, $this -> conexion );
      //--------------------------
    }
    //--------------------------



    //Manejo de Interfaz GPS
/*
  $interf_gps = new Interfaz_GPS();
  $interf_gps -> Interfaz_GPS_envio($_REQUEST[transpor],BASE_DATOS,$_REQUEST[uuario],$this -> conexion);

  if($operador_gps)
  {
	for($i = 0; $i < $interf_gps -> cant_interf; $i++)
	{
	  $indact = 0;

	  if($interf_gps -> getVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]))
	  {
	    for($j = 0; $j < $interf_gps -> cant_interf; $j++)
	    {
	    	if($operador_gps[$j] == $interf_gps -> cod_operad[$i][0])
		{
		  $indact = 1;
		  $operador_gps[$j] = "";
		}
	    }
	    if(!$indact)
	    {
	     if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_PLACAX)
	     {
	    	if($interf_gps -> EstadoVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor],"0","".CONS_APLICA_BASICO."","".CONS_SERVID_INDICA.""))
	      	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Desactivo el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." Satisfactoriamente.</b>";
	    	else
	    	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"><b>Existio un Error al Desactivar el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b>";
	     }
	     else if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_IDGPSX)
	     {
		$idgps = $interf_gps -> getIdGPS($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]);

		if($interf_gps -> EstadoVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor],"0","".CONS_APLICA_BASICO."","".CONS_SERVID_INDICA."",$idgps))
	      	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Desactivo el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." Satisfactoriamente.</b>";
	    	else
	    	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"><b>Existio un Error al Desactivar el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b>";
	     }
	    }
	  }
	  else
	  {
	    for($j = 0; $j < $interf_gps -> cant_interf; $j++)
	    {
	    	if($operador_gps[$j] == $interf_gps -> cod_operad[$i][0])
		{
		  $indact = 1;
		  $operador_gps[$j] = "";
		}
	    }
	    if($indact)
	    {
	     if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_PLACAX)
	     {
	    	if($interf_gps -> EstadoVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor],"1","".CONS_APLICA_BASICO."","".CONS_SERVID_INDICA.""))
	      	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Activo el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." Satisfactoriamente.</b>";
	    	else
	    	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"><b>Existio un Error al Activar el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b>";
	     }
	     else if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_IDGPSX)
	     {
		if($interf_gps -> EstadoVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor],"1","".CONS_APLICA_BASICO."","".CONS_SERVID_INDICA."",$codidgps[$i]))
	      	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Activo el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." Satisfactoriamente.</b>";
	    	else
	    	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"><b>Existio un Error al Activar el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b>";
	     }
	    }
	  }
	}
  }
  else
  {
	for($i = 0; $i < $interf_gps -> cant_interf; $i++)
	{
	  if($interf_gps -> getVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]))
	  {
	   if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_PLACAX)
	   {
	    if($interf_gps -> EstadoVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor],"0","".CONS_APLICA_BASICO."","".CONS_SERVID_INDICA.""))
	      $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Desactivo el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." Satisfactoriamente.</b>";
	    else
	      $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"><b>Existio un Error al Desactivar el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b>";
	   }
	   else if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_IDGPSX)
	   {
	    $idgps = $interf_gps -> getIdGPS($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor]);

	    if($interf_gps -> EstadoVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],$_REQUEST[transpor],"0","".CONS_APLICA_BASICO."","".CONS_SERVID_INDICA."",$idgps))
	      $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Desactivo el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." Satisfactoriamente.</b>";
	    else
	      $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\"><b>Existio un Error al Desactivar el Veh&iacute;culo con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b>";
	   }
	  }
	}
  }
*/
 if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Vehiculo</a></b>";

   $mensaje = "El Vehiculo <b>".$_REQUEST[placa]."</b> se Actualizo con Exito<br>".$mensaje_gps."<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("ACTUALIZAR VEHICULO",$mensaje);
  }

 }

}

$proceso = new Act_vehicu_vehicu($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php

class Lis_vehicu_vehicu
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
     $this -> buscar();
     break;
  }
 }

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
      		  WHERE b.cod_marcax = a.cod_marcax
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

   $query = "SELECT a.num_placax,g.abr_tercer,g.num_telef1,h.abr_tercer,
		      		h.num_telmov,b.nom_marcax,c.nom_lineax,d.nom_colorx,
                    e.nom_carroc,a.ano_modelo,a.ind_estado,a.fec_creaci
               FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
		      		".BASE_DATOS.".tab_genera_marcas b,
                    ".BASE_DATOS.".tab_vehige_carroc e,
		      		".BASE_DATOS.".tab_vehige_colore d,
		      		".BASE_DATOS.".tab_vehige_lineas c,
		      		".BASE_DATOS.".tab_vehige_config i,
                    ".BASE_DATOS.".tab_tercer_tercer g,
                    ".BASE_DATOS.".tab_tercer_tercer h,
                    ".BASE_DATOS.".tab_transp_vehicu j
           	  WHERE a.cod_marcax = b.cod_marcax AND
		      		a.num_config = i.num_config AND
		      	    a.cod_marcax = c.cod_marcax AND
		      		a.cod_lineax = c.cod_lineax AND
		      		a.cod_colorx = d.cod_colorx AND
                    a.cod_carroc = e.cod_carroc AND
                    a.cod_tenedo = g.cod_tercer AND
                    a.cod_conduc = h.cod_tercer AND
                    a.num_placax = j.num_placax AND
                    a.num_placax LIKE '%".$_REQUEST[placa]."%'";

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

     $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&placa=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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


   $query = "SELECT a.num_placax,n.nom_colorx,b.nom_marcax,c.nom_lineax,
                    a.ano_modelo,a.ano_repote,d.nom_tipveh,
                    e.nom_carroc,a.num_motorx,a.num_seriex,
                    a.val_pesove,a.val_capaci,a.num_config,
                    a.fec_revmec,a.nom_vincul,a.fec_vigvin,
                    a.num_agases,a.fec_vengas,a.reg_nalcar,
                    a.num_tarpro,a.cod_califi,a.ind_chelis,
                    '','','',
                    a.num_polirc,g.abr_tercer,a.fec_venprc,
                    h.abr_tercer,k.abr_tercer,m.abr_tercer,
                    a.cod_colorx,a.num_tarpro,a.obs_vehicu,
                    h.dir_domici,h.num_telef1,
                    k.dir_domici,k.num_telef1,
                    m.dir_domici,m.num_telef1,m.num_telmov,
                    h.cod_tercer,h.num_telmov,k.cod_tercer,
                    k.num_telmov,m.cod_tercer,m.num_telmov,
                    a.usr_creaci,a.fec_creaci,a.usr_modifi,
                    a.fec_modifi,a.ind_estado
              FROM ".BASE_DATOS.".tab_genera_marcas b,
                   ".BASE_DATOS.".tab_vehige_lineas c,
                   ".BASE_DATOS.".tab_genera_tipveh d,
                   ".BASE_DATOS.".tab_vehige_carroc e,
                   ".BASE_DATOS.".tab_tercer_tercer h,
                   ".BASE_DATOS.".tab_tercer_tercer k,
                   ".BASE_DATOS.".tab_tercer_tercer m,
                   ".BASE_DATOS.".tab_vehige_colore n,
                   ".BASE_DATOS.".tab_vehicu_vehicu a LEFT JOIN
                   ".BASE_DATOS.".tab_tercer_tercer g ON
                   a.cod_aseprc = g.cod_tercer
            WHERE  a.cod_marcax = b.cod_marcax AND
                   a.cod_marcax = c.cod_marcax AND
                   a.cod_lineax = c.cod_lineax AND
                   a.cod_carroc = e.cod_carroc AND
                   a.cod_tenedo = h.cod_tercer AND
                   a.cod_propie = k.cod_tercer AND
                   a.cod_conduc = m.cod_tercer AND
                   a.cod_colorx = n.cod_colorx AND
                   a.cod_tipveh = d.cod_tipveh AND
                   a.num_placax = '$_REQUEST[placa]'
            ";

            $consulta = new Consulta($query, $this -> conexion);
            $matriz = $consulta -> ret_matriz();



           $query = "SELECT dir_fotfre,dir_fotizq,dir_fotder,dir_fotpos
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu
                WHERE  num_placax = '$_REQUEST[placa]'";

            $consulta = new Consulta($query, $this -> conexion);
            $fotos = $consulta -> ret_matriz();



 $query = "SELECT a.num_placax,Max(b.num_noveda)
            FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
                 ".BASE_DATOS.".tab_trayle_placas b
           WHERE  a.num_placax = '$_REQUEST[placa]' AND
                  a.num_placax = b.num_placax
        GROUP BY 1";
  $consec = new Consulta($query, $this -> conexion);
  $ntrayler = $consec -> ret_matriz();

  $query = "SELECT b.num_trayle,b.num_trayle
            FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
                 ".BASE_DATOS.".tab_trayle_placas b
           WHERE  a.num_placax = '$_REQUEST[placa]' AND
                  a.num_placax = b.num_placax AND
                  b.num_noveda = '".$ntrayler[0][1]."'
        ORDER BY 1";
  $consec = new Consulta($query, $this -> conexion);
  $trayler = $consec -> ret_matriz();

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/vehiculos.js\"></script>\n";
  $formulario = new Formulario ("index.php","post","VEHICULO","form_vehiculos");

  $formulario -> nueva_tabla();
  $formulario -> linea("Creado Por",0,"","12%");
  $formulario -> linea($matriz[0][47],0,"i","12%");
  $formulario -> linea("Fecha Creaci&oacute;n",0,"","12%");
  $formulario -> linea($matriz[0][48],1,"i","12%");
  $formulario -> linea("Modificado Por",0,"","12%");
  $formulario -> linea($matriz[0][49],0,"i","12%");
  $formulario -> linea("Fecha Modificado",0,"","12%");
  $formulario -> linea($matriz[0][50],1,"i","12%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos B&aacute;sicos ",1,"t2");
  $formulario -> nueva_tabla();
  $formulario -> linea("Placa",0,"","25%");
  $formulario -> linea($matriz[0][0],0,"i","25%");
  $formulario -> linea("Color",0,"","25%");
  $formulario -> linea($matriz[0][1],1,"i","25%");
  $formulario -> linea("Marca",0,"","25%");
  $formulario -> linea($matriz[0][2],0,"i","25%");
  $formulario -> linea("L&iacute;nea",0);
  $formulario -> linea($matriz[0][3],1,"i");
  $formulario -> linea("Modelo",0);
  $formulario -> linea($matriz[0][4],0,"i");
  $formulario -> linea("Repotenciado a",0);
  $formulario -> linea($matriz[0][5],1,"i");
  $formulario -> linea("Tipo Vinculaci&oacute;n",0);
  $formulario -> linea($matriz[0][6],0,"i");
  $formulario -> linea("Tipo Carrocer&iacute;a",0);
  $formulario -> linea($matriz[0][7],1,"i");
  $formulario -> linea("N&uacute;mero Motor",0);
  $formulario -> linea($matriz[0][8],0,"i");
  $formulario -> linea("N&uacute;mero Serie",0);
  $formulario -> linea($matriz[0][9],1,"i");
  $formulario -> linea("Peso Vacio(Tn)",0);
  $formulario -> linea($matriz[0][10],0,"i");
  $formulario -> linea("Capacidad(Tn)",0);
  $formulario -> linea($matriz[0][11],1,"i");
  $formulario -> linea("Configuraci&oacute;n",0);
  $formulario -> linea($matriz[0][12],0,"i");
  $formulario -> linea("Revisi&oacute;n Mec&aacute;nica",0);
  $formulario -> linea($matriz[0][13],1,"i");
  $formulario -> linea("Vinculado a",0);
  $formulario -> linea($matriz[0][14],0,"i");
  $formulario -> linea("Fecha Vencimiento",0);
  $formulario -> linea($matriz[0][15],1,"i");
  $formulario -> linea("Certif.An&aacute;lisis.Gases",0);
  $formulario -> linea($matriz[0][16],0,"i");
  $formulario -> linea("Fecha Vencimiento",0);
  $formulario -> linea($matriz[0][17],1,"i");
  $formulario -> linea("Reg.Nal.Carga",0);
  $formulario -> linea($matriz[0][18],0,"i");
  $formulario -> linea("Lic. Transito",0);
  $formulario -> linea($matriz[0][19],1,"i");
  $formulario -> linea("Calificaci&oacute;n",0);
  $formulario -> linea($matriz[0][20],0,"i");
  $formulario -> linea("Check List Express",0);
  $formulario -> linea($matriz[0][21],1,"i");

  $formulario -> nueva_tabla();
  $formulario -> linea("Informaci&oacute;n del Remolque",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Remolque",0);
  $formulario -> linea($trayler[0][0],1,"i","50%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos de Personas",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Poseedor",0,"t2","25%");
  $formulario -> linea($matriz[0][28],0,"t","25%");
  $formulario -> linea("",0,"t","25%");
  $formulario -> linea("",1,"t","25%");
  $formulario -> linea("C.C o Nit",0,"");
  $formulario -> linea($matriz[0][41],0,"i");
  $formulario -> linea("Direcci&oacute;n",0);
  $formulario -> linea($matriz[0][34],1,"i");
  $formulario -> linea("Celular",0);
  $formulario -> linea($matriz[0][42],0,"i");
  $formulario -> linea("Tel&eacute;fono",0);
  $formulario -> linea($matriz[0][35],1,"i");
  $formulario -> linea("Propietario",0,"t2");
  $formulario -> linea($matriz[0][29],0,"t");
  $formulario -> linea("",0,"t");
  $formulario -> linea("",1,"t");
  $formulario -> linea("C.C o Nit",0);
  $formulario -> linea($matriz[0][43],0,"i");
  $formulario -> linea("Direcci&oacute;n",0);
  $formulario -> linea($matriz[0][36],1,"i");
  $formulario -> linea("Celular",0);
  $formulario -> linea($matriz[0][44],0,"i");
  $formulario -> linea("Tel&eacute;fono",0);
  $formulario -> linea($matriz[0][37],1,"i");
  $formulario -> linea("Conductor",0,"t2");
  $formulario -> linea($matriz[0][30],0,"t");
  $formulario -> linea("",0,"t");
  $formulario -> linea("",1,"t");
  $formulario -> linea("C.C o Nit",0);
  $formulario -> linea($matriz[0][45],0,"i");
  $formulario -> linea("Direcci&oacute;n",0);
  $formulario -> linea($matriz[0][38],1,"i");
  $formulario -> linea("Celular",0);
  $formulario -> linea($matriz[0][46],0,"i");
  $formulario -> linea("Tel&eacute;fono",0);
  $formulario -> linea($matriz[0][39],0,"i");


  $formulario -> nueva_tabla();
  $formulario -> linea("Fotos Veh&iacute;culo",1,"t2");
  $formulario -> nueva_tabla();

  $formulario -> nueva_tabla();
  $formulario -> linea("Frente",0,"t");
  $formulario -> linea("Izquierda",0,"t");
  $formulario -> linea("Derecha",0,"t");
  $formulario -> linea("Posterior",1,"t");

  if($fotos[0][0]==Null)
   $fotos[0][0] = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
  else
   $fotos[0][0] = URL_VEHICU.$fotos[0][0];

  if($fotos[0][1]==Null)
   $fotos[0][1] = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
  else
   $fotos[0][1] = URL_VEHICU.$fotos[0][1];

  if($fotos[0][2]==Null)
   $fotos[0][2] = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
  else
   $fotos[0][2] = URL_VEHICU.$fotos[0][2];

  if($fotos[0][3]==Null)
   $fotos[0][3] = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
  else
   $fotos[0][3] = URL_VEHICU.$fotos[0][3];


  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$fotos[0][0]."\" alt=\"fotografia\" width=\"120\" height=\"100\" align=\"left\" ></td>";
  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$fotos[0][1]."\" alt=\"fotografia\" width=\"120\" height=\"100\" align=\"left\" ></td>";
  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$fotos[0][2]."\" alt=\"fotografia\" width=\"120\" height=\"100\" align=\"left\" ></td>";
  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$fotos[0][3]."\" alt=\"fotografia\" width=\"120\" height=\"100\" align=\"left\" ></td>";

  $formulario -> nueva_tabla();
  $formulario -> linea("Estado Actual del Vehiculo",1,"t2");

  $estact = $estina = $estpen = 0;

  if($matriz[0][51] == COD_ESTADO_ACTIVO)
   $estact = 1;
  else if($matriz[0][51] == COD_ESTADO_INACTI)
   $estina = 1;
  else if($matriz[0][51] == COD_ESTADO_PENDIE)
   $estpen = 1;

  $formulario -> nueva_tabla();
  $formulario -> radio("Activo","estcon",COD_ESTADO_ACTIVO,$estact,0);
  $formulario -> radio("Inactivo","estcon",COD_ESTADO_INACTI,$estina,0);
  $formulario -> radio("Pendiente","estcon",COD_ESTADO_PENDIE,$estpen,1);


    //Manejo de la Interfaz GPS
    /*$interf_gps = new Interfaz_GPS();
    $interf_gps -> Interfaz_GPS_envio(NIT_TRANSPOR,BASE_DATOS,$usuario,$this -> conexion);

    if($interf_gps -> cant_interf > 0)
    {
    	$formulario -> nueva_tabla();
    	echo "<hr>";
    	$formulario -> linea("Operadores GPS - Estado de Veh&iacute;culos con Operadores",1,"t");
	$formulario -> nueva_tabla();

    	for($i = 0; $i < $interf_gps -> cant_interf; $i++)
    	{
	  if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_PLACAX)
	  {
	   if($interf_gps -> getVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR))
		$activo_v = 1;
	   else	$activo_v = 0;

	   if($activo_v)
	    $mensaje = "<br><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>El Vehiculo Se Encuentra Activado con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b></small>";
	   else
	    $mensaje = "<br><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\"><b>El Vehiculo Se Encuentra Desactivado con el Operador GPS ".$interf_gps -> nom_operad[$i][0].".</b></small>";
	  }
	  else if($interf_gps -> ind_modind[$i][0] == CONS_MODIND_IDGPSX)
	  {
	   if($interf_gps -> getVehiculo($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR))
		$activo_v = 1;
	   else	$activo_v = 0;

	   if($activo_v)
	   {
	    $idgps = $interf_gps -> getIdGPS($_REQUEST[placa],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR);

	    $mensaje = "<br><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>El Veh&iacute;culo Se Encuentra Activado con el Operador GPS ".$interf_gps -> nom_operad[$i][0]." :: ID del Dispositivo GPS - ".$idgps.".</b></small>";
	   }
	   else
	    $mensaje = "<br><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\"><b>El Veh&iacute;culo Se Encuentra Desactivado con el Operador  GPS ".$interf_gps -> nom_operad[$i][0].".</b></small>";
	  }

	  echo $mensaje;
    	}
    }
*/
  $formulario -> nueva_tabla();
  if($matriz[0][33]!= '')
   {
     $formulario -> linea("Observaciones",0,"t");
     $formulario -> linea($matriz[0][33],1,"t");

  $formulario -> nueva_tabla();}
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("placa",$_REQUEST[placa],0);
  $formulario -> oculto("opcion",3,0);
  $formulario -> oculto("cod_servic","$_REQUEST[cod_servic]",0);
  $formulario -> oculto("window","central",0);
  $formulario -> botoni("Aceptar","if(confirm('Esta Seguro de Cambiar el Estado de Este Vehiculo.?')){form_vehiculos.submit()}",1);
  $formulario -> cerrar();
 }

 function Actualizar()
 {
  if(!$_REQUEST[estcon])
   $_REQUEST[estcon] = 0;

  $query = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu
  			   SET ind_estado = '".$_REQUEST[estcon]."',
  			   	   usr_modifi = '".$_REQUEST[usuario]."',
  			   	   fec_modifi = NOW()
  			 WHERE num_placax = '".$_REQUEST[placa]."'
  		   ";

  $consulta = new Consulta ($query, $this -> conexion, "BR");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Activar/Inactivar Otro Vehiculo</a></b>";

   $mensaje = "El Vehiculo <b>".$_REQUEST[placa]."</b> se Actualizo con Exito<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("ACTIVAR/INACTIVAR VEHICULO",$mensaje);
  }
 }

}//FIN CLASE Lis_vehicu_vehicu
     $proceso = new Lis_vehicu_vehicu($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
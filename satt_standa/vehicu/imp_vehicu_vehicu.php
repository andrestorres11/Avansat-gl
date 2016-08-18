<?php

class Imp_vehicu_vehicu
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
      switch($GLOBALS[opcion])
       {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Imprimir();
          break;
        default:
          $this -> buscar();
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

    if($GLOBALS[transp])
    {
     $query = "SELECT a.cod_tercer,a.abr_tercer
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			          ".BASE_DATOS.".tab_tercer_activi b
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          b.cod_activi = ".COD_FILTRO_EMPTRA." AND
   			          a.cod_tercer = '".$GLOBALS[transp]."'
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
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Buscar","if(form_list.por_modelo.checked){if(form_list.mod1.value == '' || form_list.mod2.value == '')alert('Por favor Ingrese el Rango de los Modelos a Filtrar.'); else form_list.submit()}else if(form_list.mod1.value != '' || form_list.mod2.value != '')alert('Por Favor Activa La Casilla De Modelo si Desea Filtrar un Rango de Modelos.'); else form_list.submit();",0);
   $formulario -> cerrar();
 }//FIN FUNCION

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $fec_actual = date("Y-m-d");
   $fecha1 = $GLOBALS[ano]."-".$GLOBALS[mes]."-".$GLOBALS[dia]." 00:00:00";
   $fecha2 = $GLOBALS[ano2]."-".$GLOBALS[mes2]."-".$GLOBALS[dia2]." 23:59:59";

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
                    a.num_placax LIKE '%".$GLOBALS[placa]."%'";

	   if($GLOBALS[transp])
        $query = $query." AND j.cod_transp = '".$GLOBALS[transp]."'";
       if($GLOBALS[por_fecha])
        $query = $query." AND a.fec_creaci  BETWEEN '".$fecha1."' AND '".$fecha2."'";
       if($GLOBALS[marcax])
          $query = $query." AND a.cod_marcax = '".$GLOBALS[marcax]."'";
       if($GLOBALS[carroc])
          $query = $query." AND a.cod_carroc = '".$GLOBALS[carroc]."'";
       if($GLOBALS[colorx])
          $query = $query." AND a.cod_colorx = '".$GLOBALS[colorx]."'";
       if($GLOBALS[config])
          $query = $query." AND a.num_config = '".$GLOBALS[config]."'";
       if($GLOBALS[por_modelo])
          $query = $query." AND a.ano_modelo >= ".$GLOBALS[mod1]." AND a.ano_modelo <= ".$GLOBALS[mod2];

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
   if($GLOBALS[por_fecha])
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

     $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&placa=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Volver","form_item.opcion.value='0';form_item.submit()",1);
   $formulario -> cerrar();
 }

  function Imprimir()
  {
    $datos_usuario = $this -> usuario -> retornar();
    //PARA EL FILTRO DE TRANSPOTADORA
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
      //Si esta asociado a una empresa
      $datos_filtro = $filtro -> retornar();
      $NIT_USUARI = $datos_filtro[clv_filtro];
      $query = "SELECT UPPER( abr_tercer) AS abr_tercer,cod_tercer FROM tab_tercer_tercer 
                WHERE cod_tercer = '".$NIT_USUARI."'";
      $consulta = new Consulta($query, $this -> conexion);
      $datbas = $consulta -> ret_matriz();
    }
    else
    {
    //No esta asociado a ninguna empresa, es un administrador
      $datbas[0][0] = NOMSAD;
      $datbas[0][1] = "";
    }

    $query = "SELECT a.num_placax, b.nom_marcax, a.ano_modelo, n.nom_colorx, d.nom_tipveh, ".
                    "a.ano_repote, a.reg_nalcar, a.num_tarpro, a.nom_vincul, ".
                    "a.fec_vigvin, '', a.fec_vigfin, '', a.num_seriex, ".
                    "a.num_motorx, m.nom_tercer, m.nom_apell1, m.nom_apell2, c.nom_tipdoc, ".
                    "m.cod_tercer, m.num_telef1, m.num_telmov, m.dir_domici, e.abr_ciudad, ".
                    "z.nom_tercer, z.nom_apell1, z.nom_apell2, h.nom_tipdoc, ".
                    "z.cod_tercer, z.num_telef1, z.num_telmov, z.dir_domici, x.abr_ciudad, ".
                    "a.cod_conduc, a.dir_fotfre, a.obs_estado, a.obs_vehicu, DATE( a.fec_creaci ) ".
               "FROM ".BASE_DATOS.".tab_vehicu_vehicu a, ".
                    "".BASE_DATOS.".tab_genera_marcas b, ".
                    "".BASE_DATOS.".tab_genera_tipveh d, ".
                    "".BASE_DATOS.".tab_tercer_tercer m, ".
                    "".BASE_DATOS.".tab_genera_tipdoc c, ".
                    "".BASE_DATOS.".tab_vehige_colore n, ".
                    "".BASE_DATOS.".tab_genera_ciudad e, ".
                    "".BASE_DATOS.".tab_genera_tipdoc h, ".
                    "".BASE_DATOS.".tab_genera_ciudad x, ".
                    "".BASE_DATOS.".tab_tercer_tercer z ".
              "WHERE a.cod_marcax = b.cod_marcax ".
                "AND a.cod_colorx = n.cod_colorx ".
                "AND a.cod_tipveh = d.cod_tipveh ".
                "AND a.cod_propie = m.cod_tercer ".
                "AND m.cod_tipdoc = c.cod_tipdoc ".
                "AND m.cod_ciudad = e.cod_ciudad ".
                "AND a.cod_tenedo = z.cod_tercer ".
                "AND z.cod_tipdoc = h.cod_tipdoc ".
                "AND z.cod_ciudad = x.cod_ciudad ".
                "AND a.num_placax = '".$GLOBALS[placa]."' ".
              "GROUP BY 1";

    $consulta = new Consulta($query, $this -> conexion);
    $matriz = $consulta -> ret_matriz();

    $query = "SELECT a.num_trayle ".
               "FROM ".BASE_DATOS.".tab_trayle_placas a ".
              "WHERE a.num_placax = '$GLOBALS[placa]' ".
                "AND a.ind_actual = 'S' ".
                "AND a.fec_asigna = ( SELECT MAX( b.fec_asigna ) ".
                                       "FROM ".BASE_DATOS.".tab_trayle_placas b ".
                                      "WHERE b.num_placax = a.num_placax ) ";

    $consec = new Consulta($query, $this -> conexion);
    $ntrayler = $consec -> ret_matriz();

    $query = "SELECT c.num_trayle,c.nom_propie,d.nom_martra,c.cod_config,e.nom_colorx,c.nro_ejes ".
               "FROM ".BASE_DATOS.".tab_vehige_trayle c, ".
                    "".BASE_DATOS.".tab_vehige_martra d, ".
                    "".BASE_DATOS.".tab_vehige_colore e ".
              "WHERE c.cod_colore = e.cod_colorx ".
                "AND c.cod_marcax = d.cod_martra ".
                "AND c.num_trayle = '".$ntrayler[0][0]."' ".
              "ORDER BY 1";

    $consec = new Consulta($query, $this -> conexion);
    $trayler = $consec -> ret_matriz();

    $query = "SELECT a.nom_tercer, a.nom_apell1, a.nom_apell2, b.nom_tipdoc, ".
                    "a.cod_tercer, a.num_telef1, a.num_telmov, a.dir_domici, c.abr_ciudad, ".
                    "d.num_licenc, d.fec_venlic, f.nom_catlic, d.cod_grupsa, d.nom_refper, ".
                    "d.tel_refper, a.dir_ultfot ".
               "FROM ".BASE_DATOS.".tab_tercer_tercer a, ".
                    "".BASE_DATOS.".tab_genera_tipdoc b, ".
                    "".BASE_DATOS.".tab_genera_ciudad c, ".
                    "".BASE_DATOS.".tab_tercer_conduc d, ".
                    "".BASE_DATOS.".tab_genera_catlic f ".
              "WHERE a.cod_tipdoc = b.cod_tipdoc ".
                "AND a.cod_ciudad = c.cod_ciudad ".
                "AND a.cod_tercer = d.cod_tercer ".
                "AND d.num_catlic = f.cod_catlic ".
                "AND a.cod_tercer = '".$matriz[0][33]."' ".
              "ORDER BY 1";

    $consec = new Consulta($query, $this -> conexion);
    $conduc = $consec -> ret_matriz();

    $query = "SELECT a.nom_empre,a.tel_empre,a.num_viajes,a.num_atigue,a.nom_mercan ".
               "FROM ".BASE_DATOS.".tab_conduc_refere a ".
              "WHERE a.cod_conduc = '".$matriz[0][33]."' ".
              "ORDER BY 1";

    $consec = new Consulta($query, $this -> conexion);
    $refere = $consec -> ret_matriz();
    
    if($datbas[0][1] == '890207572')
        $d01 = "logos/logo_ceter.jpg";
    else if($datbas[0][1] == '900491068') // 900491068 NIT DE CAPITAL CARGO (CAMBIAR EN EL MOMENTO DE LLEVAR A PRODUCCION)
        $d01 = "logos/logo_capital.jpg";
    else if($datbas[0][1] == '860021912') 
        $d01 = "logos/LOGO_COOPECOL.jpg";
    else if($datbas[0][1] == '830127357') 
        $d01 = "logos/LOGO_CARGA_LIBRE.jpg";
    else if($datbas[0][1] == '830513736') 
        $d01 = "logos/LOGO_CARCOL.jpg";
    else if($datbas[0][1] == '806004895') 
        $d01 = "logos/LOGO_CARIBB.png";
    else if($datbas[0][1] == '860068121') 
        $d01 = "logos/LOGO_CORONA.png";
    else
        $d01 = "imagenes/logo_liquid.gif";

    $d0 = $datbas[0][0]; //nombre empesa
    $d1 = $datbas[0][1] === "" ? $datbas[0][1] : "NIT: ".$datbas[0][1]; //nit empresa
    $d2 = $matriz[0][37]; //fecha de creacion del vehiculo
    $d4 = $matriz[0][0];  //placa
    $d5 = $matriz[0][1];  //marca
    $d6 = $matriz[0][2];  //modelo
    $d7 = $matriz[0][3];  //color
    $d8 = $matriz[0][4];  //tipo de vehiculo
    $d9 = $matriz[0][5];  //repotenciado a
    $d10 = $matriz[0][6];  //regnal
    $d11 = $matriz[0][7];  //tar propie
    $d12 = $matriz[0][8];  //vinculado a
    $d13 = $matriz[0][9];  //vigencia vinculacion
    $d14 = $matriz[0][10]; //n soat
    $d15 = $matriz[0][11]; //vigencia soat
    $d16 = $matriz[0][12]; //aseguradora soat
    $d17 = $matriz[0][13]; //chasis motor
    $d18 = $matriz[0][14]; //motor

    $d19 = $trayler[0][0]; // trayler
    $d20 = $trayler[0][1]; //propietario trayler
    $d21 = $trayler[0][2]; //marca trayler
    $d22 = $trayler[0][3]; //configuracion trayler
    $d23 = $trayler[0][4]; //color taryler
    $d24 = $trayler[0][5]; // n ejes

    $d25 = $matriz[0][15];
    $d26 = $matriz[0][16]." ".$matriz[0][17];
    $d27 = $matriz[0][18];
    $d28 = $matriz[0][19];
    $d39 = $matriz[0][20];
    $d30 = $matriz[0][21];
    $d31 = $matriz[0][22];
    $d32 = $matriz[0][23];

    $d33 = $matriz[0][24];
    $d34 = $matriz[0][25]." ".$matriz[0][26];
    $d35 = $matriz[0][27];
    $d36 = $matriz[0][28];
    $d37 = $matriz[0][29];
    $d38 = $matriz[0][30];
    $d39 = $matriz[0][31];
    $d40 = $matriz[0][32];
    $d69 = $matriz[0][35];
    $d70 = $matriz[0][36];

    $d41 = $conduc[0][0];
    $d42 = $conduc[0][1]." ".$conduc[0][2];
    $d43 = $conduc[0][3];
    $d44 = $conduc[0][4];
    $d45 = $conduc[0][5];
    $d46 = $conduc[0][6];
    $d47 = $conduc[0][7];
    $d48 = $conduc[0][8];
    $d63 = $conduc[0][9];
    $d64 = $conduc[0][10];
    $d65 = $conduc[0][11];
    $d66 = $conduc[0][12];
    $d49 = $conduc[0][13];
    $d50 = $conduc[0][14];

    $d51 = $refere[0][0];
    $d52 = $refere[0][1];
    $d53 = $refere[0][2];
    $d54 = $refere[0][3];
    $d55 = $refere[0][4];
    $d56 = $refere[1][0];
    $d57 = $refere[1][1];
    $d58 = $refere[1][2];
    $d59 = $refere[1][3];
    $d60 = $refere[1][4];

    $d67 = $conduc[0][15];
    $d68 = $matriz[0][34];

    if(!$conduc[0][15])
      $d67 = "../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg";
    else
      $d67 = URL_CONDUC.$conduc[0][15];

    if(!$matriz[0][34])
      $d68 = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
    else
      $d68 = URL_VEHICU.$matriz[0][34];

    //LLAMADO AL ARCHIVO HTML DEL FORMATO DEL VHICULO
    $tmpl_file = "../".DIR_APLICA_CENTRAL."/vehicu/vehicu.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;

    echo "<form name=\"form\" method=\"post\" action=\"index.php\">";
    echo "<br><br>"
        ."<table border=\"0\" width=\"100%\">"
          ."<tr>"
            ."<td align=\"center\">"
              ."<input type=\"hidden\" name=\"cod_servic\" value=\"$GLOBALS[cod_servic]\">"
              ."<input type=\"hidden\" name=\"window\" value=\"central\">"
              ."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.Volver.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';form.Volver.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">"
            ."</td>"
            ."<td align=\"center\">"
              ."<input type=\"reset\" name=\"Volver\" value=\"Volver\" onClick=\"javascript:history.go(-1);\">"
            ."</td>"
          ."</tr>"
        ."</table>";
    echo "</form>";
  }

}//FIN CLASE Lis_vehicu_vehicu

     $proceso = new Imp_vehicu_vehicu($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

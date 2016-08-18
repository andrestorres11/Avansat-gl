<?php

class Act_tercer_tercer
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
  if(!isset($GLOBALS[opcion]))
    $this -> Buscar();
  else
     {
      switch($GLOBALS[opcion])
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
       }
     }
 }

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   $formulario = new Formulario ("index.php","post","BUSCAR TERCEROS","form_act");
   $formulario -> linea("Seleccionar Filtro Para la Busqueda de Terceros",0,"t2");

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
   			         ".BASE_DATOS.".tab_tercer_activi b
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         b.cod_activi = ".COD_FILTRO_EMPTRA."
   			         ORDER BY 2
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

   $query = "SELECT a.cod_activi,a.nom_activi
   		       FROM ".BASE_DATOS.".tab_genera_activi a,
   		            ".BASE_DATOS.".tab_tercer_activi b,
   		            ".BASE_DATOS.".tab_tercer_tercer c
   		      WHERE a.cod_activi = b.cod_activi AND
   		            b.cod_tercer = c.cod_tercer AND
   		            a.cod_activi <> ".COD_FILTRO_EMPTRA." AND
   		            a.cod_activi <> ".COD_FILTRO_AGENCI." AND
   		            a.cod_activi <> ".COD_FILTRO_CONDUC."
   		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND c.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND c.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND c.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND c.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND c.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND c.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
   }

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $activi = $consulta -> ret_matriz();

   $activi = array_merge($inicio,$activi);

   $formulario -> nueva_tabla();
   $formulario -> radio("C&eacute;dula &oacute; NIT","fil",1,0,1);
   $formulario -> radio("Nombre Tercero","fil",2,0,1);
   $formulario -> lista("Actividad","activi",$activi,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
   $formulario -> radio("Activos","fil",3,0,1);
   $formulario -> radio("Inactivos","fil",4,0,1);
   $formulario -> radio("Pendientes","fil",5,0,1);
   $formulario -> radio("Todos","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Buscar","form_act.submit()",0);
   $formulario -> botoni("Cancelar","form_act.reset()",1);
   $formulario -> cerrar();
 }//FIN FUNCION BUSCAR

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_tercer,a.nom_tercer,a.nom_apell1,a.nom_apell2,
                   a.cod_ciudad,a.num_telef1,a.num_telmov,a.dir_domici,
                   a.dir_emailx,a.cod_estado
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
              	   ".BASE_DATOS.".tab_transp_tercer b,
              	   ".BASE_DATOS.".tab_tercer_activi c
             WHERE a.cod_tercer = b.cod_tercer AND
             	   a.cod_tercer = c.cod_tercer AND
   		           c.cod_activi <> ".COD_FILTRO_EMPTRA." AND
   		           c.cod_activi <> ".COD_FILTRO_AGENCI." AND
   		           c.cod_activi <> ".COD_FILTRO_CONDUC."
           ";

  if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
    }
   }

  if($GLOBALS[transp])
   $query .= " AND b.cod_transp = '".$GLOBALS[transp]."'";
  if($GLOBALS[activi])
   $query = $query." AND c.cod_activi = ".$GLOBALS[activi]."";
  if($GLOBALS[fil] == 1)
   $query = $query." AND a.cod_tercer = '".$GLOBALS[tercer]."'";
  else if($GLOBALS[fil] == 2)
   $query = $query." AND a.abr_tercer LIKE '%".$GLOBALS[tercer]."%'";
  else if($GLOBALS[fil] == 3)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
  else if($GLOBALS[fil] == 4)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_INACTI."";
  else if($GLOBALS[fil] == 5)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_PENDIE."";

  $query = $query." GROUP BY 1 ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE TERCEROS","form_item");
  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Tercero(s)",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("CC",0,"t2");
  $formulario -> linea("Nombre",0,"t2");
  $formulario -> linea("Ciudad",0,"t2");
  $formulario -> linea("Telefono",0,"t2");
  $formulario -> linea("Celular",0,"t2");
  $formulario -> linea("Direcci&oacute;n",0,"t2");
  $formulario -> linea("E-mail",0,"t2");
  $formulario -> linea("Estado",1,"t2");

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

  for($i = 0; $i < sizeof($matriz); $i++)
  {
   if($matriz[$i][9] != COD_ESTADO_ACTIVO)
    $estilo = "ie";
   else
    $estilo = "i";

   if($matriz[$i][9] == COD_ESTADO_ACTIVO)
    $estado = "Activo";
   else if($matriz[$i][9] == COD_ESTADO_INACTI)
    $estado = "Inactivo";
   else if($matriz[$i][9] == COD_ESTADO_PENDIE)
    $estado = "Pendiente";


   $ciudad_a = $objciud -> getSeleccCiudad($matriz[$i][4]);

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&tercer=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario -> linea($matriz[$i][0],0,$estilo);
   $formulario -> linea($matriz[$i][1]." ".$matriz[$i][2]." ".$matriz[$i][3],0,$estilo);
   $formulario -> linea($ciudad_a[0][1],0,$estilo);
   $formulario -> linea($matriz[$i][5],0,$estilo);
   $formulario -> linea($matriz[$i][6],0,$estilo);
   $formulario -> linea($matriz[$i][7],0,$estilo);
   $formulario -> linea($matriz[$i][8],0,$estilo);
   $formulario -> linea($estado,1,$estilo);
  }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //datos
  $query = "SELECT a.cod_tercer,a.nom_tercer,a.abr_tercer,a.cod_ciudad,'',
                   a.dir_domici,a.num_telef1,a.dir_emailx,'',a.obs_tercer,
                   a.num_telmov,a.dir_urlweb,a.cod_tipdoc,a.nom_apell1,a.nom_apell2,
                   a.num_faxxxx,a.num_telef2,a.cod_terreg,a.num_verifi,a.cod_paisxx,
                   a.cod_depart,a.usr_creaci, a.fec_creaci,a.usr_modifi, a.fec_modifi
              FROM ".BASE_DATOS.".tab_tercer_tercer a
		     WHERE a.cod_tercer = '".$GLOBALS[tercer]."'
		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

   $query = "SELECT cod_tipdoc,nom_tipdoc
               FROM ".BASE_DATOS.".tab_genera_tipdoc
               WHERE cod_tipdoc= '".$matriz[0][12]."'";

   $consulta = new Consulta($query, $this -> conexion);
   $tipdoc_a = $consulta -> ret_matriz();

   $query = "SELECT cod_terreg,nom_terreg
               FROM ".BASE_DATOS.".tab_genera_terreg
               WHERE cod_terreg= '".$matriz[0][17]."'";
   $consulta = new Consulta($query, $this -> conexion);
   $terreg_a = $consulta -> ret_matriz();

   $inicio[0][0]=0;
   $inicio[0][1]='-';

   $query = "SELECT cod_tipdoc,nom_tipdoc
               FROM ".BASE_DATOS.".tab_genera_tipdoc
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $tipdoc = $consulta -> ret_matriz();
   $tipdoc=array_merge($tipdoc_a,$inicio,$tipdoc);

   $query = "SELECT cod_terreg,nom_terreg
               FROM ".BASE_DATOS.".tab_genera_terreg
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $terreg = $consulta -> ret_matriz();
   $terreg=array_merge($terreg_a,$inicio,$terreg);

   $query = "SELECT a.cod_activi,a.nom_activi
               FROM ".BASE_DATOS.".tab_genera_activi a
              WHERE a.cod_activi <> ".COD_FILTRO_EMPTRA." AND
   		            a.cod_activi <> ".COD_FILTRO_AGENCI." AND
   		            a.cod_activi <> ".COD_FILTRO_CONDUC."
             ";

   $consulta = new Consulta($query, $this -> conexion);
   $actividades = $consulta -> ret_matriz();

   $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
   $ciudades = $objciud -> getListadoCiudades();
   $ciudad_a = $objciud -> getSeleccCiudad($matriz[0][3]);
   $ciudades = array_merge($ciudad_a,$inicio,$ciudades);

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tercero.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","ACTUALIZAR TERCERO","form_insert");

   $formulario -> nueva_tabla();
   $formulario -> linea("Creado Por:",0,"t");
   $formulario -> linea($matriz[0][21],0,"i");
   $formulario -> linea("Fecha:",0,"t");
   $formulario -> linea($matriz[0][22],1,"i");
   $formulario -> linea("Actualizado por:",0,"t");
   $formulario -> linea($matriz[0][23],0,"i");
   $formulario -> linea("Fecha:",0,"t");
   $formulario -> linea($matriz[0][24],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Datos B&aacute;sicos del Tercero",1,"t2");

   $formulario -> nueva_tabla();
   if($tipdoc[0][0] == 'N'){
   $formulario -> lista("Tipo Doc.:", "tipdoc", $tipdoc, 0,1);
   $formulario -> texto("Nro. Documento:","text","tercer\" readonly onChange=\"form_tercero.submit()",1,10,10,"",$GLOBALS[tercer],"","",NULL,1);
   $formulario -> texto("Dig. Verifica:","text","dijver",0,1,1,"",$matriz[0][18],"","",NULL,1);
   $formulario -> texto ("Nombre:","text","nom",1,40,100,"","".$matriz[0][1]."","","",NULL,1);
   $formulario -> texto ("Abreviatura:","text","abr",0,20,50,"","".$matriz[0][2]."","","",NULL,1);
   $formulario -> lista ("Regimen", "regimen", $terreg, 1,1);
   $formulario -> lista ("Ciudad", "ciudad", $ciudades, 0,1);
   $formulario -> texto ("Direcci&oacute;n:","text","dir1",1,35,50,"","".$matriz[0][5]."","","",NULL,1);
   $formulario -> texto ("Tel&eacute;fono 1:","text","tel\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,15,35,"","".$matriz[0][6]."","","",NULL,1);
   $formulario -> texto ("Tel&eacute;fono 2:","text","tel2\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,15,35,"","".$matriz[0][16]."");
   $formulario -> texto ("Celular:","text","celu\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,15,35,"","".$matriz[0][10]."");
   $formulario -> texto ("Fax:","text","fax\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,15,35,"","".$matriz[0][15]."");
   $formulario -> texto ("P&eacute;gina Web:","text","web",0,30,60,"","".$matriz[0][11]."");
   $formulario -> texto ("E-mail:","text","correo",1,50,255,"","".$matriz[0][7]."");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades", 0,"t2");

   $formulario -> nueva_tabla();

   for($i=0;$i<sizeof($actividades);$i++)
   {
     //evalua si la actividad existia
     $query = "SELECT cod_tercer,cod_activi
                 FROM ".BASE_DATOS.".tab_tercer_activi
                WHERE cod_tercer = '$GLOBALS[tercer]' AND
                      cod_activi = '".$actividades[$i][0]."' ";
     $consulta = new Consulta($query, $this -> conexion);
     $anterior = $consulta -> ret_matriz();

     $j=$i+1;
     if($j%3 == 0)
     {
       if(sizeof($anterior) ==  0)
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],0,1);
       else
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],1,1);
     }//fin if $j%3 == 0
     else
     {
       if(sizeof($anterior) ==  0)
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],0,0);
       else
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],1,0);
     }//fin else $j%3 == 0
   }//fin for

   $formulario -> nueva_tabla();

   $formulario -> nueva_tabla();
   $formulario -> texto ("Observaciones","textarea","obs",1,30,3,"","".$matriz[0][9]."");

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("maxactivi","".sizeof($actividades)."",0);
   $formulario -> oculto("tercer",$GLOBALS[tercer],0);
   $formulario -> oculto("nit","".$matriz[0][0]."",0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> boton("Actualizar","button\" onClick=\"aceptar_update() ",0);
   $formulario -> boton("Borrar","reset",1);
   $formulario -> cerrar();
   }
   else
   {
   $formulario -> lista ("Tipo Doc.:", "tipdoc", $tipdoc, 0,1);
   $formulario -> texto ("No. Documento:","text","nit\" disabled ",1,15,11,"","$GLOBALS[tercer]","","",NULL,1);
   $formulario -> texto ("Nombres:","text","nom",0,40,100,"","".$matriz[0][1]."","","",NULL,1);
   $formulario -> texto ("Apellido 1:","text","apell1",1,20,50,"","".$matriz[0][13]."","","",NULL,1);
   $formulario -> texto ("Apellido 2:","text","apell2",0,20,50,"","".$matriz[0][14]."");
   $formulario -> lista ("Ciudad", "ciudad", $ciudades, 1,1);
   $formulario -> texto ("Direcci&oacute;n:","text","dir1",0,35,50,"","".$matriz[0][5]."","","",NULL,1);
   $formulario -> texto ("Tel&eacute;fono 1:","text","tel\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,15,35,"","".$matriz[0][6]."","","",NULL,1);
   $formulario -> texto ("Tel&eacute;fono 2:","text","tel2\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,15,35,"","".$matriz[0][16]."");
   $formulario -> texto ("Celular:","text","celu\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,15,35,"","".$matriz[0][10]."");
   $formulario -> texto ("Fax:","text","fax\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,15,35,"","".$matriz[0][15]."");
   $formulario -> texto ("Pagina Web:","text","web",1,30,60,"","".$matriz[0][11]."");
   $formulario -> texto ("E-mail:","text","correo",1,35,40,"","".$matriz[0][7]."");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades", 0,"t2");

   $formulario -> nueva_tabla();

   for($i=0;$i<sizeof($actividades);$i++)
   {
     //evalua si la actividad existia
     $query = "SELECT cod_tercer,cod_activi
                 FROM ".BASE_DATOS.".tab_tercer_activi
                WHERE cod_tercer = '$GLOBALS[tercer]' AND
                      cod_activi = '".$actividades[$i][0]."' ";
     $consulta = new Consulta($query, $this -> conexion);
     $anterior = $consulta -> ret_matriz();

     $j=$i+1;
     if($j%3 == 0)
     {
       if(sizeof($anterior) ==  0)
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],0,1);
       else
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],1,1);
     }//fin if $j%3 == 0
     else
     {
       if(sizeof($anterior) ==  0)
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],0,0);
       else
         $formulario -> caja ("".$actividades[$i][1]."","activi[$i]",$actividades[$i][0],1,0);
     }//fin else $j%3 == 0
   }//fin for

   $formulario -> nueva_tabla();
   $formulario -> texto ("Observaciones","textarea","obs",1,30,3,"","".$matriz[0][9]."");

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("maxactivi","".sizeof($actividades)."",0);
   $formulario -> oculto("tercer",$GLOBALS[tercer],0);
   $formulario -> oculto("nit","".$matriz[0][0]."",0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> botoni("Actualizar","aceptar_update_n()",0);
   $formulario -> botoni("Borrar","form_insert.reset()",1);
   $formulario -> cerrar();
   }

 }

 function Actualizar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $query = "SELECT a.cod_paisxx,a.cod_depart
  		      FROM ".BASE_DATOS.".tab_genera_ciudad a
  		     WHERE a.cod_ciudad = ".$GLOBALS[ciudad]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $infciuda = $consulta -> ret_matriz();

  //query que actualiza
  if($GLOBALS[tipdoc] == 'N')
    $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
               SET num_verifi = '$GLOBALS[dijver]',
                   cod_tipdoc = '$GLOBALS[tipdoc]',
                   cod_terreg = '$GLOBALS[regimen]',
                   nom_tercer = '$GLOBALS[nom]',
                   abr_tercer = '$GLOBALS[abr]',
                   dir_domici = '$GLOBALS[dir1]',
                   num_telef1 = '$GLOBALS[tel]',
                   num_telef2 = '$GLOBALS[tel2]',
                   num_telmov = '$GLOBALS[celu]',
                   num_faxxxx = '$GLOBALS[fax]',
                   cod_paisxx = ".$infciuda[0][0].",
                   cod_depart = ".$infciuda[0][1].",
                   cod_ciudad = ".$GLOBALS[ciudad].",
                   dir_emailx = '$GLOBALS[correo]',
                   dir_urlweb = '$GLOBALS[web]',
                   obs_tercer = '$GLOBALS[obs]',
                   usr_modifi = '$GLOBALS[usuario]',
                   fec_modifi = NOW()
             WHERE cod_tercer = '$GLOBALS[nit]'";
  else
  {
    $GLOBALS[abr]=$GLOBALS[apell1]." ".$GLOBALS[nom];

    $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
               SET num_verifi = '$GLOBALS[dijver]',
                   cod_tipdoc = '$GLOBALS[tipdoc]',
                   nom_apell1 = '$GLOBALS[apell1]',
                   nom_apell2 = '$GLOBALS[apell2]',
                   nom_tercer = '$GLOBALS[nom]',
                   abr_tercer = '$GLOBALS[abr]',
                   dir_domici = '$GLOBALS[dir1]',
                   num_telef1 = '$GLOBALS[tel]',
                   num_telef2 = '$GLOBALS[tel2]',
                   num_telmov = '$GLOBALS[celu]',
                   num_faxxxx = '$GLOBALS[fax]',
                   cod_paisxx = ".$infciuda[0][0].",
                   cod_depart = ".$infciuda[0][1].",
                   cod_ciudad = ".$GLOBALS[ciudad].",
                   dir_emailx = '$GLOBALS[correo]',
                   dir_urlweb = '$GLOBALS[web]',
                   obs_tercer = '$GLOBALS[obs]',
                   usr_modifi = '$GLOBALS[usuario]',
                   fec_modifi = NOW()
             WHERE cod_tercer = '$GLOBALS[nit]'";
  }

  $insercion = new Consulta($query, $this -> conexion,"BR");

  $activi = $GLOBALS[activi];

  //query que borra
  $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi
             WHERE cod_tercer = '".$GLOBALS[nit]."' AND
             	   cod_activi <> ".COD_FILTRO_EMPTRA." AND
   		           cod_activi <> ".COD_FILTRO_AGENCI." AND
   		           cod_activi <> ".COD_FILTRO_CONDUC."
           ";

  $consulta = new Consulta($query, $this -> conexion, "R");

  for($i = 0; $i < $GLOBALS[maxactivi]; $i++)
  {
   if($activi[$i] != Null)
   {
    $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                   VALUES ('".$GLOBALS[nit]."',".$activi[$i].")";

    $consulta = new Consulta($query, $this -> conexion, "R");
   }
  }

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Tercero</a></b>";

   $mensaje =  "El Tercero <b>".$GLOBALS[abr]."</b> Se Actualizo con Exito".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ACTUALIZAR TERCEROS",$mensaje);
  }
 }
}
   $proceso = new Act_tercer_tercer($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
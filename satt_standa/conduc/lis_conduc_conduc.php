<?php
/****************************************************************************
NOMBRE:   lis_conduc_conduc.php
FUNCION:  listar conductores
ELKIN JAVIER BELEÑO
AGOSTO 11 DE 2005
****************************************************************************/
class Lis_conduc_conduc
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
//********METODOS
 function principal()
 {
  echo $GLOBALS[opcion];die;
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
          $this -> Volver();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************
 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   $formulario = new Formulario ("index.php","post","BUSCAR CONDUCTORES","form_act");
   $formulario -> linea("Seleccionar Filtro Para la Busqueda de Conductores",0,"t2");

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

   $formulario -> nueva_tabla();
   $formulario -> radio("C&eacute;dula","fil",1,0,1);
   $formulario -> radio("Nombre Tercero","fil",2,0,1);
   $formulario -> radio("Activos","fil",3,0,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
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
              FROM ".BASE_DATOS.".tab_tercer_tercer a 
			  			LEFT JOIN ".BASE_DATOS.".tab_transp_tercer b ON a.cod_tercer = b.cod_tercer,
				   ".BASE_DATOS.".tab_tercer_conduc c 
             WHERE  1 = 1 AND  a.cod_tercer = c.cod_tercer";

  if($GLOBALS[transp])
   $query .= " AND b.cod_transp = '".$GLOBALS[transp]."'";
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
  /*echo "<pre>";
  print_r($query);
  echo "</pre>";*/
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  
  
    $_URL  = "../".DIR_APLICA_CENTRAL."/export/exp_recurs_recurs.php?op=3";
    $_URL .= "&url=".DIR_APLICA_CENTRAL;
    $_URL .= "&db=".BASE_DATOS;
    $_URL .= !empty($GLOBALS[transp]) ? "&transp=".$GLOBALS[transp] : '';
    $_URL .= !empty($GLOBALS[fil])    ? "&fil=".$GLOBALS[fil] : '';
    $_URL .= !empty($GLOBALS[tercer])     ? "&tercer=".$GLOBALS[tercer] : '';
    
  
  $formulario = new Formulario ("index.php","post","LISTADO DE CONDUCTORES","form_item");
  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Conductore(s)&nbsp;&nbsp;<a target='_blank' href='".$_URL."'>[ Excel ]</a>",0,"t2");

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

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&conduc=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
 }//FIN FUNCION
 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];


  $query = "SELECT b.nom_tipdoc,a.cod_tercer,a.nom_tercer,a.nom_apell1,a.nom_apell2,
                    c.cod_grupsa,if(c.cod_tipsex=1,'Masculino','Femenino'),a.dir_domici,
                    d.abr_ciudad,a.num_telef1,a.num_telef2,a.num_telmov,c.num_licenc,
                    e.nom_catlic,c.fec_venlic,c.nom_epsxxx,c.nom_arpxxx,c.nom_pensio,
                    c.num_pasado,c.fec_venpas,c.num_libtri,c.fec_ventri,c.nom_refper,
                    c.tel_refper,a.dir_ultfot,
                    a.usr_creaci, a.fec_creaci,a.usr_modifi, a.fec_modifi,a.obs_tercer
            FROM ".BASE_DATOS.".tab_tercer_tercer a 
         LEFT JOIN ".BASE_DATOS.".tab_genera_tipdoc b ON a.cod_tipdoc = b.cod_tipdoc
         LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d ON a.cod_ciudad = d.cod_ciudad,
                 ".BASE_DATOS.".tab_tercer_conduc c 
         LEFT JOIN ".BASE_DATOS.".tab_genera_catlic e ON c.num_catlic = e.cod_catlic
           WHERE a.cod_tercer = c.cod_tercer AND 
           a.cod_tercer = '$GLOBALS[conduc]'";
          
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/conduc.js\"></script>\n";

  $formulario = new Formulario ("index.php","post","CONDUCTOR","form_conduc");

  $formulario -> nueva_tabla();
  $formulario -> linea("Creado Por:",0,"","25%");
  $formulario -> linea($matriz[0][25],0,"i","25%");
  $formulario -> linea("Fecha de Creaci&oacute;n:",0,"","25%");
  $formulario -> linea($matriz[0][26],1,"i","25%");
  $formulario -> linea("Actualizado por:",0,"","25%");
  $formulario -> linea($matriz[0][27],0,"i","25%");
  $formulario -> linea("Fecha de Actualizaci&oacute;n:",0,"","25%");
  $formulario -> linea($matriz[0][28],0,"i","25%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos B&aacute;sicos ",1,"t2");

  $formulario -> nueva_tabla();

  if($matriz[0][24] == Null)
   $matriz[0][24] = "../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg";
  else
   $matriz[0][24] = URL_CONDUC.$matriz[0][24];

  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$matriz[0][24]."\" alt=\"fotografia\" width=\"80\" height=\"100\" align=\"left\" ></td>";

  $formulario -> linea("Tipo de Documento:",0);
  $formulario -> linea($matriz[0][0],0,"i");
  $formulario -> linea("N. de Documento:",0);
  $formulario -> linea($matriz[0][1],1,"i");
  $formulario -> linea("Nombres:",0);
  $formulario -> linea($matriz[0][2],0,"i");
  $formulario -> linea("Apellido 1:",0);
  $formulario -> linea($matriz[0][3],1,"i");
  $formulario -> linea("Apellido 2:",0);
  $formulario -> linea($matriz[0][4],0,"i");
  $formulario -> linea("Factor RH:",0);
  $formulario -> linea($matriz[0][5],1,"i");
  $formulario -> linea("Sexo",0);
  $formulario -> linea($matriz[0][6],0,"i");
  $formulario -> linea("Direcci&oacute;n:",0);
  $formulario -> linea($matriz[0][7],1,"i");
  $formulario -> linea("Ciudad de Residencia:",0);
  $formulario -> linea($matriz[0][8],0,"i");
  $formulario -> linea("Tel&eacute;fono 1",0);
  $formulario -> linea($matriz[0][9],1,"i");
  $formulario -> linea("Tel&eacute;fono 2:",0);
  $formulario -> linea($matriz[0][10],0,"i");
  $formulario -> linea("Tel&eacute;fono M&oacute;vil:",0);
  $formulario -> linea($matriz[0][11],1,"i");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos de la Licencia",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("N. Licencia:",0,"","16%");
  $formulario -> linea($matriz[0][12],0,"i","16%");
  $formulario -> linea("Categoria:",0,"","16%");
  $formulario -> linea($matriz[0][13],0,"i","16%");
  $formulario -> linea("Fecha de Vencimiento:",0,"","16%");
  $formulario -> linea($matriz[0][14],1,"i","16%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos de Seguridad Social",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("EPS:",0,"","16%");
  $formulario -> linea($matriz[0][15],0,"i","16%");
  $formulario -> linea("ARP:",0,"","16%");
  $formulario -> linea($matriz[0][16],0,"i","16%");
  $formulario -> linea("Fondo de Pensiones:",0,"","16%");
  $formulario -> linea($matriz[0][17],1,"i","16%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos Complementarios",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Pasado Judicial:",0,"","25%");
  $formulario -> linea($matriz[0][18],0,"i","25%");
  $formulario -> linea("Fecha de Vencimiento:",0,"","25%");
  $formulario -> linea($matriz[0][19],1,"i","25%");
  $formulario -> linea("N. Lib. Tripula:",0,"","25%");
  $formulario -> linea($matriz[0][20],0,"i","25%");
  $formulario -> linea("Fecha de Vencimiento:",0,"","25%");
  $formulario -> linea($matriz[0][21],1,"i","25%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Referencias Personales",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Nombre:",0,"","25%");
  $formulario -> linea($matriz[0][22],0,"i","25%");
  $formulario -> linea("Tel&eacute;fono:",0,"","25%");
  $formulario -> linea($matriz[0][23],1,"i","25%");

  $formulario -> nueva_tabla();
  $formulario -> linea("Referencias Laborales ",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Empresa",0,"t","20%");
  $formulario -> linea("Tel&eacute;fono",0,"t","20%");
  $formulario -> linea("Viajes",0,"t","20%");
  $formulario -> linea("Antiguedad",0,"t","20%");
  $formulario -> linea("Mercanc&iacute;a",1,"t","20%");

  $query = "SELECT a.nom_empre,a.tel_empre,a.num_viajes,a.num_atigue,a.nom_mercan
            FROM ".BASE_DATOS.".tab_conduc_refere a
           WHERE  a.cod_conduc = '$GLOBALS[conduc]'";
  $consec = new Consulta($query, $this -> conexion);
  $resulta2 = $consec -> ret_matriz();


  for($i=0; $i<sizeof($resulta2); $i++)
     {
     $formulario -> linea($resulta2[$i][0],0,"i");
     $formulario -> linea($resulta2[$i][1],0,"i");
     $formulario -> linea($resulta2[$i][2],0,"i");
     $formulario -> linea($resulta2[$i][3],0,"i");
     $formulario -> linea($resulta2[$i][4],1,"i");
     }

  $formulario -> nueva_tabla();
  $formulario -> linea("Observaciones",1,"t2");
  $formulario -> linea($matriz[0][29],1,"i");

  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);

  $formulario -> oculto("listo",1,0);
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> botoni("Volver","volver()",0);

  $formulario -> cerrar();

 }//FIN FUNCION
// *********************************************************************************

 function Volver()
 {
   $this -> Buscar();
 }//FIN FUNCION
// *********************************************************************************
}//FIN CLASE
   $proceso = new Lis_conduc_conduc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php

class Proc_tercer
{
 var $conexion,
     $usuario,
     $cod_aplica;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
  if(!isset($_REQUEST[opcion]))
    $this -> Buscar();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "2":
          $this -> Resultado();
          break;
        case "3":
          $this -> Datos();
          break;
        case "4":
          $this -> Actualizar();
          break;
        case "5":
          $this -> ListarDespac();
          break;
       }
     }
 }

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $formulario = new Formulario ("index.php","post","BUSCAR TRANSPORTADORAS","form_list");
   $formulario -> radio("NIT","fil",1,0,1);
   $formulario -> radio("Nombre","fil",2,0,1);
   $formulario -> radio("Activas","fil",3,0,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
   $formulario -> radio("Inactivas","fil",4,0,1);
   $formulario -> radio("Pendientes","fil",5,0,1);
   $formulario -> radio("Todas","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Buscar","form_list.submit()",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

   $query = "SELECT a.cod_tercer,a.nom_tercer,a.num_telef1,a.cod_ciudad,a.dir_domici
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
                   ".BASE_DATOS.".tab_tercer_activi b
             WHERE a.cod_tercer = b.cod_tercer AND
                   b.cod_activi = ".COD_FILTRO_EMPTRA."
             ";

   if($_REQUEST[fil] == 1)
    $query .= " AND a.cod_tercer = '".$_REQUEST[tercer]."'";
   else if($_REQUEST[fil] == 2)
    $query .= " AND a.abr_tercer LIKE '%".$_REQUEST[tercer]."%'";
   else if($_REQUEST[fil] == 3)
    $query .= " AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
   else if($_REQUEST[fil] == 4)
    $query .= " AND a.cod_estado = ".COD_ESTADO_INACTI."";
   else if($_REQUEST[fil] == 5)
    $query .= " AND a.cod_estado = ".COD_ESTADO_PENDIE."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consec = new Consulta($query, $this -> conexion);
   $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","LISTADO DE TRANSPORTADORAS","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Transportadora(s).",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("NIT",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea("Direcci&oacute;n",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&tercer=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
    $ciudad_a = $objciud -> getSeleccCiudad($matriz[$i][3]);

    $formulario -> linea($matriz[$i][0],0,"i");
    $formulario -> linea($matriz[$i][1],0,"i");
    $formulario -> linea($matriz[$i][2],0,"i");
    $formulario -> linea($ciudad_a[0][1],0,"i");
    $formulario -> linea($matriz[$i][4],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> botoni("Volver","javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  $remite[0][0] = 1;
  $remite[0][1] = "Remitente";
  $destin[0][0] = 2;
  $destin[0][1] = "Destinatario";

  $opcird = array_merge($inicio,$remite,$destin);

  $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

  $query = "SELECT a.cod_tercer,a.nom_tercer,a.abr_tercer,a.cod_ciudad,a.dir_domici,
                   a.num_telef1,a.num_telef2,a.num_telmov,a.num_faxxxx,d.nom_activi,e.nom_repleg,
                   e.cod_minins,e.num_resolu,e.fec_resolu,f.nom_terreg,e.num_region,e.ran_iniman,ran_finman,
                   e.ind_gracon,e.ind_ceriso,e.fec_ceriso,e.ind_cerbas,e.fec_cerbas,e.otr_certif,
                   e.ind_cobnal,e.ind_cobint,e.nro_habnal,e.fec_resnal
            FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi c,
            	 ".BASE_DATOS.".tab_genera_activi d,
                 ".BASE_DATOS.".tab_tercer_emptra e,
            	 ".BASE_DATOS.".tab_genera_terreg f
           WHERE a.cod_tercer = e.cod_tercer AND
                 a.cod_tercer = c.cod_tercer AND
                 c.cod_activi = d.cod_activi AND
                 a.cod_terreg = f.cod_terreg AND
                 a.cod_tercer = '$_REQUEST[tercer]'";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $query = "SELECT a.cod_remdes,a.num_remdes,a.nom_remdes,a.obs_adicio,
  		           a.ind_remdes,a.ind_estado
  		      FROM ".BASE_DATOS.".tab_genera_remdes a
  		     WHERE a.cod_transp = '".$_REQUEST[tercer]."'
  		   ";

  $consec = new Consulta($query, $this -> conexion);
  $remdes = $consec -> ret_matriz();

  if(!$_REQUEST[maximo])
  {
   for($i = 0; $i < sizeof($remdes); $i++)
   {
   	$selecc[$i] = 1;
   	$codird[$i] = $remdes[$i][0];
   	$docurd[$i] = $remdes[$i][1];
   	$nombrd[$i] = $remdes[$i][2];
   	$obserd[$i] = $remdes[$i][3];
   	$tipord[$i] = $remdes[$i][4];
   	$estard[$i] = $remdes[$i][5];
   }

   $_REQUEST[maximo] = sizeof($remdes);
  }
  else
  {
   $selecc = $_REQUEST[selecc];
   $codird = $_REQUEST[codird];
   $docurd = $_REQUEST[docurd];
   $nombrd = $_REQUEST[nombrd];
   $obserd = $_REQUEST[obserd];
   $tipord = $_REQUEST[tipord];
   $estard = $_REQUEST[estard];
  }

  $_REQUEST[maximo]++;

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
  $formulario = new Formulario ("index.php","post","REMITENTES/DESTINATARIOS","form_item");

  $formulario -> linea("Informaci&oacute; Principal",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Nit o CC",0,"t");
   $formulario -> linea($matriz[0][0],0,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Abreviatura",0,"t");
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("C&oacute;digo de Empresa",0,"t");
   $formulario -> linea($matriz[0][11],1,"i");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][4],0,"i");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea($matriz[0][5],1,"i");
   $formulario -> linea("R&eacute;gimen",0,"t");
   $formulario -> linea($matriz[0][14],0,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades",1,"t2");

   $formulario -> nueva_tabla();
   for($i=0;$i<sizeof($matriz);$i++)
   {
    $formulario -> linea($matriz[$i][9],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> linea("Remitentes y Destinatarios Relacionados",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("",0,"t");
   $formulario -> linea("S/N",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Documento/C&oacute;digo",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Observaciones",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Tipo",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Estado",1,"t");

   for($i = 0; $i < $_REQUEST[maximo]; $i++)
   {
    if($tipord[$i] == 1)
	 $opasrd = array_merge($remite,$opcird);
	else if($tipord[$i] == 2)
	 $opasrd = array_merge($destin,$opcird);
	else
	 $opasrd = $opcird;

    if(!$estard[$i])
     $estard[$i] = COD_ESTADO_INACTI;

    if(!$selecc[$i])
     $selecc[$i] = 0;

    if($i == $_REQUEST[maximo] - 1)
    {
     $selecc[$i] = 1;
     $estard[$i] = 1;
     $codird[$i] = "n";
    }

    $formulario -> oculto("codird[$i]",$codird[$i],0);

   	$formulario -> caja("","selecc[$i]",$codird[$i],$selecc[$i],0);
   	$formulario -> texto("","text","docurd[$i]",0,10,10,"",$docurd[$i]);
   	$formulario -> texto("","text","nombrd[$i]",0,20,32,"",$nombrd[$i]);
   	$formulario -> texto("","text","obserd[$i]",0,60,250,"",$obserd[$i]);
   	$formulario -> lista("","tipord[$i]",$opasrd,0);
   	$formulario -> caja("","estard[$i]",1,$estard[$i],1);
   }

   $formulario -> nueva_tabla();
   $formulario -> botoni("Otro","form_item.submit()",1);

   $formulario -> nueva_tabla();
   $formulario -> botoni("Aceptar","aceptar_remdes()",0);
   $formulario -> botoni("Volver","javascript:history.go(-1)",1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("tercer",$_REQUEST[tercer],0);
   $formulario -> oculto("maximo",$_REQUEST[maximo],0);
   $formulario -> oculto("opcion",$_REQUEST[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Actualizar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $selecc = $_REQUEST[selecc];
  $codird = $_REQUEST[codird];
  $docurd = $_REQUEST[docurd];
  $nombrd = $_REQUEST[nombrd];
  $obserd = $_REQUEST[obserd];
  $tipord = $_REQUEST[tipord];
  $estard = $_REQUEST[estard];

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  for($i = 0; $i < $_REQUEST[maximo]; $i++)
  {
   if(!$obserd[$i])
    $obserd[$i] = NULL;
   else
    $obserd[$i] = "'".$obserd[$i]."'";

   if(!$estard[$i])
    $estard[$i] = COD_ESTADO_INACTI;

   if($selecc[$i] && $codird[$i] == "n")
   {
   	$query = "SELECT MAX(a.cod_remdes)
   			    FROM ".BASE_DATOS.".tab_genera_remdes a
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $consecut = $consulta -> ret_matriz();
    $consecut[0][0]++;

   	$query = "INSERT INTO ".BASE_DATOS.".tab_genera_remdes
   			              (cod_remdes,num_remdes,nom_remdes,obs_adicio,
   			 			   cod_transp,ind_remdes,ind_estado,usr_creaci,
   			 			   fec_creaci)
   			 	   VALUES (".$consecut[0][0].",'".$docurd[$i]."','".$nombrd[$i]."',".$obserd[$i].",
   			 			   '".$_REQUEST[tercer]."','".$tipord[$i]."','".$estard[$i]."','".$datos_usuario["cod_usuari"]."',
   			 			   NOW())
   			 ";

   	$consulta = new Consulta($query, $this -> conexion,"R");
   }
   else if($selecc[$i] && $codird[$i] != "n")
   {
	$query = "UPDATE ".BASE_DATOS.".tab_genera_remdes
			     SET num_remdes = '".$docurd[$i]."',
			         nom_remdes = '".$nombrd[$i]."',
			         obs_adicio = ".$obserd[$i].",
			         ind_remdes = '".$tipord[$i]."',
			         ind_estado = '".$estard[$i]."',
			         usr_modifi = '".$datos_usuario["cod_usuari"]."',
			         fec_modifi = NOW()
			   WHERE cod_remdes = ".$codird[$i]."
			 ";

	$consulta = new Consulta($query, $this -> conexion,"R");
   }
   else if(!$selecc[$i] && $codird[$i] != "n")
   {
   	$query_b = "SELECT a.cod_remdes
   			    FROM ".BASE_DATOS.".tab_despac_remdes a
   			   WHERE a.cod_remdes = ".$codird[$i]."
   			 ";

    $consulta = new Consulta($query_b, $this -> conexion);
    $existreg = $consulta -> ret_matriz();

    if(!$existreg)
	{
	 $query = "DELETE FROM ".BASE_DATOS.".tab_genera_remdes
			         WHERE cod_remdes = ".$codird[$i]."
			  ";

	 $consulta = new Consulta($query, $this -> conexion,"R");
	}
	else
	 $mensaj_adici .= "<br>Imposible Eliminar a ".$nombrd[$i].", Se Encuentra Relacionado a uno &oacute; M&aacute;s Despachos";
   }
  }

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Buscar Otra Transportadora</a></b>";

   $mensaje = "Se Actualizo la Informaci&oacute;n de la Transportadora de Forma Exitosa.".$mensaj_adici."<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("REMITENTES/DESTINATARIOS",$mensaje);
  }
 }

 function ListarDespac()
 {
  $query = "SELECT a.cod_remdes,a.num_remdes,a.nom_remdes,a.obs_adicio
  		      FROM ".BASE_DATOS.".tab_genera_remdes a
  		     WHERE a.ind_remdes = '".$_REQUEST[tipoxx]."' AND
  		   		   a.ind_estado = '".COD_ESTADO_ACTIVO."' AND
  		   		   a.cod_transp = '".$_REQUEST[transport]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $remdest = $consulta -> ret_matriz();

  if($_REQUEST[tipoxx] == 1)
   $nomopcio = "Remitente";
  else if($_REQUEST[tipoxx] == 2)
   $nomopcio = "Destinatario";

  $formulario = new Formulario ("index.php","post","Listado de ".$nomopcio."s","form_item");

  if($remdest)
  {
   $formulario -> linea ("Se Encontro un Total de ".sizeof($remdest)." ".$nomopcio."(s)",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Documento/C&oacute;digo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Observaciones",1,"t");

   for($i = 0; $i < sizeof($remdest); $i++)
   {
    $remdest[$i][1] = "<a href=# onClick=\"opener.document.forms[0].cod".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][0]."';opener.document.forms[0].doc".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][1]."';opener.document.forms[0].nom".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][2]."';opener.document.forms[0].obs".$_REQUEST[indice].$_REQUEST[codigo].".value='".$remdest[$i][3]."'; top.close()\">".$remdest[$i][1]."</a>";

   	$formulario -> linea($remdest[$i][1],0,"i");
   	$formulario -> linea($remdest[$i][2],0,"i");
   	$formulario -> linea($remdest[$i][3],1,"i");
   }
  }

  $formulario -> cerrar();
 }

}

$proceso = new Proc_tercer($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

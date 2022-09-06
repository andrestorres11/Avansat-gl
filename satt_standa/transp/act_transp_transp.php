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
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Formulario();
          break;
        case "3":
          $this -> Actualizar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

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
   $formulario -> oculto("opcion",1,0);
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
    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&tercer=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
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
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Formulario()
 {
  $query = "SELECT a.cod_tercer,a.num_verifi,a.abr_tercer,a.nom_tercer,
				   b.cod_minins,a.cod_ciudad,a.dir_domici,a.num_telef1,
				   a.cod_terreg,a.obs_tercer,b.ind_cobnal,b.ind_cobint,
				   b.nro_habnal,b.fec_resnal,b.num_region,b.num_resolu,
				   b.ran_iniman,b.ran_finman,b.ind_gracon,b.ind_ceriso,
				   b.fec_ceriso,b.ind_cerbas,b.fec_cerbas,b.otr_certif,
				   a.dir_emailx
			  FROM ".BASE_DATOS.".tab_tercer_tercer a,
				   ".BASE_DATOS.".tab_tercer_emptra b
			 WHERE a.cod_tercer = b.cod_tercer AND
				   a.cod_tercer = '".$_REQUEST[tercer]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $transpor = $consulta -> ret_matriz();

  if(!$_REQUEST[nitter])
  {
	$_REQUEST[correo] = $transpor[0][dir_emailx];
	
   $_REQUEST[nitter] = $transpor[0][0];
   $_REQUEST[dijver] = $transpor[0][1];
   $_REQUEST[abr] = $transpor[0][2];
   $_REQUEST[nom] = $transpor[0][3];
   $_REQUEST[cod_minis] = $transpor[0][4];
   $_REQUEST[ciures] = $transpor[0][5];
   $_REQUEST[direc] = $transpor[0][6];
   $_REQUEST[telef] = $transpor[0][7];
   $_REQUEST[regimen] = $transpor[0][8];
   $_REQUEST[observ] = $transpor[0][9];

   $query = "SELECT a.cod_modali
   		       FROM ".BASE_DATOS.".tab_emptra_modali a
   		      WHERE a.cod_emptra = '".$transpor[0][0]."'
   			";

   $consulta = new Consulta($query, $this -> conexion);
   $moda_emp = $consulta -> ret_matriz();

   for($i = 0; $i < sizeof($moda_emp); $i++)
   	$modali[$i] = $moda_emp[$i][0];

   $_REQUEST[cobnac] = $transpor[0][10];
   $_REQUEST[cobint] = $transpor[0][11];
   $_REQUEST[nro_habnal] = $transpor[0][12];
   $_REQUEST[fecresol] = $transpor[0][13];
   $_REQUEST[codregi] = $transpor[0][14];
   $_REQUEST[numresol] = $transpor[0][15];
   $_REQUEST[ragnini] = $transpor[0][16];
   $_REQUEST[ragnfin] = $transpor[0][17];
   $_REQUEST[ind_grancon] = $transpor[0][18];
   $_REQUEST[ceriso] = $transpor[0][19];
   $_REQUEST[feciso] = $transpor[0][20];
   $_REQUEST[cerbas] = $transpor[0][21];
   $_REQUEST[fecbas] = $transpor[0][22];
   $_REQUEST[cerotr] = $transpor[0][23];
  }
  else
  {
   $modali = $_REQUEST[modali];
  }

  $datos_usuario = $this -> usuario -> retornar();
  $usuario = $datos_usuario["cod_usuari"];

  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fecha.js\"></script>\n";
  $formulario = new Formulario ("index.php","post","Transportadoras","form_transpor");

  $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
  $ciudad = $objciud -> getListadoCiudades();

  $ciudades = array_merge($inicio,$ciudad);
  $ciudagen = array_merge($inicio,$ciudad);

  if($_REQUEST[ciures])
  {
   $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST[ciures]);
   $ciudades = array_merge($ciudad_a,$ciudades);
  }

  if($_REQUEST[ciused])
  {
   $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST[ciused]);
   $ciudagen = array_merge($ciudad_a,$ciudagen);
  }

     $query = "SELECT cod_terreg,nom_terreg
                 FROM ".BASE_DATOS.".tab_genera_terreg
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $regimen = $consulta -> ret_matriz();
     $regimen = array_merge($inicio,$regimen);

     if($_REQUEST[regimen])
     {
      $query = "SELECT cod_terreg,nom_terreg
                  FROM ".BASE_DATOS.".tab_genera_terreg
             	 WHERE cod_terreg = ".$_REQUEST[regimen]."";
     $consulta = new Consulta($query, $this -> conexion);
     $regimen_a = $consulta -> ret_matriz();
     $regimen = array_merge($regimen_a,$regimen);
     }

     //modalidades
     $query = "SELECT cod_modali,nom_modali
                 FROM ".BASE_DATOS.".tab_genera_modali
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $modalidad = $consulta -> ret_matriz();

     $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Transportadora",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto ("NIT","text","tercer\" readonly onChange=\" genera_digito(this, document.form_transpor.dijver); form_transpor.submit();",0,9,9,"",$_REQUEST[tercer],"","",NULL,1);
     $formulario -> texto ("-","text","dijver\" readonly ",1,1,1,"",$_REQUEST[dijver]);

     $formulario -> texto ("Abreviatura","text","abr",0,20,25,"",$_REQUEST[abr],"","",NULL,1);
     $formulario -> texto ("Nombre o Razon Social","text","nom",1,35,100,"",$_REQUEST[nom],"","",NULL,1);
     $formulario -> texto ("Codigo de Empresa","text","cod_minis",0,4,4,"",$_REQUEST[cod_minis]);
     $formulario -> lista ("Ciudad", "ciures\" form_transpor.copiar()", $ciudades, 1,1);
     $formulario -> texto ("Direccion","text","direc\" onChange=\"copiar()",0,35,80,"",$_REQUEST[direc],"","",NULL,1);
     $formulario -> texto ("Telefono","text","telef\" onChange=\"copiar()\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,20,30,"",$_REQUEST[telef],"","",NULL,1);
     $formulario -> lista ("Regimen", "regimen", $regimen, 0,1);
	 $formulario -> texto ("Email","text","correo",1,30,255,"",$_REQUEST[correo],"","",NULL,1);
     $formulario -> texto ("Observaciones","textarea","datos[2]",1,25,2,"",$_REQUEST[datos][2]);

     $formulario -> nueva_tabla();
     $formulario -> linea ("Modalidad",1,"t2");

     $formulario -> nueva_tabla();
     for($i = 0; $i < sizeof($modalidad); $i++)
        $formulario -> caja ($modalidad[$i][1],"modali[$i]","".$modalidad[$i][0]."",0,0);

     $formulario -> nueva_tabla();
     $formulario -> linea("Habilitaciones Legales",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> caja ("Cobertura Nacional","cobnac","1",$matriz[0][3],0);
     $formulario -> caja ("Cobertura Internacional","cobint","1",$matriz[0][4],1);
     $formulario -> texto ("Nro Habilitacion Nacional","text","nro_habnal",0,10,30,"",$_REQUEST[nro_habnal]);
     $formulario -> texto ("Fecha Resolucion(AAAA-MM-DD)","text \"onChange='if(!val_fectexto(this.value)){alert(\"Fecha Invalida\");this.focus()}'","fecresol",1,10,10,"",$_REQUEST[fecresol]);
     $formulario -> texto ("Codigo Regional","text","codregi",1,3,3,"",$_REQUEST[codregi]);
     $formulario -> texto ("Nro Resolucion","text","numresol",0,4,8,"",$_REQUEST[numresol]);
     $formulario -> texto ("Del (AAAA-MM-DD)","text \"onBlur='if(!val_fectexto(this.value)){alert(\"Fecha Invalida\");this.focus()}'","fecresol1",1,10,10,"",$_REQUEST[fecresol1]);
     $formulario -> texto ("Rango Autorizados Manifiesto del","text","ragnini",0,6,7,"",$_REQUEST[ragnini]);
     $formulario -> texto ("Hasta","text","ragnfin",1,6,7,"",$_REQUEST[ragnfin]);
     $formulario -> caja ("Gran Contribuyente","ind_grancon","1",$matriz[0][3],0);

     $formulario -> nueva_tabla();
     $formulario -> linea("Certificaciones",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> caja (" Certificacion ISO","datos[3]","1",0,0);
     $formulario -> texto (" Vigencia(AAAA-MM-DD)","text \"onChange='if(!val_fectexto(this.value)){alert(\"Fecha Invalida\");this.focus()}'","datos[4]",1,4,10,"",$_REQUEST[datos][4]);
     $formulario -> caja (" Certificacion BASC","datos[5]","1",0,0);
     $formulario -> texto (" Vigencia(AAAA-MM-DD)","text \"onChange='if(!val_fectexto(this.value)){alert(\"Fecha Invalida\");this.focus()}'","datos[6]",1,4,10,"",$_REQUEST[datos][6]);
     $formulario -> texto ("Otras","text","datos[7]",1,30,100,"","");

     $formulario -> nueva_tabla();
     $formulario -> oculto("maximo",sizeof($modalidad),0);
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",0,0);

     $formulario -> nueva_tabla();
     $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
     $formulario -> botoni("Actualizar","siguiente_act(form_transpor)",0);
     $formulario -> botoni("Borrar","form_transpor.reset()",1);
     $formulario -> cerrar();
 }

 function Actualizar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $query = "SELECT cod_paisxx,cod_depart
              FROM ".BASE_DATOS.".tab_genera_ciudad
             WHERE cod_ciudad = ".$_REQUEST[ciures]."
           ";

  $consulta = new Consulta($query, $this -> conexion);
  $ciudad = $consulta -> ret_matriz();

  $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
  			   SET cod_terreg = '".$_REQUEST[regimen]."',
  			   	   nom_tercer = '".$_REQUEST[nom]."',
  			   	   abr_tercer = '".$_REQUEST[abr]."',
  			   	   dir_domici = '".$_REQUEST[direc]."',
  			   	   num_telef1 = '".$_REQUEST[telef]."',
  			   	   cod_paisxx = '".$ciudad[0][0]."',
  			   	   cod_depart = '".$ciudad[0][1]."',
  			   	   cod_ciudad = '".$_REQUEST[ciures]."',
  			   	   usr_modifi = '".$_REQUEST[usuario]."',
  			   	   fec_modifi = '".$fec_actual."',
				   dir_emailx = '$_REQUEST[correo]'
  		     WHERE cod_tercer = '".$_REQUEST[tercer]."'
  		";

  $insercion = new Consulta($query, $this -> conexion, "BR");


    //iso
   if($datos_ins[3] == 1)
        $iso='S';
     else
        $iso='N';
  //basc
   if($datos_ins[5] == 1)
        $basc='S';
     else
        $basc='N';

   //cobertura nacional
   if($_REQUEST["cobnac"] == 1)
        $ind_cobnal='S';
     else
        $ind_cobnal='N';

   //cobertura nacional
   if($_REQUEST["cobint"] == 1)
        $ind_cobint='S';
     else
        $ind_cobint='N';


   //gran contribuyente
   if($_REQUEST[ind_grancon] == 1)
        $grancon='S';
     else
        $grancon='N';

   $query = "UPDATE ".BASE_DATOS.".tab_tercer_emptra
                SET cod_minins = '".$_REQUEST[cod_minis]."',
                    num_resolu = '".$_REQUEST[numresol]."',
                    fec_resolu = '".$_REQUEST[fecresol]."',
                    num_region = '".$_REQUEST[codregi]."',
                    ran_iniman = '".$_REQUEST[ragnini]."',
                    ran_finman = '".$_REQUEST[ragnfin]."',
                    ind_gracon = '".$grancon."',
                    ind_ceriso = '".$iso."',
                    fec_ceriso = '".$datos_ins[4]."',
                    ind_cerbas = '".$basc."',
                    fec_cerbas = '".$datos_ins[6]."',
                    otr_certif = '".$datos_ins[7]."',
                    ind_cobnal = '".$ind_cobnal."',
                    ind_cobint = '".$ind_cobint."',
                    nro_habnal = '".$_REQUEST[nro_habnac]."',
                    fec_resnal = '".$_REQUEST[fecresol]."',
                    nom_repleg = '".$datos_ins[1]."',
                    usr_modifi = '".$_REQUEST[usuario]."',
                    fec_modifi = '".$fec_actual."'
              WHERE cod_tercer = '".$_REQUEST[tercer]."'
		  ";

  $insercion = new Consulta($query, $this -> conexion, "R");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Actualizar Otra Transportadora</a></b>";

   $mensaje = "Se Actualizo la Transportadora ".$_REQUEST[abr]." Exitosamente.".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ACTUALIZAR TRANSPORTADORA",$mensaje);
  }
 }

}

$proceso = new Proc_tercer($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
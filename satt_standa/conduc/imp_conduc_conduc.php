<?php
/****************************************************************************
NOMBRE:   imp_conduc_conduc.php
FUNCION:  Imprimir Clientes
ALEJANDRO ORTEGON
Septiembre 20 DE 2005
****************************************************************************/
class Imp_Conduc_Conduc
{
 var $conexion,
 	 $cod_aplica,
     $usuario;//una conexion ya establecida a la base de datos
    //Metodos
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
  if(!isset($_REQUEST[opcion]))
    $this -> Buscar();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "2":
          $this -> Resultado();
          break;
        case "1":
          $this -> Imprimir();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************
 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $formulario = new Formulario ("index.php","post","HOJA DE VIDA DEL CONDUCTOR","form_imp");
   $formulario -> radio("Documento","fil",1,0,1);
   $formulario -> radio("Nombre Conductor","fil",2,0,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
   $formulario -> radio("Todos","fil",3,0,1);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Aceptar","form_imp.submit()",0);
   $formulario -> cerrar();
 }//FIN FUNCION BUSCAR

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   
    //PARA EL FILTRO DE TRANSPORTADORA
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
    // SI tiene perfil de transporadora 
     $datos_filtro = $filtro -> retornar();
     $NIT_USUARI = $datos_filtro[clv_filtro];
     
     $query = "SELECT a.cod_tercer,a.nom_tercer,
                       a.abr_tercer,b.nom_ciudad,a.num_telef1
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_genera_ciudad b,
                     ".BASE_DATOS.".tab_tercer_conduc c,
                     ".BASE_DATOS.".tab_transp_tercer d
                WHERE  a.cod_tercer = c.cod_tercer AND
                       a.cod_ciudad = b.cod_ciudad AND
                       a.cod_tercer = d.cod_tercer AND 
                       d.cod_transp = '". $NIT_USUARI ."'";
    }
    else
    {
      // NO tiene perfil de transporadora 
      $query = "SELECT a.cod_tercer,a.nom_tercer,
                   a.abr_tercer,b.nom_ciudad,a.num_telef1
            FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_genera_ciudad b,
                 ".BASE_DATOS.".tab_tercer_conduc c
           WHERE  a.cod_tercer = c.cod_tercer AND
                  a.cod_ciudad = b.cod_ciudad ";
    }

          if(($_REQUEST[fil]=='1') AND($_REQUEST[tercer]!=''))
         $query = $query." AND a.cod_tercer LIKE '%$_REQUEST[tercer]%'";
         if(($_REQUEST[fil]=='2') AND($_REQUEST[tercer]!=''))
         $query = $query." AND (a.nom_tercer LIKE '%$_REQUEST[tercer]%' OR a.abr_tercer LIKE '%$_REQUEST[tercer]%')";


      if($datos_usuario["cod_perfil"] == "")
      {
      	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
      	}
      }
      else
      {
	//PARA EL FILTRO DE CONDUCTOR
      	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      	if($filtro -> listar($this -> conexion))
      	{
      		$datos_filtro = $filtro -> retornar();
        	$query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
      	}
      }
      //final de los filtros asignados al usuario o perfil actual

         $query = $query." GROUP BY 1 ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();
  for($i=0;$i<sizeof($matriz);$i++)
        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&conduc=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario = new Formulario ("index.php","post","CONDUCTORES","form_item");
   $formulario -> linea("Se Encontraron ".sizeof($matriz)." Conductores",0);

   $formulario -> nueva_tabla();
   if(sizeof($matriz) > 0)
     {
     $formulario -> linea("Nit",0,"t2","10%");
     $formulario -> linea("Nombre",0,"t2","30%");
     $formulario -> linea("Abreviatura",0,"t2","30%");
     $formulario -> linea("Ciudad",0,"t2","15%");
     $formulario -> linea("Tel&eacute;fono",1,"t2","15%");

     for($i=0;$i<sizeof($matriz);$i++)
        {
        if($i%2 == 0)
          {
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> linea($matriz[$i][1],0,"i");
          $formulario -> linea($matriz[$i][2],0,"i");
          $formulario -> linea($matriz[$i][3],0,"i");
          $formulario -> linea($matriz[$i][4],1,"i");
          }//fin if
        else
          {
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> linea($matriz[$i][1],0,"i");
          $formulario -> linea($matriz[$i][2],0,"i");
          $formulario -> linea($matriz[$i][3],0,"i");
          $formulario -> linea($matriz[$i][4],1,"i");
          }//fin else
        }//fin for
     }//fin if
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION


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
    
    
    $query = "SELECT g.nom_tercer, g.cod_tercer, DATE_FORMAT(b.fec_creaci,'%d-%m-%Y'), ".
                    "c.nom_agenci, if( d.cod_tipdoc = 'C', 'Cedula', '' ), d.cod_tercer, ".
                    "d.nom_tercer, d.nom_apell1, d.nom_apell2, if( b.cod_tipsex = '1', 'Masculino', 'Femenino' ), ".
                    "b.cod_grupsa, d.dir_domici, e.nom_ciudad, d.num_telef1, d.num_telef2, d.num_telmov, ".
                    "b.num_licenc, f.nom_catlic, DATE_FORMAT( b.fec_venlic, '%d-%m-%Y' ), b.nom_epsxxx, ".
                    "b.nom_arpxxx, b.nom_pensio, b.num_pasado, b.fec_venpas, b.num_libtri, b.fec_ventri, ".
                    "b.nom_refper, b.tel_refper, d.dir_ultfot, d.obs_tercer, d.obs_aproba ".
               "FROM ".BASE_DATOS.".tab_tercer_conduc b, ".
                    "".BASE_DATOS.".tab_genera_agenci c, ".
                    "".BASE_DATOS.".tab_tercer_tercer d, ".
                    "".BASE_DATOS.".tab_genera_ciudad e, ".
                    "".BASE_DATOS.".tab_genera_catlic f, ".
                    "".BASE_DATOS.".tab_tercer_tercer g, ".
                    "".BASE_DATOS.".tab_transp_tercer v ".
              "WHERE c.cod_agenci = '1' ".
                "AND b.cod_tercer = v.cod_tercer ".
                "AND v.cod_transp = g.cod_tercer ".
                "AND b.cod_tercer = d.cod_tercer ".
                "AND d.cod_ciudad = e.cod_ciudad ".
                "AND b.num_catlic = f.cod_catlic ".
                "AND b.cod_tercer = '".$_REQUEST[conduc]."' 
       ORDER BY  v.fec_creaci DESC ";

    $consulta = new Consulta($query, $this -> conexion);
    $matriz = $consulta -> ret_matriz();

    $query = "SELECT b.nom_empre,b.tel_empre,b.num_viajes,b.num_atigue, b.nom_mercan ".
               "FROM ".BASE_DATOS.".tab_tercer_conduc a, ".
                    "".BASE_DATOS.".tab_conduc_refere b ".
              "WHERE a.cod_tercer = b.cod_conduc ".
                "AND b.cod_refere = '0' ".
                "AND a.cod_tercer = '".$_REQUEST[conduc]."' ";
    $consulta = new Consulta($query, $this -> conexion);
    $reflab0 = $consulta -> ret_matriz();

    $query = "SELECT b.nom_empre,b.tel_empre,b.num_viajes,b.num_atigue, b.nom_mercan ". 
               "FROM ".BASE_DATOS.".tab_tercer_conduc a, ".
                    "".BASE_DATOS.".tab_conduc_refere b ".
              "WHERE a.cod_tercer = b.cod_conduc ".
                "AND b.cod_refere = '1' ".
                "AND a.cod_tercer = '".$_REQUEST[conduc]."' ";
    $consulta = new Consulta($query, $this -> conexion);
    $reflab1 = $consulta -> ret_matriz();

    $query = "SELECT b.nom_empre,b.tel_empre,b.num_viajes,b.num_atigue, b.nom_mercan ".
               "FROM ".BASE_DATOS.".tab_tercer_conduc a, ".
                    "".BASE_DATOS.".tab_conduc_refere b ".
              "WHERE a.cod_tercer = b.cod_conduc ".
                "AND b.cod_refere = '2' ".
                "AND a.cod_tercer = '".$_REQUEST[conduc]."' ";
    $consulta = new Consulta($query, $this -> conexion);
    $reflab2 = $consulta -> ret_matriz();

    
    if($datbas[0][1] == '890207572')
        $d01 = "logos/logo_ceter.jpg";
    else if($datbas[0][1] == '900491068') // 900491068 NIT DE CAPITAL CARGO (CAMBIAR EN EL MOMENTO DE LLEVAR A PRODUCCION)
        $d01 = "logos/logo_capital.jpg";
    else if($datbas[0][1] == '860021912') 
        $d01 = "logos/LOGO_COOPECOL.jpg";
    elseif($datbas[0][1]=='830127357')
	    $d01 = "logos/LOGO_CARGA_LIBRE.jpg";
    elseif($datbas[0][1]=='830513736')
	    $d01 = "logos/LOGO_CARCOL.jpg";
    elseif($datbas[0][1]=='806004895')
        $d01 = "logos/LOGO_CARIBB.png";
    elseif($datbas[0][1]=='860068121')
        $d01 = "logos/LOGO_CORONA.png";
    else
        $d01 = "imagenes/logo_liquid.gif";
    
    $d0 = $datbas[0][0];
    $d1 = $datbas[0][1] === "" ? $datbas[0][1] : "NIT: ".$datbas[0][1];
    $d2 = $matriz[0][2];
    $d3 = $matriz[0][3];
    $d5 = $matriz[0][4];// tipo documento
    $d6 = $matriz[0][5]; // cedula
    $d7 = $matriz[0][6]; // nombres
    $d8 = $matriz[0][7]; // primer apellido
    $d4 = $matriz[0][8]; // segundo apellido
    $d9 = $matriz[0][9]; // Sexo
    $d10 = $matriz[0][10]; // Factor RH
    $d11 = $matriz[0][11];// Dirección Residencia
    $d12 = $matriz[0][12];//Ciudad Residencia
    $d13 = $matriz[0][13];//Telófono 1
    $d14 = $matriz[0][14];//Telófono 2
    $d15 = $matriz[0][15];//Telófono Movil:
    $d19 = $matriz[0][16];//No. de Licencia
    $d20 = $matriz[0][17]; //Categoria
    $d21 = $matriz[0][18];//Fecha de Vencimiento
    $d22 = $matriz[0][19];
    $d23 = $matriz[0][20];
    $d24 = $matriz[0][21];//fondo de pensiones
    $d44 = $matriz[0][22];
    $d45 = $matriz[0][23];//vencimiento de pasado
    $d25 = $matriz[0][24];
    $d26 = $matriz[0][25];//vencimiento de tripulacion
    $d27 = $matriz[0][26];
    $d28 = $matriz[0][27];// telefono ref personal

    $d29 = $reflab0[0][0];// empresa
    $d30 = $reflab0[0][1];//Telefono
    $d31 = $reflab0[0][2];//Viajes
    $d32 = $reflab0[0][3];//Antiguedad
    $d33 = $reflab0[0][4];// Mercancia
    $d34 = $reflab1[0][0];// empresa
    $d35 = $reflab1[0][1];//Telefono
    $d36 = $reflab1[0][2];
    $d37 = $reflab1[0][3];
    $d38 = $reflab1[0][4];
    $d39 = $reflab2[0][0];// empresa
    $d40 = $reflab2[0][1];//Telefono
    $d41 = $reflab2[0][2];
    $d42 = $reflab2[0][3];
    $d43 = $reflab2[0][4];

    $d100 = $matriz[0][29];//Observaciones Generales
    $d101 = $matriz[0][30];//Observaciones Hab/Inh

    if(!$matriz[0][28])
      $fotcon = "../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg";
    else
      $fotcon = URL_CONDUC.$matriz[0][28];

    $d66 = $fotcon;// foto Conductor

    // LLAMADO AL ARCHIVO HTML DEL FORMATO DE CODNUCTORES
    $tmpl_file = "../".DIR_APLICA_CENTRAL."/conduc/conduc.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval( $thefile );
    print $r_file;

    echo "<form name=\"form\" method=\"post\" action=\"index.php\">";
    echo "<br><br>"
        ."<table border=\"0\" width=\"100%\">"
        ."<tr>"
          ."<td align=\"center\">"
            ."<input type=\"hidden\" name=\"cod_servic\" value=\"$_REQUEST[cod_servic]\">"
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

}//FIN CLASE
   $proceso = new Imp_Conduc_Conduc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

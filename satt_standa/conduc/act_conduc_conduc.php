<?php
ini_set('memory_limit', '128M');
class act_conduc_conduc
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
      switch($GLOBALS[opcion])
      {
        case "1":
          $this -> Resultado();
        break;
        case "2":
          $this -> Captura();
        break;

        case "3":
          $this -> Actualizar();
        break;
        default:
          $this -> Buscar();
          break;
     }//FIN SWITCH
 }

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
 { echo "";
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_tercer,a.nom_tercer,a.nom_apell1,a.nom_apell2,
                   a.cod_ciudad,a.num_telef1,a.num_telmov,a.dir_domici,
                   a.dir_emailx,a.cod_estado
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
              	   ".BASE_DATOS.".tab_transp_tercer b,
              	   ".BASE_DATOS.".tab_tercer_conduc c
             WHERE a.cod_tercer = b.cod_tercer AND
             	   a.cod_tercer = c.cod_tercer
           ";

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
  $query = $query." GROUP BY 1 ORDER BY 2 Limit 1000";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE CONDUCTORES","form_item");
  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Conductore(s)",0,"t2");

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
 }

 function Captura()
 {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];

     if(!isset($GLOBALS["tipdocu"]))
       {
                     $query = "SELECT a.cod_tercer,a.cod_tipdoc,a.nom_tercer,a.abr_tercer,a.nom_apell1,
                                      a.nom_apell2,a.num_telef1,a.num_telef2,b.cod_grupsa,b.cod_tipsex,
                                      a.cod_ciudad,a.dir_domici,a.num_telmov,b.num_licenc,b.num_catlic,
                                      b.fec_venlic,b.nom_epsxxx,b.nom_arpxxx,b.nom_pensio,b.num_pasado,
                                      b.fec_venpas,b.num_libtri,b.fec_ventri,b.nom_refper,b.tel_refper,a.dir_ultfot,
                                      a.usr_creaci,a.fec_creaci,a.usr_modifi,a.fec_modifi,b.cod_califi,a.obs_tercer, b.cod_operad
                                 FROM ".BASE_DATOS.".tab_tercer_tercer a,
                                      ".BASE_DATOS.".tab_tercer_conduc b
                                WHERE a.cod_tercer = b.cod_tercer AND
                                      a.cod_tercer = '$GLOBALS[conduc]'";
                      $consulta = new Consulta($query, $this -> conexion);
                      $conductor = $consulta -> ret_arreglo();
                      $GLOBALS[nom] = $conductor[nom_tercer];
                      $GLOBALS[ciudad] = $conductor[cod_ciudad];
                      $GLOBALS[tipdocu] = $conductor[cod_tipdoc];
                      $GLOBALS[apell1] = $conductor[nom_apell1];
                      $GLOBALS[apell2] = $conductor[nom_apell2];
                      $GLOBALS[tiporh] = $conductor[cod_grupsa];

                     if($conductor[cod_tipsex] == '1')
                             $GLOBALS[tipsex] = '1';
					 $GLOBALS[operad] = $conductor[operad];
                     $GLOBALS[dir1] = $conductor[dir_domici];
                     $GLOBALS[tel] = $conductor[num_telef1];
                     $GLOBALS[tel2] = $conductor[num_telef2];
                     $GLOBALS[celu] = $conductor[num_telmov];
                     $GLOBALS[licencia] = $conductor[num_licenc];
                     $GLOBALS[catlic] = $conductor[num_catlic];
                     $GLOBALS[feclic] = $conductor[fec_venlic];
                     $GLOBALS[califi] = $conductor[cod_califi];
                     $GLOBALS[eps] = $conductor[nom_epsxxx];
                     $GLOBALS[arp] = $conductor[nom_arpxxx];
                     $GLOBALS[pensiones] = $conductor[nom_pensio];
                     $GLOBALS[pasjudi] = $conductor[num_pasado];
                     $GLOBALS[fecpas] = $conductor[fec_venpas];
                     $GLOBALS[libtripu] = $conductor[num_libtri];
                     $GLOBALS[fectripus] = $conductor[fec_ventri];
                     $GLOBALS[nomref] = $conductor[nom_refper];
                     $GLOBALS[telref] = $conductor[tel_refper];
                     $GLOBALS[fotant] = $conductor[dir_ultfot];
                     $GLOBALS[obs] = $conductor[obs_tercer];


                    $query = "SELECT b.nom_empre,b.tel_empre,b.num_viajes,b.num_atigue,b.nom_mercan
                               FROM ".BASE_DATOS.".tab_conduc_refere b
                              WHERE b.cod_conduc = '$GLOBALS[conduc]'";

                $consulta = new Consulta($query, $this -> conexion);
                $condrefe = $consulta -> ret_matriz();
                $GLOBALS[maximo] = sizeof($condrefe);
                for($i = 0; $i < $GLOBALS[maximo]; $i++)
                   {
                    $GLOBALS[empresa][$i] = $condrefe[$i][nom_empre];
                    $GLOBALS[tellab][$i] = $condrefe[$i][tel_empre];
                    $GLOBALS[viajes][$i] = $condrefe[$i][num_viajes];
                    $GLOBALS[antigue][$i] = $condrefe[$i][num_atigue];
                    $GLOBALS[mercan][$i] = $condrefe[$i][nom_mercan];
                    }

                $query = "SELECT cod_activi
                            FROM ".BASE_DATOS.".tab_tercer_activi
                           WHERE cod_tercer = '$GLOBALS[conduc]' AND
                                 (cod_activi = ".COD_FILTRO_PROPIE." OR
                                  cod_activi = ".COD_FILTRO_POSEED.")
					   ";

                $consulta = new Consulta($query, $this -> conexion);
                $condacti = $consulta -> ret_matriz();

                for($i = 0; $i < sizeof($condacti); $i++)
                {
                 if($condacti[$i][0] == COD_FILTRO_PROPIE)
                  $GLOBALS[propie] = 1;
                 else if($condacti[$i][0] == COD_FILTRO_POSEED)
                  $GLOBALS[tenedo] = 1;
                }

       }

     $inicio[0][0]=0;
     $inicio[0][1]='-';

     //ciudades

     $query = "SELECT cod_ciudad, nom_ciudad
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                 WHERE ind_estado = 1
              ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $ciudad = $consulta -> ret_matriz();

     //ciudad anterior

     $query = "SELECT cod_ciudad, nom_ciudad
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                 WHERE cod_ciudad = '$GLOBALS[ciudad]'";
     $consulta = new Consulta($query, $this -> conexion);
     $ciudad_a = $consulta -> ret_matriz();

    $ciudad = array_merge($ciudad_a,$inicio,$ciudad);

     $query = "SELECT cod_tipdoc,nom_tipdoc
                 FROM ".BASE_DATOS.".tab_genera_tipdoc
                 WHERE cod_tipdoc <> 'N'";

     $consulta = new Consulta($query, $this -> conexion);
     $tipdoc = $consulta -> ret_matriz();

     $query = "SELECT cod_tipdoc,nom_tipdoc
                 FROM ".BASE_DATOS.".tab_genera_tipdoc
                 WHERE cod_tipdoc = '$GLOBALS[tipdocu]'";

     $consulta = new Consulta($query, $this -> conexion);
     $tipdoc_a = $consulta -> ret_matriz();
     $tipdoc = array_merge($tipdoc_a,$inicio,$tipdoc);

     //listado de licencias

     $query = "SELECT cod_catlic,nom_catlic
                 FROM ".BASE_DATOS.".tab_genera_catlic
             ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $catlic = $consulta -> ret_matriz();

     //licencia anterior

     $query = "SELECT cod_catlic,nom_catlic
                FROM ".BASE_DATOS.".tab_genera_catlic
             WHERE cod_catlic = '$GLOBALS[catlic]'";
     $consulta = new Consulta($query, $this -> conexion);
     $catlic_a = $consulta -> ret_matriz();
     $catlic = array_merge($catlic_a,$inicio,$catlic);

     //calificacion del conductor
     $query = "SELECT cod_califi,nom_califi
               FROM ".BASE_DATOS.".tab_genera_califi
               ORDER BY 2";
    $consulta = new Consulta($query, $this -> conexion);
    $califis = $consulta -> ret_matriz();

    $query = "SELECT cod_califi,nom_califi
              FROM ".BASE_DATOS.".tab_genera_califi
              WHERE cod_califi = '$GLOBALS[califi]'";
    $consulta = new Consulta($query, $this -> conexion);
    if($califi = $consulta -> ret_matriz())
        $califis =array_merge($califi,$inicio,$califis);
    else
        $califis =array_merge($inicio,$califis);

      //sql tipo de sangre

    $query = "SELECT nom_tiporh,nom_tiporh
              FROM ".BASE_DATOS.".tab_genera_tiporh
              ORDER BY 2";

    $consulta = new Consulta($query, $this -> conexion);
    $tiporh = $consulta -> ret_matriz();

        //sql tipo de sangre  anterior

    $query = "SELECT nom_tiporh,nom_tiporh
            FROM ".BASE_DATOS.".tab_genera_tiporh
        WHERE nom_tiporh = '$GLOBALS[tiporh]'";
    $consulta = new Consulta($query, $this -> conexion);
    $tiporh_a = $consulta -> ret_matriz();

    $tiporh = array_merge($tiporh_a,$inicio,$tiporh);
	
	//sql operador telefonico
    $query = "SELECT cod_operad,nom_operad
            FROM ".BASE_DATOS.".tab_operad_operad
        ORDER BY 2 ";
    $consulta = new Consulta($query, $this -> conexion);
    $operad = $consulta -> ret_matriz();
	$operad = array_merge($inicio,$operad);

     //formulario de insercion

     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/conduc.js\"></script>\n";
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fecha.js\"></script>\n";

     $formulario = new Formulario ("index.php","post","ACTUALIZAR","form_conduc",'',"multipart/form-data");
     $formulario -> nueva_tabla();
	 if($conductor[dir_ultfot])
 		echo "<td align=\"center\" class=\"celda\" colspan=\"2\"><img src=\"".URL_CONDUC.$conductor[dir_ultfot]."\" alt=\"fotografia\" width=\"80\" height=\"100\" align=\"center\" ></td></tr><tr>";
 	 else
 		echo "<td align=\"center\" class=\"celda\" colspan=\"2\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg\" alt=\"fotografia\" width=\"80\" height=\"100\" align=\"center\" ></td></tr><tr>";
     $formulario -> nueva_tabla();
     $formulario -> linea("Creado Por:",0,"","25%");
     $formulario -> linea($conductor['usr_creaci'],0,"i","25%");
     $formulario -> linea("Fecha de Creaci&oacute;n:",0,"","25%");
     $formulario -> linea($conductor['fec_creaci'],1,"i","25%");
     $formulario -> linea("Actualizado por:",0,"","25%");
     $formulario -> linea($conductor['usr_mofifi'],0,"i","25%");
     $formulario -> linea("Fecha de Actualizaci&oacute;n:",0,"","25%");
     $formulario -> linea($conductor['fec_modifi'],0,"i","25%");

     $formulario -> nueva_tabla();
     $formulario -> linea("Datos B&aacute;sicos ",1,"t2");

     $formulario -> nueva_tabla();
	 $formulario -> lista("Tipo Documento", "tipdocu", $tipdoc,0,1);
     $formulario -> linea("No. Documento",0);
     $formulario -> linea($GLOBALS[conduc],1,"i");
     $formulario -> texto ("Nombres:","text","nom",0,30,50,"",$GLOBALS[nom],"","",NULL,1);
     $formulario -> texto ("Apellido 1:","text","apell1",1,20,50,"",$GLOBALS[apell1],"","",NULL,1);
     $formulario -> texto ("Apellido 2:","text","apell2",0,20,50,"",$GLOBALS[apell2]);
     $formulario -> lista ("Factor RH", "tiporh", $tiporh, 1,1);
     $formulario -> radio("Masculino","tipsex",1,$GLOBALS[tipsex],0);
     $formulario -> radio("Femenino","tipsex",2,!$GLOBALS[tipsex],1);
     $formulario -> texto ("Direcci&oacute;n:","text","dir1",0,30,80,"",$GLOBALS[dir1],"","",NULL,1);
     $formulario -> lista ("Ciudad", "ciudad", $ciudad, 1,1);
     $formulario -> texto ("Tel&eacute;fono 1:","text","tel\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,20,20,"",$GLOBALS[tel],"","",NULL,1);
     $formulario -> texto ("Tel&eacute;fono 2:","text","tel2\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,20,20,"",$GLOBALS[tel2]);
     $formulario -> texto ("Tel&eacute;fono Movil:","text","celu\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,20,20,"",$GLOBALS[celu],"","",NULL,1);
     $formulario -> lista ("Operador", "operad", $operad, 1);
	 $formulario -> archivo("Foto:","foto",12,200,"",1);
     $formulario -> lista ("Calificaci&oacute;n", "califi", $califis, 0);
     $formulario -> oculto("MAX_FILE_SIZE", "2000000", 0);

     $formulario -> nueva_tabla();
     $formulario -> linea("Datos de la Licencia",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto("N Licencia","text","licencia",0,10,30,"",$GLOBALS[licencia],"","",NULL,1);
     $formulario -> lista ("Categoria", "catlic",$catlic, 0,1);
     $formulario -> fecha_calendar("Fecha Vencimiento","feclic","form_conduc",$GLOBALS[feclic],"yyyy-mm-dd",1,0,1);

     $formulario -> nueva_tabla();
     $formulario -> linea("Datos de Seguridad Social",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto("EPS","text","eps",1,40,70,"",$GLOBALS[eps]);
     $formulario -> texto("ARP","text","arp",1,40,70,"",$GLOBALS[arp]);
     $formulario -> texto("Fondo de Pensiones","text","pensiones",1,40,70,"",$GLOBALS[pensiones]);

     $formulario -> nueva_tabla();
     $formulario -> linea("Datos Complementarios",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto("Pasado Judicial","text","pasjudi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,10,30,"",$GLOBALS[pasjudi]);
     $formulario -> fecha_calendar("Fecha Vencimiento","fecpas","form_conduc",$GLOBALS[fecpas],"yyyy-mm-dd",1);
     $formulario -> texto("Nro.Lib.Tripula","text","libtripu",0,10,30,"",$GLOBALS[libtripu]);
     $formulario -> fecha_calendar("Fecha Vencimiento","fectripu","form_conduc",$GLOBALS[fectripu],"yyyy-mm-dd",1);

     $formulario -> nueva_tabla();
     $formulario -> linea("Referencias Personales (Caso de Accidente)",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto("Nombre","text","nomref",0,30,70,"",$GLOBALS[nomref],"","",NULL,1);
     $formulario -> texto("Tel&eacute;fono","text","telref\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,10,15,"",$GLOBALS[telref],"","",NULL,1);

     $formulario -> nueva_tabla();
     $formulario -> linea("Referencias Laborales",1,"t2");

     $formulario -> nueva_tabla();
     $empresa=$GLOBALS[empresa];
     $tellab=$GLOBALS[tellab];
     $viajes=$GLOBALS[viajes];
     $antigue=$GLOBALS[antigue];
     $mercan=$GLOBALS[mercan];

     if(!isset($GLOBALS[maximo]))
               $GLOBALS[maximo]=1;

        ///////////////
        for($i=0;$i<$GLOBALS[maximo];$i++)
           {
           $formulario -> texto ("Empresa","text","empresa[$i]",0,10,40,"","$empresa[$i]");
           $formulario -> texto ("Tel&eacute;fono","text","tellab[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,8,15,"","$tellab[$i]");
           $formulario -> texto ("Viajes","text","viajes[$i]",0,3,4,"","$viajes[$i]");
           $formulario -> texto ("Antiguedad","text","antigue[$i]",0,10,20,"","$antigue[$i]");
           $formulario -> texto ("Mercanc&iacute;a","text","mercan[$i]",1,10,40,"","$mercan[$i]");
           }  //fin for

     $formulario -> nueva_tabla();
     $formulario -> botoni("Otra","form_conduc.maximo.value++; form_conduc.submit()",0);

     $formulario -> nueva_tabla();
     $formulario -> linea("Otras Actividades",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> caja("Propietario","propie",1,$GLOBALS[propie],0);
     $formulario -> caja("Tenedor","tenedo",1,$GLOBALS[tenedo],1);

     $formulario -> nueva_tabla();
     $formulario -> texto ("Observaciones","textarea","obs",1,60,2,"",$GLOBALS[obs]);

     $formulario -> nueva_tabla();
     $formulario -> oculto("fec_actual",date("Y-m-d"),0);
     $formulario -> oculto("conduc","$GLOBALS[conduc]",0);
     $formulario -> oculto("fotant","$GLOBALS[fotant]",0);
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("maximo","$GLOBALS[maximo]",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
     $formulario -> oculto("opcion",2,0);
     $formulario -> botoni("Aceptar","aceptar_update()",0);
     $formulario -> botoni("Borrar","form_conduc.reset()",1);
     $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA


 function Actualizar()
 {
        $datos_usuario=$this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        $query = "SELECT cod_paisxx, cod_depart, cod_ciudad
                  FROM ".BASE_DATOS.".tab_genera_ciudad
                  WHERE cod_ciudad = '$GLOBALS[ciudad]'";

    $consulta = new Consulta($query, $this -> conexion);
    $padeci = $consulta -> ret_arreglo();

     if($GLOBALS[foto])
   {
    if(move_uploaded_file($GLOBALS[foto],URL_CONDUC.$GLOBALS[conduc].".jpg"))
     $GLOBALS[foto] = "'".$GLOBALS[conduc].".jpg'";
    else
     $GLOBALS[foto] = "NULL";
   }
   else
    $GLOBALS[foto] = "NULL";

         $empresa=$GLOBALS[empresa];
         $tellab=$GLOBALS[tellab];
         $viajes=$GLOBALS[viajes];
         $antigue=$GLOBALS[antigue];
         $mercan=$GLOBALS[mercan];

    $fec_actual = date("Y-m-d H:i:s");
    $GLOBALS[abr]=$GLOBALS[apell1]." ".$GLOBALS[nom];

   $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
                                     SET cod_tipdoc = '$GLOBALS[tipdocu]',
                                               nom_apell1 = '$GLOBALS[apell1]',
                                               nom_apell2 = '$GLOBALS[apell2]',
                                               nom_tercer = '$GLOBALS[nom]',
                                               abr_tercer = '$GLOBALS[abr]',
                    dir_domici = '$GLOBALS[dir1]',
                    num_telef1 = '$GLOBALS[tel]',
                    num_telef2 = '$GLOBALS[tel2]',
                    num_telmov = '$GLOBALS[celu]',
                    cod_paisxx = '".$padeci[0]."',
                    cod_depart = '".$padeci[1]."',
                    cod_ciudad = '".$padeci[2]."',
                    dir_ultfot = $GLOBALS[foto],

                    obs_tercer = '$GLOBALS[obs]',

                                                  usr_modifi = '$GLOBALS[usuario]',

                                                  fec_modifi = '$fec_actual'

              WHERE cod_tercer = '$GLOBALS[conduc]'";

  $insercion = new Consulta($query, $this -> conexion,"BR");

  $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi
                   WHERE cod_tercer = '$GLOBALS[conduc]' AND
                         (cod_activi = ".COD_FILTRO_PROPIE." OR
                          cod_activi = ".COD_FILTRO_POSEED.")";
   $consulta = new Consulta($query, $this -> conexion, "R");

     if($GLOBALS[propie])
     {
        $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
              VALUES ('$GLOBALS[conduc]',".COD_FILTRO_PROPIE.")";
       $insercion = new Consulta($query, $this -> conexion,"R");
     }

     if($GLOBALS[tenedo])
     {
       $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
              VALUES ('$GLOBALS[conduc]',".COD_FILTRO_POSEED.")";
       $insercion = new Consulta($query, $this -> conexion,"R");
     }

  if(!$GLOBALS[califi])
      $GLOBALS[califi] = "NULL";
  else
      $GLOBALS[califi] = "'".$GLOBALS[califi]."'";

     $query = "UPDATE ".BASE_DATOS.".tab_tercer_conduc
                  SET cod_tipsex = '$GLOBALS[tipsex]',
                      cod_grupsa = '$GLOBALS[tiporh]',
                      num_licenc = '$GLOBALS[licencia]',
                      num_catlic = '$GLOBALS[catlic]',
                      fec_venlic = '$GLOBALS[feclic]',
                      cod_califi = $GLOBALS[califi],
                      num_libtri = '$GLOBALS[libtripu]',
                      fec_ventri = '$GLOBALS[fectripu]',
                      nom_epsxxx = '$GLOBALS[eps]',
                      nom_arpxxx = '$GLOBALS[arp]',
                      nom_pensio = '$GLOBALS[pensiones]',
                      num_pasado = '$GLOBALS[pasjudi]',
                      fec_venpas = '$GLOBALS[fecpas]',
                      nom_refper = '$GLOBALS[nomref]',
                      tel_refper = '$GLOBALS[telref]',
					  cod_operad = '$GLOBALS[operad]',
                      usr_modifi = '$GLOBALS[usuario]',
                      fec_modifi  = '$fec_actual'
                WHERE cod_tercer = '$GLOBALS[conduc]'";
    $insercion = new Consulta($query, $this -> conexion,"R");

     $query = "DELETE FROM ".BASE_DATOS.".tab_conduc_refere
                     WHERE cod_conduc = '$GLOBALS[conduc]'";
     $consulta = new Consulta($query, $this -> conexion, "R");

    for($i=0;$i<$GLOBALS[maximo];$i++)
       {
       if($empresa[$i] != Null)
          {
           $query = "INSERT INTO ".BASE_DATOS.".tab_conduc_refere(
                                                     cod_conduc,cod_refere,nom_empre,tel_empre,num_viajes,num_atigue,
                                                      nom_mercan,usr_creaci,fec_creaci,usr_modifi,fec_modifi)
                                              VALUES ('$GLOBALS[conduc]','$i','$empresa[$i]','$tellab[$i]','$viajes[$i]',
                                                '$antigue[$i]','$mercan[$i]','$GLOBALS[usuario]',
                                                '$fec_actual','$GLOBALS[usuario]','$fec_actual')";
                 $insercion = new Consulta($query, $this -> conexion,"R");
          }
       }

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Conductor</a></b>";

   $mensaje =  "El Conductor <b>".$GLOBALS[abr]."</b> Se Actualizo con Exito".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ACTUALIZAR CONDUCTORES",$mensaje);
  }
 }






}//FIN CLASE act_conduc_conduc

$proceso = new act_conduc_conduc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>

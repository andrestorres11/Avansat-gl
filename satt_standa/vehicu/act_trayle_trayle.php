<?php
class Proc_traylers
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
          $this -> Capturar();
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

   $formulario = new Formulario ("index.php","post","BUSCAR REMOLQUES","form_act");
   $formulario -> linea("Seleccionar Filtro Para la Busqueda de Remolques",0,"t2");

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
   $formulario -> radio("Remolque","fil",1,0,1);
   $formulario -> radio("Activos","fil",2,0,0);
   $formulario -> texto ("","text","remolq",1,50,255,"","");
   $formulario -> radio("Inactivos","fil",3,0,1);
   $formulario -> radio("Pendientes","fil",4,0,1);
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

  $query = "SELECT a.num_trayle,b.nom_martra,a.ano_modelo,a.nro_ejes,
  				   a.nom_propie,a.ind_estado
              FROM ".BASE_DATOS.".tab_vehige_trayle a,
              	   ".BASE_DATOS.".tab_vehige_martra b,
              	   ".BASE_DATOS.".tab_transp_trayle c
           	 WHERE a.cod_marcax = b.cod_martra AND
				   a.num_trayle = c.num_trayle
           ";

  if($GLOBALS[transp])
   $query .= " AND c.cod_transp = '".$GLOBALS[transp]."'";
  if($GLOBALS[fil] == 1)
   $query = $query." AND a.num_trayle LIKE '%".$GLOBALS[remolq]."%'";
  else if($GLOBALS[fil] == 2)
   $query = $query." AND a.ind_estado = ".COD_ESTADO_ACTIVO."";
  else if($GLOBALS[fil] == 3)
   $query = $query." AND a.ind_estado = ".COD_ESTADO_INACTI."";
  else if($GLOBALS[fil] == 4)
   $query = $query." AND a.ind_estado = ".COD_ESTADO_PENDIE."";

  $query = $query." GROUP BY 1 ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE REMOLQUES","form_item");
  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Remolque(s)",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Remolque",0,"t");
  $formulario -> linea("Marca",0,"t");
  $formulario -> linea("Modelo",0,"t");
  $formulario -> linea("Nro Ejes",0,"t");
  $formulario -> linea("Propietario",0,"t");
  $formulario -> linea("Estado",1,"t");

  for($i = 0; $i < sizeof($matriz); $i++)
  {
   if($matriz[$i][5] != COD_ESTADO_ACTIVO)
    $estilo = "ie";
   else
    $estilo = "i";

   if($matriz[$i][5] == COD_ESTADO_ACTIVO)
    $estado = "Activo";
   else if($matriz[$i][5] == COD_ESTADO_INACTI)
    $estado = "Inactivo";
   else if($matriz[$i][5] == COD_ESTADO_PENDIE)
    $estado = "Pendiente";

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&trayle=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario -> linea($matriz[$i][0],0,$estilo);
   $formulario -> linea($matriz[$i][1],0,$estilo);
   $formulario -> linea($matriz[$i][2],0,$estilo);
   $formulario -> linea($matriz[$i][3],0,$estilo);
   $formulario -> linea($matriz[$i][4],0,$estilo);
   $formulario -> linea($estado,1,$estilo);
  }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }

 function Capturar()
 {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];
  $inicio[0][0]=0;
  $inicio[0][1]='-';

  //Query para traer los datos del  Trayler
  $query = "SELECT a.num_trayle,a.cod_marcax,a.ano_modelo,a.nro_ejes,a.tra_pesoxx,
                   a.tra_anchox,a.tra_altoxx,a.tra_largox,a.tra_volpos,a.tra_capaci,
                   a.tip_tramit,a.cod_carroc,a.ser_chasis,a.nom_propie,a.cod_config,
                   a.dir_fottra,a.cod_colore
           FROM ".BASE_DATOS.".tab_vehige_trayle a
           WHERE a.num_trayle = '$GLOBALS[trayle]'";
   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();


//Query para pedir la marca del Trayler
  $query = "SELECT cod_martra, nom_martra
           FROM ".BASE_DATOS.".tab_vehige_martra
           where cod_martra = '".$matriz[0][1]."'";
  $consulta = new Consulta($query, $this -> conexion);
  $marca_a = $consulta -> ret_matriz();

//Query para pedir la marca del Trayler
  $query = "SELECT cod_martra, nom_martra
           FROM ".BASE_DATOS.".tab_vehige_martra
           ORDER BY 2";
  $consulta = new Consulta($query, $this -> conexion);
  $marca = $consulta -> ret_matriz();
  $marca=array_merge($marca_a,$inicio,$marca);

  //Query para pedir el color del trayler
  $query = "SELECT cod_colorx, nom_colorx
            FROM ".BASE_DATOS.".tab_vehige_colore
            WHERE cod_colorx = '".$matriz[0][16]."'";
  $consulta = new Consulta($query, $this -> conexion);
  $color_a = $consulta -> ret_matriz();

  //Query para pedir el color del trayler
  $query = "SELECT cod_colorx, nom_colorx
            FROM ".BASE_DATOS.".tab_vehige_colore
            ORDER BY 2";
  $consulta = new Consulta($query, $this -> conexion);
  $color = $consulta -> ret_matriz();
  $color = array_merge($color_a,$inicio,$color);

  //Query para pedir el tipo de carroceria del trayler
  $query = "SELECT cod_carroc, nom_carroc
            FROM ".BASE_DATOS.".tab_vehige_carroc
            WHERE cod_carroc = '".$matriz[0][11]."'";
  $consulta     = new Consulta($query, $this -> conexion);
  $carroc_a = $consulta -> ret_matriz();

  //Query para pedir el tipo de carroceria del trayler
  $query = "SELECT cod_carroc, nom_carroc
            FROM ".BASE_DATOS.".tab_vehige_carroc
            ORDER BY 2";
  $consulta = new Consulta($query, $this -> conexion);
  $tip_carroc = $consulta -> ret_matriz();
  $tip_carroc = array_merge($carroc_a,$inicio,$tip_carroc);


  //Query para la configuracion actual
  $query = "SELECT a.num_config,a.num_config
              FROM ".BASE_DATOS.".tab_vehige_config a
              WHERE a.num_config = '".$matriz[0][14]."' ";
  $query = $query." ORDER BY 2 ";
  $consulta = new Consulta($query, $this -> conexion);
  $config_a = $consulta -> ret_matriz();

  //Query para las configuraciones
  $query = "SELECT a.num_config,a.num_config
              FROM ".BASE_DATOS.".tab_vehige_config a
              WHERE a.num_config = '2' OR
                    a.num_config = '3' OR
                    a.num_config = '4'  ";
  $query = $query." ORDER BY 2 ";

  $consulta = new Consulta($query, $this -> conexion);
  $config = $consulta -> ret_matriz();
  $config = array_merge($config_a,$inicio,$config);

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/traylers.js\"></script>\n";
  $formulario = new Formulario ("index.php","post\" enctype=\"multipart/form-data\" ","ACTUALIZAR TRAYLERS","form_trayler");

  //para enviar los datos basicos del servicio
  //y si se elige una marca antes de terminar
  //la carga de todo el formulario

  $formulario -> oculto("cod_servic","$GLOBALS[cod_servic]",0);
  $formulario -> oculto("window","central",0);

  $formulario -> linea("Datos B&aacute;sicos",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> texto("Nro.Remolque:","text","traydisa\" disabled ",1,10,10,"",$matriz[0][0]);
  $formulario -> oculto("trayler",$matriz[0][0],1);

  $formulario -> nueva_tabla();
  $formulario -> lista("Marca:","marca",$marca,0,1);
  $formulario -> texto("Modelo:","text","modelo\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,4,"",$matriz[0][2],"","",NULL,1);
  $formulario -> texto("Peso Vacio(Tn):","text","peso\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,5,10,"",$matriz[0][4],"","",NULL,1);
  $formulario -> texto("Ancho:","text","ancho\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,10,"",$matriz[0][5],"","",NULL,1);
  $formulario -> texto("Alto:","text","alto\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,5,10,"",$matriz[0][6],"","",NULL,1);
  $formulario -> texto("Largo:","text","largo\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,10,"",$matriz[0][7],"","",NULL,1);
  $formulario -> texto("Vol.Posterior:","text","volpos",0,5,10,"",$matriz[0][8]);
  $formulario -> texto("Capacidad (Tn):","text","capaci\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,10,"",$matriz[0][9],"","",NULL,1);
  $formulario -> texto("Tipo Tramite:","text","tiptram",0,5,50,"",$matriz[0][10]);
  $formulario -> lista("Carrocer&iacute;a:", "carroc",$tip_carroc,1,1);
  $formulario -> texto("Serie Chasis:","text","chasis",0,22,30,"",$matriz[0][12]);
  $formulario -> texto("Propietario:","text","propie",1,22,50,"",$matriz[0][13],"","",NULL,1);
  $formulario -> lista("Configuraci&oacute;n:","config",$config,0);
  $formulario -> lista("Color:", "color",$color,1);
  $formulario -> archivo("Foto Trayler:","fot_trayle",12,200,'',0);

  $formulario -> nueva_tabla();
  $formulario -> linea("Foto del Remolque",1,"t2");

  if(!$matriz[0][15])
   $matriz[0][15] = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
  else
   $matriz[0][15] = URL_REMOLQ.$matriz[0][15];

  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$matriz[0][15]."\" alt=\"fotografia\" width=\"120\" height=\"100\" align=\"center\" ></td>";

  $formulario -> nueva_tabla();
  $formulario -> oculto("MAX_FILE_SIZE","200000",0);
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("opcion",2,0);
  $formulario -> oculto("cod_servic","$GLOBALS[cod_servic]",0);

  $formulario -> oculto("trayle","".$matriz[0][0]."",0);

  $formulario -> oculto("window","central",0);
  $formulario -> botoni("Actualizar","actualizar_form()",0);
  $formulario -> botoni("Limpiar","form_trayler.reset()",1);
  $formulario -> cerrar();
 }

 function Actualizar()
 {

  $fec_actual = date("Y-m-d H:i:s");

   if($GLOBALS[fot_trayle])
   {
    if(move_uploaded_file($GLOBALS[fot_trayle],URL_REMOLQ.$GLOBALS[trayler].".jpg"))
     $GLOBALS[fot_trayle] = "'".$GLOBALS[trayler].".jpg'";
    else
     $GLOBALS[fot_trayle] = "NULL";
   }
   else
    $GLOBALS[fot_trayle] = "NULL";

          if( strlen($GLOBALS[config])== 3 )
        {     $ejes = substr($GLOBALS[config],2,1); }
        else
        {     $ejes = $GLOBALS[config]; }

 $query = "UPDATE ".BASE_DATOS.".tab_vehige_trayle
               SET num_trayle = '$GLOBALS[trayler]',
                   cod_marcax = '$GLOBALS[marca]',
                   cod_colore = '$GLOBALS[color]',
                   cod_carroc = '$GLOBALS[carroc]',
                   ano_modelo = '$GLOBALS[modelo]',
                   nro_ejes   = '$ejes',
                   dir_fottra = ".$GLOBALS[fot_trayle].",
                   ser_chasis = '$GLOBALS[chasis]',
                   tra_anchox = '$GLOBALS[ancho]',
                   tra_altoxx = '$GLOBALS[alto]',
                   tra_largox = '$GLOBALS[largo]',
                   tra_volpos = '$GLOBALS[volpos]',
                   tra_pesoxx = '$GLOBALS[peso]',
                   tra_capaci = '$GLOBALS[capaci]',
                   tip_tramit = '$GLOBALS[tiptram]',
                   nom_propie = '$GLOBALS[propie]',
                   cod_config = '$GLOBALS[config]',
                   usr_modifi = '$GLOBALS[usuario]',
                   fec_modifi = '$fec_actual'
             WHERE num_trayle = '$GLOBALS[trayle]'";

  $insercion = new Consulta($query, $this -> conexion,"BR");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Remolque</a></b>";

   $mensaje = "El Remolque <b>".$GLOBALS[trayler]."</b> se Actualizo con Exito<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("ACTUALIZAR REMOLQUE",$mensaje);
  }
  else
   $consulta = new Consulta ("ROLLBACK", $this -> conexion);

}//FIN FUNCTION

}//FIN CLASE Proc_traylers
     $proceso = new Proc_traylers($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php
/*! \file: eli_pcontr_pcontr.php
 *  \brief: Elimina puestos de control Padres
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \class: Proc_contro
 *  \brief: Elimina puestos de control Padres
 */
class Proc_contro
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

  

  /*! \fn: principal
   *  \brief: 
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
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
          $this -> Eliminar();
          break;
       }//FIN SWITCH
    }// FIN ELSE GLOBALS OPCION
  }//FIN FUNCION PRINCIPAL

  /*! \fn: Buscar
   *  \brief: Formulario de los filtros para realizar busqueda
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \param: 
   *  \return: 
   */
  function Buscar()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/contro.js\"></script>\n";
    $formulario = new Formulario ("index.php","post","BUSCAR PUESTOS DE CONTROL","form_list");
    $formulario -> linea("Digite el Nombre del P/C para Iniciar la B&uacute;squeda",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Texto","text","contro",0,50,255,"","");
    $formulario -> caja ("Puestos Virtuales:","virtua",1,0,0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",1,0);
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> botoni("Buscar","form_list.submit()",0);
    $formulario -> cerrar();
  }

  /*! \fn: Resultado
   *  \brief: Resultado de la busqueda
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 25/05/2015
   *  \param: 
   *  \param: 
   *  \return: 
   */
  function Resultado()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();
      $cond1 = " AND a.cod_contro IN( SELECT cod_contro FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_contro ) ";
    }

    $query = "SELECT a.cod_contro, a.nom_contro, a.dir_contro, 
                     a.tel_contro, a.nom_encarg, a.cod_colorx, 
                     a.ind_estado, a.val_senvia, a.dir_senti1, 
                     a.dir_senti2, 
                     if(a.ind_virtua = '0','Fisico','Virtual'),
                     if(a.ind_urbano = '1',' - Urbano','') 
                FROM ".BASE_DATOS.".tab_genera_contro a
               WHERE a.nom_contro LIKE '%$_REQUEST[contro]%' 
                 AND a.cod_contro != ".CONS_CODIGO_PCLLEG." ".$cond1."
                 AND a.ind_pcpadr = '1' ";

    $query .= $_REQUEST[virtua] != NULL ? " AND a.ind_virtua = '1' " : " AND a.ind_virtua = '0' ";
    $query .= " ORDER BY 2";
    $consec = new Consulta($query, $this -> conexion);
    $mArrayData = $consec -> ret_matrix('i');


    $mArrayTitu = array('C&oacute;digo', 'Descripci&oacute;n', 'Estado', 'Direcci&oacute;n', 'Sentidos Viales', 'Dir. Sentido 1', 'Dir. Sentido 2', 'Tel&eacute;fono', 'Encargado', 'Puesto');

    #Inico Dibuja tabla 
    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

    $mHtml .=   '<tr>';
    foreach ($mArrayTitu as $value) {
      $mHtml .= '<th class="CellHead">'.$value.'</th>';
    }
    $mHtml .=   '</tr>';

    foreach ($mArrayData as $row) 
    {
      if($row[6] == COD_ESTADO_ACTIVO)
        $estado = "Activo";
      else if($row[6] == COD_ESTADO_INACTI)
        $estado = "Inactivo";

      $mContr = "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&contro=".$row[0]."&opcion=2 \"target=\"centralFrame\">".$row[0]."</a>";

      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo">'.$mContr.'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[1].'</td>';
      $mHtml .=   '<td class="cellInfo">'.$estado.'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[2].'</td>';
      $mHtml .=   '<td class="cellInfo">'. ($row[7] != NULL ? $row[7] : 'Sin Informaci&oacute;n') .'</td>';
      $mHtml .=   '<td class="cellInfo">'. ($row[8] != NULL ? $row[8] : '-') .'</td>';
      $mHtml .=   '<td class="cellInfo">'. ($row[9] != NULL ? $row[9] : '-') .'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[3].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[4].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[10].$row[11].'</td>';
      $mHtml .= '</tr>';
    }

    $mHtml .= '</table>';
    #Fin Dibuja tabla

    $formulario = new Formulario ("index.php","post","RESULTADO DE LA CONSULTA","form_item");
    $formulario -> linea("Se Encontro un Total de ".sizeof($mArrayData)." Puesto(s) de Control",0,"t2");

    echo $mHtml;

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> cerrar(); 
  }

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $query = "SELECT a.nom_contro,'',CONCAT(c.nom_ciudad,' (',LEFT(d.nom_depart,4),')'),
		    a.nom_encarg,a.dir_contro,a.tel_contro,a.val_longit,
		    a.val_latitu,a.val_temper,a.val_altitu,a.dir_fotopc,
		    if(a.ind_virtua = '1','Virtual','Fisico'),
		    if(a.ind_estado = '1','Activo','Inactivo'),
		    if(a.ind_urbano = '1',' - Urbano','')
	       FROM ".BASE_DATOS.".tab_genera_contro a,
		    ".BASE_DATOS.".tab_genera_ciudad c,
		    ".BASE_DATOS.".tab_genera_depart d
	      WHERE a.cod_ciudad = c.cod_ciudad AND
		    c.cod_depart = d.cod_depart AND
		    c.cod_paisxx = d.cod_paisxx AND
		    a.cod_contro = ".$GLOBALS[contro]."
	    ";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $contracc = 0;

  $query = "SELECT a.cod_contro
	      FROM ".BASE_DATOS.".tab_genera_rutcon a
	     WHERE a.cod_contro = ".$GLOBALS[contro]."
	   ";

  $consec = new Consulta($query, $this -> conexion);
  $rutcon = $consec -> ret_matriz();

  if(!$rutcon)
  {
   $query = "SELECT a.cod_contro
	      FROM ".BASE_DATOS.".tab_despac_seguim a
	     WHERE a.cod_contro = ".$GLOBALS[contro]."
	   ";

   $consec = new Consulta($query, $this -> conexion);
   $seguim = $consec -> ret_matriz();

   if($seguim)
    $contracc = 1;
  }
  else
   $contracc = 1;

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/contro.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","ELIMINAR PUESTO DE CONTROL","form_item");

   if($matriz[0][10])
   {
    $formulario -> linea("Fotografia del Puesto de Control",1,"t2");
    $formulario -> nueva_tabla();
    $formulario -> imagen("Fotografia",URL_PCTCON.$matriz[0][10],"Imagen no Disponible",150,150,0,"",1);
   }
   $formulario -> linea("Datos del Puesto de Control",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea($GLOBALS[contro],0,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("Encargado",0,"t");
   $formulario -> linea($matriz[0][3],1,"i");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][4],0,"i");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea($matriz[0][5],1,"i");
   $formulario -> linea("Longitud",0,"t");
   $formulario -> linea($matriz[0][6],0,"i");
   $formulario -> linea("Latitud",0,"t");
   $formulario -> linea($matriz[0][7],1,"i");
   $formulario -> linea("Temperatura",0,"t");
   $formulario -> linea($matriz[0][8],0,"i");
   $formulario -> linea("Altimetria",0,"t");
   $formulario -> linea($matriz[0][9],1,"i");
   $formulario -> linea("Puesto",0,"t");
   $formulario -> linea($matriz[0][11].$matriz[0][13],0,"i");
   $formulario -> linea("Estado",0,"t");
   $formulario -> linea($matriz[0][12],1,"i");

   //Manejo de la Interfaz Aplicaciones SAT
/*   $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

   if($interfaz -> totalact > 0)
    $formulario -> linea("El Sistema Tiene Interfases Activas",1,"t2");

   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    $formulario -> nueva_tabla();

    $homolo = $interfaz -> getHomoloTranspPC($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS[contro]);

    if($homolo["PCHomolo"] != -2)
     echo "<div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>El Puesto de Control Seleccionado Se Encuentra Homologado en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".</small></div>";
    else
     echo "<div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\"><b>El Puesto de Control Seleccionado No Se Encuentra Homologado en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".</small></div>";
   }
*//*
   //Manejo de la Interfaz GPS
    $interf_gps = new Interfaz_GPS();
    $interf_gps -> Interfaz_GPS_envio(NIT_TRANSPOR,BASE_DATOS,$usuario,$this -> conexion);

    $indtit = 1;

    if($interf_gps -> cant_interf > 0)
    {
    	for($i = 0; $i < $interf_gps -> cant_interf; $i++)
    	{
	    if($indtit)
	    {
	    	$formulario -> nueva_tabla();
    	    $formulario -> linea("Operadores GPS - Homologaci&oacute;n de Zonas",1,"t2");
		$indtit = 0;
	    }

	    $zona_homolo = $interf_gps -> getHomozonasxx($interf_gps -> cod_operad[$i][0],NIT_TRANSPOR,$GLOBALS[contro]);

	    if($zona_homolo)
	    {
		$formulario -> linea("<div align = \"center\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">El Puesto de Control se Encuentra Homologado con la Zona :: <b>".$zona_homolo[0][1]."</b> :: del Operador <b>".$interf_gps -> nom_operad[$i][0]."</b>.</div>",1);
	    }
	    else
	    {
		$formulario -> linea("<div align = \"center\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">El Puesto de Control no se Encuentra Homologado con el Operador <b>".$interf_gps -> nom_operad[$i][0]."</b>.</div>",1);
	    }
    	}
    }
*/
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("contro",$GLOBALS[contro],0);
   $formulario -> oculto("nombre",$matriz[0][1],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   if($contracc)
    $formulario -> linea("El Puesto de Control Posee Transacciones Actualmente. Imposible Eliminar.",1,"e");
   else
    $formulario -> botoni("Eliminar","if(confirm('Esta Seguro de Elimnar el Puesto de Control.?')){form_item.submit()}",0);
   $formulario -> botoni("Cancelar","history.go(-1)",1);
   $formulario -> cerrar();
 }

 function Eliminar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $datos_usuario = $this -> usuario -> retornar();
  $usuario=$datos_usuario["cod_usuari"];

  $query = "DELETE FROM ".BASE_DATOS.".tab_genera_contro
             WHERE cod_contro = '$GLOBALS[contro]'";
  $insercion = new Consulta($query, $this -> conexion,"BR");

  //Manejo de la Interfaz Aplicaciones SAT
/*  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$usuario,$this -> conexion);

  for($i = 0; $i < $interfaz -> totalact; $i++)
  {
   $homolo = $interfaz -> getHomoloTranspPC($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS[contro]);

   if($homolo["PCHomolo"] != -2)
   {
    $resultado_sat = $interfaz -> eliHomoloPC($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS[contro],$homolo["PCHomolo"]);

    if($resultado_sat["Confirmacion"] == "OK")
     $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Elimino la Homologacion del Puesto de Control en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
    else
     $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Eliminar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
   }
  }
*/
  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Eliminar Otro Puesto de Control</a></b>";

   $mensaje = "El Puesto de Control <b>".$GLOBALS[nombre]."</b> se Elimino con Exito<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("ELIMINAR PUESTO DE CONTROL",$mensaje);
  }

 }

}//FIN CLASE PROC_CONTRO
   $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
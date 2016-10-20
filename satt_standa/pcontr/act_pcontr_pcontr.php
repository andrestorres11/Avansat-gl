<?php
/*! \file: act_pcontr_pcontr.php
 *  \brief: Actualiza puestos de control Padres
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \class: Proc_contro
 *  \brief: Actualiza puestos de control Padres
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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/act_pcontr_pcontr.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

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
        $this -> Buscar();
        break;
    }//FIN SWITCH
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
		$formulario -> caja ("Puestos Virtuales:","virtua",0,0,1);

    $formulario -> radio ("Puestos Padres:","ind_pcpadr\" id=\"ind_pcpadrID","1",1,0);
    $formulario -> radio ("Puestos Hijos:", "ind_pcpadr\" id=\"ind_pcpadrID","0",0,1);

		$formulario -> nueva_tabla();
		$formulario -> oculto("usuario","$usuario",0);
		$formulario -> oculto("opcion",1,0);
		$formulario -> oculto("valor",$valor,0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
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
                 AND a.ind_pcpadr = '{$_REQUEST[ind_pcpadr]}' ";

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
      else
        $estado = "Inactivo";

      $mContr = "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&contro=".$row[0]."&opcion=2 \"target=\"centralFrame\">".$row[0]."</a>";

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
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> cerrar();
  }

  function Datos()
  {
      $datos_usuario = $this -> usuario -> retornar();
      $usuario=$datos_usuario["cod_usuari"];

      $inicio[0][0]=0;
      $inicio[0][1]='-';

      $query = " SELECT a.nom_contro,'',a.cod_ciudad,a.nom_encarg,
                        a.dir_contro,a.tel_contro,a.val_longit,a.val_latitu,
                        a.val_temper,a.val_altitu,a.dir_fotopc,a.ind_virtua,
                        a.ind_estado,a.ind_urbano,a.cod_colorx, 
                        a.dir_senti1,a.dir_senti2,a.val_senvia, a.ind_pcpadr,
                        a.url_wazexx,a.url_google
                   FROM ".BASE_DATOS.".tab_genera_contro a
                  WHERE a.cod_contro = ".$_REQUEST[contro]."
      ";

      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();

      if(!$_REQUEST[nom])
        $_REQUEST[nom] = $matriz[0][0];
      if(!$_REQUEST[ciudad])
        $_REQUEST[ciudad] = $matriz[0][2];
      if(!$_REQUEST[enc])
        $_REQUEST[enc] = $matriz[0][3];
      if(!$_REQUEST[dir])
        $_REQUEST[dir] = $matriz[0][4];
      if(!$_REQUEST[tel])
        $_REQUEST[tel] = $matriz[0][5];
      if(!$_REQUEST[longi])
        $_REQUEST[longi] = $matriz[0][6];
      if(!$_REQUEST[latit])
        $_REQUEST[latit] = $matriz[0][7];
      if(!$_REQUEST[tempe])
        $_REQUEST[tempe] = $matriz[0][8];
      if(!$_REQUEST[altim])
        $_REQUEST[altim] = $matriz[0][9];
      if(!$_REQUEST[virtua])
        $_REQUEST[virtua] = $matriz[0][11];
      if(!$_REQUEST[activo])
        $_REQUEST[activo] = $matriz[0][12];
      if(!$_REQUEST[urbano])
        $_REQUEST[urbano] = $matriz[0][13];
      if(!$_REQUEST[color])
        $_REQUEST[color] = $matriz[0][14];
      if(!$_REQUEST[sent1])
        $_REQUEST[sent1] = $matriz[0][15];
      if(!$_REQUEST[sent2])
        $_REQUEST[sent2] = $matriz[0][16];
      if(!$_REQUEST[sentV])
        $_REQUEST[sentV] = $matriz[0][17];
      if(!$_REQUEST[ind_pcpadr])
        $_REQUEST[ind_pcpadr] = $matriz[0][ind_pcpadr];
      if(!$_REQUEST[url_wazexx])
        $_REQUEST[url_wazexx] = $matriz[0][url_wazexx];
      if(!$_REQUEST[url_google])
        $_REQUEST[url_google] = $matriz[0][url_google];

      $mArraySentido = array(0 => array('', '---'), 1 => array('S-N/N-S', 'S-N/N-S'), 2 => array('S-N', 'S-N'), 3 => array('N-S', 'N-S'), 4 => array('E-W/W-E', 'E-W/W-E'), 5 => array('E-W', 'E-W'), 6 => array('W-E', 'W-E') );
      $mArrayActual  = array(0 => array($_REQUEST[sentV], $_REQUEST[sentV]) );
      $sentidoVial   = array_merge($mArrayActual, $mArraySentido );

      $query = "SELECT a.cod_ciudad,CONCAT(a.nom_ciudad,' (',LEFT(b.nom_depart,4),')')
                  FROM ".BASE_DATOS.".tab_genera_ciudad a,
                       ".BASE_DATOS.".tab_genera_depart b
                 WHERE a.cod_depart = b.cod_depart AND
                       a.cod_paisxx = b.cod_paisxx AND
                       a.ind_estado = '1'
              ORDER BY 2
      ";

      $consulta = new Consulta($query, $this -> conexion);
      $ciudades = $consulta -> ret_matriz();

      $ciudades = array_merge($inicio,$ciudades);

      if($_REQUEST[ciudad])
      {
        $query = "SELECT a.cod_ciudad,CONCAT(a.nom_ciudad,' (',LEFT(b.nom_depart,4),')')
                  FROM ".BASE_DATOS.".tab_genera_ciudad a,
                 ".BASE_DATOS.".tab_genera_depart b
                 WHERE a.cod_depart = b.cod_depart AND
                 a.cod_paisxx = b.cod_paisxx AND
                 a.cod_ciudad = ".$_REQUEST[ciudad]."
         ";

        $consulta = new Consulta($query, $this -> conexion);
        $ciudades_a = $consulta -> ret_matriz();

        $ciudades = array_merge($ciudades_a,$ciudades);

      }

      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/contro.js\"></script>\n";

      $formulario = new Formulario ("index.php\" enctype=\"multipart/form-data\"","post","ACTUALIZAR PUESTOS DE CONTROL","form_insert\" id=\"form_insertID");

      if($matriz[0][10])
      {
        $formulario -> linea("Fotografia del Puesto de Control",1,"t2");
        $formulario -> nueva_tabla();
        $formulario -> imagen("Fotografia",URL_PCTCON.$matriz[0][10],"Imagen no Disponible",150,150,0,"",1);
      }

      $formulario -> nueva_tabla();
      $formulario -> linea("Datos B&aacute;sicos",1,"t2");

      $formulario -> nueva_tabla();
      $formulario -> linea ("C&oacute;digo",0);
      $formulario -> linea ($_REQUEST[contro],0,"i");
      $formulario -> texto ("Nombre:","text","nom",1,50,100,"",$_REQUEST[nom]);
      $formulario -> lista ("Ciudad:", "ciudad", $ciudades, 0);
      $formulario -> lista ("Sentido Vial:", "sentido\" id=\"sentidoID", $sentidoVial, 1);
      $formulario -> texto ("Sentido 1:","text","sent1\" id=\"sent1ID",0,50,250,"",$_REQUEST[sent1]);
      $formulario -> texto ("Sentido 2:","text","sent2\" id=\"sent2ID",1,50,250,"",$_REQUEST[sent2]);
      $formulario -> texto ("Direccion:","text","dir",0,50,400,"",$_REQUEST[dir]);
      $formulario -> texto ("Nombre del Encargado:","text","enc",1,30,100,"",$_REQUEST[enc]);
      $formulario -> texto ("Telefono:","text","tel",0,30,20,"",$_REQUEST[tel]);
      $formulario -> texto ("* Latitud:","text","longi\" onkeyup=\"reload_url()",1,30,30,"",$_REQUEST[longi]);
      $formulario -> texto ("* Longitud:","text","latit\" onkeyup=\"reload_url()",0,30,30,"",$_REQUEST[latit]);
 
     $formulario -> texto ("Url Waze:","text","url_wazexx\" readonly=\"readonly",1,30,30,"",$_REQUEST[url_wazexx]);
     $formulario -> texto ("Url Google Maps:","text","url_google\" readonly=\"readonly",0,30,30,"",$_REQUEST[url_google]);
      
      $formulario -> texto ("Temperatura:","text","tempe",1,30,5,"",$_REQUEST[tempe]);
      $formulario -> texto ("Altimetria:","text","altim",0,30,10,"",$_REQUEST[altim]);
      $formulario -> archivo("Foto:","foto",12,200,"",1);
      $formulario -> oculto("MAX_FILE_SIZE", "2000000", 0);
      $formulario -> caja ("Puesto Virtual:","virtua",1,$_REQUEST[virtua],0);

      if($_REQUEST[urbano] == COD_ESTADO_INACTI)
        $formulario -> caja ("Puesto Urbano:","urbano",1,0,1);
      else
        $formulario -> caja ("Puesto Urbano:","urbano",1,1,1);

      if($_REQUEST[activo] == COD_ESTADO_INACTI)
        $formulario -> caja ("Activo:","activo",1,0,0);
      else
        $formulario -> caja ("Activo:","activo",1,1,0);

      $formulario -> caja ("Puesto Padre:","ind_pcpadr",1,$_REQUEST[ind_pcpadr],1);
      
      $formulario -> texto ("Color:","text","color\"  id=\"colorID\" onBlur=\"setColorx()\"",0,7,7,"","$_REQUEST[color]");

      echo "<td nowrap='nowrap' id='coloreal'>
             <a href='#' onClick=\"newwin=open('../".DIR_APLICA_CENTRAL."/pcontr/colores.php?nom=color', 'calwin', 'width=320, height=300, scollbars=false');\" class='etiqueta'>Cambiar Color</a>
             &nbsp;&nbsp;&nbsp;
             <span id='test' title='test' style=\"background:#;\">
             <a href=\"#\" onClick=\"newwin=open('../".DIR_APLICA_CENTRAL."/pcontr/colores.php?nom=color', 'calwin', 'width=320, height=300, scollbars=false');\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/shim.gif\" border=\"1\" width=\"40\" height=\"20\" /></a></span>
            </td><tr>";
     

      $formulario -> nueva_tabla();
      //$formulario -> oculto("maximo",$interfaz -> cant_interf,0);
      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("opcion",2,0);
      $formulario -> oculto("contro",$_REQUEST[contro],0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
      $formulario -> botoni("Actualizar","aceptar_act2()",0);
      $formulario -> cerrar();
  }

  /*! \fn: Actualizar
   *  \brief: Actualiza el puesto de Control
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 25/05/2015
   *  \param: 
   *  \param: 
   *  \return: 
   */
  function Actualizar()
  {
    $fec_actual = date("Y-m-d H:i:s");

    $zona = $_REQUEST[zona];
    $operador = $_REQUEST[operador_gps];
    $nom_operador = $_REQUEST[nom_operador_gps];

    if(!$_REQUEST[virtua])
      $_REQUEST[virtua] = 0;

    if(!$_REQUEST[activo])
      $_REQUEST[activo] = COD_ESTADO_INACTI;
    else
      $_REQUEST[activo] = COD_ESTADO_ACTIVO;

    if(!$_REQUEST[urbano])
      $_REQUEST[urbano] = COD_ESTADO_INACTI;
    else
      $_REQUEST[urbano] = COD_ESTADO_ACTIVO;

     if(!$_REQUEST[longi])
      $_REQUEST[longi] = "";
     else
      $_REQUEST[longi] = $_REQUEST[longi];

     if(!$_REQUEST[latit])
      $_REQUEST[latit] = "";
     else
      $_REQUEST[latit] = $_REQUEST[latit];

     if(!$_REQUEST[tempe])
      $_REQUEST[tempe] = "";
     else
      $_REQUEST[tempe] = $_REQUEST[tempe];

     if(!$_REQUEST[altim])
      $_REQUEST[altim] = "";
     else
      $_REQUEST[altim] = $_REQUEST[altim];

     if(!$_REQUEST[sentV])
      $_REQUEST[sentV] = $_REQUEST[sentido];

    $query = "UPDATE ".BASE_DATOS.".tab_genera_contro
                 SET nom_contro = '".$_REQUEST[nom]."',
                     nom_encarg = '".$_REQUEST[enc]."',
                     dir_contro = '".$_REQUEST[dir]."',
                     tel_contro = '".$_REQUEST[tel]."',
        		   		   val_longit = '".$_REQUEST[longi]."',
        		   		   val_latitu = '".$_REQUEST[latit]."',
        		   		   val_temper = '".$_REQUEST[tempe]."',
        		   		   val_altitu = '".$_REQUEST[altim]."',
        		   		   ind_virtua = '".$_REQUEST[virtua]."',
        		   		   ind_estado = '".$_REQUEST[activo]."',
        		   		   ind_urbano = '".$_REQUEST[urbano]."',
        		   		   usr_modifi = '".$_REQUEST[usuario]."',
        		   		   fec_modifi = '".$fec_actual."',
                     cod_colorx = '".$_REQUEST[color]."', 
                     val_senvia = '".$_REQUEST[sentV]."',
                     dir_senti1 = '".$_REQUEST[sent1]."',
                     dir_senti2 = '".$_REQUEST[sent2]."', 
    								 ind_pcpadr = '".$_REQUEST[ind_pcpadr]."',
                     url_google = '".$_REQUEST[url_google]."',
                     url_wazexx = '".$_REQUEST[url_wazexx]."'
    					 WHERE cod_contro = '".$_REQUEST[contro]."' ";

    $insercion = new Consulta($query, $this -> conexion,"R");

     if($_REQUEST[foto])
     {
      if(move_uploaded_file($_REQUEST[foto], URL_PCTCON.$_REQUEST[contro].".jpg"))
       $_REQUEST[foto] = "'".$_REQUEST[contro].".jpg'";

      $query = "UPDATE ".BASE_DATOS.".tab_genera_contro
  		 SET dir_fotopc = ".$_REQUEST[foto]."
  	       WHERE cod_contro = ".$_REQUEST[contro]."
  	     ";

      $insercion = new Consulta($query, $this -> conexion,"R");
     }

    $query = "SELECT GROUP_CONCAT(DISTINCT a.cod_homolo  ORDER BY a.cod_homolo DESC SEPARATOR ', ') AS cod_homolo
			          FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
			         WHERE a.cod_contro = '".$_REQUEST[contro]."' 
			      GROUP BY a.cod_contro ";
		$consulta = new Consulta($query, $this -> conexion);
    $mCodHomolo = $consulta -> ret_matrix('i');
    $mCodHomolo = $mCodHomolo[0][0];

    if( $_REQUEST[ind_pcpadr] == 1 )
    {
    	#Asigna los PC Hijos de un hijo del PC actual al padre actual
	    $query = " UPDATE ".BASE_DATOS.".tab_homolo_pcxeal 
	    							SET cod_contro = '".$_REQUEST[contro]."'
    							WHERE cod_contro IN ( ".$mCodHomolo." ) ";
	  	$insercion = new Consulta($query, $this -> conexion,"R");

    	#Actualiza los PC Hijos
	    $query = "UPDATE ".BASE_DATOS.".tab_genera_contro 
	                 SET nom_contro = '$_REQUEST[nom]',
	                     nom_encarg = '$_REQUEST[enc]',
	                     dir_contro = '$_REQUEST[dir]',
	                     tel_contro = '$_REQUEST[tel]',
	                     val_longit = '".$_REQUEST[longi]."',
	                     val_latitu = '".$_REQUEST[latit]."',
	                     val_temper = '".$_REQUEST[tempe]."',
	                     val_altitu = '".$_REQUEST[altim]."',
	                     ind_virtua = '".$_REQUEST[virtua]."',
	                     ind_estado = '".$_REQUEST[activo]."',
	                     ind_urbano = '".$_REQUEST[urbano]."',
	                     usr_modifi = '".$_REQUEST[usuario]."',
	                     fec_modifi = '".$fec_actual."',
	                     cod_colorx = '".$_REQUEST[color]."', 
	                     val_senvia = '".$_REQUEST[sentV]."',
	                     dir_senti1 = '".$_REQUEST[sent1]."',
	                     dir_senti2 = '".$_REQUEST[sent2]."', 
	                     ind_pcpadr = '0',
                       url_google = '".$_REQUEST[url_google]."',
                       url_wazexx = '".$_REQUEST[url_wazexx]."'
	               WHERE cod_contro IN ( ".$mCodHomolo." ) 
	             ";
	    $insercion = new Consulta($query, $this -> conexion,"R");

	    #Si es PC Padre elimina los posibles padres que tenia anteriormente
	    $query = " DELETE FROM ".BASE_DATOS.".tab_homolo_pcxeal 
    							WHERE cod_homolo = '".$_REQUEST[contro]."' ";
	  	$insercion = new Consulta($query, $this -> conexion,"BR");
    }else{
    	#Si es hijo elimina los posibles hijos que tenia
    	$query = " DELETE FROM ".BASE_DATOS.".tab_homolo_pcxeal 
    							WHERE cod_contro = '".$_REQUEST[contro]."' ";
	  	$insercion = new Consulta($query, $this -> conexion,"BR");
    }

    $pcinterf = $_REQUEST[pcinterf];
    $operad = $_REQUEST[operad];
    $homoloini = $_REQUEST[homoloini];


     if(SERVIDOR_TRAFICO)
      $nocex_satt = $this -> conexion_trafico;
     else
      $nocex_satt = "";

    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
     $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Puesto de Control</a></b>";

     $mensaje = "El Puesto de Control <b>".$_REQUEST[contro]." - ".$_REQUEST[nom]."</b> se Actualizo con Exito<br>".$link;
     $mens = new mensajes();
     $mens -> correcto("ACTUALIZAR PUESTO DE CONTROL",$mensaje);
    }
  }

}
   $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
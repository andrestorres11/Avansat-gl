<?php

class Proc_despac
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
    $this -> Listar();
  else
  {
   switch($GLOBALS[opcion])
   {
    case "1":
    $this -> Datos();
    break;
    case "2":
    $this -> Insertar();
    break;
   }
  }
 }

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario);

 }//FIN FUNCION LISTAR


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $query = "SELECT a.cod_manifi,b.num_placax,
		    CONCAT(c.nom_ciudad,' (',LEFT(d.nom_depart,4),')'),
		    CONCAT(e.nom_ciudad,' (',LEFT(f.nom_depart,4),')'),
		    g.nom_rutasx
	       FROM ".BASE_DATOS.".tab_despac_despac a,
		    ".BASE_DATOS.".tab_despac_vehige b,
		    ".BASE_DATOS.".tab_genera_ciudad c,
		    ".BASE_DATOS.".tab_genera_depart d,
		    ".BASE_DATOS.".tab_genera_ciudad e,
		    ".BASE_DATOS.".tab_genera_depart f,
		    ".BASE_DATOS.".tab_genera_rutasx g
	      WHERE a.num_despac = b.num_despac AND
		    a.cod_ciuori = c.cod_ciudad AND
		    c.cod_depart = d.cod_depart AND
		    c.cod_paisxx = d.cod_paisxx AND
		    a.cod_ciudes = e.cod_ciudad AND
		    e.cod_depart = f.cod_depart AND
		    e.cod_paisxx = f.cod_paisxx AND
		    b.cod_rutasx = g.cod_rutasx AND
		    a.num_despac = ".$GLOBALS[despac]."
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $datbasic = $consulta -> ret_matriz();

   $inicio[0][0]=0;
   $inicio[0][1]='-';

   $query = "SELECT c.cod_rutasx,c.nom_rutasx,a.obs_despac,
		    CONCAT(e.abr_ciudad,' (',LEFT(g.abr_depart,4),') - ',LEFT(i.nom_paisxx,3)),
		    CONCAT(f.abr_ciudad,' (',LEFT(h.abr_depart,4),') - ',LEFT(j.nom_paisxx,3))
	       FROM ".BASE_DATOS.".tab_despac_despac a,
		    ".BASE_DATOS.".tab_despac_vehige b,
		    ".BASE_DATOS.".tab_genera_rutasx c,
		   ".BASE_DATOS.".tab_genera_ciudad e,
		   ".BASE_DATOS.".tab_genera_ciudad f,
		   ".BASE_DATOS.".tab_genera_depart g,
		   ".BASE_DATOS.".tab_genera_depart h,
		   ".BASE_DATOS.".tab_genera_paises i,
		   ".BASE_DATOS.".tab_genera_paises j
	      WHERE a.num_despac = ".$GLOBALS[despac]." AND
		    a.cod_ciuori = c.cod_ciuori AND
		    a.cod_ciudes = c.cod_ciudes AND
		    a.num_despac = b.num_despac AND
		    a.cod_ciuori = e.cod_ciudad AND
		    e.cod_depart = g.cod_depart AND
		    e.cod_paisxx = g.cod_paisxx AND
		    g.cod_paisxx = i.cod_paisxx AND
		    a.cod_ciudes = f.cod_ciudad AND
		    f.cod_depart = h.cod_depart AND
		    f.cod_paisxx = h.cod_paisxx AND
		    h.cod_paisxx = j.cod_paisxx AND
		    c.cod_rutasx != b.cod_rutasx AND
		    c.ind_estado = '1'
		    GROUP BY 1 ORDER BY 2
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $rutasx = $consulta -> ret_matriz();

   $query = "SELECT a.cod_noveda,a.nom_noveda
	       FROM ".BASE_DATOS.".tab_genera_noveda a
	      WHERE a.ind_tiempo = '1' AND
		    a.ind_alarma = 'N'
		    ORDER BY 2
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $noveda = $consulta -> ret_matriz();

   $noveda = array_merge($inicio,$noveda);

   $formulario = new Formulario ("index.php","post","CAMBIO DE RUTA","form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Cambio de Ruta Para el Documento #/Despacho ".$datbasic[0][0]." Vehiculo ".$datbasic[0][1],1,"t2");

   if(!$rutasx)
   {
    $formulario -> linea ("No Existen Rutas Activas &oacute; Creadas Relacionadas con el Origen ".$datbasic[0][2]." :: Destino ".$datbasic[0][3].".",1,"e");
   }
   else
   {
    $formulario -> nueva_tabla();
    $formulario -> linea("Ruta Actual",0);
    $formulario -> linea($datbasic[0][4],1,"i");

    $formulario -> nueva_tabla();
    $formulario -> linea("Selecci&oacute;n de Ruta",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("",0,"t");
    $formulario -> linea("",0,"t");
    $formulario -> linea("C&oacute;digo Ruta",0,"t");
    $formulario -> linea("Ruta a Seguir",0,"t");
    $formulario -> linea("Origen",0,"t");
    $formulario -> linea("Destino",1,"t");

    for($i = 0; $i < sizeof($rutasx); $i++)
    {
	 if($GLOBALS[rutasx] == $rutasx[$i][0])
	  $formulario -> radio("","rutasx\"",$rutasx[$i][0],1,0);
	 else
	  $formulario -> radio("","rutasx\" onClick=\"form_ins.submit()",$rutasx[$i][0],0,0);

	 $formulario -> linea($rutasx[$i][0],0,"i");
	 $formulario -> linea($rutasx[$i][1],0,"i");
	 $formulario -> linea($rutasx[$i][3],0,"i");
	 $formulario -> linea($rutasx[$i][4],1,"i");
    }

    if($GLOBALS[rutasx])
    {
     $query = "SELECT c.val_duraci,a.fec_noveda,a.cod_contro
		 		 FROM ".BASE_DATOS.".tab_despac_noveda a,
		      		  ".BASE_DATOS.".tab_genera_rutcon c
				WHERE a.num_despac = ".$GLOBALS[despac]." AND
		      		  a.cod_rutasx = ".$GLOBALS[rutasx]." AND
		      		  a.cod_rutasx = c.cod_rutasx AND
		      		  a.cod_contro = c.cod_contro AND
		      		  a.fec_noveda = (SELECT MAX(b.fec_noveda)
									    FROM ".BASE_DATOS.".tab_despac_noveda b
				       				   WHERE b.num_despac = a.num_despac AND
		      		  						 a.cod_rutasx = b.cod_rutasx
				     				 )
	      	  ";

     $consulta = new Consulta($query, $this -> conexion);
     $maxnoved = $consulta -> ret_matriz();

     $query = "SELECT c.val_duraci,a.fec_contro,a.cod_contro
		 		 FROM ".BASE_DATOS.".tab_despac_contro a,
		      		  ".BASE_DATOS.".tab_genera_rutcon c
				WHERE a.num_despac = ".$GLOBALS[despac]." AND
		      		  a.cod_rutasx = ".$GLOBALS[rutasx]." AND
		      		  a.cod_rutasx = c.cod_rutasx AND
		      		  a.cod_contro = c.cod_contro AND
		      		  a.fec_contro = (SELECT MAX(b.fec_contro)
									    FROM ".BASE_DATOS.".tab_despac_contro b
				       				   WHERE b.num_despac = a.num_despac AND
				       				   		 a.cod_rutasx = b.cod_rutasx
				     				 )
	      	  ";

     $consulta = new Consulta($query, $this -> conexion);
     $maxnocon = $consulta -> ret_matriz();

     if($maxnoved[0][1] > $maxnocon[0][1])
      $datultrep = $maxnoved;
     else
      $datultrep = $maxnocon;

     if($maxnoved)
      $query = "SELECT a.cod_contro,a.nom_contro,b.val_duraci,
		       		   if(a.ind_virtua = '0','Fisico','Virtual')
	          	  FROM ".BASE_DATOS.".tab_genera_contro a,
		       		   ".BASE_DATOS.".tab_genera_rutcon b
	      	 	 WHERE a.cod_contro = b.cod_contro AND
		       		   b.cod_rutasx = ".$GLOBALS[rutasx]." AND
		       		   b.val_duraci > ".$datultrep[0][0]." AND
		       		   b.ind_estado = '1' AND
		       		   a.ind_estado = '1'
		       		   GROUP BY 1 ORDER BY 3
	    	 ";
     else
      $query = "SELECT a.cod_contro,a.nom_contro,b.val_duraci,
		       		   if(a.ind_virtua = '0','Fisico','Virtual')
	          	  FROM ".BASE_DATOS.".tab_genera_contro a,
		       		   ".BASE_DATOS.".tab_genera_rutcon b LEFT JOIN
		       		   ".BASE_DATOS.".tab_despac_noveda c ON
		       		   a.cod_contro = c.cod_contro AND
		       		   c.num_despac = ".$GLOBALS[despac]."
		 		 WHERE c.cod_contro IS NULL AND
		       		   c.num_despac IS NULL AND
		       		   a.cod_contro = b.cod_contro AND
		       		   b.cod_rutasx = ".$GLOBALS[rutasx]." AND
		       		   b.ind_estado = '1' AND
		       		   a.ind_estado = '1'
		       		   GROUP BY 1 ORDER BY 3
	       ";

	 $consulta = new Consulta($query, $this -> conexion);
     $pcontros = $consulta -> ret_matriz();

     if($GLOBALS[rutasx] != $GLOBALS[rutasel])
      $GLOBALS[controbase] = NULL;

     if($GLOBALS[controbase])
     {
      $query = "SELECT a.cod_contro,a.nom_contro
		  FROM ".BASE_DATOS.".tab_genera_contro a
		 WHERE a.cod_contro = ".$GLOBALS[controbase]."
	       ";

      $consulta = new Consulta($query, $this -> conexion);
      $pcontros_a = $consulta -> ret_matriz();

      $pcontros = array_merge($pcontros_a,$pcontros);
     }

     $formulario -> nueva_tabla();
     $formulario -> linea("Seleccion Empalme de Puesto de Control",1,"t2");
     $formulario -> nueva_tabla();
     $formulario -> lista("Proximo P/C","controbase\" onChange=\"form_ins.submit()",$pcontros,0);
     $formulario -> texto("Tiempo de Llegada (Min)","text","tmplle\" onChange=\"form_ins.submit()",1,5,5,"",$GLOBALS[tmplle]);

     $formulario -> oculto("rutasel",$GLOBALS[rutasx],0);
    }

    if($GLOBALS[controbase] && $GLOBALS[tmplle])
    {
     $query = "SELECT c.val_duraci,a.fec_noveda,a.cod_contro
		 FROM ".BASE_DATOS.".tab_despac_noveda a,
		      ".BASE_DATOS.".tab_despac_vehige b,
		      ".BASE_DATOS.".tab_genera_rutcon c
		WHERE a.num_despac = ".$GLOBALS[despac]." AND
		      a.num_despac = b.num_despac AND
		      a.cod_rutasx = b.cod_rutasx AND
		      a.cod_rutasx = c.cod_rutasx AND
		      a.cod_contro = c.cod_contro AND
		      a.fec_noveda = (SELECT MAX(b.fec_noveda)
					FROM ".BASE_DATOS.".tab_despac_noveda b
				       WHERE b.num_despac = a.num_despac AND
					     b.cod_rutasx = a.cod_rutasx
				     )
	      ";

      $consulta = new Consulta($query, $this -> conexion);
      $maxnoact = $consulta -> ret_matriz();

     if($maxnoact)
     {
      $query = "SELECT a.nom_contro
		  FROM ".BASE_DATOS.".tab_genera_contro a
		 WHERE a.cod_contro = ".$maxnoact[0][2]."
	       ";

      $consulta = new Consulta($query, $this -> conexion);
      $controul = $consulta -> ret_matriz();

      $formulario -> nueva_tabla();
      $formulario -> linea("Ultima Noveda P/C Generada con la Ruta Actual",1,"t2");
      $formulario -> nueva_tabla();
      $formulario -> linea("Puesto de Control",0);
      $formulario -> linea($controul[0][0],1,"i");
      $formulario -> linea("Fecha Novedad",0);
      $formulario -> linea($maxnoact[0][1],1,"i");
     }

     $formulario -> nueva_tabla();
     $formulario -> linea("Fecha Programada Para el Cambio de Ruta",1,"t2");

     $formulario -> nueva_tabla();

     if(!$GLOBALS[fechaprog])
      $GLOBALS[fechaprog] = date("Y-m-d H:i");
     else
      $GLOBALS[fechaprog] = str_replace("/","-",$GLOBALS[fechaprog]);

     if($maxnoact)
     {
      if(!($GLOBALS[fechaprog] > $maxnoact[0][1] && $GLOBALS[fechaprog] <= date("Y-m-d H:i")))
      {
	   $formulario -> linea("<div align = \"center\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">No Puede Asignar una Fecha Posterior a la Actual &oacute; Menor a la Ultima Novedad Para el Cambio de Ruta del Despacho. Se Asignara La Fecha y Hora Actual</div>",1,"e");
       $GLOBALS[fechaprog] = date("Y-m-d H:i");
      }
     }
     else if(!$GLOBALS[fechaprog] <= date("Y-m-d H:i"))
     {
      $formulario -> linea("<div align = \"center\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">No Puede Asignar una Fecha Posterior a la Actual Para el Cambio de Ruta del Despacho. Se Asignara La Fecha y Hora Actual</div>",1,"e");
      $GLOBALS[fechaprog] = date("Y-m-d H:i");
     }

	 $formulario -> nueva_tabla();
     $formulario -> fecha_calendar("Fecha/Hora","fechaprog","form_ins",$GLOBALS[fechaprog],"yyyy/mm/dd hh:ii",1,1);
     $feccal = $GLOBALS[fechaprog];

     $query = "SELECT a.cod_contro,a.nom_contro,c.val_duraci,
		    if(a.ind_virtua = '0','Fisico','Virtual')
	       FROM ".BASE_DATOS.".tab_genera_contro a,
		    ".BASE_DATOS.".tab_genera_rutcon c
	      WHERE c.cod_rutasx = '".$GLOBALS[rutasx]."' AND
		    c.cod_contro = a.cod_contro AND
		    c.val_duraci >= (SELECT b.val_duraci
				       FROM ".BASE_DATOS.".tab_genera_rutcon b
				      WHERE b.cod_rutasx = c.cod_rutasx AND
					    b.cod_contro = ".$GLOBALS[controbase]."
				    ) AND
		    c.ind_estado = '1' AND
		    a.ind_estado = '1'
		    ORDER BY 3
	    ";

     $consulta = new Consulta($query, $this -> conexion);
     $pcontr = $consulta -> ret_matriz();

     $formulario -> nueva_tabla();
     $formulario -> linea("Puesto de Control",1,"h");

     $formulario -> nueva_tabla();
     $formulario -> linea("",0,"t");
     $formulario -> linea("S/N",0,"t");
     $formulario -> linea("C&oacute;digo",0,"t");
     $formulario -> linea("Nombre",0,"t");
     $formulario -> linea("Puesto",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Novedad",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Tiempo Estimado",0,"t");
     $formulario -> linea("Fecha y Hora Planeada",1,"t");

     $pcontro=$GLOBALS[pcontro];
     $pctime=$GLOBALS[pctime];
     $pcnove=$GLOBALS[pcnove];

     $tiemacu = 0;

     for($i = 0; $i < sizeof($pcontr); $i++)
     {
	if(!$GLOBALS[pcontro])
	 $pcontro[$i] = 1;

	$temp_nove = $noveda;

        if($pcnove[$i] != "0")
	{
	 $query = "SELECT a.cod_noveda,a.nom_noveda
		     FROM ".BASE_DATOS.".tab_genera_noveda a
		    WHERE a.cod_noveda = '".$pcnove[$i]."'
		  ";

	 $consulta = new Consulta($query, $this -> conexion);
   	 $nove_selec = $consulta -> ret_matriz();

	 $temp_nove = array_merge($nove_selec,$temp_nove);
	}

	if($GLOBALS[controbase] == $pcontr[$i][0])
	 $tiempcum = $GLOBALS[tmplle];
	else
	 $tiempcum = $tiemacu + ($pcontr[$i][2] - $pcontr[0][2]) + $GLOBALS[tmplle];

	$query = "SELECT DATE_ADD('".$feccal."', INTERVAL ".$tiempcum." MINUTE)
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$timemost = $consulta -> ret_matriz();

	$tiemacu += $pctime[$i];

	if($pcontr[$i][0] == CONS_CODIGO_PCLLEG)
	{
	 $formulario -> caja("","pcontro[$i]\" disabled ",$pcontr[$i][0],1,0);
	 $formulario -> linea("-",0,"i");
	 $formulario -> linea($pcontr[$i][1],0,"i");
	 $formulario -> linea($pcontr[$i][3],0,"i");
	 $formulario -> linea("",0,"t");
	 $formulario -> linea("-",0,"i");
	 $formulario -> linea("",0,"t");
	 $formulario -> linea("-",0,"i");
	 $formulario -> linea($timemost[0][0],1,"i");
	 $formulario -> oculto("pcontro[$i]",$pcontr[$i][0],0);
	}
	else
	{
	 $formulario -> caja("","pcontro[$i]",$pcontr[$i][0],$pcontro[$i]);
	 $formulario -> linea($pcontr[$i][0],0,"i");
	 $formulario -> linea($pcontr[$i][1],0,"i");
	 $formulario -> linea($pcontr[$i][3],0,"i");
	 $formulario -> lista("","pcnove[$i]",$temp_nove,0);
	 $formulario -> texto("","text","pctime[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_ins.submit()",0,10,4,"",$pctime[$i]);
	 $formulario -> linea($timemost[0][0],1,"i");
	}
     }

     $formulario -> nueva_tabla();
     $formulario -> oculto("totapc",sizeof($pcontr),1);
     $formulario -> boton("Aceptar","button\" onClick=\"if(confirm('Esta Seguro de Cambiar la Ruta')){form_ins.opcion.value = 2; form_ins.submit();} ",1);
    }

     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("opcion",$GLOBALS[opcion],0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   }

   $formulario -> oculto("despac",$GLOBALS[despac],0);
   $formulario -> cerrar();
 }

 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   $fec_cambru = $GLOBALS[fechaprog];

   $pcontro = $GLOBALS[pcontro];
   $pcnove = $GLOBALS[pcnove];
   $pctime = $GLOBALS[pctime];

   $query = "SELECT b.cod_contro,b.cod_rutasx
	       FROM ".BASE_DATOS.".tab_despac_vehige a,
		    ".BASE_DATOS.".tab_despac_seguim b
	      WHERE a.num_despac = ".$GLOBALS[despac]." AND
		    b.num_despac = a.num_despac AND
		    b.cod_rutasx = a.cod_rutasx
	    ";

   $consulta = new Consulta($query, $this -> conexion,"BR");
   $antplaru = $consulta -> ret_matriz();

   for($i = 0; $i < sizeof($antplaru); $i++)
   {
     $query = "SELECT a.cod_contro
		 FROM ".BASE_DATOS.".tab_despac_noveda a
		WHERE a.num_despac = ".$GLOBALS[despac]." AND
		      a.cod_rutasx = ".$antplaru[0][1]." AND
		      a.cod_contro = ".$antplaru[$i][0]."
              ";

     $consulta = new Consulta($query, $this -> conexion);
     $extnoved = $consulta -> ret_matriz();

     $query = "SELECT a.cod_contro
		 FROM ".BASE_DATOS.".tab_despac_contro a
		WHERE a.num_despac = ".$GLOBALS[despac]." AND
		      a.cod_rutasx = ".$antplaru[0][1]." AND
		      a.cod_contro = ".$antplaru[$i][0]."
              ";

     $consulta = new Consulta($query, $this -> conexion);
     $extnovco = $consulta -> ret_matriz();

     if(!$extnoved && !$extnovco)
     {
      $query = "DELETE FROM ".BASE_DATOS.".tab_despac_seguim
		      WHERE num_despac = ".$GLOBALS[despac]." AND
			    cod_rutasx = ".$antplaru[0][1]." AND
			    cod_contro = ".$antplaru[$i][0]."
	       ";

      $consulta = new Consulta($query, $this -> conexion,"R");
     }
   }

   $query = "SELECT a.val_duraci
	       FROM ".BASE_DATOS.".tab_genera_rutcon a
	      WHERE a.cod_rutasx = ".$GLOBALS[rutasx]." AND
		    a.cod_contro = ".$GLOBALS[controbase]."
	    ";

   $consulta = new Consulta($query, $this -> conexion,"R");
   $pcduracibase = $consulta -> ret_matriz();

   $tiemacu = 0;

   for($i = 0; $i < $GLOBALS[totapc]; $i++)
   {
    if($pcontro[$i])
    {
     $query = "SELECT a.val_duraci
		 FROM ".BASE_DATOS.".tab_genera_rutcon a
		WHERE a.cod_rutasx = ".$GLOBALS[rutasx]." AND
		      a.cod_contro = ".$pcontro[$i]."
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $pcduraci = $consulta -> ret_matriz();

     if($GLOBALS[controbase] == $pcontro[$i])
      $tiempcum = $GLOBALS[tmplle];
     else
      $tiempcum = $tiemacu + ($pcduraci[0][0] - $pcduracibase[0][0]) + $GLOBALS[tmplle];

     $query = "SELECT DATE_ADD('".$fec_cambru."', INTERVAL ".$tiempcum." MINUTE)
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $timemost = $consulta -> ret_matriz();

      $query = "SELECT a.cod_contro
		  FROM ".BASE_DATOS.".tab_despac_seguim a
		 WHERE a.num_despac = ".$GLOBALS[despac]." AND
		       a.cod_rutasx = ".$GLOBALS[rutasx]." AND
		       a.cod_contro = ".$pcontro[$i]."
	       ";

      $consulta = new Consulta($query, $this -> conexion);
      $extplaru = $consulta -> ret_matriz();

      if($extplaru)
       $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
		    SET fec_planea = '".$timemost[0][0]."',
		        fec_alarma = '".$timemost[0][0]."',
		        usr_modifi = '".$GLOBALS[usuario]."',
		        fec_modifi = '".$fec_actual."'
		  WHERE num_despac = ".$GLOBALS[despac]." AND
		        cod_rutasx = ".$GLOBALS[rutasx]." AND
		        cod_contro = ".$pcontro[$i]."
	        ";
      else
       $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
			     (num_despac,cod_contro,cod_rutasx,fec_planea,
			      fec_alarma,usr_creaci,fec_creaci)
		      VALUES (".$GLOBALS[despac].",".$pcontro[$i].",".$GLOBALS[rutasx].",
			      '".$timemost[0][0]."','".$timemost[0][0]."','".$GLOBALS[usuario]."',
			      '".$fec_actual."')
	        ";

      $consulta = new Consulta($query, $this -> conexion,"R");

      if($pcnove[$i])
      {
       $query = "SELECT a.cod_contro
		   FROM ".BASE_DATOS.".tab_despac_pernoc a
		  WHERE a.num_despac = ".$GLOBALS[despac]." AND
		        a.cod_rutasx = ".$GLOBALS[rutasx]." AND
		        a.cod_contro = ".$pcontro[$i]."
	        ";

       $consulta = new Consulta($query, $this -> conexion);
       $extpreno = $consulta -> ret_matriz();

       if($extpreno)
        $query = "UPDATE ".BASE_DATOS.".tab_despac_pernoc
		     SET cod_noveda = ".$pcnove[$i].",
			 val_pernoc = ".$pctime[$i].",
			 usr_modifi = '".$GLOBALS[usuario]."',
			 fec_modifi = '".$fec_actual."'
		   WHERE num_despac = ".$GLOBALS[despac]." AND
			 cod_rutasx = ".$GLOBALS[rutasx]." AND
			 cod_contro = ".$pcontro[$i]."
		 ";
       else
        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
			      (num_despac,cod_contro,cod_rutasx,cod_noveda,
			       val_pernoc,usr_creaci,fec_creaci)
		       VALUES (".$GLOBALS[despac].",".$pcontro[$i].",".$GLOBALS[rutasx].",
			       ".$pcnove[$i].",".$pctime[$i].",'".$GLOBALS[usuario]."',
			       '".$fec_actual."'
			      )
		 ";

       $consulta = new Consulta($query, $this -> conexion,"R");

       $tiemacu += $pctime[$i];
      }
    }
   }

   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
		SET cod_rutasx = ".$GLOBALS[rutasx].",
		    usr_modifi = '".$GLOBALS[usuario]."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = ".$GLOBALS[despac]."
	    ";

   $consulta = new Consulta($query, $this -> conexion,"R");

   $formulario = new Formulario ("index.php","post","CAMBIO DE RUTA","form_ins");

   if($consulta = new Consulta("COMMIT", $this -> conexion))
   {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Generar Otro Cambio de Ruta</a></b>";

     $mensaje =  "Se Genero el Cambio de Ruta Para el Despacho # <b>".$GLOBALS[numdespac]."</b> con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("CAMBIO DE RUTA",$mensaje);
   }

   $formulario -> cerrar();

 }

}//FIN CLASE PROC_DESPAC



   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>
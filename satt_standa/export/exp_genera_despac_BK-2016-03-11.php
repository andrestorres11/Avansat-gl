<?php

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
//include ("../lib/general/pdf_lib.inc");
include ("../lib/general/fpdf17/fpdf.php");
include ("../despac/Despachos.inc");
include ("../lib/interfaz_lib_sat.inc");
include ("../lib/GeneralFunctions.inc");
include ("../lib/general/tabla_lib.inc");
include("../../".$GLOBALS['url']."/constantes.inc");

class Proc_exp_enruta
{
 var $conexion,
 	 $GeneralFunctions;

 function Proc_exp_enruta()
 {
  $this -> conexion = new Conexion("bd10.intrared.net:3306", USUARIO, CLAVE, $GLOBALS["db"]);
  $this -> GeneralFunctions = new GeneralFunctions($this -> conexion);
  
  if($GLOBALS[tipexp] == 2)
   $this -> DetalleDespacho();
  else
   $this -> Listar();
 }

 function DetalleDespacho()
 {
  $archivo = "Detalle_".$GLOBALS[nomexp]."_".date("Y_m_d");

  $query = "SELECT a.ind_remdes
  		      FROM ".BASE_DATOS.".tab_config_parame a
  		     WHERE a.ind_remdes = '1'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $manredes = $consulta -> ret_matriz();

  if($manredes)
  {
   $query = "SELECT a.num_remdes,a.nom_remdes,a.obs_adicio,b.cod_ciudad,
   			         b.dir_destin,b.val_pesoxx,b.num_docume,b.num_pedido,   			         
   			         if(c.ind_tonela = '".COD_ESTADO_ACTIVO."',c.val_costos / c.can_tonela,c.val_costos),
   			         if(c.ind_tonela = '".COD_ESTADO_ACTIVO."',(c.val_costos / c.can_tonela) * b.val_pesoxx,c.val_costos),
   			         if(b.cod_tabfle IS NOT NULL,d.nom_trayec,'-'),
   			         if(c.ind_tonela != '".COD_ESTADO_ACTIVO."',' (Viaje)','')
   			    FROM ".BASE_DATOS.".tab_genera_remdes a,
   			         ".BASE_DATOS.".tab_despac_remdes b LEFT JOIN
   			         ".BASE_DATOS.".tab_tablax_fletes c ON
   			         b.cod_tabfle = c.cod_consec LEFT JOIN
   			         ".BASE_DATOS.".tab_genera_trayec d ON
   			         c.cod_trayec = d.cod_trayec
   			   WHERE a.cod_remdes = b.cod_remdes AND
   			         a.ind_remdes = '2' AND
   			         b.num_despac = ".$GLOBALS[despac]."
   			 ";

   $consulta = new Consulta($query, $this -> conexion);
   $liremdes = $consulta -> ret_matriz();
  }

  $query = "SELECT a.cod_colorx,a.cant_tiempo
  	 		  FROM ".BASE_DATOS.".tab_genera_alarma a
  	 		       ORDER BY 2
  	 	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $timecolo = $consulta -> ret_matriz();

  $query = "SELECT a.num_despac,a.cod_manifi,c.abr_tercer,c.cod_tercer,
		    		c.num_telmov,c.num_telef1
               FROM ".BASE_DATOS.".tab_despac_despac a,
		    		".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c
              WHERE a.num_despac = b.num_despac AND
                    b.cod_conduc = c.cod_tercer AND
                    a.num_despac = ".$GLOBALS[despac]."
	    ";

  $consulta = new Consulta($query, $this -> conexion);
  $encab1 = $consulta -> ret_matriz();

  $query = "SELECT b.num_placax,d.nom_marcax,e.nom_lineax,f.nom_colorx,
		   		   g.nom_config,h.nom_carroc,c.ano_modelo
              FROM ".BASE_DATOS.".tab_despac_vehige b,
		   		   ".BASE_DATOS.".tab_vehicu_vehicu c,
                   ".BASE_DATOS.".tab_genera_marcas d,
		   		   ".BASE_DATOS.".tab_vehige_lineas e,
                   ".BASE_DATOS.".tab_vehige_colore f,
		   		   ".BASE_DATOS.".tab_vehige_config g,
                   ".BASE_DATOS.".tab_vehige_carroc h
             WHERE b.num_placax = c.num_placax AND
                   c.cod_marcax = d.cod_marcax AND
                   c.cod_marcax = e.cod_marcax AND
                   c.cod_lineax = e.cod_lineax AND
                   c.cod_colorx = f.cod_colorx AND
                   c.num_config = g.num_config AND
                   c.cod_carroc = h.cod_carroc AND
                   b.num_despac = ".$GLOBALS[despac]."
	  ";

  $consulta = new Consulta($query, $this -> conexion);
  $encab2 = $consulta -> ret_matriz();

  $query = "SELECT g.nom_agenci,e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                   if(a.fec_salida Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y')),
                   if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
		   		   DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
              FROM ".BASE_DATOS.".tab_despac_despac a,
		   		   ".BASE_DATOS.".tab_despac_vehige b,
                   ".BASE_DATOS.".tab_genera_rutasx e,
                   ".BASE_DATOS.".tab_genera_agenci g
             WHERE a.num_despac = b.num_despac AND
                   b.cod_rutasx = e.cod_rutasx AND
                   b.cod_agenci = g.cod_agenci AND
                   a.num_despac = ".$GLOBALS[despac]."
           ";

  $consulta = new Consulta($query, $this -> conexion);
  $encab3 = $consulta -> ret_matriz();

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

  $origen = $objciud -> getSeleccCiudad($encab3[0][2]);
  $destin = $objciud -> getSeleccCiudad($encab3[0][3]);

  $query = "SELECT a.ind_fincal
  		      FROM ".BASE_DATOS.".tab_config_parame a
  		     WHERE a.ind_fincal = '1'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $viscalti = $consulta -> ret_matriz();

  if($viscalti && $GLOBALS[finali])
  {
   $query = "SELECT MAX(a.fec_planea)
   		       FROM ".BASE_DATOS.".tab_despac_seguim a
   		      WHERE a.num_despac = ".$GLOBALS[despac]."
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $fllegpla = $consulta -> ret_matriz();

   $query = "SELECT SUM(a.val_pernoc)
   		       FROM ".BASE_DATOS.".tab_despac_pernoc a
   		      WHERE a.num_despac = ".$GLOBALS[despac]."
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $tiemprog = $consulta -> ret_matriz();

   $query = "SELECT (TIME_TO_SEC(TIMEDIFF(b.fec_salipl,a.fec_salida))/60),
   		            (TIME_TO_SEC(TIMEDIFF('".$fllegpla[0][0]."',a.fec_llegad))/60),
   		            (TIME_TO_SEC(TIMEDIFF(a.fec_llegad,a.fec_salida))/60)
   		       FROM ".BASE_DATOS.".tab_despac_despac a,
   		            ".BASE_DATOS.".tab_despac_vehige b
   		      WHERE a.num_despac = b.num_despac AND
   		            a.num_despac = ".$GLOBALS[despac]."
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $calminti = $consulta -> ret_matriz();
  }

  $encab3[0][2] = $origen[0][1];
  $encab3[0][3] = $destin[0][1];

  //datos de las observaciones
  $query = "SELECT a.obs_despac,b.obs_medcom,b.obs_proesp,a.obs_llegad
              FROM ".BASE_DATOS.".tab_despac_despac a,
		   		   ".BASE_DATOS.".tab_despac_vehige b
             WHERE a.num_despac = b.num_despac AND
                   a.num_despac = ".$GLOBALS[despac]."
	  ";

  $consulta = new Consulta($query, $this -> conexion);
  $observ = $consulta -> ret_matriz();

  //datos de los seguimientos
  $query = "SELECT b.nom_contro,c.nom_noveda,a.tiem_duraci,a.obs_contro,
		   		   DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y'),a.usr_creaci,
		   		   a.val_retras
              FROM ".BASE_DATOS.".tab_despac_contro a,
                   ".BASE_DATOS.".tab_genera_contro b,
		   		   ".BASE_DATOS.".tab_genera_noveda c
             WHERE a.cod_contro = b.cod_contro AND
		   		   a.cod_noveda = c.cod_noveda AND
                   a.num_despac = ".$GLOBALS[despac]."
                   ORDER BY a.fec_contro
          ";

  $consulta = new Consulta($query, $this -> conexion);
  $seguim = $consulta -> ret_matriz();

  $query = "SELECT a.cod_rutasx
	      	  FROM ".BASE_DATOS.".tab_despac_seguim a
	     	 WHERE a.num_despac = ".$GLOBALS[despac]."
		   		   GROUP BY 1
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $totrutas = $consulta -> ret_matriz();

  if(sizeof($totrutas) < 2)
   $camporder = "fec_planea";
  else
   $camporder = "fec_alarma";

  //datos del plan de ruta
  $query = "SELECT if(c.ind_virtua = '1',CONCAT(c.nom_contro,' (Virtual)'),c.nom_contro),
		   		   DATE_FORMAT(a.fec_planea,'%H:%i %d-%m-%Y'),
                   if(b.fec_noveda IS NOT NULL,DATE_FORMAT(b.fec_noveda,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_alarma,'%H:%i %d-%m-%Y')),
                   d.nom_noveda,DATE_FORMAT(b.fec_creaci,'%H:%i %d-%m-%Y'),
                   b.des_noveda,a.fec_planea,a.cod_contro,
		";

  if(sizeof($totrutas) < 2)
   $query .= "a.fec_planea,";
  else
   $query .= "if(b.fec_noveda IS NOT NULL,b.fec_noveda,a.".$camporder."),";

  $query .= "b.usr_creaci,a.fec_alarma,'indlink',c.ind_urbano,b.val_retras,b.fec_noveda
              FROM 
                   ".BASE_DATOS.".tab_despac_vehige e,
                   ".BASE_DATOS.".tab_genera_contro c, 
              ".BASE_DATOS.".tab_despac_seguim a
           LEFT JOIN
                   ".BASE_DATOS.".tab_despac_noveda b ON
                   a.num_despac = b.num_despac AND
                   a.cod_contro = b.cod_contro LEFT JOIN
                   ".BASE_DATOS.".tab_genera_noveda d ON
                   b.cod_noveda = d.cod_noveda
             WHERE a.cod_contro = c.cod_contro AND
                   a.num_despac = e.num_despac AND
                   e.num_despac = ".$GLOBALS[despac]."
		   		   ORDER BY 9";

  if(sizeof($totrutas) < 2) $query .= ",11,15";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

  if($GLOBALS[manpdf])
   $this -> expDetallePdf($archivo,$encab1,$encab2,$encab3,$matriz,$seguim,$observ,$viscalti,$calminti,$tiemprog,$objciud,$manredes,$liremdes,$timecolo);
  else
   $this -> expDetalleExcel($archivo,$encab1,$encab2,$encab3,$matriz,$seguim,$observ,$viscalti,$calminti,$tiemprog,$objciud,$manredes,$liremdes,$timecolo);
 }

 function expDetalleExcel($archivo,$encab1,$encab2,$encab3,$matriz,$seguim,$observ,$viscalti,$calminti,$tiemprog,$objdesp,$manredes,$liremdes,$timecolo)
 {
  header('Content-Type: application/octetstream');
  header('Expires: 0');
  header('Content-Disposition: attachment; filename="'.$archivo.'.xls"');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');

  $formulario = new Formulario ("index.php","post",$archivo,"form_item", "","");

  $formulario -> nueva_tabla();
  $formulario -> linea("Informaci&oacute;n Principal",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Despacho",0,"t");
  $formulario -> linea($encab1[0][0],0,"i");
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea($encab3[0][2],1,"i");
  $formulario -> linea("Documento/Despac",0,"t");
  $formulario -> linea($encab1[0][1],0,"i");
  $formulario -> linea("Destino",0,"t");
  $formulario -> linea($encab3[0][3],1,"i");
  $formulario -> linea("Agencia",0,"t");
  $formulario -> linea($encab3[0][0],0,"i");
  $formulario -> linea("Ruta",0,"t");
  $formulario -> linea($encab3[0][1],1,"i");
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea($encab1[0][2],0,"i");
  $formulario -> linea("Fecha Salida",0,"t");
  $formulario -> linea($encab3[0][4],1,"i");
  $formulario -> linea("C.C.",0,"t");
  $formulario -> linea($encab1[0][3],0,"i");

  if(!$GLOBALS[finali])
  {
   $formulario -> linea("Fecha Planeada Llegada",0,"t");
   $formulario -> linea($encab3[0][5],1,"i");
  }
  else
  {
   $formulario -> linea("Fecha Llegada",0,"t");
   $formulario -> linea($encab3[0][7],1,"i");
  }

  $formulario -> linea("Celular",0,"t");
  $formulario -> linea($encab1[0][4],0,"i");
  $formulario -> linea("Fecha Creaci&oacute;n",0,"t");
  $formulario -> linea($encab3[0][6],1,"i");
  $formulario -> linea("Telefono",0,"t");
  $formulario -> linea($encab1[0][5],0,"i");
  $formulario -> linea("Placa",0,"t");
  $formulario -> linea($encab2[0][0],1,"i");
  $formulario -> linea("Marca",0,"t");
  $formulario -> linea($encab2[0][1],0,"i");
  $formulario -> linea("Configuraci&oacute;n",0,"t");
  $formulario -> linea($encab2[0][4],1,"i");
  $formulario -> linea("Linea",0,"t");
  $formulario -> linea($encab2[0][2],0,"i");
  $formulario -> linea("Carroceria",0,"t");
  $formulario -> linea($encab2[0][5],1,"i");
  $formulario -> linea("Color",0,"t");
  $formulario -> linea($encab2[0][3],0,"i");
  $formulario -> linea("Modelo",0,"t");
  $formulario -> linea($encab2[0][6],1,"i");

  if($viscalti && $GLOBALS[finali])
  {
   for($i = 0; $i < 3; $i++)
   {
   	if($calminti[0][$i] < 0)
   	{
   	 $estilo[$i] = "#740002";
   	 $calminti[0][$i] *= -1;
   	}
   	else
   	 $estilo[$i] = "#336600";
   }

   $formulario -> linea("Diferencia Salida P/E",0,"t");
   echo "<td><font color=\"".$estilo[0]."\">".$objdesp -> getCantTiempo($calminti[0][0])."</font></td>";
   $formulario -> linea("Diferencia Llegada P/E",0,"t");
   echo "<td><font color=\"".$estilo[1]."\">".$objdesp -> getCantTiempo($calminti[0][1])."</font></td>";
   echo "</tr><tr>";
   $formulario -> linea("Tiempo Recorrido",0,"t");
   echo "<td><font color=\"".$estilo[2]."\">".$objdesp -> getCantTiempo($calminti[0][2])."</font></td>";
   $formulario -> linea("Tiempo Novedades Programadas",0,"t");
   $formulario -> linea($objdesp -> getCantTiempo($tiemprog[0][0]),1,"i");
  }

  if($manredes)
  {
   $formulario -> nueva_tabla();
   $formulario -> linea("Selecci&oacute;n de Destinatarios",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Documento/C&oacute;digo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Observaciones",0,"t");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea("Peso (Tn)",0,"t");
   $formulario -> linea("Valor (Unit)",0,"t");
   $formulario -> linea("Valor Flete",0,"t");
   $formulario -> linea("Trayecto",0,"t");
   $formulario -> linea("Remisi&oacute;n",0,"t");
   $formulario -> linea("Pedido",1,"t");

   if($liremdes)
   {
    for($i = 0; $i < sizeof($liremdes); $i++)
    {
     $ciudestin = $objdesp -> getSeleccCiudad($liremdes[$i][3]);

	 $formulario -> linea($liremdes[$i][0],0,"i");
	 $formulario -> linea($liremdes[$i][1],0,"i");
	 $formulario -> linea($liremdes[$i][2],0,"i");
	 $formulario -> linea($ciudestin[0][1],0,"i");
	 $formulario -> linea($liremdes[$i][4],0,"i");
	 $formulario -> linea($liremdes[$i][5],0,"i");
	 $formulario -> linea($liremdes[$i][8].$liremdes[$i][11],0,"i");
	 $formulario -> linea($liremdes[$i][9],0,"i");
	 $formulario -> linea($liremdes[$i][10],0,"i");
	 $formulario -> linea($liremdes[$i][6],0,"i");
	 $formulario -> linea($liremdes[$i][7],1,"i");
    }
   }
   else
   {
    $formulario -> nueva_tabla();
    $formulario -> linea("No se Encontraron Destinatarios Relacionados al Despacho",1,"e");
   }
  }

  if($matriz)
  {
   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n del Plan de Ruta",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Oficina de Control",0,"t");
   $formulario -> linea("Hora/Fecha Programada",0,"t");
   if($viscalti && $GLOBALS[finali])
    $formulario -> linea("",0,"t");
   $formulario -> linea("Hora/Fecha Control",0,"t");
   $formulario -> linea("Novedad",0,"t");
   $formulario -> linea("Retraso",0,"t");
   $formulario -> linea("Hora/Fecha Novedad",0,"t");
   $formulario -> linea("Observaciones",0,"t");
   $formulario -> linea("Usuario",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
   	$nomost = 0;

    for($j = $i; $j < sizeof($matriz); $j++)
    {
      if($matriz[$i][7] == $matriz[$j][7])
      {
       $matriz[$j][11] = 1;

       for($k = $j-1; $k > 0; $k--)
       if($matriz[$j][7] == $matriz[$k][7])
		$matriz[$k][11] = 0;
      }
    }
   }

   for($i = 0; $i <sizeof($matriz); $i++)
   {
    if($matriz[$i][3] == NULL)
    {
     $matriz[$i][3] = "Sin Ejecutar";
     $nomost = 1;
    }

    if($viscalti && $GLOBALS[finali])
  	{
   	 $query = "SELECT (TIME_TO_SEC(TIMEDIFF('".$matriz[$i][1]."','".$matriz[$i][2]."'))/60)
   	    ";

   	 $consulta = new Consulta($query, $this -> conexion);
   	 $calminti = $consulta -> ret_matriz();

   	 $anter = $poste = "";

   	 if($calminti[0][0] < 0)
   	 {
   	  $calminti[0][0] *= -1;
   	  $estilo = "#740002";
   	  $anter = "<< ";
   	 }
   	 else
   	 {
   	  $estilo = "#336600";
   	  $poste = " >>";
   	 }

   	 if($nomost)
   	  $difftempo = "";
   	 else
   	  $difftempo = $anter.$objdesp -> getCantTiempo($calminti[0][0]).$poste;
  	}

   	$alarma_color = NULL;

   	if($matriz[$i][13])
   	{
   	 for($j = 0; $j < sizeof($timecolo); $j++)
   	 {
   	  if($timecolo[$j][1] > $matriz[$i][13])
   	  {
	   $alarma_color = $timecolo[$j][0];
   	  }
   	 }

   	 if(!$alarma_color)
   	  $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
   	}

   	if(!$alarma_color)
   	 $alarma_color = "FFFFFF";

  	if($matriz[$i][12] == "1")
  	{
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][0]."</td>";
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][1]."</td>";
  	 if($viscalti && $GLOBALS[finali])
      echo "<td><font color=\"".$estilo."\">".$difftempo."</font></td>";
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][2]."</td>";
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][3]."</td>";
     echo "<td bgcolor=\"#".$alarma_color."\">".number_format($matriz[$i][13])." Min(s)</td>";
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][4]."</td>";
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][5]."</td>";
  	 echo "<td bgcolor=\"#99CC99\">".$matriz[$i][9]."</td>";
  	 echo "</tr><tr>";

  	}
  	else
  	{
     $formulario -> linea($matriz[$i][0],0,"i","16%");
     $formulario -> linea($matriz[$i][1],0,"i","16%");
     if($viscalti && $GLOBALS[finali])
      echo "<td><font color=\"".$estilo."\">".$difftempo."</font></td>";
     $formulario -> linea($matriz[$i][2],0,"i","16%");
     $formulario -> linea($matriz[$i][3],0,"i","16%");
     echo "<td bgcolor=\"#".$alarma_color."\">".number_format($matriz[$i][13])." Min(s)</td>";
     $formulario -> linea($matriz[$i][4],0,"i","16%");
     $formulario -> linea($matriz[$i][5],0,"i","16%");
     $formulario -> linea($matriz[$i][9],1,"i","16%");
  	}
   }
  }

  if(sizeof($seguim) > 0)
  {
   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n de Notas de Controlador",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Puesto de Control",0,"t");
   $formulario -> linea("Novedad",0,"t");
   $formulario -> linea("Retraso",0,"t");
   $formulario -> linea("Tiempo (Min)",0,"t");
   $formulario -> linea("Observaci&oacute;n",0,"t");
   $formulario -> linea("Fecha",0,"t");
   $formulario -> linea("Usuario",1,"t");

   for($i=0;$i<sizeof($seguim);$i++)
   {
    if(!$seguim[$i][2])
     $seguim[$i][2] = "-";

    $alarma_color = NULL;

   	if($seguim[$i][6])
   	{
   	 for($j = 0; $j < sizeof($timecolo); $j++)
   	 {
   	  if($timecolo[$j][1] > $seguim[$i][6])
   	  {
	   $alarma_color = $timecolo[$j][0];
   	  }
   	 }

   	 if(!$alarma_color)
   	  $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
   	}

   	if(!$alarma_color)
   	 $alarma_color = "FFFFFF";

    $formulario -> linea($seguim[$i][0],0,"i","16%");
    $formulario -> linea($seguim[$i][1],0,"i","16%");
    echo "<td bgcolor=\"#".$alarma_color."\">".number_format($seguim[$i][6])." Min(s)</td>";
    $formulario -> linea($seguim[$i][2],0,"i","16%");
    $formulario -> linea($seguim[$i][3],0,"i","16%");
    $formulario -> linea($seguim[$i][4],0,"i","16%");
    $formulario -> linea($seguim[$i][5],1,"i","16%");
   }//fin for
  }//fin if

  $formulario -> nueva_tabla();
  $formulario -> linea("Observaciones Generales",0,"t2");
  $formulario -> linea("Medios de Comunicacion",0,"t2");
  $formulario -> linea("Protecciones Especiales",1,"t2");

  if(!$observ[0][0])
   $observ[0][0] = "-";
  if(!$observ[0][1])
   $observ[0][1] = "-";
  if(!$observ[0][2])
   $observ[0][2] = "-";

  $formulario -> linea($observ[0][0],0,"i");
  $formulario -> linea($observ[0][1],0,"i");
  $formulario -> linea($observ[0][2],1,"i");

  $formulario -> cerrar();
 }

 function expDetallePdf($archivo,$encab1,$encab2,$encab3,$matriz,$seguim,$observ,$viscalti,$calminti,$tiemprog,$objdesp,$manredes,$liremdes,$timecolo)
 {
  $pdf = new FPDF();
  $pdf -> AliasNbPages();
  $pdf -> AddPage( 'L', 'Letter' );

  $pdf -> SetTitle("Detalle ".$GLOBALS[nomexp]);

  $pdf -> Ln();
  $pdf -> Ln();

  $pdf -> SetFont('Arial','B',7);
  $pdf -> SetFillColor(225,225,225);

  $pdf -> Ln();
  $pdf -> Cell(190,8,"Informacion Principal",1,0,"L",1);

  $pdf -> SetFont('Arial','',7);
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Despacho",1,0,"L",1);
  $pdf -> Cell(65,8,$encab1[0][0],1,0,"R");
  $pdf -> Cell(30,8,"Origen",1,0,"L",1);
  $pdf -> Cell(65,8,$encab3[0][2],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Documento/Despac",1,0,"L",1);
  $pdf -> Cell(65,8,$encab1[0][1],1,0,"R");
  $pdf -> Cell(30,8,"Destino",1,0,"L",1);
  $pdf -> Cell(65,8,$encab3[0][3],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Agencia",1,0,"L",1);
  $pdf -> Cell(65,8,$encab3[0][0],1,0,"R");
  $pdf -> Cell(30,8,"Ruta",1,0,"L",1);
  $pdf -> Cell(65,8,$encab3[0][1],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Conductor",1,0,"L",1);
  $pdf -> Cell(65,8,$encab1[0][2],1,0,"R");
  $pdf -> Cell(30,8,"Fecha Salida",1,0,"L",1);
  $pdf -> Cell(65,8,$encab3[0][4],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"C.C.",1,0,"L",1);
  $pdf -> Cell(65,8,$encab1[0][3],1,0,"R");

  if(!$GLOBALS[finali])
  {
   $pdf -> Cell(30,8,"Fecha Planeada Llegada",1,0,"L",1);
   $pdf -> Cell(65,8,$encab3[0][5],1,0,"R");
  }
  else
  {
   $pdf -> Cell(30,8,"Fecha Llegada",1,0,"L",1);
   $pdf -> Cell(65,8,$encab3[0][7],1,0,"R");
  }

  $pdf -> Ln();
  $pdf -> Cell(30,8,"Celular",1,0,"L",1);
  $pdf -> Cell(65,8,$encab1[0][4],1,0,"R");
  $pdf -> Cell(30,8,"Fecha Creación",1,0,"L",1);
  $pdf -> Cell(65,8,$encab3[0][6],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Telefono",1,0,"L",1);
  $pdf -> Cell(65,8,$encab1[0][5],1,0,"R");
  $pdf -> Cell(30,8,"Placa",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][0],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Marca",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][1],1,0,"R");
  $pdf -> Cell(30,8,"Configuracion",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][4],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Linea",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][2],1,0,"R");
  $pdf -> Cell(30,8,"Carroceria",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][5],1,0,"R");
  $pdf -> Ln();
  $pdf -> Cell(30,8,"Color",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][3],1,0,"R");
  $pdf -> Cell(30,8,"Modelo",1,0,"L",1);
  $pdf -> Cell(65,8,$encab2[0][6],1,0,"R");

  $columnae = $this -> convertHexad("#740002");
  $columnaa = $this -> convertHexad("#336600");

  if($viscalti && $GLOBALS[finali])
  {
   for($i = 0; $i < 3; $i++)
   {
   	if($calminti[0][$i] < 0)
   	{
   	 $estilo[$i] = $columnae;
   	 $calminti[0][$i] *= -1;
   	}
   	else
   	 $estilo[$i] = $columnaa;
   }

   $pdf -> Ln();
   $pdf -> SetTextColor(0,0,0);
   $pdf -> Cell(30,8,"Diferencia Salida P/E",1,0,"L",1);

   $pdf -> SetTextColor($estilo[0][0],$estilo[0][1],$estilo[0][2]);
   $pdf -> Cell(65,8,$objdesp -> getCantTiempo($calminti[0][0]),1,0,"R");

   $pdf -> SetTextColor(0,0,0);
   $pdf -> Cell(30,8,"Diferencia Llegada P/E",1,0,"L",1);

   $pdf -> SetTextColor($estilo[1][0],$estilo[1][1],$estilo[1][2]);
   $pdf -> Cell(65,8,$objdesp -> getCantTiempo($calminti[0][1]),1,0,"R");

   $pdf -> Ln();
   $pdf -> SetTextColor(0,0,0);
   $pdf -> Cell(30,8,"Tiempo Recorrido",1,0,"L",1);

   $pdf -> SetTextColor($estilo[2][0],$estilo[2][1],$estilo[2][2]);
   $pdf -> Cell(65,8,$objdesp -> getCantTiempo($calminti[0][2]),1,0,"R");

   $pdf -> SetTextColor(0,0,0);
   $pdf -> Cell(30,8,"Tiempo Novedades Programadas",1,0,"L",1);

   $pdf -> Cell(65,8,$objdesp -> getCantTiempo($tiemprog[0][0]),1,0,"R");
  }

  if($manredes)
  {
   $pdf -> Ln();
   $pdf -> Cell(190,8,"Seleccion de Destinatarios",1,0,"L",1);

   $pdf -> Ln();
   $pdf -> Cell(15,8,"Doc/Cod",1,0,"C",1);
   $pdf -> Cell(35,8,"Nombre",1,0,"C",1);
   $pdf -> Cell(40,8,"Observaciones",1,0,"C",1);
   $pdf -> Cell(30,8,"Ciudad",1,0,"C",1);
   $pdf -> Cell(40,8,"Direccion",1,0,"C",1);
   $pdf -> Cell(15,8,"Peso (Tn)",1,0,"C",1);
   $pdf -> Cell(10,8,"Remi",1,0,"C",1);
   $pdf -> Cell(10,8,"Ped",1,0,"C",1);

   if($liremdes)
   {
    for($i = 0; $i < sizeof($liremdes); $i++)
    {
   	 $pdf -> Ln();
     $ciudestin = $objdesp -> getSeleccCiudad($liremdes[$i][3]);

	 $pdf -> Cell(15,8,$liremdes[$i][0],1,0,"R");
	 $pdf -> Cell(35,8,$liremdes[$i][1],1,0,"L");
	 $pdf -> Cell(40,8,$liremdes[$i][2],1,0,"L");
	 $pdf -> Cell(30,8,$ciudestin[0][1],1,0,"R");
	 $pdf -> Cell(40,8,$liremdes[$i][4],1,0,"R");
	 $pdf -> Cell(15,8,$liremdes[$i][5],1,0,"C");
	 $pdf -> Cell(10,8,$liremdes[$i][6],1,0,"C");
	 $pdf -> Cell(10,8,$liremdes[$i][7],1,0,"C");
    }
   }
   else
   {
    $pdf -> Ln();
    $pdf -> SetTextColor($columnae[0],$columnae[1],$columnae[2]);
    $pdf -> Cell(190,8,"No se Encontraron Destinatarios Relacionados al Despacho",1,0,"L");
   }
  }

  $pdf -> SetFont('Arial','B',7);
  $pdf -> SetTextColor(0,0,0);
  $pdf -> Ln();
  $pdf -> Cell(190,8,"Informacion del Plan de Ruta",1,0,"L",1);

  $pdf -> SetFont('Arial','',7);
  $pdf -> Ln();
  $pdf -> Cell(55,8,"Oficina de Control",1,0,"C",1);
  $pdf -> Cell(25,8,"Fecha Programada",1,0,"C",1);
  if($viscalti && $GLOBALS[finali])
   $pdf -> Cell(25,8,"",1,0,"C",1);
  $pdf -> Cell(25,8,"Fecha Control",1,0,"C",1);
  $pdf -> Cell(20,8,"Novedad",1,0,"C",1);
  $pdf -> Cell(20,8,"Retraso",1,0,"C",1);
  $pdf -> Cell(25,8,"Fecha Novedad",1,0,"C",1);
  $pdf -> Cell(60,8,"Observaciones",1,0,"C",1);
  if(!($viscalti && $GLOBALS[finali]))
   $pdf -> Cell(25,8,"Usuario",1,0,"C",1);

  for($i = 0; $i < sizeof($matriz); $i++)
  {
   if($matriz[$i][3] == NULL)
   {
    $matriz[$i][3] = "Sin Ejecutar";
    $nomost = 1;
   }

   if($viscalti && $GLOBALS[finali])
  	{
   	 $query = "SELECT (TIME_TO_SEC(TIMEDIFF('".$matriz[$i][1]."','".$matriz[$i][2]."'))/60)
   	    ";

   	 $consulta = new Consulta($query, $this -> conexion);
   	 $calminti = $consulta -> ret_matriz();

   	 $anter = $poste = "";

   	 if($calminti[0][0] < 0)
   	 {
   	  $calminti[0][0] *= -1;
   	  $estilo = $columnae;
   	  $anter = "<< ";
   	 }
   	 else
   	 {
   	  $estilo = $columnaa;
   	  $poste = " >>";
   	 }

   	 if($nomost)
   	  $difftempo = "";
   	 else
   	  $difftempo = $anter.$objdesp -> getCantTiempo($calminti[0][0]).$poste;
  	}

   	$alarma_color = NULL;

   	if($matriz[$i][13])
   	{
   	 for($j = 0; $j < sizeof($timecolo); $j++)
   	 {
   	  if($timecolo[$j][1] > $matriz[$i][13])
   	  {
	   $alarma_color = $timecolo[$j][0];
   	  }
   	 }

   	 if(!$alarma_color)
   	  $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
   	}

   	if(!$alarma_color)
   	 $alarma_color = "FFFFFF";

   $pdf -> Ln();
   $pdf -> SetTextColor(0,0,0);

   if($matriz[$i][12] == "1")
   {
    $rsultco = $this -> convertHexad("#99CC99");
    $pdf -> SetFillColor($rsultco[0],$rsultco[1],$rsultco[2]);
   }
   else
    $pdf -> SetFillColor(255,255,255);

   $rsultco = $this -> convertHexad("#99CC99");
   $pdf -> SetFillColor($rsultco[0],$rsultco[1],$rsultco[2]);

   $pdf -> Cell(55,8,$matriz[$i][0],1,0,"C");
   $pdf -> Cell(25,8,$matriz[$i][1],1,0,"R");
   if($viscalti && $GLOBALS[finali])
   {
   	$pdf -> SetTextColor($estilo[0],$estilo[1],$estilo[2]);
    $pdf -> Cell(25,8,$difftempo,1,0,"R");
   }
   $pdf -> SetTextColor(0,0,0);
   $pdf -> Cell(25,8,$matriz[$i][2],1,0,"R");
   $pdf -> Cell(20,8,$matriz[$i][3],1,0,"C");

   $colalarm = $this -> convertHexad("#".$alarma_color);
   $pdf -> SetFillColor($colalarm[0],$colalarm[1],$colalarm[2]);
   $pdf -> Cell(20,8,$matriz[$i][13]." Min(s)",1,0,"C");

   $pdf -> SetFillColor($rsultco[0],$rsultco[1],$rsultco[2]);
   $pdf -> Cell(25,8,$matriz[$i][4],1,0,"R");
   $pdf -> Cell(60,8,$matriz[$i][5],1,0,"L");
   if(!($viscalti && $GLOBALS[finali]))
    $pdf -> Cell(25,8,$matriz[$i][9],1,0,"R");
  }

  $pdf -> SetFillColor(255,255,255);

  if($seguim)
  {
   $pdf -> SetFont('Arial','B',7);
   $pdf -> Ln();
   $pdf -> Cell(190,8,"Informacion de Notas de Controlador",1,0,"L",1);

   $pdf -> SetFont('Arial','',7);
   $pdf -> Ln();
   $pdf -> Cell(35,8,"Puesto de Control",1,0,"C",1);
   $pdf -> Cell(25,8,"Novedad",1,0,"C",1);
   $pdf -> Cell(20,8,"Retraso",1,0,"C",1);
   $pdf -> Cell(20,8,"Tiempo (Min)",1,0,"C",1);
   $pdf -> Cell(100,8,"Observacion",1,0,"C",1);
   $pdf -> Cell(30,8,"Fecha",1,0,"C",1);
   $pdf -> Cell(25,8,"Usuario",1,0,"C",1);

   for($i = 0; $i < sizeof($seguim); $i++)
   {
    if(!$seguim[$i][2])
     $seguim[$i][2] = "-";

   	$alarma_color = NULL;

   	if($seguim[$i][6])
   	{
   	 for($j = 0; $j < sizeof($timecolo); $j++)
   	 {
   	  if($timecolo[$j][1] > $seguim[$i][6])
   	  {
	   $alarma_color = $timecolo[$j][0];
   	  }
   	 }

   	 if(!$alarma_color)
   	  $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
   	}

   	if(!$alarma_color)
   	 $alarma_color = "FFFFFF";

	$pdf -> Ln();
	$pdf -> SetFillColor(0,0,0);
    $pdf -> Cell(35,8,$seguim[$i][0],1,0,"L");
    $pdf -> Cell(25,8,$seguim[$i][1],1,0,"C");

    $colalarm = $this -> convertHexad("#".$alarma_color);
    $pdf -> SetFillColor($colalarm[0],$colalarm[1],$colalarm[2]);
    $pdf -> Cell(20,8,$seguim[$i][6]." Min(s)",1,0,"C");

    $pdf -> SetFillColor(255,255,255);
    $pdf -> Cell(20,8,$seguim[$i][2],1,0,"C");
    $pdf -> Cell(100,8,substr($seguim[$i][3], 0, 70),1,0,"L");
    $pdf -> Cell(30,8,$seguim[$i][4],1,0,"R");
    $pdf -> Cell(25,8,$seguim[$i][5],1,0,"R");
   }
  }

  $pdf -> SetFont('Arial','B',7);
  $pdf -> Ln();
  $pdf -> Cell(65,8,"Observaciones Generales",1,0,"C",1);
  $pdf -> Cell(60,8,"Medios de Comunicacion",1,0,"C",1);
  $pdf -> Cell(65,8,"Protecciones Especiales",1,0,"C",1);

  if(!$observ[0][0])
   $observ[0][0] = "-";
  if(!$observ[0][1])
   $observ[0][1] = "-";
  if(!$observ[0][2])
   $observ[0][2] = "-";

  $pdf -> SetFont('Arial','',7);
  $pdf -> Ln();
  $pdf -> Cell(65,8,$observ[0][0],1,0,"L");
  $pdf -> Cell(60,8,$observ[0][1],1,0,"L");
  $pdf -> Cell(65,8,$observ[0][2],1,0,"L");

  $pdf -> Footer();
  $pdf -> Output();
 }

 function Listar()
 {
   $query = "SELECT a.ind_remdes
  		       FROM ".BASE_DATOS.".tab_config_parame a
  		      WHERE a.ind_remdes = '1'
  		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $manredes = $consulta -> ret_matriz();

   $query = "SELECT a.ind_desurb
  		       FROM ".BASE_DATOS.".tab_config_parame a
  		      WHERE a.ind_desurb = '1'
  		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $desurb = $consulta -> ret_matriz();

   $query = "SELECT a.ind_restra
  		      FROM ".BASE_DATOS.".tab_config_parame a
  		     WHERE a.ind_restra = '1'
  		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $resptran = $consulta -> ret_matriz();

   $archivo = $GLOBALS[nom_opcion].date("Y_m_d");
   session_start();
   //$query = base64_decode($GLOBALS[query_exp]);
   
   $query = $_SESSION['sql']; 	
   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

   $ind_porlleg = 0;
   $ind_enrutas = 0;

   $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
   
   if($GLOBALS[finali])
   {
    $this -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $matriz[$i][15]));

    for($i = 0; $i < sizeof($matriz); $i++)
    {
     if($this -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $matriz[$i][15])))
     {
      if($manredes)
   	  {
	   $query = "SELECT a.num_docume
	 			   FROM ".BASE_DATOS.".tab_despac_remdes a
	 			  WHERE a.num_despac = ".$matriz[$i][0]."
	 		    ";

	   $consulta = new Consulta($query, $this -> conexion);
       $document_despac = $consulta -> ret_matriz();

       if($document_despac)
       {
        $remisiones_destin = $document_despac[0][0];

        for($j = 1; $j < sizeof($document_despac); $j++)
         if($document_despac[$j][0])
          $remisiones_destin .= ", ".$document_despac[$j][0];

        if(!$remisiones_destin)
         $remisiones_destin = "-";
	   }
	   else
	    $remisiones_destin = "-";

	   $matriz[$i][12] = $remisiones_destin;

	   $query = "SELECT a.num_pedido
	 			   FROM ".BASE_DATOS.".tab_despac_remdes a
	 			  WHERE a.num_despac = ".$matriz[$i][0]."
	 		    ";

	   $consulta = new Consulta($query, $this -> conexion);
       $document_despac = $consulta -> ret_matriz();

       if($document_despac)
       {
        $pedidos_destin = $document_despac[0][0];

        for($j = 1; $j < sizeof($document_despac); $j++)
         if($document_despac[$j][0])
          $pedidos_destin .= ", ".$document_despac[$j][0];

        if(!$pedidos_destin)
         $pedidos_destin = "-";
	   }
	   else
	    $pedidos_destin = "-";

	   $matriz[$i][13] = $pedidos_destin;
	  }

      $porllegad[] = $matriz[$i];
      $ind_porlleg++;
     }
    }
   }
   else
   {
    for($i = 0; $i < sizeof($matriz); $i++)
    {
     $this -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $matriz[$i][15]))."<br>";

     if($this -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $matriz[$i][15])))
     {
   	  if($manredes)
   	  {
	   $query = "SELECT a.num_docume
	 			   FROM ".BASE_DATOS.".tab_despac_remdes a
	 			  WHERE a.num_despac = ".$matriz[$i][0]."
	 		    ";

	   $consulta = new Consulta($query, $this -> conexion);
       $document_despac = $consulta -> ret_matriz();

       if($document_despac)
       {
        $remisiones_destin = $document_despac[0][0];

        for($j = 1; $j < sizeof($document_despac); $j++)
         if($document_despac[$j][0])
          $remisiones_destin .= ", ".$document_despac[$j][0];

        if(!$remisiones_destin)
         $remisiones_destin = "-";
	   }
	   else
	    $remisiones_destin = "-";

	   $matriz[$i][12] = $remisiones_destin;

	   $query = "SELECT a.num_pedido
	 			   FROM ".BASE_DATOS.".tab_despac_remdes a
	 			  WHERE a.num_despac = ".$matriz[$i][0]."
	 		    ";

	   $consulta = new Consulta($query, $this -> conexion);
       $document_despac = $consulta -> ret_matriz();

       if($document_despac)
       {
        $pedidos_destin = $document_despac[0][0];

        for($j = 1; $j < sizeof($document_despac); $j++)
         if($document_despac[$j][0])
          $pedidos_destin .= ", ".$document_despac[$j][0];

        if(!$pedidos_destin)
         $pedidos_destin = "-";
	   }
	   else
	    $pedidos_destin = "-";

	   $matriz[$i][13] = $pedidos_destin;
	  }

      $query = "SELECT a.cod_rutasx
	              FROM ".BASE_DATOS.".tab_despac_seguim a
	             WHERE a.num_despac = ".$matriz[$i][0]."
		       	       GROUP BY 1
	          ";

      $consulta = new Consulta($query, $this -> conexion);
      $totrutas = $consulta -> ret_matriz();

      if(sizeof($totrutas) < 2)
       $camporder = "fec_planea";
      else
       $camporder = "fec_alarma";

      $query = "SELECT a.cod_contro
                  FROM ".BASE_DATOS.".tab_despac_seguim a,
		      		   ".BASE_DATOS.".tab_despac_vehige c
               	 WHERE a.num_despac = c.num_despac AND
		     		   c.num_despac = ".$matriz[$i][0]." AND
                       a.".$camporder." = (SELECT MAX(b.".$camporder.")
                                       		 FROM ".BASE_DATOS.".tab_despac_seguim b
                                      	    WHERE a.num_despac = b.num_despac
                                    	  )
              ";

      $consulta = new Consulta($query, $this -> conexion);
      $ultimopc = $consulta -> ret_matriz();

      $query = "SELECT a.cod_contro
                  FROM ".BASE_DATOS.".tab_despac_noveda a,
		     		   ".BASE_DATOS.".tab_despac_vehige c
               	 WHERE a.num_despac = c.num_despac AND
		     		   c.num_despac = ".$matriz[$i][0]." AND
                       a.fec_noveda = (SELECT MAX(b.fec_noveda)
                                         FROM ".BASE_DATOS.".tab_despac_noveda b
                                        WHERE a.num_despac = b.num_despac
                                      )
               ";

      $consulta = new Consulta($query, $this -> conexion);
      $ultimnov = $consulta -> ret_matriz();

	  if($ultimnov)
	  {
       $query = "SELECT a.ind_urbano
	 		       FROM ".BASE_DATOS.".tab_genera_contro a
	 		      WHERE a.cod_contro = ".$ultimnov[0][0]." AND
	 		     	    a.ind_urbano = '1'
	 		   ";

	   $consulta = new Consulta($query, $this -> conexion);
       $pcontrurb = $consulta -> ret_matriz();
	  }
	  else
	   $pcontrurb = NULL;

	  if($manredes && $desurb && $pcontrurb)
	   $pcomparar = $ultimnov[0][0];
	  else
       $pcomparar = $ultimopc[0][0];

      if($pcomparar == $ultimnov[0][0])
      {
       $porllegad[$ind_porlleg] = $matriz[$i];
       $ind_porlleg++;
      }
      else
      {
       $desenruta[$ind_enrutas] = $matriz[$i];
       $ind_enrutas++;
      }
     }
    }
   }

   if(!$GLOBALS[finali])
   {
    $query = "SELECT nom_alarma, cant_tiempo, cod_colorx
                FROM ".BASE_DATOS.".tab_genera_alarma
                     ORDER BY 2 ";

    $consulta = new Consulta($query, $this -> conexion);
    $alarmas = $consulta -> ret_matriz();
   }

   for($i = 0; $i < sizeof($desenruta); $i++)
   {
     //trae la ultima fecha de la ultima novedad
     $query = "SELECT MAX(e.fec_noveda)
                 FROM ".BASE_DATOS.".tab_despac_vehige c,
		      		  ".BASE_DATOS.".tab_despac_seguim d,
                      ".BASE_DATOS.".tab_despac_noveda e
                WHERE c.num_despac = d.num_despac AND
                      d.num_despac = e.num_despac AND
                      c.num_despac = '".$desenruta[$i][0]."' ";

     $consulta = new Consulta($query, $this -> conexion);
     $maximo = $consulta -> ret_matriz();

     $query = "SELECT c.nom_contro,c.cod_contro
                 FROM ".BASE_DATOS.".tab_despac_noveda a,
		      		  ".BASE_DATOS.".tab_genera_noveda b,
		      		  ".BASE_DATOS.".tab_genera_contro c
                WHERE a.num_despac = '".$desenruta[$i][0]."' AND
                      a.cod_noveda = b.cod_noveda AND
                      a.cod_contro = c.cod_contro AND
                      a.fec_noveda = '".$maximo[0][0]."'
               	      GROUP BY a.num_despac ";

     $nom_contro = new Consulta($query, $this -> conexion);
     $nom_contro = $nom_contro -> ret_arreglo();

     $query = "SELECT b.nom_noveda, b.cod_noveda
                 FROM ".BASE_DATOS.".tab_despac_noveda a,
		      		  ".BASE_DATOS.".tab_genera_noveda b,
		      		  ".BASE_DATOS.".tab_genera_contro c
                WHERE a.num_despac = '".$desenruta[$i][0]."' AND
                      a.cod_noveda = b.cod_noveda AND
                      a.cod_contro = '".$nom_contro[1]."' AND
                      a.fec_noveda = '".$maximo[0][0]."'
                      GROUP BY a.num_despac ";

     $nom_noveda = new Consulta($query, $this -> conexion);
     $nom_noveda = $nom_noveda -> ret_arreglo();

     $desenruta[$i][8]  = $maximo[0][0];
     $desenruta[$i][9] = $nom_contro[0];
     $desenruta[$i][10] = $nom_noveda[0];

     if(sizeof($maximo) > 0)
     {
      $query="SELECT e.cod_contro
                FROM ".BASE_DATOS.".tab_despac_vehige c,
		     		 ".BASE_DATOS.".tab_despac_seguim d,
                     ".BASE_DATOS.".tab_despac_noveda e
               WHERE c.num_despac = d.num_despac AND
                     d.num_despac = e.num_despac AND
                     e.fec_noveda = '".$maximo[0][0]."' AND
                     c.num_despac = '".$desenruta[$i][0]."'
                     GROUP BY 1
	     ";

      $consulta = new Consulta($query, $this -> conexion);
      $contro = $consulta -> ret_matriz();
     }

     //trae la fecha de alarma del ultimo puesto de control si
     //hay novedad en el puesto de control
     if(sizeof($contro) > 0)
     {
      $query = "SELECT c.fec_alarma
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
		       		   ".BASE_DATOS.".tab_despac_seguim c LEFT JOIN
                       ".BASE_DATOS.".tab_despac_noveda AS b ON
		       		   c.num_despac = b.num_despac AND
                       c.cod_contro = b.cod_contro
                 WHERE a.num_despac = c.num_despac AND
                       a.num_despac = '".$desenruta[$i][0]."' AND
		       		   b.cod_contro = '".$contro[0][0]."'
		       		   ORDER BY 1
		";
     }
     //trae la fecha de salida si no hay novedades en los puestos decontrol
     else
     {
      $query = "SELECT b.fec_salida
                  FROM ".BASE_DATOS.".tab_despac_despac b
                 WHERE b.num_despac = '".$desenruta[$i][0]."'
                       ORDER BY 1 ";

      $consulta= new Consulta($query, $this -> conexion);
      $fec_salipl = $consulta -> ret_arreglo();
     }

     $consulta = new Consulta($query, $this -> conexion);
     $ajustada = $consulta -> ret_arreglo();

     $query = "SELECT b.fec_alarma
                 FROM ".BASE_DATOS.".tab_despac_despac a,
		      ".BASE_DATOS.".tab_despac_seguim b,
		      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.num_despac = c.num_despac AND
		      c.num_despac = b.num_despac AND
                      a.num_despac = ".$desenruta[$i][0]." AND
                      b.fec_alarma > '".$ajustada[0]."'
                      ORDER BY 1 ";

     $consulta = new Consulta($query, $this -> conexion);

     if($fec_1 = $consulta -> ret_arreglo())
      $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '".$fec_1[0]."')) / 60";
     else
     {
      $query = "SELECT b.fec_llegpl
                  FROM ".BASE_DATOS.".tab_despac_vehige b
                 WHERE b.num_despac = '".$desenruta[$i][0]."'
                       ORDER BY 1 ";

      $consulta= new Consulta($query, $this -> conexion);
      $fec_llegpl = $consulta -> ret_arreglo();

      $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '".$fec_llegpl[0]."')) / 60";
     }

     //calcula el tiempo de retraso
     $tiempo = new Consulta($query, $this -> conexion);
     $tiemp_demora = $tiempo -> ret_arreglo();

     //trae el tiempo de alarma
     $query = "SELECT cant_tiempo
                 FROM ".BASE_DATOS.".tab_genera_alarma
		      ORDER BY 1
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $alarmas_t = $consulta -> ret_matriz();

     $tiemp_alarma[0] = null;

     if($tiemp_demora[0] >= 0)
     {
      for($j = 0; $j < sizeof($alarmas_t); $j++)
      {
       if($tiemp_demora[0] < $alarmas_t[0][0])
       {
        $tiemp_alarma[0] = $alarmas_t[0][0];
        $j = sizeof($alarmas_t);
       }
       else if($tiemp_demora[0] > $alarmas_t[$j][0] && $tiemp_demora[0] < $alarmas_t[$j+1][0])
       {
        $tiemp_alarma[0] = $alarmas_t[$j+1][0];
        $j = sizeof($alarmas_t);
       }
      }
      if(!$tiemp_alarma[0])
       $tiemp_alarma[0] = $alarmas_t[sizeof($alarmas_t) - 1][0];
     }

     if($resptran)
	 {
      $query = "SELECT a.cant_tiempo
     		      FROM ".BASE_DATOS.".tab_genera_alarma a
     		     	   ORDER BY 1
     		   ";

      $consulta = new Consulta ($query, $this -> conexion);
      $color_maximo  = $consulta -> ret_matriz();

      $query = "SELECT a.cod_contro
      		     FROM ".BASE_DATOS.".tab_despac_contro a
      		    WHERE a.cod_noveda = ".CONS_NOVEDA_CAMALA." AND
      		          a.num_despac = ".$desenruta[$i][0]."
      		   ";

      $consulta = new Consulta ($query, $this -> conexion);
      $existcambala  = $consulta -> ret_matriz();

	  if($tiemp_alarma[0] == $color_maximo[sizeof($color_maximo) - 1][0])
	   $tiemp_alarma[0] = $color_maximo[sizeof($color_maximo) - 2][0];

      if($existcambala)
       $tiemp_alarma[0] = $color_maximo[sizeof($color_maximo) - 1][0];
	 }

     //trae el color de la alarma
     $query = "SELECT cod_colorx
                 FROM ".BASE_DATOS.".tab_genera_alarma
                WHERE cant_tiempo = '".$tiemp_alarma[0]."'
	      ";

     $consulta = new Consulta ($query, $this -> conexion);
     $color_a  = $consulta -> ret_arreglo();

     $desenruta[$i][11] = $color_a[0];

     $valimp = 0;

     if($GLOBALS[des_retras])
     {
      if($tiemp_alarma[0] != NULL)
       $valimp = 1;
     }
     else
      $valimp = 1;

     if($valimp)
     {
      $ciudad_o = $objciud -> getSeleccCiudad($desenruta[$i][2]);
      $ciudad_d = $objciud -> getSeleccCiudad($desenruta[$i][3]);

      $desenruta[$i][2] = $ciudad_o[0][1];
      $desenruta[$i][3] = $ciudad_d[0][1];

      $desenrutafinal[$i] = $desenruta[$i];
     }
     else
      $desenrutafinal[$i] = NULL;
   }

   for($i = 0; $i < sizeof($porllegad); $i++)
   {
   	$query = "SELECT a.ind_urbano
       		    FROM ".BASE_DATOS.".tab_genera_contro a,
       		         ".BASE_DATOS.".tab_despac_seguim b
       		   WHERE a.cod_contro = b.cod_contro AND
       		         a.ind_urbano = '1' AND
       		         b.num_despac = ".$porllegad[$i][0]."
       		 ";

    $consulta = new Consulta($query, $this -> conexion);
    $despurban = $consulta -> ret_matriz();

    //trae la ultima fecha de la ultima novedad
    $query ="SELECT MAX(e.fec_noveda)
               FROM ".BASE_DATOS.".tab_despac_vehige c,
		      		".BASE_DATOS.".tab_despac_seguim d,
                    ".BASE_DATOS.".tab_despac_noveda e
              WHERE c.num_despac = d.num_despac AND
		      		c.num_despac = d.num_despac AND
                    d.num_despac = e.num_despac AND
                    c.num_despac = '".$porllegad[$i][0]."' ";

    $consulta = new Consulta($query, $this -> conexion);
    $maximo = $consulta -> ret_matriz();

    $query = "SELECT c.nom_contro,c.cod_contro
                FROM ".BASE_DATOS.".tab_despac_noveda a,
                     ".BASE_DATOS.".tab_genera_noveda b,
                     ".BASE_DATOS.".tab_genera_contro c
               WHERE a.num_despac = '".$porllegad[$i][0]."' AND
                     a.cod_noveda = b.cod_noveda AND
                     a.cod_contro = c.cod_contro AND
                     a.fec_noveda = '".$maximo[0][0]."'
                     GROUP BY a.num_despac ";

    $nom_contro = new Consulta($query, $this -> conexion);
    $nom_contro = $nom_contro -> ret_arreglo();

    $query = "SELECT b.nom_noveda, b.cod_noveda
                FROM ".BASE_DATOS.".tab_despac_noveda a,
                     ".BASE_DATOS.".tab_genera_noveda b,
                     ".BASE_DATOS.".tab_genera_contro c
               WHERE a.num_despac = '".$porllegad[$i][0]."' AND
                     a.cod_noveda = b.cod_noveda AND
                     a.cod_contro = '".$nom_contro[1]."' AND
                     a.fec_noveda = '".$maximo[0][0]."'
                     GROUP BY a.num_despac ";

    $nom_noveda = new Consulta($query, $this -> conexion);
    $nom_noveda = $nom_noveda -> ret_arreglo();

    $porllegad[$i][8]  = $maximo[0][0];
    $porllegad[$i][9] = $nom_contro[0];
    $porllegad[$i][10] = $nom_noveda[0];
    if($despurban)
     $porllegad[$i][12] = 1;
    else
     $porllegad[$i][12] = 0;

    $ciudad_o = $objciud -> getSeleccCiudad($porllegad[$i][2]);
    $ciudad_d = $objciud -> getSeleccCiudad($porllegad[$i][3]);

    $porllegad[$i][2] = $ciudad_o[0][1];
    $porllegad[$i][3] = $ciudad_d[0][1];

    $porllegadfinal[$i] = $porllegad[$i];
   }

   if($GLOBALS[manpdf])
    $this -> expListadoPdf($archivo,$desenrutafinal,$porllegadfinal,$ind_enrutas,$ind_porlleg,$alarmas,$manredes,$desurb);
   else
    $this -> expListadoExcel($archivo,$desenrutafinal,$porllegadfinal,$ind_enrutas,$ind_porlleg,$alarmas,$manredes,$desurb);
 }

 function expListadoExcel($archivo,$desenrutafinal,$porllegadfinal,$ind_enrutas,$ind_porlleg,$alarmas,$manredes,$desurb)
 {
  $archivo .= ".xls";

  header('Content-Type: application/octetstream');
  header('Expires: 0');
  header('Content-Disposition: attachment; filename="'.$archivo.'"');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');

  $desenrutafinal = array_merge($desenrutafinal);
  $porllegadfinal = array_merge($porllegadfinal);

  echo "<table><tr>";

  if(!$GLOBALS[finali])
   $formulario = new Formulario ("index.php","post","Despachos en Ruta","form_item", "","","100%",0,1);
  else
   $formulario = new Formulario ("index.php","post","Despachos Finalizados","form_item", "","","100%",0,1);

  if(!$GLOBALS[des_retras])
  {
   $formulario -> nueva_tabla();
   if(!$GLOBALS[finali])
    $formulario -> linea("Se Encontro un Total de ".$ind_enrutas." Despacho(s) en Ruta",0,"t2");
  }


  if(!$GLOBALS[finali])
  {
   $formulario -> nueva_tabla();
   for($i=0; $i < sizeof($alarmas); $i++ )
    echo "<td bgcolor=\"".$alarmas[$i][2]."\">".$alarmas[$i][0]." = ".$alarmas[$i][1]." Min</td>";
  }

  $formulario -> nueva_tabla();
  $formulario -> linea("Despacho",0,"i");
  $formulario -> linea("Documento/Despac",0,"i");
  if($manredes)
  {
   $formulario -> linea("Remisi&oacute;n",0,"i");
   $formulario -> linea("Pedido",0,"i");
  }
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea("Destino",0,"t");
  $formulario -> linea("Transportadora",0,"t");
  $formulario -> linea("Placa",0,"t");
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea("Celular",0,"t");
  $formulario -> linea("Fecha Novedad",0,"t");
  $formulario -> linea("Ultimo P/C",0,"t");
  $formulario -> linea("Novedad",0,"t");
	$formulario -> linea("Generador",1,"t");

  for($i = 0; $i < sizeof($desenrutafinal); $i++)
  {
	 $generador = "SELECT a.abr_tercer
							 	 FROM ".BASE_DATOS.".tab_tercer_tercer a,
											".BASE_DATOS.".tab_despac_despac b
								 WHERE a.cod_tercer = b.cod_client AND
											 b.num_despac = '".$desenrutafinal[$i][0]."' ";
	
	 $consulta = new Consulta( $generador, $this -> conexion );
 	 $generador = $consulta -> ret_matriz();
	 $generador = $generador [0][0];
   echo "<td bgcolor=\"".$desenrutafinal[$i][11]."\">".$desenrutafinal[$i][0]." </td>";
   $formulario -> linea($desenrutafinal[$i][1],0,"i");
   if($manredes)
   {
    $formulario -> linea($desenrutafinal[$i][13],0,"i");
    $formulario -> linea($desenrutafinal[$i][14],0,"i");
   }
   $formulario -> linea($desenrutafinal[$i][2],0,"i");
   $formulario -> linea($desenrutafinal[$i][3],0,"i");
   $formulario -> linea($desenrutafinal[$i][4],0,"i");
   $formulario -> linea($desenrutafinal[$i][5],0,"i");
   $formulario -> linea($desenrutafinal[$i][6],0,"i");
   $formulario -> linea($desenrutafinal[$i][7],0,"i");
   $formulario -> linea($desenrutafinal[$i][8],0,"i");
   $formulario -> linea($desenrutafinal[$i][9],0,"i");
   $formulario -> linea($desenrutafinal[$i][10],0,"i");
	 $formulario -> linea($generador,1,"i");
  }

  if($ind_porlleg && !$GLOBALS[des_retras])
  {
   $formulario -> nueva_tabla();

   if(!$GLOBALS[finali])
   {
   	if($manredes && $desurb)
     $formulario -> linea("Se Encontro un Total de ".$ind_porlleg." Despacho(s) Urbanos &oacute; Pendientes por Llegada",0,"t2");
    else
     $formulario -> linea("Se Encontro un Total de ".$ind_porlleg." Despacho(s) Pendientes por Llegada",0,"t2");
   }
   else
    $formulario -> linea("Se Encontro un Total de ".$ind_porlleg." Despacho(s) Finalizado(s)",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Despacho",0,"t");
   $formulario -> linea("Documento/Despac",0,"t");
   if($manredes)
   {
    $formulario -> linea("Remisi&oacute;n",0,"i");
    $formulario -> linea("Pedido",0,"i");
   }
   $formulario -> linea("Origen",0,"t");
   $formulario -> linea("Destino",0,"t");
   $formulario -> linea("Transportadora",0,"t");
   $formulario -> linea("Placa",0,"t");
   $formulario -> linea("Conductor",0,"t");
   $formulario -> linea("Celular",0,"t");
   $formulario -> linea("Fecha Novedad",0,"t");
   $formulario -> linea("Ultimo P/C",0,"t");
   $formulario -> linea("Novedad",1,"t");

   for($i = 0; $i < sizeof($porllegadfinal); $i++)
   {
   	if($porllegadfinal[$i][12])
   	{
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][0]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][1]."</td>";
   	 if($manredes)
   	 {
   	  echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][13]."</td>";
   	  echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][14]."</td>";
   	 }
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][2]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][3]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][4]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][5]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][6]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][7]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][8]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][9]."</td>";
   	 echo "<td bgcolor=\"#99CC99\">".$porllegadfinal[$i][10]."</td>";
   	 echo "</tr><tr>";
   	}
   	else
   	{
     $formulario -> linea($porllegadfinal[$i][0],0,"i");
     $formulario -> linea($porllegadfinal[$i][1],0,"i");
     if($manredes)
      $formulario -> linea($porllegadfinal[$i][12],0,"i");
     $formulario -> linea($porllegadfinal[$i][2],0,"i");
     $formulario -> linea($porllegadfinal[$i][3],0,"i");
     $formulario -> linea($porllegadfinal[$i][4],0,"i");
     $formulario -> linea($porllegadfinal[$i][5],0,"i");
     $formulario -> linea($porllegadfinal[$i][6],0,"i");
     $formulario -> linea($porllegadfinal[$i][7],0,"i");
     $formulario -> linea($porllegadfinal[$i][8],0,"i");
     $formulario -> linea($porllegadfinal[$i][9],0,"i");
     $formulario -> linea($porllegadfinal[$i][10],1,"i");
   	}
   }
  }

  if(!$ind_porlleg)
  {
   $formulario -> nueva_tabla();
   $formulario -> linea("No Se Encontraron Despachos Pendientes por dar Llegada",0,"t2");
  }

  $formulario -> cerrar();
 }

 function expListadoPdf($archivo,$desenrutafinal,$porllegadfinal,$ind_enrutas,$ind_porlleg,$alarmas,$manredes,$desurb)
 {
  $desenrutafinal = array_merge($desenrutafinal);
  $porllegadfinal = array_merge($porllegadfinal);

  $pdf = new FPDF();
  $pdf -> AliasNbPages();
  $pdf -> AddPage('L', 'Letter');

  $pdf -> SetFont('Arial','',7);
  $pdf -> SetTitle($archivo);
  $pdf -> Ln();

  $pdf -> SetFillColor(255,255,255);

  if($desenrutafinal)
  {
   if(!$GLOBALS[des_retras])
   {
    $pdf -> Ln();
    if(!$GLOBALS[finali])
     $pdf -> Cell(190,8,"Se Encontro un Total de ".$ind_enrutas." Despacho(s) en Ruta",1,0,"C");
   }

   if(!$GLOBALS[finali])
   {
    $pdf -> Ln();
    $wxalarma = floor(190 / sizeof($alarmas));

    for($i = 0; $i < sizeof($alarmas); $i++ )
    {
     $totwala += $wxalarma;
     if($i == sizeof($alarmas) - 1)
      $wxalarma += 190 - $totwala;

     $rsultco = $this -> convertHexad($alarmas[$i][2]);
     $pdf -> SetFillColor($rsultco[0],$rsultco[1],$rsultco[2]);
     $pdf -> Cell($wxalarma,8,$alarmas[$i][0]." = ".$alarmas[$i][1]." Min",1,0,"C",1);
    }
   }

   $pdf -> SetFillColor(255,255,255);
   $pdf -> Ln();
   $pdf -> Cell(15,8,"Despacho",1,0,"C",1);
   $pdf -> Cell(15,8,"Documento/Despac",1,0,"C",1);
   if($manredes)
   {
    $pdf -> Cell(10,8,"Remision",1,0,"C",1);
    $pdf -> Cell(10,8,"Pedido",1,0,"C",1);
   }
   $pdf -> Cell(20,8,"Origen",1,0,"C",1);
   $pdf -> Cell(20,8,"Destino",1,0,"C",1);
   $pdf -> Cell(15,8,"Transportadora",1,0,"C",1);
   $pdf -> Cell(15,8,"Placa",1,0,"C",1);
   $pdf -> Cell(20,8,"Conductor",1,0,"C",1);
   $pdf -> Cell(15,8,"Celular",1,0,"C",1);
   $pdf -> Cell(20,8,"Fecha Novedad",1,0,"C",1);
   $pdf -> Cell(20,8,"Ultimo P/C",1,0,"C",1);
   $pdf -> Cell(15,8,"Novedad",1,0,"C",1);

   for($i = 0; $i < sizeof($desenrutafinal); $i++)
   {
   	if($desenrutafinal[$i][11])
    {
     $rsultco = $this -> convertHexad($desenrutafinal[$i][11]);
     $pdf -> SetFillColor($rsultco[0],$rsultco[1],$rsultco[2]);
    }
    $pdf -> Ln();
    $pdf -> Cell(15,8,$desenrutafinal[$i][0],1,0,"C",1);
    $pdf -> SetFillColor(255,255,255);
    $pdf -> Cell(15,8,$desenrutafinal[$i][1],1,0,"C",1);
    if($manredes)
    {
     $pdf -> Cell(10,8,$desenrutafinal[$i][13],1,0,"C",1);
     $pdf -> Cell(10,8,$desenrutafinal[$i][14],1,0,"C",1);
    }
    $pdf -> Cell(20,8,$desenrutafinal[$i][2],1,0,"C",1);
    $pdf -> Cell(20,8,$desenrutafinal[$i][3],1,0,"C",1);
    $pdf -> Cell(15,8,$desenrutafinal[$i][4],1,0,"C",1);
    $pdf -> Cell(15,8,$desenrutafinal[$i][5],1,0,"C",1);
    $pdf -> Cell(20,8,$desenrutafinal[$i][6],1,0,"C",1);
    $pdf -> Cell(15,8,$desenrutafinal[$i][7],1,0,"C",1);
    $pdf -> Cell(20,8,$desenrutafinal[$i][8],1,0,"C",1);
    $pdf -> Cell(20,8,$desenrutafinal[$i][9],1,0,"C",1);
    $pdf -> Cell(15,8,$desenrutafinal[$i][10],1,0,"C",1);
   }
  }

  if($ind_porlleg && !$GLOBALS[des_retras])
  {
   $pdf -> Ln();

   if(!$GLOBALS[finali])
   {
   	if($manredes && $desurb)
     $pdf -> Cell(190,8,"Se Encontro un Total de ".$ind_porlleg." Despacho(s) Urbanos &oacute; Pendientes por Llegada",1,0,"C");
    else
     $pdf -> Cell(190,8,"Se Encontro un Total de ".$ind_porlleg." Despacho(s) Pendientes por Llegada",1,0,"C");
   }
   else
    $pdf -> Cell(190,8,"Se Encontro un Total de ".$ind_porlleg." Despacho(s) Finalizado(s)",1,0,"C");

   $pdf -> Ln();
   $pdf -> Cell(15,8,"Despacho",1,0,"C",1);
   $pdf -> Cell(15,8,"Documento/Despac",1,0,"C",1);
   if($manredes)
   {
    $pdf -> Cell(10,8,"Remision",1,0,"C",1);
    $pdf -> Cell(10,8,"Pedido",1,0,"C",1);
   }
   $pdf -> Cell(20,8,"Origen",1,0,"C",1);
   $pdf -> Cell(20,8,"Destino",1,0,"C",1);
   $pdf -> Cell(15,8,"Transportadora",1,0,"C",1);
   $pdf -> Cell(15,8,"Placa",1,0,"C",1);
   $pdf -> Cell(20,8,"Conductor",1,0,"C",1);
   $pdf -> Cell(15,8,"Celular",1,0,"C",1);
   $pdf -> Cell(20,8,"Fecha Novedad",1,0,"C",1);
   $pdf -> Cell(20,8,"Ultimo P/C",1,0,"C",1);
   $pdf -> Cell(15,8,"Novedad",1,0,"C",1);

   for($i = 0; $i < sizeof($porllegadfinal); $i++)
   {
   	$pdf -> SetFillColor(255,255,255);

	if($porllegadfinal[$i][12])
	{
   	 $rsultco = $this -> convertHexad("#99CC99");
     $pdf -> SetFillColor($rsultco[0],$rsultco[1],$rsultco[2]);
	}

    $pdf -> Ln();
    $pdf -> Cell(15,8,$porllegadfinal[$i][0],1,0,"C",1);
    $pdf -> Cell(15,8,$porllegadfinal[$i][1],1,0,"C",1);
    if($manredes)
    {
     $pdf -> Cell(10,8,$porllegadfinal[$i][13],1,0,"C",1);
     $pdf -> Cell(10,8,$porllegadfinal[$i][14],1,0,"C",1);
    }
    $pdf -> Cell(20,8,$porllegadfinal[$i][2],1,0,"L",1);
    $pdf -> Cell(20,8,$porllegadfinal[$i][3],1,0,"L",1);
    $pdf -> Cell(15,8,$porllegadfinal[$i][4],1,0,"L",1);
    $pdf -> Cell(15,8,$porllegadfinal[$i][5],1,0,"C",1);
    $pdf -> Cell(20,8,$porllegadfinal[$i][6],1,0,"L",1);
    $pdf -> Cell(15,8,$porllegadfinal[$i][7],1,0,"R",1);
    $pdf -> Cell(20,8,$porllegadfinal[$i][8],1,0,"R",1);
    $pdf -> Cell(20,8,$porllegadfinal[$i][9],1,0,"L",1);
    $pdf -> Cell(15,8,$porllegadfinal[$i][10],1,0,"C",1);
   }
  }

  if(!$ind_porlleg)
  {
   $pdf -> Ln();
   $pdf -> Cell(190,8,"No Se Encontraron Despachos Pendientes por dar Llegada",1,0,"C");
  }

  $pdf -> Footer();
  $pdf -> Output();
 }

 function convertHexad($color)
 {
  if($color[0] == '#')
   $color = substr($color, 1);

  if(strlen($color) == 6)
   list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

  $r = hexdec($r);
  $g = hexdec($g);
  $b = hexdec($b);

  return array($r, $g, $b);
 }
}

class PDF extends FPDF
{
 function Header()
 {
  $this->SetFont('Arial','B',8);
 }

 function Footer()
 {
  $this -> SetY(-15);
  $this -> SetFont('Arial','I',8);
  $this -> Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
 }
}

$proceso = new Proc_exp_enruta();
?>
<?php
/****************************************************************************
NOMBRE:   INS_DESPAC_TERCER.PHP
FUNCION:  INSERTAR DESPACHOS TERCEROS
****************************************************************************/
class Proc_despac
{
 var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
    //Metodos
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $datos_usuario = $this -> usuario -> retornar();
  $this -> principal();
 }
//********METODOS
 function principal()
 {
  if(!isset($_REQUEST[opcion]))
     $this -> Formulario1();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "1":
          $this -> Formulario1();
          break;
        case "2":
          $this -> Insertar();
          echo "<br>";
          $this -> Formulario1();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************
// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA
// *****************************************************
 function Formulario1()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i");
   //presenta por defecta la fecha actual
   if(!isset($_REQUEST[fecpla]))
      $_REQUEST[fecpla]=$fec_actual;
   if(!isset($_REQUEST[horpla]))
      $_REQUEST[horpla]=$hor_actual;
   $inicio[0][0]='0';
   $inicio[0][1]='-';

   $ncarava[0][0]= "n";
   $ncarava[0][1]= "Nueva Carvana";
   $scarava[0][0]= "s";
   $scarava[0][1]= "Sin Carvana";


  //trae la transportadora
   $query = "SELECT a.cod_tercer, b.abr_tercer
           FROM ".BASE_DATOS.".tab_tercer_emptra a,
		".BASE_DATOS.".tab_tercer_tercer b
	   WHERE a.cod_tercer = b.cod_tercer";
       $consulta = new Consulta($query, $this -> conexion);
       $transpor = $consulta -> ret_matriz();

   //trae la agencia anterior
   $query = "SELECT a.cod_agenci,a.nom_agenci
             FROM ".BASE_DATOS.".tab_genera_agenci a 
             WHERE a.cod_agenci = '$_REQUEST[agencia]'";

   $consulta = new Consulta($query, $this -> conexion);
   $agencia_a = $consulta -> ret_matriz();

   //trae las agencias de la transportadora
   $query = "SELECT a.cod_agenci,a.nom_agenci
               FROM ".BASE_DATOS.".tab_genera_agenci a
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $agencias = $consulta -> ret_matriz();
   $agencias = array_merge($agencia_a,$inicio,$agencias);

   //trae el cliente anterior
   $query = "SELECT cod_tercer,abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer
              WHERE cod_tercer = '$_REQUEST[cliente]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $cliente_a = $consulta -> ret_matriz();

   //trae las clientes
     $query = "SELECT a.cod_tercer,a.abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,".BASE_DATOS.".tab_tercer_activi b,
                    ".BASE_DATOS.". tab_tercer_client c
              WHERE a.cod_tercer = b.cod_tercer AND
                    a.cod_tercer = c.cod_tercer AND
                    b.cod_activi = 10
           ORDER BY 2 ";

   $consulta = new Consulta($query, $this -> conexion);
   $clientes = $consulta -> ret_matriz();
   $clientes = array_merge($cliente_a,$inicio,$clientes);

   //trae la aseguradora anterior
   $query = "SELECT cod_tercer,abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer
              WHERE cod_tercer = '$_REQUEST[asegra]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $asegra_a = $consulta -> ret_matriz();

   //trae la aseguradora de la empresa transportadora
   $query = "SELECT cod_tercer,abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer
              WHERE cod_tercer = '".$transpor[0][2]."' ";
   $consulta = new Consulta($query, $this -> conexion);
   $asegra_emptra = $consulta -> ret_matriz();

   //trae las aseguradoras
   $query = "SELECT a.cod_tercer,a.abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,".BASE_DATOS.".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = 5
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $asegras = $consulta -> ret_matriz();

   if($_REQUEST[asegra])
   $asegras = array_merge($asegra_a,$inicio,$asegras);
   else
   $asegras = array_merge($asegra_emptra,$inicio,$asegras);

   //trae las ciudades de origen
   $query = "SELECT a.cod_ciudad,a.abr_ciudad
               FROM ".BASE_DATOS.".tab_genera_ciudad a,
		    ".BASE_DATOS.".tab_genera_rutasx b
              WHERE a.cod_ciudad = b.cod_ciuori AND
		    b.ind_estado = '1'
           	    GROUP BY 1 ORDER BY 2 ";

   $consulta = new Consulta($query, $this -> conexion);
   $ciuoris = $consulta -> ret_matriz();

   $ciuoris = array_merge($inicio,$ciuoris);

   if($_REQUEST[ciuori])
   {
    //trae la ciudad de origen anterior
    $query = "SELECT cod_ciudad,abr_ciudad
                FROM ".BASE_DATOS.".tab_genera_ciudad
               WHERE cod_ciudad = ".$_REQUEST[ciuori]."
	     ";

    $consulta = new Consulta($query, $this -> conexion);
    $ciuori_a = $consulta -> ret_matriz();

    $ciuoris = array_merge($ciuori_a,$ciuoris);

    //trae las ciudades de destino
    $query = "SELECT a.cod_ciudad,a.abr_ciudad
                FROM ".BASE_DATOS.".tab_genera_ciudad a,
		     ".BASE_DATOS.".tab_genera_rutasx b
               WHERE a.cod_ciudad = b.cod_ciudes AND
                     b.cod_ciuori = ".$_REQUEST[ciuori]." AND
		     b.ind_estado = '1'
           	     GROUP BY 1 ORDER BY 2 ";

    $consulta = new Consulta($query, $this -> conexion);
    $ciudess = $consulta -> ret_matriz();

    $ciudess = array_merge($inicio,$ciudess);

    if($_REQUEST[ciudes])
    {
     //trae la ciudad destino anterior
     $query = "SELECT cod_ciudad,abr_ciudad
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = ".$_REQUEST[ciudes]."
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $ciudes_a = $consulta -> ret_matriz();

     $ciudess = array_merge($ciudes_a,$ciudess);

     //trae las rutas segun la ciudad de origen y la ciudad de destino
     $query = "SELECT a.cod_rutasx,a.nom_rutasx
                 FROM ".BASE_DATOS.".tab_genera_rutasx a
                WHERE a.cod_ciuori = ".$_REQUEST[ciuori]." AND
                      a.cod_ciudes = ".$_REQUEST[ciudes]." AND
		      a.ind_estado = '1' 
                      GROUP BY 1 ORDER BY 2 ";

     $consulta = new Consulta($query, $this -> conexion);
     $rutas = $consulta -> ret_matriz();

     $rutas = array_merge($inicio,$rutas);

     if($_REQUEST[ruta])
     {
      //trae la ruta anterior
      $query = "SELECT cod_rutasx,nom_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx
                 WHERE cod_rutasx = ".$_REQUEST[ruta]."
           	       ORDER BY 2 ";

      $consulta = new Consulta($query, $this -> conexion);
      $ruta_a = $consulta -> ret_matriz();

      $rutas = array_merge($ruta_a,$rutas);
     }
    }
   }

   //trae el tipo anterior
   $query = "SELECT cod_tipdes,nom_tipdes
               FROM ".BASE_DATOS.".tab_genera_tipdes
              WHERE cod_tipdes = '$_REQUEST[tipdes]'
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $tipdes_a = $consulta -> ret_matriz();

   //trae los tipos de despacho
   $query = "SELECT cod_tipdes,nom_tipdes
               FROM ".BASE_DATOS.".tab_genera_tipdes
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $tipdess = $consulta -> ret_matriz();
   $tipdess = array_merge($tipdes_a,$inicio,$tipdess);


   //valida si hay caravanas en la ruta seleccionada
  $query = "SELECT a.num_carava, a.num_carava
            FROM   ".BASE_DATOS.".tab_despac_despac a
            WHERE a.fec_salida IS NULL AND
                  a.fec_llegad IS NULL AND
                  a.ind_anulad = 'R' AND
                  a.num_carava != 0 AND
                  a.cod_ciuori = '".$_REQUEST[ciuori]."' AND
                  a.cod_ciudes = '".$_REQUEST[ciudes]."'
            GROUP BY 1
            ORDER BY 1 ";
        $consulta = new Consulta($query, $this -> conexion);
        $carava = $consulta -> ret_matriz();

  //trae la caravana actual
  $query = "SELECT a.num_carava, a.num_carava
                FROM ".BASE_DATOS.".tab_despac_despac a
                WHERE a.fec_salida IS NULL AND
                      a.fec_llegad IS NULL AND
                      a.ind_anulad = 'R' AND
                  	  a.num_carava != 0 AND
                      a.num_carava = '".$_REQUEST[num_carava]."' AND
                      a.cod_ciuori = '".$_REQUEST[ciuori]."' AND
                      a.cod_ciudes = '".$_REQUEST[ciudes]."'
                GROUP BY 1
                ORDER BY 1 ";
        $consulta = new Consulta($query, $this -> conexion);
        if($carava_a = $consulta -> ret_matriz())
        	$carava = array_merge($carava_a,$inicio,$carava, $ncarava, $scarava);
        else if($_REQUEST[num_carava] == "n")
                $carava = array_merge($ncarava,$inicio,$carava,$scarava);
        else if($_REQUEST[num_carava] == "s" )
        	$carava = array_merge($scarava, $inicio,$carava,$ncarava);
        else
        	$carava = array_merge($inicio,$carava, $ncarava, $scarava);

        //valida que el vehiculo no este asignado a la caravana
        $query = "SELECT a.num_placax, b.num_despac
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
                       ".BASE_DATOS.".tab_despac_despac b
                  WHERE a.num_despac = b.num_despac AND
                       	b.num_carava != '0' AND
                        b.num_carava = '".$_REQUEST[num_carava]."' AND
                        a.num_placax = '".$_REQUEST[placa]."' AND
                        b.ind_anulad = 'R'";
        $consulta = new Consulta($query, $this -> conexion);
        $ind_placa = $consulta -> ret_arreglo();

   //trae la mercancia anterior
   $query = "SELECT cod_mercan,abr_mercan
               FROM ".BASE_DATOS.".tab_genera_mercan
              WHERE cod_mercan = '$_REQUEST[mercan]' AND
                                ind_activa = '1'
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $mercan_a = $consulta -> ret_matriz();

   //trae las mercancias que tienen abreviatura
   $query = "SELECT cod_mercan,abr_mercan
               FROM ".BASE_DATOS.".tab_genera_mercan
              WHERE ind_activa = '1'
           ORDER BY 2 ";
   $consulta = new Consulta($query, $this -> conexion);
   $mercans = $consulta -> ret_matriz();
   $mercans = array_merge($mercan_a,$inicio,$mercans);

   //TRAE LOS DATOS DEL VEHICULO Y DEL CONDUCTOR
  $query = "SELECT a.num_placax,b.nom_marcax,c.nom_lineax,d.nom_colorx,
                   e.nom_carroc,a.ano_modelo,a.num_config,
                   g.cod_tercer,CONCAT(g.nom_tercer,' ',g.nom_apell1),g.num_telmov
            FROM ".BASE_DATOS.".tab_vehicu_vehicu a,".BASE_DATOS.".tab_genera_marcas b,
                 ".BASE_DATOS.".tab_vehige_lineas c,".BASE_DATOS.".tab_vehige_colore d,
                 ".BASE_DATOS.".tab_vehige_carroc e,".BASE_DATOS.".tab_vehige_config f,
                 ".BASE_DATOS.".tab_tercer_tercer g,".BASE_DATOS.".tab_tercer_conduc h
           WHERE a.cod_marcax = b.cod_marcax AND
                 a.cod_marcax = c.cod_marcax AND
                 a.cod_lineax = c.cod_lineax AND
                 a.cod_colorx = d.cod_colorx AND
                 a.cod_carroc = e.cod_carroc AND
                 a.cod_conduc = g.cod_tercer AND
                 g.cod_tercer = h.cod_tercer AND
                 a.num_config = f.num_config AND
                 a.num_placax = '$_REQUEST[placa]'
            ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  if(!$placas = $consulta -> ret_matriz())
  	unset($_REQUEST[placa]);

  //trae los conductores
  $query = "SELECT a.cod_tercer, CONCAT(a.nom_tercer,' ',a.nom_apell1),a.num_telmov
            FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b,
                 ".BASE_DATOS.".tab_tercer_conduc c
            WHERE a.cod_tercer = c.cod_tercer AND
                  a.cod_tercer = b.cod_tercer AND
                  b.cod_activi = '16' 
				  ORDER BY 2
				  ";
   $consulta = new Consulta($query, $this -> conexion);
   $conducs  = $consulta -> ret_matriz();

  $query = "SELECT a.cod_tercer, CONCAT(a.nom_tercer,' ',a.nom_apell1),a.num_telmov
            FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b,
                 ".BASE_DATOS.".tab_tercer_conduc c
            WHERE a.cod_tercer = c.cod_tercer AND
                  a.cod_tercer = b.cod_tercer AND
                  b.cod_activi = '16' AND
                  a.cod_tercer = '$_REQUEST[conduc]' ";
   $consulta = new Consulta($query, $this -> conexion);
   $conduc_e  = $consulta -> ret_matriz();

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fecha.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","","form_insert");
   $formulario -> linea("Datos Basicos del Despacho",1);
   $formulario -> nueva_tabla();
   $formulario -> texto ("Manifiesto N:","text","manifi",0,7,7,"","$_REQUEST[manifi]");
   $formulario -> lista("Cliente:", "cliente", $clientes, 1);
   $formulario -> lista("Aseguradora:", "asegra", $asegras, 1);
   $formulario -> lista("Agencia:", "agencia", $agencias, 0);
   $formulario -> lista("Origen:", "ciuori\" onBlur=\"form_insert.submit()\"", $ciuoris, 1);
   $formulario -> lista("Destino:", "ciudes\" onBlur=\"form_insert.submit()\"", $ciudess, 0);
   $formulario -> lista("Ruta:", "ruta", $rutas, 0);
   $formulario -> nueva_tabla();
   $formulario -> texto ("Fecha Salida (dd-mm-yyyy)","text","fecpla",0,10,10,"","$_REQUEST[fecpla]");
   $formulario -> texto ("Hora Salida (HH:mm)","text","horpla",0,5,5,"","$_REQUEST[horpla]");
   $formulario -> nueva_tabla();
   $formulario -> lista("Identificacion de Caravana:", "num_carava\" onChange=\"form_insert.submit()\"", $carava, 0);
   if($_REQUEST[num_carava] == 'n')
   {
   	if(!$_REQUEST[n_carava])
   	{
   		$query = "SELECT MAX(num_carava)+1
   					FROM ".BASE_DATOS.".tab_despac_despac
   					WHERE ind_anulad = 'R'";
   		$consulta = new Consulta($query, $this -> conexion);
   		$n_carava = $consulta -> ret_arreglo();
   		$_REQUEST[n_carava] = $n_carava[0];
   	}
   	else
   	{
   		$query = "SELECT num_carava
   					FROM ".BASE_DATOS.".tab_despac_despac
   					WHERE ind_anulad = 'R'
   					  AND num_carava = '".$_REQUEST[n_carava]."'";
   		$consulta = new Consulta($query, $this -> conexion);
   		if($consulta -> ret_arreglo())
   		{
   			$_REQUEST[n_carava] = '';
	 		$mensajes_error++;
	 		echo "<br><div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">La Caravana <b>".$_REQUEST[n_carava]."</b> se Encuentra Asignada</b>, Asigne Otro N&uacute;mero.</small></div>";
   		}
   	}   	
   	$formulario -> texto ("No.","text","n_carava",0,4,4,"","$_REQUEST[n_carava]");
   }
   else
   	$formulario -> oculto("n_carava",0,0);
   
   if($ind_placa)
   {
   	$mensajes_error++;
	echo "<br><div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">El vehiculo placas <b>".$ind_placa[0]."</b> ya se Encuentra Asignado a esta Caravana con el Despacho  <b>".$ind_placa[1]."</b></small></div>";
  	$formulario -> oculto("ind_placa",$ind_placa[0],0);
   }

   if($placas)
   $formulario -> oculto("regplaca",1,0);
   else
   $formulario -> oculto("regplaca",0,0);
   $formulario -> nueva_tabla();
   $formulario -> texto ("Placa N:","text","placa\" onChange=\"form_insert.submit()\"",0,8,8,"","$_REQUEST[placa]");
   //si la placa digitada trae un registro
   //imprime los datos del vehiculo y del conductor
   if(sizeof($placas) == 1)
   {
     $conduc_a[0][0] = $placas[0][7];
     $conduc_a[0][1] = $placas[0][8];

     echo "<td class=\"etiqueta\">Marca:</td><td class=\"celda\">".$placas[0][1]."</td>";
     echo "<td class=\"etiqueta\">Linea:</td><td class=\"celda\">".$placas[0][2]."</td>";
     echo "</tr><tr>";
     echo "<td class=\"etiqueta\">Color:</td><td class=\"celda\">".$placas[0][3]."</td>";
     echo "<td class=\"etiqueta\">Carroceria:</td><td class=\"celda\">".$placas[0][4]."</td>";
     echo "</tr><tr>";
     echo "<td class=\"etiqueta\">Modelo:</td><td class=\"celda\">".$placas[0][5]."</td>";
     echo "<td class=\"etiqueta\">Configuracion:</td><td class=\"celda\">".$placas[0][6]."</td>";
     $formulario -> nueva_tabla();
     echo "<td class=\"etiqueta\"><b>Datos del Conductor</b></td>";

     if($_REQUEST[conduc]  != null)
     {
     $conducs = array_merge($conduc_e, $inicio, $conducs);
     $formulario -> nueva_tabla();
     echo "<td class=\"etiqueta\">CC:</td><td class=\"celda\">".$conduc_e[0][0]."</td>";
     $formulario -> lista("Conductor: ", "conduc\" onChange=\"form_insert.submit()\"", $conducs, 1);
     echo "<td class=\"etiqueta\">Telefono:</td><td class=\"celda\">".$conduc_e[0][2]."</td>";
     echo "</tr><tr>";
     }
     else
     {
     $conducs = array_merge($conduc_a, $inicio, $conducs);
     $formulario -> nueva_tabla();
     echo "<td class=\"etiqueta\">CC:</td><td class=\"celda\">".$placas[0][7]."</td>";
     $formulario -> lista("Conductor: ", "conduc\" onChange=\"form_insert.submit()\"", $conducs, 1);
     echo "<td class=\"etiqueta\">Telefono:</td><td class=\"celda\">".$placas[0][9]."</td>";
     echo "</tr><tr>";
     }

     $query = "SELECT a.num_trayle
     		 FROM ".BASE_DATOS.".tab_trayle_placas a,
		      ".BASE_DATOS.".tab_vehige_trayle b
     		WHERE a.ind_actual = 'S' AND
		      a.num_trayle = b.num_trayle AND
		      b.ind_estado = '1' AND
		      a.num_placax = '".$placas[0][0]."'
		      ORDER BY a.fec_asigna DESC
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $trayler = $consulta -> ret_vector();

     $query = "SELECT a.num_trayle,a.num_trayle
     		 FROM ".BASE_DATOS.".tab_vehige_trayle a
	        WHERE a.ind_estado = '1'
     		      ORDER BY 1
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $listatra = $consulta -> ret_matriz();

     $listatra = array_merge($inicio,$listatra);

     $formulario -> nueva_tabla();
     echo "<td class=\"etiqueta\"><b>Informaci&oacute;n del Remolque</b></td>";
     echo "</tr><tr>";

     if($placas[0][6] == "2" || $placas[0][6] == "3" || $placas[0][6] == "4")
     {
       echo "<td class=\"etiqueta\">La Configuraci&oacute;n Actual del Vehiculo no Solicita Remolque.</td>";
       $formulario -> oculto("l_trayle",null,0);
     }
     else if($trayler)
     {
       if(!$_REQUEST[l_trayle])
       {
        $mi_trayler[0][0] = $trayler[0];
        $mi_trayler[0][1] = $trayler[0];
       }
       else
       {
        $mi_trayler[0][0] = $_REQUEST[l_trayle];
        $mi_trayler[0][1] = $_REQUEST[l_trayle];
       }

       $listatra = array_merge($mi_trayler,$listatra);

       $formulario -> lista("Remolque:", "l_trayle", $listatra, 1);
     }
     else
     {
       if($_REQUEST[l_trayle])
       {
        $mi_trayler[0][0] = $_REQUEST[l_trayle];
        $mi_trayler[0][1] = $_REQUEST[l_trayle];

        $listatra = array_merge($mi_trayler,$listatra);
       }

       $formulario -> lista("Remolque:", "l_trayle", $listatra, 1);
       echo "<td class=\"etiqueta\">El Vehiculo Solicita una Asignaci&oacute;n De Remolque en su Informaci&oacute;n Base</td>";
     }

   }//fin if
   $formulario -> nueva_tabla();
   $formulario -> lista("Mercancia:", "mercan", $mercans, 0);
   $formulario -> lista("Tipo Despacho:", "tipdes", $tipdess, 1);
   $formulario -> texto ("Valor Flete Cliente:","text","val_flecli",0,10,10,"","$_REQUEST[val_flecli]");
   $formulario -> texto ("Valor Flete Conductor:","text","val_flecon",1,10,10,"","$_REQUEST[val_flecon]");
   $formulario -> nueva_tabla();
   $formulario -> texto ("Protecciones Especiales:","textarea","protec",1,50,2,"","$_REQUEST[protec]");
   $formulario -> texto ("Medios de Comunicacion:","textarea","medcom",1,50,2,"","$_REQUEST[med_com]");
   $formulario -> texto ("Observaciones Generales:","textarea","obsgrl",1,50,2,"","$_REQUEST[obsgrl]");
   $formulario -> nueva_tabla();
   $formulario -> oculto("transpor",$transpor[0][0],0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
   $formulario -> botoni("Aceptar","aceptar_insert()",0);
   $formulario -> botoni("Borrar","form_insert.reset()",1);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA

// *****************************************************
//FUNCION INSERTAR
// *****************************************************
 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   $fecha=explode("-",$_REQUEST[fecpla]);
   $fecha=$fecha[2]."-".$fecha[1]."-".$fecha[0]." ".$_REQUEST[horpla].":00";

   if(!$_REQUEST[l_trayle])
    $_REQUEST[l_trayle] = "null";
   else
    $_REQUEST[l_trayle] = "'".$_REQUEST[l_trayle]."'";
   
   //Se trae la aseguradora y número de poliza actual
	$query = "SELECT a.cod_asegra,a.num_poliza
                 FROM ".BASE_DATOS.".tab_poliza_tercer a
                WHERE a.cod_tercer = '".NIT_TRANSPOR."' AND
		      a.fec_modifi = (select Max(b.fec_modifi) from tab_poliza_tercer b where b.cod_tercer = a.cod_tercer) 
	    ";

   	$consulta = new Consulta($query, $this -> conexion);
   	$datospoli = $consulta -> ret_arreglo();


   //trae el consecutivo de la tabla
   $query = "SELECT Max(num_despac) AS maximo
               FROM ".BASE_DATOS.".tab_despac_despac ";
   $consec = new Consulta($query, $this -> conexion);
   $ultimo = $consec -> ret_matriz();
   $ultimo_consec = $ultimo[0][0];
   $nuevo_consec = $ultimo_consec+1;

   if($_REQUEST[num_carava] == 's' || $_REQUEST[num_carava] == 'n')
   		$_REQUEST[num_carava] = $_REQUEST[n_carava];

   //query de insercion de despachos
   $query = "INSERT INTO ".BASE_DATOS.".tab_despac_despac (num_despac,cod_manifi,fec_despac,cod_tipdes,cod_client,
		cod_ciuori,cod_ciudes,val_flecon,cod_agedes,cod_unimed,cod_natemp,obs_despac,num_carava,cod_asegra,num_poliza,usr_creaci,
		fec_creaci)
             VALUES ('$nuevo_consec','$_REQUEST[manifi]','$fec_actual','$_REQUEST[tipdes]','".$_REQUEST[cliente]."',
		     '$_REQUEST[ciuori]','$_REQUEST[ciudes]','$_REQUEST[val_flecon]','$_REQUEST[agencia]','1','1',
		     '$_REQUEST[obsgrl]','$_REQUEST[num_carava]','".$datospoli[0]."','".$datospoli[1]."','$_REQUEST[usuario]','$fec_actual') ";
   $consulta = new Consulta($query, $this -> conexion, "BR");

   //query de insercion de despachos vehiculos
   $query = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige (num_despac, cod_transp, cod_agenci, cod_rutasx, cod_conduc, num_placax, num_trayle, obs_proesp, obs_medcom, fec_salipl, ind_activo, usr_creaci, fec_creaci)

             VALUES ('$nuevo_consec','$_REQUEST[transpor]','$_REQUEST[agencia]','$_REQUEST[ruta]',
             '$_REQUEST[conduc]','$_REQUEST[placa]',".$_REQUEST[l_trayle].",'$_REQUEST[protec]','$_REQUEST[medcom]',
             '$fecha','R',
             '$_REQUEST[usuario]','$fec_actual') ";

   $consulta = new Consulta($query, $this -> conexion, "R");

   if(!$consulta = new Consulta("COMMIT", $this -> conexion));
   else
    $mensaje ="El vehiculo con Placas $_REQUEST[placa] fue
            asignado al despacho No<font size = '3'>$nuevo_consec";

   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">$mensaje <hr>";
   
   unset($_REQUEST[manifi]);
   unset($_REQUEST[tipdes]);
   unset($_REQUEST[cliente]);
   unset($_REQUEST[ciuori]);
   unset($_REQUEST[ciudes]);
   unset($_REQUEST[val_flecon]);
   unset($_REQUEST[agencia]);
   unset($_REQUEST[obsgrl]);
   unset($_REQUEST[num_carava]);
   unset($_REQUEST[transpor]);
   unset($_REQUEST[ruta]);
   unset($_REQUEST[conduc]);
   unset($_REQUEST[placa]);
   unset($_REQUEST[l_trayle]);
   unset($_REQUEST[protec]);
   unset($_REQUEST[medcom]);
   unset($_REQUEST[asegra]);
   unset($_REQUEST[val_flecli]);
   unset($_REQUEST[fecpla]);
   unset($_REQUEST[horpla]);
 }//FIN FUNCTION INSERTAR

}//FIN CLASE PROC_DESPAC
     $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
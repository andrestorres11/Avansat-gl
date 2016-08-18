<?php

class Proc_planruimp
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
  {
   $this -> Listar();
  }
  else
  {
        switch($GLOBALS[opcion])
        {
          case "0":
          $this -> Listar();
          break;

          case "1":
          if(file_exists("planru/imp_despac_planru.php"))
            {
             include("planru/imp_despac_planru.php");
             Imprimir_propio($this -> conexion);
            }
          else
            $this -> Imprimir();
          break;
        }//FIN SWITCH
   }// FIN ELSE GLOBALS OPCION
}//FIN FUNCION PRINCIPAL

function Listar()
{
  $datos_usuario = $this -> usuario -> listar($this -> conexion);
  $datos_usuario = $this -> usuario -> retornar();

  $usuario=$datos_usuario["cod_usuari"];

  //listado de despachos con salida
  $query = "SELECT a.num_despac,a.cod_manifi,d.num_placax,a.cod_ciuori,a.cod_ciudes
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_genera_ciudad b,
                 ".BASE_DATOS.".tab_genera_ciudad c,
                 ".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_vehicu_vehicu f
            WHERE a.cod_ciuori = b.cod_ciudad AND
                  a.cod_ciudes = c.cod_ciudad AND
                  a.num_despac = d.num_despac AND
                  f.num_placax = d.num_placax AND
                  a.ind_planru = 'S' AND
                  a.ind_anulad = 'R'
              ";

        if($datos_usuario["cod_perfil"] == "")
      {
              //PARA EL FILTRO DE CONDUCTOR
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE PROPIETARIO
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE POSEEDOR
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE TRANSPORTADORA
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DEL CLIENTE
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE LA AGENCIA
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
              }
      }
      else
      {
        //PARA EL FILTRO DE CONDUCTOR
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE PROPIETARIO
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE POSEEDOR
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE ASEGURADORA
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DEL CLIENTE
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE LA AGENCIA
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
              }
      }
      //final de los filtros asignados al usuario o perfil actual


     $query = $query. " ORDER BY a.fec_modifi DESC LIMIT 50 ";



     $consulta = new Consulta($query, $this -> conexion);

     $matriz = $consulta -> ret_matriz();



     $formulario = new Formulario ("index.php","post","Imprimir Plan de Ruta","form_lispalnruimp");
     $formulario -> linea("Ultimos 50 Despachos",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> linea("Despacho",0,"t");
     $formulario -> linea("Documento/Despacho",0,"t");
     $formulario -> linea("Vehiculo",0,"t");
     $formulario -> linea("Origen",0,"t");
     $formulario -> linea("Destino",1,"t");

	 $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

     for($i = 0; $i < sizeof($matriz); $i++)
     {
      $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][3]);
      $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][4]);

      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&despac=".$matriz[$i][0]."&opcion=1\" target=\"centralFrame\">".$matriz[$i][0]."</a>";

      $formulario -> linea($matriz[$i][0],0,"i");
      $formulario -> linea($matriz[$i][1],0,"i");
      $formulario -> linea($matriz[$i][2],0,"i");
      $formulario -> linea($ciudad_o[0][1],0,"i");
      $formulario -> linea($ciudad_d[0][1],1,"i");
     }

	 $formulario -> nueva_tabla();
     $formulario -> linea("Busqueda Por Numero de Despacho",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto("Despacho","text","despac",1,15,7,"","");

     $formulario -> nueva_tabla();
     $formulario -> botoni("Buscar","form_lispalnruimp.submit()",0);

     $formulario -> nueva_tabla();
     $formulario -> linea("IMPORTANTE: Recuerde Configurar su Navegador Para Imprimir Apropiadamente <br>Haga Clic en Archivo -> Configurar Pagina. Aqui Elija el Tama�o Carta, Todas Las Margenes Con el Valor en 0 y Borrar el Encabezado y el Pie de Pagina",0,"i","75%");
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",1,0);
     $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

     $formulario -> cerrar();
}//FIN FUNCION LISTAR



function Imprimir()
{
  $datos_usuario = $this -> usuario -> retornar();
  $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_genera_ciudad b,
                 ".BASE_DATOS.".tab_genera_ciudad c,
                 ".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_vehicu_vehicu f
            WHERE a.cod_ciuori = b.cod_ciudad AND
                  a.cod_ciudes = c.cod_ciudad AND
                  a.num_despac = d.num_despac AND
                  f.num_placax = d.num_placax AND
                  a.ind_planru = 'S' AND
                  a.ind_anulad = 'R' AND
                  a.num_despac = '$GLOBALS[despac]'
              ";

        if($datos_usuario["cod_perfil"] == "")
      {
              //PARA EL FILTRO DE CONDUCTOR
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE PROPIETARIO
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE POSEEDOR
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE ASEGURADORA
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DEL CLIENTE
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE LA AGENCIA
              $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
              }
      }
      else
      {
        //PARA EL FILTRO DE CONDUCTOR
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE PROPIETARIO
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
              }
        //PARA EL FILTRO DE POSEEDOR
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                      $datos_filtro = $filtro -> retornar();
                $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE ASEGURADORA
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DEL CLIENTE
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
              }

              //PARA EL FILTRO DE LA AGENCIA
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
              }
      }
      //final de los filtros asignados al usuario o perfil actual

     $query = $query. " ORDER BY 1 DESC LIMIT 10 ";

     $consulta = new Consulta($query, $this -> conexion);
     $existe = $consulta -> ret_matriz();

     if(!$existe)
     {
      $formulario = new Formulario ("index.php","post","Imprimir Plan de Ruta","form_lispalnruimp");

      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Intentar de Nuevo</a></b>";

      $mensaje =  "El Despacho # <b>".$GLOBALS[despac]."</b> no se Encuentra Registrado &oacute; el Perfil Actual del Usuario no Tiene los Permisos Correspondientes.".$link_a;
      $mens = new mensajes();
      $mens -> correcto("IMPRIMIR PLAN DE RUTA",$mensaje);

      $formulario -> cerrar();
     }
 	 else
     {
      //trae los datos de la ruta
      $query = "SELECT b.cod_rutasx,b.nom_rutasx
                 FROM ".BASE_DATOS.".tab_despac_vehige a,
                   	  ".BASE_DATOS.".tab_genera_rutasx b
              	WHERE a.num_despac = ".$GLOBALS[despac]." AND
                      a.cod_rutasx = b.cod_rutasx
              		  GROUP BY 1
              ";

      $ruta_a = new Consulta($query, $this -> conexion);
      $ruta_a = $ruta_a -> ret_matriz();

	  //trae los datos de la transportadora
      $query = "SELECT a.cod_transp,b.abr_tercer,b.dir_domici
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
                   	   ".BASE_DATOS.".tab_tercer_tercer b
              	 WHERE a.num_despac = ".$GLOBALS[despac]." AND
                       b.cod_tercer = a.cod_transp
               	 	   GROUP BY 1
               ";

      $transpor = new Consulta($query, $this -> conexion);
      $transpor = $transpor -> ret_matriz();

      $query = "SELECT '',b.nom_tercer,b.cod_tercer,
                       b.num_verifi,b.dir_domici,b.num_telef1,
                       c.nom_ciudad,b.num_telef1,b.num_telef2
                  FROM ".BASE_DATOS.".tab_tercer_tercer b,
                       ".BASE_DATOS.".tab_genera_ciudad c
                 WHERE b.cod_tercer = '".$transpor[0][0]."' AND
                       b.cod_ciudad = c.cod_ciudad
                ";

      $consulta = new Consulta($query, $this -> conexion);
      $obsplan = $consulta -> ret_arreglo();
//Jorge 12-06-2012 IF(num_carava='n','Sin Caravana',num_carava)
      $query = "SELECT a.cod_manifi,a.cod_ciuori,a.cod_ciudes,
                       DATE_FORMAT(b.fec_salipl,'%Y-%m-%d %H:%i'),
                       b.num_placax,h.nom_marcax,i.ano_modelo,j.nom_colorx,
                       k.nom_carroc,d.nom_tercer, b.cod_conduc,c.num_licenc,
                       e.nom_catlic,d.num_telmov,d.dir_ultfot,i.dir_fotfre,
                       IF(a.num_carava='0','Sin Caravana',a.num_carava),
                       DATE_FORMAT(b.fec_llegpl,'%Y-%m-%d %H:%i'),
                       a.obs_despac,m.nom_lineax,d.nom_apell1,d.nom_apell2, b.cod_agenci, b.nom_conduc, a.con_telmov
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_genera_marcas h,
                       ".BASE_DATOS.".tab_vehicu_vehicu i,
                       ".BASE_DATOS.".tab_vehige_colore j,
                       ".BASE_DATOS.".tab_vehige_carroc k,
                       ".BASE_DATOS.".tab_vehige_lineas m,
                       ".BASE_DATOS.".tab_tercer_conduc c LEFT JOIN ".BASE_DATOS.".tab_genera_catlic e ON c.num_catlic = e.cod_catlic 
                 WHERE b.cod_conduc = c.cod_tercer AND
                       a.num_despac = b.num_despac AND
                       d.cod_tercer = c.cod_tercer AND
                       b.num_placax = i.num_placax AND
                       h.cod_marcax = i.cod_marcax AND
                       i.cod_colorx = j.cod_colorx AND
                       i.cod_carroc = k.cod_carroc AND
                       i.cod_lineax = m.cod_lineax AND
                       i.cod_marcax = m.cod_marcax AND
                       a.num_despac = '".$GLOBALS[despac]."'
              ";
      
      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();
      

      $query = "SELECT a.num_trayle
                  FROM ".BASE_DATOS.".tab_despac_vehige a
		 		 WHERE a.num_despac = ".$GLOBALS[despac]."
		";

      $consec = new Consulta($query, $this -> conexion);
      $trayler = $consec -> ret_matriz();

      $query = "SELECT a.val_multpc,a.obs_planru
      		      FROM ".BASE_DATOS.".tab_config_parame a
      		   ";

      $consec = new Consulta($query, $this -> conexion);
      $paramet = $consec -> ret_matriz();

      if($matriz[0][14] == NULL)
       $f1 = "../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg";//Foto del Conductor
      else
       $f1 = URL_CONDUC.$matriz[0][14];//Foto del Conductor

      if($matriz[0][15] == NULL)
        $f2 = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";//Foto del Vehiculo
      else
        $f2 = URL_VEHICU.$matriz[0][15];//Foto del Conductor

      $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
      $ciudad_o = $objciud -> getSeleccCiudad($matriz[0][1]);
      $ciudad_d = $objciud -> getSeleccCiudad($matriz[0][2]);

    $e1 = $obsplan[1]; //nombre empresa
    $e2 = "<b>NIT: ".$obsplan[2]."-".$obsplan[3]."</b>"; //nit empresa
    $e3 = "<b>Direcci&oacute;n: ".$obsplan[4]." / ".$obsplan[6]."</b>"; //direccion empresa
    
    if( $obsplan[2] != '830100365' )
      $e4 = "<b>Telefonos: ".$obsplan[7]." / ".$obsplan[8]."</b>"; //telefonos empresa
    else
    {
      $query = "SELECT tel_agenci 
      		      FROM ".BASE_DATOS.".tab_genera_agenci
                WHERE cod_agenci = '".$matriz[0]['cod_agenci']."'
      		   ";

      $consec = new Consulta($query, $this -> conexion);
      $telef = $consec -> ret_matriz();
      $telef = $telef[0][0];
      $e4 = "<b>Telefono: ".$telef."</b>"; //telefonos agencia
    }
	
	if( $obsplan[2] == "891857878" )
		$d1 = "logos/logo_boy.png";
    elseif( $obsplan[2] == "900116578" )
		$d1 = "logos/logo_ipertr.jpg";
	elseif($obsplan[2]=='830100365')
	    $d1 = "logos/logo_mega.gif";
    elseif($obsplan[2]=='890207572')
	    $d1 = "logos/logo_ceter.jpg";
	elseif($obsplan[2]=='800090323')
	    $d1 = "logos/logo_trasco.jpg";
	else		
		$d1 = "imagenes/logo.gif";
		
    $d2 = $f2; //foto vehiculo
    $d3 = $f1; //foto conductor
    $d4 = $matriz[0][0]; //numero de manifiesto
    $d5 = $ciudad_o[0][1]; //Origen
    $d6 = $ciudad_d[0][1]; //Destino
    $d7 = $matriz[0][3]; //Fecha Salida Planeada
    $d8 = $matriz[0][4]; //placa
    $d9 = $matriz[0][5]; //marca
    $d10 = $matriz[0][6]; //modelo
    $d11 = $matriz[0][7]; //color
    $d12 = $matriz[0][8]; //carroceria
    if( $matriz[0]['nom_conduc'] )
      $d13 = $matriz[0]['nom_conduc'];
    else
      $d13 = $matriz[0][9]." ".$matriz[0][20]." ".$matriz[0][21]; //conductor
    $d14 = $matriz[0][10]; //cedula
    $d15 = $matriz[0][11]; //licencia
    $d16 = $matriz[0][12]; //categoria
    if( $matriz[0]['con_telmov'] )
      $d17 = $matriz[0]['con_telmov'];
    else
      $d17 = $matriz[0][13]; //telefono
      
      
    if((int)$transpor[0][0]==900488420) {

        $txt = "<br><br><ul style='margin: 0; padding: 0; font-size: 8px; font-weight: bold;'><li>EN CASO DE VARADA, ACCIDENTE, O CUALQUIER EVENTUALIDAD EN LA VIA COMUNIQUESE INMEDIATAMENTE CON LA OFICINA.</li>
                    <li>DEBE REPORTARSE EN EL SITIO Y HORA DONDE PERNOCTARA</li>
                    <li>FAVOR REPORTARSE CUANDO ESTE EN EL SITIO DEL DESCARGUE</li>
                    <li>POR CADA REPORTE OMITIDO O INCUMPLIMIENTO A ALGUNA DE LAS ANTERIORES SE DESCONTARAN 50.000</li>
                    <li>DEBE CONTESTAR Y ATENDER LAS LLAMADAS QUE HAGAN POR PARTE DE FARO SOBRE SU UBICACIÓN Y REPORTAR CUALQUIER NOVEDAD QUE ALTERE EL PLAN DE RUTA</li>
                    <li>HORARIO AUTORIZADO DE CIRCULACION 5 AM A 10 PM </li>
                    <li>TELEFONO DE CONTACTO: 031.7429002, Cel. 3143949474 de FARO</li></ul>";

        $d18 = (!empty($paramet[0][1])?$paramet[0][1]."<br>":'').$matriz[0][18].$txt; //observaciones plan de ruta parametros y las observaciones digitadas en el plan de ruta
    }else{
        $d18 = $paramet[0][1]."<br>".$matriz[0][18]; //observaciones plan de ruta parametros y las observaciones digitadas en el plan de ruta
    }      
    
    
    $d19 = $GLOBALS[despac]; //numero de despacho
    $d20 = $matriz[0][16]; //numero de caravana
    $d21 = $matriz[0][17]; //Fecha de llegada planeada
    $d22 = number_format($paramet[0][0]); //valor de la multa
    $d23 = $trayler[0][0]; //Nro de Trayler
    $d24 = $matriz[0][19]; //linea

    $query = "SELECT cod_perfil
                FROM ".BASE_DATOS.".tab_autori_perfil
               WHERE cod_perfil = '".$this -> usuario -> cod_perfil."' AND
              		 cod_autori = '3'";

    $consec = new Consulta($query, $this -> conexion);
    $autfec = $consec -> ret_matriz();

    $query = "SELECT if(b.ind_virtua = '1',CONCAT(b.nom_contro,' (Virtual)'),b.nom_contro),b.dir_contro,
                 	 a.fec_planea,a.fec_alarma,b.cod_contro,if(b.ind_urbano = '1','Urbano','')
            	FROM ".BASE_DATOS.".tab_despac_seguim a,
                 	 ".BASE_DATOS.".tab_genera_contro b,
                 	 ".BASE_DATOS.".tab_despac_vehige e
           	   WHERE a.cod_contro = b.cod_contro AND
                 	 a.num_despac = ".$GLOBALS[despac]." AND
                 	 a.num_despac = e.num_despac";
           if(!$autfec)
            $query .= " AND b.ind_virtua = '0'";

    $query .= " ORDER BY 3";
    
    $consulta = new Consulta($query, $this -> conexion);
    $matriz1 = $consulta -> ret_matriz();


    $query = "SELECT b.cod_contro,c.nom_noveda,b.val_pernoc
              FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_pernoc b,
                   ".BASE_DATOS.".tab_genera_noveda c
              WHERE a.num_despac = b.num_despac AND
                    b.cod_noveda = c.cod_noveda AND
                    b.cod_noveda != '0' AND
                    a.num_despac = '$GLOBALS[despac]'
             ";

    $novedades = new Consulta($query, $this -> conexion);
    $novedad = $novedades -> ret_matriz();

    for($i=0; $i < sizeof($novedad); $i++)
    {
     for($k=0 ; $k < sizeof($matriz1); $k++)
     {
         if($novedad[$i][0] == $matriz1[$k][4])
         {
          $matriz1[$k][5] = "Novedad: ".$novedad[$i][1]." <br> Duracion: " .$novedad[$i][2]." Min";
         }
     }
    }


    $j = 0;
    //Jorge 12-06-2012
    if($transpor[0][0]=='900116578') 
      $ini=1;
    else
      $ini=0;

    for($i=$ini;$i < sizeof($matriz1); $i++)
    //------------------------------------
    {
		if($matriz1[$j][5])
		 $adicional = "<br>".$matriz1[$j][5];


         $d[$i] = $matriz1[$j][0]."<small><br>".$matriz1[$j][1]."<br>".$matriz1[$j][2]." <br>".$matriz1[$j][5]." </small>";
         $j++;
    }

             if($GLOBALS[posi] == 0 || !$GLOBALS[posi])
             { 
			     
                    if($transpor[0][0]=='900113804')
                      $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje/planGlobal.html";
                    elseif($transpor[0][0]=='830100365')
                      $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviajeMega.html";
                    elseif($transpor[0][0]=='900488420')
                      $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviajeTransmer.html";
                    elseif($transpor[0][0]=='900116578')
                    {
                      $d22= '40.000';//Jorge 12-06-2012
                      $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviajeIpertr.html";
                    }
                    else
                      $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje.html";
										
                    $thefile = implode("", file($tmpl_file));
                    $thefile = addslashes($thefile);
                    $thefile = "\$r_file=\"".$thefile."\";";
                    eval($thefile);
                    print $r_file;

                    echo "<form name=\"form\" method=\"post\" action=\"index.php\">\n";

                         echo "<br><br>"

                             ."<table border=\"0\" width=\"100%\">\n"

                                 ."<tr>\n"

                                      ."<td width=\"50%\" align=\"center\">\n"

                                              ."<input type=\"hidden\" name=\"despac\" value=\"$GLOBALS[despac]\">\n"

                                               ."<input type=\"hidden\" name=\"window\" value=\"central\">\n"

                                              ."<input type=\"hidden\" name=\"cod_servic\" value=\"$GLOBALS[cod_servic]\">\n"

                                              ."<input type=\"hidden\" name=\"opcion\" value=\"1\">\n"

                                              ."<input type=\"hidden\" name=\"posi\" value=\"0\">\n"

                                              ."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.Volver.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';form.Volver.style.visibility='visible';form.Siguiente.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">\n"

                                      ."</td>\n"

                                      ."<td width=\"50%\" align=\"center\">\n"

                                              ."<input type=\"button\" name=\"Volver\" value=\"Volver\" onClick=\"form.opcion.value = 0; form.submit();\">\n"

                                      ."</td>\n";

                                      if(sizeof($matriz1) > 15 && $transpor[0][0] != '900488420' )
                                      {
                                         echo "<td width=\"50%\" align=\"center\">\n"

                                                   ."<input type=\"button\" name=\"Siguiente\" value=\"Siguiente\" onClick=\"form.posi.value = 1;  form.submit();\">\n"

                                              ."</td>\n";
                                      }
                                      elseif( sizeof($matriz1) > 25 && $transpor[0][0] == '900488420')
                                      {
                                         echo "<td width=\"50%\" align=\"center\">\n"
                                                 ."<input type=\"button\" name=\"Siguiente\" value=\"Siguiente\" onClick=\"form.posi.value = 1;  form.submit();\">\n"
                                                 ."</td>\n";                                        
                                      }

                                 echo "</tr>\n"

                             ."</table>\n";

                         echo "</form><br><br>\n";
                         echo "</div>";
             }

             if(sizeof($matriz1) > 15 && $GLOBALS[posi] == 1 && $transpor[0][0] != '900488420' )
             {	
						 		if($transpor[0][0]=='900113804')
									  $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje/planGlobal_anexo.html";
								else	
									$tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje_2.html";
                  
                    $thefile = implode("", file($tmpl_file));
                    $thefile = addslashes($thefile);
                    $thefile = "\$r_file=\"".$thefile."\";";
                    eval($thefile);
                    print $r_file;

                    echo "<form name=\"form\" method=\"post\" action=\"index.php\">\n";

                         echo "<br><br>"

                             ."<table border=\"0\" width=\"100%\">\n"

                                 ."<tr>\n"

                                      ."<td width=\"50%\" align=\"center\">\n"

                                              ."<input type=\"hidden\" name=\"despac\" value=\"$GLOBALS[despac]\">\n"

                                               ."<input type=\"hidden\" name=\"window\" value=\"central\">\n"

                                              ."<input type=\"hidden\" name=\"cod_servic\" value=\"$GLOBALS[cod_servic]\">\n"

                                              ."<input type=\"hidden\" name=\"opcion\" value=\"1\">\n"

                                              ."<input type=\"hidden\" name=\"posi\" value=\"0\">\n"

                                              ."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.Siguiente.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';;form.Siguiente.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">\n"

                                      ."</td>\n"

                                      ."<td width=\"50%\" align=\"center\">\n"

                                              ."<input type=\"button\" name=\"Siguiente\" value=\"Anterior\" onClick=\"form.posi.value = 0; form.submit();\">\n"

                                      ."</td>\n"

                                 ."</tr>\n"

                             ."</table>\n";

                         echo "</form><br><br>\n";
                         echo "</div>";
             }
             
            if(sizeof($matriz1) > 25 && $GLOBALS[posi] == 1 && $transpor[0][0] == '900488420')
            {	
                $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje_2Transmer.html";

                $thefile = implode("", file($tmpl_file));
                $thefile = addslashes($thefile);
                $thefile = "\$r_file=\"".$thefile."\";";
                eval($thefile);
                print $r_file;

                echo "<form name=\"form\" method=\"post\" action=\"index.php\">\n";

                echo "<br><br>"
                        ."<table border=\"0\" width=\"100%\">\n"
                        ."<tr>\n"
                        ."<td width=\"50%\" align=\"center\">\n"
                        ."<input type=\"hidden\" name=\"despac\" value=\"$GLOBALS[despac]\">\n"
                        ."<input type=\"hidden\" name=\"window\" value=\"central\">\n"
                        ."<input type=\"hidden\" name=\"cod_servic\" value=\"$GLOBALS[cod_servic]\">\n"
                        ."<input type=\"hidden\" name=\"opcion\" value=\"1\">\n"
                        ."<input type=\"hidden\" name=\"posi\" value=\"0\">\n"
                        ."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.Siguiente.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';;form.Siguiente.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">\n"
                        ."</td>\n"
                        ."<td width=\"50%\" align=\"center\">\n"
                        ."<input type=\"button\" name=\"Siguiente\" value=\"Anterior\" onClick=\"form.posi.value = 0; form.submit();\">\n"
                        ."</td>\n"
                        ."</tr>\n"
                        ."</table>\n";

                echo "</form><br><br>\n";
                echo "</div>";
            }

   }// fin else

}//FIN FUNCION PRINCIPAL

// *********************************************************************************

}//FIN CLASE PROC_MANIFI

     $proceso = new Proc_planruimp($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);

?>

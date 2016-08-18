<?php

class Proc_por_ruta
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
      if(!isset($GLOBALS[opcion]))
        $this -> Por_rutas();
      else
         {
          switch($GLOBALS[opcion])
           {
            case "1":
              $this -> Listar();
              break;
            case "2":
              $this -> Datos();
           }//FIN SWITCH
         }// FIN ELSE GLOBALS OPCION
     }//FIN FUNCION PRINCIPAL

    function Por_rutas()
    {
     $datos_usuario = $this -> usuario -> retornar();

     $query = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,e.cod_rutasx
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_despac_vehige d,
                     ".BASE_DATOS.".tab_genera_rutasx e,
                     ".BASE_DATOS.".tab_vehicu_vehicu i
               WHERE a.num_despac = d.num_despac AND
                     d.cod_rutasx = e.cod_rutasx AND
                     i.num_placax = d.num_placax AND
                     a.fec_salida Is Not Null AND
                     a.fec_salida <= NOW() AND
                     a.fec_llegad IS NULL AND
                     a.ind_anulad = 'R' AND
                     a.ind_planru = 'S'
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
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
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
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
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
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
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
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

  $query = $query." GROUP BY 1 ORDER BY 1";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

  $formulario = new Formulario ("index.php","post","Despachos Por Ruta","form_lista");

  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Ruta(s)",1,"t2");
  $formulario -> nueva_tabla();

  $formulario -> linea("Ruta",0,"t");
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea("Destino",1,"t");

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

  for($i=0; $i < (sizeof($matriz)/1); $i++)
  {
   $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][1]);
   $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][2]);

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&ruta=".$matriz[$i][3]."&window=central&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario -> linea($matriz[$i][0],0,"i");
   $formulario -> linea($ciudad_o[0][1],0,"i");
   $formulario -> linea($ciudad_d[0][1],1,"i");
  }

  $formulario -> oculto("usuario","$datos_usuario[cod_usuari]",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic","$GLOBALS[cod_servic]",0);
  $formulario -> cerrar();

  }

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $validmatriz[0]["linea"] = " AND d.cod_rutasx = ".$GLOBALS[ruta];

   $GLOBALS_ADD[0]["campo"] = "ruta";
   $GLOBALS_ADD[0]["valor"] = $GLOBALS[ruta];   
   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> cod_aplica,$this -> conexion,1);
   $listado_prin -> ListadoPrincipal($datos_usuario,0,"Despachos Por Ruta",0,$validmatriz,$GLOBALS_ADD);
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> cod_aplica,$this -> conexion);
   $listado_prin  -> Encabezado($GLOBALS[despac],$formulario,$datos_usuario,0,"Despachos Por Ruta");
   $listado_prin  -> PlanDeRuta($GLOBALS[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$GLOBALS[despac],0);
   $formulario -> oculto("opcion",$GLOBALS[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();
   
  //Para la carga del Popup
    echo '<tr><td><div id="AplicationEndDIV"></div>
          <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
          <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
            <div id="filtros" ></div>
            <div id="result" ></div>
      </div><div id="alg"><table></table></div></td></tr>';
 }

}//fin clase
     $proceso = new Proc_por_ruta($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php

class Proc_por_pc
{
    //Atributos
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
      if(!isset($_REQUEST[opcion]))
        $this -> Listars();
      else
         {
          switch($_REQUEST[opcion])
           {
            case "1":
              $this -> Listar();
              break;
            case "2":
              $this -> Datos();
           }//FIN SWITCH
         }// FIN ELSE GLOBALS OPCION
     }//FIN FUNCION PRINCIPAL

    function Listars()
    {

     $datos_usuario = $this -> usuario -> retornar();

     $query = "SELECT if(e.ind_virtua = '1',CONCAT(e.nom_contro,' (Virtual)'),e.nom_contro),
		      e.nom_encarg,e.dir_contro,e.tel_contro,e.cod_contro
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_despac_vehige d,
                     ".BASE_DATOS.".tab_despac_seguim c,
                     ".BASE_DATOS.".tab_genera_contro e,
                     ".BASE_DATOS.".tab_vehicu_vehicu i
               WHERE a.num_despac = d.num_despac AND
                     d.num_despac = c.num_despac AND
                     c.cod_contro = e.cod_contro AND
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

    $formulario = new Formulario ("index.php","post","Despachos Por Puesto de Control","form_lista");
    $formulario -> linea("Puesto de Control",0,"t");
    $formulario -> linea("Encargado", 0,"t");
    $formulario -> linea("Direccion", 0,"t");
    $formulario -> linea("Telefono", 1,"t");

    for($i=0; $i < (sizeof($matriz)/1); $i++)
    {
     $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&contro=".$matriz[$i][4]."&window=central&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

     $formulario -> linea($matriz[$i][0],0,"i");
     $formulario -> linea($matriz[$i][1],0,"i");
     $formulario -> linea($matriz[$i][2],0,"i");
     $formulario -> linea($matriz[$i][3],1,"i");
    }//fin for

    $formulario -> oculto("usuario","$datos_usuario[cod_usuari]",0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic","$_REQUEST[cod_servic]",0);
    $formulario -> cerrar();
  }//fin function

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $validmatriz[0]["linea"] = " AND b.cod_contro = ".$_REQUEST[contro];

   $GLOBALS_ADD[0]["campo"] = "contro";
   $GLOBALS_ADD[0]["valor"] = $_REQUEST[contro];

   $listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> cod_aplica,$this -> conexion,1);
   $listado_prin -> ListadoPrincipal($datos_usuario,0,"Despachos Por P/C",0,$validmatriz,$GLOBALS_ADD);
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> cod_aplica,$this -> conexion);
   $listado_prin  -> Encabezado($_REQUEST[despac],$formulario,$datos_usuario,0,"Despachos Por P/C");
   $listado_prin  -> PlanDeRuta($_REQUEST[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$_REQUEST[despac],0);
   $formulario -> oculto("opcion",$_REQUEST[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

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
     $proceso = new Proc_por_pc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php

class Proc_contro
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
    $this -> Resultado();
  else
     {
      switch($GLOBALS[opcion])
       {
          case "1":
          $this -> Resultado();
          break;

	  case "2":
          $this -> Actualizar();
	  $this -> Resultado();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $datos_usuario["cod_usuari"];

   $query = "SELECT a.cod_transp,b.nom_tercer
   		       FROM ".BASE_DATOS.".tab_interf_gps a,
   		       		".BASE_DATOS.".tab_tercer_tercer b
   		      WHERE a.cod_transp = b.cod_tercer AND
   		            a.ind_estado = '1'
   		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	//PARA EL FILTRO DE TRANSPORTADORA
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
	//PARA EL FILTRO DE TRANSPORTADORA
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   $consulta = new Consulta($query, $this -> conexion);
   $trasnpact = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","ZONAS OPERADORES GPS","form_item");

   for($k = 0; $k < sizeof($trasnpact); $k++)
   {
   	$formulario -> nueva_tabla();
	$formulario -> linea("Operadores GPS Activos con la Transportadora :: ".$trasnpact[$k][1].".",1,"t2");

    /*$interf_gps = new Interfaz_GPS();
    $interf_gps -> Interfaz_GPS_envio($trasnpact[$k][0],BASE_DATOS,$usuario,$this -> conexion);

    if($interf_gps -> cant_interf > 0)
    {
     for($j = 0; $j < $interf_gps -> cant_interf; $j++)
     {
	  $query = "SELECT CONVERT(a.cod_zonaxx,SIGNED),a.nom_zonaxx,
			           if(a.val_longit IS NULL,'Sin Definir',a.val_longit),
			 		   if(a.val_latitu IS NULL,'Sin Definir',a.val_latitu),
			 		   if(a.ind_estado = '1','Habilitado','Inhabilitado')
		    	  FROM ".BASE_DATOS.".tab_zonaxx_gps a
		   		 WHERE a.cod_operad = '".$interf_gps -> cod_operad[$j][0]."' AND
		   		 	   a.cod_transp = '".$trasnpact[$k][0]."'
			 		   ORDER BY 1
		  ";

  	  $consulta = new Consulta($query, $this -> conexion);
  	  $zonasgps = $consulta -> ret_matriz();

   	  $formulario -> nueva_tabla();
	  $formulario -> linea("Lista de Zonas Operador GPS :: ".$interf_gps -> nom_operad[$j][0].".",1,"h");

	  $formulario -> nueva_tabla();
   	  $formulario -> linea("CODIGO",0,"t");
   	  $formulario -> linea("NOMBRE",0,"t");
   	  $formulario -> linea("LONGITUD",0,"t");
   	  $formulario -> linea("LATITUD",0,"t");
	  $formulario -> linea("ESTADO",1,"t");

	  if(sizeof($zonasgps) > 0)
	  {
   	   for($i=0;$i<sizeof($zonasgps);$i++)
   	   {
   	    $formulario -> linea($zonasgps[$i][0],0,"i");
   	    $formulario -> linea($zonasgps[$i][1],0,"i");
   	    $formulario -> linea($zonasgps[$i][2],0,"i");
   	    $formulario -> linea($zonasgps[$i][3],0,"i");
   	    $formulario -> linea($zonasgps[$i][4],1,"i");
   	   }
	  }
	  else
	  {
	   $formulario -> nueva_tabla();
	   $formulario -> linea("Actualmente no se Encuentran Zonas Registradas.",1,"e");
	  }
     }

     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("opcion",2,0);
     $formulario -> oculto("valor",$valor,0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

     echo "<hr>";
     $formulario -> boton("Actualizar Zonas","submit",0);
     $formulario -> oculto("transporact[$k]",$trasnpact[$k][0],0);
    }
    else
    {
	 $formulario -> nueva_tabla();
	 $formulario -> linea("Para Ver la Informaci&oacute;n de las Zonas se Debe Activar por lo Menos un Operador GPS.",1,"e");
    }*/
   }

   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR


 function Actualizar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $transporact = $GLOBALS[transporact];

   /*$interf_gps = new Interfaz_GPS();

   for($j = 0; $j < sizeof($transporact); $j++)
   {
    $interf_gps -> Interfaz_GPS_envio($transporact[$j],BASE_DATOS,$usuario,$this -> conexion);

    for($i = 0; $i < $interf_gps -> cant_interf; $i++)
    {
	 $interf_gps -> SeleccionInterfaz($interf_gps -> cod_operad[$i][0]);
	 $interf_gps -> setZonasOperad($interf_gps -> tip_interf[$i][0],$interf_gps -> cod_operad[$i][0],$transporact[$j]);
    }
   }*/
 }

}//FIN CLASE PROC_CONTRO
     $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php

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
//********METODOS
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

       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/menava.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","OPERADORES","form_item");

   $formulario -> nueva_tabla();
   $formulario -> linea("Listado de Operadores Para el Envio de Mensajes de Texto",1,"t2");

   $formulario -> nueva_tabla();

   $query = "SELECT a.cod_transp,b.abr_tercer
	       	   FROM ".BD_STANDA.".tab_mensaj_bdsata a,
	       	   		".BASE_DATOS.".tab_tercer_tercer b
	          WHERE a.nom_bdsata = '".BASE_DATOS."' AND
		    	    a.cod_transp = b.cod_tercer AND
		    	    a.ind_estado = '1'
	    ";

   if($datos_usuario["cod_perfil"] == "")
   {
   	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
   	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   $query .= " ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $activado = $consulta -> ret_matriz();

   if($activado)
   {
   	for($j = 0; $j < sizeof($activado); $j++)
   	{
   	 $formulario -> nueva_tabla();
   	 $formulario -> linea("Operadores Activos con la Transportadora :: ".$activado[$j][1]."",1,"t2");

	 $query = "SELECT a.cod_operad,a.nom_operad,a.dns_operad,
					  if(a.ind_emailx = '1','E-mail','SMS')
				 FROM ".BD_STANDA.".tab_mensaj_operad a
			    WHERE a.cod_transp IS NULL AND
			   		  a.nom_bdsata IS NULL AND
					  a.ind_estado = '1'
					  ORDER BY 2
			 ";

     $consulta = new Consulta($query, $this -> conexion);
     $pordefec = $consulta -> ret_matriz();

	 $formulario -> nueva_tabla();
	 $formulario -> linea("Operadores de Mensajeria de Texto por Defecto",1,"h");

	 $formulario -> nueva_tabla();
	 $formulario -> linea("Operador",0,"t");
	 $formulario -> linea("DNS",0,"t");
	 $formulario -> linea("Tipo de Mensaje",0,"t");
	 $formulario -> linea("Estado",1,"t");

	 for($i = 0; $i < sizeof($pordefec); $i++)
	 {
	  $formulario -> linea($pordefec[$i][1],0,"i");
	  $formulario -> linea($pordefec[$i][2],0,"i");
	  $formulario -> linea($pordefec[$i][3],0,"i");
	  $formulario -> linea("Activo",1,"i");
	 }

	 $query = "SELECT a.cod_operad,a.nom_operad,a.dns_operad,
					  if(a.ind_emailx = '1','E-mail','SMS'),
					  if(a.ind_estado = '1','Activo','Inactivo')
				 FROM ".BD_STANDA.".tab_mensaj_operad a
			    WHERE a.cod_transp = '".$activado[$j][0]."' AND
					  a.nom_bdsata = '".BASE_DATOS."'
					  ORDER BY 2
			 ";

     $consulta = new Consulta($query, $this -> conexion);
     $portrans = $consulta -> ret_matriz();

	 $formulario -> nueva_tabla();
	 $formulario -> linea("Operadores de Mensajeria de Texto Definidos por la Transportadora",1,"h");

	 $formulario -> nueva_tabla();
	 $formulario -> linea("Operador",0,"t");
	 $formulario -> linea("DNS",0,"t");
	 $formulario -> linea("Tipo de Mensaje",0,"t");
	 $formulario -> linea("Estado",1,"t");

	 for($i = 0; $i < sizeof($portrans); $i++)
	 {
	  $formulario -> linea($portrans[$i][1],0,"i");
	  $formulario -> linea($portrans[$i][2],0,"i");
	  $formulario -> linea($portrans[$i][3],0,"i");
	  $formulario -> linea($portrans[$i][4],1,"i");
	 }
    }
   }
   else
   {
   	$formulario -> nueva_tabla();
   	$formulario -> linea("Para Realizar Operaciones en Esta Opción Debe Inicialmente Activar el Envio de Mensajes en la Opción :: Configuracion > Mensajes de Texto > Configuracion",1,"e");
   }

   $formulario -> cerrar();
 }

}//FIN CLASE PROC_CONTRO
     $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
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

 function principal()
 {
  if(!isset($_REQUEST[opcion]))
    $this -> Formulario();
  else
     {
      switch($_REQUEST[opcion])
       {
          case "1":
          $this -> Formulario();
          break;
          case "2":
          $this -> Insertar();
          break;

       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $indfilt = 0;
   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/menava.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","INSERTAR OPERADOR","form_item");

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
     $indfilt = 1;
    }
   }
   else
   {
   	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
     $indfilt = 1;
    }
   }

   $query .= " ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $activado = $consulta -> ret_matriz();

   if($activado)
   {
   	if(!$indfilt)
   	 $activado = array_merge($inicio,$activado);

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n Basica del Operador",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp",$activado,1);
    $formulario -> texto("Nombre","text","nombre",1,30,30,"","");
    $formulario -> texto("DNS @","text","dns",1,30,60,"","");
    $formulario -> radio("E-mail","indmai",1,0,1);
    $formulario -> radio("SMS","indmai",0,1,1);

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> boton("Insertar","button\" onClick=\"operador(form_item)",1);
    $formulario -> cerrar();
   }
   else
   {
   	$formulario -> nueva_tabla();
   	$formulario -> linea("Para Realizar Operaciones en Esta Opci&oacute;n Debe Inicialmente Activar el Envio de Mensajes en la Opci&oacute;n :: Configuracion > Mensajes de Texto > Configuracion",1,"e");
   }
 }

 function Insertar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT MAX(cod_operad)
  			  FROM ".BD_STANDA.".tab_mensaj_operad
  			";

  $consulta = new Consulta($query, $this -> conexion);
  $consecut = $consulta -> ret_matriz();

  $consecut[0][0] += 1;

  $query = "INSERT INTO ".BD_STANDA.".tab_mensaj_operad
  						(cod_operad,cod_transp,nom_bdsata,nom_operad,
  						 dns_operad,ind_emailx,usr_creaci,fec_creaci)
  				 VALUES (".$consecut[0][0].",'".$_REQUEST[transp]."','".BASE_DATOS."',
  				 		 '".$_REQUEST[nombre]."','".$_REQUEST[dns]."','".$_REQUEST[indmai]."',
  				 		 '".$usuario."',NOW())
  		";

  $consulta = new Consulta($query, $this -> conexion,"BR");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $mensaje =  "Se Inserto la Informaci&oacute;n del Operador Exitosamente.";
   $mens = new mensajes();
   $mens -> correcto("INSERTAR OPERADOR",$mensaje);
  }

 }

}//FIN CLASE PROC_CONTRO
     $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
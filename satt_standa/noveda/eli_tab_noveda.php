<?php
/****************************************************************************
NOMBRE:   MODULO_NOVEDA_ACT.PHP
FUNCION:  ELIMINAR NOVEDADES
****************************************************************************/
class Proc_noveda
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
  if(!isset($GLOBALS["opcion"]))
    $this -> Buscar();
  else
     {
      switch($GLOBALS["opcion"])
       {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Datos();
          break;
        case "3":
          $this -> Eliminar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/noveda.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","BUSCAR Y LISTAR NOVEDADES","form_list");
   $formulario -> linea("Defina la Condici&oacute;n de Busqueda",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> texto ("Novedad","text","noveda",1,50,255,"","");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> boton("Buscar","button\" onClick=\"form_list.submit()",0);
   $formulario -> boton("Todas","button\" onClick=\"form_list.submit() ",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_noveda,UPPER(a.nom_noveda),IF(a.ind_alarma = 'S', 'SI', 'NO'), IF(a.ind_tiempo = '1', 'SI', 'NO'),
                   IF(a.nov_especi = '1', 'SI', 'NO'), IF(a.ind_manala = '1', 'SI', 'NO'), IF(a.ind_fuepla = '1', 'SI', 'NO'), 
                   IF(a.ind_notsup = '1', 'SI', 'NO'), IF(b.nom_operad IS NULL, '---',b.nom_operad), IF(a.cod_homolo IS NULL , '---', a.cod_homolo), IF(a.ind_visibl = '1', 'SI', 'NO'),
                   IF(a.ind_insveh = '1', 'SI', 'NO'), IF(a.ind_ealxxx = '1', 'SI', 'NO')
            FROM ".BASE_DATOS.".tab_genera_noveda a LEFT JOIN ".CENTRAL.".tab_genera_opegps b ON a.cod_operad = b.cod_operad
           	WHERE nom_noveda LIKE '%$GLOBALS[noveda]%' AND
		 			   			cod_noveda != ".CONS_NOVEDA_PCLLEG." AND
					 				cod_noveda != ".CONS_NOVEDA_ACAEMP." AND
									cod_noveda != ".CONS_NOVEDA_ACAFAR." AND
									cod_noveda != ".CONS_NOVEDA_CAMRUT." AND
               	  cod_noveda != ".CONS_NOVEDA_CAMALA."
        		   ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Listado de Novedades","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Novedade(s)",0,"t2");
   $formulario -> nueva_tabla();

   $formulario -> linea("Codigo",0,"t");
   $formulario -> linea("Descripcion",0,"t");
   $formulario -> linea("Genera Alerta",0,"t");
   $formulario -> linea("Solicita Tiempos",0,"t");
   $formulario -> linea("Novedad Especial",0,"t");
   $formulario -> linea("Mantiene Alarma",0,"t");
   $formulario -> linea("Fuera de Plataforma",0,"t");
   $formulario -> linea("Notifica Supervisor",0,"t");
   $formulario -> linea("Inspección Vehicular",0,"t");
   $formulario -> linea("Visible Esferas",0,"t");
   $formulario -> linea("Operador Novedad",0,"t");
   $formulario -> linea("Código homologación",0,"t");
   $formulario -> linea("Visibilidad",1,"t");

   for($i=0;$i<sizeof($matriz);$i++)
   {
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&noveda=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	$formulario -> linea($matriz[$i][2],0,"i");
   	$formulario -> linea($matriz[$i][3],0,"i");
    $formulario -> linea($matriz[$i][4],0,"i");
    $formulario -> linea($matriz[$i][5],0,"i");
    $formulario -> linea($matriz[$i][6],0,"i");
    $formulario -> linea($matriz[$i][7],0,"i");
    $formulario -> linea($matriz[$i][11],0,"i");
    $formulario -> linea($matriz[$i][12],0,"i");
    $formulario -> linea($matriz[$i][8],0,"i");
    $formulario -> linea($matriz[$i][9],0,"i");
    $formulario -> linea($matriz[$i][10],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   $query = "SELECT cod_noveda,nom_noveda,IF(ind_alarma = 'S', 'SI', 'NO'), IF(ind_tiempo = '1', 'SI', 'NO'),
                    IF(nov_especi = '1', 'SI', 'NO'), IF(ind_manala = '1', 'SI', 'NO'), IF(ind_fuepla = '1', 'SI', 'NO'), 
                    if(ind_notsup = '1', 'SI', 'NO'), IF(b.nom_operad IS NULL, '---',b.nom_operad), IF(a.cod_homolo IS NULL , '---', a.cod_homolo), IF(a.ind_visibl = '1', 'SI', 'NO'),
                    IF(a.ind_insveh = '1', 'SI', 'NO'), IF(a.ind_ealxxx = '1', 'SI', 'NO')
            FROM ".BASE_DATOS.".tab_genera_noveda a LEFT JOIN ".CENTRAL.".tab_genera_opegps b ON a.cod_operad = b.cod_operad
            WHERE cod_noveda = '$GLOBALS[noveda]'";
  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

  $query = "SELECT a.cod_noveda
  		      FROM ".BASE_DATOS.".tab_despac_noveda a
  		     WHERE a.cod_noveda = ".$GLOBALS["noveda"]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existnov = $consulta -> ret_matriz();

  $query = "SELECT a.cod_noveda
  		      FROM ".BASE_DATOS.".tab_despac_contro a
  		     WHERE a.cod_noveda = ".$GLOBALS["noveda"]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existcon = $consulta -> ret_matriz();

  $query = "SELECT a.cod_noveda
  		      FROM ".BASE_DATOS.".tab_despac_pernoc a
  		     WHERE a.cod_noveda = ".$GLOBALS["noveda"]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existper = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Eliminar Novedades","form_item");
   $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Novedad",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea($matriz[0][0],1,"i");
   $formulario -> linea("Descripci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Genera Alerta",0,"t");
   $formulario -> linea($matriz[0][2],1,"i");
   $formulario -> linea("Genera Tiempo",0,"t");
   $formulario -> linea($matriz[0][3],1,"i");
   $formulario -> linea("Novedad Especial",0,"t");
   $formulario -> linea($matriz[0][4],1,"i");
   $formulario -> linea("Mantiene Alarma",0,"t");
   $formulario -> linea($matriz[0][5],1,"i");
   $formulario -> linea("Fuera de Plataforma",0,"t");
   $formulario -> linea($matriz[0][6],1,"i");
   $formulario -> linea("Notifica Supervisor",0,"t");
   $formulario -> linea($matriz[0][7],1,"i");
   
   $formulario -> linea("Inspección Vehicular",0,"t");
   $formulario -> linea($matriz[0][11],0,"i");
   $formulario -> linea("Visible Esferas",0,"t");
   $formulario -> linea($matriz[0][12],1,"i");
   
   $formulario -> linea("Operador Novedad",0,"t");
   $formulario -> linea($matriz[0][8],1,"i"); 
   $formulario -> linea("Código homologación",0,"t");
   $formulario -> linea($matriz[0][9],1,"i"); 
   $formulario -> linea("Visibilidad",0,"t");
   $formulario -> linea($matriz[0][10],1,"i");

   $formulario -> nueva_tabla();
/*
   //Manejo de la Interfaz Aplicaciones SAT
   $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

   $formulario -> nueva_tabla();

   if($interfaz -> totalact > 0)
    $formulario -> linea("El Sistema Tiene Interfases Activas Debe Homologar este P/C",1,"t2");

   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    $homolocon = $interfaz -> getHomoloTranspNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS["noveda"]);

    if($homolocon["NovedadHomolo"] > 0)
    {
     $noveda_ws = $interfaz -> getListadNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"]);

     for($j = 0; $j < sizeof($noveda_ws); $j++)
     {
      if($homolocon["NovedadHomolo"] == $noveda_ws[$j]["noveda"])
       $nomnovhom[$i] = $noveda_ws[$j]["nombre"];
     }

     $formulario -> linea("La Novedad se Encuentra Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"]." :: ".$nomnovhom[$i].".",1,"i");
    }
    else
     $formulario -> linea("La Novedad no se Encuentra Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".",1,"i");
   }
*/
   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("noveda",$GLOBALS["noveda"],0);
   $formulario -> oculto("nombre",$matriz[0][1],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   if(!$existnov && !$existcon && !$existper)
    $formulario -> boton("Eliminar","submit\" onClick=\"return confirm('Esta Seguro de Eliminar Esta Novedad.?')",0);
   else
    $formulario -> linea("Imposible Eliminar Esta Novedad, Se Encuentra Asignada a Uno &oacute; Mas Reportes.",1,"e");
   $formulario -> boton("Cancelar","button\" onClick=\"javascript:history.go(-2)",0);
   $formulario -> cerrar();
 }
	
	function Eliminar()
	{
		$fec_actual = date("Y-m-d H:i:s");
	
		$insercion = new Consulta( "START TRANSACTION", $this -> conexion );
		
		$query = "DELETE FROM " . BASE_DATOS . ".tab_perfil_noveda
				  WHERE cod_noveda = '$GLOBALS[noveda]' ";

        $consulta = new Consulta( $query, $this->conexion, "R" );
		
  $query = "DELETE FROM ".BASE_DATOS.".tab_genera_noveda
             WHERE cod_noveda = '$GLOBALS[noveda]'";
  $insercion = new Consulta($query, $this -> conexion,"R");
/*
  //Manejo de la Interfaz Aplicaciones SAT
  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

  for($i = 0; $i < $interfaz -> totalact; $i++)
  {
   $homolocon = $interfaz -> getHomoloTranspNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS["noveda"]);

   if($homolocon["NovedadHomolo"] > 0)
   {
    $resultado_sat = $interfaz -> eliHomoloNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS["noveda"],$homolocon["NovedadHomolo"]);

    if($resultado_sat["Confirmacion"] == "OK")
     $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Elimino la Homologacion de la Novedad en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
    else
     $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Eliminar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
   }
  }
*/
  if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS["cod_servic"]." \"target=\"centralFrame\">Eliminar Otra Novedad</a></b>";

     $mensaje =  "La Novedad Se Elimino con Exito".$mensaje_sat.$link_a;
     $mens = new mensajes();
     $mens -> correcto("ELIMINAR NOVEDADES",$mensaje);
    }


 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************
}//FIN CLASE PROC_NOVEDA
   $proceso = new Proc_noveda($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
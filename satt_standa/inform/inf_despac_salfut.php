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
        default:
          $this -> Listar();
          break;
      }
  }
 }

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
   $listado_prin -> ListadoPrincipal($datos_usuario,1,"Salidas Programadas",0,NULL,NULL,0,1);
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $mRuta = array("link"=>0, "finali"=>0, "opcurban"=>0, "lleg"=>NULL, "tie_ultnov"=>NULL);#Fabian

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> cod_aplica,$this -> conexion);
   $listado_prin  -> Encabezado($GLOBALS[despac],$datos_usuario,0,$mRuta);
   #$listado_prin  -> PlanDeRuta($GLOBALS[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$GLOBALS[despac],0);
   $formulario -> oculto("opcion",$GLOBALS[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();
   
  //Para la carga del Popup
  echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';
 }

}

$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>

<?php

class Proc_usuari
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $datos_usuario = $this -> usuario -> retornar();
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
   }
  }
 }

 function Listar()
 {
      $query = "SELECT cod_usuari,idx_filtro,cod_filtro
                  FROM ".BASE_DATOS.".tab_usuari_wapxxx
                       ORDER BY 1";

      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();

     for($i=0;$i<sizeof($matriz);$i++)
     {
        if($matriz[$i][1] == 0)
        {
           $matriz[$i][1] = 'SIN FILTRO';
           $matriz[$i][2] = '-';
        }//fin if == 0
        else if($matriz[$i][1] == 1)
        {
                $query = "SELECT abr_tercer
                      FROM ".BASE_DATOS.".tab_tercer_tercer
                     WHERE cod_tercer = '".$matriz[$i][2]."'";
          $consulta = new Consulta($query, $this -> conexion);
          $matriz0 = $consulta -> ret_matriz();

          $matriz[$i][1] = 'CLIENTE';
          $matriz[$i][2] = $matriz0[0][0];
        }//fin if == 1
        else if($matriz[$i][1] == 2)
        {
                $query = "SELECT nom_contro
                      FROM ".BASE_DATOS.".tab_genera_contro
                     WHERE cod_contro = '".$matriz[$i][2]."'";
          $consulta = new Consulta($query, $this -> conexion);
          $matriz0 = $consulta -> ret_matriz();

          $matriz[$i][1] = 'PUESTO DE CONTROL';
          $matriz[$i][2] = $matriz0[0][0];
        }//fin if == 2
        else if($matriz[$i][1] == 3)
        {
                $query = "SELECT nom_tercer
                      FROM ".BASE_DATOS.".tab_tercer_tercer
                     WHERE cod_tercer = '".$matriz[$i][2]."'";
          $consulta = new Consulta($query, $this -> conexion);
          $matriz0 = $consulta -> ret_matriz();

          $matriz[$i][1] = 'CONDUCTOR';
          $matriz[$i][2] = $matriz0[0][0];
        }//fin if == 1
     }
   $formulario = new Formulario ("index.php","post","LISTADO DE USUARIOS","form_item");

   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Usuario(s)",1,"t2");
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)
   {
    $formulario -> linea("Usuario",0,"t");
    $formulario -> linea("Tipo de Filtro",0,"t");
    $formulario -> linea("Filtro",1,"t");

    for($i=0; $i< sizeof($matriz); $i++)
    {
     $formulario -> linea($matriz[$i][0],0,"i");
     $formulario -> linea($matriz[$i][1],0,"i");
     $formulario -> linea($matriz[$i][2],1,"i");
    }
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA

}//FIN CLASE PROC_USUARI
     $proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
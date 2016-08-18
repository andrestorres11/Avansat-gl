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
        case "2":
          $this -> Eliminar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Listar()
 {
            $query = "SELECT cod_usuari,idx_filtro,cod_filtro
                  FROM ".BASE_DATOS.".tab_usuari_wapxxx
              ORDER BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();

     for($i=0;$i<sizeof($matriz);$i++)
     {
        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&usuari=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
        if($matriz[$i][1] == 0)
        {
           $matriz[$i][1] = 'SIN FILTRO';
           $matriz[$i][2] = '-';
        }//fin if == 0
        else if($matriz[$i][1] == 1)
        {
                $query = "SELECT nom_tercer
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
   $formulario = new Formulario ("index.php","post","ELIMINAR USUARIOS","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Usuario(s)",0,"t2");
   $formulario -> nueva_tabla();
   if(sizeof($matriz) > 0)
   {
    $formulario -> linea("Usuario",0,"t");
    $formulario -> linea("Tipo Filtro",0,"t");
    $formulario -> linea("Filtro",1,"t");

    for($i=0;$i<sizeof($matriz);$i++)
    {
     $formulario -> linea($matriz[$i][0],0,"i");
     $formulario -> linea($matriz[$i][1],0,"i");
     $formulario -> linea($matriz[$i][2],1,"i");
    }
   }//fin if
   $formulario -> nueva_tabla();
   $formulario -> botoni("Volver","history.go(-1)",0);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA

 function Datos()
 {
            //trae los datos basicos del usuario
      $query = "SELECT cod_usuari,cod_clavex,idx_filtro,cod_filtro
                  FROM ".BASE_DATOS.".tab_usuari_wapxxx
                 WHERE cod_usuari = '$GLOBALS[usuari]'
              ORDER BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();
      if($matriz[0][2] == 1)
      {
        //trae las transportadoras
              $query = "SELECT a.cod_tercer, a.nom_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer AND
                         b.cod_activi = ".COD_FILTRO_CLIENT." AND
                         a.cod_tercer = '".$matriz[0][3]."'
                ORDER BY 2";
        $consulta = new Consulta($query, $this -> conexion);
        $filtro = $consulta -> ret_matriz();
        $matriz[0][1] = 'CLIENTE';
        $matriz[0][2] = $filtro[0][1];
      }//fin if ==1
      else if($matriz[0][2] == 2)
      {
        //trae los puestos de control
              $query = "SELECT cod_contro,nom_contro
                    FROM ".BASE_DATOS.".tab_genera_contro
                   WHERE cod_contro = '".$matriz[0][3]."'
                ORDER BY 2";
        $consulta = new Consulta($query, $this -> conexion);
        $filtro = $consulta -> ret_matriz();
        $matriz[0][1] = 'PUESTO DE CONTROL';
        $matriz[0][2] = $filtro[0][1];
      } //fin == 2
      else if($matriz[0][2] == 3)
      {
        //trae los conductores
              $query = "SELECT a.cod_tercer, a.nom_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer AND
                         b.cod_activi = ".COD_FILTRO_CONDUC." AND
                         a.cod_tercer = '".$matriz[0][3]."'
                ORDER BY 2";
        $consulta = new Consulta($query, $this -> conexion);
        $filtro = $consulta -> ret_matriz();
        $matriz[0][1] = 'CONDUCTOR';
        $matriz[0][2] = $filtro[0][1];
      } //fin == 3
      else
      {
        $matriz[0][1] = 'SIN FILTRO';
        $matriz[0][2] = ' - ';
      }//fin else
      echo "<script language=\"JavaScript\" src=\"../sadc_standa/js/usrwap.js\"></script>\n";
      $formulario = new Formulario("index.php","post","ELIMINAR USUARIO WAP", "form_eli");

      $formulario -> nueva_tabla();
      $formulario -> linea("Usario",0,"t");
      $formulario -> linea($matriz[0][0],1,"i");
      $formulario -> linea("Tipo de Filtro",0,"t");
      $formulario -> linea($matriz[0][1],1,"i");
      $formulario -> linea("Codigo de Filtro",0,"t");
      $formulario -> linea($matriz[0][2],1,"i");

      $formulario -> nueva_tabla();
      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("usuari", $GLOBALS[usuari], 0);
      $formulario -> oculto("opcion",2, 0);
      $formulario -> oculto("cod_servic", $GLOBALS[cod_servic], 0);
      $formulario -> oculto("window","central", 0);
      $formulario -> botoni("Eliminar","eli_usuario()",0);
      $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA

 function Eliminar()
 {
   //query de insercion
   $query = "DELETE FROM ".BASE_DATOS.".tab_usuari_wapxxx
              WHERE cod_usuari = '$GLOBALS[usuari]'";

   $consulta = new Consulta($query, $this -> conexion,"BR");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Eliminar Otro Usuario</a></b>";

     $mensaje =  "El Usuario Se Elimino con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ELIMINAR USUARIOS WAP",$mensaje);
  }
 }//FIN FUNCTION

}//FIN CLASE PROC_USUARI
     $proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
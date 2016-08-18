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

          $this -> Actualizar();

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
     $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&opcion=1&usuari=".$matriz[$i][0]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
 }

 function Datos()

 {

            //trae los datos basicos del usuario

      $query = "SELECT cod_usuari,cod_clavex,idx_filtro,cod_filtro

                  FROM ".BASE_DATOS.".tab_usuari_wapxxx

                 WHERE cod_usuari = '$GLOBALS[usuari]'

              ORDER BY 1";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz = $consulta -> ret_matriz();



      $inicio[0][0] = 0;
      $inicio[0][1] = '-';

      if($matriz[0][2] == 1)
      {
        //trae las transportadoras
              $query = "SELECT a.cod_tercer, a.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer AND
                         b.cod_activi = ".COD_FILTRO_CLIENT." AND
                         a.cod_tercer = '".$matriz[0][3]."'
                ORDER BY 2";
        $consulta = new Consulta($query, $this -> conexion);
        $cliente_a = $consulta -> ret_matriz();

      	$contro_a = $inicio;
      	$conduc_a = $inicio;
      }//fin if ==1

      else if($matriz[0][2] == 2)
      {
      	$cliente_a = $inicio;
      	$conduc_a = $inicio;

        //trae los puestos de control

              $query = "SELECT cod_contro,nom_contro
                    FROM ".BASE_DATOS.".tab_genera_contro
                   WHERE cod_contro = '".$matriz[0][3]."'
                ORDER BY 2";
        $consulta = new Consulta($query, $this -> conexion);
        $contro_a = $consulta -> ret_matriz();
      } //fin == 2

      else if($matriz[0][2] == 3)
      {

      	$cliente_a = $inicio;
      	$contro_a = $inicio;

        //trae los conductores

              $query = "SELECT a.cod_tercer, a.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer AND
                         b.cod_activi = ".COD_FILTRO_CONDUC." AND
                         a.cod_tercer = '".$matriz[0][3]."'
                ORDER BY 2";

        $consulta = new Consulta($query, $this -> conexion);
        $conduc_a = $consulta -> ret_matriz();
      } //fin == 3
      else
      {
		$cliente_a = $inicio;
      	$contro_a = $inicio;
      	$conduc_a = $inicio;
	  }

     //trae los clientes

            $query = "SELECT a.cod_tercer, a.abr_tercer
                  FROM ".BASE_DATOS.".tab_tercer_tercer a,
                       ".BASE_DATOS.".tab_tercer_activi b
                 WHERE a.cod_tercer = b.cod_tercer AND
                       b.cod_activi = ".COD_FILTRO_CLIENT."
              ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion);
      $clientes = $consulta -> ret_matriz();
      $clientes = array_merge($cliente_a,$clientes);

      //trae los conductores

            $query = "SELECT a.cod_tercer, a.abr_tercer
                  FROM ".BASE_DATOS.".tab_tercer_tercer a,
                       ".BASE_DATOS.".tab_tercer_activi b
                 WHERE a.cod_tercer = b.cod_tercer AND
                       b.cod_activi = ".COD_FILTRO_CONDUC."
              ORDER BY 2";
      $consulta = new Consulta($query, $this -> conexion);
      $conducs = $consulta -> ret_matriz();
      $conducs = array_merge($conduc_a,$conducs);

      //trae los puestos de control

            $query = "SELECT cod_contro,nom_contro
                  FROM ".BASE_DATOS.".tab_genera_contro
              ORDER BY 2";
      $consulta = new Consulta($query, $this -> conexion);
      $contros = $consulta -> ret_matriz();
      $contros = array_merge($contro_a,$contros);

      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/usrwap.js\"></script>\n";

      $formulario = new Formulario("index.php","post","", "form_act");

      $formulario -> texto("Usuario:","text","us\" disabled ", 0, 15,  20,"","".$matriz[0][0]."");

      $formulario -> oculto("usuari",$matriz[0][0],0);

      $formulario -> texto("Nueva Clave:","password","clave1", 0, 15,  20,"","$GLOBALS[clave1]");

      $formulario -> texto("Confirmar Nueva Clave:","password","clave2", 1, 15,  20,"","$GLOBALS[clave2]");

            $formulario -> nueva_tabla();

      if($matriz[0][2] == 1 or $matriz[0][2] == 2 or $matriz[0][2] == 3)

        $formulario -> radio("Sin Filtro:", "filtro", "0", 0, 0);

      else

        $formulario -> radio("Sin Filtro:", "filtro", "0", 1, 0);

      if($matriz[0][2] == 1)

        $formulario -> radio("Cliente:", "filtro", "1", 1, 0);

      else

        $formulario -> radio("Cliente:", "filtro", "1", 0, 0);

      if($matriz[0][2] == 2)

        $formulario -> radio("Puesto de Control:", "filtro", "2", 1, 0);

      else

        $formulario -> radio("Puesto de Control:", "filtro", "2", 0, 0);

      if($matriz[0][2] == 3)

        $formulario -> radio("Conductor:", "filtro", "3", 1, 0);

      else

        $formulario -> radio("Conductor:", "filtro", "3", 0, 0);

      $formulario -> nueva_tabla();

      $formulario -> lista("Cliente:", "cliente", $clientes, 0);

      $formulario -> lista("Puesto de Control:", "contro", $contros, 1);

      $formulario -> lista("Conductor:", "conduc", $conducs, 0);

      $formulario -> nueva_tabla();

      $formulario -> oculto("usuario","$usuario",0);

      $formulario -> oculto("usuari_a", $GLOBALS[usuari], 0);

      $clave_a = $matriz[0][1];

      $clave_a = base64_decode($clave_a);

      $formulario -> oculto("clave_ant","$clave_a", 0);

      $formulario -> oculto("opcion",0, 0);

      $formulario -> oculto("cod_servic", $GLOBALS[cod_servic], 0);

      $formulario -> oculto("window","central", 0);

      $formulario -> botoni("Actualizar", "aceptar_act()", 0);

      $formulario -> cerrar();



 }//FIN FUNCTION CAPTURA

// *****************************************************

//FUNCION ACTUALIZAR

// *****************************************************

 function Actualizar()

 {

   $fec_actual = date("Y-m-d H:i:s");

   if($GLOBALS[filtro] == 1)

     $id_filtro = $GLOBALS[cliente];

   else if($GLOBALS[filtro] == 2)

     $id_filtro = $GLOBALS[contro];

   else if($GLOBALS[filtro] == 3)

     $id_filtro = $GLOBALS[conduc];

   else

     $id_filtro = 0;

   //reasignacion de variables

   if($GLOBALS[clave1] == "")

     $clave = base64_encode($GLOBALS[clave_ant]);

   else

     $clave = base64_encode($GLOBALS[clave1]);

   //query de insercion

   $query = "UPDATE ".BASE_DATOS.".tab_usuari_wapxxx

                SET cod_usuari = '$GLOBALS[usuari]',

                    cod_clavex = '$clave',

                    idx_filtro = '$GLOBALS[filtro]',

                    cod_filtro = '$id_filtro'

              WHERE cod_usuari = '$GLOBALS[usuari_a]'";

   $consulta = new Consulta($query, $this -> conexion);



  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Usuario</a></b>";

     $mensaje =  "El Usuario <b>".$GLOBALS[usuari]."</b> Se Actualizo con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ACTUALIZAR USUARIOS WAP",$mensaje);
  }

 }//FIN FUNCTION ACTUALIZAR



}//FIN CLASE PROC_USUARI

     $proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>
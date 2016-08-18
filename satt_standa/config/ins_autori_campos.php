<?php
// Modulo de insercion de Niveles de Autorizacion
// Noviembre 21 de 2005
// Alejandro Ortegon R


class Ins_Autori_Campos

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
     $this -> Listar_Perfiles();
  else
     {
      switch($GLOBALS[opcion])
       {

        case "1":
          $this -> Listar_Niveles();
          break;
        case "2":
          $this -> Insert();
          break;
       }//FIN SWITCH

     }// FIN ELSE GLOBALS OPCION

 }//FIN FUNCION PRINCIPAL


 function Listar_Perfiles()
 {

     $this-> usuario ->listar($this -> conexion);
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];


     //lista los Perfiles
             $query = "SELECT a.cod_perfil,a.nom_perfil
                         FROM ".BASE_DATOS.".tab_genera_perfil a
                              ORDER BY 2";

             $consulta = new Consulta($query, $this -> conexion);
             $matriz = $consulta -> ret_matriz();

        for($i=0;$i<sizeof($matriz);$i++)
             $matriz[$i][0]= "<a href=\"index.php?cod_servic=".$GLOBALS["cod_servic"]."&window=central&perfil=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

      $formulario = new Formulario ("index.php","post","NIVELES DE AUTORIZACION","form_perfil");
      $formulario -> linea("Listado de Perfiles",0,"t2");

      $formulario -> nueva_tabla();
      $formulario -> linea("Codigo",0,"t");
      $formulario -> linea("Perfil",1,"t");

      for($i=0; $i < sizeof($matriz); $i++)
      {
       $formulario -> linea($matriz[$i][0],0,"i");
       $formulario -> linea($matriz[$i][1],1,"i");
      }

     $formulario -> nueva_tabla();
     $formulario -> cerrar();

 }//FIN FUNCION


 function Listar_Niveles()
 {
     $this-> usuario ->listar($this -> conexion);
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];

     $query = "SELECT cod_perfil,nom_perfil
                 FROM ".BASE_DATOS.".tab_genera_perfil
                WHERE cod_perfil = '$GLOBALS[perfil]'";

     $consulta = new Consulta($query, $this -> conexion);
     $perfil_a = $consulta -> ret_arreglo();


     $query = "SELECT nom_autori,cod_autori
                 FROM ".CENTRAL.".tab_autori_campos
                      ORDER BY 1";
     $consulta = new Consulta($query, $this -> conexion);
     $matriz = $consulta -> ret_matriz();

     $formulario = new Formulario ("index.php","post","NIVELES DE AUTORIZACION","form_nivaut");

     $formulario -> linea("Asignación de Parametros y Valores por Perfil",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> linea("Código",0,"t");
     $formulario -> linea($perfil_a[0],1,"i");
     $formulario -> linea("Nombre",0,"t");
     $formulario -> linea($perfil_a[1],1,"i");

      $formulario -> nueva_tabla();
      $formulario -> linea("",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Autorización",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Minimo",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Maximo",1,"t");

      for($i=0; $i < sizeof($matriz); $i++)
      {
	$query = "SELECT val_minimo, val_maximo
		  FROM ".BASE_DATOS.".tab_autori_perfil
		  WHERE cod_autori = '".$matriz[$i][1]."'
		  AND   cod_perfil = '$GLOBALS[perfil]'";
	$consulta = new Consulta($query, $this -> conexion);
	if($autori = $consulta -> ret_arreglo())
	{
	   $chek = 1;
	   $GLOBALS["valmin"][$i] = $autori[0];
	   $GLOBALS["valmax"][$i] = $autori[1];
	}
	else
	{
	   $chek = 0;
	   $GLOBALS["valmin"][$i] = 0;
	   $GLOBALS["valmax"][$i] = 0;
	};

        if($i%2 == 0)
        {

          $formulario -> caja("","nivel[$i]",$matriz[$i][1],$chek,0);
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> texto ("","text","valmin[$i]\" id=mi$i onKeyUp=\"if(isNaN(this.value)){this.value=''}\"",0,10,20,"",$GLOBALS["valmin"][$i]);
          $formulario -> texto ("","text","valmax[$i]\" id=ma$i onKeyUp=\"if(isNaN(this.value)){this.value=''}\" onBlur=\"if(this.value < form_nivaut.mi$i.value){this.value=''}\"",1,10,20,"",$GLOBALS["valmin"][$i]);

        }

       else
        {
          $formulario -> caja("","nivel[$i]",$matriz[$i][1],$chek,0);
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> texto ("","text","valmin[$i]\" id=mi$i onKeyUp=\"if(isNaN(this.value)){this.value=''}\"",0,10,20,"",$GLOBALS["valmin"][$i]);
          $formulario -> texto ("","text","valmax[$i]\" id=ma$i onKeyUp=\"if(isNaN(this.value)){this.value=''}\" onBlur=\"if(this.value < form_nivaut.mi$i.value){this.value=''}\"",1,10,20,"",$GLOBALS["valmax"][$i]);

       }
      }//fin for
      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("nperfi","".$encabe[1][1]."",0);
      $formulario -> oculto("perfil","$GLOBALS[perfil]",0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("maximo","".sizeof($matriz)."",0);
      $formulario -> oculto("opcion",2,0);
      $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
      $formulario -> nueva_tabla();
      $formulario -> botoni("Aceptar","acep_autori()",0);
      $formulario -> botoni("Borrar","form_nivaut.reset()",1);
      $formulario -> cerrar();

 }//FIN FUNCION

// *****************************************************
 function Insert()
 {
   $fec_actual = date("Y-m-d H:i:s");

   $query = "DELETE FROM ".BASE_DATOS.".tab_autori_perfil
	     WHERE cod_perfil = '$GLOBALS[perfil]'";
   $insercion = new Consulta($query, $this -> conexion, "BR");

   for($i = 0; $i < $GLOBALS[maximo]; $i++)
   {

    if($GLOBALS[nivel][$i])
    {

     if(!$GLOBALS[valmin][$i] && !$GLOBALS[valmax][$i])
     {
       $GLOBALS[valmin][$i] = 'NULL';
       $GLOBALS[valmax][$i] = 'NULL';
     }
     else
     {
       $GLOBALS[valmin][$i] = "'".$GLOBALS[valmin][$i]."'";
       $GLOBALS[valmax][$i] = "'".$GLOBALS[valmax][$i]."'";
     };

     $query = "INSERT INTO ".BASE_DATOS.".tab_autori_perfil(
                           cod_autori,cod_perfil,val_minimo,val_maximo,
                           usr_creaci,fec_creaci)
                      VALUES ('".$GLOBALS[nivel][$i]."','$GLOBALS[perfil]',
                              ".$GLOBALS[valmin][$i].",".$GLOBALS[valmax][$i].",
                              '$GLOBALS[usuario]','$fec_actual')";
     $insercion = new Consulta($query, $this -> conexion, "R");

    }
   }

   if($insercion = new Consulta("COMMIT", $this -> conexion))
   {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Volver al Listado</a></b>";

     $mensaje =  "Se Han Registrado los Niveles de Autorizaci&oacute;n Para el Perfil <b>".$GLOBALS[perfil]."</b>".$link_a;
     $mens = new mensajes();
     $mens -> correcto("NIVELES DE AUTORIZACION",$mensaje);
   }
 }//FIN FUNCION
}//FIN CLASE

$proceso = new Ins_Autori_Campos($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
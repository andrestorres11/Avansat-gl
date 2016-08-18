<?php

class CamBan
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

      switch($GLOBALS[opcion])
      {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> registrar();
          break;
        default:
          $this -> Formulario();
          break;
      }
 }





 function Formulario()
 {
   $inicio[0][0] = 0;
   $inicio[0][1] = '-';
	 //codigo de ruta
   $query = "SELECT cod_usuari,nom_usuari FROM ".BASE_DATOS.".tab_genera_usuari ";
   $consulta = new Consulta( $query, $this -> conexion );
	 $usuarios = $consulta -> ret_matriz();
   $usuari = array_merge($inicio, $usuarios);
   if($GLOBALS[usuari]){
     $query = "SELECT cod_usuari,nom_usuari 
               FROM ".BASE_DATOS.".tab_genera_usuari 
               WHERE cod_usuari = '".$GLOBALS[usuari]."'";
     $consulta = new Consulta( $query, $this -> conexion );
  	 $usuar = $consulta -> ret_matriz();
     $usuari = array_merge($usuar,$usuari);  
   }  
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/monit.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Cambio de Bandeja","form_ins\" id=\"formularioID");
   $formulario -> nueva_tabla();
   $formulario -> lista("Usuario","usuari\" id=\"usuariID\" onchange=\"form_ins.submit();",$usuari,1); 
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> oculto("opcion\" id=\"opcionID",1,0);

  if($GLOBALS['usuari']){
    $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
    $query = "SELECT cod_inicio
   				    FROM ".BASE_DATOS.".tab_genera_usuari
   			      WHERE cod_usuari = '".$GLOBALS['usuari']."'";
    $consulta = new Consulta($query, $this -> conexion);
    $cod_inicio = $consulta -> ret_matriz();
    if($cod_inicio[0][0] == '' || $cod_inicio[0][0] == '1'){
      $bandeja[0][0] = '1';
      $bandeja[0][1] = 'Clasica';
      $bandeja[1][0] = '2';
      $bandeja[1][1] = 'Turnos';
    }else{
      $bandeja[0][0] = '2';
      $bandeja[0][1] = 'Turnos';
      $bandeja[1][0] = '1';
      $bandeja[1][1] = 'Clasica';
    }
    $formulario -> lista("Bandeja","bandeja\" id=\"usuariID",$bandeja,1); 
    $formulario -> nueva_tabla();
    $formulario -> botoni("Aceptar","aceptar_cam(3)",0);
    }
  $formulario -> nueva_tabla();
  echo "<br><br><br><br><br><br><br><br>";
  $formulario -> cerrar();
  


 }

   function registrar()
    {		
        global $HTTP_POST_FILES;
        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        $query = "UPDATE ".BASE_DATOS.".tab_genera_usuari
									 SET cod_inicio = '".$GLOBALS['bandeja']."' 
									 WHERE cod_usuari = '".$GLOBALS['usuari']."' 
										";
			  $insercion = new Consulta( $query, $this -> conexion, "BR" );
        if( $insercion = new Consulta("COMMIT", $this -> conexion))
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Cambiar Otra Bandeja de Entrada</a></b>";

            if($msm)
                $mensaje = $msm;
            $mensaje .=  "Se Cambio la Bandeja Con Exito. Recuerde Que Para Tome los Cambios el Usuario Debe Cerrar la Sesion";
            $mens = new mensajes();
            $mens -> correcto("INSERTAR Horario",$mensaje);
        }
    }

  
	
	
	
	
}//FIN CLASE PROC_DESPAC

   $proceso = new CamBan($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>

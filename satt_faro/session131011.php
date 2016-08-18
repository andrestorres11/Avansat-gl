<?php
/****************************************************************************
NOMBRE:   SESSIONES
FUNCION:  MANEJO DE PERFILES DE USUARIO CON PHP
AUTHOR: LEONARDO ROMERO
FECHA MODIFICACION 24 FEBRERO 2005
****************************************************************************/

setlocale(LC_TIME,"es_ES");

include ("constantes.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/generales.inc");

class session
{
    var $codigo,//Este codigo identifica la aplicacion en la base de datos de seguridad
        $usuario_aplicacion,//El usuario que hace uso de la aplicación
        $tit,//titulo  del formulario
        $act,//valor del action en el formulario
        $mensajes,//maneja los mensajes de error
        $imagen, //Logotipo de la empresa
        $conexion;//EL enlace que debe establecer con la base de datos

    function session()
    {
          $this -> Principal();
    }

    function Principal()
    {
  	if(!isset($GLOBALS[op]))
        {
         	$this -> Formulario();
        }
  	else
     	{
	   switch($GLOBALS[op])
	   {
		case "1":
          	$this -> Validar();
          	break;

        	case "2":
          	$this -> logout();
          	break;

        	default:
                $this -> Formulario();
          	break;
       	   }//FIN SWITCH
     	}// FIN ELSE GLOBALS OPCION
    }//FIN FUNCION PRINCIPAL

    function Formulario($mensaje="")
    {
                $login[0] = ESAD." - ".NOMSAD;
                $login[1] = NOMSAD;
                $login[2] = $mensaje;
                $tmpl_file = "login.html";
                $thefile = implode("", file($tmpl_file));
                $thefile = addslashes($thefile);
                $thefile = "\$r_file=\"".$thefile."\";";
                eval($thefile);
                print $r_file;
    }

    function Validar()
    {
     if(($GLOBALS["usuario"] == "") || ($GLOBALS["clave"] == ""))
     {
          $this -> Formulario("Ingrese sus datos...");
     }
     else
       {
                $this -> conexion = new Conexion("localhost", USUARIO, CLAVE, "".BASE_DATOS."");//Se crea la         conexión a la base de datos

                $this -> usuario_aplicacion = new Usuari($GLOBALS["usuario"]);
                $this -> usuario_aplicacion -> listar($this -> conexion);
                $datos_usuario = $this -> usuario_aplicacion -> retornar();

        $clave_encriptada = base64_encode($GLOBALS["clave"]);

        //se valida que las contraseñas sean iguales
        if($datos_usuario["clv_usuari"] == $clave_encriptada){
                session_start();
                $nom_sesion = session_name($GLOBALS["usuario"]);
                $_SESSION[datos_usuario] = $datos_usuario;
                $_SESSION[id] = session_id();
                $_SESSION[ip] = $_SERVER["REMOTE_ADDR"];
                online($_SESSION[datos_usuario], $this -> conexion);
                header("Location: index.php");

              return true;

                }
        else
        {
              $this -> Formulario("Usuario o Clave Invalidos...");
        }

        }

    }//fin funcion validar

   function logout()
   {
   	$this -> conexion = new Conexion("localhost", USUARIO, CLAVE, BASE_DATOS);//Se crea la conexiï¿½ a la base de datos
     $this -> usuario_aplicacion = new Usuari($GLOBALS["usuario"]);
     $this -> usuario_aplicacion -> listar($this -> conexion);
     $datos_usuario = $this -> usuario_aplicacion -> retornar();
     offline($datos_usuario, $this -> conexion);
            session_start();
        // Destruye todas las variables de la sesi&oacute;n
                session_unset();
                // Finalmente, destruye la sesi&oacute;n
                session_destroy();
         $this -> conexion -> cerrar(0);

        $this -> Formulario("La session se cerro correctamente...");
   }

   function val_session()
   {
	session_start();

            if (session_id() == $_SESSION["id"])
                    {
                    return true;
                        }
        else
                {
             return false;
                }
   }
}//FIN CLASE PROC_PERFIL

$proceso = new session();

?>

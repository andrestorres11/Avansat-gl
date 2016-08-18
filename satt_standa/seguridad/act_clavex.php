<?php

/*Autor: Adrian Ignacio Cardona
  Modificado por: Leonardo Romero
  Ultima Actualización: 01-08-2005

    MODIFICADO POR:     MIGUEL ANGEL GARCIA RIVERA
    FECHA MODIFICACION: 2012/10/11
  */

//Esta clase manipula un proceso de actualizacion en la tabla tab_genera_usuari
class Proc_act_usuari
{
    var $usuario,
        $conexion;

    function __construct($us, $co)
    {
        $this -> usuario = $us;
        $this -> conexion = $co;
        $this -> principal();
    }

    function principal()
    {
        if($GLOBALS["actual"] == 1)
           $this -> actualizar();
        else
           $this -> formulario();
    }

    function actualizar()
    {
        $this->usuario->listar($this -> conexion);
        $datos_usuari = $this->usuario-> retornar();

        if($GLOBALS["clave"])
           $GLOBALS["clv_usuari"] = base64_encode($GLOBALS["new_clv_usuari"]);
        else
           $GLOBALS["clv_usuari"] = base64_encode($GLOBALS["clv_usuari"]);

        $this->usuario->actualizar($this -> conexion, "BRC", $GLOBALS["clv_usuari"], $GLOBALS["nom_usuari"], $GLOBALS["usr_emailx"], $datos_usuari["cod_perfil"], $datos_usuari["cod_inicio"], $GLOBALS["num_cedula"]);

        //echo "<img src=\"../sadc_standa/imagenes/ok.gif\">Los Datos del ".$GLOBALS["nom_usuari"]." se modificaron con exito";
        echo "<img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/ok.gif\">Los Datos del ".$GLOBALS["nom_usuari"]." se modificaron con exito";
           unset($GLOBALS[actual]);
           $this -> formulario();
    }

    function formulario()
    {
            echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/usuari.js\"></script>\n";
            $this->usuario->listar($this -> conexion);

            $datos_usuari = $this->usuario-> retornar();
            $datos_usuari["clv_usuari"] = base64_decode($datos_usuari["clv_usuari"]);
            $query = " SELECT num_cedula, nom_usuari
                 FROM ".BASE_DATOS.".tab_genera_usuari a  
                 WHERE a.cod_usuari = '".$_SESSION[datos_usuario][cod_usuari]."' ";
            $consulta = new Consulta($query, $this -> conexion);
            $mResult = $consulta -> ret_matriz('a');

            $formulario = new Formulario ("index.php","post","CAMBIO DE CLAVE","form_clavex");

            $formulario -> nueva_tabla();
            $formulario -> texto ("Cédula","text","num_cedula",1,15,50,"","".$mResult[0]["num_cedula"].""); 
            $formulario -> texto ("Nombre","text","nom_usuari",0,15,50,"","".$datos_usuari["nom_usuari"]."");
            $formulario -> texto("E-mail: ", "text", "usr_emailx", 1, 15, 50, "","".$datos_usuari["usr_emailx"]."");
            $formulario -> caja("Cambiar Contraseña","clave\" onClick=\"cambiar_clave()\" ",1,0,0);
            $formulario -> texto("Nueva Contraseña: ", "password", "new_clv_usuari", 1, 15, 20, "","");
            $formulario -> texto("Confirmar Nueva Constraseña: ", "password", "new_confirma", 1, 15, 20, "","");

            $formulario -> nueva_tabla();
            $formulario -> oculto("actual", 0, 0, 0);
            $formulario -> oculto("clv_usuari", $datos_usuari["clv_usuari"], 0, 0);
            $formulario -> oculto("confirma", $datos_usuari["clv_usuari"], 0, 0);
            $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0, 0);
            $formulario -> oculto("cod_serant", $GLOBALS["cod_servic"], 0, 0);
            $formulario -> oculto("window", $GLOBALS["window"], 0, 0);
            $formulario -> botoni("Aceptar","validar()",0);

            $formulario -> cerrar();
    }
}

$proceso = new Proc_act_usuari($this -> usuario_aplicacion, $this -> conexion);
?>
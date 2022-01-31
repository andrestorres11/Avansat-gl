<?php
/*Autor: Adrian ignacio Cardona Mora
Ultima Actualización: 24-10-2003*/

//Esta clase manipula un registro en la tabla tab_aplica_perfil
class Aplica_Perfil
{
    //Atributos
    var $cod_aplica,
        $cod_perfil;

    //Metodos
    function Aplica_Perfil($ca, $cp)
    {
        $this -> cod_aplica = $ca;
        $this -> cod_perfil = $cp;
    }

    function insertar($conexion, $transaccion)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_perfil
                  VALUES ('" . $this -> cod_aplica . "',
                          '" . $this -> cod_perfil . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function listar($conexion)
    {
        $query = "SELECT *
                    FROM ".BASE_DATOS.".tab_aplica_perfil
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_perfil = '" . $this -> cod_perfil . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion)
    {
        $query = "UPDATE ".BASE_DATOS.".tab_aplica_perfil
                     SET
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_usuari = '" . $this -> cod_perfil . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_aplica_perfil
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_perfil = '" . $this -> cod_perfil . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_aplica"] = $this -> cod_aplica;
        $datos["cod_perfil"] = $this -> cod_perfil;

	return $datos;
    }
}
/* Fin Archivo */
?>
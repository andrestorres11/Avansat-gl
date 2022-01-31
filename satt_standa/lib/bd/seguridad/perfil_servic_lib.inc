<?php
/*Autor: Adrian ignacio Cardona Mora
Ultima Actualización: 24-10-2003*/

//Esta clase manipula un registro en la tabla ".BASE_DATOS.".tab_perfil_servic
class Perfil_Servic
{
    var $cod_perfil,
        $cod_servic;

    function Perfil_Servic($cp, $cs)
    {
        $this -> cod_perfil = $cp;
        $this -> cod_servic = $cs;
    }

    function insertar($conexion, $transaccion)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_perfil_servic
                  VALUES ('" . $this -> cod_perfil . "',
                          '" . $this -> cod_servic . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function listar($conexion)
    {
        $query = "SELECT *
                    FROM ".BASE_DATOS.".tab_perfil_servic
                   WHERE cod_perfil = '" . $this -> cod_perfil . "' AND
                         cod_servic = '" . $this -> cod_servic . "'";
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
        $query = "UPDATE ".BASE_DATOS.".tab_perfil_servic
                     SET
                   WHERE cod_perfil = '" . $this -> cod_perfil . "' AND
                         cod_servic = '" . $this -> cod_servic . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_perfil_servic
                   WHERE cod_perfil = '" . $this -> cod_perfil . "' AND
                         cod_servic = '" . $this -> cod_servic . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_perfil"] = $this -> cod_perfil;
        $datos["cod_servic"] = $this -> cod_servic;
        return $datos;
    }
}
/* Fin Archivo */
?>
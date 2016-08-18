<?php
/*Autor: Adrian ignacio Cardona Mora
Ultima Actualización: 24-10-2003*/

//Esta clase manipula un registro en la tabla tab_aplica_usuari
class Aplica_Usuari
{
    var $cod_aplica,
        $cod_usuari;

    function Aplica_Usuari($ca, $cu)
    {
        $this -> cod_aplica = $ca;
        $this -> cod_usuari = $cu;
    }

    function insertar($conexion, $transaccion)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_usuari
                  VALUES ('" . $this -> cod_aplica . "',
                          '" . $this -> cod_usuari . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function listar($conexion)
    {
        $query = "SELECT *
                    FROM ".BASE_DATOS.".tab_aplica_usuari
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
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
        $query = "UPDATE ".BASE_DATOS.".tab_aplica_usuari
                     SET
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_aplica_usuari
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_aplica"] = $this -> cod_aplica;
        $datos["cod_usuari"] = $this -> cod_usuari;

	return $datos;
    }
}
/* Fin Archivo */
?>
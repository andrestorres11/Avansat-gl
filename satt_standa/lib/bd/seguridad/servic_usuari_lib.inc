<?php
/*Autor: Adrian ignacio Cardona Mora
Ultima Actualización: 24-10-2003*/

//Esta clase manipula un registro en la tabla ".BASE_DATOS.".tab_servic_usuari
class Servic_Usuari
{
    var $cod_servic,
        $cod_usuari;

    function Servic_Usuari($cs, $cu)
    {
        $this -> cod_servic = $cs;
        $this -> cod_usuari = $cu;
    }

    function insertar($conexion, $transaccion)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari
                  VALUES ('" . $this -> cod_servic . "',
                          '" . $this -> cod_usuari . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);
	/*$query = "SELECT cod_aplica
		  FROM ".BASE_DATOS.".tab_genera_servic
                  WHERE cod_servic = '" . $this -> cod_servic . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
	$aplica = $consulta -> ret_arreglo();
	$query = "INSERT INTO ".BASE_DATOS.".tab_aplica_usuari
                  VALUES ('" . $aplica["cod_aplica"] . "',
                          '" . $this -> cod_usuari . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);*/
    }

    function listar($conexion)
    {
        $query = "SELECT *
                    FROM ".BASE_DATOS.".tab_servic_usuari
                   WHERE cod_servic = '" . $this -> cod_servic . "' AND
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
        $query = "UPDATE ".BASE_DATOS.".tab_servic_usuari
                     SET
                   WHERE cod_servic = '" . $this -> cod_servic . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_servic_usuari
                   WHERE cod_servic = '" . $this -> cod_servic . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_servic"] = $this -> cod_servic;
        $datos["cod_usuari"] = $this -> cod_usuari;
        return $datos;
    }
}
/* Fin Archivo */
?>
<?php
/*Autor: Adrian ignacio Cardona Mora
Ultima Actualización: 24-10-2003*/

//Esta clase manipula un registro en la tabla ".CENTRAL.".tab_servic_servic
class Servic_Servic
{
    //Atributos
    var $cod_serpad,
        $cod_serhij;

    //Metodos
    function Servic_Servic($ch)
    {
        $this -> cod_serhij = $ch;
    }

    function insertar($conexion, $transaccion, $cp)
    {
        $query = "INSERT INTO ".CENTRAL.".tab_servic_servic
                  VALUES ('" . $cp . "',
                          '" . $this -> cod_serhij . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> cod_serpad = $cp;
    }

    function listar($conexion)
    {
        $query = "SELECT cod_serpad
                    FROM ".CENTRAL.".tab_servic_servic
                   WHERE cod_serhij = '" . $this -> cod_serhij . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> cod_serpad = $datos["cod_serpad"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $cp)
    {
        $query = "UPDATE ".CENTRAL.".tab_servic_servic
                     SET cod_serpad = '" . $cp . "'
                   WHERE cod_serhij = '" . $this -> cod_serhij . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> cod_serpad = $cp;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".CENTRAL.".tab_servic_servic
                   WHERE cod_serhij = '" . $this -> cod_serhij . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_serpad"] = $this -> cod_serpad;
        $datos["cod_serhij"] = $this -> cod_serhij;
        return $datos;
    }
}
/* Fin Archivo */
?>
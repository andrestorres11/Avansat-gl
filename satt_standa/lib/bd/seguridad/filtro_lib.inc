<?php
/*Autor: Adrian ignacio Cardona Mora
  Modificado por: Adrian Ignacio Cardona
  Ultima Actualización: 29-01-2004*/

//Esta clase manipula un registro en la tabla ".BASE_DATOS.".tab_genera_filtro
class Filtro
{
    //Atributos
    var $cod_filtro,
        $nom_filtro,
            $usr_creaci,
            $fec_creaci,
            $usr_modifi,
            $fec_modifi;

    //Metodos
    function Filtro($co = 0)
    {
        $this -> cod_filtro = $co;
    }

    function insertar($conexion, $transaccion, $no, $uc, $fc)
    {
        $query = "SELECT MAX(cod_filtro) AS max_codigo
                    FROM ".CENTRAL.".tab_genera_filtro";
        $consulta = new Consulta($query, $conexion);
        if($consulta -> ret_num_rows())
        {
            $max_codigo = $consulta -> ret_arreglo();
            $this -> cod_filtro = $max_codigo["max_codigo"] + 1;
        }
        else
        {
            $this -> cod_filtro = 1;
        }
        $query = "INSERT INTO ".CENTRAL.".tab_genera_filtro
                  VALUES ('".$this -> cod_filtro."',
                          '$no',
                              '$uc',
                              '$fc',
                              NULL,
                              NULL)";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_filtro = $no;
            $this -> usr_creaci = $uc;
            $this -> fec_creaci = $fc;
    }

    function listar($conexion)
    {
        $query = "SELECT nom_filtro,
                         usr_creado,
                         fec_creaci,
                         usr_modifi,
                         fec_modifi
                    FROM ".CENTRAL.".tab_genera_filtro
                   WHERE cod_filtro = '" . $this -> cod_filtro . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> nom_filtro = $datos["nom_filtro"];
                $this -> usr_creaci = $datos["usr_creado"];
                $this -> fec_creaci = $datos["fec_creaci"];
                $this -> usr_modifi = $datos["usr_modifi"];
                $this -> fec_modifi = $datos["fec_modifi"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $no, $um, $fm)
    {
        $query = "UPDATE ".CENTRAL.".tab_genera_filtro
                     SET nom_filtro = '$no',
                             usr_modifi = '$um',
                                 fec_modifi = '$fm'
                   WHERE cod_filtro = '" . $this -> cod_filtro . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_filtro = $no;
            $this -> usr_modifi = $um;
            $this -> fec_modifi = $fm;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".CENTRAL.".tab_genera_filtro
                   WHERE cod_filtro = '" . $this -> cod_filtro . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_filtro"] = $this -> cod_filtro;
        $datos["nom_filtro"] = $this -> nom_filtro;
            $datos["usr_creado"] = $this -> usr_creaci;
            $datos["fec_creaci"] = $this -> fec_creaci;
            $datos["usr_modifi"] = $this -> usr_modifi;
            $datos["fec_modifi"] = $this -> fec_modifi;

        return $datos;
    }
}
/*Final Archivo*/
?>
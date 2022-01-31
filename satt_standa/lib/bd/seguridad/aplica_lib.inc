<?php
/*Autor: Adrian Ignacio Cardona Mora
  Modificado por: Adrian Ignacio Cardona
  Ultima Actualización: 29-01-2004*/

//Esta clase manipula un registro en la tabla ".BASE_DATOS.".tab_genera_aplica
class Aplica
{
    //Atributos
    var $cod_aplica,
        $nom_aplica,
            $usr_creaci,
            $fec_creaci,
            $usr_modifi,
            $fec_modifi;

    //Metodos
    function Aplica($co = 0)
    {
        $this -> cod_aplica = $co;
    }

    function insertar($conexion, $transaccion, $no, $uc, $fc)
    {
            $vale = 0;
            if($this->cod_aplica != 0)
            {
                $query = "SELECT ".CENTRAL.".cod_aplica
                            FROM tab_genera_aplica
                    WHERE cod_aplica = '".$this->cod_aplica."'";
                    $consulta = new Consulta($query, $conexion);
                if(!$consulta->ret_num_rows())
                        $vale = 1;
        }
            if($vale == 0)
            {
                $query = "SELECT MAX(cod_aplica) AS max_codigo
                                FROM ".CENTRAL.".tab_genera_aplica";
                $consulta = new Consulta($query, $conexion);
                if($consulta -> ret_num_rows())
                {
                        $max_codigo = $consulta -> ret_arreglo();
                        $this -> cod_aplica = $max_codigo["max_codigo"] + 1;
                }
                else
                    $this -> cod_aplica = 1;
            }
        $query = "INSERT INTO ".CENTRAL.".tab_genera_aplica
                  VALUES ('".$this -> cod_aplica."',
                          '$no',
                              '$uc',
                              '$fc',
                              NULL,
                              NULL)";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_aplica = $no;
            $this -> usr_creaci = $uc;
            $this -> fec_creaci = $fc;
    }

    function listar($conexion)
    {
        $query = "SELECT nom_aplica,
                     usr_creaci,
                     fec_creaci,
                     usr_modifi,
                     fec_modifi
               FROM ".CENTRAL.".tab_genera_aplica
               WHERE cod_aplica = '" . $this -> cod_aplica . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> nom_aplica = $datos["nom_aplica"];
                $this -> usr_creaci = $datos["usr_creaci"];
                $this -> fec_creaci = $datos["fec_creaci"];
                $this -> usr_modifi = $datos["usr_modifi"];
                $this -> fec_modifi = $datos["fec_modifi"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $no, $um, $fm)
    {
        $query = "UPDATE ".CENTRAL.".tab_genera_aplica
                     SET nom_aplica = '$no',
                                         usr_modifi = '$um',
                                                 fec_modifi = '$fm'
                   WHERE cod_aplica = '" . $this -> cod_aplica . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_aplica = $no;
        $this -> usr_modifi = $um;
        $this -> fec_modifi = $fm;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".CENTRAL.".tab_genera_aplica
                   WHERE cod_aplica = '" . $this -> cod_aplica . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_aplica"] = $this -> cod_aplica;
        $datos["nom_aplica"] = $this -> nom_aplica;
            $datos["usr_creaci"] = $this -> usr_creaci;
            $datos["fec_creaci"] = $this -> fec_creaci;
            $datos["fec_modifi"] = $this -> usr_modifi;
            $datos["fec_modifi"] = $this -> fec_modifi;

        return $datos;
    }
}
/* Final Archivo */
?>
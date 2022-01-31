<?php
/*Autor: Danilo Avilan Arias
  Modificado por: Adrian Ignacio Cardona
  Ultima Actualización: 29-01-2004*/

//Esta clase manipula un registro en la tabla seguridad.tab_genera_servic
class Servic
{
    //Atributos
    var $cod_servic,
        $nom_servic,
        $rut_archiv,
                $rut_jscrip,
        $bod_jscrip,
        $cod_aplica,
            $usr_creaci,
            $fec_creaci,
            $usr_modifi,
            $fec_modifi;

    //Métodos
    function Servic($co = 0)
    {
        $this -> cod_servic = $co;
    }

    function insertar($conexion, $transaccion, $no, $ra, $rj, $bj, $ca, $uc, $fc)
    {
        $vale = 0;
            if($this->cod_servic != 0)
            {
                $query = "SELECT cod_servic
                    FROM ".CENTRAL.".tab_genera_servic
                                    WHERE cod_servic = '".$this->cod_servic."'";
                $consulta = new Consulta($query, $conexion);
                if(!$consulta->ret_num_rows())
                        $vale = 1;
            }
            if($vale == 0)
            {
                $query = "SELECT MAX(cod_servic) AS maximo
                            FROM ".CENTRAL.".tab_genera_servic";
                    $consulta = new Consulta($query, $conexion);
                    if($consulta -> ret_num_rows())
                    {
                    $max_codigo = $consulta -> ret_arreglo();
                            $maximo = $max_codigo["maximo"] + 1;
                            $query = "SELECT MAX(cod_servic) AS maxapp
                                FROM ".CENTRAL.".tab_genera_servic WHERE cod_aplica = '".$ca."'";
                        $consulta = new Consulta($query, $conexion);
                        $max_codigo = $consulta -> ret_arreglo();
                            $maxapp = $max_codigo["maxapp"] + 1;
                    $query = "SELECT cod_servic
                                FROM ".CENTRAL.".tab_genera_servic
                        WHERE cod_servic = '".$maxapp."'";
                        $consulta = new Consulta($query, $conexion);
                    if($consulta -> ret_num_rows())
                        $this -> cod_servic = $maximo;
                    else
                        $this -> cod_servic = $maxapp;
                    }
                    else
                        $this -> cod_servic = 1;
            }
                if(!$ra)
                    $ra = 'null';
            else
                $ra = "'$ra'";
        if(!$rj)
                        $rj = 'null';
            else
                $rj = "'$rj'";
            if(!$bj)
                        $bj = 'null';
            else
                $bj = "'$bj'";
        $query = "INSERT INTO ".CENTRAL.".tab_genera_servic
                  VALUES ('".$this->cod_servic."',
                          '$no',
                          $ra,
                          $rj,
                          $bj,
                          '$ca',
                              '$uc',
                              '$fc',
                              NULL,
                              NULL)";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_servic = $no;
        $this -> rut_archiv = $ra;
        $this -> rut_jscrip = $rj;
        $this -> bod_jscrip = $bj;
        $this -> cod_aplica = $ca;
            $this -> usr_creaci = $uc;
            $this -> fec_creaci = $fc;
    }

    function listar($conexion)
    {
        $query = "SELECT nom_servic,
                         rut_archiv,
                         rut_jscrip,
                         bod_jscrip,
                         cod_aplica,
                             usr_creaci,
                             fec_creaci,
                             usr_modifi,
                             fec_modifi
                    FROM ".CENTRAL.".tab_genera_servic
                   WHERE cod_servic = '" . $this -> cod_servic . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> nom_servic = $datos["nom_servic"];
            $this -> rut_archiv = $datos["rut_archiv"];
            $this -> rut_jscrip = $datos["rut_jscrip"];
            $this -> bod_jscrip = $datos["bod_jscrip"];
            $this -> cod_aplica = $datos["cod_aplica"];
                $this -> usr_creaci = $datos["usr_creaci"];
                $this -> fec_creaci = $datos["fec_creaci"];
                $this -> usr_modifi = $datos["usr_modifi"];
                $this -> fec_modifi = $datos["fec_modifi"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $no, $ra, $rj, $bj, $ca, $um, $fm)
    {
        if(!$ra)
                $ra = 'null';
            else
                $ra = "'$ra'";
        if(!$rj)
                $rj = 'null';
            else
                $rj = "'$rj'";
        if(!$bj)
                        $bj = 'null';
            else
                $bj = "'$bj'";
       $query = "UPDATE ".CENTRAL.".tab_genera_servic
                     SET nom_servic = '$no',
                         rut_archiv = $ra,
                         rut_jscrip = $rj,
                         bod_jscrip = $bj,
                         cod_aplica = '$ca',
                             usr_modifi = '$um',
                             fec_modifi = '$fm'
                   WHERE cod_servic = '" . $this -> cod_servic . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_servic = $no;
        $this -> rut_archiv = $ra;
        $this -> rut_jscrip = $rj;
        $this -> bod_jscrip = $bj;
        $this -> cod_aplica = $ca;
            $this -> usr_modifi = $um;
            $this -> fec_modifi = $fm;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".CENTRAL.".tab_genera_servic
                   WHERE cod_servic = '" . $this -> cod_servic . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_servic"] = $this -> cod_servic;
        $datos["nom_servic"] = $this -> nom_servic;
        $datos["rut_archiv"] = $this -> rut_archiv;
        $datos["rut_jscrip"] = $this -> rut_jscrip;
        $datos["bod_jscrip"] = $this -> bod_jscrip;
        $datos["cod_aplica"] = $this -> cod_aplica;
            $datos["usr_creaci"] = $this -> usr_creaci;
            $datos["fec_creaci"] = $this -> fec_creaci;
            $datos["usr_modifi"] = $this -> usr_modifi;
            $datos["fec_modifi"] = $this -> fec_modifi;

        return $datos;
    }
}
/*Fin archivo*/
?>
<?php
/*Autor: Danilo Avilan Arias
  Modificado por: Adrian Ignacio Cardona
  Ultima Actualización: 29-01-2004*/

//Esta clase manipula un registro en la tabla tab_genera_usuari
class Usuari
{
    //Atributos
    var $cod_usuari,
        $clv_usuari,
        $nom_usuari,
        $usr_emailx,
        $cod_perfil,
        $cod_inicio;

    //Metodos
    function Usuari($co)
    {
        $this -> cod_usuari = $co;
    }

    function insertar($conexion, $transaccion, $cl, $no, $ue, $cp, $ci)
    {
                if(!$ue)
                        $ue = 'null';
                else
                        $ue = "'$ue'";
            if(!$cp)
                        $cp = 'null';
        else
                $cp = "'$cp'";
                if(!$ci)
                        $ci = 'null';
        else
                $ci = "'$ci";
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_usuari
                  VALUES ('" . $this -> cod_usuari . "',
                          '$cl',
                          '$no',
                                                  $ue,
                          $cp,
                                                  $ci)";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> clv_usuari = $cl;
        $this -> nom_usuari = $no;
                $this -> usr_emailx = $ue;
                $this -> cod_perfil = $cp;
        $this -> cod_inicio = $ci;
    }

    function listar($conexion)
    {
        $query = "SELECT clv_usuari,
                         nom_usuari,
                                                 usr_emailx,
                             cod_perfil,
                             cod_inicio
                    FROM ".BASE_DATOS.".tab_genera_usuari
                   WHERE cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> clv_usuari = $datos["clv_usuari"];
            $this -> nom_usuari = $datos["nom_usuari"];
            $this -> usr_emailx = $datos["usr_emailx"];
                    $this -> cod_perfil = $datos["cod_perfil"];
            $this -> cod_inicio = $datos["cod_inicio"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $cl, $no, $ue, $cp, $ci)
    {
                if(!$ue)
                        $ue = 'null';
                else
                        $ue = "'$ue'";
            if(!$cp)
                        $cp = 'null';
        else
                $cp = "'$cp'";
                if(!$ci)
                        $ci = 'null';
        else
                $ci = "'$ci'";
        $query = "UPDATE ".BASE_DATOS.".tab_genera_usuari
                     SET clv_usuari = '$cl',
                         nom_usuari = '$no',
                                                 usr_emailx = $ue,
                                                 cod_perfil = $cp,
                         cod_inicio = $ci
                   WHERE cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
                $this -> clv_usuari = $cl;
        $this -> nom_usuari = $no;
        $this -> usr_emailx = $ue;
        $this -> cod_perfil = $cp;
        $this -> cod_inicio = $ci;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_genera_usuari
                   WHERE cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_usuari"] = $this -> cod_usuari;
        $datos["clv_usuari"] = $this -> clv_usuari;
        $datos["nom_usuari"] = $this -> nom_usuari;
        $datos["usr_emailx"] = $this -> usr_emailx;
            $datos["cod_perfil"] = $this -> cod_perfil;
            $datos["cod_inicio"] = $this -> cod_inicio;
        return $datos;
    }

    function cambiar_clave($nueva_clave, $conexion)//Función para cambiar la clave de usuario
    {
        $clave_encrip = base64_encode($nueva_clave);
        $query = "UPDATE ".BASE_DATOS.".tab_genera_usuari
                     SET clv_usuari = '$clave_encrip'
                   WHERE cod_usuari = '" . $this -> cod_usuari. "'";
        $consulta = new Consulta($query, $conexion);
    }
}
/* Fin Archivo */
?>
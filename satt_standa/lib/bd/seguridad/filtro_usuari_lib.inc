<?php
/**************************************************************************************************************/
/*********En esta Librería se Especifica la Clase que Manipula la Tabla tab_filtro_usuari**********************/
/**************************************************************************************************************/
class Filtro_Usuario
{
    var $cod_filtro,
        $cod_usuari,
        $clv_filtro;

    function Filtro_Usuario($cf, $cu)
    {
        $this -> cod_filtro = $cf;
        $this -> cod_usuari = $cu;
    }

    function insertar($conexion, $cf)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_filtro_usuari
                  VALUES ('" . $this -> cod_filtro . "',
                          '" . $this -> cod_usuari . "',
                          '$cf')";
        $consulta = new Consulta($query, $conexion);
        $this -> clv_filtro = $cf;
    }

    function listar($conexion)
    {
        $query = "SELECT clv_filtro
                    FROM ".BASE_DATOS.".tab_aplica_filtro_usuari
                   WHERE cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> clv_filtro = $datos["clv_filtro"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $cf)
    {
        $query = "UPDATE ".BASE_DATOS.".tab_filtro_usuari
                     SET clv_filtro = '$cf'
                   WHERE cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion);
        $this -> clv_filtro = $cf;    }

    function eliminar($conexion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_filtro_usuari
                   WHERE cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion);
    }

    function retornar()
    {
        $datos["cod_filtro"] = $this -> cod_aplica;
        $datos["cod_usuari"] = $this -> cod_usuari;
        $datos["clv_filtro"] = $this -> clv_filtro;

        return $datos;
    }


}
?>
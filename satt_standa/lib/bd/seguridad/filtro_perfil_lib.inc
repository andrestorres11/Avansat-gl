<?php
/**************************************************************************************************************/
/*********En esta Librería se Especifica la Clase que Manipula la Tabla tab_filtro_perfil**********************/
/**************************************************************************************************************/
class Filtro_Perfil
{
    var $cod_filtro,
        $cod_perfil,
        $cod_aplica;

    function Filtro_Perfil($cf, $cp, $ca)
    {
        $this -> cod_filtro = $cf;
        $this -> cod_perfil = $cp;
    	$this -> cod_aplica = $ca;
    }

    function insertar($conexion, $cf)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_filtro_perfil
                  VALUES ('" . $this -> cod_filtro . "',
                          '" . $this -> cod_perfil . "',
                          '" . $this -> cod_aplica . "'
                          '$cf')";
        $consulta = new Consulta($query, $conexion);
        $this -> clv_filtro = $cf;
    }

    function listar($conexion)
    {
        $query = "SELECT clv_filtro
                    FROM ".BASE_DATOS.".tab_filtro_perfil
                   WHERE cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_perfil = '" . $this -> cod_perfil . "' AND
                         cod_aplica = '" . $this -> cod_aplica . "'";
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
                         cod_perfil = '" . $this -> cod_perfil . "' AND
                         cod_aplica = '" . $this -> cod_aplica . "'";
        $consulta = new Consulta($query, $conexion);
        $this -> clv_filtro = $cf;    }

    function eliminar($conexion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_filtro_usuari
                   WHERE cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_perfil = '" . $this -> cod_perfil . "' AND
                         cod_aplica = '" . $this -> cod_aplica . "'";
        $consulta = new Consulta($query, $conexion);
    }

    function retornar()
    {
        $datos["cod_filtro"] = $this -> cod_filtro;
        $datos["cod_perfil"] = $this -> cod_perfil;
        $datos["cod_aplica"] = $this -> cod_aplica;
        $datos["clv_filtro"] = $this -> clv_filtro;

        return $datos;
    }
}
?>
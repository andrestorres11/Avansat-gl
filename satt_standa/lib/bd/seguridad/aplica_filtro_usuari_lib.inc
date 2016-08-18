<?php
/*Autor: Adrian ignacio Cardona Mora
Ultima Actualización: 24-10-2003*/

//Esta clase manipula un registro en la tabla ".BASE_DATOS.".tab_aplica_filtro_usuari
class Aplica_Filtro_Usuari
{
    //Atributos
    var $cod_aplica,
        $cod_filtro,
        $cod_usuari,
        $clv_filtro;

    //Metodos
    function Aplica_Filtro_Usuari($ca, $cf, $cu)
    {
        $this -> cod_aplica = $ca;
        $this -> cod_filtro = $cf;
        $this -> cod_usuari = $cu;
    }

    function insertar($conexion, $transaccion, $cf)
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_filtro_usuari
                  VALUES ('" . $this -> cod_aplica . "',
		  	  '" . $this -> cod_filtro . "',
                          '" . $this -> cod_usuari . "',
                          '$cf')";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> clv_filtro = $cf;
    }

    function listar($conexion)
    {
        $query = "SELECT clv_filtro
                    FROM ".BASE_DATOS.".tab_aplica_filtro_usuari
                   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_filtro = '" . $this -> cod_filtro . "' AND
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

    function actualizar($conexion, $transaccion, $cf)
    {
        $query = "UPDATE ".BASE_DATOS.".tab_filtro_usuari
                     SET clv_filtro = '$cf'
		   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                         cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> clv_filtro = $cf;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_filtro_usuari
		   WHERE cod_aplica = '" . $this -> cod_aplica . "' AND
                   	 cod_filtro = '" . $this -> cod_filtro . "' AND
                         cod_usuari = '" . $this -> cod_usuari . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_aplica"] = $this -> cod_aplica;
        $datos["cod_filtro"] = $this -> cod_filtro;
        $datos["cod_usuari"] = $this -> cod_usuari;
        $datos["clv_filtro"] = $this -> clv_filtro;

        return $datos;
    }
}
/* Fin Archivo */
?>
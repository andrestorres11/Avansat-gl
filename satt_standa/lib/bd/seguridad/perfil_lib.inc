<?php
/*Autor: Adrian Ignacio Cardona Mora
  Modificado por: Adrian Ignacio Cardona
  Ultima Actualización: 29-01-2004*/

//Esta clase manipula un registro en la tabla ".BASE_DATOS.".tab_genera_perfil
class Perfil
{
    //Atributos
    var $cod_perfil,
        $nom_perfil,
        $usr_creaci,
	    $fec_creaci,
	    $usr_modifi,
	    $fec_modifi;

    //Métodos
    function Perfil($co = 0)
    {
        $this -> cod_perfil = $co;
    }

    function insertar($conexion, $transaccion, $no, $uc, $fc)
    {
        $vale = 0;
    	if($this->cod_perfil != 0)
		{
            $query = "SELECT cod_perfil
                      FROM tab_genera_perfil
                      WHERE cod_perfil = '".$this->cod_perfil."'";
            $consulta = new Consulta($query, $conexion);
            if(!$consulta->ret_num_rows())
                $vale = 1;
	    }
	    if($vale == 0)
	    {
	        $query = "SELECT MAX(cod_perfil) AS max_codigo
                	    FROM ".BASE_DATOS.".tab_genera_perfil";
        	$consulta = new Consulta($query, $conexion);
        	if($consulta -> ret_num_rows())
        	{
                $max_codigo = $consulta -> ret_arreglo();
                $this -> cod_perfil = $max_codigo["max_codigo"] + 1;
        	}
        	else
                $this -> cod_perfil = 1;
		}
		$query = "INSERT INTO ".BASE_DATOS.".tab_genera_perfil
                  VALUES ('".$this -> cod_perfil."',
                          '$no',
                          '$uc',
        	              '$fc',
	                      NULL,
	                      NULL)";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_perfil = $no;
        $this -> usr_creaci = $uc;
		$this -> fec_creaci = $fc;
    }

    function listar($conexion)
    {
        $query = "SELECT nom_perfil,
    	                 usr_creado,
	                     fec_creaci,
	                     usr_modifi,
	                     fec_modifi
                    FROM ".BASE_DATOS.".tab_genera_perfil
                   WHERE cod_perfil = '" . $this -> cod_perfil . "'";
        $consulta = new Consulta($query, $conexion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> nom_perfil = $datos["nom_perfil"];
	        $this -> usr_creaci = $datos["usr_creado"];
	        $this -> fec_creaci = $datos["fec_creaci"];
	        $this -> usr_modifi = $datos["usr_modifi"];
	        $this -> fec_modifi = $datos["fec_modifi"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $no, $um, $fm)
    {
        $query = "UPDATE ".BASE_DATOS.".tab_genera_perfil
                     SET nom_perfil = '$no',
			     	 usr_modifi = '$um',
					 fec_modifi = '$fm'
                   WHERE cod_perfil = '" . $this -> cod_perfil . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> nom_perfil = $no;
	    $this -> usr_modifi = $um;
	    $this -> fec_modifi = $fm;
    }

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM ".BASE_DATOS.".tab_genera_perfil
                   WHERE cod_perfil = '" . $this -> cod_perfil . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["cod_perfil"] = $this -> cod_perfil;
        $datos["nom_perfil"] = $this -> nom_perfil;
        $datos["usr_creado"] = $this -> usr_creaci;
	    $datos["fec_creaci"] = $this -> fec_creaci;
	    $datos["usr_modifi"] = $this -> usr_modifi;
	    $datos["fec_modifi"] = $this -> fec_modifi;
        return $datos;
    }
}
/* Final Archivo */
?>
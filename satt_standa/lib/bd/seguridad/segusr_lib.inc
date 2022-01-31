<?php
/*Autor: Adrian ignacio Cardona
  Modificado por: Adrian Ignacio Cardona
  Ultima Actualización: 29-01-2004*/

//Esta clase manipula un registro en la tabla tab_seguim_usuari
class Segusr
{
    //Atributos
    var $fec_operac,
        $usr_operac,
        $nom_tablax,
        $cod_regist,
        $nom_operac;

    //Métodos
    function Segusr($fo = '0', $nt = '', $cr = '0', $no = 'L')
    {
        $this -> fec_operac = $fo;
        $this -> nom_tablax = $nt;
        $this -> cod_regist = $cr;
        $this -> nom_operac = $no;
    }

    function insertar($conexion, $transaccion, $uo)
    {
        $query = "INSERT INTO tab_seguim_usuari
                  VALUES ('" . $this -> fec_operac . "',
                  		  '$uo',
                          '" . $this -> nom_tablax . "',
                          '" . $this -> cod_regist . "',
                          '" . $this -> nom_operac . "')";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> usr_operac = $uo;
    }

    function listar($conexion)
    {
        $query = "SELECT usr_operac
                    FROM tab_seguim_usuari
                   WHERE fec_operac = '" . $this -> fec_operac . "'
                   AND	 nom_tablax = '" . $this -> nom_tablax . "'
                   AND	 cod_regist = '" . $this -> cod_regist . "'
                   AND   nom_operac = '" . $this -> nom_operac . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $num_filas = $consulta -> ret_num_rows();
        if($num_filas)
        {
            $datos = $consulta -> ret_arreglo();
            $this -> usr_operac = $datos["usr_operac"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $uo)
    {
        $query = "UPDATE tab_seguim_usuari
                     SET usr_operac = '$uo'
                   WHERE fec_operac = '" . $this -> fec_operac . "'
                   AND	 nom_tablax = '" . $this -> nom_tablax . "'
                   AND	 cod_regist = '" . $this -> cod_regist . "'
                   AND   nom_operac = '" . $this -> nom_operac . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
        $this -> usr_operac = $uo;
	}

    function eliminar($conexion, $transaccion)
    {
        $query = "DELETE
                    FROM tab_seguim_usuari
                   WHERE fec_operac = '" . $this -> fec_operac . "'
                   AND	 nom_tablax = '" . $this -> nom_tablax . "'
                   AND	 cod_regist = '" . $this -> cod_regist . "'
                   AND   nom_operac = '" . $this -> nom_operac . "'";
        $consulta = new Consulta($query, $conexion, $transaccion);
    }

    function retornar()
    {
        $datos["fec_operac"] = $this -> fec_operac;
        $datos["usr_operac"] = $this -> usr_operac;
        $datos["nom_tablax"] = $this -> nom_tablax;
        $datos["cod_regist"] = $this -> cod_regist;
        $datos["nom_operac"] = $this -> nom_operac;
        return $datos;
    }
}
/* Final Archivo */
?>
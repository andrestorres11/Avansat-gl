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
        $cod_inicio,
        $cod_grupox,
        $ind_estado,
        $cod_consec;

    //Metodos
    function __construct($co)
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
                         cod_inicio,
                         cod_grupox,
                         ind_estado,
                         cod_consec
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
            $this -> cod_grupox = $datos["cod_grupox"];
            $this -> ind_estado = $datos["ind_estado"];
            $this -> cod_consec = $datos["cod_consec"];
        }
        return $num_filas;
    }

    function actualizar($conexion, $transaccion, $cl, $no, $ue, $cp, $ci, $ce)
    {
      if(!$ue) $ue = 'null'; else  $ue = "'$ue'";                  
      if(!$cp) $cp = 'null'; else $cp = "'$cp'";                  
      if(!$ci) $ci = 'null'; else $ci = "'$ci'";
      if(!$ce) $ce = 'null'; else $ce = "'$ce'";
                  
                  
      $query = "UPDATE ".BASE_DATOS.".tab_genera_usuari
                   SET clv_usuari = '$cl',
                       nom_usuari = '$no',
                       usr_emailx = $ue,
                       cod_perfil = $cp,
                       cod_inicio = $ci,
                       num_cedula = $ce
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
        $datos["cod_grupox"] = $this -> cod_grupox;
        $datos["ind_estado"] = $this -> ind_estado;
        $datos["cod_consec"] = $this -> cod_consec;
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

    function activo()//Función para averiguar el estado del usuario
    {
      return $this->ind_estado;
    }
}
/* Fin Archivo */
?>
<?php
function online($cookie, $dbi)
{
    cookiedecode($cookie, $dbi);
    $ip = getenv("REMOTE_ADDR");
    $cod_usuari = $cookie["cod_usuari"];
    $invitado = 0;
    if (!isset($cod_usuari))
    {
        $cod_usuari = "$ip";
        $invitado = 1;
    }
    $query = "SELECT tiempo FROM ".BASE_DATOS.".tab_usuari_session WHERE cod_usuari='$cod_usuari' AND host_addr='$ip'";
    $consulta = new Consulta($query, $dbi);
    $tenlinea = time();
    if ($row = $consulta -> ret_arreglo())
    {
        $query = "UPDATE ".BASE_DATOS.".tab_usuari_session SET tiempo='$tenlinea', invitado='$invitado' WHERE cod_usuari='$cod_usuari' AND host_addr='$ip'";
        $consulta = new Consulta($query, $dbi);
    }
    else
    {
        $query = "INSERT INTO ".BASE_DATOS.".tab_usuari_session VALUES ('$cod_usuari', '$tenlinea', '$ip', '$invitado');";
        $consulta = new Consulta($query, $dbi);
        $query = "INSERT INTO ".BASE_DATOS.".tab_seguim_usuari VALUES ('".date("Y-m-d H:i:s")."', '$cod_usuari', 'tab_usuari_session', '$ip', 'S')";
        $consulta = new Consulta($query, $dbi);
    }
}

function offline($cookie, $dbi)
{
    cookiedecode($cookie, $dbi);
    $cod_usuari = $cookie["cod_usuari"];
    $ip = getenv("REMOTE_ADDR");
    $query = "DELETE FROM ".BASE_DATOS.".tab_usuari_session WHERE cod_usuari='$cod_usuari' AND host_addr='$ip'";
    //$consulta = new Consulta($query, $dbi);
        $query = "INSERT INTO ".BASE_DATOS.".tab_seguim_usuari VALUES ('".date("Y-m-d H:i:s")."', '$cod_usuari', 'tab_usuari_session', '$ip', 'F')";
    //$consulta = new Consulta($query, $dbi);
}

function cookiedecode($cookie, $dbi)
{
    $query = "select clv_usuari from ".BASE_DATOS.".tab_genera_usuari where cod_usuari='".$cookie["cod_usuari"]."'";
    $consulta = new Consulta($query, $dbi);
    list($pass) = $consulta -> ret_vector();
    if ($cookie["clv_usuari"] == $pass && $pass != "")
        return $cookie;
    else
        unset($cookie);
}
?>
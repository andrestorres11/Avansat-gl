<?php
class Proc_paisxx
{
        var $conexion,
            $usuario,
            $cod_aplica;
        //Metodos
        function __construct($co, $us, $ca)
        {
                $this -> conexion = $co;
                $this -> usuario = $us;
                $this -> cod_aplica = $ca;
                $this -> principal();
        }
//********METODOS DE LA CLASE PROC_LISTA DE PRECIOS ESTANDAR*************
        function principal()
        {
                $this -> Paisxx();
        }//FIN FUNCION PRINCIPAL
// *****************************************************
//FUNCION VISUALIZAR LOS CLIENTES
// *****************************************************
        function Paisxx()
        {
                $query = "SELECT a.cod_paisxx, a.nom_paisxx
                            FROM ".BASE_DATOS.".tab_genera_paises a ";
                $con_paises = new Consulta($query, $this -> conexion);
                $paises = $con_paises -> ret_matriz();
                $formulario = new Formulario ("index.php","post","LISTADO DE PAISES","form_lis_paises");
                $formulario -> nueva_tabla();
                if(sizeof($paises) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Nombre",1);
                        for($i=0;$i<sizeof($paises);$i++)
                        {
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda2\">".$paises[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$paises[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda\">".$paises[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$paises[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                $formulario -> cerrar();
        }

// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_paisxx($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

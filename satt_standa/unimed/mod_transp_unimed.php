<?php
class Proc_unimed
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
                $this -> Activi();
        }//FIN FUNCION PRINCIPAL
// *****************************************************
//FUNCION PRINCIPAL DEL VINCULACION
// *****************************************************
        function Activi()
        {
                $query = "SELECT a.cod_unimed, a.nom_unimed
                            FROM ".BASE_DATOS.".tab_genera_unimed a ";
                $con_unimed = new Consulta($query, $this -> conexion);
                $unimed = $con_unimed -> ret_matriz();
                $formulario = new Formulario ("index.php","post","LISTADO DE UNIDADES DE MEDIDA","form_lis_unimed");
                $formulario -> nueva_tabla();
                if(sizeof($unimed) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Nombre",1);
                        for($i=0;$i<sizeof($unimed);$i++)
                        {
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda2\">".$unimed[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$unimed[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda\">".$unimed[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$unimed[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                $formulario -> nueva_tabla();
                $formulario -> cerrar();
                echo "<br />";
        }

// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_unimed($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

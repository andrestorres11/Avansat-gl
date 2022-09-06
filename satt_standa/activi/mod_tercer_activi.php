<?php
class Proc_activi
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
                if(!isset($_REQUEST[opcion]))
                        $this -> Activi();
                else
                {
                        switch($_REQUEST[opcion])
                        {
                                case "4":
                                $this -> Cambiar_Estado();
                                break;
                        }//FIN SWITCH
                }// FIN ELSE GLOBALS OPCION
         }//FIN FUNCION PRINCIPAL
// *****************************************************
//FUNCION PRINCIPAL DEL VINCULACION
// *****************************************************
        function Activi()
        {
                $query = "SELECT a.cod_activi, a.nom_activi, a.ind_estado
                            FROM ".BASE_DATOS.".tab_genera_activi a ";
                $con_activi = new Consulta($query, $this -> conexion);
                $activi = $con_activi -> ret_matriz();
                $formulario = new Formulario ("index.php","post","LISTADO DE ACTIVIDADES DE TERCEROS","form_lis_activi");
                $formulario -> nueva_tabla();
                if(sizeof($activi) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Nombre",0);
                        $formulario -> linea("Opciones",1);
                        for($i=0;$i<sizeof($activi);$i++)
                        {
                                if($activi[$i][2] == "1")
                                        $texto = "Desactivar";
                                else
                                        $texto = "Activar";
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda2\">".$activi[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$activi[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=4&cod_activi=".$activi[$i][0]."&ind_estado=".$activi[$i][2]."\">$texto</a></td></tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda\">".$activi[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$activi[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=4&cod_activi=".$activi[$i][0]."&ind_estado=".$activi[$i][2]."\">$texto</a></td></tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                $formulario -> nueva_tabla();
                $formulario -> cerrar();
                echo "<br />";
                //$this -> form_activi();
        }

// *****************************************************
//FUNCION CAMBIAR ESTADO DE LA CARROCERIA
// *****************************************************
function Cambiar_Estado()
{
        if(!isset($_GET['cod_activi']))
                die("No se puede cambiar el estado.");
        if($_GET['ind_estado'] == 1)
        {
                $new_ind = 0;
                $men = "La Actividad No. ".$_GET['cod_activi']." ha cambiado de estado a <strong>Inactiva</strong>";
        }
        elseif($_GET['ind_estado'] == 0)
        {
                $new_ind = 1;
                $men = "La Actividad No. ".$_GET['cod_activi']." ha cambiado de estado a <strong>Activa</strong>";
        }
        else
                die("No se puede cambiar el estado.");
        $query = "UPDATE ".BASE_DATOS.".tab_genera_activi SET ind_estado='".$new_ind."' WHERE cod_activi ='".$_GET['cod_activi']."'";
        $con_cambiar_estado = new Consulta($query, $this -> conexion);
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Activi();
}//FIN FUNCION CAMBIAR ESTADO


// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_activi($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

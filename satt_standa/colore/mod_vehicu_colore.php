<?php
class Proc_colore
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
                        $this -> Colore();
                else
                {
                        switch($_REQUEST[opcion])
                        {
                                case "5":
                                $this -> Mostrar();
                                break;
                        }//FIN SWITCH
                }// FIN ELSE GLOBALS OPCION
         }//FIN FUNCION PRINCIPAL

// *****************************************************
//FUNCION PRincipal
// *****************************************************
        function Colore()
        {
                $this -> form_colore();
        }


// *****************************************************
//FUNCION MOSTRAR CIUDADES
// *****************************************************
        function Mostrar()
        {
                $query = "SELECT a.cod_colorx,a.nom_colorx
                            FROM ".BASE_DATOS.".tab_vehige_colore a
                           WHERE a.nom_colorx LIKE '%$_REQUEST[colore]%'";
                $con_colore = new Consulta($query, $this -> conexion);
                $colore = $con_colore -> ret_matriz();
                $this -> form_colore();
                echo "<br />";
                $formulario = new Formulario ("index.php","post","LISTADO DE COLORES DE VEH&Iacute;CULOS","form_lis_colore");
                $formulario -> nueva_tabla();
                if(sizeof($colore) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Color",1);
                        for($i=0;$i<sizeof($colore);$i++)
                        {
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda\">".$colore[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$colore[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda2\">".$colore[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$colore[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                else
                {
                        echo "<td class=\"celda2\"><div align=\"center\">No se encontr&oacute; ninguna coincidencia.</div></td>";
                }
                $formulario -> nueva_tabla();
                $formulario -> cerrar();
        }
// *****************************************************
//FUNCION MOSTRAR FORMULARIO DE BUSQUEDA DE COLORES
// *****************************************************

        function form_colore()
        {
                        $formulario = new Formulario ("index.php","post","Buscar Colores","form_list");
                        $formulario -> linea("Inserte un texto Para Iniciar la Busqueda",1);
                        $formulario -> nueva_tabla();
                        $formulario -> texto ("Texto","text","colore",1,50,255,"","");
                        $formulario -> nueva_tabla();
                        $formulario -> oculto("opcion",5,0);
                        $formulario -> oculto("window","central",0);
                        $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
                        $formulario -> botoni("Buscar","if(form_list.colore.value == '') alert('Debe ingresar un texto para la busqueda.'); else form_list.submit();",1);
                        $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_colore($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
<?php
class Proc_marcas
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
                if(!isset($GLOBALS[opcion]))
                        $this -> Marcas();
                else
                {
                        switch($GLOBALS[opcion])
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
        function Marcas()
        {
                $this -> form_marcas();
        }


// *****************************************************
//FUNCION MOSTRAR CIUDADES
// *****************************************************
        function Mostrar()
        {
                $query = "SELECT a.cod_marcax,a.nom_marcax
                            FROM ".BASE_DATOS.".tab_genera_marcas a
                           WHERE a.nom_marcax LIKE '%$GLOBALS[marcas]%'";
                $con_marcas = new Consulta($query, $this -> conexion);
                $marcas = $con_marcas -> ret_matriz();
                $this -> form_marcas();
                echo "<br />";
                $formulario = new Formulario ("index.php","post","LISTADO DE MARCAS DE VEH&Iacute;CULOS","form_lis_marcas");
                $formulario -> nueva_tabla();
                if(sizeof($marcas) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Marca",1);
                        for($i=0;$i<sizeof($marcas);$i++)
                        {
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda\">".$marcas[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$marcas[$i][1]."</td>";
                                        echo "</tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda2\">".$marcas[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$marcas[$i][1]."</td>";
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
//FUNCION MOSTRAR FORMULARIO DE BUSQUEDA DE CIUDADES
// *****************************************************

        function form_marcas()
        {
                        $formulario = new Formulario ("index.php","post","Buscar Marcas de Veh&iacute;culos","form_list");
                        $formulario -> linea("Inserte un texto Para Iniciar la Busqueda",1);
                        $formulario -> nueva_tabla();
                        $formulario -> texto ("Texto","text","marcas",1,50,255,"","");
                        $formulario -> nueva_tabla();
                        $formulario -> oculto("opcion",5,0);
                        $formulario -> oculto("window","central",0);
                        $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
                        $formulario -> botoni("Buscar","if(form_list.marcas.value == '') alert('Debe ingresar un texto para la busqueda.'); else form_list.submit();",1);
                        $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION CAMBIAR ESTADO DE LA OBSERVACiÖN
// *****************************************************
// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_marcas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
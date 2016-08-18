<?php
class Proc_depart
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
                        $this -> Depart();
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
        function Depart()
        {
                $this -> form_depart();
        }


// *****************************************************
//FUNCION MOSTRAR CIUDADES
// *****************************************************
        function Mostrar()
        {
                $query = "SELECT a.cod_depart,a.nom_depart,a.abr_depart,b.abr_paisxx
                            FROM ".BASE_DATOS.".tab_genera_depart a,".BASE_DATOS.".tab_genera_paises b
                           WHERE a.cod_paisxx = b.cod_paisxx AND
                                 a.nom_depart LIKE '%$GLOBALS[depart]%'
                        ORDER BY 4,2";
                $con_depart = new Consulta($query, $this -> conexion);
                $depart = $con_depart -> ret_matriz();
                $this -> form_depart();
                echo "<br />";
                $formulario = new Formulario ("index.php","post","LISTADO DE DEPARTAMENTOS","form_lis_depart");
                $formulario -> nueva_tabla();
                if(sizeof($depart) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Departamento",0);
                        $formulario -> linea("Abreviatura Departamento",0);
                        $formulario -> linea("Pa&iacute;s",1);
                        for($i=0;$i<sizeof($depart);$i++)
                        {
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda\">".$depart[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$depart[$i][1]."</td>";
                                        echo "<td class=\"celda\">".$depart[$i][2]."</td>";
                                        echo "<td class=\"celda\">".$depart[$i][3]."</td>";
                                        echo "</tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda2\">".$depart[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$depart[$i][1]."</td>";
                                        echo "<td class=\"celda2\">".$depart[$i][2]."</td>";
                                        echo "<td class=\"celda2\">".$depart[$i][3]."</td>";
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

        function form_depart()
        {
                        $formulario = new Formulario ("index.php","post","Buscar Departamentos","form_list");
                        $formulario -> linea("Inserte un texto Para Iniciar la Busqueda",1);
                        $formulario -> nueva_tabla();
                        $formulario -> texto ("Texto","text","depart",1,50,255,"","");
                        $formulario -> nueva_tabla();
                        $formulario -> oculto("opcion",5,0);
                        $formulario -> oculto("window","central",0);
                        $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
                        $formulario -> botoni("Buscar","if(form_list.depart.value == '') alert('Debe ingresar un texto para la busqueda.'); else form_list.submit();",1);
                        $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION CAMBIAR ESTADO DE LA OBSERVACiÖN
// *****************************************************
// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_depart($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
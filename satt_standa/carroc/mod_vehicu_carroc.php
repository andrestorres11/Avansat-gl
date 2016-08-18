<?php
class Proc_carroc
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
                        $this -> Carroc();
                else
                {
                        switch($GLOBALS[opcion])
                        {
                                //case "1":
                                //$this -> Insertar();
                                //break;
                                case "2":
                                $this -> Actualizar();
                                break;
                                //case "3":
                                //$this -> Eliminar();
                                //break;
                                case "4":
                                $this -> Cambiar_Estado();
                                break;
                                case "5":
                                $this -> Editar();
                                break;
                        }//FIN SWITCH
                }// FIN ELSE GLOBALS OPCION
         }//FIN FUNCION PRINCIPAL
// *****************************************************
//FUNCION PRINCIPAL DEL VINCULACION
// *****************************************************
        function Carroc()
        {
                $query = "SELECT a.cod_carroc, a.nom_carroc, a.ind_estado
                            FROM ".BASE_DATOS.".tab_vehige_carroc a ";
                $con_carroc = new Consulta($query, $this -> conexion);
                $carroc = $con_carroc -> ret_matriz();
                $formulario = new Formulario ("index.php","post","LISTADO DE CARROCERIAS DE VEH&Iacute;CULOS","form_lis_carroc");
                $formulario -> nueva_tabla();
                if(sizeof($carroc) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Nombre",0);
                        $formulario -> linea("Opciones",1);
                        for($i=0;$i<sizeof($carroc);$i++)
                        {
                                if($carroc[$i][2] == "1")
                                        $texto = "Desactivar";
                                else
                                        $texto = "Activar";
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda2\">".$carroc[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$carroc[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=4&cod_carroc=".$carroc[$i][0]."&ind_estado=".$carroc[$i][2]."\">$texto</a> - <a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=5&cod_carroc=".$carroc[$i][0]."\">Actualizar</a></td></tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda\">".$carroc[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$carroc[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=4&cod_carroc=".$carroc[$i][0]."&ind_estado=".$carroc[$i][2]."\">$texto</a> - <a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=5&cod_carroc=".$carroc[$i][0]."\">Actualizar</a></td></tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                $formulario -> nueva_tabla();
                $formulario -> cerrar();
                echo "<br />";
                //$this -> form_carroc();
        }

        function form_carroc($ac="i")
        {
                if($ac == "e")
                {
                        $op = 2;
                        $tit = "ACTUALIZAR UNA CARROCERIA DE VEH&Iacute;CULO";
                        $query = "SELECT a.cod_carroc, a.nom_carroc, a.ind_estado
                                    FROM ".BASE_DATOS.".tab_vehige_carroc a
                                   WHERE a.cod_carroc=".$_GET['cod_carroc']."";
                        $con_carroc = new Consulta($query, $this -> conexion);
                        $car = $con_carroc -> ret_matriz();
                        $ind = $car[0]['ind_estado'];
                }
                else
                {
                        $op = 1;
                        $tit = "AGREGAR UNA CARROCERIA DE VEH&Iacute;CULO";
                        $ind = 1;
                }
                // FORMULARIO PARA AGREGAR UNA CARROCERIA DE VEH&Iacute;CULO
                echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/carroc.js\"></script>\n";
                $formulario = new Formulario ("index.php","post",$tit,"form_carroc");
                $formulario -> texto ("Nombre de la Carroceria","text","nom_carroc",1,100,255,"","".$car[0]['nom_carroc']."");
                $formulario -> caja("Activar la Carroceria","ind_estado",$ind,1);
                $formulario -> nueva_tabla();
                $formulario -> oculto("opcion",$op,0);
                $formulario -> oculto("cod_carroc",$car[0]['cod_carroc'],0);
                $formulario -> oculto("window","central",0);
                $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
                $formulario -> botoni("Aceptar","aceptar_form()",0);
                $formulario -> botoni("Borrar","form_carroc.reset()",1);
                $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION CAMBIAR ESTADO DE LA CARROCERIA
// *****************************************************
function Cambiar_Estado()
{
        if(!isset($_GET['cod_carroc']))
                die("No se puede cambiar el estado.");
        if($_GET['ind_estado'] == 1)
        {
                $new_ind = 0;
                $men = "La Carroceria No. ".$_GET['cod_carroc']." ha cambiado de estado a <strong>Inactiva</strong>";
        }
        elseif($_GET['ind_estado'] == 0)
        {
                $new_ind = 1;
                $men = "La Carroceria No. ".$_GET['cod_carroc']." ha cambiado de estado a <strong>Activa</strong>";
        }
        else
                die("No se puede cambiar el estado.");
        $query = "UPDATE ".BASE_DATOS.".tab_vehige_carroc SET ind_estado='".$new_ind."' WHERE cod_carroc ='".$_GET['cod_carroc']."'";
        $con_cambiar_estado = new Consulta($query, $this -> conexion);
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Carroc();
}//FIN FUNCION CAMBIAR ESTADO

// *****************************************************
//FUNCION INSERTAR UNA CARROCERIA
// *****************************************************
/*function Insertar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        if($_POST['nom_carroc'] == "")
        die("No se pudo completar la inserci&oacute;n, vuelva a intentarlo.");
        if($_POST['ind_estado'] !=  1)
                $ind = 0;
        else
                $ind = 1;
        $fec_actual = date("Y-m-d H:i:s");
        //ultimo despacho
        $query = "SELECT Max(cod_carroc)
                    FROM ".BASE_DATOS.".tab_vehige_carroc";
        $consec = new Consulta($query, $this -> conexion);
        $ultimo = $consec -> ret_matriz();
        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec+1;
        //query de insercion del carroc
        $query = "INSERT INTO ".BASE_DATOS.".tab_vehige_carroc
                       VALUES ('$nuevo_consec','".$_POST['nom_carroc']."','$ind',
                               '$usuario','$fec_actual','$usuario','$fec_actual')";
        $insercion = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Vinculaci&oacute;n fu&eacute; agregado con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION INSERTAR
*/
// *****************************************************
//FUNCION ELIMINAR TIPO DE VINCULACI&Oacute;N
// *****************************************************
/*function Eliminar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        //query de eliminación del carroc
        $query = "DELETE FROM ".BASE_DATOS.".tab_vehige_carroc
                             WHERE cod_carroc='".$_GET['cod_carroc']."'";
        $eliminar = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Vinculaci&oacute;n fu&eacute; eliminado con exito con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION ELIMINAR
*/
// *****************************************************
//FUNCION EDITAR TIPO DE VINCULACI&Oacute;N
// *****************************************************
function Editar()
{
        $this -> form_carroc("e");
}//FIN FUNCION ELIMINAR

// *****************************************************
//FUNCION ASCTUALIZAR
// *****************************************************
function Actualizar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        if($_POST['nom_carroc'] == "")
        die("No se pudo completar la inserci&oacute;n, vuelva a intentarlo.");
        if($_POST['ind_estado'] !=  1)
                $ind = 0;
        else
                $ind = 1;
        $fec_actual = date("Y-m-d H:i:s");
        //query de insercion de carroc
        $query = "UPDATE ".BASE_DATOS.".tab_vehige_carroc SET nom_carroc='".$_POST['nom_carroc']."', ind_estado='".$ind."', usr_modifi='".$usuario."', fec_modifi='".$fec_actual."' WHERE cod_carroc ='".$_POST['cod_carroc']."'";
        $act = new Consulta($query, $this -> conexion);
        $men = "La Carroceria fu&eacute; actualizado con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Carroc();
}//FIN FUNCION ACTUALIZAR

// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_carroc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

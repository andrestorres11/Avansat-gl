<?php
class Proc_tipser
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
                        $this -> Tipser();
                else
                {
                        switch($GLOBALS[opcion])
                        {
                                case "1":
                                $this -> Insertar();
                                break;
                                case "2":
                                $this -> Actualizar();
                                break;
                                case "3":
                                $this -> Eliminar();
                                break;
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
//FUNCION PRINCIPAL DEL SERVICIO
// *****************************************************
        function Tipser()
        {
                $query = "SELECT a.cod_tipser, a.nom_tipser, a.ind_estado
                            FROM ".BASE_DATOS.".tab_genera_tipser a ";
                $con_tipser = new Consulta($query, $this -> conexion);
                $tipser = $con_tipser -> ret_matriz();
                $formulario = new Formulario ("index.php","post","LISTADO DE TIPOS DE SERVICIO","form_lis_tipser");
                $formulario -> nueva_tabla();
                if(sizeof($tipser) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Descriptci&oacute;n",0);
                        $formulario -> linea("Opciones",1);
                        for($i=0;$i<sizeof($tipser);$i++)
                        {
                                if($tipser[$i][2] == "1")
                                        $texto = "Desactivar";
                                else
                                        $texto = "Activar";
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda2\">".$tipser[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$tipser[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=4&cod_tipser=".$tipser[$i][0]."&ind_estado=".$tipser[$i][2]."\">$texto</a> - <a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=5&cod_tipser=".$tipser[$i][0]."\">Actualizar</a> - <a href=\"#\" onClick=\"if(confirm('Esta seguro de que desea eliminar el tipo de servicio No. ".$tipser[$i][0]." ?')){ location.reload('index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=3&cod_tipser=".$tipser[$i][0]."');/*document.form_lis_tipser.opcion.value=3; document.form_lis_tipser.submit();*/}; return false;\">Eliminar</a></td></tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda\">".$tipser[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$tipser[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=4&cod_tipser=".$tipser[$i][0]."&ind_estado=".$tipser[$i][2]."\">$texto</a> - <a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=5&cod_tipser=".$tipser[$i][0]."\">Actualizar</a> - <a href=\"#\" onClick=\"if(confirm('Esta seguro de que desea eliminar el tipo de servicio No. ".$tipser[$i][0]." ?')){ location.reload('index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=3&cod_tipser=".$tipser[$i][0]."');/*document.form_lis_tipser.opcion.value=3; document.form_lis_tipser.submit();*/}; return false;\">Eliminar</a></td></tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                $formulario -> nueva_tabla();
                $formulario -> cerrar();
                echo "<br />";
                $this -> form_tipser();
        }

        function form_tipser($ac="i")
        {
                if($ac == "e")
                {
                        $op = 2;
                        $tit = "ACTUALIZAR UN TIPO DE SERVICIO";
                        $query = "SELECT a.cod_tipser, a.nom_tipser, a.ind_estado
                                    FROM ".BASE_DATOS.".tab_genera_tipser a
                                   WHERE a.cod_tipser=".$_GET['cod_tipser']."";
                        $con_tipser = new Consulta($query, $this -> conexion);
                        $tip = $con_tipser -> ret_matriz();
                        $ind = $tip[0]['ind_estado'];
                }
                else
                {
                        $op = 1;
                        $tit = "AGREGAR UN TIPO DE SERVICIO";
                        $ind = 1;
                }
                // FORMULARIO PARA AGREGAR UN TIPO DE SERVICIO
                echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tipser.js\"></script>\n";
                $formulario = new Formulario ("index.php","post",$tit,"form_tipser");
                $formulario -> texto ("Nombre del Tipo de Servicio","text","nom_tipser",1,100,255,"","".$tip[0]['nom_tipser']."");
                $formulario -> caja("Activar el Tipo de Servicio","ind_estado",$ind,1);
                $formulario -> nueva_tabla();
                $formulario -> oculto("opcion",$op,0);
                $formulario -> oculto("cod_tipser",$tip[0]['cod_tipser'],0);
                $formulario -> oculto("window","central",0);
                $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
                $formulario -> botoni("Aceptar","aceptar_form()",0);
                $formulario -> botoni("Borrar","form_tipser.reset()",1);
                $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION CAMBIAR ESTADO DEL TIPO DE SERVICIO
// *****************************************************
function Cambiar_Estado()
{
        if(!isset($_GET['cod_tipser']))
                die("No se puede cambiar el estado.");
        if($_GET['ind_estado'] == 1)
        {
                $new_ind = 0;
                $men = "El tipo de servicio No. ".$_GET['cod_tipser']." ha cambiado de estado a <strong>Inactiva</strong>";
        }
        elseif($_GET['ind_estado'] == 0)
        {
                $new_ind = 1;
                $men = "El tipo de servicio No. ".$_GET['cod_tipser']." ha cambiado de estado a <strong>Activa</strong>";
        }
        else
                die("No se puede cambiar el estado.");
        $query = "UPDATE ".BASE_DATOS.".tab_genera_tipser SET ind_estado='".$new_ind."' WHERE cod_tipser ='".$_GET['cod_tipser']."'";
        $con_cambiar_estado = new Consulta($query, $this -> conexion);
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION CAMBIAR ESTADO

// *****************************************************
//FUNCION INSERTAR UN TIPO DE SERVICIO
// *****************************************************
function Insertar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";*/
        if($_POST['nom_tipser'] == "")
        die("No se pudo completar la inserci&oacute;n, vuelva a intentarlo.");
        if($_POST['ind_estado'] !=  1)
                $ind = 0;
        else
                $ind = 1;
        $fec_actual = date("Y-m-d H:i:s");
        //ultimo despacho
        $query = "SELECT Max(cod_tipser)
                    FROM ".BASE_DATOS.".tab_genera_tipser";
        $consec = new Consulta($query, $this -> conexion);
        $ultimo = $consec -> ret_matriz();
        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec+1;
        //query de insercion del tipser
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_tipser
                       VALUES ('$nuevo_consec','".$_POST['nom_tipser']."','$ind',
                               '$usuario','$fec_actual','$usuario','$fec_actual')";
        $insercion = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Servicio fu&eacute; agregado con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION INSERTAR

// *****************************************************
//FUNCION ELIMINAR TIPO DE SERVICIO
// *****************************************************
function Eliminar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        //query de eliminación del tipser
        $query = "DELETE FROM ".BASE_DATOS.".tab_genera_tipser
                             WHERE cod_tipser='".$_GET['cod_tipser']."'";
        $eliminar = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Servicio fu&eacute; eliminado con exito con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION ELIMINAR

// *****************************************************
//FUNCION EDITAR TIPO DE SERVICIO
// *****************************************************
function Editar()
{
        $this -> form_tipser("e");
}//FIN FUNCION ELIMINAR

// *****************************************************
//FUNCION ASCTUALIZAR
// *****************************************************
function Actualizar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        if($_POST['nom_tipser'] == "")
        die("No se pudo completar la inserci&oacute;n, vuelva a intentarlo.");
        if($_POST['ind_estado'] !=  1)
                $ind = 0;
        else
                $ind = 1;
        $fec_actual = date("Y-m-d H:i:s");
        //query de insercion de tipser
        $query = "UPDATE ".BASE_DATOS.".tab_genera_tipser SET nom_tipser='".$_POST['nom_tipser']."', ind_estado='".$ind."', usr_modifi='".$usuario."', fec_modifi='".$fec_actual."' WHERE cod_tipser ='".$_POST['cod_tipser']."'";
        $act = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Servicio fu&eacute; actualizado con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION ACTUALIZAR

// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_tipser($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

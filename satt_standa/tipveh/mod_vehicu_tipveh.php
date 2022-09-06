<?php
class Proc_tipveh
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
                        $this -> Tipser();
                else
                {
                        switch($_REQUEST[opcion])
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
//FUNCION PRINCIPAL DEL VINCULACION
// *****************************************************
        function Tipser()
        {
                $query = "SELECT a.cod_tipveh, a.nom_tipveh, a.ind_estado
                            FROM ".BASE_DATOS.".tab_genera_tipveh a ";
                $con_tipveh = new Consulta($query, $this -> conexion);
                $tipveh = $con_tipveh -> ret_matriz();
                $formulario = new Formulario ("index.php","post","LISTADO DE TIPOS DE VINCULACI&Oacute;N","form_lis_tipveh");
                $formulario -> nueva_tabla();
                if(sizeof($tipveh) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0);
                        $formulario -> linea("Descriptci&oacute;n",0);
                        $formulario -> linea("Opciones",1);
                        for($i=0;$i<sizeof($tipveh);$i++)
                        {
                                if($tipveh[$i][2] == "1")
                                        $texto = "Desactivar";
                                else
                                        $texto = "Activar";
                                if($i%2 == 0)
                                {
                                        echo "<td class=\"celda2\">".$tipveh[$i][0]."</td>";
                                        echo "<td class=\"celda2\">".$tipveh[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=4&cod_tipveh=".$tipveh[$i][0]."&ind_estado=".$tipveh[$i][2]."\">$texto</a> - <a href=\"index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=5&cod_tipveh=".$tipveh[$i][0]."\">Actualizar</a> - <a href=\"#\" onClick=\"if(confirm('Esta seguro de que desea eliminar el tipo de vinculaci&oacute;n No. ".$tipveh[$i][0]." ?')){ location.reload('index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=3&cod_tipveh=".$tipveh[$i][0]."');/*document.form_lis_tipveh.opcion.value=3; document.form_lis_tipveh.submit();*/}; return false;\">Eliminar</a></td></tr><tr>";
                                }//fin if
                                else
                                {
                                        echo "<td class=\"celda\">".$tipveh[$i][0]."</td>";
                                        echo "<td class=\"celda\">".$tipveh[$i][1]."</td>";
                                        echo "<td class=\"celda2\"><a href=\"index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=4&cod_tipveh=".$tipveh[$i][0]."&ind_estado=".$tipveh[$i][2]."\">$texto</a> - <a href=\"index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=5&cod_tipveh=".$tipveh[$i][0]."\">Actualizar</a> - <a href=\"#\" onClick=\"if(confirm('Esta seguro de que desea eliminar el tipo de vinculaci&oacute;n No. ".$tipveh[$i][0]." ?')){ location.reload('index.php?window=central&cod_servic=".$_REQUEST['cod_servic']."&opcion=3&cod_tipveh=".$tipveh[$i][0]."');/*document.form_lis_tipveh.opcion.value=3; document.form_lis_tipveh.submit();*/}; return false;\">Eliminar</a></td></tr><tr>";
                                }//fin else
                        }//fin for
                }//fin if
                $formulario -> nueva_tabla();
                $formulario -> cerrar();
                echo "<br />";
                $this -> form_tipveh();
        }

        function form_tipveh($ac="i")
        {
                if($ac == "e")
                {
                        $op = 2;
                        $tit = "ACTUALIZAR UN TIPO DE VINCULACI&Oacute;N";
                        $query = "SELECT a.cod_tipveh, a.nom_tipveh, a.ind_estado
                                    FROM ".BASE_DATOS.".tab_genera_tipveh a
                                   WHERE a.cod_tipveh=".$_GET['cod_tipveh']."";
                        $con_tipveh = new Consulta($query, $this -> conexion);
                        $tip = $con_tipveh -> ret_matriz();
                        $ind = $tip[0]['ind_estado'];
                }
                else
                {
                        $op = 1;
                        $tit = "AGREGAR UN TIPO DE VINCULACI&Oacute;N";
                        $ind = 1;
                }
                // FORMULARIO PARA AGREGAR UN TIPO DE VINCULACI&Oacute;N
                echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tipveh.js\"></script>\n";
                $formulario = new Formulario ("index.php","post",$tit,"form_tipveh");
                $formulario -> texto ("Nombre del Tipo de Vinculaci&oacute;n","text","nom_tipveh",1,100,255,"","".$tip[0]['nom_tipveh']."");
                $formulario -> caja("Activar el Tipo de Vinculaci&oacute;n","ind_estado",$ind,1);
                $formulario -> nueva_tabla();
                $formulario -> oculto("opcion",$op,0);
                $formulario -> oculto("cod_tipveh",$tip[0]['cod_tipveh'],0);
                $formulario -> oculto("window","central",0);
                $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
                $formulario -> botoni("Aceptar","aceptar_form()",0);
                $formulario -> botoni("Borrar","form_tipveh.reset()",1);
                $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION CAMBIAR ESTADO DEL TIPO DE VINCULACI&Oacute;N
// *****************************************************
function Cambiar_Estado()
{
        if(!isset($_GET['cod_tipveh']))
                die("No se puede cambiar el estado.");
        if($_GET['ind_estado'] == 1)
        {
                $new_ind = 0;
                $men = "El tipo de vinculaci&oacute;n No. ".$_GET['cod_tipveh']." ha cambiado de estado a <strong>Inactiva</strong>";
        }
        elseif($_GET['ind_estado'] == 0)
        {
                $new_ind = 1;
                $men = "El tipo de vinculaci&oacute;n No. ".$_GET['cod_tipveh']." ha cambiado de estado a <strong>Activa</strong>";
        }
        else
                die("No se puede cambiar el estado.");
        $query = "UPDATE ".BASE_DATOS.".tab_genera_tipveh SET ind_estado='".$new_ind."' WHERE cod_tipveh ='".$_GET['cod_tipveh']."'";
        $con_cambiar_estado = new Consulta($query, $this -> conexion);
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION CAMBIAR ESTADO

// *****************************************************
//FUNCION INSERTAR UN TIPO DE VINCULACI&Oacute;N
// *****************************************************
function Insertar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";*/
        if($_POST['nom_tipveh'] == "")
        die("No se pudo completar la inserci&oacute;n, vuelva a intentarlo.");
        if($_POST['ind_estado'] !=  1)
                $ind = 0;
        else
                $ind = 1;
        $fec_actual = date("Y-m-d H:i:s");
        //ultimo despacho
        $query = "SELECT Max(cod_tipveh)
                    FROM ".BASE_DATOS.".tab_genera_tipveh";
        $consec = new Consulta($query, $this -> conexion);
        $ultimo = $consec -> ret_matriz();
        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec+1;
        //query de insercion del tipveh
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_tipveh
                       VALUES ('$nuevo_consec','".$_POST['nom_tipveh']."','$ind',
                               '$usuario','$fec_actual','$usuario','$fec_actual')";
        $insercion = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Vinculaci&oacute;n fu&eacute; agregado con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION INSERTAR

// *****************************************************
//FUNCION ELIMINAR TIPO DE VINCULACI&Oacute;N
// *****************************************************
function Eliminar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        //query de eliminación del tipveh
        $query = "DELETE FROM ".BASE_DATOS.".tab_genera_tipveh
                             WHERE cod_tipveh='".$_GET['cod_tipveh']."'";
        $eliminar = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Vinculaci&oacute;n fu&eacute; eliminado con exito con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipser();
}//FIN FUNCION ELIMINAR

// *****************************************************
//FUNCION EDITAR TIPO DE VINCULACI&Oacute;N
// *****************************************************
function Editar()
{
        $this -> form_tipveh("e");
}//FIN FUNCION ELIMINAR

// *****************************************************
//FUNCION ASCTUALIZAR
// *****************************************************
function Actualizar()
{
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        if($_POST['nom_tipveh'] == "")
        die("No se pudo completar la inserci&oacute;n, vuelva a intentarlo.");
        if($_POST['ind_estado'] !=  1)
                $ind = 0;
        else
                $ind = 1;
        $fec_actual = date("Y-m-d H:i:s");
        //query de insercion de tipveh
        $query = "UPDATE ".BASE_DATOS.".tab_genera_tipveh SET nom_tipveh='".$_POST['nom_tipveh']."', ind_estado='".$ind."', usr_modifi='".$usuario."', fec_modifi='".$fec_actual."' WHERE cod_tipveh ='".$_POST['cod_tipveh']."'";
        $act = new Consulta($query, $this -> conexion);
        $men = "El Tipo de Vinculaci&oacute;n fu&eacute; actualizado con &eacute;xito.";
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"> $men<br /><br />";
        $this -> Tipveh();
}//FIN FUNCION ACTUALIZAR

// *********************************************************************************
}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_tipveh($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

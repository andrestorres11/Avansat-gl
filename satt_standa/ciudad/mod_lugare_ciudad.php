<?php
class Proc_ciudad
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
                        $this -> Ciudad();
                else
                {
                        switch($GLOBALS[opcion])
                        {
                                case "5":
                                $this -> Mostrar();
                                break;
                                case "4":
                                $this -> Cambiar_Estado();
                                break;
                                case "6":
                                $this -> Listar();
                                break;

                        }//FIN SWITCH
                }// FIN ELSE GLOBALS OPCION
         }//FIN FUNCION PRINCIPAL

// *****************************************************
//FUNCION PRincipal
// *****************************************************
        function Ciudad()
        {
                $this -> form_ciudad();
        }


// *****************************************************
//FUNCION MOSTRAR CIUDADES
// *****************************************************
        function Mostrar()
        {
                $query = "SELECT a.cod_ciudad,a.nom_ciudad,a.abr_ciudad,
                                 b.abr_depart,c.abr_paisxx, a.ind_estado
                            FROM ".BASE_DATOS.".tab_genera_ciudad a, ".BASE_DATOS.".tab_genera_depart b,
                                 ".BASE_DATOS.".tab_genera_paises c
                           WHERE a.cod_depart = b.cod_depart AND
                                 a.cod_paisxx = c.cod_paisxx AND
                                 a.nom_ciudad LIKE '%$GLOBALS[ciudad]%'
                        ORDER BY 5,2";
                $con_ciudad = new Consulta($query, $this -> conexion);
                $ciudad = $con_ciudad -> ret_matriz();
                $this -> form_ciudad();
                $formulario = new Formulario ("index.php","post","LISTADO DE CIUDADES","form_lis_ciudad");
                $formulario -> nueva_tabla();
                if(sizeof($ciudad) > 0)
                {
                        $formulario -> linea("C&oacute;digo",0,"t");
                        $formulario -> linea("Ciudad",0,"t");
                        $formulario -> linea("Abreviatura Ciudad",0,"t");
                        $formulario -> linea("Departamento",0,"t");
                        $formulario -> linea("Pa&iacute;s",0,"t");
                        $formulario -> linea("Opciones",1,"t");
                        for($i=0;$i<sizeof($ciudad);$i++)
                        {
                                if($ciudad[$i][5] == "1")
                                        $texto = "Desactivar";
                                else
                                        $texto = "Activar";
                                	$link = "<a href=\"index.php?window=central&cod_servic=".$GLOBALS['cod_servic']."&opcion=4&cod_ciudad=".$ciudad[$i][0]."&ind_permit=".$ciudad[$i][5]."\">$texto</a>";

                                        $formulario -> linea($ciudad[$i][0],0,"i");
                                        $formulario -> linea($ciudad[$i][1],0,"i");
                                        $formulario -> linea($ciudad[$i][2],0,"i");
                                        $formulario -> linea($ciudad[$i][3],0,"i");
                                        $formulario -> linea($ciudad[$i][4],0,"i");
                                        $formulario -> linea($link,1,"i");
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

        function form_ciudad()
        {
                        $formulario = new Formulario ("index.php","post","Buscar Ciudades","form_list");
                        $formulario -> linea("Inserte un texto Para Iniciar la Busqueda",1,"t2");
                        $formulario -> nueva_tabla();
                        $formulario -> texto ("Texto","text","ciudad",1,50,255,"","");
                        $formulario -> nueva_tabla();
                        $formulario -> oculto("opcion",5,0);
                        $formulario -> oculto("window","central",0);
                        $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
                        $formulario -> botoni("Buscar","if(form_list.ciudad.value == '') alert('Debe ingresar un texto para la busqueda.'); else form_list.submit();",1);
                        $formulario -> cerrar();
        }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION CAMBIAR ESTADO DE LA OBSERVACi÷N
// *****************************************************
function Cambiar_Estado()
{
        if(!isset($_GET['cod_ciudad']))
                die("No se puede cambiar el estado.");
        if($_GET['ind_permit'] == "1")
        {
                $new_ind = "2";
                $men = "La Ciudad No. ".$_GET['cod_ciudad']." ha cambiado de estado a <strong>Inactiva</strong>";
        }
        elseif($_GET['ind_permit'] == "2")
        {
                $new_ind = "1";
                $men = "La Ciudad No. ".$_GET['cod_ciudad']." ha cambiado de estado a <strong>Activa</strong>";
        }
        else
                die("No se puede cambiar el estado.");
        $query = "UPDATE ".BASE_DATOS.".tab_genera_ciudad SET ind_estado='".$new_ind."' WHERE cod_ciudad ='".$_GET['cod_ciudad']."'";
        $con_cambiar_estado = new Consulta($query, $this -> conexion,"BR");

    if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otra Ciudad</a></b>";

     $mensaje =  "Se Cambio el Estado de la Ciudad con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("CIUDADES",$mensaje);
    }
}//FIN FUNCION CAMBIAR ESTADO


function Listar()

 {

     $datos_usuario = $this -> usuario -> retornar();

     $usuario=$datos_usuario["cod_usuari"];

     $formulario = new Formulario ("index.php","post","Buscar y Seleccionar Ciudades","form_list");

     $formulario -> radio("Nombre de la Ciudad","fil",1,1,0);

     $formulario -> texto ("","text","ciudad",1,50,255,"","");

     $formulario -> radio("Todos","fil",2,0,1);


     $query = "SELECT a.cod_ciudad,a.abr_ciudad

                 FROM ".BASE_DATOS.".tab_genera_ciudad a

                WHERE a.ind_estado = '1'";

         if(($GLOBALS[fil]=='1') AND($GLOBALS[ciudad]!=''))

         $query = $query." AND a.nom_ciudad LIKE '%$GLOBALS[ciudad]%'";


     $query = $query." GROUP BY 2 ORDER BY 2 LIMIT 0,20";
     echo "<p></p>";

     $consulta = new Consulta($query, $this -> conexion);
       $consulta = new Consulta($query, $this -> conexion);

     if($matriz = $consulta -> ret_matriz())
           for($i=0;$i<sizeof($matriz);$i++)
                 $matriz[$i][0]= "<a href=# onClick=\"opener.document.forms[0].dest".$GLOBALS[codigo].".value='".$matriz[$i][0]."';opener.document.forms[0].abrdest".$GLOBALS[codigo].".value='".$matriz[$i][1]."'; top.close()\">".$matriz[$i][0]."</a>";

   $formulario -> nueva_tabla();
   $formulario -> botoni("Buscar","form_list.submit()",1);
   $formulario -> nueva_tabla();

   $formulario -> linea("<b>SE ENCONTRARON ".sizeof($matriz)." REGISTROS</b>",0);
   $formulario -> nueva_tabla();
   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Codigo",0);
   $formulario -> linea("Abreviatura",1);
   for($i=0;$i<sizeof($matriz);$i++)
   {
     if($i%2 == 0)
     {
      echo "<td class=\"celda2\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][1]."</td></tr><tr>";
     }//fin if
     else
     {
      echo "<td class=\"celda\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][1]."</td></tr><tr>";
     }//fin else
   }//fin for
   }//fin if
      $formulario -> nueva_tabla();
      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("opcion",6,0);
      $formulario -> oculto("codigo",$GLOBALS[codigo],0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
      $formulario -> cerrar();
 }



// *********************************************************************************
}//FIN CLASE
     $proceso = new Proc_ciudad($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
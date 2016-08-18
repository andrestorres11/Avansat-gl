<?php
/**
 * Actualizado por:			MIGUEL ANGEL GARCIA RIVERA
 * Fecha Actualización:	2012/10/16
 */

class Proc_usuari
{

    var $conexion,
        $cod_aplica,
        $usuario;

    function __construct($co, $us, $ca)
    {
        $this -> conexion = $co;
        $this -> usuario = $us;
        $this -> cod_aplica = $ca;

        if(!isset($GLOBALS[opcion]))
        {
            $this -> Buscar();  
        }
        else
        {
            switch($GLOBALS[opcion])
            {
                case 1:
                    $this -> Listado();
                    break;
                case 2:
                    $this -> Insertar();
                    break;
            }
        }
    }

    function Buscar()
    {
        $cod_perfil = $this -> GetPerfil();
        
        $formulario = new Formulario ("index.php","post","DESPACHOS","form_insert","","");
        $formulario -> linea("Ingrese los Criterios de Busqueda",1,"t2");

        $formulario -> nueva_tabla();
        $formulario -> texto("Usuario: ","text","cod_usuari\" id=\"cod_usuariID",1,6,6,"","");
        //$formulario -> texto("Perfil: ","text","cod_perfil\" id=\"cod_perfilID",1,10,11,"","");
        $formulario -> lista ("Perfil: ","cod_perfil\" id=\"cod_perfilID",$cod_perfil,1 );

        $formulario -> nueva_tabla();
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("opcion",1,0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> botoni("Buscar","form_insert.submit();",1);
        $formulario -> cerrar();
    }

    function GetPerfil()
    {
        $inicio[0][0] = 0;
        $inicio[0][1] = '-';
        $sql ="SELECT cod_perfil, nom_perfil 
                 FROM ".BASE_DATOS.".tab_genera_perfil ";
        $consulta = new Consulta($sql, $this -> conexion);
        $usuari = $consulta -> ret_matriz(); 
        return array_merge($inicio,$usuari); 
    }
    
    function Listado()
    {

        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/suspension.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";

        echo '
            <script type="text/javascript">
                jQuery(function($) { 

                    $( ".date" ).datepicker();      
                    $( ".time" ).timepicker({
                        timeFormat:"hh:mm",
                        showSecond: false
                    });

                    $.mask.definitions["A"]="[12]";
                    $.mask.definitions["M"]="[01]";
                    $.mask.definitions["D"]="[0123]";

                    $.mask.definitions["H"]="[012]";
                    $.mask.definitions["N"]="[012345]";
                    $.mask.definitions["n"]="[0123456789]";

                    $( ".date" ).mask("Annn-Mn-Dn");
                    $( ".time" ).mask("Hn:Nn");

                });
            </script>

            ';
            
        $datos_usuario = $this -> usuario -> retornar();

        $query = "SELECT a.cod_usuari,a.nom_usuari,a.usr_emailx,
                         if(a.cod_perfil IS NULL,'-',b.nom_perfil)
                    FROM ".BASE_DATOS.".tab_genera_usuari a 
               LEFT JOIN ".BASE_DATOS.".tab_genera_perfil b 
                      ON a.cod_perfil = b.cod_perfil 
                   WHERE a.ind_estado = '1' ";
        
        if($GLOBALS['cod_usuari'])
            $query .= " AND a.cod_usuari = '".$GLOBALS['cod_usuari']."'";
        
        if($GLOBALS['cod_perfil'])
            $query .= " AND a.cod_perfil = ".$GLOBALS['cod_perfil']."";
        
        $query .= " ORDER BY 1,3 ";
        $consulta = new Consulta($query, $this -> conexion);
        $matriz = $consulta -> ret_matriz();

        $formulario = new Formulario("index.php","post","LSITADO DE USUARIOS", "form_ins\" id=\"form_insID");

        $formulario -> nueva_tabla();
        $formulario -> linea("Se Econtro un Total de ".sizeof($matriz)." Usuario(s)",1,"t2");

        $formulario -> nueva_tabla();
        $formulario -> linea("Usuario",0,"t");
        $formulario -> linea("Nombre",0,"t");
        $formulario -> linea("Perfil",0,"t");
        $formulario -> linea("Suspensión",0,"t");
        $formulario -> linea("Fecha",0,"t");
        $formulario -> linea("Hora",1,"t");

        for($i = 0; $i < sizeof($matriz); $i++)
        {
            $mQueryx = "SELECT fec_suspen, hor_suspen
                          FROM ".BASE_DATOS.".tab_genera_suspen
                         WHERE cod_usuari = '".$matriz[$i][0]."' 
                           AND ind_suspen = 1 
                         LIMIT 1 ";
            $consulta = new Consulta($mQueryx, $this -> conexion);
            $datos_ds = $consulta -> ret_matriz('i'); 
            
            if(count($datos_ds)>0)
            {
                $checked = ' checked="checked" ';
                $val = 1;
                $fec = $datos_ds[0]['fec_suspen'];
                $hor = $datos_ds[0]['hor_suspen'];
            }
            else
            {
                $checked = '';
                $val = 0;
                $fec = date("Y-m-d");
                $hor = date("H:i");
            }
            $formulario -> linea("<input type='hidden' name='cod_usuari".$i."' id='cod_usuari".$i."ID' value='".$matriz[$i][0]."' />".$matriz[$i][0],0,"i");
            $formulario -> linea($matriz[$i][1],0,"i");
            $formulario -> linea($matriz[$i][3],0,"i");
            echo '<td align="center" class="celda_etiqueta"><input type="checkbox" '.$checked.' onclick="this.value = ( this.checked==true ?  1 : 0 ); " value="'.$val.'" id="ind_suspen'.$i.'ID" name="ind_suspen'.$i.'"></td>';
            echo '<td align="center" class="celda_etiqueta"><input type="text" class="campo_texto date" style="text-align: center;" size="10" id="fec_suspen'.$i.'ID" name="fec_suspen'.$i.'" value="'.$fec.'" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'"></td>';
            echo '<td align="center" class="celda_etiqueta"><input type="text" class="campo_texto time" style="text-align: center;" size="10" id="hor_suspen'.$i.'ID" name="hor_suspen'.$i.'" value="'.$hor.'" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'"></td></tr>';
        }

        $formulario -> nueva_tabla();
        $formulario -> oculto("opcion",2, 0);
        $formulario -> oculto("size_suspen", "$i\" id=\"size_suspenID", 0);
        $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0);
        $formulario -> oculto("window","central", 0);
        $formulario -> boton("Guardar", "button\" onClick=\"aceptar_ins1()", 0);
        $formulario -> cerrar();
    } 
 
    function Insertar()
    {
        $datos_usuario = $this -> usuario -> retornar();
        $size =  $_REQUEST['size_suspen'];
        $cont = 0;
        $mQueryy = array();
        $mQuery = "INSERT INTO ".BASE_DATOS.".tab_genera_suspen 
                               ( cod_usuari, ind_suspen, fec_suspen, hor_suspen, fec_creaci, usr_creaci )
                       VALUES  ";
        for($i=0; $i<(int)$size; $i++)
        {		 
            $mQueryx = "SELECT 1 
                          FROM ".BASE_DATOS.".tab_genera_suspen
                         WHERE cod_usuari = '".$_REQUEST['cod_usuari'.$i]."' ";
            $consulta = new Consulta($mQueryx, $this -> conexion);
            $datos_ds = $consulta -> ret_matriz('i');
            
            if(count($datos_ds)>0)
            {
                $mQueryy[] = "UPDATE ".BASE_DATOS.".tab_genera_suspen 
                                 SET ind_suspen = '".$_REQUEST['ind_suspen'.$i]."',
                                     fec_suspen = '".$_REQUEST['fec_suspen'.$i]."',
                                     hor_suspen = '".$_REQUEST['hor_suspen'.$i]."',
                                     fec_modifi = NOW(),
                                     usr_modifi = '".$datos_usuario[cod_usuari]."'
                               WHERE cod_usuari = '".$_REQUEST['cod_usuari'.$i]."'  ";
            }
            else
            {        
                $mQuery .= "('".$_REQUEST['cod_usuari'.$i]."', '".$_REQUEST['ind_suspen'.$i]."', '".$_REQUEST['fec_suspen'.$i]."', '".$_REQUEST['hor_suspen'.$i]."', NOW(), '".$datos_usuario[cod_usuari]."'  ),";       
                $cont++;
            }
        }

        if($cont>0)
        {
            $mQuery   = substr($mQuery, 0, -1);   
            $consulta = new Consulta($mQuery, $this -> conexion);
        }
        else
        {
            foreach($mQueryy as $mQueryz)
            {
                $consulta = new Consulta($mQueryz, $this -> conexion);
            }
        }
        if($consulta)
        {
            $mensaje .= "<br><b><a href=\"#\" onclick=\"javascript:history.back()\">Volver al Listado Principal</a></b>";
            $mens = new mensajes();
            $mens -> correcto("Se Ingreso la Información Existosamente...",$mensaje);
        }
    }
 
}
$proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
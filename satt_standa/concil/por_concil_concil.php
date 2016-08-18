<?php
class PorConcil
{
    var $conexion;

    function __construct($conexion)
    {

        $this->conexion = $conexion;
        switch($_POST[opcion])
        {
            case "2":
            {
                $this->accion();
            }
            break;
            case "3":
            {
                $this->registrar();
            }
            break;
            default:
            {
                $this->listar();
            }
            break;
        }
    }

    function listar()
    {
        include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );

        echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "CONCILIACIONES", "formulario");


       $sql = "SELECT a.cod_manifi, UPPER(b.num_placax), d.nom_ciudad,
                       e.nom_ciudad, UPPER(f.abr_tercer), a.fec_creaci,
                       a.fec_llegad, a.fec_cumpli, UPPER(c.abr_tercer), c.num_telmov
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_despac_vehige b,
                     ".BASE_DATOS.".tab_tercer_tercer c,
                     ".BASE_DATOS.".tab_genera_ciudad d,
                     ".BASE_DATOS.".tab_genera_ciudad e,
                     ".BASE_DATOS.".tab_tercer_tercer f,
                     ".BASE_DATOS.".tab_despac_remdes g
                WHERE a.num_despac = b.num_despac AND
                      b.cod_conduc = c.cod_tercer AND
                      a.cod_ciuori = d.cod_ciudad AND
                      a.cod_ciudes = e.cod_ciudad AND
                      b.cod_transp = f.cod_tercer AND
                      a.num_despac = g.num_despac AND
                      a.fec_cumpli != '0000-00-00 00:00:00' AND
                      a.fec_concil IS NULL AND
                      a.fec_llegad IS NOT NULL GROUP BY 1,2 ";

        $origen = "SELECT d.cod_ciudad, d.nom_ciudad
                    FROM ".BASE_DATOS.".tab_despac_despac a,
                         ".BASE_DATOS.".tab_despac_vehige b,
                         ".BASE_DATOS.".tab_tercer_tercer c,
                         ".BASE_DATOS.".tab_genera_ciudad d
                    WHERE a.num_despac = b.num_despac AND
                          b.cod_conduc = c.cod_tercer AND
                          a.cod_ciuori = d.cod_ciudad AND
                          a.fec_cumpli != '0000-00-00 00:00:00' AND
                          a.fec_concil IS NULL AND
                          a.fec_llegad IS NOT NULL
                    GROUP BY 1,2 ";

        $origen = new Consulta($origen, $this -> conexion);
        $origen = array_merge( array(array("","--")), $origen -> ret_matriz() );

        $placa = "SELECT b.num_placax, UPPER(b.num_placax)
                    FROM ".BASE_DATOS.".tab_despac_despac a,
                         ".BASE_DATOS.".tab_despac_vehige b
                    WHERE a.num_despac = b.num_despac AND
                        a.fec_llegad IS NOT NULL
                    GROUP BY 1,2 ";

        $placa = new Consulta($placa, $this -> conexion);
        $placa = array_merge( array(array("","--")), $placa -> ret_matriz() );

        $list = new DinamicList($this->conexion, $sql, 1 );
        $list->SetClose('no');
        $list->SetHeader("Número de Transporte", "field:a.cod_manifi; type:link; onclick:sendDespacho()");
        $list->SetHeader("Placa", "field:b.num_placax",$placa);
        $list->SetHeader("Origen", "field:d.cod_ciudad",$origen);
        $list->SetHeader("Destino", "field:e.nom_ciudad" );
        $list->SetHeader("Transportadora", "field:f.abr_tercer");
        $list->SetHeader("Fecha de Creacion", "field:a.fec_creaci");
        $list->SetHeader("Fecha de Llegada", "field:a.fec_llegad");
        $list->SetHeader("Fecha de Cumplido", "field:a.fec_cumpli");
        $list->SetHeader("Conductor", "field:c.abr_tercer");
        $list->SetHeader("Celular", "field:c.num_telmov");

        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;
        echo "<td>";
        echo $list->GetHtml();
        echo "</td>";

        $formulario -> nueva_tabla();
        $formulario -> oculto("num_despac",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion",2,0);

        $formulario -> cerrar();
    }

    function accion()
    {

        $sql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax), d.nom_ciudad,
                       e.nom_ciudad, UPPER(f.abr_tercer) as transport, a.fec_creaci,
                       a.fec_llegad, UPPER(c.abr_tercer) as conductor, c.num_telmov
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_despac_vehige b,
                     ".BASE_DATOS.".tab_tercer_tercer c,
                     ".BASE_DATOS.".tab_genera_ciudad d,
                     ".BASE_DATOS.".tab_genera_ciudad e,
                     ".BASE_DATOS.".tab_tercer_tercer f
                WHERE a.num_despac = b.num_despac AND
                      b.cod_conduc = c.cod_tercer AND
                      a.cod_ciuori = d.cod_ciudad AND
                      a.cod_ciudes = e.cod_ciudad AND
                      b.cod_transp = f.cod_tercer AND
                      a.fec_llegad IS NOT NULL AND
                      a.cod_manifi = '$_POST[num_despac]'";

        $despac = new Consulta($sql, $this -> conexion);
        $despac = $despac -> ret_matriz(1);
        $despac = $despac[0];

        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";


        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "CONCILIACIONES", "formulario");

        //$formulario -> oculto("num_despac",$despac[num_despac],0);
        $cumpli = "SELECT d.num_remdes, b.num_docume, b.cod_remdes,
                          a.fec_despac, b.val_pesoxx, c.nom_ciudad

                   FROM ".BASE_DATOS.".tab_despac_despac a,
                        ".BASE_DATOS.".tab_despac_remdes b,
                        ".BASE_DATOS.".tab_genera_ciudad c,
                        ".BASE_DATOS.".tab_genera_remdes d

                   WHERE a.num_despac = b.num_despac AND
                         a.num_despac = '$despac[num_despac]' AND
                         b.cod_ciudad = c.cod_ciudad AND
                         b.cod_remdes = d.cod_remdes AND
                         b.ind_concil = 0 ";


        $cumpli = new Consulta($cumpli, $this -> conexion);
        $cumpli = $cumpli -> ret_matriz(1);

        if(!$cumpli)
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Listar otras conciliaciones</a></b>";
            $mensaje = "<font color='green' size='2'>El Despacho $_POST[num_despac] no posee remisiones para conciliar.</font>";
            echo "<div align='center'><img src=\"../satt_standa/imagenes/error.gif\"><br><b>$mensaje</b><hr>$link_a</div>";
            die();
        }

        $formulario -> nueva_tabla();
        $formulario -> linea("Datos Basicos. ",1,"t2");

        $formulario -> nueva_tabla();
        $formulario -> linea("Número de Transporte:",0,"t","15%");
        $formulario -> linea($despac[cod_manifi],0,"i","15%");
        $formulario -> linea("Conductor:",0,"t","15%");
        $formulario -> linea($despac[conductor],0,"i","15%");
        $formulario -> linea("Transportadora:",0,"t","15%");
        $formulario -> linea($despac[transport],0,"i","15%");
        $formulario -> oculto("num_despac",$despac[num_despac],0);
        $formulario -> oculto("cod_manifi",$despac[cod_manifi],0);

        $formulario -> nueva_tabla();
        $formulario -> linea("Detalle Conciliaciones. ",1,"t2");

        $formulario -> nueva_tabla();
        $formulario -> linea("Conciliacion",0,"t","5%");
        $formulario -> linea("Documento Código",0,"t","10%");
        $formulario -> linea("Remision",0,"t","10%");
        $formulario -> linea("Fecha creación despacho",0,"t","15%");
        $formulario -> linea("Peso",0,"t","10%");
        $formulario -> linea("Destino",0,"t","10%");
        $formulario -> linea("Novedad",0,"t","15%");
        $formulario -> linea("Observacion",1,"t","25%");

        $noveda = " SELECT cod_novcum, nom_novcum
                    FROM ".BASE_DATOS.".tab_genera_novcum ";

        $noveda = new Consulta($noveda, $this -> conexion);
        $noveda = $noveda -> ret_matriz(2);

        $i = 1;

        $formulario -> oculto("siz_cumpli",sizeof($cumpli),0);

        foreach( $cumpli as $row )
        {
            echo  '<td class="celda">
                    <input type="checkbox" value="'.$row[cod_remdes].'" width="" name="cod_remdes'.$i.'"/>
                   </td>';
            $formulario -> linea(strtoupper($row[num_remdes]),0,"i","");
            $formulario -> linea(strtoupper($row[num_docume]),0,"i","");
            $formulario -> linea($row[fec_despac],0,"i","");
            $formulario -> linea($row[val_pesoxx],0,"i","");
            $formulario -> oculto("val_pesoxx$i",$row[val_pesoxx],0);
            /*echo '<td class="celda_info">
                    <input class="campo_texto" type="text" width="" maxlength="3" value="'.$row[val_pesoxx].'" size="3"
                        name="val_pesoxx'.$i.'" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'"/>
                    </td>';*/
            $formulario -> linea($row[nom_ciudad],0,"i","");

            //$formulario -> lista("", "$campo\" multiple=\"multiple\" size=\"3\"", $noveda, 0,0);


            echo '<td class="celda_info"><select class="form_01" multiple="multiple" name="novedad'.$i.'[] size="3">';
            //echo ' onKeypress=buscar_op(this) onblur=borrar_buffer() onclick=borrar_buffer()> ';

            $n = sizeof($noveda);

            for($j = 0; $j < $n; $j++)
            {
             printf("<option value=\"%s\">%s</option>\n", $noveda[$j][0], $noveda[$j][1]);
            }

            printf('</select></td>');

            echo '<td class="celda_info">
                    <input class="campo_texto" type="text"  maxlength="255" size="30"
                        name="obs_noveda'.$i.'" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'"/>
                    </td></tr>';
            $i++;
        }

        /*$formulario -> nueva_tabla();
        $formulario -> archivo("Imagen Remesa: ","foto",12,200,"",1);*/

        $formulario -> nueva_tabla();
        if($cumpli)
        $formulario -> botoni("Aceptar","validarCumplidos()",0);
        $formulario -> botoni("Atras","irAtras()",1);//validarCumplidos()

        $formulario -> nueva_tabla();
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion",3,0);

        $formulario -> cerrar();
    }

    function registrar()
    {
        /*echo "<pre>";
        print_r( $GLOBALS);
        echo "</pre>";*/
        //die();
        global $HTTP_POST_FILES;

        $cumplidos = $_POST[cumplido];
        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        $error = 0;

        /*if($HTTP_POST_FILES["foto"]["type"] != "image/gif" AND $HTTP_POST_FILES["foto"]["type"] !="image/jpeg" AND $HTTP_POST_FILES["foto"]["type"] !="image/pjpeg")
            $error = 1;

         $subir = move_uploaded_file($HTTP_POST_FILES["foto"]["tmp_name"], "".URL_ARCHIV."fotcum/".$_POST[num_despac].".jpg");

         if($HTTP_POST_FILES["foto"]["tmp_name"] && $error != '1')
         {
            if($subir)
                $GLOBALS[foto] = "'fotcum/".$GLOBALS[num_despac].".jpg'";
            else
            {
             $GLOBALS[foto] = "NULL";
             $msm = "La Imagen no pudo ser cargada al cumplido.";
            }
         }
         elseif($HTTP_POST_FILES['foto']['error'])
         {
          $error = $HTTP_POST_FILES['foto']['error'];
          if($error == '1' | $error == '2')
            $msm = "<b>La Imagen cargada Excedio el tamaño maximo permitido y no fue cargada.</b><br>";
          elseif($error == '3')
            $msm = "<b>La carga de la Imagen fue Interrumpida.</b><br>";
          elseif($error == '4')
            $msm = "<b>La Imagen no fue cargada.</b><br>";

          $GLOBALS[foto] = "NULL";
         }
         else
         {
          $msm = "La Imagen no pudo ser cargada al cumplido.";
          $GLOBALS[foto] = "NULL";
         }*/

        $cumplido_total = 0;

        for( $i=1 ; $i<= $_POST[siz_cumpli];$i++ )
        {
            if($_POST["cod_remdes$i"])
            {
                $novedades = $_POST["novedad$i"];
                if(!$novedades)
                    $novedades[0] = '0';

             for ($n = 0; $n < count($novedades); $n++)
             {
                $cumplido = "INSERT INTO ".BASE_DATOS.".tab_noveda_concil
                    ( num_despac, cod_manifi, cod_remdes, val_pesoxx, cod_novcum, obs_noveda, usr_creaci, fec_creaci )
                    VALUES
                    ( '$_POST[num_despac]','$_POST[cod_manifi]','".$_POST["cod_remdes$i"]."','".$_POST["val_pesoxx$i"]."',
                      '".$novedades[$n]."','".$_POST["obs_noveda$i"]."','$usuario',NOW())";

                $insercion = new Consulta($cumplido, $this -> conexion,"R");
             }

                $cumplido = "UPDATE ".BASE_DATOS.".tab_despac_remdes
                             SET ind_concil = '1'
                             WHERE num_despac = '$_POST[num_despac]' AND cod_remdes = '".$_POST["cod_remdes$i"]."'";

                $insercion = new Consulta($cumplido, $this -> conexion,"R");

                $cumplido_total++;
            }

        }

        if($cumplido_total == $_POST[siz_cumpli])
        {
            $cumplido = "UPDATE ".BASE_DATOS.".tab_despac_despac
                            SET fec_concil = NOW()
                          WHERE num_despac = '$_POST[num_despac]'";

            $insercion = new Consulta($cumplido, $this -> conexion,"R");
        }

        if( $insercion = new Consulta("COMMIT", $this -> conexion))
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Insertar otros conciliaciones</a></b>";

            if($msm)
                $mensaje = $msm;

            $mensaje .=  "Las Conciliaciones del Despacho <b>".$_POST[num_despac]."</b> Se Insertaron con Exito".$link_a;
            $mens = new mensajes();
            $mens -> correcto("INSERTAR CONCILIACIONES",$mensaje);
        }
    }
}
$service = new PorConcil($this->conexion);
?>

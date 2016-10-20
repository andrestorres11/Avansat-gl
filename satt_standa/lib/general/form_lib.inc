<?php
//date_default_timezone_set('America/Bogota');
class Formulario

{

    //Atributos

    var $titulo,//titulo del formulario

        $accion,//archivo al que se dirige

        $metodo,//metodo de envio

        $nombre,//nombre del formulario

        $ancho,//ancho en px de la tabla de formulario

        $jscript;//llamadas a eventos y demas codigo javaScritp

    //Metodos



    function __construct($ac = "index.php", $me = "post", $ti = "Formulario", $no = "form", $bl = "_self", $en = 0, $an = "100%", $js = 0, $isexp = 0)//Constructor de la clase

    {

        $this -> titulo = $ti;

        $this -> accion = $ac;

        $this -> metodo = $me;

        $this -> nombre = $no;

        $this -> jscript = $js;

        $this -> ancho = $an;

        if(!$isexp)
        {

        echo "\n          <TR>

            <TD><TABLE ALIGN=\"center\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\" BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_08.gif\">

              <TR>

                <TD><IMG NAME=\"top_07\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_07.gif\" WIDTH=\"320\" HEIGHT=\"28\" BORDER=\"0\" ALT=\"\"></TD>

                <TD NOWRAP BACKGROUND=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_08.gif\"><TABLE WIDTH=\"250\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">

                  <TR>

                    <TD WIDTH=\"250\" HEIGHT=\"24\" ALIGN=\"CENTER\" VALIGN=\"MIDDLE\" NOWRAP CLASS=\"titulo1\">".$ti."</TD>

                  </TR>

                  <TR>

                    <TD HEIGHT=\"4\"></TD>

                  </TR>

                </TABLE></TD>

                <TD><IMG NAME=\"top_10\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_10.gif\" WIDTH=\"40\" HEIGHT=\"28\" BORDER=\"0\" ALT=\"\"></TD>

                <TD WIDTH=\"100%\" STYLE=\"background-image:url('../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_11.gif'); background-repeat:repeat-x; background-position:center;\"><IMG SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_11.gif\" WIDTH=\"3\" HEIGHT=\"28\"></TD>

                <TD><IMG NAME=\"top_13\" SRC=\"../".DIR_APLICA_CENTRAL."/estilos/".ESTILO."/imagenes/top_13.gif\" WIDTH=\"193\" HEIGHT=\"28\" BORDER=\"0\" ALT=\"\"></TD>

              </TR>

            </TABLE></TD>

          </TR>";



     echo "\n          <TR>

            <TD><TABLE ALIGN=\"center\" cellspacing=0 cellpadding=0 WIDTH=\"".$an."\" border=0>

              <TR>

                <TD valign=center align=middle>

                <FORM name =\"$no\" method =\"$me\" action =\"$ac\" target=\"$bl\"";



        if($en)

            echo " enctype=\"$en\" ";



        if($js)

            echo " $js ";



    echo "><TABLE WIDTH=\"100%\" cellpadding=4 cellspacing=0 CLASS=\"formulario\">

                  <TR>";

        }

    }



    function cerrar()

    {

       echo "\n                  </TR>

                </TABLE></FORM>

                </TD>

              </TR>

            </TABLE></TD>

          </TR>";

    }



    function nueva_tabla($lin = 0,$co=0,$size=0)

    {

       echo "\n                  </TR>

                </TABLE>";



        if($lin)

        echo "\n                <hr ";

        if($co)

        echo "color='$co'";

        if($size)

        echo "size='$size'";

        if($lin)

        echo ">";



        echo "\n                <TABLE WIDTH=\"100%\" cellpadding=4 cellspacing=0 CLASS=\"formulario\">

                  <TR>";



    }



    function espacio($alt = "10")

    {

            echo "</tr>\n<tr>\n<td height=\"".$alt."\"></td>\n</tr>\n<tr>";



    }



   function archivo($et, $no, $an, $mc, $js, $sl)

    {

        $archivo = new Form_archivo($et, $no, $an, $mc, $js, $sl);

    }



     function texto($et, $ti, $no, $sl, $an, $al, $wr, $vi, $scrp = '', $serhij = '', $texbus = NULL, $obliga = 0, $matpopup = NULL, $idelement = NULL)

    {

        $texto = new Form_texto($et, $ti, $no, $sl, $an, $al, $wr, $vi, $scrp, $serhij, $texbus, $obliga, $matpopup, $idelement);

    }



    function boton($et, $ti, $sl)

    {

        $boton = new Form_boton($et, $ti, $sl);

    }



    function oculto($no, $va, $sl)

    {

        $oculto = new Form_oculto($no, $va, $sl);

    }



    function radio($et, $no, $va, $ei, $sl)

    {

        $radio = new Form_radio($et, $no, $va, $ei, $sl);

    }



    function lista($et, $no, $va, $sl, $obliga = 0)

    {

        $linea = new Form_lista($et, $no, $va, $sl, $obliga);

    }

    function lista_titulo($et, $no, $va, $sl)

    {

        $linea = new Form_lista_titulo($et, $no, $va, $sl);

    }

    function linea($te, $sl, $est = "", $an = 0, $col = 0, $al = "left", $color = NULL)

    {

        $linea = new Form_linea($te, $sl, $an, $est, $col, $al, $color);

    }



    function datos($te, $val, $sl)

    {

        $datos = new Form_datos($te, $val, $sl);

    }



    function caja($et, $no, $va, $ei, $sl=0)

    {

        $caja = new Form_caja($et, $no, $va, $ei, $sl);

    }

    function imagen($et, $ar, $ta, $an, $al, $bo, $js, $sl, $in=0, $multip = NULL)

    {

        $caja = new Form_imagen($et, $ar, $ta, $an, $al, $bo, $js, $sl, $in, $multip);

    }

    function botoni($et, $js, $sl, $me="")

    {

        $botoni = new Form_botoni($et, $js, $sl, $me);

    }

    function fecha_calendar($et, $no, $fo, $vi, $ti, $sl, $as = 0, $ob = 0)
    {
    	$fecha_calendar = new Form_fecha_calendar($et, $no, $fo, $vi, $ti, $sl, $as, $ob);
    }

}



class Form_archivo

{

    //Atributos

    var $etiqueta,//etiqueta que va a tener el campo de archivo

        $nombre,//nombre que tomera el archivo al ser subido

        $ancho,//ancho del campo de bsqueda

        $max_caracteres,//móimo nmero de caracteres de la ruta del archivo

        $jscript,//llamadas a eventos y demas codigo javaScritp

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva



    //Metodos

    function __construct($et, $no, $an, $mc, $js, $sl)

    {

        $this -> etiqueta = $et;

        $this -> nombre = $no;

        $this -> ancho = $an;

        $this -> max_caracteres = $mc;

        $this -> jscript = $js;

        $this -> salto_linea = $sl;



        echo "     <td class=\"celda_etiqueta\" align=\"right\">$et</td>

                   <td><input type=\"file\" name=\"$no\" size=\"$an\" maxlength=\"$mc\"";

        if($js)

            echo " $js ";

        echo "></td>\n";

        if($sl)

            echo "    </tr>

                      <tr>\n";

    }

}



class Form_texto
{
    //Atributos
    var $etiqueta,//titulo descriptivo del campo
        $tipo,//puede ser text, password, textarea.
        $salto_linea,//si esta en 1 indica que se debe cerrar la fila y abrir una nueva
        $ancho,//ancho de un campo de texto
        $alto,//alto de un textarea o móimo de caracteres de un text
        $wrap,//Solo se utiliza si $tipo es textarea y puede ser OFF, VIRTUAL, PHYSICAL
        $nombre,//nombre del campo
        $scrp,//busqueda
	    $serhij, //sevicio que debe abrir
        $val_inicial,//valor inicial
        $campo_obliga,//Campo que debe ser obligatorio
        $var_popup;//Variables y valores adicionales para la ventana popup.

    //Metodos

    function  __construct($et = "Texto", $ti = "text", $no = "texto", $sl = 0, $an = "10", $al = "10", $wr = "OFF", $vi = "", $scrp = "", $serhij = '', $texbus = NULL, $obliga = 0,$matpopup = null,$idelement = null)
    {
        $this -> etiqueta = $et;
        $this -> tipo = $ti;
        $this -> salto_linea = $sl;
        $this -> ancho = $an;
        $this -> alto = $al;
        $this -> wrap = $wr;
        $this -> nombre = $no;
        $this -> val_inicial = $vi;
        $this -> scrp = $scrp;
        $this -> serhij = $serhij;
        $this -> campo_obliga = $obliga;
        $this -> var_popup = $matpopup;

        $textobliga = NULL;

        if($obliga)
         $textobliga = "* ";

        if(!$texbus)
         echo "<td align=\"right\" class=\"celda_titulo\">\n$textobliga $et\n</td>\n<td class=\"celda_info\">\n";
        else
         echo "<td align=\"center\" class=\"celda_titulo\">\n$textobliga $et\n";

        if($ti == "textarea")
        {
            echo "<textarea CLASS=\"campo_texto\" onFocus=\"this.className='campo_texto_on'\" onBlur=\"this.className='campo_texto'\" name=\"$no\" cols=\"$an\" rows=\"$al\" wrap=\"$wr\">$vi</textarea>";
        }

        else

        {
          if(!$texbus)
           printf("<input CLASS=\"campo_texto\" onFocus=\"this.className='campo_texto_on'\" onBlur=\"this.className='campo_texto'\" type=\"%s\" name=\"%s\" size=\"%s\" value=\"%s\" maxlength=\"%s\">\n", $ti, $no, $an, $vi, $al);
          else
           printf("<br><input type=\"%s\" name=\"%s\" size=\"%s\" value=\"%s\" maxlength=\"%s\">\n", $ti, $no, $an, $vi, $al);

        }

        if($this -> scrp)
        {
           $datos = explode(';', $this -> scrp);

           echo "<script language=\"javascript\" src=\"../".DIR_APLICA_CENTRAL."/js/pagina_hija.js\"></script>";

           echo "<script>";
           echo "var variahij = new Array();";
           for($i = 0; $i < sizeof($matpopup); $i++)
            echo "variahij[$i] = \"".$matpopup[$i]["nomvar"]."=".$matpopup[$i]["valorx"]."\";";
            
           echo "var variaobj = new Array();";
           for($i = 0; $i < sizeof($idelement); $i++)
            echo "variaobj[$i] = \"".$idelement[$i]["nomvar"]."\";";

           echo "</script>";
           echo "<a href=\"#\" onClick=\"hija('', '', '$serhij', '".$datos[0]."','".$_REQUEST["cod_servic"]."', '".BASE_DATOS."', 'b', '350', '".$datos[1]."',variahij,variaobj)\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/botone/puntos.gif\" border=\"0\" alt=\"buscar\"></a>";
        }

        printf("</td>\n");

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}



class Form_boton

{

    var $etiqueta,//la etiqueta del boton

    //Atributos

        $tipo,//typo del boton submit, reset o button

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

    //Metodos

    function __construct($et = "Boton", $ti = "button", $sl = 0)

    {

        $this -> etiqueta = $et;

        $this -> tipo = $ti;

        $this -> salto_linea = $sl;

        if($this -> etiqueta == "borrar")

           $es = "crmButton small cancel";

        else

           $es = "crmButton small save";



        echo "<td align=\"center\">\n";
        printf("<td align=\"center\">\n<input class= \"%s\" type=\"%s\" name=\"%s\" value=\"%s\">\n</td>\n", $es, $ti, $et, $et);
        echo "</td>\n";

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}



class Form_caja

{

    //Atributos

    var $etiqueta,//Etiqueta que va a identificar el boton

        $nombre,//nombre de la variable

        $valores,//valor que tomera la variable al checar esta caja

        $estado_inicial,//estado inicial 1 seólado, 0 sin seólar

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

    //Metodos

    function __construct($et = "", $no = "check", $va = "on", $ei = 0, $sl = 0)

    {

        $this -> nombre = $no;

        $this -> valores = $va;

        $this -> estado_inicial = $ei;

        $this -> salto_linea = $sl;

        echo "<td align=\"right\" class=\"celda_etiqueta\">\n$et\n</td>\n<td class=\"celda\">\n<input type=\"checkbox\" name=\"$no\" value=\"$va\"";

        if($ei)

            echo "checked";

        echo ">\n</td>\n";

        if($sl)

            echo "</tr>\n<tr>\n";

    }

}

class Form_radio//cr4ea un conjunto de radio buttons

{

    //Atributos

    var $etiqueta,//Etiqueta que va a identificar el boton

        $nombre,//nombre de la variable

        $valores,//valor que tomera la variable al checar este radio button

        $estado_inicial,//estado inicial 1 seólado, 0 sin seólar

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

    //Metodos

    function __construct($et = "radiobutton", $no = "radiobutton", $va = "radiobutton", $ei = 0, $sl = 0)

    {

        $this -> etiqueta = $et;

        $this -> nombre = $no;

        $this -> valores = $va;

        $this -> estado_inicial = $ei;

        $this -> salto_linea = $sl;

        printf("<td align=\"right\" class=\"celda_etiqueta\">\n%s\n</td>\n<td class=\"celda\">\n<input type=\"radio\" name=\"%s\" value=\"%s\" ", $et, $no, $va);

        if($ei)

            printf("checked");

        printf(">\n</td>\n");

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}



class Form_lista

{

    //Atributos

    var $etiqueta,//etiqueta que va a tener el menu

        $nombre,//nombre que va a tomar al variable que representa este menu

        $valores,//vector que contiene los valores que va a tomar la lista en el vector va[i][0] los value y en el va[i][1] los textos que se van a mostrar en la lista

        $salto_linea,//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

        $campo_obliga;//Campo Obligatorio



    //Metodos

    function __construct($et, $no, $va, $sl, $obliga = 0)

    {

        $this -> etiqueta = $et;

        $this -> nombre = $no;

        $this -> valores = $va;

        $this -> salto_linea = $sl;

        $this -> campo_obliga = $obliga;

        $textobliga = NULL;

        if($obliga)
         $textobliga = "*";
		 
		if($et)
        echo "<td align=\"right\" class=\"celda_titulo\">\n$textobliga $et\n</td>\n";
		echo "<td class=\"celda_info\">\n<select class=\"form_01\" name=\"$no\"";

        echo " onKeypress=buscar_op(this) onblur=borrar_buffer() onclick=borrar_buffer() ";

        echo ">\n";

        $n = sizeof($va);



        for($i = 0; $i < $n; $i++)

        {

            printf("<option value=\"%s\">%s</option>\n", $va[$i][0], $va[$i][1]);

        }

        printf("</select>\n</td>\n");

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}

class Form_lista_titulo

{

    //Atributos

    var $etiqueta,//etiqueta que va a tener el menu

        $nombre,//nombre que va a tomar al variable que representa este menu

        $valores,//vector que contiene los valores que va a tomar la lista en el vector va[i][0] los value y en el va[i][1] los textos que se van a mostrar en la lista

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva



    //Metodos

    function __construct($et, $no, $va, $sl)

    {

        $this -> etiqueta = $et;

        $this -> nombre = $no;

        $this -> valores = $va;

        $this -> salto_linea = $sl;



        printf("<td class=\"celda_titulo\">\n<select class=\"form_01\" name=\"%s\">\n",$no);

        $n = sizeof($va);



        for($i = 0; $i < $n; $i++)

        {

            printf("<option value=\"%s\">%s</option>\n", $va[$i][0], $va[$i][1]);

        }

        printf("</select>\n</td>\n");

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}



class Form_oculto

{

    //Atributos

    var $nombre,//nombre del campo oculto

        $valor,//valor que tomara el campo oculto

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva



    //Metodos

    function __construct($no = "hidden", $va = "hidden", $sl = 0)

    {

        $this -> nombre = $no;

        $this -> valor = $va;

        $this -> salto_linea = $sl;

        printf("<input type=\"hidden\" name=\"%s\" value=\"%s\">\n", $no, $va);

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}

class Form_imagen

{

    //Atributos

    var $etiqueta,//la etiqueta del boton

        $archivo,//ruta para el archivo de la imagen

        $texto_alterno,//texto alterno para la imagen

        $ancho,//ancho de la imagen

        $alto,//alto de la imagen

        $borde,//borde de la imagen

        $jscript,//llamadas a eventos y demas codigo javaScritp

        $salto_linea,//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

        $tab_index,//indica el indice del objeto en el formulario

        $multip_link;



    //Metodos

    function __construct($et, $ar, $ta, $an, $al, $bo, $js, $sl, $in, $multip)

    {

        $this -> etiqueta = $et;

        $this -> archivo = $ar;

        $this -> texto_alterno = $ta;

        $this -> ancho = $an;

        $this -> alto = $al;

        $this -> borde = $bo;

        $this -> jscript = $js;

        $this -> salto_linea = $sl;

        $this -> tab_index = $in;

        $this -> multip_link = $multip;


        $alinea = "center";

        if($multip)
         $alinea = "left";

        echo "     <td align=\"".$alinea."\"><input type=\"image\" name=\"$et\" src=\"$ar\" alt=\"$ta\" width=\"$an\" height=\"$al\" border=\"$bo\"";
        if($in)
         echo " tabindex=$in ";
        if($js)
         echo " $js >";

		if($multip)
		{
		 for($i = 0; $i < sizeof($multip); $i++)
		 {
		  echo "                          <input type=\"image\" name=\"$et\" src=\"".$multip[$i][0]."\" alt=\"$ta\" width=\"$an\" height=\"$al\" border=\"$bo\"";
		  if($in)
           echo " tabindex=$in ";
          if($multip[$i][1])
           echo " ".$multip[$i][1]." >";
		 }
		}

        echo " </td>\n";

        if($sl)

            echo "    </tr>

                      <tr>\n";

    }

}



class Form_linea

{

    //Atributos

    var $texto,//texto que va a contener la linea

        $sl,//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

        $an; //ancho

    //Metodos

    function __construct($te, $sl, $an, $est, $col, $al, $color)

    {

       //if(!$color)
       //{

        switch($est)

        {

                case "t":

                        $estilo_et = "celda_titulo";

                break;

                case "t2":

                        $estilo_et = "celda_titulo2";

                break;

                case "h":

                        $estilo_et = "header";

                break;

                case "e":

                        $estilo_et = "celda_error";

                break;

                case "a":

                        $estilo_et = "celda_aproba";

                break;

                case "ie":

                        $estilo_et = "celda_error_relleno";

                break;

                case "iu":

                        $estilo_et = "celda_urban_relleno";

                break;

                case "in":

                        $estilo_et = "celda_ultim_relleno";

                break;

                case "c":

                        $estilo_et = "celda";

                break;

                case "c2":

                        $estilo_et = "celda2";

                break;

                case "i":

                        $estilo_et = "celda_info";

                break;

                default:

                        $estilo_et = "celda_etiqueta";

                break;

        }

        echo "\n                    <TD class=\"$estilo_et\" align=\"$al\"";
       //}
       //else
       if($color)
        echo " style =\"background-color:".$color.";\"";


        if(!$te)

            $te = "&nbsp;";

        if($col)

           echo " colspan=\"$col\"";


        if($an)

           echo " width=\"$an\"";

        echo "><b>$te</b></TD>";

        if($sl)

            printf("\n                  </TR>\n                  <TR>");

    }

}



class Form_botoni

{

    var $etiqueta,//la etiqueta del boton

        $onClick,//Inclusió de Javascript

        $mens,//Mensaje cuando pone el mouse encima del boton

        $salto_linea;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

    //Metodos

    function __construct($et = "aceptar",$oc, $sl = 0, $me="")

    {

        $this -> etiqueta = strtolower($et);

        $this -> onClick = $oc;

        $this -> mens = $me;

        $this -> salto_linea = $sl;

        if($this -> etiqueta == "borrar")

           $es = "crmButton small cancel";

        else

           $es = "crmButton small save";



        printf("<td align=\"center\">\n<input class=\"%s\" type=\"button\" name=\"%s\" value=\"%s\" onClick=\"%s\">\n</td>\n", $es, $et, $et, $oc);

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}



class Form_datos

{

    //Atributos

    var $texto,//texto que va a contener la linea como titulo

        $val,//valor del texto que se va traer de la base de datos

        $sl;//si esta en 1 indica que se debe cerrar la fila y abrir una nueva

    //Metodos

    function __construct($te, $val, $sl)

    {

        printf("<td class=\"etiqueta\">\n<b>%s</td><td class=\"celda\">\n%s</td>\n", $te,$val);

        if($sl)

            printf("</tr>\n<tr>\n");

    }

}

class Form_fecha_calendar
{
	var $etiqueta,
		$nombre,
		$formulario,
		$val_inicial,
		$tip_formato,
		$salto_linea,
		$obligatorio;

	function  __construct($et, $no, $fo, $vi, $ti, $sl, $as, $ob)

    {

        $this -> etiqueta = $et;

        $this -> nombre = $no;

        $this -> formulario = $fo;

        $this -> val_inicial = $vi;

        $this -> tip_formato = $ti;

        $this -> salto_linea = $sl;

        $this -> obligatorio = $ob;

        if($ob)
         $textobliga = "*";

        if($as)
         $aplica_submit = " onChange=\"".$fo.".submit()\"";
        else
         $aplica_submit = NULL;

        echo "<td align=\"right\" class=\"celda_titulo\">\n$textobliga $et\n</td>\n<td class=\"celda_info\">\n";
        
        /*
        echo "<input CLASS=\"campo_texto\" type=\"text\" readonly name=\"".$no."\" value=\"".$vi."\"".$aplica_submit.">
        	  <input type=\"button\" value=\"...\" onclick=\"displayCalendar(".$fo.".".$no.",'".$ti."',this,true)\">
        	 ";
       */
        
        //----------------------------------------------------------------------------------------------------------------------------
        //@AUTHOR: MESSIAS, THE ONLY ONE
        
        echo '<input type="text" class="campo_texto" name="'.$no.'" size="16" maxlength="16" onkeypress="return EvalDate( event, this, \''.date( 'Y-m-d' ).'\' );" onblur="OnblurEvalDate( this, \''.date( 'Y-m-d H:i', strtotime( "+365 days" ) ).'\', \''.date( 'Y-m-d H:i', strtotime( "-365 days" ) ).'\' );" value="'.$vi.'"'.$aplica_submit.'" />';
        //echo '<input type="button" value="..." onclick="displayCalendar( \''.$fo.'.'.$no.'\', \''.$ti.'\', this, true )">';
        echo "<input type=\"button\" value=\"...\" onclick=\"displayCalendar(".$fo.".".$no.",'".$ti."',this,true)\">";
        //----------------------------------------------------------------------------------------------------------------------------

        echo "</td>";

        if($sl)
            printf("</tr>\n<tr>\n");

    }
}
?>

<?php
/**************************************************************************************************************/
/*********En esta Librería se Especifican las Clases que Permiten el Manejo de Tablas *************************/
/**************************************************************************************************************/
class Menu_Desplegable
{
    //Atributos
    var $titulo,
        $items,
        $coord_x,
        $coord_y,
        $ancho,
        $alto,
        $tipo_letra,
        $tamagnio_letra,
        $color_letra,
        $color_letra_sobre,
        $fondo,
        $fondo_sobre,
        $jscript,
        $html;

    //metodos
    function menu_desplegable($ti, $it, $cx, $cy, $an, $al, $tl, $tml, $cl, $cs, $fo, $fs)
    {
        $this -> titulo = $ti;
        $this -> items = $it;
        $this -> coord_x = $cx;
        $this -> coord_y= $cy;
        $this -> ancho = $an;
        $this -> alto = $al;
        $this -> tipo_letra = $tl;
        $this -> tamagnio_letra = $tml;
        $this -> color_letra = $cl;
        $this -> color_letra_sobre = $cs;
        $this -> fondo = $fo;
        $this -> fondo_sobre = $fs;

        $this -> jscript = "\nif (window.fw_menu_" . $this -> titulo . ")return;\nwindow.fw_menu_" . $this -> titulo . " = new Menu(\"root\", " . $this -> ancho . "," . $this -> alto . ",\"" . $this -> tipo_letra . "\"," . $this -> tamagnio_letra . ",\"" . $this -> color_letra . "\",\"" . $this -> color_letra_sobre . "\",\"" . $this -> fondo . "\",\"" . $this -> fondo_sobre . "\");";
        for($i = 0; $i < sizeof($this -> items); $i++)
        {
              $this -> jscript .= "\nfw_menu_" . $this -> titulo . ".addMenuItem(\"" . $this -> items[$i][0] ."\",\"window.open('" . $this -> items[$i][1] ."', '" . $this -> items[$i][2] . "');\");";
        }
        $this -> jscript .= "\nfw_menu_" . $this -> titulo . ".hideOnMouseOut=true;
                              fw_menu_" . $this -> titulo . ".writeMenus();";
        $this -> html = "\n<tr><td class=\"menu\"><a href=\"#\" onMouseOut=\"FW_startTimeout();\"  onMouseOver=\"window.FW_showMenu(window.fw_menu_" . $this -> titulo . ", " . $this -> coord_x . ", " . $this -> coord_y . ");\">" . $this ->titulo . "</td></tr></a>";
    }
    function ret_codigo()
    {
        $codigo["jscript"] = $this -> jscript;
        $codigo["html"] = $this -> html;
        return $codigo;
    }

}

/**************************************************************************************************************/
/****************************************************Fin del Archivo*******************************************/
/**************************************************************************************************************/
?>

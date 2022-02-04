<?php

class Pagina//Esta clase modela una págian web en su encabezado
{
    //Atributos
    var $titulo, //titulo de la página web
        $bgcolor, //color de fondo de al página en hexagecimal
        $background, //ruta de la imagen de fondo
        $leftmargin, //margen izquierda
        $topmargin, //margen superior
        $marginwidth, //margen a lo alto?
        $marginheight, //margen a lo ancho?
        $hoja_estilos, //ruta de lahoja de estilos de la pagina
        $body_java_script; // java script

    //Métodos
    function __construct($ti = "SAT Basico", $bg=0, $ba=0, $le=0, $to=0, $mw=0, $mh=0, $he=0, $js=0, $ljs=0)//Constructor de la clase
    {
        $this -> titulo = $ti;
        $this -> bgcolor = $bg;
        $this -> background = $ba;
        $this -> leftmargin = $le;
        $this -> topmargin = $to;
        $this -> marginwidth = $mw;
        $this -> marginheight = $mh;
        $this -> hoja_estilos = $he;
        $this -> java_script = $js;
                  $this -> lib_jscript = $ljs;
        echo "<html>
        <head>
          <title>$ti</title>
          <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
        </head>";
        if($he)
            echo "\n\t<link rel=\"stylesheet\" href=\"$he\" type=\"text/css\">";
        if($ljs)
            echo "\n\t<script language=\"javascript\" src=\"$ljs\"></script>";
        echo "   \n\t<body";
        if($bg)
            echo " bgcolor=\"$bg\"";
        if($ba)
            echo " background=\"$ba\"";
        if($js)
            echo " $js ";
        echo " leftmargin=\"$le\" topmargin=\"$to\" marginwidth=\"$mw\" marginheight=\"$mh\">\n";
    }

    function cerrar()//Cierra la pagina web
    {
       echo " \n\t</body>\n</html>";
    }
}
?>
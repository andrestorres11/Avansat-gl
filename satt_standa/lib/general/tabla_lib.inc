<?php
/**************************************************************************************************************/
/*********En esta Librería se Especifican las Clases que Permiten el Manejo de Tablas *************************/
/**************************************************************************************************************/
Class Tabla_matriz//Esta clase permite publicar una tabla con el contenido de una nmatriz de dos dimensiones
{
    //Atributos
    var $matriz; //la matriz que va ha ser publicada

    //Metodos
    function __construct($ma)//Constructor de la clase
    {
        echo "<table border=\"0\" width=\"100%\" bgcolor=\"#F7F7F7\">\n";
        $this -> matriz = $ma;
        for($i = 0; $i < sizeof($ma); $i++)
        {
            echo "<tr>\n";
            for($j = 0; $j < sizeof($ma[0]); $j++)
            {
                echo "<td";
                if($i == 0)
                    echo" class=\"etiqueta2\" ";
                else
                  {
                  if($i%2 == 0)
                    echo" class=\"celda\" ";
                  else
                    echo" class=\"celda2\" ";
                  }
                echo ">".$ma[$i][$j]."</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>";
    }
}

Class Tabla_arreglo//Esta clase permite publicar una lista a dos columnas
{
    //Atributos
    var $arreglo;

    //Metodos
    function Tabla_arreglo($ar)
    {
        $this -> arreglo = $ar;
        echo "<table border=\"0\" width=\"100%\" bgcolor=\"#F7F7F7\">\n";
        for($i = 0; $i < sizeof($ar[0]); $i++)
        {
            echo "<tr><td class=\"celda_etiqueta\">" . $ar[0][$i] . "</td><td class=\"celda_info\">" . $ar[1][$i] . "</td></tr>";
        }
        echo "</table>";
    }
}

Class Tabla_menu//Esta clase implementa una tabla para un menú con apariencia de botones
{
    //Atributos
    var $titulo,//EL titulo del menú
        $items;//Matriz bidimensional con el nombre de los items y los links de cada uno

    //Metodos
    function Tabla_menu($ti, $it)
    {
        $this -> titulo = $ti;
        $this -> items = $it;
        echo "<table bgcolor=\"#003399\" width=\"100%\" border=\"1\">
               <tr>
                <td class=\"titulo_menu\">$ti</td>
               </tr>";
        for($i = 0; $i < sizeof($it); $i++)
        {
            echo "<tr><td class=\"item_menu\"><a href=\"" . $it[$i][1] . "\"";
            if($it[$i][2])
                echo "target=\"" . $it[$i][2]. "\"";
            echo ">" . $it[$i][0] . "</a></td></tr>";
        }
        echo "</table>";
    }
}
//para imprimir con colores
Class Tabla_color//Esta clase permite publicar una tabla con el contenido de una nmatriz de dos dimensiones
{
    //Atributos
    var $matriz; //la matriz que va ha ser publicada

    //Metodos
    function Tabla_color($ma)//Constructor de la clase
    {
        echo "<table border=\"0\" width=\"100%\" bgcolor=\"#E6E6DC\">\n";
        $this -> matriz = $ma;
        for($i = 0; $i < sizeof($ma); $i++)
        {
            echo "<tr>\n";
            for($j = 0; $j < sizeof($ma[0]); $j++)
            {
                echo "<td";
                if($i == 0)
                    echo" class=\"etiqueta\">".$ma[$i][$j]."</td> ";
                else
                {
                   if($i%2 ==0 AND $j != 0)
                       echo" class=\"celda\">".$ma[$i][$j]."</td> ";
                   else if($i%2 == 1 AND $j != 0)
                       echo" class=\"celda2\">".$ma[$i][$j]."</td> ";
                   else if($ma[$i][15] == 4 AND $j == 0)
                     echo " bgcolor=\"#DDBBFF\"><font size=\"2\">".$ma[$i][0]."</font></td>";
                   else if($ma[$i][15] == 3 AND $j == 0)
                     echo " bgcolor=\"#FF8891\"><font size=\"2\">".$ma[$i][0]."</font></td>";
                   else if($ma[$i][15] == 2 AND $j == 0)
                     echo " bgcolor=\"#FF9E5E\"><font size=\"2\">".$ma[$i][0]."</font></td>";
                   else if($ma[$i][15] == 1 AND $j == 0)
                     echo " bgcolor=\"#FFFF6C\"><font size=\"2\">".$ma[$i][0]."</font></td>";
                   else if(($ma[$i][15] == 5 OR $ma[$i][15] == Null) AND $j == 0 AND $i%2 ==0)
                     echo" class=\"celda\">".$ma[$i][0]."</td> ";
                   else if(($ma[$i][15] == 5 OR $ma[$i][15] == Null) AND $j == 0 AND $i%2 ==1)
                     echo" class=\"celda2\">".$ma[$i][0]."</td> ";
                }//fin else $i == 0
           }//fin for j
            echo "</tr>\n";
        }//fin for i
        echo "</table>";
    }
}
//otra prueba de color
Class Tabla_color2//Esta clase permite publicar una tabla con el contenido de una nmatriz de dos dimensiones
{
    //Atributos
    var $matriz; //la matriz que va ha ser publicada

    //Metodos
    function Tabla_color2($ma)//Constructor de la clase
    {
        echo "<table border=\"0\" width=\"100%\" bgcolor=\"#E6E6DC\">\n";
        $this -> matriz = $ma;
        for($i = 0; $i < sizeof($ma); $i++)
        {
            echo "<tr>\n";
            for($j = 0; $j < sizeof($ma[0]); $j++)
            {
                echo "<td";
                if($i == 0)
                    echo" class=\"etiqueta\">".$ma[$i][$j]."</td> ";
                else
                {
                   if($i%2 ==0 AND $j != 1 AND $j != 5)
                       echo" class=\"celda\">".$ma[$i][$j]."</td> ";
                   else if($i%2 == 1 AND $j != 1 AND $j != 5)
                       echo" class=\"celda2\">".$ma[$i][$j]."</td> ";
                   else if($ma[$i][7] == 4 AND $j == 1)
                     echo " bgcolor=\"#DDBBFF\"><font size=\"2\">".$ma[$i][1]."</font></td>";
                   else if($ma[$i][7] == 3 AND $j == 1)
                     echo " bgcolor=\"#FF8891\"><font size=\"2\">".$ma[$i][1]."</font></td>";
                   else if($ma[$i][7] == 2 AND $j == 1)
                      echo " bgcolor=\"#FF9E5E\"><font size=\"2\">".$ma[$i][1]."</font></td>";
                   else if($ma[$i][7] == 1 AND $j == 1)
                      echo " bgcolor=\"#FFFF6C\"><font size=\"2\">".$ma[$i][1]."</font></td>";
                   else if($ma[$i][7] == 4 AND $j == 5)
                     echo " bgcolor=\"#DDBBFF\"><font size=\"2\">".$ma[$i][5]."</font></td>";
                   else if($ma[$i][7] == 3 AND $j == 5)
                     echo " bgcolor=\"#FF8891\"><font size=\"2\">".$ma[$i][5]."</font></td>";
                   else if($ma[$i][7] == 2 AND $j == 5)
                      echo " bgcolor=\"#FF9E5E\"><font size=\"2\">".$ma[$i][5]."</font></td>";
                   else if($ma[$i][7] == 1 AND $j == 5)
                      echo " bgcolor=\"#FFFF6C\"><font size=\"2\">".$ma[$i][5]."</font></td>";
                }//fin else $i == 0
           }//fin for j
            echo "</tr>\n";
        }//fin for i
        echo "</table>";
    }
}

class Tabla_mensaje
{
    //Atributos
    var $titulo,//EL titulo del mensaje
        $mensaje;//texto del mansaje

    //Metodos
    function Tabla_mensaje($ti, $me)
    {
        $this -> titulo = $ti;
        $this -> mensaje = $me;

        echo "<table width=\"620\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#257038\">
               <tr>
                <td align=\"right\" valign=\"middle\" class=\"titulocentral\"><font color=\"#FFFFFF\"><b>$ti&nbsp;</font></b></td>
               </tr>
               <tr>
                <td bgcolor=\"#FFFFFF\" align=\"center\" valign=\"middle\">$me</td>
               </tr>
              </table>";
    }
}


?>

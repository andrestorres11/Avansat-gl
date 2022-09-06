<?php

class Encabezado
{
        var $conexion,
            $titulo,
	    $inicial;

        function __construct($tit,$con,$ini ='0')
        {
                $this -> conexion = $con;
                $this -> titulo = $tit;
		$this -> inicial = $ini;
        }

        function Mostrar($query)
        {
                $consulta = new Consulta($query, $this -> conexion);
                $enc = $consulta -> ret_objeto();
                $this -> Imprimir($enc);
        }

        function Imprimir($obj)
        {
                echo "<style type=\"text/css\">
                        #capainicio { position:relative; }
                        #capaexpansion { position:relative; display:none; }
                      </style>\n";
                echo "<SCRIPT src=\"../".DIR_APLICA_CENTRAL."/js/formexp.js\" type=\"text/javascript\"></SCRIPT>";
                echo "<body";
		if($this -> inicial)
			echo " onLoad=\"expandir_formulario()\"";
		echo ">";
                echo "<script>
                        function expandir_formulario()
                        {
                                xDisplay('capaexpansion', 'block')
                        }
                        function contraer_formulario()
                        {
                                xDisplay('capaexpansion', 'none')
                        }
                      </script>";
                echo "<div id=\"capainicio\">
                        <a href=\"#\" onClick=\"expandir_formulario();\">
                        <img src=\"../".DIR_APLICA_CENTRAL."/imagenes/enca_expandir.gif\" alt=\"Expandir Encabezado\" border=\"0\">
                        </a>
                      </div>";
                echo "<!-- Capa 1-->
                      <div id=\"capaexpansion\">";
                        $formulario = new Formulario ("index.php","post","".$this -> titulo."","form_encabezado");
                        $formulario -> nueva_tabla();
                        foreach( $obj as $k => $v )
                        {
                                echo "<td class=\"celda2\"><b>".$k."</b></td>\n";
                                echo "<td class=\"celda\">".$v."</td></tr><tr>";
                        }
                        //echo "</tr></table>";
                        $formulario -> nueva_tabla();
                        $formulario -> cerrar();
                echo "<a href=\"#\" onClick=\"contraer_formulario();\">
                      <img src=\"../".DIR_APLICA_CENTRAL."/imagenes/enca_contraer.gif\" alt=\"Expandir Encabezado\" border=\"0\">
                      </a>";
                echo "</div>
                      <!-- FIN DE MUESTRA DE CAPAS-->";
                echo "</body>";
        }

}
?>
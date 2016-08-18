<?php

class Proc_perfil
{

    var $conexion,
            $cod_aplica,
            $usuario;

    function __construct($co, $us, $ca)
    {

        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $this->principal();
    }

    function principal()
    {
        if (!isset($GLOBALS[opcion]))
            $this->Formulario1();
        else
        {
            switch ($GLOBALS[opcion])
            {
                case "1":
                    $this->Formulario2();
                    break;
                case "2":
                    $this->Insertar();
                    break;
            }//FIN SWITCH
        }// FIN ELSE GLOBALS OPCION
    }

    function Formulario1()
    {
        $datos_usuario = $this->usuario->retornar();

        $query = "SELECT MAX(a.cod_perfil)
	       	   FROM " . BASE_DATOS . ".tab_genera_perfil a
	      	  WHERE a.cod_perfil != " . COD_PERFIL_SUPERUSR . "
	    	";

        $consulta = new Consulta($query, $this->conexion);
        $consecut = $consulta->ret_matriz();

        $query = "SELECT a.cod_servic, a.nom_servic
               FROM " . CENTRAL . ".tab_genera_servic a LEFT JOIN
		    		" . CENTRAL . ".tab_servic_servic b ON
		    		a.cod_servic = b.cod_serhij
              WHERE b.cod_serhij IS NULL
                    GROUP BY 1 ORDER BY 1";

        $consulta = new Consulta($query, $this->conexion);
        $servpadr = $consulta->ret_matriz();

        $consecut[0][0] += 1;

        echo "<script language=\"JavaScript\" src=\"./lib/js/perfil.js\"></script>\n";

        $formulario = new Formulario("index.php", "post", "INSERTAR PERFIL", "form_princi");
        $formulario->nueva_tabla();
        $formulario->linea("INFORMACION BASICA DEL PERFIL", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->texto("Codigo", "text", "cod\" disabled", 1, 50, 20, "", "" . $consecut[0][0] . "");
        $formulario->texto("Nombre", "text", "nom", 1, 50, 100, "", "");

        $formulario->nueva_tabla();
        $formulario->linea("SELECCION DE PERMISOS", 1, "t2");
        $formulario->nueva_tabla();

        $l = 0;

        for ($i = 0; $i < sizeof($servpadr); $i++)
        {
            $query = "SELECT a.cod_servic,a.nom_servic
		        FROM " . CENTRAL . ".tab_genera_servic a,
			         " . CENTRAL . ".tab_servic_servic b
		   	   WHERE a.cod_servic = b.cod_serhij AND
			 		 b.cod_serpad = '" . $servpadr[$i][0] . "'
		 ";

            $consulta = new Consulta($query, $this->conexion);
            $servhijo = $consulta->ret_matriz();


            $formulario->linea("", 1, "i");
            $formulario->linea("Modulo :: " . $servpadr[$i][1] . "", 1, "t2");

            if (!$servhijo)
            {
                $formulario->caja("" . $servpadr[$i][1] . "", "permi[$l]", $servpadr[$i][0], 0, 1);
                $l++;
            }

            for ($j = 0; $j < sizeof($servhijo); $j++)
            {
                $query = "SELECT a.cod_servic, a.nom_servic
                 FROM " . CENTRAL . ".tab_genera_servic a,
                      " . CENTRAL . ".tab_servic_servic b
                WHERE a.cod_servic = b.cod_serhij AND
			  		  b.cod_serpad = " . $servhijo[$j][0];

                $consulta = new Consulta($query, $this->conexion);
                $sniveles = $consulta->ret_matriz();

                if ($sniveles)
                {

                    $formulario->linea("", 1, "i");
                    $formulario->linea(">>> SubNivel :: " . $servhijo[$j][1] . "", 1, "h");

                    for ($k = 0; $k < sizeof($sniveles); $k++)
                    {
                        if (($k + 1) % 2 == 0)
                            $formulario->caja("" . $sniveles[$k][1] . "", "permi[$l]", $sniveles[$k][0], 0, 1);
                        else
                            $formulario->caja("" . $sniveles[$k][1] . "", "permi[$l]", $sniveles[$k][0], 0, 0);
                        $l++;
                    }
                }
                else
                {
                    if (($j + 1) % 2 == 0)
                        $formulario->caja("" . $servhijo[$j][1] . "", "permi[$l]", $servhijo[$j][0], 0, 1);
                    else
                        $formulario->caja("" . $servhijo[$j][1] . "", "permi[$l]", $servhijo[$j][0], 0, 0);
                    $l++;
                }
            }
        }

        $formulario->nueva_tabla();
        $formulario->oculto("usuario", "$usuario", 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("opcion", 1, 0);
        $formulario->oculto("cod_perfil", "" . $consecut[0][0] . "", 0);
        $formulario->oculto("cod_servic", $GLOBALS["cod_servic"], 0);

        $formulario->boton("Insertar", "button\" onClick=\"insertar()", 0);
        $formulario->cerrar();
    }

    function Formulario2()
    {
        $query = "SELECT a.cod_filtro,a.nom_filtro,a.cod_queryx
               FROM " . CENTRAL . ".tab_genera_filtro a
             		ORDER BY 1
            ";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta->ret_matriz();

        $inicio[0][0] = 0;
        $inicio[0][1] = '-';

        //responsable
        $query = "SELECT a.cod_respon, a.nom_respon
                FROM ".BASE_DATOS.".tab_genera_respon a
               WHERE a.ind_activo = '1'";
        $query .= " ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $respon = $consulta->ret_matriz('i');
                
        $responsable = array_merge( $inicio, $respon );
        
        echo "<script language=\"JavaScript\" src=\"js/perfil.js\"></script>\n";
        $formulario = new Formulario("index.php", "post", "INSERTAR PERFILES", "form_ins");

        $formulario->nueva_tabla();
        $formulario->linea("Informaci&oacute;n Base del Perfil", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->linea("Codigo", 0, "t");
        $formulario->linea($GLOBALS[cod_perfil], 1, "i");
        $formulario->linea("Nombre", 0, "t");
        $formulario->linea($GLOBALS[nom], 1, "i");
        
        $formulario->linea("Responsable", 0, "t");
        $formulario->lista_titulo("", "cod_respon", $responsable, 1);
        //$formulario->linea("--> Responsable =)", 1, "i");

        $formulario->nueva_tabla();
        $formulario->linea("Servicios", 1, "t2");

        $formulario->nueva_tabla();

        $filtrosel = $GLOBALS[codigos];

        for ($i = 0; $i < sizeof($matriz); $i++)
        {
            if ($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
            {
                $formulario->caja("" . $matriz[$i][1] . "", "seleccfil[$i]\" disabled ", "" . $matriz[$i][0] . "", 1, 0);
                $formulario->oculto("seleccion[$i]", $matriz[$i][0], 0);
            }
            else if ($filtrosel[0])
                $formulario->caja("" . $matriz[$i][1] . "", "seleccion[$i]", "" . $matriz[$i][0] . "", 0, 0);
            else
                $formulario->caja("" . $matriz[$i][1] . "", "seleccion[$i]\" disabled ", "" . $matriz[$i][0] . "", 0, 0);

            if ($matriz[$i][2])
            {
                $query = $querysel = $matriz[$i][2];

                if ($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_AGENCI)
                    $query .= " AND c.cod_transp = '" . $filtrosel[0] . "'";

                //if($matriz[$i][0] != COD_FILTRO_EMPTRA)
                //$query .= " AND cod_transp = '".$filtrosel[0]."'";

                $query .= " GROUP BY 1 ORDER BY 2";

                $consulta = new Consulta($query, $this->conexion);
                $porfiltro = $consulta->ret_matriz();

                $matriz1 = array_merge($inicio, $porfiltro);

                if ($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
                {
                    $querysel .= " AND a.cod_tercer = '" . $filtrosel[0] . "'";
                    $querysel .= " GROUP BY 1 ORDER BY 2";

                    $consulta = new Consulta($querysel, $this->conexion);
                    $portrans = $consulta->ret_matriz();

                    $matriz1 = array_merge($portrans, $matriz1);
                }

                if ($matriz[$i][0] == COD_FILTRO_EMPTRA)
                    $formulario->lista_titulo("", "codigos[$i]\" onChange=\"form_ins.submit()", $matriz1, 1);
                else
                    $formulario->lista_titulo("", "codigos[$i]", $matriz1, 1);
            }
        }

        $servicios = $GLOBALS[permi];
        $servicios = array_merge($servicios);
		
		
		$formulario->nueva_tabla();
        $formulario->linea( "Filtro Novedades", 1, "t2" );
		
		$novedades = $this -> getNovedades();
		
		$end = "";
		$i = 0;
		
		if( $novedades )
		{
			$formulario->nueva_tabla();
			foreach( $novedades as $row )
			{				
				if( $_POST[sel_todos] ) 
					$checked = 1;
				
				$formulario->caja( ucwords( strtolower( $row[1] ) ), "novedad[$i]", $row[0], $checked, $end );
				$i++;
				
				if( $i % 2 != 0 ) $end = 1;
				else $end = 0;				
			}
			
			$formulario->caja( "Seleccionar Todos", "sel_todos\" onChange=\"form_ins.submit()", 1, $checked, $end );
		}

        $formulario->nueva_tabla();
        $formulario->oculto("opcion", $GLOBALS[opcion], 0);
        $formulario->oculto("cod_servic", $GLOBALS["cod_servic"], 0);
        $formulario->oculto("cod_perfil", $GLOBALS[cod_perfil], 0);
        $formulario->oculto("nom", $GLOBALS[nom], 0);
        $formulario->oculto("max_ser", "" . sizeof($matriz) . "", 0);

        for ($i = 0; $i < sizeof($servicios); $i++)
            $formulario->oculto("permi[$i]", $servicios[$i], 0);

        $formulario->oculto("window", "central", 0);
        $formulario->boton("Insertar", "button\" onClick=\"if(confirm('Esta Seguro de Insertar el Perfil.?')){form_ins.opcion.value = 2; form_ins.submit();}", 0);
        $formulario->cerrar();
    }
	
	function getNovedades()
    {
        $query = "SELECT a.cod_noveda, a.nom_noveda
				  FROM " . BASE_DATOS . ".tab_genera_noveda a
				  ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta -> ret_matriz( "i" );
		
		return $matriz;
    }

    function Insertar()
    {
        $fec_actual = date("Y-m-d H:i:s");
        
        // echo "<pre>";
        // print_r( $_REQUEST );
        // echo "</pre>";
        // die();
        
        //reasignacion de variables
        $servicios = $GLOBALS[permi];
        $filtros = $GLOBALS[seleccion];
        $codigos = $GLOBALS[codigos];

        $nuevo_consec = $GLOBALS[cod_perfil];
        
        $_REQUEST['cod_respon'] = $_REQUEST['cod_respon'] != '' ? $_REQUEST['cod_respon'] : 'NULL' ;

        $query = "INSERT INTO " . BASE_DATOS . ".tab_genera_perfil
   						 (cod_perfil,nom_perfil,cod_respon,usr_creaci,fec_creaci)
                  VALUES (" . $nuevo_consec . ",'" . $GLOBALS[nom] . "', '".$_REQUEST['cod_respon']."',
                     	  '" . $this->usuario->cod_usuari . "','" . $fec_actual . "')";

        //die();
        $consulta = new Consulta($query, $this->conexion, "BR");

        $query = "INSERT INTO " . BASE_DATOS . ".tab_aplica_perfil
                  VALUES ('" . $this->cod_aplica . "'," . $nuevo_consec . ") ";

        $consulta = new Consulta($query, $this->conexion, "R");

        for ($i = 0; $i < sizeof($servicios); $i++)
        {
            $query = "INSERT INTO " . BASE_DATOS . ".tab_perfil_servic
                 VALUES (" . $nuevo_consec . "," . $servicios[$i] . ") ";

            $consulta = new Consulta($query, $this->conexion, "R");

            $bandera1 = 0;
            $hijo = $servicios[$i];

            $cont = 0;

            while (!$bandera1)
            {
                $query = "SELECT a.cod_serpad,b.nom_servic
                      FROM " . CENTRAL . ".tab_servic_servic a,
                           " . CENTRAL . ".tab_genera_servic b
                     WHERE a.cod_serpad = b.cod_servic AND
                           a.cod_serhij = '" . $hijo . "' ";

                $consulta = new Consulta($query, $this->conexion);
                $matriz1 = $consulta->ret_matriz();

                if (!sizeof($matriz1))
                    break;
                else
                {
                    $query = "SELECT cod_servic
                         FROM " . BASE_DATOS . ".tab_perfil_servic
                        WHERE cod_servic = '" . $matriz1[0][0] . "' AND
                              cod_perfil = '" . $nuevo_consec . "' ";

                    $consulta = new Consulta($query, $this->conexion);
                    $matriz2 = $consulta->ret_matriz();

                    if (!sizeof($matriz2))
                    {
                        $query = "INSERT INTO " . BASE_DATOS . ".tab_perfil_servic
                         VALUES ('$nuevo_consec','" . $matriz1[0][0] . "') ";

                        $consulta = new Consulta($query, $this->conexion, "R");

                        $hijo = $matriz1[0][0];
                    }//fin if
                    else
                        break;
                }//fin if
            }//fin while
        }//fin for

        for ($i = 0; $i < $GLOBALS[max_ser]; $i++)
        {
            if ($filtros[$i] != Null)
            {
                //query de insercion
                $query = "INSERT INTO " . BASE_DATOS . ".tab_aplica_filtro_perfil
                 VALUES ('" . COD_APLICACION . "','" . $filtros[$i] . "',
                         '$nuevo_consec','" . $codigos[$i] . "') ";

                $consulta = new Consulta($query, $this->conexion, "R");
            }//fin if $filtros[$i]
        }//fin for $i
		
		//Filtro de Novedades.
		$novedades = $_POST[novedad];
		
		if( $novedades )
		foreach( $novedades as $row )
		{
			$insert = "INSERT INTO  " . BASE_DATOS . ".tab_perfil_noveda 
						(
							cod_perfil , cod_noveda
						)
						VALUES 
						(
							'$GLOBALS[cod_perfil]',  '$row'
						)";
			
			$consulta = new Consulta( $insert, $this -> conexion, "R" );
		}		

        if ($insercion = new Consulta("COMMIT", $this->conexion))
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $GLOBALS[cod_servic] . " \"target=\"centralFrame\">Insertar Otro Perfil</a></b>";

            $mensaje = "El Perfil <b>" . $GLOBALS[nom] . "</b> Se Inserto con Exito" . $link_a;
            $mens = new mensajes();
            $mens->correcto("INSERTAR PERFILES", $mensaje);
        }
    }

}

//FIN CLASE PROC_PERFIL



$proceso = new Proc_perfil($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
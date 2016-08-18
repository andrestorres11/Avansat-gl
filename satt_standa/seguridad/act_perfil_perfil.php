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
            $this->Listado();
        else
        {
            switch ($GLOBALS[opcion])
            {
                case "1":
                    $this->Formulario1();
                    break;
                case "2":
                    $this->Formulario2();
                    break;
                case "3":
                    $this->Actualizar();
                    break;
            }//FIN SWITCH
        }// FIN ELSE GLOBALS OPCION
    }

    function Listado()
    {
        $datos_usuario = $this->usuario->retornar();

        $query = "SELECT a.cod_perfil,a.nom_perfil
               FROM " . BASE_DATOS . ".tab_genera_perfil a
              WHERE 1
            ";

        if ($datos_usuario["cod_perfil"] != COD_PERFIL_SUPERUSR && $datos_usuario["cod_perfil"] != COD_PERFIL_ADMINIST)
            $query .= " AND a.cod_perfil != " . COD_PERFIL_SUPERUSR . "";
        if ($datos_usuario["cod_perfil"] != COD_PERFIL_ADMINIST)
            $query .= " AND a.cod_perfil != " . COD_PERFIL_ADMINIST . "";

        $query .= " ORDER BY 1";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta->ret_matriz();

        $formulario = new Formulario("index.php", "post", "LSITADO DE PERFILES", "form_ins");

        $formulario->nueva_tabla();
        $formulario->linea("Se Econtro un Total de " . sizeof($matriz) . " Perfil(es)", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->linea("Codigo", 0, "t");
        $formulario->linea("Nombre", 1, "t");

        for ($i = 0; $i < sizeof($matriz); $i++)
        {
            $matriz[$i][0] = "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&opcion=1&perfil=" . $matriz[$i][0] . " \"target=\"centralFrame\">" . $matriz[$i][0] . "</a>";

            $formulario->linea($matriz[$i][0], 0, "i");
            $formulario->linea($matriz[$i][1], 1, "i");
        }

        $formulario->nueva_tabla();
        $formulario->oculto("opcion", $GLOBALS[opcion], 0);
        $formulario->oculto("cod_servic", $GLOBALS["cod_servic"], 0);
        $formulario->oculto("window", "central", 0);
        $formulario->cerrar();
    }

    function Formulario1()
    {
        $datos_usuario = $this->usuario->retornar();

        $query = "SELECT a.cod_perfil,a.nom_perfil, a.cod_respon
	       	   FROM " . BASE_DATOS . ".tab_genera_perfil a
	      	  WHERE a.cod_perfil = " . $GLOBALS[perfil] . "
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

        $formulario = new Formulario("index.php", "post", "ACTUALIZAR PERFIL", "form_princi");
        $formulario->nueva_tabla();
        $formulario->oculto("cod_respon", $consecut[0]['cod_respon'], 0);
        $formulario->linea("Informaci&oacute;n B&aacute;sica del Perfil", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->texto("Codigo", "text", "cod\" disabled", 1, 50, 20, "", $consecut[0][0]);
        $formulario->texto("Nombre", "text", "nom", 1, 50, 100, "", $consecut[0][1]);

        $formulario->nueva_tabla();
        $formulario->linea("Selecci&oacute;n de Permisos", 1, "t2");
        $formulario->nueva_tabla();

        $l = 0;

        for ($i = 0; $i < sizeof($servpadr); $i++)
        {
            $selecc = 0;

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
                $query = "SELECT a.cod_perfil
			     FROM " . BASE_DATOS . ".tab_perfil_servic a
			    WHERE a.cod_servic = " . $servpadr[$i][0] . " AND
			   		  a.cod_perfil = " . $GLOBALS[perfil] . "
			  ";

                $consulta = new Consulta($query, $this->conexion);
                $permasig = $consulta->ret_matriz();

                if ($permasig)
                    $selecc = 1;

                $formulario->caja("" . $servpadr[$i][1] . "", "permi[$l]", $servpadr[$i][0], $selecc, 1);
                $l++;
            }

            for ($j = 0; $j < sizeof($servhijo); $j++)
            {
                $selecc = 0;

                $query = "SELECT a.cod_servic, a.nom_servic
                 FROM " . CENTRAL . ".tab_genera_servic a,
                      " . CENTRAL . ".tab_servic_servic b
                WHERE a.cod_servic = b.cod_serhij AND
			  		  b.cod_serpad = " . $servhijo[$j][0];

                $consulta = new Consulta($query, $this->conexion);
                $sniveles = $consulta->ret_matriz();

                $query = "SELECT a.cod_perfil
			     FROM " . BASE_DATOS . ".tab_perfil_servic a
			    WHERE a.cod_servic = " . $servhijo[$j][0] . " AND
			   		  a.cod_perfil = " . $GLOBALS[perfil] . "
			  ";

                $consulta = new Consulta($query, $this->conexion);
                $permasig = $consulta->ret_matriz();

                if ($permasig)
                    $selecc = 1;

                if ($sniveles)
                {
                    $formulario->linea("", 1, "i");
                    $formulario->linea(">>> SubNivel :: " . $servhijo[$j][1] . "", 1, "h");

                    for ($k = 0; $k < sizeof($sniveles); $k++)
                    {
                        $selecc = 0;

                        $query = "SELECT a.cod_perfil
			        FROM " . BASE_DATOS . ".tab_perfil_servic a
			       WHERE a.cod_servic = " . $sniveles[$k][0] . " AND
			   		     a.cod_perfil = " . $GLOBALS[perfil] . "
			     ";

                        $consulta = new Consulta($query, $this->conexion);
                        $permasig = $consulta->ret_matriz();

                        if ($permasig)
                            $selecc = 1;

                        if (($k + 1) % 2 == 0)
                            $formulario->caja("" . $sniveles[$k][1] . "", "permi[$l]", $sniveles[$k][0], $selecc, 1);
                        else
                            $formulario->caja("" . $sniveles[$k][1] . "", "permi[$l]", $sniveles[$k][0], $selecc, 0);
                        $l++;
                    }
                }
                else
                {
                    if (($j + 1) % 2 == 0)
                        $formulario->caja("" . $servhijo[$j][1] . "", "permi[$l]", $servhijo[$j][0], $selecc, 1);
                    else
                        $formulario->caja("" . $servhijo[$j][1] . "", "permi[$l]", $servhijo[$j][0], $selecc, 0);
                    $l++;
                }
            }
        }

        $formulario->nueva_tabla();
        $formulario->oculto("usuario", "$usuario", 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("opcion", 2, 0);
        $formulario->oculto("cod_perfil", $GLOBALS[perfil], 0);
        $formulario->oculto("cod_servic", $GLOBALS["cod_servic"], 0);

        $formulario->boton("Aceptar", "button\" onClick=\"actualizar()", 0);
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

        $query = "SELECT a.cod_filtro,a.clv_filtro
   		       FROM " . BASE_DATOS . ".tab_aplica_filtro_perfil a
   		      WHERE a.cod_perfil = " . $GLOBALS[cod_perfil] . "
   		    ";

        $consulta = new Consulta($query, $this->conexion);
        $asignado = $consulta->ret_matriz();

        if (!$GLOBALS[codigos])
        {
            for ($i = 0; $i < sizeof($matriz); $i++)
            {
                for ($j = 0; $j < sizeof($asignado); $j++)
                {
                    if ($matriz[$i][0] == $asignado[$j][0])
                        $filtrosel[$i] = $asignado[$j][1];
                }
            }
        }
        else
            $filtrosel = $GLOBALS[codigos];

        $inicio[0][0] = 0;
        $inicio[0][1] = '-';
        
        $query = "SELECT a.cod_respon, a.nom_respon
                    FROM ".BASE_DATOS.".tab_genera_respon a
                   WHERE a.ind_activo = '1' 
                     AND a.cod_respon = '".$_REQUEST['cod_respon']."'";

        $consulta = new Consulta( $query, $this -> conexion );
        $now_respon = $consulta->ret_matriz();
        
        $query = "SELECT a.cod_respon, a.nom_respon
                    FROM ".BASE_DATOS.".tab_genera_respon a
                   WHERE a.ind_activo = '1' 
                   ORDER BY 2";
        $consulta = new Consulta( $query, $this -> conexion );
        $respon = $consulta->ret_matriz();
        
        if( sizeof( $now_respon ) > 0 )
        {
          $responsable = array_merge( $now_respon, $inicio, $respon );
        }
        else
        {
          $responsable = array_merge( $inicio, $respon );
        }

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

        $formulario->nueva_tabla();
        $formulario->linea("Servicios", 1, "t2");

        $formulario->nueva_tabla();

        for ($i = 0; $i < sizeof($matriz); $i++)
        {
            if ($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
            {
                $formulario->caja("" . $matriz[$i][1] . "", "seleccfil[$i]\" disabled ", "" . $matriz[$i][0] . "", 1, 0);
                $formulario->oculto("seleccion[$i]", $matriz[$i][0], 0);
            }
            else if ($filtrosel[0])
            {
                if ($filtrosel[$i])
                    $formulario->caja("" . $matriz[$i][1] . "", "seleccion[$i]", "" . $matriz[$i][0] . "", 1, 0);
                else
                    $formulario->caja("" . $matriz[$i][1] . "", "seleccion[$i]", "" . $matriz[$i][0] . "", 0, 0);
            }
            else
                $formulario->caja("" . $matriz[$i][1] . "", "seleccion[$i]", "" . $matriz[$i][0] . "", 0, 0);

            if ($matriz[$i][2])
            {
                $query = $querysel = $matriz[$i][2];

                //if($matriz[$i][0] != COD_FILTRO_EMPTRA)
                //$query .= " AND cod_transp = '".$filtrosel[0]."'";

                if ($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_AGENCI)
                    $query .= " AND c.cod_transp = '" . $filtrosel[0] . "'";
                //echo "<hr>".$query;

                $consulta = new Consulta($query, $this->conexion);
                $porfiltro = $consulta->ret_matriz();

                $matriz1 = array_merge($inicio, $porfiltro);

                if ($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
                {
                    $querysel .= " AND a.cod_tercer = '" . $filtrosel[0] . "'";
                    $querysel .= " GROUP BY 1 ORDER BY 2";
                    //echo "<hr>transp_=".$querysel;
                    $consulta = new Consulta($querysel, $this->conexion);
                    $portrans = $consulta->ret_matriz();

                    $matriz1 = array_merge($portrans, $matriz1);
                }
                else if ($filtrosel[$i] && $matriz[$i][0] == COD_FILTRO_AGENCI)
                {

                    $querysel .= " AND a.cod_agenci = '" . $filtrosel[$i] . "'";
                    $querysel .= " GROUP BY 1 ORDER BY 2";

                    $consulta = new Consulta($querysel, $this->conexion);
                    $porasign = $consulta->ret_matriz();

                    $matriz1 = array_merge($porasign, $matriz1);
                }
                else if ($filtrosel[$i] && $matriz[$i][0] == COD_FILTRO_CONTRO)
                {
                    $querysel .= " AND a.cod_contro = '" . $filtrosel[$i] . "'";
                    $querysel .= " GROUP BY 1 ORDER BY 2";

                    $consulta = new Consulta($querysel, $this->conexion);
                    $porasign = $consulta->ret_matriz();

                    $matriz1 = array_merge($porasign, $matriz1);
                }
                else if ($filtrosel[$i])
                {
                    $querysel .= " AND a.cod_tercer = '" . $filtrosel[$i] . "'";
                    $querysel .= " GROUP BY 1 ORDER BY 2";

                    $consulta = new Consulta($querysel, $this->conexion);
                    $porasign = $consulta->ret_matriz();

                    $matriz1 = array_merge($porasign, $matriz1);
                }
                /* else if($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_AGENCI)
                  {
                  if( $filtrosel[0] )
                  $querysel .= " AND c.cod_transp = '".$filtrosel[0]."'";

                  $querysel .= " GROUP BY 1 ORDER BY 2";

                  $consulta = new Consulta($querysel, $this -> conexion);
                  $porasign = $consulta -> ret_matriz();

                  $matriz1 = array_merge($porasign,$matriz1);
                  } */

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
				$checked = $this -> getPermiso( $GLOBALS[cod_perfil], $row[0] );
				
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
        $formulario->boton("Actualizar", "button\" onClick=\"if(confirm('Esta Seguro de Actualizar el Perfil.?')){form_ins.opcion.value = 3; form_ins.submit();}", 0);
        $formulario->cerrar();
    }
	
	function getPermiso( $cod_perfil, $cod_noveda )
	{
		$query = "SELECT 1
				  FROM " . BASE_DATOS . ".tab_perfil_noveda a
				  WHERE a.cod_perfil = '$cod_perfil' AND
						a.cod_noveda = '$cod_noveda' ";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta -> ret_matriz( "i" );
		
		if( $matriz ) return true;
		
		return false;
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

    function Actualizar()
    {
		
		
        $fec_actual = date("Y-m-d H:i:s");

        //reasignacion de variables
        $servicios = $GLOBALS[permi];
        $filtros = $GLOBALS[seleccion];
        $codigos = $GLOBALS[codigos];

        $nuevo_consec = $GLOBALS[cod_perfil];
        
        $_REQUEST['cod_respon'] = $_REQUEST['cod_respon'] != '' ? $_REQUEST['cod_respon'] : 'NULL' ;

        $query = "UPDATE " . BASE_DATOS . ".tab_genera_perfil
   				SET nom_perfil = '" . $GLOBALS[nom] . "',
              cod_respon = '".$_REQUEST['cod_respon']."',
                    usr_modifi = '" . $this->usuario->cod_usuari . "',
                    fec_modifi = '" . $fec_actual . "'
              WHERE cod_perfil = " . $nuevo_consec . "
            ";

        $consulta = new Consulta($query, $this->conexion, "BR");

        $query = "DELETE FROM " . BASE_DATOS . ".tab_perfil_servic
   				   WHERE cod_perfil = " . $nuevo_consec . "
   		    ";

        $consulta = new Consulta($query, $this->conexion, "R");

        $query = "DELETE FROM " . BASE_DATOS . ".tab_aplica_filtro_perfil
   				   WHERE cod_perfil = " . $nuevo_consec . "
   		    ";

        $consulta = new Consulta($query, $this->conexion, "R");

        for ($i = 0; $i < sizeof($servicios); $i++)
        {
            $query = "INSERT INTO " . BASE_DATOS . ".tab_perfil_servic
                 VALUES (" . $nuevo_consec . "," . $servicios[$i] . ") ";

            $consulta = new Consulta($query, $this->conexion, "R");

            $bandera1 = 1;
            $hijo = $servicios[$i];

            while ($bandera1)
            {
                $query = "SELECT a.cod_serpad
                      FROM " . CENTRAL . ".tab_servic_servic a
                     WHERE a.cod_serhij = " . $hijo . "
                   ";

                $consulta = new Consulta($query, $this->conexion);
                $matriz1 = $consulta->ret_matriz();

                if (!$matriz1)
                    break;
                else
                {
                    $query = "SELECT cod_servic
                         FROM " . BASE_DATOS . ".tab_perfil_servic
                        WHERE cod_servic = " . $matriz1[0][0] . " AND
                              cod_perfil = " . $nuevo_consec . "
                      ";

                    $consulta = new Consulta($query, $this->conexion);
                    $matriz2 = $consulta->ret_matriz();

                    if (!$matriz2)
                    {
                        $query = "INSERT INTO " . BASE_DATOS . ".tab_perfil_servic
                              VALUES (" . $nuevo_consec . "," . $matriz1[0][0] . ")
                        ";

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
		$query = "DELETE FROM " . BASE_DATOS . ".tab_perfil_noveda
				  WHERE cod_perfil = '$nuevo_consec' ";

        $consulta = new Consulta( $query, $this->conexion, "R" );
		
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
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $GLOBALS[cod_servic] . " \"target=\"centralFrame\">Actualizar Otro Perfil</a></b>";

            $mensaje = "El Perfil <b>" . $GLOBALS[nom] . "</b> Se Actualizo con Exito" . $link_a;
            $mens = new mensajes();
            $mens->correcto("ACTUALIZAR PERFILES", $mensaje);
        }
    }

}

//FIN CLASE PROC_PERFIL



$proceso = new Proc_perfil($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
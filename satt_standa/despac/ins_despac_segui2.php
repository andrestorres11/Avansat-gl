<?php
session_start();

class Proc_segui
{

    var $conexion,
            $cod_aplica,
            $usuario;

    function __construct($co, $us, $ca)
    { 
      include( "InsertNovedad.inc" );
      include( "PlanRuta.inc" );
      $this->conexion = $co;
      $this->usuario = $us;
      $this->cod_aplica = $ca;
      $this->principal();
    }

    function principal()
    {
        switch ($GLOBALS[opcion])
        {
            case "1":
                $this->Datos();
                break;
            case "2":
                $this->Formulario1();
                break;
            case "3":
                $this->Insertar();
                break;
            case "4":
                $this->Cambio();
                break;
            case "5":
                $this->InsertarCambio();
                break;
            case "6":
                $this->UpdateIndifi();
                break;
            case "7":
                $this->darLlegad();
                break;
            case "8":
                $this->SearchCelu();
                break;
            case "9":
                $this->getCiudades();
                break;
            default:
			    header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                //$this->Listar();
                break;
        }
    }

    function SearchCelu()
    {
		/*echo "<pre>";
		print_r( $_GET );
		echo "</pre>";*/
		
        $datos_usuario = $this->usuario->retornar();
		
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
		
        $formulario = new Formulario("index.php", "post", "Informacion del Despacho", "form_ins");
		
        $query = "SELECT a.num_despac, a.cod_manifi, b.num_placax,
						 c.abr_tercer, d.abr_tercer
				  FROM " . BASE_DATOS . ".tab_despac_despac a,
					   " . BASE_DATOS . ".tab_despac_vehige b,
					   " . BASE_DATOS . ".tab_tercer_tercer c,
					   " . BASE_DATOS . ".tab_tercer_tercer d";
        
        if( $GLOBALS[docume] )
          $query .=  ", " . BASE_DATOS . ".tab_despac_destin e";
        
        if( $GLOBALS[viaje] || $GLOBALS[solici] || $GLOBALS[pedido] )
          $query .=  ", " . BASE_DATOS . ".tab_despac_sisext f";
          
        
        $query .= " WHERE a.num_despac = b.num_despac AND
						b.cod_transp = c.cod_tercer AND
						b.cod_conduc = d.cod_tercer AND
						a.fec_llegad IS NULL AND
						a.fec_salida IS NOT NULL AND
						b.ind_activo ='S' ";
		
		if( $GLOBALS[celu] )
			$query .= " AND a.con_telmov = '$GLOBALS[celu]' ";
		
		if( $GLOBALS[placa] )
			$query .= " AND b.num_placax = '$GLOBALS[placa]' ";
    
    if( $GLOBALS[docume] )
      $query .= " AND a.num_despac = e.num_despac AND e.num_docume = '".$GLOBALS[docume]."' ";
    
    if( $GLOBALS[viaje] )
      $query .= " AND a.num_despac = f.num_despac AND f.num_desext = '".$GLOBALS[viaje]."' ";
    
    if( $GLOBALS[solici] )
      $query .= " AND a.num_despac = f.num_despac AND f.num_solici = '".$GLOBALS[solici]."' ";
    
    if( $GLOBALS[pedido] )
      $query .= " AND a.num_despac = f.num_despac AND f.num_pedido = '".$GLOBALS[pedido]."' ";
    
           //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
         
        
        
        // echo '<hr />' . $query;
        $consulta = new Consulta($query, $this->conexion);
        $despac = $consulta->ret_matriz();
        if (sizeof($despac) == 1 || $GLOBALS[despac])
        {
            if (!$GLOBALS[despac])
                $GLOBALS[despac] = $despac[0][0];
            $listado_prin = new PlanRuta($GLOBALS[cod_servic], 2, $this->cod_aplica, $this->conexion);
            $listado_prin->Encabezado($GLOBALS[despac], $formulario, $datos_usuario);
            $listado_prin->PlanDeRuta($GLOBALS[despac], $formulario, 1, 0, 0, $datos_usuario, 1);
            $usuario = $datos_usuario["cod_usuari"];
            $formulario->nueva_tabla();
            $formulario->oculto("usuario", "$usuario", 0);

            $formulario->oculto("opcion\" id=\"opcionID", $GLOBALS[opcion], 0);
            $formulario->oculto("window", "central", 0);
            $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);
            $formulario->botoni("Atras", "javascript:history.go(-1)", 0);
        }
		elseif (sizeof($despac) == 0 || !$despac)
        {
			if( $GLOBALS[celu] )
				$mensaje .= "El Celular no se Encuentra Asignado a Ningun Despacho" . $link_a;
			
			if( $GLOBALS[placa] )
				$mensaje .= "La Placa no se Encuentra Asignada a Ningun Despacho" . $link_a;
				
			if( $GLOBALS[docume] )
				$mensaje .= "El Documento no se Encuentra Asignado a Ningun Despacho" . $link_a;
      
      if( $GLOBALS[viaje] )
				$mensaje .= "El N&uacute;mero del Viaje no se Encuentra Asignado a Ningun Despacho" . $link_a;
				
      if( $GLOBALS[solici] )
				$mensaje .= "El N&uacute;mero de solicitud no se Encuentra Asignado a Ningun Despacho" . $link_a;
				
      if( $GLOBALS[pedido] )
				$mensaje .= "El N&uacute;mero del Pedido no se Encuentra Asignado a Ningun Despacho" . $link_a;
				
            $mens = new mensajes();
            $mens->error("", $mensaje);
            die();
        }
        else
        {
            $formulario->nueva_tabla();
            $formulario->linea("Despachos Encontrados Para el Celular Numero: " . $GLOBALS[celu], 0, "t2");
            $formulario->nueva_tabla();
            $formulario->linea("Despacho", 0, "t2");
            $formulario->linea("Manifiesto", 0, "t2");
            $formulario->linea("Placa", 0, "t2");
            $formulario->linea("Transportadora", 0, "t2");
            $formulario->linea("Conductor", 1, "t2");
            foreach ($despac AS $des)
            {
                echo '<tr>';
                echo ' <td align="left" style="cursor:pointer" onclick="document.getElementById(\'despacID\').value=' . $des[0] . '; form_ins.submit();" class="celda_titulo2"><b>' . $des[0] . '</b></td>';
                $formulario->linea($des[1], 0, "t");
                $formulario->linea($des[2], 0, "t");
                $formulario->linea($des[3], 0, "t");
                $formulario->linea($des[4], 1, "t");
            }
        }
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);
        $formulario->oculto("despac\" id=\"despacID", $GLOBALS[despac], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 8, 0);
        $formulario->oculto("celu\" id=\"celuID", $GLOBALS[celu], 0);
        $formulario->oculto("celu\" id=\"despacID", $GLOBALS[celu], 0);
        $formulario->cerrar();
        echo '<tr><td><div id="AplicationEndDIV"></div>
              <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">

    		  <div id="filtros" >
    		  </div>

    		  <div id="result" >


    		  </div>
     		  </div><div id="alg"> <table></table></div></td></tr>';
        echo"";
    }

    function Listar()
    {
        $datos_usuario = $this->usuario->retornar();


        $GLOBALS_ADD[0]["campo"] = "alacla";
        $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
        $GLOBALS_ADD[1]["campo"] = "totregif";
        $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
        $GLOBALS_ADD[atras] = $_GET[atras];

        $listado_prin = new PlanRuta($GLOBALS[cod_servic], 1, $this->cod_aplica, $this->conexion);
        $listado_prin->ListadoPrincipal($datos_usuario, 0, "", 0, NULL, $GLOBALS_ADD);
    }

    function darLlegad()
    {
        $query = "UPDATE " . $base_datos . ".tab_despac_despac 
                   SET fec_llegad = NOW(),
                       obs_llegad = '" . $GLOBALS[obs_llegad] . "',
                       usr_modifi = '" . $GLOBALS[usuario] . "',
                       fec_modifi = NOW() 
                   WHERE num_despac = '" . $GLOBALS[despac] . "'";
        $update = new Consulta($query, $this->conexion, "BR");
        if ($update = new Consulta("COMMIT", $this->conexion))
        {
            $mensaje .= "<b>Se dio Llegada con exito al Despacho " . $GLOBALS[despac] . "</b>";
            $mens = new mensajes();
            $mens->correcto("REGISTRO DE NOVEDADES", $mensaje);
        }
        else
        {
            $mensaje .= "<b>Error  al dar Llegada al Despacho " . $GLOBALS[despac] . "</b>";
            $mens = new mensajes();
            $mens->error("REGISTRO DE NOVEDADES", $mensaje);
        }
        $GLOBALS_ADD[0]["campo"] = "alacla";
        $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
        $GLOBALS_ADD[1]["campo"] = "totregif";
        $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
        $GLOBALS_ADD[atras] = $_GET[atras];
        unset($GLOBALS[opcion]);
        //include( "../" . DIR_APLICA_CENTRAL . "/inform/inf_bandej_entrad.php" );
    }

    function Datos()
    {
        $datos_usuario = $this->usuario->retornar();
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery17.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquerygeo.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fecha.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/salida.js\"></script>\n";
        
        $formulario = new Formulario("index.php", "post", "Informacion del Despacho", "form_ins");
        $listado_prin = new PlanRuta($GLOBALS[cod_servic], 2, $this->cod_aplica, $this->conexion);
        $listado_prin->Encabezado($GLOBALS[despac], $formulario, $datos_usuario);
        $listado_prin->PlanDeRuta($GLOBALS[despac], $formulario, 1, 0, 0, $datos_usuario, 1, $GLOBALS[tie_ultnov]);//Jorge 120404
        
        
        /*** PROTOCOLOS EN EL SITIO ***/
        $mPronov = "SELECT a.cod_protoc, b.des_protoc, a.cod_noveda, UPPER( c.nom_noveda ) AS nom_noveda, a.fec_noveda, a.usr_creaci, a.obs_protoc
                      FROM ".BASE_DATOS.".tab_despac_pronov a,
                           ".BASE_DATOS.".tab_genera_protoc b,
                           ".BASE_DATOS.".tab_genera_noveda c
                     WHERE a.cod_protoc = b.cod_protoc
                       AND a.cod_noveda = c.cod_noveda
                       AND a.ind_aproba = 'R' 
                       AND a.num_despac = '".$GLOBALS['despac']."'";
        
        /*** PROTOCOLOS ANTES DEL SITIO ***/
        $mProcon = "SELECT a.cod_protoc, b.des_protoc, a.cod_noveda, UPPER( c.nom_noveda ) AS nom_noveda, a.fec_contro AS fec_noveda, a.usr_creaci,a.obs_protoc
                      FROM ".BASE_DATOS.".tab_despac_procon a,
                           ".BASE_DATOS.".tab_genera_protoc b,
                           ".BASE_DATOS.".tab_genera_noveda c
                     WHERE a.cod_protoc = b.cod_protoc
                       AND a.cod_noveda = c.cod_noveda
                       AND a.ind_aproba = 'R'
                       AND a.num_despac = '".$GLOBALS['despac']."'";
        
        $mSelect = $mPronov . " UNION " . $mProcon . " ORDER BY fec_noveda DESC, des_protoc ASC";
        
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_PROTOCO = $consulta -> ret_matriz();
        
        if( sizeof( $_PROTOCO ) > 0 )
        {
          // echo "<pre>";
          // print_r( $_PROTOCO );
          // echo "</pre>";
          $formulario->nueva_tabla();
          $formulario->linea("Protocolos Realizados", 1, "t2");
          $formulario->nueva_tabla();
          $formulario->linea("Protocolo", 0, "t");
          $formulario->linea("Descripción", 0, "t");
          $formulario->linea("Novedad", 0, "t");
          $formulario->linea("Fecha/Hora", 0, "t");
          $formulario->linea("Ejecutado Por", 1, "t");
          foreach( $_PROTOCO as $row )
          {
            $formulario->linea($row[1], 0, "i");
            $formulario->linea($row[6], 0, "i");
            $formulario->linea($row[3], 0, "i");
            $formulario->linea( $this -> ToDate( $row[4] ), 0, "i");
            $formulario->linea($row[5], 1, "i");
          }
        }
        
        
        $usuario = $datos_usuario["cod_usuari"];
        $formulario->nueva_tabla();
        $formulario->oculto("usuario", "$usuario", 0);
        $formulario->oculto("tie_ultnov\" id=\"tie_ultnovID", $GLOBALS[tie_ultnov], 0); //Jorge 120404
        $formulario->oculto("despac\" id=\"despacID", $GLOBALS[despac], 0);
        $formulario->oculto("opcion\" id=\"opcionID", $GLOBALS[opcion], 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);


        $formulario->botoni("Atras", "javascript:history.go(-1)", 0);
        $formulario -> botoni("PDF", "GeneratePDF()", 1 );
        echo "<table border=\"0\" width=\"100%\">"
        . "<tr>"
        . "<td align=\"center\">"
        . "<input type=\"button\" onClick=\"this.style.display='none';print();this.style.display='block';\" name=\"Imprimir\" value=\"Imprimir\">"
        . "</td>"
        . "</tr>"
        . "</table>";
        $formulario->cerrar();
        echo '<tr><td><div id="AplicationEndDIV"></div>
              <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">

    		  <div id="filtros" >
    		  </div>

    		  <div id="result" >


    		  </div>
     		  </div><div id="alg"> <table></table></div></td></tr>';
        echo"";
        
         echo '<script type="text/javascript">
          $(function() { 
             showMapOpen();
           });
         </script>';
    }

    function Formulario1()
    {
        
        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];

        $cod_perfil = $datos_usuario["cod_perfil"];

        //codigo de ruta
        $query = "SELECT a.cod_rutasx,b.cod_tipdes 
	 					 FROM  " . BASE_DATOS . ".tab_despac_vehige a,
						 			 " . BASE_DATOS . ".tab_despac_despac b	 
             WHERE a.num_despac = '" . $GLOBALS[despac] . "'
						 			 AND a.num_despac = b.num_despac";
        $consulta = new Consulta($query, $this->conexion);
        $rutax = $consulta->ret_matriz();
        $query = "SELECT cod_sitiox, CONVERT(nom_sitiox USING utf8) as nom_sitiox " .
                "FROM " . BASE_DATOS . ".vis_despac_sitio WHERE cod_sitiox <= 10 ";
        $consulta = new Consulta($query, $this->conexion);
        $sitios = $consulta->ret_matriz();
        $query = "SELECT cod_contro " .
                "FROM " . BASE_DATOS . ".tab_despac_noveda " .
                "WHERE num_despac = '" . $GLOBALS[despac] . "' AND " .
                "cod_contro = '" . $GLOBALS[codpc] . "'";
        $consulta = new Consulta($query, $this->conexion);
        $contro = $consulta->ret_matriz();

        $query = "SELECT cod_contro
               FROM " . BASE_DATOS . ".tab_despac_seguim
              WHERE ind_estado = '1'
              AND num_despac = '" . $GLOBALS[despac] . "'
           ORDER BY fec_planea DESC
           LIMIT 1";
        $consulta = new Consulta($query, $this->conexion);

        $lastpc = $consulta->ret_matriz();
        $lastpc = $lastpc[0]['cod_contro'];

        //trae la fecha actual
        $fec_actual = date("d-m-Y");
        $hor_actual = date("H:i:s");
//cantidad de tiempo agregar y tipo de servicio
        $query = "SELECT tie_conurb, tie_contro, cod_tipser " .
                "FROM " . BASE_DATOS . ".tab_transp_tipser " .
                "WHERE cod_transp='" . $GLOBALS[cod_transp] . "' AND 
			                num_consec= (SELECT MAX(num_consec) FROM " . BASE_DATOS . ".tab_transp_tipser
			                						 WHERE cod_transp='" . $GLOBALS[cod_transp] . "') ";
        $consulta = new Consulta($query, $this->conexion);
        $transpor = $consulta->ret_matriz();
        if ($GLOBALS[ind_virtua] == 0 && !$contro && $transpor[0][2] == 3)
        {
            if ($rutax[0][1] == 1)
            {
                $tiem = $transpor[0][0];
            }
            else
            {
                $tiem = $transpor[0][1];
            }
            if ($tiem == "")
                $tiem = 90;
        }

		$cod_perfil = $datos_usuario[cod_perfil];
		
		$select = "SELECT a.cod_noveda
				   FROM " . BASE_DATOS . ".tab_perfil_noveda a
				   WHERE a.cod_perfil = '$cod_perfil' ";
		
		$select = new Consulta( $select, $this -> conexion );
        $select = $select -> ret_matriz( "a" );
		
        //lista las novedadesecho 
        $query = " SELECT cod_noveda, UPPER( CONCAT( CONVERT( nom_noveda USING utf8), 
						  '', if (nov_especi = '1', '(NE)', '' ), 
						  if( ind_alarma = 'S', '(GA)', '' ), 
						  if( ind_manala = '1', '(MA)', '' ),
						  if( ind_tiempo = '1', '(ST)', '' ) )) , 
						  ind_tiempo
				   FROM " . BASE_DATOS . ".tab_genera_noveda 
				   WHERE 1 = 1 AND ind_visibl = '1'";
				   
        if ($datos_usuario["cod_perfil"] != COD_PERFIL_SUPERUSR && $datos_usuario["cod_perfil"] != COD_PERFIL_ADMINIST && $datos_usuario["cod_perfil"] != COD_PERFIL_SUPEFARO)
		{
			if( $datos_usuario["cod_perfil"]  != '689' )
            $query .=" AND cod_noveda !='" . CONS_NOVEDA_ACAEMP . "' ";
		}
        if ($transpor[0][2] == '1')
            $query .=" AND cod_noveda !='" . CONS_NOVEDA_ACAFAR . "' ";
        $query .=" ORDER BY 2 ASC";
        $consulta = new Consulta($query, $this->conexion);
        $novedades = $consulta->ret_matriz();
		
		if( $select )
		{
			$fil_noveda = array();
			
			for( $i = 0; $i < sizeof( $novedades ) ; $i++ )
			{
				for( $j = 0; $j < sizeof( $select ); $j++ )
				{
					if( $novedades[$i][cod_noveda] == $select[$j][cod_noveda] )
						$fil_noveda[] = $novedades[$i];
				}
			}
			
			$novedades = $fil_noveda;
		}
		
		
		
		
		
        if ($GLOBALS[noved])
        {
            $nove = $GLOBALS[noved];
            $GLOBALS[noved] = explode("-", $GLOBALS[noved]);
            if (is_array($GLOBALS[noved]))
                $GLOBALS[noved] = $GLOBALS[noved][0];
            else
                $GLOBALS[noved] = $GLOBALS[noved];
            $query = "SELECT cod_noveda,UPPER(CONCAT(CONVERT(nom_noveda USING utf8),'',if(nov_especi='1','(NE)',''),if(ind_alarma='S','(GA)',''),if(ind_manala='1','(MA)',''),if(ind_tiempo='1','(ST)','') )) 
                    ,obs_preted,ind_alarma,nov_especi,ind_tiempo
               FROM " . BASE_DATOS . ".tab_genera_noveda
               WHERE cod_noveda = '" . $GLOBALS[noved] . "' AND ind_visibl = '1' ";
            $consulta = new Consulta($query, $this->conexion);
            $novedades_a = $consulta->ret_matriz();
            if (!$novedades_a)
                $nove = "";
            else
                $nove = $novedades_a[0][0] . "-" . $novedades_a[0][1];
        }else
        {
            $nove = "";
        }

        //presenta por defecta la fecha actual
        if (!isset($GLOBALS[fecnov]))
            $GLOBALS[fecnov] = $fec_actual;
        if (!isset($GLOBALS[hornov]))
            $GLOBALS[hornov] = $hor_actual;
        echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        $limite_tiempo = date("Y-m-d H:i", mktime( date( "H" )+24, date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ));
        $limite_hora = date("H:i", mktime( date( "H" )+24, date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ));
        $limite_dia = date("Y-m-d", mktime( date( "H" )+24, date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ));
        
        echo '  <script>
                    
                    jQuery(function($) 
                    { 
                        $( "#date" ).datepicker(
                        {
                            onSelect: function( dateText, inst ) 
                            {
                            
                                if( '.$cod_perfil.' == 7 )
                                {
                                    var n_fecha = $( "#date" ).val() + " " + $( "#hora" ).val();
                                    
                                    //alert( n_fecha );
                                    
                                    if( n_fecha > "'.$limite_tiempo.'" )
                                    {
                                        //alert( "La maxima hora que puedes configurar es '.$limite_tiempo.'" );
                                        //$( "#hora" ).val( "'.$limite_hora.'" );
                                        //$( "#date" ).val( "'.$limite_dia.'" );
                                    }
                                }
                            }
                        });
                        
                        $( "#hora" ).timepicker(
                        {
                            timeFormat:"hh:mm",
                            showSecond: false,
                            onClose: function( dateText, inst ) 
                            {
                            
                                if( '.$cod_perfil.' == 7 )
                                {
                                    var n_fecha = $( "#date" ).val() + " " + dateText;
                                    
                                    //alert( n_fecha );
                                    
                                    if( n_fecha > "'.$limite_tiempo.'" )
                                    {
                                        //alert( "La maxima hora que puedes configurar es '.$limite_tiempo.'" );
                                        //$( "#hora" ).val( "'.$limite_hora.'" );
                                        //$( "#date" ).val( "'.$limite_dia.'" );
                                    }
                                }
                            }
                        });
        
                        $.mask.definitions["A"]="[12]";
                        $.mask.definitions["M"]="[01]";
                        $.mask.definitions["D"]="[0123]";
                        
                        $.mask.definitions["H"]="[012]";
                        $.mask.definitions["N"]="[012345]";
                        $.mask.definitions["n"]="[0123456789]";
                        
                        $( "#date" ).mask("Annn-Mn-Dn");
                        $( "#hora" ).mask("Hn:Nn");
  
    var sitios = 
            [';







        if ($sitios)
        {
            echo "\"Ninguna\"";
            foreach ($sitios as $row)
            {
                echo ", \"" . htmlentities($row[nom_sitiox]) . " \"";
            }
        }

        echo '];
		
			
			$( "#sitioID" ).autocomplete({
				source: "../satt_standa/despac/ins_despac_seguim.php?opcion=9",
				minLength: 4, 
				delay: 100
			});
			
      var novedades = 
			[';

        if ($novedades)
        {
            echo "\"Ninguna\"";
            foreach ($novedades as $row)
            {
                echo ", \"$row[0]-" . htmlentities($row[1]) . " \"";
            }
        }

        echo '];
		
			
			$( "#novedadID" ).autocomplete({
				source: novedades,
				delay: 100
			}).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
      
      $( "#novedadID" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
      });
      
     </script>';
        $formulario = new Formulario("index.php", "post", "Informacion del Despacho", "form_ins\" id=\"form_insID");

        $listado_prin = new PlanRuta($GLOBALS[cod_servic], 3, $this->cod_aplica, $this->conexion, 2);
        $listado_prin->Encabezado($GLOBALS[despac], $formulario, $datos_usuario);

        $inicio[0][0] = 0;
        $inicio[0][1] = '-';

        //trae el indicador de solicitud tiempo en novedad
        $query = "SELECT ind_tiempo
               FROM " . BASE_DATOS . ".tab_genera_noveda
               WHERE cod_noveda = '" . $nove . "'";
        $consulta = new Consulta($query, $this->conexion);
        $ind_tiempo = $consulta->ret_arreglo();

        //trae el indicador de Security Question
        /* $query = "SELECT ind_secque, num_secque, por_secque
          FROM ".BASE_DATOS.".tab_config_parame";

          $consulta = new Consulta($query, $this -> conexion);
          $indsec = $consulta -> ret_arreglo(); */

        /* echo '<pre>';
          print_r($indsec);
          echo '</pre>'; */

        $query = "SELECT  MAX(e.fec_noveda)

              FROM " . BASE_DATOS . ".tab_despac_vehige c," . BASE_DATOS . ".tab_despac_seguim d,

                   " . BASE_DATOS . ".tab_despac_noveda e

             WHERE c.num_despac = d.num_despac AND

                   c.num_despac = e.num_despac AND

                   c.num_despac = '$GLOBALS[despac]' ";


        $consulta = new Consulta($query, $this->conexion);
        $ultrep = $consulta->ret_matriz();
        
        /*** PROTOCOLOS EN EL SITIO ***/
        $mPronov = "SELECT a.cod_protoc, b.des_protoc, a.cod_noveda, UPPER( c.nom_noveda ) AS nom_noveda, a.fec_noveda, a.usr_creaci, a.obs_protoc
                      FROM ".BASE_DATOS.".tab_despac_pronov a,
                           ".BASE_DATOS.".tab_genera_protoc b,
                           ".BASE_DATOS.".tab_genera_noveda c
                     WHERE a.cod_protoc = b.cod_protoc
                       AND a.cod_noveda = c.cod_noveda
                       AND a.ind_aproba = 'R' 
                       AND a.num_despac = '".$GLOBALS['despac']."'";
        
        /*** PROTOCOLOS ANTES DEL SITIO ***/
        $mProcon = "SELECT a.cod_protoc, b.des_protoc, a.cod_noveda, UPPER( c.nom_noveda ) AS nom_noveda, a.fec_contro AS fec_noveda, b.usr_creaci,a.obs_protoc
                      FROM ".BASE_DATOS.".tab_despac_procon a,
                           ".BASE_DATOS.".tab_genera_protoc b,
                           ".BASE_DATOS.".tab_genera_noveda c
                     WHERE a.cod_protoc = b.cod_protoc
                       AND a.cod_noveda = c.cod_noveda
                       AND a.ind_aproba = 'R'
                       AND a.num_despac = '".$GLOBALS['despac']."'";
        
        $mSelect = $mPronov . " UNION " . $mProcon . " ORDER BY fec_noveda DESC, des_protoc ASC";
        
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_PROTOCO = $consulta -> ret_matriz();
        
        if( sizeof( $_PROTOCO ) > 0 )
        {
          // echo "<pre>";
          // print_r( $_PROTOCO );
          // echo "</pre>";
          $formulario->nueva_tabla();
          $formulario->linea("Protocolos Realizados", 1, "t2");
          $formulario->nueva_tabla();
          $formulario->linea("Protocolo", 0, "t");
          $formulario->linea("Descripción", 0, "t");
          $formulario->linea("Novedad", 0, "t");
          $formulario->linea("Fecha/Hora", 0, "t");
          $formulario->linea("Ejecutado Por", 1, "t");
          foreach( $_PROTOCO as $row )
          {
            $formulario->linea($row[1], 0, "i");
            $formulario->linea($row[6], 0, "i");
            $formulario->linea($row[3], 0, "i");
            $formulario->linea( $this -> ToDate( $row[4] ), 0, "i");
            $formulario->linea($row[5], 1, "i");
          }
        }
        
        $formulario->nueva_tabla();
        $formulario->linea("Asignaci&oacute;n de Novedad", 1, "t2");
        $formulario->nueva_tabla();
        $formulario->linea("Fecha", 0, "t2");
        $formulario->linea("Hora", 0, "t2");
        $formulario->linea("Novedad", 0, "t2");
        
        if ($ind_tiempo[0])
        {
            $formulario->linea("Tiempo Fecha/Hora", 0, "t2");
        }
        if ($GLOBALS[noved] == CONS_NOVEDA_CAMCEL)
            $formulario->linea("Celular", 0, "t2");
        $formulario->linea("Antes/Sitio", 0, "t2");
        if ($tiem)
            $formulario->linea("Adicion de Tiempo", 0, "t2");
        $formulario->linea("Sitio", 0, "t2");
        $formulario->linea("Observacion", 0, "t2");
        $formulario->linea("Habilitar Disponibilidad PAD", 1, "t2");
        echo "<td class='celda_info' >";
        echo "<input type='text' class='campo' style='bacground:none; border:0;' size='10' id='fecID' readonly='true' name='fec' value='" . date('Y-m-d') . "'>&nbsp;";
        echo "</td>";
        echo "<td class='celda_info' width='50px'>";
        echo "<input type='text' class='campo' style='bacground:none; border:0;' size='10' id='horID' readonly='true' name='hor' value='" . date('G:i') . "'>";
        echo "</td>";
        echo "<td class='celda_info' width='50px'>";
        echo "<input type='text' name='noved' id='novedadID' maxlength='50'  value='$nove'  size='50'>";        
        echo "</td>";
		
        //SI SOLICITA TIEMPO.
        if ($ind_tiempo[0])
        {
            $h = date('G');
            $m = date('i');
            if ($h <= 9)
                $h = "0" . $h;
            if ($m <= 9)
                $m = "0" . $m;
            echo "<td class='celda_info' >";
            echo "<input type='text' class='campo' size='10' id='date' name='date' value='" . date('Y-m-d') . "'> ";
            echo "<input type='text' class='campo' size='10' id='hora' name='hora' value='" . $h . ":" . $m . "'>";
            echo "</td>";
            //$formulario -> texto("TIEMPO DURACION ","text","tiem_duraci\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '0'){alert('La Cantidad No es Valida');this.value='';this.focus()}}else{this.value=''}\" id=\"duracion",1,4,4,"","");
        }
        if ($GLOBALS[noved] == CONS_NOVEDA_CAMCEL)
        {
            echo "<td class='celda_info' >";
            echo "<input type='text' name='celu' onChange='BlurNumeric(this);' id='celuID' maxlength='10'  size='9'>";
            echo "</td>";
        }
        echo "<td class='celda_info' >";
        echo "<select  id='sitID' name='sit' class='form_01' onblur='valSit()'>";
        
        //echo "<option value='0'>---</option>";
        if (!$contro)
            echo "<option value='A'>Antes</option>";
        echo "<option value='S'>Sitio</option>";
        echo "</select>";
        echo "</td>";
        if ($tiem)
        {
            echo "<td class='celda_info' width='50px'>";
            echo "<select  name='tiempo' id='tiemID' >";
            echo "<option value=''>--</option>";
            for ($i = 15; $i <= $tiem; $i++)
            {
                echo "<option value='$i'>$i</option>";
            }
            echo "</select>";
            echo "</td>";
        }
        echo "<td class='celda_info' >";
        if ($GLOBALS[ind_virtua] == 0 && !$contro)
            echo "<input type='text' name='sitio' id='sitioID' maxlength='50'  size='20'>";
        else
            echo "<input type='text' name='sitio' id='sitioID' maxlength='50'  readonly='true' value='" . $GLOBALS[pc] . "' size='20'>";

        echo "</td>";
        echo "<td class='celda_info' >";
        echo "<textarea name='obs' id='obsID' onkeyup='UpperText( $(this) )' cols='20' Rows='4'></textarea>";
        echo "<div style='font-family:Arial,Helvetica,sans-serif; font-size: 11px;' id='counter'></div>";
        echo "</td>";
        echo "<td class='celda_info' >";
        echo "<input type='checkbox' value='1' name='habPAD'>";
        echo "</td>";
        echo '<script>
              var limit = 2000;
              var nueva_longitud = 0;
              var text;
              $("#obsID").parent().find("#counter").html("Queda(n) <b>"+limit+"</b> Caracter(es) para Escribir");
              $("#obsID").keyup(function(){ 
                nueva_longitud = limit - $("#obsID").val().length;
                if( nueva_longitud < 0 )
                {
                   text = $("#obsID").val();
                   $("#obsID").val( text.substr( 0,limit ) );
                }
                else
                {
                  $("#obsID").parent().find("#counter").html("Queda(n)<b> "+nueva_longitud+"</b> Caracter(es) para Escribir");
                }
              });  

              $("#obsID").blur(function(){ 
                nueva_longitud = limit - $("#obsID").val().length;
                if( nueva_longitud < 0 )
                {
                   text = $("#obsID").val();
                   $("#obsID").val( text.substr( 0,limit ) );
                   $("#obsID").parent().find("#counter").html("Queda(n)<b> 0</b> Caracter(es) para Escribir");
                }
                else
                {
                   $("#obsID").parent().find("#counter").html("Queda(n)<b> "+nueva_longitud+"</b> Caracter(es) para Escribir");
                }
              });  
              
              $("#obsID").focus(function(){ 
                nueva_longitud = limit - $("#obsID").val().length;
                if( nueva_longitud < 0 )
                {
                   text = $("#obsID").val();
                   $("#obsID").val( text.substr( 0,limit ) );
                   $("#obsID").parent().find("#counter").html("Queda(n)<b> 0</b> Caracter(es) para Escribir");
                }
                else
                {
                   $("#obsID").parent().find("#counter").html("Queda(n)<b> "+nueva_longitud+"</b> Caracter(es) para Escribir");
                }
              });  
            </script>';


        if ($novedades_a[0][3] == "S")
        {
            $mQuery = "SELECT a.cod_transp
                 FROM " . BASE_DATOS . ".tab_despac_vehige a
                WHERE a.num_despac = " . $GLOBALS[despac] . "
              ";

            $mConsulta = new Consulta($mQuery, $this->conexion);
            $mTranspor = $mConsulta->ret_matriz();

            $mQuery = "SELECT a.nom_transp
             FROM " . CENTRAL . ".tab_mensaj_bdsata a
          WHERE a.cod_transp = '" . $mTranspor[0][0] . "' AND
              a.nom_bdsata = '" . BASE_DATOS . "' AND
              a.ind_estado = '1'
         ";

            $mConsulta = new Consulta($mQuery, $this->conexion);
            $mActivaEnv = $mConsulta->ret_matriz();

            if ($mActivaEnv)
            {
                $mArrayValueMen = array(
                    "despac" => $GLOBALS[despac],
                    "transp" => $mTranspor[0][0],
                    "bdsata" => BASE_DATOS,
                    "perfil" => $datos_usuario["cod_perfil"]
                );

                //$listado_prin  -> getMailAssignedNov( $formulario, $mArrayValueMen );
            }
        }

        $formulario->nueva_tabla();
        $formulario->oculto("usuario", "$usuario", 0);
        $formulario->oculto("tip_servic", $transpor[0][2], 0);
        $formulario->oculto("tie_ultnov", $GLOBALS[tie_ultnov], 0);//Jorge 120404
        $formulario->oculto("novedad", $GLOBALS[noved], 0);
        $formulario->oculto("cod_lastpc\" id=\"cod_lastpcID\" ", $lastpc, 0);
        $formulario->oculto("cod_contro\" id=\"cod_controID\" ", $GLOBALS['codpc'], 0);
        $formulario->oculto("cod_transp", $GLOBALS[cod_transp], 0);
        $formulario->oculto("ind_virtua", $GLOBALS[ind_virtua], 0);
        $formulario->oculto("nov_especi\" id=\"nov_especiID\" ", $novedades_a[0][nov_especi], 0);
        $formulario->oculto("despac\" id=\"despacID", $GLOBALS[despac], 0);
        $formulario->oculto("tercero", "$tercero", 0);
        $formulario->oculto("fecnov\" id=\"fecnovID", "$fec_actual", 0);
        $formulario->oculto("rutax\" id=\"rutaxID", $rutax[0][0], 0);
        $formulario->oculto("hornov\" id=\"hornovID", "$hor_actual", 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 2, 0);
        $formulario->oculto("pc\" id=\"pcID", $GLOBALS[pc], 0);
        $formulario->oculto("codpc", $GLOBALS[codpc], 0);
        $formulario->oculto("url_archiv\" id=\"url_archivID\"", "ins_despac_seguim.php", 0);
        $formulario->oculto("dir_aplica\" id=\"dir_aplicaID\"", DIR_APLICA_CENTRAL, 0);
        $formulario->oculto("ultrep", $ultrep[0][0], 0);
        $formulario->oculto("cod_transp\" id=\"cod_transpID", trim( $GLOBALS['cod_transp'] ), 0);
        
        //echo "--> ".$GLOBALS['cod_transp'];
        
        if ($ind_tiempo[0])
            $formulario->oculto("tiem", 1, 0);
        else
            $formulario->oculto("tiem", 0, 0);

        $formulario->oculto("despac", "$GLOBALS[despac]", 0);
        $formulario->oculto("ind_protoc\" id=\"ind_protocID", "no", 0);

        $formulario->nueva_tabla();

        $formulario->botoni("Aceptar", "aceptar_ins();", 0);

        $formulario->botoni("Borrar", "form_ins.reset()", 1);

        $formulario->cerrar();

        echo '<tr><td><div id="AplicationEndDIV"></div>
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">

    		  <div id="filtros" >
    		  </div>

    		  <div id="result" >


    		  </div>
     		  </div></table></td></tr>';
          echo '<div id="newPopupID"></div>';
          
        echo"";

        if ($GLOBALS[noved])
        {
            echo "<script language=\"JavaScript\">";
            echo "document.getElementById('sitID').focus()";
            echo "</script>";
        }
        else
        {
            echo "<script language=\"JavaScript\">";
            echo "document.getElementById('novedadID').focus()";
            echo "</script>";
        }
        
        if( $_REQUEST['noved'] != '' || $_REQUEST['noved'] != NULL )
        {
        echo '<script>
              var Standa = $("#dir_aplicaID").val();
              var cod_transp = $("#cod_transpID").val();
              var novedad = $("#novedadID").val();
              var cod_noveda = novedad.split("-")[0].trim();
              var attr_ = "option=ValidaProtocNoveda";
              var attr = "&standa="+Standa+"&cod_transp="+cod_transp+"&cod_noveda="+cod_noveda;
              $.ajax({
              type: "POST",
              url: "../"+ Standa +"/desnew/ajax_despac_novpro.php",
              data: attr_ + attr,
              async: false,
              success: function( datos )
              {
                if( datos == "y")
                {
                  $("#ind_protocID").val("yes");
                  ShowProtocNoveda(attr);
                }
              }
              });
              </script>';
        }
    }

    function ToDate( $fecha )
    {
      $_MESES = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                      '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
      $_FECHA = explode( " ", $fecha );
      $_DATE = explode( "-", $_FECHA[0] );
      
      return $_DATE[2]." de ".$_MESES[ $_DATE[1] ]." de ".$_DATE[0]." a las ".$_FECHA[1];
    }
    
    function Insertar()
    {
        ini_set('memory_limit', '64M');
        $datos_usuario = $this->usuario->retornar();
        $regist["email"] = $datos_usuario[usr_emailx];
        $regist["virtua"] = $GLOBALS['ind_virtua'];
        $regist["tip_servic"] = $GLOBALS['tip_servic'];
        $regist["celular"] = $GLOBALS['celu'];

        if ($GLOBALS[sit] == 'S')
        {
            $fec_actual = date("Y-m-d H:i:s");
            //Se calcula la diferencia del tiempo entre la fecha actual y la fecha seleccionada
            $query = "SELECT TIMEDIFF( '" . $GLOBALS['date'] . " " . $GLOBALS['hora'] . "', NOW() ) ";
            $consulta = new Consulta($query, $this->conexion);
            $TIME_DIFF = $consulta->ret_matriz();
            $TIME_DIFF = explode(":", $TIME_DIFF[0][0]);

            //Se calcula cuantos minutos adicionar
            $tiemp_adicis = $TIME_DIFF[0] * 60 + $TIME_DIFF[1];
            $GLOBALS[fecpronov] = $GLOBALS[fec] . " " . $GLOBALS[hor] . ":00";
            $regist["habPAD"] = $GLOBALS[habPAD];
            $regist["faro"] = '1';
            $regist["despac"] = $GLOBALS[despac];
            $regist["contro"] = $GLOBALS[codpc];
            $regist["noveda"] = $GLOBALS[novedad];
            $regist["tieadi"] = $tiemp_adicis;
            $regist["observ"] = $GLOBALS[obs];
            $regist["fecnov"] = $GLOBALS[fecpronov];
            $regist["fecact"] = $fec_actual;
            $regist["ultrep"] = $GLOBALS[ultrep];
            $regist["usuari"] = $GLOBALS[usuario];
            $regist["sitio"] = $GLOBALS[sitio];
            $regist["rutax"] = $GLOBALS[rutax];
            $regist["tie_ultnov"] = $GLOBALS[tie_ultnov];//Jorge 120404
            if ($GLOBALS[AsignMen])
                $regist["AsignMen"] = $GLOBALS[AsignMen];
            if ($GLOBALS[AsignAdit])
                $regist["AsignAdit"] = $GLOBALS[AsignAdit];

            $consulta = new Consulta("SELECT NOW()", $this->conexion, "BR");

            // $transac_nov = new Despachos($GLOBALS[cod_servic], $GLOBALS[opcion], $this->cod_aplica, $this->conexion);
            $transac_nov = new InsertNovedad($GLOBALS[cod_servic], $GLOBALS[opcion], $this->cod_aplica, $this->conexion);
            $RESPON = $transac_nov->InsertarNovedadPC(BASE_DATOS, $regist, 0);

            if ($RESPON[0]["indica"])
            {
                $consulta = new Consulta("COMMIT", $this->conexion);

                $mensaje = $RESPON[0]["mensaj"];
                for ($i = 1; $i < sizeof($RESPON); $i++)
                {
                    if ($RESPON[$i]["indica"])
                        $mensaje .= "<br><img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/ok.gif\">" . $RESPON[$i]["mensaj"];
                    else
                        $mensaje .= "<br><img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/error.gif\">" . $RESPON[$i]["mensaj"];
                }
                /*                 * *****
                 * 
                 *  Se reporta la novedad en Destino Seguro si la Transportadora tiene activa la interfaz
                 * 
                 * **** */
                $query = "SELECT b.nom_usuari, b.clv_usuari, a.cod_transp
            FROM " . BASE_DATOS . ".tab_despac_vehige a,
                 " . BASE_DATOS . ".tab_interf_parame b
           WHERE a.num_despac = '" . $GLOBALS[despac] . "'
                 AND a.cod_transp = b.cod_transp
                 AND b.cod_operad = '35' ";

                $consulta = new Consulta($query, $this->conexion);
                $datos_ds = $consulta->ret_matriz();

                if ($datos_ds)
                {
                    include( "kd_xmlrpc.php" );

                    define("XMLRPC_DEBUG", true);
                    define("SITEDS", "www.destinoseguro.net");
                    define("LOCATIONDS", "/WS/server.php");

                    $query = "SELECT a.cod_manifi, b.num_placax 
            FROM " . BASE_DATOS . ".tab_despac_despac a, 
                 " . BASE_DATOS . ".tab_despac_vehige b
           WHERE a.num_despac = '" . $GLOBALS[despac] . "'
             AND a.num_despac = b.num_despac";

                    $consulta = new Consulta($query, $this->conexion);
                    $despacho = $consulta->ret_matriz();

                    $datosDespac['usuario'] = $datos_ds[0][0];
                    $datosDespac['clave'] = $datos_ds[0][1];
                    $datosDespac['fecha'] = date("Y-m-d", strtotime($GLOBALS[fecpronov]));
                    $datosDespac['hora'] = date("H:i:s", strtotime($GLOBALS[fecpronov]));
                    $datosDespac['nittra'] = $datos_ds[0][2];
                    $datosDespac['manifiesto'] = $despacho[0][0];
                    $datosDespac['placa'] = $despacho[0][1];
                    $datosDespac['observacion'] = $GLOBALS[obs];

                    //print_r($datosDespac);

                    /* XMLRPC_prepare works on an array and converts it to XML-RPC parameters */
                    list( $success, $response ) = XMLRPC_request
                            (SITEDS, LOCATIONDS, 'wsds.InsertarSeguimiento', array(XMLRPC_prepare($datosDespac),
                        'HarryFsXMLRPCClient')
                    );
                    $mReturn = explode("-", $response['faultString']);

                    if (0 == $mReturn[0])
                    {
                        $mMessage = "******** Encabezado ******** \n";
                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                        $mMessage .= "Empresa de transporte: " . $datosDespac['nittra'] . " \n";
                        $mMessage .= "Numero de manifiesto: " . $datosDespac['manifiesto'] . " \n";
                        $mMessage .= "Placa del vehiculo: " . $datosDespac['placa'] . " \n";
                        $mMessage .= "Observacion Novedad: " . $datosDespac['observacion'] . " \n";
                        $mMessage .= "******** Detalle ******** \n";
                        $mMessage .= "Codigo de error: " . $mReturn[1] . " \n";
                        $mMessage .= "Mesaje de error: " . $mReturn[2] . " \n";
                        mail("soporte.ingenieros@intrared.net", "Web service Trafico-Destino seguro", $mMessage, 'From: soporte.ingenieros@intrared.net');
                    }
                    //print_r($response);
                }
                /*                 * *******
                 * 
                 *  Fin Interfaz Destino Seguro
                 * 
                 * *** */

                //Verificar Check de Habilitación en PAD
                if ($regist["habPAD"] == 1)
                {
                    //Consultar Ciudad y Placa para Consumo del WebService. 
                    /* ORIGINAL
                    $mQueryDespac = "SELECT a.cod_ciudes, b.num_placax " .
                            "FROM " . BASE_DATOS . ".tab_despac_despac a, " .
                            "" . BASE_DATOS . ".tab_despac_vehige b " .
                            "WHERE a.num_despac = b.num_despac " .
                            "AND a.num_despac = '" . $regist["despac"] . "'";
                    */
                    //EDTIDADO POR MIGUEL GARCIA PARA ARROJAR EL REPORTE
                    $mQueryDespac = "SELECT a.cod_ciudes, b.num_placax, b.cod_transp, a.cod_manifi " .
                            "FROM " . BASE_DATOS . ".tab_despac_despac a, " .
                            "" . BASE_DATOS . ".tab_despac_vehige b " .
                            "WHERE a.num_despac = b.num_despac " .
                            "AND a.num_despac = '" . $regist["despac"] . "'";

                    $mQueryDespac = new Consulta($mQueryDespac, $this->conexion);
                    $mDespac = $mQueryDespac->ret_matriz();

                    /*
                    //Ruta Web Service.
                    $oSoapClient = new soapclient('https://server.intrared.net:444/si/ws/server.wsdl', true);

                    //Parametros Web Service.
                    $parametros = array("inputs" => "num_placax:" . $mDespac[0][1] . "; cod_ciuori:" . $mDespac[0][0],
                                        "aplica" => "pad",
                                        "module" => "dispo",
                                        "action" => "insert",
                                        "cod_usuari" => "faro-pad",
                                        "clv_passwd" => "13f11366ba"
                                        );

                    //Consumo Web Service.
                    $respuesta = $oSoapClient->call("Processing", $parametros);
                    $valor = explode(":", $respuesta);

                    //Mensaje de Respuesta.
                    $mensaje .= "<br><b>" . $valor[2] . "</b>";
                    */

                    /********************* TRATAMIENTO SOAP *********************/
                    ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                    try
                    {
                        //Ruta Web Service Server.
                        $url_webser = "https://server.intrared.net:444/si/ws/server.wsdl";

                        $parametros = array("inputs" => "num_placax:" . $mDespac[0][1] . "; cod_ciuori:" . $mDespac[0][0],
                                            "aplica" => "pad",
                                            "module" => "dispo",
                                            "action" => "insert",
                                            "cod_usuari" => "faro-pad",
                                            "clv_passwd" => "13f11366ba"
                                            );

                        $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

                        //Métodos disponibles en el WS
                        $respuesta = $oSoapClient -> __call( 'Processing', $parametros );
                        echo "<pre>";
                        print_r($respuesta);
                        echo "</pre>";
                    }
                    catch( SoapFault $e )
                    {
                        $error_ = $e -> getMessage();

                        if ($e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                        {
                            $mMessage = "******** Encabezado ******** \n";
                            $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                            $mMessage .= "Empresa de transporte: " . $mDespac[0][2] . " \n";
                            $mMessage .= "Numero de manifiesto: " . $mDespac[0][3] . " \n";
                            $mMessage .= "Placa del vehiculo: " . $mDespac[0][1] . " \n";
                            $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                            $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                            $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                            $mMessage .= "******** Detalle ******** \n";
                            $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                            $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                            $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                            $mMessage .= "Observaciones: " . $e -> detail . " \n";

                            /* ORIGINAL
                            $mMessage = "******** Encabezado ******** \n";
                            $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                            $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                            $mMessage .= "Codigo alianza: " . $mCodEmptra[0][0] . " \n";
                            $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                            $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                            $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                            $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                            $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                            $mMessage .= "******** Detalle ******** \n";
                            $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                            $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                            $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                            $mMessage .= "Observaciones: " . $e -> detail . " \n";
                            */

                            //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                            //mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Server Intrared", $mMessage, 'From: soporte.ingenieros@intrared.net');
                            mail("engmiguelgarcia@gmail.com", "Web service Server Intrared", $mMessage, 'From: soporte.ingenieros@intrared.net');
                        }      
                    }
                    /********************* **************** *********************/

                }

                $mensaje .= "<br><b><a href=\"index.php?cod_servic=" . $this->servic . "&window=central&cod_servic=1366 \"target=\"centralFrame\">Volver al Listado Principal</a></b>";
                $mens = new mensajes();
                $mens->correcto("REGISTRO DE NOVEDADES", $mensaje);

                $GLOBALS_ADD[0]["campo"] = "alacla";
                $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
                $GLOBALS_ADD[1]["campo"] = "totregif";
                $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
                $GLOBALS_ADD[atras] = $_GET[atras];
                //$listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
                //$listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,$GLOBALS_ADD);
                //echo "antes0";
                unset($GLOBALS[opcion]);
                //include( "../" . DIR_APLICA_CENTRAL . "/inform/inf_bandej_entrad.php" );
            }
            else
            {
                $mensaje = $RESPON[0]["mensaj"];
                $mens = new mensajes();
                $mens->advert("REGISTRO DE NOVEDADES", $mensaje);

                $GLOBALS_ADD[0]["campo"] = "alacla";
                $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
                $GLOBALS_ADD[1]["campo"] = "totregif";
                $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
                $GLOBALS_ADD[atras] = $_GET[atras];
//   $listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
//   $listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,$GLOBALS_ADD);
//echo "antes1";
                unset($GLOBALS[opcion]);
                //include( "../" . DIR_APLICA_CENTRAL . "/inform/inf_bandej_entrad.php" );
            }
        }
        else
        {

            //aca el insertar de Notas Controlador cuando sea Antes . recordar arreglar el insertar de notas
            $fec_actual = date("Y-m-d H:i:s");
            //Se calcula la diferencia del tiempo entre la fecha actual y la fecha seleccionada
            $query = "SELECT TIMEDIFF( '" . $GLOBALS['date'] . " " . $GLOBALS['hora'] . "', NOW() ) ";
            $consulta = new Consulta($query, $this->conexion);
            $TIME_DIFF = $consulta->ret_matriz();
            $TIME_DIFF = explode(":", $TIME_DIFF[0][0]);

            //Se calcula cuantos minutos adicionar
            $tiemp_adicis = $TIME_DIFF[0] * 60 + $TIME_DIFF[1];
            $query = "SELECT a.cod_transp
    		  FROM " . BASE_DATOS . ".tab_despac_vehige a
    		 WHERE a.num_despac = " . $GLOBALS[despac] . "
    		 ";

            $consulta = new Consulta($query, $this->conexion);
            $nitransp = $consulta->ret_matriz();
            $regist["despac"] = $GLOBALS[despac];
            $regist["contro"] = $GLOBALS[codpc];
            $regist["noveda"] = $GLOBALS[novedad];
            $regist["tieadi"] = $tiemp_adicis;
            $regist["observ"] = $GLOBALS[obs];
            $regist["fecact"] = $fec_actual;
            $regist["fecnov"] = $GLOBALS[fec] . " " . $GLOBALS[hor] . ":00";
            ;
            $regist["usuari"] = $GLOBALS[usuario];
            $regist["nittra"] = $nitransp[0][0];
            $regist["indsit"] = "1";
            $regist["sitio"] = $GLOBALS[sitio];
            $regist["tie_ultnov"] = $GLOBALS[tie_ultnov];//Jorge 120404
            if ($GLOBALS['tiempo'] == '')
                $GLOBALS['tiempo'] = 0;
            $regist["tiem"] = $GLOBALS['tiempo'];
            $regist["rutax"] = $GLOBALS[rutax];
            $consulta = new Consulta("SELECT NOW()", $this->conexion, "BR");

            // $transac_nov = new Despachos($GLOBALS[cod_servic], $GLOBALS[opcion], $this->cod_aplica, $this->conexion);
            $transac_nov = new InsertNovedad($GLOBALS[cod_servic], $GLOBALS[opcion], $this->cod_aplica, $this->conexion);
            $RESPON = $transac_nov->InsertarNovedadNC(BASE_DATOS, $regist, 0);

            $formulario = new Formulario("index.php", "post", "INFORMACION DEL DESPACHO", "form_ins");

            if ($RESPON[0]["indica"])
            {
                $consulta = new Consulta("COMMIT", $this->conexion);

                $mensaje = $RESPON[0]["mensaj"];
                for ($i = 1; $i < sizeof($RESPON); $i++)
                {
                    if ($RESPON[$i]["indica"])
                        $mensaje .= "<br><img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/ok.gif\">" . $RESPON[$i]["mensaj"];
                    else
                        $mensaje .= "<br><img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/error.gif\">" . $RESPON[$i]["mensaj"];
                }

                /*                 * *****
                 * 
                 *  Se reporta la nota de controlador en Destino Seguro si la Transportadora tiene activa la interfaz
                 * 
                 * **** */
                $query = "SELECT b.nom_usuari, b.clv_usuari, a.cod_transp
  	      FROM " . BASE_DATOS . ".tab_despac_vehige a,
               " . BASE_DATOS . ".tab_interf_parame b
  	     WHERE a.num_despac = '" . $GLOBALS[despac] . "'
               AND a.cod_transp = b.cod_transp
               AND b.cod_operad = '35' ";

                $consulta = new Consulta($query, $this->conexion);
                $datos_ds = $consulta->ret_matriz();

                if ($datos_ds)
                {
                    include( "kd_xmlrpc.php" );

                    define("XMLRPC_DEBUG", true);
                    define("SITEDS", "www.destinoseguro.net");
                    define("LOCATIONDS", "/WS/server.php");

                    $query = "SELECT a.cod_manifi, b.num_placax 
  	      FROM " . BASE_DATOS . ".tab_despac_despac a, 
               " . BASE_DATOS . ".tab_despac_vehige b
  	     WHERE a.num_despac = '" . $GLOBALS[despac] . "'
           AND a.num_despac = b.num_despac";

                    $consulta = new Consulta($query, $this->conexion);
                    $despacho = $consulta->ret_matriz();

                    $datosDespac['usuario'] = $datos_ds[0][0];
                    $datosDespac['clave'] = $datos_ds[0][1];
                    $datosDespac['fecha'] = date("Y-m-d", strtotime($fec_actual));
                    $datosDespac['hora'] = date("H:i:s", strtotime($fec_actual));
                    $datosDespac['nittra'] = $datos_ds[0][2];
                    $datosDespac['manifiesto'] = $despacho[0][0];
                    $datosDespac['placa'] = $despacho[0][1];
                    $datosDespac['observacion'] = $GLOBALS[obs];

                    //print_r($datosDespac);

                    /* XMLRPC_prepare works on an array and converts it to XML-RPC parameters */
                    list( $success, $response ) = XMLRPC_request
                            (SITEDS, LOCATIONDS, 'wsds.InsertarSeguimiento', array(XMLRPC_prepare($datosDespac),
                        'HarryFsXMLRPCClient')
                    );
                    $mReturn = explode("-", $response['faultString']);

                    if (0 == $mReturn[0])
                    {
                        $mMessage = "******** Encabezado ******** \n";
                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                        $mMessage .= "Empresa de transporte: " . $datosDespac['nittra'] . " \n";
                        $mMessage .= "Numero de manifiesto: " . $datosDespac['manifiesto'] . " \n";
                        $mMessage .= "Placa del vehiculo: " . $datosDespac['placa'] . " \n";
                        $mMessage .= "Observacion Nota Controlador: " . $datosDespac['observacion'] . " \n";
                        $mMessage .= "******** Detalle ******** \n";
                        $mMessage .= "Codigo de error: " . $mReturn[1] . " \n";
                        $mMessage .= "Mesaje de error: " . $mReturn[2] . " \n";
                        mail("soporte.ingenieros@intrared.net", "Web service Trafico-Destino seguro", $mMessage, 'From: soporte.ingenieros@intrared.net');
                    }
                    //print_r($response);
                }
                /*                 * *******
                 * 
                 *  Fin Interfaz Destino Seguro
                 * 
                 * *** */


                $mensaje .= "<br><b><a href=\"index.php?cod_servic=" . $this->servic . "&window=central&cod_servic=1366 \"target=\"centralFrame\">Volver al Listado Principal</a></b>";
                $formulario->cerrar();
                $mens = new mensajes();
                $mens->correcto("REGISTRO DE NOVEDADES", $mensaje);


                $GLOBALS_ADD[0]["campo"] = "alacla";
                $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
                $GLOBALS_ADD[1]["campo"] = "totregif";
                $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
                $GLOBALS_ADD[atras] = $_GET[atras];
//   $listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
//   $listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,$GLOBALS_ADD);
                unset($GLOBALS[opcion]);
                //include( "../".DIR_APLICA_CENTRAL."/inform/inf_despac_transi.php" );
            }
            else
            {
                $mensaje = $RESPON[0]["mensaj"];
                $mens = new mensajes();
                $mens->advert("REGISTRO DE NOVEDADES", $mensaje);

                $GLOBALS_ADD[0]["campo"] = "alacla";
                $GLOBALS_ADD[0]["valor"] = $GLOBALS[alacla];
                $GLOBALS_ADD[1]["campo"] = "totregif";
                $GLOBALS_ADD[1]["valor"] = $GLOBALS[totregif];
                $GLOBALS_ADD[atras] = $_GET[atras];
                //$listado_prin = new Despachos($GLOBALS[cod_servic],1,$this -> cod_aplica,$this -> conexion);
                //$listado_prin -> ListadoPrincipal($datos_usuario,0,"",0,NULL,$GLOBALS_ADD);
                unset($GLOBALS[opcion]);
                //include( "../".DIR_APLICA_CENTRAL."/inform/inf_despac_transi.php" );
            }

            $formulario->cerrar();
        }
    }

    function Cambio()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE);
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define('ESTILO', $_SESSION['ESTILO']);
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        $this->conexion = new Conexion("bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE); //cod_transp
        $query = "SELECT a.cod_manifi,b.num_placax,
		    CONCAT(c.nom_ciudad,' (',LEFT(d.nom_depart,4),')'),
		    CONCAT(e.nom_ciudad,' (',LEFT(f.nom_depart,4),')'),
		    g.nom_rutasx
	       FROM " . BASE_DATOS . ".tab_despac_despac a,
		    " . BASE_DATOS . ".tab_despac_vehige b,
		    " . BASE_DATOS . ".tab_genera_ciudad c,
		    " . BASE_DATOS . ".tab_genera_depart d,
		    " . BASE_DATOS . ".tab_genera_ciudad e,
		    " . BASE_DATOS . ".tab_genera_depart f,
		    " . BASE_DATOS . ".tab_genera_rutasx g
	      WHERE a.num_despac = b.num_despac AND
		    a.cod_ciuori = c.cod_ciudad AND
		    c.cod_depart = d.cod_depart AND
		    c.cod_paisxx = d.cod_paisxx AND
		    a.cod_ciudes = e.cod_ciudad AND
		    e.cod_depart = f.cod_depart AND
		    e.cod_paisxx = f.cod_paisxx AND
		    b.cod_rutasx = g.cod_rutasx AND
		    a.num_despac = " . $GLOBALS[despac] . "";

        $consulta = new Consulta($query, $this->conexion);
        $datbasic = $consulta->ret_matriz();

        $inicio[0][0] = 0;
        $inicio[0][1] = '-';

        $query = "SELECT c.cod_rutasx,c.nom_rutasx,a.obs_despac,
		    CONCAT(e.abr_ciudad,' (',LEFT(g.abr_depart,4),') - ',LEFT(i.nom_paisxx,3)),
		    CONCAT(f.abr_ciudad,' (',LEFT(h.abr_depart,4),') - ',LEFT(j.nom_paisxx,3))
	       FROM " . BASE_DATOS . ".tab_despac_despac a,
		    " . BASE_DATOS . ".tab_despac_vehige b,
		    " . BASE_DATOS . ".tab_genera_rutasx c,
		   " . BASE_DATOS . ".tab_genera_ciudad e,
		   " . BASE_DATOS . ".tab_genera_ciudad f,
		   " . BASE_DATOS . ".tab_genera_depart g,
		   " . BASE_DATOS . ".tab_genera_depart h,
		   " . BASE_DATOS . ".tab_genera_paises i,
		   " . BASE_DATOS . ".tab_genera_paises j
	      WHERE a.num_despac = " . $GLOBALS[despac] . " AND
		    a.cod_ciuori = c.cod_ciuori AND
		    a.cod_ciudes = c.cod_ciudes AND
		    a.num_despac = b.num_despac AND
		    a.cod_ciuori = e.cod_ciudad AND
		    e.cod_depart = g.cod_depart AND
		    e.cod_paisxx = g.cod_paisxx AND
		    g.cod_paisxx = i.cod_paisxx AND
		    a.cod_ciudes = f.cod_ciudad AND
		    f.cod_depart = h.cod_depart AND
		    f.cod_paisxx = h.cod_paisxx AND
		    h.cod_paisxx = j.cod_paisxx AND
		    c.cod_rutasx != b.cod_rutasx AND
        c.cod_rutasx  NOT IN(SELECT cod_rutasx FROM " . BASE_DATOS . ".tab_despac_seguim WHERE num_despac = '" . $GLOBALS[despac] . "') AND
		    c.ind_estado = '1'
		    GROUP BY 1 ORDER BY 2
	    ";
        $consulta = new Consulta($query, $this->conexion);
        $rutasx = $consulta->ret_matriz();

        $query = "SELECT a.cod_noveda,a.nom_noveda
	       FROM " . BASE_DATOS . ".tab_genera_noveda a
	      WHERE a.ind_tiempo = '1' AND
		    a.ind_alarma = 'N'
		    ORDER BY 2
	    ";

        $consulta = new Consulta($query, $this->conexion);
        $noveda = $consulta->ret_matriz();

        $noveda = array_merge($inicio, $noveda);

        $formulario = new Formulario("index.php", "post", "CAMBIO DE RUTA", "form");

        $formulario->nueva_tabla();
        $formulario->linea("Cambio de Ruta Para el Documento #/Despacho " . $GLOBALS[despac] . " Vehiculo " . $datbasic[0][1], 1, "t2");

        if (!$rutasx)
        {
            $formulario->linea("No Existen Rutas Activas &oacute; Creadas Relacionadas con el Origen " . $datbasic[0][2] . " :: Destino " . $datbasic[0][3] . ".", 1, "e");
        }
        else
        {
            $formulario->nueva_tabla();
            $formulario->linea("Ruta Actual", 0);
            $formulario->linea($datbasic[0][4], 1, "i");

            $formulario->nueva_tabla();
            $formulario->linea("Selecci&oacute;n de Ruta", 1, "t2");

            $formulario->nueva_tabla();
            $formulario->linea("", 0, "t");
            $formulario->linea("", 0, "t");
            $formulario->linea("C&oacute;digo Ruta", 0, "t");
            $formulario->linea("Ruta a Seguir", 0, "t");
            $formulario->linea("Origen", 0, "t");
            $formulario->linea("Destino", 1, "t");

            for ($i = 0; $i < sizeof($rutasx); $i++)
            {
                if ($GLOBALS[rutasx] == $rutasx[$i][0])
                    $formulario->radio("", "rutasx\"", $rutasx[$i][0], 1, 0);
                else
                    $formulario->radio("", "rutasx\" onClick=\"CamRuta(" . $rutasx[$i][0] . ")", $rutasx[$i][0], 0, 0);

                $formulario->linea($rutasx[$i][0], 0, "i");
                $formulario->linea($rutasx[$i][1], 0, "i");
                $formulario->linea($rutasx[$i][3], 0, "i");
                $formulario->linea($rutasx[$i][4], 1, "i");
            }

            if ($GLOBALS[rutasx])
            {
                $query = "SELECT c.val_duraci,a.fec_noveda,a.cod_contro
		 		 FROM " . BASE_DATOS . ".tab_despac_noveda a,
		      		  " . BASE_DATOS . ".tab_genera_rutcon c
				WHERE a.num_despac = " . $GLOBALS[despac] . " AND
		      		  a.cod_rutasx = " . $GLOBALS[rutasx] . " AND
		      		  a.cod_rutasx = c.cod_rutasx AND
		      		  a.cod_contro = c.cod_contro AND
		      		  a.fec_noveda = (SELECT MAX(b.fec_noveda)
									    FROM " . BASE_DATOS . ".tab_despac_noveda b
				       				   WHERE b.num_despac = a.num_despac AND
		      		  						 a.cod_rutasx = b.cod_rutasx
				     				 )
	      	  ";

                $consulta = new Consulta($query, $this->conexion);
                $maxnoved = $consulta->ret_matriz();

                $query = "SELECT c.val_duraci,a.fec_contro,a.cod_contro
		 		 FROM " . BASE_DATOS . ".tab_despac_contro a,
		      		  " . BASE_DATOS . ".tab_genera_rutcon c
				WHERE a.num_despac = " . $GLOBALS[despac] . " AND
		      		  a.cod_rutasx = " . $GLOBALS[rutasx] . " AND
		      		  a.cod_rutasx = c.cod_rutasx AND
		      		  a.cod_contro = c.cod_contro AND
		      		  a.fec_contro = (SELECT MAX(b.fec_contro)
									    FROM " . BASE_DATOS . ".tab_despac_contro b
				       				   WHERE b.num_despac = a.num_despac AND
				       				   		 a.cod_rutasx = b.cod_rutasx
				     				 )
	      	  ";

                $consulta = new Consulta($query, $this->conexion);
                $maxnocon = $consulta->ret_matriz();

                if ($maxnoved[0][1] > $maxnocon[0][1])
                    $datultrep = $maxnoved;
                else
                    $datultrep = $maxnocon;

                if ($maxnoved)
                    $query = "SELECT a.cod_contro,a.nom_contro,b.val_duraci,
		       		   if(a.ind_virtua = '0','Fisico','Virtual')
	          	  FROM " . BASE_DATOS . ".tab_genera_contro a,
		       		   " . BASE_DATOS . ".tab_genera_rutcon b
	      	 	 WHERE a.cod_contro = b.cod_contro AND
		       		   b.cod_rutasx = " . $GLOBALS[rutasx] . " AND
		       		   b.val_duraci > " . $datultrep[0][0] . " AND
		       		   b.ind_estado = '1' AND
		       		   a.ind_estado = '1'
		       		   GROUP BY 1 ORDER BY 3
	    	 ";
                else
                    $query = "SELECT a.cod_contro,a.nom_contro,b.val_duraci,
		       		   if(a.ind_virtua = '0','Fisico','Virtual')
	          	  FROM 
		       		   " . BASE_DATOS . ".tab_genera_rutcon b,
                  " . BASE_DATOS . ".tab_genera_contro a LEFT JOIN
		       		   " . BASE_DATOS . ".tab_despac_noveda c ON
		       		   a.cod_contro = c.cod_contro AND
		       		   c.num_despac = " . $GLOBALS[despac] . "
		 		 WHERE c.cod_contro IS NULL AND
		       		   c.num_despac IS NULL AND
		       		   a.cod_contro = b.cod_contro AND
		       		   b.cod_rutasx = " . $GLOBALS[rutasx] . " AND
		       		   b.ind_estado = '1' AND
		       		   a.ind_estado = '1'
		       		   GROUP BY 1 ORDER BY 3
	       ";

                $consulta = new Consulta($query, $this->conexion);
                $pcontros = $consulta->ret_matriz();
                if ($GLOBALS[rutasx] != $GLOBALS[rutasel])
                {
                    $GLOBALS[controbase] = NULL;
                }
                if ($GLOBALS[controbase])
                {
                    $query = "SELECT a.cod_contro,a.nom_contro
		  FROM " . BASE_DATOS . ".tab_genera_contro a
		 WHERE a.cod_contro = " . $GLOBALS[controbase] . "
	       ";

                    $consulta = new Consulta($query, $this->conexion);
                    $pcontros_a = $consulta->ret_matriz();

                    $pcontros = array_merge($pcontros_a, $pcontros);
                }

                $formulario->nueva_tabla();
                $formulario->linea("Seleccion Empalme del Sitio Seguimiento", 1, "t2");
                $formulario->nueva_tabla();
                $formulario->lista("Proximo Sitio de Seguimiento", "controbase\" id=\"controbaseID\") onChange=\"CamRuta(" . $GLOBALS[rutasx] . ")", $pcontros, $Globals[controbase]);
                $formulario->texto("Tiempo de Llegada (Min)", "text", "tmplle\"  id=\"tmplleID\" onChange=\" BlurNumeric(this); CamRuta(" . $GLOBALS[rutasx] . ")", 1, 5, 5, "", $GLOBALS[tmplle]);
                $formulario->oculto("rutasel\" id=\"rutaselID", $GLOBALS[rutasx], 0);
            }

            if ($GLOBALS[controbase] && $GLOBALS[tmplle])
            {
                $query = "SELECT c.val_duraci,a.fec_noveda,a.cod_contro
		 FROM " . BASE_DATOS . ".tab_despac_noveda a,
		      " . BASE_DATOS . ".tab_despac_vehige b,
		      " . BASE_DATOS . ".tab_genera_rutcon c
		WHERE a.num_despac = " . $GLOBALS[despac] . " AND
		      a.num_despac = b.num_despac AND
		      a.cod_rutasx = b.cod_rutasx AND
		      a.cod_rutasx = c.cod_rutasx AND
		      a.cod_contro = c.cod_contro AND
		      a.fec_noveda = (SELECT MAX(b.fec_noveda)
					FROM " . BASE_DATOS . ".tab_despac_noveda b
				       WHERE b.num_despac = a.num_despac AND
					     b.cod_rutasx = a.cod_rutasx
				     )
	      ";

                $consulta = new Consulta($query, $this->conexion);
                $maxnoact = $consulta->ret_matriz();

                if ($maxnoact)
                {
                    $query = "SELECT a.nom_contro
		  FROM " . BASE_DATOS . ".tab_genera_contro a
		 WHERE a.cod_contro = " . $maxnoact[0][2] . "
	       ";

                    $consulta = new Consulta($query, $this->conexion);
                    $controul = $consulta->ret_matriz();

                    $formulario->nueva_tabla();
                    $formulario->linea("Ultima Noveda dell Sitio Seguimiento Generada con la Ruta Actual", 1, "t2");
                    $formulario->nueva_tabla();
                    $formulario->linea("Sitio de Seguimiento", 0);
                    $formulario->linea($controul[0][0], 1, "i");
                    $formulario->linea("Fecha Novedad", 0);
                    $formulario->linea($maxnoact[0][1], 1, "i");
                }

                $formulario->nueva_tabla();
                $formulario->linea("Fecha Programada Para el Cambio de Ruta", 1, "t2");

                $formulario->nueva_tabla();

                if (!$GLOBALS[fechaprog])
                    $GLOBALS[fechaprog] = date("Y-m-d H:i");
                else
                    $GLOBALS[fechaprog] = str_replace("/", "-", $GLOBALS[fechaprog]);


                $formulario->nueva_tabla();
                $formulario->linea("Fecha /Hora", 0, "t2");
                $formulario->linea(date("Y-m-d H:i"), 1, "i");
                $feccal = $GLOBALS[fechaprog];

                $query = "SELECT a.cod_contro,a.nom_contro,c.val_duraci,
		    if(a.ind_virtua = '0','Fisico','Virtual')
	       FROM " . BASE_DATOS . ".tab_genera_contro a,
		    " . BASE_DATOS . ".tab_genera_rutcon c
	      WHERE c.cod_rutasx = '" . $GLOBALS[rutasx] . "' AND
		    c.cod_contro = a.cod_contro AND
		    c.val_duraci >= (SELECT b.val_duraci
				       FROM " . BASE_DATOS . ".tab_genera_rutcon b
				      WHERE b.cod_rutasx = c.cod_rutasx AND
					    b.cod_contro = " . $GLOBALS[controbase] . "
				    ) AND
		    c.ind_estado = '1' AND
		    a.ind_estado = '1'
		    ORDER BY 3
	    ";

                $consulta = new Consulta($query, $this->conexion);
                $pcontr = $consulta->ret_matriz();

                $formulario->nueva_tabla();
                $formulario->linea("Sitio Seguimiento", 1, "h");

                $formulario->nueva_tabla();
                $formulario->linea("", 0, "t");
                $formulario->linea("S/N", 0, "t");
                $formulario->linea("C&oacute;digo", 0, "t");
                $formulario->linea("Nombre", 0, "t");
                $formulario->linea("Sitio", 0, "t");
                $formulario->linea("", 0, "t");
                $formulario->linea("Novedad", 0, "t");
                $formulario->linea("", 0, "t");
                $formulario->linea("Tiempo Estimado", 0, "t");
                $formulario->linea("Fecha y Hora Planeada", 1, "t");

                $pcontro = $GLOBALS[pcontro];
                $pctime = $GLOBALS[pctime];
                $pcnove = $GLOBALS[pcnove];

                $tiemacu = 0;

                for ($i = 0; $i < sizeof($pcontr); $i++)
                {
                    if (!$GLOBALS[pcontro])
                        $pcontro[$i] = 1;

                    $temp_nove = $noveda;

                    if ($pcnove[$i] != "0")
                    {
                        $query = "SELECT a.cod_noveda,a.nom_noveda
		     FROM " . BASE_DATOS . ".tab_genera_noveda a
		    WHERE a.cod_noveda = '" . $pcnove[$i] . "'
		  ";

                        $consulta = new Consulta($query, $this->conexion);
                        $nove_selec = $consulta->ret_matriz();

                        $temp_nove = array_merge($nove_selec, $temp_nove);
                    }

                    if ($GLOBALS[controbase] == $pcontr[$i][0])
                        $tiempcum = $GLOBALS[tmplle];
                    else
                        $tiempcum = $tiemacu + ($pcontr[$i][2] - $pcontr[0][2]) + $GLOBALS[tmplle];

                    $query = "SELECT DATE_ADD('" . $feccal . "', INTERVAL " . $tiempcum . " MINUTE)
		 ";

                    $consulta = new Consulta($query, $this->conexion);
                    $timemost = $consulta->ret_matriz();

                    $tiemacu += $pctime[$i];

                    if ($pcontr[$i][0] == CONS_CODIGO_PCLLEG)
                    {
                        $formulario->caja("", "pcontro[$i]\" id=\"pcontroID$i\" disabled ", $pcontr[$i][0], 1, 0);
                        $formulario->linea("-", 0, "i");
                        $formulario->linea($pcontr[$i][1], 0, "i");
                        $formulario->linea($pcontr[$i][3], 0, "i");
                        $formulario->linea("", 0, "t");
                        $formulario->linea("-", 0, "i");
                        $formulario->linea("", 0, "t");
                        $formulario->linea("-", 0, "i");
                        $formulario->linea($timemost[0][0], 1, "i");
                        $formulario->oculto("pcontro[$i]\" id=\"pcontroID$i", $pcontr[$i][0], 0);
                    }
                    else
                    {
                        $formulario->caja("", "pcontro[$i]\" id=\"pcontroID$i", $pcontr[$i][0], $pcontro[$i]);
                        $formulario->linea($pcontr[$i][0], 0, "i");
                        $formulario->linea($pcontr[$i][1], 0, "i");
                        $formulario->linea($pcontr[$i][3], 0, "i");
                        $formulario->lista("", "pcnove[$i]\" id=\"pcnoveID$i", $temp_nove, 0);
                        $formulario->texto("", "text", "pctime[$i]\" id=\"pctimeID$i\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_ins.submit()", 0, 10, 4, "", $pctime[$i]);
                        $formulario->linea($timemost[0][0], 1, "i");
                    }
                }

                $formulario->nueva_tabla();
                $formulario->oculto("totapc\" id=\"totapcID", sizeof($pcontr), 1);
                $formulario->oculto("fechaprog\" id=\"fechaprogID", date("Y-m-d H:i"), 1);
                $formulario->boton("Aceptar", "button\" onClick=\"if(confirm('Esta Seguro de Cambiar la Ruta')){CamInsertar();} ", 1);
            }

            $formulario->nueva_tabla();
            $formulario->oculto("usuario", "$usuario", 0);
            $formulario->oculto("opcion", $GLOBALS[opcion], 0);
            $formulario->oculto("window", "central", 0);
            $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);
        }
        $formulario->oculto("despac\ id=\"despacID", $GLOBALS[despac], 0);
        $formulario->oculto("url_archiv\" id=\"url_archivID\"", "ins_despac_seguim.php", 0);
        $formulario->oculto("dir_aplica\" id=\"dir_aplicaID\"", DIR_APLICA_CENTRAL, 0);
        $formulario->botoni("Cerrar", "ClosePopup()", 1); //validarCumplidos()
        $formulario->cerrar();
    }

    function InsertarCambio()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE);
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define('ESTILO', $_SESSION['ESTILO']);
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        include( "../lib/mensajes_lib.inc" );
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        $this->conexion = new Conexion("bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE); //cod_transp
        $fec_actual = date("Y-m-d H:i:s");
        $fec_cambru = $GLOBALS[fechaprog];
        $pcontro = $GLOBALS[pcontro];
        $pcnove = $GLOBALS[pcnove];
        $pctime = $GLOBALS[pctime];

        $query = "SELECT b.cod_contro,b.cod_rutasx
  	       FROM " . BASE_DATOS . ".tab_despac_vehige a,
  		    " . BASE_DATOS . ".tab_despac_seguim b
  	      WHERE a.num_despac = " . $GLOBALS[despac] . " AND
  		    b.num_despac = a.num_despac AND
  		    b.cod_rutasx = a.cod_rutasx
  	    ";

        $consulta = new Consulta($query, $this->conexion, "BR");
        $antplaru = $consulta->ret_matriz();

        for ($i = 0; $i < sizeof($antplaru); $i++)
        {
            $query = "SELECT a.cod_contro
  		 FROM " . BASE_DATOS . ".tab_despac_noveda a
  		WHERE a.num_despac = " . $GLOBALS[despac] . " AND
  		      a.cod_rutasx = " . $antplaru[0][1] . " AND
  		      a.cod_contro = " . $antplaru[$i][0] . "
                ";

            $consulta = new Consulta($query, $this->conexion);
            $extnoved = $consulta->ret_matriz();

            $query = "SELECT a.cod_contro
  		 FROM " . BASE_DATOS . ".tab_despac_contro a
  		WHERE a.num_despac = " . $GLOBALS[despac] . " AND
  		      a.cod_rutasx = " . $antplaru[0][1] . " AND
  		      a.cod_contro = " . $antplaru[$i][0] . "
                ";

            $consulta = new Consulta($query, $this->conexion);
            $extnovco = $consulta->ret_matriz();

            /*  $query = "UPDATE FROM ".BASE_DATOS.".tab_despac_seguim
              SET ind_estado='0'
              WHERE num_despac = ".$GLOBALS[despac]." AND
              cod_rutasx = ".$antplaru[0][1]." AND
              cod_contro = ".$antplaru[$i][0]."
              ";
              $consulta = new Consulta($query, $this -> conexion,"R");
             */
        }

        $query = "SELECT a.val_duraci
  	       FROM " . BASE_DATOS . ".tab_genera_rutcon a
  	      WHERE a.cod_rutasx = " . $GLOBALS[rutasx] . " AND
  		    a.cod_contro = " . $GLOBALS[controbase] . "
  	    ";

        $consulta = new Consulta($query, $this->conexion, "R");
        $pcduracibase = $consulta->ret_matriz();

        $tiemacu = 0;

        for ($i = 0; $i < $GLOBALS[totapc]; $i++)
        {
            if ($GLOBALS['pcontro' . $i])
            {
                $query = "SELECT a.val_duraci
  		 FROM " . BASE_DATOS . ".tab_genera_rutcon a
  		WHERE a.cod_rutasx = " . $GLOBALS[rutasx] . " AND
  		      a.cod_contro = " . $GLOBALS['pcontro' . $i] . "
  	      ";

                $consulta = new Consulta($query, $this->conexion);
                $pcduraci = $consulta->ret_matriz();

                if ($GLOBALS[controbase] == $pcontro[$i])
                    $tiempcum = $GLOBALS[tmplle];
                else
                    $tiempcum = $tiemacu + ($pcduraci[0][0] - $pcduracibase[0][0]) + $GLOBALS[tmplle];

                $query = "SELECT DATE_ADD('" . $fec_cambru . "', INTERVAL " . $tiempcum . " MINUTE)
  	      ";

                $consulta = new Consulta($query, $this->conexion);
                $timemost = $consulta->ret_matriz();
                $query = "INSERT INTO " . BASE_DATOS . ".tab_despac_seguim
  			     (num_despac,cod_contro,cod_rutasx,fec_planea,
  			      fec_alarma,ind_estado,usr_creaci,fec_creaci)
  		      VALUES (" . $GLOBALS[despac] . "," . $GLOBALS['pcontro' . $i] . "," . $GLOBALS[rutasx] . ",
  			      '" . $timemost[0][0] . "','" . $timemost[0][0] . "','1','" . $GLOBALS[usuario] . "',
  			      '" . $fec_actual . "')
  	        ";

                $consulta = new Consulta($query, $this->conexion, "R");

                if ($pcnove[$i])
                {
                    $query = "SELECT a.cod_contro
  		   FROM " . BASE_DATOS . ".tab_despac_pernoc a
  		  WHERE a.num_despac = " . $GLOBALS[despac] . " AND
  		        a.cod_rutasx = " . $GLOBALS[rutasx] . " AND
  		        a.cod_contro = " . $pcontro[$i] . "
  	        ";
                    $consulta = new Consulta($query, $this->conexion);
                    $extpreno = $consulta->ret_matriz();
                    if ($extpreno)
                        $query = "UPDATE " . BASE_DATOS . ".tab_despac_pernoc
  		     SET cod_noveda = " . $GLOBALS['$pcnove' . $i] . ",
  			 val_pernoc = " . $GLOBALS['$pctime' . $i] . ",
  			 usr_modifi = '" . $GLOBALS[usuario] . "',
  			 fec_modifi = '" . $fec_actual . "'
  		   WHERE num_despac = " . $GLOBALS[despac] . " AND
  			 cod_rutasx = " . $GLOBALS[rutasx] . " AND
  			 cod_contro = " . $GLOBALS['$pcontro' . $i] . "
  		 ";
                    $consulta = new Consulta($query, $this->conexion, "R");
                    $tiemacu += $GLOBALS['$pctime' . $i];
                }
            }
        }
        $query = "UPDATE " . BASE_DATOS . ".tab_despac_vehige
  		SET cod_rutasx = " . $GLOBALS[rutasx] . ",
  		    usr_modifi = '" . $GLOBALS[usuario] . "',
  		    fec_modifi = '" . $fec_actual . "'
  	      WHERE num_despac = " . $GLOBALS[despac] . "
  	    ";

        $consulta = new Consulta($query, $this->conexion, "R");

        $formulario = new Formulario("index.php", "post", "CAMBIO DE RUTA", "form_ins");

        if ($consulta = new Consulta("COMMIT", $this->conexion))
        {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $GLOBALS[cod_servic] . " \"target=\"centralFrame\">Generar Otro Cambio de Ruta</a></b>";
            echo "<script language=\"JavaScript\" >";
            echo "ClosePopup();";
            echo "insertarCam();";
            echo "</script>;";
            $mensaje = "Se Genero el Cambio de Ruta Para el Despacho # <b>" . $GLOBALS[despac] . "</b> con Exito" . $link_a;
            $mens = new mensajes();
            $mens->correcto("CAMBIO DE RUTA", $mensaje);
        }

        $formulario->cerrar();
    }

    function UpdateIndifi()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE);
        global $HTTP_POST_FILES;
        $fec_actual = date("Y-m-d H:i:s");
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define('ESTILO', $_SESSION['ESTILO']);
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        include( "../lib/mensajes_lib.inc" );
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        $this->conexion = new Conexion("bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE);
        $query = "UPDATE " . BASE_DATOS . ".tab_despac_despac
					  	SET ind_defini = " . $GLOBALS[ind_defini] . ",
					  	    usr_modifi = '" . $_SESSION[USUARIO] . "',
					  	    fec_modifi = '" . $fec_actual . "' 
  	     			WHERE num_despac ='" . $GLOBALS[num_despac] . "'";
        $consulta = new Consulta($query, $this->conexion, "BRC");
        if (!$consulta)
        {
            $mData[] = array('script', NULL, "alert(Error al Cambiar el A cargo de la Empresa .)", NULL);
            $xml = new Xml($mData);
            echo $xml->GenerarXml($mData);
        }
    }

  function getCiudades(){
    
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);
    global $HTTP_POST_FILES;
    session_start();
    $BASE = $_SESSION[BASE_DATOS];
    define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
    include( "../lib/general/conexion_lib.inc" );
    include( "../lib/general/form_lib.inc" );
    include( "../lib/general/tabla_lib.inc" );
    $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
    $term = $_REQUEST['term'];
/*
    $query = "SELECT a.cod_ciudad,CONCAT(a.nom_ciudad,'(',b.nom_depart,')')
                        FROM ".BASE_DATOS.".tab_genera_ciudad a ,
                     ".BASE_DATOS.".tab_genera_depart b
               WHERE a.cod_depart = b.cod_depart 
                 AND a.ind_estado = 1
                 AND a.nom_ciudad LIKE '%".$term."%'
              GROUP BY a.cod_ciudad     
              LIMIT 0 , 10 
              ";
*/
    $query = "SELECT cod_sitiox, CONVERT(nom_sitiox USING utf8) as nom_sitiox " .
            "FROM " . BASE_DATOS . ".tab_despac_sitio
            WHERE nom_sitiox LIKE '%".$term."%' 
            GROUP BY nom_sitiox 
            LIMIT 0 , 10 
            ";

            //echo $query;

      $consulta = new Consulta($query, $this -> conexion);
        $ciudades = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($ciudades); $i<$len; $i++){
       $data [] = '{"label":"'.$ciudades[$i][1].'","value":"'. ($ciudades[$i][1]).'"}'; 
    }
    echo '['.join(', ',$data).']';
  }



}

//FIN CLASE PROC_DESPAC
//$proceso = new Proc_segui($this->conexion, $this->usuario_aplicacion, $this->codigo);
$proceso = new Proc_segui($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
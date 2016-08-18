<?php
/*! \file: ins_despac_seguim.php
 *  \brief: Insertar el seguimiento del despacho
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
 
session_start();

/*! \class: Proc_segui
 *  \brief: Seguimiento del despacho
 */
class Proc_segui
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

    /*! \fn: principal
     *  \brief: 
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return:
     */
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
        $datos_usuario = $this->usuario->retornar();
		
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
		
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
        
        $consulta = new Consulta($query, $this->conexion);
        $despac = $consulta->ret_matriz();

        
        $formulario = new Formulario("index.php", "post", "Informacion del Despacho", "form_ins");
        if (sizeof($despac) == 1 || $GLOBALS[despac])
        {
            if (!$GLOBALS[despac])
                $GLOBALS[despac] = $despac[0][0];

            $mRuta = array("link"=>1, "finali"=>0, "opcurban"=>0, "lleg"=>1, "tie_ultnov"=>NULL);#Fabian
            $listado_prin = new Despachos($GLOBALS[cod_servic], 2, $this->cod_aplica, $this->conexion);
            $listado_prin->Encabezado($GLOBALS[despac], $datos_usuario, 0, $mRuta);
            #$listado_prin->PlanDeRuta($GLOBALS[despac], $formulario, 1, 0, 0, $datos_usuario, 1); 
            
            $usuario = $datos_usuario["cod_usuari"];
            $formulario->oculto("usuario", "$usuario", 0);
            $formulario->oculto("opcion\" id=\"opcionID", $GLOBALS[opcion], 0);
            $formulario->oculto("window", "central", 0);
            $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);
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

        $listado_prin = new Despachos($GLOBALS[cod_servic], 1, $this->cod_aplica, $this->conexion);
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
        if (isset($_REQUEST['rad_Citcar'])) {
            $mSqlUpdate = "UPDATE  ".BASE_DATOS.".tab_despac_destin a
                     SET  a.ind_citdes = '".$_REQUEST['rad_Citcar']."'  
                     WHERE  a.num_despac = '".$_REQUEST['num_despac']."';";
                       
        $consulta = new Consulta( $mSqlUpdate, $this -> conexion );
        $mSqlUpdate = $consulta -> ret_matriz();
        
        }
        
        $datos_usuario = $this->usuario->retornar();
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery17.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquerygeo.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/fecha.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/salida.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/consol.js\"></script>\n";
        
        $mRuta = array("link"=>1, "finali"=>0, "opcurban"=>0, "lleg"=>1, "tie_ultnov"=>$GLOBALS[tie_ultnov]);#Fabian

        $formulario = new Formulario("index.php", "post", "Informacion del Despacho", "form_ins");

        $listado_prin = new Despachos($GLOBALS[cod_servic], 2, $this->cod_aplica, $this->conexion);
        $listado_prin->Encabezado($GLOBALS[despac], $datos_usuario, 0, $mRuta);
        #$listado_prin->PlanDeRuta($GLOBALS[despac], $formulario, 1, 0, 0, $datos_usuario, 1, $GLOBALS[tie_ultnov]);
        
        $usuario = $datos_usuario["cod_usuari"];
        $formulario->oculto("usuario", "$usuario", 0);
        $formulario->oculto("tie_ultnov\" id=\"tie_ultnovID", $GLOBALS[tie_ultnov], 0);
        $formulario->oculto("despac\" id=\"despacID", $GLOBALS[despac], 0);
        $formulario->oculto("opcion\" id=\"opcionID", $GLOBALS[opcion], 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $GLOBALS[cod_servic], 0);
	
		
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
        
		//--------------------------------------------------------------------------------------
		// Este fragmento de codigo verifica si se puede o no consolidar el viaje
		//--------------------------------------------------------------------------------------
		$despacho = $GLOBALS[despac];
		//---------------------------------------------------
		// Almacenando el perfil actual en una variable
		$perfil = $_SESSION['datos_usuario']['cod_perfil'];		
		// Se verifica si el perfil del usuario esta autorizado
		// para realizar las consolidaciones
		$verifi_perfil = $this -> verifyPerfilConsol( $perfil );
		//---------------------------------------------------

		// Se verifica si el viaje se puede consolidar
		if( $verifi_perfil && $this -> verifyViajeConsol( $despacho ) && $_REQUEST['ind_consol'] != '1' )
		{
			echo '<div id="dialog"></div>';
		
			?>	
				  <script>
					
				  $(function() {
					$( "#dialog" ).dialog({
					
					  modal : true,
					  resizable : true,
					  draggable: false,
					  title: "Consolidaci&oacute;n de Viajes",
					  width: $(document).width() - 500,
					  heigth : 500,
					  position:['middle',25], 
					  bgiframe: true,
					  closeOnEscape: false,
					  show : { effect: "drop", duration: 300 },
					  hide : { effect: "drop", duration: 300 },
					   open: function(event, ui) 
					   { 
						 $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
					   }	   
					});

				  });
				  
				  $.ajax({
					  type: "POST",
					  url: "../satt_standa/consol/ajax_consol_viajes.php",
					  data: "option=ConsolidarViajes&despacho=<?php echo $despacho; ?>&url=<?php echo DIREC_APLICA; ?>",
					  async: false,
					  beforeSend : 
						function () 
						{ 
						  $("#dialog").html('<table align="center"><tr><td><img src="../satt_standa/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
						},
					  success : 
						function ( data ) 
						{ 
						  $("#dialog").html( data );
						}
					});			  
				  
				  
				  </script>

			<?php  
		}//Validando el perfil del usuario logueado
		//--------------------------------------------------------------------------------------		
		
         echo '<script type="text/javascript">
          $(function() { 
             showMapOpen();
           });
         </script>';		  	 
    }
	
	private function verifyPerfilConsol( $perfil )
	{
		$sql = 'SELECT
					1
				FROM
					'.BASE_DATOS.'.tab_permis_consol
				WHERE
					cod_perfil = "'.$perfil.'" ';

        $consulta = new Consulta( $sql, $this -> conexion );
        $verify = $consulta->ret_matriz();				
		
		return $result = $verify ? true : false ;			
	}

    /*! \fn: verifyViajeConsol
     *  \brief: Verifica si el Viaje esta consolidado
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: NumDespac
     *  \return:
     */
	private function verifyViajeConsol( $despacho )
	{
	    $flag = true;
		//---------------------------------------------------------
		// Sacando la placa del despacho actual
		//---------------------------------------------------------
		$sql = "SELECT 
					a.num_placax, b.fec_salsis
				FROM
					".BASE_DATOS.".tab_despac_vehige a,
                    ".BASE_DATOS.".tab_despac_despac b
				WHERE 
                    a.num_despac = b.num_despac AND
					a.num_despac = '".$despacho."' ";

        $consulta = new Consulta( $sql , $this -> conexion );
        $num_placax = $consulta->ret_matriz();
        $placax = $num_placax[0][0];
		$fechax = $num_placax[0][1];
		//---------------------------------------------------------
		
		//---------------------------------------------------------
		// Verificando si existen mas viajes para consolidar
		//---------------------------------------------------------
        $fec_despac = explode(' ', $fechax );
        $dat_despac = explode('-', $fec_despac[0]);
        $hor_despac = explode(':', $fec_despac[1]);
        
        $num_horasx = 48;
        $fec_inicia = date( 'Y-m-d H:i:s', mktime( $hor_despac[0]-$num_horasx, $hor_despac[1], $hor_despac[2], $dat_despac[1], $dat_despac[2], $dat_despac[0] ) );
        $fec_finali = date( 'Y-m-d H:i:s', mktime( $hor_despac[0]+$num_horasx, $hor_despac[1], $hor_despac[2], $dat_despac[1], $dat_despac[2], $dat_despac[0] ) );

		$sql = "SELECT 
					1, b.num_despac 
				FROM
					".BASE_DATOS.".tab_despac_vehige a,
					".BASE_DATOS.".tab_despac_despac b,
                    ".BASE_DATOS.".tab_despac_sisext c
				WHERE 
					a.num_despac = b.num_despac
                    AND a.num_despac = c.num_despac
					AND b.fec_salida IS NOT NULL
				    AND b.fec_llegad IS NULL
                    AND c.num_desext != 'VC'
					AND b.ind_anulad = 'R' 
					AND a.ind_activo = 'S'
					AND a.num_despac <> '".$despacho."'
					AND a.num_placax = '".$placax."'
                   /* AND b.fec_salsis BETWEEN '".$fec_inicia."' AND '".$fec_finali."'*/";

        $consulta = new Consulta( $sql, $this -> conexion );
        $verify = $consulta->ret_matriz();

  
       if ($verify[0][0] == 1) {
            $flag = true;
        }
        else{

            $i = 0; 
            foreach ($verify as $row) {
                if($this -> verifyNoveda($row['num_despac']) == true){
                }
                    $i += 1;
            }
 

            if( $i == 0 ){
                $flag = false;
            }

        }   

        //---------------------------------------------------------
 
        return $flag;
	}//Fin Funcion verifyViajeConsol

    protected function verifyNoveda($despac)
    {
        $mIndReturn = true;

        $sql1 = "SELECT a.ind_defini 
                      FROM tab_despac_despac a
                     WHERE a.num_despac = '".$despac."'";

        $consulta = new Consulta( $sql1 , $this -> conexion );
        $result1 = $consulta->ret_matrix("a");

        if($result1[0][ind_defini] == 0)
        {
            $contro = getControDespac($this -> conexion, $despac);
            $mUltPC = end($contro);
          
            $sql = "SELECT a.cod_noveda 
                          FROM tab_despac_noveda a
                         WHERE a.num_despac = '".$despac."'
                           AND a.cod_contro = '".$mUltPC[cod_contro]."' ";
            $consulta = new Consulta( $sql , $this -> conexion );
            $result = $consulta->ret_matrix("a");

            if(isset($result[0][cod_noveda]))
                $mIndReturn = false;
        }
        else{
            $mIndReturn = false;
        }

        return $mIndReturn;
    }// Fin funcion verifyNoveda

    /*! \fn: Formulario1
     *  \brief: Formulario para ingresar las novedades
     *  \author:
     *  \date: dia/mes/año
     *  \date modified: 17/09/2015
     *  \modified By: Ing. Fabian Salinas
     *  \param:
     *  \return:
     */
    function Formulario1()
    {
        echo "<style>
                .divPopup{
                    position: absolute;
                    left: 0px;
                    top: 0px;
                    width: 300px;
                    height: 300px;
                    z-index: 3;
                    visibility: hidden;
                    overflow: auto;
                    border: 5px solid #333333;
                    background: white;
                }
              </style>";

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
            $tiem = $rutax[0][1] == 1 ? $transpor[0][0] : $transpor[0][1];
            
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
			if( $datos_usuario["cod_perfil"]  != '689' && $datos_usuario["cod_perfil"]  != '77' )
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
			
			for( $i = 0; $i < sizeof( $novedades ) ; $i++ ){
				for( $j = 0; $j < sizeof( $select ); $j++ ){
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
            $GLOBALS[noved] = is_array($GLOBALS[noved]) ? $GLOBALS[noved][0] : $GLOBALS[noved];

            $query = "SELECT cod_noveda,UPPER(CONCAT(CONVERT(nom_noveda USING utf8),'',if(nov_especi='1','(NE)',''),if(ind_alarma='S','(GA)',''),if(ind_manala='1','(MA)',''),if(ind_tiempo='1','(ST)','') )), 
                             obs_preted,ind_alarma,nov_especi,ind_tiempo
               FROM " . BASE_DATOS . ".tab_genera_noveda
               WHERE cod_noveda = '" . $GLOBALS[noved] . "' AND ind_visibl = '1' ";
            $consulta = new Consulta($query, $this->conexion);
            $novedades_a = $consulta->ret_matriz();
            
            $nove = !$novedades_a ? "" : $novedades_a[0][0] . "-" . $novedades_a[0][1];
        }else
            $nove = "";

        //presenta por defecta la fecha actual
        if (!isset($GLOBALS[fecnov]))
            $GLOBALS[fecnov] = $fec_actual;

        if (!isset($GLOBALS[hornov]))
            $GLOBALS[hornov] = $hor_actual;

        if ($sitios){
            $mSitios = "\"Ninguna\"";
            foreach ($sitios as $row){
                $mSitios .= ", \"" . htmlentities($row['nom_sitiox']) . " \"";
            }
        }

        if ($novedades){
            $mNovedades = "\"Ninguna\"";
            foreach ($novedades as $row){
                $mNovedades .= ", \"$row[0]-" . htmlentities($row[1]) . " \"";
            }
        }

        $limite_tiempo = date("Y-m-d H:i", mktime( date( "H" )+24, date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ));
        $limite_hora = date("H:i", mktime( date( "H" )+24, date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ));
        $limite_dia = date("Y-m-d", mktime( date( "H" )+24, date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ));

        $mScript1 = '
            jQuery(function($) 
            { 
                $( "#date" ).datepicker({
                    onSelect: function( dateText, inst ) 
                    {
                        if( '.$cod_perfil.' == 7 )
                        {
                            var n_fecha = $( "#date" ).val() + " " + $( "#hora" ).val();
                            
                            if( n_fecha > "'.$limite_tiempo.'" )
                            {
                                //alert( "La maxima hora que puedes configurar es '.$limite_tiempo.'" );
                                //$( "#hora" ).val( "'.$limite_hora.'" );
                                //$( "#date" ).val( "'.$limite_dia.'" );
                            }
                        }
                    }
                });
                
                $( "#hora" ).timepicker({
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
      
                var sitios = ['.$mSitios.'];
                
                $( "#sitioID" ).autocomplete({
                    source: "../satt_standa/despac/ins_despac_seguim.php?opcion=9",
                    minLength: 4, 
                    delay: 100
                });
                
                var novedades = ['.$mNovedades.'];
                
                $( "#novedadID" ).autocomplete({
                    source: novedades,
                    delay: 100
                }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
          
                $( "#novedadID" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
            }); 
        ';

        $mScript2 = '
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
        ';

        $mScript3 = $GLOBALS[noved] ? "document.getElementById('sitID').focus()" : "document.getElementById('novedadID').focus()";

        $inicio[0][0] = 0;
        $inicio[0][1] = '-';

        //trae el indicador de solicitud tiempo en novedad
        $query = "SELECT ind_tiempo
               FROM " . BASE_DATOS . ".tab_genera_noveda
               WHERE cod_noveda = '" . $nove . "'";
        $consulta = new Consulta($query, $this->conexion);
        $ind_tiempo = $consulta->ret_arreglo();

        $query = "SELECT MAX(e.fec_noveda) 
                    FROM " . BASE_DATOS . ".tab_despac_vehige c
              INNER JOIN " . BASE_DATOS . ".tab_despac_seguim d
                      ON c.num_despac = d.num_despac  
              INNER JOIN " . BASE_DATOS . ".tab_despac_noveda e
                      ON c.num_despac = e.num_despac
                   WHERE c.num_despac = '$GLOBALS[despac]' ";
        $consulta = new Consulta($query, $this->conexion);
        $ultrep = $consulta->ret_matriz();

        $mArraySitio = array();
        if (!$contro)
            $mArraySitio[] = array('A', 'Antes');
        $mArraySitio[] = array('S', 'Sitio');

        $mArrayTiempo = array();
        if( $tiem ){
            $mArrayTiempo[] = array('', '--');

            for ($i = 15; $i <= $tiem; $i++)
                $mArrayTiempo[] = array($i, $i);
        }

       
        if( $_REQUEST['noved'] != '' && $_REQUEST['noved'] != NULL   )
        {
            $mScript4 = '
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
            ';

            # Valida si el despacho tiene Recomendaciones sin ejecutar en el puesto de control
            $mSql = "SELECT a.num_condes 
                       FROM ".BASE_DATOS.".tab_recome_asigna a 
                      WHERE a.num_despac = '$_REQUEST[num_despac]' 
                        AND a.cod_contro = '$_REQUEST[cod_contro]' 
                        AND a.ind_ejecuc = 0 
                      LIMIT 1 ";
            $mConsulta = new Consulta($mSql, $this->conexion);
            $mValidRecome = $mConsulta->ret_arreglo();
        }

        // Si es una solucionan a novedad, consulta si el usuario tiene asignado alguna novedad.
        $cod_noveda = explode('-', $_REQUEST['noved'] );
        $mScript5 = '';
        switch( trim($cod_noveda[0]) )
        {
            case '242':
                $mScript5 = '
                    var Standa = $("#dir_aplicaID").val();
                    var cod_usuari = "'.$_SESSION['datos_usuario']['cod_usuari'].'";
                    var attr_ = "option=ValidaAsignadoUsuario";
                    var attr = "&standa="+Standa+"&num_despac="+'.$GLOBALS['despac'].'+"&cod_usuari="+cod_usuari;
                    $.ajax({
                        type: "POST",
                        url: "../"+ Standa +"/desnew/ajax_despac_novpro.php",
                        data: attr_ + attr,
                        async: false,
                        success: function( datos )
                        {
                            if( datos == "y"){
                                ShowNovedaSoluci( attr );
                            }else{
                                alert( "No existen novedades asignadas al usuario: \''.$_SESSION['datos_usuario']['cod_usuari'].'\' por solucionar" );
                                $("#novedadID").val("");
                                $("#form_insID").submit();
                            }
                        }
                    });
                ';
                break;
                
            case '338':
                $mScript5 = 'showDespacRecome('.$GLOBALS['despac'].'); ';
                break;

            case '77':
                $mScript5 = 'showRutasTransp('.$_REQUEST[num_despac].', '.$_REQUEST[cod_transp].', '.$_REQUEST[rutax].', '.$_REQUEST[cod_contro].' ); ';
                break;
            
            default:
                $mScript5 = '';
                break;
        }
        

        #Inicio HTML
        $mHtml = new Formlib(2);

        $mHtml->SetJs("dinamic_list");
        $mHtml->SetJs("new_ajax");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("noveda");
        $mHtml->SetJs("min");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("es");
        $mHtml->SetJs("time");
        $mHtml->SetJs("mask");

        $mHtml->SetCssJq("dinamic_list");
        $mHtml->SetCssJq("jquery");

        $mHtml->Javascript( $mScript1 );

        $mHtml->CloseTable('tr');
        $mHtml->Form( array("target"=>"_self", "action"=>"index.php", "method"=>"post", "id"=>"form_insID", "name"=>"form_ins"), false );

        $listado_prin = new Despachos($GLOBALS[cod_servic], 3, $this->cod_aplica, $this->conexion, 2);
        $mHtml->SetBody( $listado_prin->Encabezado($GLOBALS[despac], $datos_usuario, 0, null, true) );

        $mHtml->OpenDiv("id:contentID; class:contentAccordion");

            #<Asignación de Novedad>
                $mHtml->OpenDiv("id:asigNovedaID; class:accordion");
                    $mHtml->SetBody("<h3 style='padding:6px;'><center>ASIGNACION DE NOVEDAD</center></h3>");
                    $mHtml->OpenDiv("id:secID");
                        $mHtml->OpenDiv("id:form_asigNovedaID; class:contentAccordionForm");
                            $mHtml->Table("tr");

                                #Cabecera 
                                $mHtml->Label( "Fecha", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->Label( "Hora", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->Label( "Novedad", array("class"=>"celda_titulo2", "align"=>"left") );
                                if ($ind_tiempo[0])
                                    $mHtml->Label( "Tiempo Fecha/Hora", array("class"=>"celda_titulo2", "align"=>"left") );
                                if ($GLOBALS[noved] == CONS_NOVEDA_CAMCEL)
                                    $mHtml->Label( "Celular", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->Label( "Antes/Sitio", array("class"=>"celda_titulo2", "align"=>"left") );
                                if ($tiem)
                                    $mHtml->Label( "Adicion de Tiempo", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->Label( "Sitio", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->Label( "Observacion", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->Label( "Habilitar Disponibilidad PAD", array("class"=>"celda_titulo2", "align"=>"left") );
                                $mHtml->CloseRow();

                                #Cuerpo
                                $mHtml->Row();
                                $mHtml->SetBody("<td class='celda_info' >");
                                $mHtml->SetBody("<input type='text' class='campo' style='bacground:none; border:0;' size='10' id='fecID' readonly='true' name='fec' value='" . date('Y-m-d') . "'>");
                                $mHtml->SetBody("</td>");
                                $mHtml->SetBody("<td class='celda_info' width='50px'>");
                                $mHtml->SetBody("<input type='text' class='campo' style='bacground:none; border:0;' size='10' id='horID' readonly='true' name='hor' value='" . date('G:i') . "'>");
                                $mHtml->SetBody("</td>");
                                $mHtml->Input( array("class"=>'celda_info', "width"=>'50px', "type"=>'text', "name"=>'noved', "id"=>'novedadID', "maxlength"=>'50',  "value"=>$nove,  "size"=>'50') );

                                if ($ind_tiempo[0])
                                {#SI SOLICITA TIEMPO.
                                    $h = date('G');
                                    $m = date('i');
                                    if($h <= 9) $h = "0" . $h;
                                    if($m <= 9) $m = "0" . $m;

                                    $mHtml->SetBody("<td class='celda_info' >");
                                    $mHtml->SetBody("<input type='text' class='campo' size='10' id='date' name='date' value='" . date('Y-m-d') . "'> ");
                                    $mHtml->SetBody("<input type='text' class='campo' size='10' id='hora' name='hora' value='" . $h . ":" . $m . "'>");
                                    $mHtml->SetBody("</td>");
                                }

                                if ($GLOBALS[noved] == CONS_NOVEDA_CAMCEL)
                                    $mHtml->Input( array("class"=>'celda_info', "type"=>'text', "name"=>'celu', "onChange"=>'BlurNumeric(this);', "id"=>'celuID', "maxlength"=>'10',  "size"=>'9') );

                                $mHtml->Select2( $mArraySitio, array("class"=>'celda_info', "id"=>'sitID', "name"=>'sit', "class"=>'form_01', "onblur"=>'valSit()') );

                                if( $tiem )
                                    $mHtml->Select2( $mArrayTiempo, array("class"=>'celda_info', 'width'=>'50px', "name"=>'tiempo', "id"=>'tiemID') );

                                if ($GLOBALS['ind_virtua'] == 0 && !$contro)
                                    $mHtml->Input( array("class"=>'celda_info', "name"=>'sitio', "id"=>'sitioID', "maxlength"=>'50', "size"=>'20') );
                                else
                                    $mHtml->Input( array("class"=>'celda_info', "name"=>'sitio', "id"=>'sitioID', "maxlength"=>'50', "size"=>'20', "readonly"=>'true', "value"=>$GLOBALS[pc]) );

                                $mHtml->SetBody("<td class='celda_info' >");
                                $mHtml->SetBody("<textarea name='obs' id='obsID' onkeyup='UpperText( $(this) )' cols='20' Rows='4'></textarea>");
                                $mHtml->SetBody("<div style='font-family:Arial,Helvetica,sans-serif; font-size: 11px;' id='counter'></div>");
                                $mHtml->SetBody("</td>");

                                $mHtml->CheckBox( array("class"=>'celda_info', "value"=>'1', "name"=>'habPAD') );
                                
                                $mHtml->Javascript( $mScript2 );


                                #Otros
                                $mParamcCalifi = $this -> VerifyInterfRit( $GLOBALS['despac'] ) ;
                                if( $GLOBALS['codpc'] == '9999' && $mParamcCalifi == '1' )
                                {
                                    $mNumCalifi = array(array("0"=>"", "1"=>"--"),      array("0"=>"1","1"=>"Pésimo"),  array("0"=>"2","1"=>"Malo"),
                                                        array("0"=>"3","1"=>"Regular"), array("0"=>"4","1"=>"Bueno"),   array("0"=>"5","1"=>"Excelente"));

                                    $mHtml->CloseTable("tr");
                                    $mHtml->Table("tr");
                                        $mHtml->Label( "Calificaci&oacute;n Conductor", array("class"=>"celda_titulo2", "align"=>"left", "colspan"=>"4") );
                                        $mHtml->CloseRow();
                                        $mHtml->Row();
                                        $mHtml->Label( "Calificaci&oacute;n", array("class"=>"celda_info") );
                                        $mHtml->Select2( $mNumCalifi, array("class"=>'celda_info', "name"=>'num_califi', "id"=>'num_califiID') );
                                        $mHtml->Label( "Observaciones", array("class"=>"celda_info") );
                                        $mHtml->TextArea( "", array("class"=>"celda_info", "name"=>"obs_califi", "id"=>"obs_califiID", "cols"=>'50', "Rows"=>'5', "valign"=>"top") );
                                        $mHtml->CloseRow();
                                }
                                $mParamcCalifi = $GLOBALS['codpc'] != '9999' ? '0' : $mParamcCalifi;

                                $mHtml->Row();
                                    $mHtml->Hidden( array("name"=>"ind_calcon", "id"=>"ind_calconID",   "value"=>$mParamcCalifi) );
                                    $mHtml->Hidden( array("name"=>"usuario",    "id"=>"usuarioID",      "value"=>$usuario) );
                                    $mHtml->Hidden( array("name"=>"tip_servic", "id"=>"tip_servicID",   "value"=>$transpor[0][2]) );
                                    $mHtml->Hidden( array("name"=>"tie_ultnov", "id"=>"tie_ultnovID",   "value"=>$GLOBALS['tie_ultnov']) );
                                    $mHtml->Hidden( array("name"=>"novedad",    "id"=>"novedadID",      "value"=>$GLOBALS['noved']) );
                                    $mHtml->Hidden( array("name"=>"cod_lastpc", "id"=>"cod_lastpcID",   "value"=>$lastpc) );
                                    $mHtml->Hidden( array("name"=>"cod_contro", "id"=>"cod_controID",   "value"=>$GLOBALS['codpc']) );
                                    $mHtml->Hidden( array("name"=>"cod_transp", "id"=>"cod_transpID",   "value"=>$GLOBALS['cod_transp']) );
                                    $mHtml->Hidden( array("name"=>"ind_virtua", "id"=>"ind_virtuaID",   "value"=>$GLOBALS['ind_virtua']) );
                                    $mHtml->Hidden( array("name"=>"nov_especi", "id"=>"nov_especiID",   "value"=>$novedades_a[0]['nov_especi']) );
                                    $mHtml->Hidden( array("name"=>"despac",     "id"=>"despacID",       "value"=>$GLOBALS['despac']) );
                                    $mHtml->Hidden( array("name"=>"tercero",    "id"=>"terceroID",      "value"=>$tercero) );
                                    $mHtml->Hidden( array("name"=>"fecnov",     "id"=>"fecnovID",       "value"=>$fec_actual) );
                                    $mHtml->Hidden( array("name"=>"rutax",      "id"=>"rutaxID",        "value"=>$rutax[0][0]) );
                                    $mHtml->Hidden( array("name"=>"hornov",     "id"=>"hornovID",       "value"=>$hor_actual) );
                                    $mHtml->Hidden( array("name"=>"window",     "id"=>"windowID",       "value"=>"central") );
                                    $mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID",   "value"=>$GLOBALS['cod_servic']) );
                                    $mHtml->Hidden( array("name"=>"opcion",     "id"=>"opcionID",       "value"=>2) );
                                    $mHtml->Hidden( array("name"=>"pc",         "id"=>"pcID",           "value"=>$GLOBALS['pc']) );
                                    $mHtml->Hidden( array("name"=>"codpc",      "id"=>"codpcID",        "value"=>$GLOBALS['codpc']) );
                                    $mHtml->Hidden( array("name"=>"url_archiv", "id"=>"url_archivID",   "value"=>"ins_despac_seguim.php") );
                                    $mHtml->Hidden( array("name"=>"dir_aplica", "id"=>"dir_aplicaID",   "value"=>DIR_APLICA_CENTRAL) );
                                    $mHtml->Hidden( array("name"=>"ultrep",     "id"=>"ultrepID",       "value"=>$ultrep[0][0]) );
                                    $mHtml->Hidden( array("name"=>"cod_transp", "id"=>"cod_transpID",   "value"=>trim( $GLOBALS['cod_transp'] )) );
                                    $mHtml->Hidden( array("name"=>"num_recome", "id"=>"num_recomeID",   "value"=>NULL) );
                                    $mHtml->Hidden( array("name"=>"ind_solRec", "id"=>"ind_solRecID",   "value"=>NULL) );
                                    $mHtml->Hidden( array("name"=>"tiem",       "id"=>"tiemID",         "value"=>($ind_tiempo[0] ? 1 : 0 ) ) );
                                    $mHtml->Hidden( array("name"=>"despac",     "id"=>"despacID",       "value"=>$GLOBALS['despac']) );
                                    $mHtml->Hidden( array("name"=>"ind_protoc", "id"=>"ind_protocID",   "value"=>"no") );
                                    $mHtml->Hidden( array("name"=>"ind_resolu", "id"=>"ind_resoluID",   "value"=>"") );
                                    if( $_REQUEST['noved'] != '' && $_REQUEST['noved'] != NULL ){
                                        $mHtml->Javascript( $mScript4 );
                                        $mHtml->Hidden( array("name"=>"ind_soluci", "id"=>"indShowSoluciID", "value"=>($mValidRecome != NULL ? '1' : '0') ) );
                                    }
                            $mHtml->CloseTable("tr");

                            $mHtml->Table("tr");
                                $mHtml->Button( array("class"=>"celda_info", "align"=>"center", "value"=>"Aceptar", "name"=>"Aceptar", "onclick"=>"aceptar_ins();", "class"=>"crmButton small save") );
                                $mHtml->Button( array("class"=>"celda_info", "align"=>"center", "value"=>"Borrar", "name"=>"Borrar", "onclick"=>"form_ins.reset();", "class"=>"crmButton small save") );

                                $mHtml->Row();
                                    $mHtml->SetBody('<td>');
                                    $mHtml->OpenDiv( array("id"=>"AplicationEndDIV") );
                                    $mHtml->CloseDiv();
                                    $mHtml->OpenDiv( array("id"=>"popupDIV", "class"=>"divPopup") );
                                        $mHtml->OpenDiv( array("id"=>"filtros") );
                                        $mHtml->CloseDiv();
                                        $mHtml->OpenDiv( array("id"=>"result") );
                                        $mHtml->CloseDiv();
                                    $mHtml->CloseDiv();
                                    $mHtml->OpenDiv( array("id"=>"newPopupID") );
                                    $mHtml->CloseDiv();
                                    $mHtml->OpenDiv( array("id"=>"PopupAsiID") ); 
                                    $mHtml->CloseDiv();
                                    $mHtml->SetBody('</td>');
                                    #$mHtml->Javascript( $mScript3 );
                                    #if( $mScript5 != '' )
                                        #$mHtml->Javascript( $mScript5 );

                            $mHtml->CloseTable("tr");
                        $mHtml->CloseDiv();
                    $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            #</Asignación de Novedad>

        $mHtml->CloseDiv();

        $mHtml->OpenDiv( array("id"=>"AplicationEndDIV") );
        $mHtml->CloseDiv();
        $mHtml->OpenDiv( array("id"=>"popupDIV", "class"=>"divPopup") );
            $mHtml->OpenDiv( array("id"=>"filtros") );
            $mHtml->CloseDiv();
            $mHtml->OpenDiv( array("id"=>"result") );
            $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->OpenDiv( array("id"=>"newPopupID") );
        $mHtml->CloseDiv();
        $mHtml->OpenDiv( array("id"=>"PopupAsiID") );
        $mHtml->CloseDiv();

        $mHtml->Javascript( $mScript3 );
        if( $mScript5 != '' )
            $mHtml->Javascript( $mScript5 );

        $mHtml->CloseForm();

        echo $mHtml->MakeHtml();
    }

    function ToDate( $fecha )
    {
      $_MESES = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                      '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
      $_FECHA = explode( " ", $fecha );
      $_DATE = explode( "-", $_FECHA[0] );
      
      return $_DATE[2]." de ".$_MESES[ $_DATE[1] ]." de ".$_DATE[0]." a las ".$_FECHA[1];
    }

    function VerifyInterfRit ( $mNumDespac = NULL)
    { 
     
      $query = "SELECT ind_calcon
                     FROM ".BASE_DATOS.".tab_transp_tipser
                     WHERE cod_transp =  (SELECT b.cod_transp FROM ".BASE_DATOS.".tab_despac_vehige b WHERE b.num_despac = '".$mNumDespac."')";   
     
      $consulta = new Consulta($query, $this -> conexion);
               
      $mReturn =  $consulta -> ret_matriz( "i" );       
      $mReturnX =  end( $mReturn );      
      $mReturnX = $mReturnX[0][0] == '1' ? '1' : '0';
      return  $mReturnX; 
    }
    
    function Insertar()
    {
        include( "InsertNovedad.inc" );
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
            $regist["observ"] = $tiemp_adicis ? $GLOBALS[obs]. '. Tiempo Generado: '.$tiemp_adicis. ' Minutos ' :  $GLOBALS[obs];
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
                      
                    $mQueryDespac = "SELECT a.cod_ciudes, b.num_placax, b.cod_transp, a.cod_manifi, a.num_despac 
                                       FROM ".BASE_DATOS.".tab_despac_despac a,  
                                            ".BASE_DATOS.".tab_despac_vehige b  
                                      WHERE a.num_despac = b.num_despac AND 
                                            a.num_despac = '" . $regist["despac"] . "'";

                    $mQueryDespac = new Consulta($mQueryDespac, $this->conexion);
                    $mDespac = $mQueryDespac->ret_matriz('a');

                    include( "../".DIR_APLICA_CENTRAL."/lib/InterfPad.inc"); 
                    $mInterfPad = new TraficoPad( $this->conexion );
                    $mSetRecursosPAD = $mInterfPad -> SetDataRecursos( $mDespac[0] );
                    $mensaje .= "<br>Interfaz PAD: ".$mSetRecursosPAD[1];
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
            $regist["observ"] = $tiemp_adicis ? $GLOBALS[obs]. '. Tiempo Generado: '.$tiemp_adicis. ' Minutos ' :  $GLOBALS[obs];
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

            # Interfaz RIT
            if($_POST[codpc] == '9999' && $this -> VerifyInterfRit( $_POST[despac] ) === '1')
            {
              // Consumo WSDL Interfaz
              $mens = new mensajes();
              include( "../".DIR_APLICA_CENTRAL."/lib/InterfRIT.inc" );         
              $_POST["nom_usuari"] = $_POST["usuario"];
              $mInterfRit = new InterfRIT ( $this -> conexion );            
              $mResult = $mInterfRit -> setCalifiConduc ( $_POST['despac'] , $_POST);  
              
              if($mResult[0] === true)
              {
                // Maximo consecutivo por despacho
                $mSql = "SELECT IF( MAX(cod_consec) IS NULL, '1', MAX(cod_consec + 1)) FROM  ".BASE_DATOS.".tab_califi_conduc  WHERE num_despac = '{$_POST['despac']}' ";
                $consulta = new Consulta( $mSql, $this -> conexion );
                $mMaxConsec = $consulta -> ret_matriz( 'i' );
                
                // Inserta los datos de la calificación a nivel Local -----------------------------------------------------------------------
                $mSql = "INSERT INTO ".BASE_DATOS.".tab_califi_conduc 
                         ( 
                                  cod_consec, num_despac, cod_manifi, cod_conduc, num_placax, num_califi,
                                  usr_califi, obs_noveda, fec_califi
                         )
                         VALUES
                        (
                                  '{$mMaxConsec[0][0]}','{$_POST['despac']}','{$mResult[3][cod_manifi]}', '{$mResult[3]["cod_conduc"]['cod_tercer']}', 
                                  '{$mResult[3]["num_placax"]['num_placax']}', '{$_POST['num_califi']}', '{$this -> usuario -> nom_usuari}', '{$_POST['obs_califi']}',
                                  NOW()
                        ) ";
                $mInsert = new Consulta( $mSql, $this -> conexion , 'R'); 
               
                $mensaje = "<table><tr><td align=left>Respuesta RIT:</td></tr><tr><td>".utf8_decode( $mResult[1] )." </td></tr>
                                 <tr><td>Datos: ".$mResult[2]."</td></tr>
                                 <tr><td>Despacho: ".$GLOBALS['despac']."</td></tr>
                                 <tr><td><a href='?cod_servic=".$GLOBALS["cod_servic"]."&window=central'>CALIFICAR OTRO CONDUCTOR</a></td></tr>
                          </table>";
                $mens->correcto("REGISTRO CALIFICACIÓN CONDUCTOR RIT", $mensaje);
              }
              else
              {
                $mensaje = "Ha ocurrido un(os) Error(es) con la Interfaz del RIT:<br>".$mResult[1]." <br>Datos:".$mResult[2]."<br>Despacho:".$GLOBALS['despac'];
                $mens->error("REGISTRO CALIFICACION CONDUCTOR RIT", $mensaje);
              }
              
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
        $this->conexion = new Conexion("bd10.intrared.net", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE); //cod_transp
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
        $this->conexion = new Conexion("bd10.intrared.net", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE); //cod_transp
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
        $this->conexion = new Conexion("bd10.intrared.net", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE);
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

    function getCiudades()
    {
        ini_set('display_errors', true);
        error_reporting(E_ALL & ~E_NOTICE);
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        $this -> conexion = new Conexion( "bd10.intrared.net", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
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
<?php

  class InsertProtek
  {
    var $InterfProtekto   = NULL;
    var $conexion   = NULL;
    var $cod_aplica = NULL;
    var $inicio     = array( array( '', ' --- ' ) );  
    
    function __construct( $mConnection, $mAplica, $mData )
    {
      include( "../".DIR_APLICA_CENTRAL."/lib/InterfProtekto.inc" );
            
      $this -> conexion   = $mConnection;
      $this -> cod_aplica = $mAplica;
      
      $this -> InterfProtekto = new InterfProtekto( $this -> conexion );
      
      if( $this -> InterfProtekto -> verifyInterfProtek( ) )
      {
        $this -> Principal( $mData );
      }
      else
      {
        $message =  "Acceso Denegado, Para Activar &eacute;sta opci&oacute;n comun&iacute;quese con el &aacute;rea de Soporte";
        $mens = new mensajes();
        $mens -> advert("SOLICITAR ESCOLTA",$message);
      }
    }
    
    function Principal( $mData = NULL )
    {
      if( $mData == NULL )
        $mData = $_REQUEST;
        
      if( !isset( $mData['opcion'] ) )
      {
        $this -> Listar();
      }
      else
      {
        switch( $mData['opcion'] )
        {
          default:
            $this -> Listar();
          break;
          
          case 'sel':
            $this -> MainForm();
          break;
          
          case 'ins':
            $this -> SolicitaEscolta();
          break;
          
        }
      }
    }
    
    function GetTercer( $cod_tercer )
    {
      $mQuery = "SELECT TRIM( UPPER( abr_tercer ) ) AS nom_tercer
                   FROM ". BASE_DATOS .".tab_tercer_tercer
                  WHERE cod_tercer = '".$cod_tercer."'
                  LIMIT 1";
      
      $consulta = new Consulta( $mQuery, $this -> conexion );
      $result = $consulta -> ret_matriz();
      return $result[0][0];
    }
    
    function getAllEscolt()
    {
      $mSql = "SELECT a.num_manifi 
                 FROM ".BASE_DATOS.".tab_manifi_escele a
                WHERE a.ind_aproba = '1' 
                 AND  a.cod_consec = ( SELECT MAX( z.cod_consec ) 
                                       FROM tab_manifi_escele z 
                                       WHERE z.num_manifi = a.num_manifi 
                                         AND z.ind_aproba = '1')";
      
      $consulta = new Consulta( $mSql, $this -> conexion );
      $result = $consulta -> ret_matriz();
      $TO_RETURN = array();
      foreach( $result as $row)
      {
        $TO_RETURN[] = $row[0];
      }
      
      return $TO_RETURN;
    }
    
    function getConsec( $num_manifi )
    {
      $mSql = "SELECT MAX( cod_consec ) 
                 FROM ".BASE_DATOS.".tab_manifi_escele
                WHERE num_manifi = '". $num_manifi ."'";
     
      $consulta = new Consulta( $mSql, $this -> conexion );
      $result = $consulta -> ret_matriz();
      return $result[0][0] + 1;
    }
    
    function SolicitaEscolta()
    {
      
      $datos_usuario = $_SESSION['datos_usuario'];
      $usuario = $datos_usuario['cod_usuari'];
      
      $mens = new mensajes();
      
      $_REQUEST['fec_progra'] = $_REQUEST['fec_progra'].' '.$_REQUEST['hor_progra'];
      $_REQUEST['nom_client'] = $this -> GetTercer( $_REQUEST['cod_client'] );
      $_REQUEST['nom_transp'] = $this -> GetTercer( $_REQUEST['cod_transp'] );

      $mRequest = array( 'num_manifi' => $_REQUEST['num_manifi'], 'num_ordenc' => $_REQUEST['num_ordenc'], 'num_placax' => $_REQUEST['num_placax'],
                         'cod_conduc' => $_REQUEST['cod_conduc'], 'cod_client' => $_REQUEST['cod_client'], 'nom_conduc' => $_REQUEST['nom_conduc'],
                         'tel_conduc' => $_REQUEST['tel_conduc'], 'num_remolq' => $_REQUEST['num_remolq'], 'num_conten' => $_REQUEST['num_conten'],
                         'cod_rutax'  => $_REQUEST['cod_rutax'],  'fec_progra' => $_REQUEST['fec_progra'], 'nom_client' => $_REQUEST['nom_client'],
                         'cod_transp' => $_REQUEST['cod_transp'], 'nom_transp' => $_REQUEST['nom_transp']
                        );
      
      $mEscolta = $this -> InterfProtekto -> SolicitaEscolta( $mRequest );

      $cod_solici = $mEscolta['ind_solici'];
      $ind_aproba = $cod_solici > 0 ? '1' : '0' ;
      
      $consec = $this -> getConsec( $_REQUEST['num_manifi'] );
      
      $_consec = $consec == NULL || $consec == '' ? '1' : $consec ;
      
      $_INSERT = "INSERT INTO ". BASE_DATOS .".tab_manifi_escele
                            ( cod_consec, num_manifi,	num_ordenc,	num_placax,
                              cod_client, cod_conduc, num_remolq, num_conten,
                              rut_protek, fec_progra, cod_solici, ind_aproba,
                              usr_creaci, fec_creaci
                            )
                      VALUES
                            ( ". $_consec .", '".$_REQUEST['num_manifi']."','".$_REQUEST['num_ordenc']."', '".$_REQUEST['num_placax']."',
                              '".$_REQUEST['cod_client']."', '".$_REQUEST['cod_conduc']."', '".$_REQUEST['num_remolq']."', '".$_REQUEST['num_conten']."',  
                              '".$_REQUEST['cod_rutax']."', '".$_REQUEST['fec_progra']."', ". $cod_solici .", '".$ind_aproba."', 
                              '".$usuario."', NOW()
                            )";
                            
      $consulta = new Consulta( $_INSERT, $this -> conexion );
      
      if( $mEscolta['ind_solici'] <= 0 )
      {
        $message =  "La solicitud se ha realizado sin éxito";
        $mens -> advert("<b>SOLICITAR ESCOLTA</b>",$message);
        echo '<div id="show_error" style="display:none;" align="center" >
                <br><br><table width="50%" border="1"><tr>
                <td align="center">CODIGO</td><td align="center">MENSAJE</td></tr>
                <tr><td align="center">'.$mEscolta['ind_solici'].'</td><td align="center">'.$mEscolta['men_solici'].'</td></tr>
                </table></div>';
      }
      else
      {
        $message =  "La solicitud se ha realizado con éxito.<br>Código de su solicitud:".$mEscolta['ind_solici'];
        $mens -> correcto( "<b>SOLICITAR ESCOLTA</b>", $message );     
      }
    }
    
    function MainForm()
    {
      $mRutaProtekto  = $this -> InterfProtekto -> RutasActivasProtekto();
      
      $_RUTAS = array_merge( $this -> inicio, $mRutaProtekto );
      $datos_usuario = $_SESSION['datos_usuario'];
      $usuario = $datos_usuario["cod_usuari"];
      
      
      
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
      echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/interf_protek.js' ></script>";
	    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
	    echo '
	    <script>
	      jQuery(function($) { 
	        
	        $( "#fec_prograID" ).datepicker();      
	        $( "#hor_prograID" ).timepicker();
	        
	        $.mask.definitions["A"]="[12]";
	        $.mask.definitions["M"]="[01]";
	        $.mask.definitions["D"]="[0123]";
	        
	        $.mask.definitions["H"]="[012]";
	        $.mask.definitions["N"]="[012345]";
	        $.mask.definitions["n"]="[0123456789]";
	        
	        $( "#fec_prograID" ).mask("Annn-Mn-Dn");
	        $( "#hor_prograID" ).mask("Hn:Nn:Nn");
	      });
	     </script>';
     

      
      $_MANIFI = $this -> GetManifi( $datos_usuario, $_REQUEST['despac'] );
      
      if( sizeof( $_MANIFI ) <= 0 )
      {
        $message =  "El Despacho No.".$_REQUEST['despac']." No se Encuentra Registrado";
        $mens = new mensajes();
        $mens -> advert("<b>SOLICITAR ESCOLTA</b>",$message);
        echo "<br>";
        die();
      }
      
      echo '<script>
              jQuery(function($) { 
              $( "#fec_prograID" ).datepicker();  

              $( "#hor_prograID" ).timepicker({
                timeFormat:"hh:mm",
                showSecond: false
              });
              
              $.mask.definitions["A"]="[12]";
              $.mask.definitions["M"]="[01]";
              $.mask.definitions["D"]="[0123]";
              $.mask.definitions["H"]="[012]";
              $.mask.definitions["N"]="[012345]";
              $.mask.definitions["n"]="[0123456789]";

              $( "#fec_prograID" ).mask("Annn-Mn-Dn");

              $( "#hor_prograID" ).mask("Hn:Nn");

            });</script>';
            
      $formulario = new Formulario ("index.php","post","SOLICITAR ESCOLTA ELECTRONICO","form");
      
      $formulario->nueva_tabla();
      $formulario->texto("* Manifiesto", "text", "num_manifi\" id=\"num_manifiID\" readonly=\"readonly", 1, 20, 25, "", $_MANIFI[0][1]);
      
      $formulario->texto("* Placa", "text", "num_placax\" id=\"num_placaxID\" readonly=\"readonly", 0, 20, 25, "", $_MANIFI[0][2]);
      $formulario -> oculto("cod_client\" id=\"cod_clientID",$_MANIFI[0][3],0);
      $formulario->linea("* Cliente", 0, 25,"t", "", "right");
      $formulario->linea($_MANIFI[0][4], 1, "i");
      
      $formulario->texto("* Documento Conductor", "text", "cod_conduc\" id=\"cod_conducID\" readonly=\"readonly", 0, 20, 20, "", $_MANIFI[0][5]);
      $formulario->texto("* Nombre Conductor", "text", "nom_conduc\" id=\"nom_conducID\" readonly=\"readonly", 1, 50, 50, "", $_MANIFI[0][6]);
      
      $formulario->texto("* Telefono Conductor", "text", "tel_conduc\" id=\"tel_conducID\" readonly=\"readonly", 0, 20, 20, "", $_MANIFI[0][7]);
      $formulario->texto("* Remolque", "text", "num_remolq\" id=\"num_remolqID\" readonly=\"readonly", 1, 50, 50, "", $_MANIFI[0][8]);
      
      $formulario->texto("* Contenedor(AAAA0000000)", "text", "num_conten\" id=\"num_contenID", 0, 11, 11, NULL, NULL);
      $formulario->lista("Ruta Protekto", "cod_rutax\" id=\"cod_rutaxID", $_RUTAS, 1, 1);
      
      $formulario->texto("* Fecha Programada", "text", "fec_progra\" id=\"fec_prograID\" readonly=\"readonly", 0, 12, 12, "", NULL);
      $formulario->texto("* Hora Programada", "text", "hor_progra\" id=\"hor_prograID\" readonly=\"readonly", 1, 8, 8, "", NULL);
      
      $formulario -> oculto("cod_transp",$_MANIFI[0][9],0);
      $formulario -> nueva_tabla();
      $formulario -> oculto("opcion","ins",0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);

      $formulario -> nueva_tabla();
      $formulario -> boton("Insertar","button\" onClick=\"VerifyEscolta();",1);
      $formulario -> cerrar();
    }
        
    function GetOrdenC( $num_manifi )
    {
      $mQuery = "SELECT c.num_ordcar
                   FROM ". BASE_DATOS .".tab_manifi_manifi a,
                        ". BASE_DATOS .".tab_manifi_remesa b,
                        ". BASE_DATOS .".tab_remesa_remesa c
                  WHERE a.num_manifi = b.num_manifi AND
                        b.num_remesa = c.num_remesa AND
                        a.num_manifi = '". $num_manifi ."' 
               GROUP BY 1
               ORDER BY 1
                  LIMIT 1 ";
      
      $consulta = new Consulta( $mQuery, $this -> conexion );
      $result = $consulta -> ret_matriz();
      
      return $result[0][0];
    }    
        
    function GetManifi( $datos_usuario = NULL, $num_despac = NULL )
    {
      $query = "SELECT a.num_despac, a.cod_manifi, b.num_placax, 
                       a.cod_client, UPPER( e.abr_tercer ) AS nom_client, b.cod_conduc,
                       UPPER( d.abr_tercer ) AS nom_conduc, d.num_telmov, b.num_trayle, 
                       b.cod_transp
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_tercer_tercer c,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_tercer_tercer e
                 WHERE a.num_despac = b.num_despac AND
                       b.cod_transp = c.cod_tercer AND
                       b.cod_conduc = d.cod_tercer AND
                       a.cod_client = e.cod_tercer AND
                       a.ind_anulad != 'A' AND
                       a.ind_planru = 'S' AND
                       a.fec_salida IS NULL ";

      if($GLOBALS[manifi])
        $query .= " AND a.cod_manifi = '".$GLOBALS[manifi]."'";
      if($GLOBALS[numdes])
        $query .= " AND a.num_despac = '".$GLOBALS[numdes]."'";
      if($GLOBALS[vehicu])
        $query .= " AND b.num_placax = '".$GLOBALS[vehicu]."'";
      if($GLOBALS[trayle])
        $query .= " AND b.num_trayle = '".$GLOBALS[trayle]."'";

      $query .= " AND a.num_despac = '".$num_despac."'";
      
      if($datos_usuario["cod_perfil"] == "")
      {
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }
      }
      else
      {
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }
      }

      $consec = new Consulta($query, $this -> conexion);
      return $matriz = $consec -> ret_matriz();
    }
    
    
    function Listar()
    {      
      ini_set("memory_limit", "128M");
      $datos_usuario = $_SESSION['datos_usuario'];
      $usuario = $datos_usuario["cod_usuari"];

      $cond = $this -> getAllEscolt();
      
      $query = "SELECT a.num_despac,a.cod_manifi,a.ind_anulad,a.cod_ciuori,
                    a.cod_ciudes,c.abr_tercer,b.num_placax,b.num_trayle,
                    d.abr_tercer
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    a.fec_salida IS NULL ";
      if(count( $cond ) > 0)
      {
        $query .= " AND a.cod_manifi NOT IN(". join(',', $cond ).")";
      }
                    
      if($GLOBALS[manifi])
       $query .= " AND a.cod_manifi = '".$GLOBALS[manifi]."'";
      if($GLOBALS[numdes])
       $query .= " AND a.num_despac = '".$GLOBALS[numdes]."'";
      if($GLOBALS[vehicu])
       $query .= " AND b.num_placax = '".$GLOBALS[vehicu]."'";
      if($GLOBALS[trayle])
       $query .= " AND b.num_trayle = '".$GLOBALS[trayle]."'";

      if($datos_usuario["cod_perfil"] == "")
       {
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion))
        {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }
       }
       else
       {
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion))
        {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
        }
       }

      $query .= " GROUP BY 1 ORDER BY 2";
      $consec = new Consulta($query, $this -> conexion);
      $matriz = $consec -> ret_matriz();

      $formulario = new Formulario ("index.php","post","LISTADO DE DESPACHOS","form_item");

      $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Despacho(s).",0,"t2");

      $formulario -> nueva_tabla();
      $formulario -> texto("Despacho","text","numdes\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,8,8,"",$GLOBALS[numdes],"","",1);
      $formulario -> texto("Documento/Despacho","text","manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,8,8,"",$GLOBALS[manifi],"","",1);
      $formulario -> linea("Estado",0,"t");
      $formulario -> linea("Origen",0,"t");
      $formulario -> linea("Destino",0,"t");
      $formulario -> linea("Transportadora",0,"t");
      $formulario -> texto("Vehiculo","text","vehicu\" onChange=\"form_item.submit()",0,8,8,"",$GLOBALS[vehicu],"","",1);
      $formulario -> texto("Remolque","text","trayle\" onChange=\"form_item.submit()",0,8,8,"",$GLOBALS[trayle],"","",1);
      $formulario -> linea("Conductor",1,"t");

      for($i = 0; $i < sizeof($matriz); $i++)
      {
      if($matriz[$i][2] == "A")
      $estilo = "ie";
      else
      $estilo = "i";

      if($matriz[$i][2] == "R")
      $estado = "Activo";
      else if($matriz[$i][2] == "A")
      $estado = "Anulado";

      $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
      $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][3]);
      $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][4]);

      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&despac=".$matriz[$i][0]."&opcion=sel \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

      $formulario -> linea($matriz[$i][0],0,$estilo);
      $formulario -> linea($matriz[$i][1],0,$estilo);
      $formulario -> linea($estado,0,$estilo);
      $formulario -> linea($ciudad_o[0][1],0,$estilo);
      $formulario -> linea($ciudad_d[0][1],0,$estilo);
      $formulario -> linea($matriz[$i][5],0,$estilo);
      $formulario -> linea($matriz[$i][6],0,$estilo);
      $formulario -> linea($matriz[$i][7],0,$estilo);
      $formulario -> linea($matriz[$i][8],1,$estilo);

      }

      $formulario -> nueva_tabla();
      $formulario -> oculto("b_ciuori",$GLOBALS[b_ciuori],0);
      $formulario -> oculto("b_ciudes",$GLOBALS[b_ciudes],0);
      $formulario -> oculto("transp",$GLOBALS[transp],0);
      $formulario -> oculto("fil",$GLOBALS[fil],0);
      $formulario -> oculto("fecini",$GLOBALS[fecini],0);
      $formulario -> oculto("fecfin",$GLOBALS[fecfin],0);

      $formulario -> oculto("opcion",1,0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
      $formulario -> cerrar();


    }

  }

  $procedure = new InsertProtek( $this -> conexion, $this -> cod_aplica, $_REQUEST );

?>
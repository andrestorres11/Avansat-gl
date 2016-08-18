<?php

  class AnulaProtek
  {
    var $conexion       = NULL;
    var $cod_aplica     = NULL;
    var $inicio         = array( array( '', ' --- ' ) ); 
    var $InterfProtekto = NULL;
    
    function __construct( $mConnection, $mAplica, $mData )
    {
      echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/interf_protek.js' ></script>";
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
        $mens -> advert("<b>ANULAR ESCOLTA</b>",$message);
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
          
          case 'anu':
            $this -> Anulate();
          break;
                    
        }
      }
    }
    
    function Anulate()
    {
      $datos_usuario = $_SESSION['datos_usuario'];
      $usuario = $datos_usuario['cod_usuari'];
      
      $mens = new mensajes();
      
      $mRequest = array( 'num_solici' => $_REQUEST['cod_solici'], 
                         'num_placax' => $_REQUEST['num_placax']
                        );
                        
      $mAnula = $this -> InterfProtekto -> AnulaEscolta( $mRequest );

      if( $mAnula['ind_solici'] <= 0 )
      {
        $message =  "La anulaci&oacute;n se ha realizado sin éxito";
        $mens -> advert("<b>ANULAR ESCOLTA</b>",$message);
        echo '<div id="show_error" style="display:none;" align="center" >
                <br><br><table width="50%" border="1"><tr>
                <td align="center">CODIGO</td><td align="center">MENSAJE</td></tr>
                <tr><td align="center">'.$mAnula['ind_solici'].'</td><td align="center">'.$mAnula['men_solici'].'</td></tr>
                </table></div>';
      }
      else
      {
        $_UPDATE = "UPDATE ". BASE_DATOS .".tab_manifi_escele 
                     SET cod_solici = 'OK - ".$mAnula['ind_solici']."', 
                          ind_aproba = '0'
                  WHERE num_manifi = '".$_REQUEST['num_manifi']."' AND
                        cod_consec = '".$_REQUEST['num_consec']."'";
        
        $consulta = new Consulta( $_UPDATE, $this -> conexion );
        $message =  "La anulaci&oacute;n se ha realizado con éxito.<br>Código de su solicitud: OK - ".$mAnula['ind_solici'];
        
        if( $consulta )
          $mens -> correcto( "<b>ANULAR ESCOLTA</b>", $message );     
      }
      
      
    }
    
    function MainForm()
    {
      $datos_usuario = $_SESSION['datos_usuario'];
      $usuario = $datos_usuario["cod_usuari"];
      
      $_MANIFI = $this -> GetManifi( $datos_usuario );
          
      if( sizeof( $_MANIFI ) <= 0 )
      {
        $message =  "El Manifiesto No.".$_REQUEST['num_manifi']." No se Encuentra Registrado";
        $mens = new mensajes();
        $mens -> advert("<b>ANULAR ESCOLTA</b>",$message);
        die();
      }
      
      $mRutaProtekto  = $this -> InterfProtekto -> RutasActivasProtekto();

      $formulario = new Formulario ("index.php","post","ANULAR ESCOLTA ELECTRONICO","form");
      
      $formulario->nueva_tabla();
      $formulario->texto("Manifiesto", "text", "num_manifi\" id=\"num_manifiID\" readonly=\"readonly", 1, 20, 25, "", $_MANIFI[0][1]);
      
      $formulario->texto("Placa", "text", "num_placax\" id=\"num_placaxID\" readonly=\"readonly", 0, 20, 25, "", $_MANIFI[0][2]);
      $formulario -> oculto("cod_client\" id=\"cod_clientID",$_MANIFI[0][3],0);
      $formulario->linea("Cliente", 0, 25,"t", "", "right");
      $formulario->linea($_MANIFI[0][4], 1, "i");
      
      foreach( $mRutaProtekto as $row )
      {
        if( $row[0] == $_MANIFI[0][12] )
        {
          $RUTA = $row[1];
        }
      }
      
      $formulario->texto("No. Solicitud", "text", "cod_solici\" id=\"cod_soliciID\" readonly=\"readonly", 0, 20, 25, "", $_MANIFI[0][14]);
      $formulario->linea("Ruta Protekto", 0, 25,"t", "", "right");
      $formulario->linea( $RUTA , 1, "i");
      
      $formulario->texto("Placa Vehículo", "text", "num_placax\" id=\"num_placaxID\" readonly=\"readonly", 0, 20, 25, "", $_MANIFI[0][2]);
      $formulario->linea("Fecha Programada", 0, 25,"t", "", "right");
      $formulario->linea( $_MANIFI[0][13] , 1, "i");
      
      $formulario -> oculto("num_consec\" id=\"num_consecID",$_MANIFI[0][15],0);
      
      $formulario -> nueva_tabla();
      $formulario -> oculto("opcion","anu",0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);

      $formulario -> nueva_tabla();
      $formulario -> boton("Anular","button\" onClick=\"VerifyAnula();",1);
      $formulario -> cerrar();
      
    }
        
    function GetManifi( $datos_usuario = NULL, $cod_manifi = NULL )
    {      
      $query = "SELECT a.num_despac, a.cod_manifi, b.num_placax, 
                       a.cod_client, UPPER( e.abr_tercer ) AS nom_client, b.cod_conduc,
                       UPPER( d.abr_tercer ) AS nom_conduc, d.num_telmov, b.num_trayle, 
                       b.cod_transp, f.num_remolq, f.num_conten, 
                       f.rut_protek, f.fec_progra, f.cod_solici,
                       f.cod_consec, a.cod_ciuori, a.cod_ciudes
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_tercer_tercer c,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_tercer_tercer e,
                       ".BASE_DATOS.".tab_manifi_escele f 
                 WHERE a.num_despac = b.num_despac AND
                       b.cod_transp = c.cod_tercer AND
                       b.cod_conduc = d.cod_tercer AND
                       a.cod_client = e.cod_tercer AND
                       a.cod_manifi = f.num_manifi AND 
                       b.num_placax = f.num_placax AND 
                       a.ind_anulad != 'A' AND
                       a.ind_planru = 'S' AND
                       a.fec_salida IS NULL AND
                       f.ind_aproba = '1' AND
                       f.cod_consec = ( SELECT MAX( zz.cod_consec ) 
                                         FROM ".BASE_DATOS.".tab_manifi_escele zz
                                        WHERE f.num_manifi = zz.num_manifi AND
                                              zz.ind_aproba = '1')";

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
      
      $consec = new Consulta($query, $this -> conexion);
      return $matriz = $consec -> ret_matriz();
    }
    
    
    function Listar()
    {
      
      $datos_usuario = $_SESSION['datos_usuario'];
      $usuario = $datos_usuario["cod_usuari"];
      
      $matriz = $this -> GetManifi( $datos_usuario );

      $formulario = new Formulario ("index.php","post","LISTADO DE DESPACHOS CON ESCOLTA ELECTRÓNICO","form_item");
      
      if( sizeof( $matriz ) > 0 )
      {
        $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Despacho(s).",0,"t2");

        $formulario -> nueva_tabla();
        $formulario -> linea("Manifiesto",0,"t");
        $formulario -> linea("Vehículo",0,"t");        
        $formulario -> linea("Conductor",0,"t");
        $formulario -> linea("Cliente",0,"t");
        $formulario -> linea("Origen",0,"t");
        $formulario -> linea("Destino",1,"t");

        for($i = 0; $i < sizeof($matriz); $i++)
        {
          $estilo = "i";
          
          $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
          $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][16]);
          $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][17]);
          
          $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&manifi=".$matriz[$i][1]."&opcion=sel \"target=\"centralFrame\">".$matriz[$i][1]."</a>";

          $formulario -> linea($matriz[$i][0],0,$estilo);
          $formulario -> linea($matriz[$i][2],0,$estilo);
          $formulario -> linea($matriz[$i][6],0,$estilo);
          $formulario -> linea($matriz[0][4],0,$estilo);
          $formulario -> linea($ciudad_o[0][1],0,$estilo);
          $formulario -> linea($ciudad_d[0][1],1,$estilo);
        }
      }
      else
      {
          $formulario -> linea("No se Encontraron Despachos.",1,"t2");
      }
      $formulario -> oculto("opcion",1,0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
      $formulario -> cerrar();
    }

  }

  $procedure = new AnulaProtek( $this -> conexion, $this -> cod_aplica, $_REQUEST );

?>
<?php
/************************************************/
/* Se modifica el script para que no se queme   */
/* ningún NIT de ninguna Transportadora.        */
/* Si se desea agregar o modificar un formato,  */
/* verificar en la tabla tab_config_planru, en  */
/* donde se guarda toda la parametrización de   */
/* los formatos propios.                        */
/************************************************/
class ImprimirPlandeRuta
{
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }

  function principal()
  {
    if(!isset($_REQUEST[opcion]))
    {
      $this -> Listar();
    }
    else
    {
      switch($_REQUEST[opcion])
      {
        case "0":
        $this -> Listar();
        break;

        case "1":
        if(file_exists("planru/imp_despac_planru.php"))
        {
          include("planru/imp_despac_planru.php");
          Imprimir_propio( $this -> conexion );
        }
        else
          $this -> Imprimir();
        break;
      }
    }
  }

  function Listar()
  {
    if( $_REQUEST['fec_inicia'] == NULL || $_REQUEST['fec_inicia'] == '' )
    {
      $fec_actual = strtotime( '-7 day', strtotime( date('Y-m-d') ) );
      $_REQUEST['fec_inicia'] = date( 'Y-m-d', $fec_actual );
    }
  
    if( $_REQUEST['fec_finali'] == NULL || $_REQUEST['fec_finali'] == '' )
      $_REQUEST['fec_finali'] = date('Y-m-d');
    
    include_once( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
    echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";

    echo '<script>
          jQuery(function($) 
          {
            $( "#fec_iniciaID, #fec_finaliID" ).datepicker({
              changeMonth: true,
              changeYear: true,
              onSelect: function( dateText, inst ) 
              {
                $("#formID").submit();
              }
            });
            
            $.mask.definitions["A"]="[12]";
            $.mask.definitions["M"]="[01]";
            $.mask.definitions["D"]="[0123]";
            
            $.mask.definitions["H"]="[012]";
            $.mask.definitions["N"]="[012345]";
            $.mask.definitions["n"]="[0123456789]";
            
            $( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
          });
          
          function Print_Despac( despac )
          {
            $("<div><div style=\"background-color:#FFFFFF; width:100%; height: 100px;border-radius: 10px;\"><center><br><div style=\"width:50%; border-style: solid;border-width: 5px;background-color:rgb(0,128,0)\"><label style=\"color:#FFFFFF\"><b>TIPO DE QR: </b></label></div><br><input type=\"radio\" name=\"radTipQR\" value=\"1\" checked=\"checked\"><label style=\"color:#000000\" >Waze</label>&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"radTipQR\" value=\"2\"><label style=\"color:#000000\">Google Maps</label></center></div></div>").dialog({
              modal:true,
              buttons:{
                "Con codigo Qr":function(){
                  data = $("[name=\"radTipQR\"]:checked").val(); 
                  $("#tipoqrID").val(data); 
            $("#opcionID").val("1");
            $("#despacID").val( despac.text() );
            $("#verqrxID").val("1");
            $("#formID").submit();
                },
                "Sin Codigo Qr":function(){
                  $("#tipoqrID").val(0); 
                  $("#opcionID").val("1");
                  $("#despacID").val( despac.text() );
                  $("#verqrxID").val("0");
                  $("#formID").submit();
                },
                "Cancelar":function(){
                   $(this).dialog("close");
          }
          
              }
            });  
          }
          
          </script>';
    
    /************************* FOMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Imprimir Plan de Ruta","form\" id=\"formID");
    $formulario -> texto( "Fecha Inicial", "text", "fec_inicia\" id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia'] );
    $formulario -> texto( "Fecha Final", "text", "fec_finali\" id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali'] );
    $formulario -> nueva_tabla();
    /*************************************************************/
  
    $datos_usuario = $this -> usuario -> listar($this -> conexion);
    $datos_usuario = $this -> usuario -> retornar();

    $usuario = $datos_usuario["cod_usuari"];
    /* 
    ejecutar query y quitar relaciones
     *      */
    // QUERY PARA GENERAR EL DINAMIC LIST
 
                $query = "SELECT a.num_despac, a.cod_manifi, IF( z.num_desext IS NULL, 'N/A' , z.num_desext ) AS num_desext,
                     d.num_placax, b.nom_ciudad, c.nom_ciudad, 
                     g.abr_tercer, d.num_trayle, h.abr_tercer,
                     a.cod_ciuori, a.cod_ciudes, d.cod_transp
              FROM ".BASE_DATOS.".tab_despac_despac a LEFT JOIN 
                   ".BASE_DATOS.".tab_despac_sisext z ON a.num_despac  = z.num_despac,
                   ".BASE_DATOS.".tab_genera_ciudad b,
                   ".BASE_DATOS.".tab_genera_ciudad c,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_vehicu_vehicu f,
                   ".BASE_DATOS.".tab_tercer_tercer g,
                   ".BASE_DATOS.".tab_tercer_tercer h
              WHERE a.cod_ciuori = b.cod_ciudad AND
                    a.cod_ciudes = c.cod_ciudad AND
                    a.num_despac = d.num_despac AND
                    f.num_placax = d.num_placax AND
                    d.cod_transp = g.cod_tercer AND
                    d.cod_conduc = h.cod_tercer AND
                    a.ind_planru = 'S' AND  
                    a.ind_anulad = 'R' AND
                    a.fec_llegad IS NULL AND
                    d.ind_activo = 'S' AND 
                    a.fec_creaci >= '".$_REQUEST['fec_inicia']." 00:00:00' AND  a.fec_creaci <='".$_REQUEST['fec_finali']." 23:59:59'";

 if($datos_usuario["cod_perfil"] == "")
    {
      //PARA EL FILTRO DE CONDUCTOR
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE PROPIETARIO
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE POSEEDOR
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE TRANSPORTADORA
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DEL CLIENTE
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE LA AGENCIA
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
    }
    else
    {
      //PARA EL FILTRO DE CONDUCTOR
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
              $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE PROPIETARIO
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
              $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE POSEEDOR
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
              $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE ASEGURADORA
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DEL CLIENTE
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE LA AGENCIA
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
    }


$query.="UNION


SELECT a.num_despac, 
       IF( a.cod_manifi IS NULL, (SELECT x.cod_manifi FROM ".BASE_DATOS.".tab_despac_despac x WHERE x.num_despac = q.cod_deshij )  , a.cod_manifi ) AS cod_manifi, 
        z.num_desext  ,
                     d.num_placax, b.nom_ciudad, c.nom_ciudad, 
                     g.abr_tercer, d.num_trayle, h.abr_tercer,
                     a.cod_ciuori, a.cod_ciudes, d.cod_transp
              FROM 
                   ".BASE_DATOS.".tab_genera_ciudad b,
                   ".BASE_DATOS.".tab_genera_ciudad c,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_vehicu_vehicu f,
                   ".BASE_DATOS.".tab_tercer_tercer g,
                   ".BASE_DATOS.".tab_tercer_tercer h,
                   ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_consol_despac q , 
                   ".BASE_DATOS.".tab_despac_sisext z                     
              WHERE 
                    a.num_despac = q.cod_despad AND 
                    q.cod_deshij = z.num_despac AND
                    a.cod_ciuori = b.cod_ciudad AND
                    a.cod_ciudes = c.cod_ciudad AND
                    a.num_despac = d.num_despac AND
                    f.num_placax = d.num_placax AND
                    d.cod_transp = g.cod_tercer AND
                    d.cod_conduc = h.cod_tercer AND
                    a.ind_planru = 'S' AND    
                    a.ind_anulad = 'R' AND
                    a.fec_llegad IS NULL AND
                    d.ind_activo = 'S' AND                
                    a.fec_creaci >= '".$_REQUEST['fec_inicia']." 00:00:00' AND  a.fec_creaci <='".$_REQUEST['fec_finali']." 23:59:59' ";

    
    
    #echo "<pre>"; print_r($query); echo "</pre>";
    if($datos_usuario["cod_perfil"] == "")
    {
      //PARA EL FILTRO DE CONDUCTOR
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE PROPIETARIO
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE POSEEDOR
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE TRANSPORTADORA
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DEL CLIENTE
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE LA AGENCIA
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
    }
    else
    {
      //PARA EL FILTRO DE CONDUCTOR
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
              $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE PROPIETARIO
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
              $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE POSEEDOR
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
              $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE ASEGURADORA
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DEL CLIENTE
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE LA AGENCIA
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
    }

    $query .= " GROUP  BY q.cod_despad ";
    
    $_SESSION["queryXLS"] = $query;
    $list = new DinamicList($this -> conexion, $query, 1 );
    $list -> SetClose('no');
    $list -> SetHeader("No. Despacho", "field:a.num_despac; type:link; onclick:Print_Despac( $(this) );");
    $list -> SetHeader("No. Manifiesto", "field:a.cod_manifi;");
    $list -> SetHeader("No. Viaje", "field:z.num_desext");
    $list -> SetHeader("Vehiculo", "field:d.num_placax" );
    $list -> SetHeader("Origen", "field:b.nom_ciudad" );
    $list -> SetHeader("Destino","field:c.nom_ciudad" );
    $list -> SetHeader("Transportadora","field:g.abr_tercer" );
    $list -> SetHeader("Remolque","field:d.num_trayle");
    $list -> SetHeader("Conductor","field:h.abr_tercer");

    $list -> Display( $this -> conexion );

    $_SESSION["DINAMIC_LIST"] = $list;
    echo "<td>";
    echo $list-> GetHtml();
    echo "</td>";
  
    $formulario -> nueva_tabla();
    $formulario -> linea("IMPORTANTE: Recuerde Configurar su Navegador Para Imprimir Apropiadamente <br>Haga Clic en Archivo -> Configurar P&aacute;gina. All&iacute; Elija el Tama&ntilde;o Carta, Todas Las Margenes Con el Valor en 0 y Borrar el Encabezado y el Pie de P&aacute;gina",0,"i","75%");
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",0,0);
    $formulario -> oculto("despac\" id=\"despacID",0,0);
    $formulario -> oculto("tipoqr\" id=\"tipoqrID",0,0);
    $formulario -> oculto("verqrx\" id=\"verqrxID",0,0);
    $formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);
    $formulario -> cerrar();     
  }



  function Imprimir()
  {
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/imp_planru_contro.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.qrcode-0.12.0.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.qrcode-0.12.0.min.js\"></script>\n";
    if( $_REQUEST['sisext'] )
    {
      $mSql = "SELECT num_despac 
                 FROM ".BASE_DATOS.".tab_despac_sisext 
                WHERE num_desext = '".$_REQUEST['sisext']."'";
    
      $consulta = new Consulta( $mSql, $this -> conexion );
      $_SISEXT = $consulta -> ret_matriz();
    
    
      if( sizeof( $_SISEXT ) > 0 )
      {
        $_REQUEST['despac'] = $_SISEXT[0][0];
      }
      else
      {
        $formulario = new Formulario ("index.php","post","Imprimir Plan de Ruta","form_lispalnruimp");

        $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Intentar de Nuevo</a></b>";

        $mensaje =  "El Nro. de Viaje <b>".$_REQUEST['sisext']."</b> no se Encuentra Registrado.".$link_a;
        $mens = new mensajes();
        $mens -> correcto("IMPRIMIR PLAN DE RUTA",$mensaje);

        $formulario -> cerrar();
        die();
      }
    }
    
    $_SESSION['num_despac'] = $_REQUEST['despac'];
  
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $query = "SELECT a.num_despac
                FROM ".BASE_DATOS.".tab_despac_despac a,
                     ".BASE_DATOS.".tab_genera_ciudad b,
                     ".BASE_DATOS.".tab_genera_ciudad c,
                     ".BASE_DATOS.".tab_despac_vehige d,
                     ".BASE_DATOS.".tab_vehicu_vehicu f
               WHERE a.cod_ciuori = b.cod_ciudad AND
                     a.cod_ciudes = c.cod_ciudad AND
                     a.num_despac = d.num_despac AND
                     f.num_placax = d.num_placax AND
                     a.ind_planru = 'S' AND
                     a.ind_anulad = 'R' AND
                     a.num_despac = '$_REQUEST[despac]'";

    if($datos_usuario["cod_perfil"] == "")
    {
      //PARA EL FILTRO DE CONDUCTOR
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE PROPIETARIO
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE POSEEDOR
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      }

      //PARA EL FILTRO DE ASEGURADORA
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }

      //PARA EL FILTRO DEL CLIENTE
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
      }

      //PARA EL FILTRO DE LA AGENCIA
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
    }
    else
    {
      //PARA EL FILTRO DE CONDUCTOR
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE PROPIETARIO
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
      }
      //PARA EL FILTRO DE POSEEDOR
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
      }

      //PARA EL FILTRO DE ASEGURADORA
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }

      //PARA EL FILTRO DEL CLIENTE
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND e.cod_client = '$datos_filtro[clv_filtro]' ";
      }

      //PARA EL FILTRO DE LA AGENCIA
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
    }

    $query = $query. " ORDER BY 1 DESC LIMIT 10 ";

    $consulta = new Consulta($query, $this -> conexion);
    $existe = $consulta -> ret_matriz();

    if(!$existe)
    {
      $formulario = new Formulario ("index.php","post","Imprimir Plan de Ruta","form_lispalnruimp");

      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Intentar de Nuevo</a></b>";

      $mensaje =  "El Despacho # <b>".$_REQUEST[despac]."</b> no se Encuentra Registrado &oacute; el Perfil Actual del Usuario no Tiene los Permisos Correspondientes.".$link_a;
      $mens = new mensajes();
      $mens -> correcto("IMPRIMIR PLAN DE RUTA",$mensaje);

      $formulario -> cerrar();
    }
    else
    {
      $query = "SELECT b.cod_rutasx,b.nom_rutasx
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
                       ".BASE_DATOS.".tab_genera_rutasx b
                 WHERE a.num_despac = ".$_REQUEST[despac]." AND
                       a.cod_rutasx = b.cod_rutasx
                 GROUP BY 1";

      $ruta_a = new Consulta($query, $this -> conexion);
      $ruta_a = $ruta_a -> ret_matriz();

      
      $query = "SELECT a.cod_transp,b.abr_tercer,b.dir_domici
                  FROM ".BASE_DATOS.".tab_despac_vehige a,
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.num_despac = ".$_REQUEST[despac]." AND
                       b.cod_tercer = a.cod_transp
                 GROUP BY 1";

      $transpor = new Consulta($query, $this -> conexion);
      $transpor = $transpor -> ret_matriz();

      $query = "SELECT '',b.nom_tercer,b.cod_tercer,
                       b.num_verifi,b.dir_domici,b.num_telef1,
                       c.nom_ciudad,b.num_telef1,b.num_telef2
                  FROM ".BASE_DATOS.".tab_tercer_tercer b,
                       ".BASE_DATOS.".tab_genera_ciudad c
                 WHERE b.cod_tercer = '".$transpor[0][0]."' AND
                       b.cod_ciudad = c.cod_ciudad";

      $consulta = new Consulta($query, $this -> conexion);
      $obsplan = $consulta -> ret_arreglo();

      $query = "SELECT a.cod_manifi,a.cod_ciuori,a.cod_ciudes,
                       DATE_FORMAT(b.fec_salipl,'%Y-%m-%d %H:%i'),
                       b.num_placax,h.nom_marcax,i.ano_modelo,j.nom_colorx,
                       k.nom_carroc,d.nom_tercer, b.cod_conduc,c.num_licenc,
                       e.nom_catlic,d.num_telmov,d.dir_ultfot,i.dir_fotfre,
                       IF(a.num_carava='0','Sin Caravana',a.num_carava),
                       DATE_FORMAT(b.fec_llegpl,'%Y-%m-%d %H:%i'),
                       a.obs_despac,m.nom_lineax,d.nom_apell1,d.nom_apell2, b.cod_agenci, b.nom_conduc, a.con_telmov, 
                       z.nom_tipdes
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige b,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_genera_marcas h,
                       ".BASE_DATOS.".tab_vehicu_vehicu i,
                       ".BASE_DATOS.".tab_vehige_colore j,
                       ".BASE_DATOS.".tab_vehige_carroc k,
                       ".BASE_DATOS.".tab_vehige_lineas m,
                       ".BASE_DATOS.".tab_genera_tipdes z,
                       ".BASE_DATOS.".tab_tercer_conduc c 
             LEFT JOIN ".BASE_DATOS.".tab_genera_catlic e 
                    ON c.num_catlic = e.cod_catlic 
                 WHERE b.cod_conduc = c.cod_tercer AND
                       a.num_despac = b.num_despac AND
                       a.cod_tipdes = z.cod_tipdes AND
                       d.cod_tercer = c.cod_tercer AND
                       b.num_placax = i.num_placax AND
                       h.cod_marcax = i.cod_marcax AND
                       i.cod_colorx = j.cod_colorx AND
                       i.cod_carroc = k.cod_carroc AND
                       i.cod_lineax = m.cod_lineax AND
                       i.cod_marcax = m.cod_marcax AND
                       a.num_despac = '".$_REQUEST[despac]."'";
      
      $consulta = new Consulta($query, $this -> conexion);
      $matriz = $consulta -> ret_matriz();
        

      $query = "SELECT a.num_trayle
                  FROM ".BASE_DATOS.".tab_despac_vehige a
                 WHERE a.num_despac = ".$_REQUEST[despac]."";

      $consec = new Consulta($query, $this -> conexion);
      $trayler = $consec -> ret_matriz();

      $query = "SELECT a.val_multpc,a.obs_planru
                FROM ".BASE_DATOS.".tab_config_parame a";

      $consec = new Consulta($query, $this -> conexion);
      $paramet = $consec -> ret_matriz();

      if($matriz[0][14] == NULL){
        $f1 = "../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg";
      }else{
        $f1 = "../".BASE_DATOS."/".$matriz[0][14];
      }

      if(!file_exists($f1)){
        $f1 = "../".BASE_DATOS."/".URL_CONDUC.$matriz[0][14]; 
      }

      if($matriz[0][15] == NULL){
        $f2 = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
      }else{
        $f2 = URL_VEHICU.$matriz[0][15];
      }

      if(!file_exists($f2)){
        $f2 = "../".BASE_DATOS."/".URL_VEHICU.$matriz[0][15];
      }

      $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
      $ciudad_o = $objciud -> getSeleccCiudad($matriz[0][1]);
      $ciudad_d = $objciud -> getSeleccCiudad($matriz[0][2]);

      $mSelect = "SELECT rut_format, rut_anexox, obs_adicio, 
                         ind_pdfxxx, rut_pdfxxx, dir_logoxx, 
                         ind_telage, ind_segpue, lim_anexox,
                         cam_especi, dir_imgpub
                    FROM ".BASE_DATOS.".tab_config_planru 
                   WHERE cod_transp = '".$transpor[0][0]."'
                     AND ind_activo = '1'";
      $consult = new Consulta( $mSelect, $this -> conexion );
      $ind_forpro = $consult -> ret_matriz();
      $_FORMAT = $ind_forpro[0];
      
      $durl = $_SERVER['SCRIPT_URI'];
      $e1 = $obsplan[1]; 
      $e2 = "<b>NIT: ".$obsplan[2]."-".$obsplan[3]."</b>";
      $e3 = "<b>Direcci&oacute;n: ".$obsplan[4]." / ".$obsplan[6]."</b>";
      
      if( $_FORMAT['ind_telage'] == '0' )
        $e4 = "<b>Telefonos: ".$obsplan[7]." / ".$obsplan[8]."</b>";
      else
      {
        $query = "SELECT tel_agenci 
                    FROM ".BASE_DATOS.".tab_genera_agenci
                   WHERE cod_agenci = '".$matriz[0]['cod_agenci']."'";

        $consec = new Consulta($query, $this -> conexion);
        $telef = $consec -> ret_matriz();
        $telef = $telef[0][0];
        $e4 = "<b>Telefono: ".$telef."</b>";
      }
      /****** NUEVA VALIDACION LOGO ******/
      $d1 = "imagenes/logo.gif";
      if( $_FORMAT['dir_logoxx'] != '' )
        $d1 = $_FORMAT['dir_logoxx'];
      /***********************************/

      //Imagen banner de Corona
      if($ind_forpro[0]['dir_imgpub'] != ''){
        $d30 = $ind_forpro[0]['dir_imgpub'];
      }else{
      	echo "";
      }
        
      // VALIDACION QRCODE ----------------------------------------------------------------
      $mSelect = "SELECT a.cod_tipser 
                    FROM ".BASE_DATOS.".tab_transp_tipser a
                   WHERE a.cod_transp = '".$obsplan[2]."' 
                     AND a.num_consec = (SELECT MAX( z.num_consec ) 
                                           FROM ".BASE_DATOS.".tab_transp_tipser z 
                                          WHERE z.cod_transp = '".$obsplan[2]."' )";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $SERTRA = $consulta -> ret_matriz();
      if( $SERTRA[0][0] == '1' || $SERTRA[0][0] == '3' )
      {  
        include_once("../".DIR_APLICA_CENTRAL."/lib/general/AES.inc");
        $key = 25;
        $_ENCRYPT = new AES_functions();
        $d44 = base64_encode( $_ENCRYPT -> Encrypt( $matriz[0][0], $key ) ); // MANIFIESTO ENCRIPTADO
        $d88 = base64_encode( $_ENCRYPT -> Encrypt( $matriz[0][4], $key ) ); // PLACA ENCRIPTADA
        // $QrCode = '<div id="container"></div>';
      }
      else
        $QrCode ='';
      // ----------------------------------------------------------------------------------
    
      $d2 = $f2; //foto vehiculo
      $d3 = $f1; //foto conductor
      $d4 = $matriz[0][0]; //numero de manifiesto
      $d5 = $ciudad_o[0][1]; //Origen
      $d6 = $ciudad_d[0][1]; //Destino
      $d7 = $matriz[0][3]; //Fecha Salida Planeada
      $d8 = $matriz[0][4]; //placa
      $d9 = $matriz[0][5]; //marca
      $d10 = $matriz[0][6]; //modelo
      $d11 = $matriz[0][7]; //color
      $d12 = $matriz[0][8]; //carroceria
      if( $matriz[0]['nom_conduc'] )
        $d13 = $matriz[0]['nom_conduc'];
      else
        $d13 = $matriz[0][9]." ".$matriz[0][20]." ".$matriz[0][21]; //conductor
      $d14 = $matriz[0][10]; //cedula
      $d15 = $matriz[0][11]; //licencia
      $d16 = $matriz[0][12]; //categoria
      if( $matriz[0]['con_telmov'] )
        $d17 = $matriz[0]['con_telmov'];
      else
        $d17 = $matriz[0][13]; //telefono
    
      $d18 = $paramet[0][1] != '' ? $paramet[0][1]."<br>". $matriz[0][18].$_FORMAT['obs_adicio']: $matriz[0][18].$_FORMAT['obs_adicio'] ;
      
      if($_SESSION['datos_usuario']['cod_perfil'] == 741){
        $d18 = "Se&ntilde;or Conductor es de su Responsabilidad contestar todas las llamadas realizadas del departamento de Seguridad TRANSFOREX S.A y del CL FARO, Repostarse en todos los Puestos de Control definidos en este Plan de Ruta ya que &eacute;l no Reporte tendr&aacute; una SANCION de $ 50.000 pesos por cada Uno. ";
      }
      $d19 = $_REQUEST[despac]; //numero de despacho
      $d20 = $matriz[0]['nom_tipdes']; //numero de caravana
      $d21 = $matriz[0][17]; //Fecha de llegada planeada
      $d22 = number_format($paramet[0][0]); //valor de la multa
      $d23 = $trayler[0][0]; //Nro de Trayler
      $d24 = $matriz[0][19]; //linea

      $query = "SELECT cod_perfil
                  FROM ".BASE_DATOS.".tab_autori_perfil
                 WHERE cod_perfil = '".$this -> usuario -> cod_perfil."' AND
                       cod_autori = '3'";

      $consec = new Consulta($query, $this -> conexion);
      $autfec = $consec -> ret_matriz();
    
      /*************************************************************************************************************************************************/    
      $subquery = "SELECT MAX(num_consec)
                     FROM ".BASE_DATOS.".tab_transp_tipser
                    WHERE ind_estado = '1' AND
                          cod_transp = '".$obsplan[2]."' ";
    
      $query = "SELECT cod_tipser
                  FROM ".BASE_DATOS.".tab_transp_tipser
                 WHERE ind_estado = '1' AND
                       num_consec = (".$subquery.") AND
                       cod_transp = '".$obsplan[2]."' ";
    

      $consul = new Consulta($query, $this -> conexion);
      $consul = $consul -> ret_matriz();
      $tipser = $consul[0][0];
      /*************************************************************************************************************************************************/


      if($_REQUEST['tipoqr'] != 0){
        $tipoQr = ($_REQUEST['tipoqr'] == 1 ? 'url_wazexx' : 'url_google' );
      $query = "SELECT if(b.ind_virtua = '1',CONCAT(b.nom_contro,' (Virtual)'),b.nom_contro),b.dir_contro,
                         a.fec_planea,a.fec_alarma,b.cod_contro,if(b.ind_urbano = '1','Urbano',''), ".$tipoQr."
                    FROM ".BASE_DATOS.".tab_despac_seguim a,
                         ".BASE_DATOS.".tab_genera_contro b,
                         ".BASE_DATOS.".tab_despac_vehige e
                   WHERE a.cod_contro = b.cod_contro AND
                         a.num_despac = ".$_REQUEST[despac]." AND
                         a.num_despac = e.num_despac";
  
      }else{
    
        $query = "SELECT if(b.ind_virtua = '1',CONCAT(b.nom_contro,' (Virtual)'),b.nom_contro),b.dir_contro,
                       a.fec_planea,a.fec_alarma,b.cod_contro,if(b.ind_urbano = '1','Urbano','')
                  FROM ".BASE_DATOS.".tab_despac_seguim a,
                       ".BASE_DATOS.".tab_genera_contro b,
                       ".BASE_DATOS.".tab_despac_vehige e
                 WHERE a.cod_contro = b.cod_contro AND
                       a.num_despac = ".$_REQUEST[despac]." AND
                       a.num_despac = e.num_despac";
    
      }
 
      if( $tipser == '1' )
      {
        $query .= " AND b.ind_virtua = '0'";
      }
      elseif($tipser == '3')
      {      
         if(!$autfec)
           $query .= " AND b.ind_virtua = '0'";
      }
      
      $query .= " ORDER BY 3";
      
      if( $_REQUEST['despac'] == '1071259' )
      {
        echo "<pre style='display:none'>";
        print_r( $query );
        echo "</pre>";
      }

      $consulta = new Consulta($query, $this -> conexion);
      $matriz1 = $consulta -> ret_matriz();

      $query = "SELECT b.cod_contro,c.nom_noveda,b.val_pernoc
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_pernoc b,
                       ".BASE_DATOS.".tab_genera_noveda c
                 WHERE a.num_despac = b.num_despac AND
                       b.cod_noveda = c.cod_noveda AND
                       b.cod_noveda != '0' AND
                       a.num_despac = '$_REQUEST[despac]'";

      $novedades = new Consulta($query, $this -> conexion);
      $novedad = $novedades -> ret_matriz();

      for($i=0; $i < sizeof($novedad); $i++)
      {
        for($k=0 ; $k < sizeof($matriz1); $k++)
        {
          if($novedad[$i][0] == $matriz1[$k][4])
          {
            $matriz1[$k][5] = "Novedad: ".$novedad[$i][1]." <br> Duracion: " .$novedad[$i][2]." Min";
          }
        }
      }

      $j = 0;
      
      if( $_FORMAT['ind_segpue'] == '1' ) 
        $ini = 1;
      else
        $ini = 0;

      for( $i = $ini; $i < sizeof( $matriz1 ); $i++ )
      {
        if($matriz1[$j][5])
          $adicional = "<br>".$matriz1[$j][5];
          
            if($_REQUEST['tipoqr'] != 0 && strlen($matriz1[$j][6]) > 1){

              $d[$i] = $matriz1[$j][0]."<br><div name='qr' style='display:inline'></div><div align='justify'><small><br>".$matriz1[$j][1]."<br>".$matriz1[$j][2]."<br>".$matriz1[$j][5]." </small></div><input type='hidden' name='url_ubicac".$_REQUEST['verqrx']."' value='".$matriz1[$j][6]."'>";
            }else{
              $d[$i] = $matriz1[$j][0]."<div align='justify'><small><br>".$matriz1[$j][1]."<br>".$matriz1[$j][2]."<br>".$matriz1[$j][5]." </small></div><input type='hidden' name='url_ubicac".$_REQUEST['verqrx']."' value='".$matriz1[$j][6]."'>";
            }
          $j++;
      }
      
      $_SESSION['mat'] = $d;

      if( $_REQUEST[posi] == 0 || !$_REQUEST[posi] )
      {  
        $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje.html";
        
        if( $_FORMAT['rut_format'] != '' )
          $tmpl_file = "../".DIR_APLICA_CENTRAL."/".$_FORMAT['rut_format'];
        
        if( $_FORMAT['cam_especi'] != '' )
        {
          foreach( explode('|', $_FORMAT['cam_especi']) as $row )
          {
            $especificos = explode( '=', $row );
            // POR CADA CAMPO ESPECIFICO HACE UN CASE NUEVO
            switch( $especificos[0] )
            {
              case '22':
                $d22 = $especificos[1];
              break;
            }
          }
        }
        
        $_LIMITE = $_FORMAT['lim_anexox'] != '' ? $_FORMAT['lim_anexox']: 15;
        $display = $_FORMAT['ind_pdfxxx'] == '1' ? 'block' : 'none';
        
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;

        echo "<form name=\"form\" method=\"post\" action=\"index.php\">\n";

        echo "<br><br>"

            ."<table border=\"0\" width=\"100%\">\n"

            ."<tr>\n"

            ."<td width=\"25%\" align=\"center\">\n"

            ."<input type=\"hidden\" name=\"despac\" value=\"$_REQUEST[despac]\">\n"

            ."<input type=\"hidden\" name=\"window\" value=\"central\">\n"

            ."<input type=\"hidden\" name=\"cod_servic\" value=\"$_REQUEST[cod_servic]\">\n"

            ."<input type=\"hidden\" name=\"opcion\" value=\"1\">\n"

            ."<input type=\"hidden\" name=\"posi\" value=\"0\">\n"

            ."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.PDF.style.visibility='hidden';form.Volver.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';form.PDF.style.visibility='visible';form.Volver.style.visibility='visible';form.Siguiente.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">\n"
            
            ."</td>\n"
            
            ."<td width=\"25%\" align=\"center\">\n"
            
            ."<input style=\"display:".$display."\" type=\"button\" name=\"PDF\" value=\"PDF\" onClick=\"location.href='../".DIR_APLICA_CENTRAL."/".$_FORMAT['rut_pdfxxx']."'\">\n"
            
            ."</td>\n"

            ."<td width=\"25%\" align=\"center\">\n"

            ."<input type=\"button\" name=\"Volver\" value=\"Volver\" onClick=\"form.opcion.value = 0; form.submit();\">\n"

            ."</td>\n";
        
        
        if(sizeof($matriz1) > $_LIMITE )
        {
          echo "<td width=\"25%\" align=\"center\">\n"

              ."<input type=\"button\" name=\"Siguiente\" value=\"Siguiente\" onClick=\"form.posi.value = 1;  form.submit();\">\n"

              ."</td>\n";
        }
        echo "</tr>\n"

        ."</table>\n";

        echo "</form><br><br>\n";
        echo "</div>";
      }
      
      if( sizeof( $matriz1 ) > $_LIMITE && $_REQUEST[posi] == 1 )
      {
        $tmpl_file = "../".DIR_APLICA_CENTRAL."/despac/plandeviaje_2.html";
        if( $_FORMAT['rut_anexox'] != '' )
        {
          $tmpl_file = "../".DIR_APLICA_CENTRAL."/".$_FORMAT['rut_anexox'];
        }
        
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"".$thefile."\";";
        eval($thefile);
        print $r_file;
        
        echo "<form name=\"form\" method=\"post\" action=\"index.php\">\n";

        echo "<br><br>"

            ."<table border=\"0\" width=\"100%\">\n"

            ."<tr>\n"

            ."<td width=\"50%\" align=\"center\">\n"

            ."<input type=\"hidden\" name=\"despac\" value=\"$_REQUEST[despac]\">\n"

            ."<input type=\"hidden\" name=\"window\" value=\"central\">\n"

            ."<input type=\"hidden\" name=\"cod_servic\" value=\"$_REQUEST[cod_servic]\">\n"

            ."<input type=\"hidden\" name=\"opcion\" value=\"1\">\n"

            ."<input type=\"hidden\" name=\"posi\" value=\"0\">\n"

            ."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.Siguiente.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';;form.Siguiente.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">\n"

            ."</td>\n"

            ."<td width=\"50%\" align=\"center\">\n"

            ."<input type=\"button\" name=\"Siguiente\" value=\"Anterior\" onClick=\"form.posi.value = 0; form.submit();\">\n"

            ."</td>\n"

            ."</tr>\n"

            ."</table>\n";

        echo "</form><br><br>\n";
        echo "</div>";   
      }
    }
  }
}

$proceso = new ImprimirPlandeRuta($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>

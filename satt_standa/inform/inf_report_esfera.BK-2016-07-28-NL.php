<?
ini_set('memory_limit', '1024M');
/*******************************************************************************************************/
/* @class: Informe                                                                                     */
/* @company: Intrared.net                                                                              */
/* @author: Andrés Felipe Malaver - felipe.malaver@intrared.net                                        */
/* @date: Lunes 06 de Mayo de 2013                                                                     */
/* @brief: Informe de Reporte por esferas                                                              */
/*******************************************************************************************************/
class Informe
{
  var $conexion;
  
  function __construct( $conexion )
  {
    session_start();
    $this -> conexion = $conexion;
    
    switch( $GLOBALS[opcion] )
    {
      case "xls":
        $this -> expInformExcel();
      break;
      case "2":
        $this -> getInforme();
      break;
      default:
        $this -> Filtros();
      break;
    }
  }
  
  function expInformExcel(){

    ob_end_clean();
    ini_set('memory_limit', '1024M');
    $archivo = "Informe_EAL_".date( "Y_m_d" ).".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo $_SESSION[html];
    
  }
  
  function Filtros()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_report_esfera.js\"></script>\n";
    
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    
    echo '
    <style>
      .cellHead
      {
        padding:3px 10px;
        border-top:1px solid #FFF;
        border-left:1px solid #FFF;
        border-right:1px solid #DDD;
        border-bottom:1px solid #DDD;
      }
      
      .cellInfo
      {
        padding:3px 10px;
        border-top:1px solid #FFF;
        border-left:1px solid #FFF;
        border-right:1px solid #DDD;
        border-bottom:1px solid #DDD;
      }
      .celda_titulo2
        {
          border-right:1px solid #AAA;
          font-size:12px;
          width:20%;
        }
        
        .celda_info
        {
          width:20%;
          text-align:center;
        }
        
        .campo
        {
          border:1px solid #CCC;    
          text-transform:uppercase;
        }
        
        .info
        {
          border:0px;   
          text-align:center;          
        }     
        
        .ui-autocomplete-loading 
        { 
          background: white url(\'../".DIR_APLICA_CENTRAL."/estilos/images/ui-anim_basic_16x16.gif\') right center no-repeat; 
        } 
        
        .ui-corner-all
        {
          cursor:pointer;
        }
        
        /*.ui-autocomplete 
        {
          max-height: 200px;
          height: 200px;
          overflow-y: auto;
        }*/
    </style>
    <script>
    $(function() 
    {
        
      
      $.mask.definitions["A"]="[12]";
      $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";
      
      $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";
      
      $( "#fec_incial" ).mask("Annn-Mn-Dn");
      $( "#fec_incial" ).datepicker();
      
      $( "#fec_finali" ).mask("Annn-Mn-Dn");
      $( "#fec_finali" ).datepicker();
      
      });
    </script>';

    $transpor = $this -> getTransports();
    $puestos =  $this -> getPuestos();
    
    /*echo "<pre>";
    print_r($puestos);
    echo "</pre>";*/
    
      echo '
    <script>
    $(function() {
      var tranportadoras = 
      [';
    
      if( $transpor )
      {
        echo "\"Ninguna\"";
        foreach( $transpor as $row )
        {
          echo ", \"$row[cod_tercer] - $row[abr_tercer]\"";
        }     
      };
    
    echo ']
    
    var esferas = 
      [';
    
      if( $puestos )
      {
        echo "\"Ninguna\"";
        foreach( $puestos as $row )
        {
          echo ", \"$row[cod_contro] - $row[nom_contro]\"";
        }     
      };
    
    echo ']
    
      $( "#busq_transp" ).autocomplete({
        source: tranportadoras,
        delay: 100
      }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
      
      $( "#busq_transp" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
            
      $( "#busq_esfera" ).autocomplete({
        source: esferas,
        delay: 100
      }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
      
      $( "#busq_esfera" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
      });

    </script>';
    
    $formulario = new Formulario ( "index.php", "post", "Informe Reporte por Esfera", "formulario" );
  
    $formulario -> linea( "-Filtros", 1, "t2" );
    $formulario -> nueva_tabla();
    
    if( !$_POST[fec_incial] )
      $_POST[fec_incial] = date('Y-m-d');
    if( !$_POST[fec_finali] )
      $_POST[fec_finali] = date('Y-m-d');
      
    $formulario -> texto ( "Fecha Inicio:", "text", "fec_incial\" id=\"fec_incial", 0, 10, 10, "", $_POST[fec_incial], "", "", NULL, 1 );
    $formulario -> texto ( "Fecha Final:", "text", "fec_finali\" id=\"fec_finali", 0, 10, 10, "", $_POST[fec_finali], "", "", NULL, 1 );  
    
    $formulario -> nueva_tabla();
    echo "<tr>";
    echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
    echo "<tr>";
    echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='4' >B&uacute;squeda por Transportadora</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
        Nit / Nombre: </td>";
    echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' >
        <input class='campo_texto' type='text'  
          size='25' name='busq_transp' id='busq_transp' value='".$_POST[busq_transp]."' /></td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
    echo "<tr>";
    echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='4' >B&uacute;squeda Por Esfera</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
        Nombre Esfera: </td>";
    echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' >
        <input class='campo_texto' type='text'  
          size='25' name='busq_esfera' id='busq_esfera' value='".$_POST[busq_esfera]."'/></td>";
    echo "</tr>";
    

    $formulario -> nueva_tabla();
    $formulario -> boton( "Aceptar", "button\" onClick=\"Validar();", 0 );    
    $formulario -> oculto( "window", "central", 0 );
    $formulario -> oculto( "opcion", 2, 0 );
    $formulario -> oculto( "cod_servic", $GLOBALS[cod_servic], 0 );     
    $formulario -> cerrar();
  }
  
  function getTransports( $datos_usuario = NULL)
  {
              
    $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
          FROM ".BASE_DATOS.".tab_tercer_tercer a,
             ".BASE_DATOS.".tab_tercer_activi b,
             ".BASE_DATOS.".tab_despac_vehige c
          WHERE a.cod_tercer = b.cod_tercer AND
                a.cod_tercer = c.cod_transp AND
                b.cod_activi = ".COD_FILTRO_EMPTRA."
          GROUP BY 1 
          ORDER BY 2 ASC";
    
    $consult = new Consulta( $mSql, $this -> conexion );      
    $report = $consult -> ret_matriz( 'a' ) ;
    
    return $report;
  }
  
  function getPuestos( $datos_usuario = NULL)
  {
              
    $mSql = "SELECT cod_contro, TRIM( nom_contro ) AS nom_contro
             FROM ".BASE_DATOS.".tab_genera_contro
             WHERE ind_virtua = '0' AND
                   ind_estado = '1' AND 
                   ( nom_contro LIKE 'EAL%' OR nom_contro LIKE 'E@L%' )
             GROUP BY 2";
    
    $consult = new Consulta( $mSql, $this -> conexion );      
    $report = $consult -> ret_matriz( 'a' ) ;
    
    foreach( $report as $row)
    {
      
      $pc_noveda = $this -> getPadre( $row['cod_contro'] );
      $cod_contro[ $pc_noveda[0] ] = $pc_noveda;
    }
    
    return $cod_contro;
  }
  
  function getPadre( $cod_eal )
  {
    $mSql = "SELECT cod_contro
             FROM ".BASE_DATOS.".tab_homolo_pcxeal
             WHERE cod_homolo = '". $cod_eal ."'
             GROUP BY cod_contro";
             
    $consult = new Consulta( $mSql, $this -> conexion );      
    $eal = $consult -> ret_matriz( 'a' ) ;
    $padre = $eal[0][0];
    
    $mSql = "SELECT cod_contro, UPPER(nom_contro) AS nom_contro 
             FROM ".BASE_DATOS.".tab_genera_contro";
             
    if( $padre )
    {
      $mSql .=" WHERE cod_contro = '". $padre ."'";
    }
    else
    {
      $mSql .= " WHERE cod_contro = '". $cod_eal ."'";
    }
    $mSql .= " GROUP BY cod_contro";
    
    $consult = new Consulta( $mSql, $this -> conexion );      
    $nombre_eal = $consult -> ret_matriz( 'a' ) ;
    return $nombre_eal[0];
  }
  
  function homologar( $cod_eal )
  {
    $mSql = "SELECT cod_contro
             FROM ".BASE_DATOS.".tab_homolo_pcxeal
             WHERE cod_homolo = '". $cod_eal ."'
             GROUP BY cod_contro";
             
    $consult = new Consulta( $mSql, $this -> conexion );      
    $eal = $consult -> ret_matriz( 'a' ) ;
    $padre = $eal[0][0];
    
    $mSql = "SELECT UPPER(nom_contro) AS nom_contro 
             FROM ".BASE_DATOS.".tab_genera_contro";
             
    if( $padre )
    {
      $mSql .=" WHERE cod_contro = '". $padre ."'";
    }
    else
    {
      $mSql .= " WHERE cod_contro = '". $cod_eal ."'";
    }
    $mSql .= " GROUP BY nom_contro";
    
    $consult = new Consulta( $mSql, $this -> conexion );      
    $nombre_eal = $consult -> ret_matriz( 'a' ) ;
    return $nombre_eal[0][0];
  }
  
  function homologaEAL( $cod_eal )
  {
  
    $mSql = "SELECT cod_contro
             FROM ".BASE_DATOS.".tab_homolo_pcxeal
             WHERE cod_contro = '". $cod_eal ."' OR
                   cod_homolo = '". $cod_eal ."'
             GROUP BY cod_contro";
     
    
    $consult = new Consulta( $mSql, $this -> conexion );      
    $eal = $consult -> ret_matriz( 'a' ) ;
    $padre = $eal[0][0];
    if( $padre )
    {
      $mSql = "SELECT cod_homolo
             FROM ".BASE_DATOS.".tab_homolo_pcxeal
             WHERE cod_contro = '". $padre ."'  ";
   
      $consult = new Consulta( $mSql, $this -> conexion );      
      $hijos = $consult -> ret_matriz( 'i' ) ;       
    }
    return $hijos;
  }
  function getInforme()
  {
  
    $this -> Filtros();
    if( $_REQUEST[busq_transp] && $_REQUEST[busq_transp] != '')
    {
      $cod_tercer = explode( "-" , $_REQUEST[busq_transp] );
      $cod_tercer = trim( $cod_tercer[0] );
    }
    if( $_REQUEST[busq_esfera] && $_REQUEST[busq_esfera] != '')
    {
      $cod_eal = explode( "-" , $_REQUEST[busq_esfera] );
      $cod_eal = trim( $cod_eal[0] );
      $cod_esfera = $this -> homologaEAL($cod_eal);
    }
    /*echo "<pre>";
    print_r($cod_esfera);
    echo "</pre>";*/
    //die();
    
    //---------------------------------------------
    $mSql = " ( SELECT a.*, c.num_placax, UPPER(IF( c.cod_conduc = '1001', c.nom_conduc, d.abr_tercer )) AS nom_conduc, UPPER(h.abr_tercer) AS abr_tercer, UPPER(f.nom_ciudad) AS nom_ciuori, UPPER(g.nom_ciudad) AS nom_ciudes FROM (
              SELECT b.cod_contro, b.nom_contro, a.num_despac, a.fec_noveda, CONCAT(UPPER(z.nom_usuari), ' - ', a.usr_creaci) as usr_creaci, a.cod_verpcx ";

    $mSql .= " FROM ". BASE_DATOS .".tab_despac_noveda a,
                    ". BASE_DATOS .".tab_genera_contro b,
                    ". BASE_DATOS .".tab_genera_usuari z
                    ";

    $mSql .= " WHERE a.cod_contro = b.cod_contro AND
                     a.usr_creaci = z.cod_usuari AND
                     b.ind_virtua = '0' AND
                     b.ind_estado = '1' AND 
                     a.cod_noveda IN ( '71', '119' ) AND 
                     ( b.nom_contro LIKE '%EAL%' OR b.nom_contro LIKE '%E@L%')";
    
    if( $_REQUEST[fec_incial] && $_REQUEST[fec_finali] )
    {
      $mSql .= " 
                AND DATE(a.fec_noveda) BETWEEN '".$_REQUEST[fec_incial]."' AND '".$_REQUEST[fec_finali]."' ";
    }
                     
     $mSql .= ") AS a,";

    $mSql .= " ". BASE_DATOS .".tab_despac_vehige c, 
                    ". BASE_DATOS .".tab_tercer_tercer d,
                    ". BASE_DATOS .".tab_despac_despac e,  
                    ". BASE_DATOS .".tab_genera_ciudad f,  
                    ". BASE_DATOS .".tab_genera_ciudad g,  
                    ". BASE_DATOS .".tab_tercer_tercer h  ";
                    
    $mSql .= "WHERE a.num_despac = c.num_despac AND
                    c.cod_conduc = d.cod_tercer AND
                    a.num_despac = e.num_despac AND
                    e.cod_ciuori = f.cod_ciudad AND
                    e.cod_ciudes = g.cod_ciudad AND
                    c.cod_transp = h.cod_tercer ";

    if( $cod_tercer && $cod_tercer != '' )
    {
      $mSql .= " AND h.cod_tercer = '".$cod_tercer."' ";
    }
    
        
    if( $cod_esfera )
    {
      $mSql .= " AND a.cod_contro IN( ";
      for( $j = 0; $j < sizeof($cod_esfera); $j++ )
      {
      $cod_esferax[] = $cod_esfera[$j][0];
      }
      $mSql .= join(',', $cod_esferax );
      $mSql .= ",".$cod_eal." )";
    }
    elseif( $cod_eal )
    {
      $mSql .= " AND a.cod_contro = ". $cod_eal ." ";
    }
    
    $mSql .= " ) UNION ";
    $mSql .= " ( SELECT a.cod_contro, b.nom_contro, a.num_repnov, a.fec_repnov, CONCAT( UPPER( c.nom_usuari ), ' - ', a.usr_creaci ) as usr_creaci, 
                        a.num_placax, '-' AS nom_conduc, 
                        UPPER( h.abr_tercer ) AS abr_tercer, '-' AS nom_ciuori, '-' AS nom_ciudes, '' cod_verpcx
                FROM ".BASE_DATOS.".tab_report_noveda a,
                     ".BASE_DATOS.".tab_genera_contro b,
                     ".BASE_DATOS.".tab_genera_usuari c,
                     ".BASE_DATOS.".tab_tercer_tercer h
                WHERE a.cod_contro = b.cod_contro
                  AND a.usr_creaci = c.cod_usuari
                  AND a.cod_tercer = h.cod_tercer
                  AND ( b.nom_contro LIKE '%EAL%' OR b.nom_contro LIKE '%E@L%' )
                  AND a.cod_noveda IN ( '71', '119' ) 
                  AND b.ind_virtua = '0'
                  AND b.ind_estado = '1'";
    
     if( $_REQUEST[fec_incial] && $_REQUEST[fec_finali] )
    {
      $mSql .= " 
                AND DATE( a.fec_repnov ) BETWEEN '".$_REQUEST[fec_incial]."' AND '".$_REQUEST[fec_finali]."' ";
    }
    
    if( $cod_tercer && $cod_tercer != '' )
    {
      $mSql .= " AND h.cod_tercer = '".$cod_tercer."' ";
    }
    
    if( $cod_esfera )
    {
      $mSql .= " AND a.cod_contro IN( ";
      for( $j = 0; $j < sizeof($cod_esfera); $j++ )
      {
      $cod_esferax[] = $cod_esfera[$j][0];
      }
      $mSql .= join(',', $cod_esferax );
      $mSql .= ",".$cod_eal." )";
    }
    elseif( $cod_eal )
    {
      $mSql .= " AND a.cod_contro = ". $cod_eal ." ";
    }
    
    $mSql .= " ) ";
    
    //$mSql .= " ORDER BY a.nom_contro, a.fec_noveda DESC";
    $mSql .= " ORDER BY 2, 4 DESC";
    
    //------------------------------------------------------
    $consult = new Consulta( $mSql, $this -> conexion );      
    $_INFORME = $consult -> ret_matriz( 'a' ) ;
    
  /*echo "<pre>";
  print_r( $_INFORME );
  echo "</pre>";*/

    $cont = 0;
    $html = NULL;
    $html_z = " <style>
                .cellHead
                {
                  padding:5px 10px;
                  font-size:12px;
                  background: -webkit-gradient(linear, left top, left bottom, from( #32984B ), to( #257038 )); 
                  background: -moz-linear-gradient(top, #32984B, #257038 ); 
                  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#32984B', endColorstr='#257038');
                  color:#fff;
                  font-weight:bold;
                  text-align:center;
                  
                }
                .cellInfo
                {
                  padding:5px 10px;
                  font-size:12px;
                  color:#257038;
                  border:1px solid #ccc;
                }
              </style>";
    
    $html_z .="<td><a href ='index.php?window=central&cod_servic=$GLOBALS[cod_servic]&opcion=xls' >[ Excel ]</a></td>";
    
    $html .=" <table border=0 width='100%'>
                <tr>
                  <td class='cellHead' colspan=9 >Se Encontraron ".sizeof( $_INFORME )." Registros</td>
                </tr>"; 
                
    $html .="<tr>
              <td class='cellHead' width='10%'>EAL</td>
              <td class='cellHead' width='15%'>Despacho</td>
              <td class='cellHead' width='15%'>".utf8_encode("N° de Confirmación")."</td>
              <td class='cellHead' width='15%'>Conductor</td>
              <td class='cellHead' width='10%'>Placa</td>
              <td class='cellHead' width='10%'>Origen</td>
              <td class='cellHead' width='10%'>Destino</td>
              <td class='cellHead' width='10%'>Fecha/Hora</td>
              <td class='cellHead' width='10%'>Transportadora</td>
              <td class='cellHead' width='10%'>Usuario</td>
            </tr>";
    
    if( $_INFORME && sizeof( $_INFORME ) > 0 )
    {
      
      for( $k = 0; $k < sizeof( $_INFORME ); $k++  )
      {
        $_ROW = $_INFORME[$k];
        $cont++;
        $html .= "<tr>";
          $html .= "<td class='cellInfo'  width='10%'>".$this -> homologar($_ROW['cod_contro'])."</td>";
          $html .= "<td class='cellInfo'  width='15%'>".$_ROW['num_despac']."</td>";
          $html .= "<td class='cellInfo'  width='15%'>".($_ROW['cod_verpcx'] == NULL ? "No Registra": $_ROW['cod_verpcx'])."</td>";
          $html .= "<td class='cellInfo'  width='15%'>".$_ROW['nom_conduc']."</td>";
          $html .= "<td class='cellInfo'  width='10%'>".$_ROW['num_placax']."</td>";
          $html .= "<td class='cellInfo'  width='10%'>".$_ROW['nom_ciuori']."</td>";
          $html .= "<td class='cellInfo'  width='10%'>".$_ROW['nom_ciudes']."</td>";
          $html .= "<td class='cellInfo'  width='10%'>".$_ROW['fec_noveda']."</td>";
          $html .= "<td class='cellInfo'  width='10%'>".$_ROW['abr_tercer']."</td>";
          $html .= "<td class='cellInfo'  width='10%'>".$_ROW['usr_creaci']."</td>";
        $html .= "</tr>";
        if( $this -> homologar($_ROW['cod_contro']) != $this -> homologar($_INFORME[$k+1]['cod_contro']) )
        {
          $html .= "<tr>
                    <td class='cellHead' colspan=9 >TOTAL: ".$cont." REPORTES</td>
                  </tr>"; 
          
          if ( $k != sizeof( $_INFORME )-1 )
          {
          $html .="<tr>
              <td class='cellHead' width='10%'>EAL</td>
              <td class='cellHead' width='15%'>Despacho</td>
              <td class='cellHead' width='15%'>".utf8_encode("N° de Confirmación")."</td>
              <td class='cellHead' width='15%'>Conductor</td>
              <td class='cellHead' width='10%'>Placa</td>
              <td class='cellHead' width='10%'>Origen</td>
              <td class='cellHead' width='10%'>Destino</td>
              <td class='cellHead' width='10%'>Fecha/Hora</td>
              <td class='cellHead' width='10%'>Transportadora</td>
              <td class='cellHead' width='10%'>Usuario</td>
            </tr>"; 
          }       
          $cont = 0;
        }
      }
      
    }
  
  $_SESSION[html] = NULL;
  $_SESSION[html] = $html;
  echo $html_z ;
  echo($_SESSION[html]);
  }

}

$pagina = new Informe( $_SESSION['conexion'] );

?>
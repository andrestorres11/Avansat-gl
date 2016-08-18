<?php
/****************************************************************************
NOMBRE:   MODULO INFORME DE CANTIDAD DE DESPACHOS POR TRANSPORTADORA
FUNCION:  INFORME DE NOVEDADES DE LA TRANSP
AUTOR: JORGE PRECIADO
FECHA CREACION : 17 OCTUBRE 2013
****************************************************************************/
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
class CanDesTra
{
    var  $conexion;

    function __construct( $conexion )
    {
        //$this -> ShowMatriz( $_REQUEST );
        $this -> conexion = $conexion;
        ini_set( "memory_limit", "128M" );
        
        switch ($_REQUEST['opcion'])
        {
            case  1:
                $this -> MostrarResul();
                break;
            case  2:
                $this -> MostrarDetal();
                break;
            case  3:
                $this -> ExpInformExcel();
                break;
            case  4:
                $this -> ExpInformExcel2();
                break;
            default:
                $this -> Filtro();
                break;
        }
    }
  
    function Filtro()
    {
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_candes_empres.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/regnov.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        
        //Scrip del calendario
        echo '
                <script>
                    jQuery(function($) 
                    { 
                        $( "#fec_inicialID,#fec_finalID" ).datepicker();
                        $( "#horainiID,#horafinID" ).timepicker({
                            timeFormat:"hh:mm",
                            showSecond: false
                        });

                        $.mask.definitions["A"]="[12]";
                        $.mask.definitions["M"]="[01]";
                        $.mask.definitions["D"]="[0123]";
                        $.mask.definitions["H"]="[012]";
                        $.mask.definitions["N"]="[012345]";
                        $.mask.definitions["n"]="[0123456789]";

                        $( "#fec_inicialID,#fec_finalID" ).mask("Annn-Mn-Dn");
                        $( "#horainiID,#horafinID" ).mask("Hn:Nn");
                    });
                </script>';

        $transpor = $this -> GetTransporta( 2 );

        echo '
                <script>
                    $(function() 
                    {
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
                        $( "#busq_transpID" ).autocomplete({
                            source: tranportadoras,
                            delay: 100
                        }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
                        $( "#busq_transpID" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
                    });
                </script>';

        if( !$_REQUEST['fec_inicia'] ) $_REQUEST['fec_inicia'] = date( "Y-m-d", mktime(0, 0, 0, date( "m-" ), 1, date( "Y" )));
		if( !$_REQUEST['fec_final'] ) $_REQUEST['fec_final'] = date( "Y-m-d" );
        
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe de Cantidad de Despachos por Transportadora", "formulario\" id=\"formularioID");

        $formulario -> nueva_tabla();
        $formulario -> texto ("Fecha Inicial","text","fec_inicial\" id=\"fec_inicialID",0,7,7,"",$_REQUEST['fec_inicia'] );
        $formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID",1,7,7,"",$_REQUEST['fec_final'] );
        $formulario -> texto ("Nit / Nombre","text","busq_transp\" id=\"busq_transpID",1,30,30,"","" );

        $formulario -> nueva_tabla();
        $formulario -> botoni("Buscar","MostrarResul()",0);
        $formulario -> nueva_tabla();
        echo "<BR><BR>";
        echo "<BR><BR>";
        echo "<BR><BR>";
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion\" id=\"opcionID",0,0);
        $formulario -> cerrar();
    }

    function MostrarResul()
    {
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_candes_empres.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        
        $this -> Style();
        
        if(!$GLOBALS["horaini"]) $GLOBALS["horaini"]='00:00:00';
        if(!$GLOBALS["horafin"]) $GLOBALS["horafin"]='23:59:00';

        $transporta = $this -> GetTransporta( NULL );
        $mHtml = NULL;
        $mHtml2 = NULL;

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES DE LA TRANSPORTADORA", "formulario\" id=\"formularioID");
        
        $formulario -> nueva_tabla();
        $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_candes_empres.php",0);
        $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
        $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
        $formulario -> oculto("num_serie",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
        $formulario -> oculto("opcion\" id=\"opcionID",0,0);
        $_SESSION['COD_NOVEDAD'] = COD_NOVEDAD;
        
        $fec_inici = $GLOBALS['fec_inicial']." ".$GLOBALS['horaini']; 
        $fec_final = $GLOBALS['fec_final']." ".$GLOBALS['horafin'];
        
        $formulario -> nueva_tabla();
        $formulario -> linea("Informe de Cantidad de Despachos por Transportadora de ".$fec_inici." al ".$fec_final  ,1,"t2"); 
        $formulario -> nueva_tabla();
        foreach($transporta AS $transpor)
        {
            $despachos = $this -> GetDespachos( $transpor['cod_tercer'], NULL, $fec_inici, $fec_final);
            $mHtml  = "<table width='100%'>";
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead align='left' colspan=4 >NIT: ".$transpor['cod_tercer']." - Trasnportadora: ".$transpor['abr_tercer']."</th>";
                $mHtml .= "</tr>";
                
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead width='25%' >ORIGEN</th>";
                    $mHtml .= "<th class=cellHead width='25%' >CANTIDAD</th>";
                    $mHtml .= "<th class=cellHead width='25%' >%</th>";
                    $mHtml .= "<th class=cellHead width='25%' >NOVEDADES <br>(Varados, Accidentes Tránsito, Hurtos, continua no comunicación, saqueo)</th>";
                $mHtml .= "</tr>";

                $origen = $this -> GetDespachos( $transpor['cod_tercer'], 1, $fec_inici, $fec_final);
                foreach($origen AS $row)
                {
                    $mHtml .= "<tr>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['abr_ciudad']."</td>";
                        $mHtml .= "<td class='cellInfo' align='center' onclick=\"infoDetEmp('".$row['cod_ciuori']."','".$transpor['cod_tercer']."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."', 1);\" ><a href='#'>".$row['num_despac']."</a></td>";
                        $mHtml .= "<td class='cellInfo' align='center' >".number_format(($row['num_despac']/$despachos[0]['num_despac'])*100,0)."</td>";
                        $mHtml .= "<td class='cellInfo' align='center' onclick=\"infoDetEmp('".$row['cod_ciuori']."','".$transpor['cod_tercer']."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."', 2);\" ><a href='#'>".sizeof($this -> GetNovedades( $transpor['cod_tercer'], $row['cod_ciuori'], $fec_inici, $fec_final ))."</a></td>";
                    $mHtml .= "</tr>";
                }
                
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead align='left' >DESPAHOS</th>";
                    $mHtml .= "<th class=cellInfo align='left' colspan=3 onclick=\"infoDetEmp('','".$transpor['cod_tercer']."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."', 1);\" ><a href='#'>".$despachos[0]['num_despac']."</a></th>";
                $mHtml .= "</tr>";
                
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead align='left' >RUTAS CREADAS</th>";
                    $mHtml .= "<th class=cellInfo align='left' colspan=3 onclick=\"infoDetEmp('','".$transpor['cod_tercer']."','".$GLOBALS['fec_inicial']."','".$GLOBALS['fec_final']."','".$GLOBALS['horaini']."', '".$GLOBALS['horafin']."', 3);\" ><a href='#'>".sizeof($this -> GetRutas( $transpor['cod_tercer'], $fec_inici, $fec_final ))."</a></th>";
                $mHtml .= "</tr>";
            $mHtml  .= "</table>";
            if( $despachos[0]['num_despac'] > 0 )
            $mHtml2 .= $mHtml;
        }
        echo $mHtml2;  
        $_SESSION['LIST_TOTAL'] = "";        
        $_SESSION['LIST_TOTAL'] = $mHtml2;        
        echo '<tr>
                <td>
                    <div id="AplicationEndDIV"></div>
                    <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
                    <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
                        <div id="filtros" ></div>
                        <div id="result" ></div>
                    </div>
                    <div id="alg"> <table></table></div>
                </td>
              </tr>';  
        $formulario -> nueva_tabla(); 
        $formulario -> botoni("Excel","exportarXls()",1);
        $formulario -> cerrar();              
    }
    
    function MostrarDetal()
    {
        session_start();
        define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define ('ESTILO', $_SESSION['ESTILO']);
        define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
        define ('COD_NOVEDAD', $_SESSION['COD_NOVEDAD']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        
        $this -> conexion = new Conexion( "bd10.intrared.net:3306", $_SESSION['USUARIO'], $_SESSION['CLAVE'], $_SESSION['BASE_DATOS'] );
        
        $mHtml = NULL;
        $mHtml2 = NULL;
        
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DETALLADO", "formulario");
        $formulario -> nueva_tabla(); 
        $formulario -> botoni("Excel","exportarXls2()",0);
        $formulario -> botoni("Cerrar","ClosePopup()",1);
        $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
        $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);

        $formulario -> cerrar();
        $formulario -> nueva_tabla();
        
        $fec_inici = $_REQUEST['fec_inicial']." ".$_REQUEST['horaini']; 
        $fec_final = $_REQUEST['fec_final']." ".$_REQUEST['horafin'];
        
        if( $_REQUEST['ind'] == 1 )
            $despachos = $this -> GetDetalleDespac( $_REQUEST['empres'], $_REQUEST['cod_ciuori'], $fec_inici, $fec_final );
        elseif( $_REQUEST['ind'] == 2 )
            $despachos = $this -> GetNovedades( $_REQUEST['empres'], $_REQUEST['cod_ciuori'], $fec_inici, $fec_final );
        else
            $despachos = $this -> GetRutas( $_REQUEST['empres'], $fec_inici, $fec_final );
            
        $mHtml  = "<table width='100%'>";
            
            if( $_REQUEST['ind'] != '3' )
            {
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead align='left' >TOTAL DESPACHOS</th>";
                    $mHtml .= "<th class=cellInfo align='left' colspan=4 >".sizeof($despachos)."</th>";
                $mHtml .= "</tr>";
            
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead width='20%' >DESPACHO</th>";
                    $mHtml .= "<th class=cellHead width='20%' >MANIFIESTO</th>";
                    $mHtml .= "<th class=cellHead width='20%' >PLACA</th>";
                    $mHtml .= "<th class=cellHead width='20%' >CONDUCTOR</th>";
                    $mHtml .= "<th class=cellHead width='20%' >FECHA DE SALIDA</th>";
                $mHtml .= "</tr>";
                
                foreach($despachos AS $row)
                {
                    $mHtml .= "<tr>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['num_despac']."&nbsp;</td>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['cod_manifi']."&nbsp;</td>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['num_placax']."&nbsp;</td>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['abr_tercer']."&nbsp;</td>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['fec_salida']."&nbsp;</td>";
                    $mHtml .= "</tr>";
                }
                $mHtml  .= "</table>";
                if( $despachos[0]['num_despac'] > 0 )
                    $mHtml2 .= $mHtml;
                echo $mHtml2; 
            }
            else
            {
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead align='left' >TOTAL RUTAS</th>";
                    $mHtml .= "<th class=cellInfo align='left' colspan=4 >".sizeof($despachos)."</th>";
                $mHtml .= "</tr>";
                
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead width='30%' >CODIGO</th>";
                    $mHtml .= "<th class=cellHead width='30%' >NOMBRE</th>";
                    $mHtml .= "<th class=cellHead width='30%' >FECHA DE CREACION</th>";
                $mHtml .= "</tr>";
                
                foreach($despachos AS $row)
                {
                    $mHtml .= "<tr>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['cod_rutasx']."&nbsp;</td>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['nom_rutasx']."&nbsp;</td>";
                        $mHtml .= "<td class='cellInfo' align='left' >".$row['fec_creaci']."&nbsp;</td>";
                    $mHtml .= "</tr>";
                }
                $mHtml  .= "</table>";
                $mHtml2 .= $mHtml;
                echo $mHtml2;
            }
            

        $_SESSION['DETA_TOTAL'] = "";        
        $_SESSION['DETA_TOTAL'] = $mHtml2; 

        echo "<br>";
        
        $formulario -> nueva_tabla(); 
        $formulario -> botoni("Excel","exportarXls2()",0);
        $formulario -> botoni("Cerrar","ClosePopup()",1);
        $formulario -> cerrar();
    }
    
    function GetTransporta( $ind = NULL )
    {
        $query ="SELECT a.cod_tercer, UPPER( a.abr_tercer ) AS abr_tercer
                   FROM ".BASE_DATOS.".tab_tercer_tercer a,
                        ".BASE_DATOS.".tab_tercer_activi b
                  WHERE a.cod_tercer = b.cod_tercer AND
                        b.cod_activi = ".COD_FILTRO_EMPTRA."";

        if($GLOBALS['busq_transp'] && $ind != 2 )
        {
            $v = explode(" - ", $GLOBALS['busq_transp']);
            $query .= " AND a.cod_tercer ='".$v[0]."'";   
        }
        
        if($ind == 2)
            $query .= " ORDER BY 2 ";
            
        $consulta = new Consulta($query, $this -> conexion);    
        return $consulta -> ret_matriz();
    }
    
    function GetRutas( $tranpor = NULL, $fec_inici = NULL, $fec_final = NULL )
    {
        $query ="SELECT a.cod_rutasx, UPPER( b.nom_rutasx ) AS nom_rutasx, a.fec_creaci
                   FROM ".BASE_DATOS.".tab_genera_ruttra a,
                        ".BASE_DATOS.".tab_genera_rutasx b
                  WHERE a.cod_rutasx = b.cod_rutasx AND
                        a.cod_transp = ".$tranpor."";
        if( $fec_inici )
            $query .= " AND a.fec_creaci >= '".$fec_inici."' ";
        if( $fec_final )
            $query .= " AND a.fec_creaci <= '".$fec_final."' ";
        $consulta = new Consulta($query, $this -> conexion);    
        return $consulta -> ret_matriz();
    }
    
    function GetDespachos( $tranpor = NULL, $ind = NULL, $fec_inici = NULL, $fec_final = NULL )
    {
        $query ="SELECT a.cod_transp, 
                        COUNT( DISTINCT a.num_despac) AS num_despac, 
                        UPPER( c.abr_ciudad ) AS abr_ciudad,
                        b.cod_ciuori
                   FROM ".BASE_DATOS.".tab_despac_vehige a,
                        ".BASE_DATOS.".tab_despac_despac b,
                        ".BASE_DATOS.".tab_genera_ciudad c
                  WHERE a.num_despac = b.num_despac 
                    AND b.cod_ciuori = c.cod_ciudad 
                    AND a.ind_activo = 'S' 
                    AND a.cod_transp = ".$tranpor."
                    AND b.ind_anulad = 'R'
                ";
        if( $fec_inici )
            $query .= " AND b.fec_salida >= '".$fec_inici."' ";
        if( $fec_final )
            $query .= " AND b.fec_salida <= '".$fec_final."' ";
        
        if($ind == 1)
            $query .= " GROUP BY a.cod_transp, abr_ciudad ORDER BY 3 ";
        
        $consulta = new Consulta($query, $this -> conexion);
        return $consulta -> ret_matriz();
    }
    
    function GetNovedades( $tranpor = NULL, $cod_ciuori = NULL, $fec_inici = NULL, $fec_final = NULL )
    {
        $query ="( SELECT a.num_despac,
                          a.num_placax,
                          UPPER( d.abr_tercer ) AS abr_tercer,
                          b.cod_manifi,
                          b.fec_salida                          
                     FROM ".BASE_DATOS.".tab_despac_vehige a,
                          ".BASE_DATOS.".tab_despac_despac b,
                          ".BASE_DATOS.".tab_despac_noveda c,
                          ".BASE_DATOS.".tab_tercer_tercer d
                    WHERE a.num_despac = b.num_despac
                      AND b.num_despac = c.num_despac
                      AND a.cod_conduc = d.cod_tercer
                      AND a.ind_activo = 'S' 
                      AND a.cod_transp = ".$tranpor."
                      AND b.ind_anulad = 'R'
                      AND b.cod_ciuori = '".$cod_ciuori."'
                      AND c.cod_noveda IN (".COD_NOVEDAD.") ";//9, 49,27, 13, 5003, 12
        
        if( $fec_inici )
            $query .= " AND b.fec_salida >= '".$fec_inici."' ";
        if( $fec_final )
            $query .= " AND b.fec_salida <= '".$fec_final."' ";
            
        $query .= " GROUP BY a.num_despac, c.cod_noveda
                        ) 
                    UNION (
                   SELECT a.num_despac,
                          a.num_placax,
                          UPPER( d.abr_tercer ) AS abr_tercer,
                          b.cod_manifi,
                          b.fec_salida 
                     FROM ".BASE_DATOS.".tab_despac_vehige a,
                          ".BASE_DATOS.".tab_despac_despac b,
                          ".BASE_DATOS.".tab_despac_contro c,
                          ".BASE_DATOS.".tab_tercer_tercer d
                    WHERE a.num_despac = b.num_despac
                      AND b.num_despac = c.num_despac
                      AND a.cod_conduc = d.cod_tercer
                      AND a.ind_activo = 'S' 
                      AND a.cod_transp = ".$tranpor."
                      AND b.ind_anulad = 'R'
                      AND b.cod_ciuori = '".$cod_ciuori."'
                      AND c.cod_noveda IN (".COD_NOVEDAD.") ";//9, 49,27, 13, 5003, 12

        if( $fec_inici )
            $query .= " AND b.fec_salida >= '".$fec_inici."' ";
        if( $fec_final )
            $query .= " AND b.fec_salida <= '".$fec_final."' ";
            
        $query .= "GROUP BY a.num_despac, c.cod_noveda
                        )
                ";
        
            
        $consulta = new Consulta($query, $this -> conexion);
        return $consulta -> ret_matriz();
    }
    
    function GetDetalleDespac( $tranpor = NULL, $cod_ciuori = NULL, $fec_inici = NULL, $fec_final = NULL )
    {
        $query ="SELECT 
                        DISTINCT a.num_despac, 
                        a.cod_transp, 
                        a.num_placax,
                        UPPER( d.abr_tercer ) AS abr_tercer,
                        b.cod_manifi,
                        b.fec_salida
                   FROM ".BASE_DATOS.".tab_despac_vehige a,
                        ".BASE_DATOS.".tab_despac_despac b,
                        ".BASE_DATOS.".tab_tercer_tercer d
                  WHERE a.num_despac = b.num_despac 
                    AND a.cod_conduc = d.cod_tercer 
                    AND a.ind_activo = 'S' 
                    AND a.cod_transp = '".$tranpor."'
                    AND b.ind_anulad = 'R'
                ";
        
        if( $fec_inici )
            $query .= " AND b.fec_salida >= '".$fec_inici."' ";
        if( $fec_final )
            $query .= " AND b.fec_salida <= '".$fec_final."' ";
        
        if($cod_ciuori)
            $query .= " AND b.cod_ciuori = '".$cod_ciuori."' ";
            
        $consulta = new Consulta($query, $this -> conexion);
        return $consulta -> ret_matriz();
    }
    
    function ShowMatriz( $matriz = NULL)
    {
        echo "<pre>";
        print_r($matriz);
        echo "</pre>";
        die();
    }
    
    function Style()
    {
      echo "	<style>
              .cellHead
              {
                padding:5px 10px;
                background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                background: -moz-linear-gradient(top, #009617, #00661b ); 
                background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
                background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
                color:#fff;
                text-align:center;
              }
              
              .footer
              {
                padding:5px 10px;
                background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                background: -moz-linear-gradient(top, #009617, #00661b ); 
                background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
                background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
                color:#fff;
                text-align:left;
              }

              .cellHead2
              {
                padding:5px 10px;
                background: #03ad39;
                background: -webkit-gradient(linear, left top, left bottom, from( #03ad39 ), to( #00660f )); 
                background: -moz-linear-gradient(top, #03ad39, #00660f ); 
                background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
                background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
                color:#fff;
                text-align:right;
              }

              tr.row:hover  td
              {
                background-color: #9ad9ae;
              }
              .cellInfo
              {
                padding:5px 10px;
                background-color:#fff;
                border:1px solid #ccc;
              }

              .cellInfo2
              {
                padding:5px 10px;
                background-color:#9ad9ae;
                border:1px solid #ccc;
              }

              .label
              {
                font-size:12px;
                font-weight:bold;
              }

              .select
              {
                background-color:#fff;
                border:1px solid #009617;
              }

              .boton
              {
                background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                background: -moz-linear-gradient(top, #009617, #00661b ); 
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
                color:#fff;
                border:1px solid #fff;
                padding:3px 15px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
              }

              .boton:hover
              {
                background:#fff;
                color:#00661b;
                border:1px solid #00661b;
                cursor:pointer;
              }
      </style>";
    }
    
    function ExpInformExcel()
    {    
        $archivo = "inf_candes_empres".date( "Y_m_d" ).".xls";
        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="'.$archivo.'"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $_SESSION['LIST_TOTAL']; 
    }
    
    function ExpInformExcel2()
    {    
        $archivo = "inf_candes_empres".date( "Y_m_d" ).".xls";
        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="'.$archivo.'"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $_SESSION['DETA_TOTAL']; 
    }
}
$service= new  CanDesTra($_SESSION['conexion']);
?>
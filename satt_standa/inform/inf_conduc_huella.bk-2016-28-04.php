<?php
/***************************************************************************************************
NOMBRE:   MODULO INFORME DE CONDUCTORES RESGISTRADOS POR EL SISTEMA BIOMETRICO POR TRANSPORTADORA
FUNCION:  INFORME DE NOVEDADES DE LA TRANSP
AUTOR: JORGE PRECIADO
FECHA CREACION : 21 OCTUBRE 2013
****************************************************************************************************/
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
class ConcudHuellla
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
                $this -> ExpInformExcel();
                break;
            default:
                $this -> Filtro();
                break;
        }
    }
  
    function Filtro()
    {
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_conduc_huella.js\"></script>\n";
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
        if(!$GLOBALS["horaini"])
            $GLOBALS["horaini"]='00:00:00';
        if(!$GLOBALS["horafin"])
            $GLOBALS["horafin"]='23:59:00';
/*
        $query = "SELECT a.cod_tercer,a.nom_tercer,a.nom_apell1,a.nom_apell2,
                         a.cod_ciudad,a.num_telef1,a.num_telmov,a.dir_domici,
                         a.dir_emailx,a.cod_estado, d.fec_creaci, e.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a, 
                         ".BASE_DATOS.".tab_despac_vehige b, 
                         ".BASE_DATOS.".tab_tercer_huella d,
                         ".BASE_DATOS.".tab_tercer_tercer e                         
                   WHERE 1 = 1 
                     AND a.cod_tercer = b.cod_conduc
                     AND b.cod_conduc = d.cod_tercer
                     AND b.cod_transp = e.cod_tercer
                     AND d.cod_huella !=  ''
                     AND d.fec_creaci >= '".$GLOBALS['fec_inicial']." ".$GLOBALS['horaini']."'
                     AND d.fec_creaci <= '".$GLOBALS['fec_final']." ".$GLOBALS['horafin']."' 
                   ";

        if($GLOBALS['busq_transp'])
        {
            $v = split(" - ", $GLOBALS['busq_transp']);
            $query .= " AND b.cod_transp = '".$v[0]."'";
        }

        $query = $query." ORDER BY 2";
        
        //echo "<hr>".$query."<hr>";

        $consec = new Consulta($query, $this -> conexion);
        $matriz = $consec -> ret_matriz();
        */
        if($GLOBALS['busq_transp'])
        {
            $v = explode(" - ", $GLOBALS['busq_transp']);
        }
        
        $matriz = $this -> GetConductores( $v[0], $GLOBALS['fec_inicial']." ".$GLOBALS['horaini'], $GLOBALS['fec_final']." ".$GLOBALS['horafin'] );
        
        $this -> Style();
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_conduc_huella.js\"></script>\n";

        $formulario = new Formulario ("index.php","post","LISTADO DE CONDUCTORES RESGISTRADOS POR EL SISTEMA BIOMETRICO","form_item");

        $formulario -> nueva_tabla();
        $mHtml  = "<table width='100%'>";
                $mHtml .= "<tr>";
                    $mHtml .= "<th class=cellHead align='left' colspan=10 >Se Encontro un Total de ".sizeof($matriz)." Conductore(s)</th>";
                $mHtml .= "</tr>";
                
                $mHtml .= "<tr>";
                    if(!$GLOBALS['busq_transp'])
                        $mHtml .= "<th class=cellHead width='10%' >TRANSPORTADORA</th>";
                    $mHtml .= "<th class=cellHead width='10%' >CC</th>";
                    $mHtml .= "<th class=cellHead width='20%' >NOMBRE</th>";
                    $mHtml .= "<th class=cellHead width='10%' >CIUDAD</th>";
                    $mHtml .= "<th class=cellHead width='10%' >TELEFONO</th>";
                    $mHtml .= "<th class=cellHead width='10%' >CELULAR</th>";
                    $mHtml .= "<th class=cellHead width='10%' >DIRECCION</th>";
                    $mHtml .= "<th class=cellHead width='10%' >E-MAIL</th>";
                    $mHtml .= "<th class=cellHead width='10%' >ESTADO</th>";
                    $mHtml .= "<th class=cellHead width='10%' >FECHA HORA</th>";
                $mHtml .= "</tr>";

        $objciud = new Despachos($GLOBALS[cod_servic], $GLOBALS[opcion], $this -> aplica, $this -> conexion);

        for($i = 0; $i < sizeof($matriz); $i++)
        {
            if($matriz[$i][9] != COD_ESTADO_ACTIVO)
                $estilo = "ie";
            else
                $estilo = "i";

            if($matriz[$i][9] == COD_ESTADO_ACTIVO)
                $estado = "Activo";
            else if($matriz[$i][9] == COD_ESTADO_INACTI)
                $estado = "Inactivo";
            else if($matriz[$i][9] == COD_ESTADO_PENDIE)
                $estado = "Pendiente";

            $ciudad_a = $objciud -> getSeleccCiudad($matriz[$i][4]);

            //$matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&conduc=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

            $mHtml .= "<tr>";
                if(!$GLOBALS['busq_transp'])
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][11]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][0]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][1]." ".$matriz[$i][2]." ".$matriz[$i][3]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$ciudad_a[0][1]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][5]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][6]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][7]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][8]."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$estado."</td>";
                $mHtml .= "<td class='cellInfo' align='left' >&nbsp;".$matriz[$i][10]."</td>";
            $mHtml .= "</tr>";
        }
        echo $mHtml;  
        $_SESSION['LIST_TOTAL'] = "";        
        $_SESSION['LIST_TOTAL'] = $mHtml; 
        
        $formulario -> nueva_tabla(); 
        $formulario -> botoni("Excel","exportarXls()",1);

        $formulario -> nueva_tabla();
        $formulario -> oculto("opcion",1,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
        $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
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
    
    function GetConductores( $tranpor = NULL, $fec_inici = NULL, $fec_final = NULL )
    {
        $query ="SELECT a.cod_tercer,a.nom_tercer,a.nom_apell1,a.nom_apell2,
                        a.cod_ciudad,a.num_telef1,a.num_telmov,a.dir_domici,
                        a.dir_emailx,a.cod_estado, d.fec_creaci, e.abr_tercer
                   FROM ".BASE_DATOS.".tab_tercer_tercer a, 
                        ".BASE_DATOS.".tab_despac_vehige b, 
                        ".BASE_DATOS.".tab_tercer_huella d,
                        ".BASE_DATOS.".tab_tercer_tercer e                         
                  WHERE 1 = 1 
                    AND a.cod_tercer = b.cod_conduc
                    AND b.cod_conduc = d.cod_tercer
                    AND b.cod_transp = e.cod_tercer
                    AND d.cod_huella !=  '' ";
        
        if( $tranpor )
            $query .= " AND b.cod_transp = '".$tranpor."'";
        
        if( $fec_inici )
            $query .= " AND d.fec_creaci >= '".$fec_inici."' ";
        
        if( $fec_final )
            $query .= " AND d.fec_creaci <= '".$fec_final."' ";
            
        $query = $query." GROUP BY a.cod_tercer ORDER BY 2";
        
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
        $archivo = "inf_conduc_biometrico".date( "Y_m_d" ).".xls";
        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="'.$archivo.'"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $_SESSION['LIST_TOTAL']; 
    }

}
$service= new  ConcudHuellla($_SESSION['conexion']);
?>
<?php
    
    session_start();
    
    class InfNovedadesUsuario{
        
        var $conexion;
        var $cNull = array( array(0, '---') ); 
        
        function __construct($conexion){
            $this -> conexion = $conexion;
            if(empty($_REQUEST[option]) && !isset($_REQUEST[option]))
                $_REQUEST[option] = 'showFilters';
            
            $this -> $_REQUEST[option]( $_REQUEST );
        }

        function expInformExcel(){
            
            $archivo = "Informe_Novedades_x_Usuario_".date( "Y_m_d" ).".xls";
            header('Content-Type: application/octetstream');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="'.$archivo.'"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo $_SESSION['LIST_TOTAL'];
            
        }
        
        function showFilters(){
            
            $_USUARIOS = array_merge( $this->cNull, $this->getUsuarios() );
            
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda2.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
            echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
            echo $this -> getJqueryCalendar();
            
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe de Novedades", "formulario\" id=\"formularioID");
            $formulario -> nueva_tabla();
            $formulario -> texto ("Fecha Corte","text","fec_corte\" id=\"fec_corteID",0,7,7,"", date('Y-m-d') );
            $formulario -> texto ("Hora Corte","text","hor_corte\" id=\"hor_corteID",1,7,7,"", date('H:i') );
            
            
            echo '<tr>
                    <td align="right" class="celda_titulo">
                        Rango Corte
                    </td>
                    <td class="celda_info" >
                        <input type="text"  class="campo_texto" readonly="readonly" size="5" value="12" id="ran_corteID" name="ran_corte">
                    </td>
                    <td class="celda_info" align="center" colspan="2">
                        <div id="slider" style="width: 50%"></div>
                    </td>
                  </tr>';
            
            $formulario -> lista ("Usuario","cod_usuari\" id=\"ind_estadoID",$_USUARIOS,1 );
            $formulario -> nueva_tabla();
            $formulario -> botoni("Buscar","Listar2()",0);
            $formulario -> nueva_tabla();
            echo "<BR><BR>";
            echo "<BR><BR>";
            echo "<BR><BR>";
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("option\" id=\"optionID",'',0);
            $formulario -> cerrar();
            
        }
        
        
        function getInforme(){
            
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda2.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
            $this -> Style();
            
            if(empty($_REQUEST[hor_corte]))
                $_REQUEST[hor_corte] = date('H:i:00');
            else
                $_REQUEST[hor_corte] .= ':00';
            
            $_USUARIOS = $this->getUsuarios( $_REQUEST );
            
            $_REQUEST[fecha_corte] = $_REQUEST[fec_corte].' '.$_REQUEST[hor_corte];
            
            
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES", "formulario\" id=\"formularioID");
            $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_numero_alarma.php",0);
            $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
            $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
            $formulario -> oculto("num_serie",0,0);
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("opcion",2,0);
            $formulario -> nueva_tabla();
            $formulario -> linea("Cantidad de Novedades por Usuario al ".$_REQUEST[fecha_corte]  ,1,"t2"); 

            $_NOVEDADES = $this -> getNovedades( $_REQUEST ); 
            
            $mHtml  = "<table width='100%'>";
            
            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead rowspan='2'>Usuario</th>";
            foreach($_NOVEDADES AS $_FECHA_ => $_FECHA_VAL){
                $mHtml .= "<th class=cellHead colspan='".count($_FECHA_VAL)."' >{$_FECHA_}</th>";
            }
            $mHtml .= "<th class=cellHead rowspan='2'>TOTAL</th>";
            $mHtml .= "</tr>";
            $mHtml .= "<tr>";
            foreach($_NOVEDADES AS $_FECHA_VAL){
                foreach ( $_FECHA_VAL as $_HORA => $HORA_VAL){
                    $mHtml .= "<th class=cellHead >{$_HORA}</th>";
                }
            }
            $mHtml .= "</tr>";
            
            $_TOTAL_ = array();
            
            foreach($_USUARIOS AS $_USUARIO_){
                
                $_TOTAL = 0;
                $mHtmlr = '';
                
                $mHtmlr .= "<tr class='row'>";
                    $mHtmlr .= "<td class='cellInfo' >{$_USUARIO_[nom_usuari]}</td>";
                    foreach($_NOVEDADES AS $_FECHA => $_FECHA_VAL){
                        foreach ( $_FECHA_VAL as $_HORA => $HORA_VAL){
                            $mHtmlr .= "<td class='cellInfo' align='right' style='cursor: pointer;' ".( ((int)$HORA_VAL[ $_USUARIO_[0] ] ) > 0 ? "  onclick=\"infoNoveda2('IN', '".$_USUARIO_[0]."','".$_REQUEST[fecha_corte]."','".$_HORA."', '".$_REQUEST[ran_corte]."');\" " : null )." >".((int)$HORA_VAL[ $_USUARIO_[0] ])."</td>";
                            $_TOTAL += (int)$HORA_VAL[ $_USUARIO_[0] ];
                            $_TOTAL_[$_HORA] += (int)$HORA_VAL[ $_USUARIO_[0] ];
                        }
                    }
                    $mHtmlr .= "<td class='cellInfo' align='right' style='cursor: pointer;' ".( ((int)$_TOTAL ) > 0 ? "  onclick=\"infoNoveda2('TO', '".$_USUARIO_[0]."','".$_REQUEST[fecha_corte]."','".end( array_keys( $_TOTAL_ ) )."', '".$_REQUEST[ran_corte]."');\" " : null ).">{$_TOTAL}</td>";
                $mHtmlr .= "</tr>";
                    
                    
                if($_TOTAL > 0){
                    $mHtml .= $mHtmlr;
                }
            }
            
            
            $mHtml .= "<tr>";
            $mHtml .= "<th class='cellHead' style='text-align: right;' >TOTAL:</th>";
            foreach ($_TOTAL_ as $row){
                $mHtml .= "<th class='cellHead' style='text-align: right;' >{$row}</th>";
            }
            $mHtml .= "<th class='cellHead' style='text-align: right;'>".array_sum($_TOTAL_)."</th>";
            $mHtml .= "</tr>";
            
            
            $mHtml .= "</table>";
            
            echo $mHtml;
            
            $_SESSION['LIST_TOTAL']=$mHtml;
    
            $formulario -> nueva_tabla();
            $formulario -> botoni("Excel","exportarXls2()",0);
            $formulario -> nueva_tabla();
            
            $formulario -> cerrar();
            
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
        
        function getDetalles( $__REQUEST ){
         
            global $HTTP_POST_FILES;
            session_start();
            $BASE = $_SESSION[BASE_DATOS];
            define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
            define ('ESTILO', $_SESSION['ESTILO']);
            define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
            
            include( "../lib/general/conexion_lib.inc" );
            include( "../lib/general/form_lib.inc" );
            include( "../lib/general/tabla_lib.inc" );
            
            $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
            $_USUARIO_ = $this -> getUsuarios( array('cod_usuari' => $__REQUEST[cod_usuar]) );
            
            
            $_horaCorte = explode(':', $__REQUEST[hora_corte]);
            $_horaCorte[1] = trim(str_replace('00 ', '', $_horaCorte[1]));
            
            if(strcmp($_horaCorte[1], 'PM') == 0){
                $_horaCorte[0] = (int)$_horaCorte[0] + 12;
            }else{
                $_horaCorte[0] = (int)$_horaCorte[0];
            }
            
            if($__REQUEST[tipo]=='IN'){
                $condition1 = " AND HOUR(b.fec_contro) = {$_horaCorte[0]} ";
                $condition2 = " AND HOUR(b.fec_noveda) = {$_horaCorte[0]} ";
                $condition3 = " AND HOUR(b.fec_repnov) = {$_horaCorte[0]} ";
            }else{
                $condition1 = '';
                $condition2 = '';
                $condition3 = '';
            }
            
         $sql = "SELECT b.usr_creaci, b.fec_contro, b.num_despac, a.nom_noveda, b.obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_contro b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_contro <= '{$__REQUEST[fecha_corte]}'
                       AND b.fec_contro >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       {$condition1}
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_noveda, b.num_despac, a.nom_noveda, b.des_noveda as obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_noveda >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_noveda <= '{$__REQUEST[fecha_corte]}'
                       {$condition2}
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_repnov, b.num_repnov, a.nom_noveda, b.obs_repnov as obs_contro
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_report_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_repnov >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_repnov <= '{$__REQUEST[fecha_corte]}'
                       {$condition3}
                       ";
            
            
            $sql = "SELECT a.*, d.abr_tercer
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b,
                           ".BASE_DATOS.".tab_despac_vehige c,
                           ".BASE_DATOS.".tab_tercer_tercer d
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND c.num_despac = a.num_despac
                       AND c.cod_transp = d.cod_tercer
                       AND LOWER(a.usr_creaci) = '".$__REQUEST[cod_usuar]."'
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669) )
                    ORDER BY 6,4,1
                   ";
           
          
            
            $consulta  = new Consulta($sql, $this -> conexion);
            $despachos = $consulta -> ret_matriz();

                
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE NOVEDADES", "formulario");
            $formulario -> nueva_tabla(); 
            $formulario -> botoni("Excel","exportarXls()",0);//Jorge 2703-2012 
            $formulario -> botoni("Cerrar","ClosePopup()",1);//validarCumplidos()
            $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);//Jorge 2703-2012
            $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);//Jorge 2703-2012
            $formulario -> cerrar();
            $formulario -> nueva_tabla();
            $formulario -> linea("Total de Novedades: ".sizeof($despachos)." - ".$_USUARIO_[0][1],0,"t2","15%");
            
            $this -> Style();
            
            $mHtml  = "<table width='100%'>";
            $mHtml .= "<tr>";
                $mHtml .= "<th class=cellHead >Transportadora</th>";
                $mHtml .= "<th class=cellHead >Despacho</th>";
                $mHtml .= "<th class=cellHead >Novedad</th>";
                $mHtml .= "<th class=cellHead >Fecha</th>";
                $mHtml .= "<th class=cellHead >Observacion</th>";
            $mHtml .= "</tr>";
            
            foreach($despachos AS $row){
                 $mHtml .= "<tr>";
                    $mHtml .= "<td class='cellInfo' >{$row[5]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[2]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[3]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[1]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[4]}</td>";
                 $mHtml .= "</tr>";
            }
            
            $mHtml .= "</table>";
            
            echo $mHtml;
            
            $_SESSION['LIST_TOTAL']=$mHtml;
            
            $formulario -> nueva_tabla(); 
            $formulario -> botoni("Excel","exportarXls()",0);
            $formulario -> botoni("Cerrar","ClosePopup()",1);
            $formulario -> cerrar();
        }
        
        
        function getNovedades( $__REQUEST ){
            
            $sql = "SELECT b.usr_creaci, b.fec_contro AS fec_noveda 
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_contro b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_contro <= '{$__REQUEST[fecha_corte]}'
                       AND b.fec_contro >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_noveda
                      FROM ".BASE_DATOS.".tab_genera_noveda a, 
                           ".BASE_DATOS.".tab_despac_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_noveda >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_noveda <= '{$__REQUEST[fecha_corte]}' 
                    UNION ALL
                    SELECT b.usr_creaci, b.fec_repnov AS fec_noveda 
                      FROM ".BASE_DATOS.".tab_genera_noveda a,
                           ".BASE_DATOS.".tab_report_noveda b
                     WHERE a.cod_noveda  = b.cod_noveda
                       AND b.fec_repnov >= DATE_SUB( '{$__REQUEST[fecha_corte]}', INTERVAL {$__REQUEST[ran_corte]} HOUR)
                       AND b.fec_repnov <= '{$__REQUEST[fecha_corte]}' ";
            
            
            $sql = "SELECT COUNT(*), 
                           LOWER(a.usr_creaci),
                           HOUR(a.fec_noveda), 
                           CONCAT( SUBSTRING( DATE_FORMAT(a.fec_noveda, '%r'), 1, 2), ':00', ' ', SUBSTRING( DATE_FORMAT(a.fec_noveda, '%r'), -2 )  ), 
                           DATE(a.fec_noveda)
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                   GROUP BY 2, 3, 5
                   ORDER BY 5, 3";
            
            
            
            
            $consul = new Consulta($sql, $this -> conexion);
            
            $MATRIZ = array();
            
            foreach ( $consul -> ret_matriz() as $row){
                $MATRIZ[ $row[4] ][ $row[3] ][ $row[1] ] = $row[0]; 
            }
            return $MATRIZ;
            
        }
                
        function getJqueryCalendar(){
            return '<script>
                        jQuery(function($) { 
                          $( "#fec_corteID" ).datepicker();
                          $( "#hor_corteID" ).timepicker({
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

                          $( "#slider" ).slider({
                            min: 1,
                            max: 48,
                            range: "min",
                            value: 12,
                            slide: function( event, ui ) {
                              $("#ran_corteID").val( ui.value );
                            }
                          });

                        });
                        

   
                     </script>';
        }
        
        
        function Style(){
		echo "	<style>
                            .cellHead
                            {
                                    padding:5px 10px;
                                    background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                                    background: -moz-linear-gradient(top, #009617, #00661b ); 
                                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
                                    color:#fff;
                                    text-align:center;
                            }

                            .cellHead2
                            {
                                    padding:5px 10px;
                                    background: -webkit-gradient(linear, left top, left bottom, from( #03ad39 ), to( #00660f )); 
                                    background: -moz-linear-gradient(top, #03ad39, #00660f ); 
                                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#03ad39', endColorstr='#00660f');
                                    color:#fff;
                                    text-align:center;
                            }

                            .row:hover > td{
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

        
        
        function getUsuarios( $__REQUEST = null  ){
            $sql =" SELECT LOWER(cod_usuari), CONCAT( UPPER(nom_usuari ), ' - ', LOWER(cod_usuari) ) AS nom_usuari
                      FROM ".BASE_DATOS.".tab_genera_usuari
                     WHERE ind_estado = 1 
                       AND ( cod_perfil IN(1,7,8,73,70,77,669) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%'  )
                       ".(empty($__REQUEST[cod_usuari]) ? null : " AND cod_usuari =  '".$__REQUEST[cod_usuari]."' ")."
                    ORDER BY 2";
            $consulta = new Consulta($sql, $this -> conexion);
            return $consulta -> ret_matriz(); 
        }
        
        
    }
    $service = new InfNovedadesUsuario($_SESSION['conexion']);


?> 

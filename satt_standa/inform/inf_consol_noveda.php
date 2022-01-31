<?php
    
    session_start();
    
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

    
    class InfConsolidadoNovedades{
        
        var $conexion;
        var $cNull = array( array(0, '---') ); 
        
        function __construct($conexion){
            $this -> conexion = $conexion;
            if(empty($_REQUEST[option]) && !isset($_REQUEST[option]))
                $_REQUEST[option] = 'showFilters';
            
            $this -> $_REQUEST[option]( $_REQUEST );
        }

        function expInformExcel(){
            
            $archivo = "Informe_Consolidado_Novedades_Cliente_".date( "Y_m_d" ).".xls";
            header('Content-Type: application/octetstream');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="'.$archivo.'"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo $_SESSION['LIST_TOTAL'];
            
        }
        
        function showFilters(){
            
            if( !$_POST['fec_inicial'] )
              $_POST['fec_inicial'] = date('Y-m-d');
            
            if( !$_POST['horaini'] )
              $_POST['horaini'] = '00:00';  
              
            if( !$_POST['fec_final'] )
              $_POST['fec_final'] = date('Y-m-d');
              
            if( !$_POST['horafin'] )
              $_POST['horafin'] = date('H:i');    
              
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_consol_noveda.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/regnov.js\"></script>\n";
            echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
            echo '
            <script>
            jQuery(function($) { 

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
            //SQL AUTOCOMPLETE
            $query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
                        FROM ".BASE_DATOS.".tab_tercer_tercer a,
                             ".BASE_DATOS.".tab_tercer_activi b
                       WHERE a.cod_tercer = b.cod_tercer AND
                             b.cod_activi = ".COD_FILTRO_EMPTRA."
                    ORDER BY 2";
            $consulta = new Consulta( $query, $this -> conexion );
            $transpor = $consulta -> ret_matriz();

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
            $( "#busq_transpID" ).autocomplete({
            source: tranportadoras,
            delay: 100
            }).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );
            $( "#busq_transpID" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
            });
            </script>';


            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe Consolidado de Novedades por Cliente", "formulario\" id=\"formularioID");

            $formulario -> nueva_tabla();
            $formulario -> texto ("Fecha Inicial","text","fec_inicial\" id=\"fec_inicialID\" readonly=\"readonly\" ",0,7,7,"",$_POST['fec_inicial'] );
            $formulario -> texto ("Hora Inicial","text","horaini\" id=\"horainiID\" readonly=\"readonly\" ",1,7,7,"",$_POST['horaini'] );
            $formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID\" readonly=\"readonly\" ",0,7,7,"",$_POST['fec_final'] );
            $formulario -> texto ("Hora Final","text","horafin\" id=\"horafinID\" readonly=\"readonly\" ",1,7,7,"",$_POST['horafin'] );
            $formulario -> texto ("Nit / Nombre","text","busq_transp\" id=\"busq_transpID",0,30,30,"","" );
            $formulario -> caja ("M/CIA ALTO COSTO","ind_alt\" id=\"ind_altID","1","1",1);
            $formulario -> caja ("M/CIA RIESGO MEDIO","ind_med\" id=\"ind_medID","1","1",0);
            $formulario -> caja ("M/CIA BAJO RIESGO","ind_baj\" id=\"ind_bajID","1","1",1);
            $formulario -> caja ("OTROS CASOS","ind_otr\" id=\"ind_otrID","1","1",1);

            $formulario -> nueva_tabla();
            $formulario -> botoni("Buscar","ValidaInform()",0);
            $formulario -> nueva_tabla();

            $formulario -> oculto("window","central",0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("option\" id=\"optionID",'getInforme',0);
            $formulario -> cerrar();
            
        }
        
        
        function getInforme(){

            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_consol_noveda.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
            echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
            
            /*echo "<pre>";
            print_r($_REQUEST);
            echo "</pre>";*/
            
            $area = 0;
            if(isset($_REQUEST[ind_alt]))
                $area += $_REQUEST[ind_alt];
            if(isset($_REQUEST[ind_med]))
                $area += $_REQUEST[ind_med];
            if(isset($_REQUEST[ind_baj]))
                $area += $_REQUEST[ind_baj];
            if(isset($_REQUEST[ind_otr]))
                $area += $_REQUEST[ind_otr];
                
            $colspan = $area == 0? 8 : $area*(2);
                
            if(empty($_REQUEST[horaini]))
                $_REQUEST[horaini] = '00:00:00';
            
            if(empty($_REQUEST[horafin]))
                $_REQUEST[horafin] = '23:59:00';
            

            $_REQUEST[fecha_ini] = $_REQUEST[fec_inicial].' '.$_REQUEST[horaini];
            $_REQUEST[fecha_fin] = $_REQUEST[fec_final].' '.$_REQUEST[horafin];
            
            $_TRANSPORTADORA = $this -> getTranspor( $_REQUEST );
                        
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME CONSOLIDADO DE NOVEDADES POR CLIENTE", "formulario\" id=\"formularioID");
            $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_consol_noveda.php",0);
            $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
            $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
            $formulario -> oculto("num_serie",0,0);
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("opcion",2,0);
            $formulario -> nueva_tabla();
            $formulario -> linea("Cantidad de Novedades por Transportadora de ".$_REQUEST[fecha_ini]." al ".$_REQUEST[fecha_fin]  ,1,"t2"); 
            $formulario -> linea("Se Encontraron ".count( $_TRANSPORTADORA )." Registros"  ,1,"t2"); 
            
            $mHtml  = "<table width='100%'>";

            $mHtml .= "<tr>";
                $mHtml .= "<th class=cellHead rowspan='3' >CLIENTE EMPRESA DE TRANSPORTES</th>";
                $mHtml .= "<th class=cellHead colspan='".$colspan."'>ACUERDO DE SERVICIO PARA REALIZAR REGISTRO DE NOVEDADES</th>";
                $mHtml .= "<th class=cellHead colspan='2'rowspan='2' >CARGA DE TRABAJO X TURNO</th>";
            $mHtml .= "</tr>";
            
            $mHtml .= "<tr>";
                if(isset($_REQUEST[ind_alt]) || $area == 0)
                    $mHtml .= "<th class=cellHead colspan='2' >M/CIA ALTO COSTO</th>";
                if(isset($_REQUEST[ind_med]) || $area == 0)
                    $mHtml .= "<th class=cellHead colspan='2' >M/CIA RIESGO MEDIO</th>";
                if(isset($_REQUEST[ind_baj]) || $area == 0)
                    $mHtml .= "<th class=cellHead colspan='2' >M/CIA BAJO RIESGO</th>";
                if(isset($_REQUEST[ind_otr]) || $area == 0)
                    $mHtml .= "<th class=cellHead colspan='2' >OTROS CASOS</th>";
            $mHtml .= "</tr>";
            
            $mHtml .= "<tr>";
                if(isset($_REQUEST[ind_alt]) || $area == 0)
                {
                    $mHtml .= "<th class=cellHead >Q. VH.</th>";
                    $mHtml .= "<th class=cellHead >REP. C/60'</th>";
                }
                if(isset($_REQUEST[ind_med]) || $area == 0)
                {
                    $mHtml .= "<th class=cellHead >Q. VH.</th>";
                    $mHtml .= "<th class=cellHead >REP. C/90'</th>";
                }
                if(isset($_REQUEST[ind_baj]) || $area == 0)
                {
                    $mHtml .= "<th class=cellHead >Q. VH.</th>";
                    $mHtml .= "<th class=cellHead >REP. C/120'</th>";
                }
                if(isset($_REQUEST[ind_otr]) || $area == 0)
                {
                    $mHtml .= "<th class=cellHead >Q. VH.</th>";
                    $mHtml .= "<th class=cellHead >REP. C/180'</th>";
                }
                $mHtml .= "<th class=cellHead >8 HORAS</th>";
                $mHtml .= "<th class=cellHead >TOTAL REGISTROS</th>";
            $mHtml .= "</tr>";

            $_TOTAL_ = array();            
            
            $_ALT_ = 0;
            $_MED_ = 0;
            $_BAJ_ = 0;
            $_OTR_ = 0;
            
            $_REP_ALT_ = 0;
            $_REP_MED_ = 0;
            $_REP_BAJ_ = 0;
            $_REP_OTR_ = 0;
            
            foreach( $_TRANSPORTADORA as $row)
            {
                $mHtml .= "<tr class='row'>";
                    $mHtml .= "<td class='cellInfo' align='left' ><b>".$row['cod_tercer']."</b> - ".$row['abr_tercer']."</td>";
                    if(isset($_REQUEST[ind_alt]) || $area == 0)
                    {
                        $_ALT_ = (int)$this -> getIdicador($_REQUEST, $row['cod_tercer'], 0, 60 );
                        $_REP_ALT_ = number_format((480/60), 2);
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_ALT_) > 0 ? "  onclick=\"infoNoveda('0','60',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_ALT_)."</td>";
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' >".$_REP_ALT_."</td>";
                    }
                    if(isset($_REQUEST[ind_med]) || $area == 0)
                    {
                        $_MED_ = (int)$this -> getIdicador($_REQUEST, $row['cod_tercer'], 61, 90 );
                        $_REP_MED_ = number_format((480/90), 2);
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_MED_) > 0 ? "  onclick=\"infoNoveda('61','90',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_MED_)."</td>";
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' >".$_REP_MED_."</td>";
                    }
                    if(isset($_REQUEST[ind_baj]) || $area == 0)
                    {
                        $_BAJ_ = (int)$this -> getIdicador($_REQUEST, $row['cod_tercer'], 91, 120 );
                        $_REP_BAJ_ = number_format((480/120), 2);
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_BAJ_) > 0 ? "  onclick=\"infoNoveda('91','120',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_BAJ_)."</td>";
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' >".$_REP_BAJ_."</td>";
                    }
                    if(isset($_REQUEST[ind_otr]) || $area == 0)
                    {
                        $_OTR_ = (int)$this -> getIdicador($_REQUEST, $row['cod_tercer'], 121, 180 );
                        $_REP_OTR_ = number_format((480/180), 2);
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_OTR_) > 0 ? "  onclick=\"infoNoveda('121','180',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_OTR_)."</td>";
                        $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' >".$_REP_OTR_."</td>";
                    }
                    
                $_TOT_ = (($_ALT_)+($_MED_)+($_BAJ_)+($_OTR_));
                $_REP_TOT_ = (($_ALT_*$_REP_ALT_)+($_MED_*$_REP_MED_)+($_BAJ_*$_REP_BAJ_)+($_OTR_*$_REP_OTR_));

                $_TOTAL_[0]+= (int)$_ALT_;
                $_TOTAL_[1]+= (int)$_MED_;
                $_TOTAL_[2]+= (int)$_BAJ_;
                $_TOTAL_[3]+= (int)$_OTR_;
                $_TOTAL_[4]+= (int)$_REP_TOT_;
                $_TOTAL_[5]+= (int)$_TOT_;

                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' >".number_format($_REP_TOT_, 2)."</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TOT_) > 0 ? "  onclick=\"infoNoveda('0','180',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TOT_)."</td>";
                $mHtml .= "</tr>";
            } 
            
            if( count( $_TRANSPORTADORA ) > 1 )
            {
                $mHtml .= "<tr>";
                $mHtml .= "<th class='cellHead' style='text-align: right;' >TOTAL:</th>";
                //$mHtml .= "<th class='cellHead' style='text-align: right;' >".$_TOTAL_[0]."</th>";
                $mHtml .= "<td class='cellHead' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TOTAL_[0]) > 0 ? "  onclick=\"infoNoveda('0','60',  '','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TOTAL_[0])."</td>";
                $mHtml .= "<th class='cellHead' style='text-align: right;' > </th>";
                //$mHtml .= "<th class='cellHead' style='text-align: right;' >".$_TOTAL_[1]."</th>";
                $mHtml .= "<td class='cellHead' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TOTAL_[1]) > 0 ? "  onclick=\"infoNoveda('61','90',  '','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TOTAL_[1])."</td>";
                $mHtml .= "<th class='cellHead' style='text-align: right;' > </th>";
                //$mHtml .= "<th class='cellHead' style='text-align: right;' >".$_TOTAL_[2]."</th>";
                $mHtml .= "<td class='cellHead' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TOTAL_[2]) > 0 ? "  onclick=\"infoNoveda('91','120',  '','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TOTAL_[2])."</td>";
                $mHtml .= "<th class='cellHead' style='text-align: right;' > </th>";
                //$mHtml .= "<th class='cellHead' style='text-align: right;' >".$_TOTAL_[3]."</th>";
                $mHtml .= "<td class='cellHead' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TOTAL_[3]) > 0 ? "  onclick=\"infoNoveda('121','180',  '','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TOTAL_[3])."</td>";
                $mHtml .= "<th class='cellHead' style='text-align: right;' > </th>";
                $mHtml .= "<th class='cellHead' style='text-align: right;' >".number_format($_TOTAL_[4], 2)."</th>";
                //$mHtml .= "<th class='cellHead' style='text-align: right;' >".$_TOTAL_[5]."</th>";
                $mHtml .= "<td class='cellHead' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TOTAL_[5]) > 0 ? "  onclick=\"infoNoveda('0','180',  '','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TOTAL_[5])."</td>";
            
                /*
                foreach ($_TOTAL_ as $row){
                    $mHtml .= "<th class='cellHead' style='text-align: right;' >{$row}</th>";
                    $mHtml .= "<th class='cellHead' style='text-align: right;' > </th>";
                }*/
                $mHtml .= "</tr>";
            }
            $mHtml .= "</table>";
            
            echo $mHtml;
            
            $_SESSION['LIST_TOTAL'] = $mHtml;
    
            $formulario -> nueva_tabla();
            $formulario -> botoni("Regresar","history.back()",0);
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
         
            /*echo "<pre>";
            print_r($__REQUEST);
            echo "</pre>";*/
         
            global $HTTP_POST_FILES;
            session_start();
            $BASE = $_SESSION[BASE_DATOS];
            define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
            define ('ESTILO', $_SESSION['ESTILO']);
            define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
            
            include( "../lib/general/conexion_lib.inc" );
            include( "../lib/general/form_lib.inc" );
            include( "../lib/general/tabla_lib.inc" );
            include( "../lib/general/constantes.inc" );

            $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );
            
            if($__REQUEST[cod_transp])
            {
                $_USUARIO_ = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
                $aux= " c.cod_transp = '".$__REQUEST[cod_transp]."' ";
            }
            else
            {
                $_USUARIO_ = array(0 => array(1 => 'Todos'));
                $aux = '1=1';
            }

            $array = array();
            
            $sql0 ="SELECT c.num_despac
                     FROM ".BASE_DATOS.".tab_despac_despac a,
                          ".BASE_DATOS.".tab_despac_vehige c
                    WHERE a.num_despac = c.num_despac
                      AND $aux
                      AND a.tie_contra >= '".$__REQUEST[ini]."'
                      AND a.tie_contra <= '".$__REQUEST[fin]."'
                      AND a.ind_tiemod = '1'
                UNION ALL
                   SELECT c.num_despac
                     FROM ".BASE_DATOS.".tab_despac_despac a,
                          ".BASE_DATOS.".tab_despac_vehige c,
                          ".BASE_DATOS.".tab_transp_tipser b
                    WHERE a.num_despac = c.num_despac
                      AND $aux
                      AND a.cod_tipdes = 1
                      AND a.ind_tiemod = '0'
                      AND c.cod_transp = b.cod_transp
                      AND b.num_consec = (SELECT MAX(x.num_consec) 
                                            FROM  tab_transp_tipser x
                                            WHERE x.cod_transp =  c.cod_transp)
                      AND b.tie_conurb >= '".$__REQUEST[ini]."'
                      AND b.tie_conurb <= '".$__REQUEST[fin]."'
                UNION ALL
                    SELECT c.num_despac
                    FROM ".BASE_DATOS.".tab_despac_despac a,
                         ".BASE_DATOS.".tab_despac_vehige c,
                         ".BASE_DATOS.".tab_transp_tipser b
                    WHERE a.num_despac = c.num_despac
                    AND $aux
                    AND a.cod_tipdes != 1
                    AND a.ind_tiemod = '0'
                    AND c.cod_transp = b.cod_transp
                    AND b.num_consec = (SELECT MAX(x.num_consec) 
                                            FROM  tab_transp_tipser x
                                            WHERE x.cod_transp =  c.cod_transp)
                    AND b.tie_contro >= '".$__REQUEST[ini]."'
                    AND b.tie_contro <= '".$__REQUEST[fin]."'
                      ";

            $sql ="(SELECT b.num_despac as Despacho, a.nom_noveda as Novedad,b.fec_contro as Fecha,
                           b.obs_contro as Observacion, d.abr_tercer as Transportadora, c.num_placax as Placa,
                           IF( c.cod_conduc = '1001', UPPER(c.nom_conduc), UPPER(z.abr_tercer) ) as Conductor
                      FROM ".BASE_DATOS.".tab_genera_noveda a,
                           ".BASE_DATOS.".tab_despac_contro b,
                           ".BASE_DATOS.".tab_despac_vehige c,
                           ".BASE_DATOS.".tab_tercer_tercer d,
                           ".BASE_DATOS.".tab_tercer_tercer z
                     WHERE a.cod_noveda = b.cod_noveda
                       AND c.num_despac = b.num_despac
                       AND c.cod_conduc = z.cod_tercer
                       AND c.cod_transp = d.cod_tercer
                       AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                       AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                       AND b.cod_noveda != 4999
                       AND $aux
                       AND c.num_despac IN (".$sql0."))  
                   UNION ALL
                  (SELECT b.num_despac, a.nom_noveda, b.fec_noveda ,
                          b.des_noveda, d.abr_tercer,c.num_placax,
                           IF( c.cod_conduc = '1001', UPPER(c.nom_conduc), UPPER(z.abr_tercer) ) as Conductor
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b,
                          ".BASE_DATOS.".tab_despac_vehige c,
                          ".BASE_DATOS.".tab_tercer_tercer d,
                          ".BASE_DATOS.".tab_tercer_tercer z
                    WHERE a.cod_noveda = b.cod_noveda
                      AND c.num_despac = b.num_despac
                      AND c.cod_transp = d.cod_tercer
                      AND c.cod_conduc = z.cod_tercer
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                      AND $aux
                      AND c.num_despac IN (".$sql0.") )
                    ";

                  $sql .= " ORDER BY 5,1,3 ";

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
            
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
            
            $mHtml  = "<table width='100%'>";
            $mHtml .= "<tr>";
                $mHtml .= "<th class=cellHead >Transportadora</th>";
                $mHtml .= "<th class=cellHead >Despacho</th>";
                $mHtml .= "<th class=cellHead >Placa</th>";
                $mHtml .= "<th class=cellHead >Conductor</th>";
                $mHtml .= "<th class=cellHead >Novedad</th>";
                $mHtml .= "<th class=cellHead >Fecha</th>";
                $mHtml .= "<th class=cellHead >Observacion</th>";
            $mHtml .= "</tr>";
            
            foreach($despachos AS $row){
                 $mHtml .= "<tr>";
                    $mHtml .= "<td class='cellInfo' >{$row[4]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[0]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[5]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[6]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[1]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[2]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[3]}</td>";
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
       
        
        function getTranspor( $__REQUEST = null ){

          /*$query ="SELECT a.cod_tercer, TRIM( UPPER( a.abr_tercer ) ) as abr_tercer
                     FROM ".BASE_DATOS.".tab_tercer_tercer a,
                          ".BASE_DATOS.".tab_tercer_activi b,
                          ".BASE_DATOS.".tab_despac_vehige c,
                          ".BASE_DATOS.".tab_despac_despac d
                    WHERE a.cod_tercer = b.cod_tercer AND
                          b.cod_activi = ".COD_FILTRO_EMPTRA."
                      AND c.cod_transp = a.cod_tercer
                      AND c.num_despac = d.num_despac
                      AND d.fec_salida >= '".$__REQUEST[fecha_ini]."'
                      AND d.fec_salida <= '".$__REQUEST[fecha_fin]."'
                          ";*/
          if($__REQUEST['busq_transp'])
          {
              $query ="SELECT a.cod_tercer, TRIM( UPPER( a.abr_tercer ) ) as abr_tercer
                   FROM ".BASE_DATOS.".tab_tercer_tercer a,
                        ".BASE_DATOS.".tab_tercer_activi b
                  WHERE a.cod_tercer = b.cod_tercer AND
                        b.cod_activi = ".COD_FILTRO_EMPTRA."";
                    
            $v = explode(" - ", $__REQUEST['busq_transp']);
            $query .= " AND a.cod_tercer ='".$v[0]."'";   
          }
          else
          {
            $sql ="SELECT c.cod_transp
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b,
                          ".BASE_DATOS.".tab_despac_vehige c
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                      AND b.cod_noveda != 4999
                      AND c.num_despac = b.num_despac
                   UNION ALL
                   SELECT c.cod_transp
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b,
                          ".BASE_DATOS.".tab_despac_vehige c
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                      AND c.num_despac = b.num_despac
                    "; 
                          
            $query ="SELECT b.cod_tercer, TRIM( UPPER( b.abr_tercer ) ) as abr_tercer
                       FROM ( ".$sql." ) AS a, 
                            ".BASE_DATOS.".tab_tercer_tercer b
                      WHERE a.cod_transp = b.cod_tercer";
          }
          
 
          
          $query .= " GROUP BY 1 ORDER BY 2"; 
          
          $consulta = new Consulta($query, $this -> conexion);
          return $consulta -> ret_matriz();

        }
        
        function getIdicador( $__REQUEST, $tranpor, $ini, $fin )
        {
            $sql0 ="SELECT c.num_despac
                     FROM ".BASE_DATOS.".tab_despac_despac a,
                          ".BASE_DATOS.".tab_despac_vehige c
                    WHERE a.num_despac = c.num_despac
                      AND c.cod_transp = '".$tranpor."'
                      AND a.tie_contra >= '".$ini."'
                      AND a.tie_contra <= '".$fin."'
                      AND a.ind_tiemod = '1'
                UNION ALL
                   SELECT c.num_despac
                     FROM ".BASE_DATOS.".tab_despac_despac a,
                          ".BASE_DATOS.".tab_despac_vehige c,
                          ".BASE_DATOS.".tab_transp_tipser b
                    WHERE a.num_despac = c.num_despac
                      AND c.cod_transp = '".$tranpor."'
                      AND a.cod_tipdes = 1
                      AND a.ind_tiemod = '0'
                      AND c.cod_transp = b.cod_transp
                      AND b.num_consec = (SELECT MAX(x.num_consec) 
                                            FROM  tab_transp_tipser x
                                            WHERE x.cod_transp =  '".$tranpor."')
                      AND b.tie_conurb >= '".$ini."'
                      AND b.tie_conurb <= '".$fin."'
                UNION ALL
                    SELECT c.num_despac
                    FROM ".BASE_DATOS.".tab_despac_despac a,
                         ".BASE_DATOS.".tab_despac_vehige c,
                         ".BASE_DATOS.".tab_transp_tipser b
                    WHERE a.num_despac = c.num_despac
                    AND c.cod_transp = '".$tranpor."'
                    AND a.cod_tipdes != 1
                    AND a.ind_tiemod = '0'
                    AND c.cod_transp = b.cod_transp
                    AND b.num_consec = (SELECT MAX(x.num_consec) 
                                            FROM  tab_transp_tipser x
                                            WHERE x.cod_transp =  '".$tranpor."')
                    AND b.tie_contro >= '".$ini."'
                    AND b.tie_contro <= '".$fin."'
                      ";
                      
            $sql ="SELECT c.cod_transp, c.num_despac, a.cod_noveda, 'tab_despac_noveda', b.fec_contro
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b,
                          ".BASE_DATOS.".tab_despac_vehige c
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                      AND b.cod_noveda != 4999
                      AND c.num_despac = b.num_despac
                      AND c.cod_transp = '".$tranpor."'
                      AND c.num_despac IN (".$sql0.")
                   UNION ALL
                   SELECT c.cod_transp, c.num_despac, a.cod_noveda, 'tab_despac_noveda', b.fec_noveda
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b,
                          ".BASE_DATOS.".tab_despac_vehige c
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                      AND c.num_despac = b.num_despac
                      AND c.cod_transp = '".$tranpor."'
                      AND c.num_despac IN (".$sql0.")
                 ORDER BY  num_despac ASC 
                    ";                      

            $consul = new Consulta($sql, $this -> conexion);
            $resul = $consul -> ret_matriz();
            
            //IF($tranpor == '805027046')
            //ECHO "<br>".$sql."<br>";
            
            return sizeof($resul);
        }
        
 
    }
    $service = new InfConsolidadoNovedades($_SESSION['conexion']);


?> 

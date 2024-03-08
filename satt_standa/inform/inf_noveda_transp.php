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
        $archivo = "Informe_Novedades_Transportadora_".date( "Y_m_d" ).".xls";
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
          
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda_transp.js\"></script>\n";
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

        include_once("class_despac_trans3.php");
        $cTransp = new Despac($this -> conexion);
        $transp = $cTransp->getTransp();
        $cod_transp = "";
        $readonly = "";
        if(count($transp)>1)
        {
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
        }
        else
        {
          $cod_transp = $transp[0][0]." - ".$transp[0][1];
          $readonly = " readonly=\"readonly";
        }
        


        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe de Novedades por Transportadora", "formulario\" id=\"formularioID");

        $formulario -> nueva_tabla();
        $formulario -> texto ("Fecha Inicial","text","fec_inicial\" id=\"fec_inicialID\" readonly=\"readonly\" ",0,7,7,"",$_POST['fec_inicial'] );
        $formulario -> texto ("Hora Inicial","text","horaini\" id=\"horainiID\" readonly=\"readonly\" ",1,7,7,"",$_POST['horaini'] );
        $formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID\" readonly=\"readonly\" ",0,7,7,"",$_POST['fec_final'] );
        $formulario -> texto ("Hora Final","text","horafin\" id=\"horafinID\" readonly=\"readonly\" ",1,7,7,"",$_POST['horafin'] );
        $formulario -> texto ("Nit / Nombre","text","busq_transp\" id=\"busq_transpID\" ".$readonly,1,30,30,"",$cod_transp );

        $formulario -> nueva_tabla();
        $formulario -> botoni("Buscar","ValidaInform()",0);
        $formulario -> nueva_tabla();

        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
        $formulario -> oculto("option\" id=\"optionID",'getInforme',0);
        $formulario -> cerrar();
        
    }
    
    
    function getInforme(){
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda_transp.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
        
        if(empty($_REQUEST[horaini]))
            $_REQUEST[horaini] = '00:00:00';
        
        if(empty($_REQUEST[horafin]))
            $_REQUEST[horafin] = '23:59:00';
        
        
        $_TRANSPORTADORA = $this -> getTranspor( $_REQUEST );

        $_REQUEST[fecha_ini] = $_REQUEST[fec_inicial].' '.$_REQUEST[horaini];
        $_REQUEST[fecha_fin] = $_REQUEST[fec_final].' '.$_REQUEST[horafin];
        
                    
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES POR TRANSPORTADORA", "formulario\" id=\"formularioID");
        $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_noveda_transp.php",0);
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
            $mHtml .= "<th class=cellHead >TRANSPORTADORA</th>";
            $mHtml .= "<th class=cellHead >Novedad Especial</th>";
            $mHtml .= "<th class=cellHead >Novedad Especial MA</th>";
            $mHtml .= "<th class=cellHead >Solicita Tiempo</th>";
            $mHtml .= "<th class=cellHead >Mantiene Alarma</th>";
            $mHtml .= "<th class=cellHead >Otros</th>";
            $mHtml .= "<th class=cellHead >TOTAL</th>";
        $mHtml .= "</tr>";
        
        $_NOVESP = $this -> getNovedadesEspeciales( $_REQUEST );
        $_NOVESPMA = $this -> getNovedadesEspecialesMa( $_REQUEST );
        $_SOLTIE = $this -> getSolicitaTiempo( $_REQUEST ); 
        $_MANALA = $this -> getMantieneAlarma( $_REQUEST ); 
        $_OTROXX = $this -> getOtros( $_REQUEST ); 
        $_TODOSX = $this -> getTodos( $_REQUEST );
        $_TOTAL_ = array();            
        
        foreach( $_TRANSPORTADORA as $row)
        {
        
        if((int)$_TODOSX[ $row['cod_tercer'] ] == 0) continue;
            
            $_TOTAL_[0]+= (int)$_NOVESP[ $row['cod_tercer'] ];
            $_TOTAL_[1]+= (int)$_NOVESPMA[ $row['cod_tercer'] ];
            $_TOTAL_[2]+= (int)$_SOLTIE[ $row['cod_tercer'] ];
            $_TOTAL_[3]+= (int)$_MANALA[ $row['cod_tercer'] ];
            $_TOTAL_[4]+= (int)$_OTROXX[ $row['cod_tercer'] ];
            $_TOTAL_[5]+= (int)$_TODOSX[ $row['cod_tercer'] ];                
            
          $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' align='left' ><b>".$row['cod_tercer']."</b> - ".$row['abr_tercer']."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_NOVESP[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('NE',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_NOVESP[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_NOVESPMA[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('NEMA',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_NOVESPMA[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_SOLTIE[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('ST',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_SOLTIE[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_MANALA[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('MA',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_MANALA[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_OTROXX[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('OT',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_OTROXX[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TODOSX[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('All',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TODOSX[ $row['cod_tercer'] ])."</td>";
          $mHtml .= "</tr>";
        } 
        
        if( count( $_TRANSPORTADORA ) > 1 )
        {
          $mHtml .= "<tr>";
        $mHtml .= "<th class='cellHead' style='text-align: right;' >TOTAL:</th>";
        foreach ($_TOTAL_ as $row){
            $mHtml .= "<th class='cellHead' style='text-align: right;' >{$row}</th>";
        }
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
          <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 1px solid #333333; background: white; ">

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
        include( "../lib/general/constantes.inc" );

        $this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION['USUARIO'], $_SESSION['CLAVE'], $BASE  );
        $_USUARIO_ = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );

        $array = array();
        
        if($__REQUEST['tipo']=='OT')
            $aux="     a.nov_especi !='1'
                   AND a.ind_tiempo !='1'
                   AND a.ind_manala !='1'
                   AND a.ind_alarma !='S' ";
        if($__REQUEST['tipo']=='NE')
            $aux= "a.nov_especi ='1'";
        if($__REQUEST['tipo']=='NEMA')
            $aux= "g.ind_novesp = '1'
               AND a.cod_tipoxx = '1'";
        if($__REQUEST['tipo']=='ST')
            $aux= "a.ind_tiempo ='1'";
        if($__REQUEST['tipo']=='MA')
            $aux= "a.ind_manala ='1'";               
        if($__REQUEST['tipo']=='All')
            $aux = '1=1';
        
        $sql ="(SELECT b.num_despac as Despacho, a.nom_noveda as Novedad,b.fec_contro as Fecha,
                       b.obs_contro as Observacion, d.abr_tercer as Transportadora, c.num_placax as Placa,
                       IF( c.cod_conduc = '1001', UPPER(c.nom_conduc), UPPER(z.abr_tercer) ) as Conductor, f.nom_agenci AS agencia, 
                       b.usr_creaci AS usr_noveda, e.usr_creaci, e.fec_creaci
                  FROM ".BASE_DATOS.".tab_genera_noveda a,
                       ".BASE_DATOS.".tab_despac_contro b,
                       ".BASE_DATOS.".tab_despac_vehige c,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_tercer_tercer z,
                       ".BASE_DATOS.".tab_despac_despac e,
                       ".BASE_DATOS.".tab_genera_agenci f,
                       ".BASE_DATOS.".tab_parame_novseg g 
                 WHERE a.cod_noveda = b.cod_noveda
                   AND g.cod_transp = '".$__REQUEST[cod_transp]."'
                   AND g.cod_noveda = b.cod_noveda
                   AND $aux
                   AND c.num_despac = b.num_despac
                   AND c.cod_conduc = z.cod_tercer
                   AND c.cod_transp = d.cod_tercer
                   AND e.cod_agedes = f.cod_agenci
                   AND e.num_despac = c.num_despac
                   AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                   AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                   AND c.cod_transp = '".$__REQUEST[cod_transp]."'
                   AND a.cod_noveda != 4999 )  
               UNION ALL
              (SELECT b.num_despac, a.nom_noveda, b.fec_noveda ,
                      b.des_noveda, d.abr_tercer,c.num_placax,
                       IF( c.cod_conduc = '1001', UPPER(c.nom_conduc), UPPER(z.abr_tercer) ) as Conductor, f.nom_agenci AS agencia, 
                       b.usr_creaci AS usr_noveda, e.usr_creaci, e.fec_creaci
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c,
                      ".BASE_DATOS.".tab_tercer_tercer d,
                      ".BASE_DATOS.".tab_tercer_tercer z,
                      ".BASE_DATOS.".tab_despac_despac e,
                      ".BASE_DATOS.".tab_genera_agenci f,
                      ".BASE_DATOS.".tab_parame_novseg g 
                WHERE a.cod_noveda = b.cod_noveda
                AND g.cod_transp = '".$__REQUEST[cod_transp]."'
                AND g.cod_noveda = b.cod_noveda
                AND $aux
                  AND c.num_despac = b.num_despac
                  AND c.cod_transp = d.cod_tercer
                  AND c.cod_conduc = z.cod_tercer
                  AND e.cod_agedes = f.cod_agenci
                  AND e.num_despac = c.num_despac
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                  AND c.cod_transp = '".$__REQUEST[cod_transp]."')
                UNION ALL
                (SELECT b.num_repnov, a.nom_noveda, b.fec_repnov ,
                        b.obs_repnov, d.abr_tercer, b.num_placax,
                        ' - ' as Conductor, '' AS agencia, '' AS usr_noveda, '' AS usr_creaci, '' AS fec_creaci
                        FROM ".BASE_DATOS.".tab_genera_noveda a, 
                        ".BASE_DATOS.".tab_report_noveda b,
                        ".BASE_DATOS.".tab_parame_novseg c,
                        ".BASE_DATOS.".tab_tercer_tercer d
                  WHERE a.cod_noveda = b.cod_noveda
                    AND c.ind_novesp = '1'
                    AND a.cod_tipoxx = '1'
                    AND b.cod_tercer = d.cod_tercer
                    AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                    AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."'
                    AND b.cod_tercer = '".$__REQUEST[cod_transp]."')";
        if($__REQUEST['tipo']=='OT')  
        { 
        $aux= "a.ind_alarma ='S'";
          $sql .= "UNION ALL
                  (SELECT b.num_despac as Despacho, a.nom_noveda as Novedad,b.fec_contro as Fecha,
                       b.obs_contro as Observacion, d.abr_tercer as Transportadora, c.num_placax as Placa,
                       IF( c.cod_conduc = '1001', UPPER(c.nom_conduc), UPPER(z.abr_tercer) ) as Conductor
                  FROM ".BASE_DATOS.".tab_genera_noveda a,
                       ".BASE_DATOS.".tab_despac_contro b,
                       ".BASE_DATOS.".tab_despac_vehige c,
                       ".BASE_DATOS.".tab_tercer_tercer d,
                       ".BASE_DATOS.".tab_parame_novseg e,
                       ".BASE_DATOS.".tab_tercer_tercer z
                 WHERE a.cod_noveda = b.cod_noveda
                   AND e.cod_transp = '".$__REQUEST[cod_transp]."'
                   AND e.cod_noveda = b.cod_noveda
                   AND e.ind_novesp = '1'
                   AND a.cod_tipoxx = '1'
                   AND c.num_despac = b.num_despac
                   AND c.cod_conduc = z.cod_tercer
                   AND c.cod_transp = d.cod_tercer
                   AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                   AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                   AND c.cod_transp = '".$__REQUEST[cod_transp]."'
                   AND a.cod_noveda != 4999 )  
               UNION ALL
              (SELECT b.num_despac, a.nom_noveda, b.fec_noveda ,
                      b.des_noveda, d.abr_tercer,c.num_placax,
                       IF( c.cod_conduc = '1001', UPPER(c.nom_conduc), UPPER(z.abr_tercer) ) as Conductor
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c,
                      ".BASE_DATOS.".tab_tercer_tercer d,
                      ".BASE_DATOS.".tab_parame_novseg e,
                      ".BASE_DATOS.".tab_tercer_tercer z
                WHERE a.cod_noveda = b.cod_noveda
                  AND e.cod_transp = '".$__REQUEST[cod_transp]."'
                  AND e.cod_noveda = b.cod_noveda
                  AND e.ind_novesp = '1'
                  AND a.cod_tipoxx = '1'
                  AND c.num_despac = b.num_despac
                  AND c.cod_transp = d.cod_tercer
                  AND c.cod_conduc = z.cod_tercer
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                  AND c.cod_transp = '".$__REQUEST[cod_transp]."')
                UNION ALL
                (SELECT b.num_repnov, a.nom_noveda, b.fec_repnov ,
                        b.obs_repnov, d.abr_tercer, b.num_placax,
                        ' - ' as Conductor
                        FROM ".BASE_DATOS.".tab_genera_noveda a, 
                             ".BASE_DATOS.".tab_report_noveda b,
                             ".BASE_DATOS.".tab_parame_novseg c,
                             ".BASE_DATOS.".tab_tercer_tercer d
                  WHERE a.cod_noveda = b.cod_noveda
                    AND c.cod_transp = '".$__REQUEST[cod_transp]."'
                    AND c.cod_noveda = b.cod_noveda
                    AND c.ind_novesp = '1'
                    AND a.cod_tipoxx = '1'
                    AND b.cod_tercer = d.cod_tercer
                    AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                    AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."'
                    AND b.cod_tercer = '".$__REQUEST[cod_transp]."')";
        }
        $sql .= " ORDER BY 5,1,3 ";
        // echo '<pre>'; print_r($sql); echo '</pre>';
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
            $mHtml .= "<th class=cellHead >Agencia</th>";
            $mHtml .= "<th class=cellHead >Despacho</th>";
            $mHtml .= "<th class=cellHead >Placa</th>";
            $mHtml .= "<th class=cellHead >Conductor</th>";
            $mHtml .= "<th class=cellHead >Novedad</th>";
            $mHtml .= "<th class=cellHead >Fecha</th>";
            $mHtml .= "<th class=cellHead >Observacion</th>";
            $mHtml .= "<th class=cellHead >Usuario Novedad</th>";
            $mHtml .= "<th class=cellHead >Usuario creacion</th>";
            $mHtml .= "<th class=cellHead >Fecha creacion</th>";
        $mHtml .= "</tr>";
        
        foreach($despachos AS $row){
             $mHtml .= "<tr>";
                $mHtml .= "<td class='cellInfo' >{$row[4]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[7]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[0]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[5]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[6]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[1]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[2]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[3]}&nbsp</td>";
                $mHtml .= "<td class='cellInfo' >{$row[8]}&nbsp</td>";
                $mHtml .= "<td class='cellInfo' >{$row[9]}&nbsp</td>";
                $mHtml .= "<td class='cellInfo' >{$row[10]}&nbsp</td>";
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
      $query ="SELECT a.cod_tercer, TRIM( UPPER( a.abr_tercer ) ) as abr_tercer
           FROM ".BASE_DATOS.".tab_tercer_tercer a,
                ".BASE_DATOS.".tab_tercer_activi b
          WHERE a.cod_tercer = b.cod_tercer AND
                b.cod_activi = ".COD_FILTRO_EMPTRA."";
                
      if($__REQUEST['busq_transp'])
      {
        $v = split(" - ", $__REQUEST['busq_transp']);
        $query .= " AND a.cod_tercer ='".$v[0]."'";   
      }
      $query .= " ORDER BY 2";
      
      $consulta = new Consulta($query, $this -> conexion);
      return $consulta -> ret_matriz();
    }
    
    function getNovedadesEspeciales( $__REQUEST ){
        $sql ="SELECT  c.cod_transp 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_contro b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.nov_especi ='1'
                  AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                  AND b.cod_noveda != 4999
               UNION ALL      
               SELECT c.cod_transp 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.nov_especi ='1'
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
               UNION ALL      
               SELECT b.cod_tercer AS cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_report_noveda b
                WHERE a.cod_noveda = b.cod_noveda
                  AND a.nov_especi ='1'
                  AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

        $sql = "SELECT COUNT(*), b.cod_tercer
                  FROM ( ".$sql." ) AS a, 
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.cod_transp = b.cod_tercer
                GROUP BY 2 ";  
        $consul = new Consulta($sql, $this -> conexion);
        
        $MATRIZ = array(); 
        foreach ( $consul -> ret_matriz() as $row){
            $MATRIZ[ $row[1] ] = $row[0];
        }
        return $MATRIZ; 
    }

    function getNovedadesEspecialesMa( $__REQUEST ){
      $sql ="SELECT  c.cod_transp 
               FROM ".BASE_DATOS.".tab_genera_noveda a, 
                    ".BASE_DATOS.".tab_despac_contro b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_parame_novseg d
              WHERE a.cod_noveda = b.cod_noveda
                AND d.cod_noveda = b.cod_noveda
                AND d.cod_transp = c.cod_transp
                AND d.ind_novesp ='1'
                AND a.cod_tipoxx ='1'
                AND c.num_despac = b.num_despac
                AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                AND b.cod_noveda != 4999
             UNION ALL      
             SELECT c.cod_transp 
               FROM ".BASE_DATOS.".tab_genera_noveda a, 
                    ".BASE_DATOS.".tab_despac_noveda b,
                    ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_parame_novseg d
              WHERE a.cod_noveda = b.cod_noveda
                AND d.cod_noveda = b.cod_noveda
                AND d.cod_transp = c.cod_transp
                AND d.ind_novesp ='1'
                AND a.cod_tipoxx ='1'
                AND c.num_despac = b.num_despac
                AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
             UNION ALL      
             SELECT b.cod_tercer AS cod_transp
               FROM ".BASE_DATOS.".tab_genera_noveda a, 
                    ".BASE_DATOS.".tab_report_noveda b,
                    ".BASE_DATOS.".tab_parame_novseg c
              WHERE a.cod_noveda = b.cod_noveda
                AND c.cod_noveda = b.cod_noveda
                AND c.cod_transp = cod_transp
                AND c.ind_novesp ='1'
                AND a.cod_tipoxx ='1'
                AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

        $sql = "SELECT COUNT(*), b.cod_tercer
                FROM ( ".$sql." ) AS a, 
                     ".BASE_DATOS.".tab_tercer_tercer b
               WHERE a.cod_transp = b.cod_tercer
              GROUP BY 2 ";  
      $consul = new Consulta($sql, $this -> conexion);
      
      $MATRIZ = array(); 
      foreach ( $consul -> ret_matriz() as $row){
          $MATRIZ[ $row[1] ] = $row[0];
      }
      return $MATRIZ; 
  }
    
    
    function getSolicitaTiempo( $__REQUEST ){
        $sql ="SELECT c.cod_transp 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_contro b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.ind_tiempo ='1'
                  AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                  AND b.cod_noveda != 4999
               UNION ALL
               SELECT c.cod_transp 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.ind_tiempo ='1'
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
               UNION ALL
               SELECT b.cod_tercer AS cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_report_noveda b
                WHERE a.cod_noveda = b.cod_noveda
                  AND a.ind_tiempo ='1'
                  AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

        $sql = "SELECT COUNT(*), b.cod_tercer
                  FROM ( ".$sql." ) AS a, 
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.cod_transp = b.cod_tercer
                GROUP BY 2 ";

        $consul = new Consulta($sql, $this -> conexion);
        
        $MATRIZ = array(); 
        foreach ( $consul -> ret_matriz() as $row){
            $MATRIZ[ $row[1] ] = $row[0];
        }
        return $MATRIZ;
    }
    
    function getMantieneAlarma( $__REQUEST ){
        $sql ="SELECT c.cod_transp 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_contro b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.ind_manala ='1'
                  AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                  AND b.cod_noveda != 4999
               UNION ALL  
               SELECT c.cod_transp 
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.ind_manala ='1'
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
               UNION ALL  
               SELECT b.cod_tercer AS cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_report_noveda b
                WHERE a.cod_noveda = b.cod_noveda
                  AND a.ind_manala ='1'
                  AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

        $sql = "SELECT COUNT(*), b.cod_tercer
                  FROM ( ".$sql." ) AS a, 
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.cod_transp = b.cod_tercer
                GROUP BY 2 ";

        
        $consul = new Consulta($sql, $this -> conexion);
        
        $MATRIZ = array(); 
        foreach ( $consul -> ret_matriz() as $row){
            $MATRIZ[ $row[1] ] = $row[0];
        }
        return $MATRIZ;
    }
    
    
    function getOtros( $__REQUEST ){
        $sql ="SELECT c.cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_contro b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.nov_especi !='1'
                  AND a.ind_tiempo !='1'
                  AND a.ind_manala !='1'
                  AND a.ind_alarma !='S'
                  AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                  AND b.cod_noveda != 4999
               UNION ALL
               SELECT c.cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.nov_especi !='1'
                  AND a.ind_tiempo !='1'
                  AND a.ind_manala !='1'
                  AND a.ind_alarma !='S'
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
               UNION ALL
               SELECT b.cod_tercer AS cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_report_noveda b
                WHERE a.cod_noveda = b.cod_noveda
                  AND a.nov_especi !='1'
                  AND a.ind_tiempo !='1'
                  AND a.ind_manala !='1'
                  AND a.ind_alarma !='S'
                  AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' 
               UNION ALL 
               SELECT c.cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_contro b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.ind_alarma ='S'
                  AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                  AND b.cod_noveda != 4999
               UNION ALL  
               SELECT c.cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND a.ind_alarma ='S'
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
               UNION ALL  
               SELECT b.cod_tercer AS cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_report_noveda b
                WHERE a.cod_noveda = b.cod_noveda
                  AND a.ind_alarma ='S'
                  AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";
      
       $sql = "SELECT COUNT(*), b.cod_tercer
                  FROM ( ".$sql." ) AS a, 
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.cod_transp = b.cod_tercer
                GROUP BY 2 "; 

      $consul = new Consulta($sql, $this -> conexion);
      
      $MATRIZ = array(); 
      foreach ( $consul -> ret_matriz() as $row){
          $MATRIZ[ $row[1] ] = $row[0];
      }
      return $MATRIZ;
    }
    
    function getTodos( $__REQUEST ){
        $sql ="SELECT c.cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_contro b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                  AND b.cod_noveda != 4999
               UNION ALL
               SELECT c.cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_despac_noveda b,
                      ".BASE_DATOS.".tab_despac_vehige c
                WHERE a.cod_noveda = b.cod_noveda
                  AND c.num_despac = b.num_despac
                  AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
               UNION ALL
               SELECT b.cod_tercer AS cod_transp
                 FROM ".BASE_DATOS.".tab_genera_noveda a, 
                      ".BASE_DATOS.".tab_report_noveda b
                WHERE a.cod_noveda = b.cod_noveda
                  AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                  AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";
      
       $sql = "SELECT COUNT(*), b.cod_tercer
                  FROM ( ".$sql." ) AS a, 
                       ".BASE_DATOS.".tab_tercer_tercer b
                 WHERE a.cod_transp = b.cod_tercer
                GROUP BY 2 ";

        $consul = new Consulta($sql, $this -> conexion);
      
        $MATRIZ = array(); 
        foreach ( $consul -> ret_matriz() as $row){
            $MATRIZ[ $row[1] ] = $row[0];
        }
        return $MATRIZ; 
    }
}
$service = new InfNovedadesUsuario($_SESSION['conexion']);

?>
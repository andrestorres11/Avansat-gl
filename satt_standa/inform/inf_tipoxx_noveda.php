<?php
    
    session_start();
    
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

    
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
            $formulario -> texto ("Fecha Inicial","text","fec_inicial\" id=\"fec_inicialID",0,7,7,"","" );
            $formulario -> texto ("Hora Inicial","text","horaini\" id=\"horainiID",1,7,7,"","" );
            $formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID",0,7,7,"","" );
            $formulario -> texto ("Hora Final","text","horafin\" id=\"horafinID",1,7,7,"","" );
            $formulario -> lista ("Usuario","cod_usuari\" id=\"ind_estadoID",$_USUARIOS,1 );
            $formulario -> nueva_tabla();
            $formulario -> botoni("Buscar","Listar()",0);
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
            
            if(empty($_REQUEST[horaini]))
                $_REQUEST[horaini] = '00:00:00';
            
            if(empty($_REQUEST[horafin]))
                $_REQUEST[horafin] = '23:59:00';
            
            
            $_USUARIOS = $this->getUsuarios( $_REQUEST );
            
        
            
            
            $_REQUEST[fecha_ini] = $_REQUEST[fec_inicial].' '.$_REQUEST[horaini];
            $_REQUEST[fecha_fin] = $_REQUEST[fec_final].' '.$_REQUEST[horafin];
            
            $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE NOVEDADES", "formulario\" id=\"formularioID");
            $formulario -> oculto("url_archiv\" id=\"url_archivID\"","inf_tipoxx_noveda.php",0);
            $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
            $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
            $formulario -> oculto("num_serie",0,0);
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("opcion",2,0);
            $formulario -> nueva_tabla();
            $formulario -> linea("Informe de Novedades de ".$_REQUEST[fecha_ini]." al ".$_REQUEST[fecha_fin]  ,1,"t2"); 

            $mHtml  = "<table width='100%'>";
            $mHtml .= "<tr>";
                $mHtml .= "<th class=cellHead >Usuarios</th>";
                $mHtml .= "<th class=cellHead >Novedad Especial</th>";
                $mHtml .= "<th class=cellHead >Solicita Tiempo</th>";
                $mHtml .= "<th class=cellHead >Mantiene Alarma</th>";
                $mHtml .= "<th class=cellHead >Genera Alarma</th>";
                $mHtml .= "<th class=cellHead >Otros</th>";
                $mHtml .= "<th class=cellHead >TOTAL</th>";
            $mHtml .= "</tr>";
            //$mHtml .= "<th class=cellHead >Novedad Especial</th>";
            //$mHtml .= "<th class=cellHead >Producto</th>";
            
            $_OTROS_ = $this -> getOtros( $_REQUEST ); 
            $_ESPEC_ = $this -> getNovedadesEspeciales( $_REQUEST ); 
            $_TIEMP_ = $this -> getSolicitaTiempo( $_REQUEST ); 
            $_MANTI_ = $this -> getMantieneAlarma( $_REQUEST );
            $_GENER_ = $this -> getGeneraAlarma( $_REQUEST );
            $_TODOS_ = $this -> getTodos( $_REQUEST );
            $_TOTAL_ = array();

            foreach($_USUARIOS AS $_USUARIO_){
                
                if((int)$_TODOS_[ $_USUARIO_[0] ] == 0) continue;
                
                $_TOTAL_[0]+= (int)$_ESPEC_[ $_USUARIO_[0] ];
                $_TOTAL_[1]+= (int)$_TIEMP_[ $_USUARIO_[0] ];
                $_TOTAL_[2]+= (int)$_MANTI_[ $_USUARIO_[0] ];
                $_TOTAL_[3]+= (int)$_GENER_[ $_USUARIO_[0] ];
                $_TOTAL_[4]+= (int)$_OTROS_[ $_USUARIO_[0] ];
                $_TOTAL_[5]+= (int)$_TODOS_[ $_USUARIO_[0] ];
                
                $mHtml .= "<tr class='row'>";
                    $mHtml .= "<td class='cellInfo' >{$_USUARIO_[nom_usuari]}</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_ESPEC_[ $_USUARIO_[0] ]) > 0 ? "  onclick=\"infoNoveda('NE',  '".$_USUARIO_[0]."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_ESPEC_[ $_USUARIO_[0] ])."</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TIEMP_[ $_USUARIO_[0] ]) > 0 ? "  onclick=\"infoNoveda('ST',  '".$_USUARIO_[0]."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TIEMP_[ $_USUARIO_[0] ])."</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_MANTI_[ $_USUARIO_[0] ]) > 0 ? "  onclick=\"infoNoveda('MA',  '".$_USUARIO_[0]."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_MANTI_[ $_USUARIO_[0] ])."</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_GENER_[ $_USUARIO_[0] ]) > 0 ? "  onclick=\"infoNoveda('GA',  '".$_USUARIO_[0]."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_GENER_[ $_USUARIO_[0] ])."</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_OTROS_[ $_USUARIO_[0] ]) > 0 ? "  onclick=\"infoNoveda('OT',  '".$_USUARIO_[0]."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_OTROS_[ $_USUARIO_[0] ])."</td>";
                    $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_TODOS_[ $_USUARIO_[0] ]) > 0 ? "  onclick=\"infoNoveda('All', '".$_USUARIO_[0]."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."');\" " : null )."  >".((int)$_TODOS_[ $_USUARIO_[0] ])."</td>";
                $mHtml .= "</tr>";
                
            }
            
            $mHtml .= "<tr>";
            $mHtml .= "<th class='cellHead' style='text-align: right;' >TOTAL:</th>";
            foreach ($_TOTAL_ as $row){
                $mHtml .= "<th class='cellHead' style='text-align: right;' >{$row}</th>";
            }
            $mHtml .= "</tr>";
            
            
            $mHtml .= "</table>";
            
            echo $mHtml;
            
            $_SESSION['LIST_TOTAL']=$mHtml;
    
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
            
            
            $array = array();
            
            if($__REQUEST['tipo']=='OT')
                $aux="     a.nov_especi !='1'
                       AND a.ind_tiempo !='1'
                       AND a.ind_manala !='1'
                       AND a.ind_alarma !='S'";
            if($__REQUEST['tipo']=='NE')
                $aux= "a.nov_especi ='1'";
            if($__REQUEST['tipo']=='ST')
                $aux= "a.ind_tiempo ='1'";
            if($__REQUEST['tipo']=='MA')
                $aux= "a.ind_manala ='1'";
            if($__REQUEST['tipo']=='GA')
                $aux= "a.ind_alarma ='S'";
            if($__REQUEST['tipo']=='All')
                $aux = '1=1';
            
            $sql ="(SELECT b.num_despac as Despacho, a.nom_noveda as Novedad,b.fec_contro as Fecha,
                           b.obs_contro as Observacion, d.abr_tercer as Transportadora
                      FROM ".BASE_DATOS.".tab_genera_noveda a,
                           ".BASE_DATOS.".tab_despac_contro b,
                           ".BASE_DATOS.".tab_despac_vehige c,
                           ".BASE_DATOS.".tab_tercer_tercer d
                     WHERE a.cod_noveda = b.cod_noveda
                       AND $aux
                       AND c.num_despac = b.num_despac
                       AND c.cod_transp = d.cod_tercer
                       AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                       AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                       AND LOWER(b.usr_creaci) = '".$__REQUEST[cod_usuar]."') 
                   UNION ALL
                  (SELECT b.num_despac, a.nom_noveda, b.fec_noveda ,
                          b.des_noveda, d.abr_tercer
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b,
                          ".BASE_DATOS.".tab_despac_vehige c,
                          ".BASE_DATOS.".tab_tercer_tercer d
                    WHERE a.cod_noveda = b.cod_noveda
                      AND $aux
                      AND c.num_despac = b.num_despac
                      AND c.cod_transp = d.cod_tercer
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."' 
                      AND LOWER(b.usr_creaci) = '".$__REQUEST[cod_usuar]."')
                    UNION ALL
                    (SELECT b.num_repnov, a.nom_noveda, b.fec_repnov ,
                            b.obs_repnov, d.abr_tercer
                       FROM ".BASE_DATOS.".tab_genera_noveda a, 
                            ".BASE_DATOS.".tab_report_noveda b,
                            ".BASE_DATOS.".tab_tercer_tercer d
                      WHERE a.cod_noveda = b.cod_noveda
                        AND $aux
                        AND b.cod_tercer = d.cod_tercer
                        AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                        AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' 
                        AND LOWER(b.usr_creaci) = '".$__REQUEST[cod_usuar]."')
                   
                  ORDER BY 5,1,3";

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
                    $mHtml .= "<td class='cellInfo' >{$row[4]}</td>";
                    $mHtml .= "<td class='cellInfo' >{$row[0]}</td>";
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
        
        function getTodos( $__REQUEST ){
            
            $sql ="SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                   UNION ALL
                   SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL
                   SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_report_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";
          
           $sql = "SELECT COUNT(*), LOWER(TRIM(a.usr_creaci))
                     FROM ( ".$sql." ) AS a, 
                          ".BASE_DATOS.".tab_genera_usuari b
                    WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                      AND b.ind_estado = 1 
                      AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                   GROUP BY 2 ";  

            $consul = new Consulta($sql, $this -> conexion);
          
            $MATRIZ = array(); 
            foreach ( $consul -> ret_matriz() as $row){
                $MATRIZ[ $row[1] ] = $row[0];
            }
            return $MATRIZ;
          
        }
        
        function getOtros( $__REQUEST ){
            
            $sql ="SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.nov_especi !='1'
                      AND a.ind_tiempo !='1'
                      AND a.ind_manala !='1'
                      AND a.ind_alarma !='S'
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                   UNION ALL
                   SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.nov_especi !='1'
                      AND a.ind_tiempo !='1'
                      AND a.ind_manala !='1'
                      AND a.ind_alarma !='S'
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL
                   SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_report_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.nov_especi !='1'
                      AND a.ind_tiempo !='1'
                      AND a.ind_manala !='1'
                      AND a.ind_alarma !='S'
                      AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";
          
           $sql = "SELECT COUNT(*), LOWER(TRIM(a.usr_creaci))
                     FROM ( ".$sql." ) AS a, 
                          ".BASE_DATOS.".tab_genera_usuari b
                    WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                      AND b.ind_estado = 1 
                      AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                   GROUP BY 2 ";  

          $consul = new Consulta($sql, $this -> conexion);
          
          $MATRIZ = array(); 
          foreach ( $consul -> ret_matriz() as $row){
              $MATRIZ[ $row[1] ] = $row[0];
          }
          return $MATRIZ;
          
        }
        
        function getNovedadesEspeciales( $__REQUEST ){
            
            $sql ="SELECT  b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.nov_especi ='1'
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL      
                   SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.nov_especi ='1'
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL      
                   SELECT b.usr_creaci 
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_report_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.nov_especi ='1'
                      AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

            $sql = "SELECT COUNT(*), LOWER(TRIM(a.usr_creaci))
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                    GROUP BY 2 ";  

            $consul = new Consulta($sql, $this -> conexion);
            
            $MATRIZ = array(); 
            foreach ( $consul -> ret_matriz() as $row){
                $MATRIZ[ $row[1] ] = $row[0];
            }
            return $MATRIZ;
            
        }
        
        function getSolicitaTiempo( $__REQUEST ){
            
            $sql ="SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_tiempo ='1'
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL
                   SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_tiempo ='1'
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL
                   SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_report_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_tiempo ='1'
                      AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

            $sql = "SELECT COUNT(*), LOWER(TRIM(a.usr_creaci))
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                    GROUP BY 2 ";  

            $consul = new Consulta($sql, $this -> conexion);
            
            $MATRIZ = array(); 
            foreach ( $consul -> ret_matriz() as $row){
                $MATRIZ[ $row[1] ] = $row[0];
            }
            return $MATRIZ;
            
        }
        
        function getMantieneAlarma( $__REQUEST ){
            
            $sql ="SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_manala ='1'
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                   UNION ALL  
                   SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_manala ='1'
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL  
                   SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_report_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_manala ='1'
                      AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

            $sql = "SELECT COUNT(*), LOWER(TRIM(a.usr_creaci))
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                    GROUP BY 2 ";  

            
            $consul = new Consulta($sql, $this -> conexion);
            
            $MATRIZ = array(); 
            foreach ( $consul -> ret_matriz() as $row){
                $MATRIZ[ $row[1] ] = $row[0];
            }
            return $MATRIZ;
            
        }
        
        function getGeneraAlarma( $__REQUEST ){
            
            $sql ="SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_contro b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_alarma ='S'
                      AND b.fec_contro >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_contro <= '".$__REQUEST[fecha_fin]."' 
                   UNION ALL  
                   SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_despac_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_alarma ='S'
                      AND b.fec_noveda >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_noveda <= '".$__REQUEST[fecha_fin]."'
                   UNION ALL  
                   SELECT b.usr_creaci
                     FROM ".BASE_DATOS.".tab_genera_noveda a, 
                          ".BASE_DATOS.".tab_report_noveda b
                    WHERE a.cod_noveda = b.cod_noveda
                      AND a.ind_alarma ='S'
                      AND b.fec_repnov >= '".$__REQUEST[fecha_ini]."'
                      AND b.fec_repnov <= '".$__REQUEST[fecha_fin]."' ";

            $sql = "SELECT COUNT(*), LOWER(TRIM(a.usr_creaci))
                      FROM ( ".$sql." ) AS a, 
                           ".BASE_DATOS.".tab_genera_usuari b
                     WHERE LOWER(a.usr_creaci) = LOWER(b.cod_usuari)
                       AND b.ind_estado = 1 
                       AND ( b.cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                    GROUP BY 2 ";  

            
            $consul = new Consulta($sql, $this -> conexion);
            
            $MATRIZ = array(); 
            foreach ( $consul -> ret_matriz() as $row){
                $MATRIZ[ $row[1] ] = $row[0];
            }
            return $MATRIZ;
            
        }
                
        function getJqueryCalendar(){
            return '<script>
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

        
        
        function getUsuarios( $__REQUEST = null ){
           $sql =" SELECT LOWER(TRIM(cod_usuari)), CONCAT( UPPER(nom_usuari ), ' - ', LOWER(cod_usuari) ) AS nom_usuari
                      FROM ".BASE_DATOS.".tab_genera_usuari
                     WHERE ind_estado = 1 
                       AND ( cod_perfil IN(1,7,8,73,70,77,669,713) OR cod_usuari LIKE '%eal%' OR nom_usuari LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' )
                       ".(empty($__REQUEST[cod_usuari]) ? null : " AND cod_usuari =  '".$__REQUEST[cod_usuari]."' ")."
                    ORDER BY 2";
            $consulta = new Consulta($sql, $this -> conexion);
            return $consulta -> ret_matriz(); 
        }
        
        
    }
    $service = new InfNovedadesUsuario($_SESSION['conexion']);


?> 

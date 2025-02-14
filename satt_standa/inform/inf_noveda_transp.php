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
          
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda_transp.js?v=003\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";
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
        $formulario -> caja ("Transito","des_transi\" id=\"des_transiID",1,0,0);
        $formulario -> caja ("Finalizados","des_final\" id=\"des_finalID",1,0,1);
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
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_noveda_transp.js?v=003\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
        
        echo "<script language=\"JavaScript\">
                BlocK('Cargando Detalle...', 1);
        </script>\n";

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
            $mHtml .= "<th class=cellHead >Despachos</th>";
            $mHtml .= "<th class=cellHead >Novedad Especial</th>";
            $mHtml .= "<th class=cellHead >Solicita Tiempo</th>";
            $mHtml .= "<th class=cellHead >Mantiene Alarma</th>";
            $mHtml .= "<th class=cellHead >Total Nov. Gps</th>";
            $mHtml .= "<th class=cellHead >Otros</th>";
            $mHtml .= "<th class=cellHead >TOTAL</th>";
        $mHtml .= "</tr>";
        
        $_NOVESP = $this -> getNovedadesEspeciales( $_REQUEST );
        $_SOLTIE = $this -> getSolicitaTiempo( $_REQUEST ); 
        $_MANALA = $this -> getMantieneAlarma( $_REQUEST ); 
        $_NOVGPS = $this -> getNovedaGps( $_REQUEST );
        $_OTROXX = $this -> getOtros( $_REQUEST ); 
        $_NOVFAC = $this -> getNovedadesFacturacion( $_REQUEST );
        $_DESPAC = $this -> getDespacTotal( $_REQUEST );
        $_TOTAL_ = array();   
        
        $des_transi  = isset($_REQUEST[des_transi]) ? 1:0;
        $des_final  = isset($_REQUEST[des_final]) ? 1:0;
        
        foreach( $_TRANSPORTADORA as $row)
        {
            $_TOTAL_U = 0;
            
            $_TOTAL_[0]+= (int)$_DESPAC[ $row['cod_tercer'] ];
            $_TOTAL_[1]+= (int)$_NOVESP[ $row['cod_tercer'] ];
            $_TOTAL_[2]+= (int)$_SOLTIE[ $row['cod_tercer'] ];
            $_TOTAL_[3]+= (int)$_MANALA[ $row['cod_tercer'] ];
            $_TOTAL_[4]+= (int)$_OTROXX[ $row['cod_tercer'] ];
            $_TOTAL_[5]+= $_TOTAL_[1] + $_TOTAL_[2] + $_TOTAL_[3] + $_TOTAL_[4];  
            $_TOTAL_[6]+= (int)$_NOVFAC[ $row['cod_tercer'] ];
            $_TOTAL_U = (int) $_NOVFAC[ $row['cod_tercer'] ];  

          if($_TOTAL_U == 0) continue;               
            
          $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' align='left' ><b>".$row['cod_tercer']."</b> - ".$row['abr_tercer']."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;'>".( (int)$_DESPAC[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_NOVESP[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('NE',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."','".$des_transi."','".$des_final."');\" " : null )."  >".((int)$_NOVESP[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_SOLTIE[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('ST',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."','".$des_transi."','".$des_final."');\" " : null )."  >".((int)$_SOLTIE[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_MANALA[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('MA',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."','".$des_transi."','".$des_final."');\" " : null )."  >".((int)$_MANALA[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_NOVGPS[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('NG',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."','".$des_transi."','".$des_final."');\" " : null )."  >".((int)$_NOVGPS[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ((int)$_OTROXX[ $row['cod_tercer'] ]) > 0 ? "  onclick=\"infoNoveda('OT',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."','".$des_transi."','".$des_final."');\" " : null )."  >".((int)$_OTROXX[ $row['cod_tercer'] ])."</td>";
            $mHtml .= "<td class='cellInfo' align='right' style='width: 12%; cursor: pointer;' ".( ($_TOTAL_U) > 0 ? "  onclick=\"infoNoveda('All',  '".$row['cod_tercer']."','".$_REQUEST[fecha_ini]."','".$_REQUEST[fecha_fin]."','".$des_transi."','".$des_final."');\" " : null )."  >".($_TOTAL_U)."</td>";
          $mHtml .= "</tr>";
        } 
        
        if( count( $_TRANSPORTADORA ) > 1 )
        {
          $mHtml .= "<tr>";
        $mHtml .= "<th class='cellHead' style='text-align: right;' >TOTAL:</th>";
        foreach ($_TOTAL_ as $key => $row){
            if($key == 4){
              $mHtml .= "<th class='cellHead' style='text-align: right;' ></th>";
            }

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
        $perfiles="'1','7','8','73'";
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

        $sql = "SELECT * FROM ( SELECT b.num_despac as Despacho,a.nom_noveda as Novedad,b.fec_contro as Fecha,
                      b.obs_contro as Observacion,d.abr_tercer as Transportadora,c.num_placax as Placa,
                      CONCAT(i.nom_tercer, ' ', i.nom_apell1,' ', i.nom_apell2 ) as Conductor,
                      g.nom_agenci AS agencia, b.usr_creaci AS usr_noveda,e.usr_creaci, e.fec_creaci,
                      IFNULL(e.fec_llegad,'N/a') as fec_llegad, IFNULL(e.fec_despac,'N/a') as fec_despac

          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_agenci g on e.cod_agedes = g.cod_agenci
            inner join ".BASE_DATOS.".tab_genera_usuari h on b.usr_creaci = h.cod_usuari
            inner join ".BASE_DATOS.".tab_tercer_tercer i on c.cod_conduc = i.cod_tercer 

          WHERE 1=1
                  AND h.cod_perfil IN (".$perfiles.")
                  AND d.cod_tercer = '".$__REQUEST[cod_transp]."'
                  AND c.ind_activo != 'N'";
        
        if($__REQUEST["des_transi"] == 1)
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if($__REQUEST["des_final"] == 1)
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        if($__REQUEST['tipo']=='NE')
            $sql .= " AND f.ind_novesp = 1 ";
        
        if($__REQUEST['tipo']=='ST')
            $sql .= " AND f.ind_soltie = 1 ";

        if($__REQUEST['tipo']=='MA')
            $sql .= " AND f.ind_manale = 1 
                      AND a.cod_tipoxx = 1 ";

        if($__REQUEST['tipo']=='OT')
            $sql .= " AND f.ind_fuepla = 1 ";

        if($__REQUEST['tipo']=='NG')
            $sql .= " AND a.cod_tipoxx = 2 ";

        $sql .= "UNION ALL
          SELECT b.num_despac as Despacho,a.nom_noveda as Novedad, b.fec_noveda as Fecha,
                 b.des_noveda as Observacion,d.abr_tercer as Transportadora,c.num_placax as Placa,
                 CONCAT(i.nom_tercer, ' ', i.nom_apell1,' ', i.nom_apell2 ) as Conductor, 
                 g.nom_agenci AS agencia, b.usr_creaci AS usr_noveda,e.usr_creaci, e.fec_creaci,
                 IFNULL(e.fec_llegad,'N/a') as fec_llegad, IFNULL(e.fec_despac,'N/a') as fec_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda
            inner join ".BASE_DATOS.".tab_genera_agenci g on e.cod_agedes = g.cod_agenci 
            inner join ".BASE_DATOS.".tab_genera_usuari h on b.usr_creaci = h.cod_usuari
            inner join ".BASE_DATOS.".tab_tercer_tercer i on c.cod_conduc = i.cod_tercer 

          WHERE 
            1=1
            AND h.cod_perfil IN (".$perfiles.")
            AND d.cod_tercer = '".$__REQUEST[cod_transp]."'
            AND c.ind_activo != 'N'";

        if($__REQUEST["des_transi"] == 1)
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if($__REQUEST["des_final"] == 1)
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        if($__REQUEST['tipo']=='NE')
            $sql .= " AND f.ind_novesp = 1 ";
        
        if($__REQUEST['tipo']=='ST')
            $sql .= " AND f.ind_soltie = 1 ";
        
        if($__REQUEST['tipo']=='MA')
            $sql .= " AND f.ind_manale = 1 
                      AND a.cod_tipoxx = 1 ";
        
        if($__REQUEST['tipo']=='OT')
            $sql .= " AND f.ind_fuepla = 1 ";
        
        if($__REQUEST['tipo']=='NG')
            $sql .= " AND a.cod_tipoxx = 2 ";
        
        $sql .= ") as aa ORDER BY 5,1,3 ";

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
        echo "<style>
            .table-int {
                margin: 0 auto;
                height: 20vh;
                width: 40vh;
            }

            .table-int,
            th,
            td {
                border-collapse: collapse;
            }
        </style>";

        $mHtml  = "<table width='100%'  border='1' class='table-int' >";
        $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead >Transportadora</th>";
            $mHtml .= "<th class=cellHead >Agencia</th>";
            $mHtml .= "<th class=cellHead >Despacho</th>";
            $mHtml .= "<th class=cellHead >Fecha Salida</th>";
            $mHtml .= "<th class=cellHead >Fecha Llegada</th>";
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
                $mHtml .= "<td class='cellInfo' >{$row[11]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[12]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[5]}</td>";
                $mHtml .= "<td class='cellInfo' >{$row[6]}</td>";
                $mHtml .= "<td class='cellInfo' >".htmlentities( $row[1], ENT_COMPAT, 'ISO-8859-1', true )."</td>";
                $mHtml .= "<td class='cellInfo' >{$row[2]}</td>";
                $mHtml .= "<td class='cellInfo' ><textarea name='textarea' rows='3' cols='50' readonly style='border: none;'>".htmlentities( $row[3], ENT_COMPAT, 'ISO-8859-1', true )."</textarea></td>";
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

    function getDespacTotal($__REQUEST){

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array(); 

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(a.num_despac) as c
          FROM 
            ".BASE_DATOS.".tab_despac_despac a 
            inner join ".BASE_DATOS.".tab_despac_vehige b on a.num_despac = b.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer c on b.cod_transp = c.cod_tercer
          WHERE c.cod_tercer = '".$transpor[0]."' AND b.ind_activo = 'S'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND a.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND a.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND a.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND a.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

       
        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];

      }

      return $MATRIZ;
    }
    
    function getNovedadesEspeciales( $__REQUEST ){

      $perfiles="'1','7','8','73'";

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array(); 

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(aa.num_despac) as c  FROM (
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari
          WHERE 1=1
                  AND g.cod_perfil IN (".$perfiles.")
                  AND f.ind_novesp = 1 
                  AND d.cod_tercer = '".$transpor[0]."'
                  AND c.ind_activo != 'N'";
        
        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        $sql .= "UNION ALL
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda  
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari
          WHERE 
            1=1
            AND g.cod_perfil IN (".$perfiles.")
            AND f.ind_novesp = 1 
            AND d.cod_tercer = '".$transpor[0]."'
            AND c.ind_activo != 'N'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }
        
        $sql .= ") as aa ";
        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];
        
      }

      return $MATRIZ;
    }

    
    
    function getSolicitaTiempo( $__REQUEST ){

      $perfiles="'1','7','8','73'";

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array();

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(aa.num_despac) as c  FROM (
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari
          WHERE 1=1
                  AND g.cod_perfil IN (".$perfiles.")
                  AND f.ind_soltie = 1 
                  AND d.cod_tercer = '".$transpor[0]."'
                  AND c.ind_activo = 'S'";
        
        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        $sql .= "UNION ALL
          SELECT b.num_despac 
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari 
          WHERE 
            1=1
            AND g.cod_perfil IN (".$perfiles.")
            AND f.ind_soltie = 1
            AND d.cod_tercer = '".$transpor[0]."'
            AND c.ind_activo = 'S'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }
        
        $sql .= ") as aa ";

        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];
      }
 
      return $MATRIZ;
    }
    
    function getMantieneAlarma( $__REQUEST ){

      $perfiles="'1','7','8','73'";

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array();

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(aa.num_despac) as c  FROM (
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari 
          WHERE 1=1
                  AND g.cod_perfil IN (".$perfiles.")
                  AND f.ind_manale = 1 
                  AND a.cod_tipoxx = 1
                  AND d.cod_tercer = '".$transpor[0]."'
                  AND c.ind_activo != 'N'";
        
        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        $sql .= "UNION ALL
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda  
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari 
          WHERE 
            1=1
            AND g.cod_perfil IN (".$perfiles.")
            AND f.ind_manale = 1
            AND a.cod_tipoxx = 1
            AND d.cod_tercer = '".$transpor[0]."'
            AND c.ind_activo != 'N'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }
        
        $sql .= ") as aa ";

        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];
      }

      return $MATRIZ;
    }
    
    
    function getOtros( $__REQUEST ){

      $perfiles="'1','7','8','73'";

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array();

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(aa.num_despac) as c  FROM (
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari 
          WHERE 1=1
                  AND g.cod_perfil IN (".$perfiles.")
                  AND f.ind_fuepla = 1 
                  AND d.cod_tercer = '".$transpor[0]."'
                  AND c.ind_activo != 'N'";
        
        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        $sql .= "UNION ALL
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda  
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari 
          WHERE 
            1=1
            AND g.cod_perfil IN (".$perfiles.")
            AND f.ind_fuepla = 1
            AND d.cod_tercer = '".$transpor[0]."'
            AND c.ind_activo != 'N'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }
        
        $sql .= ") as aa ";

        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];
      }

      return $MATRIZ;
    }

    function getNovedaGps( $__REQUEST ){

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array();

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(aa.num_despac) as c  FROM (
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
          WHERE 1=1
                  AND a.cod_tipoxx = 2
                  AND d.cod_tercer = '".$transpor[0]."'
                  AND c.ind_activo != 'N'";
        
        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        $sql .= "UNION ALL
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda  
          WHERE 
            1=1
            AND a.cod_tipoxx = 2
            AND d.cod_tercer = '".$transpor[0]."'
            AND c.ind_activo != 'N'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }
        
        $sql .= ") as aa ";

        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];
      }

      return $MATRIZ;
    }

    function getNovedadesFacturacion( $__REQUEST ){

      $perfiles="'1','7','8','73'";

      $transporta = $this -> getTranspor( array('busq_transp' => $__REQUEST[cod_transp]) );
      $MATRIZ = array(); 

      foreach($transporta AS $transpor)
      {
        $sql = "SELECT COUNT(aa.num_despac) as c  FROM (
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_contro b on a.cod_noveda = b.cod_noveda
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac
            inner join ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda 
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari
          WHERE 1=1
                  AND g.cod_perfil IN (".$perfiles.")
                  AND d.cod_tercer = '".$transpor[0]."'
                  AND c.ind_activo = 'S'";
        
        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }

        $sql .= "UNION ALL
          SELECT b.num_despac
          FROM 
            ".BASE_DATOS.".tab_genera_noveda a 
            inner join ".BASE_DATOS.".tab_despac_noveda b on a.cod_noveda = b.cod_noveda 
            inner join ".BASE_DATOS.".tab_despac_vehige c on b.num_despac = c.num_despac 
            inner join  ".BASE_DATOS.".tab_tercer_tercer d on c.cod_transp = d.cod_tercer 
            inner join ".BASE_DATOS.".tab_despac_despac e on b.num_despac = e.num_despac
            inner join ".BASE_DATOS.".tab_parame_novseg f on c.cod_transp = f.cod_transp AND b.cod_noveda = f.cod_noveda  
            inner join ".BASE_DATOS.".tab_genera_usuari g on b.usr_creaci = g.cod_usuari
          WHERE 
            1=1
            AND g.cod_perfil IN (".$perfiles.")
            AND d.cod_tercer = '".$transpor[0]."'
            AND c.ind_activo = 'S'";

        if(isset($__REQUEST["des_transi"]))
        {
          $sql .=" AND e.fec_despac  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_despac <= '".$__REQUEST[fecha_fin]."'";
        }

        if(isset($__REQUEST["des_final"]))
        {
          $sql .=" AND e.fec_llegad  >= '".$__REQUEST[fecha_ini]."' 
                    AND e.fec_llegad <= '".$__REQUEST[fecha_fin]."'";
        }
        
        $sql .= ") as aa ";
        $consulta = new Consulta($sql, $this -> conexion); 
        $data = $consulta->ret_matriz("a");
        $MATRIZ[ $transpor[0] ] = $data[0]['c'];
        
      }

      return $MATRIZ;
    }
    
}
$service = new InfNovedadesUsuario($_SESSION['conexion']);

?>
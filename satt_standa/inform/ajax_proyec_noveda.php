<?php

/* ! \file: ajax_proyec_noveda.php
 *  \brief: archivo con multiples funciones ajax para los ejecutar la respuesta informe de proyeccion de novedades(Informes > Gestion de operacion > Proyeccion de nov)
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \bug: 
 *  \warning:
 */

setlocale(LC_ALL, "es_ES");
     /*  ini_set('display_errors', 1); 
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL); */
class proyec_noveda {
    private static $cConexion,
    $cCodAplica,
    $cUsuario,
    $cTotalDespac,
    $cNull = array(array('', '-----'));

    function __construct($co = null, $us = null, $ca = null) {

        if ($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on') {
            @include_once( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            @include_once( "../lib/general/functions.inc" );
            @include_once( "../../satt_faro/constantes.inc" );

            self::$cConexion = $AjaxConnection;
        } else {
            self::$cConexion = $co;
            self::$cUsuario = $us;
            self::$cCodAplica = $ca;
        }

        if ($_REQUEST[Ajax] === 'on') {
            $opcion = $_REQUEST['opcion'];

            switch ($opcion) {
                case '1';
                $this -> ordenarInfo();

                break;

                case '3':
                    $this -> Listar();
                break;
                
                case '4':
                $this -> expInformExcel();
                break;
                
                case '5':
                $this -> Listar2();
                break;
          
            }
        }
    }


     /*! funcion: ordenarInfo
 *  \brief: funcion que organiza la info recibida desde POST y la envia a la funcion resultados
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */
    function ordenarInfo()
    {
       $empresaArray = $_POST['myData'][0];
       $servicioArray = $_POST['myData'][1];
       $this -> Resultado($empresaArray,$servicioArray);
    }

   
    /*! funcion: Resultado
 *  \brief: funcion que realiza todo el proceso de respuesta del filtro de informe de proyeccion de novedades(Informes > Gestion de operacion > Proyeccion de nov)
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */
    function Resultado($busq_transp , $servicioArray = null)
    {
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/proyec_noveda.js\"></script>\n";
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
      //-----------------------------------------------------------------------------
      include_once(__DIR__.'/class_despac_enruta.php' );
      //-----------------------------------------------------------------------------
      $obj_destra = new DespacRuta(self::$cConexion, $mData);
      //-----------------------------------------------------------------------------
      
      /***************************************************/
      $_PROMED = 40;
      $_CICLOX = 7;
      $_TOTALES = array();
      $_VERTICA = array();
      $_REPORT = array();
      $_TOTALES['tot_genera'] = 0;
      $hor_actual = date('G');
      $_REQUEST['busq_transp']  = $busq_transp;
      for( $mm = 1; $mm < $_CICLOX; $mm++ )
      {
        $_TOTALES[$hor_actual] = 0 ;
        $hor_actual++;
      }
      /***************************************************/
      
      if( $_REQUEST['busq_transp'] && $_REQUEST['busq_transp'] != '' )
      {
        foreach ($busq_transp as $key => $empresaID) {
            $res = $obj_destra->GetDespacTransiReport( $empresaID );
            $_REPORT[$key] = $res;
        }
      }
      else
      {
        $_REPORT = $obj_destra->GetDespacTransiReport();
      } 
      
      $mTipDes = self::getTipDes();
           
      $mHtml = NULL; 
      $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORME DE PROYECCI&Oacute;N DE NOVEDADES ", "formulario\" id=\"formularioID");
      $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
      $formulario -> oculto("url_archiv\" id=\"url_archivID\"","ajax_proyec_noveda.php",0);
      $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
      $formulario -> oculto("opcion",2,0);
      $formulario -> nueva_tabla();
      
      if( sizeof($_REPORT) == 1 && $_REPORT[0] == NULL)
      {
        $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=6050 \"target=\"centralFrame\">Volver a la B&uacute;squeda</a></b>";

       $mensaje =  "La Empresa <b> ".$_REQUEST['busq_transp'][0]." </b> no tiene Despachos en Ruta, o no tiene contratado un servicio (Monitoreo Activo o Combinado).".$link_a;
       $mens = new mensajes();
       $mens -> correcto("INFORME DE PROYECCI&Oacute;N DE NOVEDADES",$mensaje);
       die();
      }  
      $formulario -> linea($texto , 1, "t2"); 
      $this -> Style();
      $mHtml  = "<table width='100%'>";
      $mHtml .= "<tr>";
        $mHtml .= "<th class='cellHead' width='32%' rowspan='2'>EMPRESA-SERVICIO</th>";
        $mHtml .= "<th class='cellHead' width='3%'  rowspan='2'>FRECUENCIA<br>(Mins.)</th>";
        $mHtml .= "<th class='cellHead' width='3%'  rowspan='2'>VEH&Iacute;CULOS</th>";
        $mHtml .= "<th class='cellHead' width='3%'  colspan='".$_CICLOX."'>RANGOS (12 HORAS)</th>";
        $mHtml .= "</tr>";
        $mHtml .= "<tr>";
        $hor_actual = date('G');
        for( $i = 1; $i < $_CICLOX; $i++ )
        {
          if( strlen( $hor_actual ) == '1' )
          {
            $hor_actual = '0'.$hor_actual; 
          }
          $mHtml .= "<th class='cellHead' width='4%'>". $hor_actual .":00 a ".$hor_actual.":59</th>";
          $hor_actual++;
          if( $hor_actual > 23)
          {
            $hor_actual = 00;
          }
        }
        $mHtml .= "<th class='cellHead' width='4%'>TOTAL</th>";
      $mHtml .= "</tr>";
      $cont = 0;
      $z = 0;
      $valida = false;
      $i = 0;

/* Inicio del informe de empresas */

      foreach( $_REPORT as $key => $row )
      { 
        /* valida si se recorre la cantidad de veces de array de tipo de servicio */
        $i++;
        if(count($servicioArray) == $i)
        {
          $valida = true;
        }
        
        if($servicioArray != null)
        {/* Cuando se selecciona tipos de servicios evalua los que se seleccionan contra los consultados*/
            foreach ($servicioArray as $key => $value) {
              /* array_shift para obtener el id o nit de la empresa*/
                if( $row[array_shift(array_keys($row))]['cod_tipser'] == $value)
                {/* si se encuentra una coincidencia reinicia los contadores */
                  $i=0;
                  $z=0;
                $tie_contra = $this -> getTieCon( array_shift(array_keys($row)) );
                for( $l = 0; $l < count( $mTipDes ); $l++ )
                {
                    if($l == 0 )
                    {
                    $num_entran = $row[array_shift(array_keys($row))]['can_conurb'] ? $row[array_shift(array_keys($row))]['can_conurb'] : '-';
                    }
                    else
                    {
                    $num_entran = $row[array_shift(array_keys($row))]['can_contro'] ? $row[array_shift(array_keys($row))]['can_contro'] : '-';
                    }
                    
                    if( $num_entran == '-')
                    {
                    continue;
                    }
                    
                    $_VERTICA[array_shift(array_keys($row))] = 0;
                    $r = $mTipDes[$l];
                    $mHtml .= "<tr class='row'>";
                    $mHtml .= "<td class='cellInfo' align='left' ><b>".$this -> getNombre( array_shift(array_keys($row)) )."</b> - ". $r['nom_tipdes'] ."</td>";
                    if($l == 0 )
                    {
                    $num_entran = $row[array_shift(array_keys($row))]['can_conurb'] ? $row[array_shift(array_keys($row))]['can_conurb'] : '-';
                    $tiempo = $row[array_shift(array_keys($row))]['tie_conurb'];
                    $proper = $row[array_shift(array_keys($row))]['conurb']['can_finali'];
                    }
                    else
                    {
                    $num_entran = $row[array_shift(array_keys($row))]['can_contro'] ? $row[array_shift(array_keys($row))]['can_contro'] : '-';
                    $_num_entran = $row[array_shift(array_keys($row))]['can_contro'] ? $row[array_shift(array_keys($row))]['can_contro'] : '0';
                    
                    $tiempo = $row[array_shift(array_keys($row))]['tie_contro'];
                    $proper = $row[array_shift(array_keys($row))]['contro']['can_finali']; 
                    }
                    
                    $num_vehicu = $num_entran != NULL ? "style='cursor: pointer;' onclick='Totals(".array_shift(array_keys($row)).",".$l.");'><b>". $num_entran ."</b>": '>-';
                    $mHtml .= "<td class='cellInfo' align='center' >".$tiempo."</td>";
                    $mHtml .= "<td class='cellInfo' align='center' ".$num_vehicu."</td>";
                    $hor_actual = date('G');
                    for( $mm = 1; $mm < $_CICLOX; $mm++ )
                    {
                    $_cant = $proper[$hor_actual] != NULL ? "style='cursor: pointer;' onclick='Details(".$hor_actual.", ".array_shift(array_keys($row)).",".$l.");' >". $proper[$hor_actual] : '>-';
                    $__cant = $proper[$hor_actual] != NULL ? $proper[$hor_actual] : '0' ;
                    
                    $mHtml .= "<td class='cellInfo' align='center' ".$_cant."</td>";
                    $_VERTICA[array_shift(array_keys($row))] += $__cant;
                    $_TOTALES[$hor_actual] += $__cant;
                    $hor_actual++;
                    }
                    $mHtml .= "<th class='cellHead' align='center' width='4%'>". $_VERTICA[array_shift(array_keys($row))] ."</th>";
                    $mHtml .= "</tr>";
                    
                }
                $_TOTALES['tot_genera'] += $_num_entran;           
                $cont++;
                }else{
                  $z++;
                  /* Si el contador es igual al numero total de reportes quiere decir que ninguno es del tipo se servicio elegido */
                  if( count($_REPORT) == $z && $valida == true)
                  {
                    $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=6050 \"target=\"centralFrame\">Volver a la B&uacute;squeda</a></b>";
            
                   $mensaje =  "Las Empresas no tienen datos con ese tipo de servicio, despachos en ruta o no tiene contratado un servicio (Monitoreo Activo o Combinado).".$link_a;
                   $mens = new mensajes();
                   $mens -> correcto("INFORME DE PROYECCI&Oacute;N DE NOVEDADES",$mensaje);
                   die();
                  }  

                }
            }
        }else{/* Cuando NO se selecciona tipos de servicios lista todos los de la consulta*/
            if( $row[array_shift(array_keys($row))]['cod_tipser'] != '1')
            {
            $tie_contra = $this -> getTieCon( array_shift(array_keys($row)) );
            for( $l = 0; $l < count( $mTipDes ); $l++ )
            {
                if($l == 0 )
                {
                $num_entran = $row[array_shift(array_keys($row))]['can_conurb'] ? $row[array_shift(array_keys($row))]['can_conurb'] : '-';
                }
                else
                {
                $num_entran = $row[array_shift(array_keys($row))]['can_contro'] ? $row[array_shift(array_keys($row))]['can_contro'] : '-';
                }
                
                if( $num_entran == '-')
                {
                continue;
                }
                
                $_VERTICA[array_shift(array_keys($row))] = 0;
                $r = $mTipDes[$l];
                $mHtml .= "<tr class='row'>";
                $mHtml .= "<td class='cellInfo' align='left' ><b>".$this -> getNombre( array_shift(array_keys($row)) )."</b> - ". $r['nom_tipdes'] ."</td>";
                if($l == 0 )
                {
                $num_entran = $row[array_shift(array_keys($row))]['can_conurb'] ? $row[array_shift(array_keys($row))]['can_conurb'] : '-';
                $tiempo = $row[array_shift(array_keys($row))]['tie_conurb'];
                $proper = $row[array_shift(array_keys($row))]['conurb']['can_finali'];
                }
                else
                {
                $num_entran = $row[array_shift(array_keys($row))]['can_contro'] ? $row[array_shift(array_keys($row))]['can_contro'] : '-';
                $_num_entran = $row[array_shift(array_keys($row))]['can_contro'] ? $row[array_shift(array_keys($row))]['can_contro'] : '0';
                
                $tiempo = $row[array_shift(array_keys($row))]['tie_contro'];
                $proper = $row[array_shift(array_keys($row))]['contro']['can_finali']; 
                }
                
                $num_vehicu = $num_entran != NULL ? "style='cursor: pointer;' onclick='Totals(".array_shift(array_keys($row)).",".$l.");'><b>". $num_entran ."</b>": '>-';
                $mHtml .= "<td class='cellInfo' align='center' >".$tiempo."</td>";
                $mHtml .= "<td class='cellInfo' align='center' ".$num_vehicu."</td>";
                $hor_actual = date('G');
                for( $mm = 1; $mm < $_CICLOX; $mm++ )
                {
                $_cant = $proper[$hor_actual] != NULL ? "style='cursor: pointer;' onclick='Details(".$hor_actual.", ".array_shift(array_keys($row)).",".$l.");' >". $proper[$hor_actual] : '>-';
                $__cant = $proper[$hor_actual] != NULL ? $proper[$hor_actual] : '0' ;
                
                $mHtml .= "<td class='cellInfo' align='center' ".$_cant."</td>";
                $_VERTICA[array_shift(array_keys($row))] += $__cant;
                $_TOTALES[$hor_actual] += $__cant;
                $hor_actual++;
                }
                $mHtml .= "<th class='cellHead' align='center' width='4%'>". $_VERTICA[array_shift(array_keys($row))] ."</th>";
                $mHtml .= "</tr>";
                
            }
            $_TOTALES['tot_genera'] += $_num_entran;           
            $cont++;
            }
        }
        
      }

/* Fin del informe de empresas */

      $mHtml .= "<tr>";
      $mHtml .= "<th class='cellHead2' align='right' colspan='2' >TOTALES</th>";
      $mHtml .= "<th class='cellHead' align='center'>". $_TOTALES['tot_genera'] ."</th>";
      $hor_actual = date('G');
      $_VERTICA[$key] = 0;
      for( $i = 1; $i < $_CICLOX; $i++ )
      {
        $_VERTICA['tot_genera'] += $_TOTALES[$hor_actual];
        $mHtml .= "<th class='cellHead' width='4%'>". $_TOTALES[$hor_actual] ."</th>";
        $hor_actual++;
        
      }     
      $mHtml .= "<th class='cellHead' width='4%'>". $_VERTICA['tot_genera'] ."</th>";
      $mHtml .= "</tr>";
      $mHtml .= "<tr>";
      $mHtml .= "<th class='cellHead2' align='right' colspan='3' >CONTROLADORES SUGERIDOS</th>";
      $hor_actual = date('G');
      for( $i = 1; $i < $_CICLOX; $i++ )
      {
        $mHtml .= "<th class='cellHead' width='4%'>". number_format($_TOTALES[$hor_actual]/$_PROMED, 1) ."</th>";
        $hor_actual++;
      }
      $mHtml .= "<th class='cellHead' width='4%'>". number_format($_VERTICA['tot_genera']/$_PROMED, 1) ."</th>";       
      $mHtml .= "</tr>";
      $mHtml .= "<tr>";        
        $mHtml .= "<th class='footer' width='100%' align='left' colspan='10'>PROMEDIO DE LLAMADAS DE UN CONTROLADOR POR HORA: ".$_PROMED."</th>";
      $mHtml .= "</tr>";
      $mHtml .= "<tr>";
        $mHtml .= "<th class=footer width='100%' align='left' colspan='10'>TOTAL DE EMPRESAS CON DESPACHOS EN RUTA: ".$cont."</th>";
      $mHtml .= "</tr>";
      $mHtml .= "<tr>";
        $mHtml .= "<th class=footer width='100%' align='left' colspan='10'>FECHA DE GENERACI&Oacute;N DEL INFORME: ".date('Y-m-d h:i a')."</th>";
      $mHtml .= "</tr>";
      $mHtml  .= "</table>";   
      
      echo $mHtml;       
      $_SESSION['LIST_TOTAL'] = $mHtml;
      
      $formulario -> nueva_tabla();
      $formulario -> botoni("Volver","history.back();",0);
      $formulario -> nueva_tabla();

      /*------------------------------------------Div Para la apertura del POP-UP------------------------------------------*/
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
      echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
      echo ' <script>
              $(function() {
                $( "#popupDIV" ).dialog({
                  resizable: false,
                  height: 500,
                  width: $(document).width() - 300,
                  autoOpen: false,
                  show: {
                    effect: "fold",
                    duration: 600
                  },
                  hide: {
                    effect: "fold",
                    duration: 600,
                  }
                });
              });
              </script>';
      echo '<div id="popupDIV" style=" left: 0px; top: 0px; z-index: 3; overflow: auto; border: 5px solid #333333;  "></div>';
      
      
      /*-------------------------------------------------------------------------------------------------------------------*/       
    }
    
    function getTieCon( $cod_transp )
    {
      $mSub =" ( SELECT MAX( num_consec ) 
                FROM ".BASE_DATOS.".tab_transp_tipser
               WHERE cod_transp = '". $cod_transp ."' AND
                     cod_tipser != '1' AND 
                     ind_estado = '1' )";
               
      $mSql = "( SELECT cod_transp, 1 as cod_tiencon, tie_conurb
                 FROM ".BASE_DATOS.".tab_transp_tipser
                WHERE cod_transp = '". $cod_transp ."' AND
                      num_consec = ". $mSub ." AND
                      cod_tipser != '1' AND 
                      ind_estado = '1' )";
      $mSql .= " UNION ";
      $mSql .= " ( SELECT cod_transp, 2 as cod_tiencon, tie_contro
                 FROM ".BASE_DATOS.".tab_transp_tipser
                WHERE cod_transp = '". $cod_transp ."' AND
                      num_consec = ". $mSub ." AND
                      cod_tipser != '1' AND 
                      ind_estado = '1' )";
      
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport;
    } 
    
    function getTipDes()
    {
      $mSql = "SELECT cod_tipdes, UPPER( nom_tipdes ) as nom_tipdes
                 FROM ".BASE_DATOS.".tab_genera_tipdes
                WHERE cod_tipdes IN( 1,2 ) ";
      $mSql .= "GROUP BY 1 
             ORDER BY 1 ";
      
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport;
    }
    
    function getNombre( $cod_tercer )
    {
      $mSql = "SELECT UPPER( abr_tercer ) as getNombre
                 FROM ".BASE_DATOS.".tab_tercer_tercer
                WHERE cod_tercer =". $cod_tercer ." ";
      $mSql .= "GROUP BY 1 
             ORDER BY 1 ";
      
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport[0][0];
    }

    function getTipoServicio()
    {
      $mSql = "SELECT *  FROM `tab_genera_tipser` 
      WHERE `nom_tipser` LIKE 'OAL' 
      OR `nom_tipser` LIKE 'MA' 
      OR `nom_tipser` LIKE 'OAL/MA'
      ORDER BY nom_tipser DESC";
      
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport;
    }
    
    function getTransports( $cod_transp = NULL )
    {    
        
     $mSql = "SELECT a.cod_tercer, CONCAT(a.cod_tercer,' - ', a.abr_tercer ) as abr_tercer, e.cod_tipser
                 FROM ".BASE_DATOS.".tab_tercer_tercer a,
                      ".BASE_DATOS.".tab_tercer_activi b,
                      ".BASE_DATOS.".tab_despac_vehige c,
                      ".BASE_DATOS.".tab_transp_tipser e
                WHERE a.cod_tercer = b.cod_tercer AND
                      a.cod_tercer = c.cod_transp AND
                      a.cod_tercer = e.cod_transp AND
                      e.num_consec = ( SELECT MAX( num_consec ) 
                                         FROM ".BASE_DATOS.".tab_transp_tipser
                                        WHERE cod_transp = a.cod_tercer AND
                                              ind_estado = '1'
                                      ) AND
                      b.cod_activi = ".COD_FILTRO_EMPTRA."
                      ";
      if( $cod_transp && $cod_transp != NULL )
      {
        $mSql .= " AND c.cod_transp = '". $cod_transp ."' ";
      }  
      $mSql .= "GROUP BY 1 
                ORDER BY 2 ASC ";
      

  
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;

      $mReturn = array();
      
      foreach( $mReport as $row )
      {
        if( $row['cod_tipser'] != '1' )
        {
          $mReturn[] = $row;
        }
      }
  
      return $mReturn;
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
                background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                background: -moz-linear-gradient(top, #009617, #00661b ); 
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
                color:#fff;
                text-align:left;
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
                text-align:right;
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


    function Scripts(){
      $mTransp = $this -> getTransports();
      echo '<script>
      $(document).ready(function() {
            $(function() {
            var tranportadoras = 
            [';
      if( $mTransp )
      {
        echo "\"Ninguna\"";
        foreach( $mTransp as $row )
        {
          echo ", \"$row[cod_tercer] - $row[abr_tercer]\"";
        }			
      };
      echo ']

              $( "#busq_transp" ).autocomplete({
              source: tranportadoras,
              delay: 100
              }).bind( "autocompleteclose", function(event, ui){$("#form_busquedaID").submit();
              });

                $( "#busq_transp" ).bind( "autocompletechange", function(event, ui){
                  
                  $("#form_busquedaID").submit();
                  });

              });
            });
              </script>';
    }


    function Listar2()
    {  
      global $HTTP_POST_FILES;
      $BASE = $_SESSION[BASE_DATOS];
      define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
      define ('ESTILO', $_SESSION['ESTILO']);
      define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
  
      include_once( '../inform/class_despac_enruta.php' );
      
      echo "<style>
            .ui-widget-content a {
              color: #000000;
              font-weight:bold;
            }
            </style>";

      
      $obj_destra = new DespacRuta(self::$cConexion, $mData);
      $_REPORT = $obj_destra->GetDespacTransiReport( $_REQUEST['cod_transp'] );
    
      if( $_REQUEST['cod_tipdes'] == 0 )
      {
        $_DESPAC = $_REPORT[ $_REQUEST['cod_transp'] ]['conurb']['num_despac'];
        $tie_contra = $_REPORT[ $_REQUEST['cod_transp'] ]['tie_conurb'];
      }
      
      elseif( $_REQUEST['cod_tipdes'] == 1 )
      {
        $_DESPAC = $_REPORT[ $_REQUEST['cod_transp'] ]['contro']['num_despac'];
        $tie_contra = $_REPORT[ $_REQUEST['cod_transp'] ]['tie_contro'];
      } 
      $_DESPAC = explode( ",", $_DESPAC );
      $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE DESPACHOS", "formulario");
      $formulario -> nueva_tabla(); 
      $formulario -> botoni("Excel","exportarXls()",1);
      $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
      $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
      $formulario -> cerrar();
      $formulario -> nueva_tabla();
      //echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
      $toHora = $this -> toHora ( $_REQUEST[pos_matrix] );
      $formulario -> linea("SE ENCONTR&Oacute; UN TOTAL DE ".sizeof($_DESPAC)." DESPACHOS PARA LA EMPRESA &laquo;".$this -> getNombre( $_REQUEST['cod_transp'] )."&raquo;" ,0,"t2","15%");
      
      $mHtml  = "<table width='100%'>";
      $mHtml .= "<tr>";
        $mHtml .= "<th class=cellHead width='3%' >No.</th>";
        $mHtml .= "<th class=cellHead width='7%' >Despacho</th>";
        $mHtml .= "<th class=cellHead width='7%' >Placa</th>";
        $mHtml .= "<th class=cellHead width='7%' >Manifiesto</th>";
        $mHtml .= "<th class=cellHead width='10%' >Fecha Salida</th>";
        $mHtml .= "<th class=cellHead width='10%' >Fecha Est. Llegada</th>";
        $mHtml .= "<th class=cellHead width='15%' >Ult. Novedad</th>";
        $mHtml .= "<th class=cellHead width='10%' >Ult. Fecha</th>";
        $mHtml .= "<th class=cellHead width='10%' >Ult. Sitio</th>";
        $mHtml .= "<th class=cellHead width='10%' >Fecha Prox. Novedad</th>";
        $mHtml .= "<th class=cellHead width='20%' >Observaciones</th>";
      $mHtml .= "</tr>";
          
      for( $i = 0, $cont = 1 ; $i < count( $_DESPAC ); $i++ )
      {
        if( trim( $_DESPAC[$i] ) != NULL )
        {
          $_INFONOVEDA = $this -> getInfoNoveda( trim( $_DESPAC[$i] ) );  
          $_INFOGENERA = $this -> getInfoDespac( trim( $_DESPAC[$i] ) );  
          /*************************************************************************/
          $_PLACAX = $_INFOGENERA[1] != NULL ? $_INFOGENERA[1] : " - ";
          $_MANIFI = $_INFOGENERA[0] != NULL ? $_INFOGENERA[0] : " - ";
          $_FSALID = $_INFOGENERA[2] != NULL ? $_INFOGENERA[2] : " - ";
          /*************************************************************************/
          $_FESTLL = $_INFONOVEDA[13] != NULL ? $_INFONOVEDA[13] : " SIN CONFIRMAR ";
          $_NOVEDA = $_INFONOVEDA[6] != NULL ? $_INFONOVEDA[6] : " - ";
          $_FECHAN = $_INFONOVEDA[7] != NULL ? $_INFONOVEDA[7] : " - ";
          $_SITION = $_INFONOVEDA[5] != NULL ? $_INFONOVEDA[5] : " - ";
          $_OBSERV = $_INFONOVEDA[4] != NULL ? $_INFONOVEDA[4] : " - ";
          /*************************************************************************/
          
          if( !$_INFONOVEDA[7] )
          {
            $_PROXNOV = '-';
          }
          else
          {
            $_PROXNOV = $obj_destra->sumarMinutosFecha( $_INFONOVEDA[7], $tie_contra );
          }
          
          $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' align ='left' width='3%' >". $cont++ ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='7%' ><a href=".DIREC_APLICA."index.php?cod_servic=1381&window=central&despac=".$_DESPAC[$i]."&opcion=1' target='_blank'>". $_DESPAC[$i] ."</a></td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='7%' >". $_PLACAX ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='7%' >". $_MANIFI ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_FSALID ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_FESTLL ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='15%' >". $_NOVEDA ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_FECHAN ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_SITION ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_PROXNOV ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='20%' >". $_OBSERV ."</td>";
          $mHtml .= "</tr>";
        }
      }
      
      echo $mHtml;    
      $_SESSION['LIST_TOTAL'] = $mHtml;
            
      $formulario -> nueva_tabla(); 
      $formulario -> botoni("Excel","exportarXls()",1);
      $formulario -> cerrar();  
    
    }
    function Listar()
    {    
 
      global $HTTP_POST_FILES;
      $BASE = $_SESSION[BASE_DATOS];
      define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
      define ('ESTILO', $_SESSION['ESTILO']);
      define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
            
      include( "../lib/general/conexion_lib.inc" );
      include( "../lib/general/form_lib.inc" );
      include( "../lib/general/tabla_lib.inc" );
      include( "../lib/general/constantes.inc" );
      include( "../lib/bd/seguridad/aplica_filtro_perfil_lib.inc" );
      include_once( '../inform/class_despac_enruta.php' );
      
      echo "<style>
            .ui-widget-content a {
              color: #000000;
              font-weight:bold;
            }
            </style>";

     
      $obj_destra = new DespacRuta(self::$cConexion, $mData);
      $_REPORT = $obj_destra->GetDespacTransiReport( $_REQUEST['cod_transp'] );

      
      if( $_REQUEST['cod_tipdes'] == 0 )
      {
        $_DESPAC = $_REPORT[ $_REQUEST['cod_transp'] ]['conurb']['can_desfin'][ $_REQUEST[pos_matrix] ];
        $tie_contra = $_REPORT[ $_REQUEST['cod_transp'] ]['tie_conurb'];
      }
      
      elseif( $_REQUEST['cod_tipdes'] == 1 )
      {
        $_DESPAC = $_REPORT[ $_REQUEST['cod_transp'] ]['contro']['can_desfin'][ $_REQUEST[pos_matrix] ];
        $tie_contra = $_REPORT[ $_REQUEST['cod_transp'] ]['tie_contro'];
      }    
      
      $_DESPAC = explode( ",", $_DESPAC );

      $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INFORMACION DE DESPACHOS", "formulario");
      $formulario -> nueva_tabla(); 
      $formulario -> botoni("Excel","exportarXls()",1);
      $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
      $formulario -> oculto("based\" id=\"basedID\"",BASE_DATOS,0);
      $formulario -> cerrar();
      $formulario -> nueva_tabla();
      echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
      $toHora = $this -> toHora ( $_REQUEST[pos_matrix] );
      $formulario -> linea("SE ENCONTR&Oacute; UN TOTAL DE ".sizeof($_DESPAC)." DESPACHOS PARA LA EMPRESA &laquo;".$this -> getNombre( $_REQUEST['cod_transp'] )."&laquo; ". $toHora ,0,"t2","15%");
      
      $mHtml  = "<table width='100%'>";
      $mHtml .= "<tr>";
        $mHtml .= "<th class=cellHead width='3%' >No.</th>";
        $mHtml .= "<th class=cellHead width='7%' >Despacho</th>";
        $mHtml .= "<th class=cellHead width='7%' >Placa</th>";
        $mHtml .= "<th class=cellHead width='7%' >Manifiesto</th>";
        $mHtml .= "<th class=cellHead width='10%' >Fecha Salida</th>";
        $mHtml .= "<th class=cellHead width='10%' >Fecha Est. Llegada</th>";
        $mHtml .= "<th class=cellHead width='15%' >Ult. Novedad</th>";
        $mHtml .= "<th class=cellHead width='10%' >Ult. Fecha</th>";
        $mHtml .= "<th class=cellHead width='10%' >Ult. Sitio</th>";
        $mHtml .= "<th class=cellHead width='10%' >Fecha Prox. Novedad</th>";
        $mHtml .= "<th class=cellHead width='20%' >Observaciones</th>";
      $mHtml .= "</tr>";
          
      for( $i = 0, $cont = 1 ; $i < count( $_DESPAC ); $i++ )
      {
        if( trim( $_DESPAC[$i] ) != NULL )
        {
          $_INFONOVEDA = $this -> getInfoNoveda( trim( $_DESPAC[$i] ) );  
          $_INFOGENERA = $this -> getInfoDespac( trim( $_DESPAC[$i] ) );  
          /*************************************************************************/
          $_PLACAX = $_INFOGENERA[1] != NULL ? $_INFOGENERA[1] : " - ";
          $_MANIFI = $_INFOGENERA[0] != NULL ? $_INFOGENERA[0] : " - ";
          $_FSALID = $_INFOGENERA[2] != NULL ? $_INFOGENERA[2] : " - ";
          /*************************************************************************/
          if( !$_INFONOVEDA[7] )
          {
            $_PROXNOV = '-';
          }
          else
          {
            $_PROXNOV = $obj_destra->sumarMinutosFecha( $_INFONOVEDA[7], $tie_contra );
          }
          //echo "<br>".$_PROXNOV;
          $_FESTLL = $_INFONOVEDA[13] != NULL ? $_INFONOVEDA[13] : " SIN CONFIRMAR ";
          $_NOVEDA = $_INFONOVEDA[6] != NULL ? $_INFONOVEDA[6] : " - ";
          $_FECHAN = $_INFONOVEDA[7] != NULL ? $_INFONOVEDA[7] : " - ";
          $_SITION = $_INFONOVEDA[5] != NULL ? $_INFONOVEDA[5] : " - ";
          $_OBSERV = $_INFONOVEDA[4] != NULL ? $_INFONOVEDA[4] : " - ";
          /*************************************************************************/
          $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' align ='left' width='3%' >". $cont++ ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='7%' ><a href='https://ap.intrared.net:444/ap/satt_faro/index.php?cod_servic=1381&window=central&despac=".$_DESPAC[$i]."&opcion=1' target='_blank'>". $_DESPAC[$i] ."</a></td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='7%' >". $_PLACAX ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='7%' >". $_MANIFI ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_FSALID ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_FESTLL ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='15%' >". $_NOVEDA ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_FECHAN ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_SITION ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='10%' >". $_PROXNOV ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' width='20%' >". $_OBSERV ."</td>";
          $mHtml .= "</tr>";
        }
      }
      
      echo $mHtml;    
      
      $_SESSION['LIST_TOTAL'] = $mHtml;
            
      $formulario -> nueva_tabla(); 
      $formulario -> botoni("Excel","exportarXls()",1);
      $formulario -> cerrar();  
    }
    function expInformExcel()
    {    
      $archivo = "Informe_Proyeccion_Novedades_".date( "Y_m_d" ).".xls";
      header('Content-Type: application/octetstream');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.$archivo.'"');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      echo $_SESSION['LIST_TOTAL']; 
  }
    function toHora( $Hora )
    {
      if( $Hora < 10 )
      {
        $Hora = "0".$Hora;
      }
      return ", ENTRE ".$Hora.":00 Y ".$Hora.":59";  
    }
    
    function getInfoNoveda( $num_despac )
    {
      $mSql = "( SELECT a.cod_contro, a.cod_noveda, a.fec_contro, a.usr_creaci,
                        a.obs_contro, b.nom_sitiox, c.nom_noveda, a.fec_contro, 
                        a.cod_contro, a.fec_creaci, d.cod_manifi, e.num_placax,
                        d.fec_salida, e.fec_llegpl
                   FROM ".BASE_DATOS.".tab_despac_contro a,
                        ".BASE_DATOS.".tab_despac_sitio b,
                        ".BASE_DATOS.".tab_genera_noveda c,
                        ".BASE_DATOS.".tab_despac_despac d,
                        ".BASE_DATOS.".tab_despac_vehige e
                  WHERE a.cod_sitiox = b.cod_sitiox 
                        AND a.num_despac = d.num_despac
                        AND a.num_despac = e.num_despac
                        AND a.cod_noveda = c.cod_noveda
                        AND a.num_despac = '".$num_despac."' )
                        UNION
               ( SELECT a.cod_contro, a.cod_noveda, a.fec_noveda, a.usr_creaci,
                        a.des_noveda, b.nom_contro, c.nom_noveda, a.fec_noveda, 
                        a.cod_contro, a.fec_creaci, d.cod_manifi, e.num_placax,
                        d.fec_salida, e.fec_llegpl
                        FROM ".BASE_DATOS.".tab_despac_noveda a,
                        ".BASE_DATOS.".tab_genera_contro b,
                        ".BASE_DATOS.".tab_genera_noveda c,
                        ".BASE_DATOS.".tab_despac_despac d,
                        ".BASE_DATOS.".tab_despac_vehige e
                        WHERE a.cod_contro = b.cod_contro 
                        AND a.num_despac = d.num_despac
                        AND a.num_despac = e.num_despac
                        AND a.cod_noveda = c.cod_noveda
                        AND a.num_despac = '".$num_despac."' )
                        ORDER BY 10 DESC
                        LIMIT 1 ";
                        
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport[0];
    }
    
    function getInfoDespac( $num_despac )
    {
      $mSql = "( SELECT d.cod_manifi, e.num_placax, d.fec_salida
                   FROM ".BASE_DATOS.".tab_despac_despac d,
                        ".BASE_DATOS.".tab_despac_vehige e
                  WHERE  e.num_despac = d.num_despac
                        AND e.num_despac = '".$num_despac."' )
                        LIMIT 1 ";
                        
      $mConsult = new Consulta( $mSql, self::$cConexion );      
      $mReport = $mConsult -> ret_matriz( 'a' ) ;
      
      return $mReport[0];
    }

}

if ($_REQUEST[Ajax] === 'on') {
    $_NOVEDA = new proyec_noveda();
}
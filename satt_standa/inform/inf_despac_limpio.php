<?php

header('Content-Type: text/html; charset=UTF-8');
ini_set('memory_limit', '2048M');

class InformViajes
{
  var $conexion,
      $cod_aplica,
      $usuario;
  var $cNull = array( array('', '- Todos -') ); 
  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  function principal()
  {
    switch($GLOBALS[opcion])
    {
      case 99:
        $this -> getInform();
      break;

      case 1:
        $this -> exportExcel();
      break;
      
      default:
        $this -> Listar();
      break;
    }
  }
  
  function Style()
  {
    echo "  <style>
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
  
  function getInform()
  {
    $this -> Style();

    echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    
    $mSelect  ="
                SELECT a.num_despac
                FROM satt_faro.tab_despac_despac a 
                WHERE a.fec_salida IS NOT NULL 
                                 AND a.fec_salida <= NOW() 
                                 AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                                 AND a.ind_planru = 'S' 
                                 AND a.ind_anulad = 'R'
                                 
            ";


    
    if( $_REQUEST['num_dessat'] != '' )
      $mSelect .= " AND a.num_despac = '".$_REQUEST['num_dessat']."'";
    else{
     $mSelect .= "  AND a.fec_despac BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finali']." 23:59:59'";
    }


    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_INFORM = $consulta -> ret_matriz();


    $formulario = new Formulario ( "?", "post", "Informe de Trazabilidad ", "frm_trazab\" id=\"frm_trazabID");
    



    


    echo '<a href="index.php?cod_servic='.$GLOBALS['cod_servic'].'&window=central&opcion=1 "target="centralFrame"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0"></a>';
    $mHtml  = "<table border='1'>";
      if( sizeof( $_INFORM ) > 0 )
      {
        $size = $_REQUEST['ind_noveda'] == '1' ? '72' : '68' ;
        $mHtml .= "<tr>";
          $mHtml .= "<th class=cellHead colspan='$size' >SE ENCONTRARON ".sizeof( $_INFORM )." REGISTROS</th>";
        $mHtml .= "</tr>"; 
        
        $mHtml .= "<tr>";
          $mHtml .= "<th class=cellHead >No.</th>";
          $mHtml .= "<th class=cellHead >Despacho</th>";
          $mHtml .= "<th class=cellHead >Novedad limpio</th>";
          $mHtml .= "<th class=cellHead >Novedad no limpio</th>";
          $mHtml .= "<th class=cellHead >% Limpio</th>";
          $mHtml .= "<th class=cellHead >% No limpio</th>";
          $mHtml .= "<th class=cellHead >Total novedades</th>";

        $mHtml .= "</tr>";
        
        $count = 1;
        foreach( $_INFORM as $row )
        {
          $mArrayNove = InformViajes::getDespacNoveda($row[0]);




          $mLimpio = 0;
          $mSucio = 0;

          foreach ($mArrayNove as $row1) {
            if($row1[0] == 1)
              $mLimpio += $row1[1];
            else if($row1[0] == 0)
              $mSucio += $row1[1];
          }


 
          $mTotalNov = $mSucio + $mLimpio ;


          $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' align ='center' >". $count ."</td>";
            $mHtml .= "<td class='cellInfo' align ='center' >".'<a href="?cod_servic=3302&window=central&despac='.$row[0].'&opcion=1" target="_blank">'. $row[0] ."</a></td>";
            $mHtml .= "<td class='cellInfo' align ='right' >". ($mLimpio == null ? "0": $mLimpio) ."</td>";
            $mHtml .= "<td class='cellInfo' align ='right' >". ($mSucio == null ? "0" : $mSucio) ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' >". ($mTotalNov >0? number_format((($mLimpio*100)/$mTotalNov),2) : "---") ."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' >". ($mTotalNov >0? number_format((($mSucio*100)/$mTotalNov),2) : "---" )."</td>";
            $mHtml .= "<td class='cellInfo' align ='right' >". ($mTotalNov== null ? "0" : $mTotalNov)."</td>";


          $mHtml .= "</tr>";
          $count++;
        }
        
      }
      else
      {
        $mHtml .= "<tr>";
          $mHtml .= "<th class=cellHead width='3%' >NO SE ENCONTRARON REGISTROS</th>";
        $mHtml .= "</tr>"; 
      }
    $mHtml  .= "</table>";
    
    $_SESSION[xls_InformViajes] = $mHtml;
    echo $mHtml;

    $formulario -> cerrar();
  }
  
  function toFecha( $date )
  {
    $fecha = explode( " ", $date );
    
    $fec1 = explode( "-", $fecha[0] );
    
    switch( (int)$fec1[1] )
    {
      case 1:$mes = 'ENERO'; break;
      case 2:$mes = 'FEBRERO'; break;
      case 3:$mes = 'MARZO'; break;
      case 4:$mes = 'ABRIL'; break;
      case 5:$mes = 'MAYO'; break;
      case 6:$mes = 'JUNIO'; break;
      case 7:$mes = 'JULIO'; break;
      case 8:$mes = 'AGOSTO'; break;
      case 9:$mes = 'SEPTIEMBRE'; break;
      case 10:$mes = 'OCTUBRE'; break;
      case 11:$mes = 'NOVIEMBRE'; break;
      case 12:$mes = 'DICIEMBRE'; break;
    }
    return $mes.' '.$fec1[2].' DE '.$fec1[0].' '.$fecha[1];
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
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/Verde/css/estilos.css' type='text/css'>";



    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_despac_limpio.js\"></script>\n";



    echo '<script>
        jQuery(function($) 
        {
          $( "#fec_iniciaID, #fec_finaliID" ).datepicker({
            changeMonth: true,
            changeYear: true
          });
          
          $.mask.definitions["A"]="[12]";
          $.mask.definitions["M"]="[01]";
          $.mask.definitions["D"]="[0123]";
          
          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";
          
          $( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
        });
         
        $(function() {
            var arr_transp = [];
            var nom_transp = "";
            var cod_transp = "";
            $( "#tabs" ).hide();
            $( "#tabs" ).tabs({
              beforeLoad: function( event, ui ) {
                ui.jqXHR.fail(function() {
                  ui.panel.html(
                    "No podemos cargar las tablas.");
                });
              }
            });
                   
            $("#TransportadoraID").autocomplete({
              source:"../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getTransportadoras",
              minLength: 2,
              select: function(event, ui){
                nom_transp = ui.item.label;
                arr_transp = nom_transp.split("-") ;
                $("#cod_transpID").val(arr_transp[0]);
              }
            });
                         
            $("#nom_modaliID").autocomplete({
              source:"../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getModalidades",
              minLength: 2,
              select: function(event, ui){
                nom_modali = ui.item.label;
                arr_modali = nom_modali.split("-") ;
                $("#cod_modaliID").val(arr_modali[0]);
              }
            });
      
            $("#btnPrincipal").click(function(){
             
              if ($("#cod_transpID").val() == "") {
                alert("Debe seleccionar una Transportadora");
                return false;
              }

             var parame = "";
             $( "#tabs" ).show();
             $("#ui-tabs-1").hide();
             $("#ui-tabs-2").hide();
             $("#ui-tabs-3").hide(); 
             $("#fec_inicia").val($("#fec_iniciaID").val());
             $("#fec_finali").val($("#fec_finaliID").val());
             $("#cod_transp").val($("#cod_transpID").val());

             parame += "&fec_inicia="+$("#fec_iniciaID").val()+"&fec_finali="+$("#fec_finaliID").val()+"&cod_transp="+$("#cod_transpID").val();

             $("#pestana1").click();
            });
   
             $("#pestana1").click(function(){
                var parame = "";
                parame += "&fec_inicia="+$("#fec_iniciaID").val()+"&fec_finali="+$("#fec_finaliID").val()+"&cod_transp="+$("#cod_transpID").val();
                parame += "&num_dessat="+$("#num_dessatID").val()+"&num_placa="+$("#num_placaID").val()+"&cod_modali="+$("#cod_modaliID").val();
                parame += "&num_viajex="+$("#num_viajexID").val();
                $.ajax({
                url: "../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getNovedadesTotal"+parame,
                beforeSend: function(){
                  $( "<div id=\"tempPop\"><center><img src=\"../'.DIR_APLICA_CENTRAL.'/imagenes/ajax-loader.gif\"></center></div>" ).dialog({
                    modal: true
                    });

                },
                success: function(data){  
                  $("#tempPop").dialog(\'destroy\').remove();
                  $("#tabs-1").html("");
                  $("#ui-tabs-1").find("table").remove();
                  $("#tabs-1").html(data);
                }
              })
            });


            $("#pestana2").click(function(){
              var parame = "";
                parame += "&fec_inicia="+$("#fec_iniciaID").val()+"&fec_finali="+$("#fec_finaliID").val()+"&cod_transp="+$("#cod_transpID").val();
                parame += "&num_dessat="+$("#num_dessatID").val()+"&num_placa="+$("#num_placaID").val()+"&cod_modali="+$("#cod_modaliID").val();
                parame += "&num_viajex="+$("#num_viajexID").val();
               $.ajax({
                url: "../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getNovedadesLimpio"+parame,
                beforeSend: function(){
                  $( "<div id=\"tempPop\"><center><img src=\"../'.DIR_APLICA_CENTRAL.'/imagenes/ajax-loader.gif\"></center></div>" ).dialog({
                    modal: true
                    });

                },
                success: function(data){  
                  $("#tempPop").dialog(\'destroy\').remove();
                  $("#tabs-1").html("");
                  $("#ui-tabs-1").find("table").remove();
                  $("#tabs-1").html(data);
                }
              })
            });


            $("#pestana3").click(function(){
              var parame = "";
                parame += "&fec_inicia="+$("#fec_iniciaID").val()+"&fec_finali="+$("#fec_finaliID").val()+"&cod_transp="+$("#cod_transpID").val();
                parame += "&num_dessat="+$("#num_dessatID").val()+"&num_placa="+$("#num_placaID").val()+"&cod_modali="+$("#cod_modaliID").val();
                parame += "&num_viajex="+$("#num_viajexID").val();
                $.ajax({
                url: "../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getNovedadesNoLimpio"+parame,
                beforeSend: function(){
                  $( "<div id=\"tempPop\"><center><img src=\"../'.DIR_APLICA_CENTRAL.'/imagenes/ajax-loader.gif\"></center></div>" ).dialog({
                    modal: true
                    });

                },
                success: function(data){  
                  $("#tempPop").dialog(\'destroy\').remove();
                  $("#tabs-1").html("");
                  $("#ui-tabs-1").find("table").remove();
                  $("#tabs-1").html(data);
                }
              })
            });
        });

        </script>

        
        ';

    
    $mSelect = "SELECT cod_tipdes, nom_tipdes 
                  FROM ".BASE_DATOS.".tab_genera_tipdes 
                 GROUP BY 1 
                 ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TIPDES = $consulta -> ret_matriz();

    $_NOVEDA = InformViajes::getNovedades();
    
    /************************* FOMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Informe de Operaciones","form\" id=\"formID");
    
    $formulario -> texto( "Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 15, 15, "", $_REQUEST['fec_inicia'] );
    $formulario -> texto( "Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 40, 15, "", $_REQUEST['fec_finali'] ); 
    $formulario -> texto( "No. Despacho( SATT ):", "text", "num_dessat\" id=\"num_dessatID", 0, 15, 15, "", $_REQUEST['num_dessat'] ); 
    $formulario -> texto( "Transportadora:", "text", "Transportadora\"  id=\"TransportadoraID", 1, 40, 100, "","" );
    $formulario -> texto( "Placa:", "text", "num_placa\" id=\"num_placaID", 0, 15, 15, "", "" );
    $formulario -> texto( "Modalidad:", "text", "nom_modali\" id=\"nom_modaliID", 1, 40, 15, "", "" ); 
    $formulario -> texto( "No. Viaje:", "text", "num_viajex\" id=\"num_viajexID", 0, 15, 15, "", "" );


    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar\" id=\"btnPrincipal","",0);
    
    $formulario -> nueva_tabla();
    $formulario -> oculto("cod_transp\" id=\"cod_transpID","",0);
    $formulario -> oculto("cod_modali\" id=\"cod_modaliID","",0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> oculto("standaID\" id=\"standaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("fec_inicia\" id=\"fec_inicia","",0); 
    $formulario -> oculto("cod_transp\" id=\"cod_transp","",0);

    echo '
        <div id="tabs">
      <ul>
        <li><a href="../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getNovedadesTotal" id="pestana1">Total</a></li>
        <li><a href="../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getNovedadesLimpio" id="pestana2">Limpio</a></li>
        <li><a href="../'.DIR_APLICA_CENTRAL.'/inform/ajax_despac_limpio.php?option=getNovedadesNoLimpio" id="pestana3">No Limpio</a></li> 
      </ul>
      <div id="tabs-1">
      </div>
    </div>';

    $formulario -> cerrar();     
    /*************************************************************/
  }

  //Inicio Función getNovedades
  private function getNovedades()
  {
      $mSelect = '(
                    SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                    FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND nom_noveda LIKE "%NER /%" 
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl ="1" AND 
                         ( nom_noveda LIKE "%NEC /%" OR  nom_noveda LIKE "%NICC /%" )
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND 
                          nom_noveda LIKE "%NED /%"
                  ) ';
      $consulta = new Consulta( $mSelect, $this -> conexion );
      return $_NOVEDA = $consulta -> ret_matriz('i');
  }
  //Fin Función getNovedades

  //Inicio Función getDespacNoveda
  private function getDespacNoveda($numDespac)
  {
    $mSql = "(SELECT b.ind_limpio, count(b.cod_noveda) AS cantidad
               FROM ".BASE_DATOS.".tab_despac_noveda a 
         INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                 ON a.cod_noveda = b.cod_noveda 
              WHERE a.num_despac = '$numDespac'
            GROUP BY b.ind_limpio
              )
              
              UNION
              
              (SELECT  b.ind_limpio, count(b.cod_noveda)  AS cantidad
                             FROM ".BASE_DATOS.".tab_despac_contro a 
                       INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                               ON a.cod_noveda = b.cod_noveda 
                            WHERE a.num_despac = '$numDespac'
            GROUP BY b.ind_limpio
              
                )
            ";



    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matrix('i');
  }
  //Fin Función getDespacNoveda

  //Inicio Función exportExcel
  private function exportExcel()
  {
    session_start();
    $archivo = "informe_operaciones".date( "Y_m_d_H_i" ).".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    ob_clean();
    echo $HTML = $_REQUEST[xls_InformViajes];
  }
  //Fin Función exportExcel
}

$_INFORM = new InformViajes( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
<?php

header('Content-Type: text/html; charset=UTF-8');
ini_set('memory_limit', '2048M');
 

class InfCallCenter
{
  var $conexion,
      $cod_aplica,
      $usuario;
  var $cNull = array( array('', '-----') ); 

  function __construct($co = NULL, $us = NULL, $ca = NULL)
  {
    if($_REQUEST["Ajax"] == 'on')
    {
      include_once( "../lib/ajax.inc" );
      $this -> conexion   = $AjaxConnection;
      $this -> usuario    = $_SESSION["datos_usuario"]["cod_usuari"];
      $this -> cod_aplica = $_SESSION["datos_usuario"]["cod_aplica"];
    }
    else
    {
      $this -> conexion = $co;
      $this -> usuario = $us;
      $this -> cod_aplica = $ca;
    }
  
    $this -> principal();
  }
  
  function principal()
  {
    switch($GLOBALS[opcion])
    {
      case 99:
        InfCallCenter::getInform();
      break;

      case 1:
        InfCallCenter::exportExcel();
      break;

      case 2:
        InfCallCenter::LoadCallPlay();
      break;
      
      default:
        InfCallCenter::formulario();
      break;
    }
  }
  function getInform()
  {
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
  
    echo '  <script>

      $(document).ready(function(){
          $("#pContentDiv").find("tr").each(function(){
          $(this).find("td").last().remove();
          });
          $("#pContentDiv").find("th").last().remove(); 

          $("#excelExportID").val($("#pContentDiv").html());
      });
      function exportExcel(){
          try 
          { 
 
              frm_callceID.submit();
          }
          catch(e)
          {
            console.log( "Error Función exportExcel: "+e.message+"\nLine: "+e.lineNumber );
            return false;
          }
        }


        function PlayAudioCall(num_despac, num_consec)
        {
          try
          {
            PopUpJuery("open");
            $.ajax({
              url: "../'.DIR_APLICA_CENTRAL.'/inform/inf_indica_callce.php",
              data: "Ajax=on&opcion=2&num_despac="+num_despac+"&num_consec="+num_consec,
              type: "post",
              success: function(data){
                $("#DialogCallID").html( data );
              }
            });

          }
          catch(e)
          {
            alert(e.message+" - "+e.lineNumber);
          }
        }

        function PopUpJuery(option)
        {
          try
          {
            if(option == "open")
            {
              $("<div id=\'DialogCallID\'><center>Cargando Audio...<br>Por favor espere</center></div>").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                closeOnEscape: false,
                width: "324px",
                height: "100px",
                title: "Reproductor de llamadas",
                open: function(){
                  $("#DialogCallID").css({height: "60px"});
                },
                close: function(){
                  PopUpJuery("close")
                }
              })
            }
            else
            {
              $("#DialogCallID").dialog("destroy").remove();
            }
            
          }
          catch(e)
          {
            alert(e.message+" - "+e.lineNumber);
          }
        }
      </script>';
 

    #Info del Despacho
    $mSelect = "SELECT z.num_despac, a.cod_manifi, a.fec_despac, 
                       b.nom_tipdes, c.nom_paisxx, d.nom_depart, 
                       e.nom_ciudad, f.nom_paisxx, g.nom_depart, 
                       h.nom_ciudad, a.cod_operad, a.fec_citcar, 
                       a.hor_citcar, a.nom_sitcar, a.val_flecon, 
                       a.val_despac, a.val_antici, a.val_retefu, 
                       a.nom_carpag, a.nom_despag, cc.nom_agenci, 
                       a.val_pesoxx, a.obs_despac, a.fec_llegad, 
                       a.obs_llegad, a.ind_planru, j.nom_rutasx, 
                       a.ind_anulad, a.num_poliza, a.con_telef1, 
                       a.con_telmov, a.con_domici, a.gps_operad,
                       a.gps_usuari, a.gps_paswor, k.idx_gpsxxx,  
                       a.ema_client, l.abr_tercer, m.cod_despad, 
                       z.num_solici, i.cod_conduc, i.num_placax,
                       i.num_trayle, o.nom_tipveh, z.num_pedido, 
                       n.ano_modelo, p.nom_marcax, q.nom_lineax, 
                       r.nom_colorx, t.abr_tercer, u.nom_ciudad,
                       n.num_config, v.nom_carroc, n.num_chasis, 
                       n.num_motorx, z.num_soatxx, z.dat_vigsoa, 
                       z.nom_ciasoa, n.num_tarpro, w.num_catlic, 
                       n.cod_tenedo, x.abr_tercer, y.nom_ciudad, 
                       x.dir_domici, a.ind_anulad, '' AS nmEsta, 
                       aa.nom_tiptra, z.cod_instal, bb.nom_produc, 
                       z.ind_modifi, a.ind_anulad, a.num_despac, 
                       z.cod_respon, z.msg_respon
                  FROM ".BASE_DATOS.".tab_despac_despac a 
            INNER JOIN ".BASE_DATOS.".tab_genera_tipdes b 
                    ON a.cod_tipdes = b.cod_tipdes 
            INNER JOIN ".BASE_DATOS.".tab_genera_paises c
                    ON a.cod_paiori = c.cod_paisxx
            INNER JOIN ".BASE_DATOS.".tab_genera_depart d
                    ON a.cod_paiori = d.cod_paisxx
                   AND a.cod_depori = d.cod_depart
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e
                    ON a.cod_paiori = e.cod_paisxx
                   AND a.cod_depori = e.cod_depart
                   AND a.cod_ciuori = e.cod_ciudad
            INNER JOIN ".BASE_DATOS.".tab_genera_paises f
                    ON a.cod_paides = f.cod_paisxx
            INNER JOIN ".BASE_DATOS.".tab_genera_depart g
                    ON a.cod_paides = g.cod_paisxx
                   AND a.cod_depdes = g.cod_depart
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h
                    ON a.cod_paides = h.cod_paisxx
                   AND a.cod_depdes = h.cod_depart
                   AND a.cod_ciudes = h.cod_ciudad
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige i 
                    ON a.num_despac = i.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_genera_rutasx j 
                    ON i.cod_rutasx = j.cod_rutasx 
             LEFT JOIN ".BASE_DATOS.".tab_despac_gpsxxx k 
                    ON a.num_despac = k.num_despac 
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer l
                    ON a.cod_asegur = l.cod_tercer 
             LEFT JOIN ".BASE_DATOS.".tab_consol_despac m 
                    ON a.num_despac = m.cod_deshij 
            INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu n 
                    ON i.num_placax = n.num_placax 
            INNER JOIN ".BASE_DATOS.".tab_genera_tipveh o 
                    ON n.cod_tipveh = o.cod_tipveh
            INNER JOIN ".BASE_DATOS.".tab_genera_marcas p 
                    ON n.cod_marcax = p.cod_marcax 
            INNER JOIN ".BASE_DATOS.".tab_vehige_lineas q 
                    ON n.cod_marcax = q.cod_marcax 
                   AND n.cod_lineax = q.cod_lineax 
            INNER JOIN ".BASE_DATOS.".tab_vehige_colore r 
                    ON n.cod_colorx = r.cod_colorx 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer t 
                    ON i.cod_conduc = t.cod_tercer 
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad u 
                    ON t.cod_paisxx = u.cod_paisxx
                   AND t.cod_depart = u.cod_depart
                   AND t.cod_ciudad = u.cod_ciudad
            INNER JOIN ".BASE_DATOS.".tab_vehige_carroc v 
                    ON n.cod_carroc = v.cod_carroc 
            INNER JOIN ".BASE_DATOS.".tab_tercer_conduc w 
                    ON i.cod_conduc = w.cod_tercer 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer x 
                    ON n.cod_tenedo = x.cod_tercer 
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad y 
                    ON x.cod_paisxx = y.cod_paisxx
                   AND x.cod_depart = y.cod_depart
                   AND x.cod_ciudad = y.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_despac_corona z 
                    ON a.num_despac = z.num_dessat 
             LEFT JOIN ".BASE_DATOS.".tab_genera_tiptra aa 
                    ON z.tip_transp = aa.cod_tiptra 
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc bb
                    ON z.cod_mercan = bb.cod_produc 
            INNER JOIN ".BASE_DATOS.".tab_genera_agenci cc 
                    ON a.cod_agedes = cc.cod_agenci ";

                    # fec_inicia

    $mSelect .= ( $_REQUEST['nom_estado'] != NULL || ($_REQUEST['fec_inicia'] != NULL && $_REQUEST['fec_finalx'] != NULL) ) ? " INNER JOIN ".BASE_DATOS.".tab_despac_callnov zz ON a.num_despac = zz.num_despac " : '';

    $mSelect .= "WHERE i.cod_transp = '".$_REQUEST['cod_transp']."' 
                   AND a.ind_anulad = 'R' ";

    $mSelect .= $_REQUEST['num_dessat'] != NULL ? " AND  a.num_despac = '".$_REQUEST['num_dessat']."'" : '';
    $mSelect .= $_REQUEST['num_manifi'] != NULL ? " AND  a.cod_manifi = '".$_REQUEST['num_manifi']."'" : '';
    $mSelect .= $_REQUEST['num_viajex'] != NULL ? " AND  z.num_despac = '".$_REQUEST['num_viajex']."'" : '';
    $mSelect .= $_REQUEST['num_placax'] != NULL ? " AND  i.num_placax = '".$_REQUEST['num_placax']."'" : '';
    $mSelect .= $_REQUEST['nom_estado'] != NULL ? " AND zz.nom_estado = '".$_REQUEST['nom_estado']."'" : '';
    $mSelect .= ( $_REQUEST['fec_inicia'] != NULL && $_REQUEST['fec_finalx'] != NULL ) ? " AND zz.fec_creaci BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finalx']." 23:59:59' " : "" ;

    $mSelect .= ( $_REQUEST['nom_estado'] != NULL || ($_REQUEST['fec_inicia'] != NULL && $_REQUEST['fec_finalx'] != NULL) ) ? " GROUP BY a.num_despac " : '';
    $mSelect .= " ORDER BY a.fec_creaci DESC "; 
    $mSelect .= $_REQUEST['num_placax'] != NULL ? " LIMIT 0, 10 " : "" ;
     
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mArrayData = $consulta -> ret_matriz('i');
    #Array Cabecera tabla informe
    if($_SESSION["datos_usuario"]["cod_usuari"] == 'soporte') {

    #echo "<pre>"; print_r($mArrayData); echo "</pre>";
    }

    $mArrayTitu = array( "#",
                         "No. Despacho SATT",
                         "Viaje",
                         "Fecha Despacho",
                         "Tipo Despacho",
                         "Nombre Conductor",
                         "Placa"

    );


    $mArrayTituCall = array("#", "ID Llamada", "Telefono", "Duraci&oacute;n", "Observaciones", "Fecha/Hora Llamada", "Conversaci&oacute;n");


    $formulario = new Formulario ( "?", "post", "Informe Indicador Call Center ", "frm_callce\" id=\"frm_callceID");
    
   # echo '<a href="index.php?cod_servic='.$GLOBALS['cod_servic'].'&window=central&opcion=1 "target="centralFrame"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0"></a>';
    echo ' <img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0" onclick="exportExcel();"> ';
    
    $mHtml  = "<table border='1'>";
      if( sizeof( $mArrayData ) > 0 )
      { 
        $mHtml .= "<tr>";
        foreach ($mArrayTitu as $titu) {
          $mHtml .= "<th class=cellHead >".$titu."</th>";
        }
        foreach ($mArrayTituCall as $titu) {
          $mHtml .= "<th class=cellHead >".$titu."</th>";
        }
        $mHtml .= "</tr>";
        
        $i=1;
        foreach ($mArrayData as $row) 
        {
          #Info Call Center
          $mSelect = "SELECT a.cod_consec, a.idx_llamad, a.num_telefo, 
                             a.tie_duraci, a.nom_estado, a.rut_audiox, 
                             a.fec_creaci 
                        FROM ".BASE_DATOS.".tab_despac_callnov a 
                       WHERE a.num_despac = ".$row[71]."
                     ";
          $consulta = new Consulta( $mSelect, $this -> conexion );
          $mArrayCall = $consulta -> ret_matrix('a');

          $mSizeCall = sizeof($mArrayCall);


          if($row[71]){
            $href1 = '<a href="?cod_servic=3302&window=central&despac='.$row[71].'&opcion=1" target="_blank">';
            $href2 = '</a>';
          }else{
            $href1 = '';
            $href2 = '';
          }

 
            
          $mHtml .= "<tr class='row'>";
            $mHtml .= "<td class='cellInfo' align ='center' rowspan='$mSizeCall'>".$i."</td>";
            $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeCall'>".$href1. ( $row[71] != '' ? $row[71] : 'N/A' ) .$href2."&nbsp;</td>";            
            $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeCall'>". $row[0] ."&nbsp;</td>";
            $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeCall'>". $this -> toFecha( $row[2] ) ."&nbsp;</td>";
            $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeCall'>". strtoupper( $row[3] ) ."&nbsp;</td>";
            $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeCall'>". strtoupper( $row[49] ) ."&nbsp;</td>";
            $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeCall'>". strtoupper( $row[41] ) ."&nbsp;</td>";
            
            if($mSizeCall > 0){
              $j=0;
              foreach ($mArrayCall as $rowCall) {
                $mHtml .= $j==0 ? '' : '<tr>';
                  $mHtml .= "<td class='cellInfo' align ='left'>". $rowCall['cod_consec'] ."&nbsp;</td>";
                  $mHtml .= "<td class='cellInfo' align ='left'>". $rowCall['idx_llamad'] ."&nbsp;</td>";
                  $mHtml .= "<td class='cellInfo' align ='left'>". $rowCall['num_telefo'] ."&nbsp;</td>";
                  $mHtml .= "<td class='cellInfo' align ='left'>". $rowCall['tie_duraci'] ."&nbsp;</td>";
                  $mHtml .= "<td class='cellInfo' align ='left'>". $rowCall['nom_estado'] ."&nbsp;</td>";
                  $mHtml .= "<td class='cellInfo' align ='left'>". $rowCall['fec_creaci'] ."&nbsp;</td>";
                  #$mHtml .= "<td class='cellInfo' align ='left'>  <audio controls><source src='http://pbx.intrared.net/".$rowCall['rut_audiox']."' >Your browser does not support the audio element.</audio></td>";
                  $mHtml .= "<td class='cellInfo' align ='left'>   <img src='../".DIR_APLICA_CENTRAL."/imagenes/image_play.gif' style='width: 20px; height:20px; curosr: pointer;' border='0' onclick=\"PlayAudioCall( '".$row[71]."','".$rowCall['cod_consec']."' );\"> </td>";
                 $mHtml .= $j==0 ? '' : '</tr>';
                $j++;
              }
            }else{
              $mHtml .= "<td class='cellInfo' align ='center' colspan='6'>Sin Registro de Llamadas</td>"; 
            }
          $mHtml .= "</tr>";

          $i++;
        }
      }
      else
      {
        $mHtml .= "<tr>";
          $mHtml .= "<th class=cellHead width='3%' >NO SE ENCONTRARON REGISTROS</th>";
        $mHtml .= "</tr>"; 
      }
    $mHtml  .= "</table>";
    
    /*
    echo htmlspecialchars($mHtml);

    echo "<pre>";
    print_r($mHtml);
    echo "</pre>";*/

    
    $mhtmlModded  =  "<div id='pContentDiv' style='display:none'>"; 
    $mhtmlModded .= $mHtml;
    $mhtmlModded .= '</div>';

    $mHtml .= '<input type="hidden" value="" name="excelExport" id="excelExportID">';
    $mHtml .= '<input type="hidden" value="central" name="window">';
    $mHtml .= '<input id="opcionID" type="hidden" value="1" name="opcion">';
    $mHtml .= '<input type="hidden" value="'.$GLOBALS[cod_servic].'" name="cod_servic">';

    echo $mHtml;
    echo $mhtmlModded;

    $formulario -> cerrar();
  }

  function formulario()
  {
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    echo '
      <script>

        function verifiData()
        {
          try 
          {
            var cod_transp = $("#cod_transpID");
            var num_dessat = $("#num_dessatID");
            var num_manifi = $("#num_manifiID");
            var num_viajex = $("#num_viajexID");
            var num_placax = $("#num_placaxID");
            var nom_estado = $("#nom_estadoID");
            var fec_inicia = $("#fec_iniciaID");
            var fec_finalx = $("#fec_finalxID");
            var form = $("#formID");

            if ( !cod_transp.val() ){
              alert("Por Favor Selecione una Transportadora.");
            }else if( nom_estado.val() != "" && !fec_inicia.val() && !fec_finalx.val() ){
              alert("Para Realizar Busquedas por Estado es Necesario Un Rango de Fechas. \nPor Favor Seleccione Un Rango de Fechas.");
            }else if( (!fec_inicia.val() && fec_finalx.val() != "") || (fec_inicia.val() != "" && !fec_finalx.val() != "") ){
              alert("Ha Seleccionado un Parametro de busqueda Tipo Fecha \nPor Favor Seleccione el Otro Parametro de Fecha para Realizar la Busqueda.");
            }else if( fec_inicia.val() != "" && fec_finalx.val() != "" && fec_inicia.val() > fec_finalx.val() ){
              alert("La Fecha Inicial es Mayor a la Fecha Final.\nPor Favor Corregir Seleccion.");
            }else if( !num_dessat.val() && !num_manifi.val() && !num_viajex.val() && !num_placax.val() && !nom_estado.val() && !fec_inicia.val()&& !fec_finalx.val() ){
              alert("Por Favor Selecione Algun Parametro de Busqueda.");
            }else{
              form.submit();
            }
          }
          catch(e)
          {
            console.log( "Error Función verifiData: "+e.message+"\nLine: "+Error.lineNumber );
            return false;
          }
        }

        jQuery(function($) 
        {
          $( "#fec_iniciaID, #fec_finalxID" ).datepicker({
            changeMonth: true,
            changeYear: true
          });

          $.mask.definitions["A"]="[12]";
          $.mask.definitions["M"]="[01]";
          $.mask.definitions["D"]="[0123]";

          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";

          $( "#fec_iniciaID, #fec_finalxID" ).mask("Annn-Mn-Dn");
        });

        /*function exportExcel(){
          try 
          {
              $("#pContentDiv").find("tr").each(function(){
                  $(this).find("td").last().remove();
              });
          }
          catch(e)
          {
            console.log( "Error Función export: "+e.message+"\nLine: "+Error.lineNumber );
            return false;
          }
        }*/

      </script>';

    $mSelect = "SELECT a.cod_tercer, b.abr_tercer 
                  FROM ".BASE_DATOS.".tab_tercer_activi a 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b 
                    ON a.cod_tercer = b.cod_tercer 
                 WHERE a.cod_activi = '1' ";

      if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) 
      {#PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
        if ( $filtro -> listar( $this -> conexion ) ) : 
          $datos_filtro = $filtro -> retornar();
          $mSelect .= " AND b.cod_tercer = '".$datos_filtro['clv_filtro']."' ";
        endif;
      }else{#PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
        if ( $filtro -> listar( $this -> conexion ) ) : 
          $datos_filtro = $filtro -> retornar();
          $mSelect .= " AND b.cod_tercer = '".$datos_filtro['clv_filtro']."' ";
        endif;
      }
    $mSelect .= " ORDER BY b.abr_tercer ASC ";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mArrayTransp = $consulta -> ret_matrix('i');

    $mArrayEstado = InfCallCenter::getEstadoLlamada();

    $formulario = new Formulario ("index.php","post","Informe Indicador Call Center","form\" id=\"formID");

    $formulario -> nueva_tabla();
    $formulario -> lista ("Transportadora:","cod_transp\" id=\"cod_transpID",array_merge($this -> cNull, $mArrayTransp ),1 );

    $formulario -> nueva_tabla();
    $formulario -> texto( "No. Despacho( SATT ):", "text", "num_dessat\" id=\"num_dessatID", 0, 15, 15, "" );
    $formulario -> texto( "No. Manifiesto:", "text", "num_manifi\" id=\"num_manifiID", 1, 15, 15, "" );
    
    $formulario -> texto( "No. Viaje:", "text", "num_viajex\" id=\"num_viajexID", 0, 15, 15, "" );
    $formulario -> texto( "Placa:", "text", "num_placax\" id=\"num_placaxID", 1, 15, 15, "" );

    $formulario -> lista ("Estado:","nom_estado\" id=\"nom_estadoID",array_merge($this -> cNull, $mArrayEstado ),1 );

    $formulario -> texto( "Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "" );
    $formulario -> texto( "Fecha Final:", "text", "fec_finalx\" readonly id=\"fec_finalxID", 1, 10, 10, "" );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","verifiData();",0);
    
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> cerrar();
  }

  function getEstadoLlamada()
  {
    $mSelect = "SELECT a.nom_estado 
                  FROM ".BASE_DATOS.".tab_despac_callnov a 
              GROUP BY a.nom_estado 
              ORDER BY a.nom_estado DESC 
               ";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mArrayEstado = $consulta -> ret_matrix('i');

    $mDiccionarioEst = array( 'CHANUNAVAIL' => 'Error del Sistema', 'CONGESTION' => 'Congesti&oacute;n', 'NOANSWER' => 'No Contesto', 'BUSY' => 'Ocupado', 'ANSWER' => 'Contesto', 'CANCEL' => 'Cancelado' );

    $i=0;
    foreach ($mArrayEstado as $row ) {
      $mValue = $mDiccionarioEst[$row[0]];
      $mArrayResult[$i][0] = $row[0];
      $mArrayResult[$i][1] = $mValue == NULL ? $row[0] : $mValue;
      $i++;
    }

    return $mArrayResult;
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

  //Inicio Función exportExcel
  private function exportExcel()
  {
    session_start();
    $archivo = "informe_CallCenter".date( "Y_m_d_H_i" ).".xls";
    header('Content-type: application/vnd.ms-excel');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    ob_clean();
    echo $HTML = $_REQUEST['excelExport']; 
  }
  //Fin Función exportExcel 

  private function LoadCallPlay()
  {
      $mSelect = "SELECT a.cod_consec, a.idx_llamad, a.num_telefo, 
                         a.tie_duraci, a.nom_estado, a.rut_audiox, 
                         a.fec_creaci, a.num_despac
                    FROM ".BASE_DATOS.".tab_despac_callnov a 
                   WHERE a.num_despac = ".$_REQUEST["num_despac"]." AND a.cod_consec = ".$_REQUEST["num_consec"]."
                 ";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $mDataCall = $consulta -> ret_matrix('a');
      
      # Funcionalidad con S3-------------------------------
      @include_once("../lib/InterfS3Amazon.inc");
      $mS3 = new InterfS3Amazon($this -> conexion , $mDataCall[0], "CallCenter");
      $mAudio = $mS3 -> getAudioLoaded();
          
      if($mAudio["cod_respon"] != '1000') {
        $mObjetAudio = $mAudio["msg_respon"];
      }
      else {
         # Crea el elemento de reproduccion del audio que se descargó de S3 -----------------------------------------
         
         $mSSL = $_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://';
         $mURL = $mSSL.$_SERVER["HTTP_HOST"]."/ap/".DIR_APLICA_CENTRAL.$mAudio["fil_audio"];
         $mObjetAudio = '<audio controls> <source src="'.$mURL/*$mAudio["dat_audio"]*/.'" type="audio/wav">
                        Su navegador no soporta elementos de Audio
                      </audio>';
      }
      echo $mObjetAudio;
  }
  //Fin Función exportExcel
}

if($_REQUEST["Ajax"] == 'on')
  $_INFORM = new InfCallCenter(  );
else
  $_INFORM = new InfCallCenter( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
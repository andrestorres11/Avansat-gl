<?php

//-----------------------------------
//@modificado: Alejandro Ortegon
//             Christiam Barrera
//             Gelber Andros Alarcon
//@date: 2009-09-30
//-----------------------------------
//-----------------------------------
//@modificado: Alexander Correa
//@date: 2015-12-09
//-----------------------------------
session_start();
class FacturFaro 
{
    var $conexion,
        $usuario,
        $cod_aplica;

    private static $cNotPerfil = "7",
                   $cNitCorona = "860068121";

    function __construct($conexion, $us, $ca) 
    {
        $this->conexion = $conexion;
        $this -> usuario = $us;
        $this -> cod_aplica = $ca;
        $this -> cod_filtro = $cf;

        switch($_POST[opcion])
        {
            case "1":
                $this->listar();
                break;
            case "2":
                $this->facturar();
                break;
            case "3":
                $this->exportExcel();
                break;
            default:
                $this->filtro();
                break;
        }
    }

    private function getTransp( $cod_transp )
    {
      $mSql = "SELECT cod_tercer, UPPER(abr_tercer) AS nom_tercer FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$cod_transp."' LIMIT 1";
      $consulta = new Consulta( $mSql, $this -> conexion );
      return $consulta -> ret_matriz();
    }
    
    private function VerifyTranspor()
    {

      if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) {
        //--------------------------
        //@PARA EL FILTRO DE EMPRESA
        //--------------------------
        $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
        if ( $filtro -> listar( $this -> conexion ) ) : 
        $datos_filtro = $filtro -> retornar();
        endif;
      }
      else { 
        //--------------------------
        //@PARA EL FILTRO DE EMPRESA
        //--------------------------
        $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
        if ( $filtro -> listar( $this -> conexion ) ){ 
          $datos_filtro = $filtro -> retornar();
        }
      }
      return $datos_filtro;
    }


    function filtro()
    {
        $empre = array();
        $inicio[0][0]= "";
        $inicio[0][1]= "-";
        
        $add = '';
        $filter = $this -> VerifyTranspor();
        if( sizeof( $filter ) > 0 )
        { 
          $TRANSP = $this -> getTransp( $filter['clv_filtro'] );
          $add = " AND a.cod_tercer = '".$TRANSP[0][0]."' ";
        }

        $query = "SELECT a.cod_tercer,a.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer 
                     AND b.cod_activi = '1' ".$add."
                ORDER BY 2 ";
        $consulta = new Consulta($query, $this -> conexion);
        $transp = $consulta -> ret_matriz(); 
        $transpor = array_merge($inicio,$transp);
        
        include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
        echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/facfaro.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        echo '
            <script>
                jQuery(function($) { 
                    $( "#feciniID,#fecfinID" ).datepicker();      
                    $.mask.definitions["A"]="[12]";
                    $.mask.definitions["M"]="[01]";
                    $.mask.definitions["D"]="[0123]";
                    $.mask.definitions["n"]="[0123456789]";
                    $( "#feciniID,#fecfinID" ).mask("Annn-Mn-Dn");
                })
                
                function SetAgencia( cod_empres )
                {
                  var Standa = $("#StandaID").val();
                  $.ajax({
                    type: "POST",
                    url: "../" + Standa + "/factur/ajax_factur_faroxx.php",
                    data: "standa="+Standa+"&option=SetAgencia&cod_empres="+cod_empres,
                    success: 
                      function( datos )
                      {
                        $("#cod_agenciID").html( datos );
                      }
                  });
                }
                
            </script>';
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Facturacion Faro", "formulario");
        $feactual = date("Y-m-d");
        $formulario -> nueva_tabla();

        echo "<tr><td align='right'  class='celda_titulo'>";
        echo "Fecha Inicial de Salida: &nbsp;";
        echo "</td>";
        echo '<td class="celda_info">';
        echo "<input type='text' class='campo' size='10' id='feciniID' name='fecini' value='".$fecactual."'> ";
        echo "</td>";
        echo "<td align='right' class='celda_titulo'>";
        echo "Fecha Final de Salida: &nbsp;";
        echo "</td>";
        echo '<td class="celda_info">';
        echo "<input type='text' class='campo' size='10' id='fecfinID' name='fecfin' value='".fecactual."'> ";
        echo "</td></tr>";

        echo "<tr><td align='right' class='celda_titulo'>";
        echo "Nit. Transportadora: &nbsp; (Separar por ',')";
        echo "</td>";
        echo '<td class="celda_info">';
        echo "<input type='text' class='campo' size='24' id='nit_transportID' name='nit_transport' > ";
        echo "</td>";

        $formulario -> lista("Transportadora:","transp\" onchange=\"SetAgencia( $(this).val() )\" id=\"transpID",$transpor,1);	
        $formulario -> lista("Agencia:","cod_agenci\" id=\"cod_agenciID",$inicio,1);	

        $formulario -> nueva_tabla();
        echo "<input type='button' value='Aceptar' name='Aceptar' onclick=\"aceptar_lis();\" class='bot'>";

        $formulario -> nueva_tabla();
        $formulario -> oculto("num_despac",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
        $formulario -> oculto("opcion\" id=\"opcion",1,0);
        $formulario -> oculto("Standa\" id=\"StandaID", DIR_APLICA_CENTRAL, 0);

        $formulario -> cerrar();
    }
  
    function listar()
    { 
        ini_set('memory_limit','128M');

        $inicio[0][0]= "";
        $inicio[0][1]= "-";

        $query = "SELECT a.cod_tercer,a.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer 
                     AND b.cod_activi = '1'
                ORDER BY 2 ";
        $consulta = new Consulta($query, $this -> conexion);
        $transp = $consulta -> ret_matriz(); 
        $transpor = array_merge($inicio,$transp);
        
        if( $_REQUEST['transp'] )
        {
          $mSelect = "SELECT a.cod_agenci, b.nom_agenci
                  FROM ".BASE_DATOS.".tab_transp_agenci a, 
                       ".BASE_DATOS.".tab_genera_agenci b
                 WHERE a.cod_agenci = b.cod_agenci
                   AND a.cod_transp = '".$_REQUEST['transp']."'
                 ORDER BY 2";
                 
          $consulta = new Consulta( $mSelect, $this -> conexion );
          $agencias = $consulta -> ret_matriz();
        }

        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.table2excel.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/facfaro.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";

        echo '
            <script>
                jQuery(function($) { 
                    $( "#feciniID,#fecfinID" ).datepicker();      
                    $.mask.definitions["A"]="[12]";
                    $.mask.definitions["M"]="[01]";
                    $.mask.definitions["D"]="[0123]";
                    $.mask.definitions["n"]="[0123456789]";
                    $( "#feciniID,#fecfinID" ).mask("Annn-Mn-Dn");

                })
                
                function SetAgencia( cod_empres )
                {
                  var Standa = $("#StandaID").val();
                  $.ajax({
                    type: "POST",
                    url: "../" + Standa + "/factur/ajax_factur_faroxx.php",
                    data: "standa="+Standa+"&option=SetAgencia&cod_empres="+cod_empres,
                    success: 
                      function( datos )
                      {
                        $("#cod_agenciID").html( datos );
                      }
                  });
                }
            </script>';

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Facturacion Faro", "formulario\" id=\"formulario" );
        //$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "LISTAR FACTURACION FARO", "formulario");

        $feactual = date("Y-m-d");

        $formulario -> nueva_tabla();
        echo "<tr><td align='right'  class='celda_titulo'>";
                echo "Fecha Inicial de Salida: &nbsp;";
            echo "</td>";
            echo '<td class="celda_info">';
                echo "<input type='text' class='campo' size='10' id='feciniID' name='fecini' value='".$_REQUEST[fecini]."'> ";
            echo "</td>";
            echo "<td align='right' class='celda_titulo'>";
                echo "Fecha Final de Salida: &nbsp;";
            echo "</td>";
            echo '<td class="celda_info">';
                echo "<input type='text' class='campo' size='10' id='fecfinID' name='fecfin' value='".$_REQUEST[fecfin]."'> ";
        echo "</td></tr>";

        echo "<tr><td align='right' class='celda_titulo'>";
                echo "Nit. Transportadora: &nbsp; (Separar por ',')";
            echo "</td>";
            echo '<td class="celda_info">';
                echo "<input type='text' class='campo' size='24' id='nit_transportID' name='nit_transport' value='".$_REQUEST[nit_transport]."' > ";
            echo "</td>";

        // $formulario -> lista("Transportadora:","transp\" id=\"transpID",$transpor,1);
        $formulario -> lista("Transportadora:","transp\" onchange=\"SetAgencia( $(this).val() )\" id=\"transpID",$transpor,1);
        echo '<input type="hidden" id="nameFileID" name="nameFile" value="informe_FacturacionFaro">
              <input type="hidden" id="opcionID" name="opcion" value="">
              <input type="hidden" id="exportExcelID" name="exportExcel" value="">';
        $formulario -> lista("Agencia:","cod_agenci\" id=\"cod_agenciID",array_merge($inicio,$agencias),1);
        $formulario -> nueva_tabla();
 

        echo "<input type='button' value='Aceptar' name='Aceptar' onclick=\"aceptar_lis();\" class='bot'>";
        echo "<input type='button' value='Excel' name='excel' onclick=\"exportarExcel();\" class='bot'>"; 
 

        echo '<script>
                document.getElementById("transpID").value='.$_REQUEST["transp"].';
              </script>';
        if($_REQUEST["cod_agenci"])
        {
          echo '<script>
                document.getElementById("cod_agenciID").value='.$_REQUEST["cod_agenci"].';
              </script>';
        }

        $cod_transp = array();
        foreach(explode(',',$_REQUEST[nit_transport]) as $row)
        {
            $cod_transp[] = (int)trim($row);
        }
        $cod_transp = join(', ',$cod_transp);

        //busqueda de los despachos de faro
        $query  = "SELECT a.num_despac AS 'DESPACHO',
                          a.fec_salida AS 'FECHA DE SALIDA',  
                          d.nom_ciudad AS 'CIUDAD ORIGEN',
                          e.nom_ciudad AS 'CIUDAD DESTINO',
                          a.cod_manifi AS 'MANIFIESTO',
                          c.abr_tercer AS 'EMPRESA',
                          a.fec_llegad AS 'FECHA DE LLEGADA' ,
                          b.num_placax AS 'PLACA',
                          f.abr_tercer AS 'CONDUCTOR',
                          f.cod_tercer AS 'CEDULA',
                          f.num_telmov AS 'TELEFONO',
                          c.cod_tercer,
                          d.cod_ciudad AS 'cod_ori',
                          e.cod_ciudad AS 'cod_des',
                          z.nom_agenci AS 'agenci'
                     FROM ".BASE_DATOS.".tab_despac_despac a, 
                          ".BASE_DATOS.".tab_despac_vehige b,
                          ".BASE_DATOS.".tab_genera_agenci z,
                          ".BASE_DATOS.".tab_tercer_tercer c,
                          ".BASE_DATOS.".tab_genera_ciudad d, 
                          ".BASE_DATOS.".tab_genera_ciudad e, 
                          ".BASE_DATOS.".tab_tercer_tercer f,
                          ".BASE_DATOS.".tab_genera_rutasx g 
                    WHERE a.num_despac = b.num_despac 
                      AND a.fec_salida IS NOT NULL 
                      AND b.cod_transp = c.cod_tercer 
                      AND b.cod_agenci = z.cod_agenci 
                      AND b.cod_rutasx = g.cod_rutasx 
                      AND g.cod_ciuori = d.cod_ciudad 
                      AND g.cod_ciudes = e.cod_ciudad 
                      AND f.cod_tercer = b.cod_conduc 
                      AND f.cod_tercer = b.cod_conduc 
                      AND a.num_despac NOT IN (SELECT j.num_despac 
                                                 FROM ".CENTRAL.".tab_factur_factur j 
                                                WHERE j.num_despac = a.num_despac 
                                                  AND c.cod_tercer = j.cod_tercer) 
                      
                      AND b.ind_activo = 'S' 
                      AND a.fec_llegad BETWEEN '".$_REQUEST["fecini"]." 00:00:00' AND '".$_REQUEST["fecfin"]." 23:59:59' 
                      ";
        /* Consulta 2
        AND a.fec_llegad >= '".$_REQUEST["fecini"]." 00:00:00' 
        AND a.fec_llegad <= '".$_REQUEST["fecfin"]." 23:59:59' 
        AND (a.cod_conult ='9999' OR a.fec_llegad IS NOT NULL) 
        */
        /* consulta original
        AND b.fec_creaci >= '".$_REQUEST["fecini"]." 00:00:00' 
        AND b.fec_creaci <= '".$_REQUEST["fecfin"]." 23:59:59' 
        */

        if($_REQUEST["transp"])
            $query.= " AND b.cod_transp = '".$_REQUEST["transp"]."'";
            
        if($_REQUEST["cod_agenci"])
            $query.= " AND b.cod_agenci = '".$_REQUEST["cod_agenci"]."'";
        
        if(!empty($_REQUEST[nit_transport]))
            $query.= " AND b.cod_transp IN(".$cod_transp.") ";

        $query.= " GROUP BY 1 ORDER BY b.cod_transp ";

        $consulta = new Consulta($query, $this -> conexion);
        $facturas = $consulta -> ret_matriz();

        $factur=array();
        $tot=array();
        $x=0;
        $j=0;
        $totnov =0;
        
        for($i = 0; $i < sizeof($facturas); $i++)
        {
            $perfiles="'7','8','73','74','84','1','713'";
            $sql ="(SELECT b.usr_creaci, b.cod_noveda
                      FROM ".BASE_DATOS.".tab_genera_usuari a,
                           ".BASE_DATOS.".tab_despac_contro b
                     WHERE b.num_despac = ".$facturas[$i][0]." 
                       AND a.cod_usuari = b.usr_creaci 
                       AND a.cod_perfil IN (".$perfiles.")
                       ".( self::$cNitCorona == $_REQUEST['transp'] ? " AND a.cod_perfil NOT IN (".self::$cNotPerfil.") " : "" )."
                   ) 
                   UNION ALL
                   (SELECT b.usr_creaci, b.cod_noveda
                      FROM ".BASE_DATOS.".tab_genera_usuari a,
                           ".BASE_DATOS.".tab_despac_noveda b,
                           ".BASE_DATOS.".tab_genera_contro c
                     WHERE b.num_despac = ".$facturas[$i][0]." 
                       AND a.cod_usuari = b.usr_creaci 
                       AND b.cod_contro = c.cod_contro 
                       /* AND IF(c.nom_contro <> 'LUGAR ENTREGA',c.ind_virtua = '1',c.ind_virtua = '0') */
                       AND a.cod_perfil IN (".$perfiles.")
                       ".( self::$cNitCorona == $_REQUEST['transp'] ? " AND a.cod_perfil NOT IN (".self::$cNotPerfil.") " : "" )."
                    )
                  ";

            $consulta = new Consulta($sql, $this -> conexion);
            $novedades = $consulta -> ret_matriz();

            $nov = sizeof($novedades);
            $empresa = $this->VerifyTranspor();
            if($empresa != "860068121" && $_REQUEST["transp"] != '860068121'){
              $no_com =array(52,70);
            }else{              
              $no_com =array(63,325,296,311);
            }
            
            $cuenta = 0;
            foreach ($novedades as $key => $value) {
              if(in_array($value['cod_noveda'], $no_com)){
                $cuenta ++;
              }
             } 
    
            if($nov && $nov>=1)
            {
              $_ESFERAS = $this -> getEsferas( $facturas[$i][0] );
 
              

                $factur[$j][0]=$facturas[$i][0];
                $factur[$j][1]=$facturas[$i][1];
                $factur[$j][2]=$facturas[$i][2];
                $factur[$j][3]=$facturas[$i][3];
                $factur[$j][4]=$facturas[$i][4];
                $factur[$j][5]=$facturas[$i][5];
                $factur[$j][6]=$facturas[$i][6];
                $factur[$j][7]=$facturas[$i][7];
                $factur[$j][8]=$facturas[$i][8];
                $factur[$j][9]=$facturas[$i][9];
                $factur[$j][10]='SI';
                $factur[$j][11]=$facturas[$i][5];
                $factur[$j][12]=$facturas[$i][10];
                $factur[$j][13]=$facturas[$i][11];
                $factur[$j][14]=$facturas[$i][12];
                $factur[$j][15]=$facturas[$i][13];
                $factur[$j]['agenci']=$facturas[$i]['agenci'];
                $factur[$j][16]=$nov;
                $factur[$j][19]=$cuenta;
                $factur[$j][20]=($nov-$cuenta);
                $factur[$j][17]=(int)$_ESFERAS[0] > 0 ? $_ESFERAS[0] : 'N/A';
                $factur[$j][18]=(int)$_ESFERAS[1] > 0 ? $_ESFERAS[1] : 'N/A';
                $totnov = $totnov + $nov;
                $j++;
                $x++;
            }
        }

        
        $c=0;
        $tot[0][0]=$x;
        $tot[0][1]= $facturas[0]['EMPRESA'];
        $tot[0][2]=$totnov;
        echo "</table><div id='mamafoko'>";
        $formulario -> nueva_tabla();
        $formulario -> linea("Numero Total de Despachos entre ".$_REQUEST["fecini"]." hasta el ".$_REQUEST["fecfin"]."(".sizeof($factur).")",1,"t2");
        $formulario -> nueva_tabla();
        $exp .= "url=".NOM_URL_APLICA."&db=".BASE_DATOS."&fecini=".$_REQUEST["fecini"]."&fecfin=".$_REQUEST["fecfin"]."";
        /*$formulario -> imagen("Exportar","../".DIR_APLICA_CENTRAL."/imagenes/excel.jpg","Exportar",30,30,0,"onClick=\"top.window.open('../".DIR_APLICA_CENTRAL."/export/exp_factur_faro.php?".$exp."')\"",0,0,"");
        *///$formulario -> botoni("Facturar","aceptar()",1);
 
        echo '</table><div id="tableExcelFacturasFaro"> <table width="100%" cellspacing="0" cellpadding="4" id="tableExcel" class="formulario">';
        //$formulario -> nueva_tabla();
        $formulario -> linea("#",0,"t2");
        $formulario -> linea("Despacho SATT",0,"t2");
        $formulario -> linea("Agencia",0,"t2");
        $formulario -> linea("Manifiesto",0,"t2");
        $formulario -> linea("No. Viaje",0,"t2");
        $formulario -> linea("Tipo Despacho",0,"t2");
        $formulario -> linea("Tipo Transportadora",0,"t2");
        $formulario -> linea("Poseedor",0,"t2");
        $formulario -> linea("Ciudad Origen",0,"t2");
        $formulario -> linea("Ciudad Destino",0,"t2");
        $formulario -> linea("Placa",0,"t2");
        $formulario -> linea("Conductor",0,"t2");
        $formulario -> linea("Cedula",0,"t2");
        $formulario -> linea("Celular",0,"t2");
        $formulario -> linea("Seguimiento Faro",0,"t2");
        $formulario -> linea("Fecha de Salida",0,"t2");
        $formulario -> linea("Fecha de Llegada",0,"t2");
        $formulario -> linea("Diferencia",0,"t2");
        $formulario -> linea("Empresa Transportadora",0,"t2");
        $formulario -> linea("Generador",0,"t2");
        $formulario -> linea("No Comunicaciones",0,"t2");
        $formulario -> linea("Otras Novedades",0,"t2");
        $formulario -> linea("Novedades",0,"t2");
        $formulario -> linea("No. EAL Registradas",0,"t2");
        $formulario -> linea("No. EAL Cumplidas",1,"t2");

        $comp = $factur[0][5];
        $j=0;
        $totNovedad = 0;


        #Inicio Ciclo Registros
        for($i = 0; $i < sizeof($factur); $i++)
        {
            if(!strcmp($factur[$i][5], $comp)==0)
            {
                $formulario -> nueva_tabla();
                $formulario -> linea("Empresa",0,"t2");
                $formulario -> linea("Total Despachos",0,"t2");
                $formulario -> linea("Total Novedades",1,"t2");

                $formulario -> linea($factur[$i-1][5],0,"t1");
                $formulario -> linea($j,0,"t1");
                $formulario -> linea($totNovedad,1,"t1");

                $formulario -> nueva_tabla();

                $j = ($i < sizeof($factur) ? 0 : $j);
                $totNovedad = ($i < sizeof($factur) ? 0 : $totNovedad );
                $comp = $factur[$i][5];

                $formulario -> linea("#",0,"t2");
                $formulario -> linea("Despacho SATT",0,"t2");
                $formulario -> linea("Agencia",0,"t2");
                $formulario -> linea("Manifiesto",0,"t2");
                $formulario -> linea("No. Viaje",0,"t2");
                $formulario -> linea("Tipo Despacho",0,"t2");
                $formulario -> linea("Tipo Transportadora",0,"t2");
                $formulario -> linea("Poseedor",0,"t2");
                $formulario -> linea("Ciudad Origen",0,"t2");
                $formulario -> linea("Ciudad Destino",0,"t2");
                $formulario -> linea("Placa",0,"t2");
                $formulario -> linea("Conductor",0,"t2");
                $formulario -> linea("Cedula",0,"t2");
                $formulario -> linea("Celular",0,"t2");
                $formulario -> linea("Seguimiento Faro",0,"t2");
                $formulario -> linea("Fecha de Salida",0,"t2");
                $formulario -> linea("Fecha de Llegada",0,"t2");
                $formulario -> linea("Diferencia",0,"t2");
                $formulario -> linea("Empresa Transportadora",0,"t2");
                $formulario -> linea("Generador",0,"t2");
                $formulario -> linea("No Comunicaciones",0,"t2");
                $formulario -> linea("Otras Novedades",0,"t2");
                $formulario -> linea("Novedades",0,"t2");
                $formulario -> linea("No. EAL Registradas",0,"t2");
                $formulario -> linea("No. EAL Cumplidas",1,"t2");
            }
            
            $mSelect = "SELECT a.num_despac, a.cod_tipdes, a.tip_transp,
                               a.nom_poseed, b.nom_tipdes
                          FROM ".BASE_DATOS.".tab_despac_corona a
                     LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes b
                            ON a.cod_tipdes = b.cod_tipdes
                         WHERE num_dessat = '".$factur[$i][0]."'";
            
            $consulta = new Consulta($mSelect, $this -> conexion);
            $_DESCOR = $consulta -> ret_matriz();

            $formulario -> linea(($j+1),0,"t1");
            #$formulario -> linea('<a class="classLink" target="blanck" href="index.php?cod_servic=3302&window=central&despac='.$factur[$i][0].'&opcion=1" style="">'.$factur[$i][0].'</a>',0,"t1");
            $formulario -> linea($factur[$i][0],0,"t1");
            $formulario -> linea($factur[$i]['agenci'],0,"t1");
            $formulario -> linea($factur[$i][4],0,"t1");

            $formulario -> linea( $_DESCOR[0]['num_despac'] != '' ? '<a class="classLink" target="blanck" href="index.php?cod_servic=3302&window=central&despac='.$factur[$i][0].'&opcion=1" style="">'.$_DESCOR[0]['num_despac'].'</a>' : 'N/A', 0, "t1");
            #$formulario -> linea( $_DESCOR[0]['num_despac'] != '' ? $_DESCOR[0]['num_despac'] : 'N/A', 0, "t1");
            $formulario -> linea( $_DESCOR[0]['nom_tipdes'] != '' ? $_DESCOR[0]['nom_tipdes'] : 'N/A', 0, "t1");
            
            if( $_DESCOR[0]['tip_transp'] == '1' )
            $mTipdes = 'Flota Propia';
            elseif( $_DESCOR[0]['tip_transp'] == '2' )
            $mTipdes = 'Terceros';
            elseif( $_DESCOR[0]['tip_transp'] == '3' )
            $mTipdes = 'Empresas';
            else
            $mTipdes = 'N/A';
            
            $formulario -> linea( $mTipdes, 0, "t1");
            $formulario -> linea( $_DESCOR[0]['nom_poseed'] != '' ? $_DESCOR[0]['nom_poseed'] : 'N/A', 0, "t1");
            $formulario -> linea($factur[$i][2],0,"t1");
            $formulario -> linea($factur[$i][3],0,"t1");
            $formulario -> linea($factur[$i][7],0,"t1");
            $formulario -> linea($factur[$i][8],0,"t1");
            $formulario -> linea($factur[$i][9],0,"t1");
            $formulario -> linea($factur[$i][12],0,"t1");
            $formulario -> linea($factur[$i][10],0,"t1");
            $formulario -> linea($factur[$i][1],0,"t1");
            $formulario -> linea($factur[$i][6],0,"t1");

            $time_inicio = strtotime( $factur[$i][1] );
            $time_fin = strtotime( $factur[$i][6] );

            $diff = (int)abs( round( ( $time_fin - $time_inicio ) / 60 ) );
            $formulario -> linea($diff.' Min(s)',0,"t1");
            
            $formulario -> linea($factur[$i][11],0,"t1");
            $formulario -> linea($factur[$i][5],0,"t1");
            $formulario -> linea(($factur[$i][19])+0,0,"t1");
            $formulario -> linea($factur[$i][20],0,"t1");
            $formulario -> linea($factur[$i][16],0,"t1");
            $formulario -> linea($factur[$i][17],0,"t1");
            $formulario -> linea($factur[$i][18],1,"t1");
            
            /*
            $_ESFERAS = $this -> getEsferas( $factur[$i][0] );
            $formulario -> linea( ( (int)$_ESFERAS[0] > 0 ? $_ESFERAS[0] : 'N/A' ),0,"t1");
            $formulario -> linea( ( (int)$_ESFERAS[1] > 0 ? $_ESFERAS[1] : 'N/A' ),1,"t1");
            */

            $totNovedad += $factur[$i][16];
            $j++;
        }
        echo "</div>";
       
        #Fin Ciclo Registros
        //echo "<br><br>";
        $formulario -> nueva_tabla();
        $formulario -> linea("Empresa",0,"t2");
        $formulario -> linea("Total Despachos",0,"t2");
        $formulario -> linea("Total Novedades",1,"t2");
        //for($i = 0; $i < sizeof($tot); $i++)
        //{
        $formulario -> linea($factur[$i-1][5],0,"t1");
        $formulario -> linea($j,0,"t1");
        $formulario -> linea($totNovedad,1,"t1");
        //}
  
        $_SESSION['tot'] = $tot[0];
        $_SESSION[tarifa]='';

        if(sizeof($factur)>0)
        {
            //facturacion comienza aca
            $sql ="SELECT cod_tarifa, val_minimo, DATE_FORMAT(a.fec_inifac,'%Y-%m-%d') ,
                          DATE_FORMAT(a.fec_finfac,'%Y-%m-%d') , tip_tarifa
                     FROM ".BASE_DATOS.".tab_transp_tarifa a 
                    WHERE ind_estado =1";

            if($_REQUEST["transp"])
                $sql.= " AND cod_tercer = '".$_REQUEST["transp"]."'";

            if(!empty($_REQUEST[nit_transport]))
                $sql.= " AND cod_tercer IN(".$cod_transp.") ";

            $consulta = new Consulta($sql, $this -> conexion);
            $tarifa = $consulta -> ret_matriz();

            if(!$tarifa)
            {
                $formulario -> linea("NO HAY FACTURACION ACTIVA PARA LA EMPRESA",0,"t1");
            }
            else
            {
                $formulario -> nueva_tabla();
                $valida=true;
                $formulario -> linea("Fechas De Tarifas: ".$tarifa[0][2]." al ".$tarifa[0][3],1,"t1");
                if($_REQUEST["fecini"]<$tarifa[0][2] )
                {
                    $formulario -> linea("La Fecha Inicial de Facturacion es Menor a la Fecha Inicial de la Tarifa",1,"t1");
                    $valida=false;
                }
                if($_REQUEST["fecini"]>$tarifa[0][3] )
                {
                    $formulario -> linea("La Fecha Inicial de Facturacion es Mayor a la Fecha Final de la Tarifa",1,"t1");
                    $valida=false;
                }
                if($_REQUEST["fecfin"]<$tarifa[0][2] )
                {
                    $formulario -> linea("La Fecha Final de Facturacion es Menor a la Fecha Inicial de la Tarifa",1,"t1");
                    $valida=false;
                }
                if($_REQUEST["fecfin"]>$tarifa[0][3] )
                {
                    $formulario -> linea("La Fecha Final de Facturacion es Mayor a la Fecha Final de la Tarifa",1,"t1");
                    $valida=false;
                }
                if($valida)
                {
                    $_SESSION[tarifa]= $tarifa[0];
                    if($tarifa[0][4]=='D')
                    {
                        $formulario -> linea("Tipo de Tarifa: Despachos",1,"t1");
                        $formulario -> linea("Valor Minimo : $".number_format($tarifa[0][1],0),1,"t1");
                        
                        $sql ="SELECT val_tarifa,cod_consec
                                 FROM ".BASE_DATOS.".tab_transp_tardell 
                                WHERE cod_tarifa = '".$tarifa[0][0]."' ";
                        $consulta = new Consulta($sql, $this -> conexion);
                        $val = $consulta -> ret_matriz();

                        $total = $tot[0][0] * $val[0][0];
                        $formulario -> linea("Valor Por Despacho : $".number_format($val[0][0],0),1,"t1");
                        if($tarifa[0][1]!='')
                            if($tarifa[0][1]>=$total)
                                $total = $tarifa[0][1]; 
                        $formulario -> linea("Valor a Cobrar : $".number_format($total, 0),1,"t1");
                        $_SESSION['total']= $total;
                        $_SESSION['val'] = $val[0];
                        $_SESSION[consec] = $val[0][1];
                    }
                    else
                    {
                        if($tarifa[0][4]=='N')
                        {
                            $formulario -> linea("Tipo de Tarifa: Novedades",1,"t1");
                            $formulario -> linea("Valor Minimo : $".number_format($tarifa[0][1],0),1,"t1");
                            $sql ="SELECT can_minimo, can_maximo, val_tarifa,
                                          cod_consec
                                     FROM ".BASE_DATOS.".tab_transp_tardell 
                                    WHERE cod_tarifa = '".$tarifa[0][0]."' ";
                            $consulta = new Consulta($sql, $this -> conexion);
                            $val = $consulta -> ret_matriz();

                            $j ='';
                            $i = 0;
                            foreach($val AS $rangos)
                            {
                                if((int)$tot[0][2]<=(int)$rangos[1])
                                {
                                    $j = $i;
                                    break;
                                }
                                $i++;
                            }
                            if($j == '')
                                $j = sizeof($val)-1;
                            $total = $tot[0][2] * $val[$j][2];
                            $formulario -> linea("Configuracion de Rangos de Novedades: ".$val[$j][0]." hasta ".$val[$j][1],1,"t1");
                            $formulario -> linea("Valor Por Novedad : $".number_format($val[$j][2],0),1,"t1");
                            if($tarifa[0][1]!='')
                                if($tarifa[0][1]>=$total)
                                    $total = $tarifa[0][1]; 
                            $formulario -> linea("Valor a Cobrar : $".number_format($total, 0),1,"t1");
                            $_SESSION['val'] = $val[$j];
                            $_SESSION['total']= $total;
                            $_SESSION[consec] = $val[$j][3];
                        }
                    }
                }
            }
        }
        $_SESSION[factur] = "";
        $_SESSION[factur] = $factur;
      
        $formulario -> nueva_tabla();

        /*$formulario -> oculto("fecfin",$_REQUEST["fecfin"],0);
        $formulario -> oculto("fecini",$_REQUEST["fecini"],0);*/
        //$formulario -> oculto("transp",$_REQUEST["transp"],0);
        $formulario -> oculto("num_despac",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
        //$formulario -> oculto("opcion\" id=\"opcion",1,0);
        $formulario -> oculto("Standa\" id=\"StandaID", DIR_APLICA_CENTRAL, 0);
        
        $formulario -> cerrar();
        echo '<script></script>';

 
    }

    function getEsferas( $num_despac )
    {
      $to = array();
      $mSelect = "SELECT cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_vehige
                   WHERE num_despac = '".$num_despac."'";
      
      $consulta = new Consulta($mSelect, $this -> conexion);
      $rutasx = $consulta -> ret_matriz();
      
      $cod_rutasx = $rutasx[0]['cod_rutasx'];

      $mSelect = "SELECT b.cod_contro, b.nom_contro
                    FROM ".BASE_DATOS.".tab_genera_rutcon a,
                         ".BASE_DATOS.".tab_genera_contro b
                   WHERE a.cod_contro  = b.cod_contro
                     AND a.cod_rutasx = '".$cod_rutasx."' 
                     /* AND b.nom_contro LIKE '%E@L%' */ 
                     AND b.ind_virtua = '0' ";

      $consulta = new Consulta($mSelect, $this -> conexion);
      $ealxxx = $consulta -> ret_matriz();
      $to[0] = sizeof($ealxxx);

      if( sizeof($ealxxx) > 0 )
      {
        $mData = '';
        foreach( $ealxxx as $row )
          $mData .= $mData != '' ? ', '.$row['cod_contro'] : $row['cod_contro'];

        $mSelect = "SELECT num_despac, cod_contro 
                      FROM ".BASE_DATOS.".tab_despac_noveda
                     WHERE num_despac = '".$num_despac."' 
                       AND cod_contro IN( ".$mData." ) ";

        $consulta = new Consulta($mSelect, $this -> conexion);
        $ealxxx = $consulta -> ret_matriz();
        $to[1] = sizeof($ealxxx);  
      }else
        $to[1] = '0';  
     
      return $to;
    }

    function Facturar()
    {
        session_start();
        $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");
        $tarifa = $_SESSION[tarifa];
        $consec = $_SESSION[consec];	 
        for($i = 0; $i < sizeof($_SESSION[factur]); $i++)
        {
            $query = "INSERT INTO ".CENTRAL.".tab_factur_factur
                                 (num_despac, cod_tercer, fec_despac,
                                  cod_ciuori, cod_ciudes, cod_tarifa, 
                                  cod_consec, usr_creaci,fec_creaci)
                          VALUES ('".$_SESSION[factur][$i][0]."', '".$_SESSION[factur][$i][13]."', '".$_SESSION[factur][$i][1]."',
                                  '".$_SESSION[factur][$i][14]."', '".$_SESSION[factur][$i][15]."', '".$tarifa."', 
                                  '".$consec."', '".$_SESSION[datos_usuario][cod_usuari]."', NOW())";
            $insercion = new Consulta($query, $this -> conexion,"R");
        }
        $consulta = new Consulta("SELECT NOW()", $this -> conexion,"RC");
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Se Marcaron Como Facturados los Despacho desde el ".$_REQUEST["fecini"]." hasta el ".$_REQUEST["fecfin"];
    }//FIN FUNCTION CAPTURA

    //Inicio Función exportExcel
    private function exportExcel()
    {
      $archivo = "informe_FacturacionFaro_".date( "Y_m_d_H_i" ).".xls";
      #header('Content-Type: application/vnd.ms-excel'); // This should work for IE & Opera
      header('Content-type: application/x-msexcel'); // This should work for the rest 
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("content-disposition: attachment; filename=".$archivo.".xls");

      ob_clean();
      echo  $_REQUEST['exportExcel'];  
    }
}
//$service = new FacturFaro($this->conexion);
$service = new FacturFaro($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);
?>

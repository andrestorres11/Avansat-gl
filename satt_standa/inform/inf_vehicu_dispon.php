<?php
/*! \file: inf_vehicu_dispon.php
 *  \brief: informe vehiculos disponibles, se actualizo la clase
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 07/02/2017
 *  \bug: 
 *  \warning: 
 */

//ini_set('display_errors', true);
//error_reporting(E_ALL & ~E_NOTICE);
class Proc_despac
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 /*! \fn: principal
   *  \brief: funcion principal que direcciona la solicitud
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function principal()
 {
  //$this -> Buscar();
  if(!isset($_REQUEST['excel']))
    $this -> Buscar();
  else
  {
    $this -> exportExcel();
  }
 }

  /*! \fn: Buscar
   *  \brief: busca las ciudades y pinta los fitros
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function Buscar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $fec_actual = date("Y/m/d");

  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  $query = "SELECT j.cod_ciudad,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4))
              FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_vehicu_vehicu i,
                   ".BASE_DATOS.".tab_genera_ciudad j,
                   ".BASE_DATOS.".tab_genera_depart k,
                   ".BASE_DATOS.".tab_genera_paises l
             WHERE a.cod_ciudes = j.cod_ciudad AND
                   j.cod_depart = k.cod_depart AND
                   j.cod_paisxx = k.cod_paisxx AND
                   k.cod_paisxx = l.cod_paisxx AND
                   a.num_despac = d.num_despac AND
                   i.num_placax = d.num_placax AND
                   a.fec_salida Is Not Null AND
                   a.fec_salida <= NOW() AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S'
	   ";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

  $query = $query." GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $ciudes = $consulta -> ret_matriz();

  $ciudes = array_merge($inicio,$ciudes);

  echo $this->GridStyle();
  $mHtml = new Formlib(2);
  $mHtml->SetJs("min");
  $mHtml->SetJs("jquery");
  $mHtml->SetJs("functions");
  $mHtml->SetJs("es");
  $mHtml->SetJs("time");
  $mHtml->SetCssJq("jquery");
      $mHtml->Table("tr");
        $mHtml->SetBody('<td>');
            $mHtml->SetBody('<div id="formBuscarID" class="ui-tabs ui-widget ui-widget-content ui-corner-all">');
              $mHtml->SetBody("<h3 style='padding:6px;' class='ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top'><center>VEHICULOS DISPONIBLES</center></h3>");
                $mHtml->OpenDiv("id:sec2ID");
                  $mHtml->SetBody('<form name="form_insert" method="post" action="index.php?window=central&cod_servic=1382&menant=1382">');
                    $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
                    $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>"central"));
                    $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>"1"));
                    $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST["cod_servic"]));
                    $mHtml->Hidden(array( "name" => "valorSelect", "id" => "valorSelectID", 'value'=>$_REQUEST["ciudes"]));
                    $mHtml->Hidden(array( "name" => "valorSelectCon", "id" => "valorSelectConID", 'value'=>$_REQUEST["config"]));
                    $mHtml->Table("tr");
                        $mHtml->Row();
                            $mHtml->Label( "Ciudad Destino",  array("align"=>"right", "class"=>"celda_titulo") );
                            $mHtml->Select2 ($ciudes,  array("name" => "ciudes", "width" => "25%") );
                        $mHtml->CloseRow();
                        $mHtml->Row();
                          $mHtml->Label( "Fecha:", array("align"=>"right", "width"=>"25%") );
                          $mHtml->Input( array("name"=>"fecbus", "id"=>"fecbusID", "width"=>"25%", "value"=>((isset($_REQUEST['fecbus']))?$_REQUEST['fecbus']:date('Y-m-j')) ) );
                        $mHtml->CloseRow();
                        $mHtml->Row();
                            $mHtml->Label( "Configuracion",  array("align"=>"right", "class"=>"celda_titulo") );
                            $mHtml->Select2 ($this->getConfig(),  array("name" => "config", "width" => "25%") );
                        $mHtml->CloseRow();
                         $mHtml->Row();
                          $mHtml->Button( array("value"=>"Buscar", "id"=>"buscarID","name"=>"buscar", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"2" ,"onclick"=>"Buscar()") );
                        $mHtml->CloseRow();
                    $mHtml->CloseTable("tr");
                  $mHtml->SetBody('</form>');
                  $mHtml->OpenDiv("id:tabs");
                    $mHtml->SetBody("<ul>
                                        <li><a href='#tabInform' onclick='getInform()'>INFORME</a></li>
                                    </ul>");
                    $mHtml->OpenDiv("id:tabInform");
                      switch($_REQUEST['opcion'])
                      {
                        case "1":
                          $mHtml->SetBody($this -> Listar());
                          break;
                      }
                    $mHtml->CloseDiv();
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            $mHtml->SetBody('</div>');
      $mHtml->CloseTable('tr');
    $mHtml->SetBody('<script>
                        $(document).ready(function() 
                        {
                          $("#fecbusID").datepicker({
                              changeMonth: true,
                              changeYear: true,
                              dateFormat: "yy-mm-dd",
                          });

                          valorSelect=$("#valorSelectID").val();
                          valorSelectCon=$("#valorSelectConID").val();
                          $("#ciudesID option[value="+ valorSelect +"]").attr("selected",true);
                          $("#configID option[value="+ valorSelectCon +"]").attr("selected",true);

                        });

                        $(function() {
                          $("#tabs").tabs();
                        } );

                        function ExportExcel(campo)
                        {
                          try
                          {   
                            var ciudes = $("#ciudesID").val();
                            var fecbus = $("#fecbusID").val();
                            var config = $("#configID").val();
                            window.open("index.php?window=central&cod_servic=1382&menant=1382&excel="+campo+"&ciudes="+ciudes+"&fecbus="+fecbus+"&config="+config);
                          }
                          catch(e)
                          {
                            alert("Error en ExportExcelCara: "+e.message+"\nLine:"+e.lineNumber);
                          }
                        } 

                        function Buscar()
                        {
                          try
                          {   
                            var standa = $("#standaID").val();
                            $("#popID").remove();
                            closePopUp("popID");
                            LoadPopupJQNoButton("open", "", 200, 200, false, false, true);
                            var popup = $("#popID");
                            //popup.html("<table align="center"><tr><td><img src="../" + standa + "/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>"");
                            popup.parent().children().children(".ui-dialog-titlebar-close").hide();
                            popup.html("<h3>Buscando.....</h3>")
                            form_insert.submit();
                          }
                          catch(e)
                          {
                            alert("Error en Buscar: "+e.message+"\nLine:"+e.lineNumber);
                          }
                        }                        
                      </script>');
    echo $mHtml->MakeHtml();
 }

 /*! \fn: Listar
   *  \brief: pinta los resultados obtenidos de los filtros
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function Listar()
 {
  
  $query=$this->getDesruta();
  $consulta = new Consulta($query, $this -> conexion);
  $desruta = $consulta -> ret_matriz();

  $query=$this->getDesllega();
  $consulta = new Consulta($query, $this -> conexion);
  $desllega = $consulta -> ret_matriz();


  $mHtml = new Formlib(2, "yes",TRUE);
  $mHtml->OpenDiv("id:btnVehiculosPla");
    $mHtml->Table("tr");
      $mHtml->Label( sizeof($desruta)." Vehiculo(s) en Ruta Con Llegada Planeada Desde ".$_REQUEST['fecbus']." Hasta ".$fechaadic."", array("colspan"=>"11", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
      $mHtml->CloseRow();
      $mHtml->Row();
        $mHtml->Label( "Exportar a excel",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Button( array("value"=>"EXCEL", "id"=>"ExportExcelID","name"=>"ExportExcel", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"8","onclick"=>"ExportExcel(1)") );
      $mHtml->CloseRow();
    $mHtml->CloseTable('tr');
  $mHtml->CloseDiv();
  $mHtml->OpenDiv("id:respVehiculosPla");
    $mHtml->Table("tr");
      //$mHtml->Row();
        $mHtml->Label( "Poseedor",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Tipo de Transportadora",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Placa",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Tipo Vehiculo",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Origen",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Destino",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Conductor",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Celular",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Telefono",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Llegada Planeada",  array("align"=>"right", "class"=>"celda_titulo") );
      $mHtml->CloseRow();
      for($i = 0; $i < sizeof($desruta); $i++)
      {
        $mHtml->Row();
        $mHtml->Label( utf8_encode($desruta[$i][7]),  array("align"=>"right", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][8],  array("align"=>"center", "class"=>"CellInfo1") );
        $mHtml->Label( $desruta[$i][0],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][9],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][1],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][2],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][3],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][4],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][5],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desruta[$i][6],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->CloseRow();
      }
    $mHtml->CloseTable('tr');
  $mHtml->CloseDiv();
  $mHtml->OpenDiv("id:btnVehiculosLle");
    $mHtml->Table("tr");
      $mHtml->Label( sizeof($desllega)." Vehiculo(s) Con Llegada Desde ".$fechadism." Hasta ".$_REQUEST['fecbus']."", array("colspan"=>"11", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
      $mHtml->CloseRow();
      $mHtml->Row();
        $mHtml->Label( "Exportar a excel",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Button( array("value"=>"EXCEL", "id"=>"ExportExcelID","name"=>"ExportExcel", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"8","onclick"=>"ExportExcel(2)") );
      $mHtml->CloseRow();
    $mHtml->CloseTable('tr');
  $mHtml->CloseDiv();
  $mHtml->OpenDiv("id:respVehiculosLle");
    $mHtml->Table("tr");
      //$mHtml->Row();
        $mHtml->Label( "Poseedor",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Tipo de Transportadora",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Placa",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Tipo Vehiculo",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Origen",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Destino",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Conductor",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Celular",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Telefono",  array("align"=>"right", "class"=>"celda_titulo") );
        $mHtml->Label( "Llegada",  array("align"=>"right", "class"=>"celda_titulo") );
      $mHtml->CloseRow();
      for($i = 0; $i < sizeof($desllega); $i++)
      {
        $mHtml->Row();
        $mHtml->Label( utf8_encode($desllega[$i][7]),  array("align"=>"right", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][8],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][0],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][9],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][1],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][2],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][3],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][4],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][5],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->Label( $desllega[$i][6],  array("align"=>"center", "class"=>"cellInfo1") );
        $mHtml->CloseRow();
      }
    $mHtml->CloseTable('tr');
  $mHtml->CloseDiv();
  return $mHtml->MakeHtml();
 }

 /*! \fn: getDesruta
   *  \brief: prepara la consulta para los vehiculos en ruta
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function getDesruta()
 {
    $datos_usuario = $this -> usuario -> retornar();

    $_REQUEST['fecbus'] = str_replace("/","-",$_REQUEST['fecbus']);

    $fechaadic = date("Y-m-d", strtotime("".$_REQUEST['fecbus']." +5 day"));
    $fechadism = date("Y-m-d", strtotime("".$_REQUEST['fecbus']." -5 day"));

    $query = "SELECT d.num_placax,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4)),
                   CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),') - ',LEFT(o.nom_paisxx,4)),
                   e.abr_tercer,e.num_telmov,e.num_telef1,d.fec_llegpl,p.abr_tercer, 
                   (IF(q.tip_transp IS NUll OR q.tip_transp = '','N/A',IF(q.tip_transp='1','Flota Propia',IF(q.tip_transp='2','Terceros',IF(q.tip_transp='3','Empresa','N/A'))) ) ) tip_transp,  IF(q.tip_vehicu IS NUll OR q.tip_vehicu = '' ,'-',q.tip_vehicu) tip_vehicu
                FROM ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN ".BASE_DATOS.".tab_despac_vehige d ON a.num_despac = d.num_despac
          INNER JOIN (
                              SELECT dd.num_despac, dd.num_placax, MAX(dd.fec_llegpl)  
                              FROM ".BASE_DATOS.".tab_despac_despac aa
                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige dd ON dd.num_despac = aa.num_despac
                        INNER  JOIN (
                                      SELECT ccc.num_despac, ccc.cod_contro
                                      FROM ".BASE_DATOS.".tab_despac_seguim ccc
                                      INNER JOIN (
                                                     SELECT  ss.num_despac, ss.cod_contro, ss.fec_planea, ss.fec_alarma, ss.ind_estado,  COUNT(ss.num_despac) AS conteo
                                                      FROM 
                                                           ".BASE_DATOS.".tab_despac_despac aaa INNER JOIN
                                                           ".BASE_DATOS.".tab_despac_vehige bb ON aaa.num_despac = bb.num_despac AND 
                                                                                             bb.cod_transp = '860068121' AND 
                                                                                             aaa.fec_salida IS NOT NULL AND 
                                                                                             aaa.fec_llegad IS NULL AND 
                                                                                             aaa.ind_planru = 'S' AND  
                                                                                             aaa.ind_anulad = 'R' AND 
                                                                                             bb.ind_activo = 'S' INNER JOIN
                                                           ".BASE_DATOS.".tab_despac_seguim ss ON bb.num_despac = ss.num_despac AND 
                                                                                             ss.fec_planea BETWEEN '".$_REQUEST['fecbus']." 00:00:00' AND '".$fechaadic." 23:59:59' AND 
                                                                                             ss.ind_estado = 1
                                                      GROUP BY ss.num_despac
                                                      HAVING conteo <= 2
                                        )s ON s.num_despac = ccc.num_despac 
                                        AND ccc.fec_planea BETWEEN '".$_REQUEST['fecbus']." 00:00:00' AND '".$fechaadic." 23:59:59'
                                    )cc ON cc.num_despac = aa.num_despac
                              WHERE  aa.fec_salida IS NOT NULL AND 
                                     aa.fec_llegad IS NULL AND 
                                     aa.fec_salida <= NOW() AND
                                     aa.ind_anulad = 'R' AND 
                                     aa.ind_planru = 'S'
                              GROUP BY dd.num_placax 
                      ) r ON r.num_placax = d.num_placax AND r.num_despac = a.num_despac
          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e ON d.cod_conduc = e.cod_tercer
          INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu i ON i.num_placax = d.num_placax
          INNER JOIN ".BASE_DATOS.".tab_genera_ciudad j ON a.cod_ciuori = j.cod_ciudad
          INNER JOIN ".BASE_DATOS.".tab_genera_depart k ON j.cod_depart = k.cod_depart AND j.cod_paisxx = k.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_genera_paises l ON k.cod_paisxx = l.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_genera_ciudad m ON a.cod_ciudes = m.cod_ciudad
          INNER JOIN ".BASE_DATOS.".tab_genera_depart n ON m.cod_depart = n.cod_depart AND m.cod_paisxx = n.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_genera_paises o ON  n.cod_paisxx = o.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer p ON i.cod_propie = p.cod_tercer 
           LEFT JOIN ".BASE_DATOS.".tab_despac_corona q ON a.num_despac = q.num_dessat
               WHERE a.fec_salida IS NOT NULL 
               AND a.fec_llegad IS NULL 
               AND a.fec_salida <= NOW() 
               AND a.ind_anulad = 'R' 
               AND a.ind_planru = 'S' 
               ";

    if($_REQUEST['ciudes']){
      $query .= " AND a.cod_ciudes = ".$_REQUEST['ciudes']."";
    }
    if($_REQUEST['config']){
      $query .= " AND q.tip_vehicu LIKE '$_REQUEST[config]' ";
    }
    if($_REQUEST['fecbus']){
     $query .= " AND d.fec_llegpl BETWEEN '".$_REQUEST['fecbus']." 00:00:00' AND '".$fechaadic." 23:59:59'";
    }
    if($datos_usuario["cod_perfil"] == "")
    {
     //PARA EL FILTRO DE CONDUCTOR
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE PROPIETARIO
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE POSEEDOR
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE ASEGURADORA
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DEL CLIENTE
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE LA AGENCIA
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }
    else
    {
     //PARA EL FILTRO DE CONDUCTOR
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE PROPIETARIO
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE POSEEDOR
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE ASEGURADORA
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DEL CLIENTE
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE LA AGENCIA
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }

    $query = $query." GROUP BY 1";

    return $query;

 }
 
  /*! \fn: getDesllega
   *  \brief: prepara la consult para los vehiculos con llegada estimada
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function getDesllega()
 {

    $datos_usuario = $this -> usuario -> retornar();

    $_REQUEST['fecbus'] = str_replace("/","-",$_REQUEST['fecbus']);

    $fechaadic = date("Y-m-d", strtotime("".$_REQUEST['fecbus']." +5 day"));
    $fechadism = date("Y-m-d", strtotime("".$_REQUEST['fecbus']." -5 day"));
     $query = "SELECT d.num_placax,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4)),
                   CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),') - ',LEFT(o.nom_paisxx,4)),
                   e.abr_tercer,e.num_telmov,e.num_telef1,d.fec_llegpl,p.abr_tercer, 
                   (IF(q.tip_transp IS NUll OR q.tip_transp = '','N/A',IF(q.tip_transp='1','Flota Propia',IF(q.tip_transp='2','Terceros',IF(q.tip_transp='3','Empresa','N/A'))) ) ) tip_transp,  IF(q.tip_vehicu IS NUll OR q.tip_vehicu = '' ,'-',q.tip_vehicu) tip_vehicu
                FROM ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN ".BASE_DATOS.".tab_despac_vehige d ON a.num_despac = d.num_despac
          INNER JOIN (
                              SELECT dd.num_despac, dd.num_placax, MAX(dd.fec_llegpl)  
                              FROM ".BASE_DATOS.".tab_despac_despac aa
                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige dd ON dd.num_despac = aa.num_despac
                              WHERE aa.fec_salida Is Not Null AND 
                                    aa.fec_llegad Is not Null AND 
                                    aa.ind_anulad = 'R' AND 
                                    aa.ind_planru = 'S'
                              GROUP BY dd.num_placax 
                      ) r ON r.num_placax = d.num_placax AND r.num_despac = a.num_despac
          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e ON d.cod_conduc = e.cod_tercer
          INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu i ON i.num_placax = d.num_placax
          INNER JOIN ".BASE_DATOS.".tab_genera_ciudad j ON a.cod_ciuori = j.cod_ciudad
          INNER JOIN ".BASE_DATOS.".tab_genera_depart k ON j.cod_depart = k.cod_depart AND j.cod_paisxx = k.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_genera_paises l ON k.cod_paisxx = l.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_genera_ciudad m ON a.cod_ciudes = m.cod_ciudad
          INNER JOIN ".BASE_DATOS.".tab_genera_depart n ON m.cod_depart = n.cod_depart AND m.cod_paisxx = n.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_genera_paises o ON  n.cod_paisxx = o.cod_paisxx
          INNER JOIN ".BASE_DATOS.".tab_tercer_tercer p ON i.cod_propie = p.cod_tercer 
           LEFT JOIN ".BASE_DATOS.".tab_despac_corona q ON a.num_despac = q.num_dessat
                WHERE a.fec_salida Is Not Null 
                AND a.fec_llegad Is not Null 
                AND a.ind_anulad = 'R' 
                AND a.ind_planru = 'S'
       ";


    if($_REQUEST['ciudes'])
     $query .= " AND a.cod_ciudes = ".$_REQUEST['ciudes']."";
    if($_REQUEST['config'])
     {
      $query .= " AND q.tip_vehicu LIKE '$_REQUEST[config]' ";
     }
    if($_REQUEST['fecbus'])
     $query .= " AND a.fec_llegad BETWEEN '".$fechadism." 00:00:00' AND '".$_REQUEST['fecbus']." 23:59:59'";
    if($datos_usuario["cod_perfil"] == "")
    {
     //PARA EL FILTRO DE CONDUCTOR
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE PROPIETARIO
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE POSEEDOR
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE ASEGURADORA
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DEL CLIENTE
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE LA AGENCIA
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }
    else
    {
     //PARA EL FILTRO DE CONDUCTOR
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE PROPIETARIO
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE POSEEDOR
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE ASEGURADORA
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DEL CLIENTE
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
     }
     //PARA EL FILTRO DE LA AGENCIA
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }
    $query = $query." GROUP BY 1";

    return $query;
 }

  /*! \fn: getConfig
   *  \brief: optine las configuraciones existentes
   *  \author: Edward Serrano
   *  \date:  08/02/2017
   *  \date modified: dia/mes/año
   */
 function getConfig()
 {
    $inicio[0][0] = 0;
    $inicio[0][1] = "-";
    $query = "SELECT cod_homolo AS cod, cod_homolo AS nom FROM ".BASE_DATOS.".tab_homolo_config";
    $consulta = new Consulta($query, $this -> conexion);
    $resulsql = $consulta -> ret_matriz();
    $resulsql = array_merge($inicio,$resulsql);
    return $resulsql;

 }
  /*! \fn: Datos
   *  \brief: cabezera
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $mRuta = array("link"=>0, "finali"=>0, "opcurban"=>0, "lleg"=>NULL, "tie_ultnov"=>NULL);#Fabian
   $listado_prin = new Despachos($_REQUEST['cod_servic'],2,$this -> aplica,$this -> conexion);
   $listado_prin  -> Encabezado($_REQUEST['despac'],$datos_usuario,0,$mRuta);
   #$listado_prin  -> PlanDeRuta($_REQUEST[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$_REQUEST['despac'],0);
   $formulario -> oculto("opcion",$_REQUEST['opcion'],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);

   $formulario -> cerrar();
 }

  /*! \fn: GridStyle
   *  \brief: estilos adicionales para el nuevo framework
   *  \author: Edward Serrano
   *  \date:  07/02/2017
   *  \date modified: dia/mes/año
   */
 function GridStyle()
  {
      echo "<style>
              .cellth-ltb{
                   background: #E7E7E7;
                   border-left: 1px solid #999999; 
                   border-bottom: 1px solid #999999; 
                   border-top: 1px solid #999999;
              }
              .cellth-lb{
                   background: #E7E7E7;
                   border-left: 1px solid #999999; 
                   border-bottom: 1px solid #999999; 
              }
              .cellth-b{
                   background: #E7E7E7;
                   border-bottom: 1px solid #999999; 
              }
              .cellth-tb{
                   background: #E7E7E7;
                   border-bottom: 1px solid #999999; 
                   border-top: 1px solid #999999;
              }
              .celltd-ltb{
                   border-left: 1px solid #999999; 
                   border-bottom: 1px solid #999999; 
                   border-top: 1px solid #999999;
              }
              .celltd-tb{
                   border-bottom: 1px solid #999999; 
                   border-top: 1px solid #999999;
              }
              .celltd-lb{
                   border-bottom: 1px solid #999999; 
                   border-left: 1px solid #999999;
              }
              .celltd-l{
                   border-left: 1px solid #999999;
              }
              .fontbold{
                  font-weight: bold;
              }
              .divGrilla{
                  margin: 0;
                  padding: 0;
                  border: none;
                  border-top: 1px solid #999999;
                  border-bottom: 1px solid #999999;
              }
              .CellHead {
                  background-color: #35650f;
                  color: #ffffff;
                  font-family: Times New Roman;
                  font-size: 11px;
                  padding: 4px;
              }
              .cellInfo1 {
                  background-color: #ebf8e2;
                  font-family: Times New Roman;
                  font-size: 11px;
                  padding: 2px;
              }
              .campo_texto {
                  background-color: #ffffff;
                  border: 1px solid #bababa;
                  color: #000000;
                  font-family: Times New Roman;
                  font-size: 11px;
                  padding-left: 5px;
              }
            </style>";
  }

  /*! \fn: exportExcel
   *  \brief: genera el archivo de excel a exportar
   *  \author: Edward Serrano
   *  \date:  05/01/2017
   *  \date modified: dia/mes/año
   */
  function exportExcel()
  {
    $titulos = array('Poseedor' => 'Poseedor'
                    ,'TipoTrans'=>'Tipo de Transportadora'
                    ,'Placa'=>'Placa'
                    ,'TipoVehi'=>'Tipo Vehiculo'
                    ,'Origen'=>'Origen'
                    ,'Destino'=>'Destino'
                    ,'Conductor'=>'Conductor'
                    ,'Celular'=>'Celular'
                    ,'Telefono'=>'Telefono'
                    ,'Llegada'=>'Llegada'
                    );
    $reslt= "<table>";
    $reslt.="<tr>";
    foreach ($titulos as $key => $value)
    {
      $reslt.="<td>".$value.(($key=='Llegada' AND $_REQUEST['excel']==1)?' Planeada':'')."</td>";
    }
    $reslt.="</tr>";

    $query=(($_REQUEST['excel']==1)?$this->getDesruta():$this->getDesllega());
    $consulta = new Consulta($query, $this -> conexion);
    $resulsql = $consulta -> ret_matriz();
   // echo "<pre>";print_r($resulsql);echo "</pre>";
    for($i = 0; $i < sizeof($resulsql); $i++)
    {
      $reslt.="<tr>";
        $reslt.="<td>".utf8_encode($resulsql[$i][7])."</td>";
        $reslt.="<td>".$resulsql[$i][8]."</td>";
        $reslt.="<td>".$resulsql[$i][0]."</td>";
        $reslt.="<td>".$resulsql[$i][9]."</td>";
        $reslt.="<td>".$resulsql[$i][1]."</td>";
        $reslt.="<td>".$resulsql[$i][2]."</td>";
        $reslt.="<td>".$resulsql[$i][3]."</td>";
        $reslt.="<td>".$resulsql[$i][4]."</td>";
        $reslt.="<td>".$resulsql[$i][5]."</td>";
        $reslt.="<td>".$resulsql[$i][6]."</td>";
      $reslt.="</tr>";
    }
    header("Content-Type: application/vnd.ms-excel");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename=Vehiculo_Disponibles_".date('Y-m-j').".xls");
  
    ob_clean();
    ob_end_clean();
    $reslt.= "</table>";
    echo $reslt;
  }

}

$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>

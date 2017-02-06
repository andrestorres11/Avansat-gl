<?php

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

 function principal()
 {
  if(!isset($_REQUEST[opcion]))
    $this -> Buscar();
  else
  {
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Listar();
          break;
      }
  }
 }

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

  /*$formulario = new Formulario ("index.php","post","VEHICULOS DISPONIBLES","form_insert","","");
  $formulario -> linea("Ingrese los Criterios de Busqueda",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> lista("Ciudad Destino","ciudes",$ciudes,1);

  $formulario -> nueva_tabla();
  $formulario -> fecha_calendar("Fecha","fecbus","form_insert",$fec_actual,"yyyy/mm/dd",1);

  $formulario -> nueva_tabla();
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
  $formulario -> botoni("Buscar","form_insert.submit()",1);
  $formulario -> cerrar();*/
  $this->GridStyle();
  $mHtml = new Formlib(2);
  $mHtml->SetJs("min");
  $mHtml->SetJs("jquery");
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
                    $mHtml->Table("tr");
                        $mHtml->Row();
                            $mHtml->Label( "Ciudad Destino",  array("align"=>"right", "class"=>"celda_titulo") );
                            $mHtml->Select2 ($ciudes,  array("name" => "ciudes", "width" => "25%") );
                        $mHtml->CloseRow();
                        $mHtml->Row();
                          $mHtml->Label( "Fecha:", array("align"=>"right", "width"=>"25%") );
                          $mHtml->Input( array("name"=>"fecbus", "id"=>"fecbusID", "width"=>"25%", "value"=>date('Y-m-j') ) );
                        $mHtml->CloseRow();
                        $mHtml->Row();
                          $mHtml->Button( array("value"=>"Buscar", "id"=>"buscarID","name"=>"buscar", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"2" ,"onclick"=>"form_insert.submit()") );
                        $mHtml->CloseRow();
                    $mHtml->CloseTable("tr");
                  $mHtml->SetBody('</form>');
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
                        });
                        $(function() {
                          $("#tabs").tabs();
                        } );
                        
                      </script>');
    echo $mHtml->MakeHtml();

 }

 function Listar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $_REQUEST[fecbus] = str_replace("/","-",$_REQUEST[fecbus]);

  $fechaadic = date("Y-m-d", strtotime("".$_REQUEST[fecbus]." +5 day"));
  $fechadism = date("Y-m-d", strtotime("".$_REQUEST[fecbus]." -5 day"));

  $query = "SELECT d.num_placax,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4)),
                 CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),') - ',LEFT(o.nom_paisxx,4)),
                 e.abr_tercer,e.num_telmov,e.num_telef1,d.fec_llegpl,p.abr_tercer, 
                 IF( q.tip_transp IS NULL OR q.tip_transp = '', 'N/A', q.tip_transp ) tip_transp,  IF(q.tip_vehicu IS NUll OR q.tip_vehicu = '' ,'-',q.tip_vehicu) tip_vehicu
              FROM ".BASE_DATOS.".tab_despac_despac a
        INNER JOIN ".BASE_DATOS.".tab_despac_vehige d ON a.num_despac = d.num_despac
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
             AND a.fec_llegad Is Null 
             AND a.fec_salida <= NOW() 
             AND a.ind_anulad = 'R' 
             AND a.ind_planru = 'S' ";

  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
  if($_REQUEST[fecbus])
   $query .= " AND d.fec_llegpl BETWEEN '".$_REQUEST[fecbus]." 00:00:00' AND '".$fechaadic." 23:59:59'";

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

  $consulta = new Consulta($query, $this -> conexion);
  $desruta = $consulta -> ret_matriz();

  $query = "SELECT d.num_placax,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4)),
                 CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),') - ',LEFT(o.nom_paisxx,4)),
                 e.abr_tercer,e.num_telmov,e.num_telef1,d.fec_llegpl,p.abr_tercer, 
                 IF( q.tip_transp IS NULL OR q.tip_transp = '', 'N/A', q.tip_transp ) tip_transp,  IF(q.tip_vehicu IS NUll OR q.tip_vehicu = '' ,'-',q.tip_vehicu) tip_vehicu
              FROM ".BASE_DATOS.".tab_despac_despac a
        INNER JOIN ".BASE_DATOS.".tab_despac_vehige d ON a.num_despac = d.num_despac
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


  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
  if($_REQUEST[fecbus])
   $query .= " AND a.fec_llegad BETWEEN '".$fechadism." 00:00:00' AND '".$_REQUEST[fecbus]." 23:59:59'";

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
  $consulta = new Consulta($query, $this -> conexion);
  $desllega = $consulta -> ret_matriz();
  $formulario = new Formulario ("index.php","post","VEHICULOS DISPONIBLES","form_insert","","");
  $formulario -> linea(sizeof($desruta)." Vehiculo(s) en Ruta Con Llegada Planeada Desde ".$_REQUEST[fecbus]." Hasta ".$fechaadic."",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Poseedor",0,"t");
  $formulario -> linea("Tipo de Transportadora",0,"t");
  $formulario -> linea("Placa",0,"t");
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea("Destino",0,"t");
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea("Celular",0,"t");
  $formulario -> linea("Telefono",0,"t");
  $formulario -> linea("Tipo Vehiculo",0,"t");
  $formulario -> linea("Llegada Planeada",1,"t");

  for($i = 0; $i < sizeof($desruta); $i++)
  {
   $formulario -> linea(utf8_encode($desruta[$i][7]),0,"i");
   $formulario -> linea($desruta[$i][8],0,"i");
   $formulario -> linea($desruta[$i][0],0,"i");
   $formulario -> linea($desruta[$i][1],0,"i");
   $formulario -> linea($desruta[$i][2],0,"i");
   $formulario -> linea($desruta[$i][3],0,"i");
   $formulario -> linea($desruta[$i][4],0,"i");
   $formulario -> linea($desruta[$i][5],0,"i");
   $formulario -> linea($desruta[$i][9],0,"i");
   $formulario -> linea($desruta[$i][6],1,"i");
  }

  $formulario -> nueva_tabla();
  $formulario -> linea(sizeof($desllega)." Vehiculo(s) Con Llegada Desde ".$fechadism." Hasta ".$_REQUEST[fecbus]."",1,"t2");

  $formulario -> nueva_tabla();
   $formulario -> linea("Poseedor",0,"t");
  $formulario -> linea("Tipo de Transportadora",0,"t");
  $formulario -> linea("Placa",0,"t");
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea("Destino",0,"t");
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea("Celular",0,"t");
  $formulario -> linea("Telefono",0,"t");
  $formulario -> linea("Tipo Vehiculo",0,"t");
  $formulario -> linea("Llegada",1,"t");

  for($i = 0; $i < sizeof($desllega); $i++)
  {
   $formulario -> linea(utf8_encode($desllega[$i][7]),0,"i");
   $formulario -> linea($desllega[$i][8],0,"i");
   $formulario -> linea($desllega[$i][0],0,"i");
   $formulario -> linea($desllega[$i][1],0,"i");
   $formulario -> linea($desllega[$i][2],0,"i");
   $formulario -> linea($desllega[$i][3],0,"i");
   $formulario -> linea($desllega[$i][4],0,"i");
   $formulario -> linea($desllega[$i][5],0,"i");
   $formulario -> linea($desllega[$i][9],0,"i");
   $formulario -> linea($desllega[$i][6],1,"i");
  }

  $formulario -> cerrar();
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $mRuta = array("link"=>0, "finali"=>0, "opcurban"=>0, "lleg"=>NULL, "tie_ultnov"=>NULL);#Fabian
   $listado_prin = new Despachos($_REQUEST[cod_servic],2,$this -> aplica,$this -> conexion);
   $listado_prin  -> Encabezado($_REQUEST[despac],$datos_usuario,0,$mRuta);
   #$listado_prin  -> PlanDeRuta($_REQUEST[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$_REQUEST[despac],0);
   $formulario -> oculto("opcion",$_REQUEST[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   $formulario -> cerrar();
 }
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

}

$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>

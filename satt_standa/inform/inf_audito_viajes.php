<?php

class AuditorViaje
{
  var $conexion,
      $usuario,
      $cod_aplica;

  public function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }

  
  //Inicio Función Principal
  private function principal() 
  {
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/inf_audito_viajes.js' ></script>";

    $datos_usuario = $this -> usuario -> retornar();
    $usuario = $datos_usuario["cod_usuari"];

    switch ($_REQUEST['opcion']) 
    {
      case "consulta": $this -> consult(); break;
      case "consulFec": $this -> consulFec(); break;
      default: $this -> formulario(); break;
    }
  }
  //Fin Función Principal
  
  private function formulario()
  {

    if( $_REQUEST['fec_busque'] == NULL || $_REQUEST['fec_busque'] == '' )
      $_REQUEST['fec_busque'] = date('Y-m-d');


    $formulario = new Formulario ( "?", "post", "Auditor&iacute;a de Viajes", "frm_bitaco\" id=\"frm_bitacoID");

    $formulario -> texto( "Viaje:",  "text", "num_despac\" id=\"num_despacID", 0, 15, 15, "", NULL );
    $formulario -> texto( "Manifiesto:","text", "cod_manifi\" id=\"cod_manifiID", 1, 20, 20, "", NULL );
    $formulario -> texto( "Fecha:", "text", "fec_busque\" readonly id=\"fec_busqueID", 1, 10, 10, "", $_REQUEST['fec_busque'] );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","verifiData();",0);
    $formulario -> nueva_tabla();
    $formulario -> oculto("window\" id=\"windowID","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID","consulta",0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID",$_REQUEST['cod_servic'],0);
    
    $formulario -> cerrar();
  }

  private function consulFec()
  {
    

    $mArrayTitData = array('No.', 'Viaje', 'Despacho', 'Manifiesto', 'Tipo Despacho', 'Ciudad Origen', 'Ciudad Destino', 'Fecha Despacho', 'Fecha Creaci&oacute;n', 'Fecha Llegada Planta','Fecha Salida de Planta','Fecha Cita De Cargue');
    $mArrayData = $this -> getDespacFiltro();

    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0">';
    #Fila Titulos
    $mHtml .=   '<tr>';
    foreach ($mArrayTitData as $value) {
      $mHtml .=   '<th class="CellHead">'.$value.'</th>';
    }
    $mHtml .=   '</tr>';

    #Filas Registros
    $i=1;
    foreach ($mArrayData as $row) {
      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo" align="center" >'.$i.'</td>';
      $mHtml .=   '<td class="cellInfo"><a href="index.php?cod_servic='.$_REQUEST[cod_servic].'&window=central&num_despac='.$row[0].'&opcion=consulta">'.$row[0].'</a>&nbsp;</td>';
      for($j=1; $j<sizeof($row); $j++){
        $mHtml .= '<td class="cellInfo">'.$row[$j].'&nbsp;</td>';
      }
      $mHtml .= '</tr>';
      $i++;
    }

    $mHtml .= '</table>';

    echo $mHtml;
  }

  private function consult()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $mArrayTitData  = array('&nbsp;#&nbsp;', 'Viaje', 'Despacho', 'Manifiesto', 'Fecha Despacho', 'Tipo Destino', 'Pais Origen', 'Departamento Origen', 'Ciudad Origen', 'Pis Destino', 'Departamento Destino', 'Ciudad Destino', 'cod_operad', 'Fecha Cargue', 'Hora cargue', 'Sitio Cargue', 'Valor Flete', 'Valor Despacho', 'Valor Anticipo', 'Valor Retefuente', 'Encargado Pagar Cargue', 'Encargado Pagar Descargue', 'cod_agedes', 'Peso', 'Observacion Despacho', 'Fecha LLegada', 'Observacion Llegada', 'Plan de Ruta', 'Ruta', 'Anulado', 'Poliza', 'Telefono Fijo Conductor', 'Celular Conductor', 'Direccion Conductor', 'Operador GPS', 'Usuario GPS', 'Clave GPS', 'ID GPS', 'Email Cliente', 'Aseguradora', 'Número Consolidado', 'Número Solicitud', 'C.C. Conductor', 'Conductor', 'Ciudad Conductor', 'Placa', 'Trayler', 'Tipo Vehiculo', 'Número Pedido', 'Modelo', 'Marca', 'Linea', 'Color', 'Configuración', 'Carroceria', 'Chasis', 'Motor', 'Soat', 'Fecha Vencimiento SOAT', 'Aseguradora SOAT', '# Tarjeta Propiedad', 'Categoria Licencia de Conducción', 'C.C. Poseedor', 'Poseedor', 'Ciudad Poseedor', 'Dirección Poseedor', 'Estado', 'Tipo Transportadora', 'Lugar Instalación', 'Codigo Mercancia', 'Modificado', 'Anulado', 'Fecha Llegada a Planta', 'Fecha Salida Planta', 'Canal', 'Usuario Modificación', 'Fecha Modificación', 'Usuario Creación', 'Fecha Creación');
    $mArrayData     = array(0 => $this -> getDespac());
    $mArrayDataOld  = $this -> getDespacOld($mArrayData[0][0]);
    $mArrayDatos    = array_merge($mArrayData,$mArrayDataOld);
    $mTipTrans      = array('', 'Flota Propia', 'Terceros', 'Empresas');
    $mArrayColor    = array('', '#98FB98', '#00FF00', '#32CD32', '#228B22', '#6B8E23', '#808000', '#EEE8AA', '#FFFF00', '#FFD700', '#FFA500', '#FF7F50', '#FF4500', '#FF1493', '#C71585', '#DC143C', '#B22222', '#8B0000', '#8B008B', '#4B0082', '#6A5ACD', '#0000FF', '#00008B', '#DCDCDC', '#A9A9A9', '#778899', '#2F4F4F');
    $mColum  = sizeof($mArrayTitData);
    $mFilas  = sizeof($mArrayDatos);

    #echo "<pre> mArrayDatos: ";  print_r($mArrayDatos);  echo "</pre>";

    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0">';
    #Fila Titulos
    $mHtml .=   '<tr>';
    for ($i=0; $i<$mColum; $i++){
      $mHtml .=   '<th class="CellHead">'.$mArrayTitData[$i].'</th>';
    }
    $mHtml .=   '</tr>';
    
    #Filas Registros tab_bitaco_corona
    $iD = 0;
    for ($i=0; $i<$mFilas; $i++)
    {
      $a = $i+1;
      $b = $mColum - 2;
      #$mRuta   = $this -> getRuta($mArrayDatos[$i][27]);
      $mAsegur = $this -> getAsegur($mArrayDatos[$i][37]);

      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo">'.$a.'</td>';
      for ($j=0; $j<$mColum-1; $j++)
      {
        if ($j==69 || $j==71 || $j==73 || $j==74 || $j==$b){
          $m=0;
        }
        elseif($j==23){
          $mArrayDatos[$i][$j] = $mArrayDatos[$i][$j] == '0000-00-00 00:00:00' ? '' : $mArrayDatos[$i][$j];
          $dato2 = $mArrayDatos[$i-1][$j] == '0000-00-00 00:00:00' ? '' : $mArrayDatos[$i-1][$j];
          $m = $mArrayDatos[$i][$j] != $dato2 ? $i : 0;
        }
        elseif($i>0 && $j>0 && $mArrayDatos[$i][$j] != $mArrayDatos[$i-1][$j]){
          $m=$i;
        }
        else{
          $m=0;
        }

        if ($j==37){
          $mHtml .= '<td class="cellInfo" style="background-color:'.$mArrayColor[$m].'">'.$mAsegur[0].'&nbsp;</td>';
        }elseif ($j==40){
          $mHtml .= '<td class="cellInfo" style="background-color:'.$mArrayColor[$m].'">'.$mArrayDatos[$i][40].'&nbsp;</td>';
          $mHtml .= '<td class="cellInfo" style="background-color:'.$mArrayColor[$m].'">'.$mArrayDatos[$i][49].'&nbsp;</td>';
          $mHtml .= '<td class="cellInfo" style="background-color:'.$mArrayColor[$m].'">'.$mArrayDatos[$i][50].'&nbsp;</td>';
        }elseif ($j==77){
          $mHtml .= '<td name="cellFecCreaci'.$iD.'" class="cellInfo" style="background-color:'.$mArrayColor[$m].'">'.$mArrayDatos[$i][$j].'</td>';
          $iD++;
        }elseif ($j!=49 && $j!=50){
          $mHtml .= '<td class="cellInfo" style="background-color:'.$mArrayColor[$m].'">'.$mArrayDatos[$i][$j].'&nbsp;</td>';
        }else{
          $mHtml .= '';
        }

      }

      $mHtml .= '</tr>';
    }

    $mHtml .= '</table>';

    echo $mHtml;
    echo "<script>reloadFecCreaci();</script>";
  }


  private function getDespacFiltro()
  {
    $mSql = "SELECT a.num_despac, a.num_dessat, a.cod_manifi, 
                    d.nom_tipdes, b.nom_ciudad, c.nom_ciudad, 
                    a.fec_despac, a.fec_creaci, a.fec_plalle,
                    a.fec_salida, CONCAT(a.fec_citcar, ' ', a.hor_citcar) AS fec_citcar
               FROM ".BASE_DATOS.".tab_despac_corona a
         INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b
                 ON a.cod_paiori = b.cod_paisxx AND 
                    a.cod_depori = b.cod_depart AND
                    a.cod_ciuori = b.cod_ciudad
         INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c
                 ON a.cod_paides = c.cod_paisxx AND 
                    a.cod_depdes = c.cod_depart AND
                    a.cod_ciudes = c.cod_ciudad 
          LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes d
                 ON a.cod_tipdes = d.cod_tipdes
              WHERE a.fec_creaci BETWEEN '".$_REQUEST['fec_busque']." 00:00:00' AND '".$_REQUEST['fec_busque']." 23:59:59' 
           ORDER BY a.fec_creaci
            ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matrix('i');
  }

  private function getDespac()
  {
    $mSql =  "SELECT  a.num_despac, a.num_dessat, a.cod_manifi, 
                      a.fec_despac,
                      h.nom_tipdes, b.nom_paisxx, d.nom_depart, 
                      f.nom_ciudad, c.nom_paisxx, e.nom_depart,
                      g.nom_ciudad, a.cod_operad, a.fec_citcar,
                      a.hor_citcar, a.nom_sitcar, a.val_flecon,
                      a.val_despac, a.val_antici, a.val_retefu,
                      a.nom_carpag, a.nom_despag, a.cod_agedes,
                      a.val_pesoxx, a.obs_despac, a.fec_llegad,
                      a.obs_llegad, a.ind_planru, a.cod_rutasx,
                      n.ind_anulad, a.num_poliza, a.con_telef1,
                      a.con_telmov, a.con_domici, a.gps_operad,
                      a.gps_usuari, a.gps_paswor, a.gps_idxxxx,
                      a.ema_client, a.cod_asegur, a.num_consol,
                      a.num_solici, a.cod_conduc, a.num_placax,
                      a.num_trayle, a.tip_vehicu, a.num_pedido,
                      a.ano_modelo, j.nom_marcax, k.nom_lineax,
                      l.nom_colorx, a.nom_conduc, i.nom_ciudad,
                      a.cod_config, a.cod_carroc, a.num_chasis,
                      a.num_motorx, a.num_soatxx, a.dat_vigsoa,
                      a.nom_ciasoa, a.num_tarpro, a.cat_licenc,
                      a.cod_poseed, a.nom_poseed, m.nom_ciudad,
                      a.dir_poseed, a.nom_estado, a.tip_transp,
                      a.cod_instal, a.cod_mercan, a.ind_modifi,
                      a.ind_anudes, a.fec_plalle, a.fec_salida,
                      a.cod_canalx, a.usr_modifi, a.fec_modifi,
                      a.usr_creaci, a.fec_creaci  
                    FROM ".BASE_DATOS.".tab_despac_corona a
               LEFT JOIN ".BASE_DATOS.".tab_genera_paises b
                      ON a.cod_paiori = b.cod_paisxx
               LEFT JOIN ".BASE_DATOS.".tab_genera_paises c
                      ON a.cod_paides = c.cod_paisxx
               LEFT JOIN ".BASE_DATOS.".tab_genera_depart d
                      ON a.cod_paiori = d.cod_paisxx AND 
                         a.cod_depori = d.cod_depart
               LEFT JOIN ".BASE_DATOS.".tab_genera_depart e
                      ON a.cod_paides = e.cod_paisxx AND 
                         a.cod_depdes = e.cod_depart
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad f
                      ON a.cod_ciuori = f.cod_ciudad
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad g
                      ON a.cod_ciudes = g.cod_ciudad
               LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes h
                      ON a.cod_tipdes = h.cod_tipdes
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad i
                      ON a.ciu_conduc = i.cod_ciudad
               LEFT JOIN ".BASE_DATOS.".tab_genera_marcas j
                      ON a.cod_marcax = j.cod_marcax
               LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas k
                      ON a.cod_lineax = k.cod_lineax AND
                         a.cod_marcax = k.cod_marcax
               LEFT JOIN ".BASE_DATOS.".tab_vehige_colore l
                      ON a.cod_colorx = l.cod_colorx
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad m
                      ON a.ciu_poseed = m.cod_ciudad 
               LEFT JOIN ".BASE_DATOS.".tab_despac_despac n 
                      ON a.num_dessat = n.num_despac 
              WHERE ";
    if($_REQUEST['num_despac']){
      $mSql .= "a.num_despac = '".$_REQUEST['num_despac']."'";
    }elseif($_REQUEST['cod_manifi']){
      $mSql .= "a.cod_manifi = '".$_REQUEST['cod_manifi']."'";
    }
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_arreglo();
  }


  private function getDespacOld($num_despac)
  {
    $mSql =  "SELECT  a.num_despac, a.num_dessat, a.cod_manifi, 
                      a.fec_despac, 
                      h.nom_tipdes, b.nom_paisxx, d.nom_depart, 
                      f.nom_ciudad, c.nom_paisxx, e.nom_depart,
                      g.nom_ciudad, a.cod_operad, a.fec_citcar,
                      a.hor_citcar, a.nom_sitcar, a.val_flecon,
                      a.val_despac, a.val_antici, a.val_retefu,
                      a.nom_carpag, a.nom_despag, a.cod_agedes,
                      a.val_pesoxx, a.obs_despac, a.fec_llegad,
                      a.obs_llegad, a.ind_planru, a.cod_rutasx,
                      a.ind_anulad, a.num_poliza, a.con_telef1,
                      a.con_telmov, a.con_domici, a.gps_operad,
                      a.gps_usuari, a.gps_paswor, a.gps_idxxxx,
                      a.ema_client, a.cod_asegur, a.num_consol,
                      a.num_solici, a.cod_conduc, a.num_placax,
                      a.num_trayle, a.tip_vehicu, a.num_pedido,
                      a.ano_modelo, j.nom_marcax, k.nom_lineax,
                      l.nom_colorx, a.nom_conduc, i.nom_ciudad,
                      a.cod_config, a.cod_carroc, a.num_chasis,
                      a.num_motorx, a.num_soatxx, a.dat_vigsoa,
                      a.nom_ciasoa, a.num_tarpro, a.cat_licenc,
                      a.cod_poseed, a.nom_poseed, m.nom_ciudad,
                      a.dir_poseed, a.nom_estado, a.tip_transp,
                      a.cod_instal, a.cod_mercan, a.ind_modifi,
                      '' AS ind_anudes, a.fec_plalle, '' AS fec_salida,
                      a.cod_canalx, a.usr_modifi, a.fec_modifi,
                      a.usr_creaci, a.fec_creaci
                    FROM ".BASE_DATOS.".tab_bitaco_corona a
               LEFT JOIN ".BASE_DATOS.".tab_genera_paises b
                      ON a.cod_paiori = b.cod_paisxx
               LEFT JOIN ".BASE_DATOS.".tab_genera_paises c
                      ON a.cod_paides = c.cod_paisxx
               LEFT JOIN ".BASE_DATOS.".tab_genera_depart d
                      ON a.cod_paiori = d.cod_paisxx AND 
                         a.cod_depori = d.cod_depart
               LEFT JOIN ".BASE_DATOS.".tab_genera_depart e
                      ON a.cod_paides = e.cod_paisxx AND 
                         a.cod_depdes = e.cod_depart
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad f
                      ON a.cod_ciuori = f.cod_ciudad
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad g
                      ON a.cod_ciudes = g.cod_ciudad
               LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes h
                      ON a.cod_tipdes = h.cod_tipdes
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad i
                      ON a.ciu_conduc = i.cod_ciudad
               LEFT JOIN ".BASE_DATOS.".tab_genera_marcas j
                      ON a.cod_marcax = j.cod_marcax
               LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas k
                      ON a.cod_lineax = k.cod_lineax AND
                         a.cod_marcax = k.cod_marcax
               LEFT JOIN ".BASE_DATOS.".tab_vehige_colore l
                      ON a.cod_colorx = l.cod_colorx
               LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad m
                      ON a.ciu_poseed = m.cod_ciudad
              WHERE num_despac = '".$num_despac."'
              ORDER BY a.fec_creaci DESC";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matriz();
  }

  private function getRuta($cod_rutasx)
  {
    $mSql = " SELECT a.nom_rutasx
              FROM ".BASE_DATOS.".tab_genera_rutasx a
              WHERE a.cod_rutasx = '".$cod_rutasx."'
            ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_arreglo();
  }

  private function getAsegur($cod_asegur)
  {
    $mSql = " SELECT a.abr_tercer
              FROM ".BASE_DATOS.".tab_tercer_tercer a
              WHERE a.cod_tercer = '".$cod_asegur."'
            ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_arreglo();
  }

}

$new= new AuditorViaje( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );

?>
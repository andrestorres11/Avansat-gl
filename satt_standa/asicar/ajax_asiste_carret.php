<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_asiste_carret
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['opcion']) {
      case 'consulta_ciudades':
        $this->consultaCiudades();
      break;
      case 'busqueda_transportadora':
        $this->busquedaTransportadora();
      break;
      case 'busqueda_transportador':
        $this->busquedaTransportador();
      break;
      case 'busqueda_vehiculo':
        $this->busquedaVehiculo();
      break;
      case 'registrar':
        $this -> registrar();
      break;
    }
    }

  


  public function consultaCiudades(){
    $busqueda = $_REQUEST['key'];
    $sql="SELECT a.cod_ciudad,a.nom_ciudad FROM tab_genera_ciudad a WHERE a.ind_estado = 1 AND a.nom_ciudad LIKE '%$busqueda%' ORDER BY a.nom_ciudad LIMIT 3";

      $resultado = new Consulta($sql, $this->conexion);
      $resultados = $resultado->ret_matriz();
      $htmls='';
      foreach($resultados as $valor){
        $htmls.='<div><a class="suggest-element" data="'.$valor['cod_ciudad'].' - '.$valor['nom_ciudad'].'" id="'.$valor['cod_ciudad'].'">'.$valor['nom_ciudad'].'</a></div>';
      }
      echo utf8_decode($htmls);

  }

  public function busquedaTransportadora(){
    $busqueda = $_REQUEST['valor_buscar'];
    $sql="
    SELECT COUNT(*) as 'total'
    FROM 
      tab_tercer_emptra a 
      INNER JOIN tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer 
    WHERE 
      b.cod_estado = 1 
      AND b.nom_tercer = '$busqueda';
      ";

      $resultado = new Consulta($sql, $this->conexion);
      $resultados = $resultado->ret_matriz();
      $validacion=false;
      if($resultados[0]['total']>0){
        $validacion=true;
      }

      echo $validacion;
  }

  public function busquedaTransportador(){
    $cod_conduc = $_REQUEST['cod_conduc'];
    $retorno= [];
    $retorno['validacion']=false;
    $sql="SELECT b.cod_tercer,b.nom_tercer,
                 b.nom_apell1,
                 b.nom_apell2,
                 b.num_telmov
         FROM ".BASE_DATOS.".tab_tercer_conduc a 
         INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b 
         ON a.cod_tercer = b.cod_tercer 
         AND b.cod_estado = 1
         WHERE a.cod_tercer = $cod_conduc
         ";
    $resultado = new Consulta($sql, $this->conexion);
    $cantidad_registros = $resultado->ret_num_rows();
    if($cantidad_registros>0){
      $datos=$resultado->ret_arreglo();
      $retorno['validacion']=true;
      $retorno['nom_transp']=$datos['nom_tercer'];
      $retorno['ap1_transp']=$datos['nom_apell1'];
      $retorno['ap2_transp']=$datos['nom_apell2'];
      $retorno['ce1_transp']=$datos['num_telmov'];
    }
    echo json_encode($retorno);
  }

  public function busquedaVehiculo(){
    $placa = $_REQUEST['placa'];
    $retorno= [];
    $retorno['validacion']=false;
    $sql="SELECT b.nom_marcax,
                 c.nom_colorx 
          FROM ".BASE_DATOS.".tab_vehicu_vehicu a 
          INNER JOIN ".BASE_DATOS.".tab_genera_marcas b 
          ON a.cod_marcax = b.cod_marcax
          INNER JOIN ".BASE_DATOS.".tab_vehige_colore c ON a.cod_colorx = c.cod_colorx
            WHERE a.num_placax = '$placa'";
    $resultado = new Consulta($sql, $this->conexion);
    $cantidad_registros = $resultado->ret_num_rows();
    if($cantidad_registros>0){
      $datos=$resultado->ret_arreglo();
      $retorno['validacion']=true;
      $retorno['nom_marcax']=$datos['nom_marcax'];
      $retorno['nom_colorx']=$datos['nom_colorx'];
    }
    echo json_encode($retorno);
  }

  function registrar(){
    try {
    $return = [];
    $ciu_origen="";
    $ciu_destin="";

    if(isset($_REQUEST['ciu_origen'])){
      $ciu_origen=$this->separarCodigoCiudad($_REQUEST['ciu_origen']);
    }
    if(isset($_REQUEST['ciu_origen'])){
      $ciu_destin=$this->separarCodigoCiudad($_REQUEST['ciu_destin']);
    }

    $sql="INSERT INTO ".BASE_DATOS.".tab_asiste_carret(
      tip_solici, nom_solici, cor_solici, 
      tel_solici, cel_solici, ase_solici, 
      num_poliza, num_transp, nom_transp, 
      ap1_transp, ap2_transp, ce1_transp, 
      ce2_transp, num_placax, mar_vehicu, 
      col_vehicu, tip_vehicu, num_remolq, 
      url_opegps, nom_opegps, nom_usuari, 
      con_vehicu, ubi_vehicu, pun_refere, 
      des_asiste, fec_servic, ciu_origen, 
      dir_ciuori, ciu_destin, dir_ciudes, 
      obs_acompa, usu_creaci, fec_creaci
    ) 
    VALUES 
      (
        '".$_REQUEST['tip_solici']."', '".$_REQUEST['nom_solici']."', '".$_REQUEST['ema_solici']."', 
        '".$_REQUEST['tel_solici']."', '".$_REQUEST['cel_solici']."', '".$_REQUEST['nom_asegura']."', 
        '".$_REQUEST['nom_poliza']."', '".$_REQUEST['num_transp']."', '".$_REQUEST['nom_transp']."', 
        '".$_REQUEST['ap1_transp']."', '".$_REQUEST['ap2_transp']."', '".$_REQUEST['ce1_transp']."', 
        '".$_REQUEST['ce2_transp']."', '".$_REQUEST['num_placax']."', '".$_REQUEST['nom_marcax']."', 
        '".$_REQUEST['nom_colorx']."', '".$_REQUEST['tip_transp']."', '".$_REQUEST['num_remolq']."', 
        '".$_REQUEST['url_opegps']."', '".$_REQUEST['nom_opegps']."', '".$_REQUEST['nom_usuari']."', 
        '".$_REQUEST['con_vehicu']."', '".$_REQUEST['ubi_vehicu']."', '".$_REQUEST['pun_refere']."', 
        '".$_REQUEST['des_asiste']."', '".$_REQUEST['fec_servic']."', '".$ciu_origen."', 
        '".$_REQUEST['dir_ciuori']."', '".$ciu_destin."', '".$_REQUEST['dir_ciudes']."', 
        '".$_REQUEST['obs_acompa']."', '".$_SESSION['datos_usuario']['cod_usuari']."',NOW()
      )";

      $consulta = new Consulta($sql, $this -> conexion);
      if($consulta==true){
        $return['status'] = 200;
        $return['response'] = 'El registro ha sido guardado exitosamente.';
      }else{
        $return['status'] = 500;
        $return['response'] = 'Error al realizar al almacenar la información.';
      }
      echo json_encode($return);
    }catch (Exception $e) {
      echo 'Excepción registrar: ',  $e->getMessage(), "\n";
    }
  }

  function separarCodigoCiudad($dato){
    $cod_ciudad = explode(" ", $dato);
    return trim($cod_ciudad[0]);
  }


}

$proceso = new ajax_asiste_carret();
 ?>
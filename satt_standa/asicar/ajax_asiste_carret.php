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
      case 'busqueda_costoAcompa':
        $this -> costoAcompa();
      break;
      case 'dar_serviciosAsistencia':
        $this -> serviciosAsistencia();
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
    $cod_transp = $_REQUEST['cod_transp'];
    $this->darNombreCliente($_REQUEST['cod_transp'],2);
    $placa = $_REQUEST['placa'];
    $retorno= [];
    $retorno['validacion']=false;
    $sql="SELECT b.nom_marcax,
                 c.nom_colorx 
          FROM ".BASE_DATOS.".tab_vehicu_vehicu a 
          INNER JOIN ".BASE_DATOS.".tab_genera_marcas b 
          ON a.cod_marcax = b.cod_marcax
          INNER JOIN ".BASE_DATOS.".tab_vehige_colore c ON a.cod_colorx = c.cod_colorx
          INNER JOIN ".BASE_DATOS.".tab_transp_vehicu d ON d.cod_transp = ".$cod_transp."
          AND d.num_placax = '$placa'
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
    $servicios = html_entity_decode($_REQUEST['services']);
    $servicios = json_decode($servicios);
    //Revisa la base de datos y busca el numero de la solicitud.
    $sql = "SELECT IFNULL((MAX(a.id) + 1),1)
            FROM ".BASE_DATOS.".tab_asiste_carret a";
    $num_solici = new Consulta($sql, $this->conexion);
    $num_solici = $num_solici->ret_matriz('a')[0][0];
    $tip_solici = $_REQUEST['tip_solici'];  
    if(isset($_REQUEST['ciu_origen'])){
      $ciu_origen=$this->separarCodigoCiudad($_REQUEST['ciu_origen']);
    }
    if(isset($_REQUEST['ciu_destin'])){
      $ciu_destin=$this->separarCodigoCiudad($_REQUEST['ciu_destin']);
    }
    
    $cod_transp = $_REQUEST['optionTransp'];
    if($_REQUEST['optionTransp']==""){
      $cod_transp = $_REQUEST['cod_transp'];
    }

    $cod_cliente = $this->darNombreCliente($cod_transp,2);
    //formato fecha
    $fec_servic = '';
    if(isset($_REQUEST['fec_servic'])){
      $fec_servic = $_REQUEST['fec_servic'];
      $fec_servic  = date("Y-m-d H:i:s", strtotime($fec_servic));
    }
    $sql="INSERT INTO ".BASE_DATOS.".tab_asiste_carret(
      id,cod_client,cod_transp,
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
        '".$num_solici."','".$cod_cliente."','".$cod_transp."',
        '".$_REQUEST['tip_solici']."', '".$_REQUEST['nom_solici']."', '".$_REQUEST['ema_solici']."', 
        '".$_REQUEST['tel_solici']."', '".$_REQUEST['cel_solici']."', '".$_REQUEST['nom_asegura']."', 
        '".$_REQUEST['nom_poliza']."', '".$_REQUEST['num_transp']."', '".$_REQUEST['nom_transp']."', 
        '".$_REQUEST['ap1_transp']."', '".$_REQUEST['ap2_transp']."', '".$_REQUEST['ce1_transp']."', 
        '".$_REQUEST['ce2_transp']."', '".$_REQUEST['num_placax']."', '".$_REQUEST['nom_marcax']."', 
        '".$_REQUEST['nom_colorx']."', '".$_REQUEST['tip_transp']."', '".$_REQUEST['num_remolq']."', 
        '".$_REQUEST['url_opegps']."', '".$_REQUEST['nom_opegps']."', '".$_REQUEST['nom_usuari']."', 
        '".$_REQUEST['con_vehicu']."', '".$_REQUEST['ubi_vehicu']."', '".$_REQUEST['pun_refere']."', 
        '".$_REQUEST['des_asiste']."', '".$fec_servic."', '".$ciu_origen."', 
        '".$_REQUEST['dir_ciuori']."', '".$ciu_destin."', '".$_REQUEST['dir_ciudes']."', 
        '".$_REQUEST['obs_acompa']."', '".$_SESSION['datos_usuario']['cod_usuari']."',NOW()
      )";

      $consulta = new Consulta($sql, $this -> conexion,"BR");

      foreach($servicios as $servicio){
      $des_servic = $this->darDescripServicio($servicio->servicio);
      $cos_servic = $this->darCostoServicio($servicio->servicio);
      $sql="INSERT INTO tab_servic_solasi(
        cod_solasi,cod_servic,des_servic,
        tip_tarifa,can_servic,val_servic,
        usr_creaci,fec_creaci
      )
      VALUES(
        '".$num_solici."','".$servicio->servicio."','".$des_servic."',
        'diurna',1,'".$cos_servic."',
        '".$_SESSION['datos_usuario']['cod_usuari']."',NOW()
      );";
      $consulta = new Consulta($sql, $this -> conexion, "R");
      }
      //Novedad de la bitacora
      $det_noveda = 'Sin Novedad';
      // Registra Tarifa de acompañamiento como servicio
      if($tip_solici == CON_SOLICI_ACOMPA){
        $sql="SELECT a.val_tarifa
        FROM ".BASE_DATOS.".tab_tarifa_acompa a
        WHERE a.ciu_origen = '$ciu_origen'
        AND a.ciu_destin = '$ciu_destin'";
        $resultado = new Consulta($sql, $this->conexion);
        $cantidad_registros = $resultado->ret_num_rows();
        if($cantidad_registros>0){
          $datos=$resultado->ret_arreglo();
          $nom_ciuori = $this->getNombreCiudad($ciu_origen);
          $nom_destin = $this->getNombreCiudad($ciu_destin);
          $nom_servic = 'Serv. Acomp Ruta: '.$nom_ciuori.' - '.$nom_destin;

          //Cambia el detalle de la novedad para la bitacora
          $det_noveda.=' Ruta Sol -> '.$nom_ciuori.' - '.$nom_destin;

          $sql="INSERT INTO tab_servic_solasi(
            cod_solasi,cod_servic,des_servic,
            tip_tarifa,can_servic,val_servic,
            usr_creaci,fec_creaci
          )
          VALUES(
            '".$num_solici."',0,'".$nom_servic."',
            'unica',1,'".$datos['val_tarifa']."',
            '".$_SESSION['datos_usuario']['cod_usuari']."',NOW()
          );";
          $consulta = new Consulta($sql, $this -> conexion);
        }else{
          $return['status'] = 500;
          $return['response'] = 'No se encontro origen y destino';
          echo json_encode($return);
          exit;
        }
      }

      //Registro de la bitacora de seguimiento de la asistencia
      $sql="INSERT INTO tab_seguim_solasi(
        cod_solasi, ind_estado, obs_detall,
        usr_creaci, fec_creaci
      ) 
      VALUES 
        (
          '".$num_solici."', '1', '$det_noveda',
          '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
        )";
      $consulta = new Consulta($sql, $this -> conexion, "RC");

      if($consulta==true){
        $this->enviarCorreo($num_solici,$cod_cliente,$_REQUEST['ema_solici'],$_REQUEST['tip_solici'],$_REQUEST['nom_solici']);
        $return['status'] = 200;
        $return['response'] = 'El formulario se registro exitosamente con el numero: '.$num_solici;
      }else{
        $return['status'] = 500;
        $return['response'] = 'Error al realizar al almacenar la información.';
      }
      echo json_encode($return);
    }catch (Exception $e) {
      echo 'Excepción registrar: ',  $e->getMessage(), "\n";
    }
  }

  function getNombreCiudad($cod_ciudad){
    $sql="SELECT a.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a WHERE a.ind_estado = 1 AND a.cod_ciudad = '$cod_ciudad' LIMIT 1";
      $resultado = new Consulta($sql, $this->conexion);
      $resultados = $resultado->ret_matriz()[0];
      return $resultados['nom_ciudad'];
  }

  function separarCodigoCiudad($dato){
    $cod_ciudad = explode(" ", $dato);
    return trim($cod_ciudad[0]);
  }

  function darDescripServicio($cod_servic){
    $sql="SELECT a.abr_servic FROM ".BASE_DATOS.".tab_servic_asicar a
          WHERE a.id = '".$cod_servic."'";
    $consulta = new Consulta($sql, $this -> conexion);
    $descrip = $consulta ->ret_matriz('a')[0]['abr_servic'];
    return $descrip;
  }

  function darCostoServicio($cod_servic){
    $sql="SELECT a.tar_diurna FROM ".BASE_DATOS.".tab_servic_asicar a
          WHERE a.id = '".$cod_servic."'";
    $consulta = new Consulta($sql, $this -> conexion);
    $costo= $consulta ->ret_matriz('a')[0]['tar_diurna'];
    return $costo;
  }

  private function enviarCorreo($num_solici,$cod_cliente,$correod,$solicitud,$nom_solici) {
    $logo = URL_STANDA.'imagenes/asistencia.png';
    //$logo = 'https://dev.intrared.net:8083/ap/ctorres/sat-gl-2015/satt_standa/imagenes/asistencia.png';
    $nom_asiste = $this->tipSolicitud($solicitud);
    $fec_actual = date("Y-m-d H:i:s");   
    $correos = $this->darCorreos($correod);
    $to = $correo;
    $subject = "SOLICITUD DE ".strtoupper($nom_asiste);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: asistencias@faro.com";
    $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
    
    <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta content="telephone=no" name="format-detection">
      <title>Nuevo correo electrónico 2</title>
      <!--[if (mso 16)]><style type="text/css"> a {text-decoration: none;} </style><![endif]-->
      <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
      <style type="text/css">
        @media only screen and (max-width:600px) 
        { p, ul li, ol li, a 
        { font-size: 16px!important;
         line-height: 150%!important 
        } 
        h1 { font-size: 30px!important; text-align: center; line-height: 120%!important } 
        h2 { font-size: 26px!important; text-align: center; line-height: 120%!important } 
        h3 { font-size: 20px!important; text-align: center; line-height: 120%!important } 
        h1 a { font-size: 30px!important } h2 a { font-size: 26px!important } 
        h3 a { font-size: 20px!important } 
        .es-menu td a { font-size: 16px!important } 
        .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size: 16px!important } 
        .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size: 16px!important } 
        .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size: 12px!important } 
        *[class="gmail-fix"] { display: none!important }
         .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align: center!important } 
         .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align: right!important } 
         .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align: left!important } 
         .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display: inline!important } 
         .es-button-border { display: block!important } 
         a.es-button { font-size: 20px!important; display: block!important; border-left-width: 0px!important; border-right-width: 0px!important } 
         .es-btn-fw { border-width: 10px 0px!important; text-align: center!important } 
         .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width: 100%!important } 
         .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width: 100%!important; max-width: 600px!important } 
         .es-adapt-td { display: block!important; width: 100%!important }
         .adapt-img { width: 100%!important; height: auto!important } 
         .es-m-p0 { padding: 0px!important } 
         .es-m-p0r { padding-right: 0px!important } 
         .es-m-p0l { padding-left: 0px!important } 
         .es-m-p0t { padding-top: 0px!important } 
         .es-m-p0b { padding-bottom: 0!important } 
         .es-m-p20b { padding-bottom: 20px!important } 
         .es-mobile-hidden, .es-hidden { display: none!important } 
         .es-desk-hidden { display: table-row!important; width: auto!important; overflow: visible!important; float: none!important; max-height: inherit!important; line-height: inherit!important } 
         .es-desk-menu-hidden { display: table-cell!important } 
         table.es-table-not-adapt, .esd-block-html table { width: auto!important } 
         table.es-social { display: inline-block!important } 
         table.es-social td { display: inline-block!important } } 
         #outlook a { padding: 0; } 
         .ExternalClass { width: 100%; } 
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; } 
         .es-button { mso-style-priority: 100!important; text-decoration: none!important; } 
         a[x-apple-data-detectors] { color: inherit!important; text-decoration: none!important; font-size: inherit!important; font-family: inherit!important; font-weight: inherit!important; line-height: inherit!important; } 
         .es-desk-hidden { display: none; float: left; overflow: hidden; width: 0; max-height: 0; line-height: 0; mso-hide: all; } 
         .colortext{ color:#000; }
      </style>
    </head>
    
    <body style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
      <div class="es-wrapper-color" style="background-color:#FFFFFF;">
        <!--[if gte mso 9]><v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"> <v:fill type="tile" color="#ffffff" origin="0.5, 0" position="0.5,0"></v:fill> </v:background><![endif]-->
        <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;">
          <tr style="border-collapse:collapse;">
            <td valign="top" style="padding:0;Margin:0;">
              <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                <tr style="color:#ff9800; border-collapse:collapse;">
                  <td align="center" style="padding:0;Margin:0;">
                    <table bgcolor="#efefef" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="850" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-left:1px solid #808080;border-right:1px solid #808080;border-top:1px solid #808080;border-bottom:1px solid #808080;">
                      <tr style="border-collapse:collapse;">
                        <td align="left" style="Margin:0;padding-bottom:5px;padding-top:20px;padding-left:40px;padding-right:40px;">
                          <!--[if mso]><table width="518" cellpadding="0" cellspacing="0"><tr><td width="154" valign="top"><![endif]-->
                          <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left;">
                            <tr style="border-collapse:collapse;">
                              <td width="154" class="es-m-p0r es-m-p20b" valign="top" align="center" style="padding:0;Margin:0;">
                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                  <tr style="border-collapse:collapse;">
                                    <td align="center" style="padding:0;Margin:0;font-size:0px;"><img class="adapt-img" src="'.$logo.'" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"
                                        width="250"></td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                          <!--[if mso]></td><td width="20"></td><td width="344" valign="top"><![endif]-->
                          <table cellpadding="0" cellspacing="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                            <tr style="border-collapse:collapse;">
                              <td width="344" align="left" style="padding:0;Margin:0;">
                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                  <tr style="border-collapse:collapse;">
                                    <td align="left" style="text-align: center; padding:0;Margin:0;padding-top:40px;">
                                      <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:20px;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:60px;color:#333333;"><b>Solicitud N°. '.$num_solici.' </b></p>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                          <!--[if mso]></td></tr></table><![endif]-->
                        </td>
                      </tr>
                      <tr style="border-collapse:collapse;">
                        <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;">
                          <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                            <tr style="border-collapse:collapse;">
                              <td class="es-m-p0r" width="558" valign="top" align="center" style="padding:0;Margin:0;">
                                <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                  <tr style="border-collapse:collapse;">
                                    <td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0;">
                                      <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                        <tr style="border-collapse:collapse;">
                                          <td style="padding:0;Margin:0px;border-bottom:6px solid #ff9800;background:none;height:1px;width:100%;margin:0px;"></td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                  <tr style="border-collapse:collapse;">
                                    <td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0;">
                                      <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                        <tr style="border-collapse:collapse;">
                                          <td style="padding:0;Margin:0px;border-bottom:10px solid #ff9800;background:none;height:1px;width:100%;margin:0px;"></td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr style="border-collapse:collapse;">
                        <td align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:40px;padding-right:40px;">
                          <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                            <tr style="border-collapse:collapse;">
                              <td width="518" align="center" valign="top" style="padding:0;Margin:0;">
                                <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;border:2px double #ff9800;"
                                  role="presentation">
                                  <tr style="border-collapse:collapse;">
                                    <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;">
                                      <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#655e5e;">
                                        <br><strong class="colortext">Solicitud de: </strong> '. $nom_asiste .' <br> <br><strong class="colortext">'.$this->darNombreCliente($cod_cliente,1).'</strong>                                    <br> <br><strong class="colortext">Fecha y hora de la solicitud: </strong> '. $fec_actual .' <br> <br class="colortext">Señor(a):
                                        '. $nom_solici .'. <br> <br class="colortext">Por medio del presente correo la línea de servicio <strong>Asistencia Logística</strong>                                    del <strong>Grupo OET</strong>, le informa que su solicitud se creo exitosamente. <br> <br class="colortext">Estado:
                                        <strong class="colortext">En proceso de validación.</strong> <br> <br class="colortext">Le estaremos informando el
                                        estado de su solicitud, cabe aclarar que nuestro tiempo de respuesta es de aproximadamente 45 minutos o antes. <br>                                    <br> </p>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr style="border-collapse:collapse;">
                        <td align="left" style="padding:0;Margin:0;">
                          <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                            <tr style="border-collapse:collapse;">
                              <td width="598" align="center" valign="top" style="padding:0;Margin:0;">
                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                  <tr style="border-collapse:collapse;">
                                    <td align="center" style="padding:0;Margin:0;padding-bottom:10px;">
                                      <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:10px;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:10px;color:#333333;">
                                        Copyright © '.date('Y').'. Todos Los Derechos Reservados. Diseñado y desarrollado por Grupo OET S.A.S.</p>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </body>
    
    </html>';
    foreach($correos as $correo){
      mail($correo, $subject, $message, $headers);
    }

    
}

private function tipSolicitud($solicitud){
  $sql="SELECT a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.id = $solicitud";
  $consulta = new Consulta($sql, $this -> conexion);
  $nom_asiste = $consulta->ret_matriz('a');
  return $nom_asiste[0]['nom_asiste'];
}

private function darCorreos($correodado){
  $retorno= array();
  array_push($retorno, trim(strtolower($correodado)));
  //Busca Correos registrados correspondientes a los gestores de asistencia
  $sql="SELECT a.dir_emailx FROM ".BASE_DATOS.".tab_genera_parcor a WHERE a.num_remdes = '';";
  $consulta = new Consulta($sql, $this -> conexion);
  $dcorreos = $consulta->ret_matriz('a');

  foreach($dcorreos as $correos){
    $correo = explode(",", $correos['dir_emailx']);
    foreach($correo as $correou){
      array_push($retorno, trim(strtolower($correou)));
    }
  }

  $sql="SELECT 
	        a.cod_usuari, 
	        c.nom_tercer, 
	        c.dir_emailx 
        FROM 
	      ".BASE_DATOS.".tab_genera_usuari a 
        INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b 
        ON a.cod_perfil = b.cod_perfil 
        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
        ON b.clv_filtro = c.cod_tercer
        WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."';
        ";
  $consulta = new Consulta($sql, $this -> conexion);
  $dcorreos = $consulta->ret_matriz('a');
      
  foreach($dcorreos as $correos){
    $correo = explode(",", $correos['dir_emailx']);
    foreach($correo as $correou){
      array_push($retorno, trim(strtolower($correou)));
    }
  }

  return $retorno;
}

private function darNombreCliente($cod_tercer,$ver){
  $sql="SELECT a.cod_tercer,a.abr_tercer FROM ".BASE_DATOS.".tab_tercer_tercer a
        WHERE a.cod_tercer = $cod_tercer";
  $consulta = new Consulta($sql, $this -> conexion);
  $resultado = $consulta->ret_matriz('a');
  if($ver==1){
  if($resultado[0]['abr_tercer']!=""){
    return "Cliente : ".$resultado[0]['abr_tercer'];
  }else{
    return "";
  }
  }else{
    if($resultado[0]['cod_tercer']!=""){
      return $resultado[0]['cod_tercer'];
    }else{
      return false;
    }
  }
}

private function costoAcompa(){
  if(isset($_REQUEST['ciu_origen'])){
    $ciu_origen=$this->separarCodigoCiudad($_REQUEST['ciu_origen']);
  }
  if(isset($_REQUEST['ciu_destin'])){
    $ciu_destin=$this->separarCodigoCiudad($_REQUEST['ciu_destin']);
  }
  $retorno= [];
  $retorno['validacion']=false;
  $sql="SELECT a.val_tarifa
        FROM ".BASE_DATOS.".tab_tarifa_acompa a
       WHERE a.ciu_origen = '$ciu_origen'
       AND a.ciu_destin = '$ciu_destin'
       AND a.ind_estado = 1";
  $resultado = new Consulta($sql, $this->conexion);
  $cantidad_registros = $resultado->ret_num_rows();
  $retorno['sql']=$sql;
  if($cantidad_registros>0){
    $datos=$resultado->ret_arreglo();
    $retorno['validacion']=true;
    $retorno['val_tarifa']=$datos['val_tarifa'];
    
  }
  echo json_encode($retorno);
}

private function serviciosAsistencia(){
  $asistencia = $_REQUEST['cod_tipAsi'];
  $sql="SELECT a.id, a.abr_servic 
        FROM ".BASE_DATOS.".tab_servic_asicar a  
        WHERE a.tip_asicar = '$asistencia' AND a.ind_estado = 1;";
  $resultado = new Consulta($sql, $this->conexion);
  $resultados = $resultado->ret_matriz('a');
  $total = $resultado->ret_num_rows();
  $html='';
  $conteo=1;
  if($total>0){
  foreach($resultados as $dato){
    if($conteo==1){
      $html.='<div class="row mt-3"><div class="col-1"></div>';
      }
    $html.='<div class="col-5 d-flex align-items-center">
              <div class="col-1">
                <input type="checkbox" id="ser_'.$dato['id'].'" value="'.$dato['id'].'">
              </div>
              <div class="col-11 align-items-center">
                <p class="text-right-service">'.$dato['abr_servic'].'</p>
              </div>
            </div>';

    $conteo++;
    if($conteo>=3){
      $html.='</div>';
      $conteo=1;
    }
  }
  }else{
    $html.='<div class="row mt-3"><div class="col-12"><center><h5 style="color:red;">No hay registro de servicios asociados al tipo de solicitud.</h5></center></div></div>';
  }
  echo json_encode($html);
}




}

$proceso = new ajax_asiste_carret();
 ?>
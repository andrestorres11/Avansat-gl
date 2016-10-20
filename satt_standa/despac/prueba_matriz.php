<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

class PruebaMatrizCorona
{
  private static $cConection = NULL;

  function __construct( $fConection =  NULL)
  {
    self::$cConection = $fConection;

    $mProto =  "SELECT  num_despac, cod_noveda FROM satt_faro.tab_protoc_asigna
          WHERE fec_noveda >= '2015-07-21 11:35:00'
          AND fec_noveda <= '2015-07-21 12:40:00'
          AND ind_ejecuc = '0'" ;
          $aDespac = self::RetMatriz($mProto );
          #echo "<pre>"; print_r($aDespac); echo "</pre>";
/*
          foreach ($aDespac as $key => $mdata):
            self::getMailProtoc("", $mdata["num_despac"], $mdata["cod_noveda"], 'satt_faro');
          endforeach;*/

    self::getMailProtoc("", '1649048', '326', 'satt_faro');
  }

  function getMailProtoc($cod_protoc, $num_despac, $cod_noveda, $base_datos )
  {
    # Datos del despacho para la matriz
    $mSelect = "SELECT 
                  a.cod_ciuori, b.cod_mercan AS cod_produc, a.cod_ciudes,
                  a.cod_tipdes, b.cod_instal AS cod_zonaxx, c.cod_canalx,
                  b.tip_transp AS cod_tiptra, '".$cod_noveda."' AS cod_noveda, a.num_despac, a.nom_sitcar AS cod_deposi
                FROM 
                  ".$base_datos.".tab_despac_despac a
                LEFT JOIN
                  ".$base_datos.".tab_despac_sisext b
                  ON
                  a.num_despac = b.num_despac
                LEFT JOIN
                  ".$base_datos.".tab_despac_destin c
                  ON
                  a.num_despac = c.num_despac  
               WHERE a.num_despac = '".$num_despac."' GROUP BY a.num_despac ";
      $aDespac = self::RetMatriz($mSelect );

      # Consecutivo canal ya que no viaja por el wsdl de astrans ---------------------------------
      $mSelect = "SELECT con_consec 
                FROM ".$base_datos.".tab_genera_canalx 
               WHERE cod_canalx = '".$aDespac[0]['cod_canalx']."' AND 
                     cod_produc = '".$aDespac[0]['cod_produc']."' "; 
      $aCanalx = self::RetMatriz($mSelect );
      # -------------------------------------------------------------------------------------------

      $aDespac[0]['cod_canalx'] = $aCanalx[0]['con_consec'] != '' ? $aCanalx[0]['con_consec'] : '';



 
      # Trae los correos ---------------------------------------------------------------
      $mMailTo = self::getDataListCriter( $aDespac[0], 'P', $base_datos );
      $mMailCC = self::getDataListCriter( $aDespac[0], 'S', $base_datos );


    

      # Envia correo a los destinatarios del protocolo

      # ------------------------------------------------------------------------------------

      # Valida que exista un correo o si no coloca opoveda por default --------------------- 
      $mSelect = "SELECT usr_emailx FROM ".$base_datos.".tab_genera_usuari WHERE cod_usuari = 'povedad.a' ";
      $mUsuari  = self::RetMatriz($mSelect );           


      $mMailTo = $mMailTo[0]["usr_emailx"] == ''? $mUsuari[0]['usr_emailx'] : $mMailTo[0]["usr_emailx"];
      $mMailCC = $mMailCC[0]["usr_emailx"];
      #-------------------------------------------------------------------------------------

      $RESULTADO = array( 'ema_conpri' => $mPrincipal, 'ema_otrcon' => $mSecundario, 'ema_conprX' => $mMailTo,    'ema_otrcoX' => $mMailCC   );

      #$data = self::getDataDespac($num_despac);

      echo "<pre>"; print_r($RESULTADO); echo "</pre>";

     #$mCorreo = PruebaMatrizCorona::EnviaCorreo( $RESULTADO, $num_despac , $mDataDespac);
  }

  # Metodo para enviar el correo a los usuarios del protocolo
  private function EnviaCorreo($mCorreo = NULL, $mNumDespac = NULL,$mDataDespac = NULL)
  {
    try
    {
      echo "<pre>"; print_r( $mCorreo ); echo "</pre>";
      /*
      echo "<pre>Correos a enviar ".$mNumDespac.":<br>"; print_r($mCorreo); echo "</pre>";


                  $num_despac = $mNumDespac;
                  $nom_tipdes = $mDataDespac['nom_tipdes'];
                  $cod_conduc = $mDataDespac['cod_conduc'];
                  $nom_conduc = $mDataDespac['nom_conduc'];
                  $cel_conduc = $mDataDespac['cel_conduc'];
                  $tip_vehicu = $mDataDespac['tip_vehicu'];
                  $nom_empres = $mDataDespac['nom_empres'];
                  $num_placax = $mDataDespac['num_placax'];
                  $nom_rutaxx = $mDataDespac['nom_rutaxx'];
                  $nom_origen = $mDataDespac['nom_origen'];
                  $nom_destin = $mDataDespac['nom_destin'];
                  $cod_factur = $mDataDespac['cod_factur'];
                  $nom_client = $mDataDespac['nom_client'];
                  $num_viajex = $mDataDespac['num_viajex'];
                  $nom_mercan = $mDataDespac['nom_mercan'];
                  $num_pedido = $mDataDespac['num_pedido'];
                  $num_solici = $mDataDespac['num_solici'];
                  $nom_poseed = $mDataDespac['nom_poseed'];
                  $nom_tiptra = $mDataDespac['nom_tiptra'];
                  $not_especi = $mDataDespac['not_especi'];
                  $nom_noveda = $mDataDespac['nom_noveda'];
                  $nom_sitiox = $mDataDespac['nom_sitiox'];
                  $obs_noveda = $mDataDespac['obs_noveda'];
                  $fec_noveda = $mDataDespac['fec_noveda'];
                  $nom_conasi = $mDataDespac['nom_conasi'];
                  $usr_conasi = $mDataDespac['usr_conasi'];
                  $ema_asigna = $mDataDespac['ema_asigna'];
                  $nom_asigna = $mDataDespac['nom_asigna'];

                  /*********************************************************/
                  /*$temporal = getcwd();
                  if( $temporal == '/var/www/html/ap/satt_standa/despac' )
                    $tmpl_file = '../planti/pla_notifi_corona.html';
                  else
                    $tmpl_file = '../planti/pla_notifi_corona.html';
                  
                  $thefile = implode("", file( $tmpl_file ) );
                  $thefile = addslashes($thefile);
                  $thefile = "\$r_file=\"".$thefile."\";";
                  eval( $thefile );
                  $mHtmlxx = $r_file;

          

                  $mCabece  = 'MIME-Version: 1.0' . "\r\n";
                  $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                  $mCabece .= 'From: Centro Logistico FARO <no-reply@eltransporte.org>' . "\r\n";
                  #mail( $_CENOPE['ema_conprX'], $mAsunto, $mHtmlxx, $mCabece.'Cc: '.$_CENOPE['ema_otrcoX']. "\r\n"); 
                  mail( MAIL_SUPERVISORES.", miguel.romero@intrared.net ", $mAsunto." - Validacion Correo PARA ", 
                        $mHtmlxx."<br><br>ENVIADO A: ".$_CENOPE['ema_conprX']."<br>COPIADO A: ".$_CENOPE['ema_otrcoX'], 
                        $mCabece);*/

    }
    catch(Exception $e)
    {

    }
  }

  private function getDataDespac( $despac ){

    $sql = "SELECT * FROM satt_faro.tab_despac_vehige a
             WHERE a.num_despac = '".$despac."'";
 
    $_Data  = self::RetMatriz( $sql );

    return $_Data;
  }

  private function getDataListCriter( $mData = NULL, $mTipCorreo = 'P', $base_datos, $mPrint = false)
  {

   

    $mListCr = '
          SELECT  
                 y.usr_emailx AS usr_emailx, 
                 "' . $mData["num_despac"] . '" AS num_despac,
                     z.cod_usuari


          FROM ( 

                SELECT aa.cod_usuari , 
                     aa.val_criter AS ValCriter1, aa.nom_criter AS NomCriter1, 
                     bb.val_criter AS ValCriter2, bb.nom_criter AS NomCriter2, 
                     cc.val_criter AS ValCriter3, cc.nom_criter AS NomCriter3,
                     dd.val_criter AS ValCriter4, dd.nom_criter AS NomCriter4,
                     ee.val_criter AS ValCriter5, ee.nom_criter AS NomCriter5,
                     ff.val_criter AS ValCriter6, ff.nom_criter AS NomCriter6,
                     gg.val_criter AS ValCriter7, gg.nom_criter AS NomCriter7,
                     hh.val_criter AS ValCriter8, hh.nom_criter AS NomCriter8

              FROM (
                            SELECT  a.cod_usuari, a.cod_consec
                              FROM   '.$base_datos.'.tab_genera_modcom a 
                            WHERE  1 = 1 GROUP BY a.cod_usuari
                   ) xx
                    INNER JOIN 
                   (
                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, b.nom_ciudad AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a ,
                                  '.$base_datos.'.tab_genera_ciudad b
                            WHERE a.cod_criter = "1" AND  
                                  a.val_criter = b.cod_ciudad AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"   
                   ) aa
                    ON xx.cod_usuari = aa.cod_usuari '.($mData["cod_ciuori"] != '' ? ' AND  aa.val_criter = "'.$mData["cod_ciuori"].'" /*Origen*/ ' : '').'
                    LEFT JOIN
                   (
                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bc.nom_produc AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a, 
                                  '.$base_datos.'.tab_genera_produc bc 
                            WHERE a.cod_criter = "2" AND       
                                  a.val_criter = bc.cod_produc AND                                                                    
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) bb
                    ON xx.cod_usuari = bb.cod_usuari '.($mData["cod_produc"] != '' ? ' AND  bb.val_criter = "'.$mData["cod_produc"].'" /*PRODUC*/ ' : '').'
                    LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bd.nom_ciudad AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a,
                                  '.$base_datos.'.tab_genera_ciudad bd
                            WHERE a.cod_criter = "3" AND     
                                  a.val_criter = bd.cod_ciudad AND                                                                      
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) cc
                   ON xx.cod_usuari = cc.cod_usuari '.( $mData["cod_ciudes"] != '' ? ' AND  cc.val_criter = "'.$mData["cod_ciudes"].'" /*DESTIN*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, be.nom_tipdes AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a,
                                  '.$base_datos.'.tab_genera_tipdes be
                            WHERE a.cod_criter = "4" AND  
                                  a.val_criter = be.cod_tipdes AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'" 
                   ) dd
                   ON xx.cod_usuari = dd.cod_usuari '.($mData["cod_tipdes"] != '' ? ' AND  dd.val_criter = "'.$mData["cod_tipdes"].'" /*TIPDES*/ ' : '').'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bf.nom_canalx AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a,
                                  '.$base_datos.'.tab_genera_canalx bf 
                            WHERE a.cod_criter = "6" AND
                                  a.val_criter = bf.con_consec AND                                                                           
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) ff
                   ON xx.cod_usuari = ff.cod_usuari '.( $mData["cod_canalx"] != '' ? ' AND  ff.val_criter = "'.$mData["cod_canalx"].'" /*Zona*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bg.nom_zonaxx AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a,
                                  '.$base_datos.'.tab_genera_zonasx bg 
                            WHERE a.cod_criter = "5" AND 
                                  a.val_criter = bg.cod_zonaxx AND                                                                          
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) ee
                   ON xx.cod_usuari = ee.cod_usuari '.( $mData["cod_zonaxx"] != '' ? ' AND  ee.val_criter = "'.$mData["cod_zonaxx"].'" /*Canal*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bh.nom_tiptra AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a,
                                  '.$base_datos.'.tab_genera_tiptra bh
                            WHERE a.cod_criter = "7" AND  
                                  a.val_criter = bh.cod_tiptra AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) gg
                   ON xx.cod_usuari = gg.cod_usuari '.( $mData["cod_tiptra"] != '' ? ' AND  gg.val_criter = "'.$mData["cod_tiptra"].'" /*Tiptra*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bi.nom_deposi AS nom_criter
                             FROM '.$base_datos.'.tab_detail_modcom a,
                                  '.$base_datos.'.tab_genera_deposi bi 
                            WHERE a.cod_criter = "8" AND  
                                  a.val_criter = bi.cod_deposi AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) hh
                   ON xx.cod_usuari = hh.cod_usuari '.( $mData["cod_deposi"] != '' ? ' AND  hh.val_criter = "'.$mData["cod_deposi"].'" /*DEPOSI*/ ' : ''   ).' '  ;
        $mListCr .= ' GROUP BY aa.cod_usuari ';


        $mListCr .= ' ) z, '.$base_datos.'.tab_genera_usuari y WHERE z.cod_usuari = y.cod_usuari AND y.ind_estado = 1';

    

       
        if( $mData["cod_tipdes"] != '4' )
        $Order = array( '7' => '7', '4' => '4', '2' => '2', '1' => '1', '5' => '5', '6' => '6', '8' => '8', '3' => '3' );
        else
        $Order = array( '7' => '7', '4' => '4', '2' => '2', '3' => '3', '1' => '1', '5' => '5', '6' => '6', '8' => '8' );
      

 
        $mListCr .= $mData["cod_tiptra"] != "" ? " \n AND z.ValCriter7 = '".$mData["cod_tiptra"]."' " : "";
        $mListCr .= $mData["cod_tipdes"] != "" ? " \n AND z.ValCriter4 = '".$mData["cod_tipdes"]."' " : "";
        $mListCr .= $mData["cod_produc"] != "" ? " \n AND z.ValCriter2 = '".$mData["cod_produc"]."' " : "";
        $mListCr .= $mData["cod_ciuori"] != "" ? " \n AND z.ValCriter1 = '".$mData["cod_ciuori"]."' " : "";
        $mListCr .=  $mData["cod_canalx"] != '' && $mData["cod_canalx"] != '0' && $mData["cod_canalx"] != NULL  ? " \n AND z.ValCriter6 = '".$mData["cod_canalx"]."' " :  "";





        

        $mListCr .= " ORDER BY 1 "; 

        $mQueryCanal =  $mListCr." \n AND z.ValCriter6 = '".$mData["cod_canalx"]."' ";

        #mail("nelson.liberato@intrared.net", "Query Matriz", "Matriz:<br>".var_export($mData, true)."<br>Query:<br>". $mQueryCanal);
       
 
        $_Data  = self::RetMatriz($mListCr );
      


      # consulta donde se reemplaza el usuario en la tabla                 
      for ($i = 0; $i < sizeof($_Data); $i++) {
          $mSelCor = "SELECT 
                      a.cod_reempl, a.cod_usuari, b.usr_emailx
                      FROM tab_restri_modcom a,
                           tab_genera_usuari b                             
                      WHERE a.cod_usuari = '" . $_Data[$i]['cod_usuari'] . "'
                      AND NOW() BETWEEN a.fec_inicia AND a.fec_finali
                      AND a.cod_reempl = b.cod_usuari";
 
          $aRemplaCorre = self::RetMatriz($mSelCor );

          if ($_Data[$i]["cod_usuari"] == $aRemplaCorre[0]["cod_usuari"]) {
              $_Data[$i]["cod_usuari"] = $aRemplaCorre[0]["cod_reempl"];
              $_Data[$i]["usr_emailx"] = $aRemplaCorre[0]["usr_emailx"];
          }
      }

      # Crea lista de array de los correos y usuarios        
      for ($i = 0; $i < sizeof($_Data); $i++) {
          $mCor[] = $_Data[$i]["usr_emailx"];
          $mUsr[] = $_Data[$i]["cod_usuari"];
      }

      # Crea string separados por coma, correos y usuarios
      $mArrTotCor = @join(',', $mCor);
      $mArrTotUsr = @join(',', $mUsr);

      # Reasigna el array que retorna con los tres campos
      $_Data = null;
      $_Data[0]["usr_emailx"] = $mArrTotCor;
      $_Data[0]["num_despac"] = $mData["num_despac"];
      $_Data[0]["cod_usuari"] = $mArrTotUsr;

     
      #echo "<pre>Nuevo Matriz:<br>"; print_r($_Data); echo "</pre>";
 
 
 
      return $_Data;
  }


  private function RetMatriz( $mQuery = NULL )
  {
      $mMatrix = array();
      $mResult = mysql_query($mQuery, self::$cConection); 
      while ($mRow = mysql_fetch_assoc($mResult)) {
         $mMatrix[] = $mRow;
      }      
      return $mMatrix;
  }


}


$mLink = mysql_connect("aglbd.intrared.net", "satt_faro", "sattfaro");
mysql_select_db("satt_faro");


$mMatriz = new PruebaMatrizCorona( $mLink );


?>
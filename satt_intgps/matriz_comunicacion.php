<?php 
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL & ~E_NOTICE);

define("BASE_DATOS", "satt_faro");  
class ListMatrizComunicacion
{

  static $cConnection = '';
  static $cArgv = '';

  function __construct($mIerda)
  {  
      self::$cArgv = $mIerda;
      self::setConexionBD("open");
      self::getDataMatriz();
      self::setConexionBD("close");
  }


 


  private function getDataMatriz()
  {

       
      $_REQUEST["letter"] = self::$cArgv[1];
      $_REQUEST["noveda"] = self::$cArgv[2];

      /*echo "<table width='100%'>";
      echo "<tr>";
      echo "<td>Código Usuario</td>";
      echo "<td>Nombre Usuario</td>";
      echo "<td>Novedad</td>";
      echo "<td>Tipo Correo</td>";
      echo "<td>Origen</td>";
      echo "<td>Producto</td>";
      echo "<td>Destino</td>";
      echo "<td>Tipo Operacion</td>";
      echo "<td>Zona</td>";
      echo "<td>Canal</td>";
      echo "<td>Tipo_Transporte</td>";
      echo "<td>Desposito</td>"; 
      echo "</tr>";*/
      $mFile = fopen("/var/www/html/ap/satt_faro/matrizCom/Matriz_".$_REQUEST["letter"].".csv", "a+");
      if(!$mFile) {
        self::setConexionBD("close");
        die("no se creo el archivo: "."matrizCom/Matriz_".$_REQUEST["letter"].".csv" );
      }
      fwrite($mFile,  "Codigo Usuario;Nombre Usuario;Novedad;Tipo Correo;Origen;Producto;Destino;tipo Operacion;Zona; Canal;Tipo_Transporte;Desposito\n");
      foreach (self::getUsuariosMatriz() AS $nKey => $mUsuari) 
      {
          foreach (self::getNovedadMatriz() AS $mKey => $mNoveda) 
          {
              foreach (array( "P", "S") AS $oKey => $mTypeDestin) 
              {
                $mMatriz = self::getDataByNovedad($mNoveda["cod_noveda"], $mUsuari["cod_usuari"], $mTypeDestin);

                if(sizeof($mMatriz) > 0) {
                  echo $mMatriz["cod_usuari"].";".$mNoveda["nom_noveda"]."\n";
                  /*echo "<tr>";
                  echo "<td>".$mMatriz["cod_usuari"]."</td>";
                  echo "<td>".$mMatriz["nom_usuari"]."</td>";
                  echo "<td>".$mNoveda["nom_noveda"]."</td>";
                  echo "<td>".$mMatriz["Tipo_Correo"]."</td>";
                  echo "<td>".$mMatriz["Origen"]."</td>";
                  echo "<td>".$mMatriz["Producto"]."</td>";
                  echo "<td>".$mMatriz["Destino"]."</td>";
                  echo "<td>".$mMatriz["Tipo_Despacho_Tipo_operacion"]."</td>";
                  echo "<td>".$mMatriz["Zona"]."</td>";
                  echo "<td>".$mMatriz["Canal"]."</td>";
                  echo "<td>".$mMatriz["Tipo_Transporte"]."</td>";
                  echo "<td>".$mMatriz["Desposito"]."</td>"; 
                  echo "</tr>";*/

                  @fwrite($mFile, $mMatriz["cod_usuari"].";".$mMatriz["nom_usuari"].";".$mNoveda["nom_noveda"].";".$mMatriz["Tipo_Correo"].";".$mMatriz["Origen"].";".$mMatriz["Producto"].";".$mMatriz["Destino"].";".$mMatriz["Tipo_Despacho_Tipo_operacion"].";".$mMatriz["Zona"].";".$mMatriz["Canal"].";".$mMatriz["Tipo_Transporte"].";".$mMatriz["Desposito"]."\n");
                  unset($mMatriz);
                }
                  
              }
          }

           #echo "<tr>";
           #       echo "<td colspan='12'>---------------------------------------------------------------</td>";
           #echo "</tr>";
          

          
      }
       fclose($mFile);
      echo "</table>";
  }

  private function getNovedadMatriz()
  {
    $mParam = '';
    if($_REQUEST["noveda"] != '') {
      $mParam = ' AND cod_noveda = "'.$_REQUEST["noveda"].'" '; 
    }
      
     $mSelect = '(
                    SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                    FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" '.$mParam.'
                    AND nom_noveda LIKE "%NER /%" 
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl ="1" '.$mParam.' AND
                         ( nom_noveda LIKE "%NEC /%" OR  nom_noveda LIKE "%NICC /%" )
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" '.$mParam.' AND
                          nom_noveda LIKE "%NED /%"
                  ) ';

        return self::getExecute($mSelect);

  }  

  private function getUsuariosMatriz()
  {
     $mSelect = 'SELECT a.cod_usuari 
                 FROM '.BASE_DATOS.'.tab_detail_modcom a WHERE a.cod_usuari LIKE "'.$_REQUEST["letter"].'%" GROUP BY a.cod_usuari';

        return self::getExecute($mSelect);

  }


  private function getDataByNovedad($mCodNoveda, $mCodUsuari, $mTypeDestin)
  {
      $mListCr = '
          SELECT z.cod_usuari, 
                 y.nom_usuari,
                 IF( "'.$mTypeDestin.'" = "P" , "Para", "Copia") AS Tipo_Correo,
                 z.ValCriter1 AS Nivel1, 
                 z.NomCriter1 AS Origen, 
                 z.ValCriter2 AS Nivel2, 
                 z.NomCriter2 AS Producto, 
                 z.ValCriter3 AS Nivel3, 
                 z.NomCriter3 AS Destino, 
                 z.ValCriter4 AS Nivel4,
                 z.NomCriter4 AS Tipo_Despacho_Tipo_operacion,
                 z.ValCriter5 As Nivel5, 
                 z.NomCriter5 As Zona, 
                 z.ValCriter6 AS Nivel6, 
                 z.NomCriter6 AS Canal, 
                 z.ValCriter7 AS Nivel7, 
                 z.NomCriter7 AS Tipo_Transporte, 
                 z.ValCriter8 AS Nivel8,
                 z.NomCriter8 AS Desposito


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
                              FROM   '.BASE_DATOS.'.tab_genera_modcom a 
                            WHERE  1 = 1 AND a.cod_usuari = "'.$mCodUsuari.'" GROUP BY a.cod_usuari LIMIT 1
                   ) xx
                    INNER JOIN 
                   (
                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, b.nom_ciudad AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a ,
                                  '.BASE_DATOS.'.tab_genera_ciudad b
                            WHERE a.cod_criter = "1" AND  
                                  a.val_criter = b.cod_ciudad AND                                                                         
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'" AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) aa
                    ON xx.cod_usuari = aa.cod_usuari '.($mData["cod_ciuori"] != '' ? ' AND  aa.val_criter = "'.$mData["cod_ciuori"].'" /*Origen*/ ' : '').'
                    INNER JOIN
                   (
                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bc.nom_produc AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a, 
                                  '.BASE_DATOS.'.tab_genera_produc bc 
                            WHERE a.cod_criter = "2" AND       
                                  a.val_criter = bc.cod_produc AND                                                                    
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'"  AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) bb
                    ON xx.cod_usuari = bb.cod_usuari '.($mData["cod_produc"] != '' ? ' AND  bb.val_criter = "'.$mData["cod_produc"].'" /*PRODUC*/ ' : '').'
                    LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bd.nom_ciudad AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_ciudad bd
                            WHERE a.cod_criter = "3" AND     
                                  a.val_criter = bd.cod_ciudad AND                                                                      
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'"  AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) cc
                   ON xx.cod_usuari = cc.cod_usuari '.( $mData["cod_ciudes"] != '' ? ' AND  cc.val_criter = "'.$mData["cod_ciudes"].'" /*DESTIN*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, be.nom_tipdes AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_tipdes be
                            WHERE a.cod_criter = "4" AND  
                                  a.val_criter = be.cod_tipdes AND                                                                         
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'" AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) dd
                   ON xx.cod_usuari = dd.cod_usuari '.($mData["cod_tipdes"] != '' ? ' AND  dd.val_criter = "'.$mData["cod_tipdes"].'" /*TIPDES*/ ' : '').'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bf.nom_canalx AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_canalx bf 
                            WHERE a.cod_criter = "6" AND
                                  a.val_criter = bf.con_consec AND                                                                           
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'"  AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) ff
                   ON xx.cod_usuari = ff.cod_usuari '.( $mData["cod_canalx"] != '' ? ' AND  ff.val_criter = "'.$mData["cod_canalx"].'" /*Zona*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bg.nom_zonaxx AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_zonasx bg 
                            WHERE a.cod_criter = "5" AND 
                                  a.val_criter = bg.cod_zonaxx AND                                                                          
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'"  AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) ee
                   ON xx.cod_usuari = ee.cod_usuari '.( $mData["cod_zonaxx"] != '' ? ' AND  ee.val_criter = "'.$mData["cod_zonaxx"].'" /*Canal*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bh.nom_tiptra AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_tiptra bh
                            WHERE a.cod_criter = "7" AND  
                                  a.val_criter = bh.cod_tiptra AND                                                                         
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'"  AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) gg
                   ON xx.cod_usuari = gg.cod_usuari '.( $mData["cod_tiptra"] != '' ? ' AND  gg.val_criter = "'.$mData["cod_tiptra"].'" /*Tiptra*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bi.nom_deposi AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_deposi bi 
                            WHERE a.cod_criter = "8" AND  
                                  a.val_criter = bi.cod_deposi AND                                                                         
                                  a.cod_noveda = "'.$mCodNoveda.'" AND 
                                  a.ind_tipres = "'.$mTypeDestin.'"  AND
                                  a.cod_usuari = "'.$mCodUsuari.'"  
                   ) hh
                   ON xx.cod_usuari = hh.cod_usuari '.( $mData["cod_deposi"] != '' ? ' AND  hh.val_criter = "'.$mData["cod_deposi"].'" /*DEPOSI*/ ' : ''   ).' '  ;
       # $mListCr .= ' GROUP BY aa.cod_usuari   ) z, '.BASE_DATOS.'.tab_genera_usuari y WHERE z.cod_usuari = y.cod_usuari   ORDER BY 2 '; 
        $mListCr .= '   ) z, '.BASE_DATOS.'.tab_genera_usuari y WHERE z.cod_usuari = y.cod_usuari   ORDER BY 2 '; 
        
        #echo "<pre>"; print_r($mListCr); echo "</pre>";
        $mReturn = self::getExecute(  $mListCr  );
        return $mReturn[0];
  }



  private function setConexionBD( $mAction = "close")
  {
    if($mAction == 'open')
    {
        self::$cConnection = mysqli_connect("aglbd.intrared.net", "satt_faro", "sattfaro", BASE_DATOS);
        if(!self::$cConnection){
          die("No se pudo hacer la conexion al servidor".mysqli_error( ) );
        }
    }
    else{
        if(!mysqli_close(self::$cConnection) ){
          die("No se pudo cerrar la conexion!");
        }
    }
  }


  private function getExecute( $mQuery = '')
  {
    $mArray = array();

    if( $result = mysqli_query(self::$cConnection , $mQuery) )
    {
        
        while ($row = $result->fetch_assoc()) 
        {
          $mArray[] = $row;
      
        }

        $result->free(); 
    } else{
      self::setConexionBD("close");
      die("No se pudo ejecutar la query:\n".$mQuery);
    }

    return $mArray;
  }


}

$mComunica = new ListMatrizComunicacion($argv);

?>
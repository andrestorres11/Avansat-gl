<?php 

class generaData
{
  var $conexion;
  var $tip_transp;

  public function __construct( $co )
  {
    $this -> conexion = $co;
    $this -> tip_transp = array( '1' => 'FLOTA PROPIA', '2' => 'TERCEROS', '3' => 'EMPRESAS' );
  }

  private function getCitcar( $num_despac )
  {
    $mSelect = "SELECT ind_cumcar 
                  FROM ".BASE_DATOS.".tab_despac_sisext
                 WHERE num_despac = '".$num_despac."'";
  
    $consul = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consul -> ret_matrix( 'a' );
    return $_DESPAC[0];
  }

  private function getClient( $num_despac )
  {
    $mSelect = "SELECT nom_destin
                  FROM ".BASE_DATOS.".tab_despac_destin
                 WHERE num_despac = '".$num_despac."' 
                 ORDER BY fec_citdes, hor_citdes";
    
    $consul = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consul -> ret_matrix( 'a' );
    
    if( sizeof( $_DESPAC ) > 0 )
    {
      $mDestin = '';
      foreach( $_DESPAC as $row )
      {
        $mDestin .= $row['nom_destin'] != '' ? $row['nom_destin'].'<br>' : 'DESCONOCIDO<br>';
      }
    }
    else
    {
      $mDestin = 'NO TIENE CLIENTES ASIGNADOS';
    }

    return $mDestin;
  }

  private function getUniqueClient( $num_despac, $num_docume )
  {
    $mSelect = "SELECT nom_destin
                  FROM ".BASE_DATOS.".tab_despac_destin
                 WHERE num_despac = '".$num_despac."' AND
                       num_docume = '".$num_docume."' 
                 ORDER BY fec_citdes, hor_citdes";
    
    $consul = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consul -> ret_matrix( 'a' );
    
    return $_DESPAC[0]['nom_destin'];
  }

  private function getFechasCitasDescargue( $num_despac, $num_factur )
  {
    $mSelect = "SELECT
                  a.fec_inides, a.fec_findes, IF( a.fec_llecli <> '0000-00-00 00:00:00', a.fec_llecli, '' ) AS fec_llecli,
                  a.num_despac, a.fec_citdes, a.hor_citdes, 
                  a.ind_cumdes, a.fec_cumdes, a.nom_destin,
                  a.nov_cumdes, b.nom_noveda   
                FROM 
                  ".BASE_DATOS.".tab_despac_destin a
                LEFT JOIN 
                  ".BASE_DATOS.".tab_genera_noveda b
                  ON
                  a.nov_cumdes = b.cod_noveda
                WHERE
                  a.num_despac = '".$num_despac."' AND
                  a.num_docume = '".$num_factur."' ";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESTIN = $consulta -> ret_matriz();

    $ind_cumplido = '0';
    if( stristr( strtolower( $_DESTIN[0]['nom_destin'] ), 'corona' ) || stristr( strtolower( $_DESTIN[0]['nom_destin'] ), 'sodimac' ) )
    {
      echo "1";
      if( $_DESTIN[0]['ind_cumdes'] == '1' )
        $ind_cumplido = '1';
      else
        $ind_cumplido = '0';
    }
    elseif( $_DESTIN[0]['fec_citdes'] != '0000-00-00' && $_DESTIN[0]['hor_citdes'] != '0000:00:00' )
    {
      $fec_finali = $_DESTIN[0]['fec_citdes'] . " " . $_DESTIN[0]['hor_citdes'];
      if( $_DESTIN[0]['ind_cumdes'] == '1' && $this -> dateDiff( $_DESTIN[0]['fec_cumdes'], $fec_finali, 30 ) )
        $ind_cumplido = '1';
      else
        $ind_cumplido = '0';
    }
    else
    {
      $fec_finali = $_DESTIN[0]['fec_citdes'] . " " . $_DESTIN[0]['hor_citdes'];
      if( $_DESTIN[0]['ind_cumdes'] == '1' && $fec_finali == $_DESTIN[0]['fec_cumdes'] )
        $ind_cumplido = '1';
      else
        $ind_cumplido = '0';
    }
    
    $to_return = array();
    $to_return['ind_cumpli'] = $ind_cumplido;
    $to_return['fec_cumdes'] = $_DESTIN[0]['fec_cumdes'];
    $to_return['fec_inides'] = $_DESTIN[0]['fec_inides'];
    $to_return['fec_findes'] = $_DESTIN[0]['fec_findes'];
    $to_return['fec_llecli'] = $_DESTIN[0]['fec_llecli'];
    $to_return['nom_noveda'] = $_DESTIN[0]['nom_noveda'];
    return $to_return;

  }

  private function dateDiff( $fec_inicia, $fec_finali, $limit ) 
  {
    $time_ini = strtotime( $fec_inicia );
    $time_fin = strtotime( $fec_finali );
    
    $diff = round( ( $time_fin - $time_ini ) / 60 );
    
    return $diff <= $limit ? true : false;
  }

  private function GetUltnov( $num_despac ) 
  {
    $mSelect = "SELECT ln.* FROM 
          (
              SELECT a.cod_noveda, a.fec_noveda, b.nom_noveda 
                FROM ".BASE_DATOS.".tab_despac_noveda a, 
                     ".BASE_DATOS.".tab_genera_noveda b 
               WHERE a.cod_noveda = b.cod_noveda 
                 AND a.num_despac = '".$num_despac."'
            
            UNION
              
              SELECT a.cod_noveda, b.nom_noveda, a.fec_contro AS fec_noveda
                FROM ".BASE_DATOS.".tab_despac_contro a, 
                     ".BASE_DATOS.".tab_genera_noveda b 
               WHERE a.cod_noveda = b.cod_noveda 
                 AND a.num_despac = '".$num_despac."'
          ) AS ln 
          ORDER BY 2 DESC
          LIMIT 1";
    $consul = new Consulta( $mSelect, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz[0];
  }

  private function getUltNovSolucion( $num_despac )
  {
    $mSelect = "SELECT a.cod_noveda, b.nom_noveda, a.fec_noveda
                  FROM ".BASE_DATOS.".tab_protoc_asigna a
             LEFT JOIN ".BASE_DATOS.".tab_genera_noveda b
                    ON a.cod_noveda = b.cod_noveda 
                 WHERE a.num_despac = '".$num_despac."' 
                   AND a.ind_ejecuc = '0'
                 ORDER BY a.fec_noveda DESC 
                 LIMIT 1";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mSoluci = $consulta -> ret_matriz();
    return $mSoluci[0];
  } 

  public function getDataCitaDescargue( $arr_despac )
  {
    $mSelect = "SELECT a.num_despac, 
                       b.num_despac AS num_viajex, 
                       b.cod_manifi,
                       b.fec_despac, 
                       c.nom_tipdes, 
                UPPER( d.nom_ciudad ) AS nom_ciuori,
                UPPER( e.nom_ciudad ) AS nom_ciudes, 
               CONCAT( b.fec_citcar, ' ', b.hor_citcar ) AS fec_citcar,
                       b.nom_sitcar, 
                       b.val_pesoxx, 
                       b.obs_despac,
                       b.cod_conduc, 
                       b.nom_conduc, 
                       b.con_telmov, 
                       b.num_solici, 
                       b.num_pedido, 
                       b.num_placax,
                       b.tip_vehicu, 
                       b.nom_poseed, 
                       b.tip_transp,
                       f.nom_produc
                  FROM ".BASE_DATOS.".tab_despac_despac a
             LEFT JOIN ".BASE_DATOS.".tab_despac_corona b 
                    ON a.num_despac = b.num_dessat
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes c
                    ON b.cod_tipdes = c.cod_tipdes 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d
                    ON b.cod_ciuori = d.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e
                    ON b.cod_ciudes = e.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc f
                    ON b.cod_mercan = f.cod_produc 
                 WHERE a.num_despac = '".$arr_despac[0]."' "; 
    
    $consul = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consul -> ret_matrix( 'a' );
    
      for( $i = 0, $lim = sizeof( $_DESPAC ); $i < $lim; $i++ )
      {
        $row = $_DESPAC[$i];
        $cum_citcar = $this -> getCitcar( $row['num_despac'] );
        $nom_client = $this -> getUniqueClient( $row['num_despac'], $arr_despac[1] );
        $ind_adicio = $this -> getFechasCitasDescargue( $row['num_despac'], $arr_despac[1] );

        if( $ind_soluci == '1' )
        {
          $ult_noveda = $this -> getUltNovSolucion( $row['num_despac'] );
        }
        else
        {
          $ult_noveda = $this -> getUltnov( $row['num_despac'] );
        }
        $style = $i % 2 == 0 ? 'cellInfo' : 'cellInfo';
        $mHtml .= '<tr class="row">';
          $mHtml .= '<td class="'.$style.'" align="center">
                       <a style="text-decoration:none; color:#006F1A;" href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&tie_ultnov=0&opcion=1" target="_blank">'.$row['num_despac'].'</a>
                     </td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_viajex'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_manifi'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_despac'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_tipdes'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciuori'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciudes'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_citcar'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.( $cum_citcar['ind_cumcar'] == '1' ? 'SI' : 'NO' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_sitcar'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['val_pesoxx'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['obs_despac'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.( $ult_noveda['nom_noveda'] != '' ? $ult_noveda['nom_noveda'] :'-' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_conduc'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_conduc'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['con_telmov'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_solici'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.( $row['num_pedido'] != '' ? $row['num_pedido'] : 'N/A' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_placax'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['tip_vehicu'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_poseed'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$this -> tip_transp[ $row['tip_transp'] ].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_produc'].'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.$nom_client.'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.($ind_adicio['ind_cumpli'] == '1' ? 'SI': 'NO' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.($ind_adicio['nom_noveda'] != '' ? $ind_adicio['nom_noveda'] : 'N/A' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.($ind_adicio['fec_cumdes'] != '' ? $ind_adicio['fec_cumdes'] : 'N/A' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.($ind_adicio['fec_inides'] != '' ? $ind_adicio['fec_inides'] : 'N/A' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.($ind_adicio['fec_findes'] != '' ? $ind_adicio['fec_findes'] : 'N/A' ).'</td>';
          $mHtml .= '<td class="'.$style.'" align="center">'.($ind_adicio['fec_llecli'] != '' ? $ind_adicio['fec_llecli'] : 'N/A' ).'</td>';
        $mHtml .= '</tr>';
      }
    return utf8_encode( $mHtml );
  } 

  public function getDataSolucionNovedad( $arr_despac, $infnoveda )
  {
    $mSelect = "SELECT a.fec_noveda, a.fec_noved2, TIMESTAMPDIFF( MINUTE, a.fec_noveda, a.fec_noved2 ) AS diff,
                       a.usr_asigna, a.ind_ejecuc, UPPER( b.nom_usuari ) AS nom_usuari , UPPER( c.nom_usuari ) AS nom_usreje, a.usr_ejecut
                  FROM ".BASE_DATOS.".tab_protoc_asigna a 
             LEFT JOIN ".BASE_DATOS.".tab_genera_usuari b 
                    ON a.usr_asigna = b.cod_usuari
             LEFT JOIN ".BASE_DATOS.".tab_genera_usuari c 
                    ON a.usr_ejecut = c.cod_usuari 
                 WHERE num_consec = '".$arr_despac[1]."' 
                   AND num_despac = '".$arr_despac[0]."'";
    $consul = new Consulta( $mSelect, $this -> conexion );
    $_ADICIO = $consul -> ret_matrix( 'a' );

    #Valida si el despacho es un consolidado.  
    $mQueryConsol = " SELECT cod_deshij FROM ".BASE_DATOS.".tab_consol_despac WHERE cod_despad = '".$arr_despac[0]."' ";
    $mConsol = new Consulta( $mQueryConsol, $this -> conexion );
    $mConsox = $mConsol -> ret_matriz( 'a' );

 
     $mDespac = $arr_despac[0];
     if( sizeof($mConsox) >0)
     {
        $mDespac = $mConsox[0][0];

     }


    $mSelect = "SELECT b.num_dessat AS num_despac, 
                       b.num_despac AS num_viajex, 
                       b.cod_manifi,
                       b.fec_despac, 
                       c.nom_tipdes, 
                UPPER( d.nom_ciudad ) AS nom_ciuori,
                UPPER( e.nom_ciudad ) AS nom_ciudes, 
               CONCAT( b.fec_citcar, ' ', b.hor_citcar ) AS fec_citcar,
                       b.nom_sitcar, 
                       b.val_pesoxx, 
                       b.obs_despac,
                       b.cod_conduc, 
                       b.nom_conduc, 
                       b.con_telmov, 
                       b.num_solici, 
                       b.num_pedido, 
                       b.num_placax,
                       b.tip_vehicu, 
                       b.nom_poseed, 
                       b.tip_transp,
                       f.nom_produc
                  FROM ".BASE_DATOS.".tab_despac_corona b 
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes c
                    ON b.cod_tipdes = c.cod_tipdes 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d
                    ON b.cod_ciuori = d.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e
                    ON b.cod_ciudes = e.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc f
                    ON b.cod_mercan = f.cod_produc 
                 WHERE b.num_dessat = '".$mDespac."' "; 

 

    $consul = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consul -> ret_matrix( 'a' );

    $cum_citcar = $this -> getCitcar( $mDespac );
    
	
	
    $i = 0;
    $row = $_DESPAC[$i];
    $adi = $_ADICIO[$i];

    $style = $i % 2 == 0 ? 'cellInfo' : 'cellInfo';
    $mHtml .= '<tr class="row">';
      $mHtml .= '<td class="'.$style.'" align="center">
                   <a style="text-decoration:none; color:#006F1A;" href="index.php?cod_servic=3302&window=central&despac='.$arr_despac[0].'&tie_ultnov=0&opcion=1" target="_blank">'.$arr_despac[0].'</a>
                 </td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_viajex'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_manifi'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_despac'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_tipdes'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciuori'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciudes'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_citcar'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.( $cum_citcar['ind_cumcar'] == '1' ? 'SI' : 'NO' ).'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_sitcar'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['val_pesoxx'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['obs_despac'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.( $ult_noveda != '' ? $ult_noveda :'-' ).'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_conduc'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_conduc'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['con_telmov'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_solici'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.( $row['num_pedido'] != '' ? $row['num_pedido'] : 'N/A' ).'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_placax'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['tip_vehicu'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_poseed'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$this -> tip_transp[ $row['tip_transp'] ].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_produc'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$adi['fec_noveda'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.( $adi['fec_noved2'] != '' ? $adi['fec_noved2'] : 'N/A' ).'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.( $adi['diff'] != '' ? $adi['diff'].' Min(s)' : 'N/A' ).'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$adi['nom_usuari'].'('.$adi['usr_asigna'].')</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.( $adi['fec_noved2'] != '' ? $adi['nom_usreje'].'('.$adi['usr_ejecut'].')' : 'N/A' ).'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$infnoveda.'</td>';
    $mHtml .= '</tr>';

    return utf8_encode( $mHtml );
  } 

  public function getDataGeneralDespac( $num_despac, $ind_soluci = NULL, $ind_posici = NULL)
  {
    $mText = $ind_posici == 's' ? 'Novedades' : 'Despachos'; 
  	$mCant = explode(",",  $num_despac);
    $mSelect = "SELECT a.num_despac, 
                       IF(b.num_despac IS NULL, 'NR', b.num_despac) AS num_viajex, 
                       IF(b.cod_manifi IS NULL, a.cod_manifi, b.cod_manifi) AS cod_manifi,
  			 	             IF(b.fec_despac IS NULL, a.fec_despac, b.fec_despac) AS fec_despac, 
                       IF(c.nom_tipdes IS NULL, l.nom_tipdes, c.nom_tipdes) AS nom_tipdes, 
                       IF(d.nom_ciudad IS NULL, m.nom_ciudad, d.nom_ciudad) AS nom_ciuori,
                       IF(e.nom_ciudad IS NULL, n.nom_ciudad, e.nom_ciudad) AS nom_ciudes, 
               CONCAT( b.fec_citcar, ' ', b.hor_citcar ) AS fec_citcar,
                       IF(b.nom_sitcar IS NULL, a.nom_sitcar, b.nom_sitcar) AS nom_sitcar, 
                       IF(b.val_pesoxx IS NULL, a.val_pesoxx, b.val_pesoxx) AS val_pesoxx, 
                       IF(b.obs_despac IS NULL, a.obs_despac, b.obs_despac) AS obs_despac,
                       IF(b.cod_conduc IS NULL, g.cod_conduc, b.cod_conduc) AS cod_conduc, 
                       IF(b.nom_conduc IS NULL, h.abr_tercer, b.nom_conduc) AS nom_conduc, 
                       IF(b.con_telmov IS NULL, a.con_telmov, b.con_telmov) AS con_telmov, 
                       IF(b.num_solici IS NULL, '-', b.num_solici) AS num_solici, 
                       IF(b.num_pedido IS NULL, '-', b.num_pedido) AS num_pedido, 
                       IF(b.num_placax IS NULL, g.num_placax, b.num_placax) AS num_placax,
                       IF(b.tip_vehicu IS NULL, 'No registrado', b.tip_vehicu) AS tip_vehicu, 
                       IF(b.nom_poseed IS NULL, k.abr_tercer, b.nom_poseed) AS nom_poseed, 
                       b.tip_transp,
                       f.nom_produc
                  FROM ".BASE_DATOS.".tab_despac_despac a
             LEFT JOIN ".BASE_DATOS.".tab_despac_corona b 
                    ON a.num_despac = b.num_dessat
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes c
                    ON b.cod_tipdes = c.cod_tipdes 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d
                    ON b.cod_ciuori = d.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e
                    ON b.cod_ciudes = e.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc f
                    ON b.cod_mercan = f.cod_produc 
             LEFT JOIN ".BASE_DATOS.".tab_despac_vehige g 
                    ON a.num_despac = g.num_despac 
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer h 
                    ON g.cod_conduc = h.cod_tercer 
             LEFT JOIN ".BASE_DATOS.".tab_vehicu_vehicu i 
                    ON g.num_placax = i.num_placax 
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipveh j 
                    ON i.cod_tipveh = j.cod_tipveh 
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
                    ON i.cod_tenedo = k.cod_tercer 
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes l 
                    ON a.cod_tipdes = l.cod_tipdes 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad m
                    ON a.cod_ciuori = m.cod_ciudad 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad n
                    ON a.cod_ciudes = n.cod_ciudad 
        				 WHERE a.num_despac IN(".$num_despac.")
              ORDER BY a.num_despac"; 
  	
  	$consul = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consul -> ret_matrix( 'a' );

    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
      
      $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead99" colspan="24">Se Encontr&oacute; un Total de '.sizeof( $mCant )." ".$mText.' </td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead">Despacho SAT</td>';
        $mHtml .= '<td class="CellHead">Viaje</td>';
        $mHtml .= '<td class="CellHead">Monifiesto</td>';
        $mHtml .= '<td class="CellHead">Fecha Despacho</td>';
        $mHtml .= '<td class="CellHead">Tipo Despacho</td>';
        $mHtml .= '<td class="CellHead">Origen</td>';
        $mHtml .= '<td class="CellHead">Destino</td>';
        $mHtml .= '<td class="CellHead">Fecha Cita Cargue</td>';
        $mHtml .= '<td class="CellHead">cumplimiento Cita Cargue</td>';
        $mHtml .= '<td class="CellHead">Nombre Sitio Cargue</td>';
        $mHtml .= '<td class="CellHead">Peso(Kg)</td>';
        $mHtml .= '<td class="CellHead">Observaciones</td>';
        $mHtml .= '<td class="CellHead">Novedad</td>';
        $mHtml .= '<td class="CellHead">Fecha Novedad</td>';
        $mHtml .= '<td class="CellHead">C.C. Conductor</td>';
        $mHtml .= '<td class="CellHead">Nombre Conductor</td>';
        $mHtml .= '<td class="CellHead">Celular Conductor</td>';
        $mHtml .= '<td class="CellHead">Solicitud</td>';
        $mHtml .= '<td class="CellHead">Pedido</td>';
        $mHtml .= '<td class="CellHead">Placa</td>';
        $mHtml .= '<td class="CellHead">Tipo Vehiculo</td>';
        $mHtml .= '<td class="CellHead">Poseedor</td>';
        $mHtml .= '<td class="CellHead">Tipo Transportadora</td>';
        $mHtml .= '<td class="CellHead">Mercancia/Negocio</td>';
        $mHtml .= '<td class="CellHead">Cliente(s)</td>';
      $mHtml .= '</tr>';

      $nov_pensol = 0;
      for( $i = 0, $lim = sizeof( $_DESPAC ); $i < $lim; $i++ )
      {
        $row = $_DESPAC[$i];
        $cum_citcar = $this -> getCitcar( $row['num_despac'] );
        $nom_client = $this -> getClient( $row['num_despac'] );
        if ($ind_posici == 's') {
          $nov_pensol = $this -> getNovPenSoluci($row['num_despac']);
        }
        elseif( $ind_soluci == '1' )
        {
          $ult_noveda = $this -> getUltNovSolucion( $row['num_despac'] );
        }
        else
        {
          $ult_noveda = $this -> getUltnov( $row['num_despac'] );
        }
        if( $nov_pensol ){

          for ($j=0; $j < sizeof($nov_pensol); $j++) { 
            
              $style = $i % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
              $mHtml .= '<tr>';
                $mHtml .= '<td class="'.$style.'" align="center">
                             <a style="text-decoration:none; color:#006F1A;" href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&tie_ultnov=0&opcion=1" target="_blank">'.$row['num_despac'].'</a>
                           </td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_viajex'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_manifi'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_despac'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_tipdes'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciuori'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciudes'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_citcar'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.( $cum_citcar['ind_cumcar'] == '1' ? 'SI' : 'NO' ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_sitcar'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['val_pesoxx'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$nov_pensol[$j][obs_noveda].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$nov_pensol[$j][nom_noveda].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$nov_pensol[$j][fec_noveda].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_conduc'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_conduc'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['con_telmov'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_solici'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.( $row['num_pedido'] != '' ? $row['num_pedido'] : 'N/A' ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_placax'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['tip_vehicu'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_poseed'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$this -> tip_transp[ $row['tip_transp'] ].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_produc'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$nom_client.'</td>';
              $mHtml .= '</tr>';
            }
          } 
          else{
            $style = $i % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
              $mHtml .= '<tr>';
                $mHtml .= '<td class="'.$style.'" align="center">
                             <a style="text-decoration:none; color:#006F1A;" href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&tie_ultnov=0&opcion=1" target="_blank">'.$row['num_despac'].'</a>
                           </td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_viajex'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_manifi'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_despac'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_tipdes'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciuori'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_ciudes'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['fec_citcar'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.( $cum_citcar['ind_cumcar'] == '1' ? 'SI' : 'NO' ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_sitcar'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['val_pesoxx'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['obs_despac'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.( $ult_noveda['nom_noveda'] != '' ? $ult_noveda['nom_noveda'] :'-' ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.( $ult_noveda['fec_noveda'] != '' ? $ult_noveda['fec_noveda'] :'-' ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['cod_conduc'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_conduc'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['con_telmov'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_solici'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.( $row['num_pedido'] != '' ? $row['num_pedido'] : 'N/A' ).'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_placax'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['tip_vehicu'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_poseed'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$this -> tip_transp[ $row['tip_transp'] ].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_produc'].'</td>';
                $mHtml .= '<td class="'.$style.'" align="center">'.$nom_client.'</td>';
              $mHtml .= '</tr>';
          }
        }
    $mHtml .= '</table>';

    return $mHtml;
  }

  function getNovPenSoluci($num_despac){

    $mSql = "SELECT b.nom_noveda, a.obs_noveda, a.fec_noveda 
               FROM tab_protoc_asigna a 
         INNER JOIN tab_genera_noveda b 
                 ON a.cod_noveda = b.cod_noveda 
              WHERE a.ind_ejecuc = '0'
                AND a.num_despac = '".$num_despac."'
          ORDER BY a.num_consec";

    $consul = new Consulta( $mSql, $this -> conexion );
    return $mResult = $consul -> ret_matrix( 'a' );
  }
}
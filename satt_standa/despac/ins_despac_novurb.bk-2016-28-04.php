<?php 

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

class InformDespacUrbano
{
  var $conexion = NULL;
  var $color = NULL;

  function __construct( $conexion, $mData )
  {
    $this -> conexion = $conexion;
    $this -> principal( $mData );
  }
  
  function principal( $mData )
  {
    if( !isset( $mData['option'] ) )
    {
      $this -> GetInform( $mData );
    }
    else
    {
      switch ( $mData['option'] )
      {
        default:
          $this -> GetInform( $mData );
        break;
      }    
    }
  }
  
  function GetInform( $mData )
  {
    $this -> Style();
    
    include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_transi.php' );
    $obj_destra = new DespacTransi( $this -> conexion, $mData );
    $_DESPAC = $obj_destra -> GetDespacTransp(1);
    $_ALARMA = $this -> GetGeneraAlarma();
    
    if( $_DESPAC )
    { 
      $encab = sizeof( $_DESPAC ) == 1 ? "Se Encontró Una Empresa con Despachos Urbanos" : "Se Encontró un total de ".sizeof( $_DESPAC )." Empresas con Despachos Urbanos" ;
      $formulario = new Formulario("index.php", "post", "Despachos Urbanos", "form", "", "", "100%");
      $formulario->nueva_tabla();
      $formulario->linea( $encab, 0, "t" );
      $mHtml = '';
      $mHtml .= '<table width="100%">';
      $mHtml .='<tr>';
      $ALA = array();
      foreach( $_ALARMA as $row )
      {
        $mHtml .= '<td width="25%" style="background:#'.$row['cod_colorx'].';">'.$row['nom_alarma'].' = '.$row['can_tiempo'].'Min</td>'; 
        $ALA[ $row['can_tiempo'] ] = $row['cod_colorx'];
      }
      $mHtml .='</tr>';
      $mHtml .= '</table>';
      
      $mHtml .= '<table width="100%">';
      
      $despac = array();
      
      foreach ( $_DESPAC as $key => $transp )
      {
        $despac = $transp['all_despac'];

        $mHtml .='<tr>';
          $mHtml .='<td class="cellHead2" colspan="10">EMPRESA: <b>'. $this -> GetTercer( $key ) .'</b></td>';
        $mHtml .='</tr>';
        $mHtml .='<tr>';
          $mHtml .='<td class="cellHead" >DESPACHO</td>';
          $mHtml .='<td class="cellHead" >TIEMPO</td>';
          $mHtml .='<td class="cellHead" >A CARGO EMPRESA</td>';
          $mHtml .='<td class="cellHead" >MANIFIESTO</td>';
          $mHtml .='<td class="cellHead" >ORIGEN</td>';
          $mHtml .='<td class="cellHead" >DESTINO</td>';
          $mHtml .='<td class="cellHead" >TRANSPORTADORA</td>';
          $mHtml .='<td class="cellHead" >PLACA</td>';
          $mHtml .='<td class="cellHead" >CONDUCTOR</td>';
          $mHtml .='<td class="cellHead" >CELULAR</td>';
        $mHtml .='</tr>';
        
        $ind_color = -1;
        foreach( $despac as $llave => $hora )
        {
          if( $llave != 'can_despac')
          {
            
            $despachos = explode( ',', $hora );
            for( $j = 0; $j < count( $despachos );  $j++ )
            {
              $num_despac = trim( $despachos[$j] );
              $detalles = $obj_destra -> GetDespacData( $num_despac, $mData );
              
              $dif_minuto = $detalles['dif_minuto'];
              $ind_defini = $detalles['can_defini'] == 1 ? 'SI' : 'NO';
              $nom_ciuori = $detalles['nom_ciuori'] . ' (' . substr($detalles['nom_depori'], 0, 4) . ')';
              $nom_ciudes = $detalles['nom_ciudes'] . ' (' . substr($detalles['nom_depdes'], 0, 4) . ')';
              foreach ( $ALA as $time => $alarm )
              {
                $ultimo = $alarm;
                $limite = $time;
              }
              
              if( $dif_minuto <= 0 )
                $this -> color = 'FFFFFF';
              elseif( $dif_minuto >= $limite )
                $this -> color = $ultimo;
              else
                $this -> color = $this -> getColor( $dif_minuto );
              
              $mHtml .='<tr class="row">';
                $mHtml .='<td class="cellInfo" style="background:#'.$this -> color.';" ><a href="index.php?cod_servic=3302&window=central&despac='.$num_despac.'&opcion=1">'.$num_despac.'</a></td>';
                $mHtml .='<td class="cellInfo" align="center">'.$dif_minuto.'</td>';
                $mHtml .='<td class="cellInfo" align="center">'.$ind_defini.'</td>';
                $mHtml .='<td class="cellInfo" >'.$detalles['cod_manifi'].'</td>';
                $mHtml .='<td class="cellInfo" >'.$nom_ciuori.'</td>';
                $mHtml .='<td class="cellInfo" >'.$nom_ciudes.'</td>';
                $mHtml .='<td class="cellInfo" >'.$detalles['nom_transp'].'</td>';
                $mHtml .='<td class="cellInfo" >'.$detalles['num_placax'].'</td>';
                $mHtml .='<td class="cellInfo" >'.$detalles['nom_conduc'].'</td>';
                $mHtml .='<td class="cellInfo" >'.$detalles['cel_conduc'].'</td>';
              $mHtml .='</tr>';
            }
            $ind_color++;
          }
        }
              
      }
      
      $mHtml .= '</table>'; 
    }
    echo $mHtml; 
  }
  
  function getColor( $dif_minuto )
  {
    $mSql = "SELECT a.cant_tiempo, a.cod_colorx 
            FROM " . BASE_DATOS . ".tab_genera_alarma a 
            WHERE a.cant_tiempo > ".$dif_minuto." 
            ORDER BY 1 
            LIMIT 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix();
    return $matriz[0][1];
  }
  
  function GetTercer( $cod_tercer )
  {
    $mSql = "SELECT UPPER( abr_tercer ) AS nom_tercer
            FROM " . BASE_DATOS . ".tab_tercer_tercer 
            WHERE cod_tercer = '".$cod_tercer."'
            LIMIT 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( );
    return $matriz[0][0];
  }
  
  function GetGeneraAlarma()
  {
    $mSql = "SELECT a.cod_alarma, 
                 UPPER( a.nom_alarma ) AS nom_alarma, 
                 a.cant_tiempo AS can_tiempo, 
                 a.cod_colorx 
            FROM " . BASE_DATOS . ".tab_genera_alarma a ORDER BY 1 ";
    $consul = new Consulta( $mSql, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    return $matriz;
  }
  
   function Style(){
		echo "	<style>
            .cellHead
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              text-align:center;
            }

            .cellHead2
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              text-align:left;
            }
            
            .footer
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:right;
            }

            .row:hover > td{
              background-color: #9ad9ae;
            }
            .cellInfo
            {
              padding:5px 10px;
              background-color:#fff;
              border:1px solid #ccc;
            }

            .cellInfo2
            {
              padding:5px 10px;
              background-color:#9ad9ae;
              border:1px solid #ccc;
            }



            .label
            {
              font-size:12px;
              font-weight:bold;
            }

            .select
            {
              background-color:#fff;
              border:1px solid #009617;
            }

            .boton
            {
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              border:1px solid #fff;
              padding:3px 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
            }

            .boton:hover
            {
              background:#fff;
              color:#00661b;
              border:1px solid #00661b;
              cursor:pointer;
            }
    </style>";
	}

}

$InformDespacUrbano = new InformDespacUrbano( $this -> conexion, $_REQUEST );
?>
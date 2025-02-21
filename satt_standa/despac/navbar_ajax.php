<?php
/*! \file: navbar_ajax.php
 *  \brief: Ajax para los procesos de las recomendaciones
 *  \author: Ing. Cristian Torres
 *  \version: 1.0
 *  \date: 20/02/2025
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

/*! \class: AjaxRecome
 *  \brief: Ajax para los procesos de las recomendaciones
 */
class AjaxNavBar
{

  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this->conexion = $co;
    $this->usuario = $us;
    $this->cod_aplica = $ca;
    $this->principal();
  }

  function principal()
  {
    @include( "../lib/ajax.inc" );

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST[Option])
    {
      case "GetBadge":
        AjaxNavBar::GetBadge();
        break;

      default:
        header('Location: index.php?window=central&cod_servic=1366&menant=1366');
        break;
    }
  }

  function GetBadge(){
    $cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
    $fec_actual = date('Y-m-d H:i:s');
    $mSql = "SELECT a.cod_consec FROM ".BASE_DATOS.".tab_monito_encabe a 
                WHERE '".$fec_actual."' BETWEEN a.fec_inicia AND a.fec_finalx 
                AND a.cod_usuari = '".$cod_usuari."'
                AND a.ind_estado = 1
                    ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $consecutivo = $mConsult -> ret_matrix('a');
    $consecutivo = $consecutivo[0]['cod_consec'];

    $mSql = "SELECT a.cod_tercer FROM ".BASE_DATOS.".tab_monito_detall a 
                WHERE a.cod_consec = '".$consecutivo."'
                AND a.ind_estado = 1 ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $asignaciones = $mConsult -> ret_matrix('a');

    $asignaciones_array = array();
    if (!empty($asignaciones)) {
        foreach ($asignaciones as $fila) {
            $asignaciones_array[] = "'" . $fila['cod_tercer'] . "'";
        }
        $transportadoras = implode(",", $asignaciones_array);
    } else {
        $transportadoras = NULL; // Manejo de caso vacío
    }

    if($transportadoras != NULL) {
        $fil = "AND b.cod_transp IN (".$transportadoras.") ";
    }
    $mSql = "SELECT 
						a.num_despac, 
						a.cod_manifi, 
						UPPER(b.num_placax) AS num_placax, 
						h.abr_tercer AS nom_conduc, 
						h.num_telmov, 
						a.fec_salida, 
						a.cod_tipdes, 
						UPPER(i.nom_tipdes) AS nom_tipdes, 
						UPPER(c.abr_tercer) AS nom_transp, 
						UPPER(k.abr_tercer) AS nom_genera,
						nov.novedades_count AS 'tot_psoluc'

					FROM 
						".BASE_DATOS.".tab_despac_despac a 
						INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
							ON a.num_despac = b.num_despac 
						AND a.fec_salida IS NOT NULL  
						AND ( a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00' )
						AND a.ind_planru = 'S' 
						AND a.ind_anulad = 'R' 
						AND b.ind_activo = 'S' 
						".$fil."
						INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
							ON b.cod_transp = c.cod_tercer 
						INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
							ON a.cod_ciuori = d.cod_ciudad 
						AND a.cod_depori = d.cod_depart 
						AND a.cod_paiori = d.cod_paisxx 
						INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
							ON a.cod_ciudes = e.cod_ciudad 
						AND a.cod_depdes = e.cod_depart 
						AND a.cod_paides = e.cod_paisxx 
						INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
							ON a.cod_depori = f.cod_depart 
						AND a.cod_paiori = f.cod_paisxx 
						INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
							ON a.cod_depdes = g.cod_depart 
						AND a.cod_paides = g.cod_paisxx 
						INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
							ON b.cod_conduc = h.cod_tercer 
						INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
							ON a.cod_tipdes = i.cod_tipdes 
						LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
							ON a.cod_client = k.cod_tercer 
						INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu l 
							ON b.num_placax = l.num_placax 
						LEFT JOIN ".BD_STANDA.".tab_genera_opegps m 
							ON a.gps_operad = m.cod_operad 
						-- Subconsulta que calcula el nï¿½mero de novedades (con ind_reqres = 1) y donde cod_soluci estï¿½ sin diligenciar
						INNER JOIN (
							SELECT 
								n.num_despac, 
								COUNT(*) AS novedades_count,
								MIN(n.fec_creaci) AS fecha_mas_antigua,
								MAX(r.cod_riesgo) AS nivel_riesgo
							FROM (
								SELECT num_despac, cod_noveda, fec_creaci 
								FROM ".BASE_DATOS.".tab_despac_noveda
								WHERE cod_soluci IS NULL OR cod_soluci = '' OR cod_soluci = 0
								UNION ALL
								SELECT num_despac, cod_noveda, fec_creaci 
								FROM ".BASE_DATOS.".tab_despac_contro
								WHERE cod_soluci IS NULL OR cod_soluci = '' OR cod_soluci = 0
							) n
							INNER JOIN ".BASE_DATOS.".tab_parame_novseg p 
								ON n.cod_noveda = p.cod_noveda
							AND p.ind_reqres = 1
							-- Se une para validar que el parï¿½metro corresponde al cod_transp del despacho
							INNER JOIN ".BASE_DATOS.".tab_despac_vehige b2 
								ON n.num_despac = b2.num_despac
							AND p.cod_transp = b2.cod_transp
							INNER JOIN ".BASE_DATOS.".tab_genera_noveda r 
        						ON n.cod_noveda = r.cod_noveda
							GROUP BY n.num_despac
							HAVING COUNT(*) >= 1
						) nov 
							ON a.num_despac = nov.num_despac
						-- Subconsulta para obtener la última velocidad registrada
						LEFT JOIN (
							SELECT 
								num_despac, 
								kms_vehicu 
							FROM ".BASE_DATOS.".tab_despac_contro 
							WHERE kms_vehicu IS NOT NULL AND kms_vehicu <> '' 
								AND fec_creaci = (
									SELECT MAX(fec_creaci) 
									FROM ".BASE_DATOS.".tab_despac_contro 
									WHERE kms_vehicu IS NOT NULL AND kms_vehicu <> '' 
										AND num_despac = tab_despac_contro.num_despac
								)
						) ult_velocidad ON a.num_despac = ult_velocidad.num_despac
					WHERE 1=1";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    $alarmas = $mConsult -> ret_matrix('a');

    // Inicializamos la suma total y el arreglo de alertas
    $totalBadge = 0;
    $alerts = array();
    
    if(!empty($alarmas)){
        foreach($alarmas as $fila){
            // Sumar el tot_psoluc de cada fila (convertido a entero)
            $totalBadge += (int)$fila['tot_psoluc'];
            
            // Armar el detalle de cada alerta con los campos que desees mostrar
            $alerts[] = array(
                'num_despac'         => $fila['num_despac'],
                'cod_manifi'         => $fila['cod_manifi'],
                'num_placax'         => $fila['num_placax'],
                'nom_conduc'         => $fila['nom_conduc'],
                'nom_transp'         => $fila['nom_transp'],
                'tot_psoluc'         => $fila['tot_psoluc'],
                'tiempo_sin_gestion' => $fila['tiempo_sin_gestion'],
                'nivel_riesgo'       => $fila['nivel_riesgo'],
                'code_nivel_riesgo'  => $fila['code_nivel_riesgo']
                // Agrega otros campos que necesites
            );
        }
    }
    
    // Preparamos la salida en formato JSON
    header('Content-Type: application/json');
    echo json_encode(array(
        "badge"  => $totalBadge,
        "alerts" => $alerts
    ));


  }

  


}

$proceso = new AjaxNavBar($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
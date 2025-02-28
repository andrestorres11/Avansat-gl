<?php
/*! \file: ajax_vehicu_lineas.php
 *  \brief: 
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dia/mes/aÃ±o
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

session_start();
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

/*! \class: AjaxVehigeLineas
 *  \brief: 
 */
class AjaxVehigeLineas
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

        $paramsJson = json_decode($_REQUEST['paramsJson'],true);

        $_REQUEST[Option] = isset($_REQUEST[Option]) ? $_REQUEST[Option]: $paramsJson["option"];

        switch ($_REQUEST[Option])
        {
            case 'getLineas':
                AjaxVehigeLineas::getLineas($paramsJson);
                break;
            default:
                break;
        }
    }

    function getLineas($paramsJson)
    {
        $limit = isset($_REQUEST['registros']) ? intval($_REQUEST['registros']) : 25;
        $pagina = isset($_REQUEST['pagina']) ? intVal($_REQUEST['pagina']) : 0;


        $sql = "SELECT a.cod_lineax, a.nom_lineax, a.cod_marcax, b.nom_marcax, c.cod_mintra AS mintra_cliente, c.ind_estado 
            FROM ".BD_STANDA.".tab_genera_lineas a 
            INNER JOIN ".BD_STANDA.".tab_genera_marcas b ON a.cod_marcax = b.cod_marcax 
            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c ON a.cod_lineax = c.cod_mintra AND a.cod_marcax = c.cod_marcax
            WHERE 1=1";

        $sql .= " AND ( a.cod_lineax  LIKE '%" . $_REQUEST['campo'] . "%' 
                    OR a.nom_lineax  LIKE '%" . $_REQUEST['campo'] . "%' 
                    OR   a.cod_marcax LIKE '%" . $_REQUEST['campo'] . "%'
                    OR   b.nom_marcax LIKE '%" . $_REQUEST['campo'] . "%'
                    )";

        if ($pagina==0) {
            $inicio = 0;
            $pagina = 1;
        } else {
            $inicio = ($pagina-1) * $limit;
        }

        $sql .= " ORDER BY a.cod_lineax ASC ";

        // Consulta para total de registro
        $consult = new Consulta($sql, $this->conexion);
        $total = $consult->ret_matriz("a");
        $totalRegistros = sizeof($total);

        $sql .= " LIMIT $inicio , $limit";

        $consult = new Consulta($sql, $this->conexion);
        $matriz = $consult->ret_matriz("a");
        $totalFiltro = sizeof($matriz);
        

         // Mostrado resultados
         $output = [];
         $output['totalRegistros'] = $totalRegistros;
         $output['totalFiltro'] = $totalFiltro;
         $output['data'] = '';
         $output['paginacion'] = '';
         $output['pagina'] = $pagina;
 
         //var_dump($matriz);
        if (!empty($matriz)) {

            for($i = 0; $i < sizeof($matriz); $i++)
            {
                if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "1"){
                    $mensaje = "Desactivar";
                }else if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "0"){
                    $mensaje = "Activar";
                }else{
                    $mensaje = "Insertar";
                }

                $output['data'] .= '<tr>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["cod_lineax"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["nom_lineax"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["cod_marcax"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["nom_marcax"]).'</td>';

                    if(strpos(eliminarAcentos($matriz[$i]["nom_lineax"]),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
                        $linea_cambiado = str_replace('"','`',eliminarAcentos($matriz[$i]["nom_lineax"]));
                    }else{
                        $linea_cambiado = eliminarAcentos($matriz[$i]["nom_lineax"]);
                    }

                    $output['data'] .= "<td class='celda_info  text-center' ><a href='#' onclick='javascript:ActivarLineas(\"".$matriz[$i]["cod_lineax"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$matriz[$i]["ind_estado"]."\", \"".$linea_cambiado."\", \"".$matriz[$i]["cod_marcax"]."\")'>".$mensaje."</a></td>";
                $output['data'] .= '</tr>';
            }

        }

        // Paginación
        if ($totalRegistros > 0) {
            $totalPaginas = ceil($totalRegistros / $limit);
            $output['totpag'] = $totalPaginas;
  
            $output['paginacion'] .= '<nav>';
            $output['paginacion'] .= '<ul class="pagination pagination-sm">';
  
            $numeroInicio = max(1, $pagina - 4);
            $numeroFin = min($totalPaginas, $numeroInicio + 9);
  
            for ($i = $numeroInicio; $i <= $numeroFin; $i++) {
                $output['paginacion'] .= '<li class="page-item' . ($pagina == $i ? ' active' : '') . '">';
                $output['paginacion'] .= '<a class="page-link" href="#" onclick="pag_1.nextPage(' . $i . ')">' . $i . '</a>';
                $output['paginacion'] .= '</li>';
            }
  
            $output['paginacion'] .= '</ul>';
            $output['paginacion'] .= '</nav>';
        }
  
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
    }
   
    function eliminarAcentos($cadena){
		
		$caracteres = array(
            // Vocales acentuadas
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            // Otros caracteres especiales
            'Ñ' => 'N', 'ñ' => 'n', 'Ç' => 'C', 'ç' => 'c', 'ß' => 'ss',
            // Caracteres adicionales
            'Ý' => 'Y', 'ý' => 'y', 'ÿ' => 'y', '*' => '',
        );
    
        $cadena = strtr($cadena, $caracteres);
        $cadena = preg_replace('/[\x{FFFD}]/u', '', $cadena);
    
        return $cadena;
	}
}
//FIN CLASE PROC_DESPAC

$proceso = new AjaxVehigeLineas($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
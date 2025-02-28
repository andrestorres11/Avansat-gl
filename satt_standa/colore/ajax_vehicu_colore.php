<?php
/*! \file: ajax_vehicu_colore.php
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

/*! \class: AjaxVehicuColore
 *  \brief: 
 */
class AjaxVehicuColore
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
            case 'getColores':
                AjaxVehicuColore::getColores($paramsJson);
                break;
            default:
                break;
        }
    }

    function getColores($paramsJson)
    {
        $limit = isset($_REQUEST['registros']) ? intval($_REQUEST['registros']) : 25;
        $pagina = isset($_REQUEST['pagina']) ? intVal($_REQUEST['pagina']) : 0;


        $sql = "SELECT a.cod_colorx, a.nom_colorx, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_vehige_colore a LEFT JOIN ".BASE_DATOS.".tab_vehige_colore b ON a.cod_colorx = b.cod_mintra";

        $sql .= " AND ( a.cod_colorx  LIKE '%" . $_REQUEST['campo'] . "%' 
                    OR a.nom_colorx  LIKE '%" . $_REQUEST['campo'] . "%' )";

        if ($pagina==0) {
            $inicio = 0;
            $pagina = 1;
        } else {
            $inicio = ($pagina-1) * $limit;
        }

        $sql .= " ORDER BY a.cod_colorx ASC ";

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
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["cod_colorx"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["cod_colorx"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.eliminarAcentos($matriz[$i]["nom_colorx"]).'</td>';

                    if(strpos(eliminarAcentos($matriz[$i]["nom_colorx"]),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
                        $color_cambiado = str_replace('"','`',eliminarAcentos($matriz[$i]["nom_colorx"]));
                    }else{
                        $color_cambiado = eliminarAcentos($matriz[$i]["nom_colorx"]);
                    }

                    $output['data'] .= "<td class='celda_info  text-center' ><a href='#' onclick='javascript:activarColores(\"".$matriz[$i]["cod_colorx"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$color_cambiado."\", \"".$matriz[$i]["ind_estado"]."\")'>".$mensaje."</a></td>";
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
   

}
//FIN CLASE PROC_DESPAC

$proceso = new AjaxVehicuColore($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
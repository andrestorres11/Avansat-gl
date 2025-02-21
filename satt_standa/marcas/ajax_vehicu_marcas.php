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

/*! \class: AjaxVehicuMarcas
 *  \brief: 
 */
class AjaxVehicuMarcas
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
            case 'getMarcas':
                AjaxVehicuMarcas::getMarcas($paramsJson);
                break;
            default:
                break;
        }
    }

    function getMarcas($paramsJson)
    {
        $limit = isset($_REQUEST['registros']) ? intval($_REQUEST['registros']) : 25;
        $pagina = isset($_REQUEST['pagina']) ? intVal($_REQUEST['pagina']) : 0;


        $sql = "SELECT a.cod_marcax, a.nom_marcax, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_genera_marcas a LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b ON a.cod_marcax = b.cod_mintra";

        $sql .= " AND (  a.cod_marcax LIKE '%" . $_REQUEST['campo'] . "%'
                    OR   b.nom_marcax LIKE '%" . $_REQUEST['campo'] . "%'
                    )";

        if ($pagina==0) {
            $inicio = 0;
            $pagina = 1;
        } else {
            $inicio = ($pagina-1) * $limit;
        }

        $sql .= " ORDER BY a.cod_marcax ASC ";

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
                    $output['data'] .= '<td class="celda_info  text-center" >'.utf8_decode($matriz[$i]["cod_marcax"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.utf8_decode($matriz[$i]["cod_marcax"]).'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.utf8_decode(utf8_encode($matriz[$i]["nom_marcax"])).'</td>';

                    if(strpos(utf8_decode(utf8_encode($matriz[$i]["nom_marcax"])),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
                        $marca_cambiado = str_replace('"','`',utf8_decode(utf8_encode($matriz[$i]["nom_marcax"])));
                    }else{
                        $marca_cambiado = utf8_decode(utf8_encode($matriz[$i]["nom_marcax"]));
                    }

                    $output['data'] .= "<td class='celda_info  text-center' ><a href='#' onclick='javascript:activarMarcas(\"".$matriz[$i]["cod_marcax"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$marca_cambiado."\", \"".$matriz[$i]["ind_estado"]."\")'>".$mensaje."</a></td>";
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

$proceso = new AjaxVehicuMarcas($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
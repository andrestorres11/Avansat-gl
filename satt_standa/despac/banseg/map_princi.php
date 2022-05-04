<?php
/*! \file: map_princi.php
 *  \brief: 
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

session_start();

/*! \class: AjaxDespac
 *  \brief: 
 */
class mapPrinci
{
    var $conexion,
        $cod_aplica,
        $usuario;

    function __construct($co, $us, $ca)
    {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        
        @include( "../lib/ajax.inc" );

        $this -> conexion = $AjaxConnection;

        switch ($_REQUEST['opcion'])
        {
            case "getMapaPrincipal":
                $this->getMapaPrincipal();
                break;
           
            default:
                header('Location: index.php?window=central&cod_servic=5555&menant=5555');
                //$this->Listar();
                break;
        }
    }

    function getMapaPrincipal()
    {
       include '../../lib/InterfGPS.inc';
       $_REQUEST['cod_transp'] = '860068121';
       $_REQUEST['num_manifi'] = 'getMapPrincipal';

       $InterfGPS = new InterfGPS($this -> conexion);

       echo $InterfGPS-> map_princi($_REQUEST);
    } 
}
//FIN CLASE PROC_DESPAC

$proceso = new mapPrinci($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
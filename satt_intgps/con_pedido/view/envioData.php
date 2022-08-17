<?php
/* ! \file: index.php
 *  \brief: Clase que genera la interfaz principal de consulta
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 04/09/2020
 *  \warning:
 */
    use Phppot\Captcha;
    use Phppot\Contact;


    class EnvioData 
    {
        var $conexion  = NULL;
        
        
        function __construct()
        {
            //Archivos necesarios
            session_start();
            require_once "../../constantes.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/conexion_lib.inc";
            $this->conexion = new Conexion( HOST, USUARIO, CLAVE, BASE_DATOS, BD_STANDA );
            self::opcion();
            
        }

        /*! \fn: consultaExiste
         *  \brief: De acuerdo a la opcion ingresada en el formulario hace la consulta de los diferentes metodos.
         *  \author: Ing. Cristian Andres Torres
         *  \date: 29/10/2020
         *  \date modified: dd/mm/aaaa
         *  \return: boolean
         */
        protected function opcion(){
          switch($_REQUEST['option']){
            case '1':
              self::regFecLogistics();
              break;
              case '2':
                self::regEncuestSatisfaccion();
                break;
			      default:
				    echo "me jodio";
                     break;
		      }
        }

        protected function regFecLogistics(){
          $tiemLogisticos = self::queryConsultTiemposLog();
          $mConsult=true;
          $pedido = $_REQUEST['num_pedido']."-".$_REQUEST['num_linea'];
          if(isset($_REQUEST['fec_cumdes'])){
            $fec_cumdes = date("Y-m-d H:i:s", strtotime($_REQUEST['fec_cumdes'].' '.$_REQUEST['hor_cumdes']));
            $mSql = "UPDATE tab_despac_destin SET  
                     fec_cumdes = '".$fec_cumdes."',
                     ind_cumdes = '1',
                     nov_cumdes = '256',
                     obs_cumdes = 'REGISTRADO DESDE LA INTERFAZ DE CLIENTES',
                     usr_cumdes = 'Interfcliente'
                  WHERE ped_remisi = '".$pedido."'";
            $mConsult = new Consulta($mSql, $this -> conexion);
          }
          if(isset($_REQUEST['fec_ingdes'])){
            $fec_ingdes = date("Y-m-d H:i:s", strtotime($_REQUEST['fec_ingdes'].' '.$_REQUEST['hor_ingdes']));
            $mSql = "UPDATE tab_despac_destin SET  
                     fec_ingdes = '".$fec_ingdes."',
                     nov_ingdes = '407',
                     obs_ingdes = 'REGISTRADO DESDE LA INTERFAZ DE CLIENTES',
                     usr_ingdes = 'Interfcliente'
                     WHERE ped_remisi = '".$pedido."'";
            $mConsult = new Consulta($mSql, $this -> conexion);
          }
          if(isset($_REQUEST['fec_saldes'])){
            $fec_saldes = date("Y-m-d H:i:s", strtotime($_REQUEST['fec_saldes'].' '.$_REQUEST['hor_saldes']));
            $mSql = "UPDATE tab_despac_destin SET  
                     fec_saldes = '".$fec_saldes."',
                     nov_saldes = '408',
                     obs_saldes = 'REGISTRADO DESDE LA INTERFAZ DE CLIENTES',
                     usr_saldes = 'Interfcliente'
                     WHERE ped_remisi = '".$pedido."'";
            $mConsult = new Consulta($mSql, $this -> conexion);
          }

          $_SESSION['num_pedido'] = $_REQUEST['num_pedido'];
          $_SESSION['num_linea'] = $_REQUEST['num_linea'];

          $_SESSION['msj']= "¡Datos almacenados éxitosamente!";
          $_SESSION['sta']= "success";
          header("Location: dashboard.php");
        }

        protected function queryConsultTiemposLog(){
          $mSql = "SELECT fec_cumdes, fec_ingdes, fec_saldes
                    FROM tab_despac_destin
                    WHERE num_docume = '".$_REQUEST['num_remisi']."' AND
                          ped_remisi = '".$_REQUEST['num_pedido']."';";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo();
          return $mData;
        }

        protected function validaTiemposLogisticos($fecha,$ind){
          $tiemLogisticos = self::queryConsultTiemposLog();
          if($ind==1){
            if($fecha<$tiemLogisticos['fec_ingdes'] AND $fecha<$tiemLogisticos['fec_saldes']){
              return true;
            }
          }else if($ind==2){

          }else{

          }

        }

        protected function regEncuestSatisfaccion(){
          $mSql = "
          INSERT INTO tab_respue_encues(
            num_despac, num_docume, ped_remisi, 
            cod_remdes, res_pregun1, res_pregun2, 
            res_pregun3, usr_creaci, fec_creaci
          ) 
          VALUES 
            (
              '".$_REQUEST['num_despac']."', '".$_REQUEST['num_remisi']."', '".$_REQUEST['num_pedido']."',
              '".$_REQUEST['cod_remdes']."', '".$_REQUEST['preg1']."', '".$_REQUEST['preg2']."',
              '".$_REQUEST['preg3']."', 'Interfcliente', NOW()
            )";
        $mConsult = new Consulta($mSql, $this -> conexion);

        $_SESSION['num_pedido'] = $_REQUEST['num_pedido'];
        $_SESSION['num_remisi'] = $_REQUEST['num_remisi'];
        $_SESSION['busqueda'] = $_REQUEST['busqueda'];

        $_SESSION['msj']= "¡Datos almacenados éxitosamente!";
        $_SESSION['sta']= "success";

        if(!$mConsult){
          $_SESSION['msj']= "Error al almacenar la información. Intente de nuevamente.";
          $_SESSION['sta']= "danger";
        }
        
        header("Location: dashboard.php");
        }

    }  

    $EnvioData = new EnvioData();

?>

<?php
/*! \file: ajax_emptra_agenci.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 09/09/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
//ini_set('display_errors', true);
//error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
setlocale(LC_ALL,"es_ES");

/*! \class: agenci
 *  \brief: Clase agenci que gestiona las diferentes peticiones ajax  */
class agenci{

  private static  $cConexion,
                  $cCodAplica,
                  $cUsuario,
                  $cNull = array( array('', '-----') );


  function __construct($co = null, $us = null, $ca = null)
  {
    
    if($_REQUEST[Ajax] === 'on' ){

      @include_once( "../lib/ajax.inc" );
      @include_once( "../lib/general/constantes.inc" );
      self::$cConexion = $AjaxConnection;

      switch($_REQUEST[Option]){
        case 'buscarTransportadora':
          self::buscarTransportadora();
          break;

        case 'getCiudades':
            self::getCiudades();
            break;
            
        case "registrar":
            self::registrar();
            break;
        
        case "modificar":
            self::modificar();
            break;

        case 'inactivar':
            self::inactivar();
            break;

        case 'activar':
            self::activar();
            break;

        default:
          header('Location: index.php?window=central&cod_servic=1366&menant=1366');
          break;
      }
    }else{
      self::$cConexion = $co;
      self::$cUsuario = $us;
      self::$cCodAplica = $ca;
    }
  }

    /*! \fn: funcion getDatosAgencia()
     *  \brief: funcion que extrae los datos de una transportadora de la base de datos para su visualización o edición
     *  \author: Ing. Alexander Correa
     *  \date: 01/09/2015
     *  \date modified: dia/mes/año
     *  \param: $cod_transp -> id de la transportadora a consultar
     *  \param: 
     *  \return $datos -> objeto con la informacion de la transportadora
     */

    public function getDatosAgencia($cod_agenci){
        if(!$cod_agenci){
          $cod_agenci = '0';
        }
        $datos = new stdClass();

        #consulta los datos basicos de la agencia
        $query = "SELECT a.cod_agenci,a.nom_agenci,a.cod_ciudad,a.dir_agenci,
                         a.tel_agenci,a.con_agenci,a.dir_emailx,a.num_faxxxx,
                         a.cod_estado, CONCAT( UPPER(b.abr_ciudad), '(', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) abr_ciudad,
                         f.abr_tercer, f.cod_tercer
                       
                  FROM ".BASE_DATOS.".tab_genera_agenci a

                       INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON b.cod_ciudad = a.cod_ciudad
                       INNER JOIN ".BASE_DATOS.".tab_genera_depart c ON c.cod_depart = b.cod_depart
                       INNER JOIN ".BASE_DATOS.".tab_genera_paises d ON d.cod_paisxx = b.cod_paisxx
                       INNER JOIN ".BASE_DATOS.".tab_transp_agenci e ON e.cod_agenci = a.cod_agenci
                       INNER JOIN ".BASE_DATOS.".tab_tercer_tercer f ON f.cod_tercer = e.cod_transp
                 WHERE 
                       a.cod_agenci = '".$cod_agenci."'";
              

      $consulta = new Consulta($query, self::$cConexion);
      $transpor = $consulta -> ret_matrix("a");
      $datos=(object) $transpor[0];
      $datos->dir_agenci = utf8_encode($datos->dir_agenci);
         
      return $datos;
    }
    /******************************************************************************
     *  \fn: registrar                                                            *
     *  \brief: funcion para registros nuevos de agencias                         *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 31/08/2015                                                         *
     *  \date modified:                                                           *
     *  \param: operacion: string con la operacion a realizar.                    *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function registrar(){

        $agencia = (object) $_POST['agenci'];

        $query ="SELECT cod_paisxx,cod_depart FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = '$agencia->cod_ciudad'";
        $consulta = new Consulta($query, self::$cConexion);
        $datos = $consulta -> ret_matrix("a");
       
        $agencia->cod_paisxx = $datos[0]['cod_paisxx'];
        $agencia->cod_depart = $datos[0]['cod_depart'];
        $agencia->usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];
        $agencia->fec_creaci = date("Y-m-d H:i:s");
        
        #inserta la agencia el la base de datos
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_agenci 
                              (cod_agenci,nom_agenci,cod_ciudad,dir_agenci,
                               tel_agenci,con_agenci,dir_emailx,num_faxxxx,
                               cod_depart,cod_paisxx,usr_creaci,fec_creaci)
                              VALUES('$agencia->cod_agenci','$agencia->nom_agenci','$agencia->cod_ciudad','$agencia->dir_agenci',
                                     '$agencia->tel_agenci','$agencia->con_agenci','$agencia->dir_emailx','$agencia->num_faxxxx',
                                     '$agencia->cod_depart','$agencia->cod_paisxx','$agencia->usr_creaci','$agencia->fec_creaci')";

        $insercion = new Consulta($query, self::$cConexion, "R");

        #crear la relación netre la agencia y la transportadora
        $query = "INSERT INTO ".BASE_DATOS.".tab_transp_agenci 
                                (cod_transp,cod_agenci)
                                VALUES
                                ('$agencia->cod_tercer','$agencia->cod_agenci')";
        $insercion = new Consulta($query, self::$cConexion, "R");

        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            
            $mensaje = "<font color='#000000'>Se Inserto la Agencia <b>$agenci->nom_agenci</b> Exitosamente.<br></font>" . $link_a;
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->correcto2("INSERTAR AGENCIA", $mensaje);


        }
    }

     /*****************************************************************************
     *  \fn: activar                                                              *
     *  \brief: función para activar una agencia                                  *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     *****************************************************************************/
    private function activar(){
        $agenci = (object) $_POST['agenci']; //objeto para la tabla tab_tercer_tercer
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $agenci->usr_modifi = $usuario;
        $agenci->fec_modifi = $fec_actual;
        

        $query = "UPDATE ".BASE_DATOS.".tab_genera_agenci 
                        SET cod_estado = 1,
                            usr_modifi = '$agenci->usr_modifi',
                            fec_modifi = '$agenci->fec_modifi'
                            WHERE cod_agenci = '$agenci->cod_agenci' ";
        $insercion = new Consulta($query, self::$cConexion, "R");
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
          $mensaje = "Se activó la Agencia ".$agenci->nomb_agenci." exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("ACTIVAR AGENCIA",$mensaje);
        }


    }
    /******************************************************************************
     *  \fn: inactivar                                                            *
     *  \brief: función para inactivar una agencia                                *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function inactivar(){
        $agenci = (object) $_POST['agenci']; //objeto para la agencia

        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $agenci->usr_modifi = $usuario;
        $agenci->fec_modifi = $fec_actual;

                

        $query = "UPDATE ".BASE_DATOS.".tab_genera_agenci 
                        SET cod_estado = 0,
                            usr_modifi = '$agenci->usr_modifi',
                            fec_modifi = '$agenci->fec_modifi'
                            WHERE cod_agenci = '$agenci->cod_agenci' ";
        $insercion = new Consulta($query, self::$cConexion, "R");
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
          $mensaje = "Se inactivó la Agencia ".$agenci->nomb_agenci." exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR AGENCIA",$mensaje);
        }


    }
    /******************************************************************************
     *  \fn: modificar                                                            *
     *  \brief: función para modificar una agencia                                *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function modificar(){
        $agencia = (object) $_POST['agenci'];

        $query ="SELECT cod_paisxx,cod_depart FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = '$agencia->cod_ciudad'";
        $consulta = new Consulta($query, self::$cConexion);
        $datos = $consulta -> ret_matrix("a");
       
        $agencia->cod_paisxx = $datos[0]['cod_paisxx'];
        $agencia->cod_depart = $datos[0]['cod_depart'];
        $agencia->usr_modifi = $_SESSION['datos_usuario']['cod_usuari'];

        #modifica los datos de la agencia
        $query = "UPDATE ".BASE_DATOS.".tab_genera_agenci 
                    SET 
                        nom_agenci = '$agencia->nom_agenci',
                        cod_ciudad = '$agencia->cod_ciudad',
                        dir_agenci = '$agencia->dir_agenci',
                        tel_agenci = '$agencia->tel_agenci',
                        con_agenci = '$agencia->con_agenci',
                        dir_emailx = '$agencia->dir_emailx',
                        num_faxxxx = '$agencia->num_faxxxx',
                        cod_depart = '$agencia->cod_depart',
                        cod_paisxx = '$agencia->cod_paisxx',
                        usr_modifi = '$agencia->usr_modifi',
                        fec_modifi = NOW()
                    WHERE cod_agenci = '$agencia->cod_agenci' ";
        $insercion = new Consulta($query, self::$cConexion, "R");

        #modifica la relación entre la transportadora y la agencia
        $query = "UPDATE ".BASE_DATOS.".tab_transp_agenci
                              SET 
                                  cod_transp = '$agencia->cod_tercer'
                              WHERE cod_agenci = '$agencia->cod_agenci' ";
        $insercion = new Consulta($query, self::$cConexion, "R");
       
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
          $mensaje = "Se Actualizó la Agencia <b>$agencia->nom_agenci</b> exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='confirmado()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("ACTUALIZAR AGENCIA",$mensaje);
        }

    }


    /******************************************************************************
     *  \fn: getConsecutivo                                                       *
     *  \brief: función para traer el consecutivo de una agencia                  *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 09/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return nuevo consecutivo para las agencias                               *
    ******************************************************************************/
    public function getConsecutivo(){
        $query = "SELECT MAX( CAST(cod_agenci AS UNSIGNED) ) maximo
                 FROM ".BASE_DATOS.".tab_genera_agenci";

        $consec = new Consulta($query, self::$cConexion );
        $ultimo = $consec -> ret_matriz();

        return $ultimo[0][0]+1;
    }
    
}

if($_REQUEST[Ajax] === 'on' )
  $_INFORM = new agenci();

?>

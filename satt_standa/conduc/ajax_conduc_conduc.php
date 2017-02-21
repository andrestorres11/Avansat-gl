<?php
/*! \file: ajax_conduc_conduc.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 31/09/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */



header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
setlocale(LC_ALL,"es_ES");

/*! \class: trans
 *  \brief: Clase trasn que gestiona las diferentes peticiones ajax  */
class conduc{

  private static  $cConexion,
                  $cCodAplica,
                  $cUsuario,
                  $cNull = array( array('', '-----') );


  function __construct($co = null, $us = null, $ca = null)
  {
      
    if($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on'){
      
      @include_once( "../lib/ajax.inc" );
      @include_once( "../lib/general/constantes.inc" );
      self::$cConexion = $AjaxConnection;

      
    }else{
      self::$cConexion = $co;
      self::$cUsuario = $us;
      self::$cCodAplica = $ca;
    }

    if($_REQUEST[Ajax] === 'on' ){
        $opcion = $_REQUEST[Option];
        if(!$opcion){
          $opcion = $_REQUEST[operacion];
        }

      switch($opcion){
        case 'listaConductores':
          $cod_transp = $_REQUEST['cod_transp'];
          self::listaConductores($cod_transp);
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

        case 'eliminar':
            self::eliminar();
            break;

        case 'inactivar':
            self::inactivar();
            break;

        case 'activar':
            self::activar();
            break; 
        case 'verificar':
            self::verificar();
            break;

        default:
          header('Location: index.php?window=central&cod_servic=1366&menant=1366');
          break;
      }
    }
  }

 /***********************************************************************************
 *   \fn: listaConductores                                                          *
 *  \brief: funcion para listar los conductores de una transportadoras              *
 *  \author: Ing. Alexander Correa                                                  *
 *  \date:  4/09/2015                                                               *
 *  \date modified:                                                                 *
 *  \param: $cod_transp codigo de la Trasnportador                                  *     
 *  \param:                                                                         * 
 *  \return tabla con la lista de los conductores                                   *
 ***********************************************************************************/

    private function listaConductores($cod_transp){


        //Se cambia a.cod_estado por b.ind_estado por id 201027
        $mSql = "SELECT  a.cod_tercer,a.nom_apell1,a.nom_apell2,a.nom_tercer,
                         a.num_telmov,c.num_licenc,c.fec_venlic,d.nom_catlic,
                         IF(b.ind_estado = '1','Activo', 'Inactivo') cod_estado,
                         b.ind_estado cod_option
                    FROM ".BASE_DATOS.".tab_tercer_tercer a 
                         INNER JOIN ".BASE_DATOS.".tab_transp_tercer b ON a.cod_tercer = b.cod_tercer
                         INNER JOIN ".BASE_DATOS.".tab_tercer_conduc c ON c.cod_tercer = b.cod_tercer
                         INNER JOIN ".BASE_DATOS.".tab_genera_catlic d ON c.num_catlic = d.cod_catlic
                   WHERE a.cod_tercer = c.cod_tercer AND b.cod_transp = '$cod_transp'";

                                         
        $_SESSION["queryXLS"] = $mSql;

        if(!class_exists(DinamicList)) {
          include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
        }
        $list = new DinamicList(self::$cConexion, $mSql, "9" , "no", 'ASC');

        $list->SetClose('no');
        $list->SetCreate("Agregar Conductor", "onclick:formulario()");
        $list->SetHeader(utf8_decode("Nº de Documento"), "field:a.cod_tercer; width:1%;  ");
        $list->SetHeader(utf8_decode("Primer Apellido"), "field:a.nom_apell1; width:1%");
        $list->SetHeader(utf8_decode("Segundo Apellido"), "field:a.nom_apell2; width:1%");
        $list->SetHeader(utf8_decode("Nombres"), "field:a.nom_tercer; width:1%");
        $list->SetHeader(utf8_decode("Nº Teléfono Móvil"), "field:a.num_telmov" );
        $list->SetHeader(utf8_decode("Nº de Licencia"), "field:a.num_licenc" );
        $list->SetHeader(utf8_decode("Vigencia"), "field:a.fec_venlic" );
        $list->SetHeader(utf8_decode("Categoria"), "field:a.num_catlic" );
        $list->SetHeader(utf8_decode("Estado"), "field:a.cod_estado" );
        $list->SetOption(utf8_decode("Opciones"),"field:cod_option; width:1%; onclikDisable:editarConductor( 2, this ); onclikEnable:editarConductor( 1, this ); onclikEdit:editarConductor( 99, this ); onclikPrint:editarConductor(3, this);" );
        $list->SetHidden("cod_tercer", "0" );
        $list->SetHidden("nom_tercer", "1" );


        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list -> GetHtml();

        echo $Html;
    }

     /*****************************************************************************
     *  \fn: registrar                                                            *
     *  \brief: funcion para registros nuevos de conductores                      *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 15/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function registrar(){
      #arma los objetos para cada una de las tablas necesarias

      $conduc = (object) $_POST['conduc']; #datos principales del conductor
      $conduc->abr_tercer = $conduc->nom_apell1." ".$conduc->nom_tercer;
      #pregunto si ya hay alguien con ese documento registrado
      $query = "SELECT cod_tercer
            FROM ".BASE_DATOS.".tab_tercer_tercer
            WHERE cod_tercer = '$conduc->cod_tercer'";
      $consulta = new Consulta($query,  self::$cConexion, "BR");
      $indter = $consulta -> ret_arreglo();
      if(!$indter){
       
        $conduc->cod_estado = 1;

        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $conduc->fec_creaci =$fec_actual;
        $conduc->usr_creaci = $usuario;
        #si se ingreso alguna foto se mueve al servidor y se agrega la vriabla al objeto
        if($_FILES){
          if(move_uploaded_file($_FILES['foto']['tmp_name'], "../../".NOM_URL_APLICA."/".URL_CONDUC.$conduc->cod_tercer.".jpg")){
              $conduc->dir_ultfot= URL_CONDUC.$conduc->cod_tercer.".jpg";
              $foto = "dir_ultfot = '$conduc->dir_ultfot',";
          }
        }else{
          $conduc->dir_ultfot = "NULL";
        }
        # consulta el departamento y pais del conductor basado en su ciudad
        $query = "SELECT cod_paisxx,cod_depart
                  FROM " . BASE_DATOS . ".tab_genera_ciudad
                 WHERE cod_ciudad = '$conduc->cod_ciudad' ";
               
        $consulta = new Consulta($query, self::$cConexion);

        $ciudad = $consulta->ret_matriz("a");

        $conduc->cod_paisxx = $ciudad[0][0];
        $conduc->cod_depart = $ciudad[0][1];
          #incerta la inforacion princial del conductor
          $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer(
                         cod_tercer,cod_tipdoc,nom_apell1,nom_apell2,nom_tercer,abr_tercer,
                         dir_domici,num_telef1,num_telef2,num_telmov,cod_paisxx,cod_depart,
                         cod_ciudad,cod_estado,dir_ultfot,obs_tercer,usr_creaci,fec_creaci)
                  VALUES( 
                          '$conduc->cod_tercer','$conduc->cod_tipdoc','$conduc->nom_apell1','$conduc->nom_apell2','$conduc->nom_tercer','$conduc->abr_tercer',
                          '$conduc->dir_domici','$conduc->num_telef1','$conduc->num_telef2','$conduc->num_telmov','$conduc->cod_paisxx','$conduc->cod_depart',
                          '$conduc->cod_ciudad','$conduc->cod_estado','$conduc->dir_ultfot','$conduc->obs_tercer','$conduc->usr_creaci','$conduc->fec_creaci') ";
          $insercion = new Consulta($query, self::$cConexion, "BR");

          #inserta la relacion entre el conductor y la transportadora si no existe previamente
          $query = "SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tercer WHERE cod_transp = '$conduc->cod_transp' AND cod_tercer = '$conduc->cod_tercer'";
          $consulta = new Consulta($query, self::$cConexion);
          $existe = $consulta->ret_matriz("a");

          if(!$existe){
            $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                           (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                              VALUES ('$conduc->cod_transp','$conduc->cod_tercer','$conduc->usr_creaci','$conduc->fec_creaci')";
                    $insercion = new Consulta($query,self::$cConexion,"R");
          }

          #inserta los datos adicionales del conductor
          $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_conduc(
                      cod_tercer,cod_tipsex,cod_grupsa,num_licenc,num_catlic,fec_venlic,
                      cod_califi,nom_epsxxx,nom_arpxxx,nom_pensio,nom_refper,tel_refper,
                      cod_operad,usr_creaci,fec_creaci)
              VALUES ('$conduc->cod_tercer','$conduc->cod_tipsex','$conduc->cod_grupsa','$conduc->num_licenc','$conduc->num_catlic','$conduc->fec_venlic',
                      '$conduc->cod_califi','$conduc->nom_epsxxx','$conduc->nom_arpxxx','$conduc->nom_pensio','$conduc->nom_refper','$conduc->tel_refper',
                      '$conduc->cod_operad','$conduc->usr_creaci','$conduc->fec_creaci')";
          $insercion = new Consulta($query,self::$cConexion,"R");

          #inserta los adicionales y referencias laborles
          $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
              VALUES ('$conduc->cod_tercer',".COD_FILTRO_CONDUC.")";
          $insercion = new Consulta($query,self::$cConexion,"R");

           if($conduc->cod_propie==1){
              $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                    VALUES ('$conduc->cod_tercer',".COD_FILTRO_PROPIE.")";
            $insercion = new Consulta($query,self::$cConexion,"R");
           }
           if($conduc->cod_tenedo==1){
             $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                    VALUES ('$conduc->cod_tercer',".COD_FILTRO_POSEED.")";
             $insercion = new Consulta($query,self::$cConexion,"R");
           }

           #inserta tantas referencias laborales como se hayan ingresado
           $empresas = $_POST['empresa']; #arreglo con los nombres de las empresas
           $telefonos = $_POST['telefono']; #arreglo con los numeros de telefono
           $viajes = $_POST['viajes']; #arreglo con los numeros de viajes
           $antiguedades = $_POST['antiguedad']; #arreglo con las antiguedades
           $mercancias = $_POST['mercancia']; #arreglo con las mercancias
          
          for($i=0;$i<=$conduc->cantidad;$i++){
             if($empresas[$i] != Null){
             $query = "INSERT INTO ".BASE_DATOS.".tab_conduc_refere
                            (cod_conduc,cod_refere,nom_empre,tel_empre,num_viajes,
                             num_atigue,nom_mercan,usr_creaci,fec_creaci)
                    VALUES ('$conduc->cod_tercer','$i','$empresas[$i]','$telefonos[$i]','$viajes[$i]',
                            '$antiguedades[$i]','$mercancias[$i]','$conduc->usr_creaci','$conduc->fec_creaci')";
              $insercion = new Consulta($query,self::$cConexion,"R");
             }
          }
        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
          header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1060&menant=1060&window=central&resultado=1&operacion=Registró&opcion=123&conductor='.$conduc->abr_tercer);
          
           /* $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $_REQUEST[cod_servic] . " \"target=\"centralFrame\"><font style='color:#3F7506'>Insertar Otra Transportadora</font></a></b>";

            $mensaje = "<font color='#000000'>Se Registró El Conductor: <b>$conduc->abr_tercer</b> Exitosamente.<br></font>";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->correcto2("INSERTAR CONDUCTOR", $mensaje);*/


        }else{
          header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1060&menant=1060&window=central&opcion=123&resultado=0');
            /*$mensaje = "<font color='#000000'>Ocurrió un Error inesperado <b>$conduc->abr_tercer</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
            $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->error2("INSERTAR CONDUCTOR", $mensaje);*/

        }
      }else{// si ya existe lo asocio a la nueva transportadora y llamo a la funcion modificar
        $query = "SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tercer WHERE cod_transp = '$conduc->cod_transp' AND cod_tercer = '$conduc->cod_tercer'";
          $consulta = new Consulta($query, self::$cConexion);
          $existe = $consulta->ret_matriz("a");

          if(!$existe){
            #inserta la relacion entre el conductor y la transportadora
            $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
                   (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                      VALUES ('$conduc->cod_transp','$conduc->cod_tercer','$conduc->usr_creaci','$conduc->fec_creaci')";
            $insercion = new Consulta($query,self::$cConexion,"R");
          }
          $this->modificar("Registró");
         

      }
    }

    /*****************************************************************************
     *  \fn: activar                                                              *
     *  \brief: función para activar una transportadora                           *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified: 02/05/2016  (Miguel Romero)                                                        *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function activar(){

        $conduc = (object) $_POST; 
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $conduc->usr_modifi = $usuario;
        $conduc->fec_modifi = $fec_actual;
        if(!$conduc->cod_tercer){
          $conduc->cod_tercer = $_POST['cod_conduc'];
          $conduc->abr_tercer = $_POST['nom_conduc'];
        } 

        //se modifica la consulta para cambiar la tabla y ubicar por transportadora

        $query = "UPDATE ".BASE_DATOS.".tab_transp_tercer 
                        SET ind_estado = 1,
                            usr_modifi = '$conduc->usr_modifi',
                            fec_modifi = '$conduc->fec_modifi'
                      WHERE cod_tercer = '$conduc->cod_tercer'
                        AND cod_transp = '$conduc->cod_transp' ";

        $insercion = new Consulta($query, self::$cConexion, "R");
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
           $mensaje = "Se activó el conductor ".$conduc->abr_tercer." exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("ACTIVAR CONDUCTOR",$mensaje);
        }else{
          $mensaje = "Error al activar el conductor: ".$conduc->abr_tercer;
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("ACTIVAR CONDUCTOR",$mensaje);
        }


    }
    /******************************************************************************
     *  \fn: inactivar                                                              *
     *  \brief: función para inactivar una transportadora                           *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:  02/05/2016  (Miguel Romero)                              *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function inactivar(){
        $conduc = (object) $_POST; //objeto para la tabla tab_tercer_tercer
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $conduc->usr_modifi = $usuario;
        $conduc->fec_modifi = $fec_actual;
        if(!$conduc->cod_tercer){
          $conduc->cod_tercer = $_POST['cod_conduc'];
          $conduc->abr_tercer = $_POST['nom_conduc'];
        }


        //se modifica la consulta para cambiar la tabla y ubicar por transportadora
        
        $query = "UPDATE ".BASE_DATOS.".tab_transp_tercer 
                        SET ind_estado = 0,
                            usr_modifi = '$conduc->usr_modifi',
                            fec_modifi = '$conduc->fec_modifi'
                      WHERE cod_tercer = '$conduc->cod_tercer' 
                        AND cod_transp = '$conduc->cod_transp' ";
        $insercion = new Consulta($query, self::$cConexion, "R");
        if($consulta = new Consulta ("COMMIT",self::$cConexion)) {
           $mensaje = "Se inactivó el conductor ".$conduc->abr_tercer." exitosamente.";
           $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
           $mens = new mensajes();
           $mens -> correcto2("INACTIVAR CONDUCTOR",$mensaje);
        }


    }
    /******************************************************************************
     *  \fn: modificar                                                            *
     *  \brief: función para modificar una conductores                            *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 17/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
    ******************************************************************************/
    private function modificar($operacion = null){
      if(!$operacion){
        $operacion = "Modificó";
      }
      # lleno los objetos nesesarios para la correcta modificación en la base de datos.
      $conduc = (object) $_POST['conduc']; #datos principales del conductor
      $conduc->abr_tercer = $conduc->nom_apell1." ".$conduc->nom_tercer;
      $fec_actual = date("Y-m-d H:i:s");
      $usuario = $_SESSION['datos_usuario']['cod_usuari'];
      $conduc->fec_modifi =$fec_actual;
      $conduc->usr_modifi = $usuario;
      $foto = "";
      if($_FILES){
          if(move_uploaded_file($_FILES['foto']['tmp_name'], "../../".NOM_URL_APLICA."/".URL_CONDUC.$conduc->cod_tercer.".jpg")){
              $conduc->dir_ultfot= URL_CONDUC.$conduc->cod_tercer.".jpg";
              $foto = "dir_ultfot = '$conduc->dir_ultfot',";
          }
      }
      # consulta el departamento y pais del conductor basado en su ciudad
      $query = "SELECT cod_paisxx,cod_depart
                FROM " . BASE_DATOS . ".tab_genera_ciudad
               WHERE cod_ciudad = '$conduc->cod_ciudad' ";
             
      $consulta = new Consulta($query, self::$cConexion);
      $ciudad = $consulta->ret_matriz("a");

      $conduc->cod_paisxx = $ciudad[0][0];
      $conduc->cod_depart = $ciudad[0][1];

       #actualiza la inforacion princial del conductor
        $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
                          SET
                       cod_tipdoc = '$conduc->cod_tipdoc',
                       nom_apell1 = '$conduc->nom_apell1',
                       nom_apell2 = '$conduc->nom_apell2',
                       nom_tercer = '$conduc->nom_tercer',
                       abr_tercer = '$conduc->abr_tercer',
                       dir_domici = '$conduc->dir_domici',
                       num_telef1 = '$conduc->num_telef1',
                       num_telef2 = '$conduc->num_telef2',
                       num_telmov = '$conduc->num_telmov',
                       cod_paisxx = '$conduc->cod_paisxx',
                       cod_depart = '$conduc->cod_depart',
                       cod_ciudad = '$conduc->cod_ciudad',
                                  $foto
                       obs_tercer = '$conduc->obs_tercer',
                       usr_modifi = '$conduc->usr_modifi',
                       fec_modifi = '$conduc->fec_modifi'
                       WHERE cod_tercer = '$conduc->cod_tercer'";
        $insercion = new Consulta($query, self::$cConexion, "R");

        #modifica los datos adicionales del conductor
        $query = "UPDATE ".BASE_DATOS.".tab_tercer_conduc
                          SET
                       cod_tipsex = '$conduc->cod_tipsex',
                       cod_grupsa = '$conduc->cod_grupsa',
                       num_licenc = '$conduc->num_licenc',
                       num_catlic = '$conduc->num_catlic',
                       fec_venlic = '$conduc->fec_venlic',
                       cod_califi = '$conduc->cod_califi',
                       nom_epsxxx = '$conduc->nom_epsxxx',
                       nom_arpxxx = '$conduc->nom_arpxxx',
                       nom_pensio = '$conduc->nom_pensio',
                       nom_refper = '$conduc->nom_refper',
                       tel_refper = '$conduc->tel_refper',
                       cod_operad = '$conduc->cod_operad',
                       usr_modifi = '$conduc->usr_modifi',
                       fec_modifi = '$conduc->fec_modifi'
                       WHERE cod_tercer = '$conduc->cod_tercer'";

        $insercion = new Consulta($query, self::$cConexion, "R");

        #actualiza si es poseedor o tenedor de vehiculo
        if($conduc->cod_propie==1){
          $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$conduc->cod_tercer' AND cod_activi = '".COD_FILTRO_PROPIE."'";
          $insercion = new Consulta($query,self::$cConexion,"R");

              $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                    VALUES ('$conduc->cod_tercer',".COD_FILTRO_PROPIE.")";
            $insercion = new Consulta($query,self::$cConexion,"R");

        }else{
          $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$conduc->cod_tercer' AND cod_activi = '".COD_FILTRO_PROPIE."'";
          $insercion = new Consulta($query,self::$cConexion,"R");
        }
        if($conduc->cod_tenedo==1){
          $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$conduc->cod_tercer' AND cod_activi = '".COD_FILTRO_POSEED."'";
          $insercion = new Consulta($query,self::$cConexion,"R");

         $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
                VALUES ('$conduc->cod_tercer',".COD_FILTRO_POSEED.")";
         $insercion = new Consulta($query,self::$cConexion,"R");
        }else{
          $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$conduc->cod_tercer' AND cod_activi = '".COD_FILTRO_POSEED."'";
          $insercion = new Consulta($query,self::$cConexion,"R");
        }
         $query = "SELECT cod_tercer FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$conduc->cod_tercer' AND cod_activi = ".COD_FILTRO_CONDUC;
         $consulta = new Consulta($query, self::$cConexion);
         $actividad = $consulta->ret_matriz("a");
         if(!$actividad){
            #inserta los adicionales y referencias laborles
          $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
              VALUES ('$conduc->cod_tercer',".COD_FILTRO_CONDUC.")";
          $insercion = new Consulta($query,self::$cConexion,"R");
         }
         

        #modifica las referencias laborales e ingresa nuevas si fuere el caso
        $empresas = $_POST['empresa']; #arreglo con los nombres de las empresas
        $telefonos = $_POST['telefono']; #arreglo con los numeros de telefono
        $viajes = $_POST['viajes']; #arreglo con los numeros de viajes
        $antiguedades = $_POST['antiguedad']; #arreglo con las antiguedades
        $mercancias = $_POST['mercancia']; #arreglo con las mercancias
        #se crea un array para almacenar las referencias enviadas
        $NewReferencias = array();
        for($ref=0;$ref<=sizeof($empresas);$ref++){
          $NewReferencias[$ref]=array('empresa'=>$empresas[$ref], 'telefono'=>$telefonos[$ref], 'viajes'=>$viajes[$ref],'antiguedad'=>$antiguedades[$ref], 'mercancias'=>$mercancias[$ref]);
        }
        $query = "DELETE FROM ".BASE_DATOS.".tab_conduc_refere WHERE cod_conduc = '$conduc->cod_tercer'";
        $insercion = new Consulta($query,self::$cConexion,"R");
        $h=0;
        foreach ($NewReferencias as $key => $value) {
            
          if($value['empresa'] || $value['telefono'] || $value['viajes'] || $value['antiguedad'] || $value['mercancias'])
          {
            $query = "INSERT INTO ".BASE_DATOS.".tab_conduc_refere
                            (cod_conduc,cod_refere,nom_empre,tel_empre,num_viajes,
                             num_atigue,nom_mercan,usr_creaci,fec_creaci)
                    VALUES ('$conduc->cod_tercer',".($h++).",'".$value['empresa']."','".$value['telefono']."','".$value['viajes']."',
                            '".$value['antiguedad']."','".$value['mercancias']."','$conduc->usr_modifi',NOW())";
            $insercion = new Consulta($query,self::$cConexion,"R");
          }
      }
      if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
          header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1060&menant=1060&window=central&resultado=1&operacion='.$operacion.'&opcion=123&conductor='.$conduc->abr_tercer);
            /*$mensaje = "<font color='#000000'>Se Modificó El Conductor: <b>$conduc->abr_tercer</b> Exitosamente.<br></font>";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->correcto2("MODIFICAR CONDUCTOR", $mensaje);*/


      }else{
          header('Location: ../../'.NOM_URL_APLICA.'/index.php?cod_servic=1060&menant=1060&window=central&opcion=123&resultado=0');
          /*$mensaje = "<font color='#000000'>Ocurrió un Error inesperado <b>$conduc->abr_tercer</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
          $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
          $mens = new mensajes();
          echo $mens->error2("MODIFICAR CONDUCTOR", $mensaje);*/

      }
  }

    /******************************************************************************
     *  \fn: getDatosConductor                                                    *
     *  \brief: función que consulta los datos de un coductor                     *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 16/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return $data => objeto con los datos de la consulta                      *
    ******************************************************************************/

    public function getDatosConductor($cod_tercer = '0'){
      #objeto que contiene los datos a retornar 
      $datos = new stdClass();

      $genero [0][0]=1;
      $genero [0][1]="Masculino";
      $genero [1][0]=2;
      $genero [1][1]="Femenino";
      $datos->genero = $genero;

      #añade los grupos sianguineos
      $query = "SELECT nom_tiporh,nom_tiporh
              FROM ".BASE_DATOS.".tab_genera_tiporh
          ORDER BY 2";
      $consulta = new Consulta($query, self::$cConexion);
      $grupoSanguineo = $consulta->ret_matriz("a");
      $datos->grupoSanguineo = $grupoSanguineo;

      #añade los tipod de documento
      $query = "SELECT cod_tipdoc,nom_tipdoc
                 FROM ".BASE_DATOS.".tab_genera_tipdoc
                 WHERE cod_tipdoc <> 'N' and
                 cod_tipdoc <> 'T'";
      $consulta = new Consulta($query, self::$cConexion);
      $tipoDocumento = $consulta -> ret_matriz("a");
      $datos->tipoDocumento = $tipoDocumento;

      #añade los operadores
      $query = "SELECT cod_operad,nom_operad
            FROM ".BASE_DATOS.".tab_operad_operad
        ORDER BY 2 ";
      $consulta = new Consulta($query, self::$cConexion);
      $operador = $consulta -> ret_matriz("a");
      $datos->operador = $operador;

      #añade las calificaciones
      $query = "SELECT cod_califi,nom_califi
               FROM ".BASE_DATOS.".tab_genera_califi
               ORDER BY 2";
      $consulta = new Consulta($query, self::$cConexion);
      $calificacion = $consulta -> ret_matriz("a");
      $datos->calificacion = $calificacion;

      #añade las categorias de licencia
      $query = "SELECT cod_catlic,nom_catlic
                 FROM ".BASE_DATOS.".tab_genera_catlic
             ORDER BY 2";
      $consulta = new Consulta($query, self::$cConexion);
      $categorias = $consulta -> ret_matriz("a");

      $datos->categorias = $categorias;
      #consulta si es poseedor y/o propietario
      $query = "SELECT 1 AS cod_activi FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$cod_tercer' AND cod_activi = '".COD_FILTRO_PROPIE."'";
      $consulta = new Consulta($query, self::$cConexion);
      $propietario = $consulta -> ret_matrix("a");
      $datos->cod_propie = $propietario[0]['cod_activi'];

      $query = "SELECT 1 AS cod_activi FROM ".BASE_DATOS.".tab_tercer_activi WHERE cod_tercer = '$cod_tercer' AND cod_activi = '".COD_FILTRO_POSEED."'";
      $consulta = new Consulta($query, self::$cConexion);
      $poseedor = $consulta -> ret_matrix("a");
      $datos->cod_tenedo = $poseedor[0]['cod_activi'];
      #consulta los datos del conductor
      $query = "SELECT  a.cod_tercer,a.cod_tipdoc,a.nom_apell1,a.nom_apell2,a.nom_tercer,a.abr_tercer,
                        a.dir_domici,a.num_telef1,a.num_telef2,a.num_telmov,a.cod_paisxx,a.cod_depart,
                        a.cod_ciudad,a.cod_estado,a.dir_ultfot,a.obs_tercer,b.cod_tipsex,b.cod_grupsa,
                        b.num_licenc,b.num_catlic,b.fec_venlic,b.cod_califi,b.nom_epsxxx,b.nom_arpxxx,
                        b.nom_pensio,b.nom_refper,b.tel_refper,b.cod_operad, CONCAT( UPPER(c.abr_ciudad), '(', LEFT(d.nom_depart, 4), ') - ', LEFT(e.nom_paisxx, 3) ) abr_ciudad
                 FROM ".BASE_DATOS.".tab_tercer_tercer a
                 INNER JOIN ".BASE_DATOS.".tab_tercer_conduc b ON b.cod_tercer = a.cod_tercer
                 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c ON c.cod_ciudad = a.cod_ciudad
                 INNER JOIN ".BASE_DATOS.".tab_genera_depart d ON d.cod_depart = c.cod_depart
                 INNER JOIN ".BASE_DATOS.".tab_genera_paises e ON e.cod_paisxx = c.cod_paisxx
                 WHERE a.cod_tercer = '$cod_tercer' ";

      $consulta = new Consulta($query, self::$cConexion);
      $conductor = $consulta -> ret_matrix("a");

      #agrega el resultado al objeto de retorno
      $datos->principal =(object) $conductor[0];
      
      #por ultimo consulta todas las referencias laborales del conductor
      $query = "SELECT nom_empre,tel_empre,num_viajes,num_atigue,nom_mercan FROM ".BASE_DATOS.".tab_conduc_refere WHERE cod_conduc = '$cod_tercer'";
      $consulta = new Consulta($query, self::$cConexion);
      $referencias = $consulta -> ret_matrix("a");

      foreach ($referencias as $key => $value) {
       $datos->referencias->$key = (object) $value;
      }
      return $datos;
    }

    /*! \fn: verificar
        *  \brief: funcion para saber si se repite un numero de documento o nit
        *  \author: Ing. Alexander Correa
        *  \date: 29/09/2015
        *  \date modified: dia/mes/año
        *  \param: 
        *  \param: 
        *  \return true si no existe, false si ya esta registrado
        */
          
    public function verificar(){
        $documento = $_POST['documento'];
        $query = "SELECT cod_tercer
            FROM ".BASE_DATOS.".tab_tercer_tercer
            WHERE cod_tercer = '$documento'";
      $consulta = new Consulta($query,  self::$cConexion, "BR");
      $indter = $consulta -> ret_arreglo();
        if(!$indter){
            echo true;
        }else{
            $datos = $this->getDatosConductor($documento);
            $datos = json_encode($datos);
            echo $datos;
        }

    }

    
}

if($_REQUEST[Ajax] === 'on' )
  $_INFORM = new conduc();

?>
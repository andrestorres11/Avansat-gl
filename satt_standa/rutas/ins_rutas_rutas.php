<?php
/*! \file: ins_rutas_rutas.php
 *  \brief: Insertar Rutas
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

/*! \class: Proc_rutas
 *  \brief: Clase para insertar nuevas rutas
 */
class Proc_rutas
{
  private static $cRutas;
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    @include_once("../".DIR_APLICA_CENTRAL."/lib/general/functions.inc");
    @include_once("../".DIR_APLICA_CENTRAL."/rutas/class_rutasx_rutasx.php");

    Proc_rutas::$cRutas = new rutas( $co, $us, $ca );
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;

    IncludeJS("jquery.js");
    IncludeJS("ruta.js");
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
  
    if(!isset($_REQUEST[opcion]))
      Proc_rutas::Captura_Ruta();
    else
    {
      switch($_REQUEST[opcion])
      {
        case "1":
          Proc_rutas::Insert_Ruta();
          break;
        case "2":
          Proc_rutas::Captura_Ruta();
          break;
      }
    }
  }

  /*! \fn: Captura_Ruta
   *  \brief: Formulario
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 05/08/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return:
   */
  private function Captura_Ruta()
  {
    $mUltiPc = Proc_rutas::$cRutas -> getUltiPC();

    $formulario = new Formulario ("index.php","post","INSERTAR RUTAS","form_ins\" id=\"form_insID");

    $formulario -> nueva_tabla();
    $formulario -> linea("Datos B&aacute;sicos",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> oculto("cod_ciuori\" id=\"cod_ciuoriID", $_REQUEST[cod_ciuori], 0);
    $formulario -> oculto("cod_ciudes\" id=\"cod_ciudesID", $_REQUEST[cod_ciudes], 0);
    $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 0, 40, 40, "", $_REQUEST[origen], 0, 0, 0, 1 );
    $formulario -> texto( "Destino:", "text", "destino\" id=\"destinoID", 1, 40, 40, "", $_REQUEST[destino], 0, 0, 0, 1 );

    $formulario -> texto( "Via:", "text", "nom\" id=\"nomID", 0, 40, 40, "", $_REQUEST[nom], 0, 0, 0, 1 );

    $formulario -> nueva_tabla();
    $formulario -> linea("Puestos de Control",1,"t2");

    $formulario -> nueva_tabla();
    
    if(!$_REQUEST[cont])
      $cont = 1;
    elseif($_REQUEST[adican])
      $cont = $_REQUEST[cont] + $_REQUEST[adican];
    else
      $cont = $_REQUEST[cont];

    #Inicia Ciclo Puesto de Control 
    for ($i=0; $i < $cont; $i++)
    {
      $formulario -> oculto("cod_contro[$i]\" id=\"cod_contro".$i."ID", $_REQUEST[cod_contro][$i], 0);
      $formulario -> texto("Puesto de Control:", "text", "contro[$i]\" id=\"controID[$i]\" nameID=\"cod_contro".$i."ID", 0, 40, 40, "", $_REQUEST[contro][$i] );
      $formulario -> texto("Desde el Origen (Duraci&oacute;n Min)","text","val[$i]",0,5,4,"",$_REQUEST[val][$i]);
      $formulario -> texto("(Distancia K/m)","text","kil[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,6,6,"",$_REQUEST[kil][$i]);
    }
    
    $formulario -> oculto("cod_ultipc\" id=\"cod_ultipcID", $mUltiPc[cod_contro], 0);
    $formulario -> texto("Puesto de Control:", "text", "codipcult\" disabled id=\"codipcultID", 0, 40, 40, "", $mUltiPc[nom_contro], 0, 0, 0, 1 );
    $formulario -> texto("Desde el Origen (Duraci&oacute;n Min)","text","tiempcult",0,5,4,"",$_REQUEST[tiempcult]);
    $formulario -> texto("(Distancia K/m)","text","kilulti\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,6,6,"",$_REQUEST[kilulti]);
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Aceptar","aceptar_insert()",0);
    $formulario -> texto("Adicionar Puesto(s) de Control","text","adican\" onChange=\"form_ins.submit()",0,2,2,"","");
    if($cont > 1)
      $formulario -> botoni("Borrar","form_ins.cont.value--; form_ins.submit();",1);

    $formulario -> oculto("window\" id=\"windowID", "central", 0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    $formulario -> oculto("opcion\" id=\"opcionID", 2, 0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID", $_REQUEST['cod_servic'], 0);
    $formulario -> oculto("cont",$cont,0);
    $formulario -> cerrar();
  }

  /*! \fn: Insert_Ruta
   *  \brief: Inserta la Nueva Ruta
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 05/08/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return:
   */
  private function Insert_Ruta()
  {
    $mClassDespac = new Despachos( $_REQUEST[cod_servic], $_REQUEST[opcion], $this -> aplica, $this -> conexion );
    $mCiuOri = $mClassDespac -> getSeleccCiudad( $_REQUEST[cod_ciuori] );
    $mCiuDes = $mClassDespac -> getSeleccCiudad( $_REQUEST[cod_ciudes] );

    $mNomRuta = $mCiuOri[0][1]." - ".$mCiuDes[0][1]." VIA ".$_REQUEST[nom];
    $mNewRuta = Proc_rutas::$cRutas -> getNextCodRuta();
    $mPaiDepOri = Proc_rutas::$cRutas -> getPaisDepart( $_REQUEST[cod_ciuori] );
    $mPaiDepDes = Proc_rutas::$cRutas -> getPaisDepart( $_REQUEST[cod_ciudes] );
    $mUsuari = $_SESSION[datos_usuario][cod_usuari];

    #Insertar Ruta
    $mSql = " INSERT INTO ".BASE_DATOS.".tab_genera_rutasx
                      (
                        cod_rutasx, nom_rutasx, ind_estado, 
                        cod_paiori, cod_depori, cod_ciuori, 
                        cod_paides, cod_depdes, cod_ciudes, 
                        usr_creaci, fec_creaci 
                      )
              VALUES (
                        '{$mNewRuta}', '{$mNomRuta}', '".COD_ESTADO_ACTIVO."', 
                        '{$mPaiDepOri[cod_paisxx]}', '{$mPaiDepOri[cod_depart]}', '{$_REQUEST[cod_ciuori]}', 
                        '{$mPaiDepDes[cod_paisxx]}', '{$mPaiDepDes[cod_depart]}', '{$_REQUEST[cod_ciudes]}', 
                        '{$mUsuari}', NOW() 
                     )";
    $mConsult = new Consulta($mSql, $this -> conexion, "R");

    #Inserta Puestos de control de la nueva ruta
    for($i=0; $i<$_REQUEST[cont]; $i++)
    {
      $mSql = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon
                      (
                        cod_rutasx, cod_contro, val_duraci, 
                        val_distan, usr_creaci, fec_creaci, 
                        ind_estado
                      )
               VALUES (
                        '{$mNewRuta}', '{$_REQUEST[cod_contro][$i]}', '{$_REQUEST[val][$i]}', 
                        '{$_REQUEST[kil][$i]}', '{$mUsuari}', NOW(), 
                        '".COD_ESTADO_ACTIVO."'
                      )";
      $mConsult = new Consulta($mSql, $this -> conexion, "R");
    }

    #Inserta el ultimo PC de la nueva ruta (Lugar entrega)
    $mSql = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon
                    (
                      cod_rutasx, cod_contro, val_duraci, 
                      val_distan, usr_creaci, fec_creaci, 
                      ind_estado
                    )
             VALUES (
                      '{$mNewRuta}', '{$_REQUEST[cod_ultipc]}', '{$_REQUEST[tiempcult]}', 
                      '{$_REQUEST[kilulti]}', '{$mUsuari}', NOW(), 
                      '".COD_ESTADO_ACTIVO."'
                    )";
    $mConsult = new Consulta($mSql, $this -> conexion, "R");

    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
      $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otra Ruta</a></b>";

      $mensaje = "La Ruta <b>".$_REQUEST[nom]."</b> se Inserto con Exito<br>".$link;
      $mens = new mensajes();
      $mens -> correcto("INSERTAR RUTAS",$mensaje);
    }
  }

}

$proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>
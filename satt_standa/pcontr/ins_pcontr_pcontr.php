<?php
/*! \file: ins_pcontr_pcontr.php
 *  \brief: Inserta puestos de control Padres
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \class: Proc_contro
 *  \brief: Inserta puestos de control Padres
 */
class Proc_contro
{
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }

  /*! \fn: principal
   *  \brief: 
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function principal()
  {
    if(!isset($_REQUEST[opcion]))
      $this -> Formulario();
    else
    {
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Formulario();
          break;

        case "2":
          $this -> Insertar();
          break;
      }//FIN SWITCH
    }// FIN ELSE GLOBALS OPCION
  }//FIN FUNCION PRINCIPAL

  /*! \fn: Formulario
   *  \brief: Formulario par crear un puesto de control
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 25/05/2015
   *  \param: 
   *  \return:
   */
  function Formulario()
  {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];

     $mArraySentido = array(0 => array('', '---'), 1 => array('S-N/N-S', 'S-N/N-S'), 2 => array('S-N', 'S-N'), 3 => array('N-S', 'N-S'), 4 => array('E-W/W-E', 'E-W/W-E'), 5 => array('E-W', 'E-W'), 6 => array('W-E', 'W-E') );

     $inicio[0][0]=0;
     $inicio[0][1]='-';

     $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
     $ciudad = $objciud -> getListadoCiudades();

     $ciudades = array_merge($inicio,$ciudad);
     $ciudagen = array_merge($inicio,$ciudad);

     if($_REQUEST[ciudad])
     {
      $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST[ciudad]);
      $ciudades = array_merge($ciudad_a,$ciudades);
     }

     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/contro.js\"></script>\n";
     $formulario = new Formulario ("index.php\" enctype=\"multipart/form-data\" onLoad = \"LoadWindow()\" ","post","INGRESO DE PUESTOS DE CONTROL","form_insert");

     $formulario -> linea("Datos B&aacute;sicos",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto ("Nombre:","text","nom",0,50,100,"",$_REQUEST[nom]);
     $formulario -> texto ("Nombre del Encargado:","text","enc",1,30,100,"",$_REQUEST[enc]);
     $formulario -> texto ("Telefono:","text","tel",0,30,20,"",$_REQUEST[tel]);
     $formulario -> lista ("Ciudad:", "ciudad", $ciudades, 1);
     $formulario -> texto ("Direcci&oacute;n:","text","dir",0,50,400,"",$_REQUEST[dir]);
     $formulario -> lista ("Sentido Vial:", "sentido\" id=\"sentidoID", $mArraySentido, 1);
     $formulario -> texto ("Sentido 1:","text","sent1\" id=\"sent1ID",0,50,250,"", NULL);
     $formulario -> texto ("Sentido 2:","text","sent2\" id=\"sent2ID",1,50,250,"", NULL);
     $formulario -> texto ("* Latitud:","text","longi\" onkeyup=\"reload_url()",0,30,30,"",$_REQUEST[longi]);
     $formulario -> texto ("* Longitud:","text","latit\" onkeyup=\"reload_url()",1,30,30,"",$_REQUEST[latit]);
     $formulario -> texto ("Url Waze:","text","url_wazexx\" readonly=\"readonly",0,30,30,"",$_REQUEST[url_wazexx]);
     $formulario -> texto ("Url Google Maps:","text","url_google\" readonly=\"readonly",1,30,30,"",$_REQUEST[url_google]);
     $formulario -> texto ("Temperatura:","text","tempe",0,30,5,"",$_REQUEST[tempe]);
     $formulario -> texto ("Altimetria:","text","altim",1,30,10,"",$_REQUEST[altim]);

     $formulario -> radio ("Puesto Fisico:","virtua\" id=\"virtuaID","0",0,0);
     $formulario -> radio ("Puesto Virtual:","virtua\" id=\"virtuaID","1",0,1);

     $formulario -> caja ("Puesto Urbano:","urbano \" id=\"urbanoID\"  ",1,0,0); //onclick=\"ShowRow();\"  checked=\"checked\"
     $formulario -> archivo("Foto:","foto",12,200,"",1);
     $formulario -> texto ("Color:","text","color\"  id=\"colorID\" onBlur=\"setColor()\"",0,7,7,"","$_REQUEST[color]");
     $formulario -> oculto("MAX_FILE_SIZE", "2000000", 0);

     echo "<td nowrap='nowrap' id='coloreal'>
           <a href='#' onClick=\"newwin=open('../".DIR_APLICA_CENTRAL."/pcontr/colores.php?nom=color', 'calwin', 'width=320, height=300, scollbars=false');\" class='etiqueta'>Cambiar Color</a>
           &nbsp;&nbsp;&nbsp;
           <span id='test' title='test' style=\"background:#;\">
           <a href=\"#\" onClick=\"newwin=open('../".DIR_APLICA_CENTRAL."/pcontr/colores.php?nom=color', 'calwin', 'width=320, height=300, scollbars=false');\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/shim.gif\" border=\"1\" width=\"40\" height=\"20\" /></a></span>
          </td>";

     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",1,0);
     //$formulario -> oculto("maximo",$interfaz -> cant_interf,0);
     $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
     $formulario -> botoni("Insertar","aceptar_insert()",0);
     $formulario -> botoni("Borrar","form_insert.reset()",1);
     $formulario -> cerrar();
  }//FIN FUNCTION CAPTURA

  /*! \fn: Insertar
   *  \brief: Inserta el puesto de control
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 25/05/2015
   *  \param:
   *  \return:
   */
  function Insertar()
  {
     $fec_actual = date("Y-m-d H:i:s");

     $zona = $_REQUEST[zona];
     $operador = $_REQUEST[operador_gps];
     $nom_operador = $_REQUEST[nom_operador_gps];

     if(!$_REQUEST[urbano])
      $_REQUEST[urbano] = 0;

     //trae el consecutivo de la tabla
     $query = "SELECT Max(cod_contro)
                 FROM ".BASE_DATOS.".tab_genera_contro
  	      WHERE cod_contro != ".CONS_CODIGO_PCLLEG."
  	    ";

     $consec = new Consulta($query, $this -> conexion,"R");

     $ultimo = $consec -> ret_matriz();
     $ultimo_consec = $ultimo[0][0];
     $nuevo_consec = $ultimo_consec+1;

     if($_REQUEST[foto])
     {
      if(move_uploaded_file($_REQUEST[foto],URL_PCTCON.$nuevo_consec.".jpg"))
       $_REQUEST[foto] = "'".$nuevo_consec.".jpg'";
      else
       $_REQUEST[foto] = "NULL";
     }
     else
      $_REQUEST[foto] = "NULL";

     if(!$_REQUEST[longi])
      $_REQUEST[longi] = "NULL";
     else
      $_REQUEST[longi] = "'".$_REQUEST[longi]."'";

     if(!$_REQUEST[latit])
      $_REQUEST[latit] = "NULL";
     else
      $_REQUEST[latit] = "'".$_REQUEST[latit]."'";

     if(!$_REQUEST[tempe])
      $_REQUEST[tempe] = "NULL";
     else
      $_REQUEST[tempe] = "'".$_REQUEST[tempe]."'";

     if(!$_REQUEST[altim])
      $_REQUEST[altim] = "NULL";
     else
      $_REQUEST[altim] = "'".$_REQUEST[altim]."'";
 
     //query de insercion
     $query = "INSERT INTO ".BASE_DATOS.".tab_genera_contro
               	         (cod_contro,nom_contro,cod_ciudad,nom_encarg,
          			  			  dir_contro,tel_contro,ind_virtua,val_longit,
          			  			  val_latitu,val_temper,val_altitu,dir_fotopc,
          			  			  usr_creaci,fec_creaci,ind_estado,ind_urbano,cod_colorx, ind_pcpadr, 
                          val_senvia, dir_senti1, dir_senti2,
                          url_wazexx, url_google
          	     		 		 )
                       	  VALUES 
                          ( ".$nuevo_consec.", '".$_REQUEST[nom]."', 
                            ".$_REQUEST[ciudad].", '".$_REQUEST[enc]."', '".$_REQUEST[dir]."', 
                            '".$_REQUEST[tel]."', '".$_REQUEST[virtua]."', ".$_REQUEST[longi].", 
                            ".$_REQUEST[latit].", ".$_REQUEST[tempe].", ".$_REQUEST[altim].", 
                            ".$_REQUEST[foto].", '".$_REQUEST[usuario]."', '".$fec_actual."', 
                            '".COD_ESTADO_ACTIVO."', ".$_REQUEST[urbano].", '".$_REQUEST[color]."',  '1', 
                            '".$_REQUEST[sentido]."', '".$_REQUEST[sent1]."', '".$_REQUEST[sent2]."' 
                            , '".$_REQUEST['url_wazexx']."','".$_REQUEST['url_google']."' )  ";

     $consulta = new Consulta($query, $this -> conexion,"BR");
     $pcinterf = $_REQUEST[pcinterf];
     $operad = $_REQUEST[operad];


    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
     $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otro Puesto de Control</a></b>";

     $mensaje = "El Puesto de Control <b>".$_REQUEST[nom]."</b> se Inserto con Exito<br>".$link;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR PUESTO DE CONTROL",$mensaje);
    }
  }

}
$proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
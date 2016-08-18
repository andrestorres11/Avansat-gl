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
    if(!isset($GLOBALS[opcion]))
      $this -> Formulario();
    else
    {
      switch($GLOBALS[opcion])
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

     $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
     $ciudad = $objciud -> getListadoCiudades();

     $ciudades = array_merge($inicio,$ciudad);
     $ciudagen = array_merge($inicio,$ciudad);

     if($GLOBALS[ciudad])
     {
      $ciudad_a = $objciud -> getSeleccCiudad($GLOBALS[ciudad]);
      $ciudades = array_merge($ciudad_a,$ciudades);
     }

     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/contro.js\"></script>\n";
     $formulario = new Formulario ("index.php\" enctype=\"multipart/form-data\" onLoad = \"LoadWindow()\" ","post","INGRESO DE PUESTOS DE CONTROL","form_insert");

     $formulario -> linea("Datos B&aacute;sicos",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto ("Nombre:","text","nom",0,50,100,"",$GLOBALS[nom]);
     $formulario -> texto ("Nombre del Encargado:","text","enc",1,30,100,"",$GLOBALS[enc]);
     $formulario -> texto ("Telefono:","text","tel",0,30,20,"",$GLOBALS[tel]);
     $formulario -> lista ("Ciudad:", "ciudad", $ciudades, 1);
     $formulario -> texto ("Direcci&oacute;n:","text","dir",0,50,400,"",$GLOBALS[dir]);
     $formulario -> lista ("Sentido Vial:", "sentido\" id=\"sentidoID", $mArraySentido, 1);
     $formulario -> texto ("Sentido 1:","text","sent1\" id=\"sent1ID",0,50,250,"", NULL);
     $formulario -> texto ("Sentido 2:","text","sent2\" id=\"sent2ID",1,50,250,"", NULL);
     $formulario -> texto ("Longitud:","text","longi",0,30,30,"",$GLOBALS[longi]);
     $formulario -> texto ("Latitud:","text","latit",1,30,30,"",$GLOBALS[latit]);
     $formulario -> texto ("Temperatura:","text","tempe",0,30,5,"",$GLOBALS[tempe]);
     $formulario -> texto ("Altimetria:","text","altim",1,30,10,"",$GLOBALS[altim]);

     $formulario -> radio ("Puesto Fisico:","virtua\" id=\"virtuaID","0",0,0);
     $formulario -> radio ("Puesto Virtual:","virtua\" id=\"virtuaID","1",0,1);

     $formulario -> caja ("Puesto Urbano:","urbano \" id=\"urbanoID\"  ",1,0,0); //onclick=\"ShowRow();\"  checked=\"checked\"
     $formulario -> archivo("Foto:","foto",12,200,"",1);
     $formulario -> texto ("Color:","text","color\"  id=\"colorID\" onBlur=\"setColor()\"",0,7,7,"","$GLOBALS[color]");
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
     $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
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

     $zona = $GLOBALS[zona];
     $operador = $GLOBALS[operador_gps];
     $nom_operador = $GLOBALS[nom_operador_gps];

     if(!$GLOBALS[urbano])
      $GLOBALS[urbano] = 0;

     //trae el consecutivo de la tabla
     $query = "SELECT Max(cod_contro)
                 FROM ".BASE_DATOS.".tab_genera_contro
  	      WHERE cod_contro != ".CONS_CODIGO_PCLLEG."
  	    ";

     $consec = new Consulta($query, $this -> conexion,"R");

     $ultimo = $consec -> ret_matriz();
     $ultimo_consec = $ultimo[0][0];
     $nuevo_consec = $ultimo_consec+1;

     if($GLOBALS[foto])
     {
      if(move_uploaded_file($GLOBALS[foto],URL_PCTCON.$nuevo_consec.".jpg"))
       $GLOBALS[foto] = "'".$nuevo_consec.".jpg'";
      else
       $GLOBALS[foto] = "NULL";
     }
     else
      $GLOBALS[foto] = "NULL";

     if(!$GLOBALS[longi])
      $GLOBALS[longi] = "NULL";
     else
      $GLOBALS[longi] = "'".$GLOBALS[longi]."'";

     if(!$GLOBALS[latit])
      $GLOBALS[latit] = "NULL";
     else
      $GLOBALS[latit] = "'".$GLOBALS[latit]."'";

     if(!$GLOBALS[tempe])
      $GLOBALS[tempe] = "NULL";
     else
      $GLOBALS[tempe] = "'".$GLOBALS[tempe]."'";

     if(!$GLOBALS[altim])
      $GLOBALS[altim] = "NULL";
     else
      $GLOBALS[altim] = "'".$GLOBALS[altim]."'";

     //query de insercion
     $query = "INSERT INTO ".BASE_DATOS.".tab_genera_contro
               	         (cod_contro,nom_contro,cod_ciudad,nom_encarg,
          			  			  dir_contro,tel_contro,ind_virtua,val_longit,
          			  			  val_latitu,val_temper,val_altitu,dir_fotopc,
          			  			  usr_creaci,fec_creaci,ind_estado,ind_urbano,cod_colorx, ind_pcpadr, 
                          val_senvia, dir_senti1, dir_senti2 
          	     		 		 )
                       	  VALUES 
                          ( ".$nuevo_consec.", '".$GLOBALS[nom]."', 
                            ".$GLOBALS[ciudad].", '".$GLOBALS[enc]."', '".$GLOBALS[dir]."', 
                            '".$GLOBALS[tel]."', '".$GLOBALS[virtua]."', ".$GLOBALS[longi].", 
                            ".$GLOBALS[latit].", ".$GLOBALS[tempe].", ".$GLOBALS[altim].", 
                            ".$GLOBALS[foto].", '".$GLOBALS[usuario]."', '".$fec_actual."', 
                            '".COD_ESTADO_ACTIVO."', ".$GLOBALS[urbano].", '".$GLOBALS[color]."',  '1', 
                            '".$_REQUEST[sentido]."', '".$_REQUEST[sent1]."', '".$_REQUEST[sent2]."' )  ";

     $consulta = new Consulta($query, $this -> conexion,"BR");
     $pcinterf = $GLOBALS[pcinterf];
     $operad = $GLOBALS[operad];


    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
     $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Insertar Otro Puesto de Control</a></b>";

     $mensaje = "El Puesto de Control <b>".$GLOBALS[nom]."</b> se Inserto con Exito<br>".$link;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR PUESTO DE CONTROL",$mensaje);
    }
  }

}
$proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
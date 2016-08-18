<?php
/*! \file: eli_homolo_puesto.php
 *  \brief: Desactiva puestos Hijos
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

/*! \class: EliHomoloPc
 *  \brief: Desactiva los puestos de contrl hijos
 */
class EliHomoloPc
{
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    EliHomoloPc::principal();
  }

 
  /*! \fn: principal
   *  \brief: 
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function principal()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/eli_homolo_puesto.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    switch($_REQUEST[opcion])
    {
      case'1':
        EliHomoloPc::Listar();
        break;

      case '2':
        EliHomoloPc::Desactivar();
        break;

      default:
        EliHomoloPc::Buscar();
        break;
    }
  }

  /*! \fn: Buscar
   *  \brief: Formulario con los filtros para realizar la busqueda
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Buscar()
  {
    $formulario = new Formulario ("index.php","post","BUSCAR PUESTOS DE CONTROL","form_list");
    $formulario -> linea("Digite el Nombre del P/C para Iniciar la B&uacute;squeda",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Texto","text","contro",0,50,255,"","");
    $formulario -> caja ("Puestos Virtuales:","virtua",1,0,0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion",1,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> botoni("Buscar","form_list.submit()",0);
    $formulario -> cerrar();
  }

  /*! \fn: Listar
   *  \brief: Lista el resultado de la busqueda
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Listar()
  {
   
    $mArrayPcHijos = EliHomoloPc::getPcHijos();
    
    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="1">';

    $mHtml .=   '<tr>';
    $mHtml .=     '<th class="CellHead"><input type="checkbox" name="marcarTodo"></th>';
    $mHtml .=     '<th class="CellHead">C&oacute;digo</th>';
    $mHtml .=     '<th class="CellHead">Descripci&oacute;n</th>';
    $mHtml .=     '<th class="CellHead">Direcci&oacute;n</th>';
    $mHtml .=     '<th class="CellHead">Tel&eacute;fono</th>';
    $mHtml .=     '<th class="CellHead">Encargado</th>';
    $mHtml .=     '<th class="CellHead">Puesto</th>';
    $mHtml .=   '</tr>';

    foreach ($mArrayPcHijos as $row)
    {
      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo"><input type="checkbox" name="cod_PcHijo[]" value="'.$row[0].'"></td>';
      $mHtml .=   '<td class="cellInfo">'.$row[0].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[1].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[2].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[3].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[4].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[5].''.$row[6].'&nbsp;</td>';
      $mHtml .= '</tr>';
    }

    $mHtml .= '</table>';

    $formulario = new Formulario ("index.php","post","DESACTIVAR PUESTOS DE CONTROL", "formulario\" id=\"formularioID" );

    $formulario -> nueva_tabla();
    $formulario -> linea("Seleccione los Puestos de Control que Desea Desactivar.",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontro un Total de ".sizeof($mArrayPcHijos)." Puesto(s) de Control",0,"t2");

    $formulario -> nueva_tabla();
    echo $mHtml;
    
    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> botoni("Desactivar","VerifyData()",0);
    $formulario -> cerrar();
  }

  /*! \fn: Desactivar
   *  \brief: Desactiva los puestos de control seleccionados
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Desactivar()
  {
    foreach ($_REQUEST[cod_PcHijo] as $cod_contro) {
      $mSql = " DELETE FROM ".BASE_DATOS.".tab_homolo_pcxeal 
                WHERE cod_homolo = '$cod_contro' ";
      $delete = new Consulta( $mSql, $this -> conexion,"R");

      $mSql = " UPDATE ".BASE_DATOS.".tab_genera_contro 
                SET ind_estado = '0', fec_modifi = NOW() , usr_modifi = '".$_SESSION[datos_usuario][nom_usuari]."' 
                WHERE cod_contro = '$cod_contro' ";
      $update = new Consulta( $mSql, $this -> conexion,"R");

      if($update)
        $mensaje = "Puesto de Control #".$cod_contro." Desactivado Exitosamente.";
      else
        $mensaje = "Problema al Desactivar el Puesto de Control #".$cod_contro.".";

      $mens = new mensajes();
      $mens -> correcto("DESACTIVAR PUESTOS DE CONTROL",$mensaje);
    }
  }

  /*! \fn: getPcHijos
   *  \brief: trae los puestos de control hijos
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function getPcHijos()
  {
    $mSql = "SELECT a.cod_contro, a.nom_contro, a.dir_contro, 
                    a.tel_contro, a.nom_encarg, 
                    if(a.ind_virtua = '0','Fisico','Virtual'), 
                    if(a.ind_urbano = '1',' - Urbano','')  
               FROM ".BASE_DATOS.".tab_genera_contro a 
              WHERE a.ind_pcpadr = '0' 
                AND a.ind_estado = '1' ";
    $mSql .= $_REQUEST[virtua] === '1' ? " AND a.ind_virtua = '1' " : " AND a.ind_virtua = '0' ";

    if($_REQUEST[contro] != NULL)
      $mSql .= " AND a.nom_contro LIKE '%$_REQUEST[contro]%' ";

    $mSql .= " ORDER BY a.nom_contro ";
    $mConsult = new Consulta($mSql, $this -> conexion);
    return $mResult = $mConsult -> ret_matrix('i');
  }

}//FIN CLASE EliHomoloPc


$proceso = new EliHomoloPc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
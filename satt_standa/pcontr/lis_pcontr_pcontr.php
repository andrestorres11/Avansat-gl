<?php
/*! \file: lis_pcontr_pcontr.php
 *  \brief: Lista puestos de control Padres
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \class: Proc_contro
 *  \brief: Lista puestos de control Padres
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
    switch($_REQUEST[opcion])
    {
      case "1":
        $this -> Resultado();
        break;

      default:
        $this -> Buscar();
        break;
    }//FIN SWITCH
  }//FIN FUNCION PRINCIPAL

  /*! \fn: Buscar
   *  \brief: Formulario de los filtros para realizar busqueda
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \param: 
   *  \return: 
   */
  function Buscar()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/contro.js\"></script>\n";
    $formulario = new Formulario ("index.php","post","BUSCAR PUESTOS DE CONTROL","form_list");
    $formulario -> linea("Digite el Nombre del P/C para Iniciar la B&uacute;squeda",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Texto","text","contro",0,50,255,"","");
    $formulario -> caja ("Puestos Virtuales:","virtua",1,0,0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",1,0);
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> botoni("Buscar","form_list.submit()",0);
    $formulario -> cerrar();
  }

  /*! \fn: Resultado
   *  \brief: Resultado de la busqueda
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 25/05/2015
   *  \param: 
   *  \param: 
   *  \return: 
   */
  function Resultado()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();
      $cond1 = " AND a.cod_contro IN( SELECT cod_contro FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_contro ) ";
    }

    $query = "SELECT a.cod_contro, a.nom_contro, a.dir_contro, 
                     a.tel_contro, a.nom_encarg, a.cod_colorx, 
                     a.ind_estado, a.val_senvia, a.dir_senti1, 
                     a.dir_senti2, 
                     if(a.ind_virtua = '0','Fisico','Virtual'),
                     if(a.ind_urbano = '1',' - Urbano','') 
                FROM ".BASE_DATOS.".tab_genera_contro a
               WHERE a.nom_contro LIKE '%$_REQUEST[contro]%' 
                 AND a.cod_contro != ".CONS_CODIGO_PCLLEG." ".$cond1."
                 AND a.ind_pcpadr = '1' ";

    $query .= $_REQUEST[virtua] != NULL ? " AND a.ind_virtua = '1' " : " AND a.ind_virtua = '0' ";
		$query .= " ORDER BY 2";
    $consec = new Consulta($query, $this -> conexion);
    $mArrayData = $consec -> ret_matrix('i');


    $mArrayTitu = array('C&oacute;digo', 'Descripci&oacute;n', 'Estado', 'Direcci&oacute;n', 'Sentidos Viales', 'Dir. Sentido 1', 'Dir. Sentido 2', 'Tel&eacute;fono', 'Encargado', 'Puesto');

    #Inico Dibuja tabla 
    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

    $mHtml .=   '<tr>';
    foreach ($mArrayTitu as $value) {
      $mHtml .= '<th class="CellHead">'.$value.'</th>';
    }
    $mHtml .=   '</tr>';

    foreach ($mArrayData as $row) 
    {
      if($row[6] == COD_ESTADO_ACTIVO)
        $estado = "Activo";
      else if($row[6] == COD_ESTADO_INACTI)
        $estado = "Inactivo";

      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo">'.$row[0].'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[1].'</td>';
      $mHtml .=   '<td class="cellInfo">'.$estado.'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[2].'</td>';
      $mHtml .=   '<td class="cellInfo">'. ($row[7] != NULL ? $row[7] : 'Sin Informaci&oacute;n') .'</td>';
      $mHtml .=   '<td class="cellInfo">'. ($row[8] != NULL ? $row[8] : '-') .'</td>';
      $mHtml .=   '<td class="cellInfo">'. ($row[9] != NULL ? $row[9] : '-') .'</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[3].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[4].'&nbsp;</td>';
      $mHtml .=   '<td class="cellInfo">'.$row[10].$row[11].'</td>';
      $mHtml .= '</tr>';
    }

    $mHtml .= '</table>';
    #Fin Dibuja tabla

    $formulario = new Formulario ("index.php","post","RESULTADO DE LA CONSULTA","form_item");
    $formulario -> linea("Se Encontro un Total de ".sizeof($mArrayData)." Puesto(s) de Control",0,"t2");

    echo $mHtml;

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> cerrar();
  }

}//FIN CLASE PROC_CONTRO

$proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
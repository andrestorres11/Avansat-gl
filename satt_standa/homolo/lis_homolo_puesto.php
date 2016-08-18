<?php
/*! \file: lis_homolo_puesto.php
 *  \brief: Lista los puestos homologados
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 22/04/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

/*! \class: LisHomoP
 *  \brief: Lista los puestos de control hijos segun padres
 */
class LisHomoP
{
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    LisHomoP::principal();
  }

  /*! \fn: principal
   *  \brief: 
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function principal()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/lis_homolo_puesto.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    switch($GLOBALS[opcion])
    {
      case "1":
        LisHomoP::PcPadres();
        break;

      default:
        LisHomoP::Buscar();
        break;
    }//FIN SWITCH
  }//FIN FUNCION PRINCIPAL

  /*! \fn: Formulario
   *  \brief: Formulario con los filtros para realizar la busqueda
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
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
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> botoni("Buscar","form_list.submit()",0);
    $formulario -> cerrar();
  }

  /*! \fn: PcPadres
   *  \brief: Lista los puestos de control padres
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function PcPadres()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();
      $cond1 = " AND a.cod_contro IN( SELECT cod_contro FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_contro ) ";
    }
   
    $query = "SELECT a.cod_contro,a.nom_contro,a.dir_contro,a.tel_contro,
  		   		         a.nom_encarg,'',if(a.ind_virtua = '0','Fisico','Virtual'),
  		   		         a.ind_estado,if(a.ind_urbano = '1',' - Urbano',''), a.cod_colorx
                FROM ".BASE_DATOS.".tab_genera_contro a
               WHERE a.nom_contro LIKE '%$GLOBALS[contro]%' 
                 AND a.cod_contro != ".CONS_CODIGO_PCLLEG." ".$cond1." 
                 AND a.ind_pcpadr = '1' ";
    $query .= $_REQUEST[virtua] === '1' ? " AND a.ind_virtua = '1' " : " AND a.ind_virtua = '0' ";
    $query .= " ORDER BY 2";

    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    $formulario = new Formulario ("index.php","post","PUESTOS DE CONTROL PADRES","form_item");
    $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Puesto(s) de Control",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Descripci&oacute;n",0,"t");
    $formulario -> linea("Cantidad PC Hijos",0,"t");
    $formulario -> linea("Estado",0,"t");
    #$formulario -> linea("Direcci&oacute;n",0,"t");
    $formulario -> linea("Tel&eacute;fono",0,"t");
    $formulario -> linea("Encargado",0,"t");
    $formulario -> linea("Puesto",1,"t");


    for($i=0;$i<sizeof($matriz);$i++)
    {
      $mSql = "SELECT count(a.cod_contro) AS cantidad 
                 FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
           INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                   ON a.cod_homolo = b.cod_contro 
                WHERE a.cod_contro = '".$matriz[$i][0]."' 
                  AND b.ind_estado = '1'
              ";
      $mConsult = new Consulta($mSql, $this -> conexion);
      $mCantidad = $mConsult -> ret_arreglo();

      if($matriz[$i][9] != NULL){
        #$estilo = $matriz[$i][9];
        $estilo = '#E6E6E6';
      }else{
        $estilo = "i";
      }

      if($matriz[$i][7] == COD_ESTADO_ACTIVO)
        $estado = "Activo";
      else if($matriz[$i][7] == COD_ESTADO_INACTI)
        $estado = "Inactivo";

      #$txt = '<a href="index.php?cod_servic='. $GLOBALS[cod_servic] .'&window=central&cod_contro='. $matriz[$i][0] .'&opcion=2 "target="centralFrame">'. $matriz[$i][0] .'</a>';
      $txt = '<span onclick="showListChildren('.$matriz[$i][0].')">'. $matriz[$i][0] .'</span>';

      $formulario -> linea($txt,0,$estilo,null,null,null,$estilo);
      $formulario -> linea($matriz[$i][1],0,$estilo,null,null,null,$estilo);
      $formulario -> linea($mCantidad[0],0,$estilo,null,null,null,$estilo);
      $formulario -> linea($estado,0,$estilo,null,null,null,$estilo);
      #$formulario -> linea($matriz[$i][2],0,$estilo,null,null,null,$estilo);
      $formulario -> linea($matriz[$i][3],0,$estilo,null,null,null,$estilo);
      $formulario -> linea($matriz[$i][4],0,$estilo,null,null,null,$estilo);
      $formulario -> linea($matriz[$i][6].$matriz[$i][8],1,$estilo,null,null,null,$estilo);
    }

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> oculto("standa",DIR_APLICA_CENTRAL,0);
    $formulario -> cerrar();
  }


}//FIN CLASE LisHomoP


$proceso = new LisHomoP($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
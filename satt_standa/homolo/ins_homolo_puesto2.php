<?php
/*! \file: ins_homolo_puesto2.php
 *  \brief: Asigna los puestos de control hijos a los padres
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

/*! \class: InsHomoP2
 *  \brief: Asigna los puestos de control hijos a los padres
 */
class InsHomoP2
{
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    InsHomoP2::principal();
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
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/ins_homolo_puesto2.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    switch($GLOBALS[opcion])
    {
      case "1":
        InsHomoP2::HomPuesto();
        break;

      case "2":
        InsHomoP2::Formulario();
        break;

      default:
        InsHomoP2::PreFormulario();
        break;

    }
  }

  /*! \fn: Style
   *  \brief: Estilos para las tablas
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Style()
  {
    echo "  <style>
            .cellHead
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:center;
            }
            
            .footer
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:left;
            }

            .cellHead2
            {
              padding:5px 10px;
              background: #03ad39;
              background: -webkit-gradient(linear, left top, left bottom, from( #03ad39 ), to( #00660f )); 
              background: -moz-linear-gradient(top, #03ad39, #00660f ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:right;
            }

            tr.row:hover  td
            {
              background-color: #9ad9ae;
            }
            .cellInfo
            {
              padding:5px 10px;
              background-color:#fff;
              border:1px solid #ccc;
            }

            .cellInfo2
            {
              padding:5px 10px;
              background-color:#9ad9ae;
              border:1px solid #ccc;
            }

            .label
            {
              font-size:12px;
              font-weight:bold;
            }

            .select
            {
              background-color:#fff;
              border:1px solid #009617;
            }

            .boton
            {
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              border:1px solid #fff;
              padding:3px 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
            }

            .boton:hover
            {
              background:#fff;
              color:#00661b;
              border:1px solid #00661b;
              cursor:pointer;
            }
            
            .StyleDIV
            {
              min-height: 300px; 
            }
    </style>";
  }

  /*! \fn: Formulario
   *  \brief: Formulario con los filtros para realizar la busqueda
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  private function Formulario()
  {
    InsHomoP2::Style();

    $mArrayCiudad = InsHomoP2::getCiudad();
    $mArrayPcPadr = InsHomoP2::getPcPadres();
    $mArrayPcHijo = InsHomoP2::getPcHijo();
    $mArrayTitulo = array('<input type="checkbox" name="marcarTodo">', 'C&oacute;digo', 'Descripci&oacute;n', 'Direcci&oacute;n', 'Tel&eacute;fono', 'Encargado', 'Puesto');

    #Inicio pinta tabla de Puestos de control Hijos sin padres
    $mHtml = '<table width="100%" cellspacing="1" cellpadding="0">';
    $mHtml .= '<tr>';
    foreach ($mArrayTitulo as $value) {
      $mHtml .= '<th class="CellHead">'.$value.'</th>';
    }
    $mHtml .= '</tr>';
    foreach ($mArrayPcHijo as $row) {
      $mHtml .= '<tr>';
      $mHtml .=   '<td class="cellInfo"><input type="checkbox" name="cod_PcHijo[]" value="'.$row[0].'"></td>';
      foreach ($row as $value) {
        $mHtml .= '<td class="cellInfo">'.$value.'&nbsp;</td>';
      }
      $mHtml .= '</tr>';
    }
    $mHtml .= '</table>';
    #Fin pinta tabla de Puestos de control Hijos sin padres

    $formulario = new Formulario ( "index.php", "post", "Homologar PC", "formulario\" id=\"formularioID" );
    
    $formulario -> nueva_tabla();
    $formulario -> linea( "Filtros", 1, "t2" );//Subtitulo.
    $formulario -> nueva_tabla();
    $formulario -> texto ("Palabra Clave", "text", "nom_contro\" id=\"nom_controID", 0, 50, 255, "", $_REQUEST[nom_contro]);
    $formulario -> lista ("Ciudad: ", "cod_ciudad\" id=\"cod_ciudadID", $mArrayCiudad, 1, 0 );

    $formulario -> nueva_tabla();
    $formulario -> linea( "Asignar Puestos de Control", 1, "t2" );//Subtitulo.
    $formulario -> nueva_tabla();
    $formulario -> lista ("Puesto de Control Padre: ", "cod_contro", $mArrayPcPadr, 0, 1 );

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontro un Total de ".sizeof($mArrayPcHijo)." Puesto(s) de Control",0,"t2");

    echo '<script>
            $("#cod_ciudadID").val("'.$_REQUEST[cod_ciudad].'");
          </script>';

    $formulario -> nueva_tabla();
    echo $mHtml;

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion\" id=\"opcionID",1,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> oculto("ind_virtua",$GLOBALS[ind_virtua],0);
    $formulario -> botoni("Guardar","validarForm()",0);
    $formulario -> cerrar();
  }

  /*! \fn: PreFormulario
   *  \brief: Formulario para seleccionar Tipo de PC (Virtual o FIsico)
   *  \author: Ing. Fabian Salinas
   *  \date: 30/07/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  private function PreFormulario()
  {
    echo '<script>
            function verifyFilter()
            {
              try
              {
                var form = $("#formularioID");
                var ind = $("input[type=radio]:checked");

                if( ind.length )
                  form.submit();
                else{
                  alert("Por Favor Seleccione un Tipo de Puesto de Control.");
                  return false;
                }
              }
              catch(e)
              {
                console.log( "Error Fuction verifyFilter: "+e.message+"\nLine: "+e.lineNumber );
                return false;
              }
            }
    
          </script>
         ';

    $formulario = new Formulario ( "index.php", "post", "Homologar PC", "formulario\" id=\"formularioID" );

    $formulario -> nueva_tabla();
    $formulario -> radio ("Puestos Fisicos:","ind_virtua\" id=\"ind_virtuaID","0",0,0);
    $formulario -> radio ("Puestos Virtuales:","ind_virtua\" id=\"ind_virtuaID","1",0,0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion\" id=\"opcionID",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> botoni("Buscar","verifyFilter();",0);
    $formulario -> cerrar();
  }

  /*! \fn: HomPuesto
   *  \brief: Inserta los puestos de control Hijos seleccionados al padre.
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \param: 
   *  \return: Mensaje de confirmación actualización
   */
  private function HomPuesto()
  {
    $user = $_SESSION[datos_usuario][cod_usuari];

    $mSql = " INSERT INTO ".BASE_DATOS.".tab_homolo_pcxeal 
                          (cod_contro, cod_homolo, fec_creaci, usr_creaci ) 
                   VALUES ";

    $mSize = sizeof($_REQUEST[cod_PcHijo]);
    for ($i=0; $i < ($mSize-1); $i++) { 
      $mSql .= " ('".$_REQUEST[cod_contro]."', '".$_REQUEST[cod_PcHijo][$i]."', NOW(), '". $user ."'  ), ";
    }
      $mSql .= " ('".$_REQUEST[cod_contro]."', '".$_REQUEST[cod_PcHijo][($mSize-1)]."', NOW(), '". $user ."' ); ";

    $insert = new Consulta( $mSql, $this -> conexion,"R");

    /*
    *  \brief: Actualiza la direccion del puesto de control Homologado Segun la dirección del puesto de control Padre
    *  \warning: 
    */
    $mDetallePcPadre = InsHomoP2::getDetallePcPadre( $_REQUEST[cod_contro] );
    $mSql = '';
    foreach ($mDetallePcPadre[0] as $key => $value) {
      $mSql .= $mSql == '' ? $key." = '".$value."'" : ", ".$key." = '".$value."'" ;
    }
    $mCodigos = '';
    foreach ($_REQUEST[cod_PcHijo] as $value) {
      $mCodigos .= $mCodigos == '' ? ( $value ) : ( ", ".$value ) ;
    }
    $mSql = "UPDATE ".BASE_DATOS.".tab_genera_contro 
                SET ".$mSql."
              WHERE cod_contro IN ( ".$mCodigos." ) ";
    $insert = new Consulta( $mSql, $this -> conexion,"R");

    if($insert)
      $mensaje = "Puesto de Control #".$_REQUEST[cod_contro]." Homologado </br> Exitosamente.";
    else
      $mensaje = "Problema al Homologar el Puesto de Control #".$_REQUEST[cod_contro].".";

    $mens = new mensajes();
    $mens -> correcto("HOMOLOGAR PUESTOS DE CONTROL",$mensaje);
  }

  /*! \fn: getDetallePcPadre
   *  \brief: Trae la información del Puesto de Control Padre
   *  \author: Ing. Fabian Salinas
   *  \date: 13/05/2015
   *  \date modified: dia/mes/año
   *  \param: cod_contro
   *  \param: 
   *  \return: Array detalles del Puesto de Control
   *  \warning: Según los campos que trae actualiza al  Puesto de Control hijo
   */
  private function getDetallePcPadre( $mCodContro )
  {
    $mSql = "SELECT a.nom_encarg, a.dir_contro, a.tel_contro, 
                    a.val_longit, a.val_latitu, a.val_temper, 
                    a.val_altitu, a.ind_virtua, a.ind_estado, 
                    a.ind_urbano, a.usr_modifi, a.fec_modifi, 
                    a.cod_colorx, a.val_senvia, a.dir_senti1, 
                    a.dir_senti2 
               FROM ".BASE_DATOS.".tab_genera_contro a 
              WHERE a.cod_contro = '$mCodContro' ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matrix('a');
  }

  /*! \fn: getPcHijo
   *  \brief: Trae la informacion de los puestos de control hijos
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: Matriz Informacion
   */
  private function getPcHijo()
  {
    $mSql = "SELECT a.cod_contro, UPPER(a.nom_contro), 
                    a.dir_contro, a.tel_contro, a.nom_encarg, 
                    if(a.ind_virtua = '0','Fisico','Virtual') 
               FROM ".BASE_DATOS.".tab_genera_contro a 
              WHERE a.ind_pcpadr = '0' 
                AND a.ind_estado = '1' 
                AND a.ind_virtua = '{$_REQUEST[ind_virtua]}' 
                AND a.cod_contro NOT IN (SELECT group_concat(a.cod_contro separator ',') AS cod_contro  
                                           FROM ".BASE_DATOS.".tab_genera_contro a 
                                     INNER JOIN ".BASE_DATOS.".tab_homolo_pcxeal b 
                                             ON a.cod_contro = b.cod_homolo 
                                          WHERE a.ind_pcpadr = '0' 
                                       GROUP BY b.cod_homolo
                                       ORDER BY a.cod_contro ) ";
    $mSql .= !$_REQUEST[cod_ciudad] ? "" : " AND a.cod_ciudad = '$_REQUEST[cod_ciudad]' ";
    $mSql .= !$_REQUEST[nom_contro] ? "" : " AND a.nom_contro LIKE '%".$_REQUEST[nom_contro]."%' ";
    $mSql .= " ORDER BY a.nom_contro ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = $mConsult -> ret_matrix('i');
  }

  /*! \fn: getPcPadres
   *  \brief: Trae la lista de los puestos de control padres
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: Matriz (Codigo, nombre)
   */
  private function getPcPadres()
  {
    $mSql = " SELECT a.cod_contro, UPPER( a.nom_contro ) 
                FROM ".BASE_DATOS.".tab_genera_contro a
               WHERE a.nom_contro LIKE '%$_REQUEST[contro]%' 
                 AND a.ind_pcpadr = '1'
                 AND a.ind_virtua = '{$_REQUEST[ind_virtua]}'
                 AND a.ind_estado = '1'
            ORDER BY 2";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = array_merge( array( array( "", "---" ) ), $mConsult -> ret_matrix('i') );
  }

  /*! \fn: getCiudad
   *  \brief: Trae la lista de las ciudades
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: Matriz (Codigo, Nombre)
   */
  private function getCiudad()
  {
    $mSql = " SELECT a.cod_ciudad, UPPER( b.nom_ciudad )
                FROM ".BASE_DATOS.".tab_genera_contro a
          INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b 
                  ON a.cod_ciudad = b.cod_ciudad
               WHERE a.ind_estado = '1' 
                 AND a.ind_virtua = '0' 
            GROUP BY 1
            ORDER BY 2";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = array_merge( array( array( "", "---" ) ), $mConsult -> ret_matrix('i') );
  }


}//FIN CLASE InsHomoP2


$proceso = new InsHomoP2($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
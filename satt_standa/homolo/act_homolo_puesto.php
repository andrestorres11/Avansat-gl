<?php
/*! \file: act_homolo_puesto.php
 *  \brief: Actulizar puestos homologados
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

/*! \class: ActHomoloPc
 *  \brief: Actualiza la relacion de puestos de control hijos/Padres, (Quita relaciones) 
 */
class ActHomoloPc
{
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    ActHomoloPc::principal();
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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/act_homolo_puesto.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    switch($_REQUEST[opcion])
    {
      case "1":
        ActHomoloPc::ActPuesto();
        break;

      case "2":
        ActHomoloPc::Formulario();
        break;

      default:
        ActHomoloPc::PreFormulario();
        break;
    }
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

    $formulario = new Formulario ( "index.php", "post", "Desvincular Homologaci&oacute;n PC", "formulario\" id=\"formularioID" );

    $formulario -> nueva_tabla();
    $formulario -> radio ("Puestos Fisicos:","ind_virtua\" id=\"ind_virtuaID","0",0,0);
    $formulario -> radio ("Puestos Virtuales:","ind_virtua\" id=\"ind_virtuaID","1",0,0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion\" id=\"opcionID",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> botoni("Buscar","verifyFilter();",0);
    $formulario -> cerrar();
  }

  /*! \fn: Formulario
   *  \brief: Formulario con los filtros para realizar la busqueda
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  private function Formulario()
  { 

    $mArrayPcPadr = ActHomoloPc::getPcPadres();

    if($_REQUEST[cod_contro] != NULL)
    {
      $mArrayPcHijo = ActHomoloPc::getPcHijos();
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

    }else{ $mHtml = NULL; }

    $formulario = new Formulario ( "index.php", "post", "Desvincular Homologación PC", "formulario\" id=\"formularioID" );
    
    $formulario -> nueva_tabla();
    $formulario -> linea( "Filtros", 1, "t2" );//Subtitulo.
    
    $formulario -> nueva_tabla();
    $formulario -> lista ("Puesto de Control Padre: ", "cod_contro\" id=\"cod_controID", $mArrayPcPadr, 1, 1 );

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontro un Total de ".sizeof($mArrayPcHijo)." Puesto(s) de Control",0,"t2");

    echo '<script>
            $("#cod_controID").val("'.$_REQUEST[cod_contro].'");
          </script>';

    $formulario -> nueva_tabla();
    echo $mHtml;

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion\" id=\"opcionID",1,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> oculto("ind_virtua",$_REQUEST[ind_virtua],0);
    $formulario -> botoni("Guardar","VerifyData()",0);
    $formulario -> cerrar();
  }

  /*! \fn: getPcPadres
   *  \brief: Trae los puestos de control padres
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: matriz (Codigo, Nombre)
   */
  private function getPcPadres()
  {
    $mSql = " SELECT a.cod_contro, UPPER( a.nom_contro ) 
                FROM ".BASE_DATOS.".tab_genera_contro a
               WHERE a.nom_contro LIKE '%$_REQUEST[contro]%' 
                 AND a.ind_pcpadr = '1' 
                 AND a.ind_virtua = '{$_REQUEST[ind_virtua]}' 
                 AND a.ind_estado = '1'
            ORDER BY 2 ";
    $mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult = array_merge( array( array( "", "---" ) ), $mConsult -> ret_matrix('i') );
  }

  /*! \fn: getPcHijos
   *  \brief: Trae los puestos de control hijos
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: matriz informacion puestos de control
   */
  private function getPcHijos()
  {
    $mSql = "SELECT a.cod_contro, a.nom_contro, 
                    a.dir_contro, a.tel_contro, a.nom_encarg, 
                    if(a.ind_virtua = '0','Fisico','Virtual') 
               FROM ".BASE_DATOS.".tab_genera_contro a 
         INNER JOIN ".BASE_DATOS.".tab_homolo_pcxeal b 
                 ON a.cod_contro = b.cod_homolo 
              WHERE b.cod_contro = '".$_REQUEST[cod_contro]."' 
                AND a.ind_estado = '1' ";
    $mConsult = new Consulta($mSql, $this -> conexion);
    return $mCantidad = $mConsult -> ret_matrix('i');
  }

  /*! \fn: ActPuesto
   *  \brief: Elimina la relacion puesto de control hijo/padre
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  private function ActPuesto()
  {
    foreach ($_REQUEST[cod_PcHijo] as $value) {
      $mSql = "DELETE FROM ".BASE_DATOS.".tab_homolo_pcxeal 
               WHERE cod_contro = '$_REQUEST[cod_contro]' 
                 AND cod_homolo = '$value' ";
      $delete = new Consulta( $mSql, $this -> conexion,"R");

      if($delete)
        $mensaje = "Puesto de Control #".$_REQUEST[cod_contro]." con SubPuesto de Control #".$value." </br> Actualizado Exitosamente.";
      else
        $mensaje = "Problema al actualizar el Puesto de Control #".$_REQUEST[cod_contro]." con SubPuesto de Control #".$value.".";

      $mens = new mensajes();
      $mens -> correcto("ACTUALIZAR PUESTOS DE CONTROL",$mensaje);
    }

  }

}//FIN CLASE ActHomoloPc


$proceso = new ActHomoloPc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
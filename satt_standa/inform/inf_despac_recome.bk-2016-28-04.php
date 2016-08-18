<?php
/*! \file: inf_despac_recome.php
 *  \brief: Informe Despachos Recomendados
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 21/04/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');
ini_set('memory_limit', '4096M');

/*! \class: InfDespacRecome
 *  \brief: Informe despachos recomendados
 */
class InfDespacRecome
{
  var $conexion,
      $cod_aplica,
      $usuario;
  var $cNull = array( array('', '-----') ); 

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  /*! \fn: principal
   *  \brief: 
   *  \author: Ing. Fabian Salinas
   *  \date: 21/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function principal()
  {
    switch($GLOBALS[opcion])
    {
      case 1:
        InfDespacRecome::getInform();
      break;

      case 2:
        InfDespacRecome::exportExcel();
      break;

      default:
        InfDespacRecome::formulario();
      break;
    }
  }
  
  /*! \fn: Style
   *  \brief: Estilos para las tablas
   *  \author: Ing. Fabian Salinas
   *  \date: 21/04/2015
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
    </style>";
  }


  /*! \fn: getInform
   *  \brief: Muestra los resultados de la consulta
   *  \author: Ing. Fabian Salinas
   *  \date: 21/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \param: 
   *  \return:
   */
  function getInform()
  {
    InfDespacRecome::Style();

    $mArrayData = NULL;
    $mCodContro = '';

    if($_REQUEST['cod_contro'] != NULL )
    {
      $mSelect = "SELECT a.cod_homolo 
                    FROM tab_homolo_pcxeal a 
                   WHERE a.cod_contro = '$_REQUEST[cod_contro]' 
                GROUP BY a.cod_homolo 
                ORDER BY a.cod_homolo DESC 
                 ";
      $mConsult = new Consulta( $mSelect, $this -> conexion );
      $mArrayCont = $mConsult -> ret_matrix('i');

      foreach ($mArrayCont as $row) {
        $mCodContro .= ', '.$row[0];
      }
    }

    /*
    *  \brief: Info del Despacho
    *  \warning: 
    */
    $mSelect = "SELECT a.num_despac, a.cod_manifi, z.num_despac, 
                       a.fec_despac, c.nom_tipdes, d.nom_ciudad, 
                       e.nom_ciudad 
                  FROM ".BASE_DATOS.".tab_despac_despac a 
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
                    ON a.num_despac = b.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_genera_tipdes c 
                    ON a.cod_tipdes = c.cod_tipdes  
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
                    ON a.cod_paiori = d.cod_paisxx 
                   AND a.cod_depori = d.cod_depart 
                   AND a.cod_ciuori = d.cod_ciudad 
            INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
                    ON a.cod_paides = e.cod_paisxx 
                   AND a.cod_depdes = e.cod_depart 
                   AND a.cod_ciudes = e.cod_ciudad 
            INNER JOIN ".BASE_DATOS.".tab_recome_asigna f 
                    ON a.num_despac = f.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_recome_asigna g 
                    ON a.num_despac = g.num_despac 
             LEFT JOIN ".BASE_DATOS.".tab_despac_corona z 
                    ON a.num_despac = z.num_dessat 
                 WHERE b.cod_transp = '$_REQUEST[cod_transp]' 
                   AND a.ind_anulad = 'R' 
            ";

    $mSelect .= $_REQUEST['num_dessat'] != NULL ? " AND  a.num_despac = '".$_REQUEST['num_dessat']."'" : '';
    $mSelect .= $_REQUEST['num_manifi'] != NULL ? " AND  a.cod_manifi = '".$_REQUEST['num_manifi']."'" : '';
    $mSelect .= ( $_REQUEST['fec_inicia'] != NULL && $_REQUEST['fec_finalx'] != NULL ) ? " AND a.fec_salsis BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finalx']." 23:59:59' " : "" ;
    $mSelect .= $_REQUEST['cod_contro'] != NULL ? " AND  f.cod_contro IN (".$_REQUEST['cod_contro'].$mCodContro.")" : '';

    $mSelect .= " 
                  GROUP BY a.num_despac 
                  ORDER BY a.fec_salsis DESC "; 

    $mConsult = new Consulta( $mSelect, $this -> conexion );
    $mArrayData = $mConsult -> ret_matrix('i');

    /*
    *  \brief: Array Cabecera tabla informe
    *  \warning: 
    */
    $mArrayTitu = array("#", "No. Despacho SATT", "Manifiesto", "Viaje",
                        "Fecha Despacho", "Tipo Despacho", "Ciudad Origen", 
                        "Ciudad Destino", "Recomendaci&oacuten", "Puesto de Control", 
                        "Solicito", "Fecha Solicitud", "Ejecuto", 
                        "Fecha Ejecuci&oacuten", "Observaci&oacuten Ejecuci&oacuten" );

    /*
    *  \brief: Pinta Tabla Resultado de la consulta
    *  \warning: 
    */
    $mHtml  = "<table border='1'>";

    $mHtml .= "<tr>";
    foreach ($mArrayTitu as $titu) {
      $mHtml .= "<th class=cellHead >".$titu."</th>";
    }
    $mHtml .= "</tr>";

    $mFila = 1;
    foreach ($mArrayData as $row) {
      $mLink1 = '<a href="?cod_servic=3302&window=central&despac='.$row[0].'&opcion=1">';
      $mArrayReco =  InfDespacRecome::getDespacRecome($row[0]);
      $mSize = sizeof($mArrayReco);

      $mHtml .= "<tr>";
      $mHtml .= "<td class='cellInfo' rowspan='".$mSize."'>".$mFila."</td>";
      $j=0;
      foreach ($row as $value) {
        $mHtml .= "<td class='cellInfo' rowspan='".$mSize."'>".( $j==0 ? $mLink1 : '' ).$value.( $j==0 ? '</a>' : '' )."</td>";
        $j++;
      }

      for($i=0; $i<sizeof($mArrayReco); $i++)
      {
        $mHtml .= $i == 0 ? '' : '<tr>' ;
        foreach ($mArrayReco[$i] as $value) {
          $mHtml .= "<td class='cellInfo'>".$value."</td>";   
        }
        $mHtml .= $i == 0 ? '' : '</tr>' ;
      }

      $mHtml .= "</tr>";

      $mFila++;
    }

    $mHtml .= "<table>";

    echo $mHtml;
  }
  

  /*! \fn: formulario
   *  \brief: Formulario de los filtros para realizar busqueda
   *  \author: Ing. Fabian Salinas
   *  \date: 21/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \param: 
   *  \return: 
   */
  function formulario()
  {
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    echo '
      <script>

        function verifiData()
        {
          try 
          {
            var cod_transp = $("#cod_transpID");
            var cod_contro = $("#cod_controID");
            var num_dessat = $("#num_dessatID");
            var num_manifi = $("#num_manifiID");
            var fec_inicia = $("#fec_iniciaID");
            var fec_finalx = $("#fec_finalxID");
            var form = $("#formID");

            if ( !cod_transp.val() ){
              alert("Por Favor Selecione una Transportadora.");
            }else if( (!fec_inicia.val() && fec_finalx.val() != "") || (fec_inicia.val() != "" && !fec_finalx.val() ) ){
              alert("Ha Seleccionado un Parametro de busqueda Tipo Fecha \nPor Favor Seleccione el Otro Parametro de Fecha para Realizar la Busqueda.");
            }else if( fec_inicia.val() != "" && fec_finalx.val() != "" && fec_inicia.val() > fec_finalx.val() ){
              alert("La Fecha Inicial es Mayor a la Fecha Final.\nPor Favor Corregir Seleccion.");
            }else if( !num_dessat.val() && !num_manifi.val() && !fec_inicia.val()&& !fec_finalx.val() ){
              alert("Por Favor Selecione Otro Parametro de Busqueda.");
            }else{
              form.submit();
            }
          }
          catch(e)
          {
            console.log( "Error Función verifiData: "+e.message+"\nLine: "+Error.lineNumber );
            return false;
          }
        }

        jQuery(function($) 
        {
          $( "#fec_iniciaID, #fec_finalxID" ).datepicker({
            changeMonth: true,
            changeYear: true
          });

          $.mask.definitions["A"]="[12]";
          $.mask.definitions["M"]="[01]";
          $.mask.definitions["D"]="[0123]";

          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";

          $( "#fec_iniciaID, #fec_finalxID" ).mask("Annn-Mn-Dn");
        });

      </script>';

    $mSelect = "SELECT a.cod_tercer, b.abr_tercer 
                  FROM ".BASE_DATOS.".tab_tercer_activi a 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b 
                    ON a.cod_tercer = b.cod_tercer 
                 WHERE a.cod_activi = '1' 
      ";
      if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) 
      {#PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
        if ( $filtro -> listar( $this -> conexion ) ) : 
          $datos_filtro = $filtro -> retornar();
          $mSelect .= " AND b.cod_tercer = '".$datos_filtro['clv_filtro']."' ";
        endif;
      }else{#PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
        if ( $filtro -> listar( $this -> conexion ) ) : 
          $datos_filtro = $filtro -> retornar();
          $mSelect .= " AND b.cod_tercer = '".$datos_filtro['clv_filtro']."' ";
        endif;
      }
    $mSelect .= " ORDER BY b.abr_tercer ASC ";

    $mConsult = new Consulta( $mSelect, $this -> conexion );
    $mArrayTransp = $mConsult -> ret_matrix('i');


    $mSelect = "SELECT a.cod_contro, a.nom_contro 
                  FROM ".BASE_DATOS.".tab_genera_contro a 
                 WHERE a.ind_pcpadr = '1' 
                   AND a.ind_estado = '1' 
                   AND a.ind_virtua = '0' 
               ";
    $mConsult = new Consulta( $mSelect, $this -> conexion );
    $mArrayPcPadr = $mConsult -> ret_matrix('i');

    /*
    *  \brief: Formulario
    *  \warning: 
    */
    $formulario = new Formulario ("index.php","post","Informe Despachos Recomendados","form\" id=\"formID");

    $formulario -> nueva_tabla();
    $formulario -> lista ("Transportadora:","cod_transp\" id=\"cod_transpID",array_merge($this -> cNull, $mArrayTransp ),0 );
    $formulario -> lista ("Puestos de Control:","cod_contro\" id=\"cod_controID",array_merge($this -> cNull, $mArrayPcPadr ),1 );

    $formulario -> nueva_tabla();
    $formulario -> texto( "Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "" );
    $formulario -> texto( "Fecha Final:", "text", "fec_finalx\" readonly id=\"fec_finalxID", 1, 10, 10, "" );

    $formulario -> texto( "No. Despacho( SATT ):", "text", "num_dessat\" id=\"num_dessatID", 0, 15, 15, "" );
    $formulario -> texto( "No. Manifiesto:", "text", "num_manifi\" id=\"num_manifiID", 1, 15, 15, "" );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","verifiData();",0);
    
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",1,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> cerrar();
  }

  /*! \fn: getDespacRecome
   *  \brief: Consulta las Recomendaciones por despacho
   *  \author: Ing. Fabian Salinas
   *  \date: 23/04/2015
   *  \date modified: dia/mes/año
   *  \param: num_despac
   *  \param: 
   *  \return: Array con las recomendaciones del despacho
   */
  private function getDespacRecome($numDespac)
  {
    $mSelect = "SELECT b.tex_encabe, c.nom_contro, d.nom_usuari, 
                       a.fec_solici, e.nom_usuari, a.fec_ejecut, 
                       a.obs_ejecuc 
                  FROM ".BASE_DATOS.".tab_recome_asigna a 
            INNER JOIN ".BASE_DATOS.".tab_genera_recome b 
                    ON a.cod_recome = b.cod_consec 
            INNER JOIN ".BASE_DATOS.".tab_genera_contro c 
                    ON a.cod_contro = c.cod_contro 
            INNER JOIN ".BASE_DATOS.".tab_genera_usuari d 
                    ON a.usr_solici = d.cod_usuari 
             LEFT JOIN ".BASE_DATOS.".tab_genera_usuari e 
                    ON a.usr_ejecut = e.cod_usuari 
                 WHERE a.num_despac = '$numDespac' 
              ORDER BY a.num_condes ";
    $mConsult = new Consulta( $mSelect, $this -> conexion );
    return $mResult = $mConsult -> ret_matrix('i');
  }


  /*! \fn: exportExcel
   *  \brief: Exporta a Excel
   *  \author: Ing. Fabian Salinas
   *  \date: 21/04/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: 
   */
  private function exportExcel()
  {
    session_start();
    $archivo = "informe_CallCenter".date( "Y_m_d_H_i" ).".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    ob_clean();
    echo $HTML = $_SESSION[xls_InfDespacRecome];
  }
  
}

$_INFORM = new InfDespacRecome( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
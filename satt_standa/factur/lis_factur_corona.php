<?php 
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);


/*! \class: FacturCorona
*  \brief: Clase para mostrar facturacion detallada
*/

class FacturCorona
{
    # Variables privadas de la clase
    private static $cConection;
    private static $cCodUsuari;
    private static $cCodAplica;

    /*! \fn: __construct
    *  \brief: Constructora de la clase, define que metodo cargar
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015     
    *  \param: mConection: variable de entrada de la cConection del framework
    *  \param: mDatUsuari: Variable con los datos del usuario logueado
    *  \param: mCodAplica: Variable con el codigo de la aplicacion
    *  \return valor que retorna
    */
    function __construct($mConection = NULL, $mDatUsuari = NULL, $mCodAplica = NULL) 
    {
         
        self::$cConection = $mConection;
        self::$cCodUsuari = $mDatUsuari;
        self::$cCodAplica = $mCodAplica;
        # Switch para la carga de un metodo
        switch($_POST["opcion"]) {
            case "1": FacturCorona::getDataTotales(); break;
            case "2": FacturCorona::listar(); break;
            case "3": FacturCorona::facturar(); break;
            case "4": FacturCorona::exportExcel(); break;
            default:  FacturCorona::filtro();  break;
        }
    }

    /*! \fn: getTransp
    *  \brief: Constructora de la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015     
    *  \param: mConection: variable de entrada de la cConection del framework
    *  \param: mDatUsuari: Variable con los datos del usuario logueado
    *  \param: mCodAplica: Variable con el codigo de la aplicacion
    *  \return valor que retorna
    */
    private function getTransp( $cod_transp )
    {
      $mSql = "SELECT cod_tercer, UPPER(abr_tercer) AS nom_tercer FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$cod_transp."' LIMIT 1";
      $consulta = new Consulta( $mSql, self::$cConection );
      return $consulta -> ret_matriz();
    }
    
    /*! \fn: VerifyTranspor
    *  \brief: Verfica la transportadora del usuario que está logueado
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015      
    *  \return array
    */
    private function VerifyTranspor()
    {
      if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) {
        //--------------------------
        //@PARA EL FILTRO DE EMPRESA
        //--------------------------
        $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
        if ( $filtro -> listar( self::$cConection ) ) : 
        $datos_filtro = $filtro -> retornar();
        endif;
      }
      else { 
        //--------------------------
        //@PARA EL FILTRO DE EMPRESA
        //--------------------------
        $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
        if ( $filtro -> listar( self::$cConection ) ) : 
        $datos_filtro = $filtro -> retornar();
        endif;
      }
      return $datos_filtro;
    }

    /*! \fn: Javascripts
    *  \brief: Incluye los javascripts a usar, y metodos de js a usar
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015      
    *  \return valor que retorna
    */
    private function Javascripts()
    { 
        echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
       
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
         echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/facfaroCorona.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        
    }

    /*! \fn: filtroForm
    *  \brief: cabecera de los filtros primer pantallazo al cargar el modulo
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015      
    *  \return valor que retorna
    */
    private function filtroForm()
    {
      self::Javascripts();
      try 
      {
          $empre = array();
          $inicio[0][0]= "";
          $inicio[0][1]= "-";
          
          $add = '';
          $filter = self::VerifyTranspor();
          if( sizeof( $filter ) > 0 )
          { 
            $TRANSP = self::getTransp( $filter['clv_filtro'] );
            $add = " AND a.cod_tercer = '".$TRANSP[0][0]."' ";
          }


          $query = "SELECT a.cod_tercer,a.abr_tercer
                      FROM ".BASE_DATOS.".tab_tercer_tercer a,
                           ".BASE_DATOS.".tab_tercer_activi b
                     WHERE a.cod_tercer = b.cod_tercer 
                       AND b.cod_activi = '1' ".$add."
                  ORDER BY 2 ";
          $consulta = new Consulta($query, self::$cConection);
          $transp = $consulta -> ret_matriz(); 
          $transpor = array_merge($inicio,$transp);
          $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Facturacion Faro Detalle", "formulario", NULL, NULL, NULL, NULL, 1);
          
          
          $feactual = date("Y-m-d");
          $formulario -> nueva_tabla();

          echo "<tr><td align='right'  class='celda_titulo'>";
          echo "Fecha Inicial de Salida: &nbsp;";
          echo "</td>";
          echo '<td class="celda_info">';
          echo "<input type='text' class='campo' size='10' id='feciniID' name='fecini' value='".$_REQUEST["fecini"]."'> ";
          echo "</td>";
          echo "<td align='right' class='celda_titulo'>";
          echo "Fecha Final de Salida: &nbsp;";
          echo "</td>";
          echo '<td class="celda_info">';
          echo "<input type='text' class='campo' size='10' id='fecfinID' name='fecfin' value='".$_REQUEST["fecfin"]."'> ";
          echo "</td></tr>";

          echo "<tr><td align='right' class='celda_titulo'>";
          echo "Nit. Transportadora: &nbsp; (Separar por ',')";
          echo "</td>";
          echo '<td class="celda_info">';
          echo "<input type='text' class='campo' size='24' id='nit_transportID' name='nit_transport' > ";
          echo "</td>";

          $formulario -> lista("Transportadora:","transp\" onchange=\"SetAgencia( $(this).val() )\" id=\"transpID",$transpor,1);  
          $formulario -> lista("Agencia:","cod_agenci\" id=\"cod_agenciID",$inicio,1);  

          $formulario -> nueva_tabla();
          
          echo "<input type='button' class='crmButton small save' value='Aceptar' name='Aceptar' onclick=\"aceptar_lis();\" class='bot'>";
          $formulario -> nueva_tabla();

          $formulario -> oculto("num_despac",0,0);
          $formulario -> oculto("window","central",0);
          $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
          $formulario -> oculto("opcion\" id=\"opcion",1,0);
          $formulario -> oculto("Standa\" id=\"StandaID", DIR_APLICA_CENTRAL, 0);

          #echo "<pre>"; print_r($_REQUEST); echo "</pre>";

          echo '<script>
              document.getElementById("transpID").value='.$_REQUEST["transp"].';               
            </script>';
          if($_REQUEST["cod_agenci"])
          {
            echo '<script>
                  SetAgencia ('.$_REQUEST["transp"].', '.$_REQUEST["cod_agenci"].' );
                </script>';
          }



          $formulario -> cerrar();

      } 
      catch (Exception $e) 
      {
        echo "Error de Catch Fn: filtroForm: ".$e -> getMessage();;
      } 
    }

    /*! \fn: filtro
    *  \brief: Funcion para mostrar el formulario de los filtros, ya que se pueden mostrar los filtros en otro metodo, es recursivo
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015      
    *  \return valor que retorna
    */
    private function filtro()
    {    
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Facturacion Faro Detalle", "formulario");
          self::filtroForm();
        $formulario -> cerrar();
    }
    
    /*! \fn: getDataTotales
    *  \brief: Ffunción que muestra el detalle de las novedades de los despachos
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015      
    *  \return valor que retorna
    */
    private function getDataTotales()
    { 
      try 
      {
        $mForm = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Facturacion Faro Detalle", "formulario");
          # Se muestran los filtros
          self::filtroForm();

          # ---------------------------------------------------------------------------------------------------------------------------------
          $mListTipdes = self::getTiposDespachos(); 
          # ---------------------------------------------------------------------------------------------------------------------------------
          $mFiltros = '&fec_ini='.$_REQUEST["fecini"].'&fec_fin='.$_REQUEST["fecfin"].'&transp='.$_REQUEST["transp"].'&cod_agenci='.$_REQUEST["cod_agenci"];

          $mForm -> nueva_tabla(); 
          $mHtml = '<tr>';
            $mHtml .= '<td align="left"  >';

            $mHtml .= '<div id="tabsID">';
              # Coloca las pestanas que se van a mostrar, una pestana de vista general y las otras segun la consulta
              $mHtml .= '<ul> ';
              
              /* Dejar asi comentariado por favor
              $mHtml .= '<li><a href="../'.DIR_APLICA_CENTRAL.'/factur/ajax_factur_corona.php?cod_tipdes=1000&option=DataGeneral'.$mFiltros.' " id="tipdes-1000">GENERAL</a></li>';
              foreach ($mListTipdes AS $mCodTipdes => $mNomTipdes) {
                 $mHtml .= '<li><a href="../'.DIR_APLICA_CENTRAL.'/factur/ajax_factur_corona.php?cod_tipdes='.$mNomTipdes["cod_tipdes"].'&option=DataTipdes'.$mFiltros.' " id="tipdes-'.$mNomTipdes["cod_tipdes"].'">'.$mNomTipdes["nom_tipdes"].'</a></li>';
              } */      


              $mHtml .= '<li><a href="#general" onclick="LoadTab(1000, \'DataGeneral\')"  style="cursor: pointer;" >GENERAL</a></li>';
              foreach ($mListTipdes AS $mCodTipdes => $mNomTipdes) {
                 $mHtml .= '<li><a href="#general" onclick="LoadTab('.$mNomTipdes["cod_tipdes"].', \'DataTipdes\')"  style="cursor: pointer;" >'.$mNomTipdes["nom_tipdes"].'</a></li>';
              }   
              $mHtml .= '</ul>';          
              $mHtml .= '<div id="general">';
                
              $mHtml .= '</div>';

            $mHtml .= '</div>';

            $mHtml .= '</td>';
          $mHtml .= '</tr>';

          $mHtml .= '<script>
                      $("document").ready(function(){
                         LoadTab("1000","DataGeneral");
                      });
                     </script>';

          echo $mHtml;

        $mForm -> cerrar();


      } 
      catch (Exception $e) 
      {
        echo "Genera error de catch Fn: getDataTotales: ".$e ->getMessage();
      }

    }


    /*! \fn: getTiposDespachos
    *  \brief: lista los tipos de despachos existentes en la BD
    *  \author: Ing. Nelson Liberato
    *  \date: 24/06/2015      
    *  \return array
    */
    private function getTiposDespachos( $mCodTipdes = NULL)
    {
      try 
      {
          $mListTipdes = "SELECT a.cod_tipdes, UPPER(a.nom_tipdes) AS nom_tipdes
                      FROM ".BASE_DATOS.".tab_genera_tipdes  a 
                     WHERE 1=1 ";
          $consulta = new Consulta($mListTipdes, self::$cConection);
          $mListTipdes = $consulta -> ret_matrix("a"); 
          return $mListTipdes;
      } 
      catch (Exception $e) 
      {
        return array("cod_respon" => "3001", "msg_respon" => $e -> getMessage() );
      }
    }
}
//$service = new FacturCorona($this->cConection);
$service = new FacturCorona($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>

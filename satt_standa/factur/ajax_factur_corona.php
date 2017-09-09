<?php


session_start();



/*! \class: AjaxFacturFaro
*  \brief: Clase encargada de las peticiones de ajax
*/
class AjaxFacturCorona
{
  private static $cConection;
  private static $cDataGener;
  private static $cPerfiles = "'7','8','73','74','84','1','713'";
  private static $cNotPerfi = "7";
  private static $cNitCorona = "860068121";
  private static $mArrayEtapas = array (
                              '1'   => array('tit_pestan' => 'Cita De Cargue','nom_pestan' => 'Cita_De_Cargue','cam_etapax' => 'cit_cargue') , 
                              '2'   => array('tit_pestan' => 'Cargue','nom_pestan' => 'Cargue','cam_etapax' => 'car_cargue'),
                              '0,3' => array('tit_pestan' => 'Transito','nom_pestan' => 'Transito','cam_etapax' => 'tra_transi') , 
                              '4'   => array('tit_pestan' => 'Descargue','nom_pestan' => 'Cita_Descargue','cam_etapax' => 'cit_descar'),
                              '5'   => array('tit_pestan' => 'Novedades En Entrega','nom_pestan' => 'Novedades_En_Entrega','cam_etapax' => 'nov_entreg'), 
                              '6'   => array('tit_pestan' => 'Generales Moviles','nom_pestan' => 'Novedades_Generales','cam_etapax' => 'nov_genera') 
                            );
 
  
  /*! \fn: __construct
  *  \brief: Constructora de la clase, define que metodo cargar
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015       
  *  \return n/a
  */

  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );
    echo '<link type="text/css" href="../'.DIR_APLICA_CENTRAL.'/estilos/informes.css" rel="stylesheet">';
    self::$cConection = $AjaxConnection;
    self::$_AJAX['option']( $_AJAX );
  }
  
  /*! \fn: SetAgencia
  *  \brief: consulta las agencias de una transportadora
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function SetAgencia( $_AJAX )
  {
    $mSelect = "SELECT a.cod_agenci, b.nom_agenci
                  FROM ".BASE_DATOS.".tab_transp_agenci a, 
                       ".BASE_DATOS.".tab_genera_agenci b
                 WHERE a.cod_agenci = b.cod_agenci
                   AND a.cod_transp = '".$_AJAX['cod_empres']."'
                 ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, self::$cConection );
    $agencias = $consulta -> ret_matriz();
  
    $result = '<option value="">- Seleccione -</option>';
    foreach( $agencias AS $row ) {
      echo $mSelect = $_AJAX["cod_agenci"] == $row["cod_agenci"] ? 'selected' : '';
      $result .= '<option value="'.$row['cod_agenci'].'" '.$mSelect.' >'.$row['nom_agenci'].'</option>';
    }
    echo $result;
  }

  
  /*! \fn: DataGeneral
  *  \brief: datos de la pestana general
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function DataGeneral($_AJAX)
  {
    try 
    {
       $mDataGrillaGeneral = self::LoadData($_AJAX);
      # Datos Tabla Generals-------------------------------
      $mVistaGeneral = self::DataTablaVistaGeneral($_AJAX);
      $mGriddGeneral = self::DataTablaGriddGeneral($_AJAX);
  

      $mHtml  = '<div id="div_contenedor" class="StyleDIV">';
      $mHtml .= $mVistaGeneral;
      $mHtml .= "<br><br>";
      $mHtml .= $mGriddGeneral;
      $mHtml .= '</div>';
      echo $mHtml;
    } 
    catch (Exception $e) 
    { 
        echo "error Fn DataGeneral: ".$e -> getMessage();
    }
  }

  /*! \fn: LoadData
  *  \brief: Carga la matriz de datos general
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return array
  */
  private function LoadData($_AJAX = NULL)
  {
    try 
    { 
        $mTotalNoved = 0;
        $mTotalDespa = 0;
        $mValor = self::getTipSerTransp( $_AJAX["transp"] );

        foreach (self::getTiposDespachos() AS $mConsec => $mDatCampo):                

          $mData = self::getDespacByTipdes( $mDatCampo["cod_tipdes"], $_AJAX );  
          $mDespac = @join( ', ', GetColumnFromMatrix( $mData, 'num_despac' ) );
          $mTotalNoved += sizeof( self::getNovedades($mDespac) );
          $mTotalNoApp += sizeof( self::getNovedades($mDespac, NULL, NULL, NULL, NULL, 1) );
          $mTotalNoWeb += sizeof( self::getNovedades($mDespac, NULL, NULL, NULL, NULL, 2) );
          $mTotalDespa += sizeof( $mData );

          # Carga las etapas
          foreach (self::$mArrayEtapas AS $mKey => $mNomEtapax):
            if($mKey == '6')
            {
              self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]][$mNomEtapax["cam_etapax"]] += sizeof( self::getNovedades($mDespac, false, $mKey, NULL, NULL, 1 ) );
              self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]][$mNomEtapax["cam_etapax"]."w"] += 0;
            }
            else
            {
              self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]][$mNomEtapax["cam_etapax"]] += sizeof( self::getNovedades($mDespac, false, $mKey ) );
              self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]][$mNomEtapax["cam_etapax"]."w"] += sizeof( self::getNovedades($mDespac, false, $mKey, NULL, NULL, 2 ) );
            }
            self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]][$mNomEtapax["cam_etapax"]."a"] += sizeof( self::getNovedades($mDespac, false, $mKey, NULL, NULL, 1 ) );
          endforeach;

          self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]]["nom_tipdes"] = $mDatCampo["nom_tipdes"];
          self::$cDataGener["tip_despac"][$mDatCampo["cod_tipdes"]]["num_despac"] = sizeof( $mData );
 
        endforeach;

        self::$cDataGener["tot_despac"]  = $mTotalDespa; # Total de novedades despachos
        self::$cDataGener["tot_novreg"]  = $mTotalNoved; # Total de novedades registradas
        self::$cDataGener["tot_novweb"]  = $mTotalNoWeb; # Total de novedades registradas
        self::$cDataGener["tot_novapp"]  = $mTotalNoApp; # Total de novedades registradas
        self::$cDataGener["val_totalx"]  = ($mTotalNoved * $mValor["val_regist"] ); # Total de novedades registradas
        self::$cDataGener["val_xnoved"]  = $mValor["val_regist"]; # valor de la novedad que se le cobra a la empresa


        # Sumatoria de las etapas para la vista general
        $mValToTcargue = 0;
        $mValToTtransi = 0;
        $mValToTdescar = 0;
        $mValToTentreg = 0;
        foreach (self::$cDataGener["tip_despac"] AS $mKey => $mNomEtapax):  
          $mValToTcitcar +=  $mNomEtapax["cit_cargue"];
          $mValToTcargue +=  $mNomEtapax["car_cargue"];
          $mValToTtransi +=  $mNomEtapax["tra_transi"];
          $mValToTdescar +=  $mNomEtapax["cit_descar"];
          $mValToTentreg +=  $mNomEtapax["nov_entreg"];
          $mValToTgenera +=  $mNomEtapax["nov_genera"];
        endforeach;

        self::$cDataGener["tot_citcar"]  = $mValToTcitcar; 
        self::$cDataGener["tot_cargue"]  = $mValToTcargue; 
        self::$cDataGener["tot_transi"]  = $mValToTtransi; 
        self::$cDataGener["tot_descar"]  = $mValToTdescar; 
        self::$cDataGener["tot_novent"]  = $mValToTentreg; 
        self::$cDataGener["tot_genera"]  = $mValToTgenera; 
        return self::$cDataGener;
  
    } 
    catch (Exception $e) 
    { 
        echo "error Fn LoadData: ".$e -> getMessage();
    }
  }
  
  /*! \fn: DataTablaVistaGeneral
  *  \brief: datos totales de la pestana general
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function DataTablaVistaGeneral($_AJAX)
  {
    try 
    {
        $mHtml  = '<table width="80%" align="center">';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" colspan="16" align="center">Tabla totales Vista General</td>';            
          $mHtml .= '</tr>';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2">Despachos Generados</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Nº de Novedades Registrados</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2">Valor A Facturar</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Cita De Cargue</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Cargue</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Transito</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Descargue</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Novedades En Entrega</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Generales Moviles</td>';
          $mHtml .= '</tr>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>'; 
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>'; 
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>';            
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>'; 
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>'; 
            $mHtml .= '<td class="CellHead" align="center" colspan="1">WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" colspan="1">APP</td>'; 
          $mHtml .= '<tr>';           
          $mHtml .= '</tr>';           
          $mHtml .= '<tr>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2"><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'\', \'\', \'despac\')" ><b>'.number_format( self::$cDataGener["tot_despac"], 0, ',', '.').'</b></spam></td>';  #Despachos Generados
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_novreg"], 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2">$ '.number_format( self::$cDataGener["val_totalx"], 0, ',', '.' ).'</td>';  #Valor A Facturar
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_citcar"], 0, ',', '.').'</td>';  #Cita De Cargue
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_cargue"], 0, ',', '.').'</td>';  #Cargue
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_transi"], 0, ',', '.').'</td>';  #Transito
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_descar"], 0, ',', '.').'</td>';  #Descargue
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_novent"], 0, ',', '.').'</td>';  #Novedades En Entrega
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2">'.number_format( self::$cDataGener["tot_genera"], 0, ',', '.').'</td>';  #Novedades En Entrega
          $mHtml .= '</tr>';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::$cDataGener["tot_novweb"], 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::$cDataGener["tot_novapp"], 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'cit_carguew'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'cit_carguea'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'car_carguew'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'car_carguea'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'tra_transiw'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'tra_transia'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'cit_descarw'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'cit_descara'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'nov_entregw'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'nov_entrega'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'nov_generaw'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
            $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="1">'.number_format( self::getConteoEtapa(self::$cDataGener['tip_despac'], 'nov_generaa'), 0, ',', '.').'</td>';  #Nº de Novedades Registradas
          $mHtml .= '</tr>';


        $mHtml .= '</table>';
        return utf8_decode($mHtml);
    } 
    catch (Exception $e) 
    {
      echo "error Fn DataTablaVistaGeneral: ".$e -> getMessage();
    }
  }

  /*! \fn: DataTablaVistaGeneral
  *  \brief: datos totales de la pestana general
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function DataTablaGriddGeneral($_AJAX)
  {
    try 
    {
        #$mCodtipdes = self::getTiposDespachos();
        $mHtml  = '<table width="100%" align="center">'; 

          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Modalidad</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Nº de Despachos Registrados</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Cita de Cargue</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Valor A Facturar</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Cargue</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Valor A Facturar</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Transito</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Valor A Facturar</td>';            
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Descargue</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Valor A Facturar</td>';            
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Novedades En Entrega</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Valor A Facturar</td>';            
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Generales Moviles</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Valor A Facturar</td>';            
            $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Total Novedades</td>';
            $mHtml .= '<td class="CellHead" align="center" rowspan="2" >Total Valor A Facturar</td>';
          $mHtml .= '</tr>';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
            $mHtml .= '<td class="CellHead" align="center" >WEB</td>';
            $mHtml .= '<td class="CellHead" align="center" >APP</td>';
          $mHtml .= '</tr>';  

          # Recorre todos los tipos de despachos -------------------------- 
          foreach (self::$cDataGener["tip_despac"] AS $mConsec => $mData):  
            # Procesos
            $mValCitcar = number_format(($mData["cit_cargue"] * self::$cDataGener["val_xnoved"]), 0, ',','.');
            $mValCargue = number_format(($mData["car_cargue"] * self::$cDataGener["val_xnoved"]), 0, ',','.');
            $mValTransi = number_format(($mData["tra_transi"] * self::$cDataGener["val_xnoved"]), 0, ',','.');
            $mValCitdes = number_format(($mData["cit_descar"] * self::$cDataGener["val_xnoved"]), 0, ',','.');
            $mValnovent = number_format(($mData["nov_entreg"] * self::$cDataGener["val_xnoved"]), 0, ',','.');
            $mValgenera = number_format(($mData["nov_genera"] * self::$cDataGener["val_xnoved"]), 0, ',','.');

            # Total registros por tipo despacho y valor total por tipo de despacho
            $mTotalNovedEtap = $mData["cit_cargue"] + $mData["car_cargue"] + $mData["tra_transi"] + $mData["cit_descar"] + $mData["nov_entreg"];
            $mTotalNovedWebs = $mData["cit_carguew"] + $mData["car_carguew"] + $mData["tra_transiw"] + $mData["cit_descarw"] + $mData["nov_entregw"];
            $mTotalNovedApps = $mData["cit_carguea"] + $mData["car_carguea"] + $mData["tra_transia"] + $mData["cit_descara"] + $mData["nov_entrega"];
            $mTotalNovedEtva = ( $mTotalNovedEtap * self::$cDataGener["val_xnoved"] );


            $mHtml .= '<tr>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2" ><b>'.$mData["nom_tipdes"].'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'\', \'\', \'despac\')"> '.number_format($mData["num_despac"],0, ',', '.').'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'1\', \'\', \'\')"> '.number_format($mData["cit_cargue"], 0, ',', '.').'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="right"  rowspan="2" ><b>$ '.$mValCitcar.'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'2\', \'\', \'\')"> '.number_format($mData["car_cargue"], 0, ',', '.').'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="right"  rowspan="2" ><b>$ '.$mValCargue.'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'0.3\', \'\', \'\')"> '.number_format($mData["tra_transi"], 0, ',', '.').'</spam></b></td>';                   
              $mHtml .= '<td class="cellInfo onlyCell" align="right"  rowspan="2" ><b>$ '.$mValTransi.'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'4\', \'\', \'\')"> '.number_format($mData["cit_descar"], 0, ',', '.').'</spam></b></td>';            
              $mHtml .= '<td class="cellInfo onlyCell" align="right"  rowspan="2" ><b>$ '.$mValCitdes.'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'5\', \'\', \'\')"> '.number_format($mData["nov_entreg"], 0, ',', '.').'</spam></b></td>';

              $mHtml .= '<td class="cellInfo onlyCell" align="right"  rowspan="2" ><b>$ '.$mValnovent.'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'6\', \'\', \'\', \'1\')"> '.number_format($mData["nov_genera"], 0, ',', '.').'</spam></b></td>';

              $mHtml .= '<td class="cellInfo onlyCell" align="right"  rowspan="2" ><b>$ '.$$mValgenera.'</b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="1" colspan="2"><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \''.$mEtapax.'\', \''.$row["cod_noveda"].'\')"> '. number_format( $mTotalNovedEtap, 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2"><b>$ '.number_format( $mTotalNovedEtva, 0)  .'</b></td>';   
            $mHtml .= '</tr>';
            $mHtml .= '<tr>'; 
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'1\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mData['cit_carguew'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'1\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mData['cit_carguea'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'2\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mData['car_carguew'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'2\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mData['car_carguea'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'0.3\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mData['tra_transiw'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'0.3\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mData['tra_transia'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'4\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mData['cit_descarw'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'4\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mData['cit_descara'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'5\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mData['nov_entregw'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'5\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mData['nov_entrega'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'6\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mData['nov_generaw'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \'6\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mData['nov_generaa'], 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \''.$mEtapax.'\', \''.$row["cod_noveda"].'\', \'2\', \'2\')"> '. number_format( $mTotalNovedWebs, 0 ).'</spam></b></td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1" ><b><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$mConsec.'\', \''.$mEtapax.'\', \''.$row["cod_noveda"].'\', \'1\', \'1\')"> '. number_format( $mTotalNovedApps, 0 ).'</spam></b></td>';
            $mHtml .= '</tr>';
          endforeach;

        $mHtml .= '</table>';

        

        return $_SESSION["ExcelFacturCorona"] = utf8_decode($mHtml);


         
    } 
    catch (Exception $e) 
    {
      echo "error Fn DataTablaGriddGeneral: ".$e -> getMessage();
    }
  }

  /*! \fn: DataTablaGeneral
  *  \brief: datos totales de la pestana general
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function DataTipdes($_AJAX)
  {
    try 
    {
      
      $mValor = self::getTipSerTransp( $_AJAX[transp] );
      $mData = self::getDespacByTipdes( $_AJAX[cod_tipdes], $_AJAX , NULL ,true );
      $mDespac = join( ', ', GetColumnFromMatrix( $mData, 'num_despac' ) );
      $mCantNov = sizeof( self::getNovedades($mDespac, NULL, NULL) );

      $mHtml  = '<div id="contenedor2" class="StyleDIV" ><table width="80%" align="center">';
        $mHtml .= '<tr>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="2" colspan="1">Despachos Generados</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Nº de Novedades Registrados</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="2" colspan="1">Valor A Facturar</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Cita De Cargue</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Cargue</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Transito</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Descargue</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Novedades En Entrega</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="2">Generales Moviles</td>';
        $mHtml .= '<tr>';           
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">WEB</td>';
          $mHtml .= '<td class="CellHead" align="center" rowspan="1" colspan="1">APP</td>';
        $mHtml .= '</tr>';           
        $mHtml .= '</tr>';           
        $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2"><spam style="cursor: pointer; color:#35650F; " onclick="LoadDetail(\''.$_AJAX["cod_tipdes"].'\', \'\', \'\', \'despac\')"> '.number_format(sizeof($mData), 0, ',', '.').'</spam></td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format($mCantNov, 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" rowspan="2">$ '.number_format( ($mCantNov * $mValor[val_regist] ), 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '1'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '2'   ) )  ) , 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '0,3' ) )  ) , 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '4'   ) )  ) , 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '5'   ) )  ) , 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="2">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '6', NULL, NULL, 1   ) )  ) , 0, ',', '.').'</td>';
        $mHtml .= '</tr>'; 
        $mHtml .= '<tr>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, NULL, NULL, NULL, '2'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, NULL, NULL, NULL, '1'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '1', NULL, NULL, '2'   ) )  ) , 0, ',', '.').'</td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '1', NULL, NULL, '1'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '2', NULL, NULL, '2'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '2', NULL, NULL, '1'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '0,3', NULL, NULL, '2'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '0,3', NULL, NULL, '1'   ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '4', NULL, NULL, '2' ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '4', NULL, NULL, '1' ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '5', NULL, NULL, '2' ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '5', NULL, NULL, '1' ) )  ) , 0, ',', '.').'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( 0).'</td>'; 
          $mHtml .= '<td class="cellInfo onlyCell" align="center" colspan="1">'.number_format( ( sizeof( self::getNovedades($mDespac, false, '6', NULL, NULL, '1' ) )  ) , 0, ',', '.').'</td>'; 
        $mHtml .= '</tr>'; 
      $mHtml .= '</table>';

      $mHtml .= '</br>';

      #getNovedades( $mNumDespac, $mGroupNov = false, $mCodEtapax = NULL , $mCodNoveda = NULL, $mGroupDes = false)


     

      # Zona de los taps
      $mHtml .= '<div id="TabsGrillaID">';
        # Each para crear las pestanas de los tabs ------------------------------------------------------------------------------------
        $mHtml .= '<ul>';
        foreach (self::$mArrayEtapas AS $mCodEtapax => $mNomEtapax):
          $mHtml .= '<li><a href="#tabs-'.$mNomEtapax["nom_pestan"].'">'.$mNomEtapax["tit_pestan"].'</a></li>';     
        endforeach;
        $mHtml .= '</ul>';        
        # Fin Each para crear las pestanas de los tabs --------------------------------------------------------------------------------

        # Each para los divs de los contenidos por pestana ----------------------------------------------------------------------------
        foreach (self::$mArrayEtapas AS $mCodEtapax => $mNomEtapax):
          $mHtml .= '<div id="tabs-'.$mNomEtapax["nom_pestan"].'">';     
          $mHtml .= self::DataEtapaDespac( $mDespac, $mCodEtapax , $mValor["val_regist"], $_AJAX["cod_tipdes"] ); 
          $mHtml .= '</div>';     
        endforeach;
        # Termina each de los div de contenido por pestana ----------------------------------------------------------------------------
      $mHtml .= '</div></div>';
      # Fin zona de los taps

      #$mHtml .= '<script>$("#TabsGrillaID").tabs("destroy");$("#TabsGrillaID").tabs();</script>';

 
 


      # Arma las pestanas segun las etapas -----------------------------------

      echo utf8_decode($mHtml);
    }
    catch (Exception $e) 
    { 
        echo "error Fn DataGeneral: ".$e -> getMessage();
    }
  }

  /*! \fn: DataEtapaDespac
   *  \brief: Crea la tabla del detalle de novedades por etapas
   *  \author: Ing. Fabian Salinas
   *  \date: 30/06/2015
   *  \date modified: dia/mes/año
   *  \param: mDespac  String  Numero de Despachos
   *  \param: mEtapax  String  Etapas del despacho
   *  \param: mValor  Integer  Valor del registro por transportadora
   *  \return valor que retorna
   */
  
  private function DataEtapaDespac( $mDespac, $mEtapax, $mValor , $mCodTipdes = NULL)
  {
    switch ($mEtapax)
    {
      case '1':
        $mNovEtapa = 'Cita De Cargue';
        break;
      case '2':
        $mNovEtapa = 'Cargue';
        break;
      case '0,3':
        $mNovEtapa = 'Transito';
        break;
      case '4':
        $mNovEtapa = 'Descargue';
        break;
      case '5':
        $mNovEtapa = 'Novedades En Entrega';
        break;
      case '6':
        $mNovEtapa = 'Generales Moviles';
        break;
    }
    if($mEtapax == '6')
    {
      $mNovedades = self::getNovedades( $mDespac, true, $mEtapax, NULL, NULL, 1 );
    }
    else
    {
      $mNovedades = self::getNovedades( $mDespac, true, $mEtapax );
    }
    $mHtml = '<div id="contenedor3" class="StyleDIV" ><table width="90%" align="center">';
      $mHtml .= '<tr>';
        $mHtml .= '<th class="CellHead" align="center" colspan="3">'.$mNovEtapa.'</th>';
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" align="center" >Novedad</td>';
        $mHtml .= '<td class="CellHead" align="center" >No. de Novedades</td>';
        $mHtml .= '<td class="CellHead" align="center" >Valor A Facturar</td>';
      $mHtml .= '</tr>';

      $mTotal = array(0, 0);
      foreach ($mNovedades as $row)
      {
        $mVal = $row["cantidad"] * $mValor;
        $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo onlyCell" align="left" >&ensp;<b>'.$row["nom_noveda"].'</b></td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b><spam style="cursor: pointer; color:#35650F;" onclick="LoadDetail(\''.$mCodTipdes.'\', \''.$mEtapax.'\', \''.$row["cod_noveda"].'\', \'NULL\', '.($mEtapax==6?'\'1\'':'\'NULL\'').')"> '.  number_format($row["cantidad"], 0, ',', '.').' </spam></b></td>';
          $mHtml .= '<td class="cellInfo onlyCell" align="right" ><b>$ '.number_format($mVal, 0, ',', '.').'</b></td>';
        $mHtml .= '</tr>';

        $mTotal[0] = $mTotal[0] + $row[cantidad];
        $mTotal[1] = $mTotal[1] + $mVal;
      }

      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" align="center" >TOTAL:</td>';
        $mHtml .= '<td class="CellHead" align="center" ><spam style="cursor: pointer;"   onclick="LoadDetail(\''.$mCodTipdes.'\', \''.$mEtapax.'\')"> '.number_format($mTotal[0], 0, ',', '.').' </spam></td>';
        $mHtml .= '<td class="CellHead" align="right" ><b>$ '.number_format($mTotal[1], 0, ',', '.').'</b></td>';
      $mHtml .= '</tr>';

    $mHtml .= '</table></div>';

    return utf8_encode($mHtml);
  }

  /*! \fn: LoadDetail
  *  \brief: Funcion principal para pintar tabla html con la grilla del detallado
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function LoadDetail( $_AJAX = NULL )
  {
    try 
    {
     
     #$mValor = self::getTipSerTransp( $_AJAX[transp] );
      $mData = self::getDespacByTipdes( $_AJAX[cod_tipdes], $_AJAX );
      $mDespac = join( ', ', GetColumnFromMatrix( $mData, 'num_despac' ) );
      $mCantNov =  self::getNovedades($mDespac, false, $_AJAX["cod_etapax"] ,$_AJAX["cod_noveda"], NULL, $_AJAX["tip_Noveda"] );
      $mHtml  = '<div id="contenedor4" style="background: #ffffff; width:150%; border-radius: 10px" >';
      $mHtml  .= '<spam style="cursor: pointer; color:#35650F;" onclick="LoadExcel(\'ExcelFacturCorona\')">[ EXCEL ]</spam>';
        $mHtml .= '<table width="100%" align="center">';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" align="center" ><b>#</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Despacho SATT</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Agencia</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Manifiesto</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Nº de Viaje</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Tipo Despacho</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Tipo Transportadora</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Poseedor</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Ciudad Origen</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Ciudad Destino</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Placa</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Conductor</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Cédula</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Celular</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Seguimiento Faro</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Fecha Salida</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Fecha Llegada</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Diferencia</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Empresa Transportadora</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Generador</b></td>'; 
            $mHtml .= '<td class="CellHead" align="center" ><b>Novedad</b></td>'; 
          $mHtml .= '</tr>';

          foreach ($mCantNov AS $mKey => $mData):

            $mDespac = self::getDataByNumDespac( $mData["num_despac"] );
   
            switch ($mDespac["tip_transp"] ) {
              case '1': $mTipEmpres = 'Flota Propia'; break;
              case '2': $mTipEmpres = 'Terceros'; break;
              case '3': $mTipEmpres = 'Empresas'; break;           
              default: $mTipEmpres = 'N/A'; break;
            }
 
            # Diferencia de tiempo entre la salida y lleagada del despacho --------
            $time_inicio = strtotime( $mDespac["fec_salida"] );
            $time_fin = strtotime( $mDespac["fec_llegad"] );
            $diff = (int)abs( round( ( $time_fin - $time_inicio ) / 60 ) );



          $mLink = '?cod_servic=3302&window=central&opcion=1&despac='.$mData["num_despac"];
          $mHtml .= '<tr>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.($mKey + 1).'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b><a href="'.$mLink.'" target="_blank" > <spam class="ahref" >'.$mData["num_despac"].'</spam></a></b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_agenci"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["cod_manifi"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_viajex"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_tipdes"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mTipEmpres.'</b></td>'; # Tipo transportadora
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_poseed"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_ciuori"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_ciudes"].'</b></td>';  # ciudad destino
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_placax"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_conduc"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["cod_conduc"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_conduc"].'</b></td>'; # Celular conductor
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["ind_segfar"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["fec_salida"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["fec_llegad"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.number_format($diff, 0, ',', '.').' Min(s)</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["abr_transp"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["abr_transp"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mData["nom_noveda"].'</b></td>';
          $mHtml .= '</tr>';
          endforeach;

        $mHtml .= '</table>';
      $mHtml .= '</div>';

      echo $_SESSION["ExcelFacturCorona"] = utf8_decode($mHtml);

    } 
    catch (Exception $e) 
    {
      echo "error Fn DataGeneral: ".$e -> getMessage();
    }
  }


  /*! \fn: LoadDespacList
  *  \brief: Funcion principal para mostrar los despachos
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html
  */
  private function LoadDespacList( $_AJAX = NULL )
  {
    try 
    {
    	  $mCantNov = self::getDespacByTipdes( $_AJAX[cod_tipdes], $_AJAX, NULL, true );	  
        $mHtml  = '<div class="StyleDIV" >';
        $mHtml  .= '<spam style="cursor: pointer; color:#35650F;" onclick="LoadExcel(\'ExcelFacturCorona\')">[ EXCEL ]</spam>';
        $mHtml .= '<table width="100%" align="center">';
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" align="center" ><b>#</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Despacho SATT</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Agencia</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Manifiesto</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Nº de Viaje</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Solicitud</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Tipo Despacho</b></td>';

            $mHtml .= '<td class="CellHead" align="center" ><b>Cita De Cargue</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Cargue</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Transito</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Cita de Descargue</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Novedades En Entrega</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Nº Seguimientos Total</b></td>';



            $mHtml .= '<td class="CellHead" align="center" ><b>Tipo Transportadora</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Poseedor</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Ciudad Origen</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Ciudad Destino</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Placa</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Conductor</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Cédula</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Celular</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Seguimiento Faro</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Fecha Salida</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Fecha Llegada</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Diferencia</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Empresa Transportadora</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Generador</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>Novedades</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>No. EAL Registradas</b></td>';
            $mHtml .= '<td class="CellHead" align="center" ><b>No. EAL Cumplidas</b></td>';
          $mHtml .= '</tr>';

          foreach ($mCantNov AS $mKey => $mData):

            $mTotNoveda = array();

            $mDespac  = self::getDataByNumDespac( $mData["num_despac"] );
            $mCantNov = self::getNovedades($mData["num_despac"], false, NULL ,NULL, NULL, $_AJAX['tip_Noveda'] );
            $mEalxxxx = self::getEsferas( $mData["num_despac"] );


            $mTotNoveda["mCitCargue"] = sizeof( self::getNovedades($mData["num_despac"], false, "1"  , NULL, false, $_AJAX['tip_Noveda'] ) );
            $mTotNoveda["mSegCargue"] = sizeof( self::getNovedades($mData["num_despac"], false, "2"  , NULL, false, $_AJAX['tip_Noveda'] ) );
            $mTotNoveda["mSegTransi"] = sizeof( self::getNovedades($mData["num_despac"], false, "0,3", NULL, false, $_AJAX['tip_Noveda'] ) );
            $mTotNoveda["msegCitdes"] = sizeof( self::getNovedades($mData["num_despac"], false, "4"  , NULL, false, $_AJAX['tip_Noveda'] ) );
            $mTotNoveda["msegEntreg"] = sizeof( self::getNovedades($mData["num_despac"], false, "5"  , NULL, false, $_AJAX['tip_Noveda'] ) );

            
            switch ($mDespac["tip_transp"] ) {
              case '1': $mTipEmpres = 'Flota Propia'; break;
              case '2': $mTipEmpres = 'Terceros'; break;
              case '3': $mTipEmpres = 'Empresas'; break;           
              default: $mTipEmpres = 'N/A'; break;
            }
 
            # Diferencia de tiempo entre la salida y lleagada del despacho --------
            $time_inicio = strtotime( $mDespac["fec_salida"] );
            $time_fin = strtotime( $mDespac["fec_llegad"] );
            $diff = (int)abs( round( ( $time_fin - $time_inicio ) / 60 ) );


          $mLink = '?cod_servic=3302&window=central&opcion=1&despac='.$mData["num_despac"];
          $mHtml .= '<tr>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.($mKey + 1).'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b><a href="'.$mLink.'" target="_blank" > <spam class="ahref" >'.$mData["num_despac"].'</spam></a></b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_agenci"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["cod_manifi"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_viajex"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_solici"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_tipdes"].'</b></td>';

            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mTotNoveda["mCitCargue"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mTotNoveda["mSegCargue"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mTotNoveda["mSegTransi"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mTotNoveda["msegCitdes"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mTotNoveda["msegEntreg"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.array_sum($mTotNoveda).'</b></td>';

            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mTipEmpres.'</b></td>'; # Tipo transportadora
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_poseed"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_ciuori"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_ciudes"].'</b></td>';  # ciudad destino
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_placax"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["nom_conduc"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["cod_conduc"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["num_conduc"].'</b></td>'; # Celular conductor
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["ind_segfar"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["fec_salida"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["fec_llegad"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.number_format($diff, 0, ',', '.').' Min(s)</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["abr_transp"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="left" ><b>'.$mDespac["abr_transp"].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.sizeof( $mCantNov ).'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mEalxxxx[0].'</b></td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center" ><b>'.$mEalxxxx[1].'</b></td>';
          $mHtml .= '</tr>';
          endforeach;

        $mHtml .= '</table>';
        $mHtml .= '</div>';


         
        echo $_SESSION["ExcelFacturCorona"] =  utf8_decode($mHtml);

    }
    catch(Exception $e)
    {
    	echo "error Fn LoadDespacList: ".$e -> getMessage();
    }
  }

  /*! \fn: getEsferas
  *  \brief: Consulta el estado de las novedades registradas por las esferas
  *  \author: Ing. Nelson Liberato
  *  \date: 07/07/2015     
  *  \param: mNumDespac: array de datos ($_REQUEST) 
  *  \return array html/stream
  */
  function getEsferas( $mNumDespac )
  {
      $to = array(); 
      # Busca puestos fisicos en la ruta del despacho
      $mSelectB = "SELECT b.cod_contro, b.nom_contro
                    FROM ".BASE_DATOS.".tab_genera_rutcon a,
                         ".BASE_DATOS.".tab_genera_contro b
                   WHERE a.cod_contro  = b.cod_contro
                     AND a.cod_rutasx = ( SELECT cod_rutasx
                                            FROM ".BASE_DATOS.".tab_despac_vehige
                                           WHERE num_despac = '".$mNumDespac."' 
                                        ) 
                     /* AND b.nom_contro LIKE '%E@L%' */ 
                     AND b.ind_virtua = '0' ";

      $consulta = new Consulta($mSelectB, self::$cConection);
      $ealxxx = $consulta -> ret_matriz();
      $to[0] = sizeof($ealxxx);

      # Si hay puestos fisicos, cuenta el numero de novedades en los puestos fisicos -------------------------------
      if( sizeof($ealxxx) > 0 )
      {
        $mData = '';
        foreach( $ealxxx as $row )
          $mData .= $mData != '' ? ', '.$row['cod_contro'] : $row['cod_contro'];

        $mSelectC = "SELECT num_despac, cod_contro 
                      FROM ".BASE_DATOS.".tab_despac_noveda
                     WHERE num_despac = '".$mNumDespac."' 
                       AND cod_contro IN( ".$mData." ) ";

        $consulta = new Consulta($mSelectC, self::$cConection);
        $ealxxx = $consulta -> ret_matriz();
        $to[1] = sizeof($ealxxx);  
      }else
        $to[1] = '0';  
     
      return $to;
    }

  /*! \fn: LoadExcel
  *  \brief: exporta un string HTML a excel
  *  \author: Ing. Nelson Liberato
  *  \date: 07/07/2015     
  *  \param: _AJAX: array de datos ($_REQUEST) 
  *  \return string html/stream
  */
  function LoadExcel()
  {
    try 
    {
      $mFileName = "Facturacion_corona_".date("Y_m_d_H_m_s").".xls";
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=".$mFileName);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $_SESSION[$_REQUEST["type"]]; 
    } 
    catch (Exception $e) 
    {
      echo "<pre>";  print_r($e->getMessage());   echo "</pre>"; 
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
        $mListTipdes .= $mCodTipdes != NULL ? ' AND a.cod_tipdes = "'.$mCodTipdes.'" ' : '';
        #$mListTipdes .= ' LIMIT 2 ';


        $consulta = new Consulta($mListTipdes, self::$cConection);
        $mListTipdes = $consulta -> ret_matrix("a"); 
        return $mListTipdes;
    } 
    catch (Exception $e) 
    {
      return array("cod_respon" => "3001", "msg_respon" => $e -> getMessage() );
    }
  }  

  /*! \fn: getTiposDespachos
  *  \brief: lista los tipos de despachos existentes en la BD
  *  \author: Ing. Nelson Liberato
  *  \date: 24/06/2015      
  *  \param: mCodTipdes: Tipo de despacho      
  *  \param: _AJAX: Datos para hacer los filtros que entran por la request      
  *  \param: mReturn: sql = Retorna consulta SQL     
  *  \return array
  */
  private function getDespacByTipdes( $mCodTipdes = NULL, $_AJAX = NULL, $mReturn = NULL , $mGroupDes = false)
  {
    try 
    {
        
        $mQuery = "SELECT a.num_despac, a.cod_manifi, 
                          b.num_desext AS num_viajex, 
                          c.cod_transp
                    FROM ".BASE_DATOS.".tab_despac_despac a, 
                         ".BASE_DATOS.".tab_despac_vehige c
                    LEFT JOIN ".BASE_DATOS.".tab_despac_sisext  b ON c.num_despac = b.num_despac 
                   WHERE  1=1 AND 
                          c.ind_activo = 'S' AND 
                          a.fec_salida IS NOT NULL   ";
       
        $mQuery .=  ' AND a.num_despac = c.num_despac ';
        $mQuery .=  ' AND c.cod_transp  = "'.$_AJAX["transp"].'" ';
        $mQuery .=  ' AND a.fec_llegad  >= "'.$_AJAX["fec_ini"].' 00:00:00"  AND a.fec_llegad <= "'.$_AJAX["fec_fin"].' 23:59:59" ';


        $mQuery .= $mCodTipdes != NULL ? ' AND a.cod_tipdes = "'.$mCodTipdes.'" ' : '';
        #$mQuery .= $mGroupDes ==  true ? ' GROUP BY a.num_despac ' : '';
      
        if( $mReturn == 'sql' )
          return $mQuery;
        else{
          $consulta = new Consulta($mQuery, self::$cConection);
          $mListTipdes = $consulta -> ret_matrix("a"); 
          return $mListTipdes;
        }
    } 
    catch (Exception $e) 
    {
      return array("cod_respon" => "3001", "msg_respon" => $e -> getMessage() );
    }
  }

  /*! \fn: getNovedades
   *  \brief: Trae las novedades de los despachos
   *  \author: Ing. Fabian Salinas
   *  \date: 30/06/2015
   *  \date modified: dia/mes/año
   *  \param: mNumDespac  String  Despachos
   *  \param: mGroupNov  Boolean  true = Agrupa por Codigo de Novedad
   *  \param: mCodEtapax  String  Codigos Etapas
   *  \return: Matriz
   */
  private function getNovedades( $mNumDespac, $mGroupNov = false, $mCodEtapax = NULL , $mCodNoveda = NULL, $mGroupDes = false, $mTipNoveda = NULL)
  {
    $mNumDespac = $mNumDespac == '' ? '0' : $mNumDespac;
    if( $mGroupNov == true )
      $mQuery = " SELECT x.nom_noveda, x.cod_noveda, COUNT(x.cod_noveda) AS cantidad ";
    else
      $mQuery = "SELECT x.nom_noveda, x.usr_creaci, x.fec_noveda, 
                        x.nom_sitiox, x.obs_noveda, x.nov_especi, 
                        x.cod_noveda, x.tab_origen, x.cod_consec, 
                        x.ind_ensiti, x.ind_fuepla, x.tiem_duraci, 
                        x.cod_etapax, x.nom_etapax, x.fec_creaci AS fec_crenov, x.num_despac ";

    $mQuery .= " FROM (
                        (
                               SELECT a.fec_contro AS fec_noveda, 
                                      UPPER(b.nom_noveda) AS nom_noveda, 
                                      a.usr_creaci, a.fec_creaci, 
                                      UPPER(c.nom_sitiox) AS nom_sitiox, 
                                      a.obs_contro AS obs_noveda, 
                                      b.nov_especi, a.cod_noveda, 
                                      '1' AS tab_origen, a.cod_consec, 
                                      a.cod_contro, '0' AS ind_ensiti, 
                                      b.ind_fuepla, a.tiem_duraci, 
                                      b.cod_etapax, d.nom_etapax,
                                      a.num_despac
                                 FROM ".BASE_DATOS.".tab_despac_contro a 
                           INNER JOIN ".BASE_DATOS.".tab_genera_contro x 
                                   ON a.cod_contro = x.cod_contro 
                           INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                                   ON a.cod_noveda = b.cod_noveda 
                           INNER JOIN ".BASE_DATOS.".tab_despac_sitio c 
                                   ON a.cod_sitiox = c.cod_sitiox 
                           INNER JOIN ".BASE_DATOS.".tab_genera_etapax d 
                                   ON b.cod_etapax = d.cod_etapax  
                           INNER JOIN ".BASE_DATOS.".tab_genera_usuari y                                   
                                   ON y.cod_usuari = a.usr_creaci 
 
                                WHERE a.num_despac IN ( {$mNumDespac} ) 
                                /* AND x.ind_virtua = '1'  */
                                ".( $mTipNoveda == 1 ?"" : " AND y.cod_perfil IN (".self::$cPerfiles.")" )."
                                ".( $mCodEtapax != NULL && $mCodEtapax != 'undefined' ? " AND b.cod_etapax IN ( ".str_replace(".", ",", $mCodEtapax )." )" : ""  )."
                                ".( $mCodNoveda != NULL && $mCodNoveda != 'undefined' ? " AND a.cod_noveda IN ( ".$mCodNoveda." )" : ""  )."
                                ".( self::$cNitCorona == $_REQUEST['transp'] ? " AND y.cod_perfil NOT IN (".self::$cNotPerfi.") " : "" )."

                        )
                        UNION 
                        (
                               SELECT a.fec_noveda, 
                                      UPPER(b.nom_noveda) AS nom_noveda,
                                      a.usr_creaci, 
                                      a.fec_creaci,  
                                      UPPER(x.nom_contro) AS nom_sitiox, 
                                      a.des_noveda AS obs_noveda, 
                                      b.nov_especi, a.cod_noveda, 
                                      '2' AS tab_origen, '1' AS cod_consec, 
                                      a.cod_contro, '1' AS ind_ensiti, 
                                      b.ind_fuepla, a.tiem_duraci, 
                                      b.cod_etapax, d.nom_etapax,
                                      a.num_despac
                                 FROM ".BASE_DATOS.".tab_despac_noveda a 
                           INNER JOIN ".BASE_DATOS.".tab_genera_contro x 
                                   ON a.cod_contro = x.cod_contro 
                           INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                                   ON a.cod_noveda = b.cod_noveda 
                           INNER JOIN ".BASE_DATOS.".tab_genera_etapax d 
                                   ON b.cod_etapax = d.cod_etapax 
                           INNER JOIN ".BASE_DATOS.".tab_genera_usuari y                                   
                                   ON y.cod_usuari = a.usr_creaci 
                                WHERE a.num_despac IN ( {$mNumDespac} ) 
                                  /* AND x.ind_virtua = '1'  */
                                  ".( $mTipNoveda == 1 ?"" : " AND y.cod_perfil IN (".self::$cPerfiles.")" )."
                                  ".( $mCodEtapax != NULL && $mCodEtapax != 'undefined' ? " AND b.cod_etapax IN ( ".str_replace(".", ",", $mCodEtapax )." )" : ""  )."
                                  ".( $mCodNoveda != NULL && $mCodNoveda != 'undefined' ? " AND a.cod_noveda IN ( ".$mCodNoveda." )" : ""  )."
                                  ".( self::$cNitCorona === '860068121' ? " AND y.cod_perfil NOT IN (".self::$cNotPerfi.") " : "" )."
                        )
                        UNION
                        (
                               SELECT a.fec_solici AS fec_noveda, 
                                      c.nom_noveda,
                                      a.usr_solici AS usr_creaci, 
                                      a.fec_solici AS fec_creaci, 
                                      UPPER(b.nom_contro) AS nom_sitiox, 
                                      tex_encabe AS obs_noveda, 
                                      '0' AS nov_especi, a.cod_noveda, 
                                      '3' AS tab_origen, d.cod_consec, 
                                      a.cod_contro, '0' AS ind_ensiti, 
                                      c.ind_fuepla, '' AS tiem_duraci, 
                                      c.cod_etapax, e.nom_etapax ,
                                      a.num_despac
                                 FROM ".BASE_DATOS.".tab_recome_asigna a 
                           INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                                   ON a.cod_contro = b.cod_contro 
                           INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                                   ON a.cod_noveda = c.cod_noveda 
                           INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                                   ON a.cod_recome = d.cod_consec 
                           INNER JOIN ".BASE_DATOS.".tab_genera_etapax e 
                                   ON c.cod_etapax = e.cod_etapax 
                                WHERE a.num_despac IN ( {$mNumDespac} )                                 
                                  /* AND b.ind_virtua = '1' */
                                  ".( $mCodEtapax != NULL && $mCodEtapax != 'undefined' ? " AND c.cod_etapax IN ( ".str_replace(".", ",", $mCodEtapax )." )" : ""  )."
                                  ".( $mCodNoveda != NULL && $mCodNoveda != 'undefined' ? " AND a.cod_noveda IN ( ".$mCodNoveda." )" : ""  )."
                                   
                        )
                         
                      ) x 
                WHERE 1=1 
              ";

    $mQuery .= $mCodEtapax != NULL && $mCodEtapax != 'undefined' ? " AND x.cod_etapax IN ( ".str_replace(".", ",", $mCodEtapax )." )" : "" ;
    $mQuery .= $mCodNoveda != NULL && $mCodNoveda != 'undefined' ? " AND x.cod_noveda IN ( ".$mCodNoveda." )" : "" ;
    $mQuery .= $mTipNoveda == 1 ? " AND x.nom_noveda LIKE '%(MOVIL)%' " : "" ;
    $mQuery .= $mTipNoveda == 2 ? " AND x.nom_noveda NOT LIKE '%(MOVIL)%' " : "" ;

    $mQuery .= $mGroupNov == true ? " GROUP BY x.cod_noveda ORDER BY x.nom_etapax ASC " : "" ;
    $mQuery .= $mGroupDes == true ? " GROUP BY x.num_despac ORDER BY x.num_despac ASC " : "" ;

      //echo "<pre style='display:none;' >"; print_r( $mQuery ); echo "</pre>";
    
    $mConsult = new Consulta($mQuery, self::$cConection);
    return $mResult = $mConsult -> ret_matrix("a"); 
  }

  /*! \fn: getTipSerTransp
   *  \brief: Trae la informacion de tipo de servicio por transportadora
   *  \author: Ing. Fabian Salinas
   *  \date: 30/06/2015
   *  \date modified: dia/mes/año
   *  \param: mCodTransp  Integer  Codigo transportadora
   *  \return: Arreglo
   */
  private function getTipSerTransp( $mCodTransp )
  {
    $mQuery = " SELECT c.ind_segcar, c.ind_segtra, c.ind_segdes, 
                       c.cod_transp, c.num_consec, d.nom_tipser, 
                       e.abr_tercer, c.tie_contro AS tie_nacion, 
                       c.tie_conurb AS tie_urbano, c.val_regist
                  FROM ".BASE_DATOS.".tab_transp_tipser c 
            INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
                    ON c.cod_tipser = d.cod_tipser 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
                    ON c.cod_transp = e.cod_tercer 
                 WHERE c.cod_transp = '{$mCodTransp}'
              ORDER BY c.cod_transp, c.num_consec DESC
              ";
    $mConsult = new Consulta($mQuery, self::$cConection);
    $mResult = $mConsult -> ret_matrix("a"); 
    return $mResult[0];
  }


  /*! \fn: getDataByNumDespac
   *  \brief: Trae la informacion de un despacho
   *  \author: Ing. Nelson Liberato
   *  \date: 03/07/2015   
   *  \param: mNumDespac  Int Numero del despacho
   *  \return: Arreglo
   */
  private function getDataByNumDespac( $mNumDespac = NULL )
  {
    $mQuery  = "SELECT a.num_despac,
                       a.fec_salida,
                       d.nom_ciudad AS nom_ciuori,
                       e.nom_ciudad AS nom_ciudes,
                       a.cod_manifi,
                       c.abr_tercer AS abr_transp,
                       a.fec_llegad,
                       b.num_placax,
                       f.abr_tercer AS nom_conduc,
                       f.cod_tercer AS cod_conduc,
                       f.num_telmov AS num_conduc, 
                       c.cod_tercer AS cod_transp,
                       d.cod_ciudad,
                       e.cod_ciudad,
                       z.nom_agenci,
                       a.cod_tipdes,
                       h.num_despac AS num_viajex,
                       h.tip_transp,
                       i.nom_tipdes,
                       IF(a.ind_defini = '0', 'SI', 'NO' ) AS ind_segfar,
                       h.nom_poseed,
                       num_solici
                        FROM ".BASE_DATOS.".tab_despac_despac a
                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                        INNER JOIN ".BASE_DATOS.".tab_genera_agenci z ON b.cod_agenci = z.cod_agenci
                        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.cod_transp = c.cod_tercer
                        INNER JOIN ".BASE_DATOS.".tab_genera_rutasx g ON b.cod_rutasx = g.cod_rutasx
                        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d ON g.cod_ciuori = d.cod_ciudad
                        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e ON g.cod_ciudes = e.cod_ciudad 
                        INNER JOIN ".BASE_DATOS.".tab_tercer_tercer f ON f.cod_tercer = b.cod_conduc
                        INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i ON a.cod_tipdes = i.cod_tipdes 
                    LEFT JOIN ".BASE_DATOS.".tab_despac_corona h ON a.num_despac = h.num_dessat
                    WHERE a.fec_salida IS NOT NULL 
                      AND a.num_despac = '".$mNumDespac."'";

                     
            


    $mConsult = new Consulta($mQuery, self::$cConection);
    $mResult = $mConsult -> ret_matrix("a"); 
    return $mResult[0];
  }

  /*! \fn: getConteoEtapa
   *  \brief: Recorre array en busqueda de un dato
   *  \author: Edward Serrano
   *  \date: 09/09/2017   
   *  \param: mData  Array a procesar
   *  \param: mEtapa  dato a buscar
   *  \return: int conteo de lo encontrado
   */
  private function getConteoEtapa( $mData, $mEtapa )
  {
    $mConteo = 0;
    foreach ($mData as $key => $value) 
    {
      if(is_array($value))
      {
        $mConteo += self::getConteoEtapa($value, $mEtapa);
      }
      if($key == $mEtapa)
      {
        $mConteo += $value; 
      }
    }
    return $mConteo;
  }
}

$proceso = new AjaxFacturCorona();
 ?>
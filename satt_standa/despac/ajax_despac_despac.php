<?php
/*! \file: ajax_despac_despac.php
 *  \brief: 
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

session_start();
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

/*! \class: AjaxDespac
 *  \brief: 
 */
class AjaxDespac
{
    var $conexion,
        $cod_aplica,
        $usuario;

    function __construct($co, $us, $ca)
    {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $this->principal();
    }

    function principal()
    {
        @include( "../lib/ajax.inc" );

        $this -> conexion = $AjaxConnection;
        $paramsJson = json_decode($_REQUEST['paramsJson'],true);
        $_REQUEST[Option] = isset($_REQUEST[Option]) ? $_REQUEST[Option]: $paramsJson["option"];

        switch ($_REQUEST[Option])
        {
            case "FormNovedaUpdate":
                AjaxDespac::FormNovedaUpdate();
                break;
            case "FormDeleteNoveda":
                AjaxDespac::FormDeleteNoveda();
                break;  
            case "DeleteNoveda":
                AjaxDespac::DeleteNoveda();
                break;
            case "UpdateNoveda":
                AjaxDespac::UpdateNoveda();
                break;

            case 'DetallePcPadre':
                AjaxDespac::DetallePcPadre();
                break;
           
            case 'FormNewRuta':
                AjaxDespac::FormNewRuta();
                break;

            case 'CambioRuta':
                AjaxDespac::CambioRuta();
                break;

            case 'VerificarManifiesto':
                AjaxDespac::VerificarManifiesto();
                break;
            case 'controDespac':
                AjaxDespac::getControDespac($paramsJson);
                break;
            case 'notasControDespac':
                AjaxDespac::getNotasControDespac($paramsJson);
                break;
            case 'dataMapaDespac':
                AjaxDespac::getDataMapaDespac($paramsJson);
                break;
            default:
                header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                //$this->Listar();
                break;
        }
    }

    function getControDespac($paramsJson)
    {
        $datos_usuario = $this->usuario;
    
        $limit = isset($_REQUEST['registros']) ? intval($_REQUEST['registros']) : 100;
        $pagina = isset($_REQUEST['pagina']) ? intVal($_REQUEST['pagina']) : 0;
        $mRuta = array("link"=>1, "finali"=>0, "opcurban"=>0, "lleg"=>1, "tie_ultnov"=>$_REQUEST[tie_ultnov]);

        @include_once( "../despac/DespachosNew.inc" );
        $listado_prin = new DespachosNew($_REQUEST[cod_servic], 2, $this->cod_aplica, $this->conexion);

        $paramsJson["filtro"] = isset($paramsJson["filtro"]) ? $paramsJson["filtro"]:'1';
        $data = $listado_prin->dataPlanRuta($paramsJson["num_despac"], $datos_usuario,$mRuta, $limit, $pagina, $paramsJson["filtro"]);

        // Mostrado resultados
        $output = [];
        $matriz = $data['results'];
        $output['totalRegistros'] = $data['totalRegistros'];
        $output['totalFiltro'] = $data['totalFiltro'];
        $output['data'] = '';
        $output['paginacion'] = '';
        $output['pagina'] = $pagina;
        $totalRegistros = $data['totalRegistros'];

        if (!empty($matriz)) {

            if( !$data['indConsol'])
            {
                foreach ($matriz as $row ) 
                {
                    $output['data'] .= '<tr>';
                    $output['data'] .= '<td class="celda_info  text-center"  >'.$row['sit_seguim'].'</td>';
                    if(($codseg==1) && (BASE_DATOS=='satt_faro')){
                        $output['data'] .= '<td class="celda_info text-center"  >'.$row['ubi_seguim'].'</td>';
                    } 
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['fec_progra'].'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['fec_planea'].'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['fec_creaci'].'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['cal_tiempo'].'</td>';
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['nom_noveda'].'</td>';

                    if($data['viewAlias']->inf_planru->sub->usr_creaci == 1) {
                        $output['data'] .= '<td class="celda_info  text-center" >'.$row['cod_usuari'].'</td>';
                    }
                    if($data['viewAlias']->inf_planru->sub->ali_usuari == 1) {
                        $output['data'] .= '<td class="celda_info  text-center" >'.$listado_prin->getAlias($row['cod_usuari']).'</td>';
                    }
                    $output['data'] .= '</tr>';
                }
            }
            
          
        }

        // Paginaci�n
        if ($totalRegistros > 0) {
          $totalPaginas = ceil($totalRegistros / $limit);
          $output['totpag'] = $totalPaginas;

          $output['paginacion'] .= '<nav>';
          $output['paginacion'] .= '<ul class="pagination pagination-sm">';

          $numeroInicio = max(1, $pagina - 4);
          $numeroFin = min($totalPaginas, $numeroInicio + 9);

          for ($i = $numeroInicio; $i <= $numeroFin; $i++) {
              $output['paginacion'] .= '<li class="page-item' . ($pagina == $i ? ' active' : '') . '">';
              $output['paginacion'] .= '<a class="page-link"  onclick="pag_1.nextPage(' . $i . ')">' . $i . '</a>';
              $output['paginacion'] .= '</li>';
          }

          $output['paginacion'] .= '</ul>';
          $output['paginacion'] .= '</nav>';
        }

        //$output = mb_convert_encoding($output, 'UTF-8', 'auto');
        
       
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
    }

    function getNotasControDespac($paramsJson)
    {
        $limit = isset($_REQUEST['registros']) ? intval($_REQUEST['registros']) : 25;
        $pagina = isset($_REQUEST['pagina']) ? intVal($_REQUEST['pagina']) : 0;



        @include_once( "../despac/DespachosNew.inc" );
        $listado_prin = new DespachosNew($_REQUEST[cod_servic], 2, $this->cod_aplica, $this->conexion);
        
        $paramsJson["filtro"] = isset($paramsJson["filtro"]) ? $paramsJson["filtro"]:'1';
        $data = $listado_prin->dataNotasContro($paramsJson["num_despac"], $limit, $pagina, $paramsJson["filtro"]);

         // Mostrado resultados
         $output = [];
         $matriz = $data['results'];
         $output['totalRegistros'] = $data['totalRegistros'];
         $output['totalFiltro'] = $data['totalFiltro'];
         $output['data'] = '';
         $output['paginacion'] = '';
         $output['pagina'] = $pagina;
         $totalRegistros = $data['totalRegistros'];

 
         //var_dump($matriz);
        if (!empty($matriz)) {
            foreach ($matriz as $row ) 
            {
                $output['data'] .= '<tr>';
                $output['data'] .= '<td class="celda_info  text-center" ><textarea rows="3" cols="50" style="border: none; background-color: transparent;" readonly>'.$row['nom_sitiox'].'</textarea></td>';
                $output['data'] .= '<td class="celda_info  text-center" >'.$row['nom_noveda'].'</td>';
                $output['data'] .= '<td class="celda_info  text-center" ><textarea rows="3" cols="50" style="border: none; background-color: transparent;" readonly>'.$row['obs_noveda'].'</textarea></td>';
                $output['data'] .= '<td class="celda_info  text-center" >'.$row['fec_noveda'].'</td>';
                $output['data'] .= '<td class="celda_info  text-center" >'.$row['fec_creaci'].'</td>';
                if(isset($row['usr_creaci'])){
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['usr_creaci'].'</td>';
                }
                if(isset($row['als_creaci'])){
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['als_creaci'].'</td>';
                }
                if(isset($row['lin_action'])){
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['lin_action'].'</td>';
                }
                $output['data'] .= '<td class="celda_info  text-center" >'.$row['sol_efecti'].'</td>';
                
                if(isset($row['sol_novnem'])){
                    $output['data'] .= '<td class="celda_info  text-center" >'.$row['sol_novnem'].'</td>';
                }
                $output['data'] .= '</tr>';
            }
        }

        // Paginaci�n
        if ($totalRegistros > 0) {
            $totalPaginas = ceil($totalRegistros / $limit);
            $output['totpag'] = $totalPaginas;
  
            $output['paginacion'] .= '<nav>';
            $output['paginacion'] .= '<ul class="pagination pagination-sm">';
  
            $numeroInicio = max(1, $pagina - 4);
            $numeroFin = min($totalPaginas, $numeroInicio + 9);
  
            for ($i = $numeroInicio; $i <= $numeroFin; $i++) {
                $output['paginacion'] .= '<li class="page-item' . ($pagina == $i ? ' active' : '') . '">';
                $output['paginacion'] .= '<a class="page-link"  onclick="pag_2.nextPage(' . $i . ')">' . $i . '</a>';
                $output['paginacion'] .= '</li>';
            }
  
            $output['paginacion'] .= '</ul>';
            $output['paginacion'] .= '</nav>';
        }
  
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
    }

    function FormNovedaUpdate()
    {
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

        if($_REQUEST["table"] == '2') {
            $mQuery = "SELECT CONCAT(b.cod_noveda,'-',b.nom_noveda) AS nom_noveda, a.fec_noveda AS fec_contro, a.des_noveda AS obs_contro,
                              a.cod_noveda
                         FROM ".BASE_DATOS.".tab_despac_noveda a , 
                              ".BASE_DATOS.".tab_genera_noveda b
                        WHERE a.cod_noveda = b.cod_noveda AND
                              a.num_despac = '".$_REQUEST["despac"]."' AND 
                              a.cod_contro = '".$_REQUEST["puesto"]."' AND
                              a.cod_noveda = '".$_REQUEST["novedad"]."' AND
                              a.fec_noveda = '".$_REQUEST["date"]."' AND
                              a.usr_creaci = '".$_REQUEST["user"]."'  "; 
        }else {
            $mQuery = "SELECT CONCAT(b.cod_noveda,'-',b.nom_noveda) AS nom_noveda, a.fec_contro, a.obs_contro, a.cod_noveda
                     FROM ".BASE_DATOS.".tab_despac_contro a ,
                          ".BASE_DATOS.".tab_genera_noveda b
                    WHERE a.cod_noveda = b.cod_noveda AND
                          a.num_despac = '".$_REQUEST["despac"]."' AND 
                          a.cod_consec = '".$_REQUEST["consec"]."' ";
        }
 
        $consulta = new Consulta($mQuery, $this->conexion);
        $mData = $consulta -> ret_matrix('a');

        //lista las novedadesecho 
        $query = " SELECT cod_noveda, UPPER( CONCAT( CONVERT( nom_noveda USING utf8), 
                          '', if (nov_especi = '1', '(NE)', '' ), 
                          if( ind_alarma = 'S', '(GA)', '' ), 
                          if( ind_manala = '1', '(MA)', '' ),
                          if( ind_tiempo = '1', '(ST)', '' ) )) , 
                          ind_tiempo
                    FROM " . BASE_DATOS . ".tab_genera_noveda 
                    WHERE 1 = 1 AND ind_visibl = '1'";

        if ($datos_usuario["cod_perfil"] != COD_PERFIL_SUPERUSR && $datos_usuario["cod_perfil"] != COD_PERFIL_ADMINIST && $datos_usuario["cod_perfil"] != COD_PERFIL_SUPEFARO)
        {
            if( $datos_usuario["cod_perfil"]  != '689' )
                $query .=" AND cod_noveda !='" . CONS_NOVEDA_ACAEMP . "' ";
        }
        if ($transpor[0][2] == '1')
            $query .=" AND cod_noveda !='" . CONS_NOVEDA_ACAFAR . "' ";
        $query .=" ORDER BY 2 ASC";
        $consulta = new Consulta($query, $this->conexion);
        $novedades = $consulta->ret_matriz();

        if( $select )
        {
            $fil_noveda = array();

            for( $i = 0; $i < sizeof( $novedades ) ; $i++ )
            {
                for( $j = 0; $j < sizeof( $select ); $j++ )
                {
                    if( $novedades[$i][cod_noveda] == $select[$j][cod_noveda] )
                        $fil_noveda[] = $novedades[$i];
                }
            }

            $novedades = $fil_noveda;
        }

        echo '<script> 
        jQuery(function($) 
        { 
          var novedades = 
                          [';

                            if ($novedades)
                            {
                                echo "\"Ninguna\"";
                                foreach ($novedades as $row)
                                {
                                    echo ", \"$row[0]-" . htmlentities($row[1]) . " \"";
                                }
                            }

                            echo '];
                        
        
            $( "#novedadListID" ).autocomplete({
              source: novedades,
              delay: 100
            }).bind( "autocompleteselect", function(event, ui){ setNovedadNueva(ui);} );
            
            //$( "#novedadListID" ).bind( "autocompletechange", function(event, ui){alert("xxxxxx");} ); 
            
            });
      
        </script>';
        $mHtml  = '<div class="Style2DIV"><table>';
            $mHtml .= '<tr>';
                $mHtml .= '<td colspan="2" class="Style2DIV" align="center" ><span style="color: red;"><b>ALERTA: USTED VA A ACTUALIZAR EL SIGUIENTE REGISTRO.</b></span></td>';
                
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Actualizar Novedad:</td>';
                $mHtml .= '<td class="cellInfo1"><spam id="actaulNovedadID">'.$mData[0]["nom_noveda"].'</spam>
                             <input type="hidden" id="cod_novedaID" value="'.$mData[0]["cod_noveda"].'"/> 
                           </td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Nueva Novedad:</td>';
                $mHtml .= '<td class="cellInfo1"><input type="text" id="novedadListID" size="70"  />
                              <input type="hidden" id="cod_novedaNID" value="--"/> 
                           </td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Fecha Novedad:</td>';
                $mHtml .= '<td class="cellInfo1">'.$mData[0]["fec_contro"].' </td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Observaci�n Novedad:</td>';
                $mHtml .= '<td class="cellInfo1">
                            <textarea id="descripcionID" cols="60" rows="6">'.$mData[0]["obs_contro"].'</textarea>
                            <input type="hidden" id="old_descriID" value="'.$mData[0]["obs_contro"].'"/> 
                           </td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Motivo Actualizaci�n:</td>';
                $mHtml .= '<td class="cellInfo1">
                            <textarea id="obs_motivoID" cols="60" rows="6"></textarea>
                           </td>';
            $mHtml .= '</tr>';
        $mHtml .= '</table></div>';
        echo $mHtml;
    }

    function FormDeleteNoveda()
    {
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
        
        if($_REQUEST["table"] == '2') {
            $mQuery = "SELECT b.nom_noveda, a.fec_noveda AS fec_contro, a.des_noveda AS obs_contro, a.usr_creaci
                         FROM ".BASE_DATOS.".tab_despac_noveda a , 
                              ".BASE_DATOS.".tab_genera_noveda b
                        WHERE a.cod_noveda = b.cod_noveda AND
                              a.num_despac = '".$_REQUEST["despac"]."' AND 
                              a.cod_contro = '".$_REQUEST["puesto"]."' AND
                              a.cod_noveda = '".$_REQUEST["novedad"]."' AND
                              a.fec_noveda = '".$_REQUEST["date"]."' AND
                              a.usr_creaci = '".$_REQUEST["user"]."'  "; 
        }else {
            $mQuery = "SELECT b.nom_noveda, a.fec_contro, a.obs_contro, a.usr_creaci
                         FROM ".BASE_DATOS.".tab_despac_contro a ,
                              ".BASE_DATOS.".tab_genera_noveda b
                        WHERE a.cod_noveda = b.cod_noveda AND
                              a.num_despac = '".$_REQUEST["despac"]."' AND 
                              a.cod_consec = '".$_REQUEST["consec"]."' ";
        }

        $consulta = new Consulta($mQuery, $this->conexion);
        $mData = $consulta -> ret_matrix('a');

        $mHtml  = '<div class="Style2DIV"><table>';
            $mHtml .= '<tr>';
                $mHtml .= '<td colspan="2" class="Style2DIV" align="center" ><span style="color: red;"><b>ALERTA: USTED VA A ELIMINAR EL SIGUIENTE REGISTRO.</b></span></td>';
                
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Eliminar Novedad:</td>';
                $mHtml .= '<td class="cellInfo1">'.$mData[0]["nom_noveda"].' </td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Fecha Novedad:</td>';
                $mHtml .= '<td class="cellInfo1">'.$mData[0]["fec_contro"].' Usuario: '.$mData[0]["usr_creaci"].' </td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead">Observaci�n Novedad:</td>';
                $mHtml .= '<td class="cellInfo1">'.$mData[0]["obs_contro"].' </td>';
            $mHtml .= '</tr>';
        $mHtml .= '</table></div>';
        echo $mHtml;
    }    

    function DeleteNoveda()
    {
        $mFlag = true;
        # Inicia Transaccion --------------------------------------------------------------------
        $mStartTransaction = new Consulta("START TRANSACTION", $this->conexion);        
        #Elimina tab_despac_noveda                                
        if($_REQUEST["table"] == '2')
        {
            $mQuery = "SELECT num_despac, cod_contro, cod_rutasx, cod_noveda, fec_noveda 
                           FROM ".BASE_DATOS.".tab_despac_noveda 
                          WHERE num_despac = '".$_REQUEST["despac"]."' AND 
                                cod_contro = '".$_REQUEST["puesto"]."' AND
                                cod_noveda = '".$_REQUEST["novedad"]."' AND
                                fec_noveda = '".$_REQUEST["date"]."' AND
                                usr_creaci = '".$_REQUEST["user"]."'  ";
          $consulta = new Consulta($mQuery, $this->conexion);
          $mData = $consulta -> ret_matrix('a');

            if(sizeof($mFlag) >=1) 
            {
                $mDelete1 = " DELETE FROM ".BASE_DATOS.".tab_despac_pronov 
                               WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                     cod_contro = '".$mData[0]["cod_contro"]."' AND
                                     cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND
                                     cod_noveda = '".$mData[0]["cod_noveda"]."' AND
                                     fec_noveda = '".$mData[0]["fec_noveda"]."'
                                       ";
                $consulta = new Consulta($mDelete1, $this->conexion);
                

                $mDelete = "DELETE  FROM ".BASE_DATOS.".tab_despac_noveda 
                             WHERE  num_despac = '".$mData[0]["num_despac"]."' AND 
                                    cod_contro = '".$mData[0]["cod_contro"]."' AND
                                    cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND
                                    cod_noveda = '".$mData[0]["cod_noveda"]."' AND
                                    fec_noveda = '".$mData[0]["fec_noveda"]."' ";
                $consulta = new Consulta($mDelete, $this->conexion);
            }
            else 
                $mFlag = false;
        }


        if($_REQUEST["table"] == '1')
        {
            $mQuery = "SELECT num_despac, cod_contro, cod_rutasx, cod_consec, cod_noveda, fec_contro AS fec_noveda 
                       FROM ".BASE_DATOS.".tab_despac_contro 
                      WHERE num_despac = '".$_REQUEST["despac"]."' AND 
                            cod_consec = '".$_REQUEST["consec"]."'  ";
          
            $consulta = new Consulta($mQuery, $this->conexion);
            $mData = $consulta -> ret_matrix('a');
            if(sizeof($mFlag) >=1) 
            {
                $mDeleteDos = "DELETE FROM ".BASE_DATOS.".tab_despac_procon 
                              WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                    cod_contro = '".$mData[0]["cod_contro"]."' AND 
                                    cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND 
                                    cod_consec = '".$mData[0]["cod_consec"]."'   ";
                $consulta = new Consulta($mDeleteDos, $this->conexion);

                $mDelete = "DELETE FROM ".BASE_DATOS.".tab_despac_contro 
                              WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                    cod_consec = '".$mData[0]["cod_consec"]."'   ";
                $consulta = new Consulta($mDelete, $this->conexion);
            }
            else
                $mFlag = false;
        }

        if( $mFlag == true )
        {          
          #Elimina la asignacion del protocolo al usuario
          $mCondici = $_REQUEST["novedad"]=='242'?" AND fec_noved2 = '".$mData[0]["fec_noveda"]."' ":"AND fec_noveda = '".$mData[0]["fec_noveda"]."' ";
          $mDeleteProtocAsigna = " DELETE FROM ".BASE_DATOS.".tab_protoc_asigna 
                                    WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                          cod_contro = '".$mData[0]["cod_contro"]."' AND
                                          cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND
                                          cod_noveda = '".$mData[0]["cod_noveda"]."' ";
          $mDeleteProtocAsigna .= $mCondici;
          $consulta = new Consulta($mDeleteProtocAsigna, $this->conexion);

          $consulta = new Consulta("COMMIT", $this->conexion);
        }
        else {
          $consulta = new Consulta("ROLLBACK", $this->conexion);
        }

        $mMsg = $mFlag == true ? 'SE ELIMIN&Oacute; EL REGISTRO CON &Eacute;XITO' : 'NO SE ELIMIN&Oacute; EL REGISTRO CON &Eacute;XITO';
        $mHtml  = '<div class="Style2DIV"><table>';
            $mHtml .= '<tr>';
                $mHtml .= '<td colspan="2" class="Style2DIV" align="center" ><span style="color: red;"><b>ALERTA:'.$mMsg.'.</b></span></td>';                
            $mHtml .= '</tr>';             
        $mHtml .= '</table></div>';
        echo $mHtml;
    }

    function UpdateNoveda()
    {
      $mFlag = true;
      # Inicia Transaccion --------------------------------------------------------------------
      $mStartTransaction = new Consulta("START TRANSACTION", $this->conexion);        
      if($_REQUEST["table"] == '2')
      {
          $mQuery = "SELECT num_despac, cod_contro, cod_rutasx, cod_noveda, fec_noveda 
                       FROM ".BASE_DATOS.".tab_despac_noveda 
                      WHERE num_despac = '".$_REQUEST["despac"]."' AND 
                            cod_contro = '".$_REQUEST["puesto"]."' AND
                            cod_noveda = '".$_REQUEST["novedad"]."' AND
                            fec_noveda = '".$_REQUEST["date"]."' AND
                            usr_creaci = '".$_REQUEST["user"]."'  ";
           
          $consulta = new Consulta($mQuery, $this->conexion);
          $mData = $consulta -> ret_matrix('a');
          if(sizeof($mFlag) >=1) 
          {
            if(is_numeric($_REQUEST["nCod"]) && $_REQUEST["nCod"] != '--') {
              $mUpdt = " cod_noveda = '".$_REQUEST["nCod"]."' ";
              $mCodNovnew = $_REQUEST["nCod"];
            }
            else {
               $mUpdt = " cod_noveda = '".$_REQUEST["novedad"]."' ";
               $mCodNovnew = $_REQUEST["oCod"];
            }

            $mUdpdate = "UPDATE ".BASE_DATOS.".tab_despac_noveda 
                           SET ".($mUpdt != '' ? $mUpdt.',' : '' )." des_noveda = '".$_REQUEST["desc"]."' 
                         WHERE  num_despac = '".$mData[0]["num_despac"]."' AND 
                                cod_contro = '".$mData[0]["cod_contro"]."' AND
                                cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND
                                cod_noveda = '".$mData[0]["cod_noveda"]."' AND
                                fec_noveda = '".$mData[0]["fec_noveda"]."' ";
            $consulta = new Consulta($mUdpdate, $this->conexion);

            $mUpdte = " UPDATE ".BASE_DATOS.".tab_despac_pronov 
                             SET ".$mUpdt."  
                           WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                 cod_contro = '".$mData[0]["cod_contro"]."' AND
                                 cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND
                                 cod_noveda = '".$mData[0]["cod_noveda"]."' AND
                                 fec_noveda = '".$mData[0]["fec_noveda"]."' ";
            $consulta = new Consulta($mUpdte, $this->conexion);

            self::insertBitacoUpdNov( array("cod_novold" => $_REQUEST[oCod], "cod_novnew" => $mCodNovnew, "obs_novold" => $_REQUEST[obs_novold], 
                                          "obs_novnew" => $_REQUEST[desc], "obs_motivo" => $_REQUEST[obs_motivo], "num_despac" => $_REQUEST[despac]) );
          }
          else 
            $mFlag = false;
      }elseif($_REQUEST["table"] == '1')
      {
        $mQuery = "SELECT num_despac, cod_contro, cod_rutasx, cod_consec, cod_noveda, fec_contro AS fec_noveda 
                     FROM ".BASE_DATOS.".tab_despac_contro 
                    WHERE num_despac = '".$_REQUEST["despac"]."' AND 
                          cod_consec = '".$_REQUEST["consec"]."'  ";
        
        $consulta = new Consulta($mQuery, $this->conexion);
        $mData = $consulta -> ret_matrix('a');
        if(sizeof($mFlag) >=1) 
        {
          if(is_numeric($_REQUEST["nCod"]) && $_REQUEST["nCod"] != '--') {
            $mUpdt = " cod_noveda = '".$_REQUEST["nCod"]."' , ";
            $mCodNovnew = $_REQUEST["nCod"];
          }
          else {
            $mUpdt = " cod_noveda = '".$_REQUEST["novedad"]."' , ";
            $mCodNovnew = $_REQUEST["oCod"];
          }

          $mUpdte = "UPDATE ".BASE_DATOS.".tab_despac_contro 
                        SET ".$mUpdt." obs_contro = '".$_REQUEST["desc"]."'
                        WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                              cod_consec = '".$mData[0]["cod_consec"]."'   ";
          $consulta = new Consulta($mUpdte, $this->conexion);

          $mUdpdate = "UPDATE  ".BASE_DATOS.".tab_despac_procon 
                            SET ".$mUpdt." obs_protoc = '".$_REQUEST["desc"]."'
                          WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                cod_contro = '".$mData[0]["cod_contro"]."' AND 
                                cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND 
                                cod_consec = '".$mData[0]["cod_consec"]."'   ";
          $consulta = new Consulta($mUdpdate, $this->conexion);

          self::insertBitacoUpdNov( array("cod_novold" => $_REQUEST[oCod], "cod_novnew" => $mCodNovnew, "obs_novold" => $_REQUEST[obs_novold], 
                                          "obs_novnew" => $_REQUEST[desc], "obs_motivo" => $_REQUEST[obs_motivo], "num_despac" => $_REQUEST[despac]) );
        }
        else
          $mFlag = false;
      }

      if( $mFlag == true )
      {          
        #Elimina la asignacion del protocolo al usuario
        $mCondici = $_REQUEST["novedad"]=='242'?" AND fec_noved2 = '".$mData[0]["fec_noveda"]."' ":"AND fec_noveda = '".$mData[0]["fec_noveda"]."' ";
        if(is_numeric($_REQUEST["nCod"]) && $_REQUEST["nCod"] != '--') {
            $mUpdt = ' cod_noveda = "'.$_REQUEST["nCod"].'"  ';
        }
        else {
          $mUpdt = ' cod_noveda = "'.$_REQUEST["novedad"].'"  ';
        }
        
        $mUpdateProtocAsigna = " UPDATE ".BASE_DATOS.".tab_protoc_asigna 
                                     SET ".$mUpdt."
                                  WHERE num_despac = '".$mData[0]["num_despac"]."' AND 
                                        cod_contro = '".$mData[0]["cod_contro"]."' AND
                                        cod_rutasx = '".$mData[0]["cod_rutasx"]."' AND
                                        cod_noveda = '".$mData[0]["cod_noveda"]."' ";
        $mUpdateProtocAsigna .= $mCondici;
        $consulta = new Consulta($mUpdateProtocAsigna, $this->conexion);

        $consulta = new Consulta("COMMIT", $this->conexion);
      }else{
        $consulta = new Consulta("ROLLBACK", $this->conexion);
      }

      $mMsg = $mFlag == true ? 'SE ACTUALIZ&Oacute; EL REGISTRO CON &Eacute;XITO' : 'NO SE ACTUALIZ&Oacute; EL REGISTRO CON &Eacute;XITO';
      $mHtml  = '<div class="Style2DIV"><table>';
          $mHtml .= '<tr>';
              $mHtml .= '<td colspan="2" class="Style2DIV" align="center" ><span style="color: red;"><b>ALERTA:'.$mMsg.'.</b></span></td>';                
          $mHtml .= '</tr>';             
      $mHtml .= '</table></div>';
      echo $mHtml;
    }

    function DetallePcPadre()
    {
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

        $mArrayData = AjaxDespac::getDetallePcPadre($_REQUEST[cod_contro]);
        $mArrayTitu = array('C&oacute;digo', 'Descripci&oacute;n', 'Encargado', 'Tel&eacute;fono', 'Ciudad', 'Direcci&oacute;n Global', 'Sentido Vial', 'Dir. Sentido 1', 'Dir. Sentido 2');

        $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

        $mHtml .=   '<tr>';
        foreach ($mArrayTitu as $titu) {
            $mHtml .= '<th class="CellHead">'.$titu.'</th>';
        }
        $mHtml .=   '</tr>';

        foreach ($mArrayData as $row) {
            $mHtml .= '<tr>';
            foreach ($row as $value) {
                $mHtml .= $value != NULL ? '<td class="cellInfo">'.$value.'</td>' : '<td class="cellInfo">Sin Informaci&oacute;n</td>' ;
            }
            $mHtml .= '</tr>';
        }

        $mHtml .= '</table>';

        echo $mHtml;
    }

    /*! \fn: FormNewRuta
     *  \brief: Muestra las rutas opcionales para esa Transportadora
     *  \author: Ing. Fabian Salinas
     *  \date: 13/05/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return:
     */
    function FormNewRuta()
    {
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
        
        $mTranspTipser = getTransTipser( $this -> conexion, " AND a.cod_transp = '{$_REQUEST[cod_transp]}'" );

        $mMsg = '';
        if( $mTranspTipser[0][ind_camrut] == 0 ){
          $mMsg = 'La Transportadora no tiene habilitada la opci&oacute;n de Cambio de Plan de Ruta.';
        }else
        {
          $mSql = " SELECT a.cod_ciuori, a.cod_ciudes 
                      FROM ".BASE_DATOS.".tab_genera_rutasx a 
                     WHERE a.cod_rutasx = '$_REQUEST[cod_rutasx]' ";
          $mConsult = new Consulta( $mSql, $this -> conexion );
          $mCiudadesRuta = $mConsult -> ret_matrix('a');

          $mArrayData = AjaxDespac::getDataRuta( $_REQUEST[cod_transp], $_REQUEST[cod_contro], $mCiudadesRuta[0][cod_ciuori], $mCiudadesRuta[0][cod_ciudes], $_REQUEST[cod_rutasx] );
          $mArrayTitu = array('CODIGO', 'RUTA/VIA', 'SELECCION');

          if( $mArrayData != '' && $mArrayData != NULL ) 
          {
            #Pinta la tabla de las posibles rutas
            $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';

            $mHtml .=   '<tr>';
            foreach ($mArrayTitu as $titu) {
                $mHtml .= '<th class="CellHead">'.$titu.'</th>';
            }
            $mHtml .=   '</tr>';

            foreach ($mArrayData as $row) {
                $mHtml .= '<tr>';
                $mHtml .= '<td class="cellInfo">'.$row[cod_rutasx].'</td>' ;
                $mHtml .= '<td class="cellInfo">'.$row[nom_rutasx].'</td>' ;
                $mHtml .= '<td class="cellInfo" align="center"><input type="radio" id="cod_rutasxID" name="cod_rutasx" value="'.$row[cod_rutasx].'"></td>' ;
                $mHtml .= '</tr>';
            }

            $mHtml .=   '<tr><td colspan="3" align="center">';
            $mHtml .=       '<input class="crmButton small save" type="button" onclick="updRuta()" value="Guardar" name="BTupdRuta">';
            $mHtml .=   '</td></tr>';
            $mHtml .=   '<input type="hidden" id="dir_aplicaID" name="dir_aplica" value="'.$_REQUEST[standa].'" >';
            $mHtml .=   '<input type="hidden" id="num_despacID" name="num_despac" value="'.$_REQUEST[num_despac].'" >';
            $mHtml .=   '<input type="hidden" id="cod_controID" name="cod_contro" value="'.$_REQUEST[cod_contro].'" >';
            $mHtml .=   '<input type="hidden" id="cod_rutoldID" name="cod_rutold" value="'.$_REQUEST[cod_rutasx].'" >';
            $mHtml .= '</table>';
            echo $mHtml;
          }
          else
          {
            $mMsg = 'No se encontraron rutas alternas con las ciudades origen y destino para esta transportadora';
          }
        }

        if($mMsg != '')
        {
          $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0" border="0">';
          $mHtml .=   '<tr><td class="cellInfo">'.$mMsg.'</td></tr>';
          $mHtml .=   '<tr><td colspan="3" align="center">';
          $mHtml .=       '<input class="crmButton small save" type="button" onclick="LoadPopupJQ2(\'close\');" value="Salir" name="BTupdRuta">';
          $mHtml .=   '</td></tr>';
          $mHtml .= '</table>';
          echo $mHtml;
        }
    }

    /*! \fn: CambioRuta
     *  \brief: Cambia la Ruta del Despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 13/05/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return:
     */
    function CambioRuta()
    {
        $mRutasDespac = AjaxDespac::getRutasDespac( $_REQUEST[num_despac] );

        if( sizeof($mRutasDespac) < 2 )
        {
            $mRutcon = AjaxDespac::getRutcon( $_REQUEST[cod_rutold] );
            for($i=0; $i < sizeof($mRutcon); $i++ )
            {
                $mSql = "UPDATE ".BASE_DATOS.".tab_despac_seguim 
                            SET num_consec = '".($i+1)."' 
                          WHERE num_despac = '{$_REQUEST[num_despac]}' 
                            AND cod_rutasx = '{$_REQUEST[cod_rutold]}' 
                            AND cod_contro = '".$mRutcon[$i][cod_contro]."' ";
                $mUpdate = new Consulta($mSql, $this->conexion, "R");
            }
        }

        $mSql = " SELECT c.cod_contro 
                    FROM ".BASE_DATOS.".tab_despac_seguim b 
              INNER JOIN ".BASE_DATOS.".tab_genera_rutcon c 
                      ON b.cod_contro = c.cod_contro
                     AND b.cod_rutasx = c.cod_rutasx
              INNER JOIN ".BASE_DATOS.".tab_genera_contro d 
                      ON c.cod_contro = d.cod_contro 
                   WHERE b.num_despac = '".$_REQUEST[num_despac]."' 
                     AND b.ind_estado != '2'
                     AND c.ind_estado = '1' 
                     AND c.val_duraci >= (
                                            SELECT z.val_duraci 
                                              FROM ".BASE_DATOS.".tab_genera_rutcon z 
                                             WHERE z.cod_rutasx = '".$_REQUEST[cod_rutold]."' 
                                               AND z.cod_contro = '".$_REQUEST[cod_contro]."'
                                        )
                ORDER BY c.val_duraci ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $mArrayCodContro = $mConsult -> ret_matrix('i');

        $mCodControOld = '';
        foreach ($mArrayCodContro as $row) {
            $mCodControOld .= $mCodControOld == '' ? $row[0] : ", ".$row[0] ;
        }

        #Actualiza los puestos de contol actuales ind_estado=2 (inactivo por cambio de ruta)
        $mSql = "UPDATE ".BASE_DATOS.".tab_despac_seguim 
                    SET ind_estado = '2', 
                        usr_modifi = '".$_SESSION[datos_usuario][cod_usuari]."', 
                        fec_modifi = NOW() 
                  WHERE cod_rutasx = '".$_REQUEST[cod_rutold]."' 
                    AND num_despac = '".$_REQUEST[num_despac]."' 
                    AND cod_contro IN ( ".$mCodControOld ." ) ";
        $mUpdate = new Consulta($mSql, $this->conexion, "R");

        $mSql = " SELECT a.cod_contro, a.val_duraci 
                    FROM ".BASE_DATOS.".tab_genera_rutcon a 
                   WHERE a.ind_estado = '1' 
                     AND a.cod_rutasx = '".$_REQUEST[cod_rutnew]."' 
                     AND a.val_duraci >= (
                                            SELECT z.val_duraci 
                                              FROM ".BASE_DATOS.".tab_genera_rutcon z 
                                             WHERE z.cod_rutasx = '".$_REQUEST[cod_rutnew]."' 
                                               AND z.cod_contro = '".$_REQUEST[cod_contro]."'
                                         ) 
                ORDER BY a.val_duraci ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $mArrayCodContro = $mConsult -> ret_matrix('a');

        $mSql = "SELECT 1
                   FROM ".BASE_DATOS.".tab_despac_seguim a 
                  WHERE a.cod_rutasx = '".$_REQUEST[cod_rutnew]."' 
                    AND a.num_despac = '".$_REQUEST[num_despac]."' 
                  LIMIT 1 ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $mIndViejaRuta = $mConsult -> ret_arreglo();

        $mSql = "SELECT a.num_consec 
                   FROM ".BASE_DATOS.".tab_despac_seguim a 
                  WHERE a.num_despac = '{$_REQUEST[num_despac]}' 
                    AND a.cod_contro = '{$_REQUEST[cod_contro]}'
                    AND a.ind_estado != '2' ";

        $mSql = " SELECT a.fec_planea, a.fec_alarma, a.num_consec
                    FROM ".BASE_DATOS.".tab_despac_seguim a 
                   WHERE a.num_despac = '".$_REQUEST[num_despac]."' 
                     AND a.cod_contro = '".$_REQUEST[cod_contro]."' 
                     AND a.cod_rutasx = '".$_REQUEST[cod_rutold]."' ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $mFecPlaneaAct = $mConsult -> ret_arreglo();

        $mConsec = $mFecPlaneaAct[num_consec];

        if(!$mIndViejaRuta)
        {
            $mSql1 = "( '".$_REQUEST[num_despac]."', '".$_REQUEST[cod_contro]."', '".$_REQUEST[cod_rutnew]."', '".$mConsec."', '".$mFecPlaneaAct[fec_planea]."', '".$mFecPlaneaAct[fec_alarma]."', '1', '".$_SESSION[datos_usuario][cod_usuari]."', NOW() ), 
                     ";

            for ($i=1; $i < sizeof($mArrayCodContro); $i++) 
            { 
                $mConsec++;
                $mMin = $mArrayCodContro[$i][val_duraci] - $mArrayCodContro[($i-1)][val_duraci];
                $mMin = "+".$mMin." minute";
                $mFecPlaneaAct[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mMin, strtotime ( $mFecPlaneaAct[fec_planea] ) ) ) );
                $mFecPlaneaAct[fec_alarma] = date ( 'Y-m-d H:i:s', ( strtotime( $mMin, strtotime ( $mFecPlaneaAct[fec_alarma] ) ) ) );

                $mSql1 .= "( '".$_REQUEST[num_despac]."', '".$mArrayCodContro[$i][cod_contro]."', '".$_REQUEST[cod_rutnew]."', '".$mConsec."', '".$mFecPlaneaAct[fec_planea]."', '".$mFecPlaneaAct[fec_alarma]."', '1', '".$_SESSION[datos_usuario][cod_usuari]."', NOW() )";
                $mSql1 .= $i == (sizeof($mArrayCodContro) - 1) ? "" : ",
                     ";
            }

            #Inserta el nuevo plan de ruta para el despacho
            $mSql = " INSERT INTO ".BASE_DATOS.".tab_despac_seguim 
                                  (num_despac, cod_contro, cod_rutasx, num_consec, fec_planea, fec_alarma, ind_estado, usr_creaci, fec_creaci ) 
                           VALUES ".$mSql1.";  ";
            $mInsert = new Consulta($mSql, $this->conexion, "R");
        }
        else
        {
            foreach ($mArrayCodContro as $row) {
                $mSql = "UPDATE ".BASE_DATOS.".tab_despac_seguim 
                        SET ind_estado = '1', 
                            num_consec = '".$mConsec."', 
                            usr_modifi = '".$_SESSION[datos_usuario][cod_usuari]."', 
                            fec_modifi = NOW() 
                      WHERE cod_rutasx = '".$_REQUEST[cod_rutnew]."' 
                        AND num_despac = '".$_REQUEST[num_despac]."' 
                        AND cod_contro = '".$row[cod_contro]."' ";
                $mUpdate = new Consulta($mSql, $this->conexion, "R");
                $mConsec++;
            }
        }

        
        $mSql = "UPDATE ".BASE_DATOS.".tab_despac_vehige 
                    SET cod_rutasx = '".$_REQUEST[cod_rutnew]."', 
                        usr_modifi = '".$_SESSION[datos_usuario][cod_usuari]."', 
                        fec_modifi = NOW() 
                  WHERE num_despac = '".$_REQUEST[num_despac]."' 
                    AND ind_activo = 'S' ";
        $mUpdate = new Consulta($mSql, $this->conexion, "R");

        $mSql = "UPDATE ".BASE_DATOS.".tab_despac_despac 
                    SET cod_conult = '".$_REQUEST[cod_contro]."', 
                        usr_modifi = '".$_SESSION[datos_usuario][cod_usuari]."', 
                        fec_modifi = NOW() 
                  WHERE num_despac = '".$_REQUEST[num_despac]."' 
                    AND ind_anulad = 'A' ";
        $mUpdate = new Consulta($mSql, $this->conexion, "R");
    }

    function darLlegad()
    {
        $query = "UPDATE " . $base_datos . ".tab_despac_despac 
                   SET fec_llegad = NOW(),
                       obs_llegad = '" . $_REQUEST[obs_llegad] . "',
                       usr_modifi = '" . $_REQUEST[usuario] . "',
                       fec_modifi = NOW() 
                   WHERE num_despac = '" . $_REQUEST[despac] . "'";
        $update = new Consulta($query, $this->conexion, "BR");
         $update = new Consulta("COMMIT", $this->conexion);
    }

    function getDetallePcPadre($codContro)
    {
        $mSql = " SELECT a.cod_contro, a.nom_contro, a.nom_encarg, 
                             a.tel_contro, b.abr_ciudad, a.dir_contro, 
                             a.val_senvia, a.dir_senti1, a.dir_senti2 
                        FROM ".BASE_DATOS.".tab_genera_contro a 
                  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b 
                          ON a.cod_ciudad = b.cod_ciudad 
                       WHERE a.cod_contro = '$codContro' 
                         AND a.ind_pcpadr = '1' ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        return $mConsult -> ret_matrix('i');
    }
 
    /*! \fn: getDataRuta
     *  \brief: Trae las rutas para una transportadora
     *  \author: Ing. Fabian Salinas
     *  \date: 13/05/2015
     *  \date modified: dia/mes/año
     *  \param: cod_transp, cod_PuestoControl, cod_ciudadDestino, cod_RutaActual
     *  \return: Array Rutas
     */
    function getDataRuta($mCodTransp, $mCodContro, $mCodCiuori, $mCodCiudes, $mCodRutasx)
    {
        $mSql = "SELECT a.cod_rutasx, a.nom_rutasx
                   FROM ".BASE_DATOS.".tab_genera_rutasx a 
             INNER JOIN ".BASE_DATOS.".tab_genera_rutcon b 
                     ON a.cod_rutasx = b.cod_rutasx 
             INNER JOIN ".BASE_DATOS.".tab_genera_ruttra c 
                     ON b.cod_rutasx = c.cod_rutasx 
                  WHERE b.cod_contro = '".$mCodContro."' 
                    AND a.cod_ciuori = '".$mCodCiuori."' 
                    AND a.cod_ciudes = '".$mCodCiudes."' 
                    AND a.ind_estado = '1' 
                    AND c.cod_transp = '".$mCodTransp."' 
                    AND a.cod_rutasx != '".$mCodRutasx."'
               GROUP BY a.cod_rutasx ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        return $mConsult -> ret_matrix('a');
    }

    /*! \fn: getRutcon
     *  \brief: Trae los puestos de control y val_duraci de una ruta
     *  \author: Ing. Fabian Salinas
     *  \date: 11/06/2015
     *  \date modified: dia/mes/año
     *  \param: CodRuta
     *  \return: matriz
     */
    function getRutcon($mCodRutasx)
    {
        $mSql = "SELECT a.cod_contro, a.val_duraci 
                   FROM ".BASE_DATOS.".tab_genera_rutcon a 
                  WHERE a.cod_rutasx = '{$mCodRutasx}'
               ORDER BY a.val_duraci ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        return $mConsult -> ret_matrix('a');
    }

    /*! \fn: getRutasDespac
     *  \brief: Trae las rutas del despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 11/06/2015
     *  \date modified: dia/mes/año
     *  \param: NumDespac
     *  \return: matriz
     */
    function getRutasDespac($mNumDespac)
    {
        $mSql = "SELECT a.cod_rutasx 
                   FROM ".BASE_DATOS.".tab_despac_seguim a 
                  WHERE a.num_despac = '{$mNumDespac}' 
               GROUP BY a.cod_rutasx 
               ORDER BY a.cod_rutasx ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        return $mConsult -> ret_matrix('i');
    }

    /*! \fn: VerificarManifiesto
     *  \brief: verifica que un manifiesto no este registrado en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 01/10/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: true si no existe manifiesto false si existe
     */
    function VerificarManifiesto(){
        $mani = $_REQUEST['manifiesto'];
        $transp = $_REQUEST['transp'];
        $mSql = "SELECT a.cod_manifi FROM ".BASE_DATOS.".tab_despac_despac a 
                        INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON b.num_despac = a.num_despac 
                        WHERE a.cod_manifi = '$mani' AND b.cod_transp = '$transp'";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $datos = $mConsult -> ret_matrix('i');
        if($datos){
           echo false;
        }else{
            echo true;
        }
    }

    /*! \fn: insertBitacoUpdNov
     *  \brief: Inserta en tab_bitaco_updnov
     *  \author: Ing. Fabian Salinas
     *  \date: 02/03/2016
     *  \date modified: dd/mm/aaaa
     *  \param: mData  array  Array con los datos a insertar
     *  \return: 
     */
    private function insertBitacoUpdNov( $mData = null ){
      $mSql = "INSERT INTO ".BASE_DATOS.".tab_bitaco_actnov
                  ( cod_novold, cod_novnew, obs_novold, 
                    obs_novnew, obs_motivo, num_despac, 
                    usr_creaci, fec_creaci 
                  )
                VALUES 
                  ( '$mData[cod_novold]', '$mData[cod_novnew]', '$mData[obs_novold]', 
                    '$mData[obs_novnew]', '$mData[obs_motivo]', '$mData[num_despac]', 
                    '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() 
                  )
              ";
      $consulta = new Consulta($mSql, $this->conexion);
    }

    public function getDataMapaDespac($paramsJson){
       
        $data = [];
        @include_once( "../despac/DespachosNew.inc" );
        $listado_prin = new DespachosNew($_REQUEST[cod_servic], 2, $this->cod_aplica, $this->conexion);
        $mValidaMapa = $listado_prin->validaDivMapas($paramsJson["num_despac"]);
        
        if( $mValidaMapa['ind_estado'] == 1){
            echo $listado_prin->divPaintMapas($mValidaMapa['data']);
        }else{
            echo '';
        }
    }
}
//FIN CLASE PROC_DESPAC

$proceso = new AjaxDespac($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);

?>
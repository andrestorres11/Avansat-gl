<?php

class ModuloComunicaciones
{
  var $conexion,
      $usuario,
      $cod_aplica;
  public function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  private function principal()
  {
   
    /* echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/asi_comuni_comuni.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";


    
    $formulario = new Formulario ( "index.php", "post", "LISTAR PARAMETRIZAR USUARIO", "formulario" );
		echo "<td>";
		$formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario->oculto("transp\" id=\"transpID\"", '', 0);
    #$formulario->oculto("listar\" id=\"listarID\"", 'listar', 0);
		echo "<td></tr>";
		echo "<tr>";
		echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
		echo "<tr>";
		echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='8' >Seleccion de Transportadora</td>";
		echo "</tr>";
		echo "<tr>";
    $readonly = ''; 
    $filter = $this -> VerifyTranspor();
    if( sizeof( $filter ) > 0 )
    { 
      $TRANSP = $this -> getTransp( $filter['clv_filtro'] );
      $readonly = 'readonly="readonly" value="'.$TRANSP[0][0].' - '.$TRANSP[0][1].'"';
    }
    
		echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
				Nit </td>";
		echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='1' >
				<input class='campo_texto' type='text' size='35' name='cod_transp2' id='cod_transp2ID' ".$readonly."/></td>";
        echo "<td class='celda_titulo' style='padding:4px;' width='50%' >Nombre Empresa:</td>";
        echo "<td class='celda_titulo' style='padding:4px;' width='50%' ><label id='nom_empresID'></label></td>";
		echo "</tr>";
		echo "<tr> 
            <td  class='celda_titulo' style='padding:4px;' align='left' colspan='9' >         
              Novedad:         ".ModuloComunicaciones::getNovedades('cod_noveda')."  
              Tipo Correo:     ".ModuloComunicaciones::getTipCorreo('tip_correo')."  
            </td>
          </tr>
          <tr>
            <td class='celda_titulo' style='padding:4px;' align='left' colspan='9'>      
              Destino:         ".ModuloComunicaciones::getLisCiudad('cod_ciudes', 'd')."
              Origen:          ".ModuloComunicaciones::getLisCiudad('cod_ciuori', 'o')."
              Producto:        ".ModuloComunicaciones::getLisProduc('cod_produc')."
              Tipo Operacion:  ".ModuloComunicaciones::getLisOperac('cod_operac')."
    
            </td>
          </tr> 
          <tr>
            <td class='celda_titulo' style='padding:4px;' align='left' colspan='9'>                           
              Tipo Transporte: ".ModuloComunicaciones::getLisTiptra('cod_tiptra')."
              Zona:            ".ModuloComunicaciones::getLisZonasx('cod_zonasx')."
              Canal:           ".ModuloComunicaciones::getLisCanalx('cod_canalx')."
              Depositos:       ".ModuloComunicaciones::getLisDeposi('cod_deposi')."
          </td>
        </tr>";

    echo "<td  class='celda_etiqueta' style='padding:4px;' align='center' colspan='9' >				 
            <input class='crmButton small save' style='cursor:pointer;' type='button' value='Similres' onclick='ListarDuplicados();'/></td>";
		echo "</tr>";
		echo "</table></td>";
		$formulario -> cerrar();

    */

    echo $this->GridStyle();
    #<Arma HTML>
    $mHtml = new Formlib(2);

    $mHtml->SetJs("min");
    $mHtml->SetJs("jquery");
    $mHtml->SetJs("es");
    $mHtml->SetJs("time");
    $mHtml->SetJs("time");
    $mHtml->SetJs("asi_comuni_comuni");
    $mHtml->SetCssJq("jquery");
    
    $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
    $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID"));
    $mHtml->Hidden(array( "name" => "cod_nomUsuario", "id" => "cod_nomUsuarioID"));
    $mHtml->table('tr');
        $mHtml->SetBody('<td>');
            $mHtml->SetBody('<div id="contentUser" class="ui-tabs ui-widget ui-widget-content ui-corner-all">');
            //$mHtml->OpenDiv("id:contentUser; class:accordion");
               /* $mHtml->SetBody("<h3 style='padding:6px;' class='ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top'><center>FILTROS</center></h3>");
                $mHtml->OpenDiv("id:sec1ID; class:ui-tabs-panel; class:ui-widget-content, class:ui-corner-bottom");
                    #contenido del acordeon
                    $mHtml->Table("tr");
                        $mHtml->Row();
                            $mHtml->Label( "SELECCIONES PARAMETROS DE BUSQUEDA",  array("align"=>"center", "class"=>"CellHead", "colspan"=>"2") );
                        $mHtml->CloseRow();

                        $mHtml->Row();
                           $mHtml->Label( "Nit/ Nombre Transportadora:",  array("align"=>"right", "class"=>"celda_info") );
                           $mHtml->Input(array("name" => "nom_transp", "id" => "nom_transpID", "width" => "100%","onkeyup"=>"getNomTrans()"));
                        $mHtml->CloseRow();
                    $mHtml->CloseTable("tr");
                    #fin del contenido del acordeon
                $mHtml->CloseDiv();*/

                $mHtml->SetBody("<h3 style='padding:6px;' class='ui-accordion-header ui-helper-reset ui-state-default ui-state-active ui-corner-top'><center>LISTA DE ASIGNACION</center></h3>");
                $mHtml->OpenDiv("id:sec2ID");
                    //contenido del acordeon
                    $mHtml->OpenDiv("id:tabs");
                        $mHtml->SetBody("<ul>
                                            <li><a href='#tabUsuario'>USUARIO</a></li>
                                            <li><a href='#tabCaracteristicas'>CARACTERISTICAS DESPACHO</a></li>
                                        </ul>");
                        $mHtml->OpenDiv("id:tabUsuario");
                            $mHtml->OpenDiv("id:formUsuarioID");
                                $mHtml->Table("tr");
                                    $mHtml->Row();
                                        $mHtml->Label( "*Usuario/ Nombre:",  array("align"=>"right", "class"=>"celda_titulo") );
                                        $mHtml->Input(array("name" => "nom_usuario", "id" => "nom_usuarioID", "width" => "100%","onkeyup"=>"getNomUsuario()"));
                                        $mHtml->Label( "Contacto en e-mail PARA",  array("align"=>"right", "class"=>"celda_titulo") );
                                        $mHtml->CheckBox(array("name" => "para", "id"=>"paraID", "checked"=>"true", "width" => "25%"));
                                        $mHtml->Label( "Contacto en e-mail COPIA",  array("align"=>"right", "class"=>"celda_titulo") );
                                        $mHtml->CheckBox(array("name" => "copia", "id"=>"copiaID", "checked"=>"true", "width" => "25%"));
                                    $mHtml->CloseRow();
                                    $mHtml->Row();
                                        $mHtml->Label( "Datos de Resultados:",  array("align"=>"center", "class"=>"", "colspan"=>"2") );
                                        $mHtml->Button( array("value"=>"EXCEL", "id"=>"ExportExcelUsuariosID","name"=>"ExportExcelUsuarios", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"4","onclick"=>"ExportExcelUs()", "disabled"=>"true","end"=>"1") );
                                        $mHtml->Button( array("value"=>"BUSCAR", "id"=>"buscarUsuario", "name"=>"buscarUsuario", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"8","onclick"=>"ListatAsignacion(0)","end"=>"1") );
                                    $mHtml->CloseRow();
                                $mHtml->CloseTable("tr");
                            $mHtml->CloseDiv();
                                
                            //contenido respuesta ajax
                            $mHtml->OpenDiv("id:resultadoUsuarioID");
                                        
                            $mHtml->CloseDiv();
        
                        $mHtml->CloseDiv();

                        $mHtml->OpenDiv("id:tabCaracteristicas");
                            $mHtml->OpenDiv("id:formUsuarioCaract");
                                $mHtml->Table("tr");
                                    $mHtml->Label( "*Novedad:",  array("align"=>"right", "class"=>"celda_titulo","colspan"=>"1") );
                                    $mHtml->Select2 ($this->getNovedades(),  array("name" => "SeltNovedad", "width" => "25%","colspan"=>"6","end"=>"1") );
                                    $mHtml->Label( "*Tipo Correo:",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getTipCorreo(),  array("name" => "SeltTipCorreo", "width" => "25%") );
                                    $mHtml->Label( "*Tipo Operacion:",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisOperac(),  array("name" => "SeltTipOperacion", "width" => "25%","end"=>"1") );
                                    $mHtml->Label( "*Origen:",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisCiudad(),  array("name" => "SeltTipOrigen", "width" => "25%") );
                                    $mHtml->Label( "Destino:",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisCiudad(),  array("name" => "SeltTipDestino", "width" => "25%","end"=>"1") );
                                    $mHtml->Label( "*Producto",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisProduc(),  array("name" => "SeltTipProducto", "width" => "25%") );
                                    $mHtml->Label( "Zona:",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisZonasx(),  array("name" => "SeltTipZona", "width" => "25%","end"=>"1") );
                                    $mHtml->Label( "Canal",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisCanalx(),  array("name" => "SeltTipCanal", "width" => "25%") );
                                    $mHtml->Label( "Deposito",  array("align"=>"right", "class"=>"celda_titulo") );
                                    $mHtml->Select2 ($this->getLisDeposi(),  array("name" => "SeltTipDeposito", "width" => "25%","end"=>"1") );
                                    $mHtml->Label( "Datos de Resultados:",  array("align"=>"center", "class"=>"", "colspan"=>"2") );
                                    $mHtml->Button( array("value"=>"EXCEL", "id"=>"ExportExcelCaraID","name"=>"ExportExcelCaraID", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"4","onclick"=>"ExportExcelCara()", "disabled"=>"true","end"=>"1") );
                                    $mHtml->Button( array("value"=>"BUSCAR", "id"=>"buscarCaracteristicas","name"=>"buscarCaracteristicas", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"6","onclick"=>"ListatAsignacion(1)") );
                                $mHtml->CloseTable("tr");
                            $mHtml->CloseDiv();
                            $mHtml->OpenDiv("id:resultUsuarioCaract");
                                        
                            $mHtml->CloseDiv();
                        $mHtml->CloseDiv();
                    $mHtml->CloseDiv();
                    //fin del contenido del acordeon
                $mHtml->CloseDiv();
            $mHtml->SetBody('</div>');   
            //$mHtml->CloseDiv();
        $mHtml->CloseRow('td');
    $mHtml->CloseTable('tr'); 

    $mHtml->SetBody('<script>
                      $(".accordion").accordion({
                          heightStyle : "content",
                          collapsible : false, 
                          icons: { "header" : "ui-icon-circle-arrow-e", "activeHeader": "ui-icon-circle-arrow-s" }
                      }).click(function(){
                          $("body").removeAttr("class");
                      });

                      $(function() {
                        $("#tabs").tabs();
                      } );

                      $(function() {
                        $("#sec1ID").css({"height":"auto"});

                      } );


                    </script>');
    #</Arma HTML>
    echo $mHtml->MakeHtml();
    echo "<center><div id='resultID'></div></center>";
    echo "<center><div id='PopUpID'></div></center>";
  }

  function GridStyle()
    {
        echo "<style>
                .cellth-ltb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .cellth-lb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                }
                .cellth-b{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                }
                .cellth-tb{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-ltb{
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-tb{
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-lb{
                     border-bottom: 1px solid #999999; 
                     border-left: 1px solid #999999;
                }
                .celltd-l{
                     border-left: 1px solid #999999;
                }
                .fontbold{
                    font-weight: bold;
                }
                .divGrilla{
                    margin: 0;
                    padding: 0;
                    border: none;
                    border-top: 1px solid #999999;
                    border-bottom: 1px solid #999999;
                }

                .CellHead {
                    background-color: #35650f;
                    color: #ffffff;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 4px;
                }
                .cellInfo1 {
                    background-color: #ebf8e2;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 2px;
                }
                .campo_texto {
                    background-color: #ffffff;
                    border: 1px solid #bababa;
                    color: #000000;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding-left: 5px;
                }
              </style>";
    }

  function getNovedades($mCodNivel = NULL)
  {
      $mSelect = '(
                    SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                    FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND nom_noveda LIKE "%NER /%" 
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl ="1" AND 
                         ( nom_noveda LIKE "%NEC /%" OR  nom_noveda LIKE "%NICC /%" )
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND 
                          nom_noveda LIKE "%NED /%"
                  ) ';


      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_NOVEDA = $consulta -> ret_matrix('i');
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $novedad=array_merge($inicio,$_NOVEDA);
      return $novedad;
  }

  function getTipCorreo($mCodNivel = NULL)
  {
    $mSelect = '(
                  SELECT "P" AS cod_correc, "Primario" AS nom_correc                     
                )
                UNION ALL 
                (
                  SELECT "s" AS cod_correc, "Segundario" AS nom_correc
                ) ';


      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_CORREO = $consulta -> ret_matrix('i');
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_CORREO=array_merge($inicio,$_CORREO);
      return $_CORREO;
      
  }

  function getLisCiudad( $mCodNivel = NULL, $mList = 'o')
  {
    if($mList == 'o')
    {
      $mSelect = "SELECT a.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) AS nom_ciudad
                  FROM ".BASE_DATOS.".tab_transp_origen a,
                       ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                   WHERE a.cod_ciudad = b.cod_ciudad AND
                       b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       a.cod_transp = '860068121'
                   GROUP BY 1 ORDER BY 2";
    }
    else
    {
      $mSelect = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) AS nom_ciudad
                  FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                 WHERE b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1'  
                 GROUP BY 1 ORDER BY 2 ";
    }
    

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ORIGEN = $consulta -> ret_matriz('i');
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_ORIGEN=array_merge($inicio,$_ORIGEN);
      return $_ORIGEN;

  }

  function getLisProduc( $mCodNivel = NULL)
  {
            // Producto
      $mSelect = "SELECT cod_produc, nom_produc FROM ".BASE_DATOS.".tab_genera_produc WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_PRODUC = $consulta -> ret_matrix("i");
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_PRODUC=array_merge($inicio,$_PRODUC);
      return $_PRODUC;
  }

  function getLisOperac( $mCodNivel = NULL)
  {
     // Tipo de Operacion
      $mSelect = "SELECT cod_tipdes, nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes WHERE 1 = 1 ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_TIPDES = $consulta -> ret_matrix("i");
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_TIPDES=array_merge($inicio,$_TIPDES);
      return $_TIPDES;
  } 

  function getLisTiptra( $mCodNivel = NULL)
  {
      // Tipo Transportadora
      $mSelect = "SELECT cod_tiptra, nom_tiptra FROM ".BASE_DATOS.".tab_genera_tiptra WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_TIPTRA = $consulta -> ret_matrix("i");
    
      $mHtml = '<select id="'.$mCodNivel.'ID" name="'.$mCodNivel.'">';
        $mHtml .= '<option value="">--</option>';
        foreach ($_TIPTRA as $mKey => $mValue) {
          $mHtml .= '<option value="'.$mValue["cod_tiptra"].'">'.$mValue["nom_tiptra"].'</option>';
        }
      $mHtml .= '</select>';

      return $mHtml;
  }
  
  function getLisZonasx( $mCodNivel = NULL)
  {
      // Zona
      $mSelect = "SELECT cod_zonaxx, nom_zonaxx FROM ".BASE_DATOS.".tab_genera_zonasx WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_ZONASX = $consulta -> ret_matrix("i");
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_ZONASX=array_merge($inicio,$_ZONASX);
      return $_ZONASX;
  }

  function getLisCanalx( $mCodNivel = NULL)
  {
      // Canal
      $mSelect = "SELECT con_consec, nom_canalx FROM ".BASE_DATOS.".tab_genera_canalx WHERE ind_estado = '1' ORDER BY 2";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_CANALX = $consulta -> ret_matrix("i");
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_CANALX=array_merge($inicio,$_CANALX);
      return $_CANALX;
  } 
  function getLisDeposi ( $mCodNivel = NULL)
  {
      $mSelect = "SELECT cod_deposi, nom_deposi FROM ".BASE_DATOS.".tab_genera_deposi WHERE ind_estado = '1' ORDER BY 2";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_DEPOSI = $consulta -> ret_matrix("i");
      $inicio[0][0]=0;
      $inicio[0][1]='-';
      $_DEPOSI=array_merge($inicio,$_DEPOSI);
      return $_DEPOSI;
  }


  
  private function getTransp( $cod_transp )
  {
    $mSql = "SELECT cod_tercer, UPPER(abr_tercer) AS nom_tercer FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$cod_transp."' LIMIT 1";
    $consulta = new Consulta( $mSql, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  private function VerifyTranspor()
  {
    if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) {
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
      $datos_filtro = $filtro -> retornar();
      endif;
    }
    else { 
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
      $datos_filtro = $filtro -> retornar();
      endif;
    }
    return $datos_filtro;
  }

}

$centro = new ModuloComunicaciones( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>
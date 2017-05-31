<?php
/*! \file: inf_diario_trazab.php
 *  \brief: Informes > Trazabilidad diaria 
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: 
 *  \bug: 
 *  \warning: 
 */

class Informe
{
  var $conexion = NULL;
  var $cod_aplica = NULL;
 
  function __construct( $conexion, $cod_aplica )
  {
    $this -> conexion = $conexion;
    $this -> cod_aplica = $cod_aplica;
    switch( $_REQUEST[option] )
    {     
      case "buscar":
        $this -> Buscar();
      break;
      
      case "xls":
        $this -> Excel();
      break;
      
      default:
        $this -> Filtros();
      break;
    }
  }

  /*! \fn: Filtros
   *  \brief: Formulario con filtros de busqueda
   *  \author: 
   *  \date:  dd/mm/aaaa
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: 
   *  \return: 
   */  
  function Filtros()
  {
    echo "<link rel='stylesheet' href='../sate_standa/estilos/list.css' type='text/css'>";
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_diario_trazab.js\"></script>\n";
    
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		
		echo '
          <script>
          $(function() 
          {
            $.mask.definitions["A"]="[12]";
            $.mask.definitions["M"]="[01]";
              $.mask.definitions["D"]="[0123]";
            
            $.mask.definitions["H"]="[012]";
              $.mask.definitions["N"]="[012345]";
              $.mask.definitions["n"]="[0123456789]";
            
            $( "#fec_incialID" ).mask("Annn-Mn-Dn");
            $( "#fec_incialID" ).datepicker();
            
            $( "#fec_finaliID" ).mask("Annn-Mn-Dn");
            $( "#fec_finaliID" ).datepicker();
            
            });
            
            function Validate()
            {
              var fec_ini = $("#fec_incialID").val();
              var fec_fin = $("#fec_finaliID").val();
              if( fec_ini != "" && fec_fin != "" )
              {
                if( fec_ini > fec_fin )
                {
                  alert("La fecha inicial no deebe ser mayor a la fecha final");
                  $("#fec_incialID").focus();
                  return false;
                }
                else
                {
                  $("#form_itemID").submit();
                }
              }
              else
              {
                $("#form_itemID").submit();
              }
            }
          </script>';
    
    $formulario = new Formulario ("index.php","post","Informe de trazabilidad diaria","form_item\" id=\"form_itemID");
    
    $formulario -> nueva_tabla();
    $formulario->linea("Filtros de Busqueda", 1, "t2");
    
    $agencias = $this->getAgencias($_POST[cod_transp]);
    
    $formulario -> nueva_tabla();
    $formulario -> lista ("Transportador: ","cod_transp\" onChange=\"SelectTrasnp()\" ",$this->getTranspor(),0,$_POST[cod_transp] );
    $formulario -> lista ("Agencia: ","cod_agenci",$agencias,1,$_POST[cod_agenci] );
    $formulario -> lista ("Origen: ","cod_ciuori",$this -> getOrigen(),0,$_POST[cod_ciuori] );
    $formulario -> lista ("Destino: ","cod_ciudes",$this -> getDestino(),1,$_POST[cod_ciudes] );
    $formulario -> lista ("Tipo Despacho: ","cod_tipdes",$this -> getTipdes(),0,$_POST[cod_tipdes] );
    $formulario -> lista ("Generador: ","cod_genera",$this -> getGenera($_POST[cod_transp]),1,$_POST[cod_genera] );
    $formulario -> texto ( "Fecha Inicio:", "text", "fec_incial\" id=\"fec_incialID", 0, 10, 10, "", $_POST[fec_incial], "", "", NULL);
		$formulario -> texto ( "Fecha Final:", "text", "fec_finali\" id=\"fec_finaliID", 0, 10, 10, "", $_POST[fec_finali], "", "", NULL);	
    
   
    $formulario -> nueva_tabla();
		$formulario -> botoni( "Buscar", "Validate()", 0 );
    $formulario -> oculto("option","buscar",0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);

    $formulario -> cerrar(); // Cierra el formulario    
  } 
  
  /*! \fn: getAgencias
   *  \brief: Trae la agencia
   *  \author: 
   *  \date:  dd/mm/aaaa
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: cod_trasnp  Integer  Codigo de la Transportadora
   *  \return: Matriz
   */
  function getAgencias($cod_trasnp)
  {
    $select = "SELECT a.cod_agenci, UPPER(a.nom_agenci)
          FROM  ".BASE_DATOS.".tab_genera_agenci a,
                ".BASE_DATOS.".tab_transp_agenci b
         WHERE  a.cod_agenci = b.cod_agenci AND
                b.cod_transp = '$cod_trasnp'
         ORDER BY 2 ";
    
    $select = new Consulta( $select, $this -> conexion );
    $select = array_merge( array( array( "", "---" ) ), 
    $select -> ret_matriz( "i" ) );
    return $select;
  }
  
  /*! \fn: Excel
   *  \brief: Genera Excel
   *  \author: 
   *  \date:  dd/mm/aaaa
   *  \date modified: dd/mm/aaaa
   *  \modified by: 
   *  \param: 
   *  \return: 
   */ 
  function Excel()
  {
    session_start();
    ini_set('memory_limit', '512M');
    $archivo = "Informe_trazabilidad_diaria_".date("Y_m_d").".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');

    //ob_start("ob_gzhandler");
    echo $HTML = $_SESSION[html];
    //ob_end_flush();
  }
  
  function Select( $name, $options = NULL, $value = "", $onchage = "" )
  {
    $html .= "<select name='$name' id='$name' class='itemSelect' onchange='$onchage'  >";
    
    if( $options )
    foreach( $options as $option  )
    {
      if( $value == $option[0] ) $sel = " selected "; 
      $html .= "<option value='$option[0]' $sel >".htmlentities( $option[1] )."</option>";      
      $sel =  "";
    }
    $html .= "</select>";
    
    return $html;
  }
    
  /*! \fn: getInforme
   *  \brief: Trae la data resultadante del informe
   *  \author: 
   *  \date:  dd/mm/aaaa
   *  \date modified: 08/01/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return: Matriz
   */
  function getInforme()
  {
    $datos_usuario =  $_SESSION[datos_usuario];
    $anidado = "( SELECT MAX( aa.fec_alarma ) 
            FROM ".BASE_DATOS.".tab_despac_seguim aa
            WHERE aa.num_despac = e.num_despac )";
            
    $query = "SELECT a.num_despac, 
                     a.fec_salida, 
                     UPPER((SELECT a1.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a1 WHERE a1.cod_ciudad = a.cod_ciuori)) AS origen,
	 								   UPPER((SELECT nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = a.cod_ciudes)) AS destin, 
                     IF( d.fec_llegpl IS NULL ,'0',d.fec_llegpl) AS fec_llegpl,
                     f.abr_tercer, 
                     d.num_placax,
                     IF( d.nom_conduc IS NOT NULL, d.nom_conduc, e.abr_tercer) AS abr_tercer,
                     IF(DATEDIFF(d.fec_llegpl , a.fec_salida) IS NULL ,'N/A',DATEDIFF(d.fec_llegpl , a.fec_salida)),
                     IF(DATEDIFF( NOW(), d.fec_llegpl) IS NULL ,'N/A',DATEDIFF(NOW() , d.fec_llegpl)),
                     UPPER((SELECT nom_tercer FROM tab_tercer_tercer WHERE d.cod_transp = cod_tercer)) AS nom_tercer,
										 o.num_solici, o.num_pedido,
                     a.fec_citcar, a.hor_citcar 
              FROM " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e,
                   " . BASE_DATOS . ".tab_tercer_tercer f,
                   " . BASE_DATOS . ".tab_despac_despac a
         LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n  
                ON n.cod_tercer = a.cod_asegur
         LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext o 
        				ON a.num_despac = o.num_despac 
      /*   LEFT JOIN " . BASE_DATOS . ".tab_despac_corona p 
                ON a.num_despac = p.num_dessat */
             WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                   d.cod_conduc = e.cod_tercer AND
                   d.cod_transp = f.cod_tercer AND
                   
                   a.fec_salida Is Not Null AND
                   a.fec_llegad IS NULL AND
                   a.ind_anulad = 'R' ";
    
    if( $_POST[cod_agenci] ) $query .= " AND d.cod_agenci = '$_POST[cod_agenci]' ";
    
    if( $_POST[cod_transp] ) $query .= " AND d.cod_transp = '$_POST[cod_transp]' ";    
    
    if( $_POST[cod_ciuori] ) $query .= " AND a.cod_ciuori = '$_POST[cod_ciuori]' ";
    
    if( $_POST[cod_ciudes] ) $query .= " AND a.cod_ciudes = '$_POST[cod_ciudes]' ";
    
    if( $_POST[cod_genera] ) $query .= " AND a.cod_client = '$_POST[cod_genera]' ";
    
    if( $_POST[cod_tipdes] ) $query .= " AND a.cod_tipdes = '$_POST[cod_tipdes]' ";
    
    if( $_POST[fec_incial] != '' && $_POST[fec_finali] != ''  ) 
      $query .= " AND a.fec_despac BETWEEN '".$_POST[fec_incial]." 00:00:00' AND '".$_POST[fec_finali]." 23:59:59' ";
    
    echo "<div style='display:none;'>$query</div>";
    
	if ($datos_usuario["cod_perfil"] == "")
    {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion))
      {
          $datos_filtro = $filtro->retornar();
          $query .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      } 
    }
    else
    {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion))
      {
          $datos_filtro = $filtro->retornar();
          $query .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
    }
	
    $query .= " GROUP BY 1 ORDER BY b.fec_alarma,1 " ;
    
    //$_SESSION[html] = $query;
    $select = new Consulta( $query, $this -> conexion );
    $select = $select -> ret_matriz();
    

    return $select;
  }
  
  /*! \fn: Buscar
   *  \brief: Imprime resultado del informe
   *  \author: 
   *  \date: 
   *  \date modified: 08/01/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return: 
   */
  function Buscar()
  { 
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_diario_trazab.js\"></script>\n";
    $form = new Formulario ("index.php","post","Informacion del Despacho","form_item"); 
    $informe = $this -> getInforme(); 
    
    $html  = "<style type='text/css' >";
    $html .= "  
        .cellHead
        {
          text-align: center;
          padding:3p 10px;
          border-top:1px solid #FFF;  
          border-left:1px solid #FFF; 
          border-right:1px solid #DDD;
          border-bottom:1px solid #DDD;
          font-weight: bold;
        }
        
        .cellInfo
        {
          padding:3p 10px;
          border-top:1px solid #FFF;  
          border-left:1px solid #FFF; 
          border-right:1px solid #DDD;
          border-bottom:1px solid #DDD;           
        }";
    $html .= "</style>";
    
    $html .= "<table>";
      $html .= "<tr>";
        //$htmlx .="<td><a href ='index.php?window=central&cod_servic=$_REQUEST[cod_servic]&option=xls' >[ Excel ]</a></td>";
        $html .="<td><a href ='../".DIR_APLICA_CENTRAL."/export/exp_diario_trazab.php?central=".NOM_URL_APLICA."' >[ Excel ]</a></td>";
      $html .= "</tr>";
    $html .= "</table>";
   
    
    $Hoy = $this -> toFecha( date( "Y-m-d" ) );
    $html .= "<table cellpadding='0' cellspacing='0' width='100%' border='0' >";    
    $html .= "<tr>";
    $html .= "<td class=celda_titulo colspan=17 style='text-align:left' >Fecha: ".$Hoy[0].".</td>"; 
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td class=celda_titulo colspan=17 style='text-align:left' >Hora: ".date( "h:i A" ).".</td>";  
    $html .= "</tr>";
    $html .= "<tr>";
    
    $html .= "<td class=celda_titulo colspan=17 >Se Encontraron ".sizeof( $informe )." Manifiestos.</td>";  
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td class=celda_titulo rowspan=2 >#</td>";
    $html .= "<td class=celda_titulo rowspan=2 >N° Documento</td>";
    $html .= "<td class=celda_titulo rowspan=2 >N° Transportadora</td>";
    $html .= "<td class=celda_titulo colspan=2 >Fecha y Hora de Salida</td>";
    $html .= "<td class=celda_titulo colspan=2 >Fecha y Hora de Cita Cargue</td>";
    $html .= "<td class=celda_titulo rowspan=2 >Origen</td>";
    $html .= "<td class=celda_titulo rowspan=2 >Destino</td>";
    $html .= "<td class=celda_titulo rowspan=2 >No. Pedido</td>";
    $html .= "<td class=celda_titulo rowspan=2 >No. Solicitud</td>";
    $html .= "<td class=celda_titulo colspan=3 >Estimado de Llegada</td>";  
    $html .= "<td class=celda_titulo rowspan=2 >Ubicación</td>";
    $html .= "<td class=celda_titulo rowspan=2 >Placa</td>";
    $html .= "<td class=celda_titulo rowspan=2 >Conductor</td>";    
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td class=cellHead >Fecha</td>";    
    $html .= "<td class=cellHead >Hora</td>"; 
    $html .= "<td class=cellHead >Fecha</td>";    
    $html .= "<td class=cellHead >Hora</td>"; 
    $html .= "<td class=cellHead >Fecha</td>";  
    $html .= "<td class=cellHead >Duraci&oacute;n</td>";    
    $html .= "<td class=cellHead >Días</td>";
    $html .= "</tr>";
    
    $i = 0;
    if( $informe )
    foreach( $informe as $row )
    {
      $i++;
      
      $fec_sal  = $this -> toFecha( $row[1]);
      $fec_lle  = $this -> toFecha( $row[4]);
      //valido que las fecha de cita de cargue no esta vacias o con formato 0000-00-00 00:00:00
      $ValirFecha = true;
      //valido fecha 0000-00-00
      if( $row[13] == Null || $row[13] == "0000-00-00" )
      {
        $ValirFecha = false;
      }
      //valido hora 00:00:00
      if( $row[14] == Null || $row[14] == "00:00:00" )
      {
        $ValirFecha = false;
      }
      //llamo la funcion toFecha
      if($ValirFecha == true)
      {
        $fec_cit  = $this -> toFecha( $row[13]." ".$row[14] );
      }
      else
      {
        $fec_cit[0] = "";
        $fec_cit[1] = "";
      }
      
      $html .= "<tr>";// celda_info
      $html .= "<td class=cellHead nowrap>$i</td>";                                      // Consecutivo
      $html .= "<td class=celda_info nowrap>$row[0]</td>";                               // Número despacho
      $html .= "<td class=celda_info nowrap>$row[10]</td>";                              // nombre Transportadora
      $html .= "<td class=celda_info nowrap>".$fec_sal[0]."</td>";                       // Fecha salida
      $html .= "<td class=celda_info nowrap>".$fec_sal[1]."</td>";                       // Hora salida
      $html .= "<td class=celda_info nowrap>".$fec_cit[0]."</td>";                       // Fecha cita cargue
      $html .= "<td class=celda_info nowrap>".$fec_cit[1]."</td>";                       // Hora cita cargue
      $html .= "<td class=celda_info nowrap>$row[2]</td>";                               // Origen
      $html .= "<td class=celda_info nowrap>$row[3]</td>";                               // Destino
      $html .= "<td class=celda_info nowrap>$row[num_pedido]</td>";                      // Pedido
      $html .= "<td class=celda_info nowrap>$row[num_solici]</td>";                      // Solicitud
      $html .= "<td class=celda_info nowrap>".$fec_lle[0]."</td>";                       // Fecha llegada 
      $html .= "<td class=celda_info nowrap>".$row[8]."</td>";                           // Duración Dias desde salida a llegada
      
      $bg_color = "";
      if( $row[9] <= 0 ) $bg_color = " style='background-color:#EAF1DD' "; 
      elseif( $row[9] == 1 ) $bg_color = " style='background-color:#FAC090' ";
      else $bg_color = " style='background-color:#FF3300' ";
      
      $html .= "<td class=celda_info $bg_color nowrap>".$row[9]."</td>";                 // Días desde Fecha salida      
      $html .= "<td class=celda_info nowrap>".$this -> getUbicacion( $row[0] )."</td>";  //Ubicacion.
      $html .= "<td class=celda_info nowrap>$row[6]</td>";                               // Placas  
      $html .= "<td class=celda_info nowrap>$row[7]</td>";                               // Conductor
      $html .= "</tr>";
    }
    
    $html .= "<tr>";
    $html .= "<td class=celda_titulo colspan=15 >Se Encontraron ".sizeof( $informe )." Manifiestos.</td>";  
    $html .= "</tr>";
    
    $html .= "</table>";

    echo  $html;
    $_SESSION[html] = $informe;
   
    $form -> cerrar();
  }
  
  
  
  function getUbicacion( $num_despac )
  {
    $select = "( SELECT a.fec_contro, UPPER( b.nom_sitiox )
                   FROM ".BASE_DATOS.".tab_despac_contro a,
                        ".BASE_DATOS.".tab_despac_sitio b
                  WHERE a.cod_sitiox = b.cod_sitiox AND
                        a.num_despac = '$num_despac' )
                UNION
               ( SELECT a.fec_noveda, UPPER( b.nom_contro )
                   FROM ".BASE_DATOS.".tab_despac_noveda a,
                        ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro AND
                        a.num_despac = '$num_despac' )
               ORDER BY 1 DESC ";
        //echo "<pre>"; print_r($select); echo "</pre>";
    $select = new Consulta( $select, $this -> conexion );
    $select = $select -> ret_matriz( "i" );

    return $select[0][1];
  }
  
  function toFecha($date)
    {
        $fecha = explode("-", $date);
        $dia = $fecha[2];
        $mes = $fecha[1];
        $ano = $fecha[0];     
        
        $dia = explode(" ",$dia);
        
        $hora = explode(" ",$date);        
        $letra_mes = "";

        switch ($mes)
        {
            case "1": $letra_mes = "ENE";
                break;
            case "2": $letra_mes = "FEB";
                break;
            case "3": $letra_mes = "MAR";
                break;
            case "4": $letra_mes = "ABR";
                break;
            case "5": $letra_mes = "MAY";
                break;
            case "6": $letra_mes = "JUN";
                break;
            case "7": $letra_mes = "JUL";
                break;
            case "8": $letra_mes = "AGO";
                break;
            case "9": $letra_mes = "SEP";
                break;
            case "10": $letra_mes = "OCT";
                break;
            case "11": $letra_mes = "NOV";
                break;
            case "12": $letra_mes = "DIC";
                break;
        }
              
        $salida[0] = "$dia[0]-$letra_mes";
        $salida[1] = "$hora[1]";
        
        return $salida;
    }
  
  function getTranspor()
  {
    $datos_usuario =  $_SESSION[datos_usuario];
    //echo "<pre>"; print_r($datos_usuario); echo "</pre>";
    $select = "SELECT d.cod_transp,
                     UPPER((SELECT nom_tercer FROM tab_tercer_tercer WHERE d.cod_transp = cod_tercer)) AS nom_tercer
                                          
              FROM " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e,
                   " . BASE_DATOS . ".tab_tercer_tercer f,
                   " . BASE_DATOS . ".tab_despac_despac a
                    LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n  
                   ON n.cod_tercer = a.cod_asegur
             WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                   d.cod_conduc = e.cod_tercer AND
                   d.cod_transp = f.cod_tercer AND
                   
                   a.fec_salida Is Not Null AND
                   a.fec_llegad IS NULL AND
                   a.ind_anulad = 'R' ";
    if ($datos_usuario["cod_perfil"] == "")
    {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion))
      {
          $datos_filtro = $filtro->retornar();
          $select .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      } 
    }
    else
    {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion))
      {
          $datos_filtro = $filtro->retornar();
          $select .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
    }
    $select .= "GROUP BY 1 ORDER BY 2 ";
    $select = new Consulta( $select, $this -> conexion );
   
    
    if($_POST[cod_transp] ){
    $selected = "SELECT a.cod_tercer, UPPER(a.nom_tercer)
           FROM ".BASE_DATOS.".tab_tercer_tercer a,
            ".BASE_DATOS.".tab_tercer_emptra b            
           WHERE a.cod_tercer = b.cod_tercer AND b.cod_tercer = '$_POST[cod_transp]'  
           GROUP BY 1 
           ORDER BY 2 ";
     $selected = new Consulta( $selected, $this -> conexion );    
     $selected = $selected -> ret_matriz( "i" );
     
    }
     if($_POST[cod_transp] )      
    $select = array_merge( $selected ,  array( array( "", "---" ) ), $select -> ret_matriz( "i" ) );
    else
    $select = array_merge(  array( array( "", "---" ) ), $select -> ret_matriz( "i" ) );
    
    return $select;
  }
  
  
  
  function getOrigen()
  {
    $select = "SELECT  a.cod_ciuori,
                     UPPER((SELECT a1.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a1 WHERE a1.cod_ciudad = a.cod_ciuori)) AS origen
              FROM " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e,
                   " . BASE_DATOS . ".tab_tercer_tercer f,
                   " . BASE_DATOS . ".tab_despac_despac a
                    LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n  
                   ON n.cod_tercer = a.cod_asegur
             WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                   d.cod_conduc = e.cod_tercer AND
                   d.cod_transp = f.cod_tercer AND
                   
                   a.fec_salida Is Not Null AND
                   a.fec_llegad IS NULL AND
                   a.ind_anulad = 'R' 
                   GROUP BY 1 ORDER BY 2 ";
    
    $select = new Consulta( $select, $this -> conexion );
    $select = array_merge( array( array( "", "---" ) ), 
          $select -> ret_matriz( "i" ) );
    
    return $select;
  }
  
  function getDestino()
  {
    $select = "SELECT a.cod_ciudes,
	 								   UPPER((SELECT nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = a.cod_ciudes)) AS destin
                FROM " . BASE_DATOS . ".tab_despac_seguim b,
                     " . BASE_DATOS . ".tab_despac_vehige d,
                     " . BASE_DATOS . ".tab_tercer_tercer e,
                     " . BASE_DATOS . ".tab_tercer_tercer f,
                     " . BASE_DATOS . ".tab_despac_despac a
                      LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n  
                     ON n.cod_tercer = a.cod_asegur
               WHERE a.num_despac = d.num_despac AND
                     a.num_despac = b.num_despac AND
                     d.cod_conduc = e.cod_tercer AND
                     d.cod_transp = f.cod_tercer AND
                     
                     a.fec_salida Is Not Null AND
                     a.fec_llegad IS NULL AND
                     a.ind_anulad = 'R' 
                     GROUP BY 1 ORDER BY 2 ";
    
    $select = new Consulta( $select, $this -> conexion );
    $select = array_merge( array( array( "", "---" ) ), 
          $select -> ret_matriz( "i" ) );
    
    return $select;
  }
  
  function getTipdes()
  {
    $mSelect = "SELECT cod_tipdes, nom_tipdes FROM ". BASE_DATOS .".tab_genera_tipdes WHERE 1 = 1 ORDER BY 2";
    $select = new Consulta( $mSelect, $this -> conexion );
    $select = array_merge( array( array( "", "---" ) ), 
              $select -> ret_matriz( "i" ) );
    
    return $select;
  }
  
  function getGenera($cod_transp)
  {
    $select = "SELECT b.cod_tercer,
                      b.abr_tercer
              FROM 
                   " . BASE_DATOS . ".tab_despac_despac a,
                   " . BASE_DATOS . ".tab_tercer_tercer b,
                   " . BASE_DATOS . ".tab_despac_vehige c
             WHERE 
                   b.cod_tercer = a.cod_client AND
                   a.num_despac = c.num_despac AND
                   a.fec_salida Is Not Null AND
                   a.fec_llegad IS NULL AND
                   a.ind_anulad = 'R' AND                               
                   c.cod_transp = '$cod_transp'
                   GROUP BY 1 ORDER BY 2";
    $select = new Consulta( $select, $this -> conexion );
    $select = array_merge( array( array( "", "---" ) ), 
    $select -> ret_matriz( "i" ) );

    return $select;
  }
  
}

$pagina = new Informe( $this -> conexion , $this-> codigo);
?>
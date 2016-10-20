<?php

  class DesNoveEnrut{
  
    var $conexion, $cod_aplica, $usuario;
    
    function __construct($co, $us, $ca){
      $this -> conexion = $co;
      $this -> usuario = $us;
      $this -> cod_aplica = $ca;
      $this -> principal();
    }
    
    function principal(){
      if(!isset($_REQUEST[opcion])){
        $this -> Prefilter();
      }else{
        switch($_REQUEST[opcion]){
          case "1":
            $this -> Listar();
          break;
          
           case "2":
            $this -> Xls();
          break;
          
        }
      }
    }
    
    function Prefilter(){
      
      $datos_usuario = $this -> usuario -> retornar();

      $inicio[0][0] = "";
      $inicio[0][1] = "----";
      $conditions  = '';
      $conditions2 = '';
      
      
      if($datos_usuario["cod_perfil"] == ""){
        
        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $conditions  .= " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
          $conditions2 .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $conditions  .= " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
          $conditions2 .= " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
        
      }else{
        
        //PARA EL FILTRO DE ASEGURADORA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $conditions  .= " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
          $conditions2 .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $conditions  .= " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
          $conditions2 .= " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
        }
        
      }
      
      //Ciudades
      $query1 = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3))
        			   FROM ".BASE_DATOS.".tab_genera_ciudad a,
        			        ".BASE_DATOS.".tab_genera_depart b,
        			        ".BASE_DATOS.".tab_genera_paises c,
        			        ".BASE_DATOS.".tab_despac_despac d,
        			        ".BASE_DATOS.".tab_despac_vehige e,
        			        ".BASE_DATOS.".tab_vehicu_vehicu f
        			  WHERE a.cod_depart = b.cod_depart AND
        			        a.cod_paisxx = b.cod_paisxx AND
        			        b.cod_paisxx = c.cod_paisxx AND
        			        a.cod_ciudad = d.cod_ciuori AND
        			        a.cod_depart = d.cod_depori AND
        			        a.cod_paisxx = d.cod_paiori AND
        			        d.num_despac = e.num_despac AND
        			        e.num_placax = f.num_placax AND
        			        a.ind_estado = '1'
                      ".$conditions." ";
      
      $query1 .= " GROUP BY 1 ORDER BY 2";
      
      $consulta = new Consulta($query1, $this -> conexion);
      $ciudad = $consulta -> ret_matriz();
      $ciudad = array_merge($inicio,$ciudad);
      
      
      //Terceros                
      $query2 = "SELECT g.cod_tercer,g.abr_tercer
                   FROM ".BASE_DATOS.".tab_genera_ciudad a,
                        ".BASE_DATOS.".tab_genera_depart b,
                        ".BASE_DATOS.".tab_genera_paises c,
                        ".BASE_DATOS.".tab_despac_despac d,
                        ".BASE_DATOS.".tab_despac_vehige e,
                        ".BASE_DATOS.".tab_vehicu_vehicu f,
                        ".BASE_DATOS.".tab_tercer_tercer g
                  WHERE a.cod_depart = b.cod_depart 
                    AND a.cod_paisxx = b.cod_paisxx 
                    AND b.cod_paisxx = c.cod_paisxx 
                    AND a.cod_ciudad = d.cod_ciudes 
                    AND a.cod_depart = d.cod_depdes 
                    AND a.cod_paisxx = d.cod_paides 
                    AND d.num_despac = e.num_despac 
                    AND e.num_placax = f.num_placax 
                    AND e.cod_conduc = g.cod_tercer 
                    AND a.ind_estado = '1'
                    ".$conditions."  ";
      
      $query2 .= " GROUP BY 1 ORDER BY 2";

      $consulta = new Consulta($query2, $this -> conexion);
      $conduc = $consulta -> ret_matriz();
      $conduc = array_merge($inicio,$conduc);
      
      
      $query3 = "SELECT e.cod_tercer,e.abr_tercer
              FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_tercer_tercer e,
                   ".BASE_DATOS.".tab_vehicu_vehicu i
             WHERE a.num_despac = d.num_despac AND
                   d.cod_transp = e.cod_tercer AND
                   i.num_placax = d.num_placax AND
                   a.fec_salida IS NOT NULL AND
                   a.fec_salida <= NOW() AND
                   a.fec_llegad IS NOT NULL AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' 
                   ".$conditions2."  ";
      
      $query3 .= " GROUP BY 1 ORDER BY 2";

      $consulta = new Consulta($query3, $this -> conexion);
      $transpors = $consulta -> ret_matriz();
      
      
      $transpors = array_merge($inicio,$transpors);
      
    
      $formulario = new Formulario ("index.php","post","Novedades Despachos en Ruta","form_list");
      $formulario -> nueva_tabla();
      $formulario -> linea("Especifique las Condiciones de B&uacute;squeda",1,"t2");
      
      $formulario -> nueva_tabla();
      $formulario -> lista("Transportadora","transpor",$transpors,1);
      $formulario -> texto("Despacho","text","despac",1,10,10,"","");
      $formulario -> texto("N&uacute;mero de Transporte","text","docume",1,10,10,"","");
      $formulario -> texto("Placa","text","placax",1,6,6,"","");
      $formulario -> lista("Conductor","conduc",$conduc,1);

      $formulario -> lista("Ciudad de Origen","ciuori",$ciudad,0);
      $formulario -> lista("Ciudad de Destino","ciudes",$ciudad,1);

      $formulario -> nueva_tabla();
      $formulario -> linea("Selecci&oacute;n Para el Rango de Fecha",1,"t2");

      $feactual = date("Y-m-d");

      $formulario -> nueva_tabla();
      $formulario -> fecha_calendar("Fecha Inicial","fecini","form_list",$feactual,"yyyy-mm-dd",0);
      $formulario -> fecha_calendar("Fecha Final","fecfin","form_list",$feactual,"yyyy-mm-dd",1);

      $formulario -> nueva_tabla();
      $formulario -> oculto("opcion",1,0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
      $formulario -> botoni("Buscar","form_list.submit()",0);
      $formulario -> cerrar();
    
    }
    
    function Listar(){
      
      ini_set('memory_limit', '128M');
      
      $datos_usuario = $this -> usuario -> retornar();
      
      $query = "
                 SELECT cod_transp, num_despac, fec_planea, cod_manifi, num_placax, nom_tercer, nom_client,
                        num_telmov, nom_ciuori, nom_ciudes, nom_transp, fecha, hora, nom_contro, cod_contro, cod_ciuori, cod_ciudes, cod_conduc, cod_agedes, fec_salida
                   FROM (
        
                 SELECT d.cod_transp, a.num_despac, y.fec_planea, a.cod_manifi, d.num_placax, UPPER( i.abr_tercer ) AS nom_tercer, UPPER( m.abr_tercer ) AS nom_client,
                        i.num_telmov, p.nom_ciudad AS nom_ciuori, q.nom_ciudad AS nom_ciudes, UPPER( j.abr_tercer ) as nom_transp, DATE_FORMAT(z.fec_noveda, '%Y-%m-%d') AS fecha, DATE_FORMAT(z.fec_noveda, '%H:%i:%s') AS hora, 
                        UPPER( IF( r.ind_virtua = 1, CONCAT( r.nom_contro, ' (Virtual)' ), r.nom_contro ) ) AS nom_contro, 
                        y.cod_contro, a.cod_ciuori, a.cod_ciudes, d.cod_conduc, a.cod_agedes, a.fec_salida
                  FROM ".BASE_DATOS.".tab_despac_vehige d,
                       ".BASE_DATOS.".tab_tercer_tercer j,
                       ".BASE_DATOS.".tab_tercer_tercer i,
                       ".BASE_DATOS.".tab_genera_ciudad p,
                       ".BASE_DATOS.".tab_genera_ciudad q,
                       ".BASE_DATOS.".tab_genera_contro r,
                       ".BASE_DATOS.".tab_despac_seguim y,
                       ".BASE_DATOS.".tab_despac_noveda z,
                       ".BASE_DATOS.".tab_despac_despac a
                       LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer m ON m.cod_tercer = a.cod_client
                              
                 WHERE a.num_despac = d.num_despac 
                   AND d.cod_transp = j.cod_tercer 
                   AND d.cod_conduc = i.cod_tercer
                   AND a.cod_ciuori = p.cod_ciudad
                   AND a.cod_ciudes = q.cod_ciudad
                   AND a.fec_salida IS NOT NULL 
                   AND a.fec_salida <= NOW() 
                   AND a.fec_llegad IS NULL 
                   AND a.ind_anulad = 'R' 
                   AND a.ind_planru = 'S'
                   AND y.num_despac = a.num_despac
                   AND y.cod_rutasx = d.cod_rutasx
                   AND y.cod_contro = r.cod_contro
                   AND y.num_despac = z.num_despac
                   AND y.cod_contro = z.cod_contro
                   AND y.cod_rutasx = z.cod_rutasx 
                   
                   UNION
                   
                   SELECT d.cod_transp, a.num_despac, y.fec_planea, a.cod_manifi, d.num_placax, UPPER( i.abr_tercer ) AS nom_tercer, UPPER( m.abr_tercer ) AS nom_client,
                       i.num_telmov, p.nom_ciudad AS nom_ciuori, q.nom_ciudad AS nom_ciudes, UPPER( j.abr_tercer ) as nom_transp, DATE_FORMAT(z.fec_contro, '%Y-%m-%d') AS fecha, DATE_FORMAT(z.fec_contro, '%H:%i:%s') AS hora, 
                       UPPER( IF( z.cod_sitiox IS NOT NULL, n.nom_sitiox, IF( r.ind_virtua = 1, CONCAT( r.nom_contro, ' (Virtual)' ), r.nom_contro ) ) ) AS nom_contro, 
                       y.cod_contro, a.cod_ciuori, a.cod_ciudes, d.cod_conduc, a.cod_agedes, a.fec_salida
                  FROM ".BASE_DATOS.".tab_despac_vehige d,
                       ".BASE_DATOS.".tab_tercer_tercer j,
                       ".BASE_DATOS.".tab_tercer_tercer i,
                       ".BASE_DATOS.".tab_genera_ciudad p,
                       ".BASE_DATOS.".tab_genera_ciudad q,
                       ".BASE_DATOS.".tab_genera_contro r,
                       ".BASE_DATOS.".tab_despac_seguim y,
                       ".BASE_DATOS.".tab_despac_despac a
                       LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer m ON a.cod_client = m.cod_tercer,
                       ".BASE_DATOS.".tab_despac_contro z
                       LEFT JOIN ".BASE_DATOS.".tab_despac_sitio n ON z.cod_sitiox = n.cod_sitiox
                 WHERE a.num_despac = d.num_despac 
                   AND d.cod_transp = j.cod_tercer 
                   AND d.cod_conduc = i.cod_tercer
                   AND a.cod_ciuori = p.cod_ciudad
                   AND a.cod_ciudes = q.cod_ciudad
                   AND a.fec_salida IS NOT NULL 
                   AND a.fec_salida <= NOW() 
                   AND a.fec_llegad IS NULL 
                   AND a.ind_anulad = 'R' 
                   AND a.ind_planru = 'S'
                   AND y.num_despac = a.num_despac
                   AND y.cod_rutasx = d.cod_rutasx
                   AND y.cod_contro = r.cod_contro
                   AND y.num_despac = z.num_despac
                   AND y.cod_contro = z.cod_contro
                   AND y.cod_rutasx = z.cod_rutasx
                   
                  ) AS a
                  WHERE 1=1
                  
                   ";
      
      if( $datos_usuario["cod_perfil"] == "" ){
        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica ,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_agedes = '$datos_filtro[clv_filtro]' ";
        }
      }else{
        //PARA EL FILTRO DE EMPRESA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
        }
        
        //PARA EL FILTRO DE LA AGENCIA
        $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
        if($filtro -> listar($this -> conexion)){
          $datos_filtro = $filtro -> retornar();
          $query = $query . " AND a.cod_agedes = '$datos_filtro[clv_filtro]' ";
        }
      }
      
      if(!empty($_REQUEST[despac])){
        $query = $query . " AND a.num_despac = '".$_REQUEST[despac]."' ";
      }
      
      if(!empty($_REQUEST[docume])){
        $query = $query . " AND a.cod_manifi = '".$_REQUEST[docume]."' ";
      }
      
      if(!empty($_REQUEST[placax])){
        $query = $query . " AND a.num_placax = '".$_REQUEST[placax]."' ";
      }
      
      if(!empty($_REQUEST[conduc])){
        $query = $query . " AND a.cod_conduc = '".$_REQUEST[conduc]."' ";
      }
      
      if(!empty($_REQUEST[ciuori])){
        $query = $query . " AND a.cod_ciuori = '".$_REQUEST[ciuori]."' ";
      }
      
      if(!empty($_REQUEST[ciudes])){
        $query = $query . " AND a.cod_ciudes = '".$_REQUEST[ciudes]."' ";
      }
          
       
      if(!empty($_REQUEST[fecini]) && !empty($_REQUEST[fecfin])){
        $query = $query . " AND DATE( a.fec_salida ) BETWEEN '".$_REQUEST[fecini]."' AND '".$_REQUEST[fecfin]."' ";
      }else{
        if(!empty($_REQUEST[fecini]))
          $query = $query . " AND a.fec_salida >=  '".$_REQUEST[fecini]."' ";
        
        if(!empty($_REQUEST[fecfin]))
          $query = $query . " AND a.fec_salida <=  '".$_REQUEST[fecfin]."' ";
      }
      
      if(!empty($_REQUEST[transpor])){
        $query = $query . " AND a.cod_transp = '".$_REQUEST[transpor]."' ";
      }
      
                    

      
      $query = $query."ORDER BY 2,3";
      $consulta = new Consulta($query, $this -> conexion);
      $transpor =  $consulta -> ret_matriz(); 
      
     
     
      
      $mHtml  = "<table width='100%' cellspacing=0 cellpadding=4 border=0 class='formulario'>";
        $mHtml  .= "<thead>";
          $mHtml  .= "<tr>";
            $mHtml  .= "<th colspan='12' class='celda_titulo2' style='height: 18px; font-size: 13px;'>Reporte de Vehiculos que se Encuentran en Ruta</th>";
          $mHtml  .= "</tr>";
          $mHtml  .= "<tr>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>N&uacute;mero</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>No. Manifiesto</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Placa</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Transportadora</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Conductor</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Celular</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Origen</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Destino</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Cliente</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Fecha</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Hora</th>";
            $mHtml  .= "<th class='celda_titulo' style='height: 18px; font-size: 13px;'>Sitio Actual</th>";
          $mHtml  .= "</tr>";
        $mHtml  .= "</thead>";
        $mHtml  .= "<tbody>";
        
        $num_despac = '';
        $cod_manifi = '';
        $num_placax = '';
        $nom_tercer = '';
        $num_telmov = '';
        $nom_ciuori = '';
        $nom_ciudes = '';
        $abr_tercer = '';
        $nom_transp = '';
        $fecha = '';
        
        for($i=0, $len = count($transpor); $i<$len; $i++){
          $mHtml  .= "<tr>";
            if(strcmp($transpor[$i][num_despac], $num_despac)==0)
              $mHtml  .= "<td class='celda_info' align='right'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='right'>".$transpor[$i][num_despac]."</td>";
              $num_despac = $transpor[$i][num_despac];
            }
            if(strcmp($transpor[$i][cod_manifi], $cod_manifi)==0)
              $mHtml  .= "<td class='celda_info' align='right'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='right'>".$transpor[$i][cod_manifi]."</td>";
              $cod_manifi = $transpor[$i][cod_manifi];
            }
            if(strcmp($transpor[$i][num_placax], $num_placax)==0)
              $mHtml  .= "<td class='celda_info' align='right'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='right'>".$transpor[$i][num_placax]."</td>";
              $num_placax = $transpor[$i][num_placax];
            }
            if(strcmp($transpor[$i][nom_transp], $nom_transp)==0)
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='left'>".$transpor[$i][nom_transp]."</td>";
              $nom_transp = $transpor[$i][nom_transp];
            }
            if(strcmp($transpor[$i][nom_tercer], $nom_tercer)==0)
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;".$transpor[$i][nom_tercer]."</td>";
              $nom_tercer = $transpor[$i][nom_tercer];
            }
            if(strcmp($transpor[$i][num_telmov], $num_telmov)==0)
              $mHtml  .= "<td class='celda_info' align='right'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='right'>".$transpor[$i][num_telmov]."</td>";
              $num_telmov = $transpor[$i][num_telmov];
            }
            if(strcmp($transpor[$i][nom_ciuori], $nom_ciuori)==0)
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;".$transpor[$i][nom_ciuori]."</td>";
              $nom_ciuori = $transpor[$i][nom_ciuori];
            }
            if(strcmp($transpor[$i][nom_ciudes], $nom_ciudes)==0)
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;".$transpor[$i][nom_ciudes]."</td>";
              $nom_ciudes = $transpor[$i][nom_ciudes];
            }
            if(strcmp($transpor[$i][nom_client], $abr_tercer)==0)
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;".$transpor[$i][nom_client]."</td>";
              $abr_tercer = $transpor[$i][nom_client];
            }
            
            if(strcmp($transpor[$i][fecha], $fecha)==0)
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;</td>";
            else{
              $mHtml  .= "<td class='celda_info' align='left'>&nbsp;".$transpor[$i][fecha]."</td>";
              $fecha = $transpor[$i][fecha];
            }
            
            $mHtml  .= "<td class='celda_info' align='center'>&nbsp;".$transpor[$i][hora]."</td>";
            $mHtml  .= "<td class='celda_info' align='left'>&nbsp;".$transpor[$i][nom_contro]."</td>"; 
          $mHtml  .= "</tr>";
        }
        
        $_SESSION['xls_table'] = $mHtml."</tbody></table>";
        
        $mHtml  .= "</tbody>";
        $mHtml  .= "<tfoot>";
          $mHtml  .= "<tr>";
            $mHtml  .= " <td align='center' colspan='12'> 
                          <input type='hidden' value='2' name='opcion' > 
                          <input type='hidden' value='central' name='window' > 
                          <input type='hidden' value='".$_REQUEST[cod_servic]."' name='cod_servic' > 
                          <input type='button' onclick='javascript:history.go(-1)' value='Atras' name='Atras' class='crmButton small save'> 
                          <input type='button' onclick='form_list.submit()' value='Excel' name='xsl' class='crmButton small save'> 
                        </td>";
          $mHtml  .= "</tr>";
        $mHtml  .= "</tfoot>";
        
        
        
       
      $mHtml .= "</table>";
      
      
      
      echo '<form target="_self" action="index.php" method="post" name="form_list">'.$mHtml."</form>";

    
    }
    
    function Xls(){
      ob_clean();
      header('Content-Type: application/octetstream');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename=Reporte_Vehiculos_Ruta.xls');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      echo $_SESSION['xls_table'];
      exit();
    }
    
    
  }
  $proceso = new DesNoveEnrut($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

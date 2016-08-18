<?php
class Tram_rutas
{
  var $conexion,
 	$cod_aplica,
  $usuario;

  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }

  function principal()
  {
    //echo "<pre>"; print_r("Opcion-> ".$GLOBALS[opcion]); echo "</pre>";
    switch($GLOBALS[opcion])
     {
        case "lista":
          $this -> Lista();
        break;
        case "tramo":
          $this -> Tramos();
        break;
        case "insert":
          $this -> Insert();
        break;
        case "disable":
          $this -> Disable(); 
        break;
        default:
          $this -> Buscar();
        break;
     }//FIN SWITCH
  }// FIN ELSE GLOBALS OPCION
 
  /*********************************************************************
   * function Buscar                                                   *
   * brief    Formulario default para filtrar por ciudades             *
   * param                                                             *
   * return                                                            *
   *********************************************************************/
  function Buscar()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];
    $inicio[0][0]=0;
    $inicio[0][1]='-';
 
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tramruta.js\"></script>\n";

    $formulario = new Formulario ("index.php","post\" enctype=\"multipart/form-data\" ","Construir Tramo","form_tramo");
    $formulario -> linea("Seleccione las ciudades",0,"t2");
    
    $mCiudad = $this -> getCiudades ($inicio);
    
    $formulario -> nueva_tabla();
    $formulario -> lista("Ciudad origen:","origen",$mCiudad,0,1);
    $formulario -> lista("Ciudad destino:","destin",$mCiudad,1,1);
    $formulario -> caja ("Rutas con tramos configurados:","RuTra\"id=\"RuTraID",1,0,1); //onclick=\"ShowRow();\"  checked=\"checked\"

    $formulario -> nueva_tabla();
    $formulario -> oculto("MAX_FILE_SIZE", "2000000", 0);
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion","lista",0);
    $formulario -> oculto("cod_servic","$GLOBALS[cod_servic]",0);
    $formulario -> oculto("window","central",0);
    $formulario -> botoni("Aceptar","Validate_Form()",0);//
    $formulario -> cerrar();
  }
  
  /*********************************************************************
   * function Lista                                                    *
   * brief    Lista las rutas que hay en las ciudades filtradas        *
   * param                                                             *
   * return                                                            *
   *********************************************************************/
  function Lista()
  {
    $_SESSION[origen] = $_POST[origen];
    $_SESSION[destin] = $_POST[destin];
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tramruta.js\"></script>\n";
    $formulario = new Formulario ("index.php","post\" enctype=\"multipart/form-data\" ","Seleccionar ruta","form_tramo");
    
    $mOrigen = $this -> getOrigen($_POST[origen]);
    $mDestin = $this -> getDestin($_POST[destin]);
    
   
    
    if($_POST[RuTra])
      $mRutas = $this -> getRutasTramos();
    else
      $mRutas = $this -> getRutas();
    
    //echo "<pre>"; print_r($mRutas); echo "</pre>";
    $formulario -> nueva_tabla();
    $formulario -> linea("Filtro:",0,"t2");
    
    $formulario -> nueva_tabla();
    if($mOrigen){
    $formulario -> linea("Origen:",0,"t");
    $formulario -> linea($mOrigen,0,"i");
    }
    if($mDestin){
    $formulario -> linea("Destino:",0,"t");
    $formulario -> linea($mDestin,1,"i");
    }
    
    if($_POST[RuTra])
       $mPC = "Con Tramos configurados";
    
    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontraron un Total de ".sizeof($mRutas)." Rutas ".$mPC.".",0,"t2");
    
    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",1 ,"t");
    
    if(sizeof($mRutas) == 0)
    {
      $formulario -> nueva_tabla();
      $formulario -> botoni("Volver","history.go(-1)",0);
    }
    for($n =0 ; $n< sizeof($mRutas) ; $n++)
    {
      $mRutas[$n][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&rutax=".$mRutas[$n][0]."&opcion=tramo&nom_ruta=".$mRutas[$n][nom_rutasx]." \"target=\"centralFrame\">".$mRutas[$n][0]."</a>";
      
      $estilo = "i";
      $formulario -> linea($mRutas[$n][0],0,$estilo);
      $formulario -> linea($mRutas[$n][1],1,$estilo);
    }    
    $formulario -> cerrar();    
  }
  
  /*********************************************************************
   * function Tramos                                                   *
   * brief    Muestra formulario con los puestos de control físicos    *
   * param                                                             *
   * return                                                            *
   *********************************************************************/
  function Tramos()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tramruta.js\"></script>\n";
    $formulario = new Formulario ("index.php","post\" enctype=\"multipart/form-data\" ","Contrucción del Tramo","form_tramo");
    
    $mOrigen = $this -> getOrigen($_SESSION[origen]);
    $mDestin = $this -> getDestin($_SESSION[destin]);
   
    $formulario -> nueva_tabla();
    $formulario -> linea("Filtro:",0,"t2");
    
    $formulario -> nueva_tabla();
    $formulario -> linea("Código: ".$GLOBALS[rutax]." || Ruta: ".$GLOBALS[nom_ruta]."",0,"t"); //  $mRutaContro[0][1]
    
    $formulario -> nueva_tabla();
    $formulario -> linea("Origen:",0,"t");
    $formulario -> linea($mOrigen,0,"i");
    $formulario -> linea("Destino:",0,"t");
    $formulario -> linea($mDestin,1,"i");
    
    $formulario -> nueva_tabla();
    $formulario -> linea("Trámos entre puestos de control físicos",0,"t2");
    
    //--------------------------------------------------------------
    $mRutaContro = $this -> getRutaContro($GLOBALS[rutax]); // P/C para insertar Tramos         
    $mTramoContro = $this -> getTramoContro($GLOBALS[rutax]); // P/C para actualizar Tramos   
    //--------------------------------------------------------------
    if($mTramoContro == NULL)
    { //nueva Insercion
      $mCiu_Origen = array(
                         0=>array(
                                  0=> $GLOBALS[rutax], "cod_rutasx"=>$GLOBALS[rutax]               , 1=>$GLOBALS[nom_ruta], "nom_rutasx"=>$GLOBALS[nom_ruta],
                                  2=> $_SESSION[origen], "cod_ciuori" => $_SESSION[origen] , 3=>$_SESSION[destin],"cod_ciudes" => $_SESSION[destin],
                                  4=> $_SESSION[origen], "cod_contro" => $_SESSION[origen] , 5=>$mOrigen, "nom_contro" => $mOrigen,
                                  6=> "1", "val_duraci" => "1"                                  
                                  )
                        );
    
      $mCiu_destin = array(
                         0=>array(
                                  0=> $GLOBALS[rutax], "cod_rutasx"=>$GLOBALS[rutax]               , 1=>$GLOBALS[nom_ruta], "nom_rutasx"=>$GLOBALS[nom_ruta],
                                  2=> $_SESSION[origen], "cod_ciuori" => $_SESSION[origen] , 3=>$_SESSION[destin],"cod_ciudes" => $_SESSION[destin],
                                  4=> $_SESSION[origen], "cod_contro" => $_SESSION[destin] , 5=>$mDestin, "nom_contro" => $mDestin,
                                  6=> "1", "val_duraci" => "1"                                  
                                  )
                        );
      $mRutaContro = array_merge($mCiu_Origen, $mRutaContro, $mCiu_destin);  // Cuando se inserta por primera vez el tramo
    }
    
    $mUpdate = false;
    if($mTramoContro)
       $mUpdate = true;
    
    $formulario -> nueva_tabla();
    $html = '';
    $html .= "<table class='formulario' width='100%' cellspacing='0' cellpadding='4'>";
    $html .="<tbody>";
    $html .= "<tr class='celda_titulo' align='left'>";
    $html .= "<td>N°</td>";
    $html .= "<td>Trámos</td>";
    $html .= "<td>Kilómetos</td>";
    $html .= "<td>Velocidad (km/h) </td>";
    $html .= "<td>Tiempo (Min)</td>";    
    $html .= "</tr>";
    
    if($mTramoContro == NULL)
    {
      for($t =1 ,$a = 0; $a< count( $mRutaContro ); $a++)
      {
        if($mRutaContro[$a][cod_contro] === $_SESSION[destin]) break;
  
        $mTramoA = $mRutaContro[$a][nom_contro]; 
        $mTramoB = $mRutaContro[$a + 1][nom_contro]; 
        
        $html .= "<tr >";
        $html .= "<td ><b>$t</b></td>";
        $html .= "<td class='celda_info' align='center'><b>".$mRutaContro[$a][cod_contro]." - $mTramoA - $mTramoB</b></td>";
        $html .= "<td class='celda_info' ><input type='text' name='km[]' maxlength='5' onkeypress='return soloNumeros(event)' value='".$mTramoContro[$a][dis_contro]."'/></td>";
        $html .= "<td class='celda_info' ><input type='text' name='vl[]' maxlength='3' onkeypress='return soloNumeros(event)' value='".$mTramoContro[$a][vel_contro]."'/></td>";
        $html .= "<td class='celda_info' ><input type='text' name='tm[]' maxlength='4' onkeypress='return soloNumeros(event)' value='".$mTramoContro[$a][tie_contro]."'/></td>";
        $html .= "</tr>";
        //Hidden
        $html .= "<input type='hidden' id='ruta[]' name='ruta[]' value = '".$mRutaContro[0][0]."' />";
        $html .= "<input type='hidden' id='rutanom[]' name='rutanom[]' value = '".$mRutaContro[0][1]."' />";      
        $html .= "<input type='hidden' id='controa[]' name='controa[]' value = '".$mRutaContro[$a][nom_contro]."' />";
        $html .= "<input type='hidden' id='controb[]' name='controb[]' value = '".$mRutaContro[$a + 1][nom_contro]."' />";
        
        //$html .= "<input type='hidden' id='consect[]' name='consect[]' value = '".$mTramoContro[$a][cod_consec]."' />";
        $html .= "<input type='hidden' id='in[]' name='in[]' value='".$mRutaContro[$a][cod_contro]."' />";
        $html .= "<input type='hidden' id='out[]' name='out[]' value='".$mRutaContro[$a+1][cod_contro]."' />";
        $t++;
      }
    }
    else
    {
      for($t =1 ,$a = 0; $a< count( $mTramoContro ); $a++)
      {
        if($mTramoContro[$a][nom_controa] == 'Origen') $mTramoContro[$a][nom_controa] = $mOrigen;
        if($mTramoContro[$a][nom_controb] == 'Destino') $mTramoContro[$a][nom_controb] = $mDestin;
        
        $mTramoA = $mTramoContro[$a][nom_controa]; 
        $mTramoB = $mTramoContro[$a][nom_controb]; 
        
        
        $html .= "<tr >";
        $html .= "<td ><b>$t</b></td>";
        $html .= "<td class='celda_info' align='center'><b>".$mTramoContro[$a][cod_contra]." $mTramoA - ".$mTramoContro[$a][cod_contrb]." $mTramoB</b></td>";
        $html .= "<td class='celda_info' ><input type='text' name='km[]' maxlength='5' onkeypress='return soloNumeros(event)' value='".$mTramoContro[$a][dis_contro]."'/></td>";
        $html .= "<td class='celda_info' ><input type='text' name='vl[]' maxlength='3' onkeypress='return soloNumeros(event)' value='".$mTramoContro[$a][vel_contro]."'/></td>";
        $html .= "<td class='celda_info' ><input type='text' name='tm[]' maxlength='4' onkeypress='return soloNumeros(event)' value='".$mTramoContro[$a][tie_contro]."'/></td>";
        $html .= "</tr>";
        //Hidden
        $html .= "<input type='hidden' id='ruta[]' name='ruta[]' value = '".$mTramoContro[0][0]."' />";
        $html .= "<input type='hidden' id='rutanom[]' name='rutanom[]' value = '".$mTramoContro[0][nom_rutasx]."' />"; 

        $html .= "<input type='hidden' id='controa[]' name='controa[]' value = '".$mTramoContro[$a][nom_controa]."' />";
        $html .= "<input type='hidden' id='controb[]' name='controb[]' value = '".$mTramoContro[$a][nom_controb]."' />"; 

        $html .= "<input type='hidden' id='consect[]' name='consect[]' value = '".$mTramoContro[$a][cod_consec]."' />";
        $html .= "<input type='hidden' id='in[]' name='in[]' value='".$mTramoContro[$a][cod_contra]."' />";
        $html .= "<input type='hidden' id='out[]' name='out[]' value='".$mTramoContro[$a][cod_contrb]."' />";       
       
        $t++;
        //$cod_ruta = $mTramoContro[0][0];
      }
    }
    $html .= "<input type='hidden' id='rutax' name='rutax' value = '".$GLOBALS[rutax]."' />";
    $html .= "<input type='hidden' name='rows' value='".$a."' />";
    $html .= "<input type='hidden' name='UpDate' value='".$mUpdate."' />";
    $html .="</tbody>";
    $html .= "</table>";
    echo $html;
    //--------------------------------------------------- 
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion","insert",0);
    $formulario -> oculto("cod_servic","$GLOBALS[cod_servic]",0);
    $formulario -> oculto("window","central",0);
    
    if(count( $mRutaContro ) == 0)
    {
      echo "<br><b>No hay puestos de control asignados para esta ruta.<b>";
      $formulario -> nueva_tabla();
      $formulario -> botoni("Volver","Volver_Form()",0);
    }
    else
    {
      $formulario -> nueva_tabla();
      $formulario -> botoni("Aceptar","Validate_FormTramo()",0);
      if($mTramoContro)
        $formulario -> botoni("Desactivar","Disable_Tramo()",0);
    }
    $formulario -> cerrar();

  }
  
  /*********************************************************************
   * function getCiudades                                              *
   * brief    consulta las ciudad activas                              *
   * param    array $inicio array para el inicio de las listas         *
   * return   array con las listas de las ciudades                     *
   *********************************************************************/
  function getCiudades( $inicio )
  {
    $mQuery = " SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(c.abr_depart,4),') - ',LEFT(d.nom_paisxx,3),' - ',a.cod_ciudad)
               FROM ".BASE_DATOS.".tab_genera_ciudad a,
                    ".BASE_DATOS.".tab_genera_rutasx b,
                    ".BASE_DATOS.".tab_genera_depart c,
                    ".BASE_DATOS.".tab_genera_paises d
              WHERE a.cod_ciudad = b.cod_ciuori AND
                    a.cod_depart = c.cod_depart AND
                    a.cod_paisxx = c.cod_paisxx AND
                    c.cod_paisxx = d.cod_paisxx
                    GROUP BY 1 ORDER BY a.abr_ciudad";
    $consulta = new Consulta($mQuery, $this -> conexion);
    $ciudades = $consulta -> ret_matriz();
    $ciudades = array_merge($inicio, $ciudades );
    return $ciudades;
  }
  
  /*********************************************************************
   * function getRutas                                                 *
   * brief    consulta las rutas segun parámteros                      *
   * param    int($origen) Código ciudad origen                        *
   * param    int($destin) Código ciudad destino                       *
   * return   array con las rutas entre las ciudades                   *
   *********************************************************************/
  function getRutas ()
  { 
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $query = "SELECT a.cod_rutasx,a.nom_rutasx,Count(d.cod_contro)
                 FROM ".BASE_DATOS.".tab_genera_rutasx a LEFT JOIN
                      ".BASE_DATOS.".tab_genera_rutcon d ON
                      a.cod_rutasx = d.cod_rutasx
                      ";
     $indwhere = 0;

     if($GLOBALS[origen]) {
      if($indwhere)
       $query .= " AND a.cod_ciuori = '".$GLOBALS[origen]."'";
      else   {
       $query .= " WHERE a.cod_ciuori = '".$GLOBALS[origen]."'";
       $indwhere = 1;
      }
     }

     if($GLOBALS[destin])     {
      if($indwhere)
       $query .= " AND a.cod_ciudes = '".$GLOBALS[destin]."'";
      else      {
       $query .= " WHERE a.cod_ciudes = '".$GLOBALS[destin]."' ";
       $indwhere = 1;
      }
     }
    if(!$indwhere)
      $query .= "WHERE a.ind_estado = '1'";
    else
      $query .= "AND a.ind_estado = '1'";
     
     //PARA EL FILTRO DE EMPRESA
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();      
      if($indwhere)
        $query = $query . " AND a.cod_rutasx IN( SELECT cod_rutasx FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_rutasx ) ";
      else      {
        $query = $query . " WHERE a.cod_rutasx IN( SELECT cod_rutasx FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_rutasx ) ";
        $indwhere = 1;
      }      
     }     
    $query .= " GROUP BY 1 ORDER BY 2";
    
    
       
    $consulta = new Consulta($query, $this -> conexion);
    $Rutas = $consulta -> ret_matriz();
    return $Rutas;
  }
  
  /*********************************************************************
   * function getRutasTramos                                           *
   * brief    consulta las rutas con tramos configurados               *
   * param    int($origen) Código ciudad origen                        *
   * param    int($destin) Código ciudad destino                       *
   * return   array con las rutas entre las ciudades                   *
   *********************************************************************/
  function getRutasTramos()
  { 
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];
    
    if($GLOBALS[origen] && $GLOBALS[destin])
    {
      $query = "SELECT a.cod_rutasx, a.nom_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx a, 
                       ".BASE_DATOS.".tab_tramos_rutasx d
                 WHERE a.cod_rutasx = d.cod_rutasx AND
                       (d.cod_contra = '".$GLOBALS[origen]."'  OR d.cod_contrb = '".$GLOBALS[destin]."' ) AND
                       d.ind_estado = '1'";
    }
    else
    {
      $query = "SELECT a.cod_rutasx, a.nom_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx a, 
                       ".BASE_DATOS.".tab_tramos_rutasx d
             ";
     $indwhere = 0;    
     
     if($GLOBALS[origen]) {
      if($indwhere)
       $query .= " AND d.cod_contra = '".$GLOBALS[origen]."'";
      else   {
       $query .= " WHERE d.cod_contra = '".$GLOBALS[origen]."' AND a.cod_rutasx = d.cod_rutasx AND d.ind_estado = '1'";
       $indwhere = 1;
      }
     }

     if($GLOBALS[destin])     {
      if($indwhere)
       $query .= " AND d.cod_contrb = '".$GLOBALS[destin]."'";
      else      {
       $query .= " WHERE d.cod_contrb = '".$GLOBALS[destin]."' AND a.cod_rutasx = d.cod_rutasx AND d.ind_estado = '1'";
       $indwhere = 1;
      }
     }
      if(!$indwhere)
         $query .= "WHERE a.cod_rutasx = d.cod_rutasx AND d.ind_estado = '1'";
      else
         $query .= " AND a.cod_rutasx = d.cod_rutasx ";
         
    
     //PARA EL FILTRO DE EMPRESA
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();      
      if($indwhere)
        $query = $query . " AND a.cod_rutasx IN( SELECT cod_rutasx FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_rutasx ) ";
      else      {
        $query = $query . " WHERE a.cod_rutasx IN( SELECT cod_rutasx FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_rutasx ) ";
        $indwhere = 1;
      }      
     } 
    }
     
    $query .= " GROUP BY 1 ORDER BY 2";
  
       
    $consulta = new Consulta($query, $this -> conexion);
    $Rutas = $consulta -> ret_matriz();
    return $Rutas;
    
  }
  
  /*********************************************************************
   * function getOrigen                                          * 
   * brief    consulta las ciudades filtradas                          *
   * param    int($origen) Código ciudad origen                        *
   * param    int($destin) Código ciudad destino                       *
   * return   array ($Ciudad) con el nombre de las ciudades            *
   *********************************************************************/
  function getOrigen ( $origen )
  {
    if($_GET[rutax])
    {
      $mQuery = "SELECT (SELECT nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = a.cod_ciuori) AS nom_ciudad
                        
                 FROM ".BASE_DATOS.".tab_genera_rutasx a
                WHERE a.cod_rutasx IN ( '".$_GET[rutax]."')
                LIMIT 0 , 2 ";
      $consulta = new Consulta($mQuery, $this -> conexion);
      $Ciudad = $consulta -> ret_matriz();
      return $Ciudad[0][0];
    }
    else
    {
    $mQuery = "SELECT nom_ciudad
                 FROM tab_genera_ciudad
                WHERE cod_ciudad IN ( '".$origen."')
                LIMIT 0 , 2 ";
    $consulta = new Consulta($mQuery, $this -> conexion);
    $Ciudad = $consulta -> ret_matriz();
    return $Ciudad[0][0];
    }
  }
   /********************************************************************
   * function getDestin                                                *
   * brief    consulta las ciudades filtradas                          *
   * param    int($origen) Código ciudad origen                        *
   * param    int($destin) Código ciudad destino                       *
   * return   array ($Ciudad) con el nombre de las ciudades            *
   *********************************************************************/
  function getDestin ( $destin )
  {
    if($_GET[rutax])
    {
      $mQuery = "SELECT (SELECT nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad WHERE cod_ciudad = a.cod_ciudes) AS nom_ciudad                        
                 FROM ".BASE_DATOS.".tab_genera_rutasx a
                WHERE a.cod_rutasx IN ( '".$_GET[rutax]."')
                LIMIT 0 , 2 ";
      $consulta = new Consulta($mQuery, $this -> conexion);
      $Ciudad = $consulta -> ret_matriz();
      return $Ciudad[0][0];
    }
    else
    {
      $mQuery = "SELECT nom_ciudad
                   FROM tab_genera_ciudad
                  WHERE cod_ciudad IN ('".$destin."' )
                  LIMIT 0 , 2 ";
      $consulta = new Consulta($mQuery, $this -> conexion);
      $Ciudad = $consulta -> ret_matriz();
      return $Ciudad[0][0];
    }
  }
  
   /*********************************************************************************
   * function getRutaContro                                                         *
   * brief    Consulta los puestos de control físicos que hay en una ruta           *
   * param    int($CodRuta) Código de la ruta                                       *
   * return   array ($ContrRuta) Datos de los puestos de control físicos de la ruta *
   *********************************************************************************/
  function getRutaContro($CodRuta)
  {
    $mQuery = " SELECT a.cod_rutasx,
                       a.nom_rutasx,
                       a.cod_ciuori,
                       a.cod_ciudes,
                       d.cod_contro,
                       if(e.ind_virtua = '1',CONCAT(e.nom_contro,' (Virtual)'),e.nom_contro) AS nom_contro,
                       d.val_duraci
                FROM ".BASE_DATOS.".tab_genera_rutasx a,
                      ".BASE_DATOS.".tab_genera_rutcon d,
                      ".BASE_DATOS.".tab_genera_contro e
                WHERE a.cod_rutasx = d.cod_rutasx AND
                      d.cod_contro = e.cod_contro AND
                      e.ind_virtua = '0' AND
                      d.ind_estado = '1' AND
                      e.nom_contro NOT LIKE '%Lugar entrega%' AND
                      a.cod_rutasx = '".$CodRuta."'
                ORDER BY 7";
    $consulta = new Consulta($mQuery, $this -> conexion);
    return $ContrRuta = $consulta -> ret_matriz();                   
  }
  
  function getTramoContro($CodRuta)
  {
    $mQuery ="SELECT a.cod_rutasx, 
                     a.cod_consec, 
                     a.cod_contra,
                     IF((SELECT nom_contro FROM ".BASE_DATOS.".tab_genera_contro WHERE cod_contro = a.cod_contra) IS NULL ,'Origen',(SELECT nom_contro FROM satt_faro.tab_genera_contro WHERE cod_contro = a.cod_contra) ) AS nom_controa,                       
                     a.cod_contrb, 
                     IF((SELECT nom_contro FROM ".BASE_DATOS.".tab_genera_contro WHERE cod_contro = a.cod_contrb) IS NULL ,'Destino',(SELECT nom_contro FROM satt_faro.tab_genera_contro WHERE cod_contro = a.cod_contrb)) AS nom_controb,    
                     a.dis_contro, 
                     a.vel_contro, 
                     a.tie_contro, 
                     a.ind_estado,
                     (SELECT nom_rutasx FROM ".BASE_DATOS.".tab_genera_rutasx WHERE cod_rutasx = a.cod_rutasx) AS nom_rutasx
                FROM ".BASE_DATOS.".tab_tramos_rutasx a
               WHERE a.cod_rutasx = '".$CodRuta."' AND 
                     a.ind_estado = '1' 
            ORDER BY a.cod_consec ";
                 /*echo "<pre>";
                 print_r($mQuery);
                 echo "</pre>";*/
    $consulta = new Consulta($mQuery, $this -> conexion);
    return $Tramos = $consulta -> ret_matriz();     
    
  }
   
  /***********************************************************************************
   * function Insert                                                                 *
   * brief    Hace la insercion a BD con los datos de los tramos de la ruta entre los*
   *          puestos de control.                                                    *
   *                                                                                 *
   **********************************************************************************/
  function Insert()
  {
    $mRows = $_POST[rows];
    $mRuta = $_POST[ruta];
    $mNomRuta = $_POST[rutanom];
    $mConsect = $_POST[consect];
    $mControA = $_POST[in];
    $mControB = $_POST[out];
    $mKilomet = $_POST[km];
    $mVelocid = $_POST[vl];
    $mTiempox = $_POST[tm];
    $mUsuario = $_POST[usuario];
    $UpDate = $_POST[UpDate];
    
    /*echo "<pre>";
    print_r($mControA);
    echo "</pre>";
    
    echo "<pre>";
    print_r($mControB);
    echo "</pre>";*/    
    

    if($UpDate == 1)
    {
      for($b = 1, $a = 0 ;$a <= $mRows - 1; $a++)
      {
        $mQuery = "UPDATE  ".BASE_DATOS.".tab_tramos_rutasx 
                           SET  dis_contro = '".$mKilomet[$a]."',
                                vel_contro = '".$mVelocid[$a]."',
                                tie_contro = '".$mTiempox[$a]."',
                                usr_modifi = '".$mUsuario."',
                                fec_modifi = NOW()
                         WHERE
                                cod_rutasx = '".$mRuta[$a]."' AND
                                cod_consec = '".$mConsect[$a]."' AND
                                cod_contra = '".$mControA[$a]."' AND
                                cod_contrb = '".$mControB[$a]."'";
                                                                  
        $insercion = new Consulta($mQuery, $this -> conexion,"R");
        $b++;
      }
    }
    else
    { 
      $mQueryConsec = "SELECT max(cod_consec)
                         FROM tab_tramos_rutasx a
                        WHERE a.cod_rutasx = '".$mRuta[0]."' AND
                              a.ind_estado = '0'";
      $consulta = new Consulta($mQueryConsec, $this -> conexion);
      $mMaxi = $consulta -> ret_matriz(); 
      $mMaxi = $mMaxi[0][0]+1;
      
      
      for($mMaxi = $mMaxi, $a = 0 ;$a <= $mRows - 1; $a++)
      { 
        $HomoloPcEalA = $this -> HomoPCEal($mControA[$a]);
        $HomoloPcEalB = $this -> HomoPCEal($mControB[$a]);
        
        $mQyery = "INSERT INTO ". BASE_DATOS .".tab_tramos_rutasx (cod_rutasx, cod_consec, cod_contra, cod_contrb, 
                                                                        dis_contro, vel_contro, tie_contro, ind_estado,
                                                                        usr_creaci, fec_creaci)
                                                           VALUES ('".$mRuta[$a]."'   , '".$mMaxi."'  ,'".$HomoloPcEalA."','".$HomoloPcEalB."',
                                                                   '".$mKilomet[$a]."', '".$mVelocid[$a]."','".$mTiempox[$a]."', '1',
                                                                   '".$mUsuario."', NOW())";
        $insercion = new Consulta($mQyery, $this -> conexion,"R");
        //echo "<br>";
        $mMaxi++;
      }
    }
    //------------------------------------------------------------------------------------------------------
    $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Volver</a></b>";
    
    if($UpDate != 1)
      $mensaje = "Se ha <b>CREADO</b> los tramos para la ruta:  <br><b>".$mNomRuta[0]."</b> <br>".$link;
    else
      $mensaje = "Se ha <b>ACTUALIZADO</b> el tramo para la ruta:  <br><b>".$mNomRuta[0]."</b> <br>".$link;
    
    $mens = new mensajes();
    $mens -> correcto("CONSTRUCCIÓN DE TRAMOS",$mensaje);
  }
  
  /***********************************************************************************
   * function Insert                                                                 *
   * brief    Hace la insercion a BD con los datos de los tramos de la ruta entre los*
   *          puestos de control.                                                    *
   *                                                                                 *
   **********************************************************************************/
  function HomoPCEal($cod_contro)
  {
    $mQueryConsec = "SELECT a.cod_contro AS Padre, '".$cod_contro."' AS Hijo
                       FROM ".BASE_DATOS.".tab_homolo_pcxeal a
                      WHERE (a.cod_contro = '".$cod_contro."' OR a.cod_homolo =  '".$cod_contro."')
                      GROUP BY 2";
    $consulta = new Consulta($mQueryConsec, $this -> conexion);
    $mcodPc = $consulta -> ret_matriz();  
    $mcodPc  = $mcodPc[0][0] == NULL ? $cod_contro:$mcodPc[0][0];
    return $mcodPc;      
  }
    
  /***********************************************************************************
   * function Insert                                                                 *
   * brief    Hace la insercion a BD con los datos de los tramos de la ruta entre los*
   *          puestos de control.                                                    *
   *                                                                                 *
   **********************************************************************************/
  function Disable()
  {
    $mRows = $_POST[rows];
    $mRuta = $_POST[ruta];
    $mNomRuta = $_POST[rutanom];
    $mConsect = $_POST[consect];
    $mControA = $_POST[in];
    $mControB = $_POST[out];
    $mKilomet = $_POST[km];
    $mVelocid = $_POST[vl];
    $mTiempox = $_POST[tm];
    $mUsuario = $_POST[usuario];
    $UpDate = $_POST[UpDate];
    
    for($b = 1, $a = 0 ;$a <= $mRows - 1; $a++)
    {
      $mQuery = "UPDATE  ".BASE_DATOS.".tab_tramos_rutasx 
                         SET  ind_estado = '0',
                              usr_modifi = '".$mUsuario ."',
                              fec_modifi = NOW()
                       WHERE
                              cod_rutasx = '".$mRuta[$a]."' AND
                              cod_consec = '".$mConsect[$a]."' AND
                              cod_contra = '".$mControA[$a]."' AND
                              cod_contrb = '".$mControB[$a]."'";
                                                                
      $mDisable = new Consulta($mQuery, $this -> conexion,"R");
      //echo "<br>";
      $b++;
    }
   
    //------------------------------------------------------------------------------------------------------
    $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Volver</a></b>";
    $mensaje = "Se han <b>Desactivado</b> los tramos para la ruta:  <br><b>".$mNomRuta[0]."</b> <br>".$link;
    
    $mens = new mensajes();
    $mens -> correcto("CONSTRUCCIÓN DE TRAMOS",$mensaje);
   
  }

}//FIN CLASE Proc_traylers
     $proceso = new Tram_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
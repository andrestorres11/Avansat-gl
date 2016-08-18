<?php

class Proc_despac
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
  
  ini_set( 'memory_limit', '1024M' ); 
 
  if(!isset($GLOBALS[opcion]) || $GLOBALS[opcion] == "0" )
    $this -> Buscar();
  else
  {
      switch($GLOBALS[opcion])
      {
        case "1":
          $this -> Listar();
          break;
        case "3":
          $this -> Datos();
          break;

      }
  }
 }

 function Buscar()
 {
  $datos_usuario = $this -> usuario -> retornar();

   $feactual = date("Y-m-d");
   
   echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_numero_despac.js\"></script>\n"; 
  
   $formulario = new Formulario ("index.php","post","DESPACHOS","form_insert","","");
   $formulario -> linea("Ingrese los Criterios de Busqueda",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> texto("Nro.Placa: ","text","num_placax\" id=\"num_placaxID",1,6,6,"","");
   $formulario -> texto("Nro. Despacho: ","text","num_despac\" id=\"num_despacID",1,10,11,"","");
   $formulario -> nueva_tabla();
   $formulario -> linea("Seleccione el Rango de Fechas.",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> caja ("Filtrar Por Fechas (S/N)","ind_fec\" id=\"ind_fecID","1","1",1);
   $formulario -> fecha_calendar("Fecha Inicial","fecini","form_insert",$feactual,"yyyy/mm/dd",0);
   $formulario -> fecha_calendar("Fecha Final","fecfin","form_insert",$feactual,"yyyy/mm/dd",1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> botoni("Buscar","ValidaFiltros();",1);
   $formulario -> cerrar();

 }

 function Listar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $GLOBALS[fecbus] = str_replace("/","-",$GLOBALS[fecbus]);

  $fechaini = str_replace("/","-",$GLOBALS[fecini])." 00:00:00";
  $fechafin = str_replace("/","-",$GLOBALS[fecfin])." 23:59:59";

  $query = "SELECT a.num_despac,d.num_placax,a.fec_despac,
                   CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),')') as nom_origen,
  		           CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),')') as nom_destin,
  		           e.cod_tercer as doc_conduc,e.abr_tercer as nom_conduc,
				   e.num_telmov as cel_conduc,e.num_telef1 as tel_conduc,
				   b.abr_tercer as nom_transp,
				   IF(a.fec_salida IS NULL,'POR SALIR',a.fec_salida) AS fec_salida,
				   IF(a.fec_llegad IS NULL,'EN TRANSITO',a.fec_llegad) AS fec_llegad,
				   f.nom_noveda,a.fec_ultnov as fec_noveda,a.usr_ultnov as usr_noveda,
				   g.obs_contro as nom_notaco,g.fec_contro as fec_notaco,
				   IF(a.ind_anulad = 'R','Activo','Anulado') as ind_estado
              FROM ".BASE_DATOS.".tab_tercer_tercer b,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_tercer_tercer e,
                   ".BASE_DATOS.".tab_genera_ciudad j,
                   ".BASE_DATOS.".tab_genera_depart k,
                   ".BASE_DATOS.".tab_genera_ciudad m,
                   ".BASE_DATOS.".tab_genera_depart n,
				   ".BASE_DATOS.".tab_despac_despac a 
				   LEFT JOIN ".BASE_DATOS.".tab_despac_noveda c ON a.num_despac = c.num_despac AND a.cod_ultnov = c.cod_noveda
				   LEFT JOIN ".BASE_DATOS.".tab_genera_noveda f ON f.cod_noveda = c.cod_noveda
			       LEFT JOIN ".BASE_DATOS.".tab_despac_contro g ON a.num_despac = g.num_despac AND a.cod_consec = g.cod_consec
             WHERE d.cod_conduc = e.cod_tercer AND
             	   a.cod_ciuori = j.cod_ciudad AND
                   j.cod_depart = k.cod_depart AND
                   a.cod_ciudes = m.cod_ciudad AND
                   m.cod_depart = n.cod_depart AND
                   a.num_despac = d.num_despac AND
				   d.cod_transp = b.cod_tercer 
                   
	   ";

  if($GLOBALS[num_placax])
   $query .= " AND d.num_placax = '".$GLOBALS[num_placax]."'";
  if($GLOBALS[num_despac])
   $query .= " AND a.num_despac = ".$GLOBALS[num_despac]."";
  if($GLOBALS[ind_fec])
   $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'"; 
 
  $query = $query." GROUP BY 1";

  $consulta = new Consulta($query, $this -> conexion);
  $despachos = $consulta -> ret_matriz();

 
  $formulario = new Formulario ("index.php","post","DESPACHOS","form_insert","","");
    
  if( sizeof($despachos) > 0 )
  {
    $formulario -> linea("Se Encontraron ".sizeof($despachos)." Despacho(s) ",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("Nro.Despacho",0,"t");
    $formulario -> linea("Fecha",0,"t");
    $formulario -> linea("Nro.Placa",0,"t");
    $formulario -> linea("Origen",0,"t");
    $formulario -> linea("Destino",0,"t");
    $formulario -> linea("C.C",0,"t");
    $formulario -> linea("Conductor",0,"t");
    $formulario -> linea("Celular",0,"t");
    $formulario -> linea("Teléfono",0,"t");
    $formulario -> linea("Transportadora",0,"t");
    $formulario -> linea("Fecha Salida",0,"t");
    $formulario -> linea("Fecha Llegada",0,"t");
    $formulario -> linea("Ult.Novedad",0,"t");
    $formulario -> linea("Fec.Novedad",0,"t");
    $formulario -> linea("Usr.Novedad",0,"t");
    $formulario -> linea("Ultima Nota",0,"t");
    $formulario -> linea("Fecha Nota",0,"t");  
    $formulario -> linea("Estado",1,"t");

     
    for($i = 0; $i < sizeof($despachos); $i++)
    {
     /*--------------------HIPERVINCULO----------------------*/
     $formulario -> oculto("despac",$despachos[$i][num_despac],0);
     $formulario -> linea("<a href='index.php?cod_servic=3302&window=central&despac=".$despachos[$i][num_despac]."&opcion=1'>".$despachos[$i][num_despac]."</a>",0,"i");
     /*--------------------HIPERVINCULO----------------------*/
     $formulario -> linea($despachos[$i][fec_despac],0,"i");
     $formulario -> linea($despachos[$i][num_placax],0,"i");
     $formulario -> linea($despachos[$i][nom_origen],0,"i");
     $formulario -> linea($despachos[$i][nom_destin],0,"i");
     $formulario -> linea($despachos[$i][doc_conduc],0,"i");
     $formulario -> linea($despachos[$i][nom_conduc],0,"i");
     $formulario -> linea($despachos[$i][cel_conduc],0,"i");
     $formulario -> linea($despachos[$i][tel_conduc],0,"i");
     $formulario -> linea($despachos[$i][nom_transp],0,"i");
     $formulario -> linea($despachos[$i][fec_salida],0,"i");
     $formulario -> linea($despachos[$i][fec_llegad],0,"i");
     $formulario -> linea($despachos[$i][nom_noveda],0,"i");
     $formulario -> linea($despachos[$i][fec_noveda],0,"i");
     $formulario -> linea($despachos[$i][usr_noveda],0,"i");
     $formulario -> linea($despachos[$i][nom_notaco],0,"i");
     $formulario -> linea($despachos[$i][fec_notaco],0,"i");
     $formulario -> linea($despachos[$i][ind_estado],1,"i");
    }

    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion",3,0);
    $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
    $formulario -> cerrar();
  }
  else
  {
    $formulario -> linea("No se Encontraron Resultados",1,"t2");
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion",0,0);
    $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
     $formulario -> botoni("Atras","form_insert.submit();",1);
    $formulario -> cerrar();
  }
}


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $mRuta = array("link"=>0, "finali"=>0, "opcurban"=>0, "lleg"=>NULL, "tie_ultnov"=>NULL);#Fabian

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> aplica,$this -> conexion);
   $listado_prin  -> Encabezado($GLOBALS[despac],$datos_usuario,0,$mRuta);
   #$listado_prin  -> PlanDeRuta($GLOBALS[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$GLOBALS[despac],0);
   $formulario -> oculto("opcion",0,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Atras","form_item.submit();",1);

   $formulario -> cerrar();
 }

}

$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>

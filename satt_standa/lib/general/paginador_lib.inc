<?php

class Paginador_listado
{
 var $conexion;//Conexion de Base de Datos
 var $inipag;//Pagina Inicial
 var $canpag;//Cantidad de resultados por pagina
 var $totalr;//Total resultados del Query
 var $totalp;//Total de paginas a mostrar
 var $actuap;//Pagina Actual
 var $opcion;//Opcion actual
 var $cod_servic;//Codigo del servicio
 var $ranpag;//Cantidad de paginas a mostrar del paginador
 var $query;//Query que se ejecuta en el paginador

 function __construct($opcion,$servic,$conexion)
 {
  $this -> canpag = CAN_RESULT_PAGINA;
  $this -> ranpag = 19;
  $this -> opcion = $opcion;
  $this -> cod_servic = $servic;
  $this -> conexion = $conexion;
 }

 function ejecPaginador($pagina_actual,$query)
 {
  $this -> inipag = $pagina_actual * $this -> canpag;
  $this -> actuap = $pagina_actual;

  $consulta = new Consulta($query, $this -> conexion);
  $this -> totalr = $consulta -> ret_num_rows();
  $this -> totalp = intval($this -> totalr / $this -> canpag);

  $query .= " LIMIT ".$this -> inipag.",".$this -> canpag."";
  $this -> query = $query;
  $consulta = new Consulta($query, $this -> conexion);
  $matriz_resultado = $consulta -> ret_matriz();

  return $matriz_resultado;
 }

 function getPaginas($formulario,$nompagina,$variables = NULL)
 {
  if($this -> totalp)
  {
   if($variables)
    for($i = 0; $i < sizeof($variables); $i++)
     $adicion .= "&".$variables[$i]["nomvar"]."=".$variables[$i]["valvar"];

   $formulario -> nueva_tabla();

   if ($this -> actuap != 0)
   {
    $url = $this -> actuap - 1;
    $links = "<a href=\"index.php?cod_servic=".$this -> cod_servic."&window=central&opcion=".$this -> opcion."&".$nompagina."=".$url.$adicion." \"target=\"centralFrame\">&laquo; Anterior</a>";
   }
   else
    $links = "&laquo; Anterior";

   $intervalo = (ceil($this -> ranpag / 2)-1);
   $itemini = $this -> actuap - $intervalo;
   $itemfin = $this -> actuap + $intervalo;

   if($itemini < $intervalo)
    $itemini = 0;
   if($itemfin > ($this -> totalp - $intervalo))
    $itemfin = $this -> totalp;

   for ($i = $itemini; $i <= $itemfin; $i++)
   {
    if ($i == $this -> actuap)
     $links .= " ".($i + 1);
    else
     $links .= "<a href=\"index.php?cod_servic=".$this -> cod_servic."&window=central&opcion=".$this -> opcion."&".$nompagina."=".$i.$adicion." \"target=\"centralFrame\"> ".($i + 1)."</a>";
   }

   if ($this -> actuap < $this -> totalp)
   {
    $url = $this -> actuap + 1;
    $links .= "<a href=\"index.php?cod_servic=".$this -> cod_servic."&window=central&opcion=".$this -> opcion."&".$nompagina."=".$url.$adicion." \"target=\"centralFrame\"> Siguiente &raquo;</a>";
   }
   else
    $links .= " Siguiente &raquo;";

   if($this -> actuap == $this -> totalp)
    $infopag = "Resultados del ".(($this -> actuap * $this -> canpag) + 1)." Hasta el ".$this -> totalr;
   else
    $infopag = "Resultados del ".(($this -> actuap * $this -> canpag) + 1)." Hasta el ".((($this -> actuap + 1) * $this -> canpag));

   $formulario -> linea($links." :: ".$infopag,1,"e");
  }
 }
}
?>
<?php
/****************************************************************************
NOMBRE:   MODULO_NOVEDA_LIS.PHP
FUNCION:  LISTAR NOVEDADES
****************************************************************************/
  class Proc_noveda
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
      if(!isset($_REQUEST["opcion"]))
        $this -> Buscar();
      else
      {
        switch($_REQUEST["opcion"])
        {
          case "2":
            $this -> Resultado();
          break;
        }//FIN SWITCH
      }// FIN ELSE GLOBALS OPCION
    }//FIN FUNCION PRINCIPAL

    function Buscar()
    {
      $datos_usuario = $this -> usuario -> retornar();
      $usuario=$datos_usuario["cod_usuari"];

      echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/noveda.js\"></script>\n";
      $formulario = new Formulario ("index.php","post","BUSCAR Y LISTAR NOVEDADES","form_list");
      $formulario -> linea("Defina la Condici&oacute;n de Busqueda",1,"t2");
      $formulario -> nueva_tabla();
      $formulario -> texto ("Novedad","text","noveda",1,50,255,"","");
      $formulario -> nueva_tabla();
      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("opcion",2,0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
      $formulario -> boton("Buscar","button\" onClick=\"aceptar_lis() ",0);
      $formulario -> boton("Todas","button\" onClick=\"form_list.submit() ",0);
      $formulario -> cerrar();
    }//FIN FUNCION ACTUALIZAR

    function Resultado()
    {
      $datos_usuario = $this -> usuario -> retornar();
      $usuario=$datos_usuario["cod_usuari"];
      
      $query = "SELECT a.cod_noveda,UPPER(a.nom_noveda),if(a.ind_alarma = 'S', 'SI', 'NO'), if(a.ind_tiempo = '1', 'SI', 'NO'), 
                       if(a.nov_especi = '1', 'SI', 'NO'), if(a.ind_manala = '1', 'SI', 'NO'), if(a.ind_fuepla = '1', 'SI', 'NO'), 
                       if(a.ind_notsup = '1', 'SI', 'NO'), IF(b.nom_operad IS NULL, '---',b.nom_operad), IF(a.cod_homolo IS NULL , '---', a.cod_homolo), IF(a.ind_visibl = '1', 'SI', 'NO'),
                       IF(a.ind_insveh = '1', 'SI', 'NO'), IF(a.ind_ealxxx = '1', 'SI', 'NO'), IF(a.ind_limpio = '1', 'SI', 'NO'),
                       c.nom_etapax 
                  FROM ".BASE_DATOS.".tab_genera_noveda a 
            INNER JOIN ".BASE_DATOS.".tab_genera_etapax c 
                    ON a.cod_etapax = c.cod_etapax 
             LEFT JOIN ".CENTRAL.".tab_genera_opegps b 
                    ON a.cod_operad = b.cod_operad
                 WHERE nom_noveda LIKE '%$_REQUEST[noveda]%'
              ORDER BY 2";
      
      $consec = new Consulta($query, $this -> conexion);
      $matriz = $consec -> ret_matriz();

      $formulario = new Formulario ("index.php","post","Listado de Novedades","form_item");
      $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Novedade(s)",0,"t2");
      $formulario -> nueva_tabla();

      if(sizeof($matriz) > 0)
      {
        $formulario -> linea("Codigo",0,"t");
        $formulario -> linea("Descripcion",0,"t");
        $formulario -> linea("Etapa",0,"t");
        $formulario -> linea("Genera Alerta",0,"t");
        $formulario -> linea("Solicita Tiempos",0,"t");
        $formulario -> linea("Novedad Especial",0,"t");
        $formulario -> linea("Mantiene Alarma",0,"t");
        $formulario -> linea("Fuera de Plataforma",0,"t");
        $formulario -> linea("Notifica Supervisor",0,"t");
        $formulario -> linea("Inspección Vehicular",0,"t");
        $formulario -> linea("Visible Esferas",0,"t");
        $formulario -> linea("Limpio",0,"t");
        $formulario -> linea("Operador Novedad",0,"t");
        $formulario -> linea("Código homologación",0,"t");
        $formulario -> linea("Visibilidad",1,"t");

        for($i=0;$i<sizeof($matriz);$i++)
        {
          $formulario -> linea($matriz[$i][0],0,"i");
          $formulario -> linea($matriz[$i][1],0,"i");
          $formulario -> linea($matriz[$i][nom_etapax],0,"i");
          $formulario -> linea($matriz[$i][2],0,"i");
          $formulario -> linea($matriz[$i][3],0,"i");
          $formulario -> linea($matriz[$i][4],0,"i");
          $formulario -> linea($matriz[$i][5],0,"i");
          $formulario -> linea($matriz[$i][6],0,"i");
          $formulario -> linea($matriz[$i][7],0,"i");
          $formulario -> linea($matriz[$i][11],0,"i");
          $formulario -> linea($matriz[$i][12],0,"i");
          $formulario -> linea($matriz[$i][13],0,"i");
          $formulario -> linea($matriz[$i][8],0,"i");
          $formulario -> linea($matriz[$i][9],0,"i");
          $formulario -> linea($matriz[$i][10],1,"i");
        }//fin for

      }//fin if

      $formulario -> nueva_tabla();
      $formulario -> boton("Volver <==","button\" onClick=\"javascript:history.go(-1)",0);
      $formulario -> nueva_tabla();
      $formulario -> oculto("usuario","$usuario",0);
      $formulario -> oculto("opcion",1,0);
      $formulario -> oculto("valor",$valor,0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
      $formulario -> cerrar();
    }//fin funcion

  }//FIN CLASE PROC_NOVEDA
  $proceso = new Proc_noveda($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
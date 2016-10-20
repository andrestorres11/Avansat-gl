<?php
/****************************************************************************
NOMBRE:   MODULO_NOVEDA_ACT.PHP
FUNCION:  ACTUALIZAR NOVEDADES
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
//********METODOS
 function principal()
 {
  if(!isset($_REQUEST["opcion"]))
    $this -> Buscar();
  else
     {
      switch($_REQUEST["opcion"])
       {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Datos();
          break;
        case "3":
          $this -> Actualizar();
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
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
   $formulario -> boton("Aceptar","button\" onClick=\"form_list.submit() ",0);
   $formulario -> boton("Todas","button\" onClick=\"form_list.submit() ",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
// ind_insveh
  $query = "SELECT a.cod_noveda,UPPER(a.nom_noveda),IF(a.ind_alarma = 'S', 'SI', 'NO'), IF(a.ind_tiempo = '1', 'SI', 'NO'), 
                   IF(a.nov_especi = '1', 'SI', 'NO'), IF(a.ind_manala = '1', 'SI', 'NO'), IF(a.ind_fuepla = '1', 'SI', 'NO'), 
                   IF(a.ind_notsup = '1', 'SI', 'NO'), IF(b.nom_operad IS NULL, '---',b.nom_operad), IF(a.cod_homolo IS NULL , '---', a.cod_homolo), IF(a.ind_visibl = '1', 'SI', 'NO'),
                   IF(a.ind_insveh = '1', 'SI', 'NO'), IF(a.ind_ealxxx = '1', 'SI', 'NO') , IF(a.ind_limpio = '1', 'SI', 'NO'), 
                   c.nom_etapax 
              FROM ".BASE_DATOS.".tab_genera_noveda a 
        INNER JOIN ".BASE_DATOS.".tab_genera_etapax c 
                ON a.cod_etapax = c.cod_etapax 
         LEFT JOIN ".CENTRAL.".tab_genera_opegps b 
                ON a.cod_operad = b.cod_operad 
               AND b.ind_estado = '1'
         	  WHERE nom_noveda LIKE '%$_REQUEST[noveda]%' AND
		 		          cod_noveda != ".CONS_NOVEDA_PCLLEG." AND
									cod_noveda != ".CONS_NOVEDA_ACAEMP." AND
									cod_noveda != ".CONS_NOVEDA_ACAFAR." AND
									cod_noveda != ".CONS_NOVEDA_CAMRUT." AND
              	  cod_noveda != ".CONS_NOVEDA_CAMALA."
      		   ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Listado de Novedades","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Novedade(s)",0,"t2");
   $formulario -> nueva_tabla();

   $formulario -> linea("Codigo",0,"t");
   $formulario -> linea("Descripcion",0,"t");
   $formulario -> linea("Etapa",0,"t");
   $formulario -> linea("Genera Alerta",0,"t");
   $formulario -> linea("Solicita Tiempos",0,"t");
   $formulario -> linea("Novedad Especial",0,"t");
   $formulario -> linea("Mantiene Alerta",0,"t");
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
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&noveda=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
   }

   $formulario -> nueva_tabla();
   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR


 function Datos()
 {
    $inicio[0][0]=0;
    $inicio[0][1]='-';

    $query = "SELECT a.cod_noveda, a.nom_noveda, a.ind_alarma,
                     a.ind_tiempo, a.nov_especi, a.ind_manala,
                     a.ind_fuepla, a.ind_notsup, a.cod_operad, 
                     a.cod_homolo, a.ind_visibl, a.ind_insveh, 
                     a.ind_ealxxx, a.ind_limpio, a.cod_etapax 
              FROM ".BASE_DATOS.".tab_genera_noveda a 
             WHERE cod_noveda = '$_REQUEST[noveda]'";
    $consulta = new Consulta($query, $this -> conexion);
    $matriz = $consulta -> ret_matriz();

    $mEtapas = Proc_noveda::getEtapa( $matriz[0][cod_etapax] );
 
   $formulario = new Formulario ("index.php","post","Actualizar Novedad","form_item");
   $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Novedad",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t","","","RIGHT");
   $formulario -> linea($matriz[0][0],1,"i");

   $formulario -> texto ("Nombre o descripcion de la novedad:","text","nombre",1,50,100,"","".$matriz[0][1]."");

   if($matriz[0][2] == 'S')
     $formulario -> caja ("Genera alerta:","indala","1",1,1);
   else
     $formulario -> caja ("Genera alerta:","indala","1",0,1);

   if($matriz[0][3] == '1')
     $formulario -> caja ("Solicita Tiempos:","indtiemp","1",1,1);
   else
     $formulario -> caja ("Solicita Tiempos:","indtiemp","1",0,1);

   
   if($matriz[0][4] == '1')
     $formulario -> caja ("Novedad Especial:","nov_especi","1",1,1);
   else
     $formulario -> caja ("Novedad Especial:","nov_especi","1",0,1);
   
   if($matriz[0][5] == '1')
     $formulario -> caja ("Mantiene Alarma:","ind_manala","1",1,1);
   else
     $formulario -> caja ("Mantiene Alarma:","ind_manala","1",0,1);

   if($matriz[0][6] == '1')
     $formulario -> caja ("Fuera de Plataforma:","ind_fuepla","1",1,1);
   else
     $formulario -> caja ("Fuera de Plataforma:","ind_fuepla","1",0,1);     
     
   if($matriz[0][7] == '1')
     $formulario -> caja ("Notifica Supervisor:","ind_notsup","1",1,1);
   else
     $formulario -> caja ("Notifica Supervisor:","ind_notsup","1",0,1);  
   
   if($matriz[0][11] == '1')
     $formulario -> caja ("Inspección Vehicular:","ind_insveh","1",1,1);
   else
     $formulario -> caja ("Inspección Vehicular:","ind_insveh","1",0,1); 

   if($matriz[0][12] == '1')
     $formulario -> caja ("Visible Esferas:","ind_ealxxx","1",1,1);
   else
     $formulario -> caja ("Visible Esferas:","ind_ealxxx","1",0,1);

   if($matriz[0][13] == '1')
     $formulario -> caja ("Limpio:","ind_limpio","1",1,1);
   else
     $formulario -> caja ("Limpio:","ind_limpio","1",0,1);


    $formulario -> lista ("Etapa:", "cod_etapax", $mEtapas, 1);   
   
   
   // OPeradores Novedades -------------------------------------------------------------------------------------------------------------
    	$formulario->nueva_tabla();
        $formulario->linea( "Datos Novedades", 1, "t2" );
        
      $formulario->nueva_tabla();      
        $formulario -> lista("Operador Novedad:", "cod_operad", $this -> getOperadores($matriz[0][8]), 0);
        $formulario -> texto("Código de homologación:","text","cod_homolo\"onkeypress=\"return TextInputAlpha( event )",1,10,10,"",$matriz[0][9]);
        
      if($matriz[0][10] == '1')
        $formulario -> caja ("Visibilidad (S/N):","ind_visibl","1",1,1);
      else
        $formulario -> caja ("Visibilidad (S/N):","ind_visibl","1",0,1);        
    // ------------------------------------------------------------------------------------------------------------------------
    
		$formulario->nueva_tabla();
        $formulario->linea( "Filtro de Perfiles	", 1, "t2" );
		
		$perfiles = $this -> getPerfiles();
		
		if( $perfiles )
		{
			$formulario->nueva_tabla();
			foreach( $perfiles as $row )
			{
				if( !isset( $_POST["perfil"][$i] ))
					$checked = $this -> getPermiso( $_REQUEST["noveda"], $row[0] );
				
				if( $_POST["sel_todos"] ) 
					$checked = 1;
				
				
				$formulario->caja( ucwords( strtolower( $row[1] ) ), "perfil[$i]", $row[0], $checked, $end );
				$i++;
				
				if( $i % 2 != 0 ) $end = 1;
				else $end = 0;				
			}
			
			$formulario->caja( "Seleccionar Todos", "sel_todos\" onChange=\"form_item.submit()", 1, $checked, $end );
		}

		$formulario -> nueva_tabla();
		$formulario -> oculto("maximo",$interfaz -> cant_interf,0);
		$formulario -> oculto("usuario","$usuario",0);
		$formulario -> oculto("opcion",2,0);
		$formulario -> oculto("noveda",$_REQUEST["noveda"],0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
		$formulario -> boton("Actualizar","button\" onClick=\"if(confirm('Desea Actualiza la Novedad.?')){form_item.opcion.value = 3; form_item.submit();}",0);
		$formulario -> boton("Cancelar","button\" onClick=\"javascript:history.go(-3)",0);
		$formulario -> cerrar();
	}
	
	function getPermiso( $cod_noveda, $cod_perfil )
	{
		$query = "SELECT 1
				  FROM " . BASE_DATOS . ".tab_perfil_noveda a
				  WHERE a.cod_perfil = '$cod_perfil' AND
						a.cod_noveda = '$cod_noveda' ";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta -> ret_matriz( "i" );
		
		if( $matriz ) return true;
		
		return false;
	}
	
	function getPerfiles()
	{
		$query = "SELECT a.cod_perfil, a.nom_perfil
				  FROM " . BASE_DATOS . ".tab_genera_perfil  a
				  ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta -> ret_matriz( "i" );
		
		return $matriz;
	}
  function getOperadores( $mCod_operad)
  {
    $mQuerySelected = "SELECT a.cod_operad, a.nom_operad
                FROM ".CENTRAL.".tab_genera_opegps  a
               WHERE a.cod_operad = '".$mCod_operad."'
          ORDER BY 2";
    $mConsultSelected = new Consulta($mQuerySelected, $this->conexion);
    $mConsultSelected = $mConsultSelected -> ret_matriz( "i" );
        
    $mQuerySelect = "SELECT a.cod_operad, a.nom_operad
                         FROM ".CENTRAL.".tab_genera_opegps  a
                        WHERE a.ind_estado = '1'
                        ORDER BY 2";
    $mConsultSelect = new Consulta($mQuerySelect, $this->conexion);
    $mConsultSelect = $mConsultSelect -> ret_matriz( "i" );
    return array_merge($mConsultSelected, array(array("--","--")), $mConsultSelect );
  }
	
 function Actualizar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario = $datos_usuario["cod_usuari"];

  $fec_actual = date("Y-m-d H:i:s");
   //valida el indicador
   if($_REQUEST["indala"] == 1)
     $alarma = 'S';
   else
     $alarma = 'N';

   //valida el indicador de solicitud de tiempos por novedad
     if($_REQUEST["indtiemp"])
     $tiempo = $_REQUEST["indtiemp"];
     else
     $tiempo = 0;


 //valida novedad especial
  if($_REQUEST["nov_especi"])
   $nov_especi = 1;
  else
   $nov_especi = 0;

  //valida Mantiene Alamra
  if($_REQUEST["ind_manala"])
   $ind_manala = 1;
  else
   $ind_manala = 0;
   
  //valida Fuera de Plataforma
  if($_REQUEST["ind_fuepla"])
  {
    $ind_fuepla = 1;
    $tiempo = 1;
  }  
  else
   $ind_fuepla = 0;   

  //valida Notifica Supervisor
  if($_REQUEST["ind_notsup"])
  {
    $ind_notsup = 1;
    // $ind_manala = 1;
  }  
  else
   $ind_notsup = 0;      
  
  //valida Notifica Supervisor
  if($_REQUEST["ind_insveh"])
    $ind_insveh = 1;
  else
   $ind_insveh = 0; 
  
  //valida visible esferas
  if($_REQUEST["ind_ealxxx"])
    $ind_ealxxx = 1;
  else
   $ind_ealxxx = 0;

  //valida indicador limpio
  if($_REQUEST["ind_limpio"])
    $ind_limpio = 1;
  else
   $ind_limpio = 0; 
  
  $cod_operad = $_REQUEST["cod_operad"]; 
  $cod_homolo = $_REQUEST["cod_homolo"];   
  $ind_visibl = $_REQUEST["ind_visibl"] == NULL ? '0' : $_REQUEST["ind_visibl"];    

  //query de insercion de despacho
  $query = "UPDATE ".BASE_DATOS.".tab_genera_noveda
               SET nom_noveda = '".$_REQUEST["nombre"]."',
                   ind_alarma = '".$alarma."',
                   ind_tiempo = '".$tiempo."',
                   usr_modifi = '".$usuario."',
                   ind_manala = '".$ind_manala."',
                   ind_fuepla = '".$ind_fuepla."',
                   ind_notsup = '".$ind_notsup."',
                   fec_modifi = '".$fec_actual."',
                   nov_especi = '".$nov_especi."',
                   cod_operad = '".$cod_operad."',
                   cod_homolo = '".$cod_homolo."',
                   ind_visibl = '".$ind_visibl."',
                   ind_insveh = '".$ind_insveh."',
                   ind_ealxxx = '".$ind_ealxxx."',
                   ind_limpio = '".$ind_limpio."',
                   cod_etapax = '".$_REQUEST[cod_etapax]."'
             WHERE cod_noveda = '".$_REQUEST["noveda"]."'
           ";
           
  $insercion = new Consulta($query, $this -> conexion,"BR");

  $novedaint = $_REQUEST["novedaint"];
  $operad = $_REQUEST["operad"];
  $homoloini = $_REQUEST["homoloini"];

  //Manejo de la Interfaz Aplicaciones SAT
  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$_REQUEST["usuario"],$this -> conexion);

  for($i = 0; $i < $interfaz -> totalact; $i++)
  {
   if($novedaint[$i] && $operad[$i] == $interfaz -> interfaz[$i]["operad"])
   {
    if($homoloini[$i] != $novedaint[$i])
    {
     $resultado_sat = $interfaz -> actHomoloNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST["noveda"],$novedaint[$i]);

     if($resultado_sat["Confirmacion"] == "OK")
      $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Novedad Se Homologo en la Interfaz  <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
     else
      $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Insertar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
    }
   }
   else
   {
    if($homoloini[$i])
    {
     $novedaint[$i] = $homoloini[$i];

     $resultado_sat = $interfaz -> eliHomoloNoveda($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST["noveda"],$novedaint[$i]);

     if($resultado_sat["Confirmacion"] == "OK")
      $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Elimino la Homologacion de la Novedad en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
     else
      $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Eliminar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
    }
   }
  }
		//Filtro de Perfiles.
		$query = "DELETE FROM " . BASE_DATOS . ".tab_perfil_noveda
				  WHERE cod_noveda = '$_REQUEST[noveda]' ";

        $consulta = new Consulta( $query, $this->conexion, "R" );
		
		$perfiles = $_POST[perfil];
		
		if( $perfiles )
		foreach( $perfiles as $row )
		{
			$insert = "INSERT INTO  " . BASE_DATOS . ".tab_perfil_noveda 
						(
							cod_perfil , cod_noveda
						)
						VALUES 
						(
							'$row',  '$_REQUEST[noveda]'
						)";
			
			$consulta = new Consulta( $insert, $this -> conexion, "R" );
		}	

  if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST["cod_servic"]." \"target=\"centralFrame\">Actualizar Otra Novedad</a></b>";

     $mensaje =  "La Novedad <b>".$_REQUEST["nombre"]."</b> Se Actualizo con Exito".$mensaje_sat.$link_a;
     $mens = new mensajes();
     $mens -> correcto("ACTUALIZAR NOVEDADES",$mensaje);
    }
 }

    /*! \fn: getEtapa
     *  \brief: Trae las Etapas de un despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 26/06/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: 
     */
    private function getEtapa( $mCodEtapax )
    {
      $mSql = " (
                  SELECT a.cod_etapax, a.nom_etapax 
                    FROM ".BASE_DATOS.".tab_genera_etapax a 
                   WHERE a.cod_etapax = '".$mCodEtapax."' 
                )
                UNION 
                (
                  SELECT a.cod_etapax, a.nom_etapax 
                    FROM ".BASE_DATOS.".tab_genera_etapax a 
                   WHERE a.ind_estado = 1 
                     AND a.cod_etapax != '".$mCodEtapax."' 
                ) ";
      $mConsult = new Consulta($mSql, $this -> conexion);
      return $mSql = $mConsult -> ret_matrix('i');
    }

}//FIN CLASE PROC_NOVEDA
   $proceso = new Proc_noveda($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
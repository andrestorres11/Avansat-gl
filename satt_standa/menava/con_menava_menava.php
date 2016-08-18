<?php
/****************************************************************************
NOMBRE:   MODULO_CONTRO_LIS.PHP
FUNCION:  LISTAR PUESTOS DE CONTROL
****************************************************************************/
class Proc_contro
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
  if(!isset($GLOBALS[opcion]))
    $this -> Resultado();
  else
     {
      switch($GLOBALS[opcion])
       {
          case "1":
          $this -> Resultado();
          break;

	  case "2":
          $this -> Insertar();
          break;

       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Resultado()
 {
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/menava.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","CONFIGURACION","form_item");

   $formulario -> nueva_tabla();
   $formulario -> linea("Configurar los Parametros Para el Envio de Mensajes de Texto",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Descripci&oacute;n del Mensaje",0,"t");
   $formulario -> linea("Manifiesto, Placa, Puesto de Control, Demora (Min)",1,"i");

   $query = "SELECT a.cod_tercer,a.abr_tercer
   		       FROM ".BASE_DATOS.".tab_tercer_tercer a,
   		    		".BASE_DATOS.".tab_tercer_emptra b
   		      WHERE a.cod_tercer = b.cod_tercer
   		    		ORDER BY 2
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $transpor = $consulta -> ret_matriz();

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n de Parametros",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea ("NIT",0,"t");
   $formulario -> linea ("Transportadora",0,"t");
   $formulario -> linea ("",0,"t");
   $formulario -> linea ("Intervalo de Tiempo (Min)",0,"t");
   $formulario -> linea ("",0,"t");
   $formulario -> linea ("Activa",1,"t");

   for($i = 0; $i < sizeof($transpor); $i++)
   {
   	$query = "SELECT a.val_diftim,a.ind_estado
	       	    FROM ".CENTRAL.".tab_mensaj_bdsata a
	           WHERE a.nom_bdsata = '".BASE_DATOS."' AND
		    	     a.cod_transp = '".$transpor[$i][0]."'
	         ";

    $consulta = new Consulta($query, $this -> conexion);
    $estactua = $consulta -> ret_matriz();

    $formulario -> linea($transpor[$i][0],0,"i");
    $formulario -> linea($transpor[$i][1],0,"i");
    $formulario -> texto("","text","diftim[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,4,4,"",$estactua[0][0]);
    $formulario -> caja("","transp[$i]",$transpor[$i][0],$estactua[0][1],1);
    $formulario -> oculto("transpvef[$i]",$transpor[$i][0],0);
    $formulario -> oculto("estadovef[$i]",$estactua[0][1],0);
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("maximo",sizeof($transpor),0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> nueva_tabla();
   $formulario -> boton("Aceptar","button\" onClick=\"configuracion(form_item)",1);
   $formulario -> cerrar();

 }

 function Insertar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $diftim = $GLOBALS[diftim];
   $transp = $GLOBALS[transp];
   $transpvef = $GLOBALS[transpvef];
   $estadovef = $GLOBALS[estadovef];

   $consulta = new Consulta ("SELECT NOW()", $this -> conexion,"BR");

   for($i = 0; $i < $GLOBALS[maximo]; $i++)
   {
   	if($transp[$i])
   	{
     $query = "SELECT a.cod_transp
	       	     FROM ".CENTRAL.".tab_mensaj_bdsata a
	            WHERE a.nom_bdsata = '".BASE_DATOS."' AND
		    	      a.cod_transp = '".$transp[$i]."'
	          ";

     $consulta = new Consulta($query, $this -> conexion);
     $estactua = $consulta -> ret_matriz();

     $query = "SELECT a.nom_tercer
     		     FROM ".BASE_DATOS.".tab_tercer_tercer a
     		    WHERE a.cod_tercer = '".$transp[$i]."'
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $nomtra = $consulta -> ret_matriz();
     if(!$diftim[$i] || $diftim[$i]=='')
     $diftim[$i]= '0';
     
     if(!$estactua)
      $query = "INSERT INTO ".CENTRAL.".tab_mensaj_bdsata
    					    (cod_transp,nom_bdsata,nom_transp,val_diftim,
    					     ind_estado,usr_creaci,fec_creaci)
    			     VALUES ('".$transp[$i]."','".BASE_DATOS."','".$nomtra[0][0]."',
    			  		     ".$diftim[$i].",'1','".$usuario."',NOW())
    		   ";
     else
      $query = "UPDATE ".CENTRAL.".tab_mensaj_bdsata
    			 SET nom_transp = '".$nomtra[0][0]."',
    			 	 val_diftim = ".$diftim[$i].",
    			 	 ind_estado = '1',
    			 	 usr_modifi = '".$usuario."',
    			 	 fec_modifi = NOW()
    		   WHERE cod_transp = '".$transp[$i]."' AND
    		   		 nom_bdsata = '".BASE_DATOS."'
    		 ";

      $consulta = new Consulta ($query, $this -> conexion,"R");
   	}
   	else if($estadovef[$i])
   	{
   	 $query = "UPDATE ".CENTRAL.".tab_mensaj_bdsata
    			  SET ind_estado = '0',
    			 	  val_diftim = 0,
    			 	  usr_modifi = '".$usuario."',
    			 	  fec_modifi = NOW()
    		    WHERE cod_transp = '".$transpvef[$i]."' AND
    		   		  nom_bdsata = '".BASE_DATOS."'
    		 ";

     $consulta = new Consulta ($query, $this -> conexion,"R");
   	}
   }

   if($consulta = new Consulta ("COMMIT", $this -> conexion))
   {
    $mensaje =  "Se Actualizo la Informaci&oacute;n Para la Configuraci&oacute;n de Mensajes Exitosamente.";
    $mens = new mensajes();
    $mens -> correcto("CONFIGURACION DE MENSAJES",$mensaje);
   }
 }

}//FIN CLASE PROC_CONTRO
     $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
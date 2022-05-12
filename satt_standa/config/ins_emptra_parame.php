<?php

class Ins_emptra_parame
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  @include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
  $this -> principal();
 }

 function principal()
 {
  if(!isset($_REQUEST[opcion]))
     $this -> Captura();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "1":
         $this -> Captura();
        break;
        case "2":
         $this -> Actualizar();
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Captura()
 {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];

     $query = "SELECT  ind_estcli,ind_estcon,ind_estveh,ind_estter,
                       ind_califi,obs_planru,val_multpc,ind_fincal,
                       ind_remdes,ind_desurb,ind_restra,ind_inidet,
                       ind_feplle,ind_opesta,ind_opepro
                 FROM ".BASE_DATOS.".tab_config_parame";

     $consulta = new Consulta($query, $this -> conexion);
     $matriz = $consulta -> ret_matriz();

     $query = "SELECT  cod_emptra,nom_razsoc,tel_ofipri,dir_ofupri,
                 	   dir_emailx,nom_contac,cod_paisxx 
                 FROM ".BASE_DATOS.".tab_config_parame";

     $consulta = new Consulta($query, $this -> conexion);
     $infemptr = $consulta -> ret_matriz();

     $query = "SELECT  cod_paisxx, nom_paisxx
                 FROM ".BASE_DATOS.".tab_genera_paises";

     $consulta = new Consulta($query, $this -> conexion);
     $paisesxx = $consulta -> ret_matriz();


     //formulario de insercion
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/parame.js\"></script>\n";
     $formulario = new Formulario ("index.php","post","PARAMETROS DEL SISTEMA","form_transpor");

     $formulario -> nueva_tabla();
     $formulario -> linea("Informaci&oacute;n Básica de la Empresa",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> texto("NIT","text","nitemp\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,9,9,"",$infemptr[0][0]);
     $formulario -> texto("Raz&oacute;n Social","text","razsoc",1,60,100,"",$infemptr[0][1]);
     $formulario -> texto("Telefono Principal","text","telpri",0,15,30,"",$infemptr[0][2]);
     $formulario -> texto("Direcci&oacute;n Principal","text","dirpri",1,60,100,"",$infemptr[0][3]);
     $formulario -> texto("E-mail","text","dirmai",0,60,100,"",$infemptr[0][4]);
     $formulario -> texto("Contacto","text","contac",1,30,55,"",$infemptr[0][5]);
     $formulario -> lista_value("País","cod_paisxx",$paisesxx,0,1,$infemptr[0][6]);
	 $formulario -> nueva_tabla();
     $formulario -> linea("Restricciones",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> caja ("Ingresar Transportadoras como Inactivas(S/N)","estper","1",$matriz[0][0],0);
     $formulario -> caja ("Ingresar Conductores como Inactivos(S/N)","estcon","1",$matriz[0][1],1);
     $formulario -> caja ("Ingresar Vehiculos como Inactivos(S/N)","estveh","1",$matriz[0][2],0);
     $formulario -> caja ("Ingresar Terceros como Inactivos(S/N)","estter","1",$matriz[0][3],1);
     $formulario -> caja ("Validar Calificacion del Conductor en Despacho (S/N)","califi","1",$matriz[0][4],0);
     $formulario -> caja ("Comparar Tiempos Planeados con Ejecutados en los Despachos Finalizados (S/N)","fincal","1",$matriz[0][7],1);
     $formulario -> caja ("Activar el Uso de Remitentes/Destinatarios en el Despacho (S/N)","remdes","1",$matriz[0][8],0);
     $formulario -> caja ("Activar el Manejo de Destinatarios con Puestos de Control Urbanos (S/N)","desurb","1",$matriz[0][9],1);
     $formulario -> caja ("Activar Manejo de Alarmas con Responsabilidad de la Transportadora (S/N)","restra","1",$matriz[0][10],0);
     $formulario -> caja ("Cargar la Opci&oacute;n de Despachos en Transito al Entrar a la Aplicaci&oacute;n (S/N)","inidet","1",$matriz[0][11],1);
	 $formulario -> caja ("Activar C&aacute;lculo de tiempos Fecha Planeada de Llegada (S/N)","feplle","1",$matriz[0][12],1);

     $formulario -> nueva_tabla();
     $formulario -> linea("Visualizacion y gestión de operadores GPS",1,"t2");
     $formulario -> nueva_tabla();
     $formulario -> caja ("Operadores Estandar","opesta","1",$matriz[0][13],0);
     $formulario -> caja ("Operadores Propios","opepro","1",$matriz[0][14],1);

     $formulario -> nueva_tabla();
     $formulario -> linea("Datos Adicionales",1,"t2");
     $formulario -> nueva_tabla();
     $formulario -> texto ("Valor de Multa por Puesto de Control \$","text","multa",1,10,10,"",$matriz[0][6]);
     $formulario -> texto ("Observaciones Plan de Ruta","textarea","obs_planru",1,50,3,"",$matriz[0][5]);
     
     


     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",1,0);
     $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
     $formulario -> botoni("Aceptar","aceptar(document.form_transpor)",0);
     $formulario -> cerrar();
 }

 function Actualizar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  if($_REQUEST[estper]!= 1)
    $_REQUEST[estper]=0;
   if($_REQUEST[estcon]!= 1)
    $_REQUEST[estcon]=0;
  if($_REQUEST[estveh]!= 1)
    $_REQUEST[estveh]=0;
  if($_REQUEST[estter]!= 1)
    $_REQUEST[estter]=0;
  if(!$_REQUEST[califi])
    $_REQUEST[califi]=0;
  if(!$_REQUEST[fincal])
    $_REQUEST[fincal]=0;
  if(!$_REQUEST[remdes])
    $_REQUEST[remdes]=0;
  if(!$_REQUEST[desurb])
    $_REQUEST[desurb]=0;
  if(!$_REQUEST[restra])
    $_REQUEST[restra]=0;
  if(!$_REQUEST[inidet])
    $_REQUEST[inidet]=0;
  if(!$_REQUEST[feplle])
    $_REQUEST[feplle]=0;    

    $query = "SELECT a.cod_aplica
                FROM ".BASE_DATOS.".tab_config_parame a
              ";

    $consulta = new Consulta($query, $this -> conexion);
    $matriz = $consulta -> ret_matriz();

    if($matriz)
    {
          $query = "UPDATE ".BASE_DATOS.".tab_config_parame
                        SET cod_emptra = '$_REQUEST[nitemp]',
                            nom_razsoc = '$_REQUEST[razsoc]',
                            tel_ofipri = '$_REQUEST[telpri]',
                            dir_ofupri = '$_REQUEST[dirpri]',
                            dir_emailx = '$_REQUEST[dirmai]',
                            nom_contac = '$_REQUEST[contac]',
                            cod_paisxx = '$_REQUEST[cod_paisxx]',
                            val_multpc = '$_REQUEST[multa]',
                            ind_estcli = '$_REQUEST[estper]',
                            ind_estcon = '$_REQUEST[estcon]',
                            ind_estveh = '$_REQUEST[estveh]',
                            ind_estter = '$_REQUEST[estter]',
                            ind_califi = '$_REQUEST[califi]',
                            obs_planru = '$_REQUEST[obs_planru]',
                            ind_fincal = '$_REQUEST[fincal]',
                            ind_remdes = '$_REQUEST[remdes]',
                            ind_desurb = '$_REQUEST[desurb]',
                            ind_restra = '$_REQUEST[restra]',
                            ind_inidet = '$_REQUEST[inidet]',
                            ind_feplle = '$_REQUEST[feplle]',
                            ind_opesta = '$_REQUEST[opesta]',
                            ind_opepro = '$_REQUEST[opepro]',
                            usr_modifi = '$_REQUEST[usuario]',
                            fec_modifi = '$fec_actual'";
    }
    else
    {
            $query = "INSERT INTO ".BASE_DATOS.".tab_config_parame
                                  (cod_emptra,nom_razsoc,tel_ofipri,dir_ofupri,
                                   dir_emailx,nom_contac,val_multpc,ind_estcli,
                                   ind_estcon,ind_estveh,ind_estter,ind_califi,
                                   obs_planru,usr_creaci,fec_creaci,cod_aplica,
                                   ind_fincal,ind_remdes,ind_restra,ind_inidet,
                                   ind_feplle,cod_paisxx)
                           VALUES ('$_REQUEST[nitemp]','$_REQUEST[razsoc]','$_REQUEST[telpri]','$_REQUEST[dirpri]',
                                   '$_REQUEST[dirmai]','$_REQUEST[contac]','$_REQUEST[multa]','$_REQUEST[estper]',
                                   '$_REQUEST[estcon]','$_REQUEST[estveh]','$_REQUEST[estter]','$_REQUEST[califi]',
                                   '$_REQUEST[obs_planru]','$_REQUEST[usuario]','$fec_actual',1,'$_REQUEST[fincal]',
                                   '$_REQUEST[remdes]','$_REQUEST[restra]','$_REQUEST[inidet]','$_REQUEST[feplle]',
                                   '$_REQUEST[cod_paisxx]')
                     ";
    }

    $actualizar = new Consulta($query, $this -> conexion,"BR");

    if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Volver a Parametros</a></b>";

     $mensaje =  "Se Actualizar&oacute;n los Parametros del Sistema".$link_a;
     $mens = new mensajes();
     $mens -> correcto("PARAMETROS",$mensaje);
    }


 }//FIN FUNCION

}//FIN CLASE
     $proceso = new Ins_emptra_parame($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
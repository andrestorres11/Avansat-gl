<?php
class Ins_config_emptra
{
 var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
    //Metodos
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $datos_usuario = $this -> usuario -> retornar();
  $this -> principal();
 }
//********METODOS DE LA CLASE*************
 function principal()
 {
  if(!isset($GLOBALS[opcion]))
     $this -> Captura();
  else
     {
      switch($GLOBALS[opcion])
       {
        case "1":
         $this -> Poliza($GLOBALS[datos]);
        break;
        case "2":
         $this -> Insertar($GLOBALS[datos],$GLOBALS[datos2]);
        break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************
 function Captura()
 {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];
     $inicio[0][0]=0;
     $inicio[0][1]='-';
     //ciudades
     $query = "SELECT cod_ciudad,abr_ciudad
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE ind_estado = 1
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $ciudad = $consulta -> ret_matriz();
     $ciudad = array_merge($inicio,$ciudad);

     $query = "SELECT cod_terreg,nom_terreg
                 FROM ".BASE_DATOS.".tab_genera_terreg
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $regimen = $consulta -> ret_matriz();
     $regimen = array_merge($inicio,$regimen);

     //modalidades
     $query = "SELECT cod_modali,nom_modali
                 FROM ".BASE_DATOS.".tab_genera_modali
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $modalidad = $consulta -> ret_matriz();


     //formulario de insercion
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
     $formulario = new Formulario ("index.php","post","<b>Configuración Inicial</b>","form_transpor");
     $formulario -> linea("Datos Basicos Empresa Transportadora",1);
     $formulario -> nueva_tabla();
     echo "<td align=\"right\" class=\"etiqueta\">
            Nit:</td><td class=\"celda\">
           <input type=\"text\" name=\"tercer\" OnBlur.submit() \" size=\"9\" value=\"\" maxlength=\"9\">
           &nbsp;<b>-</b>&nbsp;<input type=\"text\" name=\"dijver\" OnBlur.submit() \" size=\"1\" value=\"\" maxlength=\"1\">
          </td>";
     $formulario -> texto ("Abreviatura:","text","abr",1,30,30,"","");
     $formulario -> texto ("Nombre o Razón Social:","text","nom",0,38,50,"","");
     $formulario -> lista("Ciudad", "ciures\" onBlur=\"copiar()", $ciudad, 1);
     $formulario -> texto ("Dirección:","text","direc\" onChange=\"copiar()",0,38,30,"","");
     $formulario -> texto ("Teléfono:","text","telef\" onChange=\"copiar()",1,20,30,"","");
     $formulario -> texto ("Representante Legal:","text","datos[1]",0,38,50,"","");
     $formulario -> lista("Regimen", "regimen", $regimen, 1);
     $formulario -> texto ("Actividad:","textarea","datos[2]",0,25,2,"","");

     $formulario -> nueva_tabla();
     $formulario -> linea ("Modalidad",1);
     for($i=0;$i<sizeof($modalidad);$i++)
     {
        $formulario -> caja ($modalidad[$i][1],"modali[$i]","".$modalidad[$i][0]."",0,0);

     }//fin for
     $formulario -> nueva_tabla();
     $formulario -> linea("Certificaciones y Habilitaciones Legales",1);
     $formulario -> nueva_tabla();
     $formulario -> caja ("Certificación ISO:","datos[3]","1",0,0);
     $formulario -> texto ("Vigencia(AAAA-MM-DD):","text","datos[4]",1,4,10,"","");
     $formulario -> caja ("Certificación BASC:","datos[5]","1",0,0);
     $formulario -> texto ("Vigencia(AAAA-MM-DD):","text","datos[6]",1,4,10,"","");
     $formulario -> texto ("Otras:","text","datos[7]",1,30,100,"","");
     $formulario -> nueva_tabla();
     $formulario -> linea("Datos Sede Principal",1);
     $formulario -> nueva_tabla();
     $formulario -> texto ("Nombre:","text","nomsed",1,50,60,"","");
     $formulario -> lista("Ciudad:", "ciused", $ciudad, 0);
     $formulario -> texto ("Dirección:","text","dirsed",1,25,30,"","");
     $formulario -> texto ("Contacto:","text","datos[11]",0,25,30,"","");
     $formulario -> texto ("Teléfono:","text","telsed",1,25,30,"","");
     $formulario -> texto ("Fax:","text","datos[13]",0,25,30,"","");
     $formulario -> texto ("E-Mail:","text","datos[14]",1,25,30,"","");
     $formulario -> oculto("maximo",sizeof($modalidad),0);
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",1,0);
     $formulario -> nueva_tabla();
     $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
     $formulario -> botoni("Siguiente","siguiente()",0);
     $formulario -> botoni("Borrar","form_transpor.reset()",1);
     $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA
// *****************************************************
 function Poliza($datos)
 {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];
     $inicio[0][0]=0;
     $inicio[0][1]='-';

     $query = "SELECT cod_diaxxx,nom_diaxxx
                 FROM ".BASE_DATOS.".tab_genera_diasxx
             ORDER BY 1";
     $consulta = new Consulta($query, $this -> conexion);
     $dias = $consulta -> ret_matriz();
     //actividades
     $query = "SELECT a.cod_tercer,a.abr_tercer
                 FROM ".BASE_DATOS.".tab_tercer_tercer a,
                      ".BASE_DATOS.".tab_tercer_activi b
                WHERE a.cod_tercer = b.cod_tercer AND
                      b.cod_activi = 5
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $asegra = $consulta -> ret_matriz();
     //objetos fechas
     $fecha1 = New Fecha();
     $fecha2 = New Fecha();
     $fecha3 = New Fecha();
     //reasigan los valores anteriores
     $datos1=$GLOBALS[datos1];
     $maximo=$GLOBALS[maximo];
     $modali=$GLOBALS[modali];
     //formulario de insercion
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
     $formulario = new Formulario ("index.php","post","<b>Datos Basicos de la Poliza</b>","form_poliza");
     $formulario -> linea("Datos Basicos de la Poliza",1);
     $formulario -> nueva_tabla();
     $formulario -> texto ("Nro Poliza:","text","poliza",0,20,30,"","");
     $formulario -> lista("Aseguradora", "asegra", $asegra, 1);
     $formulario -> nueva_tabla();
     $formulario -> linea("Vigencia Final",0);
     $fecha1 -> pedir_fecha("ano2","mes2","dia2");
     $formulario -> nueva_tabla();
     $formulario -> texto ("Valor Maximo por Despacho:","text","valmax",0,20,30,"","$GLOBALS[valmax]");
     $formulario -> texto ("Rango de Modelo de Vehículos:","text","modelo",1,10,10,"","$GLOBALS[modelo]");
     $formulario -> nueva_tabla();
     $formulario -> linea("Restricciones de Horario",0);
     $formulario -> nueva_tabla();
     $formulario -> linea("Día",0);
     $formulario -> linea("Hora Inicio",0);
     $formulario -> linea("",0);
     $formulario -> linea("",0);
     $formulario -> linea("",0);
     $formulario -> linea("Hora Final",0);
     $formulario -> linea("",0);
     $formulario -> linea("",0);
     $formulario -> linea("",1);
     for($i=0;$i<sizeof($dias);$i++)
     {
      echo "<td class=\"celda\"><b>".$dias[$i][1]."</b></td>";
      $fecha3 -> pedir_hora("hora[$i]","minuto[$i]");
      $fecha3 -> pedir_hora("hora2[$i]","minuto2[$i]");
      echo "</tr><tr>";
       $formulario -> oculto("dias[$i]",$dias[$i][0],0);
     }//fin for
     for($i=0;$i<25;$i++)
        $formulario -> oculto("datos[$i]","$datos[$i]",0);
      for($i=0;$i<$maximo;$i++){
      $formulario -> oculto("modali[$i]","".$modali[$i]."",0);}
     $formulario -> oculto("nom","$GLOBALS[nom]",0);
     $formulario -> oculto("abr","$GLOBALS[abr]",0);
     $formulario -> oculto("tercer","$GLOBALS[tercer]",0);
     $formulario -> oculto("dijver","$GLOBALS[dijver]",0);
     $formulario -> oculto("ciures","$GLOBALS[ciures]",0);
     $formulario -> oculto("direc","$GLOBALS[direc]",0);
     $formulario -> oculto("telef","$GLOBALS[telef]",0);
     $formulario -> oculto("nomsed","$GLOBALS[nomsed]",0);
     $formulario -> oculto("dirsed","$GLOBALS[dirsed]",0);
     $formulario -> oculto("telsed","$GLOBALS[telsed]",0);
     $formulario -> oculto("ciused","$GLOBALS[ciused]",0);
     $formulario -> oculto("regimen","$GLOBALS[regimen]",0);
     $formulario -> oculto("indcon","$GLOBALS[indcon]",0);

     $formulario -> oculto("pedido","$GLOBALS[pedido]",0);

     $formulario -> oculto("maximo","$maximo",0);
     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",$GLOBALS[opcion],0);
     $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
     $formulario -> botoni("Aceptar","aceptar()",0);
     $formulario -> botoni("Borrar","form_poliza.reset()",1);
     $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA


 function insertar($datos_ins,$datos2)
 {
  $fec_actual = date("Y-m-d H:i:s");

   //datos de la poliza
  $fec1 = mktime(12,0,0,"$GLOBALS[mes]","$GLOBALS[dia]","$GLOBALS[ano]");
  $fecha1= date ('Y-m-d H:i:s',$fec1);
  $fec2 = mktime(12,0,0,"$GLOBALS[mes2]","$GLOBALS[dia2]","$GLOBALS[ano2]");
  $fecha2= date ('Y-m-d H:i:s',$fec2);


  $query = "SELECT cod_paisxx,cod_depart
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = '$GLOBALS[ciures]'";
     $consulta = new Consulta($query, $this -> conexion);
     $ciudad = $consulta -> ret_matriz();

   //insercion del tercero
   $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer(cod_tercer,num_verifi,cod_tipdoc,cod_terreg,nom_tercer,
                                       abr_tercer,dir_domici,num_telef1,cod_paisxx,cod_depart,cod_ciudad,dir_emailx,
                                       num_faxxxx,
                                       obs_tercer,usr_creaci,fec_creaci)
            VALUES ('$GLOBALS[tercer]','$GLOBALS[dijver]','N','$GLOBALS[regimen]',
                    '$GLOBALS[nom]','$GLOBALS[abr]','$GLOBALS[direc]','$GLOBALS[telef]',
                    '".$ciudad[0][0]."','".$ciudad[0][1]."','$GLOBALS[ciures]','$datos_ins[14]',
                    '$datos_ins[13]',
                    '$datos_ins[2]','$GLOBALS[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "BR");
   //insercion de la actividad del tercero
   $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
            VALUES ('$GLOBALS[tercer]','9')";
  $insercion = new Consulta($query, $this -> conexion, "R");


    //iso
   if($datos_ins[3] == 1)
        $iso='S';
     else
        $iso='N';
  //basc
   if($datos_ins[5] == 1)
        $basc='S';
     else
        $basc='N';

   //cobertura nacional
   if($datos_ins[8] == 1)
        $cobnal='S';
     else
        $cobnal='N';

   //cobertura internacional
   if($datos_ins[22] == 1)
        $cobint='S';
     else
        $cobint='N';


  $query = "SELECT cod_paisxx,cod_depart
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = '$GLOBALS[ciused]'";
     $consulta = new Consulta($query, $this -> conexion);
     $ciused = $consulta -> ret_matriz();

        //insercion del tercero
           $query = "UPDATE ".C_CONSULTOR.".tab_genera_tercer
                        SET dig_codigo = '$GLOBALS[dijver]',
                            cod_tipdoc = 'N',
                            nom_tercer = '$GLOBALS[nom]',
                            abr_tercer = '$GLOBALS[abr]',
                            dir_tercer = '$GLOBALS[direc]',
                            tel_terce1 = '$GLOBALS[telef]',
                            cod_paisxx = '".$ciudad[0][0]."',
                            cod_depart = '".$ciudad[0][1]."',
                            cod_ciudad = '$GLOBALS[ciures]',
                            dir_correo = '$datos_ins[14]',
                            fax_tercer = '$datos_ins[13]',
                            usr_modifi = '$GLOBALS[usuario]',
                            fec_modifi = '$fec_actual'
                    WHERE cod_tercer = '$GLOBALS[tercer]'";
          $insercion = new Consulta($query, $this -> conexion, "R");
           //insercion de la actividad del tercero
           $query = "UPDATE ".C_CONSULTOR.".tab_genera_teract
                        SET cod_activi = '9'
                      WHERE cod_tercer = '$GLOBALS[tercer]'
                        AND cod_activi = '1'";
          $insercion = new Consulta($query, $this -> conexion, "R");

          $query = "UPDATE ".C_CONSULTOR.".tab_genera_oficin
                        SET nom_oficin = '$GLOBALS[nomsed]',
                            abr_oficin = '$GLOBALS[nomsed]',
                            cod_paisxx = '".$ciused[0][0]."',
                            cod_depart = '".$ciused[0][1]."',
                            cod_ciudad = '$GLOBALS[ciused]',
                            dir_oficin = '$GLOBALS[dirsed]',
                            tel_oficin = '$GLOBALS[telsed]',
                            con_oficin = '$datos_ins[11]',
                            usr_modifi = '$GLOBALS[usuario]',
                            fec_modifi = '$fec_actual'
                    WHERE   cod_oficin = '1'";
        $insercion = new Consulta($query, $this -> conexion, "R");


    $query = "INSERT INTO ".BASE_DATOS.".tab_emptra_config
                                    (cod_emptra,nom_empres,
                                     cod_asegra,num_poliza,fec_vigfin,
                                     ind_ceriso,fec_ceriso,ind_cerbas,fec_cerbas,
                                     otr_certif,
                                     nom_repleg,usr_creaci,fec_creaci)
            VALUES ('$GLOBALS[tercer]','$GLOBALS[nom]','$GLOBALS[asegra]',
                    '$GLOBALS[poliza]','$fecha2',
                    '$iso','$datos_ins[4]','$basc','$datos_ins[6]','$datos_ins[7]',
                    '$datos_ins[1]','$GLOBALS[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "R");


  //consecutivo de la sede
  $query = "SELECT Max(cod_agenci) AS maximo
              FROM ".BASE_DATOS.".tab_genera_agenci ";
  $consec = new Consulta($query, $this -> conexion);
  $ultimo = $consec -> ret_matriz();
  $ultimo_consec = $ultimo[0][0];
  $nuevo_consec = $ultimo_consec+1;
  //INSERTA LA SEDE
  $query = "INSERT INTO ".BASE_DATOS.".tab_genera_agenci(cod_agenci,nom_agenci,cod_ciudad,
                                     dir_agenci,tel_agenci,con_agenci,dir_emailx,num_faxxxx,
                                     usr_creaci,fec_creaci)
            VALUES ('$nuevo_consec','$GLOBALS[nomsed]','$GLOBALS[ciused]','$GLOBALS[dirsed]',
                    '$GLOBALS[telsed]','$datos_ins[11]','$datos_ins[14]',
                    '$datos_ins[13]','$GLOBALS[usuario]','$fec_actual')";
 $insercion = new Consulta($query, $this -> conexion, "R");

  $query = "INSERT INTO ".BASE_DATOS.".tab_poliza_tercer
            VALUES ('$GLOBALS[tercer]','$GLOBALS[poliza]','$GLOBALS[asegra]',
            '$fecha1','$fecha2','$GLOBALS[valmax]','$GLOBALS[modelo]',
            '$GLOBALS[usuario]','$fec_actual','$GLOBALS[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "R");

  //reasignacion de variables para las horarios
  $dias=$GLOBALS[dias];
  $hora =$GLOBALS[hora];
  $minuto =$GLOBALS[minuto];
  $hora2 =$GLOBALS[hora2];
  $minuto2 =$GLOBALS[minuto2];
  for($i=0;$i<sizeof($dias);$i++)
  {
   if($dias[$i] != Null or $dias[$i] != 0)
   {
     $fec = mktime("$hora[$i]","$minuto[$i]",0,3,14,2003);
     $fecha= date ('H:i:s',$fec);
     $fec2 = mktime("$hora2[$i]","$minuto2[$i]",0,3,14,2003);
     $fecha2= date ('H:i:s',$fec2);

     $query = "INSERT INTO ".BASE_DATOS.".tab_poliza_restri
               VALUES ('$GLOBALS[tercer]','$GLOBALS[poliza]','$dias[$i]','$fecha','$fecha2',
               '$GLOBALS[usuario]','$fec_actual','$GLOBALS[usuario]','$fec_actual')";
    $insercion = new Consulta($query, $this -> conexion, "R");
   }//fin if
  }//fin for

  $modali=$GLOBALS[modali];
  for($i=0;$i<$GLOBALS[maximo];$i++)
  {
   if($modali[$i] != Null AND $modali[$i] != 0)
   {
     $query = "INSERT INTO ".BASE_DATOS.".tab_emptra_modali
               VALUES ('$GLOBALS[tercer]','$modali[$i]')";
     $insercion = new Consulta($query, $this -> conexion, "R");
   }//fin if
  }//fin for

     if(!mysql_errno())
     {
         $consulta = new Consulta ("COMMIT", $this -> conexion);
         $mensaje = "<b>Los Datos han sido Ingresados con Exito</b><br>";
         $mens = new mensajes();
         $mens -> correcto("Transacción Exitosa ", $mensaje);
     }
     else
         $consulta = new Consulta ("ROLLBACK", $this -> conexion);
 }//FIN FUNCION INSERT_SEDE

}//FIN CLASE
     $proceso = new Ins_config_emptra($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
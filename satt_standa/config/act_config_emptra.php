<?php
class Act_config_emptra
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
  if(!isset($_REQUEST[opcion]))
     $this -> Captura();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "1":
         $this -> Poliza($_REQUEST[datos]);
        break;
        case "2":
         $this -> Insertar($_REQUEST[datos],$_REQUEST[datos2]);
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


     $query = "SELECT cod_terreg,nom_terreg
                 FROM ".BASE_DATOS.".tab_genera_terreg
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $regimen = $consulta -> ret_matriz();


     //modalidades
     $query = "SELECT cod_modali,nom_modali
                 FROM ".BASE_DATOS.".tab_genera_modali
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $modalidad = $consulta -> ret_matriz();


      $query = "SELECT  a.cod_emptra,b.num_verifi,a.nom_empres,b.abr_tercer,b.cod_ciudad,b.dir_domici,b.num_telef1,
                        a.nom_repleg,b.cod_terreg,b.obs_tercer,a.ind_consul,a.ind_ceriso,a.fec_ceriso,
                        a.ind_cerbas,a.fec_cerbas,a.otr_certif,a.ind_cobnal,a.ind_cobint,a.nro_habnal,
                        a.fec_resnal,d.nom_agenci,d.cod_ciudad,d.dir_agenci,d.con_agenci,d.tel_agenci,
                        d.num_faxxxx,d.dir_emailx,a.num_region,a.cod_minins,a.num_resolu,a.fec_resolu,
                        a.ran_iniman,a.ran_finman,a.ind_pedido,a.ind_fecarg

                 FROM ".BASE_DATOS.".tab_emptra_config a,".BASE_DATOS.".tab_tercer_tercer b,
                      ".BASE_DATOS.".tab_genera_agenci d
             WHERE a.cod_emptra = b.cod_tercer AND
                   d.cod_agenci = '1'";
     $consulta = new Consulta($query, $this -> conexion);
     $matriz = $consulta -> ret_matriz();

     //ciudades anteriores
     $query = "SELECT cod_ciudad,abr_ciudad
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = '".$matriz[0][4]."'
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $ciudad_a = $consulta -> ret_matriz();
     $ciudad = array_merge($ciudad_a,$inicio,$ciudad);

     //regimen anterior
      $query = "SELECT cod_terreg,nom_terreg
                 FROM ".BASE_DATOS.".tab_genera_terreg
             WHERE cod_terreg = '".$matriz[0][8]."'";
     $consulta = new Consulta($query, $this -> conexion);
     $regimen_a = $consulta -> ret_matriz();
     $regimen = array_merge($regimen_a,$inicio,$regimen);

     //ciudades anteriores de sedes
     $query = "SELECT cod_ciudad,abr_ciudad
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = '".$matriz[0][21]."'
             ORDER BY 2";
     $consulta = new Consulta($query, $this -> conexion);
     $ciudads_a = $consulta -> ret_matriz();
     $ciudads = array_merge($ciudads_a,$inicio,$ciudad);



     //formulario de insercion
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
     $formulario = new Formulario ("index.php","post","<b>Configuración Inicial</b>","form_transpor");
     $formulario -> linea("Datos Basicos Empresa Transportadora",1);
     $formulario -> nueva_tabla();
     echo "<td align=\"right\" class=\"etiqueta\">
            Nit:</td><td class=\"celda\">
           <input type=\"text\" name=\"tercer\" OnBlur.submit() \" size=\"9\" value=\"".$matriz[0][0]."\" maxlength=\"9\">
           &nbsp;<b>-</b>&nbsp;<input type=\"text\" name=\"dijver\" OnBlur.submit() \" size=\"1\" value=\"".$matriz[0][1]."\" maxlength=\"1\">
          </td>";
     $formulario -> texto ("Abreviatura:","text","abr",1,30,30,"",$matriz[0][2]);
     $formulario -> texto ("Nombre o Razón Social:","text","nom",0,38,50,"",$matriz[0][3]);
     $formulario -> lista("Ciudad", "ciures\" onBlur=\"copiar()", $ciudad, 1);
     $formulario -> texto ("Dirección:","text","direc\" onChange=\"copiar()",0,38,30,"",$matriz[0][5]);
     $formulario -> texto ("Teléfono:","text","telef\" onChange=\"copiar()",1,20,30,"",$matriz[0][6]);
     $formulario -> texto ("Representante Legal:","text","datos[1]",0,38,50,"",$matriz[0][7]);
     $formulario -> lista("Regimen", "regimen", $regimen, 1);
     $formulario -> texto ("Actividad:","textarea","datos[2]",0,25,2,"",$matriz[0][9]);
     $formulario -> nueva_tabla();
     $formulario -> linea ("Modalidad",1);
     for($i=0;$i<sizeof($modalidad);$i++)
     {
        //modalidades
        $query = "SELECT cod_modali
                 FROM ".BASE_DATOS.".tab_emptra_modali
             WHERE cod_modali = '".$modalidad[$i][0]."'";
        $consulta = new Consulta($query, $this -> conexion);
        $modalid = $consulta -> ret_matriz();

        if($modalid)
         $val=1;
        else
         $val=0;

        $formulario -> caja ($modalidad[$i][1],"modali[$i]","".$modalidad[$i][0]."",$val,0);

     }//fin for
     $formulario -> nueva_tabla();
     $formulario -> linea("Certificaciones y Habilitaciones Legales",1);
     $formulario -> nueva_tabla();
     $formulario -> caja ("Certificación ISO:","datos[3]","1",$matriz[0][11],0);
     $formulario -> texto ("Vigencia(AAAA-MM-DD):","text","datos[4]",1,10,10,"",$matriz[0][12]);
     $formulario -> caja ("Certificación BASC:","datos[5]","1",$matriz[0][13],0);
     $formulario -> texto ("Vigencia(AAAA-MM-DD):","text","datos[6]",1,10,10,"",$matriz[0][14]);
     $formulario -> texto ("Otras:","text","datos[7]",1,30,100,"",$matriz[0][15]);
     $formulario -> nueva_tabla();
     $formulario -> linea("Datos Sede Principal",1);
     $formulario -> nueva_tabla();
     $formulario -> texto ("Nombre:","text","nomsed",1,50,60,"",$matriz[0][20]);
     $formulario -> lista("Ciudad:", "ciused", $ciudads, 0);
     $formulario -> texto ("Dirección:","text","dirsed",1,25,30,"",$matriz[0][22]);
     $formulario -> texto ("Contacto:","text","datos[11]",0,25,30,"",$matriz[0][23]);
     $formulario -> texto ("Teléfono:","text","telsed",1,25,30,"",$matriz[0][24]);
     $formulario -> texto ("Fax:","text","datos[13]",0,25,30,"",$matriz[0][25]);
     $formulario -> texto ("E-Mail:","text","datos[14]",1,25,30,"",$matriz[0][26]);
     $formulario -> nueva_tabla();
     $formulario -> oculto("maximo",sizeof($modalidad),0);
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",1,0);
     $formulario -> nueva_tabla();
     $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
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
     $datos1=$_REQUEST[datos1];
     $maximo=$_REQUEST[maximo];
     $modali=$_REQUEST[modali];


     $query = "SELECT a.num_poliza,a.cod_asegra,a.fec_vigfin,a.val_maxdes,a.ano_modelo,
                      a.fec_vigini
                 FROM ".BASE_DATOS.".tab_poliza_tercer a
                WHERE a.cod_tercer = '$_REQUEST[tercer]' ";
     $consulta = new Consulta($query, $this -> conexion);
     $matriz = $consulta -> ret_matriz();

     $query = "SELECT a.cod_tercer,a.abr_tercer
                 FROM ".BASE_DATOS.".tab_tercer_tercer a
                WHERE a.cod_tercer = '".$matriz[0][1]."'";
     $consulta = new Consulta($query, $this -> conexion);
     $asegra_a = $consulta -> ret_matriz();

     $asegra = array_merge($asegra_a,$inicio,$asegra);

     $query = "SELECT a.hor_inicio,a.hor_finalx,b.nom_diaxxx
                 FROM ".BASE_DATOS.".tab_genera_diasxx b
                      LEFT JOIN ".BASE_DATOS.".tab_poliza_restri a ON
                      a.cod_tercer = '$_REQUEST[tercer]' AND
                      a.cod_diaxxx = b.cod_diaxxx";
     $consulta = new Consulta($query, $this -> conexion);
     $polrest = $consulta -> ret_matriz();



     //formulario de insercion
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
     $formulario = new Formulario ("index.php","post","<b>Datos Basicos de la Poliza</b>","form_poliza");
     $formulario -> linea("Datos Basicos de la Poliza",1);
     $formulario -> nueva_tabla();
     $formulario -> texto ("Nro Poliza:","text","poliza",0,20,30,"",$matriz[0][0]);
     $formulario -> lista("Aseguradora", "asegra", $asegra, 1);
     $formulario -> texto ("Vigencia Inicial AAAA-MM-DD:","text","vigini",0,10,10,"",$matriz[0][5]);
     $formulario -> texto ("Vigencia Final AAAA-MM-DD:","text","vig_poliza",1,10,10,"",$matriz[0][2]);
     $formulario -> texto ("Valor Maximo por Despacho:","text","valmax",0,15,30,"",$matriz[0][3]);
     $formulario -> texto ("Rango de Modelo de Vehículos:","text","modelo",1,5,5,"",$matriz[0][4]);
     $formulario -> nueva_tabla();
     $formulario -> linea("Restricciones de Horario",0);
     $formulario -> nueva_tabla();
     $formulario -> linea("Día",0);
     $formulario -> linea("",0);
     $formulario -> linea("Hora Inicio",0);
     $formulario -> linea("",0);
     $formulario -> linea("Hora Final",1);


     for($i=0;$i<sizeof($polrest);$i++)
     {
      echo "<td class=\"celda\"><b>".$polrest[$i][2]."</b></td>";
      $formulario -> texto ("","text","hora1[$i]",0,10,10,"",$polrest[$i][0]);
      $formulario -> texto ("","text","hora2[$i]",1,10,10,"",$polrest[$i][1]);
     }//fin for




     for($i=0;$i<25;$i++)
        $formulario -> oculto("datos[$i]","$datos[$i]",0);


     for($i=0;$i<$maximo;$i++){
     $formulario -> oculto("modali[$i]","".$modali[$i]."",0);}
     $formulario -> oculto("nom","$_REQUEST[nom]",0);
     $formulario -> oculto("abr","$_REQUEST[abr]",0);
     $formulario -> oculto("tercer","$_REQUEST[tercer]",0);
     $formulario -> oculto("dijver","$_REQUEST[dijver]",0);
     $formulario -> oculto("ciures","$_REQUEST[ciures]",0);
     $formulario -> oculto("direc","$_REQUEST[direc]",0);
     $formulario -> oculto("telef","$_REQUEST[telef]",0);
     $formulario -> oculto("nomsed","$_REQUEST[nomsed]",0);
     $formulario -> oculto("dirsed","$_REQUEST[dirsed]",0);
     $formulario -> oculto("telsed","$_REQUEST[telsed]",0);
     $formulario -> oculto("ciused","$_REQUEST[ciused]",0);
     $formulario -> oculto("regimen","$_REQUEST[regimen]",0);
     $formulario -> oculto("indcon","$_REQUEST[indcon]",0);

     $formulario -> oculto("pedido","$_REQUEST[pedido]",0);

     $formulario -> oculto("maximo","$maximo",0);
     $formulario -> nueva_tabla();
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",$_REQUEST[opcion],0);
     $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
     $formulario -> botoni("Aceptar","actulizar()",0);
     $formulario -> botoni("Borrar","form_poliza.reset()",1);
     $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA


 function insertar($datos_ins,$datos2)
 {
  $fec_actual = date("Y-m-d H:i:s");

   //datos de la poliza
  $fec1 = mktime(12,0,0,"$_REQUEST[mes]","$_REQUEST[dia]","$_REQUEST[ano]");
  $fecha1= date ('Y-m-d H:i:s',$fec1);
  $fec2 = mktime(12,0,0,"$_REQUEST[mes2]","$_REQUEST[dia2]","$_REQUEST[ano2]");
  $fecha2= date ('Y-m-d H:i:s',$fec2);


  $query = "SELECT cod_paisxx,cod_depart
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = '$_REQUEST[ciures]'";
     $consulta = new Consulta($query, $this -> conexion);
     $ciudad = $consulta -> ret_matriz();

   //insercion del tercero
       $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
                        SET num_verifi = '$_REQUEST[dijver]',
                            cod_terreg = '$_REQUEST[regimen]',
                            nom_tercer = '$_REQUEST[nom]',
                            abr_tercer = '$_REQUEST[abr]',
                            dir_domici = '$_REQUEST[direc]',
                            num_telef1 = '$_REQUEST[telef]',
                            cod_paisxx = '".$ciudad[0][0]."',
                            cod_depart = '".$ciudad[0][1]."',
                            cod_ciudad = '$_REQUEST[ciures]',
                            dir_emailx = '$datos_ins[14]',
                            num_faxxxx = '$datos_ins[13]',
                            obs_tercer = '$datos_ins[2]',
                            usr_modifi = '$_REQUEST[usuario]',
                            fec_modifi = '$fec_actual'
                    WHERE cod_tercer = '$_REQUEST[tercer]'";
          $insercion = new Consulta($query, $this -> conexion, "BR");



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

     if($_REQUEST[indcon])
     {
     $query = "SELECT cod_paisxx,cod_depart
                 FROM ".BASE_DATOS.".tab_genera_ciudad
                WHERE cod_ciudad = '$_REQUEST[ciused]'";
     $consulta = new Consulta($query, $this -> conexion);
     $ciused = $consulta -> ret_matriz();

        //insercion del tercero
         $query = "UPDATE ".C_CONSULTOR.".tab_genera_tercer
                        SET dig_codigo = '$_REQUEST[dijver]',
                            cod_tipdoc = 'N',
                            nom_tercer = '$_REQUEST[nom]',
                            abr_tercer = '$_REQUEST[abr]',
                            dir_tercer = '$_REQUEST[direc]',
                            tel_terce1 = '$_REQUEST[telef]',
                            cod_paisxx = '".$ciudad[0][0]."',
                            cod_depart = '".$ciudad[0][1]."',
                            cod_ciudad = '$_REQUEST[ciures]',
                            dir_correo = '$datos_ins[14]',
                            fax_tercer = '$datos_ins[13]',
                            usr_modifi = '$_REQUEST[usuario]',
                            fec_modifi = '$fec_actual'
                      WHERE cod_tercer = '$_REQUEST[tercer]'";
          $insercion = new Consulta($query, $this -> conexion, "R");
           //insercion de la actividad del tercero
           $query = "UPDATE ".C_CONSULTOR.".tab_genera_teract
                        SET cod_activi = '9'
                      WHERE cod_tercer = '$_REQUEST[tercer]'
                        AND cod_activi = '1'";
          $insercion = new Consulta($query, $this -> conexion, "R");

          $query = "UPDATE ".C_CONSULTOR.".tab_genera_oficin
                        SET nom_oficin = '$_REQUEST[nomsed]',
                            abr_oficin = '$_REQUEST[nomsed]',
                            cod_paisxx = '".$ciused[0][0]."',
                            cod_depart = '".$ciused[0][1]."',
                            cod_ciudad = '$_REQUEST[ciused]',
                            dir_oficin = '$_REQUEST[dirsed]',
                            tel_oficin = '$_REQUEST[telsed]',
                            con_oficin = '$datos_ins[11]',
                            usr_modifi = '$_REQUEST[usuario]',
                            fec_modifi = '$fec_actual'
                    WHERE   cod_oficin = '1'";
          $insercion = new Consulta($query, $this -> conexion, "R");
     }

   $query = "UPDATE ".BASE_DATOS.".tab_emptra_config
                   SET cod_emptra = '$_REQUEST[tercer]',
                       nom_empres = '$_REQUEST[nom]',
                       cod_asegra = '$_REQUEST[asegra]',
                       num_poliza = '$_REQUEST[poliza]',
                       fec_vigfin = '$_REQUEST[vig_poliza]',
                       ind_ceriso = '$iso',
                       fec_ceriso = '$datos_ins[4]',
                       ind_cerbas = '$basc',
                       fec_cerbas = '$datos_ins[6]',
                       otr_certif = '$datos_ins[7]',
                       nom_repleg = '$datos_ins[1]',
                       usr_modifi = '$_REQUEST[usuario]',
                       fec_modifi = '$fec_actual'
               WHERE   cod_emptra = '$_REQUEST[tercer]'";
  $insercion = new Consulta($query, $this -> conexion, "R");



      $query = "UPDATE ".BASE_DATOS.".tab_genera_agenci
                   SET  nom_agenci = '$_REQUEST[nomsed]',
                        cod_ciudad = '$_REQUEST[ciused]',
                        dir_agenci = '$_REQUEST[dirsed]',
                        tel_agenci = '$_REQUEST[telsed]',
                        con_agenci = '$datos_ins[11]',
                        dir_emailx = '$datos_ins[14]',
                        num_faxxxx = '$datos_ins[13]',
                        usr_modifi = '$_REQUEST[usuario]',
                        fec_modifi = '$fec_actual'
                WHERE   cod_agenci = '1'";
  $insercion = new Consulta($query, $this -> conexion, "R");

     $query = "SELECT cod_tercer
                 FROM ".BASE_DATOS.".tab_poliza_tercer
                WHERE cod_tercer = '$_REQUEST[tercer]' AND
                      num_poliza = '$_REQUEST[poliza]'";
     $consulta = new Consulta($query, $this -> conexion);
     $polter = $consulta -> ret_matriz();

  if($polter){
  $query = "UPDATE ".BASE_DATOS.".tab_poliza_tercer
                  SET num_poliza = '$_REQUEST[poliza]',
                      cod_asegra = '$_REQUEST[asegra]',
                      fec_vigini = '$_REQUEST[vigini]',
                      fec_vigfin = '$_REQUEST[vig_poliza]',
                      val_maxdes = '$_REQUEST[valmax]',
                      ano_modelo = '$_REQUEST[modelo]',
                      usr_modifi = '$_REQUEST[usuario]',
                      fec_modifi = '$fec_actual'
                WHERE cod_tercer = '$_REQUEST[tercer]'";
  $actualizacion = new Consulta($query, $this -> conexion, "R");}
  else{
  $query = "INSERT INTO ".BASE_DATOS.".tab_poliza_tercer
            VALUES ('$_REQUEST[tercer]','$_REQUEST[poliza]','$_REQUEST[asegra]',
            '$_REQUEST[vigini]','$_REQUEST[vig_poliza]','$_REQUEST[valmax]','$_REQUEST[modelo]',
            '$_REQUEST[usuario]','$fec_actual','$_REQUEST[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "R");}


 $query = "DELETE FROM ".BASE_DATOS.".tab_poliza_restri
                   WHERE cod_tercer = '$_REQUEST[tercer]' AND
                         num_poliza = '$_REQUEST[poliza]' ";
  $actualizacion = new Consulta($query, $this -> conexion, "R");


 $query = "DELETE FROM ".BASE_DATOS.".tab_emptra_modali
                   WHERE  cod_emptra  = '$_REQUEST[tercer]'";
  $actualizacion = new Consulta($query, $this -> conexion, "R");



    $query = "SELECT cod_diaxxx,nom_diaxxx
                 FROM ".BASE_DATOS.".tab_genera_diasxx
             ORDER BY 1";
     $consulta = new Consulta($query, $this -> conexion);
     $dias = $consulta -> ret_matriz();


  //reasignacion de variables para las horarios
  $hora1 =$_REQUEST[hora1];
  $hora2 =$_REQUEST[hora2];
  for($i=0;$i<sizeof($dias);$i++)
  {

     $query = "INSERT INTO ".BASE_DATOS.".tab_poliza_restri
               VALUES ('$_REQUEST[tercer]','$_REQUEST[poliza]','".$dias[$i][0]."','$hora1[$i]','$hora2[$i]',
               '$_REQUEST[usuario]','$fec_actual','$_REQUEST[usuario]','$fec_actual')";
    $insercion = new Consulta($query, $this -> conexion, "R");

  }//fin for

  $modali=$_REQUEST[modali];
  for($i=0;$i<$_REQUEST[maximo];$i++)
  {
   if($modali[$i] != Null AND $modali[$i] != 0)
   {
     $query = "INSERT INTO ".BASE_DATOS.".tab_emptra_modali
               VALUES ('$_REQUEST[tercer]','$modali[$i]')";
     $insercion = new Consulta($query, $this -> conexion, "R");
   }//fin if
  }//fin for

     if(!mysql_errno())
     {
        $consulta = new Consulta ("COMMIT", $this -> conexion);
        echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Los Datos de la empresa $_REQUEST[nom] han sido Actualizados con Exito</b>";
     }
     else
         $consulta = new Consulta ("ROLLBACK", $this -> conexion);
 }//FIN FUNCION INSERT_SEDE

}//FIN CLASE
     $proceso = new Act_config_emptra($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
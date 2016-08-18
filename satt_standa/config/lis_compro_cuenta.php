<?php
class Lis_compro_cuenta
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
         $this -> Captura();
        break;
        case "2":
         $this -> Insertar();
         $this -> Captura();
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
     //anticipos
     $query = "SELECT a.cod_antici,a.nom_antici
                 FROM ".BASE_DATOS.".tab_genera_antici a,
                      ".BASE_DATOS.".tab_compro_cuenta b
                 WHERE a.cod_antici= b.cod_tiptra";
     if(isset($GLOBALS[tiptra]) AND $GLOBALS[tiptra]!= 0)
            $query = $query." AND a.cod_antici = '$GLOBALS[tiptra]' ";

            $query = $query." GROUP BY 1 ORDER BY 2 ";
     $consulta = new Consulta($query, $this -> conexion);
     $antici = $consulta -> ret_matriz();
     if(isset($GLOBALS[tiptra]) AND $GLOBALS[tiptra]!= 0)
      $antici = array_merge($antici,$inicio);
     else
      $antici = array_merge($inicio,$antici);

     $query = "SELECT a.cod_bancox,a.abr_bancox
                 FROM ".CONSULTOR.".tab_genera_bancos a,
                      ".BASE_DATOS.".tab_compro_cuenta b
                 WHERE a.cod_bancox = b.cod_bancox
                   AND b.cod_tiptra = '$GLOBALS[tiptra]' ";
         if(isset($GLOBALS[banco]) AND $GLOBALS[banco]!= 0)
            $query = $query." AND a.cod_bancox = '$GLOBALS[banco]' ";

            $query = $query." GROUP BY 1 ORDER BY 2 ";

     $consulta = new Consulta($query, $this -> conexion);
     $bancos = $consulta -> ret_matriz();
     if(isset($GLOBALS[banco]) AND $GLOBALS[banco]!= 0)
      $bancos = array_merge($bancos,$inicio);
     else
      $bancos = array_merge($inicio,$bancos);


     $query = "SELECT a.cod_tipcom,a.nom_tipcom
                 FROM ".C_CONSULTOR.".tab_genera_tipcom a,
                      ".BASE_DATOS.".tab_compro_cuenta b
                 WHERE a.cod_tipcom = b.cod_tipcom AND
                       b.cod_tiptra = '$GLOBALS[tiptra]' AND
                       b.cod_bancox = '$GLOBALS[banco]' ";
      if(isset($GLOBALS[tipcom]) AND $GLOBALS[tipcom]!= 0)
            $query = $query." AND a.cod_tipcom = '$GLOBALS[tipcom]' ";

            $query = $query." GROUP BY 1 ORDER BY 2 ";

     $consulta = new Consulta($query, $this -> conexion);
     $tipcom = $consulta -> ret_matriz();
     if(isset($GLOBALS[tipcom]) AND $GLOBALS[tipcom]!= 0)
      $tipcom = array_merge($tipcom,$inicio);
     else
      $tipcom = array_merge($inicio,$tipcom);

     //agencias o sedes de la transportadora
     $query = "SELECT a.cod_agenci,a.nom_agenci
                 FROM ".BASE_DATOS.".tab_genera_agenci a,
                      ".BASE_DATOS.".tab_compro_cuenta b
                 WHERE a.cod_agenci = b.cod_agenci
                   AND b.cod_tiptra = '$GLOBALS[tiptra]'
                   AND b.cod_bancox = '$GLOBALS[banco]'
                   AND b.cod_tipcom = '$GLOBALS[tipcom]'";
        if(isset($GLOBALS[agencia]) AND $GLOBALS[agencia]!= 0)
            $query = $query." AND a.cod_agenci = '$GLOBALS[agencia]' ";

            $query = $query." GROUP BY 1 ORDER BY 2 ";

     $consulta = new Consulta($query, $this -> conexion);
     $agencias = $consulta -> ret_matriz();
     if(isset($GLOBALS[agencia]) AND $GLOBALS[agencia]!= 0)
       $agencias = array_merge($agencias,$inicio);
     else
       $agencias = array_merge($inicio,$agencias);


     //formulario de insercion
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/config.js\"></script>\n";
     $formulario = new Formulario ("index.php","post","<b>Contable</b>","form_compro");
     $formulario -> lista("Transacción", "tiptra\" onChange=\"form_compro.banco.value=''; form_compro.tipcom.value=''; form_compro.agencia.value=''; form_compro.submit()", $antici, 0);
     if(($GLOBALS[tiptra] != 10)&&($GLOBALS[tiptra] != 3)&&($GLOBALS[tiptra] != 21)&&($GLOBALS[tiptra] != 30)&&($GLOBALS[tiptra] != 50))
     {
             $formulario -> lista("Bancos", "banco\" onChange=\"form_compro.tipcom.value=''; form_compro.agencia.value=''; form_compro.submit()", $bancos, 0);
             $formulario -> lista("Tipo Comprobante", "tipcom\" onChange=\"form_compro.agencia.value=''; form_compro.submit()",$tipcom, 1);
     }
     else
     {
         $formulario -> oculto("banco",0,0);
         $formulario -> lista("Tipo Comprobante", "tipcom\" onChange=\"form_compro.agencia.value=''; form_compro.submit()", $tipcom, 0);
     }
     $formulario -> lista("Agencia", "agencia\" onChange=\"form_compro.submit()",$agencias, 1);
     $formulario -> nueva_tabla();
     $query = "SELECT b.cod_clasec,b.cod_grupoc,b.cod_cuenta,b.cod_subcue,b.cod_auxili,
                      b.cod_retefu,b.cod_coniva,b.cod_retica,
                      CONCAT(b.cod_clasec,b.cod_grupoc,b.cod_cuenta,b.cod_subcue,b.cod_auxili),
                      a.nom_cuenta
                 FROM ".C_CONSULTOR.".tab_genera_plancu a,
                      ".BASE_DATOS.".tab_compro_cuenta b
                 WHERE a.cod_clasec = b.cod_clasec AND
                       a.cod_grupoc = b.cod_grupoc AND
                       a.cod_cuenta = b.cod_cuenta AND
                       a.cod_subcue = b.cod_subcue AND
                       a.cod_auxili = b.cod_auxili AND
                       b.cod_agenci = '$GLOBALS[agencia]'
                   AND b.cod_tiptra = '$GLOBALS[tiptra]'
                   AND b.cod_bancox = '$GLOBALS[banco]'
                   AND b.cod_tipcom = '$GLOBALS[tipcom]'
                   AND b.ind_nattra = '0'";

     $consulta = new Consulta($query, $this -> conexion);
     $cuedeb = $consulta -> ret_matriz();

     $query = "SELECT b.cod_clasec,b.cod_grupoc,b.cod_cuenta,b.cod_subcue,b.cod_auxili,
                      b.cod_retefu,b.cod_coniva,b.cod_retica,
                      CONCAT(b.cod_clasec,b.cod_grupoc,b.cod_cuenta,b.cod_subcue,b.cod_auxili),
                      a.nom_cuenta
                 FROM ".C_CONSULTOR.".tab_genera_plancu a,
                      ".BASE_DATOS.".tab_compro_cuenta b
                 WHERE a.cod_clasec = b.cod_clasec AND
                       a.cod_grupoc = b.cod_grupoc AND
                       a.cod_cuenta = b.cod_cuenta AND
                       a.cod_subcue = b.cod_subcue AND
                       a.cod_auxili = b.cod_auxili AND
                       b.cod_agenci = '$GLOBALS[agencia]'
                   AND b.cod_tiptra = '$GLOBALS[tiptra]'
                   AND b.cod_bancox = '$GLOBALS[banco]'
                   AND b.cod_tipcom = '$GLOBALS[tipcom]'
                   AND b.ind_nattra = '1'";

     $consulta = new Consulta($query, $this -> conexion);
     $cuecre = $consulta -> ret_matriz();

     if((($GLOBALS[tiptra] < 10)||($GLOBALS[tiptra] >= 20))&&(($GLOBALS[tiptra] < 30)||($GLOBALS[tiptra] >= 40)))//no es liquidacion ni facturacion
     {

     $formulario -> linea("Cuenta Debito",0);
     $formulario -> linea($cuedeb[0][8],0);
     $formulario -> linea("Nombre:",0);
     $formulario -> linea($cuedeb[0][9],1);
     if($GLOBALS[tiptra]>= 40 && $GLOBALS[tiptra]< 50)//recaudo
     {
       $ano_actual = date("Y");

       $query = "SELECT abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                AND cod_retefu = '".$cuedeb[3][5]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retefu = $consulta -> ret_matriz();

      $query = "SELECT abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                AND cod_retica = '".$cuedeb[2][7]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retica = $consulta -> ret_matriz();

      $formulario -> linea("Gastos Varios", 0);
      $formulario -> linea($cuedeb[1][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[1][9],1);
      $formulario -> linea("Cuenta ICA",0);
      $formulario -> linea($cuedeb[2][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[2][9],0);
      $formulario -> linea("ICA",0);
      $formulario -> linea($retica[0][0],1);
      $formulario -> linea("Cuenta Retención",0);
      $formulario -> linea($cuedeb[3][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[3][9],0);
      $formulario -> linea("Retefuente",0);
      $formulario -> linea($retefu[0][0],1);
     }
     $formulario -> linea("Cuenta Credito",0);
     $formulario -> linea($cuecre[0][8],0);
     $formulario -> linea("Nombre:",0);
     $formulario -> linea($cuecre[0][9],1);

     if($GLOBALS[tiptra]>= 40 && $GLOBALS[tiptra]< 50)//recaudo
     {
      $formulario -> linea("Ingresos Varios",0);
      $formulario -> linea($cuecre[1][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[1][9],1);
     }
     }
     else if($GLOBALS[tiptra] != 10)//Facturacion
     {

      $ano_actual = date("Y");

      $query = "SELECT abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                AND cod_retefu = '".$cuedeb[1][5]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retefu = $consulta->ret_matriz();

      $query = "SELECT abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                AND cod_retica = '".$cuedeb[2][7]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retica = $consulta->ret_matriz();

      $query = "SELECT abr_coniva
                FROM ".C_CONSULTOR.".tab_genera_coniva
                WHERE ano_vigiva = '$ano_actual'
                AND cod_coniva = '".$cuecre[1][6]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $coniva = $consulta->ret_matriz();

      $formulario -> linea("CxC Cliente",0);
      $formulario -> linea($cuedeb[0][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[0][9],1);
      $formulario -> linea("Cuenta Retención",0);
      $formulario -> linea($cuedeb[1][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[1][9],0);
      $formulario -> linea("Retención",0);
      $formulario -> linea($retefu[0][0],1);
      $formulario -> linea("Cuenta ICA",0);
      $formulario -> linea($cuedeb[2][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[2][9],0);
      $formulario -> linea("ICA",0);
      $formulario -> linea($retica[0][0],1);
      $formulario -> linea("Cuenta Faltantes",0);
      $formulario -> linea($cuedeb[3][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[3][9],1);
      $formulario -> linea("Cuenta Remesa",0);
      $formulario -> linea($cuecre[0][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[0][9],1);
      $formulario -> linea("Cuenta IVA",0);
      $formulario -> linea($cuecre[1][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[1][9],0);
      $formulario -> linea("IVA",0);
      $formulario -> linea($coniva[0][0],1);
     }
     else//Liquidacion
     {

      $ano_actual = date("Y");

      $query = "SELECT abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                AND cod_retefu = '".$cuecre[1][5]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retefu = $consulta->ret_matriz();

      $query = "SELECT abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                AND cod_retica = '".$cuecre[2][7]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retica = $consulta->ret_matriz();

      $query = "SELECT abr_coniva
                FROM ".C_CONSULTOR.".tab_genera_coniva
                WHERE ano_vigiva = '$ano_actual'
                AND cod_coniva = '".$cuecre[7][6]."'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $coniva = $consulta->ret_matriz();

      $formulario -> linea("Cuenta Flete",0);
      $formulario -> linea($cuedeb[0][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuedeb[0][9],1);
      $formulario -> linea("Cuenta Retención",0);
      $formulario -> linea($cuecre[1][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[1][9],0);
      $formulario -> linea("Retención",0);
      $formulario -> linea($retefu[0][0],1);
      $formulario -> linea("Cuenta ICA",0);
      $formulario -> linea($cuecre[2][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[2][9],0);
      $formulario -> linea("ICA", 0);
      $formulario -> linea($retica[0][0],1);
      $formulario -> linea("Cuenta Servicio Asistencia",0);
      $formulario -> linea($cuecre[3][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[3][9],1);
      $formulario -> linea("Cuenta Anticipos",0);
      $formulario -> linea($cuecre[4][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[4][9],1);
      $formulario -> linea("Faltantes",0);
      $formulario -> linea($cuecre[5][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[5][9],1);
      $formulario -> linea("Cuenta Valor Neto",0);
      $formulario -> linea($cuecre[6][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[6][9],1);
      $formulario -> linea("Cuenta IVA",0);
      $formulario -> linea($cuecre[7][8],0);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($cuecre[7][9],0);
      $formulario -> linea("IVA", 0);
      $formulario -> linea($coniva[0][0],1);
     }
     $formulario -> oculto("usuario","$usuario",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("opcion",1,0);
     $formulario -> nueva_tabla();
     $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
     $formulario -> botoni("Aceptar","compro_cuenta()",1);
     $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA
// *****************************************************


 function Insertar()
 {
  $fec_actual = date("Y-m-d H:i:s");
  $consec = 0;
  $consulta = new Consulta ("START TRANSACTION", $this -> conexion);

  if($GLOBALS[tiptra] == 10)
     $cuedeb[1] = $cuedeb[0];

  for($i=0; $i<sizeof($cuedeb); $i++)
  {
   $ica = "NULL";
   $ret = "NULL";
   if(($i == 1)&&($GLOBALS[tiptra] == 30))
      $ret = "'".$GLOBALS[retefu]."'";
   if(($i == 2)&&($GLOBALS[tiptra] == 30))
      $ica = "'".$GLOBALS[retica]."'";

      $tr = 0;
      if($GLOBALS[tiptra] != 30)
      {
          if($i == 1)
                  $tr = 1;
      }
   $query = "INSERT INTO ".BASE_DATOS.".tab_compro_cuenta(cod_tiptra,cod_tipcom,
                         cod_bancox,cod_agenci,num_consec,ind_nattra,
                         cod_clasec,cod_grupoc,cod_cuenta,cod_subcue,
                         cod_auxili,cod_retefu,cod_retica,usr_creaci, fec_creaci)
             VALUES ('$GLOBALS[tiptra]','$GLOBALS[tipcom]','$GLOBALS[banco]','$GLOBALS[agencia]',
                     '$consec','$tr','".substr($cuedeb[$i],0,1)."','".substr($cuedeb[$i],1,1)."',
                     '".substr($cuedeb[$i],2,2)."','".substr($cuedeb[$i],4,2)."','".substr($cuedeb[$i],6,2)."',
                     $ret,$ica,'$GLOBALS[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "R");
  $consec++;
  }
  for($i=0; $i<sizeof($GLOBALS[cuecre]); $i++)
  {
   $ica = "NULL";
   $ret = "NULL";
   if((!$i)&&($GLOBALS[tiptra] == 10))
      $ret = "'".$GLOBALS[retefu]."'";
   if(($i == 1)&&($GLOBALS[tiptra] == 10))
      $ica = "'".$GLOBALS[retica]."'";

   $query = "INSERT INTO ".BASE_DATOS.".tab_compro_cuenta(cod_tiptra,cod_tipcom,
                         cod_bancox,cod_agenci,num_consec,ind_nattra,
                         cod_clasec,cod_grupoc,cod_cuenta,cod_subcue,
                         cod_auxili,cod_retefu,cod_retica,usr_creaci,fec_creaci)
             VALUES ('$GLOBALS[tiptra]','$GLOBALS[tipcom]','$GLOBALS[banco]','$GLOBALS[agencia]',
                     '$consec','1','".substr($GLOBALS[cuecre][$i],0,1)."','".substr($GLOBALS[cuecre][$i],1,1)."',
                     '".substr($GLOBALS[cuecre][$i],2,2)."','".substr($GLOBALS[cuecre][$i],4,2)."','".substr($GLOBALS[cuecre][$i],6,2)."',
                     $ret,$ica,'$GLOBALS[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "R");
  $consec++;
  };

     if(!mysql_errno())
     {
         $consulta = new Consulta ("COMMIT", $this -> conexion);
         echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>Los Datos han sido Ingresados con Exito ";
     }
     else
         $consulta = new Consulta ("ROLLBACK", $this -> conexion);

  unset($GLOBALS[tiptra]);
  unset($GLOBALS[tipcom]);
  unset($GLOBALS[banco]);
  unset($GLOBALS[agenci]);
  unset($cuedeb);
  unset($GLOBALS[cuecre]);
 }//FIN FUNCION INSERT

}//FIN CLASE
     $proceso = new Lis_compro_cuenta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
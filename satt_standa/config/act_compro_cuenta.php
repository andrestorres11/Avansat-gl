<?php
class Act_compro_cuenta
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
     $formulario = new Formulario ("index.php","post","<b>Contable</b>","form_compro");
     $formulario -> lista("Transacción", "tiptra\" onChange=\"form_compro.banco.value=''; form_compro.tipcom.value=''; form_compro.agencia.value=''; form_compro.submit()", $antici, 0);
     if((($GLOBALS[tiptra] < 10)||($GLOBALS[tiptra] >= 20))&&(($GLOBALS[tiptra] < 60)||($GLOBALS[tiptra] >= 70))&&($GLOBALS[tiptra] != 3)&&($GLOBALS[tiptra] != 4)&&($GLOBALS[tiptra] != 7)&&($GLOBALS[tiptra] != 8)&&($GLOBALS[tiptra] != 21)&&(($GLOBALS[tiptra] < 30)||($GLOBALS[tiptra] >= 40))&&($GLOBALS[tiptra] != 41)&&($GLOBALS[tiptra] != 50))
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

	if(!$GLOBALS[tiptra])
	{
	   unset($GLOBALS[cuedeb]);
	   unset($GLOBALS[cuecre]);
	}

	if($GLOBALS[agencia] && !$GLOBALS[cuedeb][0])
     	{

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

	   for($i = 0; $i < sizeof($cuedeb); $i++)
	   {
	       $GLOBALS[cuedeb][$i] = $cuedeb[$i][8];
	       $auxdeb[$i] = $cuedeb[$i][9];
	   }

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

	   for($i = 0; $i < sizeof($cuecre); $i++)
	   {
	       $GLOBALS[cuecre][$i] = $cuecre[$i][8];
	       $auxcre[$i] = $cuecre[$i][9];
	   }

	  if($GLOBALS[tiptra]>= 40 && $GLOBALS[tiptra]< 50)//recaudo
          {
		$GLOBALS[retefu] = $cuedeb[3][5];
		$GLOBALS[retica] = $cuedeb[2][7];
	  }
	  else if(($GLOBALS[tiptra] >= 30)&&($GLOBALS[tiptra] < 40))//Facturacion
     	  {
		$GLOBALS[retefu] = $cuedeb[1][5];
		$GLOBALS[retica] = $cuedeb[2][7];
		$GLOBALS[coniva] = $cuecre[1][6];
	  }
	  else//liquidacion
     	  {
		$GLOBALS[retefu] = $cuecre[1][5];
		$GLOBALS[retica] = $cuecre[2][7];
		$GLOBALS[coniva] = $cuecre[7][6];
	  }
     }

     if((($GLOBALS[tiptra] < 10)||($GLOBALS[tiptra] >= 20))&&(($GLOBALS[tiptra] < 30)||($GLOBALS[tiptra] >= 40)))//no es liquidacion ni facturacion
     {
     	$query = "SELECT nom_cuenta
                 FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][0],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][0],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][0],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][0],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][0],6,2)."' AND
		       ind_movimi = '1'
                 GROUP BY 1 ";
     	$consulta = new Consulta($query, $this -> conexion);
     	if(!$auxdeb = $consulta -> ret_arreglo())
      	   unset($GLOBALS[cuedeb][0]);

     	$formulario -> texto("Cuenta Debito","text","cuedeb[0]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][0]);
     	$formulario -> linea("Nombre:",0);
     	$formulario -> linea($auxdeb[0],1);
     if($GLOBALS[tiptra]>= 40 && $GLOBALS[tiptra]< 50)//recaudo
     {
       $ano_actual = date("Y");

      $query = "SELECT cod_retica, abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                AND cod_retica = '$GLOBALS[retica]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$retica = $consulta->ret_matriz())
          $retica = $inicio;

      $query = "SELECT cod_retica, abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $reticas = $consulta->ret_matriz();
      $reticas = array_merge($retica, $reticas);

      $query = "SELECT cod_retefu, abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                AND cod_retefu = '$GLOBALS[retefu]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$retefu = $consulta->ret_matriz())
          $retefu = $inicio;

      $query = "SELECT cod_retefu, abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retefus = $consulta->ret_matriz();
      $retefus = array_merge($retefu, $retefus);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][1],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][1],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][1],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][1],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][1],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[1] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][1]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][2],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][2],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][2],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][2],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][2],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[2] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][2]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][3],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][3],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][3],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][3],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][3],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[3] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][3]);

      $formulario -> texto("Gastos Varios","text","cuedeb[1]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][1]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[1][0],1);
      $formulario -> texto("Cuenta ICA","text","cuedeb[2]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][2]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[2][0],0);
      $formulario -> lista("ICA", "retica", $reticas, 1);
      $formulario -> texto("Cuenta Retención","text","cuedeb[3]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][3]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[3][0],0);
      $formulario -> lista("Retefuente", "retefu", $retefus, 1);
     }

     $query = "SELECT nom_cuenta
                 FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][0],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][0],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][0],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][0],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][0],6,2)."' AND
		       ind_movimi = '1'
                 GROUP BY 1 ";
     $consulta = new Consulta($query, $this -> conexion);
     if(!$auxcre = $consulta -> ret_arreglo())
      unset($GLOBALS[cuecre][0]);

     $formulario -> texto("Cuenta Credito","text","cuecre[0]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][0]);
     $formulario -> linea("Nombre:",0);
     $formulario -> linea($auxcre[0],1);

     if($GLOBALS[tiptra]>= 40 && $GLOBALS[tiptra]< 50)//recaudo
     {
      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][1],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][1],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][1],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][1],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][1],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[1] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][1]);

      $formulario -> texto("Ingresos Varios","text","cuecre[1]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][1]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[1][0],1);
     }

     }
     else if(($GLOBALS[tiptra] >= 30)&&($GLOBALS[tiptra] < 40))//Facturacion
     {
      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][0],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][0],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][0],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][0],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][0],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[0] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][0]);

      //RETEFUENTE
      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][1],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][1],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][1],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][1],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][1],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[1] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][1]);

      $ano_actual = date("Y");

      $query = "SELECT cod_retefu, abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                AND cod_retefu = '$GLOBALS[retefu]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$retefu = $consulta->ret_matriz())
         $retefu = $inicio;

      $query = "SELECT cod_retefu, abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retefus = $consulta->ret_matriz();
      $retefus = array_merge($retefu, $retefus);

      //RETEICA
      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][2],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][2],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][2],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][2],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][2],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[2] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][2]);

      $query = "SELECT cod_retica, abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                AND cod_retica = '$GLOBALS[retica]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$retica = $consulta->ret_matriz())
          $retica = $inicio;

      $query = "SELECT cod_retica, abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $reticas = $consulta->ret_matriz();
      $reticas = array_merge($retica, $reticas);

      //IVA
      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][1],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][1],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][1],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][1],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][1],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[1] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][1]);

      $ano_actual = date("Y");

      $query = "SELECT cod_coniva, abr_coniva
                FROM ".C_CONSULTOR.".tab_genera_coniva
                WHERE ano_vigiva = '$ano_actual'
                AND cod_coniva = '$GLOBALS[coniva]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$coniva = $consulta->ret_matriz())
         $coniva = $inicio;

      $query = "SELECT cod_coniva, abr_coniva
                FROM ".C_CONSULTOR.".tab_genera_coniva
                WHERE ano_vigiva = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $conivas = $consulta->ret_matriz();
      $conivas = array_merge($coniva, $conivas);

      /////
      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][3],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][3],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][3],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][3],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][3],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[3] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][3]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][0],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][0],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][0],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][0],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][0],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[0] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][0]);

      $formulario -> texto("CxC Cliente","text","cuedeb[0]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][0]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[0][0],1);
      $formulario -> texto("Cuenta Retención","text","cuedeb[1]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][1]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[1][0],0);
      $formulario -> lista("Retención", "retefu", $retefus, 1);
      $formulario -> texto("Cuenta ICA","text","cuedeb[2]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][2]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[2][0],0);
      $formulario -> lista("ICA", "retica", $reticas, 1);
      $formulario -> texto("Cuenta Faltantes","text","cuedeb[3]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][3]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[3][0],1);
      $formulario -> texto("Cuenta Remesa","text","cuecre[0]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][0]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[0][0],1);
      $formulario -> texto("Cuenta IVA","text","cuecre[1]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][1]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[1][0],0);
      $formulario -> lista("IVA", "coniva", $conivas, 1);
     }
     else//Liquidacion
     {
	if($GLOBALS[agencia] && !$GLOBALS[corre])
  	   array_shift($GLOBALS[cuecre]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuedeb][0],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuedeb][0],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuedeb][0],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuedeb][0],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuedeb][0],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxdeb[0] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuedeb][0]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][6],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][6],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][6],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][6],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][6],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[6] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][6]);

      $ano_actual = date("Y");

      //RETEFUENTE
      $query = "SELECT cod_retefu, abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                AND cod_retefu = '$GLOBALS[retefu]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$retefu = $consulta->ret_matriz())
         $retefu = $inicio;

      $query = "SELECT cod_retefu, abr_retefu
                FROM ".C_CONSULTOR.".tab_genera_retefu
                WHERE ano_retefu = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $retefus = $consulta->ret_matriz();
      $retefus = array_merge($retefu, $retefus);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][0],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][0],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][0],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][0],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][0],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[0] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][0]);

      //RETEICA
      $query = "SELECT cod_retica, abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                AND cod_retica = '$GLOBALS[retica]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$retica = $consulta->ret_matriz())
          $retica = $inicio;

      $query = "SELECT cod_retica, abr_retica
                FROM ".C_CONSULTOR.".tab_genera_retica
                WHERE ano_retica = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $reticas = $consulta->ret_matriz();
      $reticas = array_merge($retica, $reticas);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][1],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][1],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][1],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][1],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][1],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[1] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][1]);

      //RETEIVA
      $query = "SELECT cod_coniva, abr_coniva
                FROM ".C_CONSULTOR.".tab_genera_coniva
                WHERE ano_vigiva = '$ano_actual'
                AND cod_coniva = '$GLOBALS[coniva]'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$coniva = $consulta->ret_matriz())
         $coniva = $inicio;

      $query = "SELECT cod_coniva, abr_coniva
                FROM ".C_CONSULTOR.".tab_genera_coniva
                WHERE ano_vigiva = '$ano_actual'
                GROUP BY 1";
      $consulta = new Consulta($query, $this -> conexion);
      $conivas = $consulta->ret_matriz();
      $conivas = array_merge($coniva, $conivas);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][2],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][2],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][2],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][2],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][2],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[2] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][2]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][3],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][3],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][3],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][3],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][3],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[3] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][3]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][4],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][4],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][4],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][4],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][4],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[4] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][4]);

      $query = "SELECT nom_cuenta
                  FROM ".C_CONSULTOR.".tab_genera_plancu
                 WHERE cod_clasec = '".substr($GLOBALS[cuecre][5],0,1)."' AND
                       cod_grupoc = '".substr($GLOBALS[cuecre][5],1,1)."' AND
                       cod_cuenta = '".substr($GLOBALS[cuecre][5],2,2)."' AND
                       cod_subcue = '".substr($GLOBALS[cuecre][5],4,2)."' AND
                       cod_auxili = '".substr($GLOBALS[cuecre][5],6,2)."' AND
		       ind_movimi = '1'";
      $consulta = new Consulta($query, $this -> conexion);
      if(!$auxcre[5] = $consulta -> ret_arreglo())
          unset($GLOBALS[cuecre][5]);

      $formulario -> texto("Cuenta Flete","text","cuedeb[0]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuedeb][0]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxdeb[0][0],1);
      $formulario -> texto("Cuenta Retención","text","cuecre[0]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][0]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[0][0],0);
      $formulario -> lista("Retención", "retefu", $retefus, 1);
      $formulario -> texto("Cuenta ICA","text","cuecre[1]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][1]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[1][0],0);
      $formulario -> lista("ICA", "retica", $reticas, 1);
      $formulario -> texto("Cuenta Servicio Asistencia","text","cuecre[2]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][2]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[2][0],1);
      $formulario -> texto("Cuenta Anticipos","text","cuecre[3]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][3]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[3][0],1);
      $formulario -> texto("Faltantes","text","cuecre[4]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][4]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[4][0],1);
      $formulario -> texto("Cuenta Valor Neto","text","cuecre[5]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][5]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[5][0],1);
      $formulario -> texto("Cuenta IVA","text","cuecre[6]\" onChange=\"form_compro.submit()",0,8,8,"",$GLOBALS[cuecre][6]);
      $formulario -> linea("Nombre:",0);
      $formulario -> linea($auxcre[6][0],0);
      $formulario -> lista("IVA", "coniva", $conivas, 1);
      if($GLOBALS[agencia])
      	$formulario -> oculto("corre",1,0);
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

  $query = "DELETE FROM ".BASE_DATOS.".tab_compro_cuenta
             	   WHERE cod_tiptra = '$GLOBALS[tiptra]'
		     AND cod_tipcom = '$GLOBALS[tipcom]'
		     AND cod_bancox = '$GLOBALS[banco]'
		     AND cod_agenci = '$GLOBALS[agencia]'";
  $insercion = new Consulta($query, $this -> conexion, "BR");

  if(($GLOBALS[tiptra] >= 10)&&($GLOBALS[tiptra] < 20))//liquidacion
     $GLOBALS[cuedeb][1] = $GLOBALS[cuedeb][0];
  for($i=0; $i<sizeof($GLOBALS[cuedeb]); $i++)
  {
   $ica = "NULL";
   $ret = "NULL";
   $iva = "NULL";
   if(($i == 1)&&($GLOBALS[tiptra] >= 30)&&($GLOBALS[tiptra] < 40))//facturacion
      $ret = "'".$GLOBALS[retefu]."'";
   else if(($i == 3)&&($GLOBALS[tiptra] >= 40)&&($GLOBALS[tiptra] < 50))//recaudos
      $ret = "'".$GLOBALS[retefu]."'";
  
   if(($i == 2)&&($GLOBALS[tiptra] >= 30)&&($GLOBALS[tiptra] < 50))//facturacion ó recaudos
      $ica = "'".$GLOBALS[retica]."'";

      $tr = 0;
      if(($GLOBALS[tiptra] >= 10)&&($GLOBALS[tiptra] < 20))//liquidacion
      {
          if($i == 1)
                  $tr = 1;
      }
   $query = "INSERT INTO ".BASE_DATOS.".tab_compro_cuenta(cod_tiptra,cod_tipcom,
                         cod_bancox,cod_agenci,num_consec,ind_nattra,
                         cod_clasec,cod_grupoc,cod_cuenta,cod_subcue,
                         cod_auxili,cod_retefu,cod_coniva,cod_retica,usr_modifi, fec_modifi)
             VALUES ('$GLOBALS[tiptra]','$GLOBALS[tipcom]','$GLOBALS[banco]','$GLOBALS[agencia]',
                     '$consec','$tr','".substr($GLOBALS[cuedeb][$i],0,1)."','".substr($GLOBALS[cuedeb][$i],1,1)."',
                     '".substr($GLOBALS[cuedeb][$i],2,2)."','".substr($GLOBALS[cuedeb][$i],4,2)."','".substr($GLOBALS[cuedeb][$i],6,2)."',
                     $ret,$iva,$ica,'$GLOBALS[usuario]','$fec_actual')";
  $insercion = new Consulta($query, $this -> conexion, "R");
  $consec++;
  }
  for($i=0; $i<sizeof($GLOBALS[cuecre]); $i++)
  {
   $ica = "NULL";
   $ret = "NULL";
   $iva = "NULL";
   if((!$i)&&($GLOBALS[tiptra] >= 10)&&($GLOBALS[tiptra] < 20))//liquidacion
      $ret = "'".$GLOBALS[retefu]."'";
   if(($i == 1)&&($GLOBALS[tiptra] >= 10)&&($GLOBALS[tiptra] < 20))//liquidacion
      $ica = "'".$GLOBALS[retica]."'";
   if(($i == 1)&&($GLOBALS[tiptra] >= 30)&&($GLOBALS[tiptra] < 40))//facturacion
      $iva = "'".$GLOBALS[coniva]."'";
   if(($i == 6)&&($GLOBALS[tiptra] >= 10)&&($GLOBALS[tiptra] < 20))//liquidacion
      $iva = "'".$GLOBALS[coniva]."'";

   $query = "INSERT INTO ".BASE_DATOS.".tab_compro_cuenta(cod_tiptra,cod_tipcom,
                         cod_bancox,cod_agenci,num_consec,ind_nattra,
                         cod_clasec,cod_grupoc,cod_cuenta,cod_subcue,
                         cod_auxili,cod_retefu,cod_coniva,cod_retica,usr_modifi,fec_modifi)
             VALUES ('$GLOBALS[tiptra]','$GLOBALS[tipcom]','$GLOBALS[banco]','$GLOBALS[agencia]',
                     '$consec','1','".substr($GLOBALS[cuecre][$i],0,1)."','".substr($GLOBALS[cuecre][$i],1,1)."',
                     '".substr($GLOBALS[cuecre][$i],2,2)."','".substr($GLOBALS[cuecre][$i],4,2)."','".substr($GLOBALS[cuecre][$i],6,2)."',
                     $ret,$iva,$ica,'$GLOBALS[usuario]','$fec_actual')";
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
  unset($GLOBALS[corre]);
 }//FIN FUNCION INSERT

}//FIN CLASE
     $proceso = new Act_compro_cuenta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
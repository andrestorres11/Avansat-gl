<?php

class Interfaz_SAT
{
 var $conexion,
     $interfaz,
     $totalact,
     $cliente,
     $bdsata,
     $transp,
     $usuari,
     $fecact,
     $resultado;

  function Interfaz_SAT($bdsata = "",$transp,$usuari,$conexion = "",$convisibi = 1,$ejecWSClient = 1)
  {
    if($conexion == "")
      $this -> conexion = new Conexion("bd10.intrared.net:3306",USUARIO,CLAVE,BASE_DATOS);
    else
      $this -> conexion = $conexion;

    $query = "SELECT a.cod_operad, a.nom_operad, a.nom_usuari, a.clv_usuari, a.val_timtra, a.url_webser ".
               "FROM ".BASE_DATOS.".tab_interf_parame a ".
              "WHERE a.ind_operad = '0' ".
                "AND a.ind_estado = '1' ";
    if($transp)
      $query .= " AND a.cod_transp = '".$transp."'";
    if($convisibi)
      $query .= " AND a.ind_intind = '1'";

    $consulta = new Consulta($query, $this -> conexion);
    $interf = $consulta  -> ret_matriz();

    $this -> totalact = sizeof($interf);

    for($i = 0; $i < sizeof($interf); $i++)
    {
      $this -> interfaz[$i]["operad"] = $interf[$i][0];
      $this -> interfaz[$i]["nombre"] = $interf[$i][1];
      $this -> interfaz[$i]["usuari"] = $interf[$i][2];
      $this -> interfaz[$i]["passwo"] = $interf[$i][3];
      $this -> interfaz[$i]["timtra"] = $interf[$i][4];
      $this -> interfaz[$i]["diwsdl"] = $interf[$i][5];
    }

    /*
    if($ejecWSClient)
      $this -> cliente = new soapclient(URL_INTERF_WEBSER."?wsdl", true);
	*/
    $this -> bdsata = $bdsata;
    $this -> transp = $transp;
    $this -> usuari = $usuari;
    $this -> fecact = date("Y-m-d H:i:s");
  }

 function Inicializar($transp,$usuari,$passwo)
 {
  $this -> transp = $transp;
  $this -> usuari = "U.I.A.SAT";
  $this -> fecact = date("Y-m-d H:i:s");

  $query = "SELECT a.nom_operad,a.val_timtra
	      FROM ".BASE_DATOS.".tab_interf_parame a
	     WHERE a.cod_transp = '".$this -> transp."' AND
		   a.nom_usuari = '".$usuari."' AND
		   a.clv_usuari = '".$passwo."' AND
		   a.ind_operad = '1' AND
		   a.ind_estado = '1'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $interfac = $consulta  -> ret_matriz();

  if($interfac)
   return TRUE;
  else
   return FALSE;
 }

 function getListadPC($operad,$usuari,$passwo)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad//Codigo del Operador
		 );

  return $this -> cliente -> call('FuncRetOperadPC', array('retOperadPCValue' => $schema));
 }

 function getHomoloPC($operad,$usuari,$passwo,$pctope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "pctope" => $pctope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloPC', array('getHomoloPCValue' => $schema));
 }

 function getHomoloTranspPC($operad,$usuari,$passwo,$pcttra)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "pcttra" => $pcttra//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloTranspPC', array('getHomoloTranspPCValue' => $schema));
 }

 function insHomoloPC($operad,$usuari,$passwo,$pcttra,$pctope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "pcttra" => $pcttra,//Puesto de Control del Cliente
	    "pctope" => $pctope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncInsHomoloPC', array('insHomoloPCValue' => $schema));
 }

 function actHomoloPC($operad,$usuari,$passwo,$pcttra,$pctope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "pcttra" => $pcttra,//Puesto de Control del Cliente
	    "pctope" => $pctope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncActHomoloPC', array('actHomoloPCValue' => $schema));
 }

 function eliHomoloPC($operad,$usuari,$passwo,$pcttra,$pctope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "pcttra" => $pcttra,//Puesto de Control del Cliente
	    "pctope" => $pctope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncEliHomoloPC', array('eliHomoloPCValue' => $schema));
 }

 function getListadRutasx($operad,$usuari,$passwo,$ciuori,$ciudes)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "ciuori" => $ciuori,//Codigo Ciudad Origen
	    "ciudes" => $ciudes//Codigo Ciudad Destino
		 );

  return $this -> cliente -> call('FuncRetOperadRutasx', array('retOperadRutasxValue' => $schema));
 }

 function getHomoloRutasx($operad,$usuari,$passwo,$rutope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "rutope" => $rutope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloRuta', array('getHomoloRutaValue' => $schema));
 }

 function getHomoloTranspRutasx($operad,$usuari,$passwo,$ruttra)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "ruttra" => $ruttra//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloTranspRutasx', array('getHomoloTranspRutasxValue' => $schema));
 }

 function getPlanruRutasx($operad,$usuari,$passwo,$rutasx)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "rutasx" => $rutasx//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncRetPlanruRutasx', array('retPlanruRutasxValue' => $schema));
 }

 function insHomoloRutasx($operad,$usuari,$passwo,$ruttra,$rutope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "ruttra" => $ruttra,//Puesto de Control del Cliente
	    "rutope" => $rutope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncInsHomoloRuta', array('insHomoloRutaValue' => $schema));
 }

 function actHomoloRutasx($operad,$usuari,$passwo,$ruttra,$rutope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "ruttra" => $ruttra,//Puesto de Control del Cliente
	    "rutope" => $rutope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncActHomoloRuta', array('actHomoloRutaValue' => $schema));
 }

 function eliHomoloRutasx($operad,$usuari,$passwo,$ruttra,$rutope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "ruttra" => $ruttra,//Puesto de Control del Cliente
	    "rutope" => $rutope//Puesto de Control del Operador
		 );

  return $this -> cliente -> call('FuncEliHomoloRuta', array('eliHomoloRutaValue' => $schema));
 }

 function getListadNoveda($operad,$usuari,$passwo)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad//Codigo del Operador
		 );

  return $this -> cliente -> call('FuncRetOperadNoveda', array('retOperadNovedaValue' => $schema));
 }

 function getHomoloNoveda($operad,$usuari,$passwo,$novope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "novope" => $novope//Novedad del Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloNoveda', array('getHomoloNovedaValue' => $schema));
 }

 function getHomoloTranspNoveda($operad,$usuari,$passwo,$novtra)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "novtra" => $novtra//Novedad del Cliente
		 );

  return $this -> cliente -> call('FuncGetHomoloTranspNoveda', array('getHomoloTranspNovedaValue' => $schema));
 }

 function insHomoloNoveda($operad,$usuari,$passwo,$novtra,$novope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "novtra" => $novtra,//Novedad del Cliente
	    "novope" => $novope//Novedad del Operador
		 );

  return $this -> cliente -> call('FuncInsHomoloNoveda', array('insHomoloNovedaValue' => $schema));
 }

 function actHomoloNoveda($operad,$usuari,$passwo,$novtra,$novope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "novtra" => $novtra,//Novedad del Cliente
	    "novope" => $novope//Novedad del Operador
		 );

  return $this -> cliente -> call('FuncActHomoloNoveda', array('actHomoloNovedaValue' => $schema));
 }

 function eliHomoloNoveda($operad,$usuari,$passwo,$novtra,$novope)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "novtra" => $novtra,//Novedad del Cliente
	    "novope" => $novope//Novedad del Operador
		 );

  return $this -> cliente -> call('FuncEliHomoloNoveda', array('eliHomoloNovedaValue' => $schema));
 }

 function insAgenci($operad,$usuari,$passwo,$agenci)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "agenci" => $agenci["agenci"],
	    "nombre" => $agenci["nombre"],
	    "contac" => $agenci["contac"],
	    "ciudad" => $agenci["ciudad"],
	    "direcc" => $agenci["direcc"],
	    "telefo" => $agenci["telefo"],
	    "correo" => $agenci["correo"],
	    "numfax" => $agenci["numfax"]
		 );

  return $this -> cliente -> call('FuncInsAgenci', array('insAgenciValue' => $schema));
 }

 function insTercer($operad,$usuari,$passwo,$tercer)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "tercer" => $tercer["tercer"],//# de Identificacion del tercer
	    "numver" => $tercer["numver"],//Digito de Verificacion
	    "tipdoc" => $tercer["tipdoc"],//Tipo de Documento
	    "terreg" => $tercer["terreg"],//Tipo de Regimen del Tercero
	    "apell1" => $tercer["apell1"],//Primer Apellido del Tercero
	    "apell2" => $tercer["apell2"],//Segundo Apellido del Tercero
	    "nombre" => $tercer["nombre"],//Nombre del Tercero
	    "abrevi" => $tercer["abrevi"],//Abreviatura del Tercer
	    "direcc" => $tercer["direcc"],//Direccion de Residencia u Establecimiento
	    "telef1" => $tercer["telef1"],//# Telefono Principal
	    "telef2" => $tercer["telef2"],//# Telefono Secundario
	    "telmov" => $tercer["telmov"],//# Telefono Celular u Movil
	    "numfax" => $tercer["numfax"],//# de Fax
	    "paisxx" => $tercer["paisxx"],//Codigo DANE del Pais
	    "depart" => $tercer["depart"],//Codigo DANE del Departamento
	    "ciudad" => $tercer["ciudad"],//Codigo DANE de la Ciudad
	    "emailx" => $tercer["emailx"],//Direccion Correo E-mail
	    "urlweb" => $tercer["urlweb"],//URL pagina WEB
	    "estado" => $tercer["estado"],//Codigo del estado actual del tercero
	    "observ" => $tercer["observ"],//Observaciones Generales
	    "obsapr" => $tercer["obsapr"],//Observaciones del Cambio de Estado

	    //Para el Envio de Informacion Conductor se Solicita

	    "tipsex" => $tercer["tipsex"],//Sexo del Conductor
	    "grupsa" => $tercer["grupsa"],//Grupo Sanguinio
	    "licenc" => $tercer["licenc"],//# Licencia de Conduccion
	    "catlic" => $tercer["catlic"],//Categoria de la Licencia
	    "venlic" => $tercer["venlic"],//Fecha de Vencimiento de la Licencia
	    "califi" => $tercer["califi"],//Codigo Calificacion
	    "libtri" => $tercer["libtri"],//# Libreta de Tripulacion
	    "ventri" => $tercer["ventri"],//Fecha Vencimiento Libreta de Tripulacion
	    "obshab" => $tercer["obshab"],//Observaciones de Cambio de Estado
	    "epsxxx" => $tercer["epsxxx"],//Nombre de la Entidad EPS
	    "arpxxx" => $tercer["arpxxx"],//Nombre de la Entidad ARP
	    "pensio" => $tercer["pensio"],//Nombre de la Entidad de Pensiones
	    "pasado" => $tercer["pasado"],//# de Pasado Judicial
	    "venpas" => $tercer["venpas"],//Fecha de Vencimiento Pasado Judicial
	    "nomref" => $tercer["nomref"],//Nombre de Referencia en Caso de Accidente
	    "telref" => $tercer["telref"],//# Telefeno de Referencia en Caso de Accidente
	    "obscon" => $tercer["obscon"],//Observaciones Generales
	    "activi" => $tercer["activi"]
		 );

  return $this -> cliente -> call('FuncInsTercer', array('insTercerValue' => $schema));
 }

 function insVehicu($operad,$usuari,$passwo,$vehicu)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "placax" => $vehicu["placax"],
		"marcax" => $vehicu["marcax"],
		"lineax" => $vehicu["lineax"],
		"anomod" => $vehicu["anomod"],
		"colorx" => $vehicu["colorx"],
		"carroc" => $vehicu["carroc"],
		"motorx" => $vehicu["motorx"],
		"seriex" => $vehicu["seriex"],
		"chasis" => $vehicu["chasis"],
		"pesove" => $vehicu["pesove"],
		"capaci" => $vehicu["capaci"],
		"nalcar" => $vehicu["nalcar"],
		"vigfin" => $vehicu["vigfin"],
		"anorep" => $vehicu["anorep"],
		"config" => $vehicu["config"],
		"propie" => $vehicu["propie"],
		"poseed" => $vehicu["poseed"],
		"conduc" => $vehicu["conduc"],
		"vincul" => $vehicu["vincul"],
		"tarpro" => $vehicu["tarpro"],
		"tarope" => $vehicu["tarope"],
		"califi" => $vehicu["califi"],
		"vigvin" => $vehicu["vigvin"],
		"polirc" => $vehicu["polirc"],
		"venprc" => $vehicu["venprc"],
		"aseprc" => $vehicu["aseprc"],
		"tipveh" => $vehicu["tipveh"],
		"chelis" => $vehicu["chelis"],
		"revmec" => $vehicu["revmec"],
		"agases" => $vehicu["agases"],
		"vengas" => $vehicu["vengas"],
		"observ" => $vehicu["observ"],
		"estado" => $vehicu["estado"],
		"obsest" => $vehicu["obsest"],
		"trayle" => $vehicu["trayle"],
		"tramar" => $vehicu["tramar"],
		"tracol" => $vehicu["tracol"],
		"tracar" => $vehicu["tracar"],
		"tramod" => $vehicu["tramod"],
		"traeje" => $vehicu["traeje"],
		"tracha" => $vehicu["tracha"],
		"traanc" => $vehicu["traanc"],
		"traalt" => $vehicu["traalt"],
		"tralar" => $vehicu["tralar"],
		"travol" => $vehicu["travol"],
		"trapes" => $vehicu["trapes"],
		"tracap" => $vehicu["tracap"],
		"tratra" => $vehicu["tratra"],
		"trapro" => $vehicu["trapro"],
		"tracfg" => $vehicu["tracfg"],
		"traest" => $vehicu["traest"]
		 );

  return $this -> cliente -> call('FuncInsVehicu', array('insVehicuValue' => $schema));
 }

 function insSalida($operad,$usuari,$passwo,$despac)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "despac" => $despac["despac"],
	    "manifi" => $despac["manifi"],
	    "fecdes" => $despac["fecdes"],
		"tipdes" => $despac["tipdes"],
		"client" => $despac["client"],
		"ciuori" => $despac["ciuori"],
		"ciudes" => $despac["ciudes"],
		"valfle" => $despac["valfle"],
		"valdes" => $despac["valdes"],
		"valant" => $despac["valant"],
		"valret" => $despac["valret"],
		"carpag" => $despac["carpag"],
		"despag" => $despac["despag"],
		"agedes" => $despac["agedes"],
		"fecpag" => $despac["fecpag"],
		"observ" => $despac["observ"],
		"salida" => $despac["salida"],
		"obssal" => $despac["obssal"],
		"numpol" => $despac["numpol"],
		"agenci" => $despac["agenci"],
		"rutasx" => $despac["rutasx"],
		"conduc" => $despac["conduc"],
		"placax" => $despac["placax"],
		"trayle" => $despac["trayle"],
		"proesp" => $despac["proesp"],
		"medcom" => $despac["medcom"],
		"salipl" => $despac["salipl"],
		"obsanu" => $despac["obsanu"],
	    "planru" => $despac["planru"],
	    "precon" => $despac["precon"],
	    "prenov" => $despac["prenov"],
	    "pretie" => $despac["pretie"]
		 );

  return $this -> cliente -> call('FuncInsSalida', array('insSalidaValue' => $schema));
 }

 function getHomoloDespac($operad,$usuari,$passwo,$destra)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "destra" => $destra,//Despacho Cliente
	    "repore" => 1
		 );

  return $this -> cliente -> call('FuncGetHomoloDespac', array('getHomoloDespacValue' => $schema));
 }

 function getHomoloContro($operad,$usuari,$passwo,$pcttra)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "pcttra" => $pcttra//Puesto de Control del Cliente
		 );

  return $this -> cliente -> call('FuncGetHomoloTranspPC', array('getHomoloTranspPCValue' => $schema));
 }

 function insNovedaPC($operad,$usuari,$passwo,$noveda)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "despac" => $noveda["despac"],//
	    "contro" => $noveda["contro"],//
	    "noveda" => $noveda["noveda"],//
	    "duraci" => $noveda["duraci"],//
	    "fechax" => $noveda["fechax"],//
	    "observ" => $noveda["observ"],//
	    "repore" => 1
		 );

  return $this -> cliente -> call('FuncInsNovePC', array('insNovePCValue' => $schema));
 }

 function insNovedaNC($operad,$usuari,$passwo,$noveda)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "despac" => $noveda["despac"],//
	    "contro" => $noveda["contro"],//
	    "noveda" => $noveda["noveda"],//
	    "duraci" => $noveda["duraci"],//
	    "fechax" => $noveda["fechax"],//
	    "observ" => $noveda["observ"],//
	    "repore" => 1
		 );

  return $this -> cliente -> call('FuncInsNoveNC', array('insNoveNCValue' => $schema));
 }

 function insLlegad($operad,$usuari,$passwo,$despac)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "despac" => $despac["despac"],//
	    "fechax" => $despac["fechax"],//
	    "observ" => $despac["observ"]//
		 );

  return $this -> cliente -> call('FuncInsLlegad', array('insLlegadValue' => $schema));
 }

 function insAnular($operad,$usuari,$passwo,$despac)
 {
  $schema = array(
	    "usuari" => $usuari,//Usuario
	    "passwo" => $passwo,//Password
	    "bdsata" => $this -> bdsata,//Base de Datos
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $operad,//Codigo del Operador
	    "despac" => $despac["despac"],//
	    "fechax" => $despac["fechax"],//
	    "observ" => $despac["observ"],//
	    "placax" => $despac["placax"],//
	    "rutasx" => $despac["rutasx"],//
	    "tipoan" => $despac["tipoan"]//
		 );

  return $this -> cliente -> call('FuncInsAnular', array('insAnularValue' => $schema));
 }

 function envDespacNovePC($despac)
 {
  $query= "SELECT MAX(e.fec_noveda)
             FROM ".BASE_DATOS.".tab_despac_vehige c,
             	  ".BASE_DATOS.".tab_despac_seguim d,
                  ".BASE_DATOS.".tab_despac_noveda e
            WHERE c.num_despac = d.num_despac AND
                  c.num_despac = e.num_despac AND
                  c.num_despac = ".$despac["despac"]."
          ";

  $consulta = new Consulta($query, $this -> conexion);
  $ultrep = $consulta -> ret_matriz();

  $query_new = "\n".str_replace("'","\'",$query);

     $query = "INSERT INTO ".$base_datos.".query value ('".$query_new."')
              ";

     $insercion = new Consulta($query, $this -> conexion);

  $regist["nittra"] = NIT_TRANSPOR;
  $regist["despac"] = $despac["despac"];
  $regist["contro"] = $despac["contro"];
  $regist["noveda"] = $despac["noveda"];
  $regist["tieadi"] = $despac["tieadi"];
  $regist["observ"] = $despac["observ"];
  $regist["fecnov"] = $despac["fechax"];
  $regist["fecact"] = $this -> fecact;
  $regist["ultrep"] = $ultrep[0][0];
  $regist["usuari"] = $this -> usuari;

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos(NULL,NULL,NULL,$this -> conexion);
  $RESPON = $transac_nov -> InsertarNovedadPC(BASE_DATOS,$regist,2);

  if($RESPON[0]["indica"])
  {
   $consulta = new Consulta ("COMMIT", $this -> conexion);
   return "";
  }
  else
   return $mensaje = $RESPON[0]["mensaj"];
 }

 function envDespacNoveNC($despac)
 {
  $regist["despac"] = $despac["despac"];
  $regist["contro"] = $despac["contro"];
  $regist["noveda"] = $despac["noveda"];
  $regist["tieadi"] = $despac["duraci"];
  $regist["observ"] = $despac["observ"];
  $regist["fecact"] = $this -> fecact;
  $regist["usuari"] = $this -> usuari;
  $regist["nittra"] = NIT_TRANSPOR;

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos(NULL,NULL,NULL,$this -> conexion);
  $RESPON = $transac_nov -> InsertarNovedadNC(BASE_DATOS,$regist,2);

  if($RESPON[0]["indica"])
  {
   $consulta = new Consulta ("COMMIT", $this -> conexion);
   return "";
  }
  else
   return $RESPON[0]["mensaj"];
 }
}

class Interfaz_SAT_client
{
 var $conexion,
     $transp,
     $interfaz,
     $usuari,
     $fecact,
     $cliente;

 function Interfaz_SAT_client($conexion = "")
 {
  if($conexion == "")
   $this -> conexion = new Conexion("bd10.intrared.net:3306",USUARIO,CLAVE,BASE_DATOS);
  else
   $this -> conexion = $conexion;
 }

 function Inicializar_client($transp,$usuari,$passwo)
 {
  $this -> transp = $transp;
  $this -> usuari = "U.I.A.SAT";
  $this -> fecact = date("Y-m-d H:i:s");

  $query = "SELECT a.nom_operad,a.val_timtra
	      FROM ".BASE_DATOS.".tab_interf_parame a
	     WHERE a.cod_transp = '".$this -> transp."' AND
		   a.nom_usuari = '".$usuari."' AND
		   a.clv_usuari = '".$passwo."' AND
		   a.ind_operad = '0' AND
		   a.ind_estado = '1'
	   ";
  $consulta = new Consulta($query, $this -> conexion);
  $interfac = $consulta  -> ret_matriz();

  if($interfac)
   return TRUE;
  else
   return FALSE;
 }

 function Inicializar_client_cliente($transp)
 {
  $this -> transp = $transp;
  $this -> usuari = "U.I.A.SAT";
  $this -> fecact = date("Y-m-d H:i:s");

  $query = "SELECT a.val_timtra,a.nom_usuari,a.clv_usuari,a.cod_operad
	      FROM ".BASE_DATOS.".tab_interf_parame a
	     WHERE a.cod_transp = '".$this -> transp."' AND
		   a.ind_operad = '0' AND
		   a.ind_estado = '1'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $interfac = $consulta  -> ret_matriz();

  $this -> interfaz["timtra"] = $interfac[0][0];
  $this -> interfaz["usuari"] = $interfac[0][1];
  $this -> interfaz["passwo"] = $interfac[0][2];
  $this -> interfaz["operad"] = $interfac[0][3];

  $this -> cliente = new soapclient(URL_INTERF_WEBSER_OPERAD."?wsdl", true);

  if($interfac)
   return TRUE;
  else
   return FALSE;
 }

 function Inicializar_client_infoTT($usr,$pwd)
 {
  $this -> usuari = "U.I.A.SAT";
  $this -> fecact = date("Y-m-d H:i:s");
  return true;
 }

 //funciones desde el operador hacia central

 function retListadHomoloNoveda()
 {
  $schema = array(
	    "usuari" => $this -> interfaz["usuari"],//Usuario
	    "passwo" => $this -> interfaz["passwo"],//Password
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $this -> interfaz["operad"]
		 );

  return $this -> cliente -> call('FuncRetOperadNoveda', array('retOperadNovedaValue' => $schema));
 }

 function getHomoloDespacClient($despac)
 {
  $schema = array(
	    "usuari" => $this -> interfaz["usuari"],//Usuario
	    "passwo" => $this -> interfaz["passwo"],//Password
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $this -> interfaz["operad"],//Codigo del Operador
	    "desope" => $despac//Codigo del Despacho Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloDespacOperad', array('getHomoloDespacOperadValue' => $schema));
 }

 function getHomoloControClient($contro)
 {
  $schema = array(
	    "usuari" => $this -> interfaz["usuari"],//Usuario
	    "passwo" => $this -> interfaz["passwo"],//Password
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $this -> interfaz["operad"],//Codigo del Operador
	    "contro" => $contro//Codigo del P/C Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloPC', array('getHomoloPCValue' => $schema));
 }

 function getHomoloNovedaClient($noveda)
 {
  $schema = array(
	    "usuari" => $this -> interfaz["usuari"],//Usuario
	    "passwo" => $this -> interfaz["passwo"],//Password
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $this -> interfaz["operad"],//Codigo del Operador
	    "noveda" => $noveda//Codigo de la Novedad Operador
		 );

  return $this -> cliente -> call('FuncGetHomoloNoveda', array('getHomoloNovedaValue' => $schema));
 }

 function insNovedaPC($noveda)
 {
  $schema = array(
	    "usuari" => $this -> interfaz["usuari"],//Usuario
	    "passwo" => $this -> interfaz["passwo"],//Password
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $this -> interfaz["operad"],//Codigo del Operador
	    "despac" => $noveda["despac"],//
	    "contro" => $noveda["contro"],//
	    "noveda" => $noveda["noveda"],//
	    "duraci" => $noveda["duraci"],//
	    "fechax" => $noveda["fechax"],//
	    "observ" => $noveda["observ"]//
		 );

  return $this -> cliente -> call('FuncInsNovePC', array('insNovePCValue' => $schema));
 }

 function insNovedaNC($noveda)
 {
  $schema = array(
	    "usuari" => $this -> interfaz["usuari"],//Usuario
	    "passwo" => $this -> interfaz["passwo"],//Password
	    "transp" => $this -> transp,//NIT Transportadora
	    "operad" => $this -> interfaz["operad"],//Codigo del Operador
	    "despac" => $noveda["despac"],//
	    "contro" => $noveda["contro"],//
	    "noveda" => $noveda["noveda"],//
	    "duraci" => $noveda["duraci"],//
	    "fechax" => $noveda["fechax"],//
	    "observ" => $noveda["observ"]//
		 );

  return $this -> cliente -> call('FuncInsNoveNC', array('insNoveNCValue' => $schema));
 }

 //funciones de Insercion desde el Cliente

 function insActiviTercer($tercer)
 {
  if($tercer["activi"] == 1)
   $tercer["activi"] = COD_FILTRO_CLIENT;
  else if($tercer["activi"] == 2)
   $tercer["activi"] = COD_FILTRO_PROPIE;
  else if($tercer["activi"] == 3)
   $tercer["activi"] = COD_FILTRO_POSEED;
  else if($tercer["activi"] == 4)
   $tercer["activi"] = COD_FILTRO_CONDUC;

  $query = "SELECT a.cod_tercer
	          FROM ".BASE_DATOS.".tab_tercer_activi a
	         WHERE a.cod_tercer = '".$tercer["tercer"]."' AND
		   		   a.cod_activi = ".$tercer["activi"]."
	      ";

  $consulta = new Consulta($query, $this -> conexion);
  $existact = $consulta  -> ret_matriz();

  if(!$existact)
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_activi
			 			 (cod_tercer,cod_activi)
		  		  VALUES ('".$tercer["tercer"]."',".$tercer["activi"].")
	       ";

   if($consulta = new Consulta($query, $this -> conexion,"BRC"))
    return TRUE;
   else
    return FALSE;
  }
  else
   return TRUE;
 }

 function insRelaciTercer($tercer)
 {
  $query = "SELECT a.cod_tercer
	          FROM ".BASE_DATOS.".tab_transp_tercer a
	         WHERE a.cod_tercer = '".$tercer["tercer"]."' AND
		   		   a.cod_transp = '".$this -> transp."'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existrel = $consulta  -> ret_matriz();

  if(!$existrel)
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer
			 			 (cod_transp,cod_tercer,usr_creaci,fec_creaci)
		          VALUES ('".$this -> transp."','".$tercer["tercer"]."','".$this -> usuari."','".$this -> fecact."')
	    	";

   if($consulta = new Consulta($query, $this -> conexion,"BRC"))
    return TRUE;
   else
    return FALSE;
  }
  else
   return TRUE;
 }

 function retExistTercer($tercer)
 {
  $query = "SELECT a.cod_tercer
	          FROM ".BASE_DATOS.".tab_tercer_tercer a
	         WHERE a.cod_tercer = '".$tercer."'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existerc = $consulta  -> ret_matriz();

  if($existerc)
   return TRUE;
  else
   return FALSE;
 }

 function retExistConduc($tercer)
 {
  $query = "SELECT a.cod_tercer
	          FROM ".BASE_DATOS.".tab_tercer_conduc a
	         WHERE a.cod_tercer = '".$tercer."'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existerc = $consulta  -> ret_matriz();

  if($existerc)
   return TRUE;
  else
   return FALSE;
 }

 function envTercer($tercer)
 {
  $query = "SELECT a.cod_paisxx,a.cod_depart
  		      FROM ".BASE_DATOS.".tab_genera_ciudad a
  		     WHERE a.cod_ciudad = ".$tercer["ciudad"]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $infopade = $consulta  -> ret_matriz();

  if(!$tercer["terreg"] || $tercer["terreg"] == "")
   $tercer["terreg"] = "NULL";

  if(!$tercer["paisxx"])
   $tercer["paisxx"] = $infopade[0][0];

  if(!$tercer["depart"])
   $tercer["depart"] = $infopade[0][1];

  if(!$tercer["estado"])
   $tercer["estado"] = 0;

  if(!$this -> retExistTercer($tercer["tercer"]))
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
			 			 (cod_tercer,num_verifi,cod_tipdoc,cod_terreg,nom_apell1,
		          		  nom_apell2,nom_tercer,abr_tercer,dir_domici,num_telef1,
		          		  num_telef2,num_telmov,num_faxxxx,cod_paisxx,cod_depart,
		          		  cod_ciudad,dir_emailx,dir_urlweb,cod_estado,dir_ultfot,
		          		  obs_tercer,obs_aproba,usr_creaci,fec_creaci
		          		 )
		  		  VALUES ('".$tercer["tercer"]."','".$tercer["numver"]."','".$tercer["tipdoc"]."',".$tercer["terreg"].",
		  		  		  '".$tercer["apell1"]."','".$tercer["apell2"]."','".$tercer["nombre"]."','".$tercer["abrevi"]."',
		  		  		  '".$tercer["direcc"]."','".$tercer["telef1"]."','".$tercer["telef2"]."','".$tercer["telmov"]."',
		  		  		  '".$tercer["numfax"]."',".$tercer["paisxx"].",".$tercer["depart"].",".$tercer["ciudad"].",
		  		  		  '".$tercer["emailx"]."','".$tercer["urlweb"]."','".$tercer["estado"]."',NULL,
		  		  		  '".$tercer["observ"]."','".$tercer["obsapr"]."','".$this -> usuari."','".$this -> fecact."')
	    	";
  }
  else
  {
   $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
				SET num_verifi = '".$tercer["numver"]."',
					cod_tipdoc = '".$tercer["tipdoc"]."',
					cod_terreg = ".$tercer["terreg"].",
					nom_apell1 = '".$tercer["apell1"]."',
					nom_apell2 = '".$tercer["apell2"]."',
					nom_tercer = '".$tercer["nombre"]."',
					abr_tercer = '".$tercer["abrevi"]."',
					dir_domici = '".$tercer["direcc"]."',
					num_telef1 = '".$tercer["telef1"]."',
					num_telef2 = '".$tercer["telef2"]."',
					num_telmov = '".$tercer["telmov"]."',
					num_faxxxx = '".$tercer["numfax"]."',
					cod_paisxx = ".$tercer["paisxx"].",
					cod_depart = ".$tercer["depart"].",
					cod_ciudad = ".$tercer["ciudad"].",
					dir_emailx = '".$tercer["emailx"]."',
					dir_urlweb = '".$tercer["urlweb"]."',
					cod_estado = '".$tercer["estado"]."',
					dir_ultfot = NULL,
					obs_tercer = '".$tercer["observ"]."',
					obs_aproba = '".$tercer["obsapr"]."',
					usr_modifi = '".$this -> usuari."',
					fec_modifi = '".$this -> fecact."'
	      	  WHERE cod_tercer = '".$tercer["tercer"]."'
	    	";
  }

  $consulta = new Consulta($query, $this -> conexion,"BRC");

  if($tercer["activi"] == 4)
  {
   if(!$tercer["califi"])
    $tercer["califi"] = 2;

   if(!$this -> retExistConduc($tercer["tercer"]))
   {
   	$query = "INSERT INTO ".BASE_DATOS.".tab_tercer_conduc
   			 			  (cod_tercer,cod_tipsex,cod_grupsa,num_licenc,
   			 			   num_catlic,fec_venlic,cod_califi,num_libtri,
   			 			   fec_ventri,obs_habili,nom_epsxxx,nom_arpxxx,
   			 			   nom_pensio,num_pasado,fec_venpas,nom_refper,
   			 			   tel_refper,obs_conduc,usr_creaci,fec_creaci
   			 			  )
   			 	   VALUES ('".$tercer["tercer"]."','".$tercer["tipsex"]."','".$tercer["grupsa"]."','".$tercer["licenc"]."',
   			 	   		   '".$tercer["catlic"]."','".$tercer["venlic"]."',".$tercer["califi"].",'".$tercer["libtri"]."',
   			 	   		   '".$tercer["ventri"]."','".$tercer["obshab"]."','".$tercer["epsxxx"]."','".$tercer["arpxxx"]."',
   			 	   		   '".$tercer["pensio"]."','".$tercer["pasado"]."','".$tercer["venpas"]."','".$tercer["nomref"]."',
   			 	   		   '".$tercer["telref"]."','".$tercer["obscon"]."','".$this -> usuari."','".$this -> fecact."'
   			 	   		  )
   			 ";
   }
   else
   {
   	$query = "UPDATE ".BASE_DATOS.".tab_tercer_conduc
   			     SET cod_tipsex = '".$tercer["tipsex"]."',
   			     	 cod_grupsa = '".$tercer["grupsa"]."',
   			     	 num_licenc = '".$tercer["licenc"]."',
   			     	 num_catlic = '".$tercer["catlic"]."',
   			     	 fec_venlic = '".$tercer["venlic"]."',
   			     	 cod_califi = ".$tercer["califi"].",
   			     	 num_libtri = '".$tercer["libtri"]."',
   			     	 fec_ventri = '".$tercer["ventri"]."',
   			     	 obs_habili = '".$tercer["obshab"]."',
   			     	 nom_epsxxx = '".$tercer["epsxxx"]."',
   			     	 nom_arpxxx = '".$tercer["arpxxx"]."',
   			     	 nom_pensio = '".$tercer["pensio"]."',
   			     	 num_pasado = '".$tercer["pasado"]."',
   			     	 fec_venpas = '".$tercer["venpas"]."',
   			     	 nom_refper = '".$tercer["nomref"]."',
   			     	 tel_refper = '".$tercer["telref"]."',
   			     	 obs_conduc = '".$tercer["obscon"]."',
   			     	 usr_modifi = '".$this -> usuari."',
   			     	 fec_modifi = '".$this -> fecact."'
   			   WHERE cod_tercer = '".$tercer["tercer"]."'
   			 ";
   }

   $consulta = new Consulta($query, $this -> conexion,"BRC");
  }

  if($this -> insRelaciTercer($tercer) && $this -> insActiviTercer($tercer))
   return TRUE;
  else
   return FALSE;
 }

 function envAgenci($agenci)
 {
  $query = "SELECT a.cod_agenci
	          FROM ".BASE_DATOS.".tab_transp_agenci a
	         WHERE a.cod_agetra = ".$agenci["agenci"]." AND
		   		   a.cod_transp = '".$this -> transp."'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existage = $consulta  -> ret_matriz();

  $query = "SELECT a.cod_paisxx,a.cod_depart
  		      FROM ".BASE_DATOS.".tab_genera_ciudad a
  		     WHERE a.cod_ciudad = ".$agenci["ciudad"]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $infopade = $consulta  -> ret_matriz();

  if(!$existage)
  {
   $query = "SELECT MAX(a.cod_agenci)
   		       FROM ".BASE_DATOS.".tab_genera_agenci a
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $consecut = $consulta  -> ret_matriz();

   $consecut[0][0] += 1;

   $query = "INSERT INTO ".BASE_DATOS.".tab_genera_agenci
			 			 (cod_agenci,nom_agenci,cod_paisxx,cod_depart,
			  			  cod_ciudad,dir_agenci,tel_agenci,con_agenci,
			  			  dir_emailx,num_faxxxx,usr_creaci,fec_creaci)
		  		  VALUES (".$consecut[0][0].",'".$agenci["nombre"]."',".$infopade[0][0].",".$infopade[0][1].",
			  			  ".$agenci["ciudad"].",'".$agenci["direcc"]."','".$agenci["telefo"]."','".$agenci["contac"]."',
			  			  '".$agenci["correo"]."','".$agenci["numfax"]."','".$this -> usuari."','".$this -> fecact."')
	    	";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   $query = "INSERT INTO ".BASE_DATOS.".tab_transp_agenci
   		                 (cod_transp,cod_agenci,cod_agetra)
   		          VALUES ('".$this -> transp."',".$consecut[0][0].",".$agenci["agenci"].")
   		    ";

   $consulta = new Consulta($query, $this -> conexion,"R");
  }
  else
  {
   $query = "UPDATE ".BASE_DATOS.".tab_genera_agenci
				SET nom_agenci = '".$agenci["nombre"]."',
					cod_paisxx = ".$infopade[0][0].",
					cod_depart = ".$infopade[0][1].",
					cod_ciudad = ".$agenci["ciudad"].",
					dir_agenci = '".$agenci["direcc"]."',
					tel_agenci = '".$agenci["telefo"]."',
					con_agenci = '".$agenci["contac"]."',
					dir_emailx = '".$agenci["correo"]."',
					num_faxxxx = '".$agenci["numfax"]."',
		    		usr_modifi = '".$this -> usuari."',
		    		fec_modifi = '".$this -> fecact."'
	          WHERE cod_agenci = ".$existage[0][0]."
	    ";

   $consulta = new Consulta($query, $this -> conexion,"BR");
  }

  if($consulta = new Consulta("COMMIT", $this -> conexion))
   return TRUE;
  else
   return FALSE;
 }

 function retExistVehicu($vehicu)
 {
  $query = "SELECT a.num_placax
	          FROM ".BASE_DATOS.".tab_vehicu_vehicu a
	         WHERE a.num_placax = '".$vehicu."'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existveh = $consulta  -> ret_matriz();

  if($existveh)
   return TRUE;
  else
   return FALSE;
 }

 function insRelaciVehicu($vehicu)
 {
  $query = "SELECT a.num_placax
	      	  FROM ".BASE_DATOS.".tab_transp_vehicu a
	         WHERE a.num_placax = '".$vehicu["placax"]."' AND
		   		   a.cod_transp = '".$this -> transp."'
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existrel = $consulta  -> ret_matriz();

  if(!$existrel)
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_transp_vehicu
			 			 (num_placax,cod_transp,usr_creaci,fec_creaci)
		  		  VALUES ('".$vehicu["placax"]."','".$this -> transp."','".$this -> usuari."','".$this -> fecact."')
	    ";

   if($consulta = new Consulta($query, $this -> conexion,"BRC"))
    return TRUE;
   else
    return FALSE;
  }
  else
   return TRUE;
 }

 function envVehicu($vehicu)
 {
  if(!$vehicu["anorep"])
   $vehicu["anorep"] = "NULL";

  if(!$vehicu["califi"])
   $vehicu["califi"] = 2;

  if(!$vehicu["estado"])
   $vehicu["estado"] = 0;

  if(!$this -> retExistVehicu($vehicu["placax"]))
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_vehicu_vehicu
			 			 (num_placax,cod_marcax,cod_lineax,ano_modelo,
			  			  cod_colorx,cod_carroc,num_motorx,num_seriex,
			  			  num_chasis,val_pesove,val_capaci,reg_nalcar,
			  			  fec_vigfin,ano_repote,num_config,cod_propie,
		      		  	  cod_tenedo,cod_conduc,nom_vincul,num_tarpro,
		      		  	  num_tarope,cod_califi,fec_vigvin,num_polirc,
		      		  	  fec_venprc,cod_aseprc,cod_tipveh,ind_chelis,
		      		  	  fec_revmec,num_agases,fec_vengas,obs_vehicu,
		      		  	  ind_estado,obs_estado,usr_creaci,fec_creaci)
		 		  VALUES ('".$vehicu["placax"]."','".$vehicu["marcax"]."','".$vehicu["lineax"]."',".$vehicu["anomod"].",
		 		  		  ".$vehicu["colorx"].",".$vehicu["carroc"].",'".$vehicu["motorx"]."','".$vehicu["seriex"]."',
		 		  		  '".$vehicu["chasis"]."',".$vehicu["pesove"].",".$vehicu["capaci"].",'".$vehicu["nalcar"]."',
		 		  		  '".$vehicu["vigfin"]."',".$vehicu["anorep"].",'".$vehicu["config"]."','".$vehicu["propie"]."',
		      		  	  '".$vehicu["poseed"]."','".$vehicu["conduc"]."','".$vehicu["vincul"]."','".$vehicu["tarpro"]."',
		      		  	  '".$vehicu["tarope"]."',".$vehicu["califi"].",'".$vehicu["vigvin"]."','".$vehicu["polirc"]."',
		      		  	  '".$vehicu["venprc"]."','".$vehicu["aseprc"]."',".$vehicu["tipveh"].",'".$vehicu["chelis"]."',
		      		  	  '".$vehicu["revmec"]."','".$vehicu["agases"]."','".$vehicu["vengas"]."','".$vehicu["observ"]."',
		      		  	  '".$vehicu["estado"]."','".$vehicu["obsest"]."','".$this -> usuari."','".$this -> fecact."')
	    	";
  }
  else
  {
   $query = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu
				SET cod_marcax = '".$vehicu["marcax"]."',
		    		cod_lineax = '".$vehicu["lineax"]."',
		    		ano_modelo = ".$vehicu["anomod"].",
		    		cod_colorx = ".$vehicu["colorx"].",
		    		cod_carroc = ".$vehicu["carroc"].",
		    		num_motorx = '".$vehicu["motorx"]."',
		    		num_seriex = '".$vehicu["seriex"]."',
		    		num_chasis = '".$vehicu["chasis"]."',
		    		val_pesove = ".$vehicu["pesove"].",
		    		val_capaci = ".$vehicu["capaci"].",
		    		reg_nalcar = '".$vehicu["nalcar"]."',
		    		fec_vigfin = '".$vehicu["vigfin"]."',
		    		ano_repote = ".$vehicu["anorep"].",
		    		num_config = '".$vehicu["config"]."',
		    		cod_propie = '".$vehicu["propie"]."',
		    		cod_tenedo = '".$vehicu["poseed"]."',
		    		cod_conduc = '".$vehicu["conduc"]."',
		    		nom_vincul = '".$vehicu["vincul"]."',
		    		num_tarpro = '".$vehicu["tarpro"]."',
		    		num_tarope = '".$vehicu["tarope"]."',
		    		cod_califi = ".$vehicu["califi"].",
		    		fec_vigvin = '".$vehicu["vigvin"]."',
		    		num_polirc = '".$vehicu["polirc"]."',
		    		fec_venprc = '".$vehicu["venprc"]."',
		    		cod_aseprc = '".$vehicu["aseprc"]."',
		    		cod_tipveh = ".$vehicu["tipveh"].",
		    		ind_chelis = '".$vehicu["chelis"]."',
		    		fec_revmec = '".$vehicu["revmec"]."',
		    		num_agases = '".$vehicu["agases"]."',
		    		fec_vengas = '".$vehicu["vengas"]."',
		    		obs_vehicu = '".$vehicu["observ"]."',
		    		ind_estado = '".$vehicu["estado"]."',
		    		obs_estado = '".$vehicu["obsest"]."',
		    		usr_modifi = '".$this -> usuari."',
		    		fec_modifi = '".$this -> fecact."'
	      WHERE num_placax = '".$vehicu["placax"]."'
	    ";
  }

  if($consulta = new Consulta($query, $this -> conexion,"BRC"))
  {
   if($this -> insRelaciVehicu($vehicu))
   {
	if($vehicu["trayle"])
	 if($this -> envTrayle($vehicu))
	  return TRUE;
	 else
	  return FALSE;
	else
   	 return TRUE;
   }
   else
    return FALSE;
  }
  else
   return FALSE;
 }

 function retExistTrayle($trayle)
 {
  $query = "SELECT a.num_trayle
  		      FROM ".BASE_DATOS.".tab_vehige_trayle a
  		     WHERE a.num_trayle = '".$trayle."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existtra = $consulta  -> ret_matriz();

  if($existtra)
   return TRUE;
  else
   return FALSE;
 }

 function insRelaciTrayle($trayle)
 {
  $query = "SELECT a.num_trayle
  		      FROM ".BASE_DATOS.".tab_transp_trayle a
  		     WHERE a.cod_transp = '".$this -> transp."' AND
  		   		   a.num_trayle = '".$trayle["trayle"]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existrel = $consulta  -> ret_matriz();

  if(!$existrel)
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_transp_trayle
   		    			 (cod_transp,num_trayle,usr_creaci,fec_creaci)
   		    	  VALUES ('".$this -> transp."','".$trayle["trayle"]."','".$this -> usuari."','".$this -> fecact."')
   		    ";

   if($consulta = new Consulta($query, $this -> conexion,"BRC"))
    return TRUE;
   else
    return FALSE;
  }
  else
   return TRUE;
 }

 function envTrayle($trayle)
 {
  if ( $trayle["trayle"] == "1")
   return TRUE;
 
  $query = "SELECT a.cod_martra
  		      FROM ".BASE_DATOS.".tab_vehige_martra a
  		     WHERE a.cod_martra = '".$trayle["tramar"]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existmar = $consulta  -> ret_matriz();

  if(!$existmar)
   $trayle["tramar"] = "NN";

  if(!$trayle["tracol"])
   $trayle["tracol"] = 0;

  if(!$trayle["tracfg"])
   $trayle["tracfg"] = 2;

  if(!$trayle["traest"])
   $trayle["traest"] = 0;

  if(!$this -> retExistTrayle($trayle["trayle"]))
  {
   $query = "INSERT INTO ".BASE_DATOS.".tab_vehige_trayle
   		    			 (num_trayle,cod_marcax,cod_colore,cod_carroc,
   		    			  ano_modelo,nro_ejes,ser_chasis,tra_anchox,
   		    			  tra_altoxx,tra_largox,tra_volpos,tra_pesoxx,
   		    			  tra_capaci,tip_tramit,nom_propie,cod_config,
   		    			  ind_estado,usr_creaci,fec_creaci)
   		   		  VALUES ('".$trayle["trayle"]."','".$trayle["tramar"]."',".$trayle["tracol"].",".$trayle["tracar"].",
   		   		  		  '".$trayle["tramod"]."','".$trayle["traeje"]."','".$trayle["tracha"]."','".$trayle["traanc"]."',
   		   		  		  '".$trayle["traalt"]."','".$trayle["tralar"]."','".$trayle["travol"]."','".$trayle["trapes"]."',
   		   		  		  '".$trayle["tracap"]."','".$trayle["tratra"]."','".$trayle["trapro"]."','".$trayle["tracfg"]."',
   		   		  		  '".$trayle["traest"]."','".$this -> usuari."','".$this -> fecact."')
   		    ";
  }
  else
  {
   $query = "UPDATE ".BASE_DATOS.".tab_vehige_trayle
   		        SET cod_marcax = '".$trayle["tramar"]."',
   		        	cod_colore = ".$trayle["tracol"].",
   		        	cod_carroc = ".$trayle["tracar"].",
   		    		ano_modelo = '".$trayle["tramod"]."',
   		        	nro_ejes = '".$trayle["traeje"]."',
   		        	ser_chasis = '".$trayle["tracha"]."',
   		        	tra_anchox = '".$trayle["traanc"]."',
   		    		tra_altoxx = '".$trayle["traalt"]."',
   		        	tra_largox = '".$trayle["tralar"]."',
   		        	tra_volpos = '".$trayle["travol"]."',
   		        	tra_pesoxx = '".$trayle["trapes"]."',
   		    		tra_capaci = '".$trayle["tracap"]."',
   		        	tip_tramit = '".$trayle["tratra"]."',
   		        	nom_propie = '".$trayle["trapro"]."',
   		        	cod_config = '".$trayle["tracfg"]."',
   		    		ind_estado = '".$trayle["traest"]."',
   		        	usr_creaci = '".$this -> usuari."',
   		        	fec_creaci = '".$this -> fecact."'
   		      WHERE num_trayle = '".$trayle["trayle"]."'
   		    ";
  }

  if($consulta = new Consulta($query, $this -> conexion,"BRC"))
  {
   if($this -> insRelaciTrayle($trayle))
    return TRUE;
   else
    return FALSE;
  }
  else
   return FALSE;
 }

 function envDespacSalida($despac)
 {
   if($despac["client"])
   {
     if(!$this -> retExistTercer($despac["client"]))
       return FALSE;
     else
       $despac["client"] = "'".$despac["client"]."'";
   }
   else
     $despac["client"] = "NULL";

   if($this -> retExistTercer($despac["conduc"]))
   {
     if($this -> retExistVehicu($despac["placax"]))
     {
       $query = "SELECT a.cod_paisxx,a.cod_depart
                   FROM ".BASE_DATOS.".tab_genera_ciudad a
                  WHERE a.cod_ciudad = '".$despac["ciuori"]."' ";

       $consulta = new Consulta($query, $this -> conexion);
       $infpadeo = $consulta  -> ret_matriz();

       if($despac["ciudes"])
       {
         $query = "SELECT a.cod_paisxx,a.cod_depart 
                     FROM ".BASE_DATOS.".tab_genera_ciudad a
                    WHERE a.cod_ciudad = '".$despac["ciudes"]."' ";

         $consulta = new Consulta($query, $this -> conexion);
         $infpaded = $consulta  -> ret_matriz();
       }
       else
       {
         $infpaded[0][0] = 3;
         $infpaded[0][1] = 1;
         $despac["ciudes"] = 1;
       }

       if(!$despac["paiori"] && $infpadeo[0][0])
         $despac["paiori"] = $infpadeo[0][0];
       if(!$despac["depori"] && $infpadeo[0][1])
         $despac["depori"] = $infpadeo[0][1];

       if(!$despac["paides"] && $infpaded[0][0])
         $despac["paides"] = $infpaded[0][0];
       if(!$despac["depdes"] && $infpaded[0][1])
         $despac["depdes"] = $infpaded[0][1];

       if(!$despac["trayle"])
         $despac["trayle"] = "NULL";
       else
         $despac["trayle"] = "'".$despac["trayle"]."'";

       if(!$despac["pesoxx"])
         $despac["pesoxx"] = "NULL";

       if(!$despac["valdec"])
         $despac["valdec"] = 0;

       if(!$despac["obssal"])
         $despac["obssal"] = "NULL";
       else
         $despac["obssal"] = "'".$despac["obssal"]."'";

       /* *
       if(!$despac["client"])
           $despac["client"] = "NULL";
       else
         $despac["client"] = "'".$despac["client"]."'";
       /**/

       if(!$despac["salida"])
         $despac["salida"] = "NULL";
       else
         $despac["salida"] = "'".$despac["salida"]."'";

       $despac["valfle"] = "NULL";
       $despac["valdes"] = "NULL";
       $despac["valant"] = "NULL";
       $despac["valret"] = "NULL";
       $despac["carpag"] = "NULL";
       $despac["despag"] = "NULL";
       $despac["fecpag"] = "NULL";
       $despac["numpol"] = "NULL";

       $query = "SELECT MAX(a.num_despac)
                   FROM ".BASE_DATOS.".tab_despac_despac a ";

       $consulta = new Consulta($query, $this -> conexion);
       $consecut = $consulta  -> ret_matriz();

       $consecut[0][0]++;
       $despac["despac"] = $consecut[0][0];

       $query = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
                  ( num_despac,cod_manifi,fec_despac,cod_tipdes,
                    cod_client,cod_paiori,cod_depori,cod_ciuori,
                    cod_paides,cod_depdes,cod_ciudes,val_flecon,
                    val_despac,val_antici,val_retefu,nom_carpag,
                    nom_despag,cod_agedes,fec_pagoxx,obs_despac,
                    fec_salida,obs_salida,fec_llegad,obs_llegad,
                    ind_planru,ind_anulad,val_declara,num_poliza,
                    usr_creaci,fec_creaci,val_pesoxx )
                 VALUES ( ".$consecut[0][0].",'".$despac["manifi"]."','".$despac["fecdes"]."','".$despac["tipdes"]."',
                          ".$despac["client"].",".$infpadeo[0][0].",".$infpadeo[0][1].",".$despac["ciuori"].",
                          ".$infpaded[0][0].",".$infpaded[0][1].",".$despac["ciudes"].",".$despac["valfle"].",
                          ".$despac["valdes"].",".$despac["valant"].",".$despac["valret"].",".$despac["carpag"].",
                          ".$despac["despag"].",".$despac["agedes"].",".$despac["fecpag"].",'".$despac["observ"]."',
                          ".$despac["salida"].",".$despac["obssal"].",NULL,NULL,
                          'N','R',".$despac["valdec"].",'".$despac["numpol"]."',
                          '".$this -> usuari."','".$this -> fecact."',".$despac["pesoxx"].") ";

       if($consulta = new Consulta($query, $this -> conexion,"BR"))
       {
         if(!$despac["rutasx"])
         {
           $query = "SELECT MIN(a.cod_rutasx)
                       FROM ".BASE_DATOS.".tab_genera_rutasx a,
                            ".BASE_DATOS.".tab_genera_ruttra b
                      WHERE a.cod_rutasx = b.cod_rutasx 
                        AND a.cod_ciuori = '".$despac["ciuori"]."' 
                        AND a.cod_ciudes = '".$despac["ciudes"]."' 
                        AND b.cod_transp = '".$this -> transp."' ";

           $consulta = new Consulta($query, $this -> conexion);
           $existrut = $consulta  -> ret_matriz();

           if(!$existrut)
           {
             return "-9";//NO EXISTEN RUTAS ORIGEN - DESTINO - TRANSPORTADORA
           }
           else
             $despac["rutasx"] = $existrut[0][0];
         }

           $query = "SELECT 1 
                       FROM ".BASE_DATOS.".tab_vehige_trayle 
                      WHERE num_trayle = ".$despac["trayle"]." ";

           $consulta = new Consulta( $query, $this -> conexion, "R" );

           if( "0" == $consulta -> ret_num_rows() )
             $despac["trayle"] = "NULL";

           $query = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                      ( num_despac,cod_transp,cod_agenci,cod_rutasx,
                        cod_conduc,num_placax,num_trayle,obs_proesp,
                        obs_medcom,fec_salipl,fec_llegpl,ind_activo,
                        obs_anulad,usr_creaci,fec_creaci )
                     VALUES ( ".$consecut[0][0].",'".$this -> transp."',".$despac["agenci"].",".$despac["rutasx"].",
                             '".$despac["conduc"]."','".$despac["placax"]."',".$despac["trayle"].",'".$despac["proesp"]."',
                             '".$despac["medcom"]."','".$despac["salipl"]."',NULL,'S',
                             '".$despac["obsanu"]."','".$this -> usuari."','".$this -> fecact."') ";

           if($consulta = new Consulta($query, $this -> conexion,"R"))
           {
             if($despac["planru"])
             {
               if(true)
               {
                 if($this -> envDespacPlanru($despac))
                 {
                   $consulta = new Consulta("COMMIT", $this -> conexion);
                   return $consecut[0][0];
                 }
                 else
                 {
                   return "-3";//ERROR AL CREAR EL PLAN DE RUTA EN EL SISTEMA
                 }
               }
               else
               {
                 return "-4";//EL VEHICULO SE ENCUENTRA EN RUTA EN EL SISTEMA
               }
             }
             else
             {
               $consulta = new Consulta("COMMIT", $this -> conexion);
               return $consecut[0][0];
             }
           }
           else
           {
             return "-5";//ERROR AL INSERTAR EL DESPACHO
           }

       }
       else
       {
         return "-6";//ERROR AL INSERTAR EL DESPACHO
       }
     }
     else
     {
       return "-7";//NO EXISTE EL VEHICULO ASIGNADO AL DESPACHO EN EL SISTEMA
     }
   }
   else
   {
     return "-8";//NO EXISTE EL CONDUCTOR ASIGNADO AL DESPACHO EN EL SISTEMA
   }
  }

 function envDespacDetinat($destin)
 {
  $query = "SELECT a.num_despac
  		      FROM ".BASE_DATOS.".tab_despac_despac a
  		     WHERE a.num_despac = ".$destin["despac"]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existdes = $consulta  -> ret_matriz();

  if($existdes)
  {
   $query = "SELECT a.cod_remdes
   		       FROM ".BASE_DATOS.".tab_genera_remdes a
   		      WHERE a.num_remdes = '".$destin["codest"]."' AND
   		            a.cod_transp = '".$this -> transp."'
   		    ";

   $consulta = new Consulta($query, $this -> conexion,"BR");
   $existtin = $consulta  -> ret_matriz();

   if(!$existtin)
   {
   	$query = "SELECT MAX(a.cod_remdes)
   			    FROM ".BASE_DATOS.".tab_genera_remdes a
   			 ";

    $consulta = new Consulta($query, $this -> conexion);
    $consecut = $consulta  -> ret_matriz();
    $consecut[0][0]++;

    $codigodest = $consecut[0][0];

    $query = "INSERT INTO ".BASE_DATOS.".tab_genera_remdes
    		              (cod_remdes,num_remdes,nom_remdes,obs_adicio,
    		 			   cod_transp,ind_remdes,ind_estado,usr_creaci,
    		 			   fec_creaci)
    		 	   VALUES (".$consecut[0][0].",'".$destin["codest"]."','".$destin["nomdes"]."','".$destin["camdes"]."',
    		 	   		   '".$this -> transp."','2','".COD_ESTADO_ACTIVO."','".$this -> usuari."',
    		 	   		   '".$this -> fecact."')
    		 ";
   }
   else
   {
    $query = "UPDATE ".BASE_DATOS.".tab_genera_remdes
    		     SET nom_remdes = '".$destin["nomdes"]."',
    		         obs_adicio = '".$destin["camdes"]."',
    		         usr_modifi = '".$this -> usuari."',
    		         fec_modifi = '".$this -> fecact."'
    		   WHERE cod_remdes = ".$existtin[0][0]."
    		 ";

    $codigodest = $existtin[0][0];
   }

   $consulta = new Consulta($query, $this -> conexion,"R");

   $query = "SELECT a.cod_paisxx,a.cod_depart
   		       FROM ".BASE_DATOS.".tab_genera_ciudad a
   		      WHERE a.cod_ciudad = ".$destin["cdesti"]."
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $paisdepa = $consulta  -> ret_matriz();

   if($paisdepa)
   {
   	if(!$destin["nrodes"])
   	 $destin["nrodes"] = "NULL";
   	else
   	 $destin["nrodes"] = "'".$destin["nrodes"]."'";

   	if(!$destin["pesdes"])
   	 $destin["pesdes"] = "NULL";
   	else
   	 $destin["pesdes"] = "'".$destin["pesdes"]."'";

   	if(!$destin["dirdes"])
   	 $destin["dirdes"] = "NULL";
   	else
   	 $destin["dirdes"] = "'".$destin["dirdes"]."'";

   	if(!$destin["camdes"])
   	 $destin["camdes"] = "NULL";
   	else
   	 $destin["camdes"] = "'".$destin["camdes"]."'";

    $query = "INSERT INTO ".BASE_DATOS.".tab_despac_remdes
   		                  (num_despac,cod_remdes,num_docume,val_pesoxx,
   		                   cod_paisxx,cod_depart,cod_ciudad,dir_destin,
   		                   obs_adicio)
   		           VALUES (".$destin["despac"].",".$codigodest.",".$destin["nrodes"].",".$destin["pesdes"].",
   		          		   ".$paisdepa[0][0].",".$paisdepa[0][1].",".$destin["cdesti"].",".$destin["dirdes"].",
   		          		   ".$destin["camdes"].")
   		    ";

    if($consulta = new Consulta($query, $this -> conexion,"R"))
    {
     $consulta = new Consulta("COMMIT", $this -> conexion);
     return "";
    }
    else
     return "-2";
   }
   else
    return "-4";//La ciudad no se encuentra registrada
  }
  else
   return "-3";//El Despacho no Existe en el Sistema
 }

 function envDespacLlegad($despac)
 {
  $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
  			   SET fec_llegad = '".$despac["fechax"]."',
  			       obs_llegad = '".$despac["observ"]."',
  			       usr_modifi = '".$this -> usuari."',
  			       fec_modifi = '".$this -> fecact."'
  			 WHERE num_despac = ".$despac["despac"]."
  		   ";

  if($consulta = new Consulta($query, $this -> conexion,"BRC"))
   return "";
  else
   return FALSE;
 }

 function envDespacNovePC($despac)
 {
  $query = "SELECT MAX(e.fec_noveda)
              FROM ".BASE_DATOS.".tab_despac_vehige c,
            	   ".BASE_DATOS.".tab_despac_seguim d,
                   ".BASE_DATOS.".tab_despac_noveda e
             WHERE c.num_despac = d.num_despac AND
                   c.num_despac = e.num_despac AND
                   c.num_despac = ".$despac["despac"]."
           ";

  $consulta = new Consulta($query, $this -> conexion);
  $ultrep = $consulta -> ret_matriz();

  $regist["despac"] = $despac["despac"];
  $regist["contro"] = $despac["contro"];
  $regist["noveda"] = $despac["noveda"];
  $regist["tieadi"] = $despac["duraci"];
  $regist["observ"] = htmlentities($despac["observ"]);
  $regist["fecnov"] = $despac["fechax"];
  $regist["fecact"] = $this -> fecact;
  $regist["ultrep"] = $ultrep[0][0];
  $regist["usuari"] = $this -> usuari;

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos(0,0,1,$this -> conexion);
  $RESPON = $transac_nov -> InsertarNovedadPC(BASE_DATOS,$regist,2);

  if($RESPON[0]["indica"])
  {
   $consulta = new Consulta ("COMMIT", $this -> conexion);

   $mensaje = $RESPON[0]["mensaj"];
   for($i = 1; $i < sizeof($RESPON); $i++)
   {
    if($RESPON[$i]["indica"])
     ;
    else
     $mensaje .= $RESPON[$i]["mensaj"]."|";
   }

   return "";
  }
  else
  {
   $mensaje = $RESPON[0]["mensaj"];

   return $mensaje;
  }
 }

 function envDespacNoveNC($despac)
 {
  $regist["despac"] = $despac["despac"];
  $regist["contro"] = $despac["contro"];
  $regist["noveda"] = $despac["noveda"];
  $regist["tieadi"] = $despac["duraci"];
  $regist["observ"] = htmlentities($despac["observ"]);
  $regist["fecact"] = $this -> fecact;
  $regist["usuari"] = $this -> usuari;
  $regist["nittra"] = $despac["transp"];

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  $transac_nov = new Despachos(0,0,1,$this -> conexion);
  $RESPON = $transac_nov -> InsertarNovedadNC(BASE_DATOS,$regist,2);

  if($RESPON[0]["indica"])
  {
   $consulta = new Consulta ("COMMIT", $this -> conexion);

   $mensaje = $RESPON[0]["mensaj"];
   for($i = 1; $i < sizeof($RESPON); $i++)
   {
    if($RESPON[$i]["indica"])
     ;
    else
     $mensaje .= $RESPON[$i]["mensaj"]."|";
   }

   return "";
  }
  else
  {
   $mensaje = $RESPON[0]["mensaj"];
   /*$mens = new mensajes();
   $mens -> advert("REGISTRO DE NOVEDADES",$mensaje);*/

   return FALSE;
  }
 }

 function envDespacAnular($despac)
 {
  $query = "SELECT a.cod_transp,a.num_placax
              FROM ".BASE_DATOS.".tab_despac_vehige a
             WHERE a.num_despac = ".$despac["despac"]."
           ";

  $consulta = new Consulta($query, $this -> conexion);
  $placax_d = $consulta -> ret_matriz();

  if($despac["tipoan"] == "1" || $despac["tipoan"] == "2")
  {
   if($despac["tipoan"] == "1")
    $detalle = "Anulaci&oacute;n Directa Desde la Aplicaci&oacute;n del Cliente.";
   else
    $detalle = "Se Anula Directamente, Se Reverso la Salida en la Aplicaci&oacute;n del Cliente.";

   	$query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                SET ind_activo = 'N',
                    obs_anulad = '".$detalle."\n',
		    		usr_modifi = '".$this -> usuari."',
		    		fec_modifi = '".$this -> fecact."'
              WHERE num_despac = ".$despac["despac"]."
            ";

   if($consulta = new Consulta($query, $this -> conexion, "BRC"))
   {
   	$query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET ind_anulad = 'A',
		    		 usr_modifi = '".$this -> usuari."',
		    		 fec_modifi = '".$this -> fecact."'
               WHERE num_despac = ".$despac["despac"]."
            ";

    if($consulta = new Consulta($query, $this -> conexion, "BRC"))
     return "";
    else
     return "ERROR AL GENERAR EL REGISTRO DE LA REVERSION";
   }
   else
    return "ERROR AL GENERAR EL REGISTRO DE LA REVERSION";
  }
  else if($despac["tipoan"] == "3")
  {
   $query = "SELECT a.num_placax
               FROM ".BASE_DATOS.".tab_despac_vehige a
              WHERE a.num_despac = ".$despac["despac"]."
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $placax = $consulta -> ret_matriz();

   $query = "SELECT a.num_despac
               FROM ".BASE_DATOS.".tab_despac_vehige a,
                    ".BASE_DATOS.".tab_despac_despac b
              WHERE a.num_placax = '".$placax[0][0]."' AND
                    a.num_despac = b.num_despac AND
                    a.ind_activo = 'S' AND
                    b.fec_salida IS NOT NULL AND
                    b.fec_llegad IS NULL AND
                    b.num_despac != ".$despac["despac"]."
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $enruta = $consulta -> ret_matriz();

   if(!$enruta)
   {
    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET fec_llegad = NULL,
		     usr_modifi = '".$this -> usuari."',
		     fec_modifi = '".$this -> fecact."'
               WHERE num_despac = '".$despac["despac"]."'
             ";

    if($consulta = new Consulta($query, $this -> conexion, "BRC"))
     return "";
    else
     return "ERROR AL GENERAR EL REGISTRO DE LA REVERSION";
   }
   else
    return "EL VEHICULO SE ENCUENTRA EN RUTA";
  }
  else
   return "EL TIPO DE ANULACION NO EXISTE";
 }

 function retRutasxContro($rutasx)
 {
  $query = "SELECT a.cod_contro,b.nom_contro,a.val_duraci
	      	  FROM ".BASE_DATOS.".tab_genera_rutcon a,
		   	       ".BASE_DATOS.".tab_genera_contro b
	     	 WHERE a.cod_rutasx = ".$rutasx["rutasx"]." AND
		   		   a.cod_contro = b.cod_contro
		   		   ORDER BY 3
           ";

  $consulta = new Consulta($query, $this -> conexion);
  $planruta = $consulta -> ret_matriz();

  if($planruta)
  {
   for($i = 0; $i < sizeof($planruta); $i++)
   {
    $resultado[$i]["contro"] = $planruta[$i][0];
    $resultado[$i]["nombre"] = $planruta[$i][1];
    $resultado[$i]["tiempo"] = $planruta[$i][2];
   }

   return $resultado;
  }
  else
   return $resultado[0]["contro"] = "NO EXISTE RELACION DE PUESTOS DE CONTROL PARA LA RUTA";
 }

 function envDespacPlanru($planru)
 {

  /*$query = "DELETE FROM ".BASE_DATOS.".tab_despac_seguim
             WHERE num_despac = ".$planru["despac"]."
           ";

  $insercion = new Consulta($query, $this -> conexion,"R");*/



  $pcontro = explode("|",$planru["planru"]);
  $pccont = explode("|",$planru["precon"]);
  $pcnove = explode("|",$planru["prenov"]);
  $pctime = explode("|",$planru["pretie"]);

  $k = 0;

  for($i = 0; $i < sizeof($pcontro); $i++)
  {
   for($j = 0; $j < sizeof($pccont); $j++)
   {
    if($pcontro[$i] == $pccont[$j])
    {
     $prenovedades[$k][0] = $pccont[$j];
     $prenovedades[$k][1] = $pcnove[$j];
     $prenovedades[$k][2] = $pctime[$j];
     $k++;
    }
   }
  }


  $tieacu = 0;

  for($i = 0; $i < sizeof($pcontro); $i++)
  {
   $query = "SELECT a.val_duraci
               FROM ".BASE_DATOS.".tab_genera_rutcon a,
		    ".BASE_DATOS.".tab_despac_vehige b
              WHERE a.cod_rutasx = b.cod_rutasx AND
		    b.num_despac = ".$planru["despac"]." AND
                    a.cod_contro = ".$pcontro[$i]."
            ";

   $consulta = new Consulta($query, $this -> conexion,"R");
   $tieori = $consulta -> ret_matriz();

   $ultimo = $tieacu + $tieori[0][0];

   $tiepla = $tieacu + $tieori[0][0];

   $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
                         (num_despac,cod_rutasx,cod_contro,fec_planea,
                          fec_alarma,usr_creaci,fec_creaci,usr_modifi,
                          fec_modifi)
                  VALUES (".$planru["despac"].",".$planru["rutasx"].",".$pcontro[$i].",
                          DATE_ADD(".$planru["salida"].", INTERVAL ".$tiepla." MINUTE),
                          DATE_ADD(".$planru["salida"].", INTERVAL ".$tiepla." MINUTE),
                          '".$this -> usuari."','".$this -> fecact."','".$this -> usuari."','".$this -> fecact."')";

   $consulta = new Consulta($query, $this -> conexion,"R");


   if($prenovedades[$i][0] && $pcontro[$i] != CONS_CODIGO_PCLLEG)
   {
    $query = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
                          (num_despac,cod_rutasx,cod_contro,cod_noveda,
                           val_pernoc,usr_creaci,fec_creaci,usr_modifi,
                           fec_modifi)
                   VALUES (".$planru["despac"].",".$planru["rutasx"].",".$pcontro[$i].",
                           ".$prenovedades[$i][1].",".$prenovedades[$i][2].",'".$this -> usuari."',
                           '".$this -> fecact."','".$this -> usuari."','".$this -> fecact."')
            ";

    $insercion = new Consulta($query, $this -> conexion,"R");

   }

   if($prenovedades[$i][0])
    $tieacu += $prenovedades[$i][2];
  }

  $query = "SELECT DATE_ADD(".$planru["salida"].", INTERVAL ".$ultimo." MINUTE)";

  $consulta = new Consulta($query, $this -> conexion);
  $timlle = $consulta -> ret_matriz();

  $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
               SET fec_salipl = ".$planru["salida"].",
                   ind_activo = 'S',
                   fec_llegpl = '".$timlle[0][0]."',
                   usr_modifi = '".$this -> usuari."',
                   fec_modifi = '".$this -> fecact."'
             WHERE num_despac = '".$planru["despac"]."'";

  $insercion = new Consulta($query, $this -> conexion,"R");


  $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
               SET fec_salida = ".$planru["salida"].",
                   ind_planru = 'S',
                   usr_modifi = '".$this -> usuari."',
                   fec_modifi = '".$this -> fecact."'
             WHERE num_despac = '".$planru["despac"]."'
           ";

  if($insercion = new Consulta($query, $this -> conexion,"R"))
   return TRUE;
  else
   return FALSE;
 }

 function retPC()
 {
  $query = "SELECT a.cod_contro,a.nom_contro
	      FROM ".BASE_DATOS.".tab_genera_contro a
		   ORDER BY 2
	   ";
  //WHERE a.cod_contro != ".CONS_CODIGO_PCLLEG."

  $consulta = new Consulta($query, $this -> conexion);
  $contro = $consulta -> ret_matriz();

  if($contro)
  {
   for($i = 0; $i < sizeof($contro); $i++)
   {
    $resultado[$i]["contro"] = $contro[$i][0];
    $resultado[$i]["nombre"] = $contro[$i][1];
   }

   return $resultado;
  }
  else
   return FALSE;
 }

 function retRutasx($ciuori,$ciudes)
 {
  $query = "SELECT a.cod_rutasx,a.nom_rutasx
	      FROM ".BASE_DATOS.".tab_genera_rutasx a
	     WHERE a.cod_ciuori = ".$ciuori." AND
		   a.cod_ciudes = ".$ciudes."
		   ORDER BY 2
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $rutasx = $consulta -> ret_matriz();

  if($rutasx)
  {
   for($i = 0; $i < sizeof($rutasx); $i++)
   {
    $resultado[$i]["rutasx"] = $rutasx[$i][0];
    $resultado[$i]["nombre"] = $rutasx[$i][1];
   }

   return $resultado;
  }
  else
   return FALSE;
 }

 function retNoveda()
 {
  $query = "SELECT a.cod_noveda,a.nom_noveda,a.ind_tiempo,a.ind_alarma
	      FROM ".BASE_DATOS.".tab_genera_noveda a
		   ORDER BY 2
	   ";

//WHERE a.cod_noveda != ".CONS_NOVEDA_PCLLEG."

  $consulta = new Consulta($query, $this -> conexion);
  $noveda = $consulta -> ret_matriz();

  if($noveda)
  {
   for($i = 0; $i < sizeof($noveda); $i++)
   {
    $resultado[$i]["noveda"] = $noveda[$i][0];
    $resultado[$i]["nombre"] = $noveda[$i][1];
    $resultado[$i]["tiempo"] = $noveda[$i][2];
    $resultado[$i]["alarma"] = $noveda[$i][3];
   }

   return $resultado;
  }
  else
   return FALSE;
 }

 function getInfoTT()
 {
  $resultado = array();

  $query = "SELECT a.cod_tercer,a.abr_tercer,c.nom_ciudad,a.num_telef1,
  		 		   a.dir_emailx
			  FROM ".BASE_DATOS.".tab_tercer_tercer a,
			  	   ".BASE_DATOS.".tab_tercer_emptra b,
			  	   ".BASE_DATOS.".tab_genera_ciudad c
  		     WHERE a.cod_tercer = b.cod_tercer AND
			  	   a.cod_ciudad = c.cod_ciudad
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $infotran = $consulta -> ret_matriz();

  $resultado[0]["respon"] = TRUE;
  $resultado[0]["menres"] = NULL;

  for($i = 1; $i <= sizeof($infotran); $i++)
  {
   $resultado[$i]["transp"] = $infotran[$i-1][0];
   $resultado[$i]["nombre"] = $infotran[$i-1][1];
   $resultado[$i]["ciudad"] = $infotran[$i-1][2];
   $resultado[$i]["telefo"] = $infotran[$i-1][3];
   $resultado[$i]["emailx"] = $infotran[$i-1][4];
   $resultado[$i]["contac"] = $infotran[$i-1][1];
  }

  return $resultado;
 }

 function setEstadoInterf($schema)
 {
  $resultado = array();

  $query = "SELECT a.ind_operad,a.ind_estado
  		      FROM ".BASE_DATOS.".tab_interf_parame a
  		     WHERE a.cod_operad = '".$schema["operad"]."' AND
  		           a.cod_transp = '".$schema["transp"]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $existreg = $consulta -> ret_matriz();

  if($existreg)
  {
   if($schema["indest"])
    $query = "UPDATE ".BASE_DATOS.".tab_interf_parame
   		        SET nom_operad = '".$schema["nomope"]."',
   		        	nom_usuari = ".$schema["usracc"].",
   		        	clv_usuari = ".$schema["pasacc"].",
   		        	val_timtra = ".$schema["timtra"].",
   		        	ind_operad = '".$schema["indope"]."',
   		        	ind_estado = '".$schema["indest"]."',
   		        	ind_intind = '".$schema["intind"]."'
   		      WHERE cod_operad = '".$schema["operad"]."' AND
   		      		cod_transp = '".$schema["transp"]."'
   		    ";
   else
    $query = "UPDATE ".BASE_DATOS.".tab_interf_parame
   		        SET ind_operad = '".$schema["indope"]."',
   		        	ind_estado = '".$schema["indest"]."'
   		      WHERE cod_operad = '".$schema["operad"]."' AND
   		      		cod_transp = '".$schema["transp"]."'
   		    ";
  }
  else
   $query = "INSERT INTO ".BASE_DATOS.".tab_interf_parame
    					 (cod_operad,cod_transp,nom_operad,nom_usuari,
    					  clv_usuari,val_timtra,ind_operad,ind_estado,
    					  ind_intind)
    			  VALUES ('".$schema["operad"]."','".$schema["transp"]."','".$schema["nomope"]."',".$schema["usracc"].",
    			   		  ".$schema["pasacc"].",".$schema["timtra"].",'".$schema["indope"]."','".$schema["indest"]."',
    			   		  '".$schema["intind"]."')
    		";

  $consulta = new Consulta($query, $this -> conexion,"BRC");

  $resultado[0]["respon"] = TRUE;
  $resultado[0]["menres"] = NULL;

  if($schema["indest"])
   $resultado[1]["confir"] = "Se Activo La Interfaz Exitosamente";
  else
   $resultado[1]["confir"] = "Se Desactivo La Interfaz Exitosamente";

  return $resultado;
 }
}

?>

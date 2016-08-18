<?php
define("URL_DIREC_APLICA", "190.143.80.47/ap");

//define("COD_PERFIL_SUPERUSR", "666");// constante para perfil administrador del sistema
define("COD_PERFIL_SUPERUSR", "73");// constante para perfil administrador del sistema
define("COD_PERFIL_ADMINIST", "1");// constante para perfil administrador de empresa
define("COD_PERFIL_SUPEFARO", "8");// supervisor faro

define("COD_FILTRO_EMPTRA", "1");// constante para filtro de Transportadora
define("COD_FILTRO_AGENCI", "2");// constante para filtro de Agencias de la transportadora
define("COD_FILTRO_CLIENT", "3");// constante para filtro de Generadores de Carga
define("COD_FILTRO_CONDUC", "4");//constante para filtro de conductores
define("COD_FILTRO_PROPIE", "5");//constante para filtro de propietarios
define("COD_FILTRO_POSEED", "6");//constante para filtro de poseedores
define("COD_FILTRO_CONTRO", "7");//constante para filtro de puestos de control

define("COD_ESTADO_PENDIE", "0");//constante para el estado Pendiente
define("COD_ESTADO_ACTIVO", "1");//constante para el estado Activo
define("COD_ESTADO_INACTI", "2");//constante para el estado Inactivo

define("COD_APLICACION", "1");// constante para el codigo de la aplicacion

define("ESTILO", "Verde");

define("COD_SERVIC_MESSENGER", "735");// constante para el servicio de mensajes entre usuarios

//MODULO DE TIEMPOS CARGUE Y DESCARGUE
define("E_PENTIE", "Remesas Pendientes De Tiempo Cargue");// CONSTANTE ENCABEZADO
define("E_PENDES", "Remesas Pendientes De Tiempo Descargue");// CONSTANTE ENCABEZADO
define("C_DISPON", "Clientes Disponibles");// CONSTANTE ENCABEZADO CLIENTES

define("URL_ARCHIV_STANDA", "/var/www/html4/ap/"); //constante para del directorio general de las aplica.
define("URL_CONDUC", "fotcon/");// constante para foto del conductor
define("URL_VEHICU", "fotveh/");// constante para foto del conductor
define("URL_PCTCON", "contro/");// constante para foto del P/C
define("URL_REMOLQ", "remolq/");// constante para foto del Remolque

define("COD_SERVIC_INICIO", "../".DIR_APLICA_CENTRAL."/despac/ins_despac_noveda.php");// codigo servicio que se carga al inicio
define("CODIGO_INS_NOVEDA", "3302");// constante para el codigo de insercion de novedades en PC

define("FDESPAC_RUTA", "Despachos en Ruta");// CONSTANTE TITULO DE FORMULARIO
define("ESAD", "S.A.T. TRAFICO");// CONSTANTE ENCABEZADO SAD
define("D_DESPACHO", "Detalle Despacho");// CONSTANTE CONTROL TRAFICO->DETALLE DESPACHOS

define("CONS_CODIGO_PCLLEG", "9999");// constante para el codigo del puesto de control de llegada.
define("CONS_NOVEDA_PCLLEG", "1");// constante para el codigo de Novedad, para el Tiempo Llegad P/C.
define("CONS_NOVEDA_CAMALA", "9999");// constante para el codigo de Novedad, Cambio de Alarma parametro de Responsabilidad Transportadora
define("CONS_NOVEDA_CAMRUT", "9998");// constante para el codigo de Novedad Especial Cambio de Ruta
define("CONS_NOVEDA_ACAEMP", "9996");// constante para el codigo de Novedad Especial A cargo Empresa
define("CONS_NOVEDA_ACAFAR", "9995");// constante para el codigo de Novedad Especial A cargo Faro
define("CONS_PERFIL", "'79'");// constante para los perfiles que no deben salir en listado de turnos


//CONSTANTE URL WEB SERVICES INTERFAZ APLICACIONES SAT
define("URL_INTERF_WEBSER", "https://flired.intrared.net/ap/SAT/ejec/Interfaz_ws_sat.php");

define("CENTRAL", "satt_standa");// constante para la base de datos de servicios centrales

define("DIAS_VALID_NUEVOS", "30");// constante para validar el tiempo de creación de registros.

define("CAN_RESULT_PAGINA","1000");// Cantidad de registros por paginador
?>

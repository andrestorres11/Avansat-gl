<?php
//define("URL_DIREC_APLICA", "190.143.80.47/ap");
define("URL_DIREC_APLICA", "avansatgl.intrared.net/ap");	//ORIGINAL 
define("DBSERVER", "aglbd.intrared.net:3306"); // nueva constante para el servidor de bd

//define("COD_PERFIL_SUPERUSR", "666");// constante para perfil administrador del sistema
define("COD_PERFIL_SUPERUSR", "73");// constante para perfil administrador del sistema
define("COD_PERFIL_ADMINIST", "1");// constante para perfil administrador de empresa
define("COD_PERFIL_SUPEFARO", "8");// supervisor faro
define("COD_PERFIL_SUPEGENE", "84");// supervisor General
define("COD_PERFIL_AUDITORX", "97");// Auditor CLF
define("COD_PERFIL_DIRECTOR", "74");// Director CLF
define("COD_PERFIL_SUPEREAL", "77");// Director CLF

define("COD_FILTRO_EMPTRA", "1");// constante para filtro de Transportadora
define("COD_FILTRO_AGENCI", "2");// constante para filtro de Agencias de la transportadora
define("COD_FILTRO_CLIENT", "3");// constante para filtro de Generadores de Carga
define("COD_FILTRO_CONDUC", "4");//constante para filtro de conductores
define("COD_FILTRO_PROPIE", "5");//constante para filtro de propietarios
define("COD_FILTRO_POSEED", "6");//constante para filtro de poseedores
define("COD_FILTRO_CONTRO", "7");//constante para filtro de puestos de control
define("COD_FILTRO_ASEGUR", "8");//constante para filtro de asegurador
define("COD_FILTRO_PROVEE", "14");//constante para filtro de proveedor

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

define("URL_ARCHIV_STANDA", "/var/www/html/ap/"); //constante para del directorio general de las aplica.
define("URL_CONDUC", "fotcon/");// constante para foto del conductor
define("URL_VEHICU", "fotveh/");// constante para foto del conductor
define("URL_PCTCON", "contro/");// constante para foto del P/C
define("URL_REMOLQ", "remolq/");// constante para foto del Remolque
define("URL_SOLICI", "files/solici/");// constante la carpeta de solicitudes a faro (solifa)

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
define("CONS_NOVEDA_TRNAUT", "9994");// constante para el codigo de Novedad Transito No autorizado
define("CONS_NOVEDA_CONDES", "9993");// constante para el codigo de Novedad Confirmacion descargue
define("CONS_NOVEDA_GPSXXX", "4999");// constante para el codigo de Novedad, GPS
define("CONS_NOVEDA_CAMCEC", "9991");// constante para el codigo de Novedad, GPS
define("CONS_PERFIL", "'79'");// constante para los perfiles que no deben salir en listado de turnos
define("CONS_NOVEDA_CAMCEL", "50");// constante para el codigo de Novedad Cambio de Celular

//CONSTANTE URL WEB SERVICES INTERFAZ APLICACIONES SAT
define("URL_INTERF_WEBSER", "https://flired.intrared.net/ap/SAT/ejec/Interfaz_ws_sat.php");

define("CENTRAL", "satt_standa");// constante para la base de datos de servicios centrales

define("DIAS_VALID_NUEVOS", "30");// constante para validar el tiempo de creaci�n de registros.

define("CAN_RESULT_PAGINA","1000");// Cantidad de registros por paginador
/*
define("URL_INTERF_SATAPX", "https://localhost:444/ap/interf/app/sat/wsdl/sat.wsdl");
define("URL_INTERF_SATSER", "https://190.143.80.46:444/ap/interf/app/sat/wsdl/sat.wsdl");
define("URL_INTERF_SATFLI", "https://flired.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl");
define("URL_INTERF_SATCLO", "https://cloud.intrared.net:8083/ap/interf/app/sat/wsdl/sat.wsdl");
*/

define("URL_INTERF_SATAPX", "https://dev.intrared.net/ap/interf/app/sat/wsdl/sat.wsdl");
define("URL_INTERF_SATSER", "https://dev.intrared.net/ap/interf/app/sat/wsdl/sat.wsdl");
define("URL_INTERF_SATFLI", "https://dev.intrared.net/ap/interf/app/sat/wsdl/sat.wsdl");
define("URL_INTERF_SATCLO", "https://dev.intrared.net/ap/interf/app/sat/wsdl/sat.wsdl");


define("URL_INTERF_GPSXXX", "https://avansatgl.intrared.net/ap/interf/app/gps/wsdl/gps.wsdl");



define("MAIL_SUPERVISORES", "faroavansat@eltransporte.com");//Correo de supervisores
define("MAIL_SOPORTE", "soporte.ingenieros@intrared.net");//Correo de soporte intrared

//---------------------------------------------------------------------------------------------------
//	CONSTANTES PARA PROCESOS DE ESCOLTA ELECTR�NICO CON PROTEKTO
//---------------------------------------------------------------------------------------------------
//Codigo Interfaz Protekto
define("COD_INTERF_PROTEK", "17");

//Constante URL Interfaz Protekto
define("URL_INTERF_PROTEK", "http://www.protekto.net/SerWeb/SWOET.asmx?WSDL");
//---------------------------------------------------------------------------------------------------


// CONSTANTES INTERFAZ RIT
//define("URL_INTERF_RITXXX", "https://web9.intrared.net/ap/interf/app/rit/wsdl/rit.wsdl");
define("URL_INTERF_RITXXX", "https://oet.intrared.net/ap/interf/app/rit/wsdl/rit.wsdl");
define("URL_USERXX_RITXXX", "InterfCalifi");
define("URL_PASSXX_RITXXX", "1nt3rF@RiT!*2014");
//---------------------------------------------------------------------------------------------------


# constante de la ruta para cargar los javascritps
define("DirJsx", "../".DIR_APLICA_CENTRAL."/js");
define("DirCss", "../".DIR_APLICA_CENTRAL."/estilos");
define("DirCssJq", "../".DIR_APLICA_CENTRAL."/estilos");

#configuracion para SPG con Panel de Control
//define("URL_INTERF_PCONTROL","https://dev.intrared.net:8084/ap/dev/panel_control_rc1/web/app.php/spg/validate");
define( 'URL_INTERF_PCONTROL' , 'https://central.intrared.net/ap/dev/panel_control_rc1/web/app.php/spg/validate');
define( 'URL_INTERF_SPG' , 'https://web6.intrared.net:8083/ap/interf/app/spg/wsdl/spg2.wsdl' );
//define( 'URL_INTERF_SPG_PANEL' , 'https://dev.intrared.net:8083/ap/interf/app/spg_new/spg.php?wsdl' );
define( 'URL_INTERF_SPG_PANEL' , 'https://interno.intrared.net:8083/ap/interf/app/spg_new/spg.php?wsdl' );
define( 'USR_LOGSPG' , 'usr_seguim_empres' );
define( 'PWD_LOGSPG' , '7f403c30fc28639297d4d7d63d3397ec' );
define( 'URL_INTERF_SPG_MODULES' ,'https://avansatgl.intrared.net/ap/interf/app/ws_panel/ws_panel.php?wsdl');

//Constante Interfaz con FARO
#define("URL_INTERF_FAROXX", "https://web10.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl");
#define("URL_INTERF_FAROXX", "https://54.215.8.158/ap/interf/app/faro/wsdl/faro.wsdl");
define("URL_INTERF_FAROXX", "https://dev.intrared.net:8083/ap/interf/app/faro/wsdl/faro.wsdl");
define("USR_INTERF_FAROXX", "SOPORTE");
define("PWD_INTERF_FAROXX", "ifx2016");


//Constante Interfaz para eventos GPS, debe apuntar al servidor central
define("URL_INTERF_GPSEVE", "https://central.intrared.net/ap/interf/APIIntegradorGPS/v1/index.php");

//Constante Solicitudes Asistencia en Carretera
define("CON_SOLICI_ASISTE", 1);
define("CON_SOLICI_ACOMPA", 2);

//Constante Tipo Servicio, llamado particularidad seguimiento control de trafico.
define("CON_TIPSER_MA", 2);
define("CON_TIPSER_OAL", 1);

?>

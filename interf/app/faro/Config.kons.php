<?php

  define( "DirLib", "/var/www/html/ap/interf/lib/" );                                 //Librerias generales.
  define( "AplKon", "/var/www/html/ap/satt_intgps/constantes.inc" ); //Constantes de la aplicacion cliente.
  //define( "NotMai", "nelson.liberato@intrared.net,soporte.ingenieros@intrared.net" );    //Mails de notificacion de errores.
  define( "NotMai", "logs.tms@intrared.net" );    //Mails de notificacion de errores.
  define( "DirSAT", "/var/www/html4/ap/" );                                           //Directorio donde se encuentran las aplicaciones SAT.
  define( "WsdSAT", "https://54.215.8.158/ap/interf/app/sat/wsdl/sat_local.wsdl" );  //Direccion del wsdl del SAT.
  define( "WsdFAR", "https://54.215.8.158/ap/interf/app/faro/wsdl/faro.wsdl" );      //Wsdl de FARO
  define( "CamCel", "9991" );                                                         //Novedad de Cambio de Celular en SAT Trafico
  define("RootDir", "/var/www/html/ap/"); //Nueva constante directorio principale de aplicaciones gl
  
  //define( "DatFar", "3143949474 - 7429002 Ext 123 - 2356268 Ext 25" );                    //Datos de FARO
  //define( "DatFar", "Vía WhatsApp al 3143949474  - Vía voz a voz 3336025102 OPCION 1" );                    //Datos de FARO
  define( "DatFar", "Vía WhatsApp al 3160271544  - Vía voz a voz 3336025102 opc 1 - 3336025155" );            //Datos de FARO  ID 395554
  define("URL_INTERF_SATAPX", "https://54.215.8.158/ap/interf/app/sat/wsdl/sat.wsdl");
  define("URL_INTERF_SATSER", "https://190.143.80.46:444/ap/interf/app/sat/wsdl/sat.wsdl");
  define("URL_INTERF_SATFLI", "https://flired.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl");




  define( "ValDir", "/var/www/html/ap/interf/app/faro/validator/" );   //Librerias generales.
  define( "LogDir", "/var/www/html/ap/interf/app/faro/logs/" );        //Constantes de la aplicacion cliente.
  define( "DictDir", "/var/www/html/ap/interf/app/faro/dictionary/" ); //Directorio Diccionario de terminos.
  define( "WsDirx", "/var/www/html/ap/interf/app/faro/wsdl/" );

  //define( "FarMai", "faroavansat@eltransporte.com,hugo.malagon@intrared.net" ); //Mail de notificacion de creacion de rutas
  // define( "FarMai", "rutas@eltransporte.org" ); //Mail de notificacion de creacion de rutas
  define( "FarMai", "supervisores@faro.com.co" ); //Mail de notificacion de creacion de rutas

  define( "UrlApl", "https://server.intrared.net/ap/" );               //Url Aplicaciones
  define( "NovAcarFar", "9995" );                                      //Novedad a cargo FARO
  //define( "FarMai", "hugo.malagon@intrared.net" );           //Mail de notificacion de creacion de rutas

  define( "Hostxx", "oet-avansatglbd.intrared.net" );                                                   //Servidor de base de datos ambiente 5.
  
  define( "DirLogoTranspo", "/var/www/html/ap/satt_intgps/logos/" );                                           //Dirección para el logo de la transportadora.
  define( "DirImagTrafico", "/var/www/html/ap/satt_intgps/imagenes/" );                                           //Dirección para el logo de la transportadora.
  define( "DirStanTrafico", "/var/www/html/ap/satt_standa/imagenes/" );                                           //Dirección para el logo de la transportadora.
  define( "DirPhotoConduc", "/var/www/html/ap/satt_intgps/fotcon/" );                                           //Dirección para la foto de conductor.
  define( "DirPhotoVehicu", "/var/www/html/ap/satt_intgps/fotveh/" );                                           //Dirección para la foto del vehiculo.
  define( "DirSolici", "/var/www/html/ap/satt_intgps/files/solici/" );                                           //Dirección para la cargar archivos de solicitudes
 
 //referencia generado con MD5(uniqueid(<some_string>, true))
// Debería consultar la Private Key desde BD no como constante
define('API_USER','*WidetechInt3grador*');
define('API_KEY','lxdG-+gJX:oYju+b5n');
define('API_KEY_CRYPT','e14804819d57fc7497bb747204ce337b');

// Llave para encriptar y desencriptar los datos, usado en mcrypt
define('SALT_CRYPT','*IxAl}M;^]uYLd8Ic');

// URL API WIDETECH INTEGRADOR
define("URL_INTERF_GPSEVE","http://web1ws.shareservice.co");
  
?>
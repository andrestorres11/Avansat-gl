<?php

  define( "DirLib", "/var/www/html/ap/interf/lib/" );                                 //Librerias generales.
  define( "AplKon", "/var/www/html/ap/satt_corona/constantes.inc" ); //Constantes de la aplicacion cliente.
  define( "NotMai", "soporte.ingenieros@intrared.net" );    //Mails de notificacion de errores.
  define( "DirSAT", "/var/www/html/ap/" );                                           //Directorio donde se encuentran las aplicaciones SAT.
  //define( "WsdSAT", "https://localhost:444/ap/interf/app/sat/wsdl/sat_local.wsdl" );  //Direccion del wsdl del SAT.
  #define( "WsdFAR", "https://web10.intrared.net/ap/interf/app/corona/wsdl/corona.wsdl" );      //Wsdl de FARO
  //define( "WsdFAR", "https://web7.intrared.net:8083/ap/interf/app/corona/wsdl/corona.wsdl" );      //Wsdl de FARO
  define( "CamCel", "9991" );                                                         //Novedad de Cambio de Celular en SAT Trafico
  define( "DatFar", "3143949474 - 7429002 Ext 123 - 2356268 Ext 25" );                    //Datos de FARO
  define("URL_INTERF_SATAPX", "https://localhost:444/ap/interf/app/sat/wsdl/sat.wsdl");
  define("URL_INTERF_SATSER", "https://190.143.80.46:444/ap/interf/app/sat/wsdl/sat.wsdl");
  define("URL_INTERF_SATFLI", "https://flired.intrared.net:444/ap/interf/app/sat/wsdl/sat.wsdl");

  // URL de acceso al WebService
  define("ROOT_FOLDER", "/ap/interf/app/corona/");                              // Directorio donde se encuentra alojado el WebService
  define("WSDL_FILE", "corona.php?wsdl");                                       // Archivo PHP que contiene la definici�n del WebService   
  define("WEB_PROTOCOL", ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://')); // Protocolo web del servidor (http o https)
  define("WSDL_URL", WEB_PROTOCOL.$_SERVER['HTTP_HOST'].ROOT_FOLDER.WSDL_FILE); // URL de acceso al WebService
  define("WSDL_EXTERNO", WEB_PROTOCOL.$_SERVER['HTTP_HOST'].'/ap/interf/app/externo/externo.php?wsdl'); // URL de acceso al WebService
  
  define( "ValDir", "/var/www/html/ap/interf/app/corona/validator/" );   //Librerias generales.
  define( "LogDir", "var/www/html/ap/integradorgps/satt_dingps/crones/logs/" );        //Constantes de la aplicacion cliente.
  //define( "DictDir", "/var/www/html/ap/interf/app/corona/dictionary/" ); //Directorio Diccionario de terminos.
  //define( "WsDirx", "/var/www/html/ap/interf/app/corona/wsdl/" );
  //define( "FarMai", "supervisores@faro.com.co" ); //Mail de notificacion de creacion de rutas
  define( "FarMai", "faroavansat@eltransporte.com" ); //Mail de notificacion de creacion de rutas
  define( "UrlApl", "https://server.intrared.net/ap/" );               //Url Aplicaciones
  define( "NovAcarFar", "9995" );                                      //Novedad a cargo FARO
  //define( "FarMai", "felipe.malaver@intrared.net" );           //Mail de notificacion de creacion de rutas

  define( "Hostxx", "coronabd.intrared.net:3306" );                                                   //Servidor de base de datos ambiente 5.
  
  define( "DirLogoTranspo", "/var/www/html/ap/satt_corona/logos/" );                                           //Direcci�n para el logo de la transportadora.
  define( "DirImagTrafico", "/var/www/html/ap/satt_corona/imagenes/" );                                           //Direcci�n para el logo de la transportadora.
  define( "DirStanTrafico", "/var/www/html/ap/satt_standa/imagenes/" );                                           //Direcci�n para el logo de la transportadora.
  define( "DirPhotoConduc", "/var/www/html/ap/satt_corona/fotcon/" );                                           //Direcci�n para la foto de conductor.
  define( "DirPhotoVehicu", "/var/www/html/ap/satt_corona/fotveh/" );                                           //Direcci�n para la foto del vehiculo

  define("URL_INTERF_GPSEVE", "https://central.intrared.net/ap/interf/APIIntegradorGPS/v1/index.php");
  // define("URL_INTERF_GPSEVE", "http://10.0.100.12/ap/interf/APIIntegradorGPS/v1/index.php/setItinerary");
  
  ?>
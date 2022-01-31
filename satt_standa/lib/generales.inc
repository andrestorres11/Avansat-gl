<?php

include ("../".DIR_APLICA_CENTRAL."/lib/general/conexion_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/menu_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/pagina_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/aplica_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/aplica_perfil_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/aplica_usuari_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/usuari_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/servic_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/aplica_filtro_usuari_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/bd/seguridad/aplica_filtro_perfil_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/enlinea_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/fecha_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/form_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/tabla_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/mensajes_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/en_letras.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/encabe.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/interfaz_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/despac/Despachos.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/EnvioMensajes.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/paginador_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/general/pdf_lib.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/GeneralFunctions.inc");
//-----Inclusion de la clase para la caducidad de la contraseña-----
include ("../".DIR_APLICA_CENTRAL."/lib/general/new_password.inc");
//------------------------------------------------------------------
//Inclusion Librerias nuSOAP
//require_once("../".DIR_APLICA_CENTRAL."/lib/nuSOAP/nusoap.php");

//Llamado a las Librerias de Manejo Interfaz GPS y Aplicaciones SAT
#include ("../GPS/lib/generales.inc");
include ("../".DIR_APLICA_CENTRAL."/lib/interfaz_lib_sat.inc");
//fin llamado


# Llamado nuevo frame de formulario
#include ("../".DIR_APLICA_CENTRAL."/lib/general/DinamicHtml2.class.php");
include ("../".DIR_APLICA_CENTRAL."/lib/general/FormLib.class.php");

?>

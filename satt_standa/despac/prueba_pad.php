<?php
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

class PruebaPad
{
  var $conexion = null;

  function __construct()
  {
      define("USUARIO", "satt_faro");
      define("CLAVE", "sattfaro");
      define("BASE_DATOS", "satt_faro");
      define("DIR_APLICA_CENTRAL", "satt_standa");
      define("HOST", "aglbd.intrared.net");

      echo "<h2>Inicio Prueba PHP 5</h2><br>";
      
    
       include_once("../lib/general/conexion_lib.inc");
       $this -> conexion = new Conexion(HOST, USUARIO, CLAVE, BASE_DATOS);//Se crea la conexion a la base de datos

   
        $regist["despac"] = "1419912";
        

        $mQueryDespac = "SELECT a.cod_ciudes, b.num_placax, b.cod_transp, a.cod_manifi, a.num_despac " .
                            "FROM " . BASE_DATOS . ".tab_despac_despac a, " .
                            "" . BASE_DATOS . ".tab_despac_vehige b " .
                            "WHERE a.num_despac = b.num_despac " .
                            "AND a.num_despac = '" . $regist["despac"] . "'";

      
        $mQueryDespac = new Consulta($mQueryDespac, $this->conexion);
        $mDespac = $mQueryDespac->ret_matriz('a');
        # Inclucion de la Libreria para el wsdl del PAD      
        include( "../lib/InterfPad.inc"); 
        $mInterfPad = new TraficoPad( $this->conexion );
        $mSetRecursosPAD = $mInterfPad -> SetDataRecursos( $mDespac[0] );
        $Mse  =  str_replace(", Codigo:", "", $mSetRecursosPAD[1]);
        echo "<pre>Resultado WebService PAD:<br>"; print_r($Mse); echo "</pre>";
        
        

 
     
  }
}
$mPad = new PruebaPad(); 
?>
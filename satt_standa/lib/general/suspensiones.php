<?php
/****************************************************************************
NOMBRE:   Clase de suspensiones
FUNCION:  PROVEE LAS FUNCIONES NECESARIAS PARA EL MANEJO DE LA INFORMACION
          DE LOS SUSPENDIDOS
FECHA DE MODIFICACION: 25/02/2020
CREADO POR: Ing. Luis Carlos Manrique
MODIFICADO POR: Luis Carlos Manrique

****************************************************************************/
/*ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);*/

class suspensiones {

  //Variables de clase
  var $AjaxConnection;   

  function __construct()
  {

    //Valida request para traer los documentos necesar
    if(isset($_REQUEST['cod_tercer'])){
      chdir(__DIR__."/../");
      include '../lib/general/constantes.inc';
      include '../lib/ajax.inc';
    }else{
      include 'constantes.inc';
      include '../'.DIR_APLICA_CENTRAL.'/lib/ajax.inc';
    }
      
    $this -> conexion = $AjaxConnection;
    $this->SetSuspensiones();
  }



  /*! \fn: SetSuspensiones
   *  \brief: Consume el API generado para consultar suspenciones
   *  \author: Ing. Luis Manrique
   *  \date: 27-02-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $cod_tercer = Nit
   *          $cod_usuari = usuario de sesión
   *  \return: array u json
   */
  function SetSuspensiones($cod_tercer = null, $cod_usuari = null)
  {

    //Captura el codigo del tercero por Request o por parametro.
    $cod_tercer = isset($_REQUEST['cod_tercer']) ? $_REQUEST['cod_tercer'] : $cod_tercer;
    
    //Valida codigo de tercero - Usuario de sesión.
    if(!is_null($cod_tercer)){

      //Consume Api con parametros
      $dataTerceros = json_decode(file_get_contents('https://dev.intrared.net:8083/ap/cmaya/ut/consultor/app/client/facturacionVencida/fact_vencida_faro.php?cod_tercero='.$cod_tercer), true);

    }else if(!is_null($cod_usuari)){

      //Se crea la consulta para traer el NIT
      $sql =  "SELECT   * 
                 FROM   ".BASE_DATOS.".tab_aplica_filtro_usuari ".
               "WHERE   cod_usuari = '".$cod_usuari."'";

      //Ejecuta la consulta
      $consulta = new Consulta( $sql, $this -> conexion );
      $cod_tercer = $consulta->ret_matrix( 'a' );

      //Consume Api con parametros
      $dataTerceros = json_decode(file_get_contents('https://dev.intrared.net:8083/ap/cmaya/ut/consultor/app/client/facturacionVencida/fact_vencida_faro.php?cod_tercero='.$cod_tercer[0]['clv_filtro']), true);

    }else{
      //Consume Api de toda la data
      $dataTerceros = json_decode(file_get_contents('https://dev.intrared.net:8083/ap/cmaya/ut/consultor/app/client/facturacionVencida/fact_vencida_faro.php'), true);
    }

    /*$dataTerceros[1000] = array(
        'cod_tercer' => 830090031, 
        'abr_tercer' => 'OTRANS HELTP LOGISTIC', 
        'fec_vencin' => '2020-02-10',
        'num_factur' => 564564564,
        'saldo_factura' => 158000
    );

    $dataTerceros[1001] = array(
        'cod_tercer' => 830141359,
        'abr_tercer' => 'TRANSBORDA S.A.S.', 
        'fec_vencin' => '2020-02-11',
        'num_factur' => 5645647544,
        'saldo_factura' => 1580000
    );*/

    //Codifica en Hson
    $raw_data = json_encode($dataTerceros);

    //Codifica en Array
    $cReturn = json_decode($raw_data, true);

    //Se recorre el arreglo
    foreach ($cReturn as $key => $value) {

      //Valida si la factura tiene dias en prologa, para asignarlos a la fehca vencimiento
      if(!is_null($value['dias_prorro'])){
        $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($value['fec_vencin']."+ ".$value['dias_prorro']." days")); 
      }

      //Valida si tiene nota contable, si la tiene, le resta el valor y del recaudo al valor total
      if(!is_null($value['nota_contable'])){
        $cReturn[$key]['val_totalx'] = $value['val_totalx']-$value['val_recaud']-$value['nota_contable']; 
      }

      //Recorre los campos
      foreach ($value as $keyCampo => $valueCampo) {
        if($keyCampo == 'fec_vencin'){

          //Asigna los dias que se asigna para hacer el pago
          $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($value['fec_vencin']."+ 10 days")); 
          $cReturn[$key]['dia'] = date("D", strtotime($value['fec_vencin']."+ 10 days"));

          //Valida fines de semana para correr los dias
          if($cReturn[$key]['dia'] == 'Sun'){
            $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($cReturn[$key]['fec_vencin']."+ 1 days"));
          }elseif($cReturn[$key]['dia'] == 'Sat'){
            $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($cReturn[$key]['fec_vencin']."+ 2 days"));
          }
        }         
      }
    }

    //Identifica que caso aplica para ser suspendido o por suspender
    $segSusp = [];
    foreach ($cReturn as $key => $value) {
      if($value['fec_vencin'] < date('Y-m-d')){
        $segSusp['suspendido'][] = $value;
      }else{
        $segSusp['porSuspender'][] = $value;
      }
    }

    //Retorna la data dependiendo de la solicitud
    if(isset($_REQUEST['cod_tercer'])){
      echo json_encode($segSusp, true);
    }else{
      return $segSusp;
    }
  }

}//fin clase


$_SUSP = new suspensiones();

?>
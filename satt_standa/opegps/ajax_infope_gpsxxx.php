<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_infope_gpsxxx
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'form':
        $this->form();
        break;
      case 'regForm':
        $this->regForm();
        break;
      case 'setRegistros':
        $this->setRegistros();
        break;
      case 'updEst':
        $this->updEst();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Cristian Andres Martinez
  *  \date: 02/06/2020
  *  \date modified: 
  *  \return HTML
  */
  private function setRegistros() {
    $mSql = " SELECT  
                a.cod_operad,
                a.nit_operad,
                a.nom_operad,
                a.url_gpsxxx,
                IF(a.apl_idxxxx=1,'SI','NO') as 'apl_idxxxx',
                IF(a.ind_cronxx=1,'SI','NO') as 'ind_cronxx',
                IF(a.ind_intgps=1,'SI','NO') as 'ind_intgps',
                IF(a.ind_rndcxx=1,'SI','NO') as 'ind_rndcxx',
                IF(a.ind_estado=1,'ACTIVO','INACTIVO') as 'ind_estado'
                FROM  ". BASE_DATOS .".tab_genera_opegps a ";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  self::cleanArray($mMatriz->ret_matrix("a"));
    
    $data = [];
    foreach ($mMatriz as $key => $datos) {
      $ind_intgps = $datos['ind_intgps'];
    	foreach ($datos as $campo => $valor) {
        if($campo == "ind_estado"){
          if($ind_intgps== 'NO'){
            if($valor == 'ACTIVO'){
                $html ='<button onclick="updEst(this)" value="'.$datos['cod_operad'].'" data-estado="1" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
              }else{
                $html ='<button onclick="updEst(this)" value="'.$datos['cod_operad'].'" data-estado="0" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
              }
            $html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_operad'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
          }else{
            $html= '<label for="Name">Operador Estandar</label>';
          }    
          $data[$key][] = $valor;
          $data[$key][] = $html;	
        }else{
          $data[$key][] = $valor;	
        }
    	}
    }
    $return = array("data" => $data);
    echo json_encode($return);
  }

  /*! \fn: form
   *  \brief: Crea el formulario de para registrar información del banco
   *  \author: Ing. Andres Martinez
   *  \date: 27/01/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function form(){
      try {
        
        if($_REQUEST['cod_operad'] == ''){
          
          $mData = $arrayName = array('cod_operad' => '', 'nom_operad' => '', 'nit_operad' => '', 'nit_verifi' => '', 'url_gpsxxx' => '', 'apl_idxxxx' => '',  'ind_cronxx' => '', 'ind_rndcxx' => '', 'ind_intgps' => '');
        }else{
          $mSql = " SELECT  a.cod_operad,
                            a.nom_operad,
                            a.nit_operad,
                            a.nit_verifi,
                            a.url_gpsxxx,
                            a.apl_idxxxx,
                            a.ind_cronxx,
                            a.ind_intgps,
                            a.ind_rndcxx
                      FROM  ".BASE_DATOS.".tab_genera_opegps a
                     WHERE  a.cod_operad = ".$_REQUEST['cod_operad'];
          $mMatriz = new Consulta($mSql, $this->conexion);
          $mData = $mMatriz->ret_matrix("a")[0];
        }

        //arrays que contienen la información de los campos y los titulos del formulario
          $mTittle[0] = array( "id" => "dat_basico", "tittle" => "DATOS BASICOS");
          $mTittle[0]['data'] = array("cod_operad" => array(
          													'name' => "Codigo", 
          													'type' => "hidden",
          													'class' => "", 
          													'atribute' => array(
																				'data-validate' => "text",
																				'readonly' => "readonly"
																				)
          													),
          							  "nom_operad" => array(
          													'name' => "Nombre Operador", 
          													'type' => "input",
          													'class' => "validate", 
          													'atribute' => array(
          																		'data-validate' => "text",
          																		'required' => "required" ,
          																		"validate"=>"texto",
          																		"minlength"=>3,
          																		"obl"=> 1,
          																		"onkeyup" => "validateFields(this)"
          																		)
                                    ),
                          "url_gpsxxx" => array(
                                      'name' => "URL Operador", 
                                      'type' => "input",
                                      'class' => "validate", 
                                      'atribute' => array(
                                                'required' => "required" ,
                                                "minlength"=>3,
                                                "obl"=> 1,
                                                "onkeyup" => "validateFields(this)"
                                                )
                                      ),        
                            "nit_operad" => array(
                                      'name' => "Nit Operador", 
                                      'type' => "inputN",
                                      'class' => "validate", 
                                      'atribute' => array(
                                                'data-validate' => "number",
                                                'required' => "required" ,
                                                "validate"=>"numero",
                                                "minlength"=>3,
                                                "obl"=> 1,
                                                "onkeyup" => "validateFields(this)"
                                                )
                                      ),
                            "nit_verifi" => array(
                                        'name' => "DV", 
                                        'type' => "inputN",
                                        'class' => "validate", 
                                        'atribute' => array(
                                                  'data-validate' => "number",
                                                  'required' => "required" ,
                                                  "validate"=>"numero",
                                                  "minlength"=>1,
                                                  "maxlength"=>1,
                                                  "obl"=> 1,
                                                  "onkeyup" => "validateFields(this)"
                                                  )
                                        ),
                            "apl_idxxxx" => array(
                                        'name' => "Aplica ID", 
                                        'type' => "check",
                                        'class' => "validate", 
                                        'atribute' => array(
                                                  'required' => "required" ,
                                                  "minlength"=>3,
                                                  "obl"=> 1,
                                                  "onkeyup" => "validateFields(this)"
                                                  )
                                        ),
                            "ind_cronxx" => array(
                                        'name' => "Integrado con CRON", 
                                        'type' => "check",
                                        'class' => "validate", 
                                        'atribute' => array(
                                                  'required' => "required" ,
                                                  "minlength"=>3,
                                                  "obl"=> 1,
                                                  "onkeyup" => "validateFields(this)"
                                                  )
                                        ),
                          "ind_rndcxx" => array(
                                      'name' => "Reporta RNDC", 
                                      'type' => "check",
                                      'class' => "validate", 
                                      'atribute' => array(
                                                'required' => "required" ,
                                                "minlength"=>3,
                                                "obl"=> 1,
                                                "onkeyup" => "validateFields(this)"
                                                )
                                      ),
                          "ind_intgps" => array(
                                      'name' => "Integrado GPS", 
                                      'type' => "check",
                                      'class' => "validate", 
                                      'atribute' => array(
                                                'required' => "required" ,
                                                "minlength"=>3,
                                                "obl"=> 1,
                                                "onkeyup" => "validateFields(this)"
                                                )
                                      )             
                                    );

          //Imprime el retorno de información 
          echo utf8_decode(self::tablesHTML($mTittle, $mData, 1));
      } catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
      }
    }

     /*! \fn: tablesHTML
   *  \brief: Arma una la tabla de HTML basado el los parametros que envian
   *  \author: Ing. Andres Martinez
   *  \date: 28/04/2020
   *  \date modified: dia/mes/año
   *  \param: $arrayTittle, $arrayData, $col
   *  \return: HTML 
   */
  private function tablesHTML($arrayTittle = null, $arrayData = null, $col){ 
    //Inicializa variables necesarias
      $html = "";
      $ultPos = "";

      //Recorre el arreglo de titulos
      foreach ($arrayTittle as $key => $titulos) {
        //Arma la cabecera del HTML
        $html .= '
                <div class="panel panel-success">
                  <div class="panel-heading">
                    <h4>'.$titulos['tittle'].'</h4>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <div class="panel-body">
                    <form role="form" id="'.$titulos['id'].'">
                  <div class="well">';
        //Crea Bandera para las columnas
        $ban = 0;
        //Recorre la posicion de los titulos
        foreach ($titulos as $key1 => $data) {
          //Recorre los tutulos del array titulos
          foreach ($data as $keyfields => $fields) {
            //Recorre la data
            foreach ($arrayData as $keyValues => $values) {
            //Captura la ultima posición del los tutulos de la data
             $ultPos = end($values);
              //Valida si es igual a la tabla
              if ($keyfields == $keyValues) {
                //Valida la posición para crear la tabla
                if ($ban == 0) {
                  $html .= '<div class="row">
                            ';
                   $html .= self::fields($keyfields, $values, $fields, $col);
                  //Cambia el estado de la bandera para la siguiente columna
                  $ban = 1;
                  //Valida si es el ultimo campo para cerrar la fila
                  $html .= $ultPos == $fields ? '</div>' : '';
                  $ban = $ultPos == $fields ? 0 : $ban;
                }else{
                  $html .= self::fields($keyfields, $values, $fields, $col);
                  //Cambia el estado de la bandera para la siguiente columna
                  $ban ++;
                  //Valida si es el ultimo campo para cerrar la fila
                  $html .= $ultPos == $fields ? '</div>' : '';
                  $ban = $ultPos == $fields ? 0 : $ban;
                }
              }
            }
          }
        }

        //Cierra el HTML
        $html .= '</div>
              </form>
            </div>
            <!-- /.card -->
          </div>';
      }
      return $html;

  }
  /*! \fn: fields
	   *  \brief: Crea los campos para el formulario
	   *  \author: Ing. Luis Manrique
	   *  \date: 28/04/2020
	   *  \date modified: dia/mes/año
	   *  \param: $name, $value, $info, $col
	   *  \return: HTML 
    */
    private function fields($name, $value, $info, $col){		
    	//Variables necesarias
    	$attr = '';

    	//Crea las clases para las columnas
    	switch ($col) {
    		case 1:
    			$classCol = 'col-xs-6 col-lg-6';
    			break;
    		case 2:
    			$classCol = 'col-xs-6 col-md-6 col-lg-6';
    			break;
    		case 3:
    			$classCol = 'col-xs-6 col-sm-6 col-md-4 col-lg-6';
    			break;
    	}

    	//Crea los campos en el formulario
		switch ($info['type']) {
			case 'input':
				//Recorre la posicion de atributos para asignalos al campo
				foreach ($info['atribute'] as $nameAttr => $valueAttr) {
					$attr .= $nameAttr.'="'.$valueAttr.'" ';
				}

				$field = '<div class="form-group '.$classCol.'">
							<input type="text" class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.utf8_encode($info['name']).'" style="height: auto;"/>
						</div>';
				break;
        case 'inputN':
          //Recorre la posicion de atributos para asignalos al campo
          foreach ($info['atribute'] as $nameAttr => $valueAttr) {
            $attr .= $nameAttr.'="'.$valueAttr.'" ';
          }
  
          $field = '<div class="form-group '.$classCol.'">
                <input type="number" class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.utf8_encode($info['name']).'" style="height: auto;"/>
              </div>';
          break;
			case 'hidden':
				//Recorre la posicion de atributos para asignalos al campo
				foreach ($info['atribute'] as $nameAttr => $valueAttr) {
					$attr .= $nameAttr.'="'.$valueAttr.'" ';
				}

				$field = '<div class="form-group '.$classCol.'" style="display:none;">
							<input type="hidden" class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.utf8_encode($info['name']).'" style="height: auto;"/>
						</div>';
				break;

        case 'check':
        if($value==1){
          $checked="checked";
        }
          //Recorre la posicion de atributos para asignalos al campo
				foreach ($info['atribute'] as $nameAttr => $valueAttr) {
					$attr .= $nameAttr.'="'.$valueAttr.'" ';
				}
        $field = '<div class="form-group '.$classCol.'">
              <label for="cbox2">'.utf8_encode($info['name']).'</label>
              <input type="checkbox" class="form-control form-control-sm" '.$attr.' name="'.$name.'" id="'.$name.'" '.$checked.' style="height: auto;"/>
            </div>';
        break;
		}

		//Se retorna el campo
    	return $field;
    }
/*! \fn: updEst
   *  \brief: Actualiza el estado del registro
   *  \author: Ing. Luis Manrique
   *  \date: 28/04/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
  function updEst(){
    try {
      //Varibales Necesarias
      $return = [];
      $estado = $_REQUEST['estado'] == 1 ? 0 : 1;

      //Geera query
      $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_opegps 
                    SET ind_estado = $estado,
                        usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                        fec_modifi = NOW()
                  WHERE cod_operad = ".$_REQUEST['cod_operad'];
      $consulta = new Consulta($mQuery, $this -> conexion);   
      if($consulta == true){
        $return['status'] = 200;
        $return['response'] = 'El registro ha sido actualizado exitosamente.';
      }else{
        $return['status'] = 500;
        $return['response'] = 'Error al realizar la actualzación.';
      }

      //Devuelve estatus de la consulta
      echo json_encode($return);
    } catch (Exception $e) {
      echo 'Excepción updEst: ',  $e->getMessage(), "\n";
    }
  }
  /*! \fn: regForm
   *  \brief: Actualzia o crea registros de bancos
   *  \author: Ing. Luis Manrique
   *  \date: 28/04/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: HTML 
   */
  function regForm(){
    try {
      //Varibales Necesarias
      $return = [];
      //Consulta
      $codigo = empty($_REQUEST['cod_operad']) ? '' : "cod_operad = ".$_REQUEST['cod_operad'].", ";
      $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_opegps 
                          SET  $codigo
                            nom_operad= '".$_REQUEST['nom_operad']."',
                            ind_usaidx= 1,
                            nit_operad= '".$_REQUEST['nit_operad']."',
                            nit_verifi= '".$_REQUEST['nit_verifi']."',
                            ind_estado= 1,
                            ind_cronxx= '".($_REQUEST['ind_cronxx'] == 'on'? 1 : 0)."',
                            ind_rndcxx= '".($_REQUEST['ind_rndcxx']== 'on'? 1 : 0)."',
                            ind_intgps= '".($_REQUEST['ind_intgps']== 'on'? 1 : 0)."',
                            url_gpsxxx= '".$_REQUEST['url_gpsxxx']."',
                            apl_idxxxx= '".($_REQUEST['apl_idxxxx']== 'on'? 1 : 0)."',
                            usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
                            fec_creaci = NOW()
      ON DUPLICATE KEY UPDATE 	nom_operad= '".$_REQUEST['nom_operad']."',
                            nit_operad= '".$_REQUEST['nit_operad']."',
                            nit_verifi= '".$_REQUEST['nit_verifi']."',
                            ind_cronxx= '".($_REQUEST['ind_cronxx'] == 'on'? 1 : 0)."',
                            ind_rndcxx= '".($_REQUEST['ind_rndcxx']== 'on'? 1 : 0)."',
                            ind_intgps= '".($_REQUEST['ind_intgps']== 'on'? 1 : 0)."',
                            url_gpsxxx= '".$_REQUEST['url_gpsxxx']."',
                            apl_idxxxx= '".($_REQUEST['apl_idxxxx']== 'on'? 1 : 0)."',
                            usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                            fec_modifi = NOW()";
                            
          $consulta = new Consulta($mQuery, $this -> conexion);   
          if($consulta == true){
            
            $return['status'] = 200;
            if(empty($_REQUEST['cod_operad'])){
              $return['response'] = 'El Operador GPS ha sido registrado exitosamente.';
            }else{
              $return['response'] = 'El Operador GPS ha sido Actualizado exitosamente.';
            }
          }else{
            $return['status'] = 500;
            $return['response'] = 'Error al realizar el registro.';
          }
      

      echo json_encode($return);
    } catch (Exception $e) {
      echo 'ExcepciÃ³n updEst: ',  $e->getMessage(), "\n";
    }
  }


  function cleanArray($array){

    $arrayReturn = array();

    //Convert function
    $convert = function($value){
        if(is_string($value)){
            return utf8_encode($value);
        }
        return $value;
    };

    //Go through data
    foreach ($array as $key => $value) {
        //Validate sub array
        if(is_array($value)){
            //Clean sub array
            $arrayReturn[$convert($key)] = self::cleanArray($value);
        }else{
            //Clean value
            $arrayReturn[$convert($key)] = $convert($value);
        }
    }
    //Return array
    return $arrayReturn;
}


  
}

$proceso = new ajax_infope_gpsxxx();
 ?>
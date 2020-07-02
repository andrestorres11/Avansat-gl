<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_bancox
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
      case 'updEst':
        $this->updEst();
        break;
      case 'regForm':
        $this->regForm();
        break;
      case 'setRegistros':
        $this->setRegistros();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Luis Manrique
  *  \date: 27/01/2020
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  private function setRegistros() {
    $mSql = " SELECT  a.cod_bancox,
                      a.nom_bancox,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_genera_bancox a";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "ind_estado"){
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_bancox'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_bancox'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_bancox'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
   *  \author: Ing. Luis Manrique
   *  \date: 27/01/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function form(){
      try { 
      	//Valida si existe codgi de bancos para consultarlos o crearlos en vacio
        if($_REQUEST['cod_bancox'] == ''){
          $mData = $arrayName = array('cod_bancox' => '', 'nom_bancox' => '');
        }else{
          $mSql = " SELECT  a.cod_bancox,
                            a.nom_bancox
                      FROM  ".BASE_DATOS.".tab_genera_bancox a
                     WHERE  a.cod_bancox = ".$_REQUEST['cod_bancox'];
          $mMatriz = new Consulta($mSql, $this->conexion);
          $mData = $mMatriz->ret_matrix("a")[0];
        }

        //arrays que contienen la información de los campos y los titulos del formulario
          $mTittle[0] = array( "id" => "dat_basico", "tittle" => "DATOS BASICOS");
          $mTittle[0]['data'] = array("cod_bancox" => array(
          													'name' => "Codigo", 
          													'type' => "hidden",
          													'class' => "", 
          													'atribute' => array(
																				'data-validate' => "text",
																				'readonly' => "readonly"
																				)
          													),
          							  "nom_bancox" => array(
          													'name' => "Nombre Banco", 
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
          													)
                                      );
          
          //Imprime el retorno de información 
          echo utf8_decode(self::tablesHTML($mTittle, $mData, 1));
      } catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
      }
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_bancox 
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_bancox = ".$_REQUEST['cod_bancox'];
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
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
      try {
        //Varibales Necesarias
        $return = [];
        $codigo = empty($_REQUEST['cod_bancox']) ? '' : "cod_bancox = ".$_REQUEST['cod_bancox'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_bancox 
	                       SET  $codigo
	                            nom_bancox = '".$_REQUEST['nom_bancox']."',
	                            ind_estado = 1,
	                            usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_creaci = NOW()
	   ON DUPLICATE KEY UPDATE 	nom_bancox = '".$_REQUEST['nom_bancox']."',
	                            usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_modifi = NOW()";
	    $consulta = new Consulta($mQuery, $this -> conexion);   
	    if($consulta == true){
	      $return['status'] = 200;
	      $return['response'] = 'El registro ha sido registrado exitosamente.';
	    }else{
	      $return['status'] = 500;
	      $return['response'] = 'Error al realizar el registro.';
	    }
        

        echo json_encode($return);
      } catch (Exception $e) {
        echo 'Excepción updEst: ',  $e->getMessage(), "\n";
      }
    }


   /*! \fn: tablesHTML
   *  \brief: Arma una la tabla de HTML basado el los parametros que envian
   *  \author: Ing. Luis Manrique
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
    			$classCol = 'col-xs-12 col-lg-2';
    			break;
    		case 2:
    			$classCol = 'col-xs-12 col-md-6 col-lg-2';
    			break;
    		case 3:
    			$classCol = 'col-xs-12 col-sm-6 col-md-4 col-lg-2';
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
			case 'hidden':
				//Recorre la posicion de atributos para asignalos al campo
				foreach ($info['atribute'] as $nameAttr => $valueAttr) {
					$attr .= $nameAttr.'="'.$valueAttr.'" ';
				}

				$field = '<div class="form-group '.$classCol.'" style="display:none;">
							<input type="hidden" class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.utf8_encode($info['name']).'" style="height: auto;"/>
						</div>';
				break;
		}

		//Se retorna el campo
    	return $field;
    }
  
  
  
}

$proceso = new ajax_genera_bancox();
 ?>
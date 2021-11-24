<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_hojvid_ctxxxx
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
      case 'dataList':
        $this->dataList();
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
    $mSql = " SELECT  a.cod_docume,
                      a.pri_apelli,
                      a.seg_apelli,
                      a.nom_contra,
                      a.num_celula,
                      a.dir_emailx,
                      b.nom_tipcto,
                      a.fec_ingres,
                      a.num_cuenta,
                      c.nom_bancox,
                      d.nom_tipcta,
                      a.doc_duecue,
                      CONCAT(a.pri_apelli, ' ', a.seg_apelli, ' ', a.nom_contra) AS nom_comple,
                      e.nom_activi,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_hojvid_ctxxxx a
          INNER JOIN  ".BASE_DATOS.".tab_genera_tipcto b
                  ON  a.cod_tipcto = b.cod_tipcto
          INNER JOIN  ".BASE_DATOS.".tab_genera_bancox c
                  ON  a.cod_bancox = c.cod_bancox
          INNER JOIN  ".BASE_DATOS.".tab_genera_tipcta d
                  ON  a.cod_tipcta = d.cod_tipcta
          INNER JOIN  ".BASE_DATOS.".tab_genera_activi e
                  ON  a.cod_activi = e.cod_activi";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "ind_estado"){
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_docume'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_docume'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_docume'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
   *  \brief: Crea el formulario de para registrar información del Tipo Cuenta
   *  \author: Ing. Luis Manrique
   *  \date: 27/01/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function form(){
      try { 
      	//Valida si existe codgi de tipo de cuenta para consultarlos o crearlos en vacio
        if($_REQUEST['cod_docume'] == ''){
          $mData = $arrayName = array( 
                                      'cod_tipdoc' => '',
                                      'nom_tipdoc' => '',
                                      'cod_docume' => '', 
                                      'nom_contra' => '',
                                      'pri_apelli' => '',
                                      'seg_apelli' => '',
                                      'nom_tiporh' => '',
                                      'cod_tipsex' => '',
                                      'nom_tipsex' => '',
                                      'dir_domici' => '',
                                      'cod_ciudad' => '',
                                      'nom_ciudad' => '',
                                      'num_celula' => '',
                                      'dir_emailx' => '',
                                      'usr_asigna' => '',
                                      'cod_perfil' => '',
                                      'nom_perfil' => '',
                                      'nom_cargox' => '',
                                      'cod_activi' => '',
                                      'nom_activi' => '',
                                      'cod_tipcto' => '',
                                      'nom_tipcto' => '',
                                      'fec_ingres' => '',      
                                      'sue_basexx' => '',
                                      'val_hordia' => '',
                                      'val_hornoc' => '',
                                      'val_horfyd' => '',
                                      'num_cuenta' => '',
                                      'cod_tipcta' => '',
                                      'nom_tipcta' => '',
                                      'cod_bancox' => '',
                                      'nom_bancox' => '',
                                      'due_cuenta' => '',
                                      'doc_duecue' => ''
                                    );   
                                    
        }else{
          $mSql = " SELECT  e.cod_tipdoc,
                            e.nom_tipdoc,
                            a.cod_docume,
                            a.pri_apelli,
                            a.seg_apelli,
                            a.nom_contra,
                            a.nom_tiporh,
                            h.cod_tipsex, 
                            h.nom_tipsex,   
                            a.dir_domici,
                            f.cod_ciudad,
                            f.nom_ciudad,
                            a.num_celula,
                            a.dir_emailx,
                            a.usr_asigna,
                            g.cod_perfil,
                            g.nom_perfil,
                            a.nom_cargox,
                            i.cod_activi,
                            i.nom_activi,
                            b.cod_tipcto,
                            b.nom_tipcto,
                            a.fec_ingres,
                            a.sue_basexx,
                            a.val_hordia,
                            a.val_hornoc,
                            a.val_horfyd,
                            a.num_cuenta,
                            d.cod_tipcta,
                            d.nom_tipcta,
                            c.cod_bancox,
                            c.nom_bancox,
                            a.due_cuenta,
                            a.doc_duecue
                      FROM  ".BASE_DATOS.".tab_hojvid_ctxxxx a
                INNER JOIN  ".BASE_DATOS.".tab_genera_tipcto b
                        ON  a.cod_tipcto = b.cod_tipcto
                INNER JOIN  ".BASE_DATOS.".tab_genera_bancox c
                        ON  a.cod_bancox = c.cod_bancox
                INNER JOIN  ".BASE_DATOS.".tab_genera_tipcta d
                        ON  a.cod_tipcta = d.cod_tipcta
                INNER JOIN  ".BASE_DATOS.".tab_genera_tipdoc e
                        ON  a.cod_tipdoc = e.cod_tipdoc
                INNER JOIN  ".BASE_DATOS.".tab_genera_ciudad f
                        ON  a.cod_ciudad = f.cod_ciudad
                LEFT JOIN  ".BASE_DATOS.".tab_genera_perfil g
                        ON  a.cod_perfil = g.cod_perfil
                INNER JOIN  ".BASE_DATOS.".tab_genera_tipsex h
                        ON  a.cod_tipsex = h.cod_tipsex
                INNER JOIN  ".BASE_DATOS.".tab_genera_activi i
                        ON  a.cod_activi = i.cod_activi
                     WHERE  a.cod_docume = ".$_REQUEST['cod_docume']."";
          $mMatriz = new Consulta($mSql, $this->conexion);
          $mData = $mMatriz->ret_matrix("a")[0];
        }

        //arrays que contienen la información de los campos y los titulos del formulario
          $mTittle[0] = array( "id" => "dat_basico", "tittle" => "DATOS BASICOS");
          $mTittle[0]['data'] = array("cod_tipdoc" => array(
	                                                            'name' => "Codigo de tipo de documento", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"text",
	                                                                "readonly"=>"readonly",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_tipdoc" => array(
	                                                            'name' => "Tipo de documento",
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"dir",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                      "cod_docume" => array(
              													'name' => "Documento Identidad", 
              													'type' => "input",
              													'class' => "validate", 
              													'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "min"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
																)
          													),
                                      "nom_contra" => array(
	                                                            'name' => "Nombres", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
	    							  "pri_apelli" => array(
	          													'name' => "Primer Apellido", 
	          													'type' => "input",
	          													'class' => "validate", 
	          													'atribute' => array(
																	"validate"=>"texto",
																	"minlength"=>3,
																	"obl"=> 1,
																	"onkeyup" => "validateFields(this)"
																	)
	    													    ),
                                       "seg_apelli" => array(
	                                                            'name' => "Segundo Apellido", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_tiporh" => array(
	                                                            'name' => "Factor RH", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "cod_tipsex" => array(
	                                                            'name' => "Codigo de genero", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "readonly"=>"readonly",
	                                                                "min"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_tipsex" => array(
	                                                            'name' => "Genero", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "dir_domici" => array(
	                                                            'name' => "Dirección", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"dir",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "cod_ciudad" => array(
	                                                            'name' => "Codigo de ciudad", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "readonly"=>"readonly",
	                                                                "min"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_ciudad" => array(
	                                                            'name' => "Ciudad", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "num_celula" => array(
	                                                            'name' => "Numero celular", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "min"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "dir_emailx" => array(
	                                                            'name' => "Email", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"email",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "usr_asigna" => array(
	                                                            'name' => "Usuario asignado", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "cod_perfil" => array(
	                                                            'name' => "Codigo de perfil", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "readonly"=>"readonly",
	                                                                "min"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_perfil" => array(
	                                                            'name' => "Tipo de perfil", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_cargox" => array(
	                                                            'name' => "Cargo", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                              ),
                                        "cod_activi" => array(
                                                                'name' => "Codigo de actividad", 
                                                                'type' => "hidden",
                                                                'class' => "validate", 
                                                                'atribute' => array(
                                                                    "validate"=>"numero",
                                                                    "readonly"=>"readonly",
                                                                    "min"=>1,
                                                                    "obl"=> 1,
                                                                    "onkeyup" => "validateFields(this)"
                                                                  )
                                                              ),                      
                                        "nom_activi" => array(
                                                                'name' => "Actividad", 
                                                                'type' => "input",
                                                                'class' => "validate list", 
                                                                'atribute' => array(
                                                                    "validate"=>"texto",
                                                                    "minlength"=>1,
                                                                    "obl"=> 1,
                                                                    "onkeyup" => "validateFields(this)"
                                                                  )
                                                              )
                                       
                                      );




		  $mTittle[1] = array( "id" => "tip_contra", "tittle" => "TIPO DE CONTRATO");
          $mTittle[1]['data'] = array("cod_tipcto" => array(
	                                                            'name' => "Codigo de tipo de contrato", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "readonly"=>"readonly",
	                                                                "min"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_tipcto" => array(
	                                                            'name' => "Tipo de contrato", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "fec_ingres" => array(
	                                                            'name' => "Fecha ingreso", 
	                                                            'type' => "input",
	                                                            'class' => "validate date", 
	                                                            'atribute' => array(
	                                                                "validate"=>"date",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "sue_basexx" => array(
	                                                            'name' => "Sueldo base", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "val_hordia" => array(
	                                                            'name' => "Valor hora dia", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                   			),
                                       "val_hornoc" => array(
	                                                            'name' => "Valor hora noche", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "val_horfyd" => array(
	                                                            'name' => "Valor hora festivos y dominicales", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"textarea",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "num_cuenta" => array(
	                                                            'name' => "Número de cuenta", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "min"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "cod_tipcta" => array(
	                                                            'name' => "Codigo de tipo de cuenta", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "readonly"=>"readonly",
	                                                                "min"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_tipcta" => array(
	                                                            'name' => "Tipo de cuenta", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "cod_bancox" => array(
	                                                            'name' => "Codigo de banco", 
	                                                            'type' => "hidden",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"numero",
	                                                                "readonly"=>"readonly",
	                                                                "min"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "nom_bancox" => array(
	                                                            'name' => "Banco", 
	                                                            'type' => "input",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "due_cuenta" => array(
	                                                            'name' => "A nombre de", 
	                                                            'type' => "input",
	                                                            'class' => "validate", 
	                                                            'atribute' => array(
	                                                                "validate"=>"texto",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                              ),
                                        "doc_duecue" => array(
                                                                'name' => "Documento", 
                                                                'type' => "input",
                                                                'class' => "validate", 
                                                                'atribute' => array(
                                                                    "validate"=>"numero",
                                                                    "minlength"=>3,
                                                                    "obl"=> 1,
                                                                    "onkeyup" => "validateFields(this)"
                                                                  )
                                                              )
                                      );

          //Imprime el retorno de información 
          echo utf8_decode(self::tablesHTML($mTittle, $mData, 3));
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_hojvid_ctxxxx 
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_docume = ".$_REQUEST['cod_docume'];
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
   *  \brief: Actualzia o crea registros de tipo de cuenta
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
        $fields = '';
        $excFields = ['option','nom_tipdoc','nom_ciudad','nom_perfil','nom_tipcto','nom_tipcta','nom_bancox','nom_tipsex','nom_activi'];

        foreach ($_REQUEST as $key => $value) {
        	if (!in_array($key, $excFields)) {
        		if (empty($fields)) {
        			$fields = $key." = '".$value."'";
        		}else{
        			$fields .= ", ".$key." = '".$value."'";
        		}
        	}
        }


       $codigo = empty($_REQUEST['cod_docume']) ? '' : "cod_docume = ".$_REQUEST['cod_docume'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_hojvid_ctxxxx 
	                       SET  $fields,
	                            ind_estado = 1,
	                            usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_creaci = NOW()
	   ON DUPLICATE KEY UPDATE 	$fields,
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



    //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Luis Manrique
  *  \date: 27/01/2020
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  private function dataList() {
    switch ($_REQUEST['file']) {
      case 'nom_tipcto':
        $table = 'tab_genera_tipcto';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

      case 'nom_tipcta':
        $table = 'tab_genera_tipcta';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

      case 'nom_bancox':
        $table = 'tab_genera_bancox';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

      case 'nom_tipdoc':
        $table = 'tab_genera_tipdoc';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

      case 'nom_tiporh':
        $table = 'tab_genera_tiporh';
        $cod = "nom_".explode("_", $_REQUEST['file'])[1];
        break;

       case 'nom_ciudad':
        $table = 'tab_genera_ciudad';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

       case 'nom_perfil':
        $table = 'tab_genera_perfil';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

       case 'nom_tipsex':
        $table = 'tab_genera_tipsex';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

       case 'usr_asigna':
        $_REQUEST['file'] = 'cod_usuari';
        $table = 'tab_genera_usuari';
        $cod = "cod_".explode("_", $_REQUEST['file'])[1];
        break;

        case 'nom_activi':
          $table = 'tab_genera_activi';
          $cod = "cod_".explode("_", $_REQUEST['file'])[1];
          $cond = 'AND ind_acthjv = 1';
          break;
    }


    $mSql = " SELECT  a.".$cod.",
                      a.".$_REQUEST['file']."
                FROM  ".BASE_DATOS.".$table a
               WHERE  a.".$_REQUEST['file']." LIKE '%".$_REQUEST['query']."%' $cond";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $return = [];

    foreach ($mMatriz as $key => $datos) {
      $return[$key]['value'] = $datos[$_REQUEST['file']];  
      $return[$key]['data'] = $datos[$cod];  
      $return[$key]['campo'] = $cod;  
    }


    $return = array("suggestions" => $return);

    echo json_encode($return);
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
							<input type="text" class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.$info['name'].'" style="height: auto;"/>
						</div>';
				break;
			case 'hidden':
				//Recorre la posicion de atributos para asignalos al campo
				foreach ($info['atribute'] as $nameAttr => $valueAttr) {
					$attr .= $nameAttr.'="'.$valueAttr.'" ';
				}

				$field = '<div class="form-group '.$classCol.'" style="display:none;">
							<input type="hidden" class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.$info['name'].'" style="height: auto;"/>
						</div>';
				break;
		}

		//Se retorna el campo
    	return $field;
    }
  
  
  
}

$proceso = new ajax_hojvid_ctxxxx();
 ?>
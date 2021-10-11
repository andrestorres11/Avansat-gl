<?php
    /****************************************************************************
    NOMBRE:   AjaxGeneraActivi
    FUNCION:  Retorna todos los datos necesarios para construir la formularios e
              informacion de actividades a desarrollar
    FECHA DE MODIFICACION: 07/10/2021
    CREADO POR: Ing. Carlos Nieto
    MODIFICADO 
    ****************************************************************************/
    
   /*  ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1); */
    

    class AjaxGeneraActivi
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();

        function __construct($co = null, $us = null, $ca = null)
        {

            //Include Connection class
            @include( "../lib/ajax.inc" );
            include_once('../lib/general/constantes.inc');

            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "1":
                    //crea la tabla
                    self::tableHtml();
                break;
                case "2":
                    //Trae los datos de bd
                    self::GetDataFromTable();
                break;
                case "3":
                    //inserta en bd
                    self::InsertNewTitle();
                break;
                case "4":
                    //trae la consulta de un registro
                    self::GetDataFromOneRecord();
                break;
                case "5":
                    //Edita en bd
                    self::EditTitle();
                break;
                case "6":
                    //cambia el estatus en bd
                    self::ChageStatus();
                break;
            }
        }

      
        function tableHtml(){
            $html='<table class="table table-striped table-bordered table" cellspacing="0" width="100%" id="tabla_inf_general">
                      <thead>
                          <tr>
                              <th colspan="15" style="background-color:#dff0d8; color: #000"><center id="text_general_fec">Tipos de Actividades<center></th> 
                          </tr>
                          <tr>
                              <th>ID</th> 
                              <th>Nombre Tipo</th>
                              <th>Estado</th>
                              <th>Inactivar</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr id="resultado_info_general">
                          </tr>
                      </tbody>
                  </table>';
    
            echo $html;
        }

        function GetDataFromTable(){
             //Create total query 
            $query = "SELECT cod_titulo, des_titulo, if(est_titulo = 1, 'Activo', 'inactivo') as est_titulo FROM ".BASE_DATOS.".tab_activi_titulo
            ORDER BY tab_activi_titulo.des_titulo  ASC ";  

            //Generate consult
            $query = new Consulta($query, self::$conexion);
            $datos = $query -> ret_matrix('a');
            $json = json_encode($datos);
            echo $json;
        }

        function InsertNewTitle() 
        {  
            $sql="INSERT INTO tab_activi_titulo (des_titulo, est_titulo, usr_creaci, fec_creaci) 
            VALUES ('".$_REQUEST['NewformData'][0]['value']."', '1', '".$_SESSION['datos_usuario']['cod_usuari']."', '".date('Y-m-d H:i:s')."')";
            
            $query = new Consulta($sql, self::$conexion);
            if($query){
                $info['status']=200; 
            }else{
                $info['status']=100; 
            } 
            echo json_encode($info);
        }

        function GetDataFromOneRecord(){
            //Create total query 
           $query = "SELECT cod_titulo, des_titulo, if(est_titulo = 1, 'Activo', 'Inactivo') as est_titulo 
           FROM ".BASE_DATOS.".tab_activi_titulo
           WHERE cod_titulo = ".$_REQUEST['cod_titulo']." 
           ORDER BY tab_activi_titulo.des_titulo  ASC ";  

           //Generate consult
           $query = new Consulta($query, self::$conexion);
           $datos = $query -> ret_matrix('a');
           echo $this->EditForm($datos[0],$_REQUEST['cod_titulo']);
        }

        function EditForm($data,$cod_titulo){
            $html='
            <form id="editActivityForm" method="POST">
                <div class="container">
                    <div class="row  mt-3">
                        <div class="col-sm">
                            <div class="form-floating">
                            <h6>Nombre del nuevo tipo Actividad * :</h6>        
                            <input class="form-control" placeholder="Escriba aqui...." id="description" name="description" value="'.$data['des_titulo'].'"></input>
                            </div>
                        </div>
                    </div>
                    <div class="row  mt-3">
                        <div class="col-md-5 offset-md-3">
                            <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="editTitle('.$cod_titulo.')">Editar</button>
                            <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>';
    
            return $html;
        }

        function EditTitle() 
        {  
            $sql="UPDATE tab_activi_titulo 
            SET des_titulo = '".$_REQUEST['NewformData'][0]['value']."', 
            usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
            fec_modifi = '".date('Y-m-d H:i:s')."' 
            WHERE tab_activi_titulo.cod_titulo = ".$_REQUEST['cod_titulo'].";";
            
            $query = new Consulta($sql, self::$conexion);
            if($query){
                $info['status']=200; 
            }else{
                $info['status']=100; 
            } 
            echo json_encode($info);
        }

        function ChageStatus() 
        {  
            $sql="UPDATE tab_activi_titulo 
            SET est_titulo = '".$_REQUEST['status']."', 
            usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
            fec_modifi = '".date('Y-m-d H:i:s')."' 
            WHERE tab_activi_titulo.cod_titulo = ".$_REQUEST['cod_titulo'].";";
            
            $query = new Consulta($sql, self::$conexion);
            if($query){
                $info['status']=200; 
            }else{
                $info['status']=100; 
            } 
            echo json_encode($info);
        }



    }
    new AjaxGeneraActivi();
    
?>
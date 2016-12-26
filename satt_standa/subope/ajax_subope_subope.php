<?php 



/**
* 
*/
class AjaxSubope
{
	var $conexion;
	function __construct($data)
	{

        @include_once( "../lib/ajax.inc" );
        @include_once( "../lib/general/constantes.inc" );

        $this -> conexion = $AjaxConnection;

		switch ($data['op']) {
			case 'switchStatus':
				$this -> switchStatus($data);
			case 'saveSubope':
				$this -> saveSubope($data);
				break;
			default:
				# code...
				break;
		}
	}

	function switchStatus($data){

		$query = "UPDATE ".BASE_DATOS.".tab_operad_subope
					 SET ind_estado = '".$data['estado']."',
					 	 usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
					 	 fec_modifi = NOW()
				   WHERE cod_subope = '".$data['id']."'";
  
        if ($update = new Consulta($query, $this -> conexion)) {
         	echo "ok";
        }else{
        	echo "no";
        }

	}

	function saveSubope($data){

		$query = "INSERT INTO ".BASE_DATOS.".tab_operad_subope
					(
					cod_operac,
					nom_subope,
					ind_estado,
					usr_creaci,
					fec_creaci
					)
				  VALUES
					(
					'".$data['cod_operac']."',
					'".$data['nom_subope']."',
					'1',
					'".$_SESSION['datos_usuario']['cod_usuari']."',
					NOW()
					)";
  		if ($insert = new Consulta($query, $this -> conexion)) {
         	echo "ok";
        }else{
        	echo "no";
        }
	}
}

$class = new AjaxSubope($_REQUEST);

?>
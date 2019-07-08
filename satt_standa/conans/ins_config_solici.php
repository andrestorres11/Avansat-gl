<?php
class ins_config_solici
{	
	var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
    
	function __construct($co, $us, $ca)
	{		
			
		if ($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on') {
			
			//ini_set('display_errors', true);
			//error_reporting(E_ALL);
			$_AJAX = $_REQUEST;
			include_once('../lib/ajax.inc');
			$this -> conexion = $AjaxConnection;
    	$this -> $_AJAX['metodo']( $_AJAX );
		} 
		else {
			$this -> conexion = $co;
		  $this -> usuario = $us;
		  $this -> cod_aplica = $ca;
		  $this -> index();
		}

	  
	  //include('../vista/view_confi_solici.php');
	}
	
	function index(){
	 	 
     $select_tipo = $this->select_tipo();
     //$select_subtipo= $this->select_subtipo($subtipo);
     echo $html="
     	<table>
				<html> 				
				<head>
					<title>Configuracion ANS</title>
					<meta http-equiv='description' content='page description' />
					<meta http-equiv='content-type' content='text/html; charset=utf-8' />
					<meta charset='UTF-8'>
					<link rel='stylesheet' type='text/css' href='../".DIR_APLICA_CENTRAL."/estilos/sweetalert.css'>
					<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/bootstrap_v335.min.css'>
					<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
					<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/bootstrap-datetimepicker.css' />
					<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/check_radio_style.css' />


				</head>
				
					<div class='row'>
						<div class='col-md-12'>
							<div class='panel panel-default' >
					      <div class='panel-heading' > <center><b style='color:black;'>Tipo de solicitud</b></center></div>
					      <div class='panel-body'>
					      	<div class='col-md-6'>
										<div class='form-group'>
											<label>Tipo: </label>
											<select class='form-control' id='select_tipo' onchange='verificar_subtipo()' >
												".$select_tipo."
											</select>					
										</div>
									</div>

									<div class='col-md-6'>
										<div class='form-group'>
											<label clasS='select_subtipo' style='display:none'>Subtipo: </label>
											<select class='form-control select_subtipo' id='cambio_select_subtipo' style='display:none'>
											</select>						
										</div>
									</div>
									
									<div class='col-md-12'>
											<center>
												<botton class='btn btn-default' id='btn-config'>Configurar<botton>
											</center>
									</div>
					      </div>
					    </div>

							<br>
							
							<div class='panel panel-default' id='panel_config' style='display:none;' >
					      <div class='panel-heading'> <center><b style='color:black;'>Configuracion</b></center></div>
					      <div class='panel-body'>

							  	<div class='col-md-6'>
										<div class='form-group'>
				            	<label>Hora final: </label>
				                <div class='input-group date' id='datetimepicker3'>
				                    <input type='text' class='time form-control' id='hora_inicial' value='06:00'/>
				                    <span class='input-group-addon'>
				                    </span>
				                </div>
				            </div>

										<div class='col-xs-6'>
											<div class='form-group'>
												<label>Tiempo en min:</label><br>													
													<label class='checkcontainer'>Min
													  <input type='radio' name='radio' id='min' checked>
													  <span class='radiobtn'></span>
													</label>
													<label class='checkcontainer'>Horas
													  <input type='radio' name='radio' id='horas'>
													  <span class='radiobtn'></span>
													</label>
													<label class='checkcontainer'>Dias
													  <input type='radio' name='radio' id='dias'>
													  <span class='radiobtn'></span>
													</label>
													<br>
													<input type='text' class='form-control' placeholder='Digite el tiempo' id='tiempo_min' size='3'>		
											</div>

										</div>

										<div class='col-xs-6'>

											<div class='form-group'>
										  	<label>Dias: </label><br>

											  	<div class='col-xs-6'>
												  	<label class='container'>
														  <input type='checkbox' id='lunes'>Lunes
														  <span class='checkmark'></span>
														</label>

														<label class='container'>
														  <input type='checkbox' id='martes'>Martes
														  <span class='checkmark'></span>
														</label>

														<label class='container'>
														  <input type='checkbox' id='miercoles'>Miercoles
														  <span class='checkmark'></span>
														</label>

														<label class='container'>
														  <input type='checkbox' id='jueves'>Jueves
														  <span class='checkmark'></span>
														</label>

													</div>
													<div class='col-xs-6'>
														<label class='container'>
														  <input type='checkbox' id='viernes'>Viernes
														  <span class='checkmark'></span>
														</label>

														<label class='container'>
														  <input type='checkbox' id='sabado'>Sabado
														  <span class='checkmark'></span>
														</label>

														<label class='container'>
														  <input type='checkbox' id='domingo'>Domingo
														  <span class='checkmark'></span>
														</label>

														<label class='container'>
														  <input type='checkbox' id='festivo'>Festivo
														  <span class='checkmark'></span>
														</label>

													</div>
												<br>
											</div>	
										</div>
									</div>	

									<div class='col-md-6'>
				            <div class='form-group'>
				            	<label>Hora final: </label>
				                <div class='input-group date' id='datetimepicker3'>
				                    <input type='text' class='time form-control' id='hora_final' value='23:59' />
				                    <span class='input-group-addon'>
				                     
				                    </span>
				                </div>
				            </div>

				            <div class='form-group'>
											<label>Observaciones: </label>
											<textarea rows='5' cols='30' class='form-control' id='observacion'  placeholder='Digite la observacion' ></textarea>
										</div>
									</div>	

									<div class='col-md-12'>
											<center>
												<botton class='btn btn-default' id='btn-aceptar' >Aceptar<botton>
											</center>
									</div>

								</div>
							</div>
						</div>
				  </div>
				  <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/moment.min.js' ></script>
				  <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery24.js' ></script>
				  <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/bootstrap.js' ></script>	
				  <script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/sweetalert-dev.js' ></script>						
					<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/bootstrap-datetimepicker.min.js' ></script>
				
				</html>
			</table>
				";
	}

	
 function select_tipo(){
  	$tipo="";
	 	$query_tab_solici_estado = "SELECT cod_tipsol,nom_tipsol
	             FROM ".BASE_DATOS.".tab_solici_tiposx WHERE cod_tipsol <> 0 ";
	  $consulta_tipo = new Consulta($query_tab_solici_estado, $this -> conexion);
	  $tipo_estado = $consulta_tipo -> ret_matriz();
	 
	 	for($i =0 ; $i <sizeof($tipo_estado) ;$i++)
	 	{
	 		$tipo.= "<option value=".$tipo_estado[$i]['cod_tipsol'].">".$tipo_estado[$i]['nom_tipsol']."</option>";
	 	}
	 	return $tipo;

	  //$select_subtipo = $this->select_subtipo($tipo);
 }

  function select_subtipo($_AJAX){

	 	$query_tab_solici_subtip = "SELECT cod_tipsol,nom_subtip
	           FROM ".BASE_DATOS.".tab_solici_subtip WHERE cod_tipsol='".$_AJAX['parametro']."' ";

	  $consulta_subtipo = new Consulta($query_tab_solici_subtip, $this -> conexion);
	  $subtipo = $consulta_subtipo -> ret_matriz();

	 	$subtipo2="";
	 	for($i =0 ; $i <sizeof($subtipo) ;$i++)
	 	{
	 		$subtipo2.= "<option value=".$subtipo[$i]['cod_tipsol'].">".$subtipo[$i]['nom_subtip']."</option>";
	 	}
	 	echo $subtipo2;
 	
  }
  function verificar_configuracion($_AJAX){

  	$usuario = $_SESSION["datos_usuario"]["cod_usuari"];
  	$consulta_existe_usuario = "SELECT usr_creaci FROM ".BASE_DATOS.".tab_solici_config WHERE usr_creaci ='".$usuario."' ;" ;
  	$consulta_existe_usuario = new Consulta( $consulta_existe_usuario, $this->conexion);
 		$consulta_existe = $consulta_existe_usuario -> ret_matriz();

    if( sizeof( $consulta_existe ) > 0 )
		{
			
	  	$consulta_existe = "SELECT usr_creaci FROM ".BASE_DATOS.".tab_solici_config WHERE cod_tipsol='".$_AJAX['cod_tipsol']."' and cod_subtip='".$_AJAX['cod_subtip']."' and usr_creaci ='".$usuario."' ;" ;
	 		$consulta_existe  = new Consulta( $consulta_existe, $this->conexion);
	 		$existe = $consulta_existe -> ret_matriz();
	    
	    if(sizeof($existe) > 0)
			{
				$consulta_existe_usuario = "SELECT * FROM ".BASE_DATOS.".tab_solici_config WHERE usr_creaci ='".$usuario."' ;" ;
		  	$consulta_existe_usuario = new Consulta( $consulta_existe_usuario, $this->conexion);
		 		$consulta_existe = $consulta_existe_usuario -> ret_matriz();
		 		//$concateno = array();
		 		$concateno = "";
		 		foreach( $consulta_existe as $row )
     		{
	        $concateno.= $row['fec_inicia'].';';
	        $concateno.= $row['fec_finali'].';';
	        $concateno.= $row['dia_calend'].';';
	        $concateno.= $row['tip_tiempo'].';';
	        $concateno.= $row['tie_respue'].';';
	        $concateno.= $row['obs_config'].';';
      	}
      	
      	echo $concateno;
				die();
			}
			else
			{
				echo "0";
				die();
			}
		}
		else
		{
			echo "1";
			die();
		}

 		
  }

 	function insertFormulCampos($_AJAX) {
		
 		//echo $_AJAX['dia_calend'];
 		$usuario = $_SESSION["datos_usuario"]["cod_usuari"];

 		$consulta_existe = "SELECT usr_creaci FROM ".BASE_DATOS.".tab_solici_config WHERE usr_creaci ='".$usuario."' ;" ;

 		$consulta  = new Consulta( $consulta_existe, $this->conexion);
 		$existe = $consulta -> ret_matriz();
    
    if( sizeof( $existe ) > 0 )
		{
			$mUpdate = "UPDATE ".BASE_DATOS.".tab_solici_config SET cod_tipsol='".$_AJAX['cod_tipsol']."' ,cod_subtip='".$_AJAX['cod_subtip']."', fec_inicia = '".$_AJAX['fec_inicia']."',fec_finali = '".$_AJAX['fec_finali']."',dia_calend = '".$_AJAX['dia_calend']."' ,tip_tiempo='".$_AJAX['tip_tiempo']."',tie_respue= '".$_AJAX['tie_respue']."',obs_config= '".$_AJAX['obs_config']."',usr_modifi ='".$usuario."' ,fec_modifi = NOW() ";
			$consulta = new Consulta( $mUpdate, $this -> conexion );
			echo "0";
			die();
		}
		else
		{
			$mInsert = "INSERT INTO ".BASE_DATOS.".tab_solici_config 
						(cod_cofsol,cod_tipsol,cod_subtip,fec_inicia,fec_finali,dia_calend,tip_tiempo,tie_respue,obs_config,usr_creaci,fec_creaci)
					VALUES
						(DEFAULT,'".$_AJAX['cod_tipsol']."','".$_AJAX['cod_subtip']."','".$_AJAX['fec_inicia']."','".$_AJAX['fec_finali']."','".$_AJAX['dia_calend']."','".$_AJAX['tip_tiempo']."','".$_AJAX['tie_respue']."','".$_AJAX['obs_config']."', '".$usuario."',NOW()) ";			
			$consulta = new Consulta( $mInsert, $this->conexion, "R" );

			echo "1";
			die();
		}
	}

}//FIN CLASE
    

  if ($_REQUEST[Ajax] === 'on') 
  {    	
    $proceso = new ins_config_solici();
   
	}
	else
  {
 	  $proceso = new ins_config_solici($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

  }		    
   
?>


<script type="text/javascript">
	
	$( document ).ready(function() {
    

   	$('.time').datetimepicker({
       format: 'HH:mm'
    });
		

	});

	$('.time').keypress(function(event){
     if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
         event.preventDefault(); //stop character from entering input
     }
   });

	$('#tiempo_min').keypress(function(event){
		 var time = $(this).val();
     if(event.which != 8 && isNaN(String.fromCharCode(event.which)) || time.length > 2){
         event.preventDefault(); //stop character from entering input
     }
   });

	function verificar_subtipo(){
		
		var select_tipo = $("#select_tipo").val();
	
		if(select_tipo != 1 && select_tipo != 4)
		{
			$(".select_subtipo").css("display","block")
			var metodo ="select_subtipo";
			var mdata="Ajax=on&metodo="+metodo+"&parametro="+select_tipo;

			$.ajax({
				url:"../satt_standa/conans/ins_config_solici.php",
				data:mdata,
				cache:false,
				type:"post",
				success:function(data){
				
					$("#cambio_select_subtipo").html(data)
				}
			})
		}
		else
		{
			$(".select_subtipo").css("display","none")
		}
	}

	$('#btn-aceptar').click(function(event){
     
		cod_tipsol = $("#select_tipo").val();		
		cod_subtip = $("#cambio_select_subtipo").val();
		fec_inicia = $("#hora_inicial").val();
		fec_finali = $("#hora_final").val();
		dia_calend = [];

		if(document.getElementById('lunes').checked == true)
		{
			var dia= 'lunes';
			dia_calend.push(dia);
		}
		if(document.getElementById('martes').checked == true)
		{
			
			var dia= 'martes';
			dia_calend.push(dia);
		}
		if(document.getElementById('miercoles').checked == true)
		{
			
			var dia= 'miercoles';
			dia_calend.push(dia);	

		}
		if(document.getElementById('jueves').checked == true)
		{
			
			var dia= 'jueves';
			dia_calend.push(dia);
		}
		if(document.getElementById('viernes').checked == true)
		{
			
			var dia= 'viernes';
			dia_calend.push(dia);
		}
		if(document.getElementById('sabado').checked == true)
		{
			
			var dia= 'sabado';
			dia_calend.push(dia);
		}
		if(document.getElementById('domingo').checked == true)
		{
			
			var dia= 'domingo';
			dia_calend.push(dia);
		}
		if(document.getElementById('festivo').checked == true)
		{
			
			var dia= 'festivo';
			 dia_calend.push(dia);
		}

		
		if(document.getElementById('horas').checked == true)
		{
			tip_tiempo = 2;
			tie_respue = $("#tiempo_min").val();
		}
		else if(document.getElementById('dias').checked == true)
		{
			tip_tiempo = 3;
			tie_respue =  $("#tiempo_min").val();
		}
		else
		{
			tip_tiempo = 1;
			tie_respue = $("#tiempo_min").val();
		}

		
		obs_config = $("#observacion").val();
		
		if(tie_respue == "")
		{
			swal("","Debe selecionar un tiempo de respuesta","error")	
		}
		else if(dia_calend == "")
		{
			swal("","Debe selecionar un dia","error")	
		}
		else
		{
			if(cod_subtip == undefined)
			{
				cod_subtip='0'	
			}

			var metodo = "insertFormulCampos"; 
			var parametros = "&cod_tipsol="+cod_tipsol+"&cod_subtip="+cod_subtip+"&fec_inicia="+fec_inicia+"&fec_finali="+fec_finali+"&dia_calend="+dia_calend+"&tip_tiempo="+tip_tiempo+"&tie_respue="+tie_respue+"&obs_config="+obs_config;


			var metodo ="insertFormulCampos";
			var mdata="Ajax=on&metodo="+metodo+parametros;

			$.ajax({
				url:"../satt_standa/conans/ins_config_solici.php",
				data:mdata,
				cache:false,
				type:"post",
				success:function(data){
					if(data  == 1)
					{
						swal("","Se ha ingresado la configuracion correctamente","success")
						$('#panel_config').css("display","none")
					}
					else
					{
						swal("","Se ha actualizado la configuracion correctamente","success")
						$('#panel_config').css("display","none")
					}
				}
			})
		}


   });

  $('#btn-config').click(function(event){

		var metodo ="verificar_configuracion";			
		var cod_tipsol = $("#select_tipo").val();		
		if($("#cambio_select_subtipo").css("display","none"))
		{
			cod_subtip ='0';	
		}
		else{
			var cod_subtip = $("#cambio_select_subtipo").val();
			if(cod_subtip == undefined)
			{
				cod_subtip ='0';	
			}
		}

		var parametros = "&cod_tipsol="+cod_tipsol+"&cod_subtip="+cod_subtip;

		var mdata="Ajax=on&metodo="+metodo+"&parametro="+parametros;
		
		$.ajax({
			url:"../satt_standa/conans/ins_config_solici.php",
			data:mdata,
			cache:false,
			type:"post",
			success:function(data){

				if(data == 0)
				{
					swal({
					  title: "Esta seguro que quiere modificar lo ANS de este tipo ?",
					  text: "",
					  type: "warning",
					  showCancelButton: true,
					  confirmButtonClass: "btn-danger",
					  confirmButtonText: "Si",
					  closeOnConfirm: true
					},
					function(){
						$('#panel_config').css("display","block")
					});
				}
				else if(data == 1)
				{
					$('#panel_config').css("display","block")
				}
				else
				{

					porciones  = data.split(';');
					$("#hora_inicial").val(porciones[0]);
					$("#hora_final").val(porciones[1]);
					dias = porciones[2];
					dia_solo = dias.split(',');

					for(i = 0 ; i<dia_solo.length; i++)
					{						
						if(document.getElementById(dia_solo[i]))
						{
							$("#"+dia_solo[i]).prop('checked',true);
						}	
					}
					
					if(porciones[3] == 2)
					{	
						$("#tiempo_min").val(porciones[4]);
						$("#horas").prop("checked",true)
					}
					else if(porciones[3] == 3)
					{
						$("#tiempo_min").val(porciones[4]);
						$("#dias").prop("checked",true)
					}

					
					$("#observacion").val(porciones[5]);
					$('#panel_config').css("display","block")
				}

			}
		})    
  });
	
</script>
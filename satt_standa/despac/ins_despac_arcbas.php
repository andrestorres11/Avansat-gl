<?php

class Proc_ins_despac_basic {
    
    private $conexion;
    private $cod_usuari;
    private $cod_transp;
    var $field = array();
    var $error = array();
    var $row = array();
    var $insertion = array();
    var $cantidadest = array();

    public function __construct($conexion, $cod_usuari) {
        @include_once('../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc');
        $this->conexion = $conexion;
        $this->cod_usuari = $cod_usuari;
        $this->enrutarPeticion();
    }

    private function enrutarPeticion() {
        switch ($_REQUEST['opcion']) {
        case 'subir':
            $this->preValidator($_REQUEST);
            break;

        default:
            $this->mostrarFormularioImportacion();
        }
    }

    private function agregarEncabezado() {
        echo '
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	        	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	        	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
	         	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
                 <link rel="stylesheet" href="../' . DIR_APLICA_CENTRAL . '/css/tab_genera_estilo.css">
                 <link rel="stylesheet" type="text/css" href="../' . DIR_APLICA_CENTRAL . '/lib/menu/css/datatables.css">
                 <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	        ';
    }

    private function agregarPiePagina($mensaje = '') {
        $scripts = '
                <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
				<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
                <script type="text/javascript" charset="utf8" src="../' . DIR_APLICA_CENTRAL . '/lib/menu/js/datatables.js"></script>
                <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
                <script src="../' . DIR_APLICA_CENTRAL . '/js/tab_regist_pedido.js"></script>
          	';
        if (!empty($mensaje)) {
            $scripts .= '
          			<script>
						mostrarMensaje("' . $mensaje . '");
                	</script>
          		';
        }
        echo $scripts;
    }

    private function generarFormulario() {
        $titulo_tarjeta = "Importar Despachos";
        $form_action = 'index.php?cod_servic=' . $_REQUEST['cod_servic'] . '&amp;window=central&amp;opcion=subir';
        $formulario = '
				<div class="container">
					<div class="row">
						<div class="col">
							<div class="card">
								<div class="card-header">
									' . $titulo_tarjeta . '
								</div>
								<div class="card-body">
									<div class="row">
                                            <div class="col">
												<a href="../' . DIR_APLICA_CENTRAL . '/despac/archiv_plan_basic.xls" target="_blank"><img src="../' . DIR_APLICA_CENTRAL . '/images/excel_logo.png" width="30" height="30" /> Descargar Plantilla</a>
											</div>
											<div class="col">
												<a href="../' . DIR_APLICA_CENTRAL . '/planea/instructivos/Instructivo_de_importacion_de_pedidos_Avansat_GL.pdf" target="_blank"><img src="../' . DIR_APLICA_CENTRAL . '/imagenes/pdf.jpg" width="30" height="30" /> Instructivo</a>
											</div>
									</div>
									<form method="post" action="' . $form_action . '" enctype="multipart/form-data" id="formID">
										<div class="row">
											<div class="col">
												<div class="label-input">Ruta de Archivo CSV:</div>
												<input type="file" name="archivo" id="archivo" size="50" maxlength="255" onchange="ValidateIt( $(this) )"/>
											</div>
										</div>
										<div class="row">
											<div class="col">
												<div style="text-align: center; padding-top: 2em;">
													<button type="button" class="btn btn-secondary btn-sm" onclick="Validator()">Importar</button>
												</div>
											</div>
										</div>
										<div class="row m-4">
											<div class="col" id="errorID">
											</div>
										</div>
									</form>
								</div>
							</dvi>
						</div>
					</div>
				</div>
			';
        return $formulario;
    }

    private function mostrarFormularioImportacion() {
        $this->agregarEncabezado();
        $formulario = $this->generarFormulario();
        echo "
				<table style='width: 100%;'>
					<tr>
						<td>
							<br>
							$formulario
						</td>
					</tr>
				</table>
			";
        $this->agregarPiePagina();
    }
    
    public function fechaHoy() {
        setlocale(LC_ALL, "es_ES");
        return date("Y-m-d H:i:s");
    }

    private function GetFileData() {
        $tipo = $_FILES['archivo']['type'];
        $tamanio = $_FILES['archivo']['size'];
        $archivotmp = $_FILES['archivo']['tmp_name'];

        $filas = file($archivotmp);
        //Calcula el total de las filas del archivo excel
        $size_file = sizeof($filas);
        $_DATA = array();

        for ($f = 0; $f < $size_file; $f++) {
            $info = preg_replace("[\n|\r|\n\r]", "", $filas[$f]);
            $datos = explode(';', $info);
            $tamanio_columna = sizeof($datos);
            for ($c = 0; $c < $tamanio_columna; $c++) {
                $_DATA[$f][$c] = trim($datos[$c]);
            }

        }
        return $_DATA;
    }

    private function preValidator($mData) {
        
        $_DATA = $this->GetFileData();
        $_RESPUESTA = $this->VerifyData($mData, $_DATA);
        $_INFO = $_RESPUESTA[0];
        $_TOTALIMPORTACIONES = $_RESPUESTA[1];
        $_TOTALACTUALIZACIONES = $_RESPUESTA[2];
        $_ACTUALIZACIONES = $_RESPUESTA[3];
        $size_info = sizeof($_INFO);

        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
        $formulario = new Formulario("index.php", "post", "IMPORTAR DESPACHOS", "form\" enctype=\"multipart/form-data\" id=\"formID");
        //MUESTRA LAS NUEVAS IMPORTACIONES
        if ($_TOTALIMPORTACIONES > 0) {
            $mensaje = "Se ha(n) Importado " . $_TOTALIMPORTACIONES . " Despacho(s) de manera exitosa";
            $mens = new mensajes();
            $mens->correcto("IMPORTAR NOVEDADES", $mensaje);
        }

        //MUESTRA LOS DIFERENTES ERRORES
        $mHtml='';

        if ($size_info > 0) {

            $mHtml .= '<table width="100%" cellpadding="0" cellspacing="1">';
            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="6s" style="padding:15px;font-family:Trebuchet MS, Verdana, Arial;font-size:13px;">Los siguientes despachos no fueron insertados</td>';
            $mHtml .= '</tr>';

            $mHtml .= '<tr class="headtable">';
            $mHtml .= '<td class="CellHead headcolumn" align="center">No.</td>';
            $mHtml .= '<td class="CellHead headcolumn" align="center">MANIFIESTO</td>';
            $mHtml .= '<td class="CellHead headcolumn" align="center">LINEA</td>';
            $mHtml .= '<td class="CellHead headcolumn" align="center">COLUMNA</td>';
            $mHtml .= '<td class="CellHead headcolumn" align="center">VALOR</td>';
            $mHtml .= '<td class="CellHead headcolumn" align="center">OBSERVACION</td>';
            $mHtml .= '</tr>';

            for ($j = 0; $j < $size_info; $j++) {
                $class = $j % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
                $mHtml .= '<tr>';
                $mHtml .= '<td class="' . $class . ' bodycolumn" align="center"><b>' . ($j + 1) . '</b></td>';
                $mHtml .= '<td class="' . $class . ' bodycolumn" align="center">' . $_INFO[$j][4] . '</td>';
                $mHtml .= '<td class="' . $class . ' bodycolumn" align="center">' . $_INFO[$j][0] . '</td>';
                $mHtml .= '<td class="' . $class . ' bodycolumn" align="center">' . $_INFO[$j][1] . '</td>';
                $valor = $_INFO[$j][2] ? $_INFO[$j][2] : '( VACIO )';
                $mHtml .= '<td class="' . $class . ' bodycolumn" align="center">' . $valor . '</td>';
                $mHtml .= '<td class="' . $class . ' bodycolumn">' . $_INFO[$j][3] . '</td>';
                $mHtml .= '</tr>';
            }

            $mHtml .= '</table>';
            echo $mHtml;
        }
        

        $formulario->nueva_tabla();
        $formulario->nueva_tabla();
        $formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
        $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
        $formulario->oculto("window\" id=\"windowID\"", 'central', 0);
        $formulario->oculto("cod_servic\" id=\"cod_servicID\"", $mData['cod_servic'], 0);
        $formulario->cerrar();
    }


    public function VerifyData($mData, $_DATA) {
        $this->field = $_DATA[0];
        $totalImportados = 0;
        $totalActualizados = 0;
        $datosActualizados = array();

        $_ERROR = array();
        //$e = conteo de errores totales del archivo
        $e = 0;
        $h = 0;
        for ($r = 1; $r < sizeof($_DATA); $r++) {
            //$er = conteo de errores de la fila al importar pedidos
            $er = 0;
            $this->row = $_DATA[$r];
            //------------------------
            if ($this->row[0] == "" && $this->row[1] == "" && $this->row[2] == "" && $this->row[3] == "" && $this->row[4] == "" && $this->row[5] == "") {continue;}
            
            for ($c = 0; $c < sizeof($this->row); $c++) {   
                //------------------------
                $item = isset($this->row[$c]) ? $this->row[$c] : NULL;
                //------------------------
                switch ($c) {

                    case 0: //Manifiesto
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'EL NUMERO DE MANIFIESTO ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                        break;

                    case 1: //@Nit Empresa
                        if ($item != "" || $item == "") {
                            if (!$this->verificarTransportadora($item)) {

                                $this->SetError($e, $this->row[0], $c, $r, 'LA TRANSPORTADORA '.$this->row[1].' NO ESTA REGISTRADA');
                                $e++;
                                $er++;
                            }
                        }
                        break;

                    case 2: //@Codigo Agencia
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'EL CÓDIGO DE LA AGENCIA ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                        if(!$this->verificarAgencia($item,$this->row[1])){
                            $this->SetError($e, $this->row[0], $c, $r, 'LA AGENCIA '.$this->row[2].' NO ESTA REGISTRADA');
                            $e++;
                            $er++;
                        }
                        break;

                    case 3: 
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'LA FECHA ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                    break;

                    case 4: //Ciudad de origen
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'LA ORIGEN ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                        if($item != ''){

                            $cod_ciudad = $this->reemplazaCiudad($item, $this->row[1]);
                            
                            $cod_ciudes = $this->reemplazaCiudad($this->row[5], $this->row[1]);

                            if (!$this->verificaCiudad($cod_ciudad)) {
                                $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE ORIGEN REGISTRADA CON EL CODIGO '.$item);
                                $e++;
                                $er++;
                            }
                            
                            if(!$this->validaRuta($cod_ciudad, $cod_ciudes)){
                                $ciu_origen = $this->getCiudad($cod_ciudad);
                                $ciu_destin = $this->getCiudad($cod_ciudes);
                                $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE RUTA (ORIGEN:'.$ciu_origen['nom_ciudad'].') - DESTINO('.$ciu_destin['nom_ciudad'].').');
                                $e++;
                                $er++;
                            }
                        }
                    break;

                    case 5: //@Ciudad destino
                        if (empty($item) || $item == "") {

                            $this->SetError($e, $this->row[0], $c, $r, 'LA ORIGEN ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                        if($item != ''){

                            $cod_ciudad = $this->reemplazaCiudad($item, $this->row[1]);
                            $cod_ciuori = $this->reemplazaCiudad($this->row[4], $this->row[1]);

                            if (!$this->verificaCiudad($cod_ciudad)) {
                                $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE DESTINO REGISTRADA CON EL CODIGO '.$item);
                                $e++;
                                $er++;
                            }

                            if(!$this->validaRuta($cod_ciuori, $cod_ciudad)){
                                $ciu_origen = $this->getCiudad($cod_ciuori);
                                $ciu_destin = $this->getCiudad($cod_ciudad);
                                $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE RUTA (ORIGEN:'.$ciu_origen['nom_ciudad'].') - DESTINO('.$ciu_destin['nom_ciudad'].').');
                                $e++;
                                $er++;
                            }
                        }
                    break;

                    case 6: //@Tipo de despacho
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'EL TIPO DE DESPACHO ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                        if (!$this->verificaTipoDespacho($item)) {
                            $this->SetError($e, $this->row[0], $c, $r, 'EL TIPO DE DESPACHO CON CODIGO '.$item.' NO EXISTE');
                            $e++;
                            $er++;
                        }
                    break;

                    case 7: //@Peso
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'EL PESO ES REQUERIDO');
                            $e++;
                            $er++;
                        }
                    break;
                        
                    case 8: //@Placa
                        
                        if (empty($item) || $item == "") {
                            $this->SetError($e, $this->row[0], $c, $r, 'LA PLACA DEL VEHICULO ES REQUERIDA');
                            $e++;
                            $er++;
                        }
                        if (!$this->verificaPlaca($item)) {

                            $this->SetError($e, $this->row[0], $c, $r, 'LA PLACA DEL VEHICULO NO REGISTRADA');
                            $e++;
                            $er++;
                        }
                        break;

                    case 9: //@Observaciones
                    break;

                } //Cierre Case

            } //Cierre For

            //Insercion Si no hay errores
            if ($er == 0) {
                $respuesta = $this->insertDespacho($_DATA[$r]);
                if ($respuesta[0] == 1) {
                    $totalImportados++;
                } else {
                    $totalActualizados++;
                    array_push($datosActualizados, $respuesta[1]);
                }

            }

        } //Cierre Segundo For

        $respuesta = array();
        array_push($respuesta, $this->error);
        array_push($respuesta, $totalImportados);
        array_push($respuesta, $totalActualizados);
        array_push($respuesta, $datosActualizados);

        return $respuesta;

    } //Cierre Funcion

    private function retornarRegistroNuevosValidator() {
        return $this->insertion;
    }



    private function SetError($e, $p, $c, $r, $des) {
        $this->error[$e][0] = $r;
        $this->error[$e][1] = $c != NULL ? $c + 1 : 'REGISTRO';
        $this->error[$e][2] = $c != NULL ? $this->row[$c] : '- - -';
        $this->error[$e][3] = $des;
        $this->error[$e][4] = $p;
    }


  
    function insertDespacho($row) {
        
        $est_import = 1;
        $respuesta = Array();
        $actualizados = Array();
        
        $fec_actual = date("Y-m-d H:i:s");
        //Se formatea la fecha e pago
        $row[3] = str_replace('/', '-', $row[3]);
        $row[3] = date('Y-m-d', strtotime($row[3]));
        
        //Condicional que revisa que el despacho este registrado
        if (!$this->verificaRegistroDespacho($row[0], $row[2])){
            //trae el consecutivo de la tabla
            $query = "SELECT Max(num_despac) AS maximo
            FROM ".BASE_DATOS.".tab_despac_despac ";
            $consec = new Consulta($query, $this -> conexion);
            $ultimo = $consec -> ret_matriz();
            $nuevo_consec =  $ultimo[0][0]+1;

            //Obtencion de datos
            $dat_ciuori = $this->getCiudad($this->reemplazaCiudad($row[4], $row[1]));
            $dat_ciudes = $this->getCiudad($this->reemplazaCiudad($row[5], $row[1]));

            $ruta  = $this->getRuta($dat_ciuori['cod_ciudad'], $dat_ciudes['cod_ciudad']);

            
            $query = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
            (
              num_despac, cod_manifi, fec_despac,
  
              cod_client, cod_paiori, cod_depori,
  
              cod_ciuori, cod_paides, cod_depdes,
  
              cod_ciudes, val_flecon, val_despac,
  
              val_antici, val_retefu, nom_carpag,
  
              nom_despag, cod_agedes, fec_pagoxx,
  
              obs_despac, val_declara, usr_creaci,
  
              fec_creaci, val_pesoxx, con_telef1,
  
              con_telmov, con_domici, cod_tipdes,
  
              gps_operad, gps_usuari, gps_paswor,
  
              gps_idxxxx
  
              
            )
            VALUES 
            (
              ".$nuevo_consec.", '".$row[0]."', '$fec_actual', 
  
              NULL, '".$dat_ciuori['cod_paisxx']."', '".$dat_ciuori['cod_depart']."',
  
              '".$dat_ciuori['cod_ciudad']."', '".$dat_ciudes['cod_paisxx']."', '".$dat_ciudes['cod_depart']."',
  
              '".$dat_ciudes['cod_ciudad']."', NULL, NULL,
  
              NULL, NULL, NULL,
  
              NULL, '".$row[2]."', NULL,
  
              '".$row[9]."', NULL, '".$this->cod_usuari."',
  
              '$fec_actual', '".$row[7]."', NULL,
  
              NULL, NULL, '".$row[6]."',
  
              NULL, NULL, NULL,
  
              NULL
  
            )";
         $consulta = new Consulta($query, $this -> conexion, "BR");

         //traer la info del conductor
        $conduc = $this->obtenerConductor($row[8]);
        

         //query de insercion de despachos vehiculos
        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                        (num_despac, cod_transp, cod_agenci, cod_rutasx,
                        cod_conduc, num_placax, obs_medcom, fec_salipl,
                        fec_llegpl, ind_activo, usr_creaci,
                        fec_creaci, usr_modifi, fec_modifi)
                         VALUES 
                        ('$nuevo_consec','".$row[1]."','".$row[2]."','$ruta',
                        '".$conduc['cod_conduc']."','".$row[8]."','".$row[9]."',
                        '$fec_actual','$fec_actual','R','".$this->cod_usuari."',
                        '$fec_actual','".$this->cod_usuari."','".$fec_actual."') ";
        $consulta = new Consulta($query, $this -> conexion, "RC");
        
         $est_import = 1;
        } else {
            $est_import = 2;
        }

        var_dump($nuevo_consec);

        array_push($respuesta, $est_import);
        array_push($respuesta, $actualizados);
        return $respuesta;
    }

     /************************* FUNCIONES VARIAS **************************************/
    public function retornarBoolean($dato) {
        $var = false;
        if ($dato > 0) {
            $var = true;
        }
        return $var;
    }

    function registrarNull($dato) {
        if ($dato != 0 or $dato != "") {
            return $dato;
        }
        return NULL;
    }

    function retornaFechasVacias($dato) {
        if ($fecha == NULL or $fecha == "") {
            return "0000-00-00";
        }
        return $dato;
    }

    /************************* VERIFICACIONES **************************************/

    function verificarTransportadora($cod) {
        $sql = 'SELECT COUNT(*) FROM ' . BASE_DATOS . '.tab_tercer_emptra a, ' . BASE_DATOS . '.tab_tercer_tercer b WHERE a.cod_tercer = "' . $cod . '" AND b.cod_tercer = "' . $cod . '"';
        $consulta = new Consulta($sql, $this->conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificarAgencia($cod_agenci,$cod_transp) {
        $sql = 'SELECT COUNT(*) FROM ' . BASE_DATOS . '.tab_genera_agenci a
                    INNER JOIN '.BASE_DATOS.'.tab_transp_agenci b ON
                        a.cod_agenci = b.cod_agenci AND b.cod_transp = "'.$cod_transp.'"
                WHERE a.cod_agenci = "'.$cod_agenci.'"';
        $consulta = new Consulta($sql, $this->conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }


    function verificaPlaca($placa){
        $query = "SELECT COUNT(*)
                       FROM " . BASE_DATOS . ".tab_vehicu_vehicu
                       WHERE num_placax = '".$placa."'";
        $consulta = new Consulta($query, $this->conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);              
    }


    function obtenerConductor($num_placax){

        $query = "SELECT cod_conduc
                     FROM ".BASE_DATOS.".tab_vehicu_vehicu
                     WHERE  num_placax = '".$num_placax."'";
        $consulta = new Consulta($query, $this->conexion);

        $consulta = $consulta->ret_matriz();
        return $consulta[0];
    }

    function verificaCiudad($cod_ciudad){
        $query = "SELECT COUNT(*)
                    FROM  ".BASE_DATOS.".tab_genera_ciudad
                    WHERE cod_ciudad = '".$cod_ciudad."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificaTipoDespacho($cod){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_genera_tipdes
                WHERE cod_tipdes = '".$cod."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificaRegistroDespacho($cod_manifi, $cod_agedes){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_despac_despac
                WHERE cod_manifi = '".$cod_manifi."' AND cod_agedes = '".$cod_agedes."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }



    function validaRuta($cod_ciuori,$cod_ciudes){
        $ciu_origen = $this->getCiudad($cod_ciuori);
        $ciu_destin = $this->getCiudad($cod_ciudes);  
        $query = "SELECT COUNT(*)
        FROM ".BASE_DATOS.".tab_genera_rutasx a
        WHERE 
                a.cod_paiori = '".$ciu_origen['cod_paisxx']."' AND
                a.cod_depori = '".$ciu_origen['cod_depart']."' AND
                a.cod_ciuori = '".$ciu_origen['cod_ciudad']."' AND
                a.cod_paides = '".$ciu_destin['cod_paisxx']."' AND
                a.cod_depdes = '".$ciu_destin['cod_depart']."' AND
                a.cod_ciudes = '".$ciu_destin['cod_ciudad']."' AND
                a.ind_estado = '1'
        GROUP BY a.cod_rutasx  ";
        
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);   
    }


  

    /************************* OBTENER INFORMACION DB **************************************/
    
    function getCiudad($cod_ciudad){
        $query = "SELECT cod_paisxx, cod_depart, cod_ciudad,
                         nom_ciudad, abr_ciudad
                    FROM  ".BASE_DATOS.".tab_genera_ciudad
                    WHERE cod_ciudad = '".$cod_ciudad."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $consulta[0];
    }

    function getRuta($cod_ciuori,$cod_ciudes){
      $ciu_origen = $this->getCiudad($cod_ciuori);
      $ciu_destin = $this->getCiudad($cod_ciudes);  
        //Trae la ruta del despacho
      $query = "SELECT a.cod_rutasx
      FROM ".BASE_DATOS.".tab_genera_rutasx a
      WHERE 
            a.cod_paiori = '".$ciu_origen['cod_paisxx']."' AND
            a.cod_depori = '".$ciu_origen['cod_depart']."' AND
            a.cod_ciuori = '".$ciu_origen['cod_ciudad']."' AND
            a.cod_paides = '".$ciu_destin['cod_paisxx']."' AND
            a.cod_depdes = '".$ciu_destin['cod_depart']."' AND
            a.cod_ciudes = '".$ciu_destin['cod_ciudad']."' AND
            a.ind_estado = '1'
     GROUP BY a.cod_rutasx  ";
     $consulta = new Consulta($query, $this -> conexion);
     if($ruta = $consulta -> ret_arreglo()){
        $ruta = $ruta[0];
     }
     return $ruta;   
    }



    function reemplazaCiudad($cod_ciudad, $nit_empres){
        //Ajusta el codigo de la ciudad si la empresa es orion.
        if($nit_empres=='800047876' || $nit_empres=='830076669'){
            $code = $cod_ciudad;
            $code = substr($code, 3);
            $fin = ltrim($code, 0);
            $cod_ciudad = $fin.'000';
        }
        return $cod_ciudad;
    }



}

new Proc_ins_despac_basic($this->conexion, $_SESSION['usuario_aplicacion']->cod_usuari);
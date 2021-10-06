<?php
class Proc_ins_despac {
    
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
                <script src="../' . DIR_APLICA_CENTRAL . '/js/imp_pedido_dividi.js"></script>
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
												<a href="../' . DIR_APLICA_CENTRAL . '/planea/plantilla/FORMATO_IMP_PEDIDOS_DEMO.csv" target="_blank"><img src="../' . DIR_APLICA_CENTRAL . '/images/excel_logo.png" width="30" height="30" /> Descargar Plantilla Demo</a>
											</div>
                                            <div class="col">
												<a href="../' . DIR_APLICA_CENTRAL . '/planea/plantilla/FORMATO_IMP_PEDIDOS__V1.csv" target="_blank"><img src="../' . DIR_APLICA_CENTRAL . '/images/excel_logo.png" width="30" height="30" /> Descargar Plantilla</a>
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
            $datos = explode(';', $filas[$f]);
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
            $mHtml .= '<td class="CellHead headcolumn" align="center">OBSERVACIÓN</td>';
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
        
        //INFORMACION DE REGISTROS NUEVOS
        $registrosI = $this->retornarRegistroNuevosValidator();
        $c_nuevos_registros = count($registrosI);
        if ($c_nuevos_registros > 0) {

            $tableRegister = '<table width="60%" cellpadding="0" cellspacing="1">';
            $tableRegister .= '<tr>';
            $tableRegister .= '<td colspan="5" style="padding:15px;font-family:Trebuchet MS, Verdana, Arial;font-size:13px;">Se han realizado ' . $c_nuevos_registros . ' nuevos registros.</td>';
            $tableRegister .= '</tr>';
            $tableRegister .= '<tr>';
            $tableRegister .= '<td class="CellHead" align="center">No.</td>';
            $tableRegister .= '<td class="CellHead" align="center">LINEA</td>';
            $tableRegister .= '<td class="CellHead" align="center">COLUMNA</td>';
            $tableRegister .= '<td class="CellHead" align="center">VALOR</td>';
            $tableRegister .= '<td class="CellHead" align="center">OBSERVACIÓN</td>';
            $tableRegister .= '</tr>';
            for ($i = 0; $i < $c_nuevos_registros; $i++) {
                $class = $i % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
                $tableRegister .= '<tr>';
                $tableRegister .= '<td class="' . $class . '" align="center"><b>' . ($i + 1) . '</b></td>';
                $tableRegister .= '<td class="' . $class . '" align="center">' . $registrosI[$i][0] . '</td>';
                $tableRegister .= '<td class="' . $class . '" align="center">' . $registrosI[$i][1] . '</td>';
                $tableRegister .= '<td class="' . $class . '">' . $registrosI[$i][2] . '</td>';
                $tableRegister .= '<td class="' . $class . '">' . $registrosI[$i][3] . '</td>';
                $tableRegister .= '</tr>';
            }
            $mHtml .= '</table></div>';
            echo $tableRegister;
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
                            if ($this->row[1] != NULL && $this->row[2] != NULL) {
                                $a = Array();
                                $a[0] = Array(
                                    "cod_tercer" => $item, //nit transportadora
                                    "nom_tercer" => $this->row[2], //transportadora
                                    "abr_tercer" => $this->row[2], //transportadora
                                );
                                $this->registrarTransportadora($a);
                                $this->setInsertion($h, $r, $c, $item, 'NUEVA TRANSPORTADORA REGISTRADA');
                                $h++;
                            } else {
                                $faltantes = '';
                                $faltantes .= ($item == NULL) ? " Nit de la Transportadora," : ""; //nit transportadora
                                $faltantes .= ($this->row[2] == NULL) ? " Nombre de la Transportadora," : ""; //transportadora
                                $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DE LA TRANSPORTADORA (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                                $e++;
                                $er++;
                            }
                        }
                    }
                    break;

                case 2: //@Nombre empresa (Razon social)
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DE LA TRANSPORTADORA ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 3: //@Codigo Agencia
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CÓDIGO DE LA AGENCIA ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificarAgencia($item,$this->row[1])){
                        $this->SetError($e, $this->row[0], $c, $r, 'LA AGENCIA '.$this->row[4].' NO ESTA REGISTRADA');
                        $e++;
                        $er++;
                    }
                    break;
                case 4: //@Nombre Agencia
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DE LA AGENCIA ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 5: //@Nit Cliente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NIT DEL CLIENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    $num_cliente = $item;
                    if (!$this->verificarRegistroCliente($item,$num_cliente)) {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CLIENTE ' .  $this->row[6] . ' NO SE ENCUENTRA REGISTRADO');
                        $e++;
                        $er++;
                    }
                    break;
                case 6: //@Nombre Cliene
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DEL CLIENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 7: //@Placa
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA PLACA DEL VEHICULO ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    if (!$this->verificaPlaca($item)) {
                        if(
                            $item != '' && $this->row[8] != '' && $this->row[9] != '' &&
                            $this->row[10] != '' && $this->row[11] != '' && $this->row[12] != '' &&
                            $this->row[13] != '' && $this->row[31] != '' && $this->row[25] != '' &&
                            $this->row[19] != ''
                        ){
                            $a = Array();
                            $a[0] = Array(
                                "num_placax" => $item, //Numero de placa
                                "cod_marcax" => $this->row[8], //Codigo de la marca
                                "cod_lineax" => $this->row[9], //Codigo de la linea
                                "ano_modelo" => $this->row[10], //Año modelo
                                "cod_colorx" => $this->row[11], //Codigo del color
                                "cod_carroc" => $this->row[12], //Codigo de la carroceria
                                "num_config" => $this->row[13], //Numero de configuracion
                                "cod_propie" => $this->row[31], //Codigo del propietario
                                "cod_tenedo" => $this->row[25], //Codigo del tenedor
                                "cod_conduc" => $this->row[19], //Codigo del conductor
                                "cod_opegps" => $this->row[14], //Codigo operador gps
                                "usr_gpsxxx" => $this->row[16], //Usuario gps
                                "clv_gpsxxx" => $this->row[17], //Clave gps
                                "idx_gpsxxx" => $this->row[18] //ID gps
                            );
                            $this->ins_vehiculo($a);
                            $this->setInsertion($h, $r, $c, $item, 'NUEVO VEHICULO REGISTRADO');
                            $h++;
                        }else {
                            $faltantes = '';
                            $faltantes .= ($item == NULL) ? " Numero de placa," : "";
                            $faltantes .= ($this->row[8] == NULL) ? " codigo de la marca," : "";
                            $faltantes .= ($this->row[9] == NULL) ? " codigo de la linea " : "";
                            $faltantes .= ($this->row[10] == NULL) ? " modelo," : "";
                            $faltantes .= ($this->row[11] == NULL) ? " codigo del color " : "";
                            $faltantes .= ($this->row[12] == NULL) ? " codigo de la carroceria " : "";
                            $faltantes .= ($this->row[13] == NULL) ? " numero de configuracion " : "";
                            $faltantes .= ($this->row[36] == NULL) ? " codigo del propietario " : "";
                            $faltantes .= ($this->row[31] == NULL) ? " codigo del poseedor " : "";
                            $faltantes .= ($this->row[19] == NULL) ? " codigo del conductor " : "";
                            $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DEL PROPIETARIO (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                            $e++;
                            $er++;
                        }
                    }
                    break;

                case 8: //@Marca
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA MARCA ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificaMarca($item)){
                        $this->SetError($e, $this->row[0], $c, $r, 'LA MARCA NO EXISTE, O NO ESTA REGISTRADA.');
                        $e++;
                        $er++;
                    }
                    break;

                case 9: //@Linea
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA LINEA ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificaLinea($item, $this->row[8])){
                        $this->SetError($e, $this->row[0], $c, $r, 'LA LINEA NO EXISTE, O NO ESTA REGISTRADA.');
                        $e++;
                        $er++;
                    }
                    break;

                case 10: //Modelo
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL MODELO DEL VEHICULO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 11: //@Color
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL COLOR DEL VEHICULO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificaColor($item)){
                        $this->SetError($e, $this->row[0], $c, $r, 'EL COLOR NO EXISTE, O NO ESTA REGISTRADA.');
                        $e++;
                        $er++;
                    }
                    break;

                case 12: //@Carroceria
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA CARROCERIA DEL VEHICULO ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificaCarroc($item)){
                        $this->SetError($e, $this->row[0], $c, $r, 'LA CARROCERIA NO EXISTE, O NO ESTA REGISTRADA.');
                        $e++;
                        $er++;
                    }
                    break;

                case 13: //@Configuracion
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CODIGO DE CONFIGURACION DEL VEHICULO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 14: //@Nit Gps
                    break;

                case 15: //@Nombre GPS
                    
                    break;

                case 16: //@Usuario

                    break;

                case 17: //@Contraseña
                    
                    break;

                case 18: //@ID GPS
                    
                    break;

                case 19: //@Codigo Conductor
                    if (empty($item) || $item == "" ) {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CODIGO DEL CONDUCTOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificarTercero($item,'tab_tercer_conduc',1)){
                        // Codigo Conductor, Nombre conductor, Primer apellido, Celular conductor, Direccion Conductor
                        if($item != '' && $this->row[20] != '' && $this->row[21] != '' && $this->row[23] != '' && $this->row[24] != '' ){
                            $a = Array();
                            $a[0] = Array(
                                "cod_tercer" => $item, //Codigo conductor
                                "nom_tercer" => $this->row[20], //Nombre del conductor
                                "nom_apell1" => $this->row[21], //Apellido 1
                                "nom_apell2" => $this->row[22], //Apellido 2
                                "num_telmov" => $this->row[23], //Telefono
                                "dir_domici" => $this->row[24], //Direccion
                            );
                            $this->ins_conductor($a);
                            $this->setInsertion($h, $r, $c, $item, 'NUEVO CONDUCTOR REGISTRADO');
                            $h++;
                        }else{
                            $faltantes = '';
                            $faltantes .= ($item == NULL) ? " Codigo conductor," : "";
                            $faltantes .= ($this->row[20] == NULL) ? " Nombre del conductor," : "";
                            $faltantes .= ($this->row[21] == NULL) ? " apellido del conductor, " : "";
                            $faltantes .= ($this->row[23] == NULL) ? " telefono del conductor," : "";
                            $faltantes .= ($this->row[24] == NULL) ? " direccion del conductor " : "";

                            $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DEL CONDUCTOR (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                            $e++;
                            $er++;
                        }
                    }

                    break;

                case 20: //@Nombre conductor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DEL CONDUCTOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    
                    break;

                case 21: //@primer apellido
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL PRIMER APELLIDO DEL CONDUCTOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 22: //@segundo apellido

                    break;

                case 23: //@celular conductor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CELULAR DEL CONDUCTOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 24: //@Direccion Conductor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA DIRECCION DEL CONDUCTOR ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    break;

                case 25: //@Codigo Poseedor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CODIGO DEL POSEEDOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificarTercero($item,'',0)){
                        // Codigo Tercero, Nombre poseedor, Primer apellido, Celular poseedor, Direccion poseedor
                        if($item != '' && $this->row[26] != '' && $this->row[27] != '' && $this->row[29] != '' && $this->row[30] != '' ){
                            $a = Array();
                            $a[0] = Array(
                                "cod_tercer" => $item, //Codigo poseedor
                                "nom_tercer" => $this->row[26], //Nombre del poseedor
                                "nom_apell1" => $this->row[27], //Apellido 1
                                "nom_apell2" => $this->row[28], //Apellido 2
                                "num_telmov" => $this->row[29], //Telefono
                                "dir_domici" => $this->row[30], //Direccion
                            );
                            $this->ins_tercer($a);
                            $this->setInsertion($h, $r, $c, $item, 'NUEVO POSEEDOR REGISTRADO');
                            $h++;
                        }else{
                            $faltantes = '';
                            $faltantes .= ($item == NULL) ? " Codigo poseedor," : "";
                            $faltantes .= ($this->row[26] == NULL) ? " Nombre del poseedor," : "";
                            $faltantes .= ($this->row[27] == NULL) ? " apellido del poseedor " : "";
                            $faltantes .= ($this->row[29] == NULL) ? " telefono del poseedor," : "";
                            $faltantes .= ($this->row[30] == NULL) ? " direccion del poseedor " : "";

                            $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DEL POSEEDOR (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                            $e++;
                            $er++;
                        }
                    }
                    break;

                case 26: //@Nombre poseedor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DEL POSEEOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    
                    break;

                case 27: //@Apellido poseedor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL APELLIDO DEL POSEEOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 28: //@Apellido poseedor
                    break;

                case 29: //@telefono poseedor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL TELEFONO DEL POSEEOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 30: //@Direccion poseedor
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA DIRECCION DEL POSEEOR ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 31: //@Codigo Propietario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CODIGO DEL PROPIETARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->verificarTercero($item,'',0)){
                        // Codigo Tercero, Nombre propietario, Primer apellido, Celular propietario, Direccion propietario
                        if($item != '' && $this->row[32] != '' && $this->row[33] != '' && $this->row[35] != '' && $this->row[36] != '' ){
                            $a = Array();
                            $a[0] = Array(
                                "cod_tercer" => $item, //Codigo propietario
                                "nom_tercer" => $this->row[32], //Nombre del propietario
                                "nom_apell1" => $this->row[33], //Apellido 1
                                "nom_apell2" => $this->row[34], //Apellido 2
                                "num_telmov" => $this->row[35], //Telefono
                                "dir_domici" => $this->row[36], //Direccion
                            );
                            $this->ins_tercer($a);
                            $this->setInsertion($h, $r, $c, $item, 'NUEVO PROPIETARIO REGISTRADO');
                            $h++;
                        }else {
                            $faltantes = '';
                            $faltantes .= ($item == NULL) ? " Codigo propietario," : "";
                            $faltantes .= ($this->row[32] == NULL) ? " Nombre del propietario," : "";
                            $faltantes .= ($this->row[33] == NULL) ? " apellido del propietario " : "";
                            $faltantes .= ($this->row[35] == NULL) ? " telefono del propietario," : "";
                            $faltantes .= ($this->row[36] == NULL) ? " direccion del propietario " : "";

                            $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DEL PROPIETARIO (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                            $e++;
                            $er++;
                        }
                    }
                    break;

                case 32: //@Nombre propietario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DEL PROPIETARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 33: //@Apellido propietario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL APELLIDO DEL PROPIETARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 34: //@Apellido propietario
                    break;

                case 35: //@Telefono propietario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL TELEFONO DEL PROPIETARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 36: //@Direccion propietario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA DIRECCION DEL PROPIETARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;

                case 37: //@Codigo mercancia
                    if (empty($item) || $item == "") {
                        if (!$this->verificarMercancia($item)) {
                            $this->SetError($e, $this->row[0], $c, $r, 'NO HAY MERCANCIA REGISTRADA CON EL CODIGO '.$item);
                            $e++;
                            $er++;
                        }
                    }
                    break;

                case 38: //Ciudad de origen
                    if (empty($item) || $item == "") {
                        if (!$this->verificaCiudad($item)) {
                            $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE CIUDAD REGISTRADA CON EL CODIGO '.$item);
                            $e++;
                            $er++;
                        }
                    }
                    if($item != '' && $row[39] != ''){
                        if(!$this->validaRuta($item, $row[39])){
                            $ciu_origen = $this->getCiudad($item);
                            $ciu_destin = $this->getCiudad($row[39]);
                            $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE RUTA '.$ciu_origen['nom_ciudad'].' - '.$ciu_destin['nom_ciudad']);
                            $e++;
                            $er++;
                        }
                    }
                    break;
                case 39: //@Ciudad destino
                    if (empty($item) || $item == "") {
                        if (!$this->verificaCiudad($item)) {
                            $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE CIUDAD REGISTRADA CON EL CODIGO '.$item);
                            $e++;
                            $er++;
                        }
                    }
                    if($item != '' && $row[38] != ''){
                        if(!$this->validaRuta($row[38], $item)){
                            $ciu_origen = $this->getCiudad($row[38]);
                            $ciu_destin = $this->getCiudad($item);
                            $this->SetError($e, $this->row[0], $c, $r, 'NO EXISTE RUTA '.$ciu_origen['nom_ciudad'].' - '.$ciu_destin['nom_ciudad']);
                            $e++;
                            $er++;
                        }
                    }
                    break;
                case 40: //@Tipo de despacho
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
                case 41: //@Valor Flete
                    break;
                case 42: //@Valor Despacho
                    break;
                case 43: //@Valor Anticipo
                    break;
                case 44: //@Codigo del remitente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CODIGO DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->validaRemdes($item, $row[45], 1)){
                        // Codigo Remdes, Nit, Nombre, Ciudad, Direccion, Correo, Telefono, transportadora
                        if($item != '' && $this->row[45] != '' && $this->row[46] != '' && $this->row[47] != '' && $this->row[48] != '' && $this->row[51] != '' && $this->row[52] != '' && $this->row[1] != ''){
                            $a = Array();
                            $a[0] = Array(
                                "cod_remdes" => $item, //Codigo remdes
                                "num_remdes" => $this->row[45], //numero remdes
                                "nom_remdes" => $this->row[46], //Nombre remdes
                                "cod_transp" => $this->row[1], //Codigo transportadora
                                "ind_remdes" => 1, //Indicador de remdes
                                "cod_ciudad" => $this->row[47], //Codigo de ciudad
                                "dir_remdes" => $this->row[48], //Direccion de remdes
                                "dir_emailx" => $this->row[51], //Email de remitente
                                "num_telefo" => $this->row[52], //Numero de telefono
                                "cod_latitu" => $this->row[49], //Codigo de latitud
                                "cod_longit" => $this->row[50], //Codigo de longitud
                                "num_telefo" => $this->row[61], //Numero de telefono
                            );
                            $this->ins_remdes($a);
                            $this->setInsertion($h, $r, $c, $item, 'NUEVO REMITENTE REGISTRADO');
                            $h++;
                        }else {
                            $faltantes = '';
                            $faltantes .= ($item == NULL) ? " Codigo del remitente," : "";
                            $faltantes .= ($this->row[45] == NULL) ? " Numero del remitente," : "";
                            $faltantes .= ($this->row[46] == NULL) ? " Nombre de remitente, " : "";
                            $faltantes .= ($this->row[1] == NULL) ? " Codigo transportadora," : "";
                            $faltantes .= ($this->row[47] == NULL) ? " Codigo de la ciudad del remitente," : "";
                            $faltantes .= ($this->row[48] == NULL) ? " Direccion del remitente," : "";
                            $faltantes .= ($this->row[51] == NULL) ? " Email remitente, " : "";
                            $faltantes .= ($this->row[52] == NULL) ? " Numero de telefono remitente, " : "";
                            $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DEL REMITENTE (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                            $e++;
                            $er++;
                        }
                    }
                    break;
                case 45: //@Nit remitente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NIT DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 46: //@Nombre del remitente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 47: //@Ciudad del remitente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA CIUDAD DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 48: //@Direccion del remitente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA DIRECCION DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 49: //@Latitud
                    break;
                case 50: //@Longitud
                    break;
                case 51: //@Correo del remitente
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CORREO DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 52: //@Telefono
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL TELEFONO DEL REMITENTE ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 53: //@Codigo del destinatario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CODIGO DEL DESTINATARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    if(!$this->validaRemdes($item, $row[54], 2)){
                        // Codigo Remdes, Nit, Nombre, Ciudad, Direccion, Correo, Telefono, transportadora
                        if($item != '' && $this->row[54] != '' && $this->row[55] != '' && $this->row[56] != '' && $this->row[57] != '' && $this->row[60] != '' && $this->row[61] != '' && $this->row[1] != ''){
                            $a = Array();
                            $a[0] = Array(
                                "cod_remdes" => $item, //Codigo remdes
                                "num_remdes" => $this->row[54], //numero remdes
                                "nom_remdes" => $this->row[55], //Nombre remdes
                                "cod_transp" => $this->row[1], //Codigo transportadora
                                "ind_remdes" => 2, //Indicador de remdes
                                "cod_ciudad" => $this->row[56], //Codigo de ciudad
                                "dir_remdes" => $this->row[57], //Direccion de remdes
                                "dir_emailx" => $this->row[60], //Email de remitente
                                "num_telefo" => $this->row[61], //Numero de telefono
                                "cod_latitu" => $this->row[58], //Codigo de latitud
                                "cod_longit" => $this->row[59], //Codigo de longitud
                                "num_telefo" => $this->row[61], //Numero de telefono
                            );
                            $this->ins_remdes($a);
                            $this->setInsertion($h, $r, $c, $item, 'NUEVO DESTINATARIO REGISTRADO');
                            $h++;
                        }else {
                            $faltantes = '';
                            $faltantes .= ($item == NULL) ? " Codigo del destinatario," : "";
                            $faltantes .= ($this->row[45] == NULL) ? " Numero del destinatario," : "";
                            $faltantes .= ($this->row[46] == NULL) ? " Nombre de destinatario, " : "";
                            $faltantes .= ($this->row[1] == NULL) ? " Codigo transportadora," : "";
                            $faltantes .= ($this->row[56] == NULL) ? " Codigo de la ciudad del destinatario," : "";
                            $faltantes .= ($this->row[57] == NULL) ? " Direccion del destinatario," : "";
                            $faltantes .= ($this->row[60] == NULL) ? " Email destinatario, " : "";
                            $faltantes .= ($this->row[61] == NULL) ? " Numero de telefono destinatario, " : "";
                            $this->SetError($e, $this->row[0], $c, $r, 'HACE FALTA INFORMACION DEL REMITENTE (' . $faltantes . ') VERIFIQUE QUE LA INFORMACION ESTE COMPLETA, PARA REALIZAR SU REGISTRO');
                            $e++;
                            $er++;
                        }
                    }
                    break;


                case 54: //@Nit destinatario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NIT DEL DESTINATARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 55: //@Nombre del destinatario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NOMBRE DEL DESTINATARIO ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 56: //@Ciudad del destinatario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA CIUDAD DEL DESTINATARIO ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    break;
                case 57: //@Direccion del destinatario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'LA DIRECCION DEL DESTINATARIO ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    break;
                case 58: //@Latitud
                    break;
                case 59: //@Longitud
                    break;
                case 60: //@Correo del destinatario
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL CORREO DEL DESTINATARIO ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    break;
                case 61: //@Telefono
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL TELEFONO DEL DESTINATARIO ES REQUERIDA');
                        $e++;
                        $er++;
                    }
                    break;
                case 62: //@fecha de pago
                    break;
                case 63: //@Codigo de unidad de medida
                    break;
                case 64: //@Codigo Naturaleza Empaque
                    break;
                case 65: //@Peso Remesa
                    break;
                case 66: //@Volumen Remesa
                    break;
                case 67: //@Codigo de Empaque de la Remesa
                    break;
                case 68: //@Codigo de Empaque de la Remesa
                    break;
                case 69: //@Numero Remesa
                    if (empty($item) || $item == "") {
                        $this->SetError($e, $this->row[0], $c, $r, 'EL NUMERO DE REMESA ES REQUERIDO');
                        $e++;
                        $er++;
                    }
                    break;
                case 70: //@Observaciones Generales
                    break;

                } //Cierre Case

            } //Cierre For

            //Insercion Si no hay errores
            if ($er == 0) {
                $respuesta = $this->insertDespacho($this->row);
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



    private function setInsertion($e, $r, $c, $valor, $des) {
        $this->insertion[$e][0] = $r; //fila
        $this->insertion[$e][1] = $c;
        $this->insertion[$e][2] = $valor != NULL ? $valor : '- - -';
        $this->insertion[$e][3] = $des;
    }

  
    function insertDespacho($row) {
        $est_import = 1;
        $respuesta = Array();
        $actualizados = Array();
        
        $fec_actual = date("Y-m-d H:i:s");
        //Se formatea la fecha e pago
        $row[62] = str_replace('/', '-', $row[62]);
        $row[62] = date('Y-m-d', strtotime($row[62]));

        //Condicional que revisa que el despacho este registrado
        if (!$this->verificaRegistroDespacho($row[0])){
            //trae el consecutivo de la tabla
            $query = "SELECT Max(num_despac) AS maximo
            FROM ".BASE_DATOS.".tab_despac_despac ";
            $consec = new Consulta($query, $this -> conexion);
            $ultimo = $consec -> ret_matriz();
            $nuevo_consec =  $ultimo[0][0]+1;

            //Obtencion de datos
            $dat_ciuori = $this->getCiudad($row[38]);
            $dat_ciudes = $this->getCiudad($row[39]);

            $ruta  = $this->getRuta($dat_ciuori['cod_ciudad'], $dat_ciudes['cod_ciudad']);

            $mercancia = $this->getMercancia($row[37]);
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
  
              ".$row[5].", '".$dat_ciuori['cod_paisxx']."', '".$dat_ciuori['cod_depart']."',
  
              '".$dat_ciuori['cod_ciudad']."', '".$dat_ciudes['cod_paisxx']."', '".$dat_ciudes['cod_depart']."',
  
              '".$dat_ciudes['cod_ciudad']."', '".$row[41]."', '".$row[42]."',
  
              '".$row[43]."', NULL, NULL,
  
              NULL, '".$row[3]."', NULL,
  
              '".$row[70]."', NULL, '".$this->cod_usuari."',
  
              '$fec_actual', NULL, '".$row[23]."',
  
              '".$row[23]."', '".$row[24]."', '".$row[40]."',
  
              '".$row[15]."', '".$row[16]."', '".$row[17]."',
  
              '".$row[18]."'
  
            )";
         $consulta = new Consulta($query, $this -> conexion, "BR");

         //query de insercion de despachos vehiculos
        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
                        (num_despac, cod_transp, cod_agenci, cod_rutasx,
                        cod_conduc, num_placax, obs_medcom, fec_salipl,
                        fec_llegpl, ind_activo, usr_creaci,
                        fec_creaci, usr_modifi, fec_modifi)
                         VALUES 
                        ('$nuevo_consec','".$row[1]."','".$row[3]."','$ruta',
                        '".$row[19]."','".$row[7]."','".$row[23]."',
                        '$fec_actual','$fec_actual','R','".$this->cod_usuari."',
                        '$fec_actual','".$this->cod_usuari."','".$fec_actual."') ";
        $consulta = new Consulta($query, $this -> conexion, "R");
        
        $query = 'INSERT INTO ' . BASE_DATOS . '.tab_despac_destin(
                        num_despac, num_docume, ped_remisi,
                        cod_remdes, cod_genera, nom_genera,
                        nom_destin, cod_ciudad, dir_destin, 
                        usr_modifi,fec_modifi,fec_citdes,
                        hor_citdes
                        ) VALUES (
                        "' . $nuevo_consec . '", "' . $row[5] . '", "' . $row[69] . '",
                        "' . $row[53] . '", "' . $row[1] . '", "' . $row[2] . '",
                        "' . $row[54] . '", "' . $row[56] . '", "' . $row[57] . '",
                        "' . $this->cod_usuari . '",NOW(), NULL, NULL
                    )';

        $consulta = new Consulta($query, $this -> conexion, "R");

        //query de insercion de remesas
         $query = "INSERT INTO ".BASE_DATOS.".tab_despac_remesa
                    (num_despac, cod_remesa, fec_estent,
                     val_pesoxx, val_volume, des_mercan,
                     abr_client, abr_remite, abr_destin,
                     ema_client, num_docume, usr_creaci,
                     fec_creaci)
                        VALUES 
                    ('$nuevo_consec','".$row[52]."', NULL,
                     '".$row[65]."', '".$row[66]."', '".$mercancia['des_comerc']."',
                     '".$row[6]."', '".$row[46]."', '".$row[56]."',
                     NULL, '".$row[5]."', '".$this->cod_usuari."',
                     '".$fec_actual."')
                    ";
         $consulta = new Consulta($query, $this -> conexion, "RC");
         $est_import = 1;
        } else {
            $est_import = 2;
        }

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

    function verificarRegistroCliente($nit_cliente,$cod_cliente) {
        $sql = 'SELECT COUNT(*) FROM ' . BASE_DATOS . '.tab_genera_remdes a 
                    WHERE a.num_remdes = "' . $nit_cliente . '"';
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

    /*ind = 1: si es conductor, otro para poseedor*/
    function verificarTercero($cod_tercer,$nom_tablex,$ind){
        if($ind==1){
        $query = "SELECT COUNT(*)
                  FROM " . BASE_DATOS . ".".$nom_tablex." a
            INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer b
                ON a.cod_tercer = b.cod_tercer
            WHERE a.cod_tercer = '".$cod_tercer."'";
        }else{
            $query = "SELECT COUNT(*)
                FROM " . BASE_DATOS . ".tab_tercer_tercer a
            WHERE a.cod_tercer = '".$cod_tercer."'";
        }
        $consulta = new Consulta($query, $this->conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]); 
    }

    function verificarMercancia($cod){
        $query = "SELECT COUNT(*)
                     FROM ".BASE_DATOS.".tab_genera_produc
                     WHERE  cod_produc = '".$cod."'";
        $consulta = new Consulta($query, $this->conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
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

    function verificaRegistroDespacho($cod_manifi){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_despac_despac
                WHERE cod_manifi = '".$cod_manifi."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificaMarca($cod_marcax){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_genera_marcas
                WHERE cod_marcax = '".$cod_marcax."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificaLinea($cod_lineax, $cod_marcax){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_vehige_lineas
                WHERE cod_marcax = '".$cod_marcax."' AND cod_lineax = '".$cod_lineax."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificaColor($cod_colorx){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_vehige_colore
                WHERE cod_colorx = '".$cod_colorx."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function verificaCarroc($cod_carroc){
        $query = "SELECT COUNT(*)
                FROM ".BASE_DATOS.".tab_vehige_carroc
                WHERE cod_carroc = '".$cod_carroc."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }

    function validaRuta($ciu_origen,$ciu_destin){
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

    function validaRemdes($cod_remdes, $num_remdes, $indicador){
        $query = "SELECT COUNT(*)
        FROM ".BASE_DATOS.".tab_genera_remdes a
        WHERE 
                a.cod_remdes = '".$cod_remdes."' AND
                a.ind_remdes = '".$indicador."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);  
    }


    /************************* INSERCIONES DB **************************************/

    function registrarTransportadora($a) {
        $sql = 'INSERT INTO ' . BASE_DATOS . '.tab_tercer_tercer(cod_tercer,
														num_verifi,
														cod_tipdoc,
														cod_terreg,
														nom_apell1,
														nom_apell2,
														nom_tercer,
														abr_tercer,
														dir_domici,
														num_telef1,
														num_telef2,
														num_telmov,
														num_faxxxx,
														cod_paisxx,
														cod_depart,
														cod_ciudad,
														dir_emailx,
														dir_urlweb,
														cod_estado,
														dir_ultfot,
														obs_tercer,
														obs_aproba,
														usr_creaci,
														fec_creaci) VALUES (
														"' . $a[0]['cod_tercer'] . '",
														"",
														"N",
														"0",
														NULL,
														NULL,
														"' . $a[0]['nom_tercer'] . '",
														"' . $a[0]['abr_tercer'] . '",
														"",
														"",
														NULL,
														NULL,
														"",
														"3",
														"11",
														"11001000",
														"",
														NULL,
														"1",
														NULL,
														"",
														NULL,
														"' . $this->cod_usuari . '",
                                                          "' . date("Y-m-d H:i:s") . '") 
                                                          ';
        new Consulta($sql, $this->conexion);
        $sql = 'INSERT INTO ' . BASE_DATOS . '.tab_tercer_emptra(cod_tercer, cod_minins, num_resolu, fec_resolu, num_region, ran_iniman, ran_finman
  									, ind_gracon, ind_ceriso, fec_ceriso, ind_cerbas, fec_cerbas, otr_certif, ind_cobnal, ind_cobint,
  									nro_habnal, fec_resnal, nom_repleg, val_pctret, usr_creaci, fec_creaci) VALUES ("' . $a[0]['cod_tercer'] . '", "",
  									 "", "0000-00-00", "0", "", "", "N", "N", "0000-00-00", "", "0000-00-00", "", "N", "N", "", "0000-00-00", "", "0.01", "' . $this->cod_usuari . '", "' . date("Y-m-d H:i:s") . '")';

        new Consulta($sql, $this->conexion);

    }

    function ins_tercer($datos){
        $abr_tercer = $datos[0]['nom_tercer']." ".$datos[0]['nom_apell1']." ".$datos[0]['nom_apell2'];
        $sql = 'INSERT INTO ' . BASE_DATOS . '.tab_tercer_tercer(
                    cod_tercer, num_verifi, cod_tipdoc, 
                    cod_terreg, nom_apell1, nom_apell2, 
                    nom_tercer, abr_tercer, dir_domici, 
                    num_telef1, num_telef2, num_telmov, 
                    num_faxxxx, cod_paisxx, cod_depart, 
                    cod_ciudad, dir_emailx, dir_urlweb, 
                    cod_estado, dir_ultfot, obs_tercer, 
                    obs_aproba, usr_creaci, fec_creaci
                )  VALUES  (
                "'.$datos[0]['cod_tercer'].'", "", 
                "C", "0", "' . $datos[0]['nom_apell1'] . '", "' . $datos[0]['nom_apell2'] . '", "' . $datos[0]['nom_tercer'] . '", 
                "' . $abr_tercer . '", 
                "' . $datos[0]['dir_domici'] . '", "' . $datos[0]['num_telmov'] . '", NULL, "' . $datos[0]['num_telmov'] . '", "", "3", "11", "11001000", 
                "", NULL, "1", NULL, "", NULL, "' . $this->cod_usuari . '", 
                "' . date("Y-m-d H:i:s") . '"
                ) ';
        new Consulta($sql, $this->conexion);
    }

    function ins_conductor($datos){
        $this->ins_tercer($datos);
        $sql = 'INSERT INTO tab_tercer_conduc(
                    cod_tercer, cod_tipsex, cod_grupsa, 
                    num_licenc, num_catlic, fec_venlic, 
                    cod_califi, num_libtri, fec_ventri, 
                    obs_habili, nom_epsxxx, nom_arpxxx, 
                    fec_venarl, nom_pensio, num_pasado, 
                    fec_venpas, nom_refper, tel_refper, 
                    obs_conduc, cod_operad, usr_creaci, 
                    fec_creaci
                ) 
                    VALUES 
                (
                    "'.$datos[0]['cod_tercer'].'", "", NULL, 
                    "'.$datos[0]['cod_tercer'].'", NULL, NULL, 
                    NULL, NULL, NULL, 
                    NULL, NULL, NULL, 
                    "0000-00-00", NULL, NULL, 
                    "0000-00-00", NULL, NULL, 
                    NULL, NULL, "' . $this->cod_usuari . '", 
                    NOW()
            )';
        new Consulta($sql, $this->conexion);
    }

    function ins_vehiculo($datos){
        $sql = "INSERT INTO tab_vehicu_vehicu(
                    num_placax, cod_marcax, cod_lineax, 
                    ano_modelo, cod_colorx, cod_carroc, 
                    num_motorx, num_seriex, num_chasis, 
                    val_pesove, val_capaci, reg_nalcar, 
                    num_poliza, nom_asesoa, cod_ciusoa, 
                    fec_vigfin, ano_repote, num_config, 
                    cod_propie, cod_tenedo, cod_conduc, 
                    nom_vincul, num_tarpro, num_tarope, 
                    cod_califi, fec_vigvin, num_polirc, 
                    fec_venprc, cod_aseprc, dir_fotfre, 
                    dir_fotizq, dir_fotder, dir_fotpos, 
                    dir_dtecno, dir_dsoatx, cod_tipveh, 
                    ind_chelis, fec_revmec, num_agases, 
                    fec_vengas, obs_vehicu, ind_estado, 
                    obs_estado, cod_paisxx, cod_opegps, 
                    usr_gpsxxx, clv_gpsxxx, idx_gpsxxx, 
                    ind_dispon, usr_creaci, fec_creaci
                ) 
                    VALUES 
                (
                    '".$datos[0]['num_placax']."', '".$datos[0]['cod_marcax']."', '".$datos[0]['cod_lineax']."',
                    '".$datos[0]['ano_modelo']."', '".$datos[0]['cod_colorx']."', '".$datos[0]['cod_carroc']."', 
                    NULL,NULL, NULL, 
                    0, 0, 0, 
                    NULL, NULL, 1, 
                    NULL, NULL, '".$datos[0]['num_config']."', 
                    '".$datos[0]['cod_propie']."', '".$datos[0]['cod_tenedo']."', '".$datos[0]['cod_conduc']."', 
                    NULL,NULL, NULL, 
                    NULL,NULL, NULL,  
                    NULL,NULL, NULL, 
                    NULL,NULL, NULL, 
                    NULL,NULL, 0, 
                    0, NULL, NULL, 
                    NULL, '', 1, 
                    '', 3, '".$datos[0]['cod_opegps']."', 
                    '".$datos[0]['usr_gpsxxx']."', '".$datos[0]['clv_gpsxxx']."', '".$datos[0]['idx_gpsxxx']."', 
                    1, '".$this->cod_usuari."', NOW() 
                )";
        new Consulta($sql, $this->conexion);
    }

    function ins_remdes($datos){
        $sql = "INSERT INTO tab_genera_remdes(
            cod_remdes, num_remdes, nom_remdes, 
            obs_adicio, cod_transp, abr_destin, 
            abr_remite, num_docume, hor_apertu, 
            hor_cierre, ind_remdes, ind_estado, 
            cod_ciudad, dir_remdes, cor_remdes, 
            cod_latitu, cod_longit, dir_emailx, 
            num_telefo, usr_creaci, fec_creaci
        ) 
        VALUES 
            (
                '".$datos[0]['cod_remdes']."', '".$datos[0]['num_remdes']."', '".$datos[0]['nom_remdes']."', 
                NULL, '".$datos[0]['cod_transp']."', '', 
                '', '', '00:00:00', 
                '00:00:00', '".$datos[0]['ind_remdes']."', 1, 
                '".$datos[0]['cod_ciudad']."', '".$datos[0]['dir_remdes']."', '', 
                '".$datos[0]['cod_latitu']."', '".$datos[0]['cod_longit']."', '".$datos[0]['dir_emailx']."', 
                '".$datos[0]['num_telefo']."','".$this->cod_usuari."', NOW() 
            )";
        new Consulta($sql, $this->conexion);
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

    function getMercancia($cod_product){
        $query = "SELECT nom_produc, des_comerc, pes_netoxx,
                         uni_medida
                    FROM  ".BASE_DATOS.".tab_genera_produc
                    WHERE cod_produc = '".$cod_product."'";
        $consulta = new Consulta($query, $this -> conexion);
        $consulta = $consulta->ret_matriz();
        return $consulta[0];
    }

    function verificarUnidadMedida($cod) {
        $sql = 'SELECT COUNT(*) FROM ' . BASE_DATOS . '.tab_genera_unimed WHERE cod_unimed = "' . $cod . '"';
        $consulta = new Consulta($sql, $this->conexion);
        $consulta = $consulta->ret_matriz();
        return $this->retornarBoolean($consulta[0][0]);
    }


    function castNumero($number){
        $number = preg_replace("/[^0-9,.]/", "", $number);
        $number = preg_replace('/[.]/', '', $number);
        return $number;
    }

}

new Proc_ins_despac($this->conexion, $_SESSION['usuario_aplicacion']->cod_usuari);
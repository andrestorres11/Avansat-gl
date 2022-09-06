<?php
/*! \file: class_califi_califi.php
 *  \brief: Clase principal para calificar
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 03/02/2016
 *  \bug: 
 *  \warning: 
 */


class Califi
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}

		switch($_REQUEST['Option']){
			case 'formAuditarDespac':
				self::formAuditarDespac();
				break;
			case 'CreateObserva':
				self::CreateObserva();
				break;

			case 'EditGPS':
				self::EditGPS();
				break;
			
			case 'IndUsaIDGPS':
				self::IndUsaIDGPS();
				break;

			case "editaGps";
				$this->editaGps();
				break;  
			case "NewObserva";
				$this->NewObserva();
				break; 
			case 'getListActivi':
				self::getListActivi();
				break;

			case 'tableCalifiDespac':
				$mCalifi = self::getCalifiDesUse( array("num_despac"=>$_REQUEST['num_despac'], "cod_activi"=>$_REQUEST['cod_activi']) );
				if( sizeof($mCalifi) > 0 ) #Verifica que la actividad ya esta calificada
					self::tableCalificado( $mCalifi[0] ); #Tabla Calificada
				else 
					self::tableCalifi('despac'); #Tabla para calificar
				break;

			case 'insertCalifiDesUsu':
				self::insertCalifiDesUsu();
				break;

			case 'tableCalifiUsuari':
				self::tableCalifiUsuari();
				break;

			case 'informGeneral':
				self::informGeneral();
				break;

			case 'informDetail':
				self::informDetail();
				break;
			
			case 'reeItiner':
				self::reeItiner();
				break;

			case 'reeNovedades':
				self::reeNovedades();
				break;
		}
	}

	/*! \fn: formAuditarDespac
	 *  \brief: Formulario para auditar despachos
	 *  \author: Ing. Fabian Salinas
	 *  \date:  03/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formAuditarDespac(){
		$mOperac = self::getOperacion();

		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Label( "<b>Filtros</b>", array("class"=>"CellHead", "align"=>"left", "colspan"=>"4", "end"=>true) );

			$mHtml->Label( "<font style='color:red'>*</font> Tipo de Operaci&oacute;n: ", array("class"=>"cellInfo1", "width"=>"25%") );
			$mHtml->Select2( array_merge(self::$cNull, $mOperac), array("name"=>"cod_operac", "id"=>"cod_operacID", "obl"=>"1", "validate"=>"select", "class"=>"cellInfo1", "width"=>"25%", "onchange"=>"getListActivi($(this));") );
			$mHtml->Label( "<font style='color:red'>*</font> Actividad: ", array("class"=>"cellInfo1", "width"=>"25%") );
			$mHtml->Select2( self::$cNull, array("name"=>"cod_activi", "id"=>"cod_activiID", "obl"=>"1", "validate"=>"select", "class"=>"cellInfo1", "width"=>"25%") );
		$mHtml->CloseTable('tr');
		$mHtml->setBody('<br>');

		$mHtml->setBody('<table name="tab_audita" id="tab_auditaID" width="100%" cellspacing="0" cellpadding="3" border="0" align="center">');
		$mHtml->setBody('</table>');

		$mHtml->setBody('<center><br>
							<input type="button" onclick="closePopUp(\'popupAuditarID\')" value=" Cerrar " class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="cursor:pointer">
						</center>');

		echo $mHtml->MakeHtml();
	}
	private function getOpeGps(){
        $paramGPS = getParameOpeGPS(self::$cConexion);
		$parOpeSt = $paramGPS[0];
		$parOpePr = $paramGPS[1];
		$parCodPais = $paramGPS[2];
		$opegpsPropio = NULL;
		$opegpsStanda = NULL;

		if($parOpeSt){
		$query = "SELECT a.cod_operad, CONCAT(a.nom_operad, ' [INTEGRADOR ESTANDAR]') as 'nom_operad', a.nit_operad 
				FROM ".BD_STANDA.".tab_genera_opegps a
				INNER JOIN ".BD_STANDA.".tab_opegps_paisxx b ON a.cod_operad = b.cod_operad AND b.cod_paisgl = $parCodPais
				WHERE a.ind_estado = '1'
			ORDER BY a.nom_operad ASC ";
		$consulta = new Consulta($query, self::$cConexion);
		$opegpsStanda = $consulta->ret_matriz("a");
		
		}

		if($parOpePr){
		$query = "SELECT cod_operad,nom_operad,nit_operad
				FROM ".BASE_DATOS.".tab_genera_opegps
				WHERE ind_estado = '1'
			ORDER BY nom_operad ASC ";
		$consulta = new Consulta($query, self::$cConexion);
		$opegpsPropio = $consulta->ret_matriz("a");
		}
		$operadores = SortMatrix(arrayMergeIgnoringNull($opegpsStanda,$opegpsPropio), 'nom_operad', 'ASC') ;
		return $operadores;
    }

	    /* ! \fn: CreateObserva
     *  \brief: inserta un nuevo contacto
     *  \author: Ing. Andres Martinez
     *  \date: 12/02/2018
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function EditGPS() {
		if ($_POST['ind_edicio'] == '0') {
			?>
		<?php
		}else{
			$informacion= self::getGPS($_POST['num_despac'])[0];
			
			$operadores = self::getOpeGps();

			$mSql = "SELECT
                IF(a.apl_idxxxx = '1', 'S', 'N') AS ind_usaidx
             FROM ".BASE_DATOS.".tab_genera_opegps a
              WHERE (a.cod_operad='".$informacion['gps_operad']."' OR a.nit_operad='".$informacion['gps_operad']."')";

			$consulta = new Consulta($mSql, self::$cConexion);
			$usaid = $consulta->ret_matriz();

			$ind_usaid = $usaid[0]['ind_usaidx'];
			$attr = '';
			if($ind_usaid=='S'){
				$attr = 'validate="text" maxlength="15" minlength="1"';
			}

		?>
		<div class="StyleDIV contenido" style="min-height: 145px !important;">
			<div class="col-md-1">&nbsp;</div>
				<div class="col-md-10">
					<div class="col-md-6">
						<div class="col-md-6 text-right">Operador GPS<font style="color:red">*</font></div>
						<div class="col-md-6 text-left">
							<select id="cod_operadEditID" name="cod_operad" class="ancho" obl="1" validate="select" onchange="habIdOperaGps(this)">
								<option value="">Seleccione una Opci&oacute;n.</option>
								<?php
								foreach ($operadores as $key => $value) {
									$sel = "";
									if ($value['cod_operad'] == $informacion['gps_operad'] || $value['nit_operad'] == $informacion['gps_operad']) {
										$sel = "selected";
									}

									?>
									<option <?= $sel ?> value="<?= $value['cod_operad'] ?>"><?= $value['nom_operad'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="col-md-3 text-right">Usuario:<font style="color:red">*</font></div>
						<div class="col-md-9 text-left">
							<input type="text" class="text-center ancho" name="gps_usuari" id="gps_usuariID" validate="text" obl="1" maxlength="250" minlength="10" value="<?= $informacion['gps_usuari'] ?>"></input>
						</div>
					</div>
					<div class="col-md-6">
						<div class="col-md-3 text-right">Contraseña:<font style="color:red">*</font></div>
						<div class="col-md-9 text-left">
							<input type="text" class="text-center ancho" name="gps_paswor" id="gps_pasworID" validate="text" obl="1" maxlength="250" minlength="10" value="<?= $informacion['gps_paswor'] ?>"></input>
						</div>
					</div>
					<div class="col-md-6">
						<div class="col-md-3 text-right">ID:<font style="color:red">*</font></div>
						<div class="col-md-9 text-left">
							<input type="text" class="text-center ancho" name="gps_idxxxx" id="gps_idxxxxID"  <?php echo $attr; ?> value="<?= $informacion['gps_idxxxx'] ?>"></input>
						</div>
					</div>
				</div>
			<div class="col-md-1">&nbsp;</div>
		</div>
	<?php
	}      
    }

	   /* ! \fn: CreateObserva
     *  \brief: inserta un nuevo contacto
     *  \author: Ing. Andres Martinez
     *  \date: 12/02/2018
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function IndUsaIDGPS() {

		$mSql = "SELECT
                IF(a.apl_idxxxx = '1', 'S', 'N') AS ind_usaidx
             FROM ".BASE_DATOS.".tab_genera_opegps a
              WHERE (a.cod_operad='".$_POST['cod_opegps']."' OR a.nit_operad='".$_POST['cod_opegps']."')";

		$consulta = new Consulta($mSql, self::$cConexion);
		$getGps = $consulta->ret_matriz();

		echo json_encode($getGps);
    }

	private function getGPS($num_despac) {
        
        $sql = "SELECT a.gps_operad, a.gps_usuari, a.gps_paswor, a.gps_idxxxx 
                  FROM " . BASE_DATOS . ".tab_despac_despac a
				  WHERE a.num_despac = '".$num_despac."'";

        $consulta = new Consulta($sql, self::$cConexion);
        $getGps = $consulta->ret_matriz();
        
        return $getGps;
    }

	private function getDespactGps($num_despac) {
        
        $sql = "SELECT MAX(a.cod_consec) as cod_consec 
                  FROM " . BASE_DATOS . ".tab_despac_gpsxxx a
				  WHERE a.num_despac = '".$num_despac."'";

        $consulta = new Consulta($sql, self::$cConexion);
        $codigoCon = $consulta->ret_matriz();
		if(COUNT($codigoCon)>=1){
			$valMax = $codigoCon[0]['cod_consec'];	
		}else{
			$valMax = 0;	
		}
        return $valMax;
    }

	  /* ! \fn: getUrlGps
     *  \brief: busca la url del operador gps
     *  \author: Ing. Cristian Torres
     *  \date: 10/06/2021
     *  \date modified: dia/mes/a?o
     *  \param: 
     *  \param: 
     *  \return 
     */
	private function getUrlGps( $cod_opegps = NULL )
	{
	  $mSql = "SELECT url_gpsxxx
				 FROM ".BASE_DATOS.".tab_genera_opegps
				WHERE (cod_operad = '".$cod_opegps."' OR nit_operad = '".$cod_opegps."')";
	  $consulta = new Consulta( $mSql,self::$cConexion);
		  $respuesta = $consulta->ret_matriz();
	  if(count($respuesta)>0){
		return $respuesta[0]['url_gpsxxx'];
	  }
	  return null;
	  
	}
	   /* ! \fn: editaGps
     *  \brief: inserta una Particularidad de la transportado
     *  \author: Ing. Andres Martinez
     *  \date: 08/02/2016
     *  \date modified: dia/mes/a?o
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function editaGps() {
        $mData = $_POST;
		
		$urlGps = self::getUrlGps($mData['gps_operad']);
        $mUpdate = "UPDATE " . BASE_DATOS . ".tab_despac_despac SET 
					gps_operad = '".$mData['gps_operad']."',
                    gps_usuari = '".utf8_decode($mData['gps_usuari'])."', 
                    gps_paswor = '".utf8_decode($mData['gps_paswor'])."',
					gps_idxxxx = '".$mData['gps_idxxxx']."',
					gps_urlxxx = '".$urlGps."',
                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                    fec_modifi = NOW()
                    WHERE num_despac = '".$mData['num_despac']."'";
		new Consulta($mUpdate, self::$cConexion);
					
		$codConsecutivo = (self::getDespactGps($mData['num_despac'])+1);

		$mInsert = "INSERT INTO " . BASE_DATOS . ".tab_despac_gpsxxx
			(
				num_despac,
				cod_consec,
				idx_gpsxxx,
				cod_opegps,
				nom_usrgps,
				clv_usrgps,
				usr_creaci,
				fec_creaci
				)
			VALUES(
				'".$mData['num_despac']."',
				$codConsecutivo,
				'".$mData['gps_idxxxx']."',
				'".$mData['gps_operad']."',
				'".utf8_decode($mData['gps_usuari'])."',
				'".utf8_decode($mData['gps_paswor'])."',
				'".$_SESSION['datos_usuario']['cod_usuari']."',
				NOW()
			)";	
				
        if ($consulta = new Consulta($mInsert, self::$cConexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    }

	private function CreateObserva() {
		$datos = (object) $_POST;
		if ($_POST['ind_edicio'] == '0') {
			?>
			<div class="StyleDIV contenido" style="min-height: 145px !important;">
				<div class="col-md-1">&nbsp;</div>
				<div class="col-md-10">
					
					<div class="col-md-12">

						<div class="col-md-3 text-right">Observacion:<font style="color:red">*</font></div>
						<div class="col-md-9 text-left">
							<textarea type="text" class="text-center ancho" name="des_observ" id="des_observID" validate="text" obl="1" maxlength="250" minlength="10"></textarea>
						</div>
					</div>
					
					
				</div>
				<div class="col-md-1">&nbsp;</div>
				</div>
		<?php
		}
            
    }
	/* ! \fn: NewObserva
     *  \brief: inserta un contacto de la transportado
     *  \author: Ing. Andres Martinez
     *  \date: 28/04/2021
     *  \date modified: dia/mes/a?o
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function NewObserva() {
        $mData = $_POST;

        $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_infdes_obsgen
        ( cod_transp, num_despac, obs_obsgen, 
        usr_creaci, fec_creaci
        )VALUES( '" . $mData['cod_transp'] . "', '" . $mData['num_despac'] . "', 
        '" . $mData['obs_obsgen'] . "','" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW() )";

        if ($consulta = new Consulta($mInsert, self::$cConexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    } 
    

	/*! \fn: getOperacion
	 *  \brief: Trae las operaciones activas
	 *  \author: Ing. Fabian Salinas
	 *  \date:  29/01/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getOperacion(){
		$mSql = "SELECT a.cod_operac, a.nom_operac 
				   FROM ".BASE_DATOS.".tab_callce_operac a 
				  WHERE a.ind_estado = 1 
			   ORDER BY a.nom_operac ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getListActivi
	 *  \brief: Crea la lista de actividades
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: HTML <select>
	 */
	private function getListActivi(){
		if( $_REQUEST['cod_operac'] != '' )
			$mActivi = self::getActivi( $_REQUEST['cod_operac'] );

		$mHtml  = '<select validate="select" id="cod_activiID" obl="1" name="cod_activi" class="form_01" onchange="'.$_REQUEST['fun_javasc'].'">';
        	$mHtml .= '<option value="">-----</option>';

        if( $_REQUEST['cod_operac'] != '' ){
			foreach ($mActivi as $row)
				$mHtml .= '<option value="'.$row[0].'">'.$row[1].'</option>';
        }

        $mHtml .= '</select>';

        echo $mHtml;
	}
	

	/*! \fn: getActivi
	 *  \brief: Trae las actividades de las operaciones
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param:
	 *  \return: Matriz
	 */
	private function getActivi( $mCodOperac = null ){
		$mSql = "SELECT a.cod_activi, a.nom_activi 
				   FROM ".BASE_DATOS.".tab_activi_activi a 
				  WHERE a.ind_estado = 1 ";
		$mSql .= !$mCodOperac ? "" : " AND a.cod_operac = $mCodOperac ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: tableCalifi
	 *  \brief: Crea la tabla para calificar el despacho segun Operacion->Actividad Items
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mIndCalifi  String   despac => Calificar Despachos; usuari => Calificar Usuarios
	 *  \param: mTabTitlex  String   Titulo de la tabla
	 *  \param: mC 			Integer  Consecutivo para el ID
	 *  \param: mIndReturn  Boolean  true retorna html, false imprime html
	 *  \return: 
	 */
	private function tableCalifi( $mIndCalifi = null, $mTabTitlex = 'Auditar', $mC = '0', $mIndReturn = false ){
		$mItemsx = self::getItems($_REQUEST['cod_activi']);
		$mCodItemsx = array();
		$mCol = 5;

		if( $mIndCalifi == 'despac' ){
			$mUsersx = self::getUsersDespacNov($_REQUEST['num_despac']);
			$mView = self::getView('jso_plarut');

			if($mView->pop_califi->sub->usr_califi == 1){
				$mCol = 6;
			}
		}

		$mHtml = new Formlib(2);

		$mHtml->Row();
			$mHtml->Label( "<b>$mTabTitlex</b>", array("class"=>"CellHead", "align"=>"left", "colspan"=>$mCol, "end"=>true) );

			$mHtml->Label( "<b>No.</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Items</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Cumplimiento</b>", array("class"=>"CellHead", "align"=>"center", "colspan"=>"3") );
			
			if($mCol == 6){
				$mHtml->Label( "<b>Usuario</b>", array("class"=>"CellHead", "align"=>"center", "end"=>true) );
			}else{
				$mHtml->setBody("</tr>");
			}

		foreach ($mItemsx as $row) {
			$mCodItemsx[] = $row[0];

			$mHtml->Label( $row[0], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $row[1], array("class"=>"cellInfo1", "align"=>"left", "id"=>"nom_items$row[0]ID") );
			$mHtml->setBody('<td align="right" class="cellInfo1" id="ind'.$mC.'_opcion'.$row[0].'TD">&nbsp;&nbsp;&nbsp;&nbsp;SI
								<input type="radio" value="SI" id="ind'.$mC.'_opcion'.$row[0].'ID" name="ind'.$mC.'_opcion'.$row[0].'" cod_itemsx="'.$row[0].'" val_porcen="'.$row[2].'">
							</td>');
			$mHtml->setBody('<td align="right" class="cellInfo1" id="ind'.$mC.'_opcion'.$row[0].'TD">&nbsp;&nbsp;NO
								<input type="radio" value="NO" id="ind'.$mC.'_opcion'.$row[0].'ID" name="ind'.$mC.'_opcion'.$row[0].'" cod_itemsx="'.$row[0].'" val_porcen="'.$row[2].'">
							</td>');
			$mHtml->setBody('<td align="right" class="cellInfo1" id="ind'.$mC.'_opcion'.$row[0].'TD">&nbsp;&nbsp;NA
								<input type="radio" value="NA" id="ind'.$mC.'_opcion'.$row[0].'ID" name="ind'.$mC.'_opcion'.$row[0].'" cod_itemsx="'.$row[0].'" val_porcen="'.$row[2].'">
							</td>');

			if($mCol == 6){
				$mHtml->Select2( array_merge(self::$cNull, $mUsersx), array("name"=>"usr".$mC."_califi".$row[0], "id"=>"usr".$mC."_califi".$row[0]."ID", "class"=>"cellInfo1", "end"=>true) );
			}else{
				$mHtml->setBody("</tr>");
			}
		}
		$mCodItemsx = implode('|', $mCodItemsx);
		
		$mHtml->Label( "Observacion:", array("class"=>"cellInfo1", "align"=>"left", "colspan"=>$mCol, "end"=>true) );
		$mHtml->TextArea( "", array("width"=>"100%", "align"=>"left", "class"=>"cellInfo1", "colspan"=>$mCol, "cols"=>"70", "rows"=>"2", "name"=>"obs_califi", "id"=>"obs_califiID", "end"=>true) );

		if( $mIndCalifi == 'despac' )
			$mHtml->Button( array("value"=>" Actualizar ", "colspan"=>$mCol, "onclick"=>"califiDespac($_REQUEST[cod_activi], '$mCodItemsx')", "class2"=>"CellHead", "align"=>"center", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only", "end"=>true) );

		if( $mIndReturn == true )
			return $mHtml->MakeHtml();
		else
			echo $mHtml->MakeHtml();
	}

	/*! \fn: tableCalificado
	 *  \brief: Crea la tabla que muestra la calificacion del (despacho o usuario) segun Operacion->Actividad Items
	 *  \author: Ing. Fabian Salinas
	 *  \date:  08/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCalifi     Array    Data de la calificacion   
	 *  \param: mIndReturn  Boolean  true retorna html, false imprime html
	 *  \return: 
	 */
	private function tableCalificado( $mCalifi = null, $mIndReturn = false ){
		$mTxt = $mCalifi['num_despac'] == '' ? 'Usuario: '.$mCalifi['usr_califi'] : 'Despacho: '.$mCalifi['num_despac'];
		$mData = json_decode($mCalifi['jso_califi']);
		$mCol = 4;

		if( $mCalifi['num_despac'] != '' ){
			$mUsersx = self::getUsersDespacNov($_REQUEST['num_despac']);
			$mView = self::getView('jso_plarut');

			if($mView->pop_califi->sub->usr_califi == 1){
				$mCol = 5;
			}
		}


		$mHtml = new Formlib(2);

		$mHtml->Row();
			$mHtml->Label( "$mTxt - Auditado por ".$mCalifi['usr_creaci']." el ".$mCalifi['fec_creaci'], array("class"=>"CellHead", "align"=>"left", "colspan"=>$mCol, "end"=>true) );

			$mHtml->Label( "<b>No.</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Items</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>Cumplio</b>", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->Label( "<b>%</b>", array("class"=>"CellHead", "align"=>"center") );

			if($mCol == 5){
				$mHtml->Label( "<b>Usuario</b>", array("class"=>"CellHead", "align"=>"center", "end"=>true) );
			}else{
				$mHtml->SetBody('</tr>');
			}

		for ($i=0; $i < sizeof($mData->cod); $i++) {
			$mItem = self::getItems($_REQUEST['cod_activi'], $mData->cod[$i]);

			$mHtml->Label( $mData->cod[$i], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $mItem[0][1], array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->Label( $mData->val[$i], array("class"=>"cellInfo1", "align"=>"center") );
			$mHtml->Label( $mData->por[$i]." %", array("class"=>"cellInfo1", "align"=>"right") );

			if($mCol == 5){
				$mHtml->Label( $mData->usr[$i], array("class"=>"cellInfo1", "align"=>"left", "end"=>true) );
			}else{
				$mHtml->SetBody('</tr>');
			}
		}

		$mHtml->Label( "<b>Observaci&oacute;n:</b> ".$mCalifi['obs_califi'], array("class"=>"cellInfo1", "align"=>"left", "colspan"=>$mCol, "end"=>true) );
		$mHtml->Label( "Cumplimiento del ".$mData->tsi." %", array("class"=>"CellHead", "align"=>"right", "colspan"=>$mCol, "end"=>true) );

		if( $mIndReturn == true )
			return $mHtml->MakeHtml();
		else
			echo $mHtml->MakeHtml();
	}

	/*! \fn: getItems
	 *  \brief: Trae los items de una actividad
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param:
	 *  \return: Matriz
	 */
	private function getItems($mCodActivi = null, $mCodItemsx = null){
		$mSql = "SELECT a.cod_itemsx, a.nom_itemsx, a.val_porcen, a.cod_activi 
				   FROM ".BASE_DATOS.".tab_activi_itemsx a 
				  WHERE a.ind_estado = 1 ";
		$mSql .= !$mCodActivi ? "" : " AND a.cod_activi IN ($mCodActivi) ";
		$mSql .= !$mCodItemsx ? "" : " AND a.cod_itemsx IN ($mCodItemsx) ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: insertCalifiDesUsu
	 *  \brief: Inserta la calificacion del despacho o usuario
	 *  \author: Ing. Fabian Salinas
	 *  \date:  05/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function insertCalifiDesUsu(){
		if( $_REQUEST['num_despac'] ){
			$mSql = "INSERT INTO ".BASE_DATOS.".tab_califi_desusr 
							( cod_activi, jso_califi, obs_califi, 
							  num_despac, val_cumpli, 
							  usr_creaci, fec_creaci ) 
					 VALUES ( '$_REQUEST[cod_activi]', '".json_encode($_REQUEST['itemsx'])."', '$_REQUEST[obs_califi]', 
							  '$_REQUEST[num_despac]', '$_REQUEST[val_cumpli]', 
							  '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
			$mConsult = new Consulta($mSql, self::$cConexion);
		}elseif( $_REQUEST['usr_califi'] ){
			$mUsuari = explode('|', $_REQUEST['usr_califi']);
			foreach ($mUsuari as $key => $val) {
				$mSql = "INSERT INTO ".BASE_DATOS.".tab_califi_desusr 
								( cod_activi, jso_califi, obs_califi, 
								  usr_califi, val_cumpli, 
								  usr_creaci, fec_creaci ) 
						 VALUES ( '$_REQUEST[cod_activi]', '".json_encode($_REQUEST['itemsx'][$val])."', '".$_REQUEST['obs_califi'][$val]."', 
								  '$val', '".$_REQUEST['itemsx'][$val]['tsi']."', 
								  '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() )";
				$mConsult = new Consulta($mSql, self::$cConexion);
			}
		}

		if( $_REQUEST['num_despac'] || $_REQUEST['usr_califi'] ){
			echo '1';
		}else
			echo 'Despacho o Usuario a calificar no Selecionado';
	}

	/*! \fn: getCalifiDesUse
	 *  \brief: Trae las calificaciones de los despacho o usuarios
	 *  \author: Ing. Fabian Salinas
	 *  \date:  08/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mData    Array   Campos del where
	 *  \param: mSqlAdi  String  Sql adicional se agrega al final de la sentencia 
	 *  \return: Matriz
	 */
	private function getCalifiDesUse( $mData = null, $mSqlAdi = null ){
		$mSql = "SELECT a.cod_activi, a.jso_califi, a.obs_califi, 
						a.usr_creaci, a.fec_creaci, a.usr_califi, 
						a.num_despac 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
				  WHERE 1=1 ";
		$mSql .= !$mData['cod_activi'] ? "" : " AND a.cod_activi = '$mData[cod_activi]' ";
		$mSql .= !$mData['num_despac'] ? "" : " AND a.num_despac = '$mData[num_despac]' ";
		$mSql .= !$mData['usr_creaci'] ? "" : " AND a.usr_creaci = '$mData[usr_creaci]' ";
		$mSql .= !$mData['usr_califi'] ? "" : " AND a.usr_califi IN ($mData[usr_califi]) ";
		$mSql .= !$mSqlAdi ? "" : $mSqlAdi;

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: getUsuari
	 *  \brief: Trae los usuarios activos
	 *  \author: Ing. Fabian Salinas
	 *  \date:  09/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mWhere  String  Iniciar con AND para agregar condiciones
	 *  \return: Matriz
	 */
	public function getUsuari( $mWhere = null ){
		$mSql = "SELECT a.cod_usuari, a.cod_usuari 
				   FROM ".BASE_DATOS.".tab_genera_usuari a 
				  WHERE a.ind_estado = 1 $mWhere 
			   ORDER BY a.cod_usuari ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: tableCalifiUsuari
	 *  \brief: Crea las tablas de las calificaciones para los usuarios
	 *  \author: Ing. Fabian Salinas
	 *  \date:  10/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function tableCalifiUsuari(){
		$mPorCalifi = false;
		$mUsrCalifi = explode('|', $_REQUEST['usr_califi']);

		
		$mHtml = new Formlib(2);

		$i=0;
		$j=0;
		foreach ($mUsrCalifi as $key => $usu) {
			$mCalifi = self::getCalifiDesUse( array("usr_califi"=>"'$usu'", "cod_activi"=>$_REQUEST['cod_activi']), " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') " );
			
			#Verifica que la actividad ya esta calificada
			if( sizeof($mCalifi) > 0 ){
				$mHtml->SetBody( '<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center" id="tab'.$j.'_okcalifiID" name="'.$usu.'" class="tab_okcalifi" consec="'.$j.'">' );
				$mHtml->SetBody( self::tableCalificado( $mCalifi[0], true ) ); #Tabla Calificada
				$j++;
			}
			else {
				$mPorCalifi = true;
				$mHtml->SetBody( '<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center" id="tab'.$i.'_porcalifID" name="'.$usu.'" class="tab_porcalif" consec="'.$i.'">' );
				$mHtml->SetBody( self::tableCalifi('usuari', $usu, $i, true) ); #Tabla para calificar
				$i++;
			}

			$mHtml->setBody('</table><br>');
		}

		if( $mPorCalifi == true ){
			$mSql = "SELECT GROUP_CONCAT(a.cod_itemsx SEPARATOR '|') AS cod_itemsx 
					   FROM ".BASE_DATOS.".tab_activi_itemsx a 
					  WHERE a.ind_estado = 1 
						AND a.cod_activi = $_REQUEST[cod_activi] 
				   GROUP BY a.cod_activi ";
			$mConsult = new Consulta($mSql, self::$cConexion );
			$mCodItemsx = $mConsult -> ret_matrix('i');

			$mHtml->Table('tr');
				$mHtml->Button( array("value"=>" Actualizar ", "colspan"=>"5", "onclick"=>"califiUsuari($_REQUEST[cod_activi], '".$mCodItemsx[0][0]."')", "class2"=>"CellHead", "align"=>"center", "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only") );
			$mHtml->closeTable('tr');
		}

		echo $mHtml->MakeHtml();
	}

	/*! \fn: informGeneral
	 *  \brief: Pinta el informe general inf_califi_desusu.php
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function informGeneral(){
		$mData = self::getDataG();
		$mTd1 = 'align="right" style="background: #00660F; color: #FFF;"';

		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Label( "DETALLADO DE LAS ACTIVIDADES", array("class"=>"CellHead", "colspan"=>"6", "end"=>true) );

			$mHtml->Label( "Actividad", array("class"=>"CellHead", "rowspan"=>"2") );
			$mHtml->Label( "Total Registros", array("class"=>"CellHead", "rowspan"=>"2") );
			$mHtml->Label( "Cumple", array("class"=>"CellHead", "colspan"=>"2") );
			$mHtml->Label( "No Cumple", array("class"=>"CellHead", "colspan"=>"2", "end"=>true) );

			$mHtml->Label( "Cantidad", array("class"=>"CellHead") );
			$mHtml->Label( "Porcentaje", array("class"=>"CellHead") );
			$mHtml->Label( "Cantidad", array("class"=>"CellHead") );
			$mHtml->Label( "Porcentaje", array("class"=>"CellHead", "end"=>true) );

			$mTotalx = array('tot'=>0, 'cum'=>0, 'noc'=>0);
			foreach ($mData as $key => $row) {
				$mTot = $row['0'] + $row['1'];
				$mTotalx['tot'] += $mTot;
				$mTotalx['cum'] += $row['1'];
				$mTotalx['noc'] += $row['0'];

				$mHtml->Label( $row['nom_activi'], array("class"=>"cellInfo1", "align"=>"left") );
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'totalx\', \''.$key.'\')">'.$mTot.'</td>');
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'cumple\', \''.$key.'\')">'.$row['1'].'</td>');
				$mHtml->Label( round(($row['1']*100/$mTot),1)." %", array("class"=>"cellInfo1") );
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'nocump\', \''.$key.'\')">'.$row['0'].'</td>');
				$mHtml->Label( round(($row['0']*100/$mTot),1)." %", array("class"=>"cellInfo1", "end"=>true) );

			}

			$mHtml->SetBody('<td align="right" style="background: #00660F; color: #FFF;">TOTAL</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'totalx\', \''.$_REQUEST['cod_activi'].'\')">'.$mTotalx['tot'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'cumple\', \''.$_REQUEST['cod_activi'].'\')">'.$mTotalx['cum'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.'>'.round(($mTotalx['cum']*100/$mTot),1).' %</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['ind_pestan'].'\', \'nocump\', \''.$_REQUEST['cod_activi'].'\')">'.$mTotalx['noc'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.'>'.round(($mTotalx['noc']*100/$mTot),1).' %</td>');
		$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getUsuariCalif
	 *  \brief: Trae los usuarios calificados o calificadores
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNomCampo  String  Nombre del campo a llamar
	 *  \return: Matriz
	 */
	public function getUsuariCalif($mNomCampo = null){
		$mSql = "SELECT a.$mNomCampo, a.$mNomCampo 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
				".( $mNomCampo == 'usr_califi' ? ' WHERE a.usr_califi IS NOT NULL ' : '' )."
			   GROUP BY a.$mNomCampo 
			   ORDER BY a.$mNomCampo ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: getDataG
	 *  \brief: Trae la data para el reporte general
	 *  \author: Ing. Fabian Salinas
	 *  \date:  15/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataG(){
		$mSql = "SELECT a.cod_activi, e.nom_activi, 
						IF(a.val_cumpli > 90, 1, 0 ) AS ind_cumpli 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON b.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_activi_activi e 
					 ON a.cod_activi = e.cod_activi 
				  WHERE 1=1 
				";

		$mSql .= !$_REQUEST['cod_activi'] ? "" : " AND a.cod_activi IN ($_REQUEST[cod_activi]) ";
		$mSql .= !$_REQUEST['usr_creaci'] ? "" : " AND a.usr_creaci IN ($_REQUEST[usr_creaci]) ";
		$mSql .= !$_REQUEST['fec_inicia'] || !$_REQUEST['fec_finali'] ? "" : " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";

		if( $_REQUEST['ind_pestan'] == 'despac' ){
			$mSql .= " AND a.usr_califi IS NULL AND a.num_despac IS NOT NULL ";
			$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = $_REQUEST[num_despac] ";
			$mSql .= !$_REQUEST['cod_tipdes'] ? "" : " AND b.cod_tipdes IN ($_REQUEST[cod_tipdes]) ";
			$mSql .= !$_REQUEST['cod_transp'] ? "" : " AND c.cod_transp IN ($_REQUEST[cod_transp]) ";
			$mSql .= !$_REQUEST['num_placax'] ? "" : " AND c.num_placax LIKE '$_REQUEST[num_placax]' ";
			$mSql .= !$_REQUEST['num_pedido'] ? "" : " AND d.num_pedido LIKE '$_REQUEST[num_pedido]' ";
		}elseif( $_REQUEST['ind_pestan'] == 'usuari' ){
			$mSql .= " AND a.usr_califi IS NOT NULL AND a.num_despac IS NULL ";
			$mSql .= !$_REQUEST['usr_califi'] ? "" : " AND a.usr_califi IN ($_REQUEST[usr_califi]) ";
		}

		$mSql = "SELECT x.cod_activi, x.nom_activi, x.ind_cumpli, 
						COUNT(x.ind_cumpli) AS can_regist 
				   FROM ( $mSql ) x 
			   GROUP BY x.cod_activi, x.ind_cumpli ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mData = $mConsult -> ret_matrix('a');

		$mResult = array();
		foreach ($mData as $row) {
			$mResult[$row['cod_activi']]['nom_activi'] = $row['nom_activi'];
			$mResult[$row['cod_activi']][$row['ind_cumpli']] = $row['can_regist'];
		}

		return $mResult;
	}

	/*! \fn: informDetail
	 *  \brief: Pinta el informe Detallado inf_califi_desusu.php
	 *  \author: Ing. Fabian Salinas
	 *  \date:  16/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function informDetail(){
		if( $_REQUEST['ind_pestan'] == 'despac' ){
			$mView = self::getView('jso_plarut');
		}

		$mTitle[0] = array( "num_consec" => "#", 
							"obj_califi" => "Calificado",
							"nom_activi" => "Actividad", 
							"val_cumpli" => "Cumplimiento", 
							"obs_califi" => "Observacion", 
							"usr_creaci" => "Calificado por", 
							"fec_creaci" => "Fecha Calificacion"
						   );

		if($mView->pop_califi->sub->usr_califi == 1){
			$mTitle[1] = array( "Item", "Calificacion", "Porcentaje", "Usuario" );
		}else{
			$mTitle[1] = array( "Item", "Calificacion", "Porcentaje" );
		}

		$mIdxTablex = "tabCalifi".$_REQUEST['ind_pestan']."ID";
		$mData = self::getDataDetail();
		$mItems = self::getItems($_REQUEST['cod_activi']);
		$mItem = array();
		foreach ($mItems as $row) {
			$mItem[$row[3]][$row[0]] = $row[1];
		}

		$mHtml = new Formlib(2);

		$mHtml->SetBody('<center>
							<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="exportTableExcel( \''.$mIdxTablex.'\' );" style="cursor:pointer">
						 </center><br>');

		$mHtml->setBody("Se Encontraron ".sizeof($mData)." Registros");

		$mHtml->Table('tr');
		$mHtml->setBody('<table id="'.$mIdxTablex.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');

				foreach ($mTitle[0] as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead", "rowspan"=>"2") );
				}
				$mHtml->Label( "Items", array("class"=>"CellHead", "colspan"=>sizeof($mTitle[1])) );
			$mHtml->CloseRow('td');

				foreach ($mTitle[1] as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
			$mHtml->CloseRow('td');

			$x=0;
			foreach ($mData as $row) {
				$mBg = $x % 2 == 0 ? 'bgTD1' : 'bgTD2';
				$mJson = json_decode($row['jso_califi']);
				$mSize = sizeof($mJson->cod);

				foreach ($mTitle[0] as $key => $tit) {
					$mHtml->Label($row[$key], array("class"=>"TD $mBg", "rowspan"=>$mSize) );
				}

				$y=0;
				for ($i=0; $i < $mSize; $i++) { 
					if( $x % 2 == 0 ){
						$mBg = $y % 2 == 0 ? 'bgTD3' : 'bgTD4';
					}else{
						$mBg = $y % 2 == 0 ? 'bgTD5' : 'bgTD6';
					}

					$j = $mJson->cod[$i];
					$k = $row['cod_activi'];

						$mHtml->Label( $mItem[$k][$j], array("class"=>"TD $mBg") );
						$mHtml->Label( $mJson->val[$i], array("class"=>"TD $mBg") );
						$mHtml->Label( $mJson->por[$i]." %", array("class"=>"TD $mBg") );

						if($mView->pop_califi->sub->usr_califi == 1){
							$mHtml->Label( $mJson->usr[$i], array("class"=>"TD $mBg") );
						}
					$mHtml->CloseRow('td');
					$y++;
				}
				$x++;
			}

		$mHtml->SetBody('</table>');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDataDetail
	 *  \brief: Trae la data para el reporte Detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date:  16/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataDetail(){
		$mSql = "SELECT a.cod_activi, a.jso_califi, e.nom_activi, 
						a.obs_califi, a.usr_creaci, a.fec_creaci, 
						CONCAT(a.val_cumpli, ' %') AS val_cumpli, 
						IF(a.val_cumpli > 90, 1, 0 ) AS ind_cumpli, 
						IF(a.num_despac IS NULL, a.usr_califi, a.num_despac) AS obj_califi 
				   FROM ".BASE_DATOS.".tab_califi_desusr a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON b.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_activi_activi e 
					 ON a.cod_activi = e.cod_activi 
				  WHERE 1=1 
				";

		$mSql .= !$_REQUEST['cod_activi'] ? "" : " AND a.cod_activi IN ($_REQUEST[cod_activi]) ";
		$mSql .= !$_REQUEST['usr_creaci'] ? "" : " AND a.usr_creaci IN ($_REQUEST[usr_creaci]) ";
		$mSql .= !$_REQUEST['fec_inicia'] || !$_REQUEST['fec_finali'] ? "" : " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";

		if( $_REQUEST['ind_pestan'] == 'despac' ){
			$mSql .= " AND a.usr_califi IS NULL AND a.num_despac IS NOT NULL ";
			$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = $_REQUEST[num_despac] ";
			$mSql .= !$_REQUEST['cod_tipdes'] ? "" : " AND b.cod_tipdes IN ($_REQUEST[cod_tipdes]) ";
			$mSql .= !$_REQUEST['cod_transp'] ? "" : " AND c.cod_transp IN ($_REQUEST[cod_transp]) ";
			$mSql .= !$_REQUEST['num_placax'] ? "" : " AND c.num_placax LIKE '$_REQUEST[num_placax]' ";
			$mSql .= !$_REQUEST['num_pedido'] ? "" : " AND d.num_pedido LIKE '$_REQUEST[num_pedido]' ";
		}elseif( $_REQUEST['ind_pestan'] == 'usuari' ){
			$mSql .= " AND a.usr_califi IS NOT NULL AND a.num_despac IS NULL ";
			$mSql .= !$_REQUEST['usr_califi'] ? "" : " AND a.usr_califi IN ($_REQUEST[usr_califi]) ";
		}

		$mSql = "SELECT @rownum := @rownum + 1 AS num_consec, x.* 
				   FROM ( SELECT @rownum :=0 ) r, 
						( $mSql ) x 
				  WHERE 1=1 ";

		switch ($_REQUEST['ind_column']) {
			case 'cumple': $mSql .= " AND x.ind_cumpli = 1 "; break;
			case 'nocump': $mSql .= " AND x.ind_cumpli = 0 "; break;
		}

		$mSql .= " ORDER BY x.obj_califi, fec_creaci ASC ";

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: getPermisosRespon
	 *  \brief: Trae los permisos de visualizacion del usuario por responsable
	 *  \author: Ing. Fabian Salinas
	 *  \date:  18/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	public function getPermisosRespon(){
		$mSql = "SELECT a.jso_infcal 
				   FROM ".BASE_DATOS.".tab_genera_respon a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
					 ON a.cod_respon = b.cod_respon 
				  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix('i');

		return json_decode($mData[0][0]);
	}

	/*! \fn: getUsersDespacNov
	 *  \brief: Trae los usuariarios que an registrado novedades en el despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date: 16/05/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mNumDespac  Integer  Numero del despacho
	 *  \return: Matriz
	 */
	private function getUsersDespacNov( $mNumDespac ){
		$mSql = "(
					SELECT usr_creaci, usr_creaci AS nom_usuari 
					  FROM ".BASE_DATOS.".tab_despac_noveda 
					 WHERE num_despac = $mNumDespac 
				 ) UNION (
					SELECT usr_creaci, usr_creaci AS nom_usuari 
					  FROM ".BASE_DATOS.".tab_despac_contro 
					 WHERE num_despac = $mNumDespac 
				 ) ORDER BY 1 ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		return $mConsult->ret_matrix('i');
	}

	/*! \fn: getView
     *  \brief: Trae los indicadores de secciones visibles por responsable (Perfil)
     *  \author: Ing. Fabian Salinas
     *  \date: 16/05/2016
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCatego   String   campo categoria a retornar
     *  \return: Object
     */
    public function getView( $mCatego )
    {
        $mSql = "SELECT a.jso_bandej, a.jso_encabe, a.jso_plarut 
                   FROM ".BASE_DATOS.".tab_genera_respon a 
             INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
                     ON a.cod_respon = b.cod_respon 
                  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
        $mConsult = new Consulta($mSql, self::$cConexion );
        $mData = $mConsult->ret_matrix('a');

        return json_decode($mData[0][$mCatego]);
    }

	/*! \fn: reeItiner
     *  \brief: Reevia Itinerario
     *  \author: Ing. Cristian Andrés Torres
     *  \date: 02/06/2022
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     */
	private function reeItiner(){
		include( '../lib/InterfGPS.inc' );
		$mInterfGps = new InterfGPS( self::$cConexion ); 
		
		$mResp = $mInterfGps->setPlacaIntegradorGPS( $_REQUEST['num_despac'], ['ind_transa' => 'I'] );

		$respuesta = [];
		if($mResp['code_resp'] == '1000'){
			$respuesta["status"] = 1000;
            $respuesta["type"] = "success";
            $respuesta["title"] = "Proceso Exitoso";
			$respuesta["info"] = "<p>Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']."</p>";
        }else{
            $respuesta["status"] = 2000;
            $respuesta["type"] = "error";
            $respuesta["title"] = "Algo fallo";
			$respuesta["info"] = "<p>Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']."</p>";
        }
		$respuesta["text"] = "Reenvio despacho: ".$_REQUEST['num_despac'].", Placa:".$_REQUEST['num_placax'];
		
		echo json_encode($respuesta);
	}

	/*! \fn: reeNovedades
     *  \brief: Realiza el reenvio de novedades a avansat
     *  \author: Ing. Cristian Andrés Torres
     *  \date: 02/08/2022
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     */
	private function reeNovedades(){
		include( '../despac/InsertNovedad.inc' );
		$transac_nov = new InsertNovedad($_REQUEST['cod_servic'], $_REQUEST['Option'], $_SESSION['codigo'], self::$cConexion);
		$RESPON = $transac_nov->reenviaNovedadesAvansat($_REQUEST['num_despac']); 
		echo json_encode(cleanArray($RESPON));
	}


}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new Califi();
else
	$_INFORM = new Califi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );	

?>
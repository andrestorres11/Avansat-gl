<?php

/* ! \file: ajax_rentab_produc
 *  \brief: Permite visualizar los infomres y la información necesaria por ajax para el informe
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 27/04/2020
 *  \bug: 
 *  \warning: 
 */

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_rentab_produc
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'crearCampos':
        $this->crearCampos();
        break;
      case 'setRegistros':
        $this->setRegistros();
        break;
      case 'dataList':
        $this->dataList();
        break;
      case 'regDetalle':
        $this->regDetalle();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Luis Manrique
  *  \date: 12/05/2020
  *  \date modified:
  *  \return BOOL
  */
  private function setRegistros() {

    $idTable = explode("_", $_REQUEST['id'])[1];
    $response = [];

    switch ($idTable) {
      case 'porNovedad':
        $response['html'] = utf8_decode(self::porNovedad($idTable));
        $response['posicion'] = 2;
      	break;

      case 'porTurno':
        $response['html'] = utf8_decode(self::porTurno($idTable));
        $response['posicion'] = 2;
        break;

      case 'diferencia':
        $response['html'] = utf8_decode(self::diferencia($idTable));
        $response['posicion'] = 3;
        break;
    }

    echo json_encode($response);
  }

  //---------------------------------------------
  /*! \fn: porNovedad
  *  \brief: Función que genera la consulta y retorna la información
  *  \author: Ing. Luis Manrique
  *  \date: 12/05/2020
  *  \date modified: 
  *  \return BOOL
  */

  private function porNovedad($idTable){
    try {
      //Declarar variables necesarias
      $usuarios = '';

   	  //Se recorre los usuarios para capturarlos y asignales la coma
      foreach ($_REQUEST['cod_usuari'] as $key => $value) {
      	if (empty($usuarios)) {
      		$usuarios = "'".$value."'";
      	}else{
      		$usuarios .= ",'".$value."'";
      	}
      }

      //Asigna las fechas y horas a las variables
      $fec_inicia = $_REQUEST['fec_inicia']." ".$_REQUEST['hor_inicia'];
      $fec_finalx = $_REQUEST['fec_finalx']." ".$_REQUEST['hor_finalx'];
     
      //Genera consulta
      $mSql = ' SELECT  d.cod_perfil,
                        d.cod_usuari,
                        e.val_hordia AS cos_horaxx,
                        c.fec_noveda AS fec_regist
                  FROM  '.BASE_DATOS.'.tab_despac_despac a
            INNER JOIN 	'.BASE_DATOS.'.tab_despac_vehige b
            		ON 	a.num_despac = b.num_despac
            			AND a.fec_salida IS NOT NULL 
                        AND (
                            a.fec_llegad IS NOT NULL 
                            OR a.fec_llegad != "0000-00-00 00:00:00"
                        ) 
                        AND a.ind_planru = "S" 
                        AND a.ind_anulad in ("R") 
                        AND b.ind_activo = "S"
            INNER JOIN 	(
            				(
            					SELECT 	a.num_despac, 
            							a.cod_noveda, 
            							a.fec_noveda, 
            							a.usr_creaci
            					 FROM 	'.BASE_DATOS.'.tab_despac_noveda a
            					WHERE 	a.usr_creaci IN ('.$usuarios.')
            							AND a.fec_noveda BETWEEN "'.$fec_inicia.'" AND "'.$fec_finalx.'"

            				)UNION(
            					SELECT 	a.num_despac, 
            							a.cod_noveda, 
            							a.fec_contro AS fec_noveda, 
            							a.usr_creaci
            					 FROM 	'.BASE_DATOS.'.tab_despac_contro a
            					WHERE 	a.usr_creaci IN ('.$usuarios.')
            							AND a.fec_contro BETWEEN "'.$fec_inicia.'" AND "'.$fec_finalx.'"
            				)
            			) c
            		ON 	a.num_despac = c.num_despac
            INNER JOIN 	'.BASE_DATOS.'.tab_genera_usuari d
            		ON 	d.cod_usuari = c.usr_creaci
           	INNER JOIN 	'.BASE_DATOS.'.tab_hojvid_ctxxxx e
           			ON 	d.cod_usuari = e.usr_asigna
            ';

      //Ejecuta la consulta
      $mMatriz = new Consulta($mSql, $this->conexion);
      $mMatriz = $mMatriz->ret_matrix("a");
		
	  //Retorna la respuesta de la función	
	  return self::informeNovedad($mMatriz, $idTable);

    } catch (Exception $e) {
      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    }
  }

  //---------------------------------------------
  /*! \fn: porTurno
  *  \brief: Función que genera la consulta y retorna la información
  *  \author: Ing. Luis Manrique
  *  \date: 12/05/2020
  *  \date modified: 
  *  \return BOOL
  */

  private function porTurno($idTable){
    try {
      //Declarar variables necesarias
      $usuarios = '';

   	  //Se recorre los usuarios para capturarlos y asignales la coma
      foreach ($_REQUEST['cod_usuari'] as $key => $value) {
      	if (empty($usuarios)) {
      		$usuarios = "'".$value."'";
      	}else{
      		$usuarios .= ",'".$value."'";
      	}
      }

      //Asigna las fechas y horas a las variables
      $fec_inicia = $_REQUEST['fec_inicia']." ".$_REQUEST['hor_inicia'];
      $fec_finalx = $_REQUEST['fec_finalx']." ".$_REQUEST['hor_finalx'];
     
      //Genera consulta
      $mSql = ' SELECT  d.cod_perfil,
                        d.cod_usuari,
                        e.val_hordia AS cos_horaxx,
                        c.fec_noveda AS fec_regist
                  FROM  '.BASE_DATOS.'.tab_despac_despac a
            INNER JOIN 	'.BASE_DATOS.'.tab_despac_vehige b
            		ON 	a.num_despac = b.num_despac
            			AND a.fec_salida IS NOT NULL 
                        AND (
                            a.fec_llegad IS NOT NULL 
                            OR a.fec_llegad != "0000-00-00 00:00:00"
                        ) 
                        AND a.ind_planru = "S" 
                        AND a.ind_anulad in ("R") 
                        AND b.ind_activo = "S"
            INNER JOIN 	(
            				(
            					SELECT 	a.num_despac, 
            							a.cod_noveda, 
            							a.fec_noveda, 
            							a.usr_creaci
            					 FROM 	'.BASE_DATOS.'.tab_despac_noveda a
            					WHERE 	a.usr_creaci IN ('.$usuarios.')
            							AND a.fec_noveda BETWEEN "'.$fec_inicia.'" AND "'.$fec_finalx.'"

            				)UNION(
            					SELECT 	a.num_despac, 
            							a.cod_noveda, 
            							a.fec_contro AS fec_noveda, 
            							a.usr_creaci
            					 FROM 	'.BASE_DATOS.'.tab_despac_contro a
            					WHERE 	a.usr_creaci IN ('.$usuarios.')
            							AND a.fec_contro BETWEEN "'.$fec_inicia.'" AND "'.$fec_finalx.'"
            				)
            			) c
            		ON 	a.num_despac = c.num_despac
            INNER JOIN 	'.BASE_DATOS.'.tab_genera_usuari d
            		ON 	d.cod_usuari = c.usr_creaci
           	INNER JOIN 	'.BASE_DATOS.'.tab_hojvid_ctxxxx e
           			ON 	d.cod_usuari = e.usr_asigna
            ';

      //Ejecuta la consulta
      $mMatriz = new Consulta($mSql, $this->conexion);
      $mMatriz = $mMatriz->ret_matrix("a");
	  //Retorna la respuesta de la función	
	  return self::informeTurno($mMatriz, $idTable);

    } catch (Exception $e) {
      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    }
  }


  //---------------------------------------------
  /*! \fn: diferencia
  *  \brief: Función que genera la consulta y retorna la información
  *  \author: Ing. Luis Manrique
  *  \date: 12/05/2020
  *  \date modified: 
  *  \return BOOL
  */

  private function diferencia($idTable){
    try {
      //Declarar variables necesarias
      $usuarios = '';

   	  //Se recorre los usuarios para capturarlos y asignales la coma
      foreach ($_REQUEST['cod_usuari'] as $key => $value) {
      	if (empty($usuarios)) {
      		$usuarios = "'".$value."'";
      	}else{
      		$usuarios .= ",'".$value."'";
      	}
      }

      //Asigna las fechas y horas a las variables
      $fec_inicia = $_REQUEST['fec_inicia']." ".$_REQUEST['hor_inicia'];
      $fec_finalx = $_REQUEST['fec_finalx']." ".$_REQUEST['hor_finalx'];
     
      //Genera consulta
      $mSql = 'SELECT   b.cod_transp,
                        IF( d.nom_tercer = "",
                            d.abr_tercer,
                            d.nom_tercer
                        ) AS nom_tercer,
                        IF(
                            f.val_regist IS NULL OR f.val_regist = 0,
                            "Despacho",
                            "Novedad"
                        ) AS tip_modali,
                        IF(
                            f.val_regist IS NULL OR f.val_regist = 0,
                            f.val_despac,
                            f.val_regist
                        ) AS val_unitar,
                        a.num_despac,
                        SUM(1) AS can_noveda
                  FROM  '.BASE_DATOS.'.tab_despac_despac a
            INNER JOIN  '.BASE_DATOS.'.tab_despac_vehige b
                    ON  a.num_despac = b.num_despac
                        AND a.fec_salida IS NOT NULL 
                        AND (
                            a.fec_llegad IS NOT NULL 
                            OR a.fec_llegad != "0000-00-00 00:00:00"
                        ) 
                        AND a.ind_planru = "S" 
                        AND a.ind_anulad in ("R") 
                        AND b.ind_activo = "S"
            INNER JOIN  (
                            (
                                SELECT  a.num_despac, 
                                        a.cod_noveda, 
                                        a.fec_noveda, 
                                        a.usr_creaci
                                 FROM   '.BASE_DATOS.'.tab_despac_noveda a
                                WHERE   a.usr_creaci IN ('.$usuarios.') 
                                        AND a.fec_noveda BETWEEN "'.$fec_inicia.'" AND "'.$fec_finalx.'"

                            )UNION(
                                SELECT  a.num_despac, 
                                        a.cod_noveda, 
                                        a.fec_contro AS fec_noveda, 
                                        a.usr_creaci
                                 FROM   '.BASE_DATOS.'.tab_despac_contro a
                                WHERE   a.usr_creaci IN ('.$usuarios.') 
                                        AND a.fec_contro BETWEEN "'.$fec_inicia.'" AND "'.$fec_finalx.'"
                            )
                        ) c
                    ON  a.num_despac = c.num_despac
            
            INNER JOIN  '.BASE_DATOS.'.tab_tercer_tercer d
                    ON  b.cod_transp = d.cod_tercer
            INNER JOIN  (
                            SELECT MAX(a.num_consec) AS num_consec, 
                                   a.cod_transp
                              FROM '.BASE_DATOS.'.tab_transp_tipser a
                          GROUP BY a.cod_transp
                         ) e
                    ON  b.cod_transp = e.cod_transp
            INNER JOIN  '.BASE_DATOS.'.tab_transp_tipser f
                    ON  e.cod_transp = f.cod_transp 
                        AND e.num_consec = f.num_consec
              GROUP BY  b.cod_transp, a.num_despac
            ';

      //Ejecuta la consulta
      $mMatriz = new Consulta($mSql, $this->conexion);
      $mMatriz = $mMatriz->ret_matrix("a");


      if (count($mMatriz)>0) {
      	return self::informeDiferencia($mMatriz, $idTable);
      }else{
      	return false;
      }
		  
		  

    } catch (Exception $e) {
      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    }
  }

  /*! \fn: informeNovedad
   *  \brief: Crea la tabla para los informes por novedad
   *  \author: Ing. Luis Manrique
   *  \date: 12/05/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */

  private function informeNovedad($mMatriz, $idTable){
  	//Declara variables necesarias
    $dataTable = [];

   	//Recorre el arreglo
    foreach ($mMatriz as $identi => $data) {
      $hora = explode(" ", $data['fec_regist'])[1];
      $hora = explode(":", $hora)[0];

      //Identifica el tipo de informe para asignar valores
      switch ($_REQUEST['tip_inform']) {
    		case 'Diario':
    			$data['fec_regist'] = explode(" ", $data['fec_regist'])[0];
    			$titulo = "Dia ";
    		 break;   		
    		case 'Semanal':
    			$data['fec_regist'] = strtotime(explode(" ", $data['fec_regist'])[0]);
      			$data['fec_regist'] = date('W', $data['fec_regist']) ;
      			$titulo = "Semana # ";
    		 break;   
    		case 'Mensual':
    			$data['fec_regist'] = strtotime(explode(" ", $data['fec_regist'])[0]);
      			$data['fec_regist'] = date('m', $data['fec_regist']) ;
      			$titulo = "Mes de ";
    		 break;   			
		}
      
	  //Valida la existencia de la posición en el arreglo para sumar la cantidad de casos		
      if(isset($dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['hor_regist'])){
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['can_casosx']++;
      }

      //Crea las nuevas posiciones del arreglo solo la primer vez y no se dupliquen
      if(!isset($dataTable[$data['fec_regist']][$data['cod_usuari']][$hora])){
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['cod_perfil'] = $data['cod_perfil'];
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['cod_usuari'] = $data['cod_usuari'];
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['cos_horaxx'] = $data['cos_horaxx'];
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['fec_regist'] = $data['fec_regist'];
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['hor_regist'] = $hora;
        $dataTable[$data['fec_regist']][$data['cod_usuari']][$hora]['can_casosx'] = 1;
      }
    }

    

    //Crea un arregle con las horas
    $arrayFechaHora = [];
    foreach ($dataTable as $fecha => $dataH) {
    	foreach ($dataH as $cod_usuari => $dataC) {
    		foreach ($dataC as $hora => $dataD) {
    			if (!isset($arrayFechaHora[$fecha][$hora])) {
    				$arrayFechaHora[$fecha][$hora] = $hora;
    			}
    		}
    	}
    }

    //Valida si el usuario no tiene las horas para poder crearcelas en 0 y no genere errores al momento de mostrar la información
    foreach ($dataTable as $fecha => $dataH) {
    	foreach ($dataH as $cod_usuari => $dataC) {
    		foreach ($dataC as $hora => $dataD) {
    			foreach ($arrayFechaHora as $fechaHora => $arrayHora) {
					foreach ($arrayHora as $keyHora => $valuehora) {
						if(!isset($dataTable[$fechaHora][$cod_usuari][$valuehora])){
							$dataTable[$fechaHora][$cod_usuari][$valuehora]['cod_perfil'] = $dataD['cod_perfil'];
					        $dataTable[$fechaHora][$cod_usuari][$valuehora]['cod_usuari'] = $dataD['cod_usuari'];
					        $dataTable[$fechaHora][$cod_usuari][$valuehora]['cos_horaxx'] = $dataD['cos_horaxx'];
					        $dataTable[$fechaHora][$cod_usuari][$valuehora]['fec_regist'] = $fechaHora;
					        $dataTable[$fechaHora][$cod_usuari][$valuehora]['hor_regist'] = $valuehora;
					        $dataTable[$fechaHora][$cod_usuari][$valuehora]['can_casosx'] = 0;
						}
					}
					//Organiza el arreglo
					ksort($dataTable[$fechaHora][$cod_usuari]);
				}
    		}
    	}
    }

    //Recoore la data para poder crear las tabals basados en las fechas
    foreach ($dataTable as $fecha => $dataH) {
      $html .= '<table id="'.$fecha.'" class="table table-bordered tab_'.$idTable.' table_datatables">
                <thead>
                    <tr>
                      <th colspan="100" class="tituloFecha">'.$titulo.$fecha.'</th>
                    </tr>
                    <tr>
                      <th rowspan="2" class="buscar subtitbusq">Cod. Perfil</th> 
                      <th rowspan="2" class="buscar subtitbusq">Usuario</th>
                      <th rowspan="2" class="buscar subtitbusq">Costo Hora</th>';
      //Crea variables necesarias
      $th1 = '';
      $th2 = '';
      $tbody = '';
      $usr_anteri = '';
      $ban = 0;
      foreach ($dataH  as $cod_usuari => $dataC) {
        foreach ($dataC as $hora => $dataD) {
        	//Crea un estado para crear los titulos solo la primera vez
        	if ($ban == 0) {
        		$th1 .= '<th colspan="2" class="tutuloHora">'.$hora.'</th>';
		        $th2 .= '<th class="buscar subtitbusq">Registros</th>';
		        $th2 .= '<th class="buscar subtitbusq">Valor total</th>';
        	}

        	if (empty($usr_anteri)) {
        		$tbody .= '<tr>
	        				<td>'.$dataD['cod_perfil'].'</td>
	        				<td>'.$dataD['cod_usuari'].'</td>
	        				<td>$'.$dataD['cos_horaxx'].'</td>
	        				<td class="btn-link" onclick="detalle(\''.$fecha.'\', \''.$hora.'\', \''.$dataD['cod_usuari'].'\', \''.$_REQUEST['tip_inform'].'\')">'.$dataD['can_casosx'].'</td>
	        				<td>$'.$dataD['cos_horaxx']*$dataD['can_casosx'].'</td>
	        		  	 ';
        	}else if ($usr_anteri != $cod_usuari) {
        		$tbody .= '</tr><tr>
	        				<td>'.$dataD['cod_perfil'].'</td>
	        				<td>'.$dataD['cod_usuari'].'</td>
	        				<td>$'.$dataD['cos_horaxx'].'</td>
	        				<td class="btn-link" onclick="detalle(\''.$fecha.'\', \''.$hora.'\', \''.$dataD['cod_usuari'].'\', \''.$_REQUEST['tip_inform'].'\')">'.$dataD['can_casosx'].'</td>
	        				<td>$'.$dataD['cos_horaxx']*$dataD['can_casosx'].'</td>
	        		  	 ';	  	 
        	}else{
        		$tbody .= '<td class="btn-link" onclick="detalle(\''.$fecha.'\', \''.$hora.'\', \''.$dataD['cod_usuari'].'\', \''.$_REQUEST['tip_inform'].'\')">'.$dataD['can_casosx'].'</td>
        				   <td>$'.$dataD['cos_horaxx']*$dataD['can_casosx'].'</td>';
        		
        	}
        	$usr_anteri = $cod_usuari;
        }
        $tbody.'</tr>';
        $ban++;
      }

      $html .= $th1.'</tr><tr>'.$th2.'</tr>';

      $html .=' 
                
              </thead>
              <tbody>'.$tbody.'</tbody>
              <tfoot>
		            <tr>
		                <th colspan="2" class="tituloFecha" style="text-align:right">Total:</th>
		            </tr>
		        </tfoot>
          </table>';
    }
    
    return $html;
  }


/*! \fn: informeTurno
   *  \brief: Crea la tabla para los informes por turno
   *  \author: Ing. Luis Manrique
   *  \date: 12/05/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */

  private function informeTurno($mMatriz, $idTable){
  	//Declara variables necesarias
    $dataTable = [];


    //Asigna franjas horarias.
    $franjaHoraria =[
					0 => [0 => '06-00', 1 => '13-59'],
					1 => [0 => '14-00', 1 => '21-59'],
					2 => [0 => '22-00', 1 => '05-59']
				];

   	//Recorre el arreglo
    foreach ($mMatriz as $identi => $data) {
      $hora = explode(" ", $data['fec_regist'])[1];
      $hora = explode(":", $hora)[0];

      //Identifica el tipo de informe para asignar valores
      switch ($_REQUEST['tip_inform']) {
			case 'Diario':
				$data['fec_regist'] = explode(" ", $data['fec_regist'])[0];
				$titulo = "Dia ";
			 break;   		
			case 'Semanal':
				$data['fec_regist'] = strtotime(explode(" ", $data['fec_regist'])[0]);
	  			$data['fec_regist'] = date('W', $data['fec_regist']) ;
	  			$titulo = "Semana # ";
			 break;   
			case 'Mensual':
				$data['fec_regist'] = strtotime(explode(" ", $data['fec_regist'])[0]);
	  			$data['fec_regist'] = date('m', $data['fec_regist']) ;
	  			$titulo = "Mes de ";
			 break;   			
		}

	  	foreach ($franjaHoraria as $idFranja => $franjas) {
	   		if ($hora >= $franjas[0] && $hora <= $franjas[1]) {
	   			//Valida la existencia de la posición en el arreglo para sumar la cantidad de casos		
		    	if(isset($dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['hor_regist'])){
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['can_casosx']++;
		    	}

		    	//Crea las nuevas posiciones del arreglo solo la primer vez y no se dupliquen
		    	if(!isset($dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora])){
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['cod_perfil'] = $data['cod_perfil'];
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['cod_usuari'] = $data['cod_usuari'];
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['cos_horaxx'] = $data['cos_horaxx'];
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['fec_regist'] = $data['fec_regist'];
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['hor_regist'] = $hora;
		      		$dataTable[$data['fec_regist']][$franjas[0]."_".$franjas[1]][$data['cod_usuari']][$hora]['can_casosx'] = 1;
		    	}
	   		}
	   	} 
    }

    //Crea un arregle con las horas
    $arrayFechaHora = [];
    foreach ($dataTable as $fecha => $dataH) {
    	foreach ($dataH as $inter => $dataI) {
    		foreach ($dataI as $cod_usuari => $dataC) {
	    		foreach ($dataC as $hora => $dataD) {
	    			if (!isset($arrayFechaHora[$fecha][$inter][$hora])) {
	    				$arrayFechaHora[$fecha][$inter][$hora] = $hora;
	    			}
	    		}
	    	}
    	}
    }

    //Valida si el usuario no tiene las horas para poder crearcelas en 0 y no genere errores al momento de mostrar la información
    foreach ($dataTable as $fecha => $dataH) {
    	foreach ($dataH as $inter => $dataI) {
    		foreach ($dataI as $cod_usuari => $dataC) {
	    		foreach ($dataC as $hora => $dataD) {
	    			foreach ($arrayFechaHora as $fechaHora => $arrayInter) {
	    				foreach ($arrayInter as $interV => $arrayHora) {
	    					foreach ($arrayHora as $keyHora => $valuehora) {
								if(!isset($dataTable[$fechaHora][$interV][$cod_usuari][$valuehora])){
									$dataTable[$fechaHora][$interV][$cod_usuari][$valuehora]['cod_perfil'] = $dataD['cod_perfil'];
							        $dataTable[$fechaHora][$interV][$cod_usuari][$valuehora]['cod_usuari'] = $dataD['cod_usuari'];
							        $dataTable[$fechaHora][$interV][$cod_usuari][$valuehora]['cos_horaxx'] = $dataD['cos_horaxx'];
							        $dataTable[$fechaHora][$interV][$cod_usuari][$valuehora]['fec_regist'] = $fechaHora;
							        $dataTable[$fechaHora][$interV][$cod_usuari][$valuehora]['hor_regist'] = $valuehora;
							        $dataTable[$fechaHora][$interV][$cod_usuari][$valuehora]['can_casosx'] = 0;
								}
							}
							ksort($dataTable[$fechaHora][$interV]);
	    				}
						ksort($dataTable[$fechaHora]);
					}
					ksort($dataTable);
	    		}
	    	}
    	}
    }

    //Recoore la data para poder crear las tabals basados en las fechas
    foreach ($dataTable as $fecha => $dataH) {
    	foreach ($dataH as $inter => $dataI) {
    		$html .= '<table id="'.$fecha.'_'.$inter.'" class="table table-bordered tab_'.$idTable.' table_datatables">
	                <thead>
	                    <tr>
	                      <th colspan="100" class="tituloFecha">'.$titulo.$fecha.' '.str_replace(["_", "-"],[" a ", ":"],$inter).'</th>
	                    </tr>
	                    <tr>
	                      <th rowspan="2" class="buscar subtitbusq">Cod. Perfil</th> 
	                      <th rowspan="2" class="buscar subtitbusq">Usuario</th>
	                      <th rowspan="2" class="buscar subtitbusq">Costo Hora</th>';
	      //Crea variables necesarias
	      $th1 = '';
	      $th2 = '';
	      $tbody = '';
	      $usr_anteri = '';
	      $ban = 0;
	      foreach ($dataI  as $cod_usuari => $dataC) {
	        foreach ($dataC as $hora => $dataD) {
	        	//Crea un estado para crear los titulos solo la primera vez
	        	if ($ban == 0) {
	        		$th1 .= '<th colspan="2" class="tutuloHora">'.$hora.'</th>';
			        $th2 .= '<th class="buscar subtitbusq">Registros</th>';
			        $th2 .= '<th class="buscar subtitbusq">Valor total</th>';
	        	}

	        	if (empty($usr_anteri)) {
	        		$tbody .= '<tr>
		        				<td>'.$dataD['cod_perfil'].'</td>
		        				<td>'.$dataD['cod_usuari'].'</td>
		        				<td>$'.$dataD['cos_horaxx'].'</td>
		        				<td class="btn-link" onclick="detalle(\''.$fecha.'\', \''.$hora.'\', \''.$dataD['cod_usuari'].'\', \''.$_REQUEST['tip_inform'].'\')">'.$dataD['can_casosx'].'</td>
		        				<td>$'.$dataD['cos_horaxx']*$dataD['can_casosx'].'</td>
		        		  	 ';
	        	}else if ($usr_anteri != $cod_usuari) {
	        		$tbody .= '</tr><tr>
		        				<td>'.$dataD['cod_perfil'].'</td>
		        				<td>'.$dataD['cod_usuari'].'</td>
		        				<td>$'.$dataD['cos_horaxx'].'</td>
		        				<td class="btn-link" onclick="detalle(\''.$fecha.'\', \''.$hora.'\', \''.$dataD['cod_usuari'].'\', \''.$_REQUEST['tip_inform'].'\')">'.$dataD['can_casosx'].'</td>
		        				<td>$'.$dataD['cos_horaxx']*$dataD['can_casosx'].'</td>
		        		  	 ';	  	 
	        	}else{
	        		$tbody .= '<td class="btn-link" onclick="detalle(\''.$fecha.'\', \''.$hora.'\', \''.$dataD['cod_usuari'].'\', \''.$_REQUEST['tip_inform'].'\')">'.$dataD['can_casosx'].'</td>
	        				   <td>$'.$dataD['cos_horaxx']*$dataD['can_casosx'].'</td>';
	        		
	        	}
	        	$usr_anteri = $cod_usuari;
	        }
	        $tbody.'</tr>';
	        $ban++;
	      }

	      $html .= $th1.'</tr><tr>'.$th2.'</tr>';

	      $html .=' 
	                
	              </thead>
	              <tbody>'.$tbody.'</tbody>
	              <tfoot>
			            <tr>
			                <th colspan="2" class="tituloFecha" style="text-align:right">Total:</th>
			            </tr>
			        </tfoot>
	          </table>';
    	}
    }
    
    return $html;
  }

  /*! \fn: informeDiferencia
   *  \brief: Crea la tabla para el informe por diferencia
   *  \author: Ing. Luis Manrique
   *  \date: 12/05/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */

  private function informeDiferencia($mMatriz, $idTable){
  	try {
  		//Declara variables necesarias
	    $dataTable = [];

	    //Recorre el arreglo
	    foreach ($mMatriz as $identi => $data) {
	      
	    //Valida la existencia de la posición en el arreglo para sumar la cantidad de casos   
	      if(isset($dataTable[$data['cod_transp']])){
	        $dataTable[$data['cod_transp']]['can_despac']++;
	        $dataTable[$data['cod_transp']]['can_noveda'] += intval($data['can_noveda']);
	      }else{
	        $dataTable[$data['cod_transp']]['cod_transp'] = $data['cod_transp'];
	        $dataTable[$data['cod_transp']]['nom_tercer'] = $data['nom_tercer'];
	        $dataTable[$data['cod_transp']]['tip_modali'] = $data['tip_modali'];
	        $dataTable[$data['cod_transp']]['val_unitar'] = $data['val_unitar'];
	        $dataTable[$data['cod_transp']]['can_noveda'] = intval($data['can_noveda']);
	        $dataTable[$data['cod_transp']]['can_despac'] = 1;
	      }
	    }


	    //Crea la tabla en HTML
	    $html = '<table id="diferencia" class="table table-bordered tab_'.$idTable.' table_datatables">
	              <thead>
	                  <tr>
	                    <th class="buscar subtitbusq">Nit</th> 
	                    <th class="buscar subtitbusq">Empresa</th>
	                    <th class="buscar subtitbusq">Modalidad</th>
	                    <th class="buscar subtitbusq">Val Unit.</th>
	                    <th class="buscar subtitbusq">Cant Despachos</th>
	                    <th class="buscar subtitbusq">Cant Novedades</th>
	                    <th class="buscar subtitbusq">Val fact Despacho</th>
	                    <th class="buscar subtitbusq">Val fact Novedad</th>
	                    <th class="buscar subtitbusq">Prom Nov x Desp</th>
                      <th class="buscar subtitbusq">Valor Nov x Desp</th>
	                  </tr>
	              <tbody>';

	    foreach ($dataTable as $key => $data) {
	      //Variables necesarias 
	      $data['val_facdes'] = $data['tip_modali'] == "Despacho" ? $data['val_unitar']*$data['can_despac'] : 0;
	      $data['val_facnov'] = $data['tip_modali'] == "Novedad" ? $data['val_unitar']*$data['can_noveda'] : 0;
	      $data['pro_novdes'] = ($data['can_noveda']/$data['can_despac']);
        $data['val_novdes'] = ($data['val_unitar']*$data['can_despac'])/$data['can_noveda'];

	      $html .= '<tr>
	                  <td>'.$data['cod_transp'].'</td>
	                  <td>'.$data['nom_tercer'].'</td>
	                  <td>'.$data['tip_modali'].'</td>
	                  <td>$'.$data['val_unitar'].'</td>
	                  <td>'.$data['can_despac'].'</td>
	                  <td>'.$data['can_noveda'].'</td>
	                  <td>$'.$data['val_facdes'].'</td>
	                  <td>$'.$data['val_facnov'].'</td>
	                  <td>'.round($data['pro_novdes']).'</td>
                    <td>$'.round($data['val_novdes']).'</td>
	                </tr>';
	    }
	    $html .='</tbody>
	                <tfoot>
	                  <tr>
	                      <th colspan="3" class="tituloFecha" style="text-align:right">Total:</th>
	                  </tr>
	              </tfoot>
	            </table>
	          </div>
	        </div>';
	    
	    return $html;
  	} catch (Exception $e) {
  		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
  	}
	    
  }

  /*! \fn: regDetalle
   *  \brief: Crea la tabla para visualizar la información al detalle
   *  \author: Ing. Luis Manrique
   *  \date: 12/05/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */

  private function regDetalle(){
    try {

      //Declarar variables necesarias
      $usuarios = '';
   	  
   	  //Se recorre los usuarios para capturarlos y asignales la coma
      foreach ($_REQUEST as $key => $value) {
      	if ($key == 'usuario') {
      		if (empty($usuarios)) {
	      		$usuarios = "'".$value."'";
	      	}else{
	      		$usuarios .= ",'".$value."'";
	      	}
      	}
      }

      //Se asigna la condición a la consulta dependiendo del tipo de informe
      switch ($_REQUEST['tipo']) {
      	case 'Diario':
      		$noveda = "AND DATE(a.fec_noveda) = '".$_REQUEST['fecha']."' AND HOUR(a.fec_noveda) = '".$_REQUEST['hora']."' ";
      		$contro = "AND DATE(a.fec_contro) = '".$_REQUEST['fecha']."' AND HOUR(a.fec_contro) = '".$_REQUEST['hora']."'";
      		$titulo = "Informe del dia ".$_REQUEST['fecha']." del usuario ".$_REQUEST['usuario'];
      		break;
      	case 'Semanal':
    		$noveda = "AND WEEK(a.fec_noveda,6) = '".$_REQUEST['fecha']."' AND HOUR(a.fec_noveda) = '".$_REQUEST['hora']."' ";
      		$contro = "AND WEEK(a.fec_contro,6) = '".$_REQUEST['fecha']."' AND HOUR(a.fec_contro) = '".$_REQUEST['hora']."' ";
      		$titulo = "Informe de la semana ".$_REQUEST['fecha']." del usuario ".$_REQUEST['usuario'];
    		break;   
		case 'Mensual':
			$noveda = "AND MONTH(a.fec_noveda) = '".$_REQUEST['fecha']."' AND HOUR(a.fec_noveda) = '".$_REQUEST['hora']."' ";
      		$contro = "AND MONTH(a.fec_contro) = '".$_REQUEST['fecha']."' AND HOUR(a.fec_contro) = '".$_REQUEST['hora']."' ";
      		$titulo = "Informe del mes ".$_REQUEST['fecha']." del usuario ".$_REQUEST['usuario'];
			break;  
      }

      //Crea la consulta
      $mSql = ' SELECT  b.cod_transp,
                        d.cod_usuari,
                        e.val_hordia AS cos_horaxx,
                        SUM(1) AS can_noveda
                  FROM  '.BASE_DATOS.'.tab_despac_despac a
            INNER JOIN 	'.BASE_DATOS.'.tab_despac_vehige b
            		ON 	a.num_despac = b.num_despac
            			AND a.fec_salida IS NOT NULL 
                        AND (
                            a.fec_llegad IS NOT NULL 
                            OR a.fec_llegad != "0000-00-00 00:00:00"
                        ) 
                        AND a.ind_planru = "S" 
                        AND a.ind_anulad in ("R") 
                        AND b.ind_activo = "S"
            INNER JOIN 	(
            				(
            					SELECT 	a.num_despac, 
            							a.cod_noveda, 
            							a.fec_noveda, 
            							a.usr_creaci
            					 FROM 	'.BASE_DATOS.'.tab_despac_noveda a
            					WHERE 	a.usr_creaci IN ('.$usuarios.')
            							'.$noveda.'

            				)UNION(
            					SELECT 	a.num_despac, 
            							a.cod_noveda, 
            							a.fec_contro AS fec_noveda, 
            							a.usr_creaci
            					 FROM 	'.BASE_DATOS.'.tab_despac_contro a
            					WHERE 	a.usr_creaci IN ('.$usuarios.') 
            							'.$contro.'
            				)
            			) c
            		ON 	a.num_despac = c.num_despac
            INNER JOIN 	'.BASE_DATOS.'.tab_genera_usuari d
            		ON 	d.cod_usuari = c.usr_creaci
           	INNER JOIN 	'.BASE_DATOS.'.tab_hojvid_ctxxxx e
           			ON 	d.cod_usuari = e.usr_asigna
           	  GROUP BY 	b.cod_transp
            ';
      
      //Ejecuta la consulta
      $mMatriz = new Consulta($mSql, $this->conexion);
      $mMatriz = $mMatriz->ret_matrix("a");

      //Crea la tabla en HTML
      $html .= '<div class="panel panel-default">
      				<div class="panel-heading">
                     <h4>'.$titulo.'</h4>
                    </div>
                    <div class="panel-body">
	      				<table id="tab_'.$_REQUEST['usuario'].'" class="table table-bordered table_datadetalle">
			                <thead>
			                    <tr>
			                      <th class="buscar subtitbusq">Transportadora</th> 
			                      <th class="buscar subtitbusq">Usuario</th>
			                      <th class="buscar subtitbusq">Costo Registro</th>
			                      <th class="buscar subtitbusq">Registro Generados</th>
			                      <th class="buscar subtitbusq">Total</th>

			                    </tr>
			                <tbody>';

			    foreach ($mMatriz as $key => $data) {
			      	$html .= '<tr>
			      				<td>'.$data['cod_transp'].'</td>
			      				<td>'.$data['cod_usuari'].'</td>
			      				<td>'.$data['cos_horaxx'].'</td>
			      				<td>'.$data['can_noveda'].'</td>
			      				<td>'.$data['can_noveda']*$data['cos_horaxx'].'</td>
			      			  </tr>';
			    }
				   $html .='</tbody>
				              <tfoot>
						            <tr>
						                <th colspan="2" class="tituloFecha" style="text-align:right">Total:</th>
						            </tr>
						        </tfoot>
				          </table>
				        </div>
			        </div>';

	  //Devuelve el valor
      echo utf8_decode($html);
    } catch (Exception $e) {
    	echo 'Excepción capturada: ',  $e->getMessage(), "\n";
    }
  }

   /*! \fn: crearCampos
   *  \brief: Crea el formulario de para registrar información
   *  \author: Ing. Luis Manrique
   *  \date: 27/04/2020
   *  \date modified:
   *  \param: 
   *  \return: HTML 
   */
    function crearCampos(){
      try { 
      	//Valida si existe codgi de tipo de cuenta para consultarlos o crearlos en vacio
          $mData = $arrayName = array( 
                                      'fec_inicia' => '',
                                      'hor_inicia' => '',
                                      'fec_finalx' => '',
                                      'hor_finalx' => '',
                                      'nom_perfil' => '', 
                                      'tip_inform' => '',
                                      'cod_usuari' => ''
                                    );   
                                    
        //arrays que contienen la información de los campos y los titulos del formulario
          $mTittle[0] = array( "id" => "dat_filtro", "tittle" => "Información");
          $mTittle[0]['data'] = array("fec_inicia" => array(
	                                                            'name' => "Fecha Inicial", 
	                                                            'type' => "input",
	                                                            'class' => "validate date", 
	                                                            'atribute' => array(
	                                                                "validate"=>"date",
	                                                                "minlength"=>3,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "hor_inicia" => array(
                                                              'name' => "Hora Inicial", 
                                                              'type' => "input",
                                                              'class' => "validate hora", 
                                                              'atribute' => array(
                                                                  "validate"=>"hora",
                                                                  "minlength"=>3,
                                                                  "obl"=> 1,
                                                                  "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "fec_finalx" => array(
	                                                            'name' => "Fecha Final",
	                                                            'type' => "input",
	                                                            'class' => "validate date", 
	                                                            'atribute' => array(
	                                                                "validate"=>"date",
	                                                                "minlength"=>1,
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "hor_finalx" => array(
                                                              'name' => "Hora Final", 
                                                              'type' => "input",
                                                              'class' => "validate hora", 
                                                              'atribute' => array(
                                                                  "validate"=>"hora",
                                                                  "minlength"=>1,
                                                                  "obl"=> 1,
                                                                  "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "tip_inform" => array(
                                                              'name' => "Tipo de informe", 
                                                              'type' => "select",
                                                              'class' => "validate list", 
                                                              'atribute' => array(
                                                                  "validate"=>"select",
                                                                  "obl"=> 1,
                                                                  "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                      "nom_perfil" => array(
	                                                            'name' => "Perfl", 
	                                                            'type' => "select",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"select",
                                                                  "multiple"=>"multiple",
	                                                                "obl"=> 1,
	                                                                "onkeyup" => "validateFields(this)"
                                                                )
                                                            ),
                                       "cod_usuari" => array(
	                                                            'name' => "Usuario", 
	                                                            'type' => "select",
	                                                            'class' => "validate list", 
	                                                            'atribute' => array(
	                                                                "validate"=>"select",
                                                                  "multiple"=>"multiple",
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


    //---------------------------------------------
  /*! \fn: dataList
  *  \brief: Retorna las listas para los select
  *  \author: Ing. Luis Manrique
  *  \date: 27/04/2020
  *  \date modified:
  *  \return BOOL
  */
  private function dataList() {
    try {
      //Variables necesarias
      $where = '';
      $arrayManual = [];
      $return = [];

      switch ($_REQUEST['file']) {
        case 'nom_perfil':
          $table = 'tab_genera_perfil';
          $cod = "cod_".explode("_", $_REQUEST['file'])[1];
          break;

        case 'cod_usuari':
          if(count($_REQUEST['dependencia']) > 0){
            $where = " AND a.cod_perfil IN(".$_REQUEST['dependencia'].")";  
          }
          $table = 'tab_genera_usuari';
          $cod = "cod_".explode("_", $_REQUEST['file'])[1];
          break;

        case 'tip_inform':
          $cod = "cod_".explode("_", $_REQUEST['file'])[1];
          $arrayManual = array(
                                0 => array(
                                          'cod_inform' => 'Diario',
                                          'tip_inform' => 'Diario'
                                          ),
                                1 => array(
                                          'cod_inform' => 'Semanal',
                                          'tip_inform' => 'Semanal'
                                          ),
                                2 => array(
                                          'cod_inform' => 'Mensual',
                                          'tip_inform' => 'Mensual'
                                          )
                              );
          break;
      }

      //Valida si el campo es manual o se crea por consulta
      if (count($arrayManual) > 0) {
        $mMatriz = $arrayManual;
      }else{
        //Consulta en BD
        $mSql = " SELECT  a.".$cod.",
                          a.".$_REQUEST['file']."
                    FROM  ".BASE_DATOS.".$table a
                   WHERE  a.".$_REQUEST['file']." LIKE '%".$_REQUEST['term']."%' AND ind_estado = 1".$where;

          
        $mMatriz = new Consulta($mSql, $this->conexion);
        $mMatriz = $mMatriz->ret_matrix("a");
      }


      foreach ($mMatriz as $key => $datos) {
        $return[$key]['text'] = $datos[$_REQUEST['file']];  
        $return[$key]['id'] = $datos[$cod];  
        $return[$key]['campo'] = $cod;  
      }

      echo json_encode($return);
    } catch (Exception $e) {
      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
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
      case 'select':
        //Recorre la posicion de atributos para asignalos al campo

        $multiple = '';
        foreach ($info['atribute'] as $nameAttr => $valueAttr) {
          $attr .= $nameAttr.'="'.$valueAttr.'" ';
          if ($nameAttr == 'multiple') {
            $multiple = '[]';
          }
        }

        $field = '<div class="form-group '.$classCol.'">
              <select class="form-control form-control-sm '.$info['class'].'" '.$attr.' name="'.$name.$multiple.'" id="'.$name.'" value="'.$value.'" style="height: auto;"></select>
            </div>';
        break;
		}

		//Se retorna el campo
    	return $field;
    }
  
  
  
}

$proceso = new ajax_rentab_produc();
 ?>
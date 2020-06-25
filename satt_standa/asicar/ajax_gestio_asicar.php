<?php
    /****************************************************************************
    NOMBRE:   AjaxGestioAsiscar
    FUNCION:  Retorna todos los datos necesarios para construir la información
    FECHA DE MODIFICACION: 13/04/2020
    CREADO POR: Ing. Cristian Andrés Torres
    MODIFICADO 
    ****************************************************************************/
    
    /*ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1);*/

    class AjaxGestioAsiscar
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
            switch($_REQUEST[opcion])
            {
                case "1":
                    self::informes();
                break;

                case "2":
                    self::loadFields();
                break;
                case "3":
                    self::traerFormulSolici();
                break;
                case "4":
                    self::respuestasBitacora();
                break;
                case "5":
                    self::registro();
                break;
                case "6":
                    self::FormulSegunEstado();
                break;
            }
        }

        /*! \fn: informes
           *  \brief: Genera la informaci�n para las tablas de los informes
           *  \author: Ing. Cristian Torres
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */

        function informes(){

            switch ($_REQUEST['tipoInforme']) {
                case 'gen':
                    $json = self::informeGeneral();
                    break;
                case 'esp':
                    $json = self::informeEspecifico();
                    break;
                case 'porGest':
                    $json = self::informePorGestion();
                    break;
                case 'porGestInvidu':
                    $json = self::informePorGestionIndivi();
                break;
                case 'mod':
                    $json = self::informeModal();
                    break;
                default:
                    $json = self::informeGeneral();
                    break;
            }
            
            echo $json;
        }


        function registro(){

            switch ($_REQUEST['tipoSol']) {
                case 'porGestio':
                    $json = self::registroGestioSolici();
                    break;
                case 'porAprobCliente':
                    $json = self::registroPorAprobaCli();
                    break;
            }
            
            echo $json;
        }


        /*! \fn: informeGeneral
           *  \brief: Genera la información para las tabla del informe general
           *  \author: Ing. Cristian Torres
           *  \date: 04-06-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */

        function contarSegunEstado(){
            
        }

        function informeGeneral(){
            //Create total query 
            $query = "SELECT 
            COUNT(*) AS 'total', 
            SUM(
                IF(a.est_solici = '1', 1, 0)
            ) as 'por_gestio', 
            SUM(
                IF(a.est_solici = '2', 1, 0)
            ) as 'por_aprcli', 
            SUM(
                IF(a.est_solici = '3', 1, 0)
            ) as 'por_asipro', 
            SUM(
                IF(a.est_solici = '4', 1, 0)
            ) as 'enx_proces', 
            SUM(
                IF(a.est_solici = '5', 1, 0)
            ) as 'xxx_finali' 
        FROM 
            ".BASE_DATOS.".tab_asiste_carret a WHERE 1=1";
            
            if($_REQUEST["num_solici"] != "" ){
                $query .= " AND a.id='".$_REQUEST["num_solici"]."'";
            }
            if($_REQUEST["cars"]!="Seleccione"){
              $query .= " AND a.cod_transp='".$_REQUEST["cars"]."'";
            }
            if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                $query .= "
                   AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'
                   ";
            }

            
            //Generate consult

            $query = new Consulta($query, self::$conexion);
            $datos = $query -> ret_matrix('a');

            //Valriable para capturar el valor anterior
            $valorAnterior = '';

            //Recorre consulta para asignar valores
                foreach ($data as $campo => $valor) {
                    //Identifica si el campo es vacio
                    if ($valor == '') {
                        //Asigna valor para calcular el porcentaje
                        $despachos[$ident][$campo] = round($valorAnterior / reset($data)*100);
                    }
                    $valorAnterior = $valor;
                }

            $json = json_encode($datos);

            $_SESSION["dashboard"][1]["table"] = $json;
            $_SESSION["dashboard"][1]["filter"] = json_encode(self::cleanArray($_REQUEST));
            // header('Content-Type: application/json');
            return $json;
        }

        /*! \fn: informeEspecifico
           *  \brief: Genera la informaci�n para las tabla del informe especifico por dia segun los filtros
           *  \author: Ing. Cristian Torres
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */
        function informeEspecifico(){

                $query="SELECT 
                b.abr_tercer,
                COUNT(*) AS 'total', 
                SUM(
                    IF(a.est_solici = '1', 1, 0)
                ) as 'por_gestio', 
                SUM(
                    IF(a.est_solici = '2', 1, 0)
                ) as 'por_aprcli', 
                SUM(
                    IF(a.est_solici = '3', 1, 0)
                ) as 'por_asipro', 
                SUM(
                    IF(a.est_solici = '4', 1, 0)
                ) as 'enx_proces', 
                SUM(
                    IF(a.est_solici = '5', 1, 0)
                ) as 'xxx_finali' 
            FROM 
                ".BASE_DATOS.".tab_asiste_carret a 
                INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_client = b.cod_tercer
                WHERE 1=1";

                if($_REQUEST["num_solici"] != "" ){
                    $query .= " AND a.id='".$_REQUEST["num_solici"]."'";
                }

                if($_REQUEST["cars"]!="Seleccione"){
                  $query .= " AND a.cod_transp='".$_REQUEST["cars"]."'";
                }

                if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                    $query .= "
                       AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
                }

                $query.="GROUP BY a.cod_client";

                $query = new Consulta($query, self::$conexion);
                $despachos = $query -> ret_matrix('a');

            $despachos = self::cleanArray($despachos);
            $json = json_encode($despachos);

            $_SESSION["dashboard"][1]["table"] = $json;
            $_SESSION["dashboard"][1]["filter"] = json_encode(self::cleanArray($_REQUEST));

            return $json;
        }

        function informePorGestion(){
            $est_solici = $_REQUEST['code'];
            $nom_tablax = $_REQUEST['tabla'];
            $html .= '<thead>
                     <tr>
                        <th colspan="9" style="background-color:#dff0d8; color: #000"><center>SERVICIO SOLICITADO<center></th>
                     </tr>
                     <tr>
                        <th><center>CLIENTE</center></th>';

            $sql="SELECT a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado = 1;";
            $sql = new Consulta($sql, self::$conexion);
            $datos = $sql->ret_matriz('a');
            $datos = self::cleanArray($datos);
            foreach($datos as $dato){
                $html .= "<th><center>".$dato[0]."</center></th>";
            }
            $html .= '</tr></thead>';
            $html .= '<tbody id="'.$nom_tablax.'">';

            $query="SELECT 
                    b.cod_tercer, b.abr_tercer 
                    FROM 
                    ".BASE_DATOS.".tab_asiste_carret a
                    LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_client = b.cod_tercer
                    WHERE 1=1 ";
            if($_REQUEST["num_solici"] != "" ){
                $query .= " AND a.id='".$_REQUEST["num_solici"]."'";
            }
            if($_REQUEST["cars"]!="Seleccione"){
              $query .= " AND a.cod_transp='".$_REQUEST["cars"]."'";
            }
            if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                $query .= " AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
            }
            $query.=" AND a.est_solici = $est_solici GROUP BY a.cod_client";
            $query = new Consulta($query, self::$conexion);
            $empresas = $query -> ret_matrix('a');
            $tip_formul = $this->darFormularios();
            foreach($empresas as $empresa){
                $html.="<tr>";
                $html.="<td class='emp_solici' style='text-align: center;'>".$empresa['abr_tercer']."</td>";
                foreach($tip_formul as $formul){
                    $query="SELECT COUNT(*) as 'total' 
                    FROM ".BASE_DATOS.".tab_asiste_carret a 
                    WHERE a.cod_client = '".$empresa['cod_tercer']."' 
                    AND a.tip_solici = '".$formul['id']."' AND a.est_solici = $est_solici";
                    if($_REQUEST["num_solici"] != "" ){
                        $query .= " AND a.id='".$_REQUEST["num_solici"]."'";
                    }
                    if($_REQUEST["cars"]!="Seleccione"){
                      $query .= " AND a.cod_transp='".$_REQUEST["cars"]."'";
                    }
                    if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                        $query .= " AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
                    }
                    $query = new Consulta($query, self::$conexion);
                    $total = $query -> ret_matrix('a');
                    $html.="<td class='can_totalx enlace-form' style='text-align: center;' onclick='resultaIndiviua(".$formul['id'].",".$empresa['cod_tercer'].",$est_solici)'>".$total[0]['total']."</td>";
                }
                $html .="</tr></tbody>";
            }
            
           echo json_encode($html);
        }

        function informePorGestionIndivi(){
            $cod_client = $_REQUEST['cod_client'];
            $cod_solici = $_REQUEST['cod_asiste'];
            $nom_tablax = $_REQUEST['tabla'];
            $cod_estado = $_REQUEST['code'];
            $html = '
            <thead>
                     <tr>
                        <th colspan="9" style="background-color:#dff0d8; color: #000"><center>SERVICIO SOLICITADO<center></th>
                     </tr>
                     <tr>
                        <th><center>NO. DE SOLICITUD</center></th>
                        <th><center>TIPO DE SOLICITUD</center></th>
                        <th><center>NOMBRE DEL CLIENTE</center></th>
                        <th><center>NOMBRE DEL SOLICITANTE</center></th>
                        <th><center>FECHA Y HORA DE SOLICITUD</center></th>
                        <th><center>USUARIO</center></th>
                    </tr>
            </thead>';

            $query = "SELECT 
            a.id, 
            b.nom_asiste, 
            c.abr_tercer, 
            a.nom_solici, 
            a.fec_creaci, 
            a.usu_creaci 
        FROM 
            ".BASE_DATOS.".tab_asiste_carret a 
            INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON a.cod_client = c.cod_tercer 
        WHERE 
            a.est_solici = '$cod_estado' 
            AND a.cod_client = '$cod_client' 
            AND a.tip_solici = '$cod_solici'";

            if($_REQUEST["num_solici"] != "" ){
                $query .= " AND a.id='".$_REQUEST["num_solici"]."'";
            }
            if($_REQUEST["cars"]!="Seleccione"){
              $query .= " AND a.cod_transp='".$_REQUEST["cars"]."'";
            }
            if($_REQUEST["fec_finxxx"] != "" && $_REQUEST["fec_inicio"] != ""){
                $query .= " AND DATE(a.fec_creaci) BETWEEN '".$_REQUEST["fec_inicio"]."' AND '".$_REQUEST["fec_finxxx"]."'";
            }
            $query = new Consulta($query, self::$conexion);
            
            $datos = $query -> ret_matriz('a');
            $html .='<tbody id="'.$nom_tablax.'">';
            $datos = self::cleanArray($datos);
            foreach($datos as $dato){
                $html.='<tr>
                            <td class="enlace-form" style="text-align: center;" onclick="abrModalPorGestio('.$dato['id'].','.$cod_estado.')">'.$dato['id'].'</td>
                            <td style="text-align: center;">'.$dato['nom_asiste'].'</td>
                            <td style="text-align: center;">'.$dato['abr_tercer'].'</td>
                            <td style="text-align: center;">'.$dato['nom_solici'].'</td>
                            <td style="text-align: center;">'.$dato['fec_creaci'].'</td>
                            <td style="text-align: center;">'.$dato['usu_creaci'].'</td>
                        </tr>';
            }
            $html .='</tbody>';

            echo json_encode($html);
            
        }

        function FormulSegunEstado(){
            $cod_estado = $_REQUEST['cod_estado'];
            $information = $this->darInformacion($_REQUEST['cod_solici']);
            $nom_solici = $this->tipSolicitud($_REQUEST['cod_solici']);
            if($cod_estado == 1){
                $html='<div class="card border border-success" style="margin:15px;">
                <div class="card-header color-heading text-align">
                  Gestion de Solicitud
                </div>
              <div class="card-body">
                <form id="PorGestio" enctype="multipart/form-data" method="POST">
                <div class="row">
                  <div class="offset-1 col-10">
                  <textarea class="form-control" id="obs_gestioID" name="obs_gestio" rows="3" placeholder="Observaciones" required></textarea>
                  </div>
                </div>
      
                <div class="row mt-4">
                  <div class="offset-1 col-4">
                    <input class="form-control form-control-sm" type="number" placeholder="Valor a Facturar" id="val_facturID" name="val_factur" onchange="llenarRetabilidad()" required>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="number" placeholder="Costo Servicio Proveedor" id="val_cosserID" name="val_cosser" onchange="llenarRetabilidad()" required>
                  </div>
                  <div class="col-2">
                    <input class="form-control form-control-sm" type="text" placeholder="Rentabilidad" id="val_rentabID" name="val_rentab" disabled>
                  </div>
                </div>
      
                <div class="row mt-4">
                    <div class="offset-1 col-6">
                            <label for="exampleFormControlFile1" style="text-align:left !important;font-size: 15px;">Adjuntar Archivo</label>
                            <input type="file" class="form-control-file" id="adjuntoFileID" name="adjuntoFile" accept="application/pdf">
                    </div>
                    <div class="col-4 label-formcheck rounded border border-warning">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="verFinSolici" onchange="razonFinali()">
                            <label class="form-check-label" for="defaultCheck1" style="text-align:left !important;font-size: 15px;">
                            Cerrar Solicitud
                            <input type="hidden" name="cod_solici" id="cod_soliciID" value="">
                            </label>
                        </div>
                    </div>
                </div>
                <div id="rzn-fin">
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                            <center><button class="small-box-footer btn btn-success btn-sm" onclick="PorGestioValidate()">Enviar</button></center>
                    </div>
                </div>
                </form>
              </div>
              </div>';
            }else if($cod_estado == 2){
                $html='<div class="card border border-success" style="margin:15px;">
                <div class="card-header color-heading text-align">
                  <center>APROBAR SOLICITUD</center>
                </div>
              <div class="card-body">
                <form id="PorAprobarCliente" enctype="multipart/form-data" method="POST">
                <div class="row">
                  <div class="offset-1 col-10">
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <label for="AproServicio">Aprobar Servicio </label>
                            <input type="radio" value="1" class="form-check-input" name="AproServicio" required>Si
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" value="0" class="form-check-input" name="AproServicio">No
                         </label>
                    </div>
                  </div>
                </div>

                <div class="row mt-2">
                  <div class="offset-1 col-10">
                  <textarea class="form-control" id="obs_aprserID" name="obs_aprser" rows="3" placeholder="Observaciones" required></textarea>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="offset-1 col-4">
                  <label for="AproServicio"> Costo '.$nom_solici.':</label>
                    <input class="form-control form-control-sm" type="number" placeholder="Costo Aproximado Servicio" id="costAproxServicioID" name="costAproxServicio"  value="'.$information['val_facges'].'" readonly disabled>
                  </div>
                </div>
      
                <div class="row mt-4">
                    <div class="offset-1 col-6">
                            <label for="exampleFormControlFile1" style="text-align:left !important;font-size: 15px;">Adjuntar Archivo</label>
                            <input type="file" class="form-control-file" id="adjuntoFileID" name="adjuntoFile" accept="application/pdf">
                    </div>
                    <div class="col-4 label-formcheck rounded border border-warning">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="verFinSolici" onchange="razonFinali()">
                            <label class="form-check-label" for="defaultCheck1" style="text-align:left !important;font-size: 15px;">
                            Cerrar Solicitud
                            <input type="hidden" name="cod_solici" id="cod_soliciID" value="">
                            </label>
                        </div>
                    </div>
                </div>
                <div id="rzn-fin">
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                    <center><button class="small-box-footer btn btn-success btn-sm" onclick="PorAprobValidate()">Enviar</button></center>
                    </div>
                </div>
                </form>
              </div>
              </div>';
            }else if($cod_estado == 3){
              $porcentaje=round(($information['val_cospro']/$information['val_facges'])*100);
              $html='
              <div class="card border border-success" style="margin:15px;">
              <div class="card-header color-heading text-align">
                <center>ASIGNACION A PROVEEDOR</center>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Numero de documento" id="num_proveeID" name="num_provee" required>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Nombre Proveedor" id="nom_proveeID" name="nom_provee" required>
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Primer Apellido" id="ape_prove1ID" name="ape_prove1" required>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Segundo Apellido" id="ape_prove2ID" name="ape_prove2" required>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Numero Celular 1" id="num_celul1ID" name="num_celul1" required>
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Numero Celular 2" id="num_celul2ID" name="num_celul2" required>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="offset-1 col-4">
                    <input class="form-control form-control-sm" type="number" placeholder="Valor a Facturar" id="val_facturID" name="val_factur" value="'.$information['val_facges'].'" onchange="llenarRetabilidad()" required>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="number" placeholder="Costo Servicio Proveedor" id="val_cosserID" name="val_cosser" value="'.$information['val_cospro'].'" onchange="llenarRetabilidad()" required>
                  </div>
                  <div class="col-2">
                    <input class="form-control form-control-sm" type="text" placeholder="Rentabilidad" id="val_rentabID" name="val_rentab" disabled value="'.$porcentaje.'%">
                  </div>
                </div>

              </div>
              </div>
              
                      <div class="card border border-success" style="margin:15px;">
                        <div class="card-header color-heading text-align">
                          <center>ASIGNACION DE FORMULARIOS DE INSPECCIÓN</center>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="offset-1 col-10">
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                  <label><input type="checkbox" value=""> Inspeccion a la cabina</label>&nbsp;&nbsp;&nbsp;
                                  <label><input type="checkbox" value=""> Inspeccion al Vehiculo</label>&nbsp;&nbsp;&nbsp;
                                  <label><input type="checkbox" value=""> Fotos al vehiculo</label>&nbsp;&nbsp;&nbsp;
                                  <label><input type="checkbox" value=""> Estado mercancia</label>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="card border border-success" style="margin:15px;">
                        <div class="card-header color-heading text-align">
                          <center>CERRAR SIN GESTIONAR</center>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="offset-1 col-10">
                              <textarea class="form-control" id="obs_gestioID" name="obs_gestio" rows="3" placeholder="Observaciones" required></textarea>
                            </div>
                          </div>

                          <div class="row mt-4">
                    <div class="offset-1 col-6">
                            <label for="exampleFormControlFile1" style="text-align:left !important;font-size: 15px;">Adjuntar Archivo</label>
                            <input type="file" class="form-control-file" id="adjuntoFileID" name="adjuntoFile" accept="application/pdf">
                    </div>
                    <div class="col-4 label-formcheck rounded border border-warning">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="verFinSolici" onchange="razonFinali()">
                            <label class="form-check-label" for="defaultCheck1" style="text-align:left !important;font-size: 15px;">
                            Cerrar Solicitud
                            <input type="hidden" name="cod_solici" id="cod_soliciID" value="">
                            </label>
                        </div>
                    </div>
                </div>

                        </div>
                      </div>'
                      ;
            }
            echo json_encode($html);
        }

        function darInformacion($cod_solici){
          $sql="SELECT * FROM tab_asiste_carret WHERE id=$cod_solici";
          $query = new Consulta($sql, self::$conexion);
          $respuestas = $query -> ret_matrix('a')[0];
          $respuestas = self::cleanArray($respuestas);
          return $respuestas;
        }

        function darFormularios(){
            $sql="SELECT a.id FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado = 1 ORDER BY a.id ASC;";
            $query = new Consulta($sql, self::$conexion);
            $tip_formul = $query -> ret_matrix('a');
            return $tip_formul;
        }


        /*! \fn: loadFields
           *  \brief: Genera los campos necesarios en el modulo
           *  \author: Ing. Luis Manrique
           *  \date: 13-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: 
           *  \return: json
        */

        function loadFields(){
            $cod_solici = $_REQUEST['cod_solici'];
            $sql="SELECT *,b.nom_asiste FROM ".BASE_DATOS.".tab_asiste_carret a LEFT JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id WHERE a.id = '$cod_solici'";
            $query = new Consulta($sql, self::$conexion);
            $informacion = $query -> ret_matrix('a');
            $json = json_encode($informacion);
            echo $json;
        }

        function traerFormulSolici(){
            $tip_solici = $_REQUEST['tip_solici'];
            $cod_solici = $_REQUEST['cod_solici'];
            $sql="SELECT *,b.nom_asiste FROM ".BASE_DATOS.".tab_asiste_carret a LEFT JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id WHERE a.id = '$cod_solici'";
            $query = new Consulta($sql, self::$conexion);
            $informacion = $query -> ret_matrix('a')[0];
            if($tip_solici==1){
            $html ='<div class="card text-center" style="margin:15px;">
            <div class="card-header color-heading">Ubicación del Vehículo</div>
            <div class="card-body">
              <div class="row">
                <div class="offset-1 col-3">
                  <input class="form-control form-control-sm" type="text" placeholder="Url Operador GPS" id="url_opegpsID" name="url_opegps" disabled value="'.$informacion['url_opegps'].'">
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Operador GPS" id="nom_opegpsID" name="nom_opegps" disabled value="'.$informacion['nom_opegps'].'">
                    </div>
                    <div class="col-4">
                      <input class="form-control form-control-sm" type="text" placeholder="Usuario" id="nom_usuariID" name="nom_usuari" disabled value="'.$informacion['nom_usuari'].'">
                      </div>
                    </div>
                    <div class="row mt-3">
                      <div class="offset-1 col-3">
                        <input class="form-control form-control-sm" type="text" placeholder="Contraseña" id="con_vehicuID" name="con_vehicu" disabled value="'.$informacion['con_vehicu'].'">
                        </div>
                        <div class="col-4">
                          <input class="form-control form-control-sm" type="text" placeholder="Ubicación" id="ubi_vehicuID" name="ubi_vehicu" disabled value="'.$informacion['ubi_vehicu'].'">
                          </div>
                          <div class="col-3">
                            <input class="form-control form-control-sm" type="text" placeholder="Punto de Referencia" id="pun_refereID" name="pun_refere" disabled value="'.$informacion['pun_refere'].'">
                            </div>
                          </div>
                          <div class="row mt-3">
                            <div class="offset-1 col-10">
                              <textarea class="form-control" id="des_asisteID" name="des_asiste" rows="3" placeholder="Breve Descripcin de la Asistencia" disabled>'.$informacion['des_asiste'].'</textarea>
                            </div>
                          </div>
                        </div>
                      </div>';
            }
            if($tip_solici==2){
                $html='<div class="card text-center" style="margin:15px;">
                <div class="card-header color-heading">Ubicación del Vehículo</div>
              <div class="card-body">
        
                <div class="row">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Fecha Servicio" id="fec_servicID" name="fec_servic" disabled value="'.$informacion['fec_servic'].'">
                  </div>
                  <div class="col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Ciudad de Origen" id="ciu_origen" name="ciu_origen" disabled value="'.$this->darNombreCiudad($informacion['ciu_origen']).'">
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Dirección" id="dir_ciuoriID" name="dir_ciuori" disabled value="'.$informacion['dir_ciuori'].'">
                  </div>
                </div>
        
                <div class="row mt-3">
                  <div class="offset-4 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Ciudad de Destino" id="ciu_destin" name="ciu_destin" disabled value="'.$this->darNombreCiudad($informacion['ciu_destin']).'">
                    <div id="ciu_destin-suggestions" class="suggestions"></div>
                  </div>
                  <div class="col-4">
                    <input class="form-control form-control-sm" type="text" placeholder="Dirección" id="dir_ciudesID" name="dir_ciudes" disabled value="'.$informacion['dir_ciudes'].'">
                  </div>
                </div>
                <div class="row mt-3">
                                <div class="offset-1 col-10">
                                  <textarea class="form-control" id="obs_acompaID" name="obs_acompa" rows="3" placeholder="Observaciones" disabled>'.$informacion['obs_acompa'].'</textarea>
                                </div>
                              </div>
              </div>
              </div>';
            }
            echo json_encode($html);
        }

        function darNombreCiudad($cod_ciudad){
            $sql="SELECT a.nom_ciudad FROM tab_genera_ciudad a WHERE a.cod_ciudad = '$cod_ciudad'";
            $query = new Consulta($sql, self::$conexion);
            $nom_ciudad = $query -> ret_matrix('a')[0]['nom_ciudad'];
            return $nom_ciudad;
        }

        function respuestasBitacora(){
            $html='';
            $cod_solici = $_REQUEST['cod_solici'];
            $sql="SELECT * FROM tab_seguim_solasi a WHERE a.cod_solasi = '$cod_solici'";
            $query = new Consulta($sql, self::$conexion);
            $respuestas = $query -> ret_matrix('a');
            $respuestas = self::cleanArray($respuestas);
            foreach ($respuestas as $respuesta){
                $html .= '<tr>
                            <td>'.$respuesta['obs_detall'].'</td>
                            <td>'.$this->darNombreEstados($respuesta['ind_estado']).'</td>
                            <td>'.$respuesta['fec_creaci'].'</td>
                            <td>'.$respuesta['usr_creaci'].'</td>
                        </tr>';
            }
            echo json_encode($html);

        }

        function darNombreEstados($cod_estado){
            switch ($cod_estado) {
                case '1':
                    return "POR GESTIONAR";
                    break;
                case '2':
                    return "POR APROBACION CLIENTE";
                    break;
                case '3':
                    return "ASIGNACION A PROVEEDOR";
                    break;
                case '4':
                    return "EN PROCESO";
                    break;
                case '5':
                    return "FINALIZADO";
                    break;
                default:
                    return "SIN ESTADO";
                    break;
            }
        }


        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificación
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que será analizado por la función
           *  \return: array
        */
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

        function registroGestioSolici(){
            try {
                $return = [];
                $num_solici=$_REQUEST['cod_solici'];
                $fichero = $_FILES["file"];
                $estado_proximo = 2;
                //datos del arhivo
                $ruta = "../../".NOM_URL_APLICA."/files/adj_solici/";
                $nombre_archivo = $fichero['name'];
                $tipo_archivo = $fichero['type'];
                $tamano_archivo = $fichero['size'];
                $temporal = $fichero['tmp_name'];
                $ext = explode(".", $nombre_archivo);
                $nombre = $ruta.$num_solici.".".end($ext);
                //Ojo Revisar la ubicación
                if (move_uploaded_file($fichero['tmp_name'],$nombre)){
                    $return['status'] = 500;
                    $return['response'] = 'No se pudo manejar el archivo '.$temporal;
                    echo json_encode($return);
                    exit();
                }

                if($nombre_archivo!=""){
                    $ubicacion="/files/adj_solici/".$num_solici.".".end($ext);
                }else{
                    $ubicacion="";
                }

                if(isset($_REQUEST['raz_finali'])){
                    $estado_proximo = 5;
                    $consultamas=" obs_finali = '".$_REQUEST['raz_finali']."', ";
                }
                $sql="UPDATE ".BASE_DATOS.".tab_asiste_carret SET 
                        est_solici = $estado_proximo,
                        obs_getsol = '".$_REQUEST['obs_gestio']."',
                        val_facges = '".$_REQUEST['val_factur']."',
                        val_cospro = '".$_REQUEST['val_cosser']."',
                        url_arcges = '$ubicacion',
                        ".$consultamas."
                        usu_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                        fec_modifi = NOW()
                    WHERE id='$num_solici'";
                new Consulta($sql, self::$conexion);
            
                  $sql="INSERT INTO ".BASE_DATOS.".tab_seguim_solasi(
                    cod_solasi, ind_estado, obs_detall,
                    usr_creaci, fec_creaci
                  ) 
                  VALUES 
                    (
                      '".$num_solici."', '$estado_proximo', '".$_REQUEST['obs_gestio']."',
                      '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                    )";
                  $consulta = new Consulta($sql, self::$conexion);
                    if($consulta){
                        $this->enviarCorreo($num_solici,$estado_proximo);
                        $return['status'] = 200;
                        $return['response'] = "Realizado con exito";
                    }    
                echo json_encode($return);
                }catch (Exception $e) {
                  echo 'Excepción registrar: ',  $e->getMessage(), "\n";
                }
        }


        function registroPorAprobaCli(){
            try {
                $return = [];
                $num_solici=$_REQUEST['cod_solici'];
                $fichero = $_FILES["file"];
                $estado_proximo = 3;
                //datos del arhivo
                $ruta = "../../".NOM_URL_APLICA."/files/adj_solici/";
                $nombre_archivo = $fichero['name'];
                $tipo_archivo = $fichero['type'];
                $tamano_archivo = $fichero['size'];
                $temporal = $fichero['tmp_name'];
                $ext = explode(".", $nombre_archivo);
                $nombre = $ruta.$num_solici.".".end($ext);
                //Ojo Revisar la ubicación
                if (move_uploaded_file($fichero['tmp_name'],$nombre)){
                    $return['status'] = 500;
                    $return['response'] = 'No se pudo manejar el archivo '.$temporal;
                    echo json_encode($return);
                    exit();
                }

                if($nombre_archivo!=""){
                    $ubicacion="/files/adj_solici/".$num_solici.".".end($ext);
                }else{
                    $ubicacion="";
                }

                if(isset($_REQUEST['raz_finali'])){
                    $estado_proximo = 5;
                    $consultamas=" obs_finali = '".$_REQUEST['raz_finali']."', ";
                }

                $sql="UPDATE ".BASE_DATOS.".tab_asiste_carret SET 
                        est_solici = $estado_proximo,
                        apr_servic = '".$_REQUEST['apr_servic']."',
                        obs_aprsol = '".$_REQUEST['obs_aprser']."',
                        cos_aprser = '".$_REQUEST['cos_aprser']."',
                        url_arcapr = '$ubicacion',
                        ".$consultamas."
                        usu_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                        fec_modifi = NOW()
                    WHERE id='$num_solici'";
                new Consulta($sql, self::$conexion);
            
                  $sql="INSERT INTO ".BASE_DATOS.".tab_seguim_solasi(
                    cod_solasi, ind_estado, obs_detall,
                    usr_creaci, fec_creaci
                  ) 
                  VALUES 
                    (
                      '".$num_solici."', '$estado_proximo', '".$_REQUEST['obs_aprser']."',
                      '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                    )";
                  $consulta = new Consulta($sql, self::$conexion);
                    if($consulta){
                        $this->enviarCorreo($num_solici,$estado_proximo);
                        $return['status'] = 200;
                        $return['response'] = "Realizado con exito";
                    }    
                echo json_encode($return);
                }catch (Exception $e) {
                  echo 'Excepción registrar: ',  $e->getMessage(), "\n";
                }
        }





//////////////////////////////////FUNCIONES RELACIONADAS AL ENVIO DE CORREOS///////////////////////////////////////////////////////////////

       
        private function darCorreos($cod_solici){
            $retorno= array();
            
            //Busca Correos registrados correspondientes a los gestores de asistencia
            $sql="SELECT a.dir_emailx FROM ".BASE_DATOS.".tab_genera_parcor a WHERE a.num_remdes = '';";
            $consulta = new Consulta($sql, self::$conexion);
            $dcorreos = $consulta->ret_matriz('a');
          
            foreach($dcorreos as $correos){
              $correo = explode(",", $correos['dir_emailx']);
              foreach($correo as $correou){
                array_push($retorno, trim(strtolower($correou)));
              }
            }
          
            $sql="SELECT 
                      a.cod_usuari, 
                      c.nom_tercer, 
                      c.dir_emailx 
                  FROM 
                    ".BASE_DATOS.".tab_genera_usuari a 
                  INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b 
                  ON a.cod_perfil = b.cod_perfil 
                  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
                  ON b.clv_filtro = c.cod_tercer
                  WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."';
                  ";
            $consulta = new Consulta($sql, self::$conexion);
            $dcorreos = $consulta->ret_matriz('a');
                
            foreach($dcorreos as $correos){
              $correo = explode(",", $correos['dir_emailx']);
              foreach($correo as $correou){
                array_push($retorno, trim(strtolower($correou)));
              }
            }
            
            $sql="SELECT a.cor_solici FROM ".BASE_DATOS.".tab_asiste_carret a WHERE a.id = '$cod_solici'";
            $consulta = new Consulta($sql, self::$conexion);
            $dcorreos = $consulta->ret_matriz('a')[0];
            array_push($retorno, trim(strtolower($dcorreos['cor_solici'])));
            return $retorno;
          }

          private function darNombreCliente($usuario,$ver){
            $sql="SELECT  
                      c.cod_tercer,c.nom_tercer
                  FROM 
                    ".BASE_DATOS.".tab_genera_usuari a 
                  INNER JOIN ".BASE_DATOS.".tab_aplica_filtro_perfil b 
                  ON a.cod_perfil = b.cod_perfil 
                  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
                  ON b.clv_filtro = c.cod_tercer
                  WHERE a.cod_usuari = '$usuario';
                  ";
            $consulta = new Consulta($sql, self::$conexion);
            $resultado = $consulta->ret_matriz('a');
            if($ver==1){
            if($resultado[0]['nom_tercer']!=""){
              return "Cliente : ".$resultado[0]['nom_tercer'];
            }else{
              return "";
            }
            }else{
              if($resultado[0]['cod_tercer']!=""){
                return $cod_tercer;
              }else{
                return false;
              }
            }
        }

        private function tipSolicitud($num_solici){
            $sql="SELECT b.nom_asiste FROM ".BASE_DATOS.".tab_asiste_carret a INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id WHERE a.id = $num_solici";
            $consulta = new Consulta($sql, self::$conexion);
            $nom_asiste = $consulta->ret_matriz('a');
            $nom_asiste = self::cleanArray($nom_asiste);
            return $nom_asiste[0]['nom_asiste'];
        }

        private function enviarCorreo($num_solici,$cod_estado) {
            $logo = URL_STANDA.'imagenes/asistencia.png';
            //$logo = 'https://dev.intrared.net:8083/ap/ctorres/sat-gl-2015/satt_standa/imagenes/asistencia.png';
            $nom_asiste = $this->tipSolicitud($num_solici);
            $fec_actual = date("Y-m-d H:i:s");   
            $correos = $this->darCorreos($num_solici);
            $estado = $this->darNombreEstados($cod_estado);
            $to = $correo;
            $subject = "NUEVO ESTADO DE SOLICITU ".strtoupper($nom_asiste);
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: asistencias@faro.com";
            $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
            <head>
              <meta charset="UTF-8">
              <meta content="width=device-width, initial-scale=1" name="viewport">
              <meta name="x-apple-disable-message-reformatting">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta content="telephone=no" name="format-detection">
              <title>Nuevo correo electrónico 2</title>
              <!--[if (mso 16)]><style type="text/css"> a {text-decoration: none;} </style><![endif]-->
              <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
              <style type="text/css">
                @media only screen and (max-width:600px) 
                { p, ul li, ol li, a 
                { font-size: 16px!important;
                 line-height: 150%!important 
                } 
                h1 { font-size: 30px!important; text-align: center; line-height: 120%!important } 
                h2 { font-size: 26px!important; text-align: center; line-height: 120%!important } 
                h3 { font-size: 20px!important; text-align: center; line-height: 120%!important } 
                h1 a { font-size: 30px!important } h2 a { font-size: 26px!important } 
                h3 a { font-size: 20px!important } 
                .es-menu td a { font-size: 16px!important } 
                .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size: 16px!important } 
                .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size: 16px!important } 
                .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size: 12px!important } 
                *[class="gmail-fix"] { display: none!important }
                 .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align: center!important } 
                 .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align: right!important } 
                 .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align: left!important } 
                 .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display: inline!important } 
                 .es-button-border { display: block!important } 
                 a.es-button { font-size: 20px!important; display: block!important; border-left-width: 0px!important; border-right-width: 0px!important } 
                 .es-btn-fw { border-width: 10px 0px!important; text-align: center!important } 
                 .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width: 100%!important } 
                 .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width: 100%!important; max-width: 600px!important } 
                 .es-adapt-td { display: block!important; width: 100%!important }
                 .adapt-img { width: 100%!important; height: auto!important } 
                 .es-m-p0 { padding: 0px!important } 
                 .es-m-p0r { padding-right: 0px!important } 
                 .es-m-p0l { padding-left: 0px!important } 
                 .es-m-p0t { padding-top: 0px!important } 
                 .es-m-p0b { padding-bottom: 0!important } 
                 .es-m-p20b { padding-bottom: 20px!important } 
                 .es-mobile-hidden, .es-hidden { display: none!important } 
                 .es-desk-hidden { display: table-row!important; width: auto!important; overflow: visible!important; float: none!important; max-height: inherit!important; line-height: inherit!important } 
                 .es-desk-menu-hidden { display: table-cell!important } 
                 table.es-table-not-adapt, .esd-block-html table { width: auto!important } 
                 table.es-social { display: inline-block!important } 
                 table.es-social td { display: inline-block!important } } 
                 #outlook a { padding: 0; } 
                 .ExternalClass { width: 100%; } 
                 .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; } 
                 .es-button { mso-style-priority: 100!important; text-decoration: none!important; } 
                 a[x-apple-data-detectors] { color: inherit!important; text-decoration: none!important; font-size: inherit!important; font-family: inherit!important; font-weight: inherit!important; line-height: inherit!important; } 
                 .es-desk-hidden { display: none; float: left; overflow: hidden; width: 0; max-height: 0; line-height: 0; mso-hide: all; } 
                 .colortext{ color:#000; }
              </style>
            </head>
            
            <body style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
              <div class="es-wrapper-color" style="background-color:#FFFFFF;">
                <!--[if gte mso 9]><v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"> <v:fill type="tile" color="#ffffff" origin="0.5, 0" position="0.5,0"></v:fill> </v:background><![endif]-->
                <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;">
                  <tr style="border-collapse:collapse;">
                    <td valign="top" style="padding:0;Margin:0;">
                      <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
                        <tr style="color:#ff9800; border-collapse:collapse;">
                          <td align="center" style="padding:0;Margin:0;">
                            <table bgcolor="#efefef" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="850" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-left:1px solid #808080;border-right:1px solid #808080;border-top:1px solid #808080;border-bottom:1px solid #808080;">
                              <tr style="border-collapse:collapse;">
                                <td align="left" style="Margin:0;padding-bottom:5px;padding-top:20px;padding-left:40px;padding-right:40px;">
                                  <!--[if mso]><table width="518" cellpadding="0" cellspacing="0"><tr><td width="154" valign="top"><![endif]-->
                                  <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left;">
                                    <tr style="border-collapse:collapse;">
                                      <td width="154" class="es-m-p0r es-m-p20b" valign="top" align="center" style="padding:0;Margin:0;">
                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                          <tr style="border-collapse:collapse;">
                                            <td align="center" style="padding:0;Margin:0;font-size:0px;"><img class="adapt-img" src="'.$logo.'" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"
                                                width="250"></td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                  <!--[if mso]></td><td width="20"></td><td width="344" valign="top"><![endif]-->
                                  <table cellpadding="0" cellspacing="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr style="border-collapse:collapse;">
                                      <td width="344" align="left" style="padding:0;Margin:0;">
                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                          <tr style="border-collapse:collapse;">
                                            <td align="left" style="text-align: center; padding:0;Margin:0;padding-top:40px;">
                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:20px;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:60px;color:#333333;"><b>Solicitud N°. '.$num_solici.' </b></p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                  <!--[if mso]></td></tr></table><![endif]-->
                                </td>
                              </tr>
                              <tr style="border-collapse:collapse;">
                                <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;">
                                  <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr style="border-collapse:collapse;">
                                      <td class="es-m-p0r" width="558" valign="top" align="center" style="padding:0;Margin:0;">
                                        <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                          <tr style="border-collapse:collapse;">
                                            <td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0;">
                                              <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                                <tr style="border-collapse:collapse;">
                                                  <td style="padding:0;Margin:0px;border-bottom:6px solid #ff9800;background:none;height:1px;width:100%;margin:0px;"></td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                          <tr style="border-collapse:collapse;">
                                            <td align="center" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px;font-size:0;">
                                              <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                                <tr style="border-collapse:collapse;">
                                                  <td style="padding:0;Margin:0px;border-bottom:10px solid #ff9800;background:none;height:1px;width:100%;margin:0px;"></td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                              <tr style="border-collapse:collapse;">
                                <td align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:40px;padding-right:40px;">
                                  <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr style="border-collapse:collapse;">
                                      <td width="518" align="center" valign="top" style="padding:0;Margin:0;">
                                        <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;border:2px double #ff9800;"
                                          role="presentation">
                                          <tr style="border-collapse:collapse;">
                                            <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;">
                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:21px;color:#655e5e;">
                                                <br><strong class="colortext">Solicitud de: </strong> '. $nom_asiste .' <br> <br><strong class="colortext">'.$this->darNombreCliente($_SESSION['datos_usuario']['cod_usuari'],1).'</strong>                                    <br> <br><strong class="colortext">Fecha y hora de la solicitud: </strong> '. $fec_actual .' <br> <br class="colortext">Señor(a):
                                                '. $nom_solici .'. <br> <br class="colortext">Por medio del presente correo la línea de servicio <strong>Asistencia Logística</strong> del <strong>Grupo OET</strong>, le informa que su solicitud se encuentra en el. <br> <br class="colortext">Estado:
                                                <strong class="colortext">'.$estado.'</strong> <br> <br class="colortext">Le estaremos informando el
                                                estado de su solicitud, cabe aclarar que nuestro tiempo de respuesta es de aproximadamente 45 minutos o antes. <br><br> </p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                              <tr style="border-collapse:collapse;">
                                <td align="left" style="padding:0;Margin:0;">
                                  <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr style="border-collapse:collapse;">
                                      <td width="598" align="center" valign="top" style="padding:0;Margin:0;">
                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                          <tr style="border-collapse:collapse;">
                                            <td align="center" style="padding:0;Margin:0;padding-bottom:10px;">
                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:10px;font-family:arial, helvetica neue, helvetica, sans-serif;line-height:10px;color:#333333;">
                                                Copyright © '.date('Y').'. Todos Los Derechos Reservados. Diseñado y desarrollado por Grupo OET S.A.S.</p>
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </body>
            
            </html>';
            foreach($correos as $correo){
              mail($correo, $subject, $message, $headers);
            }
        }

    }

    new AjaxGestioAsiscar();
    
?>
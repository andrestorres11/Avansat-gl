<?php
/* ! \file: ajax_homolo_noveda.php
 *  \brief: archivo con multiples funciones para la configuracion de usuarios de faro para gestionar transportadoras
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 24/07/2019
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

class ajax_homolo_noveda {

    private static $cConexion,
    $cCodAplica,
    $cUsuario;

    private static $cTipDespac = array(1 => "ind_desurb", 2 => "ind_desnac", 3 => "ind_desimp", 4 => "ind_desexp", 5 => "ind_desxd1", 6 => "ind_desxd2");

    function __construct($co = null, $us = null, $ca = null) {
        if ($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on') {
            @include_once( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            @include_once( "../lib/general/functions.inc" );

            self::$cConexion = $AjaxConnection;
            self::$cUsuario = $_SESSION['datos_usuario'];
            self::$cCodAplica = $_SESSION['codigo'];
        } else {
            self::$cConexion = $co;
            self::$cUsuario = $us;
            self::$cCodAplica = $ca;
        }

        if ($_REQUEST[Ajax] === 'on') {
            $opcion = $_REQUEST[Option];

            switch ($opcion) {
                case "getDataList":
                    $this->getDataList();
                    break;
                case "registrar":
                    $this->registrar();
                    break;
                case 'actualizarCod':
                    $this->actualizarCod();
                    break;
                case 'actualizarRegistro':
                    $this->actualizarRegistro();
                    break;
                default:
                    header('Location: ../../' . BASE_DATOS . '/index.php?window=central&cod_servic=558menant=558');
                    break;
            }
        }
    }

    /* !     \fn: duplicateArray
     *  \brief: funcion que valida valores duplicados en un arreglo
     *  \author: Ing. Luis Manrique
     *  \date: 24/07/2019
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return string
     */

    private function duplicateArray($moduloA, $arrayA, $moduloB = null, $arrayB = null){

        //Crea arreglo para unificar llave con el valor
        $ban = 0;
        foreach ($arrayA as $key => $value) {
            $arrayA[$ban] = $value."_".$key."_".$moduloA;
            $ban++;
        }
       
        //valida si Existe 2do arrego
        if(isset($arrayB)){
            //Crea arreglo para unificar llave con el valor
            $ban = 0;
            foreach ($arrayB as $key => $value) {
                $arrayB[$ban] = $value."_".$key."_".$moduloB;
                $ban++;
            }
            $arrayP = array_merge_recursive($arrayA, $arrayB);
        }else{
            $arrayP = $arrayA;
        }

        //Ordena el arrreglo
        asort($arrayP);

        //Variables necesarias
        $mensajeDup = "";
        $fila = "";
        $arrayDupli = [];
        $titleMod = "";

        //Recorre arreglo
        foreach ($arrayP as $key => $value) {
            //Dividir Valor con Posición
            $value = explode("_",$value);
            //Valida si es igual el valor
            if($valor == $value[0]){
                if(empty($fila)){
                    //Crea posición en la fila
                    $fila = $value[1]+1;
                    $arrayDupli[$titleFirst][] = $posiFirst;
                    $arrayDupli[$value[2]][] = $fila;
                }else{
                    //Crea posición en la fila
                    $fila = $value[1]+1;
                    $arrayDupli[$value[2]][] = $fila;
                }
                //Crea posición en la fila y asgina el valor
                $valor = $value[0];
            }else{
                //Crea posición en la fila y asgina el valor
                $valor = $value[0];
            }
            //Capturar Modulo y posición anterior
            $titleFirst = $value[2];
            $posiFirst = $value[1]+1;
        }

        //Validar si no es vacio
        if(!empty($arrayDupli))
        foreach ($arrayDupli as $modulo => $value) {
            //Ordena el arrreglo
            asort($value);
            foreach ($value as $key => $valueRow) {
                //Validar si el mensaje no se a creado y asignar valores
                if(empty($mensajeDup)){
                    $mensajeDup = "Las siguientes filas estan duplicadas en el modulo ".$modulo.":
                    
                    * Fila ".$valueRow."
                    ";
                }else{
                    if($titleMod != $modulo){
                        $mensajeDup .= "Las siguientes filas estan duplicadas en el modulo ".$modulo.":
                    
                        * Fila ".$valueRow."
                        ";
                    }else{
                        $mensajeDup .= "* Fila ".$valueRow."
                        ";
                    }
                }
                //Capturar Modulo
                $titleMod = $modulo;
            }
        }
        return $mensajeDup;
    }       

    /* !     \fn: registrar
     *  \brief: funcion para registrar Homologación de procesos y de novedades
     *  \author: Ing. Luis Manrique
     *  \date: 24/07/2019
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return boolen
     */
    private function registrar() {
        //Convertir post en arreglos
        $datos = (array) $_POST;
        $datos['usr_creaci'] = $_SESSION["datos_usuario"]["cod_usuari"];

        //Enviar arreglos para validación de duplicidad en valores
        $rowDuplicate .=$this->duplicateArray("Homologar Novedades", $datos['cod_noveda']);
        $rowDuplicate .=$this->duplicateArray("Homologar Proceso", $datos['cod_estcli'], "Homologar Novedades", $datos['cod_estcliNov']);

        //Validar si hay duplicados
        if(!empty($rowDuplicate)){
            echo $rowDuplicate;
        }else{
            //Crear inserción de campos en la tabla tab_homolo_estado
            $registrosHomProc = "";
            $cod_homest = 1;
            $mCountEst = count($datos['cod_estado']);

            foreach ($datos['cod_estado'] as $key => $value) {
                if(empty($registrosHomProc)){
                    $registrosHomProc =  "(".$cod_homest.", ".$datos['cod_estado'][$key]." , ".$datos['cod_estcli'][$key]." , '".$datos['obs_hompro'][$key]."', '".$datos['usr_creaci']."', NOW())";
                }else{
                    $registrosHomProc .= ", (".$cod_homest.", ".$datos['cod_estado'][$key]." , ".$datos['cod_estcli'][$key]." , '".$datos['obs_hompro'][$key]."', '".$datos['usr_creaci']."', NOW())";
                }
                $cod_homest++;
            }
            
            //Crear inserción de campos en la tabla tab_homolo_estnov
            $registrosHomNov = "";
            $cod_homnov = 1;
            $mCountNov = count($datos['cod_noveda']);

            foreach ($datos['cod_noveda'] as $key => $value) {
                if(empty($registrosHomNov)){
                    $registrosHomNov =  "(".$cod_homnov.", ".$datos['cod_noveda'][$key]." , ".$datos['cod_estcliNov'][$key]." , '".$datos['observNov'][$key]."', '".$datos['usr_creaci']."', NOW())";
                }else{
                    $registrosHomNov .= ", (".$cod_homnov.", ".$datos['cod_noveda'][$key]." , ".$datos['cod_estcliNov'][$key]." , '".$datos['observNov'][$key]."', '".$datos['usr_creaci']."', NOW())";
                }
                $cod_homnov++;
            }

            //Eliminar contenido de la tabla tab_homolo_estado
            $sqlDelete = "DELETE FROM " . BASE_DATOS . ".tab_homolo_estado WHERE 1";
            $consultaDelete = new Consulta($sqlDelete, self::$cConexion);
            if (count($consultaDelete) > 0) {
                //Eliminar contenido de la tabla tab_homolo_estnov
                $sqlDelete = "DELETE FROM " . BASE_DATOS . ".tab_homolo_estnov WHERE 1";
                $consultaDelete = new Consulta($sqlDelete, self::$cConexion);
                if (count($consultaDelete) > 0) {
                    //Insertar registros de la tabla tab_homolo_estado
                    $sql = "INSERT INTO " . BASE_DATOS . ".tab_homolo_estado( cod_homest, cod_estado, cod_estcli, obs_hompro, usr_creaci, fec_creaci)VALUES $registrosHomProc";
                    $consulta = new Consulta($sql, self::$cConexion, 'RC');
                    if (count($consulta) > 0) {
                        //Insertar registros de la tabla tab_homolo_estnov
                        $sql = "INSERT INTO " . BASE_DATOS . ".tab_homolo_estnov( cod_homnov, cod_noveda, cod_estcli, obs_homnov, usr_creaci, fec_creaci)VALUES $registrosHomNov";
                        $consulta = new Consulta($sql, self::$cConexion, 'RC');
                        if (count($consulta) > 0) {
                            echo 1;
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
        }
    }

    /*! \fn: actualizarCod
     *  \brief: Trae el Estado por Codigo
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */
    private function actualizarCod() {
        $sql = "
              SELECT    a.cod_estado,
                	    a.nom_estado, 
                        a.ind_estado 
                FROM    ".BASE_DATOS.".tab_genest_tracki a  
               WHERE    a.cod_estado = ".$_REQUEST['cod_estado'];
        $consulta = new Consulta($sql, self::$cConexion);
        $result = $consulta->ret_matrix('a');
        
        echo json_encode($result[0]);
    }

    /*! \fn: getDataListHom
     *  \brief: Trae los campos de la sección de Homologación por Proceso
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */

    public function getDataListHom() {
      $sql = '
              SELECT 
                        a.cod_estado,
                        a.nom_estado, 
                        c.cod_estcli, 
                        c.nom_estcli,  
                        b.obs_hompro
                FROM
                        '.BD_STANDA.'.tab_estado_despac a
           LEFT JOIN
                        '.BASE_DATOS.'.tab_homolo_estado b
                  ON    a.cod_estado = b.cod_estado
           LEFT JOIN
                        '.BASE_DATOS.'.tab_genest_tracki c
                  ON    b.cod_estcli = c.cod_estcli
               WHERE
                        a.ind_activo = 1';
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }

    /*! \fn: getSubDataFileNomEst
     *  \brief: Trae la lista del campo Nombre Estado en la sección de Homologación por Proceso
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */

    public function getSubDataFileNomEst() {
        $sql = "
              SELECT    a.cod_estcli,
                        a.nom_estcli
                FROM    ". BASE_DATOS . ".tab_genest_tracki a  
               WHERE    a.ind_estcli = '1'";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }

    /*! \fn: getDataFileNov
     *  \brief: Trae los campos de la sección de Homologación por Proceso
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */

    public function getDataFileNov() {
        $sql = "

              SELECT
                        a.cod_noveda,
                        b.nom_noveda,
                        a.cod_estcli,
                        c.nom_estcli,
                        a.obs_homnov
                FROM 
                        ". BASE_DATOS . ".tab_homolo_estnov a
           LEFT JOIN 
                        ". BASE_DATOS . ".tab_genera_noveda b
                  ON 
                        a.cod_noveda = b.cod_noveda
           LEFT JOIN
                        ". BASE_DATOS . ".tab_genest_tracki c
                  ON
                        a.cod_estcli = c.cod_estcli
                    ";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }

    /*! \fn: getSubDataFileNov
     *  \brief: Trae la lista del campo Novedad en la sección de Homologación por Novedad
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */

    public function getSubDataFileNov() {
        $sql = "
              SELECT    a.cod_noveda,
                        a.nom_noveda
                FROM    ". BASE_DATOS . ".tab_genera_noveda a  
               WHERE    a.ind_estado = '1'
            ORDER BY    a.cod_noveda";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix('a');
    }
}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new ajax_homolo_noveda();
}
?>
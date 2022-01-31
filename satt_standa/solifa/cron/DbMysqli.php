<?php
/*! \file: DbMysqli.php
 *  \brief: controla la conexion y consultas haciendo uso de mysqli
 *  \author: INTRARED <fesus.rocuts@intrared.net>
 *  \version: 1.0
 *  \date:    29/09/2016
 *  \bug: ""
 *  \warning: requiere validar las conexiones en el archivo /public/json/setting.json
 *  \warning: requiere editar archivo setting.php poner usuario y clave con privilegios de listar basesdedatos, listar y seleccionar tablas
 */

 /*! \class: Db
  *  \brief: controla la conexion y consultas haciendo uso de mysqli
  */
class DbMysqli 
{
	/*!
   * \var: $host
   * \brief: ip de conexion con puerto de la db
   */
	public $host="";
	/*!
   * \var: $user
   * \brief: usuario de la db
   */
	public $user="";
	/*!
   * \var: $pwd
   * \brief: clave del usuario
   */
	public $pwd="";
	/*!
   * \var: $db
   * \brief: nombre de base de datos de la conexión
   */
	public $db="";
  /*!
   * \var: $port
   * \brief: nombre de base de datos de la conexión
   */
	public $port="";
  /*!
   * \var: $socket
   * \brief: nombre de base de datos de la conexión
   */
	public $socket="";
	/*!
   * \var: $link
   * \brief: objeto de conexin
   */
	public $link;
	/*!
   * \var: $result
   * \brief: objeto que contiene la el resulado de la consulta
   */
	public $result;
	/*!
   * \var: $errorFatal
   * \brief: variable boolean para determinar si existe un error interno
   */
	private $errorFatal=false;
	/*!
   * \var: $lastDataFetchQuery
   * \brief: ultimo resultado de la consulta
   */
	private $lastDataFetchQuery=null;
	/*!
   * \var: $num_rows
   * \brief: numero de filas que tiene el recurso de consulta
   */
	private $num_rows=0;
	/*!
   * \var: $affected_rows
   * \brief: numero de filas afectadas del recurso de conexion
   */
	private $affected_rows=0;

	/*! \fn: __construct
	 *  \brief: recibe los arcumentos de conexion y los remite a una fn
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	function __construct($args)
	{
		try{
			LogHelper::info("*********************************************\n");
			LogHelper::info("*********************************************\n");
			LogHelper::info(get_class($this)."::__construct > ");
			if(isset($args) && is_array($args) && sizeof($args)>0){
				$this->host=isset($args["host"]) ? $args["host"] : $this->host;
				$this->user=isset($args["user"]) ? $args["user"] : $this->user;
				$this->pwd=isset($args["pwd"]) ? $args["pwd"] : $this->pwd;
				$this->db=isset($args["db"]) ? $args["db"] : $this->db;
		        $this->port=isset($args["port"]) ? $args["port"] : $this->port;
		        $this->socket=isset($args["socket"]) ? $args["socket"] : $this->socket;
				$this->setConnect();
			}else{
				throw new Exception("Invalid input arguments, check this!", 1);
			}
		}catch(Exception $e){
			LogHelper::error($e);
		}
	}

	/*! \fn: getErrorFatal
	 *  \brief: permite parar la ejecución de una instancia para no propagar excepciones
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function getErrorFatal(){
		try{
			LogHelper::info(get_class($this)."::getErrorFatal > ");
			if(!$this->errorFatal){
				//LogHelper::info("Todo anda bien!");
				return true;
			}else {
				LogHelper::info("Sorry!, please view your log.");
				return false;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return false;
		}
	}

	/*! \fn: setErrorFatal
	 *  \brief: asigna el valor de error en la variable definida para tal caso
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setErrorFatal($boolean){
		try{
			LogHelper::info(get_class($this)."::setErrorFatal > ");
			$this->errorFatal=$boolean;
		}catch(Exception $e){
			LogHelper::error($e);
		}
	}

	/*! \fn: setConnect
	 *  \brief: conecta a la db
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setConnect(){
		try{
			LogHelper::info(get_class($this)."::setConnect > ");

      if(strlen($this->port)>0 && strlen($this->socket)>0){
        $this->link = mysqli_connect($this->host, $this->user, $this->pwd, $this->db, $this->port, $this->socket);
      }elseif(strlen($this->port)>0 && strlen($this->socket)==0){
        $this->link = mysqli_connect($this->host, $this->user, $this->pwd, $this->db, $this->port);
      }else{
        $this->link = mysqli_connect($this->host, $this->user, $this->pwd, $this->db);
      }


			if (mysqli_connect_errno()) {
					//no emite excepcion fatal, pero emite el error
					//se hace para poder recorrer validar las conexiones en lote
					LogHelper::info("Fail conection: \Server[port]: $this->host \nUser: $this->user, \nPwd: $this->pwd, \nDb: $this->db");
					//LogHelper::error(mysqli_connect_error());
			    //throw new Exception("Conexión fallida: %s\n", mysqli_connect_error());
					$this->setErrorFatal(true);
			}
		}catch(Exception $e){
			LogHelper::error($e);
		}
	}

	/*! \fn: setQuery
	 *  \brief: ejecuta una consulta sql
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setQuery($sql){
		//evita ejecutar el script, usado para lotes de procesos
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::setQuery > ");
				$this->result = mysqli_query($this->link, $sql);
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: setNumRows
	 *  \brief: asinga el valor de numeroo de filas del recurso
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setNumRows(){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::setNumRows > ");
				$this->num_rows = mysqli_num_rows($this->result);
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: getNumRows
	 *  \brief: regresa el numeroo de filas del recurso
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function getNumRows(){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::getNumRows > ");
				return $this->num_rows;
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: setAffectedRows
	 *  \brief: asigna un valor de filas afectadas de una conexion
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setAffectedRows(){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::setAffectedRows > ");
				$this->affected_rows = mysqli_affected_rows($this->link);
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: setAffectedRows
	 *  \brief: retorna el valor de filas afectadas de una conexion
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function getAffectedRows(){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::getAffectedRows > ");
				return $this->affected_rows;
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: setFetchAll
	 *  \brief: asigna a un atributo de clase los resultados asociados
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setFetchAll(){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::setFetchAll > ");
				$this->lastDataFetchQuery=array();
				if($this->result){
					while($fa = mysqli_fetch_assoc($this->result)){
						array_push($this->lastDataFetchQuery,$fa);
					}
				}
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: getFetchAll
	 *  \brief: retorna el atributo de clase cn los resultados asociados
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function getFetchAll(){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::getFetchAll > ");
				return $this->lastDataFetchQuery;
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

	/*! \fn: setFreeResult
	 *  \brief: libera el recurso de resultado
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setFreeResult(){
		try{
			LogHelper::info(get_class($this)."::setFreeResult > ");
			mysqli_free_result();
		}catch(Exception $e){
			LogHelper::error($e);
		}
	}

	/*! \fn: setClose
	 *  \brief: cierra el recurso de conexion
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	public function setClose($sql){
		if($this->getErrorFatal()){
			try{
				LogHelper::info(get_class($this)."::setClose > ");
				mysqli_close($this->link);
			}catch(Exception $e){
				LogHelper::error($e);
			}
		}
	}

}

<?php

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

  /*! \Class: ClassConection
   *  \brief: Clase encargada de hacer la conexion a la BD de asterisk
   *  \author: Ing. Nelson Liberato
   *  \date: 07/07/2016   
   *  \param: $cConexion  -  Variable de la conexion para la clase en general
   *  \param: $cMsgError  -  Variable de mensaje en caso de error
   *  \return array
   */
  class ClassConection
  {
   
 
    # Variables de la clase
    private static $cHostBD = NULL;
    private static $cUserBD = NULL;
    private static $cPassBD = NULL;
    private static $cNomxBD = NULL;
    private static $cConnBD = NULL;
    
    
    /*! \fn: __construct
    *  \brief: Constructorea de la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mCo  -  Nombre del bucket     
    *  \return array
    */
    function __construct(   )
    {
        ClassConection::$cHostBD = HOSTBD; 
        ClassConection::$cUserBD = USERBD; 
        ClassConection::$cPassBD = PASSBD; 
        ClassConection::$cNomxBD = NOMXBD; 
    }

    /*! \fn: setExecute
    *  \brief: Ejecuta una query
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function setExecute( $mQuery = '', $mReturn = false  )
    {
      # Inicia conexion a BD
      ClassConection::setOpenBD();

      # Ejecuta Query
      $mData =  ClassConection::setQuery($mQuery, $mReturn);

      ClassConection::setCloseBD();

      return $mData;

    }    


    /*! \fn: setOpenConnection
    *  \brief: Asigna mensaje de error de manera global en la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function setOpenBD(  )
    {
      try {
        ClassConection::$cConnBD = mysqli_connect(ClassConection::$cHostBD, ClassConection::$cUserBD, ClassConection::$cPassBD, ClassConection::$cNomxBD);
        if(!ClassConection::$cConnBD) {
          throw new Exception("Error conectando a servidor: ".mysqli_connect_error(), mysqli_connect_errno());          
        }
      } catch (Exception $e) {
        ClassConection::setLog($e -> getCode(), $e -> getMessage() );
        return false;
      }
      return frue;
    }       


    /*! \fn: setQuery
    *  \brief: Ejecuta la query
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function setQuery( $mQuery, $mReturn )
    {
      try { 
        if ($mResult = mysqli_query(ClassConection::$cConnBD, $mQuery)) {
            $mArray = array();
            if($mReturn)
            {
              while ( $mRow = mysqli_fetch_assoc($mResult)) {
                $mArray[] = $mRow;
              }
            } else {
              $mArray["cod_respon" ] = "1000";
              $mArray["msg_respon" ] = "Ejecutado con exito";
            }         

            mysqli_free_result($mResult);
            return $mArray;

        } else {
          throw new Exception("Error ejecutando query: ".$mQuery.", ".mysqli_error(ClassConection::$cConnBD), mysqli_errno(ClassConection::$cConnBD));          
        }

      } catch (Exception $e) {
        ClassConection::setLog($e -> getCode(), $e -> getMessage() );
        return false;
      }
      return frue;
    }      

    /*! \fn: setCloseBD
    *  \brief: Ejecuta la query
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function setCloseBD(   )
    {
      try {
        
        if(!mysqli_close(ClassConection::$cConnBD )) { 
          throw new Exception("Error cerrando conexion al servidor ","3001");          
        }

      } catch (Exception $e) {
        ClassConection::setLog($e -> getCode(), $e -> getMessage() );
        return false;
      }
      return frue;
    }    



    /*! \fn: setMsgError
    *  \brief: Asigna mensaje de error de manera global en la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return bool
    */
    function setLog( $mCodError, $mMsgError )
    {
      $mFile = DirLog."/log".date("Y_m_d").".txt";
      if($mOpen = fopen($mFile, "a+")) {
        fwrite($mOpen, date("Y-m-d H:i:s")."|".$mCodError."|".$mMsgError."\n");
        fclose($mOpen);

        # Mata ejecucion de script -----
        $mWarning =  '<table>';
        $mWarning .= '<tr>';
        $mWarning .= '<td>Error de Ejecucion de S3</td>';
        $mWarning .= '</tr>';        
             
        $mWarning .= '<tr>';
        $mWarning .= '<td>'.$mMsgError.'</td>';
        $mWarning .= '</tr>';        
        $mWarning .= '<tr>';
        $mWarning .= '<td><img src="'.ImageAlert.'" /></td>';
        $mWarning .= '</tr>';
        $mWarning .= '</table>';

        echo $mWarning;

        die("Termina ejecucion de PHP");
      } else {
        die("No se pudo crear el archivo log: ".$mFile." en el directorio: logs, verificar permisos de escritura y ruta ");
      }
    }

    /*! \fn: getMsgError
    *  \brief: Recupera mensaje de error de manera global en la clase
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */
    function getMsgError()
    {
      return ClassConection::$cMsgError;
    }

    /*! \fn: getListBucket
    *  \brief: Retorna la Lista de Buckets creados en S3
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015           
    *  \return Array
    */
    function getConnection()
    {
      return ClassConection::$cConnBD = 'dsadasdasdasdas';
    }

  
      
    /*! \fn: Consulta
    *  \brief: Metodo para hacer la respectiva conexion a la base de datos de manera local para el script
    *  \author: Ing. Nelson Liberato
    *  \date: 09/06/2015   
    *  \param: $mMsgError      
    *  \return string
    */      
    /*   
    function Consulta( $mQuery = NULL, $mRetorna = true)
    {       
      $mError = array();
      $mLink = mysql_connect("aglbd.intrared.net", "nliberato", ",oMaXe,o"); 
      if( !$mLink)
      {
        $mError["ID"] = false;
        $mError["Code"] = mysql_errno($mLink );
        $mError["Error"] = mysql_error($mLink );
        return $mError;
      }
      mysql_select_db("satt_faro");

      $mDataArray = array();
      $mCont = 0;
      $mData = mysql_query($mQuery);

      if( $mRetorna ) 
      {
        while ($mRow = mysql_fetch_assoc($mData)) 
        {
          $mDataArray[$mCont++] = $mRow;         
        }
      }

      mysql_close($mLink);
      return $mDataArray;
    }*/

  }
 

?>
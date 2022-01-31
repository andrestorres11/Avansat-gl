<?php
/******************************************************************************/
/*En esta Librería se Especifican Clases útiles para la conexion a la Base de**/
/*Datos y la ejecucion de querys***********************************************/
/******************************************************************************/

/*La clase Consulta permite la ejecucion de una consulta sql*******************/
class Consulta
{
    //Atributos
    var $query, //Sentencia SQL
        $conexion, //identificador del enlace utilizado
                $transaccion, //indica si la consulta hace parte de una transaccion puede ser
                                      // B   "BEGIN" es la unica consulta en una transaccion para bloqueo del registro,
                          // BR  "BEGIN y ROLLBACK" si es la primera de varias consultas en una transaccion,
                          // R   "ROLLBACK" si no es la primera ni la ultima consulta de una transaccion,
                          // RC  "ROLLBACK O COMMIT" si es la ultima de una serie de consultas,
                                  // BRC "BEGIN Y ROLLBACK O COMMIT" si es una transaccion de una sola consulta,
                                      // C   "COMMIT" es la unica consulta en una transaccion para liberar el registro.
        $id_resultado, //resultado de la ejecucion de la consulta
        $arreglo;//arreglo asociativo del resultado de la consulta

    //Métodos
    function __construct($qu, $co, $tr = 'N')//Constructor de la clase
    {
        $this -> transaccion = $tr;
        $this -> query = $qu;
        $this -> conexion = $co;
        $id_conexion = $this -> conexion -> ret_id_conexion();
        if(($this -> transaccion == "B")||($this -> transaccion == "BR")||($this -> transaccion == "BRC"))
                           mysql_query("START TRANSACTION", $id_conexion);
               $this -> id_resultado = mysql_query($this -> query, $id_conexion);
                if (!$this -> id_resultado)
        {
                   $error = mysql_error();
                $error_num = mysql_errno();
                $mensaje = new Tabla_mensaje("Imposible Realizar Operaci&oacute;n", $this -> query . "<br>" . $error_num . ":" . $error . "<br>");
            if(($this->transaccion == "BR")||($this->transaccion == "R")||($this->transaccion == "RC")||($this->transaccion == "BRC"))
                            mysql_query("ROLLBACK", $id_conexion);
            $this -> conexion -> cerrar();
            exit;
        }
                else if(($this -> transaccion == "RC")||($this -> transaccion == "BRC")||($this -> transaccion == "C"))
                mysql_query("COMMIT", $id_conexion);
    }

    function ret_resultado()//Retornar el identificador del resultado de al consulta
    {
        return $this -> id_resultado;
    }

    function ret_arreglo()//esta funcion retorna la primera fila del resultado de la consulta como arreglo asociativo
    {
        $this -> arreglo = mysql_fetch_array($this -> id_resultado);
        //mysql_free_result($this -> id_resultado);
        return $this -> arreglo;
    }

    function ret_matriz()//Retorna una matriz bidimensional con el resultado entero de la consulta
    {
        $i = 0;
        $matriz = array();
        while($fila = mysql_fetch_array($this -> id_resultado))
        {
            $matriz[$i] = $fila;
            $i++;
        }
        //if(!isset($matriz))
        //        $matriz = 0;
        //mysql_free_result($this -> id_resultado);
        return $matriz;
    }
    
    
    function ret_matrix( $mode="b" )//Retorna una matriz bidimensional con el resultado entero de la consulta
    {
        $i = 0;
        $matriz = array();
        switch( $mode ) {
            case "a" :
                while($fila = mysql_fetch_assoc($this -> id_resultado))   {
                    $matriz[$i] = $fila;
                    $i++;
                }
            break;
            case "i" :
                while($fila = mysql_fetch_row($this -> id_resultado))   {
                    $matriz[$i] = $fila;
                    $i++;
                }
            break;
            default :
                while($fila = mysql_fetch_array($this -> id_resultado))   {
                    $matriz[$i] = $fila;
                    $i++;
                }
            break;
        }
        return $matriz;
    }

    
 
    function ret_matriz_unica()
    {
        $i = 0;
        $anterior=0;
        while($fila = mysql_fetch_array($this -> id_resultado))
        {
         if($fila[estado] == "retrasado" AND $fila[num_despac] != $anterior)
          {
            $anterior = $fila[num_despac];
            $matriz[$i] = $fila;
            $i++;
          }
         else
         {
            $i++;
         }
        }
        //mysql_free_result($this -> id_resultado);
        return $matriz;
    }

    function ret_num_rows()
    {
        return mysql_num_rows($this -> id_resultado);
    }

    function ret_vector()
    {
        $result = mysql_fetch_row($this -> id_resultado);
        //mysql_free_result($this -> id_resultado); 
        return $result;
    }
    
    
    function ret_objeto()
    {
        return mysql_fetch_object($this -> id_resultado);
    }
}

/*La clase Conexi&oacute;n especifica una conexion a una base de datos en MySQL***************************************/
class Conexion
{
    //Atributos
    var $id_conexion,//identificador de enlace
        $servidor,  //servidor de bases de datos al que se conectará
        $usuario,   //usuario de la base de datos
        $clave,     //clave del usuario
        $base_datos;//base de datos a la que se conectará

    //Metodos
    function Conexion($se, $us, $cl, $bd)//Constructor de la clase
    {

        $this -> id_conexion = 0;
        $this -> servidor = $se;
        $this -> usuario = $us;
        $this -> clave = $cl;
        $this -> base_datos = $bd;
        $this -> id_conexion = mysql_connect($this -> servidor, $this -> usuario, $this -> clave);
        if ($this -> id_conexion <= 0)
        {
            $error = mysql_error();
            $error_num = mysql_errno();
            $mensaje = new Tabla_mensaje("Imposible Realizar Operaci&oacute;n", "No se puede abrir la conexi&oacute;n a la Base de Datos<br>" . $error_num . ":" . $error . "<br>");
            exit;
        }
        $resultado = mysql_select_db($bd, $this -> id_conexion);
        if(!$resultado)
        {
            $error = mysql_error();
            $error_num = mysql_errno();
            $mensaje = new Tabla_mensaje("Imposible Realizar Operaci&oacute;n", "No se puede abrir la conexi&oacute;n a la Base de Datos<br>" . $error_num . ":" . $error . "<br>");
            exit;
        }
    }

    function cerrar($msj=1)//Cierra la Conexion
    {
        echo "<br>";
        if(mysql_close($this -> id_conexion))
	{
	   if($msj)	
           $mensaje = new Tabla_mensaje("Cerrada la conexi&oacute;n", "Se ha cerrado la conexi&oacute;n a la base de datos");
	}
        else
	{
           $mensaje = new Tabla_mensaje("Conexi&oacute;n no Cerrada ", "No se ha podido cerrar la conexi&oacute;n a la base de datos");
	}
    }

    function ret_id_conexion()//devuleve el identificador de conexi&oacute;n
    {
        return $this -> id_conexion;
    }
}
?>
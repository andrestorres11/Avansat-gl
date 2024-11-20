<?php

include( 'constantes.inc' );

//Clase Utilizada para la conexion a la base de datos.
class Conexion
{
    //Atributos
    var $link = 0;    
    var $host;//Servlinkor
    var $user;//Usuario
    var $pass;//Clave
    var $base;//Base de Datos.
    var $result = NULL;
    var $data;
    //Constructor
    //function Conexion( $host = "bd8.intrared.net:3305", $user = "sate_traris", $pass = "satetraris", $base = "sate_traris" )
    function Conexion( $host = HOSTXX, $user = USUARI, $pass = PASSWD, $base = DATABA )
    {
        //Encapsulacion de las variables.
        $this -> host = $host;
        $this -> user = $user;
        $this -> pass = $pass;
        $this -> base = $base;
        //Realizar Conexion y obtener el link.
        $this -> link = mysql_connect( $this -> host, $this -> user, $this -> pass );
        //Si no se realiza la conexion.
        if ( !$this -> link )
        {
            $error .= "<div align='center'>";
            $error .= "<br><b>No se Pudo Realizar la Conexion</b>";
            $error .= "<br><b>HOST:</b> ".$this -> host;
            $error .= "<br><b>USER:</b> ".$this -> user;
            $error .= "<br><b>PASS:</b> ".$this -> pass;
            $error .= "<br><b>BASE:</b> ".$this -> base;
            $error .= "<br><b>Error:</b> ".mysql_error();
            $error .= "<br><b>Msg:</b> ".mysql_errno();
            $error .= "</div>";			
			mail( "miguel.garcia@intrared.net", "CONEXION-Error", $error, 'Content-type: text/html; charset=iso-8859-1' );
			echo $error;
            die();
        }

        if( !mysql_select_db( $this -> base, $this -> link ) )
        {
            $error .= "<div align='center'>";
            $error .= "<br><b>No se Pudo Seleccionar la BASE DE DATOS.</b>";
            $error .= "<br><b>BASE:</b> ".$this -> base;
            $error .= "<br><b>Error:</b> ".mysql_error();
            $error .= "<br><b>Msg:</b> ".mysql_errno();
            $error .= "</div>";
			
			mail( "miguel.garcia@intrared.net", "DB-Error", $error );
			echo $error;
            die();
        }
    }

    function __destruct()
    {
        $this -> CloseConexion();
    }

    function Show()
    {
        if( $this -> data )
        {
            echo "<table border=''>";            
            foreach( $this -> data as $row )
            {
                echo "<tr>";
                if( $row )
                foreach( $row as $i )
                {
                    echo "<td>$i</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    function Consultar( $query, $array = NULL, $matriz = "", $show = "" )
    {
        $this -> result = mysql_query( $query, $this -> link );

        if ( !$this -> result )
        {
            echo "<div align='center'>";
            echo "<br><b>Error de Consulta</b>";
            echo "<br><b>Query:</b> ".$query;
            echo "<br><b>Error:</b> ".mysql_error();
            echo "<br><b>Msg:</b> ".mysql_errno();
            echo "</div>";
			
			mail( "miguel.garcia@intrared.net", "Query-Error", "Error: ".mysql_error()." \nQuery: ".$query );
			
            die();
        }

        if( $array && $matriz )
        {
            $this -> data = $this -> getMatriz( $array );
            if( $show ) $this -> Show();
        }
        elseif( $array )
        {
            $this -> data = $this -> getArray( $array );
            if( $show ) $this -> Show();
        }
        else
            $this -> data = $this -> result;
            
        return $this -> data;
    }
	
    //MYSQL_ASSOC, MYSQL_NUM, and MYSQL_BOTH
    function getArray( $type = "" )
    {
        if( $type == "t" ) $return = MYSQL_BOTH;
        if( $type == "a" ) $return = MYSQL_ASSOC;
        if( $type == "i" ) $return = MYSQL_NUM;
        return mysql_fetch_array( $this -> result, $return );
    }

    function getMatriz( $type )
    {
        $matriz = array();

        while( $row = $this -> getArray( $type ) )
        {
            $matriz[] = $row;
        }
        return $matriz;
    }

    function getCentral()
    {
        return $this -> cent;
    }

    function getBase()
    {
        return $this -> base;
    }

    function Start()
    {
        mysql_query( "START TRANSACTION", $this -> link );
    }

    function Commit()
    {
        mysql_query( "COMMIT", $this -> link );
    }

    function Rollback()
    {
        mysql_query( "ROLLBACK", $this -> link );
    }

    function CloseConexion()
    {
        mysql_close( $this -> link );
    }
}
?>

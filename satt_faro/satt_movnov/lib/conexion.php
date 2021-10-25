<?php
//Clase Utilizada para la conexion a la base de datos.
class Conexion
{
    //Atributos
    var $link = 0;//link
    var $host;//Servior
    var $user;//Usuario
    var $pass;//Clave
    var $base;//Base de Datos.
    var $cent;
    var $result = NULL;//Resource id
    var $data;
    
    //Constructor
    function __construct( $host = "", $user = "", $pass = "", $base = "", $cent = "" )
    {
        //Encapsulacion de las variables.
        $this -> host = $host;
        $this -> user = $user;
        $this -> pass = $pass;
        $this -> base = $base;
        $this -> cent = $cent;
        //Realizar Conexion y obtener el link.
        $this -> link = mysql_connect( $this -> host, $this -> user, $this -> pass );
        //Si no se realiza la conexion.
        if ( !$this -> link )
        {
            echo "<div align='center'>";
            echo "<br><b>No se Pudo Realizar la Conexion</b>";
            echo "<br><b>HOST:</b> ".$this -> host;
            echo "<br><b>USER:</b> ".$this -> user;
            //echo "<br><b>PASS:</b> ".$this -> pass;
            echo "<br><b>BASE:</b> ".$this -> base;
            echo "<br><b>Error:</b> ".mysql_error();
            echo "<br><b>Msg:</b> ".mysql_errno();
            echo "</div>";
            die();
        }

        if( !mysql_select_db( $this -> base, $this -> link ) )
        {
            echo "<div align='center'>";
            echo "<br><b>No se Pudo Seleccionar la BASE DE DATOS.</b>";
            echo "<br><b>BASE:</b> ".$this -> base;
            echo "<br><b>Error:</b> ".mysql_error();
            echo "<br><b>Msg:</b> ".mysql_errno();
            echo "</div>";
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
                if( $row && is_array( $row )  )
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
        {
            $this -> data = $this -> result;
        }
            
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
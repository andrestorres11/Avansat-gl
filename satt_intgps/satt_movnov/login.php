<?php
/* ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/
class Login
{
    var $conexion = NULL;
    var $mensaje = "";

    function Login( $conexion )
    {
        $this -> conexion = $conexion;

        switch( $_POST[option] )
        {
            case "in":
                $this -> Validar();
                break;
            
            case "cc":
                $this -> Insert();
            break;
            
            default:
                $this -> Formulario();
                break;
        }
    }

    function Formulario()
    {
        JS( "login" );
        $form = new Form( array( 'name'=>'form', 'enctype'=>'multipart/form-data' ) );
            $form -> Row();
                $form -> Label( array( "label" => "Usuario:" ) );
                $form -> Text( array( 'name'=>'user', 'value'=>$_POST['user'] ) );
            $form -> closeRow();
            $form -> Row();
                $form -> Label(  array( "label" => "Clave:" ) );
                $form -> Pass( array( 'name'=>'pass' ) );
            $form -> closeRow();
            $form -> Row();
                        $form -> Boton(  array( "label" => "Ingresar", "onclick" => "ValidarIngreso()", "colspan" => 2 ) );
            $form -> closeRow();
            $form -> Row();
                $form -> Hidden( array( "name" => "option" ));
                if( $this -> mensaje )
                    $form -> msg( $this -> mensaje );
            $form -> closeRow();
        $form -> closeForm();
    }

    function Validar()
    {
        $_POST[user] = str_replace( "'","", $_POST[user] );
        $_POST[pass] = md5( strtolower($_POST[pass]) );
        
        $buscar = $this -> getDespacho( $_POST['user'] );
        
        if( !$buscar[clv_satmov] )
            $this -> setDespacho( $buscar );
        
        $query = "SELECT b.num_placax, b.num_despac, b.clv_satmov
                    FROM tab_despac_despac a,
                         tab_despac_vehige b
                   WHERE b.num_placax = '".$_POST['user']."' 
                     AND b.clv_satmov = '".$_POST['pass']."' 
                     AND a.num_despac = b.num_despac 
                     AND a.fec_salida IS NOT NULL 
                     AND a.fec_llegad IS NULL 
                     AND a.ind_anulad = 'R' 
                     AND b.ind_activo = 'S' 
                ORDER BY b.num_despac DESC
                   LIMIT 1
                 ";
        //echo "<hr>".$query."<hr>";
        $validar = $this -> conexion -> Consultar( $query, "a" );
        
        /*echo "<pre>";
        print_r($buscar);
        echo "</pre>";*/
        
        if( $validar )
        {
            $_SESSION[satt_movil] = $validar;
            if( $validar[clv_satmov] == md5( strtolower($_POST[user]) ) )
            {
                $_SESSION[satt_movil][cc] = 1;
                $this -> Cambio_clave( $validar[clv_satmov] );
            }
            else
            {
                $_SESSION[satt_movil][cc] = 0;
                header( "location:index.php" );
            }
        }
        else
        {
            $this -> mensaje = "El <b>Usuario</b> o <b>Clave</b> no se Encuentran Registrados.";
            $this -> Formulario();
        }
    }

    function getDespacho( $num_placax = NULL )
    {
        $query = "SELECT b.num_placax, b.num_despac, b.clv_satmov
                    FROM tab_despac_despac a,
                         tab_despac_vehige b
                   WHERE b.num_placax = '".$num_placax."' 
                     AND a.num_despac = b.num_despac 
                     AND a.fec_salida IS NOT NULL 
                     AND a.fec_llegad IS NULL 
                     AND a.ind_anulad = 'R' 
                     AND b.ind_activo = 'S' 
                ORDER BY b.num_despac DESC
                   LIMIT 1
                 ";
        //echo "<hr>".$query."<hr>";
        return $this -> conexion -> Consultar( $query, "a" );
    }
    
    function setDespacho( $mData )
    {
        $query = "UPDATE tab_despac_vehige 
                     SET clv_satmov = MD5( '".strtolower( $mData[num_placax] )."' ) 
                   WHERE num_despac = '".$mData['num_despac']."'
                   LIMIT 1 ";
        //echo "<hr>".$query."<hr>";
        $this -> conexion -> Start();
        $insercion = $this -> conexion -> Consultar( $query );
        if( $insercion )
            $this -> conexion -> Commit();
    }

    function Cambio_clave( $pass_ant = NULL )
    {
        JS( "login" );
        $form = new Form( array( 'name'=>'form', 'enctype'=>'multipart/form-data' ) );
            $form -> Row();
                $form -> Label(  array( "label" => "Clave Nueva:" ) );
                $form -> Pass( array( 'name'=>'pass_nue' ) );
            $form -> closeRow();
            $form -> Row();
                $form -> Label(  array( "label" => "Confirmar Clave:" ) );
                $form -> Pass( array( 'name'=>'pass_con' ) );
            $form -> closeRow();
            $form -> Row();
                        $form -> Boton(  array( "label" => "Cambiar Clave", "onclick" => "ValidarCambio()", "colspan" => 2 ) );
            $form -> closeRow();
            $form -> Row();
                $form -> Hidden( array( "name" => "option" ));
                $form -> Hidden( array( "name" => "pass_ant", 'value' => $pass_ant ));
                if( $this -> mensaje )
                    $form -> msg( $this -> mensaje );
            $form -> closeRow();
        $form -> closeForm();
    }

    function Insert()
    {
        $this -> mensaje = "";

        $_POST[pass_nue] = md5( strtolower($_POST[pass_nue]) );
        $_POST[pass_con] = md5( strtolower($_POST[pass_con]) );
        
        
        if( $_POST[pass_con] != $_POST[pass_nue])
        {
            $this -> mensaje = "La <b>Nueva Clave</b> no es igual a la <b>Confirmacion Clave</b>.";
            $this -> Cambio_clave( $_POST[pass_ant] );
        }
        elseif( $_POST[pass_ant] == $_POST[pass_nue])
        {
            $this -> mensaje = "La <b>Nueva Clave</b> es igual a la <b>Clave anterior</b>.";
            $this -> Cambio_clave( $_POST[pass_ant] );
        }
        else
        {
            $query = "UPDATE tab_despac_vehige 
                         SET clv_satmov = '".$_POST['pass_con']."'
                       WHERE num_despac = '".$_SESSION[satt_movil][num_despac]."'
                       LIMIT 1 ";

            //echo "<hr>".$query."<hr>";
            $validar = $this -> conexion -> Consultar( $query );
            
            if( $validar )
            {
                $_SESSION[satt_movil][cc] = 0;
                header( "location:index.php" );
            }
            else
            {
                $this -> mensaje = "Error al actualizar la <b>Clave</b>.";
                $this -> Cambio_clave( $_POST[pass_ant] );
            }
        }
    }
}
$login = new Login( $this -> conexion );
?>
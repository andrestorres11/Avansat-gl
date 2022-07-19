function ValidarIngreso()
{
    try 
    {
        var formulario = document.getElementById( "form" );
        var user = document.getElementById( "user" );
        var pass = document.getElementById( "pass" );
        //var device = navigator.platform.toLowerCase();
        
        if( !user.value )
        {
            alert( "El usuario es requerido." );
            return user.focus();
        }
        if( !pass.value )
        {
            alert( "La clave es requerida." );
            return pass.focus();
        }
        
        document.getElementById( "option" ).value = 'in';
        formulario.submit();
    }  
    catch (e) 
    {
        alert( "Error " + e.message );
    }
}

function ValidarCambio()
{
    try 
    {
        var formulario = document.getElementById( "form" );
        var pass_nue = document.getElementById( "pass_nue" );
        var pass_con = document.getElementById( "pass_con" );
        var device = navigator.platform.toLowerCase();
        
        if( !pass_nue.value )
        {
            alert( "La nueva clave es requerido." );
            return pass_nue.focus();
        }
        
        if( !pass_con.value )
        {
            alert( "La confirmación de la clave es requerida." );
            return pass_con.focus();
        }
        
        document.getElementById( "option" ).value = 'cc';
        formulario.submit();
    }  
    catch (e) 
    {
        alert( "Error " + e.message );
    }
}
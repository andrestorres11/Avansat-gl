function validar()
{
    var form = document.getElementById( "form" );
    var cod_contro = document.getElementById( "cod_contro" );
    var tip_noveda = document.getElementById( "tip_noveda" );
    var ind_noveda = document.getElementById( "ind_noveda" );
    var cod_noveda = document.getElementById( "cod_noveda" );
    var option = document.getElementById( "option" );
    
    if( !cod_contro.value )
    {
        alert( "El puesto de control es requerida" );
        return cod_contro.focus();
    }
    
    if( !cod_noveda.value )
    {
        alert( "La novedad es requerida" );
        return cod_noveda.focus();
    }
    
    if( !tip_noveda.value )
    {
        alert( "El tipo de novedad es requerida" );
        return tip_noveda.focus();
    }
    
    if( ind_noveda.value == 4 || ind_noveda.value == 5 )
    {
        var num_docume = document.getElementById( "num_docume" );
        if( !num_docume.value )
        {
            alert( "El destinatario es requerida" );
            return num_docume.focus();
        }
    }
    
    if( ind_noveda.value == 5 )
    {
        var foto = document.getElementById( "foto" );
        if( !foto.value )
        {
            alert( "La foto es requerida" );
            return foto.focus();
        }
    }

    if( confirm( "¿ Esta seguro de enviar la informacion ?" ) )
    {
        option.value = "1";
        form.submit();
    }
}

function Puestoactual()
{
    var form = document.getElementById( "form" );
    var action = document.getElementById( "action" );
    action.value = "1";
    form.submit();
}

function Llegada()
{
    var form = document.getElementById( "form" );
    var action = document.getElementById( "action" );
    action.value = "2";
    form.submit();
}
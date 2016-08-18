function validaIngresar(form)
{
        if(!form.usuario.value || !form.clave.value)
        {
                alert('Digite Usuario y Clave');
                form.usuario.focus();
                return(false)
        }
        else form.op.value = 1;
}
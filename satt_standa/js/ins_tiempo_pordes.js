function soloNumeros(evt)
{
	if(window.event)
	{
		keynum = evt.keyCode;
	}
	else
	{
		keynum = evt.which;
	}
	if((keynum>47 && keynum<58) || keynum<09)
	{
		return true;
	}
	else
	{
		return false;

	}
}

function Validar()
{	
	var tiempo = document.form_insert.tie_seguim;
	var observ = document.form_insert.obs_tiedes;
	if( !tiempo.value )
	{
		alert("El tiempo de Seguimiento es Obligatorio");
		tiempo.focus();
		return false;
	}
	else if( !observ.value )
	{
		alert("La observación del Seguimiento es Obligatoria");
		observ.focus();
		return false;
	}
	else
	{
		document.form_insert.submit();
	}
}
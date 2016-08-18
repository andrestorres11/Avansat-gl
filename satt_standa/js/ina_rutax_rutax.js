function Valida_Inactivar()
{
	var ruta = document.form_item.num_ruta.value;
	var tipo = document.form_item.tipter.value;
	var msg;
	
	if (tipo == 1)
	{
		msg ="Inactivar";
	}
	else
	{
		msg ="Activar";
	}
	
	var answer = confirm("Seguro que desea "+msg+" la ruta No. " +ruta+ " ?");
	if(answer)
	{
		document.form_item.submit();
	}
	else
	{
		return false;
	}
}
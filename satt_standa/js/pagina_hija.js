function hija(usr, pw, hijo, codigo, servic, dbase, tipo, alto, op, matvar, fieldid)
{var obj ="";
  var cadenadicivar = "";  
  for(i = 0; i < fieldid.length; i++)
  {
    if(document.getElementById(fieldid[i]) != null)
    {
  		auxcadena = "&valobj" + i + "=" + document.getElementById(fieldid[i]).value;
   		obj += auxcadena;
   	}
   	else
   		obj += '';
   }

  for(i = 0; i < matvar.length; i++)
  {
   auxcadena = "&" + matvar[i];
   cadenadicivar += auxcadena;
  }

	izquierda = (screen.width) ? (screen.width-600)/2 : 100
        arriba = (screen.height) ? (screen.height-alto)/2 : 100
        opciones = 'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=' + 600 + ',height=' + alto + ',left=' + izquierda + ',top=' + arriba + ''
	if(tipo == 'e')
	{
		if(confirm('¿Esta Seguro de Eliminar el Registro?'))
        		window.open('index.php?window=central&cod_servic='+hijo+'&codigo='+codigo+'&cod_serant='+servic+'&nom_basdat='+dbase+cadenadicivar+obj,'popup', opciones);
	}
	else if(tipo == 'u')
	{
		if(confirm('¿Esta Seguro de Anular el Registro?'))
        		window.open('index.php?window=central&cod_servic='+hijo+'&codigo='+codigo+'&cod_serant='+servic+'&nom_basdat='+dbase+cadenadicivar+obj,'popup', opciones);
	}
	else
		window.open('index.php?window=central&cod_servic='+hijo+'&codigo='+codigo+'&cod_serant='+servic+'&nom_basdat='+dbase+'&opcion='+op+cadenadicivar+obj, 'popup', opciones);
}
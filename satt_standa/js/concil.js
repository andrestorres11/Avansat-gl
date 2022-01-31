/**
 * @author Diana Paola Cadena Quintero
 */
function validarCumplidos()
{
	try
	{
		var cumplidos = Number( formulario.siz_cumpli.value);
		paso =false;
		for (var i=1; i<=cumplidos; i++)
		{
			var cod = "cod_remdes"+i;


			if(formulario[cod].checked)
			{
				/*if(!formulario["val_pesoxx"+i].value)
				{
					alert("El Campo Peso es Requerido");
					return formulario["val_pesoxx"+i].focus();
				}*/
				paso = true;
			}
		}
		if(paso)
		formulario.submit();
		else
			alert("Seleccione Alguna Remision para Registrar la Conciliación.");
	}
	catch (e)
	{
		alert("Error "+e.message);
	}
}

function sendDespacho()
{
	try
	{
		var fRow = document.getElementById("ActualRowID").value;
		var codigo = document.getElementById("DLLink" + fRow + "-0").innerHTML;

		formulario.num_despac.value = codigo;
		formulario.submit();
	}
	catch (e)
	{
		alert("Error sendDespacho " + e.message);
	}
}

function irAtras()
{
    try
	{
    	formulario.opcion.value = 0;
		formulario.submit();
    }
    catch (e)
	{
    	alert("Error irAtras() "+e.message)
    }

}

function eliminarCumplidos()
{
	try
	{
		var val = false;
		var cumplidos = Number( formulario.siz_cumpli.value);
		for (var i = 1; i <= cumplidos; i++)
		{
			var cod = "cod_remdes" + i;

			if (formulario[cod].checked)
			{
				val = true;
			}
		}

		if(!val)
		{
			alert("Seleccione la Conciliación que desea eliminar.");
			return false;
		}

		if(confirm("Esta seguro que desea eliminar las Conciliaciones."))
		{
			formulario.submit();
		}

	}
	catch(e)
	{
		alert("Error "+e.message);
	}
}

function tabla(imageURL,imageTitle)
{
 PositionX = 150;
 PositionY = 40;
 defaultWidth = 1050;
 defaultHeight = 800;
 var AutoClose = true;

 if (parseInt(navigator.appVersion.charAt(0))>=4)
 {
 var isNN=(navigator.appName=="Netscape")?1:0;
 var isIE=(navigator.appName.indexOf("Microsoft")!=-1)?1:0;
 }
 var optNN='scrollbars=no,width='+defaultWidth+',height='+defaultHeight+',left='+PositionX+',top='+PositionY;
 var optIE='scrollbars=no,width=150,height=100,left='+PositionX+',top='+PositionY;

 imgWin=window.open('about:blank','Cconciliaciones',optIE);

 with (imgWin.document)
 {
  writeln('<html><head><title>Cargando ...</title><style>body{margin:0px;}</style>');
  writeln('<sc'+'ript>');
  writeln('var isIE;');
  writeln('var isNN,isIE;');
  writeln('if (parseInt(navigator.appVersion.charAt(0))>=4){');
  writeln('isNN=(navigator.appName=="Netscape")?1:0;');
  writeln('isIE=(navigator.appName.indexOf("Microsoft")!=-1)?1:0;}');
  writeln('function reSizeToImage(){');

  writeln('if (isIE){');
  writeln('window.resizeTo(800,100);');
  writeln('width=(550)');
  writeln('height=(700)');
  writeln('window.resizeTo(width,height);}');

  writeln('if (isNN){');
  writeln('window.innerWidth=550;');
  writeln('window.innerHeight=700;}');

 writeln('}function doTitle(){document.title="'+imageTitle+'";}');
 writeln('</sc'+'ript>');
 if (!AutoClose)
	writeln('</head><body bgcolor=000000 scroll="no" onload="reSizeToImage();doTitle();self.focus()">')
 else
	writeln('</head><body bgcolor=ffffff scroll="no" onload="reSizeToImage();doTitle();self.focus()" onblur="self.close()">');

 writeln('<img name="imagenes" src='+imageURL+' width = "550" height="700" style="display:block"></body></html>');
 close();
 }
}
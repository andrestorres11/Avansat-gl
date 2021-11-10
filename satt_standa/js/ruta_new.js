$("document").ready(function(){

    var standa = $("#standaID").val();
    var atributes  = '&Ajax=on&standa='+standa;
    
    $("#origenID").autocomplete({
        source: "../"+ standa +"/rutas/class_rutasx_rutasx.php?Option=getCiudades"+ atributes,
        minLength: 3,
        select: function( event, ui ) {
            $("#cod_ciuoriID").val( ui.item.id );
        }
    });

    $("#destinoID").autocomplete({
        source: "../"+ standa +"/rutas/class_rutasx_rutasx.php?Option=getCiudades"+ atributes,
        minLength: 3,
        select: function( event, ui ) {
            $("#cod_ciudesID").val( ui.item.id );
        }
    });

    $("input[name^='contr']").each(function(){
        $(this).autocomplete({
            source: "../"+ standa +"/rutas/class_rutasx_rutasx.php?Option=getPC"+ atributes,
            minLength: 3,
            select: function( event, ui ) {
                var nameID = $(this).attr("nameID");
                $("#"+nameID).val( ui.item.id );
            }
        });
    });

});


function aceptar_insert(formulario)
{
    validacion = true
    formulario = document.form_ins
    console.log(formulario);

    var numerico = /^[0-9]+\.?[0-9]*$/

    if (formulario.ind_doblevia && formulario.ind_doblevia.checked) {
        formulario.ind_doblevia.value = "1";
    }

    if(formulario.origen.value == "0")
    {
     window.alert("La Ciudad de Origen es Requerida.")
     validacion = false
     formulario.origen.focus();
    }
    else if(formulario.destino.value == "0")
    {
     window.alert("La Ciudad de Destino es Requerida.")
     validacion = false
     formulario.destino.focus();
    }
    else if(formulario.cod_viasxx && formulario.cod_viasxx.value == "" || formulario.cod_viasxx.value == 0)
    {
     window.alert("Seleccione una via")
     validacion = false
     formulario.cod_viasxx.focus();
    }
    else if(validar() == false)
    {
     validacion = false
    }
    else if(formulario.tiempcult.value == "")
    {
     window.alert("Debe Asignar los Minutos Desde el Origen Para Ultimo Puesto de Control")
     validacion = false
     formulario.tiempcult.focus();
    }
    else if(!numerico.test(formulario.tiempcult.value))
    {
     window.alert("Los Minutos Desde el Origen Deben Contener Solo Valores Numericos.")
     validacion = false
     formulario.tiempcult.focus();
    }
    else if(parseInt(formulario.tiempcult.value) <= valorMayor())
    {
     window.alert("Los Minutos Desde el Origen Para Ultimo Puesto de Control Debe ser Mayor que los Anteriores.")
     validacion = false
     formulario.tiempcult.focus();
    }
    else
    {
     if(confirm("Desea Insertar la Ruta.?"))
     {
      formulario.opcion.value= 1;
      formulario.submit();
     }
    }
}

function aceptar_actuali(formulario)
{
    validacion = true
    formulario = document.form_item

    var numerico = /^[0-9]+\.?[0-9]*$/

    if(formulario.origen.value == "0")
    {
     window.alert("La Ciudad de Origen es Requerida.")
     validacion = false
     formulario.origen.focus();
    }
    else if(formulario.destino.value == "0")
    {
     window.alert("La Ciudad de Destino es Requerida.")
     validacion = false
     formulario.destino.focus();
    }
    else if(formulario.cod_viasxx.value == "")
    {
     window.alert("La Descripcion de la Via es Requerida")
     validacion = false
     formulario.cod_viasxx.focus();
    }
    else if(validarA() == false)
    {
     validacion = false
    }
    else if(formulario.timepcult.value == "")
    {
     window.alert("Debe Asignar los Minutos Desde el Origen Para Ultimo Puesto de Control")
     validacion = false
     formulario.timepcult.focus();
    }
    else if(!numerico.test(formulario.timepcult.value))
    {
     window.alert("Los Minutos Desde el Origen Deben Contener Solo Valores Numericos.")
     validacion = false
     formulario.timepcult.focus();
    }
    else if(parseInt(formulario.timepcult.value) <= valorMayor())
    {
     window.alert("Los Minutos Desde el Origen Para Ultimo Puesto de Control Debe ser Mayor que los Anteriores.")
     validacion = false
     formulario.timepcult.focus();
    }
    else
    {
     if(confirm("Desea Modificar la Ruta.?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function aceptar_copia(formulario)
{
    validacion = true
    formulario = document.form_item

    var numerico = /^[0-9]+\.?[0-9]*$/

    if (formulario.ind_doblevia && formulario.ind_doblevia.checked) {
        formulario.ind_doblevia.value = "1";
    }


    if(formulario.origen.value == "0")
    {
     window.alert("La Ciudad de Origen es Requerida.")
     validacion = false
     formulario.origen.focus();
    }
    else if(formulario.destino.value == "0")
    {
     window.alert("La Ciudad de Destino es Requerida.")
     validacion = false
     formulario.destino.focus();
    }
    else if(formulario.ind_doblevia && (formulario.cod_viasxx.value == "" || formulario.cod_viasxx.value == 0))
    {
     window.alert("La Descripcion de la Via es Requerida")
     validacion = false
     formulario.cod_viasxx.focus();
    }
    else if(validarA() == false)
    {
     validacion = false
    }
    else if(formulario.timepcult.value == "")
    {
     window.alert("Debe Asignar los Minutos Desde el Origen Para Ultimo Puesto de Control")
     validacion = false
     formulario.timepcult.focus();
    }
    else if(!numerico.test(formulario.timepcult.value))
    {
     window.alert("Los Minutos Desde el Origen Deben Contener Solo Valores Numericos.")
     validacion = false
     formulario.timepcult.focus();
    }
    else if(formulario.timepcult && parseInt(formulario.timepcult.value) <= valorMayor())
    {
     window.alert("Los Minutos Desde el Origen Para Ultimo Puesto de Control Debe ser Mayor que los Anteriores.")
     validacion = false
     formulario.timepcult.focus();
    }
    else
    {
     if(confirm("Desea Copiar e Insertar la Ruta.?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function valorMayor()
{
    var frm = document.forms[0];
    var valido = true;
    var cont = 0;
    var i = 0;
    var valores = new Array()
    var mayor = 0;

    while(frm.elements[cont])
    {
        nombre = "val[" + i + "]";

        if(frm.elements[cont].name == nombre && frm.elements[cont].type == "text")
        {
	 valores[i] = parseInt(frm.elements[cont].value);
	 i++
	}
	cont++
    }

    for(j = 0; j <= i; j++)
    {
	if(valores[j] > mayor)
	 mayor = valores[j]
    }

    return mayor
}

function validar()
{
    var frm = document.forms[0];
    var valido = true;
    var cont = 0;
    var i = 0;

    var numerico = /^[0-9]+\.?[0-9]*$/

    while(frm.elements[cont])
    {
            if(frm.elements[cont].type == "select-one")
            {
		nombre = "contro[" + i + "]";
                if(frm.elements[cont].name == nombre)
                {
		   if(frm.elements[cont].value == "0")
		   {
			valido = false;
			if(i == 0)
				window.alert("Debe Seleccionar Por lo Menos un Puesto de Control Dentro de la Ruta.");
			else window.alert("Debe Seleccionar el Puesto de Control.");
			frm.elements[cont].focus();
			cont=1000;
		   }
		   else if(frm.elements[cont+1].value == "")
		   {
			valido = false;
			window.alert("Debe Asignar los Minutos Desde el Origen.");
			frm.elements[cont+1].focus();
			cont=1000;
		   }
		   else if(!numerico.test(frm.elements[cont+1].value) && frm.elements[cont+1].value != "")
		   {
			valido = false;
			window.alert("Los Minutos Desde el Origen Deben Contener Solo Valores Numericos.");
			frm.elements[cont+1].focus();
			cont=1000;
		   }
		   i++;
                }
            }

            cont += 1
    }

    return valido;
}

function validarA()
{
  var frm = document.forms[0];
  var valido = true;
  var cont = 0;
  var i = 0;
  var j = 0;

  var numerico = /^[0-9]+\.?[0-9]*$/

  while(frm.elements[cont])
  {
    nombre = "contro[" + i + "]";

    if(frm.elements[cont].name == nombre)
    {
      if(frm.elements[cont-2].checked == true)
      {
        j++;
        if(frm.elements[cont].value == "")
        {
          valido = false;
          if(i == 0)
            window.alert("Debe Seleccionar Por lo Menos un Puesto de Control Dentro de la Ruta.");
          else window.alert("Debe Seleccionar el Puesto de Control.");
            frm.elements[cont].focus();
          cont=1000;
        }
        else if(frm.elements[cont+1].value == "")
        {
          valido = false;
          window.alert("Debe Asignar los Minutos Desde el Origen.");
          frm.elements[cont+1].focus();
          cont=1000;
        }
        else if(!numerico.test(frm.elements[cont+1].value))
        {
          valido = false;
          window.alert("Los Minutos Desde el Origen Deben Contener Solo Valores Numericos.");
          frm.elements[cont+1].focus();
          cont=1000;
        }
      }
      i++;
    }

    cont += 1
  }

  if(j == 0)
  {
      window.alert("Debe Seleccionar Por lo Menos un Puesto de Control Dentro de la Ruta.");
      return false;
  }

  return valido;
}

function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list
    if(formulario.ruta.value == "")
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}

function aceptar_act(formulario)
{
    validacion = true
    formulario = document.form_act
    if(formulario.ruta.value == "")
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

function aceptar_eli(formulario)
{
    validacion = true
    formulario = document.form_eli
    if(formulario.ruta.value == "")
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}
function aceptar_act2(formulario)
{
    validacion = true
    formulario = document.form_item
    if(formulario.nombre.value == '')
    {
     window.alert("El Nombre la Rutas es Requerido")
     validacion = false
    }
    else if(formulario.origen.value == formulario.destino.value)
    {
     window.alert("El Origen Debe ser Diferente al Destino")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 3;
    formulario.submit();
    }
}
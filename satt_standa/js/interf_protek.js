function ValidateIt()
{
  var num_despac = document.getElementById( "num_manifiID" );
  var formulario = document.getElementById( "formularioID" );
  if( !num_despac.value )
  {
    alert("Digite el Numero de Manifiesto");
  }
  else
  {
    formulario.submit();
  }
  
}

function VerifyAnula()
{
  var manifi = document.getElementById("num_manifiID");
  if( confirm( "¿Realmente desea Anular el Escolta Electronico para el manifiesto No." + manifi.value + "?") )
  {
    document.form.submit();
  }
  else
  {
    return false;
  }
}

function VerifyEscolta()
{
  var ruta = document.getElementById("cod_rutaxID");
  var fecha = document.getElementById("fec_prograID");
  var hora = document.getElementById("hor_prograID");
  var conte = document.getElementById("num_contenID");
  var clien = document.getElementById("cod_clientID");
  var form = document.getElementById("formularioID");
  
  var exp_conten = /^[a-zA-Z]{4}[0-9]{5,7}$/;

  if( !clien.value )
  {
    alert("Seleccione el Cliente");
    clien.focus();
    return false;
  }
  else if( !conte.value )
  {
    alert("Digite el Numero del Contenedor");
    conte.focus();
    return false;
  }
  else if( !conte.value.match(exp_conten) )
  {
    alert("Formato del Numero del Contenedor Incorrecto");
    conte.focus();
    return false;
  }
  else if( !ruta.value )
  {
    alert("Seleccione la Ruta");
    ruta.focus();
    return false;
  }
  else if( !fecha.value )
  {
    alert("Seleccione la Fecha");
    fecha.focus();
    return false;
  } 
  else if( !hora.value )
  {
    alert("Seleccione la Hora");
    hora.focus();
    return false;
  }
  else
  {
    document.form.submit();
  }
}
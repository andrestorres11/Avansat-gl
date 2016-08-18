function Validate_Form ()
{
  try
  {
  
    /*var origen = document.form_tramo.origen.value;
    var destin = document.form_tramo.destin.value;
    
    if(origen == '0' && destin == '0' )  {
      alert('Debe selecionar el origen y el destino.')
      return false;
    }
    else if(origen == '0')  {
      alert('Debe selecionar el origen.')
      return false;
    }
    else if(destin == '0')  {
      alert('Debe selecionar el destino.')
      return false;
    }
    else  {*/
      
        document.form_tramo.submit();
      
    /*}*/
  }
  catch(e)
  {
    alert("Error Function: Validate_Form -> "+ e.message);
  }
}

function Validate_FormTramo()
{
  try
  {
    var Rows = document.form_tramo.rows.value;
    var rutanom = document.getElementsByName('rutanom[]');
    
    var controa = document.getElementsByName('controa[]');
    var controb = document.getElementsByName('controb[]');
    //------------------------------------------------------
    var km =  document.getElementsByName('km[]');
    var vl =  document.getElementsByName('vl[]');
    var tm =  document.getElementsByName('tm[]');
    //------------------------------------------------------
    
    for(var a = 0; a <= Rows-1; a++)  { 
      if(km[a].value == '')  {
        alert("Debe digitar los Kilómetros desde " +controa[a].value + "  hasta  "+ controb[a].value+".");
        km[a].focus();
        return false;
      }
      else if(vl[a].value == '')  {
        alert("Debe digitar la velocidad desde " +controa[a].value + "  hasta  "+ controb[a].value+".");
        vl[a].focus();
        return false;
      }
      else if(tm[a].value == '')  {
        alert("Debe digitar el tiempo en minutos desde " +controa[a].value + "  hasta  "+ controb[a].value+".");
        tm[a].focus();
        return false;
      }     
    }
    if(confirm('Desea agregar los datos de los tramos de la ruta: \n'+ rutanom[0].value+' ?'))
      document.form_tramo.submit();
    //------------------------------------------------------
  }
  catch(e)
  {
    alert("Error Function: Validate_FormTramo -> "+ e.message);
  }
}

function soloNumeros(evt)
{
  try
  {
    //asignamos el valor de la tecla a keynum
    if(window.event){// IE
    keynum = evt.keyCode;
    }else{
    keynum = evt.which;
    }
    //comprobamos si se encuentra en el rango
    if((keynum>47 && keynum<58) || keynum == 8 ){
    return true;
    }else{
    return false;
    }
  }
  catch(e)
  {
    alert("Error Function: soloNumeros -> "+ e.message);
  }
}

function Volver_Form()
{
  try
  {
    document.form_tramo.opcion.value = '';
    document.form_tramo.submit();
  }
  catch(e)
  {
    alert("Error Function: Volver -> "+ e.message);
  }
}

function Disable_Tramo ()
{
  var ruta = document.form_tramo.rutax.value;
  var rutanom = document.getElementsByName('rutanom[]');
  
  if(confirm('Desea desactivar los tramos para la ruta: '+ruta+'  \n'+rutanom[0].value+' ?'))
  {
    document.form_tramo.opcion.value = 'disable';
    document.form_tramo.submit();
    //alert(disa);
  }
}











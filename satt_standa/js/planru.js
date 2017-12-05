function aceptar_insert(formulario)
{
    var texto = $("textarea[name=obs]").val();
    texto = texto.replace(/\n/g, "<br>");
    $("textarea[name=obs").val(texto);

    var fec_citcar = $('input[name=fec_citcar]').val();
    var fecprosal = $('input[name=fecprosal]').val();
    validacion = true;
    formulario = document.form_ins;


    $valid_pc = validar_pc(formulario.desurb.value);

    if($valid_pc != "")
    {
     window.alert($valid_pc)
     validacion = false
    }
    else if(!$(':radio').is(':checked')) 
    {
      window.alert('Debe seleccionar una ruta.')
      validacion = false
    }
    else if( $('#ind_valcitID').val() == '1')
    { 
      if(fec_citcar < fecprosal )
      {
        alert("La fecha Programada de salida no puede ser mayor a la fecha citada de cargue, configurada en la creacion del despacho")
        return false;
      }
      else
      {
       if(confirm("Desea Insertar el Plan de Ruta Para el Despachos # " + formulario.despac.value + ".?"))
       {
        formulario.opcion.value= 3;
        formulario.submit();
       }
      }
    }
    else
    {
     if(confirm("Desea Insertar el Plan de Ruta Para el Despachos # " + formulario.despac.value + ".?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function SelectGps ()
{
  //alert(document.form_ins.ope_gpsxxx.value);
  document.form_ins.opcion.value= 2;
  document.form_ins.submit();
}

function validar_pc(desurb)
{
	//EXPRESIONES REGULARES PARA VALIDAR LOS FORMATOS
    var tiemp     = /[0-9]/

	var i;
    var frm = document.forms[0];
    cont = 0;

    while(frm.elements[cont])
    {
      if(frm.elements[cont].type == "checkbox" && !frm.elements[cont].checked && frm.elements[cont].id != "ind_salida")
      {
        if(frm.elements[cont+2].value != "")
        {
          frm.elements[cont].focus()
          return "Debe Seleccionar el Puesto de Control."
        }
        cont += 2
      }
      
      if(frm.elements[cont].type == "checkbox" && frm.elements[cont+1].value != "0" && frm.elements[cont+1].type == "select-one")
      {
       if(frm.elements[cont+2].value == "" || parseInt(frm.elements[cont+2].value) < 1)
       {
        frm.elements[cont+2].focus()
        return "Debe Indicar el Tiempo para La Novedad."
       }
       else if(frm.elements[cont+2].value && !tiemp.test(frm.elements[cont+2].value))
       {
        frm.elements[cont+2].focus()
        return "El Valor del Tiempo es Numerico."
       }
      cont += 2
     }     
     else if(frm.elements[cont].type == "checkbox" && frm.elements[cont+1].value == "0" && frm.elements[cont+2].value != "" && frm.elements[cont].id != "ind_salida")
     {
      frm.elements[cont+1].focus()
       return "Debe Indicar La Novedad."
      cont += 2
     }
           cont += 1
    }
    if(document.form_ins.id_gps.value == '0' || document.form_ins.id_gps.value == '1' )
    {
      if(document.form_ins.id_gps.value == 1)
      {
        if( document.form_ins.idx_gpsxxx.value == '')
        {
          document.form_ins.idx_gpsxxx.focus();
          return "Debe digitar el ID de la operadora GPS."
        }
      }
      else if(document.form_ins.usr_gpsxxx.value == '')
      {
        document.form_ins.usr_gpsxxx.focus();
        return "Debe digitar el usuario para autenticar con la operadora GPS"
      }
      else if (document.form_ins.clv_gpsxxx.value == '')
      {
        document.form_ins.clv_gpsxxx.focus();
        return "Debe digitar la clave para autenticar con la operadora GPS"
      }
    }

	return "";
}
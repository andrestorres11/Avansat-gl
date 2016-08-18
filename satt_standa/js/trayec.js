function aceptar_act(){
    validacion = true
    formulario = document.form_act
    var tiemp = /[0-9]/
    
    if(formulario.nombre.value == "" || tiemp.test(formulario.nombre.value))
    {
     if(formulario.nombre.value == "") 
     	window.alert("El Nombre es Requerido.")
     else
     	window.alert("El Nombre es Alfabetico.")
     validacion = false
     formulario.nombre.focus()
    }
    else
    { 
     if(confirm('Desea Actualiza el Trayecto.?'))
     {
      formulario.opcion.value = 3; 
      formulario.submit();
     }     
    }
}

function ins_tab_trayec(formulario)
{
	var tiemp = /[0-9]/
    validacion = true
    formulario = document.form_insert
    if(formulario.nom.value == "" || tiemp.test(formulario.nom.value))
    {
     if(formulario.nom.value == "") 
     	window.alert("El Nombre es Requerido.")
     else
     	window.alert("El Nombre es Alfabetico.")
     validacion = false
     formulario.nom.focus()
    }
    else
    { 
     formulario.opcion.value= 2;
     formulario.submit();
    }
}

function aceptar_lis(){
    validacion = true
    formulario = document.form_list
    if(formulario.trayec.value == "")
    {
     window.alert("Digite el Nombre del Trayecto.")
     formulario.trayec.focus()
     validacion = false
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }

}

function aceptar_eli(){
	validacion = true
    formulario = document.form_eli
    var tiemp = /[0-9]/
    
    if(formulario.trayec.value == "" || tiemp.test(formulario.trayec.value))
    {
     if(formulario.trayec.value == "") 
     	window.alert("El Nombre del Trayecto es Requerido.")
     else
     	window.alert("El Nombre del Trayecto es Alfabetico.")
     validacion = false
     formulario.trayec.focus()
    }
    else
    { 
      formulario.opcion.value = 1; 
      formulario.submit();
     }     
}   
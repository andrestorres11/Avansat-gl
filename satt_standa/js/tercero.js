function validar(cant)

{

          var i;

          var counter=0;

          var frm = document.forms[0];

          var chi = frm.checkeditems;

          var offset = 0;



          while (frm.elements[offset].type!="checkbox")

          offset++;



          for (i=0;i < cant; i++){



            if (frm.elements[offset].checked) {

              counter++;

            // alert(frm.elements[offset].name)

            }

            offset++;



          } // next (i)



          if (counter==0){

           return (false);

          }

          else

          {

           return(true);

          }

}

function val_text()

{

          var i;

          var counter=0;

          var frm = document.forms[0];

          var chi = frm.checkeditems;

          var offset = 0;





           for (i=0;i < frm.length; i++){



          if(frm.elements[offset].type!="text")

            {

                    if (frm.elements[offset].name == 'nomb') {

                      counter++;

                     alert(frm.elements[offset].name)

                    }



           }

            offset++;



          }





}

function validar_posee(formulario)

{

    formulario = document.form_posee

 if(formulario.posee.value == "")

    {

        window.alert("El NIT o la CC es Requerida")

            validacion = false

    }

 else if(formulario.ciudad.value == '0')

    {

        window.alert("La Ciudad es Requerida")

            validacion = false

    }

    else

     {

      if(confirm("Esta Seguro que Desea Ingresar el Poseedor "+formulario.posee.value+"?"))

      {

     formulario.opcion.value= 8;

     formulario.submit();

      }

     }



}

function validar_form(formulario)

{

    formulario = document.form_tercero



 if(formulario.tercer.value == "")

    {

        window.alert("El NIT o la CC es Requerida")

            validacion = false

    }

 else if(formulario.ciudad.value == '0')

    {

        window.alert("La Ciudad es Requerida")

            validacion = false

    }

    else

     {

      if(confirm("Esta Seguro que Desea Ingresar el Tercero "+formulario.tercer.value+"?"))

      {

     formulario.opcion.value= 3;

     formulario.submit();

      }

     }



}

function validar_conduc(formulario)

{

    formulario = document.form_conduc

 if(formulario.conduc.value == "")

    {

        window.alert("La Cedula es Requerida")

            validacion = false

    }

 else if(formulario.ciudad.value == '0')

    {

        window.alert("La Ciudad es Requerida")

            validacion = false

    }

    else

     {

      if(confirm("Esta Seguro que Desea Ingresar el Conductor "+formulario.conduc.value+"?"))

      {

     formulario.opcion.value= 6;

     formulario.submit();

      }

     }



}

function aceptar_ins(formulario)
{
    var id = /^[0-9]{6,11}$/
    var valcorreo = /^(.+\@.+\..+)$/

    formulario = document.form_tercero

	if(formulario.transp.value == "0")
    {
     window.alert("La Transportadora es Requerida")
     formulario.transp.focus()
    }
    else if(formulario.tercer.value == "")
    {
     window.alert("El NIT o la CC es Requerida")
     formulario.tercer.focus()
    }
    else if(!id.test(formulario.tercer.value))
    {
     window.alert("El NIT ó Cedula debe contener los siguientes formatos:\n\n\tCedula\t 80221219\n\tNIT\t 8300766694\n\nLa Cedula no puede tener menos de 6 digitos")
     validacion = false
     formulario.tercer.focus()
    }
    else if(formulario.tipdoc.value == '0')
    {
     window.alert("El Tipo de Documento es Requerido")
     validacion = false
     formulario.tipdoc.focus()
    }
    else if(formulario.nom.value == "")
    {
     window.alert("El Nombre del Tercero es Requerido")
     validacion = false
     formulario.nom.focus()
    }
    else if(formulario.abr.value == "")
    {
     window.alert("La Abreviatura del Tercero es Requerida")
     validacion = false
     formulario.abr.focus()
    }
    else if(formulario.regimen.value == "0")
    {
     window.alert("La Seleccion del Regimen es Requerido")
     validacion = false
     formulario.regimen.focus()
    }
    else if(formulario.ciudad.value == '0')
    {
     window.alert("La Ciudad es Requerida")
     validacion = false
     formulario.ciudad.focus()
    }
    else if(formulario.dir.value == "")
    {
     window.alert("La Direccion es Requerida")
     validacion = false
     formulario.dir.focus()
    }
    else if(formulario.tel.value == "")
    {
     window.alert("EL # de Telefono es Requerido")
     validacion = false
     formulario.tel.focus()
    }
    else if (formulario.correo.value != "" && !valcorreo.test(formulario.correo.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     formulario.correo.focus()
    }
    else  if(!validar(formulario.maximo.value))
    {
     alert("Debe seleccionar minimo una actividad")
    }
    else
    {
     if(confirm("Esta Seguro que Desea Ingresar el Tercero "+formulario.tercer.value+"?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function aceptar_ins_n(formulario)
{
    var id = /^[0-9]{6,11}$/
    var valcorreo = /^(.+\@.+\..+)$/

    formulario = document.form_tercero

	if(formulario.transp.value == "0")
    {
     window.alert("La Transportadora es Requerida")
     formulario.transp.focus()
    }
    else if(formulario.tipdoc.value == "0")
    {
     window.alert("El Tipo de Documento es Requerido")
     formulario.tipdoc.focus()
    }
    else if(formulario.tercer.value == "")
    {
     window.alert("El NIT o la CC es Requerida")
     formulario.tercer.focus()
    }
    else if(!id.test(formulario.tercer.value))
    {
     window.alert("El NIT ó Cedula debe contener los siguientes formatos:\n\n\tCedula\t 80221219\n\tNIT\t 8300766694\n\nLa Cedula no puede tener menos de 6 digitos")
     validacion = false
     formulario.tercer.focus()
    }
    else if(formulario.tipdoc.value == '0')
    {
     window.alert("El Tipo de Documento es Requerido")
     validacion = false
     formulario.tipdoc.focus()
    }
    else if(formulario.nom.value == "")
    {
     window.alert("El Nombre del Tercero es Requerido")
     validacion = false
     formulario.nom.focus()
    }
    else if(formulario.apell1.value == "")
    {
     window.alert("El Primer Apellido del Tercero es Requerido")
     validacion = false
     formulario.apell1.focus()
    }
    else if(formulario.ciudad.value == '0')
    {
     window.alert("La Ciudad es Requerida")
     validacion = false
     formulario.ciudad.focus()
    }
    else if(formulario.dir.value == "")
    {
     window.alert("La Direccion es Requerida")
     validacion = false
     formulario.dir.focus()
    }
    else if(formulario.tel.value == "")
    {
     window.alert("EL # de Telefono es Requerido")
     validacion = false
     formulario.tel.focus()
    }
    else if (formulario.correo.value != "" && !valcorreo.test(formulario.correo.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     formulario.correo.focus()
    }
    else  if(!validar(formulario.maximo.value))
    {
     alert("Debe seleccionar minimo una actividad")
    }
    else
    {
     if(confirm("Esta Seguro que Desea Ingresar el Tercero "+formulario.tercer.value+"?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function aceptar_update(formulario)
{
    var id = /^[0-9]{6,11}$/
    var valcorreo = /^(.+\@.+\..+)$/

    formulario = document.form_insert

    if(formulario.tipdoc.value == '0')
    {
     window.alert("El Tipo de Documento es Requerido")
     validacion = false
     formulario.tipdoc.focus()
    }
    else if(formulario.nom.value == "")
    {
     window.alert("El Nombre del Tercero es Requerido")
     validacion = false
     formulario.nom.focus()
    }
    else if(formulario.abr.value == "")
    {
     window.alert("La Abreviatura del Tercero es Requerida")
     validacion = false
     formulario.abr.focus()
    }
    else if(formulario.regimen.value == "0")
    {
     window.alert("La Seleccion del Regimen es Requerido")
     validacion = false
     formulario.regimen.focus()
    }
    else if(formulario.ciudad.value == '0')
    {
     window.alert("La Ciudad es Requerida")
     validacion = false
     formulario.ciudad.focus()
    }
    else if(formulario.dir.value == "")
    {
     window.alert("La Direccion es Requerida")
     validacion = false
     formulario.dir.focus()
    }
    else if(formulario.tel.value == "")
    {
     window.alert("EL # de Telefono es Requerido")
     validacion = false
     formulario.tel.focus()
    }
    else if (formulario.correo.value != "" && !valcorreo.test(formulario.correo.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     formulario.correo.focus()
    }
    else  if(!validar(formulario.maxactivi.value))
    {
     alert("Debe seleccionar minimo una actividad")
    }
    else
    {
     if(confirm("Esta Seguro que Desea Actualizar el Tercero "+formulario.nom.value+"?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function aceptar_update_n(formulario)
{
    var id = /^[0-9]{6,11}$/
    var valcorreo = /^(.+\@.+\..+)$/

    formulario = document.form_insert

    if(formulario.tipdoc.value == '0')
    {
     window.alert("El Tipo de Documento es Requerido")
     validacion = false
     formulario.tipdoc.focus()
    }
    else if(formulario.nom.value == "")
    {
     window.alert("El Nombre del Tercero es Requerido")
     validacion = false
     formulario.nom.focus()
    }
    else if(formulario.apell1.value == "")
    {
     window.alert("El Primer Apellido del Tercero es Requerido")
     validacion = false
     formulario.apell1.focus()
    }
    else if(formulario.ciudad.value == '0')
    {
     window.alert("La Ciudad es Requerida")
     validacion = false
     formulario.ciudad.focus()
    }
    else if(formulario.dir1.value == "")
    {
     window.alert("La Direccion es Requerida")
     validacion = false
     formulario.dir1.focus()
    }
    else if(formulario.tel.value == "")
    {
     window.alert("EL # de Telefono es Requerido")
     validacion = false
     formulario.tel.focus()
    }
    else if (formulario.correo.value != "" && !valcorreo.test(formulario.correo.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     formulario.correo.focus()
    }
    else  if(!validar(formulario.maxactivi.value))
    {
     alert("Debe seleccionar minimo una actividad")
    }
    else
    {
     if(confirm("Esta Seguro que Desea Actualizar el Tercero "+formulario.nom.value+"?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}


function Elimina(formulario)

{

      if(confirm("Esta Seguro que Desea Eliminar el Tercero?"))

      {

       formulario.opcion.value= 4;

       formulario.submit();

      }

}







function tiptercer(formulario)

{

  formulario = document.form_tercero

  if(formulario.tipter[0].checked)

  {

    formulario['nomjur'].style.visibility = "visible";

    formulario['abr'].style.visibility = "visible";

    formulario['nomb'].style.visibility = "hidden";

    formulario['apell1'].style.visibility = "hidden";

    formulario['apell2'].style.visibility = "hidden";

  }

  if(formulario.tipter[1].checked)

  {

    formulario['nomjur'].style.visibility = "hidden";

    formulario['nomb'].style.visibility = "visible";

    formulario['abr'].style.visibility = "visible";

    formulario['apell1'].style.visibility = "visible";

    formulario['apell2'].style.visibility = "visible";

  }

}

function act_aceptar(formulario)

{

    validacion = true

    formulario = document.form_tercero

    formulario.opcion.value=6

    formulario.submit();

}

function aceptar_lis(formulario)

{

    validacion = true

    formulario = document.form_list

    if(formulario.tercer.value == "")

    {

     window.alert("El Nombre es Requerido")

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

    if(formulario.tercer.value == "")

    {

     window.alert("El Nombre es Requerido")

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

    if(formulario.tercer.value == "")

    {

     window.alert("El Nombre es Requerido")

     validacion = false

    }

    else

    {

    formulario.opcion.value= 1;

    formulario.submit();

    }

}

function aceptar_insert(formulario)

{

    validacion = true

    formulario = document.form_insert

/*    if(formulario.activi.checked == true)

    {

       formulario.licen.visible = true;

       formulario.cat.visible = true;

       formulario.venci.visible = true;

    }

    else

    {

       formulario.licen.visible = false;

       formulario.cat.visible = false;

       formulario.venci.visible = false;

    }

  */

    if(formulario.nom.value == "")

    {

     window.alert("El Nombre es Requerido")

     validacion = false

    }

    else if(formulario.nit.value == "")

    {

     window.alert("El NIT o La C.C. es Requerida")

     validacion = false

    }

    else if(formulario.abr.value == "")

    {

     window.alert("La Abreviatura es Requerida")

     validacion = false

    }

    else if(formulario.ciudad.value == 0)

    {

     window.alert("La Ciudad de Residencia es Requerida")

     validacion = false

    }

    else

    {

    formulario.opcion.value= 3;

    formulario.submit();

    }

}
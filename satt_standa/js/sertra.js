function aceptar_insert(formulario)
{
    validacion = true;
    formulario = document.ins_sertra;

    var numerico = /^[0-9]+\.?[0-9]*$/;
    if(formulario.cod_tipser.value == "0")
    {
      window.alert("El Tipo de Servicio es Requerido")
      validacion = false;
      formulario.cod_tipser.focus();
    } 
    else if( formulario.cod_tipser.value != "1" &&  formulario.tie_controID.value== '')
    {
      window.alert("El Tiempo Entre P/C Nacionales es Requerido")
      validacion = false;
      formulario.tie_controID.focus();
    } 
    else if( formulario.cod_tipser.value != "1" && formulario.tie_conurbID.value == '')
    {
      window.alert("El Tiempo Entre P/C Urbanos es Requerido")
      validacion = false;
      formulario.tie_conurbID.focus();
    }
    else
    {
     if(confirm("¿Desea guardar la configuracion para esta transportadora?"))
     {
       var val_real = parseInt( $("#tie_trazabID").val().split(":")[0] * 60 ) + parseInt( $("#tie_trazabID").val().split(":")[1] );
      //console.log( val_real );
       $("#ori_trazabID").val( val_real );
      formulario.opcion.value= 2;
      formulario.submit();
     }
    }
}


function aceptar_tercer_contro(formulario)
{
  validacion = true;
  formulario = document.ins_tercer_contro;
  
  if( !validarPC() )
  {
    return false;
  }
  
  if(confirm("¿Desea guardar los cambios para esta transportadora?"))
  {
    formulario.opcion.value= 2;
    formulario.submit();
  }
}

function onChangeTipServic()
{
    formulario = document.ins_sertra;
    if (formulario.cod_tipser.value == '1' || formulario.cod_tipser.value == '0') 
    {  
      document.getElementById('tie_controID').value = "";
      document.getElementById('tie_conurbID').value = "";
    }
}

function buscarTieControSelected(formulario)
{
  try
  {
    var valor = false;
    for (i = 0; i<formulario.tie_contro.length;i++)
    {
      if( formulario.tie_contro[i].checked)
      {
        valor = formulario.tie_contro[i].value;
        break;
      }
    }
    return valor;
  }
  catch( e )
  {
    alert( 'Error Función buscarTieControSelected : ' + e.message + '\n' + e.stack );
  }
}
function buscarTieConurbSelected(formulario)
{
  try
  {
    var valor = false;
    for (i = 0; i<formulario.tie_conurb.length;i++)
    {
      if( formulario.tie_conurb[i].checked)
      {
        valor = formulario.tie_conurb[i].value;
        break;
      }
    }
    return valor;
  }
  catch( e )
  {
    alert( 'Error Función buscarTieConurbSelected : ' + e.message + '\n' + e.stack );
  }
}
function DrawGridPC( row, action )
{
  try {
    var formulario = document.ins_tercer_contro;
    var num_filas = formulario.num_filas.value;
    var idRef = 1;
    var atributes = '';
    for (var i = 1; i <= num_filas; i++) {
      if (row != i) {
        if (document.getElementById("cod_contro" + i)) {
          var cod_contro = document.getElementById("cod_contro" + i).value;
          var cod_operad = document.getElementById("cod_operad" + i).value;
          var ind_estado = document.getElementById("ind_estado" + i);
          
          atributes += "&cod_contro" + String(idRef) + "=" + escape(cod_contro);
          atributes += "&cod_operad" + String(idRef) + "=" + escape(cod_operad);
          if( ind_estado.checked == true )
            ind_estado.value = 1;
          else
            ind_estado.value = 0;
          atributes += "&ind_estado" + String(idRef) + "=" + escape(ind_estado.value);
          idRef++;
        }
      }
    }
    if( action == 'add' && !validarPC() )
    {
      return false;
    }
    if (action == "add") 
      num_filas = Number(num_filas) + 1;
    else if (num_filas == 1) 
        return false;
    else
    {
      num_filas = num_filas - 1;
      atributes += "&row=" + row;
    }
    
    formulario.num_filas.value = num_filas;
    
    atributes += "&opcion=addpc";
    atributes += "&cod_transp=" + formulario.cod_transp.value;
    atributes += "&filas=" + num_filas;
    
    AjaxGetData("../" + formulario.dir_aplica_central.value + "/sertra/" + formulario.url_archiv.value + "?", atributes, 'div_controsID', "post");
  }
  catch(e)
  {
    alert( e.message + '\n' + e.stack );
  }
}

function onChangeCodContro( thisContro )
{
  try
  {
    var formulario = document.ins_tercer_contro;
    var num_filas = formulario.num_filas.value;
    for (var i = 1; i <= num_filas; i++) {
      var cod_contro = document.getElementById("cod_contro" + i);
      nom_contro = cod_contro.options[cod_contro.selectedIndex].text;
      if ( cod_contro.value == thisContro.value && cod_contro != thisContro && cod_contro.value != '0' ) {
        alert("El puesto de control " + nom_contro + " ya se encuentra seleccionado para esta transportadora.");
        thisContro.value = 0;
        return thisContro.focus();
      }
    }
  }
  catch(e)
  {
    alert(e.message + '\n' + e.stack);
  }
}

function validarPC()
{
  try
  {
    var formulario = document.ins_tercer_contro;
    var num_filas = formulario.num_filas.value;
    var flag = true;
    for (var i = 1; i <= num_filas; i++) {
      var cod_operad = document.getElementById("cod_operad" + i);
      var cod_contro = document.getElementById("cod_contro" + i);
      if (!cod_contro.value || cod_contro.value == '0') {
        cod_contro.focus();
        alert("El puesto de control " + i + " es requerido.");
        flag = false;
        break;
      }
      if (!cod_operad.value || cod_operad.value == '0') {
        cod_operad.focus();
        alert("El operador del puesto " + i + " es requerido.");
        flag = false;
        break;
      }
    }
    return flag;
  }
  catch(e)
  {
    alert(e.message + '\n' + e.stack);
  }
}

function checkStatusEAL( index )
{
  try
  {
    
    var cod_pcxfar = document.getElementById( "cod_pcxfar" + index ).value;
    var val_checkx = cod_pcxfar == 0 ? 0 : 1;
    var ind_estado = document.getElementById( "ind_estado" + index ).checked = val_checkx;
  }
  catch(e)
  {
    alert( e.message + '\n' + e.stack );
  }
}


function aceptar_insert_eal()
{
  formulario = document.form_item;
  validacion = true;
  val_totalx = document.getElementById('val_totalxID').value;
  for( i = 1; i <= val_totalx; i++ )
  {
    var cod_pcxfar = document.getElementById( "cod_pcxfar" + i );
    var nom_pcxcli = document.getElementById( "nom_pcxcli" + i ).innerHTML;
    var ind_estado = document.getElementById( "ind_estado" + i );
    if( ind_estado.checked == true && cod_pcxfar.value == 0 )
    {
      window.alert( "El Puesto en Sat Trafico es Requerido para el Puesto del cliente " + i + ' - ' + nom_pcxcli )
      validacion = false;
      return cod_pcxfar.focus();
    }
  }
   
  if( confirm( "¿Desea guardar la homologación de los puestos físicos para esta transportadora?" ) )
  {
    formulario.opcion.value= 2;
    formulario.submit();
  }
}